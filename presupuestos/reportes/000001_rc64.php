<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de  Analisis Estado de Resultados</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc64.php Ver. 2016-02-05</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc64.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE  ANALISIS ESTADO DE RESULTADOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIC") or (isset($call) and $call == "SIG"))
			{
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
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$query = "SELECT  Aefcco,cconom, Aeffec, Aefdet   from ".$empresa."_000078,".$empresa."_000005 ";
			$query = $query."  where Aefano = ".$wanop;
			$query = $query."    and Aefemp = '".$wemp."'";
			$query = $query."    and Aefcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and Aefcco = ccocod ";
			$query = $query."    and Aefemp = ccoemp ";
			$query = $query."   order by Aefcco ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<center><table border=0>";
				echo "<tr><td colspan=3 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=3 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=3 align=center>INFORME DE  ANALISIS ESTADO DE RESULTADOS</td></tr>";
				echo "<tr><td colspan=3 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr></TABLE>";
				echo "<tr><td colspan=3 align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<center><table border=0><tr><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>NOMBRE</b></td><td bgcolor=#cccccc><b>ACTUALIZADO A</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc>".$row[0]."</td><td bgcolor=#cccccc>".$row[1]."</td><td bgcolor=#cccccc>".$row[2]."</td></tr>";
				echo "<tr><td colspan=3>".$row[3]."</td></tr></TABLE><br><br>";
				echo "<div style='page-break-before: always'>";
			}
		}
	}
?>
</body>
</html>
