<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>DOCUMENTACION DE PROGRAMAS</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> DOC.php Ver. 2012-10-02</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	if(!isset($key))
		$key = substr($user,2,strlen($user));
	

	

	echo "<form action='DOC.php' method=post>";
	$file = fopen($files,"r");
	$wsw=0;
	while (!feof($file) and file_exists ($files))
	{
		$data=fgets($file,4096);
		if(substr($data,0,5) == "[DOC]")
			$wsw=1;
		if($wsw == 1 and substr($data,0,6)== "[*DOC]")
			$wsw=0;
		if($wsw == 1 and substr($data,0,5) != "[DOC]")
			echo $data."<br>";
	}
}
?>
</body>
</html>
