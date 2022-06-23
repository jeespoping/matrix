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
 * . @update [2022-01-17]	-	Actualizacion para mostrar los resultados de los soportes subidos mediante un solo json
 * . @update [2022-02-14]	-	Actualizacion para mostrar los resultados de los soportes subidos cuando viene vacio(No se procesa ningun soporte)
 * . @update [2022-03-09]	-	Se incluye el soporte de Laboratorio para Barranquilla
 * . @update [2022-03-28]	-	Se incluye el soporte de Imagenologia para Barranquilla
 * . @update [2022-04-14]	-	Refactor del codigo para Imagenologia y Laboratorio, nombramiento de funciones
 * . @update [2022-06-03]	-	Se incluye shell para eliminar soportes por tema del usuario bronco, se hace refactor de codigo
 * */

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
    $arrayRespuestaSoportes = [];
    $consultaEstadoDescargas = [];
    $estadoTemporal = [];
    /**Consulta los soportes HCE y Imex(Imagenologia) que actualmente se pueden descargar automaticamente. 
    Esta información esta en la root 51**/

    $consultaArreglosHCEParametrizados = consultarAliasPorAplicacion($conex, $wemp_pmla, 'soportesHCE');
    $consultaArreglosImexParametrizados = consultarAliasPorAplicacion($conex, $wemp_pmla, 'soportesImex');
    $arraySoportesHCEAConsultar = preg_split("/\,/", $consultaArreglosHCEParametrizados);
    $arraySoportesImexAConsultar = preg_split("/\,/", $consultaArreglosImexParametrizados);
    for ($i = 0; $i < $count; $i++) {
        $arrayEstadoRespuesta = [];
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

        if (array_search($soporte = "03", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "03", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];
            $rutaOrigen = $data[$posicion]['sopalm'];
            $ordenesMedicas = consultaOrdenMedica(
                $conex,
                $wemp_pmla,
                $historia,
                $ingreso,
                $responsable,
                $soporte,
                $url,
                $descripcion,
                $rutaOrigen
            );
            $arrayRespuestaSoportes = devolverRespuestaSoporte($ordenesMedicas, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "22", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "22", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $miPresMedsNoPbs = consultaMiPresMedsNoPbs(
                $conex,
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
            );
            $arrayRespuestaSoportes = devolverRespuestaSoporte($miPresMedsNoPbs, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "41", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "41", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];
            $rutaOrigen = $data[$posicion]['sopalm'];

            $furips = consultaFurips($conex, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, $factura, $url, $descripcion, 
            $rutaOrigen);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($furips, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "43", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "43", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $facturaPdf = consultaFacturaPDF(
                $conex,
                $wemp_pmla,
                $historia,
                $ingreso,
                $responsable,
                $soporte,
                $factura,
                $fuenteDeFactura,
                $url,
                $descripcion
            );
            $arrayRespuestaSoportes = devolverRespuestaSoporte($facturaPdf, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "46", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "46", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $detalleDeCargos = consultaDetalleDeCargos(
                $conex,
                $wemp_pmla,
                $historia,
                $ingreso,
                $responsable,
                $soporte,
                $factura,
                $fuenteDeFactura,
                $url,
                $descripcion
            );
            $arrayRespuestaSoportes = devolverRespuestaSoporte($detalleDeCargos, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "48", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "48", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $historiaClinica = consultaHistoriaClinica($conex, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, $url, $descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($historiaClinica, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "50", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "50", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $registroAnestesia = consultaRegistroAnestesia(
                $conex,
                $wemp_pmla,
                $historia,
                $ingreso,
                $responsable,
                $soporte,
                $url,
                $descripcion
            );
            $arrayRespuestaSoportes = devolverRespuestaSoporte($registroAnestesia, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "66", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "66", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $detalleDeMateriales = consultaDetalleDeMateriales(
                $conex,
                $wemp_pmla,
                $historia,
                $ingreso,
                $responsable,
                $soporte,
                $factura,
                $fuenteDeFactura,
                $url,
                $descripcion
            );
            $arrayRespuestaSoportes = devolverRespuestaSoporte($detalleDeMateriales, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "67", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "67", array_column($data, 'code'));
            $url = $data[$posicion]['url'];
            $descripcion = $data[$posicion]['supportName'];

            $hojaDeMedicamentos = ConsultaHojaDeMedicamentos($conex, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, $url, $descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($hojaDeMedicamentos, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        if (array_search($soporte = "69", array_column($data, 'code')) !== false) {
            $posicion = array_search($soporte = "69", array_column($data, 'code'));
            $descripcion = $data[$posicion]['supportName'];
            $clinicaAConsultar = $data[$posicion]['url'];
            $tipoDeOrden = $data[$posicion]['soptor'];
            $laboratorio = ConsultaLaboratorio($conex, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, 
            $clinicaAConsultar,$tipoDeOrden,$descripcion);
            $arrayRespuestaSoportes = devolverRespuestaSoporte($laboratorio, $historia, $ingreso, $soporte, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }

        foreach ($arraySoportesImexAConsultar as $soporteImex) {
            if (array_search(in_array($soporteImex,  array_column($data, 'code')), array_column($data, 'code')) !== false) {
                $posicion = array_search($soporteImex, array_column($data, 'code'));
                $concepto = $data[$posicion]['concept'];
                $descripcion = $data[$posicion]['supportName'];
                $ayuda = $data[$posicion]['sopcop'];
                $clinicaAConsultar = $data[$posicion]['url'];
                $tipoDeOrden = $data[$posicion]['soptor'];
                $soportesImex = consultaSoportesImex(
                    $conex,
                    $wemp_pmla,
                    $historia,
                    $ingreso,
                    $responsable,
                    $soporteImex,
                    $concepto,
                    $ayuda,
                    $descripcion,
                    $tipoDeOrden,
                    $clinicaAConsultar
                );
                $arrayRespuestaSoportes = devolverRespuestaSoporte($soportesImex, $historia, $ingreso, $soporteImex, $descripcion);
                array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
            }
        }

        foreach ($arraySoportesHCEAConsultar as $soporteHCE) {
            if (array_search(in_array($soporteHCE,  array_column($data, 'code')), array_column($data, 'code')) !== false) {
                $posicion = array_search($soporteHCE, array_column($data, 'code'));
                $url = $data[$posicion]['url'];
                $descripcion = $data[$posicion]['supportName'];
                $formulario = $data[$posicion]['soptab'];

                $soportesHCE = consultaSoportesHCE(
                    $conex,
                    $wemp_pmla,
                    $historia,
                    $ingreso,
                    $responsable,
                    $soporteHCE,
                    $descripcion,
                    $formulario,
                    $url
                );
                $arrayRespuestaSoportes = devolverRespuestaSoporte($soportesHCE, $historia, $ingreso, $soporteHCE, $descripcion);
                array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
            }
        }

        foreach ($arrayEstadoRespuesta as $estados) {
            array_push($estadoTemporal, $estados['status']);
        }

        if (in_array(300, $estadoTemporal, true) || in_array(400, $estadoTemporal, true) || in_array(500, $estadoTemporal, true)) {
            $consultaEstadoDescargas['status'] = '400';
        }
        elseif(empty($estadoTemporal)){
            $consultaEstadoDescargas['status'] = '204';            
        }
        else {
            $consultaEstadoDescargas['status'] = '200';
        }
        $consultaEstadoDescargas['responsable'] = $responsable;
        $consultaEstadoDescargas['result'] = $arrayEstadoRespuesta;
        guardarRespuesta($conex, $wemp_pmla, $historia, $ingreso, $responsable, $consultaEstadoDescargas, '', '');
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
    $soportes = null,
	$accion = null,
	$urlArmado = null,
    $tipoDeOrden = null
) {
    $arrayEstadoRespuesta = [];
    $arrayRespuestaSoportes = [];
    $consultaEstadoDescargas = [];
    $consultaArreglosHCEParametrizados = consultarAliasPorAplicacion($conex, $wemp_pmla, 'soportesHCE');
    $arraySoportesHCEAConsultar = preg_split("/\,/", $consultaArreglosHCEParametrizados);
    $consultaArreglosImexParametrizados = consultarAliasPorAplicacion($conex, $wemp_pmla, 'soportesImex');
    $arraySoportesImexAConsultar = preg_split("/\,/", $consultaArreglosImexParametrizados);
    $arraySoportes = array();
    $estadoTemporal = [];
    array_push($arraySoportes, $soportes);

    if (array_search($soporte = '03', $arraySoportes) !== false) {
        $ordenesMedicas = consultaOrdenMedica(
            $conex,
            $wemp_pmla,
            $historia,
            $ingreso,
            $codigo_responsable,
            $soporte,
            $_SERVER['SERVER_NAME'].'/matrix/hce/procesos/ordenes_imp.php?',
            'orden medica',
            '/var/www/matrix/hce/procesos/impresion_ordenes/'
        );
        $arrayRespuestaSoportes = devolverRespuestaSoporte($ordenesMedicas, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '22', $arraySoportes) !== false) {
        $miPresMedsNoPbs = consultaMiPresMedsNoPbs(
            $conex,
            $wemp_pmla,
            $historia,
            $ingreso,
            $codigo_responsable,
            $documentoDeIdentidad,
            $tipoDeDocumento,
            $fechadeAdmision,
            $soporte,
            $_SERVER['SERVER_NAME'].'/matrix/hce/procesos/CTCmipres.php?',
            'Mi Pres'
        );
        $arrayRespuestaSoportes = devolverRespuestaSoporte($miPresMedsNoPbs, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '41', $arraySoportes) !== false) {
        $url = $_SERVER['SERVER_NAME'].'/matrix/ips/reportes/formato_furips_x_fac.php?';
        $descripcion = 'Furips';
        $furips = consultaFurips(
            $conex,
            $wemp_pmla,
            $historia,
            $ingreso,
            $codigo_responsable,
            $soporte,
            $factura,
            $url,
            $descripcion,
            '/var/www/matrix/ips/reportes/facturas/'
        );
        $arrayRespuestaSoportes = devolverRespuestaSoporte($furips, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '43', $arraySoportes) !== false) {
        $url = $_SERVER['SERVER_NAME'].'/matrix/ips/procesos/monitorE-facturacion.php?';
        $descripcion = 'Factura PDF';
        $facturaPdf = consultaFacturaPDF(
            $conex,
            $wemp_pmla,
            $historia,
            $ingreso,
            $codigo_responsable,
            $soporte,
            $factura,
            $fuenteDeFactura,
            $url,
            $descripcion
        );
        $arrayRespuestaSoportes = devolverRespuestaSoporte($facturaPdf, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '46', $arraySoportes) !== false) {
        $descripcion = 'Detalle de cargos';
        $url = $_SERVER['SERVER_NAME'].'/matrix/facturacion/reportes/rep_detafactuayuda.php?';
        $detalleDeCargos = consultaDetalleDeCargos(
            $conex,
            $wemp_pmla,
            $historia,
            $ingreso,
            $codigo_responsable,
            $soporte,
            $factura,
            $fuenteDeFactura,
            $url,
            $descripcion
        );
        $arrayRespuestaSoportes = devolverRespuestaSoporte($detalleDeCargos, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '48', $arraySoportes) !== false) {
        $historiaClinica = consultaHistoriaClinica(
            $conex,
            $wemp_pmla,
            $historia,
            $ingreso,
            $codigo_responsable,
            $soporte,
            $_SERVER['SERVER_NAME'].'/matrix/hce/procesos/solimp.php?',
            'Historia Clinica'
        );
        $arrayRespuestaSoportes = devolverRespuestaSoporte($historiaClinica, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '50', $arraySoportes) !== false) {
        $historiaClinica = consultaRegistroAnestesia(
            $conex,
            $wemp_pmla,
            $historia,
            $ingreso,
            $codigo_responsable,
            $soporte,
            $_SERVER['SERVER_NAME'].'/matrix/hce/procesos/solimp.php?',
            'Registro de anestesia'
        );
        $arrayRespuestaSoportes = devolverRespuestaSoporte($historiaClinica, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '66', $arraySoportes) !== false) {
        $url = $_SERVER['SERVER_NAME'].'/matrix/facturacion/reportes/rep_detafactu.php?';
        $descripcion = 'Detalle de materiales';
        $detalleDeMateriales = consultaDetalleDeMateriales(
            $conex,
            $wemp_pmla,
            $historia,
            $ingreso,
            $codigo_responsable,
            $soporte,
            $factura,
            $fuenteDeFactura,
            $url,
            $descripcion
        );
        $arrayRespuestaSoportes = devolverRespuestaSoporte($detalleDeMateriales, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '67', $arraySoportes) !== false) {
        $url = $_SERVER['SERVER_NAME'].'/matrix/movhos/reportes/Hoja_medicamentos_enfermeria_IPODS.php?';
        $descripcion = 'Detalle de medicamentos';
        $hojaDeMedicamentos = ConsultaHojaDeMedicamentos($conex, $wemp_pmla, $historia, $ingreso, $codigo_responsable, $soporte, $url, $descripcion);
        $arrayRespuestaSoportes = devolverRespuestaSoporte($hojaDeMedicamentos, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    if (array_search($soporte = '69', $arraySoportes) !== false) {
        $descripcion = 'laboratorio';
        $clinicaAConsultar = $_SERVER['SERVER_NAME'];
        $laboratorio = ConsultaLaboratorio($conex, $wemp_pmla, $historia, $ingreso, $codigo_responsable, $soporte, 
        $clinicaAConsultar,$tipoDeOrden, $descripcion);
        $arrayRespuestaSoportes = devolverRespuestaSoporte($laboratorio, $historia, $ingreso, $soporte, $descripcion);
        array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
    }

    foreach ($arraySoportesImexAConsultar as $soporteImex) {
        $clinicaAConsultar = $_SERVER['SERVER_NAME'];
        if (array_search($soporteImex, $arraySoportes) !== false) {
            $ayuda = '0700';
            $soportesImex = consultaSoportesImex(
                $conex,
                $wemp_pmla,
                $historia,
                $ingreso,
                $codigo_responsable,
                $soporteImex,
                $concepto,
                $ayuda,
                $descripcion,
                $tipoDeOrden,
                $clinicaAConsultar
            );
            $arrayRespuestaSoportes = devolverRespuestaSoporte($soportesImex, $historia, $ingreso, $soporteImex, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }
    }

    foreach ($arraySoportesHCEAConsultar as $soporteHCE) {
        if (array_search($soporteHCE, $arraySoportes) !== false) {
            $url = $accion == 'appFirmas' ? $urlArmado : $_SERVER['SERVER_NAME'].'/matrix/hce/procesos/solimp.php?';
            $soportesHCE = consultaSoportesHCE(
                $conex,
                $wemp_pmla,
                $historia,
                $ingreso,
                $codigo_responsable,
                $soporteHCE,
                $descripcion,
                $formulario,
                $url,
				$accion
            );
            $arrayRespuestaSoportes = devolverRespuestaSoporte($soportesHCE, $historia, $ingreso, $soporteHCE, $descripcion);
            array_push($arrayEstadoRespuesta, $arrayRespuestaSoportes);
        }
    }

    //Luego de unificado todos los soportes se saca la respuesta general del webservice

    foreach ($arrayEstadoRespuesta as $estados) {
        array_push($estadoTemporal, $estados['status']);
    }

    if (in_array(300, $estadoTemporal, true) || in_array(400, $estadoTemporal, true) || in_array(500, $estadoTemporal, true)) {
        $consultaEstadoDescargas['status'] = 400;
    } 
    else {
        $consultaEstadoDescargas['status'] = 200;
    }

    $consultaEstadoDescargas['responsable'] = $codigo_responsable;
    $consultaEstadoDescargas['result'] = $arrayEstadoRespuesta;
    guardarRespuesta($conex, $wemp_pmla, $historia, $ingreso, $codigo_responsable, $consultaEstadoDescargas, '', '');
    return $consultaEstadoDescargas;
}

/**
 * This function returns an array with the status and result of the query
 * 
 * @return A JSON object with status and result properties.
 */
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
    $responsable,
    $soporte,
    $url
) {

    // Se arma la estructura que va a llamar a la URL
    $urlWemp_pmla = "&wemp_pmla=" . $wemp_pmla;
    $urlHistoria = "&whis=" . $historia;
    $urlIngreso = "&wing=" . $ingreso;
    $adicionales = "&wservicio=*";

    return $url . $urlWemp_pmla . $urlHistoria . $urlIngreso  . "&responsable=" . $responsable . "&soporte=" . $soporte . 
    $adicionales . '&automatizacion_pdfs=';
}

/**
 * Este metodo permite generar las opciones de un CURL para los soportes que se hacen por POST
 * recibe una variable que es general para todos los oportes del HCE
 *
 * @param url[string]	[array de los datos enviados al POST]
 * @param data[array]	[array de los datos enviados al POST]
 * @return [array] Respuesta del servicio
 */

function cargarOpcionesPOST($url,$data,$accion=null)
{
    $options = array(
        CURLOPT_URL                 => $url."&".($accion != null ? $accion : 'automatizacion_pdfs')."=",
        CURLOPT_HEADER              => false,
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_POSTFIELDS          => $data,
        CURLOPT_CUSTOMREQUEST       => 'POST',
    );

    return $options;
}

/**
 * Este metodo permite generar una respuesta si la factura existe para un paciente o no
 * En caso que existe puede avanzar
 * En caso contrario inmediatamente saldra del programa
 * @param url[string]	[array de los datos enviados al POST]
 * @param data[array]	[array de los datos enviados al POST]
 * @return [array] Respuesta del servicio
 */

function validarFactura($factura, $nombreDelSoporte)
{
    if (is_null($factura) || $factura === "N/A") {
        $validarFactura['status'] = '400';
        $validarFactura['result'] = 'La historia-ingreso no posee factura generada para el soporte : ' . $nombreDelSoporte;
        return $validarFactura;
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

function guardarRespuesta($conex, $wemp_pmla, $historia, $ingreso, $responsable, $respuesta, $codError, $descripcion)
{
    $codError = !empty($codError) ? 'ERR18' : ($respuesta['status'] != '200' ? 'ERR17' : null);
    $descripcion = $respuesta['status'] == '200' ? 'se subieron los soportes correctamente' : 'algun soporte no se subio correctamente';
    // Crear un manejador cURL
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }

    $data = array(
        'history' => $historia,
        'entryNumber' => $ingreso,
        'companyCode' => $responsable,
        'support' => NULL,
        'type' => 'R',
        'process' => json_encode($respuesta['result']),
        'auditAction' => '15',
        'dateProcess' => date('Y-m-d'),
        'hourProcess' => date('h:i'),
        'httpCode' => $respuesta['status'],
        'errorCode' => $codError,
        'description' => $descripcion
    );
    //var_dump($data);
    $urlAConsumir = consultarAliasPorAplicacion($conex, $wemp_pmla, 'urlSoportesAutomaticos');
    $opciones = array(
        CURLOPT_URL                 => $urlAConsumir,
        CURLOPT_HEADER              => false,
        CURLOPT_RETURNTRANSFER      => 1,
        CURLOPT_POSTFIELDS          => $data,
        CURLOPT_CUSTOMREQUEST       => 'POST',
    );

    curl_setopt_array($ch, $opciones);
    $respuesta = curl_exec($ch);
    curl_close($ch);
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

function devolverRespuestaSoporte($respuesta,$historia,$ingreso,$soporte,$nombreDelSoporte)
{
    $arrayRespuestaSoportes['status'] = $respuesta['status'] == 200 ? $respuesta['status'] : 400;
    $arrayRespuestaSoportes['codigoDelSoporte'] = $soporte;
    $arrayRespuestaSoportes['nombreDelSoporte'] = $nombreDelSoporte;
    $arrayRespuestaSoportes['descripcion'] = $respuesta['result'];
    $arrayRespuestaSoportes['paciente'] = $historia . '-' . $ingreso;
	if(!empty($respuesta['html'])) {		
		$arrayRespuestaSoportes['html'] = $respuesta['html'];
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

function cargarDom($dom, $wemp_pmla, $codigoFormularioArbol)
{

    if (empty($dom->getElementById('fecha_inicial')) || empty($dom->getElementById('fecha_final'))) {
        $pdf_generado['status'] = '400';
        $pdf_generado['result'] = 'No se ha encontrado datos iniciales para generar PDF en la historia clinica';
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
function recuperarSoporte($historia, $ingreso, $responsable, $wemp_pmla, $soporte, $archivoOrigen)
{

    if (file_exists($archivoOrigen)) {
        $nombreSoporte = $historia . '-' . $ingreso . '-' . $soporte . '.pdf';
        $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
        $archivoDestino = $carpetaResponsable . '/' . $nombreSoporte;
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

function generarPDF($estructura, $wnombrePDF, $historia, $ingreso, $descripcion, $wemp_pmla, $responsable, $hoja = null)
{
    try {
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
        if (file_exists($archivo_dir)) {
            unlink($archivo_dir);
        }

        $f = fopen($archivo_dir, "w+");
        fwrite($f, $html);
        fclose($f);
        $respuesta = shell_exec("./generarPdfSoportes.sh \"" . $wnombrePDF . '-' . $hoja . '"');
        $archivo_origen = $wnombrePDF . '-' . $hoja . '.pdf';
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
            $array_pdf_respuesta['result'] = 'Transferencia completa del soporte:' . ' ' . $descripcion;
        } else {
            $pdf_generado['status'] = 400;
            $pdf_generado['result'] = 'El soporte automatico no se ha podido descargar correctamente:' . ' ' . $descripcion;
        }

        return $array_pdf_respuesta;
    } catch (Exception $e) {
        echo $e->getMessage();
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

function armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, $hoja = null, $conex = null, $accion,
$resultadoAdicional = null)
{
    //Verificamos resultado de la consulta CURL
    $rutaPrincipal = consultarAliasPorAplicacion($conex, $wemp_pmla, 'documentosAutomatizacion');
    $rutaHistoria = $wemp_pmla . '/' . $historia . '-' . $ingreso;

    if ($hoja == null) {
        $nombrePDFautomatizacion = $historia . '-' . $ingreso . '-' . $soporte . '.pdf';
    } else {
        $nombrePDFautomatizacion = $historia . '-' . $ingreso . '-' . $soporte . '-' . '1' . '.pdf';
    }

    $rutaArchivoGuardado = $rutaPrincipal . $rutaHistoria . '/' . $responsable . '/' . $nombrePDFautomatizacion;

	if($accion == 'appFirmas') {
		
		$rutaPrincipalFirmas = $rutaPrincipal.'AppFirmas/'.$wemp_pmla.'/'.$historia.'-'.$ingreso;
		$rutaHeaderHtml = $rutaPrincipalFirmas.'/encabezado_'.$historia.'-'.$ingreso.'-'.$soporte.'.html';
		$rutaBodyHtml = $rutaPrincipalFirmas.'/cuerpo_'.$historia.'-'.$ingreso.'-'.$soporte.'.html';
		$rutaFooterHtml = $rutaPrincipalFirmas.'/pie_'.$historia.'-'.$ingreso.'-'.$soporte.'.html';
		
		$existeHeaderHtml = file_exists($rutaHeaderHtml);
		$existeBodyHtml = file_exists($rutaBodyHtml);
		$existeFooterHtml = file_exists($rutaFooterHtml);
		if($existeHeaderHtml && $existeBodyHtml && $existeFooterHtml) {
			$html = file_get_contents($rutaHeaderHtml);
			$html .= file_get_contents($rutaBodyHtml);
			$html .= file_get_contents($rutaFooterHtml);
			
			//var_dump($html);
			$html = htmlentities($html);
			
			$pdfGenerado['status'] = 200;
			$pdfGenerado['codigoDelSoporte'] = $soporte;
			$pdfGenerado['nombreDelSoporte'] = $nombreDelSoporte;
			$pdfGenerado['result'] = 'Descarga exitosa';
			$pdfGenerado['html'] = $html;
			
		} else {
			$pdfGenerado['status'] = 400;
			$pdfGenerado['codigoDelSoporte'] = $soporte;
			$pdfGenerado['nombreDelSoporte'] = $nombreDelSoporte;
			$pdfGenerado['result'] = 'No se pudo descargar el soporte '.$nombreDelSoporte.', para firmar';
		}
		
		eliminarCarpeta($rutaPrincipalFirmas);	
		
		return $pdfGenerado;
	}

    if (file_exists($rutaArchivoGuardado)) {
        $pdfGenerado['status'] = 200;
        $pdfGenerado['codigoDelSoporte'] = $soporte;
        $pdfGenerado['nombreDelSoporte'] = $nombreDelSoporte;
        $pdfGenerado['result'] = 'Transferencia completa del soporte :' . ' ' . $nombreDelSoporte;
    } else {
        $pdfGenerado['status'] = 400;
        $pdfGenerado['codigoDelSoporte'] = $soporte;
        $pdfGenerado['nombreDelSoporte'] = $nombreDelSoporte;
        if (is_null($resultadoAdicional)) {
            $pdfGenerado['result'] = 'El soporte automatico no se ha podido descargar correctamente:' . ' ' . $nombreDelSoporte;
        } else {
            $pdfGenerado['result'] = $resultadoAdicional;
        }
    }
    return $pdfGenerado;
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

    try {
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);

        //Creacion del array para saber si tiene medicamentos o no
        $examenesAImprimir = array();
        //se hace el sql para decir si tiene mediamentos o no
        $sqlContarOrdMedic = "SELECT count(0) AS cantidad 
        FROM " . $wbasedato . "_000054 mov 
        WHERE mov.Kadhis = '" . $historia . "'
        AND mov.Kading = '" . $ingreso . "';";

        $ordMedicamentos = mysql_query($sqlContarOrdMedic, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerTiposOrden):</b><br>" .
            mysql_error());
        $ordMedicamentos = mysql_fetch_assoc($ordMedicamentos);

        if ($ordMedicamentos['cantidad'] > 0) {
            array_push($examenesAImprimir, 'medtos');
        }

        //Se hace el sql para buscar las cantidades de opciones que debe de imprimirse
        $sqlObtenerTiposOrden = "SELECT DISTINCT hcq.Codigo, hcq.Descripcion 
        FROM " . $whce . "_000015 hcq
        INNER JOIN " . $whce . "_000027  hcv on hcq.Codigo = hcv.Ordtor
        WHERE Ordhis = '" . $historia . "'
        AND Ording = '" . $ingreso . "'
        ORDER BY 2 ASC;";

        $tiposOrdenes = mysql_query($sqlObtenerTiposOrden, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerTiposOrden):</b><br>" .
            mysql_error());

        while ($rowTiposOrdenes = mysql_fetch_array($tiposOrdenes)) {
            array_push($examenesAImprimir, $rowTiposOrdenes["Codigo"]);
        }

        $separado = implode(",", $examenesAImprimir);

        // Se arma la estructura que va a llamar a la URL

        $urlWemp_pmla = "wemp_pmla=" . $wemp_pmla;
        $urlHistoria = "&whistoria=" . $historia;
        $urlIngreso = "&wingreso=" . $ingreso;
        $adicionales = '&tipoimp=imp&alt=off&pacEps=off&wtodos_ordenes=on&orden=asc&origen=' . '&arrOrden=' . $separado . 
        '&desdeImpOrden=on';
        $urlOrdenMedica = $url . $urlWemp_pmla . $urlHistoria . $urlIngreso  . $adicionales .  "&soporte=" . $soporte .
        '&automatizacion_pdfs=';

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
        echo $e->getMessage();
        return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex ,null);
    }

    return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
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

    try {
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
            $url = $url . "&soporte=" . $soporte;
            $opciones = cargarOpcionesPOST($url, $data);
            curl_setopt_array($ch, $opciones);
            $respuesta = curl_exec($ch);
            curl_close($ch);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
    }
    return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, '1', $conex, null);
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
    $nombreDelSoporte,
    $rutaOrigen
) {
    try {
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);

        //Se valida que venga la factura y no sea NULL
        $validarFactura = validarFactura($factura, $nombreDelSoporte);

        if ($validarFactura['status'] != '400') {

            //se inicia el curl
            $ch = curl_init();
            $data = array(
                'wemp_pmla'                     => $wemp_pmla,
                'wnumero'                       => $factura,
                'aceptar'                       => 'Aceptar',
                'wgrupo'                        => '0',
                'bandera'                       => '1',
            );

            $opciones = cargarOpcionesPOST($urlFurips, $data);
            curl_setopt_array($ch, $opciones);
            curl_exec($ch);
            curl_close($ch);

            $archivoOrigen = $rutaOrigen . $factura . '.pdf';
            recuperarSoporte($historia, $ingreso, $responsable, $wemp_pmla, $soporte, $archivoOrigen);
            return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
        } else {
            return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex, null,
            $validarFactura['result']);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
    }
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
    $nombreDelSoporte
) {

    try {
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);

        //Se valida que venga la factura y no sea NULL
        $validarFactura = validarFactura($factura, $nombreDelSoporte);

        if ($validarFactura['status'] != '400') {

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

            $UrlFacturaPDF = $url . "historia=" . $historia . "&ingreso=" . $ingreso . "&responsable=" . $responsable . 
            "&soporte=" . $soporte;

            $opciones = cargarOpcionesPOST($UrlFacturaPDF, $data);
            curl_setopt_array($ch, $opciones);
            $respuesta = curl_exec($ch);
            curl_close($ch);
            $respuestaCenFinanciero = 'No se pudo obtener el archivo pdf desde el cen financiero';
            $conexionBDFacturacion = utf8_decode('No se realizo conexión con la BD de Facturación');
            $validarConexionBDFacturacion = strpos($respuesta, $conexionBDFacturacion);
            $posicionCoincidencia = strpos($respuesta, $respuestaCenFinanciero);
            if ($posicionCoincidencia === false && $validarConexionBDFacturacion === false) {
                return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
            }
            else{
                return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex, null , 'error al consumir el pdf');
            }
        } else {
            return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null , $validarFactura['result']);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
    }
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
    $urlDetalleDeCargos,
    $nombreDelSoporte
) {
    try {
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);

        //Se valida que venga la factura y no sea NULL
        $validarFactura = validarFactura($factura, $nombreDelSoporte);

        if ($validarFactura['status'] != '400') {

            //Se inicia curl
            $ch = curl_init();

            $data = array(
                'usuario'                => $wemp_pmla,
                'fte'                    => $fuenteDeFactura,
                'fac'                    => $factura,
                'com'                    => 'a',
            );

            $opciones = cargarOpcionesPOST($urlDetalleDeCargos, $data);
            curl_setopt_array($ch, $opciones);
            $response = curl_exec($ch);
            curl_close($ch);

            $facturaEnSaldoCero = 'no tiene factura';
            $usuarioNoAutenticado = 'Usuario no autenticado';
            $conexionBDFacturacion = utf8_decode('No se realizo conexión con la BD de Facturación');
            $posicion_coincidencia = strpos($response, $facturaEnSaldoCero);
            $posicionUsuarioAutenticado = strpos($response, $usuarioNoAutenticado);
            $validarConexionBDFacturacion = strpos($response, $conexionBDFacturacion);
            if ($posicion_coincidencia === false && $posicionUsuarioAutenticado === false && $validarConexionBDFacturacion === false) {
                $nombrePDFDetalleDeCargos = $historia . '-' . $ingreso . '-' .  $soporte;
                return generarPDF(
                    htmlentities($response),
                    $nombrePDFDetalleDeCargos,
                    $historia,
                    $ingreso,
                    $nombreDelSoporte,
                    $wemp_pmla,
                    $responsable,
                    null
                );
            } else {
                return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex, null ,
                'No tiene asociado ningun detalle de cargo');
            }
        } else {
            return armarRespuesta($nombreDelSoporte, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex, null ,
            $validarFactura['result']);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
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
    $descripcion
) {

    //se valida credenciales
    validarIngreso($conex, $wemp_pmla);
    // Se arma la estructura que va a llamar a la URL
    $url = armarURLHistoriaClinica($wemp_pmla, $historia, $ingreso, $responsable, $soporte, $url);
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
    $sinFormulariosPaciente  = 'El usuario no tiene formularios firmados';
    $formulariosPaciente = 'NO EXISTEN FORMULARIOS DILIGENCIADOS PARA EL PACIENTE';
    $validacionSinFormulariosPaciente = strpos($ret, $sinFormulariosPaciente);
    $validacionFormulariosPaciente = strpos($ret, $formulariosPaciente);
    if ($validacionSinFormulariosPaciente === false && $validacionFormulariosPaciente === false) {
        $dom = new DOMDocument();
        @$dom->loadHTML($ret);

        if (empty($dom->getElementById('fecha_inicial'))) {
            $pdf_generado['status'] = '400';
            $pdf_generado['result'] = 'El soporte automatico no se ha podido descargar correctamente';
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
        $wespecial           = "TODAS";
        $fechaIni_def        = "";
        $fechaFin_def        = "";
        $formulariosElegidos = "";
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

        $opciones = cargarOpcionesPOST($url, $data);
        curl_setopt_array($ch, $opciones);
        $response = curl_exec($ch);
        curl_close($ch);
    } else {
        $pdf_generado['status'] = '400';
        $pdf_generado['result'] = 'El soporte automatico no se ha podido descargar correctamente';
        return $pdf_generado;
    }

    return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
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
    $descripcion
) {
    try {
        validarIngreso($conex, $wemp_pmla);

        // Se arma la estructura que va a llamar a la URL
        $UrlHistoriaClinica = armarURLHistoriaClinica($wemp_pmla, $historia, $ingreso, $responsable, $soporte, $url);
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

            $opciones = cargarOpcionesPOST($UrlHistoriaClinica, $data);
            curl_setopt_array($ch, $opciones);
            $response = curl_exec($ch);
            curl_close($ch);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
    }

    return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, null, $conex , null);
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
    $urlDetalleDeMateriales,
    $descripcion
) {
	$codError = '';
	try{
        //se valida credenciales
        validarIngreso($conex, $wemp_pmla);
        $usuarioAutomatizacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'usuarioAutomatizacionDetalleDeCargos');
        //Se valida que venga la factura y no sea NULL
        $validarFactura = validarFactura($factura,$descripcion);

        //traer resultado de nueva eps
        $nitNuevaEps = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nitNuevaEps');
        
        $eps = $responsable != $nitNuevaEps ? 'N' : 'S';
       
        if($validarFactura['status'] != '400'){
            $ch = curl_init();
            $data = array(
                'usuario'                => $usuarioAutomatizacion,
                'fte'                    => $fuenteDeFactura,
                'fac'                    =>  $factura,
                'com'                    => '*',
                'atc'                    => 'N',
                'cneps'                  => $eps
            );

            $opciones = cargarOpcionesPOST($urlDetalleDeMateriales,$data);
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
            htmlentities($response),
            $nombrePDFDetalleDeMateriales,
            $historia,
            $ingreso,
            $descripcion,
            $wemp_pmla,
            $responsable,
            null
        );
    } 
    else {
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex, null);
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
    try {

        validarIngreso($conex, $wemp_pmla);
        $query_rango = "SELECT  Ubihis, Ubiing, Ubialp as alta_pac,movhos18.Fecha_data AS fecha_ingreso,movhos18.Hora_data, Ubifad AS 
            fecha_alta,Ubihad,Ccocod,Cconom
            FROM " . $wbasedato . "_000018 AS movhos18
            LEFT JOIN " . $wbasedato . "_000011 AS movhos11
            ON Ccocod=Ubisac
            WHERE   Ubihis = '" . $historia . "'
            AND Ubiing = '" . $ingreso . "'
            ORDER BY    movhos18.fecha_data";

        $res_r = mysql_query($query_rango, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $query_rango . " - " . 
        mysql_error());
        $numreg = mysql_num_rows($res_r);
        $row_r = mysql_fetch_array($res_r);
        if ($numreg > 0) {
            $rango = array(
                'whis'          => $row_r['Ubihis'],
                'wing'          => $row_r['Ubiing'],
                'Ccocod'        => $row_r['Ccocod'],
                'Cconom'        => $row_r['Cconom'],
                'alta_pac'      => $row_r['alta_pac'],
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
            $url = $domain . $urlHistoria . $urlIngreso . $urlCentroDeCosto . $urlWemp_pmla . $urlTipoPos . $urlFecha . 
            '&automatizacion_pdfs=';
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
                $pdf_generado['result'] = 'El soporte automatico no se ha podido descargar correctamente: Hoja de medicamentos';
            } else {
                $info = curl_getinfo($ch);
                curl_close($ch); // close cURL handler

                if (empty($info['http_code']) || $info['http_code'] !== 200) {
                    $pdf_generado['status'] = '400';
                    $pdf_generado['result'] = 'El soporte automatico no se ha podido descargar correctamente: Hoja de medicamentos';
                } else {
                    // load the HTTP codes
                    $nombrePDFHojaDeMedicamentos = $historia . '-' . $ingreso . '-' . $soporte;
                    if ($posicion_coincidencia === false) {
                        $llamadoPDF = $llamadoPDF + 1;
                        $pdf_generado = generarPDF(
                            htmlentities($ret),
                            $nombrePDFHojaDeMedicamentos,
                            $historia,
                            $ingreso,
                            $descripcion,
                            $wemp_pmla,
                            $responsable,
                            $numero_de_hojas
                        );
                    }
                    else{
                        $pdf_generado['status'] = '400';
                        $pdf_generado['result'] = 'No se le han procesado medicamentos al paciente';                        
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
        echo $e->getMessage();
    }
    return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, '1', $conex , null);
}

/**
 * This function is used to generate the PDFs for the soporte automatico "LABORATORIO".
 * 
 * @param conex The connection to the database.
 * @param wemp_pmla The PMLA code of the application.
 * @param historia The patient's history number.
 * @param ingreso The patient's admission number.
 * @param responsable The name of the person who is responsible for the document.
 * @param soporte The name of the soporte.
 * @param clinicaAConsultar The URL of the clinic that is being consulted.
 * @param descripcion The description of the error.
 * 
 * @return an array with the following keys:
 *  Estado de la descarga del PDF
 *  Paciente que le descargo el PDF
 *  Ubicacion de la ruta del documento PDF
 */
function ConsultaLaboratorio(
    $conex,
    $wemp_pmla,
    $historia,
    $ingreso,
    $responsable,
    $soporte,
    $clinicaAConsultar,
    $tipoDeOrden,
    $descripcion
) {

    global $whce;
    global $wbasedato;
    
    try {		
		$sacarUrlPorSql = getBDConsult($whce,$tipoDeOrden,$ingreso,$historia,$wbasedato,$conex);
        $resLaboratorio = mysql_query($sacarUrlPorSql, $conex) or 
        die("Error: " . mysql_errno() . " - en el query: " . $sacarUrlPorSql . " - " . mysql_error());
        $numLaboratorio = mysql_num_rows($resLaboratorio);
        if ($numLaboratorio > 0) {
            $numeroDelPDF = 1;
            if ($clinicaAConsultar == 'http://matrix-portoazul.lasamericas.com.co/'){
                while ($rowsLaboratorio = mysql_fetch_array($resLaboratorio)) {
                    $urlCompleta = $rowsLaboratorio['Deturp'];
                    $verificarURL = explode(",", $urlCompleta);
                    foreach ($verificarURL as $resultadoLaboratorio) {
                        $resultadoUrlLaboratorio =  $_SERVER['SERVER_NAME'] . $resultadoLaboratorio . '&automatizacion_pdfs=';
                        $ch = curl_init();
                        if (!$ch) {
                            die("Couldn't initialize a cURL handle");
                        }
    
                        // opciones del cURL
                        curl_setopt($ch, CURLOPT_URL, $resultadoUrlLaboratorio);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    
                        //Ejecutar cURL
                        $response = curl_exec($ch);
                        $sinLaboratoriosPaciente  = 'El usuario no tiene laboratorios firmados';
                        $validacionSinLaboratoriosPaciente = strpos($response, $sinLaboratoriosPaciente);
                        if ($validacionSinLaboratoriosPaciente === false) {
                            $nombreLaboratorio = $historia . '-' . $ingreso . '-' . $soporte;
                            generarPDF(
                                htmlentities($response),
                                $nombreLaboratorio,
                                $historia,
                                $ingreso,
                                $descripcion,
                                $wemp_pmla,
                                $responsable,
                                $numeroDelPDF
                            );
                            $numeroDelPDF += 1;
                        }
                    }
                }
            }
            else{
                while ($rowsLaboratorio = mysql_fetch_array($resLaboratorio)) {
                    $urlCompleta = $rowsLaboratorio['Deturp'];
                    $verificarURL = explode(",", $urlCompleta);
                    foreach ($verificarURL as $resultadoLaboratorio) {
                        $verificarExtensionPDF = substr($resultadoLaboratorio, -4);
                        if ($verificarExtensionPDF === '.pdf') {
                            $nombreLaboratorio = $historia . '-' . $ingreso . '-' . $soporte . '-' .  $numeroDelPDF;
                            $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                            $archivoDestino = $carpetaResponsable . '/' . $nombreLaboratorio . ".pdf";
                            if (copy($resultadoLaboratorio, $archivoDestino)) {
                                $numeroDelPDF += 1;
                            }
                        } else {
                            $urlAmazon = consultarAliasPorAplicacion($conex, $wemp_pmla, 'urlActualizarLaboratorio');
                            $mysqli = new mysqli('131.1.18.106:3306', '4D_UCONSULTA', 'NTmUsQbPvRC2UD74', '4dlab');
    
                            $subStringUrlOriginal = recortarUrl($urlAmazon, $resultadoLaboratorio);
                            $agregarCaracteresParaSQL = '%' . $subStringUrlOriginal . '%';
                            $sqlUrlLaboratorio = "SELECT *
                            FROM " . 'OT' . " mov 
                            WHERE mov.NumeroEnLaWeb LIKE '$agregarCaracteresParaSQL';";
    
                            $resultado = $mysqli->query($sqlUrlLaboratorio) or 
                            die("<b>ERROR EN QUERY MATRIX(sqlUrlLaboratorio ):</b><br>" .$mysqli->error);
    
                            while ($fila = $resultado->fetch_assoc()) {
                                $url = $fila['NumeroEnLaWeb'];
                            }
    
                            $resultadoUrlLaboratorio = $urlAmazon . $url . '.pdf';
                            mysqli_close($mysqli);
    
                            if (!empty($resultadoUrlLaboratorio) && !empty($resultadoLaboratorio)) {
                                $nombreLaboratorio = $historia . '-' . $ingreso . '-' . $soporte . '-' .  $numeroDelPDF;
                                $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                                $archivoDestino = $carpetaResponsable . '/' . $nombreLaboratorio . ".pdf";
                                if (copy($resultadoUrlLaboratorio, $archivoDestino)) {
                                    $numeroDelPDF += 1;
                                }
                            }
                        }
                    }
                }                
            }
        }
        else {
            $respuestaLaboratorio['status'] = '400';
            $respuestaLaboratorio['result'] = 'El soporte automatico no se ha podido descargar correctamente:' . ' ' . $descripcion;
            return $respuestaLaboratorio;
        }
        return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, '1', $conex  , null);
    }
    catch (Exception $e) {
        echo $e->getMessage();
        return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, '1', $conex , null);
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
    $descripcion,
    $tipoDeOrden,
    $clinicaAConsultar
) {

    global $wbasedato;
    global $wcliame;
    global $whce;

    try {
        //Se valida si tiene la conexion a matrix y cual es su empresa
        validarIngreso($conex, $wemp_pmla);
        if ($clinicaAConsultar == 'http://matrix-portoazul.lasamericas.com.co/'){
            $sacarUrlPorSql = getBDConsult($whce,$tipoDeOrden,$ingreso,$historia,$wbasedato,$conex);
            $resPrescripciones = mysql_query($sacarUrlPorSql, $conex) or 
            die("Error: " . mysql_errno() . " - en el query: " . $sacarUrlPorSql . " - " . mysql_error());
            $numPrescripciones = mysql_num_rows($resPrescripciones);

            if ($numPrescripciones > 0) {
                $numeroDelPDF = 1;
                while ($rowsPrescripciones = mysql_fetch_array($resPrescripciones)) {
                    //Se nombra el archivo, se extrae la url y se lleva al intercambiador.
                    $urlCompleta = $rowsPrescripciones['Deturp'];
                    $verificarURL = explode(",", $urlCompleta);
                    foreach ($verificarURL as $resultadoImagenologia) {
                        $ch = curl_init();
                        if (!$ch) {
                            die("Couldn't initialize a cURL handle");
                        }
                        // Instruct cURL to store cookies it receives in this file.
                        curl_setopt($ch, CURLOPT_COOKIEJAR, './cookies.txt');
                        curl_setopt_array($ch, array(
                            CURLOPT_URL => $resultadoImagenologia,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_COOKIESESSION => 1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_COOKIEFILE => './cookies.txt',
                        ));
                        
                        $response = curl_exec($ch);                     
                        curl_close($ch);

                        $nombreImagenologia = $historia . '-' . $ingreso . '-' . $soporte . '-' .  $numeroDelPDF;
                        $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                        $archivoDestino = $carpetaResponsable . '/' . $nombreImagenologia . ".pdf";
                        file_put_contents($archivoDestino,$response);
                        $numeroDelPDF += 1;
                    }
                }
            }
        }
        else{
            //Se inidica la fecha inicial en que comenzo la interoperabilidad con Imex

            $fechaInicioImex = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fechaInicioImex');
            
            //Query para sacar la Url de Imex para cada uno de los soportes asociados a los pacientes
            // la fecha de inicio imex es 2020-10-01

            $sacarUrlPorSql = "SELECT mvcurp
                FROM " . $wbasedato . "_000268 a
                JOIN " . $wcliame . "_000192
                ON (Mvccup = Hompom
                AND FIND_IN_SET(Homcos,'$concepto') 
                AND Homcom = $ayuda) 
                WHERE Mvchis = $historia
                AND Mvcing = $ingreso
                AND a.Fecha_data > $fechaInicioImex 
                GROUP BY 1;";

            $resPrescripciones = mysql_query($sacarUrlPorSql, $conex) or 
            die("Error: " . mysql_errno() . "- en el query: " . $sacarUrlPorSql . " - " . mysql_error());
            $numPrescripciones = mysql_num_rows($resPrescripciones);

            //Se valida si tiene algun registro para recorrerlo
            if ($numPrescripciones > 0) {

                $numeroDelPDF = 1;
                while ($rowsPrescripciones = mysql_fetch_array($resPrescripciones)) {

                    //Se nombra el archivo, se extrae la url y se lleva al intercambiador.
                    $url = $rowsPrescripciones['mvcurp'];
                    if ($url !== null) {
                        if (stripos($url, ' /_logo')) {
                            $url = strstr($url, ' /_logo', true);
                        }
                        // Crear un manejador cURL
                        $ch = curl_init();
                        if (!$ch) {
                            die("Couldn't initialize a cURL handle");
                        }

                        // opciones del cURL
                        $ret = curl_setopt($ch, CURLOPT_URL, $url);
                        $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $ret = curl_setopt($ch, CURLOPT_HEADER, 0);

                        //Ejecutar cURL
                        $ret = curl_exec($ch);
                        if ($ret == false) {
                            echo curl_error($ch);
                        }
                        $regexImex = consultarAliasPorAplicacion($conex, $wemp_pmla, 'regexImex');
                        preg_match_all($regexImex, $ret, $match);
                        $versionPDF = consultarAliasPorAplicacion($conex, $wemp_pmla, 'versionPDF');
                        if (count($match[0]) > 1) {
                            $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                            foreach ($match[0] as $soporteUrl) {
                                $nombreImex =   $historia . '-' . $ingreso . '-' . $soporte . '-' .  $numeroDelPDF;
                                $archivoDestino = $carpetaResponsable . '/' . $nombreImex . ".pdf";
                                copy($soporteUrl, $archivoDestino);
                                modificarFuncion($archivoDestino, $versionPDF);
                                chmod($archivoDestino, 0777);
                                $numeroDelPDF += 1;
                            }
                        } else {
                            $nombreImex =   $historia . '-' . $ingreso . '-' . $soporte . '-' .  $numeroDelPDF;
                            $carpetaResponsable = creacionCarpeta($wemp_pmla, $historia, $ingreso, $responsable);
                            $archivoDestino = $carpetaResponsable . '/' . $nombreImex . ".pdf";
                            copy($url, $archivoDestino);
                            modificarFuncion($archivoDestino, $versionPDF);
                            chmod($archivoDestino, 0777);
                            $numeroDelPDF += 1;
                        }
                    } else {
                        $respuestaImex['status'] = '400';
                        $respuestaImex['descripcion'] = 'No se le ha generado el soporte:' . $descripcion;
                        return $respuestaImex;
                    }
                }
            }
        }
    }
    catch (Exception $e) {
        echo $e->getMessage();
        return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, '1', $conex , null);
    }
    //Se verifica la respuesta fue exitosa, es porque se creo el PDF correctamente
    return armarRespuesta($descripcion, $wemp_pmla, $historia, $ingreso, $responsable, $soporte, '1', $conex , null);
}

/**
 * Este metodo permite cambiar la version del pdf de un documento
 * recibiendo como parametro donde se encuentra el archivo y la version del pdf a cambiar
 *
 * @param conex_unix[object]		[Conexión a unix]
 * @param wemp_pmla[object]		    [Conexión a empresa]
 * 
 * @return [json] Respuesta Json con 200 si fue correcto o 400 si fue errado
 */

function modificarFuncion($path, $version)
{
    $fileName = (basename($path, '.pdf'));
    $temporalName = $fileName . '-temp';
    $temporalPath = explode('/', $path);
    array_pop($temporalPath);
    $temporalPath = implode('/', $temporalPath) . '/' . $temporalName . '.pdf';
    $command = 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=' . $version . ' -dNOPAUSE -dBATCH -sOutputFile=' . $temporalPath . ' ' . $path;
    $exitCode = 0;
    $output = '';
    exec($command, $output, $exitCode);
    if ($exitCode == 0) {
        $oldPath = str_replace($fileName, $temporalName, $path);
        copy($oldPath, $path);
        unlink($oldPath);
    }

    return;
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
	$accion = null
){
	$codError = '';
    try{
        validarIngreso($conex, $wemp_pmla);
		
        // Se arma la estructura que va a llamar a la URL
        $urlHistoriaClinica = $accion == 'appFirmas' ? $url : armarURLHistoriaClinica($wemp_pmla,$historia,$ingreso,$responsable,$soporte,$url);
		//borrar
		//die($urlHistoriaClinica);
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
		//BORRAR
		//print_r($ret);
		
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
            $opciones = cargarOpcionesPOST($urlHistoriaClinica,$data,$accion);        
            curl_setopt_array($ch,$opciones);
            $response = curl_exec($ch);
			//borrar
			//echo($response);			
            curl_close($ch);
			//return;
        }
    }
    catch (Exception $e) {
        $codError = $e->getCode();
        return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex, null);
    }
    return armarRespuesta($descripcion,$wemp_pmla,$historia,$ingreso,$responsable,$soporte,null,$conex, $accion);
}

/**
 * Calculate the number of days between two dates
 * 
 * @param fecha1 The first date in the format of "Y-m-d H:i:s"
 * @param fecha2 The date you want to compare to.
 * 
 * @return The number of days between the two dates.
 */
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

/**
 * 
 * @param fecha The date you want to add or subtract days to.
 * @param dias The number of days to add or subtract.
 * @param accion 'resta' or 'suma'
 * @param horas the number of hours to add or subtract from the date.
 * 
 * @return The date of the day after the date in the parameter.
 */
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

/**
 * Given a URL, return the part of the URL that comes after the specified string
 * 
 * @param urlAmazon The URL of the Amazon page.
 * @param urlMatrix The URL of the Matrix page.
 * 
 * @return The substring of the urlMatrix variable that starts at the index of the first occurrence of
 * the urlAmazon variable.
 */
function recortarUrl($urlAmazon, $urlMatrix)
{
    if (!is_bool(strpos($urlMatrix, $urlAmazon)))
        return substr($urlMatrix, strpos($urlMatrix, $urlAmazon) + strlen($urlAmazon));
};

function eliminarCarpeta($carpeta) {
	if(is_dir($carpeta)) {
		foreach(glob($carpeta . "/*") as $archivos_carpeta){
			if (is_dir($archivos_carpeta)){
				eliminarCarpeta($archivos_carpeta);
			} else {
				unlink($archivos_carpeta);
			}
		}
		rmdir($carpeta);
	}
}

/**
 * This function returns a query that will return all the urls for the given parameters
 * 
 * @param whce the name of the database where the data is stored
 * @param tipoDeOrden The type of order (e.g. "A04" for Imagenologia, "A20" for laboratorio, etc.)
 * @param ingreso The number of the patient's admission.
 * @param historia the history number
 * @param wbasedato the name of the database
 * @param conex The connection to the database.
 * 
 * @return The query that will be executed in the database.
 */
function getBDConsult($whce,$tipoDeOrden,$ingreso,$historia,$wbasedato,$conex){

    $sacarResultados = "SELECT Eexcod
    FROM " . $wbasedato . "_000045 o
    WHERE Eexcas = 'on'";

    $resResultados = mysql_query($sacarResultados, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $resResultados . 
    " - " . mysql_error());
    $numResultados = mysql_num_rows($resResultados);

    $having = '';

    if ($numResultados > 0) {
        $having = 'having(';
        while ($row = mysql_fetch_array($resResultados)) {
            $having .= 'NOT FIND_IN_SET("' . $row['Eexcod'] . '",estados) AND ';
        }

        $having = substr($having, 0, -5);
        $having .= ')';
    }

    $sqlLimitGroupConcat = 'SET SESSION group_concat_max_len = 2000000;';
    mysql_query($sqlLimitGroupConcat, $conex) or 
    die("Error: " . mysql_errno() . " - en el query: " . $sqlLimitGroupConcat . " - " . mysql_error());

    $sacarUrlPorSql = "SELECT GROUP_CONCAT(Detotr) Detotr, GROUP_CONCAT(Deturp) Deturp, GROUP_CONCAT(estados) estados FROM (
        SELECT Detotr,GROUP_CONCAT(DISTINCT od.Deturp) Deturp, GROUP_CONCAT(od.Detesi) estados        
        FROM " . $whce . "_000027 o
        INNER JOIN " . $whce . "_000028 od ON o.Ordnro = od.Detnro
        WHERE Ordtor = '$tipoDeOrden'
        AND Ording = $ingreso
        AND Ordhis = $historia
        AND od.Detesi <> 'C'
        AND Deturp <> ''
        GROUP BY od.Detotr
        ) AS subSql
        $having;";

    return $sacarUrlPorSql;
}

// Se valida si la acción fue enviada por GET o por POST para las pruebas
    if (isset($_REQUEST['accionGet'])){        
		if($_GET['accionGet'] == 'pruebaSebastian'){
            $arrayRespuesta = clientePrea();
        } else if($_GET['accionGet'] == 'appFirmas'){
            shell_exec("./deleteFolderAutomaticSupportCron.sh");
			$arrayRespuesta = ConsultaEstadoWithGet($conex,$_GET['wemp_pmla'],$_GET['historia'],$_GET['ingreso'],$_GET['responsable'],
            $_GET['documentoDeIdentidad'],$_GET['tipoDeDocumento'],$_GET['fechaDeAdmision'],$_GET['numeroFactura'],$_GET['fuenteFactura'],
            $_GET['descripcion'],$_GET['formulario'],$_GET['concepto'],$_GET['soportes'],$_GET['accionGet'],$_GET['url']);
		}else{
		    shell_exec("./deleteFolderAutomaticSupportCron.sh");
            $arrayRespuesta = ConsultaEstadoWithGet($conex,$_GET['wemp_pmla'],$_GET['historia'],$_GET['ingreso'],$_GET['responsable'],
            $_GET['documentoDeIdentidad'],$_GET['tipoDeDocumento'],$_GET['fechaDeAdmision'],$_GET['numeroFactura'],$_GET['fuenteFactura'],
            $_GET['descripcion'],$_GET['formulario'],$_GET['concepto'],$_GET['soportes'],null,null,$_GET['tipoDeOrden']);
		}
    }
    else if(isset($_REQUEST['accionPost'])){
        shell_exec("./deleteFolderAutomaticSupportCron.sh");
        $arrayRespuesta = ConsultaEstadoWithPost($conex, $_GET['wemp_pmla'], $_POST['patients']);        
    }
    else{
        $arrayRespuesta = ConsultaErronea();        
    }

// Respuesta codificada en Javascript 
header('Content-type: text/javascript');
echo json_encode($arrayRespuesta, JSON_PRETTY_PRINT);