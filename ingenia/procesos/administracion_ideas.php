<?php
include_once("conex.php");

	session_start();


	if(!isset($_SESSION['user']) )
	{
		 echo "<br /><br /><br /><br />
				  <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
					  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix ó Inicie sesión nuevamente.
				 </div>";
		  return;
	}
	
	$wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
	
	
	

	include_once("root/comun.php");
	

	$conex = obtenerConexionBD("matrix");
	
	function getDetallesIdea($idIdea, $conex, $modificar=false){
		$q = "	SELECT 
					 i.id, i.Fecha_data, i.Idetit, i.Idedes, i.Idepro, i.Ideres, i.Iderin, i.Iderpe, i.Idereq, i.Ideeje, i.Ideest, i.Ideusu, es.Estdes, es.Estcol, u.Descripcion
				FROM ingenia_000001 i
				INNER JOIN ingenia_000007 es ON es.Estcod = i.Ideest
				INNER join usuarios u ON u.Codigo = i.Ideusu
				WHERE i.id = ".$idIdea;
		$result = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(".$q."):</b><br>".mysql_error());				
		$row = mysql_fetch_assoc($result);			
				
				$recursos = "";
				$recursos .= $row["Iderin"] != "" ? "<b>Infraestructura física:</b> ". $row["Iderin"]." " : "";
				$recursos .= $row["Iderpe"] != "" ? "<b>Personal:</b> ". $row["Iderpe"]." " : "";
				$recursos .= $row["Idereq"] != "" ? "<b>Equipamiento tecnológico:</b> ". $row["Idereq"]." " : "";
				
				//Se organiza el contenido para devolver
				$htmlRespuesta = "
					<table class='table table-condensed'>
						<tr class='encabezadoTabla'>
							<td colspan='3'>Detalles de la idea</td>
						</tr>
				";
				
				if($modificar){
					$varx = array();
					$varx[0] = ($row["Ideeje"] == "Gestion del conocimiento") ? "selected='selected'" : "";
					$varx[1] = ($row["Ideeje"] == "Servicio") ? "selected='selected'" : "";
					$varx[2] = ($row["Ideeje"] == "Tecnologia") ? "selected='selected'" : "";
					$varx[3] = ($row["Ideeje"] == "Sostenibilidad") ? "selected='selected'" : "";
					
					$htmlRespuesta .= "	<tr class='fila1'>
							<td width='30%'><b>Eje de innovación:</b> </td>
							<td width='70%'>
								<input type='hidden' value='".$row["Ideeje"]."' name='ejeModificado'>
								<select class='form-control' id='ejeNuevo' name='ejeNuevo'>									
									<option value='Gestion del conocimiento' ".$varx[0].">Gesti&oacute;n del conocimiento</option>
									<option value='Servicio' ".$varx[1].">Servicio</option>
									<option value='Tecnologia' ".$varx[2].">Tecnolog&iacute;a</option>
									<option value='Sostenibilidad' ".$varx[3].">Sostenibilidad</option>
								</select>	
							</td>
						</tr>
					";			
				}else{
					$htmlRespuesta .= "	<tr class='fila1'>
							<td width='30%'><b>Eje de innovación:</b> </td>
							<td width='70%'>".$row["Ideeje"]."</td>
						</tr>
					";
				}
						
						
				$htmlRespuesta .= "		
						<tr class='fila2'>
							<td><b>Titulo:</b> </td>
							<td>".$row["Idetit"]."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Descripcion:</b> </td>
							<td>".$row["Idedes"]."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Problemas a resolver:</b> </td>
							<td>".$row["Idepro"]."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Resultado esperado:</b> </td>
							<td>".$row["Ideres"]."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Recursos necesarios:</b> </td>
							<td>".$recursos."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Usuario que publica:</b> </td>
							<td>".utf8_encode($row["Descripcion"])."</td>
						</tr>						
						<tr class='fila2'>
							<td><b>Estado actual:</b> </td>
							<td id='estadoActual'>".utf8_encode($row["Estdes"])."</td>
						</tr>
					</table>
				";
				$htmlRespuesta .= "<input type='hidden' id='prevEstado' value='".$row["Ideest"]."' readonly>";
				
		return $htmlRespuesta;
	}
	
	function getArrayEstados($conex){
		//Se crea array con la tabla de estados
		$sqlEstados = "	SELECT 
							Estcod, Estdes
						FROM ingenia_000007	
						ORDER BY Estcod ASC
		";
		$resultEstados = mysql_query($sqlEstados, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlEstados."):</b><br>".mysql_error());				
				
		$arrEstados = array();
		while($row = mysql_fetch_array($resultEstados)){
			$arrEstados[$row["Estcod"]] = utf8_encode($row["Estdes"]);
		}
		
		return $arrEstados;
	}
	
	if(isset($_POST["consultaAjax"])){
		if($_POST["accion"] === "evaluarIdea"){			
			$idIdea = isset($_POST["idIdea"]) ? $_POST["idIdea"] : "";
			
			if($idIdea != ""){	

				$modificar = isset($_POST["modificarEje"]) ? true : false;
			
				$htmlRespuesta = getDetallesIdea($idIdea, $conex, $modificar);
								
				echo json_encode(array("tipo" => "ok", "respuesta" =>$htmlRespuesta));
				exit();				
			}			
		}
				
		//Acción modificar el estado de una idea
		if($_POST["accion"] === "verDetalleEstado"){
			$ididea = isset($_POST["idIdea"]) ? $_POST["idIdea"] : "";
			
			if($idIdea != ""){
				$htmlRespuesta = getDetallesIdea($idIdea, $conex);						
				
				echo json_encode(array("tipo" => "ok", "respuesta" =>$htmlRespuesta));
				exit();				
			}
		}
		
		//Acción guardar resultado evaluación de idea
		if($_POST["accion"] === "guardarEvaluacion"){
			$params = array();
			parse_str($_POST["form_evaluacion"], $params);	

			$idIdea 	   = $params["idIdea"]; 						 //id de la idea
			$diferente     = $params["puntaje_diferente"]; 			 //puntaje como propuesta diferente
			$utilidades    = $params["puntaje_utilidad"]; 			 //puntaje genera utilidades o ahorro
			$ventaja	   = $params["puntaje_ventaja"]; 			 //puntaje ventaja en el mercado
			$impacto 	   = $params["puntaje_impacto"]; 			 //puntaje genera impacto significativo
			$totalpuntos   = $params["total_puntaje"]; 				 //total puntaje
			$descripcion   = $params["puntaje_descriptivo"]; 		 //Resultado descriptivo
			$observaciones = $params["observaciones"]; 			 //obervaciones del evaluador			
			$usuario 	   = $_SESSION["usera"]; //usuario que califica
			$fecha	       = date("Y-m-d");
			$hora	       = date("H:i:s");
			
			//para determinar el nuevo estado de la idea
			if($totalpuntos >= 9){
				$newEstado = "02";
			}else{
				$newEstado = "03"; 
			}
			
			$nuevoEstado  = $params["puntaje_descriptivo"];//$params["nuevoEstado"]; //Según el resultado de la evaluación se tiene un nuevo estado para la idea
			
			if($idIdea != "" && $diferente != "" && $utilidades != "" && $ventaja != "" && $impacto != "" && $totalpuntos != "" && $descripcion != "" &&  $usuario != "" && $nuevoEstado != "" && $observaciones != ""){
				
				//Se guardan los datos de la evaluación
				$q = "INSERT INTO ingenia_000003 (Medico, Fecha_data, Hora_data, Evaide, Evadif, Evauti, Evaven, Evasig, Evatot, Evares, Evausu, EvaObs, Seguridad) 
							VALUES('ingenia', '".$fecha."','".$hora."','".$idIdea."','".$diferente."','".$utilidades."','".$ventaja."','".$impacto."','".$totalpuntos."','".$descripcion."','".$usuario."','".$observaciones."','C-root')";			
							
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			
				$modEje = "";
				
				//Se valida si durante la evaluación se modifico el eje de innnovación de la idea para guardar el cambio.
				if(isset($params["ejeModificado"]) && isset($params["ejeNuevo"])){
					if($params["ejeModificado"] != $params["ejeNuevo"]){
						$modEje = ", Ideeje = '".$params["ejeNuevo"]."'";
					}
				}
			
				//Se debe actualizar el estado de la idea según el resultado de la evaluación.
				$q = "UPDATE ingenia_000001 SET Ideest = '".$newEstado ."'  ".$modEje." WHERE id = '".$idIdea."'";			
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				//Se guarda el log del cambio de estado
				$q = "INSERT INTO ingenia_000004 (Medico, Fecha_data, Hora_data,Ceside, Cesean, Ceseac, Cesusu, Cesobs, Seguridad)
										 VALUES('ingenia', '".$fecha."', '".$hora."', '".$idIdea."','01','".$newEstado."','".$usuario."','".$observaciones."','C-root')
				";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				$arrEstados = getArrayEstados($conex);
				echo json_encode(array("tipo" => "ok", "respuesta" =>"Los datos de la evaluación se han guardado correctamente. ", "nuevoEstado" => $arrEstados[$newEstado]));
				exit();	
			}else{
				echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! Por vafor ingrese una observación"));
				exit();
			}
		}
		
		if($_POST["accion"] === "modificarEstadoIdea"){
			
			$idIdea       = $_POST["idIdea"]; 				//id de la idea
			$estado       = $_POST["nuevoEstado"]; 			 //nuevo estado
			$observacion  = $_POST["observacion"]; 			 //nuevo estado
			$prevEstado   = $_POST["prevEstado"]; 			 //estado anterior
			$usuario 	  = $_SESSION["usera"];
			
			if($idIdea != "" && $estado != "" && $usuario != "" && $observacion != ""){
							
				//Se debe actualizar el estado de la idea según el resultado de la evaluación.
				$q = "UPDATE ingenia_000001 SET Ideest = '".$estado ."' WHERE id = '".$idIdea."'";			
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				//Se guarda el log del cambio de estado
				$fecha = date("Y-m-d");
				$hora = date("H-i-s");
				$q = "INSERT INTO ingenia_000004 (Medico, Fecha_data, Hora_data,Ceside, Cesean, Ceseac, Cesusu, Cesobs, Seguridad)
										 VALUES('ingenia', '".$fecha."', '".$hora."', '".$idIdea."','".$prevEstado."','".$estado."','".$usuario."','".$observacion."','C-root')
				";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				//Se retorna el resultado
				$arrEstados = getArrayEstados($conex);
				echo json_encode(array("tipo" => "ok", "respuesta" =>"Se ha actualizado el estado correctamente. ", "nuevoEstado" => $arrEstados[$estado]));
				exit();	
			}else{
				echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! Por favor ingrese una observación"));
				exit();
			}
		}
		
		//Funcionalidad para guardar el análisis de factibilidad
		if($_POST["accion"] === "guardarAnalisisFactibilidad"){
			
			$params = array();
			parse_str($_POST["formAnalisis"], $params);	

			$idIdea 	  		 = $params["idIdea"]; 						 //id de la idea
			$facTecnica   		 = $params["facTecnica"]; 
			$facEconomica		 = $params["facEconomica"]; 
			$facJuridica  		 = $params["facJuridica"]; 
			$facAmbiental 		 = $params["facAmbiental"]; 
			$facMercadeo  		 = $params["facMercadeo"]; 
			$estadoFactibilidad  = $params["estadoFactibilidad"]; 			
			$observaciones       = $params["observacionesFactibilidad"]; 			
			$usuario 	  		 = $_SESSION["usera"];
			$fecha	      		 = date("Y-m-d");
			$hora	      		 = date("H:i:s");
			
			if($idIdea != "" && $facTecnica != "" && $facEconomica != "" && $facJuridica != "" && $facAmbiental != "" && $facMercadeo != "" && $usuario != "" ){
							
				//Se guarda el análisis de factibilidad
				$q = "INSERT INTO ingenia_000005 (Medico, Fecha_data, Hora_data,Anaide, Anatec, Anaeco, Anajur, Anaamb, Anamer, Anausu, Anaobs, Seguridad)
										 VALUES('ingenia', '".$fecha."', '".$hora."', '".$idIdea."','".$facTecnica."','".$facEconomica."','".$facJuridica."',
										 '".$facAmbiental."','".$facMercadeo."','".$usuario."','".$observaciones."','C-root')
				";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								
				//Se realiza el cambio de estado de la idea deacuerdo al estado seleccionado luego del análisis de factibilidad				
				$q = "UPDATE ingenia_000001 SET Ideest = '".$estadoFactibilidad ."' WHERE id = '".$idIdea."'";			
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			
				//Se guarda el log de cambio de estado
				$q = "INSERT INTO ingenia_000004 (Medico, Fecha_data, Hora_data,Ceside, Cesean, Ceseac, Cesusu, Cesobs, Seguridad)
										 VALUES('ingenia', '".$fecha."', '".$hora."', '".$idIdea."','02','".$estadoFactibilidad."','".$usuario."','".$observaciones."','C-root')
				";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				//Se retorna el resultado
				$arrEstados = getArrayEstados($conex);
				echo json_encode(array("tipo" => "ok", "respuesta" =>"Se ha guardado toda la información correctamente. ", "nuevoEstado" => $arrEstados[$estadoFactibilidad]));
				exit();	
			}else{
				echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! Por favor verifique que todos los campos tengan información"));
				exit();
			}
		}
			
		if($_POST["accion"] === "verResultadoEvaluacion"){
			$idIdea = $_POST["idIdea"];
			
			if($idIdea != ""){
				$htmlRespuesta = getDetallesIdea($idIdea, $conex);
				
				$q = "SELECT 
						Evadif, Evauti, Evaven, Evasig, Evares, u.Descripcion, Evaobs, Evatot
					  FROM ingenia_000003 
					  INNER join usuarios u ON u.Codigo = Evausu
				      WHERE Evaide = ".$idIdea;
				$result = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(".$q."):</b><br>".mysql_error());				
				$row = mysql_fetch_assoc($result);			
				
				$evaDif = $row["Evadif"] == 3 ? 'Si' : 'No';
				$evaUti = $row["Evauti"] == 3 ? 'Si' : 'No';
				$evaVen = $row["Evaven"] == 3 ? 'Si' : 'No';
				$evaSig = $row["Evasig"] == 3 ? 'Si' : 'No';
				
				//Se organiza el contenido para devolver
				$htmlRespuesta = $htmlRespuesta. "<br>
					<table class='table table-condensed'>
						<tr class='encabezadoTabla'>
							<td colspan='3'>Resultado de la evaluación</td>
						</tr>
						<tr class='fila1'>
							<td width='40%'><b>¿Es una propuesta diferente en la institución?</b> </td>
							<td width='60%'>".$evaDif."</td>
						</tr>			
						<tr class='fila2'>
							<td><b>¿La idea genera ahorros / utilidades?</b> </td>
							<td>".$evaUti."</td>
						</tr>
						<tr class='fila1'>
							<td><b>¿Crea una ventaja en el mercado?</b> </td>
							<td>".$evaVen."</td>
						</tr>
						<tr class='fila2'>
							<td><b>¿La idea genera un impacto significativo en la empresa?</b> </td>
							<td>".$evaSig."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Resultado:</b> </td>
							<td>".$row["Evares"]." <b>Total puntaje:</b> ".$row["Evatot"]."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Observaciones:</b> </td>
							<td>".$row["Evaobs"]."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Usuario que evaluó:</b> </td>
							<td>".$row["Descripcion"]."</td>
						</tr>
						
					</table>
				";			
								
				echo json_encode(array("tipo" => "ok", "respuesta" =>$htmlRespuesta));
				exit();				
			}else{
				echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! No se encontraron resultados."));
				exit();
			}
		}
		
		
		if($_POST["accion"] === "verLogEstados"){
			$idIdea = $_POST["idIdea"];
			
			if($idIdea != ""){
				$htmlRespuesta = getDetallesIdea($idIdea, $conex);
				
				$arrEstados = getArrayEstados($conex);
								
				$q = "SELECT 
						Fecha_data, Hora_data, Cesean, Ceseac, Cesobs, u.Descripcion
					  FROM ingenia_000004 
					  INNER join usuarios u ON u.Codigo = Cesusu
				      WHERE Ceside = ".$idIdea;
				$result = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(".$q."):</b><br>".mysql_error());				
					
				$htmlRespuesta = $htmlRespuesta. "<br>
					<table class='table table-condensed'>
						<tr class='encabezadoTabla' align='center' style='height: 40px;'>
							<td><b>Fecha y hora</b> </td>							
						
							<td><b>Estado anterior</b> </td>							
						
							<td><b>Nuevo estado</b> </td>							
						
							<td><b>Observaciones</b> </td>							
						
							<td><b>Usuario que cambia estado</b> </td>							
						</tr>
				";				
				$fila = "fila1";
				while($row = mysql_fetch_assoc($result)) {	
					$htmlRespuesta .= "<tr class='".$fila."'>";
					$htmlRespuesta .= "<td>".$row["Fecha_data"]." " .$row["Hora_data"]. "</td>";
					$htmlRespuesta .= "<td>".$arrEstados[$row["Cesean"]]. "</td>";
					$htmlRespuesta .= "<td>".$arrEstados[$row["Ceseac"]]. "</td>";
					$htmlRespuesta .= "<td>".$row["Cesobs"]. "</td>";
					$htmlRespuesta .= "<td>".utf8_encode($row["Descripcion"]). "</td>";
					$htmlRespuesta .= "</tr>";
							
					$fila = $fila === "fila1" ? "fila2" : "fila1";
				}	
				
				$htmlRespuesta .= "</table>";	
				
				echo json_encode(array("tipo" => "ok", "respuesta" =>$htmlRespuesta));
				exit();				
			}else{
				echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! No se encontraron resultados."));
				exit();
			}
		}
		
		
		if($_POST["accion"] === "resultadoAnalisisFactibilidad"){
			$idIdea = $_POST["idIdea"];
			
			if($idIdea != ""){
				
				$htmlRespuesta = getDetallesIdea($idIdea, $conex);
				
				$arrEstados = getArrayEstados($conex);
								
				$q = "SELECT 
						Fecha_data, Hora_data, Anatec, Anaeco, Anajur, Anaamb, Anamer, u.Descripcion, Anaobs
					  FROM ingenia_000005 
					  INNER join usuarios u ON u.Codigo = Anausu
				      WHERE Anaide = ".$idIdea;
				$result = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(".$q."):</b><br>".mysql_error());				
				$row = mysql_fetch_assoc($result);
				
				$htmlRespuesta .= "<br>
					<table class='table table-condensed'>
						<tr class='encabezadoTabla'>
							<td colspan='3'>Resultados</td>
						</tr>
						<tr class='fila1'>
							<td width='30%'><b>Fecha y hora:</b> </td>
							<td width='70%'>".$row["Fecha_data"]." " .$row["Hora_data"]."</td>
						</tr>			
						<tr class='fila2'>
							<td><b>Factibilidad técnica:</b> </td>
							<td>".$row["Anatec"]."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Factibilidad económica:</b> </td>
							<td>".$row["Anaeco"]."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Factibilidad juridica:</b> </td>
							<td>".$row["Anajur"]."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Factibilidad ambiental:</b> </td>
							<td>".$row["Anaamb"]."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Factibilidad de mercadeo:</b> </td>
							<td>".$row["Anamer"]."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Observaciones:</b> </td>
							<td>".$row["Anaobs"]."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Usuario que registró el análisis</b> </td>
							<td>".$row["Descripcion"]."</td>
						</tr>	
					</table>
				";
							
				echo json_encode(array("tipo" => "ok", "respuesta" =>$htmlRespuesta));
				exit();				
			}else{
				echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! No se encontraron resultados."));
				exit();
			}
		}
		
			
		//Cuando se envíe un ajax sin opción a realizar
		echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! No se encontró una acció a realizar."));
		exit();		
	}
	else{
?>

<html lang="es-ES">
<head>
<title>REGISTRO DE IDEAS DE INNOVACION</title>
<meta charset="utf-8">

	
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
<!--<link href="../../../include/root/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />-->
<script src="../../../include/root/jquery.min.js"></script>
<!--<script src="../../../include/root/fileinput.min.js" type="text/javascript"></script>-->
<script src="../../../include/root/bootstrap.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.easing.min.js"></script>

<script src="../../../include/root/jquery.form.js" type="text/javascript"></script>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>




<!-- Inicio estilos css -->
<style type="text/css">

	.img3d{
		height: 200px;
		width: 310px;
	}

	.principal{
		width: 70%;
		align: center 
		padding-right:20px;padding-left:20px;margin-right:auto;margin-left:auto	
	}

	.row{
		margin-right:-15px;
		margin-left:-15px; 
		width:100%
	}

	.row:after,.row:before{
		display:table;content:" "
	}
	.row:after{
		clear:both
	}

	.partea {
		float: left; 
		width: 27%;
		/*text-align: center*/
	}
	.parteb {
		float: right; 
		width: 73%;
		background-color: withe; 
		text-align: center
	}
	.col-12 {
		position:relative;
		min-height:1px;padding-right:20px;
		padding-left:20px;
		background-color: #F2F2F2; 
		height:15%;text-align:center; 
		padding-top:2%
	}

	.imgprincipal {height:100%}

	.vticker{
		/*border: 1px solid #ddd;*/
		width: 100%;
	}
	.vticker ul{
		padding: 0;
	}
	.vticker li{
		list-style: none;
		border-bottom: 1px solid #ddd;
		padding: 10px;
		border-radius: 10px
	}
	.et-run{
		background: gray;
	}

	. {
		color: #fff;
		background-color: #004b8e;
		border-color: #2e6da4;
	}

	.btn-primary:hover {
		color: #fff;
		background-color: #286090;
		border-color: #204d74;
	}

	a {
		color: #337ab7;
		text-decoration: none;
	}
	div.list-group-item.active, a.list-group-item.active:hover, a.list-group-item.active:focus {
		background-color: #428bca;
		border-color: #428bca;
		color: #fff;
		z-index: 2;
		height: 4% ;
		border-radius: 2px;
		text-align: center;
		padding-top: 4px
	}
	.modal-header, h4, .close {
		background-color: #004b8e; /*#5cb85c;*/
		color:white !important;
		text-align: center;
		font-size: 24px;
		  
	}
	.modal-header {
		/*height: 60px*/
	}
	.modal-footer {
		background-color: #f9f9f9;
	}
	.obligatorio {
		color: red
	}
	.modal-body {
		max-height: calc(100vh - 210px);
		overflow-y: auto;
		background-color: InactiveBorder
	}

	.fila1 {
		background-color: #c3d9ff;
		height: 30px
	}
	.fila2{
		background-color: #e8eef7;
		height: 30px
	}

	body{
		width: 100%;
		height: 100%
	}

	.bnegrita{
		font-size: 18px
	}
	
	table {
		border-collapse: separate;
		border-spacing: 2px;
	}
	.inputSearch{
		width:55%; 
		border: 1px solid #ccc; 
		border-radius: 4px;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
		height: 34px;
		padding: 6px 12px;
	}
	
	.page-header {
		margin: -10px 0 20px;
	}
	
	.iconosOpcion{
      display: block;
      margin-left: auto;
      margin-right: auto;
      border:none;
	  max-width: 30px;
     }
	
</style>
<!-- fin estilos css -->

<script type="text/javascript"> 

function modificarEstado(id){
	
	$.post("administracion_ideas.php",
		{
			consultaAjax:   'on',
			accion:         'verDetalleEstado',
			idIdea:        	id
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);	
				$("#idIdeaModificar").val(id);			
				$("#tituloModificarIdea").html("Modificar estado a la idea # "+ id);				
				$("#bodyDetalleIdeaMod").html($.parseHTML(objRespuesta.respuesta));	
				$("#modificarEstadoIdea").modal();
			
		});
}

function evaluarIdea(id){
	//habilitar formulario, resetear los campos 
	resetFormEvaluacion();
	
	$("#evaluacionIdeas").modal();
	$.post("administracion_ideas.php",
		{
			consultaAjax:   'on',
			accion:         'evaluarIdea',
			idIdea:        	id,
			modificarEje:   true
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
				$("#idIdeaEvaluada").val(id);
				$("#tituloIdea").html("Evaluaci&oacute;n de idea # "+ id);				
				$("#bodyDetalleIdea").html($.parseHTML(objRespuesta.respuesta));	
				$("#evaluacionIdeas").modal();			
		});
}


function analisisFactibilidad(id){
	
	$.post("administracion_ideas.php",
		{
			consultaAjax:   'on',
			accion:         'evaluarIdea',
			idIdea:        	id
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
				
				$("#tituloAnalisis").html("An&aacute;lisis de factibilidad idea # "+ id);	
				
				$("#idIdeaAnalizar").val(id);
							
				$("#bodyDetalleIdeaAnalisis").html($.parseHTML(objRespuesta.respuesta));
				$("#analisisFactibilidad").modal();							
		});
	
}

function resultadoAnalisisFactibilidad(id){
	
	$.post("administracion_ideas.php",
		{
			consultaAjax:   'on',
			accion:         'resultadoAnalisisFactibilidad',
			idIdea:        	id
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
				
				$("#tituloResultadoAnalisis").html("Resultado del an&aacute;lisis de factibilidad idea # "+ id);
				$("#bodyResultadoAnalisis").html($.parseHTML(objRespuesta.respuesta));
				$("#resultadoAnalisisFactibilidad").modal();			
	});
	
}



function verEvaluacion(id){
	$.post("administracion_ideas.php",
		{
			consultaAjax:   'on',
			accion:         'verResultadoEvaluacion',
			idIdea:        	id
			}, function(respuesta){
				$("#titResultadoEvaluacion").html("Resultado de la evaluaci&oacute;n idea # "+ id);	
				var objRespuesta = $.parseJSON(respuesta);			
							
				$("#bodyResultadoEvaluacion").html($.parseHTML(objRespuesta.respuesta));				
	});
	
	$("#resultadoEvaluacion").modal();
}

function verLogEstados(id){
	$.post("administracion_ideas.php",
		{
			consultaAjax:   'on',
			accion:         'verLogEstados',
			idIdea:        	id
			}, function(respuesta){
				$("#titLogEstados").html("Registros de cambios de estado para la idea # "+ id);	
				var objRespuesta = $.parseJSON(respuesta);			
							
				$("#bodyLogEstados").html($.parseHTML(objRespuesta.respuesta));				
	});
	
	$("#logEstados").modal();
}


function resetFormEvaluacion(){
	$('#formEvaluacion').find('input, textarea, button, select').attr('disabled',false); 
		
	$("#msjOkEval").css("display", "none");
	$("#msjTextoOkEval").html("");		
	$("#msjErrorEval").css("display", "none");					
	$("#msjTextoErrEval").html("");	
	
	$("#guardarEvaluacion").attr("disabled", false);
	
	$('#eval_diferente').prop('checked', false).change();
	$('#eval_utilidad').prop('checked', false).change();
	$('#eval_ventaja').prop('checked', false).change();
	$('#eval_impacto').prop('checked', false).change();
	
	$("#formEvaluacion").trigger("reset");
}

$(document).ready(function(){
	$("#guardarEvaluacion").click(function(){
		var form_evaluacion = $("#formEvaluacion").serialize();
		$.post("administracion_ideas.php",
		{
			consultaAjax:   		'on',
			accion:         		'guardarEvaluacion',
			form_evaluacion:        form_evaluacion
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
					
				if(objRespuesta.tipo === "error"){
					$("#msjOkEval").css("display", "none");
					$("#msjErrorEval").html(objRespuesta.respuesta);
					$("#msjErrorEval").css("display", "block");			
				}else{	
					//Se limpia el formulario 
					$("#estadoActual").text(objRespuesta.nuevoEstado);
					$('#formEvaluacion').find('input, textarea, button, select').attr('disabled','disabled');
					
					$("#guardarEvaluacion").attr("disabled", true);
					$("#msjErrorEval").css("display", "none");					
					$("#msjTextoOkEval").html(objRespuesta.respuesta);
					$("#msjOkEval").css("display", "block");
				}
			});
    });

	function calcularTotalEval(){			
		var valTot = parseInt($("#puntaje_diferente").val()) + parseInt($("#puntaje_utilidad").val()) + parseInt($("#puntaje_ventaja").val()) + parseInt($("#puntaje_impacto").val());	
		
		//Se valida de acuerdo al puntaje la calificacion
		var decripcionPuntaje = "";
		switch(valTot){
			case 0 : decripcionPuntaje = "Idea no innovadora";
					   break;
			case 3 : decripcionPuntaje = "Idea no innovadora";
					   break;		   
			case 6 : decripcionPuntaje = "Idea no innovadora";
					   break;		   
			case 9 : decripcionPuntaje = "Innovaci&oacute;n para la instituci&oacute;n";
					   break;	
			case 12 : decripcionPuntaje = "Innovaci&oacute;n para la instituci&oacute;n y para el mercado";
					   break;					   
		}
		
		$("#total_puntaje").val(valTot);	
		$("#puntaje_descriptivo").val(decripcionPuntaje);	
		$("#textoCalificacion").html(decripcionPuntaje);	
	}
	
	$(function() {
		$('#eval_diferente').change(function() {
			var valTot = $("#total_puntaje").val();		
			if($(this).prop('checked') == true) {
				$("#puntaje_diferente").val(3);
			}else{
				$("#puntaje_diferente").val(0);
			}
			calcularTotalEval();				
		})
	})
	 
	$(function() {
		$('#eval_utilidad').change(function() {						
			if($(this).prop('checked') == true) {
				$("#puntaje_utilidad").val(3);
			}else{
				$("#puntaje_utilidad").val(0);
			}
			calcularTotalEval();
		})
	})
	
	$(function() {
		$('#eval_ventaja').change(function() {			
			if($(this).prop('checked') == true) {
				$("#puntaje_ventaja").val(3);
			}else{
				$("#puntaje_ventaja").val(0);
			}
			calcularTotalEval();
		})
	})
	
	$(function() {
		$('#eval_impacto').change(function() {			
			if($(this).prop('checked') == true) {
				$("#puntaje_impacto").val(3);
			}else{
				$("#puntaje_impacto").val(0);
			}
			calcularTotalEval();
		})
	})
	
	$('#btnCambioEstado').click(function() {			
		$.post("administracion_ideas.php",
		{
			consultaAjax:   		'on',
			accion:         		'modificarEstadoIdea',
			idIdea:        			$("#idIdeaModificar").val(),
			nuevoEstado:			$("#nuevoEstado").val(),
			observacion:			$("#obsCambioEstado").val(),
			prevEstado:			    $("#prevEstado").val(),
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
					
				if(objRespuesta.tipo === "error"){
					$("#msjOkMod").css("display", "none");
					$("#msjErrorMod").html(objRespuesta.respuesta);
					$("#msjErrorMod").css("display", "block");			
				}else{	
					$("#estadoActual").text(objRespuesta.nuevoEstado);
					$("#msjErrorMod").css("display", "none");					
					$("#msjTextoOkMod").html(objRespuesta.respuesta);
					$("#msjOkMod").css("display", "block");
					
					$("#obsCambioEstado").attr("disabled", true);
					$("#nuevoEstado").attr("disabled", true);	
					$("#btnCambioEstado").attr("disabled", true);	
				}
			});
	})	
	
	$('#btnGuardarAnalisis').click(function() {			
		$.post("administracion_ideas.php",
		{
			consultaAjax:   		'on',
			accion:         		'guardarAnalisisFactibilidad',
			formAnalisis:        	$("#formAnalisis").serialize(),
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
				
				$('#analisisFactibilidad .modal-body').animate({ scrollTop: 0 }, 'slow');
				
				if(objRespuesta.tipo === "error"){
					$("#msjOkAna").css("display", "none");
					$("#msjErrorAna").html(objRespuesta.respuesta);
					$("#msjErrorAna").css("display", "block");			
				}else{	
					$("#estadoActual").text(objRespuesta.nuevoEstado);
					$("#msjErrorAna").css("display", "none");					
					$("#msjTextoOkAna").html(objRespuesta.respuesta);
					$("#msjOkAna").css("display", "block");
					
					$('#formAnalisis').find('input, textarea, button, select').attr('disabled','disabled');
					$("#btnGuardarAnalisis").attr("disabled", true);	
				}
			});
	})
	
	$('input#buscador').quicksearch('#tablaListadoIdeas .find');
		
	$('#volver').click(function() {	
		var idEmpresa = $("#IdEmpresa").val();
		location.href = "registro_nueva_idea.php?wemp_pmla="+idEmpresa;
	})
	
	$('#cerrarVentana').click(function() {			
		window.close();
	})
	
});

</script>
</head>
<body>
<div class="principal">
	<div class="row"> 
		<img src="../../images/medical/ingenia/logo_ingenia.jpg<?=('?a='.rand(1,1000))?>">		
	</div>
	
	<?php
		echo "<input type='hidden' id='IdEmpresa' value='".$wemp_pmla."'>";
	?>
	
	<br>
	<div class="row" style="text-align: right;">
		<div>
			<!--<button type="button" class="btn btn-primary" id="barraAyudas" onClick='modalAdministradorBarra();'> Barra ayudas</button> -->
			<button type="button" class="btn btn-primary" id="volver"> Volver</button> 
			<button type="button" class="btn btn-primary" id="cerrarVentana"> Cerrar</button>		
		</div>
	</div>
	<div class="row">			
		<h2 class="page-header">Administrador de ideas</h2>
				
		<form id="buscados">
			<div class="partea">
			<div class="input-append">
				<input type="text" id="buscador" placeholder="Palabra a buscar" class="inputSearch">
			</div>
			</div>
								
			<div class="parteb">				
				<table>
				<tr>
					<?php
					//Se listan todas las ideas en orden de fecha de creación				
					$q = "SELECT Estdes, Estcol
						  FROM ingenia_000007
						  ORDER BY Estcod ASC
					";
					
					$res= mysql_query($q, $conex) or die( mysql_error()." <br> ".print_r( $q )  );
						
					while($row = mysql_fetch_assoc($res)) {		
						echo "<td style='background-color:".$row["Estcol"]."; height:25px; border-radius: 6px; '>&nbsp;&nbsp;".$row["Estdes"]."&nbsp;&nbsp;</td>
						";
					}
					?>
					</tr>
				</table>
			</div>
		</form>
		
		<br><br>
		
		<table width="100%" id="tablaListadoIdeas">
			<tbody>
			<tr class="encabezadoTabla" align="center" style="height: 40px;">
				<td width="10%">
					Idea No.
				</td>
				<td width="20%">
					 T&iacute;tulo
				</td>
				<!--<td width="20%">
					Descripción
				</td>
				<td width="10%">
					Problema a resolver
				</td>
				<td width="10%">
					Resultado esperado
				</td>
				<td width="10%">
					Recursos necesarios
				</td>-->
				<td width="10%">
					Eje de innovaci&oacute;n
				</td>
				<td width="30%">
					Publicado por
				</td>
				<td width="10%">
					Fecha
				</td>
				<td width="20%">
					Estado
				</td>
				<td width="10%">	
					Administrar
				</td>
			</tr>	
			</tbody>			
			
			<?php
				//Se listan todas las ideas en orden de fecha de creación				
				$q = "SELECT i.id, i.Fecha_data, i.Idetit, i.Idedes, i.Idepro, i.Ideres, i.Iderin, i.Iderpe, 
							 i.Idereq, i.Ideeje, i.Ideest, i.Ideusu, es.Estdes, es.Estcol, u.Descripcion, r.Empdes
					  FROM ingenia_000001 i
					  INNER JOIN ingenia_000007 es ON es.Estcod = i.Ideest
					  INNER join usuarios u ON u.Codigo = i.Ideusu
					  INNER JOIN root_000050 r ON r.Empcod = u.Empresa
					  WHERE Empest = 'on'
					  ORDER BY i.id DESC
				";
						
				$fila = "fila1";
				$res= mysql_query($q, $conex) or die( mysql_error()." <br> ".print_r( $q )  );
					
				while($row = mysql_fetch_assoc($res)) {		
				
					$recursos = "";
					$recursos .= $row["Iderin"] != "" ? "<b>Infraestructura:</b> ". $row["Iderin"]."<br>" : "";
					$recursos .= $row["Iderpe"] != "" ? "<b>Personal:</b> ". $row["Iderpe"]."<br>" : "";
					$recursos .= $row["Idereq"] != "" ? "<b>Equipos:</b> ". $row["Idereq"]."<br>" : "";
								
					echo "<tbody class='find'>";
					echo "<tr class='".$fila."'>";
						echo "	
							<td style='text-align:center'>
								".$row["id"]."
							</td>						
							<td>
								".utf8_decode($row["Idetit"])."
							</td>
							";
							/*<td>
								".$row["Idedes"]."
							</td>
							<td>
								".$row["Idepro"]."
							</td>							
							<td>
								".$row["Ideres"]."
							</td>
							<td>
								".$recursos."
							</td>*/
							echo "<td>
								".$row["Ideeje"]. "
							</td>
							<td>
								".($row["Descripcion"]). "<br>
								<span style='font-size:11px'><b>".$row["Empdes"]. "</b></span>
							</td>
							<td>
								".$row["Fecha_data"]. "
							</td>
							<td style='text-align:center; background-color:".$row["Estcol"].";'>
								".($row["Estdes"]). "
							</td>
						";
						echo "<td>";
						
						if($row["Ideest"] === "01"){
							echo "&nbsp;&nbsp;<img src='../../images/medical/ingenia/glyphicons-151-edit.png' width='22px' height='20px' title='Evaluar idea' 
								style='cursor:pointer' onClick='evaluarIdea(".$row["id"]. ")'>";
						}	
						if($row["Ideest"] != "01"){						
							echo "&nbsp;&nbsp;<img src='../../images/medical/ingenia/glyphicons-530-list-alt.png' width='19px' height='19px' title='Ver resultado evaluaci&oacute;n' 
								style='cursor:pointer' onClick='verEvaluacion(".$row["id"]. ")'>";
						}
						if($row["Ideest"] == "02" ){						
							echo "&nbsp;&nbsp;<img src='../../images/medical/ingenia/glyphicons-153-check.png' width='19px' height='17px' title='Realizar an&aacute;lisis de factibilidad' 
								style='cursor:pointer' onClick='analisisFactibilidad(".$row["id"]. ")'>";
						}
						if($row["Ideest"] != "01" && $row["Ideest"] != "02" && $row["Ideest"] != "03"){						
							echo "&nbsp;&nbsp;<img src='../../images/medical/ingenia/glyphicons-153-check.png' width='19px' height='17px' title='Ver resultado an&aacute;lisis de factibilidad' 
								style='cursor:pointer' onClick='resultadoAnalisisFactibilidad(".$row["id"]. ")'>";
						}
						
						if($row["Ideest"] != "01" && $row["Ideest"] != "03" && $row["Ideest"] != "02" && $row["Ideest"] != "07"){						
							echo "&nbsp;&nbsp;<img src='../../images/medical/ingenia/glyphicons-366-restart.png' width='20px' height='20px' title='Modificar estado' 
								style='cursor:pointer' onClick='modificarEstado(".$row["id"]. ")'>";
						}						
						if($row["Ideest"] != "01"){						
							echo "&nbsp;&nbsp;<img src='../../images/medical/ingenia/glyphicons-545-eye-plus.png' width='21px' height='19px' title='Ver registro de cambios de estado' 
								style='cursor:pointer' onClick='verLogEstados(".$row["id"]. ")'>&nbsp;&nbsp;";
						}	
						echo "</td>";
						echo "</tr>";
						echo "</tbody>";
						
						$fila = $fila === "fila1" ? "fila2" : "fila1";
					}				
			?>			
		</table>	
		<br><br>
		
				
		
	</div>
	
<div class="container">
	<!-- Inicio de la modal del formulario evaluación de nueva idea-->
  <div class="modal fade" id="evaluacionIdeas" role="dialog">
    <div class="modal-dialog modal-lg ">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 id="tituloIdea"></h4>
        </div>
        <div class="modal-body" style="padding:10px 40px;">
		
			<form id="formEvaluacion">
				<div id="bodyDetalleIdea">
				</div>
				<input type="hidden" id="idIdeaEvaluada" name="idIdea">
								
				<div id="msjErrorEval" class="alert alert-danger alert-dismissable" style="display:none">
				  <b id="msjTextoErrEval"> </b>
				</div>
				<div id="msjOkEval" class="alert alert-success alert-dismissable" style="display:none">
				  <b id="msjTextoOkEval"> </b>
				</div>
			
				<h3 class="page-header">Aspectos a evaluar</h3>
				<table>
					<tr>
						<td width='60%'>
							<label for='descripcion'> &iquest;Es una propuesta diferente en la instituci&oacute;n?</label>
						</td>
						<td width='10%'>
							<input data-toggle='toggle' data-on='Si' data-off='No' data-onstyle='success' data-offstyle='danger' type='checkbox' id="eval_diferente">
						</td>
						<td width='20%'>
							<input type='text' name="puntaje_diferente" id="puntaje_diferente"  value='0' readonly style="text-align:right; width:25%" class="form-control">
						</td>
					</tr>
					<tr>
						<td>
							<label for='descripcion'> &iquest;La idea genera ahorros / utilidades?</label>
						</td>
						<td>
							<input data-toggle='toggle' data-on='Si' data-off='No' data-onstyle='success' data-offstyle='danger' type='checkbox' id="eval_utilidad">
						</td>
						<td>
							<input type='text' name="puntaje_utilidad" id="puntaje_utilidad" value='0' readonly style="text-align:right; width:25%" class="form-control">
						</td>
					</tr>
					<tr>
						<td>
							<label for='descripcion'> &iquest;Crea una ventaja en el mercado?</label>
						</td>
						<td>
							<input data-toggle='toggle' data-on='Si' data-off='No' data-onstyle='success' data-offstyle='danger' type='checkbox' id="eval_ventaja">
						</td>
						<td>
							<input type='text' name="puntaje_ventaja" id="puntaje_ventaja" value='0' readonly style="text-align:right; width:25%" class="form-control">
						</td>
					</tr>
					<tr>
						<td>
							<label for='descripcion'>&iquest;La idea genera un impacto significativo en la empresa?</label>
						</td>
						<td>
							<input data-toggle='toggle' data-on='Si' data-off='No' data-onstyle='success' data-offstyle='danger' type='checkbox'  id="eval_impacto">
						</td>
						<td>
							<input type='text' name="puntaje_impacto" id="puntaje_impacto" value='0' readonly style="text-align:right; width:25%" class="form-control">
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<label>Total:</label>					
						<td>
							<input type='text' name="total_puntaje" id="total_puntaje" value='0' readonly style="text-align:right; width:25%" class="form-control">
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<label>Calificaci&oacute;n descritiva:</label> <span id="textoCalificacion" >Idea no innovadora</span>
							<input type='hidden' name="puntaje_descriptivo" id="puntaje_descriptivo" value='Idea no innnovadora' readonly >
						</td>
					</tr>
					<tr>
						<td>
							<label for='descripcion'><span class='obligatorio'>*</span> Observaciones</label>
						</td>
					</tr>
					<tr>
						<td colspan='3'>
							<textarea cols='95' name="observaciones" id="observaciones" class="form-control"></textarea>
						</td>
					</tr>
				</table>
			</form>
			<br><b>Los campos marcados con <span class="obligatorio">*</span>  son obligatorios.</b><br><br>
        </div>
        <div class="modal-footer">
			<button type="submit" class="btn btn-success" id="guardarEvaluacion"> Guardar resultado</button>
          	<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal" onClick="javascript:location.reload()">Cerrar</button>
        </div>
      </div>      
    </div>
  </div>   
  <!-- fin de la modal formulario de evaluación de ideas -->

  <!--Modal de cambio de estado-->
	<div class="modal fade" id="modificarEstadoIdea" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="tituloModificarIdea"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyModificarIdea">
					<div id="bodyDetalleIdeaMod">
					</div>
					<input type="hidden" id="idIdeaModificar" name="idIdea">
					
					<div id="msjErrorMod" class="alert alert-danger alert-dismissable" style="display:none">
						<b id="msjTextoErrMod"> </b>
					</div>
					<div id="msjOkMod" class="alert alert-success alert-dismissable" style="display:none">
						  <b id="msjTextoOkMod"> </b>
					</div>
										
					<div>
						<label><span class='obligatorio'>*</span> Nuevo estado </label>
						<select class="form-control" id="nuevoEstado">
							<option value="">Seleccione</option>
							<option value="01">Aprobada</option>
							<option value="03">Redireccionada</option>
							<option value="06">En implementaci&oacute;n</option>
							<option value="07">En operaci&oacute;n</option>
						</select>
					</div>
					<div>				
						<label for='obsCambioEstado'><span class='obligatorio'>*</span> Observaciones</label>			
						<textarea cols='95' name="obsCambioEstado" id="obsCambioEstado" class="form-control"></textarea>				
					</diV>
					<br><b>Los campos marcados con <span class="obligatorio">*</span>  son obligatorios.</b><br><br>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" id="btnCambioEstado" >Guardar modificaci&oacute;n</button>
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal" onClick="javascript:location.reload()">Cerrar</button>
				</div>
			</div>      
		</div>
   </div> 
   <!--fin modal cambio de estado -->
  
  <!--Inicio modal analisis de factibilidad-->
	<div class="modal fade" id="analisisFactibilidad" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="tituloAnalisis"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyAnalisis">
					<div id="bodyDetalleIdeaAnalisis">
					</div>
										
					<div id="msjErrorAna" class="alert alert-danger alert-dismissable" style="display:none">
						  <b id="msjTextoErrAna"> </b>
						</div>
					<div id="msjOkAna" class="alert alert-success alert-dismissable" style="display:none">
						  <b id="msjTextoOkAna"> </b>
					</div>
					
					<form id="formAnalisis">
						<input type="hidden" id="idIdeaAnalizar" name="idIdea">
						<div>				
							<label for='facTecnica'><span class='obligatorio'>*</span> Factibilidad t&eacute;cnica:</label>	
							(Tecnolog&iacute;a requerida, procesos requeridos, infraestructura f&iacute;sica y personal)
							<textarea cols='95' name="facTecnica" id="facTecnica" class="form-control"></textarea>				
						</div>	
						<br>						
						<div>				
							<label for='facEconomica'><span class='obligatorio'>*</span> Factibilidad econ&oacute;mica</label>	
							(Inversi&oacute;n inicial, costos de operaci&oacute;n, inversi&oacute;n completa y resultado econ&oacute;mico esperado)
							<textarea cols='95' name="facEconomica" id="facEconomica" class="form-control"></textarea>				
						</div>
						<br>
						<div>				
							<label for='facJuridica'><span class='obligatorio'>*</span> Factibilidad jur&iacute;dica</label>
							(Evaluaci&oacute;n de las normas aplicables, licencias requeridas)
							<textarea cols='95' name="facJuridica" id="facJuridica" class="form-control"></textarea>				
						</div>
						<br>
						<div>				
							<label for='facAmbiental'><span class='obligatorio'>*</span> Factibilidad ambiental</label>	
							(Impacto o huella del proyecto)
							<textarea cols='95' name="facAmbiental" id="facAmbiental" class="form-control"></textarea>				
						</div>
						<br>
						<div>				
							<label for='facMercadeo'><span class='obligatorio'>*</span> Factibilidad de mercadeo </label>	
							(Necesidad identificada, p&uacute;blico objetivo e impacto esperado)		
							<textarea cols='95' name="facMercadeo" id="facMercadeo" class="form-control"></textarea>				
						</div>
						<br>
						<div>
							<label><span class='obligatorio'>*</span> Nuevo estado </label>
							<select class="form-control" name="estadoFactibilidad" id="estadoFactibilidad">
								<option value="">Seleccione</option>
								<option value="04">Viable</option>
								<option value="03">Redireccionada</option>
							</select>
						</div>
						<br>
						<div>
							<label><span class='obligatorio'>*</span> Observaciones</label>
							<textarea class="form-control" name="observacionesFactibilidad" id="observacionesFactibilidad"></textarea>
						</div>
												
					</form>
					<br>Los campos marcados con <span class="obligatorio">*</span>  son obligatorios.
					<br><br>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" id="btnGuardarAnalisis" >Guardar</button>
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal" onClick="javascript:location.reload()">Cerrar</button>
				</div>
			</div>      
		</div>
   </div> 
   <!--fin modal analisis de factibilidad -->
      
	
	<!--Inicio modal del resultado del analisis de factibilidad-->
	<div class="modal fade" id="resultadoAnalisisFactibilidad" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="tituloResultadoAnalisis"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyResultadoAnalisis">
							
				</div>    
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal">Cerrar</button>
				</div>				
			</div>
		</div>
   </div> 
   <!--fin modal del resultado del analisis de factibilidad -->  
	  
	  
   <!--modal para ver el resultado de la evaluación-->
	<div class="modal fade" id="resultadoEvaluacion" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="titResultadoEvaluacion"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyResultadoEvaluacion">
				
				</div>
				<div class="modal-footer">					
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal">Cerrar</button>
				</div>				
			</div>
		</div>
	</div> 

	<!-- modal para ver el log de cambios de estado-->
	<div class="modal fade" id="logEstados" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="titLogEstados"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyLogEstados">
				
				</div>
				<div class="modal-footer">					
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal">Cerrar</button>
				</div>
			</div>      
		</div>
	</div>  
   <!-- fin modal log de cambios de estado -->
   	
	
	 <!--Modal de cambio de estado-->
	<div class="modal fade" id="AdministrarBarra" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="tituloAdministrarBarra"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyAdministrarBarra">
					<div id="bodyDetalleIdeaMod">
					</div>
					
					
										
					<div id="msjErrorMod" class="alert alert-danger alert-dismissable" style="display:none">
						<b id="msjTextoErrMod"> </b>
					</div>
					<div id="msjOkMod" class="alert alert-success alert-dismissable" style="display:none">
						  <b id="msjTextoOkMod"> </b>
					</div>
										
					<div>
						<label><span class='obligatorio'>*</span> Nuevo estado </label>
						<select class="form-control" id="nuevoEstado">
							<option value="">Seleccione</option>
							<option value="05">Aprobada</option>
							<option value="03">Redireccionada</option>
							<option value="06">En implementaci&oacute;n</option>
							<option value="07">En operaci&oacute;n</option>
						</select>
					</div>
					<div>				
						<label for='obsCambioEstado'><span class='obligatorio'>*</span> Observaciones</label>			
						<textarea cols='95' name="obsCambioEstado" id="obsCambioEstado" class="form-control"></textarea>				
					</div>
					<br><b>Los campos marcados con <span class="obligatorio">*</span>  son obligatorios.</b><br><br>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" id="btnCambioEstado" >Guardar</button>
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal" onClick="javascript:location.reload()">Cerrar</button>
				</div>
			</div>      
		</div>
   </div> 
   <!--fin modal cambio de estado -->
  
   
   
</div>
</body>
</html>

<?php
	}
?>


