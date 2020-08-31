<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.rc61.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Costos Apoyo Acumulados Recibidos x Una Unidad (T73)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc61.php Ver. 2018-02-22</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc61' action='000001_rc61.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or !isset($wtip) or (strtoupper ($wtip) != "R" and strtoupper ($wtip) != "P") or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>COSTOS APOYO ACUMULADOS RECIBIDOS X UNA UNIDAD (T73)</td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
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
				echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153,".$empresa."_000125 where empleado = '".$key."' and empresa = Empcod group by 1 order by Empcod";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wemp' OnChange='enter()'>";
					echo "<option>Seleccione</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wemp) and substr($wemp,0,strpos($wemp,"-")) == $row[0])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIC"))
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
				if(isset($wemp) and $wemp != "Seleccione")
				{
					$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod and Empresa = '".substr($wemp,0,strpos($wemp,"-"))."' order by Cc";
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
				}
				echo "</td></tr>";
			}
			echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<tr><td bgcolor=#cccccc align=center>Real o Presupuestado ? (R/P)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$wtip=strtoupper ($wtip);
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and emp = '".$wemp."'"; 
			$query = $query."    and mes =   ".$wper2;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or $wtip == "P" or $key=="costosyp" or (isset($call) and $call == "SIC"))
			{
				$wtotg=0;
				$wtotp=0;
				echo "<center><table border=1>";
				echo "<tr><td align=center colspan=6><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
				echo "<tr><td align=center colspan=6><b>APLICACION DE PRESUPUESTOS</b></td></tr>";
				echo "<tr><td align=center colspan=6><b>COSTOS APOYO ACUMULADOS RECIBIDOS X UNA UNIDAD (T73)</b></td></tr>";
				echo "<tr><td align=center colspan=6><b>EMPRESA : ".$wempt."</b></td></tr>";
				echo "<tr><td colspan=6 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
				echo "<tr><td colspan=3 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
				if($wtip == "R")
					echo "<tr><td colspan=6 align=center>CENTROS DE SERVICIO REALES</td></tr>";
				else
					echo "<tr><td colspan=6 align=center>CENTROS DE SERVICIO PRESUPUESTADOS</td></tr>";
				echo "<tr><td><b>UNIDAD ORIGEN</b></td><td><b>DESCRIPCION</b></td><td><b>VALOR</b></td></tr>";
				$query = "SELECT protic,procco,cconom,sum(promon)  from ".$empresa."_000073,".$empresa."_000005 ";
				$query = $query." where proano = ".$wanop;
				$query = $query."   and proemp = '".$wemp."'";
				$query = $query."   and promes between ".$wper1." and ".$wper2;
				$query = $query."   and proccd between '".$wcco1."' and '".$wcco2."'";
				$query = $query."   and protip = '".$wtip."'";
				$query = $query."   and proemp= ccoemp";
				$query = $query."   and procco= ccocod";
				$query = $query."  group by protic,procco,cconom ";
				$query = $query."  order by protic,procco,cconom ";
				//echo $query."<br>";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$wtic = "D";
				if ($num1>0)
				{
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						 if($wtic != $row1[0])
       					 {
	       					$wtic=$row1[0];
	       					echo "<tr>";
       						echo "<td bgcolor=#99CCFF>&nbsp</td>";
       						echo "<td bgcolor=#99CCFF><B>TOTAL CENTROS DE SERVICIO DISTRIBUIBLES</B></td>";
       					 	echo "<td bgcolor=#99CCFF align=right><B>".number_format($wtotp,2,'.',',')."</B></td></tr>";
       					 	$wtotp=0;
   					 	}
						$wtotg=$wtotg+$row1[3];
						$wtotp=$wtotp+$row1[3];
						 echo "<tr>";
       					 echo "<td>".$row1[1]."</td>";
       					 echo "<td>".$row1[2]."</td>";
       					 echo "<td align=right>".number_format($row1[3],2,'.',',')."</td></tr>";
					}
					echo "<tr>";
       				echo "<td bgcolor=#FFCC66>&nbsp</td>";
       				echo "<td bgcolor=#FFCC66><B>TOTAL CENTROS DE SERVICIO VARIABLES</B></td>";
       				echo "<td  bgcolor=#FFCC66 align=right><B>".number_format($wtotp,2,'.',',')."</B></td></tr>";
					echo "<tr>";
       				echo "<td bgcolor=#cccccc>&nbsp</td>";
       				echo "<td bgcolor=#cccccc><B>TOTALES</B></td>";
       				echo "<td align=right bgcolor=#cccccc><B>".number_format($wtotg,2,'.',',')."</B></td></tr>";
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
