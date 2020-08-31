<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='barras.php' method=post>";
		if(!isset($Codigo))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>IMPRESION DE STIKERS DE CODIGOS DE BARRAS</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='Codigo' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
		}
		else
		{
			echo "<p><font size=13 face='3 of 9 barcode'>"."*".$Codigo."*"."</font>";
			echo "<br><font size=5>".$Codigo."</font></p>";
			
		}
	}
?>
</body>
</html>