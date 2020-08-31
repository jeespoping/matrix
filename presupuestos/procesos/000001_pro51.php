<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Seleccion de Montos Presupuestados Centros de Servicios a Distribuir</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro51.php Ver. 2016-05-26</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro51.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>SELECCION DE MONTOS PRESUPUESTADOS CENTROS DE SERVICIOS A DISTRIBUIR</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
			$count=0;
			$query = "delete from ".$empresa."_000067 ";
			$query = $query."  where madano = ".$wanop;
			$query = $query."    and mademp = '".$wemp."'";
			$query = $query."    and madtip = 'P' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT rescco,sum(resmon) from ".$empresa."_000043,".$empresa."_000005 ";
			$query = $query."  where Resano = ".$wanop;
			$query = $query."    and Resemp = '".$wemp."'";
			$query = $query."    and Rescpr >= '200' ";
			$query = $query."    and Rescpr <= '299' ";
			$query = $query."    and Rescpr != '298' ";
			$query = $query."    and Resemp = ccoemp";
			$query = $query."    and Rescco = ccocod";
			$query = $query."    and ccocse = 'D' ";
			$query = $query."   group by rescco ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$wmonto=$row[1] / 12;
        		$query = "insert ".$empresa."_000067 (medico,fecha_data,hora_data,mademp,madano,madmes,madcco,madmon,madtip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",1,'".$row[0]."',".$wmonto.",'P','C-".$empresa."')";
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
