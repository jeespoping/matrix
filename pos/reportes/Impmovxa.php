<html>
<head>
  	<title>MATRIX Movimiento de Inventarios x Articulo</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Movimiento De Inventarios X Articulo</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> impmovxa.php Ver. 2006-11-23</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function bi($d,$n,$k,$i)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][$i]),20);
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}
function dias_transcurridos($fecha_i,$fecha_f)
{
	$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
	$dias 	= abs($dias); $dias = floor($dias);		
	return $dias;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='impmovxa' action='Impmovxa.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper1) or !isset($wper2) or !isset($wcon))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MOVIMIENTO DE INVENTARIOS X ARTICULO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Articulo</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wart' size=20 maxlength=20></td></tr>";
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
		$numdias=dias_transcurridos($wper1,$wper2);
		if($numdias < 366)
		{
			$dsan=array();
			$query = "SELECT  Pronit, Pronom  from ".$empresa."_000006 order by Pronit";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{	
				$row = mysql_fetch_array($err);
				$dsan[$i][0]=$row[0];
				$dsan[$i][1]=$row[1];
			}
			$tot=$num-1;
			echo "<table border=0 align=center>";
			echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
			echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>MOVIMIENTO X ARTICULO</font></b></font></td></tr>";
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
			echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wper1."</td></tr>";
			echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wper2."</td></tr>";	
			echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Concepto : </b>".$wcon."</td></tr>";
			$query = "SELECT  Mendoc, Mencon, Mencco, Menccd, Mendan, Mennit, Menfec, Mdecan, Mdevto, Mdepiv, Mdefve, Mdenlo, Mdecon, Condes, Mdeart, Artnom, Menfac  from ".$empresa."_000010, ".$empresa."_000011, ".$empresa."_000001, ".$empresa."_000008 ";
			$query .= " where  Menfec between '".$wper1."' and '".$wper2."'";
			if(substr($wcon,0,strpos($wcon,"-")) != 0)
				$query .= "     and Mencon='".substr($wcon,0,strpos($wcon,"-"))."'";
			$query .= "     and   Mencon = Mdecon ";
			$query .= "     and   Mendoc = Mdedoc ";
			if(isset($wart) and $wart != "")
				$query .= "     and   Mdeart='".$wart."'";
			$query .= "     and   Mdeart = artcod";
			$query .= "     and Mdecon=Concod";
			$query .= "     ORDER BY  Menfec, ".$empresa."_000010.Hora_data ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$wtotg=0;
			$wtotiva=0;
			$wstotg=0;
			$wstotiva=0;
			$key1="";
			echo "</tr></table><br><br>";
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FECHA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOC.</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONCEPTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>ARTICULO</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>% IVA </b></font><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR UNITARIO</b></font></td></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR IVA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VLR TOTAL</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>VENCIMIENTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>NRo. LOTE</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. ORIGEN</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. DESTINO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>ANEXO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>PROVEEDOR</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FACTURA</b></font></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[5] != "0")
				{
					$pos=bi($dsan,$tot,$row[5],0);
					$wnit="";
					if($pos != -1)
						$wnit=$dsan[$pos][0]." - ".$dsan[$pos][1];
				}
				else
					$wnit="";
				if($key1 != $row[0].$row[1] )
				{
					$color="#FFCC66";
					if($key1 != "")
					{
						echo "<tr><td bgcolor=#999999 align=center colspan=7><font face='tahoma' size=2><b>TOTAL DOCUMENTO</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wstotiva,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wstotg,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=7>&nbsp</td></tr>";	
						$wstotg=0;
						$wstotiva=0;
					}
					$key1 = $row[0].$row[1];
				}
				elseif($i % 2 == 0)
							$color="#9999FF";
						else
							$color="#ffffff";
				if($row[7] != 0)
					$valuni=$row[8] / $row[7];
				else
					$valuni=0;
				$valiva=($row[9] / 100) * $row[8];
				$wtotg += $row[8];
				$wtotiva += $valiva;
				$wstotg += $row[8];
				$wstotiva += $valiva;
				echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[6]."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[0]."</font></td>";	
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[12]."-".$row[13]."</font></td>";	
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[14]."-".$row[15]."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[7],2,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[9],2,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$valuni,4,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$valiva,2,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[8],2,'.',',')."</font></td>";
				echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[10]."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[11]."</font></td>";
				echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[2]."</font></td>";
				echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$row[3]."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[4]."</font></td>";
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wnit."</font></td>";
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".$row[16]."</font></td></tr>";
			}
			if($wtotg > 0)
			{
				echo "<tr><td bgcolor=#999999 align=center colspan=7><font face='tahoma' size=2><b>TOTAL DOCUMENTO</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wstotiva,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wstotg,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=7>&nbsp</td></tr>";	
				echo "<tr><td bgcolor=#999999 align=center colspan=7><font face='tahoma' size=2><b>TOTALES</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotiva,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotg,2,'.',',')."</b></font></td><td bgcolor=#999999 align=center colspan=7>&nbsp</td></tr>";	
			}
			echo"</table>";
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO DE TIEMPO DEL REPORTE ES SUPERIOR A UN A??O -- LLAME A SISTEMAS</MARQUEE></FONT>";
			echo "<br><br>";
		}
	}
}
?>
</body>
</html>
