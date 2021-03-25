<?php
include_once("conex.php");
include_once("root/comun.php");
header("Content-Type: text/html;charset=ISO-8859-1");
include '../presap/models/Admisiones.php';
include '../presap/service/ServicioAdmisiones.php';
include '../presap/service/GeneradorCSV.php';

use matrix\admisiones\presap\models\Admisiones;
use matrix\admisiones\presap\service\ServicioAdmisiones;
use matrix\admisiones\presap\service\GeneradorCSV;


$wactualiz = date('Y-m-d');
if (!isset($user))
	if (!isset($_SESSION['user']))
		session_register("user");
if (!isset($_SESSION['user']))
	terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
else {
	$conex = obtenerConexionBD("matrix");
	if (!isset($wemp_pmla)) {
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO . "wemp_pmla");
	}
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	if (strpos($user, "-") > 0)
		$wusuario = substr($user, (strpos($user, "-") + 1), strlen($user));
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
			encabezado("Reporte admisiones", $wactualiz, "cliame");
			/*$modeloAdmisiones = new Admisiones($conex,$wemp_pmla,$wtabcco);
			$reporte = $modeloAdmisiones->todasPorFechaIngreso('"2021-03-01"','"2021-03-25"');
			echo json_encode($reporte);*/
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
		//Rutas del programa.
		//===============================================================================================================================================
		$modeloAdmisiones = new Admisiones($conex, $wemp_pmla, $wtabcco);
		$servicioAdmisiones = new ServicioAdmisiones($modeloAdmisiones);

		$JSONEntrada = file_get_contents('php://input');
		$peticion = json_decode($JSONEntrada, TRUE);
		if ($peticion['accion'] == 'DESCARGAR_REPORTE') {
			$reporteCSV = new GeneradorCSV('test.csv', ',');
			try {
				$reporte = $modeloAdmisiones->todasPorFechaIngreso('"2021-02-23"', '"2021-03-25"');
				echo json_encode($reporte);
			} catch (Exception $err) {
				$response = ['error' => true, 'mensaje' => 'Debe especificar una acción'];
				echo json_encode($response);
			}
			/*if(sizeof($reporte)>0){
				$reporteCSV->crearArchivo($reporte);
			}*/
		} else {
			$response = ['error' => true, 'mensaje' => 'Debe especificar una acción'];
			echo json_encode($response);
		}
	}
	liberarConexionBD($conex);
}
?>