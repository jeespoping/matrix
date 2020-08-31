<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HELPDESK - MATRIX</title>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://matrixtest.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
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
        function guardar(codUsuario,nomUsuario,ccosUsuario,carUsuario,ccosReq,tipoReq,usuReq,desReq,fecIniReq,horIniReq,fecFinReq,horFinReq,accion)
        {
            ancho = 200;    alto = 245;

            var miPopup11 = null;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
            settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';

            miPopup11 = window.open("helpDeskProcesos.php?codUsuario="+codUsuario.value+
                "&nomUsuario="+nomUsuario.value+"&ccosUsuario="+ccosUsuario.value+
                "&carUsuario="+carUsuario.value+"&ccosReq="+ccosReq.value+
                "&tipoReq="+tipoReq.value+"&usuReq="+usuReq.value+
                "&desReq="+desReq.value+"&fecIniReq="+fecIniReq.value+
                "&horIniReq="+horIniReq.value+"&fecFinReq="+fecFinReq.value+
                "&horFinReq="+horFinReq.value+"&accion="+accion.value,
                "miwin",settings2);
            miPopup11.focus();
        }

        function opPaneles(idEtiqueta)
        {
            //alert(idEtiqueta);
            //nombreDiv = idEtiqueta;
            //var nombreDiv = document.getElementById(idEtiqueta).value;

            switch (idEtiqueta)
            {
                case 'xUsuario':
                    document.getElementById('xUsuario').style.display = 'block';
                    document.getElementById('xSemMes').style.display = 'none';
                break;
                case 'xSemMes':
                    document.getElementById('xUsuario').style.display = 'none';
                    document.getElementById('xSemMes').style.display = 'block';
                break;
            }
        }
    </script>
    <style>
        .datosIndicadores
        {
            border: none;
            margin-top: 0px;
            margin-bottom: 20px;
            width: 150px;
            height: 290px;
            /*background-color: #EEEEEE;*/
        }
        .listRow
        {
            border-bottom: inset;
        }
        .listRow:hover
        {
            background-color: #CCCCCC;
            color: snow;
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
            width: 490px;
            float: right;
            margin-top: -260px;
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
        

        //include_once("root/comun.php");
        include_once("helpDeskProcesos.php");
        


        $conex = obtenerConexionBD("matrix");
    }

    $fechaActual = date('Y-m-d');
    $horaActual = date('H:i:s');
    ?>
</head>

<body>
<div class="container general">
    <div class="panel panel-info contenido">
        <div class="panel-heading encabezado">
            <div class="panel-title">Matrix - Ingreso de Requerimientos</div>
        </div>

        <div class="titulo">
            <label>INFORMACION DEL USUARIO:</label>
        </div>

        <div class="datosUsuario">
            <table align="center" border="0">
                <tr>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 120px"><label for="codUsuario">CODIGO:</label></span>
                            <input id="codUsuario" name="codUsuario" type="text" class="form-control" style="width: 100px" value="<?php echo $wuse ?>" readonly>
                        </div>
                    </td>
                    <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 130px"><label for="nomUsuario">NOMBRE:</label></span>
                            <input id="nomUsuario" name="nomUsuario" type="text" class="form-control" style="width: 280px" value="<?php consultarDatosUsuario($wuse,1) ?>" readonly>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="input-group" style="margin-top: 10px">
                            <span class="input-group-addon" style="width: 120px"><label for="ccosUsuario">C. COSTOS:</label></span>
                            <input id="ccosUsuario" name="ccosUsuario" type="text" class="form-control" style="width: 100px" value="<?php consultarDatosUsuario($wuse,2) ?>" readonly>
                        </div>
                    </td>
                    <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                    <td>
                        <div class="input-group" style="margin-top: 10px">
                            <span class="input-group-addon" style="width: 130px"><label for="carUsuario">CARGO:</label></span>
                            <input id="carUsuario" name="carUsuario" type="text" class="form-control" style="width: 280px" value="<?php consultarDatosUsuario($wuse,3) ?>" readonly>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tab links -->
        <div class="tab" style="background-color: #EEEEEE;">
            <button class="tablinks" onclick="openCity(event, 'London')">REQUERIMIENTO</button>
            <button class="tablinks" onclick="openCity(event, 'Paris')">GESTION</button>
            <button class="tablinks" onclick="openCity(event, 'Tokyo')">INDICADORES</button>
        </div>

        <!-- Tab1 content -->
        <div id="London" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>DATOS DEL REQUERIMIENTO</label>
            </div>
            <div class="datosRequerimiento">
                <table align="center" border="0">
                    <tr>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" style="width: 120px"><label for="ccosReq">C. COSTOS:</label></span>
                                <select id="ccosReq" name="ccosReq" class="form-control" style="width: 190px" required>
                                    <?php
                                    $consespe2 = mysql_query("select Ccocod,Cconom from costosyp_000005 WHERE Ccoemp = '01' ORDER BY Cconom ASC");
                                    while($datoespe2 = mysql_fetch_array($consespe2))
                                    {
                                        echo "
                                        <option value='".$datoespe2['Ccocod']."'>
                                        ".$datoespe2['Cconom'].'-'.$datoespe2['Ccocod']."
                                        </option>
                                        ";
                                        //$ccostosReq = $datoespe2['Ccocod'];
                                    }
                                    ?>
                                    <option selected="selected" disabled value="">Seleccione...</option>
                                </select>
                            </div>
                        </td>
                        <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" style="width: 130px"><label for="tipoReq">TIPO:</label></span>
                                <select id="tipoReq" name="tipoReq" class="form-control" style="width: 190px">
                                    <option>Telefonico</option>
                                    <option>Presencial</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon" style="width: 120px"><label for="usuReq">SOLICITADO POR:</label></span>
                                <input id="usuReq" name="usuReq" type="text" class="form-control" style="width: 480px" value="" required>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="input-group" style="margin-top: 10px">
                            <span class="input-group-addon"><label for="desReq">DESCRIPCION:</label>
                                <br>
                                <textarea id="desReq" name="desReq" cols="65" rows="5" required></textarea>
                            </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon"><label for="fecIniReq" title="Fecha y Hora de Inicio">INICIO:</label></span>
                                <input id="fecIniReq" name="fecIniReq" type="text" class="form-control" style="width: 113px" value="<?php echo $fechaActual ?>">
                                <input id="horIniReq" name="horIniReq" type="text" class="form-control" style="width: 105px" value="<?php echo $horaActual ?>">
                            </div>
                        </td>
                        <td></td>
                        <td>
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon"><label for="fecFinReq" title="Fecha y Hora Fin">FIN:</label></span>
                                <input id="fecFinReq" name="fecFinReq" type="text" class="form-control" style="width: 113px" value="<?php echo $fechaActual ?>">
                                <input id="horFinReq" name="horFinReq" type="text" class="form-control" style="width: 105px" value="<?php echo $horaActual ?>">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="panelGuardar">
                <table align="center" border="0">
                    <tr>
                        <td>
                            <div class="col-sm-12 controls">
                                <input type="hidden" id="accion" name="accion" value="guardar">
                                <input type="button" class="btn btn-info btn-sm" id="btnIr" name="btnIr" title="Guardar" value="GUARDAR"
                                       onclick="guardar(codUsuario,nomUsuario,ccosUsuario,carUsuario,ccosReq,tipoReq,usuReq,desReq,
                                       fecIniReq,horIniReq,fecFinReq,horFinReq,accion)">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Tab2 content -->
        <div id="Paris" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>REQUERIMIENTOS GESTIONADOS</label>
            </div>
            <?php
            $usuarioReq = 'C-'.$wuse;
            $accion = 'consultar';
            ?>
            <div class="datosConsulta" id="divDatosConsulta">
                <!--
                <table align="center" border="0" style="width: 640px">
                    <thead>
                    <tr align="center" style="background-color: #EEEEEE">
                        <td style="width: 80px"><label>FECHA</label></td>
                        <td style="width: 115px"><label>SOLICITANTE</label></td>
                        <td style="width: 150px"><label>UNIDAD</label></td>
                        <td><label>TIPO</label></td>
                        <td><label>DESCRIPCION</label></td>
                    </tr>
                    </thead>
                </table>
                -->
                <iframe src="helpDeskProcesos.php?idUsuario=<?php echo $usuarioReq ?>&accion=<?php echo $accion ?>" frameborder="0" height="320px" width="643px" scrolling="yes">
                </iframe>
            </div>
        </div>

        <!-- Tab3 content -->
        <div id="Tokyo" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>INDICADORES Y COMPARATIVAS</label>
            </div>
            <div class="datosIndicadores">
                <table>
                    <tr style="border-bottom: groove; background-color: #CCCCCC"><td>&ensp;</td><td><h4>Categoria:</h4></td></tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#" onclick="opPaneles('xUsuario')">Comparativa por Usuario</a></label></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#" onclick="opPaneles('xSemMes')">Comparativa por Semana y por Mes</a></label></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#">Porcentaje de Causas</a></label></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#">Promedio Duracion Llamadas</a></label></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#">Llamadas por Area</a></label></td>
                    </tr>
                </table>
            </div>

            <div id="xUsuario" class="divxUsuario" style="display: none">
                <h3>COMPARATIVA POR USUARIO</h3>
            </div>
            <div id="xSemMes" class="divxUsuario" style="display: none">
                <h3>COMPARATIVA POR SEMANA Y POR MES</h3>
            </div>
            <div id="xPorCausas" class="divxUsuario" style="display: none">
                <h3>PORCENTAJE DE CAUSAS</h3>
            </div>
            <div id="xProDurLlama" class="divxUsuario" style="display: none">
                <h3>PROMEDIO DURACION LLAMADAS</h3>
            </div>
            <div id="xLlamaAreas" class="divxUsuario" style="display: none">
                <h3>LLAMADAS POR AREA</h3>
            </div>
        </div>
    </div>
</div>
</body>
</html>