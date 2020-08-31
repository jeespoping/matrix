<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo Depreciacion y Amortizacion Nuevas Inversiones</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro118.php Ver. 2017-12-14</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro118.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DEPRECIACION Y AMORTIZACION NUEVAS INVERSIONES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Inicio de Presupuestacion</td>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$k=0;
			$query = "delete from ".$empresa."_000121 ";
			$query = $query."  where Dppano >= ".$wanop;
			$query = $query."    and Dppemp = '".$wemp."' ";
			$query = $query."      and  Dpptip = 'N' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT Invcco, Invact, Invmon,Invano,Invmes,Invvid,Invtip,Invest  from ".$empresa."_000019 ";
			$query = $query."  where Invano = ".$wanop;
			$query = $query."    and Invemp = '".$wemp."' ";
			$query = $query."    and (Invest = 'D' ";
			$query = $query."     or  Invest = 'A') ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wper=$row[4];
				$dep=$row[2] / $row[5];
				$wanop1=$wanop;
				$d=0;
				for ($j=0;$j<$row[5];$j++)
				{
					$wper++;
					if($wper > 12)
					{
						$wper=1;
						$wanop1++;
					}
					$d++;
					$depacu= $dep;
					if($row[5] >= $d)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000121 (medico,fecha_data,hora_data,Dppemp, Dppano, Dppmes, Dppcco, Dppact, Dppvde, Dpptip, Dppest,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop1.",".$wper.",'".$row[0]."','".$row[1]."',".$depacu.",'N','".$row[7]."','C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
							$k++;
							echo "REGISTRO INSERTADO  : ".$k."<br>";
						}
					}
					$dep=$depacu;
				}
			}
			echo "<B>TOTAL REGISTROS ACTUALIZADOS : ".$k."</B><br>";
		}
	}
?>
</body>
</html>
