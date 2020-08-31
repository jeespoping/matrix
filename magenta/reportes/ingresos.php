<html>
<head>
<title>PERSONA</title>
</head>
<body >
<?php
include_once("conex.php");
if(!isset($cc)){
	echo "<form name='forma' action='' method='POST'>";
	echo "<input type='text' name='cc' ><input type='submit' name='volver' value='ACEPTAR'></form>";

}else{
	$conex_o = odbc_connect('facturacion','','');
	$q="select pachis, pactid,pacced, pachis, pacnom,pacap1, pacap2 "
	."from inpac "
	."where	pacced = '".$cc."' "
	."and 	pactid = 'CC' ";
	$err_o=odbc_do($conex_o,$q);
	if(odbc_fetch_row($err_o))	{
		$hist=odbc_result($err_o,1);
		echo "Hitoria:".odbc_result($err_o,1)."<br>";
		echo "Documento:".odbc_result($err_o,2)."-".odbc_result($err_o,3)."<br>";
		echo "Nombre complero:".odbc_result($err_o,4)." ".odbc_result($err_o,5)." ".odbc_result($err_o,6)." "."<br>";


	}else{
		$q="select pachis, pactid,pacced, pachis, pacnom,pacap1, pacap2 "
		."from inpaci "
		."where	pacced = '".$cc."' "
		."and 	pactid = 'CC' ";
		$err_o=odbc_do($conex_o,$q);
		if(odbc_fetch_row($err_o))	{
			$hist=odbc_result($err_o,1);
			echo "Hitoria:".odbc_result($err_o,1)."<br>";
			echo "Documento:".odbc_result($err_o,2)."-".odbc_result($err_o,3)."<br>";
			echo "Nombre complero:".odbc_result($err_o,4)." ".odbc_result($err_o,5)." ".odbc_result($err_o,6)." "."<br>";
		}
	}
	$q="select egrnum,  egring, egregr "
	."from inmegr "
	."where	egrhis = '".$hist."' ";
	$err_o=odbc_do($conex_o,$q);
	echo "<table border=1><tr><td>Ingreso</td><td>Fecha Ing</td><td>Fecha Egr</td>";
	While (odbc_fetch_row($err_o))
	{
		echo "<tr><td>".odbc_result($err_o,1)."</td><td>".odbc_result($err_o,2)."</td><td>".odbc_result($err_o,3)."</td>";
	}
	
	odbc_close($conex_o);
	odbc_close_all();

}
?>
</body>
</html>