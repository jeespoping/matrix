<html>
<head>
  	<title>MATRIX Movimiento de Inventarios x Articulo</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Movimiento De Inventarios X Articulos X CC X Fecha X Concepto</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> imovxarsccfeco.php Ver. 2012-02-28</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='imovxarsccfeco' action='imovxarsccfeco.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper1) or !isset($wper2) or !isset($wcon) or !isset($wcco) or !isset($wart1) or !isset($wart2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MOVIMIENTO DE INVENTARIOS X ARTICULOS X CC X FECHA X CONCEPTO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wart1' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wart2' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Tarifa</td><td bgcolor=#cccccc align=center>";
		echo "<select name='wtar'>";
		$query = "SELECT Tarcod, Tardes from ".$empresa."_000025 where Tarest = 'on' order by Tarcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Concepto</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Concod, Condes   from ".$empresa."_000008 order by Concod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcon'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
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
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>MOVIMIENTO DE INVENTARIOS X ARTICULOS X CC X FECHA X CONCEPTO</font></b></font></td></tr>";
		$color="#dddddd";
		$query = "SELECT Artnom from ".$empresa."_000001  where Artcod='".$wart1."'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$wartn1=$wart1."-".$row[0];
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo Inicial: </b>".$wartn1."</td></tr>";	
		$query = "SELECT Artnom from ".$empresa."_000001  where Artcod='".$wart2."'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$wartn2=$wart2."-".$row[0];
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo Final: </b>".$wartn2."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wper1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wper2."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Concepto : </b>".$wcon."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Centro de Costos : </b>".$wcco."</td></tr>";
		
		$query = "DROP TABLE IF EXISTS Tarifas";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$query = "CREATE TEMPORARY TABLE if not exists Tarifas as ";
		$query .= " select SUBSTRING(Mtaart ,1,LOCATE('-', Mtaart)-1) as Mtaart, Mtavan, Mtafec, Mtavac from farpmla_000026 ";
		$query .= " where Mtatar = '".$wtar."' ";
		$query .= "   and Mtacco = '".$wcco."' ";
		$query .= "   and Mtaart between '".$wartn1."' and '".$wartn2."'";
		$query .= " group by 1 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		
		$query = "CREATE UNIQUE INDEX clave1 on Tarifas (Mtaart(12))";
      	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
      	
      	$query = "DROP TABLE IF EXISTS Tarifas1";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$query = "CREATE TEMPORARY TABLE if not exists Tarifas1 as ";
		$query .= " select SUBSTRING(Mtaart ,1,LOCATE('-', Mtaart)-1) as Mtaart, Mtavan, Mtafec, Mtavac from farpmla_000026 ";
		$query .= " where Mtatar = '".$wtar."' ";
		$query .= "   and Mtacco = '".$wcco."' ";
		$query .= "   and Mtaart between '".$wartn1."' and '".$wartn2."'";
		$query .= " group by 1 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		
		$query = "CREATE UNIQUE INDEX clave1 on Tarifas1 (Mtaart(12))";
      	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		
		
		
		$query = "SELECT  Menfec, ".$empresa."_000010.Hora_data, Mendoc, Mdecan, Mdepiv, Mdevto, Mtavan, Mtafec, Mtavac, Mdefve, Mdenlo, Mencco, Menccd, Mendan, Mdeart, Artnom ";
		$query .= "  from ".$empresa."_000010, ".$empresa."_000011, ".$empresa."_000001, Tarifas ";
		$query .= "   where Mencon='".substr($wcon,0,strpos($wcon,"-"))."'";
		$query .= "     and Menfec between '".$wper1."' and '".$wper2."'";
		$query .= "     and Mencco='".substr($wcco,0,strpos($wcco,"-"))."'";
		$query .= "     and Mencon = Mdecon ";
		$query .= "     and Mendoc = Mdedoc ";
		$query .= "     and Mdeart between '".$wart1."' and '".$wart2."'";
		$query .= "     and Mdeart = Artcod ";
     	$query .= "     and Mdeart = Mtaart ";
		$query .= " UNION  ";
		$query .= " SELECT  Menfec, ".$empresa."_000010.Hora_data, Mendoc, Mdecan, Mdepiv, Mdevto, Mtavan, Mtafec, Mtavac, Mdefve, Mdenlo, Mencco, Menccd, Mendan, Mdeart, Artnom ";
		$query .= "  from ".$empresa."_000010, ".$empresa."_000011, ".$empresa."_000001, Tarifas1 ";
		$query .= "   where Mencon='".substr($wcon,0,strpos($wcon,"-"))."'";
		$query .= "     and Menfec between '".$wper1."' and '".$wper2."'";
		$query .= "     and Menccd='".substr($wcco,0,strpos($wcco,"-"))."'";
		$query .= "     and Mencon = Mdecon ";
		$query .= "     and Mendoc = Mdedoc ";
		$query .= "     and Mdeart between '".$wart1."' and '".$wart2."'";
		$query .= "     and Mdeart = Artcod ";
		$query .= "     and Mdeart = Mtaart ";
		$query .= "     ORDER BY 15,1,2 ";	
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$wstotg=0;
		$wstotiva=0;
		$wstotvt=0;
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FECHA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>HORA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOC.</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>ARTICULO</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>% IVA </b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CTO UNITARIO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR IVA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CTO TOTAL</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>TARIFA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR TOTAL</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VENCIMIENTO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>NRo. LOTE</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. ORIGEN</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. DESTINO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>ANEXO</b></font></td></tr>";
		
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			if($row[3] != 0)
				$cosuni=$row[5] / $row[3];
			else
				$cosuni=0;
			$valiva=($row[4] / 100) * $row[5];
			$wstotg += $row[5];
			$wstotiva += $valiva;
			if($row[7] <= date("Y-m-d"))
				$wtar=$row[6];
			else
				$wtar=$row[8];
			$wtot=$row[3] * $wtar;
			$wstotvt += $wtot;
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[14]."-".$row[15]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[3],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[4],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$cosuni,4,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$valiva,2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[5],2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$wtar,2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$wtot,2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[9]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[10]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[11]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[12]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[13]."</font></td></tr>";
			
		}
		if($wstotg > 0)
		{
			echo "<tr><td bgcolor=#999999 align=center colspan=7><font face='tahoma' size=2><b>TOTALES</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wstotiva,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wstotg,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center>&nbsp</td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wstotvt,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=5>&nbsp</td></tr>";	
		}
		echo"</table>";
	}
}
?>
</body>
</html>
