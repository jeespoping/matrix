<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Reporte de produccion de dosis adaptadas permite consultar las preparaciones realizadas por día.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	Septiembre 12 de 2017
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='Septiembre 26 de 2017';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2017-09-26 Jessica Madrid Mejía		- Se agrega espacio en la parte superior para que el quimico farmaceutico firme al imprimir el reporte
//  2017-09-20 Jessica Madrid Mejía		- Se agrega utf8_encode ya que no se mostraba el reporte si había algún caracter especial.
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
	

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function pintarFiltroFecha($fechaReporte)
	{
		$filtroFecha = "";
		$filtroFecha .= "	<div id='divFiltroFecha' align='center'>
								<fieldset align='center' style='padding:5px;margin:5px;border: 2px solid #2a5db0;width:33%'>
									<legend style='border: 2px solid #2a5db0;border-top: 0px;font-family: Verdana;color: #ffffff;background-color: #2a5db0;font-size:8pt;font-weight:bold;'> Consultar Producci&oacute;n diaria de Dosis Adaptadas </legend>
									<table>
										<tr>
											<td  align='center' colspan='2'>
												<input type='text' id='filtroFecha' name='filtroFecha' value='".$fechaReporte."' readOnly='readOnly' onChange='generarReporte(\"off\");'>
											</td>
										</tr>
									</table>
									
								</fieldset>
							</div>
							
							<br>";
		
		echo $filtroFecha;
	}
	
	function consultarNombreUsuario($codigoUsuario)
	{
		
		global $conex;
		
		$queryUsuario = " SELECT Descripcion 
							FROM usuarios 
						   WHERE Codigo='".$codigoUsuario."' 
							 AND Activo='A';";
								 
		$resUsuario = mysql_query($queryUsuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryUsuario." - ".mysql_error());
		$numUsuario= mysql_num_rows($resUsuario);	
		
		$nombreUsuario = "";
		if($numUsuario > 0)
		{
			$rowUsuario = mysql_fetch_array($resUsuario);
			
			$nombreUsuario = $rowUsuario['Descripcion'];
		}
		
		return $nombreUsuario;
	}
	
	function consultarProduccionDA($wemp_pmla,$wbasedato,$wcenpro,$fechaReporte)
	{
		
		global $conex;
		
		$queryProduccionDA = "SELECT Prehis,Preing,Precod,Prelot,Preno1,Preno2,Preurp,Prefrp,Preamp,Preura,Preuab,Descripcion,Deffra,Deffru,Plofcr,Plofve,Pacno1,Pacno2,Pacap1,Pacap2
								FROM ".$wcenpro."_000022,".$wbasedato."_000059,".$wcenpro."_000004,usuarios,root_000037,root_000036
							   WHERE Prerea='on'
								 AND Preest='on'
								 AND Prefrp='".$fechaReporte."'
								 AND Precod=Defart
								 AND Precod=Plopro
								 AND Prelot=Plocod
								 AND Preurp=Codigo
								 AND Prehis=Orihis
								 AND Preing=Oriing
								 AND Oriori='01'
								 AND Oriced=Pacced
								 AND Oritid=Pactid
							ORDER BY Prehis,Preing,Precod,Prelot";
								 
		$resProduccionDA = mysql_query($queryProduccionDA,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryProduccionDA." - ".mysql_error());
		$numProduccionDA = mysql_num_rows($resProduccionDA);	
		
		$arrayProduccionDA = array();
		$arrayDosisPorPaciente = array();
		$contDA = 0;
		if($numProduccionDA > 0)
		{
			while($rowProduccionDA = mysql_fetch_array($resProduccionDA))
			{
				$idArray = $rowProduccionDA['Prehis']."|".$rowProduccionDA['Preing']."|".$rowProduccionDA['Precod'];
				
				if(!array_key_exists ($idArray,$arrayProduccionDA))
				{
					$arrayProduccionDA[$idArray]['historia'] = $rowProduccionDA['Prehis'];
					$arrayProduccionDA[$idArray]['ingreso'] = $rowProduccionDA['Preing'];
					$arrayProduccionDA[$idArray]['codigoDA'] = $rowProduccionDA['Precod'];
					$arrayProduccionDA[$idArray]['nomPaciente'] = $rowProduccionDA['Pacno1']." ".$rowProduccionDA['Pacno2']." ".$rowProduccionDA['Pacap1']." ".$rowProduccionDA['Pacap2'];
					$arrayProduccionDA[$idArray]['dosis'] = $rowProduccionDA['Deffra'];
					$arrayProduccionDA[$idArray]['unidad'] = $rowProduccionDA['Deffru'];
				}
			
				if($idArray==$rowProduccionDA['Prehis']."|".$rowProduccionDA['Preing']."|".$rowProduccionDA['Precod'])
				{
					$arrayProductoDA['lote'] = $rowProduccionDA['Prelot'];
					$arrayProductoDA['nombre1'] = $rowProduccionDA['Preno1'];
					$arrayProductoDA['nombre2'] = $rowProduccionDA['Preno2'];
					$arrayProductoDA['codUsuPre'] = $rowProduccionDA['Preurp'];
					$arrayProductoDA['fechaPrep'] = $rowProduccionDA['Prefrp'];
					$arrayProductoDA['nomUsuPre'] = $rowProduccionDA['Descripcion'];
					$arrayProductoDA['FechaCrea'] = $rowProduccionDA['Plofcr'];
					$arrayProductoDA['FechaVenc'] = $rowProduccionDA['Plofve'];
					$arrayProductoDA['loteAmpolla'] = $rowProduccionDA['Preamp'];
					$arrayProductoDA['usuAcond'] = $rowProduccionDA['Preura'];
					$arrayProductoDA['usuAprueba'] = $rowProduccionDA['Preuab'];
					
					$arrayProduccionDA[$idArray]['dosisAdaptadas'][] = $arrayProductoDA;
					
				}
			}
		}
		return $arrayProduccionDA;	
		
	}
	
	function pintarReporteProduccion($wemp_pmla,$wbasedato,$wcenpro,$fechaReporte,$paraImprimir)
	{
		$paraImprimir = "off";
		
		$encabezados = "class='encabezadoTabla'";
		$styleTable = "";
		$boton = "<input type='button'  value='Imprimir'   onclick='boton_imp();'>";
		$claseImprimir = "";
		if($paraImprimir=="on")
		{
			$encabezados = "style=' border: 1px solid black;border-spacing: 0px;'";
			$styleTable = "style='border-spacing: 0px;'";
			$boton = "<input type='button'  value='Regresar'   onclick='generarReporte(\"off\");'>";
			$claseImprimir = "class='areaimprimirDA'";
		}
		
		
		$colspan="15";

		$altoimagen = '50';
		$anchoimagen = '138';
		
		$arrayProduccionDA = consultarProduccionDA($wemp_pmla,$wbasedato,$wcenpro,$fechaReporte);
		
		$reporteDA = "";
		$reporteDA .= "	<div id='divReporteProduccionDA' width='95%'>
							<div class='areaimprimirDA'>
							<br>
							<table id='tablaProdDA' width='95%' align='center' ".$styleTable.">
								<tr>
									<td  colspan='".$colspan."' align='right' style='font-size:10pt'>QF aseguramiento de la calidad: ______________________________&nbsp;&nbsp;&nbsp;</td>
								</tr>
								<tr align='center'>
									<td ".$encabezados." colspan='".$colspan."'>REPORTE DE PRODUCCION DEL DIA: ".$fechaReporte."</td>
								</tr>
								<tr align='center'>
									<td ".$encabezados.">Historia</td>
									<td ".$encabezados.">Paciente</td>
									<td ".$encabezados.">C&oacute;digo Dosis Adaptada</td>
									<td ".$encabezados.">Dosis</td>
									<td ".$encabezados.">Cantidad de dosis enviadas</td>
									<td ".$encabezados.">N&uacute;mero de Lote</td>
									<td ".$encabezados.">Descripci&oacute;n</td>
									<td ".$encabezados.">Fecha de vencimiento</td>
									<td ".$encabezados.">Cantidad de r&oacute;tulos generados por lote</td>
									<td ".$encabezados.">Cantidad de r&oacute;tulos usados</td>
									<td ".$encabezados.">Lote de la ampolla</td>
									<td ".$encabezados.">QF quien realiz&oacute; la preparaci&oacute;n</td>
									<td ".$encabezados.">RF quien realiz&oacute; el acondicionamiento</td>
									<td ".$encabezados.">Aprueba la preparaci&oacuten</td>
								</tr>";
								
								if(count($arrayProduccionDA)>0)
								{
									foreach($arrayProduccionDA as $keyDA => $valueDA)
									{
										if ($fila_lista=='Fila1')
											$fila_lista = "Fila2";
										else
											$fila_lista = "Fila1";
										
										if($paraImprimir=="on")
										{
											$fila_lista = "filaImprimir";
										}
										
										
										$rowspan = count($valueDA['dosisAdaptadas']);
										
											
		$reporteDA .= 				"	<tr align='center'>";
		$reporteDA .= 				"		<td class='".$fila_lista."' rowspan='".$rowspan."'>".$valueDA['historia']."-".$valueDA['ingreso']."</td>
											<td class='".$fila_lista."' rowspan='".$rowspan."'>".$valueDA['nomPaciente']."</td>
											<td class='".$fila_lista."' rowspan='".$rowspan."'>".$valueDA['codigoDA']."</td>
											<td class='".$fila_lista."' rowspan='".$rowspan."'>".$valueDA['dosis']." ".$valueDA['unidad']."</td>
											<td class='".$fila_lista."' rowspan='".$rowspan."'>".$rowspan."</td>";
											foreach($valueDA['dosisAdaptadas'] as $keyLote => $valueLote)
											{
												$firmaUsuPrepara = "&nbsp;";
												if(file_exists('../../images/medical/hce/Firmas/'.$valueLote['codUsuPre'].'.png'))
												{
													$firmaUsuPrepara = '<img src="../../images/medical/hce/Firmas/'.$valueLote['codUsuPre'].'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';	
												}
												
												$firmaUsuAcod = "&nbsp;";
												if(file_exists('../../images/medical/hce/Firmas/'.$valueLote['usuAcond'].'.png'))
												{
													$firmaUsuAcod = '<img src="../../images/medical/hce/Firmas/'.$valueLote['usuAcond'].'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';	
												}
												
												$firmaUsuApru = "&nbsp;";
												if(file_exists('../../images/medical/hce/Firmas/'.$valueLote['usuAprueba'].'.png'))
												{
													$firmaUsuApru = '<img src="../../images/medical/hce/Firmas/'.$valueLote['usuAprueba'].'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0"/>';	
												}
												
												if($keyLote!=0)
												{
		$reporteDA .= 				"				<tr align='center'>";												
												}
		$reporteDA .= 				"					<td class='".$fila_lista."'>".$valueLote['lote']."</td>
														<td class='".$fila_lista."'>".$valueLote['nombre1']."".$valueLote['nombre2']."</td>
														<td class='".$fila_lista."'>".$valueLote['FechaVenc']."</td>
														<td class='".$fila_lista."'>2</td>
														<td class='".$fila_lista."'>2</td>
														<td class='".$fila_lista."'>".$valueLote['loteAmpolla']."</td>
														<td class='".$fila_lista."'style='font-size:7pt;vertical-align:top;'>".$firmaUsuPrepara."<br><hr>".$valueLote['nomUsuPre']."</td>
														<td class='".$fila_lista."'style='font-size:7pt;vertical-align:top;'>".$firmaUsuAcod."<br><hr>".consultarNombreUsuario($valueLote['usuAcond'])."</td>
														<td class='".$fila_lista."'style='font-size:7pt;vertical-align:top;'>".$firmaUsuApru."<br><hr>".consultarNombreUsuario($valueLote['usuAprueba'])."</td>
													</tr>";	
											}
											
									}
								}
								else
								{
									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";
									
									if($paraImprimir=="on")
									{
										$fila_lista = "filaImprimir";
									}
									
		$reporteDA .= 				"<tr align='center'>
										<td  class='".$fila_lista."' colspan='".$colspan."'><b>No se han preparado Dosis adaptadas en esta fecha</b></td>
									</tr>";
								}
		$reporteDA .= "		</table>
							<span style='font-size:7pt'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* Reporte generado el ".date("Y-m-d")." a las ".date("H:i:s")."</span>
							<br>
						</div>
						<p align=center>".$boton."</p>
						</div>";
		
		return $reporteDA;
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
		case 'consultarProduccionDiariaDA':
		{	
			$data = pintarReporteProduccion($wemp_pmla,$wbasedato,$wcenpro,$fechaReporte,$paraImprimir);
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
	  <title>REPORTE DE PRODUCCION DOSIS ADAPTADAS</title>
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		
		<script src="../../../include/root/print.js" type="text/javascript"></script>
		
	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};

	$.datepicker.setDefaults($.datepicker.regional['esp']);	
	
	$(document).ready(function() {

		$("#filtroFecha").datepicker({
			
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonText: "Seleccione la fecha de produccion",
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			maxDate:new Date()
		});

	});
	
	
	function generarReporte(paraImprimir)
	{
		var fechaReporte = $("#filtroFecha").val();
		
		$.post("reporteProduccionDA.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarProduccionDiariaDA',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			wcenpro			: $('#wcenpro').val(),
			fechaReporte	: fechaReporte,
			paraImprimir	: paraImprimir
		}
		, function(data) {
			
			$( "#divReporteProduccionDA" ).html(data);
		
		},'json');
		
	}
	
	
	function boton_imp()
	{
		// generarReporte('on');
		generarReporte('off');
		
		$(".areaimprimirDA").printArea({			
				
			popClose: false,
			popTitle : 'Produccion diaria Dosis adaptadas',
			popHt    : 500,
			popWd    : 1200,
			popX     : 200,
			popY     : 200,
			
		});
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
	.encabezadoTabla                                 
	{
		 background-color: #2A5DB0;
		 color: #FFFFFF;
		 font-size: 10pt;
		 font-weight: bold;
	}
	.fila1                                
	{
		 background-color: #C3D9FF;
		 color: #000000;
		 font-size: 10pt;
	}
	.fila2                                
	{
		 background-color: #E8EEF7;
		 color: #000000;
		 font-size: 10pt;
	}
	.filaImprimir                                
	{
		 background-color: #FFFFFF;
		 color: #000000;
		 font-size: 10pt;
		 border-collapse: collapse;
		 border-spacing: 0px;
		 border: 1px solid black;
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
	encabezado("BATCH RECORD AJUSTE Y/O ADECUACION DE CONCENTRACIONES", $wactualiz, 'clinica');
	
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' id='wcenpro' name='wcenpro' value='".$wcenpro."'>";

	$fechaReporte = date("Y-m-d");
	
	$filtroFecha = pintarFiltroFecha($fechaReporte);
	echo $filtroFecha;
	
	echo "	<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";	
	
	$reporteProduccion = pintarReporteProduccion($wemp_pmla,$wbasedato,$wcenpro,$fechaReporte,"off");
	echo $reporteProduccion;
	
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
