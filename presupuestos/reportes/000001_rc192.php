<html>
<head>
  <title>MATRIX Ver. 2018-06-14</title>
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
					titulo : 'Tendencia ' ,
					tituloy: 'Millones de Pesos',
					divgrafica: 'amcharts'+i,
					filaencabezado : [0,1],
					datosadicionales : [2,3],
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
			echo "<center><table border=0>";
			echo "<tr><td class=tipoL03L>RESULTADOS</td></tr>";
			echo "<tr><td class=tipoL02M OnClick='toggleDisplay(div0)'>Resultado Acumulado</td></tr>";
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
			echo "<tr><td><center><table border=0 id='div0' style='display:none'>";
			echo "<tr><td class=tipoL04 colspan=7>RESULTADOS ".$wanoi."/".$wperi1."-".$wperi2." y ".$wanof."/".$wperf1."-".$wperf2."</td></tr>";
			$wdata=array();
			for ($i=0;$i<=1;$i++)
				for ($j=0;$j<=3;$j++)
					$wdata[$i][$j]=0;
			$query  = "select rvpcpr, SUM(rvpvre) from ".$empresa."_000044,".$empresa."_000005 ";
			$query .= "	where rvpano = ".$wanoi;
			$query .= "   and rvpemp = '".$wemp."'";
			$query .= "	  and rvpper between ".$wperi1." and ".$wperi2;
			$query .= "	  and rvpcpr between '100' and '899' ";
			$query .= "	  and rvpcco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and rvpcco in ".$wrango;
			$query .= "	  and rvpcco = ccocod   ";
			$query .= "	  and rvpemp = ccoemp   ";
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
					if(substr($row[0],0,1) != "4" and substr($row[0],0,1) != "6" and substr($row[0],0,1) != "7")
					{
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
			}
			$query  = "select rvpcpr, SUM(rvpvre), SUM(rvpvpr) from ".$empresa."_000044,".$empresa."_000005 ";
			$query .= "	where rvpano = ".$wanof;
			$query .= "   and rvpemp = '".$wemp."'";
			$query .= "	  and rvpper between ".$wperf1." and ".$wperf2;
			$query .= "	  and rvpcpr between '100' and '899' ";
			$query .= "	  and rvpcco between '".$wccoi."' and '".$wccof."'";
			if($wccoi == $wccof)
				$query = $query."    and ((ccoclas = 'PR' and rvpcpr != '290') or ccoclas != 'PR') ";
			if($call == "SIF")
				$query = $query."    and rvpcco in ".$wrango;
			$query .= "	  and rvpcco = ccocod   ";
			$query .= "	  and rvpemp = ccoemp   ";
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
					if(substr($row[0],0,1) != "4" and substr($row[0],0,1) != "6" and substr($row[0],0,1) != "7")
					{
						if($row[0] != "227" and $row[0] != "298")
						{
							if(substr($row[0],0,1) == "1")
							{
								$wdata[0][1] += $row[1];
								$wdata[0][4] += $row[2];
							}
							else
							{
								$wdata[0][1] -= $row[1];
								$wdata[0][4] -= $row[2];
							}
						}
						if(substr($row[0],0,1) == "1")
						{
							$wdata[1][1] += $row[1];
							$wdata[1][4] += $row[2];
						}
						else
						{
							$wdata[1][1] -= $row[1];
							$wdata[1][4] -= $row[2];
						}
					}
				}
			}
			$wdata[0][0]=$wdata[0][0]/1000000;
			$wdata[0][1]=$wdata[0][1]/1000000;
			$wdata[0][4]=$wdata[0][4]/1000000;
			$wdata[0][5]=$wdata[0][1]/$wdata[0][4] * 100;
			$wdata[1][5]=$wdata[1][1]/$wdata[1][4] * 100;
			$wdata[1][0]=$wdata[1][0]/1000000;
			$wdata[1][1]=$wdata[1][1]/1000000;
			$wdata[1][4]=$wdata[1][4]/1000000;
			$wdata[0][2]=$wdata[0][1] - $wdata[0][0];
			$wdata[1][2]=$wdata[1][1] - $wdata[1][0];
			$wdata[0][3]=($wdata[0][1] / $wdata[0][0] -1)*100;
			$wdata[1][3]=($wdata[1][1] / $wdata[1][0] -1)*100;
			$id=5;
			echo "<tr><td class=tipoL01>Resultados</td><td class=tipoL01>REAL: ".$wanoi."/".$wperi1."-".$wperi2."</td><td class=tipoL01>REAL: ".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL01>Dif.</td><td class=tipoL01>% Var.</td><td class=tipoL01>PPTO: ".$wanof."/".$wperf1."-".$wperf2."</td><td class=tipoL01>% Ejec.</td></tr>";
			echo "<tr><td class=tipoL02L>BRUTO (SIN COSTOS DE APOYO)</td><td class=tipoL02R>".number_format((double)$wdata[0][0],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[0][1],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[0][2],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[0][3],2,'.',',')."%</td><td class=tipoL02R>".number_format((double)$wdata[0][4],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[0][5],2,'.',',')."%</td></tr>";
			echo "<tr><td class=tipoL02L>OPERACIONAL</td><td class=tipoL02R>".number_format((double)$wdata[1][0],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1][1],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1][2],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1][3],2,'.',',')."%</td><td class=tipoL02R>".number_format((double)$wdata[1][4],0,'.',',')."</td><td class=tipoL02R>".number_format((double)$wdata[1][5],2,'.',',')."%</td></tr>";
			echo "</table></td></tr>";
			
			///TENDENCIA
			$T = array();
			echo "<tr><td class=tipoL02M OnClick='toggleDisplay(div2)'>Tendencia</td></tr>";
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
			$query = $query."    and meccpr not in ('298') ";
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
			
			for ($i=0;$i<$num3;$i++)
			{
				$T[$i][3] = $T[$i][1] - $T[$i][2];
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
			echo "<tr><td>MES</td><td>INGRESOS</td><td>COSTOS Y GASTOS</td><td>UTILIDAD</td></tr>";
			for ($i=0;$i<$num3;$i++)
			{
				echo "<tr><td class=tipoL01>".$T[$i][0]."</td><td class=tipoL02R>".number_format((double)$T[$i][1],0,'.','')."</td><td class=tipoL02R>".number_format((double)$T[$i][2],0,'.','')."</td><td class=tipoL02R>".number_format((double)$T[$i][3],0,'.','')."</td></tr>";
			}
			echo "</table></td></tr>";
			
			///TENDENCIA
			
			///PYG
			echo "<tr><td class=tipoL02M OnClick='toggleDisplay(div3)'>P y G</td></tr>";
			$wres="D";
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			if(!isset($wccof) or $wccof == "NO")
			{
				$wccoi = ver($wccoi);
				$wccof = $wccoi;
			}
			$wres=strtoupper ($wres);
			$wcif=1000000;
			$wserv="N";
			//                  0     1           2           3
			$query = "SELECT mgacoa,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where rvpano = ".$wanoi;
			$query = $query."    and rvpemp = '".$wemp."' ";
			$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and rvpcco in ".$wrango;
			$query = $query."    and rvpcco = Ccocod";
			$query = $query."    and ccoemp = '".$wemp."' ";
			if($wccoi == $wccof)
				$query = $query."    and ((ccoclas = 'PR' and mgacoa != '290') or ccoclas != 'PR') ";
			if($wgru != "Todos")             
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query = $query."    and rvpper between ".$wperi1." and ".$wperi2;
			$query = $query."    and rvpcpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by mgacoa,mganom";
			$query = $query."   order by mgacoa";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$query = "SELECT mgacoa,mganom,sum(rvpvre),sum(rvpvpr)   from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where rvpano = ".$wanof;
			$query = $query."    and rvpemp = '".$wemp."' ";
			$query = $query."    and rvpcco between '".$wccoi."' and '".$wccof."'";
			if($call == "SIF")
				$query = $query."    and rvpcco in ".$wrango;
			$query = $query."    and rvpcco = Ccocod";
			$query = $query."    and ccoemp = '".$wemp."' ";
			if($wccoi == $wccof)
				$query = $query."    and ((ccoclas = 'PR' and mgacoa != '290') or ccoclas != 'PR') ";
			if($wgru != "Todos")
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query = $query."    and rvpper between ".$wperf1." and ".$wperf2;
			$query = $query."    and rvpcpr = mgacod ";
			if($wserv == "N") 
				$query = $query."   and mgatip = '0' ";
			$query = $query."   group by mgacoa,mganom";
			$query = $query."   order by mgacoa";
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
			echo "<tr><td><center><table border=1 class=tipoTABLEGRID id='div3' style='display:none'>";
			echo "<tr><td colspan=11 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=11 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=11 align=center>ESTADO DE RESULTADOS CONSOLIDADO COMPARATIVO POR A&Ntilde;OS</td></tr>";
			echo "<tr><td colspan=11 align=center>Datos Iniciales</td></tr>";
			echo "<tr><td colspan=11 align=center>A&Ntilde;O : ".$wanoi." MES INICIAL : ".$wperi1. " MES FINAL : ".$wperi2."</td></tr>";
			echo "<tr><td colspan=11 align=center>Datos Finales</td></tr>";
			echo "<tr><td colspan=11 align=center>A&Ntilde;O : ".$wanof." MES INICIAL : ".$wperf1. " MES FINAL : ".$wperf2."</td></tr>";
			echo "<tr><td colspan=11 align=center>UNIDAD INICIAL : ".$wccoi. " UNIDAD FINAL : ".$wccof."</td></tr>";
			echo "<tr><td colspan=11 align=center>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
			echo "<tr><td colspan=11 align=center>CIFRAS EXPRESADAS EN  : ".$wcift."</td></tr>";
			echo "<tr><td colspan=11 align=center>EMPRESA : ".$wempt."</td></tr>";
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
					if ($row1[0] >= "100" and $row1[0] <= "129")
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
					if ($row1[0] >= "100" and $row1[0] <= "129")
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
					if ($row1[0] >= "100" and $row1[0] <= "129")
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
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>INGRESOS DE OPERACIONES ORDINARIAS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "100" and $wdata[$i][0] <= "129")
					$it=(integer)substr($wdata[$i][0],0,1);
				else 
					$it=0;
				if($it == 1)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					if((integer)$wdata[$i][0] == 100)
					{
						$wtotal[100][0] += $wdata[$i][2];
						$wtotal[100][1] += $wdata[$i][3];
						$wtotal[100][2] += $wdata[$i][4];
					}
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 1;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL INGRESOS DE OPERACIONES ORDINARIAS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			if($wtip == "C")
				echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>COSTOS DE OPERACION</B></td></tr>";
			else
				echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>COSTOS Y GASTOS DE ADMINISTRACION Y VENTAS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 2)
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
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 2;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			if($wtip == "C")
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL COSTOS DE OPERACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			else
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL COSTOS Y GASTOS DE ADMINISTARCION Y VENTAS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			if($wtip == "C")
				echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS DE ADMINISTRACION</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 3)
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
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 3;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			if($wtip == "C")
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS DE ADMINISTRACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			if($wtip == "C")
				echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS DE VENTAS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 8)
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
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 8;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			if($wtip == "C")
				echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS DE VENTAS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";

			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>OTROS INGRESOS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "130" and $wdata[$i][0] <= "199")
					$it=99;
				else 
					$it=0;
				if($it == 99)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					$wtotal[$it][1] += $wdata[$i][3];
					$wtotal[$it][2] += $wdata[$i][4];
					if((integer)$wdata[$i][0] == 100)
					{
						$wtotal[100][0] += $wdata[$i][2];
						$wtotal[100][1] += $wdata[$i][3];
						$wtotal[100][2] += $wdata[$i][4];
					}
					$wdif=$wdata[$i][3]-$wdata[$i][2];
					if($ingresos1 != 0)
						$wdata[$i][7]=$wdata[$i][2]/$ingresos1*100;
					if($ingresos2 != 0)
						$wdata[$i][8]=$wdata[$i][3]/$ingresos2*100;
					if($ingresos3 != 0)
						$wdata[$i][9]=$wdata[$i][4]/$ingresos3*100;
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 99;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL OTROS INGRESOS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>OTROS GASTOS DE OPERACION</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 5)
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
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 5;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL OTROS GASTOS DE OPERACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			$wtotal[10][0] = $wtotal[1][0] - $wtotal[2][0] - $wtotal[3][0] - $wtotal[8][0] - $wtotal[5][0] + $wtotal[99][0];
			$wtotal[10][1] = $wtotal[1][1] - $wtotal[2][1] - $wtotal[3][1] - $wtotal[8][1] - $wtotal[5][1] + $wtotal[99][1];
			$wtotal[10][2] = $wtotal[1][2] - $wtotal[2][2] - $wtotal[3][2] - $wtotal[8][2] - $wtotal[5][2] + $wtotal[99][2];
			
			$dif=$wtotal[10][1]-$wtotal[10][2];
			$cum=0;
			if($wtotal[10][2] != 0)
				$cum=($wtotal[10][1] / $wtotal[10][2])*100;
			$var=0;
			if($wtotal[10][0] != 0)
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
			echo"<tr class='tipouti'><td colspan=2><b>RESULTADOS DE ACTIVIDADES DE LA OPERACION</b></td><td align=right>".number_format((double)$wtotal[10][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[10][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[10][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>INGRESO FINANCIERO</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 4)
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
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 4;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL INGRESO FINANCIERO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTO FINANCIERO</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 6)
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
					if($wres == "D" and ((double)$wdata[$i][2] != 0 or (double)$wdata[$i][3] != 0 or (double)$wdata[$i][4] != 0))
					{
						echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][7],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][3],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][8],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][10],2,'.',',')."%</b></td><td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][9],2,'.',',')."%</td><td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td align=right>".number_format((double)$wdata[$i][6],2,'.',',')."%</td></tr>";
					}
				}
			}
			$it = 6;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTO FINANCIERO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			$wtotal[11][0] = $wtotal[4][0] - $wtotal[6][0];
			$wtotal[11][1] = $wtotal[4][1] - $wtotal[6][1];
			$wtotal[11][2] = $wtotal[4][2] - $wtotal[6][2];
			
			$dif=$wtotal[11][1]-$wtotal[11][2];
			$cum=0;
			if($wtotal[11][2] != 0)
				$cum=($wtotal[11][1] / $wtotal[11][2])*100;
			$var=0;
			if($wtotal[11][0] != 0)
				$var=(($wtotal[11][1]/$wtotal[11][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[11][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[11][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[11][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tiposub'><td colspan=2><b>COSTO FINANCIERO NETO</b></td><td align=right>".number_format((double)$wtotal[11][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[11][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[11][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			$wtotal[12][0] = $wtotal[10][0] + $wtotal[11][0];
			$wtotal[12][1] = $wtotal[10][1] + $wtotal[11][1];
			$wtotal[12][2] = $wtotal[10][2] + $wtotal[11][2];
			
			$dif=$wtotal[12][1]-$wtotal[12][2];
			$cum=0;
			if($wtotal[12][2] != 0)
				$cum=($wtotal[12][1] / $wtotal[12][2])*100;
			$var=0;
			if($wtotal[12][0] != 0)
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
			echo"<tr class='tipouti'><td colspan=2><b>GANANCIAS ANTES DE IMPUESTOS</b></td><td align=right>".number_format((double)$wtotal[12][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[12][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[12][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 760)
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
				}
			}
			$it = 760;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>PROVISION IMPUESTO DE RENTA Y CREE</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 770)
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
				}
			}
			$it = 770;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>IMPUESTO RENTA DIFERIDO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			$wtotal[13][0] = $wtotal[12][0] - $wtotal[760][0] - $wtotal[770][0];
			$wtotal[13][1] = $wtotal[12][1] - $wtotal[760][1] - $wtotal[770][1];
			$wtotal[13][2] = $wtotal[12][2] - $wtotal[760][2] - $wtotal[770][2];
			
			$dif=$wtotal[13][1]-$wtotal[13][2];
			$cum=0;
			if($wtotal[13][2] != 0)
				$cum=($wtotal[13][1] / $wtotal[13][2])*100;
			$var=0;
			if($wtotal[13][0] != 0)
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
			echo"<tr class='tipouti'><td colspan=2><font size=2.5><b>RESULTADOS PROCEDENTES DE OPERACIONES CONTINUADAS</b></font></td><td align=right>".number_format((double)$wtotal[13][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[13][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[13][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 900)
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
				}
			}
			$it = 900;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr><td colspan=2><b>INGRESOS PARA TERCEROS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			$wtotal[14][0] = $wtotal[100][0] + $wtotal[900][0];
			$wtotal[14][1] = $wtotal[100][1] + $wtotal[900][1];
			$wtotal[14][2] = $wtotal[100][2] + $wtotal[900][2];
			
			$dif=$wtotal[14][1]-$wtotal[14][2];
			$cum=0;
			if($wtotal[14][2] != 0)
				$cum=($wtotal[14][1] / $wtotal[14][2])*100;
			$var=0;
			if($wtotal[14][0] != 0)
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
			echo"<tr class='tipotit'><td colspan=2><b>FACTURACION TOTAL</b></td><td align=right>".number_format((double)$wtotal[14][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[14][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[14][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 750)
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
				}
			}
			$it = 750;
			$dif=$wtotal[$it][1]-$wtotal[$it][2];
			$cum=0;
			if($wtotal[$it][2] != 0)
				$cum=($wtotal[$it][1] / $wtotal[$it][2])*100;
			$var=0;
			if($wtotal[$it][0] != 0)
				$var=(($wtotal[$it][1]/$wtotal[$it][0])-1)*100;
			if($ingresos1 != 0)
				$i1=$wtotal[$it][0]/$ingresos1*100;
			else
				$i1=0;
			if($ingresos2 != 0)
				$i2=$wtotal[$it][1]/$ingresos2*100;
			else
				$i2=0;
			if($ingresos3 != 0)
				$i3=$wtotal[$it][2]/$ingresos3*100;
			else
				$i3=0;
			echo"<tr class='tipotit'><td colspan=2><b>PARTICIPACION TERCEROS EN UNIDAD</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td><td align=right>".number_format((double)$i1,2,'.',',')."%</td><td align=right>".number_format((double)$wtotal[$it][1],0,'.',',')."</td><td align=right>".number_format((double)$i2,2,'.',',')."%</td><td align=right>".number_format((double)$var,2,'.',',')."%</b></td><td align=right>".number_format((double)$wtotal[$it][2],0,'.',',')."</td><td align=right>".number_format((double)$i3,2,'.',',')."%</td><td align=right>".number_format((double)$dif,0,'.',',')."</td><td align=right>".number_format((double)$cum,2,'.',',')."%</td></tr>";
            echo "</table></td></tr>";
			///PYG
			echo "</table></center><br>";
		}
	}
?>
</body>
</html> 
