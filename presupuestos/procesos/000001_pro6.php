<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Resumen de Gastos Presupuestados</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro6.php Ver. 1.00</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro6.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION RESUMEN DE GASTOS PRESUPUESTADOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$count=0;
			$query = "delete from ".$empresa."_000043 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resind = '6'";
			$err = mysql_query($query,$conex);
			$query = "SELECT Gascco, Gascod, Gasano, Gasmes,sum(Gasval)  from ".$empresa."_000012 ";
			$query = $query."  where gasano = ".$wanop;
			$query = $query."  group by Gascco, Gascod, Gasano, Gasmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$row[0]."','".$row[1]."',".$row[2].",".$row[3].",".$row[4].",'6','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
    		}
			echo "REGISTROS INSERTADOS : ".$count;
		}
	}
?>
</body>
</html>
