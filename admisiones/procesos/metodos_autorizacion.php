<?php
	/**
	 * * PROGRAMA					: AUTORIZACIONES PARA ACCEDER A LA HISTORIA
	 * * AUTOR						: Ing. Joel David Payares Hernández.
	 * * FECHA CREACION				: 13 de Mayo de 2021.
	 * * FECHA ULTIMA ACTUALIZACION	: 
	 * * DESCRIPCION				: 
	 */

	/**
	 * * Se inicializa el bufer de salida de php
	 */
	ob_start();

	/*
	 * Includes
	*/
	include_once("conex.php");
	
	/****************************************************************************
	* acciones
	*****************************************************************************/
	include_once("root/comun.php");
	include_once("root/erp_unix_egreso.php");
	include_once("hce/funcionesHCE.php");

	/**
	 * * Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include
	 */
	ob_end_clean();

	$user2 = explode( "-", $user );
    ( isset( $user2[1] ) ) ? $key = $user2[1] : $key = $user2[0];

	$conex = obtenerConexionBD("matrix");

	/**
	 * Metodo que permite consultar los datos de un paciente y sus respectivas personas autorizadas y que reclaman.
	 *
	 * @param [type]	$conex					Instancia global de la conexión a la base de datos.
	 * @param [type]	$wemp_pmla				Identificador de la empresa.
	 * @param [type]	$historia				Número de la historia asociada a un paciente.
	 * @param [Int]		$ingreso				Número del ingreso asociado a una historia de un paciente.
	 * @return array	Respuesta de petición
	 */
	function consultaDatosPaciente($conex = null, $wemp_pmla = null, $historia = null, $ingreso = null)
	{
		$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
		$movhos_basedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );

		$paciente = [];
		$rpt_success = "Consulta Satisfactoria.";
		$error_query = " - Error en el query - ";
		$error_peticion = "Error al procesar la petición.";

		if( !isset($conex) )
		{
			return [
				'state'			=>	404,
				'state_mgs'		=>	'error',
				'description'	=>	'Falta conexión a base de datos.'
			];
		}

		if( !isset($wbasedato) )
		{
			return [
				'state'			=>	404,
				'state_mgs'		=>	'error',
				'description'	=>	'Falta nombre de tabla, es olbigatorio.'
			];
		}
		
		if( !isset($wemp_pmla) )
		{
			return [
				'state'			=>	404,
				'state_mgs'		=>	'error',
				'description'	=>	'Falta el campo código de empresa, es obligatorio.'
			];
		}
		
		if( !isset($historia) )
		{
			return [
				'state'			=>	404,
				'state_mgs'		=>	'error',
				'description'	=>	'Faltan el campo historia, es obligatorio.'
			];
		}
	
		/** DATOS BÁSICOS DE PACIENTE **/
		$queryDatosPaciente = "
			  SELECT
						root_000037.Oritid AS TipoDocumento,
						root_000037.Oriced AS Documento,
						CONCAT_WS(
							' ',
							root_000036.Pacno1,
							root_000036.Pacno2,
							root_000036.Pacap1,
							root_000036.Pacap2
						) AS Nombre_Paciente,
						root_000036.Pacsex AS Sexo,
						root_000036.Pacnac AS FechaNacimiento,
						{$wbasedato}_000205.Resnit AS NitResponsable,
						{$wbasedato}_000205.Resnom AS NombreResponsable,
						{$wbasedato}_000101.Ingfei as FechaIngreso,
						{$wbasedato}_000101.Inghin as HoraIngreso,
						root_000037.Oriing as Ingreso
				FROM	root_000037,
						root_000036,
						{$wbasedato}_000205,
						{$wbasedato}_000101,
						{$movhos_basedato}_000018
			   WHERE	root_000037.Orihis = '{$historia}'
		";

		if ( isset($ingreso) ) {
			$queryDatosPaciente .= "
				 AND	root_000037.Oriing = '{$ingreso}'";

			$mensaje = "No existen registros para la historia {$historia} e ingreso {$ingreso}.";
		}
		else
		{
			$mensaje = "No existen registros para la historia {$historia}";
		}

		$queryDatosPaciente .= "
				 AND	root_000037.Oriced = root_000036.Pacced
				 AND	root_000037.Orihis = {$wbasedato}_000205.Reshis
				 AND	root_000037.Oriing = {$wbasedato}_000205.Resing
				 AND	root_000037.Orihis = {$wbasedato}_000101.Inghis
				 AND	root_000037.Oriing = {$wbasedato}_000101.Ingnin
				 AND	root_000037.Orihis = {$movhos_basedato}_000018.Ubihis
				 AND	root_000037.Oriing = {$movhos_basedato}_000018.Ubiing
	    	ORDER BY 	{$movhos_basedato}_000018.Ubiing ASC
		       LIMIT 	1";

		// print_r( $queryDatosPaciente );die();

		$resultado_query = mysqli_query_multiempresa($conex, $queryDatosPaciente) or $data['error'] = (mysqli_error($conex));

		if( mysqli_num_rows($resultado_query) == 0 ) {
			return [
				"state"			=>	200,
				"state_mgs"		=>	"exitosa-parcial",
				"description"	=>	$rpt_success,
				"data"			=>	$mensaje
			];
		}
		
		$paciente = mysqli_fetch_assoc( $resultado_query );
		$ingreso = $paciente['Ingreso'];
		$paciente['Edad'] = utf8_encode( calcularEdadPaciente( $paciente['FechaNacimiento'] ) );
		
		unset( $paciente['FechaNacimiento'] );
		
		$resultado['Paciente'] = $paciente;
		/** FIN DATOS BÁSICOS DE PACIENTE **/
		
		/** AUTORIZACIÓN **/
		$resultado['Autorizacion'] = consultarAutorizacion( $conex, $wemp_pmla, $historia, $ingreso  );
		/** FIN AUTORIZACIÓN **/
		
		/** PERSONAS AUTORIZADAS **/
		$resultado['Persona_Autorizada'] = consultarPersona( $conex, $wemp_pmla, $historia, $ingreso, '1' );
		/** FIN PERSONAS AUTORIZADAS **/
		
		/** PERSONAS QUE RECLAMAN **/
		$resultado['Persona_Reclaman'] = consultarPersona( $conex, $wemp_pmla, $historia, $ingreso, '2' );
		/** FIN PERSONAS QUE RECLAMAN **/

		return [
				"state"			=>	200,
				"state_mgs"		=>	"exitosa",
				"description"	=>	$rpt_success,
				"data"			=>	$resultado
			];
	}

	/**
	 * Metodo que permite insertar los datos de autorización y sus respectivas personas autorizadas y que reclaman.
	 *
	 * @param [type]	$conex					Instancia global de la conexión a la base de datos.
	 * @param [type]	$wemp_pmla				Identificador de la empresa.
	 * @param [type]	$datos					Datos de autorización y personas que reclaman o autorizadas.
	 * @return array	Respuesta de petición
	 */
	function guardarAutorizacion($conex, $wemp_pmla = null, $datos = null )
	{
		$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );

		$historia = $datos[0]['Authis'];
		$ingreso = $datos[0]['Auting'];

		/** AUTORIZACIÓN **/
		$datosEnc = crearArrayDatos( $wbasedato, "aut", "aut_", 3, $datos[1] );

		if( empty( consultarAutorizacion( $conex, $wemp_pmla, $historia, $ingreso  ) ) )
		{
			$datosEnc[ "authis" ] = $historia;
			$datosEnc[ "auting" ] = $ingreso;
			$datosEnc[ "autest" ] = 'on';

			$sqlInsert = crearStringInsert( $wbasedato."_000219", $datosEnc );
		}
		else
		{
			$set = '';

			foreach( $datosEnc as $key => $value )
			{
				$set .= ", {$key} = '{$value}'";
			}

			$set = substr( $set, 1 );

			$sqlInsert = "
				  UPDATE	{$wbasedato}_000219
					 SET 	{$set}
				   WHERE	Authis = '{$historia}'
					 AND	Auting = '{$ingreso}'
			";
		}

		mysqli_set_charset( $conex, 'utf8' );
		$resEnc = mysqli_query_multiempresa( $conex, $sqlInsert ) || ( $data[ 'mensaje' ] = utf8_encode( mysqli_errno( $conex )." - Error grabando en la tabla - ".mysqli_error( $conex ) ) );

		if ( !$resEnc ){
			return [
				'state'			=>	400,
				'state_mgs'		=>	'error',
				'description'	=>	$data[ 'mensaje' ]
			];
		}
		/** FIN AUTORIZACIÓN **/

		/** PERSONAS AUTORIZADAS **/
		foreach( $datos[2] as $persona_autorizada )
		{
			unset( $datosEnc ); //se borra el array
			
			//se guardan todos los servicios
			$datosEnc = crearArrayDatos( $wbasedato, "dau", "dau_", 3, $persona_autorizada );
			$datosEnc[ "dauhis" ] = $historia; //historia
			$datosEnc[ "dauing" ] = $ingreso; //ingreso

			if ( $datosEnc[ "daudoc" ] != "" )
			{
				$existe = consultarPersona( $conex, $wemp_pmla, null, null, null, $datosEnc[ "daudoc" ], $datosEnc[ "dautdo" ] );

				if( !$existe )
				{
					if( $datosEnc[ "daudoc" ] != "" ){
						$sqlInsert = crearStringInsert( $wbasedato."_000220", $datosEnc );

						$state_mgs = 'created';
						$description = 'Se han insertado todos los registros correctamente.';
					}				
				}
				else
				{
					unset( $datosEnc[ 'daudoc' ] ); //se borra el número de documento
					unset( $datosEnc[ 'dauhis' ] ); //se borra el número de historia
					unset( $datosEnc[ 'dauing' ] ); //se borra el número del ingreso 
					
					$id = $existe[0]['id'];
					$set = '';

					foreach( $datosEnc as $key => $value )
					{
						$set .= ", {$key} = '{$value}'";
					}

					$set = substr( $set, 1 );

					$sqlInsert = "
						UPDATE	{$wbasedato}_000220
							SET 	{$set}
						WHERE	id = {$id}
					";

					$state_mgs = 'updated';
					$description = 'Se han actualizado todos los registros correctamente.';
				}

				$resEnc = mysql_query( $sqlInsert, $conex ) || ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla de personas autorizadas - ".mysql_error() ) );
		
				if ( !$resEnc )
				{
					return [
						'state'			=>	400,
						'state_mgs'		=>	'error',
						'description'	=>	$data[ 'mensaje' ]
					];
				}
			}
		}
		/** FIN PERSONAS AUTORIZADAS **/

		/** PERSONAS QUE RECLAMAN **/
		foreach( $datos[3] as $persona_reclama )
		{
			unset( $datosEnc ); //se borra el array
			
			//se guardan todos los servicios
			$datosEnc = crearArrayDatos( $wbasedato, "dau", "dau_", 3, $persona_reclama );
			$datosEnc[ "dauhis" ] = $historia;	//historia
			$datosEnc[ "dauing" ] = $ingreso;	//ingreso
			$datosEnc[ "dautip" ] = 2;			//tipo usuario

			if ( $datosEnc[ "daudoc" ] != "" )
			{
				$existe = consultarPersona( $conex, $wemp_pmla, null, null, null, $datosEnc[ "daudoc" ], $datosEnc[ "dautdo" ] );

				if( !$existe )
				{
					if( $datosEnc[ "daudoc" ] != "" ){
						$sqlInsert = crearStringInsert( $wbasedato."_000220", $datosEnc );

						$state_mgs = 'created';
						$description = 'Se han insertado todos los registros correctamente.';
					}				
				}
				else
				{
					unset( $datosEnc[ 'daudoc' ] ); //se borra el número de documento
					unset( $datosEnc[ 'dauhis' ] ); //se borra el número de historia
					unset( $datosEnc[ 'dauing' ] ); //se borra el número del ingreso 
					
					$id = $existe[0]['id'];
					$set = '';

					foreach( $datosEnc as $key => $value )
					{
						$set .= ", {$key} = '{$value}'";
					}

					$set = substr( $set, 1 );

					$sqlInsert = "
						UPDATE	{$wbasedato}_000220
							SET 	{$set}
						WHERE	id = {$id}
					";

					$state_mgs = 'updated';
					$description = 'Se han actualizado todos los registros correctamente.';
				}

				$resEnc = mysql_query( $sqlInsert, $conex ) || ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error grabando en la tabla de personas autorizadas - ".mysql_error() ) );
		
				if ( !$resEnc )
				{
					return [
						'state'			=>	400,
						'state_mgs'		=>	'error',
						'description'	=>	$data[ 'mensaje' ]
					];
				}
			}
		}
		/** FIN PERSONAS QUE RECLAMAN **/

		/** LOG **/
		logEgreso( 'Autorización y personas autorizadas y que reclaman', $historia, $ingreso, "" );
		/** FIN LOG **/

		return [
			'state'			=>	201,
			'state_mgs'		=>	$state_mgs,
			'description'	=>	$description,
		];
	}

	/**
	 * * Crea un array de datos que hace los siguiente.
	 *
	 * * Toma todas las variables enviadas por Post, y las convierte en un array. Este array puede ser
	 * * procesado por las funciones crearStringInsert y crearStringInsert
	 *
	 * * Explicacion:
	 * * Toma todas las variables enviadas por Post que comiencen con $prefijoHtml, creando un array
	 * * donde su clave o posicion comiencen con $prefijoBD concatenado con $longitud de caracteres
	 * * despues del $prefijoHtml y dandole como valor el valor de la variable enviada por Post
	 * * ----------------------------------------------------------------------------------------------
	 * * Ejemplo:
	 *
	 * * La variable Post es: indpersonas = 'Armando Calle'
	 * * Ejecutando la funcion: $a = crearArrayDatos( 'movhos', 'Per', 'ind', 3 );
	 *
	 * * El array que retorna la función es:
	 * *                    $a[ 'Perper' ] = 'Armando Calle'
	 * *                    $a[ 'Medico' ] = 'movhos'
	 * *                    $a[ 'Fecha_data' ] = '2013-05-22'
	 * *                    $a[ 'Hora_data' ] = '05:30:24'
	 * *                    $a[ 'Seguridad' ] = 'C-movhos'
	 * * ----------------------------------------------------------------------------------------------
	 * @param [type] $wbasedato
	 * @param [type] $prefijoBD
	 * @param [type] $prefijoHtml
	 * @param [type] $longitud
	 * @param string $datos
	 * @return void	Array con contenido a insertar en base de datos
	 */
	function crearArrayDatos( $wbasedato, $prefijoBD, $prefijoHtml, $longitud, $datos = '' ){

		$val = Array();
		if( empty( $datos ) )
		{
			$datos = $_POST;
		}
			
		$crearDatosExtras = false;
		
		$lenHtml = strlen( $prefijoHtml );
		
		if ( is_array( $datos ) ) {
			foreach( $datos as $dato )
			{
				foreach( $dato as $keyPost => $valuePost )
				{
					if( substr( $keyPost, 0, $lenHtml ) == $prefijoHtml )
					{
						if( substr( $keyPost, $lenHtml, $longitud ) != 'id' )
						{
							$val[ $prefijoBD.substr( $keyPost, $lenHtml, $longitud ) ] = utf8_decode( $valuePost );
						}
						else
						{
							$val[ substr( $keyPost, $lenHtml, $longitud ) ] = utf8_decode( $valuePost );
						}

						$crearDatosExtras = true;
					}
				}
			}
		}
		else
		{
			foreach( $datos as $keyPost => $valuePost )
			{
				if( substr( $keyPost, 0, $lenHtml ) == $prefijoHtml )
				{
	
					if( substr( $keyPost, $lenHtml, $longitud ) != 'id' )
					{
						$val[ $prefijoBD.substr( $keyPost, $lenHtml, $longitud ) ] = utf8_decode( $valuePost );
					}
					else
					{
						$val[ substr( $keyPost, $lenHtml, $longitud ) ] = utf8_decode( $valuePost );
					}

					$crearDatosExtras = true;
				}
			}
		}

		//Estos campos se llenan automáticamente y toda tabla debe tener esots campos
		if( $crearDatosExtras ){
			global $user;
			$user2 = explode("-", $user);

			( isset($user2[1]) ) ? $user2 = $user2[1] : $user2 = $user2[0];

			if( $user2 == "" )
				$user2 = $wbasedato;

			$val[ 'Medico' ]		= $wbasedato;
			$val[ 'Fecha_data' ]	= date( "Y-m-d" );
			$val[ 'Hora_data' ]		= date( "H:i:s" );
			$val[ 'Seguridad' ]		= "C-$user2";
		}

		return $val;
	}

	/**
	 * * Inserta los datos a la tabla
	 *
	 * @param [String]	$tabla	[Nombre de la tabla a la que se va a insertar los datos]
	 * @param [Array]	$datos	[Array que tiene como clave el nombre del campo y valor el valor a insertar]
	 * @return String			[Sentencia sql a ejecutar]
	 */
	function crearStringInsert( $tabla, $datos )
	{
		$stPartInsert = "";
		$stPartValues = "";

		foreach( $datos as $keyDatos => $valueDatos ){
			$stPartInsert .= ",$keyDatos";
			$stPartValues .= ",'$valueDatos'";
		}

		$stPartInsert = "INSERT INTO $tabla(".substr( $stPartInsert, 1 ).")";   //quito la coma inicial
		$stPartValues = " VALUES (".substr( $stPartValues, 1 ).")";

		return $stPartInsert.$stPartValues;
	}

	/**
	 * Metodo para consultar si existe algún registro de autorización basado en la historia e ingreso.
	 *
	 * @param [type] $conex			Instancia global de la conexión a la base de datos.
	 * @param [type] $wemp_pmla		Identificador de la empresa.
	 * @param [type] $historia		Número de la historia asociada a un paciente.
	 * @param [type] $ingreso		Número del ingreso asociado a una historia de un paciente.
	 * @return array	Respuesta de petición
	 */
	function consultarAutorizacion ( $conex, $wemp_pmla, $historia, $ingreso )
	{
		$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );

		$data = [];

		$sqlser = "
			  SELECT	Autobs,
						Autinf
				FROM	{$wbasedato}_000219
			   WHERE	Authis = '{$historia}'
				 AND	Auting = '{$ingreso}'
				 AND Autest = 'on'
				";

			$resultado_query = mysqli_query_multiempresa($conex, $sqlser) or die( mysqli_error($conex) );

		if( mysqli_num_rows($resultado_query) == 0 ) {
			return $data = [];
		}
		
		$fila = mysqli_fetch_assoc( $resultado_query );

		$data['Autoriza'] = $fila['Autinf'];
		$data['Observacion'] = $fila['Autobs'];
		
		return $data;
	}

	/**
	 * Metodo para consultar si existen registros de personas autorizadas o que reclaman,
	 * basado en la historia e ingreso.
	 *
	 * @param [type] $conex			Instancia global de la conexión a la base de datos.
	 * @param [type] $wemp_pmla		Identificador de la empresa.
	 * @param [type] $historia		Número de la historia asociada a un paciente.
	 * @param [type] $ingreso		Número del ingreso asociado a una historia de un paciente.
	 * @return array	Respuesta de petición
	 */
	function consultarPersona( $conex, $wemp_pmla, $historia = null, $ingreso = null,
								$tipoUsuario = null, $documento = null, $tipoDocumento = null )
	{
		$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
		$data = [];

		if ( $documento == null ) {
			$sqlser = "
		      SELECT	Dauhis, Dauing, Dautdo, Daudoc, Daunom, Daupar
				FROM	{$wbasedato}_000220
		       WHERE	Dauhis = '{$historia}'
				 AND	Dauing = '{$ingreso}'
				 AND	Dautip = '{$tipoUsuario}'
				 AND	Dauest = 'on'
			";
		}
		else
		{
			$sqlser = "
			  SELECT	id
				FROM	{$wbasedato}_000220
			   WHERE	Daudoc = '{$documento}'
				 AND	Dautdo = '{$tipoDocumento}'
				 AND	Dauest = 'on'
				";
		}

		mysqli_set_charset($conex, 'utf8');
		$resultado_query = mysqli_query_multiempresa($conex, $sqlser) or die( mysqli_error($conex) );

		if( mysqli_num_rows($resultado_query) == 0 ) {
			return $data = false;
		}
		elseif( mysqli_num_rows($resultado_query) > 0 )
		{
			$i = 0;
			
			while( $fila = mysqli_fetch_assoc( $resultado_query ) )
			{
				$data[$i] = $fila;

				$i++;
			}
		}
		
		return $data;
	}

	/**
	 * Metodo que permite crar un select
	 *
	 * @return [Element]	Elemento select
	 */
	function crearSelect()
	{
		$resTiposDoc = consultaMaestros( 'root_000007', 'Codigo, Descripcion', $where="Estado='on'", '', '' );

		$select= "<SELECT id='$id' name='$name' $atributos $style>";
		$select.= "<option value=''>Seleccione...</option>";

		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			while( $rows = mysql_fetch_assoc( $res ) ){

				$value = "";
				$des = "";

				$i = 0;
				foreach( $rows  as $key => $val ){

						if( $i == 0 ){
								$value = $val;
						}
						else{
								$des .= "-".$val;
						}

						$i++;
				}

				$select.= "<option value='{$value}'>".substr( $des, 1 )."</option>";
			}
		}

		$select.= "</SELECT>";

		return $select;
	}

	/**
	 * * Registro log de egreso
	 *
	 * @param [String]	$des		Descripción de log
	 * @param [String]	$historia	Número de historia
	 * @param [String]	$ingreso	Número de ingreso
	 * @param [String]	$documento	Número de documento
	 * @return void
	 */
	function logEgreso( $des, $historia, $ingreso, $documento ){
		global $key;
		global $conex;
		global $wbasedato;
		
		$data = array( 'error'=> 0, 'mensaje'=>'', 'html'=>'' );
	
		$fecha = date("Y-m-d");
		$hora = (string) date("H:i:s");
	
		$sql = "INSERT INTO ".$wbasedato."_000185 (     medico     ,      fecha_data         ,       hora_data        ,        Logusu         ,         Logdes        ,            Loghis          ,           Loging          ,            Logdoc           , Logest, seguridad )
										   VALUES ('".$wbasedato."','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($key)."','".utf8_decode($des)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($documento)."',  'on' , 'C-root'  )";
	
		$res = mysql_query( $sql, $conex ) || ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de log egreso ".$wbasedato." 178 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
		if (!$res)
		{
			$data[ 'error' ] = 1; //sale el mensaje de error
		}
	
		return $data;
	}

	// Se valida si la acción fue enviada
	if( isset( $_REQUEST['accion'] ) )
	{
		switch( $_REQUEST['accion'] )
		{
			case 'consultarDatos':

				if( isset( $_POST['Authis'] ) && isset( $_POST['Auting'] ) )
				{
					$arrayRespuesta = consultaDatosPaciente($conex, $_GET['wemp_pmla'], $_POST['Authis'], $_POST['Auting'] );
				}
				else
				{
					return [
						'state'			=>	404,
						'description'	=>	'Array de datos sin contenido o nulo, válide por favor es obligatorio.'
					];
				}

				break;
			case 'guardarAutorizacion':

				if( isset( $_POST['Datos'] ) && is_array( $_POST['Datos'] ) )
				{
					$arrayRespuesta = guardarAutorizacion($conex, $_GET['wemp_pmla'], $_POST['Datos'] );
				}
				else
				{
					return [
						'state'			=>	404,
						'description'	=>	'Array de datos sin contenido o nulo y/o parámetros no válidos, válide por favor es obligatorio.'
					];
				}

				break;
			default:
				return [
					'state'			=>	404,
					'description'	=>	'Acción no válida, pro favor revíse la petición.'
				];
				break;
		}
	}

	// Respuesta codificada en Json
	echo json_encode( $arrayRespuesta, JSON_UNESCAPED_UNICODE );
