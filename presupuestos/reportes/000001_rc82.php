<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Inconsistencias en Movimiento de Costos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc82.php Ver. 2016-03-10</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	//$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			//echo " Medio : ".$lm." valor: ".$d[$lm][$i]."<br>";
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][$i]),20);
			//if(strtoupper($k) == strtoupper($d[$lm][$i]))
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
			//echo $k." ".$d[$li][$i]." ".$d[$ls][$i]." ".$d[$lm][$i]." ".$li." ".$ls." ".$lm."<br>";
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc82.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanop) or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>INCONSISTENCIAS EN MOVIMIENTO DE COSTOS</td></tr>";
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
			$ccsub=array();
			$query  = "SELECT Diccco,Dicsub from ".$empresa."_000018 where Dicemp = '".$wemp."' ";
			$query .= "  ORDER BY  Diccco,Dicsub ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$totcs=$num;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$ccsub[$i][0]=$row[0].$row[1];
			}
			$wcco2=strtolower ($wcco2);
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center  colspan=2><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center  colspan=2><b>INCONSISTENCIAS EN MOVIMIENTO DE COSTOS</b></td></tr>";
			echo "<tr><td colspan=2 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=2 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=2 align=center bgcolor=#cccccc>COSTOS NO MATRICULADOS EN MAESTRO</td></tr>";
			$query = "SELECT  Mgagas from ".$empresa."_000092 ";
			$query = $query."  where Mgaano = ".$wanop;
			$query = $query."    and Mgaemp = '".$wemp."' ";
			$query = $query."    and Mgaper = ".$wper1;
			$query = $query."  order by  Mgagas";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT  count(*)  from ".$empresa."_000079 ";
				$query = $query."  where Cogcod = '".$row[0]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);
				if($row1[0] == 0)
					echo "<tr><td colspan=2> ".$row[0]."</td></tr>";
			}
			echo "<tr><td colspan=2 align=center bgcolor=#cccccc>VERIFICACION DE GASTOS Y DRIVERS</td></tr>";
			$query = "SELECT Mgacco, Mgagas, Mgasga from ".$empresa."_000092 ";
			$query = $query."  where Mgaano = ".$wanop;
			$query = $query."    and Mgaemp = '".$wemp."' ";
			$query = $query."    and Mgaper = ".$wper1;
			$query = $query."    and Mgacco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."  order by Mgacco, Mgagas";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "SELECT  Rcddri, Rcdtip  from ".$empresa."_000101 ";
				$query = $query."  where Rcdcco = '".$row[0]."'";
				$query = $query."    and Rcdemp = '".$wemp."' ";
				$query = $query."    and Rcdgas = '".$row[1]."'";
				$query = $query."    and Rcdsga = '".$row[2]."'";
				$query = $query."    and Rcdano = ".$wanop;
				$query = $query."    and Rcdmes = ".$wper1;
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 == 0)
					echo "<tr><td> ".$row[0]."-".$row[1]."-".$row[2]."</td><td> GASTO SIN DRIVER ASOCIADO</td></tr>";
				else
				{
					$row1 = mysql_fetch_array($err1);
					if($row1[1] == "D")
					{
						$query = "SELECT  count(*)  from ".$empresa."_000091 ";
						$query = $query." where Mdrano = ".$wanop;
						$query = $query."   and Mdremp = '".$wemp."' ";
						$query = $query."   and Mdrmes = ".$wper1;
						$query = $query."   and Mdrcco = '".$row[0]."'";
						$query = $query."   and Mdrcod = '".$row1[0]."'";
						$err2 = mysql_query($query,$conex);
						$num2 = mysql_num_rows($err2);
						$row2 = mysql_fetch_array($err2);
						if ($row2[0] == 0)
							echo "<tr><td> ".$row[0]."-".$row[1]."-".$row1[0]."</td><td> DRIVER SIN MOVIMIENTO</td></tr>";
					}
				}
			}
			$query = "SELECT Ifacco, Ifains, Ifatip  from ".$empresa."_000130 ";
			$query = $query."  where Ifatip = 'D' ";
			$query = $query."    and Ifaemp = '".$wemp."' ";
			$query = $query."    and Ifacco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and Ifains not in (select Rcdsga from ".$empresa."_000101 where Rcdano = ".$wanop." and  Rcdmes = ".$wper1." and Rcdcco = Ifacco  and Rcdemp = '".$wemp."' GROUP BY 1) ";
			$query = $query."    and Ifains in (select Almcod from ".$empresa."_000002 where Almano = ".$wanop." and  Almmes = ".$wper1." and Almcco = Ifacco and Almemp = '".$wemp."' GROUP BY 1) ";
			$query = $query."  order by Ifacco ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<tr><td> ".$row[0]."-".$row[1]."-".$row[2]."</td><td>INSUMO SIN DISTRIBUCION</td></tr>";
			}
			echo "<tr><td colspan=2 align=center bgcolor=#cccccc>VALIDACION DE ACTIVIDADES</td></tr>";
			$query = "SELECT Gascco, Gassub from ".$empresa."_000087 ";
			$query = $query."  where Gasano = ".$wanop;
			$query = $query."    and Gasemp = '".$wemp."' ";
			$query = $query."    and Gasmes = ".$wper1;
			$query = $query."    and Gascco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."  group by Gascco, Gassub";
			$query = $query."  order by Gascco, Gassub";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$pos=bi($ccsub,$totcs,$row[0].$row[1],0);
				if($pos == -1)
				{
					echo "<tr><td> ".$row[0]."-".$row[1]."</td><td>SUBPROCESO NO EXISTE EN DICCIONARIO DE ACTIVIDADES</td></tr>";
				}
			}
		}
}
?>
</body>
</html>
