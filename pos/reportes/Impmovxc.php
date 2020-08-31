<html>
<head>
  	<title>MATRIX Movimiento de Inventarios x Concepto</title>
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
	echo "<form name='impmovxa' action='Impmovxc.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper1) or !isset($wper2) or !isset($wcon))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MOVIMIENTO DE INVENTARIOS X CONCEPTO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10></td></tr>";
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
				if($wcont == $row[0]."-".$row[1])
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
		$wtotiva=array();
		$query = "SELECT  Artiva  from ".$empresa."_000001 ";
		$query .= "  group by Artiva";
		$query .= "  order  by Artiva";
		$err = mysql_query($query,$conex);
		$numi = mysql_num_rows($err);
		for ($i=0;$i<$numi;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtotiva[$i][0] = $row[0];
		}
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>MOVIMIENTO DE INVENTARIOS X CONCEPTO</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wper1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wper2."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Concepto : </b>".$wcon."</td></tr>";
		$query = "SELECT Menfec, Mendoc, Mencco, Menccd, Mendan, Menfac, Mennit   from ".$empresa."_000010 ";
		$query .= " where  Menfec between '".$wper1."' and '".$wper2."'";
		$query .= "     and Mencon='".substr($wcon,0,strpos($wcon,"-"))."'";
		$query .= "     ORDER BY  Menfec, Mendoc ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FECHA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOCUMENTO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR TOTAL</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. ORIGEN</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. DESTINO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOC. ANEXO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOC. SOPORTE</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>NIT</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>PROVEEDOR</b></font></td>";
		for ($i=0;$i<$numi;$i++)
			if($wtotiva[$i][0] == 0)
				echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>EXCLUIDO</b></font></td>";
			else
				echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>TOTAL IVA DEL ".$wtotiva[$i][0]."%</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>TOTAL IVA</b></font></td>";
		echo "</tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($row[5] != 0)
			{
				$query = "SELECT  Pronom  from ".$empresa."_000006 ";
				$query .= " where Pronit='".$row[6]."'";
				$err1 = mysql_query($query,$conex);
				$row1 = mysql_fetch_array($err1);
				$wnit=$row1[0];
			}
			else
				$wnit="";
			$wsw=1;
			$query = "SELECT  Mdevto, Mdepiv  from ".$empresa."_000011 ";
			$query .= " where  Mdecon='".substr($wcon,0,strpos($wcon,"-"))."'";
			$query .= "     and   Mdedoc=".$row[1];
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$wtotg=0;
			for ($k=0;$k<$numi;$k++)
				$wtotiva[$k][1] = 0;
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				$wtotg += $row1[0];
				for ($k=0;$k<$numi;$k++)
					if($row1[1] == $wtotiva[$k][0])
						if($row1[1] == 0)
							$wtotiva[$k][1] += $row1[0] ;
						else
							$wtotiva[$k][1] += ($row1[0] *  $row1[1] / 100);
			}
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$wtotg,2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[3]."</font></td>";	
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[4]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[5]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[6]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnit."</font></td>";
			$wtiva=0;
			for ($k=0;$k<$numi;$k++)
			{
				if($wtotiva[$k][0] != 0)
					$wtiva += $wtotiva[$k][1];
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2> ".number_format((double)$wtotiva[$k][1],2,'.',',')."</font></td>";
			}	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2> ".number_format((double)$wtiva,2,'.',',')."</font></td>";
			echo "</tr>";
		}
		echo"</table><br><br>";
		echo"<b>TOTAL REGISTROS PROCESADOS : ".$num."</b><br>";
	}
}
?>
</body>
</html>