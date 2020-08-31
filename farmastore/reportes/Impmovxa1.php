<html>
<head>
  	<title>MATRIX</title>
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
	echo "<form name='impmovxa' action='Impmovxa.php' method=post>";
	

	

	if(!isset($wart) or !isset($wper1) or !isset($wper2) or !isset($wcon))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MOVIMIENTO DE INVENTARIOS X ARTICULO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wart' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Concepto</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Concod, Condes   from farstore_000008 order by Concod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcon'>";
			echo "<option>0-NO APLICA</option>";
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
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/FARMASTORE/logo farmastore.png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>MOVIMIENTO X ARTICULO</font></b></font></td></tr>";
		$color="#dddddd";
		$query = "SELECT Artnom from farstore_000001  where Artcod='".$wart."'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo : </b>".$wart."-".$row[0]."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wper1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wper2."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Concepto : </b>".$wcon."</td></tr>";
		$query = "SELECT Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, farstore_000011.Fecha_data, Mdecon, Condes,Mdedoc  from farstore_000011, farstore_000008  ";
		$query .= " where  farstore_000011.Fecha_data between '".$wper1."' and '".$wper2."'";
		if(substr($wcon,0,strpos($wcon,"-")) != 0)
			$query .= "     and Mdecon='".substr($wcon,0,strpos($wcon,"-"))."'";
		$query .= "     and Mdeart='".$wart."'";
		$query .= "     and Mdecon=Concod";
		$query .= "     ORDER BY  farstore_000011.Fecha_data, farstore_000011.Hora_data ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$data=array();
		$wtotg=0;
		$wtotiva=0;
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FECHA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOC.</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONCEPTO</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>% IVA </b></font><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR UNITARIO</b></font></td></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR IVA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR TOTAL</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VENCIMIENTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>NRo. LOTE</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. ORIGEN</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. DESTINO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>ANEXO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>PROVEEDOR</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			$row = mysql_fetch_array($err);
			if($row[0] != 0)
				$valuni=$row[1] / $row[0];
			else
				$valuni=0;
			$valiva=($row[2] / 100) * $row[1];
			$wtotg += $row[1];
			$wtotiva += $valiva;
			$query = "SELECT  Mencco, Menccd, Mendan, Mennit, Pronom  from farstore_000010,farstore_000006 ";
			$query .="   where Mendoc = ".$row[8];
			$query .="        and Mencon = '".$row[6]."'";
			$query .="        and Mennit = Pronit ";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[5]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[8]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[6]."-".$row[7]."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[0],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[2],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$valuni,2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$valiva,2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[1],2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[3]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[4]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row1[0]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row1[1]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row1[2]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row1[4]."</font></td></tr>";
		}
		if($wtotg > 0)
			echo "<tr><td bgcolor=#999999 align=center colspan=6><font face='tahoma' size=2><b>TOTALES</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotiva,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotg,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=6>&nbsp</td></tr>";	
		echo"</table>";
	}
}
?>
</body>
</html>