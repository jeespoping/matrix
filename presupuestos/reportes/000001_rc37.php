<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Evaluacion Semanal de Ingresos por Unidad de Negocio</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc37.php Ver. 2012-08-23</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_rc37.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1)  or !isset($wres)  or !isset($wter) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or (strtoupper ($wter) != "S" and strtoupper ($wter) != "N") or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACI&Oacute;N SEMANAL DE INGRESOS POR UNIDAD DE NEGOCIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntildeo de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Imprimir Solamente Terceros ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wter' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
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
			$wres=strtoupper ($wres);
			$wter=strtoupper ($wter);
			$query = "SELECT cierre_ingresos,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes =   ".$wper1;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			$dias=(integer)substr($row[1],8,2);
			$mes=(integer)substr($row[1],5,2)-1;
			$wesp=1 / $d[$mes] * $dias * 100;
			if($num > 0 and $row[0] == "on")
			{
				$query = "select sum(Mioito) from ".$empresa."_000063 ";
				$query = $query."  where mioano = ".$wanop;
				$query = $query."      and miomes = ".$wper1;
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$gtotal=$row[0];
				$query = "select Rescco,Cconom,Ccouni,sum(Resmon) from ".$empresa."_000043,".$empresa."_000005 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resper = ".$wper1;
				if($wter == "N")
					$query = $query."    and rescpr = '100'"; 
				else
					$query = $query."    and rescpr = '900'"; 
				$query = $query."    and rescco = ccocod  ";
				$query = $query."    group by rescco,cconom,ccouni  ";
				$query = $query."    order by ccouni ,rescco ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($wter == "N")
					$query = "select Miocco,Cconom,Ccouni,sum(Mioinp) from ".$empresa."_000063,".$empresa."_000005 ";
				else
					$query = "select Miocco,Cconom,Ccouni,sum(Mioint) from ".$empresa."_000063,".$empresa."_000005 ";
				$query = $query."  where mioano = ".$wanop;
				$query = $query."      and miomes = ".$wper1;
				$query = $query."      and miocco = ccocod   ";
				$query = $query."    group by miocco,cconom,ccouni  ";
				$query = $query."    order by ccouni,miocco ";
				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);
				echo "<table border=1>";
				echo "<tr><td colspan=5 align=right  bgcolor=#dddddd><A HREF='/matrix/presupuestos/reportes/000001_rc38.php?wanop=".$wanop."&wper1=".$wper1."' target='_blank'><b>Ver Segmentacion</b></a></td></tr>";
				echo "<tr><td colspan=5 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=5 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=5 align=center>EVALUACI&Oacute;N SEMANAL DE INGRESOS POR UNIDAD DE NEGOCIO</td></tr>";
				echo "<tr><td colspan=5 align=center>PERIODO  : ".$wper1." A&NtildeO : ".$wanop."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
				echo "<tr><td><b>UNIDAD</b></td><td><b>REAL</b></td><td><b>PRESUPUESTADO</b></td><td align=right><b>EVOLUCION</b></td><td align=right><b>% PART.</b></td></tr>";
				$wdata=array();
				$k1=0;
				$k2=0;
				$num=-1;
				if ($num1 ==  0)
				{
					$k1=1;
					$row1[0]="zzzz";
					$row1[1]=" ";
					$row1[2]=0;
					$row1[3]="";
					$kla1="zzzzzzzz";
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
					$row2[0]="zzzz";
					$row2[1]=" ";
					$row2[2]=0;
					$row2[3]="";
					$kla2="zzzzzzzz";
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
							$row1[0]="zzzz";
							$kla1="zzzzzzzz";
						}
						else
						{
							$row1 = mysql_fetch_array($err1);
							$kla1=substr($row1[2],0,2).$row1[0];
						}
						if($k2 > $num2)
						{
							$row2[0]="zzzz";
							$kla2="zzzzzzzz";
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
							$row1[0]="zzzz";
							$kla1="zzzzzzzz";
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
							$row2[0]="zzzz";
							$kla2="zzzzzzzz";
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
				$ita=0;
				$unidad="";
				$wtotal1[1]=0;
				$wtotal1[2]=0;
				$wtotal2[1]=0;
				$wtotal2[2]=0;
				for ($i=0;$i<=$num;$i++)
				{
					if ($wdata[$i][2] != $unidad)
					{
						if($unidad != "")
						{
							switch ($unidad)
							{
								case "1Q":
									if($wtotal1[1] != 0)
										$wpor=($wtotal1[2]/$wtotal1[1])*100;
									else
										$wpor=0;
									if($gtotal !=0)
										$wdif=($wtotal1[2]/$gtotal)*100;
									else
										$wdif=0;
									if($wpor < $wesp)
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES QUIRURGICAS</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									else
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES QUIRURGICAS</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
								break;
								case "2H":
									if($wtotal1[1] != 0)
										$wpor=($wtotal1[2]/$wtotal1[1])*100;
									else
										$wpor=0;
									if($gtotal !=0)
										$wdif=($wtotal1[2]/$gtotal)*100;
									else
										$wdif=0;
									if($wpor < $wesp)
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES HOSPITALARIAS</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									else
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES HOSPITALARIAS</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
								break;
								case "2SF":
									if($wtotal1[1] != 0)
										$wpor=($wtotal1[2]/$wtotal1[1])*100;
									else
										$wpor=0;
									if($gtotal !=0)
										$wdif=($wtotal1[2]/$gtotal)*100;
									else
										$wdif=0;
									if($wpor < $wesp)
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL SERVICIO FARMACEUTICO</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									else
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL SERVICIO FARMACEUTICO</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
								break;
								case "3D":
									if($wtotal1[1] != 0)
										$wpor=($wtotal1[2]/$wtotal1[1])*100;
									else
										$wpor=0;
									if($gtotal !=0)
										$wdif=($wtotal1[2]/$gtotal)*100;
									else
										$wdif=0;
									if($wpor < $wesp)
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE DIAGNOSTICO</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									else
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDADES DE DIAGNOSTICO</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
								break;
								case "4A":
									if($wtotal1[1] != 0)
										$wpor=($wtotal1[2]/$wtotal1[1])*100;
									else
										$wpor=0;
									if($gtotal !=0)
										$wdif=($wtotal1[2]/$gtotal)*100;
									else
										$wdif=0;
									if($wpor < $wesp)
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDAD DE URGENCIAS/EMERGENCIAS Y CONSULTA EXTERNA</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									else
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL UNIDAD DE URGENCIAS/EMERGENCIAS Y CONSULTA EXTERNA</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
								break;
								case "7E":
									if($wtotal1[1] != 0)
										$wpor=($wtotal1[2]/$wtotal1[1])*100;
									else
										$wpor=0;
									if($gtotal !=0)
										$wdif=($wtotal1[2]/$gtotal)*100;
									else
										$wdif=0;
									if($wpor < $wesp)
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL FARMACIA COMERCIAL</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									else
										echo"<tr><td bgcolor='#cccccc'><b>TOTAL FARMACIA COMERCIAL</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
								break;
								case "5O":
									if($wtotal1[1] != 0)
										$wpor=($wtotal1[2]/$wtotal1[1])*100;
									else
										$wpor=0;
									if($gtotal !=0)
										$wdif=($wtotal1[2]/$gtotal)*100;
									else
										$wdif=0;
									if($wpor < $wesp)
										echo"<tr><td  bgcolor='#cccccc'><b>TOTAL OTRAS UNIDADES</b></td><td   bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									else
										echo"<tr><td  bgcolor='#cccccc'><b>TOTAL OTRAS UNIDADES</b></td><td   bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									if($wter == "N")
									{
										if($wtotal2[1] != 0)
											$wpor=($wtotal2[2]/$wtotal2[1])*100;
										else
											$wpor=0;
										if($gtotal !=0)
											$wdif=($wtotal2[2]/$gtotal)*100;
										else
											$wdif=0;
										if($wpor < $wesp)
											echo"<tr><td bgcolor='#99CCFF'><b>TOTAL CLINICA</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
										else
											echo"<tr><td bgcolor='#99CCFF'><b>TOTAL CLINICA</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#99CCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
									}
							}
						}
						switch ($wdata[$i][2])
						{
							case "1Q":
								echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES QUIRURGICAS</B></td></tr>";
							break;
							case "2H":
								echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES HOSPITALARIAS</B></td></tr>";
							break;
							case "2SF":
								echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>SERVICIO FARMACEUTICO</B></td></tr>";
							break;
							case "3D":
								echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDADES DE DIAGNOSTICO</B></td></tr>";
							break;
							case "4A":
								echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>UNIDAD DE URGENCIAS/EMERGENCIAS Y CONSULTA EXTERNA</B></td></tr>";
							break;
							case "5O":
								echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>OTRAS UNIDADES</B></td></tr>";
							break;
							case "7E":
								echo "<tr><td bgcolor='#FFFFFF' colspan=5><b>FARMACIA COMERCIAL</B></td></tr>";
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
					if($wdata[$i][3] != 0)
						$wpor=($wdata[$i][4]/$wdata[$i][3])*100;
					else
						$wpor=0;
					if($gtotal !=0)
						$wdif=($wdata[$i][4]/$gtotal)*100;
					else
						$wdif=0;
					if(isset($wdata[$i][4]) and isset($wdata[$i][3]) and $wres == "D")
						if($wpor < $wesp)
							echo"<tr><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right bgcolor='#FFFF00'>".number_format((double)$wpor,2,'.',',')."</td><td align=right>".number_format((double)$wdif,2,'.',',')."</td></tr>";
						else
							echo"<tr><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wpor,2,'.',',')."</td><td align=right>".number_format((double)$wdif,2,'.',',')."</td></tr>";
				}
				if($wtotal1[2] !=0 and $wtotal1[1] !=0)
				{
					if($wtotal1[1] != 0)
						$wpor=($wtotal1[2]/$wtotal1[1])*100;
					else
						$wpor=0;
					if($gtotal !=0)
						$wdif=($wtotal1[2]/$gtotal)*100;
					else
						$wdif=0;
					if($wpor < $wesp)
						echo"<tr><td  bgcolor='#cccccc'><b>TOTAL FARMACIA COMERCIAL</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
					else
						echo"<tr><td  bgcolor='#cccccc'><b>TOTAL FARMACIA COMERCIAL</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[2],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wtotal1[1],0,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td  bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
				}
				if(isset($wtotal2[2]) and isset($wtotal2[1]))
				{
					if($wtotal2[1] != 0)
						$wpor=($wtotal2[2]/$wtotal2[1])*100;
					else
						$wpor=0;
					if($gtotal !=0)
						$wdif=($wtotal2[2]/$gtotal)*100;
					else
						$wdif=0;
					if ($wter == "N")
						if($wpor < $wesp)
							echo"<tr><td  bgcolor='#FFCC99'><b>TOTAL INGRESOS PROPIOS</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
						else
							echo"<tr><td  bgcolor='#FFCC99'><b>TOTAL INGRESOS PROPIOS</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
					else
						if($wpor < $wesp)
							echo"<tr><td  bgcolor='#FFCC99'><b>TOTAL INGRESOS PARA TERCEROS</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
						else
							echo"<tr><td  bgcolor='#FFCC99'><b>TOTAL INGRESOS PARA TERCEROS</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td  bgcolor='#FFCC99' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
				}
				if ($wter == "N")
				{
					$query = "select sum(Resmon) from ".$empresa."_000043 ";
					$query = $query."  where resano = ".$wanop;
					$query = $query."    and resper = ".$wper1;
					$query = $query."    and rescpr = '900'"; 
					$err1 = mysql_query($query,$conex);
					$query = "select sum(Mioint) from ".$empresa."_000063 ";
					$query = $query."  where mioano = ".$wanop;
					$query = $query."      and miomes = ".$wper1;
					$err2 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$row2 = mysql_fetch_array($err2);
					if($row1[0] != 0)
						$wpor=($row2[0]/$row1[0])*100;
					else
						$wpor=0;
					if($gtotal !=0)
						$wdif=($row2[0]/$gtotal)*100;
					else
						$wdif=0;
					if($wpor < $wesp)
						echo"<tr><td  bgcolor='#CCFFFF'><b>TOTAL INGRESOS PARA TERCEROS</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$row2[0],0,'.',',')."</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$row1[0],0,'.',',')."</b></td><td  bgcolor='#CCFFFF' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
					else
						echo"<tr><td  bgcolor='#CCFFFF'><b>TOTAL INGRESOS PARA TERCEROS</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$row2[0],0,'.',',')."</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$row1[0],0,'.',',')."</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td  bgcolor='#CCFFFF' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
					if(isset($wtotal2[2]) and isset($wtotal2[1]))
					{
						$wtotal2[1]=$wtotal2[1]+$row1[0];
						$wtotal2[2]=$wtotal2[2]+$row2[0];
						if($wtotal2[1] != 0)
							$wpor=($wtotal2[2]/$wtotal2[1])*100;
						else
							$wpor=0;
						if($gtotal !=0)
							$wdif=($wtotal2[2]/$gtotal)*100;
						else
							$wdif=0;
						if($wpor < $wesp)
							echo"<tr><td  bgcolor='#FFCCFF'><b>TOTAL INGRESOS PMLA</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><font color='#CC0000'><b>".number_format((double)$wpor,2,'.',',')."</font></b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
						else
							echo"<tr><td  bgcolor='#FFCCFF'><b>TOTAL INGRESOS PMLA</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[2],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wtotal2[1],0,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wpor,2,'.',',')."</b></td><td  bgcolor='#FFCCFF' align=right><b>".number_format((double)$wdif,2,'.',',')."</b></td></tr>";
						echo"<tr><td  bgcolor='#FFFF00' colspan=5><b><font color='#CC0000'>* UNIDADES QUE NO CUMPLEN LA EJECUCION ESPERADA EN INGRESOS DE : ".number_format((double)$wesp,2,'.',',')."%</FONT></B></td></tr></table>";
					}
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
