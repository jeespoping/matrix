<?php
include_once("conex.php");
include_once("root/comun.php");

include "../../gesapl/procesos/gestor_aplicaciones_config.php";
include_once("../../gesapl/procesos/gesapl_funciones.php");
include_once("ips/funciones_facturacionERP.php");
include_once("hce/funcionesHCE.php");
include_once('cargos_automaticos_funciones.php');

function  obtenerArrayFormulariosHCE($conex, $wbasedato_hce, $name_hce )
{
	$name_hce = strtolower($name_hce);
	
	$q_hce = 
	"SELECT Encpro, Encdes FROM ".$wbasedato_hce."_000001 
		 WHERE Encest = 'on' AND (Encdes LIKE '%".$name_hce."%' 
		OR Encpro LIKE '%".$name_hce."%') ORDER BY Encpro, Encdes";

	mysql_query("SET character_set_results=utf8", $conex);
	$res_conceptos = mysql_query($q_hce,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar conceptos): ".$q_hce." - ".mysql_error());

	$arr_hce = array();
	while($row_hce = mysql_fetch_array($res_conceptos))
	{
		$arr_hce[$row_hce['Encpro']] = array('cod_hce' => $row_hce['Encpro'], 'nombre' => $row_hce['Encdes'], );
	}
	
	return $arr_hce;
}

//------------------------------------------------------------------------------------
//	Funcion que trae toda la informacion relacionada a un formulario de hce, en este caso trae los campos dínamicos de tipo Formula o Numero
//------------------------------------------------------------------------------------
function datos_desde_fhce($conex, $wbasedato_hce, $wcodfhce, $wconsecfhce = null)
{
	$q =  " SELECT Detcon, Detnpa 
			 FROM ".$wbasedato_hce."_000002 
		    WHERE Detpro = '$wcodfhce' 
			 AND (Dettip = 'Numero' OR Dettip = 'Formula' ) 
			 AND Detest = 'on'
			ORDER BY Detcon ";

	mysql_query("SET character_set_results=utf8", $conex);
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	$option_select = "<option selected value=''>Seleccione..</option>";
	
	while($row = mysql_fetch_array($res))
	{
		$selected = $row['Detcon'] == $wconsecfhce ? 'selected' : '';
		$option_select.= "<option value='$row[Detcon]' $selected>$row[Detcon] - $row[Detnpa]</option>";
	}
	
	return $option_select;
}

function  obtenerArrayInfoConceptos($conex, $wemp_pmla, $wbasedato_cliame, $muevenInventario='%')
{
	$q_conceptos = " SELECT Grucod, Grudes, Gruarc, Grumva FROM ".$wbasedato_cliame."_000200
						 WHERE Gruest = 'on'
						   AND Gruser in ('A','H')
						   AND Gruinv LIKE '%".$muevenInventario."%'
						   AND Gruiva <= 0
					 ORDER BY Grudes ";

	$res_conceptos = mysql_query($q_conceptos,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar conceptos): ".$q_conceptos." - ".mysql_error());

	$arr_conceptos = array();
	while($row_conceptos = mysql_fetch_array($res_conceptos))
	{
		$row_conceptos['Grudes'] = str_replace($caracter_ma, $caracter_ok, $row_conceptos['Grudes']);
		$arr_conceptos[trim($row_conceptos['Grucod'])]['nombre']  		= $row_conceptos['Grudes'];
		$arr_conceptos[trim($row_conceptos['Grucod'])]['archivo'] 		= $row_conceptos['Grudes'];
		$arr_conceptos[trim($row_conceptos['Grucod'])]['modificaVal'] 	= $row_conceptos['Grumva'];
	}
	return $arr_conceptos;

}

//------------------------------------------------------------------------------------
//	Funcion que trae toda la informacion relacionada a un concepto de facturacion
//------------------------------------------------------------------------------------
function datos_desde_concepto($wcodcon, $wcodcco = null)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	global $wuse;

	// --> array respuesta
	$data					= array();
	$data['warctar'] 		= '';
	$data['option_select'] 	= '';

	$q =  " SELECT grucod, grudes, gruarc, gruser, grutip, grumva, gruinv, gruabo, grutab, grumca
		FROM ".$wbasedato."_000200
		   WHERE gruest = 'on'
			 AND grucod = '".$wcodcon."'
				 AND gruser in ('A','H')
		ORDER BY grudes ";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);


	$wcodcon = $row['grucod'];   //Codigo del concepto
	$wnomcon = $row['grudes'];   //Nombre del concepto
	$wconser = $row['gruser'];   //Tipo de servicio (P)OS, (H)OSPITALARIO o (A)MBOS
	$wcontab = $row['grutab'];   //Tipo de Abono
	$wconmca = $row['grumca'];   //indica si el concepto mueve caja

	$data['wcontip'] = $row['grutip'];   //Tipo de concepto (P)ropio o (C)ompartido
	$data['warctar'] = $row['gruarc'];   //Archivo para validar las tarifas
	$data['wconabo'] = $row['gruabo'];   //indica si es un concepto de abono
	$data['wconmva'] = $row['grumva'];   //Indica si el valor se puede colocar al momento de grabar el cargo
	$data['wconinv'] = $row['gruinv'];   //Indica si mueve inventarios
	$data['wconser'] = $row['gruser'];   //Tipo de servicio (P)OS, (H)OSPITALARIO o (A)MBOS
	$data['wconmca'] = $row['grumca'];   //indica si el concepto mueve caja
	$wconabo = $data['wconabo'];
	$option_select ='' ;
	$option_select = cargarCcoXCodcon($wcodcon, $wcodcco);
	$data['option_select'] = $option_select;
	
	return $data;
}

//---------------------------------------------------------------------------------------------
// Funcion Obtiene los insumos 
//---------------------------------------------------------------------------------------------

function obtenerArrayInsumos($conex, $wemp_pmla, $wbasedato_movhos, $name, $codcon) {
	
	$esmaterial 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_materiales_mueven_inv');
	$esmedicamento 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_medicamentos_mueven_inv');

	// --> Obtener cual es la tabla de articulos de inventario (en cliame es movhos_000026, para las demas empresas la _000001)
	$info_tabla_articulos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tablaArticulosInventario');
	
	$explode_info_tabla_articulos = explode('|', $info_tabla_articulos);
	$tabla_articulos = $explode_info_tabla_articulos[0];
	$campo_nombre_articulo = $explode_info_tabla_articulos[1];

	$condicion_es_medicamento = " AND Artesm = 'off' ";
	if($codcon == $esmedicamento && $tabla_articulos == 'movhos_000026') {
		$condicion_es_medicamento = " AND Artesm = 'on' ";
	}
	
	$q_ins = "
		SELECT Artcod, ".$campo_nombre_articulo." as nombre_articulo 
			FROM ".$tabla_articulos." LEFT JOIN ".$wbasedato_movhos."_000027 ON (Artuni = Unicod)
		WHERE Artest = 'on' 
		AND (".$campo_nombre_articulo." LIKE '%".$name."%' OR Artcod LIKE '%".$name."%') 
		".$condicion_es_medicamento." 
		ORDER BY nombre_articulo";
	
	
	/**echo "tabla_articulos: $tabla_articulos<br>";
	echo "campo_nombre_articulo: $campo_nombre_articulo<br>";
	echo "esmedicamento: $esmedicamento<br>";
	echo "condicion_es_medicamento: $condicion_es_medicamento<br>";
	echo "esmaterial: $esmaterial<br>";
	echo "$q_ins: $q_ins<br>";
	
	return;
	*/

	mysql_query("SET character_set_results=utf8", $conex);
	$res_ins = mysql_query($q_ins,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar insumos): ".$q_ins." - ".mysql_error());
 
	$arr_ins = array();
 
	while($row_ins = mysql_fetch_array($res_ins))
	{
		$arr_ins[$row_ins['Artcod']] = array('codigo' => $row_ins['Artcod'], 'nombre' => trim($row_ins[1]), );
	}
	
	return $arr_ins; 
}

//----------------------------
function obtenerArrayProcedimientos($conex, $name_proc){

	$q_ins = "SELECT Codigo, Nombre FROM root_000012 WHERE Estado = 'on' AND (Nombre LIKE '%".$name_proc."%' OR Codigo LIKE '%".$name_proc."%') ORDER BY Codigo";

	mysql_query("SET character_set_results=utf8", $conex);
	$res_ins = mysql_query($q_ins,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar insumos): ".$q_ins." - ".mysql_error());
 
	$data = array();
 
	while($row_ins = mysql_fetch_array($res_ins))
	{
		$data[$row_ins['Codigo']] = array('codigo' => $row_ins['Codigo'], 'nombre' => $row_ins['Nombre'], );
	}
	
	return $data; 
}

function obtenerCCAPorId($conex, $wbasedato_cliame, $wbasedato_movhos, $wbasedato_hce, $id) {
	
	$q_cca = "
		SELECT cca.id
		, CONCAT(ccacon, '-', Grudes) as ccacon
		, CONCAT(ccacup, '-', Nombre) as ccacup
		, IF(COALESCE(TRIM(ccacco),'')<>'', CONCAT(ccacco, '-', Cconom), 'No seleccionado') as ccacco
		, CONCAT(ccaart, '-', Artcom) as ccaart
		, ccaeve
		, ccadat
		, ccapre
		, ccaord
		, CONCAT(ccafhce, '-', Encdes) as ccafhce
		, CONCAT(ccachce, '-', Detdes) as ccachce
		, TRIM(BOTH ', ' FROM CONCAT(
				IF(FIND_IN_SET('H',ccatcco), CONCAT('Hospitalizaci&oacute;n',', '), ''),
				IF(FIND_IN_SET('D',ccatcco), CONCAT('Domiciliaria',', '), ''),
				IF(FIND_IN_SET('A',ccatcco), CONCAT('Ayudas',', '), ''),
				IF(FIND_IN_SET('U',ccatcco), CONCAT('Urgencia',', '), ''),
				IF(FIND_IN_SET('Cx',ccatcco), CONCAT('Cirug&iacute;a', ', '), '')
			)) tcco
			FROM ".$wbasedato_cliame."_000341 as cca
			LEFT JOIN ".$wbasedato_cliame."_000200 ON ccacon = Grucod 
			LEFT JOIN ".$wbasedato_movhos."_000011 ON Ccocod = ccacco 
			LEFT JOIN root_000012 ON Codigo = ccacup 
			LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
			LEFT JOIN ".$wbasedato_hce."_000001 ON Encpro = ccafhce 
			LEFT JOIN ".$wbasedato_hce."_000002 ON Detpro = ccafhce AND ccachce = Detcon
			WHERE cca.id = $id";

	$exec_query = mysql_query($q_cca, $conex);
	return mysql_fetch_assoc($exec_query);
	
}

function obtenerArrayListado($conex, $wbasedato_cliame, $wbasedato_movhos, $wbasedato_hce){
	
	$q_ins = "
	SELECT cca.id
			, ccaeve as evento
			, ccadat as dato
			, ccapre as pre
			, ccaord as orden
			, ccacon
			, ccacco
			, ccaart
			, ccafhce
			, ccachce
			, CONCAT(
				IF(FIND_IN_SET('H',ccatcco), CONCAT('Hospitalizaci&oacute;n','<br>'), ''),
				IF(FIND_IN_SET('D',ccatcco), CONCAT('Domiciliaria','<br>'), ''),
				IF(FIND_IN_SET('A',ccatcco), CONCAT('Ayudas','<br>'), ''),
				IF(FIND_IN_SET('U',ccatcco), CONCAT('Urgencia','<br>'), ''),
				IF(FIND_IN_SET('Cx',ccatcco), CONCAT('Cirug&iacute;a', '<br>'), '')
			) cad_ccatcco
			, ccatcco
			, ccacup
			, Grudes as concepto
			, Cconom as costos
			, Nombre as procedimiento
			, Artcom as articulo
			, Encdes as hce
			, Detnpa as conse 
		FROM ".$wbasedato_cliame."_000341 as cca
		LEFT JOIN ".$wbasedato_cliame."_000200 ON ccacon = Grucod 
		LEFT JOIN ".$wbasedato_movhos."_000011 ON Ccocod = ccacco 
		LEFT JOIN root_000012 ON Codigo = ccacup 
		LEFT JOIN ".$wbasedato_movhos."_000026 ON Artcod = ccaart 
		LEFT JOIN ".$wbasedato_hce."_000001 ON Encpro = ccafhce 
	LEFT JOIN ".$wbasedato_hce."_000002 ON Detpro = ccafhce AND ccachce = Detcon";
	
	
	mysql_query("SET character_set_results=utf8", $conex);
	
	$data = array();
	if($res_ins = mysql_query($q_ins,$conex)) {
		while($row_ins = mysql_fetch_array($res_ins))
		{
			$concepto = "$row_ins[ccacon]-". trim($row_ins['concepto']);
			$c_costo = $row_ins['ccacco'] != null ? "$row_ins[ccacco]-". trim($row_ins['costos']) : '';
			$procedimiento = $row_ins['procedimiento'] != null ? "$row_ins[ccacup]-". trim($row_ins['procedimiento']) : '';
			$articulo = $row_ins['ccaart'] != null ? "$row_ins[ccaart]-". trim($row_ins['articulo']) : '';
			$hce = ($row_ins['evento'] == 'on' || $row_ins['dato'] == 'on') ? "$row_ins[ccafhce]-". trim($row_ins['hce']) : ''; 
			$consecutivo = $row_ins['dato'] == 'on' ? "$row_ins[ccachce]-". trim($row_ins['conse']) : '';
			
			$data[$row_ins['id']] = ['concepto' => $concepto
										, 'c_costos' => $c_costo
										, 'procedimiento' => $procedimiento
										, 'articulo' => $articulo
										, 'hce' => $hce
										, 'consecutivo' => $consecutivo
										, 'cad_tipo_cco' => $row_ins['cad_ccatcco']
										, 'tipo_cco' => $row_ins['ccatcco']
										, 'evento' => $row_ins['evento']
										, 'dato' => $row_ins['dato']
										, 'pre' => $row_ins['pre'] 
										, 'orden' => $row_ins['orden']
									];
		}
		
		return [ 'data' => $data, 'code' => 1];
	} else {
		return ['msj' => "Error: " . mysql_errno() . " - en el query (Consultar cargos): ".$q_ins." - ".mysql_error(), 'code' => 0];
	}
}


function eliminarCargo($conex, $wbasedato_cliame, $wbasedato_movhos, $wbasedato_hce, $wuse, $id_cargo){
	
	$json = json_encode(obtenerCCAPorId($conex, $wbasedato_cliame, $wbasedato_movhos, $wbasedato_hce, $id_cargo));
	
	$q_ins = 
	"DELETE FROM ".$wbasedato_cliame."_000341 
		WHERE id = $id_cargo";

	$res_ins = mysql_query($q_ins,$conex);
	
	if($res_ins) {
		logTransaccion($conex, $wbasedato_cliame, $wuse, $json, NULL, NULL, 'on', 'DELETE','cca');
		return [ 'msj' => 'La configuraci&oacute;n de cargo autom&aacute;tico has sido eliminada exitosamente.', 'code' => 1];
	} else {
		logTransaccion($conex, $wbasedato_cliame, $wuse, $json, NULL, "Error: " . mysql_errno() . " - en el query (Eliminar cargo): ".$q_ins." - ".mysql_error(), 'on','DELETE','cca');
		return [ 'msj' => "Error: " . mysql_errno() . " - en el query (Eliminar cargo): ".$q_ins." - ".mysql_error(), 'code' => 0 ];
	}
	return "";
	
}

//----------------------------------------------------------------------------------------------
// Funcion que pinta el seleccionador de centros de costos x código de concepto
//----------------------------------------------------------------------------------------------
function cargarCcoXCodcon($wcodcon, $wcodcco = null)
{
	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	
	// --> opciones que se cargaran en el select de centro de costos
	$q =  " SELECT relconcco
			  FROM ".$wbasedato."_000077
			 WHERE relconcon = '".$wcodcon."'
			   AND relconest = 'on'
			   AND relcontem = '*'
			 GROUP BY relconcco
	";
	$rescco = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numcco = mysql_num_rows($rescco);
	
	// --> Si hay mas de un centro de costos, muestro el campo en blanco por defecto
	if ($numcco > 1) {
		$option_select = "<option selected value='0'>Seleccione..</option>";
	}

	// --> Obtener un array de los cco segun la empresa
	$arrayCco = Obtener_array_cco();

	for ($i=1;$i<=$numcco;$i++)
	{
		$rowcco = mysql_fetch_array($rescco);

		if(array_key_exists($rowcco['relconcco'], $arrayCco)) 
		{
			$nomCco	= $arrayCco[$rowcco['relconcco']];
		} else {
			continue;
		}

		$nomCco = str_replace("?", "N", utf8_decode($nomCco));
		$option_select.= "<option ".(($i==1 && $numcco==1 || ($rowcco['relconcco'] == $wcodcco)) ? 'SELECTED' : '')." value='".$rowcco['relconcco']."'>".$rowcco['relconcco']."-".$nomCco."</option>";
	}
	
	return $option_select;
}

function guardar_configuracion_cargo_automatico($conex, $wbasedato_cliame, $wbasedato_movhos, $wbasedato_hce, $usu, $id, $concepto, $cco, $procedimiento_o_insumo, $fhce, $confhce, $tipo_origen, $procOIns, $tipo_cco) {
	
	$wuse = $usu;
	
	$ccaeve = 'off'; 
	$ccadat = 'off';
	$ccapre = 'off';
	$ccaord = 'off';
	
	$columna_tipo_origen = '';
	
	switch($tipo_origen) {
		case 'evento':
			$ccaeve = 'on';
			$columna_tipo_origen = 'ccaeve';
		break;
		case 'dato':
			$ccadat = 'on';
			$columna_tipo_origen = 'ccadat';
		break;
		case 'preescripcion':
			$ccapre = 'on';
			$columna_tipo_origen = 'ccapre';
		break;
		case 'orden':
			$ccaord = 'on';
			$columna_tipo_origen = 'ccaord';
		break;
	}
	
	$ccacon = $concepto;
	$ccacup = $procOIns == 'procedimiento' ? $procedimiento_o_insumo : '';
	$ccacco = $cco;
	$ccaart = $procOIns == 'insumo' ? $procedimiento_o_insumo : '';
	$ccafhce = $fhce;
	$ccachce = $confhce;
	
	if($ccacon == '') {
		echo json_encode([ "msj" => "El campo Concepto es requerido.", "code" => 0]);
		return;
	}
	
	if(($procOIns == 'procedimiento' &&  $ccacup == '') || ($procOIns == 'insumo' &&  $ccaart == '')) {
		echo json_encode([ "msj" => "El campo Procedimiento o Insumo es requerido.", "code" => 0]);
		return;
	}
	
	if($ccapre == 'on' && $procOIns == 'insumo') {
		$query_existe_articulo = "SELECT CONCAT(Artcod, '-',Artcom) FROM `".$wbasedato_movhos."_000026` WHERE Artcod = '$ccaart'";
		$num_registros = mysql_num_rows(mysql_query($query_existe_articulo, $conex));
		
		if($num_registros == 0) {
			echo json_encode([ "msj" => "Lo sentimos, pero el Art&iacute;culo que estas asociando a esta Prescripci&oacute;n no se encuentra en la base de datos.", "code" => 0]);
			return;
		}
	} else if($ccapre == 'on' && $procOIns == 'procedimiento') {
		echo json_encode([ "msj" => "Lo sentimos, pero un Procedimiento no puede estar asociando a una Prescripci&oacute;n, por favor verifique la informaci&oacute;n.", "code" => 0]);
		return;
	}
	
	if ($ccaord == 'on' && $procOIns == 'procedimiento') {
		$query_existe_procedimiento = "SELECT CONCAT(Codigo, '-',Nombre) procedimiento FROM root_000012 WHERE Codigo='$ccacup'";
		$num_registros = mysql_num_rows(mysql_query($query_existe_procedimiento, $conex));
		
		if($num_registros == 0) {
			echo json_encode([ "msj" => "Lo sentimos, pero el Procedimiento que estas asociando a esta Orden no se encuentra en la base de datos.", "code" => 0]);
			return;
		}
	} else if($ccaord == 'on' && $procOIns == 'insumo') {
		echo json_encode([ "msj" => "Lo sentimos, pero un Art&iacute;culo no puede estar asociando a una Orden, por favor verifique la informaci&oacute;n.", "code" => 0]);
		return;
	}
	
	if(($ccadat == 'on') && ($ccafhce == '' || $ccachce == '')) {
		if($ccafhce == '') {
			echo json_encode([ "msj" => "El campo Formulario HCE es requerido.", "code" => 0]);
			return;
		} else if ($ccachce == '') {
			echo json_encode([ "msj" => "El campo Consecutivo HCE es requerido.", "code" => 0]);
			return;
		}
	}
	
	$condicion_tipo_origen = $columna_tipo_origen != '' ? " $columna_tipo_origen = 'on'" : "";
	$condicion_tcco = str_replace(" FIND", " OR FIND", $condicion_tcco);
	$condicion_tcco = ' AND ('.$condicion_tcco.')';
	$condicion_proc_o_insumo = $procOIns == 'procedimiento' ? " AND ccacup = '$procedimiento_o_insumo' " : " AND ccaart = '$procedimiento_o_insumo' ";
	$condicion_fhce = ($ccaeve == 'on' || $ccadat == 'on') ? " AND ccafhce = '".$ccafhce."' " : "";
	$condicion_chce = ($ccadat == 'on') ? " AND ccachce = '".$ccachce."' " : "";
	// CONSULTA ANTERIOR SIN LA VALIDACION DE TIPO CARGO EVENTO
	//$query_existe_cca = "SELECT id FROM ".$wbasedato_cliame."_000341 WHERE ccacon = '$ccacon' $condicion_proc_o_insumo $condicion_cco $condicion_tipo_origen $condicion_fhce_confhce $condicion_fhce";
		
	$query_existe_cca = "SELECT id, ccatcco
						FROM ".$wbasedato_cliame."_000341 ";

	$query_existe_cca .= " WHERE ($condicion_tipo_origen $condicion_proc_o_insumo $condicion_fhce $condicion_chce $condicion_proc_o_insumo)
								AND IF('".$id."' <> '' AND '".$id."' IS NOT NULL, id <> '".$id."', TRUE)";	
	
	$exec = mysql_query($query_existe_cca, $conex) or die("Error: " . mysql_errno() . " - en el query (Validar CCA Tipo Evento): $query_existe_cca - ".mysql_error());
	$num_reg = mysql_num_rows($exec);	
	$array_tcco = array(
					"H" => "Hospitalizacion",
					"D" => "Domiciliaria",
					"A" => "Ayudas",
					"U" => "Urgencias",
					"Cx" => "Cirugías"
				);
	
	if($num_reg > 0) {
		$row = mysql_fetch_row($exec);	
		$array_tipo_cco_registered = explode(",", $row[1]);	
		$message_val = "Lo sentimos, no puede registrar esta configuraci&oacute;n de cargo autom&aacute;tico de tipo ".($ccaeve == on ? 'evento ' : ($ccadat == on ? 'dato ' : 'orden ')).'porque ya existe una configuraci&oacute;n para el procedimiento/insumo "'.$procedimiento_o_insumo.'"';
		$message_val .= ($ccaeve == on || $ccadat == on) ? ', el formulario HCE "'.$ccafhce.'"' : '';
		$message_val .= ($ccadat == on) ? ', el campo HCE "'.$ccafhce.'"' : '';
		$message_val .= ' y los tipos de centro de costo: "';
		for($i=0; $i < count($array_tipo_cco_registered); $i++){
			$message_val .= $array_tcco[$array_tipo_cco_registered[$i]].(($i < count($array_tipo_cco_registered)-2) ? ', ' : ' y ');
		}
		$message_val = trim($message_val, " y ");
		$message_val .= '".<br><br>Si desea agregar o quitar un tipo de centro de costo a esta configuraci&oacute;n, por favor actualice el registro existente.';		
		echo json_encode([ "msj" => $message_val, "code" => 0]);
		return;
	}
	
	if($id == null){
		
		$query_nuevo_cca = 
		"INSERT INTO ".$wbasedato_cliame."_000341 (Medico, 
			Fecha_data, 
			Hora_data, 
			ccacon, 
			ccacup, 
			ccacco, 
			ccaart, 
			ccaeve, 
			ccadat, 
			ccapre, 
			ccaord, 
			ccafhce, 
			ccachce,
			ccatcco,
			Seguridad) 
			VALUES ('$wbasedato_cliame', now(), now(), '$ccacon', '$ccacup', '$ccacco', '$ccaart', '$ccaeve', '$ccadat', '$ccapre', '$ccaord', '".$ccafhce."', '".$ccachce."', '".$tipo_cco."','C-".$wuse."');";
		$exec = mysql_query($query_nuevo_cca, $conex);
	
		if($exec) {
			echo json_encode([ "msj" => "La configuraci&oacute;n de cargo autom&aacute;tico ha sido almacenada exitosamente.", "code" => 1]);
			
			$query_max_idcca = "SELECT MAX(id) FROM ".$wbasedato_cliame."_000341";
			$exec_max_idcca = mysql_query($query_max_idcca, $conex);
			$data = mysql_fetch_array($exec_max_idcca);
			$json = json_encode(obtenerCCAPorId($conex, $wbasedato_cliame, $wbasedato_movhos, $wbasedato_hce, $data[0]));
			
			logTransaccion($conex, $wbasedato_cliame, $wuse, $json, NULL, NULL, 'on', 'INSERT','cca');
			return;
		} else {
			echo json_encode([ "msj" => "Error: " . mysql_errno() . " - en el query (Guardar CCA ): $query_nuevo_cca - ".mysql_error(), "code" => 0]);
			logTransaccion($conex, $wbasedato_cliame, $wuse, $json, NULL, "Error: " . mysql_errno() . " - en el query (Guardar CCA ): $query_nuevo_cca - ".mysql_error(), 'on', 'INSERT','cca');
			return;
		}
	
	} else {
		
		$json_sin_actualizar = json_encode(obtenerCCAPorId($conex, $wbasedato_cliame, $wbasedato_movhos, $wbasedato_hce, $id));
		
		$query_nuevo_cca = "
			UPDATE ".$wbasedato_cliame."_000341 SET 
				Medico = '".$wbasedato_cliame."',
				Fecha_data = now(),
				Hora_data = now(),
				ccacon = '".$ccacon."', 
				ccacup = '".$ccacup."', 
				ccacco = '".$ccacco."', 
				ccaart = '".$ccaart."', 
				ccaeve = '".$ccaeve."', 
				ccadat = '".$ccadat."', 
				ccapre = '".$ccapre."', 
				ccaord = '".$ccaord."', 
				ccafhce = '".$ccafhce."', 
				ccachce = '".$ccachce."',
				ccatcco = '".$tipo_cco."',
				Seguridad = 'C-".$wuse."'
			WHERE  id = '".$id."' ";
		$exec = mysql_query($query_nuevo_cca, $conex);
		
		if($exec) {
			echo json_encode([ "msj" => "La actualizaci&oacute;n de configuraci&oacute;n de cargo autom&aacute;tico ha sido almacenada exitosamente.", "code" => 1]);
			$json_actualizado = json_encode(obtenerCCAPorId($conex, $wbasedato_cliame, $wbasedato_movhos, $wbasedato_hce, $id));
			logTransaccion($conex, $wbasedato_cliame, $wuse, $json_sin_actualizar, $json_actualizado, NULL, 'on', 'UPDATE','cca');
			return;
		} else {
			echo json_encode([ "msj" => "Error: " . mysql_errno() . " - en el query (Editar CCA ): $query_nuevo_cca - ".mysql_error(), "code" => 0]);
			logTransaccion($conex, $wbasedato_cliame, $wuse, $json_sin_actualizar, $json_actualizado, "Error: " . mysql_errno() . " - en el query (Guardar CCA ): $query_nuevo_cca - ".mysql_error(), 'on', 'UPDATE','cca');
			return;
		}
	}
	
}

//OBTIENE SI EL CONCEPTO ES DE TIPO INVENTARIO O PROCEDIMIENTO 
function detalle_concepto($conex, $cod_concepto, $wbasedato){
	$ConceptoInventar="";
	$q_ConInv = "SELECT Gruinv
				   FROM ".$wbasedato."_000200
				  WHERE Grucod = '".$cod_concepto."'
				";
	$res_ConInv = mysql_query($q_ConInv,$conex) or die("Error en el query: ".$q_ConInv."<br>Tipo Error:".mysql_error());
	if($row_ConInv = mysql_fetch_array($res_ConInv))
	$ConceptoInventar = $row_ConInv['Gruinv'];
	return $ConceptoInventar;
}
//-------------------------------
function guardarCargoAutomaticoFacturacionERP($conex, $wemp_pmla, $use, $whis, $wing, $configCCA) {
		
	$wuse = $use;
	
	$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$Ubisac = consultarUbicacionPacienteHCE($conex, $wbasedato_movhos, $whis, $wing );	
	
	$sql = "SELECT Ccoerp, CASE
							WHEN ccohos = 'on' AND ccodom <> 'on' THEN 'H'
							WHEN ccohos = 'on' AND ccodom = 'on' THEN 'D'
							WHEN ccoayu = 'on' THEN 'A'   
							WHEN ccourg = 'on' THEN 'U'
							WHEN ccocir  = 'on' THEN 'Cx'							
						   END tcco
			  FROM ".$wbasedato_movhos."_000011
			 WHERE ccocod = '".$Ubisac."'
		";	
	
	$resCco = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	
	$numCco = mysql_num_rows( $resCco );
	$CcoErp = false;
	$tcco = "";
	if( $rowsCco = mysql_fetch_array( $resCco) ){
		$CcoErp = $rowsCco[ 'Ccoerp' ] == 'on' ? true: false;
		$tcco = $rowsCco['tcco'];		
	}	
	
	//Si el cco no maneja cargo ERP o no esta activo los cargos ERP no se ejecuta esta accion
	$cargarEnErp = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cargosPDA_ERP" );
	if( !$CcoErp || $cargarEnErp != 'on' ){
		return;
	}
	
	$sql = "SELECT *
			  FROM ".$wbasedato_movhos."_000016
			 WHERE inghis = '".$whis."'
			   AND inging = '".$wing."'
		";
	
	$resRes = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$numRes = mysql_num_rows( $resRes );
	if( $rowsRes = mysql_fetch_array( $resRes) ){
						
		$sql = "SELECT *
				  FROM ".$wbasedato_cliame."_000101
				 WHERE Inghis = '".$whis."'
				   AND Ingnin = '".$wing."'
			";
		
		$resIng = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numIng = mysql_num_rows( $resIng );
	
		if( $rowsIng = mysql_fetch_array( $resIng) ){
				
			$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
		
			if( $rowsIng[ 'Ingtpa' ] == 'P' ){
				$empresa = $codEmpParticular;
			}
			else{
				$empresa = $rowsIng[ 'Ingcem' ];
			}
			
			$sql = "SELECT *
					  FROM ".$wbasedato_cliame."_000024
					 WHERE empcod = '".$empresa."'
					";
		
			$resEmp = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numEmp = mysql_num_rows( $resEmp );
			
			if( $rowsEmp = mysql_fetch_array( $resEmp ) ){
		
				//Informacion de empresa
				$wcodemp 	  = $rowsEmp[ 'Empcod' ];
				$wnomemp 	  = $rowsEmp[ 'Empnom' ];
				$tipoEmpresa  = $rowsEmp[ 'Emptem' ];
				$nitEmpresa   = $rowsEmp[ 'Empnit' ];
				$wtar		  = $rowsEmp[ 'Emptar' ];
			
				//Informacion del paciente
				$tipoPaciente = $rowsIng[ 'Ingcla' ];
				$tipoIngreso  = $rowsIng[ 'Ingtin' ];
				$wser		  = $rowsIng[ 'Ingsei' ];
				$wfecing	  = $rowsIng[ 'Ingfei' ];
				
				//Consulta informacion de pacientes
				$infoPacienteCargos = consultarNombresPaciente( $conex, $whis, $wemp_pmla );
				
				//Conceptos de grabacion
				
				// $configCCA[ccacon] $configCCA[ccaconnom] $configCCA[ccacup] $configCCA[ccacupnom] $configCCA[ccacco] $configCCA[ccaart] $configCCA[ccaartnom] $configCCA[ccaeve] $configCCA[ccadat] $configCCA[Dettip] $configCCA[Detcon]
				$wcodcon = $configCCA['ccacon'];
				$wnomcon = $configCCA['ccaconnom'];
				
				$wexidev = 0;
				
				if($configCCA['ccaeve'] == 'on' || $configCCA['ccaord'] == 'on') {
					$wcantidad = 1;
				} else if($configCCA['ccadat'] == 'on') {
					$wcantidad = in_array($configCCA['Detcon'], explode("|", $configCCA['consecutivos_formulario'])) ? trim($configCCA['movdat']) : '';					
				}
				
				if($configCCA['ccacup'] != '') {
					$procOArt = $configCCA['ccacup'];
					$procOArtNom = $configCCA['ccacupnom'];
				} else if($configCCA['ccaart'] != '') {
					$procOArt = $configCCA['ccaart'];
					$procOArtNom = $configCCA['ccaartnom'];
				}
				
				if($configCCA['ccacco'] != '') {
					$cco = $configCCA['ccacco'];
				} else {
					$cco = $Ubisac;
				}
								
				$q =  " SELECT grucod, grudes, gruarc, gruser, grutip, grumva, gruinv, gruabo, grutab, grumca
						  FROM ".$wbasedato_cliame."_000200
						WHERE gruest = 'on'
						AND grucod = '".$wcodcon."'
						AND gruser in ('A','H')";
				
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				$row = mysql_fetch_array($res);

				$wcontip = $row['grutip'];   //Tipo de concepto (P)ropio o (C)ompartido
				$warctar = $row['gruarc'];   //Archivo para validar las tarifas
				$wconabo = $row['gruabo'];   //indica si es un concepto de abono
				$wconmva = $row['grumva'];   //Indica si el valor se puede colocar al momento de grabar el cargo
				$wconinv = $row['gruinv'];   //Indica si mueve inventarios
				$wconser = $row['gruser'];   //Tipo de servicio (P)OS, (H)OSPITALARIO o (A)MBOS
				$wconmca = $row['grumca'];   //indica si el concepto mueve caja
				
				$wfecha=date("Y-m-d");		
				$whora = date("H:i:s");
				
				//Reemplazo las variables necesarias para la funcion validar_y_grabar_cargo
				$wbasedato = $wbasedato_cliame;
				
				//$dosProc = datos_desde_procedimiento(codigoArticulo, codigoConcepto, wccogra    , ccoActualPac, wcodemp , wfeccar, '', '*', 'on', false, '', fecha  , hora  , '*', '*');
				
				$datosProc = datos_desde_procedimiento( $procOArt  , $wcodcon , $cco, $Ubisac , $wcodemp, $wfecha, '', '*', 'on', false, '', $wfecha, $whora, '*', '*');
				$wvaltar = $datosProc[ 'wvaltar' ];
				
				$datos = array();
				
				//Buscar medico 
				$turno='';
				$especialidad='*';
				$wcodter='';
				$wnomter=''; 
				$wporter=0;
		
				if(($configCCA['ccaeve'] == 'on' || $configCCA['ccadat'] == 'on') && $wcontip=="C"){
					
					$qMec =  "SELECT * FROM ".$wbasedato_movhos."_000048 WHERE `Meduma` = '".$wuse."'";
					$res = mysql_query($qMec,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res);
					$row = mysql_fetch_array($res);
					
					$porcter = datos_desde_conceptoxcco($wcodcon,$cco);
					
					$wporter = $porcter['porcentajes'] == 'nada' ? 0 : $porcter['porcentajes'];
					
					if($num>0){
						$wcodter=$row['Meddoc']; //obtiene la cedula y especialidad este o no enturno
						$wnomter = $row['Medno1'].' '.$row['Medno2'].' '.$row['Medap1'].' '.$row['Medap2'];
						$especialidad=$row['Medesp'];
						
						$turno='off';
						$fechaActual = date('Y-m-d');
						$qMec = "SELECT Agecme
										,Ageces
										,Agetur
										,Turhin
										,Turhfi
										,Turdes
										,Agefec
										,Medno1
										,Medno2
										,Medap1
										,Medap2
									FROM ".$wbasedato_movhos."_000125 INNER JOIN ".$wbasedato_movhos."_000048 ON ".$wbasedato_movhos."_000125.Agecme=".$wbasedato_movhos."_000048.Meddoc 
										INNER JOIN ".$wbasedato_movhos."_000126 ON ".$wbasedato_movhos."_000126.Turcod=".$wbasedato_movhos."_000125.Agetur 
										WHERE ".$wbasedato_movhos."_000048.Meduma = '".$wuse."'  and ".$wbasedato_movhos."_000125.Agefec='".$fechaActual."'";	
						$res = mysql_query($qMec,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);
						$row = mysql_fetch_array($res);
						
						if($num>0) {
							
							$especialidad = $row['Ageces'];
							$time = time();
							$hora_actual = date("H:i:s", $time);
							
							$hora_turno_inicio_strtorime = strtotime($row['Turhin']);
							$hora_turno_fin_strtorime = strtotime($row['Turhfi']);
							$hora_actual_strtotime = strtotime($hora_actual);
							
							if($hora_actual_strtotime >= $hora_turno_inicio_strtorime && $hora_actual_strtotime <= $hora_turno_fin_strtorime){
								$turno='on'; 
							}
						}
					}
				}
				
				$datos['whistoria']				=$whis; // $whistoria;
				$datos['wing']					=$wing; // $wing;
				$datos['wno1']					=$infoPacienteCargos['Pacno1']; // $wno1;
				$datos['wno2']					=$infoPacienteCargos['Pacno2']; // $wno2;
				$datos['wap1']					=$infoPacienteCargos['Pacap1'];
				$datos['wap2']					=$infoPacienteCargos['Pacap2'];
				$datos['wdoc']					=$infoPacienteCargos['Oriced']; // $wdoc;
				$datos['wcodemp']				=$wcodemp;	//				--> cliame_000024
				$datos['wnomemp']				=$wnomemp;	//			--> cliame_000024
				$datos['tipoEmpresa']			=$tipoEmpresa;	//			--> cliame_000024
				$datos['nitEmpresa']			=$nitEmpresa;	//			--> cliame_000024
				$datos['tipoPaciente']			=$tipoPaciente;	//		--> cliame_000101 Ingcla
				$datos['tipoIngreso']			=$tipoIngreso;	//		--> cliame_000101 Ingtin
				$datos['wfecing']				=$wfecing;		//		--> cliame_000101 Ingfei
				$datos['wtar']					=$wtar;			//		--> cliame_000024
				$datos['wser']					=$wser;			//		--> cliame_000101 Ingsei
				$datos['wcodcon']				=$wcodcon;		//		--> Codigo del concepto (0626 = materiales, 0616 = medicamentos)
				$datos['warctar'] 				=$warctar;
				$datos['wnomcon']				=$wnomcon;		//		--> Nombre del concepto Cliame 200
				$datos['wprocod']				=$procOArt; // $wprocod;				--> Codigo del articulo o del medicamento
				$datos['wpronom']				=$procOArtNom;// $wpronom;				--> Nombre del articulo Artcom
				$datos['wcodter']				=$wcodter; // $wcodter;				--> ''
				$datos['wnomter']				=$wnomter; //$wnomter;				--> ''
				$datos['wporter']				=$wporter; // $wporter;				--> '' 0
				//$datos['codigoPolitica'] 		= ''; PARAMETRO FALTANTE
				$datos['grupoMedico']			=''; // $grupoMedico;			--> ''
				$datos['wterunix']				=$wcodter; // $wterunix;				--> ''
				$datos['wcantidad']				= $wcantidad; //$wcantidad;			--> cantidad
				$datos['wvaltar']				=$wvaltar;	//			--> valor PENDIENTE FUNCION
				//$datos['porDescuento'] 		= 0; PARAMETRO FALTANTE
				$datos['wrecexc']				='R'; // $wrecexc;				--> 'R'
				$datos['wfacturable']			='S'; // $wfacturable;			--> 'S'
				//$datos['aplicarRecago'] 		= 'on'; PARAMETRO FALTANTE
				$datos['wcco']					=$cco;	// $wcco;					--> Centro de costos graba
				$datos['wccogra']				=$cco;// $wccogra;				--> cco paciente
				$datos['wfeccar']				=$wfecha; // $wfeccar;				--> Fecha del cargo
				$datos['whora_cargo']			=$whora; // $whora_cargo.':00';	-->	Hora del cargo
				$datos['wconinv']				=$wconinv; //$wconinv;				--> 'on'
				$datos['wconabo']				=$wconabo; //$wconabo;				--> ''
				$datos['wdevol']				='off'; // $wdevol;				--> 'off'
				$datos['waprovecha']			='off'; // $waprovecha;			--> 'off'
				$datos['wconmvto']				=''; //$wconmvto;				--> ''
				$datos['wexiste']				=$datosProc[ 'wexiste' ];	//				--> cantidad existente PENDIENTE FUNCION
				$datos['wbod']					='off'; //$wbod;					--> 'off'
				$datos['wconser']				='H'; //$wconser;				--> 'H'
				//$datos['wtipfac']				=$wtipfac;				--> tipo facturacion PENDIENTE FUNCION
				$datos['wtipfac']				="CODIGO";	//			--> tipo facturacion PENDIENTE FUNCION
				$datos['wespecialidad']			= $especialidad;
				//$datos['whora_cargo']			= $whora; PARAMETRO FALTANTE
				$datos['nomCajero']				=''; //$nomCajero;			--> ''
				$datos['cobraHonorarios']		= ''; // $cobraHonorarios;			--> ''
				$datos['wgraba_varios_terceros']= ''; // $wgraba_varios_terceros;		''
				$datos['estaEnTurno']			= $turno; // $estaEnTurno;					'' off
				$datos['tipoCuadroTurno']		= ''; // $tipoCuadroTurno;				''
				$datos['ccoActualPac']			= $Ubisac; //$ccoActualPac;				--> Centro de costos actual del paciente	
				//$datos['tarifaDigitada']		= 'no'; PARAMETRO FALTANTE
				$datos['codHomologar']			= ''; // $codHomologar;				--> ''	
				//$datos['loteVacuna']			= ''; PARAMETRO FALTANTE
				//$datos['codigoRips']			= ''; PARAMETRO FALTANTE
				
				$datos['wexidev']				=$wexidev;	//			--> 0 
				$datos['wfecha']				=$wfecha;	//				--> fecha act
				$datos['whora']					=$whora;	//			--> hora act
				$datos['wcodcedula']			=''; // $wcodcedula;					''
				$datos['validarCondicMedic']	=true;	//						--> FALSE
				$datos['estadoMonitor']			='';
				$datos['respuesta_array']		= 'on';
				
				$datos['numCargoInv']			= '';
				$datos['linCargoInv']			= '';
				
				//Esto es nuevo
				$datos['desde_CargosPDA']		= false;
			
				$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');

				// --> Si la empresa es particular esto se graba como excedente
				if($wcodemp == $codEmpParticular) {
					$datos['wrecexc'] = 'R';	//Septiembre 11 de 2017
				}

				// --> Valor excedente
				if($datos['wrecexc'] == 'E') {
					$datos['wvaltarExce'] = !empty($wcantidad) ? round($wcantidad*$wvaltar) : 0;
				// --> Valor reconocido
				} else {					
					$datos['wvaltarReco'] = !empty($wcantidad) ? round($wcantidad*$wvaltar) : 0;
				}
				
				$tipo_cargo = '';
				if ($configCCA['ccaeve'] == 'on') {
					$logTip ='ccaeve';
					$tipo_cargo = 'Evento';
				} else if ($configCCA['ccadat'] == 'on') {
					$logTip ='ccadat';
					$tipo_cargo = 'Dato';
				} else if ($configCCA['ccaord'] == 'on'){
					$logTip = 'ccaord';
					$tipo_cargo = 'Orden';
				}
				
				if(array_search($tcco, explode(',', $configCCA['ccatcco'])) === false){
					$respuesta['Mensajes'] = [ 'error' => 1, 'mensaje' => 'El cargo automatico no se realiz&oacute;, porque no existe una configuraci&oacute;n para la ubicaci&oacute;n del paciente.'];
					$json_datos = json_encode($datos);
					logTransaccion($conex, $wbasedato_cliame, $wuse, $json_datos, NULL, json_encode($respuesta['Mensajes']), 'on', 'INSERT', $logTip);
					return;
				}				

				if($configCCA['ccadat'] == 'on' && ($datos['wcantidad'] == null || $datos['wcantidad'] == 0 || $datos['wcantidad'] == '' || !in_array($configCCA['Detcon'], explode("|", $configCCA['consecutivos_formulario'])))) {
					$Detnpa = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($configCCA['Detnpa']));
					$respuesta['Mensajes'] = [ 'error' => 1, 'mensaje' => 'El campo con consecutivo '.$configCCA['Detcon'].'-'.$Detnpa.' de tipo '.$configCCA['Dettip'].' del formulario '.$configCCA['wformulario'].' tiene un valor null o vac&iacute;o. ' ];
					$json_datos = json_encode($datos);
					
					logTransaccion($conex, $wbasedato_cliame, $wuse, $json_datos, NULL, json_encode($respuesta['Mensajes']), 'on', 'INSERT', $logTip);
					return;
				}
				
				// Validamos la cantidad y la tarifa para que no se hagan cargos que tengan cantidades en 0 y tarifas en 0				
				if( $datos['wcantidad'] != null && $datos['wcantidad'] != 0 && $datos['wcantidad'] != '' && $wvaltar > 0) {
				
					//Llamo la funcion de cargos de CARGOS DE ERP
					$respuesta = validar_y_grabar_cargo($datos, false);
					
					if($respuesta['Mensajes']['error'] == 0) {
						$datos['idCargo'] = $respuesta['Mensajes']['idCargo'];
					} 
				}
				
				if($wvaltar == 0) {
					
					$paciente = $infoPacienteCargos['Pacno1']." ".$infoPacienteCargos['Pacno2']." ".$infoPacienteCargos['Pacap1']." ".$infoPacienteCargos['Pacap2'];
					$paciente_ced = $infoPacienteCargos['Oriced'];
					
					$wasunto = "Cargo Automatico (".$tipo_cargo.") - Tarifa No Definida";
					$detalle1 = "Cargo Automatico (".$tipo_cargo.") - Tarifa No Definida";
					
					$info_formulario = !is_null($configCCA['wformulario']) ? '\n Formulario: '.$configCCA['wformulario'] : '';
					
					$detalle2 = "El siguiente cargo automatico no tiene tarifa definida, por lo tanto no se realizo. \n Historia: ".$whis."-".$wing."\n Paciente:".$paciente." Documento:".$paciente_ced."\n Responsable: ".$wnomemp." - ".$nitEmpresa." Cod: ".$wcodemp." Tarifa: ".$wtar."\n Procedimiento o Articulo: ".$procOArtNom." - ".$procOArt." \n Concepto: ".$wcodcon.$info_formulario;
					
					$respuesta['Mensajes'] = [ 'error' => 1, 'mensaje' => 'El cargo autom&aacute;tico no se realizo, porque no tiene tarifa definida.' ];
					
					enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, '');
					
				}			
				
				$json_datos = json_encode($datos);
				logTransaccion($conex, $wbasedato_cliame, $wuse, $json_datos, NULL, json_encode($respuesta['Mensajes']), 'on', 'INSERT', $logTip);
								
				//echo "<h3>".$respuesta['Mensajes']['mensaje']."</h3>";
				//return;
			}
			//else{ echo "<h1>empresa</h1>" ;}
		}
		//else{ echo "<h1>ingreso cliame</h1>" ;}
	}
	//else{ echo "<h1>ingreso movhos</h1>" ;}
}


//                                             movhos    01 425850 29     1183 R3BB04       0.008      0110734
function guardarCargoAutomaticoPreescripcion($conex, $basedato, $emp, $his, $ing, $cco, $medicamento, $cantidad, $usuario) {

	$wcliame = consultarAliasPorAplicacion($conex, $emp, 'facturacion');
	$Ubisac = consultarUbicacionPacienteHCE($conex, $basedato, $his, $ing );
	
	$sql = "SELECT Ccoerp
			  FROM ".$basedato."_000011
			 WHERE ccocod = '".$Ubisac."'
			";
	
	$resCco = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$numCco = mysql_num_rows( $resCco );
	$CcoErp = false;
	
	if( $rowsCco = mysql_fetch_array( $resCco) ){
		$CcoErp = $rowsCco[ 'Ccoerp' ] == 'on' ? true: false;
	}
	
	//Si el cco no maneja cargo ERP o no est� activo los cargos ERP no se ejecuta esta acci�n
	$cargarEnErp = consultarAliasPorAplicacion( $conex, $emp, "cargosPDA_ERP" );
	if( !$CcoErp || $cargarEnErp != 'on' ){
		return;
	}
	
	$sql = "SELECT *
			  FROM ".$basedato."_000016
			 WHERE inghis = '".$his."'
			   AND inging = '".$ing."'
		";
	
	$resRes = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$numRes = mysql_num_rows( $resRes );
	if( $rowsRes = mysql_fetch_array( $resRes) ){
		
				
		$sql = "SELECT *
				  FROM ".$wcliame."_000101
				 WHERE Inghis = '".$his."'
				   AND Ingnin = '".$ing."'
			";
		
		$resIng = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numIng = mysql_num_rows( $resIng );
	
		if( $rowsIng = mysql_fetch_array( $resIng) ){
		
			
			$codEmpParticular = consultarAliasPorAplicacion($conex, $emp, 'codigoempresaparticular');
		
			if( $rowsIng[ 'Ingtpa' ] == 'P' ){
				$empresa = $codEmpParticular;
			}
			else{
				$empresa = $rowsIng[ 'Ingcem' ];
			}
			
			$sql = "SELECT *
					  FROM ".$wcliame."_000024
					 WHERE empcod = '".$empresa."'
					";
		
			$resEmp = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numEmp = mysql_num_rows( $resEmp );
			
			if( $rowsEmp = mysql_fetch_array( $resEmp ) ){
		
				//Informaci�n de empresa
				$wcodemp 	  = $rowsEmp[ 'Empcod' ];
				$wnomemp 	  = $rowsEmp[ 'Empnom' ];
				$tipoEmpresa  = $rowsEmp[ 'Emptem' ];
				$nitEmpresa   = $rowsEmp[ 'Empnit' ];
				$wtar		  = $rowsEmp[ 'Emptar' ];
			
				//Informaci�n del paciente
				$tipoPaciente = $rowsIng[ 'Ingcla' ];
				$tipoIngreso  = $rowsIng[ 'Ingtin' ];
				$wser		  = $rowsIng[ 'Ingsei' ];
				$wfecing	  = $rowsIng[ 'Ingfei' ];
				
				//Consulta informaci�n de pacientes
				$infoPacienteCargos = consultarNombresPaciente( $conex, $his, $emp );
				
				//Conceptos de grabaci�n
				$wcodcon = consultarAliasPorAplicacion( $conex, $emp, "concepto_medicamentos_mueven_inv" );
				//if( esMMQ($medicamento) )
					//$wcodcon = consultarAliasPorAplicacion( $conex, $emp, "concepto_materiales_mueven_inv" );
				
				$wnomcon = consultarNombreConceptos( $conex, $wcliame, $wcodcon );
				
				$wexidev = 0;
				
				$wcantidad = $cantidad;
				
				$wfecha=date("Y-m-d");		
				$whora = date("H:i:s");
				
				//Reemplazo las variables necesarias para la funci�n validar_y_grabar_cargo
				$auxWbasedato = $wbasedato;
				$wbasedato = $wcliame;
				$wuse = $usuario;
				
				//$dosProc = datos_desde_procedimiento(codigoArticulo, codigoConcepto, wccogra    , ccoActualPac, wcodemp , wfeccar, '', '*', 'on', false, '', fecha  , hora  , '*', '*');
				$datosProc = datos_desde_procedimiento( $medicamento  , $wcodcon      , $cco, $ubi_pac->servicioActual , $wcodemp, $wfecha, '', '*', 'on', false, '', $wfecha, $whora, '*', '*');
				
				$info_tabla_articulos = consultarAliasPorAplicacion($conex, $emp, 'tablaArticulosInventario');

				$explode_info_tabla_articulos = explode('|', $info_tabla_articulos);
				$tabla_articulos = $explode_info_tabla_articulos[0];
				$campo_nombre_articulo = $explode_info_tabla_articulos[1];

				$q_ins = "
					SELECT Artcod, ".$campo_nombre_articulo." as nombre_articulo 
						FROM ".$tabla_articulos." 
					WHERE Artest = 'on' AND Artcod = '".$medicamento."'";
				
				$query_art = mysql_query($q_ins, $conex);
				$nom_art = mysql_fetch_array($query_art);
				
				$wvaltar = $datosProc[ 'wvaltar' ];
				
				$wdevol = 'off';
				
				$datos=array();
				$datos['whistoria']		=$his; // $whistoria;
				$datos['wing']			=$ing; // $wing;
				$datos['wno1']			=$infoPacienteCargos['Pacno1']; // $wno1;
				$datos['wno2']			=$infoPacienteCargos['Pacno2']; // $wno2;
				$datos['wap1']			=$infoPacienteCargos['Pacap1'];
				$datos['wap2']			=$infoPacienteCargos['Pacap2'];
				$datos['wdoc']			=$infoPacienteCargos['Oriced']; // $wdoc; FALTA
				$datos['wcodemp']		=$wcodemp;	//				--> cliame_000024
				$datos['wnomemp']		=$wnomemp;	//			--> cliame_000024
				$datos['tipoEmpresa']	=$tipoEmpresa;	//			--> cliame_000024
				$datos['nitEmpresa']	=$nitEmpresa;	//			--> cliame_000024
				$datos['tipoPaciente']	=$tipoPaciente;	//		--> cliame_000101 Ingcla
				$datos['tipoIngreso']	=$tipoIngreso;	//		--> cliame_000101 Ingtin
				$datos['wser']			=$wser;			//		--> cliame_000101 Ingsei
				$datos['wfecing']		=$wfecing;		//		--> cliame_000101 Ingfei
				$datos['wtar']			=$wtar;			//		--> cliame_000024
				$datos['wcodcon']		=$wcodcon;		//		--> Codigo del concepto (0626 = materiales, 0616 = medicamentos)
				$datos['wnomcon']		=$wnomcon;		//		--> Nombre del concepto Cliame 200
				$datos['wprocod']		=$medicamento; // $wprocod;				--> Codigo del articulo o del medicamento FALTA
				$datos['wpronom']		=$nom_art['nombre_articulo'];// $wpronom;				--> Nombre del articulo Artcom FALTA
				$datos['wcodter']		=''; // $wcodter;				--> ''
				$datos['wnomter']		=''; //$wnomter;				--> ''
				$datos['wporter']		=''; // $wporter;				--> ''
				$datos['grupoMedico']	=''; // $grupoMedico;			--> ''
				$datos['wterunix']		=''; // $wterunix;				--> ''
				$datos['wcantidad']		=$wcantidad; //$wcantidad;			--> cantidad
				$datos['wvaltar']		=$wvaltar;	//			--> valor PENDIENTE FUNCION
				$datos['wrecexc']		='R'; // $wrecexc;				--> 'R'
				$datos['wfacturable']	='S'; // $wfacturable;			--> 'S'
				$datos['wcco']			=$cco;	// $wcco;					--> Centro de costos graba
				$datos['wccogra']		=$cco;// $wccogra;				--> cco paciente
				$datos['wfeccar']		=$wfecha; // $wfeccar;				--> Fecha del cargo
				$datos['whora_cargo']	=$whora; // $whora_cargo.':00';	-->	Hora del cargo
				$datos['wconinv']		='on'; //$wconinv;				--> 'on'
				$datos['wconabo']		=''; //$wconabo;				--> ''
				$datos['wdevol']		=$wdevol; // $wdevol;				--> 'off'
				$datos['waprovecha']	='off'; // $waprovecha;			--> 'off'
				$datos['wconmvto']		=''; //$wconmvto;				--> ''
				//$datos['wexiste']		=$wexiste;				--> cantidad existente PENDIENTE FUNCION
				$datos['wexiste']		=$datosProc[ 'wexiste' ];	//				--> cantidad existente PENDIENTE FUNCION
				$datos['wbod']			='off'; //$wbod;					--> 'off'
				$datos['wconser']		='H'; //$wconser;				--> 'H'
				//$datos['wtipfac']		=$wtipfac;				--> tipo facturacion PENDIENTE FUNCION
				$datos['wtipfac']		="CODIGO";	//			--> tipo facturacion PENDIENTE FUNCION
				$datos['wexidev']		=$wexidev;	//			--> 0 
				$datos['wfecha']		=$wfecha;	//				--> fecha act
				$datos['whora']			=$whora;	//			--> hora act
				$datos['nomCajero']		=''; //$nomCajero;			--> ''
				$datos['cobraHonorarios']		= ''; // $cobraHonorarios;			--> ''
				$datos['wespecialidad']			= '*';
				$datos['wgraba_varios_terceros']= ''; // $wgraba_varios_terceros;		''
				$datos['wcodcedula']			= ''; // $wcodcedula;					''
				$datos['estaEnTurno']			= ''; // $estaEnTurno;					''
				$datos['tipoCuadroTurno']		= ''; // $tipoCuadroTurno;				''
				$datos['ccoActualPac']			= $Ubisac; //$ccoActualPac;				--> Centro de costos actual del paciente	
				$datos['codHomologar']			= ''; // $codHomologar;				--> ''	
				$datos['validarCondicMedic']	= true;	//						--> FALSE
				$datos['estadoMonitor']			= '';
				$datos['respuesta_array']			= 'on';
				$datos['numCargoInv']			= '';
				$datos['linCargoInv']			= '';
				
				//Esto es nuevo
				$datos['desde_CargosPDA']			= 'off';

				//$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
				$codEmpParticular = consultarAliasPorAplicacion($conex, $emp, 'codigoempresaparticular');

				// --> Si la empresa es particular esto se graba como excedente
				if($wcodemp == $codEmpParticular)
					$datos['wrecexc'] = 'R';	//Septiembre 11 de 2017

				// --> Valor excedente
				if($datos['wrecexc'] == 'E')
					$datos['wvaltarExce'] = round($wcantidad*$wvaltar);
				// --> Valor reconocido
				else
					$datos['wvaltarReco'] = round($wcantidad*$wvaltar);
				
				//Llamo la funci�n de cargos de CARGOS DE ERP
				$respuesta = validar_y_grabar_cargo($datos, false);
				
				if($respuesta['Mensajes']['error'] == 0){
					$datos['idCargo'] = $respuesta['Mensajes']['idCargo'];
				} 
				
				$respuesta['Mensajes']['mensaje'] = htmlspecialchars ($respuesta['Mensajes']['mensaje'] );
				
				echo var_dump($respuesta['Mensajes']);
				
				$json_datos = json_encode($datos);
				logTransaccion($conex, $wcliame, $wuse, $json_datos, NULL, json_encode($respuesta['Mensajes']), 'on', 'INSERT', "ccapre");
				// var_dump( $respuesta );
				
				//echo "<h1>"; var_dump( $respuesta ); echo "</h1>";
				//Dejo las variables como estaban
				$wbasedato = $auxWbasedato;
			}
			//else{ echo "<h1>empresa</h1>" ;}
		}
		//else{ echo "<h1>ingreso cliame</h1>" ;}
	}
	//else{ echo "<h1>ingreso movhos</h1>" ;}
		
	
}

function obtenerInfoConcepto($conex, $wbasedato_cliame, $codcon) {
	$info_concepto = 
				"SELECT Grutip, Gruinv
					FROM ".$wbasedato_cliame."_000200
				 WHERE Grucod = '".$codcon."' ";
				 
	$row = mysql_query($info_concepto,$conex) or die("Error en el query: ".$ifno_concepto."<br>Tipo Error:".mysql_error());
	
	return mysql_fetch_assoc($row);
}
//----------------------------------------------------------------------------------------------------------------------------
function logTransaccion($conex, $wbasedato_cliame, $use, $logDes, $logDes2, $logErr, $logEst, $logTip, $logTipo, $logHTML = NULL) {
	
	$logHTML = $logHTML != NULL ? str_replace("'", '"', $logHTML) : $logHTML;
	
	$Logins = 'off';
	$Logupd = 'off';
	$Logdel = 'off';
	
	switch($logTip) {
		case 'INSERT': 
			$Logins = 'on';
		break;
		case 'UPDATE': 
			$Logupd = 'on';
		break;
		case 'DELETE': 
			$Logdel = 'on';
		break;
		
	}
	
	$q_log = 
	"INSERT INTO ".$wbasedato_cliame."_000342 (
						Medico		   , Fecha_data , Hora_data , Logusu	, 	Logdes	   ,  Logdes2	   ,	Loghtml   ,	 Logtip		  , 			Logerr					   ,	Logest	  ,	  Logins	, 	Logupd     , 	Logdel	  ,  Seguridad) 
		VALUES ('".$wbasedato_cliame."', now()	    , now()		, '".$use."', '".mysql_real_escape_string($logDes)."', '".$logDes2."','".mysql_real_escape_string($logHTML)."', '".$logTipo."', '".mysql_real_escape_string($logErr)."', '".$logEst."','".$Logins."', '".$Logupd."', '".$Logdel."', 'C-".$use."');";
	
	$res_log = mysql_query($q_log, $conex);
	
	if(!$res_log) {
		throw new Exception("Error: " . mysql_errno() . " - en el query (Insertar Log)");
	}
	
}

function obtenerLogTransaccion($conex, $wbasedato_cliame, $esCCA, $fecha) {
	
	$Fecha_data = $fecha != '' ? $fecha : date('Y-m-d');
	$Logtip = '';
	
	switch($esCCA) {
		case 'cca':
			$Logtip = 'cca';
		break;
		case 'ccaeve':
			$Logtip = 'ccaeve';
		break;
		case 'ccadat':
			$Logtip = 'ccadat';
		break;
		case 'ccaord':
			$Logtip = 'ccaord';
		break;
		case 'ccapre':
			$Logtip = 'ccapre';
		break;
		case 'estancia':	
			$Logtip = 'estancia';
		break;
	}
	
	$condicion_Logtip = $Logtip != '' ? " AND Logtip = '".$Logtip."' " : '';
	$condicion_Fecha_data = $fecha != '' ? " AND Fecha_data = '".$Fecha_data."' " : '';
	$condicion_limit = $esCCA == '' && $fecha == '' ? ' LIMIT 100 ' : '';
	
	$query_logs = "SELECT Medico as medico, Fecha_data as fecha, Hora_data as hora, Logusu as usuario, Logdes, Logdes2, Logtip, Logerr, Logins, Logupd, Logdel, Seguridad 
					FROM ".$wbasedato_cliame."_000342 
				   WHERE Logest = 'on'  ".$condicion_Logtip." ".$condicion_Fecha_data." 
				    ORDER BY id DESC ".$condicion_limit;
	$exec_query_logs = mysql_query($query_logs, $conex); 
	
	$data = array();
	while( $row = mysql_fetch_assoc($exec_query_logs)) {
		
		$logDes = json_decode($row['Logdes'], true);
		$logDes2 = json_decode($row['Logdes2'], true);
		
		if($row['Logtip'] == 'cca') {
			
			$tipo_cargo = '';
			
			if($logDes['ccaeve'] == 'on') {
				$tipo_cargo = 'evento';
			} else if($logDes['ccadat'] == 'on') {
				$tipo_cargo = 'dato';
			} else if($logDes['ccapre'] == 'on') {
				$tipo_cargo = 'prescripcion';
			} else if($logDes['ccaord'] == 'on') {
				$tipo_cargo = 'orden';
			}
			
			$logDes['ccato'] = $tipo_cargo;
			unset($logDes['ccaeve']);
			unset($logDes['ccadat']);
			unset($logDes['ccapre']);
			unset($logDes['ccaord']);
			
			$tipo_cargo = '';
			
			if($logDes2['ccaeve'] == 'on') {
				$tipo_cargo = 'evento';
			} else if($logDes2['ccadat'] == 'on') {
				$tipo_cargo = 'dato';
			} else if($logDes2['ccapre'] == 'on') {
				$tipo_cargo = 'prescripcion';
			} else if($logDes2['ccaord'] == 'on') {
				$tipo_cargo = 'orden';
			}
			
			$logDes2['ccato'] = $tipo_cargo;
			unset($logDes2['ccaeve']);
			unset($logDes2['ccadat']);
			unset($logDes2['ccapre']);
			unset($logDes2['ccaord']);
		}
		
		$tipo_transaccion = '';
		if($row['Logins'] == 'on') {
			$tipo_transaccion = 'Insertar';
		} else if($row['Logupd'] == 'on') {
			$tipo_transaccion = 'Actualizar';
		} else if($row['Logdel'] == 'on') {
			$tipo_transaccion = 'Eliminar';
		}
		
		$row['tipoTransaccion'] = $tipo_transaccion;
		unset($logDes['Logins']);
		unset($logDes['Logupd']);
		unset($logDes['Logdel']);
		
		$row['Logdes'] = $logDes;
		$row['Logdes2'] = $logDes2;
		$row['Logerr'] = json_decode($row['Logerr'], true);
		
		$data[] = $row;
	}
	
	return $data;
	
}

function obtenerLogTransaccionHTML($conex, $wbasedato_cliame, $esCCA, $fecha) {
	
	$Fecha_data = $fecha != '' ? $fecha : date('Y-m-d');
	$Logtip = '';
	
	switch($esCCA) {
		case 'cca':
			$Logtip = 'cca';
		break;
		case 'ccaeve':
			$Logtip = 'ccaeve';
		break;
		case 'ccadat':
			$Logtip = 'ccadat';
		break;
		case 'ccaord':
			$Logtip = 'ccaord';
		break;
		case 'ccapre':
			$Logtip = 'ccapre';
		break;
		case 'estancia':	
			$Logtip = 'estancia';
		break;
	}
	
	$condicion_Logtip = $Logtip != '' ? " AND Logtip = '".$Logtip."' " : '';
	$condicion_Fecha_data = $fecha != '' ? " AND Fecha_data = '".$Fecha_data."' " : '';
	
	$condicion_limit = $esCCA == '' && $fecha == '' ? ' LIMIT 100 ' : '';
	
	$query_logs = "SELECT Medico as medico, Fecha_data as fecha, Hora_data as hora, Logusu as usuario, Logdes, Logdes2, Logtip, Logerr, Logins, Logupd, Logdel, Seguridad 
					FROM ".$wbasedato_cliame."_000342 
				   WHERE Logest = 'on'  ".$condicion_Logtip." ".$condicion_Fecha_data." 
				    ORDER BY Fecha_data DESC, Hora_data DESC ".$condicion_limit;
	$exec_query_logs = mysql_query($query_logs, $conex); 
	
	$html = "<table id='table_logs' class='display' style='width:100%'>
				<thead>
					<tr class='encabezadoTabla' style='font-size: 10pt;' align='center'>
						<th>#</th>
						<th>Fecha</th>
						<th>Hora</th>
						<th>Usuario</th>
						<th>Detalle</th>
						<th>Detalle 2</th>
						<th>Tipo</th>
						<th>Acci&oacute;n</th>
						<th>Notas</th>
					</tr>
				</thead>";
	$contador = 1;
	while( $row = mysql_fetch_assoc($exec_query_logs)) {
		
		$class = $contador%2 == 0 ? 'fila1' : 'fila2';
		
		$Logdes = json_decode( $row['Logdes'], true);
		$Logdes2 = json_decode( $row['Logdes2'], true);
		$Logerr = json_decode( $row['Logerr'], true);
		
		$detalle = '';
		$detalle2 = '';
		$tipo_log = '';
		$notas = '';
		if( $row['Logtip'] == 'cca' ) {
			
			$Logdes['ccacco'] = !is_null($Logdes['ccacco']) && $Logdes['ccacco'] != '' ? $Logdes['ccacco'] : 'No seleccionado';
			$Logdes2['ccacco'] = !is_null($Logdes2['ccacco']) && $Logdes2['ccacco'] != '' ? $Logdes2['ccacco'] : 'No seleccionado';
			
			$detalle  = '<strong>Concepto:</strong> '.$Logdes['ccacon'].'<br>';
			$detalle .= '<strong>C. Costos:</strong> '.$Logdes['ccacco'].'<br>';
			$detalle .= '<strong>Tipo Cen. Costos:</strong> '.$Logdes['tcco'].'<br>';
			$detalle .= !is_null($Logdes['ccacup'])  ? '<strong>Procedimiento:</strong> '.$Logdes['ccacup'].'<br>'   : '';
			$detalle .= !is_null($Logdes['ccaart'])  ? '<strong>Art&iacute;culo:</strong> '.$Logdes['ccaart'].'<br>' : '';
			$detalle .= !is_null($Logdes['ccafhce']) ? '<strong>Formulario HCE:</strong> '.$Logdes['ccafhce'].'<br>' : '';
			$detalle .= !is_null($Logdes['ccachce']) ? '<strong>Campo HCE:</strong> '.$Logdes['ccachce'].'<br>' 	 : '';
			
			$detalle2  = '<strong>Concepto:</strong> '.$Logdes2['ccacon'].'<br>';
			$detalle2 .= '<strong>C. Costos:</strong> '.$Logdes2['ccacco'].'<br>';
			$detalle2 .= '<strong>Tipo Cen. Costos:</strong> '.$Logdes2['tcco'].'<br>';
			$detalle2 .= !is_null($Logdes2['ccacup'])  ? '<strong>Procedimiento:</strong> '.$Logdes2['ccacup'].'<br>'   : '';
			$detalle2 .= !is_null($Logdes2['ccaart'])  ? '<strong>Art&iacute;culo:</strong> '.$Logdes2['ccaart'].'<br>' : '';
			$detalle2 .= !is_null($Logdes2['ccafhce']) ? '<strong>Formulario HCE:</strong> '.$Logdes2['ccafhce'].'<br>' : '';
			$detalle2 .= !is_null($Logdes2['ccachce']) ? '<strong>Campo HCE:</strong> '.$Logdes2['ccachce'].'<br>' 		: '';
			
			if($Logdes['ccaeve'] == 'on') {
				$tipo_log = 'Configuraci&oacute;n Cargo Autom&aacute;tico - Evento';
			} else if($Logdes['ccadat'] == 'on') {
				$tipo_log = 'Configuraci&oacute;n Cargo Autom&aacute;tico - Dato';
			} else if($Logdes['ccapre'] == 'on') {
				$tipo_log = 'Configuraci&oacute;n Cargo Autom&aacute;tico - Prescripci&oacute;n';
			} else if($Logdes['ccaord'] == 'on') {
				$tipo_log = 'Configuraci&oacute;n Cargo Autom&aacute;tico - Orden';
			}
			
			$detalle2 = ($row['Logins'] == 'on' || $row['Logdel'] == 'on') ? '' : $detalle2;
		} else if ($row['Logtip'] == 'ccaeve' || $row['Logtip'] == 'ccadat' || $row['Logtip'] == 'ccaord' || $row['Logtip'] == 'ccapre') {
			
			$detalle  = '<strong>Historia:</strong> '.$Logdes['whistoria'].'-'.$Logdes['wing'].'<br>';
			$detalle .= !is_null($Logdes['wno1']) && 
						!is_null($Logdes['wno2']) && 
						!is_null($Logdes['wap1']) && 
						!is_null($Logdes['wap2'])		  ? '<strong>Paciente:</strong> '.$Logdes['wno1'].' '.$Logdes['wno2'].' '.$Logdes['wap1'].' '.$Logdes['wap2'].'<br>'   : '';
			$detalle .= !is_null($Logdes['wdoc'])  		  ? '<strong>Doc:</strong> '.$Logdes['wdoc'].'<br>'   : '';			
			$detalle .= !is_null($Logdes['ccoActualPac']) ? '<strong>C. Costos Paciente:</strong> '.$Logdes['ccoActualPac'].'<br>'   : '';
			$detalle .= '<br> <strong>M&aacute;s Detalles </strong>
						<img name = "btn-det-cargo" class="no_facturado-imagen" onclick="mostrarDetalleCargo(this, \''.$contador.'\')" valign="middle" style=" display: inline-block; cursor : pointer" src="../../images/medical/hce/mas.PNG"><br><br>';
			$detalle .= '<div name = "det_cargo" id = "det_cargo_'.$contador.'" style="display:none;">';
			$detalle .= !is_null($Logdes['tipoPaciente']) ? '<strong>Tipo Paciente:</strong> '.$Logdes['tipoPaciente'].'<br>'   : '';			
			$detalle .= !is_null($Logdes['tipoIngreso'])  ? '<strong>Tipo Ingreso:</strong> '.$Logdes['tipoIngreso'].'<br>'   : '';
			$detalle .= !is_null($Logdes['wnomemp']) 	  ? '<strong>Responsable:</strong> '.$Logdes['wnomemp'].'<br>' : '';
			$detalle .= !is_null($Logdes['wcodemp']) 	  ? '<strong>Cod Responsable:</strong> '.$Logdes['wcodemp'].'<br>' : '';
			$detalle .= !is_null($Logdes['tipoEmpresa'])  ? '<strong>Tipo Responsable:</strong> '.$Logdes['tipoEmpresa'].'<br>' 	 : '';
			$detalle .= !is_null($Logdes['wcodcon']) &&
						!is_null($Logdes['wnomcon'])      ? '<strong>Concepto:</strong> '.$Logdes['wcodcon'].'-'.$Logdes['wnomcon'].'<br>' 	 : '';
			$detalle .= !is_null($Logdes['wprocod']) &&
						!is_null($Logdes['wpronom'])      ? '<strong>Procedimiento o Articulo:</strong> '.$Logdes['wprocod'].'-'.$Logdes['wpronom'].'<br>' 	 : '';
			$detalle .= !is_null($Logdes['wcantidad']) 	  ? '<strong>Cantidad:</strong> '.$Logdes['wcantidad'].'<br>' : '';
			$detalle .= !is_null($Logdes['wvaltar']) 	  ? '<strong>Valor. Uni:</strong> '.number_format($Logdes['wvaltar'], 0, ',', '.').'<br>' : '';
			$detalle .= !is_null($Logdes['wvaltarReco'])  ? '<strong>Valor. Rec:</strong> '.number_format($Logdes['wvaltarReco'], 0, ',', '.').'<br>' : '';
			$detalle .= !is_null($Logdes['wcantidad']) && 
						!is_null($Logdes['wvaltar']) && is_numeric($Logdes['wcantidad']) &&  is_numeric($Logdes['wvaltar']) ? '<strong>Valor. Tot:</strong> '.number_format($Logdes['wcantidad'] * $Logdes['wvaltar'], 0, ',', '.').'<br>' : '-';
			$detalle .= !is_null($Logdes['wfeccar']) && 
						!is_null($Logdes['whora_cargo'])  ? '<strong>Fecha/Hora:</strong> '.$Logdes['wfeccar'].'/'.$Logdes['whora_cargo'].'<br>' : '';
			$detalle .= !is_null($Logdes['idCargo'])      ? '<strong>id Cargo</strong> '.$Logdes['idCargo'].'<br>' : '';
			$detalle .= '</div>';
			
			$detalle2 = '';
			
			if($row['Logtip'] == 'ccaeve') {
				$tipo_log = 'Cargo Autom&aacute;tico - Evento';
			} else if($row['Logtip'] == 'ccadat') {
				$tipo_log = 'Cargo Autom&aacute;tico - Dato';
			} else if($row['Logtip'] == 'ccapre') {
				$tipo_log = 'Cargo Autom&aacute;tico - Prescripci&oacute;n';
			} else if($row['Logtip'] == 'ccaord') {
				$tipo_log = 'Cargo Autom&aacute;tico - Orden';
			}
			
			$notas = !is_null($Logerr['error']) && $Logerr['error'] == 0  ? '<strong>Mensaje: </strong> '.$Logerr['mensaje'] : '<p style="color: red;"><strong>Advertencia: </strong> '.$Logerr['mensaje'].'</p>';
			
		} else if($row['Logtip'] == 'estancia') {
			
			//echo var_dump($Logdes);
			//echo var_dump($Logerr);
			
			$detalle  = !is_null($Logdes['whistoria']) && 
						!is_null($Logdes['wing']) 		  ? '<strong>Historia:</strong> '.$Logdes['whistoria'].'-'.$Logdes['wing'].'<br>' : '';
			$detalle .= !is_null($Logdes['wno1']) && 
						!is_null($Logdes['wno2']) && 
						!is_null($Logdes['wap1']) && 
						!is_null($Logdes['wap2'])		  ? '<strong>Paciente:</strong> '.$Logdes['wno1'].' '.$Logdes['wno2'].' '.$Logdes['wap1'].' '.$Logdes['wap2'].'<br>'   : '';
			$detalle .= !is_null($Logdes['wdoc'])  		  ? '<strong>Doc:</strong> '.$Logdes['wdoc'].'<br>'   : '';
			$detalle .= !is_null($Logdes['ccoActualPac']) ? '<strong>C. Costos Paciente:</strong> '.$Logdes['ccoActualPac'].'<br>'   : '';
			$detalle .= !is_null($Logdes['tipoPaciente']) ? '<strong>Tipo Paciente:</strong> '.$Logdes['tipoPaciente'].'<br>'   : '';
			$detalle .= !is_null($Logdes['tipoIngreso'])  ? '<strong>Tipo Ingreso:</strong> '.$Logdes['tipoIngreso'].'<br>'   : '';
		
			if(!is_null($Logdes['detalle_estancias'])) {
				$detalle.= '<br> <strong>Detalle Estancias </strong>
				<img name = "btn-det-cargo" class="no_facturado-imagen" onclick="mostrarDetalleCargo(this , \''.$contador.'\')" valign="middle" style=" display: inline-block; cursor : pointer" src="../../images/medical/hce/mas.PNG"><br><br>';
				
				$detalle.= '<div name = "det_cargo" id = "det_cargo_'.$contador.'" style="display:none;">';
								
				foreach($Logdes['detalle_estancias'] as $data) {
					$detalle .= '<strong>Habitaci&oacute;n: </strong> '.$data['num_habitacion'].' - '.$data['habitacion'].'<br>';
					$detalle .= '<strong>D&iacute;as: </strong> '.$data['cantidad'].'<br>';
					$detalle .= '<strong>Responsable: </strong> '.$data['responsable'].'<br>';
					$detalle .= '<strong>Fecha: </strong> '.$data['fecha_cargo'].'<br>';
					$detalle .= '<strong>C. Costos: </strong> '.$data['cco'].'<br>';
					$detalle .= '<strong>Valor: </strong> '.$data['valor'].'<br>';
					$detalle .= '<strong>Tarifa: </strong> '.$data['tarifa'].'<br>';
					$detalle .= !is_null($data['idcargo'])      ? '<strong>id Cargo</strong> '.$data['idcargo'].'<br>' : '<br>';
					$detalle .= '<br>';
				}
				
				//$detalle.='<div id="detalle_log_'.$contador.'" style="display:none;">'.$row['Loghtml'].'</div>';
				//$detalle.= '<button onclick="alert(\''.$contador.'\');">Ver Html</button></div>';
			}
			
			$numero_estancias = !is_null($Logdes['estancias']) ? (int) $Logdes['estancias'] : null;
			for($i = 0; $i < $numero_estancias; $i++) {
				$notas.= !is_null($Logerr['estancia'.$i]) && $Logerr['estancia'.$i]['idcargo'] != 0 ? '<strong>id Cargo</strong> '.$Logerr['estancia'.$i]['idcargo'].' - '.$Logerr['estancia'.$i]['respuesta'].'<br>' : '<p style="color:red"><strong>Advertencia: </strong> '.$Logerr['estancia'.$i]['respuesta'].'</p><br>';
			}
			
			$notas.= !is_null($Logerr['error'])  && $Logerr['error'] == 1 ? '<p style="color:red"><strong>Advertencia: </strong> '.$Logerr['mensaje'].'</p><br>' : '';
			
		}
		
		$tipo_transaccion = '';
		
		if($row['Logins'] == 'on') {
			$tipo_transaccion = 'Inserci&oacute;n';
		} else if($row['Logupd'] == 'on') {
			$tipo_transaccion = 'Actualizaci&oacute;n';
		} else if($row['Logdel'] == 'on') {
			$tipo_transaccion = 'Eliminaci&oacute;n';
		}
		
		$html .= "<tr class='".$class."' >
					<td>".$contador."</td>
					<td>".$row['fecha']."</td>
					<td>".$row['hora']."</td>
					<td>".$row['usuario']."</td>
					<td>".$detalle."</td>
					<td>".$detalle2."</td>
					<td>".$tipo_log."</td>
					<td>".$tipo_transaccion."</td>
					<td>".$notas."</td>
				</tr>";
				
		$contador++;
	}
	
	$html.="</table>";
	
	
	return $html;
	
}

function enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1,$detalle2, $html) {
	
	// --> Enviar correo informando la publicacion
	$wcorreopmla 				= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailpmla");
	$wcorreopmla 				= explode("--", $wcorreopmla );
	$wpassword   				= $wcorreopmla[1];
	$wremitente  				= $wcorreopmla[0];
	
	$datos_remitente 			= array();
	$datos_remitente['email']	= $wremitente;
	$datos_remitente['password']= $wpassword;
	$datos_remitente['from'] 	= $wremitente;
	$datos_remitente['fromName']= "Desarrollo de software";
	
	$wdestinatarios	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailNotificacionEstancia");
	$wdestinatarios = explode(",",$wdestinatarios);
	
	$mensaje		= "
	<html>
	<body>
	<table>
		<tr>
			<td align='center' style='background-color:#2a5db0;color:#ffffff;font-size:10pt;padding:1px;font-family:verdana;' colspan='2'>".$detalle1."</td>
		</tr>
		<tr>
			<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Fecha/Hora</td>
			<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>".date("Y-m-d H:i:s")."</td>
		</tr>
		<tr>
			<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Detalle</td>
			<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>".$detalle2."</td>
		</tr>
		<tr>
			".$html."
		</tr>
	</table>
	</body>
	</html>							
	";
	
	$altbody = "";
	sendToEmail($wasunto,$mensaje,$altbody, $datos_remitente, $wdestinatarios );
}

function guardarCargoAutomaticoEstancia($conex, $wemp_pmla, $wbasedato_movhos, $wbasedato_cliame, $whis, $wing) {
	
	$array_error = array();
	
	$localhost = 'localhost';
	//$URL = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
	$URL = $_SERVER['REQUEST_SCHEME'].'://'.$localhost;
	$CURLOPT_URL = $URL."/matrix/ips/procesos/Liquidacion_pensionERP.php?wemp_pmla=".$wemp_pmla;
	
	$Ubisac = consultarUbicacionPacienteHCE($conex, $wbasedato_movhos, $whis, $wing );
	
	$wno1 = '';
	$wno2 = '';
	$wap1 = '';
	$wap2 = '';
	$wdoc = '';
	
	$sql = "SELECT Ccoerp
			  FROM ".$wbasedato_movhos."_000011
			 WHERE ccocod = '".$Ubisac."'
		";
		
	$resCco = mysql_query( $sql, $conex );
	
	if(!$resCco) {
		throw new Exception("Error: " . mysql_errno() . " - 01 - en el Error en el query guardar cargo automatico estancia");
	}
	
	$numCco = mysql_num_rows( $resCco );
	$CcoErp = false;
	
	if( $rowsCco = mysql_fetch_array( $resCco) ){
		$CcoErp = $rowsCco[ 'Ccoerp' ] == 'on' ? true: false;
	}
	
	//Si el cco no maneja cargo ERP o no esta activo los cargos ERP no se ejecuta esta accion
	$cargarEnErp = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cargosPDA_ERP" );
	if( !$CcoErp || $cargarEnErp != 'on' ) {
				
		$wasunto = "Automatizacion Estancia - Historia (".$whis."-".$wing.") - cco no maneja cargo ERP o no esta activo los cargos ERP";
		$detalle1 = "La Historia ".$whis."-".$wing.", cco no maneja cargo ERP o no esta activo los cargos ERP, por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.";
		$detalle2 = "cco no maneja cargo ERP o no esta activo los cargos ERP.";		
		enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, $html);
		
		$data_json = array( 
			'wemp_pmla'	  	 			=> $wemp_pmla,
			'accion'					=> 'validacion_cco_cargo_ERP',
			'whistoria'	  	 			=> $whis,
			'wing'		  	 			=> $wing,
			'wno1' 		 				=> $wno1,
			'wno2'  		 			=> $wno2,
			'wap1'  		 			=> $wap1,
			'wap2'		 	 			=> $wap2,
			'wdoc' 		 				=> $wdoc,
			'wprocedimiento' 			=> '',
			'wnprocedimiento'			=> '',
			'wcodemp'		 			=> '',
			'wnomemp'		 			=> '',
			'wnomcon'					=> '',
		);
		
		$data = array();
		$data['error'] = 1;
		$data['mensaje'] = "La Historia ".$whis."-".$wing.", cco no maneja cargo ERP o no esta activo los cargos ERP, por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.";
		
		logTransaccion($conex, $wbasedato_cliame, '', json_encode($data_json), '',  json_encode($data), 'on', 'INSERT', 'estancia', '');
		
		return array( 'code' => 0, 'msj' => "La Historia ".$whis."-".$wing.", cco no maneja cargo ERP o no esta activo los cargos ERP, por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.");
	}
	
	$sql = "SELECT *
			  FROM ".$wbasedato_movhos."_000016
			 WHERE inghis = '".$whis."'
			   AND inging = '".$wing."'
		";
	
	$resRes = mysql_query( $sql, $conex );
	if(!$resRes) {
		throw new Exception("Error: " . mysql_errno() . " - 02 - en el Error en el query guardar cargo automatico estancia");
	}
	$numRes = mysql_num_rows( $resRes );
	if( $rowsRes = mysql_fetch_array( $resRes) ){
						
		$sql = "SELECT *
				  FROM ".$wbasedato_cliame."_000101
				 WHERE Inghis = '".$whis."'
				   AND Ingnin = '".$wing."'
			";
		
		$resIng = mysql_query( $sql, $conex );
		if(!$resIng) {
			throw new Exception("Error: " . mysql_errno() . " - 03 - en el Error en el query guardar cargo automatico estancia");
		}
		$numIng = mysql_num_rows( $resIng );
	
		if( $rowsIng = mysql_fetch_array( $resIng) ){
				
			$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
		
			if( $rowsIng[ 'Ingtpa' ] == 'P' ){
				$empresa = $codEmpParticular;
			}
			else{
				$empresa = $rowsIng[ 'Ingcem' ];
			}
			
			$sql = "SELECT *
					  FROM ".$wbasedato_cliame."_000024
					 WHERE empcod = '".$empresa."'
					";
		
			$resEmp = mysql_query( $sql, $conex );
			if(!$resEmp) {
				throw new Exception("Error: " . mysql_errno() . " - 04 - en el Error en el query guardar cargo automatico estancia");
			}
			$numEmp = mysql_num_rows( $resEmp );
			
			if( $rowsEmp = mysql_fetch_array( $resEmp ) ){
		
				//Informacion de empresa
				$wcodemp 	  = $rowsEmp[ 'Empcod' ];
				$wnomemp 	  = $rowsEmp[ 'Empnom' ];
				$tipoEmpresa  = $rowsEmp[ 'Emptem' ];
				$nitEmpresa   = $rowsEmp[ 'Empnit' ];
				$wtar		  = $rowsEmp[ 'Emptar' ];
			
				//Informacion del paciente
				$tipoPaciente = $rowsIng[ 'Ingcla' ];
				$tipoIngreso  = $rowsIng[ 'Ingtin' ];
				$wser		  = $rowsIng[ 'Ingsei' ];
				$wfecing	  = $rowsIng[ 'Ingfei' ];
												
				//Consulta informacion de pacientes
				$infoPacienteCargos = consultarNombresPaciente( $conex, $whis, $wemp_pmla );
				
				$wno1 = $infoPacienteCargos['Pacno1']; // $wno1;
				$wno2 = $infoPacienteCargos['Pacno2']; // $wno2;
				$wap1 = $infoPacienteCargos['Pacap1'];
				$wap2 = $infoPacienteCargos['Pacap2'];
				$wdoc = $infoPacienteCargos['Oriced']; // $wdoc;
			}
		}
	}
	
	$wno1 = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($wno1));
	$wno2 = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($wno2));
	$wap1 = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($wap1));
	$wap2 = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($wap2));
	
	//Conceptos de grabacion que en este caso sería el de estancia
	$q_concepto_estancia   = "  SELECT  Grucod, Grudes  "
		."    FROM ".$wbasedato_cliame."_000200 "
		."   WHERE Grutpr = 'H' ";
	
	$datos_concepto = mysql_fetch_array(mysql_query($q_concepto_estancia, $conex));
	$wconcepto = $datos_concepto['Grucod'];
	
	$ch = curl_init();
	$data = array( 
		'consultaAjax'		=> '',
		'accion'			=> 'resumen_pension',
		'whistoria' 		=> $whis,
		'wing' 				=> $wing,
		'wtar'				=> $wtar,
		'wempresa'			=> $wcodemp,
		'wconcepto'			=> $wconcepto,
		'wcambiar_valor'	=> 'off',
		'wcambiar_dias'		=> 'off',
		'wtipo_ingreso'		=> $tipoIngreso,
		'wcambiodetipos'	=> '0',
		'wtipo_paciente'	=> $tipoPaciente,
		'wfechaparcial'		=> 'no',
		'crontab'			=> '',
		'wuse'				=> '',
	);
								
	$options = array(
		CURLOPT_URL 			=> $CURLOPT_URL,
		CURLOPT_HEADER 			=> false,
		CURLOPT_POSTFIELDS 		=> $data,
		CURLOPT_RETURNTRANSFER	=> true,
		CURLOPT_CUSTOMREQUEST 	=> 'POST',
	);
		
	$opts = curl_setopt_array($ch, $options);
	
	$html = curl_exec($ch);
	curl_close($ch);
	
	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	
	if(is_null($dom->getElementById('numero_responsables'))) {
		$data_json = array( 
			'wemp_pmla'	  	 			=> $wemp_pmla,
			'accion'					=> 'validacion_dias_por_liquidar',
			'whistoria'	  	 			=> $whis,
			'wing'		  	 			=> $wing,
			'wno1' 		 				=> $wno1,
			'wno2'  		 			=> $wno2,
			'wap1'  		 			=> $wap1,
			'wap2'		 	 			=> $wap2,
			'wdoc' 		 				=> $wdoc,
			'wprocedimiento' 			=> '',
			'wnprocedimiento'			=> '',
			'wcodemp'		 			=> '',
			'wnomemp'		 			=> '',
			'wnomcon'					=> '',
		);
		
		$data = array();
		$data['error'] = 1;
		$data['mensaje'] = 'Lo sentimos, La historia ('.$whis.'-'.$wing.'), no cuenta con dias por liquidar.';
		
		$wasunto = "Automatizacion Estancia - Historia (".$whis."-".$wing.") - No tiene dias para liquidar";
		$detalle1 = "Historia ".$whis."-".$wing.", Sin dias para liquidar.";
		$detalle2 = "La Historia ($whis-$wing), no cuenta con dias para liquidar.";
		
		enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, $html);
		logTransaccion($conex, $wbasedato_cliame, '', json_encode($data_json), '',  json_encode($data), 'on', 'INSERT', 'estancia', $html);
		
		return array( 'code' => 0, 'msj' => 'Lo sentimos, La historia ('.$whis.'-'.$wing.'), no cuenta con dias por liquidar.');
	}
	
	$cantidad = $dom->getElementById('cantidad')->getAttributeNode('value')->nodeValue;
	
	$grabar = 'si';
	$clave;
	$ndia;
	$fechacargo;
	$ccogra;
	$wprocedimiento;
	$wvalor;
	$wnprocedimiento;
	$wresponsable;
	$wnresponsable;
	$wtarifa;
	$tipoEmpresa;
	$wnitEmpresa;
	$id_tope_afectado;
	$whora_ingreso;
	$whora_egreso;
	
	$ultimafecha;
	$ultimahora;
	
	$validaciontarifa = 0;
	
	//--Validacion de cargos compartidos sin tercero
	$tieneporcentaje = 0;
	$claveaux;
	
	for ($i = 1; $i <= $cantidad; $i++ ) {
		$dias = $dom->getElementById('input_dias_'.$i)->getAttributeNode('value')->nodeValue;
		for ($j = 0; $j < $dias; $j++ ) {
			if(!is_null($dom->getElementById("idporcentaje_".$i."_".$j))) {
				$id_porcentaje = $dom->getElementById("idporcentaje_".$i."_".$j);
				$valor_porcentaje = $id_porcentaje->getAttributeNode('value')->nodeValue;
				if($valor_porcentaje == 'No tiene porcentaje') {
					$tieneporcentaje++;
				}
			}
		}
	}
	
	if ($tieneporcentaje > 0) {
		$data_json = array( 
			'wemp_pmla'	  	 			=> $wemp_pmla,
			'accion'	  	 			=> 'validacion_porcentaje_tercero',
			'whistoria'	  	 			=> $whis,
			'wing'		  	 			=> $wing,
			'wno1' 		 				=> $wno1,
			'wno2'  		 			=> $wno2,
			'wap1'  		 			=> $wap1,
			'wap2'		 	 			=> $wap2,
			'wdoc' 		 				=> $wdoc,
			'wprocedimiento' 			=> '',
			'wnprocedimiento'			=> '',
			'wcodemp'		 			=> '',
			'wnomemp'		 			=> '',
			'wnomcon'					=> '',
		);
		
		$data = array();
		$data['error'] = 1;
		$data['mensaje'] = 'Lo sentimos, La historia ('.$whis.'-'.$wing.') tiene un concepto con tercero sin porcentaje, por lo tanto no se realiza el proceso automatico de estancia.';
		
		$wasunto = "Automatizacion Estancia - Historia (".$whis."-".$wing.") - Concepto con tercero sin porcentaje";
		$detalle1 = "La Historia ".$whis."-".$wing.", Concepto con tercero sin porcentaje.";
		$detalle2 = "La Historia ($whis-$wing), tiene un concepto con tercero sin porcentaje.";
		
		enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, $html);
		logTransaccion($conex, $wbasedato_cliame, '', json_encode($data_json), '',  json_encode($data), 'on', 'INSERT', 'estancia', $html);
		
		return array( 'code' => 0, 'msj' => 'Lo sentimos, La historia ('.$whis.'-'.$wing.'), tiene un concepto con tercero sin porcentaje, por lo tanto no se realiza el proceso automatico de estancia.');
	}
	
	/* EDWIN VALIDAR SI APLICA PARA LOS PACIENTES QUE VAMOS A LLAMAR EN LA CONSULTA PRINCIPAL, YA QUE SE SUPONE QUE TODOS LOS PACIENTES QUE TRAIGAMOS TIENEN FECHA DE ALTA PROGRAMADA
		//--------
		// validacion de alta programada (si el paciente no tiene aun alta programada, no se puede liquidar la pension)
		
		if (validar_alta =='si')
		{
			if($("#altaprogramada").val()=='off')
			{
				paciente_sin_alta_programada();
				return;
			}
		}
	*/
	
	$numero_responsables = $dom->getElementById('numero_responsables')->getAttributeNode('value')->nodeValue;
	//$numero_responsables = $dom->getElementById('numero_responsables')->getAttribute('value');
	//echo "numero_responsables = $numero_responsables \n\n";
	
	$sel = $dom->getElementsByTagName("select");
	foreach ($sel as $select)
	{
		if($select->hasAttribute('class') && $select->getAttributeNode('class')->nodeValue == 'habitacion') {
			$optionTags = $select->getElementsByTagName('option');
			foreach ($optionTags as $tag)
			{
				if ($tag->hasAttribute("selected"))
				{
					if(($tag->getAttributeNode('value')->nodeValue) == "" ) 
					{
						$post_fields_grabar_pension = array( 
							'wemp_pmla'	  	 			=> $wemp_pmla,
							'accion'					=> 'validacion_tipo_habitacion',
							'whistoria'	  	 			=> $whis,
							'wing'		  	 			=> $wing,
							'wno1' 		 				=> $wno1,
							'wno2'  		 			=> $wno2,
							'wap1'  		 			=> $wap1,
							'wap2'		 	 			=> $wap2,
							'wdoc' 		 				=> $wdoc,
							'wprocedimiento' 			=> '',
							'wnprocedimiento'			=> '',
							'wcodemp'		 			=> '',
							'wnomemp'		 			=> '',
							'wnomcon'					=> '',
						);
						
						$post_fields_grabar_pension['estancias'] = 1;
						$data = array();
						$data['estancia0'] = [ 'idcargo' => 0, 'respuesta' => 'No hay tipo de habitacion seleccionado. Por lo tanto no se puede realizar el Guardado de Estancia Autom&aacute;tico.'];
						$data['mensaje'] = "La Historia ".$whis."-".$wing.", no cuenta con un tipo de habitacion seleccionado, por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.";
						
						logTransaccion($conex, $wbasedato_cliame, '', json_encode($post_fields_grabar_pension), '',  json_encode($data), 'on', 'INSERT', 'estancia', $html);
						
						$wasunto = "Automatizacion Estancia - Historia (".$whis."-".$wing.") - Tipo de Habitacion No Seleccionado";
						$detalle1 = "La Historia ".$whis."-".$wing.", no cuenta con un tipo de habitacion seleccionado, por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.";
						$detalle2 = "No ha sido selecciona el tipo de habitación.";
						
						enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, $html);
						
						return array( 'code' => 0, 'msj' => "La Historia ".$whis."-".$wing.", no cuenta con un tipo de habitacion seleccionado, por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.");
					}
				}
			}
		}
	}
	
	/* AGREGAMOS LA VALIDACION PARA LA PARTE DE TARIFA EN CERO */
	$existen_tarifas_en_cero = false;
	$array_info = [];
	for($i = 1; $i <= $cantidad; $i++) {
		$dias = $dom->getElementById('input_dias_'.$i)->getAttributeNode('value')->nodeValue;
		
		$td_fechainicial_ppal =$dom->getElementById('tdfechainicialppal_'.$i)->textContent;	
		$td_fechafinal_ppal =$dom->getElementById('tdfechafinalppal_'.$i)->textContent;	
		
		$responsable = '';
		
		for($d = 0; $d < $dias; $d++ ) {
			
			$tr_ppal_cobro = $dom->getElementById('id_tr_ppal_cobro_'.$i.'_'.$d);
			$clave =  $tr_ppal_cobro->getAttributeNode('clave')->nodeValue;
			$ndia = $tr_ppal_cobro->getAttributeNode('ndia')->nodeValue;
			
			for($j = $numero_responsables ; $j >= 1; $j--) {
				$valor_tarifa = !is_null($dom->getElementById('valhab_clave'.$clave.'_'.$ndia.'_res'.$j)) ? ($dom->getElementById('valhab_clave'.$clave.'_'.$ndia.'_res'.$j)->getAttributeNode('valor')->nodeValue *1) : 0;
				
				if($valor_tarifa == 0) {
					$existen_tarifas_en_cero = true;
				}
				
				$reconocido_clave = $dom->getElementById('reconocido_clave'.$clave.'_'.$ndia.'_res'.$j);
				$nresponsable = !is_null($reconocido_clave) ? $reconocido_clave->getAttributeNode('nresponsable')->nodeValue : '';
				
				if(!is_null($reconocido_clave) && $valor_tarifa == 0 && $responsable != 'no-tiene-responsable' && $responsable != $nresponsable && $d == 0) {
					$texto_tarifa_cero = '';
					$texto_tarifa_cero2 = '';

					$texto_tarifa_cero2 = 'Responsable: '.$reconocido_clave->getAttributeNode('nresponsable')->nodeValue.'<br>';
					$texto_tarifa_cero2.= 'Nit: '.$reconocido_clave->getAttributeNode('nitresponsable')->nodeValue.'<br>';
					$texto_tarifa_cero2.= 'Tarifa: '.$reconocido_clave->getAttributeNode('tarifa')->nodeValue.'<br>';
					$texto_tarifa_cero2.= 'Cod Concepto: '.$reconocido_clave->getAttributeNode('concepto')->nodeValue.'<br>';
					$texto_tarifa_cero2.= 'Concepto: '.$reconocido_clave->getAttributeNode('nconcepto')->nodeValue.'<br>';
					$texto_tarifa_cero2.= 'Habitaci&oacute;n: '.$reconocido_clave->getAttributeNode('procedimiento')->nodeValue.'-'.$reconocido_clave->getAttributeNode('nombre_hab')->nodeValue.'<br>';
					
					$texto_tarifa_cero = 'Fecha de Ingreso: '.trim($td_fechainicial_ppal).'<br>';
					$texto_tarifa_cero.= 'Fecha de Egreso: '.trim($td_fechafinal_ppal).'<br>';
					$texto_tarifa_cero.= 'D&iacute;as: '.$dias.'<br>';
					$texto_tarifa_cero.= $texto_tarifa_cero2;
					
					$texto_tarifa_cero.= '<br><br>';
					
					$array_info[] = $texto_tarifa_cero;	
				}
				
				$responsable = !is_null($reconocido_clave) ? $reconocido_clave->getAttributeNode('nresponsable')->nodeValue : 'no-tiene-responsable';
			}
		}
	}
	
	$texto_array_info = '';
	if($existen_tarifas_en_cero) {
		$num_array_info = sizeof($array_info);
		for($i = 0; $i < $num_array_info; $i++) {
			$texto_array_info.= $array_info[$i];
		}
		
		$data_json = array( 
			'wemp_pmla'	  	 			=> $wemp_pmla,
			'accion'					=> 'validacion_tarifa_cero',
			'whistoria'	  	 			=> $whis,
			'wing'		  	 			=> $wing,
			'wno1' 		 				=> $wno1,
			'wno2'  		 			=> $wno2,
			'wap1'  		 			=> $wap1,
			'wap2'		 	 			=> $wap2,
			'wdoc' 		 				=> $wdoc,
			'wprocedimiento' 			=> '',
			'wnprocedimiento'			=> '',
			'wcodemp'		 			=> '',
			'wnomemp'		 			=> '',
			'wnomcon'					=> '',
		);
		
		$data = array();
		$data['error'] = 1;
		$data['mensaje'] = 'Lo sentimos, La historia ('.$whis.'-'.$wing.') tiene estancias con tarifa sin definir.';
		
		$wasunto = "Automatizacion Estancia - Historia (".$whis."-".$wing.")";
		$detalle1 = "Historia ".$whis."-".$wing." - Estancias Sin Tarifa Definida";
		$detalle2 = 'La siguiente liquidaci&oacute;n de estancia contiene una o varias tarifas sin definir, por lo tanto no se realiza la liquidaci&oacute;n autom&aacute;tica de estancia. <br><br>'.$texto_array_info;
		
		enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, $html);
		logTransaccion($conex, $wbasedato_cliame, '', json_encode($data_json), '',  json_encode($data), 'on', 'INSERT', 'estancia', $html);
		
		return array( 'code' => 0, 'msj' => 'La Historia ('.$whis.'-'.$wing.'), tiene algunas estancias sin tarifa definida, por lo tanto no se realiza el proceso autom&aacute;tico de liquidaci&oacute;n de estancia.');
	}
	
	//--------------------------------------------------------
	//--Proceso de contruccion de vectores para la grabacion
	//---------------------------------------------------------
	//--Se estan construyendo tres vecotres uno  que se llama datos , que es el principal se usa para grabar los cargos , en este vector estan los cargos resumidos por cada traslado, sumandose
	//--los dias y los valores a pagar (en conclusion se suman los dias y los valores cuando el paciente esta en la misma habitacion)
	//--El segundo vector datosaux contiene los cargos detallados dia a dia, se usa con el fin de validar en unix todas las politicas y comprobar si tiene tarifa y si el tercero tiene relacion
	//--con el concepto en la tabla de honorarios de unix.
	//--El tercer vector contiene  datosauxfinal  es muy parecido a datos  pero  tiene en cuenta los terceros que intervinieron  cuando el paciente estuvo en la misma habitacion asi , si el paciente
	//--se le graban cargos de estancia sin tercero, o donde el tercero no varia por habitacion , el vector datosauxfinal seria igual a datos , pero si durante la estancia por ejemplo estuvo 5 dias en una habitacion de cuidados intensivos 
	//--centro de costos neonatos, ahi se cobra con tercero y si el tercero cambio dos dias para el tercero X y 3 para el Y entonces el cargo se parte en dos.
		
	//-- se cuenta en total los dias de estancia
	$diasauxiliar = 0;
	$auxiliarexcedente=0;
	$tiene_paquete = false;
	
	for ($i = 1; $i <= $cantidad; $i++ ) {
		$dias = $dom->getElementById('input_dias_'.$i)->getAttributeNode('value')->nodeValue;
		for ($j = 0; $j < $dias; $j++ ) {
			$id_tr_ppal_cobro = $dom->getElementById("id_tr_ppal_cobro_".$i."_".$j);
			if($id_tr_ppal_cobro->hasAttribute('nosecobraporpaquete')) {
				
				$nosecobraporpaquete = $id_tr_ppal_cobro->getAttributeNode('nosecobraporpaquete')->nodeValue;
				if($nosecobraporpaquete=='si'){
					$paqclave = $id_tr_ppal_cobro->getAttributeNode('clave')->nodeValue;
					$paqndia = $id_tr_ppal_cobro->getAttributeNode('ndia')->nodeValue; 
					$auxiliarexcedente = ($auxiliarexcedente * 1) + ($dom->getElementById("excedente_".$paqclave."_".$paqndia)->getAttributeNode('valor')->nodeValue * 1);
					$tiene_paquete = true;
				}
			} else {
				$diasauxiliar++;
			}
		}
	}
	
	//-------------
	$datos = array();//array que contiene toda la informacion de los registros a grabar , agrupados por los dias que el paciente estuvo en la habitacion , este array es basicamente para grabar
	$datosaux = array();//array que contiene toda la informacion de los datos dia a dia , durante el tiempo que el paciente permanecio en la estancia, este array es para validar diariamente la cirugia
	$datosauxfinal = array();//array que contiene toda la informacion de los datos dia a dia , durante el tiempo que el paciente permanecio en la estancia, este array es para validar diariamente la cirugia
	$datosauxfinal2 = array();//array que contiene toda la informacion de los datos dia a dia , durante el tiempo que el paciente permanecio en la estancia, este array es para validar diariamente la cirugia
	$datos_dos = array();//array que contiene toda la informacion de los datos dia a dia , durante el tiempo que el paciente permanecio en la estancia, este array es para validar diariamente la cirugia
	$auxclave;
	$auxtercero = '';
	$diasauxiliar1 = 0;
	$diasauxiliar2 = 0;
	$contadore = 0;
	
	$fecha_actual = date('Y-m-d');
	
	for($i = 1; $i <= $cantidad; $i++) {
	
		$td_fechainicial_ppal =$dom->getElementById('tdfechainicialppal_'.$i)->textContent;	
		$explode_td_fechainicial_ppal = explode('/', $td_fechainicial_ppal);
		
		$fecha_ingreso = trim($explode_td_fechainicial_ppal[0]);
		$hora_ingreso = trim($explode_td_fechainicial_ppal[1]);
		
		$td_fechafinal_ppal =$dom->getElementById('tdfechafinalppal_'.$i)->textContent;	
		$explode_td_fechafinal_ppal = explode('/', $td_fechafinal_ppal);
		
		$ultimafecha = trim($explode_td_fechafinal_ppal[0]);
		$ultimahora = trim($explode_td_fechafinal_ppal[1]);
		
		//echo var_dump($fecha_ingreso );
		$dias = $dom->getElementById('input_dias_'.$i)->getAttributeNode('value')->nodeValue;
		//$valor = 0;
										
		$liquidar_estancia = ($dias == 1 && $fecha_ingreso == $fecha_actual && ($fecha_ingreso == $fecha_actual || $fecha_ingreso == $ultimafecha)) ? false : true;
		
		if(!$liquidar_estancia) {
			
			
			$data_json = array( 
				'wemp_pmla'	  	 			=> $wemp_pmla,
				'accion'					=> 'validacion_tiempo_minimo_estancia',
				'whistoria'	  	 			=> $whis,
				'wing'		  	 			=> $wing,
				'wno1' 		 				=> $wno1,
				'wno2'  		 			=> $wno2,
				'wap1'  		 			=> $wap1,
				'wap2'		 	 			=> $wap2,
				'wdoc' 		 				=> $wdoc,
				'wprocedimiento' 			=> '',
				'wnprocedimiento'			=> '',
				'wcodemp'		 			=> '',
				'wnomemp'		 			=> '',
				'wnomcon'					=> '',
			);
			
			$data = array();
			$data['error'] = 1;
			$data['mensaje'] = 'Lo sentimos, La historia ('.$whis.'-'.$wing.'), no cumple con el tiempo minimo de estancia.';
			
			$wasunto = "Automatizacion Estancia - Historia (".$whis."-".$wing.")";
			$detalle1 = "Historia ".$whis."-".$wing.", no cumple con el tiempo minimo de estancia.";
			$detalle2 = "La Historia ($whis-$wing), no cumple con el tiempo minimo de estancia, ya que no estuvo en la habitacion despues de las 12 de la madrugada.";
				
			enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, $html);
			logTransaccion($conex, $wbasedato_cliame, '', json_encode($data_json), '',  json_encode($data), 'on', 'INSERT', 'estancia', $html);
			
			return array( 'code' => 0, 'msj' => 'La Historia ('.$whis.'-'.$wing.'), no cumple con el tiempo minimo de estancia, ya que no estuvo en la habitacion despues de las 12 de la madrugada.');
		}
		
		if($liquidar_estancia) {
			for($d = 0; $d < $dias; $d++ ) {
				/*
					$input_detalle_dia = $dom->getElementById("valhab_clave".$i."_".$d."_res1");
					$valor += $input_detalle_dia->getAttribute('valor');
				*/
				
				$tr_ppal_cobro = $dom->getElementById('id_tr_ppal_cobro_'.$i.'_'.$d);
				$clave =  $tr_ppal_cobro->getAttributeNode('clave')->nodeValue;
				$ndia = $tr_ppal_cobro->getAttributeNode('ndia')->nodeValue;
				
				//echo "id_tr_ppal_cobro_".$clave."_".$ndia." \n";
				//return;
				
				//$fechacargo = $("#fechacargo_"+clave+"_"+ndia).attr("valor");
				$fechacargo = $dom->getElementById('fechacargo_'.$clave.'_'.$ndia)->getAttributeNode('valor')->nodeValue;
				//$whora_ingreso = $("#fechacargo_"+clave+"_"+ndia).attr("hora_ingreso");
				$whora_ingreso = $dom->getElementById('fechacargo_'.$clave.'_'.$ndia)->getAttributeNode('hora_ingreso')->nodeValue;
				//$whora_egreso = $("#fechacargo_"+clave+"_"+ndia).attr("hora_egreso");
				$whora_egreso = $dom->getElementById('fechacargo_'.$clave.'_'.$ndia)->getAttributeNode('hora_egreso')->nodeValue;
				//$ccogra = $(this).attr('ccogra');
				$ccogra = $tr_ppal_cobro->getAttributeNode('ccogra')->nodeValue;
				
				/*
				echo "fechacargo: $fechacargo \n";
				echo "whora_ingreso: $whora_ingreso \n";
				echo "whora_egreso: $whora_egreso \n";
				echo "ccogra: $ccogra \n\n";
				*/
				
				$aux_id_grabado = '';
				
				$wtercero = '';
				$wnomtercero = '';
				$auxwtercero = '';
				
				for($j = $numero_responsables ; $j >= 1; $j--)
				{
					$wprocedimiento = "";
					$wnprocedimiento = "";
					$wnumerohab = "";
					$wvalor = "";
					$wresponsable = "";
					$wnresponsable = "";
					$wtarifa = "";
					$id_tope_afectado = "";
					
					//if( (($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor') * 1)!=0 || $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('paf')=='si' || $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('escomplementario')=='si')   &&  $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).length)
					
					$reconocido_clave = $dom->getElementById('reconocido_clave'.$clave.'_'.$ndia.'_res'.$j);
									
					if(!is_null($reconocido_clave)) 
					{
						//$('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor')
						$valor_reconocido_clave_res = $reconocido_clave->getAttributeNode('valor')->nodeValue;
						//$('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('paf')
						$paf_reconocido_clave_res = $reconocido_clave->getAttributeNode('paf')->nodeValue;
						//$('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('escomplementario')
						$escomplementario_reconocido_clave_res = $reconocido_clave->getAttributeNode('escomplementario')->nodeValue;
						//$('#reconocido_clave'+clave+'_'+ndia+'_res'+j).length
						
						/*
						echo "valor_reconocido_clave_res: $valor_reconocido_clave_res \n";
						echo "paf_reconocido_clave_res: $paf_reconocido_clave_res \n";
						echo "escomplementario_reconocido_clave_res: $escomplementario_reconocido_clave_res \n";
						*/
						
						if((($valor_reconocido_clave_res * 1) != 0 || $paf_reconocido_clave_res == 'si' || $escomplementario_reconocido_clave_res == 'si' ))
						{
							//wnprocedimiento = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('nombre_hab');
							$wnprocedimiento = $reconocido_clave->getAttributeNode('nombre_hab')->nodeValue;
							//wnumerohab =	$('#habitacion_'+clave).attr('numero');
							$wnumerohab 	 =	$dom->getElementById('habitacion_'.$clave)->getAttributeNode('numero')->nodeValue; 
							//wvalor = ($('#valhab_clave'+clave+'_'+ndia+'_res'+j).attr('valor') *1);
							$wvalor = ($dom->getElementById('valhab_clave'.$clave.'_'.$ndia.'_res'.$j)->getAttributeNode('valor')->nodeValue *1);
							
							//wreconocido = ($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor') * 1);
							$wreconocido = ($valor_reconocido_clave_res * 1);
							//wprocedimiento = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('procedimiento');
							$wprocedimiento = $reconocido_clave->getAttributeNode('procedimiento')->nodeValue;
							//wresponsable = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('responsable');
							$wresponsable = $reconocido_clave->getAttributeNode('responsable')->nodeValue;
							
					
							//wnresponsable = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('nresponsable');
							$wnresponsable = $reconocido_clave->getAttributeNode('nresponsable')->nodeValue;
							//wtarifa = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('tarifa');
							$wtarifa = $reconocido_clave->getAttributeNode('tarifa')->nodeValue;
							//tipoEmpresa = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('tresponsable');
							$tipoEmpresa = $reconocido_clave->getAttributeNode('tresponsable')->nodeValue;
							//nitEmpresa = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('nitresponsable');
							$nitEmpresa = $reconocido_clave->getAttributeNode('nitresponsable')->nodeValue;
							//concepto_cargo = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('concepto');
							$concepto_cargo = $reconocido_clave->getAttributeNode('concepto')->nodeValue;
							//wnconcepto =  $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('nconcepto');
							$wnconcepto =  $reconocido_clave->getAttributeNode('nconcepto')->nodeValue;
							//id_tope_afectado = $('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('id_tope_afectado');
							$id_tope_afectado = $reconocido_clave->getAttributeNode('id_tope_afectado')->nodeValue;
							
							/*
							echo "wnprocedimiento: $wnprocedimiento \n";
							echo "wnumerohab: $wnumerohab \n";
							echo "wvalor: $wvalor \n";
							echo "wreconocido: $wreconocido \n";
							echo "wprocedimiento: $wprocedimiento \n";
							echo "wresponsable: $wresponsable \n";
							echo "wnresponsable: $wnresponsable \n";
							echo "wtarifa: $wtarifa \n";
							echo "tipoEmpresa: $tipoEmpresa \n";
							echo "nitEmpresa: $nitEmpresa \n";
							echo "concepto_cargo: $concepto_cargo \n";
							echo "wnconcepto: $wnconcepto \n";
							echo "id_tope_afectado: $id_tope_afectado \n\n";
							*/
							
							//--
							//--Proceso para saber el tercero que corresponde a cada cargo de pension ; si es que se tiene
							$busc_terceros_usuario = $dom->getElementById('busc_terceros_usuario_'.$clave.'_'.$ndia);
							//if ($('#busc_terceros_usuario_'+clave+'_'+ndia).attr('contercero') =='si')
							if(!is_null($busc_terceros_usuario) && $busc_terceros_usuario->hasAttribute('contercero') && $busc_terceros_usuario->getAttributeNode('contercero')->nodeValue == 'si') {
								//if($('#porter_'+clave+'_'+ndia).length)
								$porter_clave = $dom->getElementById('porter_'.$clave.'_'.$ndia);
								if(!is_null($porter_clave))
								{
									//wtercero 				 =  $('#busc_terceros_usuario_'+clave+'_'+ndia).attr('ctercero');
									$wtercero 				 =  $busc_terceros_usuario->getAttributeNode('ctercero')->nodeValue;
									//wnomtercero			 =  $('#busc_terceros_usuario_'+clave+'_'+ndia).attr('nombre');
									$wnomtercero			 =  $busc_terceros_usuario->getAttributeNode('nombre')->nodeValue;
									//wtercero_especialidad	 =  $('#busc_terceros_usuario_'+clave+'_'+ndia).attr('cespecialidad');
									$wtercero_especialidad	 =  $busc_terceros_usuario->getAttributeNode('cespecialidad')->nodeValue;
									//wgraba_varios_terceros = 0;
									$wgraba_varios_terceros  = 0;
									//wporter  				 = $('#porter_'+clave+'_'+ndia).val();
									$wporter 				 = $porter_clave->nodeValue;
									//wtercero_unix 		 = $('#busc_terceros_usuario_'+clave+'_'+ndia).attr('ctercero_unix');							
									$wtercero_unix 			 = $busc_terceros_usuario->getAttributeNode('ctercero_unix')->nodeValue;
								}
							
							} else {
								$wtercero 					= '';
								$wnomtercero			 	= '';
								$wgraba_varios_terceros 	= 0;
								$wtercero_especialidad		= '';
								$wporter 					= '';
								$wtercero_unix 				= '';
							}
							
							/*
							echo "wtercero: $wtercero \n";
							echo "wnomtercero: $wnomtercero \n";
							echo "wgraba_varios_terceros: $wgraba_varios_terceros \n";
							echo "wtercero_especialidad: $wtercero_especialidad \n";
							echo "wporter: $wporter \n";
							echo "wtercero_unix: $wtercero_unix \n\n";
							*/
							$aux = ($j*1) + 1;
							$reconocido_clave_1 = $dom->getElementById('reconocido_clave'.$clave.'_'.$ndia.'_res'.$aux);
							
							if(!is_null($reconocido_clave_1)) {
								$bool_reconocido_clave = true;
							} else {
								$bool_reconocido_clave=false;
							}
							
							if($bool_reconocido_clave && ($reconocido_clave_1->getAttributeNode('valor')->nodeValue * 1) != 0 ) {
								$wparalelo = $reconocido_clave_1->getAttributeNode('paralelo')->nodeValue;
								$wvaltarExce=0;

							} else {
								$wparalelo ='off';
								$reconocido_clave2=$dom->getElementById('reconocido_clave'.$clave.'_'.$ndia);
								
								if(!is_null($reconocido_clave2)) {
								$reconocido_clave2=true;
								} else {
									$reconocido_clave2=false;
								}
								if(!$reconocido_clave2)
								{
									$wvaltarExce =0;
								}
								else
								{
									$wvaltarExce=$dom->getElementById('excedente_'.$clave.'_'.$ndia)->getAttributeNode('valor')->nodeValue;
									if($tiene_paquete)
									{
										
										$wvaltarExce= ($wvaltarExce*1) + ($auxiliarexcedente*1);
										$tiene_paquete=false;
									}
								}
							}
							$dato_clave = $clave;
							$dato_responsable = $wresponsable;
							$existe = false;
							/** Nunca entra EDWIN
								for($jj=0;$jj<sizeof($datos);$jj++){
								$datoget = $datos[$jj];
								echo $datoget;
								if($datoget['clave'] == $dato_clave && $datoget['wresponsable']==$dato_responsable ){
									if ($dom->getElementById('busc_terceros_usuario_'.$clave.'_'.$ndia)->getAttributeNode('valor')->nodeValue=='si')
									{
											$datoget['ndia'] 			= (($datoget['ndia'])*1) + 1;
											$datoget['whora_ingreso'] 	= $datoget['whora_ingreso'];
											$datoget['whora_egreso']		= $whora_egreso;
											$datoget['wfecha_ingreso']	= $datoget['fechacargo'];
											$datoget['wfecha_egreso']	= $fechacargo;
											$datoget['wreconocido']		= (($datoget['wreconocido']*1) + ($wreconocido*1))*1;
											$existe = true;
											
											$datos[$jj] = $datoget;
										
									}
									else
									{
											$datoget['ndia'] 			= (($datoget['ndia'])*1) + 1;
											$datoget['whora_ingreso'] 	= $datoget['whora_ingreso'];
											$datoget['whora_egreso']		= $whora_egreso;
											$datoget['wfecha_ingreso']	= $datoget['fechacargo'];
											$datoget['wfecha_egreso']	= $fechacargo;
											$datoget['wreconocido']		= (($datoget['wreconocido']*1) + ($wreconocido*1))*1;
											$existe = true;
											
											$datos[jj] = $datoget;
										
									}
									
								}
								
							}
							*/
						
							//-- Aqui se construye el array principal inicial , este array contiene los datos de pension resumidos por traslado, pero
							//-- no tenia en cuenta si en estos dias habian varios terceros y se tenia que partir el cargo, por esto se hizo otro vector mas abajo
							//-- datosauxfinal 
							if( $existe == false ){
								$dato = array();
								$dato['clave'] 					= $clave;
								$dato['ndia']					= 1 ;
								$dato['fechacargo']				= $fechacargo;
								$dato['whora_ingreso']			= $whora_ingreso;
								$dato['whora_egreso']			= $whora_egreso;
								$dato['ccogra']					= $ccogra;
								$dato['wnprocedimiento']		= $wnprocedimiento;
								$dato['wnumerohab']				= $wnumerohab;
								$dato['wvalor']					= $wvalor;
								$dato['wreconocido']			= $wreconocido;
								$dato['wprocedimiento']			= $wprocedimiento;
								$dato['wresponsable']			= $wresponsable;
								$dato['wnresponsable']			= $wnresponsable;
								$dato['wtarifa']				= $wtarifa;
								$dato['tipoEmpresa']			= $tipoEmpresa;
								$dato['nitEmpresa']				= $nitEmpresa;
								$dato['concepto_cargo']			= $concepto_cargo;
								$dato['wnconcepto']				= $wnconcepto;
								$dato['id_tope_afectado']		= $id_tope_afectado;
								$dato['wtercero']				= $wtercero;
								$dato['wtercero_nombre']		= $wnomtercero;
								$dato['wtercero_unix']			= $wtercero_unix;
								$dato['wtercero_especialidad']	= $wtercero_especialidad;
								$dato['wgraba_varios_terceros']	= $wgraba_varios_terceros;
								$dato['wporter']				= $wporter;
								$dato['wparalelo']				= $wparalelo;
								$dato['wvaltarExce']			= $wvaltarExce;
								$dato['wfecha_ingreso']			= $fechacargo;
								$dato['wfecha_egreso']			= $fechacargo;
								
								$datos[] = $dato;
							}
							//-------------------------------------------------------
							
							// se llena el objeto datoaux
							//-- este vector contendra todos los cargos dia por dia, detallado , no resumido, para validar en unix si tiene tarifa, o si
							//-- el tercero tiene concepto amarrado a los honorarios
							$datoaux 							= array();
							$datoaux['clave'] 					= $clave;
							$datoaux['ndia']					= 1 ;
							$datoaux['fechacargo']				= $fechacargo;
							$datoaux['whora_ingreso']			= $whora_ingreso;
							$datoaux['whora_egreso']			= $whora_egreso;
							$datoaux['ccogra']					= $ccogra;
							$datoaux['wnprocedimiento']			= $wnprocedimiento;
							$datoaux['wnumerohab']				= $wnumerohab;
							$datoaux['wvalor']					= $wvalor;
							$datoaux['wreconocido']				= $wreconocido;
							$datoaux['wprocedimiento']			= $wprocedimiento;
							$datoaux['wresponsable']			= $wresponsable;
							$datoaux['wnresponsable']			= $wnresponsable;
							$datoaux['wtarifa']					= $wtarifa;
							$datoaux['tipoEmpresa']				= $tipoEmpresa;
							$datoaux['nitEmpresa']				= $nitEmpresa;
							$datoaux['concepto_cargo']			= $concepto_cargo;
							$datoaux['wnconcepto']				= $wnconcepto;
							$datoaux['id_tope_afectado']		= $id_tope_afectado;
							$datoaux['wtercero']				= $wtercero;
							$datoaux['wtercero_nombre']			= $wnomtercero;
							$datoaux['wtercero_unix']			= $wtercero_unix;
							$datoaux['wtercero_especialidad']	= $wtercero_especialidad;
							$datoaux['wgraba_varios_terceros']	= $wgraba_varios_terceros;
							$datoaux['wporter']					= $wporter;
							$datoaux['wparalelo']				= $wparalelo;
							$datoaux['wvaltarExce']				= $wvaltarExce;
							$datoaux['wfecha_ingreso']			= $fechacargo;
							$datoaux['wfecha_egreso']			= $fechacargo;
							$datosaux[] = $datoaux;
							
							if($numero_responsables == 1)
							{
								$contadore++;
							
								//-Se inicia proceso para crear un vector discriminando  cargos por cada tercero
								//------------------------------------------------
								if($diasauxiliar1 == 0)
								{
									
									$auxclave = $clave;
									
									$datoauxfinal_clave					= '';
									$datoauxfinal_ndia					= 0;
									$datoauxfinal_fechacargo			= $fechacargo;
									$datoauxfinal_whora_ingreso			= $whora_ingreso;
									$datoauxfinal_whora_egreso			= '';
									$datoauxfinal_ccogra				= '';
									$datoauxfinal_wnprocedimiento		= '';
									$datoauxfinal_wnumerohab			= '';
									$datoauxfinal_wvalor				= '';
									$datoauxfinal_wreconocido			= 0;
									$datoauxfinal_wprocedimiento		= '';
									$datoauxfinal_wresponsable			= '';
									$datoauxfinal_wnresponsable			= '';
									$datoauxfinal_wtarifa				= '';
									$datoauxfinal_tipoEmpresa			= '';
									$datoauxfinal_nitEmpresa			= '';
									$datoauxfinal_concepto_cargo		= '';
									$datoauxfinal_wnconcepto			= '';
									$datoauxfinal_id_tope_afectado		= '';
									$datoauxfinal_wtercero				= '';
									$datoauxfinal_wtercero_nom			= '';
									$datoauxfinal_wtercero_unix			= '';
									$datoauxfinal_wtercero_especialidad	= '';
									$datoauxfinal_wgraba_varios_terceros	= '';
									$datoauxfinal_wporter				= '';
									$datoauxfinal_wparalelo				= '';
									$datoauxfinal_wvaltarExce			= 0;
									$datoauxfinal_wfecha_ingreso			= $fechacargo;
									$datoauxfinal_wfecha_egreso			= '';
								}	
								
								if( $clave == $auxclave )
								{
									
									if($datoaux['wtercero'] == $auxtercero  )
									{
										
										$datoauxfinal_clave					= $clave;
										$datoauxfinal_ndia					= (($datoauxfinal_ndia * 1) + 1);
										$datoauxfinal_fechacargo				= $datoauxfinal_fechacargo;
										$datoauxfinal_whora_ingreso			= $datoauxfinal_whora_ingreso;
										$datoauxfinal_whora_egreso			= $whora_egreso;
										$datoauxfinal_ccogra					= $ccogra;
										$datoauxfinal_wnprocedimiento		= $wnprocedimiento;
										$datoauxfinal_wnumerohab				= $wnumerohab;
										$datoauxfinal_wvalor					= $wvalor;
										$datoauxfinal_wreconocido			= (($datoauxfinal_wreconocido * 1) + ($wreconocido*1))*1;
										$datoauxfinal_wprocedimiento			= $wprocedimiento;
										$datoauxfinal_wresponsable			= $wresponsable;
										$datoauxfinal_wnresponsable			= $wnresponsable;
										$datoauxfinal_wtarifa				= $wtarifa;
										$datoauxfinal_tipoEmpresa			= $tipoEmpresa;
										$datoauxfinal_nitEmpresa				= $nitEmpresa;
										$datoauxfinal_concepto_cargo			= $concepto_cargo;
										$datoauxfinal_wnconcepto				= $wnconcepto;
										$datoauxfinal_id_tope_afectado		= $id_tope_afectado;
										$datoauxfinal_wtercero				= $wtercero;
										$datoauxfinal_wtercero_nom			= $wnomtercero;
										$datoauxfinal_wtercero_unix			= $wtercero_unix;
										$datoauxfinal_wtercero_especialidad	= $wtercero_especialidad;
										$datoauxfinal_wgraba_varios_terceros	= $wgraba_varios_terceros;
										$datoauxfinal_wporter				= $wporter;
										$datoauxfinal_wparalelo				= $wparalelo;
										$datoauxfinal_wvaltarExce			= (($datoauxfinal_wvaltarExce * 1) + ($wvaltarExce*1))*1;
										$datoauxfinal_wfecha_ingreso			= $datoauxfinal_wfecha_ingreso;
										$datoauxfinal_wfecha_egreso			= $fechacargo;
										
									}
									else
									{
										$datoauxfinal = array();
										$datoauxfinal['clave'] = $datoauxfinal_clave;
										$datoauxfinal['ndia'] = $datoauxfinal_ndia;
										$datoauxfinal['fechacargo'] = $datoauxfinal_fechacargo	;
										$datoauxfinal['whora_ingreso'] = $datoauxfinal_whora_ingreso;
										$datoauxfinal['whora_egreso'] = $datoauxfinal_whora_egreso;
										$datoauxfinal['ccogra']	 = $datoauxfinal_ccogra	;
										$datoauxfinal['wnprocedimiento'] = $datoauxfinal_wnprocedimiento;
										$datoauxfinal['wnumerohab'] = $datoauxfinal_wnumerohab	;
										$datoauxfinal['wvalor'] = $datoauxfinal_wvalor;
										$datoauxfinal['wreconocido'] = $datoauxfinal_wreconocido;
										$datoauxfinal['wprocedimiento'] = $datoauxfinal_wprocedimiento;
										$datoauxfinal['wresponsable'] = $datoauxfinal_wresponsable;
										$datoauxfinal['wnresponsable'] = $datoauxfinal_wnresponsable;
										$datoauxfinal['wtarifa'] = $datoauxfinal_wtarifa;
										$datoauxfinal['tipoEmpresa']	 = $datoauxfinal_tipoEmpresa;
										$datoauxfinal['nitEmpresa'] = $datoauxfinal_nitEmpresa;
										$datoauxfinal['concepto_cargo'] = $datoauxfinal_concepto_cargo;
										$datoauxfinal['wnconcepto']	 = $datoauxfinal_wnconcepto;
										$datoauxfinal['id_tope_afectado'] = $datoauxfinal_id_tope_afectado;
										$datoauxfinal['wtercero'] = $datoauxfinal_wtercero;
										$datoauxfinal['wtercero_nombre'] = $datoauxfinal_wtercero_nom;
										$datoauxfinal['wtercero_unix'] = $datoauxfinal_wtercero_unix;
										$datoauxfinal['wtercero_especialidad'] = $datoauxfinal_wtercero_especialidad;
										$datoauxfinal['wgraba_varios_terceros'] = $datoauxfinal_wgraba_varios_terceros;
										$datoauxfinal['wporter'] = $datoauxfinal_wporter;
										$datoauxfinal['wparalelo'] = $datoauxfinal_wparalelo;
										$datoauxfinal['wvaltarExce'] = $datoauxfinal_wvaltarExce;
										$datoauxfinal['wfecha_ingreso'] = $datoauxfinal_wfecha_ingreso;
										$datoauxfinal['wfecha_egreso'] = $datoauxfinal_wfecha_egreso;
										
										//-- se hace push
										$datosauxfinal[] = $datoauxfinal;
										
										//-- se inicializan de nuevo las variables
										$datoauxfinal_clave					= $clave;
										$datoauxfinal_ndia					= 1 ;
										$datoauxfinal_fechacargo			= $fechacargo;
										$datoauxfinal_whora_ingreso			= $whora_ingreso;
										$datoauxfinal_whora_egreso			= $whora_egreso;
										$datoauxfinal_ccogra				= $ccogra;
										$datoauxfinal_wnprocedimiento		= $wnprocedimiento;
										$datoauxfinal_wnumerohab			= $wnumerohab;
										$datoauxfinal_wvalor				= $wvalor;
										$datoauxfinal_wreconocido			= $wreconocido ;
										$datoauxfinal_wprocedimiento		= $wprocedimiento;
										$datoauxfinal_wresponsable			= $wresponsable;
										$datoauxfinal_wnresponsable			= $wnresponsable;
										$datoauxfinal_wtarifa				= $wtarifa;
										$datoauxfinal_tipoEmpresa			= $tipoEmpresa;
										$datoauxfinal_nitEmpresa			= $nitEmpresa;
										$datoauxfinal_concepto_cargo		= $concepto_cargo;
										$datoauxfinal_wnconcepto			= $wnconcepto;
										$datoauxfinal_id_tope_afectado		= $id_tope_afectado;
										$datoauxfinal_wtercero				= $wtercero;
										$datoauxfinal_wtercero_nom				= $wnomtercero;
										$datoauxfinal_wtercero_unix				= $wtercero_unix;
										$datoauxfinal_wtercero_especialidad		= $wtercero_especialidad;
										$datoauxfinal_wgraba_varios_terceros	= $wgraba_varios_terceros;
										$datoauxfinal_wporter				= $wporter;
										$datoauxfinal_wparalelo				= $wparalelo;
										$datoauxfinal_wvaltarExce			= $wvaltarExce;
										$datoauxfinal_wfecha_ingreso			= $fechacargo;
										$datoauxfinal_wfecha_egreso			= $fechacargo;
									}
								}
								else
								{
									$datoauxfinal = array();
									$datoauxfinal['clave'] 					= $datoauxfinal_clave;
									$datoauxfinal['ndia'] 					= $datoauxfinal_ndia;
									$datoauxfinal['fechacargo'] 			= $datoauxfinal_fechacargo	;
									$datoauxfinal['whora_ingreso'] 			= $datoauxfinal_whora_ingreso;
									$datoauxfinal['whora_egreso'] 			= $datoauxfinal_whora_egreso;
									$datoauxfinal['ccogra']	 				= $datoauxfinal_ccogra	;
									$datoauxfinal['wnprocedimiento'] 		= $datoauxfinal_wnprocedimiento;
									$datoauxfinal['wnumerohab'] 			= $datoauxfinal_wnumerohab	;
									$datoauxfinal['wvalor'] 				= $datoauxfinal_wvalor;
									$datoauxfinal['wreconocido'] 			= $datoauxfinal_wreconocido;
									$datoauxfinal['wprocedimiento'] 		= $datoauxfinal_wprocedimiento;
									$datoauxfinal['wresponsable'] 			= $datoauxfinal_wresponsable;
									$datoauxfinal['wnresponsable'] 			= $datoauxfinal_wnresponsable;
									$datoauxfinal['wtarifa'] 				= $datoauxfinal_wtarifa;
									$datoauxfinal['tipoEmpresa']	 		= $datoauxfinal_tipoEmpresa;
									$datoauxfinal['nitEmpresa'] 			= $datoauxfinal_nitEmpresa;
									$datoauxfinal['concepto_cargo'] 		= $datoauxfinal_concepto_cargo;
									$datoauxfinal['wnconcepto']	 			= $datoauxfinal_wnconcepto;
									$datoauxfinal['id_tope_afectado'] 		= $datoauxfinal_id_tope_afectado;
									$datoauxfinal['wtercero'] 				= $datoauxfinal_wtercero;
									$datoauxfinal['wtercero_nombre']		= $datoauxfinal_wtercero_nom;
									$datoauxfinal['wtercero_unix'] 			= $datoauxfinal_wtercero_unix;
									$datoauxfinal['wtercero_especialidad'] 	= $datoauxfinal_wtercero_especialidad;
									$datoauxfinal['wgraba_varios_terceros'] = $datoauxfinal_wgraba_varios_terceros;
									$datoauxfinal['wporter'] 				= $datoauxfinal_wporter;
									$datoauxfinal['wparalelo'] 				= $datoauxfinal_wparalelo;
									$datoauxfinal['wvaltarExce'] 			= $datoauxfinal_wvaltarExce;
									$datoauxfinal['wfecha_ingreso'] 		= $datoauxfinal_wfecha_ingreso;
									$datoauxfinal['wfecha_egreso'] 			= $datoauxfinal_wfecha_egreso;
									//-- se hace push
									
									$datosauxfinal[] = $datoauxfinal;
									//-- se inicializan de nuevo las variables
									$datoauxfinal_clave						= $clave;
									$datoauxfinal_ndia						= 1 ;
									$datoauxfinal_fechacargo				= $fechacargo;
									$datoauxfinal_whora_ingreso				= $whora_ingreso;
									$datoauxfinal_whora_egreso				= $whora_egreso;
									$datoauxfinal_ccogra					= $ccogra;
									$datoauxfinal_wnprocedimiento			= $wnprocedimiento;
									$datoauxfinal_wnumerohab				= $wnumerohab;
									$datoauxfinal_wvalor					= $wvalor;
									$datoauxfinal_wreconocido				= $wreconocido ;
									$datoauxfinal_wprocedimiento			= $wprocedimiento;
									$datoauxfinal_wresponsable				= $wresponsable;
									$datoauxfinal_wnresponsable				= $wnresponsable;
									$datoauxfinal_wtarifa					= $wtarifa;
									$datoauxfinal_tipoEmpresa				= $tipoEmpresa;
									$datoauxfinal_nitEmpresa				= $nitEmpresa;
									$datoauxfinal_concepto_cargo			= $concepto_cargo;
									$datoauxfinal_wnconcepto				= $wnconcepto;
									$datoauxfinal_id_tope_afectado			= $id_tope_afectado;
									$datoauxfinal_wtercero					= $wtercero;
									$datoauxfinal_wtercero_nom				= $wnomtercero;
									$datoauxfinal_wtercero_unix				= $wtercero_unix;
									$datoauxfinal_wtercero_especialidad		= $wtercero_especialidad;
									$datoauxfinal_wgraba_varios_terceros	= $wgraba_varios_terceros;
									$datoauxfinal_wporter					= $wporter;
									$datoauxfinal_wparalelo					= $wparalelo;
									$datoauxfinal_wvaltarExce				= $wvaltarExce;
									$datoauxfinal_wfecha_ingreso			= $fechacargo;
									$datoauxfinal_wfecha_egreso				= $fechacargo;
								
								}
								
								$diasauxiliar1++;
								if($diasauxiliar1 == $diasauxiliar)
								{
									$datoauxfinal 							= array();
									$datoauxfinal['clave'] 					= $datoauxfinal_clave;
									$datoauxfinal['ndia'] 					= $datoauxfinal_ndia;
									$datoauxfinal['fechacargo'] 			= $datoauxfinal_fechacargo	;
									$datoauxfinal['whora_ingreso'] 			= $datoauxfinal_whora_ingreso;
									$datoauxfinal['whora_egreso'] 			= $datoauxfinal_whora_egreso;
									$datoauxfinal['ccogra']	 				= $datoauxfinal_ccogra	;
									$datoauxfinal['wnprocedimiento'] 		= $datoauxfinal_wnprocedimiento;
									$datoauxfinal['wnumerohab'] 			= $datoauxfinal_wnumerohab	;
									$datoauxfinal['wvalor'] 				= $datoauxfinal_wvalor;
									$datoauxfinal['wreconocido'] 			= $datoauxfinal_wreconocido;
									$datoauxfinal['wprocedimiento'] 		= $datoauxfinal_wprocedimiento;
									$datoauxfinal['wresponsable'] 			= $datoauxfinal_wresponsable;
									$datoauxfinal['wnresponsable'] 			= $datoauxfinal_wnresponsable;
									$datoauxfinal['wtarifa'] 				= $datoauxfinal_wtarifa;
									$datoauxfinal['tipoEmpresa']	 		= $datoauxfinal_tipoEmpresa;
									$datoauxfinal['nitEmpresa'] 			= $datoauxfinal_nitEmpresa;
									$datoauxfinal['concepto_cargo'] 		= $datoauxfinal_concepto_cargo;
									$datoauxfinal['wnconcepto']	 			= $datoauxfinal_wnconcepto;
									$datoauxfinal['id_tope_afectado'] 		= $datoauxfinal_id_tope_afectado;
									$datoauxfinal['wtercero'] 				= $datoauxfinal_wtercero;
									$datoauxfinal['wtercero_nombre'] 		= $datoauxfinal_wtercero_nom;
									$datoauxfinal['wtercero_unix'] 			= $datoauxfinal_wtercero_unix;
									$datoauxfinal['wtercero_especialidad']	= $datoauxfinal_wtercero_especialidad;
									$datoauxfinal['wgraba_varios_terceros'] = $datoauxfinal_wgraba_varios_terceros;
									$datoauxfinal['wporter'] 				= $datoauxfinal_wporter;
									$datoauxfinal['wparalelo'] 				= $datoauxfinal_wparalelo;
									$datoauxfinal['wvaltarExce'] 			= $datoauxfinal_wvaltarExce;
									$datoauxfinal['wfecha_ingreso'] 		= $datoauxfinal_wfecha_ingreso;
									$datoauxfinal['wfecha_egreso'] 			= $datoauxfinal_wfecha_egreso;
									
									$datosauxfinal[]	=	$datoauxfinal;
									
								}
								//Fin de construccion de vector unificado, detallando dias de cobro para cada tercero-----------------------------------------------
								//---------------------------------------------
								$auxclave = $clave;
								$auxtercero = $datoaux['wtercero'];
							
							}	else if($numero_responsables == 2) {
								
								if($j==1) 
								{
									if($diasauxiliar1 == 0) 
									{
										$auxclave = $clave;
										
										$datoauxfinal_clave						= 0;
										$datoauxfinal_ndia						= 0;
										$datoauxfinal_fechacargo				= $fechacargo;
										$datoauxfinal_whora_ingreso				= $whora_ingreso;
										$datoauxfinal_whora_egreso				= '';
										$datoauxfinal_ccogra					= '';
										$datoauxfinal_wnprocedimiento			= '';
										$datoauxfinal_wnumerohab				= '';
										$datoauxfinal_wvalor					= '';
										$datoauxfinal_wreconocido				= 0;
										$datoauxfinal_wprocedimiento			= '';
										$datoauxfinal_wresponsable				= '';
										$datoauxfinal_wnresponsable				= '';
										$datoauxfinal_wtarifa					= '';
										$datoauxfinal_tipoEmpresa				= '';
										$datoauxfinal_nitEmpresa				= '';
										$datoauxfinal_concepto_cargo			= '';
										$datoauxfinal_wnconcepto				= '';
										$datoauxfinal_id_tope_afectado			= '';
										$datoauxfinal_wtercero					= '';
										$datoauxfinal_wtercero_nom				= '';
										$datoauxfinal_wtercero_unix				= '';
										$datoauxfinal_wtercero_especialidad		= '';
										$datoauxfinal_wgraba_varios_terceros	= '';
										$datoauxfinal_wporter					= '';
										$datoauxfinal_wparalelo					= '';
										$datoauxfinal_wvaltarExce				= 0;
										$datoauxfinal_wfecha_ingreso			= $fechacargo;
										$datoauxfinal_wfecha_egreso				= '';
									}
								}
								if($j==2)
								{
									if($diasauxiliar2 == 0)
									{
										$auxclave = $clave;
										$datoauxfinal_clave2					= 0;
										$datoauxfinal_ndia2						= 0;
										$datoauxfinal_fechacargo2				= $fechacargo;
										$datoauxfinal_whora_ingreso2			= $whora_ingreso;
										$datoauxfinal_whora_egreso2				= '';
										$datoauxfinal_ccogra2					= '';
										$datoauxfinal_wnprocedimiento2			= '';
										$datoauxfinal_wnumerohab2				= '';
										$datoauxfinal_wvalor2					= '';
										$datoauxfinal_wreconocido2				= 0;
										$datoauxfinal_wprocedimiento2			= '';
										$datoauxfinal_wresponsable2				= '';
										$datoauxfinal_wnresponsable2			= '';
										$datoauxfinal_wtarifa2					= '';
										$datoauxfinal_tipoEmpresa2				= '';
										$datoauxfinal_nitEmpresa2				= '';
										$datoauxfinal_concepto_cargo2			= '';
										$datoauxfinal_wnconcepto2				= '';
										$datoauxfinal_id_tope_afectado2			= '';
										$datoauxfinal_wtercero2					= '';
										$datoauxfinal_wtercero_nom2				= '';
										$datoauxfinal_wtercero_unix2			= '';
										$datoauxfinal_wtercero_especialidad2	= '';
										$datoauxfinal_wgraba_varios_terceros2	= '';
										$datoauxfinal_wporter2					= '';
										$datoauxfinal_wparalelo2				= '';
										$datoauxfinal_wvaltarExce2				= 0;
										$datoauxfinal_wfecha_ingreso2			= $fechacargo;
										$datoauxfinal_wfecha_egreso2			= '';
									}
								}
								
								//console.log("responsable: "+j+ " clave: "+clave+"---auxclave: "+auxclave);
								if( $clave == $auxclave )
								{
									if($j==1)
									{
										if($datoaux['wtercero'] == $auxtercero  )
										{
											//alert("entro responsable");
											$datoauxfinal_clave					= $clave;
											$datoauxfinal_ndia					= (($datoauxfinal_ndia * 1) + 1);
											//alert(datoauxfinal_ndia);
											$datoauxfinal_fechacargo			= $datoauxfinal_fechacargo;
											$datoauxfinal_whora_ingreso			= $datoauxfinal_whora_ingreso;
											$datoauxfinal_whora_egreso			= $whora_egreso;
											$datoauxfinal_ccogra				= $ccogra;
											$datoauxfinal_wnprocedimiento		= $wnprocedimiento;
											$datoauxfinal_wnumerohab			= $wnumerohab;
											$datoauxfinal_wvalor				= $wvalor;
											$datoauxfinal_wreconocido			= (($datoauxfinal_wreconocido * 1) + ($wreconocido*1))*1;
											$datoauxfinal_wprocedimiento		= $wprocedimiento;
											$datoauxfinal_wresponsable			= $wresponsable;
											$datoauxfinal_wnresponsable			= $wnresponsable;
											$datoauxfinal_wtarifa				= $wtarifa;
											$datoauxfinal_tipoEmpresa			= $tipoEmpresa;
											$datoauxfinal_nitEmpresa			= $nitEmpresa;
											$datoauxfinal_concepto_cargo		= $concepto_cargo;
											$datoauxfinal_wnconcepto			= $wnconcepto;
											$datoauxfinal_id_tope_afectado		= $id_tope_afectado;
											$datoauxfinal_wtercero				= $wtercero;
											$datoauxfinal_wtercero_nom			= $wnomtercero;
											$datoauxfinal_wtercero_unix			= $wtercero_unix;
											$datoauxfinal_wtercero_especialidad	= $wtercero_especialidad;
											$datoauxfinal_wgraba_varios_terceros= $wgraba_varios_terceros;
											$datoauxfinal_wporter				= $wporter;
											$datoauxfinal_wparalelo				= $wparalelo;
											$datoauxfinal_wvaltarExce			= (($datoauxfinal_wvaltarExce * 1) + ($wvaltarExce*1))*1;
											$datoauxfinal_wfecha_ingreso		= $datoauxfinal_wfecha_ingreso;
											$datoauxfinal_wfecha_egreso			= $fechacargo;
											//console.log(datoauxfinal_ndia);
										}
										else
										{
											$datoauxfinal 							= array();
											$datoauxfinal['clave'] 					= $datoauxfinal_clave;
											$datoauxfinal['ndia'] 					= $datoauxfinal_ndia;
											$datoauxfinal['fechacargo'] 			= $datoauxfinal_fechacargo	;
											$datoauxfinal['whora_ingreso'] 			= $datoauxfinal_whora_ingreso;
											$datoauxfinal['whora_egreso'] 			= $datoauxfinal_whora_egreso;
											$datoauxfinal['ccogra']	 				= $datoauxfinal_ccogra	;
											$datoauxfinal['wnprocedimiento'] 		= $datoauxfinal_wnprocedimiento;
											$datoauxfinal['wnumerohab'] 			= $datoauxfinal_wnumerohab	;
											$datoauxfinal['wvalor'] 				= $datoauxfinal_wvalor;
											$datoauxfinal['wreconocido'] 			= $datoauxfinal_wreconocido;
											$datoauxfinal['wprocedimiento'] 		= $datoauxfinal_wprocedimiento;
											$datoauxfinal['wresponsable'] 			= $datoauxfinal_wresponsable;
											$datoauxfinal['wnresponsable'] 			= $datoauxfinal_wnresponsable;
											$datoauxfinal['wtarifa'] 				= $datoauxfinal_wtarifa;
											$datoauxfinal['tipoEmpresa']	 		= $datoauxfinal_tipoEmpresa;
											$datoauxfinal['nitEmpresa'] 			= $datoauxfinal_nitEmpresa;
											$datoauxfinal['concepto_cargo'] 		= $datoauxfinal_concepto_cargo;
											$datoauxfinal['wnconcepto']	 			= $datoauxfinal_wnconcepto;
											$datoauxfinal['id_tope_afectado'] 		= $datoauxfinal_id_tope_afectado;
											$datoauxfinal['wtercero'] 				= $datoauxfinal_wtercero;
											$datoauxfinal['wtercero_nombre'] 		= $datoauxfinal_wtercero_nom;
											$datoauxfinal['wtercero_unix'] 			= $datoauxfinal_wtercero_unix;
											$datoauxfinal['wtercero_especialidad']	= $datoauxfinal_wtercero_especialidad;
											$datoauxfinal['wgraba_varios_terceros'] = $datoauxfinal_wgraba_varios_terceros;
											$datoauxfinal['wporter'] 				= $datoauxfinal_wporter;
											$datoauxfinal['wparalelo'] 				= $datoauxfinal_wparalelo;
											$datoauxfinal['wvaltarExce'] 			= $datoauxfinal_wvaltarExce;
											$datoauxfinal['wfecha_ingreso'] 		= $datoauxfinal_wfecha_ingreso;
											$datoauxfinal['wfecha_egreso'] 			= $datoauxfinal_wfecha_egreso;
											
											//-- se hace push
											//console.log("push 6");
											//echo "push 6 \n";
											//console.log(datoauxfinal);
											$datosauxfinal[] = $datoauxfinal;
											
											//-- se inicializan de nuevo las variables
											$datoauxfinal_clave					= $clave;
											$datoauxfinal_ndia					= 1 ;
											$datoauxfinal_fechacargo			= $fechacargo;
											$datoauxfinal_whora_ingreso			= $whora_ingreso;
											$datoauxfinal_whora_egreso			= $whora_egreso;
											$datoauxfinal_ccogra				= $ccogra;
											$datoauxfinal_wnprocedimiento		= $wnprocedimiento;
											$datoauxfinal_wnumerohab			= $wnumerohab;
											$datoauxfinal_wvalor				= $wvalor;
											$datoauxfinal_wreconocido			= $wreconocido ;
											$datoauxfinal_wprocedimiento		= $wprocedimiento;
											$datoauxfinal_wresponsable			= $wresponsable;
											$datoauxfinal_wnresponsable			= $wnresponsable;
											$datoauxfinal_wtarifa				= $wtarifa;
											$datoauxfinal_tipoEmpresa			= $tipoEmpresa;
											$datoauxfinal_nitEmpresa			= $nitEmpresa;
											$datoauxfinal_concepto_cargo		= $concepto_cargo;
											$datoauxfinal_wnconcepto			= $wnconcepto;
											$datoauxfinal_id_tope_afectado		= $id_tope_afectado;
											$datoauxfinal_wtercero				= $wtercero;
											$datoauxfinal_wtercero_nom			= $wnomtercero;
											$datoauxfinal_wtercero_unix			= $wtercero_unix;
											$datoauxfinal_wtercero_especialidad	= $wtercero_especialidad;
											$datoauxfinal_wgraba_varios_terceros= $wgraba_varios_terceros;
											$datoauxfinal_wporter				= $wporter;
											$datoauxfinal_wparalelo				= $wparalelo;
											$datoauxfinal_wvaltarExce			= $wvaltarExce;
											$datoauxfinal_wfecha_ingreso		= $fechacargo;
											$datoauxfinal_wfecha_egreso			= $fechacargo;
											
										}
									}
									else if($j==2)
									{
										if($datoaux['wtercero'] == $auxtercero  )
										{
											//console.log("responsable 2 datos tercero iguales");
											$datoauxfinal_clave2	= $clave;
											
											//---En empresas paf solo suma si es diferente de cero
											//$paf_reconocido_clave_res
											if($reconocido_clave->hasAttribute('paf')) 
											{
												if($reconocido_clave->getAttributeNode('cuenta')->nodeValue == 'no') {
													$datoauxfinal_ndia2		= (($datoauxfinal_ndia2 * 1));
													$datoauxfinal_wvalor2	= 0;
												} else
												{
													$datoauxfinal_ndia2		= (($datoauxfinal_ndia2 * 1) + 1);
													$datoauxfinal_wvalor2	= $wvalor;
												}
												
											} else 
											{
												$datoauxfinal_ndia2		= (($datoauxfinal_ndia2 * 1) + 1);
												$datoauxfinal_wvalor2	= $wvalor;
											}
											
											$datoauxfinal_fechacargo2				= $datoauxfinal_fechacargo2;
											$datoauxfinal_whora_ingreso2			= $datoauxfinal_whora_ingreso2;
											$datoauxfinal_whora_egreso2				= $whora_egreso;
											$datoauxfinal_ccogra2					= $ccogra;
											$datoauxfinal_wnprocedimiento2			= $wnprocedimiento;
											$datoauxfinal_wnumerohab2				= $wnumerohab;
											//datoauxfinal_wvalor2					= wvalor;
											$datoauxfinal_wreconocido2				= (($datoauxfinal_wreconocido2 * 1) + ($wreconocido*1))*1;
											//alert(datoauxfinal_wreconocido2);
											$datoauxfinal_wprocedimiento2			= $wprocedimiento;
											$datoauxfinal_wresponsable2				= $wresponsable;
											$datoauxfinal_wnresponsable2			= $wnresponsable;
											$datoauxfinal_wtarifa2					= $wtarifa;
											$datoauxfinal_tipoEmpresa2				= $tipoEmpresa;
											$datoauxfinal_nitEmpresa2				= $nitEmpresa;
											$datoauxfinal_concepto_cargo2			= $concepto_cargo;
											$datoauxfinal_wnconcepto2				= $wnconcepto;
											$datoauxfinal_id_tope_afectado2			= $id_tope_afectado;
											$datoauxfinal_wtercero2					= $wtercero;
											$datoauxfinal_wtercero_nom2				= $wnomtercero;
											$datoauxfinal_wtercero_unix2			= $wtercero_unix;
											$datoauxfinal_wtercero_especialidad2	= $wtercero_especialidad;
											$datoauxfinal_wgraba_varios_terceros2	= $wgraba_varios_terceros;
											$datoauxfinal_wporter2					= $wporter;
											$datoauxfinal_wparalelo2				= $wparalelo;
											
											$datoauxfinal_wvaltarExce2				= (($datoauxfinal_wvaltarExce2 * 1) + ($wvaltarExce*1))*1;
											
											$datoauxfinal_wfecha_ingreso2			= $datoauxfinal_wfecha_ingreso2;
											$datoauxfinal_wfecha_egreso2			= $fechacargo;
											
										}
										else
										{
											// --> 2020-03-16: Jerson Trujillo, cambian todas la variables ejem datoauxfinal_clave por datoauxfinal_clave2
											//	ya que generaba un error js que decia que las variables no existian
											$datoauxfinal2 								= array();
											$datoauxfinal2['clave'] 					= $datoauxfinal_clave2;
											$datoauxfinal2['ndia'] 						= $datoauxfinal_ndia2;
											$datoauxfinal2['fechacargo'] 				= $datoauxfinal_fechacargo2	;
											$datoauxfinal2['whora_ingreso'] 			= $datoauxfinal_whora_ingreso2;
											$datoauxfinal2['whora_egreso'] 				= $datoauxfinal_whora_egreso2;
											$datoauxfinal2['ccogra']	 				= $datoauxfinal_ccogra2	;
											$datoauxfinal2['wnprocedimiento'] 			= $datoauxfinal_wnprocedimiento2;
											$datoauxfinal2['wnumerohab'] 				= $datoauxfinal_wnumerohab2	;
											$datoauxfinal2['wvalor'] 					= $datoauxfinal_wvalor2;
											$datoauxfinal2['wreconocido'] 				= $datoauxfinal_wreconocido2;
											//alert(datoauxfinal_wreconocido2);
											$datoauxfinal2['wprocedimiento'] 			= $datoauxfinal_wprocedimiento2;
											$datoauxfinal2['wresponsable'] 				= $datoauxfinal_wresponsable2;
											$datoauxfinal2['wnresponsable'] 			= $datoauxfinal_wnresponsable2;
											$datoauxfinal2['wtarifa'] 					= $datoauxfinal_wtarifa2;
											$datoauxfinal2['tipoEmpresa']	 			= $datoauxfinal_tipoEmpresa2;
											$datoauxfinal2['nitEmpresa'] 				= $datoauxfinal_nitEmpresa2;
											$datoauxfinal2['concepto_cargo'] 			= $datoauxfinal_concepto_cargo2;
											$datoauxfinal2['wnconcepto']	 			= $datoauxfinal_wnconcepto2;
											$datoauxfinal2['id_tope_afectado'] 			= $datoauxfinal_id_tope_afectado2;
											$datoauxfinal2['wtercero'] 					= $datoauxfinal_wtercero2;
											$datoauxfinal2['wtercero_nombre'] 			= $datoauxfinal_wtercero_nom2;
											$datoauxfinal2['wtercero_unix'] 			= $datoauxfinal_wtercero_unix2;
											$datoauxfinal2['wtercero_especialidad'] 	= $datoauxfinal_wtercero_especialidad2;
											$datoauxfinal2['wgraba_varios_terceros']	= $datoauxfinal_wgraba_varios_terceros2;
											$datoauxfinal2['wporter'] 					= $datoauxfinal_wporter2;
											$datoauxfinal2['wparalelo'] 				= $datoauxfinal_wparalelo2;
											$datoauxfinal2['wvaltarExce'] 				= $datoauxfinal_wvaltarExce2;
											$datoauxfinal2['wfecha_ingreso'] 			= $datoauxfinal_wfecha_ingreso2;
											$datoauxfinal2['wfecha_egreso'] 			= $datoauxfinal_wfecha_egreso2;
											
											// --> 2020-03-16: Hasta aca
											//-- se hace push
											//console.log("push 5");
											//console.log(datoauxfinal2);
											$datosauxfinal[] = $datoauxfinal2;
											
											//-- se inicializan de nuevo las variables
											$datoauxfinal_clave2				  = $clave;
											$datoauxfinal_ndia2					  = 1 ;
											$datoauxfinal_fechacargo2			  = $fechacargo;
											$datoauxfinal_whora_ingreso2		  = $whora_ingreso;
											$datoauxfinal_whora_egreso2			  = $whora_egreso;
											$datoauxfinal_ccogra2				  = $ccogra;
											$datoauxfinal_wnprocedimiento2		  = $wnprocedimiento;
											$datoauxfinal_wnumerohab2			  = $wnumerohab;
											$datoauxfinal_wvalor2				  = $wvalor;
											$datoauxfinal_wreconocido2			  = $wreconocido ;
											$datoauxfinal_wprocedimiento2		  = $wprocedimiento;
											$datoauxfinal_wresponsable2			  = $wresponsable;
											$datoauxfinal_wnresponsable2		  = $wnresponsable;
											$datoauxfinal_wtarifa2				  = $wtarifa;
											$datoauxfinal_tipoEmpresa2			  = $tipoEmpresa;
											$datoauxfinal_nitEmpresa2			  = $nitEmpresa;
											$datoauxfinal_concepto_cargo2		  = $concepto_cargo;
											$datoauxfinal_wnconcepto2			  = $wnconcepto;
											$datoauxfinal_id_tope_afectado2		  = $id_tope_afectado;
											$datoauxfinal_wtercero2				  = $wtercero;
											$datoauxfinal_wtercero_nom2			  = $wnomtercero;
											$datoauxfinal_wtercero_unix2		  = $wtercero_unix;
											$datoauxfinal_wtercero_especialidad2  = $wtercero_especialidad;
											$datoauxfinal_wgraba_varios_terceros2 = $wgraba_varios_terceros;
											$datoauxfinal_wporter2				  = $wporter;
											$datoauxfinal_wparalelo2			  = $wparalelo;
											$datoauxfinal_wvaltarExce2			  = $wvaltarExce;
											$datoauxfinal_wfecha_ingreso2		  = $fechacargo;
											$datoauxfinal_wfecha_egreso2		  = $fechacargo;
										}
									}
								}
								else
								{
									if($j==1)
									{
										//console.log("1      claves distintas  Clave"+clave+ "auxclave"+auxclave ); 
										$datoauxfinal 							= array();
										$datoauxfinal['clave'] 					= $datoauxfinal_clave;
										$datoauxfinal['ndia'] 					= $datoauxfinal_ndia;
										$datoauxfinal['fechacargo'] 			= $datoauxfinal_fechacargo	;
										$datoauxfinal['whora_ingreso'] 			= $datoauxfinal_whora_ingreso;
										$datoauxfinal['whora_egreso'] 			= $datoauxfinal_whora_egreso;
										$datoauxfinal['ccogra']	 				= $datoauxfinal_ccogra	;
										$datoauxfinal['wnprocedimiento'] 		= $datoauxfinal_wnprocedimiento;
										$datoauxfinal['wnumerohab'] 			= $datoauxfinal_wnumerohab	;
										$datoauxfinal['wvalor'] 				= $datoauxfinal_wvalor;
										$datoauxfinal['wreconocido'] 			= $datoauxfinal_wreconocido;
										$datoauxfinal['wprocedimiento'] 		= $datoauxfinal_wprocedimiento;
										$datoauxfinal['wresponsable'] 			= $datoauxfinal_wresponsable;
										$datoauxfinal['wnresponsable'] 			= $datoauxfinal_wnresponsable;
										$datoauxfinal['wtarifa'] 				= $datoauxfinal_wtarifa;
										$datoauxfinal['tipoEmpresa']	 		= $datoauxfinal_tipoEmpresa;
										$datoauxfinal['nitEmpresa'] 			= $datoauxfinal_nitEmpresa;
										$datoauxfinal['concepto_cargo'] 		= $datoauxfinal_concepto_cargo;
										$datoauxfinal['wnconcepto']	 			= $datoauxfinal_wnconcepto;
										$datoauxfinal['id_tope_afectado'] 		= $datoauxfinal_id_tope_afectado;
										$datoauxfinal['wtercero'] 				= $datoauxfinal_wtercero;
										$datoauxfinal['wtercero_nombre'] 		= $datoauxfinal_wtercero_nom;
										$datoauxfinal['wtercero_unix']	 		= $datoauxfinal_wtercero_unix;
										$datoauxfinal['wtercero_especialidad'] 	= $datoauxfinal_wtercero_especialidad;
										$datoauxfinal['wgraba_varios_terceros'] = $datoauxfinal_wgraba_varios_terceros;
										$datoauxfinal['wporter'] 				= $datoauxfinal_wporter;
										$datoauxfinal['wparalelo'] 				= $datoauxfinal_wparalelo;
										$datoauxfinal['wvaltarExce'] 			= $datoauxfinal_wvaltarExce;
										$datoauxfinal['wfecha_ingreso'] 		= $datoauxfinal_wfecha_ingreso;
										$datoauxfinal['wfecha_egreso'] 			= $datoauxfinal_wfecha_egreso;
										
										//-- se hace push
										//console.log("push 4");
										// echo "push 4 \n";
										//console.log(datoauxfinal);
										$datosauxfinal[] = $datoauxfinal;
										
										//-- se inicializan de nuevo las variables
										$datoauxfinal_clave						= $clave;
										$datoauxfinal_ndia						= 1 ;
										$datoauxfinal_fechacargo				= $fechacargo;
										$datoauxfinal_whora_ingreso				= $whora_ingreso;
										$datoauxfinal_whora_egreso				= $whora_egreso;
										$datoauxfinal_ccogra					= $ccogra;
										$datoauxfinal_wnprocedimiento			= $wnprocedimiento;
										$datoauxfinal_wnumerohab				= $wnumerohab;
										$datoauxfinal_wvalor					= $wvalor;
										$datoauxfinal_wreconocido				= $wreconocido ;
										$datoauxfinal_wprocedimiento			= $wprocedimiento;
										$datoauxfinal_wresponsable				= $wresponsable;
										$datoauxfinal_wnresponsable				= $wnresponsable;
										$datoauxfinal_wtarifa					= $wtarifa;
										$datoauxfinal_tipoEmpresa				= $tipoEmpresa;
										$datoauxfinal_nitEmpresa				= $nitEmpresa;
										$datoauxfinal_concepto_cargo			= $concepto_cargo;
										$datoauxfinal_wnconcepto				= $wnconcepto;
										$datoauxfinal_id_tope_afectado			= $id_tope_afectado;
										$datoauxfinal_wtercero					= $wtercero;
										$datoauxfinal_wtercero_nom				= $wnomtercero;
										$datoauxfinal_wtercero_unix				= $wtercero_unix;
										$datoauxfinal_wtercero_especialidad		= $wtercero_especialidad;
										$datoauxfinal_wgraba_varios_terceros	= $wgraba_varios_terceros;
										$datoauxfinal_wporter					= $wporter;
										$datoauxfinal_wparalelo					= $wparalelo;
										$datoauxfinal_wvaltarExce				= $wvaltarExce;
										$datoauxfinal_wfecha_ingreso			= $fechacargo;
										$datoauxfinal_wfecha_egreso				= $fechacargo;
								
									}
									else if($j==2)
									{
										//console.log("2  claves distintas  Clave"+clave+ "auxclave"+auxclave ); 
										$datoauxfinal2 							 = array();
										$datoauxfinal2['clave'] 				 = $datoauxfinal_clave2;
										$datoauxfinal2['ndia'] 					 = $datoauxfinal_ndia2;
										$datoauxfinal2['fechacargo'] 			 = $datoauxfinal_fechacargo2	;
										$datoauxfinal2['whora_ingreso'] 		 = $datoauxfinal_whora_ingreso2;
										$datoauxfinal2['whora_egreso'] 			 = $datoauxfinal_whora_egreso2;
										$datoauxfinal2['ccogra']	 			 = $datoauxfinal_ccogra2	;
										$datoauxfinal2['wnprocedimiento'] 		 = $datoauxfinal_wnprocedimiento2;
										$datoauxfinal2['wnumerohab'] 			 = $datoauxfinal_wnumerohab2	;
										$datoauxfinal2['wvalor'] 				 = $datoauxfinal_wvalor2;
										$datoauxfinal2['wreconocido'] 			 = $datoauxfinal_wreconocido2;
										$datoauxfinal2['wprocedimiento'] 		 = $datoauxfinal_wprocedimiento2;
										$datoauxfinal2['wresponsable'] 			 = $datoauxfinal_wresponsable2;
										$datoauxfinal2['wnresponsable'] 		 = $datoauxfinal_wnresponsable2;
										$datoauxfinal2['wtarifa'] 				 = $datoauxfinal_wtarifa2;
										$datoauxfinal2['tipoEmpresa']	 		 = $datoauxfinal_tipoEmpresa2;
										$datoauxfinal2['nitEmpresa'] 			 = $datoauxfinal_nitEmpresa2;
										$datoauxfinal2['concepto_cargo'] 		 = $datoauxfinal_concepto_cargo2;
										$datoauxfinal2['wnconcepto']	 		 = $datoauxfinal_wnconcepto2;
										$datoauxfinal2['id_tope_afectado'] 		 = $datoauxfinal_id_tope_afectado2;
										$datoauxfinal2['wtercero'] 				 = $datoauxfinal_wtercero2;
										$datoauxfinal2['wtercero_nombre'] 		 = $datoauxfinal_wtercero_nom2;
										$datoauxfinal2['wtercero_unix'] 		 = $datoauxfinal_wtercero_unix2;
										$datoauxfinal2['wtercero_especialidad']  = $datoauxfinal_wtercero_especialidad2;
										$datoauxfinal2['wgraba_varios_terceros'] = $datoauxfinal_wgraba_varios_terceros2;
										$datoauxfinal2['wporter'] 				 = $datoauxfinal_wporter2;
										$datoauxfinal2['wparalelo'] 			 = $datoauxfinal_wparalelo2;
										$datoauxfinal2['wvaltarExce'] 			 = $datoauxfinal_wvaltarExce2;
										$datoauxfinal2['wfecha_ingreso'] 		 = $datoauxfinal_wfecha_ingreso2;
										$datoauxfinal2['wfecha_egreso'] 		 = $datoauxfinal_wfecha_egreso2;
										//alert(datoauxfinal_wreconocido2);
										//-- se hace push
										if ($datoauxfinal2['wreconocido'] > 0 )
										{
											if ($datoauxfinal2['ndia'] > 0 )
											{
												//console.log("push 3");
												//echo "push 3\n";
												//console.log(datoauxfinal2);
												$datosauxfinal[] = $datoauxfinal2;
											
											}
										}
										
										//-- se inicializan de nuevo las variables
										$datoauxfinal_clave2					= $clave;
										$datoauxfinal_ndia2						= 1 ;
										$datoauxfinal_fechacargo2				= $fechacargo;
										$datoauxfinal_whora_ingreso2			= $whora_ingreso;
										$datoauxfinal_whora_egreso2				= $whora_egreso;
										$datoauxfinal_ccogra2					= $ccogra;
										$datoauxfinal_wnprocedimiento2			= $wnprocedimiento;
										$datoauxfinal_wnumerohab2				= $wnumerohab;
										$datoauxfinal_wvalor2					= $wvalor;
										$datoauxfinal_wreconocido2				= $wreconocido ;
										$datoauxfinal_wprocedimiento2			= $wprocedimiento;
										$datoauxfinal_wresponsable2				= $wresponsable;
										$datoauxfinal_wnresponsable2			= $wnresponsable;
										$datoauxfinal_wtarifa2					= $wtarifa;
										$datoauxfinal_tipoEmpresa2				= $tipoEmpresa;
										$datoauxfinal_nitEmpresa2				= $nitEmpresa;
										$datoauxfinal_concepto_cargo2			= $concepto_cargo;
										$datoauxfinal_wnconcepto2				= $wnconcepto;
										$datoauxfinal_id_tope_afectado2			= $id_tope_afectado;
										$datoauxfinal_wtercero2					= $wtercero;
										$datoauxfinal_wtercero_nom2				= $wnomtercero;
										$datoauxfinal_wtercero_unix2			= $wtercero_unix;
										$datoauxfinal_wtercero_especialidad2	= $wtercero_especialidad;
										$datoauxfinal_wgraba_varios_terceros2	= $wgraba_varios_terceros;
										$datoauxfinal_wporter2					= $wporter;
										$datoauxfinal_wparalelo2				= $wparalelo;
										$datoauxfinal_wvaltarExce2				= $wvaltarExce;
										$datoauxfinal_wfecha_ingreso2			= $fechacargo;
										$datoauxfinal_wfecha_egreso2			= $fechacargo;
									}
								}
								
								if($j==1) {
									$diasauxiliar1++;
								} else {
									$diasauxiliar2++;
								}
								
								if($j==1)
								{
									if($diasauxiliar1 == $diasauxiliar)
									{
										$datoauxfinal 							= array();
										$datoauxfinal['clave'] 					= $datoauxfinal_clave;
										$datoauxfinal['ndia'] 					= $datoauxfinal_ndia;
										$datoauxfinal['fechacargo'] 			= $datoauxfinal_fechacargo	;
										$datoauxfinal['whora_ingreso'] 			= $datoauxfinal_whora_ingreso;
										$datoauxfinal['whora_egreso'] 			= $datoauxfinal_whora_egreso;
										$datoauxfinal['ccogra']	 				= $datoauxfinal_ccogra	;
										$datoauxfinal['wnprocedimiento'] 		= $datoauxfinal_wnprocedimiento;
										$datoauxfinal['wnumerohab'] 			= $datoauxfinal_wnumerohab	;
										$datoauxfinal['wvalor'] 				= $datoauxfinal_wvalor;
										$datoauxfinal['wreconocido'] 			= $datoauxfinal_wreconocido;
										$datoauxfinal['wprocedimiento'] 		= $datoauxfinal_wprocedimiento;
										$datoauxfinal['wresponsable'] 			= $datoauxfinal_wresponsable;
										$datoauxfinal['wnresponsable'] 			= $datoauxfinal_wnresponsable;
										$datoauxfinal['wtarifa'] 				= $datoauxfinal_wtarifa;
										$datoauxfinal['tipoEmpresa']	 		= $datoauxfinal_tipoEmpresa;
										$datoauxfinal['nitEmpresa'] 			= $datoauxfinal_nitEmpresa;
										$datoauxfinal['concepto_cargo'] 		= $datoauxfinal_concepto_cargo;
										$datoauxfinal['wnconcepto']	 			= $datoauxfinal_wnconcepto;
										$datoauxfinal['id_tope_afectado'] 		= $datoauxfinal_id_tope_afectado;
										$datoauxfinal['wtercero'] 				= $datoauxfinal_wtercero;
										$datoauxfinal['wtercero_nombre'] 		= $datoauxfinal_wtercero_nom;
										$datoauxfinal['wtercero_unix'] 			= $datoauxfinal_wtercero_unix;
										$datoauxfinal['wtercero_especialidad'] 	= $datoauxfinal_wtercero_especialidad;
										$datoauxfinal['wgraba_varios_terceros'] = $datoauxfinal_wgraba_varios_terceros;
										$datoauxfinal['wporter'] 				= $datoauxfinal_wporter;
										$datoauxfinal['wparalelo'] 				= $datoauxfinal_wparalelo;
										$datoauxfinal['wvaltarExce'] 			= $datoauxfinal_wvaltarExce;
										$datoauxfinal['wfecha_ingreso'] 		= $datoauxfinal_wfecha_ingreso;
										$datoauxfinal['wfecha_egreso'] 			= $datoauxfinal_wfecha_egreso;
										
										if ($datoauxfinal['wreconocido'] >0 )
										{
											if ($datoauxfinal['ndia'] >0 )
											{
												//console.log("push 2");
												//console.log(datoauxfinal);
												$datosauxfinal[] = $datoauxfinal;
											}
										}
									}
								}
								if ($j==2)
								{
									if($diasauxiliar2 == $diasauxiliar)
									{
										// console.log("Responsable 2 dias auxiliar es igual a diasauxiliar2");
										//alert("diasauxiliar2");
										$datoauxfinal2 							 = array();
										$datoauxfinal2['clave'] 				 = $datoauxfinal_clave2;
										$datoauxfinal2['ndia'] 					 = $datoauxfinal_ndia2;
										$datoauxfinal2['fechacargo'] 			 = $datoauxfinal_fechacargo2	;
										$datoauxfinal2['whora_ingreso'] 		 = $datoauxfinal_whora_ingreso2;
										$datoauxfinal2['whora_egreso'] 			 = $datoauxfinal_whora_egreso2;
										$datoauxfinal2['ccogra']	 			 = $datoauxfinal_ccogra2	;
										$datoauxfinal2['wnprocedimiento'] 		 = $datoauxfinal_wnprocedimiento2;
										$datoauxfinal2['wnumerohab'] 			 = $datoauxfinal_wnumerohab2	;
										$datoauxfinal2['wvalor'] 				 = $datoauxfinal_wvalor2;
										$datoauxfinal2['wreconocido'] 			 = $datoauxfinal_wreconocido2;
										$datoauxfinal2['wprocedimiento'] 		 = $datoauxfinal_wprocedimiento2;
										$datoauxfinal2['wresponsable'] 			 = $datoauxfinal_wresponsable2;
										$datoauxfinal2['wnresponsable'] 		 = $datoauxfinal_wnresponsable2;
										$datoauxfinal2['wtarifa'] 				 = $datoauxfinal_wtarifa2;
										$datoauxfinal2['tipoEmpresa']	 		 = $datoauxfinal_tipoEmpresa2;
										$datoauxfinal2['nitEmpresa'] 			 = $datoauxfinal_nitEmpresa2;
										$datoauxfinal2['concepto_cargo'] 		 = $datoauxfinal_concepto_cargo2;
										$datoauxfinal2['wnconcepto']	 		 = $datoauxfinal_wnconcepto2;
										$datoauxfinal2['id_tope_afectado'] 		 = $datoauxfinal_id_tope_afectado2;
										$datoauxfinal2['wtercero'] 				 = $datoauxfinal_wtercero2;
										$datoauxfinal2['wtercero_nombre'] 		 = $datoauxfinal_wtercero_nom2;
										$datoauxfinal2['wtercero_unix'] 		 = $datoauxfinal_wtercero_unix2;
										$datoauxfinal2['wtercero_especialidad']  = $datoauxfinal_wtercero_especialidad2;
										$datoauxfinal2['wgraba_varios_terceros'] = $datoauxfinal_wgraba_varios_terceros2;
										$datoauxfinal2['wporter'] 				 = $datoauxfinal_wporter2;
										$datoauxfinal2['wparalelo'] 			 = $datoauxfinal_wparalelo2;
										$datoauxfinal2['wvaltarExce'] 			 = $datoauxfinal_wvaltarExce2;
										$datoauxfinal2['wfecha_ingreso'] 		 = $datoauxfinal_wfecha_ingreso2;
										$datoauxfinal2['wfecha_egreso'] 		 = $datoauxfinal_wfecha_egreso2;
										//alert(datoauxfinal_wreconocido2);
										if ($datoauxfinal2['wreconocido'] >0 )
										{
											if ($datoauxfinal2['ndia'] >0 )
											{
												//console.log("push 1");
												//console.log(datoauxfinal2);
												$datosauxfinal[] = $datoauxfinal2;
											
											}
										}
										
										
									}
								}
								//alert ("j:"+j+"auxclave:"+auxclave+"igual a "+clave);
								$auxtercero = $datoaux['wtercero'];
							} 
							
						}	else {
							
							//console.log("no entro , Responsable "+j+"  valor reconocido = "+$('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor') +"clave"+clave+"ndia"+ndia);
							echo "no entro , Responsable $j  valor reconocido = ".$reconocido_clave->getAttributeNode('valor')->nodeValue." clave ".$clave." ndia".$ndia."<br><br>";
							//alert($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).attr('valor'));
							//alert($('#reconocido_clave'+clave+'_'+ndia+'_res'+j).length);
							
						}
						
						if($j==1)
						{
							//console.log("aumento clave"+auxclave);
							$auxclave = $clave;
							//alert("j:"+j+"--"+wvalor);
							
						}
						/*
						if(j==2)
						{
							//alert("j:"+j+"--"+wvalor);
						}
						*/
						
						//$wdatos = json_encode($datosaux);
					}
				}
			}
		}
	}
	
	/*
	for($i=0 ;$i < count($datosauxfinal);$i++) {
		if($datosauxfinal[$i]['ndia'] == 1 && $datosauxfinal[$i]['wfecha_ingreso'] == date('Y-m-d') &&  ($datosauxfinal[$i]['wfecha_ingreso'] == date('Y-m-d') || $datosauxfinal[$i]['wfecha_ingreso'] == $datosauxfinal[$i]['wfecha_egreso'] )) {
			unset($datosauxfinal[$i]);
		}
	}
	*/
	
	$_SESSION['user']='03150-1';
	for($tt=0;$tt<count($datosauxfinal);$tt++){
		$auxiliarvector = [];
		$auxiliarvector = $datosauxfinal[$tt];
		if(is_null($auxiliarvector['wvalor'])) //isNaN()
		{
			$auxiliarvector['wvalor'] = 0;
		}				
	}
	
	$devolver = false;
	//$vector_id_grabados = [];
	
	/* INICIALIZAMOS EL CURL PARA QUE REALICE LA PETICION POST PARA VALIDAR LOS CARGOS A GRABAR DE LA ESTANCIA DEL PACIENTE */
	$ch = curl_init();
	
	$data = array( 
		'consultaAjax'=> '',
		'wemp_pmla'	  => $wemp_pmla,
		'accion'	  => 'validarCargos',
		'whistoria'	  => $whis,
		'wing'		  => $wing,
		'wdatos'      => json_encode($datosaux),
		'crontab'			=> ''
	);
									
	$options = array(
		CURLOPT_URL 			=> $CURLOPT_URL,
		CURLOPT_HEADER 			=> false,
		CURLOPT_POSTFIELDS 		=> $data,
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_CUSTOMREQUEST 	=> 'POST',
	);
			
	$opts = curl_setopt_array($ch, $options);
	
	$validar = curl_exec($ch);
	curl_close($ch);
	
	$validar = substr($validar , 4);
	$response = json_decode($validar, true);
	
	$response['Accion']['queryTarifa'] = str_replace(' ', 'EsPaCiO', $response['Accion']['queryTarifa']);
	$response['Accion']['queryTarifa'] = str_replace('EsPaCiO', ' ', $response['Accion']['queryTarifa']);
	
	$array_error['validar'] = $response;	
	
	if($response['error'] == 1)
	{
		$wasunto = "Automatizacion Estancia - Historia (".$whis."-".$wing.") - error en validar cargo";
		$detalle1 = "La Historia ".$whis."-".$wing.", ".$response['mensaje'].", por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.";
		$detalle2 = $response['mensaje'];
					
		enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, $html);	
		
		$data_json = array( 
			'wemp_pmla'	  	 			=> $wemp_pmla,
			'whistoria'	  	 			=> $whis,
			'wing'		  	 			=> $wing,
			'wno1' 		 				=> $wno1,
			'wno2'  		 			=> $wno2,
			'wap1'  		 			=> $wap1,
			'wap2'		 	 			=> $wap2,
			'wdoc' 		 				=> $wdoc,
			'wprocedimiento' 			=> '',
			'wnprocedimiento'			=> '',
			'wcodemp'		 			=> '',
			'wnomemp'		 			=> '',
			'wnomcon'					=> '',
		);
		
		$data = array();
		$data['error'] = 1;
		$data['mensaje'] = "La Historia ".$whis."-".$wing.", tuvo un erro al validar cargos ".$response['mensaje']." , por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.";
		
		logTransaccion($conex, $wbasedato_cliame, '', json_encode($data_json), '',  json_encode($data), 'on', 'INSERT', 'estancia', '');
		
		return array( 'code' => 0, 'msj' => "La Historia ".$whis."-".$wing.", ".$response['mensaje'].", por lo tanto el proceso automatico de cargos de estancia no se realizo de manera exitosa.");
		
	} else {
		$ii = 0;
		$cuantos = sizeof($datosauxfinal);
		$traerfechaparcial = date('Y-m-d');
		
		$ch = curl_init(); // descomentar en pruebas 
		$data = array( 
			'consultaAjax'	=> '',
			'wemp_pmla'	  	=> $wemp_pmla,
			'accion'	  	=> 'borrar_estancia_unix',
			'whistoria'	  	=> $whis,
			'wing'		  	=> $wing,
			'wfechaegreso'	=> $ultimafecha,
			'whoraegreso'	=> $ultimahora,
			'crontab'		=> ''
		);
								
		$options = array(
			CURLOPT_URL 			=> $CURLOPT_URL,
			CURLOPT_HEADER 			=> false,
			CURLOPT_POSTFIELDS 		=> $data,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_CUSTOMREQUEST 	=> 'POST',
		);
		
		
		$opts = curl_setopt_array($ch, $options);
		$unix_update = curl_exec($ch);
		curl_close($ch);
		
		if ($unix_update != '') {
			$unix_update = str_replace("'", "", $unix_update);
			$unix_update = str_replace(" ", "EsPaCiO", $unix_update);
			$unix_update = str_replace("EsPaCiO", " ", $unix_update);
			$array_error['borrar_estancia_unix']= $unix_update;
		}
		
		$array_detalle_estancia = [];
			
		for($jj = 0; $jj < $cuantos; $jj++) {
			/**
			if($jj == $cuantos-1) {
				if($("#wfecparcial").length>0) {
					traerfechaparcial = $("#wfecparcial").val();
				} else {
					traerfechaparcial ='no';
				}
			} else {
				traerfechaparcial ='no';
			}
			*/
			$datoget = $datosauxfinal[$jj];
			
			if ($datoget['clave'] != "") {
				if(($datoget['wvalor']*1)!=0 && ($datoget['ndia'] * 1)!=0 ) {
					
					$ch = curl_init(); // descomentar en pruebas 
					$post_fields_grabar_pension = array( 
						'consultaAjax'	 			=> '',
						'crontab'					=> '',
						'wemp_pmla'	  	 			=> $wemp_pmla,
						'accion'	  	 			=> 'grabar_pension',
						'whistoria'	  	 			=> $whis,
						'wing'		  	 			=> $wing,
						'wno1' 		 				=> $wno1,
						'wno2'  		 			=> $wno2,
						'wap1'  		 			=> $wap1,
						'wap2'		 	 			=> $wap2,
						'wdoc' 		 				=> $wdoc,
						'wcodemp'		 			=> $datoget['wresponsable'],
						'wnomemp'		 			=> $datoget['wnresponsable'],
						'wser'		 	 			=> $wser,
						'wfecing'	 	 			=> $wfecing,
						'wcodcon' 	 	 			=> $datoget['concepto_cargo'],
						'wnomcon'   	 			=> $datoget['wnconcepto'],
						'wfeccar'		 			=> $datoget['fechacargo'],
						'wccogra'	 	 			=> $datoget['ccogra'],
						'wvalor'	 	 			=> $datoget['wvalor'],
						'wprocedimiento' 			=> $datoget['wprocedimiento'],
						'wnprocedimiento'			=> $datoget['wnprocedimiento'],
						'wtarifa'		 			=> $datoget['wtarifa'],
						'wcantidad'		 			=> $datoget['ndia'],
						'wvaltarExce'	 			=> $datoget['wvaltarExce'],
						'wvaltarReco'	 			=> $datoget['wreconocido'],
						'wfacturable'	 			=> 'S',
						'wtip_paciente'	 			=> $tipoPaciente,
						'wtipo_ingreso'	 			=> $tipoIngreso,
						'wtipoEmpresa'	 			=> $datoget['tipoEmpresa'],
						'wnitEmpresa'	 			=> $datoget['nitEmpresa'],
						'wcodter'		 			=> $datoget['wtercero'],
						'wnomter' 		 			=> $datoget['wtercero_nombre'],
						'wporter' 		 			=> $datoget['wporter'],
						'wcco'			 			=> '',
						'whora_cargo'	 			=> '',
						'wnomCajero'	 			=> '',
						'wcobraHonorarios'			=> '',
						'wespecialidad'	  			=> $datoget['wtercero_especialidad'],
						'wgraba_varios_terceros'	=> $datoget['wgraba_varios_terceros'] ,
						'wcodcedula'		 		=> '',
						'wparalelo'		 			=> $datoget['wparalelo'],
						'wid_grabado_ant'	 		=> $aux_id_grabado,
						'wrecexc'		 			=> 'R',
						'wnumerohab'	 			=> $datoget['wnumerohab'],
						'wid_tope_afectado'  		=> $datoget['id_tope_afectado'],
						'whora_ingreso'		 		=> $datoget['whora_ingreso'],
						'whora_egreso'		 		=> $datoget['whora_egreso'],
						'wfecha_ingreso'	 		=> $datoget['wfecha_ingreso'],
						'wfecha_egreso' 			=> $datoget['wfecha_egreso'],
						'wterunix' 					=> $datoget['wtercero_unix'],
						'wtraerfechaparcial' 		=> $traerfechaparcial
					);
							
					$options = array(
						CURLOPT_URL 			=> $CURLOPT_URL,
						CURLOPT_HEADER 			=> false,
						CURLOPT_POSTFIELDS 		=> $post_fields_grabar_pension,
						CURLOPT_RETURNTRANSFER  => true,
						CURLOPT_CUSTOMREQUEST 	=> 'POST',
					);
					
					$opts = curl_setopt_array($ch, $options);
					$data = curl_exec($ch);
					curl_close($ch);
					
					$post_fields_grabar_pension['estancias'] = $cuantos;
					
					$array_data = substr( $data , 5 );
					$response = json_decode( $array_data, true );
					
					$respuesta = trim(str_replace('EsPaCiO', ' ',str_replace(' ', 'EsPaCiO', $response['respuesta'])));
					$respuesta = str_replace('"', "'", $respuesta);
					
					$array_error['estancia'.$jj] = ['idcargo' => $response['idcargo'], 'respuesta' => $respuesta];
					$aux_id_grabado = $response['idcargo'];
					
					if($aux_id_grabado == '') {
						$devolver = true;
					}
					
					$array_detalle_estancia[] = [ 'idcargo' 		=> $response['idcargo'], 
												  'respuesta'		=> $respuesta, 
												  'responsable' 	=> $datoget['wresponsable'].' - '.$datoget['wnresponsable'],
												  'fecha_cargo'		=> $datoget['fechacargo'],
												  'cco'         	=> $datoget['ccogra'],
												  'valor'       	=> $datoget['wvalor'],
												  'num_habitacion'	=> $datoget['wnumerohab'],
												  'habitacion'  	=> $datoget['wprocedimiento'].' - '.$datoget['wnprocedimiento'],
												  'tarifa'			=> $datoget['wtarifa'],
												  'cantidad'		=> $datoget['ndia'],
												  'wvaltarExce'		=> $datoget['wvaltarExce'],
												  'wvaltarReco'		=> $datoget['wreconocido'],												  
											    ];
				
				}
			}
		}
		
		if($devolver == false) {
			//envio tipo de habitaciones, con su dia inicial y fin
			// construyo vector  para luego ir a buscar los cargos grabados en las tablas.
			
			$Cambiocargos = $dom->getElementById('Cambiocargos');
			if(!is_null($Cambiocargos)) {
				$Cambiocargos = $Cambiocargos->getAttributeNode('value')->nodeValue;
			} else {
				$Cambiocargos="";
			}
			
			$sel = $dom->getElementsByTagName("select"); // Selects de todas las habitaciones por las que el paciente ha pasado
			$datos_estancia = array();
			
			foreach ($sel as $select){
				
				$optionTags = $select->getElementsByTagName('option');
				$inicial = $select->getAttributeNode('diainicial')->nodeValue;
				$final = $select->getAttributeNode('diafinal')->nodeValue;
				
				foreach ($optionTags as $tag){
					if ($tag->hasAttribute("selected")) {
						$tipo = $tag->getAttributeNode('value')->nodeValue;	    
					}
				}
				
				$datos_estancia[] = array('tipo' => $tipo, 'inicial' => $inicial, 'final' => $final);
			}
			
			if( count($datos_estancia) > 0 ) {
				$ch = curl_init();
				$post_fields = array( 
					'consultaAjax'		=> '',
					'crontab'			=> '',
					'wemp_pmla'			=> $wemp_pmla,
					'accion'			=> 'ModificarCargos',
					'whistoria' 		=> $whis,
					'wing' 				=> $wing,
					'wdatos'			=> json_encode($datos_estancia),
					'wcambiocargos'		=> $Cambiocargos,
				);
											
				$options = array(
					CURLOPT_URL 			=> $CURLOPT_URL,
					CURLOPT_HEADER 			=> false,
					CURLOPT_POSTFIELDS 		=> $post_fields,
					CURLOPT_RETURNTRANSFER	=> true,
					CURLOPT_CUSTOMREQUEST 	=> 'POST',
				);
					
				$opts = curl_setopt_array($ch, $options);
				
				$html_mc = curl_exec($ch);
				curl_close($ch);
				
				$dom_mc = new DOMDocument();
				@$dom_mc->loadHTML($html_mc);
				
				$tdsModificarCargos = $dom_mc->getElementsByTagName('td');
				
				///
				$datos = array();
				$clave1 = 1;
				foreach ($tdsModificarCargos as $td) {
					if( $td->hasAttribute('class') && $td->getAttributeNode('class')->nodeValue == 'cambio' && $td->hasAttribute('matrix') && $td->hasAttribute('unix')) {
						
						$matrix = $td->getAttributeNode('matrix')->nodeValue;
						$unix = $td->getAttributeNode('unix')->nodeValue;
						
						$datos[] = array('clave' => $clave1, 'matrix' => $matrix, 'unix' => $unix);
						$clave1++;
					}
				}
				
				//Grabar Politicas_--------------------------
				if( count($datos) > 0 ) {
					$ch = curl_init();
					$post_fields = array( 
						'consultaAjax'		=> '',
						'wemp_pmla'			=> $wemp_pmla,
						'accion'			=> 'GrabarPoliticasCambio',
						'whis' 				=> $whis,
						'wing' 				=> $wing,
						'wdatos'			=> json_encode($datos),
						'crontab'			=> ''
					);
												
					$options = array(
						CURLOPT_URL 			=> $CURLOPT_URL,
						CURLOPT_HEADER 			=> false,
						CURLOPT_POSTFIELDS 		=> $post_fields,
						CURLOPT_RETURNTRANSFER	=> true,
						CURLOPT_CUSTOMREQUEST 	=> 'POST',
					);
						
					$opts = curl_setopt_array($ch, $options);
					
					$info = curl_exec($ch);
					curl_close($ch);
					
					$array_error['PoliticasCambio'] = $info;
				}
			}
		}

		if ($devolver == false) {
			
			$datos = array();
			$vector_saldos = $dom->getElementById('vector_saldos')->getAttributeNode('valor')->nodeValue;
			$ArrayValores = json_decode($vector_saldos, true);
			
			foreach( $ArrayValores as $CodVal) {
				$datos[$CodVal] = $ArrayValores[$CodVal];
			}
			
			$datosJson = json_encode($datos);
			
			$ch = curl_init();
			$post_fields = array( 
				'consultaAjax'		=> '',
				'wemp_pmla'			=> $wemp_pmla,
				'accion'			=> 'descongelar_y_grabarDetalle',
				'whistoria' 		=> $whis,
				'wing' 				=> $wing,
				'wvector_saldos'	=> $datosJson,
				'whtml'				=> str_replace("'", '"', $html),
				'crontab'			=> ''
			);
			
			$post_fields['whtml'] = str_replace("'", '"', $post_fields['whtml']);
			
			$options = array(
				CURLOPT_URL 			=> $CURLOPT_URL,
				CURLOPT_HEADER 			=> false,
				CURLOPT_POSTFIELDS 		=> $post_fields,
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_CUSTOMREQUEST 	=> 'POST',
			);
				
			$opts = curl_setopt_array($ch, $options);
			
			$info = curl_exec($ch);
			
			curl_close($ch);
			
			$post_fields['whtml'] = "";
			$array_error['descongelar_y_grabarDetalle'] = $post_fields;
			
		} else {
			
			$json_error = json_encode( $array_error, JSON_UNESCAPED_SLASHES);
			$post_fields_grabar_pension['detalle_estancias'] = $array_detalle_estancia;
			
			$texto_array_info = '';
					
			$num_array_info = sizeof($array_detalle_estancia);
			for($i = 0; $i < $num_array_info; $i++) {
				$texto_array_info .= 'Responsable: '.$array_detalle_estancia[$i]['responsable'].'<br>';
				$texto_array_info .= 'Tarifa: '.$array_detalle_estancia[$i]['tarifa'].'<br>';
				$texto_array_info .= 'Habitaci&oacute;n: '.$array_detalle_estancia[$i]['habitacion'].'<br>';
				$texto_array_info .= 'D&iacute;as: '.$array_detalle_estancia[$i]['cantidad'].'<br>';
			}
			
			$wasunto = "Automatizacion Estancia - Historia (".$whis."-".$wing.")";
			$detalle1 = "Historia ".$whis."-".$wing." - Estancias Consecutivo Rips";
			$detalle2 = 'La siguiente liquidaci&oacute;n de estancia no cuenta con consecutivo RIPS, por lo tanto no se realiza la liquidaci&oacute;n autom&aacute;tica de estancia. <br><br>'.$texto_array_info;
			
			enviarCorreo( $conex, $wemp_pmla, $wasunto, $detalle1, $detalle2, $html);
			logTransaccion($conex, $wbasedato_cliame, '', json_encode($post_fields_grabar_pension), '', $json_error, 'on', 'INSERT', 'estancia', $html);
			
			return [ 'code' => 0, 'msj' => "Error al grabar." ];
		}
		
		$json_error = json_encode( $array_error, JSON_UNESCAPED_SLASHES);
		$json_error = str_replace('\t','', $json_error);
		$json_error = str_replace('\r','', $json_error);
		$json_error = str_replace('\n','', $json_error);
		$json_error = str_replace('-','', $json_error);
		$json_error = preg_replace('/\\\t/', '',$json_error);
		$json_error = preg_replace('/(\s+)?\\\t(\s+)?/', '',$json_error);
		$json_error = preg_replace('/\\\"/',"\"", $json_error);
		
		$post_fields_grabar_pension['detalle_estancias'] = $array_detalle_estancia;
		
		logTransaccion($conex, $wbasedato_cliame, '', json_encode($post_fields_grabar_pension), '', $json_error, 'on', 'INSERT', 'estancia', $html);
		
		return [ 'code' => 1, 'msj' => "El proceso autom&aacute;tico de liquidaci&oacute;n de estancia, se realiz&oacute; exitosamente." ];
	}
}


//---------------------------------------------------------------------------------------
if(isset($_POST['accion'])) {
	$accion = $_POST['accion'];
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wbasedato_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	
	switch ($accion) {
		case 'traer_conceptos':
			$muevenInventario =  $_POST['muevenInventario'];
			echo json_encode(obtenerArrayInfoConceptos($conex, $wemp_pmla, $wbasedato, $muevenInventario));
		break;
		
		case 'ObtenerElTipoDeConcepto':
			$CodigoConcepto = $_POST['CodigoConcepto'];
			$Data = array();
			
			// --> Primero validar en las politicas que el concepto sea facturable
			if (ValidarSiEsFacturable($CodigoConcepto)) {
				$Data['Facturable'] = 'si';

				$Tipo_concepto = 
				"SELECT Grutip, Gruinv
					FROM ".$wbasedato."_000200
				 WHERE Grucod = '".$CodigoConcepto."' ";
				$res_Tipo_concepto = mysql_query($Tipo_concepto,$conex) or die("Error en el query: ".$Tipo_concepto."<br>Tipo Error:".mysql_error());
				if($row_Tipo_concepto = mysql_fetch_array($res_Tipo_concepto)) {
					$Data['Tipo'] = $row_Tipo_concepto['Grutip'];
				} else {
					$Data['Tipo'] = '';
				}
				
				$Data['Inv'] = $row_Tipo_concepto['Gruinv'];
			} else {
				$Data['Facturable'] = 'no';
				$Data['Tipo'] 		= '';
				$Data['Inv'] = '';
			}
			
			echo json_encode($Data);
			
			break;
			
		case 'datos_desde_concepto':
			
			$data = datos_desde_concepto($wcodcon, $wcodcco);	
			echo json_encode($data);
			
		break;
		case 'traer_detalle_del_concepto':
			
			
			$ConceptoInventar = 'off';
			$Array_proc	= Obtener_array_detalle_concepto($cod_concepto, $ConceptoInventar, $Tarifa);

			if($ConceptoInventar != 'on')
			{
				// --> Agregarle al array los procedimientos de la 70
				$Array_proc = obtener_array_procedimientosEmpresa2($conex, $wemp_pmla, $wbasedato, NULL, NULL, $Array_proc);
			}

			echo json_encode($Array_proc);
		break;
		case 'traer_formularios_hce': 
			
			$name_hce = $_POST['name_hce'];
			$data = obtenerArrayFormulariosHCE($conex, $wbasedato_hce, $name_hce);
			echo json_encode($data);
		break;
		case 'datos_desde_fhce': 
			
			$data = datos_desde_fhce($conex, $wbasedato_hce, $wcodfhce, $wconsecfhce);
			echo json_encode($data);
		break;
		case 'traer_insumos': 
			
			$name = $_POST['name_insumo'];
			$codcon = $_POST['codcon'];
			
			$data = obtenerArrayInsumos($conex, $wemp_pmla, $wbasedato_movhos, $name, $codcon);
			echo json_encode($data);
		break;
		case 'traer_procedimientos':
			
			$name_proc = $_POST['name_proc'];
			$codcon = $_POST['codcon'];
			$data = obtenerArrayProcedimientos($conex, $name_proc);
			echo json_encode($data);
		break;
		case 'listado': 
			$data = obtenerArrayListado($conex, $wbasedato, $wbasedato_movhos, $wbasedato_hce );
			echo json_encode($data);
		break;
		case 'eliminar': 
			
			$id_cargo = $_POST['id_cargo'];
			$wuse = $_POST['wuse'];
			
			$data = eliminarCargo($conex, $wbasedato, $wbasedato_movhos, $wbasedato_hce, $wuse, $id_cargo );
			echo json_encode($data);
		break;
		case 'guardar_config_cargo_automatico':
			
			$wuse = $_POST['wuse'];
			$concepto = $_POST['con'];
			$centro_costos = $_POST['cco'];
			$procedimiento_o_insumo = $_POST['procins'];
			$formulario_hce = $_POST['fhce'];
			$consecutivo_hce = $_POST['confhce'];
			$tipo_origen = $_POST['tc'];
			$tipo_cco = $_POST['tcco'];
			$poi = $_POST['poi'];			
			guardar_configuracion_cargo_automatico($conex, $wbasedato, $wbasedato_movhos, $wbasedato_hce, $wuse, null, $concepto, $centro_costos, $procedimiento_o_insumo, $formulario_hce, $consecutivo_hce, $tipo_origen, $poi, $tipo_cco);
		break;
		case 'edit_cargo_automatico':
			
			$wuse = $_POST['wuse'];
			$concepto = $_POST['con'];
			$centro_costos = $_POST['cco'];
			$procedimiento_o_insumo = $_POST['procins'];
			$formulario_hce = $_POST['fhce'];
			$consecutivo_hce = $_POST['confhce'];
			$tipo_origen = $_POST['tc'];
			$poi = $_POST['poi'];
			$tipo_cco = $_POST['tcco'];
			$id = $_POST['id'];
			
			guardar_configuracion_cargo_automatico($conex, $wbasedato, $wbasedato_movhos, $wbasedato_hce, $wuse, $id, $concepto, $centro_costos, $procedimiento_o_insumo, $formulario_hce, $consecutivo_hce, $tipo_origen, $poi, $tipo_cco);
		break;
		case 'guardar_config_cargo_automatico_hce':
			
			$wemp_pmla = $_POST['wemp_pmla'];
			$whis = $_POST['whis'];
			$wing = $_POST['wing'];
			$wformulario = $_POST['wformulario'];
			$movusu = $_POST['movusu'];
			$str_consecutivos_formulario = $_POST['str_consecutivos_formulario'];
			$str_consecutivos_formulario_todos = $_POST['str_consecutivos_formulario_todos'];
			$explode_consecutivos = explode('|', $str_consecutivos_formulario);
			
			$wuse = $movusu;
			
			$configCCA = obtenerDatosCCAxFormulario($conex, $wemp_pmla, $wformulario, $movusu, $whis, $wing);	
			$numCCA = sizeof($configCCA);			
			for($i = 0; $i < $numCCA; $i++) {
				if($configCCA[$i]['ccaeve'] == 'on' || ($configCCA[$i]['ccadat'] == 'on' && in_array($configCCA[$i]['Detcon'], explode("|", $str_consecutivos_formulario_todos)))) {					
					$configCCA[$i]["consecutivos_formulario"] = $str_consecutivos_formulario;
					guardarCargoAutomaticoFacturacionERP($conex, $wemp_pmla, $wuse, $whis, $wing, $configCCA[$i]);
				}
			}
		break;
		case 'guardar_cargo_automatico_preescripcion':
			
			$emp = $_POST['wemp_pmla'];
			$his = $_POST['his'];
			$ing = $_POST['ing'];
			$cco = $_POST['cco'];
			$medicamento = $_POST['codMed'];
			$cantidad = $_POST['cantidad'];
			$usuario = $_POST['usuario'];
			$wuse = $usuario;
			
			guardarCargoAutomaticoPreescripcion($conex, $wbasedato_movhos, $emp, $his, $ing, $cco, $medicamento, $cantidad, $usuario);
			
		break;
		case 'concepto_tipo': 
			$cod_concepto = $_POST['cod_concepto'];
			$data = detalle_concepto($conex, $cod_concepto,$wbasedato);
			echo json_encode($data);
		break;
		case 'obtener_logs': 
			$esCCA = $_POST['esCCA'];
			$fecha = $_POST['fecha'];
			
			$data = obtenerLogTransaccion($conex, $wbasedato, $esCCA, $fecha);
			
			echo json_encode($data);
		break;
		case 'obtener_logs_html': 
			$esCCA = $_POST['esCCA'];
			$fecha = $_POST['fecha'];
			
			echo obtenerLogTransaccionHTML($conex, $wbasedato, $esCCA, $fecha);
			
		break;
		case 'resumen_estancia_cron':
			
			$whis = $_POST['whis'];
			$wing = $_POST['wing'];
			$wemp_pmla = $_POST['wemp_pmla'];
			
			$wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			
			guardarCargoAutomaticoEstancia($conex, $wemp_pmla, $wbasedato_movhos, $wbasedato_cliame, $whis, $wing);
			
		break;
		case 'guardar_cargo_automatico_orden': 
			
			$wemp_pmla = $_POST['wemp_pmla'];
			$whis = $_POST['whis'];
			$wing = $_POST['wing'];
			$wprocedimiento = $_POST['wprocedimiento'];
			$worden = $_POST['worden'];
			$movusu = $_POST['movusu'];
			$wuse = $movusu;
			$configCCA = obtenerDatosCCAxOrden($conex, $wemp_pmla, $wprocedimiento, $movusu, $whis, $wing);
			
			
			$numCCA = sizeof($configCCA);
		
			for($i = 0; $i < $numCCA; $i++) {
				$configCCA[$i]['norden']=$worden;			
				guardarCargoAutomaticoFacturacionERP($conex, $wemp_pmla, $wuse, $whis, $wing, $configCCA[$i]);
			}
			//echo json_encode($data);
		break;
		case 'guardar_estancia_test':
			
			$whis = $_POST['whis'];
			$wing = $_POST['wing'];
			$wemp_pmla = $_POST['wemp_pmla'];
			
			if($whis == '') {
				echo json_encode(array( 'code' => 0, 'El campo Historia es requerido.'));
				return;
			}
			
			if($wing == '') {
				echo json_encode(array( 'code' => 0, 'El campo Ingreso es requerido.'));
				return;
			}
						
			$wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			
			$response = guardarCargoAutomaticoEstancia($conex, $wemp_pmla, $wbasedato_movhos, $wbasedato_cliame, $whis, $wing);
			
			echo json_encode($response);
		break;
	}
}
