<?php
/*
* INICIO FUNCIONES 
* Sami Arevalo - Cristhian Barros (Equipo Iniciativa Cargos Automáticos)
* Fecha Feb 2021
*/

function validarTieneCca($conex, $origen, $dato, $tipo) {
	
	if( $tipo == 'hce' ) {
		$wbasedato_facturacion = consultarAliasPorAplicacionHCE($conex, $origen, 'facturacion');
	} else {
		$wbasedato_facturacion = consultarAliasPorAplicacion($conex, $origen, 'facturacion');
	}
	
	$num_registro =0;
	
	$query = "SELECT id FROM ".$wbasedato_facturacion."_000341";
	
	if ( $tipo == 'orden' ) {
		$query .= " WHERE ccacup = '".$dato."' AND ccaord = 'on';";
	} else if ( $tipo == 'hce' ) {
		$query .= " WHERE ccafhce = '".$dato."' AND (ccaeve = 'on' OR ccadat = 'on');";
	} else if ( $tipo == 'medicamentos' ) {
		$query .= " WHERE ccaart = '".$dato."' AND ccapre = 'on';";
	}
	
	$exec_query = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query valida tiene cca - ".mysql_error() );;
	$num_registro = mysql_num_rows($exec_query);
	
	if($query != '' && $num_registro > 0) {
		return true;
	} else {
		return false;
	}
}

function obtenerDatosCCAxFormulario($conex, $origen, $wformulario, $mov_usu, $movhis, $moving) {
	
	$wbasedato_movhos = consultarAliasPorAplicacionHCE($conex, $origen, 'movhos');
	$wbasedato_facturacion = consultarAliasPorAplicacionHCE($conex, $origen, 'facturacion');
	$wbasedato_hce = consultarAliasPorAplicacionHCE($conex, $origen, 'hce');
	
	$fecha = date('Y-m-d');
	
	$query_cca = "SELECT ccacon 
							, Grudes as ccaconnom
							, Grutip as ccacontip
							, ccacup 
							, Nombre as ccacupnom
							, ccacco 
							, ccaart
							, Artcom as ccaartnom
							, ccaeve 
							, ccadat 
							, Dettip
							, Detcon
							, Detnpa
							, (CASE WHEN ccadat = 'on' THEN '".$wformulario."' ELSE NULL END) as wformulario
							, movdat
							, COALESCE(ccatcco,'H,D,A,U,Cx') ccatcco
					FROM ".$wbasedato_facturacion."_000341 as cca 
					LEFT JOIN ".$wbasedato_hce."_000002 as hce ON (cca.ccafhce = hce.Detpro AND cca.ccachce = hce.Detcon) 
					LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
					LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
					LEFT JOIN root_000012 ON Codigo = ccacup
					LEFT JOIN ".$wbasedato_hce."_".$wformulario." as fhce ON ( cca.ccachce = movcon AND Dettip = movtip AND ccadat = 'on' AND movusu = '".$mov_usu."' AND fhce.Fecha_data = '".$fecha."' AND fhce.id = (SELECT MAX(id) FROM ".$wbasedato_hce."_".$wformulario." WHERE movusu = '".$mov_usu."' AND Fecha_data = '".$fecha."' AND movhis = '".$movhis."' AND moving = '".$moving."' AND cca.ccachce = movcon AND Dettip = movtip) )
					WHERE ccafhce = '".$wformulario."'";	
	
	$exec_query = mysql_query( $query_cca, $conex) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	
	$data = array();
	while( $row = mysql_fetch_assoc($exec_query)) {
		$data[] = $row;
	}
	
	return $data;
	
}

function obtenerDatosCCAxOrden($conex, $origen, $wprocedimiento, $mov_usu, $movhis, $moving) {
	
	$wbasedato_movhos = consultarAliasPorAplicacionHCE($conex, $origen, 'movhos');
	$wbasedato_facturacion = consultarAliasPorAplicacionHCE($conex, $origen, 'facturacion');
	$wbasedato_hce = consultarAliasPorAplicacionHCE($conex, $origen, 'hce');
	
	$fecha = date('Y-m-d');
	
	$query = "SELECT ccacon 
							, Grudes as ccaconnom
							, Grutip as ccacontip
							, ccacup 
							, Nombre as ccacupnom
							, ccacco 
							, ccaart
							, Artcom as ccaartnom
							, ccaeve 
							, ccadat
							, ccaord
							, COALESCE(ccatcco,'H,D,A,U,Cx') ccatcco
					FROM ".$wbasedato_facturacion."_000341 as cca 
					LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
					LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
					LEFT JOIN root_000012 ON Codigo = ccacup
					WHERE ccacup = '".$wprocedimiento."' 
					AND ccaord='on' ";
					
	$exec_query = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	
	$data = array();
	while( $row = mysql_fetch_assoc($exec_query)) {
		$data[] = $row;
	}
	
	return $data;
	
}

/*
* FIN FUNCIONES 
* Sami Arevalo - Cristhian Barros (Equipo Iniciativa Cargos Automáticos)
* Fecha Feb 2021
*/

?>