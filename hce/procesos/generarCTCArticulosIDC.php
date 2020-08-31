<?php
include_once("conex.php");
/**
 * Modificaciones:
 * Enero 12 2016.		Edwin MG.		Se corrige variable efectosSecundariosNoPosProtocolo que se encontraba mal escrita al momento de cargar los datos por defecto de EFECTOS SECUNDARIOS de protocolos
 * Febrero 13 2014.		Edwin MG.		Para el caso clínico, se permite grabar correctamente caracteres especiales
 * Enero 21 de 2014.	Edwin MG.		Para la presentación se deja la forma farmaceutica y se deja siempre editar los campos de posología, cantidad, dosis/día y tiempo de tratamiento
 */

 function traer_informacionctcanterior($conex, $wemp_pmla, $wbasedato, $his, $ing, $cod_articulo){
	
	$sql = " SELECT * 
	           FROM {$wbasedato}_000134
			  WHERE	ctchis = '$his'
				AND ctcing = '$ing'
			    AND ctcart = '$cod_articulo'
		   ORDER BY id DESC LIMIT 1 ";		
	$res = mysql_query( $sql , $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );	
	
	$array_datosCTC = array();
	
	while($row = mysql_fetch_assoc($res)){
						
				if(!array_key_exists($cod_articulo, $array_datosCTC)){
									
					$array_datosCTC[$cod_articulo] = $row;					
				}
			}
			
	return $array_datosCTC;
	
	
}
 
 
 
 /***
  * Consulta las diferentes unidades de medida de posología
  */
function consultarUnidadesMedidaPosologiaArtIDC(){

	global $wbasedato;
	global $conex;

	$q = "SELECT
			Unicod, Unides
		FROM 
			".$wbasedato."_000163
		WHERE
			Uniest = 'on'
		ORDER BY Unicod
		;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$unidad = array();

		$unidad[ 'codigo' ] = $info['Unicod'];
		$unidad[ 'descripcion' ] = $info['Unides'];

		$cont1++;

		$coleccion[] = $unidad;
	}

	return $coleccion;
}
 
/************************************************************************
 * Consulta el diagnóstico del paciente segun
 ************************************************************************/
function consultarDiagnostico( $conex, $whce, $his, $ing ){
	
	$val = "";

	$sql = "SELECT ".$whce."_000051.movdat, ".$whce."_000051.Fecha_data, ".$whce."_000051.Hora_data
			  FROM ".$whce."_000051
			 WHERE ".$whce."_000051.movdat != ''
			   AND ".$whce."_000051.movdat != ' '
			   AND ".$whce."_000051.movcon = 182
			   AND movhis='".$his."'
			   AND moving='".$ing."'
			UNION ALL
			SELECT ".$whce."_000052.movdat, ".$whce."_000052.Fecha_data, ".$whce."_000052.Hora_data 
			  FROM ".$whce."_000052
			 WHERE ".$whce."_000052.movdat != ''
			   AND ".$whce."_000052.movdat != ' '
			   AND ".$whce."_000052.movcon = 141
			   AND movhis='".$his."'
			   AND moving='".$ing."'
			UNION ALL
			SELECT ".$whce."_000063.movdat, ".$whce."_000063.Fecha_data, ".$whce."_000063.Hora_data
			  FROM ".$whce."_000063
			 WHERE ".$whce."_000063.movdat != ''
			   AND ".$whce."_000063.movdat != ' '
			   AND ".$whce."_000063.movcon = 240
			   AND movhis='".$his."' 
			   AND moving='".$ing."'
			ORDER BY Fecha_data DESC, Hora_data DESC";
			
	$res = mysql_query($sql,$conex) or die (mysql_errno()." - ".mysql_error());
	$nummed = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
	}
	
	return $val;
}

/****************************************************************************************************
 * Calcula la edad del paciente de acuerdo a la fecha de nacimiento
 ****************************************************************************************************/
function calcularEdad( $fecNac ){

	$ann=(integer)substr($fecNac,0,4)*360 +(integer)substr($fecNac,5,2)*30 + (integer)substr($fecNac,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($aa - $ann)/360;
	$meses=(($aa - $ann) % 360)/30;
	if ($ann1<1){
		$dias1=(($aa - $ann) % 360) % 30;
		$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
	} else {
		$dias1=(($aa - $ann) % 360) % 30;
		//$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
		$wedad=(string)(integer)$ann1." a&ntilde;o(s) ";
	}
	
	return $wedad;
}

/**************************************************************************************************
 * Consulta cuantas veces se ha solicitado el articulo NO POS
 **************************************************************************************************/
function consultarTipoSolicitud( $conex, $wbasedato, $wemp_pmla, $historia, $ingreso, $art ){

	$val = false;

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000134 a
			WHERE
				ctchis = '$historia'
				AND ctcing = '$ingreso'
				AND ctcart = '$art'
				AND ctcest = 'on'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	$num++;

	return $num;
}

/************************************************************************************
 * Consulta la información básica del paciente
 ************************************************************************************/
function consultarInfoPaciente( $conex, $wbasedato, $wemp_pmla, $historia, $ingreso ){

	$val = false;

	$sql = "SELECT
				Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid
			FROM
				{$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g
			WHERE
				ccocod = ubisac
				AND orihis = ubihis
				AND oriing = ubiing
				AND oritid = pactid
				AND oriced = pacced
				AND oriori = '$wemp_pmla'
				AND ubihis = '$historia'
				AND ubiing = '$ingreso'
			ORDER BY
				ubisac, ubihac
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows;
	}

	return $val;
}


/****************************************************************************************************
 * Consulta el tipo de atención del usuario
 * Retorna 	1: Hospitalario
 *			2: Hospitalario urgente
 *			3: Ambulatorio
 ****************************************************************************************************/
function consultarTipoAtencion( $conex, $wbasedato, $historia, $ingreso ){

	$val = false;

	$sql = "SELECT 
				Ccohos, Ccourg
			FROM 
				{$wbasedato}_000018, {$wbasedato}_000011
			WHERE 
				ubihis = '$historia'
				AND ubiing = '$ingreso'
				AND ccocod = ubisac
			;";
				 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		if( $rows['Ccohos'] == 'on' ){
			$val = 1;
		}
		elseif( $rows['Ccourg'] == 'on' ){
			$val = 2;
		}
		else{
			$val = 3;
		}
	}
	
	return $val;
}

/************************************************************************************
 * Consulta la frecuencia en horas segun el codigo pasado por parametro
 *
 * Octubre 5 de 2011
 ************************************************************************************/
function consultarFrecuencia( $conex, $wbasedato, $codigo ){

	$val = false;

	$sql = "SELECT 
				*
			FROM 
				{$wbasedato}_000043
			WHERE 
				Percod = '{$codigo}'
			;";
				 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 'Perequ' ];
	}
	if($val==0)
		$val=1;
		
	return $val;
}

// Consulta la especialidad según el código del usuario
function consultarEspecialidadUsuario($usuario, $wbasedato="movhos"){

	global $wbasedato;
	global $conex;

	$q = "SELECT
			Espcod
		FROM 
			{$wbasedato}_000044, {$wbasedato}_000048
		WHERE 
			Meduma = '".$usuario."'
		AND Medest = 'on'
		AND Medesp = Espcod
		";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$especialidad = $row['Espcod'];
	}
	else
	{
		$especialidad = '';
	}
	return $especialidad;
}


/************************************************************************************
 * Consulta los campos que trae por defecto el protocolo para el medicamento
 *
 * Enero 31 de 2013
 *
 * $fnac	Fecha de nacimiento el paciente
 ************************************************************************************/
function consultarProtocolo( $conex, $wbasedato, $codigo, $fnac, $cco, $tratamiento, $wusuario ){

	global $wemp_pmla;

	$val = false;

	$sql = "SELECT 
				Dprete, Dprese, Dprtre, Dprbib
			FROM 
				{$wbasedato}_000138 a, {$wbasedato}_000137 b
			WHERE 
				Dprcod = '{$codigo}'
				AND Dprest = 'on'
				AND Dprpro = Procod
				AND Proest = 'on'
			;";
			
	//Busco si el paciente es pediatrico, es decir es considerado un niño
	$edadPediatria = consultarAliasPorAplicacionIncArts( $conex, $wemp_pmla, "edadPediatria" );
	
	//Resto la edad maxima para ser considerado un niño a la fecha actual
	$fechaMin = date( "Y-m-d", strtotime( (date("Y")-$edadPediatria).date( "-m-d" ) ) );
	
	$esPediatrico = 'off';
	if( $fnac >= $fechaMin ){
		$esPediatrico = 'on';
	}
			
	$sql = "SELECT 
				Dprete, Dprese, Dprtre, Dprbib, Deffru, Artgen, c.*
			FROM 
				{$wbasedato}_000138 a, 
				{$wbasedato}_000137 b, 
				{$wbasedato}_000152 c, 
				{$wbasedato}_000059 d, 
				{$wbasedato}_000026 e
			WHERE 
				Dprcod = '{$codigo}'
				AND Dprest = 'on'
				AND Dprpro = Procod
				AND Proped = '$esPediatrico'
				AND Proest = 'on'
				AND Relpro = Procod
				AND Relart = Defart
				AND Defest = 'on'
				AND Artcod = Defart 
				AND Artest = 'on'
			;";
			
	
	$especialidad = consultarEspecialidadUsuario($wusuario,$wbasedato);
	
	if(trim($wusuario)=='*' || trim($wusuario)=='')
		$wusuario = '%';
	
	if(trim($especialidad)=='*' || trim($especialidad)=='')
		$especialidad = '%';

	if(trim($tratamiento)=='*' || trim($tratamiento)=='')
		$tratamiento = '%';
	
	if(trim($cco)=='*' || trim($cco)=='')
		$cco = '%';
	
	$sql = "SELECT 
				Dprete, Dprese, Dprtre, Dprbib, Deffru, Artgen, c.*
			FROM 
				{$wbasedato}_000138 a, 
				{$wbasedato}_000137 b LEFT JOIN 
				{$wbasedato}_000152 c ON ( Relpro = Procod ) LEFT JOIN
				{$wbasedato}_000059 d ON ( Relart = Defart AND Defest = 'on' ) LEFT JOIN  
				{$wbasedato}_000026 e ON ( Artcod = Defart 
				                      AND Artest = 'on' )
			WHERE 
				Dprcod = '{$codigo}'
				AND Dprest = 'on'
				AND Dprpro = Procod
				AND Proped = '$esPediatrico'
				AND Proest = 'on'
				AND Dprpes = 'CtcMedicamentos'
			";
	
	$sql2 = $sql." AND Protra LIKE '".$tratamiento."' ";
	$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
	$num2 = mysql_num_rows( $res2 );
	if($num2 > 0)
	{
		$sql = $sql2;
	}
	
	$sql2 = $sql." AND Procco LIKE '".$cco."' AND Proesp LIKE '".$especialidad."'";
	$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
	$num2 = mysql_num_rows( $res2 );
	if($num2 > 0)
	{
		$sql = $sql2;
	}
	else
	{
		$sql2 = $sql." AND Procco LIKE '".$cco."'";
		$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
		$num2 = mysql_num_rows( $res2 );
		if($num2 > 0)
			$sql = $sql2;
		
		$sql2 = $sql." AND Proesp LIKE '".$especialidad."'";
		$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
		$num2 = mysql_num_rows( $res2 );
		if($num2 > 0)
			$sql = $sql2;
		
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows;
	}

	return $val;
}

function consultarFormasFarmaceuticas( $conex, $wbasedato, $cod ){

	$val = "";

	$q = "SELECT
			Ffanom 
		  FROM 
			  ".$wbasedato."_000046
		  WHERE
			  ffacod = '$cod'
			";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array( $res ) )
	{
		$val = $rows[ 'Ffanom' ];
	}

	return $val;
}

function consultarAliasPorAplicacionIncArts($conexion, $codigoInstitucion, $nombreAplicacion){
	
	$q = " SELECT 
				Detval  
			FROM 
				root_000051
			WHERE 
				Detemp = '".$codigoInstitucion."'
				AND Detapl = '".$nombreAplicacion."'";
	
//	echo $q;
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} else {
		terminarEjecucion("La institucion con el codigo :".$codigoInstitucion." no se encuentra, ni la aplicacion: ".$nombreAplicacion.".  Por favor verifique el valor de wemp_pmla");
	}
	return $alias;
}

/************************************************************************************************
 * Consulta el id original de un articulo en el kardex
 ************************************************************************************************/
function consultarIdo( $conex, $wbasedato, $historia, $ingreso, $articulo, $fechaKardex, $fin, $hin ){

	$val = false;
	
	//Busco todos los idos del kardex
	foreach( $fin as $keyFin => $valueFin ){
		
		$horIni = gmdate( "H:i:s", substr( $hin[ $keyFin ], 0, 2 )*3600 );
		
		$sql = "SELECT 
					* 
				FROM 
					{$wbasedato}_000054
				WHERE
					kadhis = '$historia'
					AND kading = '$ingreso'
					AND kadart = '$articulo'
					AND kadfec = '$fechaKardex'
					AND kadfin = '$valueFin'
					AND kadhin = '$horIni'
					AND kadest = 'on'
				UNION
				SELECT 
					* 
				FROM 
					{$wbasedato}_000060
				WHERE
					kadhis = '$historia'
					AND kading = '$ingreso'
					AND kadart = '$articulo'
					AND kadfec = '$fechaKardex'
					AND kadfin = '$valueFin'
					AND kadhin = '$horIni'
					AND kadest = 'on'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$val .= ",".$rows[ 'Kadido' ];
		}
	}
	
	if( !empty($val) ){
		$val = substr($val,1);
	}

	return $val;
	
	// $sql = "SELECT 
				// * 
			// FROM 
				// {$wbasedato}_000054
			// WHERE
				// kadhis = '$historia'
				// AND kading = '$ingreso'
				// AND kadart = '$articulo'
				// AND kadfec = '$fechaKardex'
				// AND kadfin = '$fin'
				// AND kadhin = '$hin'
				// AND kadest = 'on'
			// UNION
			// SELECT 
				// * 
			// FROM 
				// {$wbasedato}_000060
			// WHERE
				// kadhis = '$historia'
				// AND kading = '$ingreso'
				// AND kadart = '$articulo'
				// AND kadfec = '$fechaKardex'
				// AND kadfin = '$fin'
				// AND kadhin = '$hin'
				// AND kadest = 'on'
			// ";

	// $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	// if( $rows = mysql_fetch_array( $res ) ){
		// $val = $rows[ 'Kadido' ];
	// }
}

// INSERT INTO 'matrix'.'movhos_000132' ('Medico', 'Fecha_data', 'Hora_data', 'Ctrhis', 'Ctring', 'Ctrart', 'Ctrper', 'Ctrcan', 'Ctrido', 'Ctrmed', 'Ctrfge', 'Ctrhge', 'Ctrfim', 'Ctrhim', 'Ctruim', 'Ctrimp', 'Ctrest', 'Seguridad', 'id') 
							  // VALUES ('medico', '2012-10-12', '10:00:00', '34545', '345', '345', '344', 'sd', 'sdf', 'wer', '2012-10-12', '00:00:00', '2012-10-12', '00:00:00', 'asfs', 'asd', 'on', 'fghg', NULL);
/***************************************************************************************
 * inserta los datos a la tabla
 *
 * $datos	Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla 	Nombre de la tabla a lque se va a insertar los datos
 ***************************************************************************************/
function crearStringInsert( $tabla, $datos ){
	
	$stPartInsert = "";
	$stPartValues = "";
	
	foreach( $datos as $keyDatos => $valueDatos ){
			
		$stPartInsert .= ",$keyDatos";
		$stPartValues .= ",'$valueDatos'";
	}
	
	$stPartInsert = "INSERT INTO $tabla(".substr( $stPartInsert, 1 ).")";	//quito la coma inicial
	$stPartValues = " VALUES (".substr( $stPartValues, 1 ).")";
	
	return $stPartInsert.$stPartValues;
}

/**
 * Crea un calendario, similar a campoFechaDefecto de comun.php pero permite id diferente al nombre del campo
 */
function campoFechaDefectoCTC($nombreCampo, $idCampo, $fechaDefecto){
	echo "<INPUT TYPE='text' NAME='$nombreCampo' value='".$fechaDefecto."' size=11 readonly class='textoNormal'>";
	echo "&nbsp;<button id='btn$nombreCampo'>...</button>";
	funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'$idCampo',button:'btn$idCampo',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
}

function esProductoNoPOSCM( $conex, $wbasedato, $wcenmez, $producto ){

	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$wcenmez}_000003 a, {$wcenmez}_000009 b, {$wbasedato}_000026 c
			WHERE
				pdepro='$producto'
				AND pdeins = appcod
				AND pdeest = 'on'
				AND apppre = artcod
				AND artpos = 'N'
				AND artest = 'on' ";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}

/************************************************************
 * Dice si el medicamento en NO POS
 ************************************************************/
function esNoPOS( $conex, $wbasedato, $wcenmez, $art ){

	$val = false;

	$sql = "SELECT *
			FROM
				{$wbasedato}_000026 a, {$wbasedato}_000027 b
			WHERE
				artcod = '$art'
				AND artpos = 'N'
				AND artuni = unicod
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = mysql_fetch_array( $res );
		
		$val[ 'Artfar' ] = consultarFormasFarmaceuticas( $conex, $wbasedato, $val[ 'Artfar' ] );
	}
	else{
		$esNoPos = esProductoNoPOSCM( $conex, $wbasedato, $wcenmez, $art );
		
		if( $esNoPos ){
		
			$sql = "SELECT '00' as Artfar, 'NA' as Artreg, a.*, b.*
					FROM
						{$wcenmez}_000002 a, {$wbasedato}_000027 b
					WHERE
						artcod = '$art'
						AND artuni = unicod
					";
					
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );
			
			if( $num > 0 ){
				$val = mysql_fetch_array( $res );
				
				$val[ 'Artfar' ] = consultarFormasFarmaceuticas( $conex, $wbasedato, $val[ 'Artfar' ] );
			}
		}
	}
	
	return $val;
}

/****************************************************************************************************************
 * Busca si ya fue insertado un registro en la base de datos, y de ser así devuelve el registro completo
 ****************************************************************************************************************/
function buscarSiExisteRegistro( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $fecha, $estado = 'on' ){

	$val = false;

	$sql = "SELECT 
				* 
			FROM 
				{$wbasedato}_000134
			WHERE
				Ctchis = '$historia'
				AND Ctcing = '$ingreso'
				AND Ctcart = '$articulo'
				AND Ctcest = '$estado'
				AND Ctcfkx = '$fecha'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	
	return $val;
}

/********************************************************************************************************
 * Esta función recorre todos los articulos de control para un paciente y si es de control
 ********************************************************************************************************/
function generarArticulosAImprimir( $conex, $wbasedato, $historia, $ingreso, $fechaKardex, $codMedico, $datos ){
	
	if( true ){
		
		//Debo buscar si ya fue generado el articulo
		$existe = buscarSiExisteRegistro( $conex, $wbasedato, $historia, $ingreso, $datos[ 'Kadart' ], $datos[ 'Ctcido' ], $datos[ 'Ctcfkx' ], 'on' );
		
		if( true ){
		
			if( empty( $existe ) ){ //Si no existe
				
				$sql = crearStringInsert( "{$wbasedato}_000134", $datos );
				
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			}
			else{
				
				// //Si existe verifico si ha cambiado en algo la cantidad a pedir o la frecuencia
				// //si uno de los ha cambiado hay que desactivar el registro y crear uno nuevo con las
				// //cantidades correctas
				// if( $rowsArticulos[ 'Kadper' ] != $existe[ 'Ctrper' ] || $cantidad !=  $existe[ 'Ctrcan' ] ){
					
					// //desactivo el registro
					// $desactivado = cambiarEstadoRegistro( $conex, $wbasedato, $historia, $ingreso, $rowsArticulos[ 'Kadart' ], $rowsArticulos[ 'Kadido' ], $existe[ 'Ctrmed' ], 'off' );
					
					// if( $desactivado ){
					
						// //Inserto el registro
						// insertarArticulos( $conex, $wbasedato, $historia, $ingreso, $rowsArticulos[ 'Kadart' ], $rowsArticulos[ 'Kadper' ], $cantidad, $rowsArticulos[ 'Kadido' ], $codMedico );
					// }
				// }
			}
		}
	}
	
	return $val;
}



/************************************************************************************************************************
 * 											INICIO DEL PROGRAMA
 ************************************************************************************************************************/
// include_once( "root/comun.php" );
include_once( "conex.php" );



$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));
  
$wuser1=explode("-",$user);
$wusuario=trim($wuser1[1]);

$wbasedato = consultarAliasPorAplicacionIncArts( $conex, $wemp_pmla, "movhos" );
$wcenmez = consultarAliasPorAplicacionIncArts( $conex, $wemp_pmla, "cenmez" );

if( $consultaAjax ){	//si hay ajax
	
	$datos[ 'Medico' ] = "$wbasedato";
	$datos[ 'Fecha_data' ] = date( "Y-m-d" );
	$datos[ 'Hora_data' ] = date( "H:i:s" );
	$datos[ 'Seguridad' ] = "C-$wbasedato";
	
	
	
	$datos[ 'Ctchis' ] = $historia;
	$datos[ 'Ctcing' ] = $ingreso;
	$datos[ 'Ctcart' ] = $articulo;
	$datos[ 'Ctcfkx' ] = date( "Y-m-d" );
	//$datos[ 'Ctcido' ] = consultarIdo( $conex, $wbasedato, $historia, $ingreso, $articulo, $fechaKardex, $fin, gmdate( "H:i:s", substr( $hin, 0, 2 )*3600 ) );
	$datos[ 'Ctcido' ] = consultarIdo( $conex, $wbasedato, $historia, $ingreso, $articulo, $fechaKardex, $fin, $hin );
	$datos[ 'Ctcfso' ] = date( "Y-m-d" );
	
	$tipoSolicitud = consultarTipoSolicitud( $conex, $wbasedato, $wemp_pmla, $historia, $ingreso, $articulo );
	$datos[ 'Ctctso' ] = "Nro. ".$tipoSolicitud;
	
	
	$datos[ 'Ctcnoa' ] = utf8_decode( $noAfiliacion );
	$datos[ 'Ctctus' ] = utf8_decode( $tipoUsuario );
	$datos[ 'Ctctat' ] = utf8_decode( $tipoAtencion );
	$datos[ 'Ctcear' ] = utf8_decode( $enfermedadAltoRiesgo );
	$datos[ 'Ctcdcc' ] = @utf8_decode( $descripcionCasoClinico );
	$datos[ 'Ctcrcp' ] = utf8_decode( $rpClinicaPos );
	$datos[ 'Ctcorp' ] = utf8_decode( $observacionesRpClinicaPos );
	$datos[ 'Ctcerp' ] = utf8_decode( $existeRiesgoPos );
	$datos[ 'Ctcoer' ] = utf8_decode( $observacionesRiesgoPos );
	$datos[ 'Ctcedt' ] = utf8_decode( $efectoTerapeuticoNoPos );
	$datos[ 'Ctctre' ] = utf8_decode( $tiempoRespuestaEsperado );
	$datos[ 'Ctcert' ] = utf8_decode( $efectosSecundariosNoPos );
	$datos[ 'Ctcgte' ] = utf8_decode( $grupoTerapeuticoReemplazo );
	$datos[ 'Ctcpar' ] = utf8_decode( $principioActivoReemplazo );
	$datos[ 'Ctcprr' ] = utf8_decode( $presentacionReemplazo );
	$datos[ 'Ctcddr' ] = utf8_decode( $ddReemplazo );
	$datos[ 'Ctcttr' ] = utf8_decode( $tiempoTratamientoReemplazo );
	$datos[ 'Ctcbbo' ] = utf8_decode( $bibliografia );
	$datos[ 'Ctcpap' ] = utf8_decode( $principioActivoPos );
	$datos[ 'Ctcpop' ] = utf8_decode( $posologiaPos );
	$datos[ 'Ctcprp' ] = utf8_decode( $presentacionPos );
	$datos[ 'Ctcddp' ] = utf8_decode( $ddPos );
	$datos[ 'Ctccap' ] = utf8_decode( $cantidadPos );
	$datos[ 'Ctcttp' ] = utf8_decode( $tiempoTratamientoPos );
	$datos[ 'Ctcpaa' ] = utf8_decode( $principioActivoAlternativo );
	$datos[ 'Ctcpoa' ] = utf8_decode( $posologiaAlternativa );
	$datos[ 'Ctcpra' ] = utf8_decode( $presentacionAlternativa );
	$datos[ 'Ctcdda' ] = utf8_decode( $ddAlternativa );
	$datos[ 'Ctccaa' ] = utf8_decode( $cantidadAlternativa );
	$datos[ 'Ctctta' ] = utf8_decode( $tiempoTratamientoAlternativa );
	$datos[ 'Ctcpan' ] = utf8_decode( $principioActivoNoPos );
	$datos[ 'Ctcpon' ] = utf8_decode( $posologiaNoPos );
	$datos[ 'Ctcprn' ] = utf8_decode( $presentacionNoPos );
	$datos[ 'Ctcddn' ] = utf8_decode( $ddNoPos );
	$datos[ 'Ctcttn' ] = utf8_decode( $tiempoTratamientoNoPos );
	$datos[ 'Ctccan' ] = utf8_decode( $cantidadNoPos );
	$datos[ 'Ctccfn' ] = utf8_decode( $categoriaFarmaceuticaNoPos );
	$datos[ 'Ctcrin' ] = utf8_decode( $registroInvimaNoPos );
	$datos[ 'Ctcmed' ] = utf8_decode( $wusuario );
	$datos[ 'Ctcdxc' ] = utf8_decode( $txtDxCIE10 );
	$datos[ 'Ctcfge' ] = date( "Y-m-d" );
	$datos[ 'Ctchge' ] = date( "H:i:s" );
	$datos[ 'Ctcuim' ] = '';
	$datos[ 'Ctcfim' ] = '0000-00-00';
	$datos[ 'Ctchim' ] = '00:00:00';
	$datos[ 'Ctcimp' ] = 'off';
	$datos[ 'Ctcest' ] = 'on';

	switch( $consultaAjax ){
		
		case '10':
			generarArticulosAImprimir( $conex, $wbasedato, $historia, $ingreso, $fechaKardex, $wusuario, $datos );
			break;
			
		default: break;
	}
}
else
{	//si no hay ajax
?>
	<!-- <script type="text/javascript" src="./generarCTCOrdenes.js"></script> -->
	<!-- <script type='text/javascript' src='HCE.js' ></script>		<!-- HCE -->
	
	<style>
		td{
			font-size:10pt;
			height: 30px;
		}
	</style>
<?php
	//include_once( "root/comun.php" );
	include_once( "root/montoescrito.php" );

	echo "<div style='width:90%'>";
	
	//Busco los tipos de empresa que son EPS
	$tiposEmpresa = consultarAliasPorAplicacionIncArts( $conex, $wemp_pmla, "tiposEmpresasEps" );
	
	//creo un IN para la consulta
	$list = explode( "-", $tiposEmpresa );
	
	$inEPS = '';
	
	foreach( $list as $key => $value ){
		$inEPS .= ",'$value'";
	}
	
	$inEPS = "IN( ".substr( $inEPS, 1 )." ) ";
			
	$sql = "SELECT 
				'$codArticulo' as Kadart, b.*
			FROM
				{$wbasedato}_000016 b
			WHERE
				inghis = '$historia'
				AND inging = '$ingreso'
				AND ingtip $inEPS
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	//Consulto la cantidad de fraccion del articulo
	$qf = "SELECT
				Defart, Deffra, Defdup, Defdis, Defcco, Defdie, Deffru, Ccocod
			FROM
				{$wbasedato}_000059, {$wbasedato}_000011
			WHERE
				Defart = '$codArticulo'
				AND Defcco = ccocod
				AND Ccoest = 'on'
				AND Defest = 'on'
			";

	$respin = mysql_query($qf, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qf . " - " . mysql_error());
	$numf = mysql_num_rows($respin);
	
	//Consulto la información básica de un paciente
	$infoPaciente = consultarInfoPaciente( $conex, $wbasedato, $wemp_pmla, $historia, $ingreso );
	
	if(!isset($tratamiento) || !$tratamiento)
		$tratamiento = '%';

	$datosProtocolo = consultarProtocolo( $conex, $wbasedato, $codArticulo, $infoPaciente[ 'Pacnac' ], $infoPaciente[ 'Ubisac' ], $tratamiento, $wusuario );
	$efectoTerapeuticoNoPosProtocolo = "";
	$efectosSecundariosNoPosProtocolo = "";
	if($datosProtocolo['Dprete'] || $datosProtocolo['Dprese'])
	{
		$efectoTerapeuticoNoPosProtocolo = utf8_encode( $datosProtocolo['Dprete'] );
		$efectosSecundariosNoPosProtocolo = utf8_encode( $datosProtocolo['Dprese'] );
		$tiempoRespuestaEsperadoProtocolo = utf8_encode( $datosProtocolo['Dprtre'] );
		$bibliografiaProtocolo = utf8_encode( $datosProtocolo['Dprbib'] );
		
		$principioActivoPosProtocolo = "";
		$posologiaPosProtocolo = "";
		$presentacionPosProtocolo = "";
		$dosisDiaPosProtocolo = "";
		$cantidadPosProtocolo = "";
		$tiempoTratamientoPosProtocolo = "";
		
		$principioActivoPosReemplazoProtocolo = utf8_encode( $datosProtocolo['Artgen'] );
		$posologiaPosReemplazoProtocolo = utf8_encode( $datosProtocolo['Relpos'] );
		$presentacionPosReemplazoProtocolo = utf8_encode( $datosProtocolo['Deffru'] );
		$dosisDiaPosReemplazoProtocolo = utf8_encode( $datosProtocolo['Relddi'] );
		$cantidadPosReemplazoProtocolo = utf8_encode( $datosProtocolo['Relcan'] );
		$tiempoTratamientoPosReemplazoProtocolo = utf8_encode( $datosProtocolo['Reltto'] );
	}
	
	
	$canManejo = 1;
	$unidadFraccion = '';
	if( $rowsFra = mysql_fetch_array( $respin ) ){
		$canManejo = $rowsFra[ 'Deffra' ];
		$unidadFraccion = $rowsFra[ 'Deffru' ];
	}
	
	$mostroRegistros = false;
	
	$whce = consultarAliasPorAplicacionIncArts( $conex, $wemp_pmla, "hce" );
	$dxCie10 = strip_tags( trim( consultarDiagnostico( $conex, $whce, $historia, $ingreso ) ) );
	
	if( $num > 0 ){
		
		//$infoPaciente = consultarInfoPaciente( $conex, $wbasedato, $wemp_pmla, $historia, $ingreso );
		$traer_informacionctcanterior = traer_informacionctcanterior( $conex, $wemp_pmla, $wbasedato, $historia, $ingreso, $codArticulo);
		
		echo "<table width='100%' align='center'>";
		
		echo "<tr class='encabezadotabla'>";
		echo "<td style='font-size: 12pt' align='center'>";
		echo " JUSTIFICACION DE MEDICAMENTOS NO INCLUIDOS EN EL PLAN OBLIGATORIO DE SALUD ";
		echo "</td>";		
		echo "</tr>";
		
		echo "<tr class='fila1'>";
		echo "<td style='font-size: 10pt'>";
		echo "<b>".trim( strtoupper( $infoPaciente[ 'Pacap1' ]." ".$infoPaciente[ 'Pacap2' ] ) ).", ".trim( strtoupper( substr( $infoPaciente[ 'Pacno1' ], 0, 1 ) ).strtolower( substr( $infoPaciente[ 'Pacno1' ], 1 ) )." ".strtoupper( substr( $infoPaciente[ 'Pacno2' ], 0, 1 ) ).strtolower( substr( $infoPaciente[ 'Pacno2' ], 1 ) ) )."</b>";
		echo " con <b>".$infoPaciente[ 'Pactid' ]." ".$infoPaciente[ 'Pacced' ]."</b>";
		echo " ubicado en <b>".$infoPaciente[ 'Cconom' ]." - ".$infoPaciente[ 'Ubihac' ]."</b>.";
		echo " Historia <b>".$historia."-".$ingreso."</b>.";
		echo " Edad <b>".calcularEdad( $infoPaciente[ 'Pacnac' ] )."</b>";
		echo "</td>";		
		echo "</tr>";
		
		echo "</table>";
		echo "<br><br>";
	
		for( $i = 0, $j = 0; $listaArticulos = mysql_fetch_array( $res ); $i++ ){
		
			$existe = buscarSiExisteRegistro( $conex, $wbasedato, $historia, $ingreso, $listaArticulos[ 'Kadart' ], $listaArticulos[ 'Kadido' ], $listaArticulos[ 'Kadfec' ], 'on' );
		
			if( !$existe && !isset( $artProcesados[ $listaArticulos[ 'Kadart' ] ] ) ){
			
				$artProcesados[ $listaArticulos[ 'Kadart' ] ] = 1;
			
				$esNoPos = esNoPOS( $conex, $wbasedato, $wcenmez, $listaArticulos[ 'Kadart' ] );
		
				if( $esNoPos ){
				
					$mostroRegistros = true;
					
					/****************************************************************************************************************
					 * Marzo 6 de 2013
					 *
					 * Consulto el tiempo de tratamiento del medicamento
					 * Para esto todos los medicamentos deben tener al menos días de tratamiento o dosis máxima
					 ****************************************************************************************************************/
					
					//Si hay dias de tratamiento lo convierto a dosis maxima
					if(isset($diasTto))
					{
						foreach( $diasTto as $keyDiasTto => $valueDiasTto ){
							if( !empty($valueDiasTto) ){
								$dosisMaxima[$keyDiasTto] = floor( $valueDiasTto*24/consultarFrecuencia( $conex, $wbasedato, $frecuencia[$keyDiasTto] ) );
							}
						}
					}
					
					$can = 0;
					
					if(isset($cantidad))
					{
						foreach( $cantidad as $keyCantidad => $valueCantidad ){
							$can += $valueCantidad;
						}
					}
					
					$dosisPorDia = 0;
					if( !empty( $dosis ) && count($dosis) > 0 ){
						  
						foreach( $dosis as $keyDosis => $valueDosis ){
							$dosisPorDia += $valueDosis;
						}
					}
					
					//Calculo el tiempo de tratamiento
					if( empty($tiempoTratamiento) ){
						$tiempoTratamiento = 0;
						if( $dosisPorDia > 0 ){
							$tiempoTratamiento = ceil( $can*$canManejo/$dosisPorDia );
						}
					}
					/****************************************************************************************************************/
					
					$hospitalario = '';
					$ambulatorio = '';
					$hospitalarioUrgente = '';
					
					$tipoAtencion = consultarTipoAtencion( $conex, $wbasedato, $historia, $ingreso );
					// $tipoAtencion = 3;
					
					switch( $tipoAtencion ){
						
						case 1:
							$hospitalario = 'checked';
							$ambulatorio = 'disabled';
							$hospitalarioUrgente = 'disabled';
							$tipoAtencion = "HOSPITALARIO";
							break;
						
						case 2:
							$hospitalarioUrgente = 'checked';
							$hospitalario = 'disabled';
							$ambulatorio = 'disabled';
							$tipoAtencion = "HOSPITALARIOURGENTE";
							break;
							
						case 3:
							$ambulatorio = 'checked';
							$hospitalario = 'disabled';
							$hospitalarioUrgente = 'disabled';
							$tipoAtencion = "AMBULATORIO";
							break;
					}
					
					$j++;
					
					@$ArtsNoPos .= "-".$listaArticulos[ 'Kadart' ];
					
					echo "<INPUT type='hidden' id='{$esNoPos[ 'Artcod' ]}-{$idx}' value='{$esNoPos[ 'Artgen' ]}'>";
					
					$class="class='fila".($j%2+1)."'";
				
					echo "<div id='dv{$listaArticulos[ 'Kadart' ]}-{$idx}' style='display:none'>";
					echo "<table style='width:100%'>";
					echo "<tr $class>";
					echo "<td onClick='mostrarCtcArts(\"dv{$listaArticulos[ 'Kadart' ]}-{$idx}Mostrar\")' style='cursor:hand;cursor:pointer;height:45;font-size:10pt;'>";
					echo $esNoPos[ 'Artcod' ]." - ".$esNoPos[ 'Artgen' ];
					echo "<br><b>Nombre comercial: {$esNoPos[ 'Artgen' ]}</b>";
					echo "</tr></td>";
					echo "</table>";
					echo "</div>";
					
					echo "<div id='dv{$listaArticulos[ 'Kadart' ]}-{$idx}Mostrar'>";
					
					$tipoSolicitud = consultarTipoSolicitud( $conex, $wbasedato, $wemp_pmla, $historia, $ingreso, $listaArticulos[ 'Kadart' ] );
					$tipoSolicitud = "Nro. ".$tipoSolicitud;
					
					?>
						<div id='dvInfoPacienteCTC'>
						
						<table width="829" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse" align="center">
						<tr class="encabezadotabla">
							<td colspan="3" align="center">
								<B>TIPO DE ATENCION</B>
							</td>
						</tr>
						<tr>
						  <td>Hospitalario
							<input type="radio" value="HOSPITALARIO" onClick="seleccionUnicaRadioPorFila( this, 'tipoAtencion' )" <?php echo $hospitalario;?>/>
						  </td>
						  <td>Ambultario
							<input type="radio" value="AMBULATORIO" onClick="seleccionUnicaRadioPorFila( this, 'tipoAtencion' )" <?php echo $ambulatorio;?>/>
						  </td>
						  <td>Hospitalario Urgente
							<input type="radio" value="HOSPITALARIOURGENTE" onClick="seleccionUnicaRadioPorFila( this, 'tipoAtencion' )" <?php echo $hospitalarioUrgente;?>/>
							<input type="hidden" name="tipoAtencion" id="tipoAtencion" value="<?php echo $tipoAtencion;?>"/>
						  </td>
						</tr>
						<tr>
						  <td width="270">Tipo de usuario:
							<?php 
							if( trim( $listaArticulos[ 'Ingnre' ] ) != '' ){
								echo "<b>".trim( $listaArticulos[ 'Ingnre' ] )."</b>";
								?>
								<input type="hidden" name="tipoUsuario" id="tipoUsuario" style="" value="<?php echo $listaArticulos[ 'Ingnre' ];?>"/>
								<?php
							}
							else{
								?>
									<input type="text" name="tipoUsuario" id="tipoUsuario" style="" value="<?php echo $listaArticulos[ 'Ingnre' ];?>"/>
								<?php
							}
							?>
							
						  </td>
						  <td width="270">No. Afiliaci&oacute;n:
							<?php
							
							if( trim( $infoPaciente[ 'Pacced' ] ) != '' ){
								echo "<b>".$infoPaciente[ 'Pacced' ]."</b>";
								?>
								<input type="hidden" name="noAfiliacion" id="noAfiliacion" value="<?php echo $infoPaciente[ 'Pacced' ];?>"/>
								<?php
							}
							else{
								?>
								<input type="text" name="noAfiliacion" id="noAfiliacion"  style="" value="<?php echo $infoPaciente[ 'Pacced' ];?>"/>
								<?php
							}
							?>
							
						  </td>
						  <td width="281">Tipo Solicitud:
							<?php
							if( trim( $tipoSolicitud ) != '' ){
								echo "<b>".$tipoSolicitud."</b>";
								?>
								<input type="hidden" name="tipoSolicitud" id="tipoSolicitud" value="<?php echo $tipoSolicitud;?>"/>
								<?php
							}
							else{
								?>
								<input type="text" name="tipoSolicitud" id="tipoSolicitud"  style="" value="<?php echo $tipoSolicitud;?>"/>
								<?php
							}
							?>
						  </td>
						</tr>
					  </table>
					  
					  <p>Enfermedad de alto costo
						<label>
						<input type="checkbox" name="enfermedadAltoRiesgo" id="enfermedadAltoRiesgo"  style="" checked/>
						</label>
					  </p>
					  
					  <table width="829" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse" align="center">
					    <tr>
							<td align="center" style="width:250">DIAGNOSTICO</td>
							<td><INPUT type="text" id="txtDxCIE10" name="txtDxCIE10" style="width:572" value="<?php echo $dxCie10; ?>"></td>							
						</tr>
					  </table>
					  
					  <table width="829" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse" align="center">
					    <tr>
					      <td align="center" class='encabezadotabla'>DESCRIPCION DEL CASO CLINICO
					      </td>
						</tr>
						<tr>
					      <td>
						   <?php
							
								if($traer_informacionctcanterior[$codArticulo]['Ctcdcc'] != ''){									
									echo '<textarea name="descripcionCasoClinico" id="descripcionCasoClinico" cols="135" rows="3" style="">'.utf8_encode($traer_informacionctcanterior[$codArticulo]['Ctcdcc']).'</textarea>';
								}else{
									echo '<textarea name="descripcionCasoClinico" id="descripcionCasoClinico" cols="135" rows="3" style=""></textarea>';							
								}
								
							?> 	
							<div style="font-size:10px">Traer resumen historia cl&iacute;nica <input type="checkbox" name="chkJust" id="chkJust" style="width:16px;line-height:16px" onClick="traeJustificacionHCE(this,'descripcionCasoClinico', '<?php echo "dv{$listaArticulos[ 'Kadart' ]}-{$idx}Mostrar";?>' );"></div>
							
					      </td>
						</tr>
					  </table>
					  
					  </div>
					  
					  
					
					
					  <br>
					  <table width="815" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse" align="center">
						<tr>
						  <td colspan="3" class='encabezadotabla' align='center'>MEDICAMENTO POS PREVIAMENTE UTILIZADO</td>
						</tr>
						<tr>
						  <td width="231">Principio Activo 
							<label>
							<input type="text" name="principioActivoPos" id="principioActivoPos"  style="" value="<?php echo $principioActivoPosProtocolo;?>"/>
						  </label></td>
						  <td width="314">Posolog&iacute;a 
							<label>
							<input type="text" name="posologiaPos" id="posologiaPos"  style="" value="<?php echo $posologiaPosProtocolo;?>"/>
						  </label></td>
						  <td width="262">Presentaci&oacute;n
							<label>
							<input type="text" name="presentacionPos" id="presentacionPos"  style="" value="<?php echo $presentacionPosProtocolo;?>"/>
						  </label></td>
						</tr>
						<tr>
						  <td>Dosis/D&iacute;a
							<label>
							<input type="text" name="ddPos" id="ddPos"  style="" value="<?php echo $dosisDiaPosProtocolo;?>"/>
						  </label></td>
						  <td>Cantidad
							<label>
							<input type="text" name="cantidadPos" id="cantidadPos"  style="" value="<?php echo $cantidadPosProtocolo;?>"/>
						  </label></td>
						  <td>Tiempo tratamiento
							<label>
							<input type="text" name="tiempoTratamientoPos" id="tiempoTratamientoPos" style="" value="<?php echo $tiempoTratamientoPosProtocolo;?>"/>
						  </label></td>
						</tr>
						<tr>
						  <td colspan="3"><div align="center"><b>NO EXISTEN ALTERNATIVAS EN EL POS</b></div></td>
						</tr>
						<tr>
						  <td>Principio activo      
							<label>
							<input type="text" name="principioActivoAlternativo" id="principioActivoAlternativo"  style=""/>
						  </label></td>
						  <td>Posolog&iacute;a
							<label>
							<input type="text" name="posologiaAlternativa" id="posologiaAlternativa"  style=""/>
						  </label></td>
						  <td>Presentacion
							<label>
							<input type="text" name="presentacionAlternativa" id="presentacionAlternativa"  style=""/>
						  </label></td>
						</tr>
						<tr>
						  <td>Dosis/D&iacute;a
						  <input name="ddAlternativa" type="text" id="ddAlternativa"  style=""/></td>
						  <td>Cantidad
							<label>
							<input type="text" name="cantidadAlternativa" id="cantidadAlternativa"  style=""/>
						  </label></td>
						  <td>Tiempo tratamiento
							<label>
							<input type="text" name="tiempoTratamientoAlternativa" id="tiempoTratamientoAlternativa"  style=""/>
						  </label></td>
						</tr>
						<tr>
						  <td colspan="3"><div align="center"><b>RESPUESTA CLINICA Y PARACLINICA ALCANZADA CON MEDICAMENTOS POS</b></div></td>
						</tr>
						<tr>
						  <td colspan="3" align='center'>
							No mejor&iacute;a 
							<input type="radio" name="rdRptaClinica" value="NO MEJORIA" onClick="seleccionUnicaRadioPorFila( this, 'rpClinicaPos' )"/>
							
							Reaccion Adversa
							<input type="radio" name="rdRptaClinica" value="REACCION ADVERSA" onClick="seleccionUnicaRadioPorFila( this, 'rpClinicaPos' )" />
							
							Intolerancia
							<input type="radio" name="rdRptaClinica" value="INTOLERANCIA" onClick="seleccionUnicaRadioPorFila( this, 'rpClinicaPos' )" />
							
							No aplica        
							<input type="radio" name="rdRptaClinica" value="NA" onClick="seleccionUnicaRadioPorFila( this, 'rpClinicaPos' )" />
							<input type="hidden" name="rpClinicaPos" id="rpClinicaPos" />
							</td>
						</tr>
						<tr>
						  <td colspan="3"><label>
							<textarea name="observacionesRpClinicaPos" id="observacionesRpClinicaPos" cols="135" rows="3" style=""></textarea>
						  </label></td>
						</tr>
						<tr>
						  <td colspan="3">EXISTE RIESGO INMINENTE PARA LA SALUD Y LA VIDA DEL PACIENTE: 
						  
						  <label>
						  <input type="checkbox" name="existeRiesgoPos" id="existeRiesgoPos" checked/>
						  </label></td>
						</tr>
						<tr>
						  <td colspan="3"><label>
							<textarea name="observacionesRiesgoPos" id="observacionesRiesgoPos" cols="135" rows="3" style=""></textarea>
						  </label></td>
						</tr>
					  </table>
					  <br><br>
					  <table width="815" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse" align="center">
						<tr>
						  <td colspan="3" class='encabezadotabla'><div align="center">MEDICAMENTO NO POS UTILIAZADO</div></td>
						</tr>
						<tr>
						  <td width="231">Principio Activo:
							<label>
							  <?php
								if( trim( $esNoPos[ 'Artgen' ] ) != '' ){
									echo "<b>".trim( $esNoPos[ 'Artgen' ] )."</b>";
									?>
									 <input type="hidden" name="principioActivoNoPos" id="principioActivoNoPos" value="<?php echo trim( $esNoPos[ 'Artgen' ] );?>" />
									<?php
								}
								else{
									?>
									 <input type="text" name="principioActivoNoPos" id="principioActivoNoPos" value="<?php echo trim( $esNoPos[ 'Artgen' ] );?>" style=""/>
									<?php
								}
							  ?>
							</label></td>
						  <td width="314">Posolog&iacute;a
							  <?php
							  $sumPosologia = 0;
							  
							  if( !empty( $posologia ) && count($posologia) > 0 ){
							  
								foreach( $posologia as $keyDosis => $valueDosis ){
									$sumPosologia += (integer) $valueDosis;
								}
								
								if( false && $sumPosologia > 0 ){
									echo "<b>".$sumPosologia."</b>";
									?>
									<input type="hidden" name="posologiaNoPos" id="posologiaNoPos" value="<?php echo $sumPosologia;?>" style=""/>
									<?php
								}
								else{
									if( $sumPosologia == 0 ){
										$sumPosologia = "";
									}
									
									$auxUnPos = $unidadFraccion;
									if( !empty($unidadPosologia) && count( $unidadPosologia ) > 0 ){
								  
										foreach( $unidadPosologia  as $keyUnPosologia => $valueUnPosologia ){
											if( !empty( $valueUnPosologia ) ){
												$auxUnPos = trim( $valueUnPosologia );
												break;
											}
										}
									}
									?>
									<input type="text" name="posologiaNoPos" id="posologiaNoPos" value="<?php echo $sumPosologia." ".$auxUnPos; ?>" style=""/>
									<?php
									
									// if( !empty($unidadPosologia) && count( $unidadPosologia ) > 0 ){
								  
										// foreach( $unidadPosologia  as $keyUnPosologia => $valueUnPosologia ){
											// if( !empty( $valueUnPosologia ) ){
												// $auxUnPos = trim( $valueUnPosologia );
												// break;
											// }
										// }
									// }
									
									// $unPosologia = consultarUnidadesMedidaPosologiaArtIDC();
									
									// $unPosologia[] = array( 'codigo' => $unidadFraccion, 'descripcion' => $unidadFraccion );
									
									// echo "<select name='unPosologia'>";
									// foreach( $unPosologia as $keyUnidadPosologia => $valueUnidadPosologia ){
										// if( $auxUnPos == $valueUnidadPosologia['codigo'] ){
											// echo "<option value='".$valueUnidadPosologia['codigo']."' selected>".$valueUnidadPosologia['descripcion']."</option>";
										// }
										// else{
											// echo "<option value='".$valueUnidadPosologia['codigo']."'>".$valueUnidadPosologia['descripcion']."</option>";
										// }
									// }
									// echo "</select>";
								}
							  }
							  else{
								?>
								<input type="text" name="posologiaNoPos" id="posologiaNoPos" value="<?php echo $posologia;?>" style=""/>
								<?php
							  }
							  // if( !empty($unidadPosologia) && count( $unidadPosologia ) > 0 ){
							  
								  // foreach( $unidadPosologia  as $keyUnPosologia => $valueUnPosologia ){
									// if( !empty( $valueUnPosologia ) ){
										// echo "<b>".trim( $valueUnPosologia )."</b>";
										// break;
									// }
								  // }
							  // }
							  ?>
						  </td>
						  <td width="262">Presentaci&oacute;n
							<?php
							if( $esNoPos[ 'Artfar' ] != '' ){
								echo "<b>".$esNoPos[ 'Artfar' ]."</b>";
								?>
								<input type="hidden" name="presentacionNoPos" id="presentacionNoPos"  value="<?php echo $esNoPos[ 'Artfar' ];?>" style=""/>
								<?php
							}
							else{
								?>
								<input type="text" name="presentacionNoPos" id="presentacionNoPos"  value="<?php echo $esNoPos[ 'Artfar' ];?>" style=""/>
								<?php
							}
							?>
						  </td>
						</tr>
						<tr>
						  <td>Dosis/D&iacute;a
							
							<?php
							//Calculo la dosis por día del medicamento
							//Como puede ser varios medicamento el calculo es la suma de cada uno por día
							// if(isset($frecuencia))
							// {
								// foreach( $frecuencia as $keyFrecuencia => $valueFrecuencia ){
								
									// $varFrecuencia = consultarFrecuencia( $conex, $wbasedato, $valueFrecuencia );
									
									// $valFrecuencias[ $valueFrecuencia ] = consultarFrecuencia( $conex, $wbasedato, $valueFrecuencia );
									
									// if( $varFrecuencia > 0 ){
										// if( !empty( $dosisMaxima[$keyFrecuencia] ) ){
											// $dosisPorDia += floor( ( min( $dosisMaxima[$keyFrecuencia]*$varFrecuencia, 24 ) /$varFrecuencia)*$dosis[$keyFrecuencia] );
										// }
										// else{
											// $dosisPorDia += floor( (24/$varFrecuencia)*$dosis[$keyFrecuencia] );
										// }
									// }
									// else{
										// $dosisPorDia = 0;
										// break;
									// }
								// }
							// }
							
							if( false && $dosisPorDia > 0 ){
								echo "<b>$dosisPorDia</b>";
								?>
								<input type="hidden" name="ddNoPos" id="ddNoPos" value="<?php echo $dosisPorDia;?>"/>
								<?php
							}
							else{
								// <input type="text" name="ddNoPos" id="ddNoPos" value="" style="" onChange="calcularTiempoTratamiento( <?php echo $canManejo  );"/>
								if( $dosisPorDia == 0 ){
									$dosisPorDia = "";
								}
								
								?>
								<input type="text" name="ddNoPos" id="ddNoPos" value="<?php echo $dosisPorDia." ".$unidadFraccion ?>" style="" onChange="calcularTiempoTratamiento( <?php echo $dosisPorDia; ?> );"/>
								<?php
							}
							
							// echo "<b>".trim( $unidadFraccion )."</b>";
							?>
						  </td>
						  <td>Tiempo tratamiento
							
							<?php
							if( false && trim( $tiempoTratamiento ) != '' ){
								echo "<b>".$tiempoTratamiento."</b>";
								?>
								<input type="hidden" name="tiempoTratamientoNoPos" id="tiempoTratamientoNoPos" value="<?php echo $tiempoTratamiento;?>" style="" onChange='calcularCantidadArticulo(<?php echo $dosis.", ".json_encode( $valFrecuencias ).", this, $canManejo, \"dv{$listaArticulos[ 'Kadart' ]}-{$idx}Mostrar\""; ?>)'/>
								<?php
							}
							else{
								?>
								<input type="text" name="tiempoTratamientoNoPos" id="tiempoTratamientoNoPos" value="<?php echo $tiempoTratamiento;?>" style="" onChange='calcularCantidadArticulo(<?php echo $dosis.", ".json_encode( $valFrecuencias ).", this, $canManejo, \"dv{$listaArticulos[ 'Kadart' ]}-{$idx}Mostrar\""; ?>)'/>
								<?php
							}
							?>
							<b>d&iacute;a(s)</b>
						  </td>
						  <td>Cantidad
							
							<?php
							if( false && $can > 0 ){
								echo "<b>".$can."</b>";
								?>
								<input type="hidden" name="cantidadNoPos" id="cantidadNoPos" value="<?php echo $can;?>"  style=""/>
								<?php
							}
							else{
								if( $can == 0 ){
									$can = "";
								}
								?>
								<input type="text" name="cantidadNoPos" id="cantidadNoPos" value="<?php echo $can;?>"  style=""/>
								<?php
							}
							?>
							
						  </td>
						</tr>
						<tr>
						  <td>Nombre comercial: <b><?php echo $esNoPos[ 'Artcom' ];?></b></td>
						  <td>Categor&iacute;a farmaceutica 
						    <?php
							
							if( $esNoPos[ 'Artfar' ] != '' ){
								echo "<b>".trim( $esNoPos[ 'Artfar' ] )."</b>";
								?>
								<input type="hidden" name="categoriaFarmaceuticaNoPos" id="categoriaFarmaceuticaNoPos" value="<?php echo $esNoPos[ 'Artfar' ];?>" style=""/>
								<?php
							}
							else{
								?>
								<input type="text" name="categoriaFarmaceuticaNoPos" id="categoriaFarmaceuticaNoPos" value="<?php echo $esNoPos[ 'Artfar' ];?>" style=""/>
								<?php
							}
							?>
							
						  </td>
						  <td>Registro Invima 
							<?php
							if( $esNoPos[ 'Artreg' ] != '' ){
								echo "<b>".trim( $esNoPos[ 'Artreg' ] )."</b>";
								?>
								<input type="hidden" name="registroInvimaNoPos" id="registroInvimaNoPos" value="<?php echo $esNoPos[ 'Artreg' ];?>"/>
								<?php
							}
							else{
								?>
								<input type="text" name="registroInvimaNoPos" id="registroInvimaNoPos" value="<?php echo $esNoPos[ 'Artreg' ];?>" style=""/>
								<?php
							}
							?>
							
						  </td>
						</tr>
						<tr>
						  <td colspan="3"><div align="center"><b>INDICACIONES TERAPEUTICAS</b></div></td>
						</tr>
						<tr>
						  <td colspan="3">EFECTO TERAPEUTICO DESEADO AL TRATAMIENTO</td>
						</tr>
						<tr>
						  <td colspan="3">
						  <?php
							 
							  if($traer_informacionctcanterior[$codArticulo]['Ctcedt'] != ''){							  
								  echo '<textarea name="efectoTerapeuticoNoPos" id="efectoTerapeuticoNoPos" cols="135" rows="3" style="">'.utf8_encode($traer_informacionctcanterior[$codArticulo]['Ctcedt']).'</textarea>';
							  }else{
								  echo '<textarea name="efectoTerapeuticoNoPos" id="efectoTerapeuticoNoPos" cols="135" rows="3" style="">'.$efectoTerapeuticoNoPosProtocolo.'</textarea>';					  
							  }
							?>	
							
						  </td>
						</tr>
						<tr>
						  <td colspan="3">TIEMPO DE RESPUESTA ESPERADO 
							<label>
							<?php
						 
							  if($traer_informacionctcanterior[$codArticulo]['Ctctre'] != ''){
								  echo '<input type="text" name="tiempoRespuestaEsperado" id="tiempoRespuestaEsperado"  value="'.utf8_encode($traer_informacionctcanterior[$codArticulo]['Ctctre']).'" style=""/> ';
							  }else{
								  echo '<input type="text" name="tiempoRespuestaEsperado" id="tiempoRespuestaEsperado"  value="'.$tiempoRespuestaEsperadoProtocolo.'" style=""/> ';
								  
							  }
							  ?>														
						  </label></td>
						</tr>
						<tr>
						  <td colspan="3">EFECTOS SECUNDARIOS Y POSIBLES RIESGOS AL TRATAMIENTO</td>
						</tr>
						<tr>
						  <td colspan="3"><label>
						   <?php
							 
							  if($traer_informacionctcanterior[$codArticulo]['Ctcert'] != ''){						  
								echo '<textarea name="efectosSecundariosNoPos" id="efectosSecundariosNoPos" cols="135" rows="3" style="">'.utf8_encode($traer_informacionctcanterior[$codArticulo]['Ctcert']).'</textarea>';
							  }else{							  
								echo '<textarea name="efectosSecundariosNoPos" id="efectosSecundariosNoPos" cols="135" rows="3" style="">'.$efectosSecundariosNoPosProtocolo.'</textarea>';							  
							  }
							  
							?>
							
						  </label></td>
						</tr>
					  </table>
					  <br><br>
					  <table width="831" border="1" cellspacing="0" cellpadding="0" align="center">
						<tr>
						  <td colspan="3" class='encabezadotabla'>MEDICAMENTO EN EL PLAN OBLIGATORIO DE SALUD DEL MISMO GRUPO TERAPEUTICO QUE REEMPLAZA O SUSTITUYE EL MEDICAMENTO NO POS SOLICITADO</td>
						</tr>
						<tr>
						  <td width="259">Grupo Terapeutico 
							<label>
							<input type="text" name="grupoTerapeuticoReemplazo" id="grupoTerapeuticoReemplazo"  style="" value='<?php echo ""; ?>'/>
						  </label></td>
						  <td width="287">Principio Activo 
							<label>
							<input type="text" name="principioActivoReemplazo" id="principioActivoReemplazo"  style="" value='<?php echo $principioActivoPosReemplazoProtocolo; ?>'/>
						  </label></td>
						  <td width="277">&nbsp;</td>
						</tr>
						<tr>
						  <td>Presentaci&oacute;n 
							<label>
							<input type="text" name="presentacionReemplazo" id="presentacionReemplazo"  style="" value='<?php echo $presentacionPosReemplazoProtocolo; ?>'/>
						  </label></td>
						  <td>Dosis/d&iacute;a 
							<label>
							<input type="text" name="ddReemplazo" id="ddReemplazo"  style="" value='<?php echo $dosisDiaPosReemplazoProtocolo; ?>'/>
						  </label></td>
						  <td>Tiempo tratamiento 
							<label>
							<input type="text" name="tiempoTratamientoReemplazo" id="tiempoTratamientoReemplazo"  style="" value='<?php echo $tiempoTratamientoPosReemplazoProtocolo; ?>'/>
						  </label></td>
						</tr>
						<tr>
						  <td colspan="3">BIBLIOGRAFIA</td>
						</tr>
						<tr>
						  <td colspan="3"><label>
							<textarea name="bibliografia" id="bibliografia" cols="135" rows="3" style=""><?php echo $bibliografiaProtocolo; ?></textarea>
						  </label></td>
						</tr>
					  </table>
					  <br>
					  <br>
					  
					
					<?php
					
					echo "</div>";
					
					?>
					<script>
						stylerCamposCTCArtsObligatorios( document.getElementById( "<?php echo "dv{$listaArticulos[ 'Kadart' ]}-{$idx}Mostrar"; ?>" ) );
					</script>
					<?php
				}
			}
		}
	
		if( $mostroRegistros ){
		
			//echo "<INPUT type='hidden' id='hiArtsNoPos' value='".substr( $ArtsNoPos, 1 )."'>";
			
			echo "<br>";
			echo "<table align='center'>";
			echo "<tr>";
			echo "<td align='center'>";
			echo "<INPUT type='button' value='Grabar' onClick='grabarCtcArticulos( \"$historia\", \"$ingreso\", \"$fechaKardex\", \"$wemp_pmla\", \"$protocolo\", \"$id\" );' style='width:100'>";
			echo "</td>";
			
			echo "<td align='center'>";
			echo "<INPUT type='button' value='Cerrar' onClick='elminarArticuloArts( this, \"$historia\", \"$ingreso\", \"$fechaKardex\", \"$wemp_pmla\", \"$protocolo\", \"$id\" );' style='width:100'>";
			echo "</td>";
			
			echo "</tr>";
			echo "</table>";
			
			echo "</div>";
			
			
		}
		else{	//Si tiene medicamentos NO POS pero ya estan grabados cierro la ventana
			?>
			<script>
				cerrarVentanaCtcArts( <?php echo "'$historia', '$ingreso', '$fechaKardex', '$wemp_pmla', '$protocolo', '$id'"; ?> );
			</script>
			<?php
		}
	}
	else{ //Si no tiene medicamentos NO POS cierro la ventana
		?>
		<script>
			cerrarVentanaCtcArts( <?php echo "'$historia', '$ingreso', '$fechaKardex', '$wemp_pmla', '$protocolo', '$id'"; ?> );
		</script>
		<?php
	}
}

?>

