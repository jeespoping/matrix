<?php
/** ===========================================================================================|
 * TODO: WEBSERVICE PARA DESCARGAS DE SOPORTES
 * ============================================================================================|
 * * REPORTE					:	DESCARGAS DE SOPORTES
 * * AUTOR						:	Ing. Jaime David Mejia Quintero
 * * FECHA CREACIÓN				:	2021-05-28
 * * FECHA ULTIMA ACTUALIZACIÓN	:	2021-09-27
 * * DESCRIPCIÓN				:	Obtiene un json con los estados de las descargas:
 * * 									- estado.
 * * 									- descripcion.
 * * 									- data.

 * ============================================================================================|
 * TODO: ACTUALIZACIONES
 * ============================================================================================|
 * . @update [2021-05-28]	-	Se crea inicialmente el script junto con varios soportes para hacer automaticamente
 * . @update [2021-09-20]	-	Actualizacion para soporte MIPRES
 * . @update [2021-09-24]	-	Actualizacion para soporte Imex
 * . @update [2021-09-27]	-	Actualizacion para soporte Laboratorio
 */ 

/** Se inicializa el bufer de salida de php **/
ob_start();

/*
* Includes
*/

include_once("conex.php");
include_once("root/comun.php");
include_once("GeneradorSoportesPDF.php");


/** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
ob_end_clean();
$arrayRespuesta = [];
$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$whce =  consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wcliame =  consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');

/**
 * * Este metodo permite obtener un array con los diferentes estados de las descargas de los soportes
 *
 * @param conex[object]				[Conexión a la base de datos]
 * @param wemp_pmla[int]			[Código de empresa 01 = Clínica las Américas]
 * @param historia[int]				[Número de la historia médica del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]	        [Código de responsable]
 *
 * @return [array] Respuesta de base de datos
 */

function ConsultaEstadoWithPost($conex = null, $wemp_pmla, $jsonData)
{

    $patientList = json_decode($jsonData, true);
    $count = count($patientList);
    $arrayEstadoRespuesta = [];
    $arrayRespuestaSoportes = [];
    $consultaEstadoDescargas = [];
    $consultaArreglosHCEParametrizados = consultarAliasPorAplicacion($conex,$wemp_pmla,'soportesHCE');
    $arraySoportesHCEAConsultar = preg_split ("/\,/", $consultaArreglosHCEParametrizados);
    $consultaArreglosImexParametrizados = consultarAliasPorAplicacion($conex,$wemp_pmla,'soportesImex');
    $arraySoportesImexAConsultar = preg_split ("/\,/", $consultaArreglosImexParametrizados);

    for ($i = 0; $i < $count; $i++) {

        $historia = $patientList[$i]["history"];
        $ingreso =  $patientList[$i]["entry"];
        $responsable = $patientList[$i]["responsible"];
        $tipoDeDocumento = $patientList[$i]["documentType"];
        $documentoDeIdentidad = $patientList[$i]["document"];
        $fechadeAdmision = $patientList[$i]["admissionDate"];
        $factura = $patientList[$i]["invoiceNumber"];
        $fuenteDeFactura = $patientList[$i]["typeOfInvoice"];
        $array =  $patientList[$i]["supports"];
        
        $data = [];

        foreach ($array as $support) {
            array_push($data, $support);
        }
        
        if (array_search($soporte = "03", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "03", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];
            $rutaOrigen = $data[$posicion]['sopalm'];
            $ordenesMedicas = consultaOrdenMedica($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$url,$descripcion,
            $rutaOrigen);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($ordenesMedicas,$historia,$ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }
        
        if (array_search($soporte = "22", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "22", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $miPresMedsNoPbs = consultaMiPresMedsNoPbs($conex,$wemp_pmla,$historia,$ingreso,$responsable,$documentoDeIdentidad,
            $tipoDeDocumento,$fechadeAdmision,$soporte,$url,$descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($miPresMedsNoPbs, $historia, $ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "41", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "41", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];
            $rutaOrigen = $data[$posicion]['sopalm'];

            $furips = consultaFurips($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$factura,$url,$descripcion,$rutaOrigen);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($furips, $historia, $ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }
      
        if (array_search($soporte = "43", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "43", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $facturaPdf = consultaFacturaPDF($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$factura,$fuenteDeFactura,$url,
            $descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($facturaPdf, $historia, $ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "46", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "46", array_column($data,'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $detalleDeCargos = consultaDetalleDeCargos($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$factura,
            $fuenteDeFactura,$url,$descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($detalleDeCargos, $historia, $ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }
        
        if (array_search($soporte = "48", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "48", array_column($data,'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];
            $rutaOrigen = $data[$posicion]['sopalm'];
            $parametros = $data[$posicion]['sopopa'];
           
            $historiaClinica = consultaHistoriaClinica($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$url,$descripcion,
            $rutaOrigen,$parametros);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($historiaClinica, $historia, $ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "50", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "50", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];
            $rutaOrigen = $data[$posicion]['sopalm'];
            $datosAdicionales = $data[$posicion]['sopopa'];

            $registroAnestesia = consultaRegistroAnestesia($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$url,
            $descripcion,$rutaOrigen,$datosAdicionales);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($registroAnestesia, $historia, $ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }        

        if (array_search($soporte = "66", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "66", array_column($data,'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $detalleDeMateriales = consultaDetalleDeMateriales($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$factura,
            $fuenteDeFactura,$url,$descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($detalleDeMateriales,$historia,$ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "67", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "67", array_column($data,'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $hojaDeMedicamentos = ConsultaHojaDeMedicamentos($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$url,$descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($hojaDeMedicamentos,$historia,$ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "69", array_column($data,'code')) !== false) {
            $posicion = array_search($soporte = "69", array_column($data,'code'));
            $descripcion = $data[$posicion]['supportName'];

            $laboratorio = ConsultaLaboratorio($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($laboratorio,$historia,$ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }
        
        foreach($arraySoportesImexAConsultar as $soporteImex){
            if (array_search(in_array($soporteImex,  array_column($data,'code')), array_column($data,'code')) !== false) {
                $posicion = array_search($soporteImex, array_column($data,'code'));
                $concepto = $data[$posicion]['concept'];
                $descripcion = $data[$posicion]['supportName'];
                $ayuda = $data[$posicion]['sopcop'];

                $soportesImex = consultaSoportesImex($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporteImex,$concepto,$ayuda,
                $descripcion);
                $arrayRespuestaSoportes = devolverRespuestaSoporte($soportesImex,$historia,$ingreso);
                array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
            }            
        }
       
        foreach($arraySoportesHCEAConsultar as $soporteHCE){
            if (array_search(in_array($soporteHCE,  array_column($data,'code')), array_column($data,'code')) !== false) {
                $posicion = array_search($soporteHCE, array_column($data,'code'));
                $url = $data[$posicion]['url'];
                $descripcion = $data[$posicion]['supportName'];
                $formulario = $data[$posicion]['soptab'];
                $rutaOrigen = $data[$posicion]['sopalm'];
                $datosAdicionales = $data[$posicion]['sopopa'];

                $soportesHCE = consultaSoportesHCE($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporteHCE,$descripcion,
                $formulario,$url,$rutaOrigen,$datosAdicionales);
                $arrayRespuestaSoportes = devolverRespuestaSoporte($soportesHCE,$historia,$ingreso);
                array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
            }
        }
    }

    $estadoTemporal = [];
    foreach ($arrayEstadoRespuesta as $estados) {
        array_push($estadoTemporal,$estados['status']);
    }

    if(in_array(300,$estadoTemporal,true) || in_array(400,$estadoTemporal,true) || in_array(500,$estadoTemporal,true)){
        $consultaEstadoDescargas['status'] = '400';
        $arrayRespuestaSoportes['responsable'] = $responsable;
        $consultaEstadoDescargas['result'] = $arrayEstadoRespuesta;
    } 
    else {
        $consultaEstadoDescargas['status'] = '200';
        $arrayRespuestaSoportes['responsable'] = $responsable;
        $consultaEstadoDescargas['result'] = $arrayEstadoRespuesta;
    }
    return $consultaEstadoDescargas;
}

// Esta funcion se usa para hacer pruebas con GET
function ConsultaEstadoWithGet(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $codigo_responsable,
    $documentoDeIdentidad = null,
    $tipoDeDocumento = null,
    $fechadeAdmision = null,
    $factura = null,
    $fuenteDeFactura = null,
    $descripcion = null,
    $formulario = null,
    $concepto = null,
    $soportes
){
    $arrayEstadoRespuesta = [];
    $arrayRespuestaSoportes = [];
    $consultaEstadoDescargas = [];
    $consultaArreglosHCEParametrizados = consultarAliasPorAplicacion($conex,$wemp_pmla,'soportesHCE');
    $arraySoportesHCEAConsultar = preg_split ("/\,/",$consultaArreglosHCEParametrizados);
    $consultaArreglosImexParametrizados = consultarAliasPorAplicacion($conex,$wemp_pmla,'soportesImex');
    $arraySoportesImexAConsultar = preg_split ("/\,/", $consultaArreglosImexParametrizados);
    $arraySoportes = array();
    array_push($arraySoportes,$soportes);

    if (array_search($soporte = '03', $arraySoportes) !== false) {
        $ordenesMedicas = consultaOrdenMedica($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,
        'http://matrix.lasamericas.com.co/matrix/hce/procesos/ordenes_imp.php?','orden medica',
        '/var/www/matrix/hce/procesos/impresion_ordenes/');
        $arrayRespuestaSoportes = devolverRespuestaSoporte($ordenesMedicas,$historia,$ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    if (array_search($soporte = '22', $arraySoportes) !== false) {
        $miPresMedsNoPbs = consultaMiPresMedsNoPbs($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$documentoDeIdentidad,
        $tipoDeDocumento,$fechadeAdmision,$soporte,'http://matrix.lasamericas.com.co/matrix/hce/procesos/CTCmipres.php?','Mi Pres');
        $arrayRespuestaSoportes = devolverRespuestaSoporte($miPresMedsNoPbs,$historia,$ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    if (array_search($soporte = '41', $arraySoportes) !== false) {
        $url = 'http://matrix.lasamericas.com.co/matrix/ips/reportes/formato_furips_x_fac.php?';
        $descripcion = 'Furips';
        $furips = consultaFurips($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,$factura,$url,$descripcion,
        '/var/www/matrix/ips/reportes/facturas/');
        $arrayRespuestaSoportes = devolverRespuestaSoporte($furips,$historia,$ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    if (array_search($soporte = '43', $arraySoportes) !== false) {
        $url = 'http://matrix.lasamericas.com.co/matrix/ips/procesos/monitorE-facturacion.php?';
        $descripcion = 'Factura PDF';
        $facturaPdf = consultaFacturaPDF($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,$factura,$fuenteDeFactura,$url,
        $descripcion);
        $arrayRespuestaSoportes = devolverRespuestaSoporte($facturaPdf,$historia,$ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    if (array_search($soporte = '46', $arraySoportes) !== false) {
        $descripcion = 'Detalle de cargos';
        $url = 'http://matrix.lasamericas.com.co/matrix/facturacion/reportes/rep_detafactuayuda.php?';
        $detalleDeCargos = consultaDetalleDeCargos($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,$factura,$fuenteDeFactura,
        $url,$descripcion);
        $arrayRespuestaSoportes = devolverRespuestaSoporte($detalleDeCargos,$historia,$ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    if (array_search($soporte = '48', $arraySoportes) !== false) {
        $historiaClinica = consultaHistoriaClinica($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,
        'http://matrix.lasamericas.com.co/matrix/hce/procesos/solimp.php?','Historia Clinica','../../hce/reportes/cenimp/');
        $arrayRespuestaSoportes = devolverRespuestaSoporte($historiaClinica,$historia,$ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    if (array_search($soporte = '50', $arraySoportes) !== false) {
        $historiaClinica = consultaRegistroAnestesia($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,
        'http://matrix.lasamericas.com.co/matrix/hce/procesos/solimp.php?','Registro de anestesia','../../hce/reportes/cenimp/');
        $arrayRespuestaSoportes = devolverRespuestaSoporte($historiaClinica,$historia,$ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    if (array_search($soporte = '66', $arraySoportes) !== false) {
        //$url = 'localhost/matrix/facturacion/reportes/rep_detafactu.php?';
        $url = 'http://matrix.lasamericas.com.co/matrix/facturacion/reportes/rep_detafactu.php?';
        $descripcion = 'Detalle de materiales';        
        $detalleDeMateriales = consultaDetalleDeMateriales($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,$factura,
        $fuenteDeFactura,$url,$descripcion);
        $arrayRespuestaSoportes = devolverRespuestaSoporte($detalleDeMateriales, $historia, $ingreso);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '67', $arraySoportes) !== false) {
        //$url = 'http://matrix-test.lasamericas.com.co/matrix/movhos/reportes/Hoja_medicamentos_enfermeria_IPODS.php?';
        $url = 'http://matrix.lasamericas.com.co/matrix/movhos/reportes/Hoja_medicamentos_enfermeria_IPODS.php?';
        $descripcion = 'Detalle de medicamentos';        
        $hojaDeMedicamentos = ConsultaHojaDeMedicamentos($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,$url,$descripcion);
        $arrayRespuestaSoportes = devolverRespuestaSoporte($hojaDeMedicamentos, $historia, $ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    if (array_search($soporte = '69', $arraySoportes) !== false) {
        $descripcion = 'laboratorio';
        $laboratorio = ConsultaLaboratorio($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporte,$descripcion);
        $arrayRespuestaSoportes = devolverRespuestaSoporte($laboratorio,$historia,$ingreso);
        array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
    }

    foreach($arraySoportesImexAConsultar as $soporteImex){
        if (array_search($soporteImex, $arraySoportes) !== false) {
            $ayuda = '0700';
            $soportesImex = consultaSoportesImex($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporteImex,$concepto,$ayuda,
            $descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($soportesImex,$historia,$ingreso);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }            
    }

    foreach($arraySoportesHCEAConsultar as $soporteHCE){
        if (array_search($soporteHCE, $arraySoportes) !== false) {
            $url = 'http://matrix.lasamericas.com.co/matrix/hce/procesos/solimp.php?';
            $soportesHCE = consultaSoportesHCE($conex,$wemp_pmla,$historia,$ingreso,$codigo_responsable,$soporteHCE,$descripcion,$formulario,
            $url,'../../hce/reportes/cenimp/');
            $arrayRespuestaSoportes = devolverRespuestaSoporte($soportesHCE,$historia,$ingreso);
            array_push($arrayEstadoRespuesta,$arrayRespuestaSoportes);
        }
    }

    //Luego de unificado todos los soportes se saca la respuesta general del webservice
    $estadoTemporal = [];

    foreach ($arrayEstadoRespuesta as $estados) {
        array_push($estadoTemporal, $estados['status']);
    }
    if (in_array(300,$estadoTemporal,true) || in_array(400,$estadoTemporal,true) || in_array(500,$estadoTemporal,true)) {
        $consultaEstadoDescargas['status'] = 400;
        $consultaEstadoDescargas['responsable'] = $codigo_responsable;
        $consultaEstadoDescargas['result'] = $arrayEstadoRespuesta;
    } else {
        $consultaEstadoDescargas['status'] = 200;
        $consultaEstadoDescargas['responsable'] = $codigo_responsable;
        $consultaEstadoDescargas['result'] = $arrayEstadoRespuesta;
    }

    return $consultaEstadoDescargas;
}

function ConsultaErronea()
{
    $consultaEstadoDescargas['status'] = '400';
    $consultaEstadoDescargas['result'] = 'existe un error en la URL, favor verificar';
    return $consultaEstadoDescargas;
}

/***** COMIENZO FUNCIONES GENERALES DEL WEBSERVICE */

/**
 * Este metodo permite validar si se ingreso la conexion y el wemp_pmla
 *
 * @param conex[object]		[Conexion a matrix]
 * @param wemp_pmla[int]		[empresa conectada]
 * @return [json] Respuesta del servicio
 */

function validarIngreso(
    $conex = null,
    $wemp_pmla
) {
    if (!isset($conex)) {
        return array(
            'state'            =>    404,
            'descripcion'    =>    'Falta conexión a base de datos.'
        );
    }

    if (!isset($wemp_pmla)) {
        return array(
            'state'            =>    404,
            'descripcion'    =>    'Falta el campo código de empresa, es obligatorio.'
        );
    }
}

/**
 * Este metodo permite obtener la url para armar la historia clinica
 * recibiendo como parametro una historia y un ingreso
 *
 * @param wemp_pmla[int]		[empresa conectada]]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @return [string] Respuesta del servicio
 */

function armarURLHistoriaClinica(
    $wemp_pmla,
    $historia,
    $ingreso,
    $url
){

    // Se arma la estructura que va a llamar a la URL
    $urlWemp_pmla = "wemp_pmla=" . $wemp_pmla;
    $urlHistoria = "&whis=" . $historia;
    $urlIngreso = "&wing=" . $ingreso;
    $adicionales = "&wservicio=*";

    return $url . $urlWemp_pmla . $urlHistoria . $urlIngreso . $adicionales . '&automatizacion_pdfs=';
}

/**
 * Este metodo permite generar las opciones de un CURL para los soportes que se hacen por POST
 * recibe una variable que es general para todos los oportes del HCE
 *
 * @param url[string]	[array de los datos enviados al POST]
 * @param data[array]	[array de los datos enviados al POST]
 * @return [array] Respuesta del servicio
 */

function cargarOpcionesPOST($url,$data)
{

    $options = array(
        CURLOPT_URL                 => $url."&automatizacion_pdfs=",
        CURLOPT_HEADER              => false,
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_POSTFIELDS          => $data,
        CURLOPT_CUSTOMREQUEST       => 'POST',
    );

    return $options;
}

function validarFactura($factura,$descripcion){
    if (is_null($factura) || $factura === "N/A") {
        $pdf_generado['status'] = '400';
        $pdf_generado['result'] = 'La historia-ingreso no posee factura generada para el soporte'. $descripcion;
        return $pdf_generado;
    }
}

/**
 * Este metodo es el encargado de llamar a un webservice para guardar la respuesta de los soportes
 * 
 * @param response[string]		[Respuesta dada por el consumo del curl]
 * @param wemp_pmla[int]		[empresa conectada]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param soporte[int]				[Número del soporte a generar PDF]
 * @param descripcion[int]				[Descripcion del soporte a generar PDF]
 * @return [json] Respuesta del servicio
 */

function guardarRespuesta($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte, $respuesta, $codError, $descripcion){

    $codError = !empty($codError) ? 'ERR18' : ($respuesta['status']!= '200' ? 'ERR17' : null);
		
	// Crear un manejador cURL
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }

    $data = array(  
		'history' => $historia,
		'entryNumber' => $ingreso,
		'companyCode' => $responsable,
		'support' => $soporte,
		'type' => 'R',
		'process' => json_encode($respuesta),
		'auditAction' => '15',
		'dateProcess' => date('Y-m-d'),
		'hourProcess' => date('h:i'),
		'httpCode' => $respuesta['status'],
		'errorCode' => $codError,
		'description' => $descripcion
    );

	$urlAConsumir = consultarAliasPorAplicacion($conex,$wemp_pmla,'urlSoportesAutomaticos');
		
	$opciones = array(
        CURLOPT_URL                 => $urlAConsumir,
        CURLOPT_HEADER              => false,
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_POSTFIELDS          => $data,
        CURLOPT_CUSTOMREQUEST       => 'POST',
    );      

    curl_setopt_array($ch, $opciones);
    $respuesta = curl_exec($ch);
    echo $respuesta;
	curl_close($ch);

}

/**
 * Este metodo permite armar el PDF y luego descargarlo
 * recibiendo como parametro respuesta del curl,conexion del curl,wemp_pmla,historia
 * ingreso,numero de soporte,descripcion del soporte
 * 
 * @param response[string]		[Respuesta dada por el consumo del curl]
 * @param ch[int]		[conexion del curl para manejo de excepciones]
 * @param wemp_pmla[int]		[empresa conectada]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param soporte[int]				[Número del soporte a generar PDF]
 * @param descripcion[int]				[Descripcion del soporte a generar PDF]
 * @return [json] Respuesta del servicio
 */

function armarPDFHistoriaClinica(
    $response,
    $wemp_pmla,
    $historia,
    $ingreso,
    $soporte,
    $descripcion,
    $responsable,
    $rutaOrigen,
    $conex
) {

    $porciones = explode("|", $response);
    $wcodigo_solicitud = end($porciones);
    $cadena_buscada   = 'OK';
    $posicion_coincidencia = strpos($response, $cadena_buscada);
    
    if ($posicion_coincidencia !== false) {
        $wnombrePDF = $wemp_pmla . "Solicitud_" . $wcodigo_solicitud;
        $archivoOrigen = $rutaOrigen. $wnombrePDF . ".pdf";
        recuperarSoporte($historia, $ingreso, $responsable, $wemp_pmla, $soporte,$archivoOrigen);     
    }
    
    return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex,null);
}

/**
 * Este metodo permite obtener para un arreglo de forma global(para todos los soportes)
 * el estado y la descripcion del resultado generado por los servicios que tienen cada
 * uno de los soportes.
 * recibe un json con la respuesta de cada uno de los soportes
 *
 * @param respuesta[json]  [Respuesta del servicio de cada uno de los soportes]
 * @return json[json] Respuesta del servicio y una descripcion
 */

function devolverRespuestaSoporte($respuesta, $historia, $ingreso)
{
    if ($respuesta['status'] == 200){
        $arrayRespuestaSoportes['status'] = $respuesta['status'];
        $arrayRespuestaSoportes['descripcion'] = $respuesta['result'];
        $arrayRespuestaSoportes['paciente'] = $historia . '-' . $ingreso;
    } 
    else{
        $arrayRespuestaSoportes['status'] = 400;
        $arrayRespuestaSoportes['descripcion'] = $respuesta['result'];
        $arrayRespuestaSoportes['paciente'] = $historia . '-' . $ingreso;
    }
    return $arrayRespuestaSoportes;
}

/**
 * Este metodo permite obtener los valores de los checkbox para la historia clinica
 * con el objetivo de marcar automaticamente dichos checkbox y generar PDF segun esos checkbox marcados
 *
 * @param dom[string]  [HTML devuelto por la consulta de historia clinica]
 * @param wemp_pmla[int]  [Empresa conectada]
 * @param codigoFormularioArbol[string]  [Codigo del checkbox a marcar]

 * @return json[json] Respuesta del servicio y una descripcion
 */

function cargarDom($dom,$wemp_pmla,$codigoFormularioArbol)
{

    if (empty($dom->getElementById('fecha_inicial')) || empty($dom->getElementById('fecha_final'))){
        $pdf_generado['status'] = '400';
        $pdf_generado['result'] = 'Ha ocurrido un error inesperado';
        return $pdf_generado;
    }

    $formularios         = "";
    $historia            = $dom->getElementById('whis')->getAttributeNode('value')->nodeValue;
    $ingreso             = $dom->getElementById('wing')->getAttributeNode('value')->nodeValue;
    $centroCostos        = $dom->getElementById('wcco')->getAttributeNode('value')->nodeValue;
    $wSolicitaCenimp     = $dom->getElementById('wSolicitaCenimp')->getAttributeNode('value')->nodeValue;
    $monitor             = $dom->getElementById('wmonitor')->getAttributeNode('value')->nodeValue;

    $fecha_i             = $dom->getElementById('fecha_inicial')->getAttributeNode('value')->nodeValue;
    $fecha_f             = $dom->getElementById('fecha_final')->getAttributeNode('value')->nodeValue;
    $weditar             = $dom->getElementById('weditar')->getAttributeNode('value')->nodeValue;
    $widenti             = $dom->getElementById('widenti')->getAttributeNode('value')->nodeValue;
    $wespecial           = "TODAS";
    $fechaIni_def        = "";
    $fechaFin_def        = "";
    $formulariosElegidos = "";
    $htmlProgramasAnexos = "";

    /* OTRAS VARIABLES IMPORTANTES */

    $wcenimp          = $dom->getElementById('wcenimp')->getAttributeNode('value')->nodeValue;
    $whcebasedato      = $dom->getElementById('whcebasedato')->getAttributeNode('value')->nodeValue;
    $wmovhos           = $dom->getElementById('wmovhos')->getAttributeNode('value')->nodeValue;
    $wmodalidad        = $dom->getElementById('wmodalidad')->getAttributeNode('value')->nodeValue;
    $wincluyeLogo      = $dom->getElementById('wincluyeLogo')->getAttributeNode('value')->nodeValue;
    $wincluyeTapa      = $dom->getElementById('wincluyeTapa')->getAttributeNode('value')->nodeValue;
    $wimpresionDirecta = $dom->getElementById('wimpresionDirecta')->getAttributeNode('value')->nodeValue;
    $wenviaEmail       = $dom->getElementById('wenviaEmail')->getAttributeNode('value')->nodeValue;

    $bloquear = 'no';

    if ($bloquear == "no") {
        $fechaIni_def = $dom->getElementById('wfecini_def')->getAttributeNode('value')->nodeValue;
        $fechaFin_def = $dom->getElementById('wfecfin_def')->getAttributeNode('value')->nodeValue;
    }

    $hayFormularios = false;

    $j = 0;
    //aqui se verifica si estan todos checkeados o no para la bd

    $inputs = $dom->getElementById("div_arbol_impresion")->getElementsByTagName("input");

    /*if(empty($codigoFormularioArbol)){
        if (
            $input->hasAttribute('class') &&
            $input->getAttributeNode('class')->nodeValue == 'formulario_arbol_impresion' &&
            $input->hasAttribute('type') &&
            $input->getAttributeNode('type')->nodeValue == 'checkbox' &&
            $input->hasAttribute('checked')
        ) {
            $value = $input->getAttributeNode('value')->nodeValue;
            
    }*/

    //primero = 000075 , segundo = 000150, tercero = 000334
    foreach ($codigoFormularioArbol as $codigo) {
        foreach ($inputs as $input) {
            if (
                $input->hasAttribute('class') &&
                $input->getAttributeNode('class')->nodeValue == 'formulario_arbol_impresion' &&
                $input->hasAttribute('type') &&
                $input->getAttributeNode('type')->nodeValue == 'checkbox' &&
                $input->getAttributeNode('value')->nodeValue === $codigo
            ) {
                $value = $input->getAttributeNode('value')->nodeValue;
                if ($j > 0) {
                    $formularios .= ',';
                }
                $formularios .= $value;
                $j++;
            }
        }
    }

    if ($j > 0) {
        $hayFormularios = true;
    }

    $array_paquetes = array();
    $datos = json_encode(['formularios_arbol' => $formularios, 'paquetes' => $array_paquetes]);
    if ($codigo == '000075') {
        $fecha_i = (strtotime($fecha_i . "- 3 days"));
    }

    $data = array(
        'wemp_pmla'                     => $wemp_pmla,
        'fecha_i'                       => $fecha_i,
        'fecha_f'                       => $fecha_f,
        'fecIngreso'                    => $fechaIni_def,
        'fecEgreso'                        => $fechaFin_def,
        'monitor'                        => $monitor,
        'datos'                            => $datos,
        'historia'                        => $historia,
        'ingreso'                        => $ingreso,
        'wcco'                            => $centroCostos,
        'wSolicitaCenimp'               => $wSolicitaCenimp,
        'wenviaEmail'                    => $wenviaEmail,
        'wimpresionDirecta'                => $wimpresionDirecta,
        'action'                         => 'guardarSolicitud',
        'consultaAjax'                    => '',
        'wcenimp'                        => $wcenimp,
        'wmovhos'                        => $wmovhos,
        'whcebasedato'                    => $whcebasedato,
        'wmodalidad'                    => $wmodalidad,
        'wlogo'                         => $wincluyeLogo,
        'wtapa'                            => $wincluyeTapa,
        'weditar'                        => $weditar,
        'widenti'                        => $widenti,
        'wespecial'                     => $wespecial,
        'tipoPeticion'                    => "normal",
        'htmlProgramasAnexos'            => $htmlProgramasAnexos,
        'formulariosElegidos'            => $formulariosElegidos    
    );

    return $data;
}

/**
 * Este metodo permite la creacion de las carpetas donde se van a guardar los soportes
 * recibe la empresa,historia,ingreso y responsable
 *
 * @param wemppmla[array]	    [Numero de la empresa]
 * @param historia[array]	    [historia del paciente]
 * @param ingreso[array]	    [ingreso del paciente]
 * @param responsable[array]	[entidad responsable del paciente]
 * @return [string] Retorna un string con la ubicacion relativa de la carpeta
 */

function creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable)
{

    //CARPETA PRINCIPAL
    $carpeta_principal = 'soportes';
    if (!file_exists($carpeta_principal)) {
        mkdir($carpeta_principal, 0777, true);
    }

    //CARPETA PARA GUARDAR POR EMPRESA
    $carpetaEmpresa = $carpeta_principal . '/' . $wemp_pmla;
    if (!file_exists($carpetaEmpresa)) {
        mkdir($carpetaEmpresa, 0777, true);
    }

    //CREAR CARPETA PARA GUARDAR SOPORTES
    $carpeta = $carpetaEmpresa . '/' . $historia . '-' . $ingreso;
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    //CARPETA PARA GUARDAR POR RESPONSABLE
    $carpetaResponsable = $carpeta  . '/' . $responsable;
    if (!file_exists($carpetaResponsable)) {
        mkdir($carpetaResponsable, 0777, true);
    }

    return $carpetaResponsable;
}

/**
 * Este metodo permite la recuperacion del PDF desde el archivo del origen y enviarlo a la carpeta
 *  de respuesta(carpeta de soporte automaticos)
 * recibe la empresa,historia,ingreso y responsable
 *
 * @param wemppmla[array]	    [Numero de la empresa]
 * @param historia[array]	    [historia del paciente]
 * @param ingreso[array]	    [ingreso del paciente]
 * @param responsable[array]	[entidad responsable del paciente]
 * @param soporte[array]	    [numero del soporte]
 * @param archivoOrigen[array]	[ruta inicial de donde se ubica el soporte]
 * @return [string] Retorna un string con la ubicacion relativa de la carpeta
 */
function recuperarSoporte($historia,$ingreso,$responsable,$wemp_pmla,$soporte,$archivoOrigen){

    if (file_exists($archivoOrigen)) {
        $nombreSoporte = $historia . '-' . $ingreso . '-' . $soporte . '.pdf';
        $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
        $archivoDestino = $carpetaResponsable . '/' . $nombreSoporte ;
        copy($archivoOrigen, $archivoDestino);
        unlink($archivoOrigen);
    }    
}

/**
 * Este metodo permite obtener los estados de radicados de facturas
 * recibiendo como parametro un número de documento o listado de
 * números de documentos de los pacientes.
 *
 * @param estructura[object]		[Html a generar]
 * @param wnombrePDF[object]		[Nombre del PDF]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param hoja[int]					[Parametro adicional para decir que numero de PDF es]
 * @return [html] Respuesta html
 */

function generarPDF($conex,$estructura, $wnombrePDF, $historia, $ingreso, $descripcion, $wemp_pmla, $responsable, $hoja = null, $soporte)
{
    $codError = '';
	$descError = '';
	try{
		$decode_pdf = html_entity_decode($estructura);
		// ************* CUERPO *************
		$html = '<!DOCTYPE html>
					<head></head>
					<body style="border:0; margin:2px;">
						' . $decode_pdf . '
					</body>
				</html>
				<br>';

		$carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);

		//CREAR UN ARCHIVO .HTML CON EL CONTENIDO CREADO
		$archivo_dir = $wnombrePDF . '-' . $hoja . ".html";
        echo 'el nombre generado del archivo fue'.$archivo_dir;

		if (file_exists($archivo_dir)) {
			unlink($archivo_dir);
		}

		$f = fopen($archivo_dir, "w+");
		fwrite($f, $html);
		fclose($f);

		shell_exec("./generarPdfSoportes.sh \"" . $wnombrePDF . '-' . $hoja . '"');
		$archivo_origen = $wnombrePDF . '-' . $hoja . '.pdf';
        echo 'el nombre del pdf generado fue'.$archivo_origen;

		if ($hoja == null) {
			$archivo_destino = $carpetaResponsable . '/' . $wnombrePDF . ".pdf";
		} else {
			$archivo_destino = $carpetaResponsable . '/' . $wnombrePDF . '-' . $hoja . ".pdf";
		}

		if (file_exists($archivo_origen)) {
			copy($archivo_origen, $archivo_destino);
			unlink($archivo_origen);
		}

		if (file_exists($archivo_dir)) {
			unlink($archivo_dir);
		}
		if (file_exists($archivo_destino)) {
			$array_pdf_respuesta['status'] = 200;
			$array_pdf_respuesta['result'] = 'se ha cargado el soporte correctamente:' . ' ' . $descripcion;
		} else {
			$pdf_generado['status'] = 400;
			$pdf_generado['result'] = 'Ha ocurrido un error en el soporte:' . ' ' . $descripcion;
		}

        guardarRespuesta($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte, $pdf_generado, $codError, $descripcion);
		return $array_pdf_respuesta;

	} catch (Exception $e) {
		$codError = $e->getCode();
		$descError = $e->getMessage();
        guardarRespuesta($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte, $pdf_generado, $codError, $descError);
	}
}

/**
 * Este metodo permite la verificacion de los documentos se suban correctamente a la carpeta de intercambio
 * En caso que no se suba, debe de botar un error
 * recibe la respuesta del servicio, el curl, descripcion del soporte, empresa wemppmla,historia del paciente
 * ingreso del paciente, responsable del paciente, numero del soporte y una "hoja" en caso que el soporte tenga
 * mas de un PDF
 *
 * @param response[array]	    [Respuesta del servicio]
 * @param ch[array]	            [Curl del servicio]
 * @param descripcion[array]	[Nombre del soporte]
 * @param wemppmla[array]	    [Numero de la empresa]
 * @param historia[array]	    [historia del paciente]
 * @param ingreso[array]	    [ingreso del paciente]
 * @param responsable[array]	[entidad responsable del paciente]
 * @param soporte[array]	    [Numero del soporte registrado]
 * @param hoja[array]	        [Numero de la hoja]
 * @return [string] Retorna un string con la ubicacion relativa de la carpeta
 */

function armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, $hoja = null,$conex = null,$codError = null)
{
    //Verificamos resultado de la consulta CURL
    $rutaPrincipal = consultarAliasPorAplicacion($conex, $wemp_pmla, 'documentosAutomatizacion');
    $rutaHistoria = $wemp_pmla . '/' . $historia . '-' . $ingreso;

    if ($hoja == null) {
        $nombrePDFautomatizacion = $historia . '-' . $ingreso . '-' . $soporte . '.pdf';
    } 
    else {
        $nombrePDFautomatizacion = $historia . '-' . $ingreso . '-' . $soporte . '-' . '1' . '.pdf';
    }
   
    $rutaArchivoGuardado = $rutaPrincipal . $rutaHistoria . '/' . $responsable . '/' . $nombrePDFautomatizacion;

    if (file_exists($rutaArchivoGuardado)) {
        $pdf_generado['status'] = 200;
        $pdf_generado['result'] = 'Transferencia completa del soporte :' . ' ' . $descripcion;
    } 
    else {
        $pdf_generado['status'] = 400;
        $pdf_generado['result'] = 'Ha ocurrido un error en el soporte:' . ' ' . $descripcion;
    }

    guardarRespuesta($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte, $pdf_generado, $codError, $descripcion);
    return $pdf_generado;
}

/***** FIN DE FUNCIONES GENERALES DEL WEBSERVICE */

/***** COMIENZO DE LA FUNCION DE CADA UNO DE LOS SOPORTES */

/**
 * Este metodo permite obtener el soporte automatico de la orden medica
 * recibiendo como parametro la historia y el ingreso
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 *
 * @return [html] Respuesta html
 */

function consultaOrdenMedica(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $url,
    $descripcion,
    $rutaOrigen
) {
    global $wbasedato;
	global $whce;

    $codError = '';
	try{
   //se valida credenciales
    validarIngreso($conex, $wemp_pmla);

    //Creacion del array para saber si tiene medicamentos o no
    $examenesAImprimir = array();
    //se hace el sql para decir si tiene mediamentos o no
    $sqlContarOrdMedic = "SELECT count(0) AS cantidad 
    FROM " . $wbasedato . "_000054 mov 
    WHERE mov.Kadhis = '" . $historia . "'
    AND mov.Kading = '" . $ingreso . "';";

    $ordMedicamentos = mysql_query($sqlContarOrdMedic, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerTiposOrden):</b><br>" . mysql_error());
    $ordMedicamentos = mysql_fetch_assoc($ordMedicamentos);

    if ($ordMedicamentos['cantidad'] > 0) {
        array_push($examenesAImprimir, 'medtos');
    }

    //Se hace el sql para buscar las cantidades de opciones que debe de imprimirse
    $sqlObtenerTiposOrden = "SELECT DISTINCT hcq.Codigo, hcq.Descripcion 
	FROM ".$whce."_000015 hcq
	INNER JOIN ".$whce."_000027  hcv on hcq.Codigo = hcv.Ordtor
    WHERE Ordhis = '" . $historia . "'
    AND Ording = '" . $ingreso . "'
    ORDER BY 2 ASC;";

    $tiposOrdenes = mysql_query($sqlObtenerTiposOrden, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerTiposOrden):</b><br>" . mysql_error());

    while ($rowTiposOrdenes = mysql_fetch_array($tiposOrdenes)) {
        array_push($examenesAImprimir, $rowTiposOrdenes["Codigo"]);
    }

    $separado = implode(",", $examenesAImprimir);

    // Se arma la estructura que va a llamar a la URL
    
    $urlWemp_pmla = "wemp_pmla=" . $wemp_pmla;
    $urlHistoria = "&whistoria=" . $historia;
    $urlIngreso = "&wingreso=" . $ingreso;
    $adicionales = '&tipoimp=imp&alt=off&pacEps=off&wtodos_ordenes=on&orden=asc&origen=' . '&arrOrden=' . $separado . '&desdeImpOrden=on';
    $urlOrdenMedica = $url . $urlWemp_pmla . $urlHistoria . $urlIngreso  . $adicionales .  "&soporte=" . $soporte . '&automatizacion_pdfs=';
    
    //inicializar url
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }

    // opciones del cURL
    curl_setopt($ch, CURLOPT_URL, $urlOrdenMedica);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_exec($ch);
    curl_close($ch);

    $archivoOrigen = $rutaOrigen . $historia . '-' . $ingreso . '-' . $soporte . '.pdf';
    recuperarSoporte($historia, $ingreso, $responsable, $wemp_pmla, $soporte, $archivoOrigen);

	} catch (Exception $e) {
		$codError = $e->getCode();
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex,$codError);
	}

	return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex);
}

/**
 * Este metodo permite obtener el soporte automatico de la consulta de MiPrescripcion
 * recibiendo como parametro la historia,el ingreso y el responsable
 * Adicionalmente para MIPRES se pide el documento y el tipo de documento para consultar al web-service
 * del gobierno
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 * @param documentoDeIdentidad[int]	[Número de documento del paciente]
 * @param tipoDeDocumento[int]		[Tipo de documento del paciente]
 *
 * @return [html] Respuesta html
 */

function consultaMiPresMedsNoPbs(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $documentoDeIdentidad,
    $tipoDeDocumento,
    $fechadeAdmision,
    $soporte,
    $url,
    $descripcion
) {
    
    //$documentoDeIdentidad = '32555985';
    //$tipoDeDocumento = 'CC';
    //$fechadeAdmision = '2021-05-28'; //año-mes-dia
    
	$codError = '';
	try{
    //se valida credenciales
    validarIngreso($conex, $wemp_pmla);

    // Crear un manejador cURL
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }

    if (!empty($tipoDeDocumento && $documentoDeIdentidad)) {
        $data = array(
            'consultaAjax'                  => '',
            'accion'                        => 'pintarReportePrescripcionMipres',
            'wemp_pmla'                     => $wemp_pmla,
            'historia'                      => $historia,
            'ingreso'                       => $ingreso,
            'responsable'                   => $responsable,
            'fechaInicial'                  => $fechadeAdmision,
            'fechaFinal'                    => date("Y-m-d"),
            'tipDocPac'                     => $tipoDeDocumento,
            'docPac'                        => $documentoDeIdentidad,
            'tipDocMed'                     => '',
            'docMed'                        => '',
            'codEps'                        => '',
            'tipoPrescrip'                  => '',
            'nroPrescripcion'               => '',
            'filtroMipres'                  => '',
            'ambitoAtencion'                => ''
        );
        $url = $url . "&soporte=" . $soporte ;
        $opciones = cargarOpcionesPOST($url,$data);        
        curl_setopt_array($ch,$opciones);
        curl_exec($ch);
        curl_close($ch);
    }

    } catch (Exception $e) {
        $codError = $e->getCode();
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex,$codError);
    }
    return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,'1',$conex);
}

/**
 * Este metodo permite obtener el soporte automatico de la consulta furips
 * recibiendo como parametro la historia, el ingreso y el responsable
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 *
 * @return [html] Respuesta html
 */

function consultaFurips(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $factura = null,
    $urlFurips,
    $descripcion,
    $rutaOrigen
) {
	$codError = '';
	try{
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);
        
        //Se valida que venga la factura y no sea NULL
        $validarFactura = validarFactura($factura,$descripcion);
        
        if($validarFactura['status'] != '400'){
            //se inicia el curl
            $ch = curl_init();
            $data = array(
                'wemp_pmla'                     => $wemp_pmla,
                'wnumero'                       => $factura,
                'aceptar'                       => 'Aceptar',
                'wgrupo'                        => '0',
                'bandera'                       => '1',
            );

            $opciones = cargarOpcionesPOST($urlFurips,$data);    
            curl_setopt_array($ch, $opciones);
            curl_exec($ch);
            curl_close($ch);

            $archivoOrigen = $rutaOrigen . $factura . '.pdf';
            recuperarSoporte($historia, $ingreso, $responsable, $wemp_pmla, $soporte,$archivoOrigen);
        }

    } catch (Exception $e) {
		$codError = $e->getCode();
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex,$codError);
	}

	return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex);
}

/**
 * Este metodo permite obtener el soporte automatico de la consulta facturaPDF
 * recibiendo como parametro la historia, el ingreso y el responsable
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 *
 * @return [html] Respuesta html
 */

function consultaFacturaPDF(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $factura,
    $fuenteDeFactura,
    $url,
    $descripcion
) {

    $codError = '';
	try{
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);

        //Se valida que venga la factura y no sea NULL
        $validarFactura = validarFactura($factura,$descripcion);
        
        if($validarFactura['status'] != '400'){
            // Crear un manejador cURL
            $ch = curl_init();
            if (!$ch) {
                die("Couldn't initialize a cURL handle");
            }

            $data = array(
                'wemp_pmla'                    => $wemp_pmla,
                'accion'                       => 'descargarRepGrafica',
                'fuente'                       => $fuenteDeFactura,
                'documento'                    => $factura,
            );

            $UrlFacturaPDF = $url."historia=" . $historia . "&ingreso=" . $ingreso . "&responsable=" . $responsable . "&soporte=" . $soporte ;
            
            $opciones = cargarOpcionesPOST($UrlFacturaPDF,$data);       
            curl_setopt_array($ch, $opciones);
            curl_exec($ch);
            curl_close($ch);
        }

	} catch (Exception $e) {
		$codError = $e->getCode();
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex,$codError);
	}

    return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex);
}

/**
 * Este metodo permite obtener el soporte automatico de la consulta detalle de cargos
 * recibiendo como parametro la historia, el ingreso y el responsable
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 *
 * @return [html] Respuesta html
 */

function consultaDetalleDeCargos(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $factura,
    $fuenteDeFactura,
    $UrlDetalleDeCargos,
    $descripcion
) {
	$codError = '';
	try{    
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);

        //Se valida que venga la factura y no sea NULL
        $validarFactura = validarFactura($factura,$descripcion);
        
        if($validarFactura['status'] != '400'){
            //Se inicia curl
            $ch = curl_init();

            $data = array(
                'usuario'                => $wemp_pmla,
                'fte'                    => $fuenteDeFactura,
                'fac'                    => $factura,
                'com'                    => 'a',
            );
    
            $opciones = cargarOpcionesPOST($UrlDetalleDeCargos,$data);    
            curl_setopt_array($ch, $opciones);
            $response = curl_exec($ch);
            curl_close($ch);
    
            $facturaEnSaldoCero = 'no tiene factura';
            $posicion_coincidencia = strpos($response, $facturaEnSaldoCero);              
        }

	} catch (Exception $e) {
		$codError = $e->getCode();
		$descError = $e->getMessage();
	}

	if ($posicion_coincidencia === false) {
        $nombrePDFDetalleDeCargos = $historia . '-' . $ingreso . '-' .  $soporte;
        return generarPDF(
            $conex,
            htmlentities($response),
            $nombrePDFDetalleDeCargos,
            $historia,
            $ingreso,
            $descripcion,
            $wemp_pmla,
            $responsable,
			null,
			$soporte
        );
    } else {
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex,$codError);
    }
}

/**
 * Este metodo permite obtener el soporte automatico de la consulta historia clinica HCE
 * recibiendo como parametro la historia, el ingreso y el responsable
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 *
 * @return [html] Respuesta html
 */

function consultaHistoriaClinica(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $url,
    $descripcion,
    $rutaOrigen
) {

    //se valida credenciales
    validarIngreso($conex, $wemp_pmla);

    // Se arma la estructura que va a llamar a la URL
    $url = armarURLHistoriaClinica($wemp_pmla, $historia, $ingreso,$url);
    
    // Crear un manejador cURL
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }

    // opciones del cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    //Ejecutar cURL
    $ret = curl_exec($ch);

    //buscamos palabra
    $cadena_buscada   = 'El usuario no tiene formularios firmados';
    $posicion_coincidencia = strpos($ret, $cadena_buscada);
    
    if ($posicion_coincidencia === false ) {
        $dom = new DOMDocument();
        @$dom->loadHTML($ret);

        if (empty($dom->getElementById('fecha_inicial'))) {
            $pdf_generado['status'] = '400';
            $pdf_generado['result'] = 'Ha ocurrido un error inesperado';
            return $pdf_generado;
        }

        $formularios         = "";
        $formularios_paq     = "";
        $historia            = $dom->getElementById('whis')->getAttributeNode('value')->nodeValue;
        $ingreso             = $dom->getElementById('wing')->getAttributeNode('value')->nodeValue;
        $centroCostos        = $dom->getElementById('wcco')->getAttributeNode('value')->nodeValue;
        $wSolicitaCenimp     = $dom->getElementById('wSolicitaCenimp')->getAttributeNode('value')->nodeValue;
        $monitor             = $dom->getElementById('wmonitor')->getAttributeNode('value')->nodeValue;

        $fecha_i             = $dom->getElementById('fecha_inicial')->getAttributeNode('value')->nodeValue;
        $fecha_f             = $dom->getElementById('fecha_final')->getAttributeNode('value')->nodeValue;
        $error               = 0;
        $weditar             = $dom->getElementById('weditar')->getAttributeNode('value')->nodeValue;
        $widenti             = $dom->getElementById('widenti')->getAttributeNode('value')->nodeValue;
        //$wespecial           = $dom->getElementById('wespecial')->getAttributeNode('value')->nodeValue;
        $wespecial           = "TODAS";
        $fechaIni_def        = "";
        $fechaFin_def        = "";
        $formulariosElegidos = "";
        //$formulariosElegidos = $(".contenedor_formularios[historia='"+historia+"'][ingreso='"+ingreso+"']").attr("formulariosElegidos");
        $cadenaProgramasAnexos = $dom->getElementById('cadenaProgramasAnexos')->getAttributeNode('value')->nodeValue;
        $htmlProgramasAnexos = "";

        /* OTRAS VARIABLES IMPORTANTES */

        $wcenimp          = $dom->getElementById('wcenimp')->getAttributeNode('value')->nodeValue;
        $wcliame          = $dom->getElementById('wcliame')->getAttributeNode('value')->nodeValue;
        $whcebasedato      = $dom->getElementById('whcebasedato')->getAttributeNode('value')->nodeValue;
        $wmovhos           = $dom->getElementById('wmovhos')->getAttributeNode('value')->nodeValue;
        $wmodalidad        = $dom->getElementById('wmodalidad')->getAttributeNode('value')->nodeValue;
        $wincluyeLogo      = $dom->getElementById('wincluyeLogo')->getAttributeNode('value')->nodeValue;
        $wincluyeTapa      = $dom->getElementById('wincluyeTapa')->getAttributeNode('value')->nodeValue;
        $wimpresionDirecta = $dom->getElementById('wimpresionDirecta')->getAttributeNode('value')->nodeValue;
        $wenviaEmail       = $dom->getElementById('wenviaEmail')->getAttributeNode('value')->nodeValue;
        $esFacturacion     = $dom->getElementById('wfacturacion')->getAttributeNode('value')->nodeValue;
        $wsoloActivos      = $dom->getElementById('wsoloActivos')->getAttributeNode('value')->nodeValue;
        $wusuCenimp        = $dom->getElementById('wusuCenimp')->getAttributeNode('value')->nodeValue;
        $wingresoHce       = $dom->getElementById('wingresoHce')->getAttributeNode('value')->nodeValue;

        /* FALTA POR MIGRAR
        if($cadenaProgramasAnexos!="")
        {
            htmlProgramasAnexos = consultarHtmlPorProgramaAnexo(cadenaProgramasAnexos);
        }
        */
        $bloquear = 'no';

        if ($bloquear == "no") {
            $fechaIni_def = $dom->getElementById('wfecini_def')->getAttributeNode('value')->nodeValue;
            $fechaFin_def = $dom->getElementById('wfecfin_def')->getAttributeNode('value')->nodeValue;
        }

        $hayFormularios = false;
        $hayPaquetes    = false;

        $j = 0;

        //aqui se verifica si estan todos checkeados o no para la bd

        $inputs = $dom->getElementsByTagName("input");
        foreach ($inputs as $input) {
            if (
                $input->hasAttribute('class') &&
                $input->getAttributeNode('class')->nodeValue == 'formulario_arbol_impresion' &&
                $input->hasAttribute('type') &&
                $input->getAttributeNode('type')->nodeValue == 'checkbox' &&
                $input->hasAttribute('checked')
            ) {

                $value = $input->getAttributeNode('value')->nodeValue;
                if ($j > 0) {
                    $formularios .= ',';
                }

                $formularios .= $value;
                $j++;
            }
        }

        if ($j > 0) {
            $hayFormularios = true;
        }

        $array_paquetes = array();
        /* FALTA POR MIGRAR
            $j=0;
            
            $("#tabla_paquetes").find(".formulario_de_paquete:checked").each( function(){

                var paquete = $(this).attr("paquete");
                if( datos.paquetes[ paquete ] == undefined )
                    datos.paquetes[ paquete ] = new Array();

                datos.paquetes[ paquete ].push( $(this).val() );
                hayPaquetes=true;
            });
        */
        /*
        $j = 0
        $array_paquetes = array();
        foreach ($inputs as $input)
        {
            if($input->hasAttribute('class') && 
            $input->getAttributeNode('class')->nodeValue == 'formulario_de_paquete' && 
            $input->hasAttribute('type') && 
            $input->getAttributeNode('type')->nodeValue == 'checkbox' && 
            $input->hasAttribute('checked')) {
                
                $value = $input->getAttributeNode('value')->nodeValue;
                $paquete = $input->getAttributeNode('paquete')->nodeValue;
                
                $array_paquetes[$paquete] = $value;
                
                
                $hayPaquetes = true;
            }
        }*/

        $datos = json_encode(['formularios_arbol' => $formularios, 'paquetes' => $array_paquetes]);
        $data = array(
            'wemp_pmla'                     => $wemp_pmla,
            'fecha_i'                       => $fecha_i,
            'fecha_f'                       => $fecha_f,
            'fecIngreso'                    => $fechaIni_def,
            'fecEgreso'                        => $fechaFin_def,
            'monitor'                        => $monitor,
            'datos'                            => $datos,
            'historia'                        => $historia,
            'ingreso'                        => $ingreso,
            'wcco'                            => $centroCostos,
            'wSolicitaCenimp'               => $wSolicitaCenimp,
            'wenviaEmail'                    => $wenviaEmail,
            'wimpresionDirecta'                => $wimpresionDirecta,
            'action'                         => 'guardarSolicitud',
            'consultaAjax'                    => '',
            'wcenimp'                        => $wcenimp,
            'wmovhos'                        => $wmovhos,
            'whcebasedato'                    => $whcebasedato,
            'wmodalidad'                    => $wmodalidad,
            'wlogo'                         => $wincluyeLogo,
            'wtapa'                            => $wincluyeTapa,
            'weditar'                        => $weditar,
            'widenti'                        => $widenti,
            'wespecial'                     => $wespecial,
            'tipoPeticion'                    => "normal",
            'htmlProgramasAnexos'            => $htmlProgramasAnexos,
            'formulariosElegidos'            => $formulariosElegidos    
        );

        $opciones = cargarOpcionesPOST($url,$data);        
        curl_setopt_array($ch, $opciones);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    else{
        $pdf_generado['status'] = '400';
        $pdf_generado['result'] = 'Ha ocurrido un error generando la historia Clinica';
        guardarRespuesta($conex,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,$pdf_generado,'ERR17',$descripcion);
        return $pdf_generado;       
    }

    return armarPDFHistoriaClinica($response,$wemp_pmla,$historia,$ingreso,$soporte,$descripcion,$responsable,$rutaOrigen,$conex);
}

/**
 * Este metodo permite obtener el soporte automatico de Registro de Anestesia
 * recibiendo como parametro la historia, el ingreso y el responsable
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 *
 * @return [html] Respuesta html
 */

function consultaRegistroAnestesia(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $url,
    $descripcion,
    $rutaOrigen
) {

    validarIngreso($conex, $wemp_pmla);

    // Se arma la estructura que va a llamar a la URL
    $UrlHistoriaClinica = armarURLHistoriaClinica($wemp_pmla, $historia, $ingreso,$url);

    // Crear un manejador cURL
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }

    // opciones del cURL
    curl_setopt($ch, CURLOPT_URL, $UrlHistoriaClinica);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    //Ejecutar cURL
    $respuestaInicial = curl_exec($ch);

    //buscamos si hay un error en el formulario antes de llamar al DOM
    $usuarioSinFormulario  = 'El usuario no tiene formularios firmados';
    $posicionCoincidencia = strpos($respuestaInicial, $usuarioSinFormulario);

    if ($posicionCoincidencia === false) {
        
        $dom = new DOMDocument();
        @$dom->loadHTML($respuestaInicial);

        //000075 = preanestecia adjunto
        //000077 = anestesia adjunto
        //000334 = NotaAnestesia adjunto
        $data =  cargarDom($dom, $wemp_pmla, ['000150', '000334', '000075']); 

        $opciones = cargarOpcionesPOST($url,$data);        
        curl_setopt_array($ch, $opciones);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    return armarPDFHistoriaClinica($response,$wemp_pmla,$historia,$ingreso,$soporte,$descripcion,$responsable,$rutaOrigen,$conex);
}

/**
 * Este metodo permite obtener el soporte automatico de Detalle De Materiales
 * recibiendo como parametro la historia, el ingreso y el responsable
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 *
 * @return [html] Respuesta html
 */

function consultaDetalleDeMateriales(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $factura,
    $fuenteDeFactura,
    $UrlDetalleDeMateriales,
    $descripcion
) {
	$codError = '';
	try{
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);

        //Se valida que venga la factura y no sea NULL
        $validarFactura = validarFactura($factura,$descripcion);
        
        if($validarFactura['status'] != '400'){
            $ch = curl_init();
            $data = array(
                'usuario'                => '0104686',
                'fte'                    => $fuenteDeFactura,
                'fac'                    =>  $factura,
                'com'                    => '*',
                'atc'                    => 'N',
                'cneps'                  => 'N'
            );
    
            $opciones = cargarOpcionesPOST($UrlDetalleDeMateriales,$data);    
            curl_setopt_array($ch, $opciones);
            $response = curl_exec($ch);
            curl_close($ch);
            $facturaEnSaldoCero = 'no tiene detalle de materiales';
            $posicion_coincidencia = strpos($response, $facturaEnSaldoCero);
        }


    } catch (Exception $e) {
		$codError = $e->getCode();
		$descError = $e->getMessage();
	}
    
    if ($posicion_coincidencia === false) {
        $nombrePDFDetalleDeMateriales = $historia . '-' . $ingreso . '-' . $soporte;
        return generarPDF(
            $conex,
            htmlentities($response),
            $nombrePDFDetalleDeMateriales,
            $historia,
            $ingreso,
            $descripcion,
            $wemp_pmla,
            $responsable,
            null,
            $soporte
        );
    } 
    else {
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex,$codError);
    }
}

/**
 * Este metodo permite obtener el soporte automatico de hoja de medicamentos
 * recibiendo como parametro la historia y el ingreso
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 *
 * @return [html] Respuesta html
 */

function ConsultaHojaDeMedicamentos(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $domain,
    $descripcion
) {
    global $wbasedato;
    $codError = '';
    try{

        validarIngreso($conex, $wemp_pmla);
        $query_rango = "SELECT  Ubihis, Ubiing, Ubialp as alta_pac,movhos18.Fecha_data AS fecha_ingreso,movhos18.Hora_data, Ubifad AS fecha_alta,Ubihad,Ccocod,Cconom
                                FROM " . $wbasedato . "_000018 AS movhos18
                        LEFT JOIN " . $wbasedato . "_000011 AS movhos11
                        ON Ccocod=Ubisac
                        WHERE   Ubihis = '" . $historia . "'
                        AND Ubiing = '" . $ingreso . "'
                            ORDER BY    movhos18.fecha_data";

        $res_r = mysql_query($query_rango, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $query_rango . " - " . mysql_error());
        $numreg = mysql_num_rows($res_r);
        $row_r = mysql_fetch_array($res_r);
        if ($numreg > 0) {
            $rango = array(
                'whis'          => $row_r['Ubihis'],
                'wing'          => $row_r['Ubiing'],
                'Ccocod'        => $row_r['Ccocod'],
                'Cconom'        => $row_r['Cconom'],
                'alta_pac'      => $row_r['alta_pac'],  // off: est  activo, on: esta de alta, ya sali 
                'fecha_ingreso' => $row_r['fecha_ingreso'] . ' ' . $row_r['Hora_data'],
                'fecha_alta'    => $row_r['fecha_alta'] . ' ' . $row_r['Ubihad'],
                'dif_fechas'    => 0
            );

            // Si la fecha de alta esta vac a se asigna la fecha y hora actual de servidor.
            if ($rango['alta_pac'] == 'off' || $rango['fecha_alta'] == '0000-00-00 00:00:00') {
                $rango['fecha_alta'] = date("Y-m-d H:i:s");
            }
            $rango['dif_fechas'] = diasEntreFechas($rango['fecha_ingreso'], $rango['fecha_alta']);
            $dLimite = consultarAliasPorAplicacion($conex, $wemp_pmla, 'dias_hoja_medicamentos');
        } else {
            $pdf_generado['status'] = '400';
            $pdf_generado['result'] = 'No se han encontrado hoja de medicamento para la historia' . ' ' . $historia . '-' . $ingreso;
            return $pdf_generado;
        }

        $nombre =  $row_r['Cconom'];
        $cco = $row_r['Ccocod'];
        $cadena = str_replace(" ", "%20", $nombre);
        $fecha_ingreso = $row_r['fecha_ingreso'];
        $numero_de_hojas = 1;
        $llamadoPDF = 0;

        do {

            $fecha_final = sumaDiasAFecha($fecha_ingreso, $dLimite); //2021-03-15
            $urlHistoria = "whis=" . $historia;
            $urlIngreso = "&wing=" . $ingreso;
            $urlCentroDeCosto = '&wcco=' . $cco . '-' . $cadena;
            $urlWemp_pmla = "&wemp_pmla=" . $wemp_pmla;
            $urlTipoPos = "&wtipopos=todos&imprimir=true";
            $urlFecha = '&wfechainicial=' . $fecha_ingreso . '&wfechafinal=' . $fecha_final . '&wrango_fechas=1';
            $url = $domain . $urlHistoria . $urlIngreso . $urlCentroDeCosto . $urlWemp_pmla . $urlTipoPos . $urlFecha . '&automatizacion_pdfs=';
            
            // Crear un manejador cURL
            $ch = curl_init();
            if (!$ch) {
                die("Couldn't initialize a cURL handle");
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            $ret = curl_exec($ch);
            //buscamos palabra
            $cadena_buscada   = 'SINDATOS';
            $posicion_coincidencia = strpos($ret, $cadena_buscada);
            if (empty($ret)) {
                // some kind of an error happened
                curl_close($ch); // close cURL handler       
                $pdf_generado['status'] = '400';
                $pdf_generado['result'] = 'Ha ocurrido un error en: Hoja de medicamentos';
            } else {
                $info = curl_getinfo($ch);
                curl_close($ch); // close cURL handler

                if (empty($info['http_code']) || $info['http_code'] !== 200) {
                    $pdf_generado['status'] = '400';
                    $pdf_generado['result'] = 'Ha ocurrido un error en: Hoja de medicamentos';
                } else {
                    // load the HTTP codes
                    $nombrePDFHojaDeMedicamentos = $historia . '-' . $ingreso . '-' . $soporte;
                    if ($posicion_coincidencia === false) {
                        $llamadoPDF = $llamadoPDF + 1;
                        $pdf_generado = generarPDF(
                            $conex,
                            htmlentities($ret),
                            $nombrePDFHojaDeMedicamentos,
                            $historia,
                            $ingreso,
                            $descripcion,
                            $wemp_pmla,
                            $responsable,
                            $numero_de_hojas,
                            $soporte
                        );
                    }
                }
            }

            //se repite la operacion con los nuevos valores
            if ($rango['dif_fechas'] > $dLimite) {

                $fecha_ingreso = sumaDiasAFecha($fecha_ingreso, $dLimite + 1);
                $formato = 'Y-m-d';
                $fecha = DateTime::createFromFormat($formato, $fecha_ingreso);
                $new_date =  $fecha->format('Y-m-d H:i:s');
                $rango['dif_fechas'] = diasEntreFechas($new_date, $rango['fecha_alta']); 
                $numero_de_hojas++;

            } else {
                $rango['dif_fechas'] = 0;
            }
        } while ($rango['dif_fechas'] > 0);
    } catch (Exception $e) {
        $codError = $e->getCode();
    }
	return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,'1',$conex,$codError);
}

function ConsultaLaboratorio($conex, 
    $wemp_pmla, 
    $historia, 
    $ingreso, 
    $responsable, 
    $soporte,
    $descripcion){

    global $whce;
    global $wbasedato;

    $codError = '';

    try{
        $sacarResultados = "SELECT Eexcod
        FROM " . $wbasedato . "_000045 o
        WHERE Eexcas = 'on'";

        $resResultados = mysql_query($sacarResultados, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $resResultados . " - " . mysql_error());
        $numResultados = mysql_num_rows($resResultados);    

        $having = '';

        if ($numResultados > 0) {
            $having = 'having(';
            while( $row = mysql_fetch_array( $resResultados ) ){
                $having .= 'NOT FIND_IN_SET("'.$row['Eexcod'].'",estados) AND ';         
            }   

            $having = substr($having, 0, -5);
            $having .= ')'; 
        }
        
        //Las historias deben de ser desde 2021-08-01
        $sacarUrlPorSql = "SELECT DISTINCT Deturp,Detotr FROM(
            SELECT Detotr,GROUP_CONCAT(DISTINCT od.Deturp) Deturp, GROUP_CONCAT(od.Detesi) estados        
            FROM " . $whce . "_000027 o
            INNER JOIN " . $whce . "_000028 od ON o.Ordnro = od.Detnro
            WHERE Ordhis = $historia
            AND Ording = $ingreso
            AND od.Detesi <> 'C'
            AND Deturp <> ''
            GROUP BY od.Detotr
            $having) as subSql;";
        
        $resLaboratorio = mysql_query($sacarUrlPorSql, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $sacarUrlPorSql . " - " . mysql_error());
        $numLaboratorio = mysql_num_rows($resLaboratorio);
        
        if ($numLaboratorio > 0) {
            $numeroDelPDF = 1;
            while ($rowsLaboratorio = mysql_fetch_array($resLaboratorio)) {

                $urlCompleta = $rowsLaboratorio['Deturp'];
                $verificarURL = explode(",", $urlCompleta );
                foreach($verificarURL as $resultadoLaboratorio){
                    $verificarExtensionPDF = substr($resultadoLaboratorio,-4);
                    if($verificarExtensionPDF === '.pdf'){
                        $nombreLaboratorio = $historia . '-' . $ingreso . '-' . $soporte .'-'.  $numeroDelPDF;
                        $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                        $archivoDestino = $carpetaResponsable . '/' . $nombreLaboratorio . ".pdf";
                        copy($resultadoLaboratorio, $archivoDestino);
                        $numeroDelPDF += 1;
                    }
                    else{
                        $urlAmazon = consultarAliasPorAplicacion($conex,$wemp_pmla,'urlActualizarLaboratorio');
                        $mysqli = new mysqli('131.1.18.106:3306', '4D_UCONSULTA', 'NTmUsQbPvRC2UD74','4dlab');

                        $subStringUrlOriginal = recortarUrl($urlAmazon,$resultadoLaboratorio);
                        $agregarCaracteresParaSQL = '%'.$subStringUrlOriginal.'%';
                        $sqlUrlLaboratorio = "SELECT *
                        FROM " . 'OT' . " mov 
                        WHERE mov.NumeroEnLaWeb LIKE '$agregarCaracteresParaSQL';";
                        
                        $resultado = $mysqli->query($sqlUrlLaboratorio) or die("<b>ERROR EN QUERY MATRIX(sqlUrlLaboratorio ):</b><br>". 
                        $mysqli->error);

                        while ($fila = $resultado->fetch_assoc()) {
                            $url = $fila['NumeroEnLaWeb'];
                            //$resultadoNumeroDeOrden = $fila['NumOT'];
                        }
                        
                        $resultadoUrlLaboratorio = $urlAmazon.$url.'.pdf';
                        mysqli_close($mysqli);

                        if(!empty($resultadoUrlLaboratorio) && !empty($resultadoLaboratorio)){

                            /*$likeResultadoLaboratorio = '%'.$resultadoLaboratorio.'%';

                            $actualizarURLEnMatrix = "UPDATE " . $whce . "_000028
                            SET Deturp = '$resultadoUrlLaboratorio'
                            WHERE Deturp LIKE '$likeResultadoLaboratorio'
                            AND Detotr = '$resultadoNumeroDeOrden';";    
                            
                            $ActualizarUrlQuery = mysql_query($actualizarURLEnMatrix,$conex) or die("Error: " . mysql_errno() . 
                            " - en el query: " . $ActualizarUrlQuery . " - " . mysql_error()); */
                            
                            $nombreLaboratorio = $historia . '-' . $ingreso . '-' . $soporte .'-'.  $numeroDelPDF;
                            $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                            $archivoDestino = $carpetaResponsable . '/' . $nombreLaboratorio . ".pdf";
                            copy($resultadoUrlLaboratorio, $archivoDestino);
                            $numeroDelPDF += 1;
                        }
                    }                 
                }
            }
        }
        else{
            $respuestaLaboratorio['status'] = '400';
            $respuestaLaboratorio['result'] = 'Ha ocurrido un error con el soporte de:' . ' ' . $descripcion;
            return $respuestaLaboratorio;            
        }
    }
     catch (Exception $e) {
        $codError = $e->getCode();
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,'1',$conex,$codError);
    }       
    return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,'1',$conex,$codError);
}

/**
 * Este metodo permite obtener el soporte automatico de hoja de medicamentos
 * recibiendo como parametro la historia y el ingreso
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable del paciente]
 * @param soporte[int]				[Número del soporte a buscar]
 * @param concepto[int]				[Número de concepto asociado al soporte]
 * @param ayuda[int]				[Número que indica que es un soporte = 0700 en la cliame_000192]
 * @param descripcion[int]			[Nombre del soporte en la base de datos]
 * 
 * @return [json] Respuesta Json con 200 si fue correcto o 400 si fue errado
 */

function consultaSoportesImex(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $concepto,
    $ayuda,
    $descripcion
) {

    global $wbasedato;
    global $wcliame;

    $codError = '';
    
    try{

        //Se valida si tiene la conexion a matrix y cual es su empresa
        validarIngreso($conex,$wemp_pmla);

        //Se inidica la fecha inicial en que comenzo la interoperabilidad con Imex
        $fechaInicioImex = consultarAliasPorAplicacion($conex,$wemp_pmla,'fechaInicioImex');

        //Query para sacar la Url de Imex para cada uno de los soportes asociados a los pacientes
        // la fecha de inicio imex es 2020-10-01

        $concepto = '0506';
        $sacarUrlPorSql = "SELECT mvcurp
        FROM " . $wbasedato . "_000268 a
        JOIN " . $wcliame . "_000192
        ON (Mvccup = Hompom
        AND Homcos = $concepto
        AND Homcom = $ayuda) 
        WHERE Mvchis = $historia
        AND Mvcing = $ingreso
        AND a.Fecha_data > $fechaInicioImex 
        GROUP BY 1;";

        $resPrescripciones = mysql_query($sacarUrlPorSql, $conex) or die("Error: ".mysql_errno()."- en el query: " . $sacarUrlPorSql . " - " . mysql_error());
        $numPrescripciones = mysql_num_rows($resPrescripciones);

        //Se valida si tiene algun registro para recorrerlo
        if ($numPrescripciones > 0){

            $numeroDelPDF = 1;
            while ($rowsPrescripciones = mysql_fetch_array($resPrescripciones)){

                //Se nombra el archivo, se extrae la url y se lleva al intercambiador.
                $url = $rowsPrescripciones['mvcurp'];
                if ($url !== null){

                    // Crear un manejador cURL
                    $ch = curl_init();
                    if (!$ch){
                        die("Couldn't initialize a cURL handle");
                    }

                    // opciones del cURL
                    $ret = curl_setopt($ch, CURLOPT_URL, $url);
                    $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $ret = curl_setopt($ch, CURLOPT_HEADER, 0);

                    //Ejecutar cURL
                    $ret = curl_exec($ch);
                    preg_match_all('#\bhttps?://rispmla[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $ret, $match);
                    if(count($match[0]) > 1 ){
                        $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                        foreach($match[0] as $soporteUrl){
                            $nombreImex =   $historia . '-' . $ingreso . '-' . $soporte .'-'.  $numeroDelPDF;
                            $archivoDestino = $carpetaResponsable . '/' . $nombreImex . ".pdf";
                            copy($soporteUrl, $archivoDestino);
                            chmod($archivoDestino,0777);
                            $numeroDelPDF += 1;
                        }
                    }
                    else{
                        $nombreImex =   $historia . '-' . $ingreso . '-' . $soporte .'-'.  $numeroDelPDF;
                        $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                        $archivoDestino = $carpetaResponsable . '/' . $nombreImex . ".pdf";
                        copy($url, $archivoDestino);
                        chmod($archivoDestino,0777);
                        $numeroDelPDF += 1;
                    }  
                }
                else{
                    $respuestaImex['status'] = '400';
                    $respuestaImex['descripcion'] = 'No se le ha generado el soporte:' . $descripcion;
                    return $respuestaImex;
                }
            }
        }
    }
    catch (Exception $e) {
        $codError = $e->getCode();
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,'1',$conex,$codError);
    }           
    //Se verifica la respuesta fue exitosa, es porque se creo el PDF correctamente
    return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,'1',$conex,$codError);
}

/**
 * Este metodo permite obtener el soporte automatico de las historias de HCE
 * recibiendo como parametro la historia, el ingreso y el responsable
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * @param historia[int]				[Número de historia del paciente]
 * @param ingreso[int]				[Número de ingreso del paciente]
 * @param responsable[int]			[Número de responsable a cargo del paciente]
 * @param soporte[int]			    [Número del soporte]
 * @param descripcion[int]			[Nombre del soporte]
 * @param formularioHCE[int]	    [Número de formulario relacionado a la HCE]
 * @param url[int]		            [Url donde se encuentra el script]
 * @return [html] Respuesta html
 */

function consultaSoportesHCE(
    $conex = null,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $descripcion,
    $formularioHCE,
    $url,
    $rutaOrigen
){
    validarIngreso($conex, $wemp_pmla);

    // Se arma la estructura que va a llamar a la URL
    $urlHistoriaClinica = armarURLHistoriaClinica($wemp_pmla, $historia, $ingreso,$url);

    // Crear un manejador cURL
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }

    // opciones del cURL
    $ret = curl_setopt($ch, CURLOPT_URL, $urlHistoriaClinica);
    $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ret = curl_setopt($ch, CURLOPT_HEADER, 0);

    //Ejecutar cURL
    $ret = curl_exec($ch);

    //buscamos palabra
    $cadena_buscada   = 'El usuario no tiene formularios firmados';
    $posicion_coincidencia = strpos($ret, $cadena_buscada);
    if ($posicion_coincidencia === false){

        //Se carga el DOM para buscar en el documento el formulario correspondiente
        $substringHCE = consultarAliasPorAplicacion($conex,$wemp_pmla,'substringHCE');

        $formularioHCE = substr($formularioHCE,$substringHCE); //Nota : si se va a cargar el formula meds soat los datos son : ['10144', '000235', '000160']
        $dom = new DOMDocument();
        @$dom->loadHTML($ret);

        $data =  cargarDom($dom,$wemp_pmla,[$formularioHCE]);
        $opciones = cargarOpcionesPOST($url,$data);        
        curl_setopt_array($ch,$opciones);
        $response = curl_exec($ch);
        curl_close($ch);
    }

    return armarPDFHistoriaClinica($response,$wemp_pmla,$historia,$ingreso,$soporte,$descripcion,$responsable,$rutaOrigen,$conex);
}

function diasEntreFechas($fecha1, $fecha2)
{
    $explode1 = explode(' ', $fecha1);
    $explode1f = explode('-', $explode1[0]);
    $explode1h = explode(':', $explode1[1]);

    $explode2 = explode(' ', $fecha2);
    $explode2f = explode('-', $explode2[0]);
    $explode2h = explode(':', $explode2[1]);

    $fecha_ingreso = mktime($explode1h[0], $explode1h[1], $explode1h[2], $explode1f[1], $explode1f[2], $explode1f[0]);
    $fecha_alta = mktime($explode2h[0], $explode2h[1], $explode2h[2], $explode2f[1], $explode2f[2], $explode2f[0]);

    $dif_fechas = abs($fecha_alta - $fecha_ingreso) / (60 * 60 * 24);
    return round($dif_fechas);
}

function sumaDiasAFecha($fecha, $dias, $accion = '', $horas = '')
{
    $explode1f = explode('-', $fecha);

    $explode1h = array(0 => 0, 1 => 0, 2 => 0);
    if ($horas != '') {
        $explode1h = explode(':', $horas);
    }

    $mk = mktime($explode1h[0], $explode1h[1], $explode1h[2], $explode1f[1], $explode1f[2], $explode1f[0]);
    $nueva = ($accion == 'resta') ? ($mk - ($dias * 24 * 60 * 60)) : ($mk + ($dias * 24 * 60 * 60));
    $fecha_aumentada = date("Y-m-d", $nueva);
    return $fecha_aumentada;
}

function recortarUrl($urlAmazon,$urlMatrix){
    if (!is_bool(strpos($urlMatrix, $urlAmazon)))
    return substr($urlMatrix, strpos($urlMatrix,$urlAmazon)+strlen($urlAmazon));
};

// Se valida si la acción fue enviada por GET o por POST para las pruebas
    if (isset($_REQUEST['accionGet'])){
        $arrayRespuesta = ConsultaEstadoWithGet($conex,$_GET['wemp_pmla'],$_GET['historia'],$_GET['ingreso'],$_GET['responsable'],
        $_GET['documentoDeIdentidad'],$_GET['tipoDeDocumento'],$_GET['fechaDeAdmision'],$_GET['numeroFactura'],$_GET['fuenteFactura'],
        $_GET['descripcion'],$_GET['formulario'],$_GET['concepto'],$_GET['soportes']);
    }
    else if(isset($_REQUEST['accionPost'])){
        $arrayRespuesta = ConsultaEstadoWithPost($conex, $_GET['wemp_pmla'], $_POST['patients']);        
    }
    else{
        $arrayRespuesta = ConsultaErronea();        
    }

    // Respuesta codificada en Javascript 
    header('Content-type: text/javascript');
    echo json_encode($arrayRespuesta, JSON_PRETTY_PRINT);