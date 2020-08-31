<html>
<head>
  	<title>MATRIX Comparativo de Ventas x Periodo vs Existencias</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparativo de Ventas x Periodo vs Existencias</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>cvenexi.php Ver. 2006-12-29</b></font></tr></td></table>
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
	echo "<form name='cvenexi' action='cvenexi.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfec1) or !isset($wfec2) or !isset($wcco))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>COMPARATIVO DE VENTAS X PERIODO VS EXISTENCIAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfec1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfec2' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003 where Ccovef > 0 order by Ccocod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcco'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wcco == $row[0]."-".$row[1])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$cco=array();
		$query = "SELECT Ccocod from ".$empresa."_000003 order by Ccocod ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$tot0=$num;
		
		if ($num>0)
		{
			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$cco[$i] = $row[0];
			}
		}
		$art=array();
		$query = "select Artcod, Artnom  from ".$empresa."_000001 order by Artcod ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$tot1=$num;
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$art[$i][0] = $row[0];
				$art[$i][1] = $row[1];
			}
		}
		$kar=array();
		$query  = "select Karcod,Karcco,sum(Karexi) from ".$empresa."_000007 ";
		$query .= "group by 1,2 ";
		$query .= "order by 1,2 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$artant="";
		$k=-1;
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($artant != $row[0])
				{
					$k = $k + 1;
					$artant = $row[0];
					$kar[$k][0]=$row[0];
					for ($j=1;$j<=$tot0;$j++)
						$kar[$k][$j]=0;
				}
				for ($j=1;$j<=$tot0;$j++)
					if($cco[$j] == $row[1])
						$kar[$k][$j] = $row[2];
			}
		}
		$fil=$tot0 + 4;
		$fil1=$tot0 + 1;
		echo "<table border=0 align=center>";
		echo "<tr><td colspan=".$fil." align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td colspan=".$fil." align=center bgcolor=#cccccc><font size=6 face='tahoma'><b>Comparativo de Ventas x Periodo vs Existencias</font></b></font></td></tr>";
		echo "<tr><td colspan=".$fil." bgcolor=#cccccc align=center><font face='tahoma'><b>Desde </b>".$wfec1." Hasta ".$wfec2."</td></tr>";
		echo "<tr><td colspan=".$fil." bgcolor=#cccccc align=center><font face='tahoma'><b>Punto de Venta : </b>".$wcco."</td></tr>";
		echo "<tr><td bgcolor=#999999 rowspan=2 align=center><b>CODIGO</b></td><td bgcolor=#999999 rowspan=2 align=center><b>DESCRIPCION</b></td><td bgcolor=#999999 rowspan=2 align=center><b>VENTAS</b></td><td bgcolor=#999999 colspan=".$fil1." align=center><b>EXISTENCIAS ACTUALES</b></td></tr>";
		echo "<tr>";
		for ($i=1;$i<=$tot0;$i++)
			echo "<td bgcolor=#999999 align=center><b>".$cco[$i]."</b></td>";
		echo "<td bgcolor=#999999 align=center><b>TOTAL</b></td></tr>";
		$query  = "select ".$empresa."_000011.Mdeart,sum(".$empresa."_000011.Mdecan) from ".$empresa."_000011,".$empresa."_000010 ";
		$query .= "where ".$empresa."_000011.mdecon = '802' "; 
		$query .= " and ".$empresa."_000011.fecha_data between '".$wfec1."' and '".$wfec2."' ";
		$query .= " and mdecon=mencon ";
		$query .= " and mdedoc=mendoc ";
		$query .= " and mencco = '".substr($wcco,0,strpos($wcco,"-"))."' ";
		$query .= " group by ".$empresa."_000011.Mdeart ";
		$query .= " order by ".$empresa."_000011.Mdeart ";
		$err = mysql_query($query,$conex);
		//echo $query;
		$num = mysql_num_rows($err);
		$w=0;
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color="#9999FF";
				else
					$color="#ffffff";
				$row = mysql_fetch_array($err);
				$pos1=bi($kar,$k,$row[0],0);
				if($pos1 != -1)
				{
					$w++;
					$sum=0;
					$pos2=bi($art,$tot1,$row[0],0);
					echo "<tr><td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color.">".$art[$pos2][1]."</td><td bgcolor=".$color." align=right>".$row[1]."</td>";
					for ($j=1;$j<=$tot0;$j++)
					{
						$sum += $kar[$pos1][$j];
						echo "<td bgcolor=".$color." align=right>".$kar[$pos1][$j]."</td>";
					}
					echo "<td bgcolor=".$color." align=right>".$sum."</td></tr>";
				}
			}
		}
		echo "<tr><td colspan=".$fil." bgcolor=#999999><b>ARTICULOS CON VENTAS : ".$num."</b></td></tr>";
		echo "<tr><td colspan=".$fil." bgcolor=#999999><b>ARTICULOS CON ANALIZADOS : ".$w."</b></td></tr>";
		echo "</table>";
	}
}
?>
</body>
</html>