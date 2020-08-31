<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe Comparativo de Utilidades Operacionales Entre A�os</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc27.php Ver. 1.01</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[7] > $vec2[7])
		return 1;
	elseif ($vec1[7] < $vec2[7])
				return -1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc27.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wper2)  or !isset($wres)  or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D")  or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME COMPARATIVO DE UTILIDADES OPERACIONALES ENTRE A�OS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wres=strtoupper ($wres);
			$query = "SELECT cierre_real,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper2;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on")
			{
			$wanopa=$wanop-1;
			$query = "select orucco,Cconom,Ccouni,sum(orumon) from ".$empresa."_000037,".$empresa."_000005 ";
			$query = $query."  where oruano = ".$wanop;
			$query = $query."      and orumes between ".$wper1." and ".$wper2;
			$query = $query."      and orucod = 'UU' ";
			$query = $query."      and orucco = ccocod   ";
			$query = $query."    group by orucco,Cconom,Ccouni  ";
			$query = $query."    order by ccouni,orucco ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "select orucco,Cconom,Ccouni,sum(orumon) from ".$empresa."_000037,".$empresa."_000005 ";
			$query = $query."  where oruano = ".$wanopa;
			$query = $query."      and orumes between ".$wper1." and ".$wper2;
			$query = $query."      and orucod = 'UU' ";
			$query = $query."      and orucco = ccocod   ";
			$query = $query."    group by orucco,Cconom,Ccouni  ";
			$query = $query."    order by ccouni,orucco ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<table border=1>";
			echo "<tr><td colspan=5 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=5 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=5 align=center>INFORME COMPARATIVO DE UTILIDADES OPERACIONALES ENTRE A�OS</td></tr>";
			echo "<tr><td colspan=5 align=center>PERIODO INICIAL : ".$wper1." PERIODO FINAL : ".$wper2. "</td></tr>";
			echo "<tr><td><b>UNIDAD</b></td><td><b>A�O : ".$wanopa."</b></td><td><b>A�O : ".$wanop."</b></td><td align=right><b>DIFERENCIA</b></td><td align=right><b>% VARIACION</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1=1;
				$row1[0]='9999';
				$row1[1]=" ";
				$row1[2]="";
				$row1[3]=0;
				$kla1="999999";
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$kla1=substr($row1[2],0,2).$row1[0];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2=1;
				$row2[0]='9999';
				$row2[1]=" ";
				$row2[2]="";
				$row2[3]=0;
				$kla2="999999";
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$kla2=substr($row2[2],0,2).$row2[0];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($kla1 == $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					if($row1[2] == "6OGI")
						$row1[2] = "5O";
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=$row2[3];
					$wdata[$num][5]=$row2[3]-$row1[3];
					$wdata[$num][7]=substr($wdata[$num][2],0,2).$wdata[$num][0];
					if($row1[3] != 0)
						$wdata[$num][6]=($row2[3]/$row1[3])*100;
					else
						$wdata[$num][6]=0;
					$k1++;
					$k2++;
					if($k1 > $num1)
					{
						$row1[0]="9999";
						$kla1="999999";
					}
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=substr($row1[2],0,2).$row1[0];
					}
					if($k2 > $num2)
					{
						$row2[0]="9999";
						$kla2="999999";
					}
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=substr($row2[2],0,2).$row2[0];
					}
				}
				else if($kla1 < $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					if($row1[2] == "6OGI")
						$row1[2] = "5O";
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=0-$row1[3];
					$wdata[$num][6]=0;
					$wdata[$num][7]=substr($wdata[$num][2],0,2).$wdata[$num][0];
					$k1++;
					if($k1 > $num1)
					{
						$row1[0]="9999";
						$kla1="999999";
					}
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=substr($row1[2],0,2).$row1[0];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					if($row2[2] == "6OGI")
						$row2[2] = "5O";
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=$row2[3];
					$wdata[$num][5]=$row2[3];
					$wdata[$num][6]=0;
					$wdata[$num][7]=substr($wdata[$num][2],0,2).$wdata[$num][0];
					$k2++;
					if($k2 > $num2)
					{
						$row2[0]="9999";
						$kla2="999999";
					}
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=substr($row2[2],0,2).$row2[0];
					}
				}
			}
			usort($wdata,'comparacion');
			$wtotal1=array();
			$wtotal2=array();
			$wtott=array();
			$ita=0;
			$unidad="";
			$wtotal1[1]=0;
			$wtotal1[2]=0;
			$wtotal2[1]=0;
			$wtotal2[2]=0;
			$wtott[1]=0;
			$wtott[2]=0;
			for ($i=0;$i<=$num;$i++)
			{
				if ($wdata[$i][2] != $unidad)
				{
					if($unidad != "")
					{
						switch ($unidad)
						{
							case "1Q":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES QUIRURGICAS</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";;
							break;
							case "2H":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE HOSPITALIZACION</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "3D":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE DIAGNOSTICO</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "4A":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE ATENCION AMBULATORIA</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "7E":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
									$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES EXTERNAS</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
							break;
							case "5O":
								$wdif=$wtotal1[1]-$wtotal1[2];
								if($wtotal1[2] != 0)
								$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
								else
									$wpor=0;
								echo"<tr><td  bgcolor='#cccccc'><b>TOTAL OTRAS UNIDADES</b></td><td   bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
								$wdif=$wtotal2[1]-$wtotal2[2];
								if($wtotal2[2] != 0)
									$wpor=($wtotal2[1]-$wtotal2[2])/$wtotal2[2] *100;
								else
									$wpor=0;
								echo"<tr><td bgcolor='#99CCFF'><b>TOTAL CLINICA</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
						}
					}
					switch ($wdata[$i][2])
					{
						case "1Q":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES QUIRURGICAS</B></td></tr>";
						break;
						case "2H":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES DE HOSPITALIZACION</B></td></tr>";
						break;
						case "3D":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES DE DIAGNOSTICO</B></td></tr>";
						break;
						case "4A":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES DE ATENCION AMBULATORIA</B></td></tr>";
						break;
						case "5O":
							echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>OTRAS UNIDADES</B></td></tr>";
						break;
					}
					$wtotal1[1]=0;
					$wtotal1[2]=0;
					$unidad=$wdata[$i][2];
				}
				$wtotal1[1]=$wtotal1[1]+$wdata[$i][3];
				$wtotal1[2]=$wtotal1[2]+$wdata[$i][4];
				$wtotal2[1]=$wtotal2[1]+$wdata[$i][3];
				$wtotal2[2]=$wtotal2[2]+$wdata[$i][4];
				$wdif=$wdata[$i][3]-$wdata[$i][4];
				if($wdata[$i][4] != 0)
					$wpor=($wdata[$i][3]-$wdata[$i][4])/$wdata[$i][4]*100;
				else
					$wpor=0;
				if(isset($wdata[$i][4]) and isset($wdata[$i][3]) and $wres == "D")
					echo"<tr><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdif,0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td></tr>";
			}
			if(isset($wtotal1[2]) and isset($wtotal1[1]))
			{
				$wdif=$wtotal1[1]-$wtotal1[2];
				if($wtotal1[2] != 0)
					$wpor=($wtotal1[1]-$wtotal1[2])/$wtotal1[2] *100;
				else
					$wpor=0;
				echo"<tr><td  bgcolor='#cccccc'><b>TOTAL UNIDADES EXTERNAS</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr>";
			}
			if(isset($wtotal2[2]) and isset($wtotal2[1]))
			{
				$wtotal2[1]=$wtotal2[1];
				$wtotal2[2]=$wtotal2[2];
				$wdif=$wtotal2[1]-$wtotal2[2];
				if($wtotal2[2] != 0)
					$wpor=($wtotal2[1]-$wtotal2[2])/$wtotal2[2] *100;
				else
					$wpor=0;
				echo"<tr><td  bgcolor='#FFCCFF'><b>TOTAL  PMLA</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td></tr></table>";
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
