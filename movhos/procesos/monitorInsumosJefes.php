<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:			Monitor de insumos por responsable, se filtra por centro de costos de los pacientes. Lista los insumos en
// 						transito (con saldo) de cada paciente y por responsable de insumos, muestra el detalle de insumos cargados,
// 						aplicados y devueltos; permite realizar el traslado de insumos a otro responsable, para hacerlo la enfermera
// 						jefe debe firmar.
// 						Solo los roles definidos en el parametro rolesEnfermeraJefe de root_000051 pueden visualizar este monitor.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2017-06-05
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2019-12-30';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2019-12-30 - Edwin MG:				Se modifican queries referentes a la tabla turnero (000023) para que se tenga en cuenta la configuración 
//										del centro de costos de ayuda diagnosticas (ccotct de movhos_000011)
//  2018-08-10 - Jessica Madrid Mejía:	En la función consultarBotiquin() se modifica el query ya que los centros de costos que son botiquines 
// 										no tienen registro en la tabla movhos_000058 (ya que el botiquín dispensa al mismo centro de costos)
// 										por tal motivo quedaba con botiquín vacío y no traía registros.
//  2018-08-09 - Jessica Madrid Mejía:	Se agrega la función consultarBotiquin() y en la función pintarInsumosCco() se adiciona el filtro 
// 										botiquín al query para que la consulta tome el índice bottrains_idx y sea más eficiente.
//  2018-08-06 - Jessica Madrid Mejía:	En la función trasladarInsumosNuevaAuxiliar() se elimina el filtro Cartra='on' (tránsito en on) 
// 										en el update a movhos_000227 ya que si estaba en off no se registraba el traslado (se realiza 
// 										la devolución pero no se cargaba el insumo al nuevo responsable).
//  2018-06-08 - Jessica Madrid Mejía:	Se agrega el parámetro turno en el llamado a la función trasladarInsumosNuevaAuxiliar() para los 
// 										pacientes que no son de cirugía ya que a la función le quedaba faltando un parámetro, generaba un 
// 										warning y un query quedaba con valores erróneos que generaban un error y por tal motivo no seguía
// 										con la ejecución normal del programa (no se registraba el detalle del movimiento). Además se agrega
// 										la validación al query de cirugía que se ejecutaba sin importar que no tuviera turno.
// 	2018-01-17 - Edwar Jaramillo:		Uno de los llamados a la función "trasladarInsumosNuevaAuxiliar" para trasladar de un auxiliar a otro
// 										tenía invertida la posición de los parámetros  "codAuxActual", "cco", la forma correcta es  "codAuxActual", "cco".
// 	Noviembre 15 de 2017 Edwin MG		Se hacen cambios varios para que se tenga en cuenta los insumos aplicados desde cx
// 	2017-10-11 - Jessica Madrid Mejía:	Se tienen en cuenta los pacientes de ayudas diagnosticas.
// 	2017-07-28 - Jessica Madrid Mejía:	En la función pintarInsumosCco() se agrega al order by Carhis,Caring para evitar que se pinte
// 										mal el reporte debido a que el paciente no tiene habitación (cuando es urgencias).
// 	2017-06-22 - Jessica Madrid Mejía:	Se agrega la foto de los responsables de insumos, se controla si se muestran o no con el
// 										parámetro fotoAuxiliares de root_000051
// 	2017-06-22 - Edwin MG:				Se corrige en la función pintarInsumosCco() la condicion para mostrar todos los insumos iguales al día
// 										anterior.
// 	2017-06-21 - Jessica Madrid Mejía:	Se modifica la consulta de insumos en transito de la función pintarInsumosCco() y
// 										consultarInsumosConSaldoAuxiliar() para consultar movhos_000018 en vez de movhos_000020
// 										y así mostrar todos los pacientes con insumos en transito, sin importar si esta activo o no.
// 	2017-06-13 - Edwin MG:				Solo para el personal de HCE pueden anular aplicaciones
// 	2017-06-07 - Jessica Madrid Mejía:	Se agrega fondo rojo a las fechas diferentes de la actual.
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	
	include_once("root/comun.php");
	include_once("movhos/botiquin.inc.php");
	include_once("citas/funcionesAgendaCitas.php");
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");
    $cco_cirugia_default = '1016';


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	function validarRolJefe()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedatoHce;
		global $wuse;

		$esJefe = "";

		$rolesJefe = consultarAliasPorAplicacion( $conex, $wemp_pmla, "rolesEnfermeraJefe" );
		$rolesJefe = explode(",",$rolesJefe);


		$queryRolUsuario =  " SELECT Usurol,Roldes
								FROM ".$wbasedatoHce."_000020,".$wbasedatoHce."_000019
							   WHERE Usucod='".$wuse."'
								 AND Usuest='on'
								 AND Rolcod=Usurol
								 AND Rolest='on';";

		$resRolUsuario = mysql_query($queryRolUsuario, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryRolUsuario . " - " . mysql_error());
		$numRolUsuario = mysql_num_rows($resRolUsuario);

		$rolUsuario = "";
		$descRolUsuario = "";
		if($numRolUsuario>0)
		{
			$rowRolUsuario = mysql_fetch_array($resRolUsuario);

			$rolUsuario = $rowRolUsuario['Usurol'];
			$descRolUsuario = $rowRolUsuario['Roldes'];
		}

		if (!in_array($rolUsuario, $rolesJefe))
		{
			$esJefe = $rolUsuario."-".$descRolUsuario;
		}

		return $esJefe;
	}

	function consultarCentrosDeCosto()
	{
		global $conex;
		global $wbasedato;

		$queryCCo = " SELECT Ccocod,Cconom,Ccotdi
						FROM ".$wbasedato."_000011
					   WHERE (Ccohos='on' OR Ccourg='on' OR  Ccocir='on' OR Ccoayu='on' )
					     AND Ccoest='on'
					ORDER BY Ccocod,Cconom;";

		$resCCo=  mysql_query($queryCCo,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryCCo." - ".mysql_error());
		$numCCo = mysql_num_rows($resCCo);

		$arrayCco = array();
		if($numCCo > 0)
		{
			while($rowCCo = mysql_fetch_array($resCCo))
			{
				$mostrarCco = true;
				if($rowCCo['Ccoayu']=="on")
				{
					$mostrarCco = consultarConfiguracionAyudaDiagnostica($conex,$wbasedato,$rowCCo['Ccocod']);
				}

				if($mostrarCco)
				{
					$arrayCco[$rowCCo['Ccocod']] = $rowCCo['Cconom'];
				}

			}

		}

		return $arrayCco;
	}

	function pintarFiltroCco($codCco)
	{
		$ccostos = consultarCentrosDeCosto();

		$ocultarFiltroCco = "";
		if($codCco!="")
		{
			$ocultarFiltroCco = "display:none;";
		}

		$filtroFecha = "";
		$filtroFecha .= "	<div id='divFiltroCco' align='center' style='".$ocultarFiltroCco."'>
								<fieldset align='center' style='padding:5px;margin:5px;border: 2px solid #2a5db0;width:33%'>
									<legend style='border: 2px solid #2a5db0;border-top: 0px;font-family: Verdana;color: #ffffff;background-color: #2a5db0;font-size:8pt;font-weight:bold;'> Seleccione un centro de costos </legend>
									<table>
										<tr>
											<td  align='center' colspan='2'>
												<select id='filtroCco' name='filtroCco' onChange='seleccionarCco();'>
													<option>Seleccione...</option>";
													foreach($ccostos as $keyCco => $valueCco)
													{
														$seleccionado = "";
														if($codCco==$keyCco)
														{
															$seleccionado = "selected";
														}


		$filtroFecha .= "								<option value='".$keyCco."' ".$seleccionado.">".$keyCco." - ".$valueCco."</option>";
													}
		$filtroFecha .= "						</select>
											</td>
										</tr>
									</table>

								</fieldset>
							</div>

							<br>";

		echo $filtroFecha;
	}

	function pintarInsumosCco($codCco,$mostrarTodo = false)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wuse;

		$wbasedato_tcx    = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tcx");
		$wbasedato_cliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion");

		$codBotiquin = $codCco;


		$filtro = "AND Cartra='on'";
		if( $mostrarTodo ){

			$fecha = date( "Y-m-d", time()-24*3600 );
			$hora = date( "H:i:s", time()-24*3600 );

			$filtro = "AND ( ".$wbasedato."_000227.Fecha_data > '".$fecha."'
					    OR ( ".$wbasedato."_000227.Fecha_data = '".$fecha."' AND ".$wbasedato."_000227.Hora_data >= '".$hora."' ) )
					   ";
		}

		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codCco);
		$botiquin = consultarBotiquin($conex,$wbasedato,$codCco);

		if($ccoTipo=="ayudaDiagnostica")
		{
			// $prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedato,$codCco,false);
			// $prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);

			// $queryInsumos =  "SELECT Carbot,Caraux,Carhis,Caring,Carins,Carfec,Carcca,Carcap,Carcde,Ubisac,Ubihac,Ubihan,Ubiald,Artgen,Cconom,Descripcion,Empresa,Pacno1,Pacno2,Pacap1,Pacap2
								// FROM ".$wbasedato."_000227,".$wbasedato."_000018,".$wbasedato."_000026,".$wbasedato."_000011,usuarios,root_000037,root_000036,".$prefijoTablaAyudasDiagnosticas."_000023
							   // WHERE Carbot='".$botiquin."'
							     // AND Carest='on'
								 // AND Carhis=Ubihis
								 // AND Caring=Ubiing
								 // AND Artcod=Carins
								 // AND Ccocod=Carbot
								 // AND Codigo=Caraux
								 // AND Orihis=Carhis
								 // AND Oriing=Caring
								 // AND Oriori='".$wemp_pmla."'
								 // AND Pacced=Oriced
								 // AND Pactid=Oritid
								 // $filtro
								 // AND ".$prefijoCampos."tip=Oritid
								 // AND ".$prefijoCampos."doc=Oriced
								 // AND ".$prefijoCampos."est='on'
								 // AND ".$prefijoCampos."fpr!='on'
								 // AND ".$prefijoCampos."acp!=''
							// ORDER BY Carbot,Caraux,Ubihac,Carhis,Caring,Carfec;";
							
			$prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedato,$codCco,false);
			$prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);
			
			$sql = "SELECT Ccotct 
					  FROM ".$wbasedato."_000011 
					 WHERE ccocod = '".$codCco."'";
				 
			$resTablasCitas = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());		   
			$resNum 		= mysql_num_rows($resTablasCitas);
									  
			$rowsTablasCitas = mysql_fetch_array( $resTablasCitas );
			
			list( $tablaTurnero, $tipoDocumento, $numeroDocumento, $datosCondicionesExtras ) = explode( ",", $rowsTablasCitas['Ccotct'] );
			
			if( !empty($datosCondicionesExtras) ){
				$arrayCondicionesExtras = explode( "-", $datosCondicionesExtras );
				
				$condicionesExtrasTurnero = " AND ".implode( " AND ", $arrayCondicionesExtras );
			}

			$queryInsumos =  "SELECT Carbot,Caraux,Carhis,Caring,Carins,Carfec,Carcca,Carcap,Carcde,Ubisac,Ubihac,Ubihan,Ubiald,Artgen,Cconom,Descripcion,Empresa,Pacno1,Pacno2,Pacap1,Pacap2
								FROM ".$wbasedato."_000227,".$wbasedato."_000018,".$wbasedato."_000026,".$wbasedato."_000011,usuarios,root_000037,root_000036,".$prefijoTablaAyudasDiagnosticas."
							   WHERE Carbot='".$botiquin."'
							     AND Carest='on'
								 AND Carhis=Ubihis
								 AND Caring=Ubiing
								 AND Artcod=Carins
								 AND Ccocod=Carbot
								 AND Codigo=Caraux
								 AND Orihis=Carhis
								 AND Oriing=Caring
								 AND Oriori='".$wemp_pmla."'
								 AND Pacced=Oriced
								 AND Pactid=Oritid
								 $filtro
								 AND ".$tipoDocumento."=Oritid
								 AND ".$numeroDocumento."=Oriced
								 $condicionesExtrasTurnero
							ORDER BY Carbot,Caraux,Ubihac,Carhis,Caring,Carfec;";
		}
		else
		{
			$queryInsumos =  "SELECT Carbot,Caraux,Carhis,Caring,Carins,Carfec,Carcca,Carcap,Carcde,Ubisac,Ubihac,Ubihan,Ubiald,Artgen,Cconom,Descripcion,Empresa,Pacno1,Pacno2,Pacap1,Pacap2, Cartur, '' as Turnom
								FROM ".$wbasedato."_000227,".$wbasedato."_000018,".$wbasedato."_000026,".$wbasedato."_000011,usuarios,root_000037,root_000036
							   WHERE Carbot='".$botiquin."'
							     AND Carest='on'
								 AND Carhis=Ubihis
								 AND Caring=Ubiing
								 AND Ubisac='".$codCco."'
								 AND Artcod=Carins
								 AND Ccocod=Carbot
								 AND Codigo=Caraux
								 AND Orihis=Carhis
								 AND Oriing=Caring
								 AND Oriori='".$wemp_pmla."'
								 AND Pacced=Oriced
								 AND Pactid=Oritid
								 AND Cartur=0
								 $filtro
							   UNION
							  SELECT Carbot,Caraux,tcx11.Turhis AS Carhis, tcx11.Turnin AS Caring,Carins,Carfec,Carcca,Carcap,Carcde, m18.Ubisac, CONCAT( 'Qx ', Turqui ) as Ubihac, m18.Ubihan, m18.Ubiald,Artgen, mx11.Cconom,Descripcion,Empresa, c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2, Cartur, Turnom
								FROM ".$wbasedato_tcx."_000011 AS tcx11
						   LEFT JOIN ".$wbasedato_cliame."_000100 AS c100
								  ON (tcx11.Turhis=c100.Pachis)
						   LEFT JOIN ".$wbasedato_cliame."_000101 AS c101
								  ON (c100.Pachis=c101.Inghis and tcx11.Turnin=c101.Ingnin)
						   LEFT JOIN ".$wbasedato."_000018 as m18
								  ON (Ubihis=c101.Inghis and m18.Ubiing=c101.Ingnin and m18.Ubihis != '')
						   LEFT JOIN ".$wbasedato_tcx."_000012 AS tc12
								  ON (tcx11.Turqui=tc12.Quicod)
						   LEFT JOIN ".$wbasedato."_000011 AS mx11
								  ON (tc12.Quicco=mx11.Ccocod),
									 ".$wbasedato."_000227,
									 ".$wbasedato."_000026,
									 usuarios
							   WHERE tcx11.Turtur = Cartur
								 AND Carins = Artcod
								 AND Codigo = Caraux
								 AND Cartur > 0
								 AND Carest = 'on'
								 AND Cartur>0
								 AND Carbot='".$botiquin."'
								 $filtro
							ORDER BY Carbot,Caraux,Ubihac,Cartur,Carhis,Caring,Carfec;";
		}
		
		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());
		$numInsumos = mysql_num_rows($resInsumos);

		$arrayInsumos    = array();
		$arrayCco        = array();
		$arrayFechas     = array();
		$arrayAuxiliares = array();
		$arrayPacientes  = array();
		$idInsumo        = 0;
		if($numInsumos>0)
		{
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{

				$nombrePaciente = strtoupper($rowInsumos['Pacno1']." ".$rowInsumos['Pacno2']." ".$rowInsumos['Pacap1']." ".$rowInsumos['Pacap2']);
				if( trim( $nombrePaciente ) == '' )
					$nombrePaciente = $rowInsumos['Turnom'];

				$idHisIngOrTur = $rowInsumos['Cartur'] == 0 ? $rowInsumos['Carhis']."-".$rowInsumos['Caring']: $rowInsumos['Cartur'];

				if(!array_key_exists($rowInsumos['Carbot'], $arrayCco)){
					$arrayCco[$rowInsumos['Carbot']] = "";
				}

				if(!array_key_exists($rowInsumos['Carbot'], $arrayAuxiliares)){
					$arrayAuxiliares[$rowInsumos['Carbot']] = array();
				}

				if(!array_key_exists($rowInsumos['Caraux'], $arrayAuxiliares[$rowInsumos['Carbot']])){
					$arrayAuxiliares[$rowInsumos['Carbot']][$rowInsumos['Caraux']] = 0;
				}

				if(!array_key_exists($rowInsumos['Carbot'], $arrayPacientes)){
					$arrayPacientes[$rowInsumos['Carbot']] = array();
				}

				if(!array_key_exists($rowInsumos['Caraux'], $arrayPacientes[$rowInsumos['Carbot']])){
					$arrayPacientes[$rowInsumos['Carbot']][$rowInsumos['Caraux']] = array();
				}

				if(!array_key_exists($idHisIngOrTur, $arrayPacientes[$rowInsumos['Carbot']][$rowInsumos['Caraux']])){
					$arrayPacientes[$rowInsumos['Carbot']][$rowInsumos['Caraux']][$idHisIngOrTur] = 0;
				}

				if(!array_key_exists($rowInsumos['Carbot'], $arrayFechas)){
					$arrayFechas[$rowInsumos['Carbot']] = array();
				}

				if(!array_key_exists($rowInsumos['Caraux'], $arrayFechas[$rowInsumos['Carbot']])){
					$arrayFechas[$rowInsumos['Carbot']][$rowInsumos['Caraux']] = array();
				}

				if(!array_key_exists($idHisIngOrTur, $arrayFechas[$rowInsumos['Carbot']][$rowInsumos['Caraux']])){
					$arrayFechas[$rowInsumos['Carbot']][$rowInsumos['Caraux']][$idHisIngOrTur] = array();
				}

				if(!array_key_exists($rowInsumos['Carfec'], $arrayFechas[$rowInsumos['Carbot']][$rowInsumos['Caraux']][$idHisIngOrTur])){
					$arrayFechas[$rowInsumos['Carbot']][$rowInsumos['Caraux']][$idHisIngOrTur][$rowInsumos['Carfec']] = 0;
				}

				$arrayCco[$rowInsumos['Carbot']] = strtoupper($rowInsumos['Cconom']);
				$arrayAuxiliares[$rowInsumos['Carbot']][$rowInsumos['Caraux']] += 1;
				$arrayPacientes[$rowInsumos['Carbot']][$rowInsumos['Caraux']][$idHisIngOrTur] += 1;
				$arrayFechas[$rowInsumos['Carbot']][$rowInsumos['Caraux']][$idHisIngOrTur][$rowInsumos['Carfec']] += 1;

				$arrayInsumos[$idInsumo]['codBotiquin'] = $rowInsumos['Carbot'];
				$arrayInsumos[$idInsumo]['desBotiquin'] = strtoupper($rowInsumos['Cconom']);
				$arrayInsumos[$idInsumo]['codAuxiliar'] = $rowInsumos['Caraux'];
				$arrayInsumos[$idInsumo]['auxiliar'] = strtoupper($rowInsumos['Descripcion']);
				$arrayInsumos[$idInsumo]['empresa'] = $rowInsumos['Empresa'];
				$arrayInsumos[$idInsumo]['historia'] = $rowInsumos['Carhis'];
				$arrayInsumos[$idInsumo]['ingreso'] = $rowInsumos['Caring'];
				$arrayInsumos[$idInsumo]['codInsumo'] = $rowInsumos['Carins'];
				$arrayInsumos[$idInsumo]['descInsumo'] = trim($rowInsumos['Artgen']);
				$arrayInsumos[$idInsumo]['fecha'] = $rowInsumos['Carfec'];
				$arrayInsumos[$idInsumo]['cantCargada'] = $rowInsumos['Carcca'];
				$arrayInsumos[$idInsumo]['cantAplicada'] = $rowInsumos['Carcap'];
				$arrayInsumos[$idInsumo]['cantDevuelta'] = $rowInsumos['Carcde'];
				$arrayInsumos[$idInsumo]['cco'] = $rowInsumos['Ubisac'];
				$arrayInsumos[$idInsumo]['habitacion'] = strtoupper($rowInsumos['Ubihac']);
				$arrayInsumos[$idInsumo]['habitacionAnterior'] = strtoupper($rowInsumos['Ubihan']);
				$arrayInsumos[$idInsumo]['altaDefinitiva'] = $rowInsumos['Ubiald'];
				$arrayInsumos[$idInsumo]['ccoPaciente'] = strtoupper($rowInsumos['Cconom']);
				$arrayInsumos[$idInsumo]['nombrePaciente'] = $nombrePaciente;
				$arrayInsumos[$idInsumo]['turno'] = $rowInsumos['Cartur'];

				if( $rowInsumos['Cartur'] > 0 ){
					$canAplicadaOtros = cantidadGastadaPorInsumo( $conex, $wbasedato, $rowInsumos['Cartur'], $rowInsumos['Carins'], $rowInsumos['Caraux'] );
					$arrayInsumos[$idInsumo]['cantCargada']  = $rowInsumos['Carcca'] - $canAplicadaOtros['Can'] - $canAplicadaOtros['Dev'];
					$arrayInsumos[$idInsumo]['cantAplicada'] = $rowInsumos['Carcap'] - $canAplicadaOtros['Can'];
					$arrayInsumos[$idInsumo]['cantDevuelta'] = $rowInsumos['Carcde'] - $canAplicadaOtros['Dev'];
				}

				$idInsumo++;
			}
		}

		$html = "";
		$html .= "<div id='divInsumosCco'>
					<div id='tableInsumos'>";
		if(count($arrayCco)>0)
		{
			$enfermerasHCE = consultarAliasPorAplicacion( $conex, $wemp_pmla, "enfermerasHCE" );
			$enfermerasHCE = explode( ",", $enfermerasHCE );
			$display = 'none';
			if( in_array( $wuse, $enfermerasHCE ) ){
				$display = '';
			}

			$fotoAuxiliares = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fotoAuxiliares');

			$colspanResponsable=2;
			if($fotoAuxiliares=="on")
			{
				$colspanResponsable=3;
			}


			$html .= "<table style='width:100%'>
						<tr>
							<td>
								<span id='buscadorAuxiliar' style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
									Buscar responsable:&nbsp;&nbsp;</b><input id='buscarAuxiliar' type='text' placeholder='Nombre del responsable' style='border-radius: 4px;border:1px solid #AFAFAF;'>
								</span>
								<!--
								<span  id='trasladarInsumosAuxiliar' onclick='trasladarInsumos();' style='font-family: verdana;font-weight:bold;font-size: 10pt;align:right;right:300px;position: absolute;color: #0033FF;text-decoration: underline;cursor:pointer;'>
									Trasladar insumos
								</span>
								-->
							</td>
							<td style='text-align:right'>
								<span onClick='seleccionarCco( \"".( $mostrarTodo ? 'off': 'on' )."\" );' style='font-size:10pt;font-weight:bold;display:".$display."'>
									<a href=# ".( $mostrarTodo ? '': "title='- &Uacute;ltimas 24 horas'" ).">".( $mostrarTodo ? 'Mostrar con saldo': 'Ver todo' )."</a>
								</span>
							</td>
							</tr>
						</table>";
			foreach($arrayCco as $keyCco => $valueCco)
			{
				$html .= "<div id='accordion' class='desplegable'>
							<h3 align='left'>&nbsp;&nbsp;&nbsp;&nbsp;BOTIQUIN: ".$valueCco."</h3>
							<div id='divCco".$keyCco."'>
								<table id='tableCco".$keyCco."' width='100%'>
									<tr class='encabezadoTabla' align='center'>
										<td></td>
										<td colspan='".$colspanResponsable."'>Responsable</td>
										<td colspan='3'>Paciente</td>
										<td colspan='7'>Detalle del cargo</td>
									</tr>
									<tr class='encabezadoTabla' align='center'>
										<td></td>";
										if($fotoAuxiliares=="on")
										{
				$html .= "					<td>Foto</td>";
										}
				$html .= "				<td>C&oacute;digo</td>
										<td width='10%'>Nombre</td>
										<td width='7%'>Habitaci&oacute;n</td>
										<td>Historia</td>
										<td width='12%'>Paciente</td>
										<td>Fecha</td>
										<td>C&oacute;digo<br>del insumo</td>
										<td>Descripci&oacute;n</td>
										<td>Cantidad<br>cargada</td>
										<td>Cantidad<br>aplicada</td>
										<td>Cantidad<br>devuelta</td>
										<td>Saldo</td>
									</tr>";

									$conAuxiliar = 0;
									$auxAnterior = "";
									$pacienteAnterior = "";
									$fechaAnterior = "";
									$rowspanAuxiliar = 1;
									$rowspanPacientes = 1;
									$rowspanFecha = 1;
									$fila_lista = '';
									foreach($arrayInsumos as $keyInsumo => $valueInsumo)
									{

										if($keyCco == $valueInsumo['codBotiquin'])
										{


											$hab = $valueInsumo['habitacion'];
											$habitacion = "<b>".$valueInsumo['habitacion']."</b>";
											if($valueInsumo['habitacion']=="")
											{
												$habitacion = "<b>".$valueInsumo['habitacionAnterior']."</b>";
												$hab = $valueInsumo['habitacionAnterior'];

											}

											$estiloHabitacion = "style='font-size: 12pt'";
											if($valueInsumo['altaDefinitiva']=="on")
											{
												$habitacion .= "<br><span style='font-size:8pt;'>- Alta definitiva -</span>";
												$estiloHabitacion = "style='font-size: 12pt;background-color:#FEAAA4;'";
											}


											// ------------------------------------------
											// Tooltip
											// ------------------------------------------

											$tooltipCargados = "<div id=\"dvTooltipCargados\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades cargadas</div>";
											$tooltipAplicados = "<div id=\"dvTooltipAplicados\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades aplicadas</div>";
											$tooltipDevueltos = "<div id=\"dvTooltipDevueltos\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades devueltas</div>";

											$funcionCantidadCargada = "onclick='verDetalleCantidades(\"CR\",\"".$valueInsumo['codBotiquin']."\",\"".$valueInsumo['codAuxiliar']."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";
											$funcionCantidadAplicada = "onclick='verDetalleCantidades(\"AP\",\"".$valueInsumo['codBotiquin']."\",\"".$valueInsumo['codAuxiliar']."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";
											$funcionCantidadDevuelta = "onclick='verDetalleCantidades(\"DV\",\"".$valueInsumo['codBotiquin']."\",\"".$valueInsumo['codAuxiliar']."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";



											$saldoInsumo = $valueInsumo['cantCargada']-$valueInsumo['cantAplicada']-$valueInsumo['cantDevuelta'];
											$fondoSaldo = "";
											if(true || $saldoInsumo>0)
											{
												$fondoSaldo = "class='fondoAmarillo'";
											}


											$rowspanAuxiliar = $arrayAuxiliares[$keyCco][$valueInsumo['codAuxiliar']];
											$rowspanPacientes = $arrayPacientes[$keyCco][$valueInsumo['codAuxiliar']][ $valueInsumo['turno'] == 0 ? $valueInsumo['historia']."-".$valueInsumo['ingreso']: $valueInsumo['turno'] ];
											$rowspanFecha = $arrayFechas[$keyCco][$valueInsumo['codAuxiliar']][ $valueInsumo['turno'] == 0 ? $valueInsumo['historia']."-".$valueInsumo['ingreso']: $valueInsumo['turno'] ][$valueInsumo['fecha']];

											$conAuxiliar=0;
											if($auxAnterior===$valueInsumo['codAuxiliar'])
											{
												$conAuxiliar++;
											}

											$conPaciente=0;
											if( ( $valueInsumo['turno'] == 0 && $pacienteAnterior===$valueInsumo['historia']."-".$valueInsumo['ingreso'] ) || ( $valueInsumo['turno'] > 0 && $pacienteAnterior===$valueInsumo['turno'] ) )
											{
												$conPaciente++;
											}

											$conFecha=0;
											if($fechaAnterior===$valueInsumo['fecha'])
											{
												$conFecha++;
											}


											$fondoRojo = "";
											if($valueInsumo['fecha']!=date("Y-m-d"))
											{
												$fondoRojo = "fondoRojo";
											}

											if($conAuxiliar==0)
											{
												if ($fila_lista=='Fila1')
													$fila_lista = "Fila2";
												else
													$fila_lista = "Fila1";

												$fila_rowspan = $fila_lista;
												$fila_rowspanFecha = $fila_lista;

												$auxAnterior = $valueInsumo['codAuxiliar'];
												$pacienteAnterior = $valueInsumo['turno'] == 0 ? $valueInsumo['historia']."-".$valueInsumo['ingreso'] : $valueInsumo['turno'];
												$fechaAnterior = $valueInsumo['fecha'];

			$html .= 						"</tbody>";
			$html .= 						"<tbody id='auxiliar_".$valueInsumo['codAuxiliar']."' class='find'>";
			$html .= "							<tr class='".$fila_lista."'>
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanAuxiliar."' style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;'onclick='trasladarInsumosAuxiliar(\"".$valueInsumo['codAuxiliar']."\",\"".$valueInsumo['auxiliar']."\");'>Trasladar<br>Insumos</td>";

													if($fotoAuxiliares=="on")
													{
														$longitud = strlen($valueInsumo['codAuxiliar']);
														$codigoAuxiliar = $valueInsumo['codAuxiliar'];
														if($longitud>5)
														{
															$codigoAuxiliar = substr($valueInsumo['codAuxiliar'],$longitud-5,$longitud);
														}

														$urlFotoAuxiliar = consultarFoto($conex,$wemp_pmla,$wbasedato,$codigoAuxiliar,$valueInsumo['empresa']);


			$html .= "									<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanAuxiliar."' width='65px'>
															<img class='lightbox' id='fotoAuxiliar_".$valueInsumo['codAuxiliar']."' src='".$urlFotoAuxiliar."' width=65px height=75px>
															<img class='fotoAuxiliar_".$valueInsumo['codAuxiliar']."' id='fotoAuxiliar_Apliada".$valueInsumo['codAuxiliar']."' src='".$urlFotoAuxiliar."' style='display:none' height='700px'/>
														</td>";
													}
			$html .= "
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanAuxiliar."'>".$valueInsumo['codAuxiliar']."</td>
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanAuxiliar."'>".$valueInsumo['auxiliar']."</td>
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPacientes."' ".$estiloHabitacion.">".$habitacion."</td>
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPacientes."'>".$valueInsumo['historia']."-".$valueInsumo['ingreso']."</td>
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPacientes."'>".$valueInsumo['nombrePaciente']."</td>
													<td class='".$fila_rowspan." ".$fondoRojo."' align='center' rowspan='".$rowspanFecha."'>".$valueInsumo['fecha']."</td>
													<td align='center'>".$valueInsumo['codInsumo']."</td>
													<td align='center'>".$valueInsumo['descInsumo']."</td>
													<td align='center' style='cursor:pointer;' class='cantCargadas' title='".$tooltipCargados."' ".$funcionCantidadCargada.">".$valueInsumo['cantCargada']."</td>
													<td align='center' style='cursor:pointer;' class='cantAplicadas' title='".$tooltipAplicados."' ".$funcionCantidadAplicada.">".$valueInsumo['cantAplicada']."</td>
													<td align='center' style='cursor:pointer;' class='cantDevueltas' title='".$tooltipDevueltos."' ".$funcionCantidadDevuelta.">".$valueInsumo['cantDevuelta']."</td>
													<td ".$fondoSaldo." align='center'>".$saldoInsumo."</td>
												</tr>";

											}
											else
											{
												if($conPaciente==0)
												{
													// $pacienteAnterior = $valueInsumo['historia']."-".$valueInsumo['ingreso'];
													$pacienteAnterior = $valueInsumo['turno'] == 0 ? $valueInsumo['historia']."-".$valueInsumo['ingreso'] : $valueInsumo['turno'];
													$fechaAnterior = $valueInsumo['fecha'];

			$html .= "								<tr class='".$fila_lista."'>
														<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPacientes."' ".$estiloHabitacion.">".$habitacion."</td>
														<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPacientes."'>".$valueInsumo['historia']."-".$valueInsumo['ingreso']."</td>
														<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPacientes."'>".$valueInsumo['nombrePaciente']."</td>
														<td class='".$fila_rowspan." ".$fondoRojo."' align='center' rowspan='".$rowspanFecha."'>".$valueInsumo['fecha']."</td>
														<td align='center'>".$valueInsumo['codInsumo']."</td>
														<td align='center'>".$valueInsumo['descInsumo']."</td>
														<td align='center' style='cursor:pointer;' class='cantCargadas' title='".$tooltipCargados."' ".$funcionCantidadCargada.">".$valueInsumo['cantCargada']."</td>
														<td align='center' style='cursor:pointer;' class='cantAplicadas' title='".$tooltipAplicados."' ".$funcionCantidadAplicada.">".$valueInsumo['cantAplicada']."</td>
														<td align='center' style='cursor:pointer;' class='cantDevueltas' title='".$tooltipDevueltos."' ".$funcionCantidadDevuelta.">".$valueInsumo['cantDevuelta']."</td>
														<td ".$fondoSaldo." align='center'>".$saldoInsumo."</td>
													</tr>";
												}
												else
												{
													if($conFecha==0)
													{
														$fechaAnterior = $valueInsumo['fecha'];

				$html .= "								<tr class='".$fila_lista."'>
															<td class='".$fila_rowspan." ".$fondoRojo."' align='center' rowspan='".$rowspanFecha."'>".$valueInsumo['fecha']."</td>
															<td align='center'>".$valueInsumo['codInsumo']."</td>
															<td align='center'>".$valueInsumo['descInsumo']."</td>
															<td align='center' style='cursor:pointer;' class='cantCargadas' title='".$tooltipCargados."' ".$funcionCantidadCargada.">".$valueInsumo['cantCargada']."</td>
															<td align='center' style='cursor:pointer;' class='cantAplicadas' title='".$tooltipAplicados."' ".$funcionCantidadAplicada.">".$valueInsumo['cantAplicada']."</td>
															<td align='center' style='cursor:pointer;' class='cantDevueltas' title='".$tooltipDevueltos."' ".$funcionCantidadDevuelta.">".$valueInsumo['cantDevuelta']."</td>
															<td ".$fondoSaldo." align='center'>".$saldoInsumo."</td>
														</tr>";

													}
													else
													{
				$html .= "								<tr class='".$fila_lista."'>
															<td align='center'>".$valueInsumo['codInsumo']."</td>
															<td align='center'>".$valueInsumo['descInsumo']."</td>
															<td align='center' style='cursor:pointer;' class='cantCargadas' title='".$tooltipCargados."' ".$funcionCantidadCargada.">".$valueInsumo['cantCargada']."</td>
															<td align='center' style='cursor:pointer;' class='cantAplicadas' title='".$tooltipAplicados."' ".$funcionCantidadAplicada.">".$valueInsumo['cantAplicada']."</td>
															<td align='center' style='cursor:pointer;' class='cantDevueltas' title='".$tooltipDevueltos."' ".$funcionCantidadDevuelta.">".$valueInsumo['cantDevuelta']."</td>
															<td ".$fondoSaldo." align='center'>".$saldoInsumo."</td>
														</tr>";
													}
												}
											}
										}
									}
				$html .= "		</table>
							</div>
						</div><br>";
			}
		}
		else
		{
			 $html .= "<p align='center'><b>No hay insumos cargados a los pacientes de este centro de costos.</b></p>";
		}
		$html .= "</table>";
		$html .= "</div>";

		return $html;
	}

	function pintarModalTrasladoInsumos()
	{
		global $conex;
		global $wuse;

		$colspanTabla = "6";
		$modal = "";

		$modal .= "	<div id='modalTraslado'>

						<br><br>

						<table class='ccmb' id='tablaBuscadorAuxiliares' align='center'>
							<tr>
								<td class='encabezadoTabla' align='center' colspan='4'>TRASLADAR INSUMOS</td>
							</tr>
							<tr>
								<td class='encabezadoTabla' align='center' colspan='2'>Responsable actual</td>
								<td class='encabezadoTabla' align='center' colspan='2'>Nuevo responsable</td>
							</tr>
							<tr>
								<td class='fila1'><input type='text' id='txtAuxiliarActual' size='30'><input type='hidden' id='codAuxiliarActual' size='30'></td>
								<td class='fila1' colspan='2'><img src='/matrix/images/medical/movhos/traslado.png' height=17 width=17></td>
								<td class='fila1'><input type='text' id='txtNuevaAuxiliar' size='30' disabled='disabled'><input type='hidden' id='codNuevaAuxiliar' size='30' disabled='disabled'></td>
							</tr>
						</table>
						<br>
						<div id='divInsumosAuxiliar' style='display:none'>
						</div>

					<br><br>

					<span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span>
					<br><br>
					</div>";

		return $modal;

	}

	function consultarAuxiliares()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedatoHce;


		$rolesAuxiliar = consultarAliasPorAplicacion( $conex, $wemp_pmla, "rolesAuxiliarEnfermeria" );
		$rolesAuxiliar = "'".str_replace( ",", "','", trim( $rolesAuxiliar  ) )."'";

		$queryAuxiliares =  " SELECT Usucod,Descripcion
								FROM ".$wbasedatoHce."_000020,usuarios
							   WHERE Usurol IN (".$rolesAuxiliar.")
								 AND Usuest='on'
								 AND Usucod=Codigo
								 AND Activo='A';";

		$resAuxiliares = mysql_query($queryAuxiliares, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryAuxiliares . " - " . mysql_error());
		$numAuxiliares = mysql_num_rows($resAuxiliares);

		$arrayAuxiliares = array();
		if($numAuxiliares>0)
		{
			while($rowAuxiliares = mysql_fetch_array($resAuxiliares))
			{
				$arrayAuxiliares[$rowAuxiliares['Usucod']] = utf8_encode($rowAuxiliares['Descripcion']);
			}
		}

		return $arrayAuxiliares;
	}

	function pintarInsumosAuxiliar($codAuxiliar,$codigoCco, $ccos_cirugia='')
	{
		global $conex;
		global $wuse;


		$ccos_cirugia_arr = explode(",", $ccos_cirugia);

		$data = consultarInsumosConSaldoAuxiliar($codAuxiliar,$codigoCco);

		$insumos = $data['insumos'];
		$pacientes= $data['pacientes'];
		$fechas = $data['fechas'];

		$modal = "";

		if(count($insumos)>0)
		{
			$modal .= "	<table id='tableCco".$codigoCco."' width='80%' align='center'>
							<tr class='encabezadoTabla' align='center'>
								<td colspan='3'>Paciente</td>
								<td colspan='5'>Detalle del cargo</td>
							</tr>
							<tr class='encabezadoTabla' align='center'>
								<td>Habitaci&oacute;n</td>
								<td>Historia</td>
								<td width='18%'>Paciente</td>
								<td>Fecha</td>
								<td>C&oacute;digo<br>del insumo</td>
								<td>Descripci&oacute;n</td>
								<td>Saldo</td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>

							</tr>";
							$PacienteAnterior = "";
							$fechaAnterior = "";
							$fila_lista = '';
							foreach($insumos as $keyInsumos => $valueInsumos)
							{
								$rowspanPaciente = $pacientes[$valueInsumos['historia']."-".$valueInsumos['ingreso']];
								$rowspanFecha = $fechas[$valueInsumos['historia']."-".$valueInsumos['ingreso']][$valueInsumos['fecha']];

								$conPaciente=0;
								if($PacienteAnterior===$valueInsumos['historia']."-".$valueInsumos['ingreso'])
								{
									$conPaciente++;
								}

								$conFecha=0;
								if($fechaAnterior===$valueInsumos['fecha'])
								{
									$conFecha++;
								}


								$habitacion = "<b>".$valueInsumos['habitacion']."</b>";
								if($valueInsumos['habitacion']=="")
								{
									$habitacion = "<b>".$valueInsumos['habitacionAnterior']."</b>";


								}

								$estiloHabitacion = "style='font-size: 12pt'";
								if($valueInsumos['altaDefinitiva']=="on")
								{
									$habitacion .= "<br><span style='font-size:8pt;'>- Alta definitiva -</span>";
									$estiloHabitacion = "style='font-size: 12pt;background-color:#FEAAA4;'";
								}

								$idx_campos_html = $valueInsumos['historia']."-".$valueInsumos['ingreso']."_".$valueInsumos['insumo']."_".$valueInsumos['fecha'];
								if(in_array($codigoCco, $ccos_cirugia_arr)){
									$idx_campos_html = $valueInsumos['turnocx']."_".$valueInsumos['insumo'];
								}



								if($conPaciente==0)
								{
									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";

									$fila_rowspan = $fila_lista;

									$PacienteAnterior = $valueInsumos['historia']."-".$valueInsumos['ingreso'];
									$fechaAnterior = $valueInsumos['fecha'];

					$modal .= "		<tr class='".$fila_lista."' align='center'>
										<td rowspan='".$rowspanPaciente."' class='".$fila_rowspan."' ".$estiloHabitacion.">".$habitacion."</td>
										<td rowspan='".$rowspanPaciente."' class='".$fila_rowspan."'>".$valueInsumos['historia']."-".$valueInsumos['ingreso']."</td>
										<td rowspan='".$rowspanPaciente."' class='".$fila_rowspan."' width='18%'>".$valueInsumos['nombre']."</td>
										<td rowspan='".$rowspanFecha."' class='".$fila_rowspan."'>".$valueInsumos['fecha']."</td>
										<td>".$valueInsumos['insumo']."</td>
										<td>".$valueInsumos['descripcion']."</td>
										<td>".$valueInsumos['saldo']."</td>
										<td>
											<span id='insumoNoRegistrado_".$idx_campos_html."' name='insumoNoRegistrado_".$idx_campos_html."' style='display:none;cursor:pointer;' title=''><img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17></span>
											<span id='insumoRegistrado_".$idx_campos_html."' name='insumoRegistrado_".$idx_campos_html."' style='display:none;cursor:pointer;' title=''><img src='/matrix/images/medical/movhos/checkmrk.ico' height=17 width=17></span>
										</td>
									</tr>";
								}
								else
								{
									if($conFecha==0)
									{
										$fechaAnterior = $valueInsumos['fecha'];

					$modal .= "			<tr class='".$fila_lista."' align='center'>
											<td rowspan='".$rowspanFecha."' class='".$fila_rowspan."'>".$valueInsumos['fecha']."</td>
											<td>".$valueInsumos['insumo']."</td>
											<td>".$valueInsumos['descripcion']."</td>
											<td>".$valueInsumos['saldo']."</td>
											<td>
												<span id='insumoNoRegistrado_".$idx_campos_html."' name='insumoNoRegistrado_".$idx_campos_html."' style='display:none;cursor:pointer;' title=''><img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17></span>
												<span id='insumoRegistrado_".$idx_campos_html."' name='insumoRegistrado_".$idx_campos_html."' style='display:none;cursor:pointer;' title=''><img src='/matrix/images/medical/movhos/checkmrk.ico' height=17 width=17></span>
											</td>
										</tr>";

									}
									else
									{
					$modal .= "			<tr class='".$fila_lista."' align='center'>
											<td>".$valueInsumos['insumo']."</td>
											<td>".$valueInsumos['descripcion']."</td>
											<td>".$valueInsumos['saldo']."</td>
											<td>
												<span id='insumoNoRegistrado_".$idx_campos_html."' name='insumoNoRegistrado_".$idx_campos_html."' style='display:none;cursor:pointer;' title=''><img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17></span>
												<span id='insumoRegistrado_".$idx_campos_html."' name='insumoRegistrado_".$idx_campos_html."' style='display:none;cursor:pointer;' title=''><img src='/matrix/images/medical/movhos/checkmrk.ico' height=17 width=17></span>
											</td>
										</tr>";
									}
								}

							}
			$modal .= "	</table>";
			$modal .= "		<br><br><br>
							<div align='center'>

								<table align='center'>
									<tbody>
										<tr style='height: 70px;'>
											<td class='fila1' height='30'> &nbsp; &nbsp; Firma digital &nbsp; </td>
											<td class='fila2' height='30'><input name='pswFirma' placeHolder='Ingrese su firma digital' size='40' maxlength='80' id='pswFirma' value='' class='tipo3' onkeyup='validarFirmaDigital();' type='password'></td>
											<td class='fila1'>
												<input type='button' id='btTrasladarInsumos' value='Trasladar insumos' onclick='registrarTraslado();' disabled='disabled'>
											</td>
										</tr>
									</tbody>
								</table>
							</div>";
		}
		else
		{
			$modal .= "	<p><b>No hay insumos con saldo asociados al responsable.</b></p>";
		}


		return $modal;

	}

	function consultarInsumosConSaldoAuxiliar($auxiliar,$codigoCco)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;

		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);

		$wbasedato_tcx    = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tcx");
		$wbasedato_cliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion");

		if($ccoTipo=="ayudaDiagnostica")
		{
			// $prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedato,$codigoCco,false);
			// $prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);

			// $queryInsumos =  "SELECT Carbot,Carhis,Caring,Carins,Carfec,Carcca,Carcap,Carcde,Artgen,Artuni,(Carcca-Carcap-Carcde) as Saldo,Pacno1,Pacno2,Pacap1,Pacap2,Ubisac,Ubihac,Ubihan,Ubiald,Cconom
								// FROM ".$wbasedato."_000227,".$wbasedato."_000026,root_000037,root_000036,".$wbasedato."_000018,".$wbasedato."_000011,".$prefijoTablaAyudasDiagnosticas."_000023
							   // WHERE Caraux='".$auxiliar."'
								 // AND Cartra='on'
								 // AND Carest='on'
								 // AND Artcod=Carins
								 // AND Orihis=Carhis
								 // AND Oriing=Caring
								 // AND Oriori='".$wemp_pmla."'
								 // AND Pacced=Oriced
								 // AND Pactid=Oritid
								 // AND Carhis=Ubihis
								 // AND Caring=Ubiing
								 // AND Ccocod=Carbot
								 // AND ".$prefijoCampos."tip=Oritid
								 // AND ".$prefijoCampos."doc=Oriced
								 // AND ".$prefijoCampos."est='on'
								 // AND ".$prefijoCampos."fpr!='on'
								 // AND ".$prefijoCampos."acp!=''
							// ORDER BY Carbot,Caraux,Ubihac,Carfec,Carhis,Caring;";
							
							
			$prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedato,$codigoCco,false);
			$prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);
			
			$sql = "SELECT Ccotct 
					  FROM ".$wbasedato."_000011 
					 WHERE ccocod = '".$codigoCco."'";
				 
			$resTablasCitas = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());		   
			$resNum 		= mysql_num_rows($resTablasCitas);
									  
			$rowsTablasCitas = mysql_fetch_array( $resTablasCitas );
			
			list( $tablaTurnero, $tipoDocumento, $numeroDocumento, $datosCondicionesExtras ) = explode( ",", $rowsTablasCitas['Ccotct'] );
			
			if( !empty($datosCondicionesExtras) ){
				$arrayCondicionesExtras = explode( "-", $datosCondicionesExtras );
				
				$condicionesExtrasTurnero = " AND ".implode( " AND ", $arrayCondicionesExtras );
			}
			
			$queryInsumos =  "SELECT Carbot,Carhis,Caring,Carins,Carfec,Carcca,Carcap,Carcde,Artgen,Artuni,(Carcca-Carcap-Carcde) as Saldo,Pacno1,Pacno2,Pacap1,Pacap2,Ubisac,Ubihac,Ubihan,Ubiald,Cconom
								FROM ".$wbasedato."_000227,".$wbasedato."_000026,root_000037,root_000036,".$wbasedato."_000018,".$wbasedato."_000011,".$prefijoTablaAyudasDiagnosticas."
							   WHERE Caraux='".$auxiliar."'
								 AND Cartra='on'
								 AND Carest='on'
								 AND Artcod=Carins
								 AND Orihis=Carhis
								 AND Oriing=Caring
								 AND Oriori='".$wemp_pmla."'
								 AND Pacced=Oriced
								 AND Pactid=Oritid
								 AND Carhis=Ubihis
								 AND Caring=Ubiing
								 AND Ccocod=Carbot
								 AND ".$tipoDocumento."=Oritid
								 AND ".$numeroDocumento."=Oriced
								 $condicionesExtrasTurnero
							ORDER BY Carbot,Caraux,Ubihac,Carfec,Carhis,Caring;";
		}
		else
		{
			$queryInsumos =  "SELECT Carbot,Carhis,Caring, Cartur,Carins,Carfec,Carcca,Carcap,Carcde,Artgen,Artuni,(Carcca-Carcap-Carcde) as Saldo,Pacno1,Pacno2,Pacap1,Pacap2,Ubisac,Ubihac,Ubihan,Ubiald,Cconom, Caraux
								FROM {$wbasedato}_000227,{$wbasedato}_000026,root_000037,root_000036,{$wbasedato}_000018,{$wbasedato}_000011
							   WHERE Caraux='{$auxiliar}'
								 AND Cartra='on'
								 AND Carest='on'
								 AND Artcod=Carins
								 AND Orihis=Carhis
								 AND Oriing=Caring
								 AND Oriori='{$wemp_pmla}'
								 AND Pacced=Oriced
								 AND Pactid=Oritid
								 AND Carhis=Ubihis
								 AND Caring=Ubiing
								 AND Ubisac='{$codigoCco}'
								 AND Ccocod=Carbot
								 AND Cartur=0

								UNION

								SELECT 	mv227.Carbot, tcx11.Turhis AS Carhis, tcx11.Turnin AS Caring, mv227.Cartur, mv227.Carins, mv227.Carfec, mv227.Carcca, mv227.Carcap, mv227.Carcde, mv26.Artgen, mv26.Artuni,( mv227.Carcca-mv227.Carcap-mv227.Carcde) as Saldo,c100.Pacno1,c100.Pacno2,c100.Pacap1,c100.Pacap2, m18.Ubisac, m18.Ubihac, m18.Ubihan, m18.Ubiald, mx11.Cconom, mv227.Caraux
								FROM 	{$wbasedato}_000227 AS mv227
										INNER JOIN
										{$wbasedato}_000026 AS mv26 ON (mv26.Artcod = mv227.Carins)
										INNER JOIN
										{$wbasedato_tcx}_000011 AS tcx11 ON (tcx11.Turtur = mv227.Cartur)
										LEFT JOIN
										{$wbasedato_cliame}_000100 AS c100 ON (tcx11.Turhis=c100.Pachis)
										LEFT JOIN
										{$wbasedato_cliame}_000101 AS c101 ON (c100.Pachis=c101.Inghis and tcx11.Turnin=c101.Ingnin)
										LEFT JOIN
										{$wbasedato}_000018 as m18 ON (Ubihis=c101.Inghis and m18.Ubiing=c101.Ingnin and m18.Ubihis != '')
										LEFT JOIN
										{$wbasedato_tcx}_000012 AS tc12 ON (tcx11.Turqui=tc12.Quicod)
										LEFT JOIN
										{$wbasedato}_000011 AS mx11 ON (tc12.Quicco=mx11.Ccocod)
								WHERE 	mv227.Carbot='{$codigoCco}'
										AND mv227.Caraux='{$auxiliar}'
										AND mv227.Carest = 'on'
										AND mv227.Cartra='on'
										AND mv227.Cartur>0

								ORDER BY Carbot,Caraux,Ubihac,Cartur,Carfec";
		}

		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());
		$numInsumos = mysql_num_rows($resInsumos);

		$insumos        = array();
		$arrayPacientes = array();
		$arrayFechas    = array();
		$contInsumos    = 0;
		if($numInsumos>0)
		{
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{
				if($rowInsumos['Saldo']>0)
				{
					$his_ing = $rowInsumos['Carhis']."-".$rowInsumos['Caring'];
					if(!array_key_exists($his_ing, $arrayPacientes)){
						$arrayPacientes[$his_ing] = 0;
					}

					if(!array_key_exists($his_ing, $arrayFechas)){
						$arrayFechas[$his_ing] = array();
					}

					if(!array_key_exists($rowInsumos['Carfec'], $arrayFechas[$his_ing])){
						$arrayFechas[$his_ing][$rowInsumos['Carfec']] = 0;
					}

					$arrayPacientes[$rowInsumos['Carhis']."-".$rowInsumos['Caring']] += 1;
					$arrayFechas[$rowInsumos['Carhis']."-".$rowInsumos['Caring']][$rowInsumos['Carfec']] += 1;

					if(!array_key_exists($contInsumos, $insumos)){
						$insumos[$contInsumos] = array();
					}

					$insumos[$contInsumos]['botiquin']           = $rowInsumos['Carbot'];
					$insumos[$contInsumos]['historia']           = $rowInsumos['Carhis'];
					$insumos[$contInsumos]['ingreso']            = $rowInsumos['Caring'];
					$insumos[$contInsumos]['insumo']             = $rowInsumos['Carins'];
					$insumos[$contInsumos]['fecha']              = $rowInsumos['Carfec'];
					$insumos[$contInsumos]['cargados']           = $rowInsumos['Carcca'];
					$insumos[$contInsumos]['aplicados']          = $rowInsumos['Carcap'];
					$insumos[$contInsumos]['devueltos']          = $rowInsumos['Carcde'];
					$insumos[$contInsumos]['descripcion']        = $rowInsumos['Artgen'];
					$insumos[$contInsumos]['unidad']             = $rowInsumos['Artuni'];
					$insumos[$contInsumos]['saldo']              = $rowInsumos['Saldo'];
					$insumos[$contInsumos]['nombre']             = $rowInsumos['Pacno1']." ".$rowInsumos['Pacno2']." ".$rowInsumos['Pacap1']." ".$rowInsumos['Pacap2'];
					$insumos[$contInsumos]['cco']                = $rowInsumos['Ubisac'];
					$insumos[$contInsumos]['habitacion']         = trim($rowInsumos['Ubihac']);
					$insumos[$contInsumos]['habitacionAnterior'] = trim($rowInsumos['Ubihan']);
					$insumos[$contInsumos]['altaDefinitiva']     = $rowInsumos['Ubiald'];
					$insumos[$contInsumos]['descBotiquin']       = $rowInsumos['Cconom'];
					$insumos[$contInsumos]['turnocx']            = $rowInsumos['Cartur'];

					$contInsumos++;
				}

			}
		}

		$data = array();
		$data['insumos'] = $insumos;
		$data['pacientes'] = $arrayPacientes;
		$data['fechas'] = $arrayFechas;

		return $data;
	}


	function validarFirmaElectronica($usuario, $firma )
	{
		global $conex;
		global $wbasedatoHce;

		$firmaValida = false;
		if(!empty($firma))
		{
			$firma = sha1($firma);

			$queryFirma = "SELECT Usucod,Usucla
							 FROM ".$wbasedatoHce."_000020
							WHERE Usucod = '".$usuario."'
							  AND Usucla = '".$firma."'
							  AND Usuest = 'on';";

			$resFirma = mysql_query($queryFirma, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryFirma . " - " . mysql_error());
			$numFirma = mysql_num_rows($resFirma);

			if($numFirma>0)
			{
				$firmaValida = true;
			}
		}

		return $firmaValida;
	}

	function registrarTrasladoInsumos($wemp_pmla,$wbasedato,$cco,$codAuxActual,$codAuxNueva,$ccos_cirugia)
	{
		global $conex;
		global $wuse;

		$ccos_cirugia_arr = explode(",", $ccos_cirugia);
		$insumosAuxiliar  = consultarInsumosConSaldoAuxiliar($codAuxActual,$cco);
		$insumosAuxiliar  = $insumosAuxiliar['insumos'];
		$wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");

		$insumosDevolucion = array();
		$resultadoTraslado = array();

		$contInsumos = 0;

		if(in_array($cco, $ccos_cirugia_arr)){
			foreach($insumosAuxiliar as $keyInsumos => $valueInsumos)
			{
				$arrayInsumos   = array();
				$idArrayInsumos = $valueInsumos['turnocx'].'_'.$valueInsumos['insumo']; //."_".$valueInsumos['fecha'];

				if(!array_key_exists($idArrayInsumos, $arrayInsumos)){
					$arrayInsumos[$idArrayInsumos] = array();
				}

				if(!array_key_exists($idArrayInsumos, $resultadoTraslado)){
					$resultadoTraslado[$idArrayInsumos] = array();
				}

				$arrayInsumos[$idArrayInsumos]['codInsumo'] = $valueInsumos['insumo'];
				$arrayInsumos[$idArrayInsumos]['fecha']     = $valueInsumos['fecha'];
				$arrayInsumos[$idArrayInsumos]['cantidad']  = $valueInsumos['saldo'];

				$arrayResultado = registrarDevolucion($conex,$wemp_pmla,$wbasedato,"DV",$valueInsumos['botiquin'],$codAuxActual,$arrayInsumos,$valueInsumos['historia'],$valueInsumos['ingreso'],$valueInsumos['habitacion'],$valueInsumos['cco'],$wuse,$codAuxNueva,$valueInsumos['turnocx']);

				if($arrayResultado[$idArrayInsumos]['error']=="0")
				{
					$resultado = trasladarInsumosNuevaAuxiliar($wemp_pmla,$wbasedato,"CR",$valueInsumos['botiquin'],$codAuxNueva,$valueInsumos['insumo'],$valueInsumos['saldo'],$valueInsumos['fecha'],$valueInsumos['historia'],$valueInsumos['ingreso'],$valueInsumos['habitacion'],$valueInsumos['cco'],$codAuxActual,$valueInsumos['turnocx'],$wbasedato_cliame);

					$resultadoTraslado[$idArrayInsumos]['error'] = $resultado[$idArrayInsumos]['error'];
					$resultadoTraslado[$idArrayInsumos]['mensaje'] = utf8_encode( $resultado[$idArrayInsumos]['mensaje'] );
				}
				else
				{
					$resultadoTraslado[$idArrayInsumos]['error'] = 1;
					$resultadoTraslado[$idArrayInsumos]['mensaje'] = utf8_encode( $arrayResultado[$idArrayInsumos]['mensaje'] );
				}
			}
		}
		else
		{
			foreach($insumosAuxiliar as $keyInsumos => $valueInsumos)
			{
				$arrayInsumos = array();

				$idArrayInsumos = $valueInsumos['insumo']."_".$valueInsumos['fecha'];
				$arrayInsumos[$idArrayInsumos]['codInsumo'] = $valueInsumos['insumo'];
				$arrayInsumos[$idArrayInsumos]['fecha'] = $valueInsumos['fecha'];
				$arrayInsumos[$idArrayInsumos]['cantidad'] = $valueInsumos['saldo'];

				$arrayResultado = registrarDevolucion($conex,$wemp_pmla,$wbasedato,"DV",$valueInsumos['botiquin'],$codAuxActual,$arrayInsumos,$valueInsumos['historia'],$valueInsumos['ingreso'],$valueInsumos['habitacion'],$valueInsumos['cco'],$wuse,$codAuxNueva,$valueInsumos['turnocx']);

				if($arrayResultado[$idArrayInsumos]['error']=="0")
				{
					$resultado = trasladarInsumosNuevaAuxiliar($wemp_pmla,$wbasedato,"CR",$valueInsumos['botiquin'],$codAuxNueva,$valueInsumos['insumo'],$valueInsumos['saldo'],$valueInsumos['fecha'],$valueInsumos['historia'],$valueInsumos['ingreso'],$valueInsumos['habitacion'],$valueInsumos['cco'],$codAuxActual,0,$wbasedato_cliame);

					$resultadoTraslado[$valueInsumos['historia']."-".$valueInsumos['ingreso']."_".$idArrayInsumos]['error'] = $resultado[$valueInsumos['historia']."-".$valueInsumos['ingreso']."_".$idArrayInsumos]['error'];
					$resultadoTraslado[$valueInsumos['historia']."-".$valueInsumos['ingreso']."_".$idArrayInsumos]['mensaje'] = $resultado[$valueInsumos['historia']."-".$valueInsumos['ingreso']."_".$idArrayInsumos]['mensaje'];
				}
				else
				{
					$resultadoTraslado[$valueInsumos['historia']."-".$valueInsumos['ingreso']."_".$idArrayInsumos]['error'] = 1;
					$resultadoTraslado[$valueInsumos['historia']."-".$valueInsumos['ingreso']."_".$idArrayInsumos]['mensaje'] = $arrayResultado[$idArrayInsumos]['mensaje'];
				}
			}
		}

		return $resultadoTraslado;
	}


	function trasladarInsumosNuevaAuxiliar($wemp_pmla,$wbasedato,$codMovimiento,$botiquin,$auxiliar,$codInsumo,$cantidad,$fechaInsumo,$historia,$ingreso,$habitacion,$cco,$codAuxActual,$turnocx,$wbasedato_cliame)
	{
		global $conex;
		global $wuse;

		$data = array('error'=>"",'codigo'=>"",'mensaje'=>"");

		$arrayResultado = array();

		$fecha = date("Y-m-d");
		$hora = date("H:i:s");


		$insumo = consultarInsumoEncabezado($botiquin,$auxiliar,$historia,$ingreso,$codInsumo,$fecha,$turnocx);

		if(count($insumo)>0)
		{
			$idxarrayResultado = $historia."-".$ingreso."_".$codInsumo."_".$fechaInsumo;
			if($turnocx > 0){
				$idxarrayResultado = $turnocx.'_'.$codInsumo;
				$queryUpdateEncabezado = " 	UPDATE 	{$wbasedato}_000227
													SET Caraux = '{$auxiliar}'
											WHERE 	Carbot = '{$botiquin}'
													AND Cartur = '{$turnocx}'
													AND Carins = '{$codInsumo}'
													AND Carest = 'on'";
			}else{
				$nuevaCantCargada = $insumo['cantCargada']+$cantidad;
				$queryUpdateEncabezado = " UPDATE ".$wbasedato."_000227
											  SET Carcca='".$nuevaCantCargada."',
												  Cartra='on'
											WHERE Carbot='".$botiquin."'
											  AND Caraux='".$auxiliar."'
											  AND Carhis='".$historia."'
											  AND Caring='".$ingreso."'
											  AND Carins='".$codInsumo."'
											  AND Carfec='".$fecha."'
											  AND Carest='on';";
			}

			if(!array_key_exists($idxarrayResultado, $arrayResultado)){
				$arrayResultado[$idxarrayResultado] = array();
			}
			// echo $queryUpdateEncabezado;
			$resUpdateEncabezado = mysql_query($queryUpdateEncabezado,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdateEncabezado." - ".mysql_error());

			if(mysql_affected_rows()>0)
			{
				if($turnocx > 0)
				{
					$queryUpdateMercado = " 	UPDATE 	{$wbasedato_cliame}_000207
														SET Mpauri = '{$auxiliar}'
												WHERE 	Mpatur = '{$turnocx}'
														AND Mpacom = '{$codInsumo}'";
					$resUpdateEncabezado = mysql_query($queryUpdateMercado,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdateMercado." - ".mysql_error());

				}
				
				$arrayResultado = registrarDetalleTraslado($wbasedato,$codMovimiento,$botiquin,$auxiliar,$codInsumo,$cantidad,$fechaInsumo,$historia,$ingreso,$habitacion,$cco,$codAuxActual,$fecha,$hora,$turnocx);
			}
			else
			{
				$arrayResultado[$idxarrayResultado]['error'] = 1;
				$arrayResultado[$idxarrayResultado]['mensaje'] = "No se realiz&oacute el traslado del insumo";
			}
		}
		else
		{
			$queryInsertEncabezado = " INSERT INTO ".$wbasedato."_000227(     Medico      , Fecha_data , Hora_data ,   	  Carbot   ,    Caraux     ,   	Carhis   	,  		Caring  ,     Carins     ,   Carfec   ,     Carcca    , Carcap, Carcde, Cartra, Carest,       Seguridad    )
																  VALUES( '".$wbasedato."','".$fecha."','".$hora."','".$botiquin."','".$auxiliar."', '".$historia."', '".$ingreso."','".$codInsumo."','".$fecha."','".$cantidad."',  '0'  ,   '0' ,  'on' , 'on'  , 'C-".$wbasedato."' );";


			$resInsertEncabezado = mysql_query($queryInsertEncabezado,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryInsertEncabezado." - ".mysql_error());

			if(mysql_affected_rows()>0)
			{
				$arrayResultado = registrarDetalleTraslado($wbasedato,$codMovimiento,$botiquin,$auxiliar,$codInsumo,$cantidad,$fechaInsumo,$historia,$ingreso,$habitacion,$cco,$codAuxActual,$fecha,$hora);
			}
			else
			{
				$arrayResultado[$historia."-".$ingreso."_".$codInsumo."_".$fechaInsumo]['error'] = 1;
				$arrayResultado[$historia."-".$ingreso."_".$codInsumo."_".$fechaInsumo]['mensaje'] = "No se realiz&oacute el traslado del insumo";
			}
		}


		return $arrayResultado;
	}

	function registrarDetalleTraslado($wbasedato,$codMovimiento,$botiquin,$auxiliar,$codInsumo,$cantidad,$fechaInsumo,$historia,$ingreso,$habitacion,$cco,$codAuxActual,$fecha,$hora,$turnocx=0)
	{
		global $conex;
		global $wuse;

		$arrayResultado = array();
		if($turnocx > 0){
			$idx_arrayResultado = $turnocx.'_'.$codInsumo;
			//VALUES 	1 Auxiliar que trasladó
			//			2 Auxiliar que está recibiendo
			$queryInsertLogCirugia = "INSERT INTO {$wbasedato}_000238 	(Medico 		, Fecha_data	, Hora_data, Lcxbot			, Lcxtur		, Lcxaux			, Lcxins			, Lcxcan		, Lcxmov			, Lcxest, Seguridad			)
																VALUES 	('{$wbasedato}'	, '{$fecha}'	, '{$hora}', '{$botiquin}'	, '{$turnocx}'	, '{$codAuxActual}'	, '{$codInsumo}'	, '{$cantidad}'	, 'TR'				, 'on'	, 'C-{$wbasedato}' 	),
																		('{$wbasedato}'	, '{$fecha}'	, '{$hora}', '{$botiquin}'	, '{$turnocx}'	, '{$auxiliar}'		, '{$codInsumo}'	, '{$cantidad}'	, '{$codMovimiento}', 'on'	, 'C-{$wbasedato}' 	)";

			$resInsertLogCx = mysql_query($queryInsertLogCirugia,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryInsertLogCirugia." - ".mysql_error());

			if(!array_key_exists($idx_arrayResultado, $arrayResultado)){
				$arrayResultado[$idx_arrayResultado] = array();
			}

			if(mysql_affected_rows()>0)
			{
				$arrayResultado[$idx_arrayResultado]['error'] = 0;
				$arrayResultado[$idx_arrayResultado]['mensaje'] = "Trasladado correctamente";
			}
			else
			{
				$arrayResultado[$idx_arrayResultado]['error'] = 1;
				$arrayResultado[$idx_arrayResultado]['mensaje'] = "No se registr&oacute; el detalle del traslado del insumo";
			}
		}else{
			$queryInsertDetalle = " INSERT INTO ".$wbasedato."_000228(     Medico      , Fecha_data , Hora_data ,     Movbot    ,    Movaux     ,    Movins    	 ,   Movfec   ,      Movmov        ,   	 Movhis    ,   	 Moving   ,  Movcco  ,     Movhab  	   ,     Movcmo     ,   Movfmo    ,    Movhmo  ,  Movumo   ,Movjmo,Movtra,		Movant		 , Movest,     Seguridad      )
															   VALUES( '".$wbasedato."','".$fecha."','".$hora."','".$botiquin."','".$auxiliar."','".$codInsumo."','".$fecha."','".$codMovimiento."','".$historia."','".$ingreso."','".$cco."','".$habitacion."', '".$cantidad."', '".$fecha."', '".$hora."','".$wuse."',  ''  , 'on' ,'".$codAuxActual."',  'on' , 'C-".$wbasedato."' );";

			$resInsertDetalle = mysql_query($queryInsertDetalle,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryInsertDetalle." - ".mysql_error());

			if(mysql_affected_rows()>0)
			{
				$arrayResultado[$historia."-".$ingreso."_".$codInsumo."_".$fechaInsumo]['error'] = 0;
				$arrayResultado[$historia."-".$ingreso."_".$codInsumo."_".$fechaInsumo]['mensaje'] = "Trasladado correctamente";
			}
			else
			{
				$arrayResultado[$historia."-".$ingreso."_".$codInsumo."_".$fechaInsumo]['error'] = 1;
				$arrayResultado[$historia."-".$ingreso."_".$codInsumo."_".$fechaInsumo]['mensaje'] = "No se registr&oacute; el detalle del traslado del insumo";
			}
		}

		return $arrayResultado;
	}

	function consultarInsumoEncabezado($botiquin,$auxiliar,$historia,$ingreso,$insumo,$fecha,$turnocx=0)
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;

		if($turnocx > 0){
			$queryEncabezado = "SELECT 	Carins,Carfec,Carcca,Carcap,Carcde,(Carcca-Carcap-Carcde) as Saldo
								FROM 	{$wbasedato}_000227
								WHERE 	Carbot='{$botiquin}'
										AND Cartur='{$turnocx}'
										AND Carins='{$insumo}'
										AND Carest='on'
								ORDER BY Carbot,Caraux,Carfec";
		}else{
			$queryEncabezado =  " SELECT Carins,Carfec,Carcca,Carcap,Carcde,(Carcca-Carcap-Carcde) as Saldo
									FROM ".$wbasedato."_000227
								   WHERE Carbot='".$botiquin."'
									 AND Caraux='".$auxiliar."'
									 AND Carhis='".$historia."'
									 AND Caring='".$ingreso."'
									 AND Carins='".$insumo."'
									 AND Carfec='".$fecha."'
									 AND Carest='on'
								ORDER BY Carbot,Caraux,Carfec;";
		}

		$resEncabezado = mysql_query($queryEncabezado, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEncabezado . " - " . mysql_error());
		$numEncabezado = mysql_num_rows($resEncabezado);

		$arrayEncabezado = array();
		if($numEncabezado>0)
		{
			$rowEncabezado = mysql_fetch_array($resEncabezado);

			$arrayEncabezado['insumo'] = $rowEncabezado['Carins'];
			$arrayEncabezado['fecha'] = $rowEncabezado['Carfec'];
			$arrayEncabezado['cantCargada'] = $rowEncabezado['Carcca'];
			$arrayEncabezado['cantAplicada'] = $rowEncabezado['Carcap'];
			$arrayEncabezado['cantDevuelta'] = $rowEncabezado['Carcde'];
			$arrayEncabezado['saldo'] = $rowEncabezado['Saldo'];
		}

		return $arrayEncabezado;
	}

	function consultarBotiquin($conex,$wbasedato,$codCco)
	{
		$queryBotiquin = " SELECT Ccoori 
							 FROM ".$wbasedato."_000058 
							WHERE (Ccodes='".$codCco."' AND Ccoest='on')
							   OR (Ccoori='".$codCco."' AND Ccoest='on') 
						 GROUP BY Ccoori;;";

		$resBotiquin=  mysql_query($queryBotiquin,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryBotiquin." - ".mysql_error());
		$numBotiquin = mysql_num_rows($resBotiquin);

		$botiquin = "";
		if($numBotiquin>0)
		{
			$rowBotiquin = mysql_fetch_array($resBotiquin);
			
			$botiquin = $rowBotiquin['Ccoori'];
		}

		return $botiquin;
	}

//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'pintarInsumosCco':
		{
			if( !isset($mostrarTodo) )
				$mostrarTodo = 'off';

			$mostrarTodo = $mostrarTodo == 'on' ? true: false;
			$data = pintarInsumosCco($codCco, $mostrarTodo );
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarModalTrasladoInsumos':
		{
			$data =  pintarModalTrasladoInsumos($cco);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarAuxiliares':
		{
			$data = consultarAuxiliares();
			// $data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarInsumosAuxiliar':
		{
			$ccos_cirugia = (!isset($ccos_cirugia)) ? $cco_cirugia_default : $ccos_cirugia;
			$data = pintarInsumosAuxiliar($codAuxiliar,$codigoCco,$ccos_cirugia);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'validarFirmaElectronica':
		{
			$data = validarFirmaElectronica($usuario,$firma);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'registrarTraslado':
		{
			$ccos_cirugia = (!isset($ccos_cirugia)) ? $cco_cirugia_default : $ccos_cirugia;
			$data = registrarTrasladoInsumos($wemp_pmla,$wbasedato,$cco,$codAuxActual,$codAuxNueva,$ccos_cirugia);
			// $data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	$queryCCoCir = "SELECT 	Ccocod
					FROM 	{$wbasedato}_000011
					WHERE 	Ccocir = 'on'
							AND Ccoest = 'on'
					ORDER BY Ccocod,Cconom";

	$arrayCcoCir = array();
	if($resCCo = mysql_query($queryCCoCir,$conex)) {
		while($rowCCo = mysql_fetch_array($resCCo)) {
			$arrayCcoCir[$rowCCo['Ccocod']] = $rowCCo['Ccocod'];
		}
		$ccos_cirugia = implode(",", $arrayCcoCir);
	} else {
		$ccos_cirugia = $cco_cirugia_default;
	}

	?>
	<html>
	<head>
	  <title>MONITOR INSUMO JEFES DE ENFERMERIA</title>
	</head>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>


		<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	globlaMostrarTodo = 'off';

	$(document).ready(function () {

		inicializarAccordion();
		inicializarTooltip();
		inicializarBuscador();

	});


	function anularAplicacion( cmp, auxiliar, codigo, cantidad, presentacion, botiquin, ccopac, cconompac, hab, historia, ingreso, movimiento, turno ){

		jConfirm( "Est&oacute; seguro(a) que desea desaplicar el articulo?", "ALERTA",function( resp ){

			if( resp ){

				$.ajax({
					url		: "./botiquinAplicacionInsumos.php",
					type	: "POST",
					dataType: "json",
					data	: {
						consultaAjax	: 'desaplicarListaArticulo',
						wemp_pmla		: "<?=$wemp_pmla?>",
						wauxiliar		: auxiliar,
						insumos			: [{
							insumo			: codigo,
							cantidad		: cantidad,
							unidadArticulo	: presentacion,
							ccoori			: botiquin,
							ccodes			: ccopac,
							ccoDescripcion	: cconompac,
							habitacion		: hab,
							historia		: historia,
							ingreso			: ingreso,
							movimiento		: movimiento,
							justificacion	: '',
							turnocx			: turno,
						}],
					},
					async	: true,
					success	: function(respuesta) {

						if( respuesta[0].error == 0 ){

							$( 'img', cmp ).attr({src: '/matrix/images/medical/movhos/checkmrk.ico' });
						}
						else{
							console.log( "Hubo un error al consultar los Articulos asociados" );
							console.log( respuesta );
							jAlert( respuesta.message, "ALERTA" );
						}
					}
				});
			}
		});

	};


	//Actualizar cada dos minutos
	setInterval(function()
	{
		seleccionarCco(globlaMostrarTodo);
	}, 120000);

	function inicializarAccordion()
	{
		$( ".desplegable" ).accordion({
			collapsible: true,
			active:0,
			heightStyle: "content"
		});
	}

	function inicializarTooltip()
	{
		$( ".cantCargadas").tooltip();
		$( ".cantAplicadas").tooltip();
		$( ".cantDevueltas").tooltip();
	}

	function inicializarBuscador()
	{
		$('#buscarAuxiliar').quicksearch('#tableInsumos .find');
	}

	function soloNumeros(e){
		var key = window.Event ? e.which : e.keyCode;
		return ((key >= 48 && key <= 57) || key<= 8);
	}

	function seleccionarCco( mostrarTodo )
	{
		if( !mostrarTodo )
			mostrarTodo = 'off';

		globlaMostrarTodo = mostrarTodo;

		$.post("monitorInsumosJefes.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarInsumosCco',
			codCco			: $('#filtroCco').val(),
			wemp_pmla		: $('#wemp_pmla').val(),
			mostrarTodo		: mostrarTodo,
		}
		, function(data) {

			$("#divMonitorInsumosAuxiliares").html(data);

			inicializarAccordion();
			inicializarTooltip();
			inicializarBuscador();


			//Permite que la foto de la auxiliar se vea grande.
			$('.lightbox').click(function() {

				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');

				$.blockUI({
					message: $('#'+id),
					css: {
						top:  ($(window).height() - 700) /2 + 'px',
						left: ($(window).width() - 700) /2 + 'px',
						width: 'auto'
					}
				});

				$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
			});

		},'json');

	}

	function verDetalleCantidades(codMovimiento,botiquin,auxiliar,insumo,fecha,insumoGenerico,historia,ingreso,habitacion,cco,turno)
	{
		$.post("monitorInsumosAuxiliares.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarModalDetalle',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			codMovimiento	: codMovimiento,
			botiquin		: botiquin,
			auxiliar		: auxiliar,
			insumo			: insumo,
			fecha			: fecha,
			insumoGenerico	: insumoGenerico,
			historia		: historia,
			ingreso			: ingreso,
			habitacion		: habitacion,
			cco				: cco,
			turno			: turno,
		}
		, function(data) {

			$( "#dvAuxModalDetalleInsumos" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalDetalleInsumos" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalDetalleInsumos" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalDetalleInsumos" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalDetalleInsumos" ).height();

			$.blockUI({ message: $('#modalDetalleInsumos'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "80%",
				height	: "80%",
				left	: "10%",
				top		: '100px',
			} });


		},'json');
	}

	function trasladarInsumos()
	{
		$.post("monitorInsumosJefes.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarModalTrasladoInsumos',
			wemp_pmla		: $('#wemp_pmla').val(),
			cco				: $('#filtroCco').val()
		}
		, function(data) {

			$( "#dvAuxModalTraslado" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalTraslado" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalTraslado" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalTraslado" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalTraslado" ).height();

			$.blockUI({ message: $('#modalTraslado'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "80%",
				height	: "80%",
				left	: "10%",
				top		: '100px',
			} });


			cargarAutocompleteAuxiliarActual();

		},'json');

	}

	function trasladarInsumosAuxiliar(auxiliar,nombreAuxiliar)
	{
		$.post("monitorInsumosJefes.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarModalTrasladoInsumos',
			wemp_pmla		: $('#wemp_pmla').val(),
			cco				: $('#filtroCco').val()
		}
		, function(data) {

			$( "#dvAuxModalTraslado" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalTraslado" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalTraslado" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalTraslado" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalTraslado" ).height();

			$.blockUI({ message: $('#modalTraslado'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "80%",
				height	: "80%",
				left	: "10%",
				top		: '100px',
			} });

			$("#txtAuxiliarActual").val(nombreAuxiliar);
			$("#txtAuxiliarActual").attr("readOnly",true);
			$("#codAuxiliarActual").val(auxiliar);

			// cargarAutocompleteAuxiliarActual();
			seleccionarAuxiliarActual();

		},'json');

	}

	function cargarAutocompleteAuxiliarActual()
	{
		$.post("monitorInsumosJefes.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarAuxiliares',
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {

			var arrayAuxiliares = data;
			var auxiliares	= new Array();
			var index		= -1;

			for (var auxiliar in arrayAuxiliares)
			{
				index++;
				auxiliares[index] = {};
				auxiliares[index].value  = auxiliar;
				auxiliares[index].label  = auxiliar+"-"+arrayAuxiliares[auxiliar];
				auxiliares[index].nombre = arrayAuxiliares[auxiliar];

			}

			$( "#txtAuxiliarActual" ).autocomplete({
				minLength: 	0,
				source: 	auxiliares,
				select: 	function( event, ui ){
					$( "#txtAuxiliarActual" ).val(ui.item.nombre);
					$( "#codAuxiliarActual" ).val(ui.item.value);

					seleccionarAuxiliarActual();
					return false;
				},
				change: function( event, ui ) {
					if ( !ui.item ) {
						if(ui.item!==undefined)
						{
							// No se ha seleccionado una auxiliar válida
							jAlert("No se ha seleccionado un responsable v&aacute;lido","ALERTA");
							$( "#txtAuxiliarActual" ).val("");
							$( "#codAuxiliarActual" ).val("");

							$("#divInsumosAuxiliar").html("");
						}
					}
				}
			});

		},'json');

	}

	function seleccionarAuxiliarActual()
	{
		// consultar insumos auxiliar
		$.post("monitorInsumosJefes.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarInsumosAuxiliar',
			wemp_pmla		: $('#wemp_pmla').val(),
			codAuxiliar		: $('#codAuxiliarActual').val(),
			codigoCco		: $('#filtroCco').val(),
			ccos_cirugia    : $('#ccos_cirugia').val()
		}
		, function(data) {

			$("#divInsumosAuxiliar").html(data);
			$("#divInsumosAuxiliar").show();

			$("#txtNuevaAuxiliar").attr("disabled",false);

			cargarAutocompleteNuevaAuxiliar();
			$("#txtNuevaAuxiliar").focus();

		},'json');
	}

	function cargarAutocompleteNuevaAuxiliar()
	{
		$.post("monitorInsumosJefes.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarAuxiliares',
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {

			var arrayAuxiliares = data;
			var auxiliares	= new Array();
			var index		= -1;

			for (var auxiliar in arrayAuxiliares)
			{
				index++;
				auxiliares[index] = {};
				auxiliares[index].value  = auxiliar;
				auxiliares[index].label  = auxiliar+"-"+arrayAuxiliares[auxiliar];
				auxiliares[index].nombre = arrayAuxiliares[auxiliar];

			}

			$( "#txtNuevaAuxiliar" ).autocomplete({
				minLength: 	0,
				source: 	auxiliares,
				select: 	function( event, ui ){
					$( "#txtNuevaAuxiliar" ).val(ui.item.nombre);
					$( "#codNuevaAuxiliar" ).val(ui.item.value);

					seleccionarNuevaAuxiliar();
					return false;
				},
				change: function( event, ui ) {
					if ( !ui.item ) {

						if(ui.item!==undefined)
						{
							// No se ha seleccionado una auxiliar válida
							jAlert("No se ha seleccionado un responsable v&aacute;lido","ALERTA");
							$( "#txtNuevaAuxiliar" ).val("");
							$( "#codNuevaAuxiliar" ).val("");
						}
					}
				}
			});

		},'json');

	}

	function seleccionarNuevaAuxiliar()
	{
		if($( "#codAuxiliarActual" ).val() === $( "#codNuevaAuxiliar" ).val())
		{
			jAlert("Debe seleccionar un responsable de los insmumos diferente al actual","ALERTA");
			$( "#txtNuevaAuxiliar" ).val("");
			$( "#codNuevaAuxiliar" ).val("");
		}
	}

	function validarFirmaDigital()
	{

		$.post("monitorInsumosJefes.php",
		{
			consultaAjax 	: '',
			accion			: 'validarFirmaElectronica',
			wemp_pmla		: $('#wemp_pmla').val(),
			usuario			: $( "#wusuario" ).val(),
			firma			: $( "#pswFirma" ).val()
		}
		, function(data) {

			var clase = "";

			$( "#pswFirma" ).parent().removeClass( "fondoVerde" );
			$( "#pswFirma" ).parent().removeClass( "fondoRojo" );

			if(data=="1")
			{
				clase = "fondoVerde";
				$( "#btTrasladarInsumos" ).attr({disabled: false });
			}
			else
			{
				clase = "fondoRojo";
				$( "#btTrasladarInsumos" ).attr({disabled: true });
			}

			$( "#pswFirma" ).parent().addClass( clase );

		},'json');

	}

	function validarAuxiliares(codAuxActual,codAuxNueva)
	{
		var mensajeValidacion = "";

		if(codAuxActual == "")
		{
			mensajeValidacion += "- Debe ingresar el responsable actual de los insumos";
		}

		if(codAuxNueva == "")
		{
			mensajeValidacion += "- Debe ingresar el nuevo responsable de los insumos";
		}


		// ojo, auxiliares validos y que hayan insumos a transferir


		return mensajeValidacion;
	}

	function registrarTraslado()
	{
		var codAuxActual = $('#codAuxiliarActual').val();
		var codAuxNueva = $('#codNuevaAuxiliar').val();

		var mensajeValidacion = validarAuxiliares(codAuxActual,codAuxNueva);

		if(mensajeValidacion=="")
		{

			jConfirm( "Desea realizar el traslado de insumos de "+$('#txtAuxiliarActual').val()+" a "+$('#txtNuevaAuxiliar').val()+"?", "ALERTA", function( resp ){

				if(resp)
				{
					// alert("hey hey");
					$.post("monitorInsumosJefes.php",
					{
						consultaAjax 		: '',
						accion				: 'registrarTraslado',
						wemp_pmla			: $('#wemp_pmla').val(),
						wbasedato			: $('#wbasedato').val(),
						cco					: $('#filtroCco').val(),
						ccos_cirugia 		: $('#ccos_cirugia').val(),
						codAuxActual		: codAuxActual,
						codAuxNueva			: codAuxNueva
					}
					, function(data) {

						for (var insumo in data)
						{
							if(data[insumo].error == "0")
							{
								$("#insumoRegistrado_"+insumo).show();
								$("#insumoRegistrado_"+insumo).attr("title",data[insumo].mensaje);
								$("#insumoRegistrado_"+insumo).tooltip();
							}
							else
							{
								$("#insumoNoRegistrado_"+insumo).show();
								$("#insumoNoRegistrado_"+insumo).attr("title",data[insumo].mensaje);
								$("#insumoNoRegistrado_"+insumo).tooltip();
							}
						}

						$( "#btTrasladarInsumos" ).attr({disabled: true });

					},'json');

				}
			});



		}
		else
		{
			jAlert(mensajeValidacion,"ALERTA");
		}
	}

	function cerrarModal()
	{
		$.unblockUI();
		seleccionarCco(globlaMostrarTodo);
	}

	function cerrarVentana()
	{
		top.close();
    }
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>




<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">

	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	// -->	ENCABEZADO
	encabezado("MONITOR DE INSUMOS POR RESPONSABLE", $wactualiz, 'clinica');

	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' id='wusuario' name='wusuario' value='".$wuse."'>";

	$esEnfermeraJefe = validarRolJefe();

	if($esEnfermeraJefe=="")
	{
		$codCco = "";
		if(!empty($wcco))
		{
			$codCco = $wcco;
		}

		$filtroCco = pintarFiltroCco($codCco);
		echo $filtroCco;

		if(!empty($wcco))
		{
			echo "<script>seleccionarCco();</script>";
		}

		// $codCco = "";

		// $filtroCco = pintarFiltroCco($codCco);
		// echo $filtroCco;

	}
	else
	{
		echo "<p align='center' style='font-size:18;font-weight:bold;'>El rol ".$esEnfermeraJefe." no esta configurado como jefe de enfermer&iacute;a. </p>";
	}


	echo "<div id='divMonitorInsumosAuxiliares'></div>";
	echo "<div id='dvAuxModalDetalleInsumos' style='display:none'></div>";
	echo "<div id='dvAuxModalTraslado' style='display:none'></div>";

	echo "	<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";

	?>
	<input type="hidden" id="ccos_cirugia" name="ccos_cirugia" value="<?=$ccos_cirugia?>">
	</BODY>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
}

}//Fin de session
?>
