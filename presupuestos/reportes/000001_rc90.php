<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte de Control Indirectos Reales</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc90.php Ver. 2016-05-27</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_rc90.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>REPORTE DE CONTROL INDIRECTOS REALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
			$query = "select Mircri  from ".$empresa."_000058 ";
			$query = $query."  where Mirano  = ".$wanop;
			$query = $query."    and Miremp = '".$wemp."'";
			$query = $query."    and Mirmes  = ".$wper1;
			$query = $query."  group by  Mircri";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center>REPORTE DE CONTROL INDIRECTOS REALES</td></tr>";
			echo "<tr><td colspan=16 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=16 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>CRITERIO</b></td><td><b>DESCRIPCION</b></td><td><b>INCONSISTENCIA</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "select   count(*)  from ".$empresa."_000053 ";
				$query = $query."     where Mcrcri = '".$row[0]."'";
				$query = $query."       and Mcremp = '".$wemp."'";
				$query = $query."       and Mcrano  = ".$wanop;
				$query = $query."       and Mcrmes  = ".$wper1;
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);
				if($row1[0] == 0)
				{
					$query = "select   Crides  from ".$empresa."_000051 ";
					$query = $query."  where Cricod =  '".$row[0]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);
					if($num1 > 0)
						echo "<tr><td>".$row[0]."</td><td>".$row1[0]."</td><td>CRITERIO SIN MOVIMIENTO</td></tr>";
					else
						echo "<tr><td>".$row[0]."</td><td>&nbsp</td><td>CRITERIO SIN MOVIMIENTO Y NO MATRICULADO EN MAESTRO</td></tr>";
				}
			}
		}
	}
?>
</body>
</html>
