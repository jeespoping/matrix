<?php
include_once("conex.php");
	$conex=odbc_connect('nomina','','')
			or die("No se realizo Conexion Error : ".$conex);
	echo "<table border=1 align=center cellpadding=3>";
	$query="select percod,perap1,perap2,perno1,perno2,perfin from noper where peretr = 'A' ";
	$query=$query." order by perfin";
	$err=odbc_do($conex,$query);
	$num=odbc_num_fields($err);
	$cont = 0;
	while(odbc_fetch_row($err) and $cont < 101)
	{
		echo "<tr>";
		$cont++;
		for($i=1;$i<=$num;$i++)
		{
			echo"<td>". odbc_result($err,$i)."</td>";
		}
		echo "</tr>";
	}
	echo"</table>";
	
	odbc_close($conex);
	odbc_close_all();
?> 