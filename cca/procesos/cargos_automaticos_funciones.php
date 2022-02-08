<?php
/*
* INICIO FUNCIONES 
* Sami Arevalo - Cristhian Barros - Manuel Garcia (Equipo Iniciativa Cargos Automáticos)
* Fecha Feb 2021
*/

function validarTieneCca($conex, $origen, $dato, $tipo, $tor = '') {
	
	if( $tipo == 'hce' ) {
		$wbasedato_facturacion = consultarAliasPorAplicacionHCE($conex, $origen, 'facturacion');
	} else {
		$wbasedato_facturacion = consultarAliasPorAplicacion($conex, $origen, 'facturacion');
	}
	
	$num_registro =0;
	
	$query = "SELECT id FROM ".$wbasedato_facturacion."_000341";
	
	if ( $tipo == 'orden' ) {
		$toac = tipo_orden_comodin($conex, $origen, $tor);
		$query .= $toac == 'off' ? " WHERE FIND_IN_SET('".$dato."', ccacup) AND ccator = '' AND ccaord='on';" : " WHERE ccator = '".$tor."' AND (FIND_IN_SET('".$dato."', ccacup) OR (ccacup = '*' AND NOT FIND_IN_SET('".$dato."', ccapex))) ";
	} else if ( $tipo == 'hce' ) {
		$query .= " WHERE ccafhce = '".$dato."' AND (ccaeve = 'on' OR ccadat = 'on');";
	} else if ( $tipo == 'aplicacion' ) {
		$query .= " WHERE ccamoi = '".$dato."' AND ccapre = 'on';";
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
							, (CASE WHEN ccadat = 'on' OR ccaeve = 'on' THEN '".$wformulario."' ELSE NULL END) as wformulario
							, movdat
							, COALESCE(ccatcco,'H,D,A,U,Cx') ccatcco
							, ccater
							, CASE WHEN ccater IS NOT NULL THEN TRIM(CONCAT(Medno1, ' ', Medno2, ' ' ,Medap1, ' ' ,Medap2)) ELSE '' END tercero
							, Medesp tercero_esp
					FROM ".$wbasedato_facturacion."_000341 as cca 
					LEFT JOIN ".$wbasedato_hce."_000002 as hce ON (cca.ccafhce = hce.Detpro AND cca.ccachce = hce.Detcon) 
					LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
					LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
					LEFT JOIN ".$wbasedato_movhos."_000048 ON Meddoc = ccater AND Medest = 'on' AND Meddoc <> ''
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

function obtenerDatosCCAxOrden($conex, $origen, $wprocedimiento, $tipoOrdAdmComodin, $tor) {
	
	$wbasedato_movhos = consultarAliasPorAplicacionHCE($conex, $origen, 'movhos');
	$wbasedato_facturacion = consultarAliasPorAplicacionHCE($conex, $origen, 'facturacion');
	$wbasedato_hce = consultarAliasPorAplicacionHCE($conex, $origen, 'hce');
	
	$fecha = date('Y-m-d');
	
	if($tipoOrdAdmComodin == 'off') {
		$query = "SELECT ccacon 
							, Grudes as ccaconnom
							, Grutip as ccacontip
							, Codigo ccacup 
							, Nombre as ccacupnom
							, ccacco 
							, ccaart
							, Artcom as ccaartnom
							, ccaeve 
							, ccadat
							, ccaord
							, COALESCE(ccatcco,'H,D,A,U,Cx') ccatcco
							, ccater
							, CASE WHEN ccater IS NOT NULL THEN TRIM(CONCAT(Medno1, ' ', Medno2, ' ' ,Medap1, ' ' ,Medap2)) ELSE '' END tercero
							, Medesp tercero_esp
							, CASE WHEN ccator = '' THEN 'off' ELSE 'on' END comodin
					FROM ".$wbasedato_facturacion."_000341 as cca 
					LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
					LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
					LEFT JOIN ".$wbasedato_movhos."_000048 ON Meddoc = ccater AND Medest = 'on' AND Meddoc <> ''
					LEFT JOIN root_000012 ON Codigo = ".$wprocedimiento."
					WHERE FIND_IN_SET('".$wprocedimiento."', ccacup)
					AND ccator = ''
					AND ccaord='on'";
	} else {
		
		$query = "SELECT ccacon 
							, Grudes as ccaconnom
							, Grutip as ccacontip
							, Codigo ccacup 
							, Nombre as ccacupnom
							, ccacco 
							, ccaart
							, Artcom as ccaartnom
							, ccaeve 
							, ccadat
							, ccaord
							, COALESCE(ccatcco,'H,D,A,U,Cx') ccatcco
							, ccater
							, CASE WHEN ccater IS NOT NULL THEN TRIM(CONCAT(Medno1, ' ', Medno2, ' ' ,Medap1, ' ' ,Medap2)) ELSE '' END tercero
							, Medesp tercero_esp
							, CASE WHEN ccator = '' THEN 'off' ELSE 'on' END comodin
					FROM ".$wbasedato_facturacion."_000341 as cca 
					LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
					LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
					LEFT JOIN ".$wbasedato_movhos."_000048 ON Meddoc = ccater AND Medest = 'on' AND Meddoc <> ''
					LEFT JOIN root_000012 ON Codigo = ".$wprocedimiento."
				   WHERE ccator = '".$tor."'  
					 AND (FIND_IN_SET('".$wprocedimiento."', ccacup) OR (ccacup = '*' AND NOT FIND_IN_SET('".$wprocedimiento."', ccapex)))
					 AND ccaord='on'";
	}
	
					
	$exec_query = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	
	$data = array();
	while( $row = mysql_fetch_assoc($exec_query)) {
		$data[] = $row;
	}
	
	return $data;
	
}

function obtenerDatosCCAxAplicacion($conex, $origen, $wmedicamento, $movhis, $moving, $wbasedato_movhos, $wbasedato_facturacion, $tcco = null) {
	
	$fecha = date('Y-m-d');
	
	$condicion_tipo_cco = !is_null($tcco) ? " AND FIND_IN_SET('".$tcco."', ccatcco)" : "";
	
	$query = "SELECT ccacon 
							, Grudes as ccaconnom
							, Grutip as ccacontip
							, Gruinv as wconinv
							, ccacup 
							, Nombre as ccacupnom
							, ccacco 
							, ccaart 
							, a1.Artcom as ccaartnom
							, ccamoi
							, a2.Artcom as ccamoinom
							, ccaeve 
							, ccadat
							, ccaord
							, COALESCE(ccatcco,'H,D,A,U,Cx') ccatcco
							, ccater
							, CASE WHEN ccater IS NOT NULL THEN TRIM(CONCAT(Medno1, ' ', Medno2, ' ' ,Medap1, ' ' ,Medap2)) ELSE '' END tercero
							, Medesp tercero_esp
					FROM ".$wbasedato_facturacion."_000341 as cca 
					LEFT JOIN ".$wbasedato_movhos."_000026 a1 ON a1.Artcod = ccaart
					LEFT JOIN ".$wbasedato_movhos."_000026 a2 ON a2.Artcod = ccamoi 
					LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
					LEFT JOIN ".$wbasedato_movhos."_000048 ON Meddoc = ccater AND Medest = 'on' AND Meddoc <> ''
					LEFT JOIN root_000012 ON Codigo = ccacup
					WHERE ccamoi = '".$wmedicamento."' 
					AND ccapre='on' ".$condicion_tipo_cco;
					
	
	$exec_query = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	
	$data = array();
	while( $row = mysql_fetch_assoc($exec_query)) {
		$data[] = $row;
	}
	
	return $data;
	
}

function tipo_orden_comodin($conex, $wemp_pmla, $tipo_orden)
{
	$wbasedato_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$sql = 'SELECT Tipcom
			  FROM '.$wbasedato_hce.'_000015
			 WHERE Codigo = "'.$tipo_orden.'";';
	
	$exec = mysql_query($sql, $conex) or die("Error: " . mysql_errno() . " - en el query (Tipo orden comodin): $sql - ".mysql_error());
	$row = mysql_fetch_row($exec);	
	$comodin = $row[0];
	
	return $comodin;	
}

/*
* FIN FUNCIONES 
* Sami Arevalo - Cristhian Barros (Equipo Iniciativa Cargos Automáticos)
* Fecha Feb 2021
*/

?>