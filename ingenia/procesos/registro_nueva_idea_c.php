<?php
	session_start();

	if(!isset($_SESSION['user']) ){
		echo "<br /><br /><br /><br />
				  <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
					  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
				 </div>";
		return;
	}
	
	//id de la empresa
	$wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
	
	include("conex.php");
	include ("root/comun.php");
	mysql_select_db("matrix");
	$conex = obtenerConexionBD("matrix");
	
	if(isset($_POST["consultaAjax"])){

		if($_POST["accion"] === "guardarFormulario"){
			$params = array();
			parse_str($_POST["form_registro"], $params);	
			
			$titulo       = $params["titulo"];
			$descripcion  = $params["descripcion"];
			$problema     = $params["problema"];
			$resultado	  = $params["resultado"];
			$recursos_inf = $params["recursos_inf"];
			$recursos_per = $params["recursos_per"];
			$recursos_equ = $params["recursos_equ"];
			$eje 		  = $params["eje"];
			$usuario 	  = $_SESSION["usera"];
			$estado       = "01";
			$fecha	      = date("Y-m-d");
			$hora	      = date("H:i:S");
			
			if($titulo != "" && $descripcion != "" && $problema != "" && $resultado != "" && $eje != "" && ($recursos_inf != "" || $recursos_per != "" ||  $recursos_equ != "" && $usuario != "")){
				
				$q = "INSERT INTO ingenia_000001 (Medico, Fecha_data, Hora_data, Idetit, Idedes, Idepro, Ideres, Iderin, Iderpe, Idereq, Ideeje, Ideest, Ideusu, Seguridad) 
							VALUES('ingenia', '".$fecha."','".$hora."','".$titulo."','".$descripcion."','".$problema."','".$resultado."','".$recursos_inf."','".$recursos_per."','".$recursos_equ."','".$eje."','".$estado."','".$usuario."','C-root')";			
				
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				$rs = mysql_query("SELECT MAX(id) AS id FROM ingenia_000001", $conex);
				if ($row = mysql_fetch_row($rs)) {			
					$idea = $row[0];
				}
				if($idea != ""){
					$q = "INSERT INTO ingenia_000002 (Medico, Fecha_data, Hora_data,logIde, logusu, Seguridad) 
											  VALUES ('ingenia', '".$fecha."','".$hora."','".$idea."','".$usuario."', 'C-root') ";
					$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					
					echo json_encode(array("tipo" => "ok", "respuesta" =>"La información se ha guardado correctamente. <br> Número de registro: <b class='bnegrita'>".$idea."</b>"));
					exit();
				}else{
					echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! No se han podido guardar los datos."));
					exit();
				}				
			}else{
				echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! Por favor verifique que los campos obligatorios tengan información"));
				exit();
			}
		}
		
		if($_POST["accion"] === "verDetalleIdea"){
			
			$ididea = isset($_POST["idIdea"]) ? $_POST["idIdea"] : "";
			
			if($idIdea != ""){									  
				$q = "SELECT Idetit, Idedes, Ideeje, Idepro, Ideres, e.Estdes, u.Descripcion, Empdes
						FROM ingenia_000001 i, ingenia_000007 e, usuarios u,root_000050
					   WHERE u.Codigo = Ideusu
					     AND e.Estcod = i.Ideest
						 AND i.id = ".$ididea."
						 AND Empcod = u.Empresa
						 AND Empest = 'on'; 						 
						 ";	  
				
				$result = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(".$q."):</b><br>".mysql_error());				
				$row = mysql_fetch_assoc($result);			
				
				//Se organiza el contenido para devolver
				$htmlRespuesta = "
					<table class='table table-condensed'>
						<tr class='fila1'>
							<td width='30%'><b>Eje de innovaci&oacute;n:</b> </td>
							<td width='70%'>".$row["Ideeje"]."</td>
						</tr>	
						<tr class='fila2'>
							<td><b>Estado:</b> </td>
							<td>".utf8_encode($row["Estdes"])."</td>
						</tr>						
						<tr class='fila1'>
							<td><b>Titulo:</b> </td>
							<td>".$row["Idetit"]."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Descripcion:</b> </td>
							<td>".$row["Idedes"]."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Problemas a resolver:</b> </td>
							<td>".$row["Idepro"]."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Resultado esperado:</b> </td>
							<td>".$row["Ideres"]."</td>
						</tr>
						<tr class='fila1'>
							<td><b>Usuario que publica:</b> </td>
							<td>".utf8_encode($row["Descripcion"])."</td>
						</tr>
						<tr class='fila2'>
							<td><b>Empresa:</b> </td>
							<td>".$row["Empdes"]."</td>
						</tr>
					</table>
				";
				echo json_encode(array("tipo" => "ok", "respuesta" =>$htmlRespuesta));
				exit();				
			}			
		}
		
		if($_POST["accion"] === "listarUltimasIdeas"){
			$wemp_pmla = $_POST["wemp_pmla"];
			$eje = $_POST["eje"];
			$busqueda = $_POST["busqueda"];
			$where = "";
			
			if($eje != ""){				
				$where = "And Ideeje = '".$eje."'";
			}
			
			if($busqueda != ""){
				$where .= "
					AND (Idetit LIKE '%".$busqueda."%' 
					OR Idedes LIKE '%".$busqueda."%' 
					OR Idepro LIKE '%".$busqueda."%' 
					OR Ideres LIKE '%".$busqueda."%' 
					OR Ideest LIKE '%".$busqueda."%' 
					OR i.id = '".$busqueda."' 
					)
				";
			}			
	
			//Se consultan en la tabla las últimas ideas registradas
			/*$q = "SELECT idetit, Idedes, Ideeje, Ideusu , i.id, u.Descripcion, Empdes, Ideest, e.Estdes, e.Estcol
				  FROM ingenia_000001 i, ingenia_000007 e,usuarios u,root_000050
				  WHERE u.Codigo = Ideusu
					 AND e.Estcod = i.Ideest
					 AND Empcod = u.Empresa
					 AND Empest = 'on'
					 ".$where."
				  ORDER BY i.fecha_data; 
			";
			
			$fila = "fila1";
			$res= mysql_query($q, $conex) or die( mysql_error()." <br> ".print_r( $q )  );*/
		
			$htmlRespuesta = "";
			
			while($row = mysql_fetch_assoc($res)) {						
				$htmlRespuesta .= "<li style='background-color:".$row["Estcol"].";'>";
				$htmlRespuesta .= "<b>".$row["Ideeje"]. "</b><br><b>Idea número: </b><b>".$row["id"]."</b><br><b>Estado: </b>".utf8_encode($row["Estdes"])."<br><b>T&iacute;tulo: </b>". $row["idetit"]."</b><br> ";
				//$htmlRespuesta .= substr($row["Idedes"], 0, 150)."...<br>";
				$htmlRespuesta .= "<b>".utf8_encode($row["Descripcion"])."</b>";
				$htmlRespuesta .= "<br><b
				>".$row["Empdes"]."</b>";
				$htmlRespuesta .= "</li>";
						
				$fila = $fila === "fila1" ? "fila2" : "fila1";
			}	
	
			$cuenta = mysql_num_rows($res);
			$cantIdeas = consultarAliasPorAplicacion($conex,$wemp_pmla,"cantIdeasIngenia");
			if($cuenta <= $cantIdeas ){
				$tipo = "quietas";
			}else{
				$tipo = "animadas";
			}		
				
			if($htmlRespuesta == ""){
				$htmlRespuesta .= "<li class='".$fila."'>";
				$htmlRespuesta .= "<b>No hay ideas propuestas para el eje de innovación seleccionado.</b><br> ";
				$htmlRespuesta .= "</li>";
			}	

			echo json_encode(array("tipo" => $tipo, "respuesta" =>$htmlRespuesta));
			exit();			
		}
	
		echo json_encode(array("tipo" => "error", "respuesta" =>"¡Error! No se encontr&oacute; una acci&oacute; a realizar."));
		exit();
		
	}
	else{
?>

<html lang="es-ES">
<head>
<title>REGISTRO DE IDEAS DE CAMBIO</title>
<meta charset="utf-8">
	
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
<script src="../../../include/root/jquery.min.js"></script>
<script src="../../../include/root/bootstrap.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.easing.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.easy-ticker.js"></script>

<!-- Inicio estilos css -->
<style type="text/css">
	.wrapper {
		display: inline-block;
		width: 250px;
		height: 130px;
		vertical-align: top;
		margin: 1em 1.5em 2em 0;
		cursor: pointer;
		position: relative;
		font-family: Tahoma, Arial;
		perspective: 4000px;
	}
	.item {
		text-align: center;
		height: 170px;
		transform-style: preserve-3d;
		transition: transform .6s;
		background: #004b8e; /*#5e87b0 linear-gradient(#6facd5, #497bae) repeat scroll 0 0;*/  
		color: white
	}
	.item .otrop {
		display: block;
		position: absolute;
		top: 0;
		border-radius: 3px;
		box-shadow: 0px 3px 8px rgba(0,0,0,0.3);
		transform: translateZ(50px);
		transition: all .60s;	  
	}

	.item .information {
		color: black;
		display: block;
		position: absolute;
		top: 44px;
		height: 150px;
		width: 250px;
		text-align: left;
		border-radius: 15px;
		padding: 10px;
		font-size: 12px;
		/*text-shadow: 1px 1px 1px rgba(255,255,255,0.5);*/
		box-shadow: none;
		background: #FAFAFA; /*linear-gradient(to bottom,rgba(236,241,244,1) 0%,rgba(190,202,217,1) 100%);*/
		transform: rotateX(-90deg) translateZ(50px);
		transition: all .60s;
	}

	.item:hover {
		transform: translateZ(-60px) rotateX(95deg);
	}

	.item:hover img {
		box-shadow: none;
		border-radius: 15px;
	}

	.item:hover .information {
		box-shadow: 0px 3px 8px rgba(0,0,0,0.3);
		border-radius: 3px;
	}

	.img3d{
		height: 200px;
		width: 310px;
	}

	.principalCompleto{
		width: 100%;
		position: relative;
	}
	
	.principal{
		width: 70%;
		align: center;
		padding-right:20px;padding-left:20px;margin-right:auto;margin-left:auto;
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
		width: 40%;
		/*text-align: center*/
		
	}
	.parteb {
		float: right; 
		width: 90%;
		background-color: withe; 
		text-align: center
	}
	
	.partea2 {
		float: left; 
		width: 50%;
		text-align: center
	}
	.parteb2 {
		float: right; 
		width: 50%;
		background-color: withe; 
		text-align: center
	}
	
	.terminosyCondiciones {
		background-color: #C3D9FF; 
		text-align: center;
		box-shadow: 0px 3px 8px rgba(0,0,0,0.3);
		border-radius: 4px;
		width: 90%;
	}
	
	.barra {
		width: 5%;
		position: absolute;
		right: 0%;
		top: 142px;
	}
	
	.list-group-item.active {
		background-color:#004b8e;
	}
	
	.list-group-item.active:focus {
		background-color:#004b8e;
	}
	
	.iconosBarra{
      display: block;
      margin-left: auto;
      margin-right: auto;
      border:none;
	  max-width: 30px;
      }
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
		width: 100%
	}
	.vticker li{
		list-style: none;
		border-bottom: 1px solid grey;
		padding: 10px;
		border-radius: 10px
	}
	
	.vticker2{
		/*border: 1px solid #ddd;*/
		width: 100%;
	}
	.vticker2 ul{
		padding: 0;
	}
	.vticker2 li{
		list-style: none;
		border-bottom: 1px solid grey;
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
		background-color: #c3d9ff
	}
	.fila2{
		background-color: #e8eef7
	}

	body{
		width: 100%;
		height: 100%
	}

	.bnegrita{
		font-size: 18px
	}
	
	.infoEjesMini {		
		background-color:lightblue; 
		border-color:red;
		border-radius: 16px;
		padding-left: 10px;
		padding-top: 5px;
		padding-bottom: 5px;
		padding-right: 5px;
	}
</style>
<!-- fin estilos css -->

<script type="text/javascript"> 


function abrirVentanEnlace(enlace){
	var ventanaAgenda = window.open(enlace,"miventana","width=1000,height=750");
	
}

function verDetalle(id){	
	$.post("registro_nueva_idea_c.php",
		{
			consultaAjax:   'on',
			accion:         'verDetalleIdea',
			idIdea:        	id
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
				$("#tituloIdea").html("Detalle de la idea innovaci&oacute;n # "+ id);				
				$("#bodyDetalleIdea").html(objRespuesta.respuesta);	
				$("#modalDetalleIdea").modal();
			
		});
}

function getListaIdeasNuevas(){
	
	var wemp_pmla =  $("#IdEmpresa").val();
	var eje = $("#ejeBusqueda").val();
	var busqueda = $("#busquedaGeneral").val();
	
	$.post("registro_nueva_idea_c.php",
		{
			consultaAjax:   'on',
			accion:         'listarUltimasIdeas',
			wemp_pmla:      wemp_pmla,
			eje:			eje,
			busqueda:		busqueda
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
				
				if(objRespuesta.tipo == "animadas"){
					$("#divListadoIdeasQuietas .listUltimasUdeas").html("");
					$("#divListadoIdeasAnimadas .listUltimasUdeas").html(objRespuesta.respuesta);					
				}else{
					$("#divListadoIdeasAnimadas .listUltimasUdeas").html("");
					$("#divListadoIdeasQuietas .listUltimasUdeas").html(objRespuesta.respuesta);					
				}							
			});
			
}
$(document).ready(function(){
	
	//Crgas las ultimas propustas realizadas
	getListaIdeasNuevas();
	
	
	$('.vticker, .ticker2').easyTicker({
		direction: 'up',
		easing: 'swing',
		speed: 3000,
		interval: 4000,
		height: 'auto',
		visible: $("#prueba").val(),
		mousePause: 0,
		controls: {
			up: $("#arriba"),
			down: $("#abajo"),
			toggle: $("#play"),
			playText: "<img src='../../images/medical/ingenia/icono_play.png'> Play",
			stopText: "<img src='../../images/medical/ingenia/icono_pause.png'> Stop"
		}
	});

	$("#aceptarTerminos").click(function(){
		if($('#aceptarTerminos').prop('checked') ) {
			$("#enviarForm").removeAttr("disabled");
		}else{
			$("#enviarForm").attr("disabled", true);
		}
    });

	$("#nuevaIdea").click(function(){
		$("#msjOk").css("display", "none");
		$("#msjError").css("display", "none");		
        $("#myModal").modal();
    });

    $("#enviarForm").click(function(){
		$('#myModal .modal-body').animate({ scrollTop: 0 }, 'slow');
		var form_registro = $("#formRegistro").serialize();
		$.post("registro_nueva_idea_c.php",
		{
			consultaAjax:   		'on',
			accion:         		'guardarFormulario',
			form_registro:        	form_registro
			}, function(respuesta){
				var objRespuesta = $.parseJSON(respuesta);
				
				if(objRespuesta.tipo === "error"){
					$("#msjOk").css("display", "none");
					$("#msjTexto").html(objRespuesta.respuesta);
					$("#msjError").css("display", "block");			
				}else{	
					//Se limpia el formulario 
					$('#formRegistro').trigger("reset");
					
					$("#enviarForm").attr("disabled", true);
					$("#msjError").css("display", "none");					
					$("#msjTextoOk").html(objRespuesta.respuesta);
					$("#msjOk").css("display", "block");
				}
			});
		});
		
	$("#btnAdmin").click(function(){
		
		var idEmpresa = $("#IdEmpresa").val();
		location.href = "administracion_ideas_c.php?wemp_pmla="+idEmpresa;
    });	
		
	$("#ejeBusqueda").change(function(){
		getListaIdeasNuevas();
    });		
		
	$("#busquedaGeneral").blur(function(){
		getListaIdeasNuevas();
    });		
		
	$('#cerrarVentana').click(function() {			
		window.close();
	})	
	
	$("#adminHerramientas").click(function(){		
		var idEmpresa = $("#IdEmpresa").val();
		location.href = "administrar_herramientas_c.php?wemp_pmla="+idEmpresa;
    });	
		
	//$("#imgVerTerminos").click(function(){		
	//	var ventanaAgenda = window.open("http://mx.lasamericas.com.co/matrix/images/medical/ingenia/Terminos y condiciones.pdf","miventana","width=1000,height=750");
    //});	
		
	
});

</script>
</head>
<body>
<div class="principalCompleto">
	<div class="principal">
		<div class="row">
			<img src="../../images/medical/ingenia/logo_ingenia_c.jpg <?=('?a='.rand(1,1000))?>">
			<!--<img src="../../images/medical/ingenia/unidos.jpg<?//=('?a='.rand(1,1000))?>"> -->			
		</div>
		
		<?php
			echo "<input type='hidden' id='IdEmpresa' value='".$wemp_pmla."'>";
			$cantIdeas = consultarAliasPorAplicacion($conex,$wemp_pmla,"cantIdeasIngenia");
			echo "<input type='hidden' id='prueba' value='".$cantIdeas."'>";
		?>
		
		<div class="row"> 
			<?php
				
				//$wactualiz = "Mayo 11 de 2016";
				$wactualiz = "Julio 9 de 2019";
				echo "<span style='float:right;'  class='version' >Versi&oacute;n: $wactualiz </span>";
			?>
		</div>
	
		<!--<div class="row"> -->
		
		<div style="aling:left">
		
		<!--
			<div class="partea">
				<br>			
				<!--<div class="panel panel-primary"><div class="panel-heading"><b>ULTIMAS PROPUESTAS REALIZADAS </b></div></div>
			
				<span>Buscar por eje de innovacion: </span>			
				<select name="select" class="form-control" id="ejeBusqueda">
					<option value="">Todos</option>
					<option value="Gestion del conocimiento">Gesti&oacute;n del conocimiento</option>
					<option value="Servicio">Servicio</option>
					<option value="Tecnologia">Tecnolog&iacute;a</option>
					<option value="Sostenibilidad">Sostenibilidad</option>
				</select>
				<br>
				
				<div class="input-group input-group-sm">				  
				  <input type="text" class="form-control" id="busquedaGeneral" placeHolder="Palabra o n&uacute;mero de idea a buscar">
				  <span class="input-group-addon" style="cursor:pointer">Buscar</span>
				</div> -->
				
				<!--<br> -->
				
				<!--<div style='text-align:center'>
				<button type="button" class="btn btn-default" id="arriba" style="background-color:#eee"><img src='../../images/medical/ingenia/icono_arriba.png'> Arriba</button>
				<button type="button" class="btn btn-default" id="abajo" style="background-color:#eee"> <img src='../../images/medical/ingenia/icono_abajo.png'> Abajo</button>
				<button type="button" class="btn btn-default" id="play" style="background-color:#eee"> </button>
				</div>
				<br>
				<div class="vticker2" id="divListadoIdeasQuietas">				<!-- aca va el listado de ideas animadas que se cargan por ajax				
					<ul class="listUltimasUdeas">
					
					<ul>
				</div>	
				
				<div class="vticker" id="divListadoIdeasAnimadas">				<!-- aca va el listado de ideas animadas que se cargan por ajax				
					<ul class="listUltimasUdeas">
					
					<ul>
				</div>	
				
				
				
			</div>
			
			-->
			
			<div class="parteb">
				<br>
				<div class="panel panel-primary"><div class="panel-heading"><b>EJES DE CAMBIO</b></div></div>
				<div class='partea2'> 
				<div class="wrapper">
				  <div class="item">
				  <img src="../../images/medical/ingenia/gestion.jpg">	
					<b><br>GESTI&Oacute;N DEL CONOCIMIENTO</b>    
					<span class="information">
						<br>
						<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Generaci&oacute;n de conocimientos.<br>
						<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Investigaci&oacute;n cl&iacute;nica.<br> 
						<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Prospectiva y referenciaci&oacute;n.
					</span>
				  </div>
				</div>
				</div>
				<div class="parteb2">
				<div class="wrapper">
				  <div class="item">
					<img src="../../images/medical/ingenia/servicio.jpg">	
					<b><br>SERVICIO</b>    
					<span class="information">	
					<br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Seguridad del paciente y humanizaci&oacute;n.<br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Comunicaci&oacute;n con el paciente y familia. <br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Procesos asistenciales. <br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Interacci&oacute;n con entes de salud. <br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Procesos administrativos.
					</span>
				  </div>
				</div>
				</div>
				<br><br><br><br><br><br><br><br><br><br>
				<div class='partea2'> 
					<div class="wrapper">
					  <div class="item">
						<img src="../../images/medical/ingenia/tecnologia.jpg">	
						<b><br>TECNOLOG&Iacute;A</b>    
						<span class="information">
						<br>
						<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Desarrollo de software.<br>
						<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Uso racional de la energ&iacute;a. <br>
						<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Procedimientos y dispositivos. <br>
						<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Sistemas de comunicaci&oacute;n e inter-operatibilidad. <br>
						<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Equipamiento biom&eacute;dico.
						</span>
					  </div>
					</div>
				</div>
				
				<div class="parteb2">
				<div class="wrapper">
				  <div class="item">
				   <img src="../../images/medical/ingenia/sostenibilidad.jpg">	
					<b><br>SOSTENIBILIDAD</b>    
					<span class="information">
					<br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Medio ambiente. <br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Reducci&oacute;n de costos y gastos. <br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Gesti&oacute;n humana. <br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> Continuidad del negocio. <br>
					<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> &Eacute;tica y buen gobierno.
					</span>
				  </div>
				</div>
				</div>
							
				<br><br><br><br><br><br><br><br><br><br><br><br><br>
							
				<?php 
					$listAdmin = consultarAliasPorAplicacion($conex,$wemp_pmla,"adminitradoresIngenia");
					$usuario 	  = explode("-", $_SESSION["user"]);
					$usuario 	  = isset($usuario[1]) ? $usuario[1] : "";
					$existeCcoConfigurado = strpos($listAdmin, $usuario);
			
					//Si existe ese usuario
					if($existeCcoConfigurado !== false){
						echo "
						<div class='partea2' style='text-align:center'>
							<button type='button' class='btn btn-primary btn-lg' id='btnAdmin'> Administrador de ideas</button>
						</div>
						";
						
						echo "
						<div class='parteb2'>
							<button type='button' class='btn btn-primary btn-lg' id='nuevaIdea'>Registra aqu&iacute; tu idea</button>
						</div>	
						";
					} else {
						echo "
						<div style='text-align: center' class='row'>
							<div>
								<button type='button' class='btn btn-primary btn-lg' id='nuevaIdea'>Registra aqu&iacute; tu idea</button>	
							</div>
						</div>									
						<br><br>					
						";
					}
					
					echo "<br>
					<div style='text-align: center' class='row'>
							<div>
								<button id='cerrarVentana' class='btn btn-primary btn-lg' type='button'> Cerrar</button>	
							</div>
						</div>";
				?>
				
				
				<!--<div class="terminosyCondiciones">
					
					<h3 class="page-header">&nbsp;T&eacute;rminos y condiciones&nbsp;</h3>
							
					<?php/*
						$textTerminosCondiciones = consultarAliasPorAplicacion($conex,$wemp_pmla,"terminosCondicionesIngenia");
						
						echo "<span style='float:center;'>".utf8_encode($textTerminosCondiciones)."</span>"
					*/?>				
					<br><br>
				
				</div>-->
				
			</div>
			
			
			
			
		</div>
		
		
		<!--<div class="container"> -->
			<!-- Inicio de la modal del formulario registro nueva idea-->
		  <div class="modal fade" id="myModal" role="dialog" style="border:dotted">
			<div class="modal-dialog modal-lg ">
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4>Registro de idea</h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;">

					<div id="msjError" class="alert alert-danger alert-dismissable" style="display:none">
						<b id="msjTexto"> </b>
					</div>
					<div id="msjOk" class="alert alert-success alert-dismissable" style="display:none">
						<b id="msjTextoOk"> </b>
					</div>

					<form role="form" id="formRegistro">	
						<label for="mensaje">Se recomienda leer si existen ideas similares a la que desea proponer, accediendo por palabras claves en el inicio de la plataforma, en la casilla buscar.</label>
						<br><br>
						<label for="titulo"><span class="obligatorio">*</span> T&iacute;tulo de la idea:</label>
						<input type="text" class="form-control" id="titulo" name="titulo">
							
						<label for="descripcion"><span class="obligatorio">*</span> Descripci&oacute;n:</label>
						<textarea type="text" class="form-control" id="descripcion" name="descripcion"></textarea>
							
						<label for="problema"><span class="obligatorio">*</span> Problema a resolver:</label>
						<textarea type="text" class="form-control" id="problema" name="problema"></textarea>
				 
						<label for="resultado"><span class="obligatorio">*</span> Resultado esperado:</label>
						<textarea type="text" class="form-control" id="resultado" name="resultado"></textarea>
		   
						<label for="recursos"><span class="obligatorio">*</span> Recursos necesarios:</label><br>
						<table width="100%">
							<tr>
								<td width="2%"></td>
								<td width="30%">Infraestructura f&iacute;sica:</td>
								<td width="4%"></td>
								<td width="30%">Personal:</td>
								<td width="4%"></td>
								<td width="30%">Equipamiento tecnol&oacute;gico:</td>						
							</tr>
							<tr>
							<td></td>
							
							<td><textarea type="text" class="form-control" id="recursos_inf" name="recursos_inf"></textarea></td>
							<td></td>
							<td><textarea type="text" class="form-control" id="recursos_per" name="recursos_per"></textarea></td>
							<td></td>
							<td><textarea type="text" class="form-control" id="recursos_equ" name="recursos_equ"></textarea></td>
						</table>
						 <br>
						<label for="eje"><span class="obligatorio">*</span> Ubique su idea en alguno de los ejes de cambio:</label>
						<select type="text" class="form-control" id="eje" name="eje">
							<option value="">Seleccione</option>
							<option value="Gestion del conocimiento"><b>Gesti&oacute;n del conocimiento</b></option>
							<option value="Servicio">Servicio</option>
							<option value="Tecnologia">Tecnolog&iacute;a</option>
							<option value="Sostenibilidad">Sostenibilidad</option>
						</select>	
					  
						<br>
						
						<table style='font-size:12px'>	
							<tr>
								<td width="25%" class='infoEjesMini'>	
									<b>GESTI&Oacute;N DEL CONOCIMIENTO</b><br><br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'>  
										Generaci&oacute;n de conocimientos.<br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'>  
										Investigaci&oacute;n cl&iacute;nica.<br> 
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'>  
										Prospectiva y referenciaci&oacute;n.								
								</td>
								<td width="2%"></td>
								<td width="25%" class='infoEjesMini'>
									<b>SERVICIO</b><br><br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Seguridad del paciente y humanizaci&oacute;n.<br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Comunicaci&oacute;n con el paciente y familia. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Procesos asistenciales. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Interacci&oacute;n con entes de salud. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Procesos administrativos.								
								</td>
								<td width="2%"></td>
								<td width="23%" class='infoEjesMini'>	
									<b>TECNOLOG&Iacute;A</b><br><br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Desarrollo de software.<br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Uso racional de la energ&iacute;a. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Procedimientos y dispositivos. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Sistemas de comunicaci&oacute;n e inter-operatibilidad. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Equipamiento biom&eacute;dico.								
								</td>
								<td width="2%"></td>
								<td width="23%" class='infoEjesMini'>
									<b>SOSTENIBILIDAD</b><br><br>							
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Medio ambiente. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Reducci&oacute;n de costos y gastos. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Gesti&oacute;n humana. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										Continuidad del negocio. <br>
										<img src='../../images/medical/ingenia/glyphicons-207-ok.png' width='10px' height='10px'> 
										&Eacute;tica y buen gobierno.								
								</td>
							</tr>
						</table>
					  
				
						<!--<h3 class="page-header">T&eacute;rminos y condiciones</h3> -->
						
						<?php
							//$textTerminosCondiciones = consultarAliasPorAplicacion($conex,$wemp_pmla,"terminosCondicionesIngenia");
							
							//echo "<span>".utf8_encode($textTerminosCondiciones)."</span>"
						?>				
						<br><br>
						<!--<input type="checkbox" id="aceptarTerminos" name="aceptarTerminos"> <span class="obligatorio">*</span> <b>Acepto los t&eacute;rminos y condiciones.</b> -->
						<!--<img id='imgVerTerminos' class='iconosOpcion' title='Ver documento de términos y condiciones' src='/matrix/images/medical/ingenia/icono_pdf.png' style="cursor:pointer"> -->
									
						<br><br><b>Los campos marcados con <span class="obligatorio">*</span>  son obligatorios.</b><br><br>
					</form>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" id="enviarForm" > Env&iacute;ar</button>
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal" onClick="javascript:location.reload()">Cancelar</button>
				</div>
			  </div>      
			</div>
		  </div> 
		  <!-- fin de la modal formulario de registro-->
		  
		  <!-- Modal para mostrar la información detallada de la idea-->
		  <div class="modal fade" id="modalDetalleIdea" role="dialog">
			<div class="modal-dialog">
			  <div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 id="tituloIdea"></h4>
				</div>
				<div class="modal-body" id="bodyDetalleIdea" style="padding:10px 40px;">
					
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-success" id="enviarForm" data-dismiss="modal">Aceptar</button>
				</div>
			  </div>      
			</div>
		  </div> 
		  <!-- Fin de la modal detalle de la idea-->
		
		<!--</div>-->
	
	</div>
	
	<?php 
		
	$q = "SELECT Obades,Obaimg,Obaenl,Obapos 
			FROM ingenia_000006 
		   WHERE Obaest='on' 
		   ORDER BY Obapos;";
		   
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$opcionesBarra = array();

	$contador = 0;

	if($num > 0){
		
		while ($row = mysql_fetch_array($res)) 
		{
			$opcionesBarra[$contador]['Obades'] =  utf8_encode(strtoupper($row['Obades']));
			$opcionesBarra[$contador]['Obaimg'] = $row['Obaimg'];
			$opcionesBarra[$contador]['Obaenl'] = $row['Obaenl'];
			$opcionesBarra[$contador]['Obapos'] = $row['Obapos'];
			$contador++;
		}
	} 

	
		?>
			<div class="barra">
				<div class="sidebar-offcanvas" id="sidebar">
					 <!--<div class="list-group">
						<a href="#" class="list-group-item active"><span align="center">Ayudas</span></a> -->
						<?php
							if(count($opcionesBarra)>0)
							{
								foreach( $opcionesBarra as $key => $value)
								{
									echo "<a onclick='javascript:abrirVentanEnlace(\"".$value['Obaenl']."\")' class='list-group-item'>
											<img id='opcBarra'".$value['Obapos']."' class='iconosBarra' title='".$value['Obades']."' src='/matrix/images/medical/ingenia/".$value['Obaimg']."'>
										  </a>";
								}
							}
							
							$listAdmin = consultarAliasPorAplicacion($conex,$wemp_pmla,"adminitradoresAyudasIngenia");
							$usuario 	  = explode("-", $_SESSION["user"]);
							$usuario 	  = isset($usuario[1]) ? $usuario[1] : "";
							$existeAdminConfigurado = strpos($listAdmin, $usuario);
							
							if($existeAdminConfigurado !== false){
								echo '<a class="list-group-item" id="adminHerramientas"><img class="iconosBarra" title="Configurar barra de herramientas" src="/matrix/images/medical/ingenia/configurar_admin.jpg"></a>';
							}						
						?>
					</div>
				</div>
			</div>
		<?php		
	
		
	?>

	
</div>


</body>
</html>

<?php
	}
?>


