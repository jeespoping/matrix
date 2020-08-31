<html>
<head>
  	<title>MATRIX Comparacion Fisico vs Teorico General</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Maximos y Minimos General</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>MaxMinG.php Ver. 2009-05-29</b></font></td></tr></table>
</center> 
<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
				return $lm;
			elseif(strtoupper($k) < strtoupper($d[$lm][$i]))
						$ls=$lm;
					else
						$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='MaxMinG' action='MaxMinG.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wdias) or !isset($war1) or !isset($war2) or !isset($wcco))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>CALCULO DE MAXIMOS Y MINIMOS GENERAL</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Meses a Analizar</td><td bgcolor=#cccccc align=center>";
		echo "<select name='wmes'>";
		for ($i=1;$i<13;$i++)
			echo "<option>".$i."</option>";
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Dias de Reposicion</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wdias' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='war1' size=8 maxlength=8></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='war2' size=8 maxlength=8></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Promedio</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco' size=4 maxlength=4></td></tr>";
		echo "<td bgcolor=#cccccc align=center colspan=2>TODOS LOS ARTICULOS : <br><input type='checkbox' name='wtodos'></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>CALCULO DE MAXIMOS Y MINIMOS GENERAL</font><font size=2> <b>Ver. 2008-05-13</b></font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Meses a Analizar : </b>".$wmes."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Dias de Reposicion : </b>".$wdias."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo Inicial : </b>".$war1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo Final : </b>".$war2."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Centro de Costos : </b>".$wcco."</td></tr></table>";
		$arti=array();
		$query = "SELECT  Artcod,  Artnom, Artuni  from ".$empresa."_000001 ";
		$query .= "    ORDER BY  Artcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$arti[$i][0]=$row[0];
				$arti[$i][1]=$row[1];
				$arti[$i][2]=$row[2];
			}
		}
		$k1=$num;
		$pro1=array();
		$query = "SELECT  Karcod, Karpro from ".$empresa."_000007 ";
		$query .= " WHERE  Karcco = '".$wcco."'";
		if(!isset($wtodos))
			$query .= "   and  Karcod between '".$war1."' and '".$war2."' ";
		$query .= "    ORDER BY  Karcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$pro1[$i][0]=$row[0];
				$pro1[$i][1]=$row[1];
			}
		}
		$kp=$num;
		$kardex=array();
		$query = "SELECT  Karcod, count(*), sum(Karpro), sum(Karexi) from ".$empresa."_000007 ";
		$query .= "    GROUP BY  Karcod ";
		$query .= "    ORDER BY  Karcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$kardex[$i][0]=$row[0];
				$kardex[$i][1]=$row[1];
				$kardex[$i][2]=$row[2];
				$kardex[$i][3]=$row[3];
			}
		}
		$k=$num;
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CODIGO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>UNIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>EXISTENCIAS<BR>TOTALES</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONSUMO<BR>TOTAL</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONSUMO<BR>PROMEDIO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>EXISTENCIAS<BR>MINIMAS</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>EXISTENCIAS<BR>MAXIMAS</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>PUNTO<BR>REPOSICION</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD<BR>A PEDIR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR<BR>DEL PEDIDO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD<BR>SOBRE_STOCK</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR<BR>SOBRE_STOCK</b></font></td></tr>";
		$wtot=0;
		$wvap=0;
		$wvst=0;
		$wfec2=date("Y-m-d");
		$wmest=(integer)substr($wfec2,5,2) - $wmes;
		$wanot=(integer)substr($wfec2,0,4);
		if($wmest < 0)
		{
			$wmest=12 + $wmest;
			$wanot=$wanot - 1;
		}
		if($wmest < 10)
			$wfec1=$wanot."-0".$wmest."-".substr($wfec2,8,2);
		else
			$wfec1=$wanot."-".$wmest."-".substr($wfec2,8,2);
		$query = "SELECT  Mdeart, sum(Mdecan) from ".$empresa."_000010,".$empresa."_000011 ";
		$query .= " where Mencon in ('105','802') ";
		$query .= " and Menfec between '".$wfec1."' and '".$wfec2."'";
		$query .= " and Mencco not in ('3050')";
		$query .= " and Mencon  = Mdecon ";
		$query .= " and Mendoc  = Mdedoc ";
		if(isset($wtodos))
			$query .= " and Mdeart between '0' and 'z'";
		else
			$query .= " and Mdeart between '".$war1."' and '".$war2."'";
		$query .= " group by Mdeart ";
		$query .= " order by Mdeart ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$wvap=0;
			$wvst=0;
			for ($i=0;$i<$num;$i++)
			{
				$wtot++;
				$row = mysql_fetch_array($err);
				if($i % 2 == 0)
					$color="#9999FF";
				else
					$color="#ffffff";
				$pos=bi($kardex, $k, $row[0], 0);
				if($pos != -1)
				{
					$exi=$kardex[$pos][3];
					$pos=bi($pro1, $kp, $row[0], 0);
					if($pos != -1)
						$pro=$pro1[$pos][1];
					else
						$pro=0;
				}
				else
				{
					$exi=0;
					$pro=0;
				}
				$cot=$row[1];
				$cop=$cot / $wmes;
				$exm=$cop * ($wdias / 30);
				$exM=$cop + $exm;
				//$ptr=$exm * 2;
				$ptr=($exm + $exM)/ 2;
				$cap=0;
				$sst=0;
				if(($exM - $exi) > 0)
				{
					//$cap=$exM - $exi;
					$cap=$ptr - $exi;
					$cap=round($cap);
				}
				else
				{
					$sst=($exM - $exi) * (-1);
					$sst=round($sst);
				}
				$vap=$cap * $pro;
				$vst=$sst * $pro;
				$wvap += $vap;
				$wvst += $vst;
				$pos=bi($arti, $k1, $row[0], 0);
				echo "<tr>";
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
				if($pos != -1)
				{
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$arti[$pos][1]."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$arti[$pos][2]."</font></td>";
				}
				else
				{
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>NO ESPECIFICO</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>NO ESPECIFICO</font></td>";
				}
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$exi,2,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$cot,2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$cop,2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$exm,2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$exM,2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$ptr,2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$cap,0,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$vap,2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$sst,0,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$vst,2,'.',',')."</font></td>";
				echo "</tr>";
			}
		}
		echo "<tr><td bgcolor=#999999 colspan=14><b>NUMERO TOTAL DE ARTICULOS : ".number_format((double)$wtot,0,'.',',')."</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=14><b>VALOR TOTAL DE LA COMPRA : ".number_format((double)$wvap,2,'.',',')."</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=14><b>VALOR TOTAL DEL SOBRE-STOCK : ".number_format((double)$wvst,2,'.',',')."</b></td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>