<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Procedimientos Comparativos X Segmento X A&ntilde;os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc53.php Ver. 2016-05-27</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[5] > $vec2[5])
		return 1;
	elseif ($vec1[5] < $vec2[5])
				return -1;
			else
				return 0;
}
function titulos($seg)
{
	switch ($seg)
	{
		case "1Q":
			$seg="TOTAL UNIDADES QUIRURGICAS";
		break;
		case "2H":
			$seg="TOTAL UNIDADES DE HOSPITALIZACION";
		break;
		case "3D":
			$seg="TOTAL UNIDADES DE DIAGNOSTICO";
		break;
		case "4A":
			$seg="TOTAL UNIDADES DE ATENCION AMBULATORIA";
		break;
		case "5O":
			$seg="TOTAL OTRAS UNIDADES";
		break;
		case "7E":
			$seg="TOTAL UNIDADES EXTERNAS";
		break;
	}
	$titulos=$seg;
	return $titulos;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc53.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wseg) or !isset($winfla) or (strtoupper ($winfla) != "N"  and strtoupper ($winfla) != "S") or !isset($wper1)  or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROCEDIMIENTOS COMPARATIVOS X SEGMENTO X A&Ntilde;OS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
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
			$query = $query."    and emp = '".$wemp."'";
			$query = $query."    and mes = ".$wper2;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on" and (integer)substr($row[1],8,2) >= $d[(integer)substr($row[1],5,2)-1])
			{
			$wanopi=$wanop-3;
			$query = "select msecco,Cconom,Ccouni,mseano,sum(msecan) as monto from ".$empresa."_000064,".$empresa."_000005 ";
			$query = $query."  where mseano  between ".$wanopi." and ".$wanop;
			$query = $query."    and mseemp = '".$wemp."'";
			$query = $query."    and msemes  between ".$wper1." and ".$wper2;
			$query = $query."    and msecco between '0' and 'z' ";
			$query = $query."    and msecse = '".$wseg."'";
			$query = $query."    and mseemp = ccoemp   ";
			$query = $query."    and msecco = ccocod   ";
			$query = $query."   group by msecco,Cconom,Ccouni,mseano  ";
			$query = $query."   order by msecco,mseano";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<table border=1>";
			echo "<tr><td colspan=12 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=12 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=12 align=center>PROCEDIMIENTOS COMPARATIVOS X SEGMENTO X A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=12 align=center><b>EMPRESA : ".$wempt."</b></td></tr>";
			echo "<tr><td colspan=12 align=center>SEGMENTO : ".$wseg."</td></tr>";
			echo "<tr><td colspan=12 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. " A&Ntilde;O : ".$wanop."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
			$wdat=array();
			$wdatt=array();
			$wanos=array();
			for ($i=$wanop-3;$i<=$wanop;$i++)
				$wanos[$i-$wanop+3]=$i;
			echo "<tr><td><b>UNIDAD</b></td>";
			for ($i=$wanop-3;$i<=$wanop;$i++)
				if($winfla == "S")	
					echo "<td align=center><b>A&Ntilde;O : ".$wanos[$i-$wanop+3]."</b></td><td align=right><b>% PART.</b></td>";
				else
					echo "<td align=center><b>A&Ntilde;O : ".$wanos[$i-$wanop+3]."</b></td>";
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
					$seg++;
					$segn=$row[0];
					$wdat[$seg][0]=$row[1];
					if($row[2] == "6OGI")
						$row[2] = "5O";
					$wdat[$seg][5]=$row[2];
					for ($j=1;$j<5;$j++)
						$wdat[$seg][$j]=0;
				}
				$wdat[$seg][$row[3]-$wanop+4]+=$row[4];
				$wdatt[$row[3]-$wanop+4]+=$row[4];
			}
			$wtip="";
			usort($wdat,'comparacion');
			if($num > 0)
			{
			for ($i=0;$i<=$seg;$i++)
			{
				if($wdat[$i][5] != $wtip)
				{
					switch ($wdat[$i][5])
					{
						case "1Q":
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES QUIRURGICAS</B></td></tr>";
						break;
						case "2H":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=1;$j<5;$j++)
							{
								if($wdatt[$j] != 0)
									$wpor=$wdatc[$j]/$wdatt[$j]*100;
								else
									$wpor=0;
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[$j],0,'.',',')."</B></td>";
								if($winfla == "S")			
									echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";			
							}
							if ($wdatc[3] != 0)
								$nominal=($wdatc[4] - $wdatc[3])/$wdatc[3];
							else
								$nominal=0;
							$real=$wdatc[4] - $wdatc[3];
							$nominal*=100;
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$nominal,2,'.',',')."%</B></td>";
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$real,0,'.',',')."</B></td>";
							echo "</tr>";
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES DE HOSPITALIZACION</B></td></tr>";
						break;
						case "3D":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=1;$j<5;$j++)
							{
								if($wdatt[$j] != 0)
									$wpor=$wdatc[$j]/$wdatt[$j]*100;
								else
									$wpor=0;
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[$j],0,'.',',')."</B></td>";
								if($winfla == "S")			
									echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";			
							}
							if ($wdatc[3] != 0)
								$nominal=($wdatc[4] - $wdatc[3])/$wdatc[3];
							else
								$nominal=0;
							$real=$wdatc[4] - $wdatc[3];
							$nominal*=100;
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$nominal,2,'.',',')."%</B></td>";
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$real,0,'.',',')."</B></td>";
							echo "</tr>";
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES DE DIAGNOSTICO</B></td></tr>";
						break;
						case "4A":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=1;$j<5;$j++)
							{
								if($wdatt[$j] != 0)
									$wpor=$wdatc[$j]/$wdatt[$j]*100;
								else
									$wpor=0;
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[$j],0,'.',',')."</B></td>";	
								if($winfla == "S")		
									echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";			
							}
							if ($wdatc[3] != 0)
								$nominal=($wdatc[4] - $wdatc[3])/$wdatc[3];
							else
								$nominal=0;
							$real=$wdatc[4] - $wdatc[3];
							$nominal*=100;
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$nominal,2,'.',',')."%</B></td>";
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$real,0,'.',',')."</B></td>";
							echo "</tr>";
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES DE ATENCION AMBULATORIA</B></td></tr>";
						break;
						case "5O":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=1;$j<5;$j++)
							{
								if($wdatt[$j] != 0)
									$wpor=$wdatc[$j]/$wdatt[$j]*100;
								else
									$wpor=0;
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[$j],0,'.',',')."</B></td>";	
								if($winfla == "S")		
									echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";			
							}
							if ($wdatc[3] != 0)
								$nominal=($wdatc[4] - $wdatc[3])/$wdatc[3];
							else
								$nominal=0;
							$real=$wdatc[4] - $wdatc[3];
							$nominal*=100;
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$nominal,2,'.',',')."%</B></td>";
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$real,0,'.',',')."</B></td>";
							echo "</tr>";
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>OTRAS UNIDADES</B></td></tr>";
						break;
						case "7E":
							echo"<tr><td bgcolor='#cccccc'><b>".titulos($wtip)."</B></td>";
							for ($j=1;$j<5;$j++)
							{
								if($wdatt[$j] != 0)
									$wpor=$wdatc[$j]/$wdatt[$j]*100;
								else
									$wpor=0;
								echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[$j],0,'.',',')."</B></td>";	
								if($winfla == "S")		
									echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";			
							}
							if ($wdatc[3] != 0)
								$nominal=($wdatc[4] - $wdatc[3])/$wdatc[3];
							else
								$nominal=0;
							$real=$wdatc[4] - $wdatc[3];
							$nominal*=100;
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$nominal,2,'.',',')."%</B></td>";
							echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$real,0,'.',',')."</B></td>";
							echo "</tr>";
							echo "<tr><td bgcolor='#FFFFFF' colspan=12><b>UNIDADES EXTERNAS</B></td></tr>";
						break;
					}
					$wtip=$wdat[$i][5];
					for ($k=1;$k<5;$k++)
						$wdatc[$k]=0;
				}
				echo"<tr><td>".$wdat[$i][0]."</td>";
				for ($j=1;$j<5;$j++)
				{
					$wdatc[$j]+=$wdat[$i][$j];
					if($wdatt[$j] != 0)
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
				$real=$wdat[$i][4] - $wdat[$i][3];
				$nominal*=100;
				echo "<td align=right>".number_format((double)$nominal,2,'.',',')."%</td>";
				echo "<td align=right>".number_format((double)$real,0,'.',',')."</td>";
				echo "</tr>";
			}
			switch ($wtip)
			{
				case "1Q":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES QUIRURGICAS</B></td>";
				break;
				case "2H":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE HOSPITALIZACION</B></td>";
				break;
				case "3D":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE DIAGNOSTICO</B></td>";
				break;
				case "4A":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE ATENCION AMBULATORIA</B></td>";
				break;
				case "5O":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL OTRAS UNIDADES</B></td>";
				break;
				case "7E":
					echo "<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES EXTERNAS</B></td>";
				break;
			}
			for ($j=1;$j<5;$j++)
			{
				if($wdatt[$j] != 0)
					$wpor=$wdatc[$j]/$wdatt[$j]*100;
				else
						$wpor=0;
				echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wdatc[$j],0,'.',',')."</B></td>";	
				if($winfla == "S")		
					echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."%</B></td>";			
			}
			if ($wdatc[3] != 0)
				$nominal=($wdatc[4] - $wdatc[3])/$wdatc[3];
			else
				$nominal=0;
			$real=$wdatc[4] - $wdatc[3];
			$nominal*=100;
			echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$nominal,2,'.',',')."%</B></td>";
			echo "<td bgcolor='#cccccc' align=right><b>".number_format((double)$real,0,'.',',')."</B></td>";
			echo "</tr>";
			echo"<tr><td bgcolor='#99CCFF'><b>".$wdatt[0]."</b></td>";
			for ($j=1;$j<5;$j++)
			{
				$wpor=100;
				echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdatt[$j],0,'.',',')."</b></td>";
				if($winfla == "S")	
					echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b>%</td>";		
			}		
			if ($wdatc[3] != 0)	
				$nominal=($wdatt[4] - $wdatt[3])/$wdatt[3];
			else
				$nominal=0;	
			$real=$wdatt[4] - $wdatt[3];
			$nominal*=100;
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$nominal,2,'.',',')."%</b></td>";
			echo "<td bgcolor='#99CCFF' align=right><b>".number_format((double)$real,0,'.',',')."</b></td>";
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
