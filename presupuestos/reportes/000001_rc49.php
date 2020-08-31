<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Facturacion Comparativa Para Un Segmento X A&ntilde;os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc49.php Ver. 2016-02-26</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[4] > $vec2[4])
		return -1;
	elseif ($vec1[4] < $vec2[4])
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
		

		

		echo "<form action='000001_rc49.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($winfla) or !isset($wseg) or !isset($wper1)  or !isset($wper2) or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>FACTURACION COMPARATIVA PARA UN SEGMENTO X A&Ntilde;OS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Porcentaje de Inflacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='winfla' size=5 maxlength=5></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Segmento</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT segcod,segdes from ".$empresa."_000045 order by segdes";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wseg'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
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
			$ini = strpos($wseg,"-");
			$wsegm=substr($wseg,$ini+1);
			$wseg=substr($wseg,0,$ini);
			$Graph="/matrix/images/medical/presupuestos/rc49_".$wanop."_".$wper2."_".$wseg.".jpg";
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
			$query = $query."    and mes = ".$wper2;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on" and (integer)substr($row[1],8,2) >= $d[(integer)substr($row[1],5,2)-1])
			{
			$wanopi=$wanop-3;
			$query = "select empnit,empdes,mioano,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061 ";
			$query = $query."  where mioano  between ".$wanopi." and ".$wanop;
			$query = $query."    and mioemp = '".$wemp."'";
			$query = $query."    and miomes  between ".$wper1." and ".$wper2;
			$query = $query."    and miocco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."    and mionit = epmcod   ";
			$query = $query."    and mioemp = empemp   ";
			$query = $query."    and empseg = '".$wseg."'";
			$query = $query."   group by empnit,empdes,mioano  ";
			$query = $query."   order by empnit,mioano";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=12 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=12 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=12 align=center>FACTURACION COMPARATIVA PARA UN SEGMENTO X A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=12 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=12 align=center>SEGMENTO : ".$wseg."</td></tr>";
			echo "<tr><td colspan=12 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=12 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			$wdat=array();
			$wdatt=array();
			$wanos=array();
			for ($i=$wanop-3;$i<=$wanop;$i++)
				$wanos[$i-$wanop+3]=$i;
			echo "<tr><td><b>ENTIDAD</b></td>";
			for ($i=$wanop-3;$i<=$wanop;$i++)
				echo "<td align=center><b>A&Ntilde;O : ".$wanos[$i-$wanop+3]."</b></td><td align=right><b>% PART.</b></td>";
			echo "<td align=center><b>% NOMINAL </b></td><td align=right><b>% REAL</b></td><td align=right><b>PONDERADO</b></td>";
			for ($i=1;$i<5;$i++)
				$wdatt[$i]=0;
			$seg=-1;
			$segn="";
			$wdatt[0]="TOTAL";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0] != $segn)
				{
					$seg++;
					$segn=$row[0];
					$wdat[$seg][0]=$row[1];
					for ($j=1;$j<5;$j++)
						$wdat[$seg][$j]=0;
				}
				$wdat[$seg][$row[2]-$wanop+4]+=$row[3];
				$wdatt[$row[2]-$wanop+4]+=$row[3];
			}
			usort($wdat,'comparacion');
			for ($i=0;$i<=$seg;$i++)
			{
				echo"<tr><td>".$wdat[$i][0]."</td>";
				for ($j=1;$j<5;$j++)
				{
					if($wdatt[$j] != 0)
						$wpor=$wdat[$i][$j]/$wdatt[$j]*100;
					else
						$wpor=0;
					echo "<td align=right>".number_format((double)$wdat[$i][$j],2,'.',',')."</td>";			
					echo "<td align=right>".number_format((double)$wpor,2,'.',',')."%</td>";			
				}
				if ($wdat[$i][3] != 0)
					$nominal=($wdat[$i][4] - $wdat[$i][3])/$wdat[$i][3];
				else
					$nominal=0;
				$real=((1 + $nominal)/(1 + ($winfla/100))) -1;
				$ponderado=($wpor/100) * $real;
				$nominal*=100;
				$real*=100;
				$ponderado*=100;
				echo "<td align=right>".number_format((double)$nominal,2,'.',',')."%</td>";
				echo "<td align=right>".number_format((double)$real,2,'.',',')."%</td>";
				echo "<td align=right>".number_format((double)$ponderado,2,'.',',')."%</td>";
				echo "</tr>";
			}
			echo"<tr><td bgcolor='#99CCFF'><b>".$wdatt[0]."</b></td>";
			for ($j=1;$j<5;$j++)
			{
				$wpor=100;
				echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdatt[$j],2,'.',',')."</b></td>";	
				echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b>%</td>";		
			}			
			$nominal=($wdatt[4] - $wdatt[3])/$wdatt[3];
			$real=((1 + $nominal)/(1 + ($winfla/100))) -1;
			$ponderado=($wpor/100) * $real;
			$nominal*=100;
			$real*=100;
			$ponderado*=100;
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$nominal,2,'.',',')."%</b></td>";
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$real,2,'.',',')."%</b></td>";
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$ponderado,2,'.',',')."%</b></td>";
			echo "</tr><tr><td colspan=16 align=center><IMG SRC=".$Graph."></td></tr></table>";
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
