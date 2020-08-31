<html>
<head>
  <title>MATRIX Ver. 2018-01-15</title>
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
		.tipoL01{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL01R{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		.tipoL02R{color:#000066;background:#E8EEF7;font-size:11pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		.tipoL02L{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		
		.tipoL011{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL051{color:#000066;background:#CCCCCC;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL05R1{color:#000066;background:#CCCCCC;font-size:11pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		
		.tipoL02R1{color:#000066;background:#E8EEF7;font-size:11pt;font-family:Arial;font-weight:normal;text-align:right;height:2em;}
		.tipoL02L1{color:#000066;background:#E8EEF7;font-size:11pt;font-family:Arial;font-weight:normal;text-align:left;height:2em;}
		
		.tipoL02R2{color:#000066;background:#FFFFFF;font-size:11pt;font-family:Arial;font-weight:normal;text-align:right;height:2em;}
		.tipoL02L2{color:#000066;background:#FFFFFF;font-size:11pt;font-family:Arial;font-weight:normal;text-align:left;height:2em;}
		
		.tipoL02M{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		.tipoL02MC{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL03L{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL03{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL04{color:#000066;background:#DDDDDD;foval2nt-size:12pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		.tipoL05{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL05R{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
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
		function Graficar1(i)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Ingresos Propios ' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amcharts'+i,
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column',
					rotulos : 'si',
					opcionesdelgrafico:'no',
					monocromatico : 'si'
			});
		}
		function Graficar(i)
		{
			$('#tablaresultados'+i+"1").LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : '',
					divgrafica: 'amcharts'+i+"1",
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column',
					rotulos : 'si',
					opcionesdelgrafico : 'no',
					monocromatico : 'si'
			});
			$('#tablaresultados'+i+"2").LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : '',
					divgrafica: 'amcharts'+i+"2",
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column',
					rotulos : 'si',
					opcionesdelgrafico : 'no',
					monocromatico : 'si'
			});
			$('#tablaresultados'+i+"3").LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : '',
					divgrafica: 'amcharts'+i+"3",
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column',
					rotulos : 'si',
					opcionesdelgrafico : 'no',
					monocromatico : 'si'
			});
			$('#tablaresultados'+i+"4").LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : '',
					divgrafica: 'amcharts'+i+"4",
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column',
					rotulos : 'si',
					opcionesdelgrafico : 'no',
					monocromatico : 'si'
			});
			$('#tablaresultados'+i+"5").LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : '',
					divgrafica: 'amcharts'+i+"5",
					filaencabezado : ['nada',0],
					datosadicionales : 'nada',
					tipografico : 'column',
					rotulos : 'si',
					opcionesdelgrafico : 'no',
					monocromatico : 'si'
			});
		}
		function Graficarl(i)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Tendencia ' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amcharts'+i,
					filaencabezado : [0,1],
					datosadicionales : [2],
					tipografico : 'smoothedLine',
					rotulos : 'si',
					opcionesdelgrafico:'no'
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
					tipografico : 'dona',
					opcionesdelgrafico:'no'
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
function comparacion1($vec1,$vec2)
{
	global $wperf2;
	$wcla = $wperf2 + 2;
	if($vec1[$wcla] > $vec2[$wcla])
		return -1;
	elseif ($vec1[$wcla] < $vec2[$wcla])
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
		

		

		echo "<form name='analisis' action='000001_rc189.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wccoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wccof) or !isset($wgru) or !isset($wanoi) or !isset($wperi1)  or !isset($wperi2) or $wperi1 < 1 or $wperi1 > 12 or $wperi2 < 1 or $wperi2 > 12 or $wperi1 > $wperi2 or !isset($wanof) or !isset($wperf1)  or !isset($wperf2) or $wperf1 < 1 or $wperf1 > 12 or $wperf2 < 1 or $wperf2 > 12 or $wperf1 > $wperf2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ANALISIS DE UNIDADES Ver. 2017-11-08</td></tr>";
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
			if($call == "SIF")
			{
				str_replace("(","(".chr(39),$wrango);
				str_replace(")",")".chr(39),$wrango);
				str_replace(",",chr(39).",".chr(39),$wrango);
				$wccoi = substr($wccoi,0,4);
				$wccof = substr($wccof,0,4);
			}
			echo "<center><table border=0>";
			echo "<tr><td class=tipoL03L>INGRESOS</td></tr>";
			echo "<tr><td class=tipoL02M OnClick='toggleDisplay(div0)'>Comportamiento</td></tr>";
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
			$query = "SELECT sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanoi;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and meccco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and meccco in ".$wrango;
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
			if($call == "SIF")
				$query = $query."    and meccco in ".$wrango;
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
			if($call == "SIF")
				$query = $query."    and rvpcco in ".$wrango;
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
			
			echo "<tr><td><center><table border=0 CELLPADDING=1 id='div0' style='display:none'>";
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
			echo "<tr><td class=tipoL02L>INGRESOS POR PRESTACION DE SERVICIOS</td><td class=tipoL02R>".number_format((double)$wdata[0],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[3],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[4],2,'.',',')."%</td><td class=tipoL02R>".number_format((double)$wdata[2],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[5],2,'.',',')."%</td></tr>";
			echo "<tr><td colspan=7><button type='button' onclick='toggleDisplay(seg".$id.");Graficar1(".$id.");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='Graficar1(".$id.");'></td></tr>";
			echo "<tr id='seg".$id."' style='display: none'><td colspan=7><table align='center' >";
			echo "<tr>";
			echo "<td colspan=7><div id='amcharts".$id."' style='width:500px; height:200px;'></div></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			echo "<center><table border=1 id='tablaresultados".$id."' class=tipo3GRID>";
			echo "<tr><td>ITEM</td><td>VALOR</td></tr>";
			echo "<tr><td class=tipoL01>Real ".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL02R>".number_format((double)$wdata[0],0,'.','')."</td></tr>";
			echo "<tr><td class=tipoL01>Real ".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL02R>".number_format((double)$wdata[1],0,'.','')."</td></tr>";
			echo "<tr><td class=tipoL01>Ppto ".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL02R>".number_format((double)$wdata[2],0,'.','')."</td></tr>";
			echo "</table></td></tr>";
			
			/// SEGEMENTACION
			echo "<tr><td class=tipoL02M OnClick='toggleDisplay(div1)'>Segmentacion</td></tr>";
			echo "<tr><td><center><table border=0 id='div1' style='display:none'>";
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
			echo "<tr><td><center><table border=0>";
			echo "<tr><td class=tipoL04 colspan=2>SEGMENTACION DE INGRESOS ".$wanoi."/".$wperi1."-".$wperi2." y ".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			if($wgru == "Todos")
			{
				$query  = "select mioano,empseg,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061 ";
				$query .= " where mioano = ".$wanoi;
				$query .= "   and mioemp = '".$wemp."'";
				$query .= "   and miomes between ".$wperi1." and ".$wperi2;
				$query .= "   and miocco between '".$wccoi."' and '".$wccof."'";
				if($call == "SIF")
					$query = $query."    and miocco in ".$wrango;
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
				if($call == "SIF")
					$query = $query."    and miocco in ".$wrango;
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
				echo "<tr><td><button type='button' onclick='toggleDisplay(seg".$id1.");GraficarS(".$id2.",\"".$wanof."/".$wperf1."-".$wperf2."\");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='GraficarS(".$id2.",\"".$wanof."/".$wperf1."-".$wperf2."\");'></td></tr>";
				echo "<tr id='seg".$id1."' style='display: none'><td><table align='center' >";
				echo "<tr>";
				echo "<td><div id='amcharts".$id2."' style='width:600px; height:400px;'></div></td>";
				//************************************************************************************************************************
				///SEGMENTACION
				$id=9;
				echo "<td>";
				echo "<button type='button' onclick='toggleDisplay(seg".$id.");'>Detalle Segmentaci&oacute;n</button>";
				echo "<div id='seg".$id."' style='display: block'>";
				$wanopa=$wanop-1;
				$query = "select segcod,segtip,segdes,sum(Mioito) as monto from ".$empresa."_000063,".$empresa."_000061,".$empresa."_000045,".$empresa."_000005 ";
				$query = $query."  where mioano = ".$wanoi;
				$query = $query."    and mioemp = '".$wemp."'";
				$query = $query."    and miomes between ".$wperi1." and ".$wperi2;
				$query = $query."    and miocco between '".$wccoi."' and '".$wccof."'";
				if($call == "SIF")
					$query = $query."    and miocco in ".$wrango;
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
				if($call == "SIF")
					$query = $query."    and miocco in ".$wrango;
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
				echo "<center><table border=0>";
				echo "<tr><td class=tipoL011>SEGMENTO</td><td class=tipoL011>".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL011>% PART</td><td  class=tipoL011>".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL011>% PART</td><td align=right class=tipoL011>DIF.</td><td align=right class=tipoL011>% VAR.</td></tr>";
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
					if($i % 2 == 0)
						$lin = 1;
					else
						$lin = 2;
					echo"<tr><td class=tipoL02L".$lin.">".$wdata[$i][0]."</td><td class=tipoL02R".$lin.">".number_format((double)$wdata[$i][1],0,'.',',')."</td><td class=tipoL02R".$lin.">".number_format((double)$proant,2,'.',',')."</td><td class=tipoL02R".$lin.">".number_format((double)$wdata[$i][2],0,'.',',')."</td><td class=tipoL02R".$lin.">".number_format((double)$proact,2,'.',',')."</td><td class=tipoL02R".$lin.">".number_format((double)$wdata[$i][3],0,'.',',')."</td><td class=tipoL02R".$lin.">".number_format((double)$wdata[$i][4],2,'.',',')."</td></tr>";
				}
				$proant= 100;
				$proact= 100;
				$wdif=$valact-$valant;
				if($valant != 0)
					$var=($valact - $valant)/$valant *100;
				else
					$var=0;
				echo"<tr><td class=tipoL051>TOTAL FACTURADO</td><td class=tipoL05R1>".number_format((double)$valant,0,'.',',')."</td><td class=tipoL05R1>".number_format((double)$proant,2,'.',',')."</td><td class=tipoL05R1>".number_format((double)$valact,0,'.',',')."</td><td class=tipoL05R1>".number_format((double)$proact,2,'.',',')."</td><td class=tipoL05R1>".number_format((double)$wdif,0,'.',',')."</td><td class=tipoL05R1>".number_format((double)$var,2,'.',',')."</td></tr>";
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
				echo"<tr><td class=tipoL051>TOTAL PAP</td><td class=tipoL05R1>".number_format((double)$vpapant,0,'.',',')."</td><td class=tipoL05R1>".number_format((double)$proant,2,'.',',')."</td><td class=tipoL05R1>".number_format((double)$vpapact,0,'.',',')."</td><td class=tipoL05R1>".number_format((double)$proact,2,'.',',')."</td><td class=tipoL05R1>".number_format((double)$wdif,0,'.',',')."</td><td class=tipoL05R1>".number_format((double)$var,2,'.',',')."</td></tr></table></center>";
				echo "</div>";
				echo "</table></td></tr>";
				///SEGMENTACION
				echo "</td>";
				//************************************************************************************************************************
				echo "</tr>";
				echo "</table>";
				echo "</td></tr>";
				echo "</table></center>";
				echo "<center><table border=1 id='tablaresultados".$id1."' class=tipo3GRID>";
				echo "<tr><td>ITEM</td><td>VALOR</td></tr>";
				for ($i=0;$i<7;$i++)
					echo "<tr><td class=tipoL01>".$seg[$i][0]."</td><td class=tipoL02R>".number_format((double)$seg[$i][1],0,'.','')."</td></tr>";
				echo "</table></center>";
				
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
					if($call == "SIF")
						$query = $query."    and miocco in ".$wrango;
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
					if($call == "SIF")
						$query = $query."    and miocco in ".$wrango;
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
					echo "</table></center>";
				}
			}
			///TENDENCIA
			$T = array();
			echo "<tr><td class=tipoL02M OnClick='toggleDisplay(div2)'>Tendencia</td></tr>";
			$query = "SELECT mecmes,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanof;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and meccco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and meccco in ".$wrango;
			$query = $query."    and meccpr = '100' ";
			$query = $query."    and meccco = Ccocod";
			$query = $query."    and mecemp = ccoemp ";
			if($wgru != "Todos")
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query = $query."    and mecmes between ".$wperf1." and ".$wperf2;
			$query = $query."  Group by 1 ";
			$query = $query."  Order by 1 ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			if($num2 > 0)
			{
				for ($i=0;$i<$num2;$i++)
				{
					$row2 = mysql_fetch_array($err2);
					$T[$i][0] = $row2[0];
					$T[$i][1] = (integer)($row2[1] / 1000000);
				}
			}
			
			$query = "SELECT rvpper,sum(rvpvpr) from ".$empresa."_000044,".$empresa."_000005 ";
			$query = $query."  where rvpano = ".$wanof;
			$query = $query."    and rvpemp = '".$wemp."'";
			$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and rvpcco in ".$wrango;
			$query = $query."    and rvpcpr = '100' ";
			$query = $query."    and rvpcco = Ccocod";
			$query = $query."    and rvpemp = ccoemp ";
			if($wgru != "Todos")
			{
				$query = $query."    and Ccouni = '".$wgru."' ";
			}
			$query = $query."    and rvpper between ".$wperf1." and ".$wperf2;
			$query = $query."  Group by 1 ";
			$query = $query."  Order by 1 ";
			$err3 = mysql_query($query,$conex);
			$num3 = mysql_num_rows($err3);
			if($num3 > 0)
			{
				for ($i=0;$i<$num3;$i++)
				{
					$row3 = mysql_fetch_array($err3);
					$T[$i][2] = (integer)($row3[1] / 1000000);
				}
			}
			$id=77;
			echo "<tr><td><center><table border=0 id='div2' style='display:none'>";
			echo "<tr><td colspan=7><button type='button' onclick='toggleDisplay(seg".$id.");Graficarl(".$id.");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='Graficarl(".$id.");'></td></tr>";
			echo "<tr id='seg".$id."' style='display: none'><td colspan=3><table align='center'>";
			echo "<tr>";
			echo "<td colspan=3><div id='amcharts".$id."' style='width:900px; height:500px;'></div></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			echo "<center><table border=1 id='tablaresultados".$id."' class=tipo3GRID>";
			echo "<tr><td>MES</td><td>REAL</td><td>PRESUPUESTADO</td></tr>";
			for ($i=0;$i<$num3;$i++)
				echo "<tr><td class=tipoL01>".$T[$i][0]."</td><td class=tipoL02R>".number_format((double)$T[$i][1],0,'.','')."</td><td class=tipoL02R>".number_format((double)$T[$i][2],0,'.','')."</td></tr>";
			echo "</table></td></tr>";
			
			///TENDENCIA

			///CLIENTES
			$meses=array();
			$meses[1]="ENERO";
			$meses[2]="FEBRERO";
			$meses[3]="MARZO";
			$meses[4]="ABRIL";
			$meses[5]="MAYO";
			$meses[6]="JUNIO";
			$meses[7]="JULIO";
			$meses[8]="AGOSTO";
			$meses[9]="SEPTIEMBRE";
			$meses[10]="OCTUBRE";
			$meses[11]="NOVIEMBRE";
			$meses[12]="DICIEMBRE";
			$wfactor = 1;
			echo "<tr><td class=tipoL02M OnClick='toggleDisplay(div4)'>Clientes</td></tr>";
			//                   0       1       2        3        
			$query  = "select Empcin, Empdes, Miomes, sum(Mioinp) from ".$empresa."_000063,".$empresa."_000061 ";
			$query .= "	where Mioano =  ".$wanof;
			$query .= "	  and Miomes between ".$wperf1." and ".$wperf2." ";
			$query .= "   and Mioemp = '".$wemp."'";
			$query .= "	  and Miocco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and Miocco in ".$wrango;
			$query .= "	  and Mionit = Epmcod ";
			$query .= "	  and Mioemp = Empemp ";
			$query .= "	group by 1,2,3  ";
			$query .= "	order by 1,3 ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$ncol=3 + ($wperf2 - $wperf1) + 1;
				echo "<br><table border=0 align=center CELLPADDING=1 id='div4' style='display:none'>";
				echo "<tr><td bgcolor=#cccccc><font size=2><b>CLIENTE</b></font></td>";
				for ($i=$wperf1;$i<=$wperf2;$i++)
				{
					echo "<td bgcolor=#cccccc align=center><font size=2><b>".$meses[$i]."</b></font></td>";
				}
				echo "</tr>";
				$clave="";
				$s = -1;
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($clave != $row[0])
					{
						$s++;
						$clave = $row[0];
						$data[$s][0] = $row[0];
						$data[$s][1] = $row[1];
					}
					$data[$s][$row[2]+2] = $row[3];
				}
				$wnombres = "";
				usort($data,'comparacion1');
				if($s > 5)
					$s = 5;
				for ($w=0;$w<$s;$w++)
				{ 
					$ncolor++;
					if($ncolor % 2 == 0)
						$color="#FFFFFF";
					else
						$color="#99CCFF";
					echo "<tr><td bgcolor=".$color."><font size=2>".$data[$w][1]."</font></td>";
					for ($i=$wperf1;$i<=$wperf2;$i++)
						echo "<td bgcolor=".$color." align=right><font size=2>".number_format((double)$data[$w][$i+2],0,'.',',')."</font></td>";
				}
				echo "</tr>";
			}
			$T = array();
			$id=88;
			echo "<tr><td colspan=".$ncol."><button type='button' onclick='toggleDisplay(seg".$id.");Graficar(".$id.");'>Graficar</button>&nbsp;<IMG SRC='/matrix/images/medical/presupuestos/graf1.png' style='vertical-align:middle;' onload='Graficar(".$id.");'></td></tr>";
			echo "<tr id='seg".$id."' style='display: none'><td colspan=".$ncol."><table align='center' >";
			echo "<tr>";
			echo "<td colspan=".$ncol."><table border=1><tr><td class=tipoL02MC>".$data[0][1]."</td><td class=tipoL02MC>".$data[1][1]."</td><td class=tipoL02MC>".$data[2][1]."</td><td class=tipoL02MC>".$data[3][1]."</td><td class=tipoL02MC>".$data[4][1]."</td></tr><tr><td><div id='amcharts".$id."1' style='width:350px; height:200px;'></div></td><td><div id='amcharts".$id."2' style='width:350px; height:200px;'></div></td><td><div id='amcharts".$id."3' style='width:350px; height:200px;'></div></td><td><div id='amcharts".$id."4' style='width:350px; height:200px;'></div></td><td><div id='amcharts".$id."5' style='width:350px; height:200px;'></div></td></tr></table></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			for ($i=$wperf1;$i<=$wperf2;$i++)
			{
				$T[$i][0] = $i;
				$T[$i][1] = (integer)($data[0][$i+2] / 1000000);
			}
			echo "<center><table border=1 id='tablaresultados".$id."1' class=tipo3GRID>";
			echo "<tr><td>MES</td><td>INGRESOS PROPIOS</td></tr>";
			for ($i=$wperf1;$i<=$wperf2;$i++)
				echo "<tr><td class=tipoL01>".$T[$i][0]."</td><td class=tipoL02R>".number_format((double)$T[$i][1],0,'.','')."</td></tr>";
			echo "</table></td></tr>";
			for ($i=$wperf1;$i<=$wperf2;$i++)
			{
				$T[$i][0] = $i;
				$T[$i][1] = (integer)($data[1][$i+2] / 1000000);
			}
			echo "<center><table border=1 id='tablaresultados".$id."2' class=tipo3GRID>";
			echo "<tr><td>MES</td><td>INGRESOS PROPIOS</td></tr>";
			for ($i=$wperf1;$i<=$wperf2;$i++)
				echo "<tr><td class=tipoL01>".$T[$i][0]."</td><td class=tipoL02R>".number_format((double)$T[$i][1],0,'.','')."</td></tr>";
			echo "</table></td></tr>";		
			for ($i=$wperf1;$i<=$wperf2;$i++)
			{
				$T[$i][0] = $i;
				$T[$i][1] = (integer)($data[2][$i+2] / 1000000);
			}
			echo "<center><table border=1 id='tablaresultados".$id."3' class=tipo3GRID>";
			echo "<tr><td>MES</td><td>INGRESOS PROPIOS</td></tr>";
			for ($i=$wperf1;$i<=$wperf2;$i++)
				echo "<tr><td class=tipoL01>".$T[$i][0]."</td><td class=tipoL02R>".number_format((double)$T[$i][1],0,'.','')."</td></tr>";
			echo "</table></td></tr>";	
			for ($i=$wperf1;$i<=$wperf2;$i++)
			{
				$T[$i][0] = $i;
				$T[$i][1] = (integer)($data[3][$i+2] / 1000000);
			}
			echo "<center><table border=1 id='tablaresultados".$id."4' class=tipo3GRID>";
			echo "<tr><td>MES</td><td>INGRESOS PROPIOS</td></tr>";
			for ($i=$wperf1;$i<=$wperf2;$i++)
				echo "<tr><td class=tipoL01>".$T[$i][0]."</td><td class=tipoL02R>".number_format((double)$T[$i][1],0,'.','')."</td></tr>";
			echo "</table></td></tr>";	
			for ($i=$wperf1;$i<=$wperf2;$i++)
			{
				$T[$i][0] = $i;
				$T[$i][1] = (integer)($data[4][$i+2] / 1000000);
			}
			echo "<center><table border=1 id='tablaresultados".$id."5' class=tipo3GRID>";
			echo "<tr><td>MES</td><td>INGRESOS PROPIOS</td></tr>";
			for ($i=$wperf1;$i<=$wperf2;$i++)
				echo "<tr><td class=tipoL01>".$T[$i][0]."</td><td class=tipoL02R>".number_format((double)$T[$i][1],0,'.','')."</td></tr>";
			echo "</table></td></tr>";
			///CLIENTES
			
			echo "</table></center><br>";
		}
	}
?>
</body>
</html> 
