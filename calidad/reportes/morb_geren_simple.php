<html>
<head>
<title>Rep. Auditor</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<?php
include_once("conex.php");
/********************************************************
*           Autor: Ana Maria Betancur					*
*			Fecha de Creación:2005-04-19				*
*
*********************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='' method=post>";
	if(!isset($fecha1))
	{
		if (!isset($fecha1))
		$fecha1="";
		if (!isset($fecha2))
		$fecha2="";
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>CLÍNICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>CONSOLIDADO FINAL DE MORBILIDAD</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha1' size=10 maxlength=10 value='".$fecha1."'> AAAA-MM-DD</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha2' size=10 maxlength=10 value='".$fecha2."'>AAAA-MM-DD</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Numero de diagnosticos:</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='numMax' size=2 maxlength=2 value='".$fecha2."'>AAAA-MM-DD</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<center><table border=1 width='700'>";
		echo "<tr><td colspan=3 align=center><b>CLÍNICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>$numMax MAS FRECUENTES EN EL PERIODO </b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>COMPRENDIDO ENTRE $fecha1 Y $fecha2</b></td></tr>";

		
		$query = "select Count(*) from cominf_000001 where fecha_ing between '$fecha1' and '$fecha2' or Fecha_egr between '$fecha1' and '$fecha2'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0) {
			$row = mysql_fetch_array($err);
			$total=$row[0];
			$query = "select Count(*),Dx_ppal from cominf_000001 where fecha_ing between '$fecha1' and '$fecha2' or Fecha_egr between '$fecha1' and '$fecha2' group by Dx_ppal and order by 1 DESC";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0){
				echo "</table><br/><br/><center><table border=1 width='700'>";
				echo "<tr><td bgcolor='#0055A8'><font color='#ffffff'><b>Diagnostico</td>";
				echo "<td bgcolor='#0055A8'><font color='#ffffff'><b># Pacientes</td>";
				echo "<td bgcolor='#0055A8'><font color='#ffffff'>% Sobre total Pacientes</td></tr>";
				$i=0;
				while (($i<$num) and ($i<$numMax)){
					$row = mysql_fetch_array($err);
					$i++;
					echo "<tr><td><font color='#0055A8'><i>".$row[1]."</i></td>";
					echo "<td><font color='#0055A8'>".$row[1]."</td>";
					echo "<td><font color='#0055A8'>".($row[1]/$total)."</td></tr>";
				}
			}
		}else{
			echo "";
		}
	}
}
include_once("free.php");
?>
</body>
</html>
		
		
		
		
		
		