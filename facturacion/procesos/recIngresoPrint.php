<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - RECIBOS DE CUENTAS POR COBRAR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <link href="StyleRecIngreso.css" rel="stylesheet">
    <script src="JsrecIngreso.js" type="text/javascript"></script>
    <?php
    include("conex.php");
    include("root/comun.php");
    include("recIngFunctions.php");

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
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
        $wactualiz = "Versión: 1.0 07-Febrero-2020";
    }
    session_start();

    $fte = $_GET['fuente'];        $fac = $_GET['factura'];
    if($fte == '30')
    {
        $qryCount1 = "SELECT count(*) FROM cacar WHERE carfue = '$fte' AND cardoc = '$fac' AND caranu = '0'";
        $datoCount1 = odbc_do($conex_o,$qryCount1);
        $cont = odbc_result($datoCount1,1);

        $query_1 = "SELECT * FROM cacar WHERE carfue = '$fte' AND cardoc = '$fac'";
        $dato_1 = odbc_do($conex_o,$query_1);
        $carfue = odbc_result($dato_1, 1);// numero fuente
        $cardoc = odbc_result($dato_1, 2);// documento
        $carfca = odbc_result($dato_1, 18);
        $carfac = odbc_result($dato_1, 19);
        $carval = odbc_result($dato_1, 21);

        $query_2 = "SELECT * FROM cbmov WHERE movfue = '$fte' AND movdoc = '$fac'";
        $dato_2 = odbc_do($conex_o,$query_2);
        $movval = odbc_result($dato_2,14);  $carfec = odbc_result($dato_2, 5);// fecha
        $nitced = odbc_result($dato_2,10);  $descnitced = odbc_result($dato_2,11);
        $movval2 = $movval;

        $queryEmpresa = "SELECT * FROM inemp WHERE empnit = '$nitced'";
        $commEmpresa = odbc_do($conex_o,$queryEmpresa);
        $empCod = odbc_result($commEmpresa, 1); $empNom = odbc_result($commEmpresa, 3);
    }
    ?>
</head>

<body>
<div id="divGeneral" class="divGeneral">
    <div id="divContenido" class="divContenidoPrint">
        <div id="divEncabezado" class="divEncabezado" align="center">
            <div>
                <span><label>PROMOTORA MEDICA LAS AMERICAS S.A.</label></span>
                <br>
                <span><label>Nit. 800067065</label></span>
            </div>
            <div align="right" style="width: 145px; float: right; margin-top: -50px; margin-right: 120px">
                <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="250" height="120">
            </div>
        </div>

        <div id="divDatosGenerales" class="divDatosGenerales">
            <table>
                <tr>
                    <td><label>RECIBO No: &ensp;</label><label class="lblNormal" id="numRecibo"><?php echo $cardoc ?></label></td>
                    <td>&ensp;</td>
                    <td><label>FECHA: &ensp;</label><label class="lblNormal" id="fechaRecibo"><?php echo $carfec ?></label></td>
                    <td>&ensp;</td>
                    <td><label>VALOR-RECIBO: &ensp;</label><label class="lblNormal" id="valorAbono"><?php echo number_format($movval2,2) ?></label></td>
                </tr>
                <tr>
                    <td><label>SEÑORES: &ensp;</label><label class="lblNormal" id="senores"><?php echo $empNom ?></label></td>
                    <td>&ensp;</td>
                    <td colspan="4"><label>NIT/CED: &ensp;</label><label class="lblNormal" id="nitCed"><?php echo $empCod ?></label></td>
                </tr>
                <tr>
                    <td colspan="7">
                        <label>POR CUENTA DE: &ensp;</label><label class="lblNormal" id="destinatario">
                            <?php echo $nitced.' - '.$descnitced ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <label>VALOR EN LETRAS: &ensp;</label><label class="lblNormal" id="observacion">
                            <?php
                            $valorL = str_replace('.00','',$movval);
                            echo strtoupper(convertir($valorL)).' PESOS CON 00/100 M/CTE';
                            ?>
                        </label>
                    </td>
                </tr>
            </table>

            <hr>
            <div align="center">
                <table border="0" style="width: 100%; margin-left: 0">
                    <tr>
                        <td>&ensp;</td>
                        <td align="center">
                            <label class="lblNormal" style="text-align: center; font-weight: bold; margin-left: 180px">DETALLE DE FACTURAS:</label>
                        </td>
                        <td align="right">
                            <label class="lblNormal" style="margin-right: 5px; font-weight: bold">Pagina 1</label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- DETALLE DE FACTURAS -->
        <div id="divDatosEspecificos1" class="divDatosEspecificos" style="height: 400px">
            <?php
            $div = 1;

            if($cont > 0){mostrarData2($fte,$fac,$conex,$conex_o,0,$div);}

            if($cont > 51){mostrarData2($fte,$fac,$conex,$conex_o,51,$div);}

            if($cont > 101){mostrarData2($fte,$fac,$conex,$conex_o,101,$div);}

            if($cont > 151){mostrarData2($fte,$fac,$conex,$conex_o,151,$div);}
            ?>
        </div>

        <div id="divPiepagina1" class="divPiepagina" style="margin-top: -2px">
            <?php
            detalleConceptos($fte,$fac,$conex_o,200);
            mostrarPie($fte,$fac,$conex_o);
            ?>
        </div>
    </div>

    <!--
    <div align="center" style="margin-top: 1px; margin-bottom: 30px">
        <label style="text-align: center; font-size: smaller">Pagina 1</label>
    </div>
    -->
</div>

<?php

//PAGINA 2
if($cont > 201)
{
    ?>
    <div id="divGeneral" class="divGeneral">
        <div id="divContenido" class="divContenidoPrint">

            <div id="divEncabezado" class="divEncabezado" align="center">
                <div>
                    <span><label>PROMOTORA MEDICA LAS AMERICAS S.A.</label></span>
                    <br>
                    <span><label>Nit. 800067065</label></span>
                </div>
                <div align="right" style="width: 145px; float: right; margin-top: -50px; margin-right: 55px">
                    <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="190" height="120">
                </div>
            </div>

            <div id="divDatosGenerales" class="divDatosGenerales">
                <table>
                    <tr>
                        <td><label>RECIBO No: &ensp;</label><label class="lblNormal" id="numRecibo"><?php echo $cardoc ?></label></td>
                        <td>&ensp;</td>
                        <td><label>FECHA: &ensp;</label><label class="lblNormal" id="fechaRecibo"><?php echo $carfec ?></label></td>
                        <td>&ensp;</td>
                        <td><label>VALOR-RECIBO: &ensp;</label><label class="lblNormal" id="valorAbono"><?php echo $movval ?></label></td>
                    </tr>
                    <tr>
                        <td><label>SEÑORES: &ensp;</label><label class="lblNormal" id="senores"><?php echo $empNom ?></label></td>
                        <td>&ensp;</td>
                        <td colspan="4"><label>NIT/CED: &ensp;</label><label class="lblNormal" id="nitCed"><?php echo $empCod ?></label></td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <label>POR CUENTA DE: &ensp;</label><label class="lblNormal" id="destinatario">
                                <?php echo $nitced.' - '.$descnitced ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <label>VALOR EN LETRAS: &ensp;</label><label class="lblNormal" id="observacion">
                                <?php
                                $valorL = str_replace('.00','',$movval);
                                echo strtoupper(convertir($valorL)).' PESOS CON 00/100 M/CTE';
                                ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <hr>
                <div align="center">
                    <table border="0" style="width: 100%; margin-left: 0">
                        <tr>
                            <td>&ensp;</td>
                            <td align="center">
                                <label class="lblNormal" style="text-align: center; font-weight: bold; margin-left: 180px">DETALLE DE FACTURAS:</label>
                            </td>
                            <td align="right">
                                <label class="lblNormal" style="margin-right: 5px; font-weight: bold">Pagina 2</label>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- DETALLE DE FACTURAS -->
            <div id="divDatosEspecificos2" class="divDatosEspecificos" style="height: 400px">
                <?php
                $div = 2;

                mostrarData2($fte,$fac,$conex,$conex_o,201,$div);

                if($cont > 251){mostrarData2($fte,$fac,$conex,$conex_o,251,$div);}

                if($cont > 301){mostrarData2($fte,$fac,$conex,$conex_o,301,$div);}

                if($cont > 351){mostrarData2($fte,$fac,$conex,$conex_o,351,$div);}
                ?>
            </div>

            <div id="divPiepagina2" class="divPiepagina" style="margin-top: -2px">
                <?php
                detalleConceptos($fte,$fac,$conex_o,400);
                mostrarPie($fte,$fac,$conex_o)
                ?>
            </div>
        </div>

        <!--
        <div align="center" style="margin-top: 1px; margin-bottom: 15px">
            <label style="text-align: center; font-size: smaller">Pagina 2</label>
        </div>
        -->
    </div>
    <?php
}

//PAGINA 3
if($cont > 401)
{
    ?>
    <div id="divGeneral" class="divGeneral">
        <div id="divContenido" class="divContenidoPrint">

            <div id="divEncabezado" class="divEncabezado" align="center">
                <div>
                    <span><label>PROMOTORA MEDICA LAS AMERICAS S.A.</label></span>
                    <br>
                    <span><label>Nit. 800067065</label></span>
                </div>
                <div align="right" style="width: 145px; float: right; margin-top: -50px; margin-right: 55px">
                    <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="190" height="120">
                </div>
            </div>

            <div id="divDatosGenerales" class="divDatosGenerales">
                <table>
                    <tr>
                        <td><label>RECIBO No: &ensp;</label><label class="lblNormal" id="numRecibo"><?php echo $cardoc ?></label></td>
                        <td>&ensp;</td>
                        <td><label>FECHA: &ensp;</label><label class="lblNormal" id="fechaRecibo"><?php echo $carfec ?></label></td>
                        <td>&ensp;</td>
                        <td><label>VALOR-RECIBO: &ensp;</label><label class="lblNormal" id="valorAbono"><?php echo $movval ?></label></td>
                    </tr>
                    <tr>
                        <td><label>SEÑORES: &ensp;</label><label class="lblNormal" id="senores"><?php echo $empNom ?></label></td>
                        <td>&ensp;</td>
                        <td colspan="4"><label>NIT/CED: &ensp;</label><label class="lblNormal" id="nitCed"><?php echo $empCod ?></label></td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <label>POR CUENTA DE: &ensp;</label><label class="lblNormal" id="destinatario">
                                <?php echo $nitced.' - '.$descnitced ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <label>VALOR EN LETRAS: &ensp;</label><label class="lblNormal" id="observacion">
                                <?php
                                $valorL = str_replace('.00','',$movval);
                                echo strtoupper(convertir($valorL)).' PESOS CON 00/100 M/CTE';
                                ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <hr>
                <div align="center">
                    <table border="0" style="width: 100%; margin-left: 0">
                        <tr>
                            <td>&ensp;</td>
                            <td align="center">
                                <label class="lblNormal" style="text-align: center; font-weight: bold; margin-left: 180px">DETALLE DE FACTURAS:</label>
                            </td>
                            <td align="right">
                                <label class="lblNormal" style="margin-right: 5px; font-weight: bold">Pagina 3</label>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- DETALLE DE FACTURAS -->
            <div id="divDatosEspecificos3" class="divDatosEspecificos" style="height: 400px">
                <?php
                $div = 3;

                mostrarData2($fte,$fac,$conex,$conex_o,401,$div);

                if($cont > 451){mostrarData2($fte,$fac,$conex,$conex_o,451,$div);}

                if($cont > 501){mostrarData2($fte,$fac,$conex,$conex_o,501,$div);}

                if($cont > 551){mostrarData2($fte,$fac,$conex,$conex_o,551,$div);}
                ?>
            </div>

            <div id="divPiepagina3" class="divPiepagina" style="margin-top: -2px">
                <?php
                detalleConceptos($fte,$fac,$conex_o,600);
                mostrarPie($fte,$fac,$conex_o)
                ?>
            </div>
        </div>

        <!--
        <div align="center" style="margin-top: 1px; margin-bottom: 15px">
            <label style="text-align: center; font-size: smaller">Pagina 3</label>
        </div>
        -->
    </div>
    <?php
}

//PAGINA 4
if($cont > 601)
{
    ?>
    <div id="divGeneral" class="divGeneral">
        <div id="divContenido" class="divContenidoPrint">

            <div id="divEncabezado" class="divEncabezado" align="center">
                <div>
                    <span><label>PROMOTORA MEDICA LAS AMERICAS S.A.</label></span>
                    <br>
                    <span><label>Nit. 800067065</label></span>
                </div>
                <div align="right" style="width: 145px; float: right; margin-top: -50px; margin-right: 55px">
                    <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="190" height="120">
                </div>
            </div>

            <div id="divDatosGenerales" class="divDatosGenerales">
                <table>
                    <tr>
                        <td><label>RECIBO No: &ensp;</label><label class="lblNormal" id="numRecibo"><?php echo $cardoc ?></label></td>
                        <td>&ensp;</td>
                        <td><label>FECHA: &ensp;</label><label class="lblNormal" id="fechaRecibo"><?php echo $carfec ?></label></td>
                        <td>&ensp;</td>
                        <td><label>VALOR-RECIBO: &ensp;</label><label class="lblNormal" id="valorAbono"><?php echo $movval ?></label></td>
                    </tr>
                    <tr>
                        <td><label>SEÑORES: &ensp;</label><label class="lblNormal" id="senores"><?php echo $empNom ?></label></td>
                        <td>&ensp;</td>
                        <td colspan="4"><label>NIT/CED: &ensp;</label><label class="lblNormal" id="nitCed"><?php echo $empCod ?></label></td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <label>POR CUENTA DE: &ensp;</label><label class="lblNormal" id="destinatario">
                                <?php echo $nitced.' - '.$descnitced ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <label>VALOR EN LETRAS: &ensp;</label><label class="lblNormal" id="observacion">
                                <?php
                                $valorL = str_replace('.00','',$movval);
                                echo strtoupper(convertir($valorL)).' PESOS CON 00/100 M/CTE';
                                ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <hr>
                <div align="center">
                    <table border="0" style="width: 100%; margin-left: 0">
                        <tr>
                            <td>&ensp;</td>
                            <td align="center">
                                <label class="lblNormal" style="text-align: center; font-weight: bold; margin-left: 180px">DETALLE DE FACTURAS:</label>
                            </td>
                            <td align="right">
                                <label class="lblNormal" style="margin-right: 5px; font-weight: bold">Pagina 4</label>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- DETALLE DE FACTURAS -->
            <div id="divDatosEspecificos4" class="divDatosEspecificos" style="height: 400px">
                <?php
                $div = 4;

                mostrarData2($fte,$fac,$conex,$conex_o,601,$div);

                if($cont > 651){mostrarData2($fte,$fac,$conex,$conex_o,651,$div);}

                if($cont > 701){mostrarData2($fte,$fac,$conex,$conex_o,701,$div);}

                if($cont > 751){mostrarData2($fte,$fac,$conex,$conex_o,751,$div);}
                ?>
            </div>

            <div id="divPiepagina4" class="divPiepagina" style="margin-top: -2px">
                <?php
                detalleConceptos($fte,$fac,$conex_o,800);
                mostrarPie($fte,$fac,$conex_o)
                ?>
            </div>
        </div>

        <!--
        <div align="center" style="margin-top: 1px; margin-bottom: 15px">
            <label style="text-align: center; font-size: smaller">Pagina 4</label>
        </div>
        -->
    </div>
    <?php
}
?>

<script>
    window.print();
    window.close();
</script>
</body>
</html>