<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Consolidados - PAF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="botonespaf.css" rel="stylesheet" type="text/css">
    <link href="estilospaf.css" rel="stylesheet" type="text/css">
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="calendariopaf.js" type="text/javascript"></script>
    <script src="JsProcesospaf.js" type="text/javascript"></script>
    <?php
include_once("conex.php");
    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
        </div>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        

        include_once("root/comun.php");
        


        $conex = obtenerConexionBD("matrix");
    }
    include_once("paf/librarypaf.php");

    $observacion = utf8_decode($_GET['observacion']);
    $nota = utf8_decode($_GET['nota']);
    $fecha_Ronda = $_GET['fecha_Ronda'];
    ?>
</head>

<body>
    <form role="form" style="margin-top: 10px; width: 50%; margin-left: 20px">
        <div class="form-group">
            <label for="ejemplo_email_1">Observaciones</label>
            <textarea class="form-control" rows="3" id="observacion" readonly><?php echo $observacion ?></textarea>
        </div>
        <div class="form-group">
            <label for="ejemplo_email_1">Notas del Auditor</label>
            <textarea class="form-control" rows="3" id="nota" readonly><?php echo $nota ?></textarea>
        </div>
        <label for="ejemplo_email_1">Fecha Ronda Auditoria: </label><?php echo $fecha_Ronda ?>
    </form>
</body>
</html>