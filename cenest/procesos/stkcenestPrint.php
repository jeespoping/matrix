<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - Gestion de Stickers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="StyleStkCenest.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <script src="JsStkCenest.js" type="text/javascript"></script>
    <?php
    include_once("conex.php");
    $idEtiqueta = $_GET['idEtiqueta'];          $nombre = $_GET['etq1Nombre'];              $cirujano = $_GET['etq1Cirujano'];
    $casa = $_GET['etq1Casa'];                  $sistema = $_GET['etq1NomSistema'];         $cajas = $_GET['etq1NumCajas'];
    $lote = $_GET['etq1Lote'];                  $fechaV = $_GET['etq1Fv'];                  $metodo = $_GET['etq1Metodo'];
    $responsable = $_GET['etq1Responsable'];    $fechaP = $_GET['etq1Fp'];                  $horaP = $_GET['etq1Hora'];

    $unidad = $_GET['etq2Unidad'];              $equipo = $_GET['etq2Equipo'];              $metodo2 = $_GET['etq2Metodo'];
    $fechaV2 = $_GET['etq2Fv'];                 $lote2 = $_GET['etq2Lote'];                 $responsable2 = $_GET['etq2Responsable'];
    $fechaP2 = $_GET['etq2Fp'];                 $horaP2 = $_GET['etq2Hora'];                $reproceso2 = $_GET['etq2NReproceso'];
    $codigo = $_GET['etq2Codigo'];

    $detergente = $_GET['etq3Detergente'];      $fechaV3 = $_GET['etq3Fv'];                 $fechaP3 = $_GET['etq3Fp'];
    $horaP3 = $_GET['etq3Hora'];                $responsable3 = $_GET['etq3Responsable'];

    $desinfeccion = $_GET['etq4Desinfeccion'];  $unidad4 = $_GET['etq4Unidad'];             $etq4Equipo = $_GET['etq4Equipo'];
    $fechaV4 = $_GET['etq4Fv'];                 $fechaP4 = $_GET['etq4Fp'];                 $horaP4 = $_GET['etq4Hora'];
    $responsable4 = $_GET['etq4Responsable'];   $reproceso4 = $_GET['etq4NReproceso'];
    ?>
</head>

<body class="stkBody" onload="printStiker()">
<?php
if($idEtiqueta == 1)
{
    ?>
    <div class="divEtq1Print" align="left">
        <table align="center" border="0">
            <tr>
                <td colspan="5"><label for="etq1Titulo">MATERIAL DE OSTEOSINTESIS</label></td>
                <td>&ensp;</td>
                <td colspan="2" rowspan="3" align="right">
                    <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="100" height="40" style="margin-right: 40px">
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <label for="etq1Nombre">NOMBRE: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $nombre ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <label for="etq1Cirujano">CIRUJANO: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $cirujano ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <label for="etq1Casa">CASA COMERCIAL: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $casa ?></label></td>
            </tr>
            <tr>
                <td colspan="7">
                    <label for="etq1NomSistema">NOMBRE SISTEMA: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $sistema ?></label></td>
            </tr>
            <tr>
                <td colspan="5">
                    <label for="etq1NumCajas">NUMERO DE CAJAS: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $cajas ?></label>
                </td>
                <td>&ensp;</td>
                <td colspan="3">
                    <label for="etq1Lote">LOTE: </label>
                    &ensp;
                    <label class="lblContenido" style="font-weight: normal; font-size: 15px"><?php echo $lote ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="etq1Fp">FP: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $fechaP ?></label>
                </td>
                <td>&ensp;</td>
                <td colspan="2">
                    <label for="etq1Hora">HORA P: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $horaP ?></label>
                </td>
                <td>&ensp;</td>
                <td colspan="2">
                    <label for="etq1Fv">F.V: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $fechaV ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <label for="etq1Metodo">METODO: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $metodo ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <label for="etq1Responsable">RESPONSABLE: </label>
                    &ensp;
                    <label class="lblContenido"><?php echo $responsable ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7" class="lblLeyenda">
                    <label style="margin-top: 6px">MANTENGASE EN LUGAR FRESCO Y SECO<br>
                        TEMPERATURA: 17°C - 25°C<br>
                        NO USAR SI EL EMPAQUE SE ENCUENTRA DETERIORADO.
                    </label>
                </td>
            </tr>
        </table>
    </div>
    <?php
}

if($idEtiqueta == 2)
{
    ?>
    <div class="divEtq2Print" align="left">
        <table align="center" border="0">
            <tr>
                <td colspan="6"><label for="etq1Titulo" style="font-size: 12px">MATERIAL QUIRURGICO Y HOSPITALARIO</label></td>

                <td colspan="2" rowspan="3" align="right">
                    <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="100" height="40" style="margin-right: 40px">
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <label for="etq2Unidad">UNIDAD: </label>&ensp;
                    <label class="lblContenido"><?php echo $unidad ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <label for="etq2Equipo">EQUIPO: </label>&ensp;
                    <label class="lblContenido" style="font-weight: normal; font-size: 15px"><?php echo $equipo ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <label for="etq2Codigo">CODIGO: </label>&ensp;
                    <label class="lblContenido" style="font-weight: normal; font-size: 15px"><?php echo $codigo ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <label for="etq2Metodo">METODO: </label>&ensp;
                    <label class="lblContenido"><?php echo  $metodo2 ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="etq2Fp">FP: </label>&ensp;
                    <label class="lblContenido"><?php echo $fechaP2 ?></label>
                </td>
                <td>&ensp;</td>
                <td colspan="2">
                    <label for="etq2Hora">HORA P: </label>&ensp;
                    <label class="lblContenido"><?php echo $horaP2 ?></label>
                </td>
                <td>&ensp;</td>
                <td colspan="2">
                    <label for="etq2Fv">F.V: </label>&ensp;
                    <label class="lblContenido"><?php echo $fechaV2 ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <label for="etq2Lote">LOTE: </label>&ensp;
                    <label class="lblContenido" style="font-weight: normal; font-size: 15px"><?php echo $lote2 ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <label for="etq2Responsable">RESPONSABLE: </label>&ensp;
                    <label class="lblContenido"><?php echo $responsable2 ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <label for="etq2NReproceso"># EQUIPO: </label>&ensp;
                    <label class="lblContenido"><?php echo $reproceso2 ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="7" class="lblLeyenda">
                    <label style="margin-top: 6px">MANTENGASE EN LUGAR FRESCO Y SECO - TEMPERATURA: 17°C - 25°C<br>
                        NO USAR SI EL EMPAQUE SE ENCUENTRA DETERIORADO.
                    </label>
                </td>
            </tr>
            <tr><td colspan="7"></td></tr>
        </table>
    </div>
    <?php
}

if($idEtiqueta == 3)
{
    ?>
    <div class="divEtq3Print" align="left">
        <table align="center" border="0">
            <tr>
                <td colspan="2">
                    <label for="etq3Detergente">DETERGENTE: </label>&ensp;
                    <label class="lblContenido">ENDOZYNE</label>
                </td>
                <td>&ensp;</td>
                <td>&ensp;</td>
                <td colspan="2" rowspan="3">
                    <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="110" height="50" style="margin-right: 30px">
                </td>
            </tr>
            <tr>
                <td>
                    <label for="etq3Fp">FP: </label>&ensp;
                    <label class="lblContenido"><?php echo $fechaP3 ?></label>
                </td>
                <td>&ensp;</td>
                <td>&ensp;</td>
                <td>&ensp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="etq3Hora">HORA P: </label>&ensp;
                    <label class="lblContenido"><?php echo $horaP3 ?></label>
                </td>
                <td>&ensp;</td>
                <td>&ensp;</td>
                <td>&ensp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="etq3Fv">F.V: </label>&ensp;
                    <label class="lblContenido"><?php echo $fechaV3 ?></label>
                </td>
                <td>&ensp;</td>
                <td>&ensp;</td>
                <td>&ensp;</td>
            </tr>
            <tr>
                <td colspan="5">
                    <label for="etq3Responsable">OPERARIO: </label>&ensp;
                    <label class="lblContenido"><?php echo $responsable3 ?></label>
                </td>
            </tr>
            <tr><td colspan="5">&ensp;</td></tr>
            <tr><td colspan="5">&ensp;</td></tr>
            <tr>
                <td colspan="5" class="lblLeyenda">
                    <label>MANTENGASE EN LUGAR FRESCO Y SECO <br> NO USAR PASADA LA FECHA DE PREPARACION</label>
                </td>
            </tr>
            <tr><td colspan="5"></td></tr>
        </table>
    </div>
    <?php
}

if($idEtiqueta == 4)
{
    ?>
    <div class="divEtq4Print" align="left">
        <table align="center" border="0">
            <tr>
                <td colspan="2">
                    <label for="etq4Desinfeccion">DESINFECCION </label>&ensp;
                    <label class="lblContenido"><?php echo $desinfeccion ?></label>
                </td>
                <td>&ensp;</td>
                <td>&ensp;</td>
                <td colspan="2" rowspan="3">
                    <img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="110" height="50" style="margin-right: 30px">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="etq4Unidad">UNIDAD: </label>&ensp;
                    <label class="lblContenido"><?php echo $unidad4 ?></label>
                </td>
                <td>&ensp;</td>
                <td>&ensp;</td>
                <td>&ensp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="etq4Equipo">EQUIPO: </label>&ensp;
                    <label class="lblContenido"><?php echo $etq4Equipo ?></label>
                </td>
                <td>&ensp;</td>
                <td>&ensp;</td>
                <td>&ensp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="etq4Fv">F.V: </label>&ensp;
                    <label class="lblContenido"><?php echo $fechaV4 ?></label>
                </td>
                <td>&ensp;</td>
                <td>&ensp;</td>
                <td>&ensp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="etq4Fp">FP: </label>&ensp;
                    <label class="lblContenido"><?php echo $fechaP4 ?></label>
                </td>
                <td>&ensp;</td>
                <td><label for="etq4Hora">HORA P: </label></td>
                <td><label class="lblContenido"><?php echo $horaP4 ?></label></td>
            </tr>
            <tr>
                <td colspan="5">
                    <label for="etq4Responsable">OPERARIO: </label>&ensp;
                    <label class="lblContenido"><?php echo $responsable4 ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <label for="etq2NReproceso"># EQUIPO: </label>&ensp;
                    <label class="lblContenido"><?php echo $reproceso4 ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="lblLeyenda">
                    <label>MANTENGASE EN LUGAR FRESCO Y SECO <br> NO USAR PASADA LA FECHA DE DESINFECCION</label>
                </td>
            </tr>
            <tr><td colspan="5"></td></tr>
        </table>
    </div>
    <?php
}
?>
</body>
</html>