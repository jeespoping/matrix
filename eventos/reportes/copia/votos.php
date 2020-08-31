<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='votos.php' method=post>";
		if(!isset($ci) or !isset($cf) or !isset($tip) or $tip < 1 or $tip > 3)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>IMPRESION DE VOTOS ASAMBLEAS DE PROMOTORA MEDICA LAS AMERICAS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Cedula Inicial</td><td bgcolor=#cccccc align=center><input type='TEXT' name='ci' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Cedula Final</td><td bgcolor=#cccccc align=center><input type='TEXT' name='cf' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo</td><td bgcolor=#cccccc align=center><input type='TEXT' name='tip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
		}
		else
		{
			$conex_o = odbc_connect('cajban','','');
			$query = "select socced, socnom, soctot from ejdsoc where socced between '".$ci."' and '".$cf."' order by 2";
			$err_o = odbc_do($conex_o,$query);
			$campos= odbc_num_fields($err_o);
			$count=0;
			$k=0;
			$soc=0;
			while (odbc_fetch_row($err_o))
			{
				$soc++;
				$odbc=array();
				for($m=1;$m<=$campos;$m++)
				{
					$odbc[$m-1]=odbc_result($err_o,$m);
				}
				$k++;
				echo "<center><table border=0>";
				echo "<tr><td align=center colspan=3><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>ASAMBLEA GENERAL ORDINARIA</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>Marzo 28 de 2006</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center><b>SOCIO</b></td><td align=center><b>CEDULA</b></td><td align=center><b># DE ACCIONES</b></td></tr>";
				echo "<tr><td align=center bgcolor=#dddddd>".$odbc[1]."</td><td align=center bgcolor=#dddddd>".$odbc[0]."</td><td align=center bgcolor=#dddddd>".number_format((double)$odbc[2],0,'.',',')."</td></tr>";
				echo "<tr><td align=center colspan=3><font size=13 face='3 of 9 barcode'>*".$odbc[0]."*</font></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				switch ($tip)
				{
					case 1:
						echo "<tr><td align=left colspan=2><font size=5>Eleccion Junta Directiva Periodo 2006 - 2008</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbspPlancha Nro. [&nbsp&nbsp&nbsp&nbsp]</font></td></tr>";
					break;
					case 2:
						echo "<tr><td align=left colspan=2><font size=5>Propuesta Nro. 1</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbsp [SI]&nbsp&nbsp[NO]</font></td></tr>";
					break;
					case 3:
						echo "<tr><td align=left colspan=2><font size=5>Propuesta Nro. 2</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbsp [SI]&nbsp&nbsp[NO]</font></td></tr>";
					break;
				}
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>------------------------------------------------------------</b></td></tr>";
				echo "<tr><td align=center colspan=3><b>FIRMA</b></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center colspan=3><b>&nbsp</b></td></tr></table>";
				if($k%2 == 0)
				{
					echo "</div>";
					echo "<div style='page-break-before: always'>";	
				}
				else
				{
					echo "<br><br><br>";
					echo "----------------------------------------------------------------------------------------------------------------------------------------------------------<br><br><br><br>";
				}
			}
			echo "<div style='page-break-before: always'>";
			echo "<br><br><br>Nro de Votos Impresos : ".$soc."<br>";
		}
	}
?>
</body>
</html>