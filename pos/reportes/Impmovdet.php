<html>
<head>
  	<title>MATRIX Movimiento x Articulo x Concepto</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Movimiento De Inventarios X Articulo Y Concepto</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Impmovdet.php Ver. 2006-01-10</b></font></tr></td></table>
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
			if(strtoupper($k) == strtoupper($d[$lm][$i]))
				return $lm;
			elseif(strtoupper($k) < strtoupper($d[$lm][$i]))
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
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	
	$key = substr($user,2,strlen($user));
	echo "<form name='Impmovdet' action='Impmovdet.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper1) or !isset($wper2) or !isset($wcon))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MOVIMIENTO DE INVENTARIOS X ARTICULO Y CONCEPTO</td></tr>";
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
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>MOVIMIENTO X ARTICULO Y CONCEPTO</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wper1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wper2."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Concepto : </b>".$wcon."</td></tr>";
		$dsan=array();
		$query = "SELECT  Mdeart, Mennit, Pronom, max(Menfec)   from ".$empresa."_000010,".$empresa."_000011, ".$empresa."_000006 ";
	 	$query .= "  where Mencon = '001' ";
	    $query .= "      and  Mdecon = Mencon  ";
	    $query .= "      and  Mdedoc = Mendoc ";
	    $query .= "      and  Mennit = Pronit  ";
	    $query .= "      Group by  Mdeart, Mennit, Pronom  ";
	    $query .= "      Order by  Mdeart  ";
        $err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		for ($i=0;$i<$num;$i++)
		{	
			$row = mysql_fetch_array($err);
			$dsan[$i][0]=$row[0];
			$dsan[$i][1]=$row[1]."-".$row[2];
		}
		$tot=$num-1;
		$query = "SELECT  Mencco,Mdeart, Artnom, sum(Mdecan)   from ".$empresa."_000010,".$empresa."_000011, ".$empresa."_000001 ";
		$query .= "  where  Menfec between '".$wper1."' and '".$wper2."'";
	 	$query .= "       and  Mencon='".substr($wcon,0,strpos($wcon,"-"))."'";
	    $query .= "      and  Mdecon=Mencon  ";
	    $query .= "      and  Mdedoc=Mendoc ";
        $query .= "      and   Mdeart=Artcod  ";
        $query .= "      Group by  Mencco,Mdeart, Artnom  ";
	    $query .= "      Order by  Mencco,Mdeart  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotg=0;
		$wtotiva=0;
		$wstotg=0;
		$wstotiva=0;
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CENTRO DE <BR>COSTOS</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>ARTICULO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DESCRIPCION</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>PROVEEDOR</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$pos=bi($dsan,$tot,$row[1],0);
			$wpro="";
			if($pos != -1)
			{
				$wpro=$dsan[$pos][1];
			}
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wpro."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[3],2,'.',',')."</font></td></tr>";	
		}
		echo"</table>";
	}
}
?>
</body>
</html>