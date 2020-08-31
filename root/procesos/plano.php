<html>
<head>
  <title>MATRIX</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body BGCOLOR="" background="bg2.jpg">
<font size=2>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

	session_start();
	if(!isset($_SESSION['user']))
		echo "Error Usuario NO Registrado";
	else
	{
	

	

	$datafile="plano.txt"; 
	$query = "select codigo,cedula from nomina_000001 order by codigo";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		 $file = fopen($datafile,"w+");
		 for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);	
			$registro=$row[0].",".substr($row[1],strlen($row[1])-4,strlen($row[1])).",0,A".chr(13).chr(10);
  			 fwrite ($file,$registro);
  		}
   		fclose ($file);
   		echo "<A href=".$datafile.">archivo</A>";
   	}
   	}
?>
</font>
</body>
</html>