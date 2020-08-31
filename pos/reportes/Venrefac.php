<html>
<head>
  	<title>MATRIX Informe de Ventas Refacturadas Entre Fechas</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Ventas Refacturadas Entre Fechas</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Venrefac.php Ver. 2007-12-19</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Venrefac' action='Venrefac.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper1) or !isset($wper2) or $wper1 > $wper2)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>INFORME DE VENTAS REFACTURADAS ENTRE FECHAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$color="#dddddd";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>INFORME DE VENTAS REFACTURADAS ENTRE FECHAS</font></b></font></td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wper1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wper2."</td></tr>";
		echo "</table><br><br>";	
		$query  = "CREATE TEMPORARY TABLE if not exists tempo1 as  ";
		$query .= "select Rvrvac, Venano as ano,Venmes as mes,Vennum as num, Venfec as fec, Vencco as cco, Vencod as cod, Empnom as nom, Venvto as vto  from ".$empresa."_000068,".$empresa."_000016,".$empresa."_000024 ";
		$query .= "   where ".$empresa."_000068.fecha_data between '".$wper1."' and '".$wper2."'";
		$query .= "     and Rvrvan = vennum ";
		$query .= "     and Vencod = Empcod ";
		$err = mysql_query($query,$conex) or die (mysql_errno()." : ".mysql_error());
		
		//                 0    1    2    3    4    5    6    7      8      9        10      11        12      13      14        15
		$query = "select ano, mes, num, fec, cco, cod, nom, vto, Venano ,Venmes ,Vennum , Venfec , Vencco , Vencod , Empnom , Venvto  from tempo1,".$empresa."_000016,".$empresa."_000024 ";
		$query .= "   where Rvrvac = vennum ";
		$query .= "     and Vencod = Empcod ";
		$query .= "  Order by num ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err) or die(mysql_errno().":".mysql_error());
		$wstotg=0;
		$wstotca=0;
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=7><b>VENTA ORIGINAL</b></td><td align=center bgcolor=#999999 colspan=7><b>VENTA REFACTURADA</b></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><b>AÑO</b></td><td align=center bgcolor=#999999><b>MES</b></td><td align=center bgcolor=#999999 ><b>NUMERO</b></td><td align=center bgcolor=#999999><b>FECHA</b><td align=center bgcolor=#999999><b>SUCURSAL</b></td></td><td align=center bgcolor=#999999><b>REPONSABLE</b></td><td align=center bgcolor=#999999><b>VLR TOTAL</b></td>";
		echo "<td align=center bgcolor=#999999><b>AÑO</b></td><td align=center bgcolor=#999999><b>MES</b></td><td align=center bgcolor=#999999 ><b>NUMERO</b></td><td align=center bgcolor=#999999><b>FECHA</b><td align=center bgcolor=#999999><b>SUCURSAL</b></td></td><td align=center bgcolor=#999999><b>REPONSABLE</b></td><td align=center bgcolor=#999999><b>VLR TOTAL</b></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			$wstotg += $row[7];
			$wstotca ++;
			echo "<tr>";	
			echo "<td bgcolor=".$color." align=center>".$row[0]."</td>";
			echo "<td bgcolor=".$color." align=center>".$row[1]."</td>";
			echo "<td bgcolor=".$color." align=left>".$row[2]."</td>";
			echo "<td bgcolor=".$color." align=center>".$row[3]."</td>";
			echo "<td bgcolor=".$color." align=center>".$row[4]."</td>";
			echo "<td bgcolor=".$color." align=left>".$row[5]."-".$row[6]."</td>";	
			echo "<td bgcolor=".$color." align=right>".number_format((double)$row[7],2,'.',',')."</td>";
			echo "<td bgcolor=".$color." align=center>".$row[8]."</td>";
			echo "<td bgcolor=".$color." align=center>".$row[9]."</td>";
			echo "<td bgcolor=".$color." align=left>".$row[10]."</td>";
			echo "<td bgcolor=".$color." align=center>".$row[11]."</td>";
			echo "<td bgcolor=".$color." align=center>".$row[12]."</td>";
			echo "<td bgcolor=".$color." align=left>".$row[13]."-".$row[14]."</td>";	
			echo "<td bgcolor=".$color." align=right>".number_format((double)$row[15],2,'.',',')."</td>";
			echo "</tr>";
		}
		if($wstotca > 0)
		{
			echo "<tr><td bgcolor=#999999 align=center colspan=7><b>NUMERO TOTAL DE FACTURAS : ".number_format((double)$wstotca,2,'.',',')."</b></td><td bgcolor=#999999 align=center colspan=7> VALOR TOTAL FACTURADO : <b>".number_format((double)$wstotg,2,'.',',')."</b></td></tr>";	
		}
		echo"</table>";
	}
}
?>
</body>
</html>