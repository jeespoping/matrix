<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Publicacion de Firmas de Medicos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> publicar_firmas.php Ver. 2013-06-14</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	if(!isset($key))
		$key = substr($user,2,strlen($user));
	

	

	echo "<form action='publicar_firmas.php' enctype='multipart/form-data' method=post>";
	if(!isset($files))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2>PUBLICACION DE FIRMAS HCE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
		echo "<td bgcolor=#cccccc><input type='file' name='files'  size=60 maxlength=60 /></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'></td></tr></table>";
	}
	else
	{
		$real= $_FILES['files']['name'];
		$files=$_FILES['files']['tmp_name'];
		$ruta="/var/www/matrix/images/medical/hce/Firmas/";
		$dh=opendir($ruta);
		if(readdir($dh) == false)
			mkdir($ruta,0777);
		if (!isset($ruta) or !copy($files, $ruta.$real)) 
		{
			echo "ERROR LA COPIA NO PUDO HACERSE<br>";
		}
		else
		{
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor=#DDDDDD>LA PUBLICACION EXITOSA</td></tr>";
			echo "<tr><td align=center bgcolor=#DDDDDD>ARCHIVO: <B>".$real."</B></td></tr>";
			echo "<tr><td align=center bgcolor=#DDDDDD>RUTA :<B>".$ruta."</B></td></tr></table>";
		}
	}
}
?>
</body>
</html>
