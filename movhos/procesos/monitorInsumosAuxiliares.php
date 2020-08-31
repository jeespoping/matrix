<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Monitor de insumos por auxiliares, lista los insumos en transito (con saldo) de cada paciente, muestra el 
// 						detalle de insumos cargados, aplicados y devueltos.
// 						Solo los roles definidos en el parametro rolesAuxiliarEnfermeria de root_000051 pueden visualizar este monitor.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2017-06-05
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2018-11-08';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
// 	2018-11-08 - Jessica Madrid Mejía:	- En la función pintarModalDetalleInsumos() se agrega la validación para que solo consulte 
// 										  las cantidades aplicadas por otros si el paciente tiene turno de cirugía.
// 	Noviembre 15 de 2017 Edwin MG		- Se hacen cambios varios para que se tenga en cuenta los insumos aplicados desde cx
// 	2017-10-11 - Jessica Madrid Mejía:	- Se quita el filtro Activo='A' en las consultas de la tabla de usuarios para que siempre muestre 
// 										el responsable del movimiento sin importar si está inactivo. 
// 	2017-07-28 - Jessica Madrid Mejía:	En la función pintarInsumosPorAuxiliar() se agrega al order by Carhis,Caring para evitar que se pinte 
// 										mal el reporte debido a que el paciente no tiene habitación (cuando es urgencias).
// 	2017-06-21 - Jessica Madrid Mejía:	Se modifica la consulta de insumos en transito de la función pintarInsumosPorAuxiliar() para 
// 										consultar movhos_000018 en vez de movhos_000020 y así mostrar todos los pacientes con insumos
// 										en transito, sin importar si esta activo o no. 
// 	2017-06-12 - Edwin Molina Grisales: A las enfermeras de HCE se les muestra la opción de anular aplicación.
// 	2017-06-12 - Jessica Madrid Mejía:	Se modifica el orden por habitación y no por historia. 
// 										Se corrige el nombre del botiquin que se muestra en el accordion.
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
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function validarRolAuxiliar()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedatoHce;
		global $wuse;
		
		$esAuxiliar = "";
		
		$rolesAuxiliar = consultarAliasPorAplicacion( $conex, $wemp_pmla, "rolesAuxiliarEnfermeria" );
		$rolesAuxiliar = explode(",",$rolesAuxiliar);
		
		
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
		
		if (!in_array($rolUsuario, $rolesAuxiliar)) 
		{
			$esAuxiliar = $rolUsuario."-".$descRolUsuario;
		}
		
		return $esAuxiliar;
	}
	
	function pintarInsumosPorAuxiliar()
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wuse;
		
		$wbasedato_tcx    = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tcx");
		$wbasedato_cliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion");
			
		$queryInsumos =  "SELECT Carbot,Carhis,Caring,Carins,Carfec,Carcca,Carcap,Carcde,Artgen,Ubisac,Ubihac,Ubihan,Ubiald,Cconom, Cartur
							FROM ".$wbasedato."_000227,".$wbasedato."_000026,".$wbasedato."_000011,".$wbasedato."_000018
						   WHERE Caraux='".$wuse."' 
							 AND Cartra='on'
							 AND Carest='on'
							 AND Artcod=Carins
							 AND Ubihis=Carhis 
							 AND Ubiing=Caring 
							 AND Ccocod=Ubisac 
							 AND Cartur=0
						   UNION
						  SELECT Carbot,tcx11.Turhis AS Carhis, tcx11.Turnin AS Caring,Carins,Carfec,Carcca,Carcap,Carcde,Artgen, m18.Ubisac, CONCAT( 'Qx ', Turqui ) as Ubihac, m18.Ubihan, m18.Ubiald, mx11.Cconom, Cartur
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
							 AND Cartra='on'
							 AND Carcca-Carcap-Carcde>0
							 AND Caraux='".$wuse."'
						ORDER BY Cartur,Carbot,Ubihac,Carhis,Caring,Carfec;";
						
		
		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());		   
		$numInsumos = mysql_num_rows($resInsumos);
		
		$arrayInsumos = array();
		$arrayCco = array();
		$arrayPacientes = array();
		$arrayFechas = array();
		$idInsumo = 0;
		if($numInsumos>0)
		{
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{
				$arrayCco[$rowInsumos['Carbot']] = strtoupper($rowInsumos['Cconom']);
				// $arrayPacientes[$rowInsumos['Carbot']][$rowInsumos['Carhis']."-".$rowInsumos['Caring']] += 1;
				// $arrayFechas[$rowInsumos['Carbot']][$rowInsumos['Carhis']."-".$rowInsumos['Caring']][$rowInsumos['Carfec']] += 1;
				
				$idHisingOrTurn = $rowInsumos['Cartur'] == 0 ? $rowInsumos['Carhis']."-".$rowInsumos['Caring'] : $rowInsumos['Cartur'];
				
				$arrayPacientes[$rowInsumos['Carbot']][ $idHisingOrTurn ] += 1;
				$arrayFechas[$rowInsumos['Carbot']][ $idHisingOrTurn ][$rowInsumos['Carfec']] += 1;
				
				$arrayInsumos[$idInsumo]['codBotiquin'] = $rowInsumos['Carbot'];
				$arrayInsumos[$idInsumo]['historia'] = $rowInsumos['Carhis'];
				$arrayInsumos[$idInsumo]['ingreso'] = $rowInsumos['Caring'];
				$arrayInsumos[$idInsumo]['codInsumo'] = $rowInsumos['Carins'];
				$arrayInsumos[$idInsumo]['fecha'] = $rowInsumos['Carfec'];
				$arrayInsumos[$idInsumo]['cantCargada'] = $rowInsumos['Carcca'];
				$arrayInsumos[$idInsumo]['cantAplicada'] = $rowInsumos['Carcap'];
				$arrayInsumos[$idInsumo]['cantDevuelta'] = $rowInsumos['Carcde'];
				$arrayInsumos[$idInsumo]['descInsumo'] = trim($rowInsumos['Artgen']);
				$arrayInsumos[$idInsumo]['habitacion'] = strtoupper($rowInsumos['Ubihac']);
				$arrayInsumos[$idInsumo]['habitacionAnterior'] = strtoupper($rowInsumos['Ubihan']);
				$arrayInsumos[$idInsumo]['cco'] = $rowInsumos['Ubisac'];
				$arrayInsumos[$idInsumo]['altaDefinitiva'] = $rowInsumos['Ubiald'];
				$arrayInsumos[$idInsumo]['ccoDescripcion'] = strtoupper($rowInsumos['Cconom']);
				$arrayInsumos[$idInsumo]['desBotiquin'] = strtoupper(consultarDescripcionCco( $wbasedato, $rowInsumos['Carbot'] ) );
				$arrayInsumos[$idInsumo]['turno'] = $rowInsumos['Cartur'];
				
				if( $rowInsumos['Cartur'] > 0 ){
					$canAplicadaOtros = cantidadGastadaPorInsumo( $conex, $wbasedato, $rowInsumos['Cartur'], $rowInsumos['Carins'], $wuse );
					$arrayInsumos[$idInsumo]['cantCargada']  = $rowInsumos['Carcca'] - $canAplicadaOtros['Can'] - $canAplicadaOtros['Dev'];
					$arrayInsumos[$idInsumo]['cantAplicada'] = $rowInsumos['Carcap'] - $canAplicadaOtros['Can'];
					$arrayInsumos[$idInsumo]['cantDevuelta'] = $rowInsumos['Carcde'] - $canAplicadaOtros['Dev'];
				}
				
				$idInsumo++;
			}
		}
		
		$html = "";
		$html .= "<div id='divInsumos'>";
		
		$html .= "<br><p align='center' style='font-size:18;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Responsable: ".consultarNombreAuxiliar($wuse)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p><br><br>";
		
		if(count($arrayCco)>0)
		{
			$html .= "<span  id='aplicacionInsumosAuxiliar' style='font-family: verdana;font-weight:bold;font-size: 10pt;align:right;color: #0033FF;text-decoration: underline;cursor:pointer;;position: relative; left:85%;'>
						<a href='botiquinAplicacionInsumos.php?wemp_pmla=".$wemp_pmla."' target='blank'>Aplicaci&oacute;n de insumos</a>
					</span>";
			foreach($arrayCco as $keyCco => $valueCco)
			{
				$html .= "<div id='accordion' class='desplegable'>
							<h3 align='left'>&nbsp;&nbsp;&nbsp;&nbsp;BOTIQUIN: ".consultarDescripcionCco($wbasedato,$keyCco)."</h3>
							<div id='divCco".$keyCco."'>
								<table id='tableCco".$keyCco."' width='95%'>
									<tr class='encabezadoTabla' align='center'>
										<td>Habitaci&oacute;n</td>
										<td>Historia</td>
										<td>Paciente</td>
										<td>Fecha del cargo</td>
										<td>C&oacute;digo del insumo</td>
										<td>Descripci&oacute;n</td>
										<td>Cantidad<br>cargada</td>
										<td>Cantidad<br>aplicada</td>
										<td>Cantidad<br>devuelta</td>
										<td>Saldo</td>
									</tr>";
									
									$conInsumo = 0;
									$fechaAnterior = "";
									$rowspanPaciente = 1;
									$rowspanFecha = 1;
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
											
											
											$saldoInsumo = $valueInsumo['cantCargada']-$valueInsumo['cantAplicada']-$valueInsumo['cantDevuelta'];
											
											$fondoSaldo = "";
											if($saldoInsumo>0)
											{
												$fondoSaldo = "class='fondoAmarillo'";
											}
											
											$fondoRojo = "";
											if($valueInsumo['fecha']!=date("Y-m-d"))
											{
												$fondoRojo = "fondoRojo";
											}
											
											$funcionCantidadCargada = "onclick='verDetalleCantidades(\"CR\",\"".$valueInsumo['codBotiquin']."\",\"".$wuse."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";
											$funcionCantidadAplicada = "onclick='verDetalleCantidades(\"AP\",\"".$valueInsumo['codBotiquin']."\",\"".$wuse."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";
											$funcionCantidadDevuelta = "onclick='verDetalleCantidades(\"DV\",\"".$valueInsumo['codBotiquin']."\",\"".$wuse."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";
											
											$rowspanPaciente = $arrayPacientes[$keyCco][ $valueInsumo['turno'] == 0 ? $valueInsumo['historia']."-".$valueInsumo['ingreso']:$valueInsumo['turno'] ];
											$rowspanFecha = $arrayFechas[$keyCco][ $valueInsumo['turno'] == 0 ? $valueInsumo['historia']."-".$valueInsumo['ingreso']:$valueInsumo['turno'] ][$valueInsumo['fecha']];
											
											
											$conPaciente=0;
											if( ( $valueInsumo['turno'] == 0 && $PacienteAnterior===$valueInsumo['historia']."-".$valueInsumo['ingreso'] ) || ( $valueInsumo['turno'] > 0 && $PacienteAnterior===$valueInsumo['turno'] ) )
											{
												$conPaciente++;
											}
											
											
											$conFecha=0;
											if($fechaAnterior===$valueInsumo['fecha'])
											{
												$conFecha++;
											}
											
											
											// ------------------------------------------
											// Tooltip
											// ------------------------------------------	
												
											$tooltipCargados = "<div id=\"dvTooltipCargados\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades cargadas</div>";
											$tooltipAplicados = "<div id=\"dvTooltipAplicados\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades aplicadas</div>";
											$tooltipDevueltos = "<div id=\"dvTooltipDevueltos\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades devueltas</div>";
											
											
											if($conPaciente==0)
											{
												if ($fila_lista=='Fila1')
													$fila_lista = "Fila2";
												else
													$fila_lista = "Fila1";
												
												$fila_rowspan = $fila_lista;
												
												$PacienteAnterior = $valueInsumo['turno'] == 0 ? $valueInsumo['historia']."-".$valueInsumo['ingreso'] : $valueInsumo['turno'];
												$fechaAnterior = $valueInsumo['fecha'];
												
												
				$html .= "						<tr class='".$fila_lista."'>
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPaciente."' ".$estiloHabitacion.">".$habitacion."</td>
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPaciente."'>".$valueInsumo['historia']."-".$valueInsumo['ingreso']."</td>
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPaciente."'>".consultarNombrePaciente($valueInsumo['historia'])."</td>
													<td class='".$fila_rowspan." ".$fondoRojo."' align='center' rowspan='".$rowspanFecha."'>".$valueInsumo['fecha']."</td>
													<td align='center'>".$valueInsumo['codInsumo']."</td>
													<td align='center'>".$valueInsumo['descInsumo']."</td>
													<td align='center' style='cursor:pointer;' class='cantCargadas' title='".$tooltipCargados."' ".$funcionCantidadCargada.">".$valueInsumo['cantCargada']."</td>
													<td align='center' style='cursor:pointer;' class='cantAplicadas' title='".$tooltipAplicados."' ".$funcionCantidadAplicada.">".$valueInsumo['cantAplicada']."</td>
													<td align='center' style='cursor:pointer;' class='cantDevueltas' title='".$tooltipDevueltos."' ".$funcionCantidadDevuelta.">".$valueInsumo['cantDevuelta']."</td>
													<td align='center' ".$fondoSaldo.">".$saldoInsumo."</td>
												</tr>";	
											}
											else
											{
												if($conFecha==0)
												{
													$fechaAnterior = $valueInsumo['fecha'];
				$html .= "							<tr class='".$fila_lista."'>
														<td class='".$fila_rowspan." ".$fondoRojo."' align='center' rowspan='".$rowspanFecha."'>".$valueInsumo['fecha']."</td>
														<td align='center'>".$valueInsumo['codInsumo']."</td>
														<td align='center'>".$valueInsumo['descInsumo']."</td>
														<td align='center' style='cursor:pointer;' class='cantCargadas' title='".$tooltipCargados."' ".$funcionCantidadCargada.">".$valueInsumo['cantCargada']."</td>
														<td align='center' style='cursor:pointer;' class='cantAplicadas' title='".$tooltipAplicados."' ".$funcionCantidadAplicada.">".$valueInsumo['cantAplicada']."</td>
														<td align='center' style='cursor:pointer;' class='cantDevueltas' title='".$tooltipDevueltos."' ".$funcionCantidadDevuelta.">".$valueInsumo['cantDevuelta']."</td>
														<td align='center' ".$fondoSaldo.">".$saldoInsumo."</td>
													</tr>";										
												}
												else
												{
				$html .= "							<tr class='".$fila_lista."'>
														<td align='center'>".$valueInsumo['codInsumo']."</td>
														<td align='center'>".$valueInsumo['descInsumo']."</td>
														<td align='center' style='cursor:pointer;' class='cantCargadas' title='".$tooltipCargados."' ".$funcionCantidadCargada.">".$valueInsumo['cantCargada']."</td>
														<td align='center' style='cursor:pointer;' class='cantAplicadas' title='".$tooltipAplicados."' ".$funcionCantidadAplicada.">".$valueInsumo['cantAplicada']."</td>
														<td align='center' style='cursor:pointer;' class='cantDevueltas' title='".$tooltipDevueltos."' ".$funcionCantidadDevuelta.">".$valueInsumo['cantDevuelta']."</td>
														<td align='center' ".$fondoSaldo.">".$saldoInsumo."</td>
													</tr>";										
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
			 $html .= "<p align='center'><b>No tiene insumos cargados en tr&aacute;nsito.</b></p>";
		}
		$html .= "</div>";
		
		
		return $html;
	
	}
	
	function consultarNombreAuxiliar($codAuxiliar)
	{
		global $conex;
		
		$queryAuxiliar =  "SELECT Descripcion
							 FROM usuarios
						    WHERE Codigo='".$codAuxiliar."';";
		
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
	
	function consultarNombrePaciente1($historia,$ingreso,$wemp_pmla)
	{
		global $conex;
		
		$queryPaciente =  "SELECT Pacno1,Pacno2,Pacap1,Pacap2 
							 FROM root_000037,root_000036 
							WHERE Orihis='".$historia."'
							  AND Oriing='".$ingreso."'
							  AND Oriori='".$wemp_pmla."'
							  AND Oriced=Pacced
							  AND Oritid=Pactid;";
		
		$resPaciente = mysql_query($queryPaciente, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPaciente . " - " . mysql_error());		   
		$numPaciente = mysql_num_rows($resPaciente);
		
		$nombrePaciente = "";
		if($numPaciente>0)
		{
			$rowPaciente = mysql_fetch_array($resPaciente);
			
			$nombreAPaciente = $rowPaciente['Pacno1']." ".$rowPaciente['Pacno2']." ".$rowPaciente['Pacap1']." ".$rowPaciente['Pacap2'];
			
		}
		
		return $nombrePaciente;
	}
	
	function consultarDetalleInsumos($wbasedato,$codMovimiento,$botiquin,$auxiliar,$insumo,$fecha,$historia,$ingreso,$turno=0)
	{
		global $conex;
		
		$queryDetalle =  "SELECT Movcco,Movhab,Movcmo,Movfmo,Movhmo,Movumo,Descripcion,Cconom,a.id as idMovimiento, Artuni
							FROM ".$wbasedato."_000228 a,usuarios b,".$wbasedato."_000011 c,".$wbasedato."_000026 d
						   WHERE Movbot='".$botiquin."' 
							 AND Movaux='".$auxiliar."' 
							 AND Movhis='".$historia."' 
							 AND Moving='".$ingreso."' 
							 AND Movins='".$insumo."' 
							 AND Movfec='".$fecha."' 
							 AND Movmov='".$codMovimiento."' 
							 AND Movest='on'
							 AND Movumo=Codigo
							 AND Movcco=Ccocod
							 AND Ccoest='on'
							 AND Artcod = Movins
							 AND Movtur = 0
						   UNION 
						  SELECT Movcco,Movhab,Movcmo,Movfmo,Movhmo,Movumo,Descripcion,Cconom,a.id as idMovimiento, Artuni
							FROM ".$wbasedato."_000228 a LEFT JOIN ".$wbasedato."_000011 c ON Movcco=Ccocod AND Ccoest='on',usuarios b,".$wbasedato."_000026 d
						   WHERE Movbot='".$botiquin."' 
							 AND Movaux='".$auxiliar."' 
							 AND Movtur='".$turno."' 
							 AND Movins='".$insumo."' 
							 AND Movfec='".$fecha."' 
							 AND Movmov='".$codMovimiento."' 
							 AND Movest='on'
							 AND Movumo=Codigo
							 AND Artcod = Movins
							 AND Movtur > 0
							 ;";
		
		
		$resDetalle = mysql_query($queryDetalle, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDetalle . " - " . mysql_error());		   
		$numDetalle = mysql_num_rows($resDetalle);
		
		$arrayDetalle = array();
		$contDetalleInsumos = 0;
		if($numDetalle>0)
		{
			while($rowDetalle = mysql_fetch_array($resDetalle))
			{
				$arrayDetalle[$contDetalleInsumos]['cco'] = $rowDetalle['Movcco'];
				$arrayDetalle[$contDetalleInsumos]['habitacion'] = $rowDetalle['Movhab'];
				$arrayDetalle[$contDetalleInsumos]['cantidad'] = $rowDetalle['Movcmo'];
				$arrayDetalle[$contDetalleInsumos]['fecha'] = $rowDetalle['Movfmo'];
				$arrayDetalle[$contDetalleInsumos]['hora'] = $rowDetalle['Movhmo'];
				$arrayDetalle[$contDetalleInsumos]['usuario'] = $rowDetalle['Descripcion'];
				$arrayDetalle[$contDetalleInsumos]['ccoPaciente'] = $rowDetalle['Cconom'];
				$arrayDetalle[$contDetalleInsumos]['idMovimiento'] = $rowDetalle['idMovimiento'];
				$arrayDetalle[$contDetalleInsumos]['unidadInsumo'] = $rowDetalle['Artuni'];
				
				$contDetalleInsumos++;
			}
		
		}
		
		
		return $arrayDetalle;
	}
	
	function consultarNombrePaciente($historia)
	{
		global $conex;
		global $wemp_pmla;
		
		$queryNombre =  " SELECT Pacno1,Pacno2,Pacap1,Pacap2 
							FROM root_000037,root_000036
						   WHERE Orihis='".$historia."' 
							 AND Oriori='".$wemp_pmla."'
							 AND Pacced=Oriced
							 AND Pactid=Oritid;";
		
		$resNombre = mysql_query($queryNombre, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryNombre . " - " . mysql_error());		   
		$numNombre = mysql_num_rows($resNombre);
		
		$nombrePaciente = "";
		if($numNombre>0)
		{
			$rowNombre = mysql_fetch_array($resNombre);
			
			if($rowNombre['Pacno1']!="NO APLICA" || $rowNombre['Pacno1']!="")
			{
				$nombrePaciente .= $rowNombre['Pacno1']." ";
			}
			if($rowNombre['Pacno2']!="NO APLICA" || $rowNombre['Pacno2']!="")
			{
				$nombrePaciente .= $rowNombre['Pacno2']." ";
			}
			if($rowNombre['Pacap1']!="NO APLICA" || $rowNombre['Pacap1']!="")
			{
				$nombrePaciente .= $rowNombre['Pacap1']." ";
			}
			if($rowNombre['Pacap2']!="NO APLICA" || $rowNombre['Pacap2']!="")
			{
				$nombrePaciente .= $rowNombre['Pacap2'];
			}
			
		}
		
		return $nombrePaciente;
	}
	
	function consultarDescripcionCco($wbasedato,$codCco)
	{
		global $conex;
		
		$queryCco =  "SELECT Cconom  
							FROM ".$wbasedato."_000011 
						   WHERE Ccocod='".$codCco."' 
							 AND Ccoest='on';";
		
		$resCco = mysql_query($queryCco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCco . " - " . mysql_error());		   
		$numCco = mysql_num_rows($resCco);
		
		$cco = "";
		if($numCco>0)
		{
			$rowCco = mysql_fetch_array($resCco);
			
			$cco = $rowCco['Cconom'];
			
		}
		
		return $cco;
	}
	
	function pintarModalDetalleInsumos($wemp_pmla,$wbasedato,$codMovimiento,$botiquin,$auxiliar,$insumo,$fecha,$insumoGenerico,$historia,$ingreso,$habitacion,$cco,$turno)
	{
		global $conex;
		
		list( $estaNoSeUsa, $wuse ) = explode( "-", $_SESSION['user'] );
		
		$enfermerasHCE = consultarAliasPorAplicacion( $conex, $wemp_pmla, "enfermerasHCE" );
		$enfermerasHCE = explode( ",", $enfermerasHCE );
		$mostarAnularAplicacion = in_array( $wuse, $enfermerasHCE ) && $codMovimiento== "AP";
		
		$detalleInsumos = consultarDetalleInsumos($wbasedato,$codMovimiento,$botiquin,$auxiliar,$insumo,$fecha,$historia,$ingreso,$turno);
		if( $turno > 0 ){
			$canAplicadaOtros = cantidadGastadaPorInsumo( $conex, $wbasedato, $turno, $insumo, $auxiliar );
		}
		$descripcionMovimiento = "";
		$descMov = "";
		if($codMovimiento=="CR")
		{
			$descripcionMovimiento = "cargados";
			$descMov = "EL CARGO";
		}
		elseif($codMovimiento=="AP")
		{
			$descripcionMovimiento = "aplicados";
			$descMov = "LA APLICACI&Oacute;N";
		}
		elseif($codMovimiento=="DV")
		{
			$descripcionMovimiento = "devueltos";
			$descMov = "LA DEVOLUCI&Oacute;N";
		}
		
		
		// if($habitacion=="")
		// {
			// $habitacion = "Sin ubicaci&oacute;n actual";
			// $cco = "Sin ubicaci&oacute;n actual";
		// }
		// else
		// {
			// $cco = consultarDescripcionCco($wbasedato,$cco);
		// }
		
		
		$colspanTabla = "1";
		$modal = "";
		
		
		$modal .= "	<div id='modalDetalleInsumos'>
					<br>
					<p align='center' style='font-size:18pt;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Botiqu&iacute;n: ".consultarDescripcionCco($wbasedato,$botiquin)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>
					
					";
					if(count($detalleInsumos)>0)
					{
						$htmlEncabezado = "";
						
						if($codMovimiento=="CR")
						{
							$colspanTabla="7";
							$htmlEncabezado .= "<tr class='encabezadoTabla'>
													<td align='center'>Insumo</td>
													<td align='center'>Cantidad</td>
													<td align='center'>Fecha</td>
													<td align='center'>Hora</td>
													<td align='center'>Cargado por</td>
													<td align='center'>Habitaci&oacute;n</td>
													<td align='center'>Centro de costos</td>
												</tr>";	
						}
						elseif($codMovimiento=="AP")
						{
							$colspanTabla="7";
							$htmlEncabezado .= "<tr class='encabezadoTabla'>
													<td align='center'>Insumo</td>
													<td align='center'>Cantidad</td>
													<td align='center'>Fecha</td>
													<td align='center'>Hora</td>
													<td align='center'>Aplicado por</td>
													<td align='center'>Habitaci&oacute;n</td>
													<td align='center' colspan='".( $mostarAnularAplicacion ? 2 : 1 )."'>Centro de costos</td>
												</tr>";	
						}
						elseif($codMovimiento=="DV")
						{
							$colspanTabla="7";
							$htmlEncabezado .= "<tr class='encabezadoTabla'>
													<td align='center'>Insumo</td>
													<td align='center'>Cantidad</td>
													<td align='center'>Fecha</td>
													<td align='center'>Hora</td>
													<td align='center'>Devuelto por</td>
													<td align='center'>Habitaci&oacute;n</td>
													<td align='center'>Centro de costos</td>
												</tr>";	
						}
						
						$htmlDetalle = "";
						foreach($detalleInsumos as $keyDetalles => $valueDetalles)
						{
							if ($fila_lista=='Fila1')
								$fila_lista = "Fila2";
							else
								$fila_lista = "Fila1";
							
							
							if($codMovimiento=="CR")
							{
								$cantidad = $valueDetalles['cantidad'];
								if( $turno > 0 ){
									$cantidad = $valueDetalles['cantidad'] - $canAplicadaOtros['Can'];
								}
								
								if($keyDetalles=="0")
								{
									$htmlDetalle .= "<tr class='".$fila_lista."'>
														<td align='center' rowspan='".count($detalleInsumos)."'>".$insumoGenerico."</td>
														<td align='center'>".$cantidad."</td>
														<td align='center'>".$valueDetalles['fecha']."</td>
														<td align='center'>".$valueDetalles['hora']."</td>
														<td align='center'>".$valueDetalles['usuario']."</td>
														<td align='center'>".$valueDetalles['habitacion']."</td>
														<td align='center'>".$valueDetalles['cco']." - ".$valueDetalles['ccoPaciente']."</td>
													</tr>";
								}
								else
								{
									$htmlDetalle .= "<tr class='".$fila_lista."'>
														<td align='center'>".$cantidad."</td>
														<td align='center'>".$valueDetalles['fecha']."</td>
														<td align='center'>".$valueDetalles['hora']."</td>
														<td align='center'>".$valueDetalles['usuario']."</td>
														<td align='center'>".$valueDetalles['habitacion']."</td>
														<td align='center'>".$valueDetalles['cco']." - ".$valueDetalles['ccoPaciente']."</td>
													</tr>";	
								}
								
								
							}
							elseif($codMovimiento=="AP")
							{
								$codigo 	  = $insumo;
								$cantidad 	  = $valueDetalles['cantidad'];
								$presentacion = $valueDetalles['unidadInsumo'];
								$ccopac 	  = $valueDetalles['cco'];
								$cconompac 	  = $valueDetalles['ccoPaciente'];
								$hab 		  = $valueDetalles['habitacion'];
								$movimiento   = $valueDetalles['idMovimiento'];
								
								$onClick = "anularAplicacion(this,'$auxiliar','$codigo','$cantidad','$presentacion','$botiquin','$ccopac','$cconompac','$hab','$historia','$ingreso','$movimiento','$turno')";
								
								if($keyDetalles=="0")
								{
									$htmlDetalle .= "<tr class='".$fila_lista."'>
														<td align='center' rowspan='".count($detalleInsumos)."'>".$insumoGenerico."
														<td align='center'>".$valueDetalles['cantidad']."</td>
														<td align='center'>".$valueDetalles['fecha']."</td>
														<td align='center'>".$valueDetalles['hora']."</td>
														<td align='center'>".$valueDetalles['usuario']."</td>
														<td align='center'>".$valueDetalles['habitacion']."</td>
														<td align='center'>".$valueDetalles['cco']." - ".$valueDetalles['ccoPaciente']."</td>
														".( $mostarAnularAplicacion ? "<td align='center'><a href=#null onClick=\"$onClick\"><img src='../../images/medical/root/borrar.png' width=17 height=17></a></td>" : ""  )."
													</tr>";	
								}
								else
								{
									$htmlDetalle .= "<tr class='".$fila_lista."'>
														<td align='center'>".$valueDetalles['cantidad']."</td>
														<td align='center'>".$valueDetalles['fecha']."</td>
														<td align='center'>".$valueDetalles['hora']."</td>
														<td align='center'>".$valueDetalles['usuario']."</td>
														<td align='center'>".$valueDetalles['habitacion']."</td>
														<td align='center'>".$valueDetalles['cco']." - ".$valueDetalles['ccoPaciente']."</td>
														".( $mostarAnularAplicacion ? "<td align='center'><a href=#null onClick=\"$onClick\"><img src='../../images/medical/root/borrar.png' width=17 height=17></a></td>" : ""  )."
													</tr>";	
								}
								
							}
							elseif($codMovimiento=="DV")
							{
								$cantidad = $valueDetalles['cantidad'];
								if( $turno > 0 ){
									$cantidad = $valueDetalles['cantidad'] - $canAplicadaOtros['Dev'];
								}
								
								if($keyDetalles=="0")
								{
									$htmlDetalle .= "<tr class='".$fila_lista."'>
														<td align='center' rowspan='".count($detalleInsumos)."'>".$insumoGenerico."</td>
														<td align='center'>".$cantidad."</td>
														<td align='center'>".$valueDetalles['fecha']."</td>
														<td align='center'>".$valueDetalles['hora']."</td>
														<td align='center'>".$valueDetalles['usuario']."</td>
														<td align='center'>".$valueDetalles['habitacion']."</td>
														<td align='center'>".$valueDetalles['cco']." - ".$valueDetalles['ccoPaciente']."</td>
													</tr>";
								}
								else
								{
									$htmlDetalle .= "<tr class='".$fila_lista."'>
														<td align='center'>".$cantidad."</td>
														<td align='center'>".$valueDetalles['fecha']."</td>
														<td align='center'>".$valueDetalles['hora']."</td>
														<td align='center'>".$valueDetalles['usuario']."</td>
														<td align='center'>".$valueDetalles['habitacion']."</td>
														<td align='center'>".$valueDetalles['cco']." - ".$valueDetalles['ccoPaciente']."</td>
													</tr>";	
								}
							}
						}
		if( $mostarAnularAplicacion )
			$colspanTabla++;
		$modal .= "		<table align='center' width='90%'>
							<tr class='encabezadoTabla'>
								<td colspan='".$colspanTabla."' align='center'>DATOS DEL PACIENTE</td>
							</tr>
							
							<tr>
								<td class='fila1'>Historia</td>
								<td class='fila2' colspan='2'>".$historia."-".$ingreso."</td>
								<td class='fila1' colspan='2'>Nombre</td>
								<td class='fila2' colspan='".( $mostarAnularAplicacion ? 3 : 2 )."'>".consultarNombrePaciente($historia,$ingreso,$wemp_pmla)."</td>
							</tr>
							
							<tr>
								<td class='fila1'>Habitaci&oacute;n actual</td>
								<td class='fila2' colspan='2'>".$habitacion."</td>
								<td class='fila1' colspan='2'>Centro de costos actual</td>
								<td class='fila2' colspan='".( $mostarAnularAplicacion ? 3 : 2 )."'>".consultarDescripcionCco($wbasedato,$cco)."</td>
							</tr>
							<tr class='encabezadoTabla'>
								<td colspan='".$colspanTabla."' align='center'>INSUMOS ".strtoupper($descripcionMovimiento)."</td>
							</tr>
							
							<tr class='encabezadoTabla'>
								<td colspan='".($colspanTabla-2)."' align='center'>DETALLE DE INSUMOS ".strtoupper($descripcionMovimiento)."</td>
								<td colspan='2' align='center'>UBICACI&Oacute;N DEL PACIENTE DURANTE ".$descMov."</td>
							</tr>
							".$htmlEncabezado."
							".$htmlDetalle."
						</table>";
						
					}
					else
					{
						
		$modal .= "		<p align='center'><b>No se han realizado movimientos para este insumo</b></p>";				
						
					}
		$modal .= "		<br><br>
						<span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span>
						<br><br>
					</div>";
		
		
		return $modal;
		
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
		case 'pintarModalDetalle':
		{	
			$data = pintarModalDetalleInsumos($wemp_pmla,$wbasedato,$codMovimiento,$botiquin,$auxiliar,$insumo,$fecha,$insumoGenerico,$historia,$ingreso,$habitacion,$cco,$turno);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarInsumos':
		{	
			$data = pintarInsumosPorAuxiliar();
			$data = utf8_encode($data);
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
	?>
	<html>
	<head>
	  <title>MONITOR INSUMOS</title>
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
		


	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
		
	$(document).ready(function () {
		inicializarAccordion();
		inicializarTooltip();
	});
	
	//Actualizar cada dos minutos
	setInterval(function()
	{
		pintarInsumos();
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
				height	: "70%",
				left	: "10%",
				top		: '100px',
			} });
			
		},'json');
	}
	
	function pintarInsumos()
	{
		$.post("monitorInsumosAuxiliares.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarInsumos',
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {
			
			$( "#divInsumos" ).html(data);
			inicializarAccordion();
			inicializarTooltip();
			
		},'json');
	}
	
	function cerrarModal()
	{
		$.unblockUI();
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
	encabezado("MONITOR DE INSUMOS", $wactualiz, 'clinica');
	
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	
	
	$esAuxiliar = validarRolAuxiliar();

	if($esAuxiliar=="")
	{
		$insumos = pintarInsumosPorAuxiliar();
		echo $insumos;
	}
	else
	{
		echo "<p align='center' style='font-size:18;font-weight:bold;'>El rol ".$esAuxiliar." no esta configurado como responsable de insumos. </p>";
	}
	
	echo "<div id='dvAuxModalDetalleInsumos' style='display:none'></div>";
	
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
