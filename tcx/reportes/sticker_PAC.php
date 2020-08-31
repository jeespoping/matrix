<html>
<head>
  <title>MATRIX Sticker De Pacientes</title>
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
	echo "<form action='sticker_PAC.php' method=post>";
	

	

	if(!isset($whis) or !isset($wnum) or $wnum < 1 or $wnum > 9  or !isset($wori) or !isset($wip))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>STICKER DE PACIENTES Ver. 2009-11-25</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Numero De Historia</td><td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Numero De Etiquetas</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Origen De La Etiqueta</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wori' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc>IP Impresora</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wip' value='10.15.14.6' size=15 maxlength=15></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		//                  0        1       2       3       4       5       6       7       8       9      10      11
		$query  = "select pachis, pacnom, pacap1, pacap2, pacnac, pacres, pacsex, pactar, pacnum, pactid, pacced, pactse from inpac ";
		$query .= " where pachis = ".$whis;
		$conex_o = odbc_connect('admisiones','','');
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$count=0;
		if(odbc_fetch_row($err_o))
		{
			$count++;
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}
			$ann=(integer)substr($odbc[4],0,4)*360 +(integer)substr($odbc[4],5,2)*30 + (integer)substr($odbc[4],8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$EDAD=(integer)($aa - $ann)/360;
			$NOM=trim(substr($odbc[2],0,13)).chr(32).trim(substr($odbc[3],0,13)).chr(32).trim(substr($odbc[1],0,12));
			$longC=strlen($odbc[0]);
			if($longC < 12)
			{
				echo "HISTORIA NRo : ".$whis."<br>";
				$paquete="";
				$paquete=$paquete."N".chr(13).chr(10);
				$paquete=$paquete."FK".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."FS".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V00,".$longC.",L,".chr(34)."HISTORIA".chr(34).chr(13).chr(10);
				$paquete=$paquete."V01,38,L,".chr(34)."NOMBRE".chr(34).chr(13).chr(10);
				$paquete=$paquete."V02,2,L,".chr(34)."TARIFA".chr(34).chr(13).chr(10);
				$paquete=$paquete."V03,27,L,".chr(34)."EPS".chr(34).chr(13).chr(10);
				$paquete=$paquete."V04,15,L,".chr(34)."CC".chr(34).chr(13).chr(10);
				$paquete=$paquete."V05,3,L,".chr(34)."EDAD".chr(34).chr(13).chr(10);
				$paquete=$paquete."V06,1,L,".chr(34)."SEXO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V07,3,L,".chr(34)."INGRESO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V08,10,L,".chr(34)."ORIGEN".chr(34).chr(13).chr(10);
				$paquete=$paquete."V09,1,L,".chr(34)."TIPOS".chr(34).chr(13).chr(10);
				//$paquete=$paquete."Q304,24".chr(13).chr(10);
				$paquete=$paquete."Q265,24".chr(13).chr(10);
				$paquete=$paquete."q400".chr(13).chr(10);
				$paquete=$paquete."B120,10,0,1,2,6,70,N,V00".chr(13).chr(10);
				$paquete=$paquete."A90,90,0,3,1,1,N,".chr(34)."HISTORIA:".chr(34)."V00".chr(13).chr(10);
				$paquete=$paquete."A10,115,0,2,1,1,N,V01".chr(13).chr(10);
				$paquete=$paquete."A60,135,0,2,1,1,N,".chr(34)."AGE:".chr(34)."V05".chr(13).chr(10);
				$paquete=$paquete."A165,135,0,2,1,1,N,".chr(34)."SEX:".chr(34)."V06".chr(13).chr(10);
				$paquete=$paquete."A60,155,0,2,1,1,N,".chr(34)."ING:".chr(34)."V07".chr(13).chr(10);
				$paquete=$paquete."A165,155,0,2,1,1,N,".chr(34)."TAR:".chr(34)."V02".chr(13).chr(10);
				$paquete=$paquete."B285,140,0,1,2,4,90,N,V02".chr(13).chr(10);
				$paquete=$paquete."A10,180,0,1,1,1,N,V03".chr(13).chr(10);
				$paquete=$paquete."A10,200,0,2,1,1,N,V04".chr(13).chr(10);
				$paquete=$paquete."A10,220,0,4,1,1,N,".chr(34)."ORIGEN:".chr(34)."V08".chr(13).chr(10);
				$paquete=$paquete."A285,240,0,1,1,1,N,".chr(34)."TSE:".chr(34)."V09".chr(13).chr(10);
				$paquete=$paquete."FE".chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				$paquete=$paquete."FR".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."?".chr(13).chr(10);
				$paquete=$paquete.$odbc[0].chr(13).chr(10);
				$paquete=$paquete.$NOM.chr(13).chr(10);
				$paquete=$paquete.$odbc[7].chr(13).chr(10);
				$paquete=$paquete.substr($odbc[5],0,27).chr(13).chr(10);
				$paquete=$paquete.$odbc[9].":".$odbc[10].chr(13).chr(10);
				$paquete=$paquete.$EDAD.chr(13).chr(10);
				$paquete=$paquete.$odbc[6].chr(13).chr(10);
				$paquete=$paquete.$odbc[8].chr(13).chr(10);
				$paquete=$paquete.$wori.chr(13).chr(10);
				$paquete=$paquete.$odbc[11].chr(13).chr(10);
				$paquete=$paquete."P".$wnum.chr(13).chr(10);
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
				echo "HISTORIA DE MAS 12 DIGITOS <br>\n";
		}
		odbc_close($conex_o);
		odbc_close_all();
	}
}
?>
</body>
</html>