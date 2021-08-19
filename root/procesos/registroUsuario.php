<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Programa que permite el registro del documento, correo electrónico, la recuperación 
// 						de usuario y/o contraseña de acceso a Matrix y la firma electrónica.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2020-08-31
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-08-31';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//                
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']) && $proceso!="restablecer")
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	session_start();
	
	include_once("root/comun.php");
	mysql_select_db("matrix");
	$conex = obtenerConexionBD("matrix");
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarDatosUsuario($conex, $tipoIngreso, $datoIngreso)
	{
		if($tipoIngreso=="codigo")
		{
			$query = "SELECT Codigo, Descripcion, Documento, Email 
						FROM usuarios 
					   WHERE Codigo='".$datoIngreso."'
						 AND Activo='A';";
		}
		elseif($tipoIngreso=="documento")
		{
			$query = "SELECT Codigo, Descripcion, Documento, Email 
						FROM usuarios 
					   WHERE Documento='".$datoIngreso."'
					     AND Activo='A';";
		}
		
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$arrayUsuario = array('codigo'=>'','nombre'=>'','documento'=>'','email'=>'');
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			
			$arrayUsuario['codigo'] = $row['Codigo'];
			$arrayUsuario['nombre'] = utf8_encode($row['Descripcion']);
			$arrayUsuario['documento'] = $row['Documento'];
			$arrayUsuario['email'] = utf8_encode($row['Email']);
		}
		
		return $arrayUsuario;
	}
	
	function registrarUsuario($conex, $codigo, $documento, $email)
	{
		$update = " UPDATE usuarios 
					   SET Documento='".$documento."', 
						   Email='".$email."' 
					 WHERE Codigo='".$codigo."';";
	
		$resultadoUpdate = mysqli_query($conex,$update) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		
		$registroCorrecto = false;
		if( mysqli_affected_rows($conex) > 0 )
		{
			$registroCorrecto = true;
		}
		
		return $registroCorrecto;
	}
	
	function generarPasswordTemporal($conex, $codigo)
	{
		$passwordLength = 6;
		$passwordTemporal = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $passwordLength); 
		
		$update = " UPDATE usuarios 
					   SET PasswordTemporal=SHA('".$passwordTemporal."'),
						   FechaPasswordTemp='".date("Y-m-d")."',
						   HoraPasswordTemp='".date("H:i:s")."'
					 WHERE Codigo='".$codigo."';";
	
		$resultadoUpdate = mysqli_query($conex,$update) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		
		if( mysqli_affected_rows($conex) == 0 )
		{
			$passwordTemporal="";
		}
		return $passwordTemporal;
	}
	
	function emailValido($email)
	{
		$matches = null;
		return (1 === preg_match('/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $email, $matches));

	}
	
	function enviarCorreoRestablecer($conex, $codigo, $correo, $asunto, $mensaje, $wemp_pmla)
	{
		$mensajeError = "";
		
		$altbody = "";
		$asunto = utf8_decode($asunto);
		
		$empresa = consultarInstitucionPorCodigo($conex, $wemp_pmla);					 
		$email        		= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailEnviosTI");
		$email        		= explode("--", $email );
		$wremitente			= array( 'email'	=> $email[0],
									 'password' => $email[1],
									 'from' 	=> $email[0],
									 'fromName' => $empresa->nombre,
							 );
		
		$wdestinatario[] = $correo;
		$respuesta = sendToEmail($asunto, $mensaje, $altbody, $wremitente, $wdestinatario);
		
		if($respuesta['Error'])
		{
			$mensajeError = "Correo enviado a ".$correo;
		}
		else
		{
			$mensajeError = "No se pudo enviar el correo a ".$correo;
		}
		
		return $mensajeError;
	}
	
	function restablecerDatosIngreso($conex, $codigo, $documento, $correo, $nombre, $restablecerPassword, $recordarUsuario, $wemp_pmla )
	{
		
		$arrayUsuario = consultarDatosUsuario($conex, "codigo", $codigo);
		$correo = $arrayUsuario['email'];
		
		$mensajeError = "";
		
		if($correo=="")
		{
			$mensajeError = "Debe ingresar un correo electrónico";
		}
		else if(emailValido($correo))
		{
			// establecer tiempo para cambio de clave
			$passwordTemporal = "";
			if($restablecerPassword=="true")
			{
				$passwordTemporal = generarPasswordTemporal($conex, $codigo);
			}
			
			$minutos = consultarAliasPorAplicacion($conex, '01', 'tiempoRestablecerPassword');
			
			$asunto = "";
			$mensaje = "";
			$htmlPassword = "";
			if($restablecerPassword=="true")
			{
					$htmlPassword = "<span style='font-weight:bold;'>Contraseña temporal: </span>".$passwordTemporal."<br><br>
									 <span style='font-style:italic;'>La contraseña temporal tendrá validez por ".$minutos." minutos.</span>";
			}
			
			if($restablecerPassword=="true" && $recordarUsuario=="true")
			{
				if($passwordTemporal!="")
				{
					$asunto = "Restablecer contraseña y recordar usuario Matrix";
				}
			}
			else if($restablecerPassword=="true")
			{
				if($passwordTemporal!="")
				{
					$asunto = "Restablecer contraseña Matrix";
				}
			}
			else if($recordarUsuario=="true")
			{
				$asunto = "Recordar usuario Matrix";
			}
			
			$mensaje = "<div width='80%'>
							<div style='display:flex; align-items:center;'>
								<span style='font-weight:bold; font-size:15pt; display: inline-block'>Datos de ingreso a Matrix</span>
							</div>
							<hr>
							
							De acuerdo a su solicitud realizada el ".date("Y-m-d")." a las ".date("H:i:s")." enviamos las credenciales de acceso a Matrix. <br><br>
							<span style='font-weight:bold;'>Nombre: </span>".$nombre."<br>
							<span style='font-weight:bold;'>Documento de identificación: </span>".$documento."<br>
							<span style='font-weight:bold;'>Código Matrix: </span>".$codigo."<br>
							".$htmlPassword."
						</div>";
			
			$mensajeError = "No se pudo enviar el correo electrónico.";
			
			if($asunto!="" && $mensaje!="")
			{
				$mensajeError = enviarCorreoRestablecer($conex, $codigo, $correo, $asunto, $mensaje, $wemp_pmla);
			}
		}
		else
		{
			$mensajeError = "El correo ".$correo." no es válido";
		}
		
		return $mensajeError;
	}
	
	function actualizarFirmaHCE($conex, $wbasedatoHCE, $codigo, $firmaEncriptada)
	{
		$update = " UPDATE ".$wbasedatoHCE."_000020 
					   SET Usucla='".$firmaEncriptada."',
						   Usufve='".date("Y-m-d",strtotime(date("Y-m-d")."- 1 days"))."'
					 WHERE Usucod='".$codigo."';";
	
		$resultadoUpdate = mysqli_query($conex,$update) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		
		$firmaActualizada = false;
		if( mysqli_affected_rows($conex) > 0 )
		{
			$firmaActualizada = true;
		}
		
		return $firmaActualizada;
	}
	
	
	function restablecerFirma($conex, $wbasedatoHCE, $codigo, $firmaTemporal, $firmaEncriptada, $email, $nombre)
	{
		$firmaActualizada = actualizarFirmaHCE($conex, $wbasedatoHCE, $codigo, $firmaEncriptada);
		
		$mensajeError = "No se pudo actualizar la firma electronica";
		if($firmaActualizada)
		{
			$asunto = "Restablecer firma electronica";
			
			$mensaje = "<div width='80%'>
							<div style='display:flex; align-items:center;'>
								<span style='font-weight:bold; font-size:15pt; display: inline-block'>Restablecimiento de firma electrónica</span>
							</div>
							<hr>
							
							De acuerdo a su solicitud realizada el ".date("Y-m-d")." a las ".date("H:i:s")." enviamos su nueva firma electrónica temporal que deberá actualizar al ingresar a la Historia Clínica Electrónica en Matrix. <br><br>
							<span style='font-weight:bold;'>Nombre: </span>".$nombre."<br>
							<span style='font-weight:bold;'>Código Matrix: </span>".$codigo."<br>
							<span style='font-weight:bold;'>Firma electrónica temporal: </span>".$firmaTemporal."<br>
						</div>";
			
			$mensajeError = enviarCorreoRestablecer($conex, $codigo, $email, $asunto, $mensaje, "01");
		}
		
		return $mensajeError;
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
		case 'consultarDatosUsuario':
		{	
			$data = consultarDatosUsuario($conex, $tipoIngreso, $datoIngreso);
			echo json_encode($data);
			break;
			return;
		}
		case 'registrarUsuario':
		{	
			$data = registrarUsuario($conex, $codigo, $documento, $email);
			echo json_encode($data);
			break;
			return;
		}
		case 'restablecerDatosIngreso':
		{
			$data = restablecerDatosIngreso($conex, $codigo, $documento, $email, $nombre, $restablecerPassword, $recordarUsuario, $wemp_pmla );
			echo json_encode($data);
			break;
			return;
		}
		case 'restablecerFirma':
		{
			$data = restablecerFirma($conex, $wbasedatoHCE, $codigo, $firmaTemporal, $firmaEncriptada, $email, $nombre);
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
	<!DOCTYPE html>
	<html>
	<head>
	  <title>Registro usuario</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
		<script src="../../../include/root/jquery.min.js"></script>
		<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>
				
		<link rel='stylesheet' href='../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css'>
			

		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" >
		<link rel="stylesheet" href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css">

		<link rel="stylesheet" href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css">
		
		<script   src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js" ></script>
		

		
		<!-- Bootstrap -->
		<link href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
		<!-- Font Awesome -->
		<link href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		
		
		<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

		<script src="../../../include/root/bootstrap.min.js"></script>
		
		
		<!-- Bootstrap -->
		<script src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	$( document ).ready(function(){
		
		consultarDatosUsuario();
	});
	
	window.onload = function() {
		var myInput = document.querySelectorAll('input');
		myInput.forEach(function(input) {
			input.onpaste = function(e) {
				e.preventDefault();
				$("#mensajeAlerta").html("En esta p&aacute;gina no se permite la acci&oacute;n pegar");
				$("#divAlerta").modal("show");
			}
		  
			input.oncopy = function(e) {
				e.preventDefault();
				$("#mensajeAlerta").html("En esta p&aacute;gina no se permite la acci&oacute;n copiar");
				$("#divAlerta").modal("show");
			}
		});
	}
	
	function consultarDatosUsuario()
	{
		const codigo = document.getElementById('codigo').value;
		
		$.ajax({
			url: "registroUsuario.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarDatosUsuario',
				tipoIngreso		: 'codigo',
				datoIngreso		: codigo
				},
				async: false,
				success:function(result) {
					console.log(result)
					document.getElementById('nombre-usuario').textContent = result.nombre;
					document.getElementById('documento').value = result.documento;
					document.getElementById('email').value = result.email;
				}
		});
	}
	
	function validarEmail(email)
	{
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
	
	function validarCampos(documento,email,confDocumento,confEmail)
	{
		let validacionCampos = {};
		
		validacionCampos.datosCompletos = true;
		validacionCampos.mensaje = "";
		
		$("input").removeClass("campoValido campoInvalido");
		
		if(documento=="")
		{
			validacionCampos.mensaje += "- Debe ingresar un documento<br>";
			$("#documento").addClass("campoInvalido");
		}
		
		if(email=="")
		{
			validacionCampos.mensaje += "- Debe ingresar un correo electr&oacute;nico<br>";
			$("#email").addClass("campoInvalido");
		}
		else
		{
			if(!validarEmail(email))
			{
				validacionCampos.mensaje += "- Debe ingresar un correo electr&oacute;nico v&aacute;lido<br>";
				$("#email").addClass("campoInvalido");
			}
		}
		
		if(documento!=confDocumento)
		{
			validacionCampos.mensaje += "- El documento de identificaci&oacute;n es diferente<br>";
			$("#documento").addClass("campoInvalido");
			$("#confDocumento").addClass("campoInvalido");
			
		}
		
		if(email!=confEmail)
		{
			validacionCampos.mensaje += "- El correo electr&oacute;nico es diferente<br>";
			$("#email").addClass("campoInvalido");
			$("#confEmail").addClass("campoInvalido");
		}
		
		if(validacionCampos.mensaje!="")
		{
			validacionCampos.datosCompletos = false;
		}
		else
		{
			$("#documento").addClass("campoValido");
			$("#confDocumento").addClass("campoValido");
			$("#email").addClass("campoValido");
			$("#confEmail").addClass("campoValido");
		}
		
		return validacionCampos;
	}
	
	function registrarUsuario()
	{
		const codigo = document.getElementById('codigo').value;
		const documento = document.getElementById('documento').value.trim();
		const email = document.getElementById('email').value.trim();
		const confDocumento = document.getElementById('confDocumento').value.trim();
		const confEmail = document.getElementById('confEmail').value.trim();
		
		const validacionCampos = validarCampos(documento,email,confDocumento,confEmail);
		
		if(validacionCampos.datosCompletos)
		{
			$.ajax({
				url: "registroUsuario.php",
				type: "POST",
				dataType: "json",
				data:{
					consultaAjax 	: '',
					accion			: 'registrarUsuario',
					codigo			: codigo,
					documento		: documento,
					email			: email
					},
					async: false,
					success:function(result) {
						let mensaje = "No se pudieron registrar los datos del usuario";
						if(result)
						{
							mensaje = "Se registraron correctamente los datos del usuario";
						}
						
						$("#mensajeAlerta").html(mensaje);
						$("#btnCerrarAlert").hide();
						$("#btnAceptarAlert").show();
						$("#divAlerta").modal("show");
						
					}
				});
		}
		else
		{
			$("#mensajeAlerta").html(validacionCampos.mensaje);
			$("#divAlerta").modal("show");
		}
	}
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		body
		{
			width: auto;
			height: auto;
			background-color: #FFFFFF;
			color: #000000;
		}
				
		.panel-primary {
			border-color: #2A5DB0;
		}
		
		.panel-primary > .panel-heading {
			color: #fff;
			background-color: #2A5DB0;
			border-color: #2A5DB0;
		}
		
		.btnMatrix{
			background-color: #2A5DB0;
			color: #FFFFFF;
		}
		
		.btnMatrix:hover {
			background-color: #234d90;
			color: #FFFFFF;
		}
		
		.modal-header {
			background-color: #2A5DB0;
			padding:1px;
			color:#FFF;
			border-bottom:2px dashed #2A5DB0;
			font-weight: bold;
		}
		
		.modal-Alerta {
			background-color: #2A5DB0;
			padding:16px 16px;
			color:#FFF;
			border-bottom:2px dashed #2A5DB0;
			font-weight: bold;
			font-size: 10pt;
		}
		
		.panel-body {
			padding: 8px;
		}
		
		.campoValido {
			border: 2px solid #0AAB02;
		}
		
		.campoInvalido {
			border: 2px solid #CA0808;
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
	encabezado("GESTION DE USUARIO", $wactualiz, 'clinica');
	?>
		
		<input type='hidden' id='codigo' name='codigo' value='<?php echo $codigo ?>'>
		
		<div class="container-fluid">
			<div id="divFiltros" class="col-lg-12 col-md-8 col-sm-8 col-xs-8 " style="text-align:center;">
				<p style="text-align:left;">&nbsp;&nbsp;Por favor diligencie los siguientes datos: </p><br>
				<div class="panel panel-primary">
					<div class="panel-heading" id="nombre-usuario">Usuario</div>
					<div class="panel-body">
						<form>
							<div class="col-lg-12">
								<div class="form-group col-lg-3" class="control-label" >
									<label for="documento" class="control-label">Documento de identificaci&oacute;n</label>
									<input id='documento' class='form-control' type='text' style='height:32px'  value="<?php echo $historia; ?>" tabindex=1>
								</div>
								<div class="form-group col-lg-4" class="control-label" >
									<label for="email" class="control-label">Correo electr&oacute;nico</label>
									<input id='email' class='form-control' type='email' style='height:32px;' tabindex=3>
								</div>
								<div class="form-group col-lg-2" style="top:24px;text-align:left;">
									<button type="button" id="btnGuardar" class="btn btnMatrix" onclick="registrarUsuario();" tabindex=5>Guardar</button>
								</div>
							</div>
							<div class="col-lg-12">
								<div class="form-group col-lg-3" class="control-label" >
									<label for="confDocumento" class="control-label">Confirmar documento de identificaci&oacute;n</label>
									<input id='confDocumento' class='form-control' type='text' style='height:32px'  value="<?php echo $historia; ?>" tabindex=2>
								</div>
								<div class="form-group col-lg-4" class="control-label" >
									<label for="confEmail" class="control-label">Confirmar correo electr&oacute;nico</label>
									<input id='confEmail' class='form-control' type='email' style='height:32px;' tabindex=4>
								</div>
								
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<div id='divModalRestablecer' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm'>
					<div class='modal-content'>
						<div class='modal-Alerta'>RESTABLECER USUARIO O CONTRASE&Ntilde;A</div>
						<div class='modal-body' id='bodyModal'>
							<form>
								<div class='col-lg-12'>
									<div class='form-group col-lg-1' class='control-label' >
										<label for='tipoUsuario' class='control-label'>Seleccione</label>
										<select id='tipoUsuario' class='form-control col-lg-1' style='height:32px;'>
											<option value='codigo'>Usuario Matrix</option>
											<option value='documento'>Documento de identificaci&oacute;n</option>
										</select>
									</div>
									<div class='form-group col-lg-2' class='control-label' >
										<label for='datoIngreso' class='control-label'>Documento de identificaci&oacute;n</label>
										<input id='datoIngreso' class='form-control' type='text' style='height:32px'  value=''>
									</div>
									<div class='form-group col-lg-5' class='control-label' >
										<label for='email' class='control-label'>
											Correo electr&oacute;nico al &nbsp;&nbsp;&nbsp;&nbsp;
										</label>
										<input id='email' class='form-control' type='email' style='height:32px;' disabled>
									</div>
									<div class='form-group col-lg-4' style='top:24px;text-align:left;'>
										<button type='button' id='btnConsultarHCE' class='btn btnMatrix' onclick='consultarHCE();' disabled>HCE</button>
										<button type='button' id='btnConsultarOrdenes' class='btn btnMatrix' onclick='consultarOrdenes();' disabled>Ordenes</button>
										<button type='button' id='btnIniciar' class='btn btn-default' onclick='iniciarCampos(true);'>Iniciar</button>
										<button type='button' id='btnCerrar' class='btn btn-default' onclick='cerrarVentana();'>Cerrar</button>
									</div>
								</div>
							</form>
						</div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' id='btnAceptar' data-dismiss='modal'>Cerrar</button>
							<button type='button' class='btn btnMatrix' id='btnCerrarModal' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>
			
			<div id='divAlerta' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm'>
					<div class='modal-content'>
						<div class='modal-Alerta'>ALERTA</div>
						<div class='modal-body' id='mensajeAlerta'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' id='btnAceptarAlert' data-dismiss='modal' style='display:none;' onclick="location.href='../../F1.php?accion=I'">Aceptar</button>
							<button type='button' class='btn btnMatrix' id='btnCerrarAlert' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>
			
			<div id='divMensajeEspere' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm' style='display: block;'>
					<div class='modal-content'>
						<div class='modal-body' id='mensajeEspere'><br><p align='center'><img src='../../images/medical/ajax-loader5.gif'/>&nbsp;&nbsp;&nbsp;Por favor espere un momento...</p><br></div>
					</div>
				</div>
			</div>
			
			
		</div>
		<!-- /page content -->

	</body>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</html>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
