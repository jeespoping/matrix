<html>
<head>
<title>GENERACION MENSUAL DE SALDOS DE INVENTARIO</title>
</head>
<body>
<?php
include_once("conex.php");
	

	

	if(isset($wbasedato))
		$empresa=$wbasedato;
	$wswscie = 0;
	$wanocie=date("Y");
	$wmescie=date("m");
	if($wmescie == 1)
	{
		$wanocie = $wanocie -1;
		$wmescie=12;
	}
	else
		$wmescie=$wmescie -1;
	$query = "SELECT Cieinv  from ".$empresa."_000033 where Cieano=".$wanocie." and Ciemes=".$wmescie;
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		if($row[0] == "off")
			$wswscie = 1;
	}
	else
		$wswscie = 2;
	echo "<table border=0 align=center>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
	echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>GENERACION DE SALDOS DE INVENTARIO</font></b></font></td></tr>";
	echo "<tr><td align=center bgcolor=#999999><font size=3 face='tahoma'><b>Espere un Momento por Favor</font></b></font></td></tr></table>";
	if($wswscie != 0)
	{
		$query = "lock table ".$empresa."_000007 LOW_PRIORITY WRITE, ".$empresa."_000014 LOW_PRIORITY WRITE, ".$empresa."_000033 LOW_PRIORITY WRITE";
		$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO KARDEX Y DETALLE DE MOVIMIENTO");
		$query = "delete from ".$empresa."_000014 ";
		$query = $query." where Salano =  ".$wanocie; 
		$query = $query." 	and Salmes = ".$wmescie; 
		$err = mysql_query($query,$conex);
		$query = "SELECT Karcod, Karcco, Karexi, Karpro, Karvuc, Karmax, Karmin, Karpor, Karfuc   from ".$empresa."_000007 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$w=$i+1;
				echo "GRABANDO EL REGISTRO NRo. ".$w."<br>";
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000014 (medico,fecha_data,hora_data, Salano, Salmes, Salcod, Salcco, Salexi, Salpro, Salvuc, Salmax, Salmin, Salpor, Salfuc, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanocie.",".$wmescie.",'".$row[0]."','".$row[1]."',".$row[2].",".$row[3].",".$row[4].",".$row[5].",".$row[6].",".$row[7].",'".$row[8]."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GENERANDO SALDOS : ".mysql_errno().":".mysql_error());
			}
			echo "<b>TOTAL REGISTROS GENERADOS . ".$num."</b><br>";
		}
		if($wswscie == 1)
		{
			$query =  " update ".$empresa."_000033 set Cieinv ='on'  where Cieano=". $wanocie." and Ciemes=".$wmescie;
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO CIERRES");
		}
		else
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000033 (medico,fecha_data,hora_data, Cieano, Ciemes, Ciefci, Cieinv, Ciefcp, Ciepro , seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanocie.",".$wmescie.",'".date("Y-m-d")."','on','0000-00-00','off','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GENERANDO SALDOS : ".mysql_errno().":".mysql_error());
		}
		$query = " UNLOCK TABLES";													
		$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS EN CIERRE");
	}
	else
		echo "NO APLICA GENERACION DE SALDOS<br>";
 ?>
</body>
</html>