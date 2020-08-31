<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Costos Promedios Historicos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro120.php Ver. 2015-11-17</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro120.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE COSTOS PROMEDIOS HISTORICOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			$k=0;
			$wanopa = $wanop - 1;
			$query = "delete from ".$empresa."_000122 ";
			$query = $query."  where Gahano = ".$wanop;
			$query = $query."    and Gahemp = '".$wemp."' ";
			$query = $query."    and Gahtip = '0' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT Icgano, Meccco, Icgcpr,sum(Mecval)  from ".$empresa."_000046,".$empresa."_000026 ";
			$query = $query."  where Icgano = ".$wanop;
			$query = $query."    and Icgemp = '".$wemp."' ";
			$query = $query."    and Icgtip = 'N' ";
			$query = $query."    and Icgcpr = Meccpr ";
			$query = $query."    and Icgemp = Mecemp ";
			$query = $query."    and Mecano =".$wanopa;
			$query = $query."    and Mecmes between ".$wper1." and ".$wper2;
			$query = $query."  group by Icgano, Meccco, Icgcpr ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT count(*)  from ".$empresa."_000112 ";
				$query = $query."  where Mcvano = ".$wanopa;
				$query = $query."    and Mcvemp = '".$wemp."' ";
				$query = $query."    and Mcvcco = '".$row[1]."'";
				$query = $query."    and Mcvcpr = '".$row[2]."'";
				$err1 = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
				$row1 = mysql_fetch_array($err1);
				if($row1[0] == 0)
				{
					$wval = $row[3] / ($wper2 - $wper1 + 1);
					for ($j=1;$j<=12;$j++)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
		        		$query = "insert ".$empresa."_000122 (medico,fecha_data,hora_data,Gahemp, Gahano, Gahmes, Gahcco, Gahcpr, Gahval, Gahdes, Gahtip, Gahprg, Gahccr, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[0].",".$j.",'".$row[1]."','".$row[2]."',".$wval.",'','0','0','0','C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
		           			$k++;
		           			echo "REGISTRO INSERTADO  : ".$k."<br>";
						}
					}
				}
			}
			echo "<B>TOTAL REGISTROS ACTUALIZADOS : ".$k."</B>";
		}
	}
?>
</body>
</html>
