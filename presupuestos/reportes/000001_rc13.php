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
		document.forms.rc13.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Nomina Comparativa Entre A&ntilde;os X Cargo y Concepto</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc13.php Ver. 2017-06-21</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name= 'rc13' action='000001_rc13.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or (!isset($wcco1) and !isset($wccof)) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>NOMINA COMPARATIVA ENTRE A&Ntilde;OS X CARGO Y CONCEPTO</td></tr>";
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
				echo "<tr><td bgcolor=#cccccc align=center>Unidad de Proceso</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
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
			$query = "SELECT cierre_real from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
			$wanopa=$wanop -1;
			$query = "SELECT norcar,norcod,carnom,connom, sum(norhor) ,sum(normon)  from ".$empresa."_000036,".$empresa."_000004,".$empresa."_000008 ";
			$query = $query."  where norano = ".$wanopa;
			$query = $query."    and norfil = '".$wemp."' ";
			$query = $query."    and norcco = '".$wcco1."'";
			$query = $query."    and norper between ".$wper1." and ".$wper2;
			$query = $query."    and norcod = concod ";
			$query = $query."    and norfil = conemp ";
			$query = $query."    and norcar = carcod ";
			$query = $query."    and norfil = caremp ";
			$query = $query." group by norcar,norcod,carnom,connom ";
			$query = $query." order by norcar,norcod,carnom,connom ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT norcar,norcod,carnom,connom, sum(norhor) ,sum(normon)  from ".$empresa."_000036,".$empresa."_000004,".$empresa."_000008 ";
			$query = $query."  where norano = ".$wanop;
			$query = $query."    and norfil = '".$wemp."' ";
			$query = $query."    and norcco = '".$wcco1."'";
			$query = $query."    and norper between ".$wper1." and ".$wper2;
			$query = $query."    and norcod = concod ";
			$query = $query."    and norfil = conemp ";
			$query = $query."    and norcar = carcod ";
			$query = $query."    and norfil = caremp ";
			$query = $query." group by norcar,norcod,carnom,connom ";
			$query = $query." order by norcar,norcod,carnom,connom ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>NOMINA COMPARATIVA ENTRE A&Ntilde;OS X CARGO Y CONCEPTO</td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD  : ".$wcco1. "</td></tr>";
			echo "<tr><td colspan=8 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>CARGO</b></td><td><b>NOMBRE CONCEPTO</b></td><td align=right><b>HORAS PAGADAS : ".$wanopa."</b></td><td align=right><b>HORAS PAGADAS : ".$wanop."</b></td><td align=right><b>DIFERENCIA</b></td><td align=right><b>VALOR PAGADO : ".$wanopa."</b></td><td align=right><b>VALOR PAGADO : ".$wanop."</b></td><td align=right><b>DIFERENCIA</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='99999999';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$cargo=$row1[2];
				for ($i=0;$i<=(4-strlen($row1[0]));$i++)
					$row1[0]=$row1[0]."0";
				$key1=$row1[0].$row1[1];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='99999999';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				if (!isset($cargo))
					$cargo=$row2[2];
				for ($i=0;$i<=(4-strlen($row2[0]));$i++)
					$row2[0]=$row2[0]."0";
				$key2=$row2[0].$row2[1];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($key1 == $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[2];
					$wdata[$num][1]=$row1[3];
					$wdata[$num][2]=$row1[4];
					$wdata[$num][3]=$row2[4];
					$wdata[$num][4]=$wdata[$num][3] - $wdata[$num][2];
					$wdata[$num][5]=$row1[5];
					$wdata[$num][6]=$row2[5];
					$wdata[$num][7]=$wdata[$num][6] - $wdata[$num][5];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="99999999";
					else
					{
						$row1 = mysql_fetch_array($err1);
						for ($i=0;$i<=(4-strlen($row1[0]));$i++)
							$row1[0]=$row1[0]."0";
						$key1=$row1[0].$row1[1];
					}
					if($k2 > $num2)
						$key2="99999999";
					else
					{
						$row2 = mysql_fetch_array($err2);
						for ($i=0;$i<=(4-strlen($row2[0]));$i++)
							$row2[0]=$row2[0]."0";
						$key2=$row2[0].$row2[1];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[2];
					$wdata[$num][1]=$row1[3];
					$wdata[$num][2]=$row1[4];
					$wdata[$num][3]=0;
					$wdata[$num][4]=$wdata[$num][3] - $wdata[$num][2];
					$wdata[$num][5]=$row1[5];
					$wdata[$num][6]=0;
					$wdata[$num][7]=$wdata[$num][6] - $wdata[$num][5];
					$k1++;
					if($k1 > $num1)
						$key1="99999999";
					else
					{
						$row1 = mysql_fetch_array($err1);
						for ($i=0;$i<=(4-strlen($row1[0]));$i++)
							$row1[0]=$row1[0]."0";
						$key1=$row1[0].$row1[1];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[2];
					$wdata[$num][1]=$row2[3];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row2[4];
					$wdata[$num][4]=$wdata[$num][3] - $wdata[$num][2];
					$wdata[$num][5]=0;
					$wdata[$num][6]=$row2[5];
					$wdata[$num][7]=$wdata[$num][6] - $wdata[$num][5];
					$k2++;
					if($k2 > $num2)
						$key2="99999999";
					else
					{
						$row2 = mysql_fetch_array($err2);
						for ($i=0;$i<=(4-strlen($row2[0]));$i++)
							$row2[0]=$row2[0]."0";
						$key2=$row2[0].$row2[1];
					}
				}
			}
			$cargo=$wdata[0][0];
			for ($i=0;$i<=$num;$i++)
			{
				if ($cargo == $wdata[$i][0] and $i > 0)
					$nom ="-";
				else
				{
					$cargo=$wdata[$i][0];
					$nom=$wdata[$i][0];
				}
				echo"<tr><td><b>".$nom."</b></td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],0,'.',',')."</td></tr>";
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
