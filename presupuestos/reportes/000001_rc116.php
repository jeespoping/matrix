<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ejecucion Presupuestal de Ingresos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc116.php Ver. 2017-06-21</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc116.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or (!isset($wcco1) and !isset($wccof)) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EJECUCION PRESUPUESTAL DE INGRESOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
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
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod group by 1 order by Cc";
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
			$query = "SELECT cierre_ingresos from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and emp = '".$wemp."'";
			$query = $query."    and mes =   ".$wper2;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "on") or $key=="costosyp" or (isset($call) and $call == "SIC"))
			{
				$query = "select Rescco,Rescpr,Cconom,sum(Resmon) from ".$empresa."_000043,".$empresa."_000161 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resemp = '".$wemp."'";
				$query = $query."    and rescco = '".$wcco1."'";
				$query = $query."    and resper between ".$wper1." and ".$wper2;
				$query = $query."    and rescpr IN ('100','900')"; 
				$query = $query."    and rescco = ccocod  ";
				$query = $query."    and resemp = ccoemp  ";
				$query = $query."    group by Rescco,Rescpr,Cconom";
				$query = $query."    order by  Rescco,Rescpr ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$query = "select Meccco,Meccpr,Cconom,sum(Mecval) from ".$empresa."_000026,".$empresa."_000161 ";
				$query = $query."  where Mecano = ".$wanop;
				$query = $query."    and Mecemp = '".$wemp."'";
				$query = $query."    and Meccco = '".$wcco1."'";
				$query = $query."    and Mecmes between ".$wper1." and ".$wper2;
				$query = $query."    and Meccpr IN ('100','900')"; 
				$query = $query."    and Meccco = ccocod  ";
				$query = $query."    and Mecemp = ccoemp  ";
				$query = $query."    group by Meccco,Meccpr,Cconom";
				$query = $query."    order by  Meccco,Meccpr ";
				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);
				$wdata=array();
				$k1=0;
				$k2=0;
				$num=-1;
				if ($num1 ==  0)
				{
					$k1++;
					$row1[0]='zzzzzzz';
					$row1[1]="";
					$row1[2]=0;
				}
				else
				{
					$row1 = mysql_fetch_array($err1);
					$k1++;
				}
				if ($num2 ==  0)
				{
					$k2++;
					$row2[0]='zzzzzzz';
					$row2[1]="";
					$row2[2]=0;
				}
				else
				{
					$row2 = mysql_fetch_array($err2);
					$k2++;
				}
				while ($k1 <= $num1 or $k2 <= $num2)
				{
					if($row1[0].$row1[1] == $row2[0].$row2[1])
					{
						$num++;
						$wdata[$num][0]=$row1[0].$row1[1];
						$wdata[$num][1]=$row1[2];
						$wdata[$num][2]=$row2[3];
						$wdata[$num][3]=$row1[3];
						$wdata[$num][4]=$row2[3]-$row1[3];
						if($row1[3] != 0)
							$wdata[$num][5]=(($row2[3]/$row1[3]))*100;
						else
							$wdata[$num][5]=0;
						$k1++;
						$k2++;
						if($k1 > $num1)
							$row1[0]="zzzzzzz";
						else
							$row1 = mysql_fetch_array($err1);
						if($k2 > $num2)
							$row2[0]="zzzzzzz";
						else
							$row2 = mysql_fetch_array($err2);
					}
					else if($row1[0].$row1[1] < $row2[0].$row2[1])
					{
						$num++;
						$wdata[$num][0]=$row1[0].$row1[1];
						$wdata[$num][1]=$row1[2];
						$wdata[$num][2]=0;
						$wdata[$num][3]=$row1[3];
						$wdata[$num][4]=0-$row1[3];
						if($row1[3] != 0)
							$wdata[$num][5]=((0/$row1[3])-1)*100;
						else
							$wdata[$num][5]=0;
						$k1++;
						if($k1 > $num1)
							$row1[0]="zzzzzzz";
						else
							$row1 = mysql_fetch_array($err1);
					}
					else
					{
						$num++;
						$wdata[$num][0]=$row2[0].$row2[1];
						$wdata[$num][1]=$row2[2];
						$wdata[$num][2]=$row2[3];
						$wdata[$num][3]=0;
						$wdata[$num][4]=$row2[3];
						$wdata[$num][5]=0;
						$k2++;
						if($k2 > $num2)
							$row2[0]="zzzzzzz";
						else
							$row2 = mysql_fetch_array($err2);
					}
				}
				$tot1 = 0;
				$tot2 = 0;
				echo "<table border=1>";
				echo "<tr><td colspan=7 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=7 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=7 align=center>EJECUCION PRESUPUESTAL DE INGRESOS</td></tr>";
				echo "<tr><td colspan=7 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
				echo "<tr><td colspan=7 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."</td></tr>";
				echo "<tr><td colspan=7 align=center>UNIDAD  : ".$wcco1."</td></tr>";
				echo "<tr><td bgcolor=#dddddd><b>TIPO INGRESO</b></td><td bgcolor=#dddddd><b>C.C</b></td><td bgcolor=#dddddd><b>NOMBRE</b></td><td align=center bgcolor=#dddddd><b>INGRESOS <BR>REALES</b></td><td align=center bgcolor=#dddddd><b>INGRESOS <BR>PRESUPUESTADOS</b></td><td align=right bgcolor=#dddddd><b>DIFERENCIA</b></td><td align=right bgcolor=#dddddd><b>% DE EJECUCION</b></td></tr>";
				for ($i=0;$i<=$num;$i++)
				{
					$tot1 += $wdata[$i][2];
					$tot2 += $wdata[$i][3];
					if(substr($wdata[$i][0],4,3) == "100")
						$tipo = "PROPIOS";
					else
						$tipo = "PARA TERCEROS";
					echo"<tr><td>".$tipo."</td><td>".substr($wdata[$i][0],0,4)."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][5],2,'.',',')."</td></tr>";
	    		}
	    		$tot3 = $tot1 - $tot2;
	    		if($tot2 != 0)
	    			$tot4 = (($tot1/$tot2))*100;
				else
					$tot4 = 0;
				echo"<tr><td colspan=3 bgcolor=#dddddd><b>TOTAL GENERAL</b></td><td align=right bgcolor=#dddddd><b>".number_format((double)$tot1,0,'.',',')."</b></td><td align=right bgcolor=#dddddd><b>".number_format((double)$tot2,0,'.',',')."</b></td><td align=right bgcolor=#dddddd><b>".number_format((double)$tot3,0,'.',',')."</b></td><td align=right bgcolor=#dddddd><b>".number_format((double)$tot4,2,'.',',')."</b></td></tr>";	
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
