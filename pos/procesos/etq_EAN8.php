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
  	echo "<form action='etq_EAN8.php' method=post>";
  	if(!isset($wcod) or !isset($wetq) or !isset($wip) or !isset($wnom1) or !isset($wnom1))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>FARMASTORE</td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE STIKERS DE CODIGOS DE BARRAS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Codigo del Producto</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcod' size=8 maxlength=8></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nombre del Producto(1)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnom1' size=23 maxlength=23></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nombre del Producto(2)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnom2' size=23 maxlength=23></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Numero de Etiquetas</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wetq' size=6 maxlength=6></td></tr>";	
		echo "<tr><td bgcolor=#cccccc>Numero de IP</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wip' size=15 maxlength=15></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wcod="0".$wcod;
		$longcb=strlen($wcod);
		$paquete="";
		$paquete=$paquete."N".chr(13).chr(10);
		$paquete=$paquete."FK".chr(34)."CENPRO".chr(34).chr(13).chr(10);
		$paquete=$paquete."FS".chr(34)."CENPRO".chr(34).chr(13).chr(10);; 
		$paquete=$paquete."V00,".$longcb.",L,".chr(34)."CODIGO".chr(34).chr(13).chr(10);
		$paquete=$paquete."V01,23,L,".chr(34)."NOMBRE1".chr(34).chr(13).chr(10);
		$paquete=$paquete."V02,23,L,".chr(34)."NOMBRE2".chr(34).chr(13).chr(10);
		$paquete=$paquete."q650".chr(13).chr(10);
		$paquete=$paquete."S3".chr(13).chr(10);
		$paquete=$paquete."D4".chr(13).chr(10);
		$paquete=$paquete."ZT".chr(13).chr(10);
		$paquete=$paquete."TTh:m".chr(13).chr(10);
		$paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);
		$paquete=$paquete."B230,10,0,E80,2,5,70,N,V00".chr(13).chr(10);
		$paquete=$paquete."A260,85,0,3,1,1,N,V00".chr(13).chr(10);
		$paquete=$paquete."A220,125,0,1,1,1,N,V01".chr(13).chr(10);
		$paquete=$paquete."A220,145,0,1,1,1,N,V02".chr(13).chr(10);
		$paquete=$paquete."FE".chr(13).chr(10);
		$paquete=$paquete.".".chr(13).chr(10);
		$paquete=$paquete."FR".chr(34)."CENPRO".chr(34).chr(13).chr(10);
		$paquete=$paquete."?".chr(13).chr(10);
		$paquete=$paquete.$wcod.chr(13).chr(10);
		$paquete=$paquete.$wnom1.chr(13).chr(10);
		$paquete=$paquete.$wnom2.chr(13).chr(10);
		$paquete=$paquete."P".$wetq.chr(13).chr(10);
		$paquete=$paquete.".".chr(13).chr(10);
		$addr=$wip;
		$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
		if(!$fp) 
		echo "ERROR : "."$errstr ($errno)<br>\n";
		else 
		{
			fputs($fp,$paquete);
			#echo "PAQUETE ENVIADO $errstr ($errno)<br>\n";
			echo "PAQUETE ENVIADO <br>\n";
			fclose($fp);
		}
		sleep(5);
	}
}
?>
</body>
</html>