<html>
<head>
  <title>MATRIX</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body BGCOLOR="">
<font size=2>
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Archivos Para Repair y Optimize</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> repopt.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
	

	

	$datafile="./repopt.txt"; 
	$query = "show tables ";
	$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$wsw=0;
	 	$file = fopen($datafile,"w+");
	 	$nf=0;
	 	$nt=0;
	 	$c=0;
	 	$registro1="repair table ";
	 	$registro2="optimize table ";
		 for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);	
			if ($nf == 5)
			{
				$nf=0;
				$registro1=$registro1.";".chr(13).chr(10);
				$registro2=$registro2.";".chr(13).chr(10);
				fwrite ($file,$registro1);
				fwrite ($file,$registro2);
				$c++;
				echo "REGISTRO GRABADO : ".$c."<br>";
				$c++;
				echo "REGISTRO GRABADO : ".$c."<br>";
				$registro1="repair table ";
	 			$registro2="optimize table ";
			}
			$nf++;
			$nt++;
			if($nf == 1)
			{
				$registro1=$registro1." ".$row[0];
				$registro2=$registro2." ".$row[0];
			}
			else
			{
				$registro1=$registro1.",".$row[0];
				$registro2=$registro2.",".$row[0];
			}
		}
		$registro1=$registro1.";".chr(13).chr(10);
		$registro2=$registro2.";".chr(13).chr(10);
		fwrite ($file,$registro1);
		fwrite ($file,$registro2);
		$c++;
		echo "REGISTRO GRABADO : ".$c."<br>";
		$c++;
		echo "REGISTRO GRABADO : ".$c."<br>";
		echo "<br><b>Nro de Tablas Examinadas : ".$nt."  -- ".date("Y-m-d")."</b><br>";	
		fclose ($file);	
	}
?>
</font>
</body>
</html>