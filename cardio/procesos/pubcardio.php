<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Publicacion Imagenes de Cardiologia</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> pubcardio.php Ver. 2013-12-27</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		if(!isset($key))
			$key = substr($user,2,strlen($user));
		

		

		echo "<form action='pubcardio.php' enctype='multipart/form-data' method=post>";
		echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
		if(!isset($files) or !isset($folder))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PUBLICACION DE IMAGENES DE CARDIOLOGIA EN LA INTRANET</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
			echo "<td bgcolor=#cccccc><input type='file' name='files'  size=60 maxlength=60 /></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre de la Carpeta</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='folder' size=30 maxlength=30 /></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'></td></tr></table>";
		}
		else
		{
			//$real= $HTTP_POST_FILES['files']['name'];
			//$files=$HTTP_POST_FILES['files']['tmp_name'];
			//$ruta="C:/inetpub/wwwroot/MATRIX/images/medical/cardio/".$folder."/";
			//24 DIC 2013 Se cambian solo estas tres lineas de codigo ya que no permitia publicar las imagenes (Gabriel Agudelo)
			$real= $_FILES['files']['name'];
			$files=$_FILES['files']['tmp_name'];
			$ruta="/var/www/matrix/planos/cardio/".$folder."/";
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
