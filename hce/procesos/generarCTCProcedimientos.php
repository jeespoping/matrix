<?php
include_once("conex.php");

//============================================================================
//								MODIFICACIONES
//============================================================================
// Diciembre 05 de 2016	- Se modifica la funcion insertarNotaMedicaHCE para que no haya error al insertar la nota medica si ya existe un registro en 
// 						  hce_000243, puede ocurrir al insertar notas medicas de medicamentos y procedimientos a la vez.
// Junio 16 de 2016		- Se cambia el titulo de nota medica por Procedimiento o tecnologia no pos ordenada y se muestra por defecto en 
// 						  este campo el nombre del procedimiento.
// Junio 2 de 2016		- Se agregan validacion para nota medica.
// Enero 21 de 2016		- Se agregan modificaciones para mostrar los procedimientos sin ctc desde ordenes y desde el reporte de impresion.
//============================================================================

//NIVELES SERICOS DE LIVEROLIMUS arrastra RESONANCIA MAGNETICA DE PROSTATA CON ENDOCOI

//RADIOGRAFIA ORHTO RADIOGRAFIA se arrastra EL DE ECOENDOSCOPIA
/************************************************************************
 * Consulta el diagnóstico del paciente segun
 ************************************************************************/
function consultarRegMedico($conex, $wbasedato, $usuMed)
{
	$val="";

	$queryRegMedico = "SELECT Medtdo,Meddoc,Medreg,Medesp  
					 FROM ".$wbasedato."_000048 
					WHERE Meduma='".$usuMed."';";
	
	$res = mysql_query($queryRegMedico,$conex) or die (mysql_errno()." - ".mysql_error());
	// $nummed = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 2 ];
	}
	
	return $val;
}

function consultarFirma($conex, $whce, $usuMed)
{
	$val="";
	
	$queryFirma = "SELECT Usucla 
					 FROM ".$whce."_000020 
					WHERE Usucod='".$usuMed."' 
					  AND Usuest='on';";
	
	$res = mysql_query($queryFirma,$conex) or die (mysql_errno()." - ".mysql_error());
	// $nummed = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
	}
	
	return $val;
}
function consultarUbicacion($conex, $whce, $his, $ing, $queryUbicacion)
{
	$val="";
	//reemplazar historia HIS e ingreso ING
	$queryUbicacion = str_replace("HIS",$his,$queryUbicacion);
	$queryUbicacion = str_replace("ING",$ing,$queryUbicacion);
	// echo $queryUbicacion;
	$res = mysql_query($queryUbicacion,$conex) or die (mysql_errno()." - ".mysql_error());
	// $nummed = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
	}
	
	return $val;
}

function insertarNotaMedicaHCE( $conex, $whce,$wbasedato, $fechaCTC, $horaCTC, $his, $ing, $notaMedica,$ctcmed,$idCTC )
{
	$fecha_data = date( "Y-m-d" ) ;
	$hora_data = date( "H:i:s" ) ;
	$cco = "";
	
	$notaMedica = "NOTA MÉDICA CTC PROCEDIMIENTOS [".$idCTC."]: ".$notaMedica; 
	// $notaMedica = "PROCEDIMIENTO O TECNOLOGÍA NO POS ORDENADA [".$idCTC."]: ".$notaMedica; 
	
	
	$qFormularioHCE = " SELECT Hora_data,Movdat 
							FROM ".$whce."_000243 
						   WHERE Fecha_data='".$fecha_data."' 
						     AND Hora_data='".$hora_data."' 
						     AND movpro='000243' 
							 AND movcon='6' 
							 AND movhis='".$his."' 
							 AND moving='".$ing."' 
							 AND movtip='Memo' 
							 AND movusu='".$ctcmed."';";
	
	
	$resFormularioHCE = mysql_query($qFormularioHCE,$conex) or die (mysql_errno()." - ".mysql_error());
		
	while(mysql_num_rows($resFormularioHCE) > 0)
	{
		if( $rowFormularioHCE = mysql_fetch_array($resFormularioHCE) ){
			
			if($rowFormularioHCE['Movdat'] != $notaMedica )
			{
				$hora_data = date( "H:i:s", strtotime("+1 second") ) ;
			}
		}
		
		$qFormularioHCE = " SELECT Hora_data,Movdat 
							FROM ".$whce."_000243 
						   WHERE Fecha_data='".$fecha_data."' 
						     AND Hora_data='".$hora_data."' 
						     AND movpro='000243' 
							 AND movcon='6' 
							 AND movhis='".$his."' 
							 AND moving='".$ing."' 
							 AND movtip='Memo' 
							 AND movusu='".$ctcmed."';";
		
		$resFormularioHCE = mysql_query($qFormularioHCE,$conex) or die (mysql_errno()." - ".mysql_error());
	}
	
	
	// $qFormularioHCE = " SELECT Hora_data,Movdat 
							// FROM ".$whce."_000243 
						   // WHERE Fecha_data='".$fecha_data."' 
						     // AND Hora_data='".$hora_data."' 
						     // AND movpro='000243' 
							 // AND movcon='6' 
							 // AND movhis='".$his."' 
							 // AND moving='".$ing."' 
							 // AND movtip='Memo' 
							 // AND movusu='".$ctcmed."';";
	
	
	// $resFormularioHCE = mysql_query($qFormularioHCE,$conex) or die (mysql_errno()." - ".mysql_error());
	// $numFormularioHCE = mysql_num_rows($resFormularioHCE);
			
	// if($numFormularioHCE > 0)
	// {
		// // && $rowFormularioHCE['Hora_data'] == $hora_data
		// if( $rowFormularioHCE = mysql_fetch_array($resFormularioHCE) ){
			
			// if($rowFormularioHCE['Movdat'] != $notaMedica )
			// {
				// $hora_data = date( "H:i:s", strtotime("+1 second") ) ;
			// }
		// }
	// }
	
	
	
	$qDatosFormulario = " SELECT Detcon,Dettip,Detfor 
							FROM ".$whce."_000002 
						   WHERE detpro='000243' 
						     AND Dettip !='Titulo';";
	
	$resDatosFormulario = mysql_query($qDatosFormulario,$conex) or die (mysql_errno()." - ".mysql_error());
	$numDatosFormulario = mysql_num_rows($resDatosFormulario);
	
	while( $rowDatosFormulario = mysql_fetch_array($resDatosFormulario) ){
		$datosFormulario[] = $rowDatosFormulario[ 0 ];
		
		$consecutivo = $rowDatosFormulario['Detcon'];
		$tipoDato 	 = $rowDatosFormulario['Dettip'];
		if($consecutivo== 2 )
		{
			$valorAGrabar = $fechaCTC;
		}
		
		switch ($consecutivo) {

			case 2:
				$valorAGrabar = $fechaCTC;
				break;

			case 3:
				$valorAGrabar = $horaCTC;
				break;
				
			case 4:
			
				$ubicacion = consultarUbicacion($conex, $whce, $his, $ing, $rowDatosFormulario['Detfor']);
				$valorAGrabar = $ubicacion;
				$cco = explode("-",$ubicacion);
				break;
				
			case 6:
				$valorAGrabar = $notaMedica;
				break;

			default:
				$valorAGrabar = "";
				break;
		}
		
		
		if($valorAGrabar!="")
		{
			$queryInsertHce= "INSERT INTO ".$whce."_000243 (	Medico	,		Fecha_data	,	  Hora_data	   , movpro ,		movcon		  ,		 movhis   ,		 moving   ,		 movtip	   ,		movdat	   ,	movusu    ,	Seguridad	) 
													VALUES ('".$whce."' , '".$fecha_data."' , '".$hora_data."' ,'000243',  '".$consecutivo."' ,'".$his."', '".$ing."', '".$tipoDato."','".$valorAGrabar."','".$ctcmed ."','C-".$whce."');";

			$res = mysql_query( $queryInsertHce, $conex ) or die( mysql_errno()." - Error en el query $queryInsertHce - ".mysql_error() );
	
		}
		
		
	}	
	// var_dump($datosFormulario);
	
	//Firma
	$firma = consultarFirma($conex, $whce,$ctcmed);
	$queryInsertHce= "INSERT INTO ".$whce."_000243 (	Medico	,		Fecha_data	,	  Hora_data	   , movpro ,  movcon ,	 	movhis    ,	 moving	   , movtip ,	movdat		,	 movusu	   ,Seguridad	) 
											VALUES ('".$whce."' , '".$fecha_data."' , '".$hora_data."' ,'000243',  '1000' ,'".$his."', '".$ing."', 'Firma',  '".$firma."'	,'".$ctcmed ."','C-".$whce."');";

	$res = mysql_query( $queryInsertHce, $conex ) or die( mysql_errno()." - Error en el query $queryInsertHce - ".mysql_error() );
	
	//Registrar firma
	$regMedico = consultarRegMedico($conex, $wbasedato,$ctcmed);
	$queryInsertHce= "INSERT INTO ".$whce."_000036 (	Medico	,		Fecha_data	,	  Hora_data	   , Firpro, Firhis, Firing, Firusu, Firfir, Firrol,Fircco,Seguridad	) 
											VALUES ('".$whce."' , '".$fecha_data."' , '".$hora_data."' ,'000243',  '".$his."' , '".$ing."', '".$ctcmed ."',  'on'	,'".$regMedico ."','".$cco[0] ."','C-".$whce."');";

	$res = mysql_query( $queryInsertHce, $conex ) or die( mysql_errno()." - Error en el query $queryInsertHce - ".mysql_error() );
	
}

// function consultarDiagnostico( $conex, $whce, $his, $ing ){
function consultarDiagnostico( $conex, $wmovhos, $his, $ing ){
	
	$val = "";

	// $sql = "SELECT ".$whce."_000051.movdat, ".$whce."_000051.Fecha_data, ".$whce."_000051.Hora_data
	// 		  FROM ".$whce."_000051
	// 		 WHERE ".$whce."_000051.movdat != ''
	// 		   AND ".$whce."_000051.movdat != ' '
	// 		   AND ".$whce."_000051.movcon = 182
	// 		   AND movhis='".$his."'
	// 		   AND moving='".$ing."'
	// 		UNION ALL
	// 		SELECT ".$whce."_000052.movdat, ".$whce."_000052.Fecha_data, ".$whce."_000052.Hora_data 
	// 		  FROM ".$whce."_000052
	// 		 WHERE ".$whce."_000052.movdat != ''
	// 		   AND ".$whce."_000052.movdat != ' '
	// 		   AND ".$whce."_000052.movcon = 141
	// 		   AND movhis='".$his."'
	// 		   AND moving='".$ing."'
	// 		UNION ALL
	// 		SELECT ".$whce."_000063.movdat, ".$whce."_000063.Fecha_data, ".$whce."_000063.Hora_data
	// 		  FROM ".$whce."_000063
	// 		 WHERE ".$whce."_000063.movdat != ''
	// 		   AND ".$whce."_000063.movdat != ' '
	// 		   AND ".$whce."_000063.movcon = 240
	// 		   AND movhis='".$his."' 
	// 		   AND moving='".$ing."'
	// 		ORDER BY Fecha_data DESC, Hora_data DESC";

	$sql = "SELECT r.Codigo  as diagnostico, m.Fecha_data , m.Hora_data 
				FROM {$wmovhos}_000243 m
					inner join root_000011 r on r.Codigo = m.Diacod 
				where diahis = '{$his}' and diaing = '{$ing}'";
			
	$res = mysql_query($sql,$conex) or die (mysql_errno()." - ".mysql_error());
	$nummed = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
	}
	
	return $val;
}

/************************************************************************************************************************************************
 * Modificaciones:
 *
 * Febrero 25 de 2014		Edwin MG.		Para la justificación de procedimientos que se traen por protocolo se corrige para que muestre bien 
 *											los caracateres especiales
 ************************************************************************************************************************************************/
function consultarAliasPorAplicacionIncProcs($conexion, $codigoInstitucion, $nombreAplicacion){
	
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
		$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
	}
	
	return $wedad;
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


/****************************************************************************************************
 * Consulta si se registraron opciones POS para el procedimiento 
 * Retorna 	1: Si
 *			2: No
 ****************************************************************************************************/
function consultarOpcionesPos( $conex, $wbasedato, $historia, $ingreso ){

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
		
		if( '' == '' ){
			$val = 1;
		}
		elseif( 'si' == 'on' ){
			$val = 2;
		}
		else{
			$val = 3;
		}
	}
	
	$val = 1;
	
	return $val;
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

/***************************************************************************************
 * Crea un calendario, similar a campoFechaDefecto de comun.php pero permite 
 * id diferente al nombre del campo
 ***************************************************************************************/
function campoFechaDefectoCTC($nombreCampo, $idCampo, $fechaDefecto){
	echo "<INPUT TYPE='text' NAME='$nombreCampo' id='$idCampo' value='".$fechaDefecto."' size=11 readonly class='textoNormal'>";
	echo "&nbsp;<button id='btn$idCampo' name='btn$nombreCampo'>...</button>";
	
	echo '<script language="Javascript">';
	echo "Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'$idCampo',button:'btn$idCampo',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});";
	//echo "addEvent( \"click\", document.getElementById( '$idCampo' ), zIndexZapatec );";
	
	
	echo '</script>';
}


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
		$stPartValues .= ",'".mysql_real_escape_string($valueDatos)."'";
	}
	
	$stPartInsert = "INSERT INTO $tabla(".substr( $stPartInsert, 1 ).")";	//quito la coma inicial
	$stPartValues = " VALUES (".substr( $stPartValues, 1 ).")";
	
	return $stPartInsert.$stPartValues;
}

/************************************************************
 * Dice si el medicamento en NO POS
 ************************************************************/
function esNoPOS( $conex, $wbasedato, $art ){

	$val = false;

	$sql = "SELECT *
			FROM
				{$wbasedato}_000026
			WHERE
				artcod = '$art'
				AND artpos = 'N'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = mysql_fetch_array( $res );
	}
	
	return $val;
}

/****************************************************************************************************************
 * Busca si ya fue insertado un registro en la base de datos, y de ser así devuelve el registro completo
 ****************************************************************************************************************/
function buscarSiExisteRegistro( $conex, $wbasedato, $historia, $ingreso, $tipoOrden, $procedimiento, $item, $nroOrden, $estado = 'on' ){

	$val = false;

	$sql = "SELECT 
				* 
			FROM 
				{$wbasedato}_000135
			WHERE
				Ctchis = '$historia'
				AND Ctcing = '$ingreso'
				AND Ctctor = '$tipoOrden'
				AND Ctcpro = '$procedimiento'
				AND Ctcest = '$estado'
				AND Ctcnro = '$nroOrden'
				AND Ctcite = '$item'
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
// function generarProcedimientosAImprimir( $conex, $whce, $wbasedato, $historia, $ingreso, $fechaKardex, $codMedico, $datos ){
function generarProcedimientosAImprimir( $conex, $whce, $wbasedato, $historia, $ingreso, $fechaKardex, $codMedico, $datos, $HCEnotaMedicaCTC ){
	
	if( true ){
		
		//Debo buscar si ya fue generado el articulo
		$existe = buscarSiExisteRegistro( $conex, $wbasedato, $historia, $ingreso, $datos[ 'Ctctor' ], $datos[ 'Ctcpro' ], $datos[ 'Ctcite' ], $datos[ 'Ctcnro' ], 'on' );
				  
		if( true ){
		
			if( empty( $existe ) ){ //Si no existe
				
				$sql = crearStringInsert( "{$wbasedato}_000135", $datos );
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				
				
				if($HCEnotaMedicaCTC !="")
				{
					$idCTC = mysql_insert_id();
					//Insertar formulario hce
					insertarNotaMedicaHCE($conex,$whce,$wbasedato,$datos[ 'Fecha_data' ],$datos[ 'Hora_data' ],$historia,$ingreso,$HCEnotaMedicaCTC,$datos[ 'Ctcmed' ],$idCTC);	
				}
			}
			// else{
				
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
			// }
		}
	}
	
	return $val;
}



/************************************************************************************************************************
 * 											INICIO DEL PROGRAMA
 ************************************************************************************************************************/
//include_once( "root/comun.php" );
include_once( "conex.php" );



$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));
  
$wuser1=explode("-",$user);
$wusuario=trim($wuser1[1]);

$wbasedato = consultarAliasPorAplicacionIncProcs( $conex, $wemp_pmla, "movhos" );
$whce = consultarAliasPorAplicacionIncProcs( $conex, $wemp_pmla, "hce" );

if( $consultaAjax ){	//si hay ajax
	
	$datos[ 'Medico' ] = "$wbasedato";
	$datos[ 'Fecha_data' ] = date( "Y-m-d" );
	$datos[ 'Hora_data' ] = date( "H:i:s" );
	$datos[ 'Seguridad' ] = "C-$wbasedato";
	
	
	if($accion == "R" || $accion == "M")
	{
		$sqlFechas = "  SELECT Fecha_data,Hora_data 
						  FROM ".$whce."_000028 
						 WHERE Dettor='".$tipoOrden."' 
						 AND Detnro='".$nroOrden."' 
						 AND Detite='".$item."' 
						 AND Detcod='".$codigoProcedimiento."' 
						 AND Detest='on';";
						
		$resFechas = mysql_query( $sqlFechas, $conex ) or die( mysql_errno()." - Error en el query $sqlFechas - ".mysql_error() );
		
		$rowsFechas = mysql_fetch_array( $resFechas );
		
		
		$datos[ 'Fecha_data' ] = $rowsFechas[0];
		$datos[ 'Hora_data' ] = $rowsFechas[1];
		
	}
	

		
	
	$datos[ 'Ctchis' ] = $historia;
	$datos[ 'Ctcing' ] = $ingreso;
	
	$datos[ 'Ctctor' ] = utf8_decode( $tipoOrden );
	$datos[ 'Ctcnro' ] = utf8_decode( $nroOrden );
	$datos[ 'Ctcite' ] = utf8_decode( $item );
	$datos[ 'Ctcpro' ] = utf8_decode( $codigoProcedimiento );
	
	$datos[ 'Ctcpp1' ] = utf8_decode( $fechaProcedimientoPrevio1 );
	$datos[ 'Ctcrp1' ] = utf8_decode( $razonProcedimientosPrevio1 );
	$datos[ 'Ctcpp2' ] = utf8_decode( $fechaProcedimientoPrevio2 );
	$datos[ 'Ctcrp2' ] = utf8_decode( $razonProcedimientosPrevio2 );
	$datos[ 'Ctcfus' ] = utf8_decode( $frecuenciaUso );
	$datos[ 'Ctccas' ] = utf8_decode( $cantidadSolicitada );
	$datos[ 'Ctcdtt' ] = utf8_decode( $diasTratamiento );
	$datos[ 'Ctcjus' ] = utf8_decode( $justificacion );
	$datos[ 'Ctctat' ] = utf8_decode( $tipoAtencion );
	$datos[ 'Ctctse' ] = utf8_decode( $tipoServicio );

	// $datos[ 'Ctcmed' ] = $wusuario;
	$datos[ 'Ctcmed' ] = $accion == "R" ? utf8_decode( $medico ) : utf8_decode( $wusuario )  ;
	$datos[ 'Ctcfge' ] = date( "Y-m-d" );
	$datos[ 'Ctchge' ] = date( "H:i:s" );
	$datos[ 'Ctcuim' ] = '';
	$datos[ 'Ctcfim' ] = '0000-00-00';
	$datos[ 'Ctchim' ] = '00:00:00';
	$datos[ 'Ctcimp' ] = 'off';
	$datos[ 'Ctcest' ] = 'on';
	
	$datos[ 'Ctcepo' ] = $opcionesPOS;
	$datos[ 'Ctcrnu' ] = utf8_decode( $razonesNoUsoPos );
	
	$datos[ 'Ctcpso' ] = utf8_decode( $propositoDeLoSolicitado );
	$datos[ 'Ctcdcc' ] = utf8_decode( $descripcionCasoClinico );

	if($accion == "R" || $accion == "M")
	{
		$datos[ 'Ctcacc' ] = $accion;
		$datos[ 'Ctcacr' ] = "on";
		$datos[ 'Ctcacu' ] = utf8_decode( $wusuario ) ;
		$datos[ 'Ctcacf' ] = date( "Y-m-d" ) ;
		$datos[ 'Ctcach' ] = date( "H:i:s" ) ;
	}
	
	$pideNotaMedica = consultarAliasPorAplicacionIncProcs( $conex, $wemp_pmla, "notaMedicaCTCProcedimientos" );
	
	if($pideNotaMedica=="on")
	{
		// Nota medica, se guarda en formulario hce
		$HCEnotaMedicaCTC = utf8_decode( $notaMedicaCTC );
	}
	else
	{
		$HCEnotaMedicaCTC = "";
	}
	
	
	switch( $consultaAjax ){
	
		case '10':
			// generarProcedimientosAImprimir( $conex, $whce, $wbasedato, $historia, $ingreso, $fechaKardex, $wusuario, $datos );
			generarProcedimientosAImprimir( $conex, $whce, $wbasedato, $historia, $ingreso, $fechaKardex, $wusuario, $datos, $HCEnotaMedicaCTC );
			break;
			
		default: break;
	}
}
else{	//si no hay ajax
?>
	<script type="text/javascript" src="./generarCTCprocedimientos.js?v=<?=md5_file('generarCTCprocedimientos.js');?>"></script>
	<!--<script type="text/javascript" src="./generarCTCprocedimientos.js"></script> -->
	<!--<script type='text/javascript' src='HCE.js' ></script>	 -->	<!-- HCE -->
	
	<style>
	td{
		font-size: 10pt;
	}
	</style>
<?php
	//include_once( "root/comun.php" );
	include_once( "root/montoescrito.php" );
	
	echo "<div style='width:90%'>";
	
	//Busco los tipos de empresa que son EPS
	$tiposEmpresa = consultarAliasPorAplicacionIncProcs( $conex, $wemp_pmla, "tiposEmpresasEps" );
	
	//creo un IN para la consulta
	$list = explode( "-", $tiposEmpresa );
	
	$inEPS = '';
	
	foreach( $list as $key => $value ){
		$inEPS .= ",'$value'";
	}
	
	$inEPS = "IN( ".substr( $inEPS, 1 )." ) ";
	
	$pideNotaMedica = consultarAliasPorAplicacionIncProcs( $conex, $wemp_pmla, "notaMedicaCTCProcedimientos" );
	
	echo "<input type='hidden' id='pideNotaMedica' value='".$pideNotaMedica."'>";
			
	//Consulto los articulos a imprimir de pacientes que tiene EPS
	$sql = "SELECT
				Ordhis, Ording, Ordtor, Ordnro, Detite, Codigo, Descripcion, Codcups, Detjus 
			FROM
				{$whce}_000027 a, {$whce}_000028 b, {$whce}_000017 c, {$wbasedato}_000016 d
			WHERE
				Ordhis = '$historia'
				AND Ording = '$ingreso'
				AND Ordest = 'on'
				AND Detest = 'on'
				AND Detesi LIKE 'P%'
				AND Dettor = ordtor
				AND Detnro = ordnro
				AND detcod = Codigo
				AND Estado = 'on'
				AND NoPos = 'on'
				AND Inghis = Ordhis
				AND InginG = Ording
				AND Ingtip $inEPS
			";
			
	//Consulto los articulos a imprimir de pacientes que tiene EPS
	$sql = "SELECT
				'$historia' as Ordhis, '$ingreso' as Ording, '' as Ordtor, '' as Ordnro, '' as Detite, Codigo, Descripcion, Codcups, '' as Detjus 
			FROM
				{$whce}_000017 c, {$wbasedato}_000016 d
			WHERE
				Inghis = '$historia'
				AND Inging = '$ingreso'
				AND Codigo = '$codExamen'
				AND Estado = 'on'
				AND NoPos = 'on'
				AND Ingtip $inEPS
			";
			
	//Consulto los articulos a imprimir de pacientes que tiene EPS
	$sql = "SELECT
				'$historia' as Ordhis, '$ingreso' as Ording, '' as Ordtor, '' as Ordnro, '' as Detite, Codigo, Descripcion, Codcups, '' as Detjus 
			FROM
				{$whce}_000047 c, {$wbasedato}_000016 d
			WHERE
				Inghis = '$historia'
				AND Inging = '$ingreso'
				AND Codigo = '$codExamen'
				AND Estado = 'on'
				AND NoPos = 'on'
				AND Ingtip $inEPS
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );

	$justCtc = '';
	$opcionPOS = '';
	$razonesParaNoUtilizarlas = '';


	
	$mostroProcedimientos = false;	//indica si en realidad hubo procedimientos a realizar
	
	if( $numrows > 0 ){
	
		$infoPaciente = consultarInfoPaciente( $conex, $wbasedato, $wemp_pmla, $historia, $ingreso );
		
		echo "<table width='100%' align='center'>";
		
		echo "<tr class='encabezadotabla'>";
		echo "<td style='font-size: 12pt' align='center'>";
		echo "JUSTIFICACION INSUMO Y PROCEDIMIENTOS NO POS";
		echo "</td>";		
		echo "</tr>";
		
		echo "<tr class='fila1'>";
		echo "<td style='font-size: 10pt'>";
		echo "<b>".trim( strtoupper( $infoPaciente[ 'Pacap1' ]." ".$infoPaciente[ 'Pacap2' ] ) ).", ".trim( strtoupper( substr( $infoPaciente[ 'Pacno1' ], 0, 1 ) ).strtolower( substr( $infoPaciente[ 'Pacno1' ], 1 ) )." ".strtoupper( substr( $infoPaciente[ 'Pacno2' ], 0, 1 ) ).strtolower( substr( $infoPaciente[ 'Pacno2' ], 1 ) ) )."</b>";
		echo " con <b>".$infoPaciente[ 'Pactid' ]." ".$infoPaciente[ 'Pacced' ]."</b>";
		echo " ubicado en <b>".$infoPaciente[ 'Cconom' ]." - ".$infoPaciente[ 'Ubihac' ]."</b>";
		echo " con historia <b>".$historia."-".$ingreso."</b>";
		echo " y edad <b>".calcularEdad( $infoPaciente[ 'Pacnac' ] )."</b>";
		echo "</td>";		
		echo "</tr>";
		
		echo "</table>";
		echo "<br><br>";
	
		for( $i = 0, $j = 0; $rowsPro = mysql_fetch_array( $res ); $i++ ){
		
			$rowsPro[ 'Descripcion' ] = utf8_decode($rowsPro[ 'Descripcion' ]);
			
			$opcionesPOS = "";
		
			$siPos = '';
			$noPos = '';
			
			if( $i == 0 ){
			
				//  $dxs = substr( strip_tags( consultarDiagnostico( $conex, $whce, $historia, $ingreso ) ), 0, 4 );
				$dxs = consultarDiagnostico( $conex, $wbasedato, $historia, $ingreso );
				 
				 $whereDxs = " AND Prodia = '*'";
				 if( !empty( $dxs ) ){
					$whereDxs = " AND Prodia IN ( '*', '$dxs' )";
				 }
			
				//Consulto los articulos a imprimir de pacientes que tiene EPS
				$qctc = "SELECT
							Dprjus, Dprops, Dpropn, Prodia
						FROM
							{$wbasedato}_000137 a, {$wbasedato}_000138 b
						WHERE
							Pronom = '".$rowsPro[ 'Codigo' ]."'
							AND Proest = 'on'
							AND Procod = Dprpro
							AND Dprest = 'on'
							AND Dprpes = 'CtcProcedimientos'
							$whereDxs
						";
						
				$resctc = mysql_query( $qctc, $conex ) or die( mysql_errno()." - Error en el query $qctc - ".mysql_error() );
				$numctc = mysql_num_rows( $resctc );
				
				if($numctc>0){
				
					$dxAnt = '';
					while( $rowsCtc = mysql_fetch_array( $resctc ) ){
				
						if( empty( $dxAnt ) ||  ( !empty( $dxAnt ) && $dxAnt == '*' ) ){
						
							$dxAnt = trim( $rowsCtc['Prodia'] );
						
							$justCtc = $rowsCtc['Dprjus'];
							$siPos = trim( $rowsCtc['Dprops']  ) == '' ? "" : "checked";
							$noPos = trim( $rowsCtc['Dpropn']  ) == '' ? "" : "checked";
							$optDefectoSi = trim( $rowsCtc['Dprops'] );
							$optDefectoNo = trim( $rowsCtc['Dpropn'] );
							
							if( !empty($siPos) && !empty($noPos) ){
							
								$siPos = '';
								$noPos = '';
							
								// $razonesParaNoUtilizarlas = trim( utf8_encode( $rowsCtc['Dprops'] ) == '' ) ? utf8_encode( $rowsCtc['Dprops'] ) : utf8_encode( $rowsCtc['Dpropn'] );;
							}
							elseif( !empty($siPos) ){
								$razonesParaNoUtilizarlas = trim( $rowsCtc['Dprops'] );
								$opcionesPOS = "Si";
								
							}
							else{
								$razonesParaNoUtilizarlas = trim( utf8_encode( $rowsCtc['Dpropn'] ) );
								$opcionesPOS = "No";
							}
						}
					}
				}
			}
		
		
			$existe = buscarSiExisteRegistro( $conex, $wbasedato, $rowsPro[ 'Ordhis' ], $rowsPro[ 'Ording' ], $rowsPro[ 'Ordtor' ], $rowsPro[ 'Codigo' ], $rowsPro[ 'Detite' ], $rowsPro[ 'Ordnro' ], 'on' );
											
			if( !$existe ){
				
				$mostroProcedimientos = true;
				
				$tipoAtencion = consultarTipoAtencion( $conex, $wbasedato, $historia, $ingreso );
				// if( $wemp_pmla == 10 ){
					// $tipoAtencion = 3;
				// }
				
				switch( $tipoAtencion ){
						
					case 1:
					case 2:
						$hospitalario = 'checked';
						$ambulatorio = 'disabled';
						$urgencias = 'disabled';
						$tipoAtencion = "HOSPITALARIO";
						break;
						
					case 3:
						$ambulatorio = 'checked';
						$hospitalario = 'disabled';
						$urgencias = 'disabled';
						$tipoAtencion = "AMBULATORIO";
						break;
					
					case 4:
						$urgencias = 'checked';
						$ambulatorio = 'checked';
						$hospitalario = 'disabled';
						$tipoAtencion = "URGENCIAS";
						break;
				}
				
				// $opcionesPOS = consultarOpcionesPos( $conex, $wbasedato, $historia, $ingreso );
				
				// switch( $opcionesPOS ){
						
					// case 1:
					// case 2:
						// $siPos = 'checked';
						// $noPos = 'disabled';
						// $opcionesPOS = "Si";
						// break;
						
					// case 3:
						// $noPos = 'checked';
						// $siPos = 'disabled';
						// $opcionesPOS = "No";
						// break;
					
				// }
				
				
				
				@$ArtsNoPos .= "-".$rowsPro[ 'Codigo' ];
				
				$j++;
				
				$class="class='fila".($j%2+1)."'";
				
				echo "<INPUT type='hidden' id='{$rowsPro[ 'Codigo' ]}' value='{$rowsPro[ 'Descripcion' ]}'>";
			
				echo "<div id='dv{$rowsPro[ 'Codigo' ]}' style='display:none'>";
				echo "<table style='width:100%'>";
				echo "<tr $class>";
				echo "<td style='cursor:hand;cursor:pointer;font-size:10pt;height:35'>";
				echo "<b>".$rowsPro[ 'Codigo' ]." - ".$rowsPro[ 'Descripcion' ]."</b>";
				echo "</tr></td>";
				echo "</table>";			
				echo "</div>";
				
				echo "<div id='dv{$rowsPro[ 'Codigo' ]}Mostrar-$idExamen'>";
				
				echo "<INPUT type='hidden' name='tipoOrden' value='{$rowsPro[ 'Ordtor' ]}'>";
				echo "<INPUT type='hidden' name='nroOrden' value='{$rowsPro[ 'Ordnro' ]}'>";
				echo "<INPUT type='hidden' name='item' value='{$rowsPro[ 'Detite' ]}'>";
				?>
				
				<table width="672" border="0" style="border-collapse:collapse" cellpadding="0" align="center">
				  <tr>
					<td>
						<table width="681" border="0" style="border-collapse:collapse" cellpadding="0">
						  <tr>
							<td width="178" class="encabezadoTabla">CASO CLINICO
							</td>
						  </tr>
						  <tr>
							<td align="">
							  <textarea name="descripcionCasoClinico" id="descripcionCasoClinico" cols="105" rows="5" style=""></textarea>
							  <div style="font-size:10px">
									Traer resumen historia cl&iacute;nica
									<?php
									if($accion=="R")
									{
										?>
											<input id="chkJust" type="checkbox" onclick="traeJustificacionHCE2(this,'descripcionCasoClinico',<?php echo $historia; ?>,<?php echo $ingreso; ?>,'<?php echo $wemp_pmla; ?>','<?php echo $wbasedatohce; ?>');" style="width:16px;line-height:16px" name="chkJust">
										<?php
									}
									else
									{
										?>
											<input id="chkJust" type="checkbox" onclick="traeJustificacionHCE(this,'descripcionCasoClinico', '<?php echo "dv{$rowsPro[ 'Codigo' ]}Mostrar-$idExamen"; ?>' );" style="width:16px;line-height:16px" name="chkJust">
										<?php
									}
									
									?>
									<!--<input id="chkJust" type="checkbox" onclick="traeJustificacionHCE(this,'descripcionCasoClinico', '
									<?php // echo "dv{$rowsPro[ 'Codigo' ]}Mostrar-$idExamen"; ?>' );" style="width:16px;line-height:16px" name="chkJust">-->
								</div>
							</td>							
						  </tr>
						</table>
					</td>
				  </tr>
				  
				  <tr>
					<td>&nbsp;</td>
				  </tr>
				  <tr>
					<td style="border-style:solid;border-top-width:1px;border-left-width:1px;border-right-width:1px;border-bottom-width:1px" class="encabezadoTabla">
					  Procedimiento o Insumo NO POS Solicitado: 
					</td>
				  </tr>
				  <tr>
					<td style="border-style:solid;border-top-width:1px;border-left-width:1px;border-right-width:1px;border-bottom-width:1px">
						<br />
					  <table width="712" border="0">
						<tr>
						  <td width="210"><b>Solicitud de tratamiento:</b></td>
						  <td width="170">Ambulario &nbsp;  &nbsp;
							  <input type="radio" value="AMBULATORIO" onClick="seleccionUnicaRadioPorFila( this, 'tipoAtencion' )"  <?php echo $ambulatorio;?>/>
						  </td>
						  <td width="160">Hospitalario  &nbsp;  &nbsp;
							<input type="radio" value="HOSPITALARIO" onClick="seleccionUnicaRadioPorFila( this, 'tipoAtencion' )"  <?php echo $hospitalario;?>/>
							<input type="hidden" name="tipoAtencion" id="tipoAntencion" value="<?php echo $tipoAtencion;?>"/>
						  </td>
						  <td width="170">Urgencias &nbsp;  &nbsp;
							<input type="radio" value="URGENCIAS" onClick="seleccionUnicaRadioPorFila( this, 'tipoAtencion' )"  <?php echo $urgencias;?>/>
						  </td>
						  </tr>
					  </table>
						<br />
					  <table width="712" border="0">
						<tr>
						  <td width="384">Nombre:
						  
							<?php
							if( trim( $rowsPro[ 'Descripcion' ] ) != '' ){
								echo "<b>".$rowsPro[ 'Descripcion' ]."</b>";
								?>
								<input type="hidden" name="nombreProcedimiento" id="nombreProcedimiento" value="<?php echo $rowsPro[ 'Descripcion' ];?>"/>
								<?php
							}
							else{
								?>
								<input type="text" name="nombreProcedimiento" style="width:300;;" id="nombreProcedimiento" value="<?php echo $rowsPro[ 'Descripcion' ];?>"/>
								<?php
							}
							?>
							
						  </td>
						  <td width="318">CUPS &oacute; Reg. INVIMA
							<?php
							if( $rowsPro[ 'Codcups' ] != '' ){
								echo "<b>".$rowsPro[ 'Codcups' ]."</b>";
								?>
								<input type="hidden" name="cupsRegInvima" id="cupsRegInvima" value="<?php echo $rowsPro[ 'Codcups' ];?>" style=""/>
								<?php
							}
							else{
								?>
								<input type="text" name="cupsRegInvima" id="cupsRegInvima" value="<?php echo $rowsPro[ 'Codcups' ];?>" style=""/>
								<?php
							}
							?>
						  </td>
						</tr>
					  </table>
					  
					  <br />
					  <br />
						<b>Justificaci&oacute;n para el uso del procedimiento o insumo No POS solicitado </b>
					  <br>
						<label>
						<textarea name="justificacion" id="justificacion" cols="105" rows="5" style=""><?php echo $justCtc;?></textarea>
						</label>
					  </td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
				  </tr>

				  <tr>
					<td>
						<table width="681" border="0" style="border-collapse:collapse" cellpadding="0">
						  <tr>
							<td colspan="2" class="encabezadoTabla"> &nbsp; OPCIONES POS (DESCRIBIRLAS)</td>
						  </tr>
						  <tr>
							  <td>&iquest;Existen opciones POS&#63; &nbsp;  &nbsp;  &nbsp; &nbsp;  &nbsp;
								 No &nbsp;  &nbsp;
								<input type="radio" value="No" onClick="razonPorDefecto( this, '<?php echo ( str_replace( "\"", '&quot;', str_replace( "'", "\'", str_replace( "\r", "\\r", str_replace( "\n", "\\n", $optDefectoNo ) ) ) ) );?>' );"  <?php echo $noPos;?>/> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;
								Si  &nbsp;  &nbsp;
								<input type="radio" value="Si" onClick="razonPorDefecto( this, '<?php echo ( str_replace( "\"", '&quot;', str_replace( "'", "\'", str_replace( "\r", "\\r", str_replace( "\n", "\\n", $optDefectoSi ) ) ) ) );?>' );"  <?php echo $siPos;?>/> &nbsp;  &nbsp;  &nbsp;
								<input type="hidden" name="opcionesPOS" id="opcionesPOS" value="<?php echo $opcionesPOS; ?>"/>
							  </td>
						  </tr>
						  <tr>
							  <td colspan="2"><br />Razones para no utilizarlas</td>
						  </tr>
						  <tr>
							<td align="center" colspan="2">
							  <textarea name="razonesNoUsoPos" id="razonesNoUsoPos" cols="105" rows="5" style="" value="<?php ?>"><?php echo $razonesParaNoUtilizarlas;?></textarea>
							</td>							
						  </tr>
						</table>
					</td>
				  </tr>
				  
				  <tr>
					<td>&nbsp;</td>
				  </tr>
				  
				  
				  <?php
					if($pideNotaMedica=="on")
					{
						?>
						 <tr>
							<td>
								<table width="681" border="0" style="border-collapse:collapse" cellpadding="0">
								  <tr>
									<td colspan="2" class="encabezadoTabla"> &nbsp; PROCEDIMIENTO O TECNOLOG&IacuteA NO POS ORDENADA</td>
								  </tr>
								  <tr>
									<td align="center" colspan="2">
									  <textarea name="notaMedicaCTC" id="notaMedicaCTC" formularioHCE="000243" camposFormularioHCE="2,3,4,6" cols="105" rows="5" style="" value="<?php ?>"><?php echo $rowsPro[ 'Descripcion' ];?></textarea>
									</td>							
								  </tr>
								</table>
							</td>
						  </tr>
						  
						  <tr>
							<td>&nbsp;</td>
						  </tr>
						<?php
					}
					
					?>
		  
				</table>
				
				<?php
				echo "</div>";
				
				?>
				<script>
					stylerCamposCTCProcsObligatorios( document.getElementById( "<?php echo "dv{$rowsPro[ 'Codigo' ]}Mostrar-$idExamen"; ?>" ) );
				</script>
				<?php
			}
		}
		
		if( $mostroProcedimientos ){
			
			if($accion == "R")
			{
				echo "<INPUT type='hidden' id='hiArtsNoPos1' value='".substr( $ArtsNoPos, 1 )."'>";
			
				echo "<br>";
				echo "<table align='center'>";
				echo "<tr>";
				echo "<td align='center'>";
				echo "<INPUT type='button' value='Grabar' onClick='grabarAjaxProcedimiento2( \"$tipOrden\",\"$nroOrden\",\"$nroItem\",\"$idExamen\", \"$wemp_pmla\",\"$historia\", \"$ingreso\", \"$accion\", \"$medico\" );' style='width:100'>";
				echo "</td>";
				
				echo "<td align='center'>";
				echo "<INPUT type='button' value='Salir sin guardar' onClick='cerrarModalCtc();' style='width:150'>";
				echo "</td>";
				
				echo "</tr>";
				echo "</table>";
				
				
			}
			else
			{
				echo "<INPUT type='hidden' id='hiArtsNoPos1' value='".substr( $ArtsNoPos, 1 )."'>";
			
				echo "<br>";
				echo "<table align='center'>";
				echo "<tr>";
				echo "<td align='center'>";
				echo "<INPUT type='button' value='Grabar' onClick='grabarCtcProcedimiento( \"$historia\", \"$ingreso\", \"$fechaKardex\", \"$wemp_pmla\", \"$idExamen\", \"$cadenaExamSinCTC\", \"$cadenaCTCExamGuardado\" );' style='width:100'>";
				echo "</td>";
				
				
				
				echo "<td align='center'>";
				echo "<INPUT type='button' value='Salir sin guardar' ".(($cadenaExamSinCTC != '') ? "style='display:none'" : '' )." onClick='eliminarProcedimientoNoPos( \"$historia\", \"$ingreso\", \"$fechaKardex\", \"$wemp_pmla\", \"$idExamen\" );' style='width:150'>";
				echo "</td>";
				
				
				echo "</tr>";
			echo "</table>";
			}
		
		
		}
		else{	//Sin tiene procedimientos No Pos pero ya se han generado se cierra la ventana
			?>
			<script>
				cerrarVentanaCtc( <?php echo "'$historia', '$ingreso', '$fechaKardex', '$wemp_pmla'"; ?> );
			</script>
			<?php
		}
	}
	else{	//Sin no tiene procedimientos No Pos se cierra la ventana
		?>
		<script>
			cerrarVentanaCtc( <?php echo "'$historia', '$ingreso', '$fechaKardex', '$wemp_pmla'"; ?> );
		</script>
		<?php
	}
	
	echo "</div>";
}
?>