<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Composicion Costos de Apoyo Variables Recibidos x Una Unidad (T72)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc70.php Ver. 2018-02-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return -1;
	elseif ($vec1[0] < $vec2[0])
				return 1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc70.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione"  or (!isset($wcco1) and !isset($wccof)) or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "P")  or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COMPOSICION COSTOS DE APOYO VARIABLES RECIBIDOS X UNA UNIDAD</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and strtoupper ($call) == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wccof'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Informacion (R - Real / P - Presupuestada)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1 value='R'></td></tr>";
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
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
			}
			$wres=strtoupper ($wres);
			$query = "select   Msecco,cconom, Msecod, serdes, Msecan,Tasmon, Msecan*Tasmon  from ".$empresa."_000072,".$empresa."_000071,".$empresa."_000070,".$empresa."_000005 ";
			$query = $query."  where Mseano  = ".$wanop;
			$query = $query."    and Mseemp = '".$wemp."'";
			$query = $query."    and Msemes  = ".$wper1;
			$query = $query."    and Mseccd = '".$wcco1."'";
			$query = $query."    and Msetip = '".$wres."'";
			$query = $query."    and Mseemp = Tasemp ";
			$query = $query."    and Msecod = Tascod ";
			$query = $query."    and Tasano  = ".$wanop;
			$query = $query."    and Tasmes  = ".$wper1;
			$query = $query."    and Tastip = '".$wres."'";
			$query = $query."    and Msecod = Sercod ";
			$query = $query."    and Mseemp = ccoemp ";
			$query = $query."    and Msecco = ccocod ";
			$query = $query."   order by Msecco, Msecod ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center>COMPOSICION COSTOS DE APOYO VARIABLES RECIBIDOS X UNA UNIDAD</td></tr>";
			echo "<tr><td colspan=16 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=16 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>SERVICIO</b></td><td><b>DESCRIPCION</b></td><td align=right><b>CANTIDAD</b></td><td align=right><b>TARIFA</b></td><td align=right><b>TOTAL</b></td></tr>";
			$segn="";
			$wtot=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0] != $segn)
				{
					if($i > 0)
						echo "<tr><td colspan=4 bgcolor=#99CCFF><b>TOTAL CENTRO DE COSTOS</b></td><td align=right bgcolor=#99CCFF><b>".number_format((double)$wtot,2,'.',',')."</b></td></tr>";
					$wtot=0;
					echo "<tr><td colspan=5 bgcolor=#FFCC66><b>".$row[0]."  ".$row[1]."</b></td></tr>";
					$segn=$row[0];
				}
				$wtot+=$row[6];
				echo "<tr><td>".$row[2]."</td><td>".$row[3]."</td><td align=right>".number_format((double)$row[4],2,'.',',')."</td><td align=right>".number_format((double)$row[5],2,'.',',')."</td><td align=right>".number_format((double)$row[6],2,'.',',')."</td></tr>";
			}
			echo "<tr><td colspan=4 bgcolor=#99CCFF><b>TOTAL CENTRO DE COSTOS</b></td><td align=right bgcolor=#99CCFF><b>".number_format((double)$wtot,2,'.',',')."</b></td></tr>";

		}
	}
?>
</body>
</html>
