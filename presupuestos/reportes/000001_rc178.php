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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Evaluacion Semanal de Ingresos x Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc178.php Ver. 2017-06-21</b></font></tr></td></table>
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
		

		

		echo "<form action='000001_rc178.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or !isset($wemp) or !isset($wccof) or $wemp == "Seleccione" or !isset($wres)  or !isset($wter) or (strtoupper ($wres) != "R" and strtoupper ($wres) != "D") or (strtoupper ($wter) != "S" and strtoupper ($wter) != "N") or $wper1 < 1 or $wper1 > 12)
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
				$query = "select sum(Resmon) from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resper = ".$wper1;
				$query = $query."    and rescco = '".$wcco1."'";
				$query = $query."    and resemp = '".$wemp."'";
				$query = $query."    and rescpr = '100'"; 
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$datos[1][0]=$row[0];
				$query = "select sum(Resmon) from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and resper = ".$wper1;
				$query = $query."    and rescco = '".$wcco1."'";
				$query = $query."    and resemp = '".$wemp."'";
				$query = $query."    and rescpr = '900'"; 
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$datos[2][0]=$row[0];
				$query = "select sum(Mioinp),sum(Mioint) from ".$empresa."_000063 ";
				$query = $query."  where mioano = ".$wanop;
				$query = $query."    and miomes = ".$wper1;
				$query = $query."    and miocco = '".$wcco1."'";
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
				echo "<tr><td colspan=2 align=center>UNIDAD DE PROCESO : ".$wccof."</td></tr>";
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
		}
	}
?>
</body>
</html>
