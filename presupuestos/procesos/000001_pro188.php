<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo Costo x Actividad Variable (CP)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro188.php Ver. 2016-105-20</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro188.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wcco1) or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO COSTO X ACTIVIDAD VARIABLE (CP)</td></tr>";
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
			$wempt = $wemp;
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
				//                 0       1                   2     
				$query  = "SELECT Gascco,Gassub,sum((Gasval * (Rvbpor / 100)) / Mdpcan) from ".$empresa."_000087,".$empresa."_000151,".$empresa."_000157 ";
				$query .= "  where Gasano = ".$wanop;
				$query .= "    and Gasemp = '".$wemp."'";
				$query .= "    and Gasmes = ".$wper1;
				$query .= "    and Gascco between '".$wcco1."' and '".$wcco2."'";
				$query .= "    and Gasemp = Rvbemp ";
				$query .= "    and Gasano = Rvbano ";
				$query .= "    and Gasmes = Rvbmes ";
				$query .= "    and Gascco = Rvbcco ";
				$query .= "    and Gasgas = Rvbcod ";
				$query .= "    and Rvbemp = Mdpemp ";
				$query .= "    and Rvbano = Mdpano ";
				$query .= "    and Rvbmes = Mdpmes ";
				$query .= "    and Rvbcco = Mdpcco ";  
				$query .= "    and Gassub = Mdpsub ";
				$query .= " group by Gascco,Gassub ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query = "update ".$empresa."_000154 set Cxpcvm = ".$row[2]." where Cxpano=".$wanop." and Cxpmes=".$wper1." and Cxpcco='".$row[0]."' and Cxpsub='".$row[1]."' and Cxpsub='".$wemp."' ";
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
