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
  	echo "<form action='star.php' method=post>";
	$paquete="";
	$paquete=$paquete.chr(27)."R0".chr(10);
	$paquete=$paquete.chr(27)."b,4,1,1,20,365467".chr(10).";
	echo "Hola";
	$addr="132.1.18.236";
	$fp = fsockopen( $addr,80, $errno, $errstr, 30);
	if(!$fp) 
		echo "ERROR : "."$errstr ($errno)<br>\n";
	else 
	{
		fputs($fp,$paquete);
		fclose($fp);
	}
	sleep(5);
}
?>
</body>
</html>