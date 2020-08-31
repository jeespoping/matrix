<?php
include_once("conex.php");
//=========================================================================================================================================\\
//       	REPORTE DE OCUPACION DE LAS CAMILLAS
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-01-16';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------
/*
	2020-01-16, Jerson Trujillo: Se quita filtro de estado del query que trae el maestro de camillas.
*/
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
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	

	include_once("root/comun.php");
	

	$conex 			= obtenerConexionBD("matrix");
	$wbasedatoMov 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//------------------------------------------------------------------------------------
	//	-->
	//------------------------------------------------------------------------------------
	/*function ()
	{

	}*/

//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R 	P O S T    J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'consultar':
		{
			$respuesta 	= array("Error" => false, "Html" => "");
			$arrayInfo 	= array();
			$arraySalas = array();
			$arrayCubi 	= array();

			// --> Obtener el maestro de salas
			$sqlSalas = "
			SELECT Arecod, Aredes
			  FROM ".$wbasedatoMov."_000169
			 WHERE Areest = 'on'
			";
			$resSalas = mysql_query($sqlSalas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSalas):</b><br>".mysql_error());
			while($rowSalas = mysql_fetch_array($resSalas))
				$arraySalas[$rowSalas['Arecod']] = $rowSalas['Aredes'];

			// --> Obtener el maestro cubiculos
			$sqlCubi = "
			SELECT Habcod, Habcpa, Habzon
			  FROM ".$wbasedatoMov."_000020
			";
			$resCubi = mysql_query($sqlCubi, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCubi):</b><br>".mysql_error());
			while($rowCubi = mysql_fetch_array($resCubi))
			{
				$arrayCubi[$rowCubi['Habcod']]['Zona'] = $rowCubi['Habzon'];
				$arrayCubi[$rowCubi['Habcod']]['Desc'] = $rowCubi['Habcpa'];
			}

			// --> Obtener el cco de urgencias
			$sqlCcoUrg = "
			SELECT Ccocod
			  FROM ".$wbasedatoMov."_000011
			 WHERE Ccourg = 'on'
			   AND Ccoest = 'on'
			";
			$resCcoUrg = mysql_query($sqlCcoUrg, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoUrg):</b><br>".mysql_error());
			if($rowCcoUrg = mysql_fetch_array($resCcoUrg))
			{
				$ccoUrgencias = $rowCcoUrg['Ccocod'];

				// --> Consultar los pacientes con asignacion de camilla en el periodo seleccionado
				$sqlUsoCam = "
				SELECT Mtrsal, Mtrccu, Mtrfac, Mtrhac, Mtrhis, Mtring
				  FROM ".$wbasedatoHce."_000022
				 WHERE (Fecha_data BETWEEN '".$fechaInicial."' AND '".$fechaFinal."')
				   AND Mtrcci = '".$ccoUrgencias."'
				   AND Mtrest = 'on'
				   AND (Mtrccu != '' OR Mtrsal != '')
				 ORDER BY Mtrsal, Mtrccu
				";
				$resUsoCam = mysql_query($sqlUsoCam, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUsoCam):</b><br>".mysql_error());
				while($rowUsoCam = mysql_fetch_array($resUsoCam, MYSQL_ASSOC))
				{
					$codCubiculo 	= ((trim($rowUsoCam['Mtrccu']) == '') ? 'SIN_CUBICULO' : (trim($rowUsoCam['Mtrccu'])));
					$zona 			= (($codCubiculo != 'SIN_CUBICULO') ? $arrayCubi[$codCubiculo]['Zona'] : $rowUsoCam['Mtrsal']);

					// --> Agrupar la información
					$arrayInfo[$zona][$codCubiculo][] = $rowUsoCam;
				}

				// --> Obtener la fecha y hora de salida de la camilla
				foreach($arrayInfo as $codSala => $infoSala)
				{
					foreach($infoSala as $codCamilla => $infoCamilla)
					{
						if($codCamilla == 'SIN_CUBICULO')
							continue;

						foreach($infoCamilla as $index => $infoPaciente)
						{
							// --> Consultar si el paciente salió hacia hospitalización
							$sqlIngHos = "
							SELECT Fecha_data, Hora_data
							  FROM ".$wbasedatoMov."_000017
							 WHERE Eyrhis = '".$infoPaciente['Mtrhis']."'
							   AND Eyring = '".$infoPaciente['Mtring']."'
							   AND Eyrsor = '".$ccoUrgencias."'
							   AND Eyrtip = 'Entrega'
							   AND Eyrest = 'on'
							";
							$resIngHos = mysql_query($sqlIngHos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlIngHos):</b><br>".mysql_error());
							if($rowIngHos = mysql_fetch_array($resIngHos))
							{
								$arrayInfo[$codSala][$codCamilla][$index]['Fecha_salida'] 	= $rowIngHos['Fecha_data'];
								$arrayInfo[$codSala][$codCamilla][$index]['Hora_salida'] 	= $rowIngHos['Hora_data'];
							}
							else
							{
								// --> Consultar si salió por alta
								$sqlAlta = "
								SELECT Ubifad, Ubihad
								  FROM ".$wbasedatoMov."_000018
								 WHERE Ubihis = '".$infoPaciente['Mtrhis']."'
							       AND Ubiing = '".$infoPaciente['Mtring']."'
								   AND Ubisac = '".$ccoUrgencias."'
								   AND Ubiald = 'on'
								";
								$resAlta = mysql_query($sqlAlta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAlta):</b><br>".mysql_error());
								if($rowAlta = mysql_fetch_array($resAlta))
								{
									$arrayInfo[$codSala][$codCamilla][$index]['Fecha_salida'] 	= $rowAlta['Ubifad'];
									$arrayInfo[$codSala][$codCamilla][$index]['Hora_salida'] 	= $rowAlta['Ubihad'];
								}
								else
								{
									// --> El paciente no registra ningun tipo de salida
									unset($arrayInfo[$codSala][$codCamilla][$index]);
								}
							}
						}
					}
				}

				// --> Pintar reporte
				echo "
				<fieldset align='center' style='padding:15px;width:630px'>
					<legend class='fieldset'>Resultados consulta:</legend>
					<table width='97%'>
						<tr align='center'>
							<td></td><td class='encabezadoTabla'>Camilla</td><td class='encabezadoTabla'>Horas de ocupaci&oacute;n<br><span style='font-size:12px'>h:m:s</span></td><td class='encabezadoTabla'>Total usuarios</td><td class='encabezadoTabla'>Promedio<br><span style='font-size:12px'>h:m:s</span></td>
						</tr>
						";
				if(count($arrayInfo) > 0)
				{
					$colorF 		= 'fila2';
					$primeraVez 	= true;
					$tiempoTotGen 	= 0;
					$totalPacGen	= 0;

					foreach($arrayInfo as $codSala => $infoSala)
					{
						$tiempoTotSala 	= 0;
						$totalPacSala	= 0;

						$html = "
							<tr>
								<td style='border: 1px solid #e0e0e0;cursor:pointer;' onClick='desplegar(\"".$codSala."\")' class='fondoAmarillo' colspan='5'>
									&nbsp;&nbsp;<img id='img".$codSala."' width='' height='' src='../../images/medical/hce/".(($primeraVez) ? "menos" : "mas").".PNG'>
									<b>&nbsp;&nbsp;".$codSala."-".$arraySalas[$codSala]."</b>
								</td>
							</tr>";

						foreach($infoSala as $codCamilla => $infoCamilla)
						{
							if(count($infoCamilla) == 0)
							{
								$pintar = false;
								continue;
							}
							else
								$pintar = true;

							$tiempoCamilla = 0;

							if($codCamilla != 'SIN_CUBICULO')
							{
								foreach($infoCamilla as $index => $infoPaciente)
								{
									$tiempoIni = strtotime($infoPaciente['Mtrfac'].' '.$infoPaciente['Mtrhac']);
									$tiempoFin = strtotime($infoPaciente['Fecha_salida'].' '.$infoPaciente['Hora_salida']);

									if($tiempoFin > $tiempoIni)
										$tiempoCamilla+= $tiempoFin-$tiempoIni;
								}
							}

							$tiempoTotSala+=$tiempoCamilla;
							$totalPacSala+=count($infoCamilla);

							$colorF 	= (($colorF == 'fila2') ? 'fila1' : 'fila2' );
							$nomCamilla	= ($codCamilla == 'SIN_CUBICULO') ? 'SIN CAMILLA' : $codCamilla.'</b>&nbsp;-&nbsp;'.$arrayCubi[$codCamilla]['Desc'];
							$html.= "
							<tr class='".$codSala."' style='".(($primeraVez) ? "" : "display:none")."'>
								<td width='3%'></td><td class='".$colorF."' align='center'><b>".$nomCamilla."</td>
								<td class='".$colorF."' align='center'>".floor($tiempoCamilla/3600).gmdate(":i:s", $tiempoCamilla)."</td>
								<td class='".$colorF."' align='center'>".count($infoCamilla)."</td>
								<td class='".$colorF."' align='center'>".floor(($tiempoCamilla/count($infoCamilla))/3600).gmdate(":i:s", $tiempoCamilla/count($infoCamilla))."</td>
							</tr>";
						}

						$colorF = (($colorF == 'fila2') ? 'fila1' : 'fila2' );
						$html.= "
							<tr class='".$codSala."' style='font-weight:bold;".(($primeraVez) ? "" : "display:none")."'>
								<td width='3%'></td><td class='".$colorF."' align='center' style='font-size: 11pt;'>TOTAL SALA	:</td>
								<td class='".$colorF."' align='center' style='font-size: 11pt;border-top: 1px solid #000000;'>".floor($tiempoTotSala/3600).gmdate(":i:s", $tiempoTotSala)."</td>
								<td class='".$colorF."' align='center' style='font-size: 11pt;border-top: 1px solid #000000;'>".$totalPacSala."</td>
								<td class='".$colorF."' align='center' style='font-size: 11pt;border-top: 1px solid #000000;'>".floor(@($tiempoTotSala/$totalPacSala)/3600).gmdate(":i:s", @($tiempoTotSala/$totalPacSala))."</td>
							</tr>
							<tr style='".(($primeraVez) ? "" : "display:none")."' class='".$codSala."'><td>&nbsp;</td></tr>";

						if($pintar)
						{
							echo $html;
							$tiempoTotGen+=$tiempoTotSala;
							$totalPacGen+=$totalPacSala;
						}

						$primeraVez = false;
					}

					// --> Total general
					echo "
					<tr>
						<td width='3%'></td>
						<td class='encabezadoTabla' align='center' style='font-size: 11pt;'>TOTAL GENERAL:</td>
						<td class='encabezadoTabla' align='center' >".floor($tiempoTotGen/3600).gmdate(":i:s", $tiempoTotGen)."</td>
						<td class='encabezadoTabla' align='center' >".$totalPacGen."</td>
						<td class='encabezadoTabla' align='center' >".floor(@($tiempoTotGen/$totalPacGen)/3600).gmdate(":i:s", @($tiempoTotGen/$totalPacGen))."</td>
					</tr>";
				}
				else
					echo "<tr><td class='fila1' colspan='4' align='center'><b>Sin resultados...</b></td></tr>";

				echo "
					</table>
				</fieldset>
				";
				// echo "<pre>";
				// print_r($arrayInfo);
				// echo "</pre>";
			}
			else
			{
				$respuesta["Error"]	= true;
				$respuesta["Html"] 	= "<b>No existe un centro de costos para el servicio de urgencias.</b>";
			}

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
	  <title>Reporte Ocupaci&oacute;n camillas</title>
	</head>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	$(function(){
		// --> Parametrización del datapicker
		cargar_elementos_datapicker();
		activarSeleccionadorFecha("fechaInicial");
		activarSeleccionadorFecha("fechaFinal");
	});

	//--------------------------------------------------------
	//	--> Activar datapicker
	//---------------------------------------------------------
	function cargar_elementos_datapicker()
	{
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
	}

	//-----------------------------------------------------
	//	--> Realizar consulta del reporte
	//-----------------------------------------------------
	function consultar()
	{
		if($("#fechaInicial").val() == '')
		{
			alert("Debe seleccionar la fecha inicial.");
			return;
		}
		if($("#fechaFinal").val() == '')
		{
			alert("Debe seleccionar la fecha final.");
			return;
		}
		if((Date.parse($("#fechaInicial").val())) > (Date.parse($("#fechaFinal").val())))
		{
			alert("La fecha inicial no puede ser mayor que la final.");
			return;
		}

		$("#botonConsultar").attr("disabled", "disabled");
		$("#botonConsultar").html("Consultando... <img style='cursor:pointer;' width='20' height='20' src='../../images/medical/ajax-loader11.gif'>");
		$("#resConsulta").html("<h3><b>Espere un momento... </b></h3><img style='cursor:pointer;' src='../../images/medical/ajax-loader11.gif'>");

		$.post("repOcupacionCamillas.php",
		{
			consultaAjax:     	'',
			wemp_pmla:        	$('#wemp_pmla').val(),
			accion:           	'consultar',
			fechaInicial:		$("#fechaInicial").val(),
			fechaFinal:			$("#fechaFinal").val()
		}, function (data){
			$("#resConsulta").html(data);
			$("#botonConsultar").removeAttr("disabled");
			$("#botonConsultar").text("Consultar");
		});
	}

	//-----------------------------------------------------
	//	--> Ver u ocultar detalle por sala
	//-----------------------------------------------------
	function desplegar(sala)
	{
		$("."+sala).toggle();
		var imagen = $("#img"+sala).attr("src");
		if(imagen == "../../images/medical/hce/mas.PNG")
			$("#img"+sala).attr("src", "../../images/medical/hce/menos.PNG");
		else
			$("#img"+sala).attr("src", "../../images/medical/hce/mas.PNG");
	}

	//-----------------------------------------------------
	//	--> Le activa un seleccionador de fecha a un input
	//-----------------------------------------------------
	function activarSeleccionadorFecha(elemento)
	{
		$("#"+elemento).datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			maxDate:"+0D"
			//defaultDate: periodoAct+"-01"
		});
		$("#"+elemento).next().css({"cursor": "pointer"}).attr("title", "Seleccione");
		$("#"+elemento).after("&nbsp;");
	}
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>




<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		.fila1
		{
			background-color: #C3D9FF;
			color: #000000;
			font-size: 9pt;
			padding:2px;
		}
		.fila2
		{
			background-color: #E8EEF7;
			color: #000000;
			font-size: 9pt;
			padding:3px;
		}
		// --> Estylo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		/*Firefox*/
		[tipo=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}
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
	encabezado("Ocupaci&oacute;n camillas", $wactualiz, 'clinica');

	// --> Selecionar periodo a consultar
	echo "
	<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>
	<div align='center'><br>
		<fieldset align='center' style='width:500px;padding:15px;'>
			<legend class='fieldset'>Seleccione el periodo a consultar:</legend>
			<table width='95%'>
				<tr style='color: #000000;font-size: 10pt;'>
					<td ><b>Fecha inicial:</b>&nbsp;</td>
					<td align='center'><input type='text' size='12' tipo='obligatorio' placeholder=' ' id='fechaInicial' 	disabled='disabled'></td>
					<td ><b>Fecha final:</b>&nbsp;</td>
					<td align='center'><input type='text' size='12' tipo='obligatorio' placeholder=' ' id='fechaFinal'		disabled='disabled'></td>
				</tr>
				<tr>
					<td colspan='4' align='center'><br><button id='botonConsultar' style='cursor:pointer;font-family: verdana;font-size: 10pt;' onclick='consultar()'>Consultar</button></td>
				</tr>
				</tr>
			</table>
		</fieldset>
		<br><br>
		<div id='resConsulta'>
		</div>
		<br>
		<button style='cursor:pointer;font-family: verdana;font-size: 10pt;' onclick='window.close();'>Cerrar Ventana</button>
	</div>
	";


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
