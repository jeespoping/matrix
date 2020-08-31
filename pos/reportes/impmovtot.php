<html>
<head>
  	<title>MATRIX Movimiento Consolidado x Concepto</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Movimiento Consolidado x Concepto</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> impmovtot.php Ver. 2006-10-26</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='impmovtot' action='impmovtot.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wano) or !isset($wmes) or !isset($wcco))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MOVIMIENTO CONSOLIDADO X CONCEPTO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003 order by Ccocod";
		$err = mysql_query($query,$conex);//or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
		$num = mysql_num_rows($err);// or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
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
		$query = "SELECT  Pronit, Pronom  from ".$empresa."_000006 order by Pronit";
        $err = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
		$num = mysql_num_rows($err);// or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
		$wtotE=0;
		$wtotS=0;
		$color="#dddddd";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>MOVIMIENTO CONSOLIDADO X CONCEPTO</font></b></font></td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Año : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Mes : </b>".$wmes."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Centro de Costos : </b>".$wcco."</td></tr>";
		$query = "SELECT Mencon, Condes, Conind, sum(Mdevto) from ".$empresa."_000010, ".$empresa."_000011, ".$empresa."_000008 ";
		$query .= " where  Menano = ".$wano;
		$query .= "   and  Menmes = ".$wmes;
		$query .= "   and  Mencco = '".substr($wcco,0,strpos($wcco,"-"))."' ";
		$query .= "   and  Mencon = Mdecon ";
		$query .= "   and  Mendoc = Mdedoc ";
		$query .= "   and  Menest = 'on' ";
		$query .= "   and  Mdecon = Concod ";
		$query .= "   and  Conind != '0' ";
		$query .= "  GROUP BY Mencon, Condes, Conind ";
		$query .= " union ";
		$query .= " SELECT Mencon, Condes, '-1', sum(Mdevto) from ".$empresa."_000010, ".$empresa."_000011, ".$empresa."_000008 ";
		$query .= " where  Menano = ".$wano;
		$query .= "   and  Menmes = ".$wmes;
		$query .= "   and  Mencco = '".substr($wcco,0,strpos($wcco,"-"))."' ";
		$query .= "   and  Mencon = Mdecon ";
		$query .= "   and  Mendoc = Mdedoc ";
		$query .= "   and  Menest = 'on' ";
		$query .= "   and  Mdecon = Concod ";
		$query .= "   and  Conind = '0' ";
		$query .= "  GROUP BY Mencon, Condes, Conind ";
		$query .= " union ";
		$query .= " SELECT Mencon, Condes, '1', sum(Mdevto) from ".$empresa."_000010, ".$empresa."_000011, ".$empresa."_000008 ";
		$query .= " where  Menano = ".$wano;
		$query .= "   and  Menmes = ".$wmes;
		$query .= "   and  Menccd = '".substr($wcco,0,strpos($wcco,"-"))."' ";
		$query .= "   and  Mencon = Mdecon ";
		$query .= "   and  Mendoc = Mdedoc ";
		$query .= "   and  Menest = 'on' ";
		$query .= "   and  Mdecon = Concod ";
		$query .= "   and  Conind = '0' ";
		$query .= "  GROUP BY Mencon, Condes, Conind ";
		$query .= "  ORDER BY Mencon ";
		$err = mysql_query($query,$conex);// or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
		$num = mysql_num_rows($err);// or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONCEPTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VALOR X KARDEX</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wvalk = $row[2] * $row[3];
			if($wvalk > 0)
				$wtotE += $wvalk;
			else
				$wtotS += $wvalk;
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[3],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wvalk,2,'.',',')."</font></td></tr>";	
		}
		echo "<tr><td bgcolor=#999999 align=center colspan=3><font face='tahoma' size=2><b>TOTAL MOVIMIENTO ENTRADAS</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotE,2,'.',',')."</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 align=center colspan=3><font face='tahoma' size=2><b>TOTAL MOVIMIENTO SALIDAS</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotS,2,'.',',')."</b></font></td></tr>";		
		echo"</table>";
	}
}
?>
</body>
</html>
