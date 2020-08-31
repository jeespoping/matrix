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
		

		

		echo "<form action='biensur_000001_rep6.php' method=post>";
		if(!isset($numero))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>CLINICA DEL SUR S.A.<b></td></tr>";
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
				echo "<tr><td rowspan=4><IMG SRC='/MATRIX/images/medical/pos/logo_clisur.png' ></td>";
				echo "<td colspan=2><font size=5>CLINICA DEL SUR S.A.</font></td></tr>";
				echo "<tr><td colspan=2><font size=4 aling=center>NIT : 890.939.026-1</font><td></tr>";
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
				$query = "select * from ".$pos1."_000013 where medico='".$pos1."' and consecutivo='".$row[3]."-".$row[5]."' order by porcentaje";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$totpor=0;
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$parcial=(double)$row[15]*((double)$row1[5]/100);
					$totpor=$totpor+$row1[5];
					echo "<tr><td>".$row1[4]."</td><td align=center>".$row1[5]."</td><td align=right>".number_format($parcial,2,'.',',')."</td></tr>";
				}
				echo "<tr><td colspan=3>Observaciones: ".$row[16]."</td></tr>";
				$blanco=".";
				echo "<tr><td><font size=20>".$blanco."</font></td><td><font size=20>".$blanco."</font></td><td><font size=20>".$blanco."</font></td></tr>";
				echo "<tr><td><b>Firma Proveedor</b></td><td><b>Elaborado Por</b></td><td><b>Recibido Por</b></td></tr>";
				if($totpor !=100)
					echo "<tr><td colspan=3 bgcolor=#ff0000><b>ERROR : LA DISTRIBUCION DE PROCENTAJES NO SUMA EL 100%</b></td></tr>";
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