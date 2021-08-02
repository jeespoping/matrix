<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : F1.php
	   Fecha de Liberacion : 2004-04-20
	   Autor : Pedro Ortiz Tamayo
	   Version Inicial : 2004-04-20
	   Version actual  : 2020-08-31
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite registrar los datos
	   Administrativos, Financieros y Clinicos
	   
	   REGISTRO DE MODIFICACIONES :
	   .2021-07-07 
	   		Se agrega la validacion de numero de intentos maximo por usuario.
	   .2020-08-31
			Se corrige variable para que muestre el esquema por usuarios
		.2020-08-31
			Se adiciona la opción de recuperación de usuario y/o contraseña.
	   .2016-06-03
			Se agrega imagen del proyecto Ingenia y hipervinculo al video de este proyecto.
	   .2014-06-24
			Se agrega Session_Destroy cuando el login es incorrecto.
	   .2014-05-12
			Nueva Imagen.	
	   .2004-04-20
	   		Release de Version Beta.

[*DOC]   		
***********************************************************************************************************************/
function GetIP()
{
	$IP_REAL = " ";
	$IP_PROXY = " ";
	if (@getenv("HTTP_X_FORWARDED_FOR") != "") 
	{ 
		$IP_REAL = getenv("HTTP_X_FORWARDED_FOR"); 
		$IP_PROXY = getenv("REMOTE_ADDR"); 
	} 
	else 
	{ 
		$IP_REAL = getenv("REMOTE_ADDR");
	}
	$IPS=$IP_REAL."|".$IP_PROXY;
	return $IPS;
}

function Listar($conex,$grupo,$codigo,$usera,&$w,&$DATA)
{
	$codigo = mysqli_real_escape_string( $conex, $codigo );
	$usera 	= mysqli_real_escape_string( $conex, $usera );
	
	switch($grupo)
	{
		case 'AMERICAS':
			$query = "select descripcion from root_000020 where codigo ='".$codigo."'";
		break;
	}
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	$grupoN=$row[0];
	$w++;
	$DATA[$w][0]=$codigo;
	$DATA[$w][1]=$row[0];
	$DATA[$w][2]="";
	$DATA[$w][3]="";
	$DATA[$w][4]=0;
	switch($grupo)
	{
		case 'AMERICAS':
			$query = "select codopt,descripcion,programa,ruta from root_000021 where codgru=? and usuarios like ? order by codopt ";
		break;
	}
	// $err = mysql_query($query,$conex);
	// $num = mysql_num_rows($err);
	
	/* crear una sentencia preparada */
	$stmt = mysqli_prepare($conex, $query );
	
	/* ligar parámetros para marcadores */
	$usera_pq	= '%'.$usera.'%';
	mysqli_stmt_bind_param($stmt, "ss", $codigo, $usera_pq );
	
	/* ejecutar la consulta */
	$num = mysqli_stmt_execute($stmt);
	
	$err = mysqli_stmt_get_result($stmt);
	
	//Cerrando la sentencia
	mysqli_stmt_close($stmt);
	
	if ($num > 0)
	{
		// for ($i=0;$i<$num;$i++)
		while( $row = mysql_fetch_array($err) )
		{
			// $row = mysql_fetch_array($err);
			if(substr(strtolower($row[2]),0,31) == "f1.php?accion=w&grupo=americas&")
			{
				$w++;
				$DATA[$w][0]=$row[0];
				$DATA[$w][1]=$row[1];
				$DATA[$w][2]=$row[2];
				$DATA[$w][3]=$row[3];
				$DATA[$w][4]=2;
				$codigo=substr($row[2],31);
				$codigo=substr($codigo,7,strpos($codigo,"&")-7);
				listar($conex,$grupo,$codigo,$usera,$w,$DATA);
			}
			else
			{
				$w++;
				$DATA[$w][0]=$row[0];
				$DATA[$w][1]=$row[1];
				$DATA[$w][2]=$row[2];
				$DATA[$w][3]=$row[3];
				$DATA[$w][4]=1;
			}
		}
	}
	
	$w++;
	$DATA[$w][0]=$codigo;
	$DATA[$w][1]=$row[0];
	$DATA[$w][2]="";
	$DATA[$w][3]="";
	$DATA[$w][4]=3;
}

function eliminarPasswordTemporal($conex, $codigo)
{	
	$update = " UPDATE usuarios 
				   SET PasswordTemporal='',
					   FechaPasswordTemp='0000-00-00',
					   HoraPasswordTemp='00:00:00'
				 WHERE Codigo=?;";
	
	$stUpdate = mysqli_prepare( $conex, $update );

	mysqli_stmt_bind_param( $stUpdate, "s", $codigo );

	/* Ejecutar la sentencia */
	mysqli_stmt_execute( $stUpdate ) or die ("Error: " . mysqli_errno($conex) . " - en el query: " . $update . " - " . mysqli_error($conex));
	
	mysqli_stmt_close( $stUpdate );
}

function validarTiempoRestablecer($conex, $codigo, $fechaPasswordTemp, $horaPasswordTemp)
{
	$restablecerValido = false;
	if($fechaPasswordTemp!="0000-00-00" && $horaPasswordTemp!="00:00:00")
	{
		$query = "SELECT Detval
					FROM root_000051 
				   WHERE Detemp='01'
					 AND Detapl='tiempoRestablecerPassword';";
		
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query: " . $query . " - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			
			$fechaHoraPasswordTemporal = strtotime($fechaPasswordTemp." ".$horaPasswordTemp);
			$fechaHoraMaxPasswordTemporal = ((int)trim($row['Detval'])*60)+$fechaHoraPasswordTemporal;
			$fechaHoraActual = strtotime(date("Y-m-d H:i:s"));
			
			$restablecerValido = true;
			if($fechaHoraActual>$fechaHoraMaxPasswordTemporal)
			{
				$restablecerValido = false;
				eliminarPasswordTemporal($conex, $codigo);
			}
		}
	}
	
	return $restablecerValido;
}

function procesarIntentos($codigo,$conex, $intentos, $fechaLimIntentos, $horaLimIntentos, $minutos, $limiteIntentos)
{
    if (strtotime(date('Y-m-d H:i:s')) >= strtotime($fechaLimIntentos." ".$horaLimIntentos)){
        inicializarIntentos($codigo, $minutos, $conex);
        $intentos = 0;
    }
    $intentos++;
    setNroIntentos($codigo, $intentos, $conex);
    if ($intentos == ($limiteIntentos - 1)) mensajeAlerta($limiteIntentos - 1); 

    if ($intentos >= $limiteIntentos){
        return false;
    }
    return true;
}

function inicializarIntentos($codigo,$minutos, $conex){
    $fechaCompleta = calcularFechaParam($minutos);
    $fechaLim = date('Y-m-d',strtotime($fechaCompleta));
    $horaLim = date('H:i:s',strtotime($fechaCompleta));
    $update = " UPDATE usuarios 
                    SET Intentos = 0, FechaLimIntentos = '{$fechaLim}', HoraLimIntentos = '{$horaLim}'
                    WHERE Codigo=?;";
    $stUpdate = mysqli_prepare( $conex, $update );

    mysqli_stmt_bind_param( $stUpdate, "s", $codigo );

    /* Ejecutar la sentencia */
    $num = mysqli_stmt_execute( $stUpdate );
    
    mysqli_stmt_close( $stUpdate );
   
    // return $num;
}

function calcularFechaParam($minutos){
    $fechaNueva = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")."+ $minutos minutes"));
    return $fechaNueva;
}

function setNroIntentos($codigo, $intentos, $conex){
    
    $update = " UPDATE usuarios 
                    SET Intentos = {$intentos}
                    WHERE Codigo=?;";
    
    $stUpdate = mysqli_prepare( $conex, $update );

    mysqli_stmt_bind_param( $stUpdate, "s", $codigo );

    /* Ejecutar la sentencia */
    $num = mysqli_stmt_execute( $stUpdate );
    
    mysqli_stmt_close( $stUpdate );
   
    // return $num;
}

function mensajeAlerta($intentos){ //TODO
	$intentosFin = $intentos + 1;
	echo "<script>";
	echo "alert('Llevas " . $intentos . " intentos fallidos para el ingreso de tu clave, ten en cuenta que al intento n\xfamero ".$intentosFin." tu cuenta ser\xe1 bloqueada');";
	echo "</script>";
}

function resetIntentos($codigo,$conex){
    $fechaLim = date("Y-m-d",strtotime(date("Y-m-d")."- 1 days"));
    $horaLim = date('H:i:s');
    $update = " UPDATE usuarios 
                    SET Intentos = 0, FechaLimIntentos = '{$fechaLim}', HoraLimIntentos = '{$horaLim}'
                    WHERE Codigo=?;";

    $stUpdate = mysqli_prepare( $conex, $update );

    mysqli_stmt_bind_param( $stUpdate, "s", $codigo );

    /* Ejecutar la sentencia */
    $num = mysqli_stmt_execute( $stUpdate );
    
    mysqli_stmt_close( $stUpdate );
   
    // return $num;
}

$includeLibrerias = "	<script src='../../../include/root/jquery.min.js'></script>
						<script src='../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js' type='text/javascript'></script>
						<link type='text/css' href='../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css' rel='stylesheet'/>
						<link type='text/css' href='../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css' rel='stylesheet'/>
						<link type='text/css' href='../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css' rel='stylesheet'/>
						
						<link rel='stylesheet' href='../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css' >
						<link rel='stylesheet' href='../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css'>

						<script   src='../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js' ></script>
						
						<!-- Bootstrap -->
						<link href='../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css' rel='stylesheet'>
						
						<link rel='stylesheet' href='../../../include/root/bootstrap.min.css'>

						<script src='../../../include/root/bootstrap.min.js'></script>
						
						
						<!-- Bootstrap -->
						<script src='../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js'></script>
						
						<link rel='stylesheet' href='../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css'>
						<link href='../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css' rel='stylesheet'>
						";

if(isset($_SESSION["NS"]) and $_SESSION["NS"] == 0)
{
	$_SESSION["NS"] = 1;
	$accion = 0;
}
if(!isset($accion))
{
	echo "<html>";
	echo "<head>";
	echo $includeLibrerias;
	echo "<title>MATRIX</title>";
	echo "<style type='text/css'>";
	echo "	#tipo1{color:#000000;font-size:14pt;font-family:Arial;font-weight:bold;width:120em;text-align:left;height:6em;}";
	echo "	.tipo1a{height:1.5em;}";
	echo "	#tipo2{width:65%}";
	echo "	#tipo3{color:#000000;font-size:12pt;font-family:Arial;font-weight:bold;width:22em;text-align:center;height:20em;}";
	echo "  .tipoHIDE{background:#EAEAEA;border-style:none;}";
	echo "	#tipo4{color:#000066;font-size:12pt;font-family:Arial;font-weight:bold;}";
	echo "	#tipo5{color:#000066;font-size:12pt;font-family:Arial;font-weight:bold;}";
	echo "	#tipo6{height:2em;}";
	echo "	#tipo7{width:10%;}";
	echo "	#tipo8{color:#000000;font-size:7pt;font-family:Arial;font-weight:bold;width:100%;text-align:left;height:8em;}";
	echo "	#tipoL02{color:#000066;background:#E8EEF7;font-size:8.5pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}";
	echo "	#tipoL03{color:#FF0000;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:bold;width:90em;text-align:center;height:2em;}";
	echo "	.input-login{font-size:10pt;font-family:Arial;font-weight:normal;}";
	echo "	.tipoScreen02{color:#EAEAEA;background:#EAEAEA;font-size:12pt;font-family:Ubuntu;font-weight:bold;text-align:center;width:350px;vertical-align:middle;border-style:solid;border-collapse:collapse;border-color:#EAEAEA;border-radius: 4px 4px 4px 4px;border-width:1px;padding:2px;behavior: url(PIE.htc);}";
	echo "	A {text-decoration: none;color: #000066;;font-size:12pt;font-family:Arial;font-weight:bold;}";
	echo "</style>";
	echo "<link rel='shortcut icon' href='favicon.png' type='image/png' />";
	echo "<script type='text/javascript'>";
	echo "	function enter()";
	echo "	{";
	echo "		document.forms.entrada.submit();";
	echo "	}";
	echo "	function ira()";
	echo "	{ ";
	echo "		document.entrada.codigo.focus();";
	echo "	}";
	echo "</script>";
	?>
		<script type='text/javascript'>
		function abrirRestablecerPassword()
		{
			const htmlModal =   "<div class='col-lg-12'>"+
								"	<div class='form-group col-lg-3' class='control-label' >"+
								"		<select id='tipoUsuario' class='form-control col-lg-1 inputModal' style='height:32px;' onchange='cambiarOpciones();'>"+
								"			<option value='codigo'>Usuario Matrix</option>"+
								"			<option value='documento'>N&uacute;mero de documento</option>"+
								"		</select>"+
								"	</div>"+
								"	<div class='col-lg-2'>"+
								"		<input id='datoIngreso' class='form-control inputModal' type='text' style='height:32px'  value='' onchange='obtenerEmailUsuario();'>"+
								"		<input type='hidden' id='codigoUsuario' name='codigoUsuario' value=''>"+
								"		<input type='hidden' id='documentoUsuario' name='documentoUsuario' value=''>"+
								"		<input type='hidden' id='nombreUsuario' name='codigoUsuario' value=''>"+
								"	</div>"+
								"	<div class='form-group col-lg-4'>"+
								"		<input id='email' class='form-control inputModal' type='email' style='height:32px;' disabled>"+
								"	</div>"+
								"	<div class='form-group col-lg-3 control-label checkbox' id='divCheckOpciones'>"+
								"		<label id='labelRecordarUsuario' style='font-weight:normal;display:none;'>"+
								"			<input type='checkbox' class='inputModal checkboxOpciones' id='chk_recordarUsuario'> Recordar usuario"+
								"		</label>"+
								"		<label id='labelRestablecerPassword' style='font-weight:normal;'>"+
								"			<input type='checkbox' class='inputModal checkboxOpciones' id='chk_restablecerPassword'> Restablecer contrase&ntilde;a"+
								"		</label>"+
								"	</div>"+
								"	<div class='form-group col-lg-12' class='control-label' >"+
								"		<p id='mensajeRestablecer' style='display:none;' align='center'></p>"+
								"	</div>"+
								"</div>";
								
			$("#bodyModal").html(htmlModal);
			$("#divModalRestablecer").modal("show");
			$("#btnAceptar").show();
			
			// evitar que al hacer enter en un input de la modal haga submit al form de inicio de sesion
			$(".inputModal").keydown(function(event){
				if( event.keyCode == 13) {
				  event.preventDefault();
				  return false;
				}
			 });
		}
		
		function iniciarCampos()
		{
			$("#mensajeRestablecer").html("");
			$("#mensajeRestablecer").hide();
			$("#btnAceptar").prop('disabled',true);
			
			$("#chk_recordarUsuario").prop('checked', false);
			$("#chk_restablecerPassword").prop('checked', false);
		}
		
		function cambiarOpciones()
		{
			$("#datoIngreso").val("");
			$("#email").val("");
			
			iniciarCampos();
			
			if($("#tipoUsuario").val()=="documento")
			{
				$("#labelRecordarUsuario").show();
				$("#divCheckOpciones").css('top','-14px');
			}
			else
			{
				$("#labelRecordarUsuario").hide();
				$("#divCheckOpciones").css('top','0px');
			}
		}
		
		function obtenerEmailUsuario()
		{
			iniciarCampos();
			
			$.ajax({
				url: "root/procesos/registroUsuario.php",
				type: "POST",
				dataType: "json",
				data:{
					consultaAjax 	: '',
					accion			: 'consultarDatosUsuario',
					tipoIngreso		: $("#tipoUsuario").val(),
					datoIngreso		: $("#datoIngreso").val(),
					proceso			: 'restablecer'
					},
					async: false,
					success:function(result) {
						
						$("#codigoUsuario").val(result.codigo);
						$("#documentoUsuario").val(result.documento);
						$("#nombreUsuario").val(result.nombre);
						$("#email").val(result.email);
						
						if(result.email!="")
						{
							$(".checkboxOpciones").change(validarOpciones);
							$("#btnAceptar").prop('disabled',false);
							$("#mensajeRestablecer").html("<span style='color:green;'>Se enviar&aacute; un mensaje con los datos de ingreso al correo electr&oacute;nico registrado.</span>");
							$("#mensajeRestablecer").show();
							
							if($("#tipoUsuario").val()=="documento")
							{
								$("#chk_recordarUsuario").prop('checked', true);
							}
							else
							{
								$("#chk_recordarUsuario").prop('checked', false);
								$("#chk_restablecerPassword").prop('checked', true);
							}
						}
						else
						{
							$("#btnAceptar").prop('disabled',true);
							$("#mensajeRestablecer").html("<span style='color:red;'>Debe tener un correo electr&oacute;nico registrado para poder realizar el proceso.</span>");
							$("#mensajeRestablecer").show();
						}
					}
			});
		}
		
		function validarOpciones()
		{
			if($("#chk_restablecerPassword").prop('checked')==false && $("#chk_recordarUsuario").prop('checked')==false)
			{
				$("#btnAceptar").prop('disabled',true);
			}
			else
			{
				$("#btnAceptar").prop('disabled',false);
			}
		}
		
		function restablecerDatosIngreso()
		{
			$("#btnAceptar").prop('disabled',false);
			$.ajax({
				url: "root/procesos/registroUsuario.php",
				type: "POST",
				dataType: "json",
				data:{
					consultaAjax 		: '',
					accion				: 'restablecerDatosIngreso',
					codigo				: $("#codigoUsuario").val(),
					documento			: $("#documentoUsuario").val(),
					email				: $("#email").val(),
					nombre				: $("#nombreUsuario").val(),
					restablecerPassword	: $("#chk_restablecerPassword").prop('checked'),
					recordarUsuario		: $("#chk_recordarUsuario").prop('checked'),
					wemp_pmla			: '01',
					proceso				: 'restablecer'
					},
					async: false,
					success:function(mensaje) {
						let htmlModal = "<p align='center'>"+mensaje+"</p>";
						$("#bodyModal").html(htmlModal);
						$("#btnAceptar").hide();
					}
			});
		}
		</script>
		<style type='text/css'>
			#restablecerPassword{
				// display:none;
				text-align:right;
				font-size:8pt;
				color:#757594;
				font-family:arial;
				cursor:pointer;
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
		</style>
	<?php
	echo "</head>";
	echo "<body onload=ira() BGCOLOR='ffffff'>";
	echo "<BODY TEXT='#000066'>";
	echo "<form name='entrada' action='F1.php?accion=I' method=post>";
	echo "<center><table border=0>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/GELA.png'></td></tr>";
	echo "</table></center>";
	echo "	<div id='divModalRestablecer' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-lg'>
					<div class='modal-content'>
						<div class='modal-Alerta'>RESTABLECER USUARIO O CONTRASE&Ntilde;A</div>
						<div class='modal-body' id='bodyModal'>
							
						</div>
						<br/><br/><br/><br/>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' id='btnAceptar' onclick='restablecerDatosIngreso();' disabled>Aceptar</button>
							<button type='button' class='btn btnMatrix' id='btnCerrarModal' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>";
	echo "<br><table border=0 align=center>";
	echo "<tr>";
	echo "	<td id=tipo3 valign=middle>";
	echo "      <IMG SRC='/matrix/images/medical/root/Logo_Matrix.png'><br><br>";


	//echo "	<div id='tipo4' class='tipoScreen02' style='text-align:center;font-size:20pt;'>SERVIDOR DE MIGRACION<br>".DATE( "Y-m-d H:i:s" )."</div>";
	echo "	<div class='tipoScreen02'>";
	echo "		<table border=0 style='width : 100%'>";	
	echo "			<tr><td align=center id=tipo5 colspan=2><IMG SRC='/matrix/images/medical/root/boton-9.png'></td></tr>";
	echo "          <tr><td align=center class='tipo1a' colspan=2></td></tr>";
	echo "			<tr><td align=center id=tipo4>C&oacute;digo</td><td align=center><input class='input-login' type='text' name='codigo' size=18 maxlength=8></td></tr>";
	echo "			<tr><td align=center id=tipo4>Clave</td><td align=center><input class='input-login'  type='password' name='password' size=18 maxlength=30></td></tr>";
	echo "          <tr><td align=center class='tipo1a' colspan=2></td></tr>";
	echo "			<tr><td align=center colspan=2><button onClick='enter()' class='tipoHIDE'><IMG SRC='/matrix/images/medical/root/boton-10.png'></button></td></tr>";
	echo "          <tr><td align=center class='tipo1a' id='restablecerPassword' colspan=2 >
							<span onclick='abrirRestablecerPassword();'>&iquest;olvid&oacute; su usuario o contrase&ntilde;a?</span>
							<a class='fa fa-question-circle' href='/matrix/root/manuales/registroUsuario.pdf' onclick='window.open(this.href);return false' style='cursor : pointer' title='Manual de Usuario'></a>
					</td></tr>";
	echo "		</table>";
	echo "	</div>";
	echo "</td>";
	echo "<td id=tipo2 align=center valign=middle>";
	echo "		<table border=0>";
	echo "		<div style='width: 500px; height: 250px; overflow-y: scroll;'>";
	

	
 	
	$query = "SELECT Descripcion, Icono, Url from root_000058 where Activo = 'on' order by Codigo ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			echo "<A HREF='".$row[2]."' target='_blank'><IMG SRC='/matrix/images/medical/root/".$row[1]."'></A><br>";
		}
	}
	echo "		</div>";
	echo "		</table>";
	echo "	</td>";
	echo "</tr>";
	echo "</table>";
	echo "<br>";
	echo "<center><table border=0>";
	echo "<tr><td id=tipoL02><IMG SRC='/matrix/images/medical/root/boton-12.png' style='vertical-align:middle;'></td></tr>";
	echo "</table></center>";
	if(isset($_SESSION["NS"]))
		$_SESSION["NS"] += 1;
	if(isset($END) and $END == "on")
		@session_destroy();
}
else
{
	echo "<html>";
	echo "<head>";
	echo "<title>MATRIX</title>";
	echo "</head>";
	echo "<link rel='shortcut icon' href='favicon.png' type='image/png' />";
	if($accion == "0")
	{
		$_SESSION["NS"] = 1;
		echo "<body bgcolor=#FFFFFF>";
		echo "<BODY TEXT='#000066'>";
		echo "<center>";
		echo "</center>";
		echo "<table  border=0 align=center>";
		echo "<tr><td id=tipo1 colspan=2 align=center><IMG SRC='/matrix/images/medical/root/GELA.png' BORDER=0></td></tr>";
		echo "<tr><td id=tipo1><IMG SRC='/matrix/images/medical/root/denegado.png' BORDER=0></td>";
		echo "<td id=tipo1><A HREF='F1.php?accion=0' target='_top'>YA EXISTE UNA SESION ABIERTA EN ESTE NAVEGADOR. CIERRE LA VENTANA</A></td></tr></table></body>";
	}
	elseif($accion == "I")
	{
		if(isset($_SESSION["IIPP"]) and isset($_SESSION["NS"]) and $_SESSION["NS"] > 1)
		{
			$_SESSION["NS"] = 0;
			echo "<body bgcolor=#FFFFFF>";
			echo "<BODY TEXT='#000066'>";
			echo "<center>";
			echo "</center>";
			echo "<table  border=0 align=center>";
			echo "<tr><td id=tipo1 colspan=2 align=center><IMG SRC='/matrix/images/medical/root/GELA.png' BORDER=0></td></tr>";
			echo "<tr><td id=tipo1><IMG SRC='/matrix/images/medical/root/denegado.png' BORDER=0></td>";
			echo "<td id=tipo1><A HREF='F1.php?accion=0' target='_top'>YA EXISTE UNA SESION ABIERTA EN ESTE NAVEGADOR. HAGA CLICK AQUI!!</A></td></tr></table></body>";
		}
		else
		{
			$password = sha1( $password );
			
			@session_start();
			if (!isset($user))
			{
				if(!isset($_SESSION['user']))
				{
					$_SESSION['user']  = "1-".strtolower($codigo);			
                    $user              ="1-".strtolower($codigo); 
					$_SESSION['usera'] = strtolower($codigo);
					$ipdir             = explode("|",GetIP());
					$_SESSION['IIPP']  = $ipdir[0];
					$_SESSION['NS']    = 1;
					$_SESSION['codigo'] = strtolower($codigo);
					// $_SESSION['password'] = strtolower($password);
					$_SESSION['password'] = $password;
				}
			}
			
			mysql_select_db("matrix") or die ("ERROR AL CONECTARSE A MATRIX");
			
			$query = "SELECT codigo,prioridad,grupo,password,activo,Documento,Email,PasswordTemporal,FechaPasswordTemp,HoraPasswordTemp,Intentos,FechaLimIntentos,HoraLimIntentos 
						FROM usuarios 
					   WHERE codigo=?";
			
			/* crear una sentencia preparada */
			$stmt = mysqli_prepare($conex, $query );
			
			/* ligar parámetros para marcadores */
			mysqli_stmt_bind_param($stmt, "s", $codigo);
			
			/* ejecutar la consulta */
			$num = mysqli_stmt_execute($stmt);
			
			$login = false;
			//Se modifica mensaje de respuesta Mavila 29-10-2020 :)
			//$mensajeLogin = "EL USUARIO NO EXISTE";
			$mensajeLogin = "EL USUARIO O CONTRASE&NtildeA SON INCORRECTOS";
			// if($num > 0)
			if( $num )
			{				
				/* ligar variables de resultado */
				mysqli_stmt_bind_result( $stmt, $codigo, $prioridad, $grupo, $pwd, $activo, $documento, $email, $passwordTemporal, $fechaPasswordTemp, $horaPasswordTemp, $intentos, $fechaLimIntentos, $horaLimIntentos );
				
				/* obtener valor */
				mysqli_stmt_fetch($stmt);
				
				/* cerrar sentencia */
				mysqli_stmt_close($stmt);

				$minutos =  consultarAliasPorAplicacion($conex, '*', 'tiempoBloqueoIntentos');
				$limiteIntentos =  consultarAliasPorAplicacion($conex, '*', 'numeroIntentosPassword');
				$msgIntentos = false;
				
				if($activo=="A")
				{
					if($pwd==$password)
					{
						$login = true;
						$mensajeLogin = "";
						if (strtotime(date('Y-m-d H:i:s')) < strtotime($fechaLimIntentos." ".$horaLimIntentos) && $intentos >= $limiteIntentos )
							$msgIntentos = true;
						else if ($intentos > 0)
							resetIntentos($codigo,$conex);
					}
					else
					{
						if($passwordTemporal=="")
						{
							$login = false;
							//Se modifica mensaje de respuesta Mavila 29-10-2020 :)
							//$mensajeLogin = "CONTRASE&Ntilde;A INCORRECTA";	
							if (!procesarIntentos($codigo,$conex, $intentos, $fechaLimIntentos, $horaLimIntentos,$minutos,$limiteIntentos)) $msgIntentos = true;
							
							$mensajeLogin = "EL USUARIO O CONTRASE&NtildeA SON INCORRECTOS";	
						}
						else
						{
							$restablecerValido = validarTiempoRestablecer($conex, $codigo, $fechaPasswordTemp, $horaPasswordTemp );
							if($restablecerValido)
							{
								if( $passwordTemporal ==$password)
								{	
									$update = " UPDATE usuarios 
												   SET Feccap='".date("Y-m-d",strtotime(date("Y-m-d")."- 1 days"))."'
											     WHERE Codigo=?;";
									
									$stUpdate = mysqli_prepare( $conex, $update );

									mysqli_stmt_bind_param( $stUpdate, "s", $codigo );

									/* Ejecutar la sentencia */
									mysqli_stmt_execute( $stUpdate );
									
									mysqli_stmt_close( $stUpdate );
									
									$login = true;
									$mensajeLogin = "";
									if ($intentos > 0)resetIntentos($codigo,$conex);
								}
								else
								{
									$login = false;
									$mensajeLogin = "CONTRASE&Ntilde;A DE RECUPERACI&Oacute;N INCORRECTA";
								}
							}
							else
							{
								$login = false;
								$mensajeLogin = "LA CONTRASE&Ntilde;A DE RECUPERACI&Oacute;N EXPIR&Oacute;";
							}
						}
					}
				}
				else
				{
					$login = false;
					//Se modifica mensaje de respuesta Mavila 30-10-2020 :)
					//$mensajeLogin = "EL USUARIO ESTA INACTIVO";	
					if (!procesarIntentos($codigo,$conex, $intentos, $fechaLimIntentos, $horaLimIntentos,$minutos,$limiteIntentos)) $msgIntentos = true;		
					
					$mensajeLogin = "EL USUARIO O CONTRASE&NtildeA SON INCORRECTOS";					
				}

				if ($msgIntentos){
					// $fechaLimCompleta = $fechaLimIntentos . " " . $horaLimIntentos;
					$fechaLimCompleta = $horaLimIntentos;
					// $mensajeLoginIntentos = 'NUMERO DE INTENTOS EXCEDIDO, DEBE ESPERAR HASTA: ' . $fechaLimCompleta . ' PARA VOLVER A INGRESAR';
					$mensajeLoginIntentos = 'N&Uacute;MERO DE INTENTOS EXCEDIDO, PARA VOLVER A INGRESAR DEBE HACER LA GESTI&Oacute;N DE RESTABLECER CONTRASE&Ntilde;A';
					echo "<body bgcolor=#FFFFFF class='fondo'>";
					echo "<BODY TEXT='#000066'>";
					echo "<table  border=0 align=center>";
					echo "<tr><td id=tipo1 colspan=2 align=center><IMG SRC='/matrix/images/medical/root/GELA.png' BORDER=0></td></tr>";
					echo "<tr><td id=tipo1><IMG SRC='/matrix/images/medical/root/denegado.png' BORDER=0></td>";
					@session_destroy();
					echo "<td id=tipo1><A HREF='F1.php?END=on'>".$mensajeLoginIntentos."</a></td></tr></table></body>";
					return;
				}
				
				// mysql_free_result($err);
				// mysqli_stmt_close($stmt);
			}
			
			mysql_close($conex);
			
			if ($login)
			{
				if($documento=="" || $email=="")
				{
					echo "	<script>
								window.open('root/procesos/registroUsuario.php','_self') 
							</script>";
				}
				
				switch($grupo)
				{
					case 'pda':
						echo "<center><table border=0>";
						echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
						echo "<tr><td align=center>INGRESO DE MEDICAMENTOS AL SISTEMA</td></tr>";
						if(isset($user))
						{
							unset($user);
						}
						echo "<tr><td align=center><A HREF='pda/procesos/pda_ingreso.php'>Haga Click Para Ingresar Al Sistema</A></td></tr></table>";
					break;
					
					case 'pdadevol':
						echo "<center><table border=0>";
						echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
						echo "<tr><td align=center>DEVOLUCIONES DE MEDICAMENTOS AL SISTEMA</td></tr>";
						if(isset($user))
						{
							unset($user);
						}
						echo "<tr><td align=center><A HREF='pda/procesos/devolucion.php'>Haga Click Para Ingresar Al Sistema</A></td></tr></table>";
					break;
									
					case 'fidelidad':
						echo "<center><table border=0>";
						echo "<tr><td align=center><b>CLINICA LAS AMERICAS <b></td></tr>";
						echo "<tr><td align=center>INFORMACI&Oacute;N CLIENTES</td></tr>";
						echo "<tr><td align=center><A HREF='Magenta/procesos/Magenta.php?user=".$user."'>Haga Click Para Ingresar Al Sistema</A></td></tr></table>";
					break;
					
					case 'ayudaenl':
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						

						mysql_select_db("MATRIX");
							$query = "insert root_000004 (medico,fecha_data,hora_data,usuario,seguridad) values ('root','".$fecha."','".$hora."','".$codigo."','C-root')";
						$err1 = mysql_query($query,$conex);
						mysql_close($conex);
						echo "<BODY TEXT='#000066'>";
						echo "<font size=9>";
						echo "<center><table border=0>";
						echo "<tr><td align=center><h1>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
						echo "<tr><td align=center><h2>SISTEMA DE AYUDA EN LINEA PARA FACTURACION</td></tr>";
						echo "<tr><td align=center><h2>DIRECCION DE MERCADEO</td></tr>";
						echo "<tr><td align=center><h2>ADVERTENCIA</td></tr>";
						echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/prohibido.gif'></td></tr>";
						echo "<tr><td>TODA  ACTIVIDAD QUE &nbsp&nbspUSTED REALICE &nbsp&nbspEN ESTA  PAGINA  SERA  REGISTRADA EN EL &nbsp&nbspSISTEMA</td></tr>";
						echo "<tr><td>QUEDA TOTALMENTE PROHIBIDO LA  IMPRESION,  COPIA O  REPRODUCCION PARCIAL O TOTAL</td></tr>";
						echo "<tr><td>DE LA INFORMACION  QUE  AQUI SE  DESPLIEGA SIN LA AUTORIZACION  DE LA GERENCIA DE LA</td></tr>";
						echo "<tr><td>CLINICA. </td></tr>";
						echo "<tr><td>CUALQUIER TRANSGRECION DE ESTA NORMA SERA SANCIONADA &nbsp&nbspSEGUN LO CONTEMPLADO </td></tr>";
						echo "<tr><td>EN EL REGLAMENTO INTERNO DE TRABAJO DE LA INSTITUCION.</td></tr>";
						echo "<tr><td align=center><A HREF='/ayuda/indexAY.html'><b>HAGA CLICK PARA COMENZAR AYUDA EN LINEA</b></A></td></tr>";
						echo "<tr><td align=center><A HREF='F1.php?END=on' target='_top'>Haga Click Para Salir del Programa</A></td></tr></table>";
					break;
						
					default:
						echo "<frameset cols=20%,80% frameborder=0 framespacing=2>";
						echo "  <frame src='F1.php?accion=O&grupo=".$grupo."&amp;prioridad=".$prioridad."' name='options' marginwidth=0 marginheiht=0>";
						echo "  <frameset rows='81,*' frameborder=0 framespacing=0>";
						echo "    <frame src='F1.php?accion=T&grupo=".$grupo."' name='titulos' marginwidth=0 marginheiht=0>";
						echo "    <frame src='F1.php?accion=M&grupo=".$grupo."' name='main' marginwidth=0 marginheiht=0>";
						echo "  </frameset>";
						echo "</frameset>";

					break;	
				}
			}
			else
			{
				echo "<body bgcolor=#FFFFFF class='fondo'>";
				echo "<BODY TEXT='#000066'>";
				echo "<table  border=0 align=center>";
				echo "<tr><td id=tipo1 colspan=2 align=center><IMG SRC='/matrix/images/medical/root/GELA.png' BORDER=0></td></tr>";
				echo "<tr><td id=tipo1><IMG SRC='/matrix/images/medical/root/denegado.png' BORDER=0></td>";
				@session_destroy();
				// echo "<td id=tipo1><A HREF='f1.php?END=on'>USUARIO NO EXISTE O ESTA INACTIVO</a></td></tr></table></body>";
				echo "<td id=tipo1><A HREF='f1.php?END=on'>".$mensajeLogin."</a></td></tr></table></body>";
			}
		}	
	}
	else
	{
		if(!isset($_SESSION['user']))
		{
			echo "<html>";
			echo "<head>";
			echo "<title>MATRIX</title>";
			echo "</head>";
			echo "<BODY TEXT='#000000' FACE='ARIAL'>";
			echo "<link rel='shortcut icon' href='favicon.png' type='image/png' />";
			echo "<A HREF='F1.php?END=on' target='_top'><IMG SRC='/matrix/images/medical/root/expire.png'>Su Sesion Ha Expirado. Por Favor Haga Click Aqui !!</A>";
			echo "</BODY>";
			echo "</html>";
		}
		elseif($accion != "W")
		{
			@session_start();
			echo "<html>";
			echo "<head>";
			echo "<title>MATRIX</title>";
			echo "<style type='text/css'>";
			echo ".tipoScreen02{color:#cccccc;background:#cccccc;font-size:12pt;font-family:Ubuntu;font-weight:bold;text-align:center;width:160px;vertical-align:middle;border-style:solid;border-collapse:collapse;border-color:#999999;border-radius: 4px 4px 4px 4px;border-width:1px;padding:2px;behavior: url(PIE.htc);}";
			echo ".tipoT{color:#000066;background:#cccccc;font-size:10pt;font-family:Ubuntu;font-weight:bold;text-align:center;}";
			echo ".tipoT1{color:#000066;background:#cccccc;font-size:8pt;font-family:Ubuntu;font-weight:bold;text-align:center;}";
			echo ".BlueThing{color:#000066;background: #CCCCFF;font-weight:normal;}";
			echo ".SilverThing{color:#000066;background: #EAEAEA;font-weight:normal;}";
			echo ".GrayThing{color:#000066;background: #EAEAEA;font-weight:normal;}";
			
			echo ".myButton {";
			echo "	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #04bedb), color-stop(1, #330ead));";
			echo "	background:-moz-linear-gradient(top, #04bedb 5%, #330ead 100%);";
			echo "	background:-webkit-linear-gradient(top, #04bedb 5%, #330ead 100%);";
			echo "	background:-o-linear-gradient(top, #04bedb 5%, #330ead 100%);";
			echo "	background:-ms-linear-gradient(top, #04bedb 5%, #330ead 100%);";
			echo "	background:linear-gradient(to bottom, #04bedb 5%, #330ead 100%);";
			echo "	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#04bedb', endColorstr='#330ead',GradientType=0);";
			echo "	background-color:#04bedb;";
			echo "	-moz-border-radius:19px;";
			echo "	-webkit-border-radius:19px;";
			echo "	border-radius:19px;";
			echo "	border:1px solid #1c2299;";
			echo "	display:inline-block;";
			echo "	cursor:pointer;";
			echo "	color:#ffffff;";
			echo "	font-family:Verdana;";
			echo "	font-size:11px;";
			echo "	padding:7px 13px;";
			echo "	text-decoration:none;";
			echo "	text-shadow:0px 1px 0px #2f6627;";
			echo "}";
			echo ".myButton:hover {";
			echo "	background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #330ead), color-stop(1, #04bedb));";
			echo "	background:-moz-linear-gradient(top, #330ead 5%, #04bedb 100%);";
			echo "	background:-webkit-linear-gradient(top, #330ead 5%, #04bedb 100%);";
			echo "	background:-o-linear-gradient(top, #330ead 5%, #04bedb 100%);";
			echo "	background:-ms-linear-gradient(top, #330ead 5%, #04bedb 100%);";
			echo "	background:linear-gradient(to bottom, #330ead 5%, #04bedb 100%);";
			echo "	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#330ead', endColorstr='#04bedb',GradientType=0);";
			echo "	background-color:#330ead;";
			echo "}";
			echo ".myButton:active {";
			echo "	position:relative;";
			echo "	top:1px;";
			echo "}";
			echo ".myButton1 {";
			echo "background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #d93604), color-stop(1, #ab0f24));";
			echo "background:-moz-linear-gradient(top, #d93604 5%, #ab0f24 100%);";
			echo "background:-webkit-linear-gradient(top, #d93604 5%, #ab0f24 100%);";
			echo "background:-o-linear-gradient(top, #d93604 5%, #ab0f24 100%);";
			echo "background:-ms-linear-gradient(top, #d93604 5%, #ab0f24 100%);";
			echo "background:linear-gradient(to bottom, #d93604 5%, #ab0f24 100%);";
			echo "filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#d93604', endColorstr='#ab0f24',GradientType=0);";
			echo "background-color:#d93604;";
			echo "-moz-border-radius:19px;";
			echo "-webkit-border-radius:19px;";
			echo "border-radius:19px;";
			echo "border:1px solid #991d23;";
			echo "display:inline-block;";
			echo "cursor:pointer;";
			echo "color:#ffffff;";
			echo "font-family:Verdana;";
			echo "font-size:11px;";
			echo "font-weight:bold;";
			echo "padding:7px 9px;";
			echo "text-decoration:none;";
			echo "text-shadow:0px 1px 0px #2f6627;";
			echo "}";
			echo ".myButton1:hover {";
			echo "background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ab0f24), color-stop(1, #d93604));";
			echo "background:-moz-linear-gradient(top, #ab0f24 5%, #d93604 100%);";
			echo "background:-webkit-linear-gradient(top, #ab0f24 5%, #d93604 100%);";
			echo "background:-o-linear-gradient(top, #ab0f24 5%, #d93604 100%);";
			echo "background:-ms-linear-gradient(top, #ab0f24 5%, #d93604 100%);";
			echo "background:linear-gradient(to bottom, #ab0f24 5%, #d93604 100%);";
			echo "filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ab0f24', endColorstr='#d93604',GradientType=0);";
			echo "background-color:#ab0f24;";
			echo "}";
			echo ".myButton1:active {";
			echo "	position:relative;";
			echo "	top:1px;";
			echo "}";

			echo "	A {text-decoration: none;color: #000066;;font-size:9pt;font-family:Arial;font-weight:bold;}";
			echo "</style>";
			echo "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
			echo "</head>";
			

			
 	
			$key = substr($user,2,strlen($user));
			switch($accion)
			{
				case "T":
					echo"<table border=0 align=center width=100%;>";
					echo"<tr><td align=center><IMG SRC='/matrix/images/medical/root/GELA1.png'  height='70%'></td><td align=right><A HREF='F1.php?END=on' target='_top' class='myButton1'>X</A></td></tr>";
					echo"<tr></table>";
				break;
				
				case "O": 	
					echo "<body BGCOLOR='#EAEAEA'>";
					echo "<font size=2 face='tahoma'>";
					echo "<BODY TEXT='#000066'>";
					echo "<link rel='shortcut icon' href='favicon.png' type='image/png' />";
					if(substr($user,2,strlen($user)) != $usera)
						$user="1-".$usera;
					$key = substr($user,2,strlen($user));
					

					

					// $query = "select Codigo, Password, Passdel, Feccap, Tablas, Descripcion, Prioridad, Grupo, Empresa, Ccostos, Activo  from usuarios where codigo = '".substr($user,2,strlen($user))."'";
					// $err = mysql_query($query,$conex);
					// $num = mysql_num_rows($err);
					// $row = mysql_fetch_array($err);
					
					$query = "SELECT Feccap, Prioridad  
							    FROM usuarios 
							   WHERE codigo = ? ";
							   
					$st = mysqli_prepare( $conex, $query );
					
					$user_pq = substr($user,2,strlen($user));
					
					/* ligar parámetros para marcadores */
					mysqli_stmt_bind_param($st, "s", $user_pq );

					/* ejecutar la consulta */
					mysqli_stmt_execute($st);

					/* ligar variables de resultado */
					mysqli_stmt_bind_result( $st, $feccap, $prioridad );

					/* obtener valor */
					mysqli_stmt_fetch($st);
					
					 /* cerrar sentencia */
					mysqli_stmt_close($st);

					
					
					if(date("Y-m-d") > $feccap)
						$grupo = "PASSWD";
					echo "<center>";
					echo "<hr>";
					
					
					// $query = "select descripcion from usuarios where codigo = '".$key."'";
					// $err1 = mysql_query($query,$conex);
					// $row1 = mysql_fetch_array($err1);
					
					$query = "SELECT descripcion 
								FROM usuarios 
							   WHERE codigo = ? ";
					
					$st = mysqli_prepare( $conex, $query );
					
					/* ligar parámetros para marcadores */
					mysqli_stmt_bind_param($st, "s", $key );

					/* ejecutar la consulta */
					mysqli_stmt_execute($st);

					/* ligar variables de resultado */
					mysqli_stmt_bind_result( $st, $descripcion );

					/* obtener valor */
					mysqli_stmt_fetch($st);
					
					 /* cerrar sentencia */
					mysqli_stmt_close($st);
					
					
					echo "<div class='tipoScreen02'>";
					echo "<center><table border=0>";
					echo "<tr><td Class='tipoT'>Usuario : ".substr($user,2,strlen($user))."</td></tr>";
					echo "<tr><td Class='tipoT1'>".$descripcion."</td></tr>";
					echo "<tr><td Class='tipoT'>IP: ".$IIPP."</td></tr>";
					echo "</table></center>";
					echo "</div>";
					echo "<hr>";
					echo "<font size=3><center><B>MENU DE OPCIONES</b></center></font></center>";
					switch($grupo)
					{
						case 'AMERICAS':
							echo "<ol>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='F1.php?accion=M&grupo=".$grupo."' target='main'>Inicio</A>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='help.php' target='main'>Manual de Operacion</A>";
							echo "<hr>";
							// $key = substr($user,2,strlen($user));
							// $query = "select codigo,descripcion from root_000020 where usuarios like '%-".$key."-%' ";
							// $query .= " or usuarios like '".$key."-%' ";
							// $query .= " or usuarios like '%-".$key."' ";
							// $query .= " or usuarios = '".$key."' ";
							// $query .= " order by codigo";
							// $err = mysql_query($query,$conex);
							// $num = mysql_num_rows($err);
							// if ($num > 0)
							// {
								// for ($i=0;$i<$num;$i++)
								// {
									// $row = mysql_fetch_array($err);
									// echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='f1.php?accion=W&grupo=".$grupo."&amp;codigo=".$row[0]."' target='main'>".strtoupper($row[1])."</A>";
									// echo "<hr>";
								// }
							// }
							
							$key = substr($user,2,strlen($user));
							$query = "select codigo,descripcion from root_000020 where usuarios like ? ";
							$query .= " or usuarios like ? ";
							$query .= " or usuarios like ? ";
							$query .= " or usuarios = ? ";
							$query .= " order by codigo";
							
							$st = mysqli_prepare( $conex, $query );
					
							/* ligar parámetros para marcadores */
							$key_1 = "%-".$key."-%";
							$key_2 = "%-".$key."";
							$key_3 = "".$key."-%";
							$key_4 = $key;
							mysqli_stmt_bind_param($st, "ssss", $key_1, $key_2, $key_3, $key_4 );

							/* ejecutar la consulta */
							mysqli_stmt_execute($st);

							/* ligar variables de resultado */
							mysqli_stmt_bind_result( $st, $codigo, $descripcion );
							
							/* obtener valor */
							while( mysqli_stmt_fetch($st) )
							{
								echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='f1.php?accion=W&grupo=".$grupo."&amp;codigo=".$codigo."' target='main'>".strtoupper($descripcion)."</A>";
								echo "<hr>";
							}
							
							/* cerrar sentencia */
							mysqli_stmt_close($st);
							
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='F1.php?END=on' target='_top' class='myButton'>Salida Segura</A>";
							echo "<hr>";
							echo "</ol>";
						break;
						case 'PASSWD':
							echo "<font size=2><ol>";
							echo "<li><font color=#FF0000><b>PASSWORD VENCIDO</b></font>";
							echo "<hr>";
							echo "<ol>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='password.php?wtipo=N' target='main'>Cambio de Password</A>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='F1.php?END=on' target='_top'>Salir del programa</A>";
							echo "</ol>";
						break;
						default:
							echo "<font size=2><ol>";
							echo "<li>Menu de Maestros";
							echo "<ol>";
							// if ($num > 0)
							// {
								if ($prioridad > 1)
								{			
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='F1.php?accion=M&grupo=".$grupo."' target='main'>Inicio</A>";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='formularios.php' target='main'>Formularios</A>";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='detform.php' target='main'>Detalle de Formularios</A>";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='selecciones.php' target='main'>Selecciones</A>";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='detsel.php' target='main'>Detalle de Selecciones</A>";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='numeracion.php' target='main'>Control de Numeracion</A>";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='seguridad_matrix.php' target='main'>Control de Acceso</A>";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='defpro.php' target='main'>Definicion de Procesos</A>";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='defrep.php' target='main'>Definicion de Reportes</A>";
									if ($prioridad > 2)
										echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='usuarios.php' target='main'>Usuarios</A>";
								}
								else
								{
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34).">Formularios";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34).">Detalle de Formularios";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34).">Seleciones";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34).">Detalle de Selecciones";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34).">Control de Numeracion";
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34).">Control de Acceso";
								}
							// }
							echo "</ol>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='registro.php' target='main'>REGISTRO</A>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='consultas_matrix.php' target='main'>CONSULTAS</A>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='procesos.php?grupo=".$grupo."&amp;prioridad=".$prioridad."' target='main'>PROCESOS</A>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='reportes.php?grupo=".$grupo."&amp;prioridad=".$prioridad."' target='main'>REPORTES</A>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='carga.php' target='main'>Carga de Archivos Planos</A>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='cargaG.php' target='main'>Carga de Archivos Planos Grandes</A>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='publicar.php?grupo=".$grupo."&amp;prioridad=".$prioridad."' target='main'>Publicacion de Archivos</A>";
							if ($prioridad > 2)
							{
								echo "<hr>";
								echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='esquemas.php' target='main'>Esquemas x Usuario</A>";
								echo "<hr>";
								echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='cu_esquemas.php' target='main'>Cambios de Esquemas x Usuario</A>";
							}
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='help.php' target='main'>Manual de Operacion</A>";
							echo "<hr>";
							echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='F1.php?END=on' target='_top' class='myButton'>Salida Segura</A>";
							echo "</ol>";
						break;
					}
				break;
				
				case "M": 	
					echo "<font size=2 face='tahoma'>";
					echo "<BODY TEXT='#000066'>";
					echo "<center>";	
					
					echo "<br><center><table border=0>";
					echo "<tr><td align=center colspan=2><A HREF='http://intranetlasamericas.co' target='_blank' class='myButton'><IMG SRC='/matrix/images/medical/root/ingenia.jpg' style='vertical-align:middle;'></A></td></tr>";
					//echo "<tr><td align=center colspan=2><b>BIENVENIDO A LAS AMERICAS MATRIX<b></td></tr>";
					echo "<tr><td align=center colspan=2><br><A HREF='https://www.lasamericas.com.co/Gesti%C3%B3n-%C3%A9tica' target='_blank' ><IMG SRC='/matrix/images/medical/root/linkGestionetica.jpg' width='62%' height='45%' style='vertical-align:middle;'></A></td></tr>";	

					echo "<tr><td align=right colspan=2><font size=1>Powered by : <IMG SRC='/matrix/images/medical/root/powered.png' style='vertical-align:middle;'></font></td></tr>";
					echo "<tr><td align=center colspan=2 bgcolor=#999999><b>NOTICIAS PARA HOY : ".date("d-m-Y")."<b></td></tr>";
					echo "<tr><td  bgcolor=#cccccc><font size=2><b>TEMA<b></font></td><td  bgcolor=#cccccc><font size=2><b>TEXTO<b></font></td></tr>";
					$query = "select   Tema, Texto    from root_000022 where activo='on' ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<tr onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=#EAEAEA>";
						echo "<td><font size=2>".$row[0]."</font></td><td><font size=2>".$row[1]."</font></td></tr>";
					}
					echo "</table>";
					echo "</center>";
				break;
			}
		}
		else
		{
			echo "<html>";
			echo "<head>";
			echo "  <title>MATRIX</title>";
			echo "	<style type='text/css'>";
			echo "	<!--";
			echo "		.BlueThing{color:#000066;background: #CCCCFF;font-size:10pt;font-weight:bold;font-family:Ubuntu;height:2em;}";
			echo "		.SilverThing{color:#000066;background: #EAEAEA;font-size:10pt;font-weight:bold;font-family:Ubuntu;height:2em;}";
			echo "		.GrayThing{color:#000066;background: #EAEAEA;font-size:10pt;font-weight:bold;font-family:Ubuntu;height:2em;}";
			echo "		.tipoB{color:#000066;background:#FFFFFF;font-size:6pt;font-family:Arial;font-weight:normal;text-align:justify;border-style:solid;border-collapse:collapse;border-color:#000066;border-width:1px;}";
			echo "		.tipoL{color:#000066;background:#EAEAEA;font-size:10pt;font-family:Ubuntu;font-weight:bold;height:2em;}";
			echo "	//-->";
			echo "	</style>";
			echo "	<script type='text/javascript'>";
			echo "			function ejecutar(path)";
			echo "			{";
			echo "				window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');";
			echo "			}";
			echo "			function toggleDisplay(id,id1)";
			echo "			{";
			echo "				if (id.style.display=='none')";
			echo "				{";
			echo "					id.style.display='';";
			echo "					id1.src = '/matrix/images/medical/root/menos.png';";
			echo "				}";
			echo "				else ";
			echo "				{";
			echo "					id.style.display='none';";
			echo "					id1.src = '/matrix/images/medical/root/mas.png';";
			echo "				}";
			echo "			}";
			echo "	</script>";
			echo "</head>";
			echo "<body BGCOLOR=''>";
			echo "<BODY TEXT='#000066'>";
				
			echo "<center>";
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=3><b>Opciones del Grupo de Informacion</b></font></a></tr></td>";
			echo "</table></center>";

			@session_start();
			if(!isset($_SESSION['user']))
				echo "<A HREF='F1.php?END=on' target='_top'><IMG SRC='/matrix/images/medical/root/expire.png'>Su Sesion Ha Expirado. Por Favor Haga Click Aqui !!</A>";
			else
				{
					if(isset($position))
						echo "Posicion : ".$position;
					$key = substr($user,2,strlen($user));
					echo "<form name='inventario' action='F1.php' method=post>";
					

					

					$index = -1;
					$w = -1;
					$linea = 0;
					$DATA=array();
					listar($conex,$grupo,$codigo,$usera,$w,$DATA);
					echo "<br><table border=0 align=center>";
					for ($i=0;$i<=$w;$i++)
					{
						if($DATA[$i][4] == 0)
						{
							$color = "#999999"; 
							echo "<table border=0 align=center width='95%'>";
							echo "<tr><td align=center bgcolor='#cccccc' colspan=3><font size=3 face='Tahoma'> <b>".$DATA[$i][1]." -  Grupo : ".$DATA[$i][0]."</b></font></td></tr>";
							echo "<tr>";
							echo "<td bgcolor=".$color."><b>Opcion Nro.</b></td>";
							echo "<td bgcolor=".$color."><b>Descripcion</b></td>";
							echo "<td bgcolor=".$color."><b>Programa</b></td>";
							echo "</tr>";
						}
						elseif($DATA[$i][4] == 3)
						{

							echo "</table></td></tr>";
						}
						else
						{
							$linea++;
							if($linea % 2 == 0)
								echo "<tr onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." class='tipoL'>";
							else
								echo "<tr onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='SilverThing';".chr(34)." class='tipoL'>";
							while(strlen($linea) < 4)
								$linea="0".$linea;
							echo "<td align=center>".$linea."</td>";
							echo "<td>".strtoupper($DATA[$i][1])."</td>";
							if($DATA[$i][4] == 2)
							{
								$index++;
								echo "<td align=center><IMG SRC='/matrix/images/medical/root/mas.png' style='vertical-align:middle;' id='img".$index."' OnClick='toggleDisplay(div".$index.",img".$index.")'></td></tr>";
								echo "<tr id='div".$index."' style='display:none'><td colspan=3 class='tipoB'>";
							}
							else
							{
								$token 	  = '';
								$datosJWT = array_pop( explode( "/", parse_url( $DATA[$i][2] )['path'] ) );
								$JWT 	  = explode( "-", $datosJWT );
								if( $JWT[0] == "JWT" )
								{
									if( $_SESSION && $_SESSION['codigo'] )
									{
										$encabezado	= [
														'alg' => 'HS256', 
														'typ' => 'JWT' 
													];
													
										$datos 		= [
														'usuario' 	=> $_SESSION['codigo'], 
														'password' 	=> $_SESSION['password'], 
														'wemp_pmla'	=> $JWT[1], 
														'iat'		=> time(), 
														'exp'		=> time()+24*3600, 
													];
													
										$secret_key = consultarAliasPorAplicacion( $conex, $JWT[1], "jwtLaravelToyota" );
										$cifrado 	= 'sha256';
										
										$token = crearTokenJwt( $encabezado, $datos, $secret_key, $cifrado );
										
										$dt = explode( "/", $DATA[$i][2] );
										$dt[ count($dt)-1 ] = "token/".$token.( parse_url( $DATA[$i][2] )['query'] ? "?".parse_url( $DATA[$i][2] )['query'] : '' );
										$DATA[$i][2] = implode( "/", $dt );
									}
								}
								
								if(strtoupper(substr($DATA[$i][3],0,3)) == "JSP")
								{
									$path=substr($DATA[$i][3],3).$DATA[$i][2];
									echo "<td align=center><input type='button' value='Ok'  onclick='ejecutar(".chr(34).$path.chr(34).")'></td>";
								}
								elseif(substr($DATA[$i][3],0,4) == "http")
										echo "<td align=center><A HREF='".$DATA[$i][2]."'>Ejecutar</td>";
									else
										echo "<td align=center><A HREF='".$DATA[$i][3].$DATA[$i][2]."'>Ejecutar</td>";
								echo "</tr>";
							}
						}
					}
					
					//Se cierra conexión de la base de datos :)
					//Se comenta cierre de conexion para la version estable de matrix :)
					//mysql_close($conex);
					
					echo "</table>";
					echo "<table border=0 align=center><tr><td align=center><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
				}
		}
	}
	//echo "</div>";
	echo "</body>";
	echo "</html>";
}
?>
