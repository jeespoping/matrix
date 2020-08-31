<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Seleccion Movimiento Facturacion (T160 a T108)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro160A.php Ver. 2015-11-26</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='000001_pro160A.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wanop) or !isset($wmesi))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>SELECCION MOVIMIENTO FACTURACION (T160 A T108)</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$querytime_before = array_sum(explode(' ', microtime()));
		$query = "delete from ".$empresa."_000108  ";
		$query = $query."  where Mosano =  ".$wanop;
		$query = $query."    and Mosmes =  ".$wmesi;
		$query = $query."    and Mostip in ('FA','RF')";
		$err = mysql_query($query,$conex) or die ("1 ".mysql_errno().":".mysql_error());
		$query = "insert into ".$empresa."_000108 (medico,fecha_data,hora_data,Mosano,Mosmes,Mostip,Moscon,Moslin,Moscco,Mosent,Mospro,Mosdes,Moshis,Mosing,Mosmed,Moscan,Mosipr,Mosite,Mosctt,Mosutt,Mosctv,Mosutv,Mosest,seguridad) ";
		$query = $query."select medico,fecha_data,hora_data,Mosano,Mosmes,Mostip,Moscon,Moslin,Moscco,Mosent,Mospro,Mosdes,Moshis,Mosing,Mosmed,Moscan,Mosipr,Mosite,Mosctt,Mosutt,Mosctv,Mosutv,Mosest,seguridad from ".$empresa."_000160 ";
		$query = $query."where Mosano =  ".$wanop;
		$query = $query."  and Mosmes =  ".$wmesi;
		$err = mysql_query($query,$conex) or die ("2 ".mysql_errno().":".mysql_error());
		$querytime_after = array_sum(explode(' ', microtime(true)));
		$DIFF=$querytime_after - $querytime_before;
		echo "Tiempo : ".$DIFF."<br>";
	}
}		
?>
</body>
</html>
