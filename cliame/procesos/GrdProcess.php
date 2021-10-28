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
    if($descCieCups == null){$descCieCups = $_GET['descCieCups'];}  $hacer = $_GET['hacer'];
    $fechaActual = date('Y-m-d');   $horaActual = date('H:i:s');
    ?>
</head>

<body style="overflow: hidden">
<?php
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

            <form method="post" action="GrdProcess.php">
                <?php
                switch($subaccion)
                {
                    case 'calidad09':
                        ?>
                        <h4 style="text-align: center">Maestro de Codigos GRD (calidad_000009)</h4>
                        <table align="left" style="width: 100%">
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px">
                                        <span class="input-group-addon input-sm">
                                            <label for="codgrdNew">&nbsp;&nbsp;&nbsp;&nbsp;CODIGO GRD:&nbsp;&nbsp;&nbsp;</label>
                                        </span>
                                        <input type="text" id="codgrdNew" name="codgrdNew" class="form-control form-sm" style="width: 150px">
                                    </div>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px">
                                        <span class="input-group-addon input-sm">
                                            <label for="nomgrdNew">&nbsp;&nbsp;&nbsp;&nbsp;NOMBRE GRD:&nbsp;&nbsp;&nbsp;</label>
                                        </span>
                                        <input type="text" id="nomgrdNew" name="nomgrdNew" class="form-control form-sm" style="width: 550px">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" id="accion" name="accion" value="updateCal09">
                                    <input type="hidden" id="hacer" name="hacer" value="updateCal09">
                                    <div class="input-group" style="margin-top: 10px; text-align: center">
                                        <input type="button" class="btn btn-info btn-sm" value="Crear" title="Crear" style="width: 120px"
                                               onclick="insertar2(hacer,codgrdNew,nomgrdNew)">
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php
                        break;

                    case 'calidad10':
                        ?>
                        <h4 style="text-align: center">Relacion Dx - CUPS / GRD (calidad_000010)</h4>
                        <table align="left" style="width: 100%">
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 0">
                                        <span class="input-group-addon input-sm">
                                            <label for="tipogrdNew">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TIPO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                        </span>
                                        <select id="tipogrdNew" name="tipogrdNew" class="form-control form-sm" style="height: 100%; width: 150px" onchange="this.form.submit()">
                                            <?php
                                            if($tipogrdNew != null)
                                            {
                                                ?>
                                                <option selected><?php echo $tipogrdNew ?></option>
                                                <option>M</option>
                                                <option>Q</option>
                                                <option disabled>Seleccione...</option>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <option>M</option>
                                                <option>Q</option>
                                                <option selected disabled>Seleccione...</option>
                                                <!--<option selected disabled><?php// echo $tipogrdNew ?></option>-->
                                                <?php
                                            }
                                            ?>
                                        </select>

                                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none"></span>

                                        <span class="input-group-addon input-sm">
                                            <label for="codCieCupsNew">&nbsp;&nbsp;&nbsp;&nbsp;CODIGO CIE10 / CUPS:&nbsp;&nbsp;&nbsp;</label>
                                        </span>
                                        <?php
                                        switch($tipogrdNew)
                                        {
                                            case 'M':
                                                ?>
                                                <select id="codCieCupsNew" name="codCieCupsNew" class="form-control form-sm" style="width: 200px; height: 100%" onchange="this.form.submit()">
                                                    <?php
                                                    if($codCieSelected == null)
                                                    {
                                                        $queryCie = "select Codigo from root_000011 WHERE Estado = 'on' ORDER BY Codigo ASC";
                                                        $commitCie = mysql_query($queryCie) or die (mysql_errno()." - en el query: ".$queryCie." - ".mysql_error());
                                                        while($datosCie = mysql_fetch_array($commitCie, $conex))
                                                        {
                                                            $itemCieCod = $datosCie['Codigo'];
                                                            echo "<option value='".$itemCieCod."'>".$itemCieCod."</option>";
                                                        }
                                                        ?><option selected disabled>CODIGO CIE-10</option><?php
                                                    }
                                                    else
                                                    {
                                                        $queryCie = "select Codigo from root_000011 WHERE Estado = 'on' ORDER BY Codigo ASC";
                                                        $commitCie = mysql_query($queryCie) or die (mysql_errno()." - en el query: ".$queryCie." - ".mysql_error());
                                                        while($datosCie = mysql_fetch_array($commitCie, $conex))
                                                        {
                                                            $itemCieCod = $datosCie['Codigo'];
                                                            echo "<option value='".$itemCieCod."'>".$itemCieCod."</option>";
                                                        }
                                                        ?><option selected><?php echo $codCieSelected ?></option><?php
                                                    }
                                                    ?>
                                                </select>
                                                <?php
                                                break;
                                            case 'Q':
                                                ?>
                                                <select id="codCieCupsNew" name="codCieCupsNew" class="form-control form-sm" style="width: 200px; height: 100%" onchange="this.form.submit()">
                                                    <?php
                                                    if($codCieSelected == null)
                                                    {
                                                        $queryCups = "select Codigo from root_000012 WHERE Estado = 'on' ORDER BY Codigo ASC";
                                                        $commitCups = mysql_query($queryCups) or die (mysql_errno()." - en el query: ".$queryCups." - ".mysql_error());
                                                        while($datosCups = mysql_fetch_array($commitCups, $conex))
                                                        {
                                                            $itemCupsCod = $datosCups['Codigo'];
                                                            echo "<option value='".$itemCupsCod."'>".$itemCupsCod."</option>";
                                                        }
                                                        ?><option selected disabled>CODIGO CUPS</option><?php
                                                    }
                                                    else
                                                    {
                                                        $queryCups = "select Codigo from root_000012 WHERE Estado = 'on' ORDER BY Codigo ASC";
                                                        $commitCups = mysql_query($queryCups) or die (mysql_errno()." - en el query: ".$queryCups." - ".mysql_error());
                                                        while($datosCups = mysql_fetch_array($commitCups, $conex))
                                                        {
                                                            $itemCupsCod = $datosCups['Codigo'];
                                                            echo "<option value='".$itemCupsCod."'>".$itemCupsCod."</option>";
                                                        }
                                                        ?><option selected><?php echo $codCieSelected ?></option><?php
                                                    }
                                                    ?>
                                                </select>
                                                <?php
                                                break;
                                            default:
                                                ?>
                                                <select id="codCieCupsNew" name="codCieCupsNew" class="form-control form-sm" style="width: 200px; height: 100%">
                                                    <option selected disabled>Seleccione...</option>
                                                </select>
                                                <?php
                                                break;
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 5px">
                                        DESCRIPCION CIE/CUPS:
                                        <?php
                                        if($codCieSelected == null)
                                        {
                                            ?>
                                            No ha seleccionado codigo de diagnostico o procedimiento...
                                            <input type="hidden" id="descCieCups" name="descCieCups" class="form-control form-sm" style="width: 550px" readonly>
                                            <?php
                                        }
                                        else
                                        {
                                            if($tipogrdNew == 'M')
                                            {
                                                $query5 = "select Descripcion from root_000011 WHERE Codigo = '$codCieSelected'";
                                                $commitquery5 = mysql_query($query5);
                                                $datosquery5 = mysql_fetch_array($commitquery5);
                                                $descRoot_11 = $datosquery5['Descripcion'];
                                                ?>
                                                <?php echo $descRoot_11 ?>
                                                <input type="hidden" id="descCieCups" name="descCieCups" class="form-control form-sm" style="width: 550px" value="<?php echo $descRoot_11 ?>">
                                                <?php
                                            }
                                            else
                                            {
                                                $query5 = "select Nombre from root_000012 WHERE Codigo = '$codCieSelected'";
                                                $commitquery5 = mysql_query($query5);
                                                $datosquery5 = mysql_fetch_array($commitquery5);
                                                $descRoot_11 = $datosquery5['Nombre'];
                                                ?>
                                                <?php echo $descRoot_11 ?>
                                                <input type="hidden" id="descCieCups" name="descCieCups" class="form-control form-sm" style="width: 550px" value="<?php echo $descRoot_11 ?>">
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $verificaCal10 = "select count(*),id from calidad_000010 WHERE Codigo = '$codCieSelected'";
                            $commVerificaCal10 = mysql_query($verificaCal10);
                            $datoVerificaCal10 = mysql_fetch_array($commVerificaCal10);
                            $conteoCal10 = $datoVerificaCal10[0];   $idAsociadoCal10 = $datoVerificaCal10[1];

                            //echo 'ESTE CODIGO CIE/CUPS YA SE ENCUENTRA ASOCIADO A UN GRD ID ='.$idAsociadoCal10;
                            $query6 = "select * from calidad_000010 WHERE id = '$idAsociadoCal10'";
                            $commitQuery6 = mysql_query($query6);
                            $datoQuery6 = mysql_fetch_array($commitQuery6);
                            $codGrdSelected2 = $datoQuery6['Codigogrd'];

                            //EL CODIGO CUPS/CIE YA SE ENCUENTRA ASOCIADO A UN CODIGO GRD:
                            if($conteoCal10 > 0)
                            {
                                ?>
                                <tr>
                                    <td>
                                        <br>
                                        <?php
                                        if($codgrdNew2 == null)
                                        {
                                            ?>
                                            El codigo CUPS/CIE10 seleccionado ya se encuentra asociado al codigo GRD :
                                            <div class="input-group" style="margin-left: 10px; margin-top: 5px">
                                            <span class="input-group-addon input-sm">
                                                <label for="codgrdNew">&nbsp;&nbsp;&nbsp;&nbsp;CODIGO GRD:&nbsp;&nbsp;&nbsp;</label>
                                            </span>
                                                <select id="codgrdNew2" name="codgrdNew2" class="form-control form-sm" style="width: 127px; height: 100%" onchange="this.form.submit()">
                                                    <?php
                                                    if($codgrdNew2 == null)
                                                    {
                                                        $queryGrd = "select * from calidad_000009 ORDER BY id DESC";
                                                        $commitGrd = mysql_query($queryGrd);

                                                        while($datosGrd = mysql_fetch_array($commitGrd, $conex))
                                                        {
                                                            $itemGrd = $datosGrd['Codgrd']; $nomItemGrd = $datosGrd['Grdnom'];
                                                            echo "<option selected value='".$itemGrd."'>".$itemGrd."</option>";
                                                        }
                                                        echo "<option selected value='".$codGrdSelected2."'>".$codGrdSelected2."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <div class="input-group" style="margin-left: 10px; margin-top: 5px">
                                                <span class="input-group-addon input-sm">
                                                    <label for="codgrdNew">&nbsp;&nbsp;&nbsp;&nbsp;CODIGO GRD:&nbsp;&nbsp;&nbsp;</label>
                                                </span>
                                                <select id="codgrdNew" name="codgrdNew" class="form-control form-sm" style="width: 127px; height: 100%" onchange="this.form.submit()">
                                                    <?php
                                                    $queryGrd = "select * from calidad_000009 ORDER BY id DESC";
                                                    $commitGrd = mysql_query($queryGrd);

                                                    while($datosGrd = mysql_fetch_array($commitGrd, $conex))
                                                    {
                                                        $itemGrd = $datosGrd['Codgrd']; $nomItemGrd = $datosGrd['Grdnom'];
                                                        echo "<option value='".$itemGrd."'>".$itemGrd."</option>";
                                                    }
                                                    echo "<option selected value='".$codgrdNew2."'>".$codgrdNew2."</option>";
                                                    ?>
                                                </select>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group" style="margin-left: 10px; margin-top: 5px">
                                            DESCRIPCION GRD:
                                            <?php
                                            if($codGrdSelected2 == null)
                                            {
                                                ?>
                                                No ha seleccionado GRD a asociar...
                                                <input type="hidden" id="nomgrdNew" name="nomgrdNew" class="form-control form-sm" style="width: 550px" readonly>
                                                <?php
                                            }
                                            else
                                            {
                                                $queryGrd2 = "select Grdnom from calidad_000009 WHERE Codgrd = '$codGrdSelected2'";
                                                $commitGrd2 = mysql_query($queryGrd2);
                                                $datosGrd2 = mysql_fetch_array($commitGrd2);
                                                $descGrd_09 = $datosGrd2['Grdnom'];

                                                if($codgrdNew2 != null)
                                                {
                                                    $queryGrd2 = "select Grdnom from calidad_000009 WHERE Codgrd = '$codgrdNew2'";
                                                    $commitGrd2 = mysql_query($queryGrd2);
                                                    $datosGrd2 = mysql_fetch_array($commitGrd2);
                                                    $descGrd_09 = $datosGrd2['Grdnom'];
                                                }
                                                ?>
                                                <?php echo $descGrd_09 ?>
                                                <input type="hidden" id="nomgrdNew" name="nomgrdNew" class="form-control form-sm" style="width: 550px" value="<?php echo $descGrd_09 ?>">
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input type="hidden" id="subaccion" name="subaccion" value="calidad10">
                                        <input type="hidden" id="accion" name="accion" value="maestros">
                                        <input type="hidden" id="hacer" name="hacer" value="updateCal102">
                                        <div class="input-group" style="margin-top: 10px; text-align: center">
                                            <input type="button" class="btn btn-success btn-sm" value="Actualizar" title="Actualizar CIE10/CUPS con GRD" style="width: 120px"
                                                   onclick="insertar(hacer,tipogrdNew,codCieCupsNew,descCieCups,codgrdNew)">
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            else
                            {
                                ?>
                                <tr>
                                    <td>
                                        <div class="input-group" style="margin-left: 10px; margin-top: 5px">
                                        <span class="input-group-addon input-sm">
                                            <label for="codgrdNew">&nbsp;&nbsp;&nbsp;&nbsp;CODIGO GRD:&nbsp;&nbsp;&nbsp;</label>
                                        </span>
                                            <select id="codgrdNew" name="codgrdNew" class="form-control form-sm" style="width: 127px; height: 100%" onchange="this.form.submit()">
                                                <?php
                                                if($codGrdSelected == null)
                                                {
                                                    $queryGrd = "select * from calidad_000009 ORDER BY id DESC";
                                                    $commitGrd = mysql_query($queryGrd);

                                                    while($datosGrd = mysql_fetch_array($commitGrd, $conex))
                                                    {
                                                        $itemGrd = $datosGrd['Codgrd']; $nomItemGrd = $datosGrd['Grdnom'];
                                                        echo "<option value='".$itemGrd."'>".$itemGrd."</option>";
                                                    }
                                                }
                                                else
                                                {
                                                    $queryGrd = "select * from calidad_000009 ORDER BY id DESC";
                                                    $commitGrd = mysql_query($queryGrd);

                                                    while($datosGrd = mysql_fetch_array($commitGrd, $conex))
                                                    {
                                                        $itemGrd = $datosGrd['Codgrd']; $nomItemGrd = $datosGrd['Grdnom'];
                                                        echo "<option value='".$itemGrd."'>".$itemGrd."</option>";
                                                    }
                                                    echo "<option value='".$codGrdSelected."' selected>".$codGrdSelected."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group" style="margin-left: 10px; margin-top: 5px">
                                            DESCRIPCION GRD:
                                            <?php
                                            if($codGrdSelected == null)
                                            {
                                                ?>
                                                No ha seleccionado GRD a asociar...
                                                <input type="hidden" id="nomgrdNew" name="nomgrdNew" class="form-control form-sm" style="width: 550px" readonly>
                                                <?php
                                            }
                                            else
                                            {
                                                $queryGrd2 = "select Grdnom from calidad_000009 WHERE Codgrd = '$codGrdSelected'";
                                                $commitGrd2 = mysql_query($queryGrd2);
                                                $datosGrd2 = mysql_fetch_array($commitGrd2);
                                                $descGrd_09 = $datosGrd2['Grdnom'];
                                                ?>
                                                <?php echo $descGrd_09 ?>
                                                <input type="hidden" id="nomgrdNew" name="nomgrdNew" class="form-control form-sm" style="width: 550px" value="<?php echo $descGrd_09 ?>">
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <input type="hidden" id="subaccion" name="subaccion" value="calidad10">
                                        <input type="hidden" id="accion" name="accion" value="maestros">
                                        <input type="hidden" id="hacer" name="hacer" value="updateCal10">
                                        <div class="input-group" style="margin-top: 10px; text-align: center">
                                            <input type="button" class="btn btn-info btn-sm" value="Relacionar" title="Relacionar CIE10/CUPS con GRD" style="width: 120px"
                                                   onclick="insertar(hacer,tipogrdNew,codCieCupsNew,descCieCups,codgrdNew)">
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        break;

                    case 'calidad11':
                        ?>
                        <h4 style="text-align: center">Niveles de Severidad de GRD (calidad_000011)</h4>
                        <table align="left" style="width: 100%">
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px">
                                        <span class="input-group-addon input-sm">
                                            <label for="codgrdNew">&nbsp;&nbsp;&nbsp;&nbsp;CODIGO GRD:&nbsp;&nbsp;&nbsp;</label>
                                        </span>
                                        <input type="text" id="codgrdNew" name="codgrdNew" class="form-control form-sm" style="width: 150px">
                                    </div>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 10px">
                                        <span class="input-group-addon input-sm">
                                            <label for="nomgrdNew">&nbsp;&nbsp;&nbsp;&nbsp;NOMBRE GRD:&nbsp;&nbsp;&nbsp;</label>
                                        </span>
                                        <input type="text" id="nomgrdNew" name="nomgrdNew" class="form-control form-sm" style="width: 550px">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" id="accion" name="accion" value="updateCal09">
                                    <input type="hidden" id="hacer" name="hacer" value="updateCal09">
                                    <div class="input-group" style="margin-top: 10px; text-align: center">
                                        <input type="button" class="btn btn-info btn-sm" value="Crear" title="Crear" style="width: 120px"
                                               onclick="insertar2(hacer,codgrdNew,nomgrdNew)">
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php
                        break;

                    case 'calidad12':
                        ?>
                        <h4 style="text-align: center">Maestro de Codigos GRD1 (calidad_000012)</h4>
                        <table align="left" style="width: 100%">
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 1px">
                                        <span class="input-group-addon input-sm">
                                            <label for="codgrdNew">&nbsp;&nbsp;&nbsp;&nbsp;CODIGO GRD:&nbsp;&nbsp;&nbsp;</label>
                                        </span>
                                        <select id="codgrdNew" name="codgrdNew" class="form-control form-sm" style="width: 127px; height: 100%" onchange="this.form.submit()">
                                            <?php
                                            if($codGrdSelected == null)
                                            {
                                                $queryGrd = "select * from calidad_000009 ORDER BY id DESC";
                                                $commitGrd = mysql_query($queryGrd);

                                                while($datosGrd = mysql_fetch_array($commitGrd, $conex))
                                                {
                                                    $itemGrd = $datosGrd['Codgrd']; $nomItemGrd = $datosGrd['Grdnom'];
                                                    echo "<option value='".$itemGrd."'>".$itemGrd."</option>";
                                                }
                                            }
                                            else
                                            {
                                                $queryGrd = "select * from calidad_000009 ORDER BY id DESC";
                                                $commitGrd = mysql_query($queryGrd);

                                                while($datosGrd = mysql_fetch_array($commitGrd, $conex))
                                                {
                                                    $itemGrd = $datosGrd['Codgrd']; $nomItemGrd = $datosGrd['Grdnom'];
                                                    echo "<option value='".$itemGrd."'>".$itemGrd."</option>";
                                                }
                                                echo "<option value='".$codGrdSelected."' selected>".$codGrdSelected."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px; margin-top: 5px">
                                        DESCRIPCION GRD:
                                        <?php
                                        if($codGrdSelected == null)
                                        {
                                            ?>
                                            No ha seleccionado GRD a asociar...
                                            <input type="hidden" id="nomgrdNew" name="nomgrdNew" class="form-control form-sm" style="width: 550px" readonly>
                                            <?php
                                        }
                                        else
                                        {
                                            $queryGrd2 = "select Grdnom from calidad_000009 WHERE Codgrd = '$codGrdSelected'";
                                            $commitGrd2 = mysql_query($queryGrd2);
                                            $datosGrd2 = mysql_fetch_array($commitGrd2);
                                            $descGrd_09 = $datosGrd2['Grdnom'];
                                            ?>
                                            <?php echo $descGrd_09 ?>
                                            <input type="hidden" id="nomgrdNew" name="nomgrdNew" class="form-control form-sm" style="width: 550px" value="<?php echo $descGrd_09 ?>">
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div style="margin-left: 10px; margin-top: 2px; max-height: 70px; border: groove; overflow-y: visible;">
                                        <div style="height: 80px; border: inset; max-height: 80px">
                                            <h5 style="margin-bottom: 1px">GRD existentes para este codigo:</h5>
                                            <?php
                                            if($codGrdSelected != null)
                                            {
                                                $queryCal12 = "select * from calidad_000012 WHERE Codgrd1 LIKE '%$codGrdSelected%'";
                                                $commitQueryCal12 = mysql_query($queryCal12);

                                                while($datosCal12 = mysql_fetch_array($commitQueryCal12))
                                                {
                                                    $codGrd1Cal12 = $datosCal12['Codgrd1']; $nomGrdCal12 = $datosCal12['Grdnom1'];
                                                    echo '<label style="margin-top: -1px; font-size: small; font-weight: normal">'.$codGrd1Cal12.' - '.$nomGrdCal12.'</label>';
                                                }
                                                ?>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="hidden" id="subaccion" name="subaccion" value="calidad12">
                                    <input type="hidden" id="accion" name="accion" value="maestros">
                                    <input type="hidden" id="hacer" name="hacer" value="updateCal10">
                                    <div class="input-group" style="margin-top: 10px; text-align: center">
                                        <input type="button" class="btn btn-info btn-sm" value="Relacionar" title="Relacionar CIE10/CUPS con GRD" style="width: 120px"
                                               onclick="insertar(hacer,tipogrdNew,codCieCupsNew,descCieCups,codgrdNew)">
                                    </div>
                                </td>
                            </tr>
                        </table>
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
    /*
    $sql = mysql_queryV("select * from calidad_000010
                        WHERE (calidad_000010.Descripcion LIKE '$keyword' AND Tipo = '$tipoAtencion')
                        OR (calidad_000010.Codigo LIKE '$keyword' AND Tipo = '$tipoAtencion')
                        ORDER BY Descripcion ASC LIMIT 0, 10");
    */

    if($tipoAtencion == 'M')
    {
        $sql = mysql_queryV("select * from root_000011
                            WHERE (root_000011.Descripcion LIKE '$keyword' AND Estado = 'on')
                            OR (root_000011.Codigo LIKE '$keyword' AND Estado = 'on')
                            ORDER BY Descripcion ASC LIMIT 0, 10");

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
                            ORDER BY Nombre ASC LIMIT 0, 10");

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
