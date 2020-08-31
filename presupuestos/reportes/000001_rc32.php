<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Facturacion x Conceptos  Entre A$ntilde;os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc32.php Ver. 2017-06-21</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[2] > $vec2[2])
		return -1;
	elseif ($vec1[2] < $vec2[2])
				return 1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc32.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or !isset($wper2) or  ((!isset($wcco1)  or !isset($wcco2)) and !isset($wccof)) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>FACTURACION X CONCEPTOS  ENTRE A&Ntilde;OS</td></tr>";
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
				echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
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
				$wcco2=substr($wccof,0,$ini);
			}
			$wcco2=strtolower ($wcco2);
			$d=array();
			$d[0]=31;
			$d[1]=28;
			$d[2]=31;
			$d[3]=30;
			$d[4]=31;
			$d[5]=30;
			$d[6]=31;
			$d[7]=31;
			$d[8]=30;
			$d[9]=31;
			$d[10]=30;
			$d[11]=31;
			$query = "SELECT cierre_ingresos,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on" and (integer)substr($row[1],8,2) >= $d[(integer)substr($row[1],5,2)-1])
			{
			$wanopa=$wanop-1;
			$query = "select Prelin,sum(Mioinp) as monto from ".$empresa."_000063,".$empresa."_000060,".$empresa."_000003 ";
			$query = $query."  where mioano = ".$wanopa;
			$query = $query."    and mioemp = '".$wemp."'";
			$query = $query."    and miomes between ".$wper1." and ".$wper2;
			$query = $query."    and miocco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and miocfa = cfacod   ";
			$query = $query."    and mioemp = cfaemp   ";
			$query = $query."    and cfaclas = Precod   ";
			$query = $query."   group by Prelin  ";
			$query = $query."   order by Prelin";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "select Prelin,sum(Mioinp) as monto from ".$empresa."_000063,".$empresa."_000060,".$empresa."_000003 ";
			$query = $query."  where mioano = ".$wanop;
			$query = $query."    and mioemp = '".$wemp."'";
			$query = $query."    and miomes between ".$wper1." and ".$wper2;
			$query = $query."    and miocco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and miocfa = cfacod   ";
			$query = $query."    and mioemp = cfaemp   ";
			$query = $query."    and cfaclas = Precod   ";
			$query = $query."   group by Prelin ";
			$query = $query."   order by Prelin";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=7 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=7 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=7 align=center>FACTURACION X CONCEPTOS  ENTRE A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=7 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=7 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=7 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. "<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			echo "<tr><td><b>CONCEPTO</b></td><td><b>A&Ntilde;O : ".$wanopa."</b></td><td><b>% PART</b></td><td><b>A&Ntilde;O : ".$wanop."</b></td><td><b>% PART</b></td><td align=right><b>DIFERENCIA</b></td><td align=right><b>% VARIACION</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			$valant=0;
			$valact=0;
			if ($num1 ==  0)
			{
				$k1=1;
				$row1[0]='ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ';
				$row1[1]=0;
				$row1[2]=0;
				$row1[3]=0;
				$row1[4]=0;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2=1;
				$row2[0]='ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ';
				$row2[1]=0;
				$row2[2]=0;
				$row2[3]=0;
				$row2[4]=0;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($row1[0] == $row2[0])
				{
					$num++;
					$valant=$valant+$row1[1];
					$valact=$valact+$row2[1];
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row2[1];
					$wdata[$num][3]=$row2[1]-$row1[1];
					if($row1[1] != 0)
						$wdata[$num][4]=($row2[1] - $row1[1])/$row1[1] *100;
					else
						$wdata[$num][4]=0;
					$k1++;
					$k2++;
					if($k1 > $num1)
						$row1[0]="ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ";
					else
						$row1 = mysql_fetch_array($err1);
					if($k2 > $num2)
						$row2[0]="ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ";
					else
						$row2 = mysql_fetch_array($err2);
				}
				else if($row1[0] < $row2[0])
				{
					$num++;
					$valant=$valant+$row1[1];
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=0-$row1[1];
					if($row1[1] != 0)
						$wdata[$num][4]=(0 - $row1[1])/$row1[1] *100;
					else
						$wdata[$num][4]=0;
					$k1++;
					if($k1 > $num1)
						$row1[0]="ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
					$num++;
					$valact=$valact+$row2[1];
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=0;
					$wdata[$num][2]=$row2[1];
					$wdata[$num][3]=$row2[1];
					$wdata[$num][4]=0;
					$k2++;
					if($k2 > $num2)
						$row2[0]="ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ";
					else
						$row2 = mysql_fetch_array($err2);
				}
			}
			usort($wdata,'comparacion');
			for ($i=0;$i<=$num;$i++)
			{
				if($valant != 0)
					$proant=$wdata[$i][1]/$valant * 100;
				else
					$proant=0;
				if($valact != 0)
					$proact=$wdata[$i][2]/$valact * 100;
				else
					$proact=0;
				echo"<tr><td>".$wdata[$i][0]."</td><td  align=right>".number_format((double)$wdata[$i][1],0,'.',',')."</td><td  align=right>".number_format((double)$proant,2,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td  align=right>".number_format((double)$proact,2,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][4],2,'.',',')."</td></tr>";
			}
			$proant= 100;
			$proact= 100;
			$wdif=$valact-$valant;
			if($valant != 0)
				$var=($valact - $valant)/$valant *100;
			else
				$var=0;
			echo"<tr><td bgcolor='#99CCFF'><b>TOTAL FACTURADO</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$valant,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$proant,2,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$valact,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$proact,2,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$var,2,'.',',')."</b></td></tr>";
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
