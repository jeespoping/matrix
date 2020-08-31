<html>
<head>
  	<title>MATRIX Movimiento De Translados Entre Fechas</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Movimiento De Traslados Entre Fechas</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Itranslados.php Ver. 2007-08-30</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	
	$key = substr($user,2,strlen($user));
	echo "<form name='Itranslados' action='Itranslados.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper1) or !isset($wper2) or !isset($wccoo) or !isset($wccod))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MOVIMIENTO DE TRASLADOS ENTRE FECHAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro De Costos Origen</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoo' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro De Costos Destino</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes  from ".$empresa."_000003 order by Ccocod";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wccod'>";
			echo "<option>*-Todos</option>";
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
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>MOVIMIENTO DE TRASLADOS ENTRE FECHAS</font></b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Centro De Costos Origen : </b>".$wccoo."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Centro De Costos Destino : </b>".$wccod."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Inicial : </b>".$wper1."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Fecha Final : </b>".$wper2."</td></tr>";	
		//                  0        1       2       3       4       5         6
		$query = "SELECT  Menfec, Mendoc, Mencco, Menccd, Mdeart, Artnom, sum(Mdecan) from ".$empresa."_000010,".$empresa."_000011, ".$empresa."_000001 ";
	 	$query .= "  where Mencon = '002' ";
	 	$query .= "    and Menfec between '".$wper1."' and '".$wper2."'";
	    $query .= "    and Mencco = '".$wccoo."' ";
	    if($wccod != "*-Todos")
	    	$query .= "    and Menccd = '".substr($wccod,0,strrpos($wccod,"-"))."' ";
	    $query .= "    and Mendoc = Mdedoc ";
	    $query .= "    and Mencon = Mdecon ";
	    $query .= "    and Mdeart = Artcod  ";
	    $query .= "    Group by  Menfec, Mendoc, Mencco, Menccd, Mdeart, Artnom  ";
	    $query .= "    Order by  Menfec, Mendoc  ";
        $err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		$wdoca="";
		$wtot=0;
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FECHA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOCUMENTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. ORIGEN</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C.C. DESTINO</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>ARTICULO</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>DESCRIPCION</b></font></td><td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$color="#99CCFF";
			else
				$color="#FFFFFF";
			if($wdoca != $row[1])
			{
				$wtot++;
				$wdoca=$row[1];
				echo "<tr><td bgcolor=#dddddd colspan=7>Translado nro. ".$wtot."</td></tr>";
			}
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[4]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[5]."</font></td>";			
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[6],2,'.',',')."</font></td></tr>";	
		}
		echo "<tr><td bgcolor=#999999 colspan=7><font face='tahoma' size=2><b>NUMERO DE TRANSLADOS : ".$wtot."</b></font></td></tr>";
		echo"</table>";
	}
}
?>
</body>
</html>
