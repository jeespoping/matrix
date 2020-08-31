<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Procedimientos Comparativos Para Un Segmento x Empresa x A�os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc55.php Ver. 1.00</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_rc55.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wseg) or !isset($winfla) or (strtoupper ($winfla) != "N"  and strtoupper ($winfla) != "S") or !isset($wper1)  or !isset($wper2) or !isset($wcco1)  or !isset($wcco2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROCEDIMIENTOS COMPARATIVOS PARA UN SEGMENTO X EMPRESA X A�OS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			echo "<tr><td bgcolor=#cccccc align=center>Desea Porcentaje de Participacion (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='winfla' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$ini = strpos($wseg,"-");
			$wsegm=substr($wseg,$ini+1);
			$wseg=substr($wseg,0,$ini);
			$winfla=strtoupper ($winfla);
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
			$query = "SELECT cierre_real,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on" and (integer)substr($row[1],8,2) >= $d[(integer)substr($row[1],5,2)-1])
			{
			$wanopi=$wanop-3;
			$query = "select Msecin,mseano,sum(msecan) as monto from ".$empresa."_000064 ";
			$query = $query."  where mseano  between ".$wanopi." and ".$wanop;
			$query = $query."      and msemes  between ".$wper1." and ".$wper2;
			$query = $query."      and msecco between '".$wcco1."' and '".$wcco2."'";
			$query = $query."      and msecse = '".$wseg."'";
			$query = $query."    group by Msecin,mseano  ";
			$query = $query."    order by Msecin,mseano";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=12 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=12 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=12 align=center>PROCEDIMIENTOS COMPARATIVOS PARA UN SEGMENTO X EMPRESA X A�OS</td></tr>";
			echo "<tr><td colspan=16 align=center>SEGMENTO : ".$wsegm."</td></tr>";
			echo "<tr><td colspan=12 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr><td colspan=12 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A�O : ".$wanop."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			$wdat=array();
			$wdatt=array();
			$wanos=array();
			for ($i=$wanop-3;$i<=$wanop;$i++)
				$wanos[$i-$wanop+3]=$i;
			echo "<tr><td><b>SEGMENTO</b></td>";
			for ($i=$wanop-3;$i<=$wanop;$i++)
				if($winfla == "S")
					echo "<td align=center><b>A�O : ".$wanos[$i-$wanop+3]."</b></td><td align=right><b>% PART.</b></td>";
				else
					echo "<td align=center><b>A�O : ".$wanos[$i-$wanop+3]."</b></td>";
			echo "<td align=center><b>% VARIACION </b></td><td align=right><b>DIFERENCIA</b></td>";
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
					$query = "select empdes from ".$empresa."_000061 ";
					$query = $query."  where empcin = '".$row[0]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$row1 = mysql_fetch_array($err1);
					$seg++;
					$segn=$row[0];
					$wdat[$seg][0]=$row1[0];
					for ($j=1;$j<5;$j++)
						$wdat[$seg][$j]=0;
				}
				$wdat[$seg][$row[1]-$wanop+4]+=$row[2];
				$wdatt[$row[1]-$wanop+4]+=$row[2];
			}
			usort($wdat,'comparacion');
			for ($i=0;$i<=$seg;$i++)
			{
				echo"<tr><td>".$wdat[$i][0]."</td>";
				for ($j=1;$j<5;$j++)
				{
					if ($wdatt[$j] != 0)
						$wpor=$wdat[$i][$j]/$wdatt[$j]*100;
					else
						$wpor=0;
					echo "<td align=right>".number_format((double)$wdat[$i][$j],0,'.',',')."</td>";		
					if($winfla == "S")	
						echo "<td align=right>".number_format((double)$wpor,2,'.',',')."%</td>";			
				}
				if ($wdat[$i][3] != 0)
					$nominal=($wdat[$i][4] - $wdat[$i][3])/$wdat[$i][3];
				else
					$nominal=0;
				$nominal*=100;
				$real=$wdat[$i][4] - $wdat[$i][3];
				echo "<td align=right>".number_format((double)$nominal,2,'.',',')."%</td>";
				echo "<td align=right>".number_format((double)$real,0,'.',',')."</td>";
				echo "</tr>";
			}
			echo"<tr><td bgcolor='#99CCFF'><b>".$wdatt[0]."</b></td>";
			for ($j=1;$j<5;$j++)
			{
				$wpor=100;
				echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdatt[$j],0,'.',',')."</b></td>";	
				if($winfla == "S")
					echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b>%</td>";		
			}	
			if ($wdatt[3] != 0)		
				$nominal=($wdatt[4] - $wdatt[3])/$wdatt[3];
			else
				$nominal=0;	
			$real=$wdatt[4] - $wdatt[3];
			$nominal*=100;
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$nominal,2,'.',',')."%</b></td>";
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$real,0,'.',',')."</b></td>";
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
