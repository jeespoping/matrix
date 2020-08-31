<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Archivo CAFAN</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> CAFAM.php Ver. 2015-04-09</b></font></tr></td></table>
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
	echo "<form action='CAFAM.php' method=post>";
	

	

	if(!isset($wcco1))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ARCHIVO CAFAM</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
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
		
		$TARIF=array();
		$query  = "select SUBSTRING(Mtaart,1, LOCATE('-',Mtaart)-1),'3062',Mtavac from farpmla_000026 ";
		$query .= " where SUBSTRING(Mtatar,1, LOCATE('-',Mtatar)-1) = '01' ";
		$query .= "   and SUBSTRING(Mtacco,1, LOCATE('-',Mtacco)-1) = '3062' ";
		$query .= " UNION  ";
		$query .= " select SUBSTRING(Mtaart,1, LOCATE('-',Mtaart)-1),'3064',Mtavac from farpmla_000026 ";
		$query .= " where SUBSTRING(Mtatar,1, LOCATE('-',Mtatar)-1) = '01' ";
		$query .= "   and SUBSTRING(Mtacco,1, LOCATE('-',Mtacco)-1) = '3064' ";
		$query .= " Order by 1 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		$kt="";
		$numxp=-1;
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				if($kt != $row2[0])
				{
					$numxp++;
					$kt = $row2[0];
					$TARIF[$numxp][0] = $row2[0];
					$TARIF[$numxp][1] = $row2[2];
				}
			}
		}
		$numtar = $numxp;
		
		$PROVE=array();
		$query  = "select mdeart, mennit, Pronom from farpmla_000010,farpmla_000011,farpmla_000006 ";
		$query .= "  where menano >= '2014' ";
		$query .= "    and mencon = '001' ";
		$query .= "    and mencco = '".$wcco1."' ";
		$query .= "    and mencon = mdecon ";
		$query .= "    and mendoc = mdedoc ";
		$query .= "    and mennit = Pronit ";
		$query .= " order by 1 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		$kt="";
		$numxp=-1;
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				if($kt != $row2[0])
				{
					$numxp++;
					$kt = $row2[0];
					$PROVE[$numxp][0] = $row2[0];
					$PROVE[$numxp][1] = $row2[1];
					$PROVE[$numxp][2] = $row2[2];
				}
			}
		}
		$numpro = $numxp;
		
		$KARDEX=array();
		$query  = "select Karcod, Karvuc from farpmla_000007 ";
		$query .= "  	where Karcco = '".$wcco1."' ";
		$query .= " order by 1 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		$kt="";
		$numxp=-1;
		if($num2 > 0)
		{
			for ($i=0;$i<$num2;$i++)
			{
				$row2 = mysql_fetch_array($err2);
				if($kt != $row2[0])
				{
					$numxp++;
					$kt = $row2[0];
					$KARDEX[$numxp][0] = $row2[0];
					$KARDEX[$numxp][1] = $row2[1];
				}
			}
		}
		$numkar = $numxp;
		
		//                  0       1     2      3      4      5      6      7      8      9      10
		$query  = "select Artcod,Artcna,Artnom,Artgen,Artuni,Artiva,Artctr,Artima,Artfvi,Artffa,Artcon ";
		$query .= "     from farpmla_000001 ";
		$query .= "  	  where artest = 'on' ";
		$query .= "  	    and SUBSTRING(Artcod,1,2) NOT IN ('PS')";
		$query .= "  	    and SUBSTRING(Artcod,1,4) NOT IN ('1SOS')";
		$query .= "  	    and SUBSTRING(Artcod,1,3) NOT IN ('1IM')";
		$query .= "  order by 1 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../../planos/CAFAM.txt"; 
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$registro = "";
				$pos=bi($EAN13,$numean,$row[0]);
				if($pos != -1)
					$wean13 = $EAN13[$pos][1];
				else
					$wean13 = "N/D";
					
				$pos=bi($TARIF,$numtar,$row[0]);
				if($pos != -1)
					$wtarif = $TARIF[$pos][1];
				else
					$wtarif = "N/D";
					
				$pos=bi($PROVE,$numpro,$row[0]);
				if($pos != -1)
				{
					$wprove  = $PROVE[$pos][1];
					$wproveN = $PROVE[$pos][2];
				}
				else
				{
					$wprove  = "N/D";
					$wproveN = "N/D";
				}
					
				$pos=bi($KARDEX,$numkar,$row[0]);
				if($pos != -1)
					$wprecio = $KARDEX[$pos][1];
				else
					$wprecio = "N/D";
				
				//EAN13
				$wean13 = substr($wean13,0,13);
				$wean13 = str_pad($wean13,13," ", STR_PAD_LEFT);
				$registro = $wean13;
				
				//PLU CODIGO INTERNO
				$row[0] = str_pad($row[0],9," ", STR_PAD_LEFT);
				$registro = $registro.",".$row[0];
				
				//NOMBRE ARTICULO
				$row[2] = substr($row[2],0,30);
				$row[2] = str_replace(",","-",$row[2]);
				$row[2] = str_pad($row[2],30," ", STR_PAD_RIGHT);
				$registro = $registro.",".$row[2];
				
				//PRINCIPIO ACTIVO -GENERICO
				$row[3] = substr($row[3],0,60);
				$row[3] = str_replace(",","-",$row[3]);
				$row[3] = str_pad($row[3],60," ", STR_PAD_RIGHT);
				$registro = $registro.",".$row[3];
				
				//FORMA FARMACEUTICA
				$row[9] = substr($row[9],0,15);
				$row[9] = str_replace(",","-",$row[9]);
				$row[9] = str_pad($row[9],15," ", STR_PAD_RIGHT);
				$registro = $registro.",".$row[9];
				
				//CONCENTRACION
				$row[10] = substr($row[10],0,15);
				$row[10] = str_replace(",","-",$row[10]);
				$row[10] = str_pad($row[10],15," ", STR_PAD_RIGHT);
				$registro = $registro.",".$row[10];
				
				//PRESENTACION FARMACEUTICA
				$UM = $row[4];
				$row[4] = substr($row[4],3);
				$row[4] = str_replace(",","-",$row[4]);
				$row[4] = str_pad($row[4],15," ", STR_PAD_RIGHT);
				$registro = $registro.",".$row[4];
				
				//CONTENIDO DE LA PRESENTACION
				$registro = $registro.",N/D  ";
				
				//UNIDAD DE MEDIDA
				$UM = substr($UM,0,2);
				$UM = str_replace(",","-",$UM);
				$UM = str_pad($UM,4," ", STR_PAD_RIGHT);
				$registro = $registro.",".$UM;
				
				//IVA
				$row[5] = str_pad($row[5],2," ", STR_PAD_LEFT);
				$registro = $registro.",".$row[5];
				
				//NIT PROVEEDOR
				$wprove = substr($wprove,0,12);
				$wprove = str_replace(",","-",$wprove);
				$wprove = str_pad($wprove,12," ", STR_PAD_RIGHT);
				$registro = $registro.",".$wprove;
				
				//NOMBRE PROVEEDOR
				$wproveN = substr($wproveN,0,60);
				$wproveN = str_replace(",","-",$wproveN);
				$wproveN = str_pad($wproveN,60," ", STR_PAD_RIGHT);
				$registro = $registro.",".$wproveN;
				
				//COSTO DE COMPRA
				$wprecio = number_format((double)$wprecio,2,',','');
				$wprecio = str_pad($wprecio,11," ", STR_PAD_LEFT);
				$registro = $registro.",".$wprecio;
				
				//TARIFA
				$wtarif = number_format((double)$wtarif,2,',','');
				$wtarif = str_pad($wtarif,11," ", STR_PAD_LEFT);
				$registro = $registro.",".$wtarif;
				
				//CONTENIDO DE LA PRESENTACION
				$registro = $registro.",   1";
				
				//CUM
				$row[1] = substr($row[1],0,20);
				$row[1] = str_replace(",","-",$row[1]);
				$row[1] = str_pad($row[1],20," ", STR_PAD_RIGHT);
				$registro = $registro.",".$row[1];
				
				//CONTENIDO DE LA PRESENTACION
				if($row[6] == "on")
					$registro = $registro.",S";
				else
					$registro = $registro.",N";
					
				//ESTADO DE LA CADENA
				$registro = $registro.",A";
				
				//REGISTRO SANITARIO
				$row[7] = substr($row[7],0,20);
				$row[7] = str_replace(",","-",$row[7]);
				$row[7] = str_pad($row[7],20," ", STR_PAD_RIGHT);
				$registro = $registro.",".$row[7];
				
				//REGISTRO SANITARIO
				$row[8] = substr($row[8],0,10);
				$row[8] = str_replace(",","-",$row[8]);
				$row[8] = str_pad($row[8],10," ", STR_PAD_RIGHT);
				$registro = $registro.",".$row[8];
				
				//CODIGO PROVEEDOR
				$wprove = substr($wprove,0,12);
				$wprove = str_replace(",","-",$wprove);
				$wprove = str_pad($wprove,12," ", STR_PAD_RIGHT);
				$registro = $registro.",".$wprove;
				
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				echo "REGISTRO GRABADO NRO : ".$i."<br>";
			}
			echo "<b>TOTAL REGISTROS GRABADOS : ".$num."</b><br>";
			fclose ($file);
			$ruta="../../planos/CAFAM.txt";
			echo "<A href=".$ruta.">Haga Click Para Bajar el Archivo de CAFAM</A>";
		}
	}
}
?>
</body>
</html>
