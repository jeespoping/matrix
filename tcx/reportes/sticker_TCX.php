<html>
<head>
  <title>MATRIX Sticker Para Programa de Gestion en Cirugia</title>
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
	echo "<form action='sticker_TCX.php' method=post>";
	

	

	if(!isset($wnci) or !isset($wno1) or !isset($wno2))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>STICKER PARA PROGRAMA DE GESTION EN CIRUGIA Ver. 2010-02-26</b></td></tr>";
		if(isset($wnci))
		{
			echo "<tr><td bgcolor=#cccccc>Numero de Cirugia</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wnci' value='".$wnci."' size=12 maxlength=12></td></tr>";
			$query  = "select Turnom from tcx_000011 ";
			$query .= " where Turtur = ".$wnci;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				echo "<tr><td bgcolor=#cccccc>Nombre Paciente</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wno1' value='".substr($row[0],0,20)."' size=30 maxlength=20></td></tr>";
				echo "<tr><td bgcolor=#cccccc></td><td bgcolor=#cccccc align=center><input type='TEXT' name='wno2' value='".substr($row[0],20)."' size=30 maxlength=20></td></tr>";
			}
		}
		else
			echo "<tr><td bgcolor=#cccccc>Numero de Cirugia</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wnci' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc>IP Impresora</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wip' value='10.15.14.6' size=15 maxlength=15></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		$query  = "select Turnom from tcx_000011 ";
		$query .= " where Turtur = ".$wnci;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$longC=strlen($wnci);
			$pos=190 * (2 - ($longC / 10));
			$wnet=1;
			if($longC < 11)
			{
				echo "ETIQUETA NRo : ".$wnci."<br>";
				$paquete="";
				$paquete=$paquete."N".chr(13).chr(10);
				$paquete=$paquete."FK".chr(34)."TCX".chr(34).chr(13).chr(10);
				$paquete=$paquete."FS".chr(34)."TCX".chr(34).chr(13).chr(10);; 
				$paquete=$paquete."V00,".$longC.",L,".chr(34)."NUMERO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V01,20,L,".chr(34)."NOM1".chr(34).chr(13).chr(10);
				$paquete=$paquete."V02,20,L,".chr(34)."NOM2".chr(34).chr(13).chr(10);
				$paquete=$paquete."q750".chr(13).chr(10);
				$paquete=$paquete."S3".chr(13).chr(10);
				$paquete=$paquete."D4".chr(13).chr(10);
				$paquete=$paquete."ZT".chr(13).chr(10);
				$paquete=$paquete."TTh:m".chr(13).chr(10);
				$paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);
				$paquete=$paquete."A240,20,0,3,1,1,N,".chr(34)."CLINICA LAS AMERICAS".chr(34).chr(13).chr(10);
				$paquete=$paquete."A245,40,0,3,1,1,N,".chr(34)."PROGRAMA DE GESTION".chr(34).chr(13).chr(10);
				$paquete=$paquete."A295,60,0,3,1,1,N,".chr(34)."EN CIRUGIA".chr(34).chr(13).chr(10);
				$paquete=$paquete."A280,85,0,4,1,1,N,".chr(34)."CIRUGIA NRO:".chr(34).chr(13).chr(10);
				$paquete=$paquete."A".$pos.",120,0,5,1,1,N,V00".chr(13).chr(10);
				$paquete=$paquete."A200,180,0,4,1,1,N,V01".chr(13).chr(10);
				$paquete=$paquete."A200,220,0,4,1,1,N,V02".chr(13).chr(10);
				$paquete=$paquete."FE".chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				$paquete=$paquete."FR".chr(34)."TCX".chr(34).chr(13).chr(10);
				$paquete=$paquete."?".chr(13).chr(10);
				$paquete=$paquete.$wnci.chr(13).chr(10);
				$paquete=$paquete.$wno1.chr(13).chr(10);
				$paquete=$paquete.$wno2.chr(13).chr(10);
				$paquete=$paquete."P".$wnet.chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				//echo $paquete."<br>";
				$addr=$wip;
				$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
				if(!$fp) 
				echo "ERROR : "."$errstr ($errno)<br>\n";
				else 
				{
					fputs($fp,$paquete);
					echo "PAQUETE ENVIADO <br>\n";
					
				}
				sleep(2);
				fclose($fp);
			}
			else
				echo "LA LONGITUD MAXIMA ES DE 10 DIGITOS <br>\n";
		}
	}
}
?>
</body>
</html>