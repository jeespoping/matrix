<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Reclasificacion de Centros de Costos Tablas(2 - 11 - 26 - 54)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro156.php Ver. 2015-09-18</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro156.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1)  or !isset($wcco2) or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>RECLASIFICACION DE CENTROS DE COSTOS TABLAS(2 - 11 - 26 - 54)</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Unidad Origen</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Unidad Destino</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
		$wemp = substr($wemp,0,2);
		$query = "UPDATE ".$empresa."_000002 set Almcco = '".$wcco2."'  ";
		$query = $query."  where Almano =  ".$wanop;
		$query = $query."    and Almmes =  ".$wper1;
		$query = $query."    and Almcco =  '".$wcco1."'";
		$query = $query."    and Almemp =  '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO TABLA 2 DE COSTOS Y PRESUPUESTOS : ".mysql_errno().":".mysql_error());
		echo "TABLA 2 ACTUALIZADA : <BR>";
		$query = "UPDATE ".$empresa."_000011 set Expcco = '".$wcco2."'  ";
		$query = $query."  where Expano =  ".$wanop;
		$query = $query."    and Expper =  ".$wper1;
		$query = $query."    and Expcco =  '".$wcco1."'";
		$query = $query."    and Expemp =  '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO TABLA 11 DE COSTOS Y PRESUPUESTOS : ".mysql_errno().":".mysql_error());
		echo "TABLA 11 ACTUALIZADA : <BR>";
		$query = "UPDATE ".$empresa."_000026 set Meccco = '".$wcco2."'  ";
		$query = $query."  where Mecano =  ".$wanop;
		$query = $query."    and Mecmes =  ".$wper1;
		$query = $query."    and Meccco =  '".$wcco1."'";
		$query = $query."    and Mecemp =  '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO TABLA 26 DE COSTOS Y PRESUPUESTOS : ".mysql_errno().":".mysql_error());
		echo "TABLA 26 ACTUALIZADA : <BR>";
		$query = "UPDATE ".$empresa."_000054 set Mdicco = '".$wcco2."'  ";
		$query = $query."  where Mdiano =  ".$wanop;
		$query = $query."    and Mdimes =  ".$wper1;
		$query = $query."    and Mdicco =  '".$wcco1."'";
		$query = $query."    and Mdiemp =  '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO TABLA 54 DE COSTOS Y PRESUPUESTOS : ".mysql_errno().":".mysql_error());
		echo "TABLA 54 ACTUALIZADA : <BR>";
	}
}
?>
</body>
</html>
