<html>
<head>
  <title>MATRIX Ver. 2016-01-20</title>
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
		.tipoL01{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL01R{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		.tipoL02R{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		.tipoL02L{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		.tipoL03{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL04{color:#000066;background:#DDDDDD;font-size:12pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		.tipoL05{color:#000066;background:#999999;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	.tipotot{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;}
    	.tipotit{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;}
    	.tipouti{color:#000066;background:#81F781;font-size:12pt;font-family:Arial;font-weight:bold;}
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
		function enter()
		{
			document.forms.analisis.submit();
		}
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
					titulo : 'Ingresos Operacionales ' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amcharts'+i,
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column'
			});
		}
		function GraficarE(i)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Estadisticas ' ,
					tituloy: '',
					divgrafica: 'amcharts'+i,
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column'
			});
		}
		function GraficarIP(i)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Ingreso Promedio' ,
					tituloy: '',
					divgrafica: 'amcharts'+i,
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column'
			});
		}
		function GraficarR(i)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Resultados' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amcharts'+i,
					filaencabezado : [0,1],
					datosadicionales : 'todo',
					tipografico : 'column',
			});
		}
		function GraficarM(i)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Margenes' ,
					tituloy: 'Porcentaje',
					divgrafica: 'amcharts'+i,
					filaencabezado : [0,1],
					datosadicionales : 'todo',
					tipografico : 'column',
			});
		}
		function GraficarS(i,year)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Segmentacion de Ingresos '+year ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amcharts'+i,
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'torta'
			});
		}
	</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
function buscar($data,$key,$num)
{
	for ($i=0;$i<$num;$i++)
	{
		if($data[$i][0] == $key)
			return $i;
	}
	return -1;
}
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
		

		

		echo "<form name='analisis' action='000001_rc159.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wccoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wccof) or !isset($wgru) or !isset($wanoi) or !isset($wperi1)  or !isset($wperi2) or $wperi1 < 1 or $wperi1 > 12 or $wperi2 < 1 or $wperi2 > 12 or $wperi1 > $wperi2 or !isset($wanof) or !isset($wperf1)  or !isset($wperf2) or $wperf1 < 1 or $wperf1 > 12 or $wperf2 < 1 or $wperf2 > 12 or $wperf1 > $wperf2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ANALISIS DE UNIDADES Ver. 2014-03-11</td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS INICIALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o</td>";
			if(isset($wanoi))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' value='".$wanoi."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			if(isset($wperi1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi1' value='".$wperi1."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			if(isset($wperi2))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi2' value='".$wperi2."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS FINALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o</td>";
			if(isset($wanof))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' value='".$wanof."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			if(isset($wperf1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf1' value='".$wperf1."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			if(isset($wperf2))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf2' value='".$wperf2."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>CENTROS DE COSTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
			if(isset($wccoi))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' value='".$wccoi."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
			if(isset($wccof))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' value='".$wccof."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Grupo de Unidades</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT ccouni  from ".$empresa."_000005 group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wgru'>";
				echo "<option>Todos</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
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
			echo "<input type='HIDDEN' name= 'wanoi' value='".$wanoi."'>";
			echo "<input type='HIDDEN' name= 'wperi1' value='".$wperi1."'>";
			echo "<input type='HIDDEN' name= 'wperi2' value='".$wperi2."'>";
			echo "<input type='HIDDEN' name= 'wanof' value='".$wanof."'>";
			echo "<input type='HIDDEN' name= 'wperf1' value='".$wperf1."'>";
			echo "<input type='HIDDEN' name= 'wperf2' value='".$wperf2."'>";
			echo "<input type='HIDDEN' name= 'wccoi' value='".$wccoi."'>";
			echo "<input type='HIDDEN' name= 'wccof' value='".$wccof."'>";
			echo "<IMG SRC='/matrix/images/medical/root/retornar.png' OnClick='enter()'>";
			//echo "<input type='submit' value='RETORNAR'>";
			$query = "SELECT sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanoi;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and meccco between '".$wccoi."' and '".$wccof."'";
			$query = $query."    and meccpr = '100' ";
			$query = $query."    and meccco = Ccocod";
			$query = $query."    and mecemp = ccoemp ";
			if($wgru != "Todos")
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query = $query."    and mecmes between ".$wperi1." and ".$wperi2;
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			
			$query = "SELECT sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanof;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and meccco between '".$wccoi."' and '".$wccof."'";
			$query = $query."    and meccpr = '100' ";
			$query = $query."    and meccco = Ccocod";
			$query = $query."    and mecemp = ccoemp ";
			if($wgru != "Todos")
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query = $query."    and mecmes between ".$wperf1." and ".$wperf2;
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			
			$query = "SELECT sum(rvpvpr) from ".$empresa."_000044,".$empresa."_000005 ";
			$query = $query."  where rvpano = ".$wanof;
			$query = $query."    and rvpemp = '".$wemp."'";
			$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
			$query = $query."    and rvpcpr = '100' ";
			$query = $query."    and rvpcco = Ccocod";
			$query = $query."    and rvpemp = ccoemp ";
			if($wgru != "Todos")
			{
				$query = $query."    and Ccouni = '".$wgru."' ";
			}
			$query = $query."    and rvpper between ".$wperf1." and ".$wperf2;
			$err3 = mysql_query($query,$conex);
			$num3 = mysql_num_rows($err3);
			
			echo "<center><table border=0 CELLPADDING=7>";
			echo "<tr><td colspan=7 class=tipoL05>ANALISIS DE UNIDADES</td></tr>";
			echo "<tr><td colspan=7 class=tipoL03>Datos Iniciales</td></tr>";
			echo "<tr><td colspan=7 class=tipoL03>A&Ntilde;O : ".$wanoi." MES INICIAL : ".$wperi1. " MES FINAL : ".$wperi2."</td></tr>";
			echo "<tr><td colspan=7 class=tipoL03>Datos Finales</td></tr>";
			echo "<tr><td colspan=7 class=tipoL03>A&Ntilde;O : ".$wanof." MES INICIAL : ".$wperf1. " MES FINAL : ".$wperf2."</td></tr>";
			echo "<tr><td colspan=7 class=tipoL03>UNIDAD INICIAL : ".$wccoi. " UNIDAD FINAL : ".$wccof."</td></tr>";
			echo "<tr><td colspan=7 class=tipoL03>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
			echo "<tr><td colspan=7 class=tipoL03>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=7 class=tipoL04>INGRESOS</td></tr>";
			echo "<tr><td class=tipoL01>Rubro</td><td class=tipoL01>Real ".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL01>Real ".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL01>Dif.</td><td class=tipoL01>% Var.</td><td class=tipoL01>Ppto ".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL01>% Ejec</td></tr>";
			$wdata=array();
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wdata[0]=$row1[0] / (1000000);
			}
			else
				$wdata[0]=0;
			if($num2 > 0)
			{
				$row2 = mysql_fetch_array($err2);
				$wdata[1]=$row2[0] / (1000000);
			}
			else
				$wdata[1]=0;
			if($num3 > 0)
			{
				$row3 = mysql_fetch_array($err3);
				$wdata[2]=$row3[0] / (1000000);
			}
			else
				$wdata[2]=0;
			$wdata[3]=$wdata[1]-$wdata[0];
			$wdata[4]=(($wdata[1]/$wdata[0]) -1)*100;
			$wdata[5]=($wdata[1]/$wdata[2])*100;
			$id=2;
			$ing0=$wdata[0];
			$ing1=$wdata[1];
			echo "<tr><td class=tipoL02L>INGRESOS OPERACIONALES</td><td class=tipoL02R>".number_format((double)$wdata[0],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[3],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[4],2,'.',',')."%</td><td class=tipoL02R>".number_format((double)$wdata[2],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[5],2,'.',',')."%</td></tr>";
			echo "<tr><td colspan=7><button type='button' onclick='toggleDisplay(seg".$id.");Graficar(".$id.");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='Graficar(".$id.");'></td></tr>";
			echo "<tr id='seg".$id."' style='display: block'><td colspan=7><table align='center' >";
			echo "<tr>";
			echo "<td><div id='amcharts".$id."' style='width:900px; height:400px;'></div></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			echo "<center><table border=1 id='tablaresultados".$id."' class=tipo3GRID>";
			echo "<tr><td>ITEM</td><td>VALOR</td></tr>";
			echo "<tr><td class=tipoL01>Real ".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL02R>".number_format((double)$wdata[0],0,'.','')."</td></tr>";
			echo "<tr><td class=tipoL01>Real ".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL02R>".number_format((double)$wdata[1],0,'.','')."</td></tr>";
			echo "<tr><td class=tipoL01>Ppto ".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL02R>".number_format((double)$wdata[2],0,'.','')."</td></tr>";
			echo "</table></center><br>";
			
			$seg=array();
			$seg[0][0]="EPS";
			$seg[1][0]="PREPAGADAS";
			$seg[2][0]="IPS";
			$seg[3][0]="ASEGURADORAS";
			$seg[4][0]="PARTICULARES";
			$seg[5][0]="SOAT";
			$seg[6][0]="OTROS";
			$seg[0][1]=0;
			$seg[1][1]=0;
			$seg[2][1]=0;
			$seg[3][1]=0;
			$seg[4][1]=0;
			$seg[5][1]=0;
			$seg[6][1]=0;
			echo "<center><table border=0>";
			echo "<tr><td class=tipoL04 colspan=2>SEGMENTACION DE INGRESOS ".$wanoi."/".$wperi1."-".$wperi2." y ".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			if($wgru == "Todos")
			{
				$query  = "select mioano,empseg,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061 ";
				$query .= " where mioano = ".$wanoi;
				$query .= "   and mioemp = '".$wemp."'";
				$query .= "   and miomes between ".$wperi1." and ".$wperi2;
				$query .= "   and miocco between '".$wccoi."' and '".$wccof."'";
				$query .= "   and mionit = epmcod ";
				$query .= "   and mioemp = empemp ";
				$query .= " group by 1,2 ";
				$query .= " order by 1,2 ";
			}
			else
			{
				$query  = "select mioano,empseg,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000005 ";
				$query .= " where mioano = ".$wanoi;
				$query .= "   and mioemp = '".$wemp."'";
				$query .= "   and miomes between ".$wperi1." and ".$wperi2;
				$query .= "   and miocco between '".$wccoi."' and '".$wccof."'";
				$query .= "   and miocco = ccocod ";
				$query .= "   and mioemp = ccoemp ";
				$query .= "   and ccouni = '".$wgru."' ";
				$query .= "   and mionit = epmcod ";
				$query .= "   and mioemp = empemp ";
				$query .= " group by 1,2 ";
				$query .= " order by 1,2 ";
			}
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					switch ($row[1])
					{
						case "EPS":
							$seg[0][1] += $row[2];
						break;
						case "MP":
							$seg[1][1] += $row[2];
						break;
						case "IPS":
							$seg[2][1] += $row[2];
						break;
						case "ASEG":
							$seg[3][1] += $row[2];
						break;
						case "PPJ":
							$seg[4][1] += $row[2];
						break;
						case "PPN":
							$seg[4][1] += $row[2];
						break;
						case "SOAT":
							$seg[5][1] += $row[2];
						break;
						default:
							$seg[6][1] += $row[2];
						break;
					}
				}
				$id1=3;
				$id2=4;
				echo "<tr><td><button type='button' onclick='toggleDisplay(seg".$id1.");GraficarS(".$id1.",\"".$wanoi."/".$wperi1."-".$wperi2."\");GraficarS(".$id2.",\"".$wanof."/".$wperf1."-".$wperf2."\");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='GraficarS(".$id1.",\"".$wanoi."/".$wperi1."-".$wperi2."\");GraficarS(".$id2.",\"".$wanof."/".$wperf1."-".$wperf2."\");'></td></tr>";
				echo "<tr id='seg".$id1."' style='display: block'><td><table align='center' >";
				echo "<tr>";
				echo "<td><div id='amcharts".$id1."' style='width:600px; height:400px;'></div></td>";
				echo "</tr>";
				echo "</table></td>";
				echo "<td><table align='center' >";
				echo "<tr>";
				echo "<td><div id='amcharts".$id2."' style='width:600px; height:400px;'></div></td>";
				echo "</tr>";
				echo "</table>";
				echo "</td></tr>";
				echo "</table></center>";
				echo "<center><table border=1 id='tablaresultados".$id1."' class=tipo3GRID>";
				echo "<tr><td>ITEM</td><td>VALOR</td></tr>";
				for ($i=0;$i<7;$i++)
					echo "<tr><td class=tipoL01>".$seg[$i][0]."</td><td class=tipoL02R>".number_format((double)$seg[$i][1],0,'.','')."</td></tr>";
				echo "</table></center><br>";
				
				$seg=array();
				$seg[0][0]="EPS";
				$seg[1][0]="PREPAGADAS";
				$seg[2][0]="IPS";
				$seg[3][0]="ASEGURADORAS";
				$seg[4][0]="PARTICULARES";
				$seg[5][0]="SOAT";
				$seg[6][0]="OTROS";
				$seg[0][1]=0;
				$seg[1][1]=0;
				$seg[2][1]=0;
				$seg[3][1]=0;
				$seg[4][1]=0;
				$seg[5][1]=0;
				$seg[6][1]=0;
				if($wgru == "Todos")
				{
					$query  = "select mioano,empseg,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061 ";
					$query .= " where mioano = ".$wanof;
					$query .= "   and mioemp = '".$wemp."'";
					$query .= "   and miomes between ".$wperf1." and ".$wperf2;
					$query .= "   and miocco between '".$wccoi."' and '".$wccof."'";
					$query .= "   and mionit = epmcod ";
					$query .= "   and mioemp = empemp ";
					$query .= " group by 1,2 ";
					$query .= " order by 1,2 ";
				}
				else
				{
					$query  = "select mioano,empseg,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000005 ";
					$query .= " where mioano = ".$wanof;
					$query .= "   and mioemp = '".$wemp."'";
					$query .= "   and miomes between ".$wperf1." and ".$wperf2;
					$query .= "   and miocco between '".$wccoi."' and '".$wccof."'";
					$query .= "   and miocco = ccocod ";
					$query .= "   and mioemp = ccoemp ";
					$query .= "   and ccouni = '".$wgru."' ";
					$query .= "   and mionit = epmcod ";
					$query .= "   and mioemp = empemp ";
					$query .= " group by 1,2 ";
					$query .= " order by 1,2 ";
				}
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						switch ($row[1])
						{
							case "EPS":
								$seg[0][1] += $row[2];
							break;
							case "MP":
								$seg[1][1] += $row[2];
							break;
							case "IPS":
								$seg[2][1] += $row[2];
							break;
							case "ASEG":
								$seg[3][1] += $row[2];
							break;
							case "PPJ":
								$seg[4][1] += $row[2];
							break;
							case "PPN":
								$seg[4][1] += $row[2];
							break;
							case "SOAT":
								$seg[5][1] += $row[2];
							break;
							default:
								$seg[6][1] += $row[2];
							break;
						}
					}
					echo "<center><table border=1 id='tablaresultados".$id2."' class=tipo3GRID>";
					echo "<tr><td>ITEM</td><td>VALOR</td></tr>";
					for ($i=0;$i<7;$i++)
						echo "<tr><td class=tipoL01>".$seg[$i][0]."</td><td class=tipoL02R>".number_format((double)$seg[$i][1],0,'.','')."</td></tr>";
					echo "</table></center><br>";
				}
			}
			
			///SEGMENTACION
			$id=9;
			echo "<button type='button' onclick='toggleDisplay(seg".$id.");'>Detalle Segmentaci&oacute;n</button>";
			echo "<div id='seg".$id."' style='display: none'>";
			$wanopa=$wanop-1;
			$query = "select segcod,segtip,segdes,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000045,".$empresa."_000005 ";
			$query = $query."  where mioano = ".$wanoi;
			$query = $query."    and mioemp = '".$wemp."'";
			$query = $query."    and miomes between ".$wperi1." and ".$wperi2;
			$query = $query."    and miocco between '".$wccoi."' and '".$wccof."'";
			$query = $query."	 and miocco = Ccocod ";
			$query = $query."    and mioemp = ccoemp ";
			if($wgru != "Todos")
			{
				$query = $query."    and Ccouni = '".$wgru."' ";
			}
			$query = $query."    and mionit = epmcod   ";
			$query = $query."    and mioemp = empemp  ";
			$query = $query."    and empseg = segcod   ";
			$query = $query."   group by segcod,segtip,segdes  ";
			$query = $query."   order by segcod";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "select segcod,segtip,segdes,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000045,".$empresa."_000005  ";
			$query = $query."  where mioano = ".$wanof;
			$query = $query."    and mioemp = '".$wemp."'";
			$query = $query."    and miomes between ".$wperf1." and ".$wperf2;
			$query = $query."    and miocco between '".$wccoi."' and '".$wccof."'";
			$query = $query."	 and miocco = Ccocod ";
			$query = $query."    and mioemp = ccoemp ";
			if($wgru != "Todos")
			{
				$query = $query."    and Ccouni = '".$wgru."' ";
			}
			$query = $query."    and mionit = epmcod   ";
			$query = $query."    and mioemp = empemp  ";
			$query = $query."    and empseg = segcod   ";
			$query = $query."   group by segcod,segtip,segdes  ";
			$query = $query."   order by segcod";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			echo "<center><table border=1>";
			echo "<tr><td bgcolor=#dddddd><b>SEGMENTO</b></td><td bgcolor=#dddddd><b>".$wanoi."/".$wperi1."-".$wperi2."</b></td><td bgcolor=#dddddd><b>% PART</b></td><td bgcolor=#dddddd><b>".$wanof."/".$wperf1."-".$wperf2."</b></td><td bgcolor=#dddddd><b>% PART</b></td><td align=right bgcolor=#dddddd><b>DIFERENCIA</b></td><td align=right bgcolor=#dddddd><b>% VARIACION</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			$valant=0;
			$valact=0;
			$vpapant=0;
			$vpapact=0;
			if ($num1 ==  0)
			{
				$k1++;
				$row1[0]='ZZZZZZZZZZ';
				$row1[1]=" ";
				$row1[2]=" ";
				$row1[3]=0;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2++;
				$row2[0]='ZZZZZZZZZZ';
				$row2[1]=" ";
				$row2[2]=" ";
				$row2[3]=0;
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
					$row1[3]=($row1[3]/1000000);
					$row2[3]=($row2[3]/1000000);
					$num++;
					$valant=$valant+$row1[3];
					$valact=$valact+$row2[3];
					if($row1[1] == "PAP")
						$vpapant=$vpapant+$row1[3];
					if($row2[1] == "PAP")
						$vpapact=$vpapact+$row2[3];
					$wdata[$num][0]=$row1[2];
					$wdata[$num][1]=$row1[3];
					$wdata[$num][2]=$row2[3];
					$wdata[$num][3]=$row2[3]-$row1[3];
					if($row1[3] != 0)
						$wdata[$num][4]=($row2[3] - $row1[3])/$row1[3] *100;
					else
						$wdata[$num][4]=0;
					$wdata[$num][5]=$row1[0];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$row1[0]="ZZZZZZZZZZ";
					else
						$row1 = mysql_fetch_array($err1);
					if($k2 > $num2)
						$row2[0]="ZZZZZZZZZZ";
					else
						$row2 = mysql_fetch_array($err2);
				}
				else if($row1[0] < $row2[0])
				{
					$row1[3]=($row1[3]/1000000);
					$num++;
					$valant=$valant+$row1[3];
					if($row1[1] == "PAP")
						$vpapant=$vpapant+$row1[3];
					$wdata[$num][0]=$row1[2];
					$wdata[$num][1]=$row1[3];
					$wdata[$num][2]=0;
					$wdata[$num][3]=0-$row1[3];
					if($row1[3] != 0)
						$wdata[$num][4]=(0 - $row1[3])/$row1[3] *100;
					else
						$wdata[$num][4]=0;
					$wdata[$num][5]=$row1[0];
					$k1++;
					if($k1 > $num1)
						$row1[0]="ZZZZZZZZZZ";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
					$row2[3]=($row2[3]/1000000);
					$num++;
					$valact=$valact+$row2[3];
					if($row2[1] == "PAP")
						$vpapact=$vpapact+$row2[3];
					$wdata[$num][0]=$row2[2];
					$wdata[$num][1]=0;
					$wdata[$num][2]=$row2[3];
					$wdata[$num][3]=$row2[3];
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row2[0];
					$k2++;
					if($k2 > $num2)
						$row2[0]="ZZZZZZZZZZ";
					else
						$row2 = mysql_fetch_array($err2);
				}
			}
			usort($wdata,'comparacion');
			for ($i=0;$i<=$num;$i++)
			{
				if($valant !=0 )
					$proant=$wdata[$i][1]/$valant * 100;
				else
					$proant=0;
				if($valact !=0 )
					$proact=$wdata[$i][2]/$valact * 100;
				else
					$proact=0;
				$path1="/matrix/presupuestos/reportes/000001_rc33.php?wanop=".$wanopa."&wper1=".$wper1."&wper2=".$wper2."&wcco1=".$wcco1."&wcco2=".$wcco2."&wseg=".$wdata[$i][5]."-".$wdata[$i][0]."&wgru=".$wgru."&empresa=".$empresa;
				$path2="/matrix/presupuestos/reportes/000001_rc33.php?wanop=".$wanop."&wper1=".$wper1."&wper2=".$wper2."&wcco1=".$wcco1."&wcco2=".$wcco2."&wseg=".$wdata[$i][5]."-".$wdata[$i][0]."&wgru=".$wgru."&empresa=".$empresa;
				echo"<tr><td>".$wdata[$i][0]."</td><td align=right onclick='ejecutar(".chr(34).$path1.chr(34).")'>".number_format((double)$wdata[$i][1],0,'.',',')."</td><td  align=right>".number_format((double)$proant,2,'.',',')."</td><td align=right onclick='ejecutar(".chr(34).$path2.chr(34).")'>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td  align=right>".number_format((double)$proact,2,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][4],2,'.',',')."</td></tr>";
			}
			$proant= 100;
			$proact= 100;
			$wdif=$valact-$valant;
			if($valant != 0)
				$var=($valact - $valant)/$valant *100;
			else
				$var=0;
			echo"<tr><td bgcolor='#cccccc'><b>TOTAL FACTURADO</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$valant,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$proant,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$valact,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$proact,2,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#cccccc' align=right><b>".number_format((double)$var,2,'.',',')."</b></td></tr>";
			if($valant != 0)
				$proant=$vpapant/$valant * 100;
			else
				$proant=0;
			if($valact != 0)
				$proact=$vpapact/$valact * 100;
			else
				$proact=0;
			$wdif=$vpapact-$vpapant;
			if($vpapant != 0)
				$var=($vpapact - $vpapant)/$vpapant *100;
			else
				$var=0;
			echo"<tr><td bgcolor='#99CCFF'><b>TOTAL PAP</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$vpapant,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$proant,2,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$vpapact,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$proact,2,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$wdif,0,'.',',')."</b></td><td bgcolor='#99CCFF' align=right><b>".number_format((double)$var,2,'.',',')."</b></td></tr></table></center>";
			echo "</div><br><br><br>";
			///SEGMENTACION
			
			$id=7;
			$west1=array();
			echo "<center><table border=0 CELLPADDING=7>";
			echo "<tr><td class=tipoL04 colspan=5>PROCEDIMIENTOS ".$wanoi."/".$wperi1."-".$wperi2." y ".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			$query  = "SELECT Morcod,Prodes,sum(Morcan) from ".$empresa."_000032,".$empresa."_000059,".$empresa."_000005 ";
			$query .= "  where Morano = ".$wanoi;
			$query .= "    and Moremp = '".$wemp."'";
			$query .= "	   and Mormes between ".$wperi1." and ".$wperi2;
			$query .= "	   and Mortip = 'P' "; 
			$query .= "	   and Morcod = Procod ";
			$query .= "	   and Moremp = Proemp ";
			$query .= "	   and Morcco between '".$wccoi."' and '".$wccof."'"; 
			$query .= "	   and Morcco = ccocod   ";
			$query .= "	   and Moremp = ccoemp   ";
			if($wgru != "Todos")
				$query .= "   and ccouni = '".$wgru."' ";
			$query .= " group by 1,2 ";
			$query .= " order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$west1[$i][0]=$row[0];
					$west1[$i][1]=$row[1];
					$west1[$i][2]=$row[2];
					$west1[$i][3]=0;
					$west1[$i][4]=0;
					$west1[$i][5]=0;
				}
			}
			$wetot=$num;
			$query  = "SELECT Morcod,Prodes,sum(Morcan) from ".$empresa."_000032,".$empresa."_000059,".$empresa."_000005 ";
			$query .= "  where Morano = ".$wanof;
			$query .= "    and Moremp = '".$wemp."'";
			$query .= "	   and Mormes between ".$wperf1." and ".$wperf2;
			$query .= "	   and Mortip = 'P' "; 
			$query .= "	   and Morcod = Procod ";
			$query .= "	   and Moremp = Proemp ";
			$query .= "	   and Morcco between '".$wccoi."' and '".$wccof."'"; 
			$query .= "	   and Morcco = ccocod   ";
			$query .= "	   and Moremp = ccoemp   ";
			if($wgru != "Todos")
				$query .= "   and ccouni = '".$wgru."' ";
			$query .= " group by 1,2 ";
			$query .= " order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$pos=buscar($west1,$row[0],$wetot);
					if($pos != -1)
						$west1[$pos][3]=$row[2];
					else
					{
						$wetot++;
						$west1[$wetot-1][0]=$row[0];
						$west1[$wetot-1][1]=$row[1];
						$west1[$wetot-1][2]=0;
						$west1[$wetot-1][3]=$row[2];
						$west1[$wetot-1][4]=0;
						$west1[$wetot-1][5]=0;
					}
				}
			}
			$efin=$wetot;
			$west1[$efin][0]="";
			$west1[$efin][1]="TOTALES";
			$west1[$efin][2]=0;
			$west1[$efin][3]=0;
			$west1[$efin][4]=0;
			$west1[$efin][5]=0;
			for ($i=0;$i<$wetot;$i++)
			{
				$west1[$i][4]=$west1[$i][3] - $west1[$i][2];
				if($west1[$i][2] != 0)
					$west1[$i][5]=($west1[$i][3] / $west1[$i][2] - 1) * 100;
				else
					$west1[$i][5]=0;
				$west1[$efin][2] += $west1[$i][2];
				$west1[$efin][3] += $west1[$i][3];
			}
			$west1[$efin][4]=$west1[$efin][3] - $west1[$efin][2];
			if($west1[$efin][2] != 0)
				$west1[$efin][5]=($west1[$efin][3] / $west1[$efin][2] - 1) * 100;
			else
				$west1[$efin][5]=0;
			echo "<tr><td class=tipoL01>Descripcion</td><td class=tipoL01>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL01>".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL01>Dif.</td><td class=tipoL01>% Var.</td></tr>";
			for ($i=0;$i<$efin;$i++)
				echo "<tr><td class=tipoL02L>".$west1[$i][1]."</td><td class=tipoL02R>".number_format((double)$west1[$i][2],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$west1[$i][3],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$west1[$i][4],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$west1[$i][5],2,'.',',')."%</td></tr>";
			echo "<tr><td class=tipoL01>".$west1[$efin][1]."</td><td class=tipoL01R>".number_format((double)$west1[$efin][2],0,'.',',')."</td><td class=tipoL01R>".number_format((double)$west1[$efin][3],0,'.',',')."</td><td class=tipoL01R>".number_format((double)$west1[$efin][4],0,'.',',')."</td><td class=tipoL01R>".number_format((double)$west1[$efin][5],2,'.',',')."%</td></tr>";
			echo "<tr><td colspan=5><button type='button' onclick='toggleDisplay(seg".$id.");GraficarE(".$id.");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='GraficarE(".$id.");'></td></tr>";
			echo "<tr id='seg".$id."' style='display: block'><td colspan=7><table align='center' >";
			echo "<tr>";
			echo "<td><div id='amcharts".$id."' style='width:900px; height:400px;'></div></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			echo "<center><table border=1 id='tablaresultados".$id."' class=tipo3GRID>";
			echo "<tr><td>ITEM</td><td>VALOR</td></tr>";
			echo "<tr><td class=tipoL01>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL02R>".number_format((double)$west1[$efin][2],0,'.','')."</td></tr>";
			echo "<tr><td class=tipoL01>".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL02R>".number_format((double)$west1[$efin][3],0,'.','')."</td></tr>";
			echo "</table></center><br>";
			
			$id=8;
			$west2=array();
			echo "<center><table border=0 CELLPADDING=7>";
			echo "<tr><td class=tipoL04 colspan=5>INGRESO PROMEDIO ".$wanoi."/".$wperi1."-".$wperi2." y ".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			$west2[0]=($ing0 * 1000000)/$west1[$efin][2];
			$west2[1]=($ing0 * 1000000)/$west1[$efin][3];
			$west2[2]=$west2[1]-$west2[0];
			if($west2[0] != 0)
				$west2[3]=($west2[1]-$west2[0]) / $west2[0] * 100;
			else
				$west2[3]=0;
			echo "<tr><td class=tipoL01></td><td class=tipoL01>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL01>".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL01>Dif.</td><td class=tipoL01>% Var.</td></tr>";
			echo "<tr><td class=tipoL02L>Ingreso Promedio (Expresado en Pesos)</td><td class=tipoL02R>".number_format((double)$west2[0],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$west2[1],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$west2[2],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$west2[3],2,'.',',')."%</td></tr>";
			echo "<tr><td colspan=5><button type='button' onclick='toggleDisplay(seg".$id.");GraficarIP(".$id.");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='GraficarIP(".$id.");'></td></tr>";
			echo "<tr id='seg".$id."' style='display: block'><td colspan=7><table align='center' >";
			echo "<tr>";
			echo "<td><div id='amcharts".$id."' style='width:900px; height:400px;'></div></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			echo "<center><table border=1 id='tablaresultados".$id."' class=tipo3GRID>";
			echo "<tr><td>ITEM</td><td>VALOR</td></tr>";
			echo "<tr><td class=tipoL01>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL02R>".number_format((double)$west2[0],0,'.','')."</td></tr>";
			echo "<tr><td class=tipoL01>".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL02R>".number_format((double)$west2[1],0,'.','')."</td></tr>";
			echo "</table></center><br>";
			
			echo "<center><table border=0 CELLPADDING=7>";
			echo "<tr><td class=tipoL04 colspan=5>RESULTADOS ".$wanoi."/".$wperi1."-".$wperi2." y ".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			$wdata=array();
			for ($i=0;$i<=1;$i++)
				for ($j=0;$j<=3;$j++)
					$wdata[$i][$j]=0;
			$query  = "select meccpr, SUM(Mecval) from ".$empresa."_000026,".$empresa."_000005 ";
			$query .= "	where mecano = ".$wanoi;
			$query .= "   and mecemp = '".$wemp."'";
			$query .= "	  and mecmes between ".$wperi1." and ".$wperi2;
			$query .= "	  and meccpr between '100' and '299' ";
			$query .= "	  and meccco between '".$wccoi."' and '".$wccof."'";
			$query .= "	  and meccco = ccocod   ";
			$query .= "	  and mecemp = ccoemp   ";
			if($wgru != "Todos")
				$query .= "   and ccouni = '".$wgru."' ";
			$query .= " group by 1  ";
			$query .= " order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[0] != "227" and $row[0] != "298")
					{
						if(substr($row[0],0,1) == "1")
							$wdata[0][0] += $row[1];
						else
							$wdata[0][0] -= $row[1];
					}
					if(substr($row[0],0,1) == "1")
						$wdata[1][0] += $row[1];
					else
						$wdata[1][0] -= $row[1];
			}
			}
			$query  = "select meccpr, SUM(Mecval) from ".$empresa."_000026,".$empresa."_000005 ";
			$query .= "	where mecano = ".$wanof;
			$query .= "   and mecemp = '".$wemp."'";
			$query .= "	  and mecmes between ".$wperf1." and ".$wperf2;
			$query .= "	  and meccpr between '100' and '299' ";
			$query .= "	  and meccco between '".$wccoi."' and '".$wccof."'";
			$query .= "	  and meccco = ccocod   ";
			$query .= "	  and mecemp = ccoemp   ";
			if($wgru != "Todos")
				$query .= "   and ccouni = '".$wgru."' ";
			$query .= " group by 1  ";
			$query .= " order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($row[0] != "227" and $row[0] != "298")
					{
						if(substr($row[0],0,1) == "1")
							$wdata[0][1] += $row[1];
						else
							$wdata[0][1] -= $row[1];
					}
					if(substr($row[0],0,1) == "1")
						$wdata[1][1] += $row[1];
					else
						$wdata[1][1] -= $row[1];
				}
			}
			$wdata[0][0]=$wdata[0][0]/1000000;
			$wdata[0][1]=$wdata[0][1]/1000000;
			$wdata[1][0]=$wdata[1][0]/1000000;
			$wdata[1][1]=$wdata[1][1]/1000000;
			$wdata[0][2]=$wdata[0][1] - $wdata[0][0];
			$wdata[1][2]=$wdata[1][1] - $wdata[1][0];
			$wdata[0][3]=($wdata[0][1] / $wdata[0][0] -1)*100;
			$wdata[1][3]=($wdata[1][1] / $wdata[1][0] -1)*100;
			$id=5;
			echo "<tr><td class=tipoL01>Resultados</td><td class=tipoL01>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL01>".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL01>Dif.</td><td class=tipoL01>% Var.</td></tr>";
			echo "<tr><td class=tipoL02L>BRUTO (SIN COSTOS DE APOYO)</td><td class=tipoL02R>".number_format((double)$wdata[0][0],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[0][1],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[0][2],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[0][3],2,'.',',')."%</td></tr>";
			echo "<tr><td class=tipoL02L>OPERACIONAL</td><td class=tipoL02R>".number_format((double)$wdata[1][0],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1][1],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1][2],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1][3],2,'.',',')."%</td></tr>";
			echo "<tr><td colspan=5><button type='button' onclick='toggleDisplay(seg".$id.");GraficarR(".$id.");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='GraficarR(".$id.");'></td></tr>";
			echo "<tr id='seg".$id."' style='display: block'><td colspan=5><table align='center' >";
			echo "<tr>";
			echo "<td><div id='amcharts".$id."' style='width:900px; height:400px;'></div></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			echo "<center><table border=1 id='tablaresultados".$id."' class=tipo3GRID>";
			echo "<tr><td>RESULTADO</td><td>BRUTO (SIN COSTOS DE APOYO)</td><td>OPERACIONAL</td></tr>";
			echo "<tr><td class=tipoL01>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL02R>".number_format((double)$wdata[0][0],0,'.','')."</td><td class=tipoL02R>".number_format((double)$wdata[1][0],0,'.','')."</td></tr>";
			echo "<tr><td class=tipoL01>".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL02R>".number_format((double)$wdata[0][1],0,'.','')."</td><td class=tipoL02R>".number_format((double)$wdata[1][1],0,'.','')."</td></tr>";
			echo "</table></center><br>";
			echo "</table></center><br>";
		
			$id=6;
			$wmargen=array();
			$wmargen[0][0] = 0;
			$wmargen[0][1] = 0;
			$wmargen[1][0] = 0;
			$wmargen[1][1] = 0;
			echo "<center><table border=0 CELLPADDING=7>";
			echo "<tr><td class=tipoL04 colspan=3>MARGENES ".$wanoi."/".$wperi1."-".$wperi2." y ".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			$wmargen[0][0] = $wdata[0][0] / $ing0 * 100;
			$wmargen[0][1] = $wdata[0][1] / $ing1 * 100;
			$wmargen[1][0] = $wdata[1][0] / $ing0 * 100;
			$wmargen[1][1] = $wdata[1][1] / $ing1 * 100;
			echo "<tr><td class=tipoL01>Margenes</td><td class=tipoL01>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL01>".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			echo "<tr><td class=tipoL02L>BRUTO (SIN COSTOS DE APOYO)</td><td class=tipoL02R>".number_format((double)$wmargen[0][0],2,'.',',')."%</td><td class=tipoL02R>".number_format((double)$wmargen[0][1],2,'.',',')."%</td></tr>";
			echo "<tr><td class=tipoL02L>OPERACIONAL</td><td class=tipoL02R>".number_format((double)$wmargen[1][0],2,'.',',')."%</td><td class=tipoL02R>".number_format((double)$wmargen[1][1],2,'.',',')."%</td></tr>";
			echo "<tr><td colspan=5><button type='button' onclick='toggleDisplay(seg".$id.");GraficarM(".$id.");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='GraficarM(".$id.");'></td></tr>";
			echo "<tr id='seg".$id."' style='display: block'><td colspan=5><table align='center' >";
			echo "<tr>";
			echo "<td><div id='amcharts".$id."' style='width:900px; height:400px;'></div></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			echo "<center><table border=1 id='tablaresultados".$id."' class=tipo3GRID>";
			echo "<tr><td>MARGENES</td><td>BRUTO (SIN COSTOS DE APOYO)</td><td>OPERACIONAL</td></tr>";
			echo "<tr><td class=tipoL01>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL02R>".number_format((double)$wmargen[0][0],2,'.','')."</td><td class=tipoL02R>".number_format((double)$wmargen[1][0],2,'.','')."</td></tr>";
			echo "<tr><td class=tipoL01>".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL02R>".number_format((double)$wmargen[0][1],2,'.','')."</td><td class=tipoL02R>".number_format((double)$wmargen[1][1],2,'.','')."</td></tr>";
			echo "</table></center><br>";
			echo "</table></center><br>";
			
			
			///ESTADO DE RESULTADOS
			$id=100;
			echo "<button type='button' onclick='toggleDisplay(seg".$id.");'>Estado de Resultados</button>";
			echo "<div id='seg".$id."' style='display: none'>";
			$wres="D";
			$wserv="S";
			$wcif=1000000;
			//                  0     1           2           3
			$query = "SELECT rvpcpr,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where rvpano = ".$wanoi;
			$query = $query."    and rvpemp = '".$wemp."'";
			$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
			$query = $query."    and rvpcco = Ccocod";
			$query = $query."    and rvpemp = Ccoemp";
			if($wgru != "Todos")             
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query = $query."    and rvpper between ".$wperi1." and ".$wperi2;
			$query = $query."    and rvpcpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by rvpcpr,mganom";
			$query = $query."   order by rvpcpr";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT rvpcpr,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where rvpano = ".$wanof;
			$query = $query."    and rvpemp = '".$wemp."'";
			$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
			$query = $query."    and rvpcco = Ccocod";
			$query = $query."    and rvpemp = Ccoemp";
			if($wgru != "Todos")
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query = $query."    and rvpper between ".$wperf1." and ".$wperf2;
			$query = $query."    and rvpcpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by rvpcpr,mganom";
			$query = $query."   order by rvpcpr";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			switch ($wcif)
			{
				case 1:
					$wcift = "PESOS";
				break;
				case 1000:
					$wcift = "MILES DE PESOS";
				break;
				case 1000000:
					$wcift = "MILLONES DE PESOS";
				break;
			}
			echo "<center><table border=1 class=tipoTABLEGRID>";
			echo "<tr><td colspan=11 align=center>CIFRAS EXPRESADAS EN  : ".$wcift."</td></tr>";
			echo "<tr class='tipotit'><td><b>CODIGO</b></td><td><b>RUBRO</b></td><td><b>REAL : ".$wanoi."/".$wperi1."-".$wperi2."</b></td><td><b>%PART</b></td><td><b>REAL : ".$wanof."/".$wperf1."-".$wperf2."</b></td><td><b>%PART</b></td><td><b>%VAR</b></td><td><b>PPTO : ".$wanof."/".$wperf1."-".$wperf2."</b></td><td><b>%PART</b></td><td align=right><b>DIFERENCIA (R-P)</b></td><td align=right><b>EJECUCION</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			$ingresos1 = 0;
			$ingresos2 = 0;
			if ($num1 ==  0)
			{
				$k1++;
				$row1[0]='zzz';
				$row1[1]="";
				$row1[2]=0;
				$row1[3]=0;
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2++;
				$row2[0]='zzz';
				$row2[1]="";
				$row2[2]=0;
				$row2[3]=0;
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				#echo $k1."-".$k2."-".$num1."-".$num2."<br>";
				if($row1[0] == $row2[0])
				{
					$row1[2] = $row1[2] / $wcif;
					$row1[3] = $row1[3] / $wcif;
					$row2[2] = $row2[2] / $wcif;
					$row2[3] = $row2[3] / $wcif;
					if (substr($row1[0],0,1) == "1")
					{
						$ingresos1 += $row1[2];
						$ingresos2 += $row2[2];
						$ingresos3 += $row2[3];
					}
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=$row2[3];
					$wdata[$num][5]=$row2[2]-$row2[3];
					if($row2[3] != 0)
						$wdata[$num][6]=($row2[2]/$row2[3])*100;
					else
						$wdata[$num][6]=0;
					$wdata[$num][7]=0;
					$wdata[$num][8]=0;
					$wdata[$num][9]=0;
					if($wdata[$num][2] != 0)
						$wdata[$num][10]=(($wdata[$num][3] / $wdata[$num][2])-1)*100;
					else
						$wdata[$num][10]=0;
					$k1++;
					$k2++;
					if($k1 > $num1)
						$row1[0]="zzz";
					else
						$row1 = mysql_fetch_array($err1);
					if($k2 > $num2)
						$row2[0]="zzz";
					else
						$row2 = mysql_fetch_array($err2);
				}
				else if($row1[0] < $row2[0])
				{
					$row1[2] = $row1[2] / $wcif;
					$row1[3] = $row1[3] / $wcif;
					if (substr($row1[0],0,1) == "1")
					{
						$ingresos1 += $row1[2];
						$ingresos2 += 0;
						$ingresos3 += 0;
					}
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=0;
					$wdata[$num][4]=0;
					$wdata[$num][5]=0;
					$wdata[$num][6]=0;
					$wdata[$num][7]=0;
					$wdata[$num][8]=0;
					$wdata[$num][9]=0;
					if($wdata[$num][2] != 0)
						$wdata[$num][10]=(($wdata[$num][3] / $wdata[$num][2])-1)*100;
					else
						$wdata[$num][10]=0;
					$k1++;
					if($k1 > $num1)
						$row1[0]="zzz";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
					$row2[2] = $row2[2] / $wcif;
					$row2[3] = $row2[3] / $wcif;
					if (substr($row2[0],0,1) == "1")
					{
						$ingresos1 += 0;
						$ingresos2 += $row2[2];
						$ingresos3 += $row2[3];
					}
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=0;
					$wdata[$num][3]=$row2[2];
					$wdata[$num][4]=$row2[3];
					$wdata[$num][5]=$row2[2]-$row2[3];
					if($row2[3] != 0)
						$wdata[$num][6]=($row2[2]/$row2[3])*100;
					else
						$wdata[$num][6]=0;
					$wdata[$num][7]=0;
					$wdata[$num][8]=0;
					$wdata[$num][9]=0;
					if($wdata[$num][2] != 0)
						$wdata[$num][10]=(($wdata[$num][3] / $wdata[$num][2])-1)*100;
					else
						$wdata[$num][10]=0;
					$k2++;
					if($k2 > $num2)
						$row2[0]="999";
					else
						$row2 = mysql_fetch_array($err2);
				}
			}
			if($ingresos1 == 0 and $ingresos2 == 0 and $ingresos3 == 0)
			{
				for ($i=0;$i<=$num;$i++)
				{
					if (substr($wdata[$i][0],0,1) == "2")
					{
						$ingresos1+=$wdata[$i][2];
						$ingresos2+=$wdata[$i][3];
						$ingresos3+=$wdata[$i][4];
					}
				}
			}
			$wtotal=array();
			$ita=0;
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if(!isset($wtotal[$it][0]))
				{
					$wtotal[$it][0]=0;
					$wtotal[$it][1]=0;
					$wtotal[$it][2]=0;
					switch ($ita)
					{
						case 1:
						$ita=7;
						$dif=$wtotal[1][1]-$wtotal[1][2];
						$cum=0;
						if($wtotal[1][2] != 0)
							$cum=($wtotal[1][1] / $wtotal[1][2])*100;
						$var=0;
						if($wtotal[1][2] != 0)
							$var=(($wtotal[1][1]/$wtotal[1][0])-1)*100;
						if($ingresos1 != 0)
							$i1=$wtotal[1][0]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal[1][1]/$ingresos2*100;
						else
							$i2=0;
						if($ingresos3 != 0)
							$i3=$wtotal[1][2]/$ingresos3*100;
						else
							$i3=0;
						echo"<tr class='tipotot'><td colspan=2><b>INGRESOS NETOS</b></td><td align=right>".number_format((double)$wtotal[1][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[1][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[1][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
						break;
						case 2:
						$ita=7;
						$dif=$wtotal[2][1]-$wtotal[2][2];
						$cum=0;
						if($wtotal[2][2] != 0)
							$cum=($wtotal[2][1] / $wtotal[2][2])*100;
						$var=0;
						if($wtotal[2][2] != 0)
							$var=(($wtotal[2][1]/$wtotal[2][0])-1)*100;
						if($ingresos1 != 0)
							$i1=$wtotal[2][0]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal[2][1]/$ingresos2*100;
						else
							$i2=0;
						if($ingresos3 != 0)
							$i3=$wtotal[2][2]/$ingresos3*100;
						else
							$i3=0;
						echo"<tr class='tipotot'><td colspan=2><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right>".number_format((double)$wtotal[2][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[2][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[2][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
						if(isset($wtotal[1][0] ))
							$wtotal[9][0] = $wtotal[1][0] - $wtotal[2][0];
						else
							$wtotal[9][0] = 0 - $wtotal[2][0];
						if(isset($wtotal[1][1]))
                    		$wtotal[9][1] = $wtotal[1][1] - $wtotal[2][1];
                    	else
                    		$wtotal[9][1] = 0 - $wtotal[2][1];
                    	if(isset($wtotal[1][2]))
                    		$wtotal[9][2] = $wtotal[1][2] - $wtotal[2][2];
                    	else
                    		$wtotal[9][2] = 0 - $wtotal[2][2];
                    	$dif=$wtotal[9][1]-$wtotal[9][2];
						$cum=0;
						if($wtotal[9][2] != 0)
							$cum=($wtotal[9][1] / $wtotal[9][2])*100;
						$var=0;
						if($wtotal[9][2] != 0)
							$var=(($wtotal[9][1]/$wtotal[9][0])-1)*100;
						if($ingresos1 != 0)
							$i1=$wtotal[9][0]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal[9][1]/$ingresos2*100;
						else
							$i2=0;
						if($ingresos3 != 0)
							$i3=$wtotal[9][2]/$ingresos3*100;
						else
							$i3=0;
						echo"<tr class='tipouti'><td colspan=2><b>UTILIDAD OPERACIONAL</b></td><td align=right>".number_format((double)$wtotal[9][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[9][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[9][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
						break;
						case 3:
						break;
						case 4:
						$ita=7;
						$dif=$wtotal[4][1]-$wtotal[4][2];
						$cum=0;
						if($wtotal[4][2] != 0)
							$cum=($wtotal[4][1] / $wtotal[4][2])*100;
						$var=0;
						if($wtotal[4][2] != 0)
							$var=(($wtotal[4][1]/$wtotal[4][0])-1)*100;
						if($ingresos1 != 0)
							$i1=$wtotal[4][0]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal[4][1]/$ingresos2*100;
						else
							$i2=0;
						if($ingresos3 != 0)
							$i3=$wtotal[4][2]/$ingresos3*100;
						else
							$i3=0;
						echo"<tr class='tipotot'><td colspan=2><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right>".number_format((double)$wtotal[4][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[4][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[4][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
						break;
						case 5:
						$ita=7;
						$dif=$wtotal[5][1]-$wtotal[5][2];
						$cum=0;
						if($wtotal[5][2] != 0)
							$cum=($wtotal[5][1] / $wtotal[5][2])*100;
						$var=0;
						if($wtotal[5][2] != 0)
							$var=(($wtotal[5][1]/$wtotal[5][0])-1)*100;
						if($ingresos1 != 0)
							$i1=$wtotal[5][0]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal[5][1]/$ingresos2*100;
						else
							$i2=0;
						if($ingresos3 != 0)
							$i3=$wtotal[5][2]/$ingresos3*100;
						else
							$i3=0;
						echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS NO OPERACIONALES</b><td align=right>".number_format((double)$wtotal[5][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[5][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[5][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
						break;
						case 6:
						$ita=7;
						$dif=$wtotal[6][1]-$wtotal[6][2];
						$cum=0;
						if($wtotal[6][2] != 0)
							$cum=($wtotal[6][1] / $wtotal[6][2])*100;
						$var=0;
						if($wtotal[6][2] != 0)
							$var=(($wtotal[6][1]/$wtotal[6][0])-1)*100;
						if($ingresos1 != 0)
							$i1=$wtotal[6][0]/$ingresos1*100;
						else
							$i1=0;
						if($ingresos2 != 0)
							$i2=$wtotal[6][1]/$ingresos2*100;
						else
							$i2=0;
						if($ingresos3 != 0)
							$i3=$wtotal[6][2]/$ingresos3*100;
						else
							$i3=0;
						echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right>".number_format((double)$wtotal[6][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[6][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[6][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
						break;
					}
					switch ($it)
					{
						case 1:
						echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>INGRESOS</B></td></tr>";
						$ita=1;
						break;
						case 2:
						echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>COSTOS DEL SERVICIO</B></td></tr>";
						$ita=2;
						break;
						case 3:
						echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
						$ita=3;
						break;
						case 4:
						echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>INGRESOS NO OPERACIONALES</B></td></tr>";
						$ita=4;
						break;
						case 5:
						echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS NO OPERACIONALES</B></td></tr>";
						$ita=5;
						break;
						case 6:
						echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS FINANCIEROS</B></td></tr>";
						$ita=6;
						break;
					}
				}
				if($it < 7)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D")
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
                 }
                 else
                 	if($wdata[$i][0] == "700")
                 	{
                 		$wtotal[7][0] += $wdata[$i][2];
                 		$wtotal[7][1] += $wdata[$i][3];
                 		$wtotal[7][2] += $wdata[$i][4];
                 	}
                 	else if($wdata[$i][0] == "750")
                 			{
	                 			if(!isset($wtotal[8][0]))
	                 			{
	                 				$wtotal[8][0]=0;
	                 				$wtotal[8][1]=0;
	                 				$wtotal[8][2]=0;
								}
                 				$wtotal[8][0] += $wdata[$i][2];
                 				$wtotal[8][1] += $wdata[$i][3];
                 				$wtotal[8][2] += $wdata[$i][4];
                 			}
                 			else if($wdata[$i][0] == "760")
                 					{
	                 					if(!isset($wtotal[12][0]))
										{
											$wtotal[12][0]=0;
											$wtotal[12][1]=0;
											$wtotal[12][2]=0;
										}
                 						$wtotal[12][0] += $wdata[$i][2];
										$wtotal[12][1] += $wdata[$i][3];
										$wtotal[12][2] += $wdata[$i][4];
                 					}
                 					else if($wdata[$i][0] == "900")
											{
												if(!isset($wtotal[90][0]))
												{
													$wtotal[90][0]=0;
													$wtotal[90][1]=0;
													$wtotal[90][2]=0;
												}
												$wtotal[90][0] += $wdata[$i][2];
												$wtotal[90][1] += $wdata[$i][3];
												$wtotal[90][2] += $wdata[$i][4];
											}
    		}
    		switch ($ita)
			{
				case 1:
				$ita=7;
				$dif=$wtotal[1][1]-$wtotal[1][2];
				$cum=0;
				if($wtotal[1][2] != 0)
					$cum=($wtotal[1][1] / $wtotal[1][2])*100;
				$var=0;
				if($wtotal[1][2] != 0)
					$var=(($wtotal[1][1]/$wtotal[1][0])-1)*100;
				if($ingresos1 != 0)
					$i1=$wtotal[1][0]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal[1][1]/$ingresos2*100;
				else
					$i2=0;
				if($ingresos3 != 0)
					$i3=$wtotal[1][2]/$ingresos3*100;
				else
					$i3=0;
				echo"<tr class='tipotot'><td colspan=2><b>INGRESOS NETOS</b></td><td align=right>".number_format((double)$wtotal[1][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[1][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[1][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
				break;
				case 2:
				$ita=7;
				$dif=$wtotal[2][1]-$wtotal[2][2];
				$cum=0;
				if($wtotal[2][2] != 0)
					$cum=($wtotal[2][1] / $wtotal[2][2])*100;
				$var=0;
				if($wtotal[2][2] != 0)
					$var=(($wtotal[2][1]/$wtotal[2][0])-1)*100;
				if($ingresos1 != 0)
					$i1=$wtotal[2][0]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal[2][1]/$ingresos2*100;
				else
					$i2=0;
				if($ingresos3 != 0)
					$i3=$wtotal[2][2]/$ingresos3*100;
				else
					$i3=0;
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL COSTOS DEL SERVICIO</b></td><td align=right>".number_format((double)$wtotal[2][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[2][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[2][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
				if(isset($wtotal[1][0] ))
					$wtotal[9][0] = $wtotal[1][0] - $wtotal[2][0];
				else
					$wtotal[9][0] = 0 - $wtotal[2][0];
				if(isset($wtotal[1][1]))
					$wtotal[9][1] = $wtotal[1][1] - $wtotal[2][1];
				else
					$wtotal[9][1] = 0 - $wtotal[2][1];
				if(isset($wtotal[1][2]))
					$wtotal[9][2] = $wtotal[1][2] - $wtotal[2][2];
				else
					$wtotal[9][2] = 0 - $wtotal[2][2];
				$dif=$wtotal[9][1]-$wtotal[9][2];
				$cum=0;
				if($wtotal[9][2] != 0)
					$cum=($wtotal[9][1] / $wtotal[9][2])*100;
				$var=0;
				if($wtotal[9][2] != 0)
					$var=(($wtotal[9][1]/$wtotal[9][0])-1)*100;
				if($ingresos1 != 0)
					$i1=$wtotal[9][0]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal[9][1]/$ingresos2*100;
				else
					$i2=0;
				if($ingresos3 != 0)
					$i3=$wtotal[9][2]/$ingresos3*100;
				else
					$i3=0;
				echo"<tr class='tipouti'><td colspan=2><b>UTILIDAD OPERACIONAL</b></td><td align=right>".number_format((double)$wtotal[9][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[9][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[9][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
				break;
				case 3:
				break;
				case 4:
				$ita=7;
				$dif=$wtotal[4][1]-$wtotal[4][2];
				$cum=0;
				if($wtotal[4][2] != 0)
					$cum=($wtotal[4][1] / $wtotal[4][2])*100;
				$var=0;
				if($wtotal[4][2] != 0)
					$var=(($wtotal[4][1]/$wtotal[4][0])-1)*100;
				if($ingresos1 != 0)
					$i1=$wtotal[4][0]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal[4][1]/$ingresos2*100;
				else
					$i2=0;
				if($ingresos3 != 0)
					$i3=$wtotal[4][2]/$ingresos3*100;
				else
					$i3=0;
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL INGRESOS NO OPERACIONALES</b></td><td align=right>".number_format((double)$wtotal[4][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[4][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[4][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
				break;
				case 5:
				$ita=7;
				$dif=$wtotal[5][1]-$wtotal[5][2];
				$cum=0;
				if($wtotal[5][2] != 0)
					$cum=($wtotal[5][1] / $wtotal[5][2])*100;
				$var=0;
				if($wtotal[5][2] != 0)
					$var=(($wtotal[5][1]/$wtotal[5][0])-1)*100;
				if($ingresos1 != 0)
					$i1=$wtotal[5][0]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal[5][1]/$ingresos2*100;
				else
					$i2=0;
				if($ingresos3 != 0)
					$i3=$wtotal[5][2]/$ingresos3*100;
				else
					$i3=0;
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS NO OPERACIONALES</b><td align=right>".number_format((double)$wtotal[5][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[5][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[5][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
				break;
				case 6:
				$ita=7;
				$dif=$wtotal[6][1]-$wtotal[6][2];
				$cum=0;
				if($wtotal[6][2] != 0)
					$cum=($wtotal[6][1] / $wtotal[6][2])*100;
				$var=0;
				if($wtotal[6][2] != 0)
					$var=(($wtotal[6][1]/$wtotal[6][0])-1)*100;
				if($ingresos1 != 0)
					$i1=$wtotal[6][0]/$ingresos1*100;
				else
					$i1=0;
				if($ingresos2 != 0)
					$i2=$wtotal[6][1]/$ingresos2*100;
				else
					$i2=0;
				if($ingresos3 != 0)
					$i3=$wtotal[6][2]/$ingresos3*100;
				else
					$i3=0;
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS FINANCIEROS</b></td><td align=right>".number_format((double)$wtotal[6][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[6][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[6][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
				break;
			}
			
			// ULTIMO
			
    		for ($i=0;$i<13;$i++)
    		{
    			if(!isset($wtotal[$i+1][0]))
    				$wtotal[$i+1][0]=0;
    			if(!isset($wtotal[$i+1][1]))
    				$wtotal[$i+1][1]=0;
    			if(!isset($wtotal[$i+1][2]))
    				$wtotal[$i+1][2]=0;
    		}

			$wtotal[10][0] = $wtotal[9][0] + $wtotal[4][0] - $wtotal[5][0] - $wtotal[6][0] + $wtotal[7][0];
			$wtotal[10][1] = $wtotal[9][1] + $wtotal[4][1] - $wtotal[5][1] - $wtotal[6][1] + $wtotal[7][1];
			$wtotal[10][2] = $wtotal[9][2] + $wtotal[4][2] - $wtotal[5][2] - $wtotal[6][2] + $wtotal[7][2];
			
			$dif=$wtotal[10][1]-$wtotal[10][2];
			$cum=0;
			if($wtotal[10][2] != 0)
				$cum=($wtotal[10][1] / $wtotal[10][2])*100;
			$var=0;
			if($wtotal[10][2] != 0)
				$var=(($wtotal[10][1]/$wtotal[10][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[10][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[10][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[10][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>UTILIDAD ANTES IMPUESTOS</b></td><td align=right>".number_format((double)$wtotal[10][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[10][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[10][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			$dif=$wtotal[12][1]-$wtotal[12][2];
			$cum=0;
			if($wtotal[12][2] != 0)
				$cum=($wtotal[12][1] / $wtotal[12][2])*100;
			$var=0;
			if($wtotal[12][2] != 0)
				$var=(($wtotal[12][1]/$wtotal[12][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[12][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[12][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[12][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>PROVISION IMPUESTOS RENTA - CREE</b></td><td align=right>".number_format((double)$wtotal[12][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[12][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[12][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";

			$wtotal[13][0]=$wtotal[10][0]-$wtotal[12][0];
			$wtotal[13][1]=$wtotal[10][1]-$wtotal[12][1];
			$wtotal[13][2]=$wtotal[10][2]-$wtotal[12][2];
			
			$dif=$wtotal[13][1]-$wtotal[13][2];
			$cum=0;
			if($wtotal[13][2] != 0)
				$cum=($wtotal[13][1] / $wtotal[13][2])*100;
			$var=0;
			if($wtotal[13][2] != 0)
				$var=(($wtotal[13][1]/$wtotal[13][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[13][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[13][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[13][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipouti'><td colspan=2><b>UTILIDAD NETA</b></td><td align=right>".number_format((double)$wtotal[13][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[13][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[13][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
             
            if(!isset($wtotal[90][0])) 
				$wtotal[90][0]=0;
			if(!isset($wtotal[90][1])) 
				$wtotal[90][1]=0;
			if(!isset($wtotal[90][2])) 
				$wtotal[90][2]=0;

			$dif=$wtotal[90][1]-$wtotal[90][2];
			$cum=0;
			if($wtotal[90][2] != 0)
				$cum=($wtotal[90][1] / $wtotal[90][2])*100;
			$var=0;
			if($wtotal[90][2] != 0)
				$var=(($wtotal[90][1]/$wtotal[90][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[90][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[90][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[90][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>INGRESOS RECIBIDOS PARA TERCEROS</b></td><td align=right>".number_format((double)$wtotal[90][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[90][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[90][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
             
            $wtotal[14][0]=$wtotal[90][0]+$wtotal[1][0];
			$wtotal[14][1]=$wtotal[90][1]+$wtotal[1][1];
			$wtotal[14][2]=$wtotal[90][2]+$wtotal[1][2];
			
			$dif=$wtotal[14][1]-$wtotal[14][2];
			$cum=0;
			if($wtotal[14][2] != 0)
				$cum=($wtotal[14][1] / $wtotal[14][2])*100;
			$var=0;
			if($wtotal[14][2] != 0)
				$var=(($wtotal[14][1]/$wtotal[14][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[14][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[14][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[14][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>FACTURACION TOTAL</b></td><td align=right>".number_format((double)$wtotal[14][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[14][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[14][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
             

	    	$dif=$wtotal[8][1]-$wtotal[8][2];
			$cum=0;
			if($wtotal[8][2] != 0)
				$cum=($wtotal[8][1] / $wtotal[8][2])*100;
			$var=0;
			if($wtotal[8][2] != 0)
				$var=(($wtotal[8][1]/$wtotal[8][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[8][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[8][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[8][2]/$ingresos3*100;
			else
				$i3=0;
            echo"<tr><td colspan=2><b>PARTICIPACION TERCEROS EN UNIDAD</b></td><td align=right>".number_format((double)$wtotal[8][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[8][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[8][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
            echo "</tabla></center>";
			echo "</div><br><br><br>";
			///ESTADO DE RESULTADOS
		}
	}
?>
</body>
</html> 
