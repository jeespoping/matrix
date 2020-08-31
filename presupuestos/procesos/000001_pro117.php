<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Proyeccion Depreciacion y Amortizacion Activos Actuales (T121)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro117.php Ver. 2015-11-06</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro117.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanopb) or !isset($wanop)  or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>PROYECCION DEPRECIACION Y AMORTIZACION ACTIVOS ACTUALES (T121)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Base de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanopb' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Base de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Final de Proyeccion</td>";
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
			$query = $query."  where Dpptip = 'E' ";
			$query = $query."    and Dppemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT Depcco, Depcod, Depvde,Acfaac,Acfmac,Acfvid  from ".$empresa."_000076,".$empresa."_000075 ";
			$query = $query."  where Depano = ".$wanopb;
			$query = $query."    and Depemp = '".$wemp."' ";
			$query = $query."    and Depmes = ".$wper1;
			$query = $query."    and Depcod = Acfcod";
			$query = $query."    and Depemp = Acfemp";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$vur = $row[5] - ((($wanopb * 12) + $wper1) - (($row[3] * 12) + $row[4]));
				$dep=$row[2];
				$wper=$wper1;
				$wano=$wanopb;
				for ($j=1;$j<=$vur;$j++)
				{
					$wper++;
					if($wper > 12)
					{
						$wper=1;
						$wano++;
					}
					if($wano < $wanop + 1)
					{
						if(substr($row[1],0,1) == "3")
							$west = "A";
						else
							$west = "D";
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
		        		$query = "insert ".$empresa."_000121 (medico,fecha_data,hora_data,Dppemp, Dppano, Dppmes, Dppcco, Dppact, Dppvde, Dpptip, Dppest,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wano.",".$wper.",'".$row[0]."','".$row[1]."',".$dep.",'E','".$west."','C-".$empresa."')";
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
