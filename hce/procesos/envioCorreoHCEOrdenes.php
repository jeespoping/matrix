<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Programa que permite el envío de la HCE y órdenes médicas del paciente seleccionado,
// 						al hacer clic en el cada botón abre un iframe con el programa respectivo enviando 
// 						como parámetro el correo electrónico, posteriormente se realiza el envío del archivo
// 						agregándole como contraseña al pdf el número de documento del paciente.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2020-04-02
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-05-04';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2020-05-04 - Jessica Madrid Mejía:	- Se modifica la función consultarPaciente(), si no encuentra los datos del paciente en cliame,
// 										  clisur o idc 000100 busca los datos del paciente en root_000037
// 										- Para determinar el correo de origen (desde donde se envía la HCE y Ordenes) se modificó la función 
// 										  consultarEmailEnvio(), si en el maestro de empresas (cliame_000024) no hay un correo de origen 
// 										  configurado en el campo Empcee, consulta si el centro de costos del paciente tiene un correo configurado 
// 										  (Ccocee en movhos_000011) de lo contrario debe tomar el correo del parámetro emailEnvioHce en root_000051.
// 										- Se modifica la función enviarPdf() para armar dinamicamente el mensaje del correo y el asunto si estos 
// 										  parámetros llegan vacios a la función.
//  2020-04-03 - Jessica Madrid Mejía:	Se modifica el correo electrónico de origen (desde donde se envia) para que por defecto use el
// 										correo configurado en el parámetro emailEnvioHce de root_000051 si no encontró un correo en
// 										el maestro de empresas (cliame_000024 campo Empcee) y se agrega el correo de origen al log de envio.
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
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	
	include_once("root/comun.php");
	mysql_select_db("matrix");
	$conex = obtenerConexionBD("matrix");
	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoCliame = consultarAliasPorAplicacion($conex,$wemp_pmla , 'facturacion');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarIngresosPaciente($conex, $wemp_pmla, $historia)
	{
		$query = "SELECT Oriing 
					FROM root_000037 
				   WHERE Orihis='".$historia."' 
				     AND Oriori='".$wemp_pmla."';";
					 
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$ingresos = array();
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			for($i=$row['Oriing'];$i>=1;$i--)
			{
				$ingresos[] = $i;
			}
		}
		
		return $ingresos;
	}
	
	function consultarPaciente($conex, $wbasedatoCliame, $wemp_pmla, $historia)
	{
		$query = "SELECT Pactdo,Pacdoc,Paccor 
					FROM ".$wbasedatoCliame."_000100 
				   WHERE Pachis='".$historia."';";
					 
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$paciente = array();
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			$paciente['tipoDocumento'] = $row['Pactdo'];
			$paciente['documento'] = $row['Pacdoc'];
			$paciente['email'] = trim($row['Paccor']);
		}
		else
		{
			$query = "SELECT Oriced, Oritid
						FROM root_000037
					   WHERE Orihis='".$historia."'
					     AND Oriori='".$wemp_pmla."';";
						 
			$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
			$num = mysql_num_rows($res);
			
			if($num>0)
			{
				$row = mysqli_fetch_array($res);
				$paciente['tipoDocumento'] = $row['Oritid'];
				$paciente['documento'] = $row['Oriced'];
				$paciente['email'] = "";
			}
		}
		
		return $paciente;
	}
	
	function consultarInformacionPaciente($conex, $wbasedatoCliame, $wemp_pmla, $historia)
	{
		$informacionPaciente = consultarPaciente($conex, $wbasedatoCliame, $wemp_pmla, $historia);
		$informacionPaciente['ingresos'] = consultarIngresosPaciente($conex, $wemp_pmla, $historia);
		
		return $informacionPaciente;
	}
	
	function consultarEmailEntidad($conex, $wbasedatoCliame, $wbasedatoMovhos, $wemp_pmla, $historia, $ingreso)
	{
		$query = "SELECT Ingnre,Empmai 
					FROM ".$wbasedatoMovhos."_000016 
			   LEFT JOIN ".$wbasedatoCliame."_000024
					  ON Empcod=Ingres
				   WHERE Inghis='".$historia."' 
					 AND Inging='".$ingreso."';";
					 
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$entidad = array();
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			$entidad['descripcion'] = utf8_encode($row['Ingnre']);
			
			$email = "";
			if($row['Empmai']!="NO APLICA" && $row['Empmai']!=".")
			{
				$email = $row['Empmai'];
			}
			$entidad['email'] = $email;
		}
		
		return $entidad;
	}
	
	function consultarDocumento($conex, $wemp_pmla, $historia)
	{
		$query = "SELECT Oriced 
					FROM root_000037 
				   WHERE Orihis='".$historia."' 
				     AND Oriori='".$wemp_pmla."';";
					 
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$documento = array();
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			$documento = $row['Oriced'];
		}
		
		return $documento;
	}

	function emailValido($email)
	{
		$matches = null;
		return (1 === preg_match('/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $email, $matches));

	}
	
	function consultarEmailEnvio($conex, $wbasedatoMovhos, $wemp_pmla, $historia, $ingreso)
	{
		$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
		
		// Validar por responsable del paciente
		$query = "SELECT Empcee
					FROM ".$wbasedatoMovhos."_000016 
			   LEFT JOIN ".$wbasedatoCliame."_000024
					  ON Empcod=Ingres
				   WHERE Inghis='".$historia."' 
					 AND Inging='".$ingreso."';";
					 
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$email = "";
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			
			if($row['Empcee']!="NO APLICA" && $row['Empcee']!=".")
			{
				$email = $row['Empcee'];
			}
		}
		
		if($email == "")
		{
			// Validar por centro de costos del paciente
			$query = "SELECT Ccocee
					    FROM ".$wbasedatoMovhos."_000018 
				  INNER JOIN ".$wbasedatoMovhos."_000011
						  ON Ccocod=Ubisac
					   WHERE Ubihis='".$historia."' 
						 AND Ubiing='".$ingreso."';";
						 
			$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
			$num = mysql_num_rows($res);
			
			if($num>0)
			{
				$row = mysqli_fetch_array($res);
				
				if($row['Ccocee']!="NO APLICA" && $row['Ccocee']!=".")
				{
					$email = $row['Ccocee'];
				}
			}
		}
		
		return $email;
	}
	
	function registrarLog($conex, $historia, $ingreso, $correo, $nombreArchivo, $mensaje, $tipo, $wbasedatoMovhos, $usuario, $correoOrigen)
	{
		$insert = " INSERT INTO ".$wbasedatoMovhos."_000281(Medico,Fecha_data,Hora_data,Loghis,Loging,Logtip,Logpdf,Logema,Logmsj,Logusu,Logeor,Seguridad) 
													VALUES ('".$wbasedatoMovhos."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$tipo."','".$correo."','".$nombreArchivo."','".$mensaje."','".$usuario."','".$correoOrigen."','C-".$wbasedatoMovhos."');";
	
		$resultadoInsertar = mysqli_query($conex,$insert) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
	}
	
	function enviarPdf($conex, $wemp_pmla, $historia, $ingreso, $correo, $rutaArchivo, $nombreArchivo, $asunto, $mensaje, $prefijo, $wbasedatoMovhos, $usuario, $envioPaciente, $nombrePaciente, $nombreEntidad, $nombreEmpresa, $tiposOrdenesGeneradas)
	{
		$mensajeError = "";
		
		if($correo=="")
		{
			$mensajeError = "Debe ingresar un correo electronico";
		}
		else if(emailValido($correo))
		{
			$nombreArchivoAdjunto = $nombreArchivo;
			if($envioPaciente=="on")
			{
				$password = consultarDocumento($conex, $wemp_pmla, $historia);
				$nombreArchivoAdjunto = $prefijo."_".$nombreArchivo;
				$respuesta = shell_exec( "qpdf --encrypt ".$password." ".$password." 256 -- ".$rutaArchivo."/".$nombreArchivo." ".$rutaArchivo."/".$nombreArchivoAdjunto);
			}
			
			$altbody = "";
			
			if($asunto=="")
			{
				// HCE
				if($prefijo=="HC")
				{
					// Asunto pacientes
					if($envioPaciente=="on")
					{
						$asunto = "Historia clínica relacionada con su consulta médica ".$nombrePaciente;
					}
					else // Asunto entidades
					{
						$asunto = "Historia clínica relacionada con la consulta médica del paciente ".$nombrePaciente;
					}
				}
				elseif($prefijo=="OM") // Ordenes
				{
					// Asunto pacientes
					if($envioPaciente=="on")
					{
						$asunto = "Ordenes relacionadas con su consulta médica ".$nombrePaciente;
					}
					else // Asunto entidades
					{
						$asunto = "Ordenes relacionadas con la consulta médica del paciente ".$nombrePaciente;
					}
				}
				
				$asunto = utf8_decode($asunto);
			}
			
			if($mensaje=="")
			{
				$nombrePaciente = utf8_decode($nombrePaciente); 
				$nombreEntidad = utf8_decode($nombreEntidad);
				$nombreEmpresa = utf8_decode($nombreEmpresa);
				$tiposOrdenesGeneradas = utf8_decode($tiposOrdenesGeneradas);
				
				// HCE
				if($prefijo=="HC")
				{
					// Mensaje pacientes
					if($envioPaciente=="on")
					{
						$mensaje = consultarAliasPorAplicacion( $conex, $wemp_pmla, "mensajeCorreoEnvioHcePaciente");
					}
					else // Mensaje entidades
					{
						$mensaje = consultarAliasPorAplicacion( $conex, $wemp_pmla, "mensajeCorreoEnvioHceEntidad");
					}
				}
				elseif($prefijo=="OM") // Ordenes
				{
					// Mensaje pacientes
					if($envioPaciente=="on")
					{
						$mensaje = consultarAliasPorAplicacion( $conex, $wemp_pmla, "mensajeCorreoEnvioOrdenesPaciente");
					}
					else // Mensaje entidades
					{
						$mensaje = consultarAliasPorAplicacion( $conex, $wemp_pmla, "mensajeCorreoEnvioOrdenesEntidad");
					}
					
					$mensaje = str_replace("tiposOrdenesGeneradas",$tiposOrdenesGeneradas,$mensaje);
				}

				$mensaje = str_replace("wemp_pmla",$wemp_pmla,$mensaje);
				$mensaje = str_replace("nombrePaciente",$nombrePaciente,$mensaje);
				$mensaje = str_replace("nombreEntidad",$nombreEntidad,$mensaje);
				$mensaje = str_replace("nombreEmpresa",$nombreEmpresa,$mensaje);
			}
			
			
			// Consultar si el responsable o el centro de costos del paciente tiene email de envío configurado
			$email = consultarEmailEnvio($conex, $wbasedatoMovhos, $wemp_pmla, $historia, $ingreso);
			if($email=="")
			{
				$email = consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailEnvioHce");
			}
			
			$nombreEmpresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "nombreEmpresa");
			$email = explode("--", $email );
			$wremitente =  array('email'	=> $email[0],
								 'password' => $email[1],
								 'from' 	=> $email[0],
								 'fromName' => $nombreEmpresa,
								);
							 
			$wdestinatario[] = $correo;
			$respuesta = sendToEmail($asunto, $mensaje, $altbody, $wremitente, $wdestinatario, $rutaArchivo."/".$nombreArchivoAdjunto, $nombreArchivoAdjunto);
			
			if($envioPaciente=="on")
			{
				// borrar el pdf encriptado despues de enviar el correo
				if(file_exists($rutaArchivo."/".$nombreArchivoAdjunto))
					unlink($rutaArchivo."/".$nombreArchivoAdjunto);
			}
			
			if($respuesta['Error'])
			{
				$mensajeError = "Correo enviado a ".$correo;
			}
			else
			{
				$mensajeError = "No se pudo enviar el correo a ".$correo;
			}
			
			registrarLog($conex, $historia, $ingreso, $correo, $nombreArchivoAdjunto, $mensajeError, $prefijo, $wbasedatoMovhos, $usuario, $email[0]);
		}
		else
		{
			$mensajeError = "El correo ".$correo." no es valido";
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
		case 'consultarPaciente':
		{	
			$data = consultarInformacionPaciente($conex, $wbasedatoCliame, $wemp_pmla, $historia);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarEmailEntidad':
		{	
			$data = consultarEmailEntidad($conex, $wbasedatoCliame, $wbasedatoMovhos, $wemp_pmla, $historia, $ingreso);
			echo json_encode($data);
			break;
			return;
		}
		case 'enviarPdf':
		{	
			$data = enviarPdf($conex, $wemp_pmla, $historia, $ingreso, $email, $rutaArchivo, $nombreArchivo, $asunto, $mensaje, $prefijo, $wbasedatoMovhos, $usuario, $envioPaciente, $nombrePaciente, $nombreEntidad, $nombreEmpresa, $tiposOrdenesGeneradas);
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
		  <title>Envio de historia clinica y ordenes a pacientes</title>
		  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		</head>
		
		<script src="../../../include/root/jquery.min.js"></script>
		<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>
		
		
		<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
		<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
		<link rel='stylesheet' href='../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css'>
		<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
		<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
			

		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" >
		<link rel="stylesheet" href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css">

		<link rel="stylesheet" href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css">
		
		<script   src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js" ></script>
		

		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
		
		
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
		
		if($('#historia').val()!="")
		{
			consultarPaciente();
			
			// seleccionar ingreso
			$("#ingreso option[value="+$("#ingresoActual").val()+"]").attr("selected",true);
			$('#btnIniciar').hide();
		}
		
		// Solo mostrar el botón de ordenes si es cliame
		if($('#wemp_pmla').val()!="01")
		{
			$('#btnConsultarOrdenes').hide();
		}
	});
	
	function deshabilitarCampos(habilitar)
	{
		$("#ingreso").prop( "disabled",habilitar );
		$("#email").prop( "disabled",habilitar );
		$("#btnConsultarHCE").prop( "disabled", habilitar );
		$("#btnConsultarOrdenes").prop( "disabled", habilitar );
	}
	
	function consultarPaciente()
	{
		iniciarCampos(false);
		
		$.ajax({
			url: "envioCorreoHCEOrdenes.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarPaciente',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoCliame	: $('#wbasedatoCliame').val(),
				historia		: $('#historia').val()
				},
				async: false,
				success:function(informacionPaciente) {
					
					$("#email").val(informacionPaciente.email);
					$("#emailPaciente").val(informacionPaciente.email);
					$("#documento").val(informacionPaciente.documento);
					$("#tipoDocumento").val(informacionPaciente.tipoDocumento);
					
					ingresos = informacionPaciente.ingresos;
					
					if(Object.keys(informacionPaciente.ingresos).length>0)
					{
						for(ingreso in ingresos)
						{
							html = "<option value='"+ingresos[ingreso]+"'> "+ingresos[ingreso]+"</option>";
							
							$("#ingreso").append(html);
						}
						
						deshabilitarCampos(false);
					}
					else
					{
						alert("Ingrese una historia valida")
					}
				}
		});
	}
	
	function correoPaciente()
	{
		$("#divConsulta").html("");
		$("#divConsulta").hide();
		
		$("#infoEntidad").attr('title','');
		$("#infoEntidad").hide();
		
		$("#email").val($("#emailPaciente").val());
	}
	
	function correoEntidad()
	{
		$("#divConsulta").html("");
		$("#divConsulta").hide();
		
		$("#infoEntidad").attr('title','');
		$("#infoEntidad").hide();
		
		$.ajax({
			url: "envioCorreoHCEOrdenes.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarEmailEntidad',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoCliame	: $('#wbasedatoCliame').val(),
				wbasedatoMovhos	: $('#wbasedatoMovhos').val(),
				historia		: $('#historia').val(),
				ingreso			: $('#ingreso').val()
				},
				async: false,
				success:function(informacionEntidad) {
					
					$("#email").val(informacionEntidad.email);
					$("#infoEntidad").attr('title',informacionEntidad.descripcion);
					$("#infoEntidad").tooltip();
					$("#infoEntidad").show();
				}
		});
	}
	
	function validarEmail(email)
	{
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		return regex.test(email);
	}
	
	function consultarHCE()
	{
		$("#divConsulta").html("");
		$("#divConsulta").hide();
		
		emailValido = validarEmail($("#email").val());
		
		if(emailValido)
		{
			var envioPaciente = "off";
			if($('input:radio[name=radioEmail]:checked').val()=="paciente")
			{
				envioPaciente = "on";
			}
			var urlIframe = "HCE_Impresion.php?empresa="+$("#wbasedatoHce").val()+"&wdbmhos="+$("#wbasedatoMovhos").val()+"&origen="+$("#wemp_pmla").val()+"&wcedula="+$("#documento").val()+"&wtipodoc="+$("#tipoDocumento").val()+"&wing="+$("#ingreso").val()+"&wservicio=*&protocolos=0&CLASE=I&enviarCorreo=on&emailEnviarCorreo="+$("#email").val()+"&envioPaciente="+envioPaciente;
			var html = "<iframe id='iframeConsultaHCE' src='"+urlIframe+"' style='width:100%;height:80%'></iframe>";
			
			$("#divConsulta").html(html);
			$("#divConsulta").show();
		}
		else
		{
			if($("#email").val()=="")
			{
				mensaje = "Debe ingresar un correo electr&oacute;nico.";
			}
			else
			{
				mensaje = "Debe ingresar un correo electr&oacute;nico v&aacute;lido";
			}
			$("#mensajeAlerta").html(mensaje);
			$("#divAlerta").modal("show");
		}
	}
	
	function consultarOrdenes()
	{
		$("#divConsulta").html("");
		$("#divConsulta").hide();
		
		emailValido = validarEmail($("#email").val());
		
		if(emailValido)
		{
			var envioPaciente = "off";
			if($('input:radio[name=radioEmail]:checked').val()=="paciente")
			{
				envioPaciente = "on";
			}
			
			var urlIframe = "../reportes/rep_PacientesEgresadosActivosOrdenes.php?wemp_pmla="+$("#wemp_pmla").val()+"&conservarVentana=on&enviarCorreo=on&emailEnviarCorreo="+encodeURI($("#email").val())+"&envioPaciente="+envioPaciente;
			var html = "<iframe id='iframeConsultaOrdenes' src='"+urlIframe+"' style='width:100%;height:80%'></iframe>";
			
			$("#divConsulta").html(html);
			$("#divConsulta").show();
			
			$('#iframeConsultaOrdenes').load(function(){
				$('#iframeConsultaOrdenes').contents().find('input#historia').val($("#historia").val());
				$('#iframeConsultaOrdenes').contents().find('input#btnConsultar').click();
				$('#iframeConsultaOrdenes').contents().find('a#ingreso'+$("#ingreso").val()).click();
			});
		}
		else
		{
			if($("#email").val()=="")
			{
				mensaje = "Debe ingresar un correo electr&oacute;nico.";
			}
			else
			{
				mensaje = "Debe ingresar un correo electr&oacute;nico v&aacute;lido";
			}
			$("#mensajeAlerta").html(mensaje);
			$("#divAlerta").modal("show");
		}
	}
	
	function modificaFiltro()
	{
		$("#divConsulta").html("");
		$("#divConsulta").hide();
	}
	
	function iniciarCampos(todos)
	{
		if(todos)
		{
			$("#historia").val("");
		}
		
		$("#divConsulta").hide();
		$("#ingreso").val("");
		$("#ingreso").html("");
		$("#documento").val("");
		$("#tipoDocumento").val("");
		$("#email").val("");
		deshabilitarCampos(true);
	}
	
	function cerrarVentana()
	{
		if($("#esIframe").val()=="on")
		{
			parent.$.unblockUI();  
		}
		else
		{
			top.close();		
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
		.labelRadio {
			font-weight: normal;
		}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<body class="nav-md">
		<?php
		
		// -->	ENCABEZADO
		
		encabezado("Envio de historia clinica y ordenes a pacientes", $wactualiz, "HCE".$wemp_pmla);
		
		?>
		
		<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='<?php echo $wemp_pmla ?>'>
		<input type='hidden' id='wbasedatoHce' name='wbasedatoHce' value='<?php echo $wbasedatoHce ?>'>
		<input type='hidden' id='wbasedatoMovhos' name='wbasedatoMovhos' value='<?php echo $wbasedatoMovhos ?>'>
		<input type='hidden' id='wbasedatoCliame' name='wbasedatoCliame' value='<?php echo $wbasedatoCliame ?>'>
		<input type='hidden' id='ingresoActual' name='ingresoActual' value='<?php echo $ingreso ?>'>
		<input type='hidden' id='esIframe' name='esIframe' value='<?php echo $esIframe; ?>'>
		
		<div class="container-fluid">
			<div id="divFiltros" class="col-lg-12 col-md-8 col-sm-8 col-xs-8 " style="text-align:center;">
				<div class="panel panel-primary">
					<div class="panel-heading">Consultar paciente</div>
					<div class="panel-body">
						<form>
							<div class="col-lg-12">
								<div class="form-group col-lg-1" class="control-label"  style="width:150px">
									<label for="labelHistoria" class="control-label">Historia</label>
									<input id='historia' class='form-control' type='text' style='height:32px' onchange="consultarPaciente()" value="<?php echo $historia; ?>">
									<input type='hidden' id='tipoDocumento' name='tipoDocumento' value=''>
									<input type='hidden' id='documento' name='documento' value=''>
								</div>
								<div class="form-group col-lg-1" class="control-label"  style="width:100px">
									<label for="labelIngreso" class="control-label">Ingreso</label>
									<select id="ingreso" class="form-control col-lg-1" style="height:32px;width:60px;" onchange="modificaFiltro()">
									</select>
								</div>
								<div class="form-group col-lg-5" class="control-label" >
									<label for="labelCorreo" class="control-label">
										Enviar correo electr&oacute;nico al &nbsp;&nbsp;&nbsp;&nbsp;
										<div class="radio-inline">
										  <label class="labelRadio">
											<input type="radio" name="radioEmail" id="radioPaciente" value="paciente" checked onchange="correoPaciente();">
											Paciente
										  </label>
										</div>
										<div class="radio-inline">
										  <label class="labelRadio">
											<input type="radio" name="radioEmail" id="radioEntidad" value="entidad" onchange="correoEntidad();">
											Entidad
											<span id='infoEntidad' class='fa fa-info-circle pull-right' data-toggle='tooltip' data-placement='right' data-html='true' style='cursor:pointer; font-size:12pt;text-align:right;color:#828181;display:none;'></span>
										  </label>
										</div>
									</label>
									<input id='email' class='form-control' type='email' style='height:32px;' disabled onchange="modificaFiltro()">
									<input type='hidden' id='emailPaciente' name='emailPaciente' value=''>
								</div>
								<div class="form-group col-lg-4" style="top:24px;text-align:left;">
									<button type="button" id="btnConsultarHCE" class="btn btnMatrix" onclick="consultarHCE();" disabled>HCE</button>
									<button type="button" id="btnConsultarOrdenes" class="btn btnMatrix" onclick="consultarOrdenes();" disabled>Ordenes</button>
									<button type="button" id="btnIniciar" class="btn btn-default" onclick="iniciarCampos(true);">Iniciar</button>
									<button type="button" id="btnCerrar" class="btn btn-default" onclick="cerrarVentana();">Cerrar</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<div id="divConsulta" class="col-md-12 col-sm-12 col-xs-12" style="display:none;">
				
			</div>
			
			<div id='divAlerta' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm'>
					<div class='modal-content'>
						<div class='modal-Alerta'>ALERTA</div>
						<div class='modal-body' id='mensajeAlerta'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' data-dismiss='modal'>Cerrar</button>
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
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
