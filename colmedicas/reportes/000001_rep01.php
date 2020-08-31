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
		

		include_once("root/montoescrito.php");
		

		echo "<form action='000001_rep01.php' method=post>";
		if(!isset($numero))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>CENTRO MEDICO LAS AMERICAS<b></td></tr>";
			echo "<tr><td align=center>RECIBO PROVISIONAL DE CAJA</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Digite el Numero del Recibo Provisional de Caja</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='numero' size=10 maxlength=10></td></tr></table>";
		}
		else
		{
			$query = "select Recnum, Recfec, Recpac, Recpcu, Recnit, Recvfr, Recvex, Recvco, Recvcm, Recvot, Recexo, Recvef, Recvch, Recnch, Recbch, Reccuc, Recvta, Recnut, Recent, Rectta, Recvbo, Recela, Recest  from colmedi_000007 where Recnum=".$numero." and Recest='on' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				echo "<table border=1>";
				echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg'  width=90%></td>";
				echo "<td colspan=4 bgcolor=#CCCCCC align=center><font size=5><b>PROMOTORA MEDICA LAS AMERICAS</b></font></td></tr>";
				echo "<tr><td colspan=4><font size=4 aling=center>NIT : 890.067.065-9</font><td></tr>";
				echo "<tr><td  colspan=4><font size=3>RECIBO PROVISIONAL DE CAJA</font></td></tr>";
				echo "<tr><td colspan=4>FECHA : ".$row[1]."</td><td align=right><b> NUMERO : ".$row[0]."</b></td></tr>";
				echo "<tr><td colspan=3>Recibido de : ".$row[3]."</td><td colspan=2>C.C./NIT : ".$row[4]."</td></tr>";
				$data=explode("-",$row[2]);
				echo "<tr><td colspan=3>Por Cuenta de : ".$data[2]." ".$data[3]." ".$data[4]." ".$data[5]."</td><td colspan=2>C.C./NIT :  ".$data[0]."</td></tr>";
				echo "<tr><td colspan=4 bgcolor=#cccccc><b>POR CONCEPTO DE : </b></td><td bgcolor=#cccccc align=right><b>VALOR</b></td></tr>";
				echo "<tr><td colspan=4>FRANQUICIA</td><td align=right>$".number_format((double)$row[5],2,'.',',')."</td></tr>";
				echo "<tr><td colspan=4>EXCEDENTES</td><td align=right>$".number_format((double)$row[6],2,'.',',')."</td></tr>";
				echo "<tr><td colspan=4>COPAGOS</td><td  align=right>$".number_format((double)$row[7],2,'.',',')."</td></tr>";
				echo "<tr><td colspan=4>CUOTAS MODERADORAS</td><td  align=right>$".number_format((double)$row[8],2,'.',',')."</td></tr>";
				echo "<tr><td colspan=4>OTROS CONCEPTOS</td><td align=right>$".number_format((double)$row[9],2,'.',',')."</td></tr>";
				echo "<tr><td colspan=5>".$row[10]."</td></tr>";
				$wtot=$row[5]+$row[6]+$row[7]+$row[8]+$row[9];
				echo "<tr><td colspan=4 bgcolor=#dddddd><b>TOTAL CONCEPTOS</b></td><td align=right bgcolor=#dddddd><b>$".number_format((double)$wtot,2,'.',',')."</b></td></tr>";
				echo "<tr><td colspan=5 bgcolor=#cccccc align=center><b>FORMA DE PAGO</b></td></tr>";
				echo "<tr><td bgcolor=#dddddd><b>Tipo</b></td><td bgcolor=#dddddd><b>Numero</b></td><td bgcolor=#dddddd><b>Entidad</b></td><td bgcolor=#dddddd><b>Cuenta</b></td><td bgcolor=#dddddd align=right><b>Valor</b></td></tr>";
				echo "<tr><td colspan=4>EFECTIVO</td><td align=right>$".number_format((double)$row[11],2,'.',',')."</td></tr>";
				if($row[12] > 0)
					echo "<tr><td>CHEQUE</td><td>".$row[13]."</td><td>".$row[14]."</td><td>".$row[15]."</td><td align=right>$".number_format((double)$row[12],2,'.',',')."</td></tr>";
				else
					echo "<tr><td>CHEQUE</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td align=right>$".number_format((double)$row[12],2,'.',',')."</td></tr>";
				if($row[16] > 0)
					echo "<tr><td>".$row[19]."</td><td>".$row[17]."</td><td>".$row[18]."</td><td>&nbsp </td><td align=right>$".number_format((double)$row[16],2,'.',',')."</td></tr>";
				else
					echo "<tr><td>TARJETAS</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp </td><td align=right>$".number_format((double)$row[16],2,'.',',')."</td></tr>";
				echo "<tr><td colspan=4>BONOS</td><td align=right>$".number_format((double)$row[20],2,'.',',')."</td></tr>";
				$wtot=$row[11]+$row[12]+$row[16]+$row[20];
				echo "<tr><td colspan=4 bgcolor=#dddddd><b>TOTAL</b></td><td bgcolor=#dddddd align=right><b>$".number_format((double)$wtot,2,'.',',')."</b></td></tr>";
				echo "<tr><td colspan=5 bgcolor=#dddddd>Valor en Letras : ".montoescrito($wtot)."</td></tr>";
				echo "<tr><td colspan=5>Elaborado por : ".$row[21]."</td></tr>";
				echo "</table>";		
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL RECIBO !!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
	}
?>
</body>
</html>