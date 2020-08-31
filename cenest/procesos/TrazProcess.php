<?php
include_once("conex.php");        //publicacion Matrix
include_once("root/comun.php");   //publicacion Matrix
//include_once('../MATRIX/include/root/conex.php'); //publicacion local
//include_once('../MATRIX/include/root/comun.php'); //publicacion local
$conex = obtenerConexionBD("matrix");

$fecha_Actual = date('Y-m-d');  $hora_Actual = date('H:m:s');
$accion = $_GET['accion'];
if($accion == null){$accion = $_POST['accion'];}
$idRegistro = $_GET['idRegistro'];  $Coddispo = $_GET['Coddispo'];  $idReuso = $_GET['idReuso'];    if($idReuso == null){$idReuso = $_POST['idReuso'];}
//ACTUALIZAR REGISTRO
$trazUpdate = $_POST['trazUpdate']; $numCalibreUp = $_POST['numCalibre'];   $codItemUp = $_POST['codItem']; $invimaUp = $_POST['invima'];
$limiteUp = $_POST['limite'];       $observacionUp = $_POST['observacion'];
//INSERTAR REUSO
$codReusoIns = $_POST['codReusoIns'];   $numCalIns = $_POST['numCalIns'];           $codItemIns = $_POST['codItemIns'];     $invimaIns = $_POST['invimaIns'];
$numReusoIns = $_POST['numReusoIns'];   $obserReusoIns = $_POST['obserReusoIns'];   $codDispoReu = $_POST['codDispoReu'];
//INSERTAR DISPOSITIVO
$idCco = $_GET['idCco'];
$codDispoInsert = $_POST['codDispoInsert']; $cCostDispoInsert = $_POST['codCcoDispo']; $descDispoInsert = $_POST['descDispoInsert'];
$codCcoDispo = $_GET['codCcoDispo']; if($codCcoDispo == null){$codCcoDispo = $_POST['codCcoDispo'];}
$numCalDispoInsert =
//VER REUSO EN REPORTES
$codReuso13 = $_GET['codReuso13'];
//EXPORTAR A EXCEL
$fecIniReport = $_GET['fecIni'];    $fecFinReport = $_GET['fecFin'];  $cCostoReport = $_GET['codCcoDispo'];
?>

<?php
/*
if($accion == 'exportar')
{
    header('Content-type: application/vnd.ms-excel; charset=UTF-8');
    header("Content-disposition: attachment; filename=pacientes_PAF");
    header('Pragma: no-cache');
    header('Expires: 0');
    ?>
    <!DOCTYPE html>
    <html lang="esp" xmlns="http://www.w3.org/1999/html">
    <head>
        <!--<meta charset="utf-8">-->
        <title>Reporte Pacientes Ingresados - PAF</title>
        <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
    </head>

    <body>
    <h3>EXPORTACION</h3>
    </body>
    </html>
    <?php
}
*/

if($accion == 'excel')
{
    ?>
    <!DOCTYPE html>
    <html lang="esp" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>EXPORTACION REPORTES DE REUSO - MATRIX</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="GrdStyle.css" rel="stylesheet">
        <?php
        include_once("conex.php");        //publicacion Matrix
        include_once("root/comun.php");   //publicacion Matrix
        //include_once('../MATRIX/include/root/conex.php'); //publicacion local
        //include_once('../MATRIX/include/root/comun.php'); //publicacion local
        $conex = obtenerConexionBD("matrix");
        ?>
    </head>

    <body style="overflow: hidden">
    <div style=" height: 100%">
        <form method="post" action="TrazExport.php">
            <table style="width: 800px">
                <tr>
                    <td>
                        <div class="input-group" style="margin-top: 50px; text-align: center; background-color: #DFEFF9">
                            <br>
                            <label for="radTruncate">Va a Generar un Reporte de Reusos Para:</label>
                            <br>
                            &ensp;
                            <?php datosUnidadxCco($cCostoReport,$conex,1) ?> <input type="radio" name="radTruncate" id="radTruncate" value="1" checked>
                            &ensp;&ensp;
                            Todas Las Unidades <input type="radio" name="radTruncate" id="radTruncate" value="0">
                            <br><br>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                            <input type="hidden" id="fecIni" name="fecIni" value="<?php echo $fecIniReport ?>">
                            <input type="hidden" id="fecFin" name="fecFin" value="<?php echo $fecFinReport ?>">
                            <input type="hidden" id="codCcoDispo" name="codCcoDispo" value="<?php echo $cCostoReport ?>">
                            <div class="input-group" style="margin-top: 50px; text-align: center">
                                <input type="submit" class="btn btn-info btn-sm" value="Exportar" title="Exportar Reporte a Excel" style="width: 120px">
                            </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    </body>
    </html>
    <?php
}

if($accion == 'modificar')
{
    ?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ACTUALIZACION DISPOSITIVOS REUSO - MATRIX</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="GrdStyle.css" rel="stylesheet">
        <?php
        include_once("conex.php");        //publicacion Matrix
        include_once("root/comun.php");   //publicacion Matrix
        //include('../MATRIX/include/root/conex.php'); //publicacion local
        //include('../MATRIX/include/root/comun.php'); //publicacion local
        $conex = obtenerConexionBD("matrix");
        ?>
    </head>

    <body style="overflow: hidden">
    <div class="container" style="margin-left: 0">
        <div class="panel panel-info contenido">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1">Matrix - Modificacion de Registros Reuso</div>
            </div>

            <?php
            $query2 = "select * from cenest_000011 WHERE Codigo = '$Coddispo' AND Codcco = '$codCcoDispo'";
            $commit2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
            $datos2 = mysql_fetch_array($commit2);
            $dispoNombre = $datos2['Descripcion'];

            $query3 = "select * from cenest_000012 WHERE id = '$idRegistro' AND Codcco = '$codCcoDispo'";
            $commit3 = mysql_query($query3, $conex) or die (mysql_errno()." - en el query: ".$query3." - ".mysql_error());
            $datos3 = mysql_fetch_array($commit3);
            $Codreuso = $datos3['Codreuso'];    $numCalibre = $datos3['Ncalibre'];      $codItem = $datos3['Coditem'];
            $invima = $datos3['Invima'];        $observacion = $datos3['Observacion'];  $limite = $datos3['limite'];
            ?>
            <h4 style="text-align: center"><?php echo $Codreuso.' - '.$dispoNombre ?></h4>

            <form method="post" action="TrazProcess.php">
                <table align="center">
                    <tr>
                        <td align="left">
                            <div class="input-group selectDispo" style="margin-left: 10px">
                                <span class="input-group-addon input-sm"><label for="numCalibre">NUMERO CALIBRE:</label></span>
                                <input type="text" id="numCalibre" name="numCalibre" class="form-control form-sm" style="width: 80px" value="<?php echo $numCalibre ?>">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 135px"></span>

                                <span class="input-group-addon input-sm"><label for="codItem">&ensp;CODIGO ITEM:</label></span>
                                <input type="text" id="codItem" name="codItem" class="form-control form-sm" style="width: 170px" value="<?php echo $codItem ?>">
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invima">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INVIMA:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></span>
                                <input type="text" id="invima" name="invima" class="form-control form-sm" style="width: 230px" value="<?php echo $invima ?>">

                                <span class="input-group-addon input-sm"><label for="limite">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LIMITE:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></span>
                                <input type="text" id="limite" name="limite" class="form-control form-sm" style="width: 160px" value="<?php echo $limite ?>">
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="observacion">&nbsp;OBSERVACION:&nbsp;</label></span>
                                <input type="text" id="observacion" name="observacion" class="form-control form-sm" style="width: 550px" value="<?php echo $observacion ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" id="accion" name="accion" value="actualizar">
                            <input type="hidden" id="trazUpdate" name="trazUpdate" value="<?php echo $idRegistro ?>">
                            <div class="input-group" style="margin-top: 10px; text-align: center">
                                <input type="submit" class="btn btn-info btn-sm" value="Actualizar" title="Actualizar" style="width: 120px">
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </body>
    </html>
    <?php
}

if($accion == 'actualizar')
{
        include_once("conex.php");        //publicacion Matrix
        include_once("root/comun.php");   //publicacion Matrix
        //include_once('../MATRIX/include/root/conex.php'); //publicacion local
        //include_once('../MATRIX/include/root/comun.php'); //publicacion local
        $conex = obtenerConexionBD("matrix");

    if($trazUpdate != null and $numCalibreUp != null and $codItemUp != null and $invimaUp != null and $limiteUp != null)
    {
        $queryUp12 = "update cenest_000012
                      set Fecha_data = '$fecha_Actual', Hora_data = '$hora_Actual', Ncalibre = '$numCalibreUp', Coditem = '$codItemUp',
                      Invima = '$invimaUp', Observacion = '$observacionUp', limite = '$limiteUp'
                      WHERE id = '$trazUpdate'";
        $commQryUp12 = mysql_query($queryUp12, $conex);

        if($commQryUp12 == true)
        {
            ?>
            <table style="width: 60%">
                <tr>
                    <td align="center">
                        <script>window.close()</script>
                    </td>
                </tr>
            </table>
            <?php
        }
    }
    else
    {
        ?>
        <table style="width: 60%">
            <tr>
                <td align="center">
                    <h4 style="margin-top: 100px; text-align: center">Todos los campos son obligatorios</h4>
                    <button class="btn btn-success btn-sm" onclick="window.close()">Aceptar</button>
                </td>
            </tr>
        </table>
        <?php
    }
}

if($accion == 'insertReuso')
{
    if($codDispoReu != null and $codReusoIns != null and $numCalIns != null and $codItemIns != null and $numReusoIns != null)
    {
        $queryIns12 = "insert into cenest_000012
                        VALUES('cenest','$fecha_Actual','$hora_Actual','$codReusoIns','$numCalIns','$codItemIns','$invimaIns','$obserReusoIns',
                              '0','$codDispoReu','on','$numReusoIns','$codCcoDispo','C-cenest','')";
        $commQryIns12 = mysql_query($queryIns12, $conex);

        $queryUp12 = "update cenest_000012 set Estado = 'off' WHERE id = '$idReuso'";
        mysql_query($queryUp12, $conex);

        if($commQryIns12 == true)
        {
            ?>
            <table style="width: 60%">
                <tr>
                    <td align="center">
                        <script>window.close()</script>
                    </td>
                </tr>
            </table>
            <?php
        }
    }
    else
    {
        ?>
        <table style="width: 60%">
            <tr>
                <td align="center">
                    <h4 style="margin-top: 100px; text-align: center">Todos los campos son obligatorios</h4>
                    <button class="btn btn-success btn-sm" onclick="window.close()">Aceptar</button>
                </td>
            </tr>
        </table>
        <?php
    }
}

if($accion == 'insertarReuso')
{
    ?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ACTUALIZACION DISPOSITIVOS REUSO - MATRIX</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="GrdStyle.css" rel="stylesheet">
        <?php
        include_once("conex.php");        //publicacion Matrix
        include_once("root/comun.php");   //publicacion Matrix
        //include('../MATRIX/include/root/conex.php'); //publicacion local
        //include('../MATRIX/include/root/comun.php'); //publicacion local
        $conex = obtenerConexionBD("matrix");
        ?>
    </head>

    <body style="overflow: hidden">
    <div class="container" style="margin-left: 0">
        <div class="panel panel-info contenido">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1">Matrix - Adicionar Registros de Reuso</div>
            </div>

            <?php
            $query2 = "select * from cenest_000011 WHERE Codigo = '$idRegistro' AND Codcco = '$codCcoDispo'";
            $commit2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
            $datos2 = mysql_fetch_array($commit2);
            $dispoNombre = $datos2['Descripcion'];  $codCcoDisp = $datos2['Codcco'];

            $queryReu = "select Codreuso from cenest_000012 WHERE Coddispo = '$idRegistro' AND id = '$idReuso' AND Codcco = '$codCcoDisp'";
            $commQryReu = mysql_query($queryReu, $conex) or die (mysql_errno()." - en el query: ".$queryReu." - ".mysql_error());
            $datosReu = mysql_fetch_array($commQryReu);
            $codLastReu = $datosReu[0];

            if($codLastReu != null)
            {
                list($parte1,$parte2) = explode(".", $codLastReu); //DIVIDO LA CADENA POR LOS PUNTOS
                $part = explode(".",$codLastReu); //LAS ASIGNO A LA VARIABLE $PART
                $lastReu = end($part);
                $letra = substr($lastReu, -1); //TOMO LA PARTE FINAL / TOMO EL ULTIMO CARACTER Y LO ASIGNO A $LETRA

                if(ctype_alpha($letra))
                {
                    $lastReu = trim($lastReu, $letra); //ELIMINO EL ULTIMO CARACTER
                    $nextChar = chr(ord($letra) + 1);
                    $lastReu = $lastReu.$nextChar;
                }
                else
                {
                    $lastReu = $lastReu.'A';
                }
            }
            elseif($codLastReu == null)
            {
                $queryNewReu = "select Codreuso from cenest_000012 WHERE Coddispo = '$idRegistro' AND Codcco = '$codCcoDisp' ORDER BY Codreuso DESC LIMIT 1";
                $commNewReu = mysql_query($queryNewReu, $conex) or die (mysql_errno()." - en el query: ".$queryNewReu." - ".mysql_error());
                $datosNewReu = mysql_fetch_array($commNewReu);
                $codLastReu = $datosNewReu[0];
                list($parte1,$parte2,$parte3) = explode(".", $codLastReu); //DIVIDO LA CADENA POR LOS PUNTOS
                $lastReu = $parte3;
                $lastReu = $lastReu + 1;
            }
            ?>
            <h4 style="text-align: center"><?php echo $idRegistro.' - '.$dispoNombre ?></h4>

            <form method="post" action="TrazProcess.php">
                <table align="center">
                    <tr>
                        <td align="left">
                            <div class="input-group selectDispo" style="margin-left: 10px">
                                <span class="input-group-addon input-sm"><label for="codReusoIns">CODIGO REUSO:</label></span>
                                <input type="text" id="codReusoIns" name="codReusoIns" class="form-control form-sm" style="width: 230px"
                                       value="<?php echo $parte1.'.'.$parte2.'.'.$lastReu ?>">

                                <span class="input-group-addon input-sm"><label for="codCcoDispo">SERVICIO:</label></span>
                                <input type="text" id="codCcoDispo" name="codCcoDispo" class="form-control form-sm" style="width: 195px" value="<?php echo $codCcoDispo ?>" readonly>
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="numCalIns">NUMERO CALIBRE:</label></span>
                                <input type="text" id="numCalIns" name="numCalIns" class="form-control form-sm" style="width: 80px">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 135px"></span>

                                <span class="input-group-addon input-sm"><label for="codItemIns">&ensp;CODIGO ITEM:</label></span>
                                <input type="text" id="codItemIns" name="codItemIns" class="form-control form-sm" style="width: 170px">
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invimaIns">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;INVIMA:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></span>
                                <input type="text" id="invimaIns" name="invimaIns" class="form-control form-sm" style="width: 230px">

                                <span class="input-group-addon input-sm"><label for="numReusoIns">&nbsp;LIMITE REUSO:&nbsp;</label></span>
                                <input type="text" id="numReusoIns" name="numReusoIns" class="form-control form-sm" style="width: 160px">
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="obserReusoIns">&nbsp;OBSERVACION:&nbsp;</label></span>
                                <input type="text" id="obserReusoIns" name="obserReusoIns" class="form-control form-sm" style="width: 550px">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" id="accion" name="accion" value="insertReuso">
                            <input type="hidden" id="codDispoReu" name="codDispoReu" value="<?php echo $idRegistro ?>">
                            <input type="hidden" id="idReuso" name="idReuso" value="<?php echo $idReuso ?>">
                            <?php
                            if($idReuso != 'undefined')
                            {
                                ?>
                                <div class="input-group" style="margin-top: 10px; text-align: center">
                                    <input type="submit" class="btn btn-info btn-sm" value="Actualizar" title="Actualizar" style="width: 120px">
                                </div>
                                <?php
                            }
                            else
                            {
                                ?>
                                <div class="input-group" style="margin-top: 10px; text-align: center">
                                    <input type="submit" class="btn btn-info btn-sm" value="Insertar" title="Registrar Nuevo Reuso" style="width: 120px">
                                </div>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </body>
    </html>
    <?php
}

if($accion == 'insertDispo')
{
    if($codDispoInsert != null and $cCostDispoInsert != null and $descDispoInsert != null)
    {
        $queryInsDispo = "insert into cenest_000011
                        VALUES('cenest','$fecha_Actual','$hora_Actual','$codDispoInsert','$descDispoInsert','$cCostDispoInsert','on','C-cenest','')";
        $commQryInsDispo = mysql_query($queryInsDispo, $conex);

        if($commQryInsDispo == true)
        {
            ?>
            <table style="width: 60%">
                <tr>
                    <td align="center">
                        <script>opener.document.frmDispositivo.submit(true);</script>
                        <script>window.close()</script>
                    </td>
                </tr>
            </table>
            <?php
        }
    }
    else
    {
        ?>
        <table style="width: 60%">
            <tr>
                <td align="center">
                    <h4 style="margin-top: 100px; text-align: center">Todos los campos son obligatorios</h4>
                    <button class="btn btn-success btn-sm" onclick="window.close()">Aceptar</button>
                </td>
            </tr>
        </table>
        <?php
    }
}

if($accion == 'insertarDispo')
{
    include_once("conex.php");        //publicacion Matrix
    include_once("root/comun.php");   //publicacion Matrix
    //include('../MATRIX/include/root/conex.php'); //publicacion local
    //include('../MATRIX/include/root/comun.php'); //publicacion local
    $conex = obtenerConexionBD("matrix");

    $queryDispo = "select * from cenest_000011 WHERE Codcco = '$idCco' ORDER BY Codigo DESC LIMIT 1";
    $commitDispo = mysql_query($queryDispo, $conex) or die (mysql_errno()." - en el query: ".$queryDispo." - ".mysql_error());
    $datosDispo = mysql_fetch_array($commitDispo);
    $lastIdDispo = $datosDispo['Codigo'];
    $idDispo = $lastIdDispo + 1;
    ?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ACTUALIZACION DISPOSITIVOS REUSO - MATRIX</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="GrdStyle.css" rel="stylesheet">
    </head>

    <body style="overflow: hidden">
    <div class="container" style="margin-left: 0">
        <div class="panel panel-info contenido">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1">Matrix - Agregar Nuevo Dispositivo</div>
            </div>

            <form method="post" action="TrazProcess.php">
                <table align="center">
                    <tr>
                        <td align="left">
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="codDispoInsert">CODIGO DISPOSITIVO:</label></span>
                                <input type="text" id="codDispoInsert" name="codDispoInsert" class="form-control form-sm" style="width: 80px" value="<?php echo $idDispo ?>" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="cCostDispoInsert">&ensp;C. COSTOS:</label></span>
                                <input type="text" id="cCostDispoInsert" name="cCostDispoInsert" class="form-control form-sm" style="width: 300px" value="<?php datosUnidadxCco($idCco,$conex,1) ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="descDispoInsert">DESCRIPCION:</label></span>
                                <input type="text" id="descDispoInsert" name="descDispoInsert" class="form-control form-sm" style="width: 570px">
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="numCalDispoInsert">NUMERO CALIBRE:</label></span>
                                <input type="text" id="numCalDispoInsert" name="numCalDispoInsert" class="form-control form-sm" style="width: 80px">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="codDispoInsert">&ensp;CODIGO ITEM:</label></span>
                                <input type="text" id="codDispoInsert" name="codDispoInsert" class="form-control form-sm" style="width: 300px">
                            </div>

                            <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="invDispoInsert">INVIMA:</label></span>
                                <input type="text" id="invDispoInsert" name="invDispoInsert" class="form-control form-sm" style="width: 80px">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="limReuDispoInsert">&ensp;LIMITE REUSO:</label></span>
                                <input type="text" id="limReuDispoInsert" name="limReuDispoInsert" class="form-control form-sm" style="width: 300px">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" id="accion" name="accion" value="insertDispo">
                            <input type="hidden" id="codCcoDispo" name="codCcoDispo" value="<?php echo $idCco ?>">
                            <div class="input-group" style="margin-top: 10px; text-align: center">
                                <input type="submit" class="btn btn-info btn-sm" value="Insertar" title="Insertar Dispositivo" style="width: 120px">
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </body>
    </html>
    <?php
}

if($accion == 'gestionar')
{
    echo 'GESTIONAR REUSO N: '.$idRegistro;
}

if($accion == 'verReuso')
{
    include_once("conex.php");        //publicacion Matrix
    include_once("root/comun.php");   //publicacion Matrix
    //include('../MATRIX/include/root/conex.php'); //publicacion local
    //include('../MATRIX/include/root/comun.php'); //publicacion local
    $conex = obtenerConexionBD("matrix");

    $qryDatDispo = "select * from cenest_000012 WHERE Codreuso = '$codReuso13'";
    $commQryDispo = mysql_query($qryDatDispo, $conex) or die (mysql_errno()." - en el query: ".$qryDatDispo." - ".mysql_error());
    $datDispo = mysql_fetch_array($commQryDispo);
    $calibreDispo = $datDispo['Ncalibre'];  $invimaDispo = $datDispo['Invima']; $nusosDispo = $datDispo['Numuso'];

    $queryVerReu = "select * from cenest_000013 WHERE id = '$idRegistro'";
    $commitVerReu = mysql_query($queryVerReu, $conex) or die (mysql_errno()." - en el query: ".$queryVerReu." - ".mysql_error());
    $datosVerReu = mysql_fetch_array($commitVerReu);
    $fecUsoReu = $datosVerReu['Fechauso'];          $nomUsReu = $datosVerReu['Nomusuario'];     $docUsReu = $datosVerReu['Docusuario'];
    $numQuiReu = $datosVerReu['NumQuirofano'];      $obsReu = $datosVerReu['Observacion'];      $usrEntEsteril = $datosVerReu['UserServicio'];
    $usrRecEsteril = $datosVerReu['UserEsteril'];   $fecEsteril = $datosVerReu['FechaEsteril']; $equiEsteril = $datosVerReu['EquipoEsteril'];
    $metEsteril = $datosVerReu['MetodoEsteril'];    $cicEsteril = $datosVerReu['CicloEsteril']; $respEsteril = $datosVerReu['RespEsteril'];
    $respDiligen = $datosVerReu['RespDiligen'];
    ?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ACTUALIZACION DISPOSITIVOS REUSO - MATRIX</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <link href="GrdStyle.css" rel="stylesheet">
    </head>

    <body style="overflow: hidden">
    <div class="container" style="margin-left: 0">
        <div class="panel panel-info contenido">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1" style="text-align: center">Matrix - Datos de Reuso</div>
            </div>

            <form method="post" action="TrazProcess.php">
                <table align="center">
                    <tr>
                        <td align="left">
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="codReuso">CODIGO REUSO:</label></span>
                                <input type="text" id="codReuso" class="form-control form-sm" style="width: 178px; background-color: white" value="<?php echo $codReuso13 ?>" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="calReuso">&ensp;CALIBRE:</label></span>
                                <input type="text" id="calReuso" class="form-control form-sm" style="width: 80px; background-color: white" value="<?php echo $calibreDispo ?>" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="nusosReuso">NUMERO USOS:</label></span>
                                <input type="text" id="nusosReuso" class="form-control form-sm" style="width: 60px; background-color: white" value="<?php echo $nusosDispo ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invReuso">INVIMA:&ensp;&ensp;&ensp;&ensp;</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 312px; background-color: white" value="<?php echo $invimaDispo ?>" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="nusosReuso">FECHA UTILIZACION:</label></span>
                                <input type="text" id="nusosReuso" class="form-control form-sm" style="width: 115px; background-color: white" value="<?php echo $fecUsoReu ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="invReuso">NOMBRE DE USUARIO:&ensp;&ensp;&ensp;&ensp;&ensp;</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 498px; background-color: white" value="<?php echo $nomUsReu ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invReuso">NUMERO IDENTIFICACION:</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 234px; background-color: white" value="<?php echo $docUsReu ?>" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="nusosReuso">NUMERO QUIROFANO:</label></span>
                                <input type="text" id="nusosReuso" class="form-control form-sm" style="width: 100px; background-color: white" value="<?php echo $numQuiReu ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="invReuso">OBSERVACIONES:</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 560px; background-color: white" value="<?php echo $obsReu ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invReuso">FUNCIONARIO QUE ENTREGA A ESTERILIZACION:</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 384px; background-color: white" value="<?php echo $usrEntEsteril ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="invReuso">FUNCIONARIO QUE RECIBE EN ESTERILIZACION:&ensp;</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 383px; background-color: white" value="<?php echo $usrRecEsteril ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invReuso">FECHA ESTERILIZACION:</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 230px; background-color: white" value="<?php echo $fecEsteril ?>" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="nusosReuso">EQUIPO ESTERILIZADOR:</label></span>
                                <input type="text" id="nusosReuso" class="form-control form-sm" style="width: 98px; background-color: white" value="<?php echo $equiEsteril ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 5px">
                                <span class="input-group-addon input-sm"><label for="invReuso">METODO ESTERILIZACION:</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 216px; background-color: white" value="<?php echo $metEsteril ?>" readonly>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 5px"></span>

                                <span class="input-group-addon input-sm"><label for="nusosReuso">CICLO DE ESTERILIZACION:</label></span>
                                <input type="text" id="nusosReuso" class="form-control form-sm" style="width: 80px; background-color: white" value="<?php echo $cicEsteril ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="invReuso">RESPONSABLE DE ESTERILIZACION:</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 449px; background-color: white" value="<?php datosUsrMtx($respEsteril,$conex) ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: -5px; margin-top: 10px">
                                <span class="input-group-addon input-sm"><label for="invReuso">RESPONSABLE DILIGENCIAMIENTO:</label></span>
                                <input type="text" id="invReuso" class="form-control form-sm" style="width: 453px; background-color: white" value="<?php datosUsrMtx($respDiligen,$conex) ?>" readonly>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    </body>
    </html>
    <?php
}
?>

<?php
/////////////// FUNCIONES: ////////////////

function datosUnidadxCco($ccoUnidad,$conex,$parametro)
{
    if($ccoUnidad == '10162')
    {
        echo 'CIRUGIA CARDIO';
    }
    else
    {
        $query1 = "select * from movhos_000011 WHERE Ccocod = '$ccoUnidad'";
        $commitQuery1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
        $datoUnidad = mysql_fetch_array($commitQuery1);
        $nombreUnidad = $datoUnidad[4];

        switch($parametro)
        {
            case 1: echo $nombreUnidad; break;
        }
    }
}

function dispoxCco($ccoUnidad,$conex)
{
    $query2 = "select * from cenest_000011 WHERE Codcco = '$ccoUnidad' AND Estado = 'on'";
    $commitQuery2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
    $datoDispo = mysql_fetch_array($commitQuery2);
}

function datosUsrMtx($respEsteril,$conex)
{
    $query22 = "select * from usuarios WHERE Codigo = '$respEsteril'";
    $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno()." - en el query: ".$query22." - ".mysql_error());
    $datoUsuario = mysql_fetch_array($commitQuery22);
    $nomUsrMtx = $datoUsuario['Descripcion'];
    echo $nomUsrMtx;
}
?>