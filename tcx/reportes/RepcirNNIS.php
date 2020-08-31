<html> 
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Cirugias Por Codigo NNIS Entre Fechas</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> RepcirNNIS.php Ver. 2009-09-09</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='RepcirNNIS.php' method=post>";
	if(!isset($v0) or !isset($v1))
	{
	echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>CIRUGIAS POR CODIGO NNIS ENTRE FECHAS</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Incial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query  = " select Cirnns,count(*) from tcx_000008,tcx_000002 ";
		$query .= " where Mcifec between '".$v0."' and '".$v1."' ";
		$query .= "   and Mcicod = Circod ";
		$query .= "   Group by 1 desc ";
		$query .= "   order by 2 desc ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<table border=1>";
		echo "<tr><td colspan=14 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>CIRUGIAS POR CODIGO NNIS ENTRE FECHAS</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>X FECHAS - ENTRE : ".$v0." y ".$v1."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>Codigo<BR>NNIS</b></td>";
		echo "<td bgcolor=#cccccc align=right><b>Cantidad</b></td>";
		echo "</tr>";  
		$TOT=0;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<tr>";
			echo "<td>".$row[0]."</td>";
			echo "<td align=right>".number_format((double)$row[1],0,'.',',')."</td>";
			$TOT += $row[1];
			echo "</tr>"; 
		}
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>TOTAL CIRUGIAS</b></td>";
		echo "<td bgcolor=#cccccc align=right><b>".number_format((double)$TOT,0,'.',',')."</b></td>";
		echo "</tr>"; 
		echo "</table>"; 
	}
}
?>
</body>
</html>
