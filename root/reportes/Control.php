<html>
<head>
  	<title>MATRIX</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	echo "<form action='control.php' method=post>";
	if(!isset($ok))
	{
		$ok="1";
		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2  bgcolor=#cccccc><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc  colspan=2><font size=4 face='tahoma'><b>DIRECCION DE INFORMATICA</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc  colspan=2><font size=4 face='tahoma'><b>CONTROL DE ACTIVIDADES PROGRAMADAS</font></b></font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2>Ordenar Por : <INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 1 CHECKED> GRUPO  ";
		echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 2> FECHA <INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 3> RESPONSABLE </td></tr>";	
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		$key = substr($user,2,strlen($user));
		echo "<form name='Control' action='Control.php' method=post>";
		

		

		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/matrix5_r.gif'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>PROMOTORA MEDICA LAS AMERICAS S.A.</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc><font size=4 face='tahoma'><b>DIRECCION DE INFORMATICA</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc><font size=4 face='tahoma'><b>CONTROL DE ACTIVIDADES PROGRAMADAS</font></b></font></td></tr></table><br><br>";
		$query = "SELECT  Grupo, Descripcion, Fecha_Terminacion, Responsable, Avance  from root_000026 ";
		switch ($radio1)
		{
			case 1:
				$query .= "     ORDER BY  Grupo  ";
			break;
			case 2:
				$query .= "     ORDER BY  Fecha_Terminacion  ";
			break;
			case 3:
				$query .= "     ORDER BY  Responsable  ";
			break;
		}
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>GRUPO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FECHA<BR>TERMINACION</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>RESPONSABLE</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>AVANCE</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."</font></td>";	
			if($row[4] < 34)
				$colora="#FF0000";
			elseif($row[4] > 33 and $row[4] < 67)
						$colora="#FFFF00";
					elseif($row[4] > 66 and $row[4] < 100)
								$colora="#00FF00";
							else
								$colora="#99CCFF";
			echo "<td bgcolor=".$colora." align=center><font face='tahoma' size=2><b>".$row[4]."</b></font></td></tr>";	
		}
		echo "<tr><td bgcolor=#999999 colspan=5><font face='tahoma' size=2><b>REGISTROS TOTALES : ".number_format((double)$num,0,'.',',')."</b></font></td></tr>";	
		echo"</table>";
	}
}
?>
</body>
</html>