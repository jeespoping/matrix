<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRABACION DE FACTURAS PATOLOGIA - MATRIX</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> <!-- INDICADOR DE CARGA DE PAGINA-->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="cssFact_pat.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css"><!-- NUEVO ACORDEON-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script><!-- NUEVO ACORDEON-->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script><!-- NUEVO ACORDEON-->
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <link href="http://mx.lasamericas.com.co/matrix/paf/procesos/CssAcordeonpaf.css" rel="stylesheet">
    <script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="jsFact_pat.js"></script>
    <script type="text/javascript">
        $(window).load(function() {
            $(".loader").fadeOut("slow");
        });
    </script> <!-- INDICADOR DE CARGA DE PAGINA-->
    <script>
        function modificarVal(historia)
        {
            estadoChk = document.getElementById('codPlan'+historia).checked;
            valCargosPac = document.getElementById(historia).innerHTML;
            valCargosPac = valCargosPac.replace(/ /g, "");
            valCargosPac = valCargosPac.replace(/,/g, "");
            totalResp = document.getElementById('totalResp').value;
            totalResp = totalResp.replace(/ /g, "");
            totalResp = totalResp.replace(/,/g, "");
            valAbonosPac = document.getElementById('abo'+historia).innerHTML;
            valAbonosPac = valAbonosPac.replace(/ /g, "");
            valAbonosPac = valAbonosPac.replace(/,/g, "");

            if(estadoChk == true)
            {
                valTotalEPS1 =  (parseInt(totalResp)) - (parseInt(valCargosPac));
                valTotalEPS = (parseInt(valTotalEPS1)) - (parseInt(valAbonosPac));
                document.getElementById('totalResp').value = valTotalEPS;
                document.getElementById('valTotCar').value = valTotalEPS;
                document.getElementById('acord'+historia).style.backgroundColor = '#FEF0E6';
                //accion2 = 'restar';
            }
            if(estadoChk == false)
            {
                valTotalEPS1 =  (parseInt(totalResp)) + (parseInt(valCargosPac));
                valTotalEPS = (parseInt(valTotalEPS1)) + (parseInt(valAbonosPac));
                document.getElementById('totalResp').value = valTotalEPS;
                document.getElementById('valTotCar').value = valTotalEPS;
                document.getElementById('acord'+historia).style.backgroundColor = '#FFFFFF';
                //accion2 = 'sumar';
            }
        }
    </script>
    <style>
        .btn span.glyphicon {
            opacity: 0;
        }
        .btn.active span.glyphicon {
            opacity: 1;
        }
    </style><!-- CHECKBOXES-->
    <style>
        .loader
        {
            position: fixed;    left: 0;        top: 0;
            width: 100%;        height: 100%;   z-index: 9999;
            background: url('http://132.1.18.13/matrix/images/medical/facturacion/pageLoader.gif') 50% 50% no-repeat rgb(249,249,249);
            opacity: .8;
        }
    </style> <!-- INDICADOR DE CARGA DE PAGINA-->
    <?php
    include_once("conex.php"); //publicacion en matrix
    include_once("root/comun.php"); //publicacion en matrix
    ///*
    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente<?php echo 'USER='.$_SESSION['user'];?></label>
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
        //$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }
    //*/

    $wemp = $_GET['wemp_pmla'];
    //echo 'EMPRESA='.$wemp;
    if($wemp == null){$wemp = $_POST['wemp_pmla'];}
    switch($wemp)
    {
        case '11': $wbasedato = 'patol';break;
        case '02': $wbasedato = 'clisur';break;
    }

    $fechaActual = date('Y-m-d');   $horaActual = date('H:i:s');    $reload = $_POST['reload'];
    ?>
</head>

<body>
<div class="loader"></div> <!-- INDICADOR DE CARGA DE PAGINA-->
<div class="container general">
    <div class="panel panel-info contenido">
        <div class="panel-heading encabezado">
            <div align="center" class="panel-title"><label style="font-weight: bold">Matrix - FACTURACION LABORATORIO DE PATOLOGIA</label></div>
        </div>
        <div id="allPanels" class="input-group divGeneralPaneles text-center" style="text-align: center; display: block; min-height: 140px; max-height: 130px">
            <div id="divDefault" class="input-group divGeneralAcordion text-center" style="text-align: center; display: block; margin-top: 10px">

                <!-- PANEL SELECCION DE PARAMETROS: -->
                <form name="frmParametros" method="post" action="Facturar_pat.php">
                    <table class="tblParametros" style="width: 100%" border="0">
                        <tr>
                            <td style="width: 25%"></td>
                            <td align="right">
                                <div class="input-group rangoFechas" align="center">
                                    <span class="input-group-addon input-sm"><label for="datepicker1">Fecha Inicial</label></span>
                                    <input type="text" id="datepicker1" name="fechaInicial" class="form-control form-sm" style="width: 150px">
                                </div>
                            </td>
                            <td align="left">
                                <div class="input-group rangoFechas" align="center">
                                    <span class="input-group-addon input-sm"><label for="datepicker2">Fecha Final</label></span>
                                    <input type="text" id="datepicker2" name="fechaFinal" class="form-control form-sm" style="width: 150px">
                                </div>
                            </td>
                            <td style="width: 25%">
                                <div style="float: right; border: none; margin-right: 100px; margin-top: -70px; height: 50px">
                                    <input type="submit" class="btn btn-info btn-sm" style="margin-top: 52px; margin-right: 100px; width: 120px; height: 35px" id="bntBus" name="btnBus" value="> > >" title="Generar">
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table class="tblParametros" style="margin-top: 5px; width: 100%" border="0">
                        <tr align="left">
                            <td style="width: 25%"></td>
                            <td>
                                <table style="margin-top: 5px">
                                    <tr>
                                        <td>
                                            <label for="country_id">RESPONSABLE: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" id="country_id" name="responsable" class="form-control input-sm spamMedGral" style="width: 650px" onkeyup="autocomplet()">
                                            <ul id="country_list_id" style="cursor: pointer; position: fixed; top: 160px; background-color: #FFFFFF"></ul>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="wemp_pmla" value="<?php echo $wemp ?>">
                </form>

                <!-- PANEL PARAMETROS SELECCIONADOS: -->
                <?php
                if (isset($_POST["responsable"]) or $reload == 1)
                {
                    if($fechaInicial == null and $fechaFinal == null and $responsable == null)
                    {
                        $fechaInicial = $_POST['fechaI'];   $fechaFinal = $_POST['fechaF']; $responsable = $_POST['nameResponse'];
                    }

                    if(strlen($responsable) > 35)
                    {
                        $descResponsable = substr($responsable,0,35).'...';
                    }
                    else
                    {
                        $descResponsable = $responsable;
                    }
                    ?>
                    <div align="center" style="margin-top: 15px">
                        &ensp;&ensp;
                        <label style="font-weight: bolder; font-size: medium">Fecha Inicial:</label>&ensp;
                        <label style="color: #428BCA; font-size: large"><?php echo $fechaInicial ?></label>
                        &ensp;&ensp;
                        <label style="font-weight: bolder; font-size: medium">Fecha Final:</label>&ensp;
                        <label style="color: #428BCA; font-size: large"><?php echo $fechaFinal ?></label>
                        &ensp;&ensp;
                        <label style="font-weight: bolder; font-size: medium">Empresa:</label>&ensp;
                        <label style="color: #428BCA; font-size: large"><?php echo $descResponsable ?></label>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div id="rep1" style="margin-top: 10px; position: static">
                <!-- MANTENER VISIBLES PARAMETROS DE BUSQUEDA: -->
                <section>
                <?php
                $fechaInicial = $_POST['fechaInicial']; $fechaFinal = $_POST['fechaFinal']; $responsable = $_POST['responsable'];
                if($fechaInicial == null and $fechaFinal == null and $responsable == null)
                {
                    $fechaInicial = $_POST['fechaI'];   $fechaFinal = $_POST['fechaF']; $responsable = $_POST['nameResponse'];
                }
                list($codResponsable, $nomResponsable) = explode("-", $responsable);
                $codResponsable = trim($codResponsable);
                $nomResponsable2 = trim($nomResponsable);
                $codResponsable2 = $codResponsable.'-'.$nomResponsable2;
                ?>

                    <input type="hidden" id="fecInicialDown" name="fecInicialDown" value="<?php echo $fechaInicial ?>">
                    <input type="hidden" id="fecFinalDown" name="fecFinalDown" value="<?php echo $fechaFinal ?>">
                    <input type="hidden" id="responsableDown" name="responsableDown" value="<?php echo $responsable ?>">
                    <script>checkEntradas('fecInicialDown','fecFinalDown','responsableDown')</script>
                </section>

                <?php
                if($fechaInicial != null and $fechaFinal != null and $responsable != null)
                {
                    //VERIFICAR SI EXISTEN DATOS EN TABLA_000106 QUE CUMPLAN LAS CONDICIONES INGRESADAS:
                    $verificarNulos = mysql_queryV("select COUNT(id) from".' '."$wbasedato"."_000106
                                                where Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                AND Tcarres like '$codResponsable2%'
                                                AND tcarest = 'on'
                                                AND tcarcan > 0
                                                AND Tcarvun > 0
                                                AND tcarfac = 'S'
                                                AND Tcarfre = 0
                                                AND Tcarfex = 0")
                    or die(mysql_error());
                    $datoNulos = mysql_fetch_array($verificarNulos);
                    $totalDatos = $datoNulos[0];

                    if($totalDatos > 0)
                    {
                        $queryCargos = mysql_queryV("select *, COUNT(Tcarprocod) contCargos, SUM(Tcarvto) sumCargos
                                                from".' '."$wbasedato"."_000106
                                                where Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                AND Tcarres LIKE '$codResponsable2%'
                                                AND tcarest = 'on'
                                                AND tcarcan > 0
                                                AND Tcarvun > 0
                                                AND tcarfac = 'S'
                                                AND Tcarfre = 0
                                                AND Tcarfex = 0
                                                GROUP BY Tcarhis,Tcaring
                                                order by cast(tcarhis as integer) ASC");
                        ?>
                        <!-- PANEL CONTENIDO -->
                        <div class="titulo" style="margin-top: 0">
                            <label>DETALLE DE LA CUENTA</label>
                        </div>
                        <form name="frmPrincipal" id="frmPrincipal" method="post" action="Facturar_pat.php">
                            <div class="Reporte" style="margin-top: 5px">
                                <table border="0">
                                    <thead>
                                    <tr style="background-color: #2A5DB0; color: white; font-size: medium">
                                        <th style="text-align: center; width: 300px">Historia</th>
                                        <th style="text-align: left; width: 465px">Nombre Paciente&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;&ensp;</th>
                                        <th style="text-align: center; width: 125px">N. Cargos</th>
                                        <th style="text-align: center; width: 140px">Valor Total&ensp;</th>
                                        <th style="text-align: center; width: 140px">Valor Abono&ensp;</th>
                                        <th style="text-align: center; width: 110px">&ensp;&ensp;Facturar</th>
                                    </tr>
                                    </thead>
                                </table>

                                <!-- PANEL MEDIO -->
                                <div style="border-bottom: black 2px solid; max-height: 450px; overflow: auto" id="marka">
                                    <table border="0" style="width: 100%">
                                        <tbody>
                                        <?php
                                        //$valorAllCargos = 0;
                                        $suma = 0;
                                        while($datoCargos = mysql_fetch_array($queryCargos))
                                        {
                                            $hisPac = $datoCargos['Tcarhis'];           $ingPac = $datoCargos['Tcaring'];   $contCargos = $datoCargos['contCargos'];
                                            $valorLin = $datoCargos['sumCargos'];     $nomPac = $datoCargos['Tcarno1'].' '.$datoCargos['Tcarno2'].' '.$datoCargos['Tcarap1'].' '.$datoCargos['Tcarap2'];
                                            $paPaciente = $hisPac.'-'.$ingPac;


                                            $suma1 = 0;
                                            $queryValTotal = "Select sum(Tcarvto) SUMAN from".' '."$wbasedato"."_000106
                                                    where Tcarhis = '$hisPac'
                                                    AND Tcaring = '$ingPac'
                                                    AND Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                    AND Tcarres LIKE '$codResponsable2%'
                                                    AND tcarest = 'on'
                                                    AND tcarvto > 0
                                                    AND tcarfac = 'S'
                                                    AND Tcarfre = 0
                                                    AND Tcarfex = 0
                                                    AND Tcarconcod not in('9001','9003')";
                                            //      AND Tcarconcod <> '9001';
                                            //echo $queryValTotal;
                                            $commitQueryValTotal = mysql_query($queryValTotal,$conex) or die (mysql_errno()." - en el query: ".$queryValTotal." - ".mysql_error());

                                            while($datoQueryValTotal = mysql_fetch_assoc($commitQueryValTotal))
                                            {
                                                $suma1 = $datoQueryValTotal['SUMAN'];
                                                $suma = $suma + $suma1;
                                            }

                                            //echo 'SUMA ='.$suma;

                                            //CONSULTAR SI TIENE ABONOS(Tcarconcod = 9001 (SE INCLUYE EL CONCEPTO 9003))
                                            $queryAbonos = mysql_queryV("select SUM(Tcarvto) sumAbonos
                                                from".' '."$wbasedato"."_000106
                                                where Tcarhis = '$hisPac'
                                                AND Tcaring = '$ingPac'
                                                AND Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                AND Tcarres LIKE '$codResponsable2%'
                                                AND tcarest = 'on'
                                                AND tcarcan > 0
                                                AND tcarfac = 'S'
                                                AND Tcarfre = 0
                                                AND Tcarfex = 0
                                                AND Tcarconcod in('9001','9003')");
                                            //  AND Tcarconcod = '9001'");
                                            ?>
                                            <tr>
                                                <td>
                                                    <a id="acord<?php echo $paPaciente ?>" href="#<?php echo $hisPac ?>" class="btn btn-default" data-toggle="collapse" style="width: 95%">
                                                        <table border="0" style="width: 100%">
                                                            <tr>
                                                                <td style="width: 110px" align="center" title="Historia"><?php echo $paPaciente?></td>
                                                                <td style="width: 300px" align="left"><?php echo $nomPac?></td>
                                                                <td style="width: 70px" title="Numero de Cargos Grabados"><?php echo $contCargos?></td>
                                                                <td style="width: 70px" align="center" title="Valor Total Cargos">
                                                                    <label id="<?php echo $paPaciente ?>">
                                                                        <?php
                                                                        while($datoAbonos = mysql_fetch_array($queryAbonos))
                                                                        {
                                                                            $valorAbono = abs($datoAbonos['sumAbonos']);
                                                                            $valorLinea = $valorLin - $valorAbono;
                                                                            echo number_format($valorLinea, 0);
                                                                        }
                                                                        ?>
                                                                    </label>
                                                                </td>
                                                                <td style="width: 110px" align="left" title="Valor Abono">
                                                                    &ensp;&ensp;
                                                                    <label id="abo<?php echo $paPaciente ?>">
                                                                        <?php echo number_format($valorAbono) ?>
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </a>
                                                    <div class="btn-group" data-toggle="buttons" style="float: right; margin-right: 5px; margin-top: 3px">
                                                        <input type="hidden" id="nitResponsable2" name="nitResponsable2" value="<?php obtenerNitEmpresa($codResponsable,$wbasedato) ?>">
                                                        <label class="btn btn-primary active"
                                                               onclick="modificarVal('<?php echo $paPaciente ?>')">
                                                            <input type="checkbox" id="codPlan<?php echo $paPaciente ?>" name="codPlan[]" value="<?php echo $paPaciente ?>" checked>
                                                            <span class="glyphicon glyphicon-ok">
                                                            </span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div id="<?php echo $hisPac?>" class="collapse">
                                                        <table border="0" style="width: 99%;margin-bottom: 10px; background-color: #EEEEEE">
                                                            <thead>
                                                            <tr style="background-color: #2A5DB0; color: white; font-size: medium">
                                                                <th style="text-align: center"><label>Cargo</label></th>
                                                                <th style="text-align: center"><label>Fecha Cargo</label></th>
                                                                <th style="text-align: center"><label>Procedimiento</label></th>
                                                                <th style="text-align: center"><label>Descripcion Procedimiento</label></th>
                                                                <th style="text-align: center"><label>Cantidad</label></th>
                                                                <th style="text-align: center"><label>Valor Cargo</label></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody style="border: #2A5DB0 2px solid; border-right: none">
                                                            <?php
                                                            $queryCargos2 = mysql_queryV("select * from".' '."$wbasedato"."_000106
                                                                                WHERE Tcarhis = '$hisPac' AND Tcaring = '$ingPac'
                                                                                AND Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                                                AND Tcarres LIKE '$codResponsable%'
                                                                                AND tcarest = 'on'
                                                                                AND tcarcan > 0
                                                                                AND Tcarvun > 0
                                                                                AND tcarfac = 'S'
                                                                                AND Tcarfre = 0
                                                                                AND Tcarfex = 0
                                                                                ORDER BY Tcarfec ASC");
                                                            while($datoCargos2 = mysql_fetch_array($queryCargos2))
                                                            {
                                                                $idCargo = $datoCargos2['id'];              $fecCargo = $datoCargos2['Tcarfec'];    $codproced = $datoCargos2['Tcarprocod'];
                                                                $descProced = $datoCargos2['Tcarpronom'];   $cantProced = $datoCargos2['Tcarcan'];  $valTotProced = $datoCargos2['Tcarvto'];
                                                                $valUnidad = $datoCargos2['Tcarvun'];       $cantProced = number_format($cantProced,0);
                                                                ?><input type="hidden" id="valUni<?php echo $idCargo ?>" name="valUni" value="<?php echo $valUnidad ?>"><?php

                                                                ?>
                                                                <tr class="alternar">
                                                                    <td style="border: #2A5DB0 1px solid">
                                                                        <label>
                                                                            <input type="text" id="cargo_id<?php echo $idCargo ?>" name="cargo_id[]" value="<?php echo $idCargo ?>"
                                                                                   style="border: none; background-color: transparent; width: 70px" readonly>
                                                                        </label>
                                                                    </td>
                                                                    <td style="border: #2A5DB0 1px solid"><label><?php echo $fecCargo ?></label></td>
                                                                    <td style="border: #2A5DB0 1px solid"><label><?php echo $codproced ?></label></td>
                                                                    <td align="left" style="border: #2A5DB0 1px solid">&ensp;<label><?php echo $descProced ?></label></td>
                                                                    <td style="border: #2A5DB0 1px solid"><label><?php echo $cantProced ?></label></td>
                                                                    <td align="right" style="border: #2A5DB0 1px solid"><label id="valTotP<?php echo $idCargo ?>"><?php echo $valTotProced ?></label>&ensp;</td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- PANEL INFERIOR -->
                            <div align="right" style="background-color: #C3D9FF; height: 40px">
                                <section>
                                    <?php
                                    $queryTotalCargos = mysql_queryV("select SUM(Tcarvto) from".' '."$wbasedato"."_000106
                                                             where Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                             AND Tcarres LIKE '$codResponsable%'
                                                             AND tcarest = 'on'
                                                             AND tcarcan > 0
                                                             AND tcarfac = 'S'
                                                             AND Tcarfre = 0
                                                             AND Tcarfex = 0");
                                    $datoTotalCargos = mysql_fetch_array($queryTotalCargos);
                                    $valorTotalCargos = $datoTotalCargos[0];
                                    ?>
                                </section>
                                <table>
                                    <tr style="font-size: medium; font-weight: bold">
                                        <td style="padding-top: 5px">TOTAL RESPONSABLE:</td>
                                        <td>&ensp;<?php// echo 'VALOR TOTAL = '.$suma ?></td>
                                        <td style="padding-top: 5px"><input type="text" id="totalResp" value="<?php echo number_format($suma,0) ?>" style="background-color: transparent; border: none" readonly>&ensp;&ensp;</td>
                                        <td>
                                            &ensp;
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- SECCION REALIZAR POSTBACK -->
                            <div style="height: 50px">
                                <input type="hidden" id="codResponsable" name="codResponsable" value="<?php echo $codResponsable ?>">
                                <input type="hidden" id="valTotCar" name="valTotCar" value="<?php echo $suma ?>">
                                <input type="hidden" id="fechaI" name="fechaI" value="<?php echo $fechaInicial ?>">
                                <input type="hidden" id="fechaF" name="fechaF" value="<?php echo $fechaFinal ?>">
                                <input type="hidden" id="nameResponse" name="nameResponse" value="<?php echo $responsable ?>">
                                <input type="hidden" id="nitResponsable" name="nitResponsable" value="<?php obtenerNitEmpresa($codResponsable,$wbasedato) ?>">
                                <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="<?php echo $wemp ?>">
                                <input type="hidden" id="reload" name="reload" value="1">
                                <input type="hidden" id="accion" name="accion" value="facturar">
                                <input type="submit" class="btn btn-success btn-sm" style="margin-top: 10px; margin-right: 100px; width: 120px; height: 35px"
                                       value="FACTURAR" onclick="openWinFacturar(accion,fechaI,fechaF,valTotCar,codResponsable)">
                                       <!--value="FACTURAR" onclick="openWinFacturar(accion,fechaI,fechaF,valTotCar,nitResponsable)"> -->
                            </div>
                        </form>
                        <?php
                    }
                    else
                    {
                        ?>
                        <h3>NO EXISTEN DATOS QUE CUMPLAN ESTAS CONDICIONES</h3>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
$fechaA = $_POST['fechaI']; $fechaZ = $_POST['fechaF']; $niResponse = $_POST['codResponsable']; $descResponsable2 = $_POST['nameResponse'];

if($fechaA != null)
{
    ?>
    <section>
        <input type="hidden" id="fecInicialDown2" name="fecInicialDown2" value="<?php echo $fechaA ?>">
        <input type="hidden" id="fecFinalDown2" name="fecFinalDown2" value="<?php echo $fechaZ ?>">
        <input type="hidden" id="response2" name="response2" value="<?php echo $descResponsable2 ?>">
        <script>checkEntradas2('fecInicialDown2','fecFinalDown2','response2')</script>
    </section>
    <?php
}

if (isset($_POST['codPlan'])) //TODAS LAS HISTORIAS EXCEPTO LAS DESMARCADAS
{
    $arreglo = implode(',', $_POST['codPlan']);
    $arreglo2 = implode(',', $_POST['cargo_id']);
    ?>
    <form name="formNuevo" id="formNuevo">
        <input type="hidden" id="planSelected" name="planSelected" value="<?php echo $arreglo ?>"><!--HISTORIAS-->
        <input type="hidden" id="codigoCargo" name="codigoCargo" value="<?php echo $arreglo2 ?>"><!--CARGOS DE ESAS HISTORIAS-->
    </form>
    <?php
}

?>

<?php
////////FUNCIONES//////////

function obtenerNitEmpresa($codResponsable,$wbasedato)
{
    $queryDatosEmpresa = mysql_queryV("select Empnit from".' '."$wbasedato"."_000024 WHERE Empcod like '$codResponsable%'");
    $datosEmpresa = mysql_fetch_array($queryDatosEmpresa);
    echo $datosEmpresa['Empnit'];
}
?>
</body>
</html>