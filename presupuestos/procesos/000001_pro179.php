<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Costos x Actividad Promedio Ponderado Variable</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro179.php Ver. 2017-06-16</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_pro179.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wano1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wcco1) or !isset($wcco2)  or !isset($wmes1) or $wmes1 < 1 or $wmes1 > 12 or !isset($wano2) or !isset($wmes2) or $wmes2 < 1 or $wmes2 > 12 or !isset($wano3) or !isset($wmes3) or $wmes3 < 1 or $wmes3 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE COSTOS X ACTIVIDAD PROMEDIO PONDERADO VARIABLE</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Grabaci&oacute;n</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano3' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Grabaci&oacute;n</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes3' size=2 maxlength=2></td></tr>";
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
			$query = $query."  where cicano = ".$wano3;
			$query = $query."    and cicemp = '".$wemp."'";
			$query = $query."    and cicmes =  ".$wmes3;
			$query = $query."    and ciccco between '".$wcco1."' and '".$wcco2."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num == 0)
			{
				$CAN=array();
				$query  = "SELECT Mdacco,lpad(Mdasub,5,'0'),SUM(Mdacan) from costosyp_000090,costosyp_000083 ";
				$query .= "   where Mdaano between ".$wano1." and ".$wano2;
				$query .= "     and Mdaemp = '".$wemp."'";
				$query .= " 	and ((Mdames between 1 and 12 and Mdaano != ".$wano1." and Mdaano != ".$wano2.") ";
				$query .= " 	 or (Mdames >= ".$wmes1." and Mdaano = ".$wano1." and ".$wano1." != ".$wano2.") ";
				$query .= " 	 or (Mdames <= ".$wmes2." and Mdaano = ".$wano2." and ".$wano1." != ".$wano2.") "; 
				$query .= " 	 or (Mdames between ".$wmes1." and ".$wmes2." and ".$wano1." = ".$wano2.")) ";
				$query .= " 	and Mdaemp = Cxaemp";
				$query .= " 	and Mdaano = Cxaano";
				$query .= " 	and Mdames = Cxames";
				$query .= " 	and Mdacco = Cxacco";
				$query .= " 	and Mdasub = Cxasub";
				$query .= " 	and Cxaest = 'on' "; 
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
				$query  = "SELECT Cxacco,Cxasub,sum(Gasval * (Rvbpor / 100)) from ".$empresa."_000083,".$empresa."_000087,".$empresa."_000151 ";
				$query .= "  where Cxaano between ".$wano1." and ".$wano2;
				$query .= "    and Cxaemp = '".$wemp."'";
				$query .= "    and ((Cxames between 1 and 12 and Cxaano != ".$wano1." and Cxaano != ".$wano2.") ";
				$query .= " 	or (Cxames >= ".$wmes1." and Cxaano = ".$wano1." and ".$wano1." != ".$wano2.") ";
				$query .= " 	or (Cxames <= ".$wmes2." and Cxaano = ".$wano2." and ".$wano1." != ".$wano2.") "; 
				$query .= " 	or (Cxames between ".$wmes1." and ".$wmes2." and ".$wano1." = ".$wano2.")) ";
				$query .= "	   and Cxaest = 'on' ";
				$query .= "	   and Cxacco between '".$wcco1."' and '".$wcco2."'";
				$query .= "	   and Cxaemp = Gasemp ";
				$query .= "	   and Cxaano = Gasano ";
				$query .= "	   and Cxames = Gasmes ";
				$query .= "	   and Cxacco = Gascco ";
				$query .= "	   and Cxasub = Gassub ";
				$query .= "	   and Gasemp = Rvbemp ";
				$query .= "	   and Gasano = Rvbano ";
				$query .= "	   and Gasmes = Rvbmes ";
				$query .= "	   and Gascco = Rvbcco ";
				$query .= "	   and Gasgas = Rvbcod ";
				$query .= "  group by Cxacco,Cxasub ";
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
					$query = "update ".$empresa."_000083 set Cxacvp = ".$wcant." where Cxaano=".$wano3." and Cxames=".$wmes3." and Cxacco='".$row[0]."' and Cxasub='".$row[1]."' and Cxaemp='".$wemp."'   ";
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
