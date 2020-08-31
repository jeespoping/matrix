<html>
<head>
  	<title>MATRIX - Kardex FARMASTORE</title>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	
	$key = substr($user,2,strlen($user));
	echo "<form name='Impkardex' action='Impkardex.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wart))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ESTADO DEL KARDEX X ARTICULO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wart' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>ESTADO DEL KARDEX X ARTICULO</font></b></font></td></tr>";
		$color="#dddddd";
		if(isset($wart) and $wart != "")
		{
			$query = "SELECT Artnom from ".$empresa."_000001  where Artcod='".$wart."'";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo : </b>".$wart."-".$row[0]."</td></tr>";	
		}
		else
			echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo : </b>TODOS</td></tr>";	
		$query = "SELECT Karcco, Ccodes, Karexi, Karpro, Karvuc, Karfuc, Karmax, Karmin, Karcod  from ".$empresa."_000007, ".$empresa."_000003  ";
		if(isset($wart) and $wart != "")
			$query .= " where Karcod='".$wart."'";
		else
			$query .= " where   Karcod between '0' and 'z' ";
		$query .= "     and  Karcco=Ccocod";
		$query .= "     ORDER BY Karcod, Karcco ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$wtotg=0;
		$wtotiva=0;
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C. COSTOS</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>ARTICULO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>EXISTENCIAS</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>COSTO PROMEDIO</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>VLR. ULT. COMPRA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FECHA ULT. COMPRA</b></font><td align=center bgcolor=#999999><font face='tahoma' size=2><b>MAXIMO</b></font></td></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>MINIMO</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			$row = mysql_fetch_array($err);
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."-".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[8]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[2],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[3],4,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[4],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[5]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[6],2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[7],2,'.',',')."</font></td></tr>";
		}
		echo"</table>";
	}
}
?>
</body>
</html>