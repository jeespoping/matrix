<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Permite realizar los pedidos de insumos al botiquín asignado para cada centro de costos (relación dada en 
// 						movhos_000058), se crea un pedido para cada centro de costos (el consecutivo se guarda en Ccocpi de 
// 						movhos_000011). Se muestra el saldo del botiquín para cada insumo pero permite ordenar cualquier cantidad sin
// 						tener en cuenta el saldo del insumo. Si el responsable de insumos ingresa nuevamente puede modificar  el 
// 						pedido si no se ha entregado.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2017-06-30
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
// Septiembre 2 de 2021		Joel PH		- Se modifica programa para mostrar los pacientes del botiquin de Hemodinamia, centro de costo 2503,
//										  este centro de costos no tiene tabla de citas, por lo cual se realiza una modificación en el código
//										  para que obtenga los pacientes del piso o servicio y los liste en el programa. Se debe tener en cuenta
//										  que se debe marcar el campo CComsa = on en la tabla movhos 11 para que el centro de costo sea listado
//										  en los centros de costos a dispensar.
// Diciembre 30 de 2019 Edwin MG 		- Se modifica función consultarPacientesCco para que se muestre los pacientes de ayudas diagnosticas de acuerdo
//										  a la tabla de turnero (configuración en parametro ccotct de movhos_000011 ) y no a la tabla 000023
//										  de citas. Esto debido a que en arkadia la tabla de turnero es la cliame_0000304 y no tiene la misma estructura
//										  que la tabla 000023 de citas (citasen_000023 != cliame_000304)
// Septiembre 3 de 2019 Jessica Madrid 	- El evento onkeypress dejó de funcionar en Firefox para las teclas que no ingresan un valor 
// 										  (flechas) por tal motivo no permitía realizar el desplazamiento en la cuadricula con dichas 
// 										  teclas, se modifica éste evento por onkeydown para que la funcionalidad de 
// 										  desplazamiento vuelva a funcionar.
// Abril 26 de 2018    Edwin MG		    - Se aumenta la cantidad máxima de pedido por articulo de 100 a 100000
// Diciembre 4 de 2017 Jessica Madrid 	- Para los pacientes de urgencias sin ubicación se muestra si estan en consulta (Mtrcur='on' en hce_000022)
// 										  y no se podrá  hacer pedido a esos pacientes.
// 										- Se modifica el filtro de la funcion consultarPacientesCco() Mtrcua!='on' para evitar que no se muestren 
// 										  los pacientes en caso de que este campo tenga NO APLICA.
// Diciembre 1 de 2017 Jessica Madrid 	- Se quita el filtro Mtrsal!='' en la funcion consultarPacientesCco() para que en la opcion de pacientes de 
// 										  urgencias sin ubicación muestre los pacientes sin cubiculo sin importar si tienen sala o no, esta modificación
// 										  se hace para que puedan visualizar los pacientes de ortopedia.
// Octubre 31 de 2017 Jessica Madrid 	- Se muestra una alerta cuando se agrega un paquete y alguno de los insumos no está en la lista de pedidos,
// 										  de esta forma se aclara por que razón no se agregan todos los insumos del paquete (no existe en movhos_000026,
// 										  no esta activo en movhos_000026, no es material médico quirúrgico, no existe en el botiquín o no tiene saldo
// 										  en el botiquín).
// Octubre 30 de 2017 Jessica Madrid 	- Se agrega la funcionalidad de cargar paquetes de insumos (movhos_000241 y movhos_000242), se tienen
// 										  en cuenta los paquetes configurados para el centro de costos, si no tiene ninguno definido muestra
// 										  los configurados por tipo de servicio. Si el insumo fue agregado por paquetes, en el detalle del pedido
// 										  se guarda en el campo Dpepaq los codigos de los paquetes (Si agrego un paquete y se modifican 
// 										  las cantidades se sigue considerando como paquete). Si al realizar un pedido nuevo se cargan las cantidades
// 										  anteriores con la opción TRAER ÚLTIMO PEDIDO y habían insumos cargados por paquetes no se conserva esa relación.
// Octubre 12 de 2017 Jessica Madrid 	- Se agrega el filtro Mtrsal!='' en la funcion consultarPacientesCco() para que en la opcion de pacientes de 
// 										  urgencias sin ubicación solo muestre los pacientes que ya fueron atendidos, tienen sala y no tienen 
// 										  cubiculo.
// Octubre 11 de 2017 Jessica Madrid 	- Para urgencias permite realizar pedidos para pacientes sin ubicación y ya no se muestran todas las zonas.
// 										- Si en movhos_000011 el centro de costos tiene el campo Ccopip='on' solo permitirá realizar pedidos para 
// 										  un paciente y solo si no tiene saldo.
// 										- Si en hce_000019 el rol tiene el campo Rolppc='on' solo podrá realizar pedidos para un centro de costos,
// 										  solo si no tiene saldos de insumos ni pedidos pendientes para otro centro de costo.
// 										- Se habilitan los pedidos para los centros de costos de ayudas diagnosticas configurados en root_000117 
// 										  y que tienen la tabla  000023 citas creada.
// Julio 27 de 2017		Jessica Madrid 	Se inhabilita la cabecera fija ya que genera lentitud en el programa y se crea un div que simula el 
// 										tooltip y se muestra cada vez que se el puntero se ubica en el input de cantidad (sin necesidad de 
// 										ubicar el cursor)
// Julio 18 de 2017.		Edwin MG.	Se muestra tanto el nombre generico como el comercial.
// Julio 13 de 2017.		Edwin MG.	Se impide realizar pedidos mientras se dispensa un pedido.
// Julio 5 de 2017.			Edwin MG.	SE muestra los insumos que tengan saldo o permitan negativos.
//										Al momento de dar clic sobre el boton de realizar pedido, se bloquea para no dar clic dos veces sobre el.
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2021-09-02';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//                
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
	include_once("citas/funcionesAgendaCitas.php");
	include_once("movhos/botiquin.inc.php");

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	/****************************************************************************
	 * Consulta los centros de costos que son domiciliarios
	 ****************************************************************************/ 
	function esCcoDomiciliario( $conex, $wbasedato, $cco ){
		
		$val = false;
		
		$sql = "SELECT Ccocod, Cconom
				  FROM ".$wbasedato."_000011
				 WHERE ccodom = 'on'
				   AND ccoest = 'on'
				   AND ccocod = '".$cco."'
				 ";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );
		
		while( $rows = mysql_fetch_array($res) ){
			$val = true;
		}
		
		return $val;
	}
	
	function hayPedidosEnEntrega( $conex, $wemp_pmla, $wbasedato, $auxiliar, $botiquin ){
	
		$val = false;	
		
		$tiempo = 60; //consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiempoEntregaHacerPedidos" );
		
		$fecha = date( "Y-m-d", time()-$tiempo*60 );
		$hora  = date( "H:i:s", time()-$tiempo*60 );
		
		$sql = "SELECT Pedcod
				  FROM ".$wbasedato."_000230
				 WHERE Pedaux = '".$auxiliar."'
				   AND Pedbot = '".$botiquin."'
				   AND Pedeen = 'on'
				   AND Pedest = 'on'
				 ";
				   // AND ( Pedfen > '".$fecha."' 
					// OR ( Pedfen = '".$fecha."' AND Pedhen >= '".$hora."' ) )
				 
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
			
			$val = true;
		}
		
		return $val;
	}
	
	//Indica si el pedido está siendo editado o no ( on = editando, off= no esta editando )
	function setPedidoEditando( $conex, $wbasedato, $codigo, $estado ){
		
		if( $estado == 'on' ){
			$fecha = date( "Y-m-d" );
			$hora  = date( "H:i:s" );
		}
		else{
			$fecha = "0000-00-00";
			$hora  = "00:00:00";
		}
		
		$val = false;
		
		$sql =  " UPDATE ".$wbasedato."_000230
					 SET Pededi = '".$estado."',
						 Pedfed = '".$fecha."',
						 Pedhed = '".$hora."'
				   WHERE Pedcod = '".$codigo."';";
		
		$res = mysql_query( $sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );		   
		
		if( mysql_affected_rows() > 0 )
		{
			$val = true;
		}
		
		return false;
	}
	
	function validarRolAuxiliar()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedatoHce;
		global $wuse;
		
		$esAuxiliar = "";
		
		$rolesAuxiliar = consultarAliasPorAplicacion( $conex, $wemp_pmla, "rolesAuxiliarEnfermeria" );
		$rolesAuxiliar = explode(",",$rolesAuxiliar);
		
		
		$queryRolUsuario =  " SELECT Usurol,Roldes,Rolppc 
								FROM ".$wbasedatoHce."_000020,".$wbasedatoHce."_000019
							   WHERE Usucod='".$wuse."' 
								 AND Usuest='on'
								 AND Rolcod=Usurol
								 AND Rolest='on';";
		
		$resRolUsuario = mysql_query($queryRolUsuario, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryRolUsuario . " - " . mysql_error());		   
		$numRolUsuario = mysql_num_rows($resRolUsuario);
		
		$rolUsuario = "";
		$descRolUsuario = "";
		$permitePedidosCco = "";
		if($numRolUsuario>0)
		{
			$rowRolUsuario = mysql_fetch_array($resRolUsuario);
			
			$rolUsuario = $rowRolUsuario['Usurol'];
			$descRolUsuario = $rowRolUsuario['Roldes'];
			$permitePedidosCco = $rowRolUsuario['Rolppc'];
		}
		
		$arrayRolAuxiliar = array();
		if (!in_array($rolUsuario, $rolesAuxiliar)) 
		{
			$arrayRolAuxiliar['esAuxiliar'] = false;
			$arrayRolAuxiliar['rol'] = $rolUsuario."-".$descRolUsuario;
			$arrayRolAuxiliar['permitePedidosCco'] = $permitePedidosCco;
		}
		else
		{
			$arrayRolAuxiliar['esAuxiliar'] = true;
			$arrayRolAuxiliar['rol'] = $rolUsuario."-".$descRolUsuario;
			$arrayRolAuxiliar['permitePedidosCco'] = $permitePedidosCco;
		}
		
		return $arrayRolAuxiliar;
		
	}
	
	function consultarCentrosDeCostosBotiquines()
	{
		global $conex;
		global $wbasedato;
		
		$arrayCco = consultarCentrosDeCosto();
		
		$queryCcoBotiquinesDispensan = "  SELECT Ccoori,Ccodes
											FROM ".$wbasedato."_000058
										   WHERE Ccoest='on'
										GROUP BY Ccodes;";
		
		$resCcoBotiquinesDispensan=  mysql_query($queryCcoBotiquinesDispensan,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryCcoBotiquinesDispensan." - ".mysql_error());
		$numCcoBotiquinesDispensan = mysql_num_rows($resCcoBotiquinesDispensan);	
		
		$arrayCcoBotiquinesDispensan = array();
		if($numCcoBotiquinesDispensan > 0)
		{
			while($rowCcoBotiquinesDispensan = mysql_fetch_array($resCcoBotiquinesDispensan))
			{
				if(isset($arrayCco[trim($rowCcoBotiquinesDispensan['Ccoori'])]))
				{
					$arrayCcoBotiquinesDispensan[trim($rowCcoBotiquinesDispensan['Ccoori'])]['botiquin'] = trim($rowCcoBotiquinesDispensan['Ccoori']);
					$arrayCcoBotiquinesDispensan[trim($rowCcoBotiquinesDispensan['Ccoori'])]['codcco'] = trim($rowCcoBotiquinesDispensan['Ccoori']);
					$arrayCcoBotiquinesDispensan[trim($rowCcoBotiquinesDispensan['Ccoori'])]['descco'] = $arrayCco[trim($rowCcoBotiquinesDispensan['Ccoori'])];
				}
				
				if(isset($arrayCco[trim($rowCcoBotiquinesDispensan['Ccodes'])]))
				{
					$arrayCcoBotiquinesDispensan[trim($rowCcoBotiquinesDispensan['Ccodes'])]['botiquin'] = trim($rowCcoBotiquinesDispensan['Ccoori']);
					$arrayCcoBotiquinesDispensan[trim($rowCcoBotiquinesDispensan['Ccodes'])]['codcco'] = trim($rowCcoBotiquinesDispensan['Ccodes']);
					$arrayCcoBotiquinesDispensan[trim($rowCcoBotiquinesDispensan['Ccodes'])]['descco'] = $arrayCco[trim($rowCcoBotiquinesDispensan['Ccodes'])];
				}
			}
		}
		
		return $arrayCcoBotiquinesDispensan;			
	}
	
	function consultarCentrosDeCosto()
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;

		$ccoHabilitados	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "ccoHabilitadosDispensacionInsumos" );
		
		$ccoHabilitados = explode(",",$ccoHabilitados);
		
		$queryCCo = " SELECT Ccocod,Cconom,Ccoayu,Ccomsa
						FROM ".$wbasedato."_000011 
					   WHERE (Ccohos='on' OR Ccourg='on' OR Ccoayu='on') 
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
				if($rowCCo['Ccoayu']=="on" && $rowCCo['Ccomsa'] != "on")
				{
					$mostrarCco = consultarConfiguracionAyudaDiagnostica($conex,$wbasedato,$rowCCo['Ccocod']);
				}
				
				// if(in_array("*",$ccoHabilitados) || in_array(trim($rowCCo['Ccocod']),$ccoHabilitados))
				if((in_array("*",$ccoHabilitados) || in_array(trim($rowCCo['Ccocod']),$ccoHabilitados)) && $mostrarCco)
				{
					$arrayCco[trim($rowCCo['Ccocod'])] = trim($rowCCo['Cconom']);
					
				}
			}
		}
		
		return $arrayCco;			
	}
	
	
	function pintarFiltroCcoDispensacion()
	{
		global $wbasedato;
		
		$ccoDispensacion = consultarCentrosDeCostosBotiquines();
		
		$filtroFecha = "";
		$filtroFecha .= "<br>";
		$filtroFecha .= "	<div id='divFiltroCco' align='center' width='100%'>
								<div style='width: 60%;position:absolute;left: 0%'>
								<table id='tablaCco' align='right' style=''>
									<tr class='encabezadoTabla'>
										<td colspan='2' align='center'>Centro de costos a dispensar</td>
									</tr>
									<tr id='trCco'>
										<td class='fila1'>
											<span style='font-size:10pt;'>Centro de costos:</span>
										</td>
										<td class='fila2' id='tdFiltroCco'>";
		$filtroFecha .= "					<select id='filtroCcoDispensacion' name='filtroCcoDispensacion' onChange='validarZona();'>
												<option value=''>Seleccione...</option>";
												foreach($ccoDispensacion as $keycco => $valuecco)
												{
													$esCcoDomiciliario = esCcoDomiciliario( $conex, $wbasedato, $valuecco['codcco'] );
		$filtroFecha .= "							<option value='".$valuecco['codcco']."' botiquin='".$valuecco['botiquin']."' data-domiciliario='".( $esCcoDomiciliario ? 'on' : 'off' )."'>".$valuecco['codcco']."-".$valuecco['descco']."</option>";												
												}
		$filtroFecha .= "					</select>";											
		$filtroFecha .= "				</td>
									</tr>
									<tr id='trZona' style='display:none;'>
										<td class='fila1'>
											<span style='font-size:10pt;'>Zona:</span>
										</td>
										<td class='fila2' id='tdFiltroZona'>
										</td>
									</tr>
								</table>
								
								</div>
								
								<div style='width: 40%;position:absolute;right: 0%'>
								
								
									<table id='tablaBotones' class='botonesPedido' style='display:none;' align='center' width='100%'>
										<tr>
											<td width='50%' align='center'>
												<input type='button' onclick='limpiarPedido();' value='Iniciar valores'>
												<input type='button' id='btnTraerUltimoPedido' onclick='traerUltimoPedido();' value='Traer &uacute;ltimo pedido'>
											</td>
											<td width='50%' align='center'>
												<input type='button' style='position:relative;' value='Cerrar ventana' onclick='cerrarVentana();'>
											</td>
										</tr>
										<tr>
											<td width='50%' align='center'>
												<span align=center onclick='verResumenPedido();' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;position:relative;'>Ver pedido</span>
											</td>
											<td width='50%' align='center'>
												<span id='anularpedido' onclick='anularpedido();' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;'><b>Anular pedido <img src='../../images/medical/root/borrar.png' height=17 width=17 style='vertical-align: middle;'></b></span>
											</td>
										</tr>
										
										
										<!--
										<tr>
											<td width='50%' align='center'>
												<input type='button' id='btnPedirInsumos' class='botonPedidos' style='position:relative;font-weight:bold;' onclick='pedirInsumos();' value='Realizar pedido'>
											</td>
											<td width='50%' align='center'>
												<input type='button' style='position:relative;' value='Cerrar ventana' onclick='cerrarVentana();'>
											</td>
										</tr>
										<tr>
											<td width='50%' align='center'>
												<span align=center onclick='verResumenPedido();' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;position:relative;'>Ver pedido</span>
											</td>
											<td width='50%' align='center'>
												<span id='anularpedido' onclick='anularpedido();' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;'><b>Anular pedido <img src='../../images/medical/root/borrar.png' height=17 width=17 style='vertical-align: middle;'></b></span>
											</td>
										</tr>
										-->
									</table>
								
								</div>
								
							</div>
							
							<br><br><br><br>";
		
		echo $filtroFecha;
	}
	
	
	function consultarZona($codigoCco)
	{
		global $conex;
		global $wbasedato;
		
		//función en comun.php que consulta la tabla de habitaciones por cco, por defectos es la movhos20
		//de lo contrario es lo que indique la tabla movhos 11
		$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $codigoCco );
		
		$queryZonas ="SELECT Habzon,Aredes 
						FROM ".$tablaHabitaciones.",".$wbasedato."_000169 
					   WHERE Habcco='".$codigoCco."' 
						 AND Habest='on' 
						 AND Habzon=Arecod
						 AND Areest='on' 
					GROUP BY Habzon;";
		
		$resZonas = mysql_query($queryZonas, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryZonas . " - " . mysql_error());		   
		$numZonas = mysql_num_rows($resZonas);
		
		$zonas = array();
		if($numZonas>0)
		{
			while($rowZonas = mysql_fetch_array($resZonas))
			{
				$zonas[$rowZonas['Habzon']] = $rowZonas['Aredes'];
			}
		}
		
		return $zonas;
	}
	
	function pintarZona($codigoCco)
	{
		global $conex;
		global $wbasedato;
		
		$zonas = consultarZona($codigoCco);
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
		
		$opcionUrgencias = "<option value='0'>TODAS</option>";
		if($ccoTipo=="urgencias")
		{
			$opcionUrgencias = "<option value='0'>SIN UBICACI&Oacute;N</option>";
		}
		
		
		$html = "";
		if(count($zonas)>0)
		{
			$html .= "<select id='filtroZonas' name='filtroZonas'>
						<option value=''>Seleccione...</option>
						".$opcionUrgencias;
						foreach($zonas as $keyZona => $valueZona)
						{
			$html .= "		<option value='".$keyZona."'>".$valueZona."</option>";												
						}
			$html .= "</select>";
		}
		
		
		return $html;
	}
	
	/**
	 * Metodo que permite obtener el listado de pacientes de la tabla de citas
	 * si esta existe o de lo contrario trae los pacientes del servicio o centro de costo. 
	 * 
	 * 
	 * 
	 * @author Joel Payares Hernández
	 */
	function consultarPacientesCco($codigoCco,$zona)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoHce;
		global $wbasedatoCliame;
		global $wemp_pmla;
		
		$tablaHabitaciones = "";
		$condicionesExtrasTurnero = "";
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);

		$esAyudaDiagnostica = true;

		// si es urgencias y sin ubicacion
		if($ccoTipo=="urgencias" && $zona=="0")
		{
			$queryHabitaciones =  "SELECT Ubihis AS Habhis,Ubiing AS Habing,CONCAT_WS('-',Ubihis,Ubiing) AS Habcod,CONCAT_WS('-',Ubihis,Ubiing) AS Habcpa,Pacced,Pactid,Pacno1,Pacno2,Pacap1,Pacap2,Mtrcur 
									 FROM ".$wbasedato."_000018,root_000037,root_000036,".$wbasedatoHce."_000022 
									WHERE Ubisac='".$codigoCco."' 
									  AND Ubiald!='on' 
									  AND Orihis=Ubihis 
									  AND Oriing=Ubiing 
									  AND Oriori='".$wemp_pmla."'
									  AND Pactid=Oritid
									  AND Pacced=Oriced
									  AND Mtrhis=Ubihis
									  AND Mtring=Ubiing
									  AND Mtrcua!='on';";
		}
		elseif($ccoTipo=="ayudaDiagnostica")
		{
			$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $codigoCco );
			
			$tablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedato,$codigoCco,true);

			$prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);

			// $queryHabitaciones =  "SELECT ".$prefijoCampos."tip,".$prefijoCampos."doc,".$prefijoCampos."his,".$prefijoCampos."ing,CONCAT_WS(' ',Pacno1,Pacno2,Pacap1,Pacap2) AS nombre,Ubihis,Ubiing
									 // FROM ".$tablaAyudasDiagnosticas." a,".$prefijoTablaAyudasDiagnosticas."_000023 b,".$wbasedatoCliame."_000100,".$wbasedatoCliame."_000101,".$wbasedato."_000018
									// WHERE Fecha='".date("Y-m-d")."' 
									  // AND Activo='A' 
									  // AND Causa='' 
									  // AND ".$prefijoCampos."doc=Cedula
									  // AND b.Fecha_data=Fecha
									  // AND ".$prefijoCampos."est='on'
									  // AND ".$prefijoCampos."fpr!='on'
									  // AND ".$prefijoCampos."acp!=''
									  // AND Pactdo=".$prefijoCampos."tip
									  // AND Pacdoc=".$prefijoCampos."doc
									  // AND inghis=Pachis
									  // AND Ubihis=Inghis 
									  // AND Ubiing=Ingnin
									  // AND Ubiald!='on';";
									  
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

			/**
			 * Se valida si el campo tct => Tabla campos citas, es diferente de vacio
			 * para así ir a consultar los pacientes que estan en habitaciones del
			 * servicio a consultar.
			 */
			if( $rowsTablasCitas['Ccotct'] == '' )
			{
				$queryHabitaciones = "SELECT * FROM
						(SELECT Ubisac as Habcco, Habcod, Habhis, Habing,
									CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
									Ubialp, Ubifap, Ubihap, pacced, pactid, '1' as ordenes, Habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan, '2' as orden
						   FROM ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000011, root_000037, root_000036,".$wbasedato."_000016, ".$tablaHabitaciones."
						  WHERE ubiald = 'off'
							AND Ccocir != 'on'
							AND ubisac = Ccocod
							AND oriori = '".$wemp_pmla."'
							AND ubihis = orihis
							AND ubiing = oriing
							AND oriced = pacced
							AND oritid = pactid
							AND orihis = inghis
							AND oriing = inging
							AND habhis = inghis
							AND habing = inging
							AND ubimue != 'on'
							AND Ubiste in ('".trim($codigoCco)."')
							UNION
						SELECT Ubisac as Habcco, (SELECT Cconom FROM ".$wbasedato."_000011 WHERE Ccocod = Ubisac) as Habcod, ubihis as Habhis, ubiing as Habing,
									CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
									Ubialp, Ubifap, Ubihap, pacced, pactid, '1' as ordenes,'2000' as habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan, '1' as orden
						   FROM ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000011, root_000037, root_000036,".$wbasedato."_000016
						  WHERE ubiald = 'off'
							AND ubisac = Ccocod
							AND oriori = '".$wemp_pmla."'
							AND ubihis = orihis
							AND ubiing = oriing
							AND oriced = pacced
							AND oritid = pactid
							AND orihis = inghis
							AND oriing = inging
							AND ubimue != 'on'
							AND Ubisac in ('".trim($codigoCco)."')) as t
					   GROUP BY habhis, habing
					   ORDER BY orden DESC, Habord*1, Habcod";
			}
			else
			{
				/**
				 * Se valida si campo pci => Prefijo citas, es vacío y así mostrar el
				 * mensaje pertinente para corregir el dato en la tabla de centros de costo.
				 */
				if ( explode("_", trim( $tablaAyudasDiagnosticas ) )[0] == "" ) {
					return "El campo prefijo de citas no existe &oacute; est&aacute; vac&iacute;o, en la tabla servicios o centros de costos!";
				}

				/**
				 * Se valida si campo ntc => Numero tabla citas, es vacío y así mostrar el
				 * mensaje pertinente para corregir el dato en la tabla de centros de costo.
				 */
				if ( explode("_", trim( $tablaAyudasDiagnosticas ) )[1] == "" ) {
					return "El campo número tabla citas no existe &oacute; est&aacute; vac&iacute;o, en la tabla servicios o centros de costos!";
				}

				$queryHabitaciones =  "SELECT ".$tipoDocumento.",".$numeroDocumento.",CONCAT_WS(' ',Pacno1,Pacno2,Pacap1,Pacap2) AS nombre,Ubihis,Ubiing
										FROM ".$tablaAyudasDiagnosticas." a,".$tablaTurnero." b,".$wbasedatoCliame."_000100,".$wbasedatoCliame."_000101,".$wbasedato."_000018
										WHERE Fecha='".date("Y-m-d")."' 
										AND Activo='A' 
										AND Causa='' 
										AND b.".$numeroDocumento."=Cedula
										AND Pactdo=b.".$tipoDocumento."
										AND Pacdoc=b.".$numeroDocumento."
										AND b.Fecha_data=Fecha
										".$condicionesExtrasTurnero."
										AND inghis=Pachis
										AND Ubihis=Inghis 
										AND Ubiing=Ingnin
										AND Ubiald!='on';";
			}
		}
		else
		{
			$condicionZona = "";
			if($zona!="" && $zona!="0")
			{
				$condicionZona = "AND Habzon='".$zona."'";
			}
			
			$filtroPacientes = '';
			if( isset( $_POST['pacDomciliarios'] ) && $_POST['pacDomciliarios'] ){
				$filtroPacientes = " AND Ubihis IN('".implode( "','", $_POST['pacDomciliarios'] )."')";
			}
			
			//función en comun.php que consulta la tabla de habitaciones por cco, por defectos es la movhos20
			//de lo contrario es lo que indique la tabla movhos 11
			$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $codigoCco );
			
			$queryHabitaciones =  " SELECT Habhis,Habing,Habcod,Habcpa,Pacced,Pactid,Pacno1,Pacno2,Pacap1,Pacap2,'' AS Mtrcur  
									  FROM ".$tablaHabitaciones.",root_000037,root_000036,".$wbasedato."_000018 
									 WHERE Habcco='".$codigoCco."'
									   AND Habhis!=''
									   ".$condicionZona."
									   AND Habest='on'
									   AND Orihis=Habhis
									   AND Oriing=Habing
									   AND Oriori='".$wemp_pmla."'
									   AND Pacced=Oriced
									   AND Pactid=Oritid
									   AND Ubihis=Habhis
									   AND Ubiing=Habing
									   AND Ubiald='off'
									   $filtroPacientes
								  ORDER BY Habcpa;";
		}
		
		$resHabitaciones = mysql_query($queryHabitaciones, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryHabitaciones . " - " . mysql_error());		   
		$numHabitaciones = mysql_num_rows($resHabitaciones);

		$pacientes = array();
		if($numHabitaciones>0)
		{
			while($rowHabitaciones = mysql_fetch_array($resHabitaciones))
			{
				if($ccoTipo=="ayudaDiagnostica")
				{
					$historia = $rowHabitaciones[2];
					$ingreso = $rowHabitaciones[3];
					
					// si no tiene historia e ingreso es porque es de chequeo ejecutivo
					if( $rowHabitaciones[2]=="" && $rowHabitaciones[3]=="" )
					{
						$historia = $rowHabitaciones['Ubihis'];
						$ingreso = $rowHabitaciones['Ubiing'];
					}
					
					if($historia!="" && $ingreso!="")
					{
						$habitacion = $historia."-".$ingreso;
					
						$pacientes[$habitacion]['historia'] = $historia;
						$pacientes[$habitacion]['ingreso'] = $ingreso;
						$pacientes[$habitacion]['habitacion'] = $habitacion;
						$pacientes[$habitacion]['descHabitacion'] = $habitacion;
						$pacientes[$habitacion]['tipoDocumento'] = $rowHabitaciones[11];
						$pacientes[$habitacion]['documento'] = $rowHabitaciones[10];
						$pacientes[$habitacion]['nombre'] = $rowHabitaciones['Nombre'];
						$pacientes[$habitacion]['consultaUrgencias'] = "";
					}
				}
				else
				{
					$habitacionDesc = trim($rowHabitaciones['Habcpa']);
					if(trim($habitacionDesc)=="")
					{
						$habitacionDesc = trim($rowHabitaciones['Habcod']);
					}
					$habitacion = trim($rowHabitaciones['Habcod']);
					
					$pacientes[$habitacion]['historia'] = $rowHabitaciones['Habhis'];
					$pacientes[$habitacion]['ingreso'] = $rowHabitaciones['Habing'];
					$pacientes[$habitacion]['habitacion'] = trim($rowHabitaciones['Habcod']);
					$pacientes[$habitacion]['descHabitacion'] = $habitacionDesc;
					$pacientes[$habitacion]['tipoDocumento'] = $rowHabitaciones['Pactid'];
					$pacientes[$habitacion]['documento'] = $rowHabitaciones['Pacced'];
					$pacientes[$habitacion]['nombre'] = $rowHabitaciones['Pacno1']." ".$rowHabitaciones['Pacno2']." ".$rowHabitaciones['Pacap1']." ".$rowHabitaciones['Pacap2'];
					$pacientes[$habitacion]['consultaUrgencias'] = $rowHabitaciones['Mtrcur'];
				}				
			}
		}

		return $pacientes;
	}
	
	function consultarPedidoPendiente($codBotiquin,$codigoCco,$wuse)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		
		if($codBotiquin!="" && $codigoCco!="")
		{
			$queryPedido =  " SELECT Pedcod 
								FROM ".$wbasedato."_000230 
							   WHERE Pedbot='".$codBotiquin."' 
								 AND Pedcco='".$codigoCco."' 
								 AND Pedaux='".$wuse."' 
								 AND Pedent='off' 
								 AND Pedest='on';";
		}
		else
		{
			$queryPedido =  " SELECT Pedcod 
								FROM ".$wbasedato."_000230 
							   WHERE Pedaux='".$wuse."' 
								 AND Pedent='off' 
								 AND Pedest='on';";
		}
		
		// echo$queryPedido;
		$resPedido = mysql_query($queryPedido, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPedido . " - " . mysql_error());		   
		$numPedido = mysql_num_rows($resPedido);
		
		$codPedido = "";
		if($numPedido>0)
		{
			$rowPedido = mysql_fetch_array($resPedido);
			$codPedido = $rowPedido['Pedcod'];
		}
		
		return $codPedido;
	}
	
	function consultarPedidoEntregado($codPedido,$codBotiquin,$codigoCco,$wuse)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		
		$queryPedido =  " SELECT Pedent 
							FROM ".$wbasedato."_000230 
						   WHERE Pedcod='".$codPedido."' 
						     AND Pedbot='".$codBotiquin."' 
						     AND Pedcco='".$codigoCco."' 
							 AND Pedaux='".$wuse."' 
							 AND Pedest='on';";
		
		$resPedido = mysql_query($queryPedido, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPedido . " - " . mysql_error());		   
		$numPedido = mysql_num_rows($resPedido);
		
		$entregado = "";
		if($numPedido>0)
		{
			$rowPedido = mysql_fetch_array($resPedido);
			$codPedido = $rowPedido['Pedent'];
		}
		
		return $codPedido;
	}
	
	function consultarInsumosPedido($codPedido)
	{
		global $conex;
		global $wbasedato;
		
		$queryPedido =  " SELECT Dpehis,Dpeing,Dpehab,Dpeins,Dpeped 
							FROM ".$wbasedato."_000231 
							WHERE Dpecod='".$codPedido."'
							AND Dpeest='on';";
		
		$resPedido = mysql_query($queryPedido, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPedido . " - " . mysql_error());		   
		$numPedido = mysql_num_rows($resPedido);
		
		$arrayPedido = array();
		if($numPedido>0)
		{
			while($rowPedido = mysql_fetch_array($resPedido))
			{
				$arrayPedido[$rowPedido['Dpeins']."_".$rowPedido['Dpehis']."-".$rowPedido['Dpeing']] = $rowPedido['Dpeped'];
			}
		}
		
		return $arrayPedido;
	}
	
	
	function consultarNombreAuxiliar($codAuxiliar)
	{
		global $conex;
		
		$queryAuxiliar =  "SELECT Descripcion
							 FROM usuarios
						    WHERE Codigo='".$codAuxiliar."' 
							  AND Activo='A';";
		
		$resAuxiliar = mysql_query($queryAuxiliar, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryAuxiliar . " - " . mysql_error());		   
		$numAuxiliar = mysql_num_rows($resAuxiliar);
		
		$nombreAuxiliar = "";
		if($numAuxiliar>0)
		{
			$rowAuxiliar = mysql_fetch_array($resAuxiliar);
			
			$nombreAuxiliar = $rowAuxiliar['Descripcion'];
			
		}
		
		return $nombreAuxiliar;
	}
	
	function pintarInsumosBotiquin($codBotiquin,$codigoCco,$zona,$pedidosPorCco)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		
		
		
		$estaEnEntrega = hayPedidosEnEntrega( $conex, $wemp_pmla, $wbasedato, $wuse, $codBotiquin );
		if( $estaEnEntrega ){
			
			$html = "<p align='center' class='fondoamarillo'>
						EL PEDIDO SE ESTA DISPENSANDO. DEBE ESPERAR QUE EL PEDIDO SE ENTREGUE ANTES DE PODER HACER OTRO.
					</p>";
					
			$data['error']=0;
			$data['html']=utf8_encode($html);
			
			return $data;
		}

		// validar saldos de la auxiliar
		$validarSaldo = consultarAliasPorAplicacion($conex, $wemp_pmla, 'validarSaldoAuxiliar');

		$saldoPendiente = 0;
		
		if($validarSaldo=="on")
		{
			$saldoPendiente = consultarSaldoAuxiliar( $conex, $wbasedato, time()-24*3600, $wuse, $codBotiquin );
		}
		$permitePedidosPorPaciente = consultarCcoPedidoPorPaciente($codigoCco);

		$saldoActualAuxiliar = 0;
		$tienePedidos = "";
		$tienePedidosCco = "";
		if($permitePedidosPorPaciente || $pedidosPorCco=="on")
		{
			$saldoActualAuxiliar = consultarSaldoActualAuxiliar($wuse,$codBotiquin);
			
			if($pedidosPorCco=="on")
			{
				$tienePedidos = consultarPedidoPendiente("","",$wuse);
				$tienePedidosCco = explode("-",$tienePedidos);
				$tienePedidosCco = $tienePedidosCco[0];
			}
		}

		if(isset($saldoPendiente) && $saldoPendiente!= 0)
		{
			$html = "<p align='center' class='fondoamarillo'>
						<b style='font-size:12pt'>Tiene insumos con saldo mayores a 24 horas.</b>
						<br>
						<span style='font-family: verdana;font-weight:bold;font-size: 12pt;color: #0033FF;text-decoration: underline;cursor:pointer;'>
							<a href='monitorInsumosAuxiliares.php?wemp_pmla=".$wemp_pmla."' target='blank'>Monitor de insumos</a>
						</span>
						
					</p>";
					
			$data['error']=1;
			$data['html']=utf8_encode($html);
		}
		elseif($saldoActualAuxiliar>0)
		{
			$html = "<p align='center' class='fondoamarillo'>
						<b style='font-size:12pt'>Tiene saldo de insumos, no puede realizar otro pedido.</b>
						<br>
						<span style='font-family: verdana;font-weight:bold;font-size: 12pt;color: #0033FF;text-decoration: underline;cursor:pointer;'>
							<a href='monitorInsumosAuxiliares.php?wemp_pmla=".$wemp_pmla."' target='blank'>Monitor de insumos</a>
						</span>
						
					</p>";
					
			$data['error']=1;
			$data['html']=utf8_encode($html);
		}
		elseif($tienePedidos!="" && $tienePedidosCco!=$codigoCco)
		{
			$html = "<p align='center' class='fondoamarillo'>
						<b style='font-size:12pt'>Tiene pedidos pendientes (".$tienePedidos.").</b>
						<br>
						<span style='font-family: verdana;font-weight:bold;font-size: 12pt;color: #0033FF;text-decoration: underline;cursor:pointer;'>
						</span>
						
					</p>";
					
			$data['error']=1;
			$data['html']=utf8_encode($html);
		}
		else
		{
			// funcion en botiquin.inc.php
			$insumos = consultarInsumo( $conex, $wbasedato, "%", $codBotiquin, false );
			$insumosBotiquin = $insumos['data'];
			$cantPacientes = 0;

			$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco); // ayudaDiagnostica

			$pacientes= consultarPacientesCco($codigoCco,$zona);
			
			if ( is_array( $pacientes ) )
			{
				$cantPacientes = count($pacientes);
			}

			// $cantPacientes = count($pacientes);
			
			if($cantPacientes>0)
			{
				$cadenaTooltipPacientes = "";
				$cadenaTooltipInsPac = "";
				
				$colspanTotal = 6+$cantPacientes;
				
				
				
				$tituloPacientes = "HABITACIONES";
				if(($ccoTipo=="urgencias" && $zona=="0") || $ccoTipo=="ayudaDiagnostica")
				{
					$tituloPacientes = "PACIENTES";
				}
				
				$codPedido = consultarPedidoPendiente($codBotiquin,$codigoCco,$wuse);
				$tituloPedido = "";
				$cantidadesPedidas = array();
				// $colspanOpciones = round($colspanTotal/4);
				$colspanOpciones = ceil($colspanTotal/3);
				if($codPedido!="")
				{
					setPedidoEditando( $conex, $wbasedato, $codPedido, 'on' );
					
					$tituloPedido = "<p align='center' style='font-size:18;font-weight:bold;'>
										<span style='background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N&uacute;mero de pedido: ".$codPedido."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span>
									</p>
									<br>";
					
					$cantidadesPedidas = consultarInsumosPedido($codPedido);
					
					// $colspanOpciones = $colspanTotal/2;
				}
				
				$arrayPaquetes = consultarPaquetes($codigoCco);
				$paquetes = $arrayPaquetes['paquetes'];
				
				$htmlPaquetes = "";
				if(count($paquetes)>0)
				{
					$htmlPaquetes = "<span id='seleccionarPacientes' name='seleccionarPacientes' style='background-color:#D6D6D6;color:#000000;border-radius:2px;position:absolute;right:1%;cursor:pointer;' onclick='seleccionarPacientesPaquetes();'>&nbsp; Paquetes &nbsp;</span>
									 <span id='seleccionarPaquete' name='seleccionarPaquete' style='background-color:#D6D6D6;color:#000000;border-radius:2px;position:absolute;right:1%;cursor:pointer;display:none;top:3px' onclick='abrirModalSeleccionarPaquetes();'>&nbsp; Seleccionar &nbsp;<br>&nbsp; paquete &nbsp;<img src='../../images/medical/hce/cancel.png' title='Cancelar' style='cursor:pointer;vertical-align:middle' onclick='event.stopPropagation();volverAPaquetes();'></span>";
				}
				
				$html = "";
				$html .= "	<div id='divInsumosBotiquin'>
				
								<p align='center' style='font-size:18;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Responsable: ".consultarNombreAuxiliar($wuse)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>
								
								".$tituloPedido."
								
								<input type='hidden' id='codPedido' name='codPedido' value='".$codPedido."'>
								<input type='hidden' id='permitePedidosPorPaciente' name='permitePedidosPorPaciente' value='".$permitePedidosPorPaciente."'>
								
								<table id='tableInsumosBotiquin' align='center'>
								<thead>
									<tr style='background-color:#FFFFFF'>
										<td colspan='".($colspanOpciones)."'>
											<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
												Buscar insumo:&nbsp;&nbsp;</b><input id='buscarInsumo' type='text' placeholder='Insumo a buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
											</span>
										</td>";
										if($codPedido=="")
										{
											$codUltimoPedido = consultarUltimoPedido($codigoCco);
											if($codUltimoPedido!="")
											{
												
				$html .= "						<input type='hidden' id='codigoUltimoPedido' name='codigoUltimoPedido' value='".$codUltimoPedido."'>";
												
											}
										}
				$html .= "				<td colspan='".($colspanOpciones)."' align='center'>
											<input type='button' id='btnPedirInsumos' class='botonPedidos' style='position:relative;font-weight:bold;' onclick='pedirInsumos();' value='Realizar pedido'>
										</td>
										<td colspan='".($colspanOpciones)."' align='right'>
											<div id='divConvensionesPedidoGrabado' align='center' width='80%' style='display:none;'>
												<table align='right' style='border: 1px solid black;border-radius: 5px;' width='30%'>
													<tr>
														<td align='center' style='font-size:8pt' colspan='2'><b>Convenciones</b></td>
													</tr>
													<tr>
														<td class='fondoverde' style='font-size:5pt;border-radius: 2px;' >&nbsp;&nbsp;&nbsp;</td>
														<td style='font-size:7pt;vertical-align:top;'>Insumo registrado</td>
													</tr>
													<tr>
														<td class='fondorojo' style='font-size:5pt;border-radius: 2px;'>&nbsp;&nbsp;&nbsp;</td>
														<td style='font-size:7pt;vertical-align:top;'>No se registr&oacute; el insumo</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
									
									
									<tr class='encabezadoTabla'  align='center' height='40px'>
										<td colspan='5' style='position:relative;'>
											".$htmlPaquetes."
																						
											INSUMOS
										</td>
										
										
										<td colspan='".$cantPacientes."' style='position:relative;'>
											".$tituloPacientes."
										</td>
										<td rowspan='2'>Cant<br>total<br>ordenada</td>
									</tr>
									<tr class='encabezadoTabla' align='center'>
										<td></td>
										<td>C&oacute;digo</td>
										<td>Descripci&oacute;n</td>
										<td>Presentaci&oacute;n</td>
										<td>Saldo botiqu&iacute;n</td>
									";
									
									foreach($pacientes as $keyPaciente => $valuePaciente)
									{
										$enConsulta = "";
										if(($ccoTipo=="urgencias" && $zona=="0") && $valuePaciente['consultaUrgencias']=="on")
										{
											$enConsulta = "<br><span class='fondoamarillo' style='font-size:8pt;border-radius:3px' title='No puede realizar pedidos para pacientes en consulta'>En consulta</span>";
										}
										
										// ------------------------------------------
										// Tooltip
										// ------------------------------------------	
										
										$infoTooltip = "Historia: ".$valuePaciente['historia']."-".$valuePaciente['ingreso']."<br>";
										$infoTooltip .= "Paciente: ".$valuePaciente['nombre']."<br>";
										$infoTooltip .= "Documento: ".$valuePaciente['tipoDocumento']." ".$valuePaciente['documento'];
										$infoTooltip .= "<br><br><span style=\"font-size:10pt;color:#0033FF;\">CLIC PARA PEDIR PAQUETES</span>";
										
										
										$tooltipPacientes = "<div id=\"dvTooltipPacientes\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltip."</div>";
										
										$cadenaTooltipPacientes .= "tooltipPacientes_".$valuePaciente['habitacion']."|";
										
				$html .= "				<td id='tooltipPacientes_".$valuePaciente['habitacion']."'  title='".$tooltipPacientes."' onclick='seleccionarPaquete(\"".$valuePaciente['habitacion']."\");marcarColumna(\"".$valuePaciente['habitacion']."\");' style='cursor:pointer;' enConsulta='".$valuePaciente['consultaUrgencias']."'>
											&nbsp;".$valuePaciente['descHabitacion']."&nbsp;
											".$enConsulta."
											<input type='checkbox' class='SeleccionarPacientesPaquete' id='checkPacienteConPaquete_".$valuePaciente['habitacion']."' name='checkPacienteConPaquete_".$valuePaciente['habitacion']."' paciente='".$valuePaciente['historia']."-".$valuePaciente['ingreso']."' style='display:none;' onclick='seleccionarPacientePaquete(event,this);'>
										</td>";						
				
									}
									
				$html .= "			</tr>
									</thead>
									";		
									
									$cantFrecuentes = 0;
									$cantNoFrecuentes = 0;
									
									foreach($insumosBotiquin as $keyInsumo => $valueInsumo)
									{
										$saldo = $valueInsumo['saldo'];
										
										//Si no permite negativos, sigo con el siguiente articulo
										if( $saldo*1 <= 0 && !$valueInsumo['permiteNegativos'] )
											continue;
										
										$esFrecuente = "style='background-color:#FFD125;cursor:default;' title='De uso poco frecuente'";
										if($valueInsumo['esFrecuente']=='on')
										{
											$esFrecuente = "style='background-color:#5EDD34;cursor:default;' title='De uso frecuente'";
										}	
										else
										{
											$cantNoFrecuentes++;
											if($cantNoFrecuentes==1)
											{
				$html .= "						<tr class='encabezadoTabla'><td colspan='".$colspanTotal."' align='center'>INSUMOS DE USO POCO FRECUENTE</td></tr>";													
											}
										}
										
										
										if ($fila_lista=='Fila1')
											$fila_lista = "Fila2";
										else
											$fila_lista = "Fila1";
										
				$html .= "				<tr class='".$fila_lista." find'>
											<td ".$esFrecuente." class='frecuente' width='20px'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
											<td>".$valueInsumo['codigo']."</td>
											<td><table style='font-size:10pt;'><tr><td><b>Comercial: </b>".utf8_decode($valueInsumo['nombreComercial'])."</td></tr><tr><td><b>Gen&eacute;rico: </b>".utf8_decode($valueInsumo['nombreGenerico'])."</td></tr></table></td>
											<td align='center'>".$valueInsumo['unidad']['descripcion']."</td>
											<td align='center'><span id='saldoInsumo_".$valueInsumo['codigo']."' name='saldoInsumo_".$valueInsumo['codigo']."' >".$saldo."</span></td>";
										
										
										$cantOrdenada=0;
										foreach($pacientes as $keyPaciente => $valuePaciente)
										{
											$idCampo = $valueInsumo['codigo']."_".$valuePaciente['historia']."-".$valuePaciente['ingreso'];
											
											
											$valorInput = "";
											$fondoAmarilloInput = "";
											
											if(isset($cantidadesPedidas[$idCampo]) && $cantidadesPedidas[$idCampo]!="0")
											{
												$valorInput = $cantidadesPedidas[$idCampo];
												
												$fondoAmarilloInput = "fondoamarillo";
												
												$cantOrdenada += $valorInput;
											}
											
											
											// ------------------------------------------
											// Tooltip
											// ------------------------------------------	
											
											if(($ccoTipo=="urgencias" && $zona=="0") || $ccoTipo=="ayudaDiagnostica")
											{
												$infoTooltipInsumoPaciente = "";
					
											}
											else
											{
												$infoTooltipInsumoPaciente = "<b>Habitaci&oacute;n: <span style=\"font-size:12pt;color:#BC0B0B;\">".$valuePaciente['descHabitacion']."</span></b><br>";
											}	
											
											
											$infoTooltipInsumoPaciente .= "<b>Historia:</b> ".$valuePaciente['historia']."-".$valuePaciente['ingreso']."<br>";
											$infoTooltipInsumoPaciente .= "<b>Paciente:</b> ".$valuePaciente['nombre']."<br>";
											$infoTooltipInsumoPaciente .= "<b>Documento:</b> ".$valuePaciente['tipoDocumento']." ".$valuePaciente['documento']."<br>";
											$infoTooltipInsumoPaciente .= "<b>Insumo:</b> ".$valueInsumo['codigo']." - ".$valueInsumo['nombreComercial'];
											
											$tooltipInsPac = "<div id=\"dvTooltipInsPac\" style=\"font-family:verdana;font-size:10pt\" align=left>".$infoTooltipInsumoPaciente."</div>";
											
											$cadenaTooltipInsPac .= "tooltipInsPac_".$idCampo."|";
											
				// $html .= "					<td align='center' id='tooltipInsPac_".$idCampo."' title='".$tooltipInsPac."' style='position:relative;'><input type='text' id='cant_".$idCampo."' name='cant_".$idCampo."' class='".$fondoAmarilloInput."' style='cursor:default;' onfocus='seleccionarColumna(this);' habitacion='".$valuePaciente['habitacion']."' insumo='".$valueInsumo['codigo']."' onChange='validarCantidadInsumo(this);'  onkeypress='return soloNumeros(event,this);' size='1' value='".$valorInput."'></td>";	
				$html .= "					<td align='center' id='tooltipInsPac_".$idCampo."' infoTooltipManual='".$tooltipInsPac."' style='position:relative;'><input type='text' id='cant_".$idCampo."' name='cant_".$idCampo."' class='".$fondoAmarilloInput."' style='cursor:default;' onfocus='seleccionarColumna(this);' habitacion='".$valuePaciente['habitacion']."' insumo='".$valueInsumo['codigo']."' onChange='validarCantidadInsumo(this);'  onkeypress='return soloNumeros(event,this);' onkeydown='return cambioInput(event,this);' onblur='ocultarTooltip(this);' size='1' value='".$valorInput."'></td>";	
				
				
				
										}
										
				$html .= "				<td align='center'><span id='cantTotalOrdenada_".$valueInsumo['codigo']."' name='cantTotalOrdenada_".$valueInsumo['codigo']."' style='font-weight:bold;'>".$cantOrdenada."</span></td>";								
				
									}
									
				$html .= "				<input type='hidden' id='cadenaTooltipPacientes' name='cadenaTooltipPacientes' value='".$cadenaTooltipPacientes."'>";						
				$html .= "				<input type='hidden' id='cadenaTooltipInsPac' name='cadenaTooltipInsPac' value='".$cadenaTooltipInsPac."'>";						
				$html .= "				<input type='hidden' id='pedidoGuardado' name='pedidoGuardado' value='off'>";						
									
									
									
				$html .= "		</table>
								<p align=center><input type='button' id='btnPedirInsumos' class='botonPedidos' onclick='pedirInsumos();' style='font-weight:bold;' value='Realizar pedido'></p>
							</div>";
				
				$data['error']=0;				
			}
			else
			{
				// si tiene un pedido pendiente y es de ayudas diagnosticas anulpedido
				$codPedido = consultarPedidoPendiente($codBotiquin,$codigoCco,$wuse);

				if($codPedido!="" && $ccoTipo=="ayudaDiagnostica")
				{
					$html = "<p align='center'>
								<input type='hidden' id='codPedido' name='codPedido' value='".$codPedido."'>
								<b style='font-size:12pt'>No hay habitaciones ocupadas para este centro de costos y tiene un pedido pendiente.</b>
								<br>
								<span id='anularpedido' onclick='anularpedido();' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;'><b>Anular pedido anterior</b></span>
							</p>";
				}
				elseif( $cantPacientes == 0)
				{
					$html = "<p align='center'>
								<b style='font-size:12pt'>{$pacientes}</b>
							</p>";
				}
				else
				{
					$html = "<p align='center'>
								<b style='font-size:12pt'>No hay habitaciones ocupadas para este centro de costos.</b>
							</p>";
				}
				
				// $html = "<p align='center'>
							// <b style='font-size:12pt'>No hay habitaciones ocupadas para este centro de costos.</b>
						// </p>";
				$data['error']=1;		
			}
			
			
			
			// $data['error']=0;
			$data['html']=utf8_encode($html);	
		}
		
		
		return $data;
	}
	
	function pintarResumenInsumosBotiquin($codBotiquin,$codigoCco,$codPedido,$insumosPedido,$zona)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		
		if(count($insumosPedido)>0)
		{
			$insumos = consultarInsumo( $conex, $wbasedato, "%", $codBotiquin, false );
			$insumosBotiquin = $insumos['data'];
			
			$pacientes= consultarPacientesCco($codigoCco,$zona);
			
			$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
			
			$tituloPacientes = "HABITACIONES";
			if(($ccoTipo=="urgencias" && $zona=="0") || $ccoTipo=="ayudaDiagnostica")
			{
				$tituloPacientes = "PACIENTES";
			}
			
			$pacientesResumen = array();
			$insumosResumen = array();
			
			foreach($insumosPedido as $keyInsumosPedido => $valueInsumosPedidos)
			{
				$pacientesResumen[$valueInsumosPedidos['habitacion']] = $valueInsumosPedidos['historia']."-".$valueInsumosPedidos['ingreso'];
				
				$insumosResumen[$valueInsumosPedidos['codInsumo']] = $valueInsumosPedidos['codInsumo'];
			}
			
			
			$cantPacientes = count($pacientesResumen);
			$cadenaTooltipPacientes = "";
			$cadenaTooltipInsPac = "";
			
			$colspanTotal = 5+$cantPacientes;
			
			
			
			$tituloPedido = "";
			if($codPedido!="")
			{
				$tituloPedido = "<p align='center' style='font-size:18;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N&uacute;mero de pedido: ".$codPedido."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p><br>";
			}
			
			
			$html = "";
			$html .= "	<div id='divResumenInsumosPedidos'>
							
							
							".$tituloPedido."
							
							<p align=center><span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span></p>
							
							<table id='tableInsumosBotiquinResumen' align='center'>
								<tr>
									<td colspan='".($colspanTotal/2)."'>
										<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
											Buscar insumo:&nbsp;&nbsp;</b><input id='buscarInsumoResumen' type='text' placeholder='Insumo a buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
										</span>
									</td>
								</tr>
								
								
								<tr class='encabezadoTabla'  align='center'>
									<td colspan='4'>INSUMOS</td>
									<td colspan='".$cantPacientes."'>".$tituloPacientes."</td>
									<td rowspan='2'>Cant<br>total<br>ordenada</td>
								</tr>
								<tr class='encabezadoTabla' align='center'>
									<td>C&oacute;digo</td>
									<td>Descripci&oacute;n</td>
									<td>Presentaci&oacute;n</td>
									<td>Saldo botiqu&iacute;n</td>
								";
								
								foreach($pacientes as $keyPaciente => $valuePaciente)
								{
									
									if(isset($pacientesResumen[$keyPaciente]))
									{
										
										// ------------------------------------------
										// Tooltip
										// ------------------------------------------	
										
										$infoTooltip = "Historia: ".$valuePaciente['historia']."-".$valuePaciente['ingreso']."<br>";
										$infoTooltip .= "Paciente: ".$valuePaciente['nombre']."<br>";
										$infoTooltip .= "Documento: ".$valuePaciente['tipoDocumento']." ".$valuePaciente['documento'];
										
										
										$tooltipPacientes = "<div id=\"dvTooltipPacientesRes\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltip."</div>";
										
										$cadenaTooltipPacientes .= "tooltipPacientesRes_".$valuePaciente['habitacion']."|";
										
				$html .= "				<td id='tooltipPacientesRes_".$valuePaciente['habitacion']."' title='".$tooltipPacientes."' onclick='marcarColumna(\"".$valuePaciente['habitacion']."\");' style='cursor:pointer;'>".$valuePaciente['descHabitacion']."</td>";						
									}
								
								}
								
								
			$html .= "			</tr>";						
								foreach($insumosBotiquin as $keyInsumo => $valueInsumo)
								{
									if(isset($insumosResumen[$valueInsumo['codigo']]))
									{
										$saldo = $valueInsumo['saldo'];
									
										if ($fila_lista=='Fila1')
											$fila_lista = "Fila2";
										else
											$fila_lista = "Fila1";
										
				$html .= "				<tr class='".$fila_lista." find'>
											<td>".$valueInsumo['codigo']."</td>
											<td>".utf8_decode($valueInsumo['nombreGenerico'])."</td>
											<td align='center'>".$valueInsumo['unidad']['descripcion']."</td>
											<td align='center'><span id='saldoInsumo_".$valueInsumo['codigo']."' name='saldoInsumo_".$valueInsumo['codigo']."' >".$saldo."</span></td>";
										
										
										$cantOrdenada=0;
										foreach($pacientes as $keyPaciente => $valuePaciente)
										{
											if(isset($pacientesResumen[$keyPaciente]))
											{
												$idCampo = $valueInsumo['codigo']."_".$valuePaciente['historia']."-".$valuePaciente['ingreso'];
												
												
												$valorInput = "";
												$fondoAmarilloInput = "";
												
												if(isset($insumosPedido[$idCampo]))
												{
													$valorInput = $insumosPedido[$idCampo]['cantidad'];
													$fondoAmarilloInput = "fondoamarillo";
													
													$cantOrdenada += $valorInput;
												}
												
												
												// ------------------------------------------
												// Tooltip
												// ------------------------------------------	
												if(($ccoTipo=="urgencias" && $zona=="0") || $ccoTipo=="ayudaDiagnostica")
												{
													$infoTooltipInsumoPaciente = "";
						
												}
												else
												{
													$infoTooltipInsumoPaciente  = "Habitaci&oacute;n: ".$valuePaciente['descHabitacion']."<br>";
												}	
												
												$infoTooltipInsumoPaciente .= "Historia: ".$valuePaciente['historia']."-".$valuePaciente['ingreso']."<br>";
												$infoTooltipInsumoPaciente .= "Paciente: ".$valuePaciente['nombre']."<br>";
												$infoTooltipInsumoPaciente .= "Documento: ".$valuePaciente['tipoDocumento']." ".$valuePaciente['documento']."<br>";
												$infoTooltipInsumoPaciente .= "Insumo: ".$valueInsumo['codigo']." - ".$valueInsumo['nombreGenerico'];
												
												$tooltipInsPac = "<div id=\"dvTooltipInsPacRes\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipInsumoPaciente."</div>";
												
												$cadenaTooltipInsPac .= "tooltipInsPacRes_".$idCampo."|";
												
												
					
					// $html .= "					<td align='center' id='tooltipInsPacRes_".$idCampo."' title='".$tooltipInsPac."'><input type='text' id='cantRes_".$idCampo."' name='cantRes_".$idCampo."' class='".$fondoAmarilloInput."' style='cursor:default;' onfocus='seleccionarColumna(this);' habitacion='".$valuePaciente['habitacion']."' insumo='".$valueInsumo['codigo']."' onChange='validarCantidadInsumo(this);'  onkeypress='return soloNumeros(event);' size='1' value='".$valorInput."' readOnly='readOnly'></td>";	
					$html .= "					<td align='center' id='tooltipInsPacRes_".$idCampo."' title='".$tooltipInsPac."'><input type='text' id='cantRes_".$idCampo."' name='cantRes_".$idCampo."' class='".$fondoAmarilloInput."' style='cursor:default;' habitacion='".$valuePaciente['habitacion']."' insumo='".$valueInsumo['codigo']."' onChange='validarCantidadInsumo(this);'  onkeypress='return soloNumeros(event);' size='1' value='".$valorInput."' readOnly='readOnly'></td>";	
											}
											
				
										}
										
				$html .= "				<td align='center'><span id='cantTotalOrdenada_".$valueInsumo['codigo']."' name='cantTotalOrdenada_".$valueInsumo['codigo']."' style='font-weight:bold;'>".$cantOrdenada."</span></td>";								
									}
									
			
								}
								
			$html .= "				<input type='hidden' id='cadenaTooltipPacientesRes' name='cadenaTooltipPacientesRes' value='".$cadenaTooltipPacientes."'>";						
			$html .= "				<input type='hidden' id='cadenaTooltipInsPacRes' name='cadenaTooltipInsPacRes' value='".$cadenaTooltipInsPac."'>";						
			
			$html .= "		</table>
							<p align=center><span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span></p>
						</div>";
		}
		
		
		return $html;
	}
	
	function consultarConsecutivo($codigoCco)
	{
		global $conex;
		global $wbasedato;
		
		$queryCco =  " SELECT Ccocpi 
						 FROM ".$wbasedato."_000011 
						WHERE Ccocod='".$codigoCco."' 
						AND Ccoest='on';";
		
		$resCco = mysql_query($queryCco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCco . " - " . mysql_error());		   
		$numCco = mysql_num_rows($resCco);
		
		$cco = "";
		if($numCco>0)
		{
			$rowCco = mysql_fetch_array($resCco);
			
			$consecutivo = $rowCco['Ccocpi']+1;
			
			$cco = $codigoCco."-".$consecutivo;
		}
		
		actualizarConsecutivo($codigoCco,$consecutivo);
		
		return $cco;
	}
	
	function actualizarConsecutivo($codigoCco,$consecutivo)
	{
		global $conex;
		global $wbasedato;
		
		$queryUpdateCco =  " UPDATE ".$wbasedato."_000011 
								SET Ccocpi='".$consecutivo."'
							  WHERE Ccocod='".$codigoCco."' 
								AND Ccoest='on';";
		
		$resUpdateCco = mysql_query($queryUpdateCco,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdateCco." - ".mysql_error());
		
	}
	
	function registrarPedidoInsumosBotiquin($codBotiquin,$codigoCco,$wemp_pmla,$codPedido,$zona,$insumosPedido)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		
		$fecha = date("Y-m-d");
		$hora = date("H:i:s");
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
		
		if($codPedido=="")
		{
			$consecutivo = consultarConsecutivo($codigoCco);
			
			$queryEncabezado = " INSERT INTO ".$wbasedato."_000230 (		Medico	, Fecha_data , Hora_data ,		Pedcod	    ,		Pedbot	   ,	Pedcco		,	Pedaux	,Pedent,Pedest,Seguridad) 
															VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$consecutivo."','".$codBotiquin."','".$codigoCco."','".$wuse."','off', 'on' ,'C-".$wbasedato."');";
			
			$resInsertEncabezado = mysql_query($queryEncabezado,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryEncabezado." - ".mysql_error());
						
			if(mysql_affected_rows()>0)
			{
				foreach($insumosPedido as $keyInsumo => $valueInsumo)
				{
					if(($ccoTipo=="urgencias" && $zona=="0") || $ccoTipo=="ayudaDiagnostica")
					{
						$valueInsumo['habitacion'] = "";
					}
					
					$queryDetalle = "INSERT INTO ".$wbasedato."_000231 (		Medico	, Fecha_data , Hora_data ,		Dpecod	    ,				Dpehis		   ,			Dpeing			 ,				Dpehab			  ,				Dpeins			  ,				Dpeped		     ,Dpedis,Dpeest,				Dpepaq		 ,		Seguridad	) 
																VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$consecutivo."','".$valueInsumo['historia']."','".$valueInsumo['ingreso']."','".$valueInsumo['habitacion']."','".$valueInsumo['codInsumo']."','".$valueInsumo['cantidad']."',   0  , 'on' ,'".$valueInsumo['paquete']."','C-".$wbasedato."');";
					
					$resInsertDetalle = mysql_query($queryDetalle,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryDetalle." - ".mysql_error());
					
					if(mysql_affected_rows()>0)
					{
						$arrayResultado[$keyInsumo]['error']=0;
						$arrayResultado[$keyInsumo]['mensaje']="Insumo registrado correctamente";
					}
					else
					{
						$arrayResultado[$keyInsumo]['error']=1;
						$arrayResultado[$keyInsumo]['mensaje']="El insumo no se registró";
					}
				}
				
				$data['error']=0;
				$data['mensaje']=$arrayResultado;
				$data['codPedido']=$consecutivo;
			}
			else
			{
				$data['error']=1;
				$data['mensaje']="No se registró el pedido";
			}
		}
		else
		{
			
			// consultar pedido sin entregar
			$pedidoEntregado = consultarPedidoEntregado($codPedido,$codBotiquin,$codigoCco,$wuse);
			
			if($pedidoEntregado=="off")
			{
				$cantidadInsumosPedidas = consultarInsumosPedido($codPedido);
				
				foreach($insumosPedido as $keyInsumo => $valueInsumo)
				{
					// consultar si existe el registro del insumo en movhos_000231 actualizar la cantidad si tiene cantidad diferente, si no existe hacer el insert
					
					$idArray = $valueInsumo['codInsumo']."_".$valueInsumo['historia']."-".$valueInsumo['ingreso'];
					
					if(($ccoTipo=="urgencias" && $zona=="0") || $ccoTipo=="ayudaDiagnostica")
					{
						$valueInsumo['habitacion'] = "";
					}
					
					if(isset($cantidadInsumosPedidas[$idArray]))
					{
						// actualizar
						
						if($cantidadInsumosPedidas[$idArray]!=$valueInsumo['cantidad'])
						{
							$queryUpdateDetalle = "UPDATE ".$wbasedato."_000231
													  SET Dpeped='".$valueInsumo['cantidad']."'
													WHERE Dpecod='".$codPedido."'
													  AND Dpeins='".$valueInsumo['codInsumo']."'
													  AND Dpehis='".$valueInsumo['historia']."'
													  AND Dpeing='".$valueInsumo['ingreso']."'
													  AND Dpehab='".$valueInsumo['habitacion']."'
													  AND Dpeest='on';";
							
							$resUpdateDetalle = mysql_query($queryUpdateDetalle,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdateDetalle." - ".mysql_error());
							
							if(mysql_affected_rows()>0)
							{
								$arrayResultado[$keyInsumo]['error']=0;
								$arrayResultado[$keyInsumo]['mensaje']="Insumo registrado correctamente";
							}
							else
							{
								$arrayResultado[$keyInsumo]['error']=1;
								$arrayResultado[$keyInsumo]['mensaje']="El insumo no se registró1";
							}
						}
						else
						{
							$arrayResultado[$keyInsumo]['error']=0;
							$arrayResultado[$keyInsumo]['mensaje']="Insumo registrado correctamente";
						}
						
						
						// actualizar codigos paquetes
						actualizarPaquetesPedidos($codPedido,$valueInsumo);
						
						
						// se borra del array de cantidades pedidas lo que aun sigue en el pedido
						unset($cantidadInsumosPedidas[$idArray]);
					}
					else
					{
						// insertar
						$queryDetalle = "INSERT INTO ".$wbasedato."_000231 (		Medico	, Fecha_data , Hora_data ,		Dpecod	  ,				Dpehis		   ,			Dpeing			 ,				Dpehab			  ,				Dpeins			  ,				Dpeped		       ,Dpedis,Dpeest,				Dpepaq		   ,	Seguridad	  ) 
																	VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$codPedido."','".$valueInsumo['historia']."','".$valueInsumo['ingreso']."','".$valueInsumo['habitacion']."','".$valueInsumo['codInsumo']."','".$valueInsumo['cantidad']."',   0  , 'on' ,'".$valueInsumo['paquete']."','C-".$wbasedato."');";
						
						$resInsertDetalle = mysql_query($queryDetalle,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryDetalle." - ".mysql_error());
						
						if(mysql_affected_rows()>0)
						{
							$arrayResultado[$keyInsumo]['error']=0;
							$arrayResultado[$keyInsumo]['mensaje']="Insumo registrado correctamente";
						}
						else
						{
							$arrayResultado[$keyInsumo]['error']=1;
							$arrayResultado[$keyInsumo]['mensaje']="El insumo no se registró2";
						}
					}
				}
				
				// insumos que ya no estan en el pedido se deben poner en cero
				// si son  de la misma zona
				foreach($cantidadInsumosPedidas as $keyInsumoEliminado => $valueInsumoEliminado)
				{
					
					// var_dump($valueInsumoEliminado);
					if($valueInsumoEliminado!="0")
					{
						$inshising = explode("_",$keyInsumoEliminado);
					
						$insumo = $inshising[0];
						
						$hising = explode("-",$inshising[1]);
						
						$historia = $hising[0];
						$ingreso = $hising[1];
						
						
						
						$perteneceZona = true;
						if($zona!="")
						{
							// // validar si la habitacion pertenece a la zona
							$perteneceZona = consultarZonaHabitacion($historia,$ingreso,$zona);
						}
						
						
						if($perteneceZona)
						{
							$queryUpdateDetalle = "UPDATE ".$wbasedato."_000231
													  SET Dpeped='0'
													WHERE Dpecod='".$codPedido."'
													  AND Dpeins='".$insumo."'
													  AND Dpehis='".$historia."'
													  AND Dpeing='".$ingreso."'
													  AND Dpeest='on';";
							
							$resUpdateDetalle = mysql_query($queryUpdateDetalle,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdateDetalle." - ".mysql_error());
							
							if(mysql_affected_rows()>0)
							{
								$arrayResultado[$keyInsumoEliminado]['error']=0;
								$arrayResultado[$keyInsumoEliminado]['mensaje']="Insumo registrado correctamente";
							}
							else
							{
								$arrayResultado[$keyInsumoEliminado]['error']=1;
								$arrayResultado[$keyInsumoEliminado]['mensaje']="El insumo no se registró3";
							}
						}
					}
					// else
					// {
						// $arrayResultado[$keyInsumoEliminado]['error']=0;
						// $arrayResultado[$keyInsumoEliminado]['mensaje']="Insumo registrado correctamente";
					// }

				}
				
				$data['error']=0;
				$data['mensaje']=$arrayResultado;
			}
			elseif($pedidoEntregado=="on")
			{
				$data['error']=1;
				$data['mensaje']="El pedido fue entregado, no se pudo actualizar. Debe realizar un pedido nuevo.";
			}
			else
			{
				$data['error']=1;
				$data['mensaje']="El pedido esta anulado y no puede modificarlo. Debe realizar un pedido nuevo.";
			}
			
			setPedidoEditando( $conex, $wbasedato, $codPedido, 'off' );
		}
		
		
		
		
		return $data;
	}
	
	
	function anularPedidoInsumos($codBotiquin,$codigoCco,$codPedido)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		
		$queryUpdateDetalle = "UPDATE ".$wbasedato."_000230
								  SET Pedest='off'
								WHERE Pedcod='".$codPedido."'
								  AND Pedbot='".$codBotiquin."'
								  AND Pedcco='".$codigoCco."'
								  AND Pedaux='".$wuse."'
								  AND Pedent='off'
								  AND Pedest='on';";
		
		$resUpdateDetalle = mysql_query($queryUpdateDetalle,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdateDetalle." - ".mysql_error());
		
		if(mysql_affected_rows()>0)
		{
			$arrayResultado['error']=0;
			$arrayResultado['mensaje']="El pedido fue anulado.";
		}
		else
		{
			$arrayResultado['error']=1;
			$arrayResultado['mensaje']="No se pudo anular el pedido.";
		}
		
		return $arrayResultado;
	}
	
	function consultarZonaHabitacion($historia,$ingreso,$zona)
	{
		global $conex;
		global $wbasedato;
		
		$queryZona = " SELECT Habzon 
						 FROM ".$wbasedato."_000020 
						WHERE Habhis='".$historia."' 
						  AND Habing='".$ingreso."' 
						  AND Habzon='".$zona."'
						  AND Habest='on';";
		
		$resZona = mysql_query($queryZona, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryZona . " - " . mysql_error());		   
		$numZona = mysql_num_rows($resZona);
		
		$perteneceZona = false;
		if($numZona>0)
		{
			$perteneceZona = true;
		}
		
		return $perteneceZona;
	}
	
	function consultarUltimoPedido($codigoCco)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		
		$queryPedidoAnterior = " SELECT Pedcod 
								  FROM ".$wbasedato."_000230 
								 WHERE Pedcco='".$codigoCco."' 
								   AND Pedaux='".$wuse."' 
							  ORDER BY Fecha_data DESC,Hora_Data DESC,Pedcod DESC 
								 LIMIT 1;";
		
		$resPedidoAnterior = mysql_query($queryPedidoAnterior, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPedidoAnterior . " - " . mysql_error());		   
		$numPedidoAnterior = mysql_num_rows($resPedidoAnterior);
		
		$codPedidoAnterior = "";
		if($numPedidoAnterior>0)
		{
			$rowPedidoAnterior = mysql_fetch_array($resPedidoAnterior);
			$codPedidoAnterior = $rowPedidoAnterior['Pedcod'];
		}
		
		
		return $codPedidoAnterior;
	}
	
	function traerUltimoPedido($codPedidoAnterior)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		
		$arrayInsumosPedido = array();
		if($codPedidoAnterior != "")
		{
			$arrayInsumosPedido = consultarInsumosPedido($codPedidoAnterior);
		}
		
		return $arrayInsumosPedido;
	}
	
	function consultarSaldoActualAuxiliar($auxiliar,$codBotiquin)
	{
		global $conex;
		global $wbasedato;
		
		$querySaldoUrgencias = "SELECT SUM(Carcca*1-Carcap*1-Carcde*1) as Saldo
								  FROM ".$wbasedato."_000227
								 WHERE Caraux = '".$auxiliar."'
								   AND Cartra = 'on'
								   AND Carest = 'on';";
								
		$resSaldoUrgencias = mysql_query($querySaldoUrgencias, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $querySaldoUrgencias . " - " . mysql_error());		   
		$numSaldoUrgencias = mysql_num_rows($resSaldoUrgencias);
		
		$saldo = 0;
		if($numSaldoUrgencias>0)
		{
			$rowSaldoUrgencias = mysql_fetch_array($resSaldoUrgencias);
			
			$saldo = $rowSaldoUrgencias['Saldo'];
		}
		
		return $saldo;
	}
	
	function consultarCcoPedidoPorPaciente($codigoCco)
	{
		global $conex;
		global $wbasedato;
		
		$queryPedidoPorPaciente = " SELECT Ccopip 
									  FROM ".$wbasedato."_000011 
									 WHERE Ccocod='".$codigoCco."'
									   AND Ccopip='on';";
								
		$resPedidoPorPaciente = mysql_query($queryPedidoPorPaciente, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPedidoPorPaciente . " - " . mysql_error());		   
		$numPedidoPorPaciente = mysql_num_rows($resPedidoPorPaciente);
		
		$permitePedidoPorPaciente = false;
		if($numPedidoPorPaciente>0)
		{
			$permitePedidoPorPaciente = true;
		}
		
		return $permitePedidoPorPaciente;
	}
	
	function consultarPaquetes($codigoCco)
	{
		global $conex;
		global $wbasedato;
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
		
		$tipoServicio = "";
		if($ccoTipo=="hospitalario")
		{
			$tipoServicio = "H";
		}
		elseif($ccoTipo=="urgencias")
		{
			$tipoServicio = "U";
		}
		elseif($ccoTipo=="ayudaDiagnostica")
		{
			$tipoServicio = "A";
		}
		
		$queryPaquetes = "SELECT Paqcod,Paqdes 
							FROM ".$wbasedato."_000241,".$wbasedato."_000242 
						   WHERE Paqtis='".$tipoServicio."' 
							 AND Paqcco='".$codigoCco."'
							 AND Paqest='on'
							 AND Dpapaq=Paqcod
							 AND Dpaest='on'
						GROUP BY Paqcod;";
							 
		$resPaquetes = mysql_query($queryPaquetes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPaquetes . " - " . mysql_error());		   
		$numPaquetes = mysql_num_rows($resPaquetes);
		
		$paquetes = array();
		if($numPaquetes>0)
		{
			while($rowPaquetes = mysql_fetch_array($resPaquetes))
			{
				$paquetes[$rowPaquetes['Paqcod']] = $rowPaquetes['Paqdes'];
			}
		}
		else
		{
			$queryPaquetesGenerales = "SELECT Paqcod,Paqdes 
										 FROM ".$wbasedato."_000241,".$wbasedato."_000242
									    WHERE Paqtis='".$tipoServicio."' 
										  AND Paqcco='*'
										  AND Paqest='on'
										  AND Dpapaq=Paqcod
										  AND Dpaest='on'
									 GROUP BY Paqcod;";
								 
			$resPaquetesGenerales = mysql_query($queryPaquetesGenerales, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPaquetesGenerales . " - " . mysql_error());		   
			$numPaquetesGenerales = mysql_num_rows($resPaquetesGenerales);
			if($numPaquetesGenerales>0)
			{
				while($rowPaquetesGenerales = mysql_fetch_array($resPaquetesGenerales))
				{
					$paquetes[$rowPaquetesGenerales['Paqcod']] = $rowPaquetesGenerales['Paqdes'];
				}
				
			}
		}
		
		$arrayPaquetes = array();
		$arrayPaquetes['tipoServicio'] = $tipoServicio;
		$arrayPaquetes['paquetes'] = $paquetes;
		
		return $arrayPaquetes;
	}
	
	function pintarPaquetes($wemp_pmla,$codigoCco)
	{
		global $conex;
		global $wbasedato;
		
		$arrayPaquetes = consultarPaquetes($codigoCco);
		$tipoServicio = $arrayPaquetes['tipoServicio'];
		$paquetes = $arrayPaquetes['paquetes'];
		
		$html = "<div id='dvPaquetes'>
					<input type='hidden' id='tipoServicio' name='tipoServicio' value='".$tipoServicio."'>";
		
		if(count($paquetes)>0)
		{
			$html .= "<table width='20%' align='center'>
						<tr class='encabezadoTabla' align='center'>
							<td>PAQUETES</td>
						</tr>
						<tr class='fila1' align='center'>
							<td>
								<select id='codigoPaquete''>
									<option value=''>Seleccione...</option>";
									foreach($paquetes as $codPaquete => $descPaquete)
									{
		$html .= "						<option value='".$codPaquete."'>".$descPaquete."</option>";								
									}
		$html .= "				</select>
							</td>
						</tr>
					  </table>";
		}
		else
		{
			$html .= "<p><b>No hay paquetes configurados.</b></p>";
		}
		
		$html .= "</div>";
		return $html;
	}
	
	function consultarInsumosPaquete($wemp_pmla,$codigoPaquete)
	{
		global $conex;
		global $wbasedato;
		
		$queryDetallePaquete = "SELECT Dpains,Dpacan 
								  FROM ".$wbasedato."_000242 
								 WHERE Dpapaq='".$codigoPaquete."' 
								   AND Dpaest='on';";
							 
		$resDetallePaquete = mysql_query($queryDetallePaquete, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDetallePaquete . " - " . mysql_error());		   
		$numDetallePaquete = mysql_num_rows($resDetallePaquete);
		
		$insumosPaquete = array();
		if($numDetallePaquete>0)
		{
			while($rowDetallePaquete = mysql_fetch_array($resDetallePaquete))
			{
				$insumosPaquete[$rowDetallePaquete['Dpains']] = $rowDetallePaquete['Dpacan'];
			}
		}
		return $insumosPaquete;
	}
		
	function consultarArrayPaquetesPedidoActual($wemp_pmla,$codPedido)
	{
		global $conex;
		global $wbasedato;
		
		$queryPaquetesPedido = "SELECT Dpehis,Dpeing,Dpepaq 
								  FROM ".$wbasedato."_000231 
								 WHERE Dpecod='".$codPedido."' 
								   AND Dpeest='on' 
								   AND Dpeped!='0' 
								   AND Dpepaq!='' 
							  GROUP BY Dpehis,Dpeing,Dpepaq;";
							 
		$resPaquetesPedido = mysql_query($queryPaquetesPedido, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPaquetesPedido . " - " . mysql_error());		   
		$numPaquetesPedido = mysql_num_rows($resPaquetesPedido);
		
		$arrayPaquetes = array();
		if($numPaquetesPedido>0)
		{
			while($rowPaquetesPedido = mysql_fetch_array($resPaquetesPedido))
			{
				$paquetes = explode(",",$rowPaquetesPedido['Dpepaq']);
				
				for($i=0;$i<count($paquetes);$i++)
				{
					$arrayPaquetes[$paquetes[$i]]['insumos'] = consultarInsumosPaquete($wemp_pmla,$paquetes[$i]);
					$arrayPaquetes[$paquetes[$i]]['pacientes'][$rowPaquetesPedido['Dpehis']."-".$rowPaquetesPedido['Dpeing']] = $rowPaquetesPedido['Dpehis']."-".$rowPaquetesPedido['Dpeing'];
				}
			}
		}
		return $arrayPaquetes;
	}
	
	function actualizarPaquetesPedidos($codPedido,$valueInsumo)
	{
		global $conex;
		global $wbasedato;
		
		if($valueInsumo['paquete']!="")
		{
			$queryCodigosPaquetes = " SELECT Dpepaq 
										FROM ".$wbasedato."_000231 
									   WHERE Dpecod='".$codPedido."' 
										 AND Dpehis='".$valueInsumo['historia']."' 
										 AND Dpeing='".$valueInsumo['ingreso']."' 
										 AND Dpeins='".$valueInsumo['codInsumo']."'
										 AND Dpeest='on';";
			
			$resCodigosPaquetes = mysql_query($queryCodigosPaquetes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCodigosPaquetes . " - " . mysql_error());		   
			$numCodigosPaquetes = mysql_num_rows($resCodigosPaquetes);
			
			$codigosPaquetes = "";
			if($numCodigosPaquetes>0)
			{
				$rowCodigosPaquetes = mysql_fetch_array($resCodigosPaquetes);
				
				$codigosPaquetes = $rowCodigosPaquetes['Dpepaq'];
			}
			
			$paquetesActuales = explode(",",$codigosPaquetes);
			$paquetesNuevos = explode(",",$valueInsumo['paquete']);
			
			$nuevosCodigosPaquetes = $codigosPaquetes;
			for($i=0;$i<count($paquetesNuevos);$i++)
			{
				if(!in_array($paquetesNuevos[$i],$paquetesActuales))
				{
					if($codigosPaquetes!="")
					{
						$nuevosCodigosPaquetes = $codigosPaquetes.",".$paquetesNuevos[$i];
					}
					else
					{
						$nuevosCodigosPaquetes = $paquetesNuevos[$i];
					}
				}
			}
			
			if($nuevosCodigosPaquetes!=$codigosPaquetes)
			{
				$queryUpdatePaquetes = "UPDATE ".$wbasedato."_000231
										   SET Dpepaq='".$nuevosCodigosPaquetes."'
										 WHERE Dpecod='".$codPedido."' 
										   AND Dpehis='".$valueInsumo['historia']."' 
										   AND Dpeing='".$valueInsumo['ingreso']."' 
										   AND Dpeins='".$valueInsumo['codInsumo']."'
										   AND Dpeest='on';";
				
				$resUpdatePaquetes = mysql_query($queryUpdatePaquetes,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdatePaquetes." - ".mysql_error());
			
			}
		}
	}
	
	function consultarInsumosNoAgregados($wemp_pmla,$codBotiquin,$insumosNoAgregados)
	{
		global $conex;
		global $wbasedato;
		
		$mensaje = "";
		if(count($insumosNoAgregados)>0)
		{
			foreach($insumosNoAgregados as $keyInsumos => $valueInsumo)
			{
				$msj = validarInsumosBotiquin($valueInsumo,$codBotiquin);
				
				$mensaje .= $msj;
			}
		}
		
		
		return $mensaje;
	}
	
	function validarInsumosBotiquin($codigoArticulo, $codigoBotiquin )
	{
		
		global $conex;
		global $wbasedato;
		
		$queryBotiquin = "SELECT Cconom 
							FROM ".$wbasedato."_000011 
						   WHERE Ccocod='".$codigoBotiquin."' 
						     AND Ccoest='on';";
							 
		$resBotiquin = mysql_query($queryBotiquin, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryBotiquin . " - " . mysql_error());		   
		$numBotiquin = mysql_num_rows($resBotiquin);
		
		$descripcionBotiquin="";
		if($numBotiquin>0)
		{
			$rowBotiquin = mysql_fetch_array($resBotiquin);
			
			$descripcionBotiquin = $rowBotiquin['Cconom'];
		}			
		
		$queryArticulo = " SELECT Artcom,Artest,Artgru,Melgru
							 FROM ".$wbasedato."_000026
						LEFT JOIN ".$wbasedato."_000066
							   ON Melgru = SUBSTRING_INDEX( Artgru, '-', 1 )
							   AND Meltip = 'M'
							   AND Melest = 'on'
							WHERE Artcod='".$codigoArticulo."';";
		
		$resArticulo = mysql_query($queryArticulo, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryArticulo . " - " . mysql_error());		   
		$numArticulo = mysql_num_rows($resArticulo);
		
		$mensaje="";
		if($numArticulo>0)
		{
			$rowArticulo = mysql_fetch_array($resArticulo);
			
			if($rowArticulo['Artest']=="on")
			{
				if( (empty( $rowArticulo['Melgru'] ) || $rowArticulo['Melgru'] == 'E00' ) && !empty($rowArticulo['Artcom']) )
				{
					$querySaldoInsumo = "  SELECT Salant, Salent, Salsal 
											 FROM ".$wbasedato."_000141
											WHERE Salser = '".$codigoBotiquin."' 
											  AND Salart = '".strtoupper($codigoArticulo)."';";
											  
					$resSaldoInsumo = mysql_query($querySaldoInsumo, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $querySaldoInsumo . " - " . mysql_error());		   
					$numSaldoInsumo = mysql_num_rows($resSaldoInsumo);
					
					if($numSaldoInsumo>0)
					{
						$rowSaldoInsumo = mysql_fetch_array($resSaldoInsumo);
						
						$saldo = $rowSaldoInsumo['Salant']+$rowSaldoInsumo['Salent']-$rowSaldoInsumo['Salsal'];
						$saldo -= consultarSaldoEnTransito( $conex, $wbasedato, $codigoArticulo, $codigoBotiquin );
						
						
						if($saldo<=0)
						{
							$mensaje = "- El insumo <b>".$codigoArticulo." - ".$rowArticulo['Artcom']."</b> no tiene saldo en el botiqu&iacute;n <b>(".$codigoBotiquin." - ".$descripcionBotiquin.")</b>.\n";
						}
					}
					else
					{
						$mensaje = "- El insumo: <b>".$codigoArticulo." - ".$rowArticulo['Artcom']."</b> no está configurado en el botiqu&iacute;n: <b>(".$codigoBotiquin." - ".$descripcionBotiquin.")</b>.\n";
					}
					
					
				}
				else
				{
					$mensaje = "- El artículo <b>".$codigoArticulo." - ".$rowArticulo['Artcom']."</b> no está configurado como material médico quirúrgico. \n";
				}
				
			}
			else
			{
				$mensaje = "- El artículo <b>".$codigoArticulo." - ".$rowArticulo['Artcom']."</b> está inactivo en el maestro de artículos (movhos_000026). \n";
			}
			
		}
		else
		{
			$mensaje = "- El artículo <b>".$codigoArticulo."</b> no existe en el maestro de artículos (movhos_000026). \n";
		}
		
		return $mensaje;
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
		case 'pintarInsumosBotiquin':
		{	
			$data = pintarInsumosBotiquin($codBotiquin,$codigoCco,$zona,$pedidosPorCco);
			// $data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'registrarPedidoInsumos':
		{
			$data = registrarPedidoInsumosBotiquin($codBotiquin,$codigoCco,$wemp_pmla,$codPedido,$zona,$insumosPedido);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarResumenPedidoInsumos':
		{	
			$data = pintarResumenInsumosBotiquin($codBotiquin,$codigoCco,$codPedido,$insumosPedido,$zona);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'anularPedido':
		{	
			$data = anularPedidoInsumos($codBotiquin,$codigoCco,$codPedido);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarZona':
		{	
			$data = pintarZona($codigoCco);
			echo json_encode($data);
			break;
			return;
		}
		case 'traerUltimoPedido':
		{	
			$data = traerUltimoPedido($codUltimoPedido);
			echo json_encode($data);
			break;
			return;
		}
		case 'cambiarEstadoEdicion':
		{
			setPedidoEditando( $conex, $wbasedato, $codPedido, $estado );
		}
		case 'pintarListaPaquetes':
		{
			$data = pintarPaquetes($wemp_pmla,$codigoCco);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarInsumosPaquete':
		{
			$data = consultarInsumosPaquete($wemp_pmla,$codigoPaquete);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarPaquetesPedidoActual':
		{
			$data = consultarArrayPaquetesPedidoActual($wemp_pmla,$codPedido);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarInsumosNoAgregados':
		{
			$data = consultarInsumosNoAgregados($wemp_pmla,$codBotiquin,$insNoAgregados);
			echo json_encode($data);
			break;
			return;
		}
		
		default: break;
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
	?>
	<html>
	<head>
	  <title>PEDIDO DE INSUMOS</title>
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
		
		 <script type='text/javascript' src='../../../include/root/jquery.stickytableheaders.js'></script> 

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	paquetesAgregados = {};
	
	function marcarDesmarcarSeleccionarTodos(){
		if(    $( "#divPacientesDomiciliarios > table input.hisDomiciliarias:visible" ).length > 0 
			&& $( "#divPacientesDomiciliarios > table input.hisDomiciliarias:visible" ).length == $( "#divPacientesDomiciliarios > table input.hisDomiciliarias:visible" ).filter(":checked").length
		){
			$( "#inMarcarTodos" )[0].checked = true;
			console.log("true")
		}
		else{
			$( "#inMarcarTodos" )[0].checked = false;
			console.log("false")
		}
	}
	
	function buscadorInformacionPaciente(){
		
		//Agregando la tabla al div que muestra los pacientes
		$( "<div style='display:block;margin: 0 auto; width:70%;padding: 20px;'><span style='font-weight:bold;'>Buscador:</span><input id='buscarInformacion'></div>" ).prependTo( $( "#divPacientesDomiciliarios" ) );
		
		$('#buscarInformacion').quicksearch('#divPacientesDomiciliarios .find', { 
				onAfter : function(){ 
							marcarDesmarcarSeleccionarTodos();
						},
			});
	}
	
	/**
	 *
	 */
	function consutlarPacientesDomiciliarios(){
	
		$( "#divPacientesDomiciliarios" ).html('');
		$( "#divInsumosBotiquin" ).html('');
		
		var valZona = $( "#filtroZonas" ).val();
		// var valZona = $( "#filtroZonas" ).filter(":visible").length > 0 ? $( "#filtroZonas" ).val() : '';
		console.log( valZona );
		
		if( valZona != '' && valZona != undefined ){
			
			if( valZona == '0' ){
				valZona = '';
			}
			
			$.post("gestionEnfermeria.php",
				{
					consultaAjax 	: '',
					operacion		: 'consutlarPacientesDomiciliarios',
					wemp_pmla		: $('#wemp_pmla').val(),
					wcco			: $( "#filtroCcoDispensacion" ).val(),
					wzona			: valZona,
				},
				function(data) {
					
					if( data.length > 0 ){
						
						var stHTML =   "<table style='margin: 0 auto;width:70%;padding: 0 0 20px 0;'>"
									 + 		"<tr class='encabezadoTabla'>"
									 + 			"<td>Historia</td>"
									 + 			"<td>Identifacaci&oacute;n</td>"
									 + 			"<td>Nombre del Paciente</td>"
									 + 			"<td>Barrio</td>"
									 + 			"<td>Direcci&oacute;n</td>"
									 + 			"<td>Ver pacientes<input type='checkbox' id='inMarcarTodos'></td>"
									 + 		"</tr>"
									 + "</table>";
									 
						var tbtable = $( stHTML );
						
						$( data ).each(function(index){
							
							var _class = "fila1";
							if(index %2 == 0){
								_class = "fila2";	
							}
							
							stHTML = "<tr class='"+_class+" find'>"
								   + "<td>"+this.historia+"-"+this.ingreso+"</td>"
								   + "<td>"+this.tipoDocumento+" "+this.numeroDocumento+"</td>"
								   + "<td>"+this.nombreCompleto+"</td>"
								   + "<td>"+this.barrio+"</td>"
								   + "<td>"+this.direccion+"</td>"
								   + "<td style='text-align:center;'><input type='checkbox' class='hisDomiciliarias' name='historiasDomiciliarias[]' value='"+this.historia+"'></td>"
								   + "</tr>";
								   
							var tr = $( stHTML );
							
							$( stHTML ).appendTo( $( "tbody", tbtable ) );
						});
						
						$( tbtable ).appendTo( $( "#divPacientesDomiciliarios" ) );
						
						$( "<div style='text-align:center;'><input id='inComenzarPedido' type='button' value='Comenzar a realizar pedido'></div>" ).appendTo( $( "#divPacientesDomiciliarios" ) );
						
						// if( $("#filtroZonas").val() == '' ){
							// $("#filtroZonas").val( '0' );
						// }
						
						buscadorInformacionPaciente();
					}
				}, "json"
			);
		}
	}
	
	function cambiarEstadoEdicionPedido( estado ){
		
		if( $('#codPedido').val() != '' ){
		
			$.ajax({
				url		: "pedidoInsumosAuxiliares.php",
				type	: "POST",
				async	: false,
				data	: {
					consultaAjax 	: '',
					accion			: 'cambiarEstadoEdicion',
					codPedido		: $('#codPedido').val(),
					estado			: estado,
					wemp_pmla		: $('#wemp_pmla').val(),
				},
			});
		}
	}
	
	window.onbeforeunload = function(){
		cambiarEstadoEdicionPedido( 'off' );
	}
	
	function actualizarSaldosBotiquin(codBotiquin)
	{
		
		if( codBotiquin != '' ){
			
			//Actualizando saldos
			$.ajax({
				url		: "./proceso_saldosUnix.php",
				type	: "POST",
				async	: true,
				data: {
					cco: codBotiquin
				},
			});
		}
	}
	
	function validarZona()
	{
		if($('#filtroCcoDispensacion').val()!="")
		{
			cambiarEstadoEdicionPedido( 'off' );
		
			$("#trZona").hide();
			$("#tdFiltroZona").html("");
			
			$("#divInsumosBotiquin").html("");
			
			
			$.post("pedidoInsumosAuxiliares.php",
			{
				consultaAjax 	: '',
				accion			: 'pintarZona',
				codigoCco		: $('#filtroCcoDispensacion').val(),
				wemp_pmla		: $('#wemp_pmla').val()
			}
			, function(data) {
				
				if(data=="")
				{
					seleccionarCco(false);
				}
				else
				{
					$("#tdFiltroZona").html(data);
					$("#trZona").show();
				}
				
			},'json');
		}
		
	}
	
	function seleccionarCco(anulado)
	{
		var campoConCantidadZona = false;
		
		var zona = "";
		if($("#filtroZonas").val()=="")
		{
			jAlert("Debe seleccionar una zona","ALERTA");
			return;
		}
		else if($("#filtroZonas").val()!==undefined)
		{
			zona = $("#filtroZonas").val();
			
			
			// if($("#pedidoGuardado").val()=="off")
			if($("#pedidoGuardado").val()=="off" && !anulado)
			{
				$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
					if($(this).val()!="" && $(this).val()!="0")
					{
						campoConCantidadZona = true;
						return;
					}
				});
			}
		}
		
		
		if(campoConCantidadZona)
		{
			jConfirm( "Si cambia de zona sin guardar el pedido se perderan los cambios, desea continuar?", "ALERTA", function(resp){
				
				if( resp ){
					pintarInsumosBotiquin(zona);				
					
				}
				else
				{
					$("#filtroZonas").val($("#filtroZonas").attr("zonavalida"));
				}
			});
		}
		else
		{
			pintarInsumosBotiquin(zona);
		}
		
		
	}
	
	function pintarInsumosBotiquin(zona)
	{
		var pacientesDomiciliarios = [];
		$( ".hisDomiciliarias:checked" ).each(function(){
			pacientesDomiciliarios.push( this.value );
		})
		
		$("#divInsumosBotiquin").html("");
		$("#msjEspere").show();
		$( ".botonPedidos" ).prop('disabled', false);
		$(".botonesPedido").hide();
		
		actualizarSaldosBotiquin($("#filtroCcoDispensacion option:selected").attr("botiquin"));
		
		$("#filtroZonas").attr("zonaValida",zona);
		
		if($('#filtroCcoDispensacion').val()!="")
		{
			$.post("pedidoInsumosAuxiliares.php",
			{
				consultaAjax 	: '',
				accion			: 'pintarInsumosBotiquin',
				codBotiquin		: $("#filtroCcoDispensacion option:selected").attr("botiquin"),
				codigoCco		: $('#filtroCcoDispensacion').val(),
				zona			: zona,
				wemp_pmla		: $('#wemp_pmla').val(),
				pedidosPorCco	: $('#permitePedidosPorCco').val(),
				pacDomciliarios : pacientesDomiciliarios,
			}
			, function(data) {
				$("#msjEspere").hide();
				$("#divInsumosBotiquin").html(data.html);
				
				if(data.error==0)
				{
					$(".botonesPedido").show();
					
					if($("#codPedido").val()=="")
					{
						$("#anularpedido").hide();
					}
					else
					{
						$("#anularpedido").show();
						
						
						// ----------------------------------
						var campoConCantidad = "";
						$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
							if($(this).val()!="" && $(this).val()!="0")
							{
								campoConCantidad = $(this);
								return;
							}
						});
						
						
						// -----------------------------------
						
						
						deshabilitarPacientesPedido(campoConCantidad);
						
						consultarArrayPaquetes();
						
						
					}
					
				
					inicializarBuscador();
					
					
					// incializar tooltip
					
					$( ".frecuente").tooltip();
					
					//Tooltip
					var cadenaTooltipPacientes = $("#cadenaTooltipPacientes").val();
					
					cadenaTooltipPacientes = cadenaTooltipPacientes.split("|");
					
					for(var i = 0; i < cadenaTooltipPacientes.length-1;i++)
					{
						$( "#"+cadenaTooltipPacientes[i] ).tooltip();
					}
					
					
					
					// incializar tooltip
					
					//Tooltip
					var cadenaTooltipInsPac = $("#cadenaTooltipInsPac").val();
					
					cadenaTooltipInsPac = cadenaTooltipInsPac.split("|");
					
					for(var i = 0; i < cadenaTooltipInsPac.length-1;i++)
					{
						// $( "#"+cadenaTooltipInsPac[i] ).tooltip();
					}
					
					if($("#permitePedidosPorPaciente").val())
					{
						$("#btnTraerUltimoPedido").hide();
					}
					else
					{
						$("#btnTraerUltimoPedido").show();
					}
				}
				
				
				
			},'json').done(function(){
			
				deshabilitarPacientesEnConsulta();
            });
		}
		else
		{
			jAlert("Debe seleccionar el centro de costos a dispensar","ALERTA");
		}
	}
	
	function inicializarBuscador()
	{
		$('#buscarInsumo').quicksearch('#tableInsumosBotiquin .find');
	}
	
	
	function marcarColumna(habitacion)
	{
		// si el pedido es para urgencias y sin ubicacion el atributo habitacion será la historia e ingreso del paciente
		$( "#tableInsumosBotiquin td" ).removeClass('fondoamarillo');
		
		$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
			
			if($(this).attr("habitacion")==habitacion)
			{
				$(this).parent().addClass('fondoamarillo');
			}
		});
	}
	
	function seleccionarColumna(input)
	{
		mostrarTooltip(input);
		actualizarCantidadValida(input);
		
		$( "#tableInsumosBotiquin td" ).removeClass('fondoamarillo');
		
		// si el pedido es para urgencias y sin ubicacion el atributo habitacion será la historia e ingreso del paciente
		habitacionCampo = $(input).attr("habitacion");
		idCampo = $(input).attr("id");
		
		id = idCampo.replace("cant_","");
		$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
			
			if($(this).attr("habitacion")==habitacionCampo)
			{
				$(this).parent().addClass('fondoamarillo');
			}
		});
	}
	
	function actualizarCantidadValida(input)
	{
		cantidadValida = $(input).val();
		
		// mostrar tooltip
		$(input).tooltip("open");
		
		if(cantidadValida!="")
		{
			$(input).attr("cantidadValida",cantidadValida);
		
			$(input).select();
		}
	}
	
	
	function validarCantidadInsumo(input)
	{
		
		$(input).addClass('fondoamarillo');
		
		codInsumo = $(input).attr("insumo");
		
		var saldoInsumo = $("#saldoInsumo_"+codInsumo).html();
		saldoInsumo = parseInt(saldoInsumo);
		
		cantidadInsumo = $(input).val();
		cantidadInsumo = parseInt(cantidadInsumo);
		// console.log(cantidadInsumo);
		
		if(isNaN(cantidadInsumo))
		{
			$(input).removeClass('fondoamarillo');
			$(input).attr("cantidadValida","");
		}
		else
		{
			if(cantidadInsumo<=100000)
			{
				var cantTotalOrdenada = 0;
				
				$('table[id=tableInsumosBotiquin] input[id^=cant_'+codInsumo+']').each(function(){
					
					var cantInsumo = $(this).val();
					var cant = parseInt(cantInsumo);
					
					if(!isNaN(cant))
					{
						cantTotalOrdenada = cantTotalOrdenada + cant;
					}
					
				});
			
				
				
				$("#cantTotalOrdenada_"+codInsumo).html(cantTotalOrdenada);
			
				$(input).attr("cantidadValida",cantidadInsumo);
				
				$(input).select();
			}
			else
			{
				jAlert("No puede pedir m&aacute;s de 100000","ALERTA");
				
				var cantidadValida = $(input).attr("cantidadValida");
				cantidadValida = parseInt(cantidadValida);
		
		
				if(isNaN(cantidadValida))
				{
					$(input).removeClass('fondoamarillo');
					$(input).val("");
				}
				else
				{
					$(input).val(cantidadValida);
				}
				
				
			}
		}
		
		
		deshabilitarPacientesPedido(input);
	}
	
	function mostrarTooltip(input)
	{
		ocultarTooltip(input);
		var tdInput = $(input).parent();
		
		$(tdInput).append("<div id='divTooltipManual' class='tooltipManual' style='position:absolute; top: 5px;left:80px;z-index: 1;border: solid 1px #7D7D7D;background-color:#E3E3E3;width:350px;opacity: 0.85;padding:0.5em;'>"+$(tdInput).attr("infoTooltipManual")+"</div>");
		
		
	}
	
	function ocultarTooltip(input)
	{ 
		$(".tooltipManual").remove();
	}
	
	function cambioInput(e,input)
	{
		var key = window.Event ? e.which : e.keyCode;
		
		// si es flecha derecha o tab
		// if(e.keyCode == 39 || e.keyCode == 9
		if((e.keyCode == 39 || e.keyCode == 9) && $(input).parent().next().children().prop("disabled")==false)
		{
			$(input).parent().next().children().focus();
			mostrarTooltip($(input).parent().next().children());
			return false;
		}
		
		// si es flecha izquierda
		// if(e.keyCode == 37)
		if(e.keyCode == 37 && $(input).parent().prev().children().prop("disabled")==false)
		{
			$(input).parent().prev().children().focus();
			mostrarTooltip($(input).parent().prev().children());
			return false;
		}
		
		// si es flecha abajo o enter
		if(e.keyCode == 40 || e.keyCode == 13)
		{
			indice = $(input).parent().index();
			
			$(input).parent().parent().next().children().eq(indice).children().focus()
			mostrarTooltip($(input).parent().parent().next().children().eq(indice).children());
			return false;
		}
		
		// si es flecha arriba
		if(e.keyCode == 38)
		{
			indice = $(input).parent().index();
			
			$(input).parent().parent().prev().children().eq(indice).children().focus()
			mostrarTooltip($(input).parent().parent().prev().children().eq(indice).children());
			return false;
		}
		
	}
	
	
	function soloNumeros(e,input)
	{
		var key = window.Event ? e.which : e.keyCode;
		
		// validacion solo numeros
		return ((key >= 48 && key <= 57) || key<= 8);
	}
	
	function llenarArrayInsumos()
	{
		var insumosPedido = {}; 
		
		var tieneInsumos = false;
		
		$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
		  
			if($(this).val()!=="" && $(this).val()!=="0")
			{
				// console.log($(this).val()+": "+$(this).attr('id'))
				var id = $(this).attr("id");
				idInput = id.replace("cant_","");
				
				idCampo = idInput.split("_");
				hising = idCampo[1].split("-");
				
				// si el pedido es para urgencias y sin ubicacion el atributo habitacion será la historia e ingreso del paciente
				habitacion = $(this).attr('habitacion');
				
				if( !insumosPedido[idInput] )
				{
					insumosPedido[idInput] = [];
					insumosPedido[idInput] = {
						codInsumo: idCampo[0],
						historia: hising[0],
						ingreso: hising[1],
						habitacion: habitacion,
						cantidad: $(this).val(),
						paquete: validarPaquete(hising[0],hising[1],idCampo[0])
					}
					
					tieneInsumos=true;
				}
				
			}
			
			
		});
		// console.log(insumosPedido);
		return insumosPedido;
	}
	
	
	function pedirInsumos()
	{
		$( ".botonPedidos" ).prop('disabled', true);
		
		insumosPedido = llenarArrayInsumos();
		
		var zona = "";
		if($("#filtroZonas").val()=="")
		{
			jAlert("Debe seleccionar una zona","ALERTA");
			return;
		}
		else if($("#filtroZonas").val()!==undefined)
		{
			zona = $("#filtroZonas").val();
		}
		
		var pedidoConInsumos = false;
		for(var i in insumosPedido) { 
			pedidoConInsumos = true;
		}
		
		if(pedidoConInsumos)
		{
			$.post("pedidoInsumosAuxiliares.php",
			{
				consultaAjax 	: '',
				accion			: 'registrarPedidoInsumos',
				codBotiquin		: $("#filtroCcoDispensacion option:selected").attr("botiquin"),
				codigoCco		: $('#filtroCcoDispensacion').val(),
				wemp_pmla		: $('#wemp_pmla').val(),
				codPedido		: $('#codPedido').val(),
				zona			: zona,
				insumosPedido	: insumosPedido
			}
			, function(data) {
				
				$("input[id^=cant_]").prop('disabled', true);
				$("#divConvensionesPedidoGrabado").show();
				
				if(data.error=="1")
				{
					jAlert(data.mensaje,"ALERTA");
					seleccionarCco(false);
					$( ".botonPedidos" ).prop('disabled', false);
				}
				else
				{
					$("#codPedido").val(data.codPedido);
					$("#anularpedido").show();
					
					$( "#tableInsumosBotiquin td" ).removeClass('fondoamarillo');
					$( ".botonPedidos" ).prop('disabled', true);
					
					arrayResultado = data.mensaje;
					
					for(resultado in arrayResultado)
					{
						
						if(arrayResultado[resultado].error==0)
						{
							$("#cant_"+resultado).addClass("fondoverde");
						}
						else
						{
							$("#cant_"+resultado).addClass("fondorojo");
						}
					}
					
					jAlert("El pedido fue registrado exitosamente.","ALERTA");
				}
				
				
				$("#pedidoGuardado").val("on");
				
				
			},'json');
		}
		else
		{
			jAlert("No ha ingresado las cantidades de insumos a pedir por paciente.","ALERTA");
			$( ".botonPedidos" ).prop('disabled', false);
		}
		
	}
	
	function verResumenPedido()
	{
		
		var zona = "";
		if($("#filtroZonas").val()=="")
		{
			jAlert("Debe seleccionar una zona","ALERTA");
			return;
		}
		else if($("#filtroZonas").val()!==undefined)
		{
			zona = $("#filtroZonas").val();
		}
		
		insumosPedido = llenarArrayInsumos();
		
		var pedidoConInsumos = false;
		for(var i in insumosPedido) { 
			pedidoConInsumos = true;
		}
		
		if(pedidoConInsumos)
		{
			$.post("pedidoInsumosAuxiliares.php",
			{
				consultaAjax 	: '',
				accion			: 'pintarResumenPedidoInsumos',
				wemp_pmla		: $('#wemp_pmla').val(),
				codBotiquin		: $("#filtroCcoDispensacion option:selected").attr("botiquin"),
				codigoCco		: $('#filtroCcoDispensacion').val(),
				codPedido		: $('#codPedido').val(),
				insumosPedido	: insumosPedido,
				zona			: zona
			}
			, function(data) {
				
				$( "#dvAuxModalResumen" ).html( data );
				var canWidth = $(window).width()*0.8;
				if( $( "#dvAuxModalResumen" ).width()-50 < canWidth )
					canWidth = $( "#dvAuxModalResumen" ).width();

				var canHeight = $(window).height()*0.8;;
				if( $( "#dvAuxModalResumen" ).height()-50 < canHeight )
					canHeight = $( "#dvAuxModalResumen" ).height();
				
				$.blockUI({ message: $('#divResumenInsumosPedidos'),
				css: {
					overflow: 'auto',
					cursor	: 'auto',
					width	: "80%",
					height	: "80%",
					left	: "10%",
					top		: '100px',
				} });
				
				
				$('#buscarInsumoResumen').quicksearch('#tableInsumosBotiquinResumen .find');
				
				
				
				
				// incializar tooltip
			
				//Tooltip
				var cadenaTooltipPacientesRes = $("#cadenaTooltipPacientesRes").val();
				
				cadenaTooltipPacientesRes = cadenaTooltipPacientesRes.split("|");
				
				for(var i = 0; i < cadenaTooltipPacientesRes.length-1;i++)
				{
					$( "#"+cadenaTooltipPacientesRes[i] ).tooltip();
				}
				
				
				
				// incializar tooltip
				
				//Tooltip
				var cadenaTooltipInsPacRes = $("#cadenaTooltipInsPacRes").val();
				
				cadenaTooltipInsPacRes = cadenaTooltipInsPacRes.split("|");
				
				for(var i = 0; i < cadenaTooltipInsPacRes.length-1;i++)
				{
					$( "#"+cadenaTooltipInsPacRes[i] ).tooltip();
				}
				
			},'json');
		}
		else
		{
			jAlert("No ha ingresado las cantidades de insumos a pedir por paciente.","ALERTA");
		}
	}
	
	function anularpedido()
	{
		var codPedido = $("#codPedido").val();
		jConfirm( "Desea anular el pedido: "+codPedido+"?", "ALERTA", function(resp){
			if( resp ){
								
				$.post("pedidoInsumosAuxiliares.php",
				{
					consultaAjax 	: '',
					accion			: 'anularPedido',
					wemp_pmla		: $('#wemp_pmla').val(),
					codBotiquin		: $("#filtroCcoDispensacion option:selected").attr("botiquin"),
					codigoCco		: $('#filtroCcoDispensacion').val(),
					codPedido		: codPedido
				}
				, function(data) {
					
					jAlert(data.mensaje,"ALERTA");
					seleccionarCco(true);
					
					paquetesAgregados = {};
					
				},'json');
				
				
				// marcar pedido guardado en on
			}
		});
	}
	
	function traerUltimoPedido()
	{
		var codUltimoPedido = $("#codigoUltimoPedido").val();
		jConfirm( "Desea cargar el pedido anterior? Se limpiar&aacute;n las cantidades actuales", "ALERTA", function(resp){
			if( resp ){
				
				limpiarCantidadesPedido();
				
				$.post("pedidoInsumosAuxiliares.php",
				{
					consultaAjax 	: '',
					accion			: 'traerUltimoPedido',
					wemp_pmla		: $('#wemp_pmla').val(),
					codUltimoPedido	: codUltimoPedido
				}
				, function(data) {
					
					for(campo in data)
					{
						if(data[campo]!="0")
						{
							$("#cant_"+campo).val(data[campo]);
							
							validarCantidadInsumo($("#cant_"+campo));
							
						}
					}
					
				},'json');
			}
		});
		
	}
	
	function limpiarPedido(confirmacion)
	{
		var respuesta = "";
		jConfirm( "Desea limpiar todos los campos?", "ALERTA", function(resp){
			if( resp ){
				
				limpiarCantidadesPedido();
			}
		});
	}
	
	function limpiarCantidadesPedido()
	{
		$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
			$(this).val("");
			$(this).attr("cantidadvalida","");
			$(this).removeClass('fondoamarillo');
		});
		
		$('table[id=tableInsumosBotiquin] span[id^=cantTotalOrdenada_]').each(function(){
			$(this).html("0");
		});
		
		deshabilitarPacientesPedido();
	}
	
	function cerrarModal()
	{
		$.unblockUI();
	}
	
	function cerrarVentana()
	{
		if($("#pedidoGuardado").val()=="off")
		{
			jConfirm( "No ha realizado el pedido, Desea cerrar?", "ALERTA", function(resp){
				if( resp ){
					top.close();
				}
			});
		}
		else
		{
			top.close();	
		}
		
		
    }
	
	function deshabilitarPacientesPedido(input)
	{
		if($("#permitePedidosPorPaciente").val())
		{
			if(input===undefined)
			{
				var campoConCantidad = "";
				$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
					if($(this).val()!="" && $(this).val()!="0")
					{
						campoConCantidad = $(this);
						return;
					}
				});
				
				if(campoConCantidad=="")
				{
					$('table[id=tableInsumosBotiquin] input[id^=cant_]').prop("disabled",true);
				}
				else
				{
					input = campoConCantidad;
				}
			}
			
			// -----------------------------------------------
			
			
			
			// si el pedido es para urgencias y sin ubicacion el atributo habitacion será la historia e ingreso del paciente
			habitacionCampo = $(input).attr("habitacion");
			
			var cantidadInsumos = 0;
			$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
				
				if($(this).val()!="" && $(this).val()!="0")
				{
					cantidadInsumos++;
				}
				
				if($(this).attr("habitacion")!=habitacionCampo)
				{
					$(this).prop("disabled",true);
				}
			});
			
			// habilitar campos si no tiene nada pedido
			if(cantidadInsumos==0 && input!="")
			{
				$('table[id=tableInsumosBotiquin] input[id^=cant_]').prop("disabled",false);
			}
			
			deshabilitarPacientesPaquete();
			
		}
		
		deshabilitarPacientesEnConsulta()
	}
	
	function seleccionarPacientesPaquetes()
	{
		jAlert("Elija los pacientes y seleccione el paquete","ALERTA");
		
		$(".SeleccionarPacientesPaquete").show();
		$("#seleccionarPaquete").show();
		$("#seleccionarPacientes").hide();
		
		deshabilitarPacientesEnConsulta();
		
		
	}
	
	function seleccionarPaquete(habitacion)
	{
		var pacienteValido = true;
		// validar si ya hay un paciente con insumos para cco que solo permiten un paciente por pedido
		if($("#permitePedidosPorPaciente").val())
		{
			pacienteValido = false;
			var pacienteCampo = "";
			$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
				if($(this).val()!="" && $(this).val()!="0")
				{
					pacienteCampo = $(this).attr("id");
					pacienteCampo = pacienteCampo.split("_");
					pacienteCampo = pacienteCampo[2];
					
					if($("#checkPacienteConPaquete_"+habitacion).attr("paciente")==pacienteCampo)
					{
						pacienteValido = true;
						return;
					}
				}
			});
			
			
			if(pacienteCampo=="" && $("#codPedido").val()=="")
			{
				pacienteValido = true;
			}
		}
		
		if($("#tooltipPacientes_"+habitacion).attr("enconsulta") == "on")
		{
			jAlert("No puede realizar pedidos para pacientes en consulta","ALERTA");
			return;
		}
		
		
		if(pacienteValido)
		{
			jConfirm( "Desea seleccionar un paquete?", "ALERTA", function(resp){
				if( resp ){
					$("#checkPacienteConPaquete_"+habitacion).prop("checked",true);
					abrirModalSeleccionarPaquetes();
				}
			});
		}
		else
		{
			jAlert("No puede realizar pedidos para otro paciente","ALERTA");
		}
		
		// jConfirm( "Desea seleccionar un paquete?", "ALERTA", function(resp){
			// if( resp ){
				// $("#checkPacienteConPaquete_"+habitacion).prop("checked",true);
				// abrirModalSeleccionarPaquetes();
			// }
		// });
	}
	
	
	function abrirModalSeleccionarPaquetes()
	{
		// validar paciente seleccionados
		if($('table[id=tableInsumosBotiquin] input[class=SeleccionarPacientesPaquete]:checked').length>0)
		{
			$.post("pedidoInsumosAuxiliares.php",
			{
				consultaAjax 	: '',
				accion			: 'pintarListaPaquetes',
				wemp_pmla		: $('#wemp_pmla').val(),
				codigoCco		: $('#filtroCcoDispensacion').val()
			}
			, function(data) {
				
				$( "#divPaquetes" ).html(data);
			
				$( "#divPaquetes" ).dialog({
					modal: true,
					width:"auto",
					buttons: {
					"SELECCIONAR": function() {
					  
						pintarInsumosPaquete();
					
					},
					"CANCELAR": function() {
					  $( this ).dialog( "close" );
					}
				  }
				});
				
			},'json');
			
		}
		else
		{
			jAlert("Debe seleccionar al menos un paciente para elegir el paquete","ALERTA");
		}
	}
	
	
	
	function pintarInsumosPaquete(habitacion)
	{
		var arrayInsumosNoAgregados = {}; 
	
		if($("#codigoPaquete").val()!="")
		{
			$( "#divPaquetes" ).dialog( "close" );
			$.post("pedidoInsumosAuxiliares.php",
				{
					consultaAjax 	: '',
					accion			: 'consultarInsumosPaquete',
					wemp_pmla		: $('#wemp_pmla').val(),
					codigoPaquete	: $("#codigoPaquete").val()
				}
				, function(data) {
					
					$('table[id=tableInsumosBotiquin] input[class=SeleccionarPacientesPaquete]:checked').each(function(){
						
						var pac = $(this).attr("paciente");
						
						for(resultado in data)
						{
							
							// // reemplazar por las cantidades del paquete
							// $("#cant_"+resultado+"_"+pac).val(data[resultado]);
							// validarCantidadInsumo($("#cant_"+resultado+"_"+pac));

							
							// Sumar a las cantidades actuales
							var canIns = $("#cant_"+resultado+"_"+pac).val();
							canIns = parseInt(canIns);
							
							if(isNaN(canIns))
							{
								cantidadNueva = data[resultado];
							}
							else
							{
								cantidadNueva = parseInt(data[resultado])+canIns;
							}
							
							if($("#cant_"+resultado+"_"+pac)[0]!==undefined)
							{
								$("#cant_"+resultado+"_"+pac).val(cantidadNueva);
								validarCantidadInsumo($("#cant_"+resultado+"_"+pac));
							}
							else
							{
								if(!arrayInsumosNoAgregados[resultado])
								{
									arrayInsumosNoAgregados[resultado]=resultado;
								}
								
							}
						}
						
						
						if( !paquetesAgregados[$("#codigoPaquete").val()] )
						{
							paquetesAgregados[$("#codigoPaquete").val()] = [];
							paquetesAgregados[$("#codigoPaquete").val()] = {
								insumos: data,
								pacientes: []
							}
						}
						
						paquetesAgregados[$("#codigoPaquete").val()].pacientes[pac] = pac;
							
						$(this).prop('checked', false); 
					});
					
					validarInsumosNoAgregados(arrayInsumosNoAgregados,$("#codigoPaquete").val(),$("#codigoPaquete option:selected").html());
					
					volverAPaquetes();
					
				
				},'json');
				
		}
		else
		{
			jAlert("Debe seleccionar un paquete","ALERTA");
		}
	
	}

	function seleccionarPacientePaquete(e,elemento)
	{
		e.stopPropagation();
		
		if($("#permitePedidosPorPaciente").val())
		{
			$('table[id=tableInsumosBotiquin] input[class=SeleccionarPacientesPaquete]').each(function(){
				
				if($(elemento).prop('checked'))
				{
					if($(this).prop('checked')== false)
					{
						$(this).prop('disabled',true);
					}
				
				}
				else
				{
					$(this).prop('disabled',false);
				}
				
			});
		}
		
		deshabilitarPacientesEnConsulta()
	}
	
	function deshabilitarPacientesPaquete()
	{
		$(".SeleccionarPacientesPaquete").prop('disabled',false);
		
		// si el centro de costos solo permite pedidos para un paciente y ya hay algun insumo 
		// con cantidades solo debe estar habilitado ese paciente para los paquetes
		if($("#permitePedidosPorPaciente").val())
		{
			var pacienteCampo = "";
			var cantidadHabilitados = 0;
			$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
				if($(this).val()!="" && $(this).val()!="0")
				{
					pacienteCampo = $(this).attr("id");
					pacienteCampo = pacienteCampo.split("_");
					pacienteCampo = pacienteCampo[2];
					
					$('table[id=tableInsumosBotiquin] input[class=SeleccionarPacientesPaquete]').each(function(){
					
						if($(this).attr('paciente')!=pacienteCampo)
						{
							$(this).prop('disabled',true);
						}
						else
						{
							$(this).prop('disabled',false);
						}
						
						
					});
					
					return;
				}
				
				if($(this).prop('disabled')==false)
				{
					cantidadHabilitados++;
				}
			});
			
			// Si todo está deshabilitado ocultar la opción de paquetes
			if(cantidadHabilitados==0)
			{
				$("#seleccionarPacientes").hide();
			}
		}
	}
	
	function volverAPaquetes()
	{
		$(".SeleccionarPacientesPaquete").hide();
		$("#seleccionarPaquete").hide();
		$("#seleccionarPacientes").show();
		
		$(".SeleccionarPacientesPaquete").prop('checked',false);
	}
	
	function consultarArrayPaquetes()
	{
		$.post("pedidoInsumosAuxiliares.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarPaquetesPedidoActual',
			wemp_pmla		: $('#wemp_pmla').val(),
			codPedido		: $("#codPedido").val()
		}
		, function(data) {
			
			paquetesAgregados = data;
			
			console.log(paquetesAgregados);
			
			
		},'json');
	}
	
	function validarPaquete(historia,ingreso,codigoInsumo)
	{
		var codigoPaquete = "";
		
		// paquete
		for(idArray in paquetesAgregados)
		{
			// si el insumo pertenece al paquete
			if(paquetesAgregados[idArray].insumos[codigoInsumo]!==undefined)
			{
				// miro los pacientes que utilizaron el paquete
				for(pac in paquetesAgregados[idArray].pacientes)
				{
					// si el paciente es igual al campo que estoy validando
					if(pac==historia+"-"+ingreso)
					{
						// var cantInsumosPaquete = 0;
						// var cantidadPaq = 0;
						
						// // cada uno de los insumos del paquete 
						// for(codInsumo in paquetesAgregados[idArray].insumos)
						// {
							// // validar paquete completo
							// var cantidadInsumoPaciente = $("#cant_"+codInsumo+"_"+historia+"-"+ingreso).val();
							// cantidadInsumoPaciente = parseInt(cantidadInsumoPaciente);
							
							// var cantidadPaquete = paquetesAgregados[idArray].insumos[codInsumo];
							// cantidadPaquete = parseInt(cantidadPaquete);
							
							// if(!isNaN(cantidadInsumoPaciente) && cantidadInsumoPaciente>=cantidadPaquete)
							// {
								// cantidadPaq++;
							// }
							
							// cantInsumosPaquete++;
						// }
						
						// if(cantInsumosPaquete==cantidadPaq)
						// {
							// codigoPaquete += idArray+",";
						// }
						
						
						codigoPaquete += idArray+",";
						
					}
				}
			}
		}
		
		codigoPaquete = codigoPaquete.substring(0,codigoPaquete.length-1);
		
		return codigoPaquete;
	}
	
	function validarInsumosNoAgregados(arrayInsumosNoAgregados,codigoPaquete,descripcionPaquete)
	{
		$.post("pedidoInsumosAuxiliares.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarInsumosNoAgregados',
			wemp_pmla		: $('#wemp_pmla').val(),
			codBotiquin		: $("#filtroCcoDispensacion option:selected").attr("botiquin"),
			insNoAgregados	: arrayInsumosNoAgregados
		}
		, function(data) {
			
			var mensaje = "";
			if(data!="")
			{
				mensaje = "Los siguientes insumos del paquete <b>"+descripcionPaquete+"</b> no se agregaron: \n\n";
				jAlert(mensaje+data,"ALERTA");
			}
			
			
			
		},'json');
	}
	
	function deshabilitarPacientesEnConsulta()
	{
		$('table[id=tableInsumosBotiquin] td[id^=tooltipPacientes_]').each(function(){
			
			if($(this).attr('enconsulta')=="on")
			{
				var idTd = $(this).attr('id');
				
				var habitacion = idTd.split("_");
				
				$('#checkPacienteConPaquete_'+habitacion[1]).prop('disabled', true);
				
				$('table[id=tableInsumosBotiquin] input[id^=cant_]').each(function(){
			
					if($(this).attr("habitacion")==habitacion[1])
					{
						$(this).prop('disabled', true);
					}
				});
				
				
			}
		});
	}
	
	$( document ).ready(function(){
		
		$( filtroCcoDispensacion ).change(function(){
			consutlarPacientesDomiciliarios();
		});
		
		$( "#trZona" ).on( 'change', '#filtroZonas',function(){
			
			if( $( "#filtroCcoDispensacion > option:selected" ).data("domiciliario") != 'on' ){
				seleccionarCco(false);
			}
			else{
				consutlarPacientesDomiciliarios();
			}
			
		});
		
		$( "#divPacientesDomiciliarios" ).on( 'click', '#inComenzarPedido', function(){
			
			if( $( ".hisDomiciliarias:checked" ).length > 0 ){
				
				seleccionarCco(false);
				
				$( "#divPacientesDomiciliarios > table, #divPacientesDomiciliarios > div" ).css({display:'none'});
			}
			else{
				jAlert( "Debe seleccionar al menos un paciente", "ALERTA" );
			}
		});
		
		$( "#divPacientesDomiciliarios" ).on( 'click', '#inMarcarTodos', function(){
			
			if( this.checked ){
				$( "#divPacientesDomiciliarios > table input.hisDomiciliarias:visible" ).each(function(){
					this.checked = true;
				});
			}
			else{
				$( "#divPacientesDomiciliarios > table input.hisDomiciliarias:visible" ).each(function(){
					this.checked = false;
				});
			}
		});
		
		$( "#divPacientesDomiciliarios" ).on( 'change', 'input.hisDomiciliarias', function(){ marcarDesmarcarSeleccionarTodos(); });
	})
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
	
	body
	{
		width: auto;
		height: auto;
	}
	
	#popup_title{
		background: #FEAAA4;
		color: black;
	}
	
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
	encabezado("PEDIDO DE INSUMOS", $wactualiz, 'clinica');
	
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	
	
	$arrayRolAuxiliar = validarRolAuxiliar();

	if($arrayRolAuxiliar['esAuxiliar'])
	{
		$filtroBotiquin = pintarFiltroCcoDispensacion();
		echo $filtroBotiquin;
		
		echo "<input type='hidden' id='permitePedidosPorCco' name='permitePedidosPorCco' value='".$arrayRolAuxiliar['permitePedidosCco']."'>";
	}
	else
	{
		echo "<p align='center' style='font-size:18;font-weight:bold;'>El rol ".$arrayRolAuxiliar['rol']." no esta configurado como responsable de insumos. </p>";
	}
	
	
	echo "<div id='divInsumosBotiquin'></div>";
	
	
	echo "<div id='dvAuxModalResumen' style='display:none'></div>";
	echo "<div id='dvModalResumen'></div>";
	
	echo "<div id='divPaquetes' title='Paquetes de insumos'></div>";
	
	echo "<div id='divPacientesDomiciliarios'></div>";
	
	echo "<br>";
	
	echo "	<div id='msjEspere' style='display:none;' align='center'>
				<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...
			</div>";
			
	echo "<br>";
	
	echo "	<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";
	?>
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
