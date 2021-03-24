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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$JSONEntrada = file_get_contents('php://input');
	$request = json_decode($JSONEntrada, TRUE);
	$prueba = new GeneradorCSV('prueba.csv', ',');
	$prueba->crearArchivo();
	//echo json_encode($request);
} else {
?>
	<html>
	<head>
		<script src="reporte-res1995/vue.js"></script>
		<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
		<title>MATRIX - [REPORTE ADMISIONES RES 1995]</title>
	</head>

	<body>
		<?php
		$wactualiz = date('Y-m-d');
		if (!isset($user))
			if (!isset($_SESSION['user']))
				session_register("user");

		if (!isset($_SESSION['user']))
			terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
		else {
			/*PARTE DEL REGISTRO Y LA APLICACIÓN*/
			$conex = obtenerConexionBD("matrix");
			if (!isset($wemp_pmla)) {
				terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO . "wemp_pmla");
			}
			$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
			$winstitucion = $institucion->nombre;
			$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
			$modeloAdmisiones = new Admisiones($conex, $wemp_pmla, $wtabcco);
			//$servicioAdmisiones = new ServicioAdmisiones($modeloAdmisiones);
			encabezado("Reporte admisiones", $wactualiz, "cliame");
		?>
			<div id="app">
			</div>
			<script type='module' src='reporte-res1995/main.js'>
			</script>
	<?php
			//FORMA ================================================================
			if (strpos($user, "-") > 0)
				$wusuario = substr($user, (strpos($user, "-") + 1), strlen($user));

			liberarConexionBD($conex);

			//===============================================================================================================================================
			//Rutas del programa.
			//===============================================================================================================================================
		}
		echo '</body>';
		echo '</html>';
	}
	?>
