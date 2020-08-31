<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Archivo CAFAN</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> CAFAM1.php Ver. 2015-04-22</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='CAFAM1.php' method=post>";
	

	

	if(!isset($wcco))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ARCHIVO CAFAM INVENTARIO FISICO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Bodega</td>";
		echo "<td bgcolor=#cccccc align=center>";
		echo "<select name='wcco'>";
		echo "<option>3062-LA 80</option>";
		echo "<option>3064-SAN FERNANDO</option>";
		echo "<option>3073-LA 70</option>";
		echo "</select>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wcco = substr($wcco,0,4);
		$EAN13=array();
		$query  = "select SUBSTRING(axpart,1, LOCATE('-',axpart)-1),Axpcpr from farpmla_000009 ";
		$query .= " Group by 1 ";
		$query .= " Order by 1 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				$EAN13[$i][0] = $row2[0];
				$EAN13[$i][1] = $row2[1];
			}
		}
		$numean = $num2;
		
		
		//                  0       1        2       3
		$query  = "select Karcco, karpro, Karexi, Karcod ";
		$query .= "     from farpmla_000007,farpmla_000001 ";
		$query .= "  	  where Karcco = '".$wcco."' ";
		$query .= "  	    and Karcod = Artcod ";
		$query .= "  	    and Artest = 'on' ";
		$query .= "  	    and SUBSTRING(Artcod,1,2) NOT IN ('PS')";
		$query .= "  	    and SUBSTRING(Artcod,1,4) NOT IN ('1SOS')";
		$query .= "  	    and SUBSTRING(Artcod,1,3) NOT IN ('1IM')";
		$query .= "  order by 4,1 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../../planos/".$wcco.".txt"; 
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$registro = "";
				$pos=bi($EAN13,$numean,$row[3]);
				if($pos != -1)
					$wean13 = $EAN13[$pos][1];
				else
					$wean13 = "0";
				$wtotal = $row[1] * $row[2];
				
				//CODIGO DEL ALMACEN
				$row[0] = substr($row[0],0,4);
				$row[0] = str_pad($row[0],4,"0", STR_PAD_LEFT);
				$registro = $row[0];
				
				//FECHA
				$registro = $registro."|".date("Ymd");
				
				//EAN13
				$wean13 = substr($wean13,0,13);
				$wean13 = str_pad($wean13,13,"0", STR_PAD_LEFT);
				$registro = $registro."|".$wean13;
				
				//COSTO DE COMPRA
				$row[1] = number_format((double)$row[1],2,'','');
				$row[1] = str_pad($row[1],12,"0", STR_PAD_LEFT);
				$registro = $registro."|".$row[1];
				
				//CANTIDAD
				$row[2] = number_format((double)$row[2],2,'','');
				$row[2] = str_pad($row[2],10,"0", STR_PAD_LEFT);
				$registro = $registro."|".$row[2];
				
				//VALOR TOTAL
				$wtotal = number_format((double)$wtotal,2,'','');
				$wtotal = str_pad($wtotal,10,"0", STR_PAD_LEFT);
				$registro = $registro."|".$wtotal;
				
				//PLU CODIGO INTERNO
				$row[3] = str_pad($row[3],9,"0", STR_PAD_LEFT);
				$registro = $registro."|".$row[3];
				
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				echo "REGISTRO GRABADO NRO : ".$i."<br>";
			}
			echo "<b>TOTAL REGISTROS GRABADOS : ".$num."</b><br>";
			fclose ($file);
			$ruta="../../planos/".$wcco.".txt";
			echo "<A href=".$ruta.">Haga Click Para Bajar el Archivo de CAFAM INVENTARIO FISICO BODEGA ".$wcco."</A>";
		}
	}
}
?>
</body>
</html>
