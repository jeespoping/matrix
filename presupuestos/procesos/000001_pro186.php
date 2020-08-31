<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Costos x Actividad Promedio Ponderado Variable (CP)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro186.php Ver. 2016-05-19</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_pro186.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wcco1) or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE COSTOS X ACTIVIDAD PROMEDIO PONDERADO VARIABLE (CP)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
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
			$wemp = substr($wemp,0,2);
			$query = "SELECT ciccco from ".$empresa."_000131  ";
			$query = $query."  where cicano = ".$wanop;
			$query = $query."    and cicemp = '".$wemp."'";
			$query = $query."    and cicmes = ".$wper1;
			$query = $query."    and ciccco between '".$wcco1."' and '".$wcco2."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num == 0)
			{
				$CAN=array();
				$query  = "SELECT Mdpcco,lpad(Mdpsub,5,'0'),SUM(Mdpcan) from costosyp_000157,costosyp_000154 ";
				$query .= "   where Mdpano = ".$wanop;
				$query .= "     and Mdpemp = '".$wemp."'";
				$query .= " 	and Mdpmes between 1 and ".$wper1;
				$query .= " 	and Mdpemp = Cxpemp";
				$query .= " 	and Mdpano = Cxpano";
				$query .= " 	and Mdpmes = Cxpmes";
				$query .= " 	and Mdpcco = Cxpcco";
				$query .= " 	and Mdpsub = Cxpsub";
				$query .= " 	and Cxpest = 'on' "; 
				$query .= " GROUP by 1,2 ";
				$query .= " ORDER by 1,2 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$CAN[$i][0]=$row[0].$row[1];
						$CAN[$i][1]=$row[2];
					}
				}
				$numcan=$num;
				
				//                 0       1                   2                      
				$query  = "SELECT Cxpcco,Cxpsub,sum(Gasval * (Rvbpor / 100)) from ".$empresa."_000154,".$empresa."_000087,".$empresa."_000151 ";
				$query .= "  where Cxpano = ".$wanop;
				$query .= "    and Cxpemp = '".$wemp."'";
				$query .= "	   and Cxpmes between 1 and ".$wper1;
				$query .= "	   and Cxpest = 'on' ";
				$query .= "	   and Cxpcco between '".$wcco1."' and '".$wcco2."'";
				$query .= "	   and Cxpemp = Gasemp ";
				$query .= "	   and Cxpano = Gasano ";
				$query .= "	   and Cxpmes = Gasmes ";
				$query .= "	   and Cxpcco = Gascco ";
				$query .= "	   and Cxpsub = Gassub ";
				$query .= "	   and Gasemp = Rvbemp ";
				$query .= "	   and Gasano = Rvbano ";
				$query .= "	   and Gasmes = Rvbmes ";
				$query .= "	   and Gascco = Rvbcco ";
				$query .= "	   and Gasgas = Rvbcod ";
				$query .= "  group by Cxpcco,Cxpsub ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wsub = $row[1];
					while(strlen($wsub) < 5) $wsub = "0".$wsub;
					$pos=bi($CAN,$numcan,$row[0].$wsub);
					if($pos != -1)
						$wcant = $row[2]/$CAN[$pos][1];
					else
						$wcant = 0;
					$query = "update ".$empresa."_000154 set Cxpcvp = ".$wcant." where Cxpano=".$wanop." and Cxpmes=".$wper1." and Cxpcco='".$row[0]."' and Cxpsub='".$row[1]."' and Cxpemp='".$wemp."' ";
					$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$count++;
					echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
				}
				echo "<b>TOTAL REGISTROS ACTUALIZADOS : ".$count."</b>";
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
