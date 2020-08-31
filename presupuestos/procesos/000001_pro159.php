<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Borrado Movimiento Tabla 109</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro159.php Ver. 2012-08-29</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro159.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wper1))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>BORRADO MOVIMIENTO TABLA 109</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "select count(*) from ".$empresa."_000109 ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$inicial = $row[0];
		$query = "delete from ".$empresa."_000109  ";
		$query = $query."  where Fadano =  ".$wanop;
		$query = $query."    and Fadmes =  ".$wper1;
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$query = "select count(*) from ".$empresa."_000109 ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$final = $row[0];
		$total = $inicial - $final;
		echo "Borrado Exitoso Numero de Registros Borrados :".$total;
	}
}
?>
</body>
</html>
