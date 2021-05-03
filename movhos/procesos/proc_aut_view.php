<?php
include_once("conex.php");
include_once("root/comun.php");
/***************************************************
 * PROGRAMA                   : index.php
 * AUTOR                      : Juan David Rodriguez y Johan Córdoba
 * FECHA CREACION             : 19/02/2021
 *
 * DESCRIPCION:
 * Muestra los pacientes que ingresan o egresan para un servicio seleccionado o todos.
 */
$wactualiz = "2017-05-10";
$titulo = "Procesos servicio domiciliario autom&aacute;tico";
$wemp_pmla = $_GET['wemp_pmla'];

include_once('egrFunctions.php');
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
    <div class="row border border-primary shadow p-3 mb-5 bg-body rounded form">
        <div class="col-sm-3">
            <form class="form-control">
                <div class="row form-group">
                    <label for="wcco0" class="form-label fw-bold Fila1">Entidad responsable: </label>
                    <select name="cemp" id="cemp" class="form-control selectpicker" multiple data-live-search="true">
                        <?php generarSelectEPS() ?>
                    </select>
                </div>

                <div class="row form-group">
                    <label for="wcco0" class="form-label fw-bold Fila1">Servicio: </label>
                    <select name='wcco0' id='wcco0' class="form-control selectpicker" multiple data-live-search="true">
                        <?php generarSelectCentroCostos() ?>
                    </select>
                </div>
                <hr>
                <div class="row form-group">
                    <label for="whisconsultada" class="form-label fw-bold Fila1">Historia: </label>
                    <input type='text' name='whisconsultada' id='whisconsultada' value="" placeholder="Historia..."
                           class="form-control">
                </div>
                <hr>
                <div class="row form-group">
                    <input type="button" class="btn btn-primary" value="Ejecutar procesos" id="btn_ejecutar">
                </div>
            </form>
        </div>
        <div class="col-sm">
            <table class="table table-bordered">
                <thead class="encabezadoTabla">
                <th class="text-center">Egresos</th>
                <th class="text-center">Ingresos</th>
                <th class="text-center">Ordenes</th>
                </thead>
                <tbody>
                <tr class="h-100 Fila2">
                    <td class="text-center">
                        <div class="fa-3x">
                            <i class="fas fa-sync" id="egreso_icon"></i>
                        </div>
                        <br>
                        <div class="execute-time-content">
                            Tiempo transcurrido: <p id="time_egreso">0 segundos</p>
                        </div>
                        <br>
                        <div id="egreso_mensajes"></div>

                    </td>
                    <td class="text-center">
                        <div class="fa-3x">
                            <i class="fas fa-sync" id="ingreso_icon"></i>
                        </div>
                        <br>
                        <div class="execute-time-content">
                            Tiempo transcurrido: <p id="time_ingreso">0 segundos</p>
                        </div>
                        <br>
                        <div id="ingreso_mensajes"></div>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-link mostrar_modal invisible" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop" id="INGRESO_BTN_ID">
                            Ver detalles
                        </button>
                    </td>
                    <td class="text-center">
                        <div class="fa-3x">
                            <i class="fas fa-sync" id="replicar_icon"></i>
                        </div>
                        <br>
                        <div class="execute-time-content">
                            Tiempo transcurrido: <p id="time_replicar">0 segundos</p>
                        </div>
                        <br>
                        <div id="replicar_mensajes"></div>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-link mostrar_modal invisible" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop" id="REPLICAR_BTN_ID">
                            Ver detalles
                        </button>
                    </td>
                </tr>
                <tr class="Fila1">
                    <td>
                        <ul id="egreso_mensajes_historias"></ul>
                    </td>
                    <td>
                        <ul id="ingreso_mensajes_historias"></ul>
                    </td>
                    <td>
                        <ul id="replicar_mensajes_historias"></ul>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Detalle del proceso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal_content_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>
<div id="cargarDatos"></div>
<script src="proc_aut.js"></script>
</body>

</html>