<?php
include_once("conex.php");
include_once("root/comun.php");

//header("Content-Type: text/html;charset=ISO-8859-1");

include dirname(__FILE__) . '/../presap/controllers/PacienteController.php';

use Admisiones\controllers\PacienteController;

const MSJ_ERROR_SIN_PERMISOS = "El usuario no tiene permisos para ver esta aplicacion";

//if (!isset($user)) {
//    verificarSesion();
//}
//
//if (!isset($_SESSION['user'])) {
//    terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
//}

$wemp_pmla = empty($_GET['wemp_pmla']) ? $_POST['wemp_pmla'] : $_GET['wemp_pmla'];
$nHis = empty($_GET['nHis']) ? 0 : $_GET['nHis'];
$nIng = empty($_GET['nIng']) ? 0 : $_GET['nIng'];
$pdf = empty($_GET['pdf']) ? false : $_GET['pdf'];

$conex = obtenerConexionBD("matrix");
verificarWemppmla($wemp_pmla, $MSJ_ERROR_FALTA_PARAMETRO);

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wInstitucion = $institucion->nombre;
$empresa = $institucion->baseDeDatos;
$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, $institucion->baseDeDatos);

try {
    $paciente = new PacienteController($conex, $empresa, $nHis, $nIng);
    $jsonPaciente = $paciente->jsonSerialize();
} catch (Exception $e) {
    $paciente = new \Admisiones\models\Paciente();
//    echo $e;
//    terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO . $e);
}

//validarPermisos();
if ($_SERVER["REQUEST_METHOD"] != "POST") : ?>

    <!doctype html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title>MATRIX - [Generaci&oacute;n Sticker Admisi&oacute;n]</title>
        <link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css">
    </head>

    <body class="container">
    <?php encabezado("Sticker Admisi&oacuten", date('Y-m-d'), "cliame"); ?>
    <form method="post" class="row g-1" id="stickerForm"
          action="stickers/stickers.php?consultaAjax=&wemp_pmla=<?php echo $wemp_pmla; ?>&nHis=<?php echo $paciente->getNumeroHistoria() ?>&nIng=<?php echo $paciente->getNumeroIngreso() ?>&empresa=<?php echo $empresa; ?>">
        <div class="form-floating col-sm-6">
            <input type="number" class="form-control" id="nHis" placeholder="999999" on
                   value="<?php echo $paciente->getNumeroHistoria() ?>">
            <label for="nHistoria">N&uacute;mero Historia</label>
        </div>
        <div class="form-floating col-sm-6">
            <input type="number" class="form-control" id="nIng" placeholder="0"
                   value="<?php echo $paciente->getNumeroIngreso() ?>">
            <label for="nIngreso">N&uacute;mero Ingreso</label>
        </div>
        <div class="form-floating col-12">
            <input type="text" class="form-control" id="nombres" placeholder="Pedro Perez"
                   value="<?php echo $paciente->getNombreCompleto() ?>" readonly>
            <label for="nombres">Nombres Completos</label>
        </div>
        <div class="form-floating col-12">
            <input type="text" class="form-control" id="documento" placeholder="CC 99999999"
                   value="<?php echo $paciente->getDocumento() ?>"
                   readonly>
            <label for="documento">Tipo y Documento</label>
        </div>
        <div class="form-floating col-sm-4">
            <input type="text" class="form-control" id="genero" placeholder="F=Femenino; M=Masculino"
                   value="<?php echo $paciente->getGenero() ?>"
                   readonly>
            <label for="genero">Genero</label>
        </div>
        <div class="form-floating col-sm-4">
            <input type="text" class="form-control" id="edad" placeholder="En Meses o años"
                   value="<?php echo $paciente->getEdad() ?>" readonly>
            <label for="edad">Edad</label>
        </div>
        <div class="form-floating col-sm-4">
            <input type="text" class="form-control" id="dx" placeholder="Diagnostico"
                   value="<?php echo $paciente->getDiagnostico() ?>" readonly>
            <label for="dx">Diagnostico</label>
        </div>
        <div class="form-floating col-sm-12">
            <input type="text" class="form-control" id="eps" placeholder="Aseguradora"
                   value="<?php echo $paciente->getAseguradora() ?>" readonly>
            <label for="eps">Aseguradora</label>
        </div>
        <div class="col-12 text-center">
            <input type="submit" class="btn btn-primary" value="Generar Sticker" />
            <button type=button class="btn btn-secondary" onclick="return closeWindow();">Cerrar</button>
        </div>
        <input type="hidden" id="consultaAjax" value="false">
    </form>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script>
        const closeWindow = () => { open(location, '_self').close(); };
    </script>
    <!--    <script type='module' src='stickers/main.js'></script>-->
    </body>

    </html>
<?php
endif;
liberarConexionBD($conex);

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

function validarPermisos()
{
    $codigoGrupo = $_GET['grupo'];
    $codigoOpcion = $_GET['opcion'];

    if (strpos($user, "-") > 0) {
        $wusuario = substr($user, (strpos($user, "-") + 1), strlen($user));
    }

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
}
