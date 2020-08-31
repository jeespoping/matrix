<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}
    	.tipotot{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;}
    	.tipotit{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;}
    	.tipouti{color:#000066;background:#81F781;font-size:12pt;font-family:Arial;font-weight:bold;}
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ingresos Generados Desde Hospitalizacion y Urgencias</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc163.php Ver. 2017-03-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc163.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wccoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wccof) or !isset($wccdi) or !isset($wccdf) or !isset($wgru) or !isset($wing) or !isset($wanoi) or !isset($wperi1)  or !isset($wperi2) or $wperi1 < 1 or $wperi1 > 12 or $wperi2 < 1 or $wperi2 > 12 or $wperi1 > $wperi2 or !isset($wanof) or !isset($wperf1)  or !isset($wperf2) or $wperf1 < 1 or $wperf1 > 12 or $wperf2 < 1 or $wperf2 > 12 or $wperf1 > $wperf2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INGRESOS GENERADOS DESDE HOSPITALIZACION Y URGENCIAS</td></tr>";
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
			echo "<tr><td bgcolor=#999999 align=center colspan=2>CENTROS DE COSTOS ORIGEN</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Origen Inicial</td>";
			if(isset($wccoi))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' value='".$wccoi."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Origen Final</td>";
			if(isset($wccof))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' value='".$wccof."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Grupo de Unidades</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT ccouni  from ".$empresa."_000161 group by 1 order by 1";
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
			echo "<tr><td bgcolor=#999999 align=center colspan=2>CENTROS DE COSTOS DESTINO</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Destino Inicial</td>";
			if(isset($wccoi))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccdi' value='".$wccdi."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccdi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Destino Final</td>";
			if(isset($wccof))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccdf' value='".$wccdf."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccdf' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Ingresos ?</td>";
			echo "<td bgcolor=#cccccc align=center><input type='RADIO' name=wing value=1>Propios<input type='RADIO' name=wing value=2>Para Terceros<input type='RADIO' name=wing value=3>Ambos</td></tr>";
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
			$CCO=array();
			$query  = "SELECT ccocod, cconom from ".$empresa."_000161 "; 
			$query .= "   where ccoemp = '".$wemp."'";
			$query .= "  order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$CCO[$i][0]=$row[0];
					$CCO[$i][1]=$row[1];
				}
			}
			$numcco=$num;
			
			$IP1=array();
			$query  = "SELECT morcco, sum(morcan) from ".$empresa."_000032 "; 
			$query .= "  where morano = ".$wanoi;
			$query .= "    and moremp = '".$wemp."'";
			$query .= "	   and mormes between ".$wperi1." and ".$wperi2;
			$query .= "	   and mortip = 'P' ";
			$query .= "   Group by 1 ";
			$query .= "   order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$IP1[$i][0]=$row[0];
					$IP1[$i][1]=$row[1];
				}
			}
			$numip1=$num;
			
			$IP2=array();
			$query  = "SELECT morcco, sum(morcan) from ".$empresa."_000032 "; 
			$query .= "  where morano = ".$wanof;
			$query .= "    and moremp = '".$wemp."'";
			$query .= "	   and mormes between ".$wperf1." and ".$wperf2;
			$query .= "	   and mortip = 'P' ";
			$query .= "   Group by 1 ";
			$query .= "   order by 1 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$IP2[$i][0]=$row[0];
					$IP2[$i][1]=$row[1];
				}
			}
			$numip2=$num;
			
			switch ($wing)
			{
				case 1:
					$query  = "select Igicco,Cconom,Igiccd,sum(Igiinp) ";
				break;
				case 2:
					$query  = "select Igicco,Cconom,Igiccd,sum(Igiint) ";
				break;
				case 3:
					$query  = "select Igicco,Cconom,Igiccd,sum(Igiinp + Igiint) ";
				break;
			}
			$query .= "  from ".$empresa."_000149,".$empresa."_000161 ";
			$query .= "   where igiano = ".$wanoi;   
			$query .= "	    and igimes between ".$wperi1." and ".$wperi2;
			$query .= "	    and igicco between '".$wccoi."' and '".$wccof."'"; 
			$query .= "	    and igiccd between '".$wccdi."' and '".$wccdf."'";
			$query .= "	    and Igicco = Ccocod ";
			$query .= "     and Ccoemp = '".$wemp."'";
			if($wgru != "Todos")             
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query .= " group by 1,2,3 ";
			$query .= " order by 1,3 ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			switch ($wing)
			{
				case 1:
					$query  = "select Igicco,Cconom,Igiccd,sum(Igiinp) ";
				break;
				case 2:
					$query  = "select Igicco,Cconom,Igiccd,sum(Igiint) ";
				break;
				case 3:
					$query  = "select Igicco,Cconom,Igiccd,sum(Igiinp + Igiint) ";
				break;
			}
			$query .= "  from ".$empresa."_000149,".$empresa."_000161 ";
			$query .= "   where igiano = ".$wanof;   
			$query .= "	    and igimes between ".$wperf1." and ".$wperf2;
			$query .= "	    and igicco between '".$wccoi."' and '".$wccof."'"; 
			$query .= "	    and igiccd between '".$wccdi."' and '".$wccdf."'";
			$query .= "	    and Igicco = Ccocod ";
			$query .= "     and Ccoemp = '".$wemp."'";
			if($wgru != "Todos")             
				$query = $query."    and Ccouni = '".$wgru."' ";
			$query .= " group by 1,2,3 ";
			$query .= " order by 1,3 ";
			$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num2 = mysql_num_rows($err2);
			echo "<table border=1 class=tipoTABLEGRID>";
			echo "<tr><td colspan=8 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=8 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=8 align=center>INGRESOS GENERADOS DESDE HOSPITALIZACION Y URGENCIAS</td></tr>";
			echo "<tr><td colspan=8 align=center>Datos Iniciales</td></tr>";
			echo "<tr><td colspan=8 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=8 align=center>A&Ntilde;O : ".$wanoi." MES INICIAL : ".$wperi1. " MES FINAL : ".$wperi2."</td></tr>";
			echo "<tr><td colspan=8 align=center>Datos Finales</td></tr>";
			echo "<tr><td colspan=8 align=center>A&Ntilde;O : ".$wanof." MES INICIAL : ".$wperf1. " MES FINAL : ".$wperf2."</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD ORIGEN INICIAL : ".$wccoi. " UNIDAD ORIGEN FINAL : ".$wccof."</td></tr>";
			echo "<tr><td colspan=8 align=center>UNIDAD DESTINO INICIAL : ".$wccdi. " UNIDAD DESTINO FINAL : ".$wccdf."</td></tr>";
			echo "<tr><td colspan=8 align=center>GRUPO DE UNIDADES : ".$wgru."</td></tr>";
			echo "<tr class='tipotit'><td><b>C.C. ORIGEN</b></td><td><b>DESCRIPCION</b></td><td><b>C.C. DESTINO</b></td><td><b>DESCRIPCION</b></td><td><b>INGRESOS : ".$wanoi."/".$wperi1."-".$wperi2."</b></td><td><b>PROMEDIO</b></td><td><b>INGRESOS : ".$wanof."/".$wperf1."-".$wperf2."</b></td><td><b>PROMEDIO</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1++;
				$row1[0]='zzzzzzzz';
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2++;
				$row2[0]='zzzzzzzz';
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				#echo $k1."-".$k2."-".$num1."-".$num2."<br>";
				if($row1[0].$row1[2] == $row2[0].$row2[2])
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]="";
					$wdata[$num][4]=$row1[3];
					$wdata[$num][5]=$row2[3];
					$k1++;
					$k2++;
					if($k1 > $num1)
						$row1[0]="zzzzzzzz";
					else
						$row1 = mysql_fetch_array($err1);
					if($k2 > $num2)
						$row2[0]="zzzzzzzz";
					else
						$row2 = mysql_fetch_array($err2);
				}
				else if($row1[0].$row1[2] < $row2[0].$row2[2])
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]="";
					$wdata[$num][4]=$row1[3];
					$wdata[$num][5]=0;
					$k1++;
					if($k1 > $num1)
						$row1[0]="zzzzzzzz";
					else
						$row1 = mysql_fetch_array($err1);
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]="";
					$wdata[$num][4]=0;
					$wdata[$num][5]=$row2[3];
					$k2++;
					if($k2 > $num2)
						$row2[0]="zzzzzzzz";
					else
						$row2 = mysql_fetch_array($err2);
				}
			}
			for ($i=0;$i<=$num;$i++)
			{
				$pos=bi($CCO,$numcco,$wdata[$i][2]);
				if($pos != -1)
				{
					$wdata[$i][3] = $CCO[$pos][1];
				}
				$wp1 = 0;
				$pos=bi($IP1,$numip1,$wdata[$i][0]);
				if($pos != -1)
				{
					$wp1 = $IP1[$pos][1];
					$wp1 = $wdata[$i][4] / $wp1;
				}
				$wp2 = 0;
				$pos=bi($IP2,$numip2,$wdata[$i][0]);
				if($pos != -1)
				{
					$wp2 = $IP2[$pos][1];
					$wp2 = $wdata[$i][5] / $wp2;
				}
				echo "<tr>";
				echo "<td align=center>".$wdata[$i][0]."</td>";
				echo "<td align=left>".$wdata[$i][1]."</td>";
				echo "<td align=center>".$wdata[$i][2]."</td>";
				echo "<td align=left>".$wdata[$i][3]."</td>";
				echo "<td align=right>".number_format((double)$wdata[$i][4],0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$wp1,0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td>";
				echo "<td align=right>".number_format((double)$wp2,0,'.',',')."</td>";
				echo "</tr>";
    		}
            echo "</tabla>";
		}
	}
?>
</body>
</html>
