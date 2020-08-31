<!doctype html>
<html>
<head>
    <title>Consolidados Encuesta de Satisfaccion Central de Esterilizacion</title>
    <link href="Estilos.css" rel="stylesheet" type="text/css">
    <!--
    <link href="EstilosCenest.css" rel="stylesheet" type="text/css">
    -->
    <style type="text/css"></style>
    <script src="js3.js"></script>
    <script src="jsCenest.js"></script>
    <link href="jqueryCenest.ui.core.min.css" rel="stylesheet" type="text/css">
    <link href="jqueryCenest.ui.theme.min.css" rel="stylesheet" type="text/css">
    <link href="jqueryCenest.ui.tabs.min.css" rel="stylesheet" type="text/css">
    <script src="jqueryCenest-1.8.3.min.js" type="text/javascript"></script>
    <script src="jqueryCenest-ui-1.9.2.tabs.custom.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function() {
            $( "#Tabs1" ).tabs();
        });
    </script>
    <style>
        .alternar:hover{ background-color:#e1edf7;}
    </style>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
    <?php
include_once("conex.php");
    include_once("cenest/Library2.php");
    if(!isset($_SESSION['user']))
    {
        ?>
        <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix; Inicie sesion nuevamente.</label>
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
    ?>
</head>

<body>
<div id="loginbox" style="margin-top: 5px">
    <div class="panel panel-info" >
        <div class="panel-heading">
            <div class="panel-title">Consolidado Encuesta de Satisfaccion Central de Esterilizacion</div>
        </div>

        <div style="padding-top:30px" class="panel-body">
            <form id="fservicio" method="post">
                <table border="1" class="table">
                    <thead style="background-color: #afd9ee">
                        <tr>
                            <th style="width: 350px">CENTROS DE COSTOS</th>
                            <th style="width: 225px">TOTAL ENCUESTAS POR AREA</th>
                            <th style="width: 225px">PORCENTAJE DILIGENCIADAS POR AREA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mostrarArea() ?>

                        <tr align="center" style="background-color: #afd9ee">
                        <td>TOTAL</td>
                        <td><input type="text" style="background-color: #afd9ee" class="input2" readonly id="p1" value="<?php totalEncuestas() ?>"></td>
                        <td><input type="text" style="background-color: #afd9ee" class="input2" readonly id="tp" value="100%"></td>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>


<div id="Tabs1">
    <ul>
        <li><a href="#tabs-1">Detalle</a></li>
        <li><a href="#tabs-2">Consolidado por Respuesta</a></li>
    </ul>
    <div id="tabs-1">
        <div id="detalleP1" class="panel-body">
            <div id="detalleCI" style="float: left; font-size: small">
                <table class="table">
                    <thead style="background-color: #CFDEFF;">
                    <tr align="center">
                        <td colspan="3">
                            <label id="campo" style="color: #00366A"><br>CUMPLIMIENTO DE SOLICITUDES EN CENTRAL DE ESTERILIZACION</label>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <label id="parametro">Bueno</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro1" value="f1_4">
                            <input type="hidden" id="parametro2" value="f1_5">
                            <input type="hidden" id="valor" value="Bueno">
                            <input type="hidden" id="pregunta" value="CUMPLIMIENTO DE SOLICITUDES EN CENTRAL DE ESTERILIZACION">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro1,parametro2,valor,pregunta)"><?php consultarP1b() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Regular</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro3" value="f1_3">
                            <input type="hidden" id="parametro4" value="f1_3">
                            <input type="hidden" id="valor2" value="Regular">
                            <input type="hidden" id="pregunta" value="CUMPLIMIENTO DE SOLICITUDES EN CENTRAL DE ESTERILIZACION">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro3,parametro4,valor2,pregunta)"><?php consultarP1r() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Malo</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro5" value="f1_1">
                            <input type="hidden" id="parametro6" value="f1_2">
                            <input type="hidden" id="valor3" value="Malo">
                            <input type="hidden" id="pregunta" value="CUMPLIMIENTO DE SOLICITUDES EN CENTRAL DE ESTERILIZACION">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro5,parametro6,valor3,pregunta)"><?php consultarP1m() ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="detallePASPE" style="float: left; margin-left: 30px; font-size: small">
                <table class="table">
                    <thead style="background-color: #CFDEFF">
                    <tr align="center">
                        <td colspan="3">
                            <label style="color: #00366A">CALIFIQUE LA CALIDEZ EN LA ATENCION POR PARTE DEL AUXILIAR<br>DE LAVADO</label>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <label>Bueno</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro7" value="f2_4">
                            <input type="hidden" id="parametro8" value="f2_5">
                            <input type="hidden" id="pregunta2" value="CALIFIQUE LA CALIDEZ EN LA ATENCION POR PARTE DEL AUXILIAR DE LAVADO">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro7,parametro8,valor,pregunta2)"><?php consultarP2b() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Regular</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro9" value="f2_3">
                            <input type="hidden" id="parametro10" value="f2_3">
                            <input type="hidden" id="pregunta2" value="CALIFIQUE LA CALIDEZ EN LA ATENCION POR PARTE DEL AUXILIAR DE LAVADO">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro9,parametro10,valor2,pregunta2)"><?php consultarP2r() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Malo</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro11" value="f2_1">
                            <input type="hidden" id="parametro12" value="f2_2">
                            <input type="hidden" id="pregunta2" value="CALIFIQUE LA CALIDEZ EN LA ATENCION POR PARTE DEL AUXILIAR DE LAVADO">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro11,parametro12,valor3,pregunta2)"><?php consultarP2m() ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="detalleSIS412" style="float: left; font-size: small">
                <table class="table">
                    <thead style="background-color: #CFDEFF">
                    <tr align="center">
                        <td colspan="3">
                            <label style="color: #00366A"><br>CALIFIQUE LA ATENCION POR PARTE DEL AUXILIAR EN EL AREA DE<br>DESPACHO</label>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <label>Bueno</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro13" value="f3_4">
                            <input type="hidden" id="parametro14" value="f3_5">
                            <input type="hidden" id="pregunta3" value="CALIFIQUE LA ATENCION POR PARTE DEL AUXILIAR EN EL AREA DE DESPACHO">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro13,parametro14,valor,pregunta3)"><?php consultarP3b() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Regular</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro15" value="f3_3">
                            <input type="hidden" id="parametro16" value="f3_3">
                            <input type="hidden" id="pregunta3" value="CALIFIQUE LA ATENCION POR PARTE DEL AUXILIAR EN EL AREA DE DESPACHO">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro15,parametro16,valor2,pregunta3)"><?php consultarP3r() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Malo</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro17" value="f3_1">
                            <input type="hidden" id="parametro18" value="f3_2">
                            <input type="hidden" id="pregunta3" value="CALIFIQUE LA ATENCION POR PARTE DEL AUXILIAR EN EL AREA DE DESPACHO">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro17,parametro18,valor3,pregunta3)"><?php consultarP3m() ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="detalleAYUDASDX" style="float: left; margin-left: 30px; font-size: small">
                <table class="table">
                    <thead style="background-color: #CFDEFF">
                    <tr align="center">
                        <td colspan="3">
                            <label style="color: #00366A"><br>CALIFIQUE EL TIEMPO DE ATENCION Y/O RESPUESTA A SU SOLICITUD<br>&nbsp;</label>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <label>Bueno</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro19" value="f4_4">
                            <input type="hidden" id="parametro20" value="f4_5">
                            <input type="hidden" id="pregunta4" value="CALIFIQUE EL TIEMPO DE ATENCION Y/O RESPUESTA A SU SOLICITUD">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro19,parametro20,valor,pregunta4)"><?php consultarP4b() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Regular</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro21" value="f4_3">
                            <input type="hidden" id="parametro22" value="f4_3">
                            <input type="hidden" id="pregunta4" value="CALIFIQUE EL TIEMPO DE ATENCION Y/O RESPUESTA A SU SOLICITUD">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro21,parametro22,valor2,pregunta4)"><?php consultarP4r() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Malo</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro23" value="f4_1">
                            <input type="hidden" id="parametro24" value="f4_2">
                            <input type="hidden" id="pregunta4" value="CALIFIQUE EL TIEMPO DE ATENCION Y/O RESPUESTA A SU SOLICITUD">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro23,parametro24,valor3,pregunta4)"><?php consultarP4m() ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="detalleSIS412" style="float: left; font-size: small">
                <table class="table">
                    <thead style="background-color: #CFDEFF">
                    <tr align="center">
                        <td colspan="3">
                            <label style="color: #00366A"><br>LOS PRODUCTOS ESTERILES SON ACORDE A LAS NECESIDADES<br>REALIZADAS EN SU SOLICITUD</label>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <label>Bueno</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro25" value="f5_4">
                            <input type="hidden" id="parametro26" value="f5_5">
                            <input type="hidden" id="pregunta5" value="LOS PRODUCTOS ESTERILES SON ACORDE A LAS NECESIDADES REALIZADAS<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EN SU SOLICITUD">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro25,parametro26,valor,pregunta5)"><?php consultarP5b() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Regular</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro27" value="f5_3">
                            <input type="hidden" id="parametro28" value="f5_3">
                            <input type="hidden" id="pregunta5" value="LOS PRODUCTOS ESTERILES SON ACORDE A LAS NECESIDADES REALIZADAS<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EN SU SOLICITUD">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro27,parametro28,valor2,pregunta5)"><?php consultarP5r() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Malo</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro29" value="f5_1">
                            <input type="hidden" id="parametro30" value="f5_2">
                            <input type="hidden" id="pregunta5" value="LOS PRODUCTOS ESTERILES SON ACORDE A LAS NECESIDADES REALIZADAS<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;EN SU SOLICITUD">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro29,parametro30,valor3,pregunta5)"><?php consultarP5m() ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="detalleAYUDASDX" style="float: left; margin-left: 60px; font-size: small">
                <table class="table">
                    <thead style="background-color: #CFDEFF">
                    <tr align="center">
                        <td colspan="3">
                            <label style="color: #00366A"><br>CALIFIQUE EN GENERAL EL SERVICIO PRESTADO EN CENTRAL DE<br>ESTERILIZACION</label>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <label>Bueno</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro31" value="f6_4">
                            <input type="hidden" id="parametro32" value="f6_5">
                            <input type="hidden" id="pregunta6" value="CALIFIQUE EN GENERAL EL SERVICIO PRESTADO EN CENTRAL DE ESTERILIZACION">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro31,parametro32,valor,pregunta6)"><?php consultarP6b() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Regular</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro33" value="f6_3">
                            <input type="hidden" id="parametro34" value="f6_3">
                            <input type="hidden" id="pregunta6" value="CALIFIQUE EN GENERAL EL SERVICIO PRESTADO EN CENTRAL DE ESTERILIZACION">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro33,parametro34,valor2,pregunta6)"><?php consultarP6r() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Malo</label>
                        </td>
                        <td>
                            <label>=</label>
                        </td>
                        <td>
                            <input type="hidden" id="parametro35" value="f6_1">
                            <input type="hidden" id="parametro36" value="f6_2">
                            <input type="hidden" id="pregunta6" value="CALIFIQUE EN GENERAL EL SERVICIO PRESTADO EN CENTRAL DE ESTERILIZACION">
                            <a href="" style="color:navy; font-weight: bold; font-size: medium" onclick="verInformeB(parametro35,parametro36,valor3,pregunta6)"><?php consultarP6m() ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="tabs-2">
        <div class="panel-body" style="padding-top: 1px; margin-top: -5px">
            <div id="detalleCI" style="float: left; font-size: small">
                <table class="table">
                    <thead>
                    <tr align="center">
                        <td>&nbsp;</td>
                        <td>Cantidad</td>
                        <td>Porcentaje</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="background-color: #CFDEFF">
                            <label id="campo" style="color: #00366A"><br>TOTAL RESPUESTAS CON CALIFICACION: BUENO</label>
                        </td>
                        <td>
                            <br>
                            <a href="" style="color:navy; font-weight: bold; font-size: medium"><?php consultarPb() ?></a>
                        </td>
                        <td>
                            <br>
                            <a href="" style="color:navy; font-weight: bold; font-size: medium"><?php porcentajeTotalb() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="background-color: #CFDEFF">
                            <label id="campo" style="color: #00366A"><br>TOTAL RESPUESTAS CON CALIFICACION: REGULAR</label>
                        </td>
                        <td>
                            <br>
                            <a href="" style="color:navy; font-weight: bold; font-size: medium"><?php consultarPr() ?></a>
                        </td>
                        <td>
                            <br>
                            <a href="" style="color:navy; font-weight: bold; font-size: medium"><?php porcentajeTotalr() ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="background-color: #CFDEFF">
                            <label id="campo" style="color: #00366A"><br>TOTAL RESPUESTAS CON CALIFICACION: MALA</label>
                        </td>
                        <td>
                            <br>
                            <a href="" style="color:navy; font-weight: bold; font-size: medium"><?php consultarPm() ?></a>
                        </td>
                        <td>
                            <br>
                            <a href="" style="color:navy; font-weight: bold; font-size: medium"><?php porcentajeTotalm() ?></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
