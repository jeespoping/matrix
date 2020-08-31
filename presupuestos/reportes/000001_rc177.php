<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Diccionario de Actividades</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc177.php Ver. 2016-04-28</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc177.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($west) or !isset($wemp) or $wemp == "Seleccione" or (isset($west) and $west != "on" and $west != "off") or !isset($wcco))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DICCIONARIO DE ACTIVIDADES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Estado (on/off)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='west' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wemp'>";
				echo "<option>Seleccione</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query = "SELECT Diccco,Dicsub,Dicnsu,Dicdsu,Dicdri,Dicndr,Diccal from ".$empresa."_000018 ";
			$query = $query."  where Diccco = '".$wcco."' ";
			$query = $query."    and Dicemp = '".$wemp."'"; 
			$query = $query."    and Dicest = '".$west."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=0>";
			echo "<tr><td align=center colspan=7 bgcolor=#dddddd><font size=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td align=center colspan=7 bgcolor=#dddddd><font size=2><b>DIRECCION DE INFORMATICA</b></font></td></tr>";
			echo "<tr><td align=center colspan=7 bgcolor=#dddddd><font size=2><b>DICCIONARIO DE ACTIVIDADES</b></font></td></tr>";
			echo "<tr><td align=center colspan=7 bgcolor=#dddddd><font size=2><b>EMPRESA : ".$wempt."</b></font></td></tr>";
			echo "<tr><td align=center colspan=7 bgcolor=#dddddd><font size=2><b>UNIDAD ".$wcco."</b></font></td></tr>";
			echo "<tr><td align=center colspan=7 bgcolor=#dddddd><font size=2><b>ESTADO ".$west."</b></font></td></tr>";
			echo "<tr><td bgcolor=#999999><b>UNIDAD</b></td><td bgcolor=#999999 align=center><b>SUBPROCESO</b></td><td bgcolor=#999999 align=center><b>NOMBRE<BR>SUBPROCESO</b></td><td bgcolor=#999999 align=center><b>DESCRIPCION<BR>SUBPROCESO</b></td><td bgcolor=#999999 align=center><b>DRIVER</b></td><td bgcolor=#999999 align=center><b>NOMBRE<BR>DRIVER</b></td><td bgcolor=#999999 align=center><b>CALCULO<BR></b></td></tr>";
			$wccant="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($i % 2 == 0)
					$color = "#E0ECFF";
				else
					$color = "#E8EEF7";
				echo "<tr><td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color.">".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td><td bgcolor=".$color.">".$row[3]."</td><td bgcolor=".$color.">".$row[4]."</td><td bgcolor=".$color.">".$row[5]."</td><td bgcolor=".$color.">".$row[6]."</td></tr>";
    		}
    		echo "</table>";
		}
	}
?>
</body>
</html>
