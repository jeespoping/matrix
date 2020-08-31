<html>
<head>
  <title>MATRIX Ver. 2018-02-15</title>
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
		.tipoL01R{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		.tipoL02R{color:#000066;background:#E8EEF7;font-size:11pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		.tipoL02L{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		.tipoL02M{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		.tipoL03L{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL03{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL04{color:#000066;background:#DDDDDD;font-size:12pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
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
		function Graficarl(i)
		{
			$('#tablaresultados'+i).LeerTablaAmericas(
			{ 
					empezardesdefila: 1,
					titulo : 'Margen Ebitda ' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amcharts'+i,
					filaencabezado : [0,1],
					datosadicionales : 'todo',
					tipografico : 'smoothedLine',
					rotulos : 'si'
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
					tipografico : 'dona'
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
		

		

		echo "<form name='analisis' action='000001_rc192.php' method=post>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			echo "<center><table border=0>";
			echo "<tr><td class=tipoL03L>INDICADORES</td></tr>";
			///TENDENCIA
			$T = array();
			echo "<tr><td class=tipoL02M OnClick='toggleDisplay(div2)'>Margen Ebitda</td></tr>";
			$query = "SELECT mecmes,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanof;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and meccco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and meccco in ".$wrango;
			$query = $query."    and meccpr between '100' and '129' ";
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
			
			$query = "SELECT mecmes,sum(mecval) as wmonto from ".$empresa."_000026,".$empresa."_000005 ";
			$query = $query."  where mecano = ".$wanof;
			$query = $query."    and mecemp = '".$wemp."'";
			$query = $query."    and meccco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and meccco in ".$wrango;
			$query = $query."    and MID(meccpr,1,1) in ('2','3','5','8') ";
			$query = $query."    and meccpr not in ('203','204','303','304','517','535','803','804','298') ";
			$query = $query."    and meccco = Ccocod";
			$query = $query."    and mecemp = ccoemp ";
			if($wgru != "Todos")
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query = $query."    and mecmes between ".$wperf1." and ".$wperf2;
			$query = $query."  Group by 1 ";
			$query = $query."  Order by 1 ";
			$err3 = mysql_query($query,$conex);
			$num3 = mysql_num_rows($err2);
			if($num3 > 0)
			{
				for ($i=0;$i<$num3;$i++)
				{
					$row3 = mysql_fetch_array($err3);
					$T[$i][2] = (integer)($row3[1] / 1000000);
				}
			}
			$SumaIng = 0;
			$SumaUtl = 0;
			for ($i=0;$i<$num3;$i++)
			{
				$T[$i][3] = ($T[$i][1] - $T[$i][2]) / $T[$i][1] * 100;
				$SumaIng += $T[$i][1];
				$SumaUtl += $T[$i][1] - $T[$i][2];
			}
			$EbitdaA = $SumaUtl / $SumaIng * 100;

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
			//echo "<center><table border=1 id='tablaresultados".$id."'>";
			echo "<center><table border=1 id='tablaresultados".$id."' class=tipo3GRID>";
			echo "<tr><td>MES</td><td>EBITDA</td><td>EBITDA ANUAL</td></tr>";
			for ($i=0;$i<$num3;$i++)
			{
				echo "<tr><td class=tipoL01>".$T[$i][0]."</td><td class=tipoL02R>".number_format((double)$T[$i][3],2,'.','')."</td><td class=tipoL02R>".number_format((double)$EbitdaA,2,'.','')."</td></tr>";
			}
			echo "</table></td></tr>";
			///TENDENCIA
			echo "</table></center><br>";
		}
	}
?>
</body>
</html> 
