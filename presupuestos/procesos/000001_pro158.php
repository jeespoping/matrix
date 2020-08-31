<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Carga de Archivos Grandes</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro158.php Ver. 2012-08-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro158.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($arch))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CARGA DE ARCHIVOS GRANDES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='arch' size=80 maxlength=80></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$count=0;
			$query = "select count(*) from ".$empresa."_000109 ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$inicial = $row[0];
			$query = "LOAD DATA INFILE '".$arch."'  INTO TABLE ".$empresa."_000109 FIELDS TERMINATED BY ',' ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$query = "select count(*) from ".$empresa."_000109 ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$final = $row[0];
			$total = $final - $inicial;
			echo "Carga Exitosa Numero de Registros Almacenados :".$total;
		}
	}
?>
</body>
</html>
