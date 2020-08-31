<html>
<head>
  <title>CREAR INDICES</title>
</head>

<?php
include_once("conex.php");

include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$q = " SELECT encpro "
    ."   FROM hce_000001 ";
$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$wnum = mysql_num_rows($res1);

for ($i=1; $i<=$wnum;$i++)
   {
    $row = mysql_fetch_array($res1);
	
	/*
	$q = " DROP INDEX hising_idx ON HCE_".$row[0];
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$q = " DROP INDEX hisingusutip_idx ON HCE_".$row[0];
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$q= " CREATE INDEX hisingusutip_idx ON HCE_".$row[0]." ( movhis, "
	   ."                                                    moving, " 
	   ."                                                    movusu, "
       ."                                                    movtip )";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	*/
	
	//$q = " DROP INDEX hisingusutip_idx ON HCE_".$row[0];
	//$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$q= " CREATE INDEX hising_idx ON HCE_".$row[0]." ( movhis, "
	   ."                                              moving )";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	//On
	echo $q."<br>";
   }
		  
?>
</html>
