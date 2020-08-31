<html>
<head>
  <title>MATRIX</title>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
	<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>
	<style type="text/css">
		.tipo3{color:#FFFFFF;background:#2A5DB0;font-size:10pt;font-family:Ubuntu;font-weight:bold;text-align:left;border-style:none;}
		.tipo3GRID{color:#E8EEF7;background:#E8EEF7;font-size:1pt;font-family:Arial;font-weight:bold;text-align:center;border-style:none;display:none;}
		.tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}
		.tipoL02GRID1{color:#000066;background:#C3D9FF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;}
		.tipoL02GRID2{color:#000066;background:#C3D9FF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:right;height:1em;}
		.tipoL02GRID1A{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;}
		.tipoL02GRID2A{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;text-align:right;height:1em;}
		#tipoL06GRID{color:#000066;background:#999999;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}
		#tipoL06GRIDB{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}
		#tipoL06GRIDL{color:#000066;background:#999999;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;}
		#tipoL06GRIDR{color:#000066;background:#999999;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;height:1em;}
	</style>
	<script type='text/javascript'>
		var chart;
		$(document).ready(function () 
		{	
			$('#tablaresultado1').LeerTablaAmericas(
			{ 
					empezardesdefila: 1, 
					titulo : 'Ejecucion de Ingresos ' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amchart1',
					filaencabezado : [0,1],
					datosadicionales : [2],
					tipografico : 'column'
			});	
		});
	</script>
	<script>
		function toggleDisplay(id)
		{
			if (id.style.display=="none")
			{
				id.style.display="";
			}
			else 
			{
				id.style.display="none";
			}
		}
		function Graficar(i)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Distribucion de los Ingresos x Segmento ' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amcharts'+i,
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column'
			});
		}
		function GraficarSeg()
		{
			$('#tablaresultado2').LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Ingresos x Segmento de Mercado ' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amchart2',
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column'
			});
		}
	</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Evaluacion Semanal de Ingresos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc158.php Ver. 2016-02-19</b></font></tr></td></table>
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
function comparacion1($vec1,$vec2)
{
	if($vec1[1] < $vec2[1])
		return 1;
	elseif ($vec1[1] > $vec2[1])
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
		

		

		echo "<form action='000001_rc158.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or !isset($wemp) or $wemp == "Seleccione" or !isset($wres)  or !isset($wter) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or (strtoupper ($wter) != "S" and strtoupper ($wter) != "N") or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>EVALUACI&Oacute;N SEMANAL DE INGRESOS POR UNIDAD DE NEGOCIO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntildeo de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Resumido o Detallado ? (R/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wres' size=1 maxlength=1 value='D' readonly=readonly></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Imprimir Solamente Terceros ? (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wter' size=1 maxlength=1 value='N' readonly=readonly></td></tr>";
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
			echo "<h3 OnClick='toggleDisplay(div1)' class=tipo3>EVALUACION SEMANAL DE INGRESOS (click)</h3>";
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
			$query = $query."    and mes =   ".$wper1;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			$dias=(integer)substr($row[1],8,2);
			$mes=(integer)substr($row[1],5,2)-1;
			
			if($num > 0 and $row[0] == "on")
			{
				$datos=array();
				$query = "select mensaje from ".$empresa."_000001 ";
				$query = $query."  where codigo = 1";
				$query = $query."  and ano = ".$wanop;
				$query = $query."  and mes = ".$wper1;
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$wmen="";
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$wmen=$row[0];
				}
				$query = "select sum(Resmon) from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resper = ".$wper1;
				$query = $query."    and resemp = '".$wemp."'";
				$query = $query."    and rescpr = '100'"; 
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$datos[1][0]=$row[0];
				$query = "select sum(Resmon) from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resper = ".$wper1;
				$query = $query."    and resemp = '".$wemp."'";
				$query = $query."    and rescpr = '900'"; 
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$datos[2][0]=$row[0];
				$query = "select sum(Mioinp),sum(Mioint) from ".$empresa."_000063 ";
				$query = $query."  where mioano = ".$wanop;
				$query = $query."    and miomes = ".$wper1;
				$query = $query."    and mioemp = '".$wemp."'";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$datos[4][0]=$row[0];
				$datos[5][0]=$row[1];
				$datos[0][0]=$datos[1][0]+$datos[2][0];
				$datos[3][0]=$datos[4][0]+$datos[5][0];
				$datos[6][0]=1/$d[$mes]*$dias*100;
				$datos[7][0]=$datos[3][0]/$datos[0][0]*100;
				$datos[8][0]=$datos[4][0]/$datos[1][0]*100;
				$datos[9][0]=$datos[5][0]/$datos[2][0]*100;
				$datos[10][0]=$datos[0][0]/$d[$mes];
				$datos[11][0]=$datos[3][0]/$dias;
				$datos[12][0]=($datos[10][0]-$datos[11][0])*-1;
				$datos[13][0]=$datos[11][0]*$d[$mes];
				$datos[14][0]=$datos[13][0]/$datos[0][0]*100;
				$datos[0][1]="INGRESOS PRESUPUESTADOS";
				$datos[1][1]="INGRESOS PROPIOS";
				$datos[2][1]="INGRESOS PARA TERCEROS";
				$datos[3][1]="INGRESOS REALES AL ".$fecha_cierre;
				$datos[4][1]="INGRESOS PROPIOS";
				$datos[5][1]="INGRESOS PARA TERCEROS";
				$datos[6][1]="EJECUCI&Oacute;N ESPERADA";
				$datos[7][1]="EVOLUCI&Oacute;N DE LA FACTURACI&Oacute;N P.M.L.A.";
				$datos[8][1]="INGRESOS PROPIOS";
				$datos[9][1]="INGRESOS PARA TERCEROS";
				$datos[10][1]="INGRESOS PROMEDIO D&iacute;A PRESUPUESTADO";
				$datos[11][1]="INGRESOS PROMEDIO D&iacute;A REAL";
				$datos[12][1]="DIFERENCIA";
				$datos[13][1]="PROYECCI&Oacute;N INGRESOS CON BASE PROMEDIO";
				$datos[14][1]="EJECUCION PROYECTADA CON BASE PROMEDIO";
				echo "<center><table border=1 id='div1'>";
				echo "<tr><td colspan=2 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=2 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=2 align=center>EVALUACION SEMANAL DE INGRESOS</td></tr>";
				echo "<tr><td colspan=2 align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=2 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
				for ($i=0;$i<15;$i++)
				{
					switch ($i)
					{
						case 0:
							echo"<tr><td bgcolor=#99CCFF><b>".$datos[$i][1]."</b></td><td bgcolor=#99CCFF align=right><b>".number_format((double)$datos[$i][0],0,'.',',')."</b></td></tr>";
						break;
						case 3:
							echo"<tr><td bgcolor=#FFCC66><b>".$datos[$i][1]."</b></td><td bgcolor=#FFCC66 align=right><b>".number_format((double)$datos[$i][0],0,'.',',')."</b></td></tr>";
						break;
						case 6:
							echo"<tr><td colspan=2>&nbsp</td></tr>";
							echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
						break;
						case 7:
							echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
						break;
						case 8:
							echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
						break;
						case 9:
							echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
						break;
						case 14:
							echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],2,'.',',')."%</td></tr>";
						break;
						default:
							echo"<tr><td>".$datos[$i][1]."</td><td align=right>".number_format((double)$datos[$i][0],0,'.',',')."</td></tr>";
						break;
					}
				}
				
				$datos[1][0]=$datos[1][0]/1000000;
				$datos[2][0]=$datos[2][0]/1000000;
				$datos[4][0]=$datos[4][0]/1000000;
				$datos[5][0]=$datos[5][0]/1000000;
				echo"<tr><td colspan=2>".$wmen."</td></tr>";
				echo "<tr><td colspan=2><center><table border=1 id='tablaresultado1' class=tipo3GRID>";
				echo "<tr><td class=tipo3>INGRESOS</td><td class=tipo3>PRESUPUESTADOS</td><td class=tipo3>REALES</td></tr>";
				echo "<tr><td class=tipo3>PROPIOS</td><td align=right>".number_format((double)$datos[1][0],0,'','')."</td><td align=right>".number_format((double)$datos[4][0],0,'','')."</td></tr>";
				echo "<tr><td class=tipo3>PARA TERCEROS</td><td align=right>".number_format((double)$datos[2][0],0,'','')."</td><td align=right>".number_format((double)$datos[5][0],0,'','')."</td></tr>";
				echo "</table>";
				echo "</td></tr>";
				echo "<tr><td colspan=2><table align='center' >";
				echo "<tr>";
				echo "<td><div id='amchart1' style='width:800px; height:400px;'></div></td>";
				echo "</tr>";
				echo "</table>";
				echo "</td></tr>";
				echo "</table></center>";
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO NO!! ESTA CERRADO -- LLAME A COSTOS Y PRSUPUESTOS</MARQUEE></FONT>";
				echo "<br><br>";			
			}
			echo "<h3 OnClick='toggleDisplay(div2)' class=tipo3>EVALUACI&Oacute;N SEMANAL DE INGRESOS POR UNIDAD DE NEGOCIO (click)</h3>";
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
			$query = $query."    and Emp = '".$wemp."' ";
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
				$query = $query."    and miomes = ".$wper1;
				$query = $query."    and mioemp = '".$wemp."'";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$gtotal=$row[0];
				$query = "select Rescco,Cconom,Ccouni,sum(Resmon) from ".$empresa."_000043,".$empresa."_000005 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resper = ".$wper1;
				$query = $query."    and resemp = '".$wemp."'";
				if($wter == "N")
					$query = $query."    and rescpr = '100'"; 
				else
					$query = $query."    and rescpr = '900'"; 
				$query = $query."    and rescco = ccocod  ";
				$query = $query."    and resemp = ccoemp  ";
				$query = $query."    group by rescco,cconom,ccouni  ";
				$query = $query."    order by ccouni ,rescco ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($wter == "N")
					$query = "select Miocco,Cconom,Ccouni,sum(Mioinp) from ".$empresa."_000063,".$empresa."_000005 ";
				else
					$query = "select Miocco,Cconom,Ccouni,sum(Mioint) from ".$empresa."_000063,".$empresa."_000005 ";
				$query = $query."  where mioano = ".$wanop;
				$query = $query."    and miomes = ".$wper1;
				$query = $query."    and mioemp = '".$wemp."'";
				$query = $query."    and miocco = ccocod   ";
				$query = $query."    and mioemp = ccoemp   ";
				$query = $query."   group by miocco,cconom,ccouni  ";
				$query = $query."   order by ccouni,miocco ";
				$err2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($err2);
				echo "<center><table border=1 id='div2' style='display: none'>";
				echo "<tr><td colspan=5 align=center>EVALUACI&Oacute;N SEMANAL DE INGRESOS POR UNIDAD DE NEGOCIO</td></tr>";
				echo "<tr><td colspan=5 align=center>EMPRESA : ".$wempt."</td></tr>";
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
					$query = $query."    and resemp = '".$wemp."'";
					$query = $query."    and rescpr = '900'"; 
					$err1 = mysql_query($query,$conex);
					$query = "select sum(Mioint) from ".$empresa."_000063 ";
					$query = $query."  where mioano = ".$wanop;
					$query = $query."    and miomes = ".$wper1;
					$query = $query."    and mioemp = '".$wemp."'";
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
						echo"<tr><td  bgcolor='#FFFF00' colspan=5><b><font color='#CC0000'>* UNIDADES QUE NO CUMPLEN LA EJECUCION ESPERADA EN INGRESOS DE : ".number_format((double)$wesp,2,'.',',')."%</FONT></B></td></tr></table></center>";
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
			echo "<h3 OnClick='toggleDisplay(div3)' class=tipo3>INGRESOS POR SEGMENTO DE MERCADO (click)</h3>";
			$query = "SELECT cierre_ingresos,fecha from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = ".$wper1;
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$fecha_cierre=$row[1];
			if($num > 0 and $row[0] == "on")
			{
				$query = "select Segdes,Segcod,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000045 ";
				$query = $query."  where mioano = ".$wanop;
				$query = $query."    and miomes = ".$wper1;
				$query = $query."    and mioemp = '".$wemp."'";
				$query = $query."    and miocco between '0' and 'z'";
				$query = $query."    and mionit = epmcod   ";
				$query = $query."    and mioemp = empemp   ";
				$query = $query."    and empseg = segcod   ";
				$query = $query."  group by Segdes,Segcod  ";
				$query = $query."  order by Segdes,Segcod ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				echo "<center><table border=1 id='div3' style='display: none'>";
				echo "<center><tr><td><table border=1>";
				echo "<tr><td colspan=3 align=center>INGRESOS POR SEGMENTO DE MERCADO</td></tr>";
				echo "<tr><td colspan=3 align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=3 align=center>PERIODO  : ".$wper1."<B> ACTUALIZADO A : ".$fecha_cierre."</B></td></tr>";
				echo "<tr><td class=tipo3>SEGMENTO</td><td class=tipo3>VALOR FACTURADO</td><td class=tipo3>% PART</td></tr>";
				$wdata=array();
				$val=0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wdata[$i][0]=$row[0];
					$wdata[$i][1]=$row[2];
					$wdata[$i][2]=$row[1];
					$val+=$row[2];
				}
				usort($wdata,'comparacion1');
				for ($i=0;$i<$num;$i++)
				{
					if($val !=0 )
						$pro=$wdata[$i][1]/$val * 100;
					else
						$pro=0;
					echo"<tr><td>".$wdata[$i][0]."</td><td align=right OnClick='toggleDisplay(div3".$i.")'>".number_format((double)$wdata[$i][1],0,'.',',')."</td><td  align=right>".number_format((double)$pro,2,'.',',')."</td></tr>";
					echo "<tr id='div3".$i."' style='display: none'><td colspan=3><center><table align=center border=1 class=tipoTABLEGRID>";
					$query = "select sum(Mioito) from ".$empresa."_000063 ";
					$query = $query."  where mioano = ".$wanop;
					$query = $query."    and miomes = ".$wper1;
					$query = $query."    and mioemp = '".$wemp."'";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					$gtotal=$row1[0];
					$query = "select empdes,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061 ";
					$query = $query."  where mioano = ".$wanop;
					$query = $query."    and miomes = ".$wper1;
					$query = $query."    and mioemp = '".$wemp."'";
					$query = $query."    and miocco between '0' and 'z'";
					$query = $query."    and mionit = epmcod   ";
					$query = $query."    and mioemp = empemp   ";
					$query = $query."    and empseg = '".$wdata[$i][2]."'";
					$query = $query."   group by empdes  ";
					$query = $query."   order by empdes";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					echo "<tr><td colspan=4 id=tipoL06GRIDB>DISTRIBUCI&Oacute;N DE LOS INGRESOS X SEGMENTO</td></tr>";
					echo "<tr><td id=tipoL06GRID>ENTIDAD</td><td id=tipoL06GRID>VALOR FACTURADO</td><td id=tipoL06GRID>% PART EN SEG.</td><td id=tipoL06GRID>% PART EN ING. TOT.</td></tr>";
					$wdata1=array();
					$val1=0;
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						$wdata1[$j][0]=$row1[0];
						$wdata1[$j][1]=$row1[1];
						$val1+=$row1[1];
					}
					usort($wdata1,'comparacion1');
					for ($j=0;$j<$num1;$j++)
					{
						if($val1 != 0)
							$pro1=$wdata1[$j][1]/$val1 * 100;
						else
							$pro1=0;
						if($gtotal != 0)
							$pro2=$wdata1[$j][1]/$gtotal * 100;
						else
							$pro2=0;
						if($j % 2 == 0)
							$ts="A";
						else
							$ts="";
						echo"<tr><td class=tipoL02GRID1".$ts.">".$wdata1[$j][0]."</td><td class=tipoL02GRID2".$ts.">".number_format((double)$wdata1[$j][1],0,'.',',')."</td><td class=tipoL02GRID2".$ts.">".number_format((double)$pro1,2,'.',',')."</td><td  class=tipoL02GRID2".$ts.">".number_format((double)$pro2,2,'.',',')."</td></tr>";
					}
					$pro1= 100;
					if($val1 != 0)
						$pro2=$val/$gtotal *100;
					else
						$pro2=0;
					echo"<tr><td id=tipoL06GRIDL>TOTAL FACTURADO</td><td id=tipoL06GRIDR>".number_format((double)$val1,0,'.',',')."</td><td id=tipoL06GRIDR>".number_format((double)$pro1,2,'.',',')."</td><td  id=tipoL06GRIDR>".number_format((double)$pro2,2,'.',',')."</td></tr>";
					
					echo "<tr><td colspan=4><center><table border=1 id='tablaresultados".$i."' class=tipo3GRID>";
					echo "<tr><td>SEGMENTO</td><td>VALOR FACTURADO</td></tr>";
					for ($j=0;$j<$num1;$j++)
					{
						$wdata1[$j][1] = (double)$wdata1[$j][1] / 1000000;
						$wdata1[$j][1] = round($wdata1[$j][1],2);
						echo "<tr><td>".$wdata1[$j][0]."</td><td>".$wdata1[$j][1]."</td></tr>";
					}
					echo "</table></center>";
					echo "</td></tr>";
					echo "<tr><td colspan=4><button type='button' onclick='toggleDisplay(seg".$i.");Graficar(".$i.");'>Graficar</button></td></tr>";
					echo "<tr id='seg".$i."' style='display: none'><td colspan=4><table align='center' >";
					echo "<tr>";
					echo "<td><div id='amcharts".$i."' style='width:900px; height:600px;'></div></td>";
					echo "</tr>";
					echo "</table>";
					echo "</td></tr>";
					echo "</table></center></td></tr>";
				}
				$pro= 100;
				echo"<tr><td bgcolor='#99CCFF'><b>TOTAL FACTURADO</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$val,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$pro,2,'.',',')."</b></td></tr>";
				echo "<tr><td colspan=3><center><table border=1 id='tablaresultado2' class=tipo3GRID>";
				echo "<tr><td>SEGMENTO</td><td>VALOR FACTURADO</td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$wdata[$i][1] = $wdata[$i][1] / 1000000;
					$wdata[$i][1] = round($wdata[$i][1],0);
					echo "<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td></tr>";
				}
				echo "</table></center>";
				echo "</td></tr>";
				
				echo "<tr><td colspan=4><button type='button' onclick='toggleDisplay(segmento);GraficarSeg();'>Graficar</button></td></tr>";
				echo "<tr id='segmento' style='display: none'><td colspan=3><table align='center' >";
				echo "<tr>";
				echo "<td><div id='amchart2' style='width:900px; height:600px;'></div></td>";
				echo "</tr>";
				echo "</table>";
				echo "</td></tr>";
				echo "</table></center>";
				echo "</td></tr>";
				echo "</table></center>";
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
