<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2021-11-12';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
// MODIFICAICONES
// 2021-11-12 - Juan David Rodriguez: Modificación de tilde en encabezado.             
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
	//header('Content-type: text/html;charset=ISO-8859-1');
	
	

	include_once("root/comun.php");
	

	
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
	$wfecha			= date("Y-m-d");   
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	


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
		case 'generarReporte':
		{
			$respuesta 			= array('Error' => FALSE, 'Mensaje' => '', 'Html' => '');
			$wbasedatoCliame 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
			
			$respuesta['Html'] = "
			<table width='100%' id='tablaDatosReporte'>
				<tr class='encabezadoTabla' align='center'>
					<td>Tipo de registro</td>
					<td>Consecutivo de registro</td>
					<td>Tipo de identificación</td>
					<td>Identificación</td>
					<td>Fecha de nacimiento</td>
					<td>Sexo</td>
					<td>Primer Apellido</td>
					<td>Segundo Apellido</td>
					<td>Primer Nombre</td>
					<td>Segundo Nombre</td>
					<td>Codigo Entidad</td>
					<td>Nombre Entidad</td>
					<td>EAPB</td>
					<td>Municipio de residencia</td>
					<td>Procedimiento quirúrgico</td>
					<td>Fecha de solicitud</td>
					<td>Fecha de programación</td>
					<td>Se realizó</td>
					<td>Causa no realización</td>
					<td>Se reprogramó</td>
				</tr>
			";
			
			// --> Obtener maestro de cancelaciones
			$maestroCancelaciones = array();
			$sqlCance = "
			SELECT Cancod, Candes
			  FROM ".$wbasedato."_000001
			 WHERE Canest = 'on'
			";
			$resCance = mysql_query($sqlCance, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCance):</b><br>".mysql_error());
			while($rowCance = mysql_fetch_array($resCance))
				$maestroCancelaciones[$rowCance['Cancod']] = $rowCance['Candes'];
			
			// --> Obtener maestro de empresas
			$maestroEmpresas = array();
			$sqlEmpresas = "
			SELECT Empcod, Empcmi, Empnom
			  FROM ".$wbasedatoCliame."_000024
			 WHERE Empest = 'on'
			";
			$resEmpresas = mysql_query($sqlEmpresas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEmpresas):</b><br>".mysql_error());
			while($rowEmpresas = mysql_fetch_array($resEmpresas))
			{
				$maestroEmpresas[$rowEmpresas['Empcod']]['EAPB'] 	= $rowEmpresas['Empcmi'];
				$maestroEmpresas[$rowEmpresas['Empcod']]['NOMBRE'] 	= utf8_encode($rowEmpresas['Empnom']);
			}
			// --> Consultar informacion de las cx
			$sqlCx = "
			SELECT Turtdo, Turdoc, Turfna, Tursex, Tureps, Turndt, '' AS 	Mcacau, Turfec, 			Pacap1, Pacap2, Pacno1, Pacno2, Turnom, Paciu ,'1' realizo, '2' reprogramo, Enlpro 
			  FROM ".$wbasedato."_000011 AS A LEFT JOIN ".$wbasedatoCliame."_000100 ON(Turdoc = Pacdoc AND Turtdo = Pactdo)
				   LEFT JOIN  ".$wbasedatoCliame."_000199 AS C ON(Turtur = Enltur)
			 WHERE Turfec BETWEEN '".$fechaIni."' AND '".$fechaFin."'
			   AND Turdoc NOT IN('P', '1', '0')
			 GROUP BY Turtur
			 UNION
			SELECT Mcatdo, Mcadoc, Mcafna, Mcasex, Mcaeps, Mcandt, 			Mcacau, Mcafec AS Turfec, Pacap1, Pacap2, Pacno1, Pacno2, Mcanom, Paciu ,'2' realizo, '2' reprogramo, '' Enlpro 
			  FROM ".$wbasedato."_000007 AS A LEFT JOIN ".$wbasedatoCliame."_000100 ON(Mcadoc = Pacdoc AND Mcatdo = Pactdo)
			 WHERE A.Fecha_data BETWEEN '".$fechaIni."' AND '".$fechaFin."'
			   AND A.Fecha_data = Mcafec
			   AND Mcadoc NOT IN('P', '1', '0')
			 ORDER BY Turfec
			";
			$resCx 	= mysql_query($sqlCx, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCx):</b><br>".mysql_error());
			$consec	= 0;
			while($rowCx = mysql_fetch_array($resCx))
			{
				$colorFila 	= (($colorFila == 'fila1') ? 'fila2' : 'fila1');
				
				if(trim($rowCx["Tursex"]) == "M")
					$sexo = "H";
				elseif(trim($rowCx["Tursex"]) == "F")
						$sexo = "M";
					else
						$sexo = trim($rowCx["Tursex"]);
					
				if($rowCx["Pacno1"] == "" || $rowCx["Pacap1"] == "")
				{
					$nombre 			= explode(" ", $rowCx["Turnom"]);
					
					if(count($nombre) >= 4)
					{
						$rowCx["Pacno1"] 	= $nombre[0];
						$rowCx["Pacno2"] 	= $nombre[1];
						$rowCx["Pacap1"] 	= $nombre[2];
						$rowCx["Pacap2"] 	= $nombre[3];
					}
					else
					{
						$rowCx["Pacno1"] 	= $nombre[0];
						$rowCx["Pacno2"] 	= "";
						$rowCx["Pacap1"] 	= $nombre[1];
						$rowCx["Pacap2"] 	= $nombre[2];
					}
				}	
				
				// --> Homologar causa de cancelacion, 1: Atribuible a la institución 2: Atribuible al usuario 3: Por orden médica 
				if($rowCx['Mcacau'] != '')
				{
					$tiposCancelacion = explode('/', $maestroCancelaciones[$rowCx['Mcacau']]);
					
					switch(trim($tiposCancelacion[0]))
					{
						case 'PACIENTE':
						{
							$tiposCancelacion = '2';
							break;
						}
						case 'MEDICO':
						{
							$tiposCancelacion = '3';
							break;
						}
						default:
						{
							$tiposCancelacion = '1';
							break;
						}
					}
				}
				else
					$tiposCancelacion = '';
				
				// --> Si el codido del procedimiento realizado es un codigo de paquete, se busca el correspondiente cups a ese paquete
				if(strpos($rowCx["Enlpro"], "CP") >= 0)
				{
					$sqlCodPaq = "
					SELECT Paqdetpro
					  FROM ".$wbasedatoCliame."_000114
					 WHERE Paqdetcod = '".$rowCx["Enlpro"]."'
					   AND Paqdetest = 'on'
					   AND Paqdetgen = 'on'
					 GROUP BY Paqdetpro
					 LIMIT 1
					";
					$resCodPaq = mysql_query($sqlCodPaq, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCodPaq):</b><br>".mysql_error());
					if($rowCodPaq = mysql_fetch_array($resCodPaq))						
						$rowCx["Enlpro"] = $rowCodPaq['Paqdetpro'];
				}
				
				$respuesta['Html'].= "
				<tr class='".$colorFila."'>
					<td>4</td>
					<td>".++$consec."</td>
					<td>".$rowCx["Turtdo"]."</td>
					<td>".strtoupper($rowCx["Turdoc"])."</td>
					<td>".$rowCx["Turfna"]."</td>
					<td>".$sexo."</td>
					<td>".utf8_encode(strtoupper($rowCx["Pacap1"]))."</td>
					<td>".utf8_encode(strtoupper($rowCx["Pacap2"]))."</td>
					<td>".utf8_encode(strtoupper($rowCx["Pacno1"]))."</td>
					<td>".utf8_encode(strtoupper($rowCx["Pacno2"]))."</td>
					<td>".trim($rowCx["Tureps"])."</td>
					<td>".$maestroEmpresas[trim($rowCx["Tureps"])]['NOMBRE']."</td>
					<td>".$maestroEmpresas[trim($rowCx["Tureps"])]['EAPB']."</td>
					<td>".strtoupper($rowCx["Paciu"])."</td>
					<td>".strtoupper($rowCx["Enlpro"])."</td>
					<td>".$rowCx["Turndt"]."</td>
					<td>".$rowCx["Turfec"]."</td>
					<td>".$rowCx["realizo"]."</td>
					<td>".$tiposCancelacion."</td>
					<td>".$rowCx["reprogramo"]."</td>
				</tr>
				";
			}
			
			if(mysql_num_rows($resCx) == 0)
				$respuesta['Html'].= "<tr><td colspan='18' align='center' class='fila1'>Sin registros</td></tr>";
			
			$respuesta['Html'].= "
			</table>
			";
			
			echo json_encode($respuesta);
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
	<title>Reporte resoluci&oacute;n 0256 de 2016</title>
	</head>
	
		<meta charset="UTF-8">
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/print.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	$(function(){
		// --> Parametrización del datapicker
		cargar_elementos_datapicker();
		// --> Activar datapicker
		$("#fechaIni").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
			}
		});
		
		$("#fechaFin").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
			}
		});
		
		$("#botonExportar").click(function(e) {
			
			$("#botonExportar").html("Exportando...").attr("disabled", "disabled");
			
			var html = "<table>";			
			primerTr = true;
			
			$('#tablaDatosReporte tr').each(function(){
				if(primerTr)
					primerTr = false;
				else
					html+= "<tr>"+$(this).html()+"</tr>"; 
			});
			
			html+= "</table>";
			
			window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
			e.preventDefault();
			setTimeout( function(){	
				$("#botonExportar").html("Exportar <img width='15px' height='15px' src='../../images/medical/root/export_to_excel.gif'>").removeAttr("disabled");
			}, 1000);
		});
	});
		
	//--------------------------------------------------------
	//	--> consulta la informacion para el reporte
	//---------------------------------------------------------
	function generarReporte()
	{
		if($("#fechaIni").val() > $("#fechaFin").val())
		{
			alert("La fecha fin debe ser mayor a la fecha inicio.");
			return;
		}	
		
		$("#consultar").text("Consultando...").attr("disabled", "disabled");
		$.post("rep_resolucion_0256_2016.php",
		{
			consultaAjax:   		'',
			accion:         		'generarReporte',
			wemp_pmla:        		$('#wemp_pmla').val(),
			fechaIni:				$("#fechaIni").val(),
			fechaFin:				$("#fechaFin").val()			
		}, function(respuesta){
			$("#consultar").text("Consultar").removeAttr("disabled");
			if(respuesta.Error)
			{
				alert(respuesta.Mensaje);
			}
			else
			{
				$("#datosReporte").html(respuesta.Html);
				$("#fieldsetInf").show();
				$("#tdExportar").show();
			}
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Generar excel
	//---------------------------------------------------------
	function exportar()
	{
		ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
		ventana.document.open();
		
		var contenido = "header('Content-Type: application/vnd.ms-excel');header('Expires: 0');header('Cache-Control: must-revalidate, post-check=0, pre-check=0');header('content-disposition: attachment;filename=NOMBRE.xls');"
						+"<html><head></head><body>"
						+$("#datosReporte").html();
						+"</body></html>";
		ventana.document.write(contenido);
	}
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
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		.fila1
		{
			background-color: 	#C3D9FF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fila2
		{
			background-color: 	#E8EEF7;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		fieldset{
			border: 	2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		#tooltip{font-family: verdana;font-weight:normal;color: #ffffff;font-size: 7pt;position:absolute;z-index:3000;border:1px solid #000000;background-color:#000000;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}
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
	encabezado("Reporte resoluci&oacute;n 0256 de 2016", $wactualiz, 'clinica');
	echo "
	<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>
	<div align='center'>
		<fieldset align='center' style='padding:6px;width:35%'>
			<legend class='fieldset'>Seleccione el periodo a consultar:</legend>
			<table width='100%'>
				<tr>
					<td class='fila1'>Fecha inicio:&nbsp;</td>
					<td class='fila2' align='center'><input type='text' id='fechaIni' value='".date("Y-m-d")."' style='width:80'></td>
					<td class='fila1'>Fecha fin:&nbsp;</td>
					<td class='fila2' align='center'><input type='text' id='fechaFin' value='".date("Y-m-d")."' style='width:80'></td>
				<tr>
				<tr>
					<td align='center' colspan='4'>
						<button id='consultar' style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='generarReporte()'>Consultar</button>
					</td>
				</tr>
				<tr>
					<td align='right' colspan='4' id='tdExportar' style='display:none'>
						<span id='botonExportar' style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;'>Exportar <img width='15px' height='15px' src='../../images/medical/root/export_to_excel.gif'></span>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset id='fieldsetInf' align='center' style='padding:15px;width:100%;display:none'>
			<legend class='fieldset'>Información cx:</legend>
			<div id='datosReporte' align='center'>
			</div>
		</fieldset>
		<br>
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
