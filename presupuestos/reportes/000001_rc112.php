<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Movimiento de Cuentas (Real)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc112.php Ver. 1.00</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc112.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>MOVIMIENTO DE CUENTAS (REAL)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
				echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or $key=="costosyp" or (isset($call) and $call == "SIC"))
			{
				$query = "SELECT meccpr,mganom,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000028 ";
				$query = $query."  where mecano = ".$wanop;
				$query = $query."    and meccco between '".$wcco1."' and '".$wcco2."'";
				$query = $query."    and mecmes between ".$wper1." and ".$wper2;
				$query = $query."    and meccpr = mgacod ";
				$query = $query."   and mgacla = 'S' ";
				$query = $query."   group by meccpr,mganom";
				$query = $query."   order by meccpr";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				echo "<table border=1>";
				echo "<tr><td colspan=3 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=3 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=3 align=center>MOVIMIENTO DE CUENTAS (REAL)</td></tr>";
				echo "<tr><td colspan=3 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A�O : ".$wanop."</td></tr>";
				echo "<tr><td colspan=3 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
				echo "<tr><td bgcolor=#dddddd><b>CODIGO</b></td><td bgcolor=#dddddd><b>RUBRO</b></td><td align=right bgcolor=#dddddd><b>MONTO</b></td></tr>";
				$wtotal=array();
				$ita=0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if(substr($row[0],0,1) == "1" and $i == 0)
						echo "<tr><td colspan=3 align=center bgcolor=#99CCFF><b>INGRESOS OPERACIONALES</b></td></tr>";
					elseif(substr($row[0],0,1) != "1" and $i <= 1)
									echo "<tr><td colspan=3 align=center bgcolor=#FFCC66><b>COSTOS Y GASTOS OPERACIONALES FIJOS</b></td></tr>";
					echo"<tr><td>".$row[0]."</td><td>".$row[1]."</td><td align=right>".number_format((double)$row[2],0,'.',',')."</td></tr>";
	    		}
			}
	         else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO NO!! ESTA CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
