<?php
/* *
* Script para generar Kardex automáticamente para Servicio Domiciliario
* luego de haber ejecutado scripts para egresar automáticamente y para admisiones automáticas.
* @author: Julian Mejia - julian.mejia@lasamericas.com.co
*
*/
/** Se inicializa el bufer de salida de php **/
ob_start();
/**************************************
 * INCLUDES
 **************************************/
include_once( __DIR__ . "/funcionesKardexAutomaticosSD.php"); 
include_once("root/comun.php"); // fn consultarAliasPorAplicacion

/********************************
 * INICIO DEL CÓDIGO
 ********************************/

$mostrarConteo = false;
if (isset($_POST['mostrar_conteo'])){
	$mostrarConteo = $_POST['mostrar_conteo'];
	if ($mostrarConteo == '0') $mostrarConteo = false;
}
//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos' );
$wuser = 'movhos';
$usuario = consultarUsuarioKardex($wuser,$wbasedato);
$data = [];
/**En esta funcion se consultan las historias y los ingresos que van a ser procesadas
 *  y luego se le genera el kardex automatico para cada una */
$data = consultarPacientes($conex, $wbasedato,$mostrarConteo);
/** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
ob_end_clean();
print_r(json_encode($data,JSON_UNESCAPED_SLASHES));
?>