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
		

		

		echo "<form action='000001_rev1.php' method=post>";
		if(!isset($cedula_i) or !isset($cedula_f))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ACTUALIZACION Y CONTROVERSIAS EN MEDICINA</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center colspan=2><b>Digite los Numeros de la Cedula Inicial  y Final a Imprimir</b></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='cedula_i' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='cedula_f' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
		}
		else
		{
			$query = "select nombre_completo,cedula  from evento_000001 where cedula between'".$cedula_i."' and '".$cedula_f."' and asiste='on' and certificado='off' order by nombre_completo ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$paciente=0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = "update evento_000001 set certificado='on' where cedula='".$row[1]."'";
					$err1 = mysql_query($query,$conex);
					echo "<center>";
					echo "<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
					echo "<p><font size=7><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp".$row[0]."</font></b></p>";
					echo "</center>";
					echo "<div style='page-break-before: always'>";	
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE NINGUN ASISTENTE !!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
	}
?>
</body>
</html>