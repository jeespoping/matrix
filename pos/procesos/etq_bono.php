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
  	

	

  	echo "<form action='etq_bono.php' method=post>";
  	if(!isset($wco1) or !isset($wco2) or !isset($wfev) or !isset($wip) or $wco1 > $wco2)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>FARMASTORE</td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE STIKERS DE CODIGOS DE BARRAS BONOS DE REGALO</td></tr>";
		echo "<tr><td bgcolor=#cccccc>Consecutivo Inicial</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wco1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Consecutivo Final</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wco2' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Fecha de Vencimiento</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wfev' size=10 maxlength=10></td></tr>";	
		echo "<tr><td bgcolor=#cccccc>Numero de IP</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='wip' size=15 maxlength=15></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		for ($i=$wco1;$i<=$wco2;$i++)
		{
			$num=$i;
			while(strlen($num) < 6)
				$num = "0".$num;
			$longcb=strlen($num);
			$paquete="";
			$paquete=$paquete."N".chr(13).chr(10);
			$paquete=$paquete."FK".chr(34)."CENBON".chr(34).chr(13).chr(10);
			$paquete=$paquete."FS".chr(34)."CENBON".chr(34).chr(13).chr(10);; 
			$paquete=$paquete."V00,".$longcb.",L,".chr(34)."CODIGO".chr(34).chr(13).chr(10);
			$paquete=$paquete."V01,23,L,".chr(34)."FECVEN".chr(34).chr(13).chr(10);
			$paquete=$paquete."q650".chr(13).chr(10);
			$paquete=$paquete."S3".chr(13).chr(10);
			$paquete=$paquete."D4".chr(13).chr(10);
			$paquete=$paquete."ZT".chr(13).chr(10);
			$paquete=$paquete."TTh:m".chr(13).chr(10);
			$paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);
			$paquete=$paquete."A230,10,0,2,1,1,N,".chr(34)."BONO DE REGALO".chr(34).chr(13).chr(10);
			$paquete=$paquete."B230,40,0,1,2,5,70,N,V00".chr(13).chr(10);
			$paquete=$paquete."A230,120,0,2,1,1,N,V00".chr(13).chr(10);
			$paquete=$paquete."A230,145,0,2,1,1,N,V01".chr(13).chr(10);
			$paquete=$paquete."FE".chr(13).chr(10);
			$paquete=$paquete.".".chr(13).chr(10);
			$paquete=$paquete."FR".chr(34)."CENBON".chr(34).chr(13).chr(10);
			$paquete=$paquete."?".chr(13).chr(10);
			$paquete=$paquete.$num.chr(13).chr(10);
			$paquete=$paquete."F.V.: ".$wfev.chr(13).chr(10);
			$paquete=$paquete."P1".chr(13).chr(10);
			$paquete=$paquete.".".chr(13).chr(10);
			$addr=$wip;
			$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
			if(!$fp) 
			echo "ERROR : "."$errstr ($errno)<br>\n";
			else 
			{
			fputs($fp,$paquete);
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