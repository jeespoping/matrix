<html>
<head>
  	<title>MATRIX Analisis de Rentabilidad x Centro de Costos</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Control' action='Control.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wcco) or !isset($wtar))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ANALISIS DE RENTABILIDAD X CENTRO DE COSTOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003 order by Ccocod";
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
		echo "<tr><td bgcolor=#cccccc align=center>Tarifa</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Tarcod, Tardes     from ".$empresa."_000025 order by Tarcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wtar'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wtar == $row[0]."-".$row[1])
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
		$tipo=array();
		for ($i=0;$i<5;$i++)
			$tipo[$i][1]=0;
		$tipo[0][0]="ARTICULOS EN RANGO";
		$tipo[1][0]="ARTICULOS CON MARGEN MENOR O IGUAL AL 5%";
		$tipo[2][0]="ARTICULOS CON MARGEN MAYOR AL 5% Y MENOR AL 12%";
		$tipo[3][0]="ARTICULOS CON MARGEN MAYOR AL 25% Y MENOR O IGUAL AL 60%";
		$tipo[4][0]="ARTICULOS CON MARGEN MAYOR AL 60%";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>ANALISIS DE RENTABILIDAD X CENTRO DE COSTOS Ver. 2008-06-11</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=4 face='tahoma'><b>CENTRO DE COSTOS : ".$wcco."</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=4 face='tahoma'><b>TARIFA : ".$wtar."</font></b></font></td></tr>";
		$query = "SELECT  Karcod, Artnom, Artuni, Artest, Artiva, Karexi, Karpro, Karvuc   from ".$empresa."_000007,".$empresa."_000001 ";
		$query .= "  WHERE Karcco='".substr($wcco,0,strpos($wcco,"-"))."' ";
		$query .= "       and  Karcod=Artcod ";
		$query .= "     ORDER BY  Karcod  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotg=0;
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>ARTICULO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>UNIDAD</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>EXISTENCIAS</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>COSTO PROMEDIO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR ULT. COMPRA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>TARIFA SIN <BR> IVA</b></font></td></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>MARGEN</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>PRECIO <BR> VENTA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>% IVA</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$query = "SELECT Mtavan, Mtafec, Mtavac  from ".$empresa."_000026  ";
			$query .= " where  Mtatar = '".$wtar."' ";
			$query .= "     and   Mtacco = '".$wcco."' ";
			$query .= "     and   Mtaart LIKE '".$row[0]."%' ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				if(date("Y-m-d") <= $row1[1])
				{
					$tarif=$row1[0];
					$pven=$row1[0];
				}
				else
				{
					$tarif=$row1[2];
					$pven=$row1[2];
				}
				if($row[4] !=0)
				{
					$tarif=$tarif / (1 + ($row[4] / 100));
				}
			}
			else
			{
				$tarif=0;
				$pven=0;
			}
			if($tarif != 0)
				$marg=(1 - ($row[6] / $tarif)) * 100;
			else
				$marg=0;
			$wtotg++;
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[5],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[6],2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[7],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$tarif,2,'.',',')."</font></td>";	
			if($marg < 12 and $marg > 5)
			{
				$color1="#FF99FF";
				$tipo[2][1] += 1;
			}
			elseif($marg > 25 and $marg <= 60)
					{
						$color1="#fffff00";
						$tipo[3][1] += 1;
					}
					elseif($marg > 60)
							{
								$color1="#00FF00";
								$tipo[4][1] += 1;
							}
							elseif($marg <= 5)
									{
										$color1="#ff0000";
										$tipo[1][1] += 1;
									}
									else
									{
										$color1=$color;
										$tipo[0][1] += 1;
									}
			echo "<td bgcolor=".$color1." align=right><font face='tahoma' size=2>".number_format((double)$marg,2,'.',',')."%</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".$pven."</font></td>";	
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".number_format((double)$row[4],2,'.',',')."%</font></td></tr>";	
		}
		for ($i=0;$i<5;$i++)
		{
			$por=0;
			if($wtotg>0)
				$por=$tipo[$i][1] / $wtotg * 100;
			echo "<tr><td bgcolor=#dddddd colspan=10><font face='tahoma' size=2><b>".$tipo[$i][0]." = ".number_format((double)$tipo[$i][1],0,'.',',')."&nbsp&nbsp&nbspPORCENTAJE = ".number_format((double)$por,2,'.',',')."%</b></font></td></tr>";
		}
		echo "<tr><td bgcolor=#999999 colspan=10><font face='tahoma' size=2><b>REGISTROS TOTALES : ".number_format((double)$wtotg,0,'.',',')."</b></font></td></tr>";	
		echo"</table>";
	}
}
?>
</body>
</html>
