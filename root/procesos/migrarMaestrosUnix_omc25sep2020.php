<?php
include_once("conex.php");
/******************************************************************************************************************************
 * Actualizaciones:
 * 28-04-2020				Jerson T. 	Nuevo llamdo a la ejecucion de ETL_GRD.php (Ver) 
 * 27-04-2020 				Jerson T.	Nuevo llamado a la funcion copiaDeCxDelDia() para se ejecute cada 24 H
 * Octubre 23 de 2019.		Jessica		Se comenta en la ejecución de cada hora kron_diagnosticos.php ya que se mofificó HCE.php 
 *										para que los diagnósticos se actualicen desde HCE al firmar el formulario.
 * Julio 8 de 2019          Camilo ZZ.  Se agrega llamada a la función "kronConsumoArticuloDiario" para que se ejecute cada 24 horas.
 * Junio 18 de 2018.		Edwin MG.	Se agrega llamada al método articulosLactarioAArticulosEspecialesKardex la cuál agrega
 *										los articulos de lacatario a la tabla ARTICULOS ESPECIALES DEL KARDEX (movhos_000068)
 * Diciembre 5 de 2017.		Jessica		Se agrega en las ejecuciones de cada hora el include a kron_diagnosticos.php y el
 *										llamado a la función actualizarDiagnosticosPacientes()
 * Julio 17 de 2017.		Edwin MG.	El wemp_pmla depende de lo que se mande en la url, si no se manda el wemp_pmla es 01
 ******************************************************************************************************************************/

set_time_limit(2000);

include_once("root/comun.php");
// --> Libreria que contiene todas las funciones y rutinas relacionadas con el sistema unix
include_once("root/kron_maestro_unix.php");

if($hay_unix)
{


	$conex 		= obtenerConexionBD("matrix");
	if( empty($wemp_pmla) )
		$wemp_pmla 	= '01';

	$user_session 	= explode('-',$_SESSION['user']);
	$wuseLocal 		= $user_session[1];

	//---------------------------------------------------------------------------
	//	--> Esta funcion registra el log de las funciones ejecutadas, escribe en
	//		el archivo matrix/root/procesos/logMigrarMaestrosUnix.txt y tambien
	//		guarda la fecha de la ultima ejecucion en la tabla root_000118
	//---------------------------------------------------------------------------
	function guardarLog($log, $funcion)
	{
		global $tipoEscritura;
		global $conex;
		global $wuseLocal;

		$archivo	= fopen("logMigrarMaestrosUnix.txt", $tipoEscritura) or die("Problemas en la creacion del archivo logMigrarMaestrosUnix");
		$log 		= PHP_EOL.$log." - Fin:".date("Y-m-d-H:i:s");
		fputs($archivo, $log);
		fclose($archivo);

		// --> Registrar fecha de la ejecucion
		$sqlLog = "
		UPDATE root_000118
		   SET Logfue 		= '".date("Y-m-d")."',
			   Loghue 		= '".date("H:i:s")."',
			   Logdes		= '".$log."',
			   Seguridad	= 'C-".$wuseLocal."'
		 WHERE Logfun = '".$funcion."'
		";
		$resSubSer = mysql_query($sqlLog, $conex);

		if(mysql_affected_rows() == 0)
		{
			$sqlLog = "
			INSERT INTO root_000118
			   SET Medico 		= 'root',
				   Fecha_data 	= '".date("Y-m-d")."',
				   Hora_data 	= '".date("H:i:s")."',
				   Logfun 		= '".$funcion."',
				   Logdes		= '".$log."',
				   Logfue 		= '".date("Y-m-d")."',
				   Loghue 		= '".date("H:i:s")."',
				   Logema		= 'on',
				   Logest		= 'on',
				   Seguridad	= 'C-".$wuseLocal."'
			";
			$resSubSer = mysql_query($sqlLog, $conex);
		}
	}


	$ejCron = new datosDeUnix();

	// --> Guardar log de ejecuciones
	$tipoEscritura 	= ((date('d') == '01') ? 'w+' : 'a+');
	$archivo		= fopen("logMigrarMaestrosUnix.txt", $tipoEscritura) or die("Problemas en la creacion del archivo logMigrarMaestrosUnix");

	echo date("Y-m-d-H:i:s");

	// --> la variable $tiempoEjec, viene inicializada en la ruta definida por el cron programado en el servidor
	if(isset($tiempoEjec))
	{
		$tiempoEjec = trim($tiempoEjec);

		$log = PHP_EOL.PHP_EOL."-->tiempoEjec=".$tiempoEjec."".PHP_EOL."Inicio:".date("Y-m-d-H:i:s");
		fputs($archivo, $log);
		$log 			= "";

		switch($tiempoEjec)
		{
			// --> Aqui van todas las ejecuciones que se realizaran cada 24 horas
			case '24':
			{
				if( isset($migrarSoloEmpresas ) && $migrarSoloEmpresas == "on" ){
					//$ejCron->maestroEmpresa();
					$ejCron->maestroTarifas();
				}else{

					$ejCron->importarConceptosFacElec();
					
					//Junio 18 de 2016
					$log = "  > articulosLactarioAArticulosEspecialesKardex(movhos_000068): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->articulosLactarioAArticulosEspecialesKardex();
					guardarLog($log, "articulosLactarioAArticulosEspecialesKardex");

					$log = "  > maestroTarifas(cliame_000025): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroTarifas();					// --> cliame_000025
					guardarLog($log, "maestroTarifas");

					$log = "  > cco_conceptos(cliame_000077): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->cco_conceptos();					// --> cliame_000077
					guardarLog($log, "cco_conceptos");

					$log = "  > maestro_nits(cliame_000189): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestro_nits();					// --> cliame_000189
					guardarLog($log, "maestro_nits");

					$log = "  > maestro_tipos_empresa(cliame_000029): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestro_tipos_empresa();			// --> cliame_000029
					guardarLog($log, "maestro_tipos_empresa");

					$log = "  > maestro_bancos(cliame_000069): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestro_bancos();					// --> cliame_000069
					guardarLog($log, "maestro_bancos");

					$log = "  > maestro_terceros_no_oficial(cliame_000196): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestro_terceros_no_oficial();		// --> cliame_000196
					guardarLog($log, "maestro_terceros_no_oficial");

					$log = "  > maestroGruposArticulos(cliame_000004): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroGruposArticulos();			// --> cliame_000004
					guardarLog($log, "maestroGruposArticulos");

					$log = "  > maestro_medicamentos_empresas(cliame_000214): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestro_medicamentos_empresas();	// --> cliame_000214
					guardarLog($log, "maestro_medicamentos_empresas");

					$log = "  > tarifasmedicamentos(cliame_000026): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->tarifasmedicamentos(false);		// --> cliame_000026
					guardarLog($log, "tarifasmedicamentos");

					$log = "  > maestroDiagnosticosCie10(root_000011): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroDiagnosticosCie10();		// --> root_000011
					guardarLog($log, "maestroDiagnosticosCie10");

					$log = "  > migrarPacientesRechazados(cliame_000236, cliame_000237): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->migrarPacientesRechazados();		// --> cliame_000236, cliame_000237
					guardarLog($log, "migrarPacientesRechazados");

					$log = "  > crearPaquetesEnUnix(FAPAQ): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->crearPaquetesEnUnix();				// --> FAPAQ (UNIX)
					guardarLog($log, "crearPaquetesEnUnix");

					$log = "  > maestroExamenes(): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroExamenes();					// --> hce_000017, root_000012
					guardarLog($log, "maestroExamenes");

					$log = "  > maestroEmpresa(hce_000017, root_000012): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroEmpresa();					// --> cliame_000024
					guardarLog($log, "maestroEmpresa");

					$log = "  > maestroProveedores(cliame_000006): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroProveedores();				// --> cliame_000006
					guardarLog($log, "maestroProveedores");

					$log = "  > maestroTopesSoat(cliame_000194): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroTopesSoat();				// --> cliame_000194
					guardarLog($log, "maestroTopesSoat");

					$log = "  > maestroEstadosCartera(cliame_000279): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroEstadosCartera();			// --> cliame_000279
					guardarLog($log, "maestroEstadosCartera");

					$log = "  > maestroCausas(cliame_000276): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->maestroCausas();					// --> cliame_000276
					guardarLog($log, "maestroCausas");
					
					// --> 27-04-2020: Jerson Trujillo
					$ejCron->copiaDeCxDelDia();

					$log.= PHP_EOL."  > diferenciaMaterialesUnixMatrix(): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->diferenciaMaterialesUnixMatrix();


					$log = "  > kronConsumoArticuloDiario(): Inicio:".date("Y-m-d-H:i:s");
					$ejCron->kronConsumoArticuloDiario();					// --> cliame_000276
					guardarLog($log, "kronConsumoArticuloDiario");
					$log.= " - Fin:".date("Y-m-d-H:i:s");
					//--> ESTA FUNCIÓN SIEMPRE TIENE QUE IR DE ÚLTIMA PUESTO QUE AL EJECUTARSE LA CONEXIÓN A MYSQL RESULTANTE DEL CONEX,
					//    NO EXISTIRÁ, DEBIDO A LA TARDANZA DE LAS CONSULTAS A UNIX Y LAS NUEVAS POLITICAS DE PERSISTENCIA DE CONEXIONES.
				}
				break;
			}
			// --> Aqui van todas las ejecuciones que se realizaran cada hora
			case '1':
			{
				$ejCron->llenarTablaPacientesIngreso("A");
				$ejCron->llenarTablaPacientesIngreso("B"); //pacientes de inpac (activos)
				$ejCron->llenarTablaPacientesIngreso("C"); //accidentes de transito

				// // Actualizar los diagnósticos de los pacientes activos en movhos_000243
				// include_once("root/kron_diagnosticos.php");
				// actualizarDiagnosticosPacientes();

				if(date("H") == "08" || date("H") == "14")
					$ejCron->validarEjecucionDeCrons();
				
				
				// --> 2020-04-28: Llamado al extractor de datos para el cmbd (2 am). 
				//	Todos los dias se hace el llamado al script pero la ejecucion depende de que se haya 
				//	programado para el dia actual, segun  variable 'ejecucionCronETL_GRD' de root_51.
				
				if(date("H") == "02"){
					$llamarEtlGrd	= consultarAliasPorAplicacion($conex, '01', 'ejecutarCronETL_GRD');
					if($llamarEtlGrd == "on"){
						
						$ch 		= curl_init();					
						$data 		= array('consultaAjax' => '', 'accionPost' => 'ejecutarETL');
						$options 	= array(
										CURLOPT_URL 			=> 'localhost/matrix/procesos/procesos/ETL_GRD.php',
										CURLOPT_HEADER 			=> false,
										CURLOPT_POSTFIELDS 		=> $data,
										CURLOPT_CUSTOMREQUEST 	=> 'POST'
									);
									
						curl_setopt_array($ch, $options);
						curl_exec($ch);					
						if($errno = curl_errno($ch)) {
							$error_message = curl_strerror($errno);
							echo "Error en el curl ejecutando la accion 'ejecutarCronETL_GRD': ({$errno}):\n {$error_message}";
						}					
						curl_close($ch);
					}
				}
				break;
			}
			// --> Este se ejecuta cada 24 horas a las 4:00 am
			case 'TARIFAS':
			{

				$ejCron->borrarRegistrosImportadosDesdeUnix();
				$ejCron->insercionTarifasHomologacionDeFacturacion('P');
				$ejCron->insercionTarifasHomologacionDeFacturacion('E');
				$ejCron->insercionTarifasUvrGqxHabitacionesConceptos();
				//$ejCron->diferenciaMaterialesUnixMatrix();
				break;
			}
			case 'E':
			{
				$ejCron->insercionTarifasHomologacionDeFacturacion('E');
				break;
			}
			case 'P':
			{
				$ejCron->insercionTarifasHomologacionDeFacturacion('P');
				break;
			}
			case '5min':
			{
				$pasarCargosLaboratorio	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'pasarCargosLaboratorio');
				if($pasarCargosLaboratorio == 'on')
				{
					$ejCron->pasar_examenes_lab();
					include_once("ips/funciones_facturacionERP.php");
					grabacionCargosLaboratorio();
				}
				break;
			}
			case 'laboratorio':
			{
				$ejCron->pasar_examenes_lab();
				include_once("ips/funciones_facturacionERP.php");
				grabacionCargosLaboratorio();
				break;
			}
			case 'medicamentos':
			{
					$ejCron->diferenciaMaterialesUnixMatrix();
					break;
			}
			case 'tarifasMedicamentosDeldia':
			{
					$ejCron->tarifasmedicamentos(true);
					break;
			}
			// -->	Nueva acción "cronFacElectronica" para ejecutar las funciones de facturación electrónica a
			//		través del cron, cada 5 minutos.
			case 'cronFacElectronica':
			{
				// --> Estas son las empresas que tienen facturacion electronica hasta el momento 2019-05-16
				$arrEmpresas = array('01', '11', '07','02');
				foreach($arrEmpresas as $codEmp){

					$ejecutarCronWS	= consultarAliasPorAplicacion($conex, $codEmp, 'ejecutarCronFacturacionElec');

					if($ejecutarCronWS == "on"){
					
						include_once("ips/funcionesE-fac.php");
						echo '<br>-->EMPRESA:'.$codEmp;
						// --> Registrar documentos ya facturados
						echo "<br>Documentos Insertados:";
						insertarDocumentos(array($codEmp));

						// --> De los documentos registrados, generrales el archivo XML
						echo "<br>Documentos generados XML:";
						generarDocumentosXml(array($codEmp));

						// --> Enviar documentos al cen financiero via WS
						echo "<br>Documentos enviados CEN:";
						enviarDocumentosCenFinanciero(array($codEmp));
						
						// --> Validar estado aceptado
						echo "<br>Validar estado aceptado:";
						cronValidarEstadoAceptado($codEmp);
						
						// --> Descargar y obtener el cufe/cude
						echo "<br>Descargar y obtener CUFE/CUDE:";
						cronDescargarCufe($codEmp);
						
						/*if($codEmp == "11"){
							include_once("ips/funcionesE-facV1.php");
							echo '<br>-->EMPRESA:'.$codEmp;
							// --> Registrar documentos ya facturados
							echo "<br>Documentos Insertados:";
							insertarDocumentosV1(array($codEmp));

							// --> De los documentos registrados, generrales el archivo XML
							echo "<br>Documentos generados XML:";
							generarDocumentosXmlV1(array($codEmp));

							// --> Enviar documentos al cen financiero via WS
							echo "<br>Documentos enviados CEN:";
							enviarDocumentosCenFinancieroV1(array($codEmp));

							// --> Descargar documentos pdf del ws
							// echo "<br>Documentos descargados:";
							// cronDescargarPdf($codEmp);
						}*/
					}
				}
				break;
			}
		}
		$log.= PHP_EOL."Fin:".date("Y-m-d-H:i:s");
	}
	// --> 	Esta variable viene por url y contiene el nombre de una funcion a ejecutar, esto es para cuando
	//		se tenga la necesidad de ejecutar alguna rutina manualmente.
	if(isset($funcion))
	{
		$log.= PHP_EOL.PHP_EOL."-->funcion=".$funcion."".PHP_EOL."Inicio:".date("Y-m-d-H:i:s");

		$funcion = trim($funcion);
		$ejCron->$funcion();

		$log.= PHP_EOL."Fin:".date("Y-m-d-H:i:s");
	}

	echo '<br>'.date("Y-m-d-H:i:s");

	// --> Cerrar archivo de log
	fputs($archivo, $log);
	fclose($archivo);
}
?>