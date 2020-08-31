<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Reporte de alertas y alergias por paciente
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2016-12-05	
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2017-01-25';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2017-01-25	Jessica Madrid Mejía 	- Se modifica el reporte para consultar las alertas y alergias por determinada historia e ingreso y 
// 										  asi retornar un html para la construccion del pdf de impresion de  formularios HCE y reportes
// 										  (solimp.php y HCE_Impresion.php)             
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
	// 

	// $conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function pintarBuscador()
	{
		$arrayTipoDoc = consultarTipoDocumento();
		$buscadorPaciente = "";
		// position:relative;width:50%;left:30%;
		$buscadorPaciente .= "	<div id='divBuscadorPaciente' align='center'>
									<fieldset align='center' style='padding:5px;margin:5px;border: 2px solid #2a5db0;width:33%'>
										<legend style='border: 2px solid #2a5db0;border-top: 0px;font-family: Verdana;color: #ffffff;background-color: #2a5db0;font-size:8pt;font-weight:bold;'> Buscar paciente: </legend>
										<table>
											<tr>
												<td>
													<select id='buscadorPaciente' onchange='cambiarFiltroBuscador();'>
														<option value='his'>Historia</option>
														<option value='doc'>Documento</option>
													<select>
												</td>
												<td>
													<input type='text' id='filtroBuscadorPaciente'>
													<select id='tipoDocumento' style='display:none;'>";
													if(count($arrayTipoDoc)>0)
													{
														foreach($arrayTipoDoc as $key => $value)
														{
		// $buscadorPaciente .= "							<option value='".$key."'>".$value."</option>";	
		$buscadorPaciente .= "							<option value='".$key."'>".$key."</option>";	
														}
													}
														
		$buscadorPaciente .= "						<select>
												</td>
												<td>
													<input type='button' id='botonConsultarPaciente' value='Consultar' onclick='consultarPaciente();'>
												</td>
											</tr>
										</table>
										
									</fieldset>
								</div>
								
								<br>";
		
		echo $buscadorPaciente;
	}
	
	function consultarTipoDocumento()
	{
		global $conex;
		global $wemp_pmla;
		
		$q = "SELECT Codigo,Descripcion 
				FROM root_000007 
			   WHERE Estado='on' ;";
				 
		$res=  mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);	
		
		$arrayTipoDoc = array();
		if($num > 0)
		{
			while($row = mysql_fetch_array($res))
			{
				$arrayTipoDoc[$row['Codigo']] = $row['Descripcion'];
			}
			
		}
		
		return $arrayTipoDoc;	
	}
	
	function pintarReporte($whistoria)
	{
		$ultimoIngreso = consultarUltimoIngreso($whistoria);
		
		$AAgrabadas = "<div  id='divAlergiasAlertas' class='areaimprimirAA'>
						<table id='tablaAA' align='center' width='35%'>
									<tr>
										<td class='encabezadoTabla' align='center'  colspan='4'>DATOS DEL PACIENTE</td>
									</tr>";
				$AAgrabadas .= datosDemograficos($whistoria,$ultimoIngreso,"on","off");
				$AAgrabadas .= "	<tr>
										<td class='encabezadoTabla' align='center'  colspan='4'>ALERGIAS Y ALERTAS ASOCIADAS AL PACIENTE</td>
									</tr>";
		
		$arrayAA = consultarAlergiasAlertasPorIngreso($whistoria,$ultimoIngreso,"on","off");
		$AAgrabadas .= $arrayAA;
		
		$AAgrabadas .= "<tr><td colspan='4' align='center'><input type='button'  value='Imprimir'   onclick='boton_imp();'></td></tr>";
		$AAgrabadas .= "</table>";
		
		$AAgrabadas = utf8_encode($AAgrabadas);
		return $AAgrabadas;
	}
	
	function pintarReportePorIngreso($whistoria,$wingreso)
	{
		$AAgrabadas = "<div  id='divAlergiasAlertas' class='areaimprimirAA'>
						<table id='tablaAA' align='center' width='35%'>
									<tr>
										<td class='encabezadoTabla' align='center'  colspan='4'>DATOS DEL PACIENTE</td>
									</tr>";
				$AAgrabadas .= datosDemograficos($whistoria,$wingreso,"off","off");
				$AAgrabadas .= "	<tr>
										<td class='encabezadoTabla' align='center'  colspan='4'>ALERGIAS Y ALERTAS ASOCIADAS AL PACIENTE</td>
									</tr>";
		
		$arrayAA = consultarAlergiasAlertasPorIngreso($whistoria,$wingreso,"off","off");
		$AAgrabadas .= $arrayAA;
		
		$AAgrabadas .= "<tr><td colspan='4' align='center'><input type='button'  value='Imprimir'   onclick='boton_imp();'></td></tr>";
		$AAgrabadas .= "</table>";
		
		$AAgrabadas = utf8_encode($AAgrabadas);
		return $AAgrabadas;
	}
	
	function pintarReportePorIngresoImpresionHCE($wemp_pmla,$whistoria,$wingreso)
	{
		$httpHost = $_SERVER['HTTP_HOST'];
		// src=http://".$httpHost."/matrix/images/medical/root/logo_clinica.jpg
		// style='border-style: solid;border-color: #000000;'
		$AAgrabadas = "<div  id='divAlergiasAlertas'>
							<!--
							<table id='tablaEncabezadoAA' border=1 bordercolor=GRAY cellpadding=5 width='100%' cellspacing=0>
								<tr>
									<td><img src='../../../images/medical/root/HCE".$wemp_pmla.".jpg' heigth=76 width=120></td>
									<td width=550 align=center>ALERGIAS Y ALERTAS</td>
								</tr>
							</table>-->
							
							<table id='tablaEncabezadoAA' border=1 bordercolor=GRAY width='100%' cellspacing=0>
								<tr>
									<td align=center><img src='http://".$httpHost."/matrix/images/medical/root/HCE".$wemp_pmla.".jpg' heigth=76 width=120></td>
									<td width=550 align=center><b>ALERGIAS Y ALERTAS</b></td>
								</tr>
							</table>
							<br>
							
							<!--<h1 align=center>ALERGIAS Y ALERTAS</h1>-->
							
							<table id='tablaAA' align=center border=1 bordercolor=GRAY cellpadding=5 width='100%' cellspacing=0 style=font-size:8pt>
								<tr>
									<td align=center  colspan=6><b>DATOS DEL PACIENTE</b></td>
								</tr>";
				$AAgrabadas .= datosDemograficos($whistoria,$wingreso,"off","on");
				$AAgrabadas .= "<tr>
									<td class='encabezadoTabla' align=center  colspan=6><b>ALERGIAS Y ALERTAS ASOCIADAS AL PACIENTE</b></td>
								</tr>";
		
		$arrayAA = consultarAlergiasAlertasPorIngreso($whistoria,$wingreso,"off","on");
		$AAgrabadas .= $arrayAA;
		
		$AAgrabadas .= "</table>";
		
		$AAgrabadas = utf8_encode($AAgrabadas);
		return $AAgrabadas;
	}
	
	function consultarUltimoIngreso($historia)
	{
		global $conex;
		global $wemp_pmla;
		
		$q = "SELECT Oriing 
				FROM root_000037 
			   WHERE Orihis='".$historia."' 
				 AND Oriori='".$wemp_pmla."';";
				 
		$res=  mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);	
		
		$ingreso = "";
		if($num > 0)
		{
			$row = mysql_fetch_array($res);
			
			$ingreso = $row['Oriing'];
		}
		
		return $ingreso;		
	}
	
	function consultarAlergiasAlertasPorIngreso($historia,$ultimoIngreso,$general,$impresionHCE)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		
		$AAhistoricas = "";
		$cadenaTooltipRegistro = "";
		$cantNoExiste= 0;
		
		for($i=$ultimoIngreso;$i>=1;$i--)
		{
			if(($general=="off" && $i==$ultimoIngreso) || $general=="on")
			{
				$arrayAA = consultarAlertasyAlergiasAsociadasAlPaciente($historia,$i);
			
				$AAhistoricas .= "<tbody id='historico_".$i."'>";
				
				if($impresionHCE=="off")
				{
					$AAhistoricas .= "	<tr>
											<td class='encabezadoTabla' align='center'  colspan='4'>INGRESO ".$i."</td>
										</tr>
										<tr class='encabezadoTabla' align='center' >
											<td><b>Tipo</b></td>
											<td><b>Descripci&oacute;n</b></td>
											<td><b>Duraci&oacute;n</b></td>
											<td><b>Fecha l&iacute;mite</b></td>
										</tr>
										";
										
										// <tr class='encabezadoTabla' align='center' >
											// <td><b>Tipo</b></td>
											// <td><b>Descripci&oacute;n</b></td>
											// <td><b>Duraci&oacute;n</b></td>
											// <td><b>Fecha l&iacute;mite</b></td>
										// </tr>
				}
				else
				{
					$AAhistoricas .= "	<tr>
											<td align=center><b>Tipo</b></td>
											<td align=center colspan=3><b>Descripci&oacute;n</b></td>
											<td align=center><b>Duraci&oacute;n</b></td>
											<td align=center><b>Fecha l&iacute;mite</b></td>
										</tr>";
				}
				
				if(count($arrayAA)>0)
				{
					foreach($arrayAA as $key => $value)
					{
						if ($fila_lista=='Fila2')
							$fila_lista = "Fila1";
						else
							$fila_lista = "Fila2";

						
						if($value['Maatip']=="AT")
						{
							$tipo = "ALERTA";
						}
						else
						{
							$tipo = "ALERGIA";
						}
						
						$fechaLimite = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						if($value['Daalim']=="on")
						{
							$fechaLimite = $value['Daafli'];
						}
						
						$tooltip = "";
						if($impresionHCE=="off")
						{
							// ------------------------------------------
							// Tooltip
							// ------------------------------------------	
								$infoTooltip = "Creada por: ".$value['usuario']." - ".consultarNombreUsuario($value['usuario'])."<br> Rol: ".consultarRol($value['usuario'])."<br> Fecha y hora de registro: ".$value['fecha_data']." ".$value['hora_data'];
								$tooltip = "<div id=\"dvTooltip_".$i."_".$value['Daacod']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltip."</div>";
								$cadenaTooltipRegistro .= "tooltipRegistro_".$i."_".$value['Daacod']."|";
							// ------------------------------------------	
							
							$colspanDesc = "colspan=1";
						}
						else
						{
							$colspanDesc = "colspan=3";
						}
						
						$AAhistoricas .= "	<tr  id='tooltipRegistro_".$i."_".$value['Daacod']."' title='".$tooltip."'>
												<td class='".$fila_lista."' align=center>".$tipo."</td>
												<td class='".$fila_lista."' ".$colspanDesc.">".htmlentities($value['Maades'])."</td>
												<td class='".$fila_lista."' align=center>".$value['Maadur']."</td>
												<td class='".$fila_lista."' align=center>".$fechaLimite."</td>
											</tr>";
					}
				}
				
				
				$AAhistoricas .= "</tbody>";
			}
			
			
		}
		
		if($impresionHCE=="off")
		{
			$AAhistoricas .= "	<tr class='fila1'>
									<td id='SinAA' align='center' colspan='4'>No existen alertas ni alergias asociadas al paciente</td>
								</tr>";
			
			$AAhistoricas .= "<input type='hidden' id='tooltipRegistro' value='".$cadenaTooltipRegistro."'>";
			$AAhistoricas .= "<input type='hidden' id='impresionHCE' value='".$impresionHCE."'>";			
		}
		
		return $AAhistoricas;
	}
	
	
	function consultarRol($usuario)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatohce;
		global $wemp_pmla;
		
		$q = "SELECT Usurol,Roldes 
				FROM ".$wbasedatohce."_000020,".$wbasedatohce."_000019 
			   WHERE Usucod='".$usuario."' 
			     AND Usuest='on' 
				 AND Rolcod=Usurol;";
		
		$res=  mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		
		$rol = "";
		if($num > 0)
		{
			$row = mysql_fetch_array($res);
			
			$rol = $row['Usurol']." - ".$row['Roldes'];
		}
		
		return $rol;
	}
	
	function consultarNombreUsuario($usuario)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		
		$q = "SELECT Descripcion 
				FROM usuarios 
			   WHERE Codigo='".$usuario."'
				 AND Activo = 'A';";
		
		$res=  mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		
		$nombre = "";
		if($num > 0)
		{
			$row = mysql_fetch_array($res);
			
			$nombre = $row['Descripcion'];
		}
		
		return $nombre;
	}
	
	function consultarAlertasyAlergiasAsociadasAlPaciente($whistoria,$wingreso)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wuse;
		
		$q = "SELECT Daahis,Daaing,Daacod,Daalim,Daafli,Daapos,a.fecha_data,a.hora_data,a.Seguridad,Maades,Maatip,Maacla,Maadur 
				FROM ".$wbasedato."_000220 a,".$wbasedato."_000217 
			   WHERE Daahis='".$whistoria."' 
			     AND Daaing='".$wingreso."'
			     AND Daaest='on' 
				 AND Daacod=Maacod 
			ORDER BY Daatip,Daapos;";
			
		$res=  mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);	
		
		$contAlertas = 0;
		$contAlergias = 0;
		$arrayAA = array();
		if($num > 0)
		{
			while($row = mysql_fetch_array($res))
			{
				$usuAA = explode("-",$row['Seguridad']);
					
				$arrayAA[$row['Daacod']]['Daacod'] = $row['Daacod'];
				$arrayAA[$row['Daacod']]['Maades'] = $row['Maades'];
				$arrayAA[$row['Daacod']]['Maadur'] = (trim($row['Maadur']) == "H" ) ? "Hist&oacute;rico" :"Estancia";
				$arrayAA[$row['Daacod']]['Maatip'] = $row['Maatip'];
				$arrayAA[$row['Daacod']]['Maacla'] = $row['Maacla'];
				$arrayAA[$row['Daacod']]['Daalim'] = $row['Daalim'];
				$arrayAA[$row['Daacod']]['Daafli'] = $row['Daafli'];
				$arrayAA[$row['Daacod']]['Daapos'] = $row['Daapos'];
				$arrayAA[$row['Daacod']]['fecha_data'] = $row['fecha_data'];
				$arrayAA[$row['Daacod']]['hora_data'] = $row['hora_data'];
				$arrayAA[$row['Daacod']]['usuario'] = $usuAA[1];
				
				if($row['Maatip']=="AT")
				{
					$contAlertas++;
				}
				else
				{
					$contAlergias++;
				}
			}
		}
		
		return $arrayAA;
		
	}
	
	function datosDemograficos($historia,$ultimoIngreso,$general,$impresionHCE)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		
		//Nombre del paciente
		$q = "SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Oritid, Oriced  
				FROM root_000037, root_000036
			   WHERE Orihis = '".$historia."'
				 AND Oriori = '".$wemp_pmla."'
				 AND Oriced = Pacced ";

		$res=mysql_query($q,$conex);
		$row=mysql_fetch_array($res);

		$nombrePaciente = $row['Pacno1'].' '.$row['Pacno2'].' '.$row['Pacap1'].' '.$row['Pacap2'];
		$documento = $row['Oritid'].' '.$row['Oriced'];
		
		//Nombre del paciente
		$nombrePaciente = consultarNombrePaciente($historia,$wemp_pmla);
		
		
		if($impresionHCE=="on")
		{
			$datosDemograficos = "	<tr>
										<td class='fila2' colspan=1><b>DOCUMENTO: </b></td>
										<td class='fila2' colspan=1>".$documento."</td>
										<td class='fila1' colspan=1><b>NOMBRE: </b></td>
										<td class='fila1' colspan=1>".htmlentities($nombrePaciente)."</td>
										<td class='fila1' colspan=1><b>HISTORIA: </b></td>
										<td class='fila1' colspan=1>".$historia."-".$ultimoIngreso."</td>
									</tr>
									";

		}
		else
		{
			$datosDemograficos = "	<tr>
										<td class='fila1' colspan=1><b>NOMBRE: </b></td>
										<td class='fila1' colspan=3>".$nombrePaciente."</td>
									</tr>
									<tr>
										<td class='fila2' colspan=1><b>DOCUMENTO: </b></td>
										<td class='fila2' colspan=3>".$documento."</td>
									</tr>
									<tr>
										<td class='fila1' colspan=1><b>HISTORIA: </b></td>
										<td class='fila1' colspan=3>".$historia."</td>
									</tr>
									<tr>
										<td class='fila2' colspan=1><b>INGRESO: </b></td>
										<td class='fila2' colspan=3>";
											if($general=="on")
											{
												$datosDemograficos .= "	<select id='opcionIngreso' onchange='filtrarIngreso()'>
																			<option value='0'>Todos</option>";
												for($i=$ultimoIngreso;$i>=1;$i--)
												{
													$datosDemograficos .= "<option>".$i."</option>";
												}
												$datosDemograficos .= "</select>";
											}
											else
											{
												$datosDemograficos .= $ultimoIngreso;
											}
												
			$datosDemograficos .= "		</td>
									</tr>";
		}
		
		
		return $datosDemograficos;
	}
	
	function consultarNombrePaciente($historia,$wemp_pmla)
	{
		global $conex;
		global $wbasedato;
		
		//Nombre del paciente
		$q = "SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac 
				FROM root_000037, root_000036
			   WHERE Orihis = '".$historia."'
				 AND Oriori = '".$wemp_pmla."'
				 AND Oriced = Pacced ";

		$res=mysql_query($q,$conex);
		$row=mysql_fetch_array($res);

		$nombrePaciente = $row['Pacno1'].' '.$row['Pacno2'].' '.$row['Pacap1'].' '.$row['Pacap2'];
		
		return $nombrePaciente;
	}
	
	function consultarHistoria($wbasedato,$wemp_pmla,$wdocumento,$wtipo)
	{
		global $conex;
		
		//Nombre del paciente
		$q = "SELECT Orihis 
				FROM root_000037
			   WHERE Oriced = '".$wdocumento."'
				 AND Oritid = '".$wtipo."'
				 AND Oriori = '".$wemp_pmla."';";

		$res=mysql_query($q,$conex);
		$row=mysql_fetch_array($res);

		$historia = $row['Orihis'];
		
		return $historia;
	}
	
	

//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($action)) 
{
	switch($action)
	{
		case 'pintarReporte':
		{
			$data = pintarReporte($whistoria);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarHistoria':
		{
			$whistoria = consultarHistoria($wbasedato,$wemp_pmla,$wdocumento,$wtipo);
			$data = pintarReporte($whistoria);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarHtmlImpresionHCE':
		{
			$data = array();
			$data['html']= pintarReportePorIngresoImpresionHCE($wemp_pmla,$historia,$ingreso);
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
	  <title>REPORTE DE ALERGIAS Y ALERTAS</title>
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
				
		<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		
		<script src="../../../include/root/print.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	$(document).ready(function() 
	{
		if($("#impresionHCE").val() != "on")
		{
			filasFiltro();
		
			// -------------------------------------
			//	Tooltip
			// -------------------------------------
				var cadenaTooltipRegistro = $("#tooltipRegistro").val();
				
				if(cadenaTooltipRegistro!==undefined)
				{
					cadenaTooltipRegistro = cadenaTooltipRegistro.split("|");
				
					for(var i = 0; i < cadenaTooltipRegistro.length-1;i++)
					{
						$( "#"+cadenaTooltipRegistro[i] ).tooltip();
					}
				}
				
			// -------------------------------------
		}
		
	});
	
	
	function boton_imp()
	{
		$(".areaimprimirAA").printArea({			
				
				popClose: false,
				popTitle : 'Alergias y alertas',
				popHt    : 500,
				popWd    : 1200,
				popX     : 200,
				popY     : 200,
				
			});
	}	
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function filtrarIngreso()
	{
		
		//Todos
		if($("#opcionIngreso").val() == 0)
		{
			var ingresoConAA = 0;
			$('table[id=tablaAA] tbody[id^=historico_]').each(function(){
		  
				var idTbody= $(this).attr("id");
				
				ingreso = idTbody.replace("historico_","");
				
				$("#historico_"+ingreso).show();
				
			
			});
			
			
			$('table[id=tablaAA] tbody[id^=historico_]').each(function(){
		  
				var idTbody= $(this).attr("id");
				
				// if($("#"+idTbody+" tr").length == 1)
				if($("#"+idTbody+" tr").length == 2)
				{
					$("#"+idTbody).hide();
				}
				else
				{
					ingresoConAA++;
				}
			
			});
			
			if(ingresoConAA==0)
			{
				$("#SinAA").show();
			}
			else
			{
				$("#SinAA").hide();
			}
		}
		else
		{
			$('table[id=tablaAA] tbody[id^=historico_]').each(function(){
		  
				var idTbody= $(this).attr("id");
				
				ingreso = idTbody.replace("historico_","");
				
				if(ingreso == $("#opcionIngreso").val())
				{
					$("#historico_"+ingreso).show();
					
					// if($("#"+idTbody+" tr").length == 1)
					if($("#"+idTbody+" tr").length == 2)
					{
						$("#"+idTbody).hide();
						$("#SinAA").show();
					}
					else
					{
						$("#SinAA").hide();
					}
				}
				else
				{
					$("#historico_"+ingreso).hide();
				}
			
			});
		}
	}
	
	function filasFiltro()
	{
		$('table[id=tablaAA] tbody[id^=historico_]').each(function(){
			
			idTbody = $(this).attr('id');
			
			// if($("#"+idTbody+" tr").length == 1)
			if($("#"+idTbody+" tr").length == 2)
			{
				$("#"+idTbody).hide();
			}
			else
			{
				$("#SinAA").hide();
			}
		});
	}
	 
	function cambiarFiltroBuscador()
	{
		if($("#buscadorPaciente").val() == "doc")
		{
			$("#tipoDocumento").show();
		}
		else
		{
			$("#tipoDocumento").hide();
		}
	} 
	
	function consultarPaciente()
	{
		$( "#dvAuxreporteAA" ).hide();
		
		if($("#buscadorPaciente").val() == "doc")
		{
			$.post("reporteAlertas.php",
			{
				consultaAjax 	: '',
				action			: 'consultarHistoria',
				wbasedato		: $('#wbasedato').val(),
				wemp_pmla		: $('#wemp_pmla').val(),
				wdocumento		: $("#filtroBuscadorPaciente").val(),
				wtipo			: $("#tipoDocumento").val()
			}
			, function(data) {
				
				$( "#dvAuxreporteAA" ).html(data);
				$( "#dvAuxreporteAA" ).show();
				
				filasFiltro();
		
			// -------------------------------------
			//	Tooltip
			// -------------------------------------
				var cadenaTooltipRegistro = $("#tooltipRegistro").val();
				
				cadenaTooltipRegistro = cadenaTooltipRegistro.split("|");
				
				for(var i = 0; i < cadenaTooltipRegistro.length-1;i++)
				{
					$( "#"+cadenaTooltipRegistro[i] ).tooltip();
				}
			// -------------------------------------

			},'json');
			
		}
		else
		{
			$.post("reporteAlertas.php",
			{
				consultaAjax 	: '',
				action			: 'pintarReporte',
				wbasedato		: $('#wbasedato').val(),
				wemp_pmla		: $('#wemp_pmla').val(),
				whistoria		: $("#filtroBuscadorPaciente").val()
			}
			, function(data) {
				
				$( "#dvAuxreporteAA" ).html(data);
				
				$( "#dvAuxreporteAA" ).show();
				
				filasFiltro();
		
			// -------------------------------------
			//	Tooltip
			// -------------------------------------
				var cadenaTooltipRegistro = $("#tooltipRegistro").val();
				
				cadenaTooltipRegistro = cadenaTooltipRegistro.split("|");
				
				for(var i = 0; i < cadenaTooltipRegistro.length-1;i++)
				{
					$( "#"+cadenaTooltipRegistro[i] ).tooltip();
				}
			// -------------------------------------
				
			},'json');
			
		}
		
		$("#filtroBuscadorPaciente").val(""); // Limpiar buscador
				
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
	encabezado("REPORTE ALERGIAS Y ALERTAS", $wactualiz, 'clinica');
	
	
	echo "	<input type='hidden' id='wbasedato' value='".$wbasedato."'>
			<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";

	if(isset($whistoria))
	{
		$resumen = pintarReportePorIngreso($whistoria,$wingreso);
		echo utf8_decode($resumen);
	}
	else
	{
		pintarBuscador();
	}
	// pintarReporte($whistoria);
	
	echo "	<div id='dvAuxreporteAA' style='display:none'></div>";	
	if(!isset($whistoria))
	{
		echo "	<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";		
	}
	
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
