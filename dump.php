<html>
<head>
  <title>MATRIX</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body BGCOLOR="">
<font size=2>
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de Archivos Planos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> dump.php Ver. 3.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

	session_start();
	if(!isset($_SESSION['user']))
		echo "Error Usuario NO Registrado";
	else
	{
		$superglobals = array($_SESSION,$_REQUEST);
		foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
		{
			foreach ($valueSuperglobals as $variable => $dato)
			{
				$$variable = $dato; 
			}
		}
	if(!isset($key))
		$key = substr($user,2,strlen($user));
	echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	

	

	$datafile="./planos/".$key."_plano.txt"; 
	#echo $consulta;
	if(isset($consulta))
	{
		echo "<center><table border=1>";				
		echo "<tr><td align=center rowspan=4><IMG SRC='/MATRIX/images/medical/root/americas10.jpg' ></td>";				
		echo "<td align=center bgcolor=#dddddd><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td  bgcolor=#dddddd colspan=2  align=center><font size=4>GENERACION DE ARCHIVOS PLANOS</font></td></tr>";
		echo "<tr><td bgcolor=#dddddd  colspan=2  align=center><font size=2>CONSULTA : ".$consulta."</font></td></tr>";
		$consulta=stripslashes($consulta);
		$query = $consulta;
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error()."<br>");
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
		 	$file = fopen($datafile,"w+");
			 for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);	
				$fields=count($row)/2;
				$registro=$row[0];
				for ($j=1;$j<$fields;$j++)
				{
					$registro=$registro.",".$row[$j];
				}
				$registro=$registro.chr(13).chr(10);
  				 fwrite ($file,$registro);
  			}
   			fclose ($file);
   			$ruta="/MATRIX/planos/";
   			echo "<tr><td bgcolor=#dddddd  colspan=2 align=center><b><A href=".$ruta.">Haga Click Para Bajar el Archivo</A></b></td></tr></center>";
   		}
   	}
   	}
?>
</font>
</body>
</html>
