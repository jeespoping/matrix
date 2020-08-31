<html>
<head>
  	<title>MATRIX Listado Anexo de Socios</title>
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
	$key = substr($user,2,strlen($user));
	echo "<form name='Anexo1' action='anexo1.php' method=post>";
	

	

	echo "<table border=1 align=center>";
	echo "<tr><td align=center bgcolor=#999999 colspan=7><font size=6 face='courier new'><b>PROMOTORA MEDICA LAS AMERICAS S.A.</font></b></font></td></tr>";
	echo "<tr><td align=center bgcolor=#cccccc colspan=7><font size=4 face='courier new'><b>ASAMBLEA ORDINARIA DE SOCIOS</b></font></font></td></tr>";
	echo "<tr><td align=center bgcolor=#cccccc colspan=7><font size=4 face='courier new'><b>Fecha : ".date("d-m-Y")."</b></font></font></td></tr>";
	echo "<tr><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>CEDULA</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>NOMBRE</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ACCIONES<br>PREF./94</b></font></td><td align=center bgcolor=#dddddd ><font face='courier new' size=2><b>ACCIONES<br>PREF./96</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ACCIONES<br>ORDINARIAS</b></font><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>TOTAL<BR>ACCIONES</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ASISTIO</b></font></td></tr>";
	$conex_o = odbc_connect('cajban','','');
	$query = "select socced, socnom, socp94, socp96, socord, soctot, socemp, socasi from ejdsoc order by 2";
	$err_o = odbc_do($conex_o,$query);
	$campos= odbc_num_fields($err_o);
	$count=0;
	$soc=0;
	$k=0;
	$as=0;
	$ac=0;
	while (odbc_fetch_row($err_o))
	{
		$k++;
		$count++;
		$odbc=array();
		for($m=1;$m<=$campos;$m++)
		{
			$odbc[$m-1]=odbc_result($err_o,$m);
		}
		$soc += $odbc[5];
		$color="#FFFFFF";
		echo "<tr><td bgcolor=".$color."><font face='courier new' size=2>".$odbc[0]."</font></td>";	
		if($odbc[6] == "S")
			$color="#cccccc";
		echo "<td bgcolor=".$color."><font face='courier new' size=2>".$odbc[1]."</font></td>";	
		$color="#FFFFFF";
		echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>".number_format((double)$odbc[2],0,'.',',')."</font></td>";
		echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>".number_format((double)$odbc[3],0,'.',',')."</font></td>";	
		echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>".number_format((double)$odbc[4],0,'.',',')."</font></td>";
		echo "<td bgcolor=".$color." align=center><font face='courier new' size=2>".number_format((double)$odbc[5],0,'.',',')."</font></td>";
		if($odbc[7] == "S")
		{
			$as++;
			$ac += $odbc[5];
			$color="#cccccc";
			echo "<td bgcolor=".$color."><font face='courier new' size=2>ASISTIO</font></td>";	
		}
		else
			echo "<td bgcolor=".$color."><font face='courier new' size=2> NO ASISTIO</font></td>";
		$color="#FFFFFF";
		/*if($k == 43)
		{
			$k=0;
			echo "</table>";
			echo "</div>";
			echo "<div style='page-break-before: always'>";	
			echo "<table border=1 align=center>";
			echo "<tr><td align=center bgcolor=#999999 colspan=7><font size=6 face='courier new'><b>PROMOTORA MEDICA LAS AMERICAS S.A.</font></b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc colspan=7><font size=4 face='courier new'><b>ASAMBLEA ORDINARIA DE SOCIOS</b></font></font></td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc colspan=7><font size=4 face='courier new'><b>Fecha : ".date("d-m-Y")."</b></font></font></td></tr>";
			echo "<tr><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>CEDULA</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>NOMBRE</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ACCIONES<br>PREF./94</b></font></td><td align=center bgcolor=#dddddd ><font face='courier new' size=2><b>ACCIONES<br>PREF./96</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ACCIONES<br>ORDINARIAS</b></font><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>TOTAL<BR>ACCIONES</b></font></td><td align=center bgcolor=#dddddd><font face='courier new' size=2><b>ASISTIO</b></font></td></tr>";
		}*/
	}
	echo "</table><br><br>";
	echo "<table border=1 align=center>";
	$cu = $ac / $soc * 100;
	echo "<tr><td bgcolor=#999999 colspan=1><font face='courier new' size=2><b>TOTAL ACCIONISTAS : ".number_format((double)$count,0,'.',',')."</b></font></td><td bgcolor=#999999 colspan=2><font face='courier new' size=2><b>TOTAL ACCIONISTAS ASISTENTES : ".number_format((double)$as,0,'.',',')."</b></font></td><td bgcolor=#999999 colspan=1><font face='courier new' size=2><b>TOTAL ACCIONES : ".number_format((double)$soc,0,'.',',')."</b></font></td><td bgcolor=#999999 colspan=1><font face='courier new' size=2><b>TOTAL ACCIONES PRESENTES : ".number_format((double)$ac,0,'.',',')."</b></font></td><td bgcolor=#999999 colspan=2><font face='courier new' size=2><b>CUORUM : ".number_format((double)$cu,2,'.',',')."</b></font></td></tr>";	
	echo"</table>";
	
	odbc_close($conex_o);
	odbc_close_all();
}
?>
</body>
</html>