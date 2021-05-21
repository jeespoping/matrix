<?php

	/** ===========================================================================================|
	 * TODO: REPORTE DE ESTADOS DE FACTURAS DE PACEINTE
	 * ============================================================================================|
	 * * REPORTE					:	REPORTE DE ESTADOS DE FACTURAS DE PACEINTE
	 * * AUTOR						:	Ing. Joel David Payares Hernández.
	 * * FECHA CREACIÓN				:	2021-03-11
	 * * FECHA ULTIMA ACTUALIZACIÓN	:	2021-03-11
	 * * DESCRIPCIÓN				:	Obtiene el listado de facturas con los siguientes datos:
	 * * 									- Prefijo.
	 * * 									- Número de factura.
	 * * 									- Estado cartera.
	 * * 									- Código reponsable.
	 * * 									- Valor factura.
	 * ============================================================================================|
	 * TODO: ACTUALIZACIONES
	 * ============================================================================================|
	 * . @update [2021-05-04]	-	Se modifica la manera de obtener los datos de la consulta a unix
	 * . 							haciendo uso de la función o metodo [odbc_fetch_array], lo cual
	 * . 							permite recibir los datos de manera ordenada como array.
	 * . @update [2021-05-04]	-	Se realiza modificación al momento de recibir la petición, lo
	 * . 							permite validar el [METODO] de petición [POST ó GET].
	 * . @update [2021-05-05]	-	Se realiza modificación en sistema para recibir como parametro
	 * . 							un listado de pacientes a los cuales consultar sus estados de
	 * . 							radicados de facturas.
	 * . @update [2021-05-11]	-	Se realiza modificación en sistema para recibir como parametro
	 * . 							la acción por cualquiera de los metodos [GET - POST], al igual
	 * . 							que se agregó un metodo para convertir los keys a camelCase de
	 * . 							un array.
	*/

	/** Se inicializa el bufer de salida de php **/
	ob_start();

	/*
	 * Includes
	*/
	include_once("conex.php");
	include_once("root/comun.php");
	
	/** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
	ob_end_clean();

	$arrayRespuesta = [];

	/**
	 * * Este metodo permite obtener un array de el estado de factura del paciente.
	 *
	 * @param conex[object]				[Conexión a la base de datos]
	 * @param wemp_pmla[int]			[Código de empresa 01 = Clínica las Américas]
	 * @param documento_paciente[int]	[Número de identificación del paciente]
	 * @param tipo_documento[int]		[Tipo de documento del paciente]
	 * @param historia[int]				[Número de la historia médica del paciente]
	 * @param ingreso[int]				[Número de ingreso del paciente]
	 * @param codigo_responsable[int]	[Código de responsable]
	 *
	 * @return [array] Respuesta de base de datos
	*/
	function Consulta_Estado_Factura($conex = null, $wemp_pmla = null,
										$documento_paciente = null, $tipo_documento = null,
											$historia = null, $ingreso = null, $codigo_responsable = null)
	{
		$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
		conexionOdbc( $conex, $wmovhos, $conex_unix, 'facturacion' );
		
		$resultado_asociativo = array();
		$respuesta = array();
		$indices_asociativos = ["prefijo", "numeroFactura", "estadoCartera", "codigoResponsable", "valorFactura"];
		
		if( !isset($conex) )
		{
			return [
				'state'			=>	404,
				'description'	=>	'Falta conexión a base de datos.'
			];
		}
		
		if( !isset($wemp_pmla) )
		{
			return [
				'state'			=>	404,
				'description'	=>	'Falta el campo código de empresa, es obligatorio.'
			];
		}
		
		if( !isset($codigo_responsable) )
		{
			return [
				'state'			=>	404,
				'description'	=>	'Falta el campo código del responsable, es obligatorio.'
			];
		}
		
		if( !isset($historia) && !isset($ingreso) )
		{
			if( !isset($documento_paciente) ) {
				return [
					'state'			=>	404,
					'description'	=>	'El número de documento del paciente es obligatorio.'
				];
			}

			if( !isset($tipo_documento) ) {
				return [
					'state'			=>	404,
					'description'	=>	'Falta el campo tipo de documento, es obligatorio.'
				];
			}
			
			$query_historia_ingreso = "
			SELECT root_000037.Orihis AS Historia, root_000037.Oriing AS Ingreso
				FROM root_000037
				WHERE root_000037.Oriced = " . $documento_paciente . "
					AND root_000037.Oritid = '" . $tipo_documento . "'
					AND root_000037.Oriori = " . $wemp_pmla;
					
					$resultado_query = mysqli_query($conex, $query_historia_ingreso) or die(mysqli_error($conex));
			if( !$resultado_query || mysqli_num_rows($resultado_query) > 0 ) {
				
				while($fila = mysqli_fetch_array($resultado_query)) {
					$historia = $fila[0];
					$ingreso = $fila[1];
				}
			}
		}
		
		$respuesta = consultarEstadoFacturaPorHistoriaIngreso($conex_unix, $historia, $ingreso, $codigo_responsable);
		
		if( count($respuesta) == 0 ) {
			return [
				"state"			=>	200,
				"description"	=>	"Consulta Satisfactoria.",
				"data"			=>	"No existen registros para la historia '$historia' e ingreso '$ingreso'."
			];
		}

		return [
				'state'			=>	200,
				'description'	=>	'Consulta Satisfactoria.',
				'data'			=>	$respuesta
			];
	}

	/**
	 * * Este metodo permite obtener un array de los estados de las facturas
	 * * a partir de un listado/array de pacientes.
	 *
	 * @param conex[object]		[Conexión a la base de datos]
	 * @param wemp_pmla[int]	[Código de empresa 01 = Clínica las Américas]
	 * @param pacientes[int]	[Listado/Array de pacientes]
	 *
	 * @return [array] Respuesta de base de datos
	*/
	function Consulta_Estado_Factura_Array($conex = null, $wemp_pmla = null, $pacientes = null)
	{
		$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
		conexionOdbc( $conex, $wmovhos, $conex_unix, 'facturacion' );

		$array_respuesta = array();
		$respuesta = array();
		$historia = '';
		$ingreso = '';
		$codigo_responsable = '';

		if( !isset($conex) )
		{
			return [
				'state'			=>	404,
				'description'	=>	'Falta conexión a base de datos.'
			];
		}

		if( !isset($wemp_pmla) )
		{
			return [
				'state'			=>	404,
				'description'	=>	'Falta el campo código de empresa, es obligatorio.'
			];
		}

		foreach( $pacientes as $paciente )
		{
			$codigo_responsable = $paciente['responsable'];

			if( ($paciente['historia'] == '') && ($paciente['ingreso'] == '') )
			{
				if( !isset($paciente['documento']) ) {
					return [
						'state'			=>	404,
						'description'	=>	'El número de documento del paciente es obligatorio.'
					];
				}

				if( !isset($paciente['tipoDocumento']) ) {
					return [
						'state'			=>	404,
						'description'	=>	'Falta el campo tipo de documento, es obligatorio.'
					];
				}

				$query_historia_ingreso = "
					SELECT root_000037.Orihis AS Historia, root_000037.Oriing AS Ingreso
					FROM root_000037
					WHERE root_000037.Oriced = " . $paciente['documento'] . "
						AND root_000037.Oritid = '" . $paciente['tipoDocumento'] . "'
							AND root_000037.Oriori = " . $wemp_pmla;

				$resultado_query = mysqli_query($conex, $query_historia_ingreso) or die(mysqli_error($conex));
				if( !$resultado_query || mysqli_num_rows($resultado_query) > 0 ) {
					
					while($fila = mysqli_fetch_array($resultado_query)) {
						$historia = $fila[0];
						$ingreso = $fila[1];
					}
				}
			}
			else
			{
				$historia = $paciente['historia'];
				$ingreso = $paciente['ingreso'];
			}

			$respuesta = consultarEstadoFacturaPorHistoriaIngreso($conex_unix, $historia, $ingreso, $codigo_responsable);

			if( count($respuesta) == 0 ) {
				$respuesta = [
					"state"			=>	200,
					"description"	=>	"Consulta Satisfactoria.",
					"data"			=>	"No existen registros para la historia '$historia' e ingreso '$ingreso'."
				];
			}
			
			$paciente += ["estadoFactura" => $respuesta];

			array_push($array_respuesta, $paciente);
		}

		return [
				'state'			=>	200,
				'description'	=>	'Consulta Satisfactoria.',
				'data'			=>	$array_respuesta
			];
	}

	/**
	 * Este metodo permite obtener los estados de radicados de facturas
	 * recibiendo como parametro un número de documento o listado de
	 * números de documentos de los pacientes.
	 *
	 * @param conex_unix[object]		[Conexión a unix]
	 * @param historia[int]				[Número de historia del paciente]
	 * @param ingreso[int]				[Número de ingreso del paciente]
	 * @param codigo_responsable[int]	[Código de responsable]
	 *
	 * @return [array] Respuesta de base de datos
	*/
	function consultarEstadoFacturaPorHistoriaIngreso($conex_unix, $historia, $ingreso, $codigo_responsable = null)
	{
		$result = array();
		$sql = "
			SELECT carfca as prefijo,
				   carfac as numero_factura,
				   encest as estado_cartera,
				   carced as codigo_responsable,
				   carval as valor_factura
			  FROM cacar, caenc, famov
			 WHERE movhis = ".$historia."
			   AND movnum = ".$ingreso."
			   AND carfue = encfue
			   AND cardoc = encdoc
			   AND carfue = movfue
			   AND cardoc = movdoc";

		if( isset($codigo_responsable) )
		{
			$sql .= " AND carced = '".$codigo_responsable."'";
		}

		$respuesta = odbc_exec($conex_unix, $sql); 

		if( $respuesta )
		{
			while ($fila = odbc_fetch_array($respuesta)) {
				array_push($result, convertKeysToCamelCase( $fila ));
			}
		}
		else
		{
			array_push($result, "Error al ejecutar consulta.");
		}

		return $result;
	}

	/**
	 * Este metodo permite cambiar el formato de las keys que contienen guión
	 * bajo (underscore) un array a formato camel case (esEjemplo), y devuelve
	 * el mismo array con sus respectivas keys en camelCase.
	 *
	 * @param ResponseArray[Array]		[Array a convertir keys a camel case]
	 *
	 * @return [array] Array con keys en camelCase
	*/
	function convertKeysToCamelCase( $ResponseArray )
	{
		$keys = array_map(function ($i) use (&$ResponseArray) {
			if (is_array($ResponseArray[$i]))
				$ResponseArray[$i] = $this->convertKeysToCamelCase($ResponseArray[$i]);
	
			$parts = explode('_', $i);
			return array_shift($parts) . implode('', array_map('ucfirst', $parts));
		}, array_keys($ResponseArray));
	
		return array_combine($keys, $ResponseArray);
	}

	// Se valida si la acción fue enviada
	if( isset( $_REQUEST['accion'] ) )
	{
		switch( $_REQUEST['accion'] )
		{
			case 'estadoFactura':
				
				// Se valida el metodo de petición GET o POST
				switch( $_SERVER['REQUEST_METHOD'] )
				{
					case 'POST':
						if( isset( $pacientes ) && is_array( $pacientes ) && ( count( $pacientes ) > 0 ) )
						{
							$arrayRespuesta = Consulta_Estado_Factura_Array($conex, $_POST['wemp_pmla'], $_POST['pacientes']);
						}
						else
						{
							return [
								'state'			=>	404,
								'description'	=>	'Array de datos sin contenido o nulo, válide por favor es obligatorio.'
							];
						}
						break;
					case 'GET':
						$arrayRespuesta = Consulta_Estado_Factura($conex, $_GET['wemp_pmla'], $_GET['documento'], $_GET['tipoDocumento'], $_GET['historia'], $_GET['ingreso'], $_GET['responsable']);
						break;
				}

				break;
		}
	}
	
	// Respuesta codificada en Json
	echo json_encode( $arrayRespuesta );