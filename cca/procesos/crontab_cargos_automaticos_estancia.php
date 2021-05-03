<?php

include_once("conex.php");
include_once('ajax_cargos_automaticos.php');

$wemp_pmla = $_GET['wemp_pmla'];

if( isset($_GET['wemp_pmla']) && $wemp_pmla != '' ) {

	$wbasedato_movhos = consultarAliasPorAplicacion($conex,$wemp_pmla,'movhos');
	$wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
		
	$sql = "SELECT Ubihis,Ubiing 
			  FROM ".$wbasedato_movhos."_000018 
			  JOIN ".$wbasedato_cliame."_000100 
				ON Ubihis = Pachis 
			  JOIN ".$wbasedato_movhos."_000011  
				ON Ccocod = Ubisac 
			 WHERE Ubiald = 'off' 
			   AND Ubialp = 'off' 
			   AND ccohos = 'on'";

	$res = mysql_query($sql,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar Pacientes Estancia): ".$sql." - ".mysql_error());
	$arr = array();
	
	while($row = mysql_fetch_array($res))
	{
		
		$whis = $row['Ubihis'];
		$wing = $row['Ubiing'];
		
		guardarCargoAutomaticoEstancia($conex, $wemp_pmla, $wbasedato_movhos, $wbasedato_cliame, $whis, $wing);
		
	}

}

?>