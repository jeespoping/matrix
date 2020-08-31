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
		document.forms.rc22.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ejecucion Costos Indirectos x Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc22.php Ver. 2016-08-25</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc22' action='000001_rc22.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof))  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EJECUCION COSTOS INDIRECTOS X UNIDAD</td></tr>";
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
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and emp = '".$wemp."'";
			$query = $query."    and mes =   ".$wper2;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or $key=="costosyp" or (isset($call) and $call == "SIC"))
			{
			$wmeses = $wper2 - $wper1 + 1;
			$query = "SELECT  mdiind,middes,sum(mdimon)  from ".$empresa."_000054,".$empresa."_000050 ";
			$query = $query." where mdiano = ".$wanop;
			$query = $query."   and mdiemp = '".$wemp."'";
			$query = $query."   and mdimes between ".$wper1." and ".$wper2;
			$query = $query."   and mdicco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and mditip = 'R'";
			$query = $query."   and mdiemp= midemp";
			$query = $query."   and mdiind= midcod";
			$query = $query."  group by mdiind,middes";
			$query = $query."  order by mdiind,middes";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT  mdiind,middes,sum(mdimon)  from ".$empresa."_000054,".$empresa."_000050 ";
			$query = $query." where mdiano = ".$wanop;
			$query = $query."   and mdiemp = '".$wemp."'";
			$query = $query."   and mdimes between ".$wper1." and ".$wper2;
			$query = $query."   and mdicco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."   and mditip = 'P'";
			$query = $query."   and mdiemp= midemp";
			$query = $query."   and mdiind= midcod";
			$query = $query."  group by mdiind,middes";
			$query = $query."  order by mdiind,middes";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>EJECUCION COSTOS INDIRECTOS X UNIDAD</td></tr>";
			echo "<tr><td colspan=8 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>INDIRECTO</b></td><td align=right><b>MONTO REAL</b></td><td align=right><b>MONTO PPTO</b></td><td align=right><b>EJECUCION</b></td><td align=right><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='ZZZZZZZZ';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='ZZZZZZZZ';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($key1 == $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[1];
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=$row2[2]/$row1[2] * 100;
					$wdata[$num][4]=$row2[2] - $row1[2];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="ZZZZZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
					if($k2 > $num2)
						$key2="ZZZZZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[1];
					$wdata[$num][1]=$row1[2];
					$wdata[$num][2]=0;
					$wdata[$num][3]=0/$row1[2] * 100;
					$wdata[$num][4]=0 - $row1[2];
					$k1++;
					if($k1 > $num1)
						$key1="ZZZZZZZZ";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[1];
					$wdata[$num][1]=0;
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=$row2[2] - 0;
					$k2++;
					if($k2 > $num2)
						$key2="ZZZZZZZZ";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0];
					}
				}
			}
			$wtotr=0;
			$wtotp=0;
			for ($i=0;$i<=$num;$i++)
			{
				$wtotr=$wtotr+$wdata[$i][1];
				$wtotp=$wtotp+$wdata[$i][2];
				
				echo"<tr><td>".$wdata[$i][0]."</td><td align=right>".number_format((double)$wdata[$i][1],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][2],2,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],2,'.',',')." %</td><td align=right>".number_format((double)$wdata[$i][4],2,'.',',')."</td></tr>";
    		}
    		if($wtotp != 0)
				$weje=$wtotr/$wtotp * 100;
			else
				$weje=0;
			$wdif= $wtotp - $wtotr;
    		echo"<tr><td><B>TOTALES</B></td><td align=right><B>".number_format((double)$wtotr,2,'.',',')."</B></td><td align=right><B>".number_format((double)$wtotp,2,'.',',')."</B></td><td align=right><B>".number_format((double)$weje,2,'.',',')." %</B></td><td align=right><B>".number_format((double)$wdif,2,'.',',')."</B></td></tr>";
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
