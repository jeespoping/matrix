<?php
include_once("conex.php");
include_once("root/comun.php");

include '../presap/models/Admisiones.php';
include '../presap/service/ServicioAdmisiones.php';
include '../presap/controllers/Reporte1995Controller.php';
include '../presap/service/GeneradorCSV.php';
include '../presap/service/Permisos.php';

use matrix\admisiones\presap\controllers\Reporte1995Controller;
use matrix\admisiones\presap\models\Admisiones;
use matrix\admisiones\presap\service\ServicioAdmisiones;
use matrix\admisiones\presap\service\Permisos;


$wactualiz = date('Y-m-d');
const MSJ_ERROR_SIN_PERMISOS = "El usuario no tiene permisos para ver esta aplicacion";

if (!isset($user)) {
	verificarSesion();
}
if (!isset($_SESSION['user'])) {
	terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
} else {
	$conex = obtenerConexionBD("matrix");
	verificarWemppmla($wemp_pmla, $MSJ_ERROR_FALTA_PARAMETRO);
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "reporte_1995");
	if (strpos($user, "-") > 0)
		$wusuario = substr($user, (strpos($user, "-") + 1), strlen($user));

	$codigoGrupo = $_GET['grupo'];
	$codigoOpcion = $_GET['opcion'];

	validarParametro($codigoGrupo, 'grupo', $MSJ_ERROR_FALTA_PARAMETRO);
	validarParametro($codigoOpcion, 'opcion', $MSJ_ERROR_FALTA_PARAMETRO);
	$permisos = new Permisos($wusuario, $conex);
	try {
		$tieneGrupo = $permisos->validarGrupo($codigoGrupo);
		$tieneOpcion = $permisos->validarOpcion($codigoGrupo, $codigoOpcion);
		if (!$tieneGrupo or !$tieneOpcion) {
			terminarEjecucion(MSJ_ERROR_SIN_PERMISOS);
		}
	} catch (Exception $e) {
		echo "Error, contacte a soporte";
	}
	if (!($_SERVER["REQUEST_METHOD"] == "POST")) {
?>
		<html>

		<head>
			<script src="reporte-res1995/vue.js"></script>
			<script src="../presap/sweeralert2/dist/sweetalert2.min.js"></script>
			<link rel="stylesheet" href="../presap/sweeralert2/dist/sweetalert2.min.css">
			<title>MATRIX - [REPORTE ADMISIONES RES 1995]</title>
		</head>

		<body>
			<?php
			/*PARTE DEL REGISTRO Y LA APLICACIÓN*/
			encabezado("Reporte admisiones", $wactualiz, "Logo_matrix");
			?>
			<div id="app">
			</div>
			<script type='module' src='reporte-res1995/main.js'>
			</script>
		</body>

		</html>
<?php
	} else {
		//===============================================================================================================================================
		//Rutas del programa en caso de que sea una petición tipo POST
		//===============================================================================================================================================
		$JSONEntrada = file_get_contents('php://input');
		$peticion = json_decode($JSONEntrada, TRUE);
		$modeloAdmisiones = new Admisiones($conex, $wemp_pmla, $wtabcco);
		$servicioAdmisiones = new ServicioAdmisiones($modeloAdmisiones);
		$controladorReporte = new Reporte1995Controller($servicioAdmisiones, $peticion);
		if ($peticion['accion'] == 'DESCARGAR_REPORTE') {
			$controladorReporte->descargarReporte();
		} else {
			$response = ['error' => true, 'mensaje' => 'Debe especificar una acción'];
			echo json_encode($response);
		}
	}
	liberarConexionBD($conex);
}

function verificarWemppmla($wemp_pmla, $MSJ_ERROR_FALTA_PARAMETRO)
{
	if (!isset($wemp_pmla)) {
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO . "wemp_pmla");
	}
}

function verificarSesion()
{
	if (!isset($_SESSION['user'])) {
		session_register("user");
	}
}

function validarParametro($parametro, $nombreParametro, $MSJ_ERROR_FALTA_PARAMETRO)
{
	if (!isset($parametro)) {
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO . $nombreParametro);
	}
}
?>