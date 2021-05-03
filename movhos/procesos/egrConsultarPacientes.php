<?php
/** Se inicializa el bufer de salida de php **/
ob_start();

include_once("conex.php");
include_once("root/comun.php");
require_once('PacienteEgreso.php');
include_once('egrFunctions.php');
include_once("root/erp_unix_egreso.php");

set_time_limit (6000);
//$wemp_pmla = "01";
//$wcco0 = ["todos"]; // arreglo de centros de costos
//$whisconsultada = "";
//$cemp = ["todos"]; // arreglo de codigos de empresa
//$mostrarConteo = 0;

$wemp_pmla = $_POST['wemp_pmla'];
$wcco0 = $_POST['wcco0']; // arreglo de centros de costos
$whisconsultada = $_POST['whisconsultada'];
$cemp = $_POST['cemp']; // arreglo de codigos de empresa
$mostrarConteo = $_POST['mostrar_conteo'];


$wtablacliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

/** Consultamos las historias e ingresos para dar egreso**/
$q = consultarPacientesEgresoSQL($wbasedato, $wcliame, $wemp_pmla, $whisconsultada, $wcco0, $cemp);
$res = mysql_query($q, $conex);

/** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
ob_end_clean();

if ($mostrarConteo) {
    $totalHistorias = mysql_num_rows($res);
    print_r(json_encode($totalHistorias));
} else {
    /** Se incluye este archivo después de haber realizado la consulta ya que contiene variables de conexión que cambian**/
    include('egrGuardarDatos.php');

    $arrayDatosConsulta = array();

    $arrayPacientesFinalizados = array();
    $arrayPacientesError = array();

    while ($row = mysql_fetch_assoc($res)) {

        $fechaAltDefinitiva = date("Y-m-d");
        $horaAltaDefinitiva = date("H:i:s");

        /** Concatenamos el nombre del paciente para mostrar en el JSON **/
        $pacienteNombre = "";
        $pacienteNombre .= strlen($row['pacno1']) > 1 ? $row['pacno1'] . " " : "";
        $pacienteNombre .= strlen($row['pacno2']) > 1 ? $row['pacno2'] . " " : "";
        $pacienteNombre .= strlen($row['pacap1']) > 1 ? $row['pacap1'] . " " : "";
        $pacienteNombre .= strlen($row['pacap2']) > 1 ? $row['pacap2'] : "";


        $pacienteEgreso = new PacienteEgreso();
        $pacienteEgreso->documento = $row['pacced'];
        $pacienteEgreso->tipo_documento = $row['pactid'];
        $pacienteEgreso->paciente = utf8_encode($pacienteNombre);
        $pacienteEgreso->historia = $row['historia'];
        $pacienteEgreso->ingreso = $row['ingreso'];
        $pacienteEgreso->ccoEgreso = $row['ubisac'];
        $pacienteEgreso->fechaAltDefinitiva = $fechaAltDefinitiva;
        $pacienteEgreso->horaAltDefinitiva = $horaAltaDefinitiva;

        //consultar los datos
        $pacienteEgreso->data = mostrarDatosAlmacenados($pacienteEgreso);
        //realizar el egreso
        $pacienteEgreso->errores = guardarDatos($pacienteEgreso);
        unset($pacienteEgreso->data);
        $pacienteEgreso->descripcion = utf8_encode($pacienteEgreso->errores["mensaje"]);

        $url = "../../admisiones/procesos/egreso_erp.php?c_param=1&wemp_pmla=" . $wemp_pmla . "&documento=" . $pacienteEgreso->documento . "&wtipodoc=" . $pacienteEgreso->tipo_documento . "&historia=" . $pacienteEgreso->historia . "&ingreso=" . $pacienteEgreso->ingreso . "&ccoEgreso=" . $pacienteEgreso->ccoEgreso . "&fechaAltDefinitiva=" . $fechaAltDefinitiva . "&horaAltDefinitiva=" . $horaAltaDefinitiva;
        $pacienteEgreso->url = $url;

        if ($pacienteEgreso->errores['error'] > 0) {
            array_push($arrayPacientesError, $pacienteEgreso);
        } else {

            array_push($arrayPacientesFinalizados, $pacienteEgreso);
        }
    }
    $arrayDatosConsulta['finalizados'] = $arrayPacientesFinalizados;
    $arrayDatosConsulta['fallidos'] = $arrayPacientesError;

    ob_end_clean();

    print_r(json_encode($arrayDatosConsulta, JSON_UNESCAPED_SLASHES));
}
