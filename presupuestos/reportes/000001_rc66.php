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
		document.forms.rc66.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ejecucion de Inversiones x Grupos (T77 - T19)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc66.php Ver. 2017-06-21</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc66' action='000001_rc66.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof))  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2 or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D"))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EJECUCION DE INVERSIONES X GRUPOS (T77 - T19)</td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
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
					$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod and Empresa = '".substr($wemp,0,strpos($wemp,"-"))."' group by 1 order by Cc";
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
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
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
			$wres=strtoupper ($wres);
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or $key=="costosyp" or (isset($call) and $call == "SIC"))
			{
			$query = "SELECT  mingru,mginom,mincco,cconom,sum(minmon * minpor / 100)  from ".$empresa."_000077,".$empresa."_000029,".$empresa."_000005 ";
			$query = $query." where minano = ".$wanop;
			$query = $query."   and minemp = '".$wemp."'";
			$query = $query."   and minmes between ".$wper1." and ".$wper2;
			$query = $query."   and mincco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and mingru = mgicod";
			$query = $query."   and mincco= ccocod";
			$query = $query."   and minemp = ccoemp ";
			$query = $query."   group by mingru,mginom,mincco,cconom ";
			$query = $query."   order by mingru,mincco ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT  invcod,mginom,invcco,cconom,sum(invmon)  from ".$empresa."_000019,".$empresa."_000029,".$empresa."_000005 ";
			$query = $query." where invano = ".$wanop;
			$query = $query."   and invemp = '".$wemp."'";
			$query = $query."   and invmes between ".$wper1." and ".$wper2;
			$query = $query."   and invcco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and invcod = mgicod ";
			$query = $query."   and invcco= ccocod ";
			$query = $query."   and invemp = ccoemp ";
			$query = $query."   group by invcod,mginom,invcco,cconom ";
			$query = $query."   order by invcod,invcco";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>EJECUCION DE INVERSIONES X GRUPOS (T77 - T19)</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td><b>C.C.</b></td><td><b>NOMBRE</b></td><td align=right><b>MONTO REAL</b></td><td align=right><b>MONTO PPTO</b></td><td align=right><b>EJECUCION</b></td><td align=right><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='ZZZZZZ';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0].$row1[2];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='ZZZZZZ';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0].$row2[2];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($key1 == $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=$row1[4];
					$wdata[$num][5]=$row2[4];
					if($row1[4] != 0)
						$wdata[$num][6]=$row2[4]/$row1[4] * 100;
					else
						$wdata[$num][6]=0;
					$wdata[$num][7]=$row2[4] - $row1[4];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="ZZZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[2];
					}
					if($k2 > $num2)
						$key2="ZZZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[2];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=$row1[4];
					$wdata[$num][5]=0;
					$wdata[$num][6]=0;
					$wdata[$num][7]=0 - $row1[4];
					$k1++;
					if($k1 > $num1)
						$key1="ZZZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[2];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=$row2[3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row2[4];
					$wdata[$num][6]=0;
					$wdata[$num][7]=$row2[4] - 0;
					$k2++;
					if($k2 > $num2)
						$key2="ZZZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[2];
					}
				}
			}
			$weje=0;
			$wdif=0;
			$wtotgr=0;
			$wtotgp=0;
			$wtotsr=0;
			$wtotsp=0;
			$grupo="";
			for ($i=0;$i<=$num;$i++)
			{
				if($grupo != $wdata[$i][0])
				{
					if($i > 0)
					{
						if($wtotsp != 0)
							$weje=$wtotsr/$wtotsp * 100;
						else
							$weje=0;
						$wdif= $wtotsr - $wtotsp;
						echo"<tr><td colspan=2 bgcolor=#cccccc><b>TOTAL GRUPO </b></td><td align=right bgcolor=#cccccc><b>".number_format((double)$wtotsr,2,'.',',')."</b></td><td align=right bgcolor=#cccccc><b>".number_format((double)$wtotsp,2,'.',',')."</b></td><td align=right bgcolor=#cccccc><b>".number_format((double)$weje,2,'.',',')." %</b></td><td align=right bgcolor=#cccccc><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
					}
					$grupo=$wdata[$i][0];
					$wtotsr=0;
					$wtotsp=0;
					echo"<tr><td colspan=6  align=center bgcolor=#99CCFF><b>".$wdata[$i][1]."</b></td></tr>";
				}
				$wtotsr=$wtotsr+$wdata[$i][4];
				$wtotsp=$wtotsp+$wdata[$i][5];
				$wtotgr=$wtotgr+$wdata[$i][4];
				$wtotgp=$wtotgp+$wdata[$i][5];
				if($wres == "D")
					echo"<tr><td>".$wdata[$i][2]."</td><td>".$wdata[$i][3]."</td><td align=right>".number_format((double)$wdata[$i][4],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][5],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."</td></tr>";
    		}
    		echo"<tr><td colspan=2 bgcolor=#cccccc><b>TOTAL GRUPO </b></td><td align=right bgcolor=#cccccc><b>".number_format((double)$wtotsr,2,'.',',')."</b></td><td align=right bgcolor=#cccccc><b>".number_format((double)$wtotsp,2,'.',',')."</b></td><td align=right bgcolor=#cccccc><b>".number_format((double)$weje,2,'.',',')." %</b></td><td align=right bgcolor=#cccccc><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
    		if($wtotgp != 0)
				$weje=$wtotgr/$wtotgp * 100;
			else
				$weje=0;
			$wdif= $wtotgr - $wtotgp;
    		echo"<tr><td colspan=2 bgcolor=#FFCC66><b>TOTAL GENERAL </b></td><td align=right bgcolor=#FFCC66><b>".number_format((double)$wtotgr,2,'.',',')."</b></td><td align=right bgcolor=#FFCC66><b>".number_format((double)$wtotgp,2,'.',',')."</b></td><td align=right bgcolor=#FFCC66><b>".number_format((double)$weje,2,'.',',')." %</b></td><td align=right bgcolor=#FFCC66><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
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
