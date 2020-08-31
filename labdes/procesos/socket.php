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
  	

	

  	echo "<form action='socket.php' method=post>";
  	if(!isset($wact1) or !isset($wact2) or !isset($wnetq))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>LABORATORIO MEDICO LAS AMERICAS<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE ACTIVOS FIJOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE CODIGOS DE BARRAS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Activo Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wact1' size=6 maxlength=6></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Activo Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wact2' size=6 maxlength=6></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero de Etiquetas</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnetq' size=6 maxlength=6></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center  colspan=2><INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 1 CHECKED> Impresion x Codigo  ";
    		echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 2> Impresion General</td></tr>";	
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if($radio1 == 1)
  				$query = "select codigo,nombre,fecha_adquisicion from actlab_000001 where codigo between '".$wact1."' and '".$wact2."' order  by codigo";
  			else
  				$query = "select codigo,nombre,fecha_adquisicion,cantidad from actlab_000001 where imprimible='on' order  by codigo";
  			#echo $query."<br>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
		    	$row = mysql_fetch_array($err);
  				$longcb="6";
  				$codigo=$row[0];
 				$descripcion=substr($row[1],0,30);
  				$seccion="LABORATORIO MEDICO LAS AMERICAS";
  				$fechal="Fecha Adquisicion:".$row[2];
  				if($radio1 == 1)
  					$nroetq=$wnetq;
  				else
 		 			$nroetq=$row[3];
  				$paquete="";
  				$paquete=$paquete."OD".chr(13).chr(10);
  				$paquete=$paquete."FK".chr(34)."LABORA".chr(34).chr(13).chr(10);
  				$paquete=$paquete."FS".chr(34)."LABORA".chr(34).chr(13).chr(10);; 
  				$paquete=$paquete."V00,".$longcb.",L,".chr(34)."CODART".chr(34).chr(13).chr(10);
  				$paquete=$paquete."V01,31,L,".chr(34)."NOMBRE".chr(34).chr(13).chr(10);
  				$paquete=$paquete."V02,31,L,".chr(34)."SECCION".chr(34).chr(13).chr(10);
  				$paquete=$paquete."V03,31,L,".chr(34)."FECLIM".chr(34).chr(13).chr(10);
  				$paquete=$paquete."q656".chr(13).chr(10);
  				$paquete=$paquete."Q233,14.0".chr(13).chr(10);
  				$paquete=$paquete."S3".chr(13).chr(10);
  				$paquete=$paquete."D8".chr(13).chr(10);
  				$paquete=$paquete."ZT".chr(13).chr(10);
  				$paquete=$paquete."TTh:m".chr(13).chr(10);
  				$paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);
  				$paquete=$paquete."B230,25,0,3,1,6,81,B,V00".chr(13).chr(10);
  				$paquete=$paquete."A160,144,0,1,1,1,N,V01".chr(13).chr(10);
  				$paquete=$paquete."A160,170,0,1,1,1,N,V02".chr(13).chr(10);
  				$paquete=$paquete."A160,197,0,1,1,1,N,V03".chr(13).chr(10);
  				$paquete=$paquete."FE".chr(13).chr(10);
  				$paquete=$paquete.".".chr(13).chr(10);
  				$paquete=$paquete."FR".chr(34)."LABORA".chr(34).chr(13).chr(10);
  				$paquete=$paquete."?".chr(13).chr(10);
  				$paquete=$paquete.$codigo.chr(13).chr(10);
  				$paquete=$paquete.$descripcion.chr(13).chr(10);
  				$paquete=$paquete.$fechal.chr(13).chr(10);
  				$paquete=$paquete.$seccion.chr(13).chr(10);
  				$paquete=$paquete."P".$nroetq.chr(13).chr(10);
  				$paquete=$paquete.".".chr(13).chr(10);
  				$addr="132.1.20.183";
  				$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
   				if(!$fp) 
					echo "ERROR : "."$errstr ($errno)<br>\n";
   				else 
   				{
					fputs($fp,$paquete);
					#echo "PAQUETE ENVIADO $errstr ($errno)<br>\n";
					echo "PAQUETE : ". $codigo."<br>\n";
					fclose($fp);
   				}
   				sleep(5);
			}
		}
}
?>
</body>
</html>