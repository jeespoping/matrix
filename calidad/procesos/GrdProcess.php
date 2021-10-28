<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ACTUALIZACION ALTA TEMPRANA GRD - MATRIX</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="GrdStyle.css" rel="stylesheet">
    <script src="GrdJs.js"></script>
    <script>
        function insertar(hacer,tipogrdNew,codCieCupsNew,descCieCups,codgrdNew)
        {
            ancho = 200;    alto = 245;
            var miPopup11 = null;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
            settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';

            miPopup11 = window.open("GrdProcess.php?hacer="+hacer.value+'&tipogrdNew='+tipogrdNew.value+'&codCieCupsNew='+codCieCupsNew.value+
                '&descCieCups='+descCieCups.value+'&codgrdNew='+codgrdNew.value,"miwin",settings2);
            miPopup11.focus();
        }
        function insertar2(hacer,codgrdNew,nomgrdNew)
        {
            ancho = 200;    alto = 245;
            var miPopup11 = null;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
            settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';

            miPopup11 = window.open("GrdProcess.php?hacer="+hacer.value+'&codgrdNew='+codgrdNew.value+'&nomgrdNew='+nomgrdNew.value,"miwin",settings2);
            miPopup11.focus();
        }

        function insertar3(hacer,codgrd1New,nomgrd1New)
        {
            ancho = 200;    alto = 245;
            var miPopup11 = null;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
            settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';

            miPopup11 = window.open("GrdProcess.php?hacer="+hacer.value+'&codgrd1New='+codgrd1New.value+'&nomgrd1New='+nomgrd1New.value,"miwin",settings2);
            miPopup11.focus();
        }
    </script>
    <?php
    include_once("conex.php");
    include_once("root/comun.php");
    //include('../MATRIX/include/root/conex.php'); //publicacion local
    //include('../MATRIX/include/root/comun.php'); //publicacion local
    $conex = obtenerConexionBD("matrix");

    $keyword = '%'.$_POST['keyword'].'%';   $tipoAtencion = $_POST['tipoAten']; $accion = $_GET['accion'];
    $idRegistro = $_GET['idRegistro'];      $grdUpdate = $_POST['grdUpdate'];   $nomgrdNew = $_GET['nomgrdNew'];
    $altasD = $_POST['altmeddep'];          $estanD = $_POST['estmeddep'];      $codgrdNew2 = $_POST['codgrdNew2'];
    $subaccion = $_GET['subaccion'];        if($accion == null){$accion = $_POST['accion'];}
    if($subaccion == null){$subaccion = $_POST['subaccion'];}   $tipogrdNew = $_POST['tipogrdNew'];
    if($tipogrdNew == null){$tipogrdNew = $_GET['tipogrdNew'];} $codCieSelected = $_POST['codCieCupsNew'];
    if($codCieSelected == null){$codCieSelected = $_GET['codCieCupsNew'];}  if($tipoAtencion == '1'){$tipoAtencion = 'M';}
    $codGrdSelected = $_POST['codgrdNew'];  if($codGrdSelected == null){$codGrdSelected = $_GET['codgrdNew'];}
    if($tipoAtencion == '2'){$tipoAtencion = 'Q';}  $descCieCups = $_POST['descCieCups'];
    if($descCieCups == null){$descCieCups = $_GET['descCieCups'];}  $hacer = $_POST['hacer'];
    $fechaActual = date('Y-m-d');   $horaActual = date('H:i:s');
    //calidad12:
    $codgrd1New = $_GET['codgrd1New']; $nomgrd1New = $_GET['nomgrd1new'];
    //realizar carga masiva desde csv
    $radTruncate = $_POST['radTruncate'];
    ?>
</head>

<body style="overflow: hidden">
<?php
/*
if($hacer == 'updateCal09')
{
    $queryInsCal09 = "insert into calidad_000009
                      VALUES('calidad','$fechaActual','$horaActual','$codGrdSelected','$nomgrdNew','on','C-calidad','') ";
    mysql_query($queryInsCal09) or die (mysql_errno()." - en el query: ".$queryInsCal09." - ".mysql_error());
    ?>
    <script>
        window.opener.close();
        window.close();
    </script>
    <?php
}
if($hacer == 'updateCal10')
{
    $queryInsCal10 = "insert into calidad_000010
                      VALUES('calidad','$fechaActual','$horaActual','$tipogrdNew','$codCieSelected','$descCieCups','$codGrdSelected','on','C-calidad','') ";
    mysql_query($queryInsCal10) or die (mysql_errno()." - en el query: ".$queryInsCal10." - ".mysql_error());
    ?>
    <script>
        window.opener.close();
        window.close();
    </script>
    <?php
}
if($hacer == 'updateCal102')
{
    $queryInsCal102 = "update calidad_000010 set Codigogrd = '$codGrdSelected' WHERE Codigo = '$codCieSelected'";
    mysql_query($queryInsCal102) or die (mysql_errno()." - en el query: ".$queryInsCal102." - ".mysql_error());
    ?>
    <script>
        window.opener.close();
        window.close();
    </script>
    <?php
}
if($hacer == 'updateCal12')
{
    $queryConsCal12 = "select count(Codgrd1) CONTEO from calidad_000012 WHERE Codgrd1 = '$codgrd1New'";
    $commitQryConsCal12 = mysql_query($queryConsCal12) or die (mysql_errno()." - en el query: ".$queryConsCal12." - ".mysql_error());
    $datosConsCal12 = mysql_fetch_array($commitQryConsCal12, $conex);
    $conteoCal12 = $datosConsCal12['CONTEO'];

    if($conteoCal12 == 0)
    {
        $queryInsCal12 = "insert into calidad_000012
                          VALUES('calidad','$fechaActual','$horaActual','$codgrd1New','$nomgrd1New','on','C-calidad','')";
        mysql_query($queryInsCal12) or die (mysql_errno()." - en el query: ".$queryInsCal12." - ".mysql_error());
        ?>
        <script>
            window.opener.close();
            window.close();
        </script>
        <?php
    }
    else
    {
        ?>
        <script>
            window.close();
            window.opener.alert('El registro ya existe, esta tabla no permite duplicados');
        </script>
        <?php
    }
}
*/
if($accion == 'modificar')
{
    ?>
    <div class="container" style="margin-left: 0">
        <div class="panel panel-info contenido">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1">Matrix - Modificacion de Registros de GRD</div>
            </div>

            <?php
            $query1 = "select * from calidad_000011 WHERE id = '$idRegistro'";
            $commit1 = mysql_query($query1) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
            $datos1 = mysql_fetch_array($commit1, $conex);
            $codGrd1 = $datos1['Codgrd1'];      $codSeveridad = substr($codGrd1,-1);    $altasDep = $datos1['Altdep'];
            $estMedDep = $datos1['Estmeddep'];

            $query2 = "select * from calidad_000012 WHERE Codgrd1 = '$codGrd1'";
            $commit2 = mysql_query($query2) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
            $datos2 = mysql_fetch_array($commit2, $conex);
            $grdNombre = $datos2['Grdnom1'];
            ?>
            <h4 style="text-align: center"><?php echo $codGrd1.' - '.$grdNombre ?></h4>

            <form method="post" action="GrdProcess.php">
                <table align="center">
                    <tr>
                        <td>
                            <div class="input-group selectDispo" style="margin-left: 10px">
                                <span class="input-group-addon input-sm">
                                <label for="codSeveridad">&nbsp;&nbsp;&nbsp;&nbsp;CODIGO DE SEVERIDAD:&nbsp;&nbsp;&nbsp;</label>
                                </span>
                                <input type="text" id="codSeveridad" name="codSeveridad" class="form-control form-sm" style="width: 50px" value="<?php echo $codSeveridad ?>" readonly>
                            </div>
                            <div class="input-group selectDispo" style="margin-left: 10px">
                                <span class="input-group-addon input-sm">
                                <label for="altmeddep">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ALTAS DEPURADAS:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                </span>
                                <input type="text" id="altmeddep" name="altmeddep" class="form-control form-sm" style="width: 70px" value="<?php echo $altasDep ?>">

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none"></span>

                                <span class="input-group-addon input-sm">
                                <label for="estmeddep">&nbsp;&nbsp;&nbsp;&nbsp;ESTANCIA MEDIA DEPURADA:&nbsp;&nbsp;&nbsp;</label>
                                </span>
                                <input type="text" id="estmeddep" name="estmeddep" class="form-control form-sm" style="width: 70px" value="<?php echo $estMedDep ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" id="accion" name="accion" value="actualizar">
                            <input type="hidden" id="grdUpdate" name="grdUpdate" value="<?php echo $idRegistro ?>">
                            <div class="input-group" style="margin-top: 10px; text-align: center">
                                <input type="submit" class="btn btn-info btn-sm" value="Actualizar" title="Actualizar" style="width: 120px">
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <?php
}
if($accion == 'actualizar')
{
    if($grdUpdate != null)
    {
        $query3 = "update calidad_000011 set Altdep = '$altasD', Estmeddep = '$estanD' WHERE id = '$grdUpdate'";
        $commit3 = mysql_query($query3) or die (mysql_errno()." - en el query: ".$query3." - ".mysql_error());
        if($commit3 == true)
        {
            ?>
            <h4 style="text-align: center">ACTUALIZACION EXITOSA</h4>
            <script>
                window.close();
                opener.location.reload(true);
            </script>
            <?php
        }
        else
        {
            ?>
            <h4 style="text-align: center">NO SE ACTUALIZO</h4>
            <?php
        }
    }
}
if($accion == 'maestros')
{
    ?>
    <div class="container" style="margin-left: 0">
        <div class="panel panel-info" style="border: none; height: 230px">
            <div class="panel-heading encabezado">
                <div class="panel-title titulo1">Matrix - Mantenimiento de Maestros GRD</div>
            </div>
            <form action="GrdProcess.php" enctype='multipart/form-data' method="post">
                <?php
                switch($subaccion)
                {
                    case 'calidad09':
                    ?>
                    <h4 style="text-align: center">Maestro de Codigos GRD (calidad_000009)</h4>
                    <?php
                        if(!isset($files))
                        {
                            ?>
                            <table align="left" style="width: 100%">
                                <tr>
                                    <td>
                                        <div class="input-group" style="margin-left: 10px; margin-top: 10px">
                                            <span class="input-group-addon input-sm"><label for="tableName">&nbsp;&nbsp;&nbsp;&nbsp;Nombre de la Tabla:&nbsp;&nbsp;&nbsp;</label></span>
                                            <input type="text" id="tableName" name="tableName" value="calidad_000009" class="form-control form-sm" style="width: 150px" readonly>

                                            <span class="input-group-addon input-sm"><label for="fieldSeparator">&nbsp;&nbsp;Separador de Campos:&nbsp;</label></span>
                                            <input type="text" id="fieldSeparator" name="fieldSeparator" value="|" class="form-control form-sm" style="width: 150px" readonly>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group" style="margin-left: 10px; margin-top: 10px; background-color: #DD5145; text-align: center">
                                            <label for="radTruncate" style="color: white">Realizar borrado previo de los datos en esta tabla?</label>
                                            &ensp;
                                            Si <input type="radio" name="radTruncate" id="radTruncate" value="1">
                                            &ensp;&ensp;
                                            No <input type="radio" name="radTruncate" id="radTruncate" value="0" checked>
                                        </div>
                                    </td>
                                </tr>
                                <tr align="center">
                                    <td>
                                        <div class="input-group" style="margin-top: 10px">
                                            <span class="files">
                                                <label>&ensp;&ensp;Nombre del Archivo: &ensp;</label>
                                                <input type="file" name="files" id="files">
                                            </span>
                                            <label for="files" class="labelArchivo">
                                                <span>Seleccionar Archivo</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input type="hidden" id="accion" name="accion" value="maestros">
                                        <input type="hidden" id="subaccion" name="subaccion" value="calidad09">
                                        <input type="hidden" id="hacer" name="hacer" value="updateCal09">
                                        <div class="input-group" style="margin-top: 15px; text-align: center">
                                            <input type="submit" class="btn btn-info btn-sm" value="Cargar" title="Actualizar Tabla en BD Matrix" style="width: 120px">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <?php
                        }
                        else
                        {
                            if($radTruncate == '1')
                            {
                                $queryTrunc = "truncate table calidad_000009";
                                $commQryTrunc = mysql_query($queryTrunc, $conex) or die (mysql_errno()." - en el query: ".$queryTrunc." - ".mysql_error());
                            }
                            //echo 'RADTRUNCATE = '.$radTruncate;
                            $files = $_FILES['files']['tmp_name'];
                            chmod($files, 0644);
                            $count = 0;
                            $queryCountCal09 = "select count(*) from calidad_000009";
                            $err = mysql_query($queryCountCal09, $conex) or die (mysql_errno()." - en el query: ".$queryCountCal09." - ".mysql_error());
                            $row = mysql_fetch_array($err);
                            $inicial = $row[0];
                            //$queryLoad = "LOAD DATA INFILE '$files' into table calidad_000009 FIELDS TERMINATED BY '|'";
                            $queryLoad = "LOAD DATA LOCAL INFILE '$files' into table calidad_000009 FIELDS TERMINATED BY '|'";
                            $err = mysql_query($queryLoad, $conex) or die (mysql_errno()." - en el query: ".$queryLoad." - ".mysql_error());
                            $queryLoad = "select count(*) from calidad_000009 ";
                            $err = mysql_query($queryLoad, $conex) or die (mysql_errno()." - en el query: ".$queryLoad." - ".mysql_error());
                            $row = mysql_fetch_array($err);
                            $final = $row[0];
                            $total = $final - $inicial;

                            echo "Numero de Registros Iniciales :".$inicial."<br>";
                            echo "Numero de Finales :".$final."<br>";
                            echo "Carga Exitosa Numero de Registros Almacenados :".$total;
                            echo "<br><br>";
                            ?>
                            <div align="center">
                                <button onclick="window.close()">Aceptar</button>
                            </div>
                            <?php
                        }
                        ?>
                        <script type="application/javascript">
                            jQuery('input[type=file]').change(function(){
                                var filename = jQuery(this).val().split('\\').pop();
                                var idname = jQuery(this).attr('id');
                                console.log(jQuery(this));
                                console.log(filename);
                                console.log(idname);
                                jQuery('span.'+idname).next().find('span').html(filename);
                            });
                        </script>
                    <?php
                    break;
                    case 'calidad10':
                    ?>
                    <h4 style="text-align: center">Relacion Dx - CUPS / GRD (calidad_000010)</h4>
                    <?php
                    if(!isset($files))
                    {
                        ?>
                        <table align="left" style="width: 100%">
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px">
                                        <span class="input-group-addon input-sm"><label for="tableName">&nbsp;&nbsp;&nbsp;&nbsp;Nombre de la Tabla:&nbsp;&nbsp;&nbsp;</label></span>
                                        <input type="text" id="tableName" name="tableName" value="calidad_000010" class="form-control form-sm" style="width: 150px" readonly>

                                        <span class="input-group-addon input-sm"><label for="fieldSeparator">&nbsp;&nbsp;Separador de Campos:&nbsp;</label></span>
                                        <input type="text" id="fieldSeparator" name="fieldSeparator" value="|" class="form-control form-sm" style="width: 150px" readonly>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px; background-color: #DD5145; text-align: center">
                                        <label for="radTruncate" style="color: white">Realizar borrado previo de los datos en esta tabla?</label>
                                        &ensp;
                                        Si <input type="radio" name="radTruncate" id="radTruncate" value="1">
                                        &ensp;&ensp;
                                        No <input type="radio" name="radTruncate" id="radTruncate" value="0" checked>
                                    </div>
                                </td>
                            </tr>
                            <tr align="center">
                                <td>
                                    <div class="input-group" style="margin-top: 10px">
                                        <span class="files">
                                            <label>&ensp;&ensp;Nombre del Archivo: &ensp;</label>
                                            <input type="file" name="files" id="files">
                                        </span>
                                        <label for="files" class="labelArchivo">
                                        <span>Seleccionar Archivo</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" id="accion" name="accion" value="maestros">
                                    <input type="hidden" id="subaccion" name="subaccion" value="calidad10">
                                    <input type="hidden" id="hacer" name="hacer" value="updateCal10">
                                    <div class="input-group" style="margin-top: 15px; text-align: center">
                                        <input type="submit" class="btn btn-info btn-sm" value="Cargar" title="Actualizar Tabla en BD Matrix" style="width: 120px">
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php
                    }
                    else
                    {
                        if($radTruncate == '1')
                        {
                            $queryTrunc = "truncate table calidad_000010";
                            $commQryTrunc = mysql_query($queryTrunc, $conex) or die (mysql_errno()." - en el query: ".$queryTrunc." - ".mysql_error());
                        }
                        $files = $_FILES['files']['tmp_name'];
                        chmod($files, 0644);
                        $count = 0;
                        $queryCountCal09 = "select count(*) from calidad_000010";
                        $err = mysql_query($queryCountCal09, $conex) or die (mysql_errno()." - en el query: ".$queryCountCal09." - ".mysql_error());
                        $row = mysql_fetch_array($err);
                        $inicial = $row[0];
                        //$queryLoad = "LOAD DATA INFILE '$files' into table calidad_000009 FIELDS TERMINATED BY '|'";
                        $queryLoad = "LOAD DATA LOCAL INFILE '$files' into table calidad_000010 FIELDS TERMINATED BY '|'";
                        $err = mysql_query($queryLoad, $conex) or die (mysql_errno()." - en el query: ".$queryLoad." - ".mysql_error());
                        $queryLoad = "select count(*) from calidad_000010 ";
                        $err = mysql_query($queryLoad, $conex) or die (mysql_errno()." - en el query: ".$queryLoad." - ".mysql_error());
                        $row = mysql_fetch_array($err);
                        $final = $row[0];
                        $total = $final - $inicial;

                        echo "Numero de Registros Iniciales :".$inicial."<br>";
                        echo "Numero de Finales :".$final."<br>";
                        echo "Carga Exitosa Numero de Registros Almacenados :".$total;
                        echo "<br><br>";
                        ?>
                        <div align="center">
                            <button onclick="window.close()">Aceptar</button>
                        </div>
                        <?php
                    }
                    ?>
                    <script type="application/javascript">
                        jQuery('input[type=file]').change(function(){
                            var filename = jQuery(this).val().split('\\').pop();
                            var idname = jQuery(this).attr('id');
                            console.log(jQuery(this));
                            console.log(filename);
                            console.log(idname);
                            jQuery('span.'+idname).next().find('span').html(filename);
                        });
                    </script>
                    <?php
                    break;
                    case 'calidad11':
                    ?>
                    <h4 style="text-align: center">Niveles de Severidad de GRD (calidad_000011)</h4>
                    <?php
                    if(!isset($files))
                    {
                        ?>
                        <table align="left" style="width: 100%">
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px">
                                        <span class="input-group-addon input-sm"><label for="tableName">&nbsp;&nbsp;&nbsp;&nbsp;Nombre de la Tabla:&nbsp;&nbsp;&nbsp;</label></span>
                                        <input type="text" id="tableName" name="tableName" value="calidad_000011" class="form-control form-sm" style="width: 150px" readonly>

                                        <span class="input-group-addon input-sm"><label for="fieldSeparator">&nbsp;&nbsp;Separador de Campos:&nbsp;</label></span>
                                        <input type="text" id="fieldSeparator" name="fieldSeparator" value="|" class="form-control form-sm" style="width: 150px" readonly>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px; background-color: #DD5145; text-align: center">
                                        <label for="radTruncate" style="color: white">Realizar borrado previo de los datos en esta tabla?</label>
                                        &ensp;
                                        Si <input type="radio" name="radTruncate" id="radTruncate" value="1">
                                        &ensp;&ensp;
                                        No <input type="radio" name="radTruncate" id="radTruncate" value="0" checked>
                                    </div>
                                </td>
                            </tr>
                            <tr align="center">
                                <td>
                                    <div class="input-group" style="margin-top: 10px">
                                        <span class="files">
                                            <label>&ensp;&ensp;Nombre del Archivo: &ensp;</label>
                                            <input type="file" name="files" id="files">
                                            </span>
                                        <label for="files" class="labelArchivo">
                                            <span>Seleccionar Archivo</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" id="accion" name="accion" value="maestros">
                                    <input type="hidden" id="subaccion" name="subaccion" value="calidad11">
                                    <input type="hidden" id="hacer" name="hacer" value="updateCal11">
                                    <div class="input-group" style="margin-top: 15px; text-align: center">
                                        <input type="submit" class="btn btn-info btn-sm" value="Cargar" title="Actualizar Tabla en BD Matrix" style="width: 120px">
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php
                    }
                    else
                    {
                        if($radTruncate == '1')
                        {
                            $queryTrunc = "truncate table calidad_000011";
                            $commQryTrunc = mysql_query($queryTrunc, $conex) or die (mysql_errno()." - en el query: ".$queryTrunc." - ".mysql_error());
                        }
                        $files = $_FILES['files']['tmp_name'];
                        chmod($files, 0644);
                        $count = 0;
                        $queryCountCal09 = "select count(*) from calidad_000011";
                        $err = mysql_query($queryCountCal09, $conex) or die (mysql_errno()." - en el query: ".$queryCountCal09." - ".mysql_error());
                        $row = mysql_fetch_array($err);
                        $inicial = $row[0];
                        //$queryLoad = "LOAD DATA INFILE '$files' into table calidad_000009 FIELDS TERMINATED BY '|'";
                        $queryLoad = "LOAD DATA LOCAL INFILE '$files' into table calidad_000011 FIELDS TERMINATED BY '|'";
                        $err = mysql_query($queryLoad, $conex) or die (mysql_errno()." - en el query: ".$queryLoad." - ".mysql_error());
                        $queryLoad = "select count(*) from calidad_000011 ";
                        $err = mysql_query($queryLoad, $conex) or die (mysql_errno()." - en el query: ".$queryLoad." - ".mysql_error());
                        $row = mysql_fetch_array($err);
                        $final = $row[0];
                        $total = $final - $inicial;

                        echo "Numero de Registros Iniciales :".$inicial."<br>";
                        echo "Numero de Finales :".$final."<br>";
                        echo "Carga Exitosa Numero de Registros Almacenados :".$total;
                        echo "<br><br>";
                        ?>
                        <div align="center">
                            <button onclick="window.close()">Aceptar</button>
                        </div>
                        <?php
                    }
                    ?>
                    <script type="application/javascript">
                        jQuery('input[type=file]').change(function(){
                            var filename = jQuery(this).val().split('\\').pop();
                            var idname = jQuery(this).attr('id');
                            console.log(jQuery(this));
                            console.log(filename);
                            console.log(idname);
                            jQuery('span.'+idname).next().find('span').html(filename);
                        });
                    </script>
                    <?php
                    break;
                    case 'calidad12':
                    ?>
                    <h4 style="text-align: center">Maestro de Codigos GRD1 (calidad_000012)</h4>
                    <?php
                    if(!isset($files))
                    {
                        ?>
                        <table align="left" style="width: 100%">
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px">
                                        <span class="input-group-addon input-sm"><label for="tableName">&nbsp;&nbsp;&nbsp;&nbsp;Nombre de la Tabla:&nbsp;&nbsp;&nbsp;</label></span>
                                        <input type="text" id="tableName" name="tableName" value="calidad_000012" class="form-control form-sm" style="width: 150px" readonly>

                                        <span class="input-group-addon input-sm"><label for="fieldSeparator">&nbsp;&nbsp;Separador de Campos:&nbsp;</label>                                                </span>
                                        <input type="text" id="fieldSeparator" name="fieldSeparator" value="|" class="form-control form-sm" style="width: 150px" readonly>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px; background-color: #DD5145; text-align: center">
                                        <label for="radTruncate" style="color: white">Realizar borrado previo de los datos en esta tabla?</label>
                                        &ensp;
                                        Si <input type="radio" name="radTruncate" id="radTruncate" value="1">
                                        &ensp;&ensp;
                                        No <input type="radio" name="radTruncate" id="radTruncate" value="0" checked>
                                    </div>
                                </td>
                            </tr>
                            <tr align="center">
                                <td>
                                    <div class="input-group" style="margin-top: 10px">
                                        <span class="files">
                                            <label>&ensp;&ensp;Nombre del Archivo: &ensp;</label>
                                            <input type="file" name="files" id="files">
                                        </span>
                                        <label for="files" class="labelArchivo">
                                        <span>Seleccionar Archivo</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" id="accion" name="accion" value="maestros">
                                    <input type="hidden" id="subaccion" name="subaccion" value="calidad12">
                                    <input type="hidden" id="hacer" name="hacer" value="updateCal12">
                                    <div class="input-group" style="margin-top: 15px; text-align: center">
                                        <input type="submit" class="btn btn-info btn-sm" value="Cargar" title="Actualizar Tabla en BD Matrix" style="width: 120px">
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php
                    }
                    else
                    {
                        if($radTruncate == '1')
                        {
                            $queryTrunc = "truncate table calidad_000012";
                            $commQryTrunc = mysql_query($queryTrunc, $conex) or die (mysql_errno()." - en el query: ".$queryTrunc." - ".mysql_error());
                        }
                        $files = $_FILES['files']['tmp_name'];
                        chmod($files, 0644);
                        $count = 0;
                        $queryCountCal09 = "select count(*) from calidad_000012";
                        $err = mysql_query($queryCountCal09, $conex) or die (mysql_errno()." - en el query: ".$queryCountCal09." - ".mysql_error());
                        $row = mysql_fetch_array($err);
                        $inicial = $row[0];
                        //$queryLoad = "LOAD DATA INFILE '$files' into table calidad_000009 FIELDS TERMINATED BY '|'";
                        $queryLoad = "LOAD DATA LOCAL INFILE '$files' into table calidad_000012 FIELDS TERMINATED BY '|'";
                        $err = mysql_query($queryLoad, $conex) or die (mysql_errno()." - en el query: ".$queryLoad." - ".mysql_error());
                        $queryLoad = "select count(*) from calidad_000012 ";
                        $err = mysql_query($queryLoad, $conex) or die (mysql_errno()." - en el query: ".$queryLoad." - ".mysql_error());
                        $row = mysql_fetch_array($err);
                        $final = $row[0];
                        $total = $final - $inicial;

                        echo "Numero de Registros Iniciales :".$inicial."<br>";
                        echo "Numero de Finales :".$final."<br>";
                        echo "Carga Exitosa Numero de Registros Almacenados :".$total;
                        echo "<br><br>";
                        ?>
                        <div align="center">
                            <button onclick="window.close()">Aceptar</button>
                        </div>
                        <?php
                    }
                    ?>
                    <script type="application/javascript">
                        jQuery('input[type=file]').change(function(){
                            var filename = jQuery(this).val().split('\\').pop();
                            var idname = jQuery(this).attr('id');
                            console.log(jQuery(this));
                            console.log(filename);
                            console.log(idname);
                            jQuery('span.'+idname).next().find('span').html(filename);
                        });
                    </script>
                    <?php
                    break;
                }
                ?>
            </form>
        </div>
    </div>
    <?php
}
else
{
    if($tipoAtencion == 'M')
    {
        $sql = mysql_queryV("select * from root_000011
                            WHERE (root_000011.Descripcion LIKE '$keyword' AND Estado = 'on')
                            OR (root_000011.Codigo LIKE '$keyword' AND Estado = 'on')
                            ORDER BY Descripcion ASC");

        while($rs = mysql_fetch_array($sql))
        {
            $country_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['Codigo'].' - '.$rs['Descripcion']);
            echo '<li onclick="set_item(\''.str_replace("'", "\'",$rs['Codigo'].' - '.$rs['Descripcion']).'\')">'.$country_name.'</li>';
        }
    }
    elseif($tipoAtencion == 'Q')
    {
        $sql = mysql_queryV("select * from root_000012
                            WHERE (root_000012.Nombre LIKE '$keyword' AND Estado = 'on')
                            OR (root_000012.Codigo LIKE '$keyword' AND Estado = 'on')
                            ORDER BY Nombre ASC");

        while($rs = mysql_fetch_array($sql))
        {
            $country_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs['Codigo'].' - '.$rs['Nombre']);
            echo '<li onclick="set_item(\''.str_replace("'", "\'",$rs['Codigo'].' - '.$rs['Nombre']).'\')">'.$country_name.'</li>';
        }
    }
}
?>
</body>
</html>
