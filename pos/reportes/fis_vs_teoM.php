<html>
<head>
  	<title>MATRIX Comparacion Fisico vs Teorico Multiples Centros de Costos</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Comparacion Fisico vs Teorico Multiples Centros de Costos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>fis_vs_teoM.php Ver. 2013-10-08</b></font></tr></td></table>
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
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='fis_vs_teoM' action='fis_vs_teoM.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wano) or !isset($wmes) or !isset($wcco1) or !isset($wcco2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>COMPARACION FISICO VS TEORICO MULTIPLES CENTROS DE COSTOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003 order by Ccocod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcco1'>";
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
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003 order by Ccocod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcco2'>";
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
		echo "<tr><td bgcolor=#dddddd align=center colspan=2><input type='RADIO' name=wtip value=1 checked> TODOS <input type='RADIO' name=wtip value=2> DIFERENCIAS EN CONTEO 1 Y 2 </td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>COMPARACION FISICO VS TEORICO</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>A&Ntilde;O DE PROCESO : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>MES DE PROCESO : </b>".$wmes."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>CENTRO DE COSTOS INICIAL : </b>".$wcco1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>CENTRO DE COSTOS FINAL : </b>".$wcco2."</td></tr>";
		$fisico=array();
		$query = "SELECT  Fiscco, Fisart, Fiscon, Fiscan   from ".$empresa."_000015 ";
		$query .= " where  Fisano = ".$wano;
		$query .= "     and  Fismes = ".$wmes;
		$query .= "     and  Fiscco between '".substr($wcco1,0,strpos($wcco1,"-"))."' and '".substr($wcco2,0,strpos($wcco2,"-"))."' ";
		$query .= "     and  Fisest = 'on' ";
		$query .= "     ORDER BY  Fiscco,Fisart, Fiscon ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$k=-1;
		if($num > 0)
		{
			$artant="";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if(strtoupper($artant) != strtoupper($row[0].$row[1]))
				{
					$k = $k + 1;
					$fisico[$k][0]=$row[0].$row[1];
					$fisico[$k][1]=-1;
					$fisico[$k][2]=-1;
					$fisico[$k][3]=-1;
					$fisico[$k][5]=0;
					$fisico[$k][6]=$row[0];
					$fisico[$k][7]=$row[1];
					$artant=$row[0].$row[1];
				}
				$fisico[$k][$row[2]]=$row[3];
				$fisico[$k][4]=$row[2];
			}
		}
		
		echo "<table border=0 align=center>";
		
		echo "<tr><td align=center bgcolor=#999999 colspan=4><font face='tahoma' size=2><b>ARTICULOS</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>TEORICO</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=3><font face='tahoma' size=2><b>CONTEOS</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DIFERENCIA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DIFERENCIA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>TIPO</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CENTRO COSTOS</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CODIGO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>UNIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>COSTO<BR>PROMEDIO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>1</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>2</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>3</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>EN CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>EN VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DE DIFERENCIA</b></font></td></tr>";
		$wfal=0;
		$wsob=0;
		$TOT=0;
		//                  0       1        2      3       4       5
		$query = "SELECT  Karcco, Karcod, Karexi, Karpro, Artnom, Artuni    from ".$empresa."_000007, ".$empresa."_000001 ";
		$query .= " where  Karcco between '".substr($wcco1,0,strpos($wcco1,"-"))."' and '".substr($wcco2,0,strpos($wcco2,"-"))."' ";
		$query .= "     and  Karcod = Artcod ";
		$query .= "     ORDER BY  Karcco,Karcod  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$TOT += $num;
			$ww=-1;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				
				$pos=bi($fisico,$k,$row[0].$row[1],0);
				if($pos != -1)
					$fisico[$pos][5]=1;
				
				if($wtip == 1 or ($wtip == 2 and $pos != -1 and $fisico[$pos][1] != $fisico[$pos][2]))
				{
					$ww++;
					if($ww % 2 == 0)
						$color="#9999FF";
					else
						$color="#ffffff";
					echo "<tr>";
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[4]."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[5]."</font></td>";	
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[2],2,'.',',')."</font></td>";	
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[3],2,'.',',')."</font></td>";
					if($pos != -1)
					{
						$fisico[$pos][5]=1;
						if($fisico[$pos][1] != -1)
							echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$pos][1],2,'.',',')."</font></td>";
						else
							echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
						if($fisico[$pos][2] != -1)
							echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$pos][2],2,'.',',')."</font></td>";
						else
							echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
						if($fisico[$pos][3] != -1)
							echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$pos][3],2,'.',',')."</font></td>";
						else
							echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
						$dif=$fisico[$pos][$fisico[$pos][4]] - $row[2];
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$dif,2,'.',',')."</font></td>";
						if($dif < 0)
							$v= (-1) * $dif * $row[3];
						else
							$v=$dif * $row[3];
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$v,2,'.',',')."</font></td>";
						if($dif < 0)
						{
							$wfal=$wfal + ((-1) * $dif * $row[3]);
							echo "<td bgcolor=#FFCC66 align=center><font face='tahoma' size=2>FALTANTE</font></td>";
						}
						elseif($dif > 0)
								{
									$wsob=$wsob + ($dif * $row[3]);
									echo "<td bgcolor=#99CCFF align=center><font face='tahoma' size=2>SOBRANTE</font></td>";
								}
								else
									echo "<td bgcolor=#00FF00 align=center><font face='tahoma' size=2>IGUAL</font></td></tr>";
					}
					else
					{
						echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
						echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
						echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
						$dif=0 - $row[2];
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$dif,2,'.',',')."</font></td>";
						if($dif < 0)
							$v= (-1) * $dif * $row[3];
						else
							$v=$dif * $row[3];
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$v,2,'.',',')."</font></td>";
						if($dif < 0)
						{
							$wfal=$wfal + ((-1) * $dif * $row[3]);
							echo "<td bgcolor=#FFCC66 align=center><font face='tahoma' size=2>FALTANTE</font></td>";
						}
						elseif($dif > 0)
								{
									$wsob=$wsob + ($dif * $row[3]);
									echo "<td bgcolor=#99CCFF align=center><font face='tahoma' size=2>SOBRANTE</font></td>";
								}
								else
									echo "<td bgcolor=#00FF00 align=center><font face='tahoma' size=2>IGUAL</font></td></tr>";
					}
				}
			}
		}
		$wsw=0;
		$ww=-1;
		for ($i=0;$i<=$k;$i++)
		{
			if($fisico[$i][5] == 0)
			{
				$TOT += 1;
				if($wsw == 0)
				{
					echo "<tr><td bgcolor=#FFFF00 colspan=11 align=center><b>ARTICULOS QUE NO APARECEN EN EL TEORICO</b></td></tr>";
					$wsw=1;
				}
				$query = "SELECT  Artnom, Artuni    from  ".$empresa."_000001 ";
				$query .= " where  Artcod = '".$fisico[$i][7]."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
					$row = mysql_fetch_array($err);
				else
				{
					$row[0]="NO ESPECIFICO";
					$row[1]="NO ESPECIFICO";
				}
				
				if($wtip == 1 or ($wtip == 2 and $fisico[$i][1] != $fisico[$i][2]))
				{
					$ww++;
					if($ww % 2 == 0)
						$color="#9999FF";
					else
						$color="#ffffff";
					echo "<tr>";
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$fisico[$i][6]."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$fisico[$i][7]."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
					echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>0.0</font></td>";	
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>0.0</font></td>";
					if($fisico[$i][1] != -1)
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$i][1],2,'.',',')."</font></td>";
					else
						echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
					if($fisico[$i][2] != -1)
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$i][2],2,'.',',')."</font></td>";
					else
						echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
					if($fisico[$i][3] != -1)
						echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$fisico[$i][3],2,'.',',')."</font></td>";
					else
						echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>X</font></td>";
					$dif=$fisico[$i][$fisico[$i][4]] - 0;
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$dif,2,'.',',')."</font></td>";
					$v=0;
					echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$v,2,'.',',')."</font></td>";
					if($dif < 0)
						echo "<td bgcolor=#FFCC66 align=center><font face='tahoma' size=2>FALTANTE</font></td>";
					elseif($dif > 0)
								echo "<td bgcolor=#99CCFF align=center><font face='tahoma' size=2>SOBRANTE</font></td>";
							else
								echo "<td bgcolor=#00FF00 align=center><font face='tahoma' size=2>IGUAL</font></td></tr>";
				}
			}
		}
		echo "<tr><td bgcolor=#999999 colspan=12><b>NUMERO TOTAL DE ARTICULOS : ".number_format((double)$TOT,0,'.',',')."</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=12><b>TOTAL SOBRANTE : ".number_format((double)$wsob,2,'.',',')."</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=12><b>TOTAL FALTANTE : ".number_format((double)$wfal,2,'.',',')."</b></td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>
