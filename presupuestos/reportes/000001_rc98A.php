<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Costos Comparativos x Unidad de Negocio</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc98.php Ver. 2009-06-02</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc98W.php' method=post>";
		if(!isset($wanop)   or !isset($wpv) or !isset($wtp) or !isset($wpro1) or !isset($wpro2) or (strtoupper ($wtp) != "P" and strtoupper ($wtp) != "S" and strtoupper ($wtp) != "T") or !isset($wcco1) or !isset($wcco2) or !isset($wper1)  or !isset($wper2)   or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COSTOS COMPARATIVOS X UNIDAD DE NEGOCIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Procedimiento Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro1' size=10 maxlength=10 value=0></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Procedimiento Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro2' size=10 maxlength=10 value=z></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Procedimientos (P - Principal / S Secundario / T - Todos)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtp' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Porcentaje de Variacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpv' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<frameset rows='80,*' frameborder=0 framespacing=0>";
			echo "<frame src='/Presupuestos/Reportes/000001_rc98B.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."' name='titulos' marginwidth=0 marginheiht=0>";
			echo "<frame src='/Presupuestos/Reportes/000001_rc98C.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wcco1=".$wcco1."&wcco2=".$wcco2."&wpro1=".$wpro1."&wpro2=".$wpro2."&wtp=".$wtp."&wpv=".$wpv."' name='main' marginwidth=0 marginheiht=0>";
		}
	}
?>
</body>
</html>
