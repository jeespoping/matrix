<?php

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
function registrarDetalleLog( $conex, $wmovhos, $his, $ing, $tor, $nro, $ite, $clave, $msg ){
	
	$val = false;
	
	$fecha 	= date("Y-m-d");
	$hora 	= date("H:i:s");
	
	$sql = "INSERT INTO 
				".$wmovhos."_000273(     Medico    , Fecha_data  , Hora_data  ,   Loghis  , Loging    ,  Logtor   ,   Lognro  ,   Logite  ,    Logcla   ,              Logtxt              , Logest ,    Seguridad     ) 
							VALUES ( '".$wmovhos."', '".$fecha."', '".$hora."', '".$his."', '".$ing."', '".$tor."', '".$nro."', '".$ite."', '".$clave."', '".mysql_escape_string( $msg )."',  'on'  , 'C-".$wmovhos."' )
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
function registrarMsgLogHl7( $conex, $wmovhos, $his, $ing, $tdo, $ndo, $des, $tor, $nro, $ite, $msg ){
	
	$val = false;
	
	$fecha 	= date("Y-m-d");
	$hora 	= date("H:i:s");
	
	$sql = "INSERT INTO 
				".$wmovhos."_000270(     Medico    , Fecha_data  , Hora_data  , `Loghis`  ,  `Loging` , `Logtdo`  , `Logndo`  , `Logdes`  ,  Logfec     , Loghor     , Logtor    , Lognro    , Logite    ,           Logmsg                 , Logest ,   Seguridad      ) 
							VALUES ( '".$wmovhos."', '".$fecha."', '".$hora."', '".$his."', '".$ing."', '".$tdo."', '".$ndo."', '".$des."', '".$fecha."', '".$hora."', '".$tor."', '".$nro."', '".$ite."', '".mysql_escape_string( $msg )."',  'on'  , 'C-".$wmovhos."' )
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
	
	$sql = "SELECT b.Empcod, b.Empnom , b.Empnit 
			  FROM ".$wcliame."_000205 a, ".$wcliame."_000024 b 
			 WHERE a.reshis = '".$historia."' 
			   AND a.resing = '".$ingreso."' 
			   AND b.Empcod = resnit 
			   AND a.resord = 1
			   AND a.resest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array( $res ) ){
		$val['codigoResponsable'] 	= $row[ 'Empcod' ];
		$val['nombreResponsable']	= $row[ 'Empnom' ];
		$val['nitResponsable']		= $row[ 'Empnit' ];
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




function informacionPaciente( $conex, $wemp_pmla, $historia, $ingreso ){
	
	$val = [];
	
	$whce 		= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'hce' );
	$wmovhos 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
	$wcliame 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cliame' );
	
	$sql = "SELECT *
			  FROM ".$wcliame."_000100 a, ".$wcliame."_000101 b
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
				'correoElectronico'	=> $row['Paccre'],
				'telefono'			=> $row['Pactel'],
				'codigoResponsable'	=> $responsable['codigoResponsable'],
				'nombreResponsable'	=> $responsable['nombreResponsable'],
				'nitResponsable'	=> $responsable['nitResponsable'],
				'tarifa'			=> $row['Ingtar'],
				'codigoBarrio'		=> $row['Pacbar'],
			];
	}
	
	return $val;
}


