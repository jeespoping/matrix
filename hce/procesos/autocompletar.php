<?php
include_once("conex.php");
// Version 2011-11-15
//Para que funcione tambien en php 4.0.4
if(isset($_GET)){
	$q = strtolower($_GET["q"]);
}else{
	$q = strtolower($HTTP_GET_VARS["q"]);
}

//echo $q;
//echo "parametro:".$consulta." ya \n";
$tipo=substr($consulta,0,1);
$consulta=substr($consulta,1);

if (!$q and $tipo == 1) {
	return;
}
	


	




//Consulta convencional al maestro de oficios y ocupaciones
if($tipo == "1")
	$q = substr($consulta,0,strpos($consulta,"var"))."'".$q."%'".substr($consulta,strpos($consulta,"var")+3);
elseif($tipo == "2")
		$q = str_replace("var","'%".$q."%'",$consulta);
	elseif($tipo == "3" and strpos($consulta,"-") !== false)
		{
			$query="select detfor from hce_000002 where Detpro = '".substr($consulta,0,6)."' and Detcon = ".substr($consulta,6,strpos($consulta,"-")-6);
			$res = mysql_query($query, $conex) or die ("Error En Smart: ".mysql_errno().mysql_error());
			$info = mysql_fetch_row($res);
			@eval($info[0]);
		}
		elseif($tipo == "3")
			{
				$query="select * ";
				$res = mysql_query($query, $conex) or die ("");
			}	
	
//echo $q;

	$coleccion = array();
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	//echo $num."<br>";
	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_row($res);

		//$coleccion[] = $info['Codigo']." ".$info['Descripcion']."\n";
		for ($w=0;$w<count($info);$w++)
		{
			if(isset($info[$w]) AND $info[$w] != "DEBE ESCOGER TURNO QUIRURGICO")
			{
				//$coleccion[] = $info[$w]." ".$info[1]."\n";
				// $coleccion[] = htmlentities($info[$w])." ";
				$coleccion[] = $info[$w]." ";
			}
		}
		$coleccion[] .= "\n";
		$cont1++;
	}

//Se recorre la colección completa
foreach ($coleccion as $dato){
	// echo utf8_encode($dato);
	echo $dato;
}
?>
