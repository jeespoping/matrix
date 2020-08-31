<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo Costo de Unidosis</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro198.php Ver. 2017-07-13</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			//echo strtoupper($k)." ".strtoupper($d[$lm][0])."<br>";
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro198.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or !isset($wccoi) or !isset($wccof) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO COSTO DE UNIDOSIS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
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
			$wemp = substr($wemp,0,2);
			$query = "delete from ".$empresa."_000097 ";
			$query = $query."  where Pcaano = ".$wanop;
			$query = $query."    and Pcaemp = '".$wemp."'";
			$query = $query."    and Pcacco between '".$wccoi."' and '".$wccof."'";
			$query = $query."    and Pcames = ".$wper1;
			$query = $query."    and Pcatip = 'U' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			
			$UNI=array();
			$query = "select Rluprl, Rlupru, Rlugru, sum(Rlucan * Estcan)";
 			$query = $query." from ".$empresa."_000162,".$empresa."_000141  ";
 			$query = $query." where Estemp = '".$wemp."'";
 			$query = $query."   and Estano = ".$wanop;
			$query = $query."   and Estmes = ".$wper1;
			$query = $query."   and Estcco between '".$wccoi."' and '".$wccof."'";
			$query = $query."   and Estcco = Rluccd  ";
			$query = $query."   and Estcod = Rluprd  ";
			$query = $query."   and Estcon = Rlucod  ";
			$query = $query."   Group by 1 ";
			$query = $query."   Order by 1 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$UNI[$i][0]=$row[0];
					$UNI[$i][1]=$row[1];
					$UNI[$i][2]=$row[2];
					$UNI[$i][3]=$row[3];
				}
			}
			$numuni=$num;
			
			$count = 0;
			//                  0      1        2      3              4      
			$query = "select Pcacco, Pcacod, Pcagru, Pcacon, sum(Pcactp * Estcan) ";
			$query = $query." from ".$empresa."_000097,".$empresa."_000141  ";
			$query = $query." where Pcaano = ".$wanop;
			$query = $query."   and Pcaemp = '".$wemp."'";
			$query = $query."   and Pcames = ".$wper1;
			$query = $query."   and Pcacco between '".$wccoi."' and '".$wccof."'";
			$query = $query."   and Pcaemp = Estemp ";
			$query = $query."   and Pcaano = Estano ";
			$query = $query."   and Pcames = Estmes ";
			$query = $query."   and Pcacco = Estcco ";
			$query = $query."   and Pcacod = Estcod ";
			$query = $query."   and Pcacon = Estcon ";
			$query = $query."   Group by 1,2,3,4 ";
			$query = $query."   Order by 1,2,3,4 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$pos=bi($UNI,$numuni,$row[1]);
				if($pos != -1)
				{
					$wcosxuni = $row[4] / $UNI[$pos][3];
					echo "Codigo : ".$row[1]." Numerador : ".$row[4]." Denominador : ".$UNI[$pos][3]."<br>";
					$wcoduni = $UNI[$pos][1];
					$wcodgru = $UNI[$pos][2];
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000097 (medico,fecha_data,hora_data,Pcaemp, Pcaano, Pcames, Pcacco, Pcacod, Pcagru, Pcacon, Pcapor, Pcactp, Pcatmn, Pcapro, Pcatip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$wcoduni."','".$wcodgru."','".$row[3]."',0,".$wcosxuni.",".$wcosxuni.",0,'U','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$count++;
					echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
				}
			}
			echo "TOTAL REGISTROS ACTUALIZADOS : ".$count;
		}
	}
?>
</body>
</html>
