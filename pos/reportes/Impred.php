<html>
<head>
  	<title>MATRIX Movimiento de Redenciones Ver. .2007-11-07</title>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000000">
<?php
include_once("conex.php");

/**********************************************************************************************************************  
	   PROGRAMA : impred.php
	   Fecha de Liberación : 2006-11-29
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-11-07
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite visualizar el movimiento de puntos redimidos
	   por un usuario.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   	.2006-11-29
	   		Release de Versión. Version 2006-11-29
	   		
	   	.2007-11-07
	   		Se modifico la entrada de datos para no dejar pasar datos en nulo.
	   
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='impred' action='Impred.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if (!isset($wcon) or $wcon == "")
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=5><font size=2>Powered by : MATRIX </font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>MOVIMIENTO DE REDENCION DE PUNTOS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. .2007-11-07</font></b></font></td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Consecutivo : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wcon' size=10 maxlength=10 ></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Ok'></td></tr>"; 
		echo "</table>";  
	}
	else
	{
		$query = "SELECT Reddoc from ".$empresa."_000079 ";
		$query .="   where Redcon = ".$wcon;
		$query .=" group by Reddoc ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
			$row = mysql_fetch_array($err);
		else
			$row[0]="www";
		$query = "SELECT Clidoc, Clinom  from ".$empresa."_000041  where Clidoc='".$row[0]."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			$wclit=$row1[0]."-".$row1[1];
		}
		else
		{
			$query =  " SELECT Meddoc, Mednom  FROM ".$empresa."_000051 where Meddoc= '".$row[0]."' ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wclit=$row1[0]."-".$row1[1];
			}
			else
				$wclit="NO ESPECIFICO";
		}
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font color=#000000 size=6><b>MOVIMIENTO DE REDENCION DE PUNTOS</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font color=#000000 size=3><b>CLIENTE : ".$wclit."</font></b></font></td></tr>";
		echo "</table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font color=#000000><b>NRO<br>ITEM</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>DOCUMENTO</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>CONCEPTO</b></font></td><td align=center bgcolor=#999999 ><font color=#000000><b>ARTICULO</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>DESCRIPCION</b></font><td align=center bgcolor=#999999><font color=#000000><b>CANTIDAD</b></font></td></td><td align=center bgcolor=#999999><font color=#000000><b>PUNTOS</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>VLR UNITARIO</b></font></td><td align=center bgcolor=#999999><font color=#000000><b>VALOR TOTAL</b></font></td></tr>";
		$query = "SELECT Redcon, Redcto, Reddoc, Redart, Redcan, Redpun, Redcos, Redtar, Redest, Artnom   from ".$empresa."_000079,".$empresa."_000001 ";
		$query .="   where Redcon = ".$wcon;
		$query .="     and Redart = Artcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotp=0;
		$wtotv=0;
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($i % 2 == 0)
					$color="#ffffff";
				else
					$color="#dddddd";
				
				$w=$i+1;
				echo "<tr><td bgcolor=".$color." align=center>".$w."</td>";	
				echo "<td bgcolor=".$color.">".$row[0]."</td>";	
				echo "<td bgcolor=".$color.">".$row[1]."</td>";	
				echo "<td bgcolor=".$color.">".$row[3]."</td>";	
				echo "<td bgcolor=".$color.">".$row[9]."</td>";	
				echo "<td bgcolor=".$color." align=right>".number_format((double)$row[4],0,'.',',')."</td>";	
				echo "<td bgcolor=".$color." align=right>".number_format((double)$row[5],2,'.',',')."</td>";	
				echo "<td bgcolor=".$color." align=right>$".number_format((double)$row[7],2,'.',',')."</td>";	
				$wvtot=$row[4]*$row[7];
				$wtotp += $row[5];
				$wtotv += $wvtot;
				echo "<td bgcolor=".$color." align=right>$".number_format((double)$wvtot,2,'.',',')."</td></tr>";
			}
			echo "<tr><td bgcolor=#999999  align=center colspan=6><font color=#000000><b>TOTALES</b></font></td><td bgcolor=#999999 align=right><font color=#000000><b>".number_format((double)$wtotp,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center>&nbsp</td><td bgcolor=#999999 align=right><font color=#000000><b>$".number_format((double)$wtotv,2,'.',',')."</b></font></td></tr>";	
		}
		echo"</table>";
	}
}
?>
</body>
</html>
