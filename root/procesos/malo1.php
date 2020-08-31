<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion Maestro de Activos Fijos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> malo.php Ver. 1.01</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

	echo "<form action='malo1.php' method=post>";
	if(!isset($wanop))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION MAESTRO DE ACTIVOS FIJOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		

		$conex_o = odbc_connect('facturacion','','');
		//                 1       2     3       4     5       6      7     8      9    
		echo $query = "SELECT artcod, artnom, artreg FROM ivgru,ivart WHERE grucod = artgru";
		$err_o = odbc_exec($conex_o,$query);
		
		$count=0;
				
			while ($row = odbc_fetch_array($err_o))
			{
				odbc_errormsg($conex_o);
				$cnt = $row['artcod'];
				$cnt1 = $row['artnom'];
				$cnt2 = $row['artreg'];
				echo $cnt."<br>";
				$count++;
				echo "REGISTROS ADICIONADO NRO : ".$count."<br>";
			}
		
			odbc_errormsg($conex_o);
			echo "<B>REGISTROS ADICIONADOS : ".$count."</B>";
		
		odbc_close($conex_o);
		odbc_close_all();
				
	}

?>
</body>
</html>
