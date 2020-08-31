<html> 
<head>
<title>MATRIX</title>
<style type="text/css">
    	
  		A	{text-decoration: none;color: #000066;}
  		.tipo3V{color:#000066;background:#dddddd;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#000066;background:#99CCFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	#tipoG02{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:3em;}
    	#tipoG03{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	#tipoG04{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	#tipoM01{color:#000066;background:#cccccc;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	#tipoM02{color:#000066;background:#dddddd;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:2.5em;}
    	#tipoM03{color:#000066;background:#FFFFFF;font-size:14pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	
    </style>
    <!-- BEGIN: load jqplot -->
	 <script language="javascript" type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script> 
	<script language="javascript" type="text/javascript" src="../../../include/root/excanvas.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	
	 <!--[if IE 6]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot.css" /> 
	 <![endif]--> 
	 <!--[if IE 7]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	 <![endif]--> 
	 <!--[if IE 8]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	 <![endif]--> 
  	<script language="javascript" type="text/javascript" src="../../../include/root/jquery.jqplot.min.js"></script> 
  	<script type="text/javascript" src="../../../include/root/jqplot.pieRenderer.min.js"></script> 
	<!-- END: load jqplot -->  
	
	   <script language="javascript" type="text/javascript"> 
	    // nice unique id function generator which I don't use anymore.
	    var uID = (function() 
	    {
	      var id = 1;
	      return function(){return id++};
	    })();
	 
	    function run(id) 
	    {
			switch(id)	
			{
				case 0:
					eval(document.getElementById('grafica').value);
				break;
				case 1:
					eval(document.getElementById('grafica1').value);
				break;
			}
	    }

	</script> 

</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Cirugias Programadas Canceladas Entre Fechas Por Tiempos</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>Repcircaxt.php Ver. 2010-12-20</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
function comparacion($vec1,$vec2)
{
	if($vec1[6] > $vec2[6])
		return -1;
	elseif ($vec1[6] < $vec2[6])
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
	

	

	echo "<form action='Repcircaxt.php' method=post>";
	if(!isset($v0) or !isset($v1))
	{
	echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>CIRUGIAS PROGRAMADAS CANCELADAS ENTRE FECHAS POR TIEMPOS</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Incial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query  = " select Mcafec, tcx_000007.Fecha_data, Mcacau, Candes from tcx_000007,tcx_000001 ";
		$query .= " where tcx_000007.Fecha_data between '".$v0."' and '".$v1."' ";
		$query .= "   and Mcacau = Cancod ";
		$query .= "   order by 3 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<center>";
		echo "<table border=0>";
		echo "<tr><td colspan=14 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>CIRUGIAS PROGRAMADAS CANCELADAS ENTRE FECHAS POR TIEMPOS</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>X FECHAS - ENTRE : ".$v0." y ".$v1."</b></td></tr>";
		echo "<tr><td bgcolor=#ffffff colspan=2></td><td bgcolor=#999999 colspan=5 align=center><b>Tiempo de Cancelacion En Horas</b></td>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>Codigo<br>Cancelacion</b></td>";
		echo "<td bgcolor=#cccccc><b>Descripcion</b></td>";
		echo "<td bgcolor=#cccccc><b>x <= 24</b></td>";
		echo "<td bgcolor=#cccccc><b>24 > x <=48</b></td>";
		echo "<td bgcolor=#cccccc><b>48 > x <=72</b></td>";
		echo "<td bgcolor=#cccccc><b>x > 72</b></td>";
		echo "<td bgcolor=#cccccc><b>Total</b></td>";
		echo "</tr>"; 
		$cancel = array();
		$grupos=array();
		$k=-1;
		$ant="";
		$w=0;
		$cod="";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($ant != $row[2])
			{
				$k++;
				$ant = $row[2];
				$cancel[$k][0]=$row[2];
				$cancel[$k][1]=$row[3];
				$cancel[$k][2]=0;
				$cancel[$k][3]=0;
				$cancel[$k][4]=0;
				$cancel[$k][5]=0;
				$cancel[$k][6]=0;
			}
			if($cod != substr($row[2],0,2))
			{
				$w++;
				$cod = substr($row[2],0,2);
				$grupos[$w][0]=$row[2];
				$grupos[$w][1]=substr($row[3],0,strpos($row[3],"/"));
				$grupos[$w][2]=0;
				$grupos[$w][3]=0;
				$grupos[$w][4]=0;
				$grupos[$w][5]=0;
				$grupos[$w][6]=0;	
				$grupos[$w][7]=0;	
			}
			$ann=(integer)substr($row[0],0,4)*360 +(integer)substr($row[0],5,2)*30 + (integer)substr($row[0],8,2);
			$aa=(integer)substr($row[1],0,4)*360 +(integer)substr($row[1],5,2)*30 + (integer)substr($row[1],8,2);
			$tempo=($ann - $aa)*24;
			if($tempo <= 24)
			{
				$cancel[$k][2]++;
				$grupos[$w][2]++;
			}
			elseif($tempo > 24 and $tempo <= 48)
				{
					$cancel[$k][3]++;
					$grupos[$w][3]++;
				}
				elseif($tempo > 48 and $tempo <= 72)
					{
						$cancel[$k][4]++;
						$grupos[$w][4]++;
					}
					else
					{
						$cancel[$k][5]++;
						$grupos[$w][5]++;
					}
			$cancel[$k][6]++;
			$grupos[$w][6]++;
		}
		usort($cancel,'comparacion');
		for ($i=0;$i<=$k;$i++)
		{
			if($i % 2 == 0)
				$color="#99CCFF";
			else
				$color="#FFFFFF";
			echo "<tr>";
			echo "<td bgcolor=".$color.">".$cancel[$i][0]."</td>";
			echo "<td bgcolor=".$color.">".$cancel[$i][1]."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$cancel[$i][2],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$cancel[$i][3],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$cancel[$i][4],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$cancel[$i][5],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$cancel[$i][6],0,'.',',')."</td>";
			echo "</tr>"; 
		}
		$ult= $k + 1;
		$cancel[$ult][0]="";
		$cancel[$ult][1]="";
		$cancel[$ult][2]= 0;
		$cancel[$ult][3]= 0;
		$cancel[$ult][4]= 0;
		$cancel[$ult][5]= 0;
		$cancel[$ult][6]= 0;
		for ($i=0;$i<=$k;$i++)
		{
			$cancel[$ult][2] += $cancel[$i][2];
			$cancel[$ult][3] += $cancel[$i][3];
			$cancel[$ult][4] += $cancel[$i][4];
			$cancel[$ult][5] += $cancel[$i][5];
			$cancel[$ult][6] += $cancel[$i][6];
		}
		
		$p=array();
		for ($i=0;$i<5;$i++)
		{
			$p[$i]=$cancel[$ult][$i+2]/$cancel[$ult][6] * 100;
		}
		$color="#FFCC66";
		echo "<tr>";
		echo "<td bgcolor=".$color."  colspan=2><b>PORCENTAJES</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[0],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[1],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[2],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[3],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[4],0,'.',',')."%</b></td>";
		echo "</tr>";
		$color="#999999";
		echo "<tr>";
		echo "<td bgcolor=".$color."  colspan=2><b>TOTAL GENERAL</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$cancel[$ult][2],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$cancel[$ult][3],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$cancel[$ult][4],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$cancel[$ult][5],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$cancel[$ult][6],0,'.',',')."</b></td>";
		echo "</tr>";
		echo "</table>"; 
		echo "</center>";
		
		echo "<br><center><table border=0 cellspacing=0><tr><td>";

		$wtitulo="Cirugias Canceladas Entre Fechas Por Tiempos";
		$DATOS = "line1=[['".number_format($p[0],0,'.',',')."% --- C <= 24h',".$cancel[$ult][2]."], ['".number_format($p[1],0,'.',',')."% --- 24h < C <= 48h',".$cancel[$ult][3]."], ['".number_format($p[2],0,'.',',')."% --- 40h < C <= 72h',".$cancel[$ult][4]."], ['".number_format($p[3],0,'.',',')."% --- 72h < C',".$cancel[$ult][5]."]];";
		
		$XAL=350;
		$XAN=650;
		
		echo "<div class='jqPlot' id='chart1' style='height:".$XAL."px; width:".$XAN."px;'></div>";
		echo "</td></tr></table></center>";
		
		echo "<input type=hidden id='grafica' value=\"".$DATOS." plot10 = $.jqplot('chart1', [line1], { title:'".$wtitulo."', seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}},legend:{show:true}});\"> ";
		echo "<script>run(0);</script>";
		$graficar=1;
		echo "<input type='HIDDEN' name= 'graficar' value='".$graficar."'>";
		
		echo "<br><br><center>";
		echo "<table border=0>";
		echo "<tr><td colspan=14 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>CIRUGIAS CANCELADAS  X GRUPOS ENTRE FECHAS POR TIEMPOS</b></td></tr>";
		echo "<tr><td colspan=14 align=center><b>X FECHAS - ENTRE : ".$v0." y ".$v1."</b></td></tr>";
		echo "<tr><td bgcolor=#ffffff colspan=2></td><td bgcolor=#999999 colspan=6 align=center><b>Tiempo de Cancelacion En Horas</b></td>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>Grupo<br>Cancelacion</b></td>";
		echo "<td bgcolor=#cccccc><b>Descripcion</b></td>";
		echo "<td bgcolor=#cccccc><b>x <= 24</b></td>";
		echo "<td bgcolor=#cccccc><b>24 > x <=48</b></td>";
		echo "<td bgcolor=#cccccc><b>48 > x <=72</b></td>";
		echo "<td bgcolor=#cccccc><b>x > 72</b></td>";
		echo "<td bgcolor=#cccccc><b>Total</b></td>";
		echo "<td bgcolor=#cccccc><b>%Por.</b></td>";
		echo "</tr>";
		$ult= $w + 1;
		$grupos[$ult][0]="";
		$grupos[$ult][1]="";
		$grupos[$ult][2]= 0;
		$grupos[$ult][3]= 0;
		$grupos[$ult][4]= 0;
		$grupos[$ult][5]= 0;
		$grupos[$ult][6]= 0;
		for ($i=1;$i<=$w;$i++)
		{
			$grupos[$ult][2] += $grupos[$i][2];
			$grupos[$ult][3] += $grupos[$i][3];
			$grupos[$ult][4] += $grupos[$i][4];
			$grupos[$ult][5] += $grupos[$i][5];
			$grupos[$ult][6] += $grupos[$i][6];
		}
		$TP=0;
		for ($i=1;$i<=$w;$i++)
		{
			$grupos[$i][7]=$grupos[$i][6] / $grupos[$ult][6] * 100;
			$TP += $grupos[$i][7];
		}
		for ($i=1;$i<=$w;$i++)
		{
			if($i % 2 == 0)
				$color="#99CCFF";
			else
				$color="#FFFFFF";
			echo "<tr>";
			echo "<td bgcolor=".$color.">".substr($grupos[$i][0],0,2)."</td>";
			echo "<td bgcolor=".$color.">".$grupos[$i][1]."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$grupos[$i][2],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$grupos[$i][3],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$grupos[$i][4],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$grupos[$i][5],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$grupos[$i][6],0,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=right>".number_format((double)$grupos[$i][7],0,'.',',')."%</td>";
			echo "</tr>"; 
		}
		
		
		$p=array();
		for ($i=0;$i<5;$i++)
		{
			$p[$i]=$grupos[$ult][$i+2]/$grupos[$ult][6] * 100;
		}
		$color="#FFCC66";
		echo "<tr>";
		echo "<td bgcolor=".$color."  colspan=2><b>PORCENTAJES</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[0],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[1],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[2],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[3],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$p[4],0,'.',',')."%</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$TP,0,'.',',')."%</b></td>";
		echo "</tr>";
		$color="#999999";
		echo "<tr>";
		echo "<td bgcolor=".$color."  colspan=2><b>TOTAL GENERAL</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$grupos[$ult][2],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$grupos[$ult][3],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$grupos[$ult][4],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$grupos[$ult][5],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>".number_format((double)$grupos[$ult][6],0,'.',',')."</b></td>";
		echo "<td bgcolor=".$color." align=right><b>&nbsp;</b></td>";
		echo "</tr>";
		echo "</table>"; 
		echo "</center>";
		echo "<br><center><table border=0 cellspacing=0><tr><td>";

		$wtitulo1="Cirugias Canceladas x Grupos Entre Fechas Por Tiempos";
		$DATOS="line1=[";
		for ($i=1;$i<=$w;$i++)
		{
			if($i == 1)
				$DATOS .= "['".number_format($grupos[$i][7],0,'.',',')."% --- Grupo : ".$i."',".number_format($grupos[$i][7],0,'.',',')."]";
			else
				$DATOS .= ",['".number_format($grupos[$i][7],0,'.',',')."% --- Grupo : ".$i."',".number_format($grupos[$i][7],0,'.',',')."]";
		}
		$DATOS .= "];";
		
		$XAL=350;
		$XAN=650;
		
		echo "<div class='jqPlot' id='chart2' style='height:".$XAL."px; width:".$XAN."px;'></div>";
		echo "</td></tr></table></center>";
		
		echo "<input type=hidden id='grafica1' value=\"".$DATOS." plot10 = $.jqplot('chart2', [line1], { title:'".$wtitulo1."', seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:8}},legend:{show:true}});\"> ";
		echo "<script>run(1);</script>";
	}
}
?>
</body>
</html>
