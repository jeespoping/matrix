<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Monitor para visulaizar el estado de una factura electronica y para imprimir la representacion grafica
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2019-11-29';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
/*	2019-11-22: Jerson Trujillo. 	Se muestra todo el json de respuesta del web service de carvajal.
									Si hay una respuesta de error y han pasado 15 minutos se activo boton de reenviar 
									solo para usuarios con permiso
	2019-02-07:	Jerson Trujillo. 	Se coloca boton para prender y apagar cron              
*/
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------
//modificacion jaime mejia
if(!isset($_SESSION['user']) && !isset($_GET['automatizacion_pdfs']))
{
    if(isset($accion))
	{
		$respuesta['error'] = true;
		$respuesta['msj'] 	= 'Primero recargue la página principal de Matrix ó inicie sesión nuevamente, para poder relizar esta acción.';
		
		echo json_encode($respuesta);
		return;		
	}
	else
	{
		echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
					[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
				</div>';
		return;
	}
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	$consultaAjax	= '';
	include_once("root/comun.php");
	$conex 			= obtenerConexionBD("matrix");
	
	include_once("ips/funcionesE-fac.php");

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//---------------------------------------------------------
	// --> 
	//---------------------------------------------------------

	function txtLog($txt, $append=true)
    {
        try {
                $l = date('H:i:s', time()) . ' ' . $txt . "\n";
				if ($append)
					file_put_contents('log_la.txt', $l,FILE_APPEND);
				else
					file_put_contents('log_la.txt', $l);
        } catch (Exception $e) {
        }
    } 

//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	$respuesta 	= array("error" => false, "msj" => "");
	
	switch($accion)
	{
		case 'apagarCron':
		{
			$newEst = ($estadoAct == 'on') ? 'off' : 'on';
			$sqlCron = "
			UPDATE root_000051
			   SET Detval = '".$newEst."'
			 WHERE Detemp = '".$wemp_pmla."'
			   AND Detapl = 'ejecutarCronFacturacionElec'
			";
			mysql_query($sqlCron, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCron):</b><br>".mysql_error());
			
			// --> Guargar log
			$sqlLogCron = "
			UPDATE root_000051
			   SET Detdes = CONCAT(Detdes, 'Estado:".$newEst.",FechaHora:".date("Y-m-d H:i:s").",Usuario:".$wuse."|')
			 WHERE Detemp = '".$wemp_pmla."'
			   AND Detapl = 'logCronFacturacionElectronica'
			";
			mysql_query($sqlLogCron, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogCron):</b><br>".mysql_error());
			
			break;
		}
		case 'reenviarDoc':
		{
			generarDocumentosXml(array($wemp_pmla), $fuente, $documento);
			enviarDocumentosCenFinanciero(array($wemp_pmla), $fuente, $documento);
			
			$sqlUpd = "
			UPDATE root_000122
			   SET Faevac = 'off',
				   Faeace = 'off'		
			 WHERE Faeemp = '".$wemp_pmla."'
			   AND Faefue = '".$fuente."'			 
			   AND Faedoc = '".$documento."'			 
			";
			mysql_query($sqlUpd, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpd):</b><br>".mysql_error());
			
			break;
		}
		case 'descargarRepGrafica':
		{
			$respuesta 	= wsDownload($wemp_pmla, $fuente, $documento);
			$respuesta	= json_decode($respuesta, true);
			
			if(!$respuesta['error']){
				
				$ruta 	= str_replace("/var/www/matrix/ips/", "../../../matrix/ips/", $respuesta['ruta']);
				$ruta2 	= $_SERVER['HTTP_HOST']."/".str_replace("/var/www/", "", $respuesta['ruta']);
				
				$respuesta['ruta'] = trim($ruta);
				$respuesta['html'] = "
					<object type='application/pdf' data='".$ruta."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 style='width:100%;height:80%'>"
					  ."<param name='src' value='' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
					  ."<p style='text-align:center; width: 60%;'>"
						."Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />"
						."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
						  ."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
						."</a>"
					  ."</p>"
					."</object>
					<br>
				";
			}
			break;
		}
		case 'actualizarMonitor':
		{
			$usuBtnCron = consultarAliasPorAplicacion($conex, $wemp_pmla, 'usuariosQueApaganCronFacElec');
			$usuBtnCron = explode(",", $usuBtnCron);
			
			if(in_array($wuse, $usuBtnCron))	
				$verBtnReenviar = true;
			else
				$verBtnReenviar = false;
	
			if(!isset($pagina) || $pagina == '')
				$pagina = 1;
			
			// --> Obtener maestro de estados
			$arrEstados = array();
			$sqlEstados = "
			SELECT Estval, Estdes
			  FROM root_000123
			 WHERE Estest = 'on'
			";
			$resEstados = mysql_query($sqlEstados, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstados):</b><br>".mysql_error());
			while($rowEstados = mysql_fetch_array($resEstados, MYSQL_ASSOC)){
				$arrEstados[$rowEstados['Estval']] = utf8_encode($rowEstados['Estdes']);
			}
			
			$fuente		= ((trim($fuente) == "") ? "%" : $fuente);
			$fechaIni	= ((trim($documento) != "") ? "%" : $fechaIni);			
			$documento	= ((trim($documento) == "") ? "%" : $documento);	
			$cco 		= ((trim($cco) == "") ? "%" : $cco);
			$sinAceptar = ($sinAceptar == "true") ? "AND Faevac = 'on' AND Faeace != 'on' " : "";
			
			// --> Obtener los documentos para paginar
			$arrayDoc = array();
			$sqlDocP  = "
			SELECT id
			  FROM root_000122
			 WHERE Fecha_data 	LIKE 	'".$fechaIni."'
			   AND Faeemp 		= 		'".$wemp_pmla."'
			   AND Faefue 		LIKE  	'".$fuente."' 			   
			   AND Faedoc 		LIKE  	'".$documento."' 			   
			   AND Faeccf 		LIKE  	'".$cco."' 			   
			   AND Faepre 		LIKE  	'".$prefijo."'
			   ".$sinAceptar ."
			   AND Faeest 		= 		'on'
			 ORDER BY Fecha_data, Hora_data DESC
			";
			$resDocP = mysql_query($sqlDocP, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDocP):</b><br>".mysql_error());
			while($rowDocP = mysql_fetch_array($resDocP))
				$arrayDoc[] = $rowDocP['id'];
			
			$respuesta["sqlDocP"] 			= $sqlDocP;
			$respuesta["verFiltro"] 		= $verBtnReenviar;
			
			// --> id a paginar
			$inicio = ($pagina*10)-10; 
			$in 	= "";
			for($x=$inicio; $x<=$inicio+9 && $x < count($arrayDoc); $x++)
				$in.=(($in == "") ? $arrayDoc[$x] : ", ".$arrayDoc[$x]);
				
			// --> Obtener documentos
			$arrayFac = array();
			if($in != ""){
				$sqlDoc = "
				SELECT *
				  FROM root_000122
				 WHERE id IN(".$in.")
				 ORDER BY Fecha_data, Hora_data DESC
				";
				$resDoc = mysql_query($sqlDoc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDoc):</b><br>".mysql_error());
				$idx 	= 0;
				while($rowDoc = mysql_fetch_array($resDoc))
				{
					$arrayFac[] = $rowDoc;
					$idx++;
				}
			}
			
			if($usuAdm){
				$respuesta["html"] = '					
					<table class="table small table-bordered table-condensed" style="width:100%;">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="text-center bg-danger" 	colspan="6">1. Generación de la factura</th>
								<th scope="col" class="text-center bg-info" 	colspan="4">2. Creación del Xml</th>
								<th scope="col" class="text-center bg-success" 	colspan="6">3. Envío al Cen financiero</th>
							</tr>
							<tr>
								<th scope="col" class="text-center bg-danger">Fecha</th>
								<th scope="col" class="text-center bg-danger">Fuente</th>
								<th scope="col" class="text-center bg-danger">Documento</th>
								<th scope="col" class="text-center bg-danger">Prefijo</th>
								<th scope="col" class="text-center bg-danger">Cod. Barras</th>
								<th scope="col" class="text-center bg-danger">Cco Fac.</th>
								<th scope="col" class="text-center bg-info">Creado</th>
								<th scope="col" class="text-center bg-info">Fecha</th>
								<th scope="col" class="text-center bg-info">Archivo</th>
								<th scope="col" class="text-center bg-info">Mensaje</th>
								<th scope="col" class="text-center bg-success">Enviado</th>
								<th scope="col" class="text-center bg-success">Fecha</th>
								<th scope="col" class="text-center bg-success">Respuesta</th>
								<th scope="col" class="text-center bg-success">Estado</th>
								<th scope="col" class="text-center bg-success">PDF</th>
								<th scope="col" class="text-center bg-success">Renviar</th>
							</tr>
						</thead>
						<tbody>';
			}
			else{			
				$respuesta["html"] = '					
					<table class="table small table-bordered table-condensed" style="width:100%;">
						<thead class="thead-light">
							<tr>
								<th scope="col" class="text-center bg-success" colspan="7">Información de la factura</th>
							</tr>
							<tr>
								<th scope="col" class="text-center bg-success">Fecha</th>
								<th scope="col" class="text-center bg-success">Fuente</th>
								<th scope="col" class="text-center bg-success">Documento</th>
								<th scope="col" class="text-center bg-success">Prefijo</th>
								<th scope="col" class="text-center bg-success">Cod. Barras</th>
								<th scope="col" class="text-center bg-success">Cco Fac.</th>
								<th scope="col" class="text-center bg-success">PDF</th>
							</tr>
						</thead>
						<tbody>';
			}
					
			$fila = 'fila1';
			foreach($arrayFac as $idx => $infoFac){
				
				$fila 	= ($fila == 'fila2') ? 'fila1' : 'fila2';
				$fila 	= "";
				$imgOn	= '<span class="glyphicon glyphicon-ok text-success" aria-hidden="true"></span>';
				$imgOff	= '<span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>';
				
				// --> Monitor para un usuario administrador
				if($usuAdm){				
								
					$respuesta["html"].= '
					<tr class="'.$fila.'">
						<td>'.$infoFac['Fecha_data'].' '.$infoFac['Hora_data'].'</td>
						<td>'.$infoFac['Faefue'].'</td>
						<td>'.$infoFac['Faedoc'].'</td>
						<td>'.$infoFac['Faepre'].'</td>
						<td class="text-center"><button type="button" class="btn btn-default btn-sm" onClick="imprimirCodBarras(\''.trim($infoFac['Faedoc']).'\')"><span class="glyphicon glyphicon-print" aria-hidden="true"></span></button></span></td>
						<td>'.$infoFac['Faeccf'].'</td>
						<td class="text-center">'.(($infoFac['Faexml'] == 'on') ? $imgOn : $imgOff).'</td>';
					
					if($infoFac['Faexml'] == 'on'){
						$respuesta["html"].= '
						<td>'.$infoFac['Faexmf'].'</td>
						<td class="text-center"><a href="../../../'.str_replace("/var/www/", "", $infoFac['Faedir']).'/'.$infoFac['Faearc'].'" target="_blank"  title="'.$infoFac['Faearc'].'"><span class="btn-sm glyphicon glyphicon-folder-open text-default" aria-hidden="true" ></span></a></td>
						<td>'.$infoFac['Faexme'].'</td>';
					}else{
						$respuesta["html"].= '
						<td></td>
						<td></td>
						<td>'.utf8_encode($infoFac['Faexme']).'</td>';
					}
					
					$respuesta["html"].= '
						<td class="text-center">'.(($infoFac['Faewen'] == 'on') ? $imgOn : $imgOff).'</td>';
						
					if($infoFac['Faewen'] == 'on'){
						
						$msjWs	= "";
						$resWs 	= json_decode($infoFac['Faewsr'], true);
						if(is_array($resWs)){				
							$msjWs = $resWs['status'];
						}
						
						$resWsDE = wsDocumentStatus($wemp_pmla, $infoFac['Faefue'], $infoFac['Faedoc']);
						$resWsDE = json_decode($resWsDE, true);
						
						//<td>'.(($resWsDE['processStatus'] == "FAIL") ? $imgOff.'<small> '.$resWsDE['errorMessage'].'</small>' : $imgOn.'<small> '.$arrEstados[$resWsDE['processName']].'</small>').'</td>
						
						$respuesta["html"].= '
						<td>'.$infoFac['Faewsf'].'</td>
						<td>'.$msjWs.'</td>
						<td>
							<table class="table table-striped small ">
								<tr>
									<td><strong>legalStatus:</strong>&nbsp;'.((/*$resWsDE['processStatus'] == "FAIL" ||*/ $resWsDE['legalStatus'] == "FAIL" || $resWsDE['legalStatus'] == "REJECTED") ? $imgOff : $imgOn)." ".$arrEstados[$resWsDE['legalStatus']].'</td>								
								</tr>
								<tr>								
									<td colspan="2">
										<strong>
											Mensaje Respuesta
											<span style="cursor:pointer" title="Click para ver mas información" class="glyphicon glyphicon-zoom-in text-primary" aria-hidden="true" data-toggle="collapse" data-target="#collapseExample'.$idx.'" aria-expanded="false" aria-controls="collapseExample"></span>
										:</strong>
										&nbsp;'.$resWsDE['errorMessage'].'										
										<div class="collapse" id="collapseExample'.$idx.'"><br>'.print_r($resWsDE, true).'</div>
									</td>
								</tr>								
							</table>
						</td>
						<td class="text-center">';
						
						$respuesta["html"].= '<a class="text-success" style="cursor:pointer" href="javascript:return false;" id="imp_'.$infoFac['Faefue'].'_'.$infoFac['Faedoc'].'" onClick="descargarRepGrafica(\''.$infoFac['Faefue'].'\', \''.$infoFac['Faedoc'].'\', this)"><span class="btn-sm glyphicon glyphicon-cloud-download" aria-hidden="true"></span>Descargar</a>';
						
						/*if(trim($infoFac['Faepdf']) != ""){						;
							$ruta 	= str_replace("/var/www/matrix/ips/", "../../../matrix/ips/", $infoFac['Faedir'])."/".trim($infoFac['Faepdf']);
							$respuesta["html"].= '<a title="Abrir"><span onClick="verRepGrafica(\''.$ruta.'\')" class="btn-sm glyphicon text-primary glyphicon-folder-open" aria-hidden="true" style="cursor:pointer"></span></a>';
						}
						elseif($resWsDE['processStatus'] != "FAIL")
						{													
							if($infoFac['Faenid'] <= 10)
								$respuesta["html"].= '<span class="btn-sm glyphicon text-success glyphicon-hourglass" aria-hidden="true"></span> Esperando descarga. &nbsp;<b>Intento: '.$infoFac['Faenid'].'</b>';
							else
								$respuesta["html"].= '<span class="btn-sm glyphicon text-danger glyphicon-alert" aria-hidden="true"></span> No se pudo descargar despues de <b>'.$infoFac['Faenid'].' intentos.</b>';
							
							$respuesta["html"].= '<br><a class="text-success" style="cursor:pointer" onClick="descargarRepGrafica(\''.$infoFac['Faefue'].'\', \''.$infoFac['Faedoc'].'\', this)"><span class="btn-sm glyphicon glyphicon-cloud-download" aria-hidden="true"></span>Descargar</a>';
						}*/
						
						$respuesta["html"].= '</td>';
						
					}else{
						$respuesta["html"].= '
						<td></td>
						<td></td>
						<td></td>
						<td></td>';
					}
					
					$horaEnvio 	= $infoFac['Faewsf'];
					$horaAct	= date("Y-m-d H:i:s");
					$minutos	= ceil((strtotime($horaAct)-strtotime($horaEnvio)) / 60);
					
					// --> Activar boton de reenviar, si tiene permiso y ya fue consultado el estado y no tiene el aceptado por la DIAN
					//if($verBtnReenviar && $infoFac['Faevac'] == "on" && $infoFac['Faeace'] != "on" && $resWsDE['legalStatus'] != "ACCEPTED ")
					if($verBtnReenviar && $infoFac['Faevac'] == "on" && $infoFac['Faeace'] != "on" && $resWsDE['legalStatus'] != "ACCEPTED " && $resWsDE['errorMessage'] != "ERROR-DI2" && !isset($resWsDE['errorMessage']["ERROR-DI2"]))	
					{
						$respuesta["html"].= '
						<td class="text-center"><button type="button" class="btn btn-success btn-sm" onClick="reenviarDoc(\''.$infoFac['Faefue'].'\', \''.$infoFac['Faedoc'].'\')"><span class="glyphicon glyphicon-repeat" aria-hidden="true"></span></button></td>';
					}
					else{
						$respuesta["html"].= '
						<td></td>';
					}	
					
					$respuesta["html"].= '
					</tr>
					';
				}
				// --> Monitor para un usuario que solo va a imprimir facturas
				else{
					
					$respuesta["html"].= '
					<tr class="'.$fila.'">
						<td>'.$infoFac['Fecha_data'].' '.$infoFac['Hora_data'].'</td>
						<td>'.$infoFac['Faefue'].'</td>
						<td>'.$infoFac['Faedoc'].'</td>
						<td>'.$infoFac['Faepre'].'</td>
						<td class="text-center"><button type="button" class="btn btn-default btn-sm" onClick="imprimirCodBarras(\''.trim($infoFac['Faedoc']).'\')"><span class="glyphicon glyphicon-print" aria-hidden="true"></span></button></span></td>
						<td>'.$infoFac['Faeccf'].'</td>
						<td>
							<a class="text-success" style="cursor:pointer" id="imp_'.$infoFac['Faefue'].'_'.$infoFac['Faedoc'].'" href="javascript:return false;" onClick="descargarRepGrafica(\''.$infoFac['Faefue'].'\', \''.$infoFac['Faedoc'].'\', this)"><span class="btn-sm glyphicon glyphicon-cloud-download" aria-hidden="true"></span>Descargar</a>
						</td>
					</tr>
					';
					
				}					
			}
			
			if(count($arrayFac) == 0){
				$respuesta["html"].= '<tr><td colspan="16" class="text-center">No se encontraron registros</td></tr>';
			}
			
			$respuesta["html"].= '
				<tbody>
			</table>';
			
			$respuesta["totalReg"] = count($arrayDoc);
			$respuesta["totalPag"] = floor(count($arrayDoc)/ 10)+(((count($arrayDoc)%10) > 0) ? 1 : 0);
			$respuesta["totalPag"] = ($respuesta["totalPag"] == 0) ? 1 : $respuesta["totalPag"];
			break;
		}
	}
	
	echo json_encode($respuesta);
	return;
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
	  <title>Monitor e-Factura</title>
	</head>	
		<meta charset="UTF-8">
		
		<script src="../../../include/root/jquery.min.js"></script>
		<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
		<script src="../../../include/root/bootstrap.min.js"></script>
		
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>		
		
		<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
		
		
	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	$(function(){
		// --> Parametrización del datapicker
		cargar_elementos_datapicker();		
		$("#fechaIni").datepicker({});
		actualizarMonitor('p1');
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
	//--------------------------------------------------------
	//	--> Actualizar monitor
	//--------------------------------------------------------
	function actualizarMonitor(idPagina){
		
		if($("#busDocumento").val() != "")
			idPagina = "p1";
		
		if(idPagina == "next")
		{
			$("#prev").next().remove();
			sig = (($("#next").prev().text())*1)+1;
			$("#next").before('<li id="p'+sig+'"><a onClick="actualizarMonitor(\'p'+sig+'\')">'+sig+'</a></li>');
			
			$("#prev").removeClass("disabled").children().attr("onClick", "actualizarMonitor(\'prev\')");
			return;
		}
		
		if(idPagina == "prev")
		{
			$("#next").prev().remove();
			ant = (($("#prev").next().text())*1)-1;
			$("#prev").after('<li id="p'+ant+'"><a onClick="actualizarMonitor(\'p'+ant+'\')">'+ant+'</a></li>');
			if($("#prev").next().text() == "1")
				$("#prev").addClass("disabled").children().attr("onClick", "");
			
			return;
		}	
		
		if(idPagina != ""){
			$("#paginador li[class=active]").attr("class", "");
			$("#"+idPagina).attr("class", "active");
		}
		else
			idPagina = $("#paginador li[class=active]").attr("id");	
		
		$("#modalCosultando").modal('show');
		
		$.post("monitorE-facturacion.php",
		{	
			accion			:   'actualizarMonitor',
			wemp_pmla		:	$('#wemp_pmla').val(),
			usuAdm			:	$('#usuAdm').val(),
			fechaIni		:	$('#fechaIni').val(),
			pagina			: 	$("#"+idPagina).text(),
			cco				:	$("#busCco").val(),
			fuente			:	$("#busFuente").val(),
			documento		:	$("#busDocumento").val(),
			prefijo			:	$("#busPrefijo").val(),
			sinAceptar		:	$("#sinAceptar").prop('checked')

		}, function(respuesta){	
			
			if($("#sinAceptar").prop('checked'))
				checkAcep = "checked";
			else
				checkAcep = "";
			
			$("#modalCosultando").modal('hide');
			if(respuesta.error){
				$("#divMsj").attr("class", "alert alert-danger").text(respuesta.msj).show();
			}
			else{				
				$("#divMsj").hide();
				var htmlDatos = '<h6 class="text-primary"><span style="display:'+((respuesta.verFiltro) ? "" : "none")+'">Documentos sin aceptaci&oacute;n DIAN:&nbsp;&nbsp;<input type="checkbox" class="input-sm-primary" id="sinAceptar" '+checkAcep+'>&nbsp;&nbsp;  |</span>  Total documentos:'+respuesta.totalReg+'  |  Pagina:'+$("#"+idPagina).text()+" / "+respuesta.totalPag+'</h6>'
				$("#divRespuesta").html(respuesta.html).show();				
				$("#tdEstadisticas").html(htmlDatos).show();
				
				if(respuesta.totalPag <= 5)
					$("#next").addClass("disabled").children().attr("onClick", "");
				else
					$("#next").removeClass("disabled").children().attr("onClick", "actualizarMonitor(\'next\')");
			}
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Apagar cron
	//--------------------------------------------------------
	function apagarCron(estadoAct){
		
		$("#modalCosultando").modal('show');
		
		$.post("monitorE-facturacion.php",
		{	
			accion			:   'apagarCron',
			wemp_pmla		:	$('#wemp_pmla').val(),
			estadoAct		:	estadoAct		

		}, function(respuesta){
			if(respuesta.error){
				$("#divMsj").attr("class", "alert alert-danger").text(respuesta.msj).show();
				("#modalCosultando").modal('hide');
			}
			else{				
				$("#divMsj").hide();
				actualizarMonitor('');
				if(estadoAct == "on"){
					$("#btnCron").attr("class", "btn btn-danger");
					$("#btnCron").attr("onClick", "apagarCron('off')");
					$("#btnCron").html('Prender ejecuci&oacute;n del cron <span class="glyphicon glyphicon-off" aria-hidden="true"></span>');
				}
				else{
					$("#btnCron").attr("class", "btn btn-success");
					$("#btnCron").attr("onClick", "apagarCron('on')");
					$("#btnCron").html('Apagar ejecuci&oacute;n del cron <span class="glyphicon glyphicon-off" aria-hidden="true"></span>');
				}					
			}
		}, 'json');
	}
	
	//--------------------------------------------------------------------------------------------------------------------
	//	--> Re-enviar un documento, vuelve a generar el xml y a enviar el doc al cen financiero
	//--------------------------------------------------------------------------------------------------------------------
	function reenviarDoc(fuente, documento){
		
		if(confirm("Está seguro en renviar el documento?")){
			
			$("#modalCosultando").modal('show');
			
			$.post("monitorE-facturacion.php",
			{	
				accion			:   'reenviarDoc',
				wemp_pmla		:	$('#wemp_pmla').val(),
				fuente			:	fuente,
				documento		:	documento

			}, function(respuesta){
				if(respuesta.error){
					$("#divMsj").attr("class", "alert alert-danger").text(respuesta.msj).show();
				}
				else{				
					$("#divMsj").hide();
					actualizarMonitor('');
				}
			}, 'json');
		}
	}
	
	//--------------------------------------------------------
	//	--> Prueba para enviar un documento al CEN
	//--------------------------------------------------------
	function descargarRepGrafica(fuente, documento, elemento){
		
		$("#modalCosultando").modal('show');
		
		$.post("monitorE-facturacion.php",
		{	
			accion			:   'descargarRepGrafica',
			wemp_pmla		:	$('#wemp_pmla').val(),
			fuente			:	fuente,
			documento		:	documento

		}, function(respuesta){
			$("#modalCosultando").modal('hide');
			if(respuesta.error){
				$("#divMsj").attr("class", "alert alert-danger").text(respuesta.msj).show();
			}
			else{				
				$("#divMsj").hide();
				// $("#modalBodyPdf").html(respuesta.html);
				// $("#modalPdf").modal('show');
				window.open(respuesta.ruta);
				// html = '<a title="Abrir"><span onClick="verRepGrafica(\''+respuesta.ruta+'\')" class="btn-sm glyphicon text-primary glyphicon-folder-open" aria-hidden="true" style="cursor:pointer"></span></a>';
				// $(elemento).parent().html(html);
			}
		}, 'json').fail(function(response) {
			//alert('Error: ' + response.responseText);
			alert('Error descargando pdf.');
			$("#modalCosultando").modal('hide');
	
	//--------------------------------------------------------
	//	--> Abrir representacion grafica
	//--------------------------------------------------------
	function verRepGrafica(ruta){		
		window.open(ruta);
	}
	
	//--------------------------------------------------------
	//	--> Imprimir codigo de barras
	//--------------------------------------------------------
	function imprimirCodBarras(numFactu){
		ancho = 450;   alto = 200;
		var winl = (screen.width - ancho) / 2;
		var wint = 300;
		settings = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=no, toolbar=no';
		miPopup = window.open("../../facturacion/procesos/stkFE_02.php?numFactura="+numFactu+"","miwin",settings);
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
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		.titulopagina2
		{
			border-bottom-width: 1px;
			/*border-color: <?=$bordemenu?>;*/
			border-left-width: 1px;
			border-top-width: 1px;
			font-family: verdana;
			font-size: 18pt;
			font-weight: bold;
			height: 30px;
			margin: 2pt;
			overflow: hidden;
			text-transform: uppercase;
		}
		.wn
		{
			font-weight: normal;
		}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY width="100%">
	<?php
	
	// -->	ENCABEZADO
	encabezado("<div class='titulopagina2'>Monitor/Impresi&oacute;n Facturaci&oacute;n electr&oacute;nica</div>", $wactualiz, 'clinica');
	echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";
	
	// --> Consultar cco del usuario
	$cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$sqlCcoUsu = "
	SELECT Cjecco, Cjeadm
	  FROM ".$cliame."_000030
	 WHERE Cjeusu = '".$wuse."'
	";
	$resCcoUsu = mysql_query($sqlCcoUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoUsu):</b><br>".mysql_error());
	if($rowCcoUsu = mysql_fetch_array($resCcoUsu)){
		$ccoUsuario = explode("-", $rowCcoUsu['Cjecco']);
		$ccoUsuario = $ccoUsuario[0];
		$usuAdm 	= (($rowCcoUsu['Cjeadm'] == 'on') ? true : false);
	}
	else{
		$ccoUsuario = "";
		$usuAdm 	= false;
		
		echo '
		<div class="container" style="width:100%;padding:0px;" align="center">
			<div class="alert alert-danger text-center" role="alert" style="width:40%;padding:0px;">
				El usuario '.$wuse.' no tiene un centro de costos asignado ('.$cliame.'_000030).
			</div>
		</div>
		';
	}
	
	echo "<input type='hidden' id='usuAdm' value='".$usuAdm."'>";
	
	// --> Consultar maestro cco
	$arrCco 	= array();
	
	$sqlTablaCco= "
	SELECT Emptcc
	  FROM root_000050
	 WHERE Empcod = '".$wemp_pmla."' 
	   AND Empest = 'on'
	";
	$resTablaCco = mysql_query($sqlTablaCco, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTablaCco):</b><br>".mysql_error());
	if($rowTablaCco = mysql_fetch_array($resTablaCco))
	{
		if($rowTablaCco['Emptcc'] == 'costosyp_000005'){
			$sqlCco = "
			SELECT Ccocod, Cconom as nom
			  FROM ".$rowTablaCco['Emptcc']."
			 WHERE Ccoemp = '".$wemp_pmla."'
			   AND Ccoest = 'on'
			";
		}
		else
		{
			$sqlCco = "
			SELECT Ccocod, Ccodes as nom
			  FROM ".$rowTablaCco['Emptcc']."
			 WHERE Ccoest = 'on'
			";
		}
		$resCco = mysql_query($sqlCco, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCco):</b><br>".mysql_error());
		while($rowCco = mysql_fetch_array($resCco))
			$arrCco[$rowCco['Ccocod']] = substr(utf8_encode($rowCco['nom']), 0, 22).((strlen($rowCco['nom']) > 22) ? ".." : "");
	}	
	
	// --> Consultar prefijos
	$arrPre = array();
	$sqlPre = "
	SELECT Respre
	  FROM root_000124
	 WHERE Resemp = '".$wemp_pmla."'
	   AND Resest = 'on'
	   AND Respre != ''
	";
	$resPre = mysql_query($sqlPre, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPre):</b><br>".mysql_error());
	while($rowPre = mysql_fetch_array($resPre))
		$arrPre[$rowPre['Respre']] = $rowPre['Respre'];
	
	// --> Consultar estado del cron
	$estadoCron = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ejecutarCronFacturacionElec');
	$colorBtn	= (($estadoCron == 'on') ? 'btn-success' : 'btn-danger');
	$textoBtn	= (($estadoCron == 'on') ? 'Apagar ejecuci&oacute;n del cron' : 'Prender ejecuci&oacute;n del cron');
	$usuBtnCron = consultarAliasPorAplicacion($conex, $wemp_pmla, 'usuariosQueApaganCronFacElec');
	$usuBtnCron = explode(",", $usuBtnCron);
	
	if(in_array($wuse, $usuBtnCron))	
		$verBtn = "";
	else
		$verBtn = "none";
	
	?>
	<div class="container" style="width:100%;padding:0px;" align="center">
		<div id="divMsj" class="" role="alert" style="width:90%;display:none;padding:0px;">						
		</div>
		<div class="form-inline text-left" style="width:90%; padding:0px;">
			<br>
			<table style="width:100%; padding:0px;" class="small">
				<tr>
					<td class="text-center" colspan='2' style="width:100%; padding:0px;">
						<form class="form-inline text">
							<b>Filtrar por: &nbsp;&nbsp;</b>
							<div class="input-group">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
								</div>
								<input type="text" style="cursor:pointer" readonly class="form-control input-sm" id="fechaIni" 	value="<?php echo date("Y-m-d") ?>">
							</div>
							<div class="form-group">
								<label for="busPrefijo" class="wn">&nbsp;&nbsp;Prefijo:</label>
								<select class="form-control input-sm" id="busPrefijo">
									<option value="%">Todos</option>
									<?php 
										foreach($arrPre as $pre)
											echo "<option value='".$pre."'>".$pre." (facturas)</option>"
									?>
									<option value="">Notas</option>
								</select>
							</div>
							<div class="form-group">
								<label for="busFuente" class="wn">&nbsp;&nbsp;Fuente:</label>
								<input type="text" class="form-control input-sm" id="busFuente" placeholder="Digite la fuente">
							</div>
							<div class="form-group">
								<label for="busDocumento" class="wn">&nbsp;&nbsp;Documento:</label>
								<input type="text" class="form-control input-sm" id="busDocumento" placeholder="Digite el documento">
							</div>
							<div class="form-group">
								<label for="busCco" class="wn">&nbsp;&nbsp;Centro costos factur&oacute;:</label>
								<select class="form-control input-sm" <?php echo $x = ((!$usuAdm) ? "disabled" : "") ?> id="busCco">
									<option value="">Todos</option>
									<?php 
										foreach($arrCco as $codC => $nomC)
											echo "<option ".(($ccoUsuario == $codC && !$usuAdm) ? "SELECTED" : "")." value='".$codC."'>".$codC."-".$nomC."</option>"
									?>
								</select>
							</div>
							&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-sm" onClick="actualizarMonitor('')">Actualizar <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
						</form>
					</td>
				</tr>
				<tr>
					<td id='tdEstadisticas' class="text-right" style="width:100%;padding:0px;">
					</td>
				</tr>
			</table>
		</div>		
		<div class="container" style="width:90%; padding:0px;">
			<div class="panel-body panel panel-default" id="divRespuesta" style="display:none" data-spy="scroll"></div>			
		</div>		
		<div class="container" style="width:90%; padding:0px;">
			<table style="width:100%; padding:0px;">
				<tr>
					<td class="text-right" style="padding:0px;">
						<button type="button" id="btnCron" class="btn <?php echo $colorBtn ?>" style="display:<?php echo $verBtn ?>" onClick="apagarCron('<?php echo $estadoCron ?>')"><?php echo $textoBtn ?> <span class="glyphicon glyphicon-off" aria-hidden="true"></span></button>
						<nav class="btn" aria-label="Page navigation">
							<ul class="pagination" id="paginador">
								<li id="prev" class="disabled"><a aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
								<li id="p1"><a onClick="actualizarMonitor('p1')">1</a></li>
								<li id="p2"><a onClick="actualizarMonitor('p2')">2</a></li>
								<li id="p3"><a onClick="actualizarMonitor('p3')">3</a></li>
								<li id="p4"><a onClick="actualizarMonitor('p4')">4</a></li>
								<li id="p5"><a onClick="actualizarMonitor('p5')">5</a></li>
								<li id="next"><a onClick="actualizarMonitor('next')" aria-label="Next" ><span aria-hidden="true">&raquo;</span></a></li>
							</ul>
						</nav>
					</td>
				</tr>
			</table>
		</div>	
	</div>
	
	
	<div id="modalCosultando" class="modal fade bs-example-modal-sm" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body" align="center">
					<p>Consultando...<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></p>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade bs-example-modal-lg" id="modalPdf" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Representación Grafica:</h4>
				</div>
				<div class="modal-body" id="modalBodyPdf">
				</div>
			</div>
		</div>
	</div>

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
