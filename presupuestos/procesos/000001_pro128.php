<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Replicacion de Maestro Drivers de Costos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro128.php Ver. 2018-01-19</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro128.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1) or !isset($wcco2) or !isset($wper1) or !isset($wanop2) or !isset($wper2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>REPLICACION DE MAESTRO DRIVERS DE COSTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query = "SELECT ciccco from ".$empresa."_000131  ";
			$query = $query."  where cicano = ".$wanop2;
			$query = $query."    and cicemp = '".$wemp."'"; 
			$query = $query."    and cicmes =   ".$wper2;
			$query = $query."    and ciccco between '".$wcco1."' and '".$wcco2."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num == 0)
			{
				$count=0;
				$query = "delete from ".$empresa."_000101 ";
				$query = $query."  where Rcdano = ".$wanop2;
				$query = $query."    and Rcdemp = '".$wemp."'"; 
				$query = $query."    and Rcdmes = ".$wper2;
				$query = $query."    and Rcdcco between '".$wcco1."' and '".$wcco2."' ";
				$err = mysql_query($query,$conex);
				//                 0       1       2       3       4       5       6       7
				$query = "SELECT Rcdano, Rcdmes, Rcdcco, Rcdgas, Rcdtip, Rcddri, Rcdpor, Rcdsga  from ".$empresa."_000101 ";
				$query = $query."  where Rcdano = ".$wanop1;
				$query = $query."    and Rcdemp = '".$wemp."'"; 
				$query = $query."    and Rcdmes = ".$wper1;
				$query = $query."    and Rcdcco between '".$wcco1."' and '".$wcco2."' ";
				$query = $query."    and Rcdgas NOT IN ('216','237','316','337')  ";
				//echo $query."<br>";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000101 (medico,fecha_data,hora_data, Rcdemp, Rcdano, Rcdmes, Rcdcco, Rcdgas, Rcdsga, Rcdtip, Rcddri, Rcdpor, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop2.",".$wper2.",'".$row[2]."','".$row[3]."','".$row[7]."','".$row[4]."','".$row[5]."',".$row[6].",'C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					$count++;
					echo "REGISTRO INSERTADO NRO : ".$count."<br>";
				}
				echo "TOTAL REGISTROS INSERTADOS : ".$count;
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL CCO ESTA CERRADO EN ESTE PERIODO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
