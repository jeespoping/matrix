<html>
<head>
  	<title>MATRIX Comparacion Fisico vs Teorico</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparacion Fisico vs Teorico</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>fis_vs_teo.php Ver. 2007-04-27</b></font></tr></td></table>
</center> 
<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
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
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='fis_vs_teo' action='fis_vs_teo.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wano) or !isset($wmes) or !isset($wcco))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>COMPARACION FISICO VS TEORICO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes  from ".$empresa."_000003 order by Ccocod";
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
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>COMPARACION FISICO VS TEORICO</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>AÑO DE PROCESO : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>MES DE PROCESO : </b>".$wmes."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>CENTRO DE COSTOS : </b>".$wcco."</td></tr>";
		$fisico=array();
		$query = "SELECT  Fisart, Fiscon, Fiscan, Artdes, Artuni    from ".$empresa."_000002,".$empresa."_000001 ";
		$query .= " where  Fisano = ".$wano;
		$query .= "     and  Fismes = ".$wmes;
		$query .= "     and  Fiscco = '".substr($wcco,0,strpos($wcco,"-"))."'";
		$query .= "     and  Fisest = 'on' ";
		$query .= "     and  Fisart = Artcod ";
		$query .= "     ORDER BY  Fisart, Fiscon ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$k=-1;
		if($num > 0)
		{
			$artant="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if(strtoupper($artant) != strtoupper($row[0]))
				{
					$k = $k + 1;
					$fisico[$k][0]=$row[0];
					$fisico[$k][1]=-1;
					$fisico[$k][2]=-1;
					$fisico[$k][3]=-1;
					$artant=$row[0];
					$fisico[$k][4]="";
				$fisico[$k][5]="";
				}
				$fisico[$k][$row[1]]=$row[2];
				$fisico[$k][4]=$row[3];
				$fisico[$k][5]=$row[4];
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=3><font face='tahoma' size=2><b>ARTICULOS</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=3><font face='tahoma' size=2><b>CONTEOS</b></font></td></tr>";
		
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CODIGO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>UNIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>1</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>2</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>3</b></font></td></tr>";
		if($k >= 0)
		{
			for ($i=0;$i<=$k;$i++)
			{
				if($i % 2 == 0)
					$color="#9999FF";
				else
					$color="#ffffff";
				echo "<tr>";
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$fisico[$i][0]."</font></td>";	
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$fisico[$i][4]."</font></td>";	
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$fisico[$i][5]."</font></td>";	
				if($fisico[$i][1] != -1)
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$i][1],2,'.',',')."</font></td>";
				else
					echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
				if($fisico[$i][2] != -1)
					if($fisico[$i][1] != -1 and $fisico[$i][1] != $fisico[$i][2])
						echo "<td bgcolor=#CC0000 align=right><font face='tahoma' size=2>".number_format((double)$fisico[$i][2],2,'.',',')."</font></td>";
					else
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$i][2],2,'.',',')."</font></td>";
				else
					echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
				if($fisico[$i][3] != -1)
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$i][3],2,'.',',')."</font></td>";
				else
					echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
			}
		}
		echo "<tr><td bgcolor=#999999 colspan=11><b>NUMERO TOTAL DE ARTICULOS : ".number_format((double)$k+1,0,'.',',')."</b></td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>