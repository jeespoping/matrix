<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Control de Porcentajes de Distribucion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc139.php Ver. 2016-03-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc139.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanop) or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>CONTROL DE PORCENTAJES DE DISTRIBUCION</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
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
			$wcco2=strtolower ($wcco2);
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=4 bgcolor=#DDDDDD><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center colspan=4 bgcolor=#DDDDDD><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center colspan=4 bgcolor=#DDDDDD><b>CONTROL DE PORCENTAJES DE DISTRIBUCION</b></td></tr>";
			echo "<tr><td align=center colspan=4 bgcolor=#DDDDDD>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=4 align=center bgcolor=#DDDDDD>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=4 align=center bgcolor=#DDDDDD>CC INICIAL  : ".$wcco1. " CC FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>CENTRO DE <BR>COSTOS</b></td><td bgcolor=#cccccc><b>GASTO</b></td><td bgcolor=#cccccc><b>SUBGASTO</b></td><td bgcolor=#cccccc><b>PORCENTAJE</b></td></tr>";
			//                  0       1       2          3
 			$query  = "select rcdcco, Rcdgas, Rcdsga, sum(Rcdpor) as k from ".$empresa."_000101 ";
			$query .= " where rcdano = ".$wanop; 
			$query .= "   and rcdemp = '".$wemp."' ";
			$query .= "   and rcdmes = ".$wper1;
			$query .= "   and rcdcco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "  group by rcdcco,rcdgas,rcdsga  ";
			$query .= "  order by rcdcco,Rcdgas,Rcdsga ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$k=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[3] != 100)
				{
					if($k % 2 == 0)
						$color = "#99CCFF";
					else
						$color = "#FFFFFF";
					$k++;
					echo "<tr><td bgcolor=".$color."> ".$row[0]."</td><td bgcolor=".$color."> ".$row[1]."</td><td bgcolor=".$color."> ".$row[2]."</td><td bgcolor=".$color."> ".$row[3]."</td></tr>";
				}
			}
		}
}
?>
</body>
</html>
