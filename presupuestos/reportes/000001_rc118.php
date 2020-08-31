<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Novedades de Costos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc118.php Ver. 2016-03-18</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc118.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wper2) or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 or !isset($wcco1) or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE NOVEDADES DE COSTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos Final</td>";
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
			$query = "SELECT Movcco,cconom,Movmes,Movexp from ".$empresa."_000129,".$empresa."_000005 ";
			$query = $query."  where Movano = ".$wanop;
			$query = $query."    and Movemp = '".$wemp."' ";
			$query = $query."    and Movmes between ".$wper1." and ".$wper2;
			$query = $query."    and Movcco between '".$wcco1."' and '".$wcco2."' ";
			$query = $query."    and Movcco = ccocod";
			$query = $query."    and Movemp = ccoemp";
			$query = $query."   order by 1,3 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=0>";
			echo "<tr><td align=center colspan=2 bgcolor=#dddddd><font size=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td align=center colspan=2 bgcolor=#dddddd><font size=2><b>DIRECCION DE INFORMATICA</b></font></td></tr>";
			echo "<tr><td align=center colspan=2 bgcolor=#dddddd><font size=2><b>INFORME DE NOVEDADES DE COSTOS</b></font></td></tr>";
			echo "<tr><td align=center colspan=2 bgcolor=#dddddd>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td align=center colspan=2 bgcolor=#dddddd><font size=2><b>UNIDADES ".$wcco1."-".$wcco2."</b></font></td></tr>";
			echo "<tr><td align=center colspan=2 bgcolor=#dddddd><font size=2><b>PERIODO ".$wper1."-".$wper2."</b></font></td></tr>";
			echo "<tr><td bgcolor=#999999><b>MES</b></td><td bgcolor=#999999 align=center><b>EXPLICACION</b></td></tr>";
			$wccant="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wccant != $row[0])
				{
					echo "<tr><td bgcolor='#6694E3' colspan=2 align=center><font color='#ffffff'><b>".$row[0]."-".$row[1]."</font></b></td></tr>";
					$wccant = $row[0];
				}
				if($i % 2 == 0)
					$color = "#E0ECFF";
				else
					$color = "#E8EEF7";
				echo "<tr><td bgcolor=".$color." align=center>".$row[2]."</td><td bgcolor=".$color.">".$row[3]."</td></tr>";
    		}
    		echo "</table>";
		}
	}
?>
</body>
</html>
