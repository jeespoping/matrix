<html>
<head>
  <title>MATRIX</title>
  <style>
		.tipoTABLE1{font-family:Arial;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;}
		.tipo1{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo2{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;}
	    .tipo4{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;}
	    .tipo5{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo6{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:25em;}
	    .tipo7{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo8{color:#000066;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo9{color:#000066;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:25em;}
  	</style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Inspeccion de Mercancia Recibida</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>Inmere.php Ver. 2016-03-09</b></font></tr></td></table>
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
			if($val == 0)
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

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='Inmere.php' method=post>";
		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wnum))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INSPECCION DE MERCANCIA RECIBIDA</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero de la Entrada</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$errores="";
			$ent=array();
			$query  = "select Mendan from  ".$empresa."_000010 where mendoc = '".$wnum."' "; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				$query  = "select Mdeart, Mdecan, Menusu, Descripcion from  ".$empresa."_000010,".$empresa."_000011, usuarios where Mencon='900' and Mendoc='".$row[0]."' and Mencon = Mdecon and Mendoc = Mdedoc and Menusu = codigo"; 
				$query .= " order by 1 ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err);
				$entrada=array();
				if ($num1 > 0)
				{
					$ne=$num1;
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$entrada[$i][0] = $row1[0];
						$entrada[$i][1] = $row1[1];
						$entrada[$i][2] = $row1[2]."-".$row1[3];
					}
				}
				else
					$errores="EL NUMERO DE ORDEN DE COMPRA NO EXISTE!!!<br>";
			}
			else
				$errores="EL NUMERO DE LA ENTRADA NO EXISTE!!!<br>";
			
			if($errores == "")
			{
				echo "<center><table class=tipoTABLE1>";
				echo "<tr><td class=tipo7 colspan=21>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td class=tipo7 colspan=21>LAS AMERICAS CLINICA DEL SUR</td></tr>";
				echo "<tr><td class=tipo7 colspan=21>INSPECCION DE MERCANCIA RECIBIDA</td></tr>";
				echo "<tr><td class=tipo7 colspan=21>NRO DE ENTRADA : ".$wnum."</td></tr>";
				echo "<tr><td class='tipo2'>CODIGO</td><td class='tipo2'>NOMBRE<br>COMERCIAL</td><td class='tipo2'>PRINCIPIO<br>ACTIVO</td><td class='tipo2'>FORMA<br>FARMACOLOGICA</td><td class='tipo2'>CONCENTRACION</td><td class='tipo2'>REGISTRO<br>INVIMA</td><td class='tipo2'>FECHA VEN.<br>REG. INVIMA</td><td class='tipo2'>CLASIFICACION<br>RIESGO</td><td class='tipo2'>CADENA<br>DE FRIO</td><td class='tipo2'>FECHA<br>VENCIMIENTO</td><td class='tipo2'>NRO<br>LOTE</td><td class='tipo2'>NRO ORDEN<br>DE COMPRA</td><td class='tipo2'>CANTIDAD<br>SOLICITADA</td><td class='tipo2'>RESPONSABLE<br>SOLICITUD</td><td class='tipo2'>FECHA DE<br>ENTRADA</td><td class='tipo2'>NUMERO<br>ENTRADA</td><td class='tipo2'>CANTIDAD<br>INGRESADA</td><td class='tipo2'>RESPONSABLE<br>INGRESO</td><td class='tipo2'>DIFERENCIA</td><td class='tipo2'>PROVEEDOR</td><td class='tipo2'>NRO<br>FACTURA</td></tr>";
				//                  0       1       2       3       4        5       6       7       8       9       10     11      12      13      14      15       16          17      18
				$query  = "select Artcod, Artnom, Artgen, Artffa, Artcon, Artima, Artrie, Mdefve, Mdenlo, Mendan, Mendoc, Mdecan, Menusu, Mennit, Menfac, Pronom, Descripcion, Artfvi, Menfec ";
				$query .= "from ".$empresa."_000010,".$empresa."_000011,".$empresa."_000001,".$empresa."_000006, usuarios ";
				$query .= " where mendoc = '".$wnum."' "; 
				$query .= "   and mencon = '001' "; 
				$query .= "   and mendoc = mdedoc ";
				$query .= "   and mencon = mdecon ";
				$query .= "   and mdeart = artcod ";
				$query .= "   and mennit = pronit ";
				$query .= "   and Menusu = Codigo ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				$lin=0;
				for ($i=0;$i<$num;$i++)
				{
					if($i % 2 == 0)
					{
						$colorc="tipo5";
						$colorl="tipo6";
					}
					else
					{
						$colorc="tipo8";
						$colorl="tipo9";
					}
					$row = mysql_fetch_array($err);
					echo "<tr>";
					echo "<td class=".$colorc.">".$row[0]."</td>";
					echo "<td class=".$colorl.">".$row[1]."</td>";
					echo "<td class=".$colorl.">".$row[2]."</td>";
					echo "<td class=".$colorl.">".$row[3]."</td>";
					echo "<td class=".$colorl.">".$row[4]."</td>";
					echo "<td class=".$colorl.">".$row[5]."</td>";
					echo "<td class=".$colorl.">".$row[17]."</td>";
					echo "<td class=".$colorc.">".$row[6]."</td>";
					echo "<td class=".$colorc.">PENDIENTE</td>";
					echo "<td class=".$colorc.">".$row[7]."</td>";
					echo "<td class=".$colorc.">".$row[8]."</td>";
					echo "<td class=".$colorc.">".$row[9]."</td>";
					$pos=bi($entrada,$ne,$row[0],0);
					if($pos != -1)
					{
						echo "<td class=".$colorc.">".$entrada[$pos][1]."</td>";
						echo "<td class=".$colorc.">".$entrada[$pos][2]."</td>";
					}
					else
					{	
						echo "<td class=".$colorc.">0</td>";
						echo "<td class=".$colorc.">NO ESPECIFICO</td>";
					}
					echo "<td class=".$colorc.">".$row[18]."</td>";
					echo "<td class=".$colorc.">".$row[10]."</td>";
					echo "<td class=".$colorc.">".$row[11]."</td>";
					echo "<td class=".$colorc.">".$row[12]."-".$row[16]."</td>";
					$diff = $entrada[$pos][1] - $row[11];
					echo "<td class=".$colorc.">".$diff."</td>";
					echo "<td class=".$colorl.">".$row[13]."-".$row[15]."</td>";
					echo "<td class=".$colorl.">".$row[14]."</td>";
					echo "</tr>";
				}
				echo "</table></center>";
			}
			else
			{
				echo "<table border=0 align=center id=tipo5>";
				echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;".$errores."</td></tr>";
				echo "</table></center>";
			}
		}
	}
?>
</body>
</html>
