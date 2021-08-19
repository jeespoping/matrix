<?php
/******************************************************************************
 * INICIO DEL PROGRAMA
 ******************************************************************************/
/** Se inicializa el bufer de salida de php **/
ob_start();

include_once("conex.php");
include_once("root/comun.php");
include_once("funcionesIngresos.php");

$conex = obtenerConexionBD("matrix");
$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wemp_pmla = $_REQUEST['wemp_pmla'];
 // Obtener las historias que le hicieron egreso automático
 $historias = HC_e_ingresos_de_Egresos_automaticos();
 //$historias = array(array(825487, 1));
 $cantidad_historias = count($historias);
 /** Limpiamos el buffer de salida de php para no retornar los datos de los "echos" que se hacen en los include **/
 ob_end_clean();
 if ($_POST['mostrar_conteo']) { 
 	print_r(json_encode($cantidad_historias));
 } 
 else{
 	/* 
 		Con base a las historias obtenidas, se obtienen los datos de las tablas que se afectan
 		al realizar un ingreso.
 	*/ 
 	$fallidos = array();
 	$finalizados = array();
 	foreach ($historias as $historiaIngreso) {
 		// rollbackIngreso($historiaIngreso);
		$datos = obtener_datos($historiaIngreso, $wmovhos, '_000016', 'Inghis', 'Inging');
 		if (count($datos) > 0) {
			$res = autoIncremento_movhos_000016($datos, $historiaIngreso);
 			if(empty($res['finalizados'])){
				$ingreso = $res['fallidos']['ingreso'];
				$res['fallidos']['url'] = "../../admisiones/procesos/admision_erp.php?wemp_pmla=".$wemp_pmla."&search_historia=".$historiaIngreso[0]."&search_ingreso=".$ingreso;
				array_push($fallidos, $res['fallidos']);
 			}else{
				$ingreso = $res['finalizados']['ingreso'];
				$res['finalizados']['url'] = "../../admisiones/procesos/admision_erp.php?wemp_pmla=".$wemp_pmla."&search_historia=".$historiaIngreso[0]."&search_ingreso=".$ingreso;
				array_push($finalizados, $res['finalizados']);

 			}
 		}else{
			$datosPaciente = obtenerDatosPaciente($historiaIngreso[0], $historiaIngreso[1]);
			$fallidos['historia'] = $historiaIngreso[0];
			$fallidos['ingreso'] = $historiaIngreso[1];
			$fallidos['tipo_documento'] = $datosPaciente['tipo_documento'];
			$fallidos['documento'] = $datosPaciente['documento_paciente'];
			$fallidos['paciente'] = $datosPaciente['paciente'];
			$fallidos['descripcion'] = 'No se encontró datos del paciente en la tabla root.';
		}
 	}

 	$result = array();
 	$result['finalizados'] = $finalizados;
 	$result['fallidos'] = $fallidos;
 	ob_end_clean();
 	print_r(json_encode($result, JSON_UNESCAPED_SLASHES));
 }


?>