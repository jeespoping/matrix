<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pev1.php' method=post>";
		if(!isset($cedula))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>EVENTO (Asistencia)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><B>CODIGO</B></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><input type='TEXT' name='cedula' size=12 maxlength=12></td></tr></table>";
		}
		else
		{
			$query = "select nombre  from evento_000001 where nombre like '".$cedula."-%' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num == 1)
			{
				$row = mysql_fetch_array($err);
				echo "<center>";
				echo "<font size=4><B><MARQUEE BEHAVIOR=SLIDE DIRECTION=RIGHT ALIGN=MIDDLE HEIGHT=35 BGCOLOR=#99CCFF LOOP=-1>ASISTENTE : ".$row[0]."<I> .......REGISTRADO......</I></MARQUEE></B></FONT>";
				echo "</center><br><br><br>";
				$query = "update evento_000001 set asistencia='on' where nombre = '".$row[0]."' ";
				$err1 = mysql_query($query,$conex);
				unset($cedula);
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE ESTE ASISTENTE REVISE !!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>EVENTO (Asistencia)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><B>CODIGO</B></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><input type='TEXT' name='cedula' size=12 maxlength=12></td></tr></table>";
	    }
}
include_once("free.php");
?>
</body>
</html>