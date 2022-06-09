<?php

	/** ===========================================================================================|
	 * TODO: REPORTE DE ESTADOS DE FACTURAS DE PACEINTE
	 * ============================================================================================|
	 * * REPORTE					:	REPORTE DE ESTADOS DE FACTURAS DE PACEINTE
	 * * AUTOR						:	Ing. Joel David Payares Hernández.
	 * * FECHA CREACIÓN				:	2021-03-11
	 * * FECHA ULTIMA ACTUALIZACIÓN	:	2021-07-08
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
	 * . @update [2021-07-08]	-	Se agregan metodos para obtener los estados de facturas y su
	 * . 							respectiva información, enviando como parametro un array de
	 * . 							de responsables o un array con datos adicionales como lo son
	 * . 							fecha de inicio y fecha de corte.
	 * . @update [2022-05-05]	-	Se cambia estructura de control dodne se construye la respuesta
	 * 								y se logra enviar toda la data completa sin perdidas.
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
											$historia = null, $ingreso = null, $fecha_inicio = null,
												$fecha_corte = null, $codigo_responsable = null)
	{
		$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
		conexionOdbc( $conex, $wmovhos, $conex_unix, 'facturacion' );

		$respuesta = array();
		$response = array();
		$respuesta_validacion = array();

		$estados_excluidos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "estadosExcepcionFactura" );
		$estados_excluidos_string = "'";
		$estados_excluidos_string .= implode("','", explode('-', $estados_excluidos));
		$estados_excluidos_string .= "'";

		if( !$conex_unix )
		{
			return [
				'state'			=>	400,
				'description'	=>	'Problemas de comunicación con Unix.',
				'data'			=>	[],
				'error_code'	=>	odbc_error($conex_unix),
				'error_msg'		=>	odbc_errormsg($conex_unix)
			];
		}

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
				  SELECT	root_000037.Orihis AS Historia, root_000037.Oriing AS Ingreso
					FROM	root_000037
				   WHERE	root_000037.Oriced = " . $documento_paciente . "
					 AND	root_000037.Oritid = '" . $tipo_documento . "'
					 AND	root_000037.Oriori = " . $wemp_pmla;
					
			$resultado_query = mysqli_query($conex, $query_historia_ingreso) or die(mysqli_error($conex));

			if( mysqli_num_rows($resultado_query) > 0 ) {
				
				while($fila = mysqli_fetch_array($resultado_query)) {
					$historia = $fila[0];
					$ingreso = $fila[1];
				}
			}
			else
			{
				$historia = null;
				$ingreso = null;
			}
		}
		
		// Se obtiene la información si este paciente es del PAF y si no se pasó responsble se obtiene este dato tambien
		$respuesta_validacion = validarResponsablePAF($historia, $ingreso, $codigo_responsable);

		// Se valida si es del PAF y se agrega a un array de pacientesPAF
		if( $respuesta_validacion['esPaf'] )
		{
			$codigo_responsable = $respuesta_validacion['responsable'];
			
			$respuesta = consultarEstadoFacturaPacientesPAF($conex_unix, $codigo_responsable, $fecha_inicio, $fecha_corte, $estados_excluidos_string);
		}
		else
		{
			$respuesta = consultarEstadoFacturaPorHistoriaIngreso($conex_unix, $historia, $ingreso, $codigo_responsable);
		}
		
		if( count($respuesta) == 0 ) {
			$response = [
				"state"			=>	200,
				"description"	=>	"Consulta Satisfactoria.",
				"data"			=>	"No existen registros para la historia '$historia' e ingreso '$ingreso'."
			];
		}
		else
		{
			$response = [
				'state'			=>	200,
				'description'	=>	'Consulta Satisfactoria.',
				'data'			=>	$respuesta
			];
		}

		return $response;
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
	function Consulta_Estado_Factura_Array($conex = null, $wemp_pmla = null, $pacientes = null, $fecha_inicio = null, $fecha_corte = null)
	{
		$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
		conexionOdbc( $conex, $wmovhos, $conex_unix, 'facturacion' );

		if( !$conex_unix )
		{
			return [
				'state'			=>	400,
				'description'	=>	'Problemas de comunicación con Unix.',
				'data'			=>	[],
				'error_code'	=>	odbc_error($conex_unix),
				'error_msg'		=>	odbc_errormsg($conex_unix)
			];
		}

		$array_respuesta = array();
		$respuesta = array();
		$historia = '';
		$ingreso = '';

		$estados_excluidos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "estadosExcepcionFactura" );
		$estados_excluidos_string = "'";
		$estados_excluidos_string .= implode("','", explode('-', $estados_excluidos));
		$estados_excluidos_string .= "'";

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
			// Se obtiene el responsable de los parámetros enviados, si no existes se setea en null
			$responsable = isset( $paciente['responsable'] ) && !empty( $paciente['responsable'] ) ? $paciente['responsable'] : null;
			
			// Se obtiene la información si este paciente es del PAF y si no se pasó responsble se obtiene este dato tambien
			$respuesta_validacion = validarResponsablePAF($paciente['historia'], $paciente['ingreso'], $responsable);

			// Se valida si es del PAF y se agrega a un array de pacientesPAF
			if( $respuesta_validacion['esPaf'] )
			{
				$paciente['responsable'] = $respuesta_validacion['responsable'];
				
				$respuesta = consultarEstadoFacturaPacientesPAF($conex_unix, $paciente['codigo_responsable'], $fecha_inicio, $fecha_corte, $estados_excluidos_string);

				if( count($respuesta) == 0 )
				{
					$respuesta = [
						'state'			=>	200,
						'description'	=>	"No existen registros para la historia {$historia} e ingreso {$ingreso}.",
						'data'			=>	[]
					];
				}
				
				$paciente += ["estadoFactura" => $respuesta];
	
				array_push($array_respuesta, $paciente);
			}

			$respuesta = consultarEstadoFacturaPorHistoriaIngreso($conex_unix, $paciente['historia'], $paciente['ingreso'], $paciente['responsable']);

			if( count($respuesta) == 0 ) {
				$respuesta = [
					'state'			=>	200,
					'description'	=>	"No existen registros para la historia {$paciente['historia']} e ingreso {$paciente['ingreso']}.",
					'data'			=>	[]
				];
			}
			
			$paciente += ["estadoFactura" => $respuesta];

			array_push($array_respuesta, $paciente);
		}

		return [
				'state'			=>	200,
				'description'	=>	"Consulta Satisfactoria.",
				'data'			=>	$array_respuesta
			];
	}

	function consultarHistoriaIngreso($conex, $wemp_pmla, $documento, $tipoDocumento)
	{
		$response = array();

		$query_historia_ingreso = "
				  SELECT	root_000037.Orihis AS Historia,
							root_000037.Oriing AS Ingreso
					FROM	root_000037
				   WHERE	root_000037.Oriced = " . $documento . "
					 AND	root_000037.Oritid = '" . $tipoDocumento . "'
					 AND	root_000037.Oriori = " . $wemp_pmla;

		$resultado_query = mysqli_query($conex, $query_historia_ingreso) or die(mysqli_error($conex));

		if( $resultado_query )
		{
			$result_array_response = odbc_fetch_array($respuesta);

			if( !empty( $result_array_response ) )
			{
				while($fila = mysqli_fetch_array($resultado_query)) {
					$response['historia'] = $fila[0];
					$response['ingreso'] = $fila[1];
				}
			}
			else
			{
				$response = [
					'state'			=>	404,
					'description'	=>	'No se ecnotraron datos para los parámetros enviados.',
					'data'			=>	[],
					'error_code'	=>	odbc_error($conex_unix),
					'error_msg'		=>	odbc_errormsg($conex_unix)
				];
			}
		}
		else
		{
			$response = [
				'state'			=>	400,
				'description'	=>	'Problemas de comunicación con Unix.',
				'data'			=>	[],
				'error_code'	=>	odbc_error($conex_unix),
				'error_msg'		=>	odbc_errormsg($conex_unix)
			];
		}

		return $response;
	}

	function consultarEstadoFacturaPacientesPAF($conex_unix, $codigo_responsable, $fecha_inicio, $fecha_corte, $estados_excluidos)
	{
		$sql = "
			  SELECT	cardethis as historia,
			  			cardetnum as ingreso,
						cardetvfa as valor_total,
						envencnit as responsable,
						envencest as estado
			  	FROM	caenvdet, caenvenc, facarfac, facardet
			   WHERE	envencnit = '{$codigo_responsable}'
			     AND	envencanu = '0'
				 AND	envencfec >= '{$fecha_inicio}'
				 AND	envencfec <= '{$fecha_corte}'
			     AND	envencfue = envdetfue
			     AND	envencdoc = envdetdoc
				 AND	envdetest NOT IN ({$estados_excluidos})
			     AND	carfacfue = envdetfan
			     AND	carfacdoc = envdetdan
			     AND	carfacanu = '0'
			     AND	cardetreg = carfacreg
			     AND	cardetanu = '0'
				 AND	cardetfac = 'S'
				 AND	cardettip = 'R'
			GROUP BY	1, 2
			 ";

		$respuesta = odbc_exec($conex_unix, $sql);
		
		if( $respuesta )
		{
			$result_array_response = odbc_fetch_array($respuesta);

			if( !empty( $result_array_response ) )
			{
				while ($fila = odbc_fetch_array($respuesta)) {
					$response['historia']		= $fila['historia'];
					$response['ingreso']		= $fila['ingreso'];
					$response['valor_total']	= $fila['valor_total'];
				}
			}
			else
			{
				$response = null;
			}
		}

		return $response;
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
			  SELECT
						carfca as prefijo,
						carfac as numero_factura,
						encest as estado_cartera,
						carced as codigo_responsable,
						carval as valor_factura
			  	FROM	cacar, caenc, famov
			   WHERE	movhis = '{$historia}'
			   	 AND	movnum = '{$ingreso}'
				 AND	movfuo = '01'
			     AND	carfue = encfue
			     AND	cardoc = encdoc
			     AND	carfue = movfue
			     AND	cardoc = movdoc";

		if( isset($codigo_responsable) )
		{
			$sql .= " AND carced = '".$codigo_responsable."'";
		}

		$respuesta = odbc_exec($conex_unix, $sql);

		if( is_resource($respuesta) )
		{
			while ($fila = odbc_fetch_array($respuesta)) {
				
				$fila['cuenta_cobro'] = consultarCuentaCobro($conex_unix, $fila['prefijo'], $fila['numero_factura']);

				array_push($result, convertKeysToCamelCase( $fila ));
			}
		}
		else
		{
			$result = null;
		}

		return $result;
	}

	/**
	 * Este metodo permite obtener las cuentas de cobros asociadas
	 * a una factura pasada por parametros.
	 *
	 * @param conex_unix[object]		[Conexión a unix]
	 * @param prefijo[String]			[Número de prefijo]
	 * @param numero_factura[String]	[Número de factura]
	 * @param codigo_responsable[int]	[Código de responsable]
	 *
	 * @return [array] Respuesta de base de datos
	*/
	function consultarCuentaCobro($conex_unix, $prefijo, $numero_factura)
	{
		$sql = "
			SELECT envdetdoc as cuenta_cobro
			  FROM caenvdet, caenvenc
			 WHERE envdetfan = '{$prefijo}'
			   AND envdetdan = '{$numero_factura}'
			   AND envencfue = envdetfue
			   AND envencdoc = envdetdoc";

		$respuesta = odbc_exec($conex_unix, $sql);
		
		if( is_resource($respuesta) )
		{
			while ($fila = odbc_fetch_array($respuesta))
			{
				if( isset( $fila['cuenta_cobro'] ) )
				{
					$response = $fila['cuenta_cobro'];
				}
			}
		}
		else
		{
			$response = null;
		}

		return $response;
	}

	/**
	 * Este metodo permite obtener las facturas asociadas a partir de una
	 * cuenta de cobro pasada como parametro.
	 *
	 * @param conex_unix[object]		[Conexión a unix]
	 * @param wemp_pmla[String]			[Número de empresa]
	 * @param cuentasCobro[Array]		[Array con cuentas de cobro
	 * 									a consultar]
	 *
	 * @return [array] Respuesta de base de datos
	*/
	function consultaFacturasCuentasCobroArray($conex, $wemp_pmla, $cuentas_cobro)
	{
		$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
		conexionOdbc( $conex, $wmovhos, $conex_unix, 'facturacion' );
		$facturas = [];
		$response = [];

		if( isset( $cuentas_cobro ) && is_array( $cuentas_cobro ) )
		{
			foreach( $cuentas_cobro as $key => $cuenta )
			{
				$response[ $key ]['cuentaCobro'] = $cuenta;

				$sql .= "
				  SELECT	DISTINCT envdetdan as factura,
							movhis as historia,
							movnum as ingreso,
							envdetnit as responsable
					FROM 	caenvdet, caenvenc, famov
				   WHERE	envdetdoc = '{$cuenta}'
					 AND    envdetfue =  = (
								SELECT fuecod
								FROM cafue
								WHERE fuetip = 'EV'
								GROUP BY fuecod
							)
					 AND	envdetdoc = envencden
					 AND	envdetfue = envencfen
					 AND	movfue = envdetfan
					 AND	movdoc = envdetdan
				";

				$respuesta = odbc_exec($conex_unix, $sql);
				$num_filas = odbc_num_rows($respuesta);

				$response[ $key ]['responsable'] = odbc_fetch_array($respuesta)['responsable'];

				if( $respuesta )
				{
					$result_array_response = odbc_fetch_array($respuesta);

					if( !empty( $result_array_response ) )
					{
						while ($fila = odbc_fetch_array($respuesta)) {
							unset( $fila['responsable'] );
							$facturas[] = $fila;
						}
					}
					else
					{
						$response = [
							'state'			=>	404,
							'description'	=>	'No se ecnotraron datos para los parámetros enviados.',
							'data'			=>	[],
							'error_code'	=>	odbc_error($conex_unix),
							'error_msg'		=>	odbc_errormsg($conex_unix)
						];
					}
				}
				else
				{
					return [
						'state'			=>	400,
						'description'	=>	'Problemas de comunicación con Unix.',
						'data'			=>	[],
						'error_code'	=>	odbc_error($conex_unix),
						'error_msg'		=>	odbc_errormsg($conex_unix)
					];
				}

				$response[ $key ][] = $facturas;
			}
		}
		else
		{
			$response = "El parametro enviado, no es un array.";
		}
		return $response;
	}

	/**
	 * Este metodo permite obtener las facturas asociadas a partir de un
	 * número de identificación de responsable.
	 *
	 * @param conex_unix[object]		[Conexión a unix]
	 * @param wemp_pmla[String]			[Número de empresa]
	 * @param cuentas[Array]		[Array con nit de responsables,
	 * 									fecha de inicio y fecha de corte]
	 *
	 * @return [array] Respuesta de base de datos
	*/
	function consultaFacturasResponsablesArray($conex, $wemp_pmla, $cuentas)
	{
		$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
		conexionOdbc( $conex, $wmovhos, $conex_unix, 'facturacion' );

		$factura = [];
		$response = [];
		$fechaInicio = $cuentas['fechaInicio'];
		$fechaCorte = $cuentas['fechaCorte'];
		$tipoPorceso = $cuentas['tipoProceso'];
		$historia = isset( $cuentas['historia'] ) ? $cuentas['historia'] : '';
		$ingreso = isset( $cuentas['ingreso'] ) ? $cuentas['ingreso'] : '';

		if ( !in_array( $tipoPorceso, [0, 1] ) ) {
			return "El tipo de proceso no es válido debe ser '0 <> RD' ó '1 = RD'";
		}

		$cuentas['responsables'] = str_replace('"', '', $cuentas['responsables']);

		$cuentaCobro = [];

		$sql = "
			  SELECT	envencdoc as cuenta_cobro,
						envdetdan as factura,
						fuecod as fuente_factura,
						fuecse as prefijo,
						envdetval as valor_factura,
						encest as estado_factura,
						movhis as historia,
						movnum as ingreso,
						envencnit as nit_responsable
				FROM	caenvenc, caenvdet, cafue, famov, caenc
			   WHERE	envencfue = (
							SELECT fuecod
							FROM cafue
							WHERE fuetip = 'EV'
							GROUP BY fuecod
						)
				 AND	envdetfan IN (
							SELECT fuecod
							FROM cafue
							WHERE fuetip = 'FA'
							GROUP BY fuecod
						)
				 AND	envencnit IN ( {$cuentas['responsables']} )
				 AND	envencfec >= '{$fechaInicio}'
				 AND	envencfec <= '{$fechaCorte}'
				 AND	envencfue = envdetfue
				 AND	envencdoc = envdetdoc
				 AND	envdetcco = fuecco
				 AND	envdetfan = fuefue
				 AND	movfue = envdetfan
				 AND	movdoc = envdetdan
				 AND	movfue = encfue
				 AND	movdoc = encdoc
		";

		if( intval($tipoPorceso) == intval(0) )
		{
			$sql .= "
					AND    movfue = encfue
					AND    movdoc = encdoc
					AND    encest <> 'RD'
				";
		}
		elseif( intval($tipoPorceso) == intval(1) )
		{
			$sql .= "
					AND    movfue = encfue
					AND    movdoc = encdoc
					AND    encest = 'RD'
				";
		}

		if( $historia != '' && $ingreso != '' )
		{
			$sql .= "
					AND    movhis = '{$historia}'
					AND    movnum = '{$ingreso}'
				";
		}
		
		$respuesta = odbc_exec($conex_unix, $sql);

		if (is_resource($respuesta))
		{
			$key = 0;

			while ($result[] = odbc_fetch_array($respuesta))
			{
				$factura = [
						'historia'			=>		$result[$key]['historia'],
						'ingreso'			=>		$result[$key]['ingreso'],
						'prefijo'			=>		$result[$key]['prefijo'],
						'fuente'			=>		$result[$key]['fuente_factura'],
						'factura'			=>		$result[$key]['factura'],
						'estado_factura'	=>		$result[$key]['estado_factura'],
						'valor_total'		=>		$result[$key]['valor_factura'],
						'nit_responsable'	=>		$result[$key]['nit_responsable']
					];

				$cuentaCobro[$result[$key]['cuenta_cobro']][] = (object) convertKeysToCamelCase($factura);
				$key++;
			}

			// Liberando result_set
			odbc_free_result($respuesta);

			$response = [
				'state'			=>	200,
				'description'	=>	'Consulta de datos existosa.',
				'data'			=>	(object) $cuentaCobro,
				'error_code'	=>	odbc_error($conex_unix),
				'error_msg'		=>	odbc_errormsg($conex_unix),
				'sql'			=> $sql
			];
		}
		else
		{
			$response = [
				'state'			=>	404,
				'description'	=>	'No se ecnotraron datos para los parámetros enviados.',
				'data'			=>	[],
				'error_code'	=>	odbc_error($conex_unix),
				'error_msg'		=>	odbc_errormsg($conex_unix)
			];
		}

		// Cerrando conexión Unix
		liberarConexionOdbc($conex_unix);

		return $response;
	}

	/**
	 * 
	 */
	function validarFacturaPAF($historia = null, $ingreso = null, $responsable = null)
	{
		$esPAF = false;
		$sql = "
			  SELECT	movcer
				FROM	facardet, facarfac, famov, cafue
			   WHERE	cardethis = '{$historia}'
				 AND	cardetnum = '{$ingreso}'
				 AND	cardetfac = 'S'
				 AND	cardetreg = carfacreg
				 AND	movdoc = carfacdoc
				 AND	movfue = fuecod
				 AND	fuetip = 'FA'
			";
		
		$respuesta = odbc_exec($conex_unix, $sql);

		if( $respuesta )
		{
			while ($fila = odbc_fetch_array($respuesta))
			{
				foreach( $responsablesPAF as $responsablePAF )
				{
					// Se validaria si el nit es de un PAF
					$esPAF = ($fila['movcer'] == $responsablePAF ? true : false);
				}
			}
		}

		return $esPAF;
	}

	function validarResponsablePAF( $historia = null, $ingreso = null, $responsable = null)
	{
		global $conex;
		$conex_unix = odbc_connect('facturacion','informix','sco');

		$respuesta = array();
		$responsablesPAF = responsablesPAF($conex, $_REQUEST['wemp_pmla']);

		if( $responsable == null )
		{
			$sql = "
			  SELECT	movcer
				FROM	facardet, facarfac, famov, cafue
			   WHERE	cardethis = '{$historia}'
				 AND	cardetnum = '{$ingreso}'
				 AND	cardetfac = 'S'
				 AND	cardetreg = carfacreg
				 AND	movdoc = carfacdoc
				 AND	movfue = fuecod
				 AND	fuetip = 'FA'
			";

			$response_sql = odbc_exec($conex_unix, $sql);

			if( $response_sql )
			{
				while ($fila = odbc_fetch_array($response_sql))
				{
					if( in_array($fila['movcer'], $responsablesPAF) )
					{
						$respuesta['responsable'] = $fila['movcer'];
						$respuesta['esPaf'] = true;
					}
					else
					{
						$respuesta['responsable'] = $fila['movcer'];
						$respuesta['esPaf'] = false;
					}
				}
			}
		}
		else
		{
			if( in_array($responsable, $responsablesPAF) )
			{
				$respuesta['responsable'] = $responsable;
				$respuesta['esPaf'] = true;
			}
			else
			{
				$respuesta['responsable'] = $responsable;
				$respuesta['esPaf'] = false;
			}
		}

		return $respuesta;
	}

	/**
	 * Metodo que permite obtener el listado de resposables del PAF
	 * 
	 * @param conex[object]		[Conexión a base de datos]
	 * @param wemp_pmla[String]	[Número de empresa]
	 * 
	 * @return [array] Respuesta de base de datos
	 */
	function responsablesPAF($conex, $wemp_pmla)
	{
		$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
		$responsables = [];
		$responsable = [];
		
		$query = "
		  SELECT	*
			FROM	{$wcliame}_000024
		   WHERE	Emppaf = 'on'
		     AND	Empest = 'on'
		";
					
		$result = mysqli_query($conex, $query) or die(mysqli_error($conex));
		$num_rows = mysqli_num_rows($result);

		if( $num_rows > 0 ) {
			while($fila = mysqli_fetch_array($result)) {
				$responsable = [
					'codigo'		=>		$fila['Empcod'],
					'nit'			=>		$fila['Empnit'],
					'resposnable'	=>		$fila['Empres'],
					'nombre'		=>		$fila['Empnom']
				];

				array_push( $responsables, $responsable );
			}
		}

		return $responsables;
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
			if ( is_array($ResponseArray[$i]) )
			{
				$ResponseArray[$i] = $this->convertKeysToCamelCase($ResponseArray[$i]);
			}
	
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
						if( isset( $_POST['pacientes'] ) && is_array( $_POST['pacientes'] ) && ( count( $_POST['pacientes'] ) > 0 ) )
						{
							if ( ( isset( $_POST['fecha_inicio'] ) && !empty( $_POST['fecha_inicio'] ) ) &&
							( isset( $_POST['fecha_corte'] ) && !empty( $_POST['fecha_corte'] ) ) &&
								( strtotime($_POST['fecha_corte']) >= strtotime($_POST['fecha_inicio']) ) )
							{
								$arrayRespuesta = Consulta_Estado_Factura_Array($conex, $_POST['wemp_pmla'], $_POST['pacientes'], $_POST['fecha_inicio'], $_POST['fecha_corte']);
							}
							else
							{
								$arrayRespuesta = [
									'state'			=>	401,
									'description'	=>	'Fechas vacias o fecha inicio mayor a fecha corte.'
								];
							}
						}
						else
						{
							$arrayRespuesta = [
								'state'			=>	404,
								'description'	=>	'Array de datos sin contenido o nulo, v&aacute;lide por favor es obligatorio.'
							];
						}
						break;
					case 'GET':
						$arrayRespuesta = Consulta_Estado_Factura($conex, $_GET['wemp_pmla'], $_GET['documento'], $_GET['tipoDocumento'], $_GET['historia'], $_GET['ingreso'], $_GET['responsable']);
						break;
					default:
						$arrayRespuesta = 'M&eacute;todo no identificado';
						break;
				}

				break;
			case 'facturasCuentaCobro':

				$arrayRespuesta = consultaFacturasCuentasCobroArray($conex, $_POST['wemp_pmla'], $_POST['cuentasCobro']);

				break;
			case 'facturasResponsables':
				if( !empty( $_POST['cuentas']['responsables'] ) )
				{
					if ( ( isset( $_POST['cuentas']['fechaInicio'] ) && !empty( $_POST['cuentas']['fechaInicio'] ) ) &&
							( isset( $_POST['cuentas']['fechaCorte'] ) && !empty( $_POST['cuentas']['fechaCorte'] ) ) &&
								( strtotime($_POST['cuentas']['fechaCorte']) >= strtotime($_POST['cuentas']['fechaInicio']) ) )
					{
						$arrayRespuesta = consultaFacturasResponsablesArray($conex, $_POST['wemp_pmla'], $_POST['cuentas']);
					}
					else
					{
						$arrayRespuesta = [
							'state'			=>	401,
							'description'	=>	'Fechas vacias o fecha inicio mayor a fecha corte.'
						];
					}
				}
				else
				{
					$arrayRespuesta = [
						'state'			=>	401,
						'description'	=>	'Falta de datos de responsables.'
					];
				}

				break;
			default:
				$arrayRespuesta = [
					'state'			=>	401,
					'description'	=>	'Acción no identificada.'
				];
				break;
		}
	}
	else
	{
		$arrayRespuesta = [
			'state'			=>	400,
			'description'	=>	'Acción no identificada.'
		];
	}

	// Respuesta codificada en Json
	echo json_encode( $arrayRespuesta );
