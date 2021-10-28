<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HELPDESK - MATRIX</title>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk2.css" rel="stylesheet">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link href="http://mtx.lasamericas.com.co/matrix/Library/Css/Bootstrap_v3.0.0.css" rel="stylesheet">
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/bootstrap_v3.0.0.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/jQuery_v1.11.1.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/Bootstrap_v3.1.0.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/GoogleCharts.js" type="text/javascript"></script>
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
        function guardar(codUsuario,nomUsuario,ccosUsuario,carUsuario,ccosReq,tipoReq,usuReq,tipoUsuarioReq,origenReq,causaReq,
                         desReq,fecIniReq,horIniReq,accion)
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
                "&tipoUsuReq="+tipoUsuarioReq.value+"&origenReq="+origenReq.value+
                "&causaReq="+causaReq.value+"&desReq="+desReq.value+
                "&fecIniReq="+fecIniReq.value+"&horIniReq="+horIniReq.value+
                "&accion="+accion.value,
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
        .grafico
        {
            border: groove;
        }
    </style>
    <?php
    include_once("conex.php");
    include_once("helpDeskProcesos.php");

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
        mysql_select_db("matrix");

        $conex = obtenerConexionBD("matrix");
    }
    $fechaActual = date('Y-m-d');
    //$fechaActual = '2020-04-17';
    $horaActual = date('H:i:s');
    ?>
</head>

<body onload="window.resizeTo(725,790);">
<div class="container general" style="margin-left: 0">
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
                                    $consespe2 = mysql_queryV("select Ccocod,Cconom
                                                              from costosyp_000005
                                                              WHERE Ccoemp = '01'
                                                              AND Ccocod NOT LIKE '5%'
                                                              AND Cconom NOT LIKE 'NO USAR%'
                                                              ORDER BY Cconom ASC");
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
                                    <option>Correo</option>
                                    <option>Desplazamiento</option>
                                    <option>Presencial</option>
                                    <option selected>Telefonico</option>
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
                        <td>
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon" style="width: 120px"><label for="tipoUsuarioReq">TIPO USUARIO:</label></span>
                                <select id="tipoUsuarioReq" name="tipoUsuarioReq" class="form-control" style="width: 160px" required>
                                    <option>Interno</option>
                                    <option>Externo</option>
                                    <option selected="selected" disabled value="">Interno</option>
                                </select>
                            </div>
                        </td>
                        <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                        <td>
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon" style="width: 130px"><label for="origenReq">ORIGEN:</label></span>
                                <select id="origenReq" name="origenReq" class="form-control" style="width: 190px">
                                    <option>Fallas en Aplicativo</option>
                                    <option>Manejo Inadecuado</option>
                                    <option>Reporte</option>
                                    <option>Otro</option>
                                    <option selected="selected" disabled value="">Seleccione...</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon" style="width: 150px"><label for="causaReq">CAUSA:</label></span>
                                <select id="causaReq" name="causaReq" class="form-control" style="width: 500px" required>
                                    <?php
                                    switch($wuse)
                                    {
                                        case ($wuse == '0100463')||($wuse == '00140')||($wuse == '0104935')||($wuse == '04843')||($wuse =='0205681')||($wuse =='00471'):
                                            $consespe2 = mysql_queryV("select id_causa,descripcion from equipos_000007 WHERE grupo = '01' ORDER BY id_causa ASC");
                                            break;
                                        case ($wuse == '0107491')||($wuse == '0101187')||($wuse == '0105351')||($wuse == '07726')||($wuse =='0101063'):
                                            $consespe2 = mysql_queryV("select id_causa,descripcion from equipos_000007 WHERE grupo = '02' ORDER BY id_causa ASC");
                                            break;
                                        default:
                                            echo 'Usuario no registrado';
                                            break;
                                    }
                                    while($datoespe2 = mysql_fetch_array($consespe2))
                                    {
                                        echo "<option value='".$datoespe2['id_causa']."'>
                                                 ".$datoespe2['descripcion'].'-'.$datoespe2['id_causa']."
                                                  </option>";
                                    }
                                    ?>
                                    <option selected="selected" disabled value="">Seleccione...</option>
                                </select>
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
                        <td colspan="4" align="center">
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon" style="width: 200px"><label for="fecIniReq" title="Fecha y Hora de Inicio">FECHA / DURACION:</label></span>
                                <input id="fecIniReq" name="fecIniReq" type="text" class="form-control" style="width: 113px" value="<?php echo $fechaActual ?>">
                                <input id="horIniReq" name="horIniReq" type="number" min="10" class="form-control" style="width: 105px" placeholder="Minutos" value="10" title="MINUTOS">
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
                                       onclick="guardar(codUsuario,nomUsuario,ccosUsuario,carUsuario,ccosReq,tipoReq,usuReq,
                                                        tipoUsuarioReq,origenReq,causaReq,desReq,fecIniReq,horIniReq,accion)">
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
        <div id="Tokyo" class="tabcontent" style="border: none; height: 450px">
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
                        <td><label><a href="#" onclick="opPaneles('xSemMes')">Solicitudes</a></label></td>
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
                    $queryParticipante = mysql_queryV("select Seguridad from equipos_000006 where Fecini = '$fechaActual' GROUP BY Seguridad");
                    ?>
                    <table>
                        <thead>
                        <tr align="center"><td colspan="4"><label>Requerimientos Telefonicos y Presenciales:</label></td></tr>
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

                            $querySuma = mysql_queryV("SELECT COUNT(id) from equipos_000006 WHERE Seguridad = '$participante' AND Fecini = '$fechaActual'");
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
                    <br><br>
                    <?php
                    $queryParticipante2 = mysql_queryV("select Reqpurs
                                                       from root_000040
                                                       where Reqfen = '$fechaActual'
                                                       and Reqcco = '(01)1710'
                                                       and Reqtip = '02'
                                                       and Reqest = '05'
                                                       GROUP BY Reqpurs");
                    ?>
                    <table style="width: 100%" border="0">
                        <thead>
                        <tr align="center"><td colspan="4"><label>Requerimientos Matrix:</label></td></tr>
                        </thead>
                        <?php
                        while($datoParticipante2 = mysql_fetch_array($queryParticipante2))
                        {
                            $participante2 = $datoParticipante2[0];
                            $usuario2 = $participante2;
                            $querySuma2 = mysql_queryV("SELECT COUNT(id) from root_000040 WHERE Reqpurs = '$participante2' AND Reqfen = '$fechaActual'");
                            $datoSuma2 = mysql_fetch_array($querySuma2);
                            $suma2 = $datoSuma2[0];
                            ?>
                            <tr class="alternar">
                                <td style="width: 265px"><?php consultarDatosUsuario($usuario2, 1) ?></td>
                                <td align="center"><?php echo $suma2 ?></td>
                                <td align="center"><?php operaciones2($participante2,$fechaActual,1) ?></td> <!-- Porcentaje casos-->
                                <td align="right">&ensp;&ensp;&ensp;&ensp;&ensp;</td> <!-- Total minutos-->
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
                            <td>&ensp;&ensp;&ensp;</td>
                            <td>&ensp;<label>MES:</label></td>
                            <td><label>TOTAL CASOS:</label></td>
                        </tr>
                        </thead>
                        <?php
                        consultarDatosPeriodo($mesActual,$añoActual);
                        consultarDatosAño($añoActual);
                        ?>
                    </table>
                </div>
            </div>
            <div id="xPorCausas" class="divxPorCausas" align="center" style="display: none; margin-bottom: -10px">
                <?php
                switch($mesActual)
                {
                    case '01': $mesHoy = 'Enero'; break;  case '02': $mesHoy = 'Febrero'; break;
                    case '03': $mesHoy = 'Marzo'; break;  case '04': $mesHoy = 'Abril'; break;
                    case '05': $mesHoy = 'Mayo'; break;   case '06': $mesHoy = 'Junio'; break;
                    case '07': $mesHoy = 'Julio'; break;  case '08': $mesHoy = 'Agosto'; break;
                    case '09': $mesHoy = 'Septiembre'; break;   case '10': $mesHoy = 'Octubre'; break;
                    case '11': $mesHoy = 'Noviembre'; break;    case '12': $mesHoy = 'Diciembre'; break;
                }
                ?>
                <h4>NUMERO DE CAUSAS POR MES Y A&Ntilde;O</h4>
                <h5 style="font-weight: bold"><?php echo $mesHoy.' - '.$añoActual ?></h5>
                <table border="0">
                    <thead>
                    <tr>
                        <td style="text-align: left">&ensp;<label>DESCRIPCION</label></td>
                        <td>&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;</td>
                        <td style="text-align: center"><label>TOTAL - PROMEDIO<br>MES</label></td>
                        <td>&ensp;&ensp;&ensp;&ensp;&ensp;</td>
                        <td style="text-align: center" colspan="2"><label>TOTAL - PROMEDIO<br>A&Ntilde;O</label></td>
                    </tr>
                    </thead>
                </table>
                <div id="divContenido1" class="divContenido1" style="height: 213px; overflow: auto; margin-top: -5px">
                    <table border="0" style="width: 100%">
                        <?php
                        consultarDatosCausas($mesActual,$añoActual);
                        ?>
                    </table>
                </div>
            </div>
            <div id="xProDurLlama" class="divxProDurLlama" align="center" style="display: none">
                <h4>CANTIDAD LLAMADAS POR AREA ESTE A&Ntilde;O</h4>
                <div id="divContenido1" class="divContenido1">
                    <?php
                    consultarGrafico1($añoActual)
                    ?>
                </div>
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
                        <iframe src="helpDeskProcesos.php?accion=<?php echo $accion ?>&fechaActual=<?php echo $fechaActual?>" frameborder="0" height="242px" width="540px" scrolling="yes">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>