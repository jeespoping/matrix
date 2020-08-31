<html>
<head>
  <title>MATRIX Sticker de Articulos Unidad Visual Global de Coomeva</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='sticker_uvg.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wart) or !isset($wip) or !isset($wnum) or $wnum < 1 or $wnum > 30)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>STICKER DE ARTICULOS UNIDAD VISUAL GLOBAL DE COOMEVA Ver. 2008-08-01</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Codigo del Articulo</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wart' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Numero de Sticker</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=3 maxlength=3></td></tr>";
		echo "<tr><td bgcolor=#cccccc>IP Impresora</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wip' size=15 maxlength=15></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		$query = "SELECT  Artcod, Artnom  from ".$empresa."_000001  where Artest='on' and Artcod='".$wart."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wcod=$row[0];
			$wnom1=substr($row[1],0,20);
			$wnom2=substr($row[1],20,20);
			$longcb=strlen($row[0]);
			$lin=(integer)($wnum / 3);
			if(($lin * 3) < $wnum)
				$lin++;
			$paquete="";
			$paquete=$paquete."N".chr(13).chr(10);
			$paquete=$paquete."FK".chr(34)."UVGLOBAL".chr(34).chr(13).chr(10);
			$paquete=$paquete."FS".chr(34)."UVGLOBAL".chr(34).chr(13).chr(10);; 
			$paquete=$paquete."V00,".$longcb.",L,".chr(34)."CODIGO".chr(34).chr(13).chr(10);
			$paquete=$paquete."V01,20,L,".chr(34)."NOMBRE1".chr(34).chr(13).chr(10);
			$paquete=$paquete."V02,20,L,".chr(34)."NOMBRE2".chr(34).chr(13).chr(10);
			$paquete=$paquete."q750".chr(13).chr(10);
			$paquete=$paquete."S3".chr(13).chr(10);
			$paquete=$paquete."D4".chr(13).chr(10);
			$paquete=$paquete."ZT".chr(13).chr(10);
			$paquete=$paquete."TTh:m".chr(13).chr(10);
			$paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);
			$paquete=$paquete."B28,2,0,1,1,2,50,N,V00".chr(13).chr(10);
			$paquete=$paquete."A28,55,0,2,1,1,N,V00".chr(13).chr(10);
			$paquete=$paquete."A3,75,0,1,1,1,N,V01".chr(13).chr(10);
			$paquete=$paquete."A3,90,0,1,1,1,N,V02".chr(13).chr(10);
			$paquete=$paquete."B290,2,0,1,1,2,50,N,V00".chr(13).chr(10);
			$paquete=$paquete."A290,55,0,2,1,1,N,V00".chr(13).chr(10);
			$paquete=$paquete."A265,75,0,1,1,1,N,V01".chr(13).chr(10);
			$paquete=$paquete."A265,90,0,1,1,1,N,V02".chr(13).chr(10);
			$paquete=$paquete."B560,2,0,1,1,2,50,N,V00".chr(13).chr(10);
			$paquete=$paquete."A560,55,0,2,1,1,N,V00".chr(13).chr(10);
			$paquete=$paquete."A535,75,0,1,1,1,N,V01".chr(13).chr(10);
			$paquete=$paquete."A535,90,0,1,1,1,N,V02".chr(13).chr(10);
			$paquete=$paquete."FE".chr(13).chr(10);
			$paquete=$paquete.".".chr(13).chr(10);
			$paquete=$paquete."FR".chr(34)."UVGLOBAL".chr(34).chr(13).chr(10);
			$paquete=$paquete."?".chr(13).chr(10);
			$paquete=$paquete.$wcod.chr(13).chr(10);
			$paquete=$paquete.$wnom1.chr(13).chr(10);
			$paquete=$paquete.$wnom2.chr(13).chr(10);
			$paquete=$paquete."P".$lin.chr(13).chr(10);
			$paquete=$paquete.".".chr(13).chr(10);
			echo $paquete."<br>";
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
}
?>
</body>
</html>