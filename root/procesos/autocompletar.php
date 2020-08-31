<?php
include_once("conex.php");

//Para que funcione tambien en php 4.0.4
if(isset($_GET)){
	$q = strtolower($_GET["q"]);
}else{
	$q = strtolower($HTTP_GET_VARS["q"]);
}

//echo $q;
//echo "parametro:".$consulta." ya \n";

if (!$q) 
	return;
	


	



//Consulta convencional al maestro de oficios y ocupaciones
$q = substr($consulta,0,strpos($consulta,"var"))."'".$q."%'".substr($consulta,strpos($consulta,"var")+3);
	
//echo $q;

	$coleccion = array();
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$coleccion[] = $info['Descripcion']."\n";
		
		$cont1++;
	}

//Se recorre la colección completa
foreach ($coleccion as $dato){
	echo $dato;
}
?>