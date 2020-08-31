<html>
<head>
  	<title>MATRIX Impresion del Kardex</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Impresion del Kardex</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>ikardex.php Ver. 1.00</b></font></tr></td></table>
</center> 
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='ikardex' action='ikardex.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wano) or !isset($wmes) or !isset($wcco) or (strtoupper ($wind) != "S" and strtoupper ($wind) != "N"))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>IMPRESION DEL KARDEX</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<input type='HIDDEN' name= 'wcco' value='1051'>";
		echo "<input type='HIDDEN' name= 'wind' value='N'>";
		/*echo "<tr><td bgcolor=#cccccc align=center>Diferencias (Valor / Cantidad) Mayores  a Cero ? (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wind' size=1 maxlength=1></td></tr>";*/
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wind=strtoupper ($wind);
		if($wmes == 1)
		{
			$wmesa = 12;
			$wanoa = $wano - 1;
		}
		else
		{
			$wmesa = $wmes -1;
			$wanoa = $wano;
		}
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>IMPRESION DEL KARDEX</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>AÑO DE PROCESO : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>MES DE PROCESO : </b>".$wmes."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>CENTRO DE COSTOS : </b>".$wcco."</td></tr>";
		$query = "SELECT Kxmcod, Kxmdes, Kxmuni, Kxmgru, Kxmcsi, Kxmvsi, Kxmcen, Kxmven, Kxmcsa, Kxmvsa, Kxmcsf, Kxmvsf, Kxmcdi, Kxmvdi, Kxmvro, Kxmdro   from ".$empresa."_000011 ";
		$query .= " where  Kxmano = ".$wano;
		$query .= "     and  Kxmmes = ".$wmes;
		$query .= "     and  Kxmcco = '".$wcco."'";
		if($wind == "S")
			$query .= "     and  Kxmind = 'off' ";
		$query .= "     ORDER BY  Kxmgru,Kxmcod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=3><font face='tahoma' size=2><b>ARTICULOS</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>SALDO INICIAL</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>+ ENTRADAS</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>- SALIDAS</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>SALDO FINAL</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>DIFERENCIAS</b></font></td>";
		echo "<td align=center bgcolor=#999999 colspan=2><font face='tahoma' size=2><b>ROTACION</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CODIGO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>UNIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>NRo DE VECES</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DIAS</b></font></td></tr>";
		$wtot=array();
		$wlinant="";
		for ($i=0;$i<10;$i++)
		$wtot[$i]=0;
		for ($i=0;$i<$num;$i++)
		{
			$wsalantc=0;
			$wsalantv=0;
			$row = mysql_fetch_array($err);
			/*if($row[3] != $wlinant)
			{
				$query = "SELECT Sgrcod, Sgrdes  from ".$empresa."_000005 ";
				$query .= " where  Sgrgru =  '".substr($row[3],0,strpos($row[3],"-"))."'";
				$query .= "     and   Sgrcod =  '".substr($row[3],strpos($row[3],"-")+1)."'";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				echo "<tr><td bgcolor=#FFFF00 colspan=15><b>SUBLINEA DE PRODUCTO : ".$row1[0]."-".$row1[1]."</b></td></tr>";
				$wlinant=$row[3];
			 }*/
			 echo "<input type='HIDDEN' name= 'wlinant' value='".$wlinant."'>";
			$wtot[0] +=$row[4];
			$wtot[1] +=$row[5];
			$wtot[2] +=$row[6];
			$wtot[3] +=$row[7];
			$wtot[4] +=$row[8];
			$wtot[5] +=$row[9];
			$wtot[6] +=$row[10];
			$wtot[7] +=$row[11];
			$wtot[8] +=$row[12];
			$wtot[9] +=$row[13];
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			if($row[4] < 0)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[4],2,'.',',')."</font></td>";	
			if($row[5] < 0)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[5],2,'.',',')."</font></td>";	
			if($row[6] < 0)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[6],2,'.',',')."</font></td>";	
			if($row[7] < 0)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[7],2,'.',',')."</font></td>";	
			if($row[8] < 0)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[8],2,'.',',')."</font></td>";	
			if($row[9] < 0)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[9],2,'.',',')."</font></td>";	
			if($row[10] < 0)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[10],2,'.',',')."</font></td>";	
			if($row[11] < 0)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[11],2,'.',',')."</font></td>";	
			if($row[12] >= 1 or $row[12] <= -1)
				$colord="#FF0000";
			else
				$colord=$color;
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[12],4,'.',',')."</font></td>";
			if($row[13] >= 1 or $row[13] <= -1)
				$colord="#FF0000";
			else
				$colord=$color;	
			echo "<td bgcolor=".$colord." align=right><font face='tahoma' size=2>".number_format((double)$row[13],4,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[14],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[15],2,'.',',')."</font></td>";	
			echo "</tr>";
		}
		echo "<tr>";
		echo "<td bgcolor=#cccccc colspan=3><font face='tahoma' size=2 ><b>TOTALES</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[0],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[1],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[2],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[3],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[4],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[5],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[6],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[7],2,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[8],4,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right><font face='tahoma' size=2><b>".number_format((double)$wtot[9],4,'.',',')."</b></font></td>";	
		echo "<td bgcolor=#cccccc align=right colspan=2>&nbsp </td>";	
		echo "</tr>";
		echo "<tr><td bgcolor=#999999 colspan=15><b>NUMERO TOTAL DE ARTICULOS : ".$num."</b></td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>
