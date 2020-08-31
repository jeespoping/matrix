<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rep6.php' method=post>";
		if(!isset($numero))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>FUNDACION LAS AMERICAS<b></td></tr>";
			echo "<tr><td align=center>COMPROBANTE DE COMPRA DE BIENES O SERVICIOS</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Digite el Numero del Comprobante a Imprimir</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='numero' size=10 maxlength=10></td></tr></table>";
			echo "<input type='HIDDEN' name= 'pos1' value='".$pos1."'>";
			echo "<input type='HIDDEN' name= 'pos2' value='".$pos2."'>";
		}
		else
		{
			$query = "select * from ".$pos1."_".$pos2." where medico='".$pos1."'  and consecutivo=".$numero;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				echo "<table border=1>";
				echo "<tr><td rowspan=4 align=center><IMG SRC='/MATRIX/images/medical/root/fundacion.png'  width=60%></td>";
				echo "<td colspan=2 bgcolor=#dddddd align=center><font size=5><b>FUNDACION LAS AMERICAS</b></font></td></tr>";
				echo "<tr><td colspan=2><font size=4 aling=center>NIT : 800.180.326-9</font><td></tr>";
				echo "<tr><td  colspan=2><font size=3>COMPROBANTE DE COMPRA DE BIENES O SERVICIOS</font></td></tr>";
				echo "<tr><td colspan=2 align=center><font size=1><b>PERSONAS NATURALES NO COMERCIANTES O INSCRITOS EN EL REGIMEN SIMPLIFICADO</b></font></td></tr>";
				echo "<tr><td><b>COMPROBANTE NRO. ".$row[3]."</b></td>";
				echo "<td align=right colspan=2>Fecha de Operacion.".$row[4]."</td></tr>";
				echo "<tr><td>Identificacion: ".$row[5]."</td>";
				echo "<td  colspan=2 align=right>Telefono: ".$row[9]."</td></tr>";
				echo "<tr><td colspan=3>Apellidos y Nombres: ".$row[6]."</td></tr>";
				echo "<tr><td>Direccion: ".$row[7]."</td>";
				echo "<td  colspan=2 align=right>Ciudad: ".$row[8]."</td></tr>";
				echo "<tr><td colspan=3>Concepto: ".$row[10]."</td></tr>";
				echo "<tr><td>Valor  : </td>";
				echo "<td  colspan=2 align=right>".number_format((double)$row[11],2,'.',',')."</td></tr>";
				echo "<tr><td>Impuesto Asumido : </td>";
				echo "<td  colspan=2 align=right>".number_format((double)$row[14],2,'.',',')."</td></tr>";
				echo "<tr><td>Total : </td>";
				echo "<td  colspan=2 align=right>".number_format((double)$row[15],2,'.',',')."</td></tr>";
				echo "<tr><td><b>Centro de Costos</b></td><td align=center><b>Porcentaje</b></td><td align=right><b>Valor</b></td></tr>";
				echo "<tr><td>General</td><td align=center>100 %</td><td align=right>".number_format($row[15],2,'.',',')."</td></tr>";
				echo "<tr><td colspan=3>Observaciones: ".$row[16]."</td></tr>";
				echo "<tr><td><font size=20>&nbsp </font></td><td><font size=20>&nbsp </font></td><td><font size=20>&nbsp </font></td></tr>";
				echo "<tr><td><b>Firma Proveedor</b></td><td><b>Elaborado Por</b></td><td><b>Recibido Por</b></td></tr>";
				echo "<tr><td colspan=3><b>Elaborado por : ".$key."</b></td></tr>";
				echo "</table>";		
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL COMPROBANTE !!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
	}
?>
</body>
</html>