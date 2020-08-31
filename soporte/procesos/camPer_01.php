<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CAMBIO DE PERIODO - UNIX</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="camPer_style.css" rel="stylesheet">
    <script src="camPer_js.js" type="text/javascript"></script>
    <style>
        .loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('http://132.1.18.13/matrix/images/medical/facturacion/pageLoader.gif') 50% 50% no-repeat rgb(249,249,249);
            opacity: .8;
        }
    </style>
    <script type="text/javascript">
        $(window).load(function() {
            $(".loader").fadeOut("slow");
        });
    </script>
    <?php
    //PUBLICACION MATRIX:
    ///*
    include("conex.php");
    include("root/comun.php");
    include("PHPMailer.php");
    include("class.smtp.php");
    //require_once 'PHPMailerAutoload.php';

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
        //$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
        $conex_o = odbc_connect('facturacion','teradm','1201')  or die("No se realizo conexión con la BD de Facturación");
    }
    //*/
    //include('../MATRIX/include/root/conex.php'); //publicacion local
    //include('../MATRIX/include/root/comun.php'); //publicacion local
    //include('../CarEmpleado/carEmp_Functions.php'); //publicacion local
    //mysql_select_db('matrix'); //publicacion local
    //$conex = obtenerConexionBD('matrix'); //publicacion local
    //$wuse = '0100463'; //publicacion local

    # Definir las variables
    $pestana = $_POST['pestana'];   if($pestana == null){$pestana = 1;}
    $host="132.1.18.2";  $port=21;  $user="informix";   $password="sco";    $ruta="/u8/programas/cartera";
    $fecha_actual = date("d-m-Y");  $yearMenos = date("d-m-Y",strtotime($fecha_actual."- 1 year"));
    //PASO 1:
    $anoContSubir = $_POST['anoContSubir']; $mesContSubir = $_POST['mesContSubir']; $nomArch1 = $_POST['nomArch1'];         $nomArch2 = $_POST['nomArch2'];
    $sumQryLoad1 = $_POST['queryMtx1'];     $sumQryLoad2 = $_POST['queryMtx2'];     $anoFacNotas = $_POST['anoFacNotas'];   $mesFacNotas = $_POST['mesFacNotas'];
    //PASO 2:
    $mesIniFacNotas = $_POST['mesIniFacNotas']; $mesFinFacNotas = $_POST['mesFinFacNotas'];
    $fecIniFacNotas = $_POST['fecIniFacNotas']; $fecFinFacNotas = $_POST['fecFinFacNotas'];
    $accionLocal = $_POST['accionLocal'];       $archivo = $_FILES['archivo'];
    ?>
</head>

<body>
<div class="container main">
    <div class="panel panel-info contenido">
        <div class="panel-heading encabezado">
            <div class="panel-title titulo1">PROCESOS CAMBIO DE PERIODO - UNIX</div>
        </div>
    </div>

    <h4 style="text-align: center">&ensp;</h4>
    <div class="panel-group" id="accordion">
        <!-- PASO 1:-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">PASO 1</a>
                </h4>
            </div>
            <?php
            if($pestana == '1'){?><div id="collapse1" class="panel-collapse collapse in"><?php }
            else{?><div id="collapse1" class="panel-collapse collapse"><?php }
                ?>
                <div class="panel-body" id="divPaso1">
                    <h4 class="labelTitulo">Cargar archivos a Unix</h4>
                    <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER["HTTP_SELF"]?>">
                        <div align="center" class="input-group" style="width: 100%; border: 1px solid #428BCA">
                            <div style="border: none; width: 50%; float: left">
                                <table style="margin: 5px auto 5px 5px">
                                    <tr>
                                        <td>
                                            <div class="input-group" style="margin: auto; border: none">
                                                <span class="files"><input type="file" name="files" id="files"></span>
                                                <label for="files" class="labelArchivo"><span>Seleccionar Archivo 1</span></label>
                                            </div>
                                        </td>
                                        <td>&ensp;</td>
                                        <td rowspan="2">
                                            <input type="submit" class="btn btn-info btn-sm" value="> > Cargar > >" title="Actualizar Unix">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group" style="margin: auto; border: none">
                                                <span class="files2"><input type="file" name="file2" id="files2"></span>
                                                <label for="files2" class="labelArchivo"><span>Seleccionar Archivo 2</span></label>
                                            </div>
                                        </td>
                                        <td>&ensp;</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div style="width: 50%;display: inline-block; float: right; border-left: 5px solid #428BCA; margin-top: 5px">
                                <table style="margin: 10px auto 10px 5px">
                                    <tr>
                                        <td>
                                            <p>
                                                &ensp;Antes de cargar los archivos, estos deben descargarse del correo.<br>
                                                Son enviados por el area de Impuestos.  Los archivos son 'terceros01.xls' y 'terceros02.xls'.<br>
                                                Revisar que ambos archivos vengan bien y guardarlos con la extension TXT.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>

                    <?php
                    // CARGAR ARCHIVOS PLANOS A TABLAS EQUIVALENTES EN MATRIX:
                    if(is_uploaded_file($_FILES["files"]["tmp_name"]) and is_uploaded_file($_FILES["file2"]["tmp_name"]))
                    {
                        $file1 = $_FILES['files']['tmp_name'];  $file2 = $_FILES['file2']['tmp_name'];
                        $nomArch1 = $_FILES["files"]["name"];   $nomArch2 = $_FILES["file2"]["name"];
                        chmod($file1, 0644);    chmod($file2, 0644);

                        ///PRIMERO LIMPIAR LAS DOS TABLAS EN MATRIX:
                        $queryTrunc1 = "truncate table equipos_000010"; mysql_query($queryTrunc1,$conex);
                        $queryTrunc2 = "truncate table equipos_000011"; mysql_query($queryTrunc2,$conex);

                        ///INSERTAR REGISTROS EN MATRIX:
                        $queryLoad1 = "LOAD DATA LOCAL INFILE '$file1' into table equipos_000010 FIELDS TERMINATED BY '|'";
                        $sumQryLoad1 = mysql_query($queryLoad1, $conex) or die (mysql_errno()." - en el query: ".$queryLoad1." - ".mysql_error());

                        $queryLoad2 = "LOAD DATA LOCAL INFILE '$file2' into table equipos_000011 FIELDS TERMINATED BY '|'";
                        $sumQryLoad2 = mysql_query($queryLoad2, $conex) or die (mysql_errno()." - en el query: ".$queryLoad2." - ".mysql_error());

                        if($sumQryLoad1 == true and $sumQryLoad2 == true)
                        {
                            ?>
                            <h4>Archivo 1: <?php echo $nomArch1 ?> cargado correctamente</h4>
                            <h4>Archivo 2: <?php echo $nomArch2 ?> cargado correctamente</h4>
                            <?php
                        }
                    }

                    //SI LOS ARCHIVOS SE CARGARON CORRECTAMENTE EN MATRIX:
                    if($sumQryLoad1 == true and $sumQryLoad2 == true)
                    {
                        ?>
                        <h4 class="labelTitulo">Ejecutar SUBEAMETE</h4>
                        <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin-left: auto; margin-right: auto">
                            <form method="post">
                                <div class="input-group" style="margin: 10px auto 10px auto; width: 60%">
                                    <span class="input-group-addon input-sm"><label for="anoContSubir">A&Ntilde;O CONTABLE A SUBIR:</label></span>
                                    <select id="anoContSubir" name="anoContSubir" class="form-control form-sm">
                                        <?php $year = date("Y");
                                        for ($i=2018; $i<=$year; $i++)
                                        {
                                            echo '<option value="'.$i.'">'.$i.'</option>';
                                        }
                                        ?>
                                        <?php
                                        if($anoContSubir != null){?><option selected><?php echo $anoContSubir ?></option><?php }
                                        else{?><option selected value="0" disabled>Seleccione...</option><?php }
                                        ?>
                                    </select >
                                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>
                                    <span class="input-group-addon input-sm"><label for="mesContSubir">MES CONTABLE A SUBIR:</label></span>
                                    <select id="mesContSubir" name="mesContSubir" class="form-control form-sm">
                                        <option>01</option><option>02</option><option>03</option><option>04</option>
                                        <option>05</option><option>06</option><option>06</option><option>08</option>
                                        <option>09</option><option>10</option><option>11</option><option>12</option>
                                        <?php
                                        if($mesContSubir != null){?><option selected><?php echo $mesContSubir ?></option><?php }
                                        else{?><option selected value="0" disabled>Seleccione...</option><?php }
                                        ?>
                                    </select>
                                </div>
                                <div align="center" class="input-group" style="margin: auto auto 10px auto">
                                    <input type="hidden" name="queryMtx1" value="true">
                                    <input type="hidden" name="queryMtx2" value="true">
                                    <input type="hidden" name="nomArch1" value="<?php echo $nomArch1 ?>">
                                    <input type="hidden" name="nomArch2" value="<?php echo $nomArch2 ?>">
                                    <input type="hidden" id="pestana" name="pestana" value="1">
                                    <input type="submit" class="btn btn-info btn-sm" value="> > >" title="Ejecutar">
                                </div>
                            </form>
                        </div>
                        <?php
                    }

                    //SI SE SELECCIONÓ AÑO Y MES A PROCESAR:
                    if($anoContSubir != null and $mesContSubir != null)
                    {
                        //PRIMERO BORRAR LO QUE HAYA EN AMETERTER Y AMETERNOM PARA EL AÑO Y MES INGRESADO:
                        $queryP1_1 = "delete from ameterter WHERE terano = '$anoContSubir' AND termes = '$mesContSubir'";
                        $comQryP1_1 = odbc_do($conex_o, $queryP1_1);
                        $queryP1_2 = "delete from ameternom WHERE terano = '$anoContSubir' AND termes = '$mesContSubir'";
                        $comQryP1_2 = odbc_do($conex_o, $queryP1_2);

                        //SI SE BORRARON CORRECTAMENTE LOS DATOS:
                        if($comQryP1_1 == true and $comQryP1_2 == true)
                        {
                            //CONSULTAR TABLA EQUIVALENTE A AMETERTER EN MATRIX:
                            $queryP1_3 = "select * from equipos_000010 WHERE terano = '$anoContSubir' AND termes = '$mesContSubir'";
                            $comQryP1_3 = mysql_query($queryP1_3, $conex);

                            while($dato10 = mysql_fetch_array($comQryP1_3))
                            {
                                $tercodnom = $dato10[1];    $ternit = $dato10[2];       $ternombre = $dato10[3];    $terano = $dato10[4];       $termes = $dato10[5];
                                $teremple = $dato10[6];     $terpen = $dato10[7];       $teringsup = $dato10[8];    $tersuplim = $dato10[9];    $tersalu = $dato10[10];
                                $tersalv = $dato10[11];     $tersalp = $dato10[12];     $terpension = $dato10[13];  $terpensionv = $dato10[14]; $terpensionp = $dato10[15];
                                $tersgss = $dato10[16];     $terintcm = $dato10[17];    $terprepa = $dato10[18];    $terdepen = $dato10[19];    $terafc = $dato10[20];
                                $terafcv = $dato10[21];     $terafcp = $dato10[22];     $terpenvol = $dato10[23];   $terpenvolv = $dato10[24];  $terpenvolp  = $dato10[25];
                                $terfonsol = $dato10[26];   $tercxp = $dato10[27];      $tercerti = $dato10[28];    $terfonsolp = $dato10[29];  $terporsoli  = $dato10[30];
                                $terdepura = $dato10[31];   $tervbffis = $dato10[32];   $tervlroting = $dato10[33]; $tervlrotcom = $dato10[34]; $tervlrnom  = $dato10[35];

                                //INSERTAR CADA REGISTRO EN AMETERTER:
                                $insAmeterter = "insert into ameterter VALUES('$tercodnom','$ternit','$ternombre','$terano','$termes','$teremple','$terpen',
                                                                              '$teringsup','$tersuplim','$tersalu','$tersalv','$tersalp','$terpension','$terpensionv',
                                                                              '$terpensionp','$tersgss','$terintcm','$terprepa','$terdepen','$terafc','$terafcv',
                                                                              '$terafcp','$terpenvol','$terpenvolv','$terpenvolp','$terfonsol','$tercxp','$tercerti',
                                                                              '$terfonsolp','$terporsoli','$terdepura','$tervbffis','$tervlroting','$tervlrotcom',
                                                                              '$tervlrnom')";
                                $comInsAmeterter = odbc_do($conex_o, $insAmeterter);
                            }

                            //CONSULTAR TABLA EQUIVALENTE A AMETERNOM EN MATRIX:
                            $queryP1_4 = "select * from equipos_000011 WHERE terano = '$anoContSubir' AND termes = '$mesContSubir'";
                            $comQryP1_4 = mysql_query($queryP1_4, $conex);

                            while($dato11 = mysql_fetch_array($comQryP1_4))
                            {
                                $tercodnom2 = $dato11[1];    $ternit2 = $dato11[2];       $ternombre2 = $dato11[3];    $terano2 = $dato11[4];       $termes2 = $dato11[5];
                                $teremple2 = $dato11[6];     $terpen2 = $dato11[7];       $teringsup2 = $dato11[8];    $tersuplim2 = $dato11[9];    $tersalu2 = $dato11[10];
                                $tersalv2 = $dato11[11];     $tersalp2 = $dato11[12];     $terpension2 = $dato11[13];  $terpensionv2 = $dato11[14]; $terpensionp2 = $dato11[15];
                                $tersgss2 = $dato11[16];     $terintcm2 = $dato11[17];    $terprepa2 = $dato11[18];    $terdepen2 = $dato11[19];    $terafc2 = $dato11[20];
                                $terafcv2 = $dato11[21];     $terafcp2 = $dato11[22];     $terpenvol2 = $dato11[23];   $terpenvolv2 = $dato11[24];  $terpenvolp2  = $dato11[25];
                                $terfonsol2 = $dato11[26];   $tercxp2 = $dato11[27];      $tercerti2 = $dato11[28];    $terfonsolp2 = $dato11[29];  $terporsoli2  = $dato11[30];
                                $terdepura2 = $dato11[31];   $tervbffis2 = $dato11[32];   $tervlroting2 = $dato11[33]; $tervlrotcom2 = $dato11[34]; $tervlrnom2  = $dato11[35];

                                //INSERTAR CADA REGISTRO EN AMETERNOM:
                                $insAmeternom = "insert into ameternom VALUES('$tercodnom2','$ternit2','$ternombre2','$terano2','$termes2','$teremple2','$terpen2',
                                                                              '$teringsup2','$tersuplim2','$tersalu2','$tersalv2','$tersalp2','$terpension2','$terpensionv2',
                                                                              '$terpensionp2','$tersgss2','$terintcm2','$terprepa2','$terdepen2','$terafc2','$terafcv2',
                                                                              '$terafcp2','$terpenvol2','$terpenvolv2','$terpenvolp2','$terfonsol2','$tercxp2','$tercerti2',
                                                                              '$terfonsolp2','$terporsoli2','$terdepura2','$tervbffis2','$tervlroting2','$tervlrotcom2',
                                                                              '$tervlrnom2')";
                                $comInsAmeternom = odbc_do($conex_o, $insAmeternom);
                            }

                            //SI SE INSERTARON CORRECTAMENTE LOS REGISTROS EN UNIX:
                            if($comInsAmeterter == true and $comInsAmeternom == true)
                            {
                                ?>
                                <h3>Proceso Exitoso</h3>
                                <?php
                            }
							else
							{
								?>
                                <h3>No se ejecuto el proceso</h3>
                                <?php
							}
								
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- PASO 2:-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">PASO 2</a>
                </h4>
            </div>
            <?php
            if($pestana == '2'){?><div id="collapse2" class="panel-collapse collapse in"><?php }
                else{?><div id="collapse2" class="panel-collapse collapse"><?php }
                    ?>
                    <div class="panel-body" id="divPaso2">
                        <div id="loader" class="loader" style="display: none"></div>
                        <h4 class="labelTitulo" style="text-align: center">Subir Facturas y Notas Credito</h4>
                        <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin-left: auto; margin-right: auto">
                            <form method="post">
                                <div class="input-group" style="margin: 10px auto 10px auto; width: 65%">
                                    <span class="input-group-addon input-sm"><label for="anoFacNotas">A&Ntilde;O DE FACTURAS/NOTAS:</label></span>
                                    <select id="anoFacNotas" name="anoFacNotas" class="form-control form-sm" style="width: 148px">
                                        <?php $year = date("Y");
                                        for ($i=2018; $i<=$year; $i++)
                                        {
                                            echo '<option value="'.$i.'">'.$i.'</option>';
                                        }
                                        ?>
                                        <?php
                                        if($anoFacNotas != null){?><option selected><?php echo $anoFacNotas ?></option><?php }
                                        else{?><option selected value="0" disabled>Seleccione...</option><?php }
                                        ?>
                                    </select >
                                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>
                                    <span class="input-group-addon input-sm"><label for="mesFacNotas">MES DE FACTURAS/NOTAS:</label></span>
                                    <select id="mesFacNotas" name="mesFacNotas" class="form-control form-sm">
                                        <option>01</option><option>02</option><option>03</option><option>04</option>
                                        <option>05</option><option>06</option><option>06</option><option>08</option>
                                        <option>09</option><option>10</option><option>11</option><option>12</option>
                                        <?php
                                        if($mesFacNotas != null){?><option selected><?php echo $mesFacNotas ?></option><?php }
                                        else{?><option selected value="0" disabled>Seleccione...</option><?php }
                                        ?>
                                    </select>
                                </div>

                                <div class="input-group" style="margin: 10px auto 10px auto; width: 60%">
                                    <span class="input-group-addon input-sm"><label for="fecIniFacNotas">FECHA INICIAL FACTURAS/NOTAS:</label></span>
                                    <?php
                                    if($fecIniFacNotas != null){?><input type="date" id="fecIniFacNotas" name="fecIniFacNotas" class="form-control form-sm" value="<?php echo $fecIniFacNotas ?>"><?php }
                                    else{?><input type="date" id="fecIniFacNotas" name="fecIniFacNotas" class="form-control form-sm"><?php }
                                    ?>
                                    <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>
                                    <span class="input-group-addon input-sm"><label for="fecFinFacNotas">FECHA FINAL FACTURAS/NOTAS:</label></span>
                                    <?php
                                    if($fecFinFacNotas != null){?><input type="date" id="fecFinFacNotas" name="fecFinFacNotas" class="form-control form-sm" value="<?php echo $fecFinFacNotas ?>"><?php }
                                    else{?><input type="date" id="fecFinFacNotas" name="fecFinFacNotas" class="form-control form-sm"><?php }
                                    ?>
                                </div>

                                <div align="center" class="input-group" style="margin: auto auto 10px auto">
                                    <input type="hidden" id="pestana" name="pestana" value="2">
                                    <input type="submit" class="btn btn-info btn-sm" value="> > >" title="Ejecutar" onclick="document.getElementById('loader').style.display = 'block'">
                                </div>
                            </form>
                        </div>
                        <?php
                        if($accionLocal == 'email')
                        {
                            $mail = new PHPMailer();
                            $mail->SMTPDebug = 1;                                       // Enable verbose debug output
                            $mail->isSMTP();                                            // Set mailer to use SMTP
                            $mail->Host = 'localhost';
                            $mail->Host = 'smtp.gmail.com';                             // Specify main and backup SMTP servers
                            $mail->SMTPAuth = true;                                     // Enable SMTP authentication
                            $mail->Username = 'informatica.clinica@lasamericas.com.co'; // SMTP username
                            $mail->Password = 'pmla2902';                               // SMTP password
                            $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
                            $mail->Port = 465;

                            $mail->Subject = "RETENCION EN LA FUENTE - RETTERCE";
                            $mail->setFrom('informatica.clinica@lasamericas.com.co', 'RETENCION EN LA FUENTE - RETTERCE');
                            $mail->AddAddress("honorarios@doscorreolasamericas.com", "Honorarios");
                            $mail->addCC('informatica.clinica@lasamericas.com.co', 'Informatica');
                            $body = "Se adjunta archivo RETTERCE";
                            //$body .= "Nombre: WILLIAM";
                            //$body .= "Edad: 45";
                            $mail->Body = $body;
                            //adjuntar archivo:
                            $mail->AddAttachment($archivo['tmp_name'],$archivo['name']);
                            if($mail->Send())
                            {
                                ?>
                                <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin-left: auto; margin-right: auto">
                                    <h4 style="text-align: center">Email enviado exitosamente</h4>
                                </div>
                                <?php
                            }
                            else
                            {
                                ?>
                                <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin-left: auto; margin-right: auto">
                                    <h4 style="text-align: center">El Email no pudo ser enviado</h4>
                                </div>
                                <?php
                            }
                        }
                        else
                        {
                            if($anoFacNotas != null and $mesFacNotas != null and $fecIniFacNotas != null and $fecFinFacNotas != null)
                            {
                                $qryDelAmeternot = "delete from ameternot WHERE notano = '$anoFacNotas' AND notmes = '$mesFacNotas'";
                                $comDelAmeternot = odbc_do($conex_o, $qryDelAmeternot);

                                $qryDelAmeterfac = "delete from ameterfac WHERE ano = '$anoFacNotas' AND mes = '$mesFacNotas'";
                                $comDelAmeterfac = odbc_do($conex_o, $qryDelAmeterfac);

                                if($comDelAmeternot == true and $comDelAmeterfac == true)
                                {
                                    ?>
                                    <section>
                                        <?php
                                        $queryP2_1 = "select movfue,movdoc,movano,movmes,movfec,empcod,empnit,empnom,movemp,carfca fca,carfac fac"
                                            ."    from famov,famovdet,cacar,inemp"
                                            ."    where movfue in('25','27','29')"
                                            ."    and movfec between '$fecIniFacNotas' and '$fecFinFacNotas'"
                                            ."    and movanu='0'"
                                            ."    and movfue=movdetfue"
                                            ."    and movdoc=movdetdoc"
                                            ."    and movfue=carfue"
                                            ."    and movdoc=cardoc"
                                            ."    and movemp='E'"
                                            ."    and movcer=empcod"
                                            ."    group by 1,2,3,4,5,6,7,8,9,10,11"
                                            ."    union all "
                                            ."    select movfue,movdoc,movano,movmes,movfec,movcer empcod,movcer empnit,movres empnom,movemp,carfca fca,carfac fac"
                                            ."    from famov,famovdet,cacar"
                                            ."    where movfue in('25','27','29')"
                                            ."    and movfec between '$fecIniFacNotas' and '$fecFinFacNotas'"
                                            ."    and movanu='0'"
                                            ."    and movfue=movdetfue"
                                            ."    and movdoc=movdetdoc"
                                            ."    and movfue=carfue"
                                            ."    and movdoc=cardoc"
                                            ."    and movemp='P'"
                                            ."    group by 1,2,3,4,5,6,7,8,9,10,11"
                                            ."    into temp tmpterrtenot";
                                        $comQryP2_1 = odbc_do($conex_o, $queryP2_1);

                                        $queryP2_2 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,empcod,empnit,empnom,movemp,fca,fac,carfec fecf,cartse tse"
                                            ."    from tmpterrtenot,cacar"
                                            ."    where fca=carfue"
                                            ."    and fac=cardoc"
                                            ."    into temp tmprtenot";
                                        $comQryP2_2 = odbc_do($conex_o, $queryP2_2);

                                        $queryP2_3 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,empcod,empnit,empnom,movemp,fca,fac,fecf,tse,movdetcon con,
                                              movdetnit nit,sum(movdetval-movdetvde) vlr,connitpor por"
                                            ."    FROM tmprtenot,famovdet,outer faconnit"
                                            ."    WHERE movfue=movdetfue"
                                            ."    AND movdoc=movdetdoc"
                                            ."    AND movdetcon=connitcon"
                                            ."    AND movdetnit=connitnit"
                                            ."    AND tse=connittse"
                                            ."    GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,18"
                                            ."    into temp tmprtenot1";
                                        $comQryP2_3 = odbc_do($conex_o, $queryP2_3);

                                        $queryP2_4 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,empcod,empnit,empnom,movemp,fca,fac,fecf,tse,con,nit,vlr,connitpor por"
                                            ."    FROM tmprtenot1,outer faconnit"
                                            ."    WHERE por is null"
                                            ."    AND con=connitcon"
                                            ."    AND nit=connitnit"
                                            ."    AND connittse='*'"
                                            ."    UNION ALL"
                                            ."    SELECT movfue,movdoc,movano,movmes,movfec,carpac,empcod,empnit,empnom,movemp,fca,fac,fecf,tse,con,nit,vlr,por"
                                            ."    FROM tmprtenot1"
                                            ."    WHERE por is not null"
                                            ."    into temp tmprtenot2";
                                        $comQryP2_4 = odbc_do($conex_o, $queryP2_4);

                                        $queryP2_5 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,empcod,empnit,empnom,movemp,fca,fac,fecf,tse,con,nit,vlr,0 por"
                                            ."    FROM tmprtenot2"
                                            ."    WHERE por is null"
                                            ."    UNION ALL"
                                            ."    SELECT movfue,movdoc,movano,movmes,movfec,carpac,empcod,empnit,empnom,movemp,fca,fac,fecf,tse,con,nit,vlr,por"
                                            ."    FROM tmprtenot2"
                                            ."    WHERE por is not null"
                                            ."    into temp tmprtenot3";
                                        $comQryP2_5 = odbc_do($conex_o, $queryP2_5);

                                        $queryP2_6 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,empcod,empnit,empnom,movemp,fca,fac,fecf,tse,con,nit,
                                              vlr,por,(vlr*por)/100 vlrter"
                                            ."    FROM tmprtenot3"
                                            ."    order by 1,2,3,4,5,6,7,8"
                                            ."    into temp tmprtenot4";
                                        $comQryP2_6 = odbc_do($conex_o, $queryP2_6);

                                        //CONSULTAR TMPRTENOT4 (UNLOAD) :
                                        $queryP2_7 = "select * from tmprtenot4 WHERE vlrter <> 0";
                                        $comQryP2_7 = odbc_do($conex_o, $queryP2_7);

                                        //LIMPIAR EQUIPOS_12 DE MATRIX PRIMERO:
                                        $delEquipos12 = 'truncate table equipos_000012';
                                        $comDelEquipos12 = mysql_query($delEquipos12, $conex) or die (mysql_errno()." - en el query: ".$delEquipos12." - ".mysql_error());

                                        //INSERTAR CADA REGISTRO EN MATRIX (EQUIPOS12):
                                        while(odbc_fetch_row($comQryP2_7))
                                        {
                                            $movfue = odbc_result($comQryP2_7, 1);  $movdoc = odbc_result($comQryP2_7, 2);  $movano = odbc_result($comQryP2_7, 3);
                                            $movmes = odbc_result($comQryP2_7, 4);  $movfec = odbc_result($comQryP2_7, 5);  $carpac = odbc_result($comQryP2_7, 6);
                                            $empcod = odbc_result($comQryP2_7, 7);  $empnit = odbc_result($comQryP2_7, 8);  $empnom = odbc_result($comQryP2_7, 9);
                                            $movemp = odbc_result($comQryP2_7, 10); $fca = odbc_result($comQryP2_7, 11);    $fac = odbc_result($comQryP2_7, 12);
                                            $fecf = odbc_result($comQryP2_7, 13);   $tse = odbc_result($comQryP2_7, 14);    $con = odbc_result($comQryP2_7,15);
                                            $nit = odbc_result($comQryP2_7, 16);    $vlr = odbc_result($comQryP2_7,17);     $por = odbc_result($comQryP2_7,18);
                                            $vlrter = odbc_result($comQryP2_7,19);

                                            $queryP2_8 = "insert into equipos_000012 values('','$movfue','$movdoc','$movano','$movmes','$movfec','$carpac','$empcod',
                                                                             '$empnit','$empnom','$movemp','$fca','$fac','$fecf','$tse','$con','$nit','$vlr',
                                                                             '$por','$vlrter')";
                                            $comQryP2_8 = mysql_query($queryP2_8, $conex) or die (mysql_errno()." - en el query: ".$queryP2_8." - ".mysql_error());
                                        }

                                        //ACTUALIZAR AMETERNOT (LOAD) DESDE MATRIX(EQUIPOS_000012):
                                        $queryP2_9 = "select * from equipos_000012";
                                        $comQryP2_9 = mysql_query($queryP2_9, $conex) or die (mysql_errno()." - en el query: ".$queryP2_9." - ".mysql_error());

                                        while($datoEq12 = mysql_fetch_array($comQryP2_9))
                                        {
                                            $movfue12 = $datoEq12[1];   $movdoc12 = $datoEq12[2];   $movano12 = $datoEq12[3];   $movmes12 = $datoEq12[4];
                                            $movfec12 = $datoEq12[5];   $carpac12 = $datoEq12[6];   $fca12 = $datoEq12[11];     $fac12 = $datoEq12[12];
                                            $fecf12 = $datoEq12[13];    $empcod12 = $datoEq12[7];   $empnit12 = $datoEq12[8];   $empnom12 = $datoEq12[9];
                                            $movemp = $datoEq12[10];    $nit12 = $datoEq12[16];     $con12 = $datoEq12[15];     $vlr12 = $datoEq12[17];
                                            $por12 = $datoEq12[18];     $vlrter = $datoEq12[19];

                                            //INSERTAR CADA REGISTRO ENCONTRADO EN AMETERNOT:
                                            $queryP2_10 = "insert into ameternot VALUES('$movfue12','$movdoc12','$movano12','$movmes12','$movfec12','$carpac12',
                                                                         '$fca12','$fac12','$fecf12','$empcod12','$empnit12','$empnom12','$movemp',
                                                                         '$nit12','$con12','$vlr12','$por12','$vlrter','0','0','0')";
                                            $comQryP2_10 = odbc_do($conex_o, $queryP2_10);
                                        }
                                        if($comQryP2_10 == true)
                                        {
                                            ?>
                                            <h4 style="text-align: center">NOTAS CREDITO actualizadas OK</h4>
                                            <?php
                                        }

                                        //Busca las notas en donde la fecha de la factura sea mayor a 20180901
                                        //y busca si el tercero se le hace RteICA:
                                        $queryP2_11 = "select notfue fue,notnot nota,notffa ffa,notdfa dfa,notter ternot,notcon con,sum(notvrt) vrt,sum(notvrt*porica) vlricanot"
                                            ."    from ameternot,ameterica,tetertip"
                                            ."    where notano='$anoFacNotas'"
                                            ."    and notmes='$mesFacNotas'"
                                            ."    and notfef>='20180901'"
                                            ."    and notter=tertipter"
                                            ."    and tertipnit=ameterica.nit"
                                            ."    and ameterica.ica='SI'"
                                            ."    group by 1,2,3,4,5,6"
                                            ."    order by 1,2"
                                            ."    into temp tmpnotica";
                                        $comQryP2_11 = odbc_do($conex_o, $queryP2_11);

                                        $queryP2_12 = "update ameternot"
                                            ." set vlrica=(select vlricanot from tmpnotica where fue=notfue and nota=notnot and ffa=notffa and dfa=notdfa and ternot=notter and con=notcon)"
                                            ." where notano='$anoFacNotas'"
                                            ." and notmes='$mesFacNotas'"
                                            ." and notfef>='20180901'";
                                        $comQryP2_12 = odbc_do($conex_o, $queryP2_12);

                                        $queryP2_13 = "update ameternot set vlrica = 0 where notano = '$anoFacNotas' and notmes = '$mesFacNotas' and vlrica is null";

                                        //===============================================================
                                        //Desde este punto del programa se sube la facturacion xra rtefte:
                                        //===============================================================
                                        $queryP2_14 = "select movfue,movdoc,movano,movmes,movfec,carpac,empcod,empnit,empnom,movemp"
                                            ."    from famov,cacar,inemp"
                                            ."    where movfue in ('20','21','22')"
                                            ."    and movfec between '$fecIniFacNotas' and '$fecFinFacNotas'"
                                            ."    and movanu='0'"
                                            ."    and movfue=carfue"
                                            ."    and movdoc=cardoc"
                                            ."    and movemp='E'"
                                            ."    and movcer=empcod"
                                            ."    union all"
                                            ."    select movfue,movdoc,movano,movmes,movfec,carpac,movcer empcod,movcer empnit,movres empnom,movemp"
                                            ."    from famov,cacar"
                                            ."    where movfue in ('20','21','22')"
                                            ."    and movfec between '$fecIniFacNotas' and '$fecFinFacNotas'"
                                            ."    and movanu='0'"
                                            ."    and movfue=carfue"
                                            ."    and movdoc=cardoc"
                                            ."    and movemp='P'"
                                            ."    into temp tmpterrte";
                                        $comQryP2_14 = odbc_do($conex_o, $queryP2_14);

                                        $queryP2_15 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,empcod,empnit,empnom,movemp,terter,tercon,
                                                      carconval terval,tertse,connitpor por,terreg"
                                            ."    FROM tmpterrte,cacarcon,teter,outer faconnit"
                                            ."    WHERE movfue=carconfue"
                                            ."    AND movdoc=carcondoc"
                                            ."    AND carconcon<>'8888'"
                                            ."    AND carconreg=terreg"
                                            ."    AND tercon=connitcon"
                                            ."    AND terter=connitnit"
                                            ."    AND tertse=connittse"
                                            ."    AND carcontip<>'D'"
                                            ."    into temp tmprte";
                                        $comQryP2_15 = odbc_do($conex_o, $queryP2_15);

                                        $queryP2_16 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,empcod,empnit,empnom,movemp,terter,tercon,
                                                      terval,tertse,connitpor por,terreg"
                                            ."    FROM tmprte,outer faconnit"
                                            ."    WHERE por is null"
                                            ."    AND tercon=connitcon"
                                            ."    AND terter=connitnit"
                                            ."    AND connittse='*'"
                                            ."    UNION ALL"
                                            ."    SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,empcod,empnit,empnom,movemp,terter,tercon,
                                                     terval,tertse,por,terreg"
                                            ."    FROM tmprte"
                                            ."    WHERE por is not null"
                                            ."    into temp tmprte1";
                                        $comQryP2_16 = odbc_do($conex_o, $queryP2_16);

                                        $queryP2_17 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,empcod,empnit,empnom,movemp,terter,tercon,
                                                      terval,tertse,0 por,terreg"
                                            ."    FROM tmprte1"
                                            ."    WHERE por is null"
                                            ."    UNION ALL"
                                            ."    SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,empcod,empnit,empnom,movemp,terter,tercon,
                                                            terval,tertse,por,terreg"
                                            ."    FROM tmprte1"
                                            ."    WHERE por is not null"
                                            ."    into temp tmprte2";
                                        $comQryP2_17 = odbc_do($conex_o, $queryP2_17);

                                        $queryP2_18 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,terreg,empcod,empnit,empnom,movemp,terter,tercon,
                                                      terval,por,(terval*por)/100 vlr,0 porren, porica"
                                            ."    FROM tmprte2,tetertip,outer ameterica"
                                            ."    WHERE terter=tertipter"
                                            ."    and tertipnit=ameterica.nit"
                                            ."    and ameterica.ica='SI'"
                                            ."    into temp tmprte3";
                                        $comQryP2_18 = odbc_do($conex_o, $queryP2_18);

                                        $queryP2_19 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,terreg,empcod,empnit,empnom,movemp,terter,tercon,
                                                      terval,por,vlr,porren, 0 vlrica1,0 porica"
                                            ."    FROM tmprte3"
                                            ."    WHERE porica is null"
                                            ."    UNION ALL"
                                            ."    SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,terreg,empcod,empnit,empnom,movemp,terter,tercon,
                                                            terval,por,vlr,porren,(vlr*porica) vlrica1,porica"
                                            ."    FROM tmprte3"
                                            ."    WHERE porica is not null"
                                            ."    into temp tmprte4";
                                        $comQryP2_19 = odbc_do($conex_o, $queryP2_19);

                                        //CONSULTAR TMPRTE4 (UNLOAD) :
                                        $queryP2_20 = "SELECT movfue,movdoc,movano,movmes,movfec,carpac,terfue,terdoc,terreg,empcod,empnit,empnom,movemp,terter,tercon,
                                                      terval,por,vlr,porren,vlrica1,porica"
                                            ."    FROM tmprte4"
                                            ." order by 1,2,3,4,5,6,7,8";
                                        $comQryP2_20 = odbc_do($conex_o, $queryP2_20);

                                        //LIMPIAR EQUIPOS_13 DE MATRIX PRIMERO:
                                        $delEquipos13 = 'truncate table equipos_000013';
                                        $comDelEquipos13 = mysql_query($delEquipos13, $conex) or die (mysql_errno()." - en el query: ".$delEquipos13." - ".mysql_error());

                                        //INSERTAR CADA REGISTRO EN MATRIX (EQUIPOS13):
                                        while(odbc_fetch_row($comQryP2_20))
                                        {
                                            $movfuetmp4 = odbc_result($comQryP2_20, 1); $movdoctmp4 = odbc_result($comQryP2_20, 2); $movanotmp4 = odbc_result($comQryP2_20, 3);
                                            $movmestmp4 = odbc_result($comQryP2_20, 4); $movfectmp4 = odbc_result($comQryP2_20, 5); $carpactmp4 = odbc_result($comQryP2_20, 6);
                                            $terfuetmp4 = odbc_result($comQryP2_20, 7); $terdoctmp4 = odbc_result($comQryP2_20, 8); $terregtmp4 = odbc_result($comQryP2_20, 9);
                                            $empcodtmp4 = odbc_result($comQryP2_20,10); $empnittmp4 = odbc_result($comQryP2_20,11); $empnomtmp4 = odbc_result($comQryP2_20,12);
                                            $movemptmp4 = odbc_result($comQryP2_20,13); $tertertmp4 = odbc_result($comQryP2_20,14); $tercontmp4 = odbc_result($comQryP2_20,15);
                                            $tervaltmp4 = odbc_result($comQryP2_20,16); $portmp4 = odbc_result($comQryP2_20, 17);   $vlrtmp4 = odbc_result($comQryP2_20, 18);
                                            $porrentmp4 = odbc_result($comQryP2_20,19); $vlrica1tmp4 = odbc_result($comQryP2_20,20);$poricatmp4 = odbc_result($comQryP2_20,21);

                                            $queryP2_21 = "insert into equipos_000013 VALUES('','$movfuetmp4','$movdoctmp4','$movanotmp4','$movmestmp4','$movfectmp4',
                                                                              '$carpactmp4','$terfuetmp4','$terdoctmp4','$terregtmp4','$empcodtmp4','$empnittmp4',
                                                                              '$empnomtmp4','$movemptmp4','$tertertmp4','$tercontmp4','$tervaltmp4','$portmp4',
                                                                              '$vlrtmp4','$porrentmp4','$vlrica1tmp4','$poricatmp4')";
                                            $comQryP2_21 = mysql_query($queryP2_21, $conex) or die (mysql_errno()." - en el query: ".$queryP2_21." - ".mysql_error());
                                        }

                                        //ACTUALIZAR AMETERFAC (LOAD) DESDE MATRIX(EQUIPOS_000013):
                                        $queryP2_22 = "select * from equipos_000013";
                                        $comQryP2_22 = mysql_query($queryP2_22, $conex) or die (mysql_errno()." - en el query: ".$queryP2_22." - ".mysql_error());

                                        while($datoEq13 = mysql_fetch_array($comQryP2_22))
                                        {
                                            $fue13 = $datoEq13[1];      $fac13 = $datoEq13[2];  $ano13 = $datoEq13[3];      $mes13 = $datoEq13[4];      $fec13 = $datoEq13[5];
                                            $pac13 = $datoEq13[6];      $fuec13 = $datoEq13[7]; $cargo13 = $datoEq13[8];    $reg13 = $datoEq13[9];      $codigo13 = $datoEq13[10];
                                            $nit13 = $datoEq13[11];     $res13 = $datoEq13[12]; $emp13 = $datoEq13[13];     $ter13 = $datoEq13[14];     $conc13 = $datoEq13[15];
                                            $vlrc13 = $datoEq13[16];    $por13 = $datoEq13[17]; $vlrter13 = $datoEq13[18];  $porrent13 = $datoEq13[19]; $vlrica13 = $datoEq13[20];
                                            $porica13 = $datoEq13[21];

                                            //INSERTAR CADA REGISTRO ENCONTRADO EN AMETERFAC:
                                            $queryP2_23 = "insert into ameterfac VALUES('$fue13','$fac13','$ano13','$mes13','$fec13','$pac13','$fuec13','$cargo13',
                                                                          '$reg13','$codigo13','$nit13','$res13','$emp13','$ter13','$conc13','$vlrc13','$por13',
                                                                          '$vlrter13','$porrent13','$vlrica13','$porica13')";
                                            $comQryP2_23 = odbc_do($conex_o, $queryP2_23);
                                            //echo 'EL QUERY ='.$queryP2_23;
                                        }
                                        if($comQryP2_23 == true)
                                        {
                                            ?>
                                            <h4 style="text-align: center">FACTURAS actualizadas OK</h4>
                                            <?php
                                        }

                                        $queryP2_24 = "select tertipter ter"
                                            ."    from tetertip, ameteremp"
                                            ."    where tertipnit = nitemp"
                                            ."    into temp tmpter";
                                        $comQryP2_24 = odbc_do($conex_o, $queryP2_24);

                                        $queryP2_25 = "update ameterfac"
                                            ."    set porrent=11"
                                            ."    where fec between '$fecIniFacNotas' and '$fecFinFacNotas'"
                                            ."    and ter in (select tmpter.ter from tmpter where ameterfac.ter=tmpter.ter)"
                                            ."    and emp='E'";
                                        $comQryP2_25 = odbc_do($conex_o, $queryP2_25);

                                        $queryP2_26 = "update ameternot"
                                            ."    set notporren=11"
                                            ."    where notfec between '$fecIniFacNotas' and '$fecFinFacNotas'"
                                            ."    and notter in (select tmpter.ter from tmpter where ameternot.notter=tmpter.ter)"
                                            ."    and notemp='E'";
                                        $comQryP2_26 = odbc_do($conex_o, $queryP2_26);

                                        //=================================================================//

                                        $queryP2_27 = "update ameterter"
                                            ."    set tercodnom="
                                            ."    (select ameternom.tercodnom from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    tersalv=tersalv+"
                                            ."    (select ameternom.tersalv from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    tersalp=(select ameternom.tersalp from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terpensionv=terpensionv+(select ameternom.terpensionv from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terpensionp=(select ameternom.terpensionp from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terintcm=terintcm+(select ameternom.terintcm from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terprepa=terprepa+(select ameternom.terprepa from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terafcv=terafcv+(select ameternom.terafcv from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terafcp=(select ameternom.terafcp from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terpenvolv=terpenvolv+(select ameternom.terpenvolv from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terpenvolp=(select ameternom.terpenvolp from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terfonsol=terfonsol+(select ameternom.terfonsol from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terfonsolp=(select ameternom.terfonsolp from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    terporsoli=(select ameternom.terporsoli from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit),"
                                            ."    tervlrnom=tervlrnom+(select ameternom.tervlrnom from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit)"
                                            ."    where terano='$anoFacNotas'"
                                            ."    and termes='$mesFacNotas'"
                                            ."    and ternit in (select ameternom.ternit from ameternom where ameternom.terano='$anoFacNotas' and ameternom.termes='$mesFacNotas' and ameternom.ternit=ameterter.ternit)";
                                        $comQryP2_27 = odbc_do($conex_o, $queryP2_27);

                                        //=============================================================
                                        //    Genera el reporte para marta verificar si falto algun tercero:
                                        //=============================================================
                                        $queryP2_28 = "SELECT ano,mes,ter,tertipnit nit,tertipnom nomt,emp,sum(vlrter) vlr,porrent por"
                                            ."    FROM ameterfac,tetertip"
                                            ."    WHERE ano='$anoFacNotas'"
                                            ."    AND mes='$mesFacNotas'"
                                            ."    AND ter=tertipter"
                                            ."    group by 1,2,3,4,5,6,8"
                                            ."    UNION ALL"
                                            ."    SELECT notano ano,notmes mes,notter ter,tertipnit nit,tertipnom nomt,notemp emp,sum(notvrt) vlr, notporren por"
                                            ."    FROM ameternot,tetertip"
                                            ."    WHERE notano='$anoFacNotas'"
                                            ."    AND notmes='$mesFacNotas'"
                                            ."    AND notfue='25'"
                                            ."    AND notfef between '$fecIniFacNotas' and '$fecFinFacNotas'"
                                            ."    AND notter=tertipter"
                                            ."    group by 1,2,3,4,5,6,8"
                                            ."    UNION ALL"
                                            ."    SELECT notano ano,notmes mes,notter ter,tertipnit nit,tertipnom nomt,notemp emp,sum(notvrt*-1) vlr,notporren por"
                                            ."    FROM ameternot,tetertip"
                                            ."    WHERE notano='$anoFacNotas'"
                                            ."    AND notmes='$mesFacNotas'"
                                            ."    AND notfue in ('27','29')"
                                            ."    AND notfef between '$fecIniFacNotas' and '$fecFinFacNotas'"
                                            ."    AND notter=tertipter"
                                            ."    group by 1,2,3,4,5,6,8"
                                            ."    order by 1,2,3,4"
                                            ."    into temp pruebater";
                                        $comQryP2_28 = odbc_do($conex_o, $queryP2_28);

                                        //CONSULTAR PRUEBATER (UNLOAD) :
                                        $queryP2_29 = "select ano,mes,nit,emp,sum(vlr) vlr,ameterter.ternit"
                                            ."    from pruebater,outer ameterter"
                                            ."    where nit=ternit"
                                            ."    and ano=terano"
                                            ."    and mes=termes"
                                            ."    group by 1,2,3,4,6"
                                            ."    order by 1,2,3,4";
                                        $comQryP2_29 = odbc_do($conex_o, $queryP2_29);

                                        //LIMPIAR EQUIPOS_14 DE MATRIX PRIMERO:
                                        $delEquipos14 = 'truncate table equipos_000014';
                                        $comDelEquipos14 = mysql_query($delEquipos14, $conex) or die (mysql_errno()." - en el query: ".$delEquipos14." - ".mysql_error());

                                        //INSERTAR CADA REGISTRO EN MATRIX (EQUIPOS14):
                                        while(odbc_fetch_row($comQryP2_29))
                                        {
                                            $anopbater = odbc_result($comQryP2_29, 1); $mespbater = odbc_result($comQryP2_29, 2); $nitpbater = odbc_result($comQryP2_29, 3);
                                            $emppbater = odbc_result($comQryP2_29, 4); $vlrpbater = odbc_result($comQryP2_29, 5); $atnitpbater = odbc_result($comQryP2_29, 6);

                                            $queryP2_30 = "insert into equipos_000014 VALUES('','$anopbater','$mespbater','$nitpbater','$emppbater','$vlrpbater','$atnitpbater')";
                                            $comQryP2_30 = mysql_query($queryP2_30, $conex) or die (mysql_errno()." - en el query: ".$queryP2_30." - ".mysql_error());
                                        }
                                        if($comQryP2_30 == true)
                                        {
                                            ?>
                                            <div id="divExcel" style="text-align: center; border-top: 2px solid #428BCA; border-bottom: 2px solid #428BCA; width: 32%; margin-left: auto; margin-right: auto">
                                                <h4 style="text-align: center">RETTERCE.XLSX generado OK</h4>
                                                <table style="width: 100%; margin-top: 10px; margin-bottom: 10px">
                                                    <tr>
                                                        <td>
                                                            <div class="input-group" style="margin: auto; border: none">
                                                                <form method="post" action="camPer_01.php" enctype="multipart/form-data">
                                                                    <input type="hidden" id="accion" name="accion" value="excel">
                                                                    <input type="hidden" id="accion2" name="accion2" value="email">
                                                                    <input type="hidden" id="accionLocal" name="accionLocal" value="email">
                                                                    <input type="hidden" id="fecIniNotas" name="fecIniNotas" value="<?php echo $fecIniFacNotas ?>">
                                                                    <input type="hidden" id="fecFinNotas" name="fecFinNotas" value="<?php echo $fecFinFacNotas ?>">
                                                                    <input type="hidden" id="pestana" name="pestana" value="2">
                                                                    <input type="image" id="imprimir" title="Exportar a EXCEL" src="http://mtx.lasamericas.com.co/matrix/images/medical/facturacion/excel.png" width="25" height="25"
                                                                           onclick="exportarEx(accion,fecIniNotas,fecFinNotas); return false">
                                                                    &ensp;&ensp;
                                                                    <span class="files3"><input type="file" name="archivo" id="files3"></span>
                                                                    <label for="files3" class="labelArchivo" style="width: 190px"><span>Seleccionar..</span></label>
                                                                    &ensp;&ensp;
                                                                    <input type="image" id="imprimir" title="Enviar por Email" src="http://127.0.0.1/CAMPERUNIX/email2.png" width="25" height="25">
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </section>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                    </div>
                    <?php
            ?>
        </div>




        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">PASO 3</a>
                </h4>
            </div>
            <div id="collapse3" class="panel-collapse collapse">
                <div class="panel-body">Lorem ipsum dolor sit amet, consectetur adipisicing elit,
                    sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</div>
            </div>
        </div>
    </div>



</div>

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
</body>
</html>