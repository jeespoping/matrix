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
			  JOIN ".$wbasedato_movhos."_000020  
				ON Habhis = Ubihis
			   AND Habing = Ubiing
			 WHERE Ubiald = 'off' 
			   AND Ubialp = 'off' 
			   AND ccohos = 'on'
		  ORDER BY Ubihis ASC";
	
	$res = mysql_query($sql,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar Pacientes Estancia): ".$sql." - ".mysql_error());
	$arr = array();
	
	echo "El crontab se inició - ".date("Y-m-d H:i:s");
	while($row = mysql_fetch_array($res))
	{
		$whis = $row['Ubihis'];
		$wing = $row['Ubiing'];	
		
		try {	
			guardarCargoAutomaticoEstancia($conex, $wemp_pmla, $wbasedato_movhos, $wbasedato_cliame, $whis, $wing);			
		} catch(Throwable $e) {
			$msg = 'Historia: '.$whis.'-'.$wing.'  | '.$e->getMessage().' | en la linea: '.$e->getLine();
			$sql = "INSERT INTO ".$wbasedato_cliame."_000351 (Medico, Fecha_data, Hora_data, Descripcion, Proceso, Seguridad) VALUES('Cliame', '".date("Y-m-d")."', '".date("H:i:s")."', '".$msg."', 'Cargo automatico estancia','C-root')";
			mysql_query($sql,$conex);			
		}		
	}
	echo "El crontab finalizó - ".date("Y-m-d H:i:s");
}

?>