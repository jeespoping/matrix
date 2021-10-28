<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ESTADO DE CUENTA - MATRIX</title>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="styleEstadoCuenta.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script>
        function openCity(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    </script>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
        });
    </script>
    <style>
        .alternar:hover
        {
            background-color: #D9EDF7;
        }
        .datosIndicadores
        {
            border: none;
            margin-top: 0px;
            margin-bottom: 20px;
            width: 140px;
            height: 290px;
        }
        .listRow:hover
        {
            background-color: #EEEEEE;
            color: snow;
            /*border-bottom: groove;
            border-bottom-color: #001629;*/
        }
        .listRow td label
        {
            font-weight: bold;
            font-size: 15px;
        }
        .listRow td label a
        {
            font-family: fantasy;
        }
        .divxUsuario
        {
            border: none;
            width: 535px;
            height: 310px;
            float: right;
            margin-top: -310px;
            margin-bottom: 20px;
            background-color: #FFFFFF;
        }
        .divxSemMes
        {
            border: none;
            width: 535px;
            height: 310px;
            float: right;
            margin-top: -310px;
            margin-bottom: 20px;
        }
        .divxPorCausas
        {
            border: none;
            width: 535px;
            height: 310px;
            float: right;
            margin-top: -310px;
            margin-bottom: 20px;
        }
        .divxProDurLlama
        {
            border: none;
            width: 535px;
            height: 310px;
            float: right;
            margin-top: -310px;
            margin-bottom: 20px;
        }
        .divxLlamaAreas
        {
            border: none;
            width: 535px;
            height: 310px;
            float: right;
            margin-top: -310px;
            margin-bottom: 20px;
        }
        .divContenido1
        {
            border: none;
            margin-top: 5px;
            text-align: left;
        }
    </style>
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
        //include_once("helpDeskProcesos.php");
        


        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }

    $fechaActual = date('Y-m-d');
    $horaActual = date('H:i:s');
    ?>
</head>

<body>
<div class="container general">
    <div class="panel panel-info contenido">
        <div class="panel-heading encabezado">
            <div class="panel-title"><label style="font-weight: bold">Matrix - Estado de Cuenta</label></div>
        </div>

        <!-- PANEL DE SELECCION DE INFORMES Y  FECHAS: -->
        <form method="post" action="estadoCuenta_01.php">
            <div class="row text-center" style="margin-top: -30px">
                <br><br>
                <!--<h4 style="margin-bottom: 5px">Informes a Generar:</h4>-->

                <label for="estadoCartera" class="btn btn-primary btn-sm" style="width: 310px">Estado de Cartera
                    <input type="checkbox" id="estadoCartera" name="estadoCartera" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
                <label for="venceCartera" class="btn btn-primary btn-sm" style="width: 310px">Vencimiento Cartera Radicada
                    <input type="checkbox" id="venceCartera" name="venceCartera" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
                <label for="cargosPendientes" class="btn btn-primary btn-sm" style="width: 310px">Cargos Pendientes por Facturar
                    <input type="checkbox" id="cargosPendientes" name="cargosPendientes" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
            </div>
            <div class="row text-center" style="margin-top: 5px">
                <label for="detalleAnticipos" class="btn btn-primary btn-sm" style="width: 310px">Detalle Anticipos sin Legalizar
                    <input type="checkbox" id="detalleAnticipos" name="detalleAnticipos" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
                <label for="facturaPromedio" class="btn btn-primary btn-sm" style="width: 310px">Facturacion Promedio Mensual
                    <input type="checkbox" id="facturaPromedio" name="facturaPromedio" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
                <label for="causaGlosas" class="btn btn-primary btn-sm" style="width: 310px">Causales Glosas por Gestionar
                    <input type="checkbox" id="causaGlosas" name="causaGlosas" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
            </div>
            <div class="row text-center" style="margin-top: 5px">
                <label for="causaDevolucion" class="btn btn-primary btn-sm" style="width: 310px">Causales Devoluciones por Gestionar
                    <input type="checkbox" id="causaDevolucion" name="causaDevolucion" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
                <label for="causaFactura" class="btn btn-primary btn-sm" style="width: 310px">Causales Facturacion sin Radicar
                    <input type="checkbox" id="causaFactura" name="causaFactura" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
                <label for="evalAuditoria" class="btn btn-primary btn-sm" style="width: 310px">Evaluacion Auditoria Externa
                    <input type="checkbox" id="evalAuditoria" name="evalAuditoria" class="badgebox" value="1"><span class="badge">&check;</span>
                </label>
            </div>

            <div id="rangoFechas" class="input-group rangoFechas" align="center">
                <div class="input-group" style="border: none; margin-left: 300px; width: 40%">
                    <!--<div class="input-group-addon input-sm" style="background-color: #ffffff; width: 200px; border: none"></div>-->

                    <span class="input-group-addon input-sm"><label for="datepicker1">Fecha Inicial</label></span>
                    <input type="text" id="datepicker1" name="fechaInicial" class="form-control form-sm" style="width: 150px">

                    <div class="input-group-addon input-sm" style="background-color: #ffffff; width: 10px; border: none"></div>

                    <span class="input-group-addon input-sm"><label for="datepicker2">Fecha Final</label></span>
                    <input type="text" id="datepicker2" name="fechaFinal" class="form-control form-sm" style="width: 150px">
                </div>

                <div class="input-group" style="margin-top: 10px; margin-left: 10px; border: none">
                    <span class="input-group-addon input-sm" style="width: 165px"><label for="empresa">EMPRESA:</label></span>
                    <select id="empresa" name="empresa" class="form-control form-sm" style="width: 380px" required>
                        <?php
                        $conempresa = "select empcod,empnom from inemp WHERE empact = 'S' ORDER BY empnom ASC";
                        $err_1 = odbc_do($conex_o, $conempresa);
                        while($datoempresa = odbc_fetch_row($err_1))
                        {
                            echo "<option value='".odbc_result($err_1, 1)."'>
                         ".odbc_result($err_1, 1).' - '.odbc_result($err_1, 2)."
                         </option>";
                            //$descripcionservicio=odbc_result($err_o, 2);
                        }
                        ?>
                        <option selected="selected" value="1">Todos</option>
                    </select>

                    <div class="input-group-addon input-sm" style="background-color: #ffffff; width: 10px; border: none"></div>

                    <span class="input-group-addon input-sm" style="width: 120px"><label for="serIngreso">SERVICIO DE INGRESO:</label></span>
                    <select id="serIngreso" name="serIngreso" class="form-control form-sm" style="width: 340px" required>
                        <?php
                        $conservicio = "select sercod,sernom from inser WHERE seract = 'S' ORDER BY sernom ASC";
                        $err_o = odbc_do($conex_o, $conservicio);
                        while($datoservicio = odbc_fetch_row($err_o))
                        {
                            echo "<option value='".odbc_result($err_o, 1)."'>
                         ".odbc_result($err_o, 1).' - '.odbc_result($err_o, 2)."
                         </option>";
                            //$descripcionservicio=odbc_result($err_o, 2);
                        }
                        ?>
                        <option selected="selected" value="1">Todos</option>
                    </select>
                </div>

                <div class="input-group" style="margin-top: 10px; margin-left: 10px; border: none">
                    <span class="input-group-addon input-sm" style="width: 120px"><label for="cCostos">CENTRO DE COSTOS:</label></span>
                    <select id="cCostos" name="cCostos" class="form-control form-sm" style="width: 230px" required>
                        <?php
                        $consespe2 = mysql_queryV("select Ccocod,Cconom from costosyp_000005 WHERE Ccoemp = '01' AND Ccocod NOT LIKE '5%' ORDER BY Cconom ASC");
                        while($datoespe2 = mysql_fetch_array($consespe2))
                        {
                            echo "<option value='".$datoespe2['Ccocod']."'>
                         ".$datoespe2['Ccocod'].' - '.$datoespe2['Cconom']."
                         </option>";
                            //$ccostosReq = $datoespe2['Ccocod'];
                        }
                        ?>
                        <option selected="selected" value="1">Todos</option>
                    </select>

                    <div class="input-group-addon input-sm" style="background-color: #ffffff; width: 10px; border: none"></div>

                    <div class="input-group-addon input-sm" style="background-color: #ffffff; border: none">
                        <label for="radioTipoEmpresa">Particular</label>
                        <input type="radio" name="radioTipoEmpresa" id="radioTipoEmpresa" value="0">
                        &ensp;
                        <label for="selEmpresa">Empresa</label>
                        <input type="radio" name="radioTipoEmpresa" id="radioTipoEmpresa" value="0">
                        &ensp;
                        <label for="selEmpresa">Todos</label>
                        <input type="radio" name="radioTipoEmpresa" id="radioTipoEmpresa" value="0">
                    </div>

                    <div class="input-group-addon input-sm" style="background-color: #ffffff; width: 10px; border: none"></div>

                    <div class="input-group-addon input-sm" style="background-color: #ffffff; border: none">
                        <label for="radioTipopac">Activos</label>
                        <input type="radio" name="radioTipopac" id="radioTipopac" value="0">
                        &ensp;
                        <label for="selTipopac">Inactivos</label>
                        <input type="radio" name="radioTipopac" id="radioTipopac" value="0">
                        &ensp;
                        <label for="selTipopac">Todos</label>
                        <input type="radio" name="radioTipopac" id="radioTipopac" value="0">
                    </div>

                    <div class="input-group-addon" style="background-color: #ffffff; border: none">
                        <input type="submit" class="btn btn-info btn-sm" style="margin-top: -5px; margin-right: 27px; width: 100px" id="bntBus" name="btnBus" value="> > >">
                    </div>
                </div>
            </div>
        </form>

        <?php
        $estadoCartera = $_POST['estadoCartera'];       $venceCartera = $_POST['venceCartera'];         $cargosPendientes = $_POST['cargosPendientes'];
        $detalleAnticipos = $_POST['detalleAnticipos']; $facturaPromedio = $_POST['facturaPromedio'];   $causaGlosas = $_POST['causaGlosas'];
        $causaDevolucion = $_POST['causaDevolucion'];   $causaFactura = $_POST['causaFactura'];         $evalAuditoria = $_POST['evalAuditoria'];
        $fechaInicial = $_POST['fechaInicial'];         $fechaFinal = $_POST['fechaFinal'];             $empresa = $_POST['empresa'];
        $serIngreso = $_POST['serIngreso'];             $cCostos = $_POST['cCostos'];                   $radioTipoEmpresa = $_POST['radioTipoEmpresa'];
        $radioTipopac = $_POST['radioTipopac'];
        ?>
        <!-- Tab links -->
        <div class="tab encTabs" style="background-color: #EEEEEE; margin-top: 40px; /*height: 100px*/">
            <button class="tablinks" style="width: 110px" onclick="openCity(event, 'rep1')">ESTADO DE CARTERA</button>
            <button class="tablinks" style="width: 115px" onclick="openCity(event, 'rep2')">VENCIMIENTO CARTERA RADICADA</button>
            <button class="tablinks" style="width: 132px" onclick="openCity(event, 'rep3')">CARGOS PENDIENTES POR FACTURAR</button>
            <button class="tablinks" style="width: 135px" onclick="openCity(event, 'rep4')">DETALLE ANTICIPOS SIN LEGALIZAR</button>
            <button class="tablinks" style="width: 125px" onclick="openCity(event, 'rep5')">FACTURACION PROMEDIO MENSUAL</button>
            <button class="tablinks" style="width: 125px" onclick="openCity(event, 'rep6')">CAUSALES GLOSAS POR GESTIONAR</button>
            <button class="tablinks" style="width: 140px" onclick="openCity(event, 'rep7')">CAUSALES DEVOLUCIONES POR GESTIONAR</button>
            <button class="tablinks" style="width: 125px" onclick="openCity(event, 'rep8')">CAUSALES FACTURACION SIN RADICAR</button>
            <button class="tablinks" style="width: 125px" onclick="openCity(event, 'rep9')">EVALUACION AUDITORIA EXTERNA</button>
        </div>

        <!-- Tab1 content: ESTADO DE CARTERA -->
        <div id="rep1" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>1. ESTADO DE CARTERA</label>
            </div>
            <div class="Reporte">
                <?php
                if($estadoCartera == 1)
                {
                    ?>
                    <div class="datosReporte1" id="divDatosReporte1">
                        <iframe src="estadoCuentaRep1.php?fechaInicial=<?php echo $fechaInicial ?>&fechaFinal=<?phpecho $fechaFinal?>" frameborder="0" height="250px" width="100%" scrolling="yes">
                        </iframe>
                    </div>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Tab2 content: VENCIMIENTO DE CARTERA RADICADA -->
        <div id="rep2" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>2. VENCIMIENTO DE LA CARTERA RADICADA</label>
            </div>
            <div class="datosRequerimiento">
                <?php
                if($venceCartera == 1)
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>REPORTE 2...</h3></td></tr>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Tab3 content: CARGOS PENDIENTES POR FACTURAR -->
        <div id="rep3" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>3. CARGOS PENDIENTES POR FACTURAR</label>
            </div>
            <div class="Reporte">
                <?php
                if($cargosPendientes == 1)
                {
                    ?>
                    <div class="datosReporte3" id="divDatosReporte3">
                        <table align="center" border="0" style="width: 640px">
                            <thead>
                            <tr align="center" style="background-color: #EEEEEE">
                                <td style="width: 80px"><label>HISTORIA</label></td>
                                <td style="width: 115px"><label>INGRESO</label></td>
                                <td style="width: 150px"><label>VALOR</label></td>
                                <td><label>SERVICIO</label></td>
                                <td><label>NOMBRE SERVICIO</label></td>
                                <td><label>FECHA INGRESO</label></td>
                                <td><label>FECHA EGRESO</label></td>
                                <td><label>NIT</label></td>
                                <td><label>ENTIDAD</label></td>
                                <td><label>HABITACION</label></td>
                            </tr>
                            </thead>
                        </table>

                        <!-- ABIR IFRAME VENTANA EXTERNA PARA ESTE REPORTE, PASANDO LAS VARIABLES POR GET: -->
                        <iframe src="estadoCuentaRep3.php?
                        fechaInicial=<?phpecho $fechaInicial?>
                        &fechaFinal=<?phpecho $fechaFinal?>
                        &empresa=<?phpecho $empresa?>
                        &serIngreso=<?phpecho $serIngreso?>
                        &cCostos=<?phpecho $cCostos?>
                        &radioTipoEmpresa=<?phpecho $radioTipoEmpresa?>
                        &radioTipopac=<?phpecho $radioTipopac?>"
                        frameborder="0" height="250px" width="100%" scrolling="yes">
                        </iframe>
                    </div>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Tab4 content: DETALLE ANTICIPOS SIN LEGALIZAR -->
        <div id="rep4" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>4. DETALLE DE ANTICIPOS SIN LEGALIZAR</label>
            </div>
            <div class="datosRequerimiento">
                <?php
                if($detalleAnticipos == 1)
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>REPORTE 4...</h3></td></tr>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Tab5 content: FACTURACION PROMEDIO MENSUAL -->
        <div id="rep5" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>5. FACTURACION PROMEDIO MENSUAL Y COMPOSICION</label>
            </div>
            <div class="datosRequerimiento">
                <?php
                if($facturaPromedio == 1)
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>REPORTE 5...</h3></td></tr>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Tab6 content: CAUSALES GLOSAS POR GESTIONAR -->
        <div id="rep6" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>6. CAUSALES DE GLOSAS POR GESTIONAR</label>
            </div>
            <div class="datosRequerimiento">
                <?php
                if($causaGlosas == 1)
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>REPORTE 6...</h3></td></tr>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Tab7 content: CAUSALES DEVOLUCIONES POR GESTIONAR -->
        <div id="rep7" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>7. CAUSALES DE DEVOLUCIONES POR GESTIONAR</label>
            </div>
            <div class="datosRequerimiento">
                <?php
                if($causaDevolucion == 1)
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>REPORTE 7...</h3></td></tr>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Tab8 content: CAUSALES FACTURACION SIN RADICAR -->
        <div id="rep8" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>8. CAUSALES FACTURACION SIN RADICAR</label>
            </div>
            <div class="datosRequerimiento">
                <?php
                if($causaFactura == 1)
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>REPORTE 8...</h3></td></tr>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- Tab9 content: EVALUACION AUDITORIA EXTERNA -->
        <div id="rep9" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>9. EVALUACION AUDITORIA EXTERNA</label>
            </div>
            <div class="datosRequerimiento">
                <?php
                if($evalAuditoria == 1)
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>REPORTE 9...</h3></td></tr>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <table align="center" border="0">
                        <tr><td><h3>Debe activar este reporte para ser generado...</h3></td></tr>
                    </table>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>