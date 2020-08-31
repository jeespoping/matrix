<html>
<head>
  	<title>MATRIX Reporte Para Inventario Fisico</title>
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
	echo "<form name='Fisico' action='Fisico.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wcco) or !isset($wlab))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>REPORTE PARA INVENTARIO FISICO</td></tr>";
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
		echo "<tr><td bgcolor=#cccccc align=center>Laboratorio</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wlab' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>REPORTE PARA INVENTARIO FISICO Ver. 28/12/2005</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=4 face='tahoma'><b>CENTRO DE COSTOS : ".$wcco."</font></b></font></td></tr>";
		$query = "SELECT  Karcod, Artnom, Artuni, Karexi   from ".$empresa."_000007,".$empresa."_000001 ";
		$query .= "  WHERE Karcco='".substr($wcco,0,strpos($wcco,"-"))."' ";
		$query .= "       and  Karcod like '".$wlab."%'";
		$query .= "       and  Karcod=Artcod ";
		$query .= "     ORDER BY  Karcod  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotg=0;
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>ARTICULO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>UNIDAD</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>EXISTENCIAS</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONTEO NRo. 1</b></font><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONTEO NRo. 2</b></font></td></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONTEO NRo. 3</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DIFERENCIA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>UBICACION<BR>ALTERNA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtotg++;
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[3],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>&nbsp &nbsp &nbsp </font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>&nbsp &nbsp &nbsp </font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>&nbsp &nbsp &nbsp </font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>&nbsp &nbsp &nbsp </font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>&nbsp &nbsp &nbsp </font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>&nbsp &nbsp &nbsp </font></td></tr>";
		}
		echo "<tr><td bgcolor=#999999 colspan=10><font face='tahoma' size=2><b>REGISTROS TOTALES : ".number_format((double)$wtotg,0,'.',',')."</b></font></td></tr>";	
		echo"</table>";
	}
}
?>
</body>
</html>