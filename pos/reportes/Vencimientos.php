<html>
<head>
  	<title>MATRIX Listado de Articulos x Fecha de Vencimiento y Nro de Lote</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Listado de Articulos x Fecha de Vencimiento y Nro de Lote</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Vencimientos.php Ver. 2009-02-25</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function Dias($f)
{
	$ann=(integer)substr($f,0,4)*360 +(integer)substr($f,5,2)*30 + (integer)substr($f,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($ann - $aa)/360;
	$meses=(($ann - $aa) % 360)/30;
	if ($ann1<1)
	{
		$dias1=(($ann - $aa) % 360) % 30;
		if((integer)$meses != 0)
			$Dias=(string)(integer)$meses." Mes(es) ".(string)$dias1." Dia(s)";	
		else
			$Dias=(string)$dias1." Dia(s)";	
		
	}
	else
	{
		$dias1=(($ann - $aa) % 360) % 30;
		$Dias=(string)(integer)$ann1." Año(s) ".(string)(integer)$meses." Mese(s) ".(string)$dias1." Dia(s)";
	}
	if($ann1 < 0 or $dias1 < 0 or $meses < 0)
		$Dias = "R-".$Dias;
	elseif((integer)$ann1 == 0 and (integer)$meses > 3 and (integer)$meses <= 6)
			$Dias = "L-".$Dias;
		elseif((integer)$ann1 == 0 and (integer)$meses > 1 and (integer)$meses <=3)
				$Dias = "A-".$Dias;
			elseif((integer)$ann1 == 0 and (integer)$meses <=1)
					$Dias = "N-".$Dias;
				else
					$Dias = "O-".$Dias;
				
	return $Dias;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Vencimientos' action='Vencimientos.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wper))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>LISTADO DE ARTICULOS X FECHA DE VENCIMIENTO Y NRO DE LOTE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' size=10 maxlength=10 value=".date("Y-m-d")."></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Ccocod, Ccodes from ".$empresa."_000003  order by Ccodes ";
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
		$dsan=array();
		$query = "DROP TABLE IF EXISTS tempo2";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$query = "CREATE TEMPORARY TABLE if not exists tempo2 as ";
		$query .= " select Mdeart, Mdefve, Mdenlo, sum(Mdecan)as cant from ".$empresa."_000010,".$empresa."_000011 ";
		$query .= "  where Mencon = '001' ";
		$query .= "    and Mencco = '".substr($wcco,0,strpos($wcco,"-"))."'";
		$query .= "    and ".$empresa."_000010.fecha_data >= '".$wper."'";
		$query .= "    and Mencon = Mdecon ";
		$query .= "    and Mendoc = Mdedoc ";
		$query .= "    and mdenlo != '.' ";
		$query .= "  group by 1,2,3 ";
		$query .= "  order by 1 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		
		$query = "CREATE INDEX vencimientos_idx on tempo2 (Mdeart(20), Mdefve, Mdenlo(30))";
      	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>LISTADO DE ARTICULOS X FECHA DE VENCIMIENTO Y NRO DE LOTE</font></b></font></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font face='tahoma'><b>Fecha de Proceso : </b>".$wper."</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><font face='tahoma'><b>Centro de Costos : </b>".$wcco."</td></tr>";
		echo "</tr></table><br><br>";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CODIGO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>NOMBRE<BR>COMERCIAL</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>PRINCIPIO<BR>ACTIVO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>FORMA<BR>FARMACOLOGICA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONCENTRACION</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>REGISTRO<BR>SANITARIO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CLISIFICACION<BR>DEL RIESGO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>F. VENCIMIENTO</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>NRO. LOTE</b></font></td>";
		echo "<td align=center bgcolor=#999999 ><font face='tahoma' size=2><b>CANTIDAD<BR>INGRESADA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD<BR>DESPACHADA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>CANTIDAD<BR>EN BODEGA</b></font></td>";
		echo "<td align=center bgcolor=#999999><font face='tahoma' size=2><b>NRO. DIAS <BR> DE VIGENCIA</b></font></td></tr>";
		//                        0                      1                              2                         3                          4                         5                            6             7            8             9                            10
      	$query = "select tempo2.mdeart,".$empresa."_000001.Artnom,".$empresa."_000001.Artgen,".$empresa."_000001.Artffa,".$empresa."_000001.Artcon,".$empresa."_000001.Artima,".$empresa."_000001.Artrie,tempo2.mdefve,tempo2.mdenlo,tempo2.cant,sum(".$empresa."_000011.Mdecan) from tempo2, ".$empresa."_000010, ".$empresa."_000011, ".$empresa."_000001 ";
		$query .= "  where ".$empresa."_000010.mencon = '002' ";
		$query .= "    and ".$empresa."_000010.Mencco = '".substr($wcco,0,strpos($wcco,"-"))."'";
		$query .= "    and ".$empresa."_000010.fecha_data >= '".$wper."'";
		$query .= "    and ".$empresa."_000010.Mencon = ".$empresa."_000011.Mdecon ";
		$query .= "    and ".$empresa."_000010.Mendoc = ".$empresa."_000011.Mdedoc ";
		$query .= "    and ".$empresa."_000011.mdeart = tempo2.mdeart ";
		$query .= "    and ".$empresa."_000011.mdenlo = tempo2.mdenlo "; 
		$query .= "    and ".$empresa."_000011.Mdefve = tempo2.mdefve ";
		$query .= "    and tempo2.mdeart = ".$empresa."_000001.Artcod ";
		if($empresa == "clisur")
			$query .= " and ".$empresa."_000001.Arttip in ('MD','MQ') ";
		$query .= " group by 1,2,3,4,5,6,7,8,9,10 ";
		$query .= " order by 1,8,9 ";
        $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$wtotg=0;
		$wtotiva=0;
		$wstotg=0;
		$wstotiva=0;
		$key1="";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$Dias=Dias($row[7]);
			$diff=$row[9] - $row[10];
			if($i % 2 == 0)
				$color="#99CCFF";
			else
				$color="#ffffff";

			echo "<tr>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[4]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[5]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[6]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[7]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[8]."</font></td>";
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[9],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[10],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$diff,2,'.',',')."</font></td>";	
			if($diff != 0)
			{
				$color1 = substr($Dias,0,strpos($Dias,"-"));
				$Dias = substr($Dias,strpos($Dias,"-")+1);
				switch ($color1)
				{
					case "R":
						$color="#CC0000";
					break;
					case "L":
						$color="#CC99FF";
					break;
					case "A":
						$color="#FFFF00";
					break;
					case "N":
						$color="#FF9900";
					break;
				}
				echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2>".$Dias."</font></td>";
			 }
			 else
			 	echo "<td bgcolor=".$color." align=center><font face='tahoma' size=2></font></td>";
			echo "</tr>";
		}
		echo"</table>";
	}
}
?>
</body>
</html>