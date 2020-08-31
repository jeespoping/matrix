<html>
<head>
  <title>MATRIX Sticker De Pacientes</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='HCE_Sticker_GA.php' method=post>";
	

	

	if(!isset($whis) or !isset($wip))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>STICKER DE PACIENTES - GASES ARTERIALES UCI Ver. 2015-11-30</b></td></tr>";
		if(isset($whis))
			echo "<tr><td bgcolor=#cccccc>Numero De Historia</td><td bgcolor=#cccccc align=center><input type='TEXT' name='whis' value='".$whis."' size=12 maxlength=12></td></tr>";
		else
			echo "<tr><td bgcolor=#cccccc>Numero De Historia</td><td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc>IP Impresora</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wip' value='132.1.20.27' size=15 maxlength=15></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
	}
	else
	{
		//                  0        1                   2             3       4       5       6       7       8         
		$query  = "select orihis, oriing, concat(pacno1,' ',pacno2), pacap1, pacap2, pacnac, pacsex, pactid, pacced from root_000037,root_000036 ";
		$query .= " where orihis = '".$whis."'";
		$query .= "   and oriori = '01' ";
		$query .= "   and oritid = pactid ";
		$query .= "   and oriced = pacced ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$query = "lock table agfa_000006  WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO  : ".mysql_errno().":".mysql_error());
			$query =  " update agfa_000006 set Concon = Concon + 1 where Contip='ADT_A01' ";
			$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO DE MENSAJES");
			$query = "select Concon from agfa_000006 where Contip='ADT_A01' ";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO DE MENSAJES");
			$row1 = mysql_fetch_array($err1);
			$ProcessingID=$row1[0];
			$query = " UNLOCK TABLES";
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
			$ann=(integer)substr($row[5],0,4)*360 +(integer)substr($row[5],5,2)*30 + (integer)substr($row[5],8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$EDAD=(integer)($aa - $ann)/360;
			$NOM=$row[3]." ".$row[4]." ".$row[2];
			$NOM=substr($NOM,0,32);
			$ProcessingID=str_pad($ProcessingID, 9, "0", STR_PAD_LEFT);
			$longC=strlen($ProcessingID);
			$wori="GASES UCI";
			$wsex=$row[6];
			$wfna=$row[5];
			$wnom=$row[2];
			$wap1=$row[3];
			$wap2=$row[4];
			$CED=$row[8];
			$ID=$row[0]."-".$row[1];
			if($longC < 10)
			{
				echo "HISTORIA NRo : ".$whis."<br>";
				$paquete="";
				$paquete=$paquete."N".chr(13).chr(10);
				$paquete=$paquete."FK".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."FS".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V00,".$longC.",L,".chr(34)."OT".chr(34).chr(13).chr(10);
				$paquete=$paquete."V01,13,L,".chr(34)."HISTORIA".chr(34).chr(13).chr(10);
				$paquete=$paquete."V02,32,L,".chr(34)."NOMBRE".chr(34).chr(13).chr(10);
				$paquete=$paquete."V03,15,L,".chr(34)."CC".chr(34).chr(13).chr(10);
				$paquete=$paquete."V04,3,L,".chr(34)."EDAD".chr(34).chr(13).chr(10);
				$paquete=$paquete."V05,1,L,".chr(34)."SEXO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V06,3,L,".chr(34)."INGRESO".chr(34).chr(13).chr(10);
				$paquete=$paquete."V07,20,L,".chr(34)."ORIGEN".chr(34).chr(13).chr(10);
				$paquete=$paquete."Q304,24".chr(13).chr(10);
				$paquete=$paquete."q400".chr(13).chr(10);
				$paquete=$paquete."B105,10,0,1,2,6,70,N,V00".chr(13).chr(10);
				$paquete=$paquete."A90,90,0,3,1,1,N,".chr(34)."HISTORIA:".chr(34)."V01".chr(13).chr(10);
				$paquete=$paquete."A35,120,0,4,1,1,N,".chr(34)."EXAMEN DE ".chr(34)."V07".chr(13).chr(10);
				$paquete=$paquete."A10,150,0,2,1,1,N,V02".chr(13).chr(10);
				$paquete=$paquete."A60,200,0,2,1,1,N,".chr(34)."AGE:".chr(34)."V04".chr(13).chr(10);
				$paquete=$paquete."A165,200,0,2,1,1,N,".chr(34)."SEX:".chr(34)."V05".chr(13).chr(10);
				$paquete=$paquete."A250,200,0,2,1,1,N,".chr(34)."ING:".chr(34)."V06".chr(13).chr(10);
				$paquete=$paquete."A60,175,0,2,1,1,N,V03".chr(13).chr(10);
				$paquete=$paquete."FE".chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				$paquete=$paquete."FR".chr(34)."CARGO".chr(34).chr(13).chr(10);
				$paquete=$paquete."?".chr(13).chr(10);
				$paquete=$paquete.$ProcessingID.chr(13).chr(10);
				$paquete=$paquete.$row[0]."-".$row[1].chr(13).chr(10);
				$paquete=$paquete.$NOM.chr(13).chr(10);
				$paquete=$paquete.$row[7].":".$row[8].chr(13).chr(10);
				$paquete=$paquete.$EDAD.chr(13).chr(10);
				$paquete=$paquete.$row[6].chr(13).chr(10);
				$paquete=$paquete.$row[1].chr(13).chr(10);
				$paquete=$paquete.$wori.chr(13).chr(10);
				$paquete=$paquete."P1".chr(13).chr(10);
				$paquete=$paquete.".".chr(13).chr(10);
				echo $paquete."<br>";
				$addr=$wip;
				$fp = fsockopen( $addr,9100, $errno, $errstr, 30);
				if(!$fp) 
				echo "ERROR : "."$errstr ($errno)<br>\n";
				else 
				{
					fputs($fp,$paquete);
					echo "PAQUETE ENVIADO <br>\n";
					
					//MENSAJE ADT_A01 INGRESO DE PACIENTES
					$fecna=explode("-",$wfna);
					if(strlen($fecna[1]) < 2)
						$fecna[1] = "0".$fecna[1];
					if(strlen($fecna[2]) < 2)
						$fecna[2] = "0".$fecna[2];
					$wapg=trim($wap1)." ".trim($wap2);
					$wapg=substr($wapg,0,30);
					$wnom=trim($wnom);
					$wnom=substr($wnom,0,30);
					$texto  = "MSH|^~\&||HISLIS||IT1000|".date("YmdHis")."||ADT^A01|".$ProcessingID."|P|2.3|||NE|NE|AU|ASCII".chr(13);
					$texto .= "PID|1||".$ProcessingID."|".$ID."|".$wapg."^".$wnom."||".$fecna[0].$fecna[1].$fecna[2]."|".$wsex."|".chr(13);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert agfa_000010(medico, fecha_data, hora_data, Numero, Tipo, Clase, Mensaje, Contador, Estado, Estado_servinte, seguridad)";
					$query .= " values ('AGFA','".$fecha."','".$hora."',".$ProcessingID.",'ADT_A01','O','".$texto."',0,'off','off','C-AGFA')";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."ERROR GRABANDO MENSAJE");
					
				}
				sleep(2);
				fclose($fp);
			}
			else
				echo "HISTORIA DE MAS 12 DIGITOS <br>\n";
		}
	}
}
?>
</body>
</html>
