<html>
<head>
  	<title>MATRIX Movimiento de Inventarios x Articulo</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Movimiento De Inventarios X Articulo X CC X Fecha X Concepto</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> imovxarccfeco.php Ver. 2007-01-15</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='imovxarccfeco' action='imovxarccfeco.php' method=post>";




	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper1) or !isset($wper2) or !isset($wcon) or !isset($wcco) or !isset($wart) or $wart == "0-SELECCIONE")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MOVIMIENTO DE INVENTARIOS X ARTICULO X CC X FECHA X CONCEPTO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Descripcion Articulo</td>";
		if(!isset($wartn))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wartn' size=40 maxlength=40></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wartn' size=40 maxlength=40 value=".$wartn."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo</td><td bgcolor=#cccccc align=center>";
		echo "<select name='wart'>";
		echo "<option selected>0-SELECCIONE</option>";
		if(isset($wartn) and $wartn != "")
		{
			$query = "SELECT Artcod, Artnom from ".$empresa."_000001 where Artcod = '".$wartn."' order by Artnom";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$num = mysql_num_rows($err);
			if ($num == 0)
			{
				$query = "SELECT Artcod, Artnom from ".$empresa."_000001 where Artnom like '%".$wartn."%' order by Artnom";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$num = mysql_num_rows($err) ;
			}
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($wart == $row[0]."-".$row[1])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
		}
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		if(!isset($wper1))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10 value=".$wper1."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		if(!isset($wper2))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10 value=".$wper2."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Concepto</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Concod, Condes   from ".$empresa."_000008 order by Concod";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcon'>";
			echo "<option>*-Todos</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wcon == $row[0]."-".$row[1])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes    from ".$empresa."_000003 order by Ccocod";
		$err = mysql_query($query,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
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
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wart=substr($wart,0,strpos($wart,"-"));
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>MOVIMIENTO DE INVENTARIOS X ARTICULO X CC X FECHA X CONCEPTO</font></b></font></td></tr>";
		$color="#dddddd";
		if(isset($wart) and $wart != "")
		{
			$query = "SELECT Artnom from ".$empresa."_000001  where Artcod='".$wart."'";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$row = mysql_fetch_array($err);
			echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo : </b>".$wart."-".$row[0]."</td></tr>";
		}
		else
			echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Articulo : </b>TODOS</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wper1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wper2."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Concepto : </b>".$wcon."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Centro de Costos : </b>".$wcco."</td></tr>";
		$query = "SELECT  Menfec, Mendoc, Mdecan, Mdepiv, Mdevto, Mdefve, Mdenlo, Mencco, Menccd, Mendan, Mencon, Condes, ".$empresa."_000010.Hora_data from ".$empresa."_000010, ".$empresa."_000011, ".$empresa."_000008 ";
		$query .= "  where  Menfec between '".$wper1."' and '".$wper2."'";
		if($wcon != "*-Todos")
			$query .= "     and Mencon='".substr($wcon,0,strpos($wcon,"-"))."'";
		$query .= "     and (Mencco='".substr($wcco,0,strpos($wcco,"-"))."'";
		$query .= "      or  Menccd='".substr($wcco,0,strpos($wcco,"-"))."')";
		$query .= "     and Mencon = Mdecon ";
		$query .= "     and Mendoc = Mdedoc ";
		$query .= "     and Mdeart='".$wart."'";
		$query .= "     and Mencon = Concod ";
		$query .= "     ORDER BY  Menfec, ".$empresa."_000010.Hora_data ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		$wstotg=0;
		$wstotiva=0;
		$wstotca=0;
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FECHA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOC.</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>% IVA </b></font><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR UNITARIO</b></font></td></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR IVA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR TOTAL</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VENCIMIENTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>NRo. LOTE</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. ORIGEN</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. DESTINO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>ANEXO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONCEPTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>HORA</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			if($row[2] != 0)
				$valuni=$row[4] / $row[2];
			else
				$valuni=0;
			$valiva=($row[3] / 100) * $row[4];
			$wstotg += $row[4];
			$wstotiva += $valiva;
			$wstotca += $row[2];
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[1]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[2],2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[3],2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$valuni,4,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$valiva,2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[4],2,'.',',')."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[5]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[6]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[7]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[8]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[9]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[10]."-".$row[11]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[12]."</font></td></tr>";
		}
		if($wstotca > 0)
		{
			echo "<tr><td bgcolor=#999999 align=center colspan=2><font face='tahoma' size=2><b>TOTALES</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>".number_format((double)$wstotca,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=2><font face='tahoma' size=2><b>&nbsp</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wstotiva,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wstotg,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=7>&nbsp</td></tr>";
		}
		echo"</table>";
	}
}
?>
</body>
</html>
