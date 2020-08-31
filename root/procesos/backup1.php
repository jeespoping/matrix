<html>
<head>
  <title>MATRIX</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body BGCOLOR="">
<font size=2>
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Archivos Para Backup</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> backup.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
	echo "<center><table border=1>";				
	echo "<tr><td align=center rowspan=4><IMG SRC='/MATRIX/images/medical/root/americas10.jpg' ></td>";				
	echo "<td align=center bgcolor=#dddddd><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
	echo "<tr><td  bgcolor=#dddddd colspan=2  align=center><font size=4>GENERACION DE ARCHIVO PARA BACKUP</font></td></tr>";
	

	

	$datafile="./planos/backup.bat"; 
	$query = "show tables ";
	$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$wsw=0;
	 	$file = fopen($datafile,"w+");
	 	$nf=0;
	 	$c=0;
	 	$registro="c:\mysql\bin\mysqldump -u root -e matrix "
		 for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);	
			if ($nf < 10)
			{
				$nf++;
				$registro=$registro." ".$row[0];
			}
			else
			{
				$nf=0;
				if($wsws == 0)
					$registro=$registro." > matrix1.txt".chr(13).chr(10);
				else
					$registro=$registro." >> matrix1.txt".chr(13).chr(10);
				 fwrite ($file,$registro);
				 $registro="c:\mysql\bin\mysqldump -u root -e matrix "
				 $c++;
				 echo "REGISTRO GRABADO : ".$c."<br>";
			}
		}
		fclose ($file);	
	}
?>
</font>
</body>
</html>