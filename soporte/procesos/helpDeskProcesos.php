<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>HELPDESK - MATRIX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://matrixtest.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <style>
        body {overflow-x:hidden;}
        .alternar:hover {background-color: #D9EDF7;}
    </style>
    <script>
        function actualizar(idReq,ccosReq,tipoReq,usuReq,tipoUsuarioReq,origenReq,causaReq,desReq,fecIniReq,horIniReq,accion)
        {
            ancho = 200;    alto = 245;

            var miPopup11 = null;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
            settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';

            miPopup11 = window.open("helpDeskProcesos.php?idReq="+idReq.value+
                "&ccosReq="+ccosReq.value+"&tipoReq="+tipoReq.value+
                "&usuReq="+usuReq.value+"&tipoUsuReq="+tipoUsuarioReq.value+
                "&origenReq="+origenReq.value+"&causaReq="+causaReq.value+
                "&desReq="+desReq.value+"&fecIniReq="+fecIniReq.value+
                "&horIniReq="+horIniReq.value+"&accion="+accion.value,
                "miwin",settings2);
            miPopup11.focus();
        }
    </script>
    <?php
    include_once("conex.php");
    include_once("root/comun.php");

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
    $codUsuario = $_GET['codUsuario'];  $nomUsuario = $_GET['nomUsuario'];  $ccosUsuario = $_GET['ccosUsuario'];
    $carUsuario = $_GET['carUsuario'];  $ccosReq = $_GET['ccosReq'];        $tipoReq = $_GET['tipoReq'];
    $usuReq = $_GET['usuReq'];          $tipoUsuReq = $_GET['tipoUsuReq'];  $origenReq = $_GET['origenReq'];
    $causaReq = $_GET['causaReq'];      $desReq = $_GET['desReq'];          $fecIniReq = $_GET['fecIniReq'];
    $horIniReq = $_GET['horIniReq'];    $fecFinReq = $_GET['fecFinReq'];    $horFinReq = $_GET['horFinReq'];
    $seguridad = 'C-'.$codUsuario;      $usuarioReq = $_GET['idUsuario'];   $idReq = $_GET['idReq'];
    $accion = $_GET['accion'];          $subaccion = $_GET['subaccion'];    $fechaActual2 = date('Y-m-d');
    ?>
</head>

<body>
<?php
if($accion == 'guardar')
{
    mysql_query("INSERT INTO equipos_000006 (Medico,Fecha_data,Hora_data,Nomusu,Ccousu,Carusu,Ccoreq,Tipreq,Nomreq,Tipousureq,Origenreq,Causareq,
                          Desreq,Fecini,Horini,Fecfin,Horfin,Seguridad)
                 VALUES ('Equipos','$fecIniReq','$horIniReq','$nomUsuario','$ccosUsuario','$carUsuario','$ccosReq','$tipoReq','$usuReq','$tipoUsuReq',
                         '$origenReq','$causaReq','$desReq','$fecIniReq','$horIniReq','$fecFinReq','$horFinReq','$seguridad') ");

    ?>
    <div style="margin-top: 100px; margin-left: -600px; text-align: center">
        <form method="post" action="helpDesk_01.php">
            <label style="color: #080808">Datos Almacenados Correctamente</label>
            <br><br>
            <script>
                /*window.opener.location.reload();*/
                /*window.close();*/
            </script>
            <input type="submit" class="text-success" value="ACEPTAR"/>
        </form>
    </div>
    <?php
}

if($accion == 'consultar')
{
    if($subaccion == 'modificar')
    {
        $queryReq = mysql_query("SELECT * from equipos_000006 WHERE id = '$idReq'");
        $datoReq = mysql_fetch_array($queryReq);
        $fecha = $datoReq['Fecha_data'];    $solicitante = $datoReq['Nomreq'];      $uni = $datoReq['Ccousu'];
        $tipo = $datoReq['Tipreq'];         $descripcion = $datoReq[12];            $idReq = $datoReq[18];
        $fechaIni = $datoReq[13];           $horaIni = $datoReq[14];                $fechaFin = $datoReq[15];
        $horaFin = $datoReq[16];            $tipoUsuario = $datoReq['Tipousureq'];  $origen = $datoReq['Origenreq'];
        $causa = $datoReq['Causareq'];

        $queryCco = mysql_query("select Cconom from costosyp_000005 WHERE Ccocod = '$uni'");
        $datoCco = mysql_fetch_array($queryCco);
        $unidad = $datoCco[0];
        ?>
        <div class="datosRequerimiento" style="margin-left: -627px;">
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
                                }
                                ?>
                                <option selected="selected" value="<?php echo $uni ?>"><?php echo $unidad.'-'.$uni ?></option>
                            </select>
                        </div>
                    </td>
                    <td><div class="input-group-addon" style="background-color: #ffffff; width: 5px; border: none"></div></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 90px"><label for="tipoReq">TIPO:</label></span>
                            <select id="tipoReq" name="tipoReq" class="form-control" style="width: 190px">
                                <option>Telefonico</option>
                                <option>Presencial</option>
                                <option selected="selected" value="<?php echo $tipo ?>"><?php echo $tipo ?></option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="input-group" style="margin-top: 10px">
                            <span class="input-group-addon" style="width: 120px"><label for="usuReq">SOLICITADO POR:</label></span>
                            <input id="usuReq" name="usuReq" type="text" class="form-control" style="width: 450px" value="<?php echo $solicitante ?>" required>
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
                                <option selected="selected" value="<?php echo $tipoUsuario ?>"><?php echo $tipoUsuario ?></option>
                            </select>
                        </div>
                    </td>
                    <td><div class="input-group-addon" style="background-color: #ffffff; width: 5px; border: none"></div></td>
                    <td>
                        <div class="input-group" style="margin-top: 10px">
                            <span class="input-group-addon" style="width: 130px"><label for="origenReq">ORIGEN:</label></span>
                            <select id="origenReq" name="origenReq" class="form-control" style="width: 150px">
                                <option>Fallas en Aplicativo</option>
                                <option>Manejo Inadecuado</option>
                                <option>Reporte</option>
                                <option selected="selected" value="<?php echo $origen ?>"><?php echo $origen ?></option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="input-group" style="margin-top: 10px">
                            <span class="input-group-addon" style="width: 150px"><label for="causaReq">CAUSA:</label></span>
                            <select id="causaReq" name="causaReq" class="form-control" style="width: 460px" required>
                                <?php
                                switch($wuse)
                                {
                                    case ($wuse == '0100463')||($wuse == '00140')||($wuse == '0104935')||($wuse == '04843')||($wuse =='0205681'):
                                        $consespe2 = mysql_query("select id_causa,descripcion from equipos_000007 WHERE grupo = '01' ORDER BY id_causa ASC");
                                        break;
                                    case ($wuse == '0107491')||($wuse == '0101187')||($wuse == '0105351')||($wuse == '07726')||($wuse =='0101063'):
                                        $consespe2 = mysql_query("select id_causa,descripcion from equipos_000007 WHERE grupo = '02' ORDER BY id_causa ASC");
                                        break;
                                    default:
                                        echo 'Usuario no registrado';
                                        break;
                                }
                                while($datoespe2 = mysql_fetch_array($consespe2))
                                {
                                    $descCausa = $datoespe2['descripcion']; $codCausa = $datoespe2['id_causa'];
                                    echo "<option value='".$codCausa."'>
                                                 ".$descCausa.'-'.$codCausa."
                                         </option>";
                                }

                                $qCausa = mysql_query("SELECT descripcion from equipos_000007 WHERE id_causa = '$causa'");
                                while($dCausa = mysql_fetch_array($qCausa))
                                {
                                    $Causa = $dCausa[0];
                                }
                                ?>
                                <option selected="selected" value="<?php echo $causa ?>"><?php echo $Causa; ?></option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="input-group" style="margin-top: 10px">
                            <span class="input-group-addon"><label for="desReq">DESCRIPCION:</label>
                                <br>
                                <textarea id="desReq" name="desReq" cols="60" rows="5" required><?php echo $descripcion ?></textarea>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" align="center">
                        <div class="input-group" style="margin-top: 10px">
                            <span class="input-group-addon" style="width: 200px"><label for="fecIniReq" title="Fecha y Hora de Inicio">INICIO:</label></span>
                            <input id="fecIniReq" name="fecIniReq" type="text" class="form-control" style="width: 113px" value="<?php echo $fechaIni ?>">
                            <input id="horIniReq" name="horIniReq" type="number" min="1" class="form-control" style="width: 105px" value="<?php echo $horaIni ?>">
                        </div>
                    </td>
                    <td></td>
                </tr>
            </table>
        </div>
        <div class="panelGuardar" style="margin-left: -645px;">
            <table align="center" border="0">
                <tr>
                    <td>
                        <div class="col-sm-12 controls">
                            <input type="hidden" id="accion" name="accion" value="actualizar">
                            <input type="hidden" id="idReq" name="idReq" value="<?php echo $idReq ?>">
                            <input type="button" class="btn btn-info btn-sm" id="btnIr" name="btnIr" title="Actualizar" value="ACTUALIZAR"
                                   onclick="actualizar(idReq,ccosReq,tipoReq,usuReq,tipoUsuarioReq,origenReq,causaReq,desReq,fecIniReq,horIniReq,accion)">
                        </div>
                    </td>
                    <td>
                        <div class="col-sm-12 controls">
                            <form method="get" action="helpDeskProcesos.php">
                                <input type="hidden" id="accion" name="accion" value="consultar">
                                <input type="hidden" id="idUsuario" name="idUsuario" value="<?php echo $usuarioReq ?>">
                                <input type="submit" class="btn btn-success btn-sm" id="btnIr" name="btnIr" title="Volver" value="VOLVER...">
                            </form>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
    else
    {
        $queryReq = mysql_query("SELECT * from equipos_000006 WHERE Seguridad = '$usuarioReq' AND Fecini = '$fechaActual2' ORDER BY Fecini DESC");
        ?>
        <div id="datosConsultar" class="datosConsultar" style="width: 628px">
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
            <table align="center" border="0">
                <?php
                while($datoReq = mysql_fetch_array($queryReq))
                {
                    $fecha = $datoReq[1];   $solicitante = $datoReq[8]; $uni = $datoReq[6];
                    $tipo = $datoReq[7];    $descripcion = $datoReq[12]; $idReq = $datoReq[18];
                    $queryCco = mysql_query("select Cconom from costosyp_000005 WHERE Ccocod = '$uni'");
                    $datoCco = mysql_fetch_array($queryCco);
                    $unidad = $datoCco[0];

                    $descripcion = substr($descripcion, 0, 30)."...";
                    ?>
                    <tbody>
                    <tr class="alternar" style="border-bottom: groove">
                        <td style="width: 50px">
                            <label style="font-weight: normal">
                                <a href="helpDeskProcesos.php?accion=consultar&idUsuario=<?php echo $usuarioReq?>&subaccion=modificar&idReq=<?php echo $idReq ?>" style="text-decoration: none" title="Clic Para Modificar">
                                    <?php echo $fecha ?>
                                </a>
                            </label>
                        </td>
                        <td style="width: 130px">&ensp;<label style="font-weight: normal"><?php echo $solicitante ?></label></td>
                        <td style="width: 130px">&ensp;<label style="font-weight: normal"><?php echo $unidad ?></label></td>
                        <td style="width: 100px">&ensp;<label style="font-weight: normal"><?php echo $tipo ?></label></td>
                        <td><label style="font-weight: normal" title="<?php echo $datoReq[9]?>"><?php echo $descripcion ?></label></td>
                    </tr>
                    </tbody>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
    }
}

if($accion == 'areas')
{
    $fechaActual = date('Y-m-d');
    $queryArea = mysql_query("SELECT Ccoreq from equipos_000006 WHERE Fecini = '$fechaActual' GROUP BY Ccoreq");
    ?>
    <table>
        <tbody>
        <?php
        while($datoArea = mysql_fetch_array($queryArea))
        {
            $codArea = $datoArea[0];
            ?>
            <tr class="alternar" style="border-bottom: 1px groove black;">
                <td width="245" style="font-size: small"><?php consultarDatosArea($codArea,$fechaActual,1) ?></td>
                <td width="80" align="center"><?php consultarDatosArea($codArea,$fechaActual,2) ?></td>
                <td width="105" align="center"><?php consultarDatosArea($codArea,$fechaActual,3) ?></td>
                <td align="right"><?php consultarDatosArea($codArea,$fechaActual,4) ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php
}

if($accion == 'actualizar')
{
    mysql_query("UPDATE equipos_000006 SET Ccoreq = '$ccosReq',Tipreq = '$tipoReq',Nomreq = '$usuReq',Tipousureq = '$tipoUsuReq',
                                           Origenreq = '$origenReq',Causareq = '$causaReq',Desreq = '$desReq',Fecini = '$fecIniReq',
                                           Horini = '$horIniReq',Fecfin='$fecFinReq',Horfin='$horFinReq'
                 WHERE id = '$idReq'");

    ?>
    <div style="margin-top: 100px; margin-left: -600px; text-align: center">
        <form method="post" action="helpDesk_01.php">
            <label style="color: #080808">Datos Actualizados Correctamente</label>
            <br><br>
            <input type="submit" class="text-success" value="ACEPTAR"/>
        </form>
    </div>
    <?php
}

function consultarDatosUsuario($codUsuario,$variable)
{
    $usua = str_replace("01","", $codUsuario);
    $usuario = $usua.'-01';

    $queryDatosUsuario = mysql_query("SELECT Ideced,Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg from talhuma_000013 WHERE Ideuse = '$usuario'");
    $datoUsuario = mysql_fetch_array($queryDatosUsuario);

    if($datoUsuario == null)
    {
        $usuario = $codUsuario.'-01';
        $queryDatosUsuario = mysql_query("SELECT Ideced,Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg from talhuma_000013 WHERE Ideuse = '$usuario'");
        $datoUsuario = mysql_fetch_array($queryDatosUsuario);
    }

    $queryNomUsuario = mysql_query("select Descripcion,Ccostos from usuarios WHERE Codigo = '$codUsuario'");
    $datoBomUsuario = mysql_fetch_array($queryNomUsuario);


    //$documento = $datoUsuario[0];   $nombre1 = $datoUsuario[1];     $nombre2 = $datoUsuario[2];
    //$apellido1 = $datoUsuario[3];   $apellido2 = $datoUsuario[4];
    $cCoctos = $datoBomUsuario[1];
    $codCargo = $datoUsuario[6];
    //$nombreCompleto = $nombre1.' '.$nombre2.' '.$apellido1.' '.$apellido2;
    $nombreCompleto = $datoBomUsuario['Descripcion'];

    $queryCargoUsuario = mysql_query("SELECT Cardes from root_000079 WHERE Carcod = '$codCargo'");
    $datoCargo = mysql_fetch_array($queryCargoUsuario);
    $descCargo = $datoCargo[0];

    switch($variable)
    {
        case 1: echo $nombreCompleto;
            break;
        case 2: echo $cCoctos;
            break;
        case 3: echo $descCargo;
            break;
    }
}

function consultarDatosArea($codArea,$fechaActual,$variable)
{
    $queryDatosArea = mysql_query("SELECT Cconom from costosyp_000005 WHERE Ccocod = '$codArea'");
    $datoArea = mysql_fetch_array($queryDatosArea);
    $nombreArea = $datoArea[0]; //areas llamadas por dia

    $queryTotalCasos = mysql_query("select COUNT(id) from equipos_000006 WHERE Fecini = '$fechaActual'");
    $datoTotalCasos = mysql_fetch_array($queryTotalCasos);
    $totalCasos = $datoTotalCasos[0];  //total Casos por dia

    $querySumaArea = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Ccoreq = '$codArea' and Fecini = '$fechaActual'");
    $datoSumaArea = mysql_fetch_array($querySumaArea);
    $totalSumaArea = $datoSumaArea[0]; //total llamadas por area por dia

    //$queryMinutos = mysql_query("select sum(timediff(Horfin,Horini)) from equipos_000006 WHERE Ccoreq = '$codArea' and Fecini = '$fechaActual'");
    $queryMinutos = mysql_query("select sum(Horini) from equipos_000006 WHERE Ccoreq = '$codArea' and Fecini = '$fechaActual'");
    $datoMinutos = mysql_fetch_array($queryMinutos);
    $totalT = $datoMinutos[0];
    $totalTiempo = str_replace("00.000000","",$totalT);

    switch($variable)
    {
        case 1:
            echo $nombreArea;
        break;
        case 2:
            echo $totalSumaArea;
        break;
        case 3:
            $porcentaje = ($totalSumaArea/$totalCasos)*100;
            $porcentaje = round($porcentaje,2);
            echo $porcentaje.' '.'%';
        break;
        case 4:
            echo $totalTiempo.' '.'minutos';
        break;
    }
}

//Operaciones para casos generados por llamadas:
function operaciones($participante,$fechaActual,$parametro)
{
    $consulta = mysql_query("select COUNT(Seguridad) from equipos_000006 WHERE Fecini = '$fechaActual' GROUP BY Seguridad");
    $totalUsuarios = mysql_num_rows($consulta); //total Usuarios que han registrado casos por fecha

    $queryTotalCasos = mysql_query("select COUNT(id) from equipos_000006 WHERE Fecini = '$fechaActual'");
    $datoTotalCasos = mysql_fetch_array($queryTotalCasos);
    $totalCasos = $datoTotalCasos[0];  //total Casos por dia

    $queryCasos = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Seguridad = '$participante' AND Fecini = '$fechaActual'");
    $datoCasos = mysql_fetch_array($queryCasos);
    $totalXusuario = $datoCasos[0];  //total casos por dia por usuario

    switch($parametro)
    {
        case 1: //porcentaje de casos
            $porcentaje = ($totalXusuario/$totalCasos)*100;
            $porcentaje = round($porcentaje,2);
            echo $porcentaje.' '.'%';
            break;
        case 2: //total minutos casos por usuario
            $queryMinutos = mysql_query("select sum(Horini) from equipos_000006 WHERE Seguridad = '$participante' AND Fecini = '$fechaActual'");
            $datoMinutos = mysql_fetch_array($queryMinutos);
            $totalTiempo = $datoMinutos[0];
            echo $totalTiempo.' '.'minutos';
            break;
    }
}

//operaciones para requerimientos Matrix:
function operaciones2($participante,$fechaActual,$parametro)
{
    $queryTotalCasos = mysql_query("select COUNT(id) from root_000040 where Reqfen = '$fechaActual' and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
    $datoTotalCasos = mysql_fetch_array($queryTotalCasos);
    $totalCasos = $datoTotalCasos[0];  //total Casos Matrix resueltos por dia

    $queryCasos = mysql_query("SELECT COUNT(id) from root_000040 WHERE Reqpurs = '$participante' AND Reqfen = '$fechaActual' and Reqtip = '02' and Reqest = '05'");
    $datoCasos = mysql_fetch_array($queryCasos);
    $totalXusuario = $datoCasos[0];  //total casos Matrix resueltos por dia por usuario

    switch($parametro)
    {
        case 1: //porcentaje de casos
            $porcentaje = ($totalXusuario/$totalCasos)*100;
            $porcentaje = round($porcentaje,2);
            echo $porcentaje.' '.'%';
            break;
        case 2: //total minutos casos por usuario
            $queryMinutos = mysql_query("select sum(Horini) from equipos_000006 WHERE Seguridad = '$participante' AND Fecini = '$fechaActual'");
            $datoMinutos = mysql_fetch_array($queryMinutos);
            $totalTiempo = $datoMinutos[0];
            echo $totalTiempo.' '.'minutos';
            break;
    }
}

function consultarDatosPeriodo($mesActual,$a�oActual)
{
    function enero($a�oActual)
    {
        $a�oActual = trim($a�oActual);
        $fechaInicial = $a�oActual.'-01-01';
        $fechaFinal = $a�oActual.'-01-31';

        $queryCountXmes1 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes1 = mysql_fetch_array($queryCountXmes1);
        $casosEnero = $datoCountXmes1[0];
        if($casosEnero == ''){$casosEnero = '0';}

        $queryCountXmes1Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes1Matrix = mysql_fetch_array($queryCountXmes1Matrix);
        $casosEneroMatrix = $datoCountXmes1Matrix[0];
        if($casosEneroMatrix == ''){$casosEneroMatrix = '0';}

        $totalCasos = $casosEnero + $casosEneroMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosEnero?>  Matrix: <?php echo $casosEneroMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function febrero($a�oActual)
    {
        $fechaInicial = $a�oActual.'-02-01';
        $fechaFinal = $a�oActual.'-02-28';

        $queryCountXmes2 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes2 = mysql_fetch_array($queryCountXmes2);
        $casosFebrero = $datoCountXmes2[0];
        if($casosFebrero == ''){$casosFebrero = '0';}

        $queryCountXmes2Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes2Matrix = mysql_fetch_array($queryCountXmes2Matrix);
        $casosFebreroMatrix = $datoCountXmes2Matrix[0];
        if($casosFebreroMatrix == ''){$casosFebreroMatrix = '0';}

        $totalCasos = $casosFebrero + $casosFebreroMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosFebrero?>  Matrix: <?php echo $casosFebreroMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function marzo($a�oActual)
    {
        $fechaInicial = $a�oActual.'-03-01';
        $fechaFinal = $a�oActual.'-03-31';

        $queryCountXmes3 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes3 = mysql_fetch_array($queryCountXmes3);
        $casosMarzo = $datoCountXmes3[0];
        if($casosMarzo == ''){$casosMarzo = '0';}

        $queryCountXmes3Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes3Matrix = mysql_fetch_array($queryCountXmes3Matrix);
        $casosMarzoMatrix = $datoCountXmes3Matrix[0];
        if($casosMarzoMatrix == ''){$casosMarzoMatrix = '0';}

        $totalCasos = $casosMarzo + $casosMarzoMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosMarzo?>  Matrix: <?php echo $casosMarzoMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function abril($a�oActual)
    {
        $fechaInicial = $a�oActual.'-04-01';
        $fechaFinal = $a�oActual.'-04-30';

        $queryCountXmes4 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes4 = mysql_fetch_array($queryCountXmes4);
        $casosAbril = $datoCountXmes4[0];
        if($casosAbril == ''){$casosAbril = '0';}

        $queryCountXmes4Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes4Matrix = mysql_fetch_array($queryCountXmes4Matrix);
        $casosAbrilMatrix = $datoCountXmes4Matrix[0];
        if($casosAbrilMatrix == ''){$casosAbrilMatrix = '0';}

        $totalCasos = $casosAbril + $casosAbrilMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosAbril?>  Matrix: <?php echo $casosAbrilMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function mayo($a�oActual)
    {
        $fechaInicial = $a�oActual.'-05-01';
        $fechaFinal = $a�oActual.'-05-31';

        $queryCountXmes5 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes5 = mysql_fetch_array($queryCountXmes5);
        $casosMayo = $datoCountXmes5[0];
        if($casosMayo == ''){$casosMayo = '0';}

        $queryCountXmes5Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes5Matrix = mysql_fetch_array($queryCountXmes5Matrix);
        $casosMayoMatrix = $datoCountXmes5Matrix[0];
        if($casosMayoMatrix == ''){$casosMayoMatrix = '0';}

        $totalCasos = $casosMayo + $casosMayoMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosMayo?>  Matrix: <?php echo $casosMayoMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function junio($a�oActual)
    {
        $fechaInicial = $a�oActual.'-06-01';
        $fechaFinal = $a�oActual.'-06-30';

        $queryCountXmes6 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes6 = mysql_fetch_array($queryCountXmes6);
        $casosJunio = $datoCountXmes6[0];
        if($casosJunio == ''){$casosJunio = '0';}

        $queryCountXmes6Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes6Matrix = mysql_fetch_array($queryCountXmes6Matrix);
        $casosJunioMatrix = $datoCountXmes6Matrix[0];
        if($casosJunioMatrix == ''){$casosJunioMatrix = '0';}

        $totalCasos = $casosJunio + $casosJunioMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosJunio?>  Matrix: <?php echo $casosJunioMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function julio($a�oActual)
    {
        $fechaInicial = $a�oActual.'-07-01';
        $fechaFinal = $a�oActual.'-07-31';

        $queryCountXmes7 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes7 = mysql_fetch_array($queryCountXmes7);
        $casosJulio = $datoCountXmes7[0];
        if($casosJulio == ''){$casosJulio = '0';}

        $queryCountXmes7Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes7Matrix = mysql_fetch_array($queryCountXmes7Matrix);
        $casosJulioMatrix = $datoCountXmes7Matrix[0];
        if($casosJulioMatrix == ''){$casosJulioMatrix = '0';}

        $totalCasos = $casosJulio + $casosJulioMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosJulio?>  Matrix: <?php echo $casosJulioMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function agosto($a�oActual)
    {
        $fechaInicial = $a�oActual.'-08-01';
        $fechaFinal = $a�oActual.'-08-31';

        $queryCountXmes8 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes8 = mysql_fetch_array($queryCountXmes8);
        $casosAgosto = $datoCountXmes8[0];
        if($casosAgosto == ''){$casosAgosto = '0';}

        $queryCountXmes8Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes8Matrix = mysql_fetch_array($queryCountXmes8Matrix);
        $casosAgostoMatrix = $datoCountXmes8Matrix[0];
        if($casosAgostoMatrix == ''){$casosAgostoMatrix = '0';}

        $totalCasos = $casosAgosto + $casosAgostoMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosAgosto?>  Matrix: <?php echo $casosAgostoMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function septiembre($a�oActual)
    {
        $fechaInicial = $a�oActual.'-09-01';
        $fechaFinal = $a�oActual.'-09-30';

        $queryCountXmes9 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes9 = mysql_fetch_array($queryCountXmes9);
        $casosSeptiembre = $datoCountXmes9[0];
        if($casosSeptiembre == ''){$casosSeptiembre = '0';}

        $queryCountXmes9Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes9Matrix = mysql_fetch_array($queryCountXmes9Matrix);
        $casosSeptiembreMatrix = $datoCountXmes9Matrix[0];
        if($casosSeptiembreMatrix == ''){$casosSeptiembreMatrix = '0';}

        $totalCasos = $casosSeptiembre + $casosSeptiembreMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosSeptiembre?>  Matrix: <?php echo $casosSeptiembreMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function octubre($a�oActual)
    {
        $fechaInicial = $a�oActual.'-10-01';
        $fechaFinal = $a�oActual.'-10-31';

        $queryCountXmes10 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes10 = mysql_fetch_array($queryCountXmes10);
        $casosOctubre = $datoCountXmes10[0];
        if($casosOctubre == ''){$casosOctubre = '0';}

        $queryCountXmes10Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes10Matrix = mysql_fetch_array($queryCountXmes10Matrix);
        $casosOctubreMatrix = $datoCountXmes10Matrix[0];
        if($casosOctubreMatrix == ''){$casosOctubreMatrix = '0';}

        $totalCasos = $casosOctubre + $casosOctubreMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosOctubre?>  Matrix: <?php echo $casosOctubreMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function noviembre($a�oActual)
    {
        $fechaInicial = $a�oActual.'-11-01';
        $fechaFinal = $a�oActual.'-11-30';

        $queryCountXmes11 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes11 = mysql_fetch_array($queryCountXmes11);
        $casosNoviembre = $datoCountXmes11[0];
        if($casosNoviembre == ''){$casosNoviembre = '0';}

        $queryCountXmes11Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes11Matrix = mysql_fetch_array($queryCountXmes11Matrix);
        $casosNoviembreMatrix = $datoCountXmes11Matrix[0];
        if($casosNoviembreMatrix == ''){$casosNoviembreMatrix = '0';}

        $totalCasos = $casosNoviembre + $casosNoviembreMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosNoviembre?>  Matrix: <?php echo $casosNoviembreMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    function diciembre($a�oActual)
    {
        $fechaInicial = $a�oActual.'-12-01';
        $fechaFinal = $a�oActual.'-12-31';

        $queryCountXmes12 = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
        $datoCountXmes12 = mysql_fetch_array($queryCountXmes12);
        $casosDiciembre = $datoCountXmes12[0];
        if($casosDiciembre == ''){$casosDiciembre = '0';}

        $queryCountXmes12Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
        $datoCountXmes12Matrix = mysql_fetch_array($queryCountXmes12Matrix);
        $casosDiciembreMatrix = $datoCountXmes12Matrix[0];
        if($casosDiciembreMatrix == ''){$casosDiciembreMatrix = '0';}

        $totalCasos = $casosDiciembre + $casosDiciembreMatrix;
        ?>
        <label title="Llamadas: <?php echo $casosDiciembre?>  Matrix: <?php echo $casosDiciembreMatrix?>"><?php echo $totalCasos ?></label>
        <?php
    }

    ?>
    <tr class="alternar">
        <td><label>ENERO</label></td><td align="center"><?php enero($a�oActual)?></td>
        <td>&ensp;&ensp;&ensp;</td>
        <td><label>JULIO</label></td><td align="center"><?php julio($a�oActual) ?></td>
    </tr>
    <tr class="alternar">
        <td><label>FEBRERO</label></td><td align="center"><?php febrero($a�oActual) ?></td>
        <td>&ensp;&ensp;&ensp;</td>
        <td><label>AGOSTO</label></td><td align="center"><?php agosto($a�oActual) ?></td>
    </tr>
    <tr class="alternar">
        <td><label>MARZO</label></td><td align="center"><?php marzo($a�oActual) ?></td>
        <td>&ensp;&ensp;&ensp;</td>
        <td><label>SEPTIEMBRE</label></td><td align="center"><?php septiembre($a�oActual) ?></td>
    </tr>
    <tr class="alternar">
        <td><label>ABRIL</label></td><td align="center"><?php abril($a�oActual) ?></td>
        <td>&ensp;&ensp;&ensp;</td>
        <td><label>OCTUBRE</label></td><td align="center"><?php octubre($a�oActual) ?></td>
    </tr>
    <tr class="alternar">
        <td><label>MAYO</label></td><td align="center"><?php mayo($a�oActual) ?></td>
        <td>&ensp;&ensp;&ensp;</td>
        <td><label>NOVIEMBRE</label></td><td align="center"><?php noviembre($a�oActual) ?></td>
    </tr>
    <tr class="alternar">
        <td><label>JUNIO</label></td><td align="center"><?php junio($a�oActual) ?></td>
        <td>&ensp;&ensp;&ensp;</td>
        <td><label>DICIEMBRE</label></td><td align="center"><?php diciembre($a�oActual) ?></td>
    </tr>
    <?php
}

function consultarDatosCausas($mesActual,$a�oActual)
{
    $a�oActual = trim($a�oActual);
    switch($mesActual)
    {
        case $mesActual == '01' or $mesActual == '03' or $mesActual == '05' or $mesActual == '07' or $mesActual == '08' or $mesActual == '10' or $mesActual == '12':
            $diaFin = '30';
            break;
        case $mesActual == '02':
            $diaFin = '28';
            break;
        case $mesActual == '04' or $mesActual == '06' or $mesActual == '09' or $mesActual == '11':
            $diaFin = '30';
            break;
    }
    $fechaInicial = $a�oActual.'-'.$mesActual.'-01';
    $fechaFinal = $a�oActual.'-'.$mesActual.'-'.$diaFin;

    $a�oInicial = $a�oActual.'-01-01';
    $a�oFinal = $a�oActual.'-12-31';

    $queryCountCausaxMes = mysql_query("SELECT Causareq, Count(Causareq)
                                        FROM equipos_000006
                                        WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'
                                        GROUP BY Causareq HAVING Count(Causareq)
                                        ORDER BY Count(Causareq)DESC");

    while($datoCountCausaxMes = mysql_fetch_array($queryCountCausaxMes))
    {
        $descCausa1 = $datoCountCausaxMes[0];    $contCausaMes = $datoCountCausaxMes[1];

        $queryDescCausa = mysql_query("SELECT descripcion FROM equipos_000007 WHERE id_causa = '$descCausa1'");
        $datoDescCausa = mysql_fetch_array($queryDescCausa);
        $descCausa = $datoDescCausa[0];

        $queryTotalA = mysql_query("SELECT COUNT(id) FROM equipos_000006 WHERE Causareq = '$descCausa1' AND Fecini BETWEEN '$a�oInicial' AND '$a�oFinal'");
        $datoTotalA = mysql_fetch_array($queryTotalA);
        $contCausaA�o = $datoTotalA[0];
        ?>
        <tr class="alternar">
            <td style="font-weight: bold" width="240"><?php echo $descCausa ?></td>
            <td align="center"><?php echo $contCausaMes?></td>
            <td align="right"><?php consultarPromCausa($a�oActual,$contCausaMes,$contCausaA�o) ?>
                &ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;</td>
            <td align="center"><?php echo $contCausaA�o ?></td>
            <td align="center">&ensp;<?php consultarPromCausaA�o($a�oActual,$contCausaMes,$contCausaA�o) ?></td>
            <td>&ensp;&ensp;&ensp;&ensp;</td>
        </tr>
        <?php
    }
}

function consultarDatosA�o($a�oActual)
{
    $fechaInicial = $a�oActual.'-01-01';
    $fechaFinal = $a�oActual.'-12-31';

    $queryA�o = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
    $datoA�o = mysql_fetch_array($queryA�o);
    $casosA�o = $datoA�o[0];

    $queryCountXmes12Matrix = mysql_query("select COUNT(id) from root_000040 where Reqfen BETWEEN '$fechaInicial' AND '$fechaFinal'
                                              and Reqcco = '(01)1710' and Reqtip = '02' and Reqest = '05'");
    $datoCountXmes12Matrix = mysql_fetch_array($queryCountXmes12Matrix);
    $casosDiciembreMatrix = $datoCountXmes12Matrix[0];
    if($casosDiciembreMatrix == ''){$casosDiciembreMatrix = '0';}

    $totalCasos = $casosA�o + $casosDiciembreMatrix;
    ?>
    <tr><td>&ensp;</td></tr>
    <tr style="background-color: #C3D9FF">
        <td><h5>TOTAL <?echo $a�oActual ?>&nbsp;:</h5></td>
        <td align="center" title="Llamadas: <?php echo $casosA�o?>  Matrix: <?php echo $casosDiciembreMatrix?>"><?php echo $totalCasos ?></td>
    </tr>
    <?php
}

function consultarPromCausa($a�oActual,$contCausaMes,$contCausaA�o)
{
    $fechaInicial = $a�oActual.'-01-01';
    $fechaFinal = $a�oActual.'-12-31';

    $queryA�o = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
    $datoA�o = mysql_fetch_array($queryA�o);
    $casosA�o = $datoA�o[0];

    $porcentaje = ($contCausaMes/$casosA�o)*100;
    $porcentaje = round($porcentaje,2);
    echo $porcentaje.' '.'%';
}

function consultarPromCausaA�o($a�oActual,$contCausaMes,$contCausaA�o)
{
    $fechaInicial = $a�oActual.'-01-01';
    $fechaFinal = $a�oActual.'-12-31';

    $queryA�o = mysql_query("SELECT COUNT(id) from equipos_000006 WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'");
    $datoA�o = mysql_fetch_array($queryA�o);
    $casosA�o = $datoA�o[0];

    $porcentaje = ($contCausaA�o/$casosA�o)*100;
    $porcentaje = round($porcentaje,2);
    echo $porcentaje.' '.'%';
}

function consultarGrafico1($a�oActual)
{
    $fechaInicial = $a�oActual.'-01-01';    $fechaFinal = $a�oActual.'-12-31';

    $queryAreas = mysql_query("SELECT Ccoreq,Count(id)
                               FROM equipos_000006
                               WHERE Fecini BETWEEN '$fechaInicial' AND '$fechaFinal'
                               GROUP BY Ccoreq HAVING COUNT(id)
                               ORDER BY COUNT(id) ASC");
    ?>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Areas', 'Llamadas por a�o'],
                <?php
                while($datoArea = mysql_fetch_array($queryAreas))
                {
                    $IdArea = $datoArea[0];   $contArea = $datoArea[1];

                    $queryCco = mysql_query("SELECT Cconom FROM costosyp_000005 WHERE Ccocod = '$IdArea'");
                    $datoDescCco = mysql_fetch_array($queryCco);
                    $descCco = $datoDescCco[0];

                    echo "['".$descCco."', ".$contArea."],";
                }
                ?>
            ]);

            var options = {
                /*title: 'Areas',*/
                is3D: true,
                width: 800,
                height: 400,
                chartArea:{left:-70,top:10,right:100,width:"80%",height:"80%"},
                legend: { display: "none" }
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
        }
    </script>
    <div id="piechart_3d"></div>
    <?php
}
?>
</body>
</html>
