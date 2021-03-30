<?php
    include_once("conex.php");
    include_once("root/comun.php");
    header("Content-Type: text/html;charset=ISO-8859-1");
    include dirname(__FILE__) . '/../presap/controllers/DatosSocioEconomicosController.php';
    include dirname(__FILE__) . '/../presap/controllers/StickerPDFController.php';

    use Admisiones\Controller\DatosSocioEconomicosController as DatosPaciente;
    use Admisiones\Controller\StickerPDFController; ?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>MATRIX - [Generaci&oacute;n Sticker Admisi&oacute;n]</title>
    <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
    <script src="../../assets/vendor/vuejs/vue.js"></script>

</head>
<body class="container">
<?php

    // if (!isset($user)) {
    //     if (!isset($_SESSION['user'])) {
    //         session_register("user");
    //     }
    // }

    // if (!isset($_SESSION['user'])) {
    //     terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
    // }

    $wEmp = empty($_GET['wemp_pmla']) ? 0 : $_GET['wemp_pmla'];
    $nHis = empty($_GET['nHis']) ? 0 : $_GET['nHis'];
    $nIng = empty($_GET['nIng']) ? 0 : $_GET['nIng'];
    $pdf = empty($_GET['pdf']) ? false : $_GET['pdf'];

    $conex = obtenerConexionBD("matrix");

    if (empty($wemp_pmla)) {
        terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO . "wemp_pmla");
    }

    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wInstitucion = $institucion->nombre;
    $empresa = $institucion->baseDeDatos;

    $datosPaciente = new DatosPaciente($conex, $empresa, $nHis, $nIng);
    //    echo json_encode($datosPaciente->jsonSerialize());
    $wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, $institucion->baseDeDatos);
    if (strpos($user, "-") > 0) {
        $wusuario = substr($user, (strpos($user, "-") + 1), strlen($user));
    }

    if (!$pdf || !$_SERVER["REQUEST_METHOD"] == "POST") : ?>

        <?php encabezado("Sticker Admisi&oacuten", date('Y-m-d'), "cliame"); ?>
        <div id="app" empresa="<?php echo $empresa; ?>"></div>

    <?php else :
        //===============================================================================================================================================
        //Rutas del programa.
        //===============================================================================================================================================
        $modeloAdmisiones = new Admisiones($conex, $wemp_pmla, $wtabcco);
        $servicioAdmisiones = new ServicioAdmisiones($modeloAdmisiones);

        $JSONEntrada = file_get_contents('php://input');
        $peticion = json_decode($JSONEntrada, TRUE);
        if ($peticion['accion'] == 'DESCARGAR_REPORTE') {
            $prueba = new GeneradorCSV('prueba.csv', ',');
            $prueba->crearArchivo();
        } else {
            $response = ['error' => true, 'mensaje' => 'Debe especificar una acción'];
            echo json_encode($response);
        }
    endif;
    liberarConexionBD($conex);

?>
<script src="../../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
<script type='module' src='stickers/main.js'></script>
</body>

</html>