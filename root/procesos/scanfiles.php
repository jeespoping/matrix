<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Visor de Archivos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> scanfiles.php Ver. 2008-12-24</b></font></tr></td></table>
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
	echo "<form action='scanfiles.php'>";
	echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	if(!isset($folder))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>VISOR DE ARCHIVOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nombre de la Carpeta</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='folder'  size=20 maxlength=20 /></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$datafile="..\..\planos\\".$folder;
		echo "<A href=".$datafile.">ARCHIVOS</A>";
	}
}
?>
</body>
</html>