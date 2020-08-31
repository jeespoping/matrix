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
            switch (idEtiqueta)
            {
                case 'xUsuario':
                    document.getElementById('xUsuario').style.display = 'block';
                    document.getElementById('xSemMes').style.display = 'none';
                    document.getElementById('xPorCausas').style.display = 'none';
                    document.getElementById('xProDurLlama').style.display = 'none';
                    document.getElementById('xLlamaAreas').style.display = 'none';
                break;
                case 'xSemMes':
                    document.getElementById('xUsuario').style.display = 'none';
                    document.getElementById('xSemMes').style.display = 'block';
                    document.getElementById('xPorCausas').style.display = 'none';
                    document.getElementById('xProDurLlama').style.display = 'none';
                break;
                case 'xPorCausas':
                    document.getElementById('xUsuario').style.display = 'none';
                    document.getElementById('xSemMes').style.display = 'none';
                    document.getElementById('xPorCausas').style.display = 'block';
                    document.getElementById('xProDurLlama').style.display = 'none';
                    document.getElementById('xLlamaAreas').style.display = 'none';
                break;
                case 'xProDurLlama':
                    document.getElementById('xUsuario').style.display = 'none';
                    document.getElementById('xSemMes').style.display = 'none';
                    document.getElementById('xPorCausas').style.display = 'none';
                    document.getElementById('xProDurLlama').style.display = 'block';
                    document.getElementById('xLlamaAreas').style.display = 'none';
                break;
                case 'xLlamaAreas':
                    document.getElementById('xUsuario').style.display = 'none';
                    document.getElementById('xSemMes').style.display = 'none';
                    document.getElementById('xPorCausas').style.display = 'none';
                    document.getElementById('xProDurLlama').style.display = 'none';
                    document.getElementById('xLlamaAreas').style.display = 'block';
                break;
            }
        }
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
        .listRow
        {
            /*
            border-bottom: groove;
            border-bottom-color: #FF3B24;
            */
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

        <!-- Tab1 content: REQUERIMIENTO -->
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
                                    $consespe2 = mysql_query("select Ccocod,Cconom from costosyp_000005 WHERE Ccoemp = '01' AND Ccocod NOT LIKE '5%' ORDER BY Cconom ASC");
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

        <!-- Tab2 content: RESUMEN -->
        <div id="Paris" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>REQUERIMIENTOS GESTIONADOS</label>
            </div>
            <?php
            $usuarioReq = 'C-'.$wuse;
            $accion = 'consultar';
            ?>
            <div class="datosConsulta" id="divDatosConsulta">
                <iframe src="helpDeskProcesos.php?idUsuario=<?php echo $usuarioReq ?>&accion=<?php echo $accion ?>" frameborder="0" height="320px" width="643px" scrolling="yes">
                </iframe>
            </div>
        </div>

        <!-- Tab3 content: INDICADORES -->
        <div id="Tokyo" class="tabcontent">
            <div class="titulo" style="margin-top: 0">
                <label>INDICADORES Y COMPARATIVAS</label>
            </div>
            <div class="datosIndicadores">
                <table>
                    <tr>
                        <td>&ensp;</td>
                        <td>&ensp;</td>
                        <td></td>
                    </tr>
                    <tr style="border-bottom: groove">
                        <td>&ensp;</td>
                        <td><h4>Categoria:</h4></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#" onclick="opPaneles('xUsuario')">Usuarios</a></label></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#" onclick="opPaneles('xSemMes')">Periodos</a></label></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#" onclick="opPaneles('xPorCausas')">Causas</a></label></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#" onclick="opPaneles('xProDurLlama')">Llamadas</a></label></td>
                    </tr>
                    <tr class="listRow">
                        <td>&ensp;</td>
                        <td><label><a href="#" onclick="opPaneles('xLlamaAreas')">Areas</a></label></td>
                    </tr>
                </table>
            </div>

            <div id="xUsuario" class="divxUsuario" align="center" style="display: none">
                <h4>COMPARATIVA POR USUARIO - <?php echo $fechaActual ?></h4>
                <div id="divContenido1" class="divContenido1">
                    <?php
                    $queryParticipante = mysql_query("select Seguridad from equipos_000006 where Fecini = '$fechaActual' GROUP BY Seguridad");
                    ?>
                    <table>
                        <thead>
                        <tr align="center">
                            <td><label>USUARIO</label></td>
                            <td><label>NUM. CASOS</label></td>
                            <td>&ensp;<label>PROMEDIO</label></td>
                            <td>&ensp;<label>TIEMPO</label></td>
                        </tr>
                        </thead>
                        <?php
                        while($datoParticipante = mysql_fetch_array($queryParticipante))
                        {
                            $participante = $datoParticipante[0];
                            $usuario = str_replace("C-","",$participante);

                            $querySuma = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Seguridad = '$participante' AND Fecini = '$fechaActual'");
                            $datoSuma = mysql_fetch_array($querySuma);
                            $suma = $datoSuma[0];
                            ?>
                            <tr class="alternar">
                                <td><?php consultarDatosUsuario($usuario, 1) ?></td>
                                <td align="center"><?php echo $suma ?></td>
                                <td align="center"><?php operaciones($participante,$fechaActual,1) ?></td>
                                <td align="right"><?php operaciones($participante,$fechaActual,2)?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>
            <div id="xSemMes" class="divxSemMes" align="center" style="display: none">
                <h4>COMPARATIVA POR MES</h4>
                <div id="divContenido1" class="divContenido1">
                    <?php
                    $fechaActual = date('Y-m-d');
                    $ma = explode('-',$fechaActual);
                    $mesActual = $ma[1];
                    $añoActual = $ma[0];
                    ?>
                    <table>
                        <thead>
                        <tr>
                            <td>&ensp;<label>MES:</label></td>
                            <td><label>TOTAL CASOS:</label></td>
                        </tr>
                        </thead>
                        <?php
                        consultarDatosPeriodo($mesActual);
                        consultarDatosAño($añoActual);
                        ?>
                    </table>
                </div>
            </div>
            <div id="xPorCausas" class="divxPorCausas" align="center" style="display: none">
                <h4>PORCENTAJE DE CAUSAS</h4>
            </div>
            <div id="xProDurLlama" class="divxProDurLlama" align="center" style="display: none">
                <h4>PROMEDIO DURACION LLAMADAS</h4>
            </div>
            <div id="xLlamaAreas" class="divxLlamaAreas" align="center" style="display: none">
                <h4>LLAMADAS POR AREA</h4>
                <div id="divContenido1" class="divContenido1">
                    <?php $accion = 'areas' ?>
                    <table border="0">
                        <thead>
                        <tr align="center">
                            <td align="center" width="240"><label>AREA</label></td>
                            <td><label>N. LLAMADAS</label></td>
                            <td>&ensp;<label>PROMEDIO</label></td>
                            <td width="100">&ensp;<label>TIEMPO</label></td>
                        </tr>
                        </thead>
                    </table>
                    <div class="datosConsulta" id="divDatosConsulta">
                        <iframe src="helpDeskProcesos.php?accion=<?php echo $accion ?>&fechaActual=<?phpecho $fechaActual?>" frameborder="0" height="250px" width="540px" scrolling="yes">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>