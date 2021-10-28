<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DETALLE DE CARGOS PATOLOGIA - MATRIX</title>
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
    <script>
        function imprimir2(responsablePrint,facturaPrint,empresaPrint,fechaIniPrint,fechaFinPrint)
        {
            ancho = 200;    alto = 245;

            var miPopup11 = null;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
            settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';

            miPopup11 = window.open("printDetCargos_pat.php?responsable="+responsablePrint.value+
                "&wemp_pmla="+empresaPrint.value+"&factura="+facturaPrint.value+
                "&fechaIni="+fechaIniPrint.value+"&fechaFin="+fechaFinPrint.value,
                "miwin",settings2);
            miPopup11.focus();
        }
    </script>
    <style>
        .btn span.glyphicon {
            opacity: 0;
        }
        .btn.active span.glyphicon {
            opacity: 1;
        }
        .alternar:hover {background-color: #D9EDF7;}
    </style><!-- CHECKBOXES-->
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
<div class="container general">
    <div class="panel panel-info contenido">
        <div class="panel-heading encabezado">
            <div align="center" class="panel-title"><label style="font-weight: bold">Matrix - DETALLE DE CARGOS PATOLOGIA</label></div>
        </div>
        <div id="allPanels" class="input-group divGeneralPaneles text-center" style="text-align: center; display: block; min-height: 140px; max-height: 130px">
            <div id="divDefault" class="input-group divGeneralAcordion text-center" style="text-align: center; display: block; margin-top: 10px">

                <!-- PANEL SELECCION DE PARAMETROS: -->
                <form name="frmParametros" method="post" action="detCargos_pat.php">
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
                                        <td colspan="2">
                                            <label for="country_id">RESPONSABLE: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="text" id="country_id" name="responsable" class="form-control input-sm spamMedGral" style="width: 410px" onkeyup="autocomplet()">
                                            <ul id="country_list_id" style="cursor: pointer; position: fixed; top: 160px; background-color: #FFFFFF"></ul>
                                        </td>
                                        <td>&ensp;&ensp;&ensp;</td>
                                        <td align="left">
                                            <div class="input-group rangoFechas" align="center">
                                                <span class="input-group-addon input-sm"><label for="factura">Factura</label></span>
                                                <input type="text" id="factura" name="factura" class="form-control form-sm" style="width: 150px">
                                            </div>
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

                    $qryResponsable = "select Empnom from".' '."$wbasedato"."_000024 where Empcod like '$descResponsable%'";
                    $commitQryResponsable = mysql_query($qryResponsable,$conex) or die (mysql_errno()." - en el query: ".$qryResponsable." - ".mysql_error());
                    $datoResponsable = mysql_fetch_assoc($commitQryResponsable);
                    $nombreResp = $datoResponsable['Empnom'];
                    if(strlen($nombreResp) > 35)
                    {
                        $nombreResp = substr($nombreResp,0,35).'...';
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
                        <label style="color: #428BCA; font-size: large"><?php echo $nombreResp ?></label>
                    </div>
                    <?php
                }
                ?>
            </div>

            <div id="rep1" style="margin-top: 10px; position: static">
                <!-- MANTENER VISIBLES PARAMETROS DE BUSQUEDA: -->
                <section>
                    <?php
                    $fechaInicial = $_POST['fechaInicial']; $fechaFinal = $_POST['fechaFinal'];
                    $responsable = $_POST['responsable'];   $factura = $_POST['factura'];
                    if($fechaInicial == null and $fechaFinal == null and $responsable == null)
                    {
                        $fechaInicial = $_POST['fechaI'];   $fechaFinal = $_POST['fechaF']; $responsable = $_POST['nameResponse'];
                    }
                    list($codResponsable, $nomResponsable) = explode("-", $responsable);
                    $codResponsable = trim($codResponsable);    $nomResponsable2 = trim($nomResponsable);
                    $codResponsable2 = $codResponsable.'-'.$nomResponsable2;
                    ?>
                    <input type="hidden" id="fecInicialDown" name="fecInicialDown" value="<?php echo $fechaInicial ?>">
                    <input type="hidden" id="fecFinalDown" name="fecFinalDown" value="<?php echo $fechaFinal ?>">
                    <input type="hidden" id="responsableDown" name="responsableDown" value="<?php echo $responsable ?>">
                    <input type="hidden" id="facturaDown" name="facturaDown" value="<?php echo $factura ?>">
                    <script>checkEntradas3('fecInicialDown','fecFinalDown','responsableDown','facturaDown')</script>
                </section>

                <?php
                //BUSQUEDA POR FACTURA:
                if($factura != null)
                {
                    //VERIFICAR SI EXISTEN DATOS EN TABLA_000018 QUE CUMPLAN LAS CONDICIONES INGRESADAS:
                    $queryNumFactura = "select * from".' '."$wbasedato"."_000018 where Fenfac = '$factura' and Fenest = 'on'";
                    $datoNumFactura = mysql_query($queryNumFactura,$conex) or die (mysql_errno()." - en el query: ".$queryNumFactura." - ".mysql_error());
                    $existe = mysql_num_rows($datoNumFactura);
                }

                //BUSQUEDA POR FECHA INICIAL - FECHA FINAL - RESPONSABLE:
                elseif($fechaInicial != null and $fechaFinal != null and $responsable != null)
                {

                }

                if($existe > 0)
                {
                    while($datosxFactura = mysql_fetch_assoc($datoNumFactura))
                    {
                        $Fecha_Data18 = $datosxFactura['Fecha_data']; $responsable18 = $datosxFactura['Fencod'];
                    }
                    ?>
                    <input type="hidden" id="fechaFnd" name="fechaFnd" value="<?php echo $Fecha_Data18 ?>">
                    <input type="hidden" id="responsableFnd" name="responsableFnd" value="<?php echo $responsable18 ?>">
                    <script>
                        checkEntradas4('fechaFnd','responsableFnd');
                        checkEntradas3('fecInicialDown','fecFinalDown','responsableDown','facturaDown');
                    </script>

                    <!-- PANEL CONTENIDO -->
                    <div class="titulo" style="margin-top: 0">
                        <label>DETALLE DE LA FACTURA</label>&ensp;<label style="font-weight: bolder"><?php echo $factura ?></label>
                    </div>
                    <form name="frmPrincipal" id="frmPrincipal" method="post" action="detCargos_pat.php">
                        <div class="Reporte" style="margin-top: 5px">
                            <table border="0" style="width: 100%">
                                <thead>
                                <tr style="background-color: #2A5DB0; color: white; font-size: medium">
                                    <th style="text-align: center; width: 30px">Orden</th>
                                    <th style="text-align: center; width: 75px">Fecha</th>
                                    <th style="text-align: center; width: 60px">Autorizac</th>
                                    <th style="text-align: center; width: 60px">Documento</th>
                                    <th style="text-align: center; width: 280px">Usuario</th>
                                    <th style="text-align: center; width: 50px">Facturado</th>
                                    <th style="text-align: center; width: 50px">Descto</th>
                                    <th style="text-align: center; width: 50px">Franquicia</th>
                                    <th style="text-align: center; width: 80px">Facturado Neto</th>
                                </tr>
                                </thead>
                            </table>

                            <!-- PANEL MEDIO -->
                            <?php
                            $queryFactura66 = "select tcarhis,tcaring,rcffac,Tcarno1,Tcarno2,Tcarap1,Tcarap2,Tcardoc,Tcarconcod,sum(Tcarvto) SUMA, rcfreg
                                               from".' '."$wbasedato"."_000066 p66, ".' '."$wbasedato"."_000106 p106
                                               where rcffac = '$factura'
                                               and rcfreg = p106.id
                                               and tcarest = 'on'
                                               group by rcffac,Tcarno1,Tcarno2,Tcarap1,Tcarap2,Tcardoc,Tcarconcod
                                               order by cast(tcarhis as integer) ASC";
                            $datoFactura66 = mysql_query($queryFactura66,$conex) or die (mysql_errno()." - en el query: ".$queryFactura66." - ".mysql_error());
                            //echo $queryFactura66;
                            ?>
                            <div style="border-bottom: black 2px solid; max-height: 450px; overflow: auto">
                                <table align="left" border="0" style="width: 100%">
                                    <tbody>
                                    <?php
                                    while($datosFactura66 = mysql_fetch_assoc($datoFactura66))
                                    {
                                        $historia = $datosFactura66['tcarhis'];     $ingreso = $datosFactura66['tcaring'];
                                        $docPaciente = $datosFactura66['Tcardoc'];  $id106 = $datosFactura66['rcfreg'];
                                        $nomPaciente = $datosFactura66['Tcarno1'].' '.$datosFactura66['Tcarno2'].' '.$datosFactura66['Tcarap1'].' '.$datosFactura66['Tcarap2'];
                                        $codigoConcepto = $datosFactura66['Tcarconcod'];    $sumaConcepto = $datosFactura66['SUMA'];

                                        if($sumaConcepto > 0){ $concepPosit = $sumaConcepto;}
                                        if($sumaConcepto < 0){ $concepNegat = abs($sumaConcepto);}
                                        $valorNeto = $concepPosit - $concepNegat;

                                        //OBTENER DATOS DEL INGRESO:
                                        $queryDatosIng = "select * from".' '."$wbasedato"."_000101 where Inghis = '$historia' and Ingnin = '$ingreso'";
                                        $datoDatIng = mysql_query($queryDatosIng,$conex);
                                        $datosIngreso = mysql_fetch_assoc($datoDatIng);
                                        $fechaIngreso = $datosIngreso['Ingfei'];    $autorizacion = $datosIngreso['Ingord'];

                                        //MOSTRAR UNA FILA POR CADA HISTORIA - INGRESO:
                                        if($sumaConcepto > 0)
                                        {
                                            ?>
                                            <tr class="alternar">
                                                <td style="width: 50px" align="left">&ensp;<?php echo $historia.'-'.$ingreso ?></td>
                                                <td style="width: 50px" align="left"><?php echo $fechaIngreso ?></td>
                                                <td style="width: 50px" align="left"><?php echo $autorizacion ?></td>
                                                <td style="width: 50px" align="left"><?php echo $docPaciente ?></td>
                                                <td style="width: 230px" align="left"><?php echo $nomPaciente ?></td>
                                                <td style="width: 60px" align="right"><?php echo $concepPosit ?></td>
                                                <td style="width: 60px">0</td>
                                                <td style="width: 60px">
                                                    <?php
                                                    $qryValores106 = "select sum(Tcarvto) SUMAR
                                                                   from".' '."$wbasedato"."_000066 p66, ".' '."$wbasedato"."_000106 p106
                                                                   where rcffac = '$factura'
                                                                   and rcfreg = p106.id
                                                                   and tcarest = 'on'
                                                                   and tcarconcod in('9001','9003')
                                                                   and tcarhis = '$historia'
                                                                   and tcaring = '$ingreso'
                                                                   group by Tcarconcod, rcffac";
                                                    $commitQryValores106 = mysql_query($qryValores106,$conex);
                                                    $datoValores106 = mysql_fetch_assoc($commitQryValores106);

                                                    $valorAbonos = $datoValores106['SUMAR'];
                                                    echo abs($valorAbonos);
                                                    ?>
                                                </td>
                                                <td style="width: 80px" align="right"><?php $valorNeto2 = $concepPosit - abs($valorAbonos); echo $valorNeto2 ?>&ensp;&ensp;</td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PANEL INFERIOR -->
                            <div align="left" style="background-color: #C3D9FF; height: 100px">
                                <section>
                                    <?php
                                    $queryTotalCargos = mysql_queryV("select Fenval, Fenabo, Fendes from".' '."$wbasedato"."_000018
                                                             where Fenfac = '$factura'
                                                             AND Fenest = 'on'");
                                    $datoTotalCargos = mysql_fetch_array($queryTotalCargos);
                                    $Fenval = $datoTotalCargos[0];  $Fenabo = $datoTotalCargos[1]; $Fendes = $datoTotalCargos[2];
                                    $subtotal = $Fenval + $Fenabo;  $franquicia = $Fenabo;  $descuento = $Fendes;
                                    ?>
                                </section>
                                <table align="left">
                                    <tr style="font-size: small; font-weight: bold">
                                        <td style="padding-top: 5px">&ensp;&ensp;SUBTOTAL:</td>
                                        <td>&ensp;</td>
                                        <td style="padding-top: 5px"><?php echo number_format($subtotal,0) ?></td>
                                    </tr>
                                    <tr style="font-size: small; font-weight: bold">
                                        <td style="padding-top: 5px">&ensp;&ensp;DESCUENTO:</td>
                                        <td>&ensp;</td>
                                        <td style="padding-top: 5px"><?php echo number_format($descuento,0) ?></td>
                                    </tr>
                                    <tr style="font-size: small; font-weight: bold">
                                        <td style="padding-top: 5px">&ensp;&ensp;MENOS FRANQUICIA:</td>
                                        <td>&ensp;</td>
                                        <td style="padding-top: 5px"><?php echo number_format($franquicia,0) ?></td>
                                    </tr>
                                    <tr style="font-size: small; font-weight: bold">
                                        <td style="padding-top: 5px">&ensp;&ensp;TOTAL A PAGAR:</td>
                                        <td>&ensp;</td>
                                        <td style="padding-top: 5px"><?php echo number_format($Fenval,0) ?></td>
                                    </tr>
                                </table>

                                <table align="right" style="margin-right: 540px; margin-top: 15px">
                                    <tr>
                                        <td>
                                            <input type="hidden" name="responsablePrint" id="responsablePrint" value="<?php echo $descResponsable?>">
                                            <input type="hidden" name="facturaPrint" id="facturaPrint" value="<?php echo $factura?>">
                                            <input type="hidden" name="empresaPrint" id="empresaPrint" value="<?php echo $wemp?>">
                                            <input type="hidden" name="fechaIniPrint" id="fechaIniPrint" value="<?php echo $fechaInicial?>">
                                            <input type="hidden" name="fechaFinPrint" id="fechaFinPrint" value="<?php echo $fechaFinal?>">
                                            <br>
                                            <input type="hidden" id="imprimir" title="IMPRIMIR" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25" onclick="//imprimir()">
                                            &ensp;&ensp;
                                            <a id="exportar" onclick="imprimir2(responsablePrint,facturaPrint,empresaPrint,fechaIniPrint,fechaFinPrint)" title="IMPRIMIR" style="cursor: hand">
                                                <img src='/matrix/images/medical/dircie/printventosad.png' width="25" height="25" border="0" style="margin-top: -18px"/>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                    <?php
                }
                else
                {
                    while($datosxFactura = mysql_fetch_assoc($datoNumFactura))
                    {
                        $Fecha_Data18 = $datosxFactura['Fecha_data']; $responsable18 = $datosxFactura['Fennit'];
                    }
                    ?>
                    <input type="hidden" id="fechaFnd" name="fechaFnd" value="<?php echo $Fecha_Data18 ?>">
                    <script>
                        checkEntradas4('fechaFnd');
                        checkEntradas3('fecInicialDown','fecFinalDown','responsableDown','facturaDown');
                    </script>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- FUNCIONES: -->
    <?php
    function consultarAbonos($wbasedato,$conex,$historia,$ingreso,$idRegistro66)
    {
        //$queryDetFactura2 = "select sum(Tcarvto) SUMA from".' '."$wbasedato"."_000106 where Tcarhis = '$historia' and Tcaring = '$ingreso' and Tcarvto < '0' and Tcarest = 'on' group by Tcarhis,Tcaring";
        //$queryDetFactura2 = "select sum(Tcarvto) SUMA from".' '."$wbasedato"."_000106 where Tcarhis = '$historia' and Tcaring = '$ingreso' and Tcarconcod = '9001' and Tcarest = 'on' group by Tcarhis,Tcaring";
        // SE ADICIONA EL CONCEPTO DE DESCUETO 9003:
        $queryDetFactura2 = "select sum(Tcarvto) SUMA from".' '."$wbasedato"."_000106 where Tcarhis = '$historia' and Tcaring = '$ingreso' and Tcarconcod in('9001','9003') and Tcarest = 'on' group by Tcarhis,Tcaring";
        //$queryDetFactura2 = "select sum(Tcarvto) SUMA from".' '."$wbasedato"."_000106 where id = '$idRegistro66'";
        $datoDetFactura2 = mysql_query($queryDetFactura2,$conex);
        $detFactura2 = mysql_fetch_assoc($datoDetFactura2);
        $totCargoNeg = $detFactura2['SUMA'];

        if($totCargoNeg < '0')
        {
            echo $totCargoNeg;
        }
        else
        {
            echo '0';
        }
        ?>
        <?php
    }
    ?>
</body>
</html>