<?php
include_once("root/comun.php");
/**
 * Modificaciones
 * =============================================================================================================================================
 * Octubre 21 de 2020		Edwin MG	- Se cambia el campo de correo electrónico de Correo electrónico del responsalbe(Paccre) por correo de usuario (Paccor)
 * Junio 19 de 2020			Edwin MG	- Se crea función estadoPorInteroperabilidadEsCancelado para indicar si un estado recibido por interoperabilidad
 *										  es cancelado o no
 * Junio 03 de 2020			Edwin MG	- No se permite el cambio de estado Autorizado a Pendiente
 * Mayo 21 de 2020			Edwin MG	- No se puede modificar el estado si es cancelado cuando se cambia el estado automaticamente (funcion cambioEstadoInteroperabilidad)
 * Mayo 19 de 2020			Edwin MG	- Se crea la función cambioEstadoInteroperabilidad para que permite el cambio de estados automaticamente
 *										  por interoperabilidad si el cco dónde se encuentra ubicado el paciente la tiene activa.
 *										  Esta función es llamada al momento de traslado desde la función crearMensajesHL7OLMTrasladoPacientes
 *										  del script interoperabilidad/procesoso/IoLaboratorio o al momento de abrir ordenes en el script
 *										  hce/procesos/ordenes.php
 * Mayo 04 de 2020			Edwin MG	- Se crea funciones ccoConInteroperabilidadPorEstudio y ccoConInteroperabilidadLaboratorio para
 *										  realizar interoperabilidad por cco o por estudio
 * Febrero 20 de 2019		Edwin MG	- En la función consultarProcedimientosCargados, en el query principal se agrega la fecha_data para ordenar
 *										  correctamente los registros
 *										- En la función informacionPaciente se hace un trim al número de documento del paciente para que no 
 *										  tengas espacios al final
 * Noviembre 26 de 2019		Edwin MG	- Se estaba mostrando la modal al cargar insumos en CITY PLAZA (1033)
 *										  Esto era debido a que se buscaba el último procedimiento cargado y no lo último factrado.
 *										  Para corrigir esto se crea la funcion consultarProcedimientoPorCodigo que se llama desde la función 
 *										  consultarProcedimientosCargados y a esta última función ( consultarProcedimientosCargados )
 *										  se verifica que lo último cargado sea un procedimiento si no, retorna un array vacío
 * =============================================================================================================================================
 */
 
function estadoPorInteroperabilidadEsCancelado( $conex, $wmovhos, $westado_externo ){
	
	$val = false;
	
	if( !empty($westado_externo) ){
	
		//Busco si se permite cancelar el examen
		$sql = "SELECT Estcan
				  FROM ".$wmovhos."_000257 a
				 WHERE Esthl7 = '".$westado_externo."'
				   AND Estest = 'on'
				   AND Estcan = 'on'
				";
				
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
				
		if( $num > 0 ){
			$val = true;
		}
	}
	else{
		$val = true;
	}
	
	return $val;
}
 

/******************************************************************************************************************
 * Cambia el estado de las ordenes
 ******************************************************************************************************************/
function cambioEstadoInteroperabilidad( $conex, $wmovhos, $whce, $his, $ing ){
	
	$val = '';
	
	//Consulto los cco con interoperabilidad para la historia
	$sql = "SELECT Ccotio
			  FROM ".$wmovhos."_000011 a, ".$wmovhos."_000020 b
			 WHERE ccocod = habcco
			   AND habhis = '".$his."'
			   AND habing = '".$ing."'
			 ";

	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$ccoConInteroperabilidad = explode( "-", $rows[ 'Ccotio' ] );
		
		//Consulto que la interoperabilidad si este activa
		$sql = "SELECT Valtor
				  FROM ".$wmovhos."_000267 a
				 WHERE valtor IN( '".implode( ",", $ccoConInteroperabilidad )."' )
				   AND valest = 'on'
				 GROUP BY Valtor
				 ";

		$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
		$num = mysql_num_rows( $res );
		
		while( $rowsTor = mysql_fetch_array($res) ){
			
			//Consulto encabezado de kardex del dia
			//Si el estudio tiene estado cancelado por parte de enfermería (detesi = C) no se permite cambiar el estado por parte de laboratorio y
			//Si el estado que tiene por parte de enfermería es Autoriazado(detesi = A) no se permite cambiar el estado por parte de laboratorio si se va a dejar en pendiente
			$sql = "UPDATE ".$whce."_000027 b, ".$wmovhos."_000159 c, ".$wmovhos."_000257 d, ".$wmovhos."_000045 e, ".$wmovhos."_000045 f
					   SET c.detesi = d.estepc
					 WHERE c.dettor = b.ordtor
					   AND c.detnro = b.ordnro
					   AND b.ordhis = '".$his."'
					   AND b.ording = '".$ing."'
					   AND b.ordtor = '".$rowsTor['Valtor']."'
					   AND d.esthl7 = c.deteex
					   AND d.estest = 'on'
					   AND b.ordest = 'on'
					   AND d.estepc = e.Eexcod
					   AND c.detesi = f.Eexcod
					   AND f.eexcan!= 'on'
					   AND ( e.eexepe != 'on' OR ( e.eexepe = 'on' AND f.eexepe != 'on' AND f.eexeau != 'on' ) )
					 ";

			$resupt = mysql_query( $sql, $conex ) or die ( "Error: ".mysql_errno()." - en el query: $sql - " . mysql_error() );
			
			//Consulto encabezado de kardex del dia
			//Si el estudio tiene estado cancelado por parte de enfermería (detesi = C) no se permite cambiar el estado por parte de laboratorio y
			//Si el estado que tiene por parte de enfermería es Autoriazado(detesi = A) no se permite cambiar el estado por parte de laboratorio si se va a dejar en pendiente
			$sql = "UPDATE ".$whce."_000027 b, ".$whce."_000028 c, ".$wmovhos."_000257 d, ".$wmovhos."_000045 e, ".$wmovhos."_000045 f
					   SET c.detesi = d.estepc
					 WHERE c.dettor = b.ordtor
					   AND c.detnro = b.ordnro
					   AND b.ordhis = '".$his."'
					   AND b.ording = '".$ing."'
					   AND b.ordtor = '".$rowsTor['Valtor']."'
					   AND d.esthl7 = c.deteex
					   AND d.estest = 'on'
					   AND b.ordest = 'on'
					   AND d.estepc = e.Eexcod
					   AND c.detesi = f.Eexcod
					   AND f.eexcan!= 'on'
					   AND ( e.eexepe != 'on' OR ( e.eexepe = 'on' AND f.eexepe != 'on' AND f.eexeau != 'on' ) )
					 ";

			$resupt = mysql_query( $sql, $conex ) or die ( "Error: ".mysql_errno()." - en el query: $sql - " . mysql_error() );
			
			if( mysql_affected_rows > 0 ){
				$val = true;
			}
		}
	}
	
	return $val;
	
}
 
function ccoConInteroperabilidadPorEstudio( $conex, $wbasedato, $his, $ing, $tor, $cup ){
	
	$val = [];
	
	//Consulto encabezado de kardex del dia
	$sql = "SELECT Ccocod, Ccoerl
			  FROM ".$wbasedato."_000018 a, ".$wbasedato."_000011 b
			 WHERE ubihis = '".$his."' 
			   AND ubiing = '".$ing."'
			   AND ubisac = ccocod";

	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		$interoperabilidadPorEstudio = $rows[ 'Ccoerl' ] == 'on';
		
		if( $interoperabilidadPorEstudio ){
		
			$tablaOfertas 	= "";
			$campoOferta	= "";
			$campoEstado	= "";
			
			//Consulto si existe cups ofertados por tipo de orden
			$sql = "SELECT Valtoc, Valcoc, Valeoc, Valerl
					  FROM ".$wbasedato."_000267
					 WHERE valtor = '".$tor."'
					   AND valest = 'on'
				  GROUP BY 1,2,3";
			
			$resToOfertado 	= mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			$numToOfertado	= mysql_num_rows($resToOfertado);
			
			if( $numToOfertado > 0 ){
				
				if( $rowsToOfertado = mysql_fetch_array( $resToOfertado ) ){
					
					$tablaOfertas 		= $rowsToOfertado['Valtoc'];
					$campoOferta		= $rowsToOfertado['Valcoc'];
					$campoEstado		= $rowsToOfertado['Valeoc'];
					$campoRealizaUnidad	= $rowsToOfertado['Valerl'];
					
					//Consulto encabezado de kardex del dia
					$sql = "SELECT *
							  FROM ".$tablaOfertas."
							 WHERE ".$campoOferta." = '".$cup."' 
							   AND ".$campoEstado." = 'on'";

					$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
					$num = mysql_num_rows( $res );
					
					if( $rows = mysql_fetch_array( $res ) ){
						if( isset( $rows[ $campoRealizaUnidad ] ) ){
							$val = $rows['Proerl'] == 'on' ? [ $tor ] : [];
						}
					}
				}
			}
		}
	}
	
	return $val;
}

function ccoConInteroperabilidadLaboratorio( $conex, $wbasedato, $his, $ing ){
	
	$val = [];
	
	//Consulto encabezado de kardex del dia
	$sql = "SELECT Ccotio
			  FROM ".$wbasedato."_000018 a, ".$wbasedato."_000011 b
			 WHERE ubihis = '".$his."' 
			   AND ubiing = '".$ing."'
			   AND ubisac = ccocod";

	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$rows = mysql_fetch_array( $res );
		
		$val = explode( "-", $rows[ 'Ccotio' ] );
	}
	
	return $val;
}

/**
 * Registra el mensaje en el log
 */
function registrarDetalleLog( $conex, $wmovhos, $his, $ing, $tor, $nro, $ite, $clave, $msg,$ack = null){
	
	$val = false;
	
	$fecha 	= date("Y-m-d");
	$hora 	= date("H:i:s");
	
	$sql = "INSERT INTO 
				".$wmovhos."_000273(     Medico    , Fecha_data  , Hora_data  ,   Loghis  , Loging    ,  Logtor   ,   Lognro  ,   Logite  ,    Logcla   ,              Logtxt              , Logest , Logack,   Seguridad     ) 
							VALUES ( '".$wmovhos."', '".$fecha."', '".$hora."', '".$his."', '".$ing."', '".$tor."', '".$nro."', '".$ite."', '".$clave."', '".mysql_escape_string( $msg )."',  'on'  , '".$ack."' ,'C-".$wmovhos."' )
			   ";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ". mysql_error() );
	$num = mysql_affected_rows();
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}



/**
 * Registra el mensaje en el log
 */
function registrarMsgLogHl7( $conex, $wmovhos, $his, $ing, $tdo, $ndo, $des, $tor, $nro, $ite, $msg,$ack = null ){
	
	$val = false;
	
	$fecha 	= date("Y-m-d");
	$hora 	= date("H:i:s");
	
	$sql = "INSERT INTO 
				".$wmovhos."_000270(     Medico    , Fecha_data  , Hora_data  , `Loghis`  ,  `Loging` , `Logtdo`  , `Logndo`  , `Logdes` ,  Logfec     , Loghor     , Logtor    , Lognro    , Logite    ,           Logmsg          ,      Logest ,     Logack        ,  Seguridad      ) 
							VALUES ( '".$wmovhos."', '".$fecha."', '".$hora."', '".$his."', '".$ing."', '".$tdo."', '".$ndo."', '".$des."', '".$fecha."', '".$hora."', '".$tor."', '".$nro."', '".$ite."', '".mysql_escape_string( $msg )."',  'on'  ,'".$ack."' , 'C-".$wmovhos."' )
			   ";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ". mysql_error() );
	$num = mysql_affected_rows();
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
} 


function consultarHistoriaPaciente( $conex, $wemp_pmla, $tipoDocumento, $nroDocumento ){
	
	$val = [];
	
	$sql = "SELECT Orihis, Oriing
			  FROM root_000037
			 WHERE Oritid = '".$tipoDocumento."'
			   AND Oriced = '".$nroDocumento."'
			   AND Oriori = '".$wemp_pmla."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		$val = [
				'historia' 		=> $row[ 'Orihis' ],
				'ultimoIngreso'	=> $row[ 'Oriing' ],
			];
	}
	
	return $val;
}

function consultarProcedimiento( $conex, $wcliame, $cup ){
	
	$val = [];
	
	$sql = "SELECT Procup, Pronom
			  FROM ".$wcliame."_000103
			 WHERE Procup = '".$cup."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		$val[] = [
				'codigo' 		=> $row[ 'Procup' ] ,
				'descripcion' 	=> $row[ 'Pronom' ] ,
			];
	}
	
	return $val;
}

function consultarProcedimientoPorCodigo( $conex, $wcliame, $cod ){
	
	$val = [];
	
	$sql = "SELECT Procup, Pronom
			  FROM ".$wcliame."_000103
			 WHERE Procod = '".$cod."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) ){
		$val = [
				'codigo' 		=> $row[ 'Procup' ] ,
				'descripcion' 	=> $row[ 'Pronom' ] ,
			];
	}
	
	return $val;
}


function consultarProcedimientosCargados( $conex, $wcliame, $historia, $ingreso, $cco ){
	
	$val = [];
	
	// $sql = "SELECT Tcarprocod, Tcarpronom, Procup, Gruinv
			  // FROM ".$wcliame."_000106 a, ".$wcliame."_000200 b, ".$wcliame."_000103 c
			 // WHERE Tcarhis = '".$historia."'
			   // AND Tcaring = '".$ingreso."'
			   // AND Tcarfec = CURDATE()
			   // AND Tcarconcod = Grucod
			   // AND Procod = Tcarprocod
			   // AND Tcarser = '".$cco."'
		  // ORDER BY a.Hora_data DESC
			// ";
			
	$sql = "SELECT Tcarprocod, Tcarpronom, Gruinv
			  FROM ".$wcliame."_000106 a, ".$wcliame."_000200 b
			 WHERE Tcarhis = '".$historia."'
			   AND Tcaring = '".$ingreso."'
			   AND Tcarconcod = Grucod
			   AND Tcarser = '".$cco."'
			   AND a.Fecha_data = CURDATE()
		  ORDER BY a.Hora_data DESC
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $row = mysql_fetch_array( $res ) )
	{
		// Si el grupo no es de inventario entonces es un procedimiento
		if( strtolower( $row[ 'Gruinv' ] ) != 'on' )
		{
			//Si es procedimiento
			$pro = consultarProcedimientoPorCodigo( $conex, $wcliame, $row['Tcarprocod'] );
			
			$val[] = [
					'codigo' 		=> $pro[ 'codigo' ] ,
					'descripcion' 	=> $pro[ 'descripcion' ] ,
				];
		}
		else{
			// Si no es un procedimiento ( medicamentos e insumos) no se continúa buscando más procedimientos
			break;
		}
	}
	
	return $val;
}

function consultarMunicipio( $conex, $codigo ){
			
	$val = '';
	
	$sql = "SELECT *
			  FROM root_000006
			 WHERE codigo = '".$codigo."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array( $res ) ){
		$val = $row[ 'Nombre' ];
	}
	
	return $val;
}

function consultarDepartamento( $conex, $pais, $dep ){
	
	$val = '';
	
	$sql = "SELECT *
			  FROM root_000002
			 WHERE codigoPais = '".$pais."'
			   AND codigo = '".$dep."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array( $res ) ){
		$val = $row[ 'Descripcion' ];
	}
	
	return $val;
}
function consultarNombrePlan($conex,$wcliame,$codigo){
	
	
	

	$sql = "SELECT Seldes
			  FROM ".$wcliame."_000105 a
			 WHERE Seltip ='16'
			   AND SelCod = '".$codigo."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	 $row = mysql_fetch_array( $res);
	return  $row[0];

}	
function consultarPais( $conex, $codigo ){
	
	$val = '';
	
	$sql = "SELECT *
			  FROM root_000077
			 WHERE Paicod = '".$codigo."'
			   AND Paiest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array( $res ) ){
		$val = $row[ 'Painom' ];
	}
	
	return $val;
}

function consultarResponsable( $conex, $wcliame, $historia, $ingreso ){
	
	$val = [];
	
	$sql = "SELECT b.Empcod, b.Empnom , b.Empnit, b.Emptse,c.Temdes
			  FROM ".$wcliame."_000205 a, ".$wcliame."_000024 b ,".$wcliame."_000029 c 
			 WHERE a.reshis = '".$historia."' 
			   AND a.resing = '".$ingreso."' 
			   AND b.Emptem=c.Temcod
			   AND b.Empcod = resnit 
			   AND a.resord = 1
			   AND a.resest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array( $res ) ){
		$val['codigoResponsable'] 	= $row[ 'Empcod' ];
		$val['nombreResponsable']	= $row[ 'Empnom' ];
		$val['nitResponsable']		= $row[ 'Empnit' ];
		$val['tipoServicio']		= $row[ 'Emptse' ];
	    $val['tipoEmpresa']         = $row[ 'Temdes' ];
	}
	
	return $val;
}

function informacionMedico( $conex, $wbasedato, $wemp_pmla, $codigo ){

	$medico = new medicoDTO();

	$q = "SELECT Meddoc, Medtdo, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Meduma, Medesp, a.id  
			FROM ".$wbasedato."_000048 a
		   WHERE Medest='on'
			 AND Meduma = '".$codigo."'
		GROUP BY Meddoc, Medtdo";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		if( $info = mysql_fetch_array($res) ){

			$medico->tipoDocumento 		= $info['Medtdo'];
			$medico->numeroDocumento	= $info['Meddoc'];
			$medico->nombre1			= $info['Medno1'];
			$medico->nombre2 			= $info['Medno2'];
			$medico->apellido1 			= $info['Medap1'];
			$medico->apellido2 			= $info['Medap2'];
			$medico->registroMedico 	= $info['Medreg'];
			$medico->telefono 			= $info['Medtel'];
			$medico->codigoEspecialidad = $info['Medesp'];
			$medico->usuarioMatrix 		= $info['Meduma'];
			$medico->id 				= $info['id'];
		}
	}
	
	return $medico;
}
function informacionMedicoArray( $conex, $wbasedato, $wemp_pmla, $codigo ){

	

	$q = "SELECT Meddoc, Medtdo, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Meduma, Medesp, a.id  
			FROM ".$wbasedato."_000048 a
		   WHERE Medest='on'
			 AND Meduma = '".$codigo."'
		GROUP BY Meddoc, Medtdo";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
array();
	if ($num > 0)
	{
		if( $info = mysql_fetch_assoc($res) ){
				  $respuesta=array(
					"tipoDocumento"=> $info['Medtdo'],
					"numeroDocumento"	=> $info['Meddoc'],
					"nombre1" => $info['Medno1'],
					"nombre2" => $info['Medno2'],
					"apellido1" => $info['Medap1'],
					"apellido2" => $info['Medap2'],
					"registroMedico" => $info['Medreg'],
					"telefono" => utf8_encode($info['Medtel']),
					"codigoEspecialidad" => $info['Medesp'],
					"usuarioMatrix" => $info['Meduma'],
					"id" => $info['id'],
				 )  ;
			
		}
	}
	return $respuesta;

}


function informacionPaciente( $conex, $wemp_pmla, $historia, $ingreso ){
	
	$val = [];
	
	$whce 		= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
	$wmovhos 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	$wcliame 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
	
	
	$sql = "SELECT *
			  FROM ".$wcliame."_000100 a, ".$wcliame."_000101 b, root_000098 c
              WHERE pachis = '".$historia."'
			   AND Inghis = pachis
			   AND Ingnin = '".$ingreso."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	
	if( $row = mysql_fetch_array( $res) ){
		
		$ubicacionPaciente	= consultarUbicacionPaciente( $conex, $wmovhos, $historia, $ingreso );
		$responsable 		= consultarResponsable( $conex, $wcliame, $historia, $ingreso );
		
		$nombresCompletos = trim( $row['Pacno1']." ".$row['Pacno2'] );
		$apellidosCompletos = trim( $row['Pacap1']." ".$row['Pacap2'] );
		
		$val = [
				'historia'			=> $historia,
				'ingreso'			=> $ingreso,
				'nroDocumento'		=> trim( $row['Pacdoc'] ),
				'tipoDocumento'		=> $row['Pactdo'],
				'nombre1'			=> $row['Pacno1'],
				'nombre2' 			=> $row['Pacno2'],
				'apellido1' 		=> $row['Pacap1'],
				'apellido2' 		=> $row['Pacap2'],	
				'nombresCompletos' 	=> $nombresCompletos,
				'apellidosCompletos'=> $apellidosCompletos,
				'nombreCompleto' 	=> trim( $nombresCompletos." ".$apellidosCompletos ),
				'fechaNacimiento'	=> $row['Pacfna'],
				'genero'			=> $row['Pacsex'],
				'direccion' 		=> $row['Pacdir'],
				'codigoPais' 		=> $row['Pacpah'],
				'pais' 				=> consultarPais( $conex, $row['Pacpah'] ),
				'codigomMunicipio' 	=> $row['Paciu'],
				'municipio' 		=> consultarMunicipio( $conex, $row['Paciu'] ),
				'codigomDepartamento'=>$row['Pacdep'],
				'departamento'		=> consultarDepartamento( $conex, $row['Pacpah'], $row['Pacdep'] ),
				'habitacion' 		=> $ubicacionPaciente->habitacionActual,
				'servicioActual'	=> $ubicacionPaciente->servicioActual,
				'celular'			=> $row['Pacmov'],
				'correoElectronico'	=> $row['Paccor'],	//Octubre 21 de 2020
				'telefono'			=> $row['Pactel'],
				'codigoResponsable'	=> $responsable['codigoResponsable'],
				'nombreResponsable'	=> $responsable['nombreResponsable'],
				'nitResponsable'	=> $responsable['nitResponsable'],
				'tipoServicio'		=> $responsable['tipoServicio'],
				'tipoEmpresa'       => $responsable['tipoEmpresa'],
				'tarifa'			=> $row['Ingtar'],
				'codigoBarrio'		=> $row['Pacbar'],
				'raza' => $row['Petdes'],
				'tipoPlan' => $row['Pactaf']!= null? consultarNombrePlan($conex, $wcliame, $row['Pactaf']): consultarNombrePlan($conex, $wcliame, 3),
				'fechaAdmision'=>fechaAdmision($conex,$wemp_pmla,$historia,$ingreso),
			];
	}
	
	return $val;
}
function fechaAdmision($conex,$wemp_pmla,$his,$ing){
		$wcliame 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
	
	$sql = "SELECT a.Fecha_data, a.Hora_data
			  FROM ".$wcliame."_000101 a
			 WHERE Inghis = '".$his."'
			   AND Ingnin = '".$ing."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_array( $res);
	return date("Ymdhis", strtotime($row[0].$row[1]));
	


}
function consultarNombreCups( $conex,$codigo){
	 $sql = "SELECT a.Nombre
			  FROM root_000012 a
			 WHERE a.Codigo = '".$codigo."'
			    ";
	
	$res 	= mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	$rows = mysql_fetch_array( $res );
	return $rows[0];
}

function consultarEstudios( $conex, $whce,$wcliame, $tor, $nro,$paciente ){
	
	$val =[];
	
	//Consulto si existe cups ofertados por tipo de orden
	$sql = "SELECT b.Codigo,a.Detite,CONCAT(a.Fecha_data, a.Hora_data)as fechaOrden,a.Detjus,a.Detesi
			  FROM ".$whce."_000028 a, root_000012 b
			 WHERE Dettor = '".$tor."'
			   AND Detnro = '".$nro."'
			   AND Detcod = Codigo
			   AND b.Estado='ON'
			 ";
	
	$res 	= mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	
	while( $rows = mysql_fetch_array( $res ) ){
		$val[]=["cups"=>$rows[0],"nombreCups"=> consultarNombreCups($conex,$rows[0]),"numeroItemEnOrden"=>$rows[1], "fechaOrdenamiento"=>$rows[2],"numeroOrden"=>$nro,"tipoOrden"=>$tor,"justificacionEstudio"=>$rows[3],"estadoEstudio"=>$rows[4],"tarifaEstudio"=>consultarTarifaPorprocedimiento($conex,$wcliame,$rows[0],$paciente['tarifa'],$paciente['servicioActual'])];
	}
	return $val;
	
	
}

function consultarInteroperabilidades($conex,$wemp_pmla){
	
		$val=[];

	
		$val[]=consultarAliasPorAplicacion( $conex, $wemp_pmla, "interoperabilidadRis" );
		$val[]=consultarAliasPorAplicacion( $conex, $wemp_pmla, "interoperabilidadLis" );
	
	return $val;
	
	
}
function consultarTarifaPorprocedimiento($conex , $wcliame,$cups,$tarifa,$servicioActual){

	$sql = "SELECT Tarvac,Tarvan,Tarfec,Tarcco
				  FROM ".$wcliame."_000104 a
				 WHERE a.Tarcod = '".$cups."'
				   AND a.Tartar = '".$tarifa."'";
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			
			$retorno=0;//variable para controlar el retorno en caso de que existan mas de 1 registro
			while( $rows = mysql_fetch_array( $res ) ){
				if($rows[3]==$servicioActual && $retorno==0 ){//Si se encuentra el centro de costo se retorna la tarifa para dicho centro de costo
				if(date("Y-m-d")>= $rows[2]){
					return $rows[0];
					}else{
					 return $rows[1];
					}	
				$retorno=1;
			}elseif($rows[3]=="*" && $retorno==0){
				if(date("Y-m-d")>= $rows[2]){
					return $rows[0];
					}else{
					 return $rows[1];
					}	
			 $retorno=1;	
			}
			}
			
		
}
function consultarTipoOrdenenviarPoringreso($conex,$whce,$historia,$ingreso){
	$val=[];
	$sql = "SELECT DISTINCT a.Dettor
				  FROM ".$whce."_000027 d, ".$whce."_000028 a, ".$whce."_000047 b, root_000012 c
				 WHERE a.Detcod = b.codigo
				   AND a.Detenv = 'on'
				   AND a.Detest = 'on'
				   AND b.Estado = 'on'
				   AND b.Codcups= c.Codigo
				   AND d.Ordtor = a.Dettor
				   AND d.Ordnro = a.Detnro
				   AND d.Ordhis = '".$historia."'
				   AND d.Ording = '".$ingreso."'";
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			while( $rows = mysql_fetch_array( $res ) ){
				$val[]=$rows[0];
			}
			return $val;
}
function interoperabilidadCargosAutomaticos($conex, $wemp_pmla, $whce, $wmovhos, $worigen, $nroOrden, $item, $tor, $estGeneraCca) {
	
	$anulacion_cca = 'off';
	// Validamos si el estado en la movhos_45 es Eexcca=on y si el tipo de orden es facturable con la hce_15
	
	$sql_estado_orden = "SELECT Detcod, Detnro, B.id wordenid, Detite, Dettor, C.Eexcca Eexcca, C.Eexdes Eexdes, Tiptnf, Deticg, Ordhis, Ording
						   FROM ".$whce."_000028 B
						   JOIN ".$whce."_000027 A ON Ordtor = Dettor AND Ordnro = Detnro
						   JOIN ".$wmovhos."_000045 C ON C.Eexcod = Detesi
						   JOIN ".$whce."_000015 ON Dettor = Codigo
						  WHERE B.Detnro = '".$nroOrden."'
						    AND	B.Detite = '".$item."'
						    AND B.Dettor = '".$tor."'
						    AND B.Detest = 'on'";
	
	$res_estado_orden = mysql_query($sql_estado_orden, $conex);
	//$num_valid = mysql_num_rows($res_estado_orden );
	$datos = mysql_fetch_array($res_estado_orden);
	
	$eexcca = $datos['Eexcca'];
	$tiptnf = $datos['Tiptnf'];
	$wEstadoExamen = $datos['Eexdes'];
	
	//$sql_auditorio = "INSERT INTO cliame_000351 (Medico, Fecha_data, Hora_data, Descripcion, Proceso, Seguridad) VALUES ('cliame', CURRENT_DATE, CURRENT_TIME, 'interoperabilidadCargosAutomaticos $wemp_pmla, $whce, $wmovhos, $worigen, $nroOrden, $item, $tor, $estGeneraCca', 'interoperabilidad cca', 'C-root');";
	//mysql_query($sql_auditorio, $conex);
		
	if($estGeneraCca == 'off' && !empty($datos['Deticg'])) {
		$anulacion_cca = 'on';
	}
	
	if(($eexcca=='on' && $tiptnf<>'on') || $anulacion_cca == 'on') {
		
		//$sql_auditorio = "INSERT INTO cliame_000351 (Medico, Fecha_data, Hora_data, Descripcion, Proceso, Seguridad) VALUES ('cliame', CURRENT_DATE, CURRENT_TIME, 'Ingreso para generar cargos eexcca: $eexcca, tiptnf: $tiptnf, anulacion_cca: $anulacion_cca', 'interoperabilidad cca', 'C-root');";
		//mysql_query($sql_auditorio, $conex);
		
		$query_cup = "SELECT A.Codigo,B.Codcups AS Codigo_dos 
						FROM root_000012 A 
						JOIN ".$whce."_000047 B ON A.Codigo = B.Codcups
					   WHERE B.Estado = 'on' AND B.Codigo = '".$datos['Detcod']."'
					   UNION
					  SELECT A.Codigo,B.Codcups AS Codigo_dos 
						FROM root_000012 A 
						JOIN ".$whce."_000017 B ON A.Codigo = B.Codcups 
					   WHERE B.nuevo = 'on' AND B.Codigo = '".$datos['Detcod']."';";
		
		$res_cup = mysql_query($query_cup, $conex);
		$cup = mysql_fetch_array($res_cup);
		
		//validaciones de cargos en la tabla maestro de cargos automaticos
		include_once("../../cca/procesos/cargos_automaticos_funciones.php");			
		$tieneCCA = validarTieneCca($conex, $wemp_pmla, $cup['Codigo_dos'], "orden", $datos['Dettor']);
		
		if($tieneCCA || $anulacion_cca == 'on')	{		
			
			$ch = curl_init();
			$data = array( 
				'consultaAjax'			=> '',
				'accion'				=> 'guardar_cargo_automatico_orden',
				'movusu'				=> '',
				'whis' 					=> $datos['Ordhis'],
				'wing' 					=> $datos['Ording'],
				'wemp_pmla'				=> $wemp_pmla,
				'wprocedimiento'		=> $cup['Codigo_dos'],
				'worden'	        	=> $datos['Detnro'],
				'wdetcod'	    		=> $datos['Detcod'],
				'wite'	        		=> $datos['Detite'],
				'wdettor'	       		=> $datos['Dettor'],
				'worigen'	        	=> $worigen,
				'wcen_cos'				=> '',
				'wanulacion_cca'		=> $anulacion_cca,
				'wdeticg'				=> $datos['Deticg'],
				'wEstadoExamen'			=> $wEstadoExamen
			);
									
			$options = array(
				CURLOPT_URL 			=> "localhost/matrix/cca/procesos/ajax_cargos_automaticos.php",
				CURLOPT_HEADER 			=> false,
				CURLOPT_POSTFIELDS 		=> $data,
				CURLOPT_CUSTOMREQUEST 	=> 'POST',
			);

			$opts = curl_setopt_array($ch, $options);
			$exec = curl_exec($ch);
			curl_close($ch);
									
		}	
		
	}//hasta aca llega Ordenes cargos automaticos
}
 switch($_GET['accion']) {
    case 'consultarInteroperabilidades':
		$wemp_pmla=$_GET['wemp_pmla'];
        print_r(consultarInteroperabilidades($conex,$wemp_pmla));
        break;
  /*  case 'consultarTipoOrdenenviarPoringreso':
		$historia=$_GET['historia'];
	    $ingreso=$_GET['ingreso'];
		$wemp_pmla=$_GET['wemp_pmla'];
		$whce=consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
        print_r(consultarTipoOrdenenviarPoringreso($conex,$whce,$historia,$ingreso));
        break;*/
   
} 
	


