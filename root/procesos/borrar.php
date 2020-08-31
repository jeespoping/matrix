<html>
<head>
  <title>MATRIX</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body BGCOLOR="">
<font size=2>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

	session_start();
	if(!isset($_SESSION['user']))
		echo "Error Usuario NO Registrado";
	else
	{
	$key = substr($user,2,strlen($user));
	

	

	$datafile="./planos"; 
	$nombre="ptomea.csv";
	$manager=opendir($datafile);
	if(readdir($manager))
	{
		while($elemento = readdir($manager))
			if($elemento == $nombre)
			{
				echo "existe<br>";
				echo $datafile."/".$elemento."<br>";;
				unlink($datafile."/".$elemento);
			}
	}
	else
		echo "Error.";
   	}
?>
</font>
</body>
</html>