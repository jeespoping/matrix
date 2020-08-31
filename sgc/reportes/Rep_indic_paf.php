<?php
include_once("conex.php");
//=========================================================================================================================================\\
//
//						REPORTE INDICADORES PAF
//
//=========================================================================================================================================\\
//FECHA CREACION:       Septiembre 20 de 2012.
//AUTOR:                Jerson Andres Trujillo.                                                                                                        \\
//=========================================================================================================================================\\
//OBJETIVO: 	Calcular indicadores para el PAF
//Descripcion:  Este programa permite calcular diferentes inidcadores, con diferentes opciones como por ejemplo seleccionar empresa
//				Seleccion de periodo, seleccion de servicio.                                                                                                                                                                                                                                  \\
//==========================================================================================================================================\\	
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//	2012-11-29: Se agrego la funcionalidad de ver el detalle en una ventana emergente; 
//				Jerson trujillo.
//	2013-03-04: Se modifico el numerador del query del indicador 3, para que no tenga en cuenta los dias sabados, domingos ni festivos. 
//				Jerson trujillo.                                                                                                                                          \\
//                                                                                                                                         \\
//=========================================================================================================================================\\
//=====================================
//		INICIO DE SESSION
//=====================================
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
{
	echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");
	$wactualiz="(Abril 03 de 2013)";                      // ultima fecha de actualizacion               
	$wfecha	=	date("Y-m-d");   
	$whora 	= 	(string)date("H:i:s");                                                         
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	
	//====================================================================================================================================    
	// F U N C I O N E S   G E N E R A L E S
	//====================================================================================================================================
	function consultar_servicios ($baseddato_citas, $select='')
	{
		global $conex;
		$options.="<option value='TODOS'>TODOS</option>";
		$q_servicios = " SELECT clecod, cledes  
						   FROM ".$baseddato_citas."_000020
						  WHERE cleest = 'on' ";
		$res_servicios = mysql_query($q_servicios,$conex);
		
		if ((mysql_num_rows($res_servicios))>0)
		{
			while ($arr_servicios = mysql_fetch_array ($res_servicios))
			{
				if ($select!='' && $select==$arr_servicios['clecod'])
					$options.="<option value='".$arr_servicios['clecod']."' selected>".$arr_servicios['cledes']."</option>";
				else
					$options.="<option value='".$arr_servicios['clecod']."'>".$arr_servicios['cledes']."</option>";
			}
		}
		return $options;
	}
	
	function consultar_empresa($buscar, $baseddato_citas, $selected = 'no')
	{
		global $conex;
		$wbasedato_movhos = consultarAliasPorAplicacion($conex, '01', 'movhos');
		
		$options.="<option value=''>TODOS</option>";
		$q_empresas = " SELECT Nit, Epsnom
						  FROM ".$baseddato_citas."_000002, ".$wbasedato_movhos."_000049
						 WHERE Epsnom LIKE '%".$buscar."%'
						   AND Epscod = Nit
					  ORDER BY Epsnom
						";
		$res_empresas = mysql_query($q_empresas,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar Empresas ".$baseddato_citas."): ".$q_empresas." - ".mysql_error());
		while($row = mysql_fetch_array($res_empresas))
		{
			if ($selected != 'no' && $selected == $row['Nit'])
				$options .= '<option selected value="'.$row['Nit'].'" >'.utf8_encode(ucwords(strtolower($row['Epsnom']))).'</option>';
			else
				$options .= '<option value="'.$row['Nit'].'" >'.utf8_encode(ucwords(strtolower($row['Epsnom']))).'</option>';
		}
		return $options;
	}
	function consultar_empresa3($buscar, $wemp_pmla, $selected = 'no')
	{
		global $conex;
		$wbasedato_mag = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');
		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		
		$options.="<option value=''>TODOS</option>";
		$q_empresas = " SELECT 	Cempcod, Cempnom, Epsnom
						  FROM ".$wbasedato_mag."_000025, ".$wbasedato_movhos."_000049
						 WHERE Epsnom LIKE '%".$buscar."%'
						   AND Epscod = Cempcun
						   AND Cempest = 'on' 
					  ORDER BY Cempnom
						";
		$res_empresas = mysql_query($q_empresas,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar Empresas magenta): ".$q_empresas." - ".mysql_error());
		while($row = mysql_fetch_array($res_empresas))
		{
			if ($selected != 'no' && $selected == $row['Cempcod'])
				$options .= '<option selected value="'.$row['Cempcod'].'-'.$row['Cempnom'].'" >'.utf8_encode(ucwords(strtolower($row['Epsnom']))).'</option>';
			else
				$options .= '<option value="'.$row['Cempcod'].'-'.$row['Cempnom'].'" >'.utf8_encode(ucwords(strtolower($row['Epsnom']))).'</option>';
		}
		return $options;
	}
	
	function consultar_empresa_movhos49($buscar, $wemp_pmla, $selected = 'no')
	{
		global $conex;
		$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		
		$options.="<option value=''>TODOS</option>";
		$q_empresas = " SELECT Epscod, Epsnom
						  FROM ".$wbasedato."_000049
						 WHERE Epsnom LIKE '%".$buscar."%'
					  ORDER BY Epsnom
						";
		$res_empresas = mysql_query($q_empresas,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar Empresas movhos_49): ".$q_empresas." - ".mysql_error());
		while($row = mysql_fetch_array($res_empresas))
		{
			if ($selected != 'no' && $selected == $row['Epscod'])
				$options .= '<option selected value="'.$row['Epscod'].'" >'.utf8_encode(ucwords(strtolower($row['Epsnom']))).'</option>';
			else
				$options .= '<option value="'.$row['Epscod'].'" >'.utf8_encode(ucwords(strtolower($row['Epsnom']))).'</option>';
		}
		return $options;
	}
	
	//====================================================================================================================================
	//F I N  F U N C I O N E S
	//====================================================================================================================================	
	
	//=========================
	// CONSULTAS AJAX
	//=========================
	if ( isset($consultaAjax))
	{
		switch($accion)
		{
			case 'Recargar_procedimiento':
			{
				$options=consultar_servicios ($baseddato_citas);
				echo $options;
				break;
			}
			case 'Empresa_indicador2':
			{
				$options2 =consultar_empresa($buscar, $baseddato_citas);
				echo $options2;
				break;
			}
			case 'Empresa_indicador3':
			{
				$options3 = consultar_empresa3($buscar, $wemp_pmla);
				echo $options3;
				break;
			}
			case 'Empresa_movhos49':
			{
				$options4 = consultar_empresa_movhos49($buscar, $wemp_pmla);
				echo $options4;
				break;
			}
			case 'detalle_indicador_1':
			{
				$wbasedato_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
				$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
				$array_id_movimiento = explode('|', $id_movimiento);
				$color_fila= 'Fila1';
				
				if(count($array_id_movimiento)>0)
				{
					echo "
					<div  align='right' style='padding: 10px;cursor:default;'>
						<div class='fila2' onclick='$.unblockUI();' title='Cerrar' style='width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center>
									<b>Cerrar</b>
									<img width='10' height='10' border='0' style='cursor:pointer;' title='Eliminar Fila' src='../../images/medical/eliminar1.png'>
						</div>
					</div>
					<div style='padding: 10px;cursor:default;background:none repeat scroll 0 0; align:center;width:100 %;height:630px;overflow:auto;' >
					<table>
					<tr class='FondoAmarillo'>
						<td align='center' colspan='11' ><b>DETALLE</n></td>
					</tr>
					<tr class='Encabezadotabla'> 
						<td align='center' colspan='4'>Paciente</td>
						<td align='center' colspan='2'>Triage</td>
						<td align='center' colspan='2'>Atenci&oacute;n</td>
						<td align='center' rowspan='2'>Medico</td>
						<td align='center' rowspan='2'>Nivel<br>Triage</td>
						<td align='center' rowspan='2'>Oportunidad<br>(minutos)</td>
					</tr>
					<tr class='Encabezadotabla'>
						<td align='center'>Historia</td>
						<td align='center'>Nombre</td>
						<td align='center'>Apellidos</td>
						<td align='center'>Responsable</td>
						<td align='center'>Fecha</td>
						<td align='center'>Hora</td>
						<td align='center'>Fecha</td>
						<td align='center'>Hora</td>						
					</tr>";
				}
				foreach ($array_id_movimiento as $id)
				{				
					$q_detalle1 = "SELECT Mtrhis, Mtring, A.Mtrftr, A.Mtrhtr, Mtrfco, Mtrhco, Pacno1, Pacno2, Pacap1, Pacap2, Descripcion, Mtrtri, Ingnre, TIMESTAMPDIFF(MINUTE, CONCAT(A.Mtrftr,' ',A.Mtrhtr), CONCAT(Mtrfco,' ',Mtrhco)) as diferencia 
									 FROM ".$wbasedato_hce."_000022 as A, root_000036, root_000037, usuarios, ".$wbasedato_mov."_000016
									WHERE A.id = '".$id."'
									AND Mtrhis = Orihis
									AND Oriori = '".$wemp_pmla."'
									AND Oritid = Pactid
									AND Oriced = Pacced
									AND Mtrmed = codigo
									AND Mtrhis = Inghis
									AND Mtring = Inging 
								";
								
					$res_detalle1 = mysql_query($q_detalle1,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar detalle_indicador_1): ".$q_detalle1." - ".mysql_error());
					while($row_detalle1 = mysql_fetch_array($res_detalle1))
					{
						if($color_fila=='Fila2')
						{
							$color_fila ='Fila1';
						}
						else
							$color_fila ='Fila2';
							
						echo "
						<tr class='".$color_fila."'>
							<td>".$row_detalle1['Mtrhis'].'-'.$row_detalle1['Mtring']."</td>
							<td>".$row_detalle1['Pacno1'].' '.$row_detalle1['Pacno2']."</td>
							<td>".$row_detalle1['Pacap1'].' '.$row_detalle1['Pacap2']."</td>
							<td>".utf8_encode($row_detalle1['Ingnre'])."</td>
							<td align='center' style='padding: 3px;border-left:solid 2px #2A5DB0;border-top:solid 2px #2A5DB0;border-bottom:solid 2px #2A5DB0;'>".$row_detalle1['Mtrftr']."</td>
							<td align='center' style='padding: 3px;border-right:solid 2px #2A5DB0;border-top:solid 2px #2A5DB0;border-bottom:solid 2px #2A5DB0;'>".$row_detalle1['Mtrhtr']."</td>
							<td align='center' style='padding: 3px;border-left:solid 2px #666666;border-top:solid 2px #666666;border-bottom:solid 2px #666666;'>".$row_detalle1['Mtrfco']."</td>
							<td align='center' style='padding: 3px;border-right:solid 2px #666666;border-top:solid 2px #666666;border-bottom:solid 2px #666666;'>".$row_detalle1['Mtrhco']."</td>
							<td>".$row_detalle1['Descripcion']."</td>
							<td align='center'>".$row_detalle1['Mtrtri']."</td>
							<td align='center'>".(($row_detalle1['diferencia'] < 0) ? '0' : $row_detalle1['diferencia'])."</td>
						</tr>
							";
					}	
				}
				if(count($array_id_movimiento)>0)
				{
					echo "</table></div>";
				}
				break;
				
			}
			case 'detalle_indicador_2':
			{
				$wbasedato_citas = $servicio;
				$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
				$array_id_movimiento = explode('|', $id_movimiento);
				$color_fila= 'Fila1';
				
				if(count($array_id_movimiento)>0)
				{
					echo "
					<div  align='right' style='padding: 10px;cursor:default;'>
						<div class='fila2' onclick='$.unblockUI();' title='Cerrar' style='width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center>
									<b>Cerrar</b>
									<img width='10' height='10' border='0' style='cursor:pointer;' title='Eliminar Fila' src='../../images/medical/eliminar1.png'>
						</div>
					</div>
					<div style='padding: 10px;cursor:default;background:none repeat scroll 0 0; align:center;width:100 %;height:630px;overflow:auto;' >
					<table>
					<tr class='FondoAmarillo'>
						<td align='center' colspan='11' ><b>DETALLE</n></td>
					</tr>
					<tr class='Encabezadotabla'> 
						<td align='center' rowspan='2'>Nombre Paciente</td>
						<td align='center' rowspan='2'>Examen/Procedimiento</td>
						<td align='center' rowspan='2'>Responsable</td>
						<td align='center' rowspan='2'>Usuario</td>
						<td align='center' colspan='2'>Cita</td>
						<td align='center' rowspan='2'>Oportunidad<br>(dias)</td>
					</tr>
					<tr class='Encabezadotabla'>
						<td align='center'>Fecha solicitud</td>
						<td align='center'>Fecha asignaci&oacute;n</td>					
					</tr>";
				}
				foreach ($array_id_movimiento as $id)
				{
					if($id !='')
					{
						$q_detalle2 = "SELECT A.Nom_pac, B.Descripcion as examen,  C.Epsnom,  A.Fecha_data, A.Fecha, D.Descripcion, DATEDIFF(A.Fecha, A.Fecha_data) as numero_dias 
										 FROM ".$wbasedato_citas."_000001 as A, ".$wbasedato_citas."_000006 as B, ".$wbasedato_mov."_000049 as C, usuarios as D
										WHERE A.id = '".$id."'
										  AND Cod_exa = B.Codigo
										  AND Nit_resp = Epscod
										  AND A.Usuario = D.Codigo
										GROUP BY  B.Codigo
									";
									
						$res_detalle2 = mysql_query($q_detalle2,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar detalle_indicador_2): ".$q_detalle2." - ".mysql_error());
						while($row_detalle2 = mysql_fetch_array($res_detalle2))
						{
							if($color_fila=='Fila2')
							{
								$color_fila ='Fila1';
							}
							else
								$color_fila ='Fila2';
								
							echo "
							<tr class='".$color_fila."'>
								<td>".$row_detalle2['Nom_pac']."</td>
								<td align='center'>".$row_detalle2['examen']."</td>
								<td>".$row_detalle2['Epsnom']."</td>
								<td>".$row_detalle2['Descripcion']."</td>
								<td align='center'>".$row_detalle2['Fecha_data']."</td>
								<td align='center'>".$row_detalle2['Fecha']."</td>
								<td align='center'>".$row_detalle2['numero_dias']."</td>
							</tr>
								";
						}
					}
				}
				if(count($array_id_movimiento)>0)
				{
					echo "</table></div>";
				}
				break;
			}
			case 'detalle_indicador_3':
			case 'detalle_indicador_4':
			{
				$wbasedato_mag = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');
				$array_id_movimiento = explode('|', $id_movimiento);
				$color_fila= 'Fila1';
				
				if(count($array_id_movimiento)>0)
				{
					echo "
					<div  align='right' style='padding: 10px;cursor:default;'>
						<div class='fila2' onclick='$.unblockUI();' title='Cerrar' style='width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center>
									<b>Cerrar</b>
									<img width='10' height='10' border='0' style='cursor:pointer;' title='Eliminar Fila' src='../../images/medical/eliminar1.png'>
						</div>
					</div>
					<div style='padding: 10px;cursor:default;background:none repeat scroll 0 0; align:center;width:100 %;height:630px;overflow:auto;' >
					<table>
					<tr class='FondoAmarillo'>
						<td align='center' colspan='11' ><b>DETALLE</n></td>
					</tr>
					<tr class='Encabezadotabla'> 
						<td align='center' >Origen</td>
						<td align='center' >Id Comentario</td>
						<td align='center' >Entidad usuario</td>
						<td align='center' >Estado</td>
						<td align='center' >Fecha comentario</td>
						<td align='center' >Fecha respuesta</td>
						<td align='center' >Oportunidad<br>(dias)</td>
					</tr>";
				}
				foreach ($array_id_movimiento as $id)
				{
					if($id !='')
					{
						$q_detalle3 = " SELECT Ccoori, Ccoid, Ccoent, Ccoest, Ccofori, Ccorfec, (DATEDIFF(Ccorfec, DATE_ADD(Ccofori, INTERVAL 1 DAY))-( CantFestivos_Lun_a_Vie(Ccofori, Ccorfec) + CantidadDia(Ccofori, Ccorfec, 'Sunday') + CantidadDia(Ccofori, Ccorfec, 'Saturday'))) as numero_dias 
										  FROM ".$wbasedato_mag."_000017
										 WHERE Ccoid  = '".$id."' ";
									
						$res_detalle3 = mysql_query($q_detalle3,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar detalle_indicador_3): ".$q_detalle3." - ".mysql_error());
						while($row_detalle3 = mysql_fetch_array($res_detalle3))
						{
							if($color_fila=='Fila2')
							{
								$color_fila ='Fila1';
							}
							else
								$color_fila ='Fila2';
								
							echo "
							<tr class='".$color_fila."'>
								<td>".$row_detalle3['Ccoori']."</td>
								<td>".$row_detalle3['Ccoid']."</td>
								<td>".$row_detalle3['Ccoent']."</td>
								<td align='center'>".$row_detalle3['Ccoest']."</td>
								<td align='center'>".$row_detalle3['Ccofori']."</td>
								<td align='center'>".$row_detalle3['Ccorfec']."</td>
								";
								if($row_detalle3['numero_dias'] > 15)
								echo "<td align='center' style='background-color: #E37371;'>".$row_detalle3['numero_dias']."</td>";
								else
								echo "<td align='center'>".$row_detalle3['numero_dias']."</td>";
							
							echo"
							</tr>
								";
						}
					}
				}
				if(count($array_id_movimiento)>0)
				{
					echo "</table></div>";
				}
				break;
			}
		}
		
	}
	//=========================
	// FIN CONSULTAS AJAX
	//=========================
	//===============================
	// EJECUCION NORMAL DEL PROGRAMA
	//===============================
	else
	{
		?>
		<html>
		<head>
			<title>REPORTE INDICADORES PAF</title>

			<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_page.css" rel="stylesheet"/>
			<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_table.css" rel="stylesheet"/>
			<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_validation.css" rel="stylesheet"/>
			<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
			<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
			<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
			
			<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
			<script src="../../../include/root/jquery_1_7_2/js/jquery.DataTables.min.js" type="text/javascript"></script>
			<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.js" type="text/javascript"></script>
			<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
			<script src="../../../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
			<script src="../../../include/root/jquery_1_7_2/js/jquery.DataTables.editable.js" type="text/javascript"></script>
			<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.checkbox.js" type="text/javascript"></script>
			<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.datapicker.js" type="text/javascript"></script>
			<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
			<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
			<style type="text/css">
				#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
				#tooltip h3, #tooltip div{margin:0; width:500px}
			</style>	
			
			  
			<script type="text/javascript">

				function enter()
				{
				 document.forms.indicadores.submit();
				}
				
				function cerrarVentana()
				{
				  window.close();		  
				}
				function recargar_procedimientos(basedato, select)
				{
					$('#'+select).load("Rep_indic_paf.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&accion=Recargar_procedimiento&baseddato_citas="+basedato);
					
					recargarEmpresa('%', 'wempresa', basedato, 'Empresa_indicador2', 'no');
				}
				
				function cambioImagen(img1, img2)
				{
					$('#'+img1).hide(1000);
					$('#'+img2).show(1000);
				}
				
				function enterBuscar(hijo, e)
				{
					tecla = (document.all) ? e.keyCode : e.which;
					if(tecla==13) { $("#"+hijo).focus(); }
					else { return true; }
					return false;
				}
				
				function recargarEmpresa(id_padre, id_hijo, servicio, accion, llamado_buscador)
				{
					if (llamado_buscador == 'si')
					{
						val = $("#"+id_padre.id).val();
						servicio = document.getElementById(servicio).value;
					}
					else
					{
						val = id_padre;
					}
				
					$('#'+id_hijo).load("Rep_indic_paf.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&accion="+accion+"&buscar="+val+"&baseddato_citas="+servicio);
				}
				//recargarEmpresa2(this,\"wempresa\", \"Empresa_indicador3\")
				function recargarEmpresa2(id_padre, id_hijo, accion)
				{
					val = $("#"+id_padre.id).val();
					$('#'+id_hijo).load("Rep_indic_paf.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&accion="+accion+"&buscar="+val);
				
				}
				
				//Pintar el detalle del movimiento
				function pintar_detalle(id_movimiento, indicador)
				{
					$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >', 
						css: 	{
									width: 	'auto',
									height: 'auto'                                    
								}
						}
					);
					$.post("Rep_indic_paf.php",
						{
							consultaAjax:   		'',
							wemp_pmla:      		$('#wemp_pmla').val(),
							wuse:           		$('#wuse').val(),
							accion:         		'detalle_indicador_'+indicador,
							servicio:				$('#wservicio').val(),		
							id_movimiento:			id_movimiento
							
						}
						,function(data) {
						
							$.blockUI({ message: data, 
							css: {  left: '3%', 
									top:'10%',
								    width: 'auto',
									height: 'auto',
									position:'absolute'									
								 } 
							});	
						}
					);
				}
				
				//mostrar los title de los nombres de los patrones
				$(document).ready(function()
				{
					var cont1 = 1;
					while(document.getElementById("descripcion"+cont1))
					{
						$('#descripcion'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
						cont1++;
					}; 
				}
				);
				 //fin mostrar titles
			</script>
		</head>
		<body>
		<?php         
		
		encabezado("Reporte Indicadores PAF", $wactualiz, 'clinica');
		echo "<div align=center>";
		//================================================================
		//	FORMA 
		//================================================================
		echo "<form name='indicadores' action='' method=post>";
		echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";     
		if (strpos($user,"-") > 0)
			$wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
		 
		//=========================================
		// Ver manual
		//=========================================
		echo "<div style='left: 9%; position: absolute;'><A href='configuracion_inidcadores_PAF.pdf' target='_blank'><img src='../../images/medical/iconos/gifs/i.p.next[1].gif' /> <b>Ver Manual de configuración </b></A></div><br><br>";
		//=============================================================
		// Seleccionar indicador
		//=============================================================
		$indicadores[1]["nombre"]="Oportunidad de la atención en el servicio de urgencias";
		$indicadores[1]["numerador"]="Sumatoria del número de minutos transcurridos entre la solicitud de atención en la consulta de urgencias y el momento en el cual es atendido el paciente en consulta por parte del médico";
		$indicadores[1]["denominador"]="Total de usuarios atendidos en consulta de urgencias";
		$indicadores[1]["resultado"]="Es el tiempo que se esta demorando la atención de los pacientes en minutos en Emergencias y Urgencias";
		$indicadores[1]["nom_numerador"]="Sumatoria minutos transcurridos entre solicitud de atención en urgencias y momento de atencion por parte del médico";
		$indicadores[1]["nom_denominador"]="Usuarios atendidos";
		$indicadores[1]["nom_resultado"]="Tiempo Promedio que se esta demorando la atención de los pacientes en Urgencias";
		$indicadores[2]["nombre"]="Oportunidad en la Atención (Citas)";
		$indicadores[2]["numerador"]="Sumatoria total de los días calendario transcurridos entre la fecha en la cual el paciente solicita cita para ser atendido en el servicio y la fecha para la cual es asignada la cita";
		$indicadores[2]["denominador"]="Número total de atenciones en el servicio";
		$indicadores[2]["resultado"]="Es el tiempo que se esta demorando la atención de los pacientes en días para una cita";
		$indicadores[2]["nom_numerador"]="Sumatoria de días calendario entre la fecha de solicitud de cita y fecha para la cual es asignada ";
		$indicadores[2]["nom_denominador"]="Total de atenciones en el servicio";
		$indicadores[2]["nom_resultado"]="Tiempo que se esta demorando la atención ";
		$indicadores[3]["nombre"]="Proporción de quejas resueltas antes de 15 días";
		$indicadores[3]["numerador"]="Numero de quejas resueltas antes de 15 días hábiles";
		$indicadores[3]["denominador"]="Total  de quejas radicadas";
		$indicadores[3]["resultado"]="Es el porcentaje de quejas resueltas antes de 15 días";
		$indicadores[3]["nom_numerador"]="Quejas resueltas antes de 15 días";
		$indicadores[3]["nom_denominador"]="Total de quejas radicadas";
		$indicadores[3]["nom_resultado"]="Porcentaje de quejas resueltas";
		$indicadores[4]["nombre"]="Tasa de quejas PAF x 10.000";
		$indicadores[4]["numerador"]="Nro de Quejas radicadas relacionadas con servicios PAF";
		$indicadores[4]["denominador"]="Total población asignada al PAF";
		$indicadores[4]["resultado"]="Es la tasa de quejas de usuarios PAF";
		$indicadores[4]["nom_numerador"]="Nro de Quejas radicadas";
		$indicadores[4]["nom_denominador"]="Total usuarios";
		$indicadores[4]["nom_resultado"]="Tasa de quejas x cada 10.000 usuarios ";
		
		$color ="Fila1";
		echo "<table style='border: 2px solid #2A5DB0; padding: 5px; width: 60%;'>
				<tr>
					<td align=center class='EncabezadoTabla'><b>SELECCIONE EL INDICADOR:</b><br></td>
				</tr>";
				foreach ($indicadores as $clave => $valor)
				{
					if ($color == "Fila1")
						$color = "Fila2";
					else
						$color = "Fila1";
					echo "<tr class=".$color.">
					<td align=left>";
					if (isset ($inidcador) && $inidcador==$clave)
						$checked = "checked='checked'";
					else
						$checked = "";
					echo "<input type='radio' name='inidcador'  value='".$clave."' ".$checked." onClick='enter();'>".$clave."&nbsp;".$valor["nombre"]."<br>"; 
					
					echo"</td>
					</tr>";
				}
		echo"</table>";
		//=============================================================
		// Fin seleccionar indicador
		//=============================================================
		echo "</form>"; 
		
		if (isset ($inidcador))
		{
			//==================================================================
			//	FILTROS DE CONSULTA PARA EL INDICADOR  
			//==================================================================
			echo "<center><table style='border: 2px solid #2A5DB0; padding: 5px; width: 60%;'>
			<tr><td colspan=2 align='center' class='EncabezadoTabla'><b>Filtros de consulta</b></td></tr>
			<form name='filtros' action='' method=post>
			<input type='HIDDEN' name='inidcador' id='inidcador' value='".$inidcador."'>";		
			//=================================
			// SELECCIONAR FECHAS A CONSULTAR
			//=================================
			echo "<tr class='Fila2'>
					<td  align=center><b>Fecha inicial: </b>";  
				if(isset($wfec_i) && isset($wfec_f))
				{
					campoFechaDefecto("wfec_i", $wfec_i);
				}
				else
				{
					campoFechaDefecto("wfec_i", date("Y-m-d"));
				}
				echo "</td>";
				echo "<td align=center><b>Fecha final: </b>"; 
				if(isset($wfec_i) && isset($wfec_f))
				{
					campoFechaDefecto("wfec_f", $wfec_f);
				}
				else
				{
					campoFechaDefecto("wfec_f", date("Y-m-d"));
				}
				echo "</td>";
			echo "</tr>";
			switch ($inidcador)
			{
				//--------------------------------------------------------
				//Oportunidad de la atención en el servicio de urgencias
				//--------------------------------------------------------
				case 1: 
					{
						// Seleccionar triage
						$wbasedato_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
						$q_triage = " 	SELECT tricod, trinom
										  FROM ".$wbasedato_hce."_000040
										 WHERE Triest = 'on' 
									";
						$res_triage = mysql_query($q_triage,$conex) or die ("Error: ".mysql_errno()." - en el query(Triage): ".$q_triage." - ".mysql_error());
						echo "<tr class='Fila1'>
								<td  align=center colspan=2><b>Nivel Triage: </b>";
								while ($arr_triage = mysql_fetch_array ($res_triage))
								{
									if (isset($triagge) && in_array($arr_triage['tricod'], $triagge))
										echo "<input type='checkbox' name=triagge[]  checked='checked' value='".$arr_triage['tricod']."' >".$arr_triage['trinom']."&nbsp&nbsp";
									else
										echo "<input type='checkbox' name=triagge[]  value='".$arr_triage['tricod']."' >".$arr_triage['trinom']."&nbsp&nbsp";
								}
								if (isset($triagge) && in_array(' ', $triagge))
									echo "<input type='checkbox' name=triagge[]  checked='checked' value=' ' >Sin triage&nbsp&nbsp";
								else
									echo "<input type='checkbox' name=triagge[]  value=' ' >Sin triage&nbsp&nbsp";
								
								echo "</td>";
						echo "</tr>";
						//----------------------
						// Ingresar empresa
						//----------------------
						if (isset($wempresa))
						{
							$wempresa_selected = $wempresa;
							$options4 = consultar_empresa_movhos49('%', $wemp_pmla, $wempresa_selected);
						}
						else
							$options4 = consultar_empresa_movhos49('%', $wemp_pmla );
							
						echo "<tr><td colspan='2'class='Fila2' align=center><b>Nombre Empresa:</b>
							<img width='15' height='15' border='0' src='../../images/medical/HCE/lupa.PNG'/>
							<input name='wempresa_bus' id='wempresa_bus' type='text' onkeypress='return enterBuscar(\"wempresa\", event);'  onblur='recargarEmpresa2(this,\"wempresa\", \"Empresa_movhos49\");cambioImagen(\"ccload1\",\"\");' onfocus='cambioImagen(\"\",\"ccload1\");'  \>
							<img id='ccload1' style='display:none;' width='15' height='15' border='0' src='../../images/medical/ajax-loader9.gif' />
								<select style='width:265px;' name='wempresa' id='wempresa' >
									$options4
								</select>
						 </td>
						 </tr>";
						break;
					}
				//--------------------------------------------------------
				// Oportunidad en la Atención (Citas)
				//--------------------------------------------------------
				case 2: 
					{
						// Seleccionar servicio
						$q_servicios = " SELECT detval, detdes  
										   FROM root_000051
										  WHERE Detemp = '".$wemp_pmla."'
											AND Detapl = 'citas' ";
						
						$res_servicios = mysql_query($q_servicios,$conex) or die ("Error: ".mysql_errno()." - en el query(Servicios): ".$q_servicios." - ".mysql_error());
		
						echo "<tr class='Fila1'>
								<td align=center><b>Servicio: </b>
									<select id='wservicio' name='wservicio'>";
								$servicio_default='on';
								while ($arr_servicios = mysql_fetch_array ($res_servicios))
								{
									$wbasedato_citas=$arr_servicios['detval'];
									if ($servicio_default=='on')
										$servicio_default = $wbasedato_citas;
										
									if (isset($wservicio) && $wservicio==$wbasedato_citas)
									{
										$servicio_selected = $wservicio;
										echo "<option value='".$wbasedato_citas."' selected onclick='recargar_procedimientos(\"".$wbasedato_citas ."\", \"wprocedimiento\");'>".$arr_servicios['detdes']."</option>";
									}
									else
										echo "<option value='".$wbasedato_citas."' onclick='recargar_procedimientos(\"".$wbasedato_citas ."\", \"wprocedimiento\");'>".$arr_servicios['detdes']."</option>";
								}
						echo "	</td>";
						// Seleccionar procedimiento
						if (!isset($wservicio))
						{
							$options=consultar_servicios ($servicio_default);
						}
						else
						{
							$options=consultar_servicios ($wservicio, $wprocedimiento );
						}
						echo"	<td colspan=2 align=center><b>Procedimiento: </b>
									<select name='wprocedimiento' id='wprocedimiento'>
										$options
									</SELECT>
								</td></tr>";
						//-------------------------------------
						// Buscador y seleccionador de empresa
						//-------------------------------------
						//value='".((!isset($wservicio)) ? $wempresa : '')."'
						//$options2 =consultar_empresa('%', $baseddato_citas);
						if (isset($wempresa) && isset($wempresa_bus))
						{
							$options2 = consultar_empresa($wempresa_bus, $servicio_selected, $wempresa);
						}
						else
						{
							$options2 = consultar_empresa('%', $servicio_default);
						}
						
						echo "<tr class='Fila2'><td  align=center><b>Nombre Empresa:</b>
						<img width='15' height='15' border='0' src='../../images/medical/HCE/lupa.PNG'/>
						<input name='wempresa_bus' id='wempresa_bus' type='text' onkeypress='return enterBuscar(\"wempresa\", event);' onblur='recargarEmpresa(this,\"wempresa\", \"wservicio\", \"Empresa_indicador2\", \"si\");cambioImagen(\"ccload1\",\"\");' onfocus='cambioImagen(\"\",\"ccload1\");'  \>
						<img id='ccload1' style='display:none;' width='15' height='15' border='0' src='../../images/medical/ajax-loader9.gif' />
						<br><select style='width:265px;' name='wempresa' id='wempresa' >
							$options2
						</select>
							 </td>";
							 
						$checked_si='';
						$checked_no=''; 
						if (isset($primera_vez))
						{
							if ($primera_vez=='si')
								$checked_si='checked';
							else
								$checked_no='checked';
						}
						else
						{
							$checked_no='checked';
						}
						echo"<td align=center><b>¿ Primera vez ?</b><br>
								<input type='radio' name='primera_vez'  value='si' ".$checked_si.">Si
								<input type='radio' name='primera_vez'  value='no' ".$checked_no.">Todas<br>
								<b>¿ Imagen diagnostica ?</b><br>";
						if (isset($imagen))
						{
							foreach ($imagen as $valor)
							{
								switch($valor)
								{
									case 'Exaidb':
											{
												$chec_img_B='checked';
												break;
											}
									case 'Exaide':
											{
												$chec_img_E='checked';
												break;
											}
								}
							}
						}
							
						echo"		<input type='checkbox' name='imagen[]'  value='Exaidb' ".$chec_img_B.">Básica
								<input type='checkbox' name='imagen[]'  value='Exaide' ".$chec_img_E.">Especializada
							 </td>
							 ";
						break;
					}
				//--------------------------------------------------------
				// Proporción de quejas resueltas antes de 15 días
				//--------------------------------------------------------
				case 3:case 4:
					{
						//----------------------
						// Ingresar empresa
						//----------------------
						if (isset($wempresa))
						{
							$wempresa_selected = explode ('-', $wempresa);
							$options3 = consultar_empresa3('', $wemp_pmla, $wempresa_selected[0]);
						}
						else
							$options3 = consultar_empresa3('%', $wemp_pmla );
							
						echo "<tr><td colspan='2'class='Fila1' align=center><b>Nombre Empresa:</b>
							<img width='15' height='15' border='0' src='../../images/medical/HCE/lupa.PNG'/>
							<input name='wempresa_bus' id='wempresa_bus' type='text' onkeypress='return enterBuscar(\"wempresa\", event);' onblur='recargarEmpresa2(this,\"wempresa\", \"Empresa_indicador3\");cambioImagen(\"ccload1\",\"\");' onfocus='cambioImagen(\"\",\"ccload1\");'  \>
							<img id='ccload1' style='display:none;' width='15' height='15' border='0' src='../../images/medical/ajax-loader9.gif' />
								<select style='width:265px;' name='wempresa' id='wempresa' >
									$options3
								</select>
						 </td>
						 </tr>";
						break;
					}
			}
			echo "<tr>";
				echo "<td align=center colspan=2 ><b><br><input type='submit' name='GENERAR' value='GENERAR'></b></td>";
			echo "</tr>";
			echo "</form>";
			echo "</table><br>";
		}
		
		//====================================================================================
		//		CALCULAR INDICADOR
		//====================================================================================
		if (isset($GENERAR) && isset($wfec_i) && isset($wfec_f) && $wfec_f>=$wfec_i ) // si ya seleccionaron los parametros para consultar
		{
			//==================
			//	QUERYS
			//==================
			switch ($inidcador)
			{
				//--------------------------------------------------------
				//Oportunidad de la atención en el servicio de urgencias
				//--------------------------------------------------------
				case 1:
					{
						//----------------------------------------------------------------------------------------------------------------------
						// NUMERADOR:	Sumatoria del número de minutos transcurridos entre la solicitud de atención en la consulta de urgencias 
						//				y el momento en el cual es atendido el paciente en consulta por parte del médico.
						// DENOMINADOR:	Total de usuarios atendidos en consulta de urgencias
						//----------------------------------------------------------------------------------------------------------------------
						//Consultar el centro de costos de urgencias
						$q_urg = " SELECT ccocod 
								    FROM ".$wbasedato."_000011
								   WHERE Ccourg = 'on'
								     AND Ccoest = 'on' ";
								  
						$res_urg = mysql_query($q_urg,$conex) or die ("Error: ".mysql_errno()." - en el query(Cco urg): ".$q_urg." - ".mysql_error());
						$arr_urg = mysql_fetch_array ($res_urg);
						
						//Consultar cuantos triagges existen
						$q_triage = " 	SELECT count(*)
										  FROM ".$wbasedato_hce."_000040
										 WHERE Triest = 'on' 
									";
						$res_triage = mysql_query($q_triage,$conex) or die ("Error: ".mysql_errno()." - en el query(Triage): ".$q_triage." - ".mysql_error());
						$total_triagges = mysql_fetch_array ($res_triage); 
						$total_triagges = $total_triagges[0];
						
						//Esta variable la agregamos en la consulta para condicionar que no incluya los usuarios de tipo SOAT		
						$q= "SELECT detapl,detval
							   FROM root_000051
							  WHERE detapl='tiposoat'
								AND detemp='$wemp_pmla'";
						$res=mysql_query($q);
						$row=mysql_fetch_array($res);
						$wdetval=$row['detval']; 
						
						//ingresos que sean SOAT: Consultar tiempos en que realizaron el triage y relizaron la consulta
						$q_1 = " SELECT mtrfco, mtrhco, mtrftr, mtrhtr, mtrhis, mtring, A.id, Mtrtri  
								   FROM ".$wbasedato_hce."_000022 as A, ".$wbasedato."_000016 as B
								  WHERE A.Fecha_data between '".$wfec_i."' AND '".$wfec_f."' ";
						if(count ($triagge)>0)
						{
							$q_1.=" AND Mtrtri IN (";
							$primera_entrada='si';
							foreach ($triagge as $posicion => $valor_triage)
							{
								if($primera_entrada=='si')
								{
									$q_1.= " '".$valor_triage."'";
									$primera_entrada='no';
								}
								else
									$q_1.= " ,'".$valor_triage."'";
							}
							$q_1.=" 			) ";
						}
						$q_1.= "
									AND mtrfco 		 != '0000-00-00'
									AND mtrftr		 != '0000-00-00'
									AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )
									AND Mtrcci 		  = '".trim($arr_urg['ccocod'])."'
									AND Mtrhis 		  = Inghis
									AND Mtring 		  = Inging 
									AND Ingtip  	  = '".$wdetval."' ";
						if ($wempresa!='')	
						{
						$q_1.= "	AND Ingres = '".$wempresa."'";
						}
						$res_q1 = mysql_query($q_1,$conex) or die ("Error: ".mysql_errno()." - en el query(Indicador 1): ".$q_1." - ".mysql_error());
						$num_q1 = mysql_num_rows ($res_q1);
						
						if($num_q1>0)
						{
							//si hay ingresos creamos una tabla temporal para guardar los ingresos de accidente
							conexionOdbc($conex, 'movhos', $conexUnix, 'facturacion');
							$q1=" DROP TABLE IF EXISTS ingreacc2 ";
							$result1 = mysql_query($q1,$conex) or die("ERROR EN QUERY $q1 - ".mysql_error() );
							
							$q1=" CREATE TEMPORARY TABLE IF NOT EXISTS ingreacc2 "
							   ."(INDEX cod_idx( mtrftr, mtrhtr ),"	
							   ."Mtrfco date, mtrhco time, mtrftr date, mtrhtr time, id INT, Mtrtri Varchar(3))";

							$result1 = mysql_query($q1,$conex) or die("ERROR EN QUERY $q1 - ".mysql_error() );

							while ($row = mysql_fetch_array ($res_q1))
							{
								$whis = $row['mtrhis'];
								$wing = $row['mtring'];
								$wfco = $row['mtrfco'];
								$whco = $row['mtrhco'];
								$wfed = $row['mtrftr'];
								$whod = $row['mtrhtr'];
								$wid  = $row['id'];
								$wtri = $row['Mtrtri'];

								//preguntamos en unix si el ingreso es de accidente
								$q = "SELECT *
										FROM inaccdet
									   WHERE accdethis = '".$whis."'
										 AND accdetnum = '".$wing."'";

								$res1 = odbc_do($conexUnix,$q);
								$num1 = odbc_fetch_row($res1);

								if($num1>0)
								{
									//llenamos la temporal con los ingresos de tipo accidente
									$q2 = "INSERT INTO ingreacc2 (Mtrfco, Mtrhco, mtrftr, mtrhtr, id, Mtrtri) VALUES ('".$wfco."','".$whco."','".$wfed."','".$whod."', '".$wid."', '".$wtri."')";
									$result2 = mysql_query($q2,$conex) or die("ERROR EN QUERY $q2 - ".mysql_error() );
								}
							}
						}
						// --> Ingresos que no sean SOAT: Consultar tiempos en que realizaron el triage y relizaron la consulta
						$q_1 = " SELECT mtrfco, mtrhco, mtrftr, mtrhtr, A.id, Mtrtri  
								   FROM ".$wbasedato_hce."_000022 as A, ".$wbasedato."_000016 as B
								  WHERE A.Fecha_data between '".$wfec_i."' AND '".$wfec_f."' ";
						if(count ($triagge)>0)
						{
							$q_1.=" AND Mtrtri IN (";
							$primera_entrada='si';
							foreach ($triagge as $posicion => $valor_triage)
							{
								if($primera_entrada=='si')
								{
									$q_1.= " '".$valor_triage."'";
									$primera_entrada='no';
								}
								else
									$q_1.= " ,'".$valor_triage."'";
							}
							$q_1.=" 			) ";
						}
						$q_1.= "
									AND mtrfco 		 != '0000-00-00'
									AND mtrftr 		 != '0000-00-00'
									AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )
									AND Mtrcci 		  = '".trim($arr_urg['ccocod'])."'
									AND Mtrhis 		  = Inghis
									AND Mtring 		  = Inging 
									AND Ingtip  	  != '".$wdetval."' ";
						if ($wempresa!='')	
						{
						$q_1.= "	AND Ingres = '".$wempresa."'";
						}
						
						
						//echo $q_1;
						$q2 = '';
						if($num_q1>0)
						{
							$q2.="UNION"
							   ." SELECT mtrfco, mtrhco, mtrftr, mtrhtr, id, Mtrtri "
							   ."    FROM ingreacc2 ";
						}

						$q = "
								SELECT mtrfco, mtrhco, mtrftr, mtrhtr, id, Mtrtri
								FROM
								(
									$q_1
									$q2
									ORDER BY Mtrtri, mtrftr DESC, mtrhtr DESC
								) as t
							";
						$result = mysql_query($q, $conex) or die("ERROR EN QUERY $q - ".mysql_error() );
						$num_5 = mysql_num_rows( $result );

						$tiempo_espera = 0;
						$id_movimientos = ''; 
						while ($arr_q1 = mysql_fetch_array ($result))
						{
							$fecha_consulta 		= $arr_q1['mtrfco'];
							$hora_consulta 			= $arr_q1['mtrhco'];
							$fecha_triage			= $arr_q1['mtrftr'];
							$hora_triage 			= $arr_q1['mtrhtr'];
							
							$id_movimientos.= 		$arr_q1['id'].'|';
							
							$tiempo_consulta 		= strtotime($fecha_consulta.' '.$hora_consulta); 	// Convertir fecha y hora a formato unix
							$tiempo_triage			= strtotime($fecha_triage.' '.$hora_triage);		// Convertir fecha y hora a formato unix
							
							if($tiempo_consulta > $tiempo_triage)
							{
								$tiempo_espera+= floor(($tiempo_consulta-$tiempo_triage)/60);
							}
						}
						if ($num_5>0)
						{
							$tiempo_espera_promedio= round($tiempo_espera/$num_5);
							$numerador = $tiempo_espera.'  (minutos)';
							$denominador = $num_5;
							$resultado = $tiempo_espera_promedio.'  (minutos)';
						}
						break;
					}
				//--------------------------------------------------------
				// Oportunidad en la Atención (Citas)
				//--------------------------------------------------------
				case 2:
					{	
						//----------------------------------------------------------------------------------------------------------------------
						// NUMERADOR:	Sumatoria total de los días calendario transcurridos entre la fecha en la cual el paciente solicita cita 
						//				para ser atendido en el servicio de ecocardiografia y la fecha para la cual es asignada la cita.
						// DENOMINADOR:	Número total de atenciones en el servicio de ecocardiografia asignadas en la Institución. $primera_vez
						//----------------------------------------------------------------------------------------------------------------------
						
						// Si no es primera vez
						if (!isset($primera_vez) || $primera_vez=='no')
						{
							$q_ind2 = " SELECT  DATEDIFF(A.Fecha, A.Fecha_data) as numero_dias, A.id
										  FROM ".$wservicio."_000001 as A, ".$wservicio."_000006 as B
										 WHERE A.Fecha_data between '".$wfec_i."' AND '".$wfec_f."'";
							if ($wempresa!='')	
							{
							$q_ind2.= "	   AND Nit_resp	 	 = '".$wempresa."'";
							}
							$q_ind2.= "    AND Cod_equ 		 = Cod_equipo
										   AND Cod_exa 		 = Codigo ";
							if(isset($imagen))
							{
							$q_ind2.= "    AND ( ";
								foreach ($imagen as $clave_im => $valor_im)
								{
									if ($clave_im == 0)
							$q_ind2.= "	   ".$valor_im." = 'on' ";
									else
							$q_ind2.= "	   || ".$valor_im." = 'on' ";
								}
							$q_ind2.= "    		) ";
							}
							if($wprocedimiento!='TODOS')
							$q_ind2.= "	   AND Clasificacion = '".$wprocedimiento."' ";

							//echo $q_ind2; 
							$res_ind2 = mysql_query($q_ind2,$conex) or die ("Error: ".mysql_errno()." - en el query(Indicador 2): ".$q_ind2." - ".mysql_error());
							$num_ind2 = mysql_num_rows ($res_ind2);
						}
						// Si es primera vez
						else
						{
							// Consulto los datos y lo inserto en una temporal, este query es la misma estructura como si fuera para todas las citas
							// si no que aca como es solo para citas de primera vez agrupo por cedula y selecciono la de menor fecha lo que me indica 
							// que es la primera cita.
							$q_crear_temporal = " CREATE TEMPORARY TABLE IF NOT EXISTS tem_indicador_3".date('Ymd')." 
												  SELECT  MIN(A.Fecha) as Fecha, A.Fecha_data 
												    FROM ".$wservicio."_000001 as A, ".$wservicio."_000006 as B
											       WHERE A.Fecha_data between '".$wfec_i."' AND '".$wfec_f."'";
										if ($wempresa!='')	
										{
							$q_crear_temporal.= "	 AND Nit_resp	 	 = '".$wempresa."'";
										}
							$q_crear_temporal.= "    AND Cod_equ 		 = Cod_equipo
													 AND Cod_exa 		 = Codigo ";
										if($wprocedimiento!='TODOS')
										{
							$q_crear_temporal.= "	 AND Clasificacion = '".$wprocedimiento."' ";
										}
							$q_crear_temporal.=	"    AND Cedula !=''
												   GROUP BY Cedula ";
							mysql_query($q_crear_temporal,$conex) or die ("Error: ".mysql_errno()." - en el query(crear temporal): ".$q_crear_temporal." - ".mysql_error());
							
							//consulto sobre la temporal calculando la diferencia entre la fecha de solicitud y la fecha de la cita
							$q_ind2 = " SELECT DATEDIFF(A.Fecha, A.Fecha_data) as numero_dias, A.id 
										  FROM tem_indicador_3".date('Ymd')." ";
							$res_ind2 = mysql_query($q_ind2,$conex) or die ("Error: ".mysql_errno()." - en el query(Indicador 2): ".$q_ind2." - ".mysql_error());
							$num_ind2 = mysql_num_rows ($res_ind2);
						}
						
						if ($num_ind2>0)
						{
							echo "<input type='hidden'  name='wservicio' id='wservicio' value='".$wservicio."'>";
							$id_movimientos = '';
							$numerador = 0;
							while ($arr_ind2 = mysql_fetch_array ($res_ind2))
							{
								$numerador+= $arr_ind2['numero_dias'];
								$id_movimientos.= $arr_ind2['id'].'|';
							}
							$denominador = $num_ind2;
							if ($numerador!=NULL && $denominador > 0)
							{
								$resultado = $numerador/$denominador;
								$resultado =number_format($resultado,1,",",".").'  (Días)';
								$numerador.='  (Días)'; 
							}
						}
						break;
					}
				//--------------------------------------------------------
				// Proporción de quejas resueltas antes de 15 días
				//--------------------------------------------------------
				case 3:
					{
						//----------------------------------------------------------------------------------------------------------------------
						// NUMERADOR:	Numero de quejas resueltas por servicios PAF antes de 15 días calendario.
						// DENOMINADOR:	Total  de quejas radicadas por los diferentes canales establecidos.
						//----------------------------------------------------------------------------------------------------------------------
						$wbasedato_mag = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');
						
						// --> Resueltas antes de 15 dias habiles, es decir sin contar festivos, sabados ni domingos y sin contar el dia en 
						//	   que realizaron el comentario, por eso se le suma un dia. 
						// 	   Se utilizan las siguientes funciones almacenadas:
						//	   CantFestivos_Lun_a_Vie('Fecha inicio', 'Fecha Fin', 'Dia a contar'): Funcion que dado un rango de fechas
						//																			 y el nombre de un dia, retorna la cantidad
						//																			 de apariciones de ese dia especifico en el rango de fechas.
						//	   CantFestivos_Lun_a_Vie('Fecha inicio', 'Fecha Fin'): Funcion que me retorna la cantidad de dias festivos que existen en un periodo
						//															solo de lunes a viernes, es decir si hay un dia festivo que cae un sabado no lo cuenta,
						//															esta funcion depende de la tabla root_000063 (Dias festivos).				
						
						$q_ind3 = " SELECT Id_Comentario 
									  FROM ".$wbasedato_mag."_000017, ".$wbasedato_mag."_000018 
									 WHERE Ccofori between '".$wfec_i."' AND '".$wfec_f."' 
									   AND DATEDIFF(Ccorfec, DATE_ADD(Ccofori, INTERVAL 1 DAY))-( CantFestivos_Lun_a_Vie(Ccofori, Ccorfec) + CantidadDia(Ccofori, Ccorfec, 'Sunday') + CantidadDia(Ccofori, Ccorfec, 'Saturday') ) <= 15 ";  
						if ($wempresa!='')	
						{
						$q_ind3.= "	   AND Ccoent	 	 = '".$wempresa."'";
						}
						$q_ind3.= "    AND Ccoid  = Id_Comentario
									   AND Cmotip = 'Desagrado' ";
						$res_ind3 = mysql_query($q_ind3,$conex) or die ("Error: ".mysql_errno()." - en el query(Indicador 3): ".$q_ind3." - ".mysql_error());
						$numerador = mysql_num_rows ($res_ind3);
						$id_movimientos = '';
												
						$q_ind3 = " SELECT Id_Comentario 
									  FROM ".$wbasedato_mag."_000017, ".$wbasedato_mag."_000018
									 WHERE Ccofori between '".$wfec_i."' AND '".$wfec_f."' ";
						if ($wempresa!='')	
						{
						$q_ind3.= "	   AND Ccoent	 	 = '".$wempresa."'";
						}
						$q_ind3.= "	   AND Ccoid  = Id_Comentario
									   AND Cmotip = 'Desagrado' 
									 ORDER BY Ccofori DESC
										";
						$res_ind3 = mysql_query($q_ind3,$conex) or die ("Error: ".mysql_errno()." - en el query(Indicador 3): ".$q_ind3." - ".mysql_error());
						$denominador = mysql_num_rows ($res_ind3);
						while( $row_ind3 = mysql_fetch_array($res_ind3))
						{
							$id_movimientos.= $row_ind3['Id_Comentario'].'|';
						}
						
						if ($denominador>0)
							$resultado = ($numerador/$denominador)*100;
							$resultado = number_format($resultado,1,",",".").' %';
						break;
					}
				case 4:
					{
						//----------------------------------------------------------------------------------------------------------------------
						// NUMERADOR:	Nro de Quejas radicadas relacionadas con servicios PAF
						// DENOMINADOR:	Total población asignada al PAF
						//----------------------------------------------------------------------------------------------------------------------
						$wbasedato_mag = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');						
						//$wempresa = '73-PAF';
						
						$q_ind4= "  SELECT Id_Comentario 
									  FROM ".$wbasedato_mag."_000017, ".$wbasedato_mag."_000018
									 WHERE Ccofori between '".$wfec_i."' AND '".$wfec_f."'"; 
						if ($wempresa!='')	
						{
						$q_ind4.= "	   AND Ccoent	 	 = '".$wempresa."'";
						}
						$q_ind4.= "	   AND Ccoid  = Id_Comentario
									   AND Cmotip = 'Desagrado'
								  ORDER BY Ccofori DESC
									  ";
						$res_ind4 = mysql_query($q_ind4,$conex) or die ("Error: ".mysql_errno()." - en el query(Indicador 4): ".$q_ind4." - ".mysql_error());
						$numerador = mysql_num_rows ($res_ind4);
						$id_movimientos = '';
						while( $row_ind4 = mysql_fetch_array($res_ind4))
						{
							$id_movimientos.= $row_ind4['Id_Comentario'].'|';
						}
						
						$wempresa = explode ('-', $wempresa);
						$wempresa = $wempresa[0];
						$denominador = 0;
						
					
						$q_tabla = " SELECT Cemppaf, Cempcun
									   FROM ".$wbasedato_mag."_000025 
									  WHERE Cempcod = '".$wempresa."' ";
						$res_tabla = mysql_query($q_tabla,$conex) or die ("Error: ".mysql_errno()." - en el query(Consultar tabla): ".$q_tabla." - ".mysql_error());
						$row_tabla = mysql_fetch_array($res_tabla);
						
						// 1- Si la empresa seleccionada tiene el parametro Cemppaf = on, 
						// 	  quiere decir que es del programa paf y el denominador sera el total de registros
						//    de la tabla paf_000002 (usuarios paf)
						// 2- Si wempresa == '', quiere decir que seleccionaron todas la empresas y entonces el denominador
						//    sera la sumatoria de la poblacion paf, mas la poblacion de las demas empresas
						if ($row_tabla['Cemppaf']=='on' || $wempresa=='') 
						{
							$q_ind4 = " SELECT count(*) as num 
										  FROM paf_000002 ";
							$res_ind4 = mysql_query($q_ind4,$conex) or die ("Error: ".mysql_errno()." - en el query(Denominador Indicador 4): ".$q_ind4." - ".mysql_error());
							$num_ind4 = mysql_num_rows ($res_ind4);
							if ($num_ind4>0)
							{	
								$arr_ind4 = mysql_fetch_array ($res_ind4);
								$denominador+= $arr_ind4['num'];
							}
						}
						if ($row_tabla['Cemppaf']!='on' || $wempresa=='')
						{
							$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
							
							if ($row_tabla['Cempcun']!='')	//si la empresa tiene su correspondiente codigo unix
							{
								$q_ind4 = " SELECT Inghis as num 
											  FROM  ".$wbasedato_mov."_000016 
											 WHERE Ingres = '".$row_tabla['Cempcun']."' 
											 GROUP BY Inghis
											";
								$res_ind4 = mysql_query($q_ind4,$conex) or die ("Error: ".mysql_errno()." - en el query(Denominador Indicador 4): ".$q_ind4." - ".mysql_error());
								$num_ind4 = mysql_num_rows ($res_ind4);
							}
							if ($wempresa=='')	//Si seleccionaron todos los centros de costos
							{
								$q_ind4 = " SELECT Inghis as num 
											  FROM  ".$wbasedato_mov."_000016, ".$wbasedato_mag."_000025 
											 WHERE Cemppaf != 'on' 
											   AND Ingres   = Cempcun
											   AND Ingres  != '' 
											 GROUP BY Inghis
											";
								$res_ind4 = mysql_query($q_ind4,$conex) or die ("Error: ".mysql_errno()." - en el query(Denominador Indicador 4): ".$q_ind4." - ".mysql_error());
								$num_ind4 = mysql_num_rows ($res_ind4);
							}
							$denominador+= $num_ind4;
							
						}
						
						if ($denominador>0)
						{
							$resultado = ($numerador/$denominador)*10000;
							$resultado = number_format($resultado,1,",",".");
						}
						break;
					}	
			}
			//------------------------
			// PintaR resultado
			//------------------------
			echo '<br><table style="border: 2px solid #2A5DB0;font-family: verdana;padding: 5px; width: 60%;">
							<tr><td class="Fondoamarillo" colspan=3 align=center><b>RESULTADO:</b></td></tr>';
			if (isset($resultado))
			{
					echo'	<tr class="EncabezadoTabla" align="center">
								<td  > <span id="descripcion1" title="'.$indicadores[$inidcador]["numerador"].'" ><b>'.$indicadores[$inidcador]["nom_numerador"].':</b></span></td>
								<td ><span id="descripcion2" title="'.$indicadores[$inidcador]["denominador"].'" ><b>'.$indicadores[$inidcador]["nom_denominador"].':</b></span></td>
								<td><span id="descripcion3" title="'.$indicadores[$inidcador]["resultado"].'" ><b>'.$indicadores[$inidcador]["nom_resultado"].':</b></span></td> 
							</tr>
							<tr class="fila2" align="center" style="cursor:pointer" onclick="pintar_detalle(\''.$id_movimientos.'\', \''.$inidcador.'\')">
								<td >'.$numerador.'</td>
								<td>'.$denominador.'</td>
								<td>'.$resultado.'</td>
							</tr>';	
			}
			else
			{
					echo'	<tr class="fila2">
								<td align=center >No se encontraron resultados.<br />Intente con otros filtros de consulta.</td>
							</tr>';
			}
			echo '</table><br>';
			
		}//if parametros 
		else
		{
			if($wfec_i>$wfec_f)
			{
				echo "<script type='text/javascript'>
				alert ('La fecha inicial NO puede ser mayor a la final');
				</script>	";
			}
		} 
		echo "<br>";
		echo "<table>"; 
		echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		echo "</table>";
		echo "</div>";
	?>
	</body>
	</html>
	<?php
	}// FIN EJECUCION NORMAL DEL PROGRAMA
} // if de register
