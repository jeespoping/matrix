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
        $wactualiz = "2021-11-16";
    }
    session_start();

    $fteSend = $_POST['fte'];   $docSend = $_POST['fac'];
    //$usuarioF = $wuse;
    $fecha_actual=date("Y-m-d");    $hora_actual = date('H:i:s')
    ?>
</head>

<body>
<div id="divGeneral" class="divGeneral">
    <!-- TITULO: -->
    <div class="divHeader">
        <table align="center">
            <tr style="background-color: #C3D9FF">
                <td style="padding: 10px">
                    <font style='font-size: x-large; font-weight: bold'>IMPRESION RECIBOS DE CUENTAS POR COBRAR</font>
                </td>
            </tr>
            <tr align="right" style="background-color: #E8EEF7">
                <td>
                    <?php echo $wactualiz ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- BUSCADOR: -->
    <div id="divSelector" class="divSelector">
        <form class="form-horizontal" role="form" name="frmSelector" action="recIngreso_01.php?wemp_pmla=<?php echo $_GET["wemp_pmla"] ?>" method="post">
            <table align="center">
                <tr>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><label for="fte">FUENTE</label></span>
                            <input id="fte" name="fte" type="text" class="form-control" style="width: 150px" value="<?php echo $fteSend ?>">
                        </div>
                    </td>
                    <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><label for="fac">DOCUMENTO</label></span>
                            <input id="fac" name="fac" type="text" class="form-control" style="width: 150px" value="<?php echo $docSend ?>">
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
    //SI SE PRESIONÓ IR:
    if($docSend != null)
    {
        $fte = $_POST['fte'];   $fac = $_POST['fac'];

        //SI HAY DATOS EN LOS CAMPOS FUENTE Y DOCUMENTO:
        if($fte != null and $fac != null)
        {
            $qryCount1 = "SELECT count(*) FROM cacar WHERE carfue = '$fte' AND cardoc = '$fac' AND caranu = '0' AND carval <> 0";
            $datoCount1 = odbc_do($conex_o,$qryCount1);
            $cont = odbc_result($datoCount1,1);

            //SI LOS DATOS SON VÁLIDOS (EXISTENTES EN CACAR):
            if($cont > 0)
            {
                if($fte == '30')
                {
                    $query_1 = "SELECT * FROM cacar WHERE carfue = '$fte' AND cardoc = '$fac' AND caranu = '0' AND carval <> 0";
                    $dato_1 = odbc_do($conex_o,$query_1);

                    $query_2 = "SELECT * FROM cbmov WHERE movfue = '$fte' AND movdoc = '$fac'";
                    $dato_2 = odbc_do($conex_o,$query_2);
                    $movval = odbc_result($dato_2,14);  $carfec = odbc_result($dato_2, 5);// fecha
                    $nitced = odbc_result($dato_2,10);  $descnitced = odbc_result($dato_2,11);
                    $movval2 = $movval;

                    $qryTrunc_1 = "truncate table equipos_000021";
                    $comTrunc_1 = mysql_query($qryTrunc_1, $conex) or die (mysql_errno()." - en el query: ".$qryTrunc_1." - ".mysql_error());

                    while(odbc_fetch_row($dato_1))
                    {
                        $carfue = odbc_result($dato_1, 1);  $cardoc = odbc_result($dato_1, 2);  $carfca = odbc_result($dato_1, 18);
                        $carfac = odbc_result($dato_1, 19); $carval = odbc_result($dato_1, 21); $carcco = odbc_result($dato_1, 6);

                        $qryInsert_1 = "insert into equipos_000021
                                        VALUES('Equipos','$fecha_actual','$hora_actual','$carfue','$cardoc','$carfca','$carfac','$carval','$carcco','C-$wuse','')";
                        $commit_1 = mysql_query($qryInsert_1, $conex) or die (mysql_errno()." - en el query: ".$qryInsert_1." - ".mysql_error());

                    }

                    $queryEmpresa = "SELECT * FROM inemp WHERE empnit = '$nitced'";
                    $commEmpresa = odbc_do($conex_o,$queryEmpresa);
                    $empCod = odbc_result($commEmpresa, 1); $empNom = odbc_result($commEmpresa, 3);
                }

                $wemp_pmla = $_REQUEST['wemp_pmla'];
                $wbasedato1 = consultarInstitucionPorCodigo($conex, $wemp_pmla);
                $wnit = $wbasedato1->nit;
                $wnombre = $wbasedato1->nombre;

                ?>
                <div id="divContenido" class="divContenido">
                    <div id="divEncabezado" class="divEncabezado" align="center">
                        <div>
                            <span><label><?php $wnombre; ?></label></span>
                            <br>
                            <span><label>Nit. <?php echo $wnit; ?></label></span>
                        </div>
                        <div align="right" style="width: 145px; float: right; margin-top: -50px; margin-right: 55px">
                            <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="190" height="120">
                        </div>
                    </div>

                    <!-- INFORMACION ENCABEZADO -->
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
                            <label class="lblNormal" style="text-align: center; font-weight: bold">DETALLE DE FACTURAS:</label>
                        </div>
                    </div>

                    <!-- DETALLE DE FACTURAS -->
                    <div id="divDatosEspecificos" class="divDatosEspecificos">
                        <?php
                        if($cont > 0){mostrarData($fte,$fac,$conex,$conex_o,0);}

                        if($cont > 51){mostrarData($fte,$fac,$conex,$conex_o,51);}

                        if($cont > 101){mostrarData($fte,$fac,$conex,$conex_o,101);}

                        if($cont > 151){mostrarData($fte,$fac,$conex,$conex_o,151);}
                        ?>
                    </div>

                    <div id="divPiepagina" class="divPiepagina">
                        <?php
                        detalleConceptos($fte,$fac,$conex_o,200);
                        mostrarPie($fte,$fac,$conex_o)
                        ?>
                    </div>
                </div>

                <div align="center" style="margin-top: 5px">
                    <label style="text-align: center">Pagina 1</label>
                </div>
                <?php
            }
            else
            {
                ?>
                <h4 align="center">No se encontraron registros con los datos ingresados</h4>
                <?php
            }
        }
        else
        {
            ?>
            <h4 align="center">Fuente y Documento son requeridos</h4>
            <?php
        }
    }
    else
    {
        ?>
        <h4 align="center">Ingrese Fuente y Documento...</h4>
        <?php
    }
    ?>
</div>

<?php
//PAGINA 2
if($cont > 201)
{
    ?>
    <div id="divGeneral" class="divGeneral">
        <div id="divContenido" class="divContenido">
            <?php
            mostrarData($fte,$fac,$conex,$conex_o,201);

            if($cont > 251){mostrarData($fte,$fac,$conex,$conex_o,251);}

            if($cont > 301){mostrarData($fte,$fac,$conex,$conex_o,301);}

            if($cont > 351){mostrarData($fte,$fac,$conex,$conex_o,351);}
            ?>

            <div id="divPiepagina" class="divPiepagina">
                <?php
                detalleConceptos($fte,$fac,$conex_o,400);
                mostrarPie($fte,$fac,$conex_o);
                ?>
            </div>
        </div>

        <div align="center" style="margin-top: 5px">
            <label style="text-align: center">Pagina 2</label>
        </div>
    </div>
    <?php
}

//PAGINA 3
if($cont > 401)
{
    ?>
    <div id="divGeneral" class="divGeneral">
        <div id="divContenido" class="divContenido">
            <?php
            mostrarData($fte,$fac,$conex,$conex_o,401);

            if($cont > 451){mostrarData($fte,$fac,$conex,$conex_o,451);}

            if($cont > 501){mostrarData($fte,$fac,$conex,$conex_o,501);}

            if($cont > 551){mostrarData($fte,$fac,$conex,$conex_o,551);}
            ?>

            <div id="divPiepagina" class="divPiepagina">
                <?php
                detalleConceptos($fte,$fac,$conex_o,600);
                mostrarPie($fte,$fac,$conex_o);
                ?>
            </div>
        </div>

        <div align="center" style="margin-top: 5px">
            <label style="text-align: center">Pagina 3</label>
        </div>
    </div>
    <?php
}

//PAGINA 4
if($cont > 601)
{
    ?>
    <div id="divGeneral" class="divGeneral">
        <div id="divContenido" class="divContenido">
            <?php
            mostrarData($fte,$fac,$conex,$conex_o,601);

            if($cont > 651){mostrarData($fte,$fac,$conex,$conex_o,651);}

            if($cont > 701){mostrarData($fte,$fac,$conex,$conex_o,701);}

            if($cont > 751){mostrarData($fte,$fac,$conex,$conex_o,751);}
            ?>

            <div id="divPiepagina" class="divPiepagina">
                <?php
                detalleConceptos($fte,$fac,$conex_o,800);
                mostrarPie($fte,$fac,$conex_o)
                ?>
            </div>
        </div>

        <div align="center" style="margin-top: 5px">
            <label style="text-align: center">Pagina 4</label>
        </div>
    </div>
    <?php
}

//////////IMPRESION:
if($cont > 0)
{
    ?>
    <div class="divPrint">
        <table>
            <tr>
                <td>
                    <input type="hidden" name="fuente" id="fuente" value="<?php echo $fte ?>">
                    <input type="hidden" name="factura" id="factura" value="<?php echo $fac ?>">
                    <input type="image" id="imprimir" title="IMPRIMIR" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25" style="margin-left: 630px"
                           onclick="imprimir(fuente,factura);return false">
                </td>
            </tr>
        </table>
    </div>
    <?php
}
?>
</body>
</html>