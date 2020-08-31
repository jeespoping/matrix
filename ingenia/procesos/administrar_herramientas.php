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
		

	function validarNombreScript($script, $conex, $wbasedato, $grupo)
	{				
		$q_existe = " SELECT count(*) as numero
					    FROM ".$wbasedato."_000087
					   WHERE Matcod = '".$script."'
					";
		$res_existe = mysql_query($q_existe,$conex) or die("Error: " . mysql_errno() . " - en el query (Validar existencia script): ".$q_existe." - ".mysql_error());
		$row_existe = mysql_fetch_array($res_existe);
		
		if($row_existe['numero'] > 0){
			return false;
		} else {
			return true;
		}
	}
	
	function registrarScript($conex, $fgrupo, $nombre_script, $ftipo, $fdescripcion, $wbasedato){
		
		$result = false;
		
		$fdesarrollador = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";	
		$Cod_est_prod = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptProduccion');
		
		$usuarios_aprueban = consultarAliasPorAplicacion($conex, '01', 'UsuarioMatriculaScripts');
		$arr_usu_apru = explode(',' ,$usuarios_aprueban);
		$usuApr = isset($arr_usu_apru[0]) ? $arr_usu_apru[0] : "";
		
		if($usuApr){	
			$q_guardar="INSERT INTO ".$wbasedato."_000087
									( 	Medico, 		Fecha_data,  		Hora_data, 			Matcod, 			Matgru, 	   Matcar, 		Matdes, 	 		Matest, 		Matcre,			   				Seguridad, id )
							VALUES  ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$nombre_script."','".$fgrupo."','".$ftipo."','".$fdescripcion."', '".$Cod_est_prod."', '".$fdesarrollador."', 'C-".$usuApr."','' )
										";
			$resultMatricula = mysql_query($q_guardar,$conex) or die ("Error: ".mysql_errno()." - en el query (Guardar script): ".$q_guardar." - ".mysql_error());
							
			if ($resultMatricula){			
					//Se guarda el registro del log en la 89 para dejarlo en producción
					$q_guardarLog = "INSERT INTO ".$wbasedato."_000089
										( 	Medico, 		Fecha_data,  		Hora_data, 			Dypscr, 			Dypusu, 	   Dypest, 		Dypjus, Seguridad)
								VALUES  ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$nombre_script."','".$usuApr."','".$Cod_est_prod."','', 'C-".$usuApr."')
											";
					$resultLog = mysql_query($q_guardarLog,$conex) or die ("Error: ".mysql_errno()." - en el query (Guardar script): ".$q_guardarLog." - ".mysql_error());
								
					if($resultLog){
						$result = true;
					}			
			}
		}						
		return $result;	
	}
		
	
    function validarTamanoArchivo($bytes)
    {
		$tamMaximo = "5";
		$correctSize = true;
		$bytesTot = 0;
		
        if ($bytes >= 1073741824)
        {
            $bytesTot = number_format($bytes / 1073741824, 2);
			$correctSize = false;
        }
        elseif ($bytes >= 1048576)
        {
            $bytesTot = number_format($bytes / 1048576, 2);
			if($bytesTot > $tamMaximo){
				$correctSize = false;
			}
        }

        return $correctSize;
	}

	if(isset($_POST["consultaAjax"])){				
				
		//Acción barra de ayudas
		if($_POST["accion"] === "guardarEdicion"){
			//$_FILES["examinar_imagen_0"]
			echo '<pre>' . __FILE__ . ':' . __LINE__ . ' {' .
			print_r($formulario ) . '}';
			exit;
			// echo json_encode(array("tipo" => "ok", "respuesta" =>$htmlRespuesta));
			// exit();	
		}		
	
		if(isset($_POST["idRegistro"])){
			
			$respuesta = array("error" => false, "mensaje" => "", "arrNuevosDatos" => array());
			
			//Consultamos la información que tenía antes de hacer los cambios
			$sqlGetInfoPrevia = "SELECT *
								FROM ingenia_000006
								WHERE id = ".$_POST["idRegistro"]."
			";
			$resultInfoPrev = mysql_query($sqlGetInfoPrevia, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlGetInfoPrevia."):</b><br>".mysql_error());				
			$rowInfoPrev = mysql_fetch_assoc($resultInfoPrev);			
			
			//Validamos la información 
			$contador 	 	= $_POST["contador"];
			$descripcion 	= $_POST["descripcion"];
			$enlace 		= $rowInfoPrev["Obaten"] != "doc" ?  $_POST["enlace"] : "";
			$posicion 		= $_POST["posicion"];
			$estado 		= $_POST["estado"];
			$nombreImagen 	= $rowInfoPrev["Obaimg"];
		
			//Si hay un nuevo archivo subido se guarda sino se deja el que tenía antes
			$arrFile = $_FILES["examinar_imagen"];
			
			if(isset($arrFile["name"]) && $arrFile["name"] != ""){
				
				if($nombreImagen != $arrFile["name"]){
					$respuesta["error"] = true;
					$respuesta["mensaje"] .= " La imagen debe tener el mismo nombre que la que tenia antes.";
				}else{
					$rutaImagenes = "../../images/medical/ingenia/" ;
					$ruta_provisional = $arrFile["tmp_name"];
					$newRuta = $rutaImagenes . $arrFile["name"] ;
					
					if (file_exists($newRuta)) {
						unlink($newRuta);
					}
					
					move_uploaded_file($ruta_provisional, $newRuta);
					
					//Si fue posible guardar la imagen y borrar la existente se actualiza el nombre.
					$nombreImagen = $arrFile["name"];
				}
			}
			
			$arrFileDoc = $_FILES["examinar_documento"];
			if($arrFileDoc["name"] && $arrFileDoc["name"] != ""){
				
				$urlInicial = consultarAliasPorAplicacion($conex, '01', 'urlDocumentosIngenia');
				$urlEditada =  $urlInicial."".$arrFileDoc["name"];
	
				if($rowInfoPrev["Obaenl"] != $urlEditada){
					$respuesta["error"] = true;
					$respuesta["mensaje"] .= " El documento debe tener el mismo nombre que el anterior.";
				}else{
					$rutaImagenes = "../../images/medical/ingenia/" ;
					$ruta_provisional = $arrFile["tmp_name"];
					$newRuta = $rutaImagenes . $arrFile["name"] ;
					
					if (file_exists($newRuta)) {
						unlink($newRuta);
					}
					
					move_uploaded_file($ruta_provisional, $newRuta);
					
					//Si fue posible guardar la imagen y borrar la existente se actualiza el nombre.
					$enlace = $urlEditada;
				}
			}
			
			if($enlace == ""){
				$enlace = $rowInfoPrev["Obaenl"];
			}
		
			//Si no hay errores con las imagenes se guardan los cambios
			if(!$respuesta["error"]){
				//Guardamos los nuevos datos
				$sqlUpdate = "UPDATE
								ingenia_000006
							 SET 
								obades = '".$descripcion."'
								, obaenl = '".$enlace."'
								, obapos = '".$posicion."'
								, obaest = '".$estado."'
								, obaimg = '".$nombreImagen."'
							WHERE id = ".$_POST["idRegistro"]."
				";
				$resultNewInfo = mysql_query($sqlUpdate, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlUpdate."):</b><br>".mysql_error());				
				
				if(!$resultNewInfo){
					$respuesta["error"] = true;
					$respuesta["mensaje"] = "No ha sido posible guardar los cambios.";
				}else{
					//Se hace un array con los nuevos datos para actualizar la tabla
					$arrNuevosDatos = array(
						"descripcion" 	=> $descripcion,
						"enlace" 		=> $enlace,
						"posicion" 		=> $posicion,
						"estado" 		=> $estado,
						"imagen" 	=> $nombreImagen,
						"contador" 	=> $contador,
					);
					$respuesta["arrNuevosDatos"] = $arrNuevosDatos;
				}
			}
			
			//Retornamos la respuesta
			echo json_encode($respuesta);
			exit();			
		}
				
		if(isset($_POST["crearNuevo"])){
			
			$respuesta = array("error" => false, "mensaje" => "", "arrNuevosDatos" => array());
			
			//Validamos la información 			
			$descripcion 	= $_POST["descripcion"];			
			$estado 		= $_POST["estado"];
			$posicion 		= $_POST["posicion"];
			$nombreImagen 	= "";
		
			//Si hay un nuevo archivo subido se guarda sino se deja el que tenía antes
			$arrFile = $_FILES["nueva_imagen"];
			$grupo = "ingenia";
			$tipoFile = "images";
			$wbasedato = "root";
			$tamañoEstablecido = "5";
			$tamImagen = validarTamanoArchivo($arrFile["size"]);
			
			
			$guardarDoc = false;
			$guardarImagen = false;
			$noEsDocumento = false;
			
			if(isset($arrFile["name"]) && $arrFile["name"] != "" && $tamImagen){
				
				$isValidNameIcon = validarNombreScript($arrFile["name"], $conex, $wbasedato, $grupo);
				
				if($isValidNameIcon){
					//Si fue posible guardar la imagen y borrar la existente se actualiza el nombre.
					$nombreImagen = $arrFile["name"];
					$guardarImagen = true;					
				}else{
					$nombreImagen = "";
					$respuesta["error"] = true;
					$respuesta["mensaje"] .= " Debe cambiar el nombre de la imagen que desea cargar, ya existe en el sistema una imagen con el mismo nombre.";
				}				
			}else{
				$respuesta["error"] = true;
				$respuesta["mensaje"] .= " Por favor verifique la imagen cargada y que ésta no pese más de 5MB.";
			}
			
			if(isset($tipoEnlace)){
				
				//Se valida si fue documento
				if($tipoEnlace == "doc"){
					
					$arrFileDoc = $_FILES["nuevo_doc"];
					$tamDoc = validarTamanoArchivo($arrFileDoc["size"]);
					
					if(isset($arrFileDoc["name"]) && $arrFileDoc["name"] != "" && $tamDoc){
						$isValidName = validarNombreScript( $arrFileDoc["name"], $conex, $wbasedato, $grupo);
						
						if($isValidName){						
							$guardarDoc = true;	
						}else{
							$respuesta["error"] = true;
							$respuesta["mensaje"] .= "<br>Debe cambiar el nombre del documento que desea cargar, ya existe en el sistema un documento con el mismo nombre.";
						}						
					}else{
						$respuesta["error"] = true;
						$respuesta["mensaje"] .= " Por favor verifique el documento cargado y que este no pese más de 5MB.";
					}					
				}	
			}else{
				$enlace = $_POST["enlace"];
				$noEsDocumento = true;
			}
			
			
			//Si la imagen y el documento están bien se deben registrar y copiar en el directorio					
			if($respuesta["error"] === false && (($guardarImagen === true && $guardarDoc === true ) || ($guardarImagen === true && $noEsDocumento === true ))){
				
				$rutaImagenes = "../../images/medical/ingenia/" ;	
				$descripcionImg = "Imagen registrada como icono desde Ingenia"; 
				$resultRegistrarIcono = registrarScript($conex, $grupo, $nombreImagen, $tipoFile, $descripcionImg,$wbasedato);
				if($resultRegistrarIcono){					
					$newRuta = $rutaImagenes . $arrFile["name"] ;
					$ruta_provisionalI = $arrFile["tmp_name"];
										
					move_uploaded_file($ruta_provisionalI, $newRuta);
				}else{
					$respuesta["error"] = true;
					$respuesta["mensaje"] = " No se puedo guardar la imagen por favor consulte con el area de sistemas.";
				}
				
				//Se valida si si cargó documento para registrarlo
				if($noEsDocumento === false){
					$descripcionArc = "Documento cargado desde Ingenia"; 
					$resultRegistrarDoc = registrarScript($conex, $grupo, $arrFileDoc["name"], $tipoFile, $descripcionArc, $wbasedato);	
					if($resultRegistrarDoc){	
						$newRuta = $rutaImagenes . $arrFileDoc["name"] ;					
						$ruta_provisional = $arrFileDoc["tmp_name"];														
						$urlInicial = consultarAliasPorAplicacion($conex, '01', 'urlDocumentosIngenia');
						$enlace =  $urlInicial."".$arrFileDoc["name"];
							
						move_uploaded_file($ruta_provisional, $newRuta);									
					}else{
						$respuesta["error"] = true;
						$respuesta["mensaje"] = " No se puedo guardar el documento por favor consulte con el area de sistemas.";
					}
				}	
			}						
			
			//Guardamos los nuevos datos
			if(!$respuesta["error"]){
				
				$sqlUpdate = "INSERT INTO
							ingenia_000006
								(Medico, Fecha_data,Hora_data, Obades, Obaimg, Obaenl, Obaten, Obapos, Obaest, Seguridad ) 
								VALUES
									('ingenia', '".date("Y-m-d")."', '".date("H:i:s")."', '".$descripcion."', '".$nombreImagen."', '".$enlace."','".$tipoEnlace."', '".$posicion."', '".$estado."', 'C-ingenia')					
				";
				
				$resultNewInfo = mysql_query($sqlUpdate, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlUpdate."):</b><br>".mysql_error());				
				
				if(!$resultNewInfo){
					$respuesta["error"] = true;
					$respuesta["mensaje"] = "No ha sido posible guardar el nuevo registro.";
				}else{
					//Se hace un array con los nuevos datos para actualizar la tabla
					$arrNuevosDatos = array(
						"descripcion" 	=> $descripcion,
						"enlace" 		=> $enlace,
						"posicion" 		=> $posicion,
						"estado" 		=> $estado,
						"imagen" 	=> $nombreImagen,
						"contador" 	=> $contador,
					);
					$respuesta["arrNuevosDatos"] = $arrNuevosDatos;
					$respuesta["mensaje"] = "Se han guardado los datos correctamente.";
				}
			}
			
			
			//Retornamos la respuesta
			echo json_encode($respuesta);
			exit();			
		}
		
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

<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />


<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
<script src="../../../include/root/jquery.min.js"></script>
<script src="../../../include/root/bootstrap.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.easing.min.js"></script>

<script src="../../../include/root/jquery.form.js" type="text/javascript"></script>

<!--<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js"></script>-->
<!--<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>-->

<script type='text/javascript' src='https://johnny.github.io/jquery-sortable/js/jquery-sortable.js'></script>
<!-- Inicio estilos css -->
<style type="text/css">

	.img3d{-
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

function modalAdministradorBarra(){
	
	$.post("administracion_ideas.php",
		{
			consultaAjax:   'on',
			accion:         'administrarBarraAyudas'
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);	
				$("#tituloAdministrarBarra").html("Administrar barra de ayudas");				
				$("#bodyAdministrarBarra").html($.parseHTML(objRespuesta.respuesta));	
				$("#AdministrarBarra").modal();
			
		}).done(function(){
			// $("#imagesOpcion").fileinput();
		});
}

function activarEditarOpcion(contador){
		
	$("#span_posicionOpcion"+contador).hide();
	$("#span_descripcionOpcion"+contador).hide();
	$("#imagenOpcion"+contador).hide();
	$("#span_enlaceOpcion"+contador).hide();
	$("#span_estadoOpcion"+contador).hide();
	
	$("#posicionOpcion"+contador).show();
	$("#descripcionOpcion"+contador).show();
	// $("#imagenOpcion"+contador).show();
	$("#enlaceOpcion"+contador).show();	
	$("#estadoOpcion_on_"+contador).show();	
	$("#estadoOpcion_off_"+contador).show();	
	
	$("#cargarImagen"+contador).show();		
	$("#examinar_imagesOpcion"+contador).show();	
	
	$("#editarOpcion"+contador).hide();	
	$("#guardarOpcion"+contador).show();	
	$("#cancelarOpcion"+contador).show();	
	
	$("#cargarDocumento"+contador).show();		
	
	
}



//Crear una nueva opcion
function guardarNuevaOpcion(){
	
	if($("#posicion").val() != "" && $("#descripcion").val() != "" && $("#nueva_imagen").val() != ""){
		var formData = new FormData($("#nuevo_enlace")[0]);
		var ruta = "administrar_herramientas.php";
		$("#guardarNuevo").prop("disabled", true);
		
		$.ajax({
			url: ruta,
			type: "POST",
			dataType: "json",
			data: formData,
			contentType: false,
			processData: false,
			success: function(datos)
			{		
				if(datos.error){									
					jAlert(datos.mensaje, "Alerta");
				}else{					
					$("#tituloMensaje").html("Confirmaci&oacute;n");								
					$("#bodyMensaje").html($.parseHTML(datos.mensaje));	
					$("#modalMensajes").modal();				
				}
			   $("#guardarNuevo").prop("disabled", false);
			}
		});
	}else{
		var msj = "Por favor ingrese todos los datos antes de guardar.";
		jAlert(msj, "Alerta");
	}
}


function guardarOpcion(contador){
		
	var formData = new FormData($("#opcionBarra_"+contador)[0]);
	var ruta = "administrar_herramientas.php";
	$("#guardarOpcion"+contador).prop("disabled", true);
	$.ajax({
		url: ruta,
	    type: "POST",
		dataType: "json",
	    data: formData,
	    contentType: false,
	    processData: false,
	    success: function(datos)
		{		
			if(datos.error){
				cancelarOpcion(contador, '');					
				jAlert(datos.mensaje, "Alerta");
			}else{
			
				cancelarOpcion(contador, '');		
				$("#span_descripcionOpcion"+contador).text(datos.arrNuevosDatos.descripcion);
				$("#span_posicionOpcion"+contador).text(datos.arrNuevosDatos.posicion);
				$("#span_enlaceOpcion"+contador).text(datos.arrNuevosDatos.enlace);
				$("#imagenOpcion"+contador).attr("src","/matrix/images/medical/ingenia/"+datos.arrNuevosDatos.imagen)				
				var estado = datos.arrNuevosDatos.estado === "on" ? "Activo" : "Inactivo";
				$("#span_estadoOpcion"+contador).text(estado);	
			
			
			
			}
	       $("#guardarOpcion"+contador).prop("disabled", false);
	    }
	});
}
function cancelarOpcion(contador,fondo){		
	$("#span_posicionOpcion"+contador).show();
	$("#span_descripcionOpcion"+contador).show();
	$("#imagenOpcion"+contador).show();
	$("#span_enlaceOpcion"+contador).show();
	$("#span_estadoOpcion"+contador).show();
	
	$("#posicionOpcion"+contador).hide();
	$("#descripcionOpcion"+contador).hide();
	$("#enlaceOpcion"+contador).hide();	
	$("#estadoOpcion_on_"+contador).hide();	
	$("#estadoOpcion_off_"+contador).hide();	
	$("#cargarImagen"+contador).hide();
	
	$("#editarOpcion"+contador).show();	
	$("#guardarOpcion"+contador).hide();	
	$("#cancelarOpcion"+contador).hide();	

	$("#cargarDocumento"+contador).hide();
	$("#examinar_documento"+contador).val("");

	
}

function validarImagenCargada(contador){
	
	if(contador != ""){
		var fileName = $("#examinar_imagesOpcion"+contador).val();
	}else{
		var fileName = $("#nueva_imagen").val();
	}
	
	
	if(fileName != ""){
		var arrFile = fileName.split(".");
		var cont = arrFile.length;
		var cont = cont-1;
		var extension = arrFile[cont];
		
		if(extension != "jpg" && extension != "png"){
			var msj = "Solo esta permitido subir archivos con extensiones jpg y png";
			jAlert(msj, "Alerta");
			$('#examinar_imagesOpcion'+contador).val("");
		}	
	}	
}


function validarDocumentoCargado(contador){

	if(contador != ""){
		var fileName = $("#examinar_documento"+contador).val();
	}else{
		var fileName = $("#nuevo_doc").val();
	}
		
	if(fileName != ""){
		var arrFile = fileName.split(".");
		var cont = arrFile.length;
		var cont = cont-1;
		var extension = arrFile[cont];
		
		if(extension != "pdf" && extension != "doc" && extension != "docx" && extension != "xls" && extension != "xlsx"){
			var msj = "Solo esta permitido subir archivos con extensiones pdf, doc, docx, xls y xlsx";
			jAlert(msj, "Alerta");
			$('#nuevo_doc').val("");
		}	
	}	
}

function habilitarEnlace(tipo){
		
	if(tipo == "documento"){
		$("#enlace").css("display", "none");
		$("#nuevo_doc").css("display", "block");
	}else{
		$('#nuevo_doc').val("");
		$("#enlace").css("display", "block");
		$("#nuevo_doc").css("display", "none");
	}
}

$(document).ready(function(){
		
	$('#volver').click(function() {	
		var idEmpresa = $("#IdEmpresa").val();
		location.href = "registro_nueva_idea.php?wemp_pmla="+idEmpresa;
	})
	
	$('#cerrarVentana').click(function() {			
		window.close();
	})
	
	$('#adicionarEnlace').click(function() {			
		$('#formNuevoEnlace').toggle(500);
	})

	$('#cancelarNuevo').click(function() {			
		$("#formNuevoEnlace input, #formNuevoEnlace textarea").val("");
		$('#formNuevoEnlace').toggle(500);
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
			<button type="button" class="btn btn-primary" id="volver"> Volver</button> 
			<button type="button" class="btn btn-primary" id="cerrarVentana"> Cerrar</button>		
		</div>
	</div>
	
	<div class="row">			
		<h2 class="page-header">Configurar barra de herramientas</h2>
	</div>
	
	<div class="row">	
		<br>
		<button type="button" class="btn btn-primary" id="adicionarEnlace"> + Adicionar nuevo </button> 
		<br><br>	
	</div>
			
	<div class="row" id="formNuevoEnlace" style="display:none; background-color:#f2f2f2; text-align:center">	
		<div class="panel panel-primary"><div class="panel-heading"><b>Crear nuevo enlace</b></div></div>	
		<table  style="width:95%" align="center">	
		<form id='nuevo_enlace' enctype='multipart/form-data' method='post'> 	
	
		<tr class="encabezadoTabla">
			<td><label> Posici&oacute;n</label></td>
			<td><label> Descripci&oacute;n</label></td>
			<td><label> Imagen</label></td>
			<td><label> Tipo de enlace</label></td>
			<td><label> Enlace</label></td>
			<td><label> Estado</label>	</td>	
		</tr>		
		
		<tr class="fila2">		
			<td>
				<input type='text' name='posicion' id="posicion" size='6px'>
			</td>
			<td>
				<input type="hidden" name="consultaAjax" >
				<input type="hidden" name="crearNuevo">
				<input class='form-control input-sm' id='descripcion' name='descripcion' type='text'>
			</td>		
			<td>
				<input id='nueva_imagen' type='file' name='nueva_imagen' onChange='validarImagenCargada("")'>
			</td>
			<td>
				<input type='radio' name='tipoEnlace' value='url' checked='checked' onClick='javascript:habilitarEnlace("url")'> Url <br>
				<input type='radio' name='tipoEnlace' value='doc' onClick='javascript:habilitarEnlace("documento")'> Documento (word, excel, pdf)
			</td>
			<td>	
				
				<textarea class='form-control input-sm' id='enlace' name='enlace'></textarea>
				
				<input id='nuevo_doc' type='file' name='nuevo_doc' onChange='validarDocumentoCargado("")' style='display:none'>
			</td>	
			
			<td>		<label class='radio-inline' id='estadoOpcion_on_'>
						<input type='radio' name='estado' value='on'>Activo
					</label>
					<br>
					<label class='radio-inline' id='estadoOpcion_off_'>
						<input type='radio' name='estado' value='off'>Inactivo
					</label>								
			<td>
		</tr>		
		<tr>	
			<td align="center" colspan='6'><br>			
				<button type='button' class='btn btn-default btn-sm' id='guardarNuevo' onClick='guardarNuevaOpcion()'>Guardar</button>		
				<button type='button' class='btn btn-default btn-sm' id='cancelarNuevo' >Cancelar</button>
			</td>		
		</tr>
		
		</form>	
	</table>
	  </fieldset>				
	</div>
		<br>
	
	<div class="row">	
	 
		<table width="100%" class="tablaListadoIdeas">
			<thead>
			<tr class="encabezadoTabla" align="center" style="height: 40px;">
				<td width="5%">
					Posici&oacute;n
				</td>
				<td width="20%">
					 Descripci&oacute;n
				</td>
				<td width="10%">
					Imagen
				</td>
				<td width="40%">
					Enlace
				</td>
				<td width="5%">
					Estado
				</td>
				<td width="20%">
					Editar
				</td>				
			</tr>	
			</thead>			
		
		<?php
			$fila = "fila1";
			$fondofila = "#c3d9ff";
			
			$qBarra = " SELECT 
							Obades, Obaimg, Obaenl, Obapos, Obaest, id , Obaten
						FROM ingenia_000006  
						ORDER BY Obapos;
			";
				   
			$resBarra = mysql_query($qBarra, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qBarra . " - " . mysql_error());
			$numBarra = mysql_num_rows($resBarra);

			if($numBarra > 0){
				$contador=0;
				while ($rowBarra = mysql_fetch_array($resBarra)) 
				{
					echo "						
						<tr class='".$fila."'>
							<form id='opcionBarra_".$contador."' enctype='multipart/form-data' method='post'> 
								<td style='text-align:center' align='center'>
									<span id='span_posicionOpcion".$contador."' style='background-color:".$fondofila.";border:".$fondofila.";text-align:center'> ".$rowBarra["Obapos"]."</span> 
									<input class='form-control input-sm' id='posicionOpcion".$contador."' name='posicion' type='text' value='".$rowBarra["Obapos"]."' style='display:none;'> 
								</td>	
								
								<td>
									<span id='span_descripcionOpcion".$contador."' style='background-color:".$fondofila.";border:".$fondofila.";text-align:center'> ".utf8_decode($rowBarra["Obades"])."</span> 
									<input class='form-control input-sm' id='descripcionOpcion".$contador."' name='descripcion' type='text' value='".utf8_decode($rowBarra["Obades"])."' style='display:none;'>
								</td>
								
								<td>
									<img id='imagenOpcion".$contador."' class='iconosOpcion' title='".$rowBarra["Obaimg"]."' src='/matrix/images/medical/ingenia/".$rowBarra['Obaimg']."'>
									<div id='cargarImagen".$contador."' style='display:none;'>
										<input id='examinar_imagesOpcion".$contador."' type='file' name='examinar_imagen' onChange='validarImagenCargada(".$contador.")'>
									</div>										
								</td>
								
								<td>								
									<span id='span_enlaceOpcion".$contador."' style='background-color:".$fondofila.";border:".$fondofila.";text-align:center'> ". ( strlen($rowBarra["Obaenl"]) > 50 ? substr($rowBarra["Obaenl"], 0, 50)."..." : $rowBarra["Obaenl"]) ."</span> 
									
								";	
								
								if($rowBarra["Obaten"] != "doc"){
									echo "<textarea class='form-control input-sm' id='enlaceOpcion".$contador."'  name='enlace' style='display:none;'> ".$rowBarra["Obaenl"]."</textarea>
									";
								}else{
									echo "<div id='cargarDocumento".$contador."' style='display:none;'>
											<input id='examinar_documento".$contador."' type='file' name='examinar_documento' onChange='validarDocumentoCargado(".$contador.")'>
										</div>	
									";
								}
								
								echo "</td>									
								<td>
									<span id='span_estadoOpcion".$contador."' style='background-color:".$fondofila.";border:".$fondofila.";text-align:center'> ".( $rowBarra["Obaest"] == "on"  ? "Activo" : "Inactivo") ."</span> 
									<label class='radio-inline' style='display:none;' id='estadoOpcion_on_".$contador."'>
										<input type='radio' name='estado' ".( $rowBarra["Obaest"] == "on"  ? "checked" : "") ." value='on'>Activo
									</label>
									<br>
									<label class='radio-inline' style='display:none;' id='estadoOpcion_off_".$contador."'>
										<input type='radio' name='estado' ".( $rowBarra["Obaest"] == "on"  ? "" : "checked") ." value='off'>Inactivo
									</label>								
								</td>
									<input type='hidden' name='contador' value='".$contador."'>
									<input type='hidden' name='consultaAjax' value=''>
									<input type='hidden' name='idRegistro' value='".$rowBarra["id"]."'>
								<td align='center'>									
									<button type='button' class='btn btn-default btn-sm' id='editarOpcion".$contador."' onClick='activarEditarOpcion(".$contador.");'>Editar</button>
									<button type='button' class='btn btn-default btn-sm' id='guardarOpcion".$contador."' onClick='guardarOpcion(".$contador.");' style='display:none;'>Guardar</button>
									<button type='button' class='btn btn-default btn-sm' id='cancelarOpcion".$contador."' onClick='cancelarOpcion(".$contador.",\"".$fondofila."\");' style='display:none;'>Cancelar</button>
								</td>
							</form>	
						</tr>						
					";
										
					$fila = $fila === "fila1" ? "fila2" : "fila1";
					$fondofila = $fondofila === "#c3d9ff" ? "#e8eef7" : "#c3d9ff";
					
					$contador++;
				}
			}
			?>			
		</table>	
		<br><br>
		
	</div>
			
	
	<!-- Modal para mostrar la información detallada de la idea-->
	<div class="modal fade" id="modalMensajes" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 id="tituloMensaje"></h4>
				</div>
				<div class="modal-body" id="bodyMensaje" style="padding:10px 40px;">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onClick="javascript:location.reload();"data-dismiss="modal">Aceptar</button>
				</div>
			</div>      
		</div>
	</div> 
	<!-- Fin de la modal detalle de la idea-->
			
</div>
</body>
</html>

<?php
	}
?>
