<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Conciliacion de Costos vs Estado de Resultado Real</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc146.php Ver. 2016-09-20</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			//echo " Medio : ".$lm." valor: ".$d[$lm][$i]."<br>";
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][$i]),20);
			//if(strtoupper($k) == strtoupper($d[$lm][$i]))
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
			//echo $k." ".$d[$li][$i]." ".$d[$ls][$i]." ".$d[$lm][$i]." ".$li." ".$ls." ".$lm."<br>";
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc146.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanop) or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE COSTOS</td></tr>";
			echo "<tr><td align=center colspan=2>CONCILIACION DE COSTOS VS ESTADO DE RESULTADO REAL</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Comentarios</td>";
			echo "<td bgcolor=#cccccc align=center><textarea name='wcom' cols=80 rows=5></textarea></td></tr>";
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
			$wcco2=strtolower ($wcco2);
			$query  = "select ".$empresa."_000092.Mgacco,".$empresa."_000092.Mgagas,".$empresa."_000079.cognom,sum(".$empresa."_000092.Mgaval) "; 
			$query .= " from ".$empresa."_000092,".$empresa."_000079,".$empresa."_000005  ";
			$query .= "  where ".$empresa."_000092.mgaano = ".$wanop; 
			$query .= "    and ".$empresa."_000092.mgaemp = '".$wemp."'"; 
			$query .= "    and ".$empresa."_000092.mgaper = ".$wper1; 
			$query .= "	   and ".$empresa."_000092.mgacco between '".$wcco1."' and  '".$wcco2."' "; 
			$query .= "	   and ".$empresa."_000092.mgacco = ".$empresa."_000005.Ccocod "; 
			$query .= "	   and ".$empresa."_000092.mgaemp = ".$empresa."_000005.Ccoemp "; 
			$query .= "	   and ".$empresa."_000005.Ccocos = 'S' "; 
			$query .= "	   and ".$empresa."_000092.mgatip in ('traslados','explicaciones','generales') "; 
			$query .= "	   and ".$empresa."_000092.Mgagas = ".$empresa."_000079.cogcod "; 
			$query .= " group by 1,2,3 ";  
			$query .= " order by 1,2 ";		
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$dist=array();
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$dist[$i][0]=$row[0].$row[1];
					$dist[$i][1]=$row[3];
				}
			}
			$totdis=$num;
			$totales=array();
			$numtot=-1;
			for ($i=0;$i<4;$i++)
			{
				$totales[$i][0]=0;
				$totales[$i][1]=0;
			}
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=7 bgcolor=#FFFFFF><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center colspan=7 bgcolor=#FFFFFF><b>APLICACION DE COSTOS</b></td></tr>";
			echo "<tr><td align=center colspan=7 bgcolor=#FFFFFF><b>CONCILIACION DE COSTOS VS ESTADO DE RESULTADO REAL</b></td></tr>";
			echo "<tr><td align=center colspan=7 bgcolor=#FFFFFF><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=7 align=center bgcolor=#FFFFFF>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td colspan=7 align=center bgcolor=#FFFFFF>CC INICIAL  : ".$wcco1. " CC FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>RUBRO</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td bgcolor=#cccccc><b>COSTOS</b></td><td bgcolor=#cccccc><b>DISTRIBUIBLE</b></td><td bgcolor=#cccccc><b>E.R. REAL</b></td><td bgcolor=#cccccc><b>DIFERENCIA</b></td></tr>";
			echo "<tr><td colspan=7 align=left bgcolor=#999999><b>Gastos Generales</b></td></tr>";
			//                               0                      1                      2                   3
			$query  = "select ".$empresa."_000026.Meccco,".$empresa."_000026.Meccpr,".$empresa."_000079.cognom,sum(".$empresa."_000026.Mecval) ";
			$query .= "  from ".$empresa."_000026,".$empresa."_000079,".$empresa."_000005  "; 
			$query .= "   where ".$empresa."_000026.mecano = ".$wanop; 
			$query .= "     and ".$empresa."_000026.mecemp = '".$wemp."'"; 
			$query .= "     and ".$empresa."_000026.mecmes = ".$wper1;
			$query .= " 	and ".$empresa."_000026.meccco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "	    and ".$empresa."_000026.meccco = ".$empresa."_000005.Ccocod "; 
			$query .= "	    and ".$empresa."_000026.mecemp = ".$empresa."_000005.Ccoemp "; 
			$query .= "	    and ".$empresa."_000005.Ccocos = 'S' "; 
			$query .= " 	and ".$empresa."_000026.Meccpr between '200'  and '399' "; 
			$query .= " 	and ".$empresa."_000026.meccue between '0'  and '88888888' "; 
			$query .= " 	and ".$empresa."_000026.Meccpr = ".$empresa."_000079.cogcod "; 
			$query .= "  group by 1,2,3 ";  
			$query .= "  order by 1,2 "; 
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			//                               0                      1                      2                   3      
			$query  = "select ".$empresa."_000087.Gascco,".$empresa."_000087.Gasgas,".$empresa."_000079.cognom,sum(".$empresa."_000087.Gasval) "; 
			$query .= "  from ".$empresa."_000087,".$empresa."_000079,".$empresa."_000005 "; 
			$query .= "   where ".$empresa."_000087.gasano = ".$wanop; 
			$query .= "     and ".$empresa."_000087.gasemp = '".$wemp."'";
			$query .= "     and ".$empresa."_000087.gasmes = ".$wper1; 
			$query .= " 	and ".$empresa."_000087.gascco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "	    and ".$empresa."_000087.gascco = ".$empresa."_000005.Ccocod "; 
			$query .= "	    and ".$empresa."_000087.gasemp = ".$empresa."_000005.Ccoemp "; 
			$query .= "	    and ".$empresa."_000005.Ccocos = 'S' "; 
			$query .= " 	and ".$empresa."_000087.Gasgas between '200'  and '399' "; 
			$query .= " 	and LENGTH(".$empresa."_000087.Gasgas) = 3 "; 
			$query .= " 	and ".$empresa."_000087.Gasgas = ".$empresa."_000079.cogcod "; 
			$query .= "  group by 1,2,3 ";
			$query .= "  order by 1,2 ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzzzzzz';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0].$row1[1];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='zzzzzzz';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0].$row2[1];
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
					$wdata[$num][3]=$row2[3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row1[3];
					$wdata[$num][6]=$row1[3] - $row2[3];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
					if($k2 > $num2)
						$key2="zzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row1[3];
					$wdata[$num][6]=$row1[3];
					$k1++;
					if($k1 > $num1)
						$key1="zzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
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
					$wdata[$num][5]=0;
					$wdata[$num][6]=0 - $row2[3];
					$k2++;
					if($k2 > $num2)
						$key2="zzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
			}
			$ccoant="";
			$suma1=0;
			$suma2=0;
			for ($i=0;$i<=$num;$i++)
			{
				if($i % 2 == 0)
					$color = "#99CCFF";
				else
					$color = "#FFFFFF";
				if($wdata[$i][0] != $ccoant)
				{
					$ccoant=$wdata[$i][0];
					if($i > 0)
						echo "<tr><td colspan=3 bgcolor=#FFCC66><b>TOTAL CENTRO DE COSTOS</b></td><td align=right bgcolor=#FFCC66><b>".number_format($suma1,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td><td align=right bgcolor=#FFCC66><b>".number_format($suma2,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td></tr>";
					$suma1=0;
					$suma2=0;
				}
				$pos=bi($dist,$totdis,$wdata[$i][0].$wdata[$i][1],0);
				if($pos != -1)
				{
					$wdata[$i][4]=$dist[$pos][1];
				}
				$suma1 += $wdata[$i][3];
				$suma2 += $wdata[$i][5];
				$totales[0][0] += $wdata[$i][3];
				$totales[0][1] += $wdata[$i][5];
				$totales[3][0] += $wdata[$i][3];
				$totales[3][1] += $wdata[$i][5];
				echo "<tr><td bgcolor=".$color."> ".$wdata[$i][0]."</td><td bgcolor=".$color."> ".$wdata[$i][1]."</td><td bgcolor=".$color."> ".$wdata[$i][2]."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][3],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][4],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][5],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][6],0,'.',',')."</td></tr>";
			}
			echo "<tr><td colspan=3 bgcolor=#FFCC66><b>TOTAL CENTRO DE COSTOS</b></td><td align=right bgcolor=#FFCC66><b>".number_format($suma1,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td><td align=right bgcolor=#FFCC66><b>".number_format($suma2,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td></tr>";
			echo "<tr><td colspan=3 bgcolor=#dddddd><b>TOTALES</b></td><td align=right bgcolor=#dddddd><b>".number_format($totales[0][0],0,'.',',')."</b></td><td align=right bgcolor=#dddddd></td><td align=right bgcolor=#dddddd><b>".number_format($totales[0][1],0,'.',',')."</b></td><td align=right bgcolor=#dddddd></td></tr>";
			
			echo "<tr><td colspan=7 align=left bgcolor=#999999><b>Indirectos</b></td></tr>";
			//                               0                      1                      2                   3
			$query  = "select ".$empresa."_000054.Mdicco,".$empresa."_000054.Mdiind,".$empresa."_000079.cognom,sum(".$empresa."_000054.Mdimon) "; 
			$query .= "   from ".$empresa."_000054,".$empresa."_000079,".$empresa."_000005 "; 
			$query .= "    where ".$empresa."_000054.mdiano = ".$wanop;  
			$query .= "      and ".$empresa."_000054.mdiemp = '".$wemp."'";
			$query .= "      and ".$empresa."_000054.mdimes = ".$wper1; 
			$query .= " 	 and ".$empresa."_000054.mdicco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "	     and ".$empresa."_000054.mdicco = ".$empresa."_000005.Ccocod "; 
			$query .= "	     and ".$empresa."_000054.mdiemp = ".$empresa."_000005.Ccoemp "; 
			$query .= "	     and ".$empresa."_000005.Ccocos = 'S' "; 
			$query .= " 	 and ".$empresa."_000054.mditip = 'R' ";  
			$query .= " 	 and ".$empresa."_000054.Mdiind = ".$empresa."_000079.cogcod  ";
			$query .= "  group by 1,2,3  "; 
			$query .= "  order by 1,2 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			//                               0                      1                      2                   3      
			$query  = "select ".$empresa."_000087.Gascco,".$empresa."_000087.Gasgas,".$empresa."_000079.cognom,sum(".$empresa."_000087.Gasval) "; 
			$query .= "  from ".$empresa."_000087,".$empresa."_000079,".$empresa."_000005 "; 
			$query .= "   where ".$empresa."_000087.gasano = ".$wanop; 
			$query .= "     and ".$empresa."_000087.gasemp = '".$wemp."'";
			$query .= "     and ".$empresa."_000087.gasmes = ".$wper1; 
			$query .= " 	and ".$empresa."_000087.gascco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "	    and ".$empresa."_000087.gascco = ".$empresa."_000005.Ccocod "; 
			$query .= "	    and ".$empresa."_000087.gasemp = ".$empresa."_000005.Ccoemp "; 
			$query .= "	    and ".$empresa."_000005.Ccocos = 'S' "; 
			$query .= " 	and ".$empresa."_000087.Gastip = 'indirectos' "; 
			$query .= " 	and ".$empresa."_000087.Gasgas = ".$empresa."_000079.cogcod "; 
			$query .= "  group by 1,2,3 ";
			$query .= "  order by 1,2 ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzzzzzzzzzzz';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0].$row1[1];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='zzzzzzzzzzzz';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0].$row2[1];
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
					$wdata[$num][3]=$row2[3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row1[3];
					$wdata[$num][6]=$row1[3] - $row2[3];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
					if($k2 > $num2)
						$key2="zzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row1[3];
					$wdata[$num][6]=$row1[3];
					$k1++;
					if($k1 > $num1)
						$key1="zzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
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
					$wdata[$num][5]=0;
					$wdata[$num][6]=0 - $row2[3];
					$k2++;
					if($k2 > $num2)
						$key2="zzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
			}
			$ccoant="";
			$suma1=0;
			$suma2=0;
			for ($i=0;$i<=$num;$i++)
			{
				if($i % 2 == 0)
					$color = "#99CCFF";
				else
					$color = "#FFFFFF";
				if($wdata[$i][0] != $ccoant)
				{
					$ccoant=$wdata[$i][0];
					if($i > 0)
						echo "<tr><td colspan=3 bgcolor=#FFCC66><b>TOTAL CENTRO DE COSTOS</b></td><td align=right bgcolor=#FFCC66><b>".number_format($suma1,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td><td align=right bgcolor=#FFCC66><b>".number_format($suma2,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td></tr>";
					$suma1=0;
					$suma2=0;
				}
				$suma1 += $wdata[$i][3];
				$suma2 += $wdata[$i][5];
				$totales[1][0] += $wdata[$i][3];
				$totales[1][1] += $wdata[$i][5];
				$totales[3][0] += $wdata[$i][3];
				$totales[3][1] += $wdata[$i][5];
				echo "<tr><td bgcolor=".$color."> ".$wdata[$i][0]."</td><td bgcolor=".$color."> ".$wdata[$i][1]."</td><td bgcolor=".$color."> ".$wdata[$i][2]."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][3],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][4],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][5],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][6],0,'.',',')."</td></tr>";
			}
			echo "<tr><td colspan=3 bgcolor=#FFCC66><b>TOTAL CENTRO DE COSTOS</b></td><td align=right bgcolor=#FFCC66><b>".number_format($suma1,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td><td align=right bgcolor=#FFCC66><b>".number_format($suma2,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td></tr>";
			echo "<tr><td colspan=3 bgcolor=#dddddd><b>TOTALES</b></td><td align=right bgcolor=#dddddd><b>".number_format($totales[1][0],0,'.',',')."</b></td><td align=right bgcolor=#dddddd></td><td align=right bgcolor=#dddddd><b>".number_format($totales[1][1],0,'.',',')."</b></td><td align=right bgcolor=#dddddd></td></tr>";
			
			echo "<tr><td colspan=7 align=left bgcolor=#999999><b>Centros de Servicios</b></td></tr>";
			//                               0                      1                      2                   3
			$query  = "select ".$empresa."_000073.Proccd,".$empresa."_000073.Procco,".$empresa."_000079.cognom,sum(".$empresa."_000073.Promon) "; 
			$query .= "  from ".$empresa."_000073,".$empresa."_000079,".$empresa."_000005 "; 
			$query .= "   where ".$empresa."_000073.proano = ".$wanop;  
			$query .= "     and ".$empresa."_000073.proemp = '".$wemp."'";
			$query .= "     and ".$empresa."_000073.promes = ".$wper1;  
			$query .= " 	and ".$empresa."_000073.Proccd between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "	    and ".$empresa."_000073.Proccd = ".$empresa."_000005.Ccocod "; 
			$query .= "	    and ".$empresa."_000073.proemp = ".$empresa."_000005.Ccoemp "; 
			$query .= "	    and ".$empresa."_000005.Ccocos = 'S' ";
			$query .= " 	and ".$empresa."_000073.protip = 'R' "; 
			$query .= " 	and ".$empresa."_000073.Procco = ".$empresa."_000079.cogcod "; 
			$query .= "  group by 1,2,3 "; 
			$query .= "  order by 1,2 "; 
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			//                               0                      1                      2                   3      
			$query  = "select ".$empresa."_000087.Gascco,".$empresa."_000087.Gasgas,".$empresa."_000079.cognom,sum(".$empresa."_000087.Gasval) "; 
			$query .= "  from ".$empresa."_000087,".$empresa."_000079,".$empresa."_000005 "; 
			$query .= "   where ".$empresa."_000087.gasano = ".$wanop; 
			$query .= "     and ".$empresa."_000087.gasemp = '".$wemp."'";
			$query .= "     and ".$empresa."_000087.gasmes = ".$wper1; 
			$query .= " 	and ".$empresa."_000087.gascco between '".$wcco1."' and  '".$wcco2."' ";
			$query .= "	    and ".$empresa."_000087.gascco = ".$empresa."_000005.Ccocod "; 
			$query .= "	    and ".$empresa."_000087.gasemp = ".$empresa."_000005.Ccoemp "; 
			$query .= "	    and ".$empresa."_000005.Ccocos = 'S' ";
			$query .= " 	and ".$empresa."_000087.Gastip = 'servicios' "; 
			$query .= " 	and ".$empresa."_000087.Gasgas = ".$empresa."_000079.cogcod "; 
			$query .= "  group by 1,2,3 ";
			$query .= "  order by 1,2 ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$key1='zzzzzzzzzzzz';
				$k1=1;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$key1= $row1[0].$row1[1];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$key2='zzzzzzzzzzzz';
				$k2=1;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$key2= $row2[0].$row2[1];
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
					$wdata[$num][3]=$row2[3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row1[3];
					$wdata[$num][6]=$row1[3] - $row2[3];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$key1="zzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
					}
					if($k2 > $num2)
						$key2="zzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
				else if($key1 < $key2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row1[3];
					$wdata[$num][6]=$row1[3];
					$k1++;
					if($k1 > $num1)
						$key1="zzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$key1=$row1[0].$row1[1];
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
					$wdata[$num][5]=0;
					$wdata[$num][6]=0 - $row2[3];
					$k2++;
					if($k2 > $num2)
						$key2="zzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$key2=$row2[0].$row2[1];
					}
				}
			}
			$ccoant="";
			$suma1=0;
			$suma2=0;
			for ($i=0;$i<=$num;$i++)
			{
				if($i % 2 == 0)
					$color = "#99CCFF";
				else
					$color = "#FFFFFF";
				if($wdata[$i][0] != $ccoant)
				{
					$ccoant=$wdata[$i][0];
					if($i > 0)
						echo "<tr><td colspan=3 bgcolor=#FFCC66><b>TOTAL CENTRO DE COSTOS</b></td><td align=right bgcolor=#FFCC66><b>".number_format($suma1,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td><td align=right bgcolor=#FFCC66><b>".number_format($suma2,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td></tr>";
					$suma1=0;
					$suma2=0;
				}
				$suma1 += $wdata[$i][3];
				$suma2 += $wdata[$i][5];
				$totales[2][0] += $wdata[$i][3];
				$totales[2][1] += $wdata[$i][5];
				$totales[3][0] += $wdata[$i][3];
				$totales[3][1] += $wdata[$i][5];
				echo "<tr><td bgcolor=".$color."> ".$wdata[$i][0]."</td><td bgcolor=".$color."> ".$wdata[$i][1]."</td><td bgcolor=".$color."> ".$wdata[$i][2]."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][3],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][4],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][5],0,'.',',')."</td><td align=right bgcolor=".$color."> ".number_format($wdata[$i][6],0,'.',',')."</td></tr>";
			}
			echo "<tr><td colspan=3 bgcolor=#FFCC66><b>TOTAL CENTRO DE COSTOS</b></td><td align=right bgcolor=#FFCC66><b>".number_format($suma1,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td><td align=right bgcolor=#FFCC66><b>".number_format($suma2,0,'.',',')."</b></td><td align=right bgcolor=#FFCC66></td></tr>";
			echo "<tr><td colspan=3 bgcolor=#dddddd><b>TOTALES</b></td><td align=right bgcolor=#dddddd><b>".number_format($totales[2][0],0,'.',',')."</b></td><td align=right bgcolor=#dddddd></td><td align=right bgcolor=#dddddd><b>".number_format($totales[2][1],0,'.',',')."</b></td><td align=right bgcolor=#dddddd></td></tr>";
			echo "<tr><td colspan=3 bgcolor=#999999><b>TOTALES GENERALES</b></td><td align=right bgcolor=#999999><b>".number_format($totales[3][0],0,'.',',')."</b></td><td align=right bgcolor=#999999></td><td align=right bgcolor=#999999><b>".number_format($totales[3][1],0,'.',',')."</b></td><td align=right bgcolor=#999999></td></tr>";
			echo "<tr><td colspan=7 align=left bgcolor=#dddddd>".$wcom."</td></tr>";
			echo "</table>";
		}
}
?>
</body>
</html>
