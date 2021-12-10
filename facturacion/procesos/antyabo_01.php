<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - IMPRESION DE ANTICIPOS Y ABONOS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <link href="StyleAntyabo.css" rel="stylesheet">
    <script src="JsAntyabo.js" type="text/javascript"></script>
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
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
        $wactualiz = "1.1 16-Mayo-2017";
    }
    session_start();
    ?>
</head>

<body>
<div id="divGeneral" class="divGeneral">
<?php encabezado("<font style='font-size: x-large; font-weight: bold'>"."IMPRESION DE ANTICIPOS Y ABONOS"."</font>",$wactualiz,"clinica"); ?>

    <div id="divSelector" class="divSelector">
        <form class="form-horizontal" role="form" name="frmSelector" action="antyabo_01.php" method="post">
            <table align="center">
                <tr>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><label for="fte">FUENTE</label></span>
                            <input id="fte" name="fte" type="text" class="form-control" style="width: 150px" value="">
                        </div>
                    </td>
                    <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><label for="fac">DOCUMENTO</label></span>
                            <input id="fac" name="fac" type="text" class="form-control" style="width: 150px" value="">
                        </div>
                    </td>
                    <td>
                        <div class="col-sm-12 controls">
                            <input type="submit" class="btn btn-info btn-sm" id="bntIr" name="btnIr" title="Generar" value="> > >">
                        </div>
                    </td>
                    <td><div class="input-group-addon" style="background-color: #ffffff; width: 20px; border: none"></div></td>
                </tr>
            </table>
        </form>
    </div>

    <?php
    $fte = $_POST['fte'];   $fac = $_POST['fac'];

    if($fte != null and $fac != null)
    {
        if($fte == '30' or $fte == '31')
        {
            $query_1="SELECT movdoc, movfec, movval, movben, movnom, movind"
                ."  FROM cbmov"
                ."  WHERE movfue = '$fte'"
                ."  and movdoc = '$fac'";
        }
        else
        {
            $query_1="SELECT antdoc, antfec, antval, antced, antnom, antesp"
                ."	FROM anant"
                ."	WHERE antfue = '$fte'"
                ."  and antdoc = '$fac'";
        }

        $query_2="SELECT movdetcaj, movdetfpa, movdetban, movdetpla, movdetpob, movdetfdo, movdetcba, movdetval, movdetdco"
            ."  FROM cbmovdet"
            ."  WHERE movdetfue = '$fte'"
            ."  and movdetdoc = '$fac'";

        $query_3="SELECT carobsdes"
            ."  FROM cacarobs"
            ."  WHERE carobsfue = '$fte'"
            ."  and carobsdoc = '$fac'";

        /*
        $query_4="Select antpacced, antpacnom, antpacap1, antpacap2, antpacval"
            ."  FROM anantpac"
            ."  WHERE antpacfue = '$fte'"
            ."  and antpacdoc = '$fac'"
            ."  and antpacsec = (select max(antpacsec)"
            ."  FROM anantpac"
            ."  WHERE antpacfue = '$fte'"
            ."  and antpacdoc = '$fac')";
        */
        $query_4="Select antpacced, antpacnom, antpacap1, antpacval"
            ."  FROM anantpac"
            ."  WHERE antpacfue = '$fte'"
            ."  and antpacdoc = '$fac'"
            ."  and antpacsec = (select max(antpacsec)"
            ."  FROM anantpac"
            ."  WHERE antpacfue = '$fte'"
            ."  and antpacdoc = '$fac')";

        $query_6="Select antpacap2"
            ."  FROM anantpac"
            ."  WHERE antpacfue = '$fte'"
            ."  and antpacdoc = '$fac'";

        $query_5="SELECT movdetfpa, movdetban, movdetpla, movdetpob, movdetdpa, movdetcba, movdetval, movdetbco"
            ."  FROM cbmovdet"
            ."  WHERE movdetfue = '$fte'"
            ."  and movdetdoc = '$fac'";

        $dato_1 = odbc_do($conex_o,$query_1);
        $dato_2 = odbc_do($conex_o,$query_2);
        $dato_3 = odbc_do($conex_o,$query_3);
        $dato_4 = odbc_do($conex_o,$query_4);
        $dato_6 = odbc_do($conex_o,$query_6);
        $dato_5 = odbc_do($conex_o,$query_5);

        while(odbc_fetch_row($dato_1))
        {
            $documento = odbc_result($dato_1, 1);// numero recibo o abono
            $fecha = odbc_result($dato_1, 2);// fecha de recibo o abono
            $val = odbc_result($dato_1, 3);// valor recibo o abono
            $valor = number_format($val,0);// valor formateado para eliminar decimales y agregar como separador la ','
            $nitced = odbc_result($dato_1, 4);//nit o cedula
            $descnitced = odbc_result($dato_1, 5);// descripcion de nit o cedula
            $destespec = odbc_result($dato_1, 6);//
        }
        while(odbc_fetch_row($dato_2))
        {
            $caja = odbc_result($dato_2, 1);// caja N.
        }
        while(odbc_fetch_row($dato_3))
        {
            $observacion = odbc_result($dato_3, 1); // observaciones
            //if(is_null($observacion)){$observacion = 'NA';}
        }
        ?>

        <div id="divContenido" class="divContenido">
            <div id="divEncabezado" class="divEncabezado" align="center">
                <div>
                    <span><label>PROMOTORA MEDICA LAS AMERICAS S.A.</label></span>
                    <br>
                    <span><label>Nit. 800067065</label></span>
                </div>
                <div align="right" style="width: 145px; float: right; margin-top: -65px; margin-right: 30px">
                    <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="70">
                </div>
            </div>

            <div id="divDatosGenerales" class="divDatosGenerales">
                <table>
                    <tr>
                        <td><label>RECIBO No: &ensp;</label><label class="lblNormal" id="numRecibo"><?php echo $documento ?></label></td>
                        <td>&ensp;</td>
                        <td><label>FECHA: &ensp;</label><label class="lblNormal" id="fechaRecibo"><?php echo $fecha ?></label></td>
                        <td>&ensp;</td>
                        <td><label>VALOR-ABONO: &ensp;</label><label class="lblNormal" id="valorAbono"><?php echo $valor ?></label></td>
                        <td>&ensp;</td>
                        <td><label>VALOR-RECIBO: &ensp;</label><label class="lblNormal" id="valorRecibo"><?php echo $valor ?></label></td>
                    </tr>
                    <tr>
                        <td><label>NIT/CED: &ensp;</label><label class="lblNormal" id="nitCed"><?php echo $nitced ?></label></td>
                        <td>&ensp;</td>
                        <td colspan="5"><label>SEÑORES: &ensp;</label><label class="lblNormal" id="senores"><?php echo $descnitced ?></label></td>
                    </tr>
                    <tr>
                        <td><label>BENEFICIARIO: &ensp;</label><label class="lblNormal" id="beneficiario"><?php echo $nitced?></label></td>
                        <td>&ensp;</td>
                        <td colspan="5"><label>NOMBRE: &ensp;</label><label class="lblNormal" id="nomBeneficiario"><?php echo $descnitced ?></label></td>
                    </tr>
                    <tr>
                        <td><label>DEST. ESPEC: &ensp;</label><label class="lblNormal" id="destinatario"><?php echo $destespec ?></label></td>
                        <td>&ensp;</td>
                        <td><label>CONTRATO: &ensp;</label><label class="lblNormal" id="contrato"></label></td>
                        <td>&ensp;</td>
                        <td colspan="3"><label>CAJA No: &ensp;</label><label class="lblNormal" id="caja"><?php echo $caja ?></label></td>
                    </tr>
                    <tr>
                        <td colspan="7"><label>OBSERVACIONES: &ensp;</label><label class="lblNormal" id="observacion"><?php echo $observacion ?></label></td>
                    </tr>
                </table>
            </div>

            <hr><label class="lblNormal">&ensp;NO SE DEFINIO DETALLE DE CONCEPTOS CONTABLES</label>

            <div id="divDatosEspecificos" class="divDatosEspecificos">
                <div align="center">
                    <label class="lblNormal">Detalle de Pacientes Beneficiarios del Recibo:</label>
                </div>

                <table class="tblDatosEspecificos">
                    <thead>
                        <tr>
                            <td><label>Cedula</label></td>
                            <td><label>Nombre</label></td>
                            <td><label>1er. Apellido</label></td>
                            <td><label>2do. Apellido</label></td>
                            <td><label>Valor</label></td>
                            <td><label>Tr</label></td>
                        </tr>
                    </thead>
                    <?php
                    if(is_null($dato_6))
                    {
                        $apellido2 = 'NO DATO';
                    }
                    else
                    {
                        /*
                        while(odbc_fetch_row($dato_6))
                        {
                            $apellido2 = odbc_result($dato_6,0);
                        }
                        */
                    }

                    while (odbc_fetch_row($dato_4))
                    {
                        $cedulaPac = odbc_result($dato_4,1);    if(is_null($cedulaPac)){$cedulaPac = 0;}
                        $nombrePac = odbc_result($dato_4,2);
                        $ape1Pac = odbc_result($dato_4,3);      //$ape2Pac = odbc_result($dato_4,4);
                        $valPac = odbc_result($dato_4,4);       $valorPac = number_format($valPac,0);
                        /*
                        if($datoR_6 == false)
                        {
                            $registros = $datoR_6;
                        }
                        /*
                        elseif($datoR_6 === true)
                        {
                            $registros = 'TRUE';
                        }
                        */
                        ?>
                        <tbody>
                            <tr>
                                <td><label class="lblNormal" id="cedulaPac"><?php echo $cedulaPac ?></label></td>
                                <td><label class="lblNormal" id="nombrePac"><?php echo $nombrePac ?></label></td>
                                <td><label class="lblNormal" id="ape1Pac"><?php echo $ape1Pac ?></label></td>
                                <td><label class="lblNormal" id="ape2Pac"><?php echo /*$ape2Pac*/ $apellido2 ?></label></td>
                                <td><label class="lblNormal" id="valorPac"><?php echo $valorPac ?></label></td>
                            </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>

                <table class="tblDetalle" border="0" align="left" style="margin-bottom: 10px">
                    <thead>
                        <tr>
                            <td><label>F-RE</label></td>
                            <td><label>BANCO</label></td>
                            <td><label>PLA</label></td>
                            <td><label>POB</label></td>
                            <td><label>NUM-DOC</label></td>
                            <td><label>CUE-BANC.</label></td>
                            <td><label>VALOR</label></td>
                            <td><label>BCO-CON</label></td>
                        </tr>
                    </thead>
                    <?php
                    $valorTotal = 0;

                    while(odbc_fetch_row($dato_5))
                    {
                        $tipoPago = odbc_result($dato_5, 1);// tipo pago
                        if($tipoPago == 99){$tipoPago = 'EF';}
                        if(odbc_result($dato_5, 2) != null) {$banco = odbc_result($dato_5, 2);}
                        if(odbc_result($dato_5, 3) != null) {$pla = odbc_result($dato_5, 3);}
                        if(odbc_result($dato_5, 4) != null) {$pob = odbc_result($dato_5, 4);}
                        if(odbc_result($dato_5, 5) != null) {$numdoc = odbc_result($dato_5, 5);}
                        if(odbc_result($dato_5, 6) != null) {$cuebanc = odbc_result($dato_5, 6);}
                        if(odbc_result($dato_5, 7) != null) {$valordet = odbc_result($dato_5, 7);} //valor
                        if(odbc_result($dato_5, 8) != null) {$bcocon = odbc_result($dato_5,8);}
                        $valDet = number_format($valordet,0);

                        $valorTotal = $valorTotal + $valordet;
                        ?>
                        <tbody>
                        <tr>
                            <td class="tdDetalle" width="40"><label class="lblNormal" id="tipoPago"><?php echo $tipoPago ?></label></td>
                            <td class="tdDetalle" width="60"><label class="lblNormal" id="banco"><?php echo $banco ?></label></td>
                            <td class="tdDetalle" width="40"><label class="lblNormal" id="pla"><?php echo $pla ?></label></td>
                            <td class="tdDetalle" width="40"><label class="lblNormal" id="pob"><?php echo $pob ?></label></td>
                            <td class="tdDetalle" width="80"><label class="lblNormal" id="numdoc"><?php echo $numdoc ?></label></td>
                            <td class="tdDetalle" width="90"><label class="lblNormal" id="cuebanc"><?php echo $cuebanc ?></label></td>
                            <td class="tdDetalle" width="90" style="text-align: right"><label class="lblNormal" id="valdet"><?php echo $valDet ?>&ensp;</label></td>
                            <td class="tdDetalle" width="80"><label class="lblNormal" id="bcocon"><?php echo $bcocon ?></label></td>
                        </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                </table>

                <table class="tblDetalle2" border="0" align="right">
                    <tr>
                        <td><label>&ensp;TOTAL CONCEPTOS :</label></td>
                        <td>&ensp;&ensp;0</td>
                    </tr>
                    <tr>
                        <td><label>&ensp;TOTAL FORMAS DE PAGO :</label></td>
                        <td>&ensp;
                            <?php
                            $valorFinal = $valorTotal;
                            $valFinal = number_format($valorFinal);
                            ?>
                            <label class="lblNormal" id="valFinal"><?php echo $valFinal ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td><label>&ensp;CAMBIO: </label></td>
                        <td>&ensp;&ensp;0</td>
                    </tr>
                </table>
            </div>

            <div id="divPiepagina" class="divPiepagina">
                <table>
                    <tr>
                        <td>
                            <label class="lblNormal">&ensp;El asterisco (*) en la columna Tr informa que el beneficiario fue registrado por un traslado</label>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <label class="lblNormal">Fecha y hora de impresion: <?php echo date('Y-m-d / H:m') ?> --- Usuario: <?php echo $wuse ?>&ensp;</label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
if($valFinal != null)
{
    ?>
    <div class="divPrint">
        <table>
            <tr>
                <td>
                    <input type="hidden" name="fuente" id="fuente" value="<?php echo $fte ?>">
                    <input type="hidden" name="factura" id="factura" value="<?php echo $fac ?>">
                    <input type="image" id="imprimir" title="IMPRIMIR" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25" style="margin-left: 600px"
                           onclick="imprimir();return false">
                </td>
            </tr>
        </table>
    </div>
    <?php
}
?>
</body>
</html>
