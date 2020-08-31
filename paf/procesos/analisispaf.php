<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Analisis - PAF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="botonespaf.css" rel="stylesheet" type="text/css">
    <link href="estilospaf.css" rel="stylesheet" type="text/css">
    <script src="JsProcesospaf.js"></script>
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
    ?>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="calendariopaf.js" type="text/javascript"></script>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
        });
    </script>
</head>

<body onload="totalPac('npn','nps','npst','totalPac'),          totalFem('sfn','sfs','sfst','totalFem'),                totalMas('smn','sms','smst','totalMas'),
              totalEdad('en','es','est','totalEdad'),           edadMayor('emn','ems','emst','edadMayor'),              edadMenor('eminn','emins','eminst','edadMenor'),
              totaldiasest('depn','deps','depst','diasEst'),    totalPromEst('diasEst','totalPac','tpromEstancia'),     totalEstuci('sumEstn','sumEsts','sumEstst','tsumEst'),
              tMeu('mayEsun','mayEsus','mayEsust','tmayEst'),   tMreu('menEsun','menEss','menEsst','tmenEst'),          totalDep('dEpn','dEps','dEpst','tdEp'),
              totalPro('nPron','nPros','nProst','tPro'),        totalPpro('tPro','totalPac','tPpro'),                   totalRei('nRein','nReis','nReist','tRei'),
              totalpAmb('pAmbn','pAmbs','pAmbst','tpAmb'),      totalNopro('noPron','noPros','noProst','tnoPro'),       totalP1cx('p1cxn','p1cxs','p1cxst','tp1cx'),
              totalP2cx('p2cxn','p2cxs','p2cxst','tp2cx'),      totalP3cx('p3cxn','p3cxs','p3cxst','tp3cx'),            totalcxhem('cxhemn','cxhems','cxhemst','tcxhem'),
              totalcxef('cxefn','cxefs','cxefst','tcxef'),      tcxheef('cxhemefn','cxhemefs','cxhemefst','tcxhemef'),  totalesUci('tsumEst','tpAmb','tesUci'),
              totesUcinop('tsumEst','tnoPro','tesUciNop'),      totalEsUciPaf('tsumEst','totalPac','toEsuci'),          totalEspre('tPro','tRei','toEspre'),
              totalEstHos('detn','dets','detst','diasEstT')">
<div id="loginbox" style="margin-top: 5px">
    <div class="panel panel-info" >
        <div class="panel-heading">
            <div class="panel-title">ANALISIS CX PAF</div>
        </div>

        <div style="padding-top:30px" class="panel-body">
            <form method="post" action="analisispaf.php">
                <table align="center">
                    <tr>
                        <td colspan="3" align="center">
                            <h5 class="text-primary"><strong>Periodo de analisis: </strong></h5>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon"><label>Fecha Inicial</label></span>
                                <input id="datepicker1" type="text" class="form-control" style="width: 150px" name="fechai" value="">
                            </div>
                        </td>
                        <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon"><label>Fecha Final</label></span>
                                <input id="datepicker2" type="text" class="form-control" style="width: 150px" name="fechaf" value="">
                            </div>
                        </td>
                        <td>
                            <div class="col-sm-12 controls">
                                <input type="submit" class="btn btn-info btn-sm" id="bntIr" name="btnIr" value="> > >">
                            </div>
                        </td>
                        <td><div class="input-group-addon" style="background-color: #ffffff; width: 20px; border: none"></div></td>
                    </tr>
                </table>
            </form>
            <br><br>
            <?php
            if(isset($_POST['btnIr']))
            {
                $fechai=$_POST['fechai'];
                $fechaf=$_POST['fechaf'];
                $respNueva='900156264CV';
                $respSura='800088702CV';
                $respStotal='800130907CV';
                $sexo1='F';
                $sexo2='M';
                ?>
                <h6 class="text-primary" align="center"><strong><?php echo 'FECHA INICIAL= '.$fechai.' --- FECHA FINAL= '.$fechaf;?></strong></h6>
                <form id="fservicio" method="post">
                    <table border="1" class="table" style="width: 1000px" align="center">
                        <thead>
                        <tr style="background-color: #afd9ee">
                            <th style="width: 20px"><label>&nbsp;</label></th>
                            <th style="width: 20px"><label>NUEVA EPS PAF</label></th>
                            <th style="width: 20px"><label>SURA PAF</label></th>
                            <th style="width: 20px"><label>SALUD TOTAL PAF</label></th>
                            <th style="width: 20px"><label>TOTAL</label></th>
                        </tr>
                        <tr style="min-height: 10px">
                            <th colspan="5"><center><label style="font-size: small">CARACTERIZACION DE LA POBLACION</label></center></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>N. Pacientes</td>
                            <td align="center"><input type="text" class="input" id="npn" value="<?php contarPacientes($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="nps" value="<?php contarPacientes($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="npst" value="<?php contarPacientes($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="totalPac"></td>
                        </tr>
                        <tr>
                            <td>Sexo Femenino</td>
                            <td align="center"><input type="text" class="input" id="sfn" value="<?php contarSexo($fechai,$fechaf,$respNueva,$sexo1) ?>"></td>
                            <td align="center"><input type="text" class="input" id="sfs" value="<?php contarSexo($fechai,$fechaf,$respSura,$sexo1) ?>"></td>
                            <td align="center"><input type="text" class="input" id="sfst" value="<?php contarSexo($fechai,$fechaf,$respStotal,$sexo1) ?>"></td>
                            <td align="center"><input type="text" class="input" id="totalFem"></td>
                        </tr>
                        <tr>
                            <td>Sexo Masculino</td>
                            <td align="center"><input type="text" class="input" id="smn" value="<?php contarSexo($fechai,$fechaf,$respNueva,$sexo2) ?>"></td>
                            <td align="center"><input type="text" class="input" id="sms" value="<?php contarSexo($fechai,$fechaf,$respSura,$sexo2) ?>"></a></td>
                            <td align="center"><input type="text" class="input" id="smst" value="<?php contarSexo($fechai,$fechaf,$respStotal,$sexo2) ?>"></td>
                            <td align="center"><input type="text" class="input" id="totalMas"></td>
                        </tr>
                        <tr>
                            <td>Edad Promedio</td>
                            <td align="center"><input type="text" class="input" id="en" value="<?php promedioEdad($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="es" value="<?php promedioEdad($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="est" value="<?php promedioEdad($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="totalEdad"></td>
                        </tr>
                        <tr>
                            <td>Edad Mayor</td>
                            <td align="center"><input type="text" class="input" id="emn" value="<?php maxEdad($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="ems" value="<?php maxEdad($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="emst" value="<?php maxEdad($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="edadMayor"></td>
                        </tr>
                        <tr>
                            <td>Edad Menor</td>
                            <td align="center"><input type="text" class="input" id="eminn" value="<?php minEdad($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="emins" value="<?php minEdad($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="eminst" value="<?php minEdad($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="edadMenor"></td>
                        </tr>
                        <tr style="min-height: 10px">
                            <th colspan="5"><center><label style="font-size: small">ANALISIS DE ESTANCIA</label></center></th>
                        </tr>
                        <tr>
                            <td>Estancia Hospitalaria Total</td>
                            <td align="center"><input type="text" class="input" id="detn" value="<?php sumEstanciaTotal($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="dets" value="<?php sumEstanciaTotal($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="detst" value="<?php sumEstanciaTotal($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="diasEstT"></td>
                        </tr>
                        <tr>
                            <td>Dias Estancia PAF</td>
                            <td align="center"><input type="text" class="input" id="depn" value="<?php sumEstanciaPaf($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="deps" value="<?php sumEstanciaPaf($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="depst" value="<?php sumEstanciaPaf($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="diasEst"></td>
                        </tr>
                        <tr>
                            <td>Promedio Estancia PAF</td>
                            <td align="center"><input type="text" class="input" id="pepn" value="<?php promEstanciaPaf($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="peps" value="<?php promEstanciaPaf($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="pepst" value="<?php promEstanciaPaf($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tpromEstancia"></td>
                        </tr>
                        <tr>
                            <td>Estancia UCI PAF</td>
                            <td align="center"><input type="text" class="input" id="sumEstn" value="<?php sumEstanciaUciPaf($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="sumEsts" value="<?php sumEstanciaUciPaf($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="sumEstst" value="<?php sumEstanciaUciPaf($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tsumEst"></td>
                        </tr>
                        <tr>
                            <td>Mayor Estancia UCI PAF</td>
                            <td align="center"><input type="text" class="input" id="mayEsun" value="<?php mayEstanciaUciPaf($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="mayEsus" value="<?php mayEstanciaUciPaf($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="mayEsust" value="<?php mayEstanciaUciPaf($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tmayEst"></td>
                        </tr>
                        <tr>
                            <td>Menor Estancia UCI PAF</td>
                            <td align="center"><input type="text" class="input" id="menEsun" value="<?php minEstanciaUciPaf($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="menEss" value="<?php minEstanciaUciPaf($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="menEsst" value="<?php minEstanciaUciPaf($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tmenEst"></td>
                        </tr>
                        <tr>
                            <td>Dias Evitados al PAF</td>
                            <td align="center"><input type="text" class="input" id="dEpn" value="<?php diasEvitadosPAF2($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="dEps" value="<?php diasEvitadosPAF2($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="dEpst" value="<?php diasEvitadosPAF2($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tdEp"></td>
                        </tr>
                        <tr style="min-height: 10px">
                            <th colspan="5"><center><label style="font-size: small">PROCEDIMIENTOS PROGRAMADOS</label></center></th>
                        </tr>
                        <tr>
                            <td>N. Procedimientos</td>
                            <td align="center"><input type="text" class="input" id="nPron" value="<?php nprocedimientos2($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="nPros" value="<?php nprocedimientos2($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="nProst" value="<?php nprocedimientos2($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tPro"></td>
                        </tr>
                        <tr>
                            <td>Prom Procedimientos</td>
                            <td align="center"><input type="text" class="input" id="pPron" value="<?php promProcedimientos($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="pPros" value="<?php promProcedimientos($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="pProst" value="<?php promProcedimientos($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tPpro"></td>
                        </tr>
                        <tr>
                            <td>N. Reintervenciones</td>
                            <td align="center"><input type="text" class="input" id="nRein" value="<?php nReinter($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="nReis" value="<?php nReinter($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="nReist" value="<?php nReinter($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tRei"></td>
                        </tr>
                        <tr>
                            <td>Programados ambulatorios</td>
                            <td align="center"><input type="text" class="input" id="pAmbn" value="<?php programadoSi($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="pAmbs" value="<?php programadoSi($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="pAmbst" value="<?php programadoSi($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tpAmb"></td>
                        </tr>
                        <tr>
                            <td>No Programados</td>
                            <td align="center"><input type="text" class="input" id="noPron" value="<?php programadoNo($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="noPros" value="<?php programadoNo($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="noProst" value="<?php programadoNo($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tnoPro"></td>
                        </tr>
                        <tr style="min-height: 10px">
                            <th colspan="5"><center><label style="font-size: small">PROCEDIMIENTOS POR PACIENTES</label></center></th>
                        </tr>
                        <tr>
                            <td>Pacientes Una Cx</td>
                            <td align="center"><input type="text" class="input" id="p1cxn" value="<?php contUnaCx($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="p1cxs" value="<?php contUnaCx($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="p1cxst" value="<?php contUnaCx($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tp1cx"></td>
                        </tr>
                        <tr>
                            <td>Pacientes Dos Cx</td>
                            <td align="center"><input type="text" class="input" id="p2cxn" value="<?php contDosCx($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="p2cxs" value="<?php contDosCx($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="p2cxst" value="<?php contDosCx($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tp2cx"></td>
                        </tr>
                        <tr>
                            <td>Pacientes Tres Cx</td>
                            <td align="center"><input type="text" class="input" id="p3cxn" value="<?php contTresCx($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="p3cxs" value="<?php contTresCx($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="p3cxst" value="<?php contTresCx($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tp3cx"></td>
                        </tr>
                        <tr>
                            <td>Cx + Hem</td>
                            <td align="center"><input type="text" class="input" id="cxhemn" value="<?php contCxHem($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="cxhems" value="<?php contCxHem($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="cxhemst" value="<?php contCxHem($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tcxhem"></td>
                        </tr>
                        <tr>
                            <td>Cx + EEFF</td>
                            <td align="center"><input type="text" class="input" id="cxefn" value="<?php contCxEf($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="cxefs" value="<?php contCxEf($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="cxefst" value="<?php contCxEf($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tcxef"></td>
                        </tr>
                        <tr>
                            <td>Cx + Hem + EEFF</td>
                            <td align="center"><input type="text" class="input" id="cxhemefn" value="<?php contCxHemEf($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="cxhemefs" value="<?php contCxHemEf($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="cxhemefst" value="<?php contCxHemEf($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tcxhemef"></td>
                        </tr>
                        <tr style="min-height: 10px">
                            <th colspan="5"><center><label style="font-size: small">ESTANCIA UCI PROMEDIO - DIAS</label></center></th>
                        </tr>
                        <tr>
                            <td>Programado</td>
                            <td align="center"><input type="text" class="input" id="esUcin" value="<?php sumEstanciaUciPafProg($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="esUcis" value="<?php sumEstanciaUciPafProg($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="esUcist" value="<?php sumEstanciaUciPafProg($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tesUci"></td>
                        </tr>
                        <tr>
                            <td>No Programado</td>
                            <td align="center"><input type="text" class="input" id="esUciNopn" value="<?php sumEstanciaUciPafNoProg($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="esUciNops" value="<?php sumEstanciaUciPafNoProg($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="esUciNopst" value="<?php sumEstanciaUciPafNoProg($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="tesUciNop"></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td align="center"><input type="text" class="input" id="toEsucin" value="<?php totalProgyNoprog($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toEsucis" value="<?php totalProgyNoprog($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toEsucist" value="<?php totalProgyNoprog($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toEsuci"></td>
                        </tr>
                        <tr style="min-height: 10px">
                            <th colspan="5"><center><label style="font-size: small">ESTANCIA UCI PROMEDIO - DIAS (Reintervenidos)</label></center></th>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td align="center"><input type="text" class="input" id="toEspren" value="<?php totalProgyNoprogR($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toEspres" value="<?php totalProgyNoprogR($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toEsprest" value="<?php totalProgyNoprogR($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toEspre"></td>
                        </tr>
                        <tr style="min-height: 10px">
                            <th colspan="5"><center><label style="font-size: small">OPORTUNIDAD QUIRURGICA PROMEDIO - DIAS</label></center></th>
                        </tr>
                        <tr>
                            <td>Programado</td>
                            <td align="center"><input type="text" class="input" id="opQxpn" value="<?php oportunidadqxProm($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="opQxps" value="<?php oportunidadqxProm($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="opQxpst" value="<?php oportunidadqxProm($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toopQxp" value="<?php totalOportunidadPromProg($fechai,$fechaf) ?>"></td>
                        </tr>
                        <tr>
                            <td>No Programado</td>
                            <td align="center"><input type="text" class="input" id="opQxnopn" value="<?php oportunidadqxProm2($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="opQxnops" value="<?php oportunidadqxProm2($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="opQxnopst" value="<?php oportunidadqxProm2($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toopQxnop" value="<?php totalOportunidadPromnoProg($fechai,$fechaf) ?>"></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td align="center"><input type="text" class="input" id="toopQxn" value="<?php totalOportunidadProm($fechai,$fechaf,$respNueva) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toopQxs" value="<?php totalOportunidadProm($fechai,$fechaf,$respSura) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toopQxst" value="<?php totalOportunidadProm($fechai,$fechaf,$respStotal) ?>"></td>
                            <td align="center"><input type="text" class="input" id="toopQx" value="<?php totalOportunidadQx($fechai,$fechaf) ?>"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>