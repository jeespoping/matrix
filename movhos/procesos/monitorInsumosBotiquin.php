<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Monitor de insumos por botiquin. Lista los insumos en transito (con saldo) de cada paciente y por responsable 
// 						de insumos, muestra el detalle de insumos cargados, aplicados y devueltos.
// 						Se realiza la devolución de insumos con saldo.
// 						Se listan los insumos en tránsito y el inventario del botiquín con saldo.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2017-06-05
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2017-11-15';
//--------------------------------------------------------------------------------------------------------------------------------------------       
//  Marzo 12 de 2018	 Edwin MG		Al devolver un dispositivo implantable se piede el lote
// 	Noviembre 15 de 2017 Edwin MG		Se hacen cambios varios para que se tenga en cuenta los insumos aplicados desde cx
// 	2017-07-28 - Jessica Madrid Mejía:	En la función pintarInsumosPorAuxiliar() se agrega al order by Carhis,Caring para evitar que se pinte 
// 										mal el reporte debido a que el paciente no tiene habitación (cuando es urgencias).
// 	2017-06-22 - Jessica Madrid Mejía:	Se agrega la foto de los responsables de insumos, se controla si se muestran o no con el 
// 										parámetro fotoAuxiliares de root_000051
// 	2017-06-21 - Jessica Madrid Mejía:	Se modifica la consulta de insumos en transito de la función pintarInsumosPorAuxiliar() para 
// 										consultar movhos_000018 en vez de movhos_000020 y así mostrar todos los pacientes con insumos
// 										en transito, sin importar si esta activo o no. 
// 	2017-06-12 - Jessica Madrid Mejía:	Se modifica el orden por habitación y no por historia. 
// 										Se corrige el nombre del botiquin que se muestra en el accordion.
// 	2017-06-07 - Jessica Madrid Mejía:	Se agrega fondo rojo a las fechas diferentes de la actual.
// 	2017-06-05 - Jessica Madrid Mejía:	Se adiciona la opción Inventario Botiquín así el botiquín no tenga insumos en tránsito.
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
	include_once("movhos/cargosSF.inc.php");
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarBotiquines()
	{
		global $conex;
		global $wbasedato;
		
		$queryBotiquines =  " SELECT Ccoori,Cconom  
								FROM ".$wbasedato."_000058 a,".$wbasedato."_000011 b
							   WHERE a.Ccoest='on' 
							     AND Ccocod=Ccoori
								 AND b.Ccoest='on' 
							GROUP BY Ccoori;";
		
		$resBotiquines = mysql_query($queryBotiquines, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryBotiquines . " - " . mysql_error());		   
		$numBotiquines = mysql_num_rows($resBotiquines);
		
		$arrayBotiquines = array();
		if($numBotiquines>0)
		{
			while($rowBotiquines = mysql_fetch_array($resBotiquines))
			{
				$arrayBotiquines[$rowBotiquines['Ccoori']] = $rowBotiquines['Cconom'];
			}
		}
		
		return $arrayBotiquines;
	}
	
	function pintarFiltroBotiquin($codBotiquin)
	{
		$botiquines = consultarBotiquines();
		
		$ocultarFiltroCco = "";
		if($codBotiquin!="")
		{
			$ocultarFiltroCco = "display:none;";
		}
		
		$filtroFecha = "";
		$filtroFecha .= "	<div id='divFiltroCco' align='center' style='".$ocultarFiltroCco."'>
								<fieldset align='center' style='padding:5px;margin:5px;border: 2px solid #2a5db0;width:33%'>
									<legend style='border: 2px solid #2a5db0;border-top: 0px;font-family: Verdana;color: #ffffff;background-color: #2a5db0;font-size:8pt;font-weight:bold;'> Seleccione un botiqu&iacute;n </legend>
									<table>
										<tr>
											<td  align='center' colspan='2'>
												<select id='filtroBotiquin' name='filtroBotiquin' onChange='seleccionarBotiquin();'>
													<option>Seleccione un botiquin</option>";
													foreach($botiquines as $keyBotiquin => $valueBotiquin)
													{
														$seleccionado = "";
														if($codBotiquin==$keyBotiquin)
														{
															$seleccionado = "selected";
														}
														
		$filtroFecha .= "								<option value='".$keyBotiquin."' ".$seleccionado.">".$keyBotiquin."-".$valueBotiquin."</option>";												
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
	
	function pintarInsumosPorAuxiliar($codBotiquin)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wuse;
		
		$wbasedato_tcx    = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tcx");
		$wbasedato_cliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion");
		
		$condicionBotiquin = "";
		if($codBotiquin!="")
		{
			$condicionBotiquin = "AND Carbot='".$codBotiquin."'";
		}
		
		$queryInsumos =  "SELECT Carbot,Caraux,Carhis,Caring,Carins,Carfec,Carcca,Carcap,Carcde,Artgen,Cconom,Descripcion,Empresa,Ubisac,Ubihac,Ubihan,Ubiald,Pacno1,Pacno2,Pacap1,Pacap2, Cartur, Ccourg
							FROM ".$wbasedato."_000227,".$wbasedato."_000026,".$wbasedato."_000011,usuarios,".$wbasedato."_000018,root_000037,root_000036
						   WHERE Cartra='on'
							 ".$condicionBotiquin."
							 AND Carest='on'
							 AND Artcod=Carins
							 AND Codigo=Caraux
							 AND Activo='A'
							 AND Ubihis=Carhis 
							 AND Ubiing=Caring
							 AND Ccocod=Ubisac
							 AND Orihis=Carhis
							 AND Oriing=Caring
							 AND Oriori='".$wemp_pmla."'
							 AND Pacced=Oriced
							 AND Pactid=Oritid
							 AND Cartur=0
						   UNION
						  SELECT Carbot,Caraux,tcx11.Turhis AS Carhis, tcx11.Turnin AS Caring,Carins,Carfec,Carcca,Carcap,Carcde,Artgen, mx11.Cconom,Descripcion,Empresa, m18.Ubisac, CONCAT( 'Qx ', Turqui ) as Ubihac, m18.Ubihan, m18.Ubiald, c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2, Cartur, Ccourg
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
							 ".$condicionBotiquin."
						ORDER BY Carbot,Caraux,Cartur,Ubihac,Carhis,Caring,Carfec;";
		
		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());		   
		$numInsumos = mysql_num_rows($resInsumos);
		
		$arrayInsumos = array();
		$arrayCco = array();
		$arrayFechas = array();
		$arrayAuxiliares = array();
		$idInsumo = 0;
		if($numInsumos>0)
		{
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{
				
				$arrayCco[$rowInsumos['Carbot']] = strtoupper($rowInsumos['Cconom']);
				$arrayAuxiliares[$rowInsumos['Carbot']][$rowInsumos['Caraux']] += 1;
				$arrayPacientes[$rowInsumos['Carbot']][$rowInsumos['Caraux']][$rowInsumos['Carhis']."-".$rowInsumos['Caring']] += 1;
				$arrayFechas[$rowInsumos['Carbot']][$rowInsumos['Caraux']][$rowInsumos['Carhis']."-".$rowInsumos['Caring']][$rowInsumos['Carfec']] += 1;
				
				
				
				$arrayInsumos[$idInsumo]['codBotiquin'] = $rowInsumos['Carbot'];
				$arrayInsumos[$idInsumo]['codAuxiliar'] = $rowInsumos['Caraux'];
				$arrayInsumos[$idInsumo]['historia'] = $rowInsumos['Carhis'];
				$arrayInsumos[$idInsumo]['ingreso'] = $rowInsumos['Caring'];
				$arrayInsumos[$idInsumo]['codInsumo'] = $rowInsumos['Carins'];
				$arrayInsumos[$idInsumo]['fecha'] = $rowInsumos['Carfec'];
				$arrayInsumos[$idInsumo]['cantCargada'] = $rowInsumos['Carcca'];
				$arrayInsumos[$idInsumo]['cantAplicada'] = $rowInsumos['Carcap'];
				$arrayInsumos[$idInsumo]['cantDevuelta'] = $rowInsumos['Carcde'];
				$arrayInsumos[$idInsumo]['descInsumo'] = trim($rowInsumos['Artgen']);
				$arrayInsumos[$idInsumo]['desBotiquin'] = strtoupper(consultarDescripcionCco($rowInsumos['Cconom']));
				$arrayInsumos[$idInsumo]['auxiliar'] = strtoupper($rowInsumos['Descripcion']);
				$arrayInsumos[$idInsumo]['empresa'] = $rowInsumos['Empresa'];
				$arrayInsumos[$idInsumo]['cco'] = $rowInsumos['Ubisac'];
				$arrayInsumos[$idInsumo]['ccoPaciente'] = strtoupper($rowInsumos['Cconom']);
				$arrayInsumos[$idInsumo]['habitacion'] = strtoupper($rowInsumos['Ubihac']);
				$arrayInsumos[$idInsumo]['habitacionAnterior'] = strtoupper($rowInsumos['Ubihan']);
				$arrayInsumos[$idInsumo]['altaDefinitiva'] = $rowInsumos['Ubiald'];
				$arrayInsumos[$idInsumo]['nombrePaciente'] = strtoupper($rowInsumos['Pacno1']." ".$rowInsumos['Pacno2']." ".$rowInsumos['Pacap1']." ".$rowInsumos['Pacap2']);
				$arrayInsumos[$idInsumo]['permiteDevolver'] = $rowInsumos['Cartur'] > 0 ? false : true;
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
		
		$arrayBotiquines = consultarBotiquines();
		
		$fotoAuxiliares = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fotoAuxiliares');
		
		$colspanResponsable=2;
		if($fotoAuxiliares=="on")
		{
			$colspanResponsable=3;
		}
		
		
		$html = "";
		$html .= "<div id='divBotiquin'>
					<div id='tableInsumos'>";
		if(count($arrayCco)>0)
		{
			foreach($arrayCco as $keyCco => $valueCco)
			{
				$html .= "
							<span  id='inventarioBotiquin' onclick='abrirModalInventarioBotiquin();' style='font-family: verdana;font-weight:bold;font-size: 10pt;align:right;right:300px;position: absolute;color: #0033FF;text-decoration: underline;cursor:pointer;'>
								Inventario botiqu&iacute;n
							</span>
							<br>
							<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
								Buscar responsable:&nbsp;&nbsp;</b><input id='buscarAuxiliar' type='text' placeholder='Nombre del responsable' style='border-radius: 4px;border:1px solid #AFAFAF;'>
							</span>
							<span  id='insumosTransitoBotiquin' onclick='abrirModalInsumosBotiquin();' style='font-family: verdana;font-weight:bold;font-size: 10pt;align:right;right:300px;position: absolute;color: #0033FF;text-decoration: underline;cursor:pointer;'>
								Insumos en tr&aacute;nsito
							</span>
							<br>
							
						<div id='accordion' class='desplegable'>
							<h3 align='left'>&nbsp;&nbsp;&nbsp;&nbsp;BOTIQUIN: ".$arrayBotiquines[$codBotiquin]."</h3>
							<div id='divCco".$codBotiquin."'>
								<table id='tableCco".$codBotiquin."' width='100%'>
									<tr class='encabezadoTabla' align='center'>
										<td colspan='".$colspanResponsable."'>Responsable</td>
										<td colspan='3'>Paciente</td>
										<td colspan='8'>Detalle del cargo</td>
									</tr>
									<tr class='encabezadoTabla' align='center'>";
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
										<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
									</tr>";
									
									$conAuxiliar = 0;
									$auxAnterior = "";
									$pacienteAnterior = "";
									$fechaAnterior = "";
									$rowspanAuxiliar = 1;
									$rowspanPacientes = 1;
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
											
											
											// ------------------------------------------
											// Tooltip
											// ------------------------------------------	
												
											$tooltipCargados = "<div id=\"dvTooltipCargados\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades cargadas</div>";
											$tooltipAplicados = "<div id=\"dvTooltipAplicados\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades aplicadas</div>";
											$tooltipDevueltos = "<div id=\"dvTooltipDevueltos\" style=\"font-family:verdana;font-size:10pt\">Ver detalle de cantidades devueltas</div>";
											
											$funcionCantidadCargada = "onclick='verDetalleCantidades(\"CR\",\"".$valueInsumo['codBotiquin']."\",\"".$valueInsumo['codAuxiliar']."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";
											$funcionCantidadAplicada = "onclick='verDetalleCantidades(\"AP\",\"".$valueInsumo['codBotiquin']."\",\"".$valueInsumo['codAuxiliar']."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";
											$funcionCantidadDevuelta = "onclick='verDetalleCantidades(\"DV\",\"".$valueInsumo['codBotiquin']."\",\"".$valueInsumo['codAuxiliar']."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['descInsumo']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\",\"".$valueInsumo['turno']."\");'";
											
											$funcionDevolucion = "onclick='devolverInsumos(\"".$valueInsumo['codBotiquin']."\",\"".$valueInsumo['codAuxiliar']."\",\"".$valueInsumo['codInsumo']."\",\"".$valueInsumo['fecha']."\",\"".$valueInsumo['historia']."\",\"".$valueInsumo['ingreso']."\",\"".$hab."\",\"".$valueInsumo['cco']."\");'";
											if( !$valueInsumo['permiteDevolver'] ){
												$funcionDevolucion = "";
											}
											
											
											$saldoInsumo = $valueInsumo['cantCargada']-$valueInsumo['cantAplicada']-$valueInsumo['cantDevuelta'];
											$fondoSaldo = "";
											if($saldoInsumo>0)
											{
												$fondoSaldo = "class='fondoAmarillo'";
											}
											
											
											$rowspanAuxiliar = $arrayAuxiliares[$keyCco][$valueInsumo['codAuxiliar']];
											$rowspanPacientes = $arrayPacientes[$keyCco][$valueInsumo['codAuxiliar']][$valueInsumo['historia']."-".$valueInsumo['ingreso']];
											$rowspanFecha = $arrayFechas[$keyCco][$valueInsumo['codAuxiliar']][$valueInsumo['historia']."-".$valueInsumo['ingreso']][$valueInsumo['fecha']];
											
											$conAuxiliar=0;
											if($auxAnterior===$valueInsumo['codAuxiliar'])
											{
												$conAuxiliar++;
											}
											
											$conPaciente=0;
											if($pacienteAnterior===$valueInsumo['historia']."-".$valueInsumo['ingreso'])
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
												
												$fila_rowspanFecha = $fila_lista;
												$fila_rowspan = $fila_lista;
											
												$auxAnterior = $valueInsumo['codAuxiliar'];	
												$pacienteAnterior = $valueInsumo['historia']."-".$valueInsumo['ingreso'];
												$fechaAnterior = $valueInsumo['fecha'];
												
												
												
			$html .= 						"</tbody>";										
			$html .= 						"<tbody id='auxiliar_".$valueInsumo['codAuxiliar']."' class='find'>";									
			$html .= "							<tr class='".$fila_lista."'>";
												if($fotoAuxiliares=="on")
												{
													$longitud = strlen($valueInsumo['codAuxiliar']);
													$codigoAuxiliar = $valueInsumo['codAuxiliar'];
													if($longitud>5)
													{
														$codigoAuxiliar = substr($valueInsumo['codAuxiliar'],$longitud-5,$longitud);
													}
													
													$urlFotoAuxiliar = consultarFoto($conex,$wemp_pmla,$wbasedato,$codigoAuxiliar,$valueInsumo['empresa']);
													
													// ".consultarFoto($conex,$wemp_pmla,$wbasedato,$codigoAuxiliar,$valueInsumo['empresa'])."
			$html .= "								<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanAuxiliar."' width='65px'>
														<img class='lightbox' id='fotoAuxiliar_".$valueInsumo['codAuxiliar']."' src='".$urlFotoAuxiliar."' width=65px height=75px>
														<img class='fotoAuxiliar_".$valueInsumo['codAuxiliar']."' id='fotoAuxiliar_Apliada".$valueInsumo['codAuxiliar']."' src='".$urlFotoAuxiliar."' style='display:none'  height='700px' />
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
													<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPacientes."' ".$funcionDevolucion." style='cursor:pointer;color: #0033FF;font-weight:bold;font-family: verdana;font-size: 10pt;'>".( $valueInsumo['permiteDevolver'] ? "Devolver" : "" )."</td>
												</tr>";	
											}
											else
											{
												if($conPaciente==0)
												{
													$pacienteAnterior = $valueInsumo['historia']."-".$valueInsumo['ingreso'];
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
														<td class='".$fila_rowspan."' align='center' rowspan='".$rowspanPacientes."' ".$funcionDevolucion." style='cursor:pointer;color: #0033FF;font-weight:bold;font-family: verdana;font-size: 10pt;'>".( $valueInsumo['permiteDevolver'] ? "Devolver" : "" )."</td>
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
			$html .= "<p align='center'>
						<span  id='inventarioBotiquin' onclick='abrirModalInventarioBotiquin();' style='font-family: verdana;font-weight:bold;font-size: 10pt;align:center;color: #0033FF;text-decoration: underline;cursor:pointer;'>
							Inventario botiqu&iacute;n
						</span>
						<br><br>
					 <b>El botiqu&iacuten no tiene insumos en tr&aacute;nsito.</b>
					 </p>";
		}
		$html .= "</table>";
		$html .= "</div>";
		
		return $html;		
	}
	
	function pintarModalDevolucionInsumos($wemp_pmla,$wbasedato,$botiquin,$auxiliar,$insumo,$fecha,$historia,$ingreso,$habitacion,$cco)
	{
		global $conex;
		global $wuse;
		
		
		$insumos = consultarInsumosConSaldo($conex,$wbasedato,$botiquin,$auxiliar,"","",$historia,$ingreso);
		
		$colspanTabla = "6";
		$modal = "";
		
		$modal .= "	<div id='modalDevolucion'>
						
						<br>
						
						<p align='center' style='font-size:18pt;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Responsable de insumos: ".consultarDescripcionUsuario($auxiliar)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>
						<p align='center' style='font-size:18pt;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Botiqu&iacute;n: ".consultarDescripcionCco($botiquin)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>
						<p align='center' style='font-size:8pt;font-weight:bold;'><span><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Devoluciones realizadas por: ".consultarDescripcionUsuario($wuse)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>
						
						<br>
						<fieldset style='padding:5px;margin:5px;border: 2px solid #2a5db0;position:relative;width:67%;left:15%;' align='center'>
							<legend style='border: 2px solid #2a5db0;border-top: 0px;font-family: Verdana;color: #ffffff;background-color: #2a5db0;font-size:8pt;font-weight:bold;'> Buscar insumos a devolver: </legend>
						
							<table class='ccmb' id='tablaBuscadorInsumos' align='center' width='33%'>
								<tr>
									<td class='encabezadoTabla' align='center'>INSUMOS</td>
								</tr>
								<tr>
									<td class='fila1'>Buscar: <input type='text' id='txtInsumo' size='30' onkeypress='return validarEnter(this,event);'></td>
								</tr>
								<tr>
									<td class='fila1'>
										<div id='dvMsgAlertas' class='fondoamarillo' style='display:none;width:100%;'>
											<div style='display:table-row;'>
												<div style='display:table-cell;padding:5px;vertical-align:middle;'>
													<img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17>
												</div>
												<div id='msgInsumos' style='display:table-cell;padding:5px;'></div>
											</div>
										</div>
									</td>
								</tr>
							</table>
							<br>
						</fieldset>
						
						<br><br>
						
						<table id='tablaDevoluciones' align='center' width='70%'>
							<tr class='encabezadoTabla' align='center'>
								<td colspan='".$colspanTabla."'>DEVOLUCIONES</td>
							</tr>
							
							<tr class='encabezadoTabla'>
								<td colspan='".$colspanTabla."' align='center'>DATOS DEL PACIENTE</td>
							</tr>
							
							<tr>
								<td class='fila1'>Historia</td>
								<td class='fila2' colspan='1'>".$historia."-".$ingreso."</td>
								<td class='fila1' colspan='1'>Nombre</td>
								<td class='fila2' colspan='3'>".consultarNombrePaciente($historia)."</td>
							</tr>
							
							<tr>
								<td class='fila1'>Habitaci&oacute;n actual</td>
								<td class='fila2' colspan='1'>".$habitacion."</td>
								<td class='fila1' colspan='1'>Centro de costos actual</td>
								<td class='fila2' colspan='3'>".$cco."</td>
							</tr>
							
							<tr class='encabezadoTabla' align='center'>
								<td colspan='".$colspanTabla."'>INSUMOS A DEVOLVER</td>
							</tr>
							<tr class='encabezadoTabla' align='center'>
								<td>Fecha</td>
								<td>C&oacute;digo</td>
								<td>Descripci&oacute;n</td>
								<td>Saldo</td>
								<td>Cantidad a<br>devolver</td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							</tr>";
							
							$arrayFechas = array();
							foreach($insumos as $keyInsumo => $valueInsumos)
							{
								
								$lotesHtml = "";
								$lotes = consultarListaLotesCargadosPorAuxiliar( $conex, 'cliame', $valueInsumos['insumo'], $historia, $ingreso, 'on', $auxiliar );
								
								if( count( $lotes ) > 0 ){
									
									$lotesHtml .= "<b>Lote:  </b><select class='fondorojo' name='lote' onchange='validarLote( this, \"cantDevolucion_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."\")'><option value=''>Seleccione...</option>";
									
									foreach( $lotes as $key => $value ){
										$lotesHtml .= "<option data-cantidad='".$value['can']."' value='".$value['lote']."'>".$value['lote']."</option>";
									}
									
									$lotesHtml .= "</select>";
								}
								
								$saldo = (int)$valueInsumos['saldo'];
								
								if($saldo>0)
								{
									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";
									
									
									$arrayFechas[$valueInsumos['fecha']] += 1;
									
		$modal .= "					<tr class='".$fila_lista."' align='center'>
										<td id='fecha_".$valueInsumos['insumo']."' name='fecha_".$valueInsumos['insumo']."'>".$valueInsumos['fecha']."</td>
										<td>".$valueInsumos['insumo']."</td>
										<td>".$valueInsumos['descripcion']."</td>
										<td><span id='saldo_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."' name='saldo_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."'>".$saldo."</span></td>
										<td>
											<input type='text' id='cantDevolucion_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."' name='cantDevolucion_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."' value='' size='3' onkeypress='return soloNumeros(event);' onblur='validarCantidad(\"".$valueInsumos['insumo']."\",\"".$valueInsumos['fecha']."\");'> ".$valueInsumos['unidad']."
											$lotesHtml
										</td>
										<td>
											<span id='insumoNoRegistrado_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."' name='insumoNoRegistrado_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."' style='display:none;cursor:pointer;' title=''><img src='/matrix/images/medical/sgc/Mensaje_alerta.png' height=17 width=17></span>
											<span id='insumoRegistrado_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."' name='insumoRegistrado_".$valueInsumos['insumo']."_".$valueInsumos['fecha']."' style='display:none;cursor:pointer;' title=''><img src='/matrix/images/medical/movhos/checkmrk.ico' height=17 width=17></span>
										</td>
									</tr>";	
								}
							}
							
							$cadenaFechas = "";
							foreach($arrayFechas as $fecha => $cantidad)
							{
								$cadenaFechas .= $fecha."_".$cantidad."|";
							}
							$cadenaFechas = substr($cadenaFechas, 0, -1);
							
							
		$modal .= "			<input type='hidden'  id='cadenaFechas' name='cadenaFechas' value='".$cadenaFechas."'>
						</table>
						
						
					<br><br>
					<span><input type='button' id='botonDevolverInsumos' value='Devolver insumos' onclick='registrarDevolucion(\"".$botiquin."\",\"".$auxiliar."\",\"".$historia."\",\"".$ingreso."\",\"".$habitacion."\",\"".$cco."\");'></span>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span>
					<br><br>
					</div>";
		
		
		return $modal;
		
	}
	
	function consultarDescripcionUsuario($codUsuario)
	{
		global $conex;
		global $wbasedato;
		
		$queryUsuarios =  " SELECT Descripcion 
								FROM usuarios
							   WHERE Codigo='".$codUsuario."' 
							     AND Activo='A';";
		
		$resUsuarios = mysql_query($queryUsuarios, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryUsuarios . " - " . mysql_error());		   
		$numUsuarios = mysql_num_rows($resUsuarios);
		
		$usuario = "";
		if($numUsuarios>0)
		{
			$rowUsuarios = mysql_fetch_array($resUsuarios);
			$usuario = $rowUsuarios['Descripcion'];
		}
		
		return $usuario;
	}

	
	function consultarDescripcionCco($codBotiquin)
	{
		global $conex;
		global $wbasedato;
		
		$queryBotiquin =  "   SELECT Cconom 
								FROM ".$wbasedato."_000011
							   WHERE Ccocod='".$codBotiquin."' 
							     AND Ccoest='on';";
		
		$resBotiquin = mysql_query($queryBotiquin, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryBotiquin . " - " . mysql_error());		   
		$numBotiquin = mysql_num_rows($resBotiquin);
		
		$botiquin = "";
		if($numBotiquin>0)
		{
			$rowBotiquin = mysql_fetch_array($resBotiquin);
			$botiquin = $rowBotiquin['Cconom'];
		}
		
		return $botiquin;
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
	
	function registrarDevolucionInsumos($wemp_pmla,$wbasedato,$codMovimiento,$botiquin,$auxiliar,$insumosDevolucion,$historia,$ingreso,$habitacion,$cco)
	{
		global $conex;
		global $wuse;
		
		$arrayResultado = array();
		$arrayResultado = registrarDevolucion($conex,$wemp_pmla,$wbasedato,$codMovimiento,$botiquin,$auxiliar,$insumosDevolucion,$historia,$ingreso,$habitacion,$cco,$wuse);
			
		return $arrayResultado;
	}
	
	function consultarInsumosTransito($wbasedato,$botiquin)
	{
		global $conex;
		
		$queryInsumosTransito = " SELECT Carins,Artgen,SUM(Carcca) as Carcca,SUM(Carcap) as Carcap,SUM(Carcde) as Carcde,(SUM(Carcca)-SUM(Carcap)-SUM(Carcde)) as Saldo
									FROM ".$wbasedato."_000227,".$wbasedato."_000026
								   WHERE Carbot='".$botiquin."'
									 AND Cartra='on'
									 AND Carest='on'
									 AND Artcod=Carins
								GROUP BY Carins	 
								ORDER BY Artgen;";
		
		$resInsumosTransito = mysql_query($queryInsumosTransito, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumosTransito . " - " . mysql_error());		   
		$numInsumosTransito = mysql_num_rows($resInsumosTransito);
		
		$arrayInsumos = array();
		if($numInsumosTransito>0)
		{
			while($rowInsumosTransito = mysql_fetch_array($resInsumosTransito))
			{
				$arrayInsumos[$rowInsumosTransito['Carins']]['codigo'] = $rowInsumosTransito['Carins'];
				$arrayInsumos[$rowInsumosTransito['Carins']]['descripcion'] = $rowInsumosTransito['Artgen'];
				$arrayInsumos[$rowInsumosTransito['Carins']]['cantCargada'] = $rowInsumosTransito['Carcca'];
				$arrayInsumos[$rowInsumosTransito['Carins']]['cantAplicada'] = $rowInsumosTransito['Carcap'];
				$arrayInsumos[$rowInsumosTransito['Carins']]['cantDevuelta'] = $rowInsumosTransito['Carcde'];
				$arrayInsumos[$rowInsumosTransito['Carins']]['saldo'] = $rowInsumosTransito['Saldo'];
			}
		}
		
		return $arrayInsumos;
	}
	
	function pintarModalInsumosTransito($wemp_pmla,$wbasedato,$botiquin)
	{
		$insumosTransito = consultarInsumosTransito($wbasedato,$botiquin);
		
		$modal = "";
		$modal .= "	<div id='insumosTransito'><br>
						<p align='center' style='font-size:18pt;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Botiqu&iacute;n: ".consultarDescripcionCco($botiquin)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>";
		
		if(count($insumosTransito)>0)
		{
			$modal .= "
						<input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span><br><br>";
			$modal .= "	<table id='tableInsumosTransito' align='center'>
							<tr class='encabezadoTabla' align='center'>
								<td colspan='6' style='font-size: 12pt'>INSUMOS EN TR&Aacute;NSITO</td>
							</tr>
							<tr class='encabezadoTabla' align='center'>
								<td>C&oacute;digo</td>
								<td>Descripci&oacute;n</td>
								<td>Cantidad<br>cargada</td>
								<td>Cantidad<br>aplicada</td>
								<td>Cantidad<br>devuelta</td>
								<td>Saldo</td>
							</tr>";
			
			foreach($insumosTransito as $keyInsumo => $valueInsumo)
			{
				$saldo = (int)$valueInsumo['saldo'];
				
				if($saldo>0)
				{
					if ($fila_lista=='Fila1')
						$fila_lista = "Fila2";
					else
						$fila_lista = "Fila1";
					
					
				
			$modal .= "		<tr class='".$fila_lista."'>
								<td>".$valueInsumo['codigo']."</td>
								<td>".$valueInsumo['descripcion']."</td>
								<td align='center'>".$valueInsumo['cantCargada']."</td>
								<td align='center'>".$valueInsumo['cantAplicada']."</td>
								<td align='center'>".$valueInsumo['cantDevuelta']."</td>
								<td align='center' class='fondoAmarillo'>".$valueInsumo['saldo']."</td>
							</tr>";
				}							
			}
			$modal .= "</table>";
		}
		else
		{
			$modal .= "<p align='center'><b>El botiqu&iacuten no tiene insumos en tr&aacute;nsito.</b></p>";
		}
		$modal .= "	<br><br>
					<span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span>
					<br><br>
					</div>";
		
		
		return $modal;
		
	}
	
	function pintarModalInventarioBotiquin($wemp_pmla,$wbasedato,$botiquin)
	{
		global $conex;
		
		$insumosBotiquin = consultarInsumosBotiquin($wbasedato,$botiquin);
		
		$modal = "";
		$modal .= "	<div id='insumosBotiquin'><br>
						<p align='center' style='font-size:18pt;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Botiqu&iacute;n: ".consultarDescripcionCco($botiquin)."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>";
		
		if(count($insumosBotiquin)>0)
		{
			$modal .= "<span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span>
						<br><br>";
			$modal .= "	<table id='tableInsumosBotiquin' align='center'>
			
							<tr align='left'>
								<td colspan='5'>
									<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
										Buscar insumo:&nbsp;&nbsp;</b><input id='buscarInsumo' type='text' placeholder='Insumo' style='border-radius: 4px;border:1px solid #AFAFAF;'>
									</span>
								</td>
							</tr>
							<tr class='encabezadoTabla' align='center'>
								<td colspan='5' style='font-size: 12pt'>INVENTARIO BOTIQU&Iacute;N</td>
							</tr>
							<tr class='encabezadoTabla' align='center'>
								<td>C&oacute;digo</td>
								<td>Descripci&oacute;n</td>
								<td>Saldo<br>Unix</td>
								<td>Saldo en<br>tr&aacute;nsito</td>
								<td>Saldo<br>disponible</td>
							</tr>";
			
			foreach($insumosBotiquin as $keyInsumo => $valueInsumo)
			{
				$saldo = (int)$valueInsumo['saldoUnix'];
				
				// if($saldo>=0)
				if($saldo>0 || (int)$valueInsumo['saldoEnTransito']>0 || (int)$valueInsumo['saldoDisponible']>0)
				{
					if ($fila_lista=='Fila1')
						$fila_lista = "Fila2";
					else
						$fila_lista = "Fila1";
					
					
				
			$modal .= "		<tr class='".$fila_lista." find'>
								<td>".$valueInsumo['codigo']."</td>
								<td>".$valueInsumo['nombreGenerico']."</td>
								<td align='center'>".$valueInsumo['saldoUnix']."</td>
								<td align='center'>".$valueInsumo['saldoEnTransito']."</td>
								<td align='center' class='fondoAmarillo'>".$valueInsumo['saldoDisponible']."</td>
							</tr>";
				}							
			}
			$modal .= "</table>";
		}
		else
		{
			$modal .= "<p align='center'><b>El botiqu&iacuten no tiene insumos.</b></p>";
		}
		$modal .= "	<br><br>
					<span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span>
					<br><br>
					</div>";
		
		
		return $modal;
		
	}
	
	function consultarInsumosBotiquin( $wbasedato, $cco )
	{
	
		global $conex;
		
		$insumosEnTransito = consultarInsumosTransito($wbasedato,$cco);
		
		$queryInventario = "SELECT artcod, artcom, artgen, artuni, unides, artgru, melgru, meltip, Salant + Salent - Salsal as saldo
							  FROM ".$wbasedato."_000026 
				   LEFT OUTER JOIN ".$wbasedato."_000066
								ON melgru = SUBSTRING_INDEX( artgru, '-', 1 )
							   AND meltip = 'M'
							   AND melest = 'on',
								   ".$wbasedato."_000027,
								   ".$wbasedato."_000141
							 WHERE artest = 'on'
							   AND unicod = artuni
							   AND salart = artcod
							   AND salser = '".$cco."'
						  ORDER BY Artgen;";	
			  
		
		$resInventario = mysql_query($queryInventario, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInventario . " - " . mysql_error());		   
		$numInventario = mysql_num_rows($resInventario);
		
		$arrayInsumos = array();
		if($numInventario>0)
		{
			while($rowInventario = mysql_fetch_array($resInventario))
			{
				if((empty( $rowInventario['melgru'] ) || $rowInventario['melgru'] == 'E00' ) && !empty($rowInventario['artcom']))
				{
					
					$saldo = $rowInventario['saldo'];
					$saldoTransito = $insumosEnTransito[$rowInventario['artcod']]['saldo'];
					
					if(!isset($saldoTransito))
					{
						$saldoTransito = 0;
					}
					
					$saldoDisponible = (int)$saldo-(int)$saldoTransito;
					
					$arrayInsumos[$rowInventario['artcod']]['codigo'] = $rowInventario['artcod'];
					$arrayInsumos[$rowInventario['artcod']]['nombreGenerico'] = $rowInventario['artgen'];
					$arrayInsumos[$rowInventario['artcod']]['unidad'] = $rowInventario['artuni'];
					$arrayInsumos[$rowInventario['artcod']]['descUnidad'] = $rowInventario['unides'];
					$arrayInsumos[$rowInventario['artcod']]['saldoUnix'] = $saldo;
					$arrayInsumos[$rowInventario['artcod']]['saldoEnTransito'] = $saldoTransito;
					$arrayInsumos[$rowInventario['artcod']]['saldoDisponible'] = $saldoDisponible;
					
				}
			}
		}
			
		return $arrayInsumos;
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
		case 'consultarBotiquines':
		{	
			$data = consultarBotiquines();
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarBotiquin':
		{	
			$data = pintarInsumosPorAuxiliar($codBotiquin);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarModalDevolucion':
		{	
			$data =  pintarModalDevolucionInsumos($wemp_pmla,$wbasedato,$botiquin,$auxiliar,$insumo,$fecha,$historia,$ingreso,$habitacion,$cco);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'registrarDevolucion':
		{
			$data = registrarDevolucionInsumos($wemp_pmla,$wbasedato,$codMovimiento,$botiquin,$auxiliar,$insumosDevolucion,$historia,$ingreso,$habitacion,$cco);
			echo json_encode($data);
			break;
			return;
		}
		case 'insumosTransitoBotiquin':
		{	
			$data = pintarModalInsumosTransito($wemp_pmla,$wbasedato,$botiquin);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'inventarioBotiquin':
		{	
			$data = pintarModalInventarioBotiquin($wemp_pmla,$wbasedato,$botiquin);
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
	  <title>MONITOR BOTIQUIN</title>
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
		
	$(document).ready(function () {
		
		inicializarAccordion();
		inicializarTooltip();
		inicializarBuscador();
		
	});
	
	
	function validarLote( cmpSelect, cmpInput ){
		console.log( cmpSelect );
		console.log(  $( "#"+cmpInput ) );
		var val = true;
		
		if( $( cmpSelect ).val() != '' ){
		
			var optSelected = $( "option:selected", cmpSelect );
			var canMaxima	= $( optSelected ).data( "cantidad" );
			
			if( canMaxima < $( "#"+cmpInput ).val()*1 ){
				msg = "La cantidad a devolver no puede ser mayor a "+canMaxima;
				jAlert( msg, "ALERTA", function(){
					$( "#"+cmpInput ).val( '' )
				});
			}
		}
		
		return val;
	}
	
	//Actualizar cada dos minutos
	setInterval(function()
	{
		seleccionarBotiquin();
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
	
	
	function cargarAutocompleteInsumos(wemp_pmla,botiquin)
	{
		$( "#txtInsumo" ).autocomplete({
			source		: function( request, response ){
				_self = this;
				$.ajax({
					url		: "./botiquinDispensacionInsumos.php?wemp_pmla="+wemp_pmla+"&bot="+botiquin+"",
					type	: "POST",
					dataType: "json",
					data	: {
						consultaAjax: 'consultarInsumo',
						term		: request.term,
						validarSaldo: false,
					},
					async	: true,
					success	: function( respuesta ){
						
						// if( respuesta.error == 0 ){
							// response( respuesta.data );
						// }
						// else{
							// console.log( respuesta.message )
						// }
						// ----------------------------------
						var data = [];
						for( var idx  in respuesta.data ){
							data.push( respuesta.data[idx] );
						}
						
						if( data.length > 0 )
						{
							response( data );
							$( "#msgInsumos" ).html("");
							$( "#dvMsgAlertas" ).hide();
						}
						else if( $( "#txtInsumo" )[0].enterPress ){
							$( "#txtInsumo" ).val('');
							generarSonidoAlerta();
							
							var msg = "No se encontr&oacute; el art&iacute;culo";
							// jAlert(msg,"ALERTA");
							
							$( "#msgInsumos" ).html(msg);
							$( "#dvMsgAlertas" ).show();
							
							
							$( "#txtInsumo" ).focus();
							
							response( data );
						}
						else
						{
							response( [] );
						}
					}
				});
			},
			minLength	: 3,
			delay		: 300,
			autoFocus	: true,
			autoFill	: true,
			open		: function(event, ui){
				
				var enterPresionado = this.enterPress;				//this en este caso es $( "#txtInsumo" )
				var options 		= $(this).data("autocomplete");	//options es el objeto autocomplete asociado a $( "#txtInsumo" )
				var numOptions		= $( "li", options.menu.element[0] ).length;
				ttt = options;
				//Si el total de resultados es uno se deja la opción mostrada como seleccionada
				if( numOptions == 1 ){
					
					var opt = $( "li", options.menu.element[0] ).eq(0);
					
					var data = opt.data( "item.autocomplete" );	//Esto es un item de source
					data.preEnter = false;
					
					setTimeout(function(){
							//Event crea un evento artificial
							options.menu.activate( $.Event( 'click' ), opt );
							
							if( enterPresionado ){
								data.preEnter = true;
								options.menu.select( $.Event('click'), { item: opt });
							}
						},
						10
					);
				}
			},
			select		: function( event, ui ){
				agregarCantidadInsumo( ui.item );
				setTimeout(function(){ 
					$( "#txtInsumo" ).val('') 
					$( "#txtInsumo" ).change() 
				}, 200 );
				
				this.enterPress = false;
			},
			change: function( event, ui ) {
				if ( !ui.item ) {
					if(ui.item!==undefined)
					{
						// generarSonidoAlerta();
						// // jAlert("No puede devolver un insumo que no ha sido cargado o no tiene saldo.","ALERTA");
						// msg="No puede devolver un insumo que no ha sido cargado o no tiene saldo.";
							
						// $( "#msgInsumos" ).html(msg);
						// $( "#dvMsgAlertas" ).show();
						
						
						setTimeout(function(){ 
							$( "#txtInsumo" ).val("");
							$( "#txtInsumo" ).focus();
						}, 200 );
						
					}
				}
			}
		}).keypress(function( event ){
			if( event.which == 13 )
			{
				this.enterPress = true;
			}
			else
			{
				this.enterPress = false;
			}
				
		});
		
		
		// $("#txtInsumo").blur();
	}
	
	function validarInsumoDevolucion(articulo)
	{
		var articuloDevolucion = false;
		$( "[id^=cantDevolucion_"+articulo+"]").each(function(){
			
			articuloDevolucion = true;
			
		});
		
		return articuloDevolucion;
	}
	
	function agregarCantidadInsumo(articulo)
	{
		var cant = "";
		var saldo = "";
		var id = "";
		
		$( "#tablaDevoluciones input" ).removeClass('fondoamarillo');
		
		
		$( "[id^=cantDevolucion_"+articulo.codigo+"]").each(function(){
			
			var idCantidad = $(this).attr('id');
			id = idCantidad.replace("cantDevolucion_","");
			
			cant = $("#cantDevolucion_"+id).val();
			saldo = $("#saldo_"+id).html();
			
			cant = parseInt(cant);
			saldo = parseInt(saldo);
			
			
			if(cant<saldo || isNaN(cant))
			{
				return false;
			}
			
		});
		
		if(cant!="")
		{
			if(cant>=saldo)
			{
				generarSonidoAlerta();
				// jAlert("No puede devolver una cantidad superior al saldo","ALERTA");
				
				msg = "No puede devolver una cantidad superior al saldo";
				
				$( "#msgInsumos" ).html(msg);
				$( "#dvMsgAlertas" ).show();
				// $( "#dvMsgAlertas" ).css({
							// display: "table",
						// });
				
				$("#cantDevolucion_"+id).val(saldo);
				$("#txtInsumo").focus();
			}
			else
			{
				if($("#cantDevolucion_"+id).val()=="")
				{
					$("#cantDevolucion_"+id).val(1);
				}
				else
				{
					$("#cantDevolucion_"+id).val(cant+1);
				}
				$("#cantDevolucion_"+id).addClass('fondoamarillo');
			}
		}
		else
		{
			generarSonidoAlerta();
			// jAlert("No puede devolver un insumo que no ha sido cargado o no tiene saldo.","ALERTA");
			msg="No puede devolver un insumo que no ha sido cargado o no tiene saldo.";
			
			$( "#msgInsumos" ).html(msg);
			$( "#dvMsgAlertas" ).show();
				
			$("#txtInsumo").focus();
		}
		
	}
	
	function generarSonidoAlerta()
	{
		if($("#audio_fb").length == 0)
		{
			var elemento_audio = '<audio id="audio_fb"><source src="../../images/medical/root/alerta_error.mp3" type="audio/mp3"></audio>';
			$( "body" ).append(elemento_audio);
		}
		$("#audio_fb")[0].play();
	}
	
	function validarCantidad(codInsumo,fecha)
	{
		var val = true;
		var msg = "";
		
		var cant = $("#cantDevolucion_"+codInsumo+"_"+fecha).val();
		var saldo = $("#saldo_"+codInsumo+"_"+fecha).html();
		
		var cmpSelect = $( "select[name=lote]", $( "#cantDevolucion_"+codInsumo+"_"+fecha ).parent() );
		
		if( cmpSelect.length > 0 ){
			
			var optSelected = $( "option:selected", cmpSelect );
			var canMaxima	= $( optSelected ).data( "cantidad" );
			
			if( canMaxima < cant ){
				msg = "La cantidad a devolver no puede ser mayor a "+canMaxima;
				jAlert( msg, "ALERTA", function(){
					$("#cantDevolucion_"+codInsumo+"_"+fecha).val( canMaxima );
					$("#cantDevolucion_"+codInsumo+"_"+fecha).select();
					$("#cantDevolucion_"+codInsumo+"_"+fecha).focus();
				});
				val = false;
			}
		}
		
		if(parseInt(cant)>parseInt(saldo))
		{
			val = false;
			msg = "No puede devolver una cantidad superior al saldo"
		}
		
		if( !val ){
			
			jAlert( msg , "ALERTA", function(){
				
				var saldoValido = $("#cantDevolucion_"+codInsumo+"_"+fecha).attr("saldoValido");
				$("#cantDevolucion_"+codInsumo+"_"+fecha).val(saldoValido);
				
				$("#cantDevolucion_"+codInsumo+"_"+fecha).select();
				$("#cantDevolucion_"+codInsumo+"_"+fecha).focus();
			});
		}
		else
		{
			$("#cantDevolucion_"+codInsumo+"_"+fecha).attr("saldoValido",cant);
		}
	}
	
	function soloNumeros(e){
		var key = window.Event ? e.which : e.keyCode;
		
		if(key == 13)
		{
			$("#txtInsumo").focus();
			return false;
		}
		
		return ((key >= 48 && key <= 57) || key<= 8);
	}
	
	function validarEnter(id,e){
		var key = window.Event ? e.which : e.keyCode;
		
		if(key == 13)
		{
			// $(id).blur();
			return false;
		}
	}
	
	function seleccionarBotiquin()
	{
		
		$.post("monitorInsumosBotiquin.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarBotiquin',
			codBotiquin		: $('#filtroBotiquin').val(),
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {
			
			$("#divMonitorBotiquin").html(data);
			
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
	
	function devolverInsumos(botiquin,auxiliar,insumo,fecha,historia,ingreso,habitacion,cco)
	{
		$.post("monitorInsumosBotiquin.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarModalDevolucion',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			botiquin		: botiquin,
			auxiliar		: auxiliar,
			insumo			: insumo,
			fecha			: fecha,
			historia		: historia,
			ingreso			: ingreso,
			habitacion		: habitacion,
			cco				: cco
		}
		, function(data) {
			
			$( "#dvAuxModalDevolucion" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalDevolucion" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalDevolucion" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalDevolucion" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalDevolucion" ).height();
			
			$.blockUI({ message: $('#modalDevolucion'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "80%",
				height	: "80%",
				left	: "10%",
				top		: '100px',
			} });
			
			
			cargarAutocompleteInsumos($('#wemp_pmla').val(),botiquin);
			
			ocultarTdFechas();
			
			
		},'json');
		
	}
	
	function ocultarTdFechas()
	{
		var cadenaFechas = $("#cadenaFechas").val();
		fechas = cadenaFechas.split("|");
		
		var fechaAnterior = "";
		for(var i=0;i<fechas.length;i++)
		{
			fec = fechas[i].split("_");
			
			fecha = fec[0];
			cant = fec[1];
			
			$('table[id=tablaDevoluciones] td[id^=fecha_]').each(function(){
				
				if($(this).html()==fecha)
				{
					if(fechaAnterior!=fecha)
					{
						$(this).attr("rowspan",cant);
						 fechaAnterior=fecha;
					}
					else
					{
						$(this).hide();
					}
				}
			});
		}
	}
	
	function registrarDevolucion(botiquin,auxiliar,historia,ingreso,habitacion,cco)
	{
		var insumosDevolucion = {}; 
		
		$('table[id=tablaDevoluciones] input[id^=cantDevolucion_]').each(function(){
		  
			if($(this).val()!=="" && $(this).val()!=="0")
			{
				var lote = "";
				var loteRequerido = false;
				
				var id = $(this).attr("id");
				codInsumo = id.replace("cantDevolucion_","");
				
				var slLote = $( "select[name=lote]", $( this ).parent() );
				
				if( slLote.length > 0 ){
					lote = slLote.val();
					
					if( lote == '' )
						loteRequerido = true;
				}
				
				
				var insumo = codInsumo.split("_");
				
				if( !insumosDevolucion[codInsumo] && !loteRequerido )
				{
					insumosDevolucion[codInsumo] = [];
					insumosDevolucion[codInsumo] = {
						codInsumo	: insumo[0],
						fecha	 	: insumo[1],
						cantidad	: $(this).val(),
						lote		: lote,
					}
				}
			}
			
			$(this).prop('disabled', true);
		});
		
		$("#txtInsumo").prop('disabled', true);
		$("#botonDevolverInsumos").prop('disabled', true);
		
		$.post("monitorInsumosBotiquin.php",
		{
			consultaAjax 		: '',
			accion				: 'registrarDevolucion',
			wemp_pmla			: $('#wemp_pmla').val(),
			wbasedato			: $('#wbasedato').val(),
			codMovimiento		: "DV",
			botiquin			: botiquin,
			auxiliar			: auxiliar,
			insumosDevolucion	: insumosDevolucion,
			historia			: historia,
			ingreso				: ingreso,
			habitacion			: habitacion,
			cco					: cco
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
			
		},'json');
		
	}
	
	function abrirModalInsumosBotiquin()
	{
		$.post("monitorInsumosBotiquin.php",
		{
			consultaAjax 	: '',
			accion			: 'insumosTransitoBotiquin',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			botiquin		: $('#filtroBotiquin').val()
		}
		, function(data) {
			
			$( "#dvAuxModalInsumosTransito" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalInsumosTransito" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalInsumosTransito" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalInsumosTransito" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalInsumosTransito" ).height();
			
			$.blockUI({ message: $('#insumosTransito'),
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
	
	function abrirModalInventarioBotiquin()
	{
		
		var codBotiquin = $('#filtroBotiquin').val();
		
		//Actualizando saldos de unix en la tabla movhos_000141
		$.ajax({
			url		: "./proceso_saldosUnix.php",
			type	: "POST",
			async	: true,
			data: {
				cco: codBotiquin
			},
		});
		
		$.post("monitorInsumosBotiquin.php",
		{
			consultaAjax 	: '',
			accion			: 'inventarioBotiquin',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			botiquin		: codBotiquin
		}
		, function(data) {
			
			$( "#dvAuxModalInventario" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalInventario" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalInventario" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalInventario" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalInventario" ).height();
			
			$.blockUI({ message: $('#insumosBotiquin'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "80%",
				height	: "80%",
				left	: "10%",
				top		: '100px',
			} });
			
			$('#buscarInsumo').quicksearch('#tableInsumosBotiquin .find');
			
		},'json');
	}
	
	function cerrarModal()
	{
		$.unblockUI();
		seleccionarBotiquin();
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
	encabezado("MONITOR BOTIQUIN", $wactualiz, 'clinica');
	
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	
	
	
	$codBotiquin = "";
	if(!empty($wcco))
	{
		$codBotiquin = $wcco;
	}
	
	$filtroBotiquin = pintarFiltroBotiquin($codBotiquin);
	echo $filtroBotiquin;
	
	if(!empty($wcco))
	{
		echo "<script>seleccionarBotiquin();</script>";
	}
	
	// $codBotiquin = "";
	
	// $filtroBotiquin = pintarFiltroBotiquin($codBotiquin);
	// echo $filtroBotiquin;
	
	
	echo "<div id='divMonitorBotiquin'></div>";
	echo "<div id='dvAuxModalDetalleInsumos' style='display:none'></div>";
	echo "<div id='dvAuxModalDevolucion' style='display:none'></div>";
	echo "<div id='dvAuxModalInsumosTransito' style='display:none'></div>";
	echo "<div id='dvAuxModalInventario' style='display:none'></div>";
	
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
