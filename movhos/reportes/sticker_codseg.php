<html>
<head>
  <title>MATRIX Sticker Codigo de Seguridad de Pacientes</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
/****************************************************************************
 ACTUALIZACIÓN
 --------------------------------------------------------------------------
 08/03/2022-Brigith Lagares : Se estandariza wemp_pmla  

*****************************************************************************/

$wemp_pmla = $_REQUEST['wemp_pmla'];
include_once("conex.php");
include_once("root/comun.php");

$wactualiz = '2022-02-24';
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
encabezado("STICKER CODIGO DE SEGURIDAD DE PACIENTES ",$wactualiz, $wbasedato1);

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='sticker_codseg.php' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	//echo "<input type='HIDDEN' name= 'codemp' value='".$codemp."'>";
	echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";

	

	if(!isset($whis) or !isset($wip))
	{
		echo "<center><table border=0>";
		//echo "<tr><td align=center colspan=2><b>STICKER CODIGO DE SEGURIDAD DE PACIENTES Ver. 2013-09-16</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc>Numero de Historia</td><td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=20 maxlength=20></td></tr>";
		echo "<tr><td bgcolor=#cccccc>IP Impresora</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Impnip, Impnom  from root_000053 where Impest='on'  and Impseg='on' order by Impnip";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wip' id=tipo1>";
		echo "<option>0-SELECCIONE IMPRESORA</option>";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		$wip = substr($wip,0,strpos($wip,"-"));
		if($wip != "0")
		{
			$query = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2 ";
			$query .= " from ".$empresa."_000018,root_000037,root_000036 ";
			$query .= " where ubiald = 'off'  ";
			$query .= " and ubihis = '".$whis."' ";
			$query .= " and ubihis = orihis  ";
			$query .= " and ubiing = oriing  ";
			$query .= " and  oriori = '".$wemp_pmla."'  ";
			$query .= " and oriced = pacced  ";
			$query .= " and oritid = pactid  ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wno1 = substr($row[4]." ".$row[5],0,40);
				$wno2 = substr($row[6]." ".$row[7],0,40);
				$longC=strlen($whis."-".$row[1]);
				//$pos=190 * (2 - ($longC / 10));
				$wnet=1;
				if($longC < 21)
				{
					echo "HISTORIA NRo : ".$whis."<br>";
					$pos= (600 - ($longC * 2))/2;
					$paquete="";
					$paquete=$paquete."N".chr(13).chr(10);
					$paquete=$paquete."FK".chr(34)."TCX".chr(34).chr(13).chr(10);
					$paquete=$paquete."FS".chr(34)."TCX".chr(34).chr(13).chr(10);; 
					$paquete=$paquete."V00,".$longC.",L,".chr(34)."NUMERO".chr(34).chr(13).chr(10);
					$paquete=$paquete."V01,30,L,".chr(34)."NOM1".chr(34).chr(13).chr(10);
					$paquete=$paquete."V02,30,L,".chr(34)."NOM2".chr(34).chr(13).chr(10);
					$paquete=$paquete."q750".chr(13).chr(10);
					$paquete=$paquete."S3".chr(13).chr(10);
					$paquete=$paquete."D8".chr(13).chr(10);
					$paquete=$paquete."ZT".chr(13).chr(10);
					$paquete=$paquete."TTh:m".chr(13).chr(10);
					$paquete=$paquete."TDy2.mn.dd".chr(13).chr(10);
					$paquete=$paquete."A240,20,0,3,1,1,N,".chr(34)."CLINICA LAS AMERICAS".chr(34).chr(13).chr(10);
					$paquete=$paquete."A228,40,0,3,1,1,N,".chr(34)."SEGURIDAD DEL PACIENTE".chr(34).chr(13).chr(10);
					$paquete=$paquete."A280,70,0,3,1,1,N,".chr(34)."CODIGO NRO:".chr(34).chr(13).chr(10);
					$paquete=$paquete."A".$pos.",110,0,4,1,2,N,V00".chr(13).chr(10);
					$paquete=$paquete."A200,190,0,2,1,1,N,V01".chr(13).chr(10);
					$paquete=$paquete."A200,220,0,2,1,1,N,V02".chr(13).chr(10);
					$paquete=$paquete."FE".chr(13).chr(10);
					$paquete=$paquete.".".chr(13).chr(10);
					$paquete=$paquete."FR".chr(34)."TCX".chr(34).chr(13).chr(10);
					$paquete=$paquete."?".chr(13).chr(10);
					$paquete=$paquete.$whis."-".$row[1].chr(13).chr(10);
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
					echo "LA LONGITUD MAXIMA ES DE 20 DIGITOS <br>\n";
			}
			else
				echo "EL PACIENTE NO EXISTE O NO ESTA ACTIVO <br>\n";
		}
		else
			echo "POR FAVOR SELECIONE IMPRESORA <br>\n";
	}
}
?>
</body>
</html>
