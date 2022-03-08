<?php



/************************************************************************************************************************

PROGRAMA: cargos_automaticos_funciones.php
Fecha de liberación: 04 Mayo 2021
Autor: Cidenet S.A - Iniciativa Cargos Automáticos
Versión Actual: 2022-02-22

OBJETIVO GENERAL: Este archivo contiene algunas funciones necesarias para el correcto funcionamiento del programa de configuración de cargos automáticos.

************************************************************************************************************************/

/**********************************************************************************************************************  
* INICIO FUNCIONES 
* Sami Arevalo - Cristhian Barros - Manuel Garcia (Equipo Iniciativa Cargos Automáticos)
* Fecha Feb 2021

[DOC]	   
	   OBJETIVO GENERAL :
	   
	   REGISTRO DE MODIFICACIONES :
	   .2022-01-13
			1. Se agrega el parametro $wespecialidad en la funcion obtenerDatosCCAxFormulario ademas se agrega la condicion de especialidad (condicion_espec)
[*DOC]
***********************************************************************************************************************/

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

/* MODIFICADO 2022-01-13 */
function obtenerDatosCCAxFormulario($conex, $origen, $wformulario, $mov_usu, $movhis, $moving, $wespecialidad, $wmeddoc) {
	
	$wbasedato_movhos = consultarAliasPorAplicacionHCE($conex, $origen, 'movhos');
	$wbasedato_facturacion = consultarAliasPorAplicacionHCE($conex, $origen, 'facturacion');
	$wbasedato_hce = consultarAliasPorAplicacionHCE($conex, $origen, 'hce');
	
	$fecha = date('Y-m-d');
	$data = array();
	
	$sql = "SELECT *
			  FROM ".$wbasedato_facturacion."_000101
			 WHERE Inghis = '".$movhis."'
			   AND Ingnin = '".$moving."'
				";
			
	$resIng = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql 1- ".mysql_error() );
	
	if( $rowsIng = mysql_fetch_array( $resIng) ){
			
		$codEmpParticular = consultarAliasPorAplicacion($conex, $origen, 'codigoempresaparticular');
	
		if( $rowsIng[ 'Ingtpa' ] == 'P' ){
			$empresa = $codEmpParticular;
		} else{
			$empresa = $rowsIng[ 'Ingcem' ];
		}
		
		$sql = "SELECT *
				  FROM ".$wbasedato_facturacion."_000024
				 WHERE empcod = '".$empresa."'";
	
		$resEmp = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql 2- ".mysql_error() );
		
		if( $rowsEmp = mysql_fetch_array( $resEmp ) ){
			//Informacion de empresa
			$wcodemp 	  = $rowsEmp[ 'Empcod' ];
			$tipoEmpresa  = $rowsEmp[ 'Emptem' ];
		}
	
		// Se setea por defecto el valor de la condicion tipo empresa para que busque la condición de tipo empresa comodín
		$condicion_temp_emp = " AND ccatem = '*' ";
		
		$sql_temp_especifica_comodin = "SELECT id 
										FROM ".$wbasedato_facturacion."_000341 
										WHERE ccafhce = '".$wformulario."' AND ccaesp = '".$wespecialidad."' AND  ccatem <> '*' AND ccatem = '".$tipoEmpresa."' AND ccaemp = '*'";
								
		$exec_query_temp_especifica_comodin = mysql_query($sql_temp_especifica_comodin, $conex) or die( mysql_errno()." - Error en el query CCA Tipo Empresa Especifica - ".mysql_error() );
		$num_reg_temp_especifica_comodin = mysql_num_rows($exec_query_temp_especifica_comodin);
		
		if($num_reg_temp_especifica_comodin > 0) {
			$condicion_temp_emp = " AND ccatem = '".$tipoEmpresa."' AND ccaemp = '*' ";
			
		}
		
		$sql_temp_especifica_especifica = "SELECT id 
											FROM ".$wbasedato_facturacion."_000341 
											WHERE ccafhce = '".$wformulario."' AND ccaesp = '".$wespecialidad."'  AND  ccatem <> '*' AND ccatem = '".$tipoEmpresa."' AND ccaemp = '".$wcodemp."'";
								
		$exec_query_temp_especifica_especifica = mysql_query($sql_temp_especifica_especifica, $conex) or die( mysql_errno()." - Error en el query CCA Tipo Empresa Especifica, Empresa Especifica - ".mysql_error() );
		$num_reg_temp_especifica_especifica = mysql_num_rows($exec_query_temp_especifica_especifica);
		
		if($num_reg_temp_especifica_especifica > 0) {
			$condicion_temp_emp = " AND ccatem = '".$tipoEmpresa."' AND ccaemp = '".$wcodemp."' ";
		}

		$sql_rol_usuario = "SELECT hce19.rolcod 
							  FROM ".$wbasedato_hce."_000020 hce20
							  JOIN ".$wbasedato_hce."_000019 hce19 ON Rolcod = Usurol AND Rolest = 'on' AND Rolmed = 'on'
							 WHERE usucod = '".$mov_usu."'";

		$exec_query_rol_usuario = mysql_query($sql_rol_usuario, $conex) or die(mysql_errno()." - Error en el query rol usuario - ".mysql_error());
		$num_reg_rol_usuario = mysql_num_rows($exec_query_rol_usuario);

		$case_ccater = "ccater";

		$condicion_espec = "";
		$condicion_esp_movhos_48 = "";

		if($num_reg_rol_usuario > 0) {
			if($wespecialidad != '' ) {
				$condicion_espec = " AND ((ccater = '*' AND ccaesp = '".$wespecialidad."') OR ccaesp = '') ";
				$condicion_esp_movhos_48 = " AND Medesp = '".$wespecialidad."' ";
			}
	
			$condicion_meddoc = "";
	
			if ($wmeddoc != "") {
				$condicion_meddoc = $wmeddoc;
			}

			$case_ccater = $condicion_meddoc." ccater";
		}
		
		$sql_cca_temp_especifica = "
			SELECT ccacon 
					, Grudes as ccaconnom
					, Grutip as ccacontip
					, ccacup 
					, Pronom as ccacupnom
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
					, ".$case_ccater."
					, CASE WHEN ccater IS NOT NULL THEN TRIM(CONCAT(Medno1, ' ', Medno2, ' ' ,Medap1, ' ' ,Medap2)) ELSE '' END tercero
					, Medesp tercero_esp
					, ccafac
					, ccatem
					, ccaemp
					, ccaesp
			FROM ".$wbasedato_facturacion."_000341 as cca 
			LEFT JOIN ".$wbasedato_hce."_000002 as hce ON (cca.ccafhce = hce.Detpro AND cca.ccachce = hce.Detcon) 
			LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
			LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
			LEFT JOIN ".$wbasedato_movhos."_000048 ON Meddoc = ccater AND Medest = 'on' AND Meddoc <> '' ".$condicion_esp_movhos_48." 
			LEFT JOIN ".$wbasedato_facturacion."_000103 ON Procod = ccacup
			LEFT JOIN ".$wbasedato_hce."_".$wformulario." as fhce ON ( cca.ccachce = movcon AND Dettip = movtip AND ccadat = 'on' AND movusu = '".$mov_usu."' AND fhce.Fecha_data = '".$fecha."' AND fhce.id = (SELECT MAX(id) FROM ".$wbasedato_hce."_".$wformulario." WHERE movusu = '".$mov_usu."' AND Fecha_data = '".$fecha."' AND movhis = '".$movhis."' AND moving = '".$moving."' AND cca.ccachce = movcon AND Dettip = movtip) ) 
			WHERE ccafhce = '".$wformulario."' ".$condicion_temp_emp." ".$condicion_espec;
			
		$exec_query = mysql_query( $sql_cca_temp_especifica, $conex) or die( mysql_errno()." - Error en el query CCA X FORMULARIO - ".mysql_error() );
		
		while( $row = mysql_fetch_assoc($exec_query)) {
			$data[] = $row;
		}
	}

	return $data;
	
}

function obtenerDatosCCAxOrden($conex, $origen, $wprocedimiento, $tipoOrdAdmComodin, $tor, $his, $ing) {
	
	$wbasedato_movhos = consultarAliasPorAplicacionHCE($conex, $origen, 'movhos');
	$wbasedato_facturacion = consultarAliasPorAplicacionHCE($conex, $origen, 'facturacion');
	
	$data = array();

	$sql = "SELECT *
			  FROM ".$wbasedato_facturacion."_000101
			 WHERE Inghis = '".$his."'
			   AND Ingnin = '".$ing."'
				";
			
	$resIng = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql 1- ".mysql_error() );
	
	if( $rowsIng = mysql_fetch_array( $resIng) ){
			
		$codEmpParticular = consultarAliasPorAplicacion($conex, $origen, 'codigoempresaparticular');
	
		if( $rowsIng[ 'Ingtpa' ] == 'P' ){
			$empresa = $codEmpParticular;
		} else{
			$empresa = $rowsIng[ 'Ingcem' ];
		}
		
		$sql = "SELECT *
				  FROM ".$wbasedato_facturacion."_000024
				 WHERE empcod = '".$empresa."'";
	
		$resEmp = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql 2- ".mysql_error() );
		
		if( $rowsEmp = mysql_fetch_array( $resEmp ) ){
			//Informacion de empresa
			$wcodemp 	  = $rowsEmp[ 'Empcod' ];
			$tipoEmpresa  = $rowsEmp[ 'Emptem' ];
		}
	
	
		// Se setea por defecto el valor de la condicion tipo empresa para que busque la condición de tipo empresa comodín
		$con_tipo_empresa_empresa = " AND ccatem = '*' ";
		$con_tipo_orden_comodin = $tipoOrdAdmComodin == 'on' ? " AND (FIND_IN_SET('".$wprocedimiento."', ccacup) OR (ccacup = '*' AND NOT FIND_IN_SET('".$wprocedimiento."', ccapex)))" : " AND FIND_IN_SET('".$wprocedimiento."', ccacup) ";
		$con_ccator = $tipoOrdAdmComodin == 'on' ? " ccator= '".$tor."'" : " ccator = '' ";

		$sql_temp_especifica_comodin = "SELECT id 
										FROM ".$wbasedato_facturacion."_000341 
										WHERE ".$con_ccator." ".$con_tipo_orden_comodin." AND ccaord='on' AND  ccatem <> '*' AND ccatem = '".$tipoEmpresa."' AND ccaemp = '*'";
								
		$exec_query_temp_especifica_comodin = mysql_query($sql_temp_especifica_comodin, $conex) or die( mysql_errno()." - Error en el query CCA Tipo Empresa Especifica - ".mysql_error() );
		$num_reg_temp_especifica_comodin = mysql_num_rows($exec_query_temp_especifica_comodin);
		
		if($num_reg_temp_especifica_comodin > 0) {
			$con_tipo_empresa_empresa = " AND ccatem = '".$tipoEmpresa."' AND ccaemp = '*' ";
		}
		
		$sql_temp_especifica_especifica = "SELECT id 
											FROM ".$wbasedato_facturacion."_000341 
											WHERE ".$con_ccator." ".$con_tipo_orden_comodin." AND ccaord='on' AND  ccatem <> '*' AND ccatem = '".$tipoEmpresa."' AND ccaemp = '".$wcodemp."'";
								
		$exec_query_temp_especifica_especifica = mysql_query($sql_temp_especifica_especifica, $conex) or die( mysql_errno()." - Error en el query CCA Tipo Empresa Especifica, Empresa Especifica - ".mysql_error() );
		$num_reg_temp_especifica_especifica = mysql_num_rows($exec_query_temp_especifica_especifica);
		
		if($num_reg_temp_especifica_especifica > 0) {
			$con_tipo_empresa_empresa = " AND ccatem = '".$tipoEmpresa."' AND ccaemp = '".$wcodemp."' ";
		}
		
		$query = "SELECT ccacon 
							, Grudes as ccaconnom
							, Grutip as ccacontip
							, Procod ccacup 
							, Pronom as ccacupnom
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
							, ccafac
							, ccatem
							, ccaemp
							, ccaesp
					FROM ".$wbasedato_facturacion."_000341 as cca 
					LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
					LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
					LEFT JOIN ".$wbasedato_movhos."_000048 ON Meddoc = ccater AND Medest = 'on' AND Meddoc <> ''
					LEFT JOIN ".$wbasedato_facturacion."_000103 ON Procod = ".$wprocedimiento."
					WHERE ".$con_ccator." AND ccaord='on' ".$con_tipo_orden_comodin." ".$con_tipo_empresa_empresa;
						
		$exec_query = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query - ".mysql_error() );
		while( $row = mysql_fetch_assoc($exec_query)) {
			$data[] = $row;
		}
	}
	
	return $data;
	
}


function obtenerDatosCCAxAplicacion($conex, $origen, $wmedicamento, $movhis, $moving, $wbasedato_movhos, $wbasedato_facturacion, $tcco = null) {
	
	$sql = "SELECT *
			  FROM ".$wbasedato_facturacion."_000101
			 WHERE Inghis = '".$movhis."'
			   AND Ingnin = '".$moving."'
				";
			
	$resIng = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql 1- ".mysql_error() );
	
	$data = array();

	if( $rowsIng = mysql_fetch_array( $resIng) ) {
			
		$codEmpParticular = consultarAliasPorAplicacion($conex, $origen, 'codigoempresaparticular');
	
		if( $rowsIng[ 'Ingtpa' ] == 'P' ){
			$empresa = $codEmpParticular;
		} else{
			$empresa = $rowsIng[ 'Ingcem' ];
		}
		
		$sql = "SELECT *
				  FROM ".$wbasedato_facturacion."_000024
				 WHERE empcod = '".$empresa."'";
	
		$resEmp = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql 2- ".mysql_error() );
		
		if( $rowsEmp = mysql_fetch_array( $resEmp ) ){
			//Informacion de empresa
			$wcodemp 	  = $rowsEmp[ 'Empcod' ];
			$tipoEmpresa  = $rowsEmp[ 'Emptem' ];
		}
	
	
		$condicion_tipo_cco = !is_null($tcco) ? " AND FIND_IN_SET('".$tcco."', ccatcco)" : "";
		
		// Se setea por defecto el valor de la condicion tipo empresa para que busque la condición de tipo empresa comodín
		// es decir va desde lo más general a lo más específico
		
		$con_tipo_empresa_empresa = " AND ccatem = '*' ";
		
		
		$sql_temp_especifica_comodin = "SELECT id 
										FROM ".$wbasedato_facturacion."_000341 
										WHERE ccapre = 'on' AND ccamoi = '".$wmedicamento."' ".$condicion_tipo_cco."  AND  ccatem <> '*' AND ccatem = '".$tipoEmpresa."' AND ccaemp = '*'";
								
		$exec_query_temp_especifica_comodin = mysql_query($sql_temp_especifica_comodin, $conex) or die( mysql_errno()." - Error en el query CCA Tipo Empresa Especifica - ".mysql_error() );
		$num_reg_temp_especifica_comodin = mysql_num_rows($exec_query_temp_especifica_comodin);
		
		if($num_reg_temp_especifica_comodin > 0) {
			$con_tipo_empresa_empresa = " AND ccatem = '".$tipoEmpresa."' AND ccaemp = '*' ";
		}
		
		$sql_temp_especifica_especifica = "SELECT id 
											FROM ".$wbasedato_facturacion."_000341 
											WHERE ccapre = 'on' AND ccamoi = '".$wmedicamento."' ".$condicion_tipo_cco." AND  ccatem <> '*' AND ccatem = '".$tipoEmpresa."' AND ccaemp = '".$wcodemp."'";
								
		$exec_query_temp_especifica_especifica = mysql_query($sql_temp_especifica_especifica, $conex) or die( mysql_errno()." - Error en el query CCA Tipo Empresa Especifica, Empresa Especifica - ".mysql_error() );
		$num_reg_temp_especifica_especifica = mysql_num_rows($exec_query_temp_especifica_especifica);
		
		if($num_reg_temp_especifica_especifica > 0) {
			$con_tipo_empresa_empresa = " AND ccatem = '".$tipoEmpresa."' AND ccaemp = '".$wcodemp."' ";
		}
		
		$query = "SELECT ccacon 
								, Grudes as ccaconnom
								, Grutip as ccacontip
								, Gruinv as wconinv
								, ccacup 
								, Pronom as ccacupnom
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
								, ccafac
								, ccatem
								, ccaemp
								, ccaesp
						FROM ".$wbasedato_facturacion."_000341 as cca 
						LEFT JOIN ".$wbasedato_movhos."_000026 a1 ON a1.Artcod = ccaart
						LEFT JOIN ".$wbasedato_movhos."_000026 a2 ON a2.Artcod = ccamoi 
						LEFT JOIN ".$wbasedato_facturacion."_000200 ON ccacon = Grucod 
						LEFT JOIN ".$wbasedato_movhos."_000048 ON Meddoc = ccater AND Medest = 'on' AND Meddoc <> ''
						LEFT JOIN ".$wbasedato_facturacion."_000103 ON Procod = ccacup
					WHERE ccamoi = '".$wmedicamento."'
						AND ccapre='on' ".$condicion_tipo_cco." ".$con_tipo_empresa_empresa;
						
		
		$exec_query = mysql_query( $query, $conex) or die( mysql_errno()." - Error en el query - ".mysql_error() );
		
		
		while( $row = mysql_fetch_assoc($exec_query)) {
			$data[] = $row;
		}

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