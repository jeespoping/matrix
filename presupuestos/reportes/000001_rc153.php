<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Conciliacion de Ingresos (Facturacion vs Estado de Resultados)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc153.php Ver. 2011-05-12</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc153.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wper2) or !isset($wanop) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>CONCILIACION DE INGRESOS (FACTURACION VS ESTADO DE RESULTADOS)</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Ano  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=10 bgcolor=#DDDDDD><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center colspan=10 bgcolor=#DDDDDD><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center colspan=10 bgcolor=#DDDDDD><b>CONCILIACION DE INGRESOS (FACTURACION VS ESTADO DE RESULTADOS)</b></td></tr>";
			echo "<tr><td colspan=10 align=center bgcolor=#DDDDDD>PERIODO  DESDE : ".$wper1." HASTA : ".$wper2." ANO : ".$wanop."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>CONCEPTO<BR>FACTURACION</b></td><td bgcolor=#cccccc><b>DESCRIPCION<BR>CPTO</b></td><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td bgcolor=#cccccc><b>VALOR<BR>PROPIO 63</b></td><td bgcolor=#cccccc><b>VALOR<BR>PROPIO 137</b></td><td bgcolor=#cccccc><b>DIFERENCIA</b></td><td bgcolor=#cccccc><b>VALOR<BR>TERCEROS 63</b></td><td bgcolor=#cccccc><b>VALOR<BR>TERCEROS 137</b></td><td bgcolor=#cccccc><b>DIFERENCIA</b></td></tr>";
			//                                  0                      1                       2                      3                         4                           5
			$query  = "select ".$empresa."_000063.Miocfa,".$empresa."_000060.Cfades,".$empresa."_000063.Miocco,".$empresa."_000005.Cconom,sum(".$empresa."_000063.Mioinp),sum(".$empresa."_000063.Mioint) from ".$empresa."_000063,".$empresa."_000005,".$empresa."_000060 ";
			$query .= " where ".$empresa."_000063.mioano = ".$wanop; 
			$query .= "   and ".$empresa."_000063.miomes between ".$wper1." and ".$wper2;
			$query .= "   and ".$empresa."_000063.miocla = 'fac'"; 
			$query .= "   and ".$empresa."_000063.miocco = ".$empresa."_000005.ccocod ";
			$query .= "   and ".$empresa."_000063.miocfa = ".$empresa."_000060.cfacod ";
			$query .= " group by 1,2,3,4 ";
			$query .= " order by 1,3 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			//                                  0                      1                       2                      3                         4                           5     
			$query  = "select ".$empresa."_000137.Fddcon,".$empresa."_000060.Cfades,".$empresa."_000137.Fddcco,".$empresa."_000005.cconom,sum(".$empresa."_000137.Fddipr),sum(".$empresa."_000137.Fddite) from ".$empresa."_000137,".$empresa."_000005,".$empresa."_000060 ";
			$query .= " where ".$empresa."_000137.fddano = ".$wanop; 
			$query .= " and ".$empresa."_000137.fddmes between ".$wper1." and ".$wper2;
			$query .= " and ".$empresa."_000137.Fddcco = ".$empresa."_000005.ccocod ";
			$query .= " and ".$empresa."_000137.Fddcon = ".$empresa."_000060.cfacod ";
			$query .= " group by 1,2,3,4 ";
			$query .= " order by 1,3 ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzzzzzzz';
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
				$key2='zzzzzzzz';
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
					$wdata[$num][6]=$row1[4] - $row2[4];
					$wdata[$num][7]=$row1[5];
					$wdata[$num][8]=$row2[5];
					$wdata[$num][9]=$row1[5] - $row2[5];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[2];
					}
					if($k2 > $num2)
						$key2="zzzzzzzz";
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
					$wdata[$num][6]=$row1[4] - 0;
					$wdata[$num][7]=$row1[5];
					$wdata[$num][8]=0;
					$wdata[$num][9]=$row1[5] - 0;
					$k1++;
					if($k1 > $num1)
						$key1="zzzzzzzz";
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
					$wdata[$num][6]=0 - $row2[4];
					$wdata[$num][7]=0;
					$wdata[$num][8]=$row2[5];
					$wdata[$num][9]=0 - $row2[5];
					$k2++;
					if($k2 > $num2)
						$key2="zzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[2];
					}
				}
			}
			$total=array();
			for ($i=0;$i<=5;$i++) $total[$i]=0;
			for ($i=0;$i<=$num;$i++)
			{
				$total[0] += $wdata[$i][4];
				$total[1] += $wdata[$i][5];
				$total[2] += $wdata[$i][6];
				$total[3] += $wdata[$i][7];
				$total[4] += $wdata[$i][8];
				$total[5] += $wdata[$i][9];
				if($i % 2 == 0)
					$color = "#99CCFF";
				else
					$color = "#FFFFFF";
				echo "<tr><td bgcolor=".$color."> ".$wdata[$i][0]."</td><td bgcolor=".$color."> ".$wdata[$i][1]."</td><td bgcolor=".$color."> ".$wdata[$i][2]."</td><td bgcolor=".$color."> ".$wdata[$i][3]."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][4],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][5],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][6],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][7],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][8],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][9],0,'.',',')."</td></tr>";
			}
			$color = "#DDDDDD";
			echo "<tr><td bgcolor=".$color." colspan=4>TOTAL GENERAL</td><td align=right bgcolor=".$color."> ".number_format($total[0],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($total[1],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($total[2],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($total[3],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($total[4],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($total[5],0,'.',',')."</td></tr>";
			echo "</table>";
		}
}
?>
</body>
</html>
