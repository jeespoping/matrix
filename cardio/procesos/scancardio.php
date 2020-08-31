<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Imagenes de Cardiologia</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> scancardio.php Ver. 2013-12-27</b></font></tr></td></table>
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
		

		

		echo "<form action='scancardio.php'>";
		$datafile="http://mx.lasamericas.com.co/matrix/planos/cardio/imagenes/";
		echo "<A href=".$datafile.">IMAGENES</A>";
}
?>
</body>
</html>
