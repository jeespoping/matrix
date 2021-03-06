<?php
include_once("conex.php");
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : F1.php
	   Fecha de Liberacion : 2004-04-20
	   Autor : Pedro Ortiz Tamayo
	   Version Inicial : 2004-04-20
	   Version actual  : 2016-06-03
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite registrar los datos
	   Administrativos, Financieros y Clinicos
	   
	   REGISTRO DE MODIFICACIONES :
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
			$query = "select codopt,descripcion,programa,ruta from root_000021 where codgru='".$codigo."' and usuarios like '%".$usera."%' order by codopt ";
		break;
	}
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
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
				listar($conex,$grupo,$codigo,$usera,&$w,&$DATA);
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

if(isset($_SESSION["NS"]) and $_SESSION["NS"] == 0)
{
	$_SESSION["NS"] = 1;
	$accion = 0;
}
if(!isset($accion))
{
	echo "<html>";
	echo "<head>";
	echo "<title>MATRIX</title>";
	echo "<style type='text/css'>";
	echo "	#tipo1{color:#000000;font-size:14pt;font-family:Arial;font-weight:bold;width:120em;text-align:left;height:6em;}";
	echo "	#tipo1a{height:1.5em;}";
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
	echo "</head>";
	echo "<body onload=ira() BGCOLOR='ffffff'>";
	echo "<BODY TEXT='#000066'>";
	echo "<form name='entrada' action='F1.php?accion=I' method=post>";
	echo "<center><table border=0>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/GELA.png'></td></tr>";
	echo "</table></center>";
	echo "<br><table border=0 align=center>";
	echo "<tr>";
	echo "	<td id=tipo3 valign=middle>";
	echo "      <IMG SRC='/matrix/images/medical/root/Logo_Matrix.png'><br><br>";
	echo "	<div class='tipoScreen02'>";
	echo "		<table border=0 style='width : 100%'>";	
	echo "			<tr><td align=center id=tipo5 colspan=2><IMG SRC='/matrix/images/medical/root/boton-9.png'></td></tr>";
	echo "          <tr><td align=center id=tipo1a colspan=2></td></tr>";
	echo "			<tr><td align=center id=tipo4>C&oacute;digo</td><td align=center id=tipo4><input type='text' name='codigo' size=10 maxlength=8></td></tr>";
	echo "			<tr><td align=center id=tipo4>Clave</td><td align=center id=tipo4><input type='password' name='password' size=10 maxlength=8></td></tr>";
	echo "          <tr><td align=center id=tipo1a colspan=2></td></tr>";
	echo "			<tr><td align=center colspan=2><button onClick='enter()' class='tipoHIDE'><IMG SRC='/matrix/images/medical/root/boton-10.png'></button></td></tr>";
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
			@session_start();
			if (!isset($user))
			{
				if(!isset($_SESSION['user']))
				{
					session_register("user","usera","IIPP","NS");
					$user="1-".strtolower($codigo);
					$usera=strtolower($codigo);
					$ipdir=explode("|",GetIP());
					$IIPP=$ipdir[0];
					$NS=1;
				}
			}
			

			mysql_select_db("matrix") or die ("ERROR AL CONECTARSE A MATRIX");
			$query = "select codigo,prioridad,grupo from usuarios where codigo='".$codigo."' and password='".$password."' and activo = 'A'";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$prioridad=$row[1];
			$codigo=$row[0];
			$grupo=$row[2];
			mysql_free_result($err);
			mysql_close($conex);
			if ($num > 0)
			{
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
				echo "<td id=tipo1><A HREF='f1.php?END=on'>USUARIO NO EXISTE O ESTA INACTIVO</a></td></tr></table></body>";
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
					

					

					$query = "select Codigo, Password, Passdel, Feccap, Tablas, Descripcion, Prioridad, Grupo, Empresa, Ccostos, Activo  from usuarios where codigo = '".substr($user,2,strlen($user))."'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$row = mysql_fetch_array($err);
					if(date("Y-m-d") > $row[3])
						$grupo = "PASSWD";
					echo "<center>";
					echo "<hr>";
					$query = "select descripcion from usuarios where codigo = '".$key."'";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					echo "<div class='tipoScreen02'>";
					echo "<center><table border=0>";
					echo "<tr><td Class='tipoT'>Usuario : ".substr($user,2,strlen($user))."</td></tr>";
					echo "<tr><td Class='tipoT1'>".$row1[0]."</td></tr>";
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
							$key = substr($user,2,strlen($user));
							$query = "select codigo,descripcion from root_000020 where usuarios like '%-".$key."-%' ";
							$query .= " or usuarios like '".$key."-%' ";
							$query .= " or usuarios like '%-".$key."' ";
							$query .= " or usuarios = '".$key."' ";
							$query .= " order by codigo";
							$err = mysql_query($query,$conex);
							$num = mysql_num_rows($err);
							if ($num > 0)
							{
								for ($i=0;$i<$num;$i++)
								{
									$row = mysql_fetch_array($err);
									echo "<li onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)."><A HREF='f1.php?accion=W&grupo=".$grupo."&amp;codigo=".$row[0]."' target='main'>".strtoupper($row[1])."</A>";
									echo "<hr>";
								}
							}
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
							if ($num > 0)
							{
								if ($row[6] > 1)
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
									if ($row[6] > 2)
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
							}
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
							if ($row[6] > 2)
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
					//echo "<tr><td align=center colspan=2>Haga Click En La Imagen<br><A HREF='/matrix/images/medical/ingenia/INGENIA.webm' target='_blank' class='myButton'><IMG SRC='/matrix/images/medical/root/ingenia.jpg' style='vertical-align:middle;'></A></td></tr>";
					echo "<tr><td align=center colspan=2>Haga Click En La Imagen<br><A HREF='https://docs.google.com/forms/d/e/1FAIpQLSe2GFXjS5o7akj-gPMJhG7bRW_kF8cVbiv-_JWjSjmWcG37Qw/viewform' target='_blank' class='myButton'><IMG SRC='/matrix/images/medical/root/ingenia.jpg' style='vertical-align:middle;'></A></td></tr>";
					echo "<tr><td align=center colspan=2><b>BIENVENIDO A LAS AMERICAS MATRIX<b></td></tr>";
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
					listar($conex,$grupo,$codigo,$usera,&$w,&$DATA);
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
