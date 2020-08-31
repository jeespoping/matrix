<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reporte Control de Inconsistencias Costos de Apoyo (T66-T68)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc68.php Ver. 2018-02-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc68.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wres) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "P")  or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>REPORTE DE CONTROL DE INCONSISTENCIAS COSTOS DE APOYO (T66-T68)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Informacion (R - Real / P - Presupuestada)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
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
			$wres=strtoupper ($wres);
			$query = "select  Cxccco,cconom,sum(Cxcpor) from ".$empresa."_000066,".$empresa."_000005 ";
			$query = $query."  where Cxcano  = ".$wanop;
			$query = $query."    and Cxcemp = '".$wemp."'";
			$query = $query."    and Cxcmes  = ".$wper1;
			$query = $query."    and Cxctip = '".$wres."'";
			$query = $query."    and Cxcemp = ccoemp";
			$query = $query."    and Cxccco = ccocod ";
			$query = $query."  group by  Cxccco,cconom   ";
			$query = $query."  order by Cxccco  ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=3 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=3 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=3 align=center>CENTRO DE SERVICIOS CON SUMATORIA DE CRITERIOS DIFERENTE A 100%</td></tr>";
			echo "<tr><td colspan=3 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=3 align=center><B>PERIODO : ".$wper1. " A&Ntilde;O : ".$wanop."</B></td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>DESCRIPCION</b></td><td align=right><b>SUMATORIA</b></td></TR>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if ($row[2] != 1)
					echo"<tr><td>".$row[0]."</td><td>".$row[1]."</td><td align=right>".number_format((double)$row[2],2,'.',',')."</td></tr>";
			}
			echo "</table><br><br>";
			
			$query = "select   Cxccri   from ".$empresa."_000066 ";
			$query = $query."  where Cxcano  = ".$wanop;
			$query = $query."    and Cxcemp = '".$wemp."'";
			$query = $query."    and Cxcmes  = ".$wper1;
			$query = $query."    and Cxctip = '".$wres."'";
			$query = $query."   group by  Cxccri   ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=3 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=3 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=3 align=center>CENTRO DE SERVICIOS CRITERIOS SIN MOVIMIENTO</td></tr>";
			echo "<tr><td colspan=3 align=center><B>PERIODO : ".$wper1. " A&Ntilde;O : ".$wanop."</B></td></tr>";
			echo "<tr><td><b>CRITERIO</b></td></TR>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "select   count(*)   from ".$empresa."_000068 ";
				$query = $query."  where Mcrano  = ".$wanop;
				$query = $query."    and Mcremp = '".$wemp."'";
				$query = $query."    and Mcrmes  = ".$wper1;
				$query = $query."    and Mcrcri = '".$row[0]."'";
				$query = $query."    and Mcrtip = '".$wres."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);
				if ($row1[0] == 0)
					echo"<tr><td>".$row[0]."</td></tr>";
			}
			echo "</table>";
		}
	}
?>
</body>
</html>
