<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion Tipo y Nombre de Habitacion(T113)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro180.php Ver. 2014-10-24</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro180.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ACTUALIZACION TIPO Y NOMBRE DE HABITACION(T113)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$k=0;
			$query = "Update ".$empresa."_000113 set Mhotip = (select habtip from ".$empresa."_000039 where Habcco=Mhocco and Habhab=Mhohab) where Mhoano=".$wanop." and Mhomes=".$wper." ";
			$err1 = mysql_query($query,$conex);
			if ($err1 != 1)
				echo mysql_errno().":".mysql_error()."<br>";
			else
			{
				$k++;
				echo "REGISTRO ACTUALIZADO  : ".$k."<br>";
			}
			$query = "Update ".$empresa."_000113 set Mhonom = (select habdes from ".$empresa."_000039 where Habcco=Mhocco and Habhab=Mhohab) where Mhoano=".$wanop." and Mhomes=".$wper." ";
			$err1 = mysql_query($query,$conex);
			if ($err1 != 1)
				echo mysql_errno().":".mysql_error()."<br>";
			else
			{
				$k++;
				echo "REGISTRO ACTUALIZADO  : ".$k."<br>";
			}

		}
	}
?>
</body>
</html>
