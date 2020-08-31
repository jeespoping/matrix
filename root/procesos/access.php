<?php
include_once("conex.php");
	$conex=odbc_connect('data','root','sco')
			or die("No se ralizo Conexion Error : ".$conex);
	$query="select * from tabla1 ";
	$err=odbc_do($conex,$query);
	echo "<table border=1 align=center cellpadding=3>";
	echo "<tr><td>DATO 1</td><td>DATO 2</td><td>DATO 3</td></tr>";
	while(odbc_fetch_row($err))
	{
		echo "<tr><td  align=center>".odbc_result($err,1)."</td><td  align=center>".odbc_result($err,2)."</td><td  align=center>".odbc_result($err,3)."</td></tr>";
	}
	echo "</table>";
		
	odbc_close($conex);
	odbc_close_all();
?> 