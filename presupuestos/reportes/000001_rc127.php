<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Composicion De Los Ingresos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc26.php Ver. 1.01</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc26.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wper2)  or !isset($wres)  or !isset($wter) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or (strtoupper ($wter) != "S" and strtoupper ($wter) != "N")  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPOSICION DE LOS INGRESOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			if(isset($wres))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1 value=".$wres."></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Imprimir Solamente Terceros ? (S/N)</td>";
			if(isset($wter))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wter' size=1 maxlength=1 value=".$wter."></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wter' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}