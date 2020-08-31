<?php
include_once("conex.php");


include_once("root/comun.php");


$conex 			= obtenerConexionBD("matrix");
$wfecha			= date("Y-m-d");
$whora 			= date("H:i:s");
$conexUnix		= odbc_connect('facturacion','informix','sco');






$sql = "
SELECT id
  FROM tempJ
 WHERE otr = 'ok'
";
$ressql = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX(sql):</b><br>".mysql_error());
while($rowsql = mysql_fetch_array($ressql))
{
	$sqlUp = "
	UPDATE tempJ2
	   SET otr = 'ok'
	 WHERE id = '".$rowsql['id']."'
	";
	mysql_query($sqlUp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUp):</b><br>".mysql_error());
}








return;
return;
return;

$sql = "
SELECT his, ing, id
  FROM tempJ2
";
$ressql = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX(sql):</b><br>".mysql_error());
while($rowsql = mysql_fetch_array($ressql))
{
	$arrayH[$rowsql['id']]["his"] = trim($rowsql['his']);
	$arrayH[$rowsql['id']]["ing"] = trim($rowsql['ing']);
}

foreach($arrayH as $id => $inf)
{
	$arrayRes = array();
	$sqlEncFac = "
	SELECT movcer, movdoc, movres, carval
	  FROM FAMOV, CACAR 
	 WHERE movhis = '".$inf["his"]."'
	   AND movnum = '".$inf["ing"]."'
	   AND movanu = '0'
	   AND movemp = 'E'
	   AND movfue = '20'
	   AND movfec BETWEEN '2016-12-12' AND '2017-02-28' 
       AND carfue = movfue
	   AND cardoc = movdoc	
       AND caranu = '0'	   
	";
	$resEncFac = odbc_exec($conexUnix, $sqlEncFac);
	$x = 0;
	while(odbc_fetch_row($resEncFac))
	{
		$idx = count($arrayRes);
		$arrayRes[$idx]["fac"] 		= trim(odbc_result($resEncFac,'movdoc'));
		$arrayRes[$idx]["res"] 		= trim(odbc_result($resEncFac,'movcer'))."  ".trim(odbc_result($resEncFac,'movres'));
		$arrayRes[$idx]["valTotal"] = trim(odbc_result($resEncFac,'carval'));
		
		$sqlCar = "
		SELECT envdetfue, envdetdoc
		  FROM CAENVDET
		 WHERE envdetfan = '20'
	       AND envdetdan = '".trim(odbc_result($resEncFac,'movdoc'))."'		   
		";
		$resCar = odbc_exec($conexUnix, $sqlCar);
		if(odbc_fetch_row($resCar))
		{
			$cartaCob = trim(odbc_result($resCar,'envdetfue'))."-".trim(odbc_result($resCar,'envdetdoc'));
			$arrayRes[$idx]["carta"] = $cartaCob;
		}			
	}
	// if(count($arrayRes) > 1)
	echo "<pre>";	
	print_r($arrayRes);
	echo "</pre>";
	
	$sqlInsert = "
	UPDATE tempJ2
	   SET fac 		= '".$arrayRes[0]["fac"]."',
		   res 		= '".$arrayRes[0]["res"]."',
		   carta 	= '".$arrayRes[0]["carta"]."',
		   valFac 	= '".$arrayRes[0]["valTotal"]."',
		   fac2 	= '".$arrayRes[1]["fac"]."',
		   res2 	= '".$arrayRes[1]["res"]."',
		   carta2 	= '".$arrayRes[1]["carta"]."',
		   valFac2 	= '".$arrayRes[1]["valTotal"]."',
		   fac3 	= '".$arrayRes[2]["fac"]."',
		   res3 	= '".$arrayRes[2]["res"]."',
		   carta3 	= '".$arrayRes[2]["carta"]."',
		   valFac3 	= '".$arrayRes[2]["valTotal"]."'
	 WHERE  id = '".$id."'
	";
	mysql_query($sqlInsert, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInsert):</b><br>".mysql_error());
}
?>
