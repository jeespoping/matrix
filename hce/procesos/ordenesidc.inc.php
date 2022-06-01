<?php
include_once("conex.php");  header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<?php  header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<?php
//ORDENES PARA HCE a
//BSD
//Creado JUNIO 1 DE 2010
//Mauricio Sanchez Castaño

/************************************************************************************************************************
 * A tener en cuenta
 * 
 * Abrir el programa de ordenes desde la HCE sin que se vea la ventana de consultar de ordnes
 *  - Para abrir ordenes sin que se vea la pantalla de consultar ordenes, en la tabla 000009 de HCE, el link que se crea
 *    para el programa de ordenes debe tener &hce=on
 *  - Un medico no debe estar registrado con el centro de costos 1050 o 1051, ya que da el efecto de que no se guarda los articulos.
 *    Esto debido a que los articulos son guardados con cco * y su encabezado como 1051, por tanto no se vería los articulos al
 *    abrir el kardex
 *  - Para mostrar los componentes de un articulo generico, sus componentes deben estar en la tabla 000098 de mhosidc
 *  - Los consecutivos de la orden se encuentran en mhosidc_000011
 *  - El examen de la orden médica, para mensajes HL7 se guarda en tabla indicada segun hceidc_000017
 *	- Solo los examenes cuyo tipo tengan justificacion, indicado en la tabla 000015, campo reqjus, obliga a ingresar una 
 *	  justificacion para el examen.
 ************************************************************************************************************************/
/************************************************************************************************************************
 * Modificaciones:
 *  * Enero 22 de 2022  Marlon Osorio  
 * 									Se parametriza los centros de costos de Dispensacion Servicio Farmaceutico (1050) y 
 * 									Central de Mezclas (1051)
 * Octubre 25 de 2021   Daniel CB   Se realizan cambios en parametros quemados 
 * Agosto 13 de 2019	Edwin MG	Se castea valor en array ($arrAplicacion) como entero
 * Enero 29 de 2019		Edwin MG	En la función generarListaProtocolos se agrega filtro de estado en la consulta principal
 * Agosto 6 de 2018		Edwin MG	A la función consultarExamenesAnteriorHCE se comenta su contenido ya que esta función se llama
 *									desde ordenesidc.php más su respuesta no es usada
 * Febrero 18 de 2016	Edwin MG	Se corrige la impresion de CTC
 * Julio 21 de 2015		Edwin MG	En la función cargarProcedimientosTemporalADetalle se tiene en cuenta el estado al preguntar
 *									si existen datos en la temporal, antes solo miraba los procedimientos con estado on
 * Abril 10 de 2014		Edwin MG	Se maneja tabla temporal para los procedimientos
 * Abril 1 de 2014		Edwin MG	Solo se buscan los examenes homologados en ordenes de examenes
 *									Se impide buscar procedimientos no homologados
 * Marzo 18 de 2014		Edwin MG	Se modifica query en la función grabarExamenKardex para que tenga en cuenta el nro de item
 * Marzo 12 de 2014		Edwin MG	Se hacen cambios varios para las ordenes de procedimiento
 * Febrero 13 de 2014	Edwin MG	Se quita la (S) para las frecuencias corrientes
 * Febrero 3 de 2014 	Edwin MG	Se permite agregar examenes nuevos, los que serán homologados, con caracteres especiales
 * Enero 21 de 2014: 	Edwin MG	Se corrige consulta para los medicamentos internos, ya que para indicar si un medicamento
 *									era interno o no se estaba tomando el campo oncologico del maestro de artículos
 ************************************************************************************************************************/

/**********************************
 * INCLUDES
 **********************************/
include_once("root/comun.php");
include_once("root/magenta.php");

/**********************************
 * VARIABLES GLOBALES
 **********************************/
$grupoControl = "CTR";

$centroCostosServicioFarmaceutico = ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
$centroCostosCentralMezclas = ccoUnificadoCM(); //Se obtiene el Codigo de Central de Mezclas

$codigoServicioFarmaceutico = "SF";
$codigoCentralMezclas = "CM";

$descripcionServicioFarmaceutico = "Servicio farmacéutico";
$descripcionCentralMezclas = "Central de mezclas";
$descripcionOtroServicio = "Otro servicio";

$articuloInsulina = "I";

//Tipos de protocolo
$protocoloNormal = "N";
$nombreProtocoloNormal = "Normal";
$protocoloNutricion = "U";
$nombreProtocoloNutricion = "Nutricion";
$protocoloAnalgesia = "A";
$nombreProtocoloAnalgesia = "Analgesia";
$protocoloQuimioterapia = "Q";
$nombreProtocoloQuimioterapia = "Quimioterapia";

// Regleta con dosis por ronda para cada familia
$regletaFamilia = array();

	
//Nombre del esquema de hce
$esquemaBDHce = "hceidc";
$codigoAplicacion = "ordenes";
$codigoAyudaHospitalaria="H";

//Conexion base de datos
$conex = obtenerConexionBD("matrix");

//Consulta de la información del usuario
@$usuario = consultarUsuarioOrdenes($wuser);

/**********************************
 * PARAMETROS DE LA BASE DE DATOS *
 **********************************/

global $wemp_pmla;
$horaCorteDispensacion 	= consultarAliasPorAplicacion($conex,$wemp_pmla,"horaCorteDispensacion");
$inicioDiaDispensacion 	= consultarAliasPorAplicacion($conex,$wemp_pmla,"inicioDiaDispensacion");
$topePorcentualCtc 		= consultarAliasPorAplicacion($conex,$wemp_pmla,"topePorcentualCTC");

/***********************************
 * CLASES
 ***********************************/

class tiposAyudasDxHCEDTO{
	var $codigo = "";
	var $descripcion = "";
	var $arc_HL7 = "";
	var $programa = "";
	var $formato = "";
}

class UsuarioOrden{
	var $codigo;
	var $contrasena;
	var $descripcion;
	var $empresa;

	//Centro costos
	var $centroCostos;
	var $nombreCentroCostos;
	var $centroCostosHospitalario;
	var $centroCostosGrabacion;
	var $pestanasKardex;
	var $gruposMedicamentos;
	var $gruposMedicamentosQuery;
	var $esUsuarioCM;
	var $esUsuarioSF;
	var $esUsuarioCTC;
	var $esUsuarioLactario;

	//Campos HCE
	var $codigoRolHCE = "";
	var $nombreRolHCE = "";
	var $pestanasHCE = "";
	var $nombreEmpresaAgrupada = "";
	var $empresasAgrupadas = "";
	var $codigoEmpresaAgrupada = "";
	var $firmaElectronicamente = "";
	
	var $esCcoUrgencias = false;
	var $esCcoCirugia = false;
	var $esCcoIngreso = false;
	
	var $permisosPestanas = [];
	
	var $codigoEspecialidad = "";
}

class detalleKardexDTO {
	var $historia = "";
	var $ingreso = "";
	var $fecha = "";
	var $codigoArticulo = "";
	var $nombreArticulo = "";
	var $unidadDosis = "";				//Unidad de fraccion a aplicar
	var $cantidadDosis = "";			//Partes de la unidad de manejo
	var $periodicidad = "";
	var $condicionSuministro = "";
	var $formaFarmaceutica = "";
	var $diasTratamiento = "";
	var $estadoRegistro = "";
	var $estadoAdministracion = "";
	var $fechaInicioAdministracion = "";
	var $horaInicioAdministracion = "";
	var $fechaFinAdministracion = "";
	var $fechaKardex = "";
	var $horaKardex = "";
	var $via = "";
	var $origen = "";
	var $dosisMaxima = "";
	var $estaConfirmado = "";
	var $observaciones = "";
	var $suspendido = "";
	var $cantidadGrabar = "";
	var $cantidadUnidadManejo = "";
	var $unidadManejo = "";
	var $grupo = "";
	var $cantidadADispensar = "";
	var $cantidadDispensada = "";
	var $permiteModificar = "";
	var $permiteReemplazar = "";
	var $centroCostos = "";
	var $maximoUnidadManejo = "";
	var $unidadMaximoManejo = "";
	var $vencimiento = "";
	var $diasVencimiento = "";
	var $esDispensable = "";
	var $esDuplicable = "";
	var $tienePrioridad = "";
	var $estaAprobado = "";
	var $manejoInterno = "";

	var $imprimirArticulo = "";
	var $altaArticulo = "";
	var $cantidadAlta = "";
	var $posologia = "";
	var $unidadPosologia = "";
	
	var $campoAuxiliar = "";
	var $tipoProtocolo = "";
	var $diasTotalesTto = "";
	var $dosisTotalesTto = "";
	var $saldoDispensacion = "";
	var $viasPosibles = "";
	var $nombreProtocolo = "";

	var $fechaCreacion = "";		//Corresponde a fecha data de mhosidc_000053 o mhosidc_000060
	var $horaCreacion = "";			//Corresponde a fecha data de mhosidc_000053 o mhosidc_000060
	var $esPos = "";

	var $cantidadAutorizadaCtc = "";
	var $cantidadUtilizadaCtc = "";
	var $unidadesCantidadesCtc = "";
	
	var $estadoArticulo = "";
	
	var $codigoCreador = "";		//Codigo de usuario quien crea el articulo
	
	var $codigoFamilia = "";
	var $nombreFamilia = "";
	
	var $nombreGenerico = "";	//Agosto 27 de 2012
	
	var $idOriginal = "";

	/************************************************************************************
	 * Metodo que obtiene el codigo del articulo
	 * Creado: Diciembre 5 de 2011
	 ************************************************************************************/
	function consultarCodigoArticulo(){
		$datos = explode( "-", $this->codigoArticulo );
		
		return $datos[ 0 ];
	}
	
	/************************************************************************************
	 * Metodo que obtiene el nombre comercial del articulo
	 * Creado: Diciembre 5 de 2011
	 ************************************************************************************/
	function consultarNombreArticulo(){
		$datos = explode( "-", $this->codigoArticulo );
		
		return $datos[ 2 ];
	}
	
	function tieneCTC( $conex, $wbaseadato ){
	
		$val = false;
		
		if( !$this->esPos ){
			
			//Se consulta si un articulo tiene CTC
			$sql = "SELECT *
					  FROM ".$wbaseadato."_000134
					 WHERE ctchis = '".$this->historia."'
					   AND ctcing = '".$this->ingreso."'
					   AND ctcart = '".$this->consultarCodigoArticulo()."'
					   AND FIND_IN_SET( '".$this->idOriginal."' , ctcido ) > 0
					   AND ctcest = 'on'
					";
			
			$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );
			
			if( $rows = mysql_fetch_array( $res ) ){
				$val = true;
			}
		}
		
		return $val;
	}
}

class medicoDTO {
	var $tipoDocumento = "";
	var $numeroDocumento = "";
	var $nombre1 = "";
	var $nombre2 = "";
	var $apellido1 = "";
	var $apellido2 = "";
	var $telefono = "";
	var $registroMedico = "";
	var $interconsultante = "";
	var $tratante = "";
	var $codigoEspecialidad = "";
	var $usuarioMatrix = "";
	var $id = "";
}

class dietaKardexDTO {
	var $id = "";
	var $codigoDieta = "";
	var $descripcionDieta = "";
	var $historia = "";
	var $ingreso = "";
	var $fechaKardex = "";
	var $estado = "";
}

class cambioKardexDTO{
	var $historia = "";
	var $ingreso = "";
	var $fecha = "";
	var $hora = "";
	var $descripcion = "";
	var $mensaje = "";
	var $usuario = "";
}

class kardexDTO {
	var $historia = "";
	var $ingreso = "";
	var $fechaCreacion = "";
	var $fechaGrabacion = "";
	var $horaCreacion = "";
	var $observaciones = "";
	var $estado = "";
	var $rutaOrdenMedica = "";
	var $diagnostico = "";
	var $talla = "";
	var $peso = "";
	var $antecedentesAlergicos = "";
	var $cuidadosEnfermeria = "";
	var $terapiaRespiratoria = "";
	var $sondasCateteres = "";
	var $curaciones = "";
	var $interconsulta = "";
	var $consentimientos = "";
	var $medidasGenerales = "";
	var $preparacionAlta = "";
	var $confirmado = "";
	var $usuario = "";

	var $firmaDigital = "";
	var $obsDietas = "";
	var $mezclas = "";
	var $procedimientos = "";
	var $dextrometer = "";
	var $cirugiasPendientes = "";
	var $terapiaFisica = "";
	var $rehabilitacionCardiaca = "";
	var $antecedentesPersonales = "";
	var $aislamientos = "";

	var $esAnterior = "";
	var $editable = "";
	var $grabado = "";
	var $aprobado = "";
	var $esPrimerKardex = "";

	var $usuarioQueModifica = "";
	var $nombreUsuarioQueModifica = "";
	var $centroCostos = "";
	var $noAcumulaSaldoDispensacion = "";
	var $descontarDispensaciones = "";
	var $horaDescuentoDispensaciones = "";
}

class pacienteKardexDTO {
	var $historiaClinica = "";
	var $ingresoHistoriaClinica = "";
	var $documentoIdentidad = "";
	var $tipoDocumentoIdentidad = "";
	var $nombre1 = "";
	var $nombre2 = "";
	var $apellido1 = "";
	var $apellido2 = "";

	//Adicionales en UNIX
	var $fechaIngreso = "";
	var $horaIngreso = "";
	var $servicioActual = "";
	var $servicioAnterior = "";
	var $servicioAnteriorUrgencias = "";
	var $servicioAnteriorCirugia = "";
	var $habitacionActual = "";
	var $habitacionAnterior = "";
	var $numeroIdentificacionResponsable = "";
	var $nombreResponsable = "";
	var $genero = "";
	var $fechaNacimiento = "";
	var $deHospitalizacion = "";
	var $ultimoMvtoHospitalario = "";
	var $fechaHoraIngresoServicio = "";
	var $nombreServicioActual = "";

	var $altaProceso = "";
	var $altaDefinitiva = "";
	
	var $sexo = "";
}

class DietaDTO{
	var $codigo = "";
	var $descripcion = "";
	var $estado = "";
}

class RegistroGenericoDTO{
	var $codigo = "";
	var $descripcion = "";
	var $observacion = "";
	var $fecha = "";
	
	var $valDefecto = "";	//Junio 13 de 2012. Valor por defecto
}

class RegistroEstadoDet{
	var $estado = "";
}

class CentroCostosOrdenesDTO{
	var $codigo = "";
	var $nombre = "";
	var $consecutivoOrden = "";
}

class PeriodicidadDTO{
	var $codigo = "";
	var $descripcion = "";
	var $equivalencia = "";
}

class AuditoriaDTO{
	var $fechaRegistro = "";
	var $horaRegistro = "";
	var $historia = "";
	var $ingreso = "";
	var $fechaKardex = "";
	var $descripcion = "";
	var $mensaje = "";
	var $seguridad = "";

	//Anexo para reporte de cambios por tiempo
	var $servicio = "";
	var $confirmadoKardex = "";
	
	var $idOriginal = 0;
}

class MedicamentoHorarioDTO{
	var $servicio = "";
	var $habitacion = "";
	var $historia = "";
	var $fechaKardex = "";
	var $fechaInicioAdministracion = "";
	var $horaInicioAdministracion = "";
	var $horasFrecuencia = "";
	var $ingreso = "";
	var $paciente = "";
	var $codigoArticulo = "";
	var $seguridad = "";
	var $dosis = "";
	var $unidadDosis = "";
	var $horaConsulta = "";
	var $frecuencia = "";
	var $via = "";

	var $diasTratamiento = "";
	var $dosisMaximas = "";
	var $observaciones = "";
	var $cantidadADispensar = "";
	var $cantidadDispensada = "";
	var $condicion = "";
	var $estadoKardex = "";
}

class ReportesKardexDTO{
	var $historia = "";
	var $ingreso = "";
	var $paciente = "";
	var $servicio = "";
	var $habitacion = "";
	var $detalle = "";
	var $fecha = "";
}

class ExamenKardexDTO{
	var $codigoExamen = "";
	var $descripcionExamen = "";
	var $historia = "";
	var $ingreso = "";
	var $fecha = "";
	var $fechaDeSolicitado = "";
	var $estado = "";
	var $observaciones = "";
	var $justificacion = "";

	var $campoAuxiliar = "";
}

class ExamenHCEDTO{
	var $fecha = "";
	var $hora = "";
	var $historia = "";
	var $ingreso = "";
	var $tipoDeOrden = "";
	var $numeroDeOrden = "";
	var $observacionesOrden = "";
	var $estadoOrden = "";
	var $estadoRegistroOrden = "";
	var $firmaOrden = "";
	var $fechaARealizar = "";

	var $nombreCentroCostos = "";
	var $nombreExamen = "";

	var $codigoExamen = "";
	var $estadoExamen = "";
	var $resultadoExamen = "";
	var $estadoRegistroExamen = "";
	var $justificacion = "";
	var $protocoloPreparacion = "";
	var $nroItem = "";
	var $tipoEstudio = "";
	var $tipoAyudasDxs = "";
	
	var $tieneAnteriores = false;
	var $totalPendientes = 0;
	var $totalAnteriores = 0;
	
	var $creadorOrden = "";
	var $creadorItem = "";
	
	var $firmHCE = "";
	
	var $esPos = "";
}

class pestanaKardexDTO{
	var $codigoPestana = "";
	var $nombrePestana = "";
	var $estado = "";
	var $posicion = "";
	var $rutaScript = "";
	var $tablaGrabacion = "";
}

class IntervaloDextrometerDTO{
	var $minimo = "";
	var $maximo = "";
	var $dosis = "";
	var $unidadDosis = "";
	var $observaciones = "";
	var $via = "";
}

class ArticuloDTO{
	var $codigo = "";
	var $nombre = "";
	var $centroCostos = "";
	var $estado = "";
	var $frecuencia = "";
	var $codEsquema = "";
	var $tipo = "";
	var $via = "";
}

class PestanaDTO{
	var $nroPestana = "";
	var $nombrePestana = "";
	var $colAcciones = "";
}

class AccionPestanaDTO{
	var $nroPestana = "";
	var $codigoAccion = "";
	var $crear = "";
	var $leer = "";
	var $borrar = "";
	var $actualizar = "";
}

/***********************************
 * FUNCIONES
 ***********************************/
 
/********************************************************************************************************
 * Indica si existe un articulo para el kardex en un día en particular
 ********************************************************************************************************/
function existeArticuloEnKardex($conex, $wbasedato,$historia,$ingreso,$codigoArticulo,$fecha,$ido){

	$val = false;

	$q = "SELECT * 
	        FROM ".$wbasedato."_000054
		   WHERE Kadhis = '$historia'
			 AND Kading = '$ingreso'
			 AND Kadfec = '$fecha'
			 AND Kadart = '$codigoArticulo'
			 AND Kadido = '$ido'
		   UNION
		  SELECT * 
	        FROM ".$wbasedato."_000060
		   WHERE Kadhis = '$historia'
			 AND Kading = '$ingreso'
			 AND Kadfec = '$fecha'
			 AND Kadart = '$codigoArticulo'
			 AND Kadido = '$ido'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = true;
	}

	return $val;
}

/**
 * Devuelve true si un procedimiento tiene CTC
 */
function tieneCTCProcedimientos( $conex, $wbaseadato, $historia, $ingreso, $tipoOrden, $nroOrden, $item, $pro ){
	
	$val = false;
	
	//Se consulta si un articulo tiene CTC
	$sql = "SELECT *
			  FROM ".$wbaseadato."_000135
			 WHERE ctchis = '".$historia."'
			   AND ctcing = '".$ingreso."'
			   AND ctctor = '".$tipoOrden."'
			   AND ctcnro = '".$nroOrden."'
			   AND ctcite = '".$item."'
			   AND ctcpro = '".$pro."'
			   AND ctcest = 'on'
			";
	
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}

function actualizarImpMedicamentoHist($wbasedato,$estado,$historia,$ingreso,$codigoArticulo,$fecha,$ido){
	
	$conexion = obtenerConexionBD("matrix");

	$q = "UPDATE ".$wbasedato."_000054 SET
				Kadimp = '$estado'
			WHERE 
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadfec = '$fecha'
				AND Kadart = '$codigoArticulo'
				AND Kadido = '$ido'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	$estado = "1";

	liberarConexionBD($conexion);

	return $estado;
}
 
 
/**
 * Elimina la temporal de procedimientos al grabar ordenes
 */
function eliminarDatoTemporalProcedimiento( $wbasedato, $whce, $his, $ing ){

	global $conex;
	
	//Elimino todos los examenes sin firmar de la temporal
	//No se elimina del detalle por que en el detalle están todos los registros firmados
	$sql = "DELETE b FROM ".$whce."_000027 a, ".$wbasedato."_000159 b
			 WHERE a.ordhis = '".$his."'
			   AND a.ording = '".$ing."'
			   AND a.ordtor = dettor
			   AND a.ordnro = detnro
			   AND b.detfir = ''";
			   
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	// $num = mysql_num_rows( $res );
	
	
	//Elimino de la temporal de procedimientos aquellos procedimientos que no sean de la fecha actual de ordenes
	$sql = "DELETE b FROM ".$whce."_000027 a, ".$wbasedato."_000159 b
			 WHERE a.ordhis = '".$his."'
			   AND a.ording = '".$ing."'
			   AND ordfec != '".date( "Y-m-d" )."'
			   AND a.ordtor = dettor
			   AND a.ordnro = detnro
			   ";
			   
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	// $num = mysql_num_rows( $res );
	
	//Elimino los encabezados sin detalle de procedimientos
	$sql = "DELETE a FROM ".$whce."_000027 a
			 WHERE a.ordhis = '".$his."'
			   AND a.ording = '".$ing."'
			   AND a.ordtor NOT IN( 
						SELECT dettor
						  FROM ".$whce."_000028 b
						 WHERE ordtor = dettor
						   AND ordnro = detnro
						 UNION
						SELECT dettor
						  FROM ".$wbasedato."_000159  b
						 WHERE ordtor = dettor
						   AND ordnro = detnro
			   )";
			   
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	// $num = mysql_num_rows( $res );
	
	
	$q = "  DELETE b FROM ".$whce."_000027 a, ".$wbasedato."_000159 b, ".$whce."_000028 c
		     WHERE a.ordhis = '".$his."'
			   AND a.ording = '".$ing."'
			   AND ordtor = b.dettor
			   AND ordnro = b.detnro
			   AND c.dettor = b.dettor
			   AND c.detnro = b.detnro
			   AND c.detite = b.detite
			   ";
			   
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
}

/****************************************************************************************************
 * Carga todos los procedimientos de la temporal al Detalle de procedimientos
 ****************************************************************************************************/
function cargarProcedimientosTemporalADetalle( $his, $ing ){

	global $conex;
	global $whce;
	global $user;
	global $basedatos;
	
	//Se consulta si no hay examenes o procedimientos en la temporal
	$sql = "SELECT Dettor, Detnro, Detite
			  FROM ".$whce."_000027 a, ".$basedatos."_000159 b
			 WHERE ordtor = dettor
			   AND ordnro = detnro
			   AND ordhis = '$his'
			   AND ording = '$ing'";
			   
			   
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	while( $rows = mysql_fetch_array( $res ) ){
	
		cargarProcedimientosTemporalADetalleItem( $rows[ 'Dettor' ], $rows[ 'Detnro' ], $rows[ 'Detite' ] );
	}
}

/****************************************************************************************************
 * Carga todos los procedimientos de la temporal al Detalle de procedimientos
 ****************************************************************************************************/
function cargarProcedimientosTemporalADetalleItem( $tipoOrden, $nroOrden, $numeroItem ){
	
	global $conex;
	global $whce;
	global $user;
	global $basedatos;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0); 	
	
	//Se consulta si no hay examenes o procedimientos en la temporal
	$sql = "SELECT a.Medico
			  FROM ".$whce."_000027 a, ".$whce."_000028 b
			 WHERE ordtor = dettor
			   AND ordnro = detnro
			   AND dettor = '".$tipoOrden."'
			   AND detnro = '".$nroOrden."'
			   AND detite = '".$numeroItem."'";
			   
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if($num == 0){
		
		//Si no hay registros en la tabla de detalle de examenes se inserta
		$sql = "INSERT INTO ".$whce."_000028( Medico, Fecha_data, Hora_data, Dettor, Detnro, Detcod, Detesi, Detrdo, Detfec, Detjus, Detest, Detite, Detusu, Detfir, Deture, Detalt, Detimp, Detifh, Seguridad )
				SELECT '$whce' as Medico, b.Fecha_data, b.Hora_data, Dettor, Detnro, Detcod, Detesi, Detrdo, Detfec, Detjus, Detest, Detite, Detusu, Detfir, Deture, Detalt, Detimp, Detifh, 'C-$whce' as Seguridad
				  FROM ".$whce."_000027 a, ".$basedatos."_000159 b
				 WHERE a.ordtor = dettor
				   AND a.ordnro = detnro
				   AND dettor = '".$tipoOrden."'
				   AND detnro = '".$nroOrden."'
				   AND detite = '".$numeroItem."'
				   AND b.Detfir != ''";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
		
	}
	else{
		
		//Si no hay registros en la tabla de detalle de examenes se inserta
		$sql = "UPDATE ".$whce."_000028 a, ".$basedatos."_000159 b
				   SET a.Detjus = b.Detjus,
				       a.Detest = b.Detest,
					   a.Detfir = b.Detfir,
					   a.Detifh = b.Detifh
				 WHERE a.dettor = '".$tipoOrden."'
				   AND a.detnro = '".$nroOrden."'
				   AND a.detite = '".$numeroItem."'
				   AND a.dettor = b.dettor
				   AND a.detnro = b.detnro
				   AND a.detite = b.detite
				   AND b.Detfir != ''";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	}
	
	
	echo json_encode($datamensaje);
    return;
	
	
}

/************************************************************************************
 * Pasa los procedimientos de la tabla de detalle a la temporal
 ************************************************************************************/
function cargarProcedimientosDetalleATemporal( $conex, $wbasedato, $wmovhos, $his, $ing, $fecha ){

	//Elimino todos los examenes sin firmar de la temporal
	//No se elimina del detalle por que en el detalle están todos los registros firmados
	$sql = "DELETE b FROM ".$wbasedato."_000027 a, ".$wmovhos."_000159 b
			 WHERE a.ordhis = '".$his."'
			   AND a.ording = '".$ing."'
			   AND a.ordtor = dettor
			   AND a.ordnro = detnro
			   AND b.detfir = ''";
			   
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	// $num = mysql_num_rows( $res );
	
	
	//Elimino de la temporal de procedimientos aquellos procedimientos que no sean de la fecha actual de ordenes
	$sql = "DELETE b FROM ".$wbasedato."_000027 a, ".$wmovhos."_000159 b
			 WHERE a.ordhis = '".$his."'
			   AND a.ording = '".$ing."'
			   AND ordfec != '".date( "Y-m-d" )."'
			   AND a.ordtor = dettor
			   AND a.ordnro = detnro
			   ";
			   
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	// $num = mysql_num_rows( $res );
	
	//Elimino los encabezados sin detalle de procedimientos
	$sql = "DELETE a FROM ".$wbasedato."_000027 a
			 WHERE a.ordhis = '".$his."'
			   AND a.ording = '".$ing."'
			   AND a.ordtor NOT IN( 
						SELECT dettor
						  FROM ".$wbasedato."_000028 b
						 WHERE ordtor = dettor
						   AND ordnro = detnro
						 UNION
						SELECT dettor
						  FROM ".$wmovhos."_000159  b
						 WHERE ordtor = dettor
						   AND ordnro = detnro
			   )";
			   
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	// $num = mysql_num_rows( $res );
	
	
	//Se consulta si no hay examenes o procedimientos en la temporal
	$sql = "SELECT a.Medico
			  FROM ".$wbasedato."_000027 a, ".$wmovhos."_000159 b
			 WHERE a.ordhis = '".$his."'
			   AND a.ording = '".$ing."'
			   AND a.ordtor = dettor
			   AND a.ordnro = detnro";			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if($num == 0){
		
		//Si la temporal está vacía se llena la temporal
		$sql = "INSERT INTO ".$wmovhos."_000159( Medico, Fecha_data, Hora_data, Dettor, Detnro, Detcod, Detesi, Detrdo, Detfec, Detjus, Detest, Detite, Detusu, Detfir, Deture, Detalt, Detimp, Detifh, Seguridad )
				SELECT '$wmovhos' as Medico, b.Fecha_data, b.Hora_data, Dettor, Detnro, Detcod, Detesi, Detrdo, Detfec, Detjus, Detest, Detite, Detusu, Detfir, Deture, Detalt, Detimp, Detifh, 'C-$wmovhos' as Seguridad
				  FROM ".$wbasedato."_000027 a, ".$wbasedato."_000028 b
				 WHERE a.ordhis = '".$his."'
				   AND a.ording = '".$ing."'
				   AND a.ordtor = dettor
				   AND a.ordnro = detnro";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	}
}


/****************************************************************************************************************
 * Crea el encabezado del kardex por una llamada ajax
 ****************************************************************************************************************/
function crearEncabezadoKardexCerrar( $his, $ing, $firmaDigital ){

	global $conex;
	global $whce;
	global $basedatos;
	global $user;
	
	global $wbasedato;
	global $usuario;
	$wbasedato = $basedatos;
	
	$wfecha = date( "Y-m-d" );
	
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	
	@$usuario = consultarUsuarioOrdenes($wuser);
	
	$kardexGrabar = new kardexDTO();

	//Captura de parametros. Encabezado del kardex
	$kardexGrabar->historia = $his;
	$kardexGrabar->ingreso = $ing;
	$kardexGrabar->fechaCreacion = $wfecha;
	$kardexGrabar->horaCreacion = date("H:i:s");
	$kardexGrabar->fechaGrabacion = $wfecha;
	$kardexGrabar->usuario = $wuser;
	$kardexGrabar->confirmado = 'on';
	$kardexGrabar->esPrimerKardex = false;
	$kardexGrabar->centroCostos = $usuario->centroCostosGrabacion;			
	$kardexGrabar->usuarioQueModifica = $usuario->codigo;
	$kardexGrabar->firmaDigital = $firmaDigital;
	$kardexGrabar->noAcumulaSaldoDispensacion = false;
	
	if(!existeEncabezadoKardex($his,$ing,$wfecha)){
		crearKardex( $kardexGrabar, 'off' );	
		$mensaje = "El kardex ha sido creado con éxito";
	} else {
		//Actualiza SOLO encabezado
		@actualizarKardex($kardexGrabar,$vecPestanaGrabacion, 'off' );
		$mensaje = "El kardex ha sido actualizado con éxito";
	}
}

/************************************************************************************************
 * Consulta Dxs según el CIE 10
 ************************************************************************************************/
function consultarDxCie10( $imp ){

		global $conex;
		
		

		
		$val = ""; 

		//Diagnostico
		$sql = "SELECT Codigo, Descripcion
				FROM root_000011
				WHERE (Descripcion LIKE '%".utf8_decode($imp)."%' or Codigo like '%".utf8_decode($imp)."%')
				ORDER BY Descripcion
				";
				
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
				
		if( $num > 0 ){
			
			for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
					$rows[ 'Codigo' ] = trim( utf8_encode($rows[ 'Codigo' ]) );
					$rows[ 'Descripcion' ] = trim( utf8_encode($rows[ 'Descripcion' ] ) );
				
					//Creo el resultado como un json
					//Primero creo un array con los valores necesarios
					$data[ 'valor' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Descripcion' ] );	//Este es el dato a procesar en javascript
					$data[ 'usu' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
					$dat = Array();
					$dat[] = $data;
					
					$val .= json_encode( $dat )."\n";
				
			}
		}
		
		return $val;
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
 
 /**************************************************************************************************
 * Dice si un paciente tiene medicamentos activos para un dia
 **************************************************************************************************/
function tieneMedicamentosActivos( $conex, $wbasedato, $wcenmez, $historia, $ingreo, $cco, $fechaKardex ){

	$val = false;
	$conArts = cargarMedicamentosActivosAnterior( $conex, $wbasedato, $wcenmez, $historia, $ingreo, $cco, $fechaKardex );
	
	if( !empty($conArts) ){
		$val = true;
	}
	
	return $val;
}
 
/************************************************************************
 * Junio 15 de 2012
 *
 * Trae del día anterior medicamentos que quedaron activos el día 
 * anterior( No fueron suspendidos, para el día actual )
 ************************************************************************/
function cargarMedicamentosActivosAnterior( $conex, $wbasedato, $wcenmez, $historia, $ingreo, $cco, $fechaKardex )
{
	$val = "";

	//Consulto los medicamentos del día anterior
	$sql = "SELECT Artcod, Artcom, Kadori,Artgru,Kadffa,Artuni,Artpos,Kadufr,Kadcma,Defven,Defdie,Defdis,Defdup,Defdim,Defdom,Defvia,Kadpro,Kadess,Kadfin,Kadhin,Kadcfr,Kadper,Kadcnd,Kadcon,Kaddia,Kaddma,Kadvia 
			FROM 
				{$wbasedato}_000054 a, {$wbasedato}_000011 d, {$wbasedato}_000059 b, {$wbasedato}_000026 c
			WHERE
				kadhis = '$historia'
				AND kading = '$ingreo'
				AND kadfec = '$fechaKardex'
				AND kadcco = '$cco'
				AND kadsus != 'on'
				AND kadest = 'on'
				AND artcod = kadart
				AND artest = 'on'
				AND defart = artcod
				AND defest = 'on'
				AND ccocod = defcco
				AND ccoima != 'on'
			UNION
			SELECT Artcod, Artcom, Kadori,'' as Artgru,Kadffa,Artuni,'' as Artpos,Kadufr,Kadcma,Defven,Defdie,Defdis,Defdup,Defdim,Defdom,Defvia,Kadpro,Kadess,Kadfin,Kadhin,Kadcfr,Kadper,Kadcnd,Kadcon,Kaddia,Kaddma,Kadvia 
			FROM 
				{$wbasedato}_000054 a, {$wbasedato}_000011 d, {$wbasedato}_000059 b, {$wcenmez}_000002 c, {$wcenmez}_000001 e
			WHERE
				kadhis = '$historia'
				AND kading = '$ingreo'
				AND kadfec = '$fechaKardex'
				AND kadcco = '$cco'
				AND kadsus != 'on'
				AND kadest = 'on'
				AND artcod = kadart
				AND artest = 'on'
				AND defart = artcod
				AND defest = 'on'
				AND ccocod = defcco
				AND ccoima = 'on'
				AND arttip = tipcod
				AND tipcdo != 'on'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		for( $i = 0; $rs = mysql_fetch_array( $res ); $i++ ){
		
			$newFecHorIni = strtotime( date( "Y-m-d" )." ".$rs[ 'Kadhin' ] );
			
			//Si la fecha y hora de inicio es menor a la fecha actual, busco la siguiente hora par mas cercana
			if( time() > $newFecHorIni ){
				$horaPar = floor( date( "H", time()*2*3600 )/2 )*2*3600;
				
				$rs[ 'Kadfin' ] = date( "Y-m-d", time()+3600*2 );
				$rs[ 'Kadhin' ] = gmdate( "H:i:s", $horaPar );
			}
			else{
				$rs[ 'Kadfin' ] = date( "Y-m-d" );
			}
			
			//				0										1											2						3																4						5				6					7					8						9					10				11						12				13					14					15					16						17						18					19					20							21					22					23						24						25					26		
			$val .= "@".$rs['Artcod']."','".str_replace(" ","_",trim(htmlentities($rs['Artcom'])))."','".$rs['Kadori']."','".trim( substr( $rs['Artgru'], 0, strpos( $rs['Artgru'], '-' ) ) )."','".$rs['Kadffa']."','".$rs['Artuni']."','".$rs['Artpos']."','".$rs['Kadufr']."','".$rs['Kadcma']."','".$rs['Defven']."','".$rs['Defdie']."','".$rs['Defdis']."','".$rs['Defdup']."','".$rs['Defdim']."','".$rs['Defdom']."','".$rs['Defvia']."','".$rs[ 'Kadpro' ]."','".$rs[ 'Kadess' ]."','".$rs[ 'Kadfin' ]."','".$rs[ 'Kadhin' ]."','".$rs[ 'Kadcfr' ]."','".$rs[ 'Kadper' ]."','".$rs[ 'Kadcnd' ]."','".$rs[ 'Kadcon' ]."','".$rs[ 'Kaddia' ]."','".$rs[ 'Kaddma' ]."','".$rs[ 'Kadvia' ];
		}
		
		$val = substr( $val, 1 );
	}
	
	return $val;
}
 
/********************************************************************************************************************************************
 * Busca medicamento por codigo para la contingencia del kardex
 ********************************************************************************************************************************************/
function consultarMedicamentosPorCodigoContingencia($conex, $wbasedato,$codigo,$tipoMedicamento,$unidadMedida,$centroCostos,$gruposMedicamentos,$tipoProtocolo, $ccoPaciente = ''){

	$coleccion = array();
	$consulta = "";

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;
	global $wemp_pmla;
	

	$esSF = $centroCostos == $centroCostosServicioFarmaceutico ? true : false;
	$esCM = $centroCostos == $centroCostosCentralMezclas ? true : false;

	$codigo = str_replace("-","%",$codigo);
	
	$wcenpro = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	registrarFraccion( $conex, $wbasedato, $cenpro, $codigo, $centroCostosCentralMezclas, $wbasedato );	//Marzo 7 de 2011

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	$gruposIncluidos = "(";

	$q6 = "SELECT DISTINCT Ccogka FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccogka != '*' AND Ccocod='$centroCostos';";
	$res6 = mysql_query($q6, $conex);

	while($rs6 = mysql_fetch_array($res6)){
		$tieneGruposIncluidos = true;
		if(strpos($rs6['Ccogka'],$gruposIncluidos) === false){
			$gruposIncluidos .= "'".str_replace(",","','",$rs6['Ccogka'])."',";
		}
	}
	$gruposIncluidos .= "'')";
	//********************************

	//Preproceso de los grupos de medicamentos.  De formato X00,Y00,Z00... a 'X00','Y00','Z00'
	$criterioGrupo = "";

	$vecGruposMedicamentos = explode(",",$gruposMedicamentos);

	$cont2 = 0;
	while($cont2 < count($vecGruposMedicamentos)){
		$criterioGrupo .= "'".$vecGruposMedicamentos[$cont2]."',";
		$cont2++;
	}
	$criterioGrupo .= "''";

	switch ($tipoProtocolo) {
		case 'A';
		case 'U';
		case 'Q':
			//Para cualquier servicio
			$q = "SELECT "
			."		Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
			."	FROM "
			."		{$wbasedato}_000068, {$wbasedato}_000026, {$wbasedato}_000059, {$wbasedato}_000027 "
			."	WHERE "
			."		Arktip = '$tipoProtocolo' "
			."		AND Arkest = 'on' "
			."		AND Arkcco = '$centroCostosServicioFarmaceutico' "
			."		AND Arkcod = Artcod "
			."		AND Artuni LIKE '$unidadMedida' "
			."		AND Artest = 'on' "
			."		AND Artuni = Unicod "
			."		AND artcod LIKE '%".$codigo."%' "
			."		AND Defart = Arkcod "
			."		AND Defest = 'on' ";
					if($tieneGruposIncluidos){
						$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
					}
					$q .= " AND Defcco = Arkcco ";
					if($gruposMedicamentos != "*"){
						$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
					} else {
						$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
					}

				$subConsulta = "SELECT "
							."	Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
							." FROM "
							."	{$wbasedato}_000068, {$wcenpro}_000002, {$wbasedato}_000059, {$wbasedato}_000027 "
							." WHERE "
							."	artuni = unicod "
							."	AND Arktip = '$tipoProtocolo' "
							."	AND Arkest = 'on' "
							."	AND Defart = Arkcod "
							."	AND Arkcco = Defcco "
							."	AND Artuni = Unicod "
							."	AND artcod LIKE '%".$codigo."%' "
							."	AND Artuni LIKE '$unidadMedida' "
							."	AND Defest = 'on' "
							."	AND Defcco = '$centroCostosCentralMezclas' "
							."	AND artest = 'on' "
							."	AND Defart = Artcod";


				/****
				 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
				 */
				if($esCM){
					$q = $subConsulta;
				} else {
					if(!$tieneGruposIncluidos){
						$q = $q." UNION ".$subConsulta;
					}
				}
				$q = $q." LIMIT 100";
			break;
		case 'N':
			//Si el usuario no pertenece a SF ni a CM se consultan todos
			$q = "SELECT "
			."	Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
			." FROM "
			."	{$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
			." WHERE "
			."	artuni = unicod "
			."	AND artcod LIKE '%".$codigo."%' "
			."	AND Artuni LIKE '$unidadMedida' "
			."	AND artcod = Defart "
			."	AND artest = 'on' "
			."	AND Defest = 'on' ";
				if($tieneGruposIncluidos){
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
				}
//				$q .= " AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I' AND Arktip != 'T' AND Arktip != 'N') "
				$q .= " AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I' AND Arktip != 'LC' AND Arktip NOT IN (SELECT tiptpr FROM {$wcenpro}_000001 WHERE tiptpr != '' AND tiptpr != 'NO APLICA' GROUP BY tiptpr) AND Arktip != 'N') "	//Marzo 2 DE 2011
			."	AND Defcco = '$centroCostosServicioFarmaceutico' ";
			if($gruposMedicamentos != "*"){
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo))";
			} else {
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M')";
			}

			$subConsulta = "SELECT "
						   ."		Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
						   ."	FROM "
							."	{$wbasedato}_000027, {$wcenpro}_000002, {$wbasedato}_000059 "
							." WHERE "
							."	artuni = unicod "
							."	AND artcod LIKE '%".$codigo."%' "
							."	AND Artuni LIKE '$unidadMedida' "
							."	AND artcod = Defart "
							."	AND artest = 'on' "
//							."	AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I' AND Arktip != 'T' AND Arktip != 'N') "
							."	AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I' AND Arktip NOT IN (SELECT tiptpr FROM {$wcenpro}_000001 WHERE tiptpr != '' AND tiptpr != 'NO APLICA' GROUP BY tiptpr) AND Arktip != 'N') "	//Marzo 2 DE 2011
							."	AND Defest = 'on' "
							."	AND Defcco = '$centroCostosCentralMezclas'";

			/****
			 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
			 */
			if($esCM){
				$q = $subConsulta;
			} else {
				if(!$tieneGruposIncluidos){
					$q = $q." UNION ".$subConsulta;
				}
			}

			$q = $q." LIMIT 100";

			break;
		default:
			$q = "SELECT "
				." Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
			." FROM "
				." {$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
			." WHERE "
			."	artuni = unicod "
			."	AND artcod LIKE '%".$codigo."%' "
			."	AND Artuni LIKE '$unidadMedida' "
			."	AND artcod = Defart "
			."	AND artest = 'on' "
			."	AND Defest = 'on' ";
				if($tieneGruposIncluidos){
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
				}
				$q .= " AND Defcco = '$centroCostosServicioFarmaceutico' ";
			if($gruposMedicamentos != "*"){
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
			} else {
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
			}

			$subConsulta = "SELECT "
							."	Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
							." FROM "
								." {$wbasedato}_000027, {$wcenpro}_000002, {$wbasedato}_000059 "
							." WHERE "
							."  artuni = unicod "
							."	AND artcod LIKE '%".$codigo."%' "
							."	AND Artuni LIKE '$unidadMedida' "
							."	AND artcod = Defart "
							."	AND artest = 'on' "
							."	AND Defest = 'on' "
							."	AND Defcco = '$centroCostosCentralMezclas'";

			/****
			 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
			 */
			if($esCM){
				$q = $q." UNION ".$subConsulta;
			} else {
				if(!$tieneGruposIncluidos){
					$q = $q." UNION ".$subConsulta;
				}
			}
			$q = $q." LIMIT 100";
			break;
	}

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;

	if($num > 0){
		while ($cont1 < 1)
		{
			$puedeAgregar = true;

			$rs = mysql_fetch_array($res);
			$color = "red";

			$fftica = $rs['Artfar'];

			if($rs['origen'] == $codigoCentralMezclas){
				$color = "blue";
				$fftica = '00';
				if(!$esCM && !$esSF && substr(strtoupper($rs['Artcod']),0,1) == "J"){
					$puedeAgregar = false;
				}
			}

			if($puedeAgregar){
				
				$noEnviar = ( esStock( $conex, $wbasedato, $rs['Artcod'], $ccoPaciente ) == true )? 'on' : 'off';	//Abril 25 de 2011
				
				// $referencia = "javascript:seleccionarMedicamento('".$rs['Artcod']."','".str_replace(" ","_",trim(htmlentities($rs[1])))."','".$rs['origen']."','".str_replace(" ","_",trim($rs['Artgru']))."','".str_replace(" ","_",trim($fftica))."','".str_replace(" ","_",trim($rs['Artuni']))."','".str_replace(" ","_",trim($rs['Artpos']))."','".str_replace(" ","_",trim($rs['Deffru']))."','".str_replace(" ","_",trim($rs['Deffra']))."','".str_replace(" ","_",trim($rs['Defven']))."','".str_replace(" ","_",trim($rs['Defdie']))."','".str_replace(" ","_",trim($rs['Defdis']))."','".str_replace(" ","_",trim($rs['Defdup']))."','".str_replace(" ","_",trim($rs['Defdim']))."','".str_replace(" ","_",trim($rs['Defdom']))."','".str_replace(" ","_",trim($rs['Defvia']))."','".$tipoProtocolo."','".$noEnviar."');";
				// $consulta = $consulta."<font color=$color>*[".$rs['origen']."]<a href='#null' onclick=$referencia>".htmlentities($rs[1]).'</a></font><br/>';
				
							 //         0        ,     1      ,    2              ,        3          ,    4        ,      5            ,         6         ,         7         ,         8         ,          9        ,        11         ,     12            ,        13         ,     14            ,       15          ,      16           ,      17            ,      18     
							 //        Codigo    ,  NOmbre    , Origen            , Grupo             , FFA         , Unidad            ,  POS              , unidad fraccion   , Fraccion          , Vencimiento       , ??                , dispensable       , duplicable        ,    ??             ,     ??            ,    via            ,         Protocolo  , No enviar
				$referencia = "".$rs['Artcod']."','".$rs[1]."','".$rs['origen']."','".$rs['Artgru']."','".$fftica."','".$rs['Artuni']."','".$rs['Artpos']."','".$rs['Deffru']."','".$rs['Deffra']."','".$rs['Defven']."','".$rs['Defdie']."','".$rs['Defdis']."','".$rs['Defdup']."','".$rs['Defdim']."','".$rs['Defdom']."','".$rs['Defvia']."','".$tipoProtocolo."','".$noEnviar."";
				$consulta = $referencia;
			}
			
			//        Codigo    ,  NOmbre    , Origen            , Grupo             , FFA         , Unidad            ,  POS              , unidad fraccion   , Fraccion          , Vencimiento       , ??                , dispensable       , duplicable        ,    ??             ,     ??            ,    via            ,         Protocolo  , No enviar
			//"'".$rs['Artcod']."','".$rs[1]."','".$rs['origen']."','".$rs['Artgru']."','".$fftica."','".$rs['Artuni']."','".$rs['Artpos']."','".$rs['Deffru']."','".$rs['Deffra']."','".$rs['Defven']."','".$rs['Defdie']."','".$rs['Defdis']."','".$rs['Defdup']."','".$rs['Defdim']."','".$rs['Defdom']."','".$rs['Defvia']."','".$tipoProtocolo."','".$noEnviar."'
			
			$cont1++;
		}
	} else {
		$consulta = $consulta."";
	}

	//liberarConexionBD($conex);

	return $consulta;
}
 

/************************************************************************************************************************
 * Consulta cuanto es el total de articulos creados automaticamente por contigencia en el kardex
 ************************************************************************************************************************/
function consultarArticuloAutomatico( $conex, $wbasedato, $historia, $ingreso ){

    $val = false;

    $sql = "SELECT 
                count(*)
            FROM 
                {$wbasedato}_000054
            WHERE 
                kadhis = '$historia'
                AND kading = '$ingreso'
                AND Kadusu = '$wbasedato'
            ;";
                 
    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
    
    if( $rows = mysql_fetch_array($res) ){
        $val = $rows[ 0 ];
    }
    
    return $val;
}
 
/************************************************************
 * Indica si un centro de costos es de ingreso
 * 
 * @param $conexion
 * @param $wbasedato
 * @param $cco
 * @return unknown_type
 ************************************************************/
function estaUrgenciaCirugia( $conexion, $wbasedato, $historia, $ingreso ){

	$val = false;

	$sql = "SELECT
				Ccourg, Ccoing, Ccocir
			FROM
				{$wbasedato}_000018, {$wbasedato}_000011
			WHERE
				ubihis = '$historia'
				AND ubiing = '$ingreso'
				AND ccocod = ubisac
				AND ccoest = 'on'
				AND ccoing = 'on'
			";
				
	$res = mysql_query( $sql, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$rows = mysql_fetch_array( $res );
		
		if( $rows[ 'Ccocir' ] == 'on' || $rows[ 'Ccourg' ] == 'on' ){
			$val = true;
		}
	}
	
	return $val;
}
 
/******************************************************************************************
 * Para un paciente que se encuentran en urgencia o cirugia, recalcula el kardex
 ******************************************************************************************/
function recalcularKardex( $conex, $wbasedato, $historia, $ingreso, $fecha ){

	$val = false;

	//Busco todos los articulos que tiene el paciente y que se encuentre en urgencias
	$sql = "SELECT
				a.Fecha_data as encFecha_data, a.Fecha_data as encHora_data, a.id as encId, b.*
			FROM
				{$wbasedato}_000053 a, {$wbasedato}_000054 b
			WHERE
				karhis = '$historia'
				AND karing = '$ingreso'
				AND a.fecha_data = '$fecha'
				AND karrca = 'on'
				AND karest = 'on'
				AND kadhis = karhis
				AND kading = karing
				AND kadfec = a.fecha_data
				AND karcco = kadcco
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	$hrTraslado = '';
	
	//Si encuentro registros
	if( $num > 0 ){
	
		//Busco a que horas el paciente fue traslado a piso desde urgencia
		$horaTraslado = consultarHoraTrasladoUrgencias( $conex, $wbasedato, $historia, $ingreso, $fecha );
		
		//Si hora traslado es diferente a falso, significa que si lo han recibido en piso
		if( $horaTraslado ){
		
			//Recorro las filas del articulo
			for( $i = 0; $info = mysql_fetch_array( $res ); $i++ ){
				
				// //Consulto la frecuencia del medicamento
				$horasFrecuencia = consultarFrecuencia( $conex, $wbasedato, $info['Kadper'] );
				
				// echo "<br>fecha de inicio despues del traslado 2: {$info['Kadart']} : ".date( "Y-m-d H:i:s", $fhInicioTraslado );
				
				//Inicializo variables
				$cantGrabar = $saldo = $cantDispensar = $cantidadManejo = '';

				//Calculo de los dias de tratamiento actuales, Sumo 1 dia por el registro del dia actual que esta en temporal
				$diasTtoAcumulados = 0;
				$dosisTtoTotales = 0;
				obtenerDatosAdicionalesArticulo( $historia, $ingreso, $info['Kadart'], $diasTtoAcumulados, $dosisTtoTotales );
				
				//Calculo los datos necesarios
				$horasAplicacionDia = "";
				calcularSaldoActual( $conex,$wbasedato,$info['Kadhis'],$info['Kading'],$fecha,$info['Kadart'],$info['Kadfin'],$info['Kadhin'],$info['Kadcfr'],$horasFrecuencia,$info['Kaddma'],$info['Kadori'],$diasTtoAcumulados+1,$cantGrabar,$saldo,$cantDispensar,$cantidadManejo,$info['Kadsad'],$info['Kadpro'],$horasAplicacionDia, $info['Kaddia'], $info['Kadreg'] );
				
				//Consulto el cco al que pertenece el articulo
				if( strtoupper( $info['Kadori'] ) == "SF" ){
				
					$sqlCcoTras = "SELECT
								     Ccocod
								   FROM
										{$wbasedato}_000011
								   WHERE
										ccoest = 'on'
										AND ccotra = 'on'
										AND ccoima != 'on'
									";
				}
				else{
				
					$sqlCcoTras = "SELECT
								     Ccocod
								   FROM
										{$wbasedato}_000011
								   WHERE
										ccoest = 'on'
										AND ccotra = 'on'
										AND ccoima = 'on'
									";
				}
				
				$resCco = mysql_query( $sqlCcoTras, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$rowCco = mysql_fetch_array( $resCco );
				
				
				//Busco si el articulo es dispensable
				$qf = "SELECT
							Defart, Deffra, Defdup, Defdis, Defcco, Defdie
						FROM
							{$wbasedato}_000059
						WHERE
							Defart = '{$info['Kadart']}'
							AND Defcco = '{$rowCco['Ccocod']}'
							AND Defest = 'on' ";

				$respin = mysql_query( $qf, $conex ) or die ("Error: " . mysql_errno() . " - en el query: " . $qf . " - " . mysql_error());
				$numf = mysql_num_rows( $respin );

				$dispensable = "on";

				while( $infofr = mysql_fetch_array( $respin ) ){
					$dispensable 		= ( $infofr['Defdis'] == 'on' )? true: false;
				}
				
				if( !$dispensable || $info[ 'Kadess' ] == 'on' ){
					$cantGrabar = 0;
					$saldo = 0;
					$cantDispensar = 0;
				}
				
				$saldoDeDispensacionAnterior = 0;
					
				//De acuerdo a lo calculado actualizo la tabla para el articulo
				//Las campo a modificar son
				// - cantidad a dispensar
				// - saldo de dispensacion
				// - saldo del articulo
				// - cantidad a grabar
				// - horas aplicacion dia
				// - ultima ronda de grabacion
				// - fecha ultima ronda de grabacion				
				$sql = "UPDATE ".$wbasedato."_000054 SET
							Kadsal = '$saldo',
							Kadcdi = '$cantDispensar',
							Kadsad = '$saldoDeDispensacionAnterior',
							Kadcan = '$cantGrabar',
							Kadcpx = '$horasAplicacionDia',
							Kadron = '{$info['Kadron']}',
							Kadfro = '{$info['Kadfro']}',
							Kaddis = '{$info['Kaddis']}'						
						WHERE
							id = '{$info[ 'id' ]}' ";
				
				$resAct = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			}
			
			//Coloco el encabezado del kardex como ya recalculado
			$sql = "UPDATE ".$wbasedato."_000053 
					SET
						Karrca = 'off'
					WHERE
						karhis = '$historia'
						AND karing = '$ingreso'
						AND fecha_data = '$fecha'
					";
					
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			if( mysql_affected_rows() > 0 ){
				$val = true;
			}
		}
	}
	
	return $val;	
}
 
/************************************************************************************************
 * Consulta las familias de medicamentos
 *
 * Solo salen los medicamentos que esten activos tanto en el maesttro de articulos {$wbasedato}_000026
 * como en la definicion de articulos {$wbasedato}_000059.
 * @return unknown_type
 ************************************************************************************************/
function consultarMedicamentos( $conex, $wbasedato, $articulo )
{
	
	// Esta es la variable que contendrá todos los datos de los artículos 
	// que correspondan con la búsqueda de la familia
	$val = "";
	
	 // Se consultan los medicamentos asociados a la familia seleccionada
	 /*
	 $sql = "( SELECT
				Relfam,Famnom,Famcod,Reluni,Unicod,Unides,Relart,Relcon*1 as Relcon, '' Famund,Relpre,Ffacod,Ffanom,Defvia,Artonc
			FROM
				{$wbasedato}_000114 a, 
				{$wbasedato}_000027 b,
				{$wbasedato}_000115 c,
				{$wbasedato}_000026 d,
				{$wbasedato}_000059 e,
				{$wbasedato}_000046 f
			WHERE
				famcod = relfam
				AND reluni =unicod
				AND relpre =ffacod
				AND famnom  LIKE '%$familia%'
				AND relest = 'on'
				AND artcod = relart
				AND artest = 'on'
				AND defart = artcod
				AND defest = 'on' )
			UNION
			( SELECT
				Relfam,Famnom,Famcod,Reluni,Unicod,Unides,Relart,Relcon*1 as Relcon, '' Famund,Relpre,'00' Ffacod,'SIN FORMA FARMACOLOGICA' Ffanom,Defvia,'' as Artonc
			FROM
				{$wbasedato}_000114 a, 
				{$wbasedato}_000027 b,
				{$wbasedato}_000115 c,
				{$wcenmez}_000002 d,
				{$wbasedato}_000059 e
			WHERE
				famcod = relfam
				AND reluni =unicod
				AND famnom  LIKE '%$familia%'
				AND relest = 'on'
				AND artcod = relart
				AND artest = 'on'
				AND artcod NOT IN (SELECT b.artcod FROM {$wbasedato}_000026 b WHERE b.artcod = relart ) 
				AND defart = artcod
				AND defest = 'on' )
			ORDER BY
				Famund DESC, relfam asc, reluni asc, relcon asc, relart asc
			";
		*/
	
	
	/*
	----------- DESCRIPCION DE LAS TABLAS PARA LA SIGUIENTE CONSULTA ---------------
	{$wbasedato}_000026 -> Maestro de artículos (Art)
	{$wbasedato}_000027 -> Maestro de unidades (Uni)
	{$wbasedato}_000046 -> Formas farmacéuticas (Ffa)
	{$wbasedato}_000059 -> Definición fracciones artículos (Def)
	---------------------------------------------------------------------------------
	*/
	
	 // Se consultan los medicamentos asociados al articulo seleccionado
	 $sql = "SELECT
				Artcod,Artgen,Unicod,Unides,Deffra*1 as Deffra,Ffacod,Ffanom,Defvia,Artonc,Artint,Artpos
			FROM
				{$wbasedato}_000026 a,
				{$wbasedato}_000027 b,
				{$wbasedato}_000046 c,
				{$wbasedato}_000059 d
			WHERE
					Deffru = Unicod
				AND Artfar = Ffacod "
		//	."	AND ( Artgen LIKE '%$articulo%' OR Artcom LIKE '%$articulo%' ) "
			."	AND Artgen LIKE '%$articulo%' "
			."	AND Artest = 'on'
				AND Artcod = Defart
				AND Defest = 'on' 
			ORDER BY
				Artgen, Deffru, Deffra, Artcod
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 )
	{
		$artAnt = "";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
		{
			
			$artcod = trim($rows[ 'Artcod' ]);
			$unicod = trim($rows[ 'Unicod' ]);
			$ffacod = trim($rows[ 'Ffacod' ]);
			
			
			////////////////////// ARTICULO //////////////////////
			// Si es diferente al articulo de la iteracción anterior
			if( $artAnt != $artcod )
			{
				$textfind = trim($rows[ 'Artgen' ])."|".$artcod."";
				
				// Defino si el articulo ya se encuentra en la variable $val
				$pos = strpos($val, $textfind);

				if( $i > 0 && $pos === false ){
					$val .= "\n";
					unset($arrFamilias);
				}

				if($pos === false)
					$val .= $textfind;
			}
			//////////////////////////////////////////////////////
			
			
			////////////////////// ARTICULO //////////////////////
			// Incluyo en la variable $val el código y la concentración o dosis del artículo
			if( !isset( $arrFamilias[ $artcod ] ) )
			{
				$arrFamilias[ $artcod ] = 1;
				$val .= "|-".$artcod."|".$rows[ 'Deffra' ];
			}
			//////////////////////////////////////////////////////

			
			////////////////// UNIDAD DE MEDIDA //////////////////
			// Incluyo en la variable $val las unidades de medida para el artículo
			if( !isset( $arrFamilias[ $unicod ] ) )
			{
				$arrFamilias[ $unicod ] = 1;
				$val .= "|@".$unicod."|".trim($rows[ 'Unides' ])."";
			}
			///////////////////////////////////////////////////////
			
			
			///////////////// FORMA FARMACEUTICA //////////////////
			// Incluyo en la variable $val la presentación o forma farmacéutica para el artículo
			if( !isset( $arrFamilias[ $ffacod ] ) )
			{
				$arrFamilias[ $ffacod ] = 1;
				$val .= "|&".$ffacod."|".trim($rows[ 'Ffanom' ])."";
			}
			///////////////////////////////////////////////////////


			/////////////// VIA DE ADMINISTRACION /////////////////
			// Si el campo via de administración no está vacio
			if($rows['Defvia'] && $rows['Defvia']!="" && $rows['Defvia']!=" ")
			{
				// Se da formato a las vías para la sentencia IN del query
				$defvia_arr = str_replace(",","','",$rows['Defvia']);
				
				// Consulto descripcion de la via o vías de administracion
				$sql = " SELECT
							Viacod,Viades
						FROM
							{$wbasedato}_000040
						WHERE
							Viacod IN ('".$defvia_arr."')
						";
			}
			// Si no existen vías de administración asociadas se hace una consulta vacía
			else
			{
				// Consulto descripcion via de administracion
				$sql = " SELECT
							Viacod,Viades
						FROM
							{$wbasedato}_000040
						WHERE
							Viacod = -1
						";
			}
			$resvia = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			// Incluyo en la variable $val las vías de administración para el artículo
			while($rowsvia = mysql_fetch_array( $resvia ))
			{
				$viacod = trim($rowsvia[ 'Viacod' ]);
				if( !isset( $arrFamilias[ $viacod ] ) )
				{
					$arrFamilias[ $viacod ] = 1;
					$val .= "|#".$viacod."|".trim($rowsvia[ 'Viades' ])."";
				}
			}
			///////////////////////////////////////////////////////

			/////////////// CONCENTRACIÓN O DOSIS /////////////////
			// $textfind5 = "|$".$rows[ 'Deffra' ]."|";
			// $pos5 = strpos($val, $textfind5);
			// if($pos5 === false)
				$val .= "|$".$rows[ 'Deffra' ];
			///////////////////////////////////////////////////////

			
			$esOncologico = $rows['Artonc'];
			if(trim($esOncologico)!="on" && trim($esOncologico)!="On")
				$esOncologico = "off";
			
			$val .= "|?".$esOncologico;
			
			$esInterno = strtolower( $rows['Artint'] );
			if(trim($esInterno)!="on" && trim($esInterno)!="On")
				$esInterno = "off";
			
			$val .= "|*".$esInterno;
			
			$esPos = trim( strtoupper( $rows['Artpos'] ) );
			$esPos = ($esPos == 'P' )? "on": "off";
			
			$val .= "|=".$esPos;
				
			// Defino los valores para la iteración actual que servirán para comprarar
			// como valor anterior en la siguiente iteración, si la hay
			$artAnt = $artcod;
		}

		unset($arrFamilias);
	}
	

	return $val."\n";
}

/************************************************************************************************
 * Consulta la presentación unidad de medida y vias de administración de la familia de medicamentos
 * según las selecciones hechas en el formulario de adición de medicamentos
 *
 * @return unknown_type
 ************************************************************************************************/
function filtrarFamiliaMedicamentos( $conex, $wbasedato, $familia, $presentacion, $unidad )
{
	
	// Esta es la variable que contendrá todos los datos de los artículos 
	// que correspondan con la búsqueda de la familia
	$val = "";

	/*
	 // Se consultan los medicamentos asociados a la familia seleccionada
	 $sql = "( SELECT
				Relfam,Famnom,Famcod,Reluni,Unicod,Unides,Relart,Relcon*1 as Relcon, '' Famund,Relpre,Ffacod,Ffanom,Defvia 
			FROM
				{$wbasedato}_000114 a, 
				{$wbasedato}_000027 b,
				{$wbasedato}_000115 c,
				{$wbasedato}_000026 d,
				{$wbasedato}_000059 e,
				{$wbasedato}_000046 f
			WHERE
				famcod = relfam
				AND reluni LIKE '$unidad'
				AND relpre LIKE '$presentacion'
				AND reluni =unicod
				AND relpre =ffacod
				AND TRIM(famnom) LIKE TRIM('$familia')
				AND relest = 'on'
				AND artcod = relart
				AND artest = 'on'
				AND defart = artcod
				AND defest = 'on' )
			UNION
			( SELECT
				Relfam,Famnom,Famcod,Reluni,Unicod,Unides,Relart,Relcon*1 as Relcon, '' Famund,Relpre,Ffacod,Ffanom,Defvia 
			FROM
				{$wbasedato}_000114 a, 
				{$wbasedato}_000027 b,
				{$wbasedato}_000115 c,
				{$wcenmez}_000002 d,
				{$wbasedato}_000059 e,
				{$wbasedato}_000046 f
			WHERE
				famcod = relfam
				AND reluni LIKE '$unidad'
				AND relpre LIKE '$presentacion'
				AND reluni =unicod
				AND relpre =ffacod
				AND TRIM(famnom)  LIKE TRIM('$familia')
				AND relest = 'on'
				AND artcod = relart
				AND artest = 'on'
				AND artcod NOT IN (SELECT b.artcod FROM {$wbasedato}_000026 b WHERE b.artcod = relart ) 
				AND defart = artcod
				AND defest = 'on' )
			ORDER BY
				Famund DESC, relfam asc, reluni asc, relcon asc, relart asc
			";
	*/
	// 2012-07-09
	// Se agregó ORDER BY Famund DESC para poder ordenar según la unidad destacada para la familia de medicamentos

	/*
	----------- DESCRIPCION DE LAS TABLAS PARA LA SIGUIENTE CONSULTA ---------------
	{$wbasedato}_000026 -> Maestro de artículos (Art)
	{$wbasedato}_000027 -> Maestro de unidades (Uni)
	{$wbasedato}_000040 -> Vías de administración (Ffa)
	{$wbasedato}_000046 -> Formas farmacéuticas (Ffa)
	{$wbasedato}_000059 -> Definición fracciones artículos (Def)
	---------------------------------------------------------------------------------
	*/

	 // Se consultan los medicamentos asociados a la familia seleccionada
	 $sql = "SELECT
				Artcod,Artgen,Unicod,Unides,Deffra*1 as Deffra,Ffacod,Ffanom,Defvia 
			FROM
				{$wbasedato}_000026 a,
				{$wbasedato}_000027 b,
				{$wbasedato}_000046 c,
				{$wbasedato}_000059 d
			WHERE
					TRIM(Artgen) LIKE TRIM('$familia') "
		//	."		( TRIM(Artgen) LIKE TRIM('$familia') OR TRIM(Artcom) LIKE TRIM('$familia') ) "
			."	AND Artuni LIKE '$unidad'
				AND Artfar LIKE '$presentacion'
				AND Artuni = Unicod
				AND Artfar = Ffacod
				AND Artest = 'on'
				AND Artcod = Defart
				AND Defest = 'on' 
			ORDER BY
				Artgen, Artuni, Deffra, Artcod
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		
		$artAnt = "";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$artcod = trim($rows[ 'Artcod' ]);
			$unicod = trim($rows[ 'Unicod' ]);
			$ffacod = trim($rows[ 'Ffacod' ]);
			
			
			////////////////////// FAMILIA //////////////////////
			// Si es diferente a la familia de la iteracción anterior
			if( $artAnt != $artcod )
			{
				$textfind = trim($rows[ 'Artgen' ])."|".$artcod."";
				
				// Defino si la familia ya se encuentra en la variable $val
				$pos = strpos($val, $textfind);

				if( $i > 0 && $pos === false )
					$val .= "\n";

				if($pos === false)
					$val .= $textfind;
			}
			//////////////////////////////////////////////////////
			
			
			////////////////////// ARTICULO //////////////////////
			// Incluyo en la variable $val el código y la concentración o dosis del artículo
			// Incluyo en la variable $val el código y la concentración o dosis del artículo
			$val .= "|-".$artcod."|".$rows[ 'Deffra' ];
			//////////////////////////////////////////////////////
			
			
			////////////////// UNIDAD DE MEDIDA //////////////////
			// Incluyo en la variable $val las unidades de medida para el artículo
			$textfind2 = "|@".$unicod."|".trim($rows[ 'Unides' ])."";
			$pos2 = strpos($val, $textfind2);
			if($pos2 === false)
				$val .= $textfind2;
			//////////////////////////////////////////////////////

			
			///////////////// FORMA FARMACEUTICA //////////////////
			// Incluyo en la variable $val la presentación o forma farmacéutica para el artículo
			$textfind3 = "|&".$ffacod."|".trim($rows[ 'Ffanom' ])."";
			$pos3 = strpos($val, $textfind3);
			if($pos3 === false)
				$val .= $textfind3;
			///////////////////////////////////////////////////////
			

			/////////////// VIA DE ADMINISTRACION /////////////////
			// Si existe vía o vías de administración asociadas se consultan
			if($rows['Defvia'] && $rows['Defvia']!="" && $rows['Defvia']!=" ")
			{
				$defvia_arr = str_replace(",","','",$rows['Defvia']);
				
				// Consulto descripcion via de administracion
				$sql = " SELECT
							Viacod,Viades
						FROM
							{$wbasedato}_000040
						WHERE
							Viacod IN ('".$defvia_arr."')
						";
			}
			// Si no existen vías de administración asociadas se hace una consulta vacía
			else
			{
				// Consulto descripcion via de administracion
				$sql = " SELECT
							Viacod,Viades
						FROM
							{$wbasedato}_000040
						WHERE
							Viacod = -1
						";
			}

			// Incluyo en la variable $val las unidades de medida para el artículo
			$resvia = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

			// Incluyo en la variable $val las vías de administración para el artículo
			while($rowsvia = mysql_fetch_array( $resvia ))
			{
				$viacod = trim($rowsvia[ 'Viacod' ]);
				$textfind4 = "|#".$viacod."|".trim($rowsvia[ 'Viades' ])."";
				$pos4 = strpos($val, $textfind4);
				if($pos4 === false)
					$val .= $textfind4;
			}
			///////////////////////////////////////////////////////
			
			/////////////// CONCENTRACIÓN O DOSIS /////////////////
			$textfind5 = "|$".$rows[ 'Deffra' ]."|";
			$pos5 = strpos($val, $textfind5);
			if($pos5 === false)
				$val .= "|$".$rows[ 'Deffra' ];
			///////////////////////////////////////////////////////

			
			// Defino los valores para la iteración actual que servirán para comprarar
			// como valor anterior en la siguiente iteración, si la hay
			$artAnt = $rows[ 'Artcod' ];
		}
	}
	
	echo $val."\n";
}
 
/****************************************************************************************************
 * Indica si un articulo tiene saldo de articulo o no
 ****************************************************************************************************/
function tieneSaldo( $conex, $wbasedato, $historia, $ingreso, $articulo ){

	$val = false;

	$sql = "SELECT
				SUM( Spauen ) as Spauen, SUM( Spausa ) as Spausa
			FROM
				{$wbasedato}_000004
			WHERE
				spahis = '$historia'
				AND spaing = '$ingreso'
				AND spaart = '$articulo'
			GROUP BY
				spaart
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()."".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		if( $rows[ 'Spauen' ] == $rows['Spausa'] ){
			$val = false;
		}
		else{
			$val = true;
		}
	}
	
	return $val;
}

/************************************************************************
 * Indica si un articulo fue aplicado en una fecha
 ************************************************************************/
function esAplicado( $conex, $wbasedato, $historia, $ingreso, $articulo, $fecha ){

	$val = false;
	
	$q="SELECT
			COUNT(*)
		FROM
			{$wbasedato}_000015
		WHERE
			Aplhis = '$historia'
			AND Apling = '$ingreso'
			AND Aplart = '$articulo'
			AND Aplest = 'on'
			AND Aplfec = '$fecha'
		HAVING
			count(*) > 0
		";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0 ){
		$val = true;
	}
	
	return $val;
}
 
/********************************************************************************
 * Borra la tabla temporal dejando los dos ultimos dias
 ********************************************************************************/
function borrarTablaTemporal( $conex, $wbasedato ){

	$val = false;

	$fecha = date( "Y-m-d", time() - 24*3600 );	//Busco

	$sql = "DELETE FROM
				{$wbasedato}_000060
			WHERE
				kadfec < '$fecha'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}

 /****************************************************************************************
  * Octubre 24 de 2011
  *
  * Consulta los pacientes por cco para el perfil.  Esta funcion devuelve una tabla
  * con todos los pacientes que se encuentre en un cco especificado en la variable $servicio.
  * La tabla contiene la informacion de habitacion, historia, nombre del paciente, accion y
  * mensajes sin leer para el perfil
  ****************************************************************************************/
function consultarHabitacionPacienteServicioPerfil($basedatos,$servicio){
	
	$conexion = obtenerConexionBD("matrix");		
		
	$q = "SELECT
			Habcod, Habcco, Habhis, Habing,
			CONCAT(pacno1,' ', pacno2,' ', pacap1,' ', pacap2, '|', Pactid, '|', Pacced ) as nombre, 'off' Ubiptr
		FROM
			{$basedatos}_000020, root_000036, root_000037
		WHERE
			Habcco = '$servicio'
			AND Habdis = 'off'
			AND Habhis != ''
			AND Habest = 'on'
			AND oriced = pacced 
			AND oriori = '10' 
			AND orihis = Habhis 
			AND oriing = Habing 
			AND Oritid = Pactid
		UNION
		SELECT 
			'Urgencias'Habcod, Ubisac Habcco, Ubihis Habhis, Ubiing Habing, 
			CONCAT( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2, '|', Pactid, '|', Pacced ) AS nombre,
			Ubiptr
		FROM
			{$basedatos}_000011, {$basedatos}_000018, root_000036, root_000037
		WHERE
			Ccourg = 'on'
			AND Ccocod = Ubisac
			AND Ccoest = 'on'
			AND Ccocod = '$servicio'
			AND {$basedatos}_000018.fecha_data >= DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 2 DAY),'%Y-%m-%d')
			AND oriced = pacced 
			AND oriori = '10' 
			AND orihis = Ubihis 
			AND oriing = Ubiing 
			AND Oritid = Pactid
		UNION
		SELECT 
			'Cirugia'Habcod, Ubisac Habcco, Ubihis Habhis, Ubiing Habing, 
			CONCAT( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2, '|', Pactid, '|', Pacced ) as nombre,
			Ubiptr
		FROM
			{$basedatos}_000011, {$basedatos}_000018, root_000036, root_000037
		WHERE
			Ccocir = 'on'
			AND Ccocod = Ubisac
			AND Ccoest = 'on'
			AND Ccocod = '$servicio'
			AND ubiald = 'off'
			AND oriced = pacced 
			AND oriori = '10' 
			AND orihis = Ubihis 
			AND oriing = Ubiing 
			AND Oritid = Pactid
		ORDER by 1,5;";
			
	$res = mysql_query($q, $conexion);
	$num = mysql_num_rows($res);

	//Con tabla
	$consulta = "<table>";

	$cont1 = 0;
	$clase = 'fila1';

	if($num > 0){
		$consulta = $consulta."<tr class=encabezadoTabla align=center>";
		$consulta = $consulta."<td>Habitacion</td>";
		$consulta = $consulta."<td>Historia</td>";
		$consulta = $consulta."<td>Paciente</td>";
		$consulta = $consulta."<td>Accion</td>";
		$consulta = $consulta."<td>Mensajes<br>sin leer</td>";
		$consulta = $consulta."<td>Afinidad</td>";
		$consulta = $consulta."</tr>";

		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);

			if( $rs['Ubiptr'] != 'on' ){
				
				if(isset($rs['nombre']) && $rs['nombre'] != ''){
				
					@list( $rs['nombre'], $rs['Pactid'], $rs['Pacced'] ) = explode( "|", $rs['nombre'] );
					
					$consulta = $consulta."<tr class='$clase'>";
					if($clase == 'fila2'){
						$clase = 'fila1';
					} else {
						$clase = 'fila2';
					}

					$consulta = $consulta."<td align=center>".$rs['Habcod']."</td>";
					$consulta = $consulta."<td align=center>".$rs['Habhis']."-".$rs['Habing']."</td>";
					$consulta = $consulta."<td>".$rs['nombre']."</td>";
					$consulta = $consulta."<td><a href='javascript:irAPerfil(\"{$rs['Habhis']}\");'>Ir al Perfil</a></td>";
					
					$sinLeer = consultarMensajesSinLeer($conexion, $basedatos, 'Perfil', $rs['Habhis'],$rs['Habing'] );
					
					if( $sinLeer > 0 ){
						$consulta = $consulta."<td align='center'><blink><b>".$sinLeer."</b></blink></td>";
					}
					else{
						$consulta = $consulta."<td align='center'></td>";
					}
					
					//Afinidad
					$tipo = $color = '';
					clienteMagenta( $rs['Pacced'], $rs['Pactid'], $tipo, $color );
					
					if( !empty( $tipo ) ){
						$consulta = $consulta."<td style='font-size:10pt;color:$color' align='center'><b>";
						$consulta = $consulta.$tipo;
						$consulta = $consulta."</b></td>";
					}
					else{
						$consulta = $consulta."<td></td>";
					}
					
					$consulta = $consulta."</tr>";
				}
			}
			$cont1++;
		}
	} else {
		$consulta = $consulta."<tr><td colspan=3 class=encabezadoTabla>No se encontraron pacientes</td></tr>";
	}
	
	$consulta = $consulta."</table>";

	return $consulta;
}
 
/********************************************************************************
 * Consutla cuantos mensajes no se han leido de la Mensajeria Kardex
 ********************************************************************************/
function consultarMensajesSinLeer( $conex, $wbasedato, $programa, $historia, $ingreso, $fecha, $hora ){

	$val = false;

	$sql = "SELECT 
				count(*)
			FROM 
				{$wbasedato}_000117
			WHERE 
				Menhis = '$historia'
				AND Mening = '$ingreso'
				AND Menprg != '$programa'
				AND Menlei != 'on'
			;";
				 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
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
	
	return $val;
}
 
 
 
 /*************************************************************************************************
 * Calcula la cantidad dispensada hasta una ronda dada
 * 
 * @return unknown_type
 *
 * Modificaciones:
 * Agosto 19 de 2011.		Se verifica que ronda se debe mirar, la primera o la segunda hora
 *************************************************************************************************/
function cantidadADispensarRondaKardex( $horasAplicar, $ronda ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	//Verifico si es la primera o segunda hora en la regleta
	//para esto se mira si ya paso la hora de la ronda
	//La ronda siempre es la que sigue, nunca se muestra rondas antes que la actual
	$timeRonda = strtotime( date( "Y-m-d" )." $ronda" );
	if( time() > $timeRonda ){
		$esPrimera = false;
	}
	else{
		$esPrimera = true;
	}
	
	if( $ronda == "00:00:00" ){
		$esPrimera = true;
	}
	
	//echo "<br>...$ronda...?".$esPrimera;
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		if( $ronda == $valores[0] && $esPrimera ){
			
			$val = $valores[1];
			break;
		}
		elseif( $ronda == $valores[0] ){
			$esPrimera = true;
		}
	}
	
	return $val;
}
 
/************************************************************************************************************************
 * Dada una regleta de la forma hora1-cdi1-dis1,hora2-cdi2-dis2,hora3-cdi3-dis3..., cambia todas las cdin por la nueva
 * unidad de articulo
 ************************************************************************************************************************/
function nuevaDosis( $unidadArticulo, $regleta ){

	$val = "";
	
	if( empty( $regleta ) ){
		return $val;
	}
 
	$rondas = explode( ",", $regleta );
	
	for( $i = 0; $i < count($rondas); $i++ ){
		
		$valores = explode( "-", $rondas[$i] );
		
		if( true || $valores[0] != "Ant" ){
			
			if( $valores[1] > 0 ){
				$val .= ",".$valores[0]."-".round( $unidadArticulo, 3 )."-0";
			}
			else{
				$val .= ",".$valores[0]."-".$valores[1]."-".$valores[2];
			}
		}
	} 
	
	return substr( $val, 1 );
}

/*********************************************************************************************************************
 * Devuelve la hora de traslado de un paciente desde cualquier centro de costos que no se maneje ciclos de produccion
 * 
 * Nota: La primera del día
 * 
 * @param $conexion
 * @param $wbasedato
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $urgencia
 * @return unknown_type
 * 
 * Septiembre 26 de 2011
 *********************************************************************************************************************/
function consultarHoraTraslado( $conexion, $wbasedato, $historia, $ingreso, $fecha ){
	
	$qMv = "SELECT
				a.Hora_data
			FROM
				".$wbasedato."_000011 b,".$wbasedato."_000017 a
			WHERE
				Eyrhis = '$historia'
				AND Eyring = '$ingreso'
				AND Eyrtip = 'Recibo'
				AND a.Fecha_data = '".$fecha."'
				AND Ccocod = Eyrsor
				AND Ccocpx != 'on'
				AND Eyrest='on'
			ORDER BY
				Hora_data asc
			";
	
	$res = mysql_query( $qMv, $conexion ) or die( mysql_errno()." - Error en el query $qMv - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if($numrows > 0 ){
		$rows = mysql_fetch_array( $res );
		
		return $rows['Hora_data'];
	}
	else{
		return false;
	}
}
 

/***********************************************************************************************************
 * Consulta la hora militar, sin minutos y segundos, de la ultima ronda dispensada
 *
 * Modificaciones:
 * - Noviembre 1 de 2011	(Edwin MG)	Se  modifica funcion para poder determinar si la 
 *										ultima ronda grabada corresponde al dia actual o al siguiente
 ***********************************************************************************************************/
function consultarUltimaRondaDispensadaKardex( $vectorAplicaciones ){
 
	$val = "|";
	
	if( empty( $vectorAplicaciones ) ){
		return $val;
	}
	
	$hoy = true;
 
	$rondas = explode( ",", $vectorAplicaciones );
	
	for( $i = 0; $i < count($rondas); $i++ ){
		
		$valores = explode( "-", $rondas[$i] );
		
		if( $valores[0] != "Ant" ){
		
			if( $valores[0] == "00:00:00" ){
				$hoy = false;
			}
			
			if( $valores[2] > 0 ){
				$val = $valores[0];
				list( $val ) = explode( ":", $val );
				
				$val .= "|".$hoy;
			}
		}
	} 
	
	return $val;
}

/************************************************************************************************************
 * Indica si un centro de costos comenzo ya con el ciclo de producción
 * 
 * @param $cco
 * @return unknown_type
 ************************************************************************************************************/
function tieneCpx( $conex, $bd, $cco ){
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$bd}_000011
			WHERE
				ccocod = '$cco'
				AND ccocpx = 'on'
				AND ccoest = 'on'
				AND ccourg != 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en elquery $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$val = true;
	}
	
	return $val;
}

/****************************************************************************************************
 * Indica si el paciente se encuentra en un centro de costos que tiene actualmente cpx 
 ****************************************************************************************************/
function tieneCpxPorHistoria( $conex, $bd, $his, $ing ){
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$bd}_000011 a, {$bd}_000018 b
			WHERE
				ccocpx = 'on'
				AND ccoest = 'on'
				AND ccourg != 'on'
				AND ubihis = '$his'
				AND ubiing = '$ing'
				AND ubisac = ccocod
			"; //echo ".....<pre>$sql</pre>";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en elquery $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$val = true;
	}
	
	return $val;
}

 /**
  * Combina dos vectores
  */
 function cantidadCargadaDiaSiguiente( $vector ){
	
	if( !empty( $vector ) ){
		return;
	}
	
	$valoresVec = explode( ",", $vector );
	
	for( $i = 0; $i < count($valoresVec); $i++ ){
		
		$valVector = explode( "-", $valoresVec );
		
		
	}
	
	return substr( $subString, 1 );
 }
 
 /********************************************************************************
  * Agosto 3 de 2011
  * Si el kardex fue generado automaticamente, los campos karcon y kadare son
  * desactivados 
  ********************************************************************************/
function quitandoKardexAutomatico( $conex, $wbasedato, $his, $ing, $fecha ){
	
	//Agosto 25 de 2011. Se deja el kardex como venga, solo se quita el indicador		
	$sql = "UPDATE
				{$wbasedato}_000053 a
			SET
				karaut = 'off'
			WHERE
				a.fecha_data = '$fecha'
				AND karhis = '$his'
				AND karing = '$ing'
				AND karaut = 'on'
			"; //echo "<pre>....$sql</pre>";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	
	return false;
}

/********************************************************************************
  * Agosto 25 de 2011
  * Si el kardex fue generado automaticamente, los campos karcon y kadare son
  * desactivados 
  ********************************************************************************/
function esAutomatico( $conex, $wbasedato, $his, $ing, $fecha ){
	
	$sql = "SELECT *
			FROM
				{$wbasedato}_000053 a
			WHERE
				a.fecha_data = '$fecha'
				AND karhis = '$his'
				AND karing = '$ing'
				AND karaut = 'on'
			"; //echo "<pre>....$sql</pre>";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		return true;
	}
	
	return false;
}
 
 /********************************************************************************
  * Agosto 3 de 2011
  * Indica si un centro de costo maneja ciclos de produccion
  ********************************************************************************/
 function manejaCPX( $conex, $wbasedato, $cco ){
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000011
			WHERE
				ccocod = '$cco'
				AND ccourg != 'on'
				AND ccocpx = 'on'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$val = true;
	}
	
	return $val;
 }
 
 /************************************************************************************************************************
 * Consulta la hora de corte de produccion para un tipo de articulo
 * 
 * @param $conexion
 * @param $wbasedatoMH
 * @param $wbasedatoCM
 * @param $codArticulo
 * @return unknown_type
 *
 * Agosto 3 de 2011
 ************************************************************************************************************************/
 function consultarHoraCorteDispensacionCpx( $conex, $wbasedato, $tipo, &$tiempoProduccion ){
 
	$val = false;
 
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000099
			WHERE
				tarcod = '$tipo'
				AND tarpdx = 'on'
				AND tarest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$rows = mysql_fetch_array( $res );
		$val = $rows['Tarhcp'];
		$tiempoProduccion = $rows['Tarpre'];
	}
	
	return $val;
 }
 
 
 /************************************************************************************************************************
 * Determina si un articulo es generico y devuelve el tipo de articulo al que pertenece
 * 
 * @param $conexion
 * @param $wbasedatoMH
 * @param $wbasedatoCM
 * @param $codArticulo
 * @return unknown_type
 *
 * Agosto 3 de 2011
 ************************************************************************************************************************/
function esArticuloGenericoCpx( $conexion, $wbasedatoMH, $wbasedatoCM, $codArticulo ){
	
	$sql = "SELECT
				*
			FROM
				{$wbasedatoMH}_000068,
				{$wbasedatoCM}_000002,
				{$wbasedatoCM}_000001
			WHERE
				artcod = '$codArticulo'
				AND arttip = tipcod
				AND tiptpr = arktip
				AND artest = 'on'
				AND arkest = 'on'
				AND tipest = 'on' 
			";
	
	$res = mysql_query( $sql, $conexion ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		return $rows['Arktip'];
	}
	else{
		return false;
	}
}

/************************************************************************
 * Consulta cada cuanto se va a dispensar los articulos
 ************************************************************************/
function consultarTiempoDispensacion( $conex, $wemp ){
	return 2;
	$val = '';
	
	$sql = "SELECT
				Detval
			FROM
				root_000051
			WHERE
				Detapl = 'Dispensacion'
				AND Detemp = '$wemp'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $numrows > 0 ){
		$rows = mysql_fetch_array( $res );
		
		$val = $rows[ 'Detval' ];
	}
	
	return $val;
}

/**
 * Dado un vector con las horas a apllicar un medicamento, crea un vector de cantidad a aplicar por fecha
 * de la siguiente manera:
 * 
 * Hora1-Cantidadadispensar1-0, Hora2-Cantidadadispensar2-0, Hora3-Cantidadadispensar3-0,...
 * 
 * El campo $horasAplicar debe tener el siguiente formato
 * 
 * [ hora ] = 'contador'
 * 
 * contador: puede ser * o -. (*) indica que a esa hora se aplica y (-) que no se aplicar
 * hora: es la hora de aplicacion, como entero de 2-24
 * 
 * @param $horasAplicar
 * @param $horaIncio
 * @param $frecuencia
 * @param $tiempoDispensacion	Enteros pares (0,2,4,6,..)
 * @return unknown_type
 *
 * Modificaciones:
 * Agosto 19 de 2011.  Se tiene en cuenta el tiempo de dispensacion para crear la regleta.
 *
 */
function crearVectorAplicaciones( $horasAplicar, $frecuencia, $can, $tiempoDispensacion ){
	
	$can = round( $can, 3 );
	
	$val = "";
	$apl = 0;
	
	// 2012-09-04
	// Se aumenta hora máxima a 30
	if( $tiempoDispensacion > 2 ){
		$horaMaxima = 30 + ceil( $tiempoDispensacion/2 )*2-2;
	}
	else{
		$horaMaxima = 30;
	}

	if( count($horasAplicar) > 0 ){
		
		$j = 0;
		$k = 0;
		
		for( $i = 0; $k <= $horaMaxima; $i += 2, $j += 2 ){
			
			if( $j == $frecuencia ){
				
				$j = 0;
				
				if( $j == 0 ){
					
					if( $k != 0 ){
						if( $k < 10 ){
							$val .= ",0".($k).":00:00-".($apl*$can)."-0";
						}
						else{
							if( $k < 24 ){
								$val .= ",".($k).":00:00-".($apl*$can)."-0";
							}
							else{
								if( $k - 24 < 10 ){
									$val .= ",0".($k - 24).":00:00-".($apl*$can)."-0";
								}
								else{
									$val .= ",".($k - 24).":00:00-".($apl*$can)."-0";
								}
							}
						}
					}
					$k = $i;
					$apl = 0;
				}
			}
			
			if( @$horasAplicar[$i] == "*" ){
				$apl++;
			}
		}
	}
	
	return substr( $val, 1 );
}


// $a = obtenerVectorAplicacionMedicamentos( "2011-07-29", "2011-07-29", "16:00:00", 6 );
// $b = obtenerVectorAplicacionMedicamentos( "2011-07-30", "2011-07-29", "16:00:00", 6 );
// echo "<pre>"; var_dump($b); echo "</pre>";
// foreach( $b as $keyB => $valueB ){
	// $a[] = $valueB;
// }
// echo "....<pre>"; var_dump($a ); echo "</pre>";
// echo crearVectorAplicaciones( $a, 2, 1, 6 );


/********************************************************************************************************************************************
 * Consulta la ultima ronda creada para un tipo de articulo
 * 
 * @param $tipo
 * @return unknown_type
 ********************************************************************************************************************************************/
function consultarUltimaRondaKardex( $conex, $wbasedato, $tipo ){
	
	$val = date("Y-m-d 00:00:00");
	
	$fecha = date( "Y-m-d", strtotime( date("Y-m-d H:i:s") )-24*3600 );
	
	
	
	//Consulto la ultima ronda que se hizo para un tipo de articulo
	//desde el día anterior, esto por que la siguiente ronda puede ser a la medianoche

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000106
			WHERE
				ecptar = '$tipo'
				AND ecpfec >= '$fecha'
				AND ecpest = 'on'
				AND ( ecpmod = 'P'
				OR ( 
					ecpmod = 'N'
					AND  UNIX_TIMESTAMP( NOW() )  > UNIX_TIMESTAMP(CONCAT( ecpfec,' ',ecpron ) ) 
				) )				
			ORDER BY
				ecpfec desc, ecpron desc 
			"; //echo "<BR>.......A: ".$sql; return;
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res) ){
		$val = $rows[ 'Ecpfec' ]." ".$rows[ 'Ecpron' ];
	}
	
	return $val;
}

/************************************************************************************************************************
 * Proxima ronda de produccion
 * 
 * @return unknown_type
 ************************************************************************************************************************/
function proximaRondaProduccionKardex( $fecha, $hora, $tiempo ){
	
	$val = strtotime( "$fecha $hora" )+3600*$tiempo;
	
	return $val;
}

/**************************************************************************************************************************
 * Calcula la cantidad total a dispensar para segun $horasAplicar
 **************************************************************************************************************************/
function calcularCantidadTotalACargarPorDia( $horasAplicar ){

	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += $valores[1];
	}
	
	return $val;
}

/**************************************************************************************************************************
 * Calcula la cantidad total a dispensar para segun $horasAplicar
 **************************************************************************************************************************/
function calcularCantidadTotalDispensadaPorDia( $horasAplicar ){

	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += $valores[2];
	}
	
	return $val;
}



/**************************************************************************************************************************
 * 
 **************************************************************************************************************************/
function calcularCantidadTotalADispensadaDia( $horasAplicar ){

	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += $valores[2];
	}
	
	return $val;
}

/****************************************************************************************
 * Calcula la cantidad a dispensar hasta una ronda dada
 * 
 * @return unknown_type
 ****************************************************************************************/
function cantidadTotalADispensarRonda( $horasAplicar, $ronda ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += $valores[1];
		
		if( $ronda == $valores[0] ){
			break;
		}
	}
	
	return $val;
}


/****************************************************************************************
 * Calcula la cantidad dispensada hasta una ronda dada
 * 
 * @return unknown_type
 ****************************************************************************************/
function cantidadTotalDispensadaRonda( $horasAplicar, $ronda ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += $valores[2];
		
		if( $ronda == $valores[0] ){
			break;
		}
	}
	
	return $val;
}

/*************************************************************************************************
 * Calcula la cantidad dispensada hasta una ronda dada
 * 
 * @return unknown_type
 *
 * Modificaciones:
 * Agosto 19 de 2011.		Se verifica que ronda se debe mirar, la primera o la segunda hora
 *************************************************************************************************/
function cantidadDispensadaRondaKardex( $horasAplicar, $ronda ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	//Verifico si es la primera o segunda hora en la regleta
	//para esto se mira si ya paso la hora de la ronda
	//La ronda siempre es la que sigue, nunca se muestra rondas antes que la actual
	$timeRonda = strtotime( date( "Y-m-d" )." $ronda" );
	if( time() > $timeRonda ){
		$esPrimera = false;
	}
	else{
		$esPrimera = true;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		if( $ronda == $valores[0] && $esPrimera ){
			
			$val = $valores[2];
			break;
		}
		elseif( $ronda == $valores[0] ){
			$esPrimera = true;
		}
	}
	
	return $val;
}

/************************************************************************************************
 * crea un vector con las cantidades cargadas durante el dia segun la hora de aplicaciones
 * @return unknown_type
 * 
 * Julio 14 de 2011
 ************************************************************************************************/
function crearAplicacionesCargadasPorHorasKardex( $vector, $cantidad ){
	
	if( $cantidad == 0 ){
		return $vector;
	}
	
	if( empty( $vector ) ){
		return "";
	}
	elseif( !empty( $vector ) ){
		//Obtengo las horas de aplicacion del medicamento para el paciente
		$exp = explode( ",", $vector );
	}
	
	$nuevoAplicaciones = "";
	
	if( !empty($vector) ){
		
		$acumuloFraccionCargada = 0;
		
		for( $acumuloFraccionCargada = 0, $j = 0; $acumuloFraccionCargada < $cantidad; $j++ ){
			
			if( $j < count($exp) ){
				
				/************************************
				 * Formato para valores
				 * [0]	Hora
				 * [1]	Cantidad a dispensar
				 * [2]	Cantidad dispensada
				 * @var unknown_type
				 ************************************/
				$valores = explode( "-", $exp[$j] );
				
				if( $valores[1] - $valores[2] > 0 ){
					
					if( $cantidad - $acumuloFraccionCargada >= $valores[1] - $valores[2] ){
						$fraccionCargada = $valores[1] - $valores[2];
					}
					else{
						$fraccionCargada = $cantidad - $acumuloFraccionCargada;
					}
					
					$acumuloFraccionCargada += $fraccionCargada;
					
					$exp[$j] = $valores[0]."-".$valores[1]."-".( round( $valores[2]+$fraccionCargada, 3 ) );
					
					$j--;
				}
			}
			else{
				break;
			}
		}
		
		for( $i = 0; $i < count($exp); $i++ ){
			
			if( $i == 0 ){
				$nuevoAplicaciones = $exp[$i];
			}
			else{
				$nuevoAplicaciones .= ",".$exp[$i];
			}
		}
	}
	
	return $nuevoAplicaciones;
}

//echo "...........".crearAplicacionesCargadasPorHorasKardex( "02:00:00-0-0,04:00:00-0-0,06:00:00-0-0,08:00:00-0-0,10:00:00-0.9-0,12:00:00-0.7-0,14:00:00-0.5-0,16:00:00-0.5-0,18:00:00-0.5-0,20:00:00-0.5-0,22:00:00-0.5-0,00:00:00-0.5-0", 2 );

/************************************************************************************************
 * Trae la informacion correspondiente al tipo de articulo
 * 
 * @param $conex
 * @param $wbasedato
 * @param $tipo
 * @return unknown_type
 ************************************************************************************************/
function consultarinfotipoarticulosKardex( $conex, $wbasedato ){
	
	$val = "";
	
	$tiposAriculos = Array();
	
	//Consultando los tipos de protocolo
	$sql = "SELECT 
				* 
			FROM
				{$wbasedato}_000099 a
			WHERE
				Tarest = 'on'
				AND tarhcp != '00:00:00'
				AND tarhcd != '00:00:00'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
				
	if( $numrows > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['codigo'] = $rows[ 'Tarcod' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['nombre'] = $rows[ 'Tardes' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['tiempoPreparacion'] = consultarTiempoDispensacion( $conex, '10' ); //$rows[ 'Tarpre' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCorteProduccion'] = $rows[ 'Tarhcp' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCaroteDispensacion'] = $rows[ 'Tarhcd' ];
			
			$aux = consultarUltimaRondaKardex( $conex, $wbasedato, $rows[ 'Tarcod' ] );
			
			$auxfec = ""; 
			@list( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'] ) = explode( " ", $aux );
			
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] = proximaRondaProduccionKardex( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'], $tiposAriculos[ $rows[ 'Tarcod' ] ]['tiempoPreparacion'] );
			
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['tieneArticulos'] = 0;
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['totalArticulosSinFiltro'] = 0;
//			if( $i > 10 ){ die(".....Se paso"); }
		}
	}
	
	return $tiposAriculos;
}

/************************************************************************************************
 * Verifica que un usuario tenga permiso de usar el kardex de enfermería editable.
 * @return unknown_type
 ************************************************************************************************/
function verficacionKardexEditable(){
	
	global $wuser;
	global $conex;
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				root_000021
			WHERE
				codgru = '110'
				AND codopt = '1586'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );

		$exp = explode( "-", $rows['Usuarios'] );
		
		foreach( $exp as $keyUsuario => $valueUsuario ){
			
			if( $valueUsuario == $wuser ){
				return true;
			}
		}
	}
	
	return false;
}

/************************************************************************************
 * Verifico que los articulos si esten guardados
 * 
 * @param $conex
 * @param $wbaseadato
 * @param $tabla
 * @param $articulos
 * @return unknown_type
 * 
 * Mayo 20 de 2011
 ************************************************************************************/
function verificacionArticulosGuardados( $conex, $wbasedato, $tabla, $articulos, $descripcion ){
	
	global $usuario;
	
	$malos = Array();
	
	for( $i = 0; $i < count( $articulos ); $i++ ){
		
		//Busco articulos repetidos para un paciente
		//No se permite tener para un paciente, articulos repetidos con la misma fecha y hora de inicio
		$sql = "SELECT
					* 
				FROM
					{$wbasedato}_{$tabla}
				WHERE
					kadfec = '{$articulos[$i][16]}'
					AND kadhis = '{$articulos[$i]['Kadhis']}'
					AND kading = '{$articulos[$i]['Kading']}'
					AND kadhin = '{$articulos[$i]['Kadhin']}'
					AND kadfin = '{$articulos[$i]['Kadfin']}'
					AND kadart = '{$articulos[$i]['Kadart']}'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );

		//Si hay mas de un articulo borro un articulo, ya que no esta
		if( $numrows > 1 ){
			
			// $malos[$i] = $i;
			
			$rows = mysql_fetch_array( $res, MYSQL_ASSOC );
			
			//Creo un array con toda la informacion del articulo
			$datos = "";
			foreach( $rows as $keyDatos => $valueDatos ){
				$datos .= ",".$keyDatos.":".$valueDatos;
			}
			
			if( !empty($datos) ){
				$datos = substr( $datos, 1);
			}
			
			//Borro solo el primer registro encontrado
			$sql = "DELETE FROM {$wbasedato}_{$tabla} 
					 WHERE id = '{$rows[ 'id' ]}'";
			
			$resDelete = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			//registro en auditoria cierre kardex el articulo que se borro por duplicado
			// registrarAuditoriaCierreKardex( $articulos[0]['Kadhis'], $articulos[0]['Kading'], $usuario->codigo, "*".$descripcion."*".$datos );
		}
	}
	
	return $malos;
}

/************************************************************************************
 * Guardo en un array todos los elementos de una consulta
 * @return unknown_type
 * 
 * Mayo 20 de 2011
 ************************************************************************************/
function elementosGrabados( $conex, &$array, $consulta ){
	
	$array = Array();
	
	$sql = "$consulta";
//	echo "<br>.......".$sql;
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$array[$i] = $rows; 
		}
	}
}

/**********************************************************************************************************************************
 * Indica si un articulo peretenece al stock
 * 
 * @param $conex
 * @param $wbasedato
 * @param $articulo
 * @param $cco
 * @return unknown_type
 **********************************************************************************************************************************/
function esStock( $conex, $wbasedato, $articulo, $cco ){
	
	$val = false;
	
	$sql = "SELECT
				Arscod
			FROM
				{$wbasedato}_000091
			WHERE
				arscco = '$cco'
				AND arscod = '$articulo'
				AND arsest = 'on'
			"; //echo $sql;
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$val = true;
	}
	
	return $val;
}

/************************************************************************************************************************
 * Dados una fecha actual, frecuencia y una fecha y hora de inicio del medicamento, retorna la ultima hora
 * de suministro antes de la fecha de corte
 * 
 * @param $fechaActual
 * @param $fechaIncio
 * @param $horaInicio
 * @param $frecuencia
 * @return unknown_type
 * 
 * Nota: La frecuencia esta dada en horas
 * 
 * Abril 13 1 de 2011
 ************************************************************************************************************************/
function suministroAntesFechaCorte( $fechaActual, $horaCorte, $fechaIncio, $horaInicio, $frecuencia ){
	
	$horaIncioActual = false;
	
	//Fecha actual debe ser menor o igual que la fecha de inicio
	if( $fechaIncio <= $fechaActual ){
		
		//convierto las fechas
		$fechorActual = strtotime( "$fechaActual $horaCorte" );
		$fechorInicio = strtotime( "$fechaIncio $horaInicio" );
		
		if( $fechorActual >= $fechorInicio ){
		
			$horaIncioActual = $horaInicio;
			
			//Sumo la frecuencia hasta el día en que comience el medicamento
			for( $i = 0 ; $fechorInicio <= $fechorActual; $i++ ){

				$fechorInicio += $frecuencia*3600;
				$horaIncioActual = $fechorInicio;
			}
			$horaIncioActual = $fechorInicio-$frecuencia*3600;
		}
	}
	
	return $horaIncioActual;
}

/************************************************************************************************************************
 * Registra la auditoria de los articulos que van a ser borrados por que se inactivaron en el sistema
 * 
 * @param $conex
 * @param $wbasedato
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @return unknown_type
 * 
 * Creacion: Abril 5 de 2011
 ************************************************************************************************************************/
function articulosBorradosInactivos( $conex, $wbasedato, $historia, $ingreso, $fecha, $usuario, $tieneGruposIncluidos, $tipoProtocolo ){
	
	global $codigoServicioFarmaceutico;
	
//	$sql = "SELECT 
//				*
//			FROM
//				{$wbasedato}_000054 a, {$wbasedato}_000026 b
//			WHERE
//				kadhis = '$historia'
//				AND kadori = '$codigoServicioFarmaceutico'
//				AND kading = '$ingreso'
//				AND kadfec = '$fecha'
//				AND kadart = artcod
//				AND artest != 'on' ";
	
	$sql = "SELECT 
				*
			FROM
				".$wbasedato."_000054, {$wbasedato}_000026 b
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadpro LIKE '$tipoProtocolo' 
				AND Kadori = '$codigoServicioFarmaceutico'" ;
				if($tieneGruposIncluidos){
					$sql .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
				}
				$sql .= " AND Kadfec = '$fecha'
				AND Kadcco = '$usuario->centroCostosGrabacion'
				AND kadart = artcod
				AND artest != 'on'
			"; //echo ".......<pre>$sql</pre>";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query: $sql - ".mysql_error() );
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

		$mensajeAuditoria = obtenerMensaje( 'MSJ_ARTICULO_ELIMINADO' );

		//Registro de auditoria
		$auditoria = new AuditoriaDTO();

		$auditoria->historia = $historia;
		$auditoria->ingreso = $ingreso;
		$auditoria->descripcion = "{$rows['Kadart']}, inactivo en el sistema";
		$auditoria->fechaKardex = $fecha;
		$auditoria->mensaje = $mensajeAuditoria;
		$auditoria->idOriginal = $rows['Kauido'];
		$auditoria->seguridad = $usuario->codigo;

		registrarAuditoriaKardex( $conex,$wbasedato,$auditoria );
	}
}

function consultaInformacionMedico( $conex, $wbasedato, $codigoMatrix ){
	
	$informacion = false;
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000048
			WHERE
				meduma = '$codigoMatrix'
			"; //echo "....$sql";
				
	$res = mysql_query( $sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	$num = mysql_num_rows($res);

	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		$informacion['nombres'] = $rows['Medno1']." ".$rows['Medno2'];
		$informacion['apellidos'] = $rows['Medap1']." ".$rows['Medap2'];
		$informacion['tipoDocumento'] = $rows['Medtdo'];
		$informacion['nroDocumento'] = $rows['Meddoc'];
		$informacion['especialidad'] = $rows['Medesp'];
		$informacion['registroMedico'] = $rows['Medreg'];
	}
				
	return $informacion;
}

function consultarInfoPacienteOrdenHCEPorHistoria( $conex, $wbasedato, $historia ){

	$paciente = new pacienteKardexDTO();

	//ESTA ES PARA QUE DEJE BUSCAR GENTE DE URGENCIAS
//	$q = "SELECT
//			pacno1, pacno2, pacap1, pacap2, pactid, pacced, Ubisac, Ubihac, Ubisan, Ubihan, Ubialp, Ubiald, Ubiptr,
//			".$wbasedato."_000018.fecha_data fecha_data, ".$wbasedato."_000018.hora_data hora_data, Pacnac,
//			Ingres,Ingnre, '' fechaServicio, '' horaServicio, Cconom, Orihis, Oriing, Ccourg, Ccocir, Pacsex
//		FROM
//			root_000036, root_000037, ".$wbasedato."_000016, ".$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000011 ON Ubisac = Ccocod
//		WHERE
//			oriced = pacced
//			AND Ubihis = Orihis
//			AND Ubiing = Oriing
//			AND Inghis = Orihis
//			AND Inging = Oriing
//			AND Oriori = '10'
//			AND Oriced = '$nroDocumento'
//			AND Oritid = '$tipoDocumento';";

	$q = "SELECT
			pacno1, pacno2, pacap1, pacap2, pactid, pacced, Ubisac, Ubihac, Ubisan, Ubihan, Ubialp, Ubiald, Ubiptr,
			".$wbasedato."_000018.fecha_data fecha_data, ".$wbasedato."_000018.hora_data hora_data, Pacnac,
			Ingres,Ingnre, '' fechaServicio, '' horaServicio, Cconom, Orihis, Oriing, Ccourg, Ccocir, Pacsex
		FROM
			root_000036, root_000037, ".$wbasedato."_000016, ".$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000011 ON Ubisac = Ccocod
		WHERE
			oriced = pacced
			AND Ubihis = Orihis
			AND Ubiing = Oriing
			AND Inghis = Orihis
			AND Inging = Oriing
			AND Oriori = '10'
			AND Orihis = '$historia'
		";	

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$info = mysql_fetch_array($res);

		$paciente->historiaClinica = $info['Orihis'];
		$paciente->ingresoHistoriaClinica = $info['Oriing'];
		$paciente->nombre1 = $info['pacno1'];
		$paciente->nombre2 = $info['pacno2'];
		$paciente->apellido1 = $info['pacap1'];
		$paciente->apellido2 = $info['pacap2'];
		$paciente->documentoIdentidad = $info['pacced'];
		$paciente->tipoDocumentoIdentidad = $info['pactid'];
		$paciente->fechaNacimiento = $info['Pacnac'];
		$paciente->servicioActual = $info['Ubisac'];
		$paciente->servicioAnterior = $info['Ubisan'];
		$paciente->habitacionActual = $info['Ubihac'];
		$paciente->habitacionAnterior = $info['Ubihan'];
		$paciente->numeroIdentificacionResponsable = $info['Ingres'];
		$paciente->nombreResponsable = $info['Ingnre'];

		$paciente->fechaHoraIngresoServicio = $info['fechaServicio']." ".$info['horaServicio'];

		$paciente->fechaIngreso = $info['fecha_data'];
		$paciente->horaIngreso = $info['hora_data'];

		$paciente->altaDefinitiva = $info['Ubiald'];
		$paciente->altaProceso = $info['Ubialp'];
		
		$paciente->sexo = $info['Pacsex'];

		$estado = false;
		if($info['Ubiptr'] == 'on'){
			$paciente->ultimoMvtoHospitalario = "En proceso de traslado";
			$estado = true;
		}

		if ($info['Ubialp'] == 'on'){
			$paciente->ultimoMvtoHospitalario = "Alta en proceso";
			$estado = true;
		}

		if ($info['Ubiald'] == 'on') {
			$paciente->ultimoMvtoHospitalario = "Alta definitiva";
			$estado = true;
		}

		if(!$estado){
			$paciente->ultimoMvtoHospitalario = "En habitaci&oacute;n";
		}
		$paciente->nombreServicioActual = $info['Cconom'];
		
		//El paciente se encuentra en cirugia o en urgencias
		$paciente->enCirugia = isset($info['Ccocir']) && $info['Ccocir'] == "on" ? true : false;
		$paciente->enUrgencias = isset($info['Ccourg']) && $info['Ccourg'] == "on" ? true : false;

		//Edad
		$ann=(integer)substr($paciente->fechaNacimiento,0,4)*360 +(integer)substr($paciente->fechaNacimiento,5,2)*30 + (integer)substr($paciente->fechaNacimiento,8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$ann1=($aa - $ann)/360;
		$meses=(($aa - $ann) % 360)/30;
		if ($ann1<1){
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		} else {
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		}
		$paciente->edadPaciente = $wedad; 
	}
	return $paciente;
}

/******************************************************************************************************
 * Indica si hay articulos en la temporal para un paciente
 * 
 * @param $conex
 * @param $wbasedato
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @return unknown_type
 * 
 * Marzo 8 de 2011
 ******************************************************************************************************/
function hayArticulosEnTemporal( $conex, $wbasedato, $historia, $ingreso, $fecha ){

	$sql = "SELECT 
				*
			FROM
				{$wbasedato}_000060
			WHERE
				kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadfec = '$fecha'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en la consulta - $sql - ". mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		return true;
	}
	else{
		return false;
	}
}


function crearMensajeORMInbound( $conex, $wbasedatoMH, $wbasedatoHCE, $historia, $ingreso, $cco, $codExamen, $nombreExamen, $nroOrden, $item, $datosMsjInbound ){
	
	$infoPaciente = consultarInfoPacienteOrdenHCEPorHistoria( $conex, $wbasedatoMH, $historia );
	
//	$infoPaciente = new pacienteKardexDTO();

	$fechaActualCorta = date( "ymd" );
	$fechaActual = date( "Ymd" );
	$horaActual = date( "His" );
	
//	$datosMsjInbound['codMedicoDefecto'] = $rows000015['Tipmed'];
//	$datosMsjInbound['nomMedicoDefecto'] = $rows000015['Tipnme'];
	
	$codMedico = $datosMsjInbound['codMedicoDefecto'];
	$nomMedico = $datosMsjInbound['nomMedicoDefecto'];
	$consecutivo = $nroOrden.$datosMsjInbound['caracterSeparacion'].$item;
	$nombres = $infoPaciente->nombre1." ".$infoPaciente->nombre2;
	$apellidos = $infoPaciente->apellido1." ".$infoPaciente->apellido2;
	$documento = $infoPaciente->tipoDocumentoIdentidad.$infoPaciente->documentoIdentidad;
	$fechaNacimiento = str_replace( "-", "",$infoPaciente->fechaNacimiento );
	$sexo = $infoPaciente->sexo;
	$direccion = "SIN DEFINIR";
	
	$nombreSolicitante = $datosMsjInbound['solicitante']['nombres']." ".$datosMsjInbound['solicitante']['apellidos'];
	$documentoSolicitante = $datosMsjInbound['solicitante']['nroDocumento'];
	
	$opcional = "";
	
	$mensaje = "MSH|^~\\&|QDOC|AGFA|XXXX|XXXX|$fechaActual$horaActual||ORM^O01^ORM_O01|$consecutivo|P|2.4||||||8859/1".chr(13).chr(10)
			  ."PID|1|$documento|$documento|$consecutivo|$apellidos^$nombres|$apellidos^$nombres^^^^^B|$fechaNacimiento|$sexo|$apellidos^$nombres^^^^^B||$direccion^^MEDELLIN^^05001^CO||3019141".chr(13).chr(10)
			  ."PV1||O||||||||||||||||A".chr(13).chr(10)
			  ."ORC|SC|5010^{$datosMsjInbound['abreviaturaEmpresa']}|{$fechaActualCorta}0002^01|5010|IP||^^^$fechaActual$horaActual^^R|||quadrat||{$datosMsjInbound['codMedicoDefecto']}^{$datosMsjInbound['nomMedicoDefecto']}^^^^DR||||||||||||NA |A^PATIENT TOEGEKOMEN^QDOC".chr(13).chr(10)
			  ."OBR||5010^{$datosMsjInbound['abreviaturaEmpresa']}|{$fechaActualCorta}0002^01|$codExamen^$nombreExamen^{$datosMsjInbound['abreviaturaEmpresa']}||$fechaActual$horaActual|$fechaActual$horaActual||||||$opcional|||{$datosMsjInbound['codMedicoDefecto']}^{$datosMsjInbound['nomMedicoDefecto']}^^^^DR||132978|125153|||$fechaActual$horaActual|||S||^^^$fechaActual$horaActual^^R|||||$documentoSolicitante&$nombreSolicitante||^^^^RMN||$fechaActual$horaActual"
			  .chr(13).chr(10);
	
	return $mensaje;
}

/************************************************************************************************************************
 * Dados una fecha actual, frecuencia y una fecha y hora de inicio del medicamento, retorna la hora en que
 * comienza un medicamento en la fecha actual
 * 
 * @param $fechaActual
 * @param $fechaIncio
 * @param $horaInicio
 * @param $frecuencia
 * @return unknown_type
 * 
 * Nota: La frecuencia esta dada en horas
 * 
 * Febrero 29 de 2011
 ************************************************************************************************************************/
function horaInicioMedicamento( $fechaActual, $fechaIncio, $horaInicio, $frecuencia ){
	
	$horaIncioActual = false;
	
	//Fecha actual debe ser menor o igual que la fecha de inicio
	if( $fechaIncio <= $fechaActual ){
		
		//convierto las fechas
		$fechorActual = strtotime( "$fechaActual 00:00:00" );
		$fechorInicio = strtotime( "$fechaIncio $horaInicio" );
		
		$horaIncioActual = $horaInicio;
		
		//Sumo la frecuencia hasta el día en que comience el medicamento
		for( $i = 0 ; date( "Y-m-d", $fechorInicio ) < date( "Y-m-d", $fechorActual ); $i++ ){

			$fechorInicio += $frecuencia*3600;
			$horaIncioActual = date( "H:i:s", $fechorInicio );
		}
	}
	
	return $horaIncioActual;
}

/************************************************************
 * Indica si un centro de costos es de ingreso
 * 
 * @param $conexion
 * @param $wbasedato
 * @param $cco
 * @return unknown_type
 ************************************************************/
function esCcoIngreso( $conexion, $wbasedato, $cco ){

	$sql = "SELECT
				Ccourg, Ccoing, Ccocir
			FROM
				{$wbasedato}_000011
			WHERE
				ccocod = '$cco'
				AND ccoest = 'on'
				AND ccoing = 'on'
			";
				
	$res = mysql_query( $sql, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		return true;
	}
	
	return false;
}


/************************************************************************************************************************
 * Actualiza la firma de los campos de dextrometer si el usuario fue quien creo o modifico el esquema de dextrometer
 * 
 * @param $conex
 * @param $wbasedato
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $codigoUsuario
 * @param $firma
 * @return unknown_type
 ************************************************************************************************************************/
function actualizarUsuarioDextrometer( $conex,$wbasedato,$historia,$ingreso,$fecha,$codigoUsuario,$firma ){
	
	$sql = "UPDATE
				{$wbasedato}_000071
			SET
				Indfir = '$firma'
			WHERE
				Indhis = '$historia'
				AND Inding = '$ingreso'
				AND Indfec = '$fecha'
				AND Indusu = '$codigoUsuario'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

/******************************************************************************************
 * Desactiva un registro en la tabla correpondiente HL7 de mhosidc
 * 
 * @param $conex
 * @param $wbasedato
 * @param $tabla
 * @param $id
 * @return unknown_type
 ******************************************************************************************/
function cancelarDatosHL7( $conex, $wbasedato, $tabla, $id = '' ){
	
	if( $tabla != '' ){
		
		$sql = "UPDATE
					{$wbasedato}_{$tabla}
				SET
					hl7est = 'off'
				WHERE
					id = '$id'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			return true;
		}
		else{
			return false;
		}
	}
}

/**************************************************************************************************
 * Actualiza los datos de la tabla HL7
 * @param $conex
 * @param $wbasedato
 * @param $tabla
 * @param $justificacion
 * @param $id
 * @return unknown_type
 **************************************************************************************************/
function actualizarDatosHL7( $conex, $wbasedato, $tabla, $justificacion, $id = '' ){

	if( $tabla != "" ){
		
		$sql = "UPDATE
					{$wbasedato}_{$tabla}
				SET
					hl7edo = '$justificacion',
					hl7est = 'on'
				WHERE
					id = '$id'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			return true;
		}
		else{
			return false;
		}
	}
}

/********************************************************************************************************************************
 * Registra los datos correspondientes a la tabla HL7
 * 
 * @return unknown_type
 ********************************************************************************************************************************/
function registrarDatosHL7( $conex, $wbasedato, $tabla, $ori, $des, $nor, $nit, $historia, $ingreso, $inbound, $oru, $edo ){
	
	if( $tabla != "" ){
		$fecha = date( "Y-m-d" );
		$hora = date( "H:i:s" );
		
		$sql = "INSERT INTO {$wbasedato}_{$tabla}(     Medico    , Fecha_data, Hora_data, hl7ori, hl7des, hl7nor, hl7nit     ,    hl7his  , hl7ing   ,   hl7inb  , hl7rdo , hl7edo, hl7est, hl7env,   Seguridad    )
					  					   VALUES( '{$wbasedato}',  '$fecha' , '$hora'  , '$ori', '$des', '$nor', '$nor-$nit', '$historia', '$ingreso', '$inbound', '$oru', '$edo',  'on' , 'off' ,'C-{$wbasedato}')
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			return true;
		}
		else{
			return false;
		}
	}
}

/**************************************************************************************************************
 * Busco si el examen tiene tabla hcl7, si la tiene registro o actualizo los datos correspondientes segun el 
 * caso
 * 
 * @param $conex
 * @param $wbasedato
 * @param $tabla
 * @param $historia
 * @param $ingreso
 * @param $examen
 * @param $consecutivo
 * @param $justificacion
 * @return unknown_type
 **************************************************************************************************************/
function datosHL7( $conex, $bdMH, $wbasedato, $historia, $ingreso, $examen, $nroOrden, $destino, $nroItem, $justificacion, $solicitante ){
	
	//Busco si tiene tabla HCL7
	$sql = "SELECT
				Arc_HL7, Tipcse, Tipabr, Tipmed, Tipnme, Tipcun, Tipnun, b.Descripcion, Servicio
			FROM
				{$wbasedato}_000015 a, {$wbasedato}_000017 b
			WHERE
				b.codigo = '$examen'
				AND tipoestudio = a.codigo
				AND Arc_HL7 != ''
				AND Arc_HL7 != 'NO APLICA'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows000015 = mysql_fetch_array( $res );
		
		//Datos para crear el mensaje
		$datosMsjInbound = Array();
		
		$datosMsjInbound['caracterSeparacion'] = $rows000015['Tipcse'];
		$datosMsjInbound['abreviaturaEmpresa'] = $rows000015['Tipabr'];
		$datosMsjInbound['codMedicoDefecto'] = $rows000015['Tipmed'];
		$datosMsjInbound['nomMedicoDefecto'] = $rows000015['Tipnme'];
		$datosMsjInbound['codigoUnidadProveedor'] = $rows000015['Tipcun'];
		$datosMsjInbound['nombreUnidadProveedor'] = $rows000015['Tipnun'];
		
		//Consulto la informacion del medico Solicitante
		$datosMsjInbound['solicitante'] = consultaInformacionMedico( $conex, $bdMH, $solicitante );
		
		$servicio = $rows000015['Servicio'];
		$nombreExamen = $rows000015['Descripcion'];;
		
		$tabla = $rows000015['Arc_HL7'];
			
		//Busco si el mensaje HL7 existe para dicho examen
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_{$tabla} 
				WHERE
					hl7his = '$historia'
					AND hl7ing = '$ingreso'
					AND hl7des = '$destino'
					AND hl7nor = '$nroOrden'
					AND hl7nit = '$nroOrden-$nroItem'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numHL7 = mysql_num_rows( $res );
		
		if( $numHL7 <= 0 ){
			
			$nor = $nit = $inbound = $edo = "";
			$inbound = crearMensajeORMInbound( $conex, $bdMH, $wbasedato, $historia, $ingreso, $servicio, $examen, $nombreExamen, $nroOrden, $nroItem, $datosMsjInbound );
			$oru= '';
			
			//Consulto el cco en quq  se encuentra el paciente
			$sql = "SELECT
						Ubisac
					FROM
						mhosidc_000018
					WHERE
						Ubihis = '$historia'
						AND Ubiing = '$ingreso'
					";
			
			$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			
			if( $rows = mysql_fetch_array( $res ) ){
				$origen = $rows['Ubisac'];
				
				registrarDatosHL7( $conex, $wbasedato, $tabla, $origen, $destino, $nroOrden, $nroItem, $historia, $ingreso, $inbound, $oru, $justificacion );
			}
		}
		else{
			
			$rowsHL7 = mysql_fetch_array( $res );
			
			actualizarDatosHL7( $conex, $wbasedato, $tabla, $justificacion, $rowsHL7['id'] );
		}
	}
	else{
		return false;
	}
}

/******************************************************************************************************************************
 * Devuelve todos los tipos diangositocs en un string, con forma codigo1-descripción,codigo2-descripción2
 * 
 * @param $conexion
 * @param $wbasedato
 * @return unknown_type
 * 
 * Enero 24 de 2011
 ******************************************************************************************************************************/
function tiposAyudasDiagnosticas( $conexion, $wbasedato, $especialidad ){
	
	$val = "";
	
	$sql = "SELECT
				Codigo, Descripcion
			FROM
				{$wbasedato}_000015
			WHERE
				estado = 'on'
			AND Codigo NOT IN (
				SELECT
					Toetip
				FROM
					{$wbasedato}_000045
				WHERE
					Toeesp != '{$especialidad}' 
				AND Toeest = 'on'			
			)
			ORDER BY
				2 ASC
			";
	
	$res = mysql_query( $sql, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			if( $i == 0 ){
				$val = $rows[ 'Codigo' ]."-".$rows[ 'Descripcion' ];
			}
			else{
				$val .= "|".$rows[ 'Codigo' ]."-".$rows[ 'Descripcion' ];
			}		
		}
	}
	
	return $val;
}

/********************************************************************************************************
 * Devuelve la hora de traslado de un paciente desde urgencia o cirugia.
 * 
 * Nota: La primera del día
 * 
 * @param $conexion
 * @param $wbasedato
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $urgencia
 * @return unknown_type
 * 
 * Enero 14 de 2011
 ********************************************************************************************************/
function consultarHoraTrasladoUrgencias( $conexion, $wbasedato, $historia, $ingreso, $fecha ){
	
	$qMv = "SELECT
				a.Hora_data
			FROM
				".$wbasedato."_000017 a, ".$wbasedato."_000011 b 
			WHERE
				Eyrhis = '$historia'
				AND Eyring = '$ingreso'
				AND Eyrtip = 'Recibo'
				AND a.Fecha_data = '".$fecha."'
				AND Ccocod = Eyrsor
				AND ( Ccourg = 'on' OR Ccocir = 'on' OR Ccoing = 'on' )
				AND Eyrest='on'
			ORDER BY
				Hora_data asc
			";  //echo ".........".$qMv;
	
	$res = mysql_query( $qMv, $conexion ) or die( mysql_errno()." - Error en el query $qMv - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if($numrows > 0 ){
		$rows = mysql_fetch_array( $res );
		
		return $rows['Hora_data'];
	}
	else{
		return false;
	}
}


/******************************************************************************************
 * Inidca si un articulo es un génerico o no
 * 
 * @param $conexion			Conexión a la BD
 * @param $wbasedatoMH		Base de datos de movimiento hospitalario
 * @param $wbasedatoCM		Base de datos de Central de Mezclas
 * @param $codArticulo		Codigo del articulo
 * @return unknown_type		Booleano
 * 
 * Enero 05 de 2011
 ******************************************************************************************/
function esArticuloGenerico( $conexion, $wbasedatoMH, $wbasedatoCM, $codArticulo ){
	
	$sql = "SELECT
				*
			FROM
				{$wbasedatoMH}_000068,
				{$wbasedatoCM}_000002,
				{$wbasedatoCM}_000001
			WHERE
				arkcod = '$codArticulo'
				AND artcod = arkcod
				AND arttip = tipcod
				AND tiptpr = arktip
				AND artest = 'on'
				AND arkest = 'on'
				AND tipest = 'on' 
			";
	
	$res = mysql_query( $sql, $conexion ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		return true;
	}
	else{
		return false;
	}
}


/************************************************************************************************************
 * Diciembre 17 de 2010
 * Determina si un articulo es creado por primera vez en el kardex, basandose en el kardex del día anterior
 * 
 * @param $conexion
 * @param $wbasedato
 * @param $codArticulo
 * @param $idRegDiaAnterior		id del registro anterior
 * @param $fechaKardex			Fecha del kardex
 * @param $fechaInicio			Fecha de Incio del medicamento
 * @param $horaInicio			Hora de inicio del medicamento
 * @param $fechaUsar			La fecha que se debe usar para el calculo de cantidad a dispensar
 * @return unknown_type
 * 
 * Modificacion:  Enero 14 de 2011
 * - Se agrega campo FechaUsar en la función
 * - Se agrega condición: si el paciente fue trasladado de urgencias o cirugía antes de que el kardex sea
 * 	 sea creado, los medicamentos se toman como primera vez.  Además si esto ocurre, al día siguiente se tomo
 * 	 como segunda vez, esto último debido a que habría conflicto al día siguiente con la regla de que si viene
 *   del día anterior y no fue dispensado, se calcula el medicamento como si fuera primera vez
 * - Se agrega condición: Si el medicamento comienza al día siguiente antes de la hora de dispensación,
 *   calcular como si fuera primera vez. 
 * 
 ************************************************************************************************************/
function esPrimeraVez( $conexion, $wbasedato, $historia, $ingreso, $codArticulo, $idRegDiaAnterior, $fechaKardex, $fechaInicio, $horaInicio, $frecuencia, &$fechaUsar, &$horaUsar, &$dejarSaldoArticulo ){
	
	global $horaCorteDispensacion;
	
	$encabezado = false;
	$horaTrasladoUrgencias = false;
	$esTrasladadoUrgenciaCirugia = false;
	$existeEncabezado = false;
	$antesCrearKardex = false;	//Indica si un kardex fue creado despues de la hora de traslado del paciente
	$esModificado = false;	//Indica si el articulo fue modificado
	
	$fechaUsar = $fechaInicio;
	$horaUsar = $horaInicio;
	
	if( $idRegDiaAnterior != '' ){
		
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000043 b, {$wbasedato}_000054 a
				WHERE
					a.id = '$idRegDiaAnterior'
					AND percod = kadper
				";
	
		$res = mysql_query( $sql, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );

		if( $numrows > 0 ){
			
			$fila = mysql_fetch_array($res);
			
			//Un articulo no puede ser creado dias posteriores a la fecha del kardex
			//Si ocurre significa que el se comenzo a crear el kardex el día anterior pero se terminó de crear al día siguiente (Despues de las 23:59:59)
			//Por tal motivo se deja con la fecha y hora máxima para los calculos de la creacion del kardex
			if( $fila['Fecha_data'] > $fila['Kadfec'] ){
				
				$fila['Fecha_data'] = $fila['Kadfec'];
				$fila['Hora_data'] = "23:59:59";
			}
			
			//No es primera vez
			//Diciembre 9 de 2010
			//Diciembre 6 de 2010
			//Si fue trasladado de urgencia a piso, se trata el articulo como si fuera primera vez y si no fue dispensado el dia anterior
			if(isset($fila) && isset($fila['Kadart']) && $fila['Kadart'] != '' ){
				
				/**********************************************************************
				 * Enero 14 de 2011
				 **********************************************************************/
				//Verifico si hubo traslado desde urgencias el dia actual
				$esTrasladadoUrgenciaCirugia = esTrasladoDeUregnciasDiaAnterior( $conexion, $wbasedato, $fila['Kadhis'], $fila['Kading'], date( "Y-m-d", strtotime( $fechaKardex )+24*3600 ) );
				
				$esTrasladadoUrgenciaCirugiaHoy = $esTrasladadoUrgenciaCirugia;	//Marzo 31 de 2011
				$horaTrasladoHoy = false;										//Marzo 31 de 2011
				
				//si hubo recibo de pacientes en el piso, miro la hora de creación del kardex
				//si la hora de recibo de kardex es menor a la hora traslado significa que los medicamentos son tratados como primera vez 
				if( $esTrasladadoUrgenciaCirugia ){
					
					$horaTraslado = consultarHoraTrasladoUrgencias( $conexion, $wbasedato, $fila['Kadhis'], $fila['Kading'], $fechaKardex );
					$existeEncabezado = existeEncabezadoKardexSinCco( $wbasedato, $conexion, $fila['Kadhis'], $fila['Kading'], $fechaKardex, $encabezado );
					
					$horaTrasladoHoy = $horaTraslado;							//Marzo 31 de 2011

					if( $horaTraslado < $encabezado['Hora_data'] ){
						$antesCrearKardex = true;
					}
				}
				/***********************************************************************/

				if( !$esTrasladadoUrgenciaCirugia ){	//Enero 14 de 2010.  Si fue trasladado a piso desde urgencia y no se le ha hecho kardex ni el articulo ha sido dispensado el día anterior se calcula como primera vez
					
					/**********************************************************************
				 	 * Enero 14 de 2011
				 	 **********************************************************************/
					//verifico que el dia anterior no haya sido cosiderado como primera vez por el caso de
					//que si el paciente fue recibido desde urgencias a piso y no se había hecho el kardex al paciente
					$antesCrearKardex = false;
					
					$esTrasladadoUrgenciaCirugia = esTrasladoDeUregnciasDiaAnterior( $conexion, $wbasedato, $fila['Kadhis'], $fila['Kading'], $fechaKardex );
					
					if( $esTrasladadoUrgenciaCirugia ){
							
						$horaTraslado = consultarHoraTrasladoUrgencias( $conexion, $wbasedato, $fila['Kadhis'], $fila['Kading'], date( "Y-m-d", strtotime( $fechaKardex )-24*3600 ) );
						$existeEncabezado = existeEncabezadoKardexSinCco( $wbasedato, $conexion, $fila['Kadhis'], $fila['Kading'], date( "Y-m-d", strtotime( $fechaKardex )-24*3600 ), $encabezado );

						$ayer = date( "Y-m-d", strtotime( $fechaKardex )-24*3600 );
						
						if( $horaTraslado < $encabezado['Hora_data'] || $ayer < $fila['Fecha_data'] || ( $ayer == $fila['Fecha_data'] && $horaTraslado < $fila['Hora_data'] ) ){
							$antesCrearKardex = true;
						}
					}
					/**********************************************************************/

					if( ($fila['Kaddis'] > 0 && $fila['Kadhdi'] != "00:00:00")
						 || !esTrasladoDeUregnciasDiaAnterior( $conexion, $wbasedato, $fila['Kadhis'], $fila['Kading'], $fechaKardex )
						 || $antesCrearKardex
						 || true
					  ){	//Evaluo si viene de Urgencias o Cirugía del día anterior
						
					   	if( !( trim( $fechaInicio ) == date( "Y-m-d", strtotime( date("Y-m-d") )+24*3600 ) && trim( $horaInicio ) <= $horaCorteDispensacion ) ){
					   		
					   		/************************************************************************************************************************************
					   		 * Enero 24 de 2011
					   		 ************************************************************************************************************************************/
					   		//Si el articulo tiene la misma fecha, hora de inicio y frecuencia que el registro anterior, se considera que no fue modificado
					   		// echo "...kadfin: ".$fila['Kadfin'], "...fecini: ".trim( $fechaInicio ), "...kadhin: ".$fila['Kadhin'], "...horini: ".trim( $horaInicio ), "...equ: ".$fila['Perequ'], "....fre: ".$frecuencia;
					   		
					   		if( $fila['Kadfin'] == trim( $fechaInicio ) && $fila['Kadhin'] == trim( $horaInicio ) && $fila['Perequ'] == $frecuencia ){
					   			$esModificado = false;
					   		}
					   		else{
					   			$esModificado = true;
					   		}
					   		/************************************************************************************************************************************/
					   		
					   		if( !$esModificado ){	//Enero 24 de 2011 verifico si el articulo no fue modificado
					   		
								//===========================================================================================================================
								//Diciembre 16 de 2010
								//===========================================================================================================================
								if( ( ( trim( $fila['Kadfin'] ) == date("Y-m-d") ) && ( trim( $fila['Kadhin'] ) > $horaCorteDispensacion ) )
									|| ( ( trim( $fila['Kadfin'] ) == date( "Y-m-d", strtotime( date("Y-m-d") )+24*3600 ) ) && ( trim( $fila['Kadhin'] ) <= $horaCorteDispensacion ) )
								){
									return true;
								}
								else{
									$horaUsar = $horaCorteDispensacion.":00:00";
									// echo "<br>..........Holaassssssaaassss....";
									// $horaUsar = "08:00:00";
									// $fechaUsar = "2012-02-24";
									// return true;
									return false;
								}
								//===========================================================================================================================
					   		}
					   		else{
								
					   			/****************************************************************************************************************
					   			 * Marzo 31 de 2011
					   			 * 
					   			 * Si un paciente es traslado desde urgencia a piso y se modifica el medicamento y no fue dispensado el día anterior
					   			 * el calculo de la cantidad a dispensar se hace como si fuera primera vez
					   			 ****************************************************************************************************************/
								if( $esTrasladadoUrgenciaCirugiaHoy && $horaTrasladoHoy && $horaTrasladoHoy < date("H:i:s")
									&& ($fila['Kaddis'] == 0 && $fila['Kadhdi'] == "00:00:00") 
								){
									$dejarSaldoArticulo = false;
									return true;
								}
								/****************************************************************************************************************/
					   			
					   			/************************************************************************************************************************************
					   		 	* Enero 24 de 2011
					   		 	************************************************************************************************************************************/
					   			if( $fechaInicio <= $fechaKardex && $horaInicio <= $horaCorteDispensacion ){
									
									return true;
					   			}
					   			elseif( $fechaInicio < $fechaKardex && $horaInicio > $horaCorteDispensacion ){
									
					   				$fechaUsar = $fechaKardex;	//Enero 25 de 2011
					   				return true;
					   			}
					   			else{
					   				return true;
					   			}
					   			/************************************************************************************************************************************/
					   		}//Enero 24 de 2011
					   	}
					   	else{
					   		return true;
					   	}
					}
					else{
						
						$dejarSaldoArticulo = false;
						
						if( $fechaInicio < $fechaKardex ){	//Enero 17 de 2011
							
							$horaUsar = horaInicioMedicamento( $fechaKardex, $fechaInicio, $horaInicio, $frecuencia );	//Marzo 1 de 2011
							$fechaUsar = $fechaKardex;
						}
						return true;
					}
				}
				else{	//Si es traslado de urgencia cirugia el dia actual
					
					$dejarSaldoArticulo = false;
					
					//Caculo a partir de que horas debe calcular el kardex
					//Esta es siempre a partir de la siguiente ronda de trasaldo
					list( $hrTraslado ) = explode( ":", $horaTraslado );
					
					//Esto calclo la hora para posterior
					$hrTraslado = intval( $hrTraslado/2 )*2;
					
					//Consulto fecha de inicio despues de la hora de traslado
					$fhInicioTraslado = suministroAntesFechaCorte( $fechaKardex, gmdate( "H:i:s", $hrTraslado*3600 ), $fechaInicio, $horaInicio, $frecuencia );
					
					//Si ultimo suministro es  falso es por la fecha de inicio es posterior a la hora de traslado 
					//Por tanto la fecha y hora de inicio se mantiene
					//Si es verdadero cambio la fecha y hora de inicio por la fecha y hora de inicio despues del traslado
					if( $fhInicioTraslado ){
					
						//Cambio la fecha y hora de inicio por la nueva
						//Siempre sumo uno a la frecuencia ya que la funcion suministroAntesFechaCorte devuelve el ultimo suministro
						//o aplicacion antes o igual a las fecha dada
						//Recordar que $horasFrecuencia esta dada en horas
						$fechaUsar = date( "Y-m-d", $fhInicioTraslado + $frecuencia*3600 );
						$horaUsar= date( "H:i:s", $fhInicioTraslado + $frecuencia*3600 );
					}
					else{
						$horaUsar = $horaInicio;
						$fechaUsar = $fechaInicio;
					}
					
					return true;               //Es primera vez
				}
			}
			else{
				return false;
			}
		}
		else{
			return true;
		}
	}
	else{
	
		/************************************************************************************************************
		 * Abril 23 de 2012
		 *
		 * Si el paciente es trasladado a piso el día actual el kardex se recalcula a partir de la ronda siguiente
		 * de la fecha y hora de traslado
		 ************************************************************************************************************/
		$esTrasladadoUrgenciaCirugia = esTrasladoDeUregnciasDiaAnterior( $conexion, $wbasedato, $historia, $ingreso, date( "Y-m-d", strtotime( $fechaKardex )+24*3600 ) );
		
		if( $esTrasladadoUrgenciaCirugia ){

			$horaTraslado = consultarHoraTrasladoUrgencias( $conexion, $wbasedato, $historia, $ingreso, $fechaKardex );
			
			//Si es traslado de urgencia cirugia el dia actual
			$dejarSaldoArticulo = false;
			
			//Caculo a partir de que horas debe calcular el kardex
			//Esta es siempre a partir de la siguiente ronda de trasaldo
			list( $hrTraslado ) = explode( ":", $horaTraslado );
			
			//Esto calclo la hora par posterior
			$hrTraslado = intval( $hrTraslado/2 )*2;
			
			//Consulto fecha de inicio despues de la hora de traslado
			$fhInicioTraslado = suministroAntesFechaCorte( date( "Y-m-d", strtotime( $fechaKardex." 00:00:00" ) + $hrTraslado*3600 ), 
														   date( "H:i:s", strtotime( $fechaKardex." 00:00:00" ) + $hrTraslado*3600 ), 
														   $fechaInicio, $horaInicio, 
														   $frecuencia );

			//Si ultimo suministro es  falso es por la fecha de inicio es posterior a la hora de traslado 
			//Por tanto la fecha y hora de inicio se mantiene
			//Si es verdadero cambio la fecha y hora de inicio por la fecha y hora de inicio despues del traslado
			if( $fhInicioTraslado ){
				//Cambio la fecha y hora de inicio por la nueva
				//Siempre sumo uno a la frecuencia ya que la funcion suministroAntesFechaCorte devuelve el ultimo suministro
				//o aplicacion antes o igual a las fecha dada
				//Recordar que $horasFrecuencia esta dada en horas
				$fechaUsar = date( "Y-m-d", $fhInicioTraslado + $frecuencia*3600 );
				$horaUsar= date( "H:i:s", $fhInicioTraslado + $frecuencia*3600 );
			}
		}
		/************************************************************************************************************/
		
		return true; 
	}
}

/**
 * Dice si un centro de costos tiene kardex o no
 * 
 * @param $conexion
 * @param $wbasedato
 * @param $cco				Centro de costos donde se encuentra el paciente
 * @param $ccoUsuario		Centro de costos al que pertenece el usuario
 * @return unknown_type
 * 
 * Diciembre 7 de 2010
 */
function servicioConKardex( $conexion, $wbasedato, $cco, $ccoUsuario ){
	
	global $centroCostosServicioFarmaceutico;
	global $centroCostosCentralMezclas;
	
	if( $centroCostosServicioFarmaceutico == $ccoUsuario || $centroCostosCentralMezclas == $ccoUsuario ){
		
		$qMv = "SELECT
					ccokar
				FROM
					{$wbasedato}_000011
				WHERE
					ccocod = '$cco'
					AND ccokar = 'on'
					AND ccoest = 'on'
				";  //echo ".........".$qMv;
		
//		$centroCostosServicioFarmaceutico = "1050";
//		$centroCostosCentralMezclas = "1051";
					
		$res = mysql_query( $qMv, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if($numrows > 0 ){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

function esTrasladoDeUregnciasDiaAnterior( $conexion, $wbasedato, $historia, $ingreso, $fecha ){
	
	$qMv = "SELECT
				Ccourg, Ccocir, Ccoing
			FROM
				".$wbasedato."_000017, ".$wbasedato."_000011 
			WHERE
				Eyrhis = '$historia'
				AND Eyring = '$ingreso'
				AND Eyrtip = 'Recibo'
				AND ".$wbasedato."_000017.Fecha_data = DATE_SUB('".$fecha."',INTERVAL 1 DAY)
				AND Ccocod = Eyrsor
				AND Eyrest='on'
			";  //echo ".........".$qMv;
	
	$res = mysql_query( $qMv, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if($numrows > 0 ){
		
		if( $rows = mysql_fetch_array( $res ) ){
			
			if( ( isset($rows['Ccourg']) && $rows['Ccourg'] == 'on') || ( isset($rows['Ccocir']) && $rows['Ccocir'] == 'on' ) || ( isset($rows['Ccoing']) && $rows['Ccoing'] == 'on' ) ){
				return true;
			}
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}


function consultarUnidadManejo( $codigoArticulo, $cco ){
	
	global $conex;
	global $wbasedato;
	global $wcenpro;
	
	$sql = "SELECT
				*
			FROM
				{$wcenpro}_000002, {$wcenpro}_000001
			WHERE
				artcod = '$codigoArticulo'
				AND arttip = tipcod
				AND tipcod != ''
				AND tipcod != 'NO APLICA'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000059
				WHERE
					Defcco = '$cco'
					AND defart = '$codigoArticulo'
					AND defest = 'on'
				";
	
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			return $rows['Deffru'];
		}
	}
	else{
		return;
	}
}

function obtenerMensaje($clave){
	$texto = 'No encontrado';

	switch ($clave) {
		case 'MSJ_KARDEX_CREADO':
			$texto = "Kardex creado";
			break;
		case 'MSJ_KARDEX_ACTUALIZADO':
			$texto = "Kardex actualizado";
			break;
		case 'MSJ_ARTICULO_CREADO':
			$texto = "Articulo creado";
			break;
		case 'MSJ_ARTICULO_ACTUALIZADO':
			$texto = "Articulo actualizado";
			break;
		case 'MSJ_ARTICULO_ELIMINADO':
			$texto = "Articulo eliminado";
			break;
		case 'MSJ_ARTICULO_NO_CREADO':
			$texto = "No se pudo crear articulo";
			break;
		case 'MSJ_EXAMEN_CREADO':
			$texto = "Examen creado";
			break;
		case 'MSJ_EXAMEN_ACTUALIZADO':
			$texto = "Examen actualizado";
			break;
		case 'MSJ_EXAMEN_ELIMINADO':
			$texto = "Examen eliminado";
			break;
		case 'MSJ_EXAMEN_NO_CREADO':
			$texto = "No se pudo crear el examen";
			break;
		case 'MSJ_INFUSION_CREADA':
			$texto = "Líquido endovenoso creado";
			break;
		case 'MSJ_INFUSION_ACTUALIZADA':
			$texto = "Liquido endovenoso actualizado";
			break;
		case 'MSJ_INFUSION_ELIMINADA':
			$texto = "Liquido endovenoso eliminado";
			break;
		case 'MSJ_INFUSION_NO_CREADA':
			$texto = "No se pudo crear el liquido endovenoso";
			break;
		case 'MSJ_MEDICO_ASOCIADO':
			$texto = "Medico asociado";
			break;
		case 'MSJ_MEDICO_RETIRADO':
			$texto = "Medico retirado";
			break;
		case 'MSJ_DIETA_ASOCIADA':
			$texto = "Dieta asociada";
			break;
		case 'MSJ_DIETA_RETIRADA':
			$texto = "Dieta retirada";
			break;
		case 'MSJ_ARCHIVO_CARGADO':
			$texto = "Archivo cargado";
			break;
		case 'MSJ_SUSPENDER_MEDICAMENTO':
			$texto = "Articulo suspendido";
			break;
		case 'MSJ_ACTIVAR_MEDICAMENTO':
			$texto = "Articulo activado";
			break;
		case 'MSJ_SUPENSION_NO_MODIFICADA':
			$texto = "Estado de suspension no modificado";
			break;
		case 'MSJ_ARTICULO_MODIFICADO_DESDE_PERFIL':
			$texto = "Articulo modificado desde el perfil farmacologico";
			break;
		case 'MSJ_ARTICULO_NO_MODIFICADO_DESDE_PERFIL':
			$texto = "Articulo no pudo ser modificado desde el perfil farmacologico";
			break;
		case 'MSJ_ARTICULO_REEMPLAZADO_DESDE_PERFIL':
			$texto = "Articulo ha sido reemplazado desde el perfil farmacologico";
			break;
		case 'MSJ_KARDEX_DESAPROBADO':
			$texto = "Kardex no aprobado por parte del regente";
			break;
		case 'MSJ_KARDEX_APROBADO':
			$texto = "Kardex aprobado por parte del regente";
			break;
		case 'MSJ_ALERGIA_MODIFICADA':
			$texto = "Alergia modificada";
			break;
		case 'MSJ_ESQUEMA_GRABADO':
			$texto = "Esquema de insulina grabado";
			break;
		case 'MSJ_ESQUEMA_ELIMINADO':
			$texto = "Esquema de insulina eliminado";
			break;
		case 'MSJ_CANTIDAD_CTC_MODIFICADA':
			$texto = "Cantidad CTC modificada";
			break;
		case 'MSJ_CANTIDAD_CTC_CREADA':
			$texto = "Cantidad CTC creada";
			break;
		case 'MSJ_CANTIDAD_CTC_NO_ALTERADA':
			$texto = "No se pudo crear cantidad CTC";
			break;
		case 'MSJ_ORDEN_CREADA':
			$texto = "Orden creada";
			break;
		case 'MSJ_ORDEN_NO_CREADA':
			$texto = "No se pudo crear la orden";
			break;
		case 'MSJ_ORDEN_CANCELADA':
			$texto = "La orden ha sido cancelada";
			break;
		case 'MSJ_OBSERVACIONES_ORDEN_MODIFICADAS':
			$texto = "Las observaciones de la orden han sido modificadas";
			break;
		case 'MSJ_ARTICULO_APROBADO':
			$texto = "Articulo aprobado";
			break;
		case 'MSJ_ARTICULO_NO_APROBADO':
			$texto = "No se pudo aprobar el articulo";
			break;
		case 'MSJ_SUSPENDIDO_AUTOMATICAMENTE_PERFIL':
			$texto = "Suspendido automaticamente por reemplazo desde el perfil";
			break;
		default:
			$texto = "Mensaje no especificado";
			break;
	}
	return $texto;
}
/***************************************************************************
 * Prepara el comodin espacio por '%' de sql
 ***************************************************************************/
function prepararCriterio($criterio){
	return str_replace(" ","%",$criterio);
}
/***************************************************************************
 *
 ***************************************************************************/
function inicializarAccionesPestana(){
	$acciones = new AccionPestanaDTO();

	//Inicializacion con todas las acciones
	$acciones->nroPestana = "";
	$acciones->codigoAccion = "";
	$acciones->nombreAccion = "";
	$acciones->crear = true;
	$acciones->leer = true;
	$acciones->borrar = true;
	$acciones->actualizar = true;

	return $acciones;
}

/***************************************************************************
 * FUNCIONES DE VISTAS
 ***************************************************************************/
//Genera la convencion de los articulos
function generarListaProtocolos($nombreCampo,$codigoUsuario,$codigoCco,$filtro = '%'){
	
	global $conex;
	global $wbasedato;

	// Se consulta la especialidad del médico
	$sql =  " SELECT Medesp
				FROM {$wbasedato}_000048
			   WHERE Meduma = '".$codigoUsuario."'
				 AND Medest = 'on'
			";
	$resmed = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$nummed = mysql_num_rows( $resmed );
	$rowmed = mysql_fetch_array($resmed);
	
	$especialidad = "";
	
	if($nummed>0)
	{
		$str_especialidad = explode("-",$rowmed['Medesp']);
		$especialidad = $str_especialidad[0];
	}
	
	$sql = "SELECT
					Pronom, Dprpes
				FROM
					".$wbasedato."_000137, ".$wbasedato."_000138
				WHERE
						Procod = Dprpro 
					AND Protip = 'Ordenes'
					AND Proest = 'on'
					AND
					(   Dprpes = 'Medicamentos'
					 OR Dprpes = 'Procedimientos'
					)
					AND
					(   Promed = '".$codigoUsuario."'
					 OR Promed = '*'
					)
					AND
					(   Proesp = '".$especialidad."'
					 OR Proesp = '*'
					)
					AND
					(   Procco = '".$codigoCco."'
					 OR Procco = '*'
					)
				ORDER BY Pronom ASC
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	$Pestanas = array();
	
	//Agrupo todo en una pestaña
	if( $numrows > 0 )
	{
		while($rows = mysql_fetch_array($res))
		{
			$Pestanas[ $rows[ 'Pronom' ] ][ $rows[ 'Dprpes' ] ] = 1;
		}
	}

	
	$listaProtocolo .= "<select name='".$nombreCampo."' id='".$nombreCampo."'>";	
	
	$listaProtocolo .= "<option value='' selected> -- seleccione el protocolo -- </option>";
	
	if( count( $Pestanas ) > 0 ){
	
		foreach( $Pestanas as $key => $value ){
			if( $filtro == "%" ){
				$listaProtocolo .= "<option value='".$key."'>".$key."</option>";
			}
			else if( isset( $value[ $filtro ] ) ){
				$listaProtocolo .= "<option value='".$key."'>".$key."</option>";
			}
		}
	}
	
	$listaProtocolo .= "</select>";
	return $listaProtocolo;
}

/***************************************************************************
 * FUNCIONES DE VISTAS
 ***************************************************************************/
//Genera la convencion de los articulos

//Genera la convencion de los articulos
function vista_generarConvencion($listaProtocolo){
	echo "<div align='center'>";
	echo "<table width='100%' align='center'>";
	echo "<tr align=center>";
	echo "<td align='left' valign='bottom' style='font-size:10pt'><b>Nota:</b> Los articulos con saldo no pueden ser eliminados";
	echo "<br /><br />".$listaProtocolo." <input type='button' name='btnImport' value='Importar protocolo' onclick='eleccionMedicamento(1)'>"; 
	echo "</td>";
	/*
	echo "<td class='fila1' width=80>Normal</td>";
	echo "<td class='fila2' width=80>Normal</td>";
	echo "<td class='suspendido' width=80>Suspendido</td>";
	echo "<td class='articuloControl' width=80>Control</td>";
	echo "<td class='fondoAlertaConfirmar' width=80 style='text-decoration: blink'>Sin confirmar</td>";	//Marzo 7 de 2011
	echo "<td class='fondoAlertaEliminar' width=80 style='text-decoration: blink'>Inactivo en el<br>Maestro</td>";	//Abril 1 de 2011
	echo "<td class='esCompuesta' width=80>Multidosis</td>";	//Octubre 29 de 2012
	echo "<td width=200>&nbsp;</td>";
	*/
	echo "</tr>";
	echo "</table>";
	echo "</div>";
	echo "<br>";
}

// function vista_generarConvencion($listaProtocolo){
	// echo "<div align='center'>";
	// echo "<table width='100%' align='center'>";
	// echo "<tr align=center>";
	// echo "<td align='left' valign='bottom' style='font-size:10pt'>";
	// //echo "<b>Nota:</b> Los articulos con saldo no pueden ser eliminados";
	// echo "<div style='display:none'>br /><br />".$listaProtocolo." <input type='button' name='btnImport' value='Importar protocolo</div>' onclick='eleccionMedicamento(1)'>"; 
	// echo "</td>";
	// /*
	// echo "<td class='fila1' width=80>Normal</td>";
	// echo "<td class='fila2' width=80>Normal</td>";
	// echo "<td class='suspendido' width=80>Suspendido</td>";
	// echo "<td class='articuloControl' width=80>Control</td>";
	// echo "<td class='fondoAlertaConfirmar' width=80 style='text-decoration: blink'>Sin confirmar</td>";	//Marzo 7 de 2011
	// echo "<td class='fondoAlertaEliminar' width=80 style='text-decoration: blink'>Inactivo en el<br>Maestro</td>";	//Abril 1 de 2011
	// echo "<td class='esCompuesta' width=80>Multidosis</td>";	//Octubre 29 de 2012
	// */
	// echo "<td width=200>&nbsp;</td>";
	// echo "</tr>";
	// echo "</table>";
	// echo "</div>";
	// echo "<br>";
// }


//Despliega la lista de articulos de acuerdo a los tipos de protocolos
function vista_desplegarListaArticulos($colDetalle,$cantidadElementos,$tipoProtocolo,$esEditable,$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro,$accionesPestana,$indicePestana){
	global $grupoControl;
	global $wfecha;
	global $topePorcentualCtc;

	global $conex;
	global $wbasedato;
	global $usuario;		//Información de usuario
	
	global $codigoServicioFarmaceutico;
	
	global $regletaFamilia;
	
	global $paciente;

	//Detalle
	$contArticulos = 0;
	$clase = 'fila1';
	$clasef = 'fila2';
	$mostrarArticulo = false;
	$tipoProtocoloAux = "N";
	
	switch($tipoProtocolo){
		case 'N':
			echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='$cantidadElementos'/>";
			break;
		case 'A':
			echo "<input type='HIDDEN' name='elementosAnalgesia' id=elementosAnalgesia value='$cantidadElementos'/>";
			break;
		case 'U':
			echo "<input type='HIDDEN' name='elementosNutricion' id=elementosNutricion value='$cantidadElementos'/>";
			break;
		case 'Q':
			echo "<input type='HIDDEN' name='elementosQuimioterapia' id=elementosQuimioterapia value='$cantidadElementos'/>";
			break;
		default:
			echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='$cantidadElementos'/>";
			break;
	}

	echo "<table align='center' border='0' id='tbDetalleAdd$tipoProtocolo'>";

	/////////////////////////////////////////
	// Encabezado articulos agregados
	echo "<tr align='center' class='encabezadoTabla' id='trEncabezadoTbAdd' style='display:none;'>";
	$tipoProtocolo="N";
	echo "<td>Acciones</td>";
	echo "<td>Imprimir</td>";
	echo "<td>Manejo<br />Interno</td>";
	echo "<td>Medicamento<span class='obligatorio'>(*)</span></td>";
	echo "<td style='display:none'>Protocolo</td>";
	echo "<td style='display:none'>No enviar</td>";
	echo "<td>Dosis a aplicar<span class='obligatorio'>(*)</span></td>";
	echo "<td>Cantidad</td>";
	echo "<td>Via<span class='obligatorio'>(*)</span></td>";
	echo "<td>Posolog&iacutea</td>";
	echo "<td>Unidad Posolog&iacutea</td>";
	echo "<td>Frecuencia<span class='obligatorio'>(*)</span></td>";
	echo "<td style='display:none'>Fecha y hora inicio<span class='obligatorio'>(*)</span></td>";
	echo "<td>Dias tto.</td>";
	echo "<td>Condici&oacute;n</td>";
	echo "<td style='display:none'>Cnf.</td>";
	echo "<td>Observaciones</td>";
	//echo "<td>Traer<br />Resumen</td>";
	echo "</tr>";
	/////////////////////////////////////////

	echo "<tbody id='detKardexAdd$tipoProtocolo'>";
	echo "</tbody>";

	echo "</table>";

	
	echo "<table align='center' border='0' id='tbDetalle$tipoProtocolo'>";
	echo "<tbody id='detKardex$tipoProtocolo'>";

	// if(isset($cantidadElementos) && $cantidadElementos > 0)	
	// {
		// echo "<tr align='center' class='encabezadoTabla' id='trEncabezadoTb$tipoProtocolo' style='display:none;'>";
		// $tipoProtocolo="N";
		// //echo "<td>Acciones</td>";
		// echo "<td>Medicamento</td>";
		// echo "<td>02</td>";
		// echo "<td>04</td>";
		// echo "<td>06</td>";
		// echo "<td>08</td>";
		// echo "<td>10</td>";
		// echo "<td>12</td>";
		// echo "<td>14</td>";
		// echo "<td>16</td>";
		// echo "<td>18</td>";
		// echo "<td>20</td>";
		// echo "<td>22</td>";
		// echo "<td>24</td>";
		// echo "</tr>";
	// }
	
	$aux = $esEditable;
	$auxFamilia = "";
	$contFamilia = 1;
	$contArtsFam = 1;
	
	foreach ($colDetalle as $articulo){
		
		$esEditable = $aux;
		$aElminiar = false;
		
		if( $articulo->estadoArticulo != "on" && $articulo->origen == $codigoServicioFarmaceutico ){
			$esEditable = false;
			$aElminiar = true;
		}

		// Inicio cambio de familia de medicamentos
		if($articulo->codigoFamilia!=$auxFamilia)
		{

			if($contArtsFam==0)
			{
				$actFamilia = $contFamilia-1;
				echo "<script> cambiarDisplay('filaTituloFamilia".$tipoProtocolo.$actFamilia."'); cambiarDisplay('trEncabezadoTb".$tipoProtocolo.$actFamilia."');  </script>";
			}

			$contArtsFam = 0;
			
			if($clasef == 'fila2')
				$clasef = 'fila1';
			else
				$clasef = 'fila2';

			if(isset($regletaFamilia[$articulo->codigoFamilia]['esCompuestaFamilia']) && $regletaFamilia[$articulo->codigoFamilia]['esCompuestaFamilia']=='1')
				$clasef = 'esCompuesta';
			
			if($contFamilia>1)
			{
				//echo "</table>";
				echo "</div>";
				//echo "</td></tr>";
				echo "</tbody>";
				//echo "<tr><td colspan='13'>&nbsp;</td></tr>";
			}
			 
			//echo "<tr class='".$clasef."' id='filaTituloFamilia".$tipoProtocolo.$contFamilia."' style='cursor: pointer;display:none;' onclick='mostrarDetalleFlia(this,\"detKardex".$tipoProtocolo.$contFamilia."\",\"divDetalle".$tipoProtocolo.$contFamilia."\",\"filaTituloFamilia".$tipoProtocolo.$contFamilia."\",\"".$clasef."\");'><td><b>".$articulo->codigoFamilia." - ".$articulo->nombreFamilia."</b></td>";

			// echo "<td colspan='8'>";
			// echo "<table><tr>";
			//print_r($regletaFamilia[$articulo->codigoFamilia]);
			/*********************************************************
			 * 	GRAFICA DE LOS HORARIOS DE SUMINISTRO DE FAMILIAS.
			 *
			 *
			 * 1.  Si la fecha de inicio es menor o igual a la de consulta del kardex se muestra la info
			 * 2.  Si se encuentra suspendido no se muestra la grafica
			 *********************************************************/
			//Grafica de los horarios ... 24 horas del dia.  Debe convertirse a horas cada periodicidad

			$horaArranque = 2;

			$cont3 = 1;
			$cont4 = $horaArranque;   //Desplazamiento desde la hora inicial
			//$claseGrafica = "fondoVerde";

			// while($cont3 <= 24)
			// {
				// if(isset($regletaFamilia[$articulo->codigoFamilia][$cont4]['valor']) && $regletaFamilia[$articulo->codigoFamilia][$cont4]['valor'] > 0){
					// echo "<td class='msg_tooltip' title='".$regletaFamilia[$articulo->codigoFamilia][$cont4]['tooltip']."' align='center' onMouseOver='mostrarTooltip(this, $cont4)' width='100'>";
					// echo $regletaFamilia[$articulo->codigoFamilia][$cont4]['valor']." ".$regletaFamilia[$articulo->codigoFamilia][$cont4]['unidad'];
					// echo "</td>";
				// } else {
					// echo "<td class='msg_tooltip' title='".$regletaFamilia[$articulo->codigoFamilia][$cont4]['tooltip']."' onMouseOver='mostrarTooltip(this, $cont4)' width='50'>&nbsp;</td>";
				// }

				// if($cont4 == 24){
					// $cont4 = 0;
				// }

				// $cont3++;
				// $cont4++;

				// if($cont4 % 2 != 0){
					// $cont4++;
				// }
				// if($cont3 % 2 != 0){
					// $cont3++;
				// }

				// if($cont4 == $horaArranque){
					// break;
				// }
			// }
			// //echo "</tr></table>";


			//echo "</td>";
			//echo "</tr>";
			echo "<tbody id='detKardex$tipoProtocolo$contFamilia'>";
			//echo "<tr><td colspan='13'>";
			echo "<div id='divDetalle$tipoProtocolo$contFamilia'>";
			//echo "<table align='center'>";

			$contFamilia++;

			if($contFamilia==2)
			{
				// Inicio encabezado articulos
				echo "<tr align='center' class='encabezadoTabla' id='trEncabezadoTb$tipoProtocolo$contFamilia'>";

				$tipoProtocolo="N";
				if($esEditable){
					echo "<td>";
					echo "Acciones";
					
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.15' id='wacc$tipoProtocolo.15' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."15"])."'>";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.2' id='wacc$tipoProtocolo.2' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."2"])."'>";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.3' id='wacc$tipoProtocolo.3' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"])."'>";
					echo "</td>";
					
					echo "<td>";
					echo "Imprimir";
					echo "<INPUT TYPE='hidden' name='wchkint$tipoProtocolo.16' id='wchkint$tipoProtocolo.16' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."16"])."'>";
					echo "</td>";
					
					echo "<td>";
					echo "Manejo<br />Interno";
					echo "<INPUT TYPE='hidden' name='wchkint$tipoProtocolo.16' id='wchkint$tipoProtocolo.16' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."16"])."'>";
					echo "</td>";
					
					echo "<td>Medicamento<span class='obligatorio'>(*)</span></td>";
					echo "<td style='display:none'>Protocolo</td>";
					echo "<td style='display:none'>";
					echo "No enviar";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.4' id='wacc$tipoProtocolo.4' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."4"])."'>";
					echo "</td>";
					echo "<td>";
					echo "Dosis a aplicar<span class='obligatorio'>(*)</span>";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.5' id='wacc$tipoProtocolo.5' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."5"])."'>";
					echo "</td>";
					echo "<td>";
					echo "Cantidad</td>";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.13' id='wacc$tipoProtocolo.13' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."13"])."'>";
					echo "<td>";
					echo "Via<span class='obligatorio'>(*)</span>";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.8' id='wacc$tipoProtocolo.8' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."8"])."'>";
					echo "</td>";
					echo "<td>";
					echo "Posolog&iacute;a";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.17' id='wacc$tipoProtocolo.17' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."17"])."'>";
					echo "</td>";
					echo "<td>";
					echo "Unidad Posolog&iacute;a";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.18' id='wacc$tipoProtocolo.18' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."18"])."'>";
					echo "</td>";
					echo "<td>";
					echo "Frecuencia<span class='obligatorio'>(*)</span>";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.7' id='wacc$tipoProtocolo.7' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."7"])."'>";
					echo "</td>";
					echo "<td style='display:none'>";
					echo "Fecha y hora inicio<span class='obligatorio'>(*)</span>";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.9' id='wacc$tipoProtocolo.9' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."9"])."'>";
					echo "</td>";
					echo "<td style='display:none'>";
					echo "Cnf.";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.11' id='wacc$tipoProtocolo.11' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."11"])."'>";
					echo "</td>";
					echo "<td>";
					echo "Dias tto.";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.12' id='wacc$tipoProtocolo.12' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."12"])."'>";
					echo "</td>";
					echo "<td>";
					echo "Condici&oacute;n";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.10' id='wacc$tipoProtocolo.10' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."10"])."'>";
					echo "</td>";
					echo "<td>";
					echo "Observaciones";
					echo "<INPUT TYPE='hidden' name='wacc$tipoProtocolo.14' id='wacc$tipoProtocolo.14' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."14"])."'>";
					echo "</td>";
					/*
					if($esEditable)
					{
						echo "<td>";
						echo "Traer<br />Resumen";
						echo "<INPUT TYPE='hidden' name='wchkint$tipoProtocolo.16' id='wchkint$tipoProtocolo.16' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."16"])."'>";
						echo "</td>";
					}
					*/

				} else {
					echo "<td>Acciones</td>";
					echo "<td>Imprimir</td>";
					echo "<td>Manejo<br />Interno</td>";
					echo "<td width='100px'>Articulo</td>";
					echo "<td style='display:none'>Protocolo</td>";
					echo "<td style='display:none'>No enviar</td>";
					echo "<td>Dosis a aplicar</td>";
					echo "<td>Cantidad</td>";
					echo "<td>Via</td>";
					echo "<td>Frecuencia</td>";
					echo "<td>Posolog&iacute;a</td>";
					echo "<td>Unidad Posolog&iacute;a</td>";
					echo "<td style='display:none'>Fecha y hora inicio</td>";
					echo "<td style='display:none'>Cnf.</td>";
					echo "<td>Dias tto.</td>";
					echo "<td>Condici&oacute;n</td>";
					echo "<td>Observaciones</td>";
					/*
					if($esEditable)
						echo "<td>Traer<br />Resumen</td>";
					*/
					// echo "<td>Manejo<br />Interno</td>";

				}
				echo "</tr>";
				// Fin encabezado articulos
			}
		}	// Fin cambio de familia de medicamentos

		$auxFamilia = $articulo->codigoFamilia;
		
		
		//Mirar si esto funciona!
		$articulo->tipoProtocolo = $tipoProtocoloAux;
		
		$mostrarArticulo = true;
		if($mostrarArticulo){
			
			$claseEliminar = "";
			if( $aElminiar ){
				$claseEliminar = "class='fondoAlertaEliminar'";
			}
			
			if($articulo->suspendido == 'on'){
				$clase = 'suspendido';
			}else{
				if($clase == 'fila2'){
					$clase = 'fila1';
				} else {
					$clase = 'fila2';
				}

				//Si el articulo es del grupo de control se resalta con color morado
				if($articulo->grupo == $grupoControl){
					$clase = "articuloControl";
				}
			}

			//Informacion adicional del articulo
			$informacion = "<strong>$articulo->codigoArticulo</strong><br>";
			
			$informacion .= "<br><strong>Nombre Comercial: </strong>$articulo->nombreGenerico<br>";

			$informacion .= "<br>$articulo->maximoUnidadManejo ".descripcionMaestroPorCodigo($colUnidades,$articulo->unidadMaximoManejo)." POR ".descripcionMaestroPorCodigo($colUnidades,$articulo->unidadManejo)."</strong><br><br>";

			//Ctc
			if(isset($articulo->cantidadAutorizadaCtc) && !empty($articulo->cantidadAutorizadaCtc)){
				$miniClase = "";
				$diferenciaCtc = intval($articulo->cantidadAutorizadaCtc)-intval($articulo->cantidadUtilizadaCtc);
				$porcentajeUsoCtc = ($articulo->cantidadUtilizadaCtc/$articulo->cantidadAutorizadaCtc)*100;

				$informacion .= "<br><strong>ESTADO DE CTC</strong><br>";
				$informacion .= "Autorizado: $articulo->cantidadAutorizadaCtc<br>";
				$informacion .= "Utilizado	: $articulo->cantidadUtilizadaCtc<br>";
				if($diferenciaCtc > 0){
					$miniClase = "fondoVerde";
				} else {
					$miniClase = "fondoRojo";
				}

				//Alerta si se llega al tope
				if( false && $porcentajeUsoCtc >= $topePorcentualCtc){
					$miniClase = "fondoRojo";
					mensajeEmergente("El articulo ".trim($articulo->codigoArticulo)." está a punto de agotarse o se agotó por CTC.  Utilización: ".intval($porcentajeUsoCtc)."%");
				}
				$informacion .= "<span class=$miniClase><strong>DISPONIBLE</strong>: $diferenciaCtc</span><br>";
				$informacion .= "<br>";
			}

			if($articulo->esDispensable == 'on'){
				$informacion .= "-Es dispensable<br>";
			} else {
				$informacion .= "-No es dispensable<br>";
			}

			if($articulo->esDuplicable == 'on'){
				$informacion .= "-Es duplicable<br>";
			} else {
				$informacion .= "-No es duplicable<br>";
			}

			if($articulo->diasTotalesTto > 0){
				$informacion .= "-Dias acumulados: ".$articulo->diasTotalesTto."<br>";
			} else {
				$informacion .= "-Dias acumulados: 0<br>";
			}

			if($articulo->dosisTotalesTto > 0){
				$informacion .= "-Dosis acumuladas: ".$articulo->dosisTotalesTto."<br>";
			} else {
				$informacion .= "-Dosis acumuladas: 0<br>";
			}
			
			$filaVisible = "";
			if($articulo->altaArticulo=="on")
				$filaVisible = " style='display:none;'";
			else
				$contArtsFam++;
			
			echo "<tr id='trFil".$contArticulos."' title=' - ".$informacion."' class='".$clase."'".$filaVisible.">";
			
			if( $esEditable ){
				$eventosQuitarTooltip = " onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'";	//Creo los eventos que quitan el tooltip si el kardex es editable
			}

			//Acciones			
			echo "<td align=center $eventosQuitarTooltip>";
			if($esEditable && $articulo->permiteModificar){
				
				$tieneSaldo = tieneSaldo( $conex, $wbasedato, $articulo->historia, $articulo->ingreso, $articulo->consultarCodigoArticulo() );
				
				$usuario->permisosPestanas[ $indicePestana ]['modifica'];
				
				$puedeEliminar = true;
				if( $usuario->permisosPestanas[ $indicePestana ]['modifica'] ){
				
					if( $articulo->codigoCreador != $usuario->codigo ){
						$puedeEliminar = false;
					}
				}

				if( $puedeEliminar && !$tieneSaldo ){
					crearCampo("4","",@$accionesPestana[$indicePestana.".$tipoProtocolo"."2"],array("onClick"=>"javascript:quitarArticulo($contArticulos,'$articulo->tipoProtocolo',this);"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' />");
					
					echo "&nbsp;";
				}

				crearCampo("4","",@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"],array("onClick"=>"javascript:suspenderArticulo($contArticulos,'$articulo->tipoProtocolo');"),"<img src='../../images/medical/root/suspender.png' border='0' width='17' height='17' />");

			} else {
				echo "&nbsp;";
			}
			echo "</td>";
			
			
			$str_codigo_articulo = explode("-",$articulo->codigoArticulo);
			$codigo_articulo = $str_codigo_articulo[0];
			
			
			if($articulo->imprimirArticulo=='on')
				$chkImp = "checked";
			else
				$chkImp = "unchecked";
			
			//Imprimir
			echo "<td align='center' $eventosQuitarTooltip>";
			if($esEditable){
			
				// crearCampo("5","wchkint$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."16"],array($chkImp=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');marcarManejoInterno(this,'".$articulo->historia."','".$articulo->ingreso."','".$codigo_articulo."','".$articulo->fechaKardex."','".$articulo->fechaInicioAdministracion."','".$articulo->horaInicioAdministracion."');"),"");
				crearCampo("5","wchkimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"], array($chkImp=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
				//					echo "<input type='checkbox' name='wchkconf$articulo->tipoProtocolo$contArticulos' alt='Confirmar preparacion' disabled onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
			}else{
				// crearCampo("5","wchkimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"], array($chkImp=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
				crearCampo("5","whistchkimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"], array($chkImp=>"","onChange"=>"javascript:marcarImpresionHist( this,'".$articulo->historia."','".$articulo->ingreso."','".$codigo_articulo."','".$articulo->fechaKardex."','".$articulo->idOriginal."' );"),"");
				
				// // if($articulo->manejoInterno == 'on'){
					// // echo "Si";
				// // }else{
					// // echo "No";
				// // }
			}
			echo "</td>";
			
			
			if($articulo->manejoInterno=='on')
				$chkInt = "checked";
			else
				$chkInt = "unchecked";
				
			// Manejo Interno
			echo "<td align='center' $eventosQuitarTooltip>";
			if($esEditable){
				crearCampo("5","wchkint$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."16"],array($chkInt=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');marcarManejoInterno(this,'".$articulo->historia."','".$articulo->ingreso."','".$codigo_articulo."','".$articulo->fechaKardex."','".$articulo->fechaInicioAdministracion."','".$articulo->horaInicioAdministracion."');"),"");
				//					echo "<input type='checkbox' name='wchkconf$articulo->tipoProtocolo$contArticulos' alt='Confirmar preparacion' disabled onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
			}else{
				if($articulo->manejoInterno == 'on'){
					echo "Si";
				}else{
					echo "No";
				}
			}
			echo "</td>";
			

			//Articulo
			echo "<td $claseEliminar>";
			if($esEditable){
				echo "<div id='wnmmed$articulo->tipoProtocolo$contArticulos'>$articulo->codigoArticulo</div>";
			} else {
				echo $articulo->codigoArticulo;
			}
			echo "</td>";

			//Nombre del protocolo
			echo "<td align=center style='display:none'>";
			if($esEditable){
				echo $articulo->nombreProtocolo;
			}
			echo "</td>";
			
			//No dispensar
			echo "<td align=center style='display:none' $eventosQuitarTooltip>";
			if($esEditable){
				if($articulo->estadoAdministracion == 'on'){
					crearCampo("5","wchkdisp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."4"],array("checked"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
					//					echo "<input type='checkbox' name='wchkdisp$articulo->tipoProtocolo$contArticulos' id='wchkdisp$articulo->tipoProtocolo$contArticulos' alt='No dispensar' checked onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				} else {
					crearCampo("5","wchkdisp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."4"],array("unchecked"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
					//					echo "<input type='checkbox' name='wchkdisp$articulo->tipoProtocolo$contArticulos' id='wchkdisp$articulo->tipoProtocolo$contArticulos' alt='No dispensar' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				}
			} else {
				if($articulo->estaConfirmado == 'on'){
					echo "Si";
				} else {
					echo "No";
				}
			}
			echo "</td>";

			//Dosis y unidad de medida
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				// 2012-06-27
				// Se adiciona el atributo "readonly" para que este campo siempre sea de solo lectura, que no se pueda modificar
				crearCampo("1","wdosis$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."5"],array("size"=>"7","maxlength"=>"7","class"=>"campo2","readonly"=>"readonly","onKeyPress"=>"return validarEntradaDecimal(event)","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$articulo->cantidadDosis");
				echo "<input type='hidden' name='wdosisori$articulo->tipoProtocolo$contArticulos' id='wdosisori$articulo->tipoProtocolo$contArticulos' value='$articulo->cantidadDosis' />";
				//				echo "<INPUT TYPE='text' NAME='wdosis$articulo->tipoProtocolo$contArticulos' id='wdosis$articulo->tipoProtocolo$contArticulos' size=7 maxlength=7 onkeypress='return validarEntradaDecimal(event);' class='campo2' value='$articulo->cantidadDosis' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				$opcionesSeleccion = "";
				foreach ($colUnidades as $unidad){
					if(trim($unidad->codigo) == trim($articulo->unidadDosis)){
						$opcionesSeleccion .= "<option value='".$unidad->codigo."' selected>$unidad->descripcion</option>";
					}
				}
				crearCampo("6","wudosis$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."6"],array("style"=>"width:120","class"=>"seleccion","disabled"=>""),"$opcionesSeleccion");
				echo "<input type='hidden' name='wudosisori$articulo->tipoProtocolo$contArticulos' id='wudosisori$articulo->tipoProtocolo$contArticulos' value='$articulo->unidadDosis' />";
				//				echo "<select id='wudosis$articulo->tipoProtocolo$contArticulos' style='width:120' class='seleccion' disabled>";
				//				echo "<option value=''>Seleccione</option>";
				//				foreach ($colUnidades as $unidad){
				//					if(trim($unidad->codigo) == trim($articulo->unidadDosis)){
				//						echo "<option value='".$unidad->codigo."' selected>$unidad->descripcion</option>";
				//					}else{
				//						echo "<option value='".$unidad->codigo."'>$unidad->descripcion</option>";
				//					}
				//				}
				//				echo "</select>";

				$colFormasFarmaceuticas = consultarFormasFarmaceuticas();
				$opcionesSeleccion2 = "";
				foreach ($colFormasFarmaceuticas as $formasFticas){
					if(trim($formasFticas->codigo) == trim($articulo->formaFarmaceutica)){
						$opcionesSeleccion2 .= "<option value='".$formasFticas->codigo."' selected>$formasFticas->descripcion</option>";
					}
				}
				crearCampo("6","wfftica$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."6"],array("style"=>"width:120","class"=>"seleccion","disabled"=>""),"$opcionesSeleccion2");
				echo "<input type='hidden' name='wffticaori$articulo->tipoProtocolo$contArticulos' id='wffticaori$articulo->tipoProtocolo$contArticulos' value='$articulo->formaFarmaceutica' />";

				
			}else{
				echo "&nbsp;".$articulo->cantidadDosis." ";

				foreach ($colUnidades as $unidad){
					if($unidad->codigo == $articulo->unidadDosis){
						echo $unidad->descripcion;
					}
				}
				echo " (S)";
			}
			echo "</td>";

			//Unidad de manejo
			if($articulo->permiteModificar){
				echo "<INPUT TYPE='hidden' name='wmodificado$articulo->tipoProtocolo$contArticulos' id='wmodificado$articulo->tipoProtocolo$contArticulos' value='N'>";
			}
			echo "<INPUT TYPE='hidden' NAME='whcmanejo$articulo->tipoProtocolo$contArticulos' id='whcmanejo$articulo->tipoProtocolo$contArticulos' value='$articulo->maximoUnidadManejo'>";
			echo "<INPUT TYPE='hidden' NAME='whvence$articulo->tipoProtocolo$contArticulos' id='whvence$articulo->tipoProtocolo$contArticulos' value='$articulo->vencimiento'>";
			echo "<INPUT TYPE='hidden' NAME='whdiasvence$articulo->tipoProtocolo$contArticulos' id='whdiasvence$articulo->tipoProtocolo$contArticulos' value='$articulo->diasVencimiento'>";
			echo "<INPUT TYPE='hidden' NAME='whdispensable$articulo->tipoProtocolo$contArticulos' id='whdispensable$articulo->tipoProtocolo$contArticulos' value='$articulo->esDispensable'>";
			echo "<INPUT TYPE='hidden' NAME='whduplicable$articulo->tipoProtocolo$contArticulos' id='whduplicable$articulo->tipoProtocolo$contArticulos' value='$articulo->esDuplicable'>";
			echo "<INPUT TYPE='hidden' name='wfftica$articulo->tipoProtocolo$contArticulos' id='wfftica$articulo->tipoProtocolo$contArticulos' value='$articulo->formaFarmaceutica'>";
			echo "<INPUT TYPE='hidden' name='wcundmanejo$articulo->tipoProtocolo$contArticulos' id='wcundmanejo$articulo->tipoProtocolo$contArticulos' value='$articulo->unidadManejo'>";
			
			echo "<INPUT TYPE='hidden' name='wido$articulo->tipoProtocolo$contArticulos' id='wido$articulo->tipoProtocolo$contArticulos' value='".$articulo->idOriginal."'>";
			echo "<INPUT TYPE='hidden' name='wespos$articulo->tipoProtocolo$contArticulos' id='wespos$articulo->tipoProtocolo$contArticulos' value='".( $articulo->esPos ? "on":"off" )."'>";
			echo "<INPUT TYPE='hidden' name='wtienectc$articulo->tipoProtocolo$contArticulos' id='wtienectc$articulo->tipoProtocolo$contArticulos' value='".( $articulo->tieneCTC( $conex, $wbasedato ) ? "on":"off" )."'>";
			//$paciente->historiaClinica, $paciente->ingresoHistoriaClinica;
			
			//Dosis máximas
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				if( empty( $articulo->diasTratamiento ) ){
					crearCampo("1","wdosmax$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."13"],array("size"=>"6","maxlength"=>"6","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","onKeyUp"=>"inhabilitarDiasTratamiento( this,'$articulo->tipoProtocolo', $contArticulos);"),"$articulo->dosisMaxima");
				}
				else{
					crearCampo("1","wdosmax$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."13"],array("size"=>"6","maxlength"=>"6","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","readOnly"=>"","onKeyUp"=>"inhabilitarDiasTratamiento( this,'$articulo->tipoProtocolo', $contArticulos);"),"$articulo->dosisMaxima");
				}
			}else{
				echo $articulo->dosisMaxima;
			}
			echo "</td>";

			//Via administracion
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				$opcionesSeleccion = "<option value=''>Seleccione</option>";
				foreach ($colVias as $via){
					if($via->codigo == $articulo->via){
						$opcionesSeleccion .= "<option value='".$via->codigo."' selected>$via->descripcion</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$via->codigo."'>$via->descripcion</option>";
					}
				}
				crearCampo("6","wviadmon$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."8"],array("class"=>"seleccion","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$opcionesSeleccion");
				//				echo "<select name='wviadmon$articulo->tipoProtocolo$contArticulos' id='wviadmon$articulo->tipoProtocolo$contArticulos' class='seleccion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				//				echo "<option value=''>Seleccione</option>";
				//
				//				foreach ($colVias as $via){
				//					if($via->codigo == $articulo->via){
				//						echo "<option value='".$via->codigo."' selected>$via->descripcion</option>";
				//					}else{
				//						echo "<option value='".$via->codigo."'>$via->descripcion</option>";
				//					}
				//				}
				//				echo "</select>";
			}else{
				foreach ($colVias as $via){
					if($via->codigo == $articulo->via){
						echo $via->descripcion;
					}
				}
			}
			echo "</td>";

			
			//posolog&iacute;a
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				if( empty( $articulo->posologia ) ){
					crearCampo("1","wposologia$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."17"],array("size"=>"6","maxlength"=>"6","class"=>"campo2","onKeyPress"=>"return validarEntradaDecimal(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$articulo->posologia");
				}
				else{
					crearCampo("1","wposologia$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."17"],array("size"=>"6","maxlength"=>"6","class"=>"campo2","onKeyPress"=>"return validarEntradaDecimal(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","readOnly"=>""),"$articulo->posologia");
				}
			}else{
				echo $articulo->posologia;
			}
			echo "</td>";

			//Unidad posolog&iacute;a
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				if( empty( $articulo->unidadPosologia ) ){
					crearCampo("1","wunidadposologia$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."17"],array("size"=>"6","maxlength"=>"80","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","onKeyUp"=>"inhabilitarDiasTratamiento( this,'$articulo->tipoProtocolo', $contArticulos);"),"$articulo->unidadPosologia");
				}
				else{
					crearCampo("1","wunidadposologia$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."17"],array("size"=>"6","maxlength"=>"80","class"=>"campo2","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","readOnly"=>""),"$articulo->unidadPosologia");
				}
			}else{
				echo $articulo->unidadPosologia;
			}
			echo "</td>";

			
			//Periodicidad
			$equivalenciaPeriodicidad = 0;
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				
				$opcionesSeleccion = "<option value=''>Seleccione</option>";
				foreach ($colPeriodicidades as $periodicidad){
					if($periodicidad->codigo == $articulo->periodicidad){
						$opcionesSeleccion .= "<option value='".$periodicidad->codigo."' selected>$periodicidad->descripcion</option>";
						$equivalenciaPeriodicidad = $periodicidad->equivalencia;
					} else {
						$opcionesSeleccion .= "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
					}
				}
				
				
				//crearCampo("1","wperiod$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."7"],array("size"=>"17","class"=>"campo2","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$articulo->periodicidad");
				echo "<input type='hidden' name='wperiodori$articulo->tipoProtocolo$contArticulos' id='wperiodori$articulo->tipoProtocolo$contArticulos' value='$articulo->periodicidad' />";
								echo "<select name='wperiod$articulo->tipoProtocolo$contArticulos' id='wperiod$articulo->tipoProtocolo$contArticulos' class='seleccion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
								echo "<option value=''>Seleccione</option>";
				
								foreach ($colPeriodicidades as $periodicidad){
									if($periodicidad->codigo == $articulo->periodicidad){
										echo "<option value='".$periodicidad->codigo."' selected>$periodicidad->descripcion</option>";
										$equivalenciaPeriodicidad = $periodicidad->equivalencia;
									}else{
										echo "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
									}
								}
								echo "</select>";
			} else {
				
				foreach ($colPeriodicidades as $periodicidad){
					if($periodicidad->codigo == $articulo->periodicidad){
						echo $periodicidad->descripcion;
					}
				}
				
				$articulo->periodicidad;
			}
			echo "<input type='hidden' id='wequdosis$articulo->tipoProtocolo$contArticulos' value='$equivalenciaPeriodicidad'>";
			echo "</td>";

			//Fecha y hora inicio
			echo "<td style='display:none' $eventosQuitarTooltip>";
			if($esEditable){
				echo "<INPUT TYPE='hidden' NAME='whfinicio$articulo->tipoProtocolo$contArticulos' id='whfinicio$articulo->tipoProtocolo$contArticulos' SIZE=22 readonly class='campo2' value='$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion'>";

				echo "<INPUT TYPE='text' NAME='wfinicio$articulo->tipoProtocolo$contArticulos' id='wfinicio$articulo->tipoProtocolo$contArticulos' SIZE=25 readonly class='campo2' value='$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				crearCampo("3","btnFecha$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."9"],array("onClick"=>"javascript:calendario($contArticulos,'$articulo->tipoProtocolo');"),"*");
				echo "<input type='hidden' name='wfinicioori$articulo->tipoProtocolo$contArticulos' id='wfinicioori$articulo->tipoProtocolo$contArticulos' value='$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion' />";
				//				echo "<input type='button' id='btnFecha$articulo->tipoProtocolo$contArticulos' onclick='javascript:calendario($contArticulos,\"$articulo->tipoProtocolo\");' value='*'>";
			}else{
				echo "$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion";
			}
			echo "</td>";

			//Confirmacion
			echo "<td style='display:none' $eventosQuitarTooltip>";
			if($esEditable){
				if($articulo->origen == 'CM'){
					if($articulo->estaConfirmado == 'on'){
						crearCampo("5","wchkconf$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."11"],array("checked"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
						//						echo "<input type='checkbox' name='wchkconf$articulo->tipoProtocolo$contArticulos' id='wchkconf$articulo->tipoProtocolo$contArticulos' alt='Confirmar preparacion' checked onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
					} else {
						crearCampo("5","wchkconf$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."11"],array("unchecked"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
						//						echo "<input type='checkbox' name='wchkconf$articulo->tipoProtocolo$contArticulos' id='wchkconf$articulo->tipoProtocolo$contArticulos' alt='Confirmar preparacion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
					}
				} else {
					crearCampo("5","wchkconf$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."11"],array("unchecked"=>"","disabled"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
					//					echo "<input type='checkbox' name='wchkconf$articulo->tipoProtocolo$contArticulos' alt='Confirmar preparacion' disabled onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				}
			}else{
				if($articulo->estaConfirmado == 'on'){
					echo "Si";
				}else{
					echo "No";
				}
			}
			echo "</td>";

			//Dias tratamiento, debe mostrarse en un alt la fecha de terminación y los dias restantes
			if($articulo->diasTratamiento != ''){
				$vecFechaKardex = explode("-",$wfecha);

				$diasFaltantes = intval($articulo->diasTratamiento) - diasDiferenciaFechas($wfecha,$articulo->fechaInicioAdministracion);
				$fechaFinal = date("Y-m-d", mktime(0,0,0,$vecFechaKardex[1],$vecFechaKardex[2],(int)$vecFechaKardex[0]) + ($diasFaltantes*60*60*24));
			} else {
				$diasFaltantes = 0;
				$fechaFinal = "-";
			}

			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				if( empty( $articulo->dosisMaxima ) ){
					crearCampo("1","wdiastto$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."12"],array("size"=>"3","maxlength"=>"3","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","onKeyUp"=>"inhabilitarDosisMaxima( this,'$articulo->tipoProtocolo', $contArticulos );"),"$articulo->diasTratamiento");
				}
				else{
					crearCampo("1","wdiastto$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."12"],array("size"=>"3","maxlength"=>"3","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","readOnly"=>"","onKeyUp"=>"inhabilitarDosisMaxima( this,'$articulo->tipoProtocolo', $contArticulos );"),"$articulo->diasTratamiento");
				}

				// if($articulo->diasTratamiento != ""){
					// if($diasFaltantes < 0){
						// echo "<img src='../../images/medical/root/inactivo.gif' alt='Cumplido'/>";
					// }else{
						// echo "<img src='../../images/medical/root/activo.gif' alt='Hasta: $fechaFinal - dias restantes: $diasFaltantes'/>";
					// }
				// }
			}else{
				echo $articulo->diasTratamiento;
			}
			echo "</td>";

			//Condicion de suministro
			echo "<td onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'>";
			if($esEditable){
				$opcionesSeleccion = "<option value=''>Seleccione</option>";
				foreach ($colCondicionesSuministro as $condicion){
					if($condicion->codigo == $articulo->condicionSuministro){
						$opcionesSeleccion .= "<option value='".$condicion->codigo."' selected>$condicion->descripcion</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
					}
				}
				crearCampo("6","wcondicion$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."10"],array("class"=>"seleccion","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos', this );"),"$opcionesSeleccion");

				//				echo "<select name='wcondicion$articulo->tipoProtocolo$contArticulos' id='wcondicion$articulo->tipoProtocolo$contArticulos' class='seleccion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				//				echo "<option value=''>Seleccione</option>";
				//
				//				foreach ($colCondicionesSuministro as $condicion){
				//					if($condicion->codigo == $articulo->condicionSuministro){
				//						echo "<option value='".$condicion->codigo."' selected>$condicion->descripcion</option>";
				//					} else {
				//						echo "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
				//					}
				//				}
				//				echo "</select>";
			}else{
				foreach ($colCondicionesSuministro as $condicion){
					if($condicion->codigo == $articulo->condicionSuministro){
						echo $condicion->descripcion;
					}
				}
			}
			echo "</td>";

			//Observaciones
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				crearCampo("2","wtxtobs$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."14"],array("cols"=>"40","rows"=>"2","onKeyPress"=>"return validarEntradaAlfabetica(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$articulo->observaciones");
				//				echo "<textarea id='wtxtobs$articulo->tipoProtocolo$contArticulos' rows=2 cols=10 onkeypress='return validarEntradaAlfabetica(event);' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				//				echo $articulo->observaciones;
				//				echo "</textarea>";
			} else {
				crearCampo("2","wtxtobs$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."14"],array("cols"=>"40","rows"=>"2","readonly"=>""),"$articulo->observaciones");
				//				echo "<textarea id='wtxtobs$articulo->tipoProtocolo$contArticulos' rows=2 cols=10 readonly>";
				//				echo $articulo->observaciones;
				//				echo "</textarea>";
			}
			echo "<input type='hidden' name='wtxtobsori$articulo->tipoProtocolo$contArticulos' id='wtxtobsori$articulo->tipoProtocolo$contArticulos' value='".htmlspecialchars( $articulo->observaciones, ~ENT_COMPAT )."' />";
			echo "</td>";

			// Traer observaciion
			/*
			if($esEditable){
				echo "<td align='center' $eventosQuitarTooltip>";
				crearCampo("5","wchkobs$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."16"],array("onClick"=>"traeJustificacionHCE(this,'wtxtobs$articulo->tipoProtocolo$contArticulos');"),"");
				echo "</td>";
			}
			*/

			
			/*********************************************************
			 * 	GRAFICA DE LOS HORARIOS DE SUMINISTRO DE MEDICAMENTOS.
			 *
			 *
			 * 1.  Si la fecha de inicio es menor o igual a la de consulta del kardex se muestra la info
			 * 2.  Si se encuentra suspendido no se muestra la grafica
			 *********************************************************/
			//Grafica de los horarios ... 24 horas del dia.  Debe convertirse a horas cada periodicidad
			foreach ($colPeriodicidades as $periodicidad){
				if($periodicidad->codigo == $articulo->periodicidad){
					$horasPeriodicidad = intval($periodicidad->equivalencia);
					break;
				}
			}

			$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),$articulo->fechaInicioAdministracion,$articulo->horaInicioAdministracion,$horasPeriodicidad);
			
			$horaArranque = 2;
			$aplicaGraficaSuministro = true;

			$cont1 = 1;
			$cont2 = $horaArranque;   //Desplazamiento desde la hora inicial
			$caracterMarca = "*";
			$claseGrafica = "";

			$articulo->suspendido == 'on' ? $claseGrafica = "suspendido" : $claseGrafica = "fondoVerde";

			/*
			while($cont1 <= 24){
				if(isset($arrAplicacion[$cont2]) && $arrAplicacion[$cont2] == $caracterMarca && $aplicaGraficaSuministro){
					echo "<td class='$claseGrafica' align='center' onMouseOver='mostrarTooltip(this, $cont2)'>";
					echo $caracterMarca;
					echo "</td>";
				} else {
					echo "<td onMouseOver='mostrarTooltip(this, $cont2)'>&nbsp;</td>";
				}

				if($cont2 == 24){
					$cont2 = 0;
				}

				$cont1++;
				$cont2++;

				if($cont2 % 2 != 0){
					$cont2++;
				}
				if($cont1 % 2 != 0){
					$cont1++;
				}

				if($cont2 == $horaArranque){
					break;
				}
			}
			*/
			echo "</tr>";
			
			/**********************************************************************************************************************************************************
			 * Diciembre 15 de 2011
			 *
			 * Si falta un 80% o menos de los días de tratamiento, sale un mensaje
			 * diciendo cuanto falta para terminar el medicamento por dias de tratamiento
			 **********************************************************************************************************************************************************/
			if( false && $articulo->diasTratamiento != '' && $articulo->diasTratamiento > 0 ){
				if($esEditable){
					//Calculo total de tiempo transcurrido desde la fecha de inicio del medicamento
					//hasta el actual
					$tiempoTranscurrido = intval( ( strtotime( date( "Y-m-d" )." 00:00:00" ) - strtotime( $articulo->fechaInicioAdministracion." 00:00:00" ) )/(24*3600) )+1;

					if( $tiempoTranscurrido >= intval( intval($articulo->diasTratamiento)*$topePorcentualCtc/100 ) ){
						mensajeEmergente( "El articulo ".trim($articulo->codigoArticulo)." le faltan ".intval( intval( intval($articulo->diasTratamiento) ) - $tiempoTranscurrido )." día(s) más tratamiento ");
					}
				}
			}
			/**********************************************************************************************************************************************************/
			
			$contArticulos++;
		}
	}

	if($contArtsFam==0)
	{
		$actFamilia = $contFamilia-1;
		echo "<script> cambiarDisplay('filaTituloFamilia".$tipoProtocolo.$actFamilia."'); cambiarDisplay('trEncabezadoTb".$tipoProtocolo.$actFamilia."');  </script>";
	}
	
	if($contFamilia>1)
	{
		//echo "</table>";
		echo "</div>";
		//echo "</td></tr>";
		//echo "<tr><td colspan='24'>&nbsp;</td></tr>";
		echo "</tbody>";
	}

	echo "</tbody>";
	echo "</table>";
	echo "<p>&nbsp;</p>";
}


//Despliega la lista de articulos de acuerdo a los tipos de protocolos
function vista_desplegarListaArticulosAlta($colDetalle,$cantidadElementos,$tipoProtocolo,$esEditable,$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro,$accionesPestana,$indicePestana){
	global $grupoControl;
	global $wfecha;
	global $topePorcentualCtc;

	global $conex;
	global $wbasedato;
	global $usuario;		//Información de usuario
	
	global $codigoServicioFarmaceutico;
	
	global $regletaFamilia;

	//Detalle
	$contArticulos = 0;
	$clase = 'fila1';
	$clasef = 'fila2';
	$mostrarArticulo = false;
	$tipoProtocoloAux = "N";
	
	// switch($tipoProtocolo){
		// case 'N':
			// echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='$cantidadElementos'/>";
			// break;
		// case 'A':
			// echo "<input type='HIDDEN' name='elementosAnalgesia' id=elementosAnalgesia value='$cantidadElementos'/>";
			// break;
		// case 'U':
			// echo "<input type='HIDDEN' name='elementosNutricion' id=elementosNutricion value='$cantidadElementos'/>";
			// break;
		// case 'Q':
			// echo "<input type='HIDDEN' name='elementosQuimioterapia' id=elementosQuimioterapia value='$cantidadElementos'/>";
			// break;
		// default:
			// echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='$cantidadElementos'/>";
			// break;
	// }

	echo "<table align='center' border='0' id='tbDetalleAddImp$tipoProtocolo'>";

	/////////////////////////////////////////
	// Encabezado articulos agregados
	echo "<tr align='center' class='encabezadoTabla' id='trEncabezadoTbAddImp' style='display:none;'>";
	$tipoProtocolo="N";
	echo "<td>Acciones</td>";
	echo "<td>Medicamento<span class='obligatorio'>(*)</span></td>";
	echo "<td style='display:none'>Protocolo</td>";
	echo "<td style='display:none'>No enviar</td>";
	echo "<td>Dosis a aplicar<span class='obligatorio'>(*)</span></td>";
	echo "<td>Frecuencia<span class='obligatorio'>(*)</span></td>";
	echo "<td style='display:none'>Fecha y hora inicio<span class='obligatorio'>(*)</span></td>";
	echo "<td>Cantidad<span class='obligatorio'>(*)</span></td>";
	echo "<td style='display:none'>Dias tto.</td>";
	echo "<td style='display:none'>Condici&oacute;n</td>";
	echo "<td style='display:none'>Cnf.</td>";
	echo "<td style='display:none'>Dosis máx.</td>";
	echo "<td>Observaciones</td>";
	echo "</tr>";
	/////////////////////////////////////////

	echo "<tbody id='detKardexAddImp$tipoProtocolo'>";
	echo "</tbody>";

	echo "</table>";

	
	echo "<table align='center' border='0' id='tbDetalleImp$tipoProtocolo'>";
	echo "<tbody id='detKardexImp$tipoProtocolo'>";

	$aux = $esEditable;

	// Inicio encabezado articulos
	echo "<tr align='center' class='encabezadoTabla' id='trEncabezadoTbImp$tipoProtocolo'>";

	$tipoProtocolo="N";
	if($esEditable){
		echo "<td>";
		echo "Quitar";
		
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.15' id='waccimp$tipoProtocolo.15' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."15"])."'>";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.2' id='waccimp$tipoProtocolo.2' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."2"])."'>";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.3' id='waccimp$tipoProtocolo.3' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"])."'>";
		echo "</td>";
		echo "<td>Medicamento<span class='obligatorio'>(*)</span></td>";
		echo "<td style='display:none'>Protocolo</td>";
		echo "<td style='display:none'>";
		echo "No enviar";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.4' id='waccimp$tipoProtocolo.4' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."4"])."'>";
		echo "</td>";
		echo "<td>";
		echo "Dosis a aplicar<span class='obligatorio'>(*)</span>";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.5' id='waccimp$tipoProtocolo.5' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."5"])."'>";
		echo "</td>";
				echo "<td style='display:none'>";
		echo "Dosis máx.</td>";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.13' id='waccimp$tipoProtocolo.13' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."13"])."'>";
		echo "<td>";
echo "<td>";
		echo "Frecuencia<span class='obligatorio'>(*)</span>";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.7' id='waccimp$tipoProtocolo.7' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."7"])."'>";
		echo "</td>";
		echo "<td>";
		echo "Cantidad<span class='obligatorio'>(*)</span>";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.8' id='waccimp$tipoProtocolo.8' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."8"])."'>";
		echo "</td>";
		echo "<td>";
		echo "Fecha y hora inicio<span class='obligatorio'>(*)</span>";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.9' id='waccimp$tipoProtocolo.9' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."9"])."'>";
		echo "</td>";
		echo "<td style='display:none'>";
		echo "Dias tto.";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.12' id='waccimp$tipoProtocolo.12' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."12"])."'>";
		echo "</td>";
		echo "<td style='display:none'>";
		echo "Condici&oacute;n";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.10' id='waccimp$tipoProtocolo.10' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."10"])."'>";
		echo "</td>";
		echo "<td style='display:none'>";
		echo "Cnf.";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.11' id='waccimp$tipoProtocolo.11' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."11"])."'>";
		echo "</td>";
		echo "<td>";
		echo "Observaciones";
		echo "<INPUT TYPE='hidden' name='waccimp$tipoProtocolo.14' id='waccimp$tipoProtocolo.14' value='".accionesATexto(@$accionesPestana[$indicePestana.".$tipoProtocolo"."14"])."'>";
		echo "</td>";
	} else {
		echo "<td>Imprimir</td>";
		echo "<td width='100px'>Articulo</td>";
		echo "<td style='display:none'>Protocolo</td>";
		echo "<td style='display:none'>No enviar</td>";
		echo "<td>Dosis a aplicar</td>";
		echo "<td style='display:none'>Dosis máx.</td>";
		echo "<td>Frecuencia</td>";
		echo "<td>Cantidad</td>";
		echo "<td style='display:none'>Fecha y hora inicio</td>";
		echo "<td style='display:none'>Dias tto.</td>";
		echo "<td style='display:none'>Condici&oacute;n</td>";
		echo "<td style='display:none'>Cnf.</td>";
		echo "<td>Observaciones</td>";
	}
	echo "</tr>";
	// Fin encabezado articulos
	
	
	foreach ($colDetalle as $articulo){
		
		$esEditable = $aux;
		$aElminiar = false;
		
		if( $articulo->estadoArticulo != "on" && $articulo->origen == $codigoServicioFarmaceutico ){
			$esEditable = false;
			$aElminiar = true;
		}
		
		//Mirar si esto funciona!
		$articulo->tipoProtocolo = $tipoProtocoloAux;
		
		$mostrarArticulo = true;
		if($mostrarArticulo)
		{
			
			$str_codigo_articulo = explode('-',$articulo->codigoArticulo);
			$codigo_articulo = $str_codigo_articulo[0];
			
			$qfam = "SELECT
						Famnom
					FROM
						{$wbasedato}_000114 a, {$wbasedato}_000115 b
					WHERE
						Relart = '$codigo_articulo'
						AND Relest = 'on'
						AND Relfam = Famcod
						AND Famest = 'on'
					";
		
			$resfam = mysql_query( $qfam, $conex ) or die( mysql_errno()." - Error en el query $qfam - ".mysql_error() );
			$numfam = mysql_num_rows( $resfam );
			$rowfam = mysql_fetch_array($resfam);
			$nombre_familia = $rowfam['Famnom'];		

			$claseEliminar = "";
			if( $aElminiar ){
				$claseEliminar = "class='fondoAlertaEliminar'";
			}
			
			if($articulo->suspendido == 'on'){
				$clase = 'suspendido';
			}else{
				if($clase == 'fila2'){
					$clase = 'fila1';
				} else {
					$clase = 'fila2';
				}

				//Si el articulo es del grupo de control se resalta con color morado
				if($articulo->grupo == $grupoControl){
					$clase = "articuloControl";
				}
			}

			//Informacion adicional del articulo
			$informacion = "<strong>$articulo->codigoArticulo</strong><br>";
			
			$informacion .= "<br><strong>Nombre Comercial: </strong>$articulo->nombreGenerico<br>";

			$informacion .= "<br>$articulo->maximoUnidadManejo ".descripcionMaestroPorCodigo($colUnidades,$articulo->unidadMaximoManejo)." POR ".descripcionMaestroPorCodigo($colUnidades,$articulo->unidadManejo)."</strong><br><br>";

			//Ctc
			if(isset($articulo->cantidadAutorizadaCtc) && !empty($articulo->cantidadAutorizadaCtc)){
				$miniClase = "";
				$diferenciaCtc = intval($articulo->cantidadAutorizadaCtc)-intval($articulo->cantidadUtilizadaCtc);
				$porcentajeUsoCtc = ($articulo->cantidadUtilizadaCtc/$articulo->cantidadAutorizadaCtc)*100;

				$informacion .= "<br><strong>ESTADO DE CTC</strong><br>";
				$informacion .= "Autorizado: $articulo->cantidadAutorizadaCtc<br>";
				$informacion .= "Utilizado	: $articulo->cantidadUtilizadaCtc<br>";
				if($diferenciaCtc > 0){
					$miniClase = "fondoVerde";
				} else {
					$miniClase = "fondoRojo";
				}

				//Alerta si se llega al tope
				if( false && $porcentajeUsoCtc >= $topePorcentualCtc){
					$miniClase = "fondoRojo";
					mensajeEmergente("El articulo ".trim($articulo->codigoArticulo)." está a punto de agotarse o se agotó por CTC.  Utilización: ".intval($porcentajeUsoCtc)."%");
				}
				$informacion .= "<span class=$miniClase><strong>DISPONIBLE</strong>: $diferenciaCtc</span><br>";
				$informacion .= "<br>";
			}

			if($articulo->esDispensable == 'on'){
				$informacion .= "-Es dispensable<br>";
			} else {
				$informacion .= "-No es dispensable<br>";
			}

			if($articulo->esDuplicable == 'on'){
				$informacion .= "-Es duplicable<br>";
			} else {
				$informacion .= "-No es duplicable<br>";
			}

			if($articulo->diasTotalesTto > 0){
				$informacion .= "-Dias acumulados: ".$articulo->diasTotalesTto."<br>";
			} else {
				$informacion .= "-Dias acumulados: 0<br>";
			}

			if($articulo->dosisTotalesTto > 0){
				$informacion .= "-Dosis acumuladas: ".$articulo->dosisTotalesTto."<br>";
			} else {
				$informacion .= "-Dosis acumuladas: 0<br>";
			}
			
			echo "<tr id='trFilImp".$contArticulos."' class='".$clase."'>";
			
			if( $esEditable ){
				$eventosQuitarTooltip = " onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'";	//Creo los eventos que quitan el tooltip si el kardex es editable
			}

			//Acciones			
			echo "<td align=center $eventosQuitarTooltip>";
			if($esEditable && $articulo->permiteModificar){
				
				$tieneSaldo = tieneSaldo( $conex, $wbasedato, $articulo->historia, $articulo->ingreso, $articulo->consultarCodigoArticulo() );
				
				$usuario->permisosPestanas[ $indicePestana ]['modifica'];
				
				$puedeEliminar = true;
				if( $usuario->permisosPestanas[ $indicePestana ]['modifica'] ){
				
					if( $articulo->codigoCreador != $usuario->codigo ){
						$puedeEliminar = false;
					}
				}
				
				$chkimp = "checked";
				if($articulo->imprimirArticulo!='on')
					$chkimp = "";

				/*crearCampo("5","wchkimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"], array($chkimp=>"","style"=>"","onChange"=>"javascript:marcarImpresion(this,'".$articulo->historia."','".$articulo->ingreso."','$codigo_articulo','$articulo->fechaKardex','$articulo->fechaInicioAdministracion','$articulo->horaInicioAdministracion');"),"");*/
				
				crearCampo("5","wchkimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"], array($chkimp=>"","style"=>"display:none"),"");
				echo "<div id='trFilImp".$examen->numeroDeOrden."' style='display:inline' title='Click para no imprimir este medicamento'><img onClick='javascript:quitarFilaAlta(\"trFilImp".$contArticulos."\",\"wchkimp$articulo->tipoProtocolo$contArticulos\");marcarImpresion(\"quitar\",\"".$articulo->historia."\",\"".$articulo->ingreso."\",\"$codigo_articulo\",\"$articulo->fechaKardex\",\"$articulo->fechaInicioAdministracion\",\"$articulo->horaInicioAdministracion\");' src='../../images/medical/root/borrar.png' width='17' height='17' border='0'/> &nbsp;&nbsp; </div>";
				
				
			} else {
				echo "&nbsp;";
			}
			echo "<input type='hidden' name='wimp$articulo->tipoProtocolo$contArticulos' id='wimp$articulo->tipoProtocolo$contArticulos' value='$articulo->altaArticulo' />";			
			echo "</td>";

			//Articulo
			echo "<td $claseEliminar>";
			echo "<div id='wnmmedimp$articulo->tipoProtocolo$contArticulos'>$nombre_familia</div>";
			echo "</td>";

			//Nombre del protocolo
			echo "<td align=center style='display:none'>";
			if($esEditable){
				echo $articulo->nombreProtocolo;
			}
			echo "</td>";
			
			//No dispensar
			echo "<td align=center $eventosQuitarTooltip  style='display:none'>";
			if($esEditable){
				if($articulo->estadoAdministracion == 'on'){
					crearCampo("5","wchkdispimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."4"],array("checked"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
					//					echo "<input type='checkbox' name='wchkdisp$articulo->tipoProtocolo$contArticulos' id='wchkdisp$articulo->tipoProtocolo$contArticulos' alt='No dispensar' checked onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				} else {
					crearCampo("5","wchkdispimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."4"],array("unchecked"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
					//					echo "<input type='checkbox' name='wchkdisp$articulo->tipoProtocolo$contArticulos' id='wchkdisp$articulo->tipoProtocolo$contArticulos' alt='No dispensar' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				}
			} else {
				if($articulo->estaConfirmado == 'on'){
					echo "Si";
				} else {
					echo "No";
				}
			}
			echo "</td>";

			//Dosis y unidad de medida
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				// 2012-06-27
				// Se adiciona el atributo "readonly" para que este campo siempre sea de solo lectura, que no se pueda modificar
				crearCampo("1","wdosisimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."5"],array("size"=>"7","maxlength"=>"7","class"=>"campo2","onKeyPress"=>"return validarEntradaDecimal(event)","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$articulo->cantidadDosis");
				echo "<input type='hidden' name='wdosisori$articulo->tipoProtocolo$contArticulos' id='wdosisori$articulo->tipoProtocolo$contArticulos' value='$articulo->cantidadDosis' />";
				//				echo "<INPUT TYPE='text' NAME='wdosis$articulo->tipoProtocolo$contArticulos' id='wdosis$articulo->tipoProtocolo$contArticulos' size=7 maxlength=7 onkeypress='return validarEntradaDecimal(event);' class='campo2' value='$articulo->cantidadDosis' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				$opcionesSeleccion = "";
				foreach ($colUnidades as $unidad){
					if(trim($unidad->codigo) == trim($articulo->unidadDosis))
						$opcionesSeleccion .= "<option value='".$unidad->codigo."' selected>$unidad->descripcion</option>";
					else
						$opcionesSeleccion .= "<option value='".$unidad->codigo."'>$unidad->descripcion</option>";					
				}
				crearCampo("6","wudosisimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."6"],array("style"=>"width:120","class"=>"seleccion","disabled"=>""),"$opcionesSeleccion");
				echo "<input type='hidden' name='wudosisori$articulo->tipoProtocolo$contArticulos' id='wudosisori$articulo->tipoProtocolo$contArticulos' value='$articulo->unidadDosis' />";
				//				echo "<select id='wudosis$articulo->tipoProtocolo$contArticulos' style='width:120' class='seleccion' disabled>";
				//				echo "<option value=''>Seleccione</option>";
				//				foreach ($colUnidades as $unidad){
				//					if(trim($unidad->codigo) == trim($articulo->unidadDosis)){
				//						echo "<option value='".$unidad->codigo."' selected>$unidad->descripcion</option>";
				//					}else{
				//						echo "<option value='".$unidad->codigo."'>$unidad->descripcion</option>";
				//					}
				//				}
				//				echo "</select>";

				$colFormasFarmaceuticas = consultarFormasFarmaceuticas();
				$opcionesSeleccion2 = "";
				foreach ($colFormasFarmaceuticas as $formasFticas){
					if(trim($formasFticas->codigo) == trim($articulo->formaFarmaceutica))
						$opcionesSeleccion2 .= "<option value='".$formasFticas->codigo."' selected>$formasFticas->descripcion</option>";
					else
						$opcionesSeleccion2 .= "<option value='".$formasFticas->codigo."'>$formasFticas->descripcion</option>";
				}
				crearCampo("6","wffticaimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."6"],array("style"=>"width:120","class"=>"seleccion","disabled"=>""),"$opcionesSeleccion2");
				echo "<input type='hidden' name='wffticaori$articulo->tipoProtocolo$contArticulos' id='wffticaori$articulo->tipoProtocolo$contArticulos' value='$articulo->formaFarmaceutica' />";
				
			}else{
				echo "&nbsp;".$articulo->cantidadDosis." ";

				foreach ($colUnidades as $unidad){
					if($unidad->codigo == $articulo->unidadDosis){
						echo $unidad->descripcion;
					}
				}
				echo " (S)";
			}
			echo "</td>";

			//Unidad de manejo
			if($articulo->permiteModificar){
				echo "<INPUT TYPE='hidden' name='wmodificadoimp$articulo->tipoProtocolo$contArticulos' id='wmodificadoimp$articulo->tipoProtocolo$contArticulos' value='N'>";
			}
			echo "<INPUT TYPE='hidden' NAME='whcmanejoimp$articulo->tipoProtocolo$contArticulos' id='whcmanejoimp$articulo->tipoProtocolo$contArticulos' value='$articulo->maximoUnidadManejo'>";
			echo "<INPUT TYPE='hidden' NAME='whvenceimp$articulo->tipoProtocolo$contArticulos' id='whvenceimp$articulo->tipoProtocolo$contArticulos' value='$articulo->vencimiento'>";
			echo "<INPUT TYPE='hidden' NAME='whdiasvenceimp$articulo->tipoProtocolo$contArticulos' id='whdiasvenceimp$articulo->tipoProtocolo$contArticulos' value='$articulo->diasVencimiento'>";
			echo "<INPUT TYPE='hidden' NAME='whdispensableimp$articulo->tipoProtocolo$contArticulos' id='whdispensableimp$articulo->tipoProtocolo$contArticulos' value='$articulo->esDispensable'>";
			echo "<INPUT TYPE='hidden' NAME='whduplicableimp$articulo->tipoProtocolo$contArticulos' id='whduplicableimp$articulo->tipoProtocolo$contArticulos' value='$articulo->esDuplicable'>";
			echo "<INPUT TYPE='hidden' name='wffticaimp$articulo->tipoProtocolo$contArticulos' id='wffticaimp$articulo->tipoProtocolo$contArticulos' value='$articulo->formaFarmaceutica'>";
			echo "<INPUT TYPE='hidden' name='wcundmanejoimp$articulo->tipoProtocolo$contArticulos' id='wcundmanejoimp$articulo->tipoProtocolo$contArticulos' value='$articulo->unidadManejo'>";

			//Dosis máximas
			echo "<td $eventosQuitarTooltip style='display:none'>";
			if($esEditable){
				if( empty( $articulo->diasTratamiento ) ){
					crearCampo("1","wdosmaximp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."13"],array("size"=>"6","maxlength"=>"6","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","onKeyUp"=>"inhabilitarDiasTratamiento( this,'$articulo->tipoProtocolo', $contArticulos);"),"$articulo->dosisMaxima");
				}
				else{
					crearCampo("1","wdosmaximp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."13"],array("size"=>"6","maxlength"=>"6","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","readOnly"=>"","onKeyUp"=>"inhabilitarDiasTratamiento( this,'$articulo->tipoProtocolo', $contArticulos);"),"$articulo->dosisMaxima");
				}
			}else{
				echo $articulo->dosisMaxima;
			}
			echo "&nbsp;</td>";

			//Periodicidad
			$equivalenciaPeriodicidad = 0;
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				$opcionesSeleccion = "<option value=''>Seleccione</option>";
				foreach ($colPeriodicidades as $periodicidad){
					if($periodicidad->codigo == $articulo->periodicidad){
						$opcionesSeleccion .= "<option value='".$periodicidad->codigo."' selected>$periodicidad->descripcion</option>";
						$equivalenciaPeriodicidad = $periodicidad->equivalencia;
					} else {
						$opcionesSeleccion .= "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
					}
				}
				crearCampo("6","wperiodimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."7"],array("class"=>"seleccion","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$opcionesSeleccion");
				echo "<input type='hidden' name='wperiodori$articulo->tipoProtocolo$contArticulos' id='wperiodori$articulo->tipoProtocolo$contArticulos' value='$articulo->periodicidad' />";
				//				echo "<select name='wperiod$articulo->tipoProtocolo$contArticulos' id='wperiod$articulo->tipoProtocolo$contArticulos' class='seleccion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				//				echo "<option value=''>Seleccione</option>";
				//
				//				foreach ($colPeriodicidades as $periodicidad){
				//					if($periodicidad->codigo == $articulo->periodicidad){
				//						echo "<option value='".$periodicidad->codigo."' selected>$periodicidad->descripcion</option>";
				//						$equivalenciaPeriodicidad = $periodicidad->equivalencia;
				//					}else{
				//						echo "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
				//					}
				//				}
				//				echo "</select>";
			} else {
				foreach ($colPeriodicidades as $periodicidad){
					if($periodicidad->codigo == $articulo->periodicidad){
						echo $periodicidad->descripcion;
					}
				}
			}
			echo "<input type='hidden' id='wequdosisimp$articulo->tipoProtocolo$contArticulos' value='$equivalenciaPeriodicidad'>";
			echo "</td>";

			//Cantidad ordenada al egreso
			echo "<td $eventosQuitarTooltip>";

			crearCampo("1","wcantaltaimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."8"],array("size"=>"14","class"=>"campo2","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$articulo->cantidadAlta");

			echo "<input type='hidden' id='wviadmonimp$articulo->tipoProtocolo$contArticulos' value='".$articulo->via."'>";
			//				echo "<select name='wviadmon$articulo->tipoProtocolo$contArticulos' id='wviadmon$articulo->tipoProtocolo$contArticulos' class='seleccion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
			//				echo "<option value=''>Seleccione</option>";
			//
			//				foreach ($colVias as $via){
			//					if($via->codigo == $articulo->via){
			//						echo "<option value='".$via->codigo."' selected>$via->descripcion</option>";
			//					}else{
			//						echo "<option value='".$via->codigo."'>$via->descripcion</option>";
			//					}
			//				}
			//				echo "</select>";

			echo "</td>";
				
			//Fecha y hora inicio
			echo "<td style='display:none' $eventosQuitarTooltip>";
			if($esEditable){
				echo "<INPUT TYPE='hidden' NAME='whfinicioimp$articulo->tipoProtocolo$contArticulos' id='whfinicioimp$articulo->tipoProtocolo$contArticulos' SIZE=22 readonly class='campo2' value='$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion'>";

				echo "<INPUT TYPE='text' NAME='wfinicioimp$articulo->tipoProtocolo$contArticulos' id='wfinicioimp$articulo->tipoProtocolo$contArticulos' SIZE=25 readonly class='campo2' value='$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				crearCampo("3","btnFechaimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."9"],array("onClick"=>"javascript:calendarioimp($contArticulos,'$articulo->tipoProtocolo');"),"*");
				
				echo "<input type='hidden' name='wfinicioori$articulo->tipoProtocolo$contArticulos' id='wfinicioori$articulo->tipoProtocolo$contArticulos' value='$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion' />";
				//				echo "<input type='button' id='btnFecha$articulo->tipoProtocolo$contArticulos' onclick='javascript:calendario($contArticulos,\"$articulo->tipoProtocolo\");' value='*'>";
			}else{
				echo "$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion";
			}
			echo "</td>";

			//Condicion de suministro
			echo "<td onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );' style='display:none'>";
			if($esEditable){
				$opcionesSeleccion = "<option value=''>Seleccione</option>";
				foreach ($colCondicionesSuministro as $condicion){
					if($condicion->codigo == $articulo->condicionSuministro){
						$opcionesSeleccion .= "<option value='".$condicion->codigo."' selected>$condicion->descripcion</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
					}
				}
				crearCampo("6","wcondicionimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."10"],array("class"=>"seleccion","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos', this );"),"$opcionesSeleccion");

				//				echo "<select name='wcondicion$articulo->tipoProtocolo$contArticulos' id='wcondicion$articulo->tipoProtocolo$contArticulos' class='seleccion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				//				echo "<option value=''>Seleccione</option>";
				//
				//				foreach ($colCondicionesSuministro as $condicion){
				//					if($condicion->codigo == $articulo->condicionSuministro){
				//						echo "<option value='".$condicion->codigo."' selected>$condicion->descripcion</option>";
				//					} else {
				//						echo "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
				//					}
				//				}
				//				echo "</select>";
			}else{
				foreach ($colCondicionesSuministro as $condicion){
					if($condicion->codigo == $articulo->condicionSuministro){
						echo $condicion->descripcion;
					}
				}
			}
			echo "&nbsp;</td>";

			//Confirmacion
			echo "<td $eventosQuitarTooltip style='display:none'>";
			if($esEditable){
				if($articulo->origen == 'CM'){
					if($articulo->estaConfirmado == 'on'){
						crearCampo("5","wchkconfimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."11"],array("checked"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
						//						echo "<input type='checkbox' name='wchkconf$articulo->tipoProtocolo$contArticulos' id='wchkconf$articulo->tipoProtocolo$contArticulos' alt='Confirmar preparacion' checked onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
					} else {
						crearCampo("5","wchkconfimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."11"],array("unchecked"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
						//						echo "<input type='checkbox' name='wchkconf$articulo->tipoProtocolo$contArticulos' id='wchkconf$articulo->tipoProtocolo$contArticulos' alt='Confirmar preparacion' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
					}
				} else {
					crearCampo("5","wchkconfimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."11"],array("unchecked"=>"","disabled"=>"","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"");
					//					echo "<input type='checkbox' name='wchkconf$articulo->tipoProtocolo$contArticulos' alt='Confirmar preparacion' disabled onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				}
			}else{
				if($articulo->estaConfirmado == 'on'){
					echo "Si";
				}else{
					echo "No";
				}
			}
			echo "</td>";

			//Dias tratamiento, debe mostrarse en un alt la fecha de terminación y los dias restantes
			if($articulo->diasTratamiento != ''){
				$vecFechaKardex = explode("-",$wfecha);

				$diasFaltantes = intval($articulo->diasTratamiento) - diasDiferenciaFechas($wfecha,$articulo->fechaInicioAdministracion);
				$fechaFinal = date("Y-m-d", mktime(0,0,0,$vecFechaKardex[1],$vecFechaKardex[2],(int)$vecFechaKardex[0]) + ($diasFaltantes*60*60*24));
			} else {
				$diasFaltantes = 0;
				$fechaFinal = "-";
			}

			echo "<td $eventosQuitarTooltip style='display:none'>";
			if($esEditable){
				if( empty( $articulo->dosisMaxima ) ){
					crearCampo("1","wdiasttoimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."12"],array("size"=>"3","maxlength"=>"3","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","onKeyUp"=>"inhabilitarDosisMaxima( this,'$articulo->tipoProtocolo', $contArticulos );"),"$articulo->diasTratamiento");
				}
				else{
					crearCampo("1","wdiasttoimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."12"],array("size"=>"3","maxlength"=>"3","class"=>"campo2","onKeyPress"=>"return validarEntradaEntera(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');","readOnly"=>"","onKeyUp"=>"inhabilitarDosisMaxima( this,'$articulo->tipoProtocolo', $contArticulos );"),"$articulo->diasTratamiento");
				}

				if($articulo->diasTratamiento != ""){
					if($diasFaltantes < 0){
						echo "<img src='../../images/medical/root/inactivo.gif' alt='Cumplido'/>";
					}else{
						echo "<img src='../../images/medical/root/activo.gif' alt='Hasta: $fechaFinal - dias restantes: $diasFaltantes'/>";
					}
				}
			}else{
				echo $articulo->diasTratamiento;
			}
			echo "</td>";

			//Observaciones
			echo "<td $eventosQuitarTooltip>";
			if($esEditable){
				crearCampo("2","wtxtobsimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."14"],array("cols"=>"40","rows"=>"2","onKeyPress"=>"return validarEntradaAlfabetica(event);","onChange"=>"javascript:marcarCambio('$articulo->tipoProtocolo','$contArticulos');"),"$articulo->observaciones");
				echo "<input type='hidden' name='wtxtobsori$articulo->tipoProtocolo$contArticulos' id='wtxtobsori$articulo->tipoProtocolo$contArticulos' value='".htmlspecialchars( $articulo->observaciones, ~ENT_COMPAT )."' />";
				
				//				echo "<textarea id='wtxtobs$articulo->tipoProtocolo$contArticulos' rows=2 cols=10 onkeypress='return validarEntradaAlfabetica(event);' onChange='javascript:marcarCambio(\"$articulo->tipoProtocolo\",\"$contArticulos\");'>";
				//				echo $articulo->observaciones;
				//				echo "</textarea>";
			} else {
				crearCampo("2","wtxtobsimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."14"],array("cols"=>"40","rows"=>"2","readonly"=>""),"$articulo->observaciones");
				//				echo "<textarea id='wtxtobs$articulo->tipoProtocolo$contArticulos' rows=2 cols=10 readonly>";
				//				echo $articulo->observaciones;
				//				echo "</textarea>";
			}
			
			echo "</td>";

			echo "</tr>";
			
			/**********************************************************************************************************************************************************
			 * Diciembre 15 de 2011
			 *
			 * Si falta un 80% o menos de los días de tratamiento, sale un mensaje
			 * diciendo cuanto falta para terminar el medicamento por dias de tratamiento
			 **********************************************************************************************************************************************************/
			if( false && $articulo->diasTratamiento != '' && $articulo->diasTratamiento > 0 ){
				if($esEditable){
					//Calculo total de tiempo transcurrido desde la fecha de inicio del medicamento
					//hasta el actual
					$tiempoTranscurrido = intval( ( strtotime( date( "Y-m-d" )." 00:00:00" ) - strtotime( $articulo->fechaInicioAdministracion." 00:00:00" ) )/(24*3600) )+1;

					if( $tiempoTranscurrido >= intval( intval($articulo->diasTratamiento)*$topePorcentualCtc/100 ) ){
						mensajeEmergente( "El articulo ".trim($articulo->codigoArticulo)." le faltan ".intval( intval( intval($articulo->diasTratamiento) ) - $tiempoTranscurrido )." día(s) más tratamiento ");
					}
				}
			}
			/**********************************************************************************************************************************************************/
			
			$contArticulos++;
		}
	}

	echo "</tbody>";
	echo "</table>";
	echo "<p>&nbsp;</p>";
}



//Realiza el despliegue de la lista de articulos en el historial
function vista_desplegarListaArticulosHistorial($colDetalle,$tipoProtocolo,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias,$esEditable){
	global $grupoControl;

	global $centroCostosServicioFarmaceutico;
	global $centroCostosCentralMezclas;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;

	global $usuario;		//Información de usuario
	
	global $wbasedato;
	
	global $conex;

	//Muestra el detalle de medicamentos del kardex consultado
	echo '<span class="subtituloPagina2" align="center">';
	echo "Detalle de articulos anteriores";
	echo "</span><br><br>";

	$cont1=0;
	$fechaTmp = "";
	$clase = "";
	$mostrarArticulo = false;

	$contArticulos = -1;
	foreach ($colDetalle as $articulo){

		$contArticulos++;
		
		$mostrarArticulo = true;

		if($mostrarArticulo){
			$cumplido = false;

			if($fechaTmp != $articulo->fecha){
				//Seccion nueva del acordeon
				if($cont1 > 0){
					echo "</table>";
					echo "</p>";
					echo "</div>";
				}
				echo "<a href='#null' onclick=javascript:intercalarMedicamentoAnterior('$articulo->fecha',\"$tipoProtocolo\");>$articulo->fecha</a></br>";
				echo "<div id='med$tipoProtocolo$articulo->fecha' style='display:none'>";

				echo "<p>";
				echo "<table align='center' border=0>";

				echo "<tr align='center' class='encabezadoTabla'>";

				//Encabezado detalle kardex
				echo "<td>Imprimir</td>";
				echo "<td>Articulo</td>";
				echo "<td>Dosis</td>";
				echo "<td>Posología</td>";
				echo "<td>Periodicidad</td>";
				echo "<td>Condicion</td>";
				echo "<td>Forma farmaceutica</td>";
				echo "<td>Via</td>";
				echo "<td>Dias tto.</td>";
				echo "<td>Susp.</td>";
				echo "<td>Observaciones</td>";

				echo "</tr>";
			}

			if($clase == "fila1"){
				$clase = "fila2";
			} else {
				$clase = "fila1";
			}

			if($articulo->grupo == $grupoControl){
				$clase = "articuloControl";
			}
			
			//Septiembre 20 de 2012
			if($articulo->suspendido == 'on'){
				$clase = "suspendido";
			}

			echo "<tr id='tr$cont1' class='$clase'>";

			//Fecha del kardex
			$fechaTmp = $articulo->fecha;

			//Boton de Imprimir
			echo "<td align='center' $eventosQuitarTooltip>";
			if($esEditable){
				$existeKardexActual = existeArticuloEnKardex($conex, $wbasedato,$articulo->historia,$articulo->ingreso,$articulo->consultarCodigoArticulo(),date("Y-m-d"),$articulo->idOriginal);
				if( !$existeKardexActual ){
					crearCampo("5","whistchkimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"], array($chkImp=>"","onChange"=>"javascript:marcarImpresionHist( this,'".$articulo->historia."','".$articulo->ingreso."','".$articulo->consultarCodigoArticulo()."','".$articulo->fecha."','".$articulo->idOriginal."' );"),"");
				}
				
				echo "<INPUT TYPE='hidden' name='whistido$articulo->tipoProtocolo$contArticulos' id='whistido$articulo->tipoProtocolo$contArticulos' value='".$articulo->idOriginal."'>";
				echo "<INPUT TYPE='hidden' name='whistespos$articulo->tipoProtocolo$contArticulos' id='whistespos$articulo->tipoProtocolo$contArticulos' value='".( $articulo->esPos ? "on":"off" )."'>";
				echo "<INPUT TYPE='hidden' name='whisttienectc$articulo->tipoProtocolo$contArticulos' id='whisttienectc$articulo->tipoProtocolo$contArticulos' value='".( $articulo->tieneCTC( $conex, $wbasedato ) ? "on":"off" )."'>";
			}
			else{
				crearCampo("5","whistchkimp$articulo->tipoProtocolo$contArticulos",@$accionesPestana[$indicePestana.".$tipoProtocolo"."3"], array($chkImp=>"","onChange"=>"javascript:marcarImpresionHist( this,'".$articulo->historia."','".$articulo->ingreso."','".$articulo->consultarCodigoArticulo()."','".$articulo->fecha."','".$articulo->idOriginal."' );"),"");
			}
			echo "</td>";
			
			
			//Articulo
			if($cumplido){
				echo "<td>".$articulo->nombreGenerico." - C</td>";
			} else {
				echo "<td>".$articulo->nombreGenerico."</td>";
			}

			//Dosis y unidad de medida
			echo "<td>";
			echo $articulo->cantidadDosis." ";

			foreach ($colUnidades as $unidad){
				if($unidad->codigo == $articulo->unidadDosis){
					echo $unidad->descripcion;
				}
			}
			echo " (S)";
			echo "</td>";
			
			
			//Posología y unidad de posología
			echo "<td>";
			echo $articulo->posologia." ";
			
			$unidadesPosologia = consultarUnidadesMedidaPosologia();
			
			foreach( $unidadesPosologia as $key => $value ){
				if( $value->codigo == $articulo->unidadPosologia ){
					echo $value->descripcion;
					break;
				}
			}
			
			echo "</td>";
			

			//Periodicidad
			echo "<td>";
			foreach ($colPeriodicidades as $periodicidad){
				if($periodicidad->codigo == $articulo->periodicidad){
					echo $periodicidad->descripcion;
				}
			}
			echo "</td>";

			//Condicion
			echo "<td>";
			foreach ($colCondicionesSuministro as $condicion){
				if($condicion->codigo == $articulo->condicionSuministro){
					echo $condicion->descripcion;
				}
			}
			echo "</td>";

			//Forma farmaceutica
			echo "<td>";

			foreach ($colFormasFarmaceuticas as $formasFarmaceutica){
				if($formasFarmaceutica->codigo == $articulo->formaFarmaceutica){
					echo $formasFarmaceutica->descripcion;
				}
			}

			echo "</td>";

			//Via administracion
			echo "<td>";
			foreach ($colVias as $via){
				if($via->codigo == $articulo->via){
					echo $via->descripcion;
				}
			}
			echo "</td>";

			//Dias tratamiento
			echo "<td>";
			echo $articulo->diasTratamiento;
			echo "</td>";
			
			//Suspendido o no
			echo "<td>";
			if($articulo->suspendido == 'on'){
				echo "Si";
			}else{
				echo "No";
			}
			echo "</td>";

			//Observaciones
			echo "<td>";
			// Octubre 11 de 2013
			echo '<div style="overflow:auto;width:270px;height:60px;background:#fff;border:1px #999 solid;font-size:9pt;text-align:left;">'.$articulo->observaciones.'</div>';
			echo "<input type='hidden' id='wtxtobs$cont1' value='".$articulo->observaciones."'>";
			echo "</td>";
			
			echo "</tr>";

			$cont1++;
		}
	}
	echo "</table>";
	echo "</div>";
}



function vista_desplegarListaArticulosHistorial111($colDetalle,$tipoProtocolo,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias){
	global $grupoControl;

	global $centroCostosServicioFarmaceutico;
	global $centroCostosCentralMezclas;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;

	global $usuario;		//Información de usuario
	
	global $topePorcentualCtc;

	global $regletaFamiliaHist;

	//Muestra el detalle de medicamentos del kardex consultado
	echo '<span class="subtituloPagina2" align="center">';
	echo "Detalle de articulos anteriores";
	echo "</span><br><br>";

	$cont1=0;
	$fechaTmp = "";
	$clase = "";
	$mostrarArticulo = false;

	$auxFamilia = "";
	$contFamilia = 1;

	foreach ($colDetalle as $articulo){

		/*
		 $mostrarArticulo = ((isset($articulo->grupo) && $articulo->grupo != '' && strpos($gruposCco,$articulo->grupo) !== false) || $gruposCco == "*");

		 if($esCM){
			if($articulo->origen == $codigoCentralMezclas){
			$mostrarArticulo = true;
			} else {
			$mostrarArticulo = false;
			}
			}*/

		$mostrarArticulo = true;

		if($mostrarArticulo){
			$cumplido = false;

			
			if($articulo->codigoFamilia!=$auxFamilia && $contFamilia>1)
			{
				echo "</table></div></td></tr></tbody>";
			}
			
			
			if($fechaTmp != $articulo->fecha){
				//Seccion nueva del acordeon
				if($cont1 > 0){
					echo "</table>";
					echo "</p>";
					echo "</div>";
				}
				echo "<a href='#null' onclick=javascript:intercalarMedicamentoAnterior('$articulo->fecha',\"$tipoProtocolo\");>$articulo->fecha</a></br>";
				echo "<div id='medAnt$articulo->fecha' style='display:none'>";

				echo "<p>";
				echo "<table align='center' border=0>";

				echo "<tr align='center' class='encabezadoTabla' id='trEncabezadoTbHist$tipoProtocolo'>";
				$tipoProtocolo="N";
				//echo "<td>Acciones</td>";
				echo "<td>Medicamento</td>";
				echo "<td>02</td>";
				echo "<td>04</td>";
				echo "<td>06</td>";
				echo "<td>08</td>";
				echo "<td>10</td>";
				echo "<td>12</td>";
				echo "<td>14</td>";
				echo "<td>16</td>";
				echo "<td>18</td>";
				echo "<td>20</td>";
				echo "<td>22</td>";
				echo "<td>24</td>";
				echo "</tr>";
			}	// FIN if($fechaTmp != $articulo->fecha)
					
			$fecha_ciclo = str_replace("-","",$articulo->fecha);
					
			// Inicio cambio de familia de medicamentos
			if($articulo->codigoFamilia!=$auxFamilia)
			{
				
				if($clasef == 'fila2')
					$clasef = 'fila1';
				else
					$clasef = 'fila2';

				if(isset($regletaFamiliaHist[$fecha_ciclo][$articulo->codigoFamilia]['esCompuestaFamilia']) && $regletaFamiliaHist[$fecha_ciclo][$articulo->codigoFamilia]['esCompuestaFamilia']=='1')
					$clasef = 'esCompuesta';
				
				echo "<tr class='".$clasef."' id='filaTituloFamiliaHist".$tipoProtocolo.$contFamilia."'	onclick='mostrarDetalleFlia(this,\"detKardexHist".$tipoProtocolo.$contFamilia."\",\"divDetalle".$tipoProtocolo.$contFamilia."\",\"filaTituloFamiliaHist".$tipoProtocolo.$contFamilia."\",\"".$clasef."\");'><td><a style='cursor: pointer;'><b>".$articulo->codigoFamilia." - ".$articulo->nombreFamilia."</b></a></td>";

				// echo "<td colspan='8'>";
				// echo "<table><tr>";
				//print_r($regletaFamiliaHist[$articulo->codigoFamilia]);
				/*********************************************************
				 * 	GRAFICA DE LOS HORARIOS DE SUMINISTRO DE FAMILIAS.
				 *
				 *
				 * 1.  Si la fecha de inicio es menor o igual a la de consulta del kardex se muestra la info
				 * 2.  Si se encuentra suspendido no se muestra la grafica
				 *********************************************************/
				//Grafica de los horarios ... 24 horas del dia.  Debe convertirse a horas cada periodicidad

				$horaArranque = 2;

				$cont3 = 1;
				$cont4 = $horaArranque;   //Desplazamiento desde la hora inicial
				//$claseGrafica = "fondoVerde";

				while($cont3 <= 24)
				{

					if(isset($regletaFamiliaHist[$fecha_ciclo][$articulo->codigoFamilia][$cont4]['valor']) && $regletaFamiliaHist[$fecha_ciclo][$articulo->codigoFamilia][$cont4]['valor'] > 0){
						echo "<td class='msg_tooltip' title='".$regletaFamiliaHist[$fecha_ciclo][$articulo->codigoFamilia][$cont4]['tooltip']."' align='center' onMouseOver='mostrarTooltip(this, $cont4)' width='100'>";
						echo $regletaFamiliaHist[$fecha_ciclo][$articulo->codigoFamilia][$cont4]['valor']." ".$regletaFamiliaHist[$fecha_ciclo][$articulo->codigoFamilia][$cont4]['unidad'];
						echo "</td>";
					} else {
						echo "<td class='msg_tooltip' title='".$regletaFamiliaHist[$fecha_ciclo][$articulo->codigoFamilia][$cont4]['tooltip']."' onMouseOver='mostrarTooltip(this, $cont4)' width='50'>&nbsp;</td>";
					}

					if($cont4 == 24){
						$cont4 = 0;
					}

					$cont3++;
					$cont4++;

					if($cont4 % 2 != 0){
						$cont4++;
					}
					if($cont3 % 2 != 0){
						$cont3++;
					}

					if($cont4 == $horaArranque){
						break;
					}
				}
				//echo "</tr></table>";


				//echo "</td>";
				echo "</tr>";
				
				echo "<tbody id='detKardexHist$tipoProtocolo$contFamilia' style='display:none;'>";
				
				echo "<tr><td colspan='13'>";
				echo "<div id='divDetalle$tipoProtocolo$contFamilia' style='display:none;'>";

				$contFamilia++;

			
				echo "<table align='center' border=0>";

				echo "<tr align='center' class='encabezadoTabla'>";
	
				$auxEspacios = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				
				//Encabezado detalle kardex
				echo "<td nowrap> $auxEspacios $auxEspacios $auxEspacios Articulo $auxEspacios $auxEspacios $auxEspacios </td>";
				echo "<td style='display:none'>Protocolo</td>";
				echo "<td>Dosis</td>";
				echo "<td>Frecuencia</td>";
				echo "<td>Condicion</td>";
				echo "<td>Forma farmaceutica</td>";
				echo "<td style='display:none'>Fecha y hora inicio</td>";
				echo "<td>Via</td>";
				echo "<td>Conf.</td>";
				echo "<td>Dias tto.</td>";
				echo "<td>Dosis max.</td>";
				echo "<td>Susp.</td>";
				echo "<td nowrap> $auxEspacios $auxEspacios Observaciones $auxEspacios $auxEspacios </td>";
				echo "<td>02</td>";
				echo "<td>04</td>";
				echo "<td>06</td>";
				echo "<td>08</td>";
				echo "<td>10</td>";
				echo "<td>12</td>";
				echo "<td>14</td>";
				echo "<td>16</td>";
				echo "<td>18</td>";
				echo "<td>20</td>";
				echo "<td>22</td>";
				echo "<td>24</td>";

				echo "</tr>";
			}	// FIN if($articulo->codigoFamilia!=$auxFamilia)

			$auxFamilia = $articulo->codigoFamilia;
		
			if($clase == "fila1"){
				$clase = "fila2";
			} else {
				$clase = "fila1";
			}

			if($articulo->grupo == $grupoControl){
				$clase = "articuloControl";
			}

			if($articulo->suspendido == 'on'){
				$clase = "suspendido";
			}

			echo "<tr id='tr$cont1' class='$clase'>";

			//Fecha del kardex
			$fechaTmp = $articulo->fecha;

			//Articulo
			if($cumplido){
				echo "<td>$articulo->codigoArticulo - C</td>";
			} else {
				echo "<td>$articulo->codigoArticulo</td>";
			}
			
			//Nombre protocolo
			echo "<td style='display:none'>$articulo->nombreProtocolo</td>";

			//Dosis y unidad de medida
			echo "<td>";
			echo $articulo->cantidadDosis." ";


			foreach ($colUnidades as $unidad){
				if($unidad->codigo == $articulo->unidadDosis){
					echo $unidad->descripcion;
				}
			}
			echo " (S)";
			echo "</td>";

			//Periodicidad
			echo "<td>";
			foreach ($colPeriodicidades as $periodicidad){
				if($periodicidad->codigo == $articulo->periodicidad){
					echo $periodicidad->descripcion;
				}
			}
			echo "</td>";

			//Condicion
			echo "<td>";
			foreach ($colCondicionesSuministro as $condicion){
				if($condicion->codigo == $articulo->condicionSuministro){
					echo $condicion->descripcion;
				}
			}
			echo "</td>";

			//Forma farmaceutica
			echo "<td>";

			foreach ($colFormasFarmaceuticas as $formasFarmaceutica){
				if($formasFarmaceutica->codigo == $articulo->formaFarmaceutica){
					echo $formasFarmaceutica->descripcion;
				}
			}

			echo "</td>";

			//Fecha y hora inicio
			echo "<td style='display:none'>";
			echo "$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion";
			echo "</td>";

			//Via administracion
			echo "<td>";
			foreach ($colVias as $via){
				if($via->codigo == $articulo->via){
					echo $via->descripcion;
				}
			}
			echo "</td>";

			//Confirmacion
			echo "<td>";
			if($articulo->estaConfirmado == 'on'){
				echo "Si";
			}else{
				echo "No";
			}
			echo "</td>";

			//Dias tratamiento
			echo "<td>";
			echo $articulo->diasTratamiento;
			echo "</td>";

			//Dosis maxima
			echo "<td>";
			echo $articulo->dosisMaxima;
			echo "</td>";

			//Suspendido o no
			echo "<td>";
			if($articulo->suspendido == 'on'){
				echo "Si";
			}else{
				echo "No";
			}
			echo "</td>";

			//Observaciones
			echo "<td>";
			echo "<textarea id='wtxtobs$cont1' rows=2 cols=20 readonly>";
			echo $articulo->observaciones;
			echo "</textarea>";
			echo "</td>";

			//Grafica de administracion de medicamentos
			foreach ($colPeriodicidades as $periodicidad){
				if($periodicidad->codigo == $articulo->periodicidad){
					$horasPeriodicidad = intval($periodicidad->equivalencia);
					break;
				}
			}

			$arrAplicacion = obtenerVectorAplicacionMedicamentos($articulo->fecha,$articulo->fechaInicioAdministracion,$articulo->horaInicioAdministracion,$horasPeriodicidad);
			$horaArranque = 2;
			$aplicaGraficaSuministro = true;

			$cont1 = 1;
			$cont2 = $horaArranque;   //Desplazamiento desde la hora inicial
			$caracterMarca = "*";

			while($cont1 <= 24){
				if(isset($arrAplicacion[$cont2]) && $arrAplicacion[$cont2] == $caracterMarca && $aplicaGraficaSuministro){
					echo "<td class='fondoVerde' align='center'>";
					echo $caracterMarca;
					echo "</td>";
				} else {
					echo "<td>&nbsp;</td>";
				}

				if($cont2 == 24){
					$cont2 = 0;
				}

				$cont1++;
				$cont2++;

				if($cont2 % 2 != 0){
					$cont2++;
				}
				if($cont1 % 2 != 0){
					$cont1++;
				}

				if($cont2 == $horaArranque){
					break;
				}
			}

			echo "</tr>";
			
			$cont1++;
		}	// FIN if($mostrarArticulo)

	}	// FOR($colDetalle as $articulo)

	echo "</table></div></td></tr></tbody>";
	echo "</table>";
	echo "</p>";
	echo "</div>";
}


//Realiza los movimientos necesarios de definitivo a temporal al abrir el kardex
/* Bajo el esquema de tablas temporales se trabajará asi:
 * APLICA PARA:
 * a. Articulos
 * b. Examenes
 * c. Liquidos endovenosos
 * d. Medicos
 * e. Dietas
 *
 * 1. Consulta de estructura temporal.
 * 1.1. Si hay registros, carga en pantalla
 * 1.2. No hay registros
 * 1.2.1. Consulta de estructura definitiva
 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
 *
 * 02-Jun-10 (Msanchez):  Carga de articulos dependiente del centro de costos y los grupos de medicamentos que puede ver.
 */

function realizarMovimientosArticulos($kardexActual, $paciente, $esFechaActual, $fechaConsulta, $fechaGrabacion, $tipoProtocolo, &$elementosActuales, &$colDetalle){
	global $usuario;    //Usuario que ingresa al kardex
	global $protocoloNormal;
	
	global $wbasedato;
	global $conex;

	$esEditable = $kardexActual->editable;
	$esAnterior = $kardexActual->esAnterior;
	$descontarDispensaciones = $kardexActual->descontarDispensaciones;

	$historia = $paciente->historiaClinica;
	$ingreso = $paciente->ingresoHistoriaClinica;
	/*************************************************************************************************
	 * Las condiciones para los centros de costos son las siguientes (sin importar el protocolo):
	 *
	 * 1.  Central de mezclas.  	Mueve unicamente los articulos del 1051
	 * 2.  Lactario.				Mueve los articulos del 1050 y solo los que tenga ccogka
	 * 3.  Servicio farmaceutico.	Mueve los articulos del 1050 y solo los que tenga ccogka
	 * 4.  Cualquier otro.			Mueve todos los articulos.
	 *************************************************************************************************/
	if($esEditable){
		//Verifica que si no existe detalle temporal, se cargue el detalle definitivo del dia anterior
		if($esEditable && $esAnterior && $esFechaActual){
			$colTemporal = consultarDetalleTemporalKardex($historia,$ingreso,$fechaConsulta,$tipoProtocolo);
			if(count($colTemporal) == 0){
				//antes de cargar los articulos a la temporal recalculo nuevamente
				//esto por si viene de urgencias o cirugia y no se encuentre recalculado
				recalcularKardex( $conex, $wbasedato, $historia, $ingreso, date( "Y-m-d", strtotime( $fechaGrabacion." 00:00:00" ) - 24*3600 ) );
				cargarArticulosAnteriorATemporal($historia,$ingreso,$fechaConsulta,$fechaGrabacion,$tipoProtocolo,$descontarDispensaciones,$kardexActual->horaDescuentoDispensaciones);
				$fechaConsulta = $fechaGrabacion;
			}
		}

		$colDetalle = consultarDetalleTemporalKardex($historia,$ingreso,$fechaConsulta,$tipoProtocolo);
		$elementosActuales = count($colDetalle);

		if($elementosActuales == 0){
			//1.2.1. Consulta de estructura definitiva
			$colDetalle = consultarDetalleDefinitivoKardex($historia,$ingreso,$fechaConsulta,$tipoProtocolo);
			$elementosActuales = count($colDetalle);

			if($elementosActuales > 0 && $esFechaActual){
				//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				cargarArticulosATemporal($historia,$ingreso,$fechaConsulta,$fechaConsulta,$tipoProtocolo);
			}
		}
	} else {
		$colDetalle = consultarDetalleDefinitivoKardex($historia,$ingreso,$fechaConsulta,$tipoProtocolo);
		$elementosActuales = count($colDetalle);
	}

	//Los articulos de central de mezclas deben verse en el kardex en forma editable.  Solo para el servicio farmaceutico
	if($usuario->esUsuarioSF && $tipoProtocolo == $protocoloNormal){
		$colDetalle = array_merge($colDetalle,consultarArticulosCMParaSF($historia,$ingreso,$fechaConsulta,$tipoProtocolo));
	}

	//Articulos del lactario
	if(!$usuario->esUsuarioSF && !$usuario->esUsuarioCM && !$usuario->esUsuarioLactario && $tipoProtocolo == $protocoloNormal){
		$colDetalle = array_merge($colDetalle,consultarArticulosLactario($historia,$ingreso,$fechaConsulta,$tipoProtocolo));
	}
}

function realizarMovimientosArticulosAlta($kardexActual, $paciente, $esFechaActual, $fechaConsulta, $fechaGrabacion, $tipoProtocolo, &$elementosActualesAlta, &$colDetalleAlta){
	global $usuario;    //Usuario que ingresa al kardex
	global $protocoloNormal;
	
	global $wbasedato;
	global $conex;

	$esEditable = $kardexActual->editable;
	$esAnterior = $kardexActual->esAnterior;
	$descontarDispensaciones = $kardexActual->descontarDispensaciones;

	$historia = $paciente->historiaClinica;
	$ingreso = $paciente->ingresoHistoriaClinica;
	/*************************************************************************************************
	 * Las condiciones para los centros de costos son las siguientes (sin importar el protocolo):
	 *
	 * 1.  Central de mezclas.  	Mueve unicamente los articulos del 1051
	 * 2.  Lactario.				Mueve los articulos del 1050 y solo los que tenga ccogka
	 * 3.  Servicio farmaceutico.	Mueve los articulos del 1050 y solo los que tenga ccogka
	 * 4.  Cualquier otro.			Mueve todos los articulos.
	 *************************************************************************************************/
	// $colDetalleAlta = consultarDetalleKardexAlta($historia,$ingreso,$fechaConsulta,$tipoProtocolo);
	// $elementosActualesAlta = count($colDetalleAlta);

	if($esEditable){
		//Verifica que si no existe detalle temporal, se cargue el detalle definitivo del dia anterior

		$colDetalleAlta = consultarDetalleTemporalKardex($historia,$ingreso,$fechaConsulta,$tipoProtocolo,'on');
		$elementosActualesAlta = count($colDetalleAlta);

		if($elementosActualesAlta == 0){
			//1.2.1. Consulta de estructura definitiva
			$colDetalleAlta = consultarDetalleDefinitivoKardex($historia,$ingreso,$fechaConsulta,$tipoProtocolo,'on');
			$elementosActualesAlta = count($colDetalleAlta);

		}
	} else {
		$colDetalleAlta = consultarDetalleDefinitivoKardex($historia,$ingreso,$fechaConsulta,$tipoProtocolo,'on');
		$elementosActualesAlta = count($colDetalleAlta);
	}

	//Los articulos de central de mezclas deben verse en el kardex en forma editable.  Solo para el servicio farmaceutico
	if($usuario->esUsuarioSF && $tipoProtocolo == $protocoloNormal){
		$colDetalleAlta = array_merge($colDetalleAlta,consultarArticulosCMParaSF($historia,$ingreso,$fechaConsulta,$tipoProtocolo));
	}

	//Articulos del lactario
	if(!$usuario->esUsuarioSF && !$usuario->esUsuarioCM && !$usuario->esUsuarioLactario && $tipoProtocolo == $protocoloNormal){
		$colDetalleAlta = array_merge($colDetalleAlta,consultarArticulosLactario($historia,$ingreso,$fechaConsulta,$tipoProtocolo));
	}
}


/************************************************************************************************************************
 * CONSULTA DE ACCIONES POR CADA CAMPO DE UNA PESTAÑA
 *
 * Precedencia de operaciones:
 *
 * 1.Lectura.   Inhibe o permite el resto de operaciones
 ************************************************************************************************************************/
function consultarAccionesPestana($indicePestana){
	global $wbasedato;
	global $wbasedatohce;
	global $conex;
	global $codigoAplicacion;
	global $usuario;

	$acciones = array();

	//Dias de tratamiento
	$q = "SELECT
				Accpma,Accpes,Accopc,Accrol,Acccre,Accrea,Accupd,Accdel,Accest
			FROM
				hceidc_000029
			WHERE 
				Accpma = '$codigoAplicacion'
				AND Accpes = '$indicePestana'
				AND Accrol = '$usuario->codigoRolHCE';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	while($info = mysql_fetch_array($res)){
		$accion = inicializarAccionesPestana();

		$accion->nroPestana = $indicePestana;
		$accion->codigoAccion = $info['Accopc'];
		$accion->actualizar = $info['Accupd'] == "on" ? true : false;
		$accion->borrar = $info['Accdel'] == "on" ? true : false;
		$accion->crear = $info['Acccre'] == "on" ? true : false;
		$accion->leer = $info['Accrea'] == "on" ? true : false;

		//Forma de indizacion de acciones
		$acciones[$indicePestana.".".$accion->codigoAccion] = $accion;
	}

	return $acciones;
}

/************************************************************************************************************************
 * Coloca un campo en html
 *
 * Tipo:
 *
 * 1.Text
 * 2.Textarea
 ************************************************************************************************************************/
/**/function crearCampo($tipo,$id,$acciones,$atributos,$valor){
	$salida = "";

	//SI no hay accion por BD, se otorgan todos los permisos
	if(empty($acciones)){
		$acciones = inicializarAccionesPestana();
	}

	switch ($tipo){
		case '1':		//Texto
			$salida = "<input type=text name='$id' id='$id' ";
			foreach ($atributos as $clave => $vr){
				if(!empty($vr)){
					$salida .= " $clave=\"$vr\" ";
				} else {
					$salida .= " $clave ";
				}
			}
			//Valor
			$salida .= " value=\"$valor\"";
				
			//Lectura
			if(!$acciones->leer){
				$salida .= " readonly=readonly ";
			}
			$salida .= "/>";
			break;
		case '2':		//Textarea
			$salida = "<textarea name='$id' id='$id' ";
			foreach ($atributos as $clave => $vr){
				if(!empty($vr)){
					$salida .= " $clave=\"$vr\" ";
				} else {
					$salida .= " $clave ";
				}
			}
				
			//Lectura
			if(!$acciones->leer){
				$salida .= " readonly=readonly ";
			}
				
			$salida .= ">$valor";
			$salida .= "</textarea>";
			break;
		case '3':		//Boton
			$salida = "<input type='button' name='$id' id='$id' ";
			foreach ($atributos as $clave => $vr){
				if(!empty($vr)){
					$salida .= " $clave=\"$vr\" ";
				} else {
					$salida .= " $clave ";
				}
			}
			//Valor
			$salida .= " value=\"$valor\"";
				
			//Lectura
			if(!$acciones->leer){
				$salida .= " disabled ";
			}
				
			$salida .= "/>";
			break;
		case '4':		//Link
			$salida = "<a href='#null' name='$id' id='$id' ";
			foreach ($atributos as $clave => $vr){
				if(!empty($vr)){
					$salida .= " $clave=\"$vr\" ";
				} else {
					$salida .= " $clave ";
				}
			}
			//Valor
			$salida .= ">$valor";
			$salida .= "</a>";

			//Lectura
			if(!$acciones->leer){
				$salida = " ";
			}

			break;
		case '5':		//Checkbox
			$salida = "<input type='checkbox' name='$id' id='$id' ";
			foreach ($atributos as $clave => $vr){
				if(!empty($vr)){
					$salida .= " $clave=\"$vr\" ";
				} else {
					$salida .= " $clave ";
				}
			}
			//Valor
			$salida .= " value=\"$valor\"";
				
			//Lectura
			if(!$acciones->leer){
				$salida .= " disabled ";
			}
				
			$salida .= "/>";
			break;
		case '6':		//Select
			$salida = "<select name='$id' id='$id' ";

			//Lectura
			if(!$acciones->leer){
				$salida .= " disabled ";
			}
				
			foreach ($atributos as $clave => $vr){
				if(!empty($vr)){
					$salida .= " $clave=\"$vr\" ";
				} else {
					$salida .= " $clave ";
				}
			}
			$salida .= ">";
			// 2012-07-09
			//Valor
			$salida .= " <option>$valor</option> ";
			$salida .= "</select>";
			break;
		case '7':		//Rama de arbol
			$salida = "$valor";
				
			//Lectura
			if(!$acciones->leer){
				$salida = " ";
			}
			break;
		case '8':		//Link
			$salida = "<a href='#null' name='$id' id='$id' ";
			foreach ($atributos as $clave => $vr){
				if(!empty($vr)){
					$salida .= " $clave=\"$vr\" ";
				} else {
					$salida .= " $clave ";
				}
			}
			//Valor
			$salida .= ">$valor";
			$salida .= "</a>";

			//Lectura
			if(!$acciones->leer){
				$salida = $valor;
			}
			break;
		default:
			break;
	}
	echo $salida;
}
/*
 * Para comunicar PHP con el javascript de creacion dinamico, se serializan las acciones separadas por coma
 */
function accionesATexto($acciones){
	$texto = "";

	if(isset($acciones)){
		$texto .= $acciones->leer ? "S":"N";
		$texto .= $acciones->actualizar ? ",S":",N";
		$texto .= $acciones->borrar ? ",S":",N";
		$texto .= $acciones->crear ? ",S":",N";
	} else {
		$texto .= "S";
		$texto .= ",S";
		$texto .= ",S";
		$texto .= ",S";
	}

	return $texto;
}

//Obtiene los datos adicionales de un articulo por paciente
function obtenerDatosAdicionalesArticulo($historia, $ingreso, $articulo, &$diasTratamiento, &$dosisTotalesTto){
	global $wbasedato;
	global $conex;

	//
	/*Dias de tratamiento
	$q = "SELECT
				A.Kadhis,A.Kading,A.Kadart,SUM(DATEDIFF(Kadfec,A.Kadfin)) diasTto
			FROM
				(
					SELECT
						Kadhis,Kading,Kadart,Kadfin
					FROM
						mhosidc_000054
					WHERE
						Kadhis = '$historia'
						AND Kading = '$ingreso'
						AND Kadart = '$articulo'
					GROUP BY
						Kadfin
				) A,
				(
					SELECT
						Kadhis,Kading,Kadart,Kadfin,max( kadfec ) Kadfec
					FROM
						mhosidc_000054
					WHERE
						Kadhis = '$historia'
						AND Kading = '$ingreso'
						AND Kadart = '$articulo'
					GROUP BY
						Kadfin
				) B
			WHERE
				A.Kadhis = B.Kadhis
				AND A.Kading = B.Kading
				AND A.Kadart = B.Kadart
				AND A.Kadfin = B.Kadfin
			GROUP BY
				Kadhis,Kading,Kadart";
*/
	$q="SELECT 
			COUNT(*) diasTto
		FROM (
			SELECT 
				COUNT(*) 
			FROM 
				mhosidc_000015
			WHERE 
				Aplhis = '$historia' 
				AND Apling = '$ingreso' 
				AND Aplart = '$articulo' 
				AND Aplest = 'on'
			GROUP BY 
				Aplfec
			)Apl";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0 ){
			$info = mysql_fetch_array($res);
			$diasTratamiento = $info['diasTto'];
	}

	//Dosis aplicadas BASADAS SOLO en el kardex
	/*
	$q = "SELECT
			SUM(Kadcan * Kadcfr) cantFr, Kadufr
		FROM
			mhosidc_000054
		WHERE
			Kadhis = '$historia'
			AND Kading = '$ingreso'
			AND Kadart = '$articulo'
			AND Kadfin != Kadfec
		GROUP BY
			Kadcfr, Kadufr";
	*/
	$q = "SELECT
			IFNULL( COUNT(Aplcan),0 ) aplicaciones
		FROM
			mhosidc_000015
		WHERE
			Aplest = 'on'
			AND Aplcan > 0
			AND Aplhis  = '$historia'
			AND Apling = '$ingreso'
			AND Aplart = '$articulo'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0 ){
			$info = mysql_fetch_array($res);
			$dosisTotalesTto = $info['aplicaciones'];
	}
}

function consultarEstadoGrabacionKardex($historia, $ingreso, $fecha){
	global $wbasedato;
	global $conex;

	$grabado = "";

	$q = "SELECT
			Kargra
		FROM 
		{$wbasedato}_000053
		WHERE
			Fecha_data = '$fecha'
			AND Karhis = '".$historia."'
			AND Karing = '".$ingreso."';";

		$coleccion = array();

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		$cont1 = 0;
		while($cont1 <= $num){
			$info = mysql_fetch_array($res);
			$grabado = $info['Kargra'];
			$cont1++;
		}
		return $grabado;
}

//Itera basado en RegistroGenericoDTO
function descripcionMaestroPorCodigo($coleccion, $valor){
	$descripcion = "";

	foreach ($coleccion as $elemento){
		if(trim($elemento->codigo) == $valor){
			$descripcion = $elemento->descripcion;
			break;
		}
	}

	return $descripcion;
}

function consultarDietasTemporalPaciente($historia, $ingreso, $fecha){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			".$wbasedato."_000064.id, Dikcod, Diedes, Dikhis, Diking, Dikfec, Dikest
		FROM 
			".$wbasedato."_000064, ".$wbasedato."_000041 
		WHERE
			Diecod = Dikcod
			AND Dikhis = '".$historia."'
			AND Diking = '".$ingreso."'
			AND Dikfec = '".$fecha."';";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	if($num > 0 ){
		while($cont1 <= $num)
		{
			$info = mysql_fetch_array($res);

			$dieta = new dietaKardexDTO();

			$dieta->id = $info['id'];

			$dieta->codigoDieta = $info['Dikcod'];
			$dieta->descripcionDieta = $info['Diedes'];
			$dieta->historia = $info['Dikhis'];
			$dieta->ingreso = $info['Diking'];
			$dieta->fechaKardex = $info['Dikfec'];
			$dieta->estado = $info['Dikest'];

			$cont1++;

			$coleccion[] = $dieta;
		}
	}
	return $coleccion;
}

function consultarDietasDefinitivoPaciente($historia, $ingreso, $fecha){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			".$wbasedato."_000052.id, Dikcod, Diedes, Dikhis, Diking, Dikfec, Dikest
		FROM 
			".$wbasedato."_000052, ".$wbasedato."_000041 
		WHERE
			Diecod = Dikcod
			AND Dikhis = '".$historia."'
			AND Diking = '".$ingreso."'
			AND Dikfec = '".$fecha."';";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 <= $num)
	{
		$info = mysql_fetch_array($res);

		$dieta = new dietaKardexDTO();

		$dieta->id = $info['id'];

		$dieta->codigoDieta = $info['Dikcod'];
		$dieta->descripcionDieta = $info['Diedes'];
		$dieta->historia = $info['Dikhis'];
		$dieta->ingreso = $info['Diking'];
		$dieta->fechaKardex = $info['Dikfec'];
		$dieta->estado = $info['Dikest'];

		$cont1++;

		$coleccion[] = $dieta;
	}

	return $coleccion;
}

function consultarEsquemaInsulina($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Infade, Inffde, Infcde
		FROM 
		{$wbasedato}_000070
		WHERE
			Infhis = '$historia' 
			AND Infing = '$ingreso'
			AND Inffec = '$fecha'
		;";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		$cont1 = 0;
		$registro = new ArticuloDTO();

		if($num > 0)
		{
			$info = mysql_fetch_array($res);

			$registro->codigo = $info['Infade'];
			$registro->frecuencia = $info['Inffde'];
			$registro->codEsquema = $info['Infcde'];

			$cont1++;
		}

		return $registro;
}

function consultarIntervalosDextrometer($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Indime,Indima,Inddos,Indudo,Indobs,Indvia  
		FROM 
			".$wbasedato."_000071
		WHERE
			Indhis = '$historia'
			AND Inding = '$ingreso'
			AND Indfec = '$fecha'
		ORDER BY
			Indime ASC;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$registro = new IntervaloDextrometerDTO();

		$registro->minimo = $info['Indime'];
		$registro->maximo = $info['Indima'];
		$registro->dosis = $info['Inddos'];
		$registro->unidadDosis = $info['Indudo'];
		$registro->observaciones = $info['Indobs'];
		$registro->via = $info['Indvia'];

		$cont1++;

		$coleccion[] = $registro;
	}

	return $coleccion;
}


function consultarUnidadesMedida(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Unicod, Unides
		FROM 
			".$wbasedato."_000027
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

		$unidad = new RegistroGenericoDTO();

		$unidad->codigo = $info['Unicod'];
		$unidad->descripcion = $info['Unides'];

		$cont1++;

		$coleccion[] = $unidad;
	}

	return $coleccion;
}

function consultarUnidadesMedidaPosologia(){
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

		$unidad = new RegistroGenericoDTO();

		$unidad->codigo = $info['Unicod'];
		$unidad->descripcion = $info['Unides'];

		$cont1++;

		$coleccion[] = $unidad;
	}

	return $coleccion;
}

function consultarViasAdministracion(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Viacod, Viades 
		FROM 
			".$wbasedato."_000040		
		ORDER BY
			Viades ASC;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$via = new RegistroGenericoDTO();

		$via->codigo = $info['Viacod'];
		$via->descripcion = $info['Viades'];

		$cont1++;

		$coleccion[] = $via;
	}

	return $coleccion;
}

function consultarFormasFarmaceuticas(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ffacod, Ffanom 
		FROM 
			".$wbasedato."_000046
		ORDER BY 
			Ffanom
		;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$via = new RegistroGenericoDTO();

		$via->codigo = trim($info['Ffacod']);
		$via->descripcion = trim($info['Ffanom']);

		$cont1++;

		$coleccion[] = $via;
	}

	return $coleccion;
}

function consultarPeriodicidades(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Percod, Percan, Peruni, Perequ
		FROM 
			".$wbasedato."_000043
		WHERE Pertip = 'C'
		ORDER BY id ASC;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new PeriodicidadDTO();

		$reg->codigo = $info['Percod'];
		$reg->descripcion = $info['Percan']." - ".strtoupper($info['Peruni']);
		$reg->equivalencia = $info['Perequ'];

		$cont1++;

		$coleccion[] = $reg;
	}

	return $coleccion;
}

function consultarPeriodicidadesOnc(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Percod, Percan, Peruni, Perequ
		FROM 
			".$wbasedato."_000043
		WHERE Pertip = 'O'
		ORDER BY id ASC;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new PeriodicidadDTO();

		$reg->codigo = $info['Percod'];
		//2013-10-08
		//$reg->descripcion = $info['Percan']." - ".strtoupper($info['Peruni'])."(S)";
		$reg->descripcion = strtoupper($info['Peruni']);
		$reg->equivalencia = $info['Perequ'];

		$cont1++;

		$coleccion[] = $reg;
	}

	return $coleccion;
}

function puedeCambiarEstado() {
	global $conex;
	global $usuario;
	global $wbasedato;

	$q = "SELECT
			Reerol
		FROM 
			{$wbasedato}_000162
		WHERE
			Reerol = '".$usuario->codigoRolHCE."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num>0)
		return true;
	else
		return false;

}

function consultarDescripcionEstado($estadoActual) {
	global $wbasedato;
	global $conex;
	
	$q = "SELECT
			Esedes
		FROM 
			{$wbasedato}_000161
		WHERE 
			Esecod = '".$estadoActual."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$info = mysql_fetch_array($res);
	return $info['Esedes'];
}

function consultarEstadosExamenes(){
	global $wbasedato;
	global $conex;
	global $usuario;
	
	$usuario->codigoRolHCE;
	$q = "SELECT
			Esecod,Esedes
		FROM 
			{$wbasedato}_000161
		WHERE
			Eseest = 'on';";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new RegistroGenericoDTO();

		$reg->codigo = $info['Esecod'];
		$reg->descripcion = $info['Esedes'];

		$cont1++;

		$coleccion[] = $reg;
	}

	return $coleccion;
}

function consultarEstadosExamenesRol(){
	global $wbasedato;
	global $conex;
	global $usuario;
	
	$usuario->codigoRolHCE;
	$q = "SELECT
			Esecod,Esedes
		FROM 
			{$wbasedato}_000161, {$wbasedato}_000162
		WHERE
			Esecod = Reecod
		AND Reerol = '".$usuario->codigoRolHCE."';";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new RegistroGenericoDTO();

		$reg->codigo = $info['Esecod'];
		$reg->descripcion = $info['Esedes'];

		$cont1++;

		$coleccion[] = $reg;
	}

	return $coleccion;
}

function consultarEstadosExamenesLaboratorio(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Eexcod,Eexdes
		FROM 
			".$wbasedato."_000045;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new RegistroGenericoDTO();

		$reg->codigo = $info['Eexcod'];
		$reg->descripcion = $info['Eexdes'];

		$cont1++;

		$coleccion[] = $reg;
	}

	return $coleccion;
}

function consultarEstadosDetalleOrdenes(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Detesi
		FROM 
			hceidc_000028
		GROUP BY Detesi;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new RegistroEstadoDet();

		$reg->estado = $info['Detesi'];

		$cont1++;

		$coleccion[] = $reg;
	}

	return $coleccion;
}

function consultarExamenesLaboratorio(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod,Cconom 
		FROM 
			".$wbasedato."_000011
		WHERE 
			Ccoest = 'on'
			AND Ccoayu = 'on';";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new RegistroGenericoDTO();

		$reg->codigo = $info['Ccocod'];
		$reg->descripcion = $info['Cconom'];

		$cont1++;

		$coleccion[] = $reg;
	}
	return $coleccion;
}

function consultarCentrosAyudasDiagnosticas($historia="",$ingreso=""){
	global $wbasedato;
	global $conex;

	if($historia!="" && $ingreso!="")
	{
		$q = "SELECT
				Ccocod,Cconom,Ccocor
			FROM 
				hceidc_000027, ".$wbasedato."_000011
			WHERE 
				Ordhis = '$historia' 
				AND Ording = '$ingreso'
				AND Ordest = 'on'
				AND Ccocod = Ordtor
			GROUP BY 
				Ccocod
			ORDER BY 
				Cconom ";	
	}
	else
	{
		$q = "SELECT
				Ccocod,Cconom,Ccocor
			FROM 
				".$wbasedato."_000011
			WHERE 
				Ccoest = 'on'
				AND Ccoayu = 'on';";
	}
	
	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new CentroCostosOrdenesDTO();

		$reg->codigo = $info['Ccocod'];
		$reg->nombre = $info['Cconom'];
		$reg->consecutivoOrden = $info['Ccocor'];

		$cont1++;

		$coleccion[] = $reg;
	}
	return $coleccion;
}

function consultarCentrosCostosActivos(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Ccocod,Cconom 
		FROM 
			".$wbasedato."_000011
		WHERE 
			Ccoest = 'on';";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new RegistroGenericoDTO();

		$reg->codigo = $info['Ccocod'];
		$reg->descripcion = $info['Cconom'];

		$cont1++;

		$coleccion[] = $reg;
	}
	return $coleccion;
}

function consultarCondicionesSuministroMedicamentos($tipo){
	global $wbasedato;
	global $conex;

	switch ($tipo){
		case 'I':
			$q = "SELECT Concod,condes,Condma FROM ".$wbasedato."_000042 WHERE Contip = '$tipo' ORDER BY Condes;";
			break;
		default:
			$q = "SELECT Concod,condes,Condma FROM ".$wbasedato."_000042 WHERE Contip != 'I' ORDER BY Condes;";
			break;
	}

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new RegistroGenericoDTO();

		$reg->codigo = $info['Concod'];
		$reg->descripcion = $info['condes'];
		
		$reg->valDefecto = $info['Condma'];	//Junio 13 de 2012

		$cont1++;

		$coleccion[] = $reg;
	}
	return $coleccion;
}

/**
 * Listado de medicos del sistema
 *
 */
function consultarMedicos(){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Meddoc, Medtdo, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Meduma, SUBSTRING_INDEX(Medesp,'-',1) Medesp, id 
		FROM 
			".$wbasedato."_000048
		WHERE 
			Medest = 'on'
			AND Meduma != ''
			AND Meduma != 'NO APLICA'
		GROUP BY
			Meddoc, Medtdo
		ORDER BY 
			Medap1, Medno1";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		while ($cont1 < $num){
			$cont1++;
				
			$medico = new medicoDTO();
				
			$info = mysql_fetch_array($res);

			$medico->tipoDocumento = $info['Medtdo'];
			$medico->numeroDocumento = $info['Meddoc'];
			$medico->nombre1 = $info['Medno1'];
			$medico->nombre2 = $info['Medno2'];
			$medico->apellido1 = $info['Medap1'];
			$medico->apellido2 = $info['Medap2'];
			$medico->registroMedico = $info['Medreg'];
			$medico->telefono = $info['Medtel'];
			$medico->codigoEspecialidad = $info['Medesp'];
			$medico->usuarioMatrix = $info['Meduma'];
			$medico->id = $info['id'];
				
			$coleccion[] = $medico;
				
		}
	}
	return $coleccion;
}


function consultarEspecialidadUsuario($usuario){

	global $wbasedato;
	global $conex;

	$q = "SELECT
			Espcod
		FROM 
			mhosidc_000044, mhosidc_000048
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

function consultarEspecialidades(){

	global $wbasedato;
	global $conex;
	$coleccion = array();

	$q = "SELECT
			Espcod, Espnom
		FROM 
			".$wbasedato."_000044
		ORDER BY Espnom";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		while ($cont1 < $num){
			$cont1++;
				
			$registro = new RegistroGenericoDTO();
				
			$info = mysql_fetch_array($res);

			$registro->codigo = $info['Espcod'];
			$registro->descripcion = $info['Espnom'];
				
			$coleccion[] = $registro;
		}
	}
	return $coleccion;
}
/**
 * Consulta los articulos especiales del kardex.
 *
 * Indica tipos de articulos para las secciones del kardex, este maestro es por articulo y no fue incluido en fracciones por articulo mhosidc_000058 debido a que
 * un articulo por centro de costos puede ser de varios tipos, en caso de que un grupo completo sea de un tipo dado, ya existe el maestro de grupos por tipo
 *
 * I : Insulina
 *
 * @param $centroCostos
 * @param $tipo
 * @return unknown_type
 */
function consultarArticulosEspeciales($centroCostos,$tipo){
	global $wbasedato;
	global $conex;
	global $wcenpro;
	global $centroCostosServicioFarmaceutico;

	$coleccion = array();

	if(empty($centroCostos)){
		$centroCostos = "%";
	}
	 
	$q = "SELECT
			Arkcod, Arkest, Arkcco, Arktip,
			(CASE WHEN Arkcco = '$centroCostosServicioFarmaceutico' THEN (
												SELECT Artcom
												FROM {$wbasedato}_000026
												WHERE artcod = Arkcod
										) ELSE (
												SELECT Artcom
												FROM {$wcenpro}_000002
												WHERE artcod = Arkcod
										)
			END ) Nombre
		FROM 
			".$wbasedato."_000068
		WHERE
			Arkest = 'on'
			AND Arkcco LIKE '$centroCostos'
			AND Arktip = '$tipo'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		while ($cont1 < $num){
			$cont1++;
				
			$registro = new ArticuloDTO();
				
			$info = mysql_fetch_array($res);

			$registro->codigo = $info['Arkcod'];
			$registro->centroCostos = $info['Arkcco'];
			$registro->nombre = @$info['Nombre'];
			$registro->tipo = $info['Arktip'];
				
			$coleccion[] = $registro;
		}
	}
	return $coleccion;
}

function consultarMaestroEsquemasInsulina(){

	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Esdcod, Esdest, Esdime, Esdima, Esddos, Esdudo, Esdobs, Esdvia  
		FROM 
			".$wbasedato."_000069
		WHERE
			Esdest = 'on'
		GROUP BY 
			Esdcod";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		while ($cont1 < $num){
			$cont1++;
				
			$registro = new RegistroGenericoDTO();
				
			$info = mysql_fetch_array($res);

			$registro->codigo = $info['Esdcod'];
				
			$coleccion[] = $registro;
		}
	}
	return $coleccion;
}

/*
 * CONSULTA DE DATOS DEMOGRAFICOS DEL PACIENTE A TRAVÉS DE SU DOCUMENTO Y TIPO DE DOCUMENTO DE IDENTIDAD
 */
function consultarInfoPacienteOrdenHCE($tipoDocumento,$nroDocumento){
	global $wbasedato;
	global $conex;

	$paciente = new pacienteKardexDTO();

	$q = "SELECT
			pacno1, pacno2, pacap1, pacap2, pactid, pacced, Ubisac, Ubihac, Ubisan, Ubihan, Ubialp, Ubiald, Ubiptr,
			".$wbasedato."_000018.fecha_data fecha_data, ".$wbasedato."_000018.hora_data hora_data, Pacnac,
			Ingres,Ingnre,".$wbasedato."_000017.fecha_data fechaServicio, ".$wbasedato."_000017.hora_data horaServicio, Cconom, Orihis, Oriing
		FROM 
			root_000036, root_000037, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000011 ON Ubisac = Ccocod
		WHERE 
			oriced = pacced 
			AND Ubihis = Orihis
			AND Ubiing = Oriing 
			AND Inghis = Orihis
			AND Inging = Oriing
			AND orihis = eyrhis
			AND oriing = eyring
			AND ubisac = eyrsde
			AND ubihac = eyrhde
			AND eyrtip = 'Recibo'
			AND eyrest = 'on'
			AND Oriori = '10'
			AND Oriced = '$nroDocumento'
			AND Oritid = '$tipoDocumento';";

	//ESTA ES PARA QUE DEJE BUSCAR GENTE DE URGENCIAS
	$q = "SELECT
			pacno1, pacno2, pacap1, pacap2, pactid, pacced, Ubisac, Ubihac, Ubisan, Ubihan, Ubialp, Ubiald, Ubiptr,
			".$wbasedato."_000018.fecha_data fecha_data, ".$wbasedato."_000018.hora_data hora_data, Pacnac,
			Ingres,Ingnre, '' fechaServicio, '' horaServicio, Cconom, Orihis, Oriing, Ccourg, Ccocir, Pacsex
		FROM
			root_000036, root_000037, ".$wbasedato."_000016, ".$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000011 ON Ubisac = Ccocod
		WHERE
			oriced = pacced
			AND Ubihis = Orihis
			AND Ubiing = Oriing
			AND Inghis = Orihis
			AND Inging = Oriing
			AND Oriori = '10'
			AND Oriced = '$nroDocumento'
			AND Oritid = '$tipoDocumento';";	

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$info = mysql_fetch_array($res);

		$paciente->historiaClinica = $info['Orihis'];
		$paciente->ingresoHistoriaClinica = $info['Oriing'];
		$paciente->nombre1 = $info['pacno1'];
		$paciente->nombre2 = $info['pacno2'];
		$paciente->apellido1 = $info['pacap1'];
		$paciente->apellido2 = $info['pacap2'];
		$paciente->documentoIdentidad = $info['pacced'];
		$paciente->tipoDocumentoIdentidad = $info['pactid'];
		$paciente->fechaNacimiento = $info['Pacnac'];
		$paciente->servicioActual = $info['Ubisac'];
		$paciente->servicioAnterior = $info['Ubisan'];
		$paciente->habitacionActual = $info['Ubihac'];
		$paciente->habitacionAnterior = $info['Ubihan'];
		$paciente->numeroIdentificacionResponsable = $info['Ingres'];
		$paciente->nombreResponsable = $info['Ingnre'];

		$paciente->fechaHoraIngresoServicio = $info['fechaServicio']." ".$info['horaServicio'];

		$paciente->fechaIngreso = $info['fecha_data'];
		$paciente->horaIngreso = $info['hora_data'];

		$paciente->altaDefinitiva = $info['Ubiald'];
		$paciente->altaProceso = $info['Ubialp'];
		
		$paciente->sexo = $info['Pacsex'];

		$estado = false;
		if($info['Ubiptr'] == 'on'){
			$paciente->ultimoMvtoHospitalario = "En proceso de traslado";
			$estado = true;
		}

		if ($info['Ubialp'] == 'on'){
			$paciente->ultimoMvtoHospitalario = "Alta en proceso";
			$estado = true;
		}

		if ($info['Ubiald'] == 'on') {
			$paciente->ultimoMvtoHospitalario = "Alta definitiva";
			$estado = true;
		}

		if(!$estado){
			$paciente->ultimoMvtoHospitalario = "En habitaci&oacute;n";
		}
		$paciente->nombreServicioActual = $info['Cconom'];
		
		//El paciente se encuentra en cirugia o en urgencias
		$paciente->enCirugia = isset($info['Ccocir']) && $info['Ccocir'] == "on" ? true : false;
		$paciente->enUrgencias = isset($info['Ccourg']) && $info['Ccourg'] == "on" ? true : false;

		//Edad
		$ann=(integer)substr($paciente->fechaNacimiento,0,4)*360 +(integer)substr($paciente->fechaNacimiento,5,2)*30 + (integer)substr($paciente->fechaNacimiento,8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$ann1=($aa - $ann)/360;
		$meses=(($aa - $ann) % 360)/30;
		if ($ann1<1){
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		} else {
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$ann1." año(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		}
		$paciente->edadPaciente = $wedad; 
	}
	return $paciente;
}

function consultarInfoPacienteKardex($whistoria, $ingresoAnterior){
	global $wbasedato;
	global $conex;

	$paciente = new pacienteKardexDTO();

	if(isset($ingresoAnterior) && $ingresoAnterior != ''){
		$ingreso = $ingresoAnterior;
	} else {
		$ingreso = consultarUltimoIngresoHistoria($conex,$whistoria);
	}

	$q = "SELECT
			pacno1, pacno2, pacap1, pacap2, pactid, pacced, Ubisac, Ubihac, Ubisan, Ubihan, Ubialp, Ubiald, Ubiptr,
			".$wbasedato."_000018.fecha_data fecha_data, ".$wbasedato."_000018.hora_data hora_data, Pacnac,
			Ingres,Ingnre,".$wbasedato."_000017.fecha_data fechaServicio, ".$wbasedato."_000017.hora_data horaServicio, Cconom
		FROM 
			root_000036, root_000037, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000011 ON Ubisac = Ccocod
		WHERE 
			oriced = pacced 
			AND Ubihis = Orihis
			AND Ubiing = Oriing 
			AND Inghis = Orihis
			AND Inging = Oriing
			AND orihis = eyrhis
			AND oriing = eyring
			AND ubisac = eyrsde
			AND ubihac = eyrhde
			AND eyrtip = 'Recibo'
			AND eyrest = 'on'
			AND orihis = '".$whistoria."'
			AND oriing = '".$ingreso."';";

	//ESTA ES PARA QUE DEJE BUSCAR GENTE DE URGENCIAS 	AND oriing = '".$ingreso."'
	$q = "SELECT
			pacno1, pacno2, pacap1, pacap2, pactid, pacced, Ubisac, Ubihac, Ubisan, Ubihan, Ubialp, Ubiald, Ubiptr,
			".$wbasedato."_000018.fecha_data fecha_data, ".$wbasedato."_000018.hora_data hora_data, Pacnac,
			Ingres,Ingnre, '' fechaServicio, '' horaServicio, Cconom, Ccourg, Ccocir
		FROM
			root_000036, root_000037, ".$wbasedato."_000016, ".$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000011 ON Ubisac = Ccocod
		WHERE
			oriced = pacced
			AND Ubihis = Orihis
			AND Ubiing = Oriing
			AND Inghis = Orihis
			AND Inging = Oriing
			AND orihis = '".$whistoria."'
			AND Oriori = '10';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$info = mysql_fetch_array($res);

		$paciente->historiaClinica = $whistoria;
		$paciente->ingresoHistoriaClinica = $ingreso;
		$paciente->nombre1 = $info['pacno1'];
		$paciente->nombre2 = $info['pacno2'];
		$paciente->apellido1 = $info['pacap1'];
		$paciente->apellido2 = $info['pacap2'];
		$paciente->documentoIdentidad = $info['pacced'];
		$paciente->tipoDocumentoIdentidad = $info['pactid'];
		$paciente->fechaNacimiento = $info['Pacnac'];

		$paciente->servicioActual = $info['Ubisac'];
		$paciente->servicioAnterior = $info['Ubisan'];
		$paciente->habitacionActual = $info['Ubihac'];
		$paciente->habitacionAnterior = $info['Ubihan'];
		$paciente->numeroIdentificacionResponsable = $info['Ingres'];
		$paciente->nombreResponsable = $info['Ingnre'];

		$paciente->fechaHoraIngresoServicio = $info['fechaServicio']." ".$info['horaServicio'];

		$paciente->fechaIngreso = $info['fecha_data'];
		$paciente->horaIngreso = $info['hora_data'];

		$paciente->altaDefinitiva = $info['Ubiald'];
		$paciente->altaProceso = $info['Ubialp'];

		$estado = false;
		if($info['Ubiptr'] == 'on'){
			$paciente->ultimoMvtoHospitalario = "En proceso de traslado";
			$estado = true;
		}

		if ($info['Ubialp'] == 'on'){
			$paciente->ultimoMvtoHospitalario = "Alta en proceso";
			$estado = true;
		}

		if ($info['Ubiald'] == 'on') {
			$paciente->ultimoMvtoHospitalario = "Alta definitiva";
			$estado = true;
		}

		if(!$estado){
			$paciente->ultimoMvtoHospitalario = "En habitaci&oacute;n";
		}
		$paciente->nombreServicioActual = $info['Cconom'];

		//El paciente se encuentra en cirugia o en urgencias
		$paciente->enCirugia = isset($info['Ccocir']) && $info['Ccocir'] == "on" ? true : false;
		$paciente->enUrgencias = isset($info['Ccourg']) && $info['Ccourg'] == "on" ? true : false;

		//Edad
		$ann=(integer)substr($paciente->fechaNacimiento,0,4)*360 +(integer)substr($paciente->fechaNacimiento,5,2)*30 + (integer)substr($paciente->fechaNacimiento,8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$ann1=($aa - $ann)/360;
		$meses=(($aa - $ann) % 360)/30;
		if ($ann1<1){
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		} else {
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$ann1." año(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		}
		$paciente->edadPaciente = $wedad;
	}
	return $paciente;
}

function consultarCentrosCostosHospitalarios(){  
	global $wbasedato;
	global $conex;

	$q = "SELECT
				ccocod, UPPER(Cconom)
			FROM 
				".$wbasedato."_000011
			WHERE  
  				Ccohos = 'on'
			ORDER by 1;";
	 
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	 
	$coleccion = array();
	 
	if ($num1 > 0 )
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
				
			$consulta = new centroCostosDTO();
				
			$consulta->codigo = $row1[0];
			$consulta->nombre = $row1[1];
				
			$coleccion[] = $consulta;
		}
	}
	return $coleccion;
}

function consultarHabitacionesOcupadasServicio($servicio){
	global $wbasedato;
	global $conex;

	$q = "SELECT
				Habcod
			FROM 
				".$wbasedato."_000020
			WHERE 
  				Habdis = 'off'
  				AND Habest = 'on'
  				AND Habcco = '$servicio'
			ORDER by 1";
	 
	//	echo "Hab:".$q;

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	 
	$coleccion = array();
	 
	if ($num1 > 0 )
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
				
			$consulta = new habitacionDTO();
				
			$consulta->codigo = $row1[0];
				
			$coleccion[] = $consulta;
		}
	}
	return $coleccion;
}

function consultarMedicosTratantesHCE($whistoria,$wingreso){
	global $wbasedato;
	global $conex;

	global $esquemaBDHce;

	//Mtrhis  Mtring  Mtrmed  Mtrest  Medno1  Medno2  Medap1  Medap2
	$q = "SELECT DISTINCT
			Mtrmed,Medno1,Medno2,Medap1,Medap2,Mtrtra  
		FROM 
			".$esquemaBDHce."_000022, {$wbasedato}_000048  
		WHERE
			Mtrhis = '$whistoria'
			AND Mtring = '$wingreso'
			AND Meduma = Mtrmed
			AND Mtrest = 'on';";
	
	//Mtrhis  Mtring  Mtrmed  Mtrest  Medno1  Medno2  Medap1  Medap2
	$q = "SELECT DISTINCT
			Mtrmed,Medno1,Medno2,Medap1,Medap2,Mtrtra  
		FROM 
			".$esquemaBDHce."_000022, {$wbasedato}_000048  
		WHERE
			Mtrhis = '$whistoria'
			AND Mtring = '$wingreso'
			AND SUBSTRING_INDEX( Medesp, '-', 1 ) = SUBSTRING_INDEX( Mtrmed ,'-',-1 )
			AND CONCAT( Medtdo, '-', Meddoc ) = SUBSTRING_INDEX( Mtrmed ,'-',2 )
			AND Mtrest = 'on';";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	if($num > 0 ){
		while($cont1 < $num)
		{
			$info = mysql_fetch_array($res);

			$medico = new medicoDTO();

			//Mtrhis,Mtring,Mtrmed,Mtrest,Medno1,Medno2,Medap1,Medap2
			$medico->usuarioMatrix = $info['Mtrmed'];
				
			$medico->nombre1 = $info['Medno1'];
			$medico->nombre2 = $info['Medno2'];
			$medico->apellido1 = $info['Medap1'];
			$medico->apellido2 = $info['Medap2'];
			$medico->tratante = $info['Mtrtra'];

			$cont1++;

			$coleccion[] = $medico;
		}
	}

	return $coleccion;
}

function consultarMedicosTratantesTemporalKardex($historia, $ingreso, $fecha){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			".$wbasedato."_000048.id, Mettdo , Metdoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Metint, Metesp
		FROM 
			".$wbasedato."_000063, ".$wbasedato."_000048
		WHERE
			Metest = 'on'
			AND Mettdo = Medtdo
			AND Meddoc = Metdoc
			AND Methis = '".$historia."'
			AND Meting = '".$ingreso."'
			AND Metfek = '".$fecha."'
			AND Metesp = SUBSTRING_INDEX(Medesp,'-',1)
			AND Medest = 'on'
		;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	if($num > 0 ){
		while($cont1 <= $num)
		{
			$info = mysql_fetch_array($res);

			$medico = new medicoDTO();

			$medico->id = $info['id'];

			$medico->tipoDocumento = $info['Mettdo'];
			$medico->numeroDocumento = $info['Metdoc'];

			$medico->nombre1 = $info['Medno1'];
			$medico->nombre2 = $info['Medno2'];
			$medico->apellido1 = $info['Medap1'];
			$medico->apellido2 = $info['Medap2'];

			$medico->registroMedico = $info['Medreg'];
			$medico->telefono = $info['Medtel'];
				
			$medico->interconsultante = $info['Metint'];
			$medico->codigoEspecialidad = $info['Metesp'];

			$cont1++;

			$coleccion[] = $medico;
		}
	}

	return $coleccion;
}

function consultarMedicosTratantesDefinitivoKardex($historia, $ingreso, $fecha){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			".$wbasedato."_000048.id, Mettdo , Metdoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Metint, Metesp
		FROM 
			".$wbasedato."_000047, ".$wbasedato."_000048
		WHERE
			Metest = 'on'
			AND Mettdo = Medtdo
			AND Meddoc = Metdoc
			AND Methis = '".$historia."'
			AND Meting = '".$ingreso."'
			AND Metfek = '".$fecha."'
			AND Metesp = SUBSTRING_INDEX(Medesp,'-',1)
			AND Medest = 'on'
		;";

	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 <= $num)
	{
		$info = mysql_fetch_array($res);

		$medico = new medicoDTO();

		$medico->id = $info['id'];

		$medico->tipoDocumento = $info['Mettdo'];
		$medico->numeroDocumento = $info['Metdoc'];

		$medico->nombre1 = $info['Medno1'];
		$medico->nombre2 = $info['Medno2'];
		$medico->apellido1 = $info['Medap1'];
		$medico->apellido2 = $info['Medap2'];

		$medico->registroMedico = $info['Medreg'];
		$medico->telefono = $info['Medtel'];
		$medico->interconsultante = $info['Metint'];
		$medico->codigoEspecialidad = $info['Metesp'];

		$cont1++;

		$coleccion[] = $medico;
	}

	return $coleccion;
}

function consultarDietas(){
	global $wbasedato;
	global $conex;

	$q = "SELECT
				Diecod, UPPER(Diedes)
			FROM 
				".$wbasedato."_000041
			WHERE  
  				Dieest = 'on'
			ORDER by 1;";
	 
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	 
	$coleccion = array();
	 
	if ($num1 > 0 )
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
				
			$consulta = new DietaDTO();
				
			$consulta->codigo = $row1[0];
			$consulta->descripcion = $row1[1];
				
			$coleccion[] = $consulta;
		}
	}
	return $coleccion;
}

/**
 * Verifica si el encabezado del kardex existe o no
 *
 * @param unknown_type $conex
 * @param unknown_type $kardexGrabar
 */
function existeEncabezadoKardex($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;
	global $usuario;

	$existe = false;

	//Consulta el kardex del dia seleccionado, si es diferente al dia actual sera de consulta
	$q = "SELECT * FROM
			".$wbasedato."_000053
		WHERE
			Karest = 'on'
			AND Fecha_data = '".$fecha."' 
			AND Karhis = '".$historia."'
			AND Karing = '".$ingreso."'
			AND Karcco = '$usuario->centroCostosGrabacion';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){
		$existe = true;
	}
	return $existe;
}

/************************************************************************************
 * Busco la hora de creación del kardex y devuelvo los datos del encabezado, el mas 
 * antiguo de los encabezado en caso de tener varios.  Si tiene encabezado la función
 * retorna verdadero, de lo contrario retorna falso
 *
 * @param unknown_type $conex
 * @param unknown_type $kardexGrabar
 * 
 * Enero 14 de 2011
 ************************************************************************************/
function existeEncabezadoKardexSinCco($wbasedato,$conex,$historia,$ingreso,$fecha, &$datos ){
	
	$existe = false;

	//Consulta el kardex del dia seleccionado, si es diferente al dia actual sera de consulta
	$q = "SELECT *
		   	FROM ".$wbasedato."_000053
		  WHERE Karest = 'on'
		    AND Karhis = '".$historia."'
		    AND Karing = '".$ingreso."'
		    AND Fecha_data = '$fecha'
		  ORDER BY
		  	hora_data asc
		";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){
		
		$datos = mysql_fetch_array( $res );
		$existe = true;
	}

	return $existe;
}

function marcarGrabacionKardex($historia,$ingresoHistoria,$wfecha,$estado){
	global $wbasedato;
	global $conex;

	global $usuario;		//Información de usuario

	$marcado = false;
	$q = "UPDATE ".$wbasedato."_000053 SET
				Kargra = '$estado',
				Karusu = '$usuario->codigo'
			WHERE 
				Fecha_data = '$wfecha'
				AND Karhis = '$historia'
				AND Karing = '$ingresoHistoria'
				AND Karcco = '$usuario->centroCostosGrabacion'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	if(mysql_affected_rows() > 0){
		$marcado = true;
	}
	return $marcado;
}

function marcarAprobacionRegente($wbasedato,$historia,$ingresoHistoria,$wfecha,$estado,$usuario){
	$conexion = obtenerConexionBD("matrix");

	$marcado = "1";

	$q = "UPDATE ".$wbasedato."_000053 SET
				Karare = '$estado'
			WHERE 
				Fecha_data = '$wfecha'
				AND Karhis = '$historia'
				AND Karing  = '$ingresoHistoria'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Registro de auditoria de kardex
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingresoHistoria;
	$auditoria->descripcion = "-";
	$auditoria->fechaKardex = $wfecha;
	if($estado == "on"){
		$auditoria->mensaje = obtenerMensaje('MSJ_KARDEX_DESAPROBADO');
	} else {
		$auditoria->mensaje = obtenerMensaje('MSJ_KARDEX_APROBADO');
	}
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

	return $marcado;
}

function crearEncabezadoKardexAnterior($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	global $usuario;		//Información de usuario

	$creado = false;

	//Consulta el kardex del dia seleccionado, si es diferente al dia actual sera de consulta Karmeg,
	$q = "INSERT INTO {$wbasedato}_000053 (Medico,Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Kardie,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karfir,Karpal,Karmez,Seguridad)
				SELECT 
			Medico,'$fecha',Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Kardie,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,'$usuario->codigo','',Karpal,Karmez,Seguridad
		FROM 
			".$wbasedato."_000053
		WHERE
			Karest = 'on'
			AND Fecha_data = DATE_ADD('".$fecha."', INTERVAL -1 DAY) 
			AND Karhis = '".$historia."'
			AND Karing = '".$ingreso."'
			AND Karcco = '$usuario->centroCostosGrabacion';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	if(mysql_affected_rows() > 0){
		$creado = true;
	}
	return $creado;
}

function grabadoEncabezadoKardexFecha($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;
	global $usuario;		//Información de usuario

	$grabado = false;

	//Consulta el kardex del dia seleccionado, si es diferente al dia actual sera de consulta
	$q = "SELECT
			Kargra
		FROM 
			".$wbasedato."_000053
		WHERE
			Karest = 'on'
			AND Fecha_data = '".$fecha."' 
			AND Karhis = '".$historia."'
			AND Karing = '".$ingreso."'
			AND Karcco = '".$usuario->centroCostosGrabacion."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	if($fila['Kargra'] == 'on'){
		$grabado = true;
	}
	return $grabado;
}

/**
 * Consulta el kardex para su modificacion (actual) o si no se encuentra el actual para su creación .
 *
 * Reglas:
 *
 * 1. Si se ingresa una fecha de consulta diferente a la fecha presente y ademas antes del dia anterior a la fecha actual, el kardex
 * será solo de consulta.
 * 2. Si se ingresa una fecha de consulta del dia actual, se consultará el kardex del dia y si no se encuentra se traerá el del dia anterior
 * para modificación.
 *
 * (2010-06-03):> 3. El kardex ahora diferencia los centros de costos, se discrimina el centro de costos de acuerdo a root_000025
 *
 * @param unknown_type $wfecha
 * @param unknown_type $paciente
 * @return unknown
 */
function consultarKardexPorFechaPaciente($wfecha, $paciente){
	global $wbasedato;
	global $conex;

	global $usuario;			//Usuario que ingresa al kardex

	$kardex = new kardexDTO();

	$kardex->editable = false;
	$kardex->esAnterior = false;
	$kardex->esPrimerKardex = true;
	
	$esFechaActual = ($wfecha == date("Y-m-d"));

	//Consulta el kardex del dia seleccionado, si es diferente al dia actual sera de consulta
	$q = "SELECT
			Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Kardie,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karmeg,Karprc
		FROM 
			".$wbasedato."_000053
		WHERE
			Karest = 'on'
			AND Fecha_data = '".$wfecha."' 
			AND Karhis = '".$paciente->historiaClinica."'
			AND Karing = '".$paciente->ingresoHistoriaClinica."'
			AND Karcco = '$usuario->centroCostosGrabacion';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){
		if($esFechaActual){
			$kardex->editable = true;
		}
	}

	//Si se consulta el dia actual y no hay kardex del dia actual se consulta el dia anterior
	if($num == 0 && $esFechaActual){
		$q = "SELECT
				Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Kardie,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karmeg,Karprc
			FROM 
				".$wbasedato."_000053
			WHERE
				Karest = 'on'
				AND Fecha_data = DATE_SUB('".$wfecha."',INTERVAL 1 DAY) 
				AND Karhis = '".$paciente->historiaClinica."'
				AND Karing = '".$paciente->ingresoHistoriaClinica."'
				AND Karcco = '$usuario->centroCostosGrabacion';";
				
		$q = "SELECT
				Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Kardie,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karmeg,Karprc
			FROM 
				".$wbasedato."_000053
			WHERE
				Karest = 'on'
				AND Fecha_data <= DATE_SUB('".$wfecha."',INTERVAL 1 DAY) 
				AND Karhis = '".$paciente->historiaClinica."'
				AND Karing = '".$paciente->ingresoHistoriaClinica."'
				AND Karcco = '$usuario->centroCostosGrabacion'
			ORDER BY Fecha_data Desc;";
				

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		if($num > 0 ){
			if($esFechaActual){
				$kardex->editable = true;
				$kardex->esAnterior = true;
			}
		}
	}

	if ($num > 0){
		$info = mysql_fetch_array($res);

		$kardex->historiaClinica 			= $info['Karhis'];
		$kardex->ingresoHistoriaClinica 	= $info['Karing'];
		$kardex->fechaCreacion 				= $info['Fecha_data'];
		$kardex->horaCreacion 				= $info['Hora_data'];
		$kardex->observaciones 				= $info['Karobs'];
		$kardex->estado 					= $info['Karest'];
		$kardex->diagnostico 				= $info['Kardia'];
		$kardex->rutaOrdenMedica 			= $info['Karrut'];
		$kardex->talla 						= $info['Kartal'];
		$kardex->peso 						= $info['Karpes'];
		$kardex->antecedentesAlergicos 		= $info['Karale'];
		$kardex->cuidadosEnfermeria 		= $info['Karcui'];
		$kardex->terapiaRespiratoria 		= $info['Karter'];
		$kardex->confirmado 				= $info['Karcon'];
		$kardex->sondasCateteres 			= $info['Karson'];
		$kardex->curaciones 				= $info['Karcur'];
		$kardex->interconsulta 				= $info['Karint'];
		$kardex->consentimientos 			= $info['Kardec'];
		$kardex->medidasGenerales 			= $info['Karmeg'];
		$kardex->obsDietas 					= $info['Kardie'];
		$kardex->procedimientos				= $info['Karprc'];
		$kardex->dextrometer 				= $info['Kardem'];
		$kardex->cirugiasPendientes 		= $info['Karcip'];
		$kardex->terapiaFisica 				= $info['Kartef'];
		$kardex->rehabilitacionCardiaca 	= $info['Karrec'];
		$kardex->antecedentesPersonales 	= $info['Karanp'];
		$kardex->aislamientos 				= $info['Karais'];
		$kardex->aprobado 					= $info['Karare'] == 'on' ? true : false;
		$kardex->esPrimerKardex 			= false;
		$kardex->grabado 					= $info['Kargra'];

		$kardex->centroCostos 				= $info['Karcco'];
		
		$q1 = "SELECT CONCAT(Codigo,' - ',Descripcion) Usuario FROM usuarios WHERE Codigo = '{$info['Karusu']}'";
		$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
		$num1 = mysql_num_rows($res1);
		if($num1 > 0){
			$info1 = mysql_fetch_array($res1);
		}
//		$paciente = $info1['Usuario'];

		$kardex->usuarioQueModifica 		= $info['Karusu'];
		$kardex->nombreUsuarioQueModifica	= $info1['Usuario'];
	}
	//****************2010-10-07****Consulto el ultimo movimiento hospitalario
	/*La no acumulacion de saldos depende de lo siguiente:
	 * 0. Los indicadores estan en el objeto paciente y estan marcados como enCirugia y enUrgencias
	 * 1. Si el paciente se encuentra en urgencias o cirugia NO acumula saldos, debido a que no se graba por matrix:
	 * 		Los medicamentos no se envian con el paciente en ninguno de los dos servicios por lo tanto hay un corte en la dispensacion que debe simularse.
	 * 		NOTA:: Cuando el paciente se encuentra en urgencias no hay movimientos en la tabla 17
	 * 2. Si no se encuentra en urgencias o cirugia se debe preguntar por la fecha y hora del ultimo traslado y si el kardex ha sido generado.
	 * 		Si el servicio anterior es urgencias y el encabezado del kardex no ha sido generado consulto
	 */
	$kardex->noAcumulaSaldoDispensacion = false;
	$kardex->descontarDispensaciones = false;

	if($paciente->enCirugia || $paciente->enUrgencias){
		$kardex->noAcumulaSaldoDispensacion = true;
	} else {
		/* El Paciente no esta en urgencias ni en cirugia (Los demás servicios acumulan de saldos de dispensación).
		 * 1. Consulta del traslado del dia anterior.
		 * 2. Consulta si el servicio anterior es de urgencias o cirugia
		 * 3. Consulta de la fecha y hora del ultimo traslado para compararlo con la creación de encabezado de kardex
		 */
		$qMv = "SELECT
						".$wbasedato."_000017.Fecha_data,".$wbasedato."_000017.hora_data,Eyrsor,Eyrsde,Eyrhor,Ccourg,Ccocir,Ccoing 
					FROM 
						".$wbasedato."_000017, ".$wbasedato."_000011 
					WHERE 
						Eyrhis = '$paciente->historiaClinica' 
						AND Eyring = '$paciente->ingresoHistoriaClinica' 
						AND Eyrtip = 'Recibo' 
						AND ".$wbasedato."_000017.Fecha_data = DATE_SUB('".$wfecha."',INTERVAL 1 DAY) 
						AND Ccocod = Eyrsor	
						AND Eyrest='on';";

		$resMv = mysql_query($qMv,$conex) or die ("Error: " . mysql_errno() . " - en el querys: $qMv - " . mysql_error());
		$contMv = mysql_num_rows($resMv);
		if($contMv>0){
			$infoMv = mysql_fetch_array($resMv);
			//Si hubo traslado el dia anterior, se verifica que sea de urgencias o cirugia
			if(isset($infoMv['Ccourg']) && isset($infoMv['Ccocir']) && isset($infoMv['Ccoing']) && ($infoMv['Ccourg'] == "on" || $infoMv['Ccocir'] == "on" || $infoMv['Ccoing'] == "on")){
				//Si el kardex no fue creado en la misma fecha del traslado, no acumula saldo
				if($kardex->fechaCreacion != $wfecha){
					$kardex->descontarDispensaciones = true; 
					$kardex->noAcumulaSaldoDispensacion = true;	
				} else { //Si el kardex fue creado en la misma fecha del traslado, si acumula saldo
					$kardex->noAcumulaSaldoDispensacion = false;
				}
			} //El paciente en el traslado anterior no estuvo en urgencias o cirugia, necesariamente, debe estar en un serv. hospitalario
			$kardex->horaDescuentoDispensaciones = $infoMv['hora_data'];
		} else {
			//Si no hubo traslado el dia anterior consulto posibles traslados en el dia actual
			$qMv = "SELECT
						".$wbasedato."_000017.Fecha_data,".$wbasedato."_000017.hora_data,Eyrsor,Eyrsde,Eyrhor,Ccourg,Ccocir,Ccoing 
					FROM 
						".$wbasedato."_000017, ".$wbasedato."_000011 
					WHERE 
						Eyrhis = '$paciente->historiaClinica' 
						AND Eyring = '$paciente->ingresoHistoriaClinica' 
						AND Eyrtip = 'Recibo' 
						AND ".$wbasedato."_000017.Fecha_data = '".$wfecha."' 
						AND Ccocod = Eyrsor	
						AND Eyrest='on';";

			if($contMv>0){
				$infoMv = mysql_fetch_array($resMv);
				//Si hubo traslado del dia, se verifica que sea de urgencias o cirugia
				if(isset($infoMv['Ccourg']) && isset($infoMv['Ccocir']) && isset($infoMv['Ccoing']) && ($infoMv['Ccourg'] == "on" || $infoMv['Ccocir'] == "on" || $infoMv['Ccoing'] == "on")){
					//Si el kardex no fue creado en la misma fecha del traslado, no acumula saldo
					if($kardex->fechaCreacion != $wfecha){
						$kardex->noAcumulaSaldoDispensacion = false;
					} else { //Si el kardex fue creado en la misma fecha del traslado, si acumula saldo
						$kardex->noAcumulaSaldoDispensacion = true;
					}
				} //El paciente en el traslado anterior no estuvo en urgencias o cirugia, necesariamente, debe estar en un serv. hospitalario
				$kardex->horaDescuentoDispensaciones = $infoMv['hora_data'];
			}
		}
	}

	return $kardex;
}

function crearKardex($kardex, $kardexGrabado = 'on'){
	global $wbasedato;
	global $conex;
	
	//Consulto si el paciente se encuentra en urgencias o cirugia
	$recalcular = 'off';
	if( estaUrgenciaCirugia( $conex, $wbasedato, $kardex->historia,$kardex->ingreso ) ){
		$recalcular = 'on';
	}

	$q="INSERT INTO
			".$wbasedato."_000053(Medico,Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karmeg,Kardie,Karprc,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karfir,Karpal,Karmez,Karrca,Karegr,Seguridad)
		VALUES
			('mhosidc','$kardex->fechaCreacion','$kardex->horaCreacion','$kardex->historia','$kardex->ingreso','$kardex->observaciones','on','$kardex->diagnostico','$kardex->rutaOrdenMedica','$kardex->talla','$kardex->peso','$kardex->antecedentesAlergicos','$kardex->cuidadosEnfermeria','$kardex->terapiaRespiratoria','$kardex->confirmado','$kardex->sondasCateteres','$kardex->curaciones','$kardex->interconsulta','$kardex->consentimientos','$kardex->medidasGenerales',
			'$kardex->obsDietas','$kardex->procedimientos','$kardex->dextrometer','$kardex->cirugiasPendientes','$kardex->terapiaFisica','$kardex->rehabilitacionCardiaca','$kardexGrabado','$kardex->antecedentesPersonales','$kardex->aislamientos','off',
			'$kardex->centroCostos','$kardex->usuarioQueModifica','$kardex->firmaDigital','','','$recalcular','$kardex->indicaciones','A-$kardex->usuario');";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Registro de auditoria de kardex
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $kardex->historia;
	$auditoria->ingreso = $kardex->ingreso;
	$auditoria->descripcion = "-";
	$auditoria->fechaKardex = $kardex->fechaCreacion;

	if(isset($kardex->confirmado) && $kardex->confirmado == "on"){
		$auditoria->descripcion = "Confirmado";
	} else {
		$auditoria->descripcion = "No confirmado";
	}

	$auditoria->mensaje = obtenerMensaje('MSJ_KARDEX_CREADO');
	$auditoria->seguridad = $kardex->usuario;

	registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
	
	
	// Registro auditoria información demográfica
	$auditoria->mensaje = "Informacion demografica - ".obtenerMensaje('MSJ_KARDEX_CREADO');
	$auditoria->descripcion = $kardex->diagnostico.",".$kardex->antecedentesAlergicos.",".$kardex->antecedentesPersonales;
	registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
	
	// Registro auditoria medidas generales
	$auditoria->mensaje = "Medidas generales - ".obtenerMensaje('MSJ_KARDEX_CREADO');
	$auditoria->descripcion = $kardex->procedimientos.",".$kardex->aislamientos.",".$kardex->terapiaRespiratoria.",".$kardex->interconsulta.",".$kardex->cirugiasPendientes.",".$kardex->sondasCateteres.",".$kardex->curaciones.",".$kardex->medidasGenerales;
	registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
}

function actualizarKardex($kardexGrabar,$vecPestanas,$kardexGrabado = 'on'){
	global $wbasedato;
	global $conex;

	global $usuario;		//Información de usuario
	$indicePestana = 1;

	//Verificacion de la fecha actual del sistema
	$esFechaActual = ($kardexGrabar->fechaCreacion == $kardexGrabar->fechaGrabacion);

	//Actualizo los campos del kardex
	$q="UPDATE ".$wbasedato."_000053
		SET
			Kargra = '$kardexGrabado',
			Karusu = '$usuario->codigo',		
			Karcon = '$kardexGrabar->confirmado',
			Karfir = '$kardexGrabar->firmaDigital'";
	
//	$q="UPDATE ".$wbasedato."_000053
//		SET
//			Kargra = 'on',
//			Karusu = '$usuario->codigo',		
//			Karcon = '$kardexGrabar->confirmado'";
//	
//	if( $usuario->firmaElectronicamente ){
//		$q = $q.",Karfir = '$kardexGrabar->firmaDigital'";
//	}

	if(isset($vecPestanas[$indicePestana]) && $vecPestanas[$indicePestana]){
		$q = $q.",Kartal = '$kardexGrabar->talla',
			Karpes = '$kardexGrabar->peso',
			Kardia = '$kardexGrabar->diagnostico',
			Karale = '$kardexGrabar->antecedentesAlergicos',
			Karanp = '$kardexGrabar->antecedentesPersonales'";
	}

	$indicePestana = 5;
	if(isset($vecPestanas[$indicePestana]) && $vecPestanas[$indicePestana]){
		$q = $q.",Karcui = '$kardexGrabar->cuidadosEnfermeria',
			Karobs = '$kardexGrabar->observaciones'
		";
	}

	$indicePestana = 6;
	if(isset($vecPestanas[$indicePestana]) && $vecPestanas[$indicePestana]){
		$q = $q.",Kardem = '$kardexGrabar->dextrometer'";
	}

	$indicePestana = 11;
	if(isset($vecPestanas[$indicePestana]) && $vecPestanas[$indicePestana]){
		$q = $q.",Karegr = '$kardexGrabar->indicaciones'";
	}
	
	$indicePestana = 10;
	if(isset($vecPestanas[$indicePestana]) && $vecPestanas[$indicePestana]){
		$q = $q.",Karmeg = '$kardexGrabar->medidasGenerales',
			Karprc = '$kardexGrabar->procedimientos',
			Karter = '$kardexGrabar->terapiaRespiratoria',
			Karobs = '$kardexGrabar->observaciones',
			Karcur = '$kardexGrabar->curaciones',
			Karint = '$kardexGrabar->interconsulta',
			Karson = '$kardexGrabar->sondasCateteres',
			Kardec = '$kardexGrabar->consentimientos',
			Karpal = '$kardexGrabar->preparacionAlta',
			Kardie = '$kardexGrabar->obsDietas',
			Karmez = '$kardexGrabar->mezclas',
			Kardem = '$kardexGrabar->dextrometer',
			Karcip = '$kardexGrabar->cirugiasPendientes',
			Kartef = '$kardexGrabar->terapiaFisica',
			Karrec = '$kardexGrabar->rehabilitacionCardiaca',
			Karais = '$kardexGrabar->aislamientos',
			Kardec = '$kardexGrabar->consentimientos'";
	}

	$q = $q." WHERE
			Karhis = '$kardexGrabar->historia'
			AND Karing = '$kardexGrabar->ingreso'
			AND Karcco = '$usuario->centroCostosGrabacion'
			AND Fecha_data = '$kardexGrabar->fechaCreacion'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Registro de auditoria de kardex
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $kardexGrabar->historia;
	$auditoria->ingreso = $kardexGrabar->ingreso;
	$auditoria->descripcion = "-";

	if(isset($kardexGrabar->confirmado) && $kardexGrabar->confirmado == "on"){
		$auditoria->descripcion = "Confirmado";
	} else {
		$auditoria->descripcion = "No confirmado";
	}
	$auditoria->fechaKardex = $kardexGrabar->fechaCreacion;
	$auditoria->mensaje = obtenerMensaje('MSJ_KARDEX_ACTUALIZADO');
	$auditoria->seguridad = $kardexGrabar->usuario;

	registrarAuditoriaKardex($conex,$wbasedato,$auditoria);

	
	// Registro auditoria información demográfica
	$auditoria->mensaje = "Informacion demografica - ".obtenerMensaje('MSJ_KARDEX_ACTUALIZADO');
	$auditoria->descripcion = $kardexGrabar->diagnostico.",".$kardexGrabar->antecedentesAlergicos.",".$kardexGrabar->antecedentesPersonales;
	registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
	
	// Registro auditoria medidas generales
	$auditoria->mensaje = "Medidas generales - ".obtenerMensaje('MSJ_KARDEX_ACTUALIZADO');
	$auditoria->descripcion = $kardexGrabar->procedimientos.",".$kardexGrabar->aislamientos.",".$kardexGrabar->terapiaRespiratoria.",".$kardexGrabar->interconsulta.",".$kardexGrabar->cirugiasPendientes.",".$kardexGrabar->sondasCateteres.",".$kardexGrabar->curaciones.",".$kardexGrabar->medidasGenerales;
	registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
}

function incrementarConsecutivoCentroCostos($historia,$ingreso,$wfecha){
	global $wbasedato;
	global $conex;

	//Verificacion de que las ordenes existan y firmadas
	//Medico  Fecha_data  Hora_data  Ordfec  Ordhor  Ordhis  Ording  Ordtor  Ordnro  Ordobs  Ordesp  Ordest  Ordfir  Seguridad  id
	$q = "SELECT DISTINCT
			Ordtor
		FROM 
			hceidc_000027
		WHERE
			Ordhis = '$historia'
			AND Ording = '$ingreso'
			AND Ordest = 'on'
			AND Ordfir != ''
			AND Ordesp = 'Pendiente'
			AND Ordfec = '$wfecha';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	while($info = mysql_fetch_array($res)){
		//Actualizar centro de costos
		$q2 = "UPDATE ".$wbasedato."_000011 SET
					Ccocor = Ccocor + 1
				WHERE 
					Ccocod = '{$info['Ordtor']}';";

		$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	}
}

function grabarAlertaHCE($historia,$ingreso,$wfecha,$alertas,$codUsuario){
	global $wbasedato;
	global $conex;

	//Verificacion de que las ordenes existan y firmadas
	//Detpro  Detcon  Detorp  Dettip  Detdes  Detarc  Detcav  Detvde  Detnpa  Detvim  Detume  Detcol  Dethl7  Detjco  Detsiv  Detase  Detved  Detimp  Detimc  Detvco  Detvcr  Detobl  Detdep  Detcde  Deturl  Detfor  Detcco  Detcac  Detnse  Detfac  Detest  Detcoa  Detprs  Detalm  Detanm  Detlrb  Detdde  detcbu  detnbu  dettta  detcua  detccu  detcro  dettii  Detdpl
	$q = "SELECT
			Encpro,Encdes,Enctus,Enctfo,Enctim,Encale,Enccol,Encnfi,Encnco,Encest,Encvis,Encmax,Detpro,Detcon,Detorp,Dettip 
		FROM 
			hceidc_000001, hceidc_000002
		WHERE
			Encale = 'on'
			AND Encest = 'on'
			AND Encpro = Detpro;";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	if($info = mysql_fetch_array($res)){
		$q2 = "UPDATE hceidc_{$info['Encpro']} SET
					movdat = '$alertas'
				WHERE
					movpro = '{$info['Encpro']}'
					AND movcon = '{$info['Detcon']}'
					AND movtip = '{$info['Dettip']}'
					AND movhis = '$historia'
					AND moving = '$ingreso'
					AND fecha_data = '$wfecha';";

		$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
		if(mysql_affected_rows() == 0){
			$q2 = "INSERT INTO hceidc_{$info['Encpro']}
						(Medico,Fecha_data,Hora_data,movpro,movcon,movhis,moving,movtip,movdat,movusu,Seguridad)
					VALUES 
						('hceidc', '$wfecha','".date("H:i:s")."', '{$info['Encpro']}', '{$info['Detcon']}', '$historia', '$ingreso', '{$info['Dettip']}', '$alertas', '$codUsuario','C-hceidc');";

			$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
		}
	}
}

function ultimaFechaKardexHistoriaIngreso($whistoria,$wingreso){
	global $wbasedato;
	global $conex;

	$fecha = "";

	$q = "SELECT
			MAX(Fecha_data) fechaKardex
		FROM 
			".$wbasedato."_000053
		WHERE
			Karest = 'on'
			AND Karhis = '".$whistoria."'
			AND Karing = '".$wingreso."';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){
		$info = mysql_fetch_array($res);

		$fecha = $info['fechaKardex'];
	}
	return $fecha;
}

function actualizarRutaArchivoKardex($historia,$ingreso,$fecha,$ruta,$usuario){
	global $conex;
	global $wbasedato;

	//Actualizo los campos del kardex
	$q="UPDATE ".$wbasedato."_000053
		SET
			Karrut = '$ruta'		
		WHERE
			Karhis = '$historia'
			AND Karing = '$ingreso'
			AND Fecha_data = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
	//Registro de auditoria de kardex
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "<a href=$ruta target=_blank>$ruta</a>";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = obtenerMensaje('MSJ_ARCHIVO_CARGADO');
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
}


/**
 * Consulta el detalle del kardex del dia anterior para mostrarlo en pantalla (en caso de no tener uno el dia seleccionado)
 * @param unknown_type $historia
 * @param unknown_type $ingreso
 * @param unknown_type $wfecha
 */
function consultarDetalleTemporalKardex($historia,$ingreso,$fecha,$tipoProtocolo,$pestAlta='off'){
	//,$centroCostosMovimiento,$gruposMedicamentosMovimiento
	global $wbasedato;
	global $wcenpro;
	global $conex;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosServicioFarmaceutico;
	global $centroCostosCentralMezclas;
	global $usuario;		//Información de usuario

	//Protocolos
	global $protocoloNormal;
	global $protocoloNutricion;
	global $protocoloAnalgesia;
	global $protocoloQuimioterapia;

	global $nombreProtocoloNormal;
	global $nombreProtocoloNutricion;
	global $nombreProtocoloAnalgesia;
	global $nombreProtocoloQuimioterapia;
	
	global $regletaFamilia;
	
	$coleccion = array();

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	if($usuario->gruposMedicamentos != "*" && $usuario->gruposMedicamentos != '' && $usuario->gruposMedicamentos != 'NO APLICA'){
		$tieneGruposIncluidos = true;
	}
	//********************************
	
	// $condicionFecha = " AND Kadfec = '$fecha' ";
	$condicionFecha = " ";
	$condicionAlta = " AND Kadalt != 'on' ";
	$condicionGroupBy = "";
	if($pestAlta=='on')
	{
		$condicionAlta = "";
		//$condicionFecha = "";
		$condicionGroupBy = " GROUP BY Kadart ";
	}
		
	/****************************************************************************************
	 * Junio 14 de 2012
	 ****************************************************************************************/
	$validarCco = "";
	
	if( $usuario->centroCostosGrabacion != '*' ){
		$validarCco = " AND Kadcco = '$usuario->centroCostosGrabacion'";
	}
	/****************************************************************************************/

		// $q = "SELECT
				// Kadart,Artcom,Artgen,Artuni,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcdi,Kaddis,Kadpro,Kadcco,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kaddis,Kaduma,Kadcma,Kadnar, Artest, Kadusu, Famcod, Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
			// FROM
				// ".$wbasedato."_000060, ".$wbasedato."_000026, ".$wbasedato."_000115, ".$wbasedato."_000114
			// WHERE
				// Kadhis = '$historia'
				// AND Kading = '$ingreso'
				// $condicionFecha ";
				// if($tieneGruposIncluidos){
					// $q .= " AND Kadori = '$codigoServicioFarmaceutico' ";
					// $q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
				// }
				// $q .= " AND Kadpro LIKE '$tipoProtocolo'
				// AND Artcod = Kadart
				// AND kadori = '$codigoServicioFarmaceutico'
				// AND Kadsus != 'on'
				// AND Kadart = Relart
				// AND Relfam = Famcod
				// $condicionAlta
				// $validarCco
				// $condicionGroupBy ";

	// $subConsulta = " SELECT
						// Kadart,Artcom,Artgen,Artuni,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcdi,Kaddis,Kadpro,Kadcco,'' Artgru,Kaddis,Kaduma,Kadcma,Kadnar, Artest, Kadusu, Famcod, Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
					// FROM
						// ".$wbasedato."_000060, cenpro_000002, ".$wbasedato."_000115, ".$wbasedato."_000114
					// WHERE
						// Kadhis = '$historia'
						// AND Kading = '$ingreso'
						// $condicionFecha
						// AND Kadpro LIKE '$tipoProtocolo'
						// AND Kadori = '$codigoCentralMezclas'
						// AND Artcod = Kadart
						// AND Kadsus != 'on'
						// AND Kadart = Relart
						// AND Relfam = Famcod
						// $condicionAlta
						// $validarCco
						// $condicionGroupBy ";
						
						
	$q = "SELECT
				Kadart,Artcom,Artgen,Artuni,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcdi,Kaddis,Kadpro,Kadcco,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kaddis,Kaduma,Kadcma,Kadnar, Artest, Kadusu, 'a' as Famcod, 'a' as Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, '' as Relcon, Artuni as Reluni, Artpos, Kadido
			FROM
				".$wbasedato."_000060, ".$wbasedato."_000026
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				$condicionFecha ";
				if($tieneGruposIncluidos){
					$q .= " AND Kadori = '$codigoServicioFarmaceutico' ";
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
				}
				$q .= " AND Kadpro LIKE '$tipoProtocolo'
				AND Artcod = Kadart
				AND kadori = '$codigoServicioFarmaceutico'
				AND Kadsus != 'on'
				$condicionAlta
				$validarCco
				$condicionGroupBy ";

	$subConsulta = " SELECT
						Kadart,Artcom,Artgen,Artuni,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcdi,Kaddis,Kadpro,Kadcco,'' Artgru,Kaddis,Kaduma,Kadcma,Kadnar, Artest, Kadusu, 'a' as Famcod, 'a' as Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, '' as Relcon, Artuni as Reluni, '' as Artpos, Kadido
					FROM
						".$wbasedato."_000060, ".$wcenpro."_000002
					WHERE
						Kadhis = '$historia'
						AND Kading = '$ingreso'
						$condicionFecha
						AND Kadpro LIKE '$tipoProtocolo'
						AND Kadori = '$codigoCentralMezclas'
						AND Artcod = Kadart
						AND Kadsus != 'on'
						$condicionAlta
						$validarCco
						$condicionGroupBy ";

	if($usuario->esUsuarioCM){
		$q = $subConsulta;
	} else {
		if(!$tieneGruposIncluidos){
			$q = $q." UNION ".$subConsulta;
		}
	}
	$q = $q." ORDER BY Artcom ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		$auxFamilia = "";

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new detalleKardexDTO();

			//$rondasFamilia = array();
			
			$vectorDosis = array();
			
			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			
			$nombreArticulo = "";
			if(isset($info['Kadnar']) && !empty($info['Kadnar'])){
				$detalle->nombreArticulo	= $info['Kadnar'];
				$nombreArticulo = $detalle->nombreArticulo;
			} else {
				$nombreArticulo = $info['Artcom'];
			}
			
			$detalle->codigoFamilia = $info['Famcod'];
			$detalle->nombreFamilia = $info['Famnom'];
			$codFamilia = $info['Famcod'];

			/***********************************************************************/
			/******************* Calculo de regleta por familia ********************/
			/***********************************************************************/

			$colPeriodicidades = consultarPeriodicidades();
			foreach ($colPeriodicidades as $periodicidad){
				if($periodicidad->codigo == $info['Kadper']){
					$horaPeriodicidad = intval($periodicidad->equivalencia);
					break;
				}
			}
			
			$arrAplicacion = array();
			
			$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),$info['Kadfin'],$info['Kadhin'],$horaPeriodicidad,$info['Kadcfr']);
			
			$horaArranque = 2;

			$cont3 = 1;
			$cont2 = $horaArranque;   //Desplazamiento desde la hora inicial
			$caracterMarca = $info['Kadcfr'];

			while($cont3 <= 24)
			{
			
				if(!isset($arrAplicacion[$cont2]) || $arrAplicacion[$cont2]=="" || $arrAplicacion[$cont2]==" " || $arrAplicacion[$cont2]=="-")
					$arrAplicacion[$cont2] = 0;
				
				if(!array_key_exists($codFamilia,$regletaFamilia))
					$regletaFamilia[$codFamilia] = array();
				
				if(array_key_exists($cont2,$regletaFamilia[$codFamilia]))
				{
					$regletaFamilia[$codFamilia][$cont2]['valor'] += (integer)$arrAplicacion[$cont2]*1;
				} 
				else 
				{
					$regletaFamilia[$codFamilia][$cont2] = array();
					$regletaFamilia[$codFamilia][$cont2]['valor'] = (integer) $arrAplicacion[$cont2]*1;
				}

				if($arrAplicacion[$cont2] > 0)
				{
					if(isset($regletaFamilia[$codFamilia][$cont2]['condicion']) && $regletaFamilia[$codFamilia][$cont2]['condicion']!="")
					{
						$pos = strpos($regletaFamilia[$codFamilia][$cont2]['condicion'], "|".$info['Kadcnd']);
						if ($pos === false && $regletaFamilia[$codFamilia][$cont2]['condicion'] != $info['Kadcnd'])
							$regletaFamilia[$codFamilia][$cont2]['condicion'] .= "|".$info['Kadcnd'];
					}
					else
					{
						$regletaFamilia[$codFamilia][$cont2]['condicion'] = $info['Kadcnd'];
					}	

					if(isset($regletaFamilia[$codFamilia][$cont2]['unidad']) && $regletaFamilia[$codFamilia][$cont2]['unidad']!="")
					{
						$pos = strpos($regletaFamilia[$codFamilia][$cont2]['unidad'], "|".$info['Kadufr']);
						if ($pos === false && $regletaFamilia[$codFamilia][$cont2]['unidad'] != $info['Kadufr'])
							$regletaFamilia[$codFamilia][$cont2]['unidad'] .= "|".$info['Kadufr'];
					}
					else
					{
						$regletaFamilia[$codFamilia][$cont2]['unidad'] = $info['Kadufr'];
					}
					
					if(!isset($regletaFamilia[$codFamilia][$cont2]['cont']))
						$regletaFamilia[$codFamilia][$cont2]['cont'] = 1; 
					else
						$regletaFamilia[$codFamilia][$cont2]['cont']++; 
						
					if(isset($regletaFamilia[$codFamilia][$cont2]['cont']) && $regletaFamilia[$codFamilia][$cont2]['cont'] % 2 != 0)
						$bgTooltip = "#C3D9FF";
					else
						$bgTooltip = "#E8EEF7";
					
					if(!isset($regletaFamilia[$codFamilia][$cont2]['tooltip']) || $regletaFamilia[$codFamilia][$cont2]['tooltip']=="")
					{
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] = '<table cellspacing=4>';
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] .= '<tr style=background-color:#2A5DB0;color:#ffffff><td align=center> Código </td><td align=center> Nombre </td><td align=center> Dosis </td><td align=center> Unidad </td><td align=center> Frecuencia </td><td align=center> Vía </td><td align=center> Inicio </td><td align=center> Condición </td></tr>';
					}
					
					if(isset($regletaFamilia[$codFamilia][$cont2]['tooltip']))
					{
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] .= '<tr style=background-color:'.$bgTooltip.'><td> '.$info['Kadart'].' </td><td> '.trim($info['Artgen']).' </td><td> '.$info['Kadcfr'].' '.$info['Kadufr'].' </td><td align=center> '.$info['Artuni'].' </td><td align=center> '.$info['Kadper'].' </td><td align=center> '.$info['Kadvia'].' </td><td align=center> '.$info['Kadfin'].' </td><td align=center> '.$info['Kadcnd'].' </td></tr>';
					}
				}
				
				if($cont2 == 24){
					$cont2 = 0;
				}

				$cont3++;
				$cont2++;

				if($cont2 % 2 != 0){
					$cont2++;
				}
				if($cont3 % 2 != 0){
					$cont3++;
				}

				if($cont2 == $horaArranque){
					break;
				}
			}
			
			// Indico si hay mas de un articulo en la familia
			if($auxFamilia == $codFamilia)
				$regletaFamilia[$codFamilia]['esCompuestaFamilia'] = '1';

			$auxFamilia = $codFamilia;
			
			//$regletaFamilia = $rondasFamilia;
			
			unset($arrAplicacion);
			//unset($rondasFamilia);
			unset($colPeriodicidades);
			
			/********************************************************************/
			
			$detalle->codigoArticulo = $info['Kadart']."-".$info['Artgen'];
			//$detalle->codigoArticulo = $info['Famnom']." ".$info['Relcon']." ".$info['Reluni'];
			$detalle->cantidadDosis = $info['Kadcfr'];
			$detalle->unidadDosis = $info['Kadufr'];
			$detalle->diasTratamiento = $info['Kaddia'];
			$detalle->estadoRegistro = $info['Kadest'];
			$detalle->estadoAdministracion = $info['Kadess'];
			$detalle->periodicidad = $info['Kadper'];
			$detalle->condicionSuministro = $info['Kadcnd'];
			$detalle->dosisMaxima = $info['Kaddma'];
			$detalle->formaFarmaceutica = $info['Kadffa'];
			$detalle->fechaInicioAdministracion = $info['Kadfin'];
			$detalle->horaInicioAdministracion = $info['Kadhin'];
			$detalle->via = $info['Kadvia'];
			$detalle->fechaKardex = $info['Kadfec'];
			$detalle->suspendido = $info['Kadsus'];
			$detalle->estaConfirmado = $info['Kadcon'];
			$detalle->origen = $info['Kadori'];
			$detalle->observaciones = $info['Kadobs'];
			$detalle->cantidadUnidadManejo = $info['Kadcma'];
			$detalle->unidadManejo = $info['Artuni'];
			$detalle->grupo = $info['Artgru'];
			$detalle->cantidadADispensar = $info['Kadcdi'];
			$detalle->cantidadDispensada = $info['Kaddis'];
			$detalle->tipoProtocolo = $info['Kadpro'];
			$detalle->centroCostos	= $info['Kadcco'];
			$detalle->estadoArticulo =  $info['Artest'];
			
			$detalle->imprimirArticulo =  $info['Kadimp'];
			$detalle->altaArticulo =  $info['Kadalt'];
			$detalle->cantidadAlta = $info['Kadcal'];
			$detalle->manejoInterno = $info['Kadint'];
			$detalle->posologia = $info['Kadpos'];
			$detalle->unidadPosologia = $info['Kadupo'];
			
			$detalle->codigoCreador = $info['Kadusu'];
			
			$detalle->nombreGenerico = $nombreArticulo;		//Agosto 27 de 2012
			
			$detalle->esPos = $info['Artpos'] == "N" ? false: true;
			
			$detalle->idOriginal = $info['Kadido'];
			
			switch($detalle->tipoProtocolo){
				case $protocoloNormal:
					$detalle->nombreProtocolo = $nombreProtocoloNormal;
					break;
				case $protocoloNutricion:
					$detalle->nombreProtocolo = $nombreProtocoloNutricion;
					break;
				case $protocoloAnalgesia:
					$detalle->nombreProtocolo = $nombreProtocoloAnalgesia;
					break;
				case $protocoloQuimioterapia:
					$detalle->nombreProtocolo = $nombreProtocoloQuimioterapia;
					break;
				default:
					$detalle->nombreProtocolo = $nombreProtocoloNormal;
					break;
						
			}

			//Kadcma-Kadufr
			if(isset($info['Kadcma']) && !empty($info['Kadcma'])){
				$detalle->maximoUnidadManejo = $info['Kadcma'];
				$detalle->unidadMaximoManejo = $info['Kadufr'];
//				$detalle->vencimiento		 = $info['Defven'];
//				$detalle->diasVencimiento	 = $info['Defdie'];
//				$detalle->esDispensable		 = $info['Defdis'];
//				$detalle->esDuplicable		 = $info['Defdup'];

				//No modificar
				$detalle->permiteModificar	 = true;

				//Consulta de los dias de tratamiento de este articulo
				obtenerDatosAdicionalesArticulo($historia, $ingreso, $info['Kadart'], $detalle->diasTotalesTto, $detalle->dosisTotalesTto);

				//Consulta de cantidades ctc
				//****************************Consulta de las cantidades del CTC autorizado acumulado y usado
				$q2 = "SELECT Ctccau,Ctccus,Ctcuca FROM {$wbasedato}_000095 WHERE Ctchis = '".$historia."' AND Ctcing = '".$ingreso."' AND Ctcart = '".$info['Kadart']."'";
				$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				while($info2 = mysql_fetch_array($res2)){
					$detalle->cantidadAutorizadaCtc 	= $info2['Ctccau'];
					$detalle->cantidadUtilizadaCtc 		= consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$info['Kadart']);
					$detalle->unidadesCantidadesCtc 	= $info2['Ctcuca'];
				}
				//***************************

				//Consulta de vias del articulo
				//****************************
				$q3 = "SELECT Defcco,Defart,Deffra,Deffru,Defest,Defven,Defdie,Defdis,Defdup,Defcon,Defnka,Defdim,Defdom,Defvia FROM {$wbasedato}_000059 WHERE Defart = '".$info['Kadart']."' AND Defest = 'on'";
				$res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
				if($info3 = mysql_fetch_array($res3)){
					$detalle->viasPosibles 	= $info3['Defvia'];
					
					//Consulto si es dispensable y duplicable
					$detalle->esDispensable = $info3['Defdis'];	//Marzo 3 de 2011
					$detalle->esDuplicable =  $info3['Defdup'];	//Marzo 3 de 2011
				}
				//***************************
				
				$coleccion[] = $detalle;
			}
		}
	}
	return $coleccion;
}

function consultarDetalleDefinitivoKardex($historia,$ingreso,$fecha,$tipoProtocolo,$pestAlta='off'){
	global $wbasedato;
	global $conex;
	global $wcenpro;

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;
	
	//Protocolos
	global $protocoloNormal;
	global $protocoloNutricion;
	global $protocoloAnalgesia;
	global $protocoloQuimioterapia;

	global $nombreProtocoloNormal;
	global $nombreProtocoloNutricion;
	global $nombreProtocoloAnalgesia;
	global $nombreProtocoloQuimioterapia;

	global $usuario;		//Información de usuario
	
	global $regletaFamilia;

	$coleccion = array();

	$condicionFecha = " AND Kadfec = '$fecha' ";
	// $condicionFecha = " ";
	$condicionAlta = " AND Kadalt != 'on' ";
	$condicionGroupBy = "";
	if($pestAlta=='on')
	{
		$condicionAlta = "";
		//$condicionFecha = "";
		$condicionGroupBy = " GROUP BY Kadart ";
	}
		
	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	if($usuario->gruposMedicamentos != "*" && $usuario->gruposMedicamentos != '' && $usuario->gruposMedicamentos != 'NO APLICA'){
		$tieneGruposIncluidos = true;
	}
	//********************************
	
	/************************************************************
	 * Junio 14 de 2012
	 ************************************************************/
	$valCcoquery = '';
	if( $usuario->centroCostosGrabacion != '*' ){
		$valCcoquery = " AND Kadcco = '$usuario->centroCostosGrabacion' ";
	}
	/************************************************************/

	// $q = "	SELECT
				// Kadart,Artcom,Artgen,Artuni,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Kadnar, Artest, Kadusu, Famcod, Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
			// FROM
				// ".$wbasedato."_000054, ".$wbasedato."_000026, ".$wbasedato."_000115, ".$wbasedato."_000114
			// WHERE
				// Kadhis = '$historia'
				// AND Kading = '$ingreso'
				// $condicionFecha ";
				// if($tieneGruposIncluidos){
					// $q .= " AND Kadori = '$codigoServicioFarmaceutico' ";
					// $q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
				// }
				// $q .= " AND Kadpro LIKE '$tipoProtocolo'
				// $valCcoquery
				// AND Kadori = '$codigoServicioFarmaceutico' 
				// AND Kadsus != 'on'
				// AND Artcod = Kadart 
				// AND Kadart = Relart
				// AND Relfam = Famcod 
				// $condicionAlta
				// $condicionGroupBy ";

	// $subConsulta = " SELECT
						// Kadart,Artcom,Artgen,Artuni,'' Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Kadnar, Artest, Kadusu, Famcod, Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
					// FROM
						// ".$wbasedato."_000054, cenpro_000002, ".$wbasedato."_000115, ".$wbasedato."_000114
					// WHERE
						// Kadhis = '$historia'
						// AND Kading = '$ingreso'
						// $condicionFecha
						// AND Kadpro LIKE '$tipoProtocolo'
						// AND Kadori = '$codigoCentralMezclas'
						// AND Kadsus != 'on'
						// $valCcoquery
						// AND Artcod = Kadart 
						// AND Kadart = Relart
						// AND Relfam = Famcod 
						// $condicionAlta 
						// $condicionGroupBy ";
						
						
	$q = "	SELECT
				Kadart,Artcom,Artgen,Artuni,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Kadnar, Artest, Kadusu, 'a' as Famcod, 'a' as Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, '' as Relcon, Artuni as Reluni, Artpos, Kadido
			FROM
				".$wbasedato."_000054, ".$wbasedato."_000026
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				$condicionFecha ";
				if($tieneGruposIncluidos){
					$q .= " AND Kadori = '$codigoServicioFarmaceutico' ";
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
				}
				$q .= " AND Kadpro LIKE '$tipoProtocolo'
				$valCcoquery
				AND Kadori = '$codigoServicioFarmaceutico' 
				AND Kadsus != 'on'
				AND Artcod = Kadart 
				$condicionAlta
				$condicionGroupBy ";

	$subConsulta = " SELECT
						Kadart,Artcom,Artgen,Artuni,'' Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Kadnar, Artest, Kadusu, 'a' as Famcod, 'a' as Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, '' as Relcon, Artuni as Reluni, '' as Artpos, Kadido
					FROM
						".$wbasedato."_000054, ".$wcenpro."_000002
					WHERE
						Kadhis = '$historia'
						AND Kading = '$ingreso'
						$condicionFecha
						AND Kadpro LIKE '$tipoProtocolo'
						AND Kadori = '$codigoCentralMezclas'
						AND Kadsus != 'on'
						$valCcoquery
						AND Artcod = Kadart 
						$condicionAlta 
						$condicionGroupBy ";

	if($usuario->esUsuarioCM){
		$q = $subConsulta;
	} else {
		if(!$tieneGruposIncluidos){
			$q = $q." UNION ".$subConsulta;
		}
	}
	$q = $q." ORDER BY Artcom ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		$auxFamilia = "";

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new detalleKardexDTO();
				
			//$rondasFamilia = array();
			
			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			
			$nombreArticulo = "";
			if(isset($info['Kadnar']) && !empty($info['Kadnar'])){
				$detalle->nombreArticulo	= $info['Kadnar'];
				$nombreArticulo = $detalle->nombreArticulo;
			} else {
				$nombreArticulo = $info['Artcom'];
			}
			
			$detalle->codigoFamilia = $info['Famcod'];
			$detalle->nombreFamilia = $info['Famnom'];
			$codFamilia = $info['Famcod'];

			/***********************************************************************/
			/******************* Calculo de regleta por familia ********************/
			/***********************************************************************/

			$colPeriodicidades = consultarPeriodicidades();
			foreach ($colPeriodicidades as $periodicidad){
				if($periodicidad->codigo == $info['Kadper']){
					$horaPeriodicidad = intval($periodicidad->equivalencia);
					break;
				}
			}
			
			$arrAplicacion = array();
			
			$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),$info['Kadfin'],$info['Kadhin'],$horaPeriodicidad,$info['Kadcfr']);
			
			$horaArranque = 2;

			$cont3 = 1;
			$cont2 = $horaArranque;   //Desplazamiento desde la hora inicial
			$caracterMarca = $info['Kadcfr'];

			while($cont3 <= 24){
			
				if(!isset($arrAplicacion[$cont2]) || $arrAplicacion[$cont2]=="" || $arrAplicacion[$cont2]==" " || $arrAplicacion[$cont2]=="-")
					$arrAplicacion[$cont2] = 0;
				
				if(!array_key_exists($codFamilia,$regletaFamilia))
				{
					$regletaFamilia[$codFamilia] = array();
					$regletaFamilia[$codFamilia]['esCompuestaFamilia'] = '0';
				}
				
				if(array_key_exists($cont2,$regletaFamilia[$codFamilia]))
				{
					$regletaFamilia[$codFamilia][$cont2]['valor'] += (integer) $arrAplicacion[$cont2]*1;
				} 
				else 
				{
					$regletaFamilia[$codFamilia][$cont2] = array();
					$regletaFamilia[$codFamilia][$cont2]['valor'] = (integer) $arrAplicacion[$cont2]*1;
				}

				if($arrAplicacion[$cont2] > 0)
				{
					if(isset($regletaFamilia[$codFamilia][$cont2]['condicion']) && $regletaFamilia[$codFamilia][$cont2]['condicion']!="")
					{
						$pos = strpos($regletaFamilia[$codFamilia][$cont2]['condicion'], "|".$info['Kadcnd']);
						if ($pos === false && $regletaFamilia[$codFamilia][$cont2]['condicion'] != $info['Kadcnd'])
							$regletaFamilia[$codFamilia][$cont2]['condicion'] .= "|".$info['Kadcnd'];
					}
					else
					{
						if($info['Kadcnd']!="")
							$regletaFamilia[$codFamilia][$cont2]['condicion'] = "|".$info['Kadcnd'];
						else
							$regletaFamilia[$codFamilia][$cont2]['condicion'] = "";
					}
				
					if(isset($regletaFamilia[$codFamilia][$cont2]['unidad']) && $regletaFamilia[$codFamilia][$cont2]['unidad']!="")
					{
						$pos = strpos($regletaFamilia[$codFamilia][$cont2]['unidad'], "|".$info['Kadufr']);
						if ($pos === false && $regletaFamilia[$codFamilia][$cont2]['unidad'] != $info['Kadufr'])
							$regletaFamilia[$codFamilia][$cont2]['unidad'] .= $info['Kadufr'];
					}
					else
						$regletaFamilia[$codFamilia][$cont2]['unidad'] = $info['Kadufr'];
						

					if(!isset($regletaFamilia[$codFamilia][$cont2]['cont']))
						$regletaFamilia[$codFamilia][$cont2]['cont'] = 1; 
					else
						$regletaFamilia[$codFamilia][$cont2]['cont']++; 
						
					if(isset($regletaFamilia[$codFamilia][$cont2]['cont']) && $regletaFamilia[$codFamilia][$cont2]['cont'] % 2 != 0)
						$bgTooltip = "#C3D9FF";
					else
						$bgTooltip = "#E8EEF7";
					
					if(!isset($regletaFamilia[$codFamilia][$cont2]['tooltip']) || $regletaFamilia[$codFamilia][$cont2]['tooltip']=="")
					{
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] = '<table cellspacing=4>';
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] .= '<tr style=background-color:#2A5DB0;color:#ffffff><td align=center> Código </td><td align=center> Nombre </td><td align=center> Dosis </td><td align=center> Unidad </td><td align=center> Frecuencia </td><td align=center> Vía </td><td align=center> Inicio </td><td align=center> Condición </td></tr>';
					}
					
					if(isset($regletaFamilia[$codFamilia][$cont2]['tooltip']))
					{
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] .= '<tr style=background-color:'.$bgTooltip.'><td> '.$info['Kadart'].' </td><td> '.trim($info['Artgen']).' </td><td> '.$info['Kadcfr'].' '.$info['Kadufr'].' </td><td align=center> '.$info['Artuni'].' </td><td align=center> '.$info['Kadper'].' </td><td align=center> '.$info['Kadvia'].' </td><td align=center> '.$info['Kadfin'].' </td><td align=center> '.$info['Kadcnd'].' </td></tr>';
					}
				}
				
				
				if($cont2 == 24){
					$cont2 = 0;
				}

				$cont3++;
				$cont2++;

				if($cont2 % 2 != 0){
					$cont2++;
				}
				if($cont3 % 2 != 0){
					$cont3++;
				}

				if($cont2 == $horaArranque){
					break;
				}
			}

			// Indico si hay mas de un articulo en la familia
			if($auxFamilia == $codFamilia)
				$regletaFamilia[$codFamilia]['esCompuestaFamilia'] = '1';

			$auxFamilia = $codFamilia;
			
			
			//$regletaFamilia = $rondasFamilia;
			
			unset($arrAplicacion);
			//unset($rondasFamilia);
			unset($colPeriodicidades);
			
			/********************************************************************/
			
			//$detalle->codigoArticulo = $info['Famnom']." ".$info['Relcon']." ".$info['Reluni'];
			//$detalle->codigoArticulo = $info['Kadart']."-".$nombreArticulo;
			$detalle->codigoArticulo = $info['Kadart']."-".$info['Artgen'];
			$detalle->cantidadDosis = $info['Kadcfr'];
			$detalle->unidadDosis = $info['Kadufr'];
			$detalle->diasTratamiento = $info['Kaddia'];
			$detalle->estadoRegistro = $info['Kadest'];
			$detalle->estadoAdministracion = $info['Kadess'];
			$detalle->periodicidad = $info['Kadper'];
			$detalle->condicionSuministro = $info['Kadcnd'];
			$detalle->formaFarmaceutica = $info['Kadffa'];
			$detalle->dosisMaxima = $info['Kaddma'];
			$detalle->fechaInicioAdministracion = $info['Kadfin'];
			$detalle->horaInicioAdministracion = $info['Kadhin'];
			$detalle->via = $info['Kadvia'];
			$detalle->fechaKardex = $info['Kadfec'];
			$detalle->suspendido = $info['Kadsus'];
			$detalle->estaConfirmado = $info['Kadcon'];
			$detalle->origen = $info['Kadori'];
			$detalle->observaciones = $info['Kadobs'];
			$detalle->cantidadUnidadManejo = $info['Kadcma'];
			$detalle->unidadManejo = $info['Artuni'];
			$detalle->grupo = $info['Artgru'];
			$detalle->tipoProtocolo = $info['Kadpro'];
			$detalle->nombreArticulo	= $info['Kadnar'];
			$detalle->estadoArticulo = $info['Artest'];
			
			$detalle->imprimirArticulo = $info['Kadimp'];
			$detalle->altaArticulo = $info['Kadalt'];
			$detalle->cantidadAlta = $info['Kadcal'];
			$detalle->manejoInterno = $info['Kadint'];
			$detalle->posologia = $info['Kadpos'];
			$detalle->unidadPosologia = $info['Kadupo'];
			
			$detalle->codigoCreador = $info['Kadusu'];
			
			$detalle->nombreGenerico = $info['Artgen'];		//Agosto 27 de 2012
			
			$detalle->esPos = $info['Artpos'] == "N" ? false: true;
			
			$detalle->idOriginal = $info['Kadido'];

			switch($detalle->tipoProtocolo){
				case $protocoloNormal:
					$detalle->nombreProtocolo = $nombreProtocoloNormal;
					break;
				case $protocoloNutricion:
					$detalle->nombreProtocolo = $nombreProtocoloNutricion;
					break;
				case $protocoloAnalgesia:
					$detalle->nombreProtocolo = $nombreProtocoloAnalgesia;
					break;
				case $protocoloQuimioterapia:
					$detalle->nombreProtocolo = $nombreProtocoloQuimioterapia;
					break;
				default:
					$detalle->nombreProtocolo = $nombreProtocoloNormal;
					break;
						
			}
				
			//Kadcma-Kadufr
			if(isset($info['Kadcma']) && !empty($info['Kadcma'])){
				$detalle->maximoUnidadManejo = $info['Kadcma'];
				$detalle->unidadMaximoManejo = $info['Kadufr'];
//				$detalle->vencimiento		 = $info['Defven'];
//				$detalle->esDispensable		 = $info['Defdis'];
//				$detalle->esDuplicable		 = $info['Defdup'];

				$detalle->permiteModificar 	 = true;

				//Consulta de los dias de tratamiento de este articulo
				obtenerDatosAdicionalesArticulo($historia, $ingreso, $info['Kadart'], $detalle->diasTotalesTto, $detalle->dosisTotalesTto);

				//Consulta de cantidades ctc
				//****************************Consulta de las cantidades del CTC autorizado acumulado y usado
				$q2 = "SELECT Ctccau,Ctccus,Ctcuca FROM {$wbasedato}_000095 WHERE Ctchis = '".$historia."' AND Ctcing = '".$ingreso."' AND Ctcart = '".$info['Kadart']."'";
				$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				while($info2 = mysql_fetch_array($res2)){
					$detalle->cantidadAutorizadaCtc 	= $info2['Ctccau'];
					$detalle->cantidadUtilizadaCtc 		= consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$info['Kadart']);
					$detalle->unidadesCantidadesCtc 	= $info2['Ctcuca'];
				}
				//***************************
				
				//Consulta de vias del articulo
				//*****************************
				$q3 = "SELECT Defcco,Defart,Deffra,Deffru,Defest,Defven,Defdie,Defdis,Defdup,Defcon,Defnka,Defdim,Defdom,Defvia FROM {$wbasedato}_000059 WHERE Defart = '".$info['Kadart']."' AND Defest = 'on'";
				$res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
				if($info3 = mysql_fetch_array($res3)){
					$detalle->viasPosibles 	= $info3['Defvia'];
					
					//Consulto si es dispensable y duplicable
					$detalle->esDispensable = $info3['Defdis'];	//Marzo 3 de 2011
					$detalle->esDuplicable =  $info3['Defdup'];	//Marzo 3 de 2011
				}
				//***************************
				$coleccion[] = $detalle;
			}
		}
	}
	return $coleccion;
}



/**
 * Consulta el detalle del kardex del dia anterior para mostrarlo en pantalla (en caso de no tener uno el dia seleccionado)
 * @param unknown_type $historia
 * @param unknown_type $ingreso
 * @param unknown_type $wfecha
 */
function consultarDetalleKardexAlta($historia,$ingreso,$fecha,$tipoProtocolo,$pestAlta='off'){
	//,$centroCostosMovimiento,$gruposMedicamentosMovimiento
	global $wbasedato;
	global $wcenpro;
	global $conex;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosServicioFarmaceutico;
	global $centroCostosCentralMezclas;
	global $usuario;		//Información de usuario

	//Protocolos
	global $protocoloNormal;
	global $protocoloNutricion;
	global $protocoloAnalgesia;
	global $protocoloQuimioterapia;

	global $nombreProtocoloNormal;
	global $nombreProtocoloNutricion;
	global $nombreProtocoloAnalgesia;
	global $nombreProtocoloQuimioterapia;
	
	global $regletaFamilia;
	
	$coleccion = array();

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	if($usuario->gruposMedicamentos != "*" && $usuario->gruposMedicamentos != '' && $usuario->gruposMedicamentos != 'NO APLICA'){
		$tieneGruposIncluidos = true;
	}
	//********************************
	
	$condicionAlta = "";
	$condicionFecha = "";
	$condicionGroupBy = " GROUP BY Kadart ";
	/****************************************************************************************
	 * Junio 14 de 2012
	 ****************************************************************************************/
	$validarCco = "";
	
	if( $usuario->centroCostosGrabacion != '*' ){
		$validarCco = " AND Kadcco = '$usuario->centroCostosGrabacion'";
	}
	/****************************************************************************************/

		$q = "	SELECT
					Kadart,Artcom,Artgen,Artuni,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcdi,Kaddis,Kadpro,Kadcco,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kaddis,Kaduma,Kadcma,Kadnar, Artest, Kadusu, Famcod, Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
				FROM
					".$wbasedato."_000054, ".$wbasedato."_000026, ".$wbasedato."_000115, ".$wbasedato."_000114
				WHERE
					Kadhis = '$historia'
					AND Kading = '$ingreso'
					$condicionFecha ";
					if($tieneGruposIncluidos){
						$q .= " AND Kadori = '$codigoServicioFarmaceutico' ";
						$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
					}
					$q .= " AND Kadpro LIKE '$tipoProtocolo'
					$valCcoquery
					AND Kadori = '$codigoServicioFarmaceutico' 
					AND Kadsus != 'on'
					AND Artcod = Kadart 
					AND Kadart = Relart
					AND Relfam = Famcod 
					$condicionAlta
					$condicionGroupBy ";
		
		$q .= " UNION ";
		
		$q .= "SELECT
				Kadart,Artcom,Artgen,Artuni,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcdi,Kaddis,Kadpro,Kadcco,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kaddis,Kaduma,Kadcma,Kadnar, Artest, Kadusu, Famcod, Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
			FROM
				".$wbasedato."_000060, ".$wbasedato."_000026, ".$wbasedato."_000115, ".$wbasedato."_000114
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				$condicionFecha ";
				if($tieneGruposIncluidos){
					$q .= " AND Kadori = '$codigoServicioFarmaceutico' ";
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
				}
				$q .= " AND Kadpro LIKE '$tipoProtocolo'
				AND Artcod = Kadart
				AND kadori = '$codigoServicioFarmaceutico'
				AND Kadsus != 'on'
				AND Kadart = Relart
				AND Relfam = Famcod
				$condicionAlta
				$validarCco
				$condicionGroupBy ";

	$subConsulta = " SELECT
						Kadart,Artcom,Artgen,Artuni,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcdi,Kaddis,Kadpro,Kadcco,'' Artgru,Kaddis,Kaduma,Kadcma,Kadnar, Artest, Kadusu, Famcod, Famnom, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
					FROM
						".$wbasedato."_000060, ".$wcenpro."_000002, ".$wbasedato."_000115, ".$wbasedato."_000114
					WHERE
						Kadhis = '$historia'
						AND Kading = '$ingreso'
						$condicionFecha
						AND Kadpro LIKE '$tipoProtocolo'
						AND Kadori = '$codigoCentralMezclas'
						AND Artcod = Kadart
						AND Kadsus != 'on'
						AND Kadart = Relart
						AND Relfam = Famcod
						$condicionAlta
						$validarCco
						$condicionGroupBy ";

	if($usuario->esUsuarioCM){
		$q = $subConsulta;
	} else {
		if(!$tieneGruposIncluidos){
			$q = $q." UNION ".$subConsulta;
		}
	}
	$q = $q." ORDER BY Famcod, Artcom ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;
		$auxFamilia = "";

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new detalleKardexDTO();

			//$rondasFamilia = array();
			
			$vectorDosis = array();
			
			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			
			$nombreArticulo = "";
			if(isset($info['Kadnar']) && !empty($info['Kadnar'])){
				$detalle->nombreArticulo	= $info['Kadnar'];
				$nombreArticulo = $detalle->nombreArticulo;
			} else {
				$nombreArticulo = $info['Artcom'];
			}
			
			$detalle->codigoFamilia = $info['Famcod'];
			$detalle->nombreFamilia = $info['Famnom'];
			$codFamilia = $info['Famcod'];

			/***********************************************************************/
			/******************* Calculo de regleta por familia ********************/
			/***********************************************************************/

			$colPeriodicidades = consultarPeriodicidades();
			foreach ($colPeriodicidades as $periodicidad){
				if($periodicidad->codigo == $info['Kadper']){
					$horaPeriodicidad = intval($periodicidad->equivalencia);
					break;
				}
			}
			
			$arrAplicacion = array();
			
			$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),$info['Kadfin'],$info['Kadhin'],$horaPeriodicidad,$info['Kadcfr']);
			
			$horaArranque = 2;

			$cont3 = 1;
			$cont2 = $horaArranque;   //Desplazamiento desde la hora inicial
			$caracterMarca = $info['Kadcfr'];

			while($cont3 <= 24)
			{
			
				if(!isset($arrAplicacion[$cont2]) || $arrAplicacion[$cont2]=="" || $arrAplicacion[$cont2]==" " || $arrAplicacion[$cont2]=="-")
					$arrAplicacion[$cont2] = 0;
				
				if(!array_key_exists($codFamilia,$regletaFamilia))
					$regletaFamilia[$codFamilia] = array();
				
				if(array_key_exists($cont2,$regletaFamilia[$codFamilia]))
				{
					$regletaFamilia[$codFamilia][$cont2]['valor'] += $arrAplicacion[$cont2]*1;
				} 
				else 
				{
					$regletaFamilia[$codFamilia][$cont2] = array();
					$regletaFamilia[$codFamilia][$cont2]['valor'] = $arrAplicacion[$cont2]*1;
				}

				if($arrAplicacion[$cont2] > 0)
				{
					if(isset($regletaFamilia[$codFamilia][$cont2]['condicion']) && $regletaFamilia[$codFamilia][$cont2]['condicion']!="")
					{
						$pos = strpos($regletaFamilia[$codFamilia][$cont2]['condicion'], "|".$info['Kadcnd']);
						if ($pos === false && $regletaFamilia[$codFamilia][$cont2]['condicion'] != $info['Kadcnd'])
							$regletaFamilia[$codFamilia][$cont2]['condicion'] .= "|".$info['Kadcnd'];
					}
					else
					{
						$regletaFamilia[$codFamilia][$cont2]['condicion'] = $info['Kadcnd'];
					}

					if(isset($regletaFamilia[$codFamilia][$cont2]['unidad']) && $regletaFamilia[$codFamilia][$cont2]['unidad']!="")
					{
						$pos = strpos($regletaFamilia[$codFamilia][$cont2]['unidad'], "|".$info['Kadufr']);
						if ($pos === false && $regletaFamilia[$codFamilia][$cont2]['unidad'] != $info['Kadufr'])
							$regletaFamilia[$codFamilia][$cont2]['unidad'] .= "|".$info['Kadufr'];
					}
					else
					{
						$regletaFamilia[$codFamilia][$cont2]['unidad'] = $info['Kadufr'];
					}
					
					if(!isset($regletaFamilia[$codFamilia][$cont2]['cont']))
						$regletaFamilia[$codFamilia][$cont2]['cont'] = 1; 
					else
						$regletaFamilia[$codFamilia][$cont2]['cont']++; 
						
					if(isset($regletaFamilia[$codFamilia][$cont2]['cont']) && $regletaFamilia[$codFamilia][$cont2]['cont'] % 2 != 0)
						$bgTooltip = "#C3D9FF";
					else
						$bgTooltip = "#E8EEF7";
					
					if(!isset($regletaFamilia[$codFamilia][$cont2]['tooltip']) || $regletaFamilia[$codFamilia][$cont2]['tooltip']=="")
					{
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] = '<table cellspacing=4>';
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] .= '<tr style=background-color:#2A5DB0;color:#ffffff><td align=center> Código </td><td align=center> Nombre </td><td align=center> Dosis </td><td align=center> Unidad </td><td align=center> Frecuencia </td><td align=center> Vía </td><td align=center> Inicio </td><td align=center> Condición </td></tr>';
					}
					
					if(isset($regletaFamilia[$codFamilia][$cont2]['tooltip']))
					{
						$regletaFamilia[$codFamilia][$cont2]['tooltip'] .= '<tr style=background-color:'.$bgTooltip.'><td> '.$info['Kadart'].' </td><td> '.trim($info['Artgen']).' </td><td> '.$info['Kadcfr'].' '.$info['Kadufr'].' </td><td align=center> '.$info['Artuni'].' </td><td align=center> '.$info['Kadper'].' </td><td align=center> '.$info['Kadvia'].' </td><td align=center> '.$info['Kadfin'].' </td><td align=center> '.$info['Kadcnd'].' </td></tr>';
					}
				}
				
				if($cont2 == 24){
					$cont2 = 0;
				}

				$cont3++;
				$cont2++;

				if($cont2 % 2 != 0){
					$cont2++;
				}
				if($cont3 % 2 != 0){
					$cont3++;
				}

				if($cont2 == $horaArranque){
					break;
				}
			}
			
			// Indico si hay mas de un articulo en la familia
			if($auxFamilia == $codFamilia)
				$regletaFamilia[$codFamilia]['esCompuestaFamilia'] = '1';

			$auxFamilia = $codFamilia;
			
			//$regletaFamilia = $rondasFamilia;
			
			unset($arrAplicacion);
			//unset($rondasFamilia);
			unset($colPeriodicidades);
			
			/********************************************************************/
			
			//$detalle->codigoArticulo = $info['Kadart']."-".$info['Kadori']."-".$info['Artgen'];
			$detalle->codigoArticulo = $info['Famnom']." ".$info['Relcon']." ".$info['Reluni'];
			$detalle->cantidadDosis = $info['Kadcfr'];
			$detalle->unidadDosis = $info['Kadufr'];
			$detalle->diasTratamiento = $info['Kaddia'];
			$detalle->estadoRegistro = $info['Kadest'];
			$detalle->estadoAdministracion = $info['Kadess'];
			$detalle->periodicidad = $info['Kadper'];
			$detalle->condicionSuministro = $info['Kadcnd'];
			$detalle->dosisMaxima = $info['Kaddma'];
			$detalle->formaFarmaceutica = $info['Kadffa'];
			$detalle->fechaInicioAdministracion = $info['Kadfin'];
			$detalle->horaInicioAdministracion = $info['Kadhin'];
			$detalle->via = $info['Kadvia'];
			$detalle->fechaKardex = $info['Kadfec'];
			$detalle->suspendido = $info['Kadsus'];
			$detalle->estaConfirmado = $info['Kadcon'];
			$detalle->origen = $info['Kadori'];
			$detalle->observaciones = $info['Kadobs'];
			$detalle->cantidadUnidadManejo = $info['Kadcma'];
			$detalle->unidadManejo = $info['Artuni'];
			$detalle->grupo = $info['Artgru'];
			$detalle->cantidadADispensar = $info['Kadcdi'];
			$detalle->cantidadDispensada = $info['Kaddis'];
			$detalle->tipoProtocolo = $info['Kadpro'];
			$detalle->centroCostos	= $info['Kadcco'];
			$detalle->estadoArticulo =  $info['Artest'];
			
			$detalle->imprimirArticulo =  $info['Kadimp'];
			$detalle->altaArticulo =  $info['Kadalt'];
			$detalle->cantidadAlta = $info['Kadcal'];
			$detalle->manejoInterno = $info['Kadint'];
			$detalle->posologia = $info['Kadpos'];
			$detalle->unidadPosologia = $info['Kadupo'];
			
			
			$detalle->codigoCreador = $info['Kadusu'];
			
			$detalle->nombreGenerico = $nombreArticulo;		//Agosto 27 de 2012
			
			$detalle->idOriginal = $info['Kadido'];
			
			switch($detalle->tipoProtocolo){
				case $protocoloNormal:
					$detalle->nombreProtocolo = $nombreProtocoloNormal;
					break;
				case $protocoloNutricion:
					$detalle->nombreProtocolo = $nombreProtocoloNutricion;
					break;
				case $protocoloAnalgesia:
					$detalle->nombreProtocolo = $nombreProtocoloAnalgesia;
					break;
				case $protocoloQuimioterapia:
					$detalle->nombreProtocolo = $nombreProtocoloQuimioterapia;
					break;
				default:
					$detalle->nombreProtocolo = $nombreProtocoloNormal;
					break;
						
			}

			//Kadcma-Kadufr
			if(isset($info['Kadcma']) && !empty($info['Kadcma'])){
				$detalle->maximoUnidadManejo = $info['Kadcma'];
				$detalle->unidadMaximoManejo = $info['Kadufr'];
//				$detalle->vencimiento		 = $info['Defven'];
//				$detalle->diasVencimiento	 = $info['Defdie'];
//				$detalle->esDispensable		 = $info['Defdis'];
//				$detalle->esDuplicable		 = $info['Defdup'];

				//No modificar
				$detalle->permiteModificar	 = true;

				//Consulta de los dias de tratamiento de este articulo
				obtenerDatosAdicionalesArticulo($historia, $ingreso, $info['Kadart'], $detalle->diasTotalesTto, $detalle->dosisTotalesTto);

				//Consulta de cantidades ctc
				//****************************Consulta de las cantidades del CTC autorizado acumulado y usado
				$q2 = "SELECT Ctccau,Ctccus,Ctcuca FROM {$wbasedato}_000095 WHERE Ctchis = '".$historia."' AND Ctcing = '".$ingreso."' AND Ctcart = '".$info['Kadart']."'";
				$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				while($info2 = mysql_fetch_array($res2)){
					$detalle->cantidadAutorizadaCtc 	= $info2['Ctccau'];
					$detalle->cantidadUtilizadaCtc 		= consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$info['Kadart']);
					$detalle->unidadesCantidadesCtc 	= $info2['Ctcuca'];
				}
				//***************************

				//Consulta de vias del articulo
				//****************************
				$q3 = "SELECT Defcco,Defart,Deffra,Deffru,Defest,Defven,Defdie,Defdis,Defdup,Defcon,Defnka,Defdim,Defdom,Defvia FROM {$wbasedato}_000059 WHERE Defart = '".$info['Kadart']."' AND Defest = 'on'";
				$res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
				if($info3 = mysql_fetch_array($res3)){
					$detalle->viasPosibles 	= $info3['Defvia'];
					
					//Consulto si es dispensable y duplicable
					$detalle->esDispensable = $info3['Defdis'];	//Marzo 3 de 2011
					$detalle->esDuplicable =  $info3['Defdup'];	//Marzo 3 de 2011
				}
				//***************************
				
				$coleccion[] = $detalle;
			}
		}
	}
	return $coleccion;
}




/**
 * La idea de este metodo es agregar los articulos de central de mezclas en modo editable
 *
 * @param $historia
 * @param $ingreso
 * @param $fechaConsulta
 * @param $tipoProtocolo
 * @return unknown_type
 */
function consultarArticulosCMParaSF($historia,$ingreso,$fecha,$tipoProtocolo){
	global $wbasedato;
	global $conex;
	global $wcenpro;

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	global $usuario;		//Información de usuario

	$coleccion = array();

	$q = " SELECT
				Kadart,Artcom,Artgen,Artuni,'' Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Kadare,Deffra,Deffru,Defven,Defdis,Defdup, Artest, Kadusu, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Artpos, Kadido
			FROM 
				".$wbasedato."_000054, ".$wcenpro."_000002 LEFT JOIN ( SELECT Deffra, Deffru, Defart, Defven,Defdis,Defdup  FROM {$wbasedato}_000059 WHERE Defest = 'on' AND Defcco = '$centroCostosCentralMezclas') a ON a.Defart = Artcod  
			WHERE 
				Kadhis = '$historia' 
				AND Kading = '$ingreso'				 
				AND Kadfec = '$fecha'
				AND Kadpro = '$tipoProtocolo'
				AND Kadori = '$codigoCentralMezclas'
				AND Kadcco = '$centroCostosCentralMezclas'
				AND Artcod = Kadart 
			UNION
			SELECT 
				Kadart,Artcom,Artgen,Artuni,'' Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Kadare,Deffra,Deffru,Defven,Defdis,Defdup, Artest, Kadusu, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, '' as Artpos, Kadido
			FROM 
				".$wbasedato."_000060, ".$wcenpro."_000002 LEFT JOIN ( SELECT Deffra, Deffru, Defart, Defven,Defdis,Defdup  FROM {$wbasedato}_000059 WHERE Defest = 'on' AND Defcco = '$centroCostosCentralMezclas') a ON a.Defart = Artcod  
			WHERE 
				Kadhis = '$historia' 
				AND Kading = '$ingreso'				 
				AND Kadfec = '$fecha'
				AND Kadpro = '$tipoProtocolo'
				AND Kadori = '$codigoCentralMezclas'
				AND Kadcco = '$centroCostosCentralMezclas'
				AND Artcod = Kadart 
			ORDER BY Artcom ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new detalleKardexDTO();
				
			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			$detalle->codigoArticulo = $info['Kadart']."-".$info['Kadori']."-".$info['Artcom'];
			$detalle->cantidadDosis = $info['Kadcfr'];
			$detalle->unidadDosis = $info['Kadufr'];
			$detalle->diasTratamiento = $info['Kaddia'];
			$detalle->estadoRegistro = $info['Kadest'];
			$detalle->estadoAdministracion = $info['Kadess'];
			$detalle->periodicidad = $info['Kadper'];
			$detalle->condicionSuministro = $info['Kadcnd'];
			$detalle->formaFarmaceutica = $info['Kadffa'];
			$detalle->dosisMaxima = $info['Kaddma'];
			$detalle->fechaInicioAdministracion = $info['Kadfin'];
			$detalle->horaInicioAdministracion = $info['Kadhin'];
			$detalle->via = $info['Kadvia'];
			$detalle->fechaKardex = $info['Kadfec'];
			$detalle->suspendido = $info['Kadsus'];
			$detalle->estaConfirmado = $info['Kadcon'];
			$detalle->origen = $info['Kadori'];
			$detalle->observaciones = $info['Kadobs'];
			$detalle->cantidadUnidadManejo = $info['Kadcma'];
			$detalle->unidadManejo = $info['Artuni'];
			$detalle->grupo = $info['Artgru'];
			$detalle->tipoProtocolo = $info['Kadpro'];
			$detalle->estadoArticulo = $info['Artest'];

			$detalle->imprimirArticulo =  $info['Kadimp'];
			$detalle->altaArticulo =  $info['Kadalt'];
			$detalle->cantidadAlta = $info['Kadcal'];
			$detalle->manejoInterno = $info['Kadint'];
			$detalle->posologia = $info['Kadpos'];
			$detalle->unidadPosologia = $info['Kadupo'];
			
		
			$detalle->codigoCreador = $info['Kadusu'];
			
			$detalle->nombreGenerico = $info['Artgen'];		//Agosto 27 de 2012
			
			$detalle->esPos = $info['Artpos'] == "N" ? false: true;
			
			$detalle->idOriginal = $info['Kadido'];
				
			if(isset($info['Deffra']) && !empty($info['Deffra'])){
				$detalle->maximoUnidadManejo = $info['Deffra'];
				$detalle->unidadMaximoManejo = $info['Deffru'];
				$detalle->vencimiento		 = $info['Defven'];
				$detalle->esDispensable		 = $info['Defdis'];
				$detalle->esDuplicable		 = $info['Defdup'];
				
				$detalle->permiteModificar 	 = false;
				
				//Consulta de los dias de tratamiento de este articulo
				obtenerDatosAdicionalesArticulo($historia, $ingreso, $info['Kadart'], $detalle->diasTotalesTto, $detalle->dosisTotalesTto);
					
				//Consulta de cantidades ctc
				//****************************Consulta de las cantidades del CTC autorizado acumulado y usado
				$q2 = "SELECT Ctccau,Ctccus,Ctcuca FROM {$wbasedato}_000095 WHERE Ctchis = '".$historia."' AND Ctcing = '".$ingreso."' AND Ctcart = '".$info['Kadart']."'";
				$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				while($info2 = mysql_fetch_array($res2)){
					$detalle->cantidadAutorizadaCtc 	= $info2['Ctccau'];
					$detalle->cantidadUtilizadaCtc 		= consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$info['Kadart']);
					$detalle->unidadesCantidadesCtc 	= $info2['Ctcuca'];
				}
				//***************************
				$coleccion[] = $detalle;
			}
		}
	}
	return $coleccion;
}

/**
 * Muestra a modo de consulta para enfermeria los articulos del lactario
 *
 * @param $historia
 * @param $ingreso
 * @param $fechaConsulta
 * @param $tipoProtocolo
 * @return unknown_type
 */
function consultarArticulosLactario($historia,$ingreso,$fecha,$tipoProtocolo){
	global $wbasedato;
	global $conex;

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	//Protocolos
	global $protocoloNormal;
	global $protocoloNutricion;
	global $protocoloAnalgesia;
	global $protocoloQuimioterapia;

	global $nombreProtocoloNormal;
	global $nombreProtocoloNutricion;
	global $nombreProtocoloAnalgesia;
	global $nombreProtocoloQuimioterapia;

	global $usuario;		//Información de usuario

	$coleccion = array();

	$q = " SELECT
				Kadart,Artcom,Artgen,Artuni,'' Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadpro,Kadcco,Kadare,Deffra,Deffru,Defven,Defdis,Defdup,Kadnar, Artest, Kadusu, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Artpos, Kadido
			FROM
				".$wbasedato."_000054, ".$wbasedato."_000026 LEFT JOIN ( SELECT Deffra, Deffru, Defart, Defven,Defdis,Defdup  FROM {$wbasedato}_000059 WHERE Defest = 'on' AND Defcco = '$centroCostosServicioFarmaceutico') a ON a.Defart = Artcod
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadfec = '$fecha'
				AND Kadpro LIKE '$tipoProtocolo'
				AND Kadori = 'SF'
				AND Kadcco = '1120'
				AND Artcod = Kadart			
			ORDER BY Artcom ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$detalle = new detalleKardexDTO();

			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			$detalle->codigoArticulo = $info['Kadart']."-".$info['Kadori']."-".$info['Artcom'];
			$detalle->cantidadDosis = $info['Kadcfr'];
			$detalle->unidadDosis = $info['Kadufr'];
			$detalle->diasTratamiento = $info['Kaddia'];
			$detalle->estadoRegistro = $info['Kadest'];
			$detalle->estadoAdministracion = $info['Kadess'];
			$detalle->periodicidad = $info['Kadper'];
			$detalle->condicionSuministro = $info['Kadcnd'];
			$detalle->formaFarmaceutica = $info['Kadffa'];
			$detalle->dosisMaxima = $info['Kaddma'];
			$detalle->fechaInicioAdministracion = $info['Kadfin'];
			$detalle->horaInicioAdministracion = $info['Kadhin'];
			$detalle->via = $info['Kadvia'];
			$detalle->fechaKardex = $info['Kadfec'];
			$detalle->suspendido = $info['Kadsus'];
			$detalle->estaConfirmado = $info['Kadcon'];
			$detalle->origen = $info['Kadori'];
			$detalle->observaciones = $info['Kadobs'];
			$detalle->cantidadUnidadManejo = $info['Kadcma'];
			$detalle->unidadManejo = $info['Artuni'];
			$detalle->grupo = $info['Artgru'];
			$detalle->tipoProtocolo = $info['Kadpro'];
			$detalle->nombreArticulo = $info['Kadnar'];
			$detalle->estadoArticulo = $info['Artest'];
			
			$detalle->imprimirArticulo =  $info['Kadimp'];
			$detalle->altaArticulo =  $info['Kadalt'];
			$detalle->cantidadAlta = $info['Kadcal'];
			$detalle->manejoInterno = $info['Kadint'];
			$detalle->posologia = $info['Kadpos'];
			$detalle->unidadPosologia = $info['Kadupo'];
			
			$detalle->codigoCreador = $info['Kadusu'];
			
			$detalle->nombreGenerico = $info['Artgen'];		//Agosto 27 de 2012
			
			$detalle->esPos = $info['Artpos'] == "N" ? false: true;
			
			$detalle->idOriginal = $info['Kadido'];

			switch($detalle->tipoProtocolo){
				case $protocoloNormal:
					$detalle->nombreProtocolo = $nombreProtocoloNormal;
					break;
				case $protocoloNutricion:
					$detalle->nombreProtocolo = $nombreProtocoloNutricion;
					break;
				case $protocoloAnalgesia:
					$detalle->nombreProtocolo = $nombreProtocoloAnalgesia;
					break;
				case $protocoloQuimioterapia:
					$detalle->nombreProtocolo = $nombreProtocoloQuimioterapia;
					break;
				default:
					$detalle->nombreProtocolo = $nombreProtocoloNormal;
					break;
						
			}
			
			if(isset($info['Deffra']) && !empty($info['Deffra'])){
				$detalle->maximoUnidadManejo = $info['Deffra'];
				$detalle->unidadMaximoManejo = $info['Deffru'];
				$detalle->vencimiento		 = $info['Defven'];
				$detalle->esDispensable		 = $info['Defdis'];
				$detalle->esDuplicable		 = $info['Defdup'];

				$detalle->permiteModificar 	 = false;

				//Consulta de los dias de tratamiento de este articulo
				obtenerDatosAdicionalesArticulo($historia, $ingreso, $info['Kadart'], $detalle->diasTotalesTto, $detalle->dosisTotalesTto);

				//Consulta de cantidades ctc
				//****************************Consulta de las cantidades del CTC autorizado acumulado y usado
				$q2 = "SELECT Ctccau,Ctccus,Ctcuca FROM {$wbasedato}_000095 WHERE Ctchis = '".$historia."' AND Ctcing = '".$ingreso."' AND Ctcart = '".$info['Kadart']."'";
				$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				while($info2 = mysql_fetch_array($res2)){
					$detalle->cantidadAutorizadaCtc 	= $info2['Ctccau'];
					$detalle->cantidadUtilizadaCtc 		= consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$info['Kadart']);
					$detalle->unidadesCantidadesCtc 	= $info2['Ctcuca'];
				}
				//***************************
				$coleccion[] = $detalle;
			}
		}
	}
	return $coleccion;
}
/**
 * Consulta el detalle del perfil farmacoterapeutico basado en el detalle historico del kardex por fecha y paciente
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @return unknown_type
 *
 * NOTAS:
 *
 * 20-Sep-10:  La consulta de los articulos se hará por la fracción que tenga en el registro no en la 59, el kardex tendra la responsabilidad de asignar la fracción adecuada.
 */
function consultarDetalleDefinitivoPerfil($historia,$ingreso,$fecha){
	global $wbasedato;
	global $wcenpro;
	global $conex;
	global $usuario;		//Información de usuario

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	$coleccion = array();

	$q = "SELECT
			{$wbasedato}_000054.Fecha_data,{$wbasedato}_000054.Hora_data,Kadart,Artcom,Artgen,Artgru,Artpos,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadcdi,Kadpri,Kadare,Kadsad,Kadusu, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Kadido
		FROM
			{$wbasedato}_000054, {$wbasedato}_000026
		WHERE
			Kadhis = '$historia'
			AND Kading = '$ingreso'
			AND Kadfec = '$fecha'
			AND Kadsus = 'off'
			AND Kadori = '$codigoServicioFarmaceutico'
			AND Artcod = Kadart
			AND Kadcdi > 0 ";

	$subConsulta = " SELECT
			{$wbasedato}_000054.Fecha_data,{$wbasedato}_000054.Hora_data,Kadart,Artcom,Artgen,'' Artgru,'' Artpos,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadcdi,Kadpri,Kadare,Kadsad,Kadusu, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Kadido
		FROM
			{$wcenpro}_000002, {$wbasedato}_000054
		WHERE
			Kadhis = '$historia'
			AND Kading = '$ingreso'
			AND Kadfec = '$fecha'
			AND Kadsus = 'off'
			AND Kadori = '$codigoCentralMezclas'
			AND Artcod = Kadart
			AND Kadcdi > 0 ";
			
//	if($usuario->esUsuarioCM){
//		$q = $subConsulta;
//	}

//	if($usuario->esUsuarioCTC){
		$q .= " UNION ".$subConsulta;
//	}

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$detalle = new detalleKardexDTO();
			
			$info = mysql_fetch_array($res);
			
			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			$detalle->codigoArticulo = $info['Kadart']."-".$info['Kadori']."-".$info['Artcom'];
			$detalle->cantidadDosis = $info['Kadcfr'];
			$detalle->unidadDosis = $info['Kadufr'];
			$detalle->diasTratamiento = $info['Kaddia'];
			$detalle->estadoRegistro = $info['Kadest'];
			$detalle->estadoAdministracion = $info['Kadess'];
			$detalle->periodicidad = $info['Kadper'];
			$detalle->condicionSuministro = $info['Kadcnd'];
			$detalle->formaFarmaceutica = $info['Kadffa'];
			$detalle->dosisMaxima = $info['Kaddma'];
			$detalle->fechaInicioAdministracion = $info['Kadfin'];
			$detalle->horaInicioAdministracion = $info['Kadhin'];
			$detalle->via = $info['Kadvia'];
			$detalle->fechaKardex = $info['Kadfec'];
			$detalle->suspendido = $info['Kadsus'];
			$detalle->estaConfirmado = $info['Kadcon'];
			$detalle->origen = $info['Kadori'];
			$detalle->observaciones = $info['Kadobs'];
			$detalle->cantidadGrabar = $info['Kadcdi'];
			$detalle->cantidadUnidadManejo = $info['Kadcma'];
			$detalle->unidadManejo = $info['Kaduma'];
			$detalle->grupo = $info['Artgru'];
			$detalle->tienePrioridad = $info['Kadpri'];
			$detalle->aprobado = $info['Kadare'];
			$detalle->fechaCreacion = $info['Fecha_data'];
			$detalle->horaCreacion = $info['Hora_data'];
			$detalle->saldoDispensacion = $info['Kadsad'];
			$detalle->esPos = $info['Artpos'] == "N" ? false: true;
			
			$detalle->imprimirArticulo =  $info['Kadimp'];
			$detalle->altaArticulo =  $info['Kadalt'];
			$detalle->cantidadAlta = $info['Kadcal'];
			$detalle->manejoInterno = $info['Kadint'];
			$detalle->posologia = $info['Kadpos'];
			$detalle->unidadPosologia = $info['Kadupo'];
			
			$detalle->codigoCreador = $info['Kadusu'];
			
			$detalle->nombreGenerico = $info['Artgen'];		//Agosto 27 de 2012
			
			$detalle->idOriginal = $info['Kadido'];
			
			/****************************************************************************************
			 * Junio 7 de 2012
			 ****************************************************************************************/
			if( isset($detalle->origen) && $detalle->origen == $codigoCentralMezclas ){
				if( esProductoNoPOSCM( $conex, $wbasedato, $wcenmez, $info['Kadart'] ) ){
					$detalle->esPos = false;
				}
			}
			/****************************************************************************************/

			//****************************Consulta de las cantidades del CTC autorizado acumulado y usado
			$q2 = "SELECT Ctccau,Ctccus,Ctcuca FROM {$wbasedato}_000095 WHERE Ctchis = '$historia' AND Ctcing = '$ingreso' AND Ctcart = '".$info['Kadart']."'";
			$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			while($info2 = mysql_fetch_array($res2)){
				$detalle->cantidadAutorizadaCtc 	= $info2['Ctccau'];
				$detalle->cantidadUtilizadaCtc 		= consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$info['Kadart']);
				$detalle->unidadesCantidadesCtc 	= $info2['Ctcuca'];
			}
			//***************************

			//****************************Puede reemplazar
			$q3 = "SELECT Spauen, Spausa FROM {$wbasedato}_000004 WHERE Spahis = '".$historia."' AND Spaing = '".$ingreso."' AND Spaart = '".$info['Kadart']."'";
			$res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
			$detalle->puedeReemplazar = true;
			while($info3 = mysql_fetch_array($res3)){
				$detalle->puedeReemplazar 	= $info3['Spauen'] == $info3['Spausa'] ? true : false;
			}

			/*
			//Si ya hay una cantidad dispensada diferente de la solicitada
			if($info['Kadcdi'] == $info['Kaddis'] && $info['Kadcdi'] > 0){
				$detalle->puedeReemplazar = false;
			}
			//Si ya hay una cantidad dispensada diferente de la solicitada
			if($info['Kadcdi'] != $info['Kaddis'] && $info['Kaddis'] == 0){
				$detalle->puedeReemplazar = false;
			}
			*/
			//***************************
			
			//Si no esta confirmado el articulo siendo de central de mezclas, no se incluye
			if(isset($detalle->origen) && $detalle->origen == $codigoCentralMezclas){
				if(isset($detalle->estaConfirmado) && $detalle->estaConfirmado == "on"){
					$coleccion[] = $detalle;
				}
			} else {
				$coleccion[] = $detalle;
			}
		}
	}
	return $coleccion;
}
/**
 * Consulta el detalle del perfil farmacoterapeutico basado en el detalle historico del kardex por fecha y paciente
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @return unknown_type
 * 
 * NOTAS:
 * 
 * 20-Sep-10:  La consulta de los articulos se hará por la fracción que tenga en el registro no en la 59, el kardex tendra la responsabilidad de asignar la fracción adecuada.
 */
function consultarDetallePerfilKardex($historia,$ingreso,$fecha){
	global $wbasedato;
	global $wcenpro;
	global $conex;
	global $wemp_pmla;
	global $usuario;		//Información de usuario
	
	$coleccion = array();

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	if($usuario->gruposMedicamentos != "*" && $usuario->gruposMedicamentos != '' && $usuario->gruposMedicamentos != 'NO APLICA'){
		$tieneGruposIncluidos = true;
	}
	//********************************

	$q = "SELECT
			Kadart,Artcom,Artgen,Artgru,Artpos,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadcdi,Kadpri,Kadare,Kadusu, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Kadido
		FROM
			".$wbasedato."_000054, ".$wbasedato."_000026
		WHERE 
			Kadhis = '$historia'
			AND Kading = '$ingreso'
			AND Kadfec < '$fecha'";
			if($tieneGruposIncluidos){
				$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
			}
			$q .= "
			AND Kadori = '$codigoServicioFarmaceutico'
			AND Kadcco = '$usuario->centroCostosGrabacion'
			AND Artcod = Kadart ";

	$subConsulta = "SELECT
			Kadart,Artcom,Artgen,'' Artgru,'' Artpos,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadcdi,Kadpri,Kadare,Kadusu, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Kadido
		FROM
			".$wcenpro."_000002, ".$wbasedato."_000054
		WHERE
			Kadhis = '$historia'
			AND Kading = '$ingreso'
			AND Kadfec < '$fecha'
			AND Kadori = '$codigoCentralMezclas' 
			AND Kadcco = '$usuario->centroCostosGrabacion'
			AND Artcod = Kadart ";

	if($usuario->esUsuarioCM){
		$q = $q." UNION ".$subConsulta;
	} else {
		if(!$tieneGruposIncluidos){
			$q = $q." UNION ".$subConsulta;
		}
	}
	$q = $q." ORDER BY Kadfec DESC";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new detalleKardexDTO();
				
			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $info['Kadfec'];
			$detalle->codigoArticulo = $info['Kadart']."-".$info['Kadori']."-".$info['Artcom'];
			$detalle->cantidadDosis = $info['Kadcfr'];
			$detalle->unidadDosis = $info['Kadufr'];
			$detalle->diasTratamiento = $info['Kaddia'];
			$detalle->estadoRegistro = $info['Kadest'];
			$detalle->estadoAdministracion = $info['Kadess'];
			$detalle->periodicidad = $info['Kadper'];
			$detalle->condicionSuministro = $info['Kadcnd'];
			$detalle->dosisMaxima = $info['Kaddma'];
			$detalle->formaFarmaceutica = $info['Kadffa'];
			$detalle->fechaInicioAdministracion = $info['Kadfin'];
			$detalle->horaInicioAdministracion = $info['Kadhin'];
			$detalle->via = $info['Kadvia'];
			$detalle->fechaKardex = $info['Kadfec'];
			$detalle->suspendido = $info['Kadsus'];
			$detalle->estaConfirmado = $info['Kadcon'];
			$detalle->origen = $info['Kadori'];
			$detalle->observaciones = $info['Kadobs'];
			$detalle->cantidadGrabar = $info['Kadcdi'];
			$detalle->cantidadUnidadManejo = $info['Kadcma'];
			$detalle->unidadManejo = $info['Kaduma'];
			$detalle->grupo = $info['Artgru'];
			$detalle->tienePrioridad = $info['Kadpri'];
			$detalle->aprobado = $info['Kadare'];
			$detalle->esPos = $info['Artpos'] == "N" ? false: true;
			
			$detalle->imprimirArticulo =  $info['Kadimp'];
			$detalle->altaArticulo =  $info['Kadalt'];
			$detalle->cantidadAlta = $info['Kadcal'];
			$detalle->manejoInterno = $info['Kadint'];
			$detalle->posologia = $info['Kadpos'];
			$detalle->unidadPosologia = $info['Kadupo'];
			
			$detalle->codigoCreador = $info['Kadusu'];
			
			$detalle->nombreGenerico = $info['Artgen'];		//Agosto 27 de 2012
			
			$detalle->idOriginal = $info['Kadido'];
			
			/****************************************************************************************
			 * Junio 7 de 2012
			 ****************************************************************************************/
			$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
			 
			if( isset($detalle->origen) && $detalle->origen == $codigoCentralMezclas ){
				if( esProductoNoPOSCM( $conex, $wbasedato, $wcenmez, $info['Kadart'] ) ){
					$detalle->esPos = false;
				}
			}
			/****************************************************************************************/

			$coleccion[] = $detalle;
		}
	}
	return $coleccion;
}

function consultarUsuariosCtc($servicio){
	global $wbasedato;
	global $conex;

	$usuarios = "";

	$q = "SELECT
			Ccouct
		FROM 
			".$wbasedato."_000011
		WHERE
			Ccocod = '$servicio'
			AND Ccoest = 'on';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$info = mysql_fetch_array($res);

		$usuarios = $info['Ccouct'];
	}
	return $usuarios;
}

function esUsuarioCtc($centroCostosUsuario,$codigoUsuario){
	global $wbasedato;
	global $conex;

	$esUsuarioCtc = false;

	$q 	 = "SELECT Ccouct FROM ".$wbasedato."_000011 WHERE Ccocod = '$centroCostosUsuario' AND Ccoest = 'on';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$info = mysql_fetch_array($res);

		$usuarios = $info['Ccouct'];
	}

	//Verifico si el codigo del usuario esta matriculado en el ccouct
	$pos = strpos($usuarios, $codigoUsuario);

	if ($pos !== false) {
		$esUsuarioCtc = true;
	}

	return $esUsuarioCtc;
}

/**
 * Consulta los medicamentos anteriores a la fecha del kardex
 *
 * @param unknown_type $conexion
 * @param unknown_type $historia
 * @param unknown_type $ingreso
 * @param unknown_type $fecha
 * @return unknown
 */
function consultarDetalleMedicamentosAnterioresKardex($historia,$ingreso,$fecha,$tipoProtocolo){
	global $wbasedato;
	global $wcenpro;
	global $conex;

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	global $usuario;		//Información de usuario

	//Protocolos
	global $protocoloNormal;
	global $protocoloNutricion;
	global $protocoloAnalgesia;
	global $protocoloQuimioterapia;

	global $nombreProtocoloNormal;
	global $nombreProtocoloNutricion;
	global $nombreProtocoloAnalgesia;
	global $nombreProtocoloQuimioterapia;
	
	global $regletaFamiliaHist;
	
	//$usuario->centroCostos
	$coleccion = array();

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	if($usuario->gruposMedicamentos != "*" && $usuario->gruposMedicamentos != '' && $usuario->gruposMedicamentos != 'NO APLICA'){
		$tieneGruposIncluidos = true;
	}
	//********************************

	//CONSULTA DE RANGO DE FECHAS ANTERIORES DE KARDEX::
	$qFechas = "SELECT
					MIN(Fecha_data) fMin,MAX(Fecha_data) fMax
				FROM
					mhosidc_000053
				WHERE
					Karhis = '$historia'
					AND Karing = '$ingreso'
					AND Fecha_data < '$fecha'";

	$resFechas = mysql_query($qFechas, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qFechas . " - " . mysql_error());
	if($rsFechas = mysql_fetch_array($resFechas)){

		if(isset($rsFechas['fMin']) && !empty($rsFechas['fMin'])){
			// $q = "SELECT
				// Kadart,Artcom,Artgen,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadnar,Kadpro,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadusu, Famcod, Famnom, Artuni, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
			// FROM
				// ".$wbasedato."_000054, ".$wbasedato."_000026, ".$wbasedato."_000115, ".$wbasedato."_000114
			// WHERE
				// Kadhis = '$historia'
				// AND Kading = '$ingreso'
				// AND Kadfec BETWEEN '{$rsFechas['fMin']}' AND '{$rsFechas['fMax']}'";

			// if($tieneGruposIncluidos){
				// $q .=" AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
			// }
			// $q .=" AND Kadpro LIKE '$tipoProtocolo'
				// AND Kadcco = '$usuario->centroCostosGrabacion'
				// AND Kadori = '$codigoServicioFarmaceutico'
				// AND Kadalt != 'on'
				// AND Artcod = Kadart
				// AND Kadart = Relart
				// AND Relfam = Famcod 
			// UNION
			// SELECT
				// Kadart,Artcom,Artgen,'' Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadnar,Kadpro,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadusu, Famcod, Famnom, Artuni, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, Relcon, Reluni
			// FROM
				// cenpro_000002, ".$wbasedato."_000054, ".$wbasedato."_000115, ".$wbasedato."_000114
			// WHERE
				// Kadhis = '$historia'
				// AND Kading = '$ingreso'
				// AND Kadfec BETWEEN '{$rsFechas['fMin']}' AND '{$rsFechas['fMax']}'
				// AND Kadpro LIKE '$tipoProtocolo'
				// AND Kadcco = '$usuario->centroCostosGrabacion'
				// AND Kadori = '$codigoCentralMezclas'
				// AND Kadalt != 'on'
				// AND Artcod = Kadart
				// AND Kadart = Relart
				// AND Relfam = Famcod 
			// ORDER BY
				// Kadfec DESC, Famcod, Artcom ";
				
				
			$q = "SELECT
				Kadart,Artcom,Artgen,SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadnar,Kadpro,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadusu, Artcod as Famcod, Artgen as Famnom, Artuni, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, '' as Relcon, Artuni as Reluni, Artpos, Kadido
			FROM
				".$wbasedato."_000054, ".$wbasedato."_000026
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadfec BETWEEN '{$rsFechas['fMin']}' AND '{$rsFechas['fMax']}'";

			if($tieneGruposIncluidos){
				$q .=" AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery ";
			}
			$q .=" AND Kadpro LIKE '$tipoProtocolo'
				AND Kadcco = '$usuario->centroCostosGrabacion'
				AND Kadori = '$codigoServicioFarmaceutico'
				AND Kadalt != 'on'
				AND Artcod = Kadart
			UNION
			SELECT
				Kadart,Artcom,Artgen,'' Artgru,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadnar,Kadpro,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kaddis,Kaduma,Kadcma,Kadusu, Artcod as Famcod, Artgen as Famnom, Artuni, Kadimp, Kadalt, Kadcal, Kadint, Kadpos, Kadupo, '' as Relcon, Artuni as Reluni, '' as Artpos, Kadido
			FROM
				".$wcenpro."_000002, ".$wbasedato."_000054
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadfec BETWEEN '{$rsFechas['fMin']}' AND '{$rsFechas['fMax']}'
				AND Kadpro LIKE '$tipoProtocolo'
				AND Kadcco = '$usuario->centroCostosGrabacion'
				AND Kadori = '$codigoCentralMezclas'
				AND Kadalt != 'on'
				AND Artcod = Kadart
			ORDER BY
				Kadfec DESC, Famcod, Artcom ";

			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{
				$cont1 = 0;

				while ($cont1 < $num){
					$cont1++;

					$detalle = new detalleKardexDTO();

					$info = mysql_fetch_array($res);

					$detalle->historia = $historia;
					$detalle->ingreso = $ingreso;
					$detalle->fecha = $info['Kadfec'];

					$fecha_ciclo = str_replace("-","",$info['Kadfec']);

					$detalle->codigoFamilia = $info['Famcod'];
					$detalle->nombreFamilia = $info['Famnom'];
					$codFamilia = $info['Famcod'];

					if(@!array_key_exists($codFamilia,$regletaFamiliaHist[$fecha_ciclo]))
					{
						$regletaFamiliaHist[$fecha_ciclo][$codFamilia] = array();
					}
					/***********************************************************************/
					/******************* Calculo de regleta por familia ********************/
					/***********************************************************************/

					$colPeriodicidades = consultarPeriodicidades();
					foreach ($colPeriodicidades as $periodicidad){
						if($periodicidad->codigo == $info['Kadper']){
							$horaPeriodicidad = intval($periodicidad->equivalencia);
							break;
						}
					}
					
					$arrAplicacion = array();
					
					$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),$info['Kadfin'],$info['Kadhin'],$horaPeriodicidad,$info['Kadcfr']);
					
					$horaArranque = 2;

					$cont3 = 1;
					$cont2 = $horaArranque;   //Desplazamiento desde la hora inicial
					$caracterMarca = $info['Kadcfr'];

					while($cont3 <= 24){
					
						if(!isset($arrAplicacion[$cont2]) || $arrAplicacion[$cont2]=="" || $arrAplicacion[$cont2]==" " || $arrAplicacion[$cont2]=="-")
							$arrAplicacion[$cont2] = 0;
						
						if(@!array_key_exists($codFamilia,$regletaFamiliaHist[$fecha_ciclo]))
						{
							$regletaFamiliaHist[$fecha_ciclo][$codFamilia] = array();
							$regletaFamiliaHist[$fecha_ciclo][$codFamilia]['esCompuestaFamilia'] = '0';
						}
						
						if(array_key_exists($cont2,$regletaFamiliaHist[$fecha_ciclo][$codFamilia]))
						{
							//Agosto 13 de 2019
							$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['valor'] += (integer) $arrAplicacion[$cont2]*1;
						} 
						else 
						{
							$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2] = array();
							$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['valor'] = (integer)$arrAplicacion[$cont2]*1;
						}

						if($arrAplicacion[$cont2] > 0)
						{
							if(isset($regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['condicion']) && $regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['condicion']!="")
								$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['condicion'] .= "|".$info['Kadcnd'];
							else
								if($info['Kadcnd']!="")
									$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['condicion'] = "|".$info['Kadcnd'];
								else
									$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['condicion'] = "";
						
							if(isset($regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['unidad']) && $regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['unidad']!="")
								$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['unidad'] = $info['Kadufr'];
							else
								$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['unidad'] = $info['Kadufr'];
								

							if(!isset($regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['cont']))
								$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['cont'] = 1; 
							else
								$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['cont']++; 
								
							if(isset($regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['cont']) && $regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['cont'] % 2 != 0)
								$bgTooltip = "#C3D9FF";
							else
								$bgTooltip = "#E8EEF7";
							
							if(!isset($regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['tooltip']) || $regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['tooltip']=="")
							{
								$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['tooltip'] = '<table cellspacing=4>';
								$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['tooltip'] .= '<tr style=background-color:#2A5DB0;color:#ffffff><td align=center> Código </td><td align=center> Nombre </td><td align=center> Dosis </td><td align=center> Unidad </td><td align=center> Frecuencia </td><td align=center> Vía </td><td align=center> Inicio </td><td align=center> Condición </td></tr>';
							}
							
							if(isset($regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['tooltip']))
							{
								$regletaFamiliaHist[$fecha_ciclo][$codFamilia][$cont2]['tooltip'] .= '<tr style=background-color:'.$bgTooltip.'><td> '.$info['Kadart'].' </td><td> '.trim($info['Artgen']).' </td><td> '.$info['Kadcfr'].' '.$info['Kadufr'].' </td><td align=center> '.$info['Artuni'].' </td><td align=center> '.$info['Kadper'].' </td><td align=center> '.$info['Kadvia'].' </td><td align=center> '.$info['Kadfin'].' </td><td align=center> '.$info['Kadcnd'].' </td></tr>';
							}

						}
						
						
						if($cont2 == 24){
							$cont2 = 0;
						}

						$cont3++;
						$cont2++;

						if($cont2 % 2 != 0){
							$cont2++;
						}
						if($cont3 % 2 != 0){
							$cont3++;
						}

						if($cont2 == $horaArranque){
							break;
						}
					}

					// Indico si hay mas de un articulo en la familia
					if($auxFamilia == $codFamilia)
						$regletaFamiliaHist[$fecha_ciclo][$codFamilia]['esCompuestaFamilia'] = '1';

					$auxFamilia = $codFamilia;
					
					//$detalle->codigoArticulo = $info['Kadart']."-".$info['Kadori']."-".$info['Artcom'];
					// $detalle->codigoArticulo = $info['Famnom']." ".$info['Relcon']." ".$info['Reluni'];
					$detalle->codigoArticulo = $info['Kadart']."--".$info['Famnom'];
					$detalle->cantidadDosis = $info['Kadcfr'];
					$detalle->unidadDosis = $info['Kadufr'];
					$detalle->diasTratamiento = $info['Kaddia'];
					$detalle->estadoRegistro = $info['Kadest'];
					$detalle->estadoAdministracion = $info['Kadess'];
					$detalle->periodicidad = $info['Kadper'];
					$detalle->condicionSuministro = $info['Kadcnd'];
					$detalle->dosisMaxima = $info['Kaddma'];
					$detalle->formaFarmaceutica = $info['Kadffa'];
					$detalle->fechaInicioAdministracion = $info['Kadfin'];
					$detalle->horaInicioAdministracion = $info['Kadhin'];
					$detalle->via = $info['Kadvia'];
					$detalle->fechaKardex = $info['Kadfec'];
					$detalle->suspendido = $info['Kadsus'];
					$detalle->estaConfirmado = $info['Kadcon'];
					$detalle->origen = $info['Kadori'];
					$detalle->observaciones = $info['Kadobs'];
					$detalle->cantidadUnidadManejo = $info['Kadcma'];
					$detalle->unidadManejo = $info['Kaduma'];
					$detalle->grupo = $info['Artgru'];
					$detalle->nombreArticulo	= $info['Kadnar'];
					$detalle->tipoProtocolo = $info['Kadpro'];
					
					$detalle->imprimirArticulo =  $info['Kadimp'];
					$detalle->altaArticulo =  $info['Kadalt'];
					$detalle->cantidadAlta = $info['Kadcal'];
					$detalle->manejoInterno = $info['Kadint'];
					$detalle->posologia = $info['Kadpos'];
					$detalle->unidadPosologia = $info['Kadupo'];
					
					$detalle->codigoCreador = $info['Kadusu'];
					
					$detalle->nombreGenerico = $info['Artgen'];		//Agosto 27 de 2012
					
					$detalle->esPos = $info['Artpos'] == "N" ? false: true;
					
					$detalle->idOriginal = $info['Kadido'];

					switch($detalle->tipoProtocolo){
						case $protocoloNormal:
							$detalle->nombreProtocolo = $nombreProtocoloNormal;
							break;
						case $protocoloNutricion:
							$detalle->nombreProtocolo = $nombreProtocoloNutricion;
							break;
						case $protocoloAnalgesia:
							$detalle->nombreProtocolo = $nombreProtocoloAnalgesia;
							break;
						case $protocoloQuimioterapia:
							$detalle->nombreProtocolo = $nombreProtocoloQuimioterapia;
							break;
						default:
							$detalle->nombreProtocolo = $nombreProtocoloNormal;
							break;
								
					}
						
					$coleccion[] = $detalle;
				}
			}
		}
	}
	return $coleccion;
}

function consultarCambiosKardexPorTiempo($servicio, $minutos){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Kauhis,Kauing,Kaufec,Kaumen,Kaudes, (SELECT CONCAT(Usuario,' - ',Descripcion) FROM usuarios WHERE Codigo = Usuario) Usuario, Fecha_registro, Hora_registro, Ubisac servicio,
			(SELECT Cconom FROM ".$wbasedato."_000011 WHERE	Ccocod = Ubisac) nomServicio, Karcon
		FROM 
			".$wbasedato."_000018,
			(
				SELECT
					Kauhis, Kauing, Kaufec, Kaumen, Kaudes, SUBSTRING(".$wbasedato."_000055.Seguridad FROM INSTR(".$wbasedato."_000055.Seguridad,'-')+1) Usuario, ".$wbasedato."_000055.Fecha_data Fecha_registro, ".$wbasedato."_000055.Hora_data Hora_registro, Karcon
				FROM 
					".$wbasedato."_000055,".$wbasedato."_000053
				WHERE 
					CONCAT(".$wbasedato."_000055.Fecha_data,' ',".$wbasedato."_000055.Hora_data) >= now() - INTERVAL $minutos MINUTE
					AND Kaumen LIKE 'ARTICULO%'
					AND ".$wbasedato."_000053.Fecha_data = Kaufec
					AND Karhis = Kauhis
					AND Karing = Kauing
			) auditoria			
		WHERE 
			auditoria.Kauhis = Ubihis
			AND auditoria.Kauing = Ubiing
			AND Ubisac LIKE '$servicio'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new AuditoriaDTO();
				
			$info = mysql_fetch_array($res);

			$detalle->historia 			= 		$info['Kauhis'];
			$detalle->ingreso 			= 		$info['Kauing'];
			$detalle->fechaKardex 		= 		$info['Kaufec'];
			$detalle->fechaRegistro		= 		$info['Fecha_registro'];
			$detalle->horaRegistro		= 		$info['Hora_registro'];
			$detalle->mensaje			= 		$info['Kaumen'];
			$detalle->descripcion		= 		$info['Kaudes'];
			$detalle->seguridad			= 		$info['Usuario'];
			$detalle->servicio			= 		$info['nomServicio'];
			$detalle->confirmadoKardex  = 		$info['Karcon'];
				
			$coleccion[] = $detalle;
		}
	}
	return $coleccion;
}

function consultarMedicamentosPendientesHorario($wservicio, $fecha, $whabitacion){
	global $wbasedato;
	global $conex;
	global $wcenpro;
	global $codigoServicioFarmaceutico;

	global $usuario;

	$coleccion = array();

	$q = "SELECT
			Karhis, Karing, Kadfec, Kadart, Kadfin, Kadhin, Kadper, Kadcnd, Perequ, Habcod, Kadcfr, Kadufr, Kadvia,
			(
				CASE WHEN Kadori = '$codigoServicioFarmaceutico' THEN (
												SELECT Artcom
												
												FROM {$wbasedato}_000026
												WHERE artcod = kadart
										) ELSE (
												SELECT Artcom
												FROM {$wcenpro}_000002
												WHERE artcod = kadart
										)
				END) Nombre
		FROM
		{$wbasedato}_000020, {$wbasedato}_000053, {$wbasedato}_000054, {$wbasedato}_000043
		WHERE
			Habest='on'
			AND Habdis = 'off'
			AND Karhis = Habhis
			AND Karing = Habing	
			AND Karcon = 'on'
			AND Karest = 'on'
			AND Kadsus = 'off'
			AND Kadper = Percod
			AND Kadhis = Karhis
			AND Karcco = Kadcco			
			AND Kading = Kading
			AND Kadfec = mhosidc_000053.Fecha_data
			AND mhosidc_000053.Fecha_data = '$fecha' 
			AND Habcco LIKE '$wservicio' 
			AND Habcod LIKE '$whabitacion'
		ORDER BY
			Kadfec,Habcod,Karhis ASC";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		//Historia e ingreso para traer los nombres del paciente
		$historia = "";
		$ingreso = "";
		$paciente = "";

		if ($num > 0)
		{
			$cont1 = 0;

			while ($cont1 < $num){
					
				$detalle = new MedicamentoHorarioDTO();
					
				$info = mysql_fetch_array($res);

				$detalle->historia 						= 		$info['Karhis'];
				$detalle->ingreso 						= 		$info['Karing'];
					
				if($historia != $detalle->historia){
					$q1 = "SELECT
						CONCAT(pacno1,' ', pacno2,' ', pacap1,' ', pacap2) nombre	
					FROM 
						root_000036, root_000037
					WHERE 
						oriced = pacced 
						AND orihis = '$detalle->historia'
						AND oriing = '$detalle->ingreso'
						AND oriori = '10'
					";

					$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
					$num1 = mysql_num_rows($res1);

					if($num1 > 0){
						$info1 = mysql_fetch_array($res1);
					}
					$paciente = $info1['nombre'];

					//				echo "$cont1 $paciente";
				}
					
				$detalle->fechaKardex 					= 		$info['Kadfec'];
				$detalle->codigoArticulo				= 		$info['Kadart']." - ".$info['Nombre'];
				$detalle->fechaInicioAdministracion		= 		$info['Kadfin'];
				$detalle->horaInicioAdministracion		= 		$info['Kadhin'];
				$detalle->horasFrecuencia				= 		$info['Perequ'];
				$detalle->servicio						= 		$wservicio;
					
				$q2 = "SELECT
							Unides unidadDosis
						FROM 
							{$wbasedato}_000027
						WHERE 
							Unicod = '{$info['Kadufr']}';";

					$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
					$num2 = mysql_num_rows($res2);

					if($num2 > 0){
						$info2 = mysql_fetch_array($res2);
					}
						
					$detalle->dosis							= 		$info['Kadcfr']." ".$info2['unidadDosis'];
					$detalle->habitacion					= 		$info['Habcod'];
					$detalle->paciente						= 		$paciente;

					$condicion = "&nbsp;";
						
					if(!empty($info['Kadcnd'])){

						$q3 = "SELECT
						Condes
					FROM 
					{$wbasedato}_000042
					WHERE 
						Concod = '{$info['Kadcnd']}';";

					$res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
					$num3 = mysql_num_rows($res3);

					if($num3 > 0){
						$info3 = mysql_fetch_array($res3);
					}
					$condicion = $info3['Condes'];
					}
					$detalle->condicion						= 		$condicion;
						
					//Frecuencia
					$frecuencia = "&nbsp;";
						
					if(!empty($info['Kadper'])){

						$q4 = "SELECT
						Percan, Peruni  
					FROM 
					{$wbasedato}_000043
					WHERE 
						Percod = '{$info['Kadper']}';";

					$res4 = mysql_query($q4, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q4 . " - " . mysql_error());
					$num4 = mysql_num_rows($res4);

					if($num4 > 0){
						$info4 = mysql_fetch_array($res4);
					}
					$frecuencia = $info4['Percan']." ".strtoupper($info4['Peruni'])."S";
					}
					$detalle->frecuencia					= 		$frecuencia;
					//Fin frecuencia
						
					//Via
					$via = "&nbsp;";
						
					if(!empty($info['Kadvia'])){
						$q5 = "SELECT
							Viacod,Viades
						FROM 
						{$wbasedato}_000040
						WHERE 
							Viacod = '{$info['Kadvia']}';";

						$res5 = mysql_query($q5, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q5 . " - " . mysql_error());
						$num5 = mysql_num_rows($res5);

						if($num5 > 0){
							$info5 = mysql_fetch_array($res5);
						}
						$via = strtoupper($info5['Viades']);
					}
					$detalle->via					= 		$via;
					//Fin frecuencia
						
					$historia = $detalle->historia;
					$ingreso = $detalle->ingreso;

					$coleccion[] = $detalle;
						
					$cont1++;
			}

		}
		return $coleccion;
}

/*Listado de articulos por kardex
 *
 * 2010-04-30: (Msanchez) - Consulta de todos los pacientes que se encuentren en el servicio, si tienen kardex del dia actual se muestra en pantalla (actualizado)
 * 							si se encuentran en el servicio pero tienen el kardex actualizado del dia anterior (no generado hoy) se muestran,
 */
function consultarListadoArticulosPacientes($wservicio, $fecha){
	global $conex;
	global $wbasedato;
	global $wcenpro;

	$coleccion = array();
	global $codigoServicioFarmaceutico;

	$caso = "";

	//Consulta pacientes en las habitaciones
	$q3 = "SELECT
				Habhis, Habing, Habcod, Habcco
			FROM 
			{$wbasedato}_000020
			WHERE 
				Habcco LIKE '$wservicio' 
				AND Habest = 'on' 
				AND Habdis = 'off' 
				AND Habhis != '' 
				AND Habing != ''
			ORDER BY
				Habcod";

			$res3 = mysql_query($q3, $conex) or die ("Error: ".mysql_errno()." - en el query: $q3 - " . mysql_error());
			$num3 = mysql_num_rows($res3);

			if ($num3 > 0){
				$cont1 = 0;

				while ($cont1 < $num3){
					$cont1++;

					$info = mysql_fetch_array($res3);

					/* Busqueda del encabezado del dia actual, si no se encuentra kardex generado se consulta el dia anterior por kardex, si no se
					 * encuentra
					 */
					$fechaActual		= $fecha;
					$fechaActualMilis 	= time();
					$ayerMilis			= time() - (24 * 60 * 60);
					$fechaAyer			= date("Y-m-d", $ayerMilis);

						
					//Consulto encabezado de kardex del dia
					$q4 = "SELECT
						Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karmeg,Kardie,Karprc,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare
					FROM 
						".$wbasedato."_000053
					WHERE
						Karest = 'on'
						AND Karcon = 'on'
						AND Fecha_data = '".$fecha."' 
						AND Karhis = '".$info['Habhis']."'
						AND Karing = '".$info['Habing']."';";

					$res4 = mysql_query($q4, $conex) or die ("Error: ".mysql_errno()." - en el query: $q4 - " . mysql_error());
					$num4 = mysql_num_rows($res4);

					//Se verifica tambien el detalle
					$q6 = "SELECT Kadhis, Kading FROM ".$wbasedato."_000054 WHERE Kadhis = '".$info['Habhis']."' AND Kading = '".$info['Habing']."' AND Kadfec = '$fecha';";
					$res6 = mysql_query($q6, $conex) or die ("Error: ".mysql_errno()." - en el query: $q6 - " . mysql_error());
					$num6 = mysql_num_rows($res6);
						
					//			echo "<br><br>4".$q4." 6 $q6 Num4: $num4 Num6: $num6<br><br>";
						
					if($num4 > 0 && $num6 > 0){		//Encontro encabezado
						$caso = "1";
					} else {			//No encontro encabezado en el dia actual
						$q5 = "SELECT
						Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karmeg,Kardie,Karprc,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare
					FROM 
						".$wbasedato."_000053
					WHERE
						Karest = 'on'
						AND Karcon = 'on'
						AND Fecha_data = '".$fechaAyer."' 
						AND Karhis = '".$info['Habhis']."'
						AND Karing = '".$info['Habing']."';";
							
						//				echo "<br><br>5".$q5."<br><br>";
							
						$res5 = mysql_query($q5, $conex) or die ("Error: ".mysql_errno()." - en el query: $q5 - " . mysql_error());
						$num5 = mysql_num_rows($res5);
							
						if($num5 > 0){
							$caso = "2";
						} else {
							$caso = "3";
						}
					}

					//Se consultan los kardex por fecha los resultados
					//						echo "<br><br>El caso es: '$caso' <br><br>";
					$estadoKardex = "";

					switch ($caso){
						case '1':		//Kardex actualizado
							$estadoKardex = "Actualizado";
							$fechaConsulta = $fecha;
							break;
						case '2':		//Kardex no actualizado
							$estadoKardex = "No actualizado";
							$fechaConsulta = $fechaAyer;
							break;
						default:
							$estadoKardex = "Sin generar";
							$fechaConsulta = "";
							break;
					}

					//			echo "<br><br>el caso es '$caso' y el estado kardex es '$estadoKardex'";
					if(!empty($fechaConsulta)){
						$q = "SELECT
								Karhis, Karing, Kadfec, Kadart, Kadfin, Kadhin, Kadper, Perequ, Kadcfr, Kadufr, Kaddma, Kaddia, Kadobs, Kadcdi, Kaddis,
							(
							CASE WHEN Kadori = '$codigoServicioFarmaceutico' THEN (
												SELECT Artcom
												FROM {$wbasedato}_000026
												WHERE artcod = kadart
										) ELSE (
												SELECT Artcom
												FROM {$wcenpro}_000002
												WHERE artcod = kadart
										)
							END) Nombre
						FROM
						{$wbasedato}_000053, {$wbasedato}_000054, {$wbasedato}_000043
						WHERE
							Karhis = '{$info['Habhis']}'
							AND Karing = '{$info['Habing']}'
							AND Karcon = 'on'
							AND Karest = 'on' 
							AND Kadsus = 'off'
							AND Kadper = Percod
							AND Kadhis = Karhis
							AND Kading = Kading
							AND Kadfec = mhosidc_000053.Fecha_data
							AND mhosidc_000053.Fecha_data = '$fechaConsulta'
						ORDER BY
							Karhis ASC";
							
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						$num = mysql_num_rows($res);

						//Historia e ingreso para traer los nombres del paciente
						$historia = "";
						$ingreso = "";
						$paciente = "";

						if ($num > 0)
						{
							$cont7 = 0;

							while ($cont7 < $num){
								$cont7++;

								$detalle = new MedicamentoHorarioDTO();

								$info9 = mysql_fetch_array($res);

								$detalle->historia 						= 		$info9['Karhis'];
								$detalle->ingreso 						= 		$info9['Karing'];
								$detalle->fechaKardex					= 		$info9['Kadfec'];
								$detalle->fechaInicioAdministracion 	= 		$info9['Kadfin'];
								$detalle->horaInicioAdministracion 		= 		$info9['Kadhin'];
								$detalle->diasTratamiento 				= 		$info9['Kaddma'];
								$detalle->dosisMaximas 					= 		$info9['Kaddia'];
								$detalle->observaciones 				= 		$info9['Kadobs'];
								$detalle->cantidadADispensar 			= 		$info9['Kadcdi'];
								$detalle->cantidadDispensada 			= 		$info9['Kaddis'];
								$detalle->estadoKardex 					= 		$estadoKardex;

								if($historia != $detalle->historia){
									$q1 = "SELECT CONCAT(pacno1,' ', pacno2,' ', pacap1,' ', pacap2) nombre FROM root_000036, root_000037 WHERE oriced = pacced AND orihis = '$detalle->historia' AND oriing = '$detalle->ingreso' AND oriori = '10'";

									$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
									$num1 = mysql_num_rows($res1);

									if($num1 > 0){
										$info1 = mysql_fetch_array($res1);
									}
									$paciente = $info1['nombre'];

									//				echo "$cont1 $paciente";
								}

								$detalle->fechaKardex 					= 		$info9['Kadfec'];
								$detalle->codigoArticulo				= 		$info9['Kadart']." - ".$info9['Nombre'];
								$detalle->fechaInicioAdministracion		= 		$info9['Kadfin'];
								$detalle->horaInicioAdministracion		= 		$info9['Kadhin'];
								$detalle->horasFrecuencia				= 		$info9['Perequ'];
								$detalle->servicio						= 		$wservicio;

								$q2 = "SELECT Unides unidadDosis FROM {$wbasedato}_000027 WHERE Unicod = '{$info9['Kadufr']}';";

								$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
								$num2 = mysql_num_rows($res2);

								if($num2 > 0){
									$info2 = mysql_fetch_array($res2);

									$detalle->dosis							= 		$info9['Kadcfr']." ".$info2['unidadDosis'];
									$detalle->habitacion					= 		$info['Habcod'];
									$detalle->paciente						= 		$paciente;

									$historia = $detalle->historia;
									$ingreso = $detalle->ingreso;

									$coleccion[] = $detalle;
								}
							}
						}
					}
				}
			} else {
				//No hay habitaciones con pacientes
			}
			return $coleccion;
}

//Listado de articulos por kardex
function consultarListadoArticulosPacientesCentral($wservicio, $fecha){
	global $conex;
	global $wbasedato;
	global $wcenpro;

	$coleccion = array();
	global $codigoCentralMezclas;

	$q = "SELECT
			Karhis, Karing, Kadfec, Kadart, Kadfin, Kadhin, Kadper, Perequ, Habcod, Kadcfr, Kadufr, Kaddma, Kaddia, Kadobs, Kadcdi, Kaddis
		FROM
		{$wbasedato}_000020, {$wbasedato}_000053, {$wbasedato}_000054, {$wbasedato}_000043
		WHERE
			Habest='on'
			AND Habdis = 'off'
			AND Karhis = Habhis
			AND Karing = Habing	
			AND Karcon = 'on'
			AND Karest = 'on'
			AND Kadsus = 'off'
			AND Kadori = '$codigoCentralMezclas'
			AND Kadper = Percod
			AND Kadhis = Karhis
			AND Kading = Kading
			AND Kadfec = {$wbasedato}_000053.Fecha_data
			AND {$wbasedato}_000053.Fecha_data = '$fecha'
			AND Habcco LIKE '$wservicio' 
		ORDER BY
			Habcod,Karhis ASC";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		//Historia e ingreso para traer los nombres del paciente
		$historia = "";
		$ingreso = "";
		$paciente = "";

		if ($num > 0)
		{
			$cont1 = 0;

			while ($cont1 < $num){
				$cont1++;
					
				$detalle = new MedicamentoHorarioDTO();
					
				$info = mysql_fetch_array($res);

				$detalle->historia 						= 		$info['Karhis'];
				$detalle->ingreso 						= 		$info['Karing'];
				$detalle->fechaKardex					= 		$info['Kadfec'];
				$detalle->fechaInicioAdministracion 	= 		$info['Kadfin'];
				$detalle->horaInicioAdministracion 		= 		$info['Kadhin'];
				$detalle->diasTratamiento 				= 		$info['Kaddma'];
				$detalle->dosisMaximas 					= 		$info['Kaddia'];
				$detalle->observaciones 				= 		$info['Kadobs'];
				$detalle->cantidadADispensar 			= 		$info['Kadcdi'];
				$detalle->cantidadDispensada 			= 		$info['Kaddis'];
					
				if($historia != $detalle->historia){
					$q1 = "SELECT
						CONCAT(pacno1,' ', pacno2,' ', pacap1,' ', pacap2) nombre	
					FROM 
						root_000036, root_000037
					WHERE 
						oriced = pacced 
						AND orihis = '$detalle->historia'
						AND oriing = '$detalle->ingreso'
						AND oriori = '10'
					";

					$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
					$num1 = mysql_num_rows($res1);

					if($num1 > 0){
						$info1 = mysql_fetch_array($res1);
					}
					$paciente = $info1['nombre'];

					//				echo "$cont1 $paciente";
				}
					
					
				$detalle->fechaKardex 					= 		$info['Kadfec'];
				$nombreArticulo = "";

				if(isset($info['Kadart']) && $info['Kadart'] != ''){
					$q2 = "SELECT
						Artcom
					FROM 
						{$wcenpro}_000002
					WHERE 
						artcod = '{$info['Kadart']}'";

					$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
					$num2 = mysql_num_rows($res2);

					if($num2 > 0){
						$info2 = mysql_fetch_array($res2);
					}
					$nombreArticulo = $info2['Artcom'];
				}
					
				$detalle->codigoArticulo				= 		$info['Kadart']." - ".$nombreArticulo;
				$detalle->fechaInicioAdministracion		= 		$info['Kadfin'];
				$detalle->horaInicioAdministracion		= 		$info['Kadhin'];
				$detalle->horasFrecuencia				= 		$info['Perequ'];
				$detalle->servicio						= 		$wservicio;
					
				$q2 = "SELECT
						Unides unidadDosis
					FROM 
					{$wbasedato}_000027
					WHERE 
						Unicod = '{$info['Kadufr']}';";

					$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
					$num2 = mysql_num_rows($res2);

					if($num2 > 0){
						$info2 = mysql_fetch_array($res2);
					}
						
					$detalle->dosis							= 		$info['Kadcfr']." ".$info2['unidadDosis'];
					$detalle->habitacion					= 		$info['Habcod'];
					$detalle->paciente						= 		$paciente;
						
					$historia = $detalle->historia;
					$ingreso = $detalle->ingreso;

					$coleccion[] = $detalle;
			}
		}
		return $coleccion;
}

function consultarKardexModificadosFecha($wservicio, $fecha){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
				Habhis, Habing, Habcod,( 
								IFNULL((SELECT DISTINCT
											CONCAT(Codigo, ' - ', Descripcion, ' Modifico el ', mhosidc_000055.Fecha_data, ' a las ', mhosidc_000055.Hora_data)
										FROM 
											mhosidc_000053, mhosidc_000055, Usuarios
										WHERE 
											Karhis = Habhis 
											AND Karing = Habing 
											AND Karcon = 'on'
											AND Karest = 'on' 
											AND Kauhis = Habhis
											AND Kauing = Habing			
											AND Kaufec = mhosidc_000053.Fecha_data
											AND Codigo = SUBSTRING(mhosidc_000055.Seguridad FROM INSTR(mhosidc_000055.Seguridad,'-')+1)
											AND mhosidc_000053.Fecha_data = '$fecha' 
											AND Habcco = '$wservicio' 
										LIMIT 1
								),'Kardex sin modificaciones, no creado o sin confirmar') ) detalle,
				(SELECT  
					CONCAT(pacno1,' ', pacno2,' ', pacap1,' ', pacap2) nombre	
				FROM 
					root_000036, root_000037
				WHERE 
					oriced = pacced
					AND oriori = 01 
					AND orihis = Habhis
					AND oriing = Habing
				) paciente
		FROM
			mhosidc_000020
		WHERE
			Habest='on'
			AND Habdis = 'off'		
			AND Habcco = '$wservicio'";
		
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new ReportesKardexDTO();
				
			$info = mysql_fetch_array($res);

			if(isset($info['Habhis']) && $info['Habhis'] != '' && isset($info['Habing']) && $info['Habing'] != ''){

				$detalle->historia 						= 		$info['Habhis'];
				$detalle->ingreso 						= 		$info['Habing'];
				$detalle->habitacion 					= 		$info['Habcod'];
				$detalle->paciente 						= 		$info['paciente'];
				$detalle->detalle						= 		$info['detalle'];
				$detalle->fecha							= 		$fecha;
					
				$coleccion[] = $detalle;
			}
		}
	}
	return $coleccion;
}

/************************************************************************************************************************************************
 * Marca las horas de aplicacion o suministro de los medicamentos, iniciando a partir de la 1 hasta las 24 horas
 *
 * Parametros:
 *
 * 1.Hora pivote: 	Hora de consulta... Mejor dejarla fija
 * 2.Fecha inicio del suministro:  En formato AAAA-MM-DD
 * 3.Hora inicio del suministro: En formato HH:00:00  TENER EN CUENTA que son HORAS sin minutos, ni segundos
 *
 * @return unknown
 * 
 * Modificaciones:
 * Febrero 22 de 2011.	(Edwin MG)	Si un medicamento comienza al día siguiente a 00:00, 
 * 									no se muestra para el día actual en la regleta
 ************************************************************************************************************************************************/
function obtenerVectorAplicacionMedicamentos($fechaActual, $fechaInicioSuministro, $horaInicioSuministro, $horasPeriodicidad, $caracterMarca = "*"){
	$arrAplicacion = array();

	$horaPivote = 1;

	//$caracterMarca = "*";

	$vecHoraInicioSuministro   = explode(":",$horaInicioSuministro);
	$vecFechaInicioSuministro  = explode("-",$fechaInicioSuministro);

	$vecFechaActual			   = explode("-",$fechaActual);

	$fechaActualGrafica 	= mktime($horaPivote, 0, 0, date($vecFechaActual[1]), date($vecFechaActual[2]), date($vecFechaActual[0]));
	$fechaSuministroGrafica = mktime(intval($vecHoraInicioSuministro[0]), 0, 0, date($vecFechaInicioSuministro[1]), date($vecFechaInicioSuministro[2]), date($vecFechaInicioSuministro[0]));

	$horasDiferenciaHoyFechaSuministro = ROUND(($fechaActualGrafica - $fechaSuministroGrafica)/(60*60));

	if($horasDiferenciaHoyFechaSuministro <= 0 && abs($horasDiferenciaHoyFechaSuministro) >= 24){
		$caracterMarca = "";
	}
	
	/************************************************************************************************************************************************
	 * Febrero 22 de 2011
	 ************************************************************************************************************************************************/
	if( date( "Y-m-d", $fechaActualGrafica+(24*3600) ) == date( "Y-m-d", $fechaSuministroGrafica ) && $vecHoraInicioSuministro[0] == "00" ){
		$caracterMarca = "";
	}
	/************************************************************************************************************************************************/

	if($horasPeriodicidad <= 0){
		$horasPeriodicidad = 1;
	}

	$horaUltimaAplicacion = abs($horasDiferenciaHoyFechaSuministro) % $horasPeriodicidad;
	

	$cont1 = 1;   //Desplazamiento de 24 horas
	$cont2 = 0;   //Desplazamiento desde la hora inicial

	$inicio = false;	//Guia de marca de hora inicial

	if( $fechaActual == $fechaInicioSuministro){
		$cont1 = intval($vecHoraInicioSuministro[0]);
		$arrAplicacion[$cont1] = $caracterMarca;

		while($cont1 <= 24){
			$out = "-";
			if($cont2 % $horasPeriodicidad == 0){
				$out = $caracterMarca;
			}
			$cont2++;

			$arrAplicacion[$cont1] = $out;
			$cont1++;
		}
	} else {
		
		while($cont1 <= 24){
			$out = "-";
			
			//Hasta llegar a la aplicacion
			if($cont1 == abs($horaPivote+$horasPeriodicidad-$horaUltimaAplicacion) || ($cont1==1 && $horaUltimaAplicacion == 0)){
				
				$out = $caracterMarca;
				$inicio = true;
			}

			if($inicio){
				
				if($cont2 % $horasPeriodicidad == 0){
					$out = $caracterMarca;
				}
				$cont2++;
			}
			$arrAplicacion[$cont1] = $out;
			$cont1++;
		}
	}
	return $arrAplicacion;
}

/******************************************************************************************************************************************
 * Inserta los articulo de la tabla definitaiva (mhosidc-000054) y los inserta en la tabla temporal(mhosidc-000060), por ultimo borra
 * los registros de la tabla definitiva
 * 
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $fechaGrabacion
 * @param $tipoProtocolo
 * @return unknown_type
 ******************************************************************************************************************************************/
function cargarArticulosATemporal($historia,$ingreso,$fecha,$fechaGrabacion,$tipoProtocolo){
	global $wbasedato;
	global $conex;
	global $usuario;		//Información de usuario

	//$centroCostosMovimiento,$gruposMedicamentosMovimiento
	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	if($usuario->gruposMedicamentos != "*" && $usuario->gruposMedicamentos != '' && $usuario->gruposMedicamentos != 'NO APLICA'){
		$tieneGruposIncluidos = true;
	}
	//********************************
	
	/********************************************************************************
	 * Junio 14 de 2012
	 ********************************************************************************/
	if( $usuario->centroCostosGrabacion == "*" ){
		$validarCcoQuery = "";
	}
	else{
		$validarCcoQuery = " AND Kadcco = '$usuario->centroCostosGrabacion' ";
	}
	/********************************************************************************/

	//Parte 1:  Parametros de inserción en la tabla temporal
	$q = "INSERT INTO ".$wbasedato."_000060
		   	(Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,Kadnar,Kadreg,Kadusu,Kadfir,Kadcpx,Kadron,Kadfro,Kadaan,Kadcda,Kadcdt,Kaddan,Kadfum,Kadhum,Kadido,Kadfra,Kadfcf,Kadhcf,Kadimp,Kadalt,Kadcal,Kadint,Kadpos,Kadupo,seguridad) ";

	//Parte 1:  Consulta de servicio farmaceutico y grupos de medicamentos si aplica
	$subConsulta1 =	" SELECT
			 ".$wbasedato."_000054.Medico,".$wbasedato."_000054.Fecha_data,".$wbasedato."_000054.hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,'$fechaGrabacion',Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,Kadnar,Kadreg,Kadusu,Kadfir,Kadcpx,Kadron,Kadfro,Kadaan,Kadcda,Kadcdt,Kaddan,Kadfum,Kadhum,Kadido,Kadfra,Kadfcf,Kadhcf,Kadimp,Kadalt,Kadcal,Kadint,Kadpos,Kadupo,".$wbasedato."_000054.Seguridad
		FROM
			".$wbasedato."_000054, ".$wbasedato."_000026
		WHERE
			Kadhis = '$historia'
			AND Kadart = Artcod
			AND Artest = 'on'
			AND Kading = '$ingreso'
			AND Kadori = '$codigoServicioFarmaceutico' ";
			if($tieneGruposIncluidos){
				$subConsulta1 .= " AND Kadart IN (SELECT Artcod FROM ".$wbasedato."_000026 WHERE SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery AND Artest='on') ";
			}
			$subConsulta1 .= " AND Kadpro LIKE '$tipoProtocolo'
			$validarCcoQuery
			AND Kadfec = '$fecha'
		GROUP BY
			".$wbasedato."_000054.Medico,".$wbasedato."_000054.Fecha_data,".$wbasedato."_000054.hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,".$wbasedato."_000054.Seguridad ";

	$subConsulta2 = " SELECT
			 Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,'$fechaGrabacion',Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,Kadnar,Kadreg,Kadusu,Kadfir,Kadcpx,Kadron,Kadfro,Kadaan,Kadcda,Kadcdt,Kaddan,Kadfum,Kadhum,Kadido,Kadfra,Kadfcf,Kadhcf,Kadimp,Kadalt,Kadcal,Kadint,Kadpos,Kadupo,seguridad
		FROM
			".$wbasedato."_000054
		WHERE
			Kadhis = '$historia'
			AND Kadori = '$codigoCentralMezclas'
			AND Kading = '$ingreso'
			$validarCcoQuery
			AND Kadpro LIKE '$tipoProtocolo'
			AND Kadfec = '$fecha'
		GROUP BY
			Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,Kadnar,seguridad ";

	if($usuario->esUsuarioCM){
		$q .= $subConsulta2;
		$q1 = $subConsulta2;
	} else {
		if(!$tieneGruposIncluidos){
			$q .= $subConsulta1." UNION ".$subConsulta2;
			$q1 = $subConsulta1." UNION ".$subConsulta2;
		} else {
			$q .= $subConsulta1;
			$q1 = $subConsulta1;
		}
	}
	
	elementosGrabados( $conex, $articulos, $q1 );	//Mayo 20 de 2011

	$res = mysql_query($q, $conex) or die ( "Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error() );
	
	//Busco si hay un articulo a elminar por inactivación en el sistema
	articulosBorradosInactivos( $conex, $wbasedato, $historia, $ingreso, $fecha, $usuario, $tieneGruposIncluidos, $tipoProtocolo );	//Abril 5 de 2011
	
	/************************************************************************************************************************************
	 * Mayo 20 de 2011
	 * 
	 * Borro uno a uno los registros que efectivamente pasaron a la tabla temporal, por historia, ingreso, fecha del kardex, hora de inicio
	 * y fecha de inicio
	 ************************************************************************************************************************************/
	$malos = verificacionArticulosGuardados( $conex, $wbasedato, "000060", $articulos, "Cargando articulos a la temporal" );
	
	if( count( $articulos ) > 0 ){
		
		foreach( $articulos as $key => $value ){
	
			if( !isset( $malos[$key] ) ){
				
				$q = "DELETE FROM
						".$wbasedato."_000054
					WHERE
						Kadhis = '$historia'
						AND Kading = '$ingreso'
						AND Kadart = '{$value['Kadart']}'
						AND Kadfec = '$fecha'
						AND Kadfin = '{$value['Kadfin']}'
						AND Kadhin = '{$value['Kadhin']}'";
					
				$resDel = mysql_query( $q, $conex ) or die( mysql_errno()."- Error en el query $sql - ".mysql_error() );
			}
		}
	}
	/************************************************************************************************************************************/
	
	//Borro el detalle de la definitiva. Esto con el fin de almacenar los registros modificados de la temporal
//	$q = "DELETE FROM
//			".$wbasedato."_000054
//		WHERE
//			Kadhis = '$historia'
//			AND Kading = '$ingreso'
//			AND Kadpro LIKE '$tipoProtocolo' ";
//			if($tieneGruposIncluidos){
//				$q .= " AND Kadori = '$codigoServicioFarmaceutico' ";
//				$q .= " AND Kadart IN (SELECT Artcod FROM ".$wbasedato."_000026 WHERE SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery AND Artest='on') ";
//			}
//			$q .= " AND Kadfec = '$fecha'
//			AND Kadcco = '$usuario->centroCostosGrabacion'";
//
//	$subConsulta = "DELETE FROM
//			".$wbasedato."_000054
//		WHERE 
//			Kadhis = '$historia'
//			AND Kading = '$ingreso'
//			AND Kadori = '$codigoCentralMezclas'
//			AND Kadpro LIKE '$tipoProtocolo'
//			AND Kadfec = '$fecha'
//			AND Kadcco = '$usuario->centroCostosGrabacion'";
//
//	if($usuario->esUsuarioCM){
//		$q = $subConsulta;
//	}
//	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}
 
/********************************************************************************************************************************
 * Consulta la cantidad de aplicaciones dado un artículo por paciente.  Basado unicamente en la cantidad dispensada del articulo 
 * de días anteriores
 * 
 * @param $historia
 * @param $ingreso
 * @param $codigoArticulo
 * @param $fechaInicial
 * @param $fechaFinal
 * @return unknown_type
 * 
 * Enero 05 de 2011
 ********************************************************************************************************************************/
function consultarAplicacionesArticuloPaciente( $conex, $wbasedato, $idRegistroAnterior ){

	$dosisAplicadas = 0;
	$rows = false;

	//Se suman las cantidades dispensadas de dicho articulo mientras la dosis maxima sea > 0
	do{
		//Cuenta de dosis
		$q = "SELECT
				  Kaddis, Kaddma, Kadreg, Kadcfr, Kadcma
			  FROM
				  {$wbasedato}_000054
			  WHERE
				  id = '$idRegistroAnterior'
				  AND kaddma > 0
				  AND kaddma != ''
			  ";
	
		$res = mysql_query( $q, $conex ) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
	
		if ($num > 0){
			
			$rows = mysql_fetch_array( $res );
			
			@$dosisAplicadas += intval( $rows['Kaddis']/($rows['Kadcfr']/$rows['Kadcma']) );	//Abril 18 de 2011
			
			$idRegistroAnterior = $rows['Kadreg'];
		}
		else{
			$rows = false;
		}
	}
	while( $rows && ( $rows['Kaddma'] != '' || $rows['Kaddma'] > 0 ) && !empty( $idRegistroAnterior ) );
	
	return $dosisAplicadas;
}

/********************************************************************************************************************************
 * Coloca las horas faltantes a la regleta. Siempre faltan las horas antes del inicio del medicamento
 ********************************************************************************************************************************/
function arreglarVectorKardex( $array ){

	$val = "";

	if( empty($array) ){	
		return "";
	}
	
	foreach( $array as $key => $value ){
		$inicial = $key;
		break;
	}
	
	for( $i = 1; $i < $inicial; $i++ ){
	
		$array2[ $i ] = '-';
	}
	
	for( $i = $inicial; $i < count( $array )+$inicial; $i++ ){	
		$array2[ $i ] = $array[ $i ];
	}
	
	return $array2;
}



/**
 * Consulta el detalle de articulos del dia anterior a la fecha introducida que no se encuentren suspendidos
 *
 *Se seleccionan los articulos anteriores y se insertarán en la tabla temporal solo los que cumplan las siguientes condiciones:
	-Si no se encuentra suspendido (Hecho en query)
	-Si el articulo tiene dias de tratamiento pendientes, deberá compararse asi:  (Dias tratamiento - (FechaActual - FechaInicioAdministracion) (Logica php)

 * @param unknown_type $conexion
 * @param unknown_type $historia
 * @param unknown_type $ingreso
 * @param unknown_type $fecha
 * @param unknown_type $fechaGrabacion
 */
function cargarArticulosAnteriorATemporal($historia,$ingreso,$fecha,$fechaGrabacion,$tipoProtocolo,$descontarDispensaciones,$horaTrasladoDescuento){
	global $wbasedato;
	global $conex;
	global $usuario;		//Información de usuario

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;
	global $grupoControl;
	
	global $horaCorteDispensacion;

	$tieneCpx = tieneCpxPorHistoria( $conex, $wbasedato, $historia, $ingreso );
	
	$adicionar = true;
	$noAdicionaPorDosis = false;
	$saldoDispensacion = 0;
	$cantidadADispensar = 0;
	$aprobado="'off'";
	$noEnviar="";
	$diasTratamiento = "";
	
	$horaTraslado = false;
	
	//*******************************SI ES PERSONAL DEL LACTARIO, LOS ARTICULOS SE CARGAN APROBADOS
	if($usuario->esUsuarioLactario){
		$aprobado="'on'";
	}
	//********************************
	
	/****************************************************************************************************************
	 * Septiembre 15 de 2011
	 *
	 * Si el centro de costos donde se encuentra el paciente manejan ciclos de produccion, la aprobacion del 
	 * articulo es tal como viene del día anterior
	 ****************************************************************************************************************/
	if( !$usuario->esUsuarioLactario && $tieneCpx ){
		$aprobado = '';
	}
	/****************************************************************************************************************/
	
	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	if($usuario->gruposMedicamentos != "*" && $usuario->gruposMedicamentos != '' && $usuario->gruposMedicamentos != 'NO APLICA'){
		$tieneGruposIncluidos = true;
	}
	//********************************
	// $q = "SELECT
		 	// Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,'$fechaGrabacion',Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,'off' Kadpri,Kadpro,Kadcco,$aprobado Kadare,Kadsad,Kadnar,Kadron,seguridad,
		 	// (SELECT Perequ FROM ".$wbasedato."_000043 WHERE Percod = Kadper) periodicidad, id, Kadusu, Kadfir,Kadcpx, Kadreg, Kadfro, Kadaan, Kadcda,Kadcdt, Kadido, Kadalt, Kadcal, Kadimp, Kadonc, Kadint, Kadpos, Kadupo
		// FROM
			// ".$wbasedato."_000054
		// WHERE
			// Kadhis = '$historia'
			// AND Kading = '$ingreso'
			// AND Kadpro LIKE '$tipoProtocolo'
			// AND Kadcco = '$usuario->centroCostosGrabacion'
			// AND Kadsus = 'off'
			// AND Kadfec = DATE_SUB('".$fecha."',INTERVAL 1 DAY)
		// GROUP BY
			// Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,seguridad";
			
	$q = "SELECT
		 	Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,'$fechaGrabacion',Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,'off' Kadpri,Kadpro,Kadcco,$aprobado Kadare,Kadsad,Kadnar,Kadron,seguridad,
		 	(SELECT Perequ FROM ".$wbasedato."_000043 WHERE Percod = Kadper) periodicidad, id, Kadusu, Kadfir,Kadcpx, Kadreg, Kadfro, Kadaan, Kadcda,Kadcdt, Kadido, Kadalt, Kadcal, Kadimp, Kadonc, Kadint, Kadpos, Kadupo
		FROM
			".$wbasedato."_000054
		WHERE
			Kadhis = '$historia'
			AND Kading = '$ingreso'
			AND Kadpro LIKE '$tipoProtocolo'
			AND Kadcco = '$usuario->centroCostosGrabacion'
			AND Kadsus = 'off'
			AND Kadfec = ( SELECT MAX(kadfec) FROM ".$wbasedato."_000054 WHERE kadhis='$historia' AND kading='$ingreso' )
		GROUP BY
			Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,seguridad";

	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res1);
	
	$articulos = Array();

	if ($num > 0)
	{
		$cont1 = 0;
		
		while ($cont1 < $num){
			
			$aplArticulos = 0; //Aplicaciones anteriores del articulo
			
			$adicionar = true;
			$noAdicionaPorDosis = false;
			$cont1++;
			
			$esDeControl = false;

			$info = mysql_fetch_array($res1);
			
			$auxFecha = $info['Fecha_data'];
			$auxHora = $info['hora_data'];
			
			//Quito espacios			
			$info['Kaddma'] = trim( $info['Kaddma'] );
			$info['Kaddia'] = trim( $info['Kaddia'] );

			if( $info['Fecha_data'] > $info['Kadfec'] ){
				$info['Fecha_data'] = $info['Kadfec'];
				$info['hora_data'] = "23:59:59";
			}
			
			//Busca la hora de traslado
			//Si el articulo fue creado despues de la hora de traslado, entonces, el articulo fue creado en piso
			if( $cont1 == 1 ){
				$horaTraslado = consultarHoraTrasladoUrgencias( $conex, $wbasedato, $historia, $ingreso, $info['Kadfec'] );
			}
			

			//Si se cumplieron los dias de suministro, no se incluyen en el detalle a cargar
			if( isset($info['Kaddia']) && $info['Kaddia'] != "" ){
				$vecFechaKardex = explode("-",$fecha);
				$diasFaltantes = intval($info['Kaddia']) - diasDiferenciaFechas($fecha,$info['Kadfin']);

				if($diasFaltantes <= 0){
					$adicionar = false;
				}
			}

			//Control de aplicaciones máximas
			/************************************************************************
			* Enero 05 de 2011 
			* 
			* Modificación: Enero 24 de 2011
			************************************************************************/
			if( isset($info['Kaddma']) && $info['Kaddma'] != "" ){
				//Consulta el numero de aplicaciones realizadas entre la fecha de inicio del tratamiento y la fecha del kardex
//				$aplArticulos = consultarAplicacionesArticuloPaciente($historia,$ingreso,$info['Kadart'],$info['Kadfin'],$info['Kadhin'],$info['Kadper'],$fecha, $info['id'] );
				$aplArticulos = consultarAplicacionesArticuloPaciente( $conex, $wbasedato, $info['id'] );
				 
				$faltantesDosisMaximas = $info['Kaddma']-$aplArticulos;	//Indica cuantas cantidades de las dosis maximas faltan por aplicar
				
//				if($aplArticulos >= $info['Kaddma']){
//					$adicionar = false;
//					$noAdicionaPorDosis = true;
//				}
				
				if( $faltantesDosisMaximas <= 0 ){
					
					$finFechaDosisMaximas = explode( "-", $info['Kadfin'] );
					$finHoraDosisMaximas = explode( ":", $info['Kadhin'] );
					$finFechaDosisMaximas = date( "Y-m-d", mktime( $finHoraDosisMaximas[0], $finHoraDosisMaximas[1], $finHoraDosisMaximas[2], $finFechaDosisMaximas[1], $finFechaDosisMaximas[2], $finFechaDosisMaximas[0] )+$info['periodicidad']*($info['Kaddma']-1)*3600 );
					
					if( $fechaGrabacion > $finFechaDosisMaximas ){
						$adicionar = false;
						$noAdicionaPorDosis = false;
					}
				}
			}
			/************************************************************************/

			//Control de dosis y dias maximos
			if(isset($info['Kaddia']) && $info['Kaddia'] != "" && isset($info['Kaddma']) && $info['Kaddma'] != "" && !$adicionar){
				$noAdicionaPorDosis = false;
			}

			//Control de la cantidad dispensada
			$cantidadDispensada = $info['Kaddis'];
			$horaDispensacion = $info['Kadhdi'];
			if(trim($info['Kadfec']) != trim($fechaGrabacion)){
				$cantidadDispensada = 0;
				$horaDispensacion = "00:00:00";
			}

			//No adiciono al temporal del dia actual en los siguientes casos:
			//El centro de costos del usuario es diferente del del articulo
//			if($info['Kadori'] == $codigoCentralMezclas && !$usuario->esUsuarioCM && !$usuario->centroCostosHospitalario){
			if($usuario->centroCostosGrabacion == "*" && $usuario->esUsuarioLactario){
				//Articulo de la central de mezclas pero usuario no de la central...
				$adicionar = false;
			} else {
				if($tieneGruposIncluidos){
					//Articulo en uno de los grupos, eso aplica unicamente para el servicio farmaceutico ya que la cm no tiene grupos
					if($info['Kadori'] == $codigoServicioFarmaceutico){
						//*************************Consulta del grupo del medicamento
						$q2 = "SELECT * FROM ".$wbasedato."_000026 WHERE Artcod = '{$info['Kadart']}' AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $usuario->gruposMedicamentosQuery;";
						$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el querys: " . $q2 . " - " . mysql_error());
						$contArticulo = mysql_num_rows($res2);
						//*************************

						if($contArticulo == 0){
							$adicionar = false;
						}
					}
				}
			}

			//Cálculo de las cantidades SIN DISPENSAR del dia anterior.
			if(isset($info['Kadcdi']) && $info['Kadcdi'] != "" && isset($info['Kaddis']) && $info['Kaddis'] != ""){
				$saldoDispensacion = $info['Kadcdi'] - $info['Kaddis'];
			}

			/* El saldo de dispensación es cero si:
			 *
			 *	-Es medicamento de control.
			 *	-El servicio actual es urgencias.
			 *	-El paciente viene trasladado desde urgencias el dia anterior
			 */
			if(intval($saldoDispensacion) < 0){
				$saldoDispensacion = 0;
			}

			//*************************
			if( $descontarDispensaciones ){	//$descontarDispensaciones es verdadero si el día anterior estaba en un cco de urgencias o cirugia
				
				$dosisUsadasServicioAnterior = 0;

				$horaActual = date("H");

				if($horaActual == "00"){
					$horaActual = "24";
				}

				$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),$info['Kadfin'],$info['Kadhin'],$info['periodicidad']);
				$cntAplicaciones = 1;
				$dosisUsadasServicioAnterior = 0;
				$horasDelDia = "24";
				$indicadorDeMarca = "*";

				/* Contabilizar las dosis totales del dia, esto se hace con la cantidad de horas del dia / frecuencia.
				 * Esto se puede hacer con base en 24 horas por lo siguiente:
				 * -La dispensación se basa en 24 horas, si el articulo esta siendo analizado aqui es por que no es primera vez (dosis: 24/frec)
				 * -Lo que se dejó de usar de esas dosis debe pasar al dia siguiente
				 * -La fecha y hora del traslado no afecta los calculos de dosis que pasan, esto debido a las dos premisas anteriores
				 */
				foreach ($arrAplicacion as $apl){
					if($apl == $indicadorDeMarca){

						if($cntAplicaciones < intval(substr($horaTrasladoDescuento,0,2))){
							$dosisUsadasServicioAnterior++;
						}
					}
					$cntAplicaciones++;

				}
				$dosisTotalesDia = $horasDelDia/$info['periodicidad'];
				$dosisACargarDiaSiguiente = $dosisTotalesDia-$dosisUsadasServicioAnterior;

				//Obtiene los dias y dosis de tratamiento acumulado
				obtenerDatosAdicionalesArticulo($info['Kadhis'],$info['Kading'],$info['Kadart'],$diasTtoAcumulados,$dosisTtoTotales);

				//La variable $dosisACargarDiaSiguiente esta expresada en cantidad de dosis pero no representa las unidades a cargar o dispensar
				//La idea entonces es limitar via dosis maximas esta cantidad para calcular CUANTAS UNIDADES FRACCIONADAS representan las dosis a sumar
				//Calculo de las cantidades por articulo
				calcularSaldoActual($conex,$wbasedato,$info['Kadhis'],$info['Kading'],$fecha,$info['Kadart'],$info['Kadfin'],$info['Kadhin'],$info['Kadcfr'],$info['periodicidad'],$dosisACargarDiaSiguiente,$info['Kadori'],$diasTtoAcumulados+1,$cantGrabar,$saldo,$cantDispensar,$cantidadManejo,0,$tipoProtocolo,$horasAplicacionDia, $info['Kaddia'], '');
				
				//Si ya hay dispensación, el sistema no deberá realizar calculos de dias anteriores
				if($info['Kaddis'] != 0 || $info['Kadcdi'] == 0){
					$cantDispensar = 0;
				}

				//$saldoDispensacion = 0; //$cantDispensar; //Abril 23 de 2012, se deja saldo de dispensacion
//				$saldoDispensacion = $cantDispensar;
			}

			$esArticuloNecesidad = false;
			if( !$tieneCpx ){
			
				//No debo contabilizar saldos de dispensacion para articulos de CONTROL
				if($info['Kadori'] == $codigoServicioFarmaceutico){
					//*************************Grupo de control no acumula saldo dispensacion
					$qGru = "SELECT SUBSTRING_INDEX( Artgru, '-', 1 ) grupo FROM ".$wbasedato."_000026 WHERE Artcod = '{$info['Kadart']}';";
					$resGru = mysql_query($qGru,$conex) or die ("Error: " . mysql_errno() . " - en el querys: " . $qGru . " - " . mysql_error());
					$contArticulo = mysql_num_rows($resGru);

					if($infoGru = mysql_fetch_array($resGru)){
						if(isset($infoGru['grupo']) && $infoGru['grupo'] == $grupoControl){
							$esDeControl = true;
							$saldoDispensacion = 0;
						}
					}
					//*************************
				}
			}

			
			//*************************Si el paciente se encuentra en urgencias o cirugia no deberá acumularse saldo bajo ninguna circunstancia
			$qCco = "SELECT Ubisac,Ccourg,Ccocir,Ccoing FROM ".$wbasedato."_000018, ".$wbasedato."_000011 WHERE Ubihis = '$historia' AND Ubiing = '$ingreso' AND Ccocod = Ubisac;";
			$resCco = mysql_query($qCco,$conex) or die ("Error: " . mysql_errno() . " - en el querys: " . $qCco . " - " . mysql_error());

			if($infoCco = mysql_fetch_array($resCco)){
				if(isset($infoCco['Ccourg']) && $infoCco['Ccourg'] == "on" || isset($infoCco['Ccocir']) && $infoCco['Ccocir'] == "on"  || isset($infoCco['Ccoing']) && $infoCco['Ccoing'] == "on"){
					$saldoDispensacion = 0;
				}
			}
			//*************************
			
				
			
			//****************************************************************************************************
			//Febrero 17 de 2011
			//
			// Si el paciente se encuentra en urgencias o cirugia y esta en proceso de traslado, servicio anterior se considera como actual
			// por tanto, si el servicio anterior es de urgencia o cirugia o ingreso y esta en proceso de traslado, el saldo de dispensacion del dia anterior es 0
			//****************************************************************************************************/
			$qCco = "SELECT 
						Ubisac,Ccourg,Ccocir,Ccoing 
					 FROM 
					 	".$wbasedato."_000018, ".$wbasedato."_000011 
					 WHERE 
					 	Ubihis = '$historia' 
					 	AND Ubiing = '$ingreso' 
					 	AND Ccocod = Ubisan
					 	AND Ubiptr = 'on'
					 ";
			$resCco = mysql_query($qCco,$conex) or die ("Error: " . mysql_errno() . " - en el querys: " . $qCco . " - " . mysql_error());
		

			if( $infoCco = mysql_fetch_array($resCco) ){
				if(isset($infoCco['Ccourg']) && $infoCco['Ccourg'] == "on" || isset($infoCco['Ccocir']) && $infoCco['Ccocir'] == "on"  || isset($infoCco['Ccoing']) && $infoCco['Ccoing'] == "on"){
					$saldoDispensacion = 0;
				}
			}
			//*****************************************************************************************************************************
			
			//================================================================================================================================================
			//Diciembre 17 de 2010
			//================================================================================================================================================
			// Si el paciente es trasaldo de urgencias a cirugia en el día actual y no se ha creado kardex (No tiene encabezado)
			// el saldo del dia anterior debe ser 0
			if( !existeEncabezadoKardex( $historia, $ingreso, $fecha ) && esTrasladoDeUregnciasDiaAnterior( $conex, $wbasedato, $historia, $ingreso, date( "Y-m-d", strtotime( $fecha )+24*3600 ) ) ){
				$saldoDispensacion = 0;
			}
			//================================================================================================================================================
			
			/****************************************************************************************
			 * Marzo 1 de 2011
			 * 
			 * Si el articulo es creado en piso el dia anterior, se trae el saldo de dispensacion
			 ****************************************************************************************/
			if( $horaTraslado != false && !$esDeControl ){
				
				if( $horaTraslado < $info['hora_data'] ){
					$saldoDispensacion = $info['Kadcdi'] - $info['Kaddis'];
				}
			}
			
			if(intval($saldoDispensacion) < 0){
				$saldoDispensacion = 0;
			}
			/****************************************************************************************/
			
			/******************************************************************************************************
			 * Abril 25 de 2011
			 * 
			 * Si un articulo es a necesidad el saldo de dispensacion del dia anterior es 0
			 *
			 * Modificaciones:
			 * Septiembre 02 de 2011. Se requiere saber si un articulo es a necesidad
			 ******************************************************************************************************/
			$esArticuloNecesidad = false;
			if( $info['Kadcnd'] != '' || $saldoDispensacion > 0 ){	//Septiembre 02 de 2011
				
				$sql = "SELECT Contip "
	     			   ."   FROM ".$wbasedato."_000042 "      //Tabla condiciones de administracion
	     			   ."  WHERE concod = '".$info['Kadcnd']."'";
				
				$resAN = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$numrowsAN = mysql_num_rows( $resAN );
				
				if( $numrowsAN ){
					$rowsAN = mysql_fetch_array( $resAN );
					if( $rowsAN[ 'Contip' ] == 'AN' ){
						$saldoDispensacion = 0; 
						$esArticuloNecesidad = true;
					}
				}
			}
			/******************************************************************************************************/
			

			
			
			
			//Cantidad a dispensar
			//Cantidad a dispensar
			//$descontarDispensaciones solo es verdadera si tiene traslado desde uregncias el dia anterior
			//Se calcula la cantidad a dispensar como si fuera la primera vez en caso de que el paciente el dia anterior
			//venga de urgencias
//			if( false && $descontarDispensaciones ){
//				$cantidadADispensar = calcularCantidadGrabar( date( "Y-m-d" ), $info['Kadhin'], 2, true );
//			}
//			else 
			if($saldoDispensacion == 0){
				$cantidadADispensar = $info['Kadcdi'];
			} else {
				$cantidadADispensar = $info['Kadcdi'] + $saldoDispensacion;
			}
			
			$noEnviar = $info['Kadess'];
			$diasTratamiento = $info['Kaddia'];

			/* Si no adiciona del dia anterior por dosis deberá hacerse lo siguiente:
			 * -No enviar activo
			 * -Sin cantidad a dispensar
			 * -Sin dosis a aplicar
			 * -Colocar los dias maximos de tratamiento en el kardex para que el articulo SE ELIMINE AUTOMATICAMENTE
			 */
			if($noAdicionaPorDosis && isset($diasTratamiento) && $diasTratamiento == "" && $info['Kaddma'] != ""){
				$noEnviar = "on";
				$cantidadADispensar = "0";

				/*Los dias de tratamiento se asignarán asi:
				 * El calculo es el siguiente:
				 * Cantidades diarias = 24 / Frecuencia
				 * Cantidad dias que cumbre esa dosis maximas = dosis maximas articulo / cantidades diarias
				 */
				$cantidadesDiarias = "";
				$arrAplicaciones = obtenerVectorAplicacionMedicamentos($fecha,$info['Kadfin'],$info['Kadhin'],$info['periodicidad']);

				foreach($arrAplicaciones as $aplic){
					if(isset($aplic) && $aplic == "*"){
						$cantidadesDiarias++;
					}
				}

				if($cantidadesDiarias != 0){
					$diasTratamiento = intval($info['Kaddma']/$cantidadesDiarias);
					
					if($diasTratamiento <= 0){
						$noAdicionaPorDosis = false;
					}

					//Si se cumplieron los dias de suministro, no se incluyen en el detalle a cargar
					if(isset($info['Kaddia']) && $info['Kaddia'] != ""){
						$vecFechaKardex = explode("-",$fecha);
						$diasFaltantes = intval($info['Kaddia']) - $diasTratamiento;

						if($diasFaltantes <= 0){
							$adicionar = false;
							$noAdicionaPorDosis = false;
						}
					}
				}
			}
			
//			ECHO "<BR>.........sALDO A DISPENSASAR: ".$saldoDispensacion;
			//Para el idc siempre debe entrar aquí
			if(true || $adicionar || $noAdicionaPorDosis){
			
				/******************************************************************************************************
				 * Septiembre 02 de 2011 
				 * Si el articulo es a necesidad se busca si hay saldo de articulo, si hay saldo no se envia
				 *
				 * Septiembre 14 de 2011
				 * Por la modificacion de cargos de pda, esta parte se desactiva.  La modifiacion a cargos de pda es:
				 * Si un medicamento es a necesidad y no tiene saldo en piso se pide la cantidad necesaria para una dosis
				 ******************************************************************************************************/
				if( false && $esArticuloNecesidad ){
				
					if( $info['Kadori'] == "SF" ){
						$ccoSaldos = $centroCostosServicioFarmaceutico;
					}
					
					if( $info['Kadori'] == "CM" ){
						$ccoSaldos = $centroCostosServicioFarmaceutico;
					}
					
					$sqlSaldo = "SELECT
									*
								 FROM
									{$wbasedato}_000004 a
								 WHERE
									spahis = '{$info['Kadhis']}'
									AND spaing = '{$info['Kading']}'
									AND spaart = '{$info['Kadart']}'
									AND spacco = '$ccoSaldos'
									AND spauen > spausa
								 ";
								 
					$resSaldo = mysql_query( $sqlSaldo, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					$numrowsSaldo = mysql_num_rows( $resSaldo );
					
					if( $numrowsSaldo > 0 ){
						$noEnviar = 'on';
					}
				}
				/******************************************************************************************************/
				
				/****************************************************************************************************************************************************************
				 * Julio 14 de 2011
				 * 
				 * Busco el sobrante de la cantidad cargada del diá anterior para sumarla a la actual
				 * 
				 ****************************************************************************************************************************************************************/
				$info['Kadron'] = '';
				$info['Kadfro'] = '0000-00-00';
				$auxiliarHoraTraslado = $horaTraslado;
				
				//Busco si hubo traslado el dia actual
				$horaTrasladoHoyAux = consultarHoraTrasladoUrgencias( $conex, $wbasedato, $historia, $ingreso, $fechaGrabacion );
				
				$auxTieneCpx = $tieneCpx;
				
				//Si hay traslado el día actual no creo regleta, esto para que lo genere la PDA
				if( $horaTrasladoHoyAux ){
					$tieneCpx = false;
				}
				
				if( $tieneCpx ){
					
					if( !$horaTraslado ){ 
						$horaTraslado = consultarHoraTraslado( $conex, $wbasedato, $historia, $ingreso, $info['Kadfec'] );
						
						$esTrasladoUrgencia = false;
					}
					else{
						$esTrasladoUrgencia = true;
					}
					
					$infoTipoArticulo = consultarinfotipoarticulosKardex( $conex, $wbasedato );
				
					if( !empty($info['Kadcpx']) ){	//Si ya se ha generado la regleta
						
						$vacioCpx = false;
						if( empty($info['Kadcpx'] ) ){
							$vacioCpx = true;
						}
						
						/************************************************************************************
						 * Septiembre 28 de 2011
						 ************************************************************************************/
						//Si el paciente fue trasladado el dia anterior, debo ver cuantas dosis demas fueron cargadas para el dia actual
						$canCargadaHoraCorte = 0;
						
						$fueReemplazado = false;
						if( trim( $info[ 'Kadaan' ] ) != '' ){
							$explodeRee = explode( ",", $info[ 'Kadaan' ] );
							
							if( !empty( $explodeRee[1] ) && $explodeRee[1] == $info['Kadfec'] ){
								$fueReemplazado = true;
							}
						}
						
						if( $horaTraslado && !empty( $info['Kadreg'] ) && !$fueReemplazado ){
							
							if( $esTrasladoUrgencia ){	//El traslado es del dia anterior a la creacion del kardex
								 
								list( $horaPar ) = explode( ":", $horaTraslado );
								
								if( $horaPar >= 0 ){	//Noviembre 11 de 2011

									$horaPar = intval( $horaPar/2 )*2;

									if( $horaPar < 10 ){
										if( $horaPar != 0 ){
											$horaPar = "0$horaPar:00:00";
										}
										else{
											$horaPar = false;
										}
									}
									else{
										$horaPar = "$horaPar:00:00";
									}

									//Calculo cuanto fue la cantidad cargada hasta la hora de corte
									$canCargadaHoraCorte = 0;
									if( $horaPar ){
										$canCargadaHoraCorte = cantidadTotalDispensadaRonda( $info['Kadcpx'], $horaPar );
									}
								}
								// else{	//Noviembre 11 de 2011
									
									// //Si el medicamento comienza despues de la hora de traslado no se calcula hasta la hora de dispensacion
									// //Esto por que el medicamento ya esta correcto
									// if( strtotime( $info['Kadfec']." $horaTraslado" ) > strtotime( $info['Kadfin']." ".$info['Kadhin'] ) ){
										// //Calculo cuanto fue la cantidad cargada hasta la hora de corte
										// $canCargadaHoraCorte = cantidadTotalDispensadaRonda( $info['Kadcpx'], "$horaCorteDispensacion:00:00" );
									// }
								// }
							}
							else{
								//Calculo cuanto fue la cantidad cargada hasta la hora de corte
								$canCargadaHoraCorte = cantidadTotalDispensadaRonda( $info['Kadcpx'], "$horaCorteDispensacion:00:00" );
							}						
							
							//Le resto el saldo del dia anterior
							$canCargadaHoraCorte -= $info['Kadsad'];
							
							if( $canCargadaHoraCorte < 0 ){
								$canCargadaHoraCorte = 0;
							}
							
							$info['Kaddis'] += $canCargadaHoraCorte;
						}
						/************************************************************************************/
						
						$canTotalACargar = calcularCantidadTotalACargarPorDia( $info['Kadcpx'] );
						$canTotalDispensada = calcularCantidadTotalDispensadaPorDia( $info['Kadcpx'] );
						$canCargadaAyer2 = calcularCantidadTotalADispensadaDia( $info['Kadcpx'] );
						$canCargadaAyer3 = cantidadTotalDispensadaRonda( $info['Kadcpx'], "00:00:00" );
						
						$canADispensarAyer = cantidadTotalADispensarRonda( $info['Kadcpx'], "00:00:00" );
						
						$info['Kadcpx'] = crearAplicacionesCargadasPorHorasKardex( $info['Kadcpx'], $info['Kaddis'] );
						$canCargadaAyer = cantidadTotalDispensadaRonda( $info['Kadcpx'], "00:00:00" );
						
						/************************************************************************************************************************
						 * Creando regleta
						 ************************************************************************************************************************/
						$vectorAplicacion = obtenerVectorAplicacionMedicamentos( $fechaGrabacion, $info['Kadfin'], $info['Kadhin'], $info['periodicidad'] );
						$vectorAplicacion2 = obtenerVectorAplicacionMedicamentos( date( "Y-m-d", strtotime( $fechaGrabacion )+24*3600 ), $info['Kadfin'], $info['Kadhin'], $info['periodicidad'] );
						$vectorAplicacion2 = arreglarVectorKardex( $vectorAplicacion2 );
						
						$quitarUnoARegleta = 25;
						if( count($vectorAplicacion2) == 25 ){
							$quitarUnoARegleta = 24;
						}
						
						foreach( $vectorAplicacion2 as $keyB => $valueB ){
							$vectorAplicacion[ $quitarUnoARegleta ] = $valueB;
							$quitarUnoARegleta++;
						}
						
						$horasAplicacionDia = crearVectorAplicaciones( $vectorAplicacion, $infoTipoArticulo[ $info[ 'Kadpro' ] ]['tiempoPreparacion'], ( (integer) $info['Kadcfr'] )/( (integer) $info['Kadcma'] ), intval( ( strtotime( "1970-01-01 ".$infoTipoArticulo[ $info[ 'Kadpro' ] ]['horaCaroteDispensacion'] ) - strtotime( "1970-01-01 00:00:00" ) )/3600 ) );
						/************************************************************************************************************************/

						if( ($canTotalDispensada - $canCargadaAyer3) > 0 ){
							$horasAplicacionDia = crearAplicacionesCargadasPorHorasKardex( $horasAplicacionDia,  ($canTotalDispensada - $canCargadaAyer3) );
						}
						
						if( $info['Kaddis'] - $canCargadaAyer > 0 ){
							$horasAplicacionDia = crearAplicacionesCargadasPorHorasKardex( $horasAplicacionDia, $info['Kaddis'] - ($canTotalDispensada - $canCargadaAyer3) - $canCargadaAyer);
						}					

						$info['Kadcpx'] = $horasAplicacionDia;
						
						list( $info['Kadron'], $info['Kadfro'] ) = explode( "|", consultarUltimaRondaDispensadaKardex( $info['Kadcpx'] ) );
						
						/**************************************************************************************************************
						 * Noviembre 1 de 2011
						 **************************************************************************************************************/
						if( !empty( $info['Kadron'] ) ){
							
							if( empty( $info['Kadfro'] ) ){
								$info['Kadfro'] = date( "Y-m-d", strtotime( trim( $fechaGrabacion )." 00:00:00" ) + 3600*24 );
							}
							else{
								$info['Kadfro'] = $fechaGrabacion;
							}
						}
						/**************************************************************************************************************/
						
						if( !$esArticuloNecesidad && !$esDeControl && $noEnviar != 'on' ){	//Septiembre 02 de 2011. Si es a necesidad o de control no debe crearse los saldo pendientes del dia anterior	//Mayo 23 de 2012, si el medicamento esta como no enviar no se generea saldo pendiente del dìa anterior
							if( $canADispensarAyer - ceil( $canCargadaAyer2 ) > 0 ){
								$info['Kadcpx'] = "Ant-".( ($canTotalACargar - $canCargadaAyer2)-($canTotalDispensada - $canCargadaAyer3)-($canTotalACargar-$canADispensarAyer) )."-0,".$info['Kadcpx'];
							}
						}
						
						if( $vacioCpx ){
							if( $info['Kadfin'] == date( "Y-m-d" ) && $info['Kadhin'] == "00:00:00" && $info['Kaddis'] == 0 ){
								$info['Kadcpx'] = "Ant-".($info['Kadcfr']/$info['Kadcma'])."-0,".$info['Kadcpx'];
							}
						}
					}
					else{	//Si no se ha generado la regleta
						
						//Si el campo Kadcpx esta vacio significa que recien empezo el ciclo de produccion
						//Se crea la regleta y se carga lo necesario para el dia actual
						//Para ello se tiene en cuenta que la dispensacion se hace desde el dia anterior hasta la hora de corte
						
						/************************************************************************************************************************
						 * Creando regleta para el dia anterior
						 ************************************************************************************************************************/
						$vectorAplicacion = obtenerVectorAplicacionMedicamentos( date( "Y-m-d", strtotime( $fechaGrabacion." 00:00:00" )-24*3600 ), $info['Kadfin'], $info['Kadhin'], $info['periodicidad'] );
						$vectorAplicacion2 = obtenerVectorAplicacionMedicamentos( $fechaGrabacion, $info['Kadfin'], $info['Kadhin'], $info['periodicidad'] );
						$vectorAplicacion2 = arreglarVectorKardex( $vectorAplicacion2 );
						
						$quitarUnoARegleta = 25;
						if( count($vectorAplicacion2) == 25 ){
							$quitarUnoARegleta = 24;
						}

						foreach( $vectorAplicacion2 as $keyB => $valueB ){
							$vectorAplicacion[$quitarUnoARegleta] = $valueB;
							$quitarUnoARegleta++;
						}						
						
						$horasAplicacionDia = crearVectorAplicaciones( $vectorAplicacion, $infoTipoArticulo[ $tipoProtocolo ]['tiempoPreparacion'], $info['Kadcfr']/$info['Kadcma'], intval( ( strtotime( "1970-01-01 ".$infoTipoArticulo[ $tipoProtocolo ]['horaCaroteDispensacion'] ) - strtotime( "1970-01-01 00:00:00" ) )/3600 ) );
						/************************************************************************************************************************/
						
						//Busco hasta donde alcanza las cantidades a dispensar
						$stringAnt = "";
						if( !$horaTraslado ){
							//Calculo la cantidad a dispensar hasta las hora de corte
							if( !empty( $info['Kadreg'] ) ){
								$canADispensarTemp1 = cantidadTotalADispensarRonda( $horasAplicacionDia, "$horaCorteDispensacion:00:00" );
							}
							else{
								$canADispensarTemp1 = 0;
							}
							
							//Calculo la cantidad a dispensar hasta las 24 horas
							$canADispensarTemp2 = cantidadTotalADispensarRonda( $horasAplicacionDia, "00:00:00" );
							
							//La diferencia indica cuanto era la cantidad total a dispensar entre la hora de corte y media noche del mismo dia
							$canADispensarDesdeCorteA24 = $canADispensarTemp2 - $canADispensarTemp1;
							
							//Calculo cuanto hay para cargar para el dia actual
							$totalACargarDiaActual = $info['Kaddis'] - $info['Kadsad'] - $canADispensarDesdeCorteA24 + cantidadDispensadaRondaKardex( $horasAplicacionDia, "00:00:00" );
						}
						else{	//Si hubo traslado
							
							$auxhoraTraslado = $horaTraslado;
							
							//Busco solo la hora, sin minutos y segundos
							list( $horaTraslado ) = explode( ":", $horaTraslado );
							
							$tmpDispensacion = 2; //consultarTiempoDispensacion( $conex, "10" );
							
							//Consulto la hora par mas cercana anterior
							$horaTraslado = intval( $horaTraslado/$tmpDispensacion )*$tmpDispensacion;
							
							//Creo la hora correctamente
							if( $horaTraslado < 10 ){
								if( $horaTraslado != 0 ){
									$horaTraslado = "0$horaTraslado:00:00";
								}
								else{
									$horaTraslado = false;
								}
							}
							else{
								if( $horaTraslado == 24 ){
									$horaTraslado .= "00:00:00";
								}
								else{
									$horaTraslado .= ":00:00";
								}
							}
							
							if( empty( $info['Kaddis'] ) ){	//Si no se dispenso dia anterior
							
								//Calculo cuanto se debe dispensar hasta la hora de corte
								$canADispensarTemp1 = cantidadTotalADispensarRonda( $horasAplicacionDia, "00:00:00" );
								
								//Calculo cuanto se debe dispensar hasta la hora par mas cercana
								$canADispensarAntesRecibo = cantidadTotalADispensarRonda( $horasAplicacionDia, $horaTraslado );
								
								//Calculo la cantidad que no se dispensó el día anterior
								$cantidadFaltante = $canADispensarTemp1 - $canADispensarAntesRecibo;
								
								//Creo string que indica cuanto se dejo de dispensar el dia anterior
								if( $cantidadFaltante > 0 ){
									$stringAnt = "Ant-".$cantidadFaltante."-0,";
								}

								//Como hubo traslado el dia anterior dejo la hora en 0
								//Esto para calcular la cantidad anterior correcta
								if( $horaTraslado ){
									$horaTraslado = "00:00:00";
								}
								
								$totalACargarDiaActual = 0;
								
								$horaTraslado = $auxhoraTraslado;
							}
							else{	//Si se dispenso dia anterior
								
								if( empty( $info['Kadreg'] ) ){	//Para cuando el articulo es creado la primera vez
									
									//Calculo cuanto se debe cargar en el dia actual
									$canADispensarTemp1 = cantidadTotalADispensarRonda( $horasAplicacionDia, "00:00:00" );
									
									$totalACargarDiaActual = $info['Kaddis'] - $canADispensarTemp1;
									
									//Si da negativo significa que hay saldo por grabar del dia anterior
									if( $totalACargarDiaActual < 0 ){
										$stringAnt = "Ant-".abs($totalACargarDiaActual)."-0,";
										$totalACargarDiaActual = 0;
									}
								}
								else{	//No es primera vez
								
									if( $esTrasladoUrgencia ){
									
										$fechorIncioMedicamento = strtotime( "{$info['Kadfin']} {$info['Kadhin']}" );
			
										if( time() > $fechorIncioMedicamento ){
											$ultimoSuministro = suministroAntesFechaCorte( $info['Kadfec'], "$horaTraslado", $info['Kadfin'], $info['Kadhin'], $info['periodicidad'] );
										}
										else{
											$ultimoSuministro = $fechorIncioMedicamento;	//Octubre 12 de 2011
										}
										
										$tiempoHastaDiaSiguiente = strtotime( $info['Kadfec']." $horaCorteDispensacion:00:00" );
										
										$tempSaldo = intval( ( $tiempoHastaDiaSiguiente - $ultimoSuministro )/( $info['periodicidad']*3600 ) );
										
										//Si es menor a 0 indica que el traslado es posterior a la hora de corte
										if( $tempSaldo < 0 ){
											
											$ultimoSuministro = suministroAntesFechaCorte( $info['Kadfec'], "$horaCorteDispensacion:00:00", $info['Kadfin'], $info['Kadhin'], $info['periodicidad'] );
										
											$info['Kaddis'] += intval( ( strtotime( $info['Kadfec']." $horaTraslado" ) - $ultimoSuministro + 5 )/( $info['periodicidad']*3600 ) );
										}
										else{
											$info['Kadsad'] += $tempSaldo;
										}
									}
									
									//Calculo cuanto se debe cargar a partir de la hora de corte
									$canADispensarTemp1 = cantidadTotalADispensarRonda( $horasAplicacionDia, "00:00:00" );
									
									//Calculo cuanto se debe cargar en el dia actual
									$canADispensarTemp1 -= cantidadTotalADispensarRonda( $horasAplicacionDia, "$horaCorteDispensacion:00:00" );
									
									$totalACargarDiaActual = $info['Kaddis'] - $canADispensarTemp1 - $info['Kadsad'];
									
									//Si da negativo significa que hay saldo por grabar del dia anterior
									if( $totalACargarDiaActual < 0 ){
										$stringAnt = "Ant-".abs($totalACargarDiaActual)."-0,";
										$totalACargarDiaActual = 0;
									}	
								}
							
								$horaTraslado = $auxhoraTraslado;
							}
						}
						
						/************************************************************************************************************************
						 * Creando regleta para el dia actual
						 ************************************************************************************************************************/
						$vectorAplicacion = obtenerVectorAplicacionMedicamentos( $fechaGrabacion, $info['Kadfin'], $info['Kadhin'], $info['periodicidad'] );
						$vectorAplicacion2 = obtenerVectorAplicacionMedicamentos( date( "Y-m-d", strtotime( $fechaGrabacion )+24*3600 ), $info['Kadfin'], $info['Kadhin'], $info['periodicidad'] );
						$vectorAplicacion2 = arreglarVectorKardex( $vectorAplicacion2 );
						
						$quitarUnoARegleta = 25;
						if( count($vectorAplicacion2) == 25 ){
							$quitarUnoARegleta = 24;
						}
		
						foreach( $vectorAplicacion2 as $keyB => $valueB ){
							$vectorAplicacion[$quitarUnoARegleta] = $valueB;
							$quitarUnoARegleta++;
						}
						
						$horasAplicacionDia = crearVectorAplicaciones( $vectorAplicacion, $infoTipoArticulo[ $tipoProtocolo ]['tiempoPreparacion'], $info['Kadcfr']/$info['Kadcma'], intval( ( strtotime( "1970-01-01 ".$infoTipoArticulo[ $tipoProtocolo ]['horaCaroteDispensacion'] ) - strtotime( "1970-01-01 00:00:00" ) )/3600 ) );
						/************************************************************************************************************************/
						
						if( $totalACargarDiaActual > 0 ){
							$horasAplicacionDia = crearAplicacionesCargadasPorHorasKardex( $horasAplicacionDia, $totalACargarDiaActual );
						}
						
						$info['Kadcpx'] = $stringAnt.$horasAplicacionDia;
						
						list( $info['Kadron'], $info['Kadfro'] ) = explode( "|", consultarUltimaRondaDispensadaKardex( $info['Kadcpx'] ) );
						
						/**************************************************************************************************************
						 * Noviembre 1 de 2011
						 **************************************************************************************************************/
						if( !empty( $info['Kadron'] ) ){
						
							if( empty( $info['Kadfro'] ) ){								
								$info['Kadfro'] = date( "Y-m-d", strtotime( trim( $fechaGrabacion )." 00:00:00" ) + 3600*24 );
							}
							else{
								$info['Kadfro'] = $fechaGrabacion;
							}
						}
						/**************************************************************************************************************/
					}
				}
				else{
					$info['Kadcpx'] = '';
				}
				
				$tieneCpx = $auxTieneCpx;
				
				$horaTraslado = $auxiliarHoraTraslado;
				/****************************************************************************************************************************************************************/
				
				/**************************************************************************************************************
				 * Septiembre 15 de 2011
				 **************************************************************************************************************/
				if( $tieneCpx ){
					$confirmacionPreparacion = $info[ 'Kadcon' ];
				}
				else{
					$confirmacionPreparacion = 'off';
				}
				/**************************************************************************************************************/
				
				$articulos[] = $info;
				
				$info['Fecha_data'] = $auxFecha;
				$info['hora_data'] = $auxHora;
				
				$info['Kadobs'] = str_replace( "\\", "\\\\", $info['Kadobs'] );	//Marzo 3 de 2011
				$info['Kadobs'] = str_replace( "'", "\'", $info['Kadobs'] );	//Marzo 3 de 2011
				
				//Si el articulo fue aplicado en el dia anterior (kadfec) resto uno
				if( !empty( $info['Kadaan'] ) ){
				
					list( $articuloAnterior, $fechaReemplazo ) = explode( ",", $info['Kadaan'] );
					
					if( $fechaReemplazo == $info['Kadfec'] ){
			
						$fueAplicadoHoy = esAplicado( $conex, $wbasedato, $info['Kadhis'], $info['Kading'], $articuloAnterior, $fechaReemplazo );
						
						if( $fueAplicadoHoy && esAplicado( $conex, $wbasedato, $info['Kadhis'], $info['Kading'], $info['Kadart'], $fechaReemplazo ) ){
							$info['Kadcda']--;
						}
					}
				}
				
				$q = "INSERT INTO ".$wbasedato."_000060
		   					(Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,Kadnar,kadreg,Kadusu,Kadfir,Kadcpx,Kadron,Kadfro,Kadaan,Kadcda,Kadcdt,Kadido, Kadalt,Kadcal,Kadimp,Kadonc,Kadint,Kadpos,Kadupo,seguridad)
		   				VALUES
		   				   ('{$info['Medico']}','{$info['Fecha_data']}','{$info['hora_data']}','{$info['Kadhis']}',
		   					'{$info['Kading']}','{$info['Kadart']}','{$info['Kadcfr']}','{$info['Kadufr']}','$diasTratamiento','{$info['Kadest']}','$noEnviar','{$info['Kadper']}','{$info['Kadffa']}',
		   					'{$info['Kadfin']}','{$info['Kadhin']}','{$info['Kadvia']}','{$fechaGrabacion}','$confirmacionPreparacion','{$info['Kadobs']}','{$info['Kadori']}','{$info['Kadsus']}','{$info['Kadcnd']}',
		   					'{$info['Kaddma']}','$cantGrabar','$cantidadDispensada','{$info['Kaduma']}','{$info['Kadcma']}','{$horaDispensacion}','{$info['Kadsal']}','$cantidadADispensar','{$info['Kadpri']}',
		   					'{$info['Kadpro']}','{$info['Kadcco']}','{$info['Kadare']}','$saldoDispensacion','{$info['Kadnar']}','{$info['id']}','{$info['Kadusu']}','{$info['Kadfir']}','{$info['Kadcpx']}','{$info['Kadron']}','{$info['Kadfro']}','{$info['Kadaan']}','{$info['Kadcda']}','{$info['Kadcdt']}','{$info['Kadido']}','{$info['Kadalt']}','{$info['Kadcal']}','{$info['Kadimp']}','{$info['Kadonc']}','{$info['Kadint']}','{$info['Kadpos']}','{$info['Kadupo']}','{$info['seguridad']}')";

				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
	}
	
	verificacionArticulosGuardados( $conex, $wbasedato, "000060", $articulos, "Kardex dia anterior a temporal" );	//Mayo 20 de 2011 
}

function cargarMedicosAnteriorATemporal($historia,$ingreso,$fecha,$fechaGrabacion){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000063
		   	(Medico,Fecha_data,hora_data,Mettdo,Metdoc,Methis,Meting,Metfek,Metest,Metint,Metesp,Metusu,Metfir,seguridad)
		SELECT
			 Medico,Fecha_data,hora_data,Mettdo,Metdoc,Methis,Meting,'$fechaGrabacion',Metest,Metint,Metesp,Metusu,Metfir,seguridad 
		FROM
			".$wbasedato."_000047
		WHERE
			Methis = '$historia'
			AND Meting = '$ingreso'
			AND Metfek = DATE_SUB('".$fecha."',INTERVAL 1 DAY)";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function cargarDietasATemporal($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000064
		   	(Medico,Fecha_data,hora_data,Dikcod,Dikhis,Diking,Dikfec,Dikest,Dikusu,Dikfir,seguridad)
		SELECT 
			 Medico,Fecha_data,hora_data,Dikcod,Dikhis,Diking,Dikfec,Dikest,Dikusu,Dikfir,seguridad
		FROM 
			".$wbasedato."_000052
		WHERE 
			Dikhis = '$historia'
			AND Diking = '$ingreso'
			AND Dikfec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Borrar
	$q = "DELETE FROM
			".$wbasedato."_000052
		WHERE 
			Dikhis = '$historia'
			AND Diking = '$ingreso'
			AND Dikfec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function cargarDietasAnteriorATemporal($historia,$ingreso,$fecha,$fechaGrabacion){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000064
		   	(Medico,Fecha_data,hora_data,Dikcod,Dikhis,Diking,Dikfec,Dikest,Dikusu,Dikfir,seguridad)
		SELECT 
			 Medico,Fecha_data,hora_data,Dikcod,Dikhis,Diking,'$fechaGrabacion',Dikest,Dikusu,Dikfir,seguridad
		FROM 
			".$wbasedato."_000052
		WHERE 
			Dikhis = '$historia'
			AND Diking = '$ingreso'
			AND Dikfec = DATE_SUB('".$fecha."',INTERVAL 1 DAY)";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function cargarExamenesATemporal($historia,$ingreso,$fecha,$fechaGrabacion){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000061
		   	(Medico,Fecha_data,hora_data,Ekahis,Ekaing,Ekacod,Ekaest,Ekaobs,Ekafes,Ekafec,Ekajus,Ekausu,Ekafir,seguridad)
		SELECT 
			 Medico,Fecha_data,hora_data,Ekahis,Ekaing,Ekacod,Ekaest,Ekaobs,Ekafes,'$fechaGrabacion',Ekajus,Ekausu,Ekafir,seguridad 
		FROM 
			".$wbasedato."_000050
		WHERE 
			Ekahis = '$historia'
			AND Ekaing = '$ingreso'
			AND Ekafec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Borrar
	$q = "DELETE FROM
			".$wbasedato."_000050
		WHERE 
			Ekahis = '$historia'
			AND Ekaing = '$ingreso'
			AND Ekafec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function cargarExamenesAnteriorATemporal($historia,$ingreso,$fecha,$fechaGrabacion){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000061
		   	(Medico,Fecha_data,hora_data,Ekahis,Ekaing,Ekacod,Ekaest,Ekaobs,Ekafes,Ekafec,Ekajus,Ekausu,Ekafir,seguridad)
		SELECT 
			 Medico,Fecha_data,hora_data,Ekahis,Ekaing,Ekacod,Ekaest,Ekaobs,Ekafes,'$fechaGrabacion',Ekajus,Ekausu,Ekafir,seguridad 
		FROM 
			".$wbasedato."_000050
		WHERE 
			Ekahis = '$historia'
			AND Ekaing = '$ingreso'
			AND Ekafec = DATE_SUB('".$fecha."',INTERVAL 1 DAY)";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

/******************************************************************************************************
 * Consulta todas las ordenes que tenga el paciente
 * 
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $datosAdicionales
 * @return unknown_type
 ******************************************************************************************************/
function consultarOrdenesHCE($historia,$ingreso,$fecha,&$datosAdicionales,$especialidad,$detalt=false){
	global $wbasedato;
	global $conex;
	global $codigoAyudaHospitalaria;

	$coleccion = array();
	$datosAdicionales = array();

	$condicionAlta = "";
	if($detalt)
		$condicionAlta = " AND Detalt = 'on' ";
	else
		$condicionAlta = " AND Detalt != 'on' ";
	
	$fecha = date("Y-m-d");
	
	//Medico  Fecha_data  Hora_data  Dettor  Detnro  Detcod  Detesi  Detrdo  Detfec  Detest
	//Medico  Fecha_data  Hora_data  Ordfec  Ordhor  Ordhis  Ording  Ordtor  Ordnro  Ordobs  Ordesp  Ordest  Ordfir
	$q = "SELECT
			Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,Cconom,Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp, NoPos
		FROM 
			hceidc_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, hceidc_000028, hceidc_000017
		WHERE 
			Ordfec = '".$fecha."' 
			AND Ordhis = '$historia' 
			AND Ording = '$ingreso'
			AND Ordest = 'on'
			AND Ordtor = Dettor
			AND Ordnro = Detnro
			AND Detest = 'on'	"
		//	.$condicionAlta.
		."	AND Codigo = Detcod
			AND Servicio = Ordtor
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '{$especialidad}' 
				AND Toeest = 'on'			
				)				
		ORDER BY 
			Ordtor,Ordnro";	//AND Detesi IN ('P','Pendiente') AND Ordesp = 'Pendiente' AND Servicio = Dettor
	
	// $q = "SELECT
			// Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,Cconom,Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Detifh, NoPos
		// FROM 
			// hceidc_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, ".$wbasedato."_000159, hceidc_000047
		// WHERE 
			// Ordfec = '".$fecha."' 
			// AND Ordhis = '$historia' 
			// AND Ording = '$ingreso'
			// AND Ordest = 'on'
			// AND Ordtor = Dettor
			// AND Ordnro = Detnro
			// AND Detest = 'on'	"
		// //	.$condicionAlta.
		// ."	AND Codigo = Detcod
			// AND Servicio = Ordtor
			// AND Tipoestudio NOT IN (
				// SELECT
					// Toetip
				// FROM
					// hceidc_000045
				// WHERE
					// Toeesp != '{$especialidad}' 
				// AND Toeest = 'on'			
				// )
		// UNION
		// SELECT
			// Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,Cconom,Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Detifh, NoPos
		// FROM 
			// hceidc_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, ".$wbasedato."_000159, hceidc_000017
		// WHERE 
			// Ordfec = '".$fecha."' 
			// AND Ordhis = '$historia' 
			// AND Ording = '$ingreso'
			// AND Ordest = 'on'
			// AND Ordtor = Dettor
			// AND Ordnro = Detnro
			// AND Detest = 'on'	
			// AND Codigo = Detcod
			// AND Servicio = Ordtor
			// AND Tipoestudio NOT IN (
				// SELECT
					// Toetip
				// FROM
					// hceidc_000045
				// WHERE
					// Toeesp != '{$especialidad}' 
				// AND Toeest = 'on'			
				// )				
		// ORDER BY 
			// Ordtor,Ordnro		
		// ";	//AND Detesi IN ('P','Pendiente') AND Ordesp = 'Pendiente' AND Servicio = Dettor
		
		$q = "SELECT
			Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,Cconom,Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Detifh, NoPos
		FROM 
			hceidc_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, ".$wbasedato."_000159, hceidc_000047
		WHERE 
			Ordfec = '".$fecha."' 
			AND Ordhis = '$historia' 
			AND Ording = '$ingreso'
			AND Ordest = 'on'
			AND Ordtor = Dettor
			AND Ordnro = Detnro
			AND Detest = 'on'	
			AND Codigo = Detcod
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '{$especialidad}' 
				AND Toeest = 'on'			
				)
		UNION
		SELECT
			Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,Cconom,Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Detifh, NoPos
		FROM 
			hceidc_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, ".$wbasedato."_000159, hceidc_000017
		WHERE 
			Ordfec = '".$fecha."' 
			AND Ordhis = '$historia' 
			AND Ording = '$ingreso'
			AND Ordest = 'on'
			AND Ordtor = Dettor
			AND Ordnro = Detnro
			AND Detest = 'on'	
			AND Codigo = Detcod
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '{$especialidad}' 
				AND Toeest = 'on'			
				)				
		ORDER BY 
			Ordtor,Ordnro		
		";	//AND Detesi IN ('P','Pendiente') AND Ordesp = 'Pendiente' AND Servicio = Dettor


	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new ExamenHCEDTO();
			
			@$info['Ordfec'] = $fecha;

			//Ordfec,Ordhor,Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest
				
			$info = mysql_fetch_array($res);

			//Encabezado de orden
			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
				
			$detalle->fecha = @$info['Ordfec'];
			$detalle->hora = @$info['Ordhor'];
			$detalle->tipoDeOrden = $info['Ordtor'];
			$detalle->numeroDeOrden = $info['Ordnro'];
			$detalle->observacionesOrden = $info['Ordobs'];
			$detalle->estadoOrden = $info['Ordesp'];
			$detalle->estadoRegistroOrden = $info['Ordest'];
			$detalle->firmaOrden = $info['Ordfir'];
			$detalle->nombreExamen = $info['Descripcion'];
			$detalle->nombreCentroCostos = @$info['Cconom'];
			$detalle->codigoExamen = $info['Detcod'];
			$detalle->estadoExamen = $info['Detesi'];
			$detalle->resultadoExamen = $info['Detrdo'];
			$detalle->estadoRegistroExamen = $info['Detest'];
			$detalle->fechaARealizar = $info['Detfec'];
			$detalle->justificacion = $info['Detjus'];
			$detalle->protocoloPreparacion = $info['Protocolo'];
			
			$detalle->nroItem = $info['Detite'];
			$detalle->tipoEstudio = $info['Tipoestudio'];
			
			$detalle->altaExamen = $info['Detalt'];
			$detalle->imprimirExamen = $info['Detimp'];

			$detalle->creadorOrden = $info['Ordusu'];
			$detalle->creadorItem = $info['Detusu'];
			
			$detalle->esPos = $info['NoPos'] == 'on' ? false: true;
			
			//Si es ayuda hospitalaria el centro de costos es hospitalario
			if($detalle->tipoDeOrden == $codigoAyudaHospitalaria){
				$detalle->nombreCentroCostos = "HOSPITALARIAS";
			}
			
			$detalle->firmHCE = $info['Detifh'];
			
			//contando el examenes anteriores
			//$datosAdicionales Array de tres dimensiones
			//[] 1ra. dimension Tipo de origen o servicio al que pertenece
			//[] 2da. dimension Numero de orden 
			//[] 3ra. dimension Si es pendiente o no
			//Este array contiene informacion de cuantos examenes hay pendientes o diferentes a pendientes (Anteriores)
			
			if( $detalle->estadoExamen == "Pendiente" || $detalle->estadoExamen == "P" || $detalle->estadoExamen == "PendienteResultado" ){
				@$datosAdicionales[ $detalle->tipoDeOrden ][ $detalle->numeroDeOrden ]['Pendientes']++;
			}
			else{
				@$datosAdicionales[ $detalle->tipoDeOrden ][ $detalle->numeroDeOrden ]['Anteriores']++;
			}
			
			//Tipos de ayudas, cooresponde al codigo Tipo Estudio
			$detalle->tipoAyudasDxs =  new tiposAyudasDxHCEDTO();
			
			$sql = "SELECT
						Codigo,Descripcion,Arc_HL7,Programa,Formato
					FROM
						hceidc_000015
					WHERE
						codigo = '{$info['Tipoestudio']}'
					";
			
			$resTAdx = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			$numTAdx = mysql_num_rows($resTAdx);
	
			if( $rowsTAdx = mysql_fetch_array( $resTAdx ) ){
				
				$detalle->tipoAyudasDxs->codigo = $rowsTAdx['Codigo']; 
				$detalle->tipoAyudasDxs->descripcion = $rowsTAdx['Descripcion'];
				$detalle->tipoAyudasDxs->arc_HL7 = $rowsTAdx['Arc_HL7'];
				$detalle->tipoAyudasDxs->programa = $rowsTAdx['Programa'];
				$detalle->tipoAyudasDxs->formato = $rowsTAdx['Formato'];
				
				@$datosAdicionales[ $detalle->tipoDeOrden ][ $detalle->numeroDeOrden ]['Programa'] = $rowsTAdx['Programa']; 
			}
			
			//Fin de tipos de ayudas
			
			//Busco si el paciente tiene examenes anteriores
			$sql = "SELECT
						*
					FROM
						hceidc_000027, hceidc_000028
					WHERE
						Ordhis =  '$historia' 
						AND Ording = '$ingreso'
						AND Ordtor = '{$info['Dettor']}'
						AND Ordnro = Detnro
						AND Dettor = Ordtor
						AND Detesi NOT IN ('P','Pendiente')
						AND Detest = 'on'
					";
			
			$resAnt = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			$numAnt = mysql_num_rows($resAnt);
			
			if( $numAnt > 0 ){
				$detalle->tieneAnteriores = true;
			}
			else{
				$detalle->tieneAnteriores = false;
			}
			//Fin de Buscar

			$coleccion[] = $detalle;
		}
	}
	return $coleccion;
}


function cargarInfusionesATemporal($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000062
		   	(Medico,Fecha_data,hora_data,Inkhis,Inking,Inkcon,Inkdes,Inkfec,Inkobs,Inkfes,seguridad)
		SELECT
			 Medico,Fecha_data,hora_data,Inkhis,Inking,Inkcon,Inkdes,Inkfec,Inkobs,Inkfes,seguridad
		FROM
			".$wbasedato."_000051
		WHERE
			Inkhis = '$historia'
			AND Inking = '$ingreso'
			AND Inkfec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Borro
	$q = "DELETE FROM
			".$wbasedato."_000051
		WHERE
			Inkhis = '$historia'
			AND Inking = '$ingreso'
			AND Inkfec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function cargarInfusionesAnteriorATemporal($historia,$ingreso,$fecha,$fechaGrabacion){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000062
		   	(Medico,Fecha_data,hora_data,Inkhis,Inking,Inkcon,Inkdes,Inkfec,Inkobs,Inkfes,seguridad)
		SELECT
			 Medico,Fecha_data,hora_data,Inkhis,Inking,Inkcon,Inkdes,'$fechaGrabacion',Inkobs,Inkfes,seguridad
		FROM
			".$wbasedato."_000051
		WHERE
			Inkhis = '$historia'
			AND Inking = '$ingreso'
			AND Inkfec = DATE_SUB('".$fecha."',INTERVAL 1 DAY)";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function consultarExamenesLaboratorioTemporalKardex($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Ekacod,Cconom,Ekaest,Ekaobs,Ekafes,Ekajus 
		FROM 
			".$wbasedato."_000061, ".$wbasedato."_000011
		WHERE
			Ekahis = '$historia'
			AND Ekaing = '$ingreso'
			AND Ekafec = '$fecha'
			AND Ekacod = Ccocod";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$detalle = new ExamenKardexDTO();

			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			$detalle->estado = @$info['Ekaest'];
			$detalle->codigoExamen = @$info['Ekacod'];
			$detalle->fechaDeSolicitado = @$info['Ekafes'];
			$detalle->descripcionExamen = @$info['Cconom'];
			$detalle->observaciones = @$info['Ekaobs'];
			$detalle->justificacion = @$info['Ekajus'];
				
			$coleccion[] = $detalle;
		}
	}
	return $coleccion;
}

/************************************************************************************************************************************************
 * Inserta los campos de la temporal del kardex (tabla 60) a la tabla definitiva del kardex(54) y borra los datos de la temporal.
 * segun la historia e ingreso de un paciente
 * 
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $esPrimerKardex
 * @return unknown_type
 ************************************************************************************************************************************************/
function cargarArticulosADefinitivo( $historia, $ingreso, $fecha, $esPrimerKardex ){
	global $wbasedato;
	global $conex;
	global $usuario;		//Información de usuario

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $centroCostosCentralMezclas;

	$horasFrecuencia = 0;

	/****************************************************************
	 * Junio 14 de 2012
	 ****************************************************************/
	$valCooquery = "";
	if( $usuario->centroCostosGrabacion != '*' ){
		$valCooquery = " AND Kadcco = '$usuario->centroCostosGrabacion' ";
	}
	/****************************************************************/
	
	$q = "SELECT
			Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,seguridad,Kadreg,Kadron,Kadcpx,Kadfro,Kadaan,Kadcda,Kadcdt,Kadusu,Kadfir,Kadnar,Kaddan,Kadfum,Kadhum,Kadido,Kadfra,Kadfcf,Kadhcf,Kadimp,Kadalt,Kadcal,Kadint,Kadonc,Kadpos,Kadupo
		FROM
			{$wbasedato}_000060
		WHERE
			Kadfec = '$fecha'
			AND Kadhis = '$historia'
			AND Kading = '$ingreso'
			$valCooquery";
			
	elementosGrabados( $conex, $articulos, $q );

	//Coleccion de horas de frecuencias
	$colFrecuencias = consultarPeriodicidades();

	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res1);

	if ($num > 0){
		$cont1 = 0;

		while ($cont1 < $num ){
			$cont1++;
			
			if( true ){
	
				$info = mysql_fetch_array($res1);
	
				//La solución a esto es consultar las fracciones, si no tiene fracciones se usan dias de estabilidad cero (no tiene)
				$tarti = $centroCostosCentralMezclas;
				if($info['Kadori'] == $codigoServicioFarmaceutico){
					$tarti = $centroCostosServicioFarmaceutico;
				}
	
				$qf = "SELECT
							Defart,Defdup, Defdis, Defcco, Defdie
						FROM
							{$wbasedato}_000059
						WHERE
							Defart = '{$info['Kadart']}'
							AND Defcco = '$tarti'
							AND Defest = 'on'";
	
				$respin = mysql_query($qf, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qf . " - " . mysql_error());
				$numf = mysql_num_rows($respin);
	
				$diasEstabilidad = 0;
				$dispensable = "on";
	
				while($infofr = mysql_fetch_array($respin)){
					$diasEstabilidad 	= $infofr['Defdie'];
					$dispensable 		= $infofr['Defdis'];
				}
	
				//Frecuencia
				foreach ($colFrecuencias as $frecuencia){
					if($frecuencia->codigo == $info['Kadper']){
						$horasFrecuencia = $frecuencia->equivalencia;
						break;
					}
				}
	
				//Calculo del saldo actual con base en el anterior
				$saldo = 0;
				$cantGrabar = 0;
				$cantDispensar = 0;
				$cantidadManejo = 0;
	
				//$ccoOrigen,$diasEstabilidad,$diasTtoAcumulados,
	
				//Calculo de los dias de tratamiento actuales, Sumo 1 dia por el registro del dia actual que esta en temporal
				$diasTtoAcumulados = 0;
				$dosisTtoTotales = 0;
	
				obtenerDatosAdicionalesArticulo($info['Kadhis'],$info['Kading'],$info['Kadart'],$diasTtoAcumulados,$dosisTtoTotales);
	
				//Calculo de las cantidades por articulo
				$horasAplicacionDia = "";
				calcularSaldoActual( $conex,$wbasedato,$info['Kadhis'],$info['Kading'],$fecha,$info['Kadart'],$info['Kadfin'],$info['Kadhin'],$info['Kadcfr'],$horasFrecuencia,$info['Kaddma'],$info['Kadori'],$diasTtoAcumulados+1,$cantGrabar,$saldo,$cantDispensar,$cantidadManejo,$info['Kadsad'],$info['Kadpro'],$horasAplicacionDia, $info['Kaddia'], $info['Kadreg'] );
				//calcularSaldoActual($conex,$wbasedato,$info['Kadhis'],$info['Kading'],$fecha,$info['Kadart'],$info['Kadfin'],$info['Kadhin'],$info['Kadcfr'],$horasFrecuencia,$info['Kaddma'],$info['Kadori'],$diasTtoAcumulados+1,&$cantGrabar,&$saldo,&$cantDispensar,&$cantidadManejo,$info['Kadsad'],$info['Kadpro'],$info['Kadcpx'], $info['Kadreg'] );
				
				//No dispensable, saldos en cero
				if($dispensable == 'off' || $info['Kadess'] == 'on'){
					$cantDispensar = 0;
					$cantGrabar = 0;
					$saldo = 0;
				}
	
				$info['Kadobs'] = str_replace( "\\", "\\\\", $info['Kadobs'] ); //Marzo 3 de 2011
				$info['Kadobs'] = str_replace( "'", "\'", $info['Kadobs'] );	//Marzo 3 de 2011
				
				$q = "INSERT INTO ".$wbasedato."_000054
			   				(Medico,Fecha_data,hora_data,Kadhis,Kading,Kadart,Kadcfr,Kadufr,Kaddia,Kadest,Kadess,Kadper,Kadffa,Kadfin,Kadhin,Kadvia,Kadfec,Kadcon,Kadobs,Kadori,Kadsus,Kadcnd,Kaddma,Kadcan,Kaddis,Kaduma,Kadcma,Kadhdi,Kadsal,Kadcdi,Kadpri,Kadpro,Kadcco,Kadare,Kadsad,Kadreg,Kadcpx,Kadron,Kadfro,Kadaan,Kadcda,Kadcdt,Kadusu,Kadfir,Kadnar,Kaddan,Kadfum,Kadhum,Kadido,Kadfra,Kadfcf,Kadhcf,Kadimp,Kadalt,Kadcal,Kadint,Kadonc,Kadpos,Kadupo,seguridad)
			   			VALUES
			   			   ('{$info['Medico']}','{$info['Fecha_data']}','{$info['hora_data']}','{$info['Kadhis']}',
			   				'{$info['Kading']}','{$info['Kadart']}','{$info['Kadcfr']}','{$info['Kadufr']}','{$info['Kaddia']}','{$info['Kadest']}','{$info['Kadess']}','{$info['Kadper']}','{$info['Kadffa']}',
			   				'{$info['Kadfin']}','{$info['Kadhin']}','{$info['Kadvia']}','{$fecha}','{$info['Kadcon']}','{$info['Kadobs']}','{$info['Kadori']}','{$info['Kadsus']}','{$info['Kadcnd']}',
			   				'{$info['Kaddma']}','{$cantGrabar}','{$info['Kaddis']}','{$info['Kaduma']}','{$cantidadManejo}','{$info['Kadhdi']}','{$saldo}','{$cantDispensar}','{$info['Kadpri']}',
			   				'{$info['Kadpro']}','{$info['Kadcco']}','{$info['Kadare']}','{$info['Kadsad']}','{$info['Kadreg']}','{$info['Kadcpx']}','{$info['Kadron']}','{$info['Kadfro']}','{$info['Kadaan']}','{$info['Kadcda']}','{$info['Kadcdt']}','{$info['Kadusu']}','{$info['Kadfir']}','{$info['Kadnar']}','{$info['Kaddan']}','{$info['Kadfum']}','{$info['Kadhum']}','{$info['Kadido']}','{$info['Kadfra']}','{$info['Kadfcf']}','{$info['Kadhcf']}','{$info['Kadimp']}','{$info['Kadalt']}','{$info['Kadcal']}','{$info['Kadint']}','{$info['Kadonc']}','{$info['Kadpos']}','{$info['Kadupo']}','{$info['seguridad']}')";

				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
	}
	
	/************************************************************************************************************************************
	 * Mayo 20 de 2011
	 * 
	 * Borro los registros que efectivamente pasaron a la tabla definitiva del kardex(000054) desde la tabla temporal
	 ************************************************************************************************************************************/
	$malos = verificacionArticulosGuardados( $conex, $wbasedato, "000054", $articulos, "Cargando articulos a definitivo" );
	
	if( count( $articulos ) > 0 ){
		
		foreach( $articulos as $key => $value ){
	
			if( !isset( $malos[$key] ) ){
				
				$q = "DELETE FROM
						".$wbasedato."_000060
					WHERE
						Kadhis = '$historia'
						AND Kading = '$ingreso'
						AND Kadart = '{$value['Kadart']}'
						AND Kadfec = '$fecha'
						AND Kadfin = '{$value['Kadfin']}'
						AND Kadhin = '{$value['Kadhin']}'";
					
				$resDel = mysql_query( $q, $conex ) or die( mysql_errno()."- Error en el query $sql - ".mysql_error() );
			}
		}
	}
	/************************************************************************************************************************************/

	//Eliminar de la tabla temporal los registros anteriores
//	$q = "DELETE FROM
//			".$wbasedato."_000060
//		WHERE
//			Kadhis = '$historia'
//			AND Kading = '$ingreso'
//			AND Kadfec = '$fecha'
//			AND Kadcco = '$usuario->centroCostosGrabacion'";
//
//	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}
 
function cargarExamenesADefinitivo($historia,$ingreso,$fecha,$firmaDigital){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000050
		   	(Medico,Fecha_data,hora_data,Ekahis,Ekaing,Ekacod,Ekaest,Ekafec,Ekaobs,Ekafes,Ekajus,Ekausu,Ekafir,seguridad)
		SELECT 
			 Medico,Fecha_data,hora_data,Ekahis,Ekaing,Ekacod,Ekaest,Ekafec,Ekaobs,Ekafes,Ekajus,Ekausu,Ekafir,seguridad
		FROM 
			".$wbasedato."_000061
		WHERE
			Ekahis = '$historia'
			AND Ekaing = '$ingreso'
			AND Ekafec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Eliminar de la tabla temporal los registros anteriores
	$q = "DELETE FROM
			".$wbasedato."_000061
		WHERE
			Ekahis = '$historia'
			AND Ekaing = '$ingreso'
			AND Ekafec <= '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//HCE
	$numIns = mysql_affected_rows();
	if($numIns > 0 && $firmaDigital != ''){
		$q = "UPDATE
				hceidc_000027
			SET 
				Ordfir = '$firmaDigital'
			WHERE
				Ordhis = '$historia'
				AND Ording = '$ingreso'
				AND fecha_data = '$fecha'";

//		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

function cargarInfusionesADefinitivo($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000051
		   	(Medico,Fecha_data,hora_data,Inkhis,Inking,Inkcon,Inkdes,Inkfec,Inkobs,Inkfes,seguridad)
		SELECT 
			 Medico,Fecha_data,hora_data,Inkhis,Inking,Inkcon,Inkdes,Inkfec,Inkobs,Inkfes,seguridad 
		FROM 
			".$wbasedato."_000062
		WHERE 
			Inkhis = '$historia'
			AND Inking = '$ingreso'
			AND Inkfec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Eliminar de la tabla temporal los registros anteriores
	$q = "DELETE FROM
			".$wbasedato."_000062
		WHERE 
			Inkhis = '$historia'
			AND Inking = '$ingreso'
			AND Inkfec <= '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function cargarMedicoADefinitivo($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000047
		   	(Medico,Fecha_data,hora_data,Mettdo,Metdoc,Methis,Meting,Metfek,Metest,Metint,Metesp,Metusu,Metfir,seguridad)
		SELECT 
			 Medico,Fecha_data,hora_data,Mettdo,Metdoc,Methis,Meting,Metfek,Metest,Metint,Metesp,Metusu,Metfir,seguridad
		FROM 
			".$wbasedato."_000063
		WHERE 
			Methis = '$historia'
			AND Meting = '$ingreso'
			AND Metfek = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Eliminar de la tabla temporal los registros anteriores
	$q = "DELETE FROM
			".$wbasedato."_000063
		WHERE 
			Methis = '$historia'
			AND Meting = '$ingreso'
			AND Metfek <= '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
}

function cargarDietasADefinitivo($historia,$ingreso,$fecha,$firma=''){
	global $wbasedato;
	global $conex;
	global $usuario;
	
	//actualizo firma
	if( $firma != '' ){
		$sql = "UPDATE
					{$wbasedato}_000064
				SET
					Dikfir = '$firma'
				WHERE
					Dikhis = '$historia'
					AND Diking = '$ingreso'
					AND Dikfec = '$fecha'
					AND Dikusu = '$usuario->codigo' 
				";
					
		$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	}

	$q = "INSERT INTO ".$wbasedato."_000052
		   	(Medico,Fecha_data,hora_data,Dikcod,Dikhis,Diking,Dikfec,Dikest,Dikusu,Dikfir,seguridad)
		SELECT 
			 Medico,Fecha_data,hora_data,Dikcod,Dikhis,Diking,Dikfec,Dikest,Dikusu,Dikfir,seguridad
		FROM 
			".$wbasedato."_000064
		WHERE 
			Dikhis = '$historia'
			AND Diking = '$ingreso'
			AND Dikfec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Eliminar de la tabla temporal los registros anteriores
	$q = "DELETE FROM
			".$wbasedato."_000064
		WHERE 
			Dikhis = '$historia'
			AND Diking = '$ingreso'
			AND Dikfec <= '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
}

function cargarEsquemaDextrometer($historia, $ingreso, $fechaAnterior, $fechaActual){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	//Infhis  Infing  Inffec  Infade  Inffde  Infcde
	$q = "SELECT
			Medico,Fecha_data,Hora_data,Infade,Inffde,Infcde,Seguridad
		FROM
			".$wbasedato."_000070
		WHERE 
			Infhis = '$historia'
			AND Infing = '$ingreso'
			AND Inffec = '$fechaActual'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num == 0)
	{
		//Infhis  Infing  Inffec  Infade  Inffde  Infcde
		$q = "SELECT
				Medico,Fecha_data,Hora_data,Infade,Inffde,Infcde,Seguridad
			FROM
				".$wbasedato."_000070
			WHERE 
				Infhis = '$historia'
				AND Infing = '$ingreso'
				AND Inffec = '$fechaAnterior'";


		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$info = mysql_fetch_array($res);

		if($info['Infade'] != ''){
			$q = "INSERT INTO ".$wbasedato."_000070
		   				(Medico,Fecha_data,Hora_data,Infhis,Infing,Inffec,Infade,Inffde,Infcde,Seguridad)
		   			VALUES
		   			   ('{$info['Medico']}','{$info['Fecha_data']}','{$info['Hora_data']}','{$historia}','{$ingreso}','{$fechaActual}','{$info['Infade']}','{$info['Inffde']}','{$info['Infcde']}','{$info['Seguridad']}')";

			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}

	//Carga de los intervalos dextrometer
	//Medico  Fecha_data  Hora_data  Indhis  Inding  Indfec  Indime  Indima  Inddos  Indudo  Indobs  Indvia  Seguridad
	$q = "SELECT
			Indhis,Inding,Indfec,Indime
		FROM
			".$wbasedato."_000071
		WHERE
			Indhis = '$historia'
			AND Inding = '$ingreso'
			AND Indfec = '$fechaActual'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num == 0){
		$q = "INSERT INTO ".$wbasedato."_000071
			   	(Medico,Fecha_data,Hora_data,Indhis,Inding,Indfec,Indime,Indima,Inddos,Indudo,Indobs,Indvia,Indusu,Indfir,Seguridad)
			SELECT
				Medico,Fecha_data,Hora_data,Indhis,Inding,'{$fechaActual}',Indime,Indima,Inddos,Indudo,Indobs,Indvia,Indusu,Indfir,Seguridad
			FROM
				".$wbasedato."_000071
			WHERE
				Indhis = '$historia'
				AND Inding = '$ingreso'
				AND Indfec = '$fechaAnterior'
				AND Indime IS NOT NULL
				AND Indime != '' AND Indime != ' '";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

function consultarExamenesLaboratorioDefinitivoKardex($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Ekacod,Cconom,Ekaest,Ekaobs,Ekafes,Ekajus
		FROM 
			".$wbasedato."_000050, ".$wbasedato."_000011
		WHERE
			Ekahis = '$historia'
			AND Ekaing = '$ingreso'
			AND Ekafec = '$fecha'
			AND Ekacod = Ccocod";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$detalle = new ExamenKardexDTO();

			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $fecha;
			$detalle->estado = $info['Ekaest'];
			$detalle->codigoExamen = $info['Ekacod'];
			$detalle->fechaDeSolicitado = $info['Ekafes'];
			$detalle->descripcionExamen = $info['Cconom'];
			$detalle->observaciones = $info['Ekaobs'];
			$detalle->justificacion = @$info['Ekajus'];
				
			$coleccion[] = $detalle;
		}
	}
	return $coleccion;
}

/**
 * Consulta los examenes anteriores realizados al paciente
 *
 * @param unknown_type $conexion
 * @param unknown_type $historia
 * @param unknown_type $ingreso
 * @param unknown_type $fecha
 * @return unknown
 */
function consultarExamenesLaboratorioAnteriorKardex($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT * FROM (
			SELECT 
				Ekacod,Cconom,Ekaest,Ekaobs,Ekafes,Ekafec,Ekajus 
		FROM 
			".$wbasedato."_000050, ".$wbasedato."_000011
		WHERE 
			Ekahis = '$historia'
			AND Ekaing = '$ingreso'
			AND Ekafec < '$fecha'
			AND Ekacod = Ccocod
		ORDER BY Ekafec) A,
		(
			SELECT 
				Ekafec,COUNT(*) cntDia 
			FROM 
				".$wbasedato."_000050 cnExa 
			WHERE 
				cnExa.Ekahis = Ekahis 
				AND cnExa.Ekaing = Ekaing 
				AND cnExa.Ekafec = Ekafec 
			GROUP BY Ekafec
		) B
		WHERE A.Ekafec = B.Ekafec";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;
				
			$detalle = new ExamenKardexDTO();
				
			$info = mysql_fetch_array($res);

			$detalle->historia = $historia;
			$detalle->ingreso = $ingreso;
			$detalle->fecha = $info['Ekafec'];
			$detalle->estado = $info['Ekaest'];
			$detalle->codigoExamen = $info['Ekacod'];
			$detalle->fechaDeSolicitado = $info['Ekafes'];
			$detalle->descripcionExamen = $info['Cconom'];
			$detalle->observaciones = $info['Ekaobs'];
			$detalle->campoAuxiliar = $info['cntDia'];
			$detalle->justificacion = $info['Ekajus'];
				
			$coleccion[] = $detalle;
		}
	}
	return $coleccion;
}

/**
 * Consulta del listado de examenes anteriores por HCE
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @return unknown_type
 */
function consultarExamenesAnteriorHCE($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	/************************************************************************************************
	 * Agosto 6 de 2018
	 * Esta función no está en uso y no se debe usar los queries se deben corregir
	 * Que se encuentran en esta función son incorrectos
	 ************************************************************************************************/
	return $coleccion = array();

	// $q = "SELECT * FROM (
			// SELECT 
			 // Ordtor,Ordnro,Ordfec,Ordhor,Ordobs,Detcod,Detesi,Detfec,Detrdo,Detest,Detjus,Cconom,Descripcion,Ordusu,Detusu
		// FROM 
			// hceidc_000028, hceidc_000017, hceidc_000027 LEFT JOIN ".$wbasedato."_000011 ON Ordtor = Ccocod
		// WHERE 
			// Ordhis = '$historia'
			// AND Ording = '$ingreso'
			// AND Ordfec < '$fecha'
			// AND Ordtor = Dettor
			// AND Codigo = Detcod
			// AND Detnro = Ordnro
		// ORDER BY Ordfec,Cconom,Ordtor,Ordnro,Detfec) A,
		// (
			// SELECT 
				// Ordfec,COUNT(*) cntDia 
			// FROM 
				// hceidc_000027 cnExa 
			// WHERE 
				// cnExa.Ordhis = Ordhis 
				// AND cnExa.Ording = Ording 
				// AND cnExa.Ordtor = Ordtor
				// AND cnExa.Ordnro = Ordnro
				// AND cnExa.Ordfec = Ordfec 
			// GROUP BY Ordfec
		// ) B
		// WHERE A.Ordfec = B.Ordfec";
		
	// $q = "SELECT * FROM (
			// SELECT 
			 // Ordtor,Ordnro,Ordfec,Ordhor,Ordobs,Detcod,Detesi,Detfec,Detrdo,Detest,Detjus,Cconom,Descripcion,Ordusu,Detusu, NoPos
		// FROM 
			// hceidc_000028, hceidc_000047, hceidc_000027 LEFT JOIN ".$wbasedato."_000011 ON Ordtor = Ccocod
		// WHERE 
			// Ordhis = '$historia'
			// AND Ording = '$ingreso'
			// AND Ordfec < '$fecha'
			// AND Ordtor = Dettor
			// AND Codigo = Detcod
			// AND Detnro = Ordnro
		// ORDER BY Ordfec,Cconom,Ordtor,Ordnro,Detfec) A,
		// (
			// SELECT 
				// Ordfec,COUNT(*) cntDia 
			// FROM 
				// hceidc_000027 cnExa 
			// WHERE 
				// cnExa.Ordhis = Ordhis 
				// AND cnExa.Ording = Ording 
				// AND cnExa.Ordtor = Ordtor
				// AND cnExa.Ordnro = Ordnro
				// AND cnExa.Ordfec = Ordfec 
			// GROUP BY Ordfec
		// ) B
		// WHERE A.Ordfec = B.Ordfec";

	// $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	// $num = mysql_num_rows($res);

	// if ($num > 0){
		// $cont1 = 0;
		// while ($cont1 < $num){
			// $cont1++;

			// $detalle = new ExamenHCEDTO();
				
			// $info = mysql_fetch_array($res);
				
			// $detalle->historia = @$historia;
			// $detalle->ingreso = @$ingreso;
			// $detalle->fecha = @$info['Ordfec'];
			// $detalle->hora = @$info['Ordhor'];
			// $detalle->tipoDeOrden = @$info['Ordtor'];
			// $detalle->numeroDeOrden = @$info['Ordnro'];
			// $detalle->observacionesOrden = @$info['Ordobs'];
			// $detalle->estadoOrden = @$info['Ordesp'];
			// $detalle->estadoRegistroOrden = @$info['Ordest'];
			// $detalle->firmaOrden = @$info['Ordfir'];
			// $detalle->fechaARealizar = @$info['Detfec'];

			// $detalle->nombreCentroCostos = @$info['Cconom'];
			
			// if($detalle->tipoDeOrden == "H"){
				// $detalle->nombreCentroCostos = "Hospitalario";
			// }
			
			// $detalle->nombreExamen = @$info['Descripcion'];

			// $detalle->codigoExamen = @$info['Detcod'];
			// $detalle->estadoExamen = @$info['Detesi'];
			// $detalle->resultadoExamen = @$info['Detrdo'];
			// $detalle->estadoRegistroExamen = @$info['Detest'];
			// $detalle->justificacion = @$info['Detjus'];
			
			// $detalle->creadorOrden = $info[ 'Ordusu' ];
			// $detalle->creadorItem = $info[ 'Detusu' ];
			
			// $detalle->esPos = $info['NoPos'] == 'on' ? false: true;

			// $coleccion[] = $detalle;
		// }
	// }
	// return $coleccion;
}

function consultarInfusionesTemporalKardex($historia,$ingreso,$fecha) {
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Id,Inkcon,Inkdes,Inkobs,Inkfes
		FROM
			".$wbasedato."_000062
		WHERE
			Inkhis = '$historia'
			AND Inking = '$ingreso'
			AND Inkfec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$detalle = new RegistroGenericoDTO();

			$info = mysql_fetch_array($res);

			$detalle->codigo 		= $info['Inkcon'];
			$detalle->descripcion 	= $info['Inkdes'];
			$detalle->observacion 	= $info['Inkobs'];
			$detalle->fecha 		= $info['Inkfes'];

			$coleccion[] = $detalle;
		}
	}
	return $coleccion;
}

function consultarInfusionesDefinitivoKardex($historia,$ingreso,$fecha) {
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Id,Inkcon,Inkdes,Inkobs,Inkfes
		FROM
			".$wbasedato."_000051
		WHERE
			Inkhis = '$historia'
			AND Inking = '$ingreso'
			AND Inkfec = '$fecha'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$detalle = new RegistroGenericoDTO();

			$info = mysql_fetch_array($res);

			$detalle->codigo 		= $info['Inkcon'];
			$detalle->descripcion 	= $info['Inkdes'];
			$detalle->observacion 	= $info['Inkobs'];
			$detalle->fecha 		= $info['Inkfes'];

			$coleccion[] = $detalle;
		}
	}
	return $coleccion;
}

function consultarComponentesPorCodigo($wbasedato,$codigo,$tipoMedicamento,$unidadMedida){
	$conexion = obtenerConexionBD("matrix");

	global $codigoServicioFarmaceutico;

	$q = "SELECT
				artcod, artcom, artuni, unides, '$codigoServicioFarmaceutico' origen
			FROM
				".$wbasedato."_000026, ".$wbasedato."_000027
			WHERE
				artuni = unicod
				AND artcod LIKE '%".$codigo."%'
				AND Artuni LIKE '$unidadMedida'
				AND artest = 'on'
				AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'L')
			ORDER BY Artcom
			LIMIT 100
		";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	$consulta = "";

	if($num > 0){
		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);

			$referencia = "javascript:seleccionarComponente('".$rs['artcod']."','".str_replace(" ","_",htmlentities(trim($rs[1])))."');";
			$consulta = $consulta." * <a href='#null' onClick=$referencia>".htmlentities(trim($rs[1])).'</a><br/>';
			$cont1++;
		}
	} else {
		$consulta = $consulta."<b>No se encontraron coincidencias</b>";
	}

	liberarConexionBD($conexion);

	return $consulta;
}

function consultarAlergiasDiagnosticosAnteriores($historia,&$alergiasAnteriores,&$diagnosticosAnteriores){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Fecha_data, Karhis, Karing, Karale, Kardia
		FROM
			".$wbasedato."_000053
		WHERE
			Karhis = '$historia'
		ORDER BY
			Fecha_data DESC";

	$aleAnt = "";
	$diagAnt = "";

	$acumuladorAlergias = "";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$info = mysql_fetch_array($res);

			if(isset($info['Karale']) && !empty($info['Karale'])){
				$pos = strpos($acumuladorAlergias, "'".$info['Karale']."'");
				if($pos===false){
					$acumuladorAlergias .= "'".$info['Karale']."'";
					$alergiasAnteriores .= "-".$info['Karale']."\n";
				}
			}

			if(isset($info['Kardia']) && $info['Kardia'] != "" && $diagAnt != $info['Kardia']){
				$diagnosticosAnteriores .= "-".$info['Kardia']." \n";
			}

			$aleAnt = $info['Karale'];
			$diagAnt = $info['Kardia'];
		}
	}
}

/*
 * AJAX::Consulta medicamentos por codigo
 */
//function consultarMedicamentosPorCodigo($wbasedato,$codigo,$tipoMedicamento,$unidadMedida,$centroCostos,$gruposMedicamentos,$tipoProtocolo){
function consultarMedicamentosPorCodigo($wbasedato,$codigo,$tipoMedicamento,$unidadMedida,$centroCostos,$gruposMedicamentos,$tipoProtocolo, $ccoPaciente = ''){
	global $conex;
	global $wcenpro;

	$coleccion = array();
	$consulta = "";

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	$esSF = $centroCostos == $centroCostosServicioFarmaceutico ? true : false;
	$esCM = $centroCostos == $centroCostosCentralMezclas ? true : false;

	$codigo = str_replace("-","%",$codigo);

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	$gruposIncluidos = "(";

	$q6 = "SELECT DISTINCT Ccogka FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccogka != '*' AND Ccocod='$centroCostos';";
	$res6 = mysql_query($q6, $conex);

	while($rs6 = mysql_fetch_array($res6)){
		$tieneGruposIncluidos = true;
		if(strpos($rs6['Ccogka'],$gruposIncluidos) === false){
			$gruposIncluidos .= "'".str_replace(",","','",$rs6['Ccogka'])."',";
		}
	}
	$gruposIncluidos .= "'')";
	//********************************

	//Preproceso de los grupos de medicamentos.  De formato X00,Y00,Z00... a 'X00','Y00','Z00'
	$criterioGrupo = "";

	$vecGruposMedicamentos = explode(",",$gruposMedicamentos);

	$cont2 = 0;
	while($cont2 < count($vecGruposMedicamentos)){
		$criterioGrupo .= "'".$vecGruposMedicamentos[$cont2]."',";
		$cont2++;
	}
	$criterioGrupo .= "''";

	switch ($tipoProtocolo) {
		case 'A';
		case 'U';
		case 'Q':
			//Para cualquier servicio
			$q = "SELECT "
			."		Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
			."	FROM "
			."		{$wbasedato}_000068, {$wbasedato}_000026, {$wbasedato}_000059, {$wbasedato}_000027 "
			."	WHERE "
			."		Arktip = '$tipoProtocolo' "
			."		AND Arkest = 'on' "
			."		AND Arkcco = '$centroCostosServicioFarmaceutico' "
			."		AND Arkcod = Artcod "
			."		AND Artuni LIKE '$unidadMedida' "
			."		AND Artest = 'on' "
			."		AND Artuni = Unicod "
			."		AND artcod LIKE '%".$codigo."%' "
			."		AND Defart = Arkcod "
			."		AND Defest = 'on' ";
					if($tieneGruposIncluidos){
						$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
					}
					$q .= " AND Defcco = Arkcco ";
					if($gruposMedicamentos != "*"){
						$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
					} else {
						$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
					}

				$subConsulta = "SELECT "
							."	Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
							." FROM "
							."	{$wbasedato}_000068, {$wcenpro}_000002, {$wbasedato}_000059, {$wbasedato}_000027 "
							." WHERE "
							."	artuni = unicod "
							."	AND Arktip = '$tipoProtocolo' "
							."	AND Arkest = 'on' "
							."	AND Defart = Arkcod "
							."	AND Arkcco = Defcco "
							."	AND Artuni = Unicod "
							."	AND artcod LIKE '%".$codigo."%' "
							."	AND Artuni LIKE '$unidadMedida' "
							."	AND Defest = 'on' "
							."	AND Defcco = '$centroCostosCentralMezclas' "
							."	AND artest = 'on' "
							."	AND Defart = Artcod";


				/****
				 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
				 */
				if($esCM){
					$q = $subConsulta;
				} else {
					if(!$tieneGruposIncluidos){
						$q = $q." UNION ".$subConsulta;
					}
				}
				$q = $q." LIMIT 100";
			break;
		case 'N':
			//Si el usuario no pertenece a SF ni a CM se consultan todos
			$q = "SELECT "
			."	Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
			." FROM "
			."	{$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
			." WHERE "
			."	artuni = unicod "
			."	AND artcod LIKE '%".$codigo."%' "
			."	AND Artuni LIKE '$unidadMedida' "
			."	AND artcod = Defart "
			."	AND artest = 'on' "
			."	AND Defest = 'on' ";
				if($tieneGruposIncluidos){
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
				}
				$q .= " AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I' AND Arktip != 'T' AND Arktip != 'N') "
			."	AND Defcco = '$centroCostosServicioFarmaceutico' ";
			if($gruposMedicamentos != "*"){
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo))";
			} else {
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M')";
			}

			$subConsulta = "SELECT "
						   ."		Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
						   ."	FROM "
							."	{$wbasedato}_000027, {$wcenpro}_000002, {$wbasedato}_000059 "
							." WHERE "
							."	artuni = unicod "
							."	AND artcod LIKE '%".$codigo."%' "
							."	AND Artuni LIKE '$unidadMedida' "
							."	AND artcod = Defart "
							."	AND artest = 'on' "
							."	AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I' AND Arktip != 'T' AND Arktip != 'N') "
							."	AND Defest = 'on' "
							."	AND Defcco = '$centroCostosCentralMezclas'";

			/****
			 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
			 */
			if($esCM){
				$q = $subConsulta;
			} else {
				if(!$tieneGruposIncluidos){
					$q = $q." UNION ".$subConsulta;
				}
			}

			$q = $q." LIMIT 100";

			break;
		default:
			$q = "SELECT "
				." Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
			." FROM "
				." {$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
			." WHERE "
			."	artuni = unicod "
			."	AND artcod LIKE '%".$codigo."%' "
			."	AND Artuni LIKE '$unidadMedida' "
			."	AND artcod = Defart "
			."	AND artest = 'on' "
			."	AND Defest = 'on' ";
				if($tieneGruposIncluidos){
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
				}
				$q .= " AND Defcco = '$centroCostosServicioFarmaceutico' ";
			if($gruposMedicamentos != "*"){
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
			} else {
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
			}

			$subConsulta = "SELECT "
							."	Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
							." FROM "
								." {$wbasedato}_000027, {$wcenpro}_000002, {$wbasedato}_000059 "
							." WHERE "
							."  artuni = unicod "
							."	AND artcod LIKE '%".$codigo."%' "
							."	AND Artuni LIKE '$unidadMedida' "
							."	AND artcod = Defart "
							."	AND artest = 'on' "
							."	AND Defest = 'on' "
							."	AND Defcco = '$centroCostosCentralMezclas'";

			/****
			 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
			 */
			if($esCM){
				$q = $q." UNION ".$subConsulta;
			} else {
				if(!$tieneGruposIncluidos){
					$q = $q." UNION ".$subConsulta;
				}
			}
			$q = $q." LIMIT 100";
			break;
	}

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;

	if($num > 0){
		while ($cont1 < $num)
		{
			$puedeAgregar = true;

			$rs = mysql_fetch_array($res);
			$color = "red";

			$fftica = $rs['Artfar'];

			if($rs['origen'] == $codigoCentralMezclas){
				$color = "blue";
				$fftica = '00';
				if(!$esCM && !$esSF && substr(strtoupper($rs['Artcod']),0,1) == "J"){
					$puedeAgregar = false;
				}
			}

			if($puedeAgregar){
				
				$noEnviar = ( esStock( $conex, $wbasedato, $rs['Artcod'], $ccoPaciente ) == true )? 'on' : 'off';	//Abril 25 de 2011
				
				$referencia = "javascript:seleccionarMedicamento('".$rs['Artcod']."','".str_replace(" ","_",trim(htmlentities($rs[1])))."','".$rs['origen']."','".str_replace(" ","_",trim($rs['Artgru']))."','".str_replace(" ","_",trim($fftica))."','".str_replace(" ","_",trim($rs['Artuni']))."','".str_replace(" ","_",trim($rs['Artpos']))."','".str_replace(" ","_",trim($rs['Deffru']))."','".str_replace(" ","_",trim($rs['Deffra']))."','".str_replace(" ","_",trim($rs['Defven']))."','".str_replace(" ","_",trim($rs['Defdie']))."','".str_replace(" ","_",trim($rs['Defdis']))."','".str_replace(" ","_",trim($rs['Defdup']))."','".str_replace(" ","_",trim($rs['Defdim']))."','".str_replace(" ","_",trim($rs['Defdom']))."','".str_replace(" ","_",trim($rs['Defvia']))."','".$tipoProtocolo."','".$noEnviar."');";
				$consulta = $consulta."<font color=$color>*[".$rs['origen']."]<a href='#null' onclick=$referencia>".htmlentities($rs[1]).'</a></font><br/>';
			}

			$cont1++;
		}
	} else {
		$consulta = $consulta."<b>No se encontraron coincidencias</b>";
	}

	liberarConexionBD($conex);

	return $consulta;
}

function consultarComponentesPorNombre($wbasedato,$nombre,$tipoMedicamento,$unidadMedida){
	global $codigoServicioFarmaceutico;

	$conexion = obtenerConexionBD("matrix");

	if($tipoMedicamento == 'C'){
		$q = "SELECT
				artcod, artcom, artuni, unides, '$codigoServicioFarmaceutico' origen, Artfar
			FROM
				".$wbasedato."_000026, ".$wbasedato."_000027
			WHERE
				artuni = unicod
				AND artcom LIKE '%".$nombre."%'
				AND Artuni LIKE '$unidadMedida'
				AND artest = 'on'
				AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'L')
			ORDER BY Artcom
			LIMIT 100
		";
	} else {
		$q = "SELECT
				artcod, artgen, artuni, unides, '$codigoServicioFarmaceutico' origen, Artfar
			FROM
				".$wbasedato."_000026, ".$wbasedato."_000027
			WHERE
				artuni = unicod
				AND artgen LIKE '%".$nombre."%'
				AND Artuni LIKE '$unidadMedida'
				AND artest = 'on'
				AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN ('E00')
			ORDER BY artgen
			LIMIT 100
		";
	}

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$consulta = "";

	$cont1 = 0;

	if($num > 0){
		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);

			$referencia = "javascript:seleccionarComponente('".$rs['artcod']."','".str_replace(" ","_",htmlentities(trim($rs[1])))."');";
			$consulta = $consulta." * <a href='#null' onClick=$referencia>".htmlentities(trim($rs[1])).'</a><br/>';
			$cont1++;
		}
	} else {
		$consulta = $consulta."<b>No se encontraron coincidencias</b>";
	}

	liberarConexionBD($conexion);

	return $consulta;
}

/********************************************************************************************************
 * AJAX::Consulta medicamentos por nombre
 * 
 * Modificaciones
 * 
 * Abril 25 de 2011	Se agrega campo ccoPaciente
 ********************************************************************************************************/
//function consultarMedicamentosPorNombre($wbasedato,$nombre,$tipoMedicamento,$unidadMedida,$centroCostos,$gruposMedicamentos,$tipoProtocolo){
function consultarMedicamentosPorNombre($wbasedato,$nombre,$tipoMedicamento,$unidadMedida,$centroCostos,$gruposMedicamentos,$tipoProtocolo, $ccoPaciente = '' ){
	global $conex;
	global $wcenpro;

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;

	$esSF = $centroCostos == $centroCostosServicioFarmaceutico ? true : false;
	$esCM = $centroCostos == $centroCostosCentralMezclas ? true : false;

	$nombre = str_replace("-","%",$nombre);

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	$gruposIncluidos = "(";

	$q6 = "SELECT DISTINCT Ccogka FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccogka != '*' AND Ccocod='$centroCostos';";
	$res6 = mysql_query($q6, $conex);

	while($rs6 = mysql_fetch_array($res6)){
		$tieneGruposIncluidos = true;
		if(strpos($rs6['Ccogka'],$gruposIncluidos) === false){
			$gruposIncluidos .= "'".str_replace(",","','",$rs6['Ccogka'])."',";
		}
	}
	$gruposIncluidos .= "'')";
	//********************************

	//Preproceso de los grupos de medicamentos.  De formato X00,Y00,Z00... a 'X00','Y00','Z00'
	$criterioGrupo = "";

	$vecGruposMedicamentos = explode(",",$gruposMedicamentos);

	$cont2 = 0;
	while($cont2 < count($vecGruposMedicamentos)){
		$criterioGrupo .= "'".$vecGruposMedicamentos[$cont2]."',";
		$cont2++;
	}
	$criterioGrupo .= "''";

	/*De acuerdo al $tipoProtocolo tomo los articulos de las siguientes fuentes:
	 *
	 * N:  Normal, fuente maestro del servicio farmaceutico o de central de mezclas.
	 * A:  Analgesia, el articulo se agrega a los protocolos de analgesia (tipo especial de articulo A)
	 * U:  nUtricion, el articulo se agrega a los protocolos de nutricion (tipo especial de articulos U)
	 * Q:  Quimioterapia, el articulo se agrega a los protocolos de quimioterapia (tipo especial de articulos Q)
	 */
	switch ($tipoProtocolo) {
		case 'A';
		case 'U';
		case 'Q':
			$q = "SELECT
					Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
				FROM
				{$wbasedato}_000068, {$wbasedato}_000026, {$wbasedato}_000059, {$wbasedato}_000027
				WHERE
					Arktip = '$tipoProtocolo'
					AND Arkest = 'on'
					AND Arkcco = '$centroCostosServicioFarmaceutico'
					AND Arkcod = Artcod ";
				if($tieneGruposIncluidos){
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
				}
				$q .= " AND Artuni LIKE '$unidadMedida'
					AND Artest = 'on' ";
				if($tipoMedicamento == 'C'){
					$q = $q."AND artcom LIKE '%".$nombre."%' ";
				} else {
					$q = $q."AND artgen LIKE '%".$nombre."%' ";
				}
				$q = $q."AND Unicod = Artuni
					AND Defart = Arkcod
					AND Defest = 'on'
					AND Defcco = Arkcco ";
				if($gruposMedicamentos != "*"){
					$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
				} else {
					$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
				}
					
				$subConsulta = "SELECT
								Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
							FROM
							{$wbasedato}_000068, {$wcenpro}_000002, {$wbasedato}_000059, {$wbasedato}_000027
							WHERE
								artuni = unicod
								AND Arktip = '$tipoProtocolo'
								AND Arkest = 'on'
								AND Defart = Arkcod
								AND Arkcco = Defcco
								AND Artgen LIKE '%".$nombre."%'
								AND Artuni LIKE '$unidadMedida'
								AND Defest = 'on'
								AND Defcco = '$centroCostosCentralMezclas'
								AND artest = 'on'
								AND Defart = Artcod	";
							/****
							 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
							 */
							if($esCM){
								$q = $subConsulta;
							} else {
								if(!$tieneGruposIncluidos){
									$q = $q." UNION ".$subConsulta;
								}
							}
							$q = $q."LIMIT 100";
							break;
		case 'N':
			if($tipoMedicamento == 'C'){
				$q = "SELECT
							Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
						FROM
						{$wbasedato}_000026, {$wbasedato}_000059, {$wbasedato}_000027
						WHERE
							artuni = unicod
							AND artcom LIKE '%".$nombre."%'
							AND Artuni LIKE '$unidadMedida'
							AND Defest = 'on'
							AND Defcco = '$centroCostosServicioFarmaceutico'
							AND artest = 'on' ";
						if($tieneGruposIncluidos){
							$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
						}
						$q .= " AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I')
							AND Defart = Artcod ";
						if($gruposMedicamentos != "*"){
							$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
						} else {
							$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
						}
							
						$subConsulta = "SELECT
									Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
								FROM
									{$wcenpro}_000002, {$wbasedato}_000059, {$wbasedato}_000027
								WHERE
									artuni = unicod
									AND artcom LIKE '%".$nombre."%'
									AND Artuni LIKE '$unidadMedida'
									AND Defest = 'on'
									AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I')
									AND Defcco = '$centroCostosCentralMezclas'
									AND artest = 'on'
									AND Defart = Artcod";				

						/****
						 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
						 */
						if($esCM){
							$q = $subConsulta;
						} else {
							if(!$tieneGruposIncluidos){
								$q = $q." UNION ".$subConsulta;
							}
						}
						$q = $q. " LIMIT 100";
			} else {
				$q = "SELECT
							Artcod, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
						FROM
						{$wbasedato}_000026, {$wbasedato}_000059, {$wbasedato}_000027
						WHERE
							artuni = unicod
							AND artgen LIKE '%".$nombre."%'
							AND Artuni LIKE '$unidadMedida'
							AND Defart = Artcod
							AND artest = 'on' ";
						if($tieneGruposIncluidos){
							$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
						}
						$q .= " AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I')
							AND Defest = 'on'
							AND Defcco = '$centroCostosServicioFarmaceutico' ";
						if($gruposMedicamentos != "*"){
							$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo))";
						} else {
							$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
						}
							
						$subConsulta = "SELECT
									Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
								FROM
									{$wcenpro}_000002, {$wbasedato}_000059, {$wbasedato}_000027
								WHERE
									artuni = unicod
									AND artgen LIKE '%".$nombre."%'
									AND Artuni LIKE '$unidadMedida'
									AND Defest = 'on'
									AND Artcod NOT IN (SELECT Arkcod FROM {$wbasedato}_000068 WHERE Arkcod = Artcod AND Arkest = 'on' AND Arktip != 'I')
									AND Defcco = '$centroCostosCentralMezclas'
									AND artest = 'on'
									AND Defart = Artcod";
						/****
						 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
						 */
						if($esCM){
							$q = $subConsulta;
						} else {
							if(!$tieneGruposIncluidos){
								$q = $q." UNION ".$subConsulta;
							}
						}
						$q = $q. " LIMIT 100";
			}
			break;
		default:
			if($tipoMedicamento == 'C'){
				$q = "SELECT
					Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
				FROM 
				{$wbasedato}_000026, {$wbasedato}_000059, {$wbasedato}_000027
				WHERE
					artuni = unicod 
					AND artcom LIKE '%".$nombre."%'
					AND Artuni LIKE '$unidadMedida'
					AND Defest = 'on' 
					AND Defcco = '$centroCostosServicioFarmaceutico'
					AND artest = 'on' ";
				if($tieneGruposIncluidos){
					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
				}
				$q .= " AND Defart = Artcod ";
				if($gruposMedicamentos != "*"){
					$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
				} else {
					$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
				}

				$subConsulta = "SELECT
									Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
								FROM 
									{$wcenpro}_000002, {$wbasedato}_000059, {$wbasedato}_000027
								WHERE
									artuni = unicod 
									AND artcom LIKE '%".$nombre."%'
									AND Artuni LIKE '$unidadMedida'
									AND Defest = 'on' 
									AND Defcco = '$centroCostosCentralMezclas'
									AND artest = 'on'
									AND Defart = Artcod";

				/****
				 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
				 */
				if($esCM){
					$q = $subConsulta;
				} else {
					if(!$tieneGruposIncluidos){
						$q = $q." UNION ".$subConsulta;
					}
				}
				$q = $q. " LIMIT 100";
			} else {
				$q = "SELECT
				Artcod, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
			FROM 
			{$wbasedato}_000026, {$wbasedato}_000059, {$wbasedato}_000027
			WHERE
				artuni = unicod 
				AND artgen LIKE '%".$nombre."%'
				AND Artuni LIKE '$unidadMedida'
				AND Defart = Artcod
				AND artest = 'on'
				AND Defest = 'on' ";
			if($tieneGruposIncluidos){
				$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
			}
			$q .= " AND Defcco = '$centroCostosServicioFarmaceutico' ";
			if($gruposMedicamentos != "*"){
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
			} else {
				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
			}
				
			$subConsulta = "SELECT
								Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia
							FROM 
								{$wcenpro}_000002, {$wbasedato}_000059, {$wbasedato}_000027
							WHERE
								artuni = unicod 
								AND artgen LIKE '%".$nombre."%'
								AND Artuni LIKE '$unidadMedida'
								AND Defest = 'on' 
								AND Defcco = '$centroCostosCentralMezclas'
								AND artest = 'on'
								AND Defart = Artcod"; 
			/****
			 * Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
			 */
			if($esCM){
				$q = $subConsulta;
			} else {
				if(!$tieneGruposIncluidos){
					$q = $q." UNION ".$subConsulta;
				}
			}
			$q = $q. " LIMIT 100";
			}
			break;
	}

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$consulta = "";

	$cont1 = 0;

	if($num > 0){
		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);
				
			$fftica = $rs['Artfar'];
				
			$color = "red";
			if($rs['origen'] == $codigoCentralMezclas){
				$color = "blue";
				$fftica = "00";
			}
			
			$noEnviar = ( esStock( $conex, $wbasedato, $rs['Artcod'], $ccoPaciente ) == true )? 'on' : 'off';	//Abril 25 de 2011
				
			$referencia = "javascript:seleccionarMedicamento('".$rs['Artcod']."','".str_replace(" ","_",trim(htmlentities($rs[1])))."','".$rs['origen']."','".str_replace(" ","_",trim($rs['Artgru']))."','".str_replace(" ","_",trim($fftica))."','".trim($rs['Artuni'])."','".str_replace(" ","_",trim($rs['Artpos']))."','".str_replace(" ","_",trim($rs['Deffru']))."','".str_replace(" ","_",trim($rs['Deffra']))."','".str_replace(" ","_",trim($rs['Defven']))."','".str_replace(" ","_",trim($rs['Defdie']))."','".str_replace(" ","_",trim($rs['Defdis']))."','".str_replace(" ","_",trim($rs['Defdup']))."','".str_replace(" ","_",trim($rs['Defdim']))."','".str_replace(" ","_",trim($rs['Defdom']))."','".str_replace(" ","_",trim($rs['Defvia']))."','".$tipoProtocolo."','".$noEnviar."');";
				
			$consulta = $consulta."<font color=$color>*[".$rs['origen']."]<a href='#null' onclick=$referencia>".htmlentities($rs[1]).'</a></font><br/>';
			$cont1++;
		}
	} else {
		$consulta = $consulta."<b>No se encontraron coincidencias</b>";
	}

	liberarConexionBD($conex);

	return $consulta;
}

function consultarHistorialCambiosKardex($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Fecha_data,Hora_data,Kaudes,Kaumen,SUBSTRING(Seguridad FROM INSTR(Seguridad,'-')+1) codigoUsuario, Descripcion
		FROM
			".$wbasedato."_000055, usuarios
		WHERE
			Kauhis = '$historia'
			AND Kauing = '$ingreso'
			AND ".$wbasedato."_000055.Fecha_data <= '$fecha'
			AND Codigo = SUBSTRING(Seguridad FROM INSTR(Seguridad,'-')+1)
		ORDER BY
			Fecha_data DESC, Hora_data DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$cambio = new cambioKardexDTO();

			$info = mysql_fetch_array($res);

			$cambio->historia = $historia;
			$cambio->ingreso = $ingreso;
			$cambio->fecha = $info['Fecha_data'];
			$cambio->hora = $info['Hora_data'];
			$cambio->descripcion = $info['Kaudes'];
			$cambio->mensaje = $info['Kaumen'];
			$cambio->usuario = $info['codigoUsuario']." - ".$info['Descripcion'];

			$coleccion[] = $cambio;
		}
	}
	return $coleccion;
}

function consultarArchivosDia($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$clave = obtenerMensaje("MSJ_ARCHIVO_CARGADO");

	$q = "SELECT
			Fecha_data,Hora_data,Kaudes
		FROM
			".$wbasedato."_000055
		WHERE
			Kauhis = '$historia'
			AND Kauing = '$ingreso'
			AND ".$wbasedato."_000055.Fecha_data = '$fecha'
			AND Kaumen = '$clave'";


	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$cambio = new cambioKardexDTO();

			$info = mysql_fetch_array($res);

			$cambio->historia = $historia;
			$cambio->ingreso = $ingreso;
			$cambio->fecha = $info['Fecha_data'];
			$cambio->hora = $info['Hora_data'];
			$cambio->descripcion = $info['Kaudes'];

			$coleccion[] = $cambio;
		}
	}
	return $coleccion;
}

/**
 * AJAX: Insercion de medico tratante
 *
 * REGLAS:
 *
 * 1. Solamente se permite un medico responsable a la vez por ingreso un solo Mtrtra en on por ingreso
 * 2. Inicialmente el medico se asociará a través de la admision
 * 3. La asociacion/desasociacion se realiza a través de la marca Mtrest
 * 4. Los medicos asociados que no tengan Mtrtra en on SON TODOS interconsultantes
 *
 * @param unknown_type $wbasedato
 * @param unknown_type $tipoDocumento
 * @param unknown_type $numeroDocumento
 * @param unknown_type $historia
 * @param unknown_type $ingreso
 * @param unknown_type $usuario
 * @return unknown
 */
function insertarMedicoTratante($wbasedato,$tipoDocumento,$numeroDocumento,$historia,$ingreso,$usuario,$fecha,$idRegistro,$tratante,$codigoEspecialidad,$codigoMatrix){
	$conexion = obtenerConexionBD("matrix");
	$codigo = 1;

	$tieneMedicoResponsable = false;
	$medicoExiste = false;
	$medicoActivo = false;

	$medicos = array();

	//Consulta del medico por ingreso
	$q = "SELECT
			Mtrhis,Mtring,Mtrmed,Mtrest,Mtrtra
		FROM 
			hceidc_000022
		WHERE 
			Mtrhis = '$historia'
			AND Mtring = '$ingreso'";


	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	//Medicos tratantes todos
	while($rs = mysql_fetch_array($res)){
		$medicos[$rs['Mtrmed']]['estado'] 	= $rs['Mtrest'];
		$medicos[$rs['Mtrmed']]['tratante'] = $rs['Mtrtra'];

		//El registro del medico se encuentra asociado previamente
		if($codigoMatrix == $rs['Mtrmed']){
			$medicoExiste = true;
				
			if($rs['Mtrest'] == "on"){
				$medicoActivo = true;
			}
		}

		//Verificacion de existencia de responsable
		if($rs['Mtrest'] == 'on' && $rs['Mtrtra'] == 'on'){
			$tieneMedicoResponsable = true;
		}
	}

	//	echo $medicoExiste ? "si existe":"no existe";
	//	echo $tieneMedicoResponsable ? "si responsable":"no responsable";

	/********
	 * EVALUACION de todas las posibles combinaciones de medico tratante
	 */
	$caso = "";
	if($medicoExiste){
		if($medicoActivo){
			if($tratante == "on"){
				if(!$tieneMedicoResponsable){
					$caso = "1";
				}
			} else {
				$caso = "2";
			}
		} else {  //Medico inactivo
			if($tratante == "on"){
				if(!$tieneMedicoResponsable){
					$caso = "5";
				}
			} else {
				$caso ="4";
			}
		}
	} else { // NO Existe medico
		if($tratante == "on"){
			if(!$tieneMedicoResponsable){
				$caso = "3";
			}
		} else {
			$caso = "3";
		}
	}
	//	echo "Caso::::".$caso;

	switch ($caso){
		case '1':	//Medico tratante en on
			$q = "UPDATE
					hceidc_000022
				SET
					Mtrtra = 'on'
				WHERE 
					Mtrhis = '$historia'
					AND Mtring = '$ingreso'
					AND Mtrmed = '$codigoMatrix'";
			$codigo = 2;
			break;
		case '2':	//Ya existe un medico asociado
			$q="";
			$codigo = 1;
			break;
		case '3':	//Insertar medico
			$q = "INSERT INTO hceidc_000022(
				Medico,Fecha_data,Hora_data,Mtrhis,Mtring,Mtrmed,Mtrest,Mtrtra,Seguridad) VALUES
			('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$codigoMatrix."','on','$tratante','A-$usuario')";
				
			$audNuevo = "$codigoMatrix";
			$codigo = 2;
			break;
		case '4':	//Actualizar activo on y tratante off
			$q = "UPDATE
					hceidc_000022
				SET
					Mtrtra = 'off',
					Mtrest = 'on'
				WHERE 
					Mtrhis = '$historia'
					AND Mtring = '$ingreso'
					AND Mtrmed = '$codigoMatrix'";
			$codigo = 2;
			break;
		case '5':	//Actualizar activo en on y tratante en on
			$q = "UPDATE
					hceidc_000022
				SET
					Mtrtra = 'on',
					Mtrest = 'on'
				WHERE 
					Mtrhis = '$historia'
					AND Mtring = '$ingreso'
					AND Mtrmed = '$codigoMatrix'";
			$codigo = 2;
			break;
		default:
			//No pueden haber dos medicos tratantes al tiempo
			$codigo = 3;
			break;
	}

	if($q != ""){
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	//Selecciono el medico  Medico  Fecha_data  Hora_data  Mtrhis  Mtring  Mtrmed  Mtrest  Mtrtra  Seguridad
	$num2 = mysql_affected_rows();

	if($num2 == 0){
		$auditoria = new AuditoriaDTO();

		$auditoria->historia = $historia;
		$auditoria->ingreso = $ingreso;
		$auditoria->descripcion = $audNuevo;
		$auditoria->fechaKardex = $fecha;
		$auditoria->mensaje = obtenerMensaje('MSJ_MEDICO_ASOCIADO');
		$auditoria->seguridad = $usuario;

		registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

		liberarConexionBD($conexion);
		$codigo = 2;
	}
	return $codigo;
}


function grabarNuevoExamen($wemp_pmla,$basedatos,$nombre,$tipoServicio,$especialidad){
	global $conex;
	global $codigoAyudaHospitalaria;
	global $whce;
	global $user;
	
	list( $aaaa, $key ) = explode( "-", $user );

	$esHospitalario = "off";
	$consecutivo = "1";
	$auxConsecutivoAyudaHospitalaria = "1";

	$conExamenes = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConsecutivoExamenes');
	$conExamenes++;
	
	
	//Valido que el nombre no exista
	$q = "SELECT
			a.Codigo, a.Tipoestudio, b.Descripcion
		FROM
			{$whce}_000047 a, {$whce}_000015 b
		WHERE
			a.descripcion = '".$nombre."'
		AND a.Estado = 'on'
		AND tipoestudio = b.codigo
		UNION
		SELECT
			a.Codigo, a.Tipoestudio, b.Descripcion
		FROM
			{$whce}_000017 a, {$whce}_000015 b
		WHERE
			a.descripcion = '".$nombre."'
		AND a.Estado = 'on'
		AND tipoestudio = b.codigo
		;";
		
	$resExi = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$numExi = mysql_num_rows($resExi);
	
	if( $numExi == 0){
		// Se consulta si el consecutivo de examenes que nos da root_000051 no existe
		$q = "SELECT
				Codigo 
			FROM
				{$whce}_000017
			WHERE
				Codigo = '".$conExamenes."'
			AND Estado = 'on';";
		$resMax = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numMax = mysql_num_rows($resMax);
		
		// Si el consecutivo e examenes existe siga consultando hasta que encuentre uno que no exista
		if($numMax>0) 
		{
			do
			{
				$conExamenes++;
				$q = "SELECT
						Codigo 
					FROM
						{$whce}_000017
					WHERE
						Codigo = '".$conExamenes."'
					AND Estado = 'on';";
			
				$resMax = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$numMax = mysql_num_rows($resMax);

			} while( $numMax > 0);
		}

		// Consulto el servicio asociado a el tipo de estudio
		$q = "SELECT
				Servicio 
			FROM
				{$whce}_000017
			WHERE
				Tipoestudio = '".$tipoServicio."'
			AND Estado = 'on';";
		$resSer = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numSer = mysql_num_rows($resSer);	
		$rowSer = mysql_fetch_array($resSer);
		
		// Defino datos a guardar
		$medico = $whce; 
		$fecha_data = date('Y-m-d');
		$hora_data = date('H:i:s');
		$codigo = $conExamenes;
		$descripcion = strtoupper( utf8_decode( $nombre ) );
		$tipoestudio = $tipoServicio;
		$servicio = $rowSer['Servicio'];
		$servicio = $tipoestudio;
		$anatomia = '';
		$codcups = $conExamenes;
		$protocolo = '';
		$estado = 'on';
		$clase = '';
		$noPos = '';
		$nuevo = 'on';
		$seguridad = "C-$key";

		$q = "	INSERT INTO 
					{$whce}_000017
					(Medico,Fecha_data,Hora_data,Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Protocolo,Estado,Clase,NoPos,Nuevo,Seguridad)	
				VALUES
					('$medico','$fecha_data','$hora_data','$codigo','$descripcion','$servicio','$tipoestudio','$anatomia','$codcups','$protocolo','$estado','$clase','$noPos','$nuevo','$seguridad')";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		$q = "	UPDATE 
					root_000051
				SET
					Detval = '".$conExamenes."'
				WHERE 
					Detemp = '".$wemp_pmla."'
				AND Detapl = 'ConsecutivoExamenes' ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	else{
		$rowsExi = mysql_fetch_array( $resExi );
		$codigo = $rowsExi[ 'Codigo' ];
		
		//valido que sean del mismo tipo de estudio
		if($tipoServicio != $rowsExi[ 'Tipoestudio' ] )
		{
			echo "000|El nombre de examen ya existe para el tipo de orden ".$rowsExi[ 'Descripcion' ];
			return;
		}
	}
	
	$q = "SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM {$whce}_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos, (SELECT Tipgoi FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) Tipgoi 
		FROM
			{$whce}_000017 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Codigo = '$codigo'
			AND Tipoestudio LIKE '$tipoServicio'
			AND Tipoestudio IN (SELECT codigo FROM {$whce}_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)
			AND nuevo = 'on'
		UNION
		SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM {$whce}_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos, (SELECT Tipgoi FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) Tipgoi 
		FROM
			{$whce}_000047 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Codigo = '$codigo'
			AND Tipoestudio LIKE '$tipoServicio'
			AND Tipoestudio IN (SELECT codigo FROM {$whce}_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)
			AND nuevo = 'off'
			;";
	
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	//Consecutivo de ayudas hospitalarias
	//NO QUEMAR EL NRO EMPRESA
	$q2 = "SELECT
				Detval 
			FROM 
				root_000051
			WHERE
				Detemp = '$wemp_pmla'			
				AND Detapl = 'consecutivoAyudasHospitalarias';";

	$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);

	if($rs2 = mysql_fetch_array($res2)){
		$consecutivo = $rs2['Detval'];
		$auxConsecutivoAyudaHospitalaria = $rs2['Detval']; 
	}

	while ($rs = mysql_fetch_array($res)){
		//		$referencia = "javascript:seleccionarAyudaDiagnostica('".$rs['Servicio']."','".$rs['Codigo']."','".str_replace(" ","_",trim(htmlentities($rs['Descripcion'])))."','".str_replace(" ","_",trim(htmlentities($rs['Tipoestudio'])))."','".str_replace(" ","_",trim(htmlentities($rs['Anatomia'])))."','".str_replace(" ","_",trim(htmlentities($rs['Codcups'])))."','".str_replace(" ","_",trim(htmlentities($rs['cons'])))."','".str_replace(" ","_",trim(htmlentities($rs['Cconom'])))."');";
		//		$consulta = $consulta."<a href='#null' onclick=$referencia>".htmlentities($rs['Descripcion']).'</a><br/>';

		if(!empty($rs['Servicio']) && $rs['Servicio'] == $codigoAyudaHospitalaria){
			$esHospitalario = "on";
			$consecutivo = $auxConsecutivoAyudaHospitalaria;
		} else {
			$consecutivo = @$rs['cons'];
		}
		
		if( empty( $rs['reqJus'] ) ){
			$rs['reqJus'] = "off";
		}

		// echo htmlentities(@$rs['Descripcion'])."|".@$rs['Servicio']."|".@$rs['Codigo']."|".str_replace(" ","_",trim(htmlentities(@$rs['Descripcion'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Tipoestudio'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Anatomia'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Codcups'])))."|".@$consecutivo."|".str_replace(" ","_",trim(htmlentities(@$rs['Cconom'])))."|".@$esHospitalario."|".@$rs['reqJus']."|".@$rs['NoPos']."|".@$rs['Tipgoi']."|\n";
		echo ( (@$rs['Descripcion']) )."|".@$rs['Servicio']."|".@$rs['Codigo']."|".str_replace(" ","_",trim((@$rs['Descripcion'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Tipoestudio'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Anatomia'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Codcups'])))."|".@$consecutivo."|".str_replace(" ","_",trim(htmlentities(@$rs['Cconom'])))."|".@$esHospitalario."|".@$rs['reqJus']."|".@$rs['NoPos']."|".@$rs['Tipgoi']."|\n";
	}
	//	return $consulta;
}



function consultarAyudasDiagnosticasPorNombre($basedatos,$nombre,$unidadRealiza,$tipoServicio = '%',$especialidad){
	global $conex;
	global $codigoAyudaHospitalaria;

	$esHospitalario = "off";
	$consecutivo = "1";
	$auxConsecutivoAyudaHospitalaria = "1";
			
	$q = "SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos, (SELECT Tipgoi FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) Tipgoi 
		FROM
			hceidc_000047 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Servicio LIKE '%$unidadRealiza%'
			AND Descripcion LIKE '%$nombre%'
			AND Tipoestudio LIKE '$tipoServicio'
			AND Tipoestudio IN (SELECT codigo FROM hceidc_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)
		UNION
		SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos, (SELECT Tipgoi FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) Tipgoi 
		FROM
			hceidc_000017 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Servicio LIKE '%$unidadRealiza%'
			AND Descripcion LIKE '%$nombre%'
			AND Tipoestudio LIKE '$tipoServicio'
			AND Tipoestudio IN (SELECT codigo FROM hceidc_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)
			AND nuevo = 'on'
			;";
			
	$q = "SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos, (SELECT Tipgoi FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) Tipgoi 
		FROM
			hceidc_000047 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Servicio LIKE '%$unidadRealiza%'
			AND Descripcion LIKE '%$nombre%'
			AND Tipoestudio LIKE '$tipoServicio'
			AND Tipoestudio IN (SELECT codigo FROM hceidc_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)
			;";
	
	
//	echo "........Tip Servicio: ".$tipoServicio."|";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	//Consecutivo de ayudas hospitalarias
	//NO QUEMAR EL NRO EMPRESA
	$q2 = "SELECT
				Detval 
			FROM 
				root_000051
			WHERE
				Detemp = '10'			
				AND Detapl = 'consecutivoAyudasHospitalarias';";

	$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);

	if($rs2 = mysql_fetch_array($res2)){
		$consecutivo = $rs2['Detval'];
		$auxConsecutivoAyudaHospitalaria = $rs2['Detval']; 
	}

	while ($rs = mysql_fetch_array($res)){
		//		$referencia = "javascript:seleccionarAyudaDiagnostica('".$rs['Servicio']."','".$rs['Codigo']."','".str_replace(" ","_",trim(htmlentities($rs['Descripcion'])))."','".str_replace(" ","_",trim(htmlentities($rs['Tipoestudio'])))."','".str_replace(" ","_",trim(htmlentities($rs['Anatomia'])))."','".str_replace(" ","_",trim(htmlentities($rs['Codcups'])))."','".str_replace(" ","_",trim(htmlentities($rs['cons'])))."','".str_replace(" ","_",trim(htmlentities($rs['Cconom'])))."');";
		//		$consulta = $consulta."<a href='#null' onclick=$referencia>".htmlentities($rs['Descripcion']).'</a><br/>';

		if(!empty($rs['Servicio']) && $rs['Servicio'] == $codigoAyudaHospitalaria){
			$esHospitalario = "on";
			$consecutivo = $auxConsecutivoAyudaHospitalaria;
		} else {
			$consecutivo = @$rs['cons'];
		}
		
		if( empty( $rs['reqJus'] ) ){
			$rs['reqJus'] = "off";
		}

		echo (@$rs['Descripcion'])."|".@$rs['Servicio']."|".@$rs['Codigo']."|".str_replace(" ","_",trim((@$rs['Descripcion'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Tipoestudio'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Anatomia'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Codcups'])))."|".@$consecutivo."|".str_replace(" ","_",trim(htmlentities(@$rs['Cconom'])))."|".@$esHospitalario."|".@$rs['reqJus']."|".@$rs['NoPos']."|".@$rs['Tipgoi']."\n";
	}
	//	return $consulta;
}


function consultarAyudasDiagnosticasPorTipo($basedatos,$tipoServicio = '%',$especialidad){
	
	// $nombre,$unidadRealiza
	
	global $conex;
	global $codigoAyudaHospitalaria;

	$esHospitalario = "off";
	$consecutivo = "1";
	$auxConsecutivoAyudaHospitalaria = "1";

	$q = "SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos 
		FROM
			hceidc_000017 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Tipoestudio LIKE '$tipoServicio'
			AND Tipoestudio IN (SELECT codigo FROM hceidc_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)				
			;";
			
	
	$q = "SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos, (SELECT Tipgoi FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) Tipgoi 
		FROM
			hceidc_000047 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Tipoestudio LIKE '$tipoServicio'
			AND Tipoestudio IN (SELECT codigo FROM hceidc_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)
		UNION
		SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos, (SELECT Tipgoi FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) Tipgoi 
		FROM
			hceidc_000017 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Tipoestudio LIKE '$tipoServicio'
			AND Tipoestudio IN (SELECT codigo FROM hceidc_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)
			AND nuevo = 'on'
			;";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	//Consecutivo de ayudas hospitalarias
	//NO QUEMAR EL NRO EMPRESA
	$q2 = "SELECT
				Detval 
			FROM 
				root_000051
			WHERE
				Detemp = '10'			
				AND Detapl = 'consecutivoAyudasHospitalarias';";

	$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);

	if($rs2 = mysql_fetch_array($res2)){
		$consecutivo = $rs2['Detval'];
		$auxConsecutivoAyudaHospitalaria = $rs2['Detval']; 
	}

	while ($rs = mysql_fetch_array($res)){
		//		$referencia = "javascript:seleccionarAyudaDiagnostica('".$rs['Servicio']."','".$rs['Codigo']."','".str_replace(" ","_",trim(htmlentities($rs['Descripcion'])))."','".str_replace(" ","_",trim(htmlentities($rs['Tipoestudio'])))."','".str_replace(" ","_",trim(htmlentities($rs['Anatomia'])))."','".str_replace(" ","_",trim(htmlentities($rs['Codcups'])))."','".str_replace(" ","_",trim(htmlentities($rs['cons'])))."','".str_replace(" ","_",trim(htmlentities($rs['Cconom'])))."');";
		//		$consulta = $consulta."<a href='#null' onclick=$referencia>".htmlentities($rs['Descripcion']).'</a><br/>';

		if(!empty($rs['Servicio']) && $rs['Servicio'] == $codigoAyudaHospitalaria){
			$esHospitalario = "on";
			$consecutivo = $auxConsecutivoAyudaHospitalaria;
		} else {
			$consecutivo = @$rs['cons'];
		}
		
		if( empty( $rs['reqJus'] ) ){
			$rs['reqJus'] = "off";
		}

		echo htmlentities(@$rs['Descripcion']).",".@$rs['Servicio'].",".@$rs['Codigo'].",".str_replace(" ","_",trim(htmlentities(@$rs['Descripcion']))).",".str_replace(" ","_",trim(htmlentities(@$rs['Tipoestudio']))).",".str_replace(" ","_",trim(htmlentities(@$rs['Anatomia']))).",".str_replace(" ","_",trim(htmlentities(@$rs['Codcups']))).",".@$consecutivo.",".str_replace(" ","_",trim(htmlentities(@$rs['Cconom']))).",".@$esHospitalario.",".@$rs['reqJus'].",".@$rs['NoPos'].",".@$rs['Tipgoi'].",\n";
	}
	//	return $consulta;
}


 function consultarAyudasDiagnosticasPorCodigo($basedatos,$codigo,$unidadRealiza,$especialidad){
	global $conex;
	global $codigoAyudaHospitalaria;

	$esHospitalario = "off";
	$consecutivo = "1";
	$auxConsecutivoAyudaHospitalaria = "1";
	
	$consulta = "";
			
	$q = "SELECT
			Codigo,Descripcion,Servicio,Tipoestudio,Anatomia,Codcups,Estado,Ccocor cons,Cconom, (SELECT tiprju FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) reqJus, NoPos, (SELECT Tipgoi FROM hceidc_000015 c WHERE c.codigo = Tipoestudio) Tipgoi 
		FROM
			hceidc_000047 LEFT JOIN {$basedatos}_000011 ON Ccocod = Servicio
		WHERE
			Estado = 'on'			
			AND Servicio LIKE '%$unidadRealiza%'
			AND Codigo = '$codigo'
			AND Tipoestudio IN (SELECT codigo FROM hceidc_000015 c WHERE c.codigo = Tipoestudio)
			AND Tipoestudio NOT IN (
				SELECT
					Toetip
				FROM
					hceidc_000045
				WHERE
					Toeesp != '".$especialidad."' 
				AND Toeest = 'on'			
				)				
			;";
		
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	//Consecutivo de ayudas hospitalarias
	//NO QUEMAR EL NRO EMPRESA
	$q2 = "SELECT
				Detval 
			FROM 
				root_000051
			WHERE
				Detemp = '10'			
				AND Detapl = 'consecutivoAyudasHospitalarias';";

	$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);

	if($rs2 = mysql_fetch_array($res2)){
		$consecutivo = $rs2['Detval'];
		$auxConsecutivoAyudaHospitalaria = $rs2['Detval']; 
	}

	while ($rs = mysql_fetch_array($res)){
		//		$referencia = "javascript:seleccionarAyudaDiagnostica('".$rs['Servicio']."','".$rs['Codigo']."','".str_replace(" ","_",trim(htmlentities($rs['Descripcion'])))."','".str_replace(" ","_",trim(htmlentities($rs['Tipoestudio'])))."','".str_replace(" ","_",trim(htmlentities($rs['Anatomia'])))."','".str_replace(" ","_",trim(htmlentities($rs['Codcups'])))."','".str_replace(" ","_",trim(htmlentities($rs['cons'])))."','".str_replace(" ","_",trim(htmlentities($rs['Cconom'])))."');";
		//		$consulta = $consulta."<a href='#null' onclick=$referencia>".htmlentities($rs['Descripcion']).'</a><br/>';

		if(!empty($rs['Servicio']) && $rs['Servicio'] == $codigoAyudaHospitalaria){
			$esHospitalario = "on";
			$consecutivo = $auxConsecutivoAyudaHospitalaria;
		} else {
			$consecutivo = @$rs['cons'];
		}
		
		if( empty( $rs['reqJus'] ) ){
			$rs['reqJus'] = "off";
		}

		$consulta = htmlentities(@$rs['Descripcion'])."|".@$rs['Servicio']."|".@$rs['Codigo']."|".str_replace(" ","_",trim(htmlentities(@$rs['Descripcion'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Tipoestudio'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Anatomia'])))."|".str_replace(" ","_",trim(htmlentities(@$rs['Codcups'])))."|".@$consecutivo."|".str_replace(" ","_",trim(htmlentities(@$rs['Cconom'])))."|".@$esHospitalario."|".@$rs['reqJus']."|".@$rs['NoPos']."|".$rs['Tipgoi']."\n";
	}
	return $consulta;
}

  
/**
 *
 * @param $basedatos
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $codUsuario
 * @param $centroCostos
 * @param $consecutivoOrden
 * @return unknown_type
 */
function cancelarOrdenHCE($basedatos,$historia,$ingreso,$fecha,$codUsuario,$centroCostos,$consecutivoOrden){
	global $conex;
	$consulta = "";

	//Inactivar el encabezado de la orden
	$q = "UPDATE hceidc_000027 SET
			 Ordest = 'off'
		WHERE
			Ordhis = '$historia'  
			AND Ording = '$ingreso' 
			AND Ordtor = '$centroCostos' 
			AND Ordnro = '$consecutivoOrden';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Inactivar el detalle de la orden
	$q = "UPDATE ".$basedatos."_000159 SET
			 Detest = 'off'
		WHERE
			Dettor = '$centroCostos'
			AND Detnro = '$consecutivoOrden';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	if( mysql_affected_rows() > 0 ){
		//Cancelacion de la orden
		$auditoria = new AuditoriaDTO();
	
		$auditoria->historia = $historia;
		$auditoria->ingreso = $ingreso;
		$auditoria->fechaKardex = $fecha;
		$auditoria->mensaje = obtenerMensaje('MSJ_ORDEN_CANCELADA');
		$auditoria->seguridad = $codUsuario;
		$auditoria->descripcion = "$centroCostos,$consecutivoOrden";
	
		registrarAuditoriaKardex($conex,$basedatos,$auditoria);
	}
		 
	
	$q = "SELECT 
			  Arc_HL7, c.codigo as codigo
		  FROM
			  ".$basedatos."_000159 a, hceidc_000015 b, hceidc_000047 c
		  WHERE
			  detcod = c.codigo
			  AND tipoestudio = b.codigo
			  AND Dettor = '$centroCostos'
			  AND Detnro = '$consecutivoOrden'
			  AND Arc_HL7 != ''
			  AND Arc_HL7 != 'NO APLICA'
		 UNION
		 SELECT 
			  Arc_HL7, c.codigo as codigo
		  FROM
			  ".$basedatos."_000159 a, hceidc_000015 b, hceidc_000017 c
		  WHERE
			  detcod = c.codigo
			  AND tipoestudio = b.codigo
			  AND Dettor = '$centroCostos'
			  AND Detnro = '$consecutivoOrden'
			  AND Arc_HL7 != ''
			  AND Arc_HL7 != 'NO APLICA'
		 ;";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	for(;$rows = mysql_fetch_array( $res ); ){
		
		$sql = "SELECT 
			 		id
		  		FROM
			  		hceidc_{$rows['Arc_HL7']}
		  		WHERE
				  	hl7des = '$centroCostos'
				  	AND hl7nor = '$consecutivoOrden'
		 		;";
		
		$res1 = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		
		for(; $rowsHL7 = mysql_fetch_array( $res1 ); ){
			cancelarDatosHL7( $conex, "hceidc", $rows['Arc_HL7'], $rowsHL7['id'] );
		}
	}
	
	liberarConexionBD($conex);

	$consulta = "1";
	return $consulta;
}

function grabarOrdenHCE($basedatos,$historia,$ingreso,$fecha,$codUsuario,$centroCostos,$consecutivoOrden,$observacionesOrden){
	global $conex;
	$consulta = "";
	$hora = date("H:i:s");

	//Inactivar el encabezado de la orden
	$q = "UPDATE hceidc_000027 SET
			 Ordobs = CONCAT(Ordobs,'\r\n','"."Observacion añadida el $fecha a las $hora:\r\n$observacionesOrden"."')
		WHERE
			Ordhis = '$historia'  
			AND Ording = '$ingreso' 
			AND Ordtor = '$centroCostos' 
			AND Ordnro = '$consecutivoOrden';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = obtenerMensaje('MSJ_OBSERVACIONES_ORDEN_MODIFICADAS');
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conex,$basedatos,$auditoria);

	liberarConexionBD($conex);

	$consulta = "1";
	return $consulta;
}

function mostrarDetalleOrdenes($wemp_pmla,$wempresa,$wbasedato,$whis,$wing,$wfecini,$wfecfin,$wtiposerv,$wprocedimiento,$westadodet)
{
	global $conex;

	  //$dias = 31;
	  $fechaord = date("Y-m-d");
	  if(!isset($wfecini))
		$wfecini = gmdate("Y-m-d", 0 );
	  if(!isset($wfecfin))
		$wfecfin = date("Y-m-d", strtotime("$fechaord - 1 day"));
	  
	  if(!isset($wtiposerv))
		$wtiposerv = "";
		
	  if(!isset($wprocedimiento))
		$wprocedimiento = "";
		
	  $westadodet = 'Realizado';

	  if(isset($wprocedimiento) && $wprocedimiento!="")
		$qprocedimiento = " AND C.descripcion LIKE '%".$wprocedimiento."%' ";

	  if(isset($wtiposerv) && $wtiposerv!="")
		$qtiposerv = " AND A.ordtor LIKE '".$wtiposerv."' ";
		
	  //Traigo todas las Ordenes del Servicio seleccionado y de la historia ingreso dado y que esten 'Realizadas'
	  $q = " SELECT A.ordfec, A.ordhor, B.detcod, C.descripcion, B.detrdo, D.Arc_HL7, D.Programa, D.Formato, A.ordnro, B.detite, B.detesi, E.Cconom, A.Ordtor, B.Detimp, B.Detfec, NoPos "
		  ."   FROM ".$wempresa."_000027 A LEFT JOIN ".$wbasedato."_000011 E ON Ccocod = Ordtor, ".$wempresa."_000028 B, ".$wempresa."_000017 C, ".$wempresa."_000015 D "
		  ."  WHERE A.ordfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
		  ."  " . $qprocedimiento . " "	
		  ."  " . $qtiposerv . " "	
		  ."    AND A.ordtor      = B.dettor "
		  ."    AND A.ordnro      = B.detnro "
	//	  ."    AND B.detesi      = 'Realizado'"
		  ."    AND A.ordest      = 'on' "
		  ."	AND B.detest  	  = 'on' "		   //Solo traigo los que estén activos	// 2012-06-26
		  ."    AND B.detcod      = C.codigo "
		  ."    AND C.tipoestudio = D.codigo "
		  ."    AND A.ordhis      = '".$whis."'"
		  ."    AND A.ording      = '".$wing."'"
		  ."  ORDER BY 1 desc ";
		  
	
	  //Traigo todas las Ordenes del Servicio seleccionado y de la historia ingreso dado y que esten 'Realizadas'
	  $q = " SELECT A.ordfec, A.ordhor, B.detcod, C.descripcion, B.detrdo, D.Arc_HL7, D.Programa, D.Formato, A.ordnro, B.detite, B.detesi, E.Cconom, A.Ordtor, B.Detimp, B.Detfec, NoPos "
		  ."   FROM ".$wempresa."_000027 A LEFT JOIN ".$wbasedato."_000011 E ON Ccocod = Ordtor, ".$wempresa."_000028 B, ".$wempresa."_000047 C, ".$wempresa."_000015 D "
		  ."  WHERE A.ordfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
		  ."  " . $qprocedimiento . " "	
		  ."  " . $qtiposerv . " "	
		  ."    AND A.ordtor      = B.dettor "
		  ."    AND A.ordnro      = B.detnro "
	//	  ."    AND B.detesi      = 'Realizado'"
		  ."    AND A.ordest      = 'on' "
		  ."	AND B.detest  	  = 'on' "		   //Solo traigo los que estén activos	// 2012-06-26
		  ."    AND B.detcod      = C.codigo "
		  ."    AND C.tipoestudio = D.codigo "
		  ."    AND A.ordhis      = '".$whis."'"
		  ."    AND A.ording      = '".$wing."'"
		  ."  ORDER BY 1 desc ";
	  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error());
	  $num = mysql_num_rows($res);

	  if ($num > 0)
		  $row = mysql_fetch_array($res);

	  echo "<center><table>";   
	  echo "<tr>";
	  echo "<td colspan='4' class='textoNormal'><b>Filtro</b></td>";
	  echo "</tr>";
	  echo "<tr>";

	  echo "<td width='140' colspan='2'>";
	  echo "<table>";   
	  echo "<tr>";
	  echo "<td width='110'>".campoFechaDefecto('wfecini',$wfecini)."</td>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "<td width='110'>".campoFechaDefecto('wfecfin',$wfecfin)."</td>";
	  echo "</tr>";
	  echo "</table>";   
	  echo "</td>";

	  echo "<td width='80' valign='middle'>";
		//EXAMENES DE LABORATORIO
		echo "<select id='wtiposerv' name='wtiposerv' class='textoNormal' onchange='consultarOrdenes(\"".$wemp_pmla."\",\"".$wempresa."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\",\"".$wfecini."\",\"".$wfecfin."\",\"".$wtiposerv."\",\"".$wprocedimiento."\",\"".$westadodet."\")'>";
		$examenesLaboratorio = consultarCentrosAyudasDiagnosticas($whis,$wing);
		$colServiciosExamenes = $examenesLaboratorio;

		echo "<option value=''>Seleccione</option>";
		foreach ($examenesLaboratorio as $examen){
			if($wtiposerv!=$examen->codigo)
				echo "<option value='$examen->codigo'>$examen->nombre</option>";
			else
				echo "<option value='$examen->codigo' selected>$examen->nombre</option>";
		}
		echo "</select>";
	  echo "</td>";
	  
	  echo "<td valign='middle'><input class='textoNormal' type='text' name='wprocedimiento' id='wprocedimiento' value='".$wprocedimiento."' onblur='consultarOrdenes(\"".$wemp_pmla."\",\"".$wempresa."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\",\"".$wfecini."\",\"".$wfecfin."\",\"".$wtiposerv."\",\"".$wprocedimiento."\",\"".$westadodet."\")' size='81'></td>";
	  
	  echo "<td width='80' valign='middle' style='display:none'>";
		//ESTADOS DEL EXAMEN DEL LABORATORIO
		echo "<select id='westadodet' name='westadodet' class='textoNormal' disabled onchange='consultarOrdenes(\"".$wemp_pmla."\",\"".$wempresa."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\",\"".$wfecini."\",\"".$wfecfin."\",\"".$wtiposerv."\",\"".$wprocedimiento."\",\"".$westadodet."\")'>";

		$colEstadosExamen = consultarEstadosExamenes();
		foreach ($colEstadosExamen as $estadoExamen){
			if($westadodet!=$estadoExamen->codigo)
				echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
			else
				echo "<option value='$estadoExamen->codigo' selected>$estadoExamen->descripcion</option>";
		}

	  echo "</select>";
	  echo "</td>";
	  echo "</tr>";
	  echo "<tr class='encabezadoTabla'>";
	  echo "<td><b>Fecha</b></td>";
	  echo "<td><b>Imprimir</b></td>";
	  echo "<td><b>Tipo</b></td>";
	  echo "<td><b>Procedimiento</b></td>";
	  echo "<td style='display:none'><b>Estado</b></td>";
	  echo "</tr>";

	  $contExamenes = 999;
	  
	  $i=1;
	  while ($i <= $num)
		{
		 ///$row = mysql_fetch_array($res);
			   
		 $wfecord=$row[0];
		 $wproord=$row[3];
		 $westord=$row[10];
		  
		  $wtipord = $row['Cconom'];
		  //Si es ayuda hospitalaria el centro de costos es hospitalario
		  if($row['Ordtor'] == $codigoAyudaHospitalaria){
			$wtipord = "HOSPITALARIAS";
		  }
		 
		 
		 if (isset($wclase) && $wclase == "fila1")
			$wclase = "fila2";
		   else 
			  $wclase = "fila1";
				  
		  echo "<tr class='".$wclase."'>";

		  echo "<td>".$wfecord."</td>";

			echo "<td align='center'>";
			
			if($row['Detimp']!='on')
			{
				$claseImagen = 'opacar';
				$claseAlterna = 'aclarar';
				$activoImpresion = '';
			}
			else
			{
				$claseImagen = 'aclarar';
				$claseAlterna = 'opacar';
				$activoImpresion = ' checked';
			}
			
			// echo "<div id='imgImprimir".$contExamenes."' class='".$claseImagen."' style='display:inline' title='Click para imprimir este procedimiento'><img width='21' height='21' onClick='javascript:cambiarClase(\"imgImprimir".$contExamenes."\",\"opacar\",\"aclarar\"); marcarImpresionExamen(\"\",\"".$examen->tipoDeOrden."\",\"".$examen->numeroDeOrden."\",\"".$examen->codigoExamen."\",\"".$examen->fechaARealizar."\",\"".$contExamenes."\");' src='../../images/medical/hce/icono_imprimir.png' border='0'/> &nbsp;&nbsp; </div>";
			
			echo "<div id='imgImprimir".$contExamenes."' style='display:inline'><img width='18' height='18' src='../../images/medical/hce/icono_imprimir.png' border='0'/><br /><input type='checkbox' id='imprimir_examen".$contExamenes."' name='imprimir_examen".$contExamenes."' onClick='javascript:marcarImpresionExamen(this,\"".$row['Ordtor']."\",\"".$row['ordnro']."\",\"".$row['detcod']."\",\"".$row['Detfec']."\",\"".$contExamenes."\",\"".$row['detite']."\");' ".$activoImpresion." /></div>";
			echo "<input type='hidden' id='wproespos".$contExamenes."' value='".( $row['NoPos'] == 'on' ? 'off':'on' )."'>";
			
			$proConCTC = false;
			if( $row['NoPos'] == 'on' ){
				$proConCTC = tieneCTCProcedimientos( $conex, $wbasedato, $whis, $wing, $row['Ordtor'], $row['ordnro'], $row['detite'] , $row['detcod'] );
			}
			echo "<input type='hidden' id='wprotienectc".$contExamenes."' value='".( $proConCTC ? 'on':'off' )."'>";
			// tieneCTCProcedimientos( $conex, $wbaseadato, $historia, $ingreso, $tipoOrden, $nroOrden, $item, $pro );
			echo "</td>";

		  echo "<td>".$wtipord."</td>";
		 
		 // 2012-06-26
		 // Se concatena al final con la variable $i para asegurar que no coincida ningún nombre de los DIV's
		 $wnomdiv=$row[0]."-".$row[1]."-".$row[2]."-".$i;   //Nombre del DIV
			 
		 echo "<td>";   
		 echo "<a href='#null' onclick=javascript:intercalarOrdenAnterior('".$wnomdiv."');>".$row[3]."</a>";

		 echo "<div id='".$wnomdiv."' style='display:none'>";
		 echo "<table align='center' border=0>";
			
		 //echo "<tr class='encabezadoTabla' align='center'>";
		 //echo "<td align=center><font size=4>".$row[3]."</font></td>";                                        //Nombre del Estudio
		 //echo "</tr>";
			
		 //Formatos
		 // 1: Descriptivo                  ej: Patologia, Mamografia, Endoscopia, Cardiología (hasta que no se tome por HL7), etc.
		 // 2: Descriptivo com Imagen       ej: Imagenología (TAC, RX) con HL7, etc.
		 // 3: Por valores y con referencia ej: Laboratorio Clínico
		 
		 $wtabla=$row[5];             //Archivo en el que se almacena el resultado, si tiene valor es porque es HL7, este valor esta en la tabla hceidc_000015
		 $wformatoVista=$row[7];      //Formato en que se ve el resultado, Viene de la tabla hceidc_000015
		 $wnor  =$row[8];             //Numero de orden
		 $wite  =$row[9];             //Numero de Orden es el numero de orden seguido de un guion y la fila correspodiente al examen o procedimiento
		 
		 switch ($wformatoVista)
			{
			  case "1":  
					if (trim($wtabla)=="" or strtoupper(trim($wtabla)) == "NO APLICA") 
					 {
					   //Muestra el Resultado
					   echo "<tr class='encabezadoTabla' align='center'>";
					   echo "<td align=center><textarea cols=81 rows=5 readonly>".$row[4]."</textarea></td>";
					   echo "</tr>";
					 }
					else 
					   traer_resultado($wtabla, $wcco, $whis, $wing, $wnor, $wite);
			  break;
				  
			  case "2":    
			  
				  $wresultado=traer_resultado($wtabla, $wcco, $whis, $wing, $wnor, $wite);
				  
				  //Muestra el Resultado
				  echo "<tr class='encabezadoTabla' align='center'>";
				  echo "<td align=center><textarea cols=81 rows=5 name=wrdo readonly>".$wresultado."</textarea></td>";
				  echo "</tr>";
				 // 2012-06-26
				 // Se comenta porque aun no existe una tabla o campo de donde tomar la imagen
				 //echo "<tr><td><A HREF='Entrega_de_turno_enfermeria.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wfec=".$wfecant."' class=tipo4V>Ver Imagen</A></td></tr>";	
				  
			  break;
				  
			  case "3":
			  
				  $wresultado = traer_resultado($wtabla, $wcco, $whis, $wing, $wnor, $wite);
				  $wresultado_parciado = mostrar_resultado($wresultado);
				  
				  //Muestra el Resultado
				  //echo "<tr class='encabezadoTabla' align='center'>";
				  //echo "<td align=center><textarea rows=22 cols=140 name=wrdo readonly>".$wresultado_parciado."</textarea></td>";
				  //echo "</tr>";
			  
			  break;
				  
			  default: 
				  echo "<tr class='encabezadoTabla' align='center'>";
				  echo "<td align=center><textarea cols=81 rows=5 name=wrdo readonly>".$row[4]."</textarea></td>";                 //Muestra el Resultado
				  echo "</tr>";
			  break;
			}  
				
		 echo "</table>";
		 echo "</div>";

		 echo "</td>";
		 echo "<td style='display:none'>".$westord."</td>";
		 echo "</tr>";
		 $i++;
		 $row = mysql_fetch_array($res);
		 
		 $contExamenes++;

		 //echo "</table></center>";	
		}
	  echo "</table></center>";	
}

function consultarComponenteLEV($basedatos,$descripcion){
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Levcod,Levnom,Levcon,Levvol,Levest
		FROM
		{$basedatos}_000075
		WHERE
			Levnom LIKE '%$descripcion%'
			AND Levest = 'on'";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		while($info = mysql_fetch_array($res)){
			echo $info['Levnom']." [".$info['Levcon']."] en ".$info['Levvol']."\n";
		}
}

/************************************************************************************************************************
 * Calcula la cantidad a grabar para un medicamento
 * 
 * @param $fechaHoy
 * @param $fechaInicio
 * @param $horaInicioSuministro
 * @param $horasFrecuencia
 * @param $esPrimeraVez
 * @return unknown_type
 * 
 * Modificaciones
 * Marzo 8 de 2011	(Edwin MG)	La variable $fechaHoy se manda por parametro, queda con la fecha del kardex
 ************************************************************************************************************************/
function calcularCantidadGrabar( $fechaHoy,$fechaInicio,$horaInicioSuministro,$horasFrecuencia,$esPrimeraVez, $dosisMaximas, $aplicacionesAnteriores, $aplicacionesPorSaldo ){
	global $horaCorteDispensacion;

	$horasDia = 24;
	$cantidad = 0;
	$cocienteHoras = 0;

//	$fechaHoy = date("Y-m-d");
	$fechaAyer = date( "Y-m-d", strtotime($fechaHoy)-24*3600 );

	//Indicadores
	$esAnteriorAHoraSuministro = false;
	$esHoy = false;

	//Hora de inicio entera
	$vecHoraInicioSuministro	= explode(":",$horaInicioSuministro);
	$vecFechaInicioSuministro	= explode("-",$fechaInicio);
	$horaInicioInt 				= intval($vecHoraInicioSuministro[0]);

//	if($horaInicioInt == 0){
//		$horaInicioInt = 24;
//	}

	//Si la hora de inicio es mayor a las 16 se tiene en cuenta la frecuencia
//	$fechaTempDiaSiguiente = mktime($horaCorteDispensacion,0,0,date("m"),date("d"),date("Y")) + (24*60*60);
//	$fechaActualHoraInicio = mktime($horaInicioSuministro,0,0,date("m"),date("d"),date("Y"));

	$fechaTempDiaSiguiente = mktime($horaCorteDispensacion,0,0,$vecFechaInicioSuministro[1],$vecFechaInicioSuministro[2],$vecFechaInicioSuministro[0]) + (24*60*60);
	$fechaActualHoraInicio = mktime($horaInicioSuministro,0,0,$vecFechaInicioSuministro[1],$vecFechaInicioSuministro[2],$vecFechaInicioSuministro[0]);

	$fechaHoraInicio = mktime(0,0,0,$vecFechaInicioSuministro[1],$vecFechaInicioSuministro[2],$vecFechaInicioSuministro[0]);

	$vecFechaHoy	= explode("-",$fechaHoy);

	$diferenciaHoras = ($fechaTempDiaSiguiente - $fechaActualHoraInicio);
	$diferenciaHoras = intval($diferenciaHoras / 3600); 	//60 (minutos) * 60 (horas)
	$diferenciaSuministroInicio = intval($horaCorteDispensacion-$horaInicioInt);
	$diferenciaInicioSuministro = intval($horaInicioInt-$horaCorteDispensacion);

	//Diferencia de horas entre el dia actual y la fecha de inicio
	$timeActual = mktime(0,0,0,$vecFechaHoy[1],$vecFechaHoy[2],$vecFechaHoy[0]);
	$diferenciaTotal = intval(($fechaHoraInicio-$timeActual) / 3600);
	/*****************************************************************************************
	 * Son cuatro condiciones a controlar:
	 * 1.Menor o igual a la hora maxima suministro
	 * 2.Despues de la hora maxima de suministro
	 * 3.La fecha de inicio es de hoy
	 * 4.Primera vez del articulo en un kardex
	 *****************************************************************************************/
	if($horaInicioInt <= $horaCorteDispensacion){
		$esAnteriorAHoraSuministro = true;
	}

	if(trim($fechaInicio) == trim($fechaHoy)){
		$esHoy = true;
	}

	$esta = in_array("*",obtenerVectorAplicacionMedicamentos($fechaHoy, $fechaInicio, $horaInicioSuministro, $horasFrecuencia));
	
	if($esPrimeraVez){
		if($esAnteriorAHoraSuministro && $esHoy){
			if(!isset($horasFrecuencia) || $horasFrecuencia == 0) $horasFrecuencia = 1;
			$cocienteHoras = intval(($horasDia + ($horaCorteDispensacion-$horaInicioInt))/$horasFrecuencia) + 1;
		}

		if($esAnteriorAHoraSuministro && !$esHoy){
			
			if(!isset($horasFrecuencia) || $horasFrecuencia==0)
				$horasFrecuencia = 1;
		
			$cocienteHoras = intval($diferenciaSuministroInicio/$horasFrecuencia) + 1;

			if($horaInicioInt > 8 && $horasFrecuencia > 8){	//Junio 10 de 2011, condicion original: $horaInicioInt > 8 && $horasFrecuencia >= 8
				$cocienteHoras = 1;
			}
		}

		if(!$esAnteriorAHoraSuministro && $esHoy){
			$cocienteHoras = intval(($horasDia - $diferenciaInicioSuministro)/$horasFrecuencia) + 1;
		}

		if(!$esHoy){
			if(!$esAnteriorAHoraSuministro){
				$cocienteHoras = 0;
			} else {
				if($diferenciaTotal >= 48){
					$cocienteHoras = 0;
				}
			}
		}
	} else {
		if( $fechaHoy >= $fechaInicio  ){ 			//Diciembre 16 de 2010
//			if( $horasFrecuencia <= $horasDia ){		//Si la frecuencia es hasta 24 horas aplica esto
			if( $horasFrecuencia <= (($fechaTempDiaSiguiente - $fechaActualHoraInicio)/3600) ){		//Si la frecuencia es hasta 24 horas aplica esto
//				echo "<br>...........Dif horas: ".(($fechaTempDiaSiguiente - $fechaActualHoraInicio)/3600);
				$cocienteHoras = (($fechaTempDiaSiguiente - $fechaActualHoraInicio)/3600) / $horasFrecuencia;
//				$cocienteHoras = $horasDia / $horasFrecuencia;
			} else {
				if($esta){  //Si la frecuencia es de mas de 24 horas
					$cocienteHoras = 1;
				} else {
					$cocienteHoras = 0;
				}
			}
		}		//Diciembre 16 de 2010
	}

	$cocienteHorasEntero = intval($cocienteHoras);
	$cantidad = $cocienteHorasEntero;
	
	
	/****************************************************************************************************************
	 * Enero 05 de 2011
	 ****************************************************************************************************************/
	//$aplicacionesAnteriores = consultarAplicacionesArticuloPaciente( $conexion, $wbasedato, $idRegDiaAnterior );
	
	//Si la dosis maxima es menor que la cantidad a grabar, la cantidad a grabar seran la dosis maximas
	if( $dosisMaximas != '' && $dosisMaximas != 0 && $dosisMaximas - $aplicacionesAnteriores - $aplicacionesPorSaldo < $cantidad ){
		$cantidad = $dosisMaximas - $aplicacionesAnteriores - $aplicacionesPorSaldo;
	}
	
	if( $cantidad < 0 ){
		$cantidad = 0;
	}
	/******************************************************************************************************************/

	return $cantidad;
}

function consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$codigoArticulo){
	$cantidad = "";

	/*En algunos casos las devoluciones no se marcan adecuadamente en el kadcdi por eso se encuentran cantidades demas
	$q = "SELECT
			SUM(Kadcdi) canAcumulada
		FROM
			{$wbasedato}_000054
		WHERE
			Kadhis = '$historia'
			AND Kading = '$ingreso'
			AND Kadart = '$codigoArticulo'
			AND Kadest = 'on'";*/

	$q = "SELECT
			IFNULL(SUM(Aplcan),0) canAcumulada
		FROM
			{$wbasedato}_000015
		WHERE
			Aplhis 		= '$historia'
			AND Apling  = '$ingreso'
			AND Aplart  = '$codigoArticulo'
			AND Aplest  = 'on'";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q - " . mysql_error());
	$num = mysql_num_rows($res);

	while($info = mysql_fetch_array($res)){
		$cantidad = $info['canAcumulada'];
	}
	return $cantidad;
}


function calcularSaldoActual($conexion,$wbasedato,$historia,$ingreso,$fechaKardex,$codArticulo,$fechaInicio,$horaInicio,$cantDosis,$horasFrecuencia,$dosisMaximas,$ccoOrigen,$diasTtoAcumulados,&$cantGrabar,&$saldoNuevo,&$cantDispensar,&$cantManejo,$saldoDispensacion, $tipoProtocolo, &$horasAplicacionDia,$diasTto,$idRegDiaAnterior = '' ){
	
	global $codigoCentralMezclas;
	global $horaCorteDispensacion;
	global $wemp_pmla;
	
	$saldoDispensacion	= (integer) $saldoDispensacion;
	$cantDosis			= (integer) $cantDosis;
	$cantidadFracciones = (integer) $cantidadFracciones;

	//Variables
	$saldoAnterior = 0;
	$saldoNuevo = 0;
	$dispensarNuevo = false;
	$fraccionesTotales = 0;
	$factor = 1;

	if( $idRegDiaAnterior != '' ){
		//Consulta del saldo anterior del articulo
					
		$q = "SELECT
					Kadart,Kadsal,Kadfec,Kadfin,Kadhin,Kadori,Kadhdi,Kaddis,Kadcnd, Kadcdi
				FROM
					{$wbasedato}_000054
				WHERE
					id = '$idRegDiaAnterior'
				";
				
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
	}
	else{
		$num = 0; 
	}

	if($num > 0){
		$fila = mysql_fetch_array($res);
	}

	$ccoCM=ccoUnificadoCM(); //Se obtiene el Codigo de Central de Mezclas
	$ccoSF=ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
	//La solución a esto es consultar las fracciones, si no tiene fracciones se usan dias de estabilidad cero (no tiene)
	$tarti = $ccoCM;
	
	if( $ccoOrigen == "SF" ){
		$tarti = $ccoSF;
	}

	$qf = "SELECT
			Defart, Deffra, Defdup, Defdis, Defcco, Defdie
		FROM
			{$wbasedato}_000059
		WHERE
			Defart = '$codArticulo'
			AND Defcco = '$tarti'
			AND Defest = 'on';"; 

	$respin = mysql_query($qf, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qf . " - " . mysql_error());
	$numf = mysql_num_rows($respin);

	$diasEstabilidad = "0";
	$dispensable = "on";
	$cantidadFracciones = 1;

	while($infofr = mysql_fetch_array($respin)){
		$diasEstabilidad 	= $infofr['Defdie'];
		$dispensable 		= $infofr['Defdis'];
		$cantidadFracciones = $infofr['Deffra'];
	}
	
	$fechaUsar = $fechaInicio;
	$horaUsar = $horaInicio;
	$dejarSaldoArticulo = true;
	
	/******************************************************************************
	 * Marzo 26 de 2012
	 * Calculo el total de dosis correspondientes por días de tratamiento
	 ******************************************************************************/
	//Calculo cuantas dosis maximas son por dias de tratamiento en caso de tener
	if( !empty( $diasTto ) ){
		
		if(!isset($horasFrecuencia) || $horasFrecuencia==0)
			$horasFrecuencia = 1;
	
		$dosisPorDtto = strtotime( $fechaInicio." 00:00:00" ) + $diasTto*24*3600;
		$dosisPorDtto = intval( ( $dosisPorDtto - strtotime( $fechaInicio." ".$horaInicio ) )/($horasFrecuencia*3600 ) ) + 1;
		
		$dosisMaximas = $dosisPorDtto;
	}
	/******************************************************************************/
	
	$esPrimeraVez = esPrimeraVez( $conexion, $wbasedato, $historia, $ingreso, $codArticulo, $idRegDiaAnterior, $fechaKardex, $fechaInicio, $horaInicio, $horasFrecuencia, $fechaUsar, $horaUsar, $dejarSaldoArticulo );

	//Marzo 26 de 2012
	//Agrego dosis maximas y dias de tratamiento en la funcion 
	$aplicacionesAnteriores = consultarAplicacionesArticuloPaciente( $conexion, $wbasedato, $idRegDiaAnterior );
	
	$cantGrabar = @calcularCantidadGrabar( $fechaKardex, $fechaUsar, $horaUsar, $horasFrecuencia, $esPrimeraVez, $dosisMaximas, $aplicacionesAnteriores, intval( $saldoDispensacion/($cantDosis/$cantidadFracciones) ) );	//Enero 14 de 2011
	
	/****************************************************************************************************************
	 * creando string con las aplicaciones de las hora 
	 * @var unknown_type
	 ****************************************************************************************************************/
	$info = consultarinfotipoarticulosKardex( $conexion, $wbasedato );
	
	$vectorAplicacion = obtenerVectorAplicacionMedicamentos( $fechaKardex, $fechaUsar, $horaUsar, $horasFrecuencia );
	$vectorAplicacion2 = obtenerVectorAplicacionMedicamentos( date( "Y-m-d", strtotime( $fechaKardex )+24*3600 ), $fechaUsar, $horaUsar, $horasFrecuencia );

	$vectorAplicacion2 = arreglarVectorKardex( $vectorAplicacion2 );

	$quitarUnoARegleta = 25;
	if( count($vectorAplicacion2) == 25 ){
		$quitarUnoARegleta = 24;
	}			
	
	foreach( $vectorAplicacion2 as $keyB => $valueB ){
		$vectorAplicacion[$quitarUnoARegleta] = $valueB;
		$quitarUnoARegleta++;
	}

	$horasAplicacionDia = crearVectorAplicaciones( $vectorAplicacion, $info[ $tipoProtocolo ]['tiempoPreparacion'], $cantDosis/$cantidadFracciones, intval( ( strtotime( "1970-01-01 ".$info[ $tipoProtocolo ]['horaCaroteDispensacion'] ) - strtotime( "1970-01-01 00:00:00" ) )/3600 ) );
	/****************************************************************************************************************/
			
	/****************************************************************************************************************
	 * Abril 25 de 2011
	 * Si el medicamento tiene condicion a necesidad, el saldo de articulo es 0 si no se dispenso completamente
	 ****************************************************************************************************************/
	if( $num > 0 ){
		
		$sql = " SELECT Contip "
		      ."   FROM ".$wbasedato."_000042 "      //Tabla condiciones de administracion
			  ."  WHERE concod = '".$fila['Kadcnd']."'";
			  
		$resAN = mysql_query( $sql, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrowsAN = mysql_num_rows( $resAN );
			  
		if( $numrowsAN > 0 ){
			$rowsAn = mysql_fetch_array( $resAN );
	
			if( $rowsAn['Contip'] == "AN" && ($fila['Kadcdi']-$fila['Kaddis']) > 0){
				$fila['Kadsal'] = 0;
			}
		}
	}
	/****************************************************************************************************************/	  
	
	if( !$dejarSaldoArticulo ){	//Abril 27 de 2011. Si el paciente viene de urgencia o cirugia, el saldo del articulo del día anterior es 0
		$fila['Kadsal'] = 0;
	}
	
	// /****************************************************************************************************************
	 // * Enero 05 de 2011
	 // ****************************************************************************************************************/
	// $aplicacionesAnteriores = consultarAplicacionesArticuloPaciente( $conexion, $wbasedato, $idRegDiaAnterior );
	
	// //Si la dosis maxima es menor que la cantidad a grabar, la cantidad a grabar seran la dosis maximas
// //	if($dosisMaximas != '' && $dosisMaximas != 0 && $dosisMaximas < $cantGrabar){
	// if($dosisMaximas != '' && $dosisMaximas != 0 && $dosisMaximas - $aplicacionesAnteriores - $saldoDispensacion < $cantGrabar){
		// $cantGrabar = $dosisMaximas - $aplicacionesAnteriores - $saldoDispensacion;
	// }
	
	// if( $cantGrabar < 0 ){
		// $cantGrabar = 0;
	// }
	// /******************************************************************************************************************/
	
	$dosisTemporal = (double)($cantDosis * $cantGrabar);

	if( isset($fila['Kadsal']) && $codArticulo == trim( $fila['Kadart'] ) ){	//Mayo 2 de 2012
		$saldoAnterior = (double)($fila['Kadsal']);
	}
	else{
		$saldoAnterior = 0;
	}
	
	$fraccionesTotales = (double)($cantidadFracciones);
	$cantManejo = $fraccionesTotales;

	//Se incluyen los dias de estabilidad:
	//Si el origen del articulo es central de mezclas y los dias de tratamiento acumulados, superan los dias de estabilidad,se hace el saldo a cero (pide completo)
	if($ccoOrigen == $codigoCentralMezclas && intval($diasEstabilidad) > 0 && (intval($diasTtoAcumulados) % intval($diasEstabilidad) == 0)){
		$saldoAnterior = 0;
	}

	//Fracciones totales
	if($fraccionesTotales < $dosisTemporal){
		$factor = ceil((double)(round(($dosisTemporal-$saldoAnterior) / $fraccionesTotales,1)));	//Marzo 1 de 2011, se adiciona el round para que calcule correctamente las fracciones
	}
	
	if($saldoAnterior < $dosisTemporal){
		$dispensarNuevo = true;
		$saldoNuevo = ($saldoAnterior + ($fraccionesTotales * $factor)) - $dosisTemporal;
	}else{
		$saldoNuevo = $saldoAnterior - $dosisTemporal;
	}

	if($dispensarNuevo){
		$cantDispensar = $factor;
	}
	
	$cantDispensar += (integer)$saldoDispensacion;
}

/************************************************************************************************************************************************
 * Graba un articulo en el detalle del kardex.  Si existe lo actualiza caso contrario inserta
 *
 * Retorna un codigo de estado que puede caer en uno de los siguientes:
 *
 * 0->No inserto el registro
 * 1->Creo un nuevo item de detalle en el kardex
 * 2->Actualizo un item del detalle en el kardex
 *
 * @param unknown_type $wbasedato
 * @param unknown_type $historia
 * @param unknown_type $ingreso
 * @param unknown_type $codArticulo
 * @param unknown_type $cantDosis
 * @param unknown_type $unDosis
 * @param unknown_type $per
 * @param unknown_type $fmaFtica
 * @param unknown_type $fini
 * @param unknown_type $hini
 * @param unknown_type $ffin
 * @param unknown_type $via
 * @param unknown_type $conf
 * @param unknown_type $dtto
 * @param unknown_type $obs
 * @param unknown_type $usuario
 * @return unknown
 ************************************************************************************************************************************************/
function grabarArticuloDetalle($wbasedato,$historia,$ingreso,$fechaKardex,$codArticulo,$cantDosis,$unDosis,$per,$fmaFtica,$fini,$hini,$via,$conf,$dtto,$obs,$origenArticulo,$codUsuario,$condicion,$dosisMax,$cantGrabar,$unidadManejo,$cantidadManejo,$primerKardex,$horasFrecuencia,$fInicioAnt,$hInicioAnt,$noDispensar,$tipoProtocolo,$centroCostosGrabacion,$prioridad,$cantidadAlta,$impresion,$deAlta,$tipoManejo,$posologia,$unidadPosologia){
	$conexion = obtenerConexionBD("matrix");
	
	global $horaCorteDispensacion;
	global $wemp_pmla;
	
	$obs = utf8_decode( $obs );
	//Confirmada preparación.
	$conf == "true" ? $conf = 'on' : $conf = 'off';
	$noDispensar == "true" ? $noDispensar = 'on' : $noDispensar = 'off';
	$multiplicable = false;
	$fhInicialIgual = false;
	$existe = false;
	$esPrimerKardex = false;
	$estado = "0";
	$saldo = 0;
	$cantGrabar = 0;
	$cantDispensar = 0;
	$limiteCtc = false;
	$saldoDispensacion = 0;

	$cantidadAutorizadaCtc 	= "";
	$cantidadUtilizadaCtc 	= "";
	$unidadesCantidadesCtc 	= "";
	
	$cantDosis = utf8_decode( $cantDosis );

	if(!empty($nombreArticulo) && substr($nombreArticulo,0,1) == "*"){
		$nombreArticulo = substr($nombreArticulo,1);
	} else {
		$nombreArticulo = "";
	}
	
	$auxHorasFrecuencia = $horasFrecuencia;
	
	$cambioFecHorInicio = false;

	//Cuando los articulos son multiples se usa la hora de actualizacion como referente
	$horaActualizacion = "";

	/**********************************************************************************
	* Septiembre 20 de 2012
	*
	* Si hay días de tratamiento se convierten a dosis máxima. Los días de tratamiento deben
	* contar desde la hora de inicio del medicamento
	*
	*
	* Modificado: Octubre 25 de 2012. quito dosis adicional
	**********************************************************************************/
   /* 2013-08-09
   if( !empty( $dtto ) ){
		   
		   //Convierto los días de tratamiento a dosis máxima
		   // $dosisMax = floor( ($dtto*24)/$horasFrecuencia ) + 1;        //El adicional de uno es debido a que debe contar la dosis inicial
		   $dosisMax = floor( ($dtto*24)/$horasFrecuencia );        //Octubre 25 de 2012
		   
		   $dtto = '';
   }
   */
   /**********************************************************************************/
 
	if(!empty($fInicioAnt) && !empty($hInicioAnt)){
		
		//Junio 13 de 2012. Modifico consulta, no tengo en cuenta el centro de grabacion del articulo
		$q = "SELECT
				Kadart, Kadcfr, Kadufr, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadvia, Kadfec, Kadcon, Kadobs, Kadsus, Kadori, Kadcnd, Kaddma, Kadcan, Kaduma, Kadcma, Kadsad, Kaddis, Kadcdi, Kadreg, Kadcpx, Kadron, Kadfro, Kadaan, Kadcda, Kadcdt,Kaddan,Kadfum,Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf
			FROM
				{$wbasedato}_000054
			WHERE
				Kadart = '$codArticulo'
				AND Kadfec = '$fechaKardex'
				AND Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadpro = '$tipoProtocolo'
				AND Kadfin = '$fInicioAnt'
				AND Kadhin = '$hInicioAnt'";
		
		$q .= " UNION "; //Marzo 14 de 2011
		$q .= "SELECT
				Kadart, Kadcfr, Kadufr, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadvia, Kadfec, Kadcon, Kadobs, Kadsus, Kadori, Kadcnd, Kaddma, Kadcan, Kaduma, Kadcma, Kadsad, Kaddis, Kadcdi, Kadreg, Kadcpx, Kadron, Kadfro, Kadaan, Kadcda, Kadcdt, Kaddan, Kadfum, Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf
			FROM
				{$wbasedato}_000060
			WHERE
				Kadart = '$codArticulo'
				AND Kadfec = '$fechaKardex'
				AND Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadpro = '$tipoProtocolo'
				AND Kadfin = '$fInicioAnt'
				AND Kadhin = '$hInicioAnt';";
				
		
	} else {
		$q = "SELECT
				Kadart, Kadcfr, Kadufr, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadvia, Kadfec, Kadcon, Kadobs, Kadsus, Kadori, Kadcnd, Kaddma, Kadcan, Kaduma, Kadcma, Kaddis, Kadcdi, Kadsad, Kadreg, Kadcpx, Kadron, Kadfro, Kadaan, Kadcda, Kadcdt,Kaddan,Kadfum,Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf
			FROM
				{$wbasedato}_000060
			WHERE
				Kadart = '$codArticulo'
				AND Kadfec = '$fechaKardex'
				AND Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadpro = '$tipoProtocolo';";
	}

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	$ccoCM=ccoUnificadoCM(); //Se obtiene el Codigo de Central de Mezclas
	$ccoSF=ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
	//La solución a esto es consultar las fracciones, si no tiene fracciones se usan dias de estabilidad cero (no tiene)
	$tarti = $ccoCM;
	if($origenArticulo == "SF"){
		$tarti = $ccoSF;
	}

	$qf = "SELECT
			Defart, Deffra, Defdup, Defdis, Defcco, Defdie
		FROM
			{$wbasedato}_000059
		WHERE
			Defart = '$codArticulo'
			AND Defcco = '$tarti'
			AND Defest = 'on';";

	$respin = mysql_query($qf, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qf . " - " . mysql_error());
	$numf = mysql_num_rows($respin);

	$diasEstabilidad = "0";
	$dispensable = "on";
	$cantidadFracciones = "1";
	$duplicable = "on";

	while($infofr = mysql_fetch_array($respin)){
		$diasEstabilidad 	= $infofr['Defdie'];
		$dispensable 		= $infofr['Defdis'];
		$cantidadFracciones = $infofr['Deffra'];
		$duplicable 		= $infofr['Defdup'];
	}

	//Existencia del articulo
	if($num > 0){
		$existe = true;
	}

	//El articulo es multiplicable, se pueden grabar duplicados varias veces
	if($duplicable == 'on'){
		$multiplicable = true;
	}

	//Fecha y hora de inicio anterior son iguales a fecha y hora de inicio digitada
	if($hInicioAnt == $hini && $fini == $fInicioAnt){
		$fhInicialIgual = true;
	}

	if($primerKardex == "S"){
		$esPrimerKardex = true;
	}

	//Calculo de los dias de tratamiento actuales, Sumo 1 dia por el registro del dia actual que esta en temporal
	$diasTtoAcumulados = 0;
	$dosisTtoTotales = 0;
	obtenerDatosAdicionalesArticulo($historia,$ingreso,$codArticulo,$diasTtoAcumulados,$dosisTtoTotales);
	
	/************************************************************************************
	 * Marzo 26 de 2012
	 ************************************************************************************/
	 //Si hay cambio de frecuencia y no se ha modificado fecha y hora de inicio
	 //se debe calcular a partir de la ultima aplicacion de la fecha vieja
	if($existe){
		if( $fila['Kadper'] != $per ){
			
			$fechaHoraInicioAnterior = strtotime( trim( $fInicioAnt )." ".trim( $hInicioAnt ) );
			$fechaHoraInicioActual = strtotime( trim( $fini )." ".trim( $hini ) );
			
			
			//Si no se encuentra la fecha y hora de inicio
			if( $fechaHoraInicioActual == $fechaHoraInicioActual ){
			
				//Calculo la fecha y hora de la ultima aplicacion
				$ultimoSuministro = suministroAntesFechaCorte( date( "Y-m-d" ), date( "H:i:s" ), trim( $fini ), trim( $hini ), consultarFrecuencia( $conexion, $wbasedato, $fila['Kadper'] ) );
				
				if( $ultimoSuministro ){
					
					//Calculo ronda actual
					$auxfechaUnix = strtotime( "1970-01-01 00:00:00" );
					$rondaActual = intval( ( time()-$auxfechaUnix )/(2*3600) )*2*3600 + $auxfechaUnix;	//Ok
					
					if( $rondaActual - $ultimoSuministro < consultarFrecuencia( $conexion, $wbasedato, $per )*3600 ){
						$fini = date( "Y-m-d", $ultimoSuministro );
						$hini = date( "H:i:s", $ultimoSuministro );
						
						$cambioFecHorInicio = true;
					}
					
					// echo ".............111fehorini new: $fini $hini ::::: ant: ".trim( $fInicioAnt )." ".trim( $hInicioAnt );
				}
				else{
					// echo ".............222fehorini: $fini $hini ::::: ".trim( $fInicioAnt )." ".trim( $hInicioAnt );
				}
			}
		}
	}
	/************************************************************************************/
	
	/****************************************************************************************************************
	 * Mayo 23 de 2012
	 *
	 * Si el medicamento estaba como no enviar y lo cambian a enviar, genero el saldo del día anterior 
	 * y ademas venga de días anteriores y no se ha cambiado ni fecha ni hora de inicio ni frecuencia
	 ****************************************************************************************************************/
	$activacionNoEnviar = false;
	if($existe){
	
		if( $fila['Kadess'] == 'on' && $fila[ 'Kadreg' ] != '' ){
		
			$activacionNoEnviar = true;

			if( $fila['Kadper'] == $per && trim( $fini ) == $fila[ 'Kadfin' ] && substr( trim( $hini ),0,2 ) == substr( $fila[ 'Kadhin' ], 0, 2 ) && $fechaKardex != $fila[ 'Kadfin' ] 
				&& time() <= strtotime( "$fechaKardex $horaCorteDispensacion:00:00" )
			){

				//Calculo siguiente hora de aplicacion desde el momento de cambio
				$siguienteSuministro = suministroAntesFechaCorte( $fechaKardex , date( "H:00:00" ), trim( $fini ), trim( $hini ), $horasFrecuencia );
				
				//Si es verdadero significa que si hay fecha y hora de inicio anterior
				if( $siguienteSuministro ){
					
					//Aumento una frecuencia, ya que no se puede pedir la ronda actual
					$siguienteSuministro += $horasFrecuencia*3600;
					
					//Calculo el saldo del día anterior 
					//$fila[ 'Kadsad' ] = calcularCantidadGrabar( $fechaKardex, $fechaUsar, $horaUsar, $horasFrecuencia, $esPrimeraVez, $dosisMaximas, $aplicacionesAnteriores, intval( $saldoDispensacion/($cantDosis/$cantidadFracciones) ) );
					$fila[ 'Kadsad' ] = calcularCantidadGrabar( date( "Y-m-d", strtotime( $fechaKardex." 00:00:00" )-24*3600 ), date( "Y-m-d", $siguienteSuministro ), date( "H:i:s", $siguienteSuministro ), $horasFrecuencia, true, '', '', '' );
				
					$fila[ 'Kadsad' ] = floor( $fila[ 'Kadsad' ]*( $fila[ 'Kadcfr' ]/$fila[ 'Kadcma' ] ) );
					//$fila[ 'Kaddis' ] = 0;
					
					//Calculo la cantidad de aplicaciones
					$fila[ 'Kadcan' ] = calcularCantidadGrabar( $fechaKardex, $fila[ 'Kadfin' ], $fila[ 'Kadhin' ], consultarFrecuencia( $conexion, $wbasedato, $fila[ 'Kadper' ] ), false, '', '', '' );
				}
			}
			else{

				//Calculo siguiente hora de aplicacion desde el momento de cambio
				//$siguienteSuministro = suministroAntesFechaCorte( $fechaKardex ,gmdate( "H:00:00", floor( date( "H" )/2 )*2*3600 ), trim( $fini ), trim( $hini ), $horasFrecuencia );
				$siguienteSuministro = suministroAntesFechaCorte( $fechaKardex ,gmdate( "H:00:00", floor( date( "H" )/2 )*2*3600 ), $fila[ 'Kadfin' ], $fila[ 'Kadhin' ], consultarFrecuencia( $conexion, $wbasedato, $fila[ 'Kadper' ] ) );
				
				//Si es verdadero significa que si hay fecha y hora de inicio anterior
				if( $siguienteSuministro ){
				
					$siguienteSuministro += $horasFrecuencia*3600;
					
					//$fila[ 'Kadcan' ] = calcularCantidadGrabar( $fechaKardex, $horaCorteDispensacion.":00:00", date( "Y-m-d", $siguienteSuministro ), date( "H:i:s", $siguienteSuministro ), $horasFrecuencia, false, '', 0, 0 );
					$fila[ 'Kadcan' ] = calcularCantidadGrabar( $fechaKardex, date( "Y-m-d", $siguienteSuministro ), date( "H:i:s", $siguienteSuministro ), consultarFrecuencia( $conexion, $wbasedato, $fila[ 'Kadper' ] ), true, '', '', '' );
					//$fila[ 'Kaddis' ] = 0;
				}
				
				$fila[ 'Kadsad' ] = 0;
			}
		}
	}
	/****************************************************************************************************************/
	
	// echo "...reg Anterior: ".$fila[ 'Kadreg' ];
	//Calculo del saldo actual con base en el anterior
	$horasAplicacionDia = "";
	// calcularSaldoActual($conexion,$wbasedato,$historia,$ingreso,$fechaKardex,$codArticulo,$fInicioAnt,$hInicioAnt,$cantDosis,$horasFrecuencia,$dosisMax,( empty( $fila['Kadori'] ) )? $origenArticulo: $fila['Kadori'],$diasTtoAcumulados+1,&$cantGrabar,&$saldo,&$cantDispensar,&$cantidadManejo,$fila['Kadsad'], $tipoProtocolo,$horasAplicacionDia, $fila['Kadreg'] );
	calcularSaldoActual($conexion,$wbasedato,$historia,$ingreso,$fechaKardex,$codArticulo,trim($fini)       ,substr( trim( $hini ),0,2 ).":00:00"       ,$cantDosis,$horasFrecuencia,$dosisMax    ,( empty( $fila['Kadori'] ) )? $origenArticulo: $fila['Kadori'],$diasTtoAcumulados+1,$cantGrabar,  $saldo   ,$cantDispensar,$cantidadManejo,$fila['Kadsad']   , $tipoProtocolo, $horasAplicacionDia , $dtto,$fila['Kadreg']       );

 // calcularSaldoActual($conexion,$wbasedato,$historia,$ingreso,$fechaKardex,$codArticulo,    $fechaInicio  ,$horaInicio                                ,$cantDosis,$horasFrecuencia,$dosisMaximas,$ccoOrigen                                                    ,$diasTtoAcumulados  ,&$cantGrabar,&$saldoNuevo,&$cantDispensar,&$cantManejo    ,$saldoDispensacion, $tipoProtocolo, &$horasAplicacionDia,$diasTto,$idRegDiaAnterior = '' )
	
	// if($dispensable == 'off' || $noDispensar == 'on'){
		// $cantGrabar = 0;
		// $saldo = 0;
		// $cantDispensar = 0;
	// }

	//Si el articulo se encuentra suspendido no puede modificarlo.
	if(@$fila['Kadsus'] != 'on'){
	
		if($dispensable == 'off' || $noDispensar == 'on'){
			$cantGrabar = 0;
			$saldo = 0;
			$cantDispensar = 0;
		}
		
		/**********************************************************************
		 * Julio 24 de 2012
		 *
		 * Todos los articulos de lactario deben quedar siempre aprobados
		 **********************************************************************/
		$aprobado = 'off';
		
		//Consulto el grupo del medicamento
		$sqlGrupo = "SELECT
						Artgru
					 FROM
						{$wbasedato}_000026
					 WHERE
						artcod = '$codArticulo'
					";
					
		$resGrupo = mysql_query( $sqlGrupo, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numGrupo = mysql_num_rows( $resGrupo );
		
		if( $numGrupo > 0 ){
		
			$rowGrupo = mysql_fetch_array( $resGrupo );
			
			list( $rowGrupo[ 'Artgru' ] ) = explode( "-", $rowGrupo[ 'Artgru' ] );
			
			//Consulto si el articulo pertenece a lactario
			$sql = "SELECT
						Ccogka
					FROM
						{$wbasedato}_000011
					WHERE
						ccolac = 'on'
						AND ccoest = 'on'
						AND Ccogka != '*'
					";
					
			$resArtLacatrio = mysql_query( $sql, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numLac = mysql_num_rows( $resArtLacatrio );
			
			if( $numLac > 0 ){
				$rows = mysql_fetch_array( $resArtLacatrio );
				
				$artsLactario = explode( ",", $rows['Ccogka'] );
				
				foreach( $artsLactario as $keyLactario => $valueLactario ){
				
					if( strtoupper( $rowGrupo[ 'Artgru' ] ) == strtoupper( $valueLactario ) ){
						$aprobado = 'on';
						break;
					}
				}
			}
		}
		/**********************************************************************/
		
		
//
		//Si el articulo es duplicable y la fecha y hora de inicio son diferentes siempre insert
//		if(!$existe || ($existe && $multiplicable && !$fhInicialIgual)){
		if(!$existe){
			
			$audAnterior = "";
			
			// if( !tieneCpxPorHistoria( $conexion, $wbasedato, $historia, $ingreso ) ){
				// $horasAplicacionDia = "";
			// }
			// else{
				// //Octbure 26 de 2011
				// //Si el articulo comienza a media noche, debe mostrar
				// if( $fechaKardex == trim( $fini ) && substr( trim( $hini ), 0, 2 ) == "00" ){
					// $horasAplicacionDia = "Ant-".round($cantDosis/$cantidadFracciones,3)."-0,".$horasAplicacionDia;
				// }
			// }

			$q = "INSERT INTO ".$wbasedato."_000060
					(Medico,Fecha_data,Hora_data,Kadhis, Kading, Kadart, Kadcfr, Kadufr, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadvia, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kaddma, Kadcan, Kaduma, Kadcma, Kaddis, Kadhdi, Kadsal, Kadcdi, Kadpri, Kadpro, Kadcco, Kadare, Kadsad, Kadnar, Kadreg, Kadusu, Kadcpx, Kadcal, Kadimp, Kadalt, Kadint, Kadpos, Kadupo, Seguridad)
				VALUES
					('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','$historia','$ingreso','$codArticulo','$cantDosis','$unDosis','$dtto','on','$noDispensar','$per','$fmaFtica','$fini','$hini','$via','".$fechaKardex."','$conf','".mysqli_real_escape_string( $conexion, $obs )."','$origenArticulo','off','$condicion','$dosisMax','$cantGrabar','$unidadManejo','$cantidadManejo','0','00:00','$saldo','$cantDispensar','$prioridad','$tipoProtocolo','$centroCostosGrabacion','$prioridad','$saldoDispensacion','$nombreArticulo','','$codUsuario','$horasAplicacionDia','$cantidadAlta','$impresion','$deAlta','$tipoManejo','$posologia','$unidadPosologia','A-$codUsuario')";

			$estado = "1";
		} else {
			
			$fila['Kadobs'] = str_replace( "\\", "\\\\", $fila['Kadobs'] );	//Marzo 3 de 2011
			$fila['Kadobs'] = str_replace( "'", "\'", $fila['Kadobs'] );	//Marzo 3 de 2011
			
			$audAnterior = "A:".$fila['Kadart'].",".$fila['Kadcfr'].",".$fila['Kadufr'].",".$fila['Kadper'].",".$fila['Kadffa'].",".$fila['Kadfin'].",".$fila['Kadhin'].",".$fila['Kadvia'].",".$fila['Kadcon'].",".$fila['Kaddia'].",".$fila['Kadobs'].",".$fila['Kadori'].",".$fila['Kadcnd'].",".$fila['Kaddma'].",".$fila['Kadcan'].",".$fila['Kaduma'].",".$fila['Kadcma'].",".$codUsuario;
			
			
			
			// /********************************************************************************************************************************************
			 // * Marzo 06 de 2012
			 // * Si hay cambio en el medicamento, y tiene saldo, recalculo saldo
			 // ********************************************************************************************************************************************/
			
			
			// //Si no hubo cambio por fecha y hora de inicio dejo la ronda actual como la ronda antrior a la fecha y hora de inicio
			// if( !$cambioFecHorInicio ){
				// //Calculo ronda actual
				// $auxfechaUnix = strtotime( "1970-01-01 00:00:00" );
				// $rondaActual = intval( ( time()-$auxfechaUnix )/(2*3600) )*2*3600 + $auxfechaUnix;	//Ok
			// }
			// else{
				// //Si hubo cambio dejo la ronda actual como la inmediamente anterior a la fecha y hora de inicio
				// $rondaActual = strtotime( trim( $fini )." ".trim( $hini ) ) - 2*3600;
				// // echo "\n...ronda Actual: ".date( "Y-m-d H:i:s", $rondaActual );
				// // $fila[ 'Kadcan' ] = $cantGrabar;
			// }
			
			// $saldoDeDispensacionAnterior = $fila['Kadsad'];
			
			// $fechaCorteActual = strtotime( "$fechaKardex $horaCorteDispensacion:00:00" );
			// $fechaHoraInicioAnterior = strtotime( trim( $fInicioAnt )." ".trim( $hInicioAnt ) );
			
			// $fechaHoraInicioActual = strtotime( trim( $fini )." ".trim( $hini ) );
			// // $fechaHoraInicioActual = intval( ( time() - strtotime( "1970-01-01 00:00:00" ) )/2*3600 )*2*3600 + strtotime( "1970-01-01 00:00:00" );
			
			// $auxHorasFrecuencia = consultarFrecuencia( $conexion, $wbasedato, $fila['Kadper'] );
			
			// //Si es Modificado por fecha y hora, frecuencia o cantidad de dosis
			// if( $fechaHoraInicioAnterior != $fechaHoraInicioActual //&& !empty( $fila['Kadsad'] ) 
				// || $fila['Kadper'] != $per
				// || $fila[ 'Kadcfr' ] != $cantDosis
			// ){
			
				
				// //Consulto la ultima ronda de aplicacion de acuero a la fecha y hora de inicio nueva
				// $ultimoSuministroDiaSiguienteNuevo = suministroAntesFechaCorte( date( "Y-m-d", strtotime( $fechaKardex." 00:00:00" )+24*3600 ), "$horaCorteDispensacion:00:00", trim( $fini ), trim( $hini ), $horasFrecuencia );
				
				// //Calculo la fecha y hora final de terminacion de medicamento por dosis maximas
				// if( !empty( $dosisMax ) ){
					// $auxSN = $fechaHoraInicioActual + ($dosisMax-1)*$horasFrecuencia*3600;
					// $ultimoSuministroDiaSiguienteNuevo = min( $auxSN, $ultimoSuministroDiaSiguienteNuevo );
				// }
				
				// //Calculo la fecha y hora final de terminacion de medicamento por dias de tratamiento
				// if( !empty( $dtto ) ){
					// $auxSN = strtotime( date( "Y-m-d 00:00:00", $fechaHoraInicioActual ) ) + ( $dtto )*24*3600;
					
					// //Esto da el total de aplicaciones por dosis maximas
					// $auxSN = intval( ( $auxSN - $ultimoSuministroDiaSiguienteNuevo )/( $horasFrecuencia*3600 ) );
					
					// $auxSN = $fechaHoraInicioActual + ($auxSN)*$horasFrecuencia*3600;
					
					// $ultimoSuministroDiaSiguienteNuevo = min( $auxSN, $ultimoSuministroDiaSiguienteNuevo );
				// }
				
				// //Resto el total de aplicaciones calculadas para saber la hora de inicio tomada inicialmente
				// $fechorIniNuevo = $ultimoSuministroDiaSiguienteNuevo - ($cantGrabar-1)*$horasFrecuencia*3600; //Ok
				
				// //Busco la hora de inicio real
				// //Si se cambio la fecha y hora de inicio esa es la hora de cambio real
				// if( $fechaHoraInicioAnterior != $fechaHoraInicioActual ){
					// $ultimoSuministroDiaActualViejo = $fechaHoraInicioActual;
				// }
				// else{
					// //Si no se cambio fecha y hora de inicio, es la siguiten hora de aplicacion para el medicamento
					
					// //Calculo ronda actual
					// // $auxfechaUnix = strtotime( "1970-01-01 00:00:00" );
					// // $fechaHoraInicioActual = intval( ( time()-$auxfechaUnix )/(2*3600) )*2*3600 + $auxfechaUnix;	//Ok
					
					// // //Calculo ronda actual
					// // $auxfechaUnix = strtotime( "1970-01-01 00:00:00" );
					// // $rondaActual = intval( ( time()-$auxfechaUnix )/(2*3600) )*2*3600 + $auxfechaUnix;	//Ok
					
					// //Busco ultima hora de aplicacion antes de la ronda actual
					// $ultimoSuministroDiaActualViejo = suministroAntesFechaCorte( date( "Y-m-d", $rondaActual ), date( "H:i:s", $rondaActual ), date( "Y-m-d", $fechaHoraInicioAnterior ), date( "H:i:s", $fechaHoraInicioAnterior ), $horasFrecuencia );
		

					// //Si ultimoSuministroDiaActualViejo es falso significa que el medicamento comienza posterior a la ronda actual
					// if( !$ultimoSuministroDiaActualViejo ){
						// $ultimoSuministroDiaActualViejo = $fechaHoraInicioAnterior;
					// }
					
					
					// //Como esta era la aplicacion anterior a la ronda actual anterior, calculo la nuevo
					// //Solo si es anterior a la ronda actual
					// if( $rondaActual >= $ultimoSuministroDiaActualViejo ){
						// $ultimoSuministroDiaActualViejo += $horasFrecuencia*3600;
					// }
				// }
				
				
				// //Calculo la cantidad de aplicaciones hasta el día siguiente
				// $cantAplicacionesReal = 0;
				// if( $ultimoSuministroDiaSiguienteNuevo >= $ultimoSuministroDiaActualViejo ){	//Marzo 9 de 2012, no tenía el =, solo esta el >
					// $cantAplicacionesReal = ( $ultimoSuministroDiaSiguienteNuevo - $ultimoSuministroDiaActualViejo )/($horasFrecuencia*3600) + 1;
					
					// // echo "\n\n....cantAplicacionesReal = ( ultimoSuministroDiaSiguienteNuevo - ultimoSuministroDiaActualViejo )/(horasFrecuencia*3600) + 1;";
					// // echo "\n\n....$cantAplicacionesReal = ( $ultimoSuministroDiaSiguienteNuevo - $ultimoSuministroDiaActualViejo )/($auxHorasFrecuencia*3600) + 1;";
				// }
				
				// //Calculo la cantidad necesaria hasta el dia siguiente
				// $cantNecesariaHastaHoraCorteDiaSiguiente = ceil( $cantAplicacionesReal*($cantDosis/$cantidadManejo) );

				// //Si hay diferencia esta pertenece al saldo de dispensacion
				// $auxSaldoDispensacion = $cantNecesariaHastaHoraCorteDiaSiguiente - ( $cantDispensar - $saldoDeDispensacionAnterior );
				// // echo "\n\n....auxSaldoDispensacion = cantNecesariaHastaHoraCorteDiaSiguiente - ( cantDispensar - saldoDeDispensacionAnterior );";
				// // echo "\n....$auxSaldoDispensacion = $cantNecesariaHastaHoraCorteDiaSiguiente - ( $cantDispensar - $saldoDeDispensacionAnterior );";

				
				// /*************************************************************************************************
				 // * Calculo cuantas aplicaciones hay antes del cambio de parametros para el articulo
				 // *************************************************************************************************/
				// //Primero miro cuantas aplicaciones se dejaron de dispensar
				// $totalAplicacionesPorSaldo = $saldoDeDispensacionAnterior/($fila[ 'Kadcfr' ]/$fila['Kadcma']);
				
				// //Consulta las aplicaciones hasta el dia acutal a la hora de corte
				// $ultimoSuministroHoyCorteDispensacion = suministroAntesFechaCorte( "$fechaKardex", "$horaCorteDispensacion:00:00", trim( $fila['Kadfin'] ), trim( $fila['Kadhin'] ), $auxHorasFrecuencia );
				
				// //Resto el total de aplicaciones que hay por saldo
				// $ultimoSuministroHoyCorteDispensacion = $ultimoSuministroHoyCorteDispensacion - ($totalAplicacionesPorSaldo-1)*$auxHorasFrecuencia*3600;	//Ok
				
				// //Calculo cuantas aplicaciones estan por fuera del rango
				// //fecha inicial de cambio y fecha final de corte dia actual
				// $totAplicaciones = 0;
				
				// // //Calculo ronda actual
				// // $auxfechaUnix = strtotime( "1970-01-01 00:00:00" );
				// // $rondaActual = intval( ( time()-$auxfechaUnix )/(2*3600) )*2*3600 + $auxfechaUnix;	//Ok
				
								
				// //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				// // Calculo cuantas aplicaciones hay antes del cambio de parametros para el articulo
				// //
				// // Calculando el saldo fijo del articulo
				// //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				
				// /**
				// Lo forma de calculo es la siguiente:
				
				// Calculo cuantas aplicaciones hay hasta el día siguiente a partir de la ronda actual hasta el día siguiente
				// a este calculo le resto la cantidad calculada segun lo datos anteriores
				// La diferencia de los dos los resto al saldo que aparece del articulo
				// El resultado se tiene que dejar en el saldo, ya que el resultado indica cuanto saldo se debe dejar
				// */
				
				// //Calculo ronda actual
				
				
				
				// //Consulto las aplicaciones hasta la fecha y hora final de aplicacion
				// //Esta es, hasta el día siguiente a la hora de cambio o segun la dosis maximas o dias de tratamiento
				// $fechaFinalizacionMedicamento = strtotime( "$fechaKardex $horaCorteDispensacion:00:00" ) + 24*3600;
				
				// //Si hay dosis maximas
				// if( !empty( $fila['Kaddma'] ) ){
					// $fechaFinalizacionMedicamento = min( $fechaHoraInicioAnterior + ($fila['Kaddma']-1)*$auxHorasFrecuencia*3600, 
														 // $fechaFinalizacionMedicamento );
				// }
				
				// //fecha y hora de finalizacion del medicamento
				// $ultimoSuministroFecHorFinalMed = suministroAntesFechaCorte( date( "Y-m-d" , $fechaFinalizacionMedicamento ), 
																		     // date( "H:i:s", $fechaFinalizacionMedicamento ), 
																		     // trim( $fila['Kadfin'] ), 
																		     // trim( $fila['Kadhin'] ), 
																		     // $auxHorasFrecuencia );
				
				// //Ultimo suministro antes de la ronda actual
				// $ultimoSuministroRondaActual = suministroAntesFechaCorte( date( "Y-m-d" , $rondaActual ), 
																		     // date( "H:i:s", $rondaActual ), 
																		     // trim( $fila['Kadfin'] ), 
																		     // trim( $fila['Kadhin'] ), 
																		     // $auxHorasFrecuencia );
				
				// //Si es falso es por que la fecha de inicio del medicamento es posterior
				// //por tanto debe se igual a la fecha de inicio del medicamento
				// if( !$ultimoSuministroRondaActual ){
					// $ultimoSuministroRondaActual = $fechaHoraInicioAnterior;
				// }
																			 
				// if( $ultimoSuministroRondaActual <= $rondaActual ){
					// $ultimoSuministroRondaActual += $auxHorasFrecuencia*3600;
				// }
				
				// //Teniendo la fecha final de medicamentos Calculo el total de aplicaciones desde la ronda actual hasta
				// //fecha finalizacion de medicamentos
				// $totAplAux = 0;
				// // echo "\n.....ultimoSuministroFecHorFinalMed: ".date( "Y-m-d H:i:s", $ultimoSuministroFecHorFinalMed );
				// // echo "\n.....ultimoSuministroRondaActual: ".date( "Y-m-d H:i:s", $ultimoSuministroRondaActual );
				// if( $ultimoSuministroFecHorFinalMed >= $ultimoSuministroRondaActual && $ultimoSuministroRondaActual >= $fechaHoraInicioAnterior ){
					// $totAplAux = ( $ultimoSuministroFecHorFinalMed - $ultimoSuministroRondaActual )/($auxHorasFrecuencia*3600) + 1;
					// // echo "\n\n....totAplAux";
					// // echo "\n....$totAplAux";
				// }
				
				// // echo "\n\n....1: totAplAux";
				// // echo "\n....2: $totAplAux";
				
				// // $totalArticulosAux = $totAplAux*$fila[ 'Kadcfr' ]/$fila[ 'Kadcma' ];
				
				// $totalArticulosAux = ( $totAplAux - $fila[ 'Kadcan' ] )*$fila[ 'Kadcfr' ]/$fila[ 'Kadcma' ];
				// // echo "\n\ntotalArticulosAux = ( totAplAux - fila[ 'Kadcan' ] )*{fila[ 'Kadcfr' ]/fila[ 'Kadcma' ];";
				// // echo "\n$totalArticulosAux = ( $totAplAux - {$fila[ 'Kadcan' ]} )*{$fila[ 'Kadcfr' ]}/{$fila[ 'Kadcma' ]};";

				// $auxSaldoCant = ceil( $fila[ 'Kadsad' ] - $totalArticulosAux );
				// // $auxSaldoCant = $fila[ 'Kadsad' ] - ( $totalArticulosAux - ( $fila[ 'Kadcdi' ] - $fila[ 'Kadsad' ] ) );
				
				// // echo "\n\n....auxSaldoCant = ceil( fila[ 'Kadsad' ] - totalArticulosAux )";
				// // echo "\n....$auxSaldoCant = ceil( {$fila[ 'Kadsad' ]} - $totalArticulosAux )";
				
				// //Si este saldo es negativo significa que ya habían dispensado esa cantidad
				// if( $auxSaldoCant < 0 ){
				
					// $fila[ 'Kaddis' ] += abs($auxSaldoCant);
					// $auxSaldoCant = 0;
				// }
				// // echo "\n....dis1: ".$fila[ 'Kaddis' ];
				// //=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			
				
				// /********************************************************************************/
				
				
				
				// //Si la dispensacion es negativa significa ese valor pertenece al saldo de dispensacion del dia anterior
				// if( $fila['Kaddis'] < 0 ){
					// // $saldoDeDispensacionAnterior += $fila['Kaddis']*(-1);
					// $fila['Kaddis'] = 0;
				// }
				
				// $saldoDeDispensacionAnterior = $auxSaldoCant + $auxSaldoDispensacion;
				// // echo "\n\n....saldoDeDispensacionAnterior = auxSaldoCant + auxSaldoDispensacion";
				// // echo "\n....$saldoDeDispensacionAnterior = $auxSaldoCant + $auxSaldoDispensacion";
			// }
			// /********************************************************************************************************************************************/

			
			// /********************************************************************************************************************************************
			 // * Julio 15 de 2011
			 // * 
			 // * Si se ha cambiado la frecuencia, o la dosis, o la fecha y hora de inicio se cambia el campo kadcpx
			 // ********************************************************************************************************************************************/
			// if( tieneCpxPorHistoria( $conexion, $wbasedato, $historia, $ingreso ) ){
			
				// //Busco la cantidad no cargada ayer
				// $sinCargarAnterior = "";
				// $fila['Kadcpx'] = trim($fila['Kadcpx']);
				// if( !empty( $fila['Kadcpx'] ) ){
					// $exp1 = explode( ",", $fila['Kadcpx'] );
					// $exp2 = explode( "-", $exp1[0] );
					
					// if( $exp2[0] == "Ant" ){
						// $sinCargarAnterior = "Ant-".$exp2[1]."-0,";
					// }
				// }

				// if( ( trim( $fInicioAnt ) != trim($fini) || ( trim( $fInicioAnt ) == trim( $fini ) && trim( $hini ) != trim( $hInicioAnt ) ) ) //Verificando cambio de fecha y hora de inicio
					// || $fila['Kadcfr'] != $cantDosis	//verifico si se cambio la cantidad de dosis
					// || $fila['Kadper'] != $per			//verifico frecuencia diferente
					// || $fila['Kadcma'] != $cantidadManejo			//verifico cantidad de manejo
				// ){
					// $horasAplicacionDia = "";
					// $fila['Kadron'] = '';
					// $fila['Kadfro'] = '';
					
					
					
					// /****************************************************************************************************************
					 // * creando string con las aplicaciones de las hora 
					 // * @var unknown_type
					 // ****************************************************************************************************************/
					// $info = consultarinfotipoarticulosKardex( $conexion, $wbasedato );
					
					// //Creando la regleta
					// $vectorAplicacion = obtenerVectorAplicacionMedicamentos( $fechaKardex, trim( $fini ), $hini, $horasFrecuencia );
					// $vectorAplicacion2 = obtenerVectorAplicacionMedicamentos( date( "Y-m-d", strtotime( $fechaKardex." 00:00:00" )+24*3600 ), trim( $fini ), $hini, $horasFrecuencia );
					// $vectorAplicacion2 = arreglarVectorKardex( $vectorAplicacion2 );

					// $quitarUnoARegleta = 25;
					// if( count($vectorAplicacion2) == 25 ){
						// $quitarUnoARegleta = 24;
					// }			
					
					// foreach( $vectorAplicacion2 as $keyB => $valueB ){
						// $vectorAplicacion[$quitarUnoARegleta] = $valueB;
						// $quitarUnoARegleta++;
					// }
									
					// $horasAplicacionDia = crearVectorAplicaciones( $vectorAplicacion, $info[ $tipoProtocolo ]['tiempoPreparacion'], $cantDosis/$cantidadManejo, intval( ( strtotime( "1970-01-01 ".$info[ $tipoProtocolo ]['horaCaroteDispensacion'] ) - strtotime( "1970-01-01 00:00:00" ) )/3600 ) );
					
					
					
					// /****************************************************************************************************************************************************************
					 // * Septiembre 20 de 2011
					 // *
					 // * Consulto cuanto es cantidad de dosis que se deben cargar al paciente
					 // *
					 // * Reglas
					 // * - Si no se modifica fecha y hora de inicio significa que cualquier dosis antes de la hora actual se debe quedar como cargada
					 // * - Se debe sumar al cantidad a dispensar (cdi) la cantidad dispensada (dis) que hay hasta el momento 
					 // ****************************************************************************************************************************************************************/

					// if( $rondaActual < strtotime( date( "Y-m-d 00:00:00" ) ) ){
						// $rondaActual = strtotime( date( "Y-m-d 00:00:00" ) );
					// }
					
					// if( !( trim( $fInicioAnt ) != trim( $fini ) || ( trim( $fInicioAnt ) == trim( $fini ) && trim( $hini ) != trim( $hInicioAnt ) ) ) ){ //Si no han modificado fecha y hora de inicio
						// //Consultar ultima ronda que pudo ser cargada segun la hora actual
						// $ultimaRonda = ( intval( date( "H", $rondaActual )/2 )*2 );
					// }
					// else{
						// //Busco la hora de inicio nueva
						// list( $ultimaRonda ) = explode( ":", $hini );
						// // $fini = trim( $fini );
						// if( trim( $fini ) == date( "Y-m-d", $rondaActual ) ){	//Si la fecha de inicio es la misma que la actual
						
							// if( $ultimaRonda > date( "H", $rondaActual ) ){	//Si la hora de inicio es mayor a la actual
								
								// $ultimaRonda = ( intval( date( "H", $rondaActual )/2 )*2 );	//Dejo la ultima hora par como hora de cambio								
							// }
							// else{	//Si la hora de inicio es antes a la actual
								// $ultimaRonda -= 2;	//Quito dos horas, esto por que la hora de inicio puede tener medicamentos a favor para la nueva hora
							// }
						// }
						// else{	//Si la fecha de inicio es en otro dia
							
							// //Consultar ultima ronda que pudo ser cargada segun la hora actual
							// $ultimaRonda = ( intval( date( "H", $rondaActual )/2 )*2 );
						// }
					// }
					
					// //Creo la hora correctamente en formato hora:min:seg
					// if( $ultimaRonda >= 10 ){
						// $ultimaRonda = $ultimaRonda.":00:00";
					// }
					// else{
						// $ultimaRonda = "0".$ultimaRonda.":00:00";
					// }
					
					// if( date( "H", $rondaActual ) >= 2 ){
					
						// //Busco si hay posibles cantidades cargadas despues de la hora actual						
						// //Busco la cantidad total cargada durante el dia segun la regleta anterior
						// $cantidadCargada = calcularCantidadTotalDispensadaPorDia( $fila['Kadcpx'] );
						
						// //Busco la cantidad cargada hasta la ronda actual calculada
						// $cantidadCargadaHastaRonda = cantidadTotalDispensadaRonda( $fila['Kadcpx'], $ultimaRonda );
						
						// //Esta diferencia indica cuanto hay para cargar a la nueva regleta
						// $saldoRegletaAnterior = $cantidadCargada - $cantidadCargadaHastaRonda;
						
						// //Consulto cuanto es la cantidad total a dispensar hasta la ultima ronda
						// $cantidadTotalADispensar = cantidadTotalADispensarRonda( $horasAplicacionDia, $ultimaRonda );
						
						// $totalACargar = $cantidadTotalADispensar + $saldoRegletaAnterior;
						
						// $fila['Kaddis'] = intval( $totalACargar );
						// /****************************************************************************************************************************************************************/
						
						// //Dejando la cantidad pedida del dia anterior como estaba
						// $horasAplicacionDia = $sinCargarAnterior.$horasAplicacionDia;
					// }
					// else{
						// $totalACargar = cantidadTotalDispensadaRonda( $fila['Kadcpx'], $ultimaRonda );
					// }
						
					// //Dejando la regleta con la cantidad dispensada
					// //$horasAplicacionDia = crearAplicacionesCargadasPorHorasKardex( $horasAplicacionDia, $fila['Kaddis'] );
					// $horasAplicacionDia = crearAplicacionesCargadasPorHorasKardex( $horasAplicacionDia, $totalACargar );
					
					// list( $fila['Kadron'], $fila['Kadfro'] ) = explode( "|", consultarUltimaRondaDispensadaKardex( $horasAplicacionDia ) );
					
					// /**************************************************************************************************************
					 // * Noviembre 1 de 2011
					 // **************************************************************************************************************/
					// if( !empty( $fila['Kadron'] ) ){
						
						// if( empty( $fila['Kadfro'] ) ){
							// $fila['Kadfro'] = date( "Y-m-d", strtotime( trim( $fechaKardex )." 00:00:00" ) + 3600*24 );
						// }
						// else{
							// $fila['Kadfro'] = $fechaKardex;
						// }
					// }
					// /**************************************************************************************************************
					 // * Fin de noviembre 1
					// /**************************************************************************************************************/
					// /********************************************************************************/
				// }
				// else{
					// $horasAplicacionDia = $fila['Kadcpx'];
				// }
			// }
			// else{
				// $horasAplicacionDia = '';				
			// }
			// /********************************************************************************************************************************************/
			
			/************************************************************************************************************************************
			 * Febrero 20 de 2012
			 *
			 * Si la cnatidad de dosis fue modificada, guardo la ultima cantidad de dosis y la fecha y hora de modificacion
			 ************************************************************************************************************************************/
			if( $fila[ 'Kadcfr' ] != $cantDosis ){
				$cantidadDosisAnterior = $fila[ 'Kadcfr' ];
				$fechaUltimaModificacion = date( "Y-m-d" );
				$horaUltimaModificacion = date( "H:i:s" );
			}
			else{
				$cantidadDosisAnterior = $fila[ 'Kaddan' ];
				$fechaUltimaModificacion = $fila[ 'Kadfum' ];
				$horaUltimaModificacion = $fila[ 'Kadhum' ];
			}
			/************************************************************************************************************************************/
			
			/************************************************************************************************************************************
			 * Febrero 23 de 2012
			 *
			 * Si cambia la frecuencia, registro la frecuencia anterior y la fecha y hora de cambio
			 ************************************************************************************************************************************/
			if( $fila['Kadper'] != $per ){
				$frecuenciaAnterior = $fila[ 'Kadper' ];
				$fcFrecuencia = date( "Y-m-d" );
				$hcFrecuencia = date( "H:i:s" );
			}
			else{
				$frecuenciaAnterior = $fila[ 'Kadfra' ];
				$fcFrecuencia = $fila[ 'Kadfcf' ];
				$hcFrecuencia = $fila[ 'Kadhcf' ];
			}
			/************************************************************************************************************************************/
			
			// $saldoDeDispensacionAnterior = -1000;
			if( $saldoDeDispensacionAnterior < 0 ){
				$cantDispensar += abs( $saldoDeDispensacionAnterior );
				$saldoDeDispensacionAnterior = 0;
			}
			
			// $fila['Kaddis'] = -1000;
			if( !empty($fila['Kaddis']) && $fila['Kaddis'] < 0  ){
				$fila['Kaddis'] = 0;
			}
			
			// $cantDispensar = -1000;
			if( $cantDispensar < 0 ){
				$cantDispensar = 0;
			}
				
			// echo "...dis an update: {$fila['Kaddis']}";
			
			
			
			/************************************************************************
			 * Mayo 23 de 2012
			 ************************************************************************/
			if($dispensable == 'off' || $noDispensar == 'on'){
				$cantGrabar = 0;
				$saldo = 0;
				$cantDispensar = 0;
				$saldoDeDispensacionAnterior = 0;
			}
			/************************************************************************/
			
			//Junio 14 de 2012. No actulizo el cco del articulo
			$q = "UPDATE ".$wbasedato."_000060 SET
					Kadcfr = '$cantDosis',
					Kadufr = '$unDosis',
					Kaddia = '$dtto',
					Kadper = '$per',
					Kadcnd = '$condicion',
					Kadffa = '$fmaFtica',
					Kadfin = '$fini',
					Kadhin = '$hini',
					Kadvia = '$via',
					Kaddma = '$dosisMax',
					Kadcon = '$conf',
					Kadcan = '$cantGrabar',
					Kaduma = '$unidadManejo',
					Kadcma = '$cantidadManejo',
					Kadobs = '".mysqli_real_escape_string( $conexion, $obs )."',
					Kadpri = '$prioridad',
					Kadsal = '$saldo',
					Kadcdi = '$cantDispensar',
					Kadess = '$noDispensar',
					Kadare = '$prioridad', 
					Kadcpx = '$horasAplicacionDia',
					Kadron = '{$fila['Kadron']}',
					Kadfro = '{$fila['Kadfro']}',
					Kaddis = '{$fila['Kaddis']}',
					Kadusu = '$codUsuario',
					Kaddan = '$cantidadDosisAnterior',
					Kadfum = '$fechaUltimaModificacion',
					Kadhum = '$horaUltimaModificacion',
					Kadsad = '$saldoDeDispensacionAnterior',
					Kadfra = '$frecuenciaAnterior',
					Kadfcf = '$fcFrecuencia',
					Kadhcf = '$hcFrecuencia',
					Kadcal = '$cantidadAlta',
					Kadimp = '$impresion',
					Kadalt = '$deAlta',
					Kadint = '$tipoManejo',
					Kadpos = '$posologia',
					Kadupo = '$unidadPosologia'
				WHERE
					Kadhis = '$historia'
					AND Kading = '$ingreso'
					AND Kadart = '$codArticulo'
					AND Kadfec = '$fechaKardex'
					AND Kadhin = '$hInicioAnt'
					AND Kadpro = '$tipoProtocolo'
					AND Kadfin = '$fInicioAnt'";

			$estado = "2";

			//Si el articulo esta completamente dispensado y la fecha y hora de inicio cambian, se muestra un mensaje de sobrante o faltante
			// if(($fInicioAnt != $fila['Kadfin'] && $hInicioAnt != $fila['Kadhin']) && $fila['Kaddis'] == $fila['Kadcdi']){
				// $estado = "4*".$fila['Kaddis']."*".$fila['Kadcdi'];
			// }
		}
		
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		
		if( $estado == "1" ){
			
			if( mysql_affected_rows() > 0 ){
				
				$idOriginal = mysql_insert_id();
				
				$sql = "UPDATE ".$wbasedato."_000060 
						SET
							kadido = '$idOriginal'
						WHERE
							id = '$idOriginal'
						";
				
				$res = mysql_query($sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
			}
		}
		else{
			$idOriginal = $fila[ 'Kadido' ];
		}

		$audNuevo = "N:$codArticulo,$cantDosis,$unDosis,$per,$fini,$hini,$via,$conf,$dtto,$obs,$origenArticulo,$condicion,$dosisMax,$cantGrabar,$cantidadManejo,$codUsuario";

		$mensajeAuditoria = "";
		switch ($estado){
			case "1":
				$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_CREADO');
				break;
			case "2":
				$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_ACTUALIZADO');
				break;
			default:
				$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_ACTUALIZADO');
				break;
		}

		//Registro de auditoria
		$auditoria = new AuditoriaDTO();

		$auditoria->historia = $historia;
		$auditoria->ingreso = $ingreso;
		$auditoria->descripcion = "$audAnterior \n\n$audNuevo";
		$auditoria->fechaKardex = $fechaKardex;
		$auditoria->mensaje = $mensajeAuditoria;
		$auditoria->seguridad = $codUsuario;
		$auditoria->idOriginal = $idOriginal;

		registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

		/******************************************
		 * 	MOVIMIENTOS DEL CTC
		 ******************************************/
		$q2 = "SELECT Ctccau,Ctccus,Ctcuca FROM {$wbasedato}_000095 WHERE Ctchis = '".$historia."' AND Ctcing = '".$ingreso."' AND Ctcart = '".$codArticulo."'";
		$res2 = mysql_query($q2, $conexion) or die ("Error: " . mysql_errno() . " - en el query: $q2 - " . mysql_error());

		while($info2 = mysql_fetch_array($res2)){
			$cantidadAutorizadaCtc 	= $info2['Ctccau'];
			$cantidadUtilizadaCtc 	= consultarCantidadAcumuladaDispensada($conexion,$wbasedato,$historia,$ingreso,$codArticulo);
			$unidadesCantidadesCtc 	= $info2['Ctcuca'];

			//****************************Actualizacion de datos CTC
			$q3 = "UPDATE {$wbasedato}_000095 SET Ctccus = '$cantidadUtilizadaCtc' WHERE Ctchis = '".$historia."' AND Ctcing = '".$ingreso."' AND Ctcart = '".$codArticulo."'";
			$res3 = mysql_query($q3, $conexion) or die ("Error: " . mysql_errno() . " - en el query: $q3 - " . mysql_error());
			//***************************
		}
		//***************************

		//liberarConexionBD($conexion);
	} else {
		$estado = "3";
	}
	return $estado;
}


function consultarAlergias($historia,$ingreso,$fecha){
	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT
			Fecha_data,Karale
		FROM 
			".$wbasedato."_000053
		WHERE 
			Karhis = '$historia'
			AND Karing = '$ingreso'
			AND Fecha_data < '$fecha'
			AND Karale != ''
		GROUP BY Karale";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$registro = new RegistroGenericoDTO();

			$info = mysql_fetch_array($res);

			$registro->codigo = $historia."-".$ingreso;
			$registro->descripcion = $info['Fecha_data'];
			$registro->observacion = $info['Karale'];

			$coleccion[] = $registro;
		}
	}
	return $coleccion;
}

function grabarArticuloDetallePerfil($wbasedato,$historia,$ingreso,$fechaKardex,$codArticulo,$dtto,$obs,$codUsuario,$via,$dosisMaximas,$prioridad,$fechaInicio,$horaInicio,$autorizadoCtc){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";
	$audAnterior = "";
	$audNuevo = "";

	//Primero verifico si ya existe el artículo en el detalle del kardex para saber si es insert o update
	$q = "SELECT
				Kadart, Kaddia, Kadobs, Kadvia, Kaddma, Kaduma, Kadcdi
			FROM
				".$wbasedato."_000054
			WHERE
				 Kadhis = '$historia'
				 AND Kading = '$ingreso'
				 AND Kadart = '$codArticulo'";

	$res 	= mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila 	= mysql_fetch_array($res);
	$num 	= mysql_num_rows($res);

	//Si existe el registro ACTUALIZO caso contrario INSERTO
	if($num > 0){
		$audAnterior = "A:".$fila['Kadart'].",".$fila['Kaddia'].",".$fila['Kadobs'].",".$fila['Kadvia'].",".$fila['Kaddma'].",".$fila['Kadpri'];
		
		/**********************************************************************************
		* Septiembre 20 de 2012
		*
		* Si hay días de tratamiento se convierten a dosis máxima. Los días de tratamiento deben
		* contar desde la hora de inicio del medicamento
		*
		*
		* Modificado: Octubre 25 de 2012. quito dosis adicional
		**********************************************************************************/
	   if( !empty( $dtto ) ){
			   
			   //Convierto los días de tratamiento a dosis máxima
			   // $dosisMax = floor( ($dtto*24)/$horasFrecuencia ) + 1;        //El adicional de uno es debido a que debe contar la dosis inicial
			   $dosisMax = floor( ($dtto*24)/$horasFrecuencia );        //Octubre 25 de 2012
			   
			   $dtto = '';
	   }
	   /**********************************************************************************/
		
		/************************************************************************************************************************
		 * Noviembre 16 de 2011
		 * Se pretende recalcular la cantidad a pedir de acuerdo a la dosis maxima
		 * Nota: copia del codigo que se encuentra en la funcion calcularSaldoActual con fecha de Enero 05 de 2011
		 ************************************************************************************************************************/
		//calcularSaldoActual($conexion,$wbasedato,$historia,$ingreso,$fechaKardex,$codArticulo,$fInicioAnt ,$hInicioAnt,    $cantDosis ,$horasFrecuencia                                            ,   $dosisMax ,( empty( $fila['Kadori'] ) )? $origenArticulo: $fila['Kadori'],$diasTtoAcumulados+1,&$cantGrabar,&$saldo,&$cantDispensar,&$cantidadManejo,$fila['Kadsad'], $tipoProtocolo ,$horasAplicacionDia, $fila['Kadreg'] );
		  calcularSaldoActual($conexion,$wbasedato,$historia,$ingreso,$fechaKardex,$codArticulo,$fechaInicio,$horaInicio,$fila['Kadcfr'], consultarFrecuencia( $conexion,$wbasedato,$fila['Kadper'] ),$dosisMaximas,$fila['Kadori']                                               ,$dtto+1             ,$cantGrabar,$saldo,$cantDispensar,$fila['Kadcma'],$fila['Kadsad'], $fila['Kadpro'],$horasAplicacionDia, $dtto, $fila['Kadreg'] );
		  
		/************************************************************************************************************************/

		$q = "UPDATE ".$wbasedato."_000054 SET
				Kaddia = '$dtto',
				Kadobs = '$obs',
				Kaddma = '$dosisMaximas',
				Kadvia = '$via',
				Kadpri = '$prioridad',
				Kadcan = '$cantGrabar',
				Kadcdi = '$cantDispensar'
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadart = '$codArticulo'
				AND Kadfec = '$fechaKardex'
				AND Kadfin = '$fechaInicio'
				AND Kadhin = '$horaInicio'";

		$estado = "2";
	}
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Generación de auditoria cambio / creación
	$audNuevo = "N:".$codArticulo.",".$dtto.",".$obs.",".$obs.",".$via.",".$dosisMaximas.",$autorizadoCtc";

	$mensajeAuditoria = "";

	switch ($estado){
		case "2":
			$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_MODIFICADO_DESDE_PERFIL');
			break;
		default:
			$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_NO_MODIFICADO_DESDE_PERFIL');
			break;
	}

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior $audNuevo";
	$auditoria->fechaKardex = $fechaKardex;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $codUsuario;
	$auditoria->idOriginal = $fila['Kadido'];

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

	//Movimiento de CTC
	$estado2 = "";

	//Actualizacion del valor disponible de ctc
	if(isset($autorizadoCtc) && !empty($autorizadoCtc)){
		$cantidadUsadaCtc = consultarCantidadAcumuladaDispensada($conexion,$wbasedato,$historia,$ingreso,$codArticulo);

		$q4 = "SELECT * FROM ".$wbasedato."_000095 WHERE Ctchis = '$historia' AND Ctcing = '$ingreso' AND Ctcart = '$codArticulo';";
		$res4 = mysql_query($q4, $conexion) or die ("Error: ".mysql_errno()." - en el query: $q4 - ".mysql_error());
		$num4 = mysql_num_rows($res4);

		if($num4 > 0){
			$q2 = "UPDATE ".$wbasedato."_000095 SET
				   	Ctccau = '$autorizadoCtc',
					Ctccus = '$cantidadUsadaCtc'
				WHERE
					Ctchis = '$historia'
					AND Ctcing = '$ingreso'
					AND Ctcart = '$codArticulo'";

			$res2 = mysql_query($q2, $conexion) or die ("Error: ".mysql_errno()." - en el query: $q2 - ".mysql_error());
			$estado2 = "2";
		} else {
			$q3 = "INSERT INTO ".$wbasedato."_000095
				(Medico,Fecha_data,Hora_data,Ctchis,Ctcing,Ctcart,Ctccau,Ctccus,Ctcuca,Seguridad)
			VALUES
				('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','$historia','$ingreso','$codArticulo','$autorizadoCtc','$cantidadUsadaCtc','{$fila['Kaduma']}','A-$usuario')";

			$res3 = mysql_query($q3, $conexion) or die ("Error: ".mysql_errno()." - en el query: $q3 - ".mysql_error());
			$estado2 = "1";
		}

		switch ($estado2){
			case "1":
				$mensajeAuditoria = obtenerMensaje('MSJ_CANTIDAD_CTC_CREADA');
				break;
			case "2":
				$mensajeAuditoria = obtenerMensaje('MSJ_CANTIDAD_CTC_MODIFICADA');
				break;
			default:
				$mensajeAuditoria = obtenerMensaje('MSJ_CANTIDAD_CTC_NO_ALTERADA');
				break;
		}

		//Registro de auditoria
		$auditoria = new AuditoriaDTO();

		$auditoria->historia = $historia;
		$auditoria->ingreso = $ingreso;
		$auditoria->descripcion = "$audAnterior $audNuevo";
		$auditoria->fechaKardex = $fechaKardex;
		$auditoria->mensaje = $mensajeAuditoria;
		$auditoria->seguridad = $codUsuario;

		registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

		$estado .= "|".$cantidadUsadaCtc;
	}
	liberarConexionBD($conexion);

	return $estado;
}

/**
 * Condiciones del reemplazo:
 * 
 * 1.No se permitirá si el saldo de la tabla 4 es diferente de cero (Entradas != Salidas {Por aplicacion, descarte o devolucion})
 * 2.Se debe comparar lo pedido por el kardex (P), lo despachado y el saldo.
 * 3.P=D=S Insert 
 * 4.P=0=0 Update
 * 5.Cualquier otra variante de D Y S no puede reemplazar.
 * 
 * @param $wbasedatos
 * @param $historia
 * @param $ingreso
 * @param $fechaKardex
 * @param $codArticulo
 * @param $codArticuloNuevo
 * @param $dtto
 * @param $obs
 * @param $unidadDosis
 * @param $formaFarm
 * @param $origen
 * @param $fechaInicio
 * @param $horaInicio
 * @param $usuario
 * @return unknown_type
 */
function reemplazarArticuloDetallePerfil($wbasedatos,$historia,$ingreso,$fechaKardex,$codArticulo,$codArticuloNuevo,$dtto,$obs,$unidadDosis,$formaFarm,$origen,$fechaInicio,$horaInicio,$usuario){

	global $codigoServicioFarmaceutico;
	global $centroCostosServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;
	global $wemp_pmla;

	global $protocoloNormal;
	
	global $horaCorteDispensacion;
	
	$formaFarm = trim( $formaFarm );
	
	if( empty( $formaFarm ) ){
		$formaFarm = '00';
	}
	
	$suspendido = '';	//Mensaje si el articulo fue suspendido

	$conexion = obtenerConexionBD("matrix");
	$estado = "0";
	$tipoProtocolo = $protocoloNormal;
	$puedeGrabar = true;

	$q = "SELECT
				*
			FROM
				".$wbasedatos."_000054
			WHERE
				 Kadhis = '$historia'
				 AND Kading = '$ingreso'
				 AND Kadfec = '$fechaKardex'
				 AND Kadart = '$codArticulo'
				 AND Kadfin = '$fechaInicio'
				 AND Kadhin = '$horaInicio'
			";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	//BUSCA R EL TIPO DE ARTICULO EN EL MAESTRO UNICO TABLA 68 A QUE TIPO DE PROTOCOLO PERTENECE
	$centroCostos = "";

	if($origen == $codigoServicioFarmaceutico){
		$centroCostos = $centroCostosServicioFarmaceutico;
	}

	if($origen == $codigoCentralMezclas){
		$centroCostos = $centroCostosCentralMezclas;
	}

	//Tipo del protocolo
	$q2 = "SELECT
				Arktip
			FROM
				".$wbasedatos."_000068
			WHERE
				 Arkcod = '$codArticuloNuevo'
				 AND Arkest = 'on'
				 AND Arkcco = '$centroCostos'";

	$res2 = mysql_query($q2, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);

	if($num2 > 0){
		$fila2 = mysql_fetch_array($res2);
		if( strlen( trim( $fila2['Arktip'] ) ) == 1 ){	//Para que sea visible en las pestañas del kardex, la longitud debe ser = 1
			$tipoProtocolo = $fila2['Arktip'];
		}
	}

	//Fracciones
	$q3 = "SELECT
				Deffra,Deffru,Defart,Defven,Defdie,Defdis,Defdup,Defvia
			FROM
				{$wbasedatos}_000059
			WHERE
				Defest = 'on'
				AND Defart = '$codArticuloNuevo'
				AND Defcco = '$centroCostos'
			GROUP BY
				Defart";

	$res3 = mysql_query($q3, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
	$num3 = mysql_num_rows($res3);

	if($num3 > 0){
		$fila3 = mysql_fetch_array($res3);
		
		$fila3['Defvia'] = trim( $fila3['Defvia'] );
		
		if( strtoupper( $fila3['Defvia'] ) == "NO APLICA"  ){
			$fila3['Defvia'] = '';
		}
		
		$viasArticuloNuevo = explode( ",", $fila3['Defvia'] );
	}

	$audAnterior = "";
	if($num > 0){
		$audAnterior = "A:".$fila['Kadart'].",".$fila['Kaddia'].",".$fila['Kadufr'].",".$fila['Kadffa'].",".$fila['Kadori'].",".$fila['Kadobs'];

		//Si el articulo existe pero no es duplicable no puede grabarse
		if(isset($fila3['Defdup']) && !empty($fila3['Defdup']) && $fila3['Defdup'] == "off"){

			$qNvo = "SELECT
						Kadart, Kaddia, Kadufr, Kadffa, Kadori, Kadobs
					FROM
						".$wbasedatos."_000054
					WHERE
						Kadhis = '$historia'
						AND Kading = '$ingreso'
						AND Kadart = '$codArticuloNuevo'
						AND Kadfec = '$fechaKardex'";

			$resNvo = mysql_query($qNvo, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qNvo . " - " . mysql_error());
			$filaNvo = mysql_fetch_array($resNvo);
			$numNvo = mysql_num_rows($resNvo);

			if($numNvo > 0){
				$puedeGrabar = false;
			}
		}
	} else {
		$puedeGrabar = false;
	}
	
	/************************************************************
	 * Enero 05 de 2011
	 ************************************************************/
	$cantidadFraccion = $fila['Kadcfr'];
	$cantidadDispensar = $fila['Kadcdi'];
	$cantidadSaldoAnterior = $fila['Kadsad'];
	$cantidadDispensada = $fila['Kaddis'];
	$saldoDelArticulo = $fila['Kadsal'];
	$cantidadDosis = $fila['Kadcan'];
	$dosisMaxima = trim( $fila['Kaddma'] );
	
	$wcempro = consultarAliasPorAplicacion( $conexion, $wemp_pmla, "cenmez" );
	$esGenerico = esArticuloGenerico( $conexion, $wbasedatos, $wcenpro, $codArticulo );
	
	//Si el articulo que se va a reemplazar es generico se debe cambiar la cantidad de fracción
	//según la definición de fracciones
	if( $puedeGrabar ){
		if( $esGenerico && $num > 0 && $num3 > 0 ){
			$cantidadFraccion = $fila['Kadcfr']/$fila['Kadcma']*$fila3['Deffra'];
		}
		elseif( $num > 0 && $num3 > 0 ){	//Enero 12 de 2011
			//Si el articulo es no generico, se debe calcular la dosis con respecto a la cantidad de dosis a aplicar del articulo viejo
			if( true || $fila['Kadcfr'] >= $fila3['Deffra'] ){
				
				if( trim($fila['Kadufr']) != trim($fila3['Deffru']) ){	//Febrero 21 de 2011
					
					$cantidadFraccion = ceil( $fila['Kadcfr']/$fila['Kadcma'] )*$fila3['Deffra'];	//Enero 24 de 2011
					$wcempro = consultarAliasPorAplicacion( $conexion, $wemp_pmla, "cenmez" );
					if( esTipoGenerico( $conexion, $wbasedatos, $wcenpro, $codArticuloNuevo ) && $cantidadFraccion != $fila3['Deffra'] ){		
//						$cantidadFraccion = ceil( $fila['Kadcfr']/$fila3['Deffra'] )*$fila3['Deffra'];
						$cantidadFraccion = (1)*$fila3['Deffra'];
					}

					$cantidadDispensar = ceil( ( $cantidadFraccion/$fila3['Deffra'] )*( $fila['Kadcan'] ) );
				
					$cantidadDispensar += $cantidadSaldoAnterior;
					
					$saldoDelArticulo = ($cantidadDispensar-$cantidadSaldoAnterior)*$fila3['Deffra']-$fila['Kadcan']*$cantidadFraccion;
				}
				else{
					//echo "<br>.....cant antes: ".($fila['Kadcma']*$fila['Kaddis']/$fila3['Deffra']);
					
					$cantidadDispensada = ceil( $fila['Kadcma']*$fila['Kaddis']/$fila3['Deffra'] );
					
					//echo "<br>.....cant despues: ".$cantidadDispensada.":::";
					
					/************************************************************************************************************
					 * Marzo 15 de 2011
					 * 
					 * Si el articulo es de un tipo  generico se debe gastar toda la bolsa
					 ************************************************************************************************************/

					consultarAliasPorAplicacion( $conexion, $wemp_pmla, "cenmez" );
					if( esTipoGenerico( $conexion, $wbasedatos, $wcenpro, $codArticuloNuevo ) && $cantidadFraccion != $fila3['Deffra'] ){
//						$cantidadFraccion = ceil( $fila['Kadcfr']/$fila3['Deffra'] )*$fila3['Deffra'];
						$cantidadFraccion = (1)*$fila3['Deffra'];
					}
					/************************************************************************************************************/

//					$cantidadDispensar = ceil( $fila['Kadcfr']/$fila3['Deffra']*( $fila['Kadcan'] ) );
					$cantidadDispensar = ceil( $cantidadFraccion/$fila3['Deffra']*( $fila['Kadcan'] ) );

					$cantidadSaldoAnterior = ceil( $fila['Kadsad']*$fila['Kadcma']/$fila3['Deffra'] );

					$cantidadDispensar += $cantidadSaldoAnterior;

					$saldoDelArticulo = ($cantidadDispensar-$cantidadSaldoAnterior)*$fila3['Deffra']-$fila['Kadcan']*$cantidadFraccion;
				}
			}
		} //Fin de Enero 12 de 2011
	}
	/************************************************************/
	
	/***********************************************************************************************
	 * Octubre 4 de 2011
	 *
	 * - No se permite hacer reemplazo de medicamentos si
	 * - El paciente esta en un centro de costos que NO maneja ciclos de produccion
	 *   y ya se ha sido dispensado
	 * - El paciente esta en un centro de costos que maneja ciclos de produccion
	 *   y para la siguiente ronda ya se ha dispensado algo, si no se ha dispensado, mostrar un
	 *   mensaje que diga que puede dispensar a partir de la siguiente ronda
	 ***********************************************************************************************/
	/******************************************************************************************
	 * Octubre 4 de 2011
	 ******************************************************************************************/			
	$tieneCpx = tieneCpxPorHistoria( $conexion, $wbasedatos, $historia, $ingreso );
	
	if( $tieneCpx && !empty( $fila[ 'Kadcpx' ] ) ){

		if( !empty( $fila[ 'Kadcpx' ] ) ){
			
			$timeActual = time()-3600*2;
	
			// if( date( "H", $timeActual ) > 2 && date( "Y-m-d", $timeActual ) == date( "Y-m-d" )  )
			if( date( "Y-m-d", $timeActual ) == date( "Y-m-d" )  ){
			
				//Consulto ronda actual
				$rondaActual = intval( date( "H", $timeActual )/2 )*2;
				$rondaActualPosterior = intval( date( "H" )/2 )*2;
				
				if( $rondaActual < 10 ){
					$rondaActual = "0$rondaActual:00:00";
				}
				else{
					if( $rondaActual == 24 ){
						$rondaActual = "00:00:00";
					}
					else{
						$rondaActual = "$rondaActual:00:00";
					}
				}
				
				//Consulto ronda poterior
				if( $rondaActualPosterior < 10 ){					
					$rondaActualPosterior = "0$rondaActualPosterior:00:00";
				}
				else{
					if( $rondaActual == 24 ){
						$rondaActualPosterior = "00:00:00";
					}
					else{
						$rondaActualPosterior = "$rondaActualPosterior:00:00";
					}
				}
				
				$kadcpx = nuevaDosis( $cantidadFraccion/$fila3['Deffra'], $fila['Kadcpx'] );
				
				// list( $kadron, $kadfro ) = explode( "|", consultarUltimaRondaDispensadaKardex( $kadcpx ) );
				list(  $fila['Kadron'], $fila['Kadfro'] ) = explode( "|", consultarUltimaRondaDispensadaKardex( $fila['Kadcpx'] ) );
				
				//Coloco la ronda correcta para kadfro
				if( !empty( $fila['Kadron'] ) ){
					
					//Si es vacio, significa que la fecha corresponde al dia siguiente
					if( empty( $fila['Kadfro'] ) ){
						$fila['Kadfro'] = date( "Y-m-d", strtotime( "$fechaKardex 00:00:00" )+24*3600 );
					}
					else{
						$fila['Kadfro'] = $fechaKardex;
					}
				}
				
				// echo $fila['Kadron'], '....aslfjdflsf....'.$fila['Kadfro'];
				if( !empty( $fila['Kadron'] ) &&  $fila['Kadfro'] == date("Y-m-d") ){
				
					if( $fila['Kadron']*1 > substr( $rondaActual,0 ,2 )*1 ){
						$cantidadDispensada = cantidadTotalADispensarRonda( $kadcpx, $rondaActualPosterior );
					}
					elseif( $rondaActual != "00:00:00" ){
						$cantidadDispensada = cantidadTotalADispensarRonda( $kadcpx, $rondaActual );// + cantidadDispensadaRondaKardex( $fila['Kadcpx'], $rondaActualPosterior );
					}
					elseif( substr( $kadcpx, 0, 3 ) == "Ant" ){	//Quito el saldo del dia anterior
						$cantidadDispensada = cantidadTotalADispensarRonda( $kadcpx, "Ant" );
					}
				}
				elseif( $rondaActual != "00:00:00" ){
					$cantidadDispensada = cantidadTotalADispensarRonda( $kadcpx, $rondaActual );// + cantidadDispensadaRondaKardex( $fila['Kadcpx'], $rondaActualPosterior );
				}
				elseif( substr( $kadcpx, 0, 3 ) == "Ant" ){	//Quito el saldo del dia anterior
					$cantidadDispensada = cantidadTotalADispensarRonda( $kadcpx, "Ant" );
				}
				
				$cantidadDispensada = intval( $cantidadDispensada );
				
				$kadcpx = crearAplicacionesCargadasPorHorasKardex( $kadcpx, $cantidadDispensada );
				
				/************************************************************************
				 * Noviembre 29 de 2011
				 *
				 * - Miro que la cantidad a dispensar sea suficiente 
				 ************************************************************************/
				
				//Si el saldo es 0 y no tiene registro del dia anterior (Kadreg), significa que viene de un cco que no maneja ciclos de produccion
				//y por tanto hay que recalcular saldos de dispensacion del dia anterior
				if( empty( $fila['Kadsad'] ) ||  trim( $fila['Kadsad'] ) == '' ){
				
					if( !empty( $fila[ 'Kadron' ] ) && trim( $fila['Kadron'] ) != '' /* && trim( $fila['Kadron'] ) != "00" */ ){
						
						//Busco la cantidad dispensada hasta la ronda del medicamento anterior
						//Esto por que ya puede haber una cantidad dispensada y de esta forma traduzco cuanto fue con respecto al medicamento nuevo
						if( $fila['Kadfro'] == $fila['Kadfec'] ){
							$cantidadDispensadaMedAnterior = intval( cantidadTotalADispensarRonda( $kadcpx, $fila['Kadron'].":00:00" ) );
						}
						else{
							if( $fila[ 'Kadron' ] = "00" ){
								$cantidadDispensadaMedAnterior = intval( cantidadTotalADispensarRonda( $kadcpx, "00:00:00" ) );
							}
							else{
								$cantidadDispensadaMedAnterior = intval( cantidadTotalADispensarRonda( $kadcpx, "" ) );
							}
						}
				
						$cantidadDispensada = $cantidadDispensada - $cantidadDispensadaMedAnterior;
												//   (                 aplicaciones med anterior                     )*$cantidadArticuloPorDosis
						$cantidadDispensada += intval( intval( ( $fila[ 'Kaddis' ]*$fila['Kadcma'] )/$fila['Kadcfr'] )*($cantidadFraccion/$fila3['Deffra']) );	//Transformo la cantidad dispensada del medicamento anterior por el nuevo, para mantener la relacion
					
						if( $cantidadDispensada < 0 ){
							$cantidadSaldoAnterior += $cantidadDispensada*(-1);
							$cantidadDispensada = 0;
						}
					}
				}
				
				/************************************************************************/
				
				list( $kadron, $kadfro ) = explode( "|", consultarUltimaRondaDispensadaKardex( $kadcpx ) );
				
				/**************************************************************************************************************			
				 * Noviembre 1 de 2011
				 **************************************************************************************************************/
				if( !empty( $kadron ) ){
							
					if( empty( $kadfro ) ){
						$kadfro = date( "Y-m-d", strtotime( trim( $fechaKardex )." 00:00:00" ) + 3600*24 );
					}
					else{
						$kadfro = $fechaKardex;
					}
				}
				/**************************************************************************************************************/
				
				//calcularCantidadGrabar( $fechaHoy,$fechaInicio,$horaInicioSuministro,$horasFrecuencia,$esPrimeraVez );
			}
			else{	//Si el reemplazo es hecho antes de las 2 de la mañana del dia actual, significa que no hay que cargar nada nuevo
				$kadcpx = nuevaDosis( $cantidadFraccion/$fila3['Deffra'], $fila['Kadcpx'] );
				
				if( substr( $kadcpx, 0, 3 ) == "Ant" && $fila['Kaddis'] > 0 ){	//Quito el saldo del dia anterior
					$cantidadDispensada = cantidadTotalADispensarRonda( $kadcpx, "Ant" );
					$kadcpx = crearAplicacionesCargadasPorHorasKardex( $kadcpx, $cantidadDispensada );
				}
				else{
					$cantidadDispensada = 0;
				}				
				
				$kadron = "";
				$kadfro = "0000-00-00";
			}
		}
		else{
			$kadcpx = "";
			$kadron = "";
			$kadfro = "";
		}
	}
	else{
		
		$kadcpx = '';
		$kadron = '';
		$kadfro = "";
		
		//Calculo cuantas dosis faltan hasta el dia siguiente
		
		//Consulto la frecuencia
		$frecuencia = consultarFrecuencia( $conexion, $wbasedatos, $fila['Kadper'] );
		
		$fechorIncioMedicamento = strtotime( "{$fila['Kadfin']} {$fila['Kadhin']}" );
		
		/****************************************************************************************************
		 * Febrero 16 de 2012
		 *
		 * Calculo la cantidad a dispensar hasta el día siguiente
		 * El calculo general es el siguiente:
		 * - Se calcula siempre la ultima ronda de aplicacion antes de la hora de corte del día siguiente
		 * - Calculo cuantas aplicaciones o suministros se le aplican al paciente desde la ronda de reemplazo
		 *   hasta la última ronda de aplicacion
		 * - Si la ronda de reemplazo es una ronda de aplicación, se busca si el medicamento fue dispensado
		 *   para esa ronda segun el kardex, adicionar una aplicacion al calculo o no, esta adición cubre
		 *   la primera aplicación.
		 *
		 * Fromula: aplicaciones: (Fecha y hora de cambio - Fecha y hora final)/frecuencia + adicional
		 *					 	  0 >= adicional <= 1
		 ****************************************************************************************************/
		//Calculo la ronda actual
		$time = time() - strtotime( "1970-01-01 00:00:00" );
		$timeActual = ceil( $time/(2*3600) )*2*3600 - 2*3600 + strtotime( "1970-01-01 00:00:00" );
		
		if( date( "Y-m-d", $timeActual ) != date( "Y-m-d" ) ){
			$timeActual = strtotime( date( "Y-m-d 00:00:00" ) );
		}
		
		//Calculo el tiempo hasta la hora de corete del día siguiente
		$tiempoHastaDiaSiguiente = strtotime( date( "Y-m-d", $timeActual+24*3600)." $horaCorteDispensacion:00:00" );
		
		//Busco la ulitma hora de dispensacion del dia siguiente
		$ultimoSuministroDiaSiguiente = suministroAntesFechaCorte( date("Y-m-d", $tiempoHastaDiaSiguiente ), date( "H:i:s", $tiempoHastaDiaSiguiente ), $fila['Kadfin'], $fila['Kadhin'], $frecuencia );
		
		//Si tiene dosis maxima calculo hasta cuando es
		if( !empty( $dosisMaxima ) ){
			//Si tiene dosis Maximas, el ultimo suministro o aplicacion es el minimo entre la fecha de terminacion del medicamento y el dia siguiente
			$ultimoSuministroDiaSiguiente = min( $ultimoSuministroDiaSiguiente, $fechorIncioMedicamento + ($dosisMaxima-1)*$frecuencia*3600 );
		}
		
		//Calculo cuantas aplicaciones se dejaron de dispensar
		$cantDosisSinDispensar = ceil( $cantidadSaldoAnterior/( $cantidadFraccion/$fila3['Deffra'] ) );
		
		//Calculo cuantas dosis se dispensaron
		$cantDosisDispensadas = intval( $cantidadDispensada/( $cantidadFraccion/$fila3['Deffra'] ) );
		
		//Calculo de la ultima aplicación según la dispensación
		$comienzoDiaActual = $ultimoSuministroDiaSiguiente - ($cantidadDosis + $cantDosisSinDispensar - $cantDosisDispensadas)*$frecuencia*3600;
		
		if( $timeActual > $fechorIncioMedicamento ){
			$ultimoSuministro = $timeActual;
		}
		else{
		
			$ultimoSuministro = $fechorIncioMedicamento;
			
			//Si la hora de reemplazo es antes de la hora de inicio del medicamento
			//siempre se tiene que agregar uno
			if( $timeActual < $fechorIncioMedicamento ){
				$adicional = 1;
			}
		}
		
		//Si existe adicional es por que ya se calculo con anterioridad y por tanto
		//no se tiene que volver a calcular
		if( !isset($adicional) ){
			$adicional = 0;
			if( $ultimoSuministro > $comienzoDiaActual && ( $ultimoSuministro-$fechorIncioMedicamento )%($frecuencia*3600) == 0 ){
				$adicional = 1;
			}
		}
		
		$totalAplicaciones = ceil( ( $ultimoSuministroDiaSiguiente - $ultimoSuministro )/($frecuencia*3600) );
		
		//Total de articulos que faltan desde el momento de reemplazo hasta el dia siguiente
		$cantTotal = ceil( $totalAplicaciones*($cantidadFraccion/$fila3['Deffra']) + $adicional*($cantidadFraccion/$fila3['Deffra']) );
		
		//Calculo la cantidad total a dispensar segun las aplicaciones
		$cantTotalSegunAplicaciones = ceil( $cantidadDosis*($cantidadFraccion/$fila3['Deffra']) );
		/****************************************************************************************************/
		
		/************************************************************************************************
		 * Febrero 16 de 2012
		 *
		 * Calculo cuanto es la cantidad a dispensar, la cantidad dispensada y el saldo del día anterior
		 ************************************************************************************************/
		
		//Calculo cuanto queda del saldo del dia anterior
		$auxCanSalAnterior = $cantTotalSegunAplicaciones - $cantTotal;
		
		//Si es negativo significa que tiene saldo del dia anterior de lo contrario no
		if( $auxCanSalAnterior < 0 ){
			$cantidadSaldoAnterior = $auxCanSalAnterior*(-1);
			$cantidadDispensada = 0;
		}
		else{
			$cantidadSaldoAnterior = 0;
			
			//Dejo la cantidad dispensada en 0
			$cantidadDispensada = 0;
			
			//Calculo cuanto fue dispensado
			if( $auxCanSalAnterior > 0 ){
				$cantidadDispensada = $auxCanSalAnterior;
			}
		}
		
		$cantidadDispensar = $cantTotalSegunAplicaciones + $cantidadSaldoAnterior;
		/************************************************************************************************/
		
		//Si tiene cpx significa que no tiene regleta y procedo a crearla
		if( $tieneCpx ){
		
			$infoTipoArticulo = consultarinfotipoarticulosKardex( $conexion, $wbasedatos );
			
			/************************************************************************************************************************
			 * Creando regleta
			 ************************************************************************************************************************/
			$frecuencia = consultarFrecuencia( $conexion, $wbasedatos, $fila['Kadper'] );
			
			$vectorAplicacion =  obtenerVectorAplicacionMedicamentos( date("Y-m-d", $timeActual ), trim( $fechaInicio ), trim( $horaInicio ), $frecuencia );
			$vectorAplicacion2 = obtenerVectorAplicacionMedicamentos( date( "Y-m-d", $timeActual+24*3600 ), trim( $fechaInicio ), trim( $horaInicio ), $frecuencia );
			$vectorAplicacion2 = arreglarVectorKardex( $vectorAplicacion2 );
			
			$quitarUnoARegleta = 25;
			if( count($vectorAplicacion2) == 25 ){
				$quitarUnoARegleta = 24;
			}
			
			foreach( $vectorAplicacion2 as $keyB => $valueB ){
				$vectorAplicacion[ $quitarUnoARegleta ] = $valueB;
				$quitarUnoARegleta++;
			}
			
			$kadcpx = crearVectorAplicaciones( $vectorAplicacion, $infoTipoArticulo[ $tipoProtocolo ]['tiempoPreparacion'], $cantidadFraccion/$fila3['Deffra'], intval( ( strtotime( "1970-01-01 ".$infoTipoArticulo[ $tipoProtocolo ]['horaCaroteDispensacion'] ) - strtotime( "1970-01-01 00:00:00" ) )/3600 ) );
			/************************************************************************************************************************/
			
			//Si pide medicamento para las 00:00:00 del dia actual creo el saldo del dia anterior
			if( strtotime( date( "Y-m-d 00:00:00" ) ) - strtotime( $fila[ 'Kadfin' ]." ".$fila[ 'Kadhin' ] ) >= 0 && abs( strtotime( date( "Y-m-d 00:00:00" ) ) - strtotime( $fila[ 'Kadfin' ]." ".$fila[ 'Kadhin' ] ) )%$frecuencia == 0 ){
				$kadcpx = "Ant-".round( $cantidadFraccion/$fila3['Deffra'], 3 )."-0,".$kadcpx;
			}
			
			$kadcpx = crearAplicacionesCargadasPorHorasKardex( $kadcpx, $cantidadDispensada );
			
			list( $kadron, $kadfro ) = explode( "|", consultarUltimaRondaDispensadaKardex( $kadcpx ) );
			
			/**************************************************************************************************************
			 * Noviembre 1 de 2011
			 **************************************************************************************************************/
			if( !empty( $kadron ) ){
						
				if( empty( $kadfro ) ){
					$kadfro = date( "Y-m-d", strtotime( trim( $fechaKardex )." 00:00:00" ) + 3600*24 );
				}
				else{
					$kadfro = $fechaKardex;
				}
			}
			/**************************************************************************************************************/
		}
	}
	/******************************************************************************************/
	
	/******************************************************************************************
	 * Octubre 27 de 2011
	 * - Si el articulo no tiene la misma via que el original no se puede reemplazar
	 ******************************************************************************************/
	$fila3['Defvia'] = trim( $fila3['Defvia'] );
	 
	if( strtoupper( $fila3['Defvia'] ) == "NO APLICA" ){
		$fila3['Defvia'] = '';
	}
	
	if( $origen == $codigoServicioFarmaceutico ){
		if( !empty( $fila3['Defvia'] ) ){
			
			$conViaIgual = false;
			foreach( $viasArticuloNuevo as $viasKey => $viasValue ){
				if( trim( $viasValue ) == trim( $fila['Kadvia'] ) ){
					$conViaIgual = true;
				}
			}
			
			if( !$conViaIgual ){
				$puedeGrabar = false;
				$estado = "6";	//Indica que no tiene la misma via que el original
			}
		}
		else{
			$estado = "7";	//Indica que no tiene vias registradas, debe tener una
			$puedeGrabar = false;
		}
	}
	/******************************************************************************************/
	 
	//Si existe el registro ACTUALIZO caso contrario INSERTO
	if($puedeGrabar){
		
		/************************************************************************
		 * Enero 12 de 2011 
		 ************************************************************************/
		$q = "SELECT
					*
				FROM
					".$wbasedatos."_000054
				WHERE
				 	Kadhis = '$historia'
				 	AND Kading = '$ingreso'
				 	AND Kadfec = '$fechaKardex'
				 	AND Kadart = '$codArticuloNuevo'
				 	AND Kadfin = '$fechaInicio'
				 	AND Kadhin = '$horaInicio'";
		
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numrows = mysql_num_rows( $res );
		
		if( $numrows <= 0 ){
			$fecha = $fila['Fecha_data'];
			$hora = $fila['Hora_data'];
			
			/******************************************************************************************
			 * Junio 30 de 2011
			 ******************************************************************************************/
			//Consulto unidad de manejo del articulo nuevo
			
			if( $origen == $codigoServicioFarmaceutico ){
			
				$sql = "SELECT
							Artuni
						FROM
							{$wbasedatos}_000026
						WHERE
							artcod = '$codArticuloNuevo'
							";
			}
			else{

				$sql = "SELECT
							Artuni
						FROM
							{$wcenpro}_000002
						WHERE
							artcod = '$codArticuloNuevo'
						";
			}
			
			$res = mysql_query( $sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			
			if( $rowsUma = mysql_fetch_array( $res ) ){
				$uniMan = $rowsUma[ 'Artuni' ]; 
			}
			/******************************************************************************************/
			
			obtenerDatosAdicionalesArticulo($historia,$ingreso,$codArticulo,$diasTtoAcumulados,$dosisTtoTotales);
			
			$dosisTtoTotales += $fila['Kadcdt'];
			$diasTtoAcumulados += $fila['Kadcda'];
			
			//Inserto registro nuevo
			$q = "INSERT INTO {$wbasedatos}_000054(     Medico   , Fecha_data, Hora_data,    Kadhis  ,  Kading   ,     Kadart         ,       Kadcma        ,  Kaduma  ,  Kaddia, Kadest, Kadess,       Kadper       ,    Kadffa   ,       Kadfin  ,   Kadhin     ,        Kadvia      ,      Kadfec   , Kadcon, Kadobs,  Kadori  , Kadsus,       Kadcnd       ,       Kaddma      ,       Kadcan        ,       Kaddis         ,       Kadcfr       ,      Kadufr   ,        Kadhdi       ,      Kadsal        ,       Kadcdi        ,      Kadpri        ,    Kadpro       ,        Kadcco      , Kadare,          Kadsad        ,       Kadnar       ,        Kadreg      ,  Kadcpx  ,  Kadron  ,  Kadfro  ,          Kadaan                 ,       Kadcda        ,        Kadcdt     ,     Kadimp     ,   Kadalt     ,   Kadcal,    Kadint,  Seguridad         )
											VALUES( '$wbasedatos',  '$fecha' ,  '$hora' , '$historia', '$ingreso', '$codArticuloNuevo', '{$fila3['Deffra']}', '$uniMan', '$dtto',  'on' , 'off' , '{$fila['Kadper']}', '$formaFarm', '$fechaInicio', '$horaInicio', '{$fila['Kadvia']}', '$fechaKardex',  'on' , '$obs', '$origen',	 'off', '{$fila['Kadcnd']}', '{$fila['Kaddma']}', '{$fila['Kadcan']}', '$cantidadDispensada', '$cantidadFraccion', '$unidadDosis', '{$fila['Kadhdi']}', '$saldoDelArticulo', '$cantidadDispensar', '{$fila['Kadpri']}', '$tipoProtocolo', '{$fila['Kadcco']}',  'on' , '$cantidadSaldoAnterior', '{$fila['Kadnar']}', '{$fila['Kadreg']}', '$kadcpx', '$kadron', '$kadfro', '$codArticulo,".$fechaKardex."', '$diasTtoAcumulados', '$dosisTtoTotales', '{$fila['Kadimp']}', '{$fila['Kadalt']}', '{$fila['Kadcal']}', '{$fila['Kadint']}','{$fila['Seguridad']}' ) ";

			$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			
			//registro el id original
			$idOriginal = mysql_insert_id();
			
			$q = "UPDATE
					{$wbasedatos}_000054
				  SET
				  	Kadido = '$idOriginal'
				  WHERE
				  	id = '$idOriginal'
				 ";

			$res = mysql_query($q, $conexion) or die ( "Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			//Suspendo el registro del articulo viejo
			$q = "UPDATE
					{$wbasedatos}_000054
				  SET
				  	Kadsus = 'on'
				  WHERE
				  	id = '{$fila['id']}'
				 ";

			$res = mysql_query($q, $conexion) or die ( "Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			
			
			//crenado el mensaje de articulo suspendido
			$suspendido = ",".obtenerMensaje('MSJ_SUSPENDER_MEDICAMENTO');
			
			$suspendido .= ":".$fila['Kadart'];
			
			$estado = "1";
		}
		else{

			if( $numrows == 1 ){
				
				$rowsRepetidos = mysql_fetch_array( $res );
				
				if( $rowsRepetidos['Kadsus'] == 'on' && $rowsRepetidos['Kadper'] == $fila['Kadper'] ){
				
					obtenerDatosAdicionalesArticulo($historia,$ingreso,$codArticuloNuevo,$diasTtoAcumulados,$dosisTtoTotales);
					$dosisTtoTotales = $fila['Kadcdt'];
					
					//obtenerDatosAdicionalesArticulo($historia,$ingreso,$codArticulo,&$diasTtoAcumulados,&$dosisTtoTotales);
					$diasTtoAcumulados = 0;
					
					if( !empty( $fila['Kadaan'] ) ){
					
						list( $articuloAnterior, $fechaReemplazo ) = explode( ",", $fila['Kadaan'] );
						
						if( $fechaReemplazo == date( "Y-m-d" ) ){
							
							if( esAplicado( $conexion, $wbasedatos, $historia, $ingreso, $fila['Kadart'], $fechaReemplazo ) ){
								$diasTtoAcumulados++;
							}						
						}
					}
					
					$q = "UPDATE
							{$wbasedatos}_000054
						  SET
						  	Kadsus = 'on'
						  WHERE
						  	id = '{$fila['id']}'
						 ";
	
					$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					
					$q = "UPDATE
							{$wbasedatos}_000054
						  SET
						  	Kadsus = 'off',
						  	Kadcan = '$cantidadDosis',
							Kadcfr = '$cantidadFraccion',
							Kadcdi = '$cantidadDispensar',
							Kadsad = '$cantidadSaldoAnterior',
							Kaddis = '$cantidadDispensada',
							Kadare = 'on',
							Kadcpx = '$kadcpx',
							Kadron = '$kadron',
							Kadfro = '$kadfro',
							Kadaan = '$codArticulo,".$fechaKardex."',
							Kadcda = '$diasTtoAcumulados',
							Kadcdt = '$dosisTtoTotales'
						  WHERE
						  	id = '{$rowsRepetidos['id']}'
						 ";
	
					$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					
					$idOriginal = $rowsRepetidos['Kadido'];
					
					$estado = "1";
				}
				else{
					$estado = "5";
				}
			}
			else{
				$estado = "5";
			}
		}
		/************************************************************
		 *                   Fin Enero 12 de 2011
		/************************************************************/
		
	} else {
		
		if( $estado != "6" && $estado != "7" ){
			$estado = "4";
		}
	}

	//Generación de auditoria cambio / creación
	$audNuevo = "N:".$codArticuloNuevo.",".$dtto.",".$unidadDosis.",".$formaFarm.",".$origen.",".$dtto.",".$obs;

	$mensajeAuditoria = "";

	switch ($estado){
		case "1":
			$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_REEMPLAZADO_DESDE_PERFIL');
			break;
		default:
			$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_NO_MODIFICADO_DESDE_PERFIL');
			break;
	}

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior $audNuevo $suspendido";
	$auditoria->fechaKardex = $fechaKardex;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;
	$auditoria->idOriginal = $idOriginal;

	registrarAuditoriaKardex($conexion,$wbasedatos,$auditoria);

	liberarConexionBD($conexion);
	
	if( $tieneCpx && $estado == "1" ){	//Octubre 10 de 2011
		$estado = "8";
		$estado .= "|".$cantidadFraccion."|".$cantidadDispensar."|".$cantidadSaldoAnterior;//.$lastRonda; //Enero 05 de 2011
	}
	else{
		$estado .= "|".$cantidadFraccion."|".$cantidadDispensar."|".$cantidadSaldoAnterior; //Enero 05 de 2011
	}

	return $estado;	 
}


//Consulta de los medicos por especialidad
function consultarMedicosPorEspecialidad($basedatos,$especialidad){
	$conexion = obtenerConexionBD("matrix");

	//Relacion muchos a muchos
	/*
	$q = "SELECT
			Meddoc,Medtdo,Medno1,Medno2,Medap1,Medap2
		FROM
  			{$basedatos}_000065, {$basedatos}_000048
  		WHERE
  			Esmcod = '".$especialidad."'
  			AND Meddoc = Esmndo
  			AND Medtdo = Esmtdo
		ORDER by Medap1,Medno1";
	*/

	//...
	$q = "SELECT
			Meddoc,Medtdo,Medno1,Medno2,Medap1,Medap2,Meduma,SUBSTRING_INDEX(Medesp,'-',1) Medesp
		FROM
			{$basedatos}_000048
		WHERE
			SUBSTRING_INDEX(Medesp,'-',1) = '".$especialidad."'
			AND Medest = 'on'
		ORDER by
			Medap1,Medno1";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$consulta = "<select name='wselmed' id='wselmed' class='seleccionNormal'>";

	$consulta = $consulta."<option value=''>Seleccionar medico...</option>";

	$cont1 = 0;

	if($num > 0){
		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);

			$consulta = $consulta."<option value='".$rs['Medtdo']."-".$rs['Meddoc']."-".htmlentities($rs['Medap1'])." ".htmlentities($rs['Medap2']).", ".htmlentities($rs['Medno1'])." ".htmlentities($rs['Medno2'])."-".$rs['Medesp']."'>".htmlentities($rs['Medap1']." ".$rs['Medap2'].", ".$rs['Medno1']." ".$rs['Medno2'])."</option>";

			$cont1++;
		}
	} else {
		$consulta = $consulta."<option value=''>No hay medicos con la especialidad seleccionable</option>";
	}
	$consulta = $consulta."</select>";

	return $consulta;
}


//Consulta de los tratamientos
function consultarTratamientos($basedatos,$articulo){
	$conexion = obtenerConexionBD("matrix");

	$q = "SELECT
			Tracod, Trades
		FROM
			{$basedatos}_000164, {$basedatos}_000137
		WHERE 
			Traest = 'on'
		AND	Tracod = Protra
		AND	Pronom = '".$articulo."'
		AND	Proest = 'on'
		GROUP BY
			Tracod 
		ORDER by
			Trades DESC 
			";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	$consulta = '';

	if($num > 0)
	{
		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);
			$nombreTratamiento = str_replace('-',' ',$rs['Trades']);
			$consulta .= $rs['Tracod'].'-'.$nombreTratamiento.'|';
			$cont1++;
		}
	} 
	
	$consulta .= "*-*";

	return $consulta;
}

//Consulta de los tratamientos
function consultarDetalleFamilia($basedatos,$articulo){
	$conexion = obtenerConexionBD("matrix");

	$consulta = "";
	
	$q = "SELECT
			Famnom, Relcon, Reluni
		FROM
			{$basedatos}_000114, {$basedatos}_000115
		WHERE 
			Famcod = Relfam
		AND Relart = '".$articulo."' ";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num>0)
	{
		$rs = mysql_fetch_array($res);
		$consulta = $rs['Famnom'].' '.$rs['Relcon'].' '.$rs['Reluni'];
	}
	
	return $consulta;
}


//Consulta los tipos de empresa EPS
function tipoResponsableEsEPS($wemp_pmla,$basedatos,$whistoria,$wingreso)
{
	$conexion = obtenerConexionBD("matrix");

	//Busco los tipos de empresa que son EPS
	$tiposEmpresa = consultarAliasPorAplicacion( $conexion, $wemp_pmla, "tiposEmpresasEps" );
	
	//creo un IN para la consulta
	$list = explode( "-", $tiposEmpresa );
	
	$inEPS = '';
	
	foreach( $list as $key => $value ){
		$inEPS .= ",'$value'";
	}
	
	$inEPS = "IN( ".substr( $inEPS, 1 )." ) ";
			
	$sql = "SELECT 
				Inghis
			FROM
				{$basedatos}_000016 b
			WHERE
				Inghis = '$whistoria'
				AND inging = '$wingreso'
				AND ingtip $inEPS
			";
			
	$res = mysql_query( $sql, $conexion ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	return $num;
}


//Consulta si el tipo de orden tiene asociado u formulario de historia clínica electrónica
function consultarFormularioTipoOrden($basedatoshce,$wtipo)
{
	$conexion = obtenerConexionBD("matrix");

	$q = "SELECT
			Tipfrm
		FROM
			{$basedatoshce}_000015
		WHERE 
			Codigo = '".$wtipo."'
		AND Estado = 'on' ";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0)
	{
		$rs = mysql_fetch_array($res);
		$formulario = $rs['Tipfrm'];
	}
	else
	{
		$formulario = "";
	}
	
	return $formulario;
}


function consultarFormularioHCE($basedatoshce,$formTipoOrden,$historia,$ingreso)
{
	$conexion = obtenerConexionBD("matrix");
	
	$num = false;

	$q = "SELECT
			movpro
		FROM
			{$basedatoshce}_{$formTipoOrden}
		WHERE 
			movhis = '".$historia."'
		AND moving = '".$ingreso."' 
		AND movtip = 'Firma' 
		AND fecha_data='".date( "Y-m-d" )."'
		";
		
	$sql = "SELECT
				id
			FROM
				{$basedatoshce}_000036
			WHERE 
				Firpro = '{$formTipoOrden}'
			AND Firhis = '".$historia."'
			AND Firing = '".$ingreso."' 
			AND fecha_data='".date( "Y-m-d" )."'
			ORDER BY
				Fecha_data DESC, Hora_data DESC
		";

	$res = mysql_query($sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	// $num = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array( $res ) ){
		$num = $rows[ 'id' ];
	}

	// if($num > 0)
		// return 'ok';
	// else
		// return 'no-data';
		
	return $num;
}

function borrarFormularioHCE($basedatoshce,$wcco,$historia,$ingreso, $firmHce )
{
	$conexion = obtenerConexionBD("matrix");
	$formulario = "";

	// Consulto el tipo de orden
	$q = "SELECT
			a.Tipfrm
		FROM
			{$basedatoshce}_000015 a, {$basedatoshce}_000017 b
		WHERE 
			a.Codigo = b.Tipoestudio
		AND b.Servicio = '".$wcco."'
		AND a.Estado = 'on' 
		AND b.Estado = 'on'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0)
	{
		$rs = mysql_fetch_array($res);
		$formulario = trim( $rs['Tipfrm'] );
	}
	
	if($formulario != "")
	{
		$q = "DELETE
			FROM
				{$basedatoshce}_{$formulario}
			WHERE 
				movhis = '".$historia."'
			AND moving = '".$ingreso."' ";
			
		$q="DELETE a
			FROM {$basedatoshce}_{$formulario} a, {$basedatoshce}_000036 b
			WHERE 
				movhis = '".$historia."'
			AND moving = '".$ingreso."' 
			AND a.fecha_data = b.fecha_data
			AND a.hora_data = b.hora_data
			AND b.id = '$firmHce'
			";
			

		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	
	return 'ok';
}


//Consulta el resumen de la historia
function consultarResumenHistoria($wemp_pmla,$basedatoshce,$historia,$ingreso){
	$conexion = obtenerConexionBD("matrix");

	// Se comenta porque se necesita saber si trajo resultados sin que saque el mensaje que saca esta función
	//$resumenHCE = consultarAliasPorAplicacion($conexion, $wemp_pmla, 'resumenHCE');
	
	$sql = " SELECT Detval
			   FROM root_000051
			  WHERE Detapl = 'resumenHCE'
				AND Detemp = '".$wemp_pmla."' ";
	$res = mysql_query( $sql, $conexion );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 )
	{
		$rows = mysql_fetch_array( $res );
		$resumenHCE = $rows[ 'Detval' ];
	}
	else
	{
		$resumenHCE = "";
	}

	
	$tablasConsulta = explode(',',$resumenHCE);

	if($resumenHCE!="")
	{
		$q = "";
		$cont=0;
	
		foreach ($tablasConsulta as $tablaConsulta)
		{
			$tabla_con = explode('-',$tablaConsulta);
			$tabla = $tabla_con[0];
			$consecutivo = $tabla_con[1];
			$tabla = $basedatoshce."_".$tabla;
			
			if($cont>0)
				$q .= " UNION ALL ";
			
			$q .= " SELECT ".$tabla.".movdat, ".$tabla.".Fecha_data, ".$tabla.".Hora_data
					  FROM ".$tabla."
					 WHERE ".$tabla.".movcon = ".$consecutivo."
					   AND movhis = '".$historia."'
					   AND moving = '".$ingreso."' ";
			
			$cont++;
		}
		
		$q .= " ORDER BY Fecha_data DESC, Hora_data DESC ";
		
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
	}
	else
	{
		$num = 0;
	}
	
	if($num > 0)
	{
		$rs = mysql_fetch_array($res);
		$consulta = $rs['movdat'];
	} 
	else
	{
		$consulta = "";
	}
	
	return $consulta;
}


/**
 * 
 * @param $wbasedato
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $codigoExamen
 * @param $nombreExamen
 * @param $observaciones
 * @param $estadoExamen
 * @param $fechaDeSolicitado
 * @param $usuario
 * @param $consecutivoOrden
 * @param $firma
 * @param $observacionesOrden
 * @param $justificacion
 * @param $consecutivoExamen
 * @param $numeroItem
 * @return unknown_type
 */
function grabarExamenKardex($wbasedato,$historia,$ingreso,$fecha,$codigoExamen,$nombreExamen,$observaciones,$estadoExamen,$fechaDeSolicitado,$usuario,$consecutivoOrden,$firma,$observacionesOrden,$justificacion,$consecutivoExamen,$numeroItem,$impExamen,$altExamen,$firmHCE){

	global $whce;

	$conexion = obtenerConexionBD("matrix");
	
	$estado = "0";

	//Primero verifico si ya existe el artículo en el detalle del kardex para saber si es insert o update
	$q = "SELECT
				Fecha_data,Hora_data, Ekacod, Ekahis, Ekaing, Ekafec, Ekaest, Ekaobs, Ekafes, Ekajus, Seguridad
			FROM	
				".$wbasedato."_000061
			WHERE 
				Ekacod = '$codigoExamen'
				AND Ekahis = '$historia'
				AND Ekaing = '$ingreso'
				AND Ekafec = '$fecha'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	//Si existe el registro ACTUALIZO caso contrario INSERTO
	if($num == 0){
//		$audAnterior = ""; '$usuario','$firma',
			
//		$q = "INSERT INTO ".$wbasedato."_000061
//				(Medico, Fecha_data,Hora_data, Ekacod, Ekahis, Ekaing, Ekafec, Ekaest, Ekaobs, Ekafes, Ekajus, Seguridad)
//			VALUES 
//				('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','$codigoExamen','$historia','$ingreso','$fecha','$estadoExamen','$observaciones','$fechaDeSolicitado','$justificacion','A-$usuario')";
		
		$q = "INSERT INTO ".$wbasedato."_000061
				(Medico, Fecha_data,Hora_data, Ekacod, Ekahis, Ekaing, Ekafec, Ekaest, Ekaobs, Ekafes, Ekajus,Ekausu,Ekafir, Seguridad)
			VALUES 
				('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','$codigoExamen','$historia','$ingreso','$fecha','$estadoExamen','$observaciones','$fechaDeSolicitado','$justificacion','$usuario','$firma','A-$usuario')";

		$estado = "1";
	} else {
//		$audAnterior = "A:".$fila['Ekacod'].",".$fila['Ekaest'].",".$fila['Ekaobs'].",".$fila['Ekafes'].",".$fila['Ekajus'];

		$q = "UPDATE ".$wbasedato."_000061 SET
				Ekaest = '$estadoExamen',
				Ekaobs = '$observaciones',
				Ekafes = '$fechaDeSolicitado',
				Ekajus = '".mysqli_real_escape_string( $conexion, $justificacion )."',
				Ekausu = '$usuario',
				Ekafir = '$firma'
			WHERE 
				Ekacod = '$codigoExamen'
				AND Ekahis = '$historia' 
				AND Ekaing = '$ingreso' 
				AND Ekafec = '$fecha'";
			
		$estado = "2";
	}
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Inserción en Ordenes de HCE

	//Verifico que exista EL ENCABEZADO DE LA orden
	$q = "SELECT
				Fecha_data,Hora_data,Ordfec,Ordhor,Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Seguridad  
			FROM
				{$whce}_000027
			WHERE
				Ordtor = '$codigoExamen'
				AND Ordhis = '$historia'
				AND Ording = '$ingreso'
				AND Ordnro = '$consecutivoOrden'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	//Si existe el registro ACTUALIZO caso contrario INSERTO
	if($num == 0){
	
		if($codigoExamen == "H"){
		
			$sqlCons = "SELECT 
							Detval as Ccocor
						FROM 
							root_000051 SET
						WHERE 
							Detemp = '10' 
							AND Detapl = 'consecutivoAyudasHospitalarias';";
		}
		else {
			$sqlCons = "SELECT 
							Ccocor
						FROM 
							".$wbasedato."_000011
						WHERE 
							Ccocod = '$codigoExamen';"; 
		}
		
		$resCons = mysql_query($sqlCons, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sqlCons . " - " . mysql_error());
			
		if( $rowsCons = mysql_fetch_array( $resCons ) ){
			$consecutivoOrden = $rowsCons[ 'Ccocor' ];
		}
		else{
			return "Error";
		}
	
		
		$estado = "1";
		
		$audAnterior = "";
		
		$q = "INSERT INTO {$whce}_000027
				(Medico,Fecha_data,Hora_data,Ordfec,Ordhor,Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Ordusu,Seguridad)
			VALUES 
				('hceidc','".date("Y-m-d")."','".date("H:i:s")."','$fecha','".date("H:i:s")."','$historia','$ingreso','$codigoExamen','$consecutivoOrden','$observacionesOrden','Pendiente','on','$firma','$usuario','C-hceidc')";

		//Solo cuando la orden es nueva se incrementan los consecutivos

		if($codigoExamen == "H"){
			$qInc = "UPDATE root_000051 SET
						Detval = Detval + 1
					WHERE 
						Detemp = '10' 
						AND Detapl = 'consecutivoAyudasHospitalarias';";
		} else {
			$qInc = "UPDATE ".$wbasedato."_000011 SET
					Ccocor = Ccocor + 1
				WHERE 
					Ccocod = '$codigoExamen';"; 
		}
		
		$resInc = mysql_query($qInc, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qInc . " - " . mysql_error());

	} else {
		
		$estado = "2";
		
//		$audAnterior = "A:".$codigoExamen.",".$consecutivoOrden.",".$fila['Ekaest'].",".$fila['Ekaobs'].",".$fila['Ekafes'].",".$fila['Ekajus'];
		
		if(!empty($observacionesOrden)){
			$hora = date("H:i:s");
				
			$q = "UPDATE {$whce}_000027 SET
					Ordobs = CONCAT(Ordobs,'\r\n','"."Observacion añadida el $fecha a las $hora:\r\n$observacionesOrden"."'),
					Ordest = 'on'
				WHERE
					Ordhis = '$historia'
					AND Ording = '$ingreso'
					AND Ordtor = '$codigoExamen'
					AND Ordnro = '$consecutivoOrden'";
		} else {
			$q = "UPDATE {$whce}_000027 SET
					Ordest = 'on'
				WHERE
					Ordhis = '$historia'
					AND Ording = '$ingreso'
					AND Ordtor = '$codigoExamen'
					AND Ordnro = '$consecutivoOrden'";
		}
	}
	
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	
	//Verifico que exista EL DETALLE de la orden (el examen en particular)
	$q = "SELECT
				a.Fecha_data,a.Hora_data,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detjus,Detfec,a.Seguridad, Descripcion, Detfir  
			FROM	
				{$wbasedato}_000159 a, {$whce}_000047 b
			WHERE 
				Dettor = '$codigoExamen'
				AND Detnro = '$consecutivoOrden'
				AND Detcod = '$consecutivoExamen'
				AND Detite = '$numeroItem'
				AND Detcod = codigo
			UNION
		  SELECT
				a.Fecha_data,a.Hora_data,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detjus,Detfec,a.Seguridad, Descripcion, Detfir
			FROM	
				{$wbasedato}_000159 a, {$whce}_000017 b
			WHERE 
				Dettor = '$codigoExamen'
				AND Detnro = '$consecutivoOrden'
				AND Detcod = '$consecutivoExamen'
				AND Detite = '$numeroItem'
				AND Detcod = codigo
				AND nuevo = 'on'";

//	echo "Existe examen::".$q;
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	$item = 1;
	
	if( $numeroItem == 0 ){

		//Busco el consecutivo del item
		$sql = "SELECT MAX(Detite) as item
				  FROM {$wbasedato}_000159
				 WHERE dettor = '$codigoExamen'
				   AND detnro = '$consecutivoOrden'";
		$resItem = mysql_query($sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numItem = mysql_num_rows($resItem);
		$filaItem = mysql_fetch_array($resItem);

		if( $numItem > 0 ){
			$item = $filaItem['item']+1;
		}
	}
	
	

	$qrep = "SELECT
				Fecha_data,Hora_data,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detjus,Detfec,Seguridad  
			FROM	
				{$wbasedato}_000159
			WHERE 
				Dettor = '$codigoExamen'
				AND Detnro = '$consecutivoOrden'
				AND Detcod = '$consecutivoExamen'
				AND Detesi = '$estadoExamen'
				AND Detest = 'on'
				AND Detfec = '$fechaDeSolicitado'
				AND Detjus = '".mysqli_real_escape_string( $conexion, utf8_decode( $justificacion ) )."'
				AND Detusu = '$usuario'
				AND Detite = '$numeroItem'
				AND Detifh = '$firmHCE'
				AND Detfir = '$firma'
				";

//	echo "Existe examen::".$q;
	$resrep = mysql_query($qrep, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qrep . " - " . mysql_error());
	$numrep = mysql_num_rows($resrep);


	if($numrep==0)
	{
		
		//Si existe el registro ACTUALIZO caso contrario INSERTO
		if($num == 0){
			$audNuevo = "N:".$codigoExamen.",".$consecutivoOrden.",".$item.",".$estadoExamen.",".utf8_decode( $justificacion ).",".$fechaDeSolicitado;
			
			$numeroItem = $item;
			
			$estado = "1";
			
			$q = "INSERT INTO {$wbasedato}_000159
					(Medico,Fecha_data,Hora_data,Dettor,Detnro,Detcod,Detesi,Detrdo,Detfec,Detest,Detjus,Detite,Detusu,Detfir,Detimp,Detalt,Detifh,Seguridad)
				VALUES 
					('$wbasedato','".date("Y-m-d")."','".date("H:i:s")."','$codigoExamen','$consecutivoOrden','$consecutivoExamen','$estadoExamen','','$fechaDeSolicitado','on','".utf8_decode( $justificacion )."','$item','$usuario','','$impExamen','$altExamen','$firmHCE','C-$wbasedato')";
		} else {
			
			$estado = "2";
			
			// $fila = mysql_fetch_array($res);
			
			$audAnterior = "A:".$codigoExamen.",".$consecutivoOrden.",".$numeroItem.",".$fila['Detesi'].",".$fila['Detjus'].",".$fila['Detfec'];
			$audNuevo = "N:".$codigoExamen.",".$consecutivoOrden.",".$numeroItem.",".$estadoExamen.",".utf8_decode( $justificacion ).",".$fechaDeSolicitado;
			
			$q = "UPDATE {$wbasedato}_000159 SET
					Detfec = '$fechaDeSolicitado',
					Detjus = '".mysqli_real_escape_string( $conexion, utf8_decode( $justificacion ) )."',
					Detesi = '$estadoExamen',
					Detusu = '$usuario',
					Detfir = '$firma',
					Detalt = '$altExamen',
					Detimp = '$impExamen',
					Detest = 'on',
					Detifh = '$firmHCE'
				WHERE
					Dettor = '$codigoExamen'
					AND Detnro = '$consecutivoOrden'
					AND Detcod = '$consecutivoExamen'
					AND Detite = '$numeroItem'
				";
		}
	//	echo "Detalle::".$q;
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	
	//Generación de auditoria cambio / creación
//	$audNuevo = "N:".$codigoExamen.",".$estadoExamen.",".$observaciones.",".$fechaDeSolicitado;

	$mensajeAuditoria = "";

	switch ($estado){
		case "1":
			$mensajeAuditoria = obtenerMensaje('MSJ_EXAMEN_CREADO');
			break;
		case "2":
			$mensajeAuditoria = obtenerMensaje('MSJ_EXAMEN_ACTUALIZADO');
			break;
		default:
			$mensajeAuditoria = obtenerMensaje('MSJ_EXAMEN_NO_CREADO');
			break;

	}

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior $audNuevo";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	if( trim( $auditoria->descripcion ) != "" ){
		registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);
	}
	
	//A tener en cuenta
	// - $codigoExamen corresponde al servicio de la tabla 17
	// - $consecutivoOrden corresponde al numero de la orden
	// - $consecutivoExamen corresponde al codigo del examen de la 17
	//echo ".......consecutivoExamen: \n".$consecutivoExamen." .... consecutivoOrden:".$consecutivoOrden." \n ........ codigoExamen: ".$codigoExamen;
	datosHL7( $conexion, $wbasedato, $whce, $historia, $ingreso, $consecutivoExamen, $consecutivoOrden, $codigoExamen, $numeroItem, $estadoExamen, $usuario );
	
	liberarConexionBD($conexion);

	return $estado."|$consecutivoOrden|$numeroItem";	//Noviembre 08 de 2012
}

function eliminarArticuloDetalle($wbasedato,$historia,$ingreso,$fecha,$codArticulo,$usuario,$fechaInicio,$horaInicio){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	//Primero verifico si ya existe el artículo en el detalle del kardex para saber si es INSERT o UPDATE
	$q = "SELECT
				Kadart, Kadcfr, Kadufr, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadvia, Kadfec, Kadcon, Kadobs, Kadsus, Kadcnd, Kaddma, Kaddis, Kaduma, Kadcma, Defdup, Kadido
			FROM
				".$wbasedato."_000060, {$wbasedato}_000059
			WHERE
				 Kadhis = '$historia'
				 AND Kading = '$ingreso'
				 AND Kadart = '$codArticulo'
				 AND Kadart = Defart
				 AND Kadfec = '$fecha'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	$audAnterior = "A:".$codArticulo.",".$fila['Kadcfr'].",".$fila['Kadufr'].",".$fila['Kaddia'].",".$fila['Kadest'].",".$fila['Kadess'].",".$fila['Kadfin'].",".$fila['Kadhin'].",".$fila['Kadper'].",".$fila['Kadvia'].",".$fila['Kadfes'].",".$fila['Kadhes'].",".$fila['Kadcon'].",".$fila['Kadobs'].",".$fila['Kadsus'].",".$fila['Kadcnd'].",".$fila['Kaddma'].",".$fila['Kaddis'].",".$fila['Kadcma'].",".$fila['Kaduma'];

	//Si el articulo no es duplicable, borro por codigo de articulo
	if($fila['Defdup'] != 'on'){
		$q = "DELETE FROM
				".$wbasedato."_000060
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadart = '$codArticulo'
				AND Kadfec = '$fecha'";
	} else {
		$q = "DELETE FROM
				".$wbasedato."_000060
			WHERE
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadart = '$codArticulo'
				AND Kadfec = '$fecha'
				AND Kadfin = '$fechaInicio'
				AND Kadhin = '$horaInicio'";
	}

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	if( mysql_affected_rows() > 1 ){
		registrarAuditoriaCierreKardex( $historia, $ingreso, $usuario, "*Mas de un articulo eliminado*|Kadart:$codArticulo-Kadfec:$fecha-Kadfin:$fechaInicio-Kadhin:$horaInicio|" );	//Mayo 20 de 2011
	}

	$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_ELIMINADO');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;
	$auditoria->idOriginal = $fila['Kadido'];

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

	liberarConexionBD($conexion);

	return $estado;
}

function consultarEsquemaInsulinaPorCodigo($basedatos,$codigo){
	$conexion = obtenerConexionBD("matrix");

	$q = "SELECT
			Esdcod,Esdest,Esdime,Esdima,Esddos,Esdudo,(SELECT Unides FROM {$basedatos}_000027 WHERE Unicod = Esdudo) unDosis,Esdobs,Esdvia,(SELECT Viades FROM {$basedatos}_000040 WHERE Viacod = Esdvia) via 
		FROM 
			{$basedatos}_000069
  		WHERE 
  			Esdest = 'on'
  			AND Esdcod = '$codigo'
		ORDER by
			Esdime";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	//Seleccion de unidades de manejo
	$q2 = "SELECT
			Unicod, Unides
		FROM
			".$basedatos."_000027
		WHERE
			Uniest = 'on'
		ORDER BY
			Unicod;";

	$colUnidades = array();

	$res2 = mysql_query($q2, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);

	$cont1 = 0;

	while($cont1 < $num2)
	{
		$info = mysql_fetch_array($res2);

		$unidad = new RegistroGenericoDTO();

		$unidad->codigo = $info['Unicod'];
		$unidad->descripcion = $info['Unides'];

		$cont1++;

		$colUnidades[] = $unidad;
	}

	//Seleccion de vias de suministro
	$q3 = "SELECT
			Viacod, Viades
		FROM
			".$basedatos."_000040
		ORDER BY
			Viades ASC;";

	$colVias = array();

	$res3 = mysql_query($q3, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
	$num3 = mysql_num_rows($res3);

	$cont1 = 0;
	while($cont1 < $num3)
	{
		$info = mysql_fetch_array($res3);

		$via = new RegistroGenericoDTO();

		$via->codigo = $info['Viacod'];
		$via->descripcion = $info['Viades'];

		$cont1++;

		$colVias[] = $via;
	}

	$consulta = "<table>";

	$consulta = $consulta."<tr class=encabezadoTabla align=center><td>M&iacute;nimo</td><td>M&aacute;ximo</td><td>Dosis</td><td>V&iacute;a</td><td>Observaciones</td></tr>";

	$cont1 = 0;

	if($num > 0){
		$clase = "fila2";
		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);

			if($clase=="fila1"){
				$clase = "fila2";
			} else {
				$clase = "fila1";
			}

			$consulta = $consulta."<tr class=$clase align=center>";

			$consulta = $consulta."<td>{$rs['Esdime']}</td>";
			$consulta = $consulta."<td>{$rs['Esdima']}</td>";
			$consulta = $consulta."<td><input type='text' id='wdexint$cont1' value='{$rs['Esddos']}' size=5 maxlength=6 class='textoNormal' onKeyPress='return validarEntradaEntera(event);'> ";

			//Seleccion de unidades de medida
			$consulta = $consulta."<select id='wdexseludo$cont1' class=seleccionNormal>";

			foreach ($colUnidades as $unidad){
				if($unidad->codigo == $rs['Esdudo']){
					$consulta = $consulta."<option value='$unidad->codigo' selected>".htmlentities(ucfirst(strtolower($unidad->descripcion)))."</option>";
				} else {
					$consulta = $consulta."<option value='$unidad->codigo'>".htmlentities(ucfirst(strtolower($unidad->descripcion)))."</option>";
				}
			}

			$consulta = $consulta."</select>";
			$consulta = $consulta."</td>";

			$consulta = $consulta."<td>";

			//Seleccion de unidades de medida
			$consulta = $consulta."<select id='wdexselvia$cont1' class=seleccionNormal>";

			foreach ($colVias as $via){
				if($via->codigo == $rs['Esdvia']){
					$consulta = $consulta."<option value='$via->codigo' selected>".htmlentities($via->descripcion)."</option>";
				} else {
					$consulta = $consulta."<option value='$via->codigo'>".htmlentities($via->descripcion)."</option>";
				}
			}
			$consulta = $consulta."</select>";
			$consulta = $consulta."</td>";

			$consulta = $consulta."<td><input type='text' id='wdexobs$cont1' value='{$rs['Esdobs']}' size=40 maxlength=40 class='textoNormal'></td>";

			$consulta = $consulta."</tr>";

			$cont1++;
		}
	}
	$consulta = $consulta."</table>";

	return $consulta;
}

/************************************************************************************************************************
 * Desactiva un item de la orden
 * 
 * @param $wbasedato
 * @param $historia
 * @param $ingreso
 * @param $fecha
 * @param $codigoExamen
 * @param $usuario
 * @param $numeroOrden
 * @param $numeroItem
 * @return unknown_type
 * 
 * Modificaciones
 * 
 * Enero 27 de 2011
 * - Se modifica para que se elimine las ordenes de HCE.
 ************************************************************************************************************************/
function eliminarExamenKardex($wbasedato,$historia,$ingreso,$fecha,$codigoExamen,$numeroOrden,$usuario,$numeroItem ){
	
	global $whce;

	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	//Primero verifico si ya existe el artículo en el detalle del kardex para saber si es INSERT o UPDATE
	$q = "SELECT
				Ekacod,Ekahis,Ekaing,Ekafec,Ekaest,Ekaobs,Ekafes
			FROM
				".$wbasedato."_000061
			WHERE
				 Ekacod = '$codigoExamen'
				 AND Ekahis = '$historia'
				 AND Ekaing = '$ingreso'
				 AND Ekafec = '$fecha'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	$audAnterior = "A:".$fila['Ekacod'].",".$numeroOrden.",".$numeroItem.",".$fila['Ekaest'].",".$fila['Ekaobs'].",".$fila['Ekafes'];

	//Si existe el registro ACTUALIZO caso contrario INSERTO
	$q = "DELETE FROM
				".$wbasedato."_000061
			WHERE
				Ekacod = '$codigoExamen'
				AND Ekahis = '$historia'
				AND Ekaing = '$ingreso'
				AND Ekafec = '$fecha'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$mensajeAuditoria = obtenerMensaje('MSJ_EXAMEN_ELIMINADO');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);
	
	
	//Desactivando HL7 para la orden
	$q = "SELECT 
			  Arc_HL7, c.codigo as codigo, a.id as id
		  FROM
			  {$wbasedato}_000159 a, {$whce}_000015 b, {$whce}_000017 c
		  WHERE
			  detcod = c.codigo
			  AND tipoestudio = b.codigo
			  AND Dettor = '$codigoExamen'
			  AND Detnro = '$numeroOrden'
			  AND Detite = '$numeroItem'
		 ;";
		 
	//Desactivando HL7 para la orden
	$q = "SELECT 
			  Arc_HL7, c.codigo as codigo, a.id as id
		  FROM
			  {$wbasedato}_000159 a, {$whce}_000015 b, {$whce}_000047 c
		  WHERE
			  detcod = c.codigo
			  AND tipoestudio = b.codigo
			  AND Dettor = '$codigoExamen'
			  AND Detnro = '$numeroOrden'
			  AND Detite = '$numeroItem'
		UNION
		SELECT 
			  Arc_HL7, c.codigo as codigo, a.id as id
		  FROM
			  {$wbasedato}_000159 a, {$whce}_000015 b, {$whce}_000017 c
		  WHERE
			  detcod = c.codigo
			  AND tipoestudio = b.codigo
			  AND Dettor = '$codigoExamen'
			  AND Detnro = '$numeroOrden'
			  AND Detite = '$numeroItem'
		 ;";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$sql = "UPDATE
					{$wbasedato}_000159
				SET
					Detest = 'off'
				WHERE
					id = '{$rows['id']}'
				";

		$res = mysql_query( $sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		
		if( trim( $rows['Arc_HL7'] ) != '' && trim( $rows['Arc_HL7'] ) != 'NO APLICA' ){
			
			$sql = "SELECT 
				 		id
			  		FROM
				  		{$whce}_{$rows['Arc_HL7']}
			  		WHERE
			  			hl7his = '$historia'
					  	AND hl7ing = '$ingreso'
					  	AND hl7des = '$codigoExamen'
					  	AND hl7nor = '$numeroOrden'
					  	AND hl7nit = '$numeroOrden-$numeroItem'
			 		;";
			
			$res = mysql_query($sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			
			for(; $rowsHL7 = mysql_fetch_array( $res ); ){
				cancelarDatosHL7( $conexion, "hceidc", $rows['Arc_HL7'], $rowsHL7['id'] );
			}
		}
	}
	
	liberarConexionBD($conexion);

	$estado = "1";

	return $estado;
}

function eliminarInfusionKardex($wbasedato,$historia,$ingreso,$fecha,$componentes,$consecutivo,$usuario){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	//Primero verifico si ya existe el artículo en el detalle del kardex para saber si es INSERT o UPDATE
	$q = "SELECT
				Inkhis,Inking,Inkfec,Inkcon,Inkdes,Inkobs
			FROM
				".$wbasedato."_000062
			WHERE
				 Inkhis = '$historia'
				 AND Inking = '$ingreso'
				 AND Inkfec = '$fecha'
				 AND Inkcon = '$consecutivo'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	$audAnterior = "A:".$fila['Inkcon'].",".$fila['Inkdes'].",".$fila['Inkobs'];

	//Si existe el registro ACTUALIZO caso contrario INSERTO
	$q = "DELETE FROM
				".$wbasedato."_000062
			WHERE
				Inkhis = '$historia'
				AND Inking = '$ingreso'
				AND Inkfec = '$fecha'
				AND Inkcon = '$consecutivo'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$mensajeAuditoria = obtenerMensaje('MSJ_INFUSION_ELIMINADA');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

	liberarConexionBD($conexion);

	$estado = "1";

	return $estado;
}

function eliminarMedicoTratante($wbasedato,$historia,$ingreso,$usuario,$idRegistro,$fecha){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	$audAnterior = "A:".$idRegistro;

	//Mtrhis  Mtring  Mtrmed  Mtrest
	$q = "UPDATE
			hceidc_000022
		SET
			Mtrest = 'off',
			Mtrtra = 'off'  
		WHERE
			Mtrhis = '".$historia."'
			AND	Mtring = '$ingreso'
			AND Mtrmed = '$idRegistro'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$mensajeAuditoria = obtenerMensaje('MSJ_MEDICO_RETIRADO');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

	liberarConexionBD($conexion);

	$estado = "1";

	return $estado;
}

function actualizarAlergiaPorFecha($basedatos,$historia,$ingreso,$fecha,$descripcion,$usuario){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	$q = "UPDATE ".$basedatos."_000053 SET
				Karale = '$descripcion'
			WHERE
				Fecha_data = '$fecha'
				AND	Karhis = '$historia'
				AND Karing = '$ingreso'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$mensajeAuditoria = obtenerMensaje('MSJ_ALERGIA_MODIFICADA');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "N: ".$descripcion;
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$basedatos,$auditoria);

	liberarConexionBD($conexion);

	$estado = "1";

	return $estado;
}

function grabarEsquemaDextrometer($basedatos,$historia,$ingreso,$fecha,$codInsulina,$frecuencia,$codEsquema,$arrDosis,$arrUDosis,$arrVia,$arrObservaciones,$usuario, $actualizaIntervalos){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	//Seleccion del esquema dextrometer para obtener los intervalos
	$q2 = "SELECT
				Esdime,Esdima,Esddos,Esdudo,Esdobs,Esdvia  
			FROM 
			{$basedatos}_000069
			WHERE
				Esdest = 'on'
				AND Esdcod = '$codEsquema'
			ORDER BY 
				Esdime;";

	$res2 = mysql_query($q2, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);

	//Existe el codigo del dextrometer
	$q = "UPDATE {$basedatos}_000070 SET
				Infade = '$codInsulina',
				Inffde = '$frecuencia',
				Infcde = '$codEsquema'
			WHERE
				Infhis = '$historia'
				AND Infing = '$ingreso'
				AND Inffec = '$fecha'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$existe = mysql_affected_rows();

	$descripcion = "$codInsulina,$frecuencia,$codEsquema";

	if($existe <= 0){
		$qIns="INSERT INTO {$basedatos}_000070 (
				Medico, Fecha_data, Hora_data, Infhis, Infing, Inffec, Infade, Inffde, Infcde, Seguridad)
			VALUES
				('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$fecha."','".$codInsulina."','$frecuencia','$codEsquema','A-$usuario')";

		$resIns = mysql_query($qIns, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qIns . " - " . mysql_error());
	}

	if($actualizaIntervalos == "on"){
		//Borra los intervalos eventuales que existan
		$qDel="DELETE FROM
		{$basedatos}_000071
			WHERE
				Indhis = '$historia'	
				AND Inding = '$ingreso'
				AND Indfec = '$fecha'
			";

		$resDel = mysql_query($qDel, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qDel . " - " . mysql_error());

		$cont1 = 0;

		//Los datos de los inputs y selects llegan serializados separados por | (pipe)
		$vecDosis 			= explode("|",$arrDosis);
		$vecUDosis			= explode("|",$arrUDosis);
		$vecVia 			= explode("|",$arrVia);
		$vecObservaciones 	= explode("|",$arrObservaciones);

		while($cont1 < $num2)
		{
			$info = mysql_fetch_array($res2);

			$qInt="INSERT INTO {$basedatos}_000071 (
				Medico, Fecha_data, Hora_data, Indhis, Inding, Indfec, Indime, Indima, Inddos, Indudo, Indobs, Indvia,Indusu,Seguridad)
			VALUES
				('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$fecha."','".$info['Esdime']."','".$info['Esdima']."','".$vecDosis[$cont1]."','".$vecUDosis[$cont1]."','".$vecObservaciones[$cont1]."','".$vecVia[$cont1]."','".$usuario."','A-$usuario')";

			$resInt = mysql_query($qInt, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $qInt . " - " . mysql_error());

			$cont1++;
		}
	}

	$mensajeAuditoria = obtenerMensaje('MSJ_ESQUEMA_GRABADO');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "N: ".$descripcion;
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$basedatos,$auditoria);

	liberarConexionBD($conexion);

	$estado = "1";

	return $estado;
}

function insertarDietaKardex($wbasedato,$historia,$ingreso,$usuario,$fecha,$idRegistro){
	$conexion = obtenerConexionBD("matrix");

	$q = "INSERT INTO ".$wbasedato."_000064(
				Medico,Fecha_data,Hora_data,Dikcod,Dikhis,Diking,Dikfec,Dikest,Dikusu,Seguridad)
			VALUES
				('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','".$idRegistro."','".$historia."','".$ingreso."','$fecha','on','$usuario','A-$usuario')";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$audNuevo = "N: $idRegistro";

	//Auditoria
	$mensajeAuditoria = obtenerMensaje('MSJ_DIETA_ASOCIADA');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audNuevo";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

	liberarConexionBD($conexion);

	return "1";
}

function eliminarDietaKardex($wbasedato,$historia,$ingreso,$usuario,$idRegistro,$fecha){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	//Primero verifico si ya existe el artículo en el detalle del kardex para saber si es INSERT o UPDATE
	$q = "SELECT
				Dikcod,Dikhis,Diking,Dikfec,Dikest
			FROM
				".$wbasedato."_000064
			WHERE
				Dikcod = '$idRegistro'
				AND Dikhis = '$historia'
				AND Diking = '$ingreso'
				AND Dikfec = '$fecha'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	$audAnterior = "A: ".$fila['Dikcod'];

	$q = "DELETE FROM
				".$wbasedato."_000064
			WHERE
				Dikcod = '$idRegistro'
				AND Dikhis = '$historia'
				AND Diking = '$ingreso'
				AND Dikest = 'on'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$mensajeAuditoria = obtenerMensaje('MSJ_DIETA_RETIRADA');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

	liberarConexionBD($conexion);

	$estado = "1";

	return $estado;
}
function eliminarEsquemaDextrometer($basedatos,$historia,$ingreso,$fecha,$codInsulina,$usuario){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	$q = "UPDATE
				{$basedatos}_000070
			SET
				Infade = '',
				Inffde = '', 
				Infcde = '' 		
			WHERE
				Infhis = '$historia'
				AND Infing = '$ingreso'  
				AND Inffec = '$fecha';";
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$audAnterior = "A: $codInsulina";

	$q = "DELETE FROM
				{$basedatos}_000071
			WHERE
				Indhis = '$historia'
				AND Inding = '$ingreso'
				AND Indfec = '$fecha';";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$mensajeAuditoria = obtenerMensaje('MSJ_ESQUEMA_ELIMINADO');

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($conexion,$basedatos,$auditoria);

	liberarConexionBD($conexion);

	$estado = "1";

	return $estado;
 }

function consultarClases($idArbol){
	global $wbasedato;
	global $conex;

	$q = "SELECT
			Clacod, Clades  
		FROM 
			".$wbasedato."_000072 
		WHERE
			Claarb = '$idArbol'
			AND Claest = 'on';";

	
	$coleccion = array();

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	if($num > 0 ){
		while($cont1 <= $num)
		{
			$info = mysql_fetch_array($res);

			$registro = new RegistroSimple();

			$registro->codigo = $info['Clacod'];
			$registro->descripcion = $info['Clades'];

			$cont1++;

			$coleccion[] = $registro;
		}
	}
	return $coleccion;
}

/**
 * El nivel A (primer nivel ya debe estar construido)
 * @param $basedatos
 * @param $nivelA
 * @param $nivelB
 * @param $nivelC
 * @param $nivelD
 * @param $usuario
 * @return unknown_type
 */
function consultarNiveles($basedatos,$nivelA,$nivelB,$nivelC,$nivelD){
	$conexion = obtenerConexionBD("matrix");
	$rama = "";

	//Nivel 1 - Si viene con valor nivelA y el resto no
	if(isset($nivelA) && $nivelA != '' && (!isset($nivelB) || $nivelB == '') && (!isset($nivelC) || $nivelC == '') && (!isset($nivelD) || $nivelD == '')){
		$q = "SELECT
				Prkcod n1, Prkcon n2, '' n3, '' n4, Prkdes des 
			FROM 
				".$basedatos."_000073
			WHERE
				Prkcod = '$nivelA'
				AND Prkest = 'on'
			ORDER BY
				Prkdes;";

		$rama = 1;
	}

	//Nivel 2 - Si viene con valor nivelA, nivelB y el resto no
	if(isset($nivelA) && $nivelA != '' && $nivelB != '' && isset($nivelB) && (!isset($nivelC) || $nivelC == '') && (!isset($nivelD) || $nivelD == '')){
		$q = "SELECT
				Sekcod n1, Sekpri n2, Sekcon n3, '' n4,Sekdes des 
			FROM 
				".$basedatos."_000074
			WHERE
				Sekcod = '$nivelA'
				AND Sekpri = '$nivelB'
			ORDER BY
				Sekdes;";

		$rama = 2;
	}

	//Nivel 3 - Si viene con valor nivelA, nivelB, nivelC y el resto no
	if(isset($nivelA) && $nivelA != '' && isset($nivelB) && $nivelB != '' && isset($nivelC) && $nivelC != '' && (!isset($nivelD) || $nivelD == '')){
		$q = "SELECT
					Tekcod n1, Tekpri n2, Tekseg n3, Tekcon n4, Tekdes des
				FROM
					".$basedatos."_000080
				WHERE
					Tekcod = '$nivelA'
					AND Tekpri = '$nivelB'
					AND Tekseg = '$nivelC'
				ORDER BY
					Tekdes;";

		$rama = 3;
	}

	//Nivel 4 - Si viene con valor nivelA, nivelB, nivelC y nivelD
	if(isset($nivelA) && $nivelA != '' && isset($nivelB) && $nivelB != '' && isset($nivelC) && $nivelC != '' && isset($nivelD) && $nivelD != ''){
		$q = "SELECT
					Cukcod n1, Cukpri n2, Cukseg n3, Cukter n4, Cukcon con, Cukdes des
				FROM
					".$basedatos."_000081
				WHERE
					Cukcod = '$nivelA'
					AND Cukpri = '$nivelB'
					AND Cukseg = '$nivelC'
					AND Cukter = '$nivelD'
				ORDER BY
					Cukdes;";

		$rama = 4;
	}

	$coleccion = array();

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);


	$cont1 = 0;
	if($num > 0 ){
		while($cont1 < $num)
		{
			$info = mysql_fetch_array($res);

			$id = "{$info['n1']}-{$info['n2']}-{$info['n3']}-{$info['n4']}";

			switch ($rama){ //Verifico si el siguiente nivel tiene ramas
				case '1':
					$q2 = "SELECT
								COUNT(*) cnt2, Sekcod cod2, Sekcon con2, Sekdes des2 
							FROM 
							{$basedatos}_000074
							WHERE 
								Sekcod = '{$info['n1']}'
								AND Sekpri = '{$info['n2']}' 
							GROUP BY 
								Sekcod, Sekcon";
							break;
				case '2':
					$q2 = "SELECT
								COUNT(*) cnt2, Tekcod cod2, Tekcon con2, Tekdes des2 
							FROM 
							{$basedatos}_000080
							WHERE 
								Tekcod = '{$info['n1']}'
								AND Tekpri = '{$info['n2']}'
								AND Tekseg = '{$info['n3']}' 
							GROUP BY 
								Tekcod, Tekcon";
							break;
				case '3':
					$q2 = "SELECT
								COUNT(*) cnt2, Cukcod cod2, Cukcon con2, Cukdes des2 
							FROM 
							{$basedatos}_000081
							WHERE 
								Cukcod = '{$info['n1']}'
								AND Cukpri = '{$info['n2']}'
								AND Cukseg = '{$info['n3']}'
								AND Cukter = '{$info['n4']}' 
							GROUP BY 
								Cukcod, Cukcon";
							break;
				case '4':
					$q2 = "SELECT
								COUNT(*) cnt2, Cukcod cod2, Cukcon con2, Cukdes des2 
							FROM 
							{$basedatos}_000081
							WHERE 
								Id = 0 
							GROUP BY 
								Cukcod, Cukcon";
							break;
				default:
					break;
						
			}

			$res2 = mysql_query($q2, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$num2 = mysql_num_rows($res2);
				
			if($num2 > 0){
				$rama = "R";
				$info2 = mysql_fetch_array($res2);

				echo "<li id='$rama$id'><span>".htmlentities($info['des'])."</span>";
				echo "<ul class='ajax'>";
				echo "<li id='Ajax{$rama}{$id}'>{url:../../../include/movhos/kardex.inc.php?tree_id=1&consultaAjaxKardex=24&nivelA=".$info['n1']."&nivelB=".$info['n2']."&nivelC=".$info['n3']."&nivelD=".$info['n4']."&basedatos=$basedatos}</li>";
				echo "</ul>";
				echo "</li>";
			} else {
				$rama = "H";

				//Hojas finales
				echo "<li id='$rama$id'><span class='text'>".htmlentities($info['des'])."</span></li>";
			}
			$cont1++;
		}
	}
}

function registrarAuditoriaKardex($conexion,$wbasedato, $auditoria){

	$q = "INSERT INTO ".$wbasedato."_000055
				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaudes, Kaufec, Kaumen, Kauido, Seguridad)
			VALUES
				('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','$auditoria->historia','$auditoria->ingreso','".mysqli_real_escape_string( $conexion, $auditoria->descripcion )."','$auditoria->fechaKardex','".mysqli_real_escape_string( $conexion, $auditoria->mensaje )."','$auditoria->idOriginal','A-$auditoria->seguridad')";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function grabarInfusionKardex($wbasedato,$historia,$ingreso,$fecha,$componentes,$consecutivo,$observaciones,$usuario,$fechaSolicitud){
	$conexion = obtenerConexionBD("matrix");
	$estado = "0";

	//Primero verifico si ya existe el registro en el detalle para saber si es insert o update
	$q = "SELECT
				Fecha_data, Hora_data, Inkhis, Inking, Inkfec, Inkcon, Inkdes, Inkobs, Inkfes, Seguridad
			FROM
				".$wbasedato."_000062
			WHERE 
				Inkhis = '$historia'
				AND Inking = '$ingreso'
				AND Inkfec = '$fecha'
				AND Inkcon = '$consecutivo'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$fila = mysql_fetch_array($res);
	$num = mysql_num_rows($res);

	//Si existe el registro ACTUALIZO caso contrario INSERTO
	if($num == 0){
		$audAnterior = "";
			
		$q = "INSERT INTO ".$wbasedato."_000062
				(Medico, Fecha_data,Hora_data, Inkhis, Inking, Inkfec, Inkdes, Inkcon, Inkobs, Inkfes, Seguridad)
			VALUES 
				('mhosidc','".date("Y-m-d")."','".date("H:i:s")."','$historia','$ingreso','$fecha','$componentes','$consecutivo','$observaciones','$fechaSolicitud','A-$usuario')";

		$estado = "1";
	} else {
		$audAnterior = "A:".$fila['Inkcon'].",".$fila['Inkdes'].",".$fila['Inkobs'].",".$fila['Inkfes'];

		$q = "UPDATE ".$wbasedato."_000062 SET
				Inkdes = '$componentes',
				Inkobs = '$observaciones',
				Inkfes = '$fechaSolicitud'
			WHERE 
				Inkhis = '$historia'
				AND Inking = '$ingreso'
				AND Inkfec = '$fecha'
				AND Inkcon = '$consecutivo'";
			
		$estado = "2";
	}
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Generación de auditoria cambio / creación
	$audNuevo = "N:$consecutivo,$componentes,$observaciones";

	$mensajeAuditoria = "";

	switch ($estado){
		case "1":
			$mensajeAuditoria = obtenerMensaje('MSJ_INFUSION_CREADA');
			break;
		case "2":
			$mensajeAuditoria = obtenerMensaje('MSJ_INFUSION_ACTUALIZADA');
			break;
		default:
			$mensajeAuditoria = obtenerMensaje('MSJ_INFUSION_NO_CREADA');
			break;
	}

	$q = "INSERT INTO ".$wbasedato."_000055
				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaufec, Kaudes, Kaumen, Seguridad)
			VALUES 
				('mhosidc','".$fecha."','".date("H:i:s")."','$historia','$ingreso','$fecha','$audAnterior $audNuevo','$mensajeAuditoria','A-$usuario')";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	liberarConexionBD($conexion);

	return $estado;
}

function suspenderMedicamentoKardex($wbasedato,$historia,$ingreso,$codigoArticulo,$fecha,$estadoSuspension,$fechaInicio,$horaInicio,$usuario){
	$conexion = obtenerConexionBD("matrix");
	$estado = "0";

	$q = "UPDATE ".$wbasedato."_000060 SET
				Kadsus = '$estadoSuspension'
			WHERE 
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadfec = '$fecha'
				AND Kadfin = '$fechaInicio'
				AND Kadhin = '$horaInicio'
				AND Kadart = '$codigoArticulo'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	$idOriginal = 0;
	if( mysql_affected_rows() > 0 ){
	
		//Consulto el id Original
		$sql = "SELECT * 
				FROM
					".$wbasedato."_000060
				WHERE
					Kadhis = '$historia'
					AND Kading = '$ingreso'
					AND Kadfec = '$fecha'
					AND Kadfin = '$fechaInicio'
					AND Kadhin = '$horaInicio'
					AND Kadart = '$codigoArticulo'";
		
		$res = mysql_query($sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
			$rows = mysql_fetch_array( $res );
			$idOriginal = $rows[ 'Kadido' ];
		}
	}
	
	$estado = "1";

	$audAnterior = "$codigoArticulo";
	$audNuevo = "";

	//Generación de auditoria cambio / creación
	$mensajeAuditoria = "";

	switch ($estadoSuspension){
		case "on":
			$mensajeAuditoria = obtenerMensaje('MSJ_SUSPENDER_MEDICAMENTO');
			break;
		case "off":
			$mensajeAuditoria = obtenerMensaje('MSJ_ACTIVAR_MEDICAMENTO');
			break;
		default:
			$mensajeAuditoria = obtenerMensaje('MSJ_SUPENSION_NO_MODIFICADA');
			break;
	}

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	$auditoria->descripcion = "$audAnterior";
	$auditoria->fechaKardex = $fecha;
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;
	$auditoria->idOriginal = $idOriginal;

	registrarAuditoriaKardex($conexion,$wbasedato,$auditoria);

//	$q = "INSERT INTO ".$wbasedato."_000055
//				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaufec, Kaudes, Kaumen, Seguridad)
//			VALUES
//				('mhosidc','".$fecha."','".date("H:i:s")."','$historia','$ingreso','$fecha','$audAnterior $audNuevo','$mensajeAuditoria','A-$usuario')";
//
//	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	liberarConexionBD($conexion);

	return $estado;
}


function actualizarImpresion($wbasedato,$historia,$ingreso){
	$conexion = obtenerConexionBD("matrix");
	$estado = "0";

	$q = " UPDATE ".$wbasedato."_000060 SET
				Kadimp = 'on'
			WHERE 
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadfec = '$fecha'
				AND Kadest = 'on'";
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect==0)
	{
		$q = " UPDATE ".$wbasedato."_000054 SET
					Kadimp = 'on'
				WHERE 
					Kadhis = '$historia'
					AND Kading = '$ingreso'
					AND Kadfec = '$fecha'
					AND Kadest = 'on'";
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}

	
	$estado = "1";

	liberarConexionBD($conexion);

	return $estado;
}

function actualizarImpMedicamento($wbasedato,$articuloImp,$historia,$ingreso,$codigoArticulo,$fecha,$fechaInicio,$horaInicio){
	$conexion = obtenerConexionBD("matrix");
	$estado = "0";

	$q = "UPDATE ".$wbasedato."_000060 SET
				Kadimp = '$articuloImp'
			WHERE 
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadart = '$codigoArticulo'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect==0)
	{
		$q = "UPDATE ".$wbasedato."_000054 SET
					Kadimp = '$articuloImp'
				WHERE 
					Kadhis = '$historia'
					AND Kading = '$ingreso'
					AND Kadfec = '$fecha'
					AND Kadart = '$codigoArticulo'";

		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	
	$estado = "1";

	liberarConexionBD($conexion);

	return $estado;
}


function actualizarTipoManejo($wbasedato,$articuloInt,$historia,$ingreso,$codigoArticulo,$fecha,$fechaInicio,$horaInicio){
	$conexion = obtenerConexionBD("matrix");
	$estado = "0";

	$q = "UPDATE ".$wbasedato."_000060 SET
				Kadint = '$articuloInt'
			WHERE 
				Kadhis = '$historia'
				AND Kading = '$ingreso'
				AND Kadart = '$codigoArticulo'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect==0)
	{
		$q = "UPDATE ".$wbasedato."_000054 SET
					Kadint = '$articuloInt'
				WHERE 
					Kadhis = '$historia'
					AND Kading = '$ingreso'
					AND Kadfec = '$fecha'
					AND Kadart = '$codigoArticulo'";

		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	
	$estado = "1";

	liberarConexionBD($conexion);

	return $estado;
}


function actualizarIndicaciones($wbasedato,$historia,$ingreso,$fecha,$indicaciones){
	$conexion = obtenerConexionBD("matrix");
	$estado = "0";

	//Consulto el id Original
	$sql = "SELECT id
			FROM
				".$wbasedato."_000053
			WHERE 
				Karhis = '$historia'
				AND Karing = '$ingreso'
				AND Karest = 'on'
				AND Fecha_data = '$fecha'";
	
	$res = mysql_query($sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	$numrows = mysql_num_rows( $res );
		
	if($numrows>0)
	{
		$q = "UPDATE ".$wbasedato."_000053 SET
					Karegr = '$indicaciones'
				WHERE 
					Karhis = '$historia'
					AND Karing = '$ingreso'
					AND Karest = 'on'
					AND Fecha_data = '$fecha'";
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
	else
	{
		//Consulto el id Original
		$sql = "SELECT id
				FROM
					".$wbasedato."_000053
				WHERE 
					Karhis = '$historia'
					AND Karing = '$ingreso'
					AND Karest = 'on'
				ORDER BY id DESC
				LIMIT 1";
		
		$res = mysql_query($sql, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		$row = mysql_fetch_array( $res );
		$numrows = mysql_num_rows( $res );
		
		if($numrows>0)
		{
			$q = "UPDATE ".$wbasedato."_000053 SET
						Karegr = '$indicaciones'
					WHERE 
						id = '".$row['id']."'";
			$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	}
	
	$estado = "1";

	liberarConexionBD($conexion);

	return $estado;
}


function actualizarAltaExamen($wbasedato,$imprimirExamen,$codigoExamen,$fecha,$tipo_orden,$numero_orden,$item){
	$conexion = obtenerConexionBD("matrix");
	$estado = "0";
	
	if( !empty($item) ){
		$filtroItem = " AND Detite = '$item' ";
	}
	else{
		$filtroItem = "";
	}	

	$q = "UPDATE hceidc_000028 SET
				Detimp = '$imprimirExamen'
			WHERE 
				Dettor = '$tipo_orden'
				AND Detnro = '$numero_orden'
				AND Detcod = '$codigoExamen'
				AND Detfec = '$fecha'
				$filtroItem
		";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	
	$estado = "1";

	liberarConexionBD($conexion);

	return $estado;
}

/*
function consultarPestanas(){
	global $conex;
	global $wbasedato;

	$q = "SELECT
				Pescod, Pesnom, Pesest, Pespro, Pespos, Pestab
			FROM
				{$wbasedato}_000075
			WHERE
				Pesest = 'on'
			ORDER BY
				Pespos";

	$res 	= mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num 	= mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while ($cont1 < $num){
			$cont1++;

			$pestana = new pestanaKardexDTO();

			$info = mysql_fetch_array($res);

			$pestana->codigoPestana 	= $info['Pescod'];
			$pestana->nombrePestana 	= $info['Pesnom'];
			$pestana->estado 			= $info['Pesest'];
			$pestana->posicion 			= $info['Pespro'];
			$pestana->rutaScript 		= $info['Pespos'];
			$pestana->tablaGrabacion 	= $info['Pestab'];

			$coleccion[] = $pestana;
		}
	}
	return $coleccion;
}
*/


/**
 * Lista los centros de costos hospitalarios
 *
 * @param unknown_type $seleccionCco
 * @param unknown_type $funcion
 */
function centrosCostosHospitalariosOcupados(){
	global $conex;
	global $wbasedato;

  	$q = "SELECT
				ccocod, UPPER(Cconom)
			FROM
				".$wbasedato."_000011,
			(
				SELECT
					Habcco
				FROM
					".$wbasedato."_000020
				WHERE
					Habdis='off'
				GROUP BY
					Habcco
			) a
			WHERE
  				Ccohos = 'on'
  				AND Ccoing = 'off'
  				AND Ccoest = 'on'
  				AND a.Habcco = ccocod
  			UNION
				SELECT
					Ccocod, UPPER(Cconom)
				FROM
					".$wbasedato."_000011
				WHERE
					Ccourg = 'on'
			ORDER by 1;";
	 
	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	$coleccion = array();
	 
	if ($num1 > 0 )
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$cco = new centroCostosDTO();
			$row1 = mysql_fetch_array($res1);

			$cco->codigo = $row1[0];
			$cco->nombre = $row1[1];
				
			$coleccion[] = $cco;
		}
	}
	return $coleccion;
}

function validarFirmaElectronica($basedatos,$usuario,$firma){
	$conexion = obtenerConexionBD("matrix");
	$estado = "";

	$q = "SELECT
			Usucod,Usucla
		FROM 
			hceidc_000020 
		WHERE 
			Usucod = '".$usuario."' 
			AND Usucla = '".$firma."' 
			AND Usuest = 'on';";

	$res = mysql_query($q, $conexion);
	$num = mysql_num_rows($res);

	if($num > 0){
		$estado = "1";
	} else {
		if(!empty($firma)){
			$estado = "2";
		}
	}

	return $estado;
}

function consultarHabitacionPacienteServicio($basedatos,$servicio){
	$conexion = obtenerConexionBD("matrix");

	$q = "SELECT
			Habcod, Habcco, Habhis, Habing,
			(SELECT CONCAT(pacno1,' ', pacno2,' ', pacap1,' ', pacap2) FROM root_000036, root_000037 WHERE oriced = pacced AND oriori = '10' AND orihis = Habhis AND oriing = Habing AND Oritid = Pactid) nombre
		FROM
			{$basedatos}_000020
		WHERE
			Habcco = '$servicio'
			AND Habdis = 'off'
			AND Habhis != ''
			AND Habest = 'on'
		UNION
		SELECT 'Urgencias'Habcod, Ubisac Habcco, Ubihis Habhis, Ubiing Habing, IFNULL((
		SELECT CONCAT( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 )
		FROM root_000036, root_000037
		WHERE oriced = pacced AND oriori = '10' AND orihis = Ubihis AND oriing = Ubiing AND Oritid = Pactid ) , '')nombre
		FROM
			{$basedatos}_000018, {$basedatos}_000011
		WHERE
			Ccourg = 'on'
			AND Ccocod = Ubisac
			AND Ccoest = 'on'
			AND Ccocod = '$servicio'
			AND {$basedatos}_000018.fecha_data >= DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 2 DAY),'%Y-%m-%d')
		ORDER by 1,5;";

	$res = mysql_query($q, $conexion);
	$num = mysql_num_rows($res);

	//Con select
//	$consulta = "<select name='wselhab' onclick='javascript:seleccionHabitacionPaciente();' class='textoNormal'>";
//	$consulta = $consulta."<option value=''>Seleccione</option>";
//
//	$cont1 = 0;
//
//	if($num > 0){
//		while ($cont1 < $num)
//		{
//			$rs = mysql_fetch_array($res);
//
//			if(isset($rs['nombre']) && $rs['nombre'] != ''){
//				$consulta = $consulta."<option value='".$rs['Habhis']."'>".$rs['Habcod']." - ".$rs['nombre']."</option>";
//			}
//			$cont1++;
//		}
//	} else {
//		$consulta = $consulta."<option value=''>No hay habitaciones disponibles</option>";
//	}
//	$consulta = $consulta."</select>";

	//Con tabla
	$consulta = "<table>";

	$cont1 = 0;
	$clase = 'fila1';

	if($num > 0){
		$consulta = $consulta."<tr class=encabezadoTabla align=center>";
		$consulta = $consulta."<td>Habitacion</td>";
		$consulta = $consulta."<td>Historia</td>";
		$consulta = $consulta."<td>Paciente</td>";
		$consulta = $consulta."<td>Accion</td>";
		$consulta = $consulta."</tr>";

		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);

			if(isset($rs['nombre']) && $rs['nombre'] != ''){
				$consulta = $consulta."<tr class='$clase'>";
				if($clase == 'fila2'){
					$clase = 'fila1';
				} else {
					$clase = 'fila2';
				}

				$consulta = $consulta."<td align=center>".$rs['Habcod']."</td>";
				$consulta = $consulta."<td align=center>".$rs['Habhis']."-".$rs['Habing']."</td>";
				$consulta = $consulta."<td>".$rs['nombre']."</td>";
				$consulta = $consulta."<td><a href='javascript:irAKardex(\"{$rs['Habhis']}\");'>Ir a kardex</a></td>";
				$consulta = $consulta."</tr>";
			}
			$cont1++;
		}
	} else {
		$consulta = $consulta."<tr><td colspan=3 class=encabezadoTabla>No se encontraron pacientes</td></tr>";
	}
	$consulta = $consulta."</table>";

//	liberarConexionBD($conexion);

	return $consulta;
}

function consultarUsuarioOrdenes($codigo)
{
	global $conex;
	// global $wbasedato;
	global $esquemaBDHce;
	global $codigoAplicacion;

	global $centroCostosServicioFarmaceutico;
	global $centroCostosCentralMezclas;

	$q = "SELECT
				Codigo,Password,Passdel,Feccap,Tablas,Descripcion,Prioridad,Grupo,Empresa,Cc,Cconom,Ccohos,Ccogka,Ccopek,Ccouct,Ccolac,Ccocir,Ccoing,Ccourg   
			FROM 
				usuarios, root_000025 LEFT JOIN mhosidc_000011 ON Cc = Ccocod 
			WHERE
				Codigo = '".$codigo."'
				AND Empleado = Codigo
				AND Activo = 'A'";
	
	global $wemp_pmla;
	// $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
				
	$q = "SELECT
				Codigo,Password,Passdel,Feccap,Tablas,Descripcion,Prioridad,Grupo,Empresa,Cc,Cconom,Ccohos,Ccogka,Ccopek,Ccouct,Ccolac,Ccocir,Ccoing,Ccourg   
			FROM 
				usuarios, root_000025, mhosidc_000011
			WHERE
				Codigo = '".$codigo."'
				AND Empleado = Codigo
				AND Activo = 'A'
				AND Cc = Ccocod ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$consulta = new UsuarioOrden();

	$cont1 = 0;
	if($num > 0)
	{
		
		$rs = mysql_fetch_array($res);

		$consulta->codigo = $rs['Codigo'];
		$consulta->descripcion = $rs['Descripcion'];
		$consulta->empresa = $rs['Empresa'];
		$consulta->centroCostos = $rs['Cc'];

		//Nombre del centro de costos
		if(isset($rs['Cconom']) && !empty($rs['Cconom'])){
			$consulta->nombreCentroCostos = $rs['Cconom'];
		} else {
			$consulta->nombreCentroCostos = "";
		}

		//Es hospitalario o no
		if(isset($rs['Ccohos']) && !empty($rs['Ccohos']) && $rs['Ccohos'] == 'on'){
			$consulta->centroCostosHospitalario = true;
			$consulta->centroCostosGrabacion = "*";
		} else {
			$consulta->centroCostosHospitalario = false;
			$consulta->centroCostosGrabacion = $consulta->centroCostos;
		}

		//Pestañas
		if(isset($rs['Ccopek']) && !empty($rs['Ccopek']) && $rs['Ccopek'] != 'NO APLICA'){
			$consulta->pestanasKardex = $rs['Ccopek'];
		} else {
			$consulta->pestanasKardex = "";
		}

		//Grupos medicamentos
		if(isset($rs['Ccogka']) && !empty($rs['Ccogka']) && $rs['Ccogka'] != 'NO APLICA'){
			$consulta->gruposMedicamentos = $rs['Ccogka'];
		} else {
			$consulta->gruposMedicamentos = "*";
		}
		
		if( isset($rs['Ccocir']) && $rs['Ccocir'] == 'on' ){
			$consulta->esCcoCirugia = true;
		}
		
		if( isset($rs['Ccoing']) && $rs['Ccoing'] == 'on' ){
			$consulta->esCcoIngreso = true;
		}
		
		if( isset($rs['Ccourg']) && $rs['Ccourg'] == 'on' ){
			$consulta->esCcoUrgencias = true;
		}

		//Grupos de medicamentos formateados para ser usados en queries y clausulas tipo IN Ej.  Campo IN ('LTR','LTQ')
		if(isset($rs['Ccogka']) && !empty($rs['Ccogka']) && $rs['Ccogka'] != 'NO APLICA'){
			$gruposIncluidos = "(";
				
			if($rs['Ccogka'] != "*"){
				$vecGrupos = explode(",",$rs['Ccogka']);
				$cuenta = count($vecGrupos);
				$cont1 = 0;
				foreach ($vecGrupos as $grupo){
					$gruposIncluidos .= "'".str_replace(",","','",$grupo)."'";
					if($cont1 < $cuenta-1){
						$gruposIncluidos .= ",";
					}
					$cont1++;
				}
			}
			$gruposIncluidos .= ")";
			$consulta->gruposMedicamentosQuery = $gruposIncluidos;
		} else {
			$consulta->gruposMedicamentosQuery = "('')";
		}

		//Usuario es de central de mezclas
		$consulta->esUsuarioSF = ($consulta->centroCostos == $centroCostosServicioFarmaceutico) ? true : false;

		//Usuario es de servicio farmaceutico
		$consulta->esUsuarioCM = ($consulta->centroCostos == $centroCostosCentralMezclas) ? true : false;

		//Usuario tiene permisos de modificar ctc
		if (strpos($rs['Ccouct'], $consulta->codigo) !== false) {
			$consulta->esUsuarioCTC = true;
		} else {
			$consulta->esUsuarioCTC = false;
		}

		//Usuario es de lactario
		if(isset($rs['Ccolac']) && !empty($rs['Ccolac']) && $rs['Ccolac'] == 'on'){
			$consulta->esUsuarioLactario = true;
		} else {
			$consulta->esUsuarioLactario = false;
		}

		//Especialidad del usuario
		if(isset($consulta->codigo) && !empty($consulta->codigo)){
			$consulta->codigoEspecialidad = consultarEspecialidadUsuario($consulta->codigo);
		} else {
			$consulta->codigoEspecialidad = "";
		}
		
		
		//***CONSULTAS DEL ESQUEMA DE HCE
		//Rolcod  Roldes  Rolatr  Rolemp  Rolest
		$q2 = "SELECT
				Usucla,Usurol,Roldes,Rolemp, Rolenf, Rolmed
			FROM 
			{$esquemaBDHce}_000020,{$esquemaBDHce}_000019
			WHERE
				Usucod = '".$consulta->codigo."'
				AND Rolcod = Usurol
				AND Usuest = 'on'
				AND Rolest = Usuest
				AND NOW() < Usufve";

		$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
		$num2 = mysql_num_rows($res2);

		if($num2 > 0)
		{
			$rs2 = mysql_fetch_array($res2);
				
			$consulta->codigoRolHCE = $rs2['Usurol'];
			$consulta->nombreRolHCE = $rs2['Roldes'];
			$consulta->esEnfermeraRolHCE = $rs2['Rolenf'];
			$consulta->esMedicoRolHCE = $rs2['Rolmed'];
				
			if($rs2['Rolemp'] != '' && $rs2['Rolemp'] != 'NO APLICA'){
				$consulta->codigoEmpresaAgrupada = $rs2['Rolemp'];
			} else {
				$consulta->codigoEmpresaAgrupada = "*";
			}
				
			//Pronom  Proest
			//Rrprol  Rrppro  Rrpopc  Rrpgra  Rrpest
			//Oprpro  Oprnop  Oprdop  Oprest
			$q3 = "SELECT
				Rrprol,Rrpnpe,Pronom,(Rrpopc+0) as Rrpopc,Rrpgra,Oprdop,Oprmod
			FROM 
			{$esquemaBDHce}_000026,{$esquemaBDHce}_000023,{$esquemaBDHce}_000024
			WHERE
				Pronom = '".$codigoAplicacion."' 
				AND Rrprol = '$consulta->codigoRolHCE'
				AND Proest = 'on'
				AND Rrpest = Proest
				AND Oprest = Proest
				AND Rrppro = Pronom
				AND Oprpro = Pronom
				AND Oprnop = Rrpopc
				AND Oprnop != 10
				AND Oprnop != 11
			ORDER BY
				Rrpopc asc";

			$res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
			$num3 = mysql_num_rows($res3);

			//			Pestañas ordenes HCE.  Nomenclatura:  CODIGO|NOMBRE|GRABA(on/off)
			while($rs3 = mysql_fetch_array($res3)){
				// 2012-08-03
				// Se cambió $rs3['Rrpnpe'] por $rs3['Oprdop'] ya que se necesita que el nombre de las pestañas 
				// esté definido por la tabla hceidc_000024 y no por hceidc_000026
				$consulta->pestanasHCE .= $rs3['Rrpopc']."|".$rs3['Oprdop']."|".$rs3['Rrpgra'].";";
				$consulta->permisosPestanas[ $rs3['Rrpopc'] ]['modifica'] = ( $rs3['Oprmod'] == "on" ) ? true: false;	//Indica si el medico puede modificar
			}
				
			//Empresas agrupadas
			if($consulta->codigoEmpresaAgrupada != "*"){
				
				$q4 = "SELECT
							Empdes,Empemp 
						FROM 
							{$esquemaBDHce}_000025
						WHERE
							Empcod = '$consulta->codigoEmpresaAgrupada'
							AND Empest = 'on'";

				$res4 = mysql_query($q4, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q4 . " - " . mysql_error());
				$num4 = mysql_num_rows($res4);
					
				//Pestañas ordenes HCE.  Nomenclatura:  NITS SEPARADOS POR COMA
				while($rs4 = mysql_fetch_array($res4)){
					$consulta->empresasAgrupadas = $rs4['Empemp'];
					$consulta->nombreEmpresaAgrupada = $rs4['Empdes'];
				}
			} else {
				$consulta->empresasAgrupadas = "*";
				$consulta->nombreEmpresaAgrupada = "TODAS LAS EMPRESAS";
			}

			//Consulta de posibilidad de firmar si el rol lo permite
			$q4 = "SELECT
						Prorol,Proprg,Profir  
					FROM 
						{$esquemaBDHce}_000030
					WHERE
						Proprg = '".$codigoAplicacion."' 
						AND Prorol = '$consulta->codigoRolHCE'";

			$res4 = mysql_query($q4, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q4 . " - " . mysql_error());
			$num4 = mysql_num_rows($res4);

			//Pestañas ordenes HCE.  Nomenclatura:  CODIGO|NOMBRE|GRABA(on/off)
			$consulta->firmaElectronicamente = false;
			if($rs4 = mysql_fetch_array($res4)){
				$consulta->firmaElectronicamente = $rs4['Profir'] == "on" ? true : false;
			}
		}
	}
	return $consulta;
}



/*
 * AJAX::ConsultarArticulosPorNombre
 */
function consultarArticulos($wbasedato,$criterio,$ccoPaciente){
	//Variable que se necesitan
	$centroCostos = "1183";
	$criterio = strtoupper($criterio);
	
	global $conex;

	$coleccion = array();
	$consulta = "";

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;
	global $wemp_pmla;
	global $wcenpro;
	
	global $protocoloNormal;

	$esSF = $centroCostos == $centroCostosServicioFarmaceutico ? true : false;
	$esCM = $centroCostos == $centroCostosCentralMezclas ? true : false;

	@$codigo = str_replace("-","%",$codigo);

	$observacionesDA = "";
	
	$tipoArticulo = "";
	$tipoGenerico = "";
	$parametrosFijosArticuloGenerico = array();
	
	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	$gruposIncluidos = "(";

	$q6 = "SELECT DISTINCT Ccogka FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccogka != '*' AND Ccocod='$centroCostos';";
	$res6 = mysql_query($q6, $conex);

	while($rs6 = mysql_fetch_array($res6)){
		$tieneGruposIncluidos = true;
		if(strpos($rs6['Ccogka'],$gruposIncluidos) === false){
			$gruposIncluidos .= "'".str_replace(",","','",$rs6['Ccogka'])."',";
		}
	}
	$gruposIncluidos .= "'')";
	//********************************

	//Preproceso de los grupos de medicamentos.  De formato X00,Y00,Z00... a 'X00','Y00','Z00'
	$criterioGrupo = "";
	@$vecGruposMedicamentos = explode(",",$gruposMedicamentos);

	$cont2 = 0;
	while($cont2 < count($vecGruposMedicamentos)){
		$criterioGrupo .= "'".$vecGruposMedicamentos[$cont2]."',";
		$cont2++;
	}
	$criterioGrupo .= "''";

	/*La consulta de medicamentos o articulos tiene los siguientes elementos:
	 * -Criterios aplicados:  Codigo, Nombre generico, Nombre comercial
	 * -Tipo protocolo
	 * 
	 */
	
	//Con el fin de obtener los genericos se tendra en cuenta unicamente la primera palabra de la consulta antes del espacio
	$vecCriterio = explode("%",$criterio);
	$criterioCM = $vecCriterio[0];
	
	//Por codigo
	$qSfCod = "SELECT "
	." 		Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip , Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
	." FROM "
	." 		{$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
	." WHERE "
	."		artuni = unicod "
	."		AND artcod LIKE '%".$criterio."%' "
	."		AND artcod = Defart "
	."		AND artest = 'on' "
	."		AND Defest = 'on' ";

	$qCmCod = "SELECT "
	."	Artcod, Artcom, Artgen, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Arttip , Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
	." FROM "
	." {$wbasedato}_000027, {$wcenpro}_000002, {$wbasedato}_000059 "
	." WHERE "
	."  artuni = unicod "
	."	AND artcod LIKE '%".$criterioCM."%' "
	."	AND artcod = Defart "
	."	AND artest = 'on' "
	."	AND Defest = 'on' ";

	//Por nombre generico
	$qSfGen = "SELECT "
	." 		Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
	." FROM "
	." 		{$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
	." WHERE "
	."		artuni = unicod "
	."		AND Artgen LIKE '%".$criterio."%' "
	."		AND artcod = Defart "
	."		AND artest = 'on' "
	."		AND Defest = 'on' ";

	$qCmGen = "SELECT "
	."	Artcod, Artcom, Artgen, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Arttip , Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
	." FROM "
	." {$wbasedato}_000027, {$wcenpro}_000002, {$wbasedato}_000059 "
	." WHERE "
	."  artuni = unicod "
	."	AND Artgen LIKE '%".$criterioCM."%' "
	."	AND artcod = Defart "
	."	AND artest = 'on' "
	."	AND Defest = 'on' ";
	
	//Por nombre comercial
	$qSfCom = "SELECT "
	." 		Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip , Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
	." FROM "
	." 		{$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
	." WHERE "
	."		artuni = unicod "
	."		AND Artcom LIKE '%".$criterio."%' "
	."		AND artcod = Defart "
	."		AND artest = 'on' "
	."		AND Defest = 'on' ";

	$qCmCom = "SELECT "
	."	Artcod, Artcom, Artgen, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Arttip, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
	." FROM "
	." {$wbasedato}_000027, {$wcenpro}_000002, {$wbasedato}_000059 "
	." WHERE "
	."  artuni = unicod "
	."	AND Artcom LIKE '%".$criterioCM."%' "
	."	AND artcod = Defart "
	."	AND artest = 'on' "
	."	AND Defest = 'on' ";
	
	$q = $qSfCod;
	$q.= " UNION ".$qCmCod;
	$q.= " UNION ".$qSfGen;
	$q.= " UNION ".$qCmGen;
	$q.= " UNION ".$qSfCom;
	$q.= " UNION ".$qCmCom;
	
	//Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
//	if($esCM){
//		$q = $q." UNION ".$subConsulta;
//	} else {
//		if(!$tieneGruposIncluidos){
//			$q = $q." UNION ".$subConsulta;
//		}
//	}
	$q = $q." LIMIT 100";
	
//	echo $q;
	
//	$q = "SELECT "
//				." Artcod, Artcom, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
//			." FROM "
//				." {$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
//			." WHERE "
//			."	artuni = unicod "
//			."	AND artcod LIKE '%".$codigo."%' "
//			."	AND Artuni LIKE '$unidadMedida' "
//			."	AND artcod = Defart "
//			."	AND artest = 'on' "
//			."	AND Defest = 'on' ";
//				if($tieneGruposIncluidos){
//					$q .= " AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN $gruposIncluidos ";
//				}
//				$q .= " AND Defcco = '$centroCostosServicioFarmaceutico' ";
//			if($gruposMedicamentos != "*"){
//				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M' AND Melgru IN ($criterioGrupo)) ";
//			} else {
//				$q = $q."AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM {$wbasedato}_000066 WHERE Melest = 'on' AND Meltip = 'M') ";
//			}
//
//			$subConsulta = "SELECT "
//							."	Artcod, Artcom, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia "
//							." FROM "
//								." {$wbasedato}_000027, cenpro_000002, {$wbasedato}_000059 "
//							." WHERE "
//							."  artuni = unicod "
//							."	AND artcod LIKE '%".$codigo."%' "
//							."	AND Artuni LIKE '$unidadMedida' "
//							."	AND artcod = Defart "
//							."	AND artest = 'on' "
//							."	AND Defest = 'on' "
//							."	AND Defcco = '$centroCostosCentralMezclas'";
//
//			 //Si es usuario de central de mezclas SOLO se le permitirá ver lo de la central
//			if($esCM){
//				$q = $q." UNION ".$subConsulta;
//			} else {
//				if(!$tieneGruposIncluidos){
//					$q = $q." UNION ".$subConsulta;
//				}
//			}
//			$q = $q." LIMIT 100";
//			break;
//	echo $q;
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	$agrupoGenericos = false;
	$abreVentanaModal = "";
	
	if($num > 0){
		$codigoGenericoArticulo = "";
		
		while ($cont1 < $num)
		{
			$tieneParametrosFijos = "N";
			$nombreArticuloGenerico = "";
			$puedeAgregar = true;
			
			$rs = mysql_fetch_array($res);
			
			$tipoProtocolo = $protocoloNormal;
			$tipoArticuloMedicamentoLiquido = "M";
			$tipoGenerico = "";
			$tipoArticulo = "1";
			$borrar = "";
			
			//Consulta del tipo al que pertenece
			$qTipLM = "SELECT Meltip FROM {$wbasedato}_000066 WHERE Melgru = '{$rs['Artgru']}' AND Melest='on' ";
			$resTipLM = mysql_query($qTipLM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipLM . " - " . mysql_error());
			if($infoTipLM = mysql_fetch_array($resTipLM)){
				$tipoArticuloMedicamentoLiquido = $infoTipLM['Meltip'];
			}
			
			//Consulta del protocolo al que pertenece el articulo
			$qProt = "SELECT Arkcod,Arkest,Arkcco,Arktip FROM {$wbasedato}_000068 WHERE Arkcod = '{$rs['Artcod']}'";
			$resProt = mysql_query($qProt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qProt . " - " . mysql_error());
			if($infoProt = mysql_fetch_array($resProt)){
				$tipoProtocolo = $infoProt['Arktip'];
			}
			
			$color = "red";
			$fftica = $rs['Artfar'];

			//CLASIFICACION DE LOS ARTICULOS
			
			/*TIPOS DE ARTICULOS
			 * 1.  Articulos del maestro de SF
			 * 2.  Articulos del maestro de CM
			 * 3.  Articulos genericos NU,QT,DA dependiendo del tipo en la tabla 68 y la 2
			 * 4.  
			 */
			if($rs['origen'] == $codigoServicioFarmaceutico){ 		//No tiene genéricos
				$tipoGenerico = "";
			}
			
			//On
			/*
					echo "Tipo gen: ".$qTipGen."<br>";
					echo "rs: ".$rs['origen']."<br>";
					echo "central M : ".$codigoCentralMezclas."<br>";
					echo "tipo: ".$rs['Arttip']."<br>";
					*/
			
			if($rs['origen'] == $codigoCentralMezclas){  			//Puede tener genéricos
				//Consulta del tipo al que pertenece
				$tipoCentralMezclas = $rs['Arttip'];
				
				if(!empty($tipoCentralMezclas)){
					$qTipGen = "SELECT Arkcod, Tiptpr, Tipdes FROM {$wcenpro}_000001, mhosidc_000068 WHERE Tipcod = '{$tipoCentralMezclas}' AND Arktip = Tiptpr";
					$resTipGen = mysql_query($qTipGen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipGen . " - " . mysql_error());
					if($infoTipGen = mysql_fetch_array($resTipGen)){
						$tipoGenerico = $infoTipGen['Tiptpr'];
						$codigoGenericoArticulo = $infoTipGen['Arkcod'];
						$articuloGenerico = true;
					} else {
						$articuloGenerico = false;
					}
				} else {
					$qTipGen = "SELECT Arkcod, Arktip FROM mhosidc_000068, mhosidc_000098 WHERE Arkcod = '{$rs['Artcod']}' AND Cartip = Arktip";
					
					$resTipGen = mysql_query($qTipGen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipGen . " - " . mysql_error());
					if($infoTipGen = mysql_fetch_array($resTipGen)){
						$tipoGenerico = $infoTipGen['Arktip'];
						$codigoGenericoArticulo = $infoTipGen['Arkcod'];
						$articuloGenerico = true;
					} else {
						$articuloGenerico = false;
					}
				}
				
				if($articuloGenerico){
					//Partip  Parcdo  Parudo  Parnen  Parfre  Parvia  Parcon  Parcnf  Pardim  Pardom  Parobs
					$qParametros = "SELECT Parcdo,Parudo,Parnen,Parfre,Parvia,Parcon,Parcnf,Pardim,Pardom,Parobs FROM mhosidc_000097 WHERE Partip = '{$tipoGenerico}';";
					$resParametros = mysql_query($qParametros, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qParametros . " - " . mysql_error());
					if($infoParametros = mysql_fetch_array($resParametros)){
						$tieneParametrosFijos = "S";

						$parametrosFijosArticuloGenerico[0] = $infoParametros['Parcdo'];		//Cantidad de dosis
						$parametrosFijosArticuloGenerico[1] = $infoParametros['Parudo'];		//Unidad de dosis
						$parametrosFijosArticuloGenerico[2] = $infoParametros['Parnen'];		//No enviar
						$parametrosFijosArticuloGenerico[3] = $infoParametros['Parfre'];		//Frecuencia
						$parametrosFijosArticuloGenerico[4] = $infoParametros['Parvia'];		//Vias de administracion
						$parametrosFijosArticuloGenerico[5] = $infoParametros['Parcon'];		//Condicion de suministro
						$parametrosFijosArticuloGenerico[6] = $infoParametros['Parcnf'];		//Confirmada preparacion
						$parametrosFijosArticuloGenerico[7] = $infoParametros['Pardim'];		//Dias maximos tratamiento
						$parametrosFijosArticuloGenerico[8] = $infoParametros['Pardom'];		//Dosis maximas tratamiento
						$parametrosFijosArticuloGenerico[9] = $infoParametros['Parobs'];		//Observaciones
					}
				}
			}
			
			//Si tiene componentes asociados en la tabla de componentes por tipo, mostrará los tipos
			$qComp = "SELECT Cartip,Carcod,Carcco,Cardis FROM mhosidc_000098 WHERE Cartip = '{$tipoGenerico}';";
			$resComp = mysql_query($qComp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qComp . " - " . mysql_error());
			$componentesTipo = "";
			$tieneComponentes = false;
			$ccoSF = ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
			while($infoComp = mysql_fetch_array($resComp)){
				if($infoComp['Carcco'] == $ccoSF){
					$qArt = "SELECT Artcom,Artgen FROM mhosidc_000026 WHERE Artcod = '{$infoComp['Carcod']}';";
				} else {
					$qArt = "SELECT Artcom,Artgen FROM {$wcenpro}_000002 WHERE Artcod = '{$infoComp['Carcod']}';";
				}
				$resArt = mysql_query($qArt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qArt . " - " . mysql_error());
				if($infoArt = mysql_fetch_array($resArt)){
					$componentesTipo .= $infoComp['Carcod']."**";
					$componentesTipo .= $infoComp['Cardis']."**";
					$componentesTipo .= utf8_decode($infoArt['Artcom'])."**";
					$componentesTipo .= utf8_decode($infoArt['Artgen']).";";
				}
				$tieneComponentes = true;
			}
			
			if(str_replace("%"," ",$codigo)){
				$nombreArticuloPersonalizado = "S";
			}
			
			//Clasificacion por tipo
			if($tipoArticuloMedicamentoLiquido == "L"){
				$tipoArticulo = "2";
			}
			
			//Casos posibles de tipo de articulo
			switch($tipoArticulo){
				case '1':	//Articulo generico
					$borrar .= " generico";
 					break;
				case '2':	//Articulo liquido
					$borrar .= " liquido";
					break;
				default:	//Articulo normal
					$borrar .= " normal";
					break;
			}

			
			if(@$articuloGenerico){
				
				// 2012-07-25
				// Se modificaron las asignaciones a las variables para que tomen el nombre del articulo y no el criterio de busqueda
				// ya que e criterio en la nueva busqueda por amilia siempre va a ser un codig, no un nombre
				@$articuloCodigoConsulta = $rs['Artcod'];  // $codigoGenericoArticulo;
				@$articuloNombreGenerico = "*".$rs['Artgen'];  // "*".str_replace("%"," ",$rs['Artgen']);
				@$articuloNombreComercial = $rs['Artcom'];  // $criterioCM
				
				$nombreArticuloGenerico = $articuloNombreGenerico;
				
				if(!$agrupoGenericos){
					$agrupoGenericos = true;
					$puedeAgregar = true;
				} else {
					$puedeAgregar = false;
				}
			} else {
				$articuloCodigoConsulta = $rs['Artcod'];
				$articuloNombreGenerico = $rs['Artgen'];
				$articuloNombreComercial = $rs['Artcom'];
			}
			
			//Los articulos J deben ser pedidos genericos por central
//			if($rs['origen'] == $codigoCentralMezclas){
//				$color = "blue";
//				$fftica = '00';
//				if(!$esCM && !$esSF && substr(strtoupper($rs['Artcod']),0,1) == "J"){
//					$puedeAgregar = false;
//				}
//			}
				
			/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
			 * Argumentos:
			 * 
			 * 0: Como se muestra en el autocomplete
			 * 1: Codigo del articulo
			 * 2: Nombre comercial del articulo
			 * 3: Nombre genérico del articulo
			 * 4: Tipo protocolo
			 * 5: (M)edicamento o (L)iquido
			 * 6: Es generico
			 * 7: Origen
			 * 8: Grupo de medicamento
			 * 9: Forma farmaceutica
			 * 10:Unidad
			 * 11:POS
			 * 12:Unidad de fraccion
			 * 13:Cantidad de fracciones
			 * 14:Vencimiento
			 * 15:Dias de estabilidad
			 * 16:Es dispensable
			 * 17:Es duplicable
			 * 18:Dias maximos sugeridos
			 * 19:Dosis maximas sugeridas
			 * 20:Via
			 * 21:Abre ventana parametrizada ----
			 * 22:Cantidad de dosis
			 * 23:Unidad de dosis
			 * 24:No enviar
			 * 25:Frecuencia
			 * 26:Vias de administracion
			 * 27:Condicion de suministro
			 * 28:Confirmada preparacion
			 * 29:Dias maximos tratamiento
			 * 30:Dosis maximas tratamiento
			 * 31:Observaciones adicionales
			 * 32:Componentes asociados al tipo
			 * 33:Observaciones si es Dosis Adaptada
			 */
			if($puedeAgregar)
			{
				if($tieneComponentes){
					$abreVentanaModal = "S";
				} else {
					$abreVentanaModal = "N";
				}
				
				$noEnviar = ( esStock( $conex, $wbasedato, $rs['Artcod'], $ccoPaciente ) == true )? 'on' : 'off';
				
//				$referencia = "javascript:seleccionarMedicamento('".$rs['Artcod']."','".str_replace(" ","_",trim(htmlentities($rs[1])))."','".$rs['origen']."','".str_replace(" ","_",trim($rs['Artgru']))."','".str_replace(" ","_",trim($fftica))."','".str_replace(" ","_",trim($rs['Artuni']))."','".str_replace(" ","_",trim($rs['Artpos']))."','".str_replace(" ","_",trim($rs['Deffru']))."','".str_replace(" ","_",trim($rs['Deffra']))."','".str_replace(" ","_",trim($rs['Defven']))."','".str_replace(" ","_",trim($rs['Defdie']))."','".str_replace(" ","_",trim($rs['Defdis']))."','".str_replace(" ","_",trim($rs['Defdup']))."','".str_replace(" ","_",trim($rs['Defdim']))."','".str_replace(" ","_",trim($rs['Defdom']))."','".str_replace(" ","_",trim($rs['Defvia']))."','".$tipoProtocolo."');";
				$consulta .= "<font color=gray><b>Generico:</b></font> ".htmlentities($articuloNombreGenerico)." <font color=gray><b>Comercial:</b></font> ".htmlentities($articuloNombreComercial).
				"|".strtoupper($articuloCodigoConsulta).
				"|".htmlentities($articuloNombreGenerico).
				"|".htmlentities($articuloNombreComercial).
				"|".$tipoProtocolo.
				"|".$tipoArticuloMedicamentoLiquido.
				"|".$tipoGenerico.
				"|".$rs['origen'].
				"|".$rs['Artgru'].
				"|".$rs['Artfar'].
				"|".$rs['Artuni'].
				"|".$rs['Artpos'].
				"|".$rs['Deffru'].
				"|".$rs['Deffra'].
				"|".$rs['Defven'].
				"|".$rs['Defdie'].
				"|".$rs['Defdis'].
				"|".$rs['Defdup'].
				"|".$rs['Defdim'].
				"|".$rs['Defdom'].
				"|".$rs['Defvia'].
				"|".$abreVentanaModal.
				"|".@$parametrosFijosArticuloGenerico[0].
				"|".@$parametrosFijosArticuloGenerico[1].
				"|".@$parametrosFijosArticuloGenerico[2].
				"|".@$parametrosFijosArticuloGenerico[3].
				"|".@$parametrosFijosArticuloGenerico[4].
				"|".@$parametrosFijosArticuloGenerico[5].
				"|".@$parametrosFijosArticuloGenerico[6].
				"|".@$parametrosFijosArticuloGenerico[7].
				"|".@$parametrosFijosArticuloGenerico[8].
				"|".@$parametrosFijosArticuloGenerico[9].
				"|".@$componentesTipo.
				"|".@$noEnviar.
				"|".@$observacionesDA.
				"\n";
			}
			
			$cont1++;
		}
	} else {
		$consulta = $consulta."No se encontraron coincidencias";
	}

	liberarConexionBD($conex);

	return $consulta;
}


/*
 * AJAX::ConsultarArticulosPorNombre
 */
function prepararDivisor( $numero )
{
	if($numero==0)
		$numero = 1;
	else
		$numero = $numero*1;
	
	return $numero;
}	

/*
 * AJAX::ConsultarArticulosPorNombre
 */
function consultarArticulosFamilia( $wbasedato, $wcenmez, $criterio, $ccoPaciente, $presentacion, $medida, $dosis, $administracion ){
	//Variable que se necesitan
	$centroCostos = "1183";
	$criterio = strtoupper($criterio);
	
	global $conex;

	$coleccion = array();
	$consulta = "";

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;
	global $wemp_pmla;
	global $wcenpro;
	
	global $protocoloNormal;

	$esSF = $centroCostos == $centroCostosServicioFarmaceutico ? true : false;
	$esCM = $centroCostos == $centroCostosCentralMezclas ? true : false;

	$dosis_exacta = true;
	$articulo_pos = true;
	
	@$codigo = str_replace("-","%",$codigo);

	$tipoArticulo = "";
	$tipoGenerico = "";

	$observacionesDA = "";

	// Variables para guardar el último artículo que mas se acerca a los criterios de búsqueda
	$articulo_encontrado = "";
	$articulo_pos_encontrado = "";
	$unidadesRequeridas_encontrado = -1;
	$dosisCubiertas_encontrado = -1;
	$dosis_exacta_encontrado = false;
	
	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	$gruposIncluidos = "(";

	$q6 = "SELECT DISTINCT Ccogka FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccogka != '*' AND Ccocod='$centroCostos';";
	$res6 = mysql_query($q6, $conex);

	// Define los grupos de medicamentos que puede ver el centro de costos en el kardex
	while($rs6 = mysql_fetch_array($res6))
	{
		$tieneGruposIncluidos = true;
		if(strpos($rs6['Ccogka'],$gruposIncluidos) === false)
		{
			$gruposIncluidos .= "'".str_replace(",","','",$rs6['Ccogka'])."',";
		}
	}
	$gruposIncluidos .= "'')";
	//********************************

	//Preproceso de los grupos de medicamentos.  De formato X00,Y00,Z00... a 'X00','Y00','Z00'
	$criterioGrupo = "";
	@$vecGruposMedicamentos = explode(",",$gruposMedicamentos);

	$cont2 = 0;
	while($cont2 < count($vecGruposMedicamentos))
	{
		$criterioGrupo .= "'".$vecGruposMedicamentos[$cont2]."',";
		$cont2++;
	}
	$criterioGrupo .= "''";

	/*
	 // Se consultan los medicamentos que cumplan con los criterios seleccionados
	 $sql = "( SELECT
				Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, 0 Defmin, 10000 Defmax, Artonc
			FROM
				{$wbasedato}_000114 a, 
				{$wbasedato}_000027 b,
				{$wbasedato}_000115 c,
				{$wbasedato}_000026 d,
				{$wbasedato}_000059 e,
				{$wbasedato}_000046 f
			WHERE
				Famnom  LIKE '%$criterio%'
				AND Famcod = Relfam
				AND Reluni LIKE '$medida'
				AND Relpre LIKE '$presentacion'
				AND Relcon = '$dosis'
				AND Reluni = Unicod
				AND Relpre = Ffacod
				AND Relest = 'on'
				AND Artcod = Relart
				AND Artest = 'on'
				AND Artcod = Defart 
				AND Defest = 'on' )
			";
	

	// ORDER BY
		// Artpos DESC, Relfam asc, Reluni asc, Relcon asc, Relart asc

	 // Se consultan los medicamentos que cumplan con los criterios seleccionados, excepto la dosis
	 $sql2 = "( SELECT
				Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, 0 Defmin, 10000 Defmax, Artonc
			FROM
				{$wbasedato}_000114 a, 
				{$wbasedato}_000027 b,
				{$wbasedato}_000115 c,
				{$wbasedato}_000026 d,
				{$wbasedato}_000059 e,
				{$wbasedato}_000046 f
			WHERE
				Famnom  LIKE '%$criterio%'
				AND Famcod = Relfam
				AND Reluni LIKE '$medida'
				AND Relpre LIKE '$presentacion'
				AND Reluni = Unicod
				AND Relpre = Ffacod
				AND Relest = 'on'
				AND Artcod = Relart
				AND Artest = 'on'
				AND Artcod = Defart 
				AND Defest = 'on' )

			";
	
	// 2012-07-09
	// Se agregó ORDER BY Famund DESC para poder ordenar según la unidad destacada para la familia de medicamentos
	*/
	
	
	/*
	----------- DESCRIPCION DE LAS TABLAS PARA LA SIGUIENTE CONSULTA ---------------
	{$wbasedato}_000026 -> Maestro de artículos (Art)
	{$wbasedato}_000027 -> Maestro de unidades (Uni)
	{$wbasedato}_000040 -> Vías de administración (Ffa)
	{$wbasedato}_000046 -> Formas farmacéuticas (Ffa)
	{$wbasedato}_000059 -> Definición fracciones artículos (Def)
	---------------------------------------------------------------------------------
	*/
		
	
	 // Se consultan los medicamentos que cumplan con los criterios seleccionados
	 $sql = "SELECT
				Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, 0 Defmin, 10000 Defmax, Artonc, Artint
			FROM
				{$wbasedato}_000026 a,
				{$wbasedato}_000027 b,
				{$wbasedato}_000046 c,
				{$wbasedato}_000059 d
			WHERE
					Artgen LIKE '%$criterio%' "
		//	."	(Artgen LIKE '%$criterio%' OR Artcom LIKE '%$criterio%') "
			."	AND Artuni LIKE '$medida'
				AND Artfar LIKE '$presentacion'
				AND Deffra = '$dosis'
				AND Artuni = Unicod
				AND Artfar = Ffacod
				AND Artest = 'on'
				AND Artcod = Defart 
				AND Defest = 'on'
			";
	

	// ORDER BY
		// Artpos DESC, Relfam asc, Reluni asc, Relcon asc, Relart asc

	 // Se consultan los medicamentos que cumplan con los criterios seleccionados, excepto la dosis
	 // $sql2 = "SELECT
				// Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, 0 Defmin, 10000 Defmax, Artonc, Artint
			// FROM
				// {$wbasedato}_000026 a,
				// {$wbasedato}_000027 b,
				// {$wbasedato}_000046 c,
				// {$wbasedato}_000059 d
			// WHERE
					// Artgen LIKE '%$criterio%' "
		// //	."	(Artgen LIKE '%$criterio%' OR Artcom LIKE '%$criterio%') "
			// ."	AND Artuni LIKE '$medida'
				// AND Artfar LIKE '$presentacion'
				// AND Artuni = Unicod
				// AND Artfar = Ffacod
				// AND Artest = 'on'
				// AND Artcod = Defart 
				// AND Defest = 'on' 

			// ";
			
	// Se consultan los medicamentos que cumplan con los criterios seleccionados, excepto la dosis
	 $sql2 = "SELECT
				Artcod, Artcom, Artgen, Deffru as Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, 0 Defmin, 10000 Defmax, Artonc, Artint
			FROM
				{$wbasedato}_000026 a,
				{$wbasedato}_000027 b,
				{$wbasedato}_000046 c,
				{$wbasedato}_000059 d
			WHERE
					Artgen LIKE '%$criterio%' "
		//	."	(Artgen LIKE '%$criterio%' OR Artcom LIKE '%$criterio%') "
			."	AND Deffru LIKE '$medida'
				AND Artfar LIKE '$presentacion'
				AND Deffru = Unicod
				AND Artfar = Ffacod
				AND Artest = 'on'
				AND Artcod = Defart 
				AND Defest = 'on' 

			";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	// Declaro valor inicial para el código del articulo que se busca
	// Se pone -1 para que no arroje resgistros si no se encuentra en el query final 
	$articulo_encontrado = -1;
	
	// Inicializó el array que va a contener los datos de los artículos candidatos a ser seleccionados
	// $articulosEncontrados = array();
	
	// Declaro valor inicial para las variables que me dirá que artículo se acerca mas a la dosis pedida
	$auxUnidadesRequeridas = -1;
	$auxDosisCubiertas = -1;
	$retornar = "";
	// Si no se encontró articulos con dosis exacta, se pasa a consultar sin dosis ($sql2)
	if($num == 0)
	{
		$res = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
		$num = mysql_num_rows( $res );
		// Se desactiva el indicador de dosis exacta
		$dosis_exacta = false;
	}

	// Contador para los ciclos del while
	$cont = 0;

	// Si hay un articulo o más
	if($num > 0)
	{
		while($rs = mysql_fetch_array($res))
		{
			// Se asigna la cantidad de fracciones del artículo
			$fraccionArticulo = $rs['Deffra'];
			
			// Si la dosis no es exacta se definen variables para uso en la definición
			// del artíclo con la dosis mas adecuada
			if(!$dosis_exacta)
			{
				// Se define si las fracciones son múltiplo de la dosis
				$multiploDosis = ($dosis*1)%prepararDivisor($fraccionArticulo);
				$multiploFraccion = ($fraccionArticulo*1)%prepararDivisor($dosis);
				
				// Se define las unidades requerida para cubir la dosis y
				// las dosis cubiertas por la fracción del artículo
				$unidadesRequeridas = ($dosis*1)/prepararDivisor($fraccionArticulo);
				$dosisCubiertas = ($fraccionArticulo*1)/prepararDivisor($dosis);
				
			}
		
			// Si encontro una sola conincidencia
			if($num == 1)
			{
				// Si se encuentra un solo registro y es dosis exacta no es necesario hacer evaluación de fracciones 
				// y este se lleva como resultado de la búsqueda
				
				// LAS SIGUIENTES LINEAS SE COMENTAN PORQUE NO APLICAN PARA EL INSTITUTO DE CANCEROLOGIA
				/* if($dosis_exacta)
				{	*/
					$articulo_encontrado = $rs['Artcod'];
					break;
				/*
				}
				else
				{
					$prefijoArticulo = substr($rs['Artcod'], 0, 2);
					if($prefijoArticulo=="DA" || $prefijoArticulo=="LQ")
					{
						$articulo_encontrado = $prefijoArticulo."0000";
						break;
					}
					
					// Si la dosis es múltiplo de las fracciones o viceversa
					if($multiploDosis==0 || $multiploFraccion==0)
					{
						// Si las unidades a pedir estan en el rango de máximo y mínimo
						if(($dosis*1)>=($rs['Defmin']*1) && ($dosis)*1<=($rs['Defmax']*1))
						{
							// Si es primera vez que pasa o las unidades requeridas son menores a las anteriormente guardadas
							if($auxUnidadesRequeridas == -1 || $unidadesRequeridas < $auxUnidadesRequeridas)
							{
								$auxUnidadesRequeridas = $unidadesRequeridas;
								$articulo_encontrado = $rs['Artcod'];
							}
						} 
						// Si la fracción del artículo es superior a la dosis
						else if($dosisCubiertas>1)
						{
							// Si es primera vez que pasa o las unidades requeridas son menores a las anteriormente guardadas
							if($auxDosisCubiertas == -1 || $dosisCubiertas < $auxDosisCubiertas)
							{
								$auxDosisCubiertas = $dosisCubiertas;
								$articulo_encontrado = $rs['Artcod'];
							}
						}
					}
				}
				*/				
			}
			// Si se encontraron varias conincidencias
			elseif($num > 1)
			{
				// comienza la evaluación de selección cuando son varios artículos los que coinciden

				if($rs['Artpos']!='P')
					$articulo_pos = false;

				// Si es dosis exacta y es POS no es necesario hacer evaluación de fracciones 
				// y este se lleva como resultado de la búsqueda, se finaliza el ciclo
				
				if($dosis_exacta && $articulo_pos)
				{
					$articulo_encontrado = $rs['Artcod'];
					// return $articulo_encontrado;
					break;
				}
				// Si es dosis exacta y es articulo NO POS
				// Se toma como articulo encontrado pero se sigue buscando si hay uno POS
				elseif($dosis_exacta && !$articulo_pos)
				{
					$articulo_encontrado = $rs['Artcod'];
					$articulo_pos_encontrado = $rs['Artpos'];
					$unidadesRequeridas_encontrado = -1;
					$dosisCubiertas_encontrado = -1;
					$dosis_exacta_encontrado = true;
				}
				// Si no es dosis exacta asi sea POS o NO POS
				else
				{
					// Si hasta ahora no se ha encontrado dosis exacta
					if(!$dosis_exacta_encontrado)
					{
						if($articulo_pos_encontrado!='P')
						{
							// Si la dosis es múltiplo de las fracciones o viceversa
							// LAS SIGUIENTES LINEAS SE COMENTAN PORQUE NO APLICAN PARA EL INSTITUTO DE CANCEROLOGIA
							/* if($multiploDosis==0 || $multiploFraccion==0)
							{	*/
								// Si las unidades a pedir estan en el rango de máximo y mínimo
								if(($dosis*1)>=($rs['Defmin']*1) && ($dosis)*1<=($rs['Defmax']*1))
								{
									// Si es primera vez que pasa o las unidades requeridas son menores a las anteriormente guardadas
									if($auxUnidadesRequeridas == -1 || $unidadesRequeridas < $auxUnidadesRequeridas)
									{
										$auxUnidadesRequeridas = $unidadesRequeridas;
										$articulo_encontrado = $rs['Artcod'];
										$articulo_pos_encontrado = $rs['Artpos'];
										$unidadesRequeridas_encontrado = $unidadesRequeridas;
										$dosisCubiertas_encontrado = $dosisCubiertas;
										$dosis_exacta_encontrado = true;
									}
								} 
								// Si la fracción del artículo es superior a la dosis
								else if($dosisCubiertas>1)
								{
									// Si es primera vez que pasa o las unidades requeridas son menores a las anteriormente guardadas
									if($auxDosisCubiertas == -1 || $dosisCubiertas < $auxDosisCubiertas)
									{
										$auxDosisCubiertas = $dosisCubiertas;
										$articulo_encontrado = $rs['Artcod'];
										$articulo_pos_encontrado = $rs['Artpos'];
										$unidadesRequeridas_encontrado = $unidadesRequeridas;
										$dosisCubiertas_encontrado = $dosisCubiertas;
										$dosis_exacta_encontrado = true;
									}
								}
							/* } */
						}
					}
				}	
			}
		}

		if($articulo_encontrado==-1)
		{
			// Si no se encontro artículo que cumpla con la dosis
			// Se crea una dosis Adaptada
			$articulo_encontrado = "DA0000";
			$observacionesDA = $criterio;
		}
						
		//Con el fin de obtener los genericos se tendra en cuenta unicamente la primera palabra de la consulta antes del espacio
		//$vecCriterio = explode("%",$articulo_encontrado);
		//$articulo_encontradoCM = $vecCriterio[0];
		
		// Se obtienen los datos del articulo encontrado
		$qCod = " ( SELECT "
		." 		Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip , Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, Artonc "
		." FROM "
		." 		{$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
		." WHERE "
		."			Artcod = '".$articulo_encontrado."' "
		."		AND Artest = 'on' "
		."		AND Artuni = Unicod "
		."		AND Artcod = Defart "
		."		AND Defest = 'on' ) ";

		/*
		$qCod .= " UNION ";
		
		$qCod .= " ( SELECT "
		."	Artcod, Artcom, Artgen, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Arttip , Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, Artonc "
		." FROM "
		." {$wbasedato}_000027, cenpro_000002, {$wbasedato}_000059 "
		." WHERE "
		."		Artcod = '".$articulo_encontrado."' "
		."	AND Artest = 'on' "
		."  AND Artuni = Unicod "
		."	AND Artcod = Defart "
		."	AND Defest = 'on' ) ";
		*/

		$resart = mysql_query( $qCod, $conex ) or die( mysql_errno()." - Error en el query $qCod - ".mysql_error() );
		$numart = mysql_num_rows( $resart );
		
	}
	else
	{
		$numart = 0;
	}

	$cont1 = 0;
	$agrupoGenericos = false;
	$abreVentanaModal = "";
	
	if($numart > 0)
	{

			$codigoGenericoArticulo = "";


			
			$tieneParametrosFijos = "N";
			$nombreArticuloGenerico = "";
			$puedeAgregar = true;
			
			$rs = mysql_fetch_array($resart);
			
			$tipoProtocolo = $protocoloNormal;
			$tipoArticuloMedicamentoLiquido = "M";
			$tipoGenerico = "";
			$tipoArticulo = "1";
			$borrar = "";
			
			//Consulta del tipo al que pertenece
			$qTipLM = "SELECT Meltip FROM {$wbasedato}_000066 WHERE Melgru = '{$rs['Artgru']}' AND Melest='on' ";
			$resTipLM = mysql_query($qTipLM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipLM . " - " . mysql_error());
			if($infoTipLM = mysql_fetch_array($resTipLM))
			{
				$tipoArticuloMedicamentoLiquido = $infoTipLM['Meltip'];
			}
			
			//Consulta del protocolo al que pertenece el articulo
			$qProt = "SELECT Arkcod,Arkest,Arkcco,Arktip FROM {$wbasedato}_000068 WHERE Arkcod = '{$rs['Artcod']}'";
			$resProt = mysql_query($qProt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qProt . " - " . mysql_error());
			if($infoProt = mysql_fetch_array($resProt))
			{
				$tipoProtocolo = $infoProt['Arktip'];
			}
			
			$color = "red";
			$fftica = $rs['Artfar'];

			//CLASIFICACION DE LOS ARTICULOS
			
			/*TIPOS DE ARTICULOS
			 * 1.  Articulos del maestro de SF
			 * 2.  Articulos del maestro de CM
			 * 3.  Articulos genericos NU,QT,DA dependiendo del tipo en la tabla 68 y la 2
			 * 4.  
			 */
			if($rs['origen'] == $codigoServicioFarmaceutico){ 		//No tiene genéricos
				$tipoGenerico = "";
			}
			
			//On
			/*
			echo "Tipo gen: ".$qTipGen."<br>";
			echo "rs: ".$rs['origen']."<br>";
			echo "central M : ".$codigoCentralMezclas."<br>";
			echo "tipo: ".$rs['Arttip']."<br>";
			*/
	
			if($rs['origen'] == $codigoCentralMezclas)
			{  			//Puede tener genéricos
				//Consulta del tipo al que pertenece
				$tipoCentralMezclas = $rs['Arttip'];
				
				if(!empty($tipoCentralMezclas))
				{
					$qTipGen = "SELECT Arkcod, Tiptpr, Tipdes FROM {$wcenpro}_000001, mhosidc_000068 WHERE Tipcod = '{$tipoCentralMezclas}' AND Arktip = Tiptpr";
					$resTipGen = mysql_query($qTipGen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipGen . " - " . mysql_error());
					if($infoTipGen = mysql_fetch_array($resTipGen)){
						$tipoGenerico = $infoTipGen['Tiptpr'];
						$codigoGenericoArticulo = $infoTipGen['Arkcod'];
						$articuloGenerico = true;
					} else {
						$articuloGenerico = false;
					}
				} else {
					$qTipGen = "SELECT Arkcod, Arktip FROM mhosidc_000068, mhosidc_000098 WHERE Arkcod = '{$rs['Artcod']}' AND Cartip = Arktip";
					$resTipGen = mysql_query($qTipGen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipGen . " - " . mysql_error());
					if($infoTipGen = mysql_fetch_array($resTipGen)){
						$tipoGenerico = $infoTipGen['Arktip'];
						$codigoGenericoArticulo = $infoTipGen['Arkcod'];
						$articuloGenerico = true;
					} else {
						$articuloGenerico = false;
					}
				}
				
				if($articuloGenerico)
				{
					//Partip  Parcdo  Parudo  Parnen  Parfre  Parvia  Parcon  Parcnf  Pardim  Pardom  Parobs
					$qParametros = "SELECT Parcdo,Parudo,Parnen,Parfre,Parvia,Parcon,Parcnf,Pardim,Pardom,Parobs FROM mhosidc_000097 WHERE Partip = '{$tipoGenerico}';";
					$resParametros = mysql_query($qParametros, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qParametros . " - " . mysql_error());
					if($infoParametros = mysql_fetch_array($resParametros))
					{
						$tieneParametrosFijos = "S";

						$parametrosFijosArticuloGenerico[0] = $infoParametros['Parcdo'];		//Cantidad de dosis
						$parametrosFijosArticuloGenerico[1] = $infoParametros['Parudo'];		//Unidad de dosis
						$parametrosFijosArticuloGenerico[2] = $infoParametros['Parnen'];		//No enviar
						$parametrosFijosArticuloGenerico[3] = $infoParametros['Parfre'];		//Frecuencia
						$parametrosFijosArticuloGenerico[4] = $infoParametros['Parvia'];		//Vias de administracion
						$parametrosFijosArticuloGenerico[5] = $infoParametros['Parcon'];		//Condicion de suministro
						$parametrosFijosArticuloGenerico[6] = $infoParametros['Parcnf'];		//Confirmada preparacion
						$parametrosFijosArticuloGenerico[7] = $infoParametros['Pardim'];		//Dias maximos tratamiento
						$parametrosFijosArticuloGenerico[8] = $infoParametros['Pardom'];		//Dosis maximas tratamiento
						$parametrosFijosArticuloGenerico[9] = $infoParametros['Parobs'];		//Observaciones
					}
				}
			}
			
			//Si tiene componentes asociados en la tabla de componentes por tipo, mostrará los tipos
			$qComp = "SELECT Cartip,Carcod,Carcco,Cardis FROM mhosidc_000098 WHERE Cartip = '{$tipoGenerico}';";
			$resComp = mysql_query($qComp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qComp . " - " . mysql_error());
			$componentesTipo = "";
			$tieneComponentes = false;
			$ccoSF = ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
			while($infoComp = mysql_fetch_array($resComp))
			{
				if($infoComp['Carcco'] == $ccoSF)
				{
					$qArt = " SELECT Artcom, Artgen, Deffra, Deffru  
								FROM mhosidc_000026, mhosidc_000059 
							   WHERE Artcod = '".$infoComp['Carcod']."'
								 AND Artest = 'on'
							     AND Artcod = Defart
								 AND Defcco = '".$infoComp['Carcco']."'
								 AND Defest = 'on';";
					
				} 
				else 
				{
					$qArt = " SELECT Artcom, Artgen, Deffra, Deffru 
								FROM ".$wcenpro."_000002, mhosidc_000059 
							   WHERE Artcod = '".$infoComp['Carcod']."' 
								 AND Artest = 'on'
							     AND Artcod = Defart
								 AND Defcco = '".$infoComp['Carcco']."'
								 AND Defest = 'on';";
				}
				$resArt = mysql_query($qArt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qArt . " - " . mysql_error());
				if($infoArt = mysql_fetch_array($resArt))
				{
					$componentesTipo .= $infoComp['Carcod']."**";
					$componentesTipo .= $infoComp['Cardis']."**";
					$componentesTipo .= utf8_decode($infoArt['Artcom'])."**";
					$componentesTipo .= utf8_decode($infoArt['Artgen'])."**";
					$componentesTipo .= utf8_decode($infoArt['Deffra'])."**";
					$componentesTipo .= utf8_decode($infoArt['Deffru']).";";
				}
				
				$tieneComponentes = true;
			}
			
			//return $componentesTipo;

			if(str_replace("%"," ",$codigo)){
				$nombreArticuloPersonalizado = "S";
			}
			
			//Clasificacion por tipo
			if($tipoArticuloMedicamentoLiquido == "L"){
				$tipoArticulo = "2";
			}
			
			//Casos posibles de tipo de articulo
			switch($tipoArticulo){
				case '1':	//Articulo generico
					$borrar .= " generico";
 					break;
				case '2':	//Articulo liquido
					$borrar .= " liquido";
					break;
				default:	//Articulo normal
					$borrar .= " normal";
					break;
			}

			
			if(@$articuloGenerico)
			{
				
				// 2012-07-25
				// Se modificaron las asignaciones a las variables para que tomen el nombre del articulo y no el criterio de busqueda
				// ya que el criterio en la nueva busqueda por familia siempre va a ser un codigo, no un nombre
				@$articuloCodigoConsulta = $rs['Artcod'];  // $codigoGenericoArticulo;
				@$articuloNombreGenerico = "*".$rs['Artgen'];  // "*".str_replace("%"," ",$rs['Artgen']);
				@$articuloNombreComercial = $rs['Artcom'];  // $criterioCM
				
				$nombreArticuloGenerico = $articuloNombreGenerico;
				
				if(!$agrupoGenericos)
				{
					$agrupoGenericos = true;
					$puedeAgregar = true;
				} 
				else 
				{
					$puedeAgregar = false;
				}
			} 
			else 
			{
				$articuloCodigoConsulta = $rs['Artcod'];
				$articuloNombreGenerico = $rs['Artgen'];
				$articuloNombreComercial = $rs['Artcom'];
			}
			
				
			/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
			 * Argumentos:
			 * 
			 * 0: Como se muestra en el autocomplete
			 * 1: Codigo del articulo
			 * 2: Nombre comercial del articulo
			 * 3: Nombre genérico del articulo
			 * 4: Tipo protocolo
			 * 5: (M)edicamento o (L)iquido
			 * 6: Es generico
			 * 7: Origen
			 * 8: Grupo de medicamento
			 * 9: Forma farmaceutica
			 * 10:Unidad
			 * 11:POS
			 * 12:Unidad de fraccion
			 * 13:Cantidad de fracciones
			 * 14:Vencimiento
			 * 15:Dias de estabilidad
			 * 16:Es dispensable
			 * 17:Es duplicable
			 * 18:Dias maximos sugeridos
			 * 19:Dosis maximas sugeridas
			 * 20:Via
			 * 21:Abre ventana parametrizada ----
			 * 22:Cantidad de dosis
			 * 23:Unidad de dosis
			 * 24:No enviar
			 * 25:Frecuencia
			 * 26:Vias de administracion
			 * 27:Condicion de suministro
			 * 28:Confirmada preparacion
			 * 29:Dias maximos tratamiento
			 * 30:Dosis maximas tratamiento
			 * 31:Observaciones adicionales
			 * 32:Componentes asociados al tipo
			 * 33:Observaciones si es Dosis Adaptada
			 * 34:Tipo de manejo (Interno/Externo)
			 */
			
			if($puedeAgregar)
			{
				if($tieneComponentes)
				{
					$abreVentanaModal = "S";
				} 
				else 
				{
					$abreVentanaModal = "N";
				}
				
				$noEnviar = ( esStock( $conex, $wbasedato, $rs['Artcod'], $ccoPaciente ) == true )? 'on' : 'off';
				
				$consulta .= "<font color=gray><b>Generico:</b></font> ".htmlentities($articuloNombreGenerico)." <font color=gray><b>Comercial:</b></font> ".htmlentities($articuloNombreComercial).
				"|".strtoupper($articuloCodigoConsulta).
				"|".htmlentities($articuloNombreGenerico).
				"|".htmlentities($articuloNombreComercial).
				"|".$tipoProtocolo.
				"|".$tipoArticuloMedicamentoLiquido.
				"|".$tipoGenerico.
				"|".$rs['origen'].
				"|".$rs['Artgru'].
				"|".$rs['Artfar'].
				"|".$rs['Artuni'].
				"|".$rs['Artpos'].
				"|".$rs['Deffru'].
				"|".$rs['Deffra'].
				"|".$rs['Defven'].
				"|".$rs['Defdie'].
				"|".$rs['Defdis'].
				"|".$rs['Defdup'].
				"|".$rs['Defdim'].
				"|".$rs['Defdom'].
				"|".$rs['Defvia'].
				"|".$abreVentanaModal.
				"|".@$parametrosFijosArticuloGenerico[0].
				"|".@$parametrosFijosArticuloGenerico[1].
				"|".@$parametrosFijosArticuloGenerico[2].
				"|".@$parametrosFijosArticuloGenerico[3].
				"|".@$parametrosFijosArticuloGenerico[4].
				"|".@$parametrosFijosArticuloGenerico[5].
				"|".@$parametrosFijosArticuloGenerico[6].
				"|".@$parametrosFijosArticuloGenerico[7].
				"|".@$parametrosFijosArticuloGenerico[8].
				"|".@$parametrosFijosArticuloGenerico[9].
				"|".@$componentesTipo.
				"|".@$noEnviar.
				"|".@$observacionesDA.
				"|".$rs['Artonc'].
				"|".$rs['Artint'].
				"\n";
			}
			
			$cont1++;
	} 
	else 
	{
		$consulta = $consulta."No se encontraron coincidencias";
	}

	liberarConexionBD($conex);

	return $consulta;
}


/*
 * AJAX::ConsultarArticulosPorNombre
 */
function consultarArticulosProtocolo( $wbasedato, $wcenmez, $criterio, $ccoPaciente, $dosis, $administracion ){
	//Variable que se necesitan
	$centroCostos = "1183";
	$criterio = strtoupper($criterio);
	
	global $conex;
	global $wcenpro;

	$coleccion = array();
	$consulta = "";

	global $centroCostosServicioFarmaceutico;
	global $codigoServicioFarmaceutico;
	global $codigoCentralMezclas;
	global $centroCostosCentralMezclas;
	
	global $protocoloNormal;

	$esSF = $centroCostos == $centroCostosServicioFarmaceutico ? true : false;
	$esCM = $centroCostos == $centroCostosCentralMezclas ? true : false;

	$dosis_exacta = true;
	$articulo_pos = true;
	
	@$codigo = str_replace("-","%",$codigo);

	$tipoArticulo = "";
	$tipoGenerico = "";

	$observacionesDA = "";
	
	// Variables para guardar el último artículo que mas se acerca a los criterios de búsqueda
	$articulo_encontrado = "";

	//*******************************Grupos que puede ver el centro de costos del usuario
	$tieneGruposIncluidos = false;
	$gruposIncluidos = "(";

	$q6 = "SELECT DISTINCT Ccogka FROM {$wbasedato}_000011 WHERE Ccoest='on' AND Ccogka != '*' AND Ccocod='$centroCostos';";
	$res6 = mysql_query($q6, $conex);

	// Define los grupos de medicamentos que puede ver el centro de costos en el kardex
	while($rs6 = mysql_fetch_array($res6)){
		$tieneGruposIncluidos = true;
		if(strpos($rs6['Ccogka'],$gruposIncluidos) === false){
			$gruposIncluidos .= "'".str_replace(",","','",$rs6['Ccogka'])."',";
		}
	}
	$gruposIncluidos .= "'')";
	//********************************

	 // Se consultan los medicamentos que cumplan con los criterios seleccionados, excepto la dosis
	 $sql = "( SELECT
				Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip, Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, 0 Defmin, 10000 Defmax, Artonc
			FROM
				{$wbasedato}_000026 a,
				{$wbasedato}_000027 b,
				{$wbasedato}_000059 c
			WHERE 
					Artcod = '".$criterio."'
				AND Artest = 'on'
				AND Artuni = Unicod
				AND Artcod = Defart
				AND Defest = 'on' )

			";

	// 2012-07-09
	// Se agregó ORDER BY Famund DESC para poder ordenar según la unidad destacada para la familia de medicamentos

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	// Si hay un articulo o más
	if($num > 0)
	{
		$rs = mysql_fetch_array($res);
		
		// Se asigna la cantidad de fracciones del artículo
		$fraccionArticulo = $rs['Deffra'];
		
		// Si se encuentra un solo registro y es dosis exacta no es necesario hacer evaluación de fracciones 
		// y este se lleva como resultado de la búsqueda
		if($dosis_exacta)
		{
			$articulo_encontrado = $rs['Artcod'];
		}
		else
		{
			// Si la dosis es múltiplo de las fracciones o viceversa
			if($multiploDosis==0 || $multiploFraccion==0)
			{
				// Si las unidades a pedir estan en el rango de máximo y mínimo
				if(($dosis*1)>=($rs['Defmin']*1) && ($dosis)*1<=($rs['Defmax']*1))
				{
					// Si es primera vez que pasa o las unidades requeridas son menores a las anteriormente guardadas
					if($auxUnidadesRequeridas == -1 || $unidadesRequeridas < $auxUnidadesRequeridas)
					{
						$auxUnidadesRequeridas = $unidadesRequeridas;
						$articulo_encontrado = $rs['Artcod'];
					}
				} 
				// Si la fracción del artículo es superior a la dosis
				else if($dosisCubiertas>1)
				{
					// Si es primera vez que pasa o las unidades requeridas son menores a las anteriormente guardadas
					if($auxDosisCubiertas == -1 || $dosisCubiertas < $auxDosisCubiertas)
					{
						$auxDosisCubiertas = $dosisCubiertas;
						$articulo_encontrado = $rs['Artcod'];
					}
				}
			}
		}	

		if($articulo_encontrado==-1)
		{
			// Si no se encontro artículo que cumpla con la dosis
			// Se crea una dosis Adaptada
			$articulo_encontrado = "DA0000";
		}
						
		//Con el fin de obtener los genericos se tendra en cuenta unicamente la primera palabra de la consulta antes del espacio
		//$vecCriterio = explode("%",$articulo_encontrado);
		//$articulo_encontradoCM = $vecCriterio[0];
		
		// Se obtienen los datos del articulo encontrado
		$qCod = " ( SELECT "
		." 		Artcod, Artcom, Artgen, Artuni, Unides, '$codigoServicioFarmaceutico' origen, SUBSTRING_INDEX( Artgru, '-', 1 ) Artgru, IFNULL(Artfar,'00') Artfar, Artpos, '' Arttip , Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, Artonc "
		." FROM "
		." 		{$wbasedato}_000027, {$wbasedato}_000026, {$wbasedato}_000059 "
		." WHERE "
		."			Artuni = Unicod "
		."		AND Artcod = '".$articulo_encontrado."' "
		."		AND Artcod = Defart "
		."		AND Artest = 'on' "
		."		AND Defest = 'on' ) ";

		/*
		$qCod .= " UNION ";
		
		$qCod .= " ( SELECT "
		."	Artcod, Artcom, Artgen, Artuni, Unides, '$codigoCentralMezclas' origen, '' Artgru, '00' Artfar, '' Artpos, Arttip , Deffra, Deffru, Defven, Defdie, Defdis, Defdup, Defdim, Defdom, Defvia, Artonc "
		." FROM "
		." {$wbasedato}_000027, cenpro_000002, {$wbasedato}_000059 "
		." WHERE "
		."  	Artuni = Unicod "
		."	AND Artcod = '".$articulo_encontrado."' "
		."	AND Artcod = Defart "
		."	AND Artest = 'on' "
		."	AND Defest = 'on' ) ";
		*/

		$resart = mysql_query( $qCod, $conex ) or die( mysql_errno()." - Error en el query $qCod - ".mysql_error() );
		$numart = mysql_num_rows( $resart );
		
	}
	else
	{
		$numart = 0;
	}

	$cont1 = 0;
	$agrupoGenericos = false;
	$abreVentanaModal = "";
	
	if($numart > 0)
	{

			$codigoGenericoArticulo = "";


			
			$tieneParametrosFijos = "N";
			$nombreArticuloGenerico = "";
			$puedeAgregar = true;
			
			$rs = mysql_fetch_array($resart);
			
			$tipoProtocolo = $protocoloNormal;
			$tipoArticuloMedicamentoLiquido = "M";
			$tipoGenerico = "";
			$tipoArticulo = "1";
			$borrar = "";
			
			//Consulta del tipo al que pertenece
			$qTipLM = "SELECT Meltip FROM {$wbasedato}_000066 WHERE Melgru = '{$rs['Artgru']}' AND Melest='on' ";
			$resTipLM = mysql_query($qTipLM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipLM . " - " . mysql_error());
			if($infoTipLM = mysql_fetch_array($resTipLM)){
				$tipoArticuloMedicamentoLiquido = $infoTipLM['Meltip'];
			}
			
			//Consulta del protocolo al que pertenece el articulo
			$qProt = "SELECT Arkcod,Arkest,Arkcco,Arktip FROM {$wbasedato}_000068 WHERE Arkcod = '{$rs['Artcod']}'";
			$resProt = mysql_query($qProt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qProt . " - " . mysql_error());
			if($infoProt = mysql_fetch_array($resProt)){
				$tipoProtocolo = $infoProt['Arktip'];
			}
			
			$color = "red";
			$fftica = $rs['Artfar'];

			//CLASIFICACION DE LOS ARTICULOS
			
			/*TIPOS DE ARTICULOS
			 * 1.  Articulos del maestro de SF
			 * 2.  Articulos del maestro de CM
			 * 3.  Articulos genericos NU,QT,DA dependiendo del tipo en la tabla 68 y la 2
			 * 4.  
			 */
			if($rs['origen'] == $codigoServicioFarmaceutico){ 		//No tiene genéricos
				$tipoGenerico = "";
			}
			
			//On
			/*
			echo "Tipo gen: ".$qTipGen."<br>";
			echo "rs: ".$rs['origen']."<br>";
			echo "central M : ".$codigoCentralMezclas."<br>";
			echo "tipo: ".$rs['Arttip']."<br>";
			*/
	
			if($rs['origen'] == $codigoCentralMezclas)
			{  			//Puede tener genéricos
				//Consulta del tipo al que pertenece
				$tipoCentralMezclas = $rs['Arttip'];
				
				if(!empty($tipoCentralMezclas))
				{
					$qTipGen = "SELECT Arkcod, Tiptpr, Tipdes FROM {$wcenpro}_000001, mhosidc_000068 WHERE Tipcod = '{$tipoCentralMezclas}' AND Arktip = Tiptpr";
					$resTipGen = mysql_query($qTipGen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipGen . " - " . mysql_error());
					if($infoTipGen = mysql_fetch_array($resTipGen)){
						$tipoGenerico = $infoTipGen['Tiptpr'];
						$codigoGenericoArticulo = $infoTipGen['Arkcod'];
						$articuloGenerico = true;
					} else {
						$articuloGenerico = false;
					}
				} else {
					$qTipGen = "SELECT Arkcod, Arktip FROM mhosidc_000068, mhosidc_000098 WHERE Arkcod = '{$rs['Artcod']}' AND Cartip = Arktip";
					
					$resTipGen = mysql_query($qTipGen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qTipGen . " - " . mysql_error());
					if($infoTipGen = mysql_fetch_array($resTipGen)){
						$tipoGenerico = $infoTipGen['Arktip'];
						$codigoGenericoArticulo = $infoTipGen['Arkcod'];
						$articuloGenerico = true;
					} else {
						$articuloGenerico = false;
					}
				}
				
				if($articuloGenerico)
				{
					//Partip  Parcdo  Parudo  Parnen  Parfre  Parvia  Parcon  Parcnf  Pardim  Pardom  Parobs
					$qParametros = "SELECT Parcdo,Parudo,Parnen,Parfre,Parvia,Parcon,Parcnf,Pardim,Pardom,Parobs FROM mhosidc_000097 WHERE Partip = '{$tipoGenerico}';";
					$resParametros = mysql_query($qParametros, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qParametros . " - " . mysql_error());
					if($infoParametros = mysql_fetch_array($resParametros))
					{
						$tieneParametrosFijos = "S";

						$parametrosFijosArticuloGenerico[0] = $infoParametros['Parcdo'];		//Cantidad de dosis
						$parametrosFijosArticuloGenerico[1] = $infoParametros['Parudo'];		//Unidad de dosis
						$parametrosFijosArticuloGenerico[2] = $infoParametros['Parnen'];		//No enviar
						$parametrosFijosArticuloGenerico[3] = $infoParametros['Parfre'];		//Frecuencia
						$parametrosFijosArticuloGenerico[4] = $infoParametros['Parvia'];		//Vias de administracion
						$parametrosFijosArticuloGenerico[5] = $infoParametros['Parcon'];		//Condicion de suministro
						$parametrosFijosArticuloGenerico[6] = $infoParametros['Parcnf'];		//Confirmada preparacion
						$parametrosFijosArticuloGenerico[7] = $infoParametros['Pardim'];		//Dias maximos tratamiento
						$parametrosFijosArticuloGenerico[8] = $infoParametros['Pardom'];		//Dosis maximas tratamiento
						$parametrosFijosArticuloGenerico[9] = $infoParametros['Parobs'];		//Observaciones
					}
				}
			}
			
			//Si tiene componentes asociados en la tabla de componentes por tipo, mostrará los tipos
			$qComp = "SELECT Cartip,Carcod,Carcco,Cardis FROM mhosidc_000098 WHERE Cartip = '{$tipoGenerico}';";
			$resComp = mysql_query($qComp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qComp . " - " . mysql_error());
			$componentesTipo = "";
			$tieneComponentes = false;
			$ccoSF = ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
			while($infoComp = mysql_fetch_array($resComp))
			{
				if($infoComp['Carcco'] == $ccoSF){
					$qArt = "SELECT Artcom,Artgen FROM mhosidc_000026 WHERE Artcod = '{$infoComp['Carcod']}';";
				} else {
					$qArt = "SELECT Artcom,Artgen FROM {$wcenpro}_000002 WHERE Artcod = '{$infoComp['Carcod']}';";
				}
				$resArt = mysql_query($qArt, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qArt . " - " . mysql_error());
				if($infoArt = mysql_fetch_array($resArt)){
					$componentesTipo .= $infoComp['Carcod']."**";
					$componentesTipo .= $infoComp['Cardis']."**";
					$componentesTipo .= utf8_decode($infoArt['Artcom'])."**";
					$componentesTipo .= utf8_decode($infoArt['Artgen']).";";
				}
				$tieneComponentes = true;
			}
			
			if(str_replace("%"," ",$codigo)){
				$nombreArticuloPersonalizado = "S";
			}
			
			//Clasificacion por tipo
			if($tipoArticuloMedicamentoLiquido == "L"){
				$tipoArticulo = "2";
			}
			
			//Casos posibles de tipo de articulo
			switch($tipoArticulo){
				case '1':	//Articulo generico
					$borrar .= " generico";
 					break;
				case '2':	//Articulo liquido
					$borrar .= " liquido";
					break;
				default:	//Articulo normal
					$borrar .= " normal";
					break;
			}

			
			if(@$articuloGenerico){
				
				// 2012-07-25
				// Se modificaron las asignaciones a las variables para que tomen el nombre del articulo y no el criterio de busqueda
				// ya que el criterio en la nueva busqueda por familia siempre va a ser un codigo, no un nombre
				@$articuloCodigoConsulta = $rs['Artcod'];  // $codigoGenericoArticulo;
				@$articuloNombreGenerico = "*".$rs['Artgen'];  // "*".str_replace("%"," ",$rs['Artgen']);
				@$articuloNombreComercial = $rs['Artcom'];  // $criterioCM
				
				$nombreArticuloGenerico = $articuloNombreGenerico;
				
				if(!$agrupoGenericos){
					$agrupoGenericos = true;
					$puedeAgregar = true;
				} else {
					$puedeAgregar = false;
				}
			} else {
				$articuloCodigoConsulta = $rs['Artcod'];
				$articuloNombreGenerico = $rs['Artgen'];
				$articuloNombreComercial = $rs['Artcom'];
			}
			
				
			/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
			 * Argumentos:
			 * 
			 * 0: Como se muestra en el autocomplete
			 * 1: Codigo del articulo
			 * 2: Nombre comercial del articulo
			 * 3: Nombre genérico del articulo
			 * 4: Tipo protocolo
			 * 5: (M)edicamento o (L)iquido
			 * 6: Es generico
			 * 7: Origen
			 * 8: Grupo de medicamento
			 * 9: Forma farmaceutica
			 * 10:Unidad
			 * 11:POS
			 * 12:Unidad de fraccion
			 * 13:Cantidad de fracciones
			 * 14:Vencimiento
			 * 15:Dias de estabilidad
			 * 16:Es dispensable
			 * 17:Es duplicable
			 * 18:Dias maximos sugeridos
			 * 19:Dosis maximas sugeridas
			 * 20:Via
			 * 21:Abre ventana parametrizada ----
			 * 22:Cantidad de dosis
			 * 23:Unidad de dosis
			 * 24:No enviar
			 * 25:Frecuencia
			 * 26:Vias de administracion
			 * 27:Condicion de suministro
			 * 28:Confirmada preparacion
			 * 29:Dias maximos tratamiento
			 * 30:Dosis maximas tratamiento
			 * 31:Observaciones adicionales
			 * 32:Componentes asociados al tipo
			 * 33:Observaciones si es Dosis Adaptada
			 */
			
			if($rs['Artfar']=="" || $rs['Artfar']==" " || trim($rs['Artfar'])==".")
				$rs['Artfar'] = "00";
			
			if($puedeAgregar)
			{
				if($tieneComponentes){
					$abreVentanaModal = "S";
				} else {
					$abreVentanaModal = "N";
				}
				
				$noEnviar = ( esStock( $conex, $wbasedato, $rs['Artcod'], $ccoPaciente ) == true )? 'on' : 'off';
				
				$consulta .= "<font color=gray><b>Generico:</b></font> ".htmlentities($articuloNombreGenerico)." <font color=gray><b>Comercial:</b></font> ".htmlentities($articuloNombreComercial).
				"|".strtoupper($articuloCodigoConsulta).
				"|".htmlentities($articuloNombreGenerico).
				"|".htmlentities($articuloNombreComercial).
				"|".$tipoProtocolo.
				"|".$tipoArticuloMedicamentoLiquido.
				"|".$tipoGenerico.
				"|".$rs['origen'].
				"|".$rs['Artgru'].
				"|".$rs['Artfar'].
				"|".$rs['Artuni'].
				"|".$rs['Artpos'].
				"|".$rs['Deffru'].
				"|".$rs['Deffra'].
				"|".$rs['Defven'].
				"|".$rs['Defdie'].
				"|".$rs['Defdis'].
				"|".$rs['Defdup'].
				"|".$rs['Defdim'].
				"|".$rs['Defdom'].
				"|".$rs['Defvia'].
				"|".$abreVentanaModal.
				"|".@$parametrosFijosArticuloGenerico[0].
				"|".@$parametrosFijosArticuloGenerico[1].
				"|".@$parametrosFijosArticuloGenerico[2].
				"|".@$parametrosFijosArticuloGenerico[3].
				"|".@$parametrosFijosArticuloGenerico[4].
				"|".@$parametrosFijosArticuloGenerico[5].
				"|".@$parametrosFijosArticuloGenerico[6].
				"|".@$parametrosFijosArticuloGenerico[7].
				"|".@$parametrosFijosArticuloGenerico[8].
				"|".@$parametrosFijosArticuloGenerico[9].
				"|".@$componentesTipo.
				"|".@$noEnviar.
				"|".@$observacionesDA.
				"|".$rs['Artonc'].
				"\n";
			}
			
			$cont1++;
	} 
	else 
	{
		$consulta = $consulta."No se encontraron coincidencias";
	}

	liberarConexionBD($conex);

	return $consulta;
}


/*
 * AJAX::ConsultarArticulosPorNombre
 */
function consultarProtocolo($wbasedato,$protocolo){

	$protocolo = $protocolo;
	
	global $conex;
	global $usuario;
	global $user;

	$coleccion = array();
	$consulta = "";

	// Se consulta la especialidad del médico
	$sql =  " SELECT Medesp
				FROM {$wbasedato}_000048
			   WHERE Meduma = '".substr( $user, 2 )."'
				 AND Medest = 'on'
			";
	$resmed = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$nummed = mysql_num_rows( $resmed );
	$rowmed = mysql_fetch_array($resmed);
	
	$especialidad = "";
	
	if($nummed>0)
	{
		$str_especialidad = explode("-",$rowmed['Medesp']);
		$especialidad = $str_especialidad[0];
	}
	
	//Se buscan los encabezados posibles
	$sqlEncPro = "SELECT
					Procod, Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, Procio, Proped, Prorec, Protra
				FROM
					".$wbasedato."_000137
				WHERE Protip = 'Ordenes'
					AND Pronom = '".$protocolo."'
					AND Proest = 'on'
					AND
					(   Promed = '".$codigoUsuario."'
					 OR Promed = '*'
					)
					AND
					(   Proesp = '".$especialidad."'
					 OR Proesp = '*'
					)
				ORDER BY Pronom ASC
				";
				
	$resEncPro = mysql_query( $sqlEncPro, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
	
	$sumAnt = 0;
	$codProtocolo = false;
	//Se buscará el protocolo que más concordancias tenga
	//Este será el protocolo a cargar
	while( $rowsEncPro = mysql_fetch_array($resEncPro) ){
	
		$sumAct = 1; //Cuenta los puntos para el protocolo actual
		
		if( $rowsEncPro['Promed'] == $codigoUsuario ){
			$sumAct++;
		}
		
		if( $rowsEncPro['Proesp'] == $especialidad ){
			$sumAct++;
		}
		
		// if( $rowsEncPro['Procco'] == $codigoUsuario ){
			// $sumAct++;
		// }
		
		if( $sumAct > $sumAnt ){
			$sumAnt = $sumAct;
			$codProtocolo = $rowsEncPro['Procod'];
		}
	}
	

	// Se consultan los datos del protocolo
	$sql =  " SELECT Dprcod, Dprfre, Dprvia, Dprcnd, Dprdos, Dprobs, Dprpes, Dprjus, Dprete, Dprese, Promed, Proesp
				FROM {$wbasedato}_000137, {$wbasedato}_000138
			   WHERE Pronom = '".$protocolo."'
				 AND Proest = 'on'
				 AND Protip = 'Ordenes'
				 AND Procod = Dprpro
				 AND Dprest = 'on' 
				 AND Procod = '$codProtocolo'";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	// Si se encontró protocolo
	if($num > 0)
	{
		while($rs = mysql_fetch_array($res))
		{	
			$consulta .= 
			$rs['Dprcod'].
			"|".$rs['Dprfre'].
			"|".$rs['Dprvia'].
			"|".$rs['Dprcnd'].
			"|".$rs['Dprdos'].
			"|".$rs['Dprobs'].
			"|".$rs['Dprpes'].
			"|".$rs['Dprjus'].
			"|".$rs['Dprete'].
			"|".$rs['Dprese'].
			"\\";
		}
	} 
	else 
	{
		$consulta = $consulta."No se encontraron coincidencias";
	}

	liberarConexionBD($conex);

	return $consulta;
}



function grabarEstadoAprobacionArticulos($basedatos,$historia,$ingreso,$fecha,$codigosArticulos,$estadoAprobacion,$codUsuario){
	$conexion = obtenerConexionBD("matrix");

	$estado = "0";

	$centroCostos = "%";
	$vecArticulos = explode("|",$codigosArticulos);

	foreach ($vecArticulos as $articulo){
		$vecArticulo = explode(";",$articulo);

		if(!empty($vecArticulo[0])){
			$q = "UPDATE {$basedatos}_000054 SET
					Kadare = '$estadoAprobacion'
				WHERE
					Kadhis = '$historia'
					AND Kading = '$ingreso'
					AND Kadfec = '$fecha'
					AND Kadart = '$vecArticulo[0]'
					AND Kadori = '$vecArticulo[1]'
					AND Kadfin = '$vecArticulo[2]'
					AND Kadhin = '$vecArticulo[3]';";

			$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

			if( $estadoAprobacion != 'on' ){
				$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_DESAPROBADO');
			}
			else{
				$mensajeAuditoria = obtenerMensaje('MSJ_ARTICULO_APROBADO');
			}
			
			//Consulto idOriginal
			$q = "SELECT * 
				  FROM
					{$basedatos}_000054
				 WHERE
					Kadhis = '$historia'
					AND Kading = '$ingreso'
					AND Kadfec = '$fecha'
					AND Kadart = '$vecArticulo[0]'
					AND Kadori = '$vecArticulo[1]'
					AND Kadfin = '$vecArticulo[2]'
					AND Kadhin = '$vecArticulo[3]';";

			$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$numrows = mysql_num_rows( $res );
			
			if( $numrows > 0 ){
				$rows = mysql_fetch_array( $res );
				$idOriginal = $rows[ 'Kadido' ];
			}

			//Registro de auditoria
			$auditoria = new AuditoriaDTO();

			$auditoria->historia = $historia;
			$auditoria->ingreso = $ingreso;
			$auditoria->descripcion = "$vecArticulo[0],$vecArticulo[1],$vecArticulo[2],$vecArticulo[3]";
			$auditoria->fechaKardex = $fecha;
			$auditoria->mensaje = $mensajeAuditoria;
			$auditoria->seguridad = $codUsuario;
			$auditoria->idOriginal = $idOriginal;

			registrarAuditoriaKardex($conexion,$basedatos,$auditoria);
			
			$estado = "1";
		}
	}
	liberarConexionBD($conexion);
	return $estado;
}
function consultarComponentesTipoLev(){
	global $codigoServicioFarmaceutico;
	global $conex;

	$q = "SELECT
				artcod, artcom, artuni, unides, '$codigoServicioFarmaceutico' origen, Artfar
			FROM
				mhosidc_000026, mhosidc_000027
			WHERE
				artuni = unicod
				AND artest = 'on'
				AND SUBSTRING_INDEX( Artgru, '-', 1 ) IN (SELECT Melgru FROM mhosidc_000066 WHERE Melest = 'on' AND Meltip = 'L')
			ORDER BY Artcom";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$consulta = "";

	$cont1 = 0;

	if($num > 0){
		while ($cont1 < $num)
		{
			$rs = mysql_fetch_array($res);

			$referencia = "javascript:adicionarComponenteArticulo('".$rs['artcod']."','".str_replace(" ","_",htmlentities(trim($rs[1])))."');";
			$consulta = $consulta." * <a href='#null' onClick=$referencia>".htmlentities(trim($rs[1])).'</a><br/>';
			$cont1++;
		}
	} else {
		$consulta = $consulta."<b>No se encontraron coincidencias</b>";
	}
	return $consulta;
}



function salirsigrabar( $wemp_pmla, $whistoria, $wingreso, $wfechagrabacion, $tipoDocumento,$wcedula, $editable ){
 
	global $conex;
	global $whce;
	global $wbasedatohce;
	global $wbasedato;
	global $paciente;
	global $wusuario;
	global $usuario;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0); 		
	
	if( $editable ){
		$firmaDigital = "";
		$esPrimerKardex = "";
		
		$paciente = consultarInfoPacienteOrdenHCE($tipoDocumento,$wcedula);
		$usuario = consultarUsuarioOrdenes($wusuario);
		
		// if(!existeEncabezadoKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfechagrabacion)){
		if(!existeEncabezadoKardexSinCco($wbasedato,$conex,$paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion, $datos )){
			replicarEncabezadoKardexAnterior($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfechagrabacion);
		}
		else{
			//Se desbloquea el kardex.
			$q="UPDATE ".$wbasedato."_000053
				   SET Kargra = 'on'
				 WHERE Karhis = '$paciente->historiaClinica'
				   AND Karing = '$paciente->ingresoHistoriaClinica'
				   
				   AND Fecha_data = '$wfechagrabacion'";
			$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
		}
		
		//marca los registros cómo leídos según el usuario
		//marcarRegistrosLeidos( $conex, $wbasedato, $wbasedatohce, $usuario->codigo, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
		
		//Se habilita nuevamente esta funcion para que guarde las pestañas leidas sin necesidad de hacer clic en grabar
		// marcarRegistrosLeidos( $conex, $wbasedato, $wbasedatohce, $usuario->codigo, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $pestanasVistas); 
		
		cargarInfusionesADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion);
		cargarArticulosADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$esPrimerKardex,$firmaDigital);
		cargarExamenesADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$firmaDigital);
		cargarDietasADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$firmaDigital);
		actualizarUsuarioDextrometer( $conex, $wbasedato,$paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$usuario->codigo,$firmaDigital);	
		cargarMedicoADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion);
		
		cargarProcedimientosTemporalADetalle( $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
		eliminarDatoTemporalProcedimiento( $wbasedato,$wbasedatohce, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );	
	}
	
	$_SESSION['wordenes'] = 0; //Asigna a la variable de sesion wordenes el valor de 0 para que el usuario pueda ingresar a otra orden.
	
	echo json_encode($datamensaje);
    return;
 }

/*********************************************************************************************************************************
 * 						SECCION PARA INCLUIR EL USO DE CONSULTAR MEDIANTE AJAX
 * ****MODO DE USO
 * **1.  Hacer la invocación a este php cuando se haga la invocación asíncrona en el objeto xmlhttprequest
 * **	Ej: ajax.open("POST", "../../../include/root/comun.php",true);
 * **2.  Enviar por parametro en esta invocacion
 * **	Ej: ajax.send("consultaAjax=01&basedatos="+document.forms.forma.wbasedato.value+"&parametro1=" + document.forms.forma.parametro1.value);
 **********************************************************************************************************************************/
//Consultas ajax.
if(isset($consultaAjaxKardex)){

	if( empty($whce) && !empty($wemp_pmla) )
		$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
		
	if( empty($basedatos) && !empty($wemp_pmla) )
		$basedatos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	switch($consultaAjaxKardex){
		case 1:
			echo grabarArticuloDetalle($basedatos,$historia,$ingreso,$fechaKardex,$codArticulo,$cantDosis,$unDosis,$per,$fmaFtica,$fini,$hini,$via,$conf,$dtto,$obs,$origenArticulo,$codUsuario,$condicion,$dosMax,$cantGrabar,$unidadManejo,$cantidadManejo,$primerKardex,$horasFrecuencia,$fIniAnt,$hIniAnt,$noDispensar,$tipoProtocolo,$centroCostosGrabacion,$prioridad,$wcantidadAlta,$wimpresion,$walta,$wmanejo,$wposologia,$wunidadposologia,$nombreArticulo);
			break;
		case 2:
			if(!isset($tipoProtocolo)){
				$tipoProtocolo = "";
			}
			echo consultarMedicamentosPorCodigo($basedatos,prepararCriterio($codigo),$tipoMedicamento,$unidadMedida,$centroCostos,$gruposMedicamentos,$tipoProtocolo, @$ccoPaciente);
			break;
		case 3:
			if(!isset($tipoProtocolo)){
				$tipoProtocolo = "";
			}
				
			echo consultarMedicamentosPorNombre($basedatos,prepararCriterio($nombre),$tipoMedicamento,$unidadMedida,$centroCostos,$gruposMedicamentos,$tipoProtocolo, @$ccoPaciente);
			break;
		case 4:
			echo eliminarArticuloDetalle($basedatos,$historia,$ingreso,$fecha,$codArticulo,$codUsuario,$fechaInicio,$horaInicio);
			break;
		case 5:
			echo consultarEsquemaInsulinaPorCodigo($basedatos,$codigo);
			break;
		case 6:
			echo insertarMedicoTratante($basedatos,$tipoDocumento,$numeroDocumento,$historia,$ingreso,$codUsuario,$fecha,$idRegistro,$tratante,$codigoEspecialidad,$codigoMatrix);
			break;
		case 7:
			echo grabarExamenKardex($basedatos,$historia,$ingreso,$fecha,$codigoExamen,$nombreExamen,$observaciones,$estado,$fechaDeSolicitado,$codUsuario,$consecutivoOrden,$firma,$observacionesOrden,$justificacion,$consecutivoExamen,$numeroItem,$impExamen,$altExamen, $firmHCE );
			break;
		case 8:
			echo eliminarExamenKardex($basedatos,$historia,$ingreso,$fecha,$codExamen,$consecutivoOrden,$codUsuario,$numeroItem);
//			echo eliminarExamenKardex($basedatos,$historia,$ingreso,$fecha,$codExamen,$consecutivoExamen,$consecutivoOrden,$codUsuario,$numeroItem);
			break;
		case 9:
			echo consultarComponentesPorCodigo($basedatos,$codigo,$tipoMedicamento,$unidadMedida);
			break;
		case 10:
			echo consultarComponentesPorNombre($basedatos,$nombre,$tipoMedicamento,$unidadMedida);
			break;
		case 11:
			echo grabarInfusionKardex($basedatos,$historia,$ingreso,$fecha,$componentes,$consecutivo,$observaciones,$codUsuario,$fechaSolicitud);
			break;
		case 12:
			echo eliminarInfusionKardex($basedatos,$historia,$ingreso,$fecha,$componentes,$consecutivo,$codUsuario);
			break;
		case 13:
			echo eliminarMedicoTratante($basedatos,$historia,$ingreso,$codUsuario,$idRegistro,$fecha);
			break;
		case 14:
			echo insertarDietaKardex($basedatos,$historia,$ingreso,$codUsuario,$fecha,$idRegistro);
			break;
		case 15:
			echo eliminarDietaKardex($basedatos,$historia,$ingreso,$codUsuario,$idRegistro,$fecha);
			break;
		case 16:
			echo suspenderMedicamentoKardex($basedatos,$historia,$ingreso,$codigoArticulo,$fecha,$estado,$fechaInicio,$horaInicio,$codUsuario);
			break;
		case 17:
			echo grabarArticuloDetallePerfil($basedatos,$historia,$ingreso,$fechaKardex,$codArticulo,$dtto,$obs,$codUsuario,$via,$dosisMaximas,$prioridad,$fechaInicio,$horaInicio,$autorizadoCtc);
			break;
		case 18:
			echo reemplazarArticuloDetallePerfil($basedatos,$historia,$ingreso,$fechaKardex,$codArticulo,$codArticuloNuevo,$dtto,$obs,$unidadDosis,$formaFarm,$origen,$fechaInicio,$horaInicio,$codUsuario);
			break;
		case 19:
			echo consultarMedicosPorEspecialidad($basedatos,$especialidad);
			break;
		case 20:
			echo marcarAprobacionRegente($basedatos,$historia,$ingreso,$fecha,$estado,$codUsuario);
			break;
		case 21:
			echo actualizarAlergiaPorFecha($basedatos,$historia,$ingreso,$fecha,$descripcion,$codUsuario);
			break;
		case 22:
			echo grabarEsquemaDextrometer($basedatos,$historia,$ingreso,$fecha,$codInsulina,$frecuencia,$codEsquema,$arrDosis,$arrUDosis,$arrVia,$arrObservaciones,$codUsuario,$actualizaIntervalos);
			break;
		case 23:
			echo eliminarEsquemaDextrometer($basedatos,$historia,$ingreso,$fecha,$codInsulina,$codUsuario);
			break;
		case 24:
			//			echo consultarNiveles($basedatos,$historia,$ingreso,$fecha,$codInsulina,$usuario);
			if(!isset($nivelB)){
				$nivelB = '';
			}

			if(!isset($nivelC)){
				$nivelC = '';
			}

			if(!isset($nivelD)){
				$nivelD = '';
			}

			echo consultarNiveles($basedatos,$nivelA,$nivelB,$nivelC,$nivelD);
			break;
		case 25:
			echo @consultarHabitacionPacienteServicio($basedatos,$servicio);
			break;
		case 26:
			echo validarFirmaElectronica($basedatos,$usuarioHce,$firma);
			break;
		case 27:
			if(isset($_GET)){
				$q = strtolower(utf8_decode( $_GET["q"]));
			}else{
				$q = strtolower(utf8_decode( $HTTP_GET_VARS["q"]));
			}

			echo consultarAyudasDiagnosticasPorNombre($basedatos,prepararCriterio($q),@$unidadRealiza,$tipoServicio,$especialidad);
			break;
		case 28:
			echo cancelarOrdenHCE($basedatos,$historia,$ingreso,$fecha,$codUsuario,$centroCostos,$consecutivoOrden);
			break;
		case 29:
			echo grabarOrdenHCE($basedatos,$historia,$ingreso,$fecha,$codUsuario,$centroCostos,$consecutivoOrden,$observacionesOrden);
			break;
		case 30:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
					// $q = strtolower($_GET["q"]);
				}
			}
				
			echo consultarArticulos($basedatos,prepararCriterio($q), $ccoPaciente );
			break;
			
		case 31:
			echo consultarMedicamentos( $conex, $basedatos, $q );
			break;
			
		case 32:
			echo cargarMedicamentosActivosAnterior( $conex, $wbasedato, $wcenmez, $historia, $ingreso, $cco, $fecha );
			break;
			
		case 33:
			mostrarDetalleOrdenes($wemp_pmla,$wempresa,$wbasedato,$whis,$wing,$wfecini,$wfecfin,$wtiposerv,$wprocedimiento,$westadodet);
			break;
		
		case 34:
			filtrarFamiliaMedicamentos($conex, $basedatos, $familia, $presentacion, $unidad);
			break;
		
		case 35:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
			
			echo consultarArticulosFamilia($basedatos,$cenmez, $q, $ccoPaciente, $pre, $med, $dos, $adm );
			break;
			
		case 36:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
			echo consultarArticulosProtocolo($basedatos,$cenmez,prepararCriterio($q), $ccoPaciente, $dos, $adm );
			break;

		case 37:
		
			if( !isset( $protocolo ) ){
				if(isset($_GET)){
					$protocolo = $_GET["protocolo"];
				}else{
					$protocolo = $HTTP_GET_VARS["protocolo"];
				}
			}
			echo consultarProtocolo($basedatos,$protocolo);
			break;

		case 38:
			echo consultarAyudasDiagnosticasPorCodigo($basedatos,$ayd_cod,@$unidadRealiza,$especialidad);
			break;
			
		case 39:
			echo actualizarImpMedicamento($basedatos,$articuloAlta,$whis,$wing,$codigoArticulo,$wfecha,$wfecini,$wfecfin);
			break;
			
		case 40:
			echo actualizarAltaExamen($basedatos,$imprimirExamen,$wcodigo_examen,$wfecha,$wtipo_orden,$wnumero_orden,$item );
			break;

		case 41:
			echo actualizarIndicaciones($basedatos,$historia,$ingreso,$wfecha,$windicaiones);
			break;

		case 42:
			echo actualizarImpresion($basedatos,$whis,$wing,$wfec);
			break;

		case 43:
			echo actualizarTipoManejo($basedatos,$articuloInterno,$whis,$wing,$codigoArticulo,$wfecha,$wfecini,$wfecfin);
			break;

		case 44:
			echo grabarNuevoExamen($wemp_pmla,$basedatos,$descripcion,$tipoServicio,$especialidad);
			break;
			
		case 45:
			echo consultarTratamientos($basedatos,$wart);
			break;
		
		case 46:
			echo consultarResumenHistoria($wemp_pmla,$basedatoshce,$whistoria,$wingreso);
			break;
		
		case 47:
			echo consultarFormularioTipoOrden($basedatoshce,$wtipo);
			break;
	
		case 48:
			echo tipoResponsableEsEPS($wemp_pmla,$basedatos,$whistoria,$wingreso);
			break;

		case 49:
			echo consultarAyudasDiagnosticasPorTipo($basedatos,$tipoServicio,$especialidad);
			break;
			
		case 50:
			echo consultarFormularioHCE($basedatoshce,$formTipoOrden,$historia,$ingreso);
			break;
	
		case 51:
			echo borrarFormularioHCE($basedatoshce,$wcco,$historia,$ingreso,$firmHce );
			break;

		case 52:
			echo consultarDetalleFamilia($basedatos,$articulo);
			break;
			
		case 53: 
			echo consultarDxCie10( $q );
			break;
			
		case 54:
			crearEncabezadoKardexCerrar( $whis, $wing, $firmaDigital );
			break;
			
		case 55:	
			cargarProcedimientosTemporalADetalle( $his, $ing );
			break;
			
		case 56:
			echo actualizarImpMedicamentoHist($basedatos,$estado,$whis,$wing,$codigoArticulo,$wfecha,$ido);
			break;
			
		case 57:
			$wAjaxEditable = $weditable == 'on' ? true: false;
			salirsigrabar( $wemp_pmla, $whistoria, $wingreso, $wfechagrabacion, $tipoDocumento,$wcedula, $wAjaxEditable );
			break;
			
		default :
			break;
	}
	
}
?>