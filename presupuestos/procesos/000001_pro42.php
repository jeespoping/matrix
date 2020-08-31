<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Replicacion de Tarifas Servicios (Centros Servicios)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro42.php Ver. 2015-11-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro42.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wanop2) or !isset($wper2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>REPLICACION DE TARIFAS SERVICIOS (CENTROS SERVICIOS)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$count=0;
			$query = "delete from ".$empresa."_000071 ";
			$query = $query."  where tasano = ".$wanop2;
			$query = $query."    and tasmes = ".$wper2;
			$query = $query."    and tasemp = '".$wemp."' ";
			$query = $query."    and tastip = 'R' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT tasano,tasmes,tascod,tasmon,tastip,tasusu from ".$empresa."_000071 ";
			$query = $query."  where tasano = ".$wanop1;
			$query = $query."    and tasmes = ".$wper1;
			$query = $query."    and tasemp = '".$wemp."' ";
			$query = $query."    and tastip = 'R' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000071 (medico,fecha_data,hora_data,tasemp,tasano,tasmes,tascod,tasmon,tastip,tasusu,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop2.",".$wper2.",'".$row[2]."',".$row[3].",'".$row[4]."','".$row[5]."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				$count++;
				echo "REGISTRO INSERTADO NRO : ".$count."<br>";
    		}
			echo "TOTAL REGISTROS INSERTADOS : ".$count;
		}
	}
?>
</body>
</html>
