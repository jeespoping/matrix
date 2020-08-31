<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='password_lab.php' method=post>";
		if(!isset($codigo) or !isset($wpass) or !isset($wpassr))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>LABORATORIO MEDICO LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>NOMINA Y PRESTACIONES SOCIALES</td></tr>";
			echo "<tr><td align=center colspan=2>COLILLA DE PAGO</td></tr>";
			echo "<tr><td align=center colspan=2>CAMBIO DE PASSWORD</td></tr>";
			echo "<td bgcolor=#cccccc align=center>Password Nuevo</td>";
			echo "<td bgcolor=#cccccc align=center><input type='password' name='wpass' size=8 maxlength=8></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Redigite Password</td>";
			echo "<td bgcolor=#cccccc align=center><input type='password' name='wpassr' size=8 maxlength=8></td></tr>";	;		
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
			echo "<input type='HIDDEN' name= 'codigo' value='".$codigo."'>";
		}
		else
		{   
			if($wpass==$wpassr)
			{
				$query = "update usuarios set password='".$wpass."' where codigo='".$codigo."'";
				$err = mysql_query($query,$conex);
				if($err !=1)
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PASSWORD NO SE PUDO CAMBIAR !!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/laboratorio/mario.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>EL PASSWORD SE CAMBIO SATISFACTORIAMENTE !!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffff00 LOOP=-1>ERROR EN LA DIGITACION !!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
			echo "<li><A HREF='000001_rep5.php?cedula=".$codigo."'>Programa de Colillas de Pago</A>";
			echo "<li><A HREF='SALIDA.php'>Salir del Programa</A>";
	}
}
?>
</body>
</html>