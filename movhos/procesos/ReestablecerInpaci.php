<?php
include_once("conex.php");
include_once("root/comun.php");


$wactualiz = "2021-07-05";
$titulo = "Reestablecer datos procesos domiciliario autom&aacute;tico";
$wemp_pmla = $_GET['wemp_pmla'];

if(!isset($_SESSION['user']))
{
    ?>
    <div align="center">
        <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
    </div>
    <?php
    return;
}

?>
<html lang="es">

<head>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css"/>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
          integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js"
            integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG"
            crossorigin="anonymous"></script>

    <!-- Multiple select -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>

</head>

<body BGCOLOR="" TEXT="#000000">
<input type="hidden" id="wemp_pmla" value="<?= $wemp_pmla ?>">

<div class="container-fluid">
    <div class="row">
        <?= encabezado($titulo, $wactualiz, "clinica"); ?>
    </div>
    <div class="row border border-primary shadow p-3 mb-5 bg-body rounded form justify-content-center">
        <div class="col-sm-3">
            <form class="form-control">
                <hr>
                <div class="row form-group">
                    <input type="button" class="btn btn-primary" value="Ejecutar procesos" id="btn_ejecutar">
                </div>
            </form>
        </div>
    </div>

</div>
<div id="cargarDatos"></div>
<script src="reestablecer.js"></script>
</body>

</html>