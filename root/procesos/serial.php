<?php
include_once("conex.php");
`mode com1: baud=9600 PARITY=N data=8 stop=1 xon=off`;
$fp= fopen("COM1: ","w+");
if (!$fp)
	echo "ERROR AL ABRIR PUERTO";
else
	{
		echo "PUERTO ABIERTO 9600,N,8,1,off";
		$c = fgetc($fp);
		$line="";
 		while(isset($c))
 		{
	 		$line=$line.$c;
	 		echo $line."<br>";
	 		$c = fgetc($fp);
 		}
 	}
?>