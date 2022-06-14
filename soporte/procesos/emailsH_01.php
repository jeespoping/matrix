<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PROCESOS HONORARIOS - MATRIX / UNIX</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="emailsH_style.css" rel="stylesheet">
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
    include("PHPMailerH.php");
    include("class.smtpH.php");
    //require("fpdfH.php");

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
        $conex_o = odbc_connect('facturacion','teradm','1201')  or die("No se realizo conexión con la BD de Facturación");
        $conex_o2 = odbc_connect('cuepag','cuecli','1910')  or die("No se realizo conexión con la BD de Facturación");
    }
    //*/
    //include('../MATRIX/include/root/conex.php'); //publicacion local
    //include('../MATRIX/include/root/comun.php'); //publicacion local
    //include('../CarEmpleado/carEmp_Functions.php'); //publicacion local
    //mysql_select_db('matrix'); //publicacion local
    //$conex = obtenerConexionBD('matrix'); //publicacion local
    //$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación"); //publicacion local
    //$wuse = '0100463'; //publicacion local

    $pestana = $_POST['pestana'];       if($pestana == null){$pestana = 1;}
    $anoActual = date('Y');             $mesActual = date('m');

    ///////// REPORTE 1 //////////////
    $terceroSel = $_POST['tercero'];    $fechaI = $_POST['fechaI']; $fechaF = $_POST['fechaF'];
    ///////// REPORTE 2 //////////////
    $anioI = $_POST['anioI'];           /*$mesI = $_POST['mesI'];*/     $mesF = $_POST['mesF'];
    $terceroSelRep2 = $_POST['terceroRep2'];
    //////// REPORTE 3 //////////////
    $anioR3 = $_POST['anioR3'];         $mesR3 = $_POST['mesR3'];   $terceroSel3 = $_POST['terceroR3'];

    $body = "Cordial Saludo."."<br>";
    $body .= "Adjuntamos archivo con el detalle del pago de honorarios médicos contra recaudo de los siguientes valores relacionados."."<br>";
    $body .= "Cualquier inquietud con gusto será atendida."."<br><br>";
    $body .= "Atentamente:"."<br>";
    $body .= "Honorarios Medicos - Clinica Las Americas"."<br><br>";
    $body .= "Nota: Para abrir el archivo adjunto tener presente realizarlo desde un computador de escritorio o portátil."."<br><br>";

    $body2 = "Cordial Saludo."."<br>";
    $body2 .= "Adjuntamos archivo con el detalle de las deducciones aplicadas a los honorarios médicos del presente mes."."<br>";
    $body2 .= "Cualquier inquietud con gusto será atendida."."<br><br>";
    $body2 .= "Atentamente:"."<br>";
    $body2 .= "Honorarios Medicos - Clinica Las Americas"."<br><br>";
    $body2 .= "Nota: Para abrir el archivo adjunto tener presente realizarlo desde un computador de escritorio o portátil."."<br><br>";

    $body3 = "Cordial Saludo."."<br>";
    $body3 .= "Adjuntamos archivo con el detalle del pago de honorarios médicos por factura física o cuenta de cobro de los siguientes valores relacionados."."<br>";
    $body3 .= "Cualquier inquietud con gusto será atendida."."<br><br>";
    $body3 .= "Atentamente:"."<br>";
    $body3 .= "Honorarios Medicos - Clinica Las Americas"."<br><br>";
    $body3 .= "Nota: Para abrir el archivo adjunto tener presente realizarlo desde un computador de escritorio o portátil."."<br><br>";

    $asunto1 .= "Recaudo - Soporte de Pago Honorarios Médicos ".$mesActual.'/'.$anoActual;
    $asunto2 .= "Deducciones ".$mesActual.'/'.$anoActual;
    $asunto3 .= "Factura Física o Cuenta de Cobro - Soporte de Pago Honorarios Médicos ".$mesActual.'/'.$anoActual;
    ?>
</head>

<body>
<div class="container main">
<div id="loader" class="loader" style="display: none"></div>
    <div class="panel panel-info contenido" style="margin-bottom: 50px">
        <div class="panel-heading encabezado">
            <div class="panel-title titulo1">PROCESOS HONORARIOS</div>
        </div>
    </div>

    <h3 style="text-align: center; margin-bottom: 50px">GENERACION Y ENVIO DE REPORTES</h3>

    <div class="panel-group" id="accordion">

        <!-- REPORTE DE RECAUDO (rautgene.4gl /// TERADM 6-3-22): -->

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">REPORTE DE RECAUDO</a>
                </h4>
            </div>
            <?php
            if($pestana == '1'){?><div id="collapse1" class="panel-collapse collapse in"><?php }
                else{?><div id="collapse1" class="panel-collapse collapse"><?php }
                    ?>
                <div class="panel panel-info" id="divPaso1">
                    <h4 class="labelTitulo">Seleccion de parametros</h4>
                    <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 70%; margin: auto auto 10px auto">
                        <form method="post" action="emailsH_01.php">
                            <div class="input-group" style="margin: 10px auto 10px auto; width: 70%">
                                <span class="input-group-addon input-sm"><label for="fechaI">FECHA INICIAL:</label></span>
                                <?php
                                if($fechaI != null){?><input type="date" id="fechaI" name="fechaI" class="form-control form-sm" value="<?php echo $fechaI ?>"><?php }
                                else{?><input type="date" id="fechaI" name="fechaI" class="form-control form-sm"><?php }
                                ?>
                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>
                                <span class="input-group-addon input-sm"><label for="fechaF">FECHA FINAL:</label></span>
                                <?php
                                if($fechaI != null){?><input type="date" id="fechaF" name="fechaF" class="form-control form-sm" value="<?php echo $fechaF ?>"><?php }
                                else{?><input type="date" id="fechaF" name="fechaF" class="form-control form-sm"><?php }
                                ?>
                            </div>
                            <div align="center" class="input-group" style="margin: auto auto 10px auto; width: 70%">
                                <span class="input-group-addon input-sm"><label for="tercero">TERCERO:</label></span>
                                <select id="tercero" name="tercero" class="form-control form-sm">
                                    <?php
                                    $conempresa = "select tertipnit,tertipnom from tetertip WHERE tertipact = 'S' GROUP BY tertipnit,tertipnom ORDER BY tertipnit ASC";
                                    $err_1 = odbc_do($conex_o, $conempresa);
                                    while($datoempresa = odbc_fetch_row($err_1))
                                    {
                                        $tertipnit = odbc_result($err_1, 1);    $tertipnom = odbc_result($err_1, 2);
                                        $nitNomTercero = $tertipnit.' - '.$tertipnom;
                                        echo "<option>".$nitNomTercero."</option>";
                                    }
                                    ?>
                                    <?php
                                    if($terceroSel != null)
                                    {
                                        ?><option selected="selected"><?php echo $terceroSel ?></option><?php
                                        ?><option>TODOS(*)</option><?php
                                    }
                                    else{?><option selected="selected">TODOS(*)</option><?php }
                                    ?>
                                </select >
                            </div>
                            <div align="center" class="input-group" style="margin: auto auto 10px auto">
                                <input type="hidden" id="pestana" name="pestana" value="1">
                                <input type="submit" id="submit1" name="submit1" class="btn btn-info btn-sm" value="> > >" title="Ejecutar"
                                    onclick="document.getElementById('loader').style.display = 'block'">
                            </div>
                        </form>
                    </div>

                    <?php
                    if(isset($_POST['submit1']))
                    {
                        if ($fechaI != null and $fechaF != null)
                        {
                            $pica = 0.002;
                            $tottergv = 0;
                            $tottergp = 0;
                            $totterpp = 0;
                            $totterep = 0;

                            $totg1 = 0;
                            $totg2 = 0;
                            $totg3 = 0;
                            $totg4 = 0;
                            $totg5 = 0;
                            $totg11 = 0;
                            $tott = 0;

                            //$vlrica = 0;

                            if($terceroSel == 'TODOS(*)')
                            {
                                $query1 = "SELECT tertipter tert,tertipnom,tertipnit nitt"
                                    ."  FROM tetertip"
                                    ."  into temp tercer";
                                $commit1 = odbc_do($conex_o, $query1);
                            }
                            else
                            {
                                $pieces = explode(" - ", $terceroSel);
                                $nitTerceroSel = $pieces[0]; // piece1
                                $nomTerceroSel = $pieces[1]; // piece2

                                $query1 = "SELECT tertipter tert,tertipnom,tertipnit nitt"
                                    ."  FROM tetertip"
                                    ."  WHERE tertipnit='$nitTerceroSel'"
                                    ."  into temp tercer";
                                $commit1 = odbc_do($conex_o, $query1);
                            }

                            //echo 'uno'.'<br>';

                            $query2 = "SELECT cardethis his,cardetnum num,autfue,autdoc,autfec,autdetfca fca,
                                              autdetfac fac,autdetval val,autdetpor por,autdetpag pag,terfec,
                                              terdoc,terfue,pagord,pagfec,tert,nitt autter,tertipnom,tercon,
                                              autdetemp emp,autdetreg reg,porrent,tercco,vlrica"
                                ."  FROM teaut,teautdet,teter,tercer,faconnit,outer tepag,outer facardet,outer ameterfac"
                                ."  WHERE autfue='AU'"
                                ."  AND autfec between '$fechaI' and '$fechaF'"
                                ."  AND autfue=autdetfue"
                                ."  AND autdoc=autdetdoc"
                                ."  AND autdetreg=terreg"
                                ."  AND autdetdoc=pagaut"
                                ."  AND terfue=cardetfue"
                                ."  AND terdoc=cardetdoc"
                                ."  AND tertab='facardet'"
                                ."  AND ternum=cardetreg"
                                ."  AND autter=tert"
                                ."  AND autanu='0'"
                                ."  AND paganu='0'"
                                ."  AND autdetfca=fue"
                                ."  AND autdetfac=fac"
                                ."  AND terfue=fuec"
                                ."  AND terdoc=cargo"
                                ."  AND tercon=conc"
                                ."  AND terter=ter"
                                ."  AND terreg=reg"
                                ."  AND autdetpor=connitpor"
                                ."  AND tercon=connitcon"
                                ."  AND terter=connitnit"
                                ."  AND connittip<>'F'"
                                ."  group by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24"
                                ."  into temp histo";
                            $commit2 = odbc_do($conex_o, $query2);

                            //echo 'dos'.'<br>';

                            $query3 = "select cardetdoo his,cardetnum num,autfue,autdoc,autfec,autdetfca fca,
                                              autdetfac fac,autdetval val,autdetpor por,autdetpag pag,terfec,
                                              terdoc,terfue,pagord,pagfec,tert,nitt autter,tertipnom,tercon,
                                              autdetemp emp,autdetreg reg,porrent,tercco,vlrica"
                                ."  from teaut,teautdet,teter,tercer,faconnit,outer tepag,outer aycardet,outer ameterfac"
                                ."  where autfue='AU'"
                                ."  and autfec between '$fechaI' and '$fechaF'"
                                ."  and autfue=autdetfue"
                                ."  and autdoc=autdetdoc"
                                ."  and autdetreg=terreg"
                                ."  and autdetdoc=pagaut"
                                ."  and terfue=cardetfue"
                                ."  and terdoc=cardetdoc"
                                ."  and tertab='aycardet'"
                                ."  and ternum=cardetreg"
                                ."  and autter=tert"
                                ."  and autanu='0'"
                                ."  and paganu='0'"
                                ."  AND autdetfca=fue"
                                ."  AND autdetfac=fac"
                                ."  AND terfue=fuec"
                                ."  AND terdoc=cargo"
                                ."  AND tercon=conc"
                                ."  AND terter=ter"
                                ."  AND terreg=reg"
                                ."  AND tercon=connitcon"
                                ."  AND terter=connitnit"
                                ."  AND connittip<>'F'"
                                ."  group by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24"
                                ."  into temp ayuda";
                            $commit3 = odbc_do($conex_o, $query3);

                            //echo 'AYUDA'.'<br>';

                            $query4 = "select his,num,autdoc,autfec,fca,fac,val,por,pag,terfec,
                                       terdoc,terfue,pagord,pagfec,pacced,pacap1 ap1,pacap2 ap2,pacnom nom,
                                        tert,autter,tertipnom,tercon,connom,movcer cer,movres res,nitnit,nitnom,
                                        famov.movfec,emp,reg,porrent,tercco,vlrica"
                                ."  from histo,famov,facon,outer inpaci,inemp,conit"
                                ."  where fca=movfue"
                                ."  and fac=movdoc"
                                ."  and his=pachis"
                                ."  and tercon=concod"
                                ."  and movemp='E'"
                                ."  and movcer=empcod"
                                ."  and empnit=nitnit"
                                ."  UNION ALL"
                                ."  select his,num,autdoc,autfec,fca,fac,val,por,pag,terfec,
                                           terdoc,terfue,pagord,pagfec,pacced,pacap1 ap1,pacap2 ap2,pacnom nom,
                                           tert,autter,tertipnom,tercon,connom,movcer cer,movres res,movcer nitnit,
                                           movres nitnom,
                                           famov.movfec,emp,reg,porrent,tercco,vlrica"
                                ."  from histo,famov,facon,outer inpaci"
                                ."  where fca=movfue"
                                ."  and fac=movdoc"
                                ."  and his=pachis"
                                ."  and tercon=concod"
                                ."  and movemp='P'"
                                ."  into temp histo1";
                            $commit4 = odbc_do($conex_o, $query4);

                            //echo 'HISTO1'.'<br>';

                            //                 1   2    3       4    5   6  7    8   9   10     11      12     13    14           15                 16
                            $query5 = "select his,num,autdoc,autfec,fca,fac,val,por,pag,terfec,terdoc,terfue,
                                              pagord,pagfec,aymov.movced pacced,aymov.movape ap1,aymov.movap2 ap2,
                                              aymov.movnom nom,tert,autter,tertipnom,tercon,connom,
                                              famov.movcer cer,famov.movres res,nitnit,nitnom,
                                              famov.movfec,emp,reg,porrent,tercco,vlrica"
                            //                 17               18         19    20       21      22     23          24            25       26  27    28      29     30
                                ."  from ayuda,famov,facon,outer aymov,inemp,conit"
                                ."  where terfue=aymov.movfue"
                                ."  and terdoc=aymov.movdoc"
                                ."  and fca=famov.movfue"
                                ."  and fac=famov.movdoc"
                                ."  and famov.movemp='E'"
                                ."  and tercon=concod"
                                ."  and famov.movcer=empcod"
                                ."  and empnit=nitnit"
                                ."  UNION ALL"
                                ."  select his,num,autdoc,autfec,fca,fac,val,por,pag,terfec,terdoc,terfue,
                                           pagord,pagfec,aymov.movced pacced,aymov.movape ap1,aymov.movap2 ap2,
                                           aymov.movnom nom,tert,autter,tertipnom,tercon,connom,
                                           famov.movcer cer,famov.movres res,famov.movcer nitnit,
                                           famov.movres nitnom,famov.movfec,emp,reg,porrent,tercco,vlrica"
                                ."  from ayuda,famov,facon,outer aymov"
                                ."  where terfue=aymov.movfue"
                                ."  and terdoc=aymov.movdoc"
                                ."  and fca=famov.movfue"
                                ."  and fac=famov.movdoc"
                                ."  and famov.movemp='P'"
                                ."  and tercon=concod"
                                ."  into temp ayuda1";
                            $commit5 = odbc_do($conex_o, $query5);

                            $query6 = "select *"." from histo1 union all select * from ayuda1 into temp final";
                            $commit6 = odbc_do($conex_o, $query6);

                            $query7 = "select * from final";
                            $commit7 = odbc_do($conex_o, $query7);

                            //LIMPIAR TABLA MATRIX:
                            $qryTrunEq15 = "truncate table equipos_000015";
                            $comTrunEq15 = mysql_query($qryTrunEq15, $conex) or die (mysql_errno()." - en el query: ".$qryTrunEq15." - ".mysql_error());

                            while(odbc_fetch_row($commit7))
                            {
                                $rep1His = odbc_result($commit7, 1);        $rep1Num = odbc_result($commit7, 2);    $rep1Doc = odbc_result($commit7, 3);    $rep1Fec = odbc_result($commit7, 4);
                                $rep1Fca = odbc_result($commit7, 5);        $rep1Fac = odbc_result($commit7, 6);    $rep1Val = odbc_result($commit7, 7);    $rep1Por = odbc_result($commit7, 8);
                                $rep1Pag = odbc_result($commit7, 9);        $rep1Fecc = odbc_result($commit7,10);   $rep1Docc = odbc_result($commit7,11);   $rep1Fuec = odbc_result($commit7,12);

                                $rep1Egr = odbc_result($commit7,13);        $rep1Feco = odbc_result($commit7,14);   $rep1Ced = odbc_result($commit7,15);    $rep1Ap1 = odbc_result($commit7,16);
                                $rep1Ap2 = odbc_result($commit7,17);

                                $rep1Nom = odbc_result($commit7,18);        $rep1TerC =odbc_result($commit7,19);    $rep1Ter = odbc_result($commit7,20);    $rep1Nomter = odbc_result($commit7,21);
                                $rep1Con = odbc_result($commit7,22);        $rep1Connom = odbc_result($commit7,23);

                                $rep1Cer = odbc_result($commit7,24);        $rep1Res = odbc_result($commit7,25);    $rep1Nit = odbc_result($commit7,26);

                                $rep1Nnom = odbc_result($commit7,27);       $rep1Fecf = odbc_result($commit7,28);   $rep1Emp = odbc_result($commit7,29);    $rep1Reg = odbc_result($commit7,30);
                                $rep1Rpor = odbc_result($commit7,31);       $rep1Rcco = odbc_result($commit7,32);   $rep1Vlrica = odbc_result($commit7,33);

                                $rep1TerC = trim($rep1TerC);    $rep1Ter = trim($rep1Ter);

                                $rep1Rpor = number_format($rep1Rpor,2); //LIMITAR A 2 DECIMALES EL PORCENTAJE RETFTE Y  REDONDEARLO

                                $totterv = 0;   $val = 0;
                                $totterp = 0;   $pag = 0;
                                $valor1 = 0;
                                $valor2 = 0;
                                $valor3 = 0;
                                $valor4 = 0;
                                $valor5 = 0;
                                $valor11 = 0;
                                $valort = 0;
                                $totv1 = 0;
                                $totv2 = 0;
                                $totv3 = 0;
                                $totv4 = 0;
                                $totv5 = 0;
                                $totv11 = 0;
                                $totvt = 0;

                                $query8 = "select carpac as paci"
                                    ."  from cacar"
                                    ."  where carfue = '$rep1Fca'"
                                    ."  and cardoc = '$rep1Fac'";
                                $commit8 = odbc_do($conex_o, $query8);
                                $paci = odbc_result($commit8, 1);

                                if($rep1Emp == 'E')
                                {
                                    if($rep1Fecf <= '2013-06-30')
                                    {
                                        if($rep1Rpor == '11.00')
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                        }
                                        else
                                        {
                                            $valor1 = ($rep1Pag * $rep1Rpor)/100;   $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                        }
                                    }
                                    else    // <="20130630"
                                    {
                                        if($rep1Rpor == '11.00')
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                        }
                                        else
                                        {
                                            if($rep1Rpor == '10.00')
                                            {
                                                $valor1 = ($rep1Pag * $rep1Rpor)/100;   $valor2 = 0;    $valor3 = 0;
                                                $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            }
                                            else
                                            {
                                                $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                                $valor4 = 0;    $valor5 = ($rep1Pag * $rep1Rpor)/100;   $valor11 = 0;
                                            }
                                        }

                                    }
                                }
                                else
                                {
                                    $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                    $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                }

                                if($rep1Emp == 'E')
                                {
                                    $query9 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit in ('900449401')";
                                    $commit9 = odbc_do($conex_o, $query9);
                                    $wcant = odbc_result($commit9, 1);

                                    if($wcant > 0)
                                    {
                                        if($rep1Fecf >= '2016-01-01')
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }
                                    }

                                    //=======================================================

                                    $query10 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit = '900815348'";
                                    $commit10 = odbc_do($conex_o, $query10);
                                    $wcant = odbc_result($commit10, 1);

                                    if($wcant > 0)
                                    {
                                        if($rep1Fecf >= '2015-11-01' and $rep1Fecf <= '2016-01-31')
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            if($rep1Fecf >= '2017-01-01')
                                            {
                                                $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                                $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                                $rep1Rpor = '11.00';
                                            }
                                            else
                                            {
                                                $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                                $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                                $rep1Rpor = 0;
                                            }
                                        }
                                    }

                                    //========================================================================
                                    //Otro grupo de terceros sociedades que si la factura es mayor a 20170101
                                    //se le aplica el 11 y si no en 0
                                    //========================================================================

                                    $query11 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit in ('900682163','900733796','900812047','900813773','900822849','900823690',
                                                               '900827646','900966983','901038207')";
                                    $commit11 = odbc_do($conex_o, $query11);
                                    $wcant = odbc_result($commit11, 1);

                                    if ($wcant > 0)
                                    {
                                        if($rep1Fecf >= '2017-01-01')
                                        {
                                            if($rep1Ter == '900682163' or $rep1Ter == '900812047' or $rep1Ter == '900966983')
                                            {
                                                if($rep1Fecf <= '2019-10-31')
                                                {
                                                    $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                                    $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                                    $rep1Rpor = '11.00';
                                                }
                                                else
                                                {
                                                    $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                                    $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                                    $rep1Rpor = 0;
                                                }//-- fecf <= "20191031"
                                            }
                                            else //-- ter="900682163"
                                            {
                                                $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                                $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                                $rep1Rpor = '11.00';
                                            }//-- ter="900682163..."
                                        }
                                        else//-- fecf >= "20170101"
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }// -- fecf >= "20170101"
                                    }//-- wcant>0

                                    //==========================================================================

                                    $query12 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit in ('900426496')";
                                    $commit12 = odbc_do($conex_o, $query12);
                                    $wcant = odbc_result($commit12, 1);

                                    if($wcant > 0)
                                    {
                                        if($rep1Fecf <= '2014-11-30')
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }
                                    }

                                    //==========================================================================

                                    $query20 = "select count(*)"
                                        ."  from tetertip"
                                        ."  where tertipter = '$rep1TerC'"
                                        ."  and tertipnit in ('901169975')";
                                    $commit20 = odbc_do($conex_o, $query20);
                                    $wcant = odbc_result($commit20, 1);

                                    if($wcant > 0)
                                    {
                                        if($rep1Fecf <= '20191231')
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }
                                    }
                                    //==========================================================================

                                    //===========================================================================

                                    $query13 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit in ('900459600')";
                                    $commit13 = odbc_do($conex_o, $query13);
                                    $wcant = odbc_result($commit13, 1);

                                    if($wcant > 0)
                                    {
                                        $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                        $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                        $rep1Rpor = 0;
                                    }

                                    //===========================================================================

                                    $query14 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit in ('900831991','900887155')";
                                    $commit14 = odbc_do($conex_o, $query14);
                                    $wcant = odbc_result($commit14, 1);

                                    if($wcant > 0)
                                    {
                                        if($rep1Fecf >= '2015-11-01')
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }
                                    }
									
									/////////////////////// CAMBIO 2021-02-17 /////////////

									

									$wcant  = 0;
									
									$query14 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit in ('900831991')";
                                    $commit14 = odbc_do($conex_o, $query14);
                                    $wcant = odbc_result($commit14, 1);
									
                                    if($wcant > 0)
                                    {
                                        if(($rep1Fecf >= '2020-04-01') && ($rep1Fecf <= '2021-01-31') )
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }
                                    }									
									
					
									
									/////////////////////// FIN CAMBIO 2021-02-17 /////////////		


									/////////////////////// CAMBIO 2021-02-26 /////////////

									

									$wcant  = 0;
									
									$query14 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit in ('70545141')";
                                    $commit14 = odbc_do($conex_o, $query14);
                                    $wcant = odbc_result($commit14, 1);
									
                                    if($wcant > 0)
                                    {
                                        if(($rep1Fecf >= '2020-04-01') && ($rep1Fecf <= '2021-01-31') )
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }
                                    }									
									


									$wcant  = 0;
									
									$query14 = "select count(*)"
                                        . "  from tetertip"
                                        . "  where tertipter = '$rep1TerC'"
                                        . "  and tertipnit in ('71642761')";
                                    $commit14 = odbc_do($conex_o, $query14);
                                    $wcant = odbc_result($commit14, 1);
									
                                    if($wcant > 0)
                                    {
                                        if(($rep1Fecf >= '2020-04-01') && ($rep1Fecf <= '2021-01-31') )
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }
                                    }														
									
									/////////////////////// FIN CAMBIO 2021-02-26 /////////////		
									
									
                                    //========================= NUEVO ====================================

                                    $queryN1 = "select count(*)"
                                        ."  from tetertip"
                                        ."  where tertipter = '$rep1TerC'"
                                        ."  and tertipnit in ('811000586')";
                                    $commitN1 = odbc_do($conex_o, $queryN1);
                                    $wcant = odbc_result($commitN1, 1);

                                    if($wcant > 0)
                                    {
                                        if($rep1Fecf >= '2020-04-01')
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                            $rep1Rpor = 0;
                                        }
                                    }


                                    //======================= FIN NUEVO ==================================

                                    //===========================================================================

                                    $query15 = "select count(*)"
                                        . "  from tercer"
                                        . "  where tert = '$rep1TerC'"
                                        . "  and nitt in ('800115469','800179820','800205837','800214774',
                                                          '811010022','811016292','811023544','900284485',
                                                          '900356064','900591562','900540815')";
                                    $commit15 = odbc_do($conex_o, $query15);
                                    $wcant = odbc_result($commit15, 1);

                                    if($wcant > 0)
                                    {
                                        if($rep1Fecf >= '2013-05-01' and $rep1Fecf <= '2013-08-31')
                                        {
                                            $valor1 = 0;                    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = ($rep1Pag * 0.006);   $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                        else
                                        {
                                            $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                            $valor4 = 0;    $valor5 = 0;    $valor11 = ($rep1Pag * 0.11);
                                            $rep1Rpor = '11.00';
                                        }
                                    }
                                }
                                else
                                {
                                    $valor1 = 0;    $valor2 = 0;    $valor3 = 0;
                                    $valor4 = 0;    $valor5 = 0;    $valor11 = 0;
                                    $rep1Rpor = 0;
                                }

                                /////////////////////// CAMBIO 2020-07-07 /////////////

                                if($rep1Pag < 0)
                                {
                                    //$rep1Vlrica = $pica * (-1);
                                    $rep1Vlrica = $rep1Pag * $pica;
                                }

                                /*
                                   IF (pag<0) THEN
                                    LET vlrica=vlrica*-1
                                   END IF
                                */
                                ////////////////////////////////////////////////////////

                                $valort = $rep1Pag - $valor1 - $valor2 - $valor3 - $valor4 - $valor5 - $valor11 - $rep1Vlrica;

                                $totterv = $totterv + $rep1Val;
                                $totterp = $totterp + $rep1Pag;
                                $tottergv = $tottergv + $rep1Val;
                                $tottergp = $tottergp + $rep1Pag;

                                $totv1 = $totv1 + $valor1;
                                $totv2 = $totv2 + $valor2;
                                $totv3 = $totv3 + $valor3;
                                $totv4 = $totv4 + $valor4;
                                $totv5 = $totv5 + $valor5;
                                $totv11 = $totv11 + $valor11;
                                $totvt = $totvt + $valort;

                                $totg1 = $totg1 + $valor1;  $totg2 = $totg2 + $valor2;  $totg3 = $totg3 + $valor3;
                                $totg4 = $totg4 + $valor4;  $totg5 = $totg5 + $valor5;  $totg11 = $totg11 + $valor11;
                                $tott = $tott + $valort;

                                if($rep1Emp == 'P')
                                {
                                    $totterpp = $totterpp + $rep1Pag;
                                }
                                else
                                {
                                    $totterep = $totterep + $rep1Pag;
                                }

                                $query18 = " select sum(carconval) valt"
                                    ."  FROM cacarcon"
                                    ."  where carconfca = '$rep1Fca'"
                                    ."  and carconfac = '$rep1Fac'"
                                    ."  and carconfue <> carconfca"
                                    ."  and carconter = '$rep1Ter'";
                                $commit18 = odbc_do($conex_o, $query18);
                                $valt = odbc_result($commit18, 1);

                                //ACTUALIZAR TABLA EQUIVALENTE EN MATRIX:
                                //                                                    0       1         2          3         4           5          6          7
                                $qryInsEquipos15 = "insert into equipos_000015 VALUES('','$rep1His','$rep1Num','$rep1Doc','$rep1Fec','$rep1Fac','$rep1Val','$rep1Por',
                                                                '$rep1Pag','$rep1Fecc','$rep1Docc','$rep1Fuec','$rep1Egr','$rep1Feco','$rep1Ced','$rep1Ap1','$rep1Ap2',
                                                                '$rep1Nom','$rep1TerC','$rep1Ter','$rep1Nomter','$rep1Con','$rep1Res','$rep1Connom','$rep1Fecf','$rep1Emp','$rep1Reg',
                                                                '$rep1Rpor','$rep1Rcco','$valor1','$valor2','$valor3','$valor4','$valor5','$valor11','$rep1Vlrica','$valort')";
                                $commitEq15 = mysql_query($qryInsEquipos15, $conex) or die (mysql_errno()." - en el query: ".$qryInsEquipos15." - ".mysql_error());
                            }

                            if($terceroSel == 'TODOS(*)')
                            {
                                $qryTerceros1 = "select * from equipos_000016 WHERE estado = 'on'";
                            }
                            else
                            {
                                $qryTerceros1 = "select * from equipos_000016 WHERE cednit = '$rep1Ter' AND estado = 'on'";
                            }
                            $commitTer1 = mysql_query($qryTerceros1, $conex) or die (mysql_errno()." - en el query: ".$qryTerceros1." - ".mysql_error());

                            $contEmail = 0;

                            //PARA CADA REGISTRO EXISTENTE EN TABLA DE DIRECCIONES DE CORREO EN MATRIX:
                            while($datoTer1 = mysql_fetch_array($commitTer1))
                            {
                                $cedNitMtx = $datoTer1[1];   $nomTerMtx = $datoTer1[2];

                                $Contenido = '<table border="1">';
                                $Contenido .= '<tr>';
                                    $Contenido .= '<td colspan="31">';
                                        $Contenido .= '<label>TERCERO: </label>'.$cedNitMtx.'  '.$nomTerMtx;
                                    $Contenido .= '</td>';
                                $Contenido .= '</tr>';
                                $Contenido .= '<tr>';
                                    $Contenido .= '<td><label>HISTORIA</label></td>        <td><label>INGRESO</label></td>     <td><label>PACIENTE</label></td>
                                                   <td><label>AUTORIZACION</label></td>    <td><label>FECHA AUT</label></td>    <td><label>FACTURA</label></td>
                                                   <td><label>VLR CARGO</label></td>       <td><label>POR</label></td>          <td><label>VLR A PAGAR</label></td>
                                                   <td><label>FECHA CARGO</label></td>     <td><label>CARGO</label></td>        <td><label>FUENTE</label></td>
                                                   <td><label>EGRESO</label></td>          <td><label>FECHA EGRE</label></td>   <td><label>DOCUMENTO</label></td>
                                                   <td><label>NOMBRE PACIENTE FACTURA</label></td>
                                                   <td><label>CONC</label></td>             <td><label>NOMBRE CONCEPTO</label></td>
                                                   <td><label>RESPONSABLE</label></td> <td><label>FECHA FACT</label></td>       <td><label>E/P</label></td>
                                                   <td><label>CODTERCERO</label></td>
                                                   <td><label>TERCERO</label></td>     <td><label>NOM TERCERO</label></td>
                                                   <td><label>%RETFTE</label></td>         <td><label>CCO</label></td>         <td><label>VLRRTE10%</label></td>
                                                   <td><label>VLRRTE11%</label></td>       <td><label>VLRRTE1%</label></td>    <td><label>VLRRTE DEL CREE</label></td>
                                                   <td><label>VLR % RTEFTE</label></td>    <td><label>VLR RTE11%</label></td>  <td><label>VLR ICA</label></td>
                                                   <td><label>VALOR NETO TERCERO</label></td>';
                                $Contenido .= '</tr>';

                                $totterv = 0;   $totterp = 0;       $totv1 = 0; $totv2 = 0; $totv3 = 0; $totv4 = 0; $totv5 = 0; $totv11 = 0;    $totvt = 0;
                                $totIca = 0;    $totReteFte = 0;    $totVlrCree = 0;        $totVlrRte1 = 0;        $totVlrRte11 = 0;   $totVlrRte10 = 0;

                                $qrySelEquipos15 = "select * from equipos_000015 WHERE ter = '$cedNitMtx'";
                                $commitTer2 = mysql_query($qrySelEquipos15, $conex) or die (mysql_errno()." - en el query: ".$qrySelEquipos15." - ".mysql_error());

                                //PARA CADA TERCERO EXISTENTE EN TABLA EQUIVALENTE EN MATRIX:
                                while($datoTer2 = mysql_fetch_array($commitTer2))
                                {
                                    $rep1Ap115 = $datoTer2[15]; $rep1Ap215 = $datoTer2[16]; $rep1Nom15 = $datoTer2[17];
                                    $pac15 = $rep1Ap115.' '.$rep1Ap215.' '.$rep1Nom15;

                                    $rep1His15 = $datoTer2[1];      $rep1Num15 = $datoTer2[2];
                                    $rep1Doc15 = $datoTer2[3];      $rep1Fec15 = $datoTer2[4];      $rep1Fac15 = $datoTer2[5];
                                    $rep1Val15 = $datoTer2[6];      $rep1Por15 = $datoTer2[7];      $rep1Pag15 = $datoTer2[8];
                                    $rep1Fecc15 = $datoTer2[9];     $rep1Docc15 = $datoTer2[10];    $rep1Fuec15 = $datoTer2[11];
                                    $rep1Egr15 = $datoTer2[12];     $rep1Feco15 = $datoTer2[13];    $rep1Ced15 = $datoTer2[14];
                                    $rep1Con15 = $datoTer2[21];     $rep1Connom15 = $datoTer2[23];  $rep1Res15 = $datoTer2[22];
                                    $rep1Fecf15 = $datoTer2[24];    $rep1Emp15 = $datoTer2[25];     $rep1TerC15 = $datoTer2[18];    $rep1Ter15 = $datoTer2[19];
                                    $rep1Nomter15 = $datoTer2[20];  $rep1Rpor15 = $datoTer2[27];    $rep1Rcco15 = $datoTer2[28];
                                    $valor115 = $datoTer2[29];      $valor215 = $datoTer2[30];      $valor315 = $datoTer2[31];
                                    $valor415 = $datoTer2[32];      $valor515 = $datoTer2[33];      $valor1115 = $datoTer2[34];
                                    $vlrica15 = $datoTer2[35];      $valort15 = $datoTer2[36];      //$totalIca = $datoTer2['totalIca'];

                                    $Contenido .= '<tr>';
                                    $Contenido .= '<td>'.$rep1His15.'</td>'.'<td>'.$rep1Num15.'</td>'.'<td>'.$pac15.'</td>';
                                    $Contenido .= '<td>'.$rep1Doc15.'</td>'.'<td>'.$rep1Fec15.'</td>'.'<td>'.$rep1Fac15.'</td>';
                                    $Contenido .= '<td>'.$rep1Val15.'</td>'.'<td>'.$rep1Por15.'</td>'.'<td>'.$rep1Pag15.'</td>';
                                    $Contenido .= '<td>'.$rep1Fecc15.'</td>'.'<td>'.$rep1Docc15.'</td>'.'<td>'.$rep1Fuec15.'</td>';
                                    $Contenido .= '<td>'.$rep1Egr15.'</td>'.'<td>'.$rep1Feco15.'</td>'.'<td>'.$rep1Ced15.'</td>'.'<td>'.$pac15.'</td>';
                                    $Contenido .= '<td>'.$rep1Con15.'</td>'.'<td>'.$rep1Connom15.'</td>'.'<td>'.$rep1Res15.'</td>'.'<td>'.$rep1Fecf15.'</td>';
                                    $Contenido .= '<td>'.$rep1Emp15.'</td>'.'<td>'.$rep1TerC15.'</td>'.'<td>'.$rep1Ter15.'</td>'.'<td>'.$rep1Nomter15.'</td>';
                                    $Contenido .= '<td>'.$rep1Rpor15.'</td>'.'<td>'.$rep1Rcco15.'</td>'.'<td>'.$valor115.'</td>';
                                    $Contenido .= '<td>'.$valor215.'</td>'.'<td>'.$valor315.'</td>'.'<td>'.$valor415.'</td>';
                                    $Contenido .= '<td>'.$valor515.'</td>'.'<td>'.$valor1115.'</td>'.'<td>'.$vlrica15.'</td>';
                                    $Contenido .= '<td>'.$valort15.'</td>';
                                    $Contenido .= '</tr>';

                                    $totterv = $totterv + $rep1Val15;   $totterp = $totterp + $rep1Pag15;   $totv1 = $totv1 + $valor115;
                                    $totv2 = $totv2 + $valor215;        $totv3 = $totv3 + $valor315;        $totv4 = $totv4 + $valor415;
                                    $totv5 = $totv5 + $valor515;        $totv11 = $totv11 + $valor1115;     $totvt = $totvt + $valort15;
                                    $totIca = $totIca + $vlrica15; //TOTAL RETEICA (COLUMNA AG)
                                    $totReteFte = $totReteFte + $valor115 + $valor215 + $valor315 + $valor515 + $valor1115; //TOTAL RETENCIONES
                                    $totVlrCree = $totVlrCree + $valor415; //TOTAL VLRRTE DEL CREE (COLUMNA AD)
                                    $totVlrRte1 = $totVlrRte1 + $valor315; //TOTAL VLRRTE1% (COLUMNA AC)
                                    $totVlrRte11 = $totVlrRte11 + $valor215; //TOTAL VLRRTE11% (COLUMNA AB)
                                    $totVlrRte10 = $totVlrRte10 + $valor115; //TOTAL VLRRTE10% (COLUMNA AA)
                                }

                                $Contenido .= '<tr>';
                                    $Contenido .= '<td>'.'<label>'.'TOTAL TERCERO: '.$cedNitMtx.'</label>'.'</td>'.'<td></td>'.'<td></td>'.'<td></td>'.'<td></td>'.'<td></td>';
                                    $Contenido .= '<td><label></label></td>'.'<td></td>'.'<td><label>'.$totterp.'</label></td>'.'<td><label></label></td>';
                                    $Contenido .= '<td><label>'.$totv2.'</label></td>'.'<td><label>'.$totv3.'</label></td>'.'<td><label>'.$totv4.'</label></td>';
                                    $Contenido .= '<td></td>'.'<td></td>'.'<td></td>'.'<td></td>'.'<td></td>'.'<td></td>'.'<td></td>'.'<td></td>';
                                    $Contenido .= '<td></td>'.'<td></td>'.'<td></td>'.'<td></td>'.'<td></td>'.'<td><label>'.$totVlrRte10.'</label></td>'.'<td><label>'.$totVlrRte11.'</label></td>'.'<td><label>'.$totVlrRte1.'</label></td>'.'<td><label>'.$totVlrCree.'</label></td>';
                                    $Contenido .= '<td><label>'.$totv5.'</label></td>'.'<td><label>'.$totv11.'</label></td>'.'<td><label>'.$totIca.'</label></td>'.'<td><label>'.$totvt.'</label></td>';
                                $Contenido .= '</tr>';
                                $Contenido .= '</table>';

                                //////////// RESUMEN: /////////////
                                $resInfo = '<table class="tblInfo" style="font-size: medium; font-weight: bold">';
                                $resInfo .= '<tr align="center" style="background-color: #F5F5F5;"><td colspan="2"><label>RESUMEN</label></td></tr>';
                                $resInfo .= '<tr style="background-color: #F5F5F5;"><td><label>Tercero:</label></td><td>'.$cedNitMtx.'</td></tr>';
                                $resInfo .= '<tr style="background-color: #F5F5F5;"><td><label>Valor a Pagar: </label></td><td align="right">'.number_format($totterp).'</td></tr>';
                                $resInfo .= '<tr style="background-color: #F5F5F5;"><td><label>Valor RETEFUENTE: </label></td><td align="right">'.number_format($totReteFte).'</td></tr>';
                                $resInfo .= '<tr style="background-color: #F5F5F5;"><td><label>Valor RETEICA: </label></td><td align="right">'.number_format($totIca).'</td></tr>';
                                $resInfo .= '<tr style="background-color: #F5F5F5;"><td><label>Valor Neto Tercero: </label></td><td align="right">'.number_format($totvt).'</td></tr>';
                                $resInfo .= '</table>';

                                ///////////////////// GENERAR Y ENVIAR CORREOS: //////////////////////////

                                if($Contenido != null)
                                {
                                    //GENERAR EL ARCHIVO EN EL SERVIDOR:
                                    ?>
                                    <div style="display: none">
                                        <?php
                                        $files1 = glob('./nuevo/*'); //obtenemos todos los nombres de los ficheros
                                        foreach($files1 as $file1)
                                        {
                                            if(is_file($file1)) unlink($file1); //elimino el fichero
                                        }
                                        //mkdir('./nuevo/',0777);
                                        ?>
                                    </div>
                                    <?php
                                    $nombre_temp = tempnam("./nuevo/","");
                                    $archivo = $nombre_temp.".xls"; //NOMBRE TEMPORAL(AUTOMATICO)
                                    $nomArchivo = 'Reporte_1.xls';  //NOMBRE ARCHIVO(CONFIGURABLE)

                                    //$gestor = fopen($archivo, "w");
                                    $gestor = fopen($archivo,"w+");
                                    fwrite($gestor, $Contenido);
                                    fclose($gestor);

                                    //SELECCIONAR EMAIL(S) DESTINATARIO(S):
                                    $queryMail = "select correo1,correo2,correo3,correo4,nombre,cednit from equipos_000016 WHERE cednit = '$cedNitMtx' AND estado = 'on'";
                                    $commitMail = mysql_query($queryMail, $conex) or die (mysql_errno()." - en el query: ".$queryMail." - ".mysql_error());
                                    $datoMail = mysql_fetch_array($commitMail);
                                    $m11 = $datoMail[0];        $m21 = $datoMail[1];        $m31 = $datoMail[2];  $m41 = $datoMail[3];
                                    $nombreTer = $datoMail[4];  $cedTercero = $datoMail[5];

                                    if($m11 != '0'){$mail1 = $m11;} if($m21 != '0'){$mail2 = $m21;} if($m31 != '0'){$mail3 = $m31;} if($m41 != '0'){$mail4 = $m41;}

                                    ////////////// ENVIAR EL ARCHIVO AUTOMATICAMENTE LEYENDOLO DESDE EL SERVIDOR: //////////////////
                                    $mail = new PHPMailer(true);
                                    $mail->SMTPDebug = 1;                                       // Enable verbose debug output
                                    $mail->isSMTP();                                            // Set mailer to use SMTP
                                    $mail->Host = 'localhost';
                                    $mail->Host = 'smtp.gmail.com';                             // Specify main and backup SMTP servers
                                    $mail->SMTPAuth = true;                                     // Enable SMTP authentication
                                    $mail->Username = 'honorarios@lasamericas.com.co';   // SMTP username
                                    $mail->Password = 'Honorarios2022';                               // SMTP password
                                    $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
                                    $mail->Port = 465;
                                    $mail->isHTML(true);                                        // Set email format to HTML
                                    /////
                                    $mail->Subject = $asunto1;                                   //Asunto
                                    $mail->setFrom('honorarios@lasamericas.com.co','Honorarios Clinica Las Americas');

                                    //SELECCIONAR LOS CORREOS QUE EL TERCERO TENGA REGISTRADO EN MATRIX:
                                    if($mail1 != null){$mail->AddAddress($mail1, $nombreTer);}
                                    if($mail2 != null){$mail->addCC($mail2, $nombreTer);}
                                    if($mail3 != null){$mail->addCC($mail3, $nombreTer);}
                                    if($mail4 != null){$mail->addCC($mail4, $nombreTer);}

                                    if($mail1 != null)
                                    {
                                        //adjuntar texto de cuerpo del correo:
                                        $mail->Body = $body.'<br><br>'.$resInfo;

                                        //adjuntar archivo:
                                        $mail->AddAttachment($archivo,$nomArchivo);

                                        if($totvt != null or $totvt != 0) //SI VALOR TOTAL ES NULO O '0', NO ENVIAR
                                        {
                                            if($mail->Send())
                                            {
                                                $contEmail = $contEmail + 1;
                                                ?>
                                                <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin-left: auto; margin-right: auto">
                                                    <h4 style="text-align: center">Email enviado exitosamente a: <?php echo $cedTercero.'-'.$nombreTer ?></h4>
                                                </div>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin-left: auto; margin-right: auto">
                                                    <h4 style="text-align: center">El Email no pudo ser enviado a: <?php echo $cedTercero.'-'.$nombreTer ?></h4>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                }
                            }

                            ?>
                            <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin: 10px auto 10px auto">
                                <h4 style="text-align: center">Numero total de archivos generados y enviados: <?php echo $contEmail ?></h4>
                            </div>
                            <script>document.getElementById('loader').style.display = 'none'</script>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- REPORTE DE NOTAS (rfteNOTdet.4gl /// CUECIA1 6-1-13): -->

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">REPORTE DE NOTAS</a>
                </h4>
            </div>
            <?php
            if($pestana == '2'){?><div id="collapse2" class="panel-collapse collapse in"><?php }
                else{?><div id="collapse2" class="panel-collapse collapse"><?php }
                    ?>
                <div class="panel panel-info" id="divPaso2">
                    <h4 class="labelTitulo">Seleccion de parametros</h4>
                    <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 70%; margin: auto auto 10px auto">
                        <form method="post" action="emailsH_01.php">
                            <div class="input-group" style="margin: 10px auto 10px auto; width: 70%">
                                <span class="input-group-addon input-sm"><label for="anioI">FECHA INICIAL:</label></span>
                                <input type="date" id="anioI" name="anioI" class="form-control form-sm" style="width: 200px" value="<?php echo $anioI ?>">
                                <!--<select id="anioI" name="anioI" class="form-control form-sm" style="width: 139px">
                                    <option>2018</option>
                                    <option>2019</option>
                                    <?php
                                    if($anioI != null){?><option selected><?php echo $anioI ?></option><?php }
                                    else{?><option selected value="0" disabled>Seleccione...</option><?php }
                                    ?>
                                </select>-->

                                <div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div>

                                <!--
                                <span class="input-group-addon input-sm"><label for="mesI">MES INICIAL:</label></span>
                                <select id="mesI" name="mesI" class="form-control form-sm" style="width: 130px">
                                    <option>01</option><option>02</option><option>03</option><option>04</option>
                                    <option>05</option><option>06</option><option>06</option><option>08</option>
                                    <option>09</option><option>10</option><option>11</option><option>12</option>
                                    <?php
                                    if($mesI != null){?><option selected><?php echo $mesI ?></option><?php }
                                    else{?><option selected value="0" disabled>Seleccione...</option><?php }
                                    ?>
                                </select >

                                <div class="input-group-addon" style="background-color: #ffffff; width: 127px; border: none"></div>
                                <input type="hidden" id="pestana" name="pestana" value="2">
                                -->

                                <span class="input-group-addon input-sm"><label for="mesF">FECHA FINAL:</label></span>
                                <input type="date" id="mesF" name="mesF" class="form-control form-sm" style="width: 200px" value="<?php echo $mesF ?>">
                                <!--<select id="mesF" name="mesF" class="form-control form-sm" style="width: 130px">
                                    <option>01</option><option>02</option><option>03</option><option>04</option>
                                    <option>05</option><option>06</option><option>06</option><option>08</option>
                                    <option>09</option><option>10</option><option>11</option><option>12</option>
                                    <?php
                                    if($mesF != null){?><option selected><?php echo $mesF ?></option><?php }
                                    else{?><option selected value="0" disabled>Seleccione...</option><?php }
                                    ?>
                                </select >-->
                            </div>

                            <table class="input-group" style="margin: 10px auto 10px auto">
                                <tr>
                                    <td>
                                        <div class="input-group" align="left">
                                            <span class="input-group-addon input-sm"><label for="terceroRep2">TERCERO:</label></span>
                                            <select id="terceroRep2" name="terceroRep2" class="form-control form-sm" style="width: 500px">
                                                <?php
                                                $conempresaR2 = "select cednit,nombre from equipos_000016 WHERE estado = 'on' GROUP BY cednit,nombre ORDER BY cednit ASC";
                                                $err_1R2 = mysql_query($conempresaR2, $conex) or die (mysql_errno() . " - en el query: " . $conempresaR2 . " - " . mysql_error());
                                                while($datoempresaRep2 = mysql_fetch_array($err_1R2))
                                                {
                                                    $tertipnitRep2 = $datoempresaRep2[0];    $tertipnomRep2 = $datoempresaRep2[1];
                                                    $nitNomTerceroRep2 = $tertipnitRep2.' - '.$tertipnomRep2;
                                                    echo "<option>".$nitNomTerceroRep2."</option>";
                                                }
                                                ?>
                                                <?php
                                                if($terceroSelRep2 != null)
                                                {
                                                    ?><option selected="selected"><?php echo $terceroSelRep2 ?></option><?php
                                                    ?><option>TODOS(*)</option><?php
                                                }
                                                else{?><option selected="selected">TODOS(*)</option><?php }
                                                ?>
                                            </select >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group-addon" style="background-color: #ffffff; width: 10%; border: none"></div>
                                    </td>
                                    <td>
                                        <div class="input-group" align="right">
                                            <input type="hidden" id="pestana" name="pestana" value="2">
                                            <input type="submit" id="submit2" name="submit2" class="btn btn-info btn-sm" value="> > >" title="Ejecutar">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>

                    <?php
                    if(isset($_POST['submit2']))
                    {
                        if ($anioI != null and $mesF != null)
                        {
                            $query1R2 = "select movdetano ano,movdetmes mes,movdetfue fue,movdetdoc doc,movencfec fec,movencpro pro,movdetfca fca,
                                         movdetdca dca,movdetval val,movdesdes des"
                                . "  from cpmovdet,fpmovenc,outer cpmovdes"
                                . "  where movdetfue in ('40','41','42','45')"
                                . "  and movencfec between '$anioI' and '$mesF'"    //$anioI y $mesF equivalen a: fechaInicial and fechaFinal
                                //. "  and movdetmes between '$mesI' and '$mesF'"
                                . "  and movdetfue = movencfue"
                                . "  and movdetdoc = movencdoc"
                                . "  and movencanu = '0'"
                                . "  and movdetfue = movdesfue"
                                . "  and movdetdoc = movdesdoc"
                                . "  into temp tmpcxp";
                            odbc_do($conex_o2, $query1R2);

                            $piecesR10 = explode(" - ", $terceroSelRep2);
                            $nitTerceroSelRep10 = $piecesR10[0]; // piece1
                            $nomTerceroSelRep10 = $piecesR10[1]; // piece2

                            if($terceroSelRep2 != 'TODOS(*)')
                            {
                                $query2R2 = "select ano,mes,fue,doc,fec,pro,pronit,fca,dca,movencfec fec2,val,des"
                                    . "  from tmpcxp,fpmovenc,outer cppro"
                                    . "  where pronit = '$nitTerceroSelRep10'"
                                    . "  and pronit <> ''"
                                    . "  and fca = movencfue"
                                    . "  and dca = movencdoc"
                                    . "  and pro=procod"
                                    . "  order by ano,mes,fue,doc,fec";
                                $commit2R2 = odbc_do($conex_o2, $query2R2);

                                //LIMPIAR TABLA EQUIVALENTE EN MATRIX:
                                $qryTrunEq18 = "truncate table equipos_000018";
                                $comTrunEq18 = mysql_query($qryTrunEq18, $conex) or die (mysql_errno() . " - en el query: " . $qryTrunEq18 . " - " . mysql_error());
                            }
                            else
                            {
                                $query2R2 = "select ano,mes,fue,doc,fec,pro,pronit,fca,dca,movencfec fec2,val,des"
                                    . "  from tmpcxp,fpmovenc,outer cppro"
                                    . "  where fca = movencfue"
                                    . "  and dca = movencdoc"
                                    . "  and pro=procod"
                                    . "  order by ano,mes,fue,doc,fec";
                                $commit2R2 = odbc_do($conex_o2, $query2R2);

                                //LIMPIAR TABLA EQUIVALENTE EN MATRIX:
                                $qryTrunEq18 = "truncate table equipos_000018";
                                $comTrunEq18 = mysql_query($qryTrunEq18, $conex) or die (mysql_errno() . " - en el query: " . $qryTrunEq18 . " - " . mysql_error());
                            }

                            //ACTUALIZAR TABLA EQUIVALENTE EN MATRIX:
                            while (odbc_fetch_row($commit2R2))
                            {
                                $inmovAno = odbc_result($commit2R2, 1);     $inmovMes = odbc_result($commit2R2, 2);     $inmovFue = odbc_result($commit2R2, 3);
                                $inmovDoc = odbc_result($commit2R2, 4);     $inmovFec = odbc_result($commit2R2, 5);     $inmovPro = odbc_result($commit2R2, 6);
                                $inmovNit = odbc_result($commit2R2, 7);     $inmovFca = odbc_result($commit2R2, 8);     $inmovDca = odbc_result($commit2R2, 9);
                                $inmovFec2 = odbc_result($commit2R2, 10);   $inmovVal = odbc_result($commit2R2, 11);    $inmovDes = odbc_result($commit2R2, 12);

                                if($inmovNit != '')
                                {
                                    $qryInsEquipos18 = "insert into equipos_000018 VALUES('','$inmovAno','$inmovMes','$inmovFue','$inmovDoc','$inmovFec',
                                                                '$inmovPro','$inmovNit','$inmovFca','$inmovDca','$inmovFec2','$inmovVal','$inmovDes')";
                                    $commitEq18 = mysql_query($qryInsEquipos18, $conex) or die (mysql_errno() . " - en el query: " . $qryInsEquipos18 . " - " . mysql_error());
                                }
                            }

                            $contEmail2 = 0;

                            //SOLO PARA TERCEROS REGISTRADOS EN MAESTRO DE CORREOS DE MATRIX
                            $qryTerRep2 = "select * from equipos_000016 WHERE estado = 'on'";
                            $commitTerRep2 = mysql_query($qryTerRep2, $conex) or die (mysql_errno() . " - en el query: " . $qryTerRep2 . " - " . mysql_error());

                            //PARA CADA TERCERO REGISTRADO EN EL MAESTRO DE CORREOS:
                            while ($datoTerRep2 = mysql_fetch_array($commitTerRep2))
                            {
                                $cedNitMtx2 = $datoTerRep2[1];  $nomTerMtx2 = $datoTerRep2[2];

                                $Contenido2 = '<table border="1">';
                                $Contenido2 .= '<tr>';
                                $Contenido2 .= '<td colspan="12">';
                                $Contenido2 .= '<label>TERCERO: </label>'.$cedNitMtx2.'  '.$nomTerMtx2;
                                $Contenido2 .= '</td>';
                                $Contenido2 .= '</tr>';
                                $Contenido2 .= '<tr>';
                                $Contenido2 .= '<td><label>ANO</label></td>             <td><label>MES</label></td>     <td><label>FUE</label></td>
                                                    <td><label>DOC</label></td>         <td><label>FECHA</label></td>   <td><label>CODIGO_PRO</label></td>
                                                    <td><label>N.I.T_PRO</label></td>   <td><label>FUED</label></td>    <td><label>DOCU_D</label></td>
                                                    <td><label>FECHA_DET</label></td>   <td><label>VALOR</label></td>   <td><label>OBSERVACION</label></td>';
                                $Contenido2 .= '</tr>';


                                $qrySelEquipos18 = "select * from equipos_000018 WHERE nit = '$cedNitMtx2'";
                                $commitSelEq18 = mysql_query($qrySelEquipos18, $conex) or die (mysql_errno() . " - en el query: " . $qrySelEquipos18 . " - " . mysql_error());

                                while ($datoEq18 = mysql_fetch_array($commitSelEq18))
                                {
                                    $anoE18 = $datoEq18[1];     $mesE18 = $datoEq18[2];     $fueE18 = $datoEq18[3];
                                    $docE18 = $datoEq18[4];     $fecE18 = $datoEq18[5];     $proE18 = $datoEq18[6];
                                    $nitE18 = $datoEq18[7];     $fcaE18 = $datoEq18[8];     $dcaE18 = $datoEq18[9];
                                    $fec2E18 = $datoEq18[10];   $valE18 = $datoEq18[11];    $desE18 = $datoEq18[12];

                                    $Contenido2 .= '<tr>';
                                    $Contenido2 .= '<td>' . $anoE18 . '</td>' . '<td>' . $mesE18 . '</td>' . '<td>' . $fueE18 . '</td>';
                                    $Contenido2 .= '<td>' . $docE18 . '</td>' . '<td>' . $fecE18 . '</td>' . '<td>' . $proE18 . '</td>';
                                    $Contenido2 .= '<td>' . $nitE18 . '</td>' . '<td>' . $fcaE18 . '</td>' . '<td>' . $dcaE18 . '</td>';
                                    $Contenido2 .= '<td>' . $fec2E18 . '</td>' . '<td>' . $valE18 . '</td>' . '<td>' . $desE18 . '</td>';
                                    $Contenido2 .= '</tr>';
                                }

                                $Contenido2 .= '</table>';

                                //////////// RESUMEN: /////////////
                                $resInfo2 = '<table class="tblInfo" style="font-size: medium; font-weight: bold">';
                                $resInfo2 .= '<tr align="center" style="background-color: #F5F5F5;"><td colspan="2"><label>RESUMEN</label></td></tr>';
                                $resInfo2 .= '<tr style="background-color: #F5F5F5;"><td><label>Tercero:</label></td><td>'.$cedNitMtx2.'</td></tr>';
                                $resInfo2 .= '<tr style="background-color: #F5F5F5;"><td><label>Documento: </label></td><td align="right">'.$dcaE18.'</td></tr>';
                                $resInfo2 .= '<tr style="background-color: #F5F5F5;"><td><label>Fecha: </label></td><td align="right">'.$fec2E18.'</td></tr>';
                                $resInfo2 .= '<tr style="background-color: #F5F5F5;"><td><label>Valor: </label></td><td align="right">'.number_format($valE18).'</td></tr>';
                                $resInfo2 .= '<tr style="background-color: #F5F5F5;"><td><label>Observacion: </label></td><td align="right">'.$desE18.'</td></tr>';
                                $resInfo2 .= '</table>';

                                ///////////////////// GENERAR Y ENVIAR CORREOS: //////////////////////////

                                if ($Contenido2 != null)
                                {
                                    //VACEAR LA CARPETA EN EL SERVIDOR:
                                    ?><div style="display: none">
                                        <?php
                                            $files2 = glob('./reporte2/*'); //obtenemos todos los nombres de los ficheros
                                            foreach($files2 as $file2)
                                            {
                                                if(is_file($file2)) unlink($file2); //elimino el fichero
                                            }
                                            //mkdir('./reporte2/',0777);
                                        ?>
                                    </div><?php

                                    //GENERAR EL ARCHIVO EXCEL EN EL SERVIDOR:
                                    $nombre_temp2 = tempnam("./reporte2/","");
                                    $archivo2 = $nombre_temp2.".xls"; //NOMBRE TEMPORAL(AUTOMATICO)
                                    $nomArchivo2 = 'Reporte_2.xls';  //NOMBRE ARCHIVO(CONFIGURABLE)

                                    $gestor2 = fopen($archivo2, "w");
                                    fwrite($gestor2, $Contenido2);
                                    fclose($gestor2);

                                    ////////////// ENVIAR EL ARCHIVO AUTOMATICAMENTE LEYENDOLO DESDE EL SERVIDOR: //////////////////
                                    $mail2 = new PHPMailer();
                                    $mail2->SMTPDebug = 1;                                       // Enable verbose debug output
                                    $mail2->isSMTP();                                            // Set mailer to use SMTP
                                    $mail2->Host = 'localhost';
                                    $mail2->Host = 'smtp.gmail.com';                             // Specify main and backup SMTP servers
                                    $mail2->SMTPAuth = true;                                     // Enable SMTP authentication
                                    $mail2->Username = 'honorarios@lasamericas.com.co'; // SMTP username
                                    $mail2->Password = 'Honorarios2022';                               // SMTP password
                                    $mail2->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
                                    $mail2->Port = 465;
                                    $mail2->isHTML(true);                                        // Set email format to HTML
                                    /////
                                    $mail2->Subject = $asunto2;                                  //Asunto
                                    $mail2->setFrom('honorarios@lasamericas.com.co','Honorarios Clinica Las Americas');

                                    //SELECCIONAR EMAIL(S) DESTINATARIO(S):
                                    if($terceroSelRep2 == null or $terceroSelRep2 == 'TODOS(*)')
                                    {
                                        $queryMail2 = "select correo1,correo2,correo3,correo4,nombre,cednit from equipos_000016 WHERE cednit = '$cedNitMtx2'";
                                        $commitMail2 = mysql_query($queryMail2, $conex) or die (mysql_errno() . " - en el query: " . $queryMail2 . " - " . mysql_error());
                                        $datoMail2 = mysql_fetch_array($commitMail2);
                                    }
                                    else
                                    {
                                        $piecesR2 = explode(" - ", $terceroSelRep2);
                                        $nitTerceroSelRep2 = $piecesR2[0]; // piece1
                                        $nomTerceroSelRep2 = $piecesR2[1]; // piece2

                                        $queryMail2 = "select correo1,correo2,correo3,correo4,nombre,cednit from equipos_000016 WHERE cednit = '$nitTerceroSelRep2'";
                                        $commitMail2 = mysql_query($queryMail2, $conex) or die (mysql_errno() . " - en el query: " . $queryMail2 . " - " . mysql_error());
                                        $datoMail2 = mysql_fetch_array($commitMail2);
                                    }

                                    $m12 = $datoMail2[0];    $m22 = $datoMail2[1];    $m32 = $datoMail2[2];    $m42 = $datoMail2[3];
                                    $nombreTer2 = $datoMail2[4];$nit17 = $datoMail2[5];

                                    if($m12 != '0'){$mail21 = $m12;} if($m22 != '0'){$mail22 = $m22;} if($m32 != '0'){$mail23 = $m32;} if($m42 != '0'){$mail24 = $m42;}

                                    //SELECCIONAR LOS CORREOS QUE EL TERCERO TENGA REGISTRADO EN MATRIX:
                                    if ($mail21 != null){$mail2->AddAddress($mail21, $nombreTer2); }
                                    if ($mail22 != null){$mail2->addCC($mail22, $nombreTer2); }
                                    if ($mail23 != null){$mail2->addCC($mail23, $nombreTer2); }
                                    if ($mail24 != null){$mail2->addCC($mail24, $nombreTer2); }

                                    if ($mail21 != null)
                                    {
                                        //adjuntar texto de cuerpo del correo:
                                        $mail2->Body = $body2.'<br><br>'.$resInfo2;
                                        //adjuntar archivo:
                                        $mail2->AddAttachment($archivo2,$nomArchivo2);

                                        $qryUlt = "select nit,val from equipos_000018 WHERE nit = '$cedNitMtx2'";
                                        $commitUlt = mysql_query($qryUlt, $conex) or die (mysql_errno() . " - en el query: " . $qryUlt . " - " . mysql_error());
                                        $datoUlt = mysql_fetch_array($commitUlt);
                                        $nitU = $datoUlt[0];    $valU = $datoUlt[1];

                                        if($nitU != null)
                                        {
                                            if($valU != null or $valU != 0)
                                            {
                                                $mail2->Send();
                                                $contEmail2 = $contEmail2 + 1;
                                                ?>
                                                <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin-left: auto; margin-right: auto">
                                                    <h4 style="text-align: center">Email enviado exitosamente a: <?php echo $nit17.'-'.$nombreTer2 ?></h4>
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                            <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin: 10px auto 10px auto">
                                <h4 style="text-align: center">Numero total de archivos generados y enviados: <?php echo $contEmail2 ?></h4>
                            </div>
                            <script>document.getElementById('loader').style.display = 'none'</script>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- REPORTE DE FACTURA FISICA (rfte37hon.4gl /// CUECLI 6-1-19): -->

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">REPORTE DE FACTURA FISICA</a>
                </h4>
            </div>
            <?php
            if($pestana == '3'){?><div id="collapse3" class="panel-collapse collapse in"><?php }
                else{?><div id="collapse3" class="panel-collapse collapse"><?php }
                    ?>
                <div class="panel panel-info" id="divPaso2">
                    <h4 class="labelTitulo">Seleccion de parametros</h4>
                    <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 70%; margin: auto auto 10px auto">
                        <form method="post" action="emailsH_01.php">
                            <div class="input-group" style="margin: 10px auto 10px auto; width: 70%">
                                <span class="input-group-addon input-sm"><label for="anioR3">FECHA INICIAL:</label></span>
                                <?php
                                if($anioR3 != null){?><input type="date" id="anioR3" name="anioR3" class="form-control form-sm" value="<?php echo $anioR3 ?>"><?php }
                                else{?><input type="date" id="anioR3" name="anioR3" class="form-control form-sm"><?php }
                                ?>

                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>

                                <span class="input-group-addon input-sm"><label for="mesR3">FECHA FINAL:</label></span>
                                <?php
                                if($mesR3 != null){?><input type="date" id="mesR3" name="mesR3" class="form-control form-sm" value="<?php echo $mesR3 ?>"><?php }
                                else{?><input type="date" id="mesR3" name="mesR3" class="form-control form-sm"><?php }
                                ?>
                            </div>
                            <div align="center" class="input-group" style="margin: auto auto 10px auto; width: 70%">
                                <span class="input-group-addon input-sm"><label for="terceroR3">NIT:</label></span>
                                <select id="terceroR3" name="terceroR3" class="form-control form-sm">
                                    <?php
                                    $conempresa = "select tertipnit,tertipnom from tetertip WHERE tertipact = 'S' GROUP BY tertipnit,tertipnom ORDER BY tertipnit ASC";
                                    $err_1 = odbc_do($conex_o, $conempresa);
                                    while($datoempresa = odbc_fetch_row($err_1))
                                    {
                                        $tertipnit3 = odbc_result($err_1, 1);    $tertipnom3 = odbc_result($err_1, 2);
                                        $nitNomTercero3 = $tertipnit3.' - '.$tertipnom3;
                                        echo "<option>".$nitNomTercero3."</option>";
                                    }
                                    ?>
                                    <?php
                                    if($terceroSel3 != null)
                                    {
                                        ?><option selected="selected"><?php echo $terceroSel3 ?></option><?php
                                        ?><option>TODOS(*)</option><?php
                                    }
                                    else{?><option selected="selected">TODOS(*)</option><?php }
                                    ?>
                                </select >
                            </div>
                            <div align="center" class="input-group" style="margin: auto auto 10px auto">
                                <input type="hidden" id="pestana" name="pestana" value="3">
                                <input type="submit" id="submit3" name="submit3" class="btn btn-info btn-sm" value="> > >" title="Ejecutar"
                                       onclick="document.getElementById('loader').style.display = 'block'">
                            </div>
                        </form>
                    </div>
                    <?php
                    if(isset($_POST['submit3']))
                    {
                        if($anioR3 != null and $mesR3 != null and $terceroSel3 != null)
                        {
                            if($terceroSel3 == 'TODOS(*)')
                            {
                                $query1R3 = "select comovnit nit,nitnom,comovfue fue,comovdoc doc,comovano ano,
                                                    comovmes mes,sum(comovval) vlrbruto,0 vlrrte,0 vlrneto,0 vlrica"
                                    ."  from cpcomov,conit"
                                    ."  where comovfue = '37'"
                                    ."  and comovfec between '$anioR3' and '$mesR3'"
                                    ."  and comovanu = '0'"
                                    ."  and comovind = '1'"
                                    ."  and comovcue in ('61051001','51051008','51051003','61051101','51053511')"
                                    ."  and comovnit = nitnit"
                                    ."  group by 1,2,3,4,5,6,8,9,10"
                                    ."  UNION ALL"
                                    ."  select comovnit nit,nitnom,comovfue fue,comovdoc doc,comovano ano,
                                               comovmes mes,0 vlrbruto,sum(comovval) vlrrte,0 vlrneto,0 vlrica"
                                    ."  from cpcomov,conit"
                                    ."  where comovfue = '37'"
                                    ."  and comovfec between '$anioR3' and '$mesR3'"
                                    ."  and comovanu = '0'"
                                    ."  and comovind = '2'"
                                    ."  and comovcue in ('23651502','23651508','23651501')"
                                    ."  and comovnit = nitnit"
                                    ."  group by 1,2,3,4,5,6,7,9,10"
                                    ."  UNION ALL"
                                    ."  select comovnit nit,nitnom,comovfue fue,comovdoc doc,comovano ano,
                                               comovmes mes,0 vlrbruto,0 vlrrte,0 vlrneto,sum(comovval) vlrica"
                                    ."  from cpcomov,conit"
                                    ."  where comovfue = '37'"
                                    ."  and comovfec between '$anioR3' and '$mesR3'"
                                    ."  and comovanu = '0'"
                                    ."  and comovind = '2'"
                                    ."  and comovcue = '23680502'"
                                    ."  and comovnit = nitnit"
                                    ."  group by 1,2,3,4,5,6,7,8,9"
                                    ."  UNION ALL"
                                    ."  select comovnit nit,nitnom,comovfue fue,comovdoc doc,comovano ano,
                                               comovmes mes,0 vlrbruto,0 vlrrte,sum(comovval) vlrneto,0 vlrica"
                                    ."  from cpcomov,conit"
                                    ."  where comovfue = '37'"
                                    ."  and comovfec between '$anioR3' and '$mesR3'"
                                    ."  and comovanu = '0'"
                                    ."  and comovind = '2'"
                                    ."  and comovcue in ('23352501','23150101')"
                                    ."  and comovnit = nitnit"
                                    ."  group by 1,2,3,4,5,6,7,8,10"
                                    ."  into temp tmphono";
                                $commit1R3 = odbc_do($conex_o2, $query1R3);
                            }
                            else
                            {
                                $pieces = explode(" - ", $terceroSel3);
                                $nitTerceroSel = $pieces[0]; // piece1
                                $nomTerceroSel = $pieces[1]; // piece2

                                $query1R3 = "select comovnit nit,nitnom,comovfue fue,comovdoc doc,comovano ano,
                                             comovmes mes,sum(comovval) vlrbruto,0 vlrrte,0 vlrneto,0 vlrica"
                                    ."  from cpcomov,conit"
                                    ."  where comovfue = '37'"
                                    ."  and comovfec between '$anioR3' and '$mesR3'"
                                    //."  and comovano = '$anioR3'"
                                    //."  and comovmes = '$mesR3'"
                                    ."  and comovnit = '$nitTerceroSel'"
                                    ."  and comovanu = '0'"
                                    ."  and comovind = '1'"
                                    ."  and comovcue in ('61051001','51051008','51051003','61051101','51053511')"
                                    ."  and comovnit = nitnit"
                                    ."  group by 1,2,3,4,5,6,8,9,10"
                                    ."  UNION ALL"
                                    ."  select comovnit nit,nitnom,comovfue fue,comovdoc doc,comovano ano,
                                               comovmes mes,0 vlrbruto,sum(comovval) vlrrte,0 vlrneto,0 vlrica"
                                    ."  from cpcomov,conit"
                                    ."  where comovfue = '37'"
                                    ."  and comovfec between '$anioR3' and '$mesR3'"
                                    //."  and comovano = '$anioR3'"
                                    //."  and comovmes = '$mesR3'"
                                    ."  and comovnit = '$nitTerceroSel'"
                                    ."  and comovanu = '0'"
                                    ."  and comovind = '2'"
                                    ."  and comovcue in ('23651502','23651508','23651501')"
                                    ."  and comovnit = nitnit"
                                    ."  group by 1,2,3,4,5,6,7,9,10"
                                    ."  UNION ALL"
                                    ."  select comovnit nit,nitnom,comovfue fue,comovdoc doc,comovano ano,
                                               comovmes mes,0 vlrbruto,0 vlrrte,0 vlrneto,sum(comovval) vlrica"
                                    ."  from cpcomov,conit"
                                    ."  where comovfue = '37'"
                                    ."  and comovfec between '$anioR3' and '$mesR3'"
                                    //."  and comovano = '$anioR3'"
                                    //."  and comovmes = '$mesR3'"
                                    ."  and comovnit = '$nitTerceroSel'"
                                    ."  and comovanu = '0'"
                                    ."  and comovind = '2'"
                                    ."  and comovcue in ('23680502')"
                                    ."  and comovnit = nitnit"
                                    ."  group by 1,2,3,4,5,6,7,8,9"
                                    ."  UNION ALL"
                                    ."  select comovnit nit,nitnom,comovfue fue,comovdoc doc,comovano ano,
                                               comovmes mes,0 vlrbruto,0 vlrrte,sum(comovval) vlrneto,0 vlrica"
                                    ."  from cpcomov,conit"
                                    ."  where comovfue='37'"
                                    ."  and comovfec between '$anioR3' and '$mesR3'"
                                    ."  and comovnit = '$nitTerceroSel'"
                                    ."  and comovanu = '0'"
                                    ."  and comovind = '2'"
                                    ."  and comovcue in ('23352501','23150101')"
                                    ."  and comovnit=nitnit"
                                    ."  group by 1,2,3,4,5,6,7,8,10"
                                    ."  into temp tmphono";
                                $commit1R3 = odbc_do($conex_o2, $query1R3);
                            }

                            $query2R3 = "select nit,nitnom,fue,doc,ano,mes,sum(vlrbruto) vlrbruto,sum(vlrrte) vlrrte,
                                                sum(vlrneto) vlrneto,sum(vlrica) vlrica,movdesdes des,movencfac"
                                ."  from tmphono,cpmovdes,fpmovenc"
                                ."  where fue = movdesfue"
                                ."  and doc = movdesdoc"
                                ."  and fue = movencfue"
                                ."  and doc = movencdoc"
                                ."  group by 1,2,3,4,5,6,11,12"
                                ."  order by 1,2,3,4,5,6,10,11,12";
                            $commit2R3 = odbc_do($conex_o2, $query2R3);

                            if($commit2R3)
                            {
                                //LIMPIAR TABLA MATRIX:
                                $qryTrunEq19 = "truncate table equipos_000019";
                                $comTrunEq19 = mysql_query($qryTrunEq19, $conex) or die (mysql_errno()." - en el query: ".$qryTrunEq19." - ".mysql_error());

                                while(odbc_fetch_row($commit2R3))
                                {
                                    $rep3Nit = odbc_result($commit2R3, 1);      $rep3NitNom = odbc_result($commit2R3, 2);   $rep3Fue = odbc_result($commit2R3, 3);
                                    $rep3Doc = odbc_result($commit2R3, 4);      $rep3Ano = odbc_result($commit2R3, 5);      $rep3Mes = odbc_result($commit2R3, 6);
                                    $rep3Vlrbru = odbc_result($commit2R3, 7);   $rep3Vlrrte = odbc_result($commit2R3, 8);   $rep3Vlrneto = odbc_result($commit2R3, 9);
                                    $rep3Vlrica = odbc_result($commit2R3,10);   $rep3Des = odbc_result($commit2R3,11);      $rep3Fac = odbc_result($commit2R3,12);

                                    //ACTUALIZAR TABLA EQUIVALENTE EN MATRIX:
                                    $qryInsEquipos19 = "insert into equipos_000019 VALUES('','$rep3Nit','$rep3NitNom','$rep3Fue','$rep3Doc','$rep3Ano',
                                                                    '$rep3Mes','$rep3Vlrbru','$rep3Vlrrte','$rep3Vlrneto','$rep3Vlrica','$rep3Des','$rep3Fac')";
                                    $commitEq19 = mysql_query($qryInsEquipos19, $conex) or die (mysql_errno()." - en el query: ".$qryInsEquipos19." - ".mysql_error());
                                }

                                if($terceroSel3 == 'TODOS(*)')
                                {
                                    $qryTerceros3 = "select * from equipos_000016 WHERE estado = 'on'";
                                }
                                else
                                {
                                    $qryTerceros3 = "select * from equipos_000016 WHERE cednit = '$nitTerceroSel' AND estado = 'on'";
                                }
                                $commitTer3 = mysql_query($qryTerceros3, $conex) or die (mysql_errno()." - en el query: ".$qryTerceros3." - ".mysql_error());

                                $contEmail3 = 0;

                                //PARA CADA REGISTRO EXISTENTE EN TABLA DE DIRECCIONES DE CORREO EN MATRIX:
                                while($datoTer3 = mysql_fetch_array($commitTer3))
                                {
                                    $cedNitMtx3 = $datoTer3[1];   $nomTerMtx3 = $datoTer3[2];

                                    $Contenido3 = '<table border="1">';
                                    $Contenido3 .= '<tr>';
                                    $Contenido3 .= '<td colspan="12">';
                                    $Contenido3 .= '<label>TERCERO: </label>'.$cedNitMtx3.'  '.$nomTerMtx3;
                                    $Contenido3 .= '</td>';
                                    $Contenido3 .= '</tr>';
                                    $Contenido3 .= '<tr>';
                                    $Contenido3 .= '<td><label>NIT_TERCERO</label></td> <td><label>NOMBRE_TERCERO</label></td>  <td><label>FTE</label></td>
                                                   <td><label>CAUSACION</label></td>    <td><label>MES</label></td>             <td><label>ANO</label></td>
                                                   <td><label>VALOR_BRUTO</label></td>  <td><label>VALOR_RTEFTE</label></td>    <td><label>VALOR_ICA</label></td>
                                                   <td><label>VALOR NETO</label></td>   <td><label>CONCEPTO</label></td>        <td><label>FACTURA</label></td>';
                                    $Contenido3 .= '</tr>';

                                    $qrySelEquipos19 = "select * from equipos_000019 WHERE nit = '$cedNitMtx3'";
                                    $commitSelEq19 = mysql_query($qrySelEquipos19, $conex) or die (mysql_errno()." - en el query: ".$qrySelEquipos19." - ".mysql_error());

                                    $sumVlrBru = 0; $sumVlrRtefte = 0;  $sumVlrNeto = 0; $sumValorIca = 0;

                                    //PARA CADA TERCERO EXISTENTE EN TABLA EQUIVALENTE EN MATRIX:
                                    while($datoEq19 = mysql_fetch_array($commitSelEq19))
                                    {
                                        $nitE19 = $datoEq19[1];         $nitNomE19 = $datoEq19[2];  $fueE19 = $datoEq19[3];
                                        $docE19 = $datoEq19[4];         $anoE19 = $datoEq19[5];     $mesE19 = $datoEq19[6];
                                        $vlrBruE19 = $datoEq19[7];      $vlrRteE19 = $datoEq19[8];  $vlrIcaE19 = $datoEq19[10];
                                        $vlrNetoE19 = $datoEq19[9];    $desE19 = $datoEq19[11];    $facE19 = $datoEq19[12];

                                        $Contenido3 .= '<tr>';
                                            $Contenido3 .= '<td>'.$nitE19.'</td>'.'<td>'.$nitNomE19.'</td>'.'<td>'.$fueE19.'</td>';
                                            $Contenido3 .= '<td>'.$docE19.'</td>'.'<td>'.$mesE19.'</td>'.'<td>'.$anoE19.'</td>';
                                            $Contenido3 .= '<td>'.$vlrBruE19.'</td>'.'<td>'.$vlrRteE19.'</td>'.'<td>'.$vlrIcaE19.'</td>';
                                            $Contenido3 .= '<td>'.$vlrNetoE19.'</td>'.'<td>'.$desE19.'</td>'.'<td>'.$facE19.'</td>';
                                        $Contenido3 .= '</tr>';

                                        $sumVlrBru = $sumVlrBru + $vlrBruE19;       $sumVlrRtefte = $sumVlrRtefte + $vlrRteE19;
                                        $sumVlrNeto = $sumVlrNeto + $vlrNetoE19;    $sumValorIca = $sumValorIca + $vlrIcaE19;
                                    }
                                    $Contenido3 .= '</table>';

                                    //////////// RESUMEN: /////////////
                                    $resInfo3 = '<table class="tblInfo" style="font-size: medium; font-weight: bold">';
                                    $resInfo3 .= '<tr align="center" style="background-color: #F5F5F5;"><td colspan="2"><label>RESUMEN</label></td></tr>';
                                    $resInfo3 .= '<tr style="background-color: #F5F5F5;"><td><label>Tercero:</label></td><td>'.$nitE19.'</td></tr>';
                                    $resInfo3 .= '<tr style="background-color: #F5F5F5;"><td><label>Mes: </label></td><td align="right">'.$mesE19.'</td></tr>';
                                    $resInfo3 .= '<tr style="background-color: #F5F5F5;"><td><label>Año: </label></td><td align="right">'.$anoE19.'</td></tr>';
                                    $resInfo3 .= '<tr style="background-color: #F5F5F5;"><td><label>Total Valor Bruto: </label></td><td align="right">'.number_format($sumVlrBru).'</td></tr>';
                                    $resInfo3 .= '<tr style="background-color: #F5F5F5;"><td><label>Total Valor Rtefte: </label></td><td align="right">'.number_format($sumVlrRtefte).'</td></tr>';
                                    $resInfo3 .= '<tr style="background-color: #F5F5F5;"><td><label>Total RETEICA: </label></td><td align="right">'.number_format($sumValorIca).'</td></tr>';
                                    $resInfo3 .= '<tr style="background-color: #F5F5F5;"><td><label>Total Valor Neto: </label></td><td align="right">'.number_format($sumVlrNeto).'</td></tr>';
                                    $resInfo3 .= '</table>';

                                    ///////////////////// GENERAR Y ENVIAR CORREOS: //////////////////////////

                                    if ($Contenido3 != null)
                                    {
                                        //GENERAR EL ARCHIVO EN EL SERVIDOR:
                                        ?> <div style="display: none">
                                            <?php
                                            $files3 = glob('./reporte3/*'); //obtenemos todos los nombres de los ficheros
                                            foreach($files3 as $file3)
                                            {
                                                if(is_file($file3)) unlink($file3); //elimino el fichero
                                            }
                                            //mkdir('./reporte3/', 0777); ?></div>
                                        <?php
                                        $nombre_temp3 = tempnam("./reporte3/", "");
                                        $archivo3 = $nombre_temp3.".xls"; //NOMBRE TEMPORAL(AUTOMATICO)
                                        $nomArchivo3 = 'Reporte_3.xls';  //NOMBRE ARCHIVO(CONFIGURABLE)

                                        $gestor3 = fopen($archivo3, "w");
                                        fwrite($gestor3, $Contenido3);
                                        fclose($gestor3);

                                        ////////////// ENVIAR EL ARCHIVO AUTOMATICAMENTE LEYENDOLO DESDE EL SERVIDOR: //////////////////
                                        $mail3 = new PHPMailer();
                                        $mail3->SMTPDebug = 1;                                       // Enable verbose debug output
                                        $mail3->isSMTP();                                            // Set mailer to use SMTP
                                        $mail3->Host = 'localhost';
                                        $mail3->Host = 'smtp.gmail.com';                             // Specify main and backup SMTP servers
                                        $mail3->SMTPAuth = true;                                     // Enable SMTP authentication
                                        $mail3->Username = 'honorarios@lasamericas.com.co'; // SMTP username
                                        $mail3->Password = 'Honorarios2022';                               // SMTP password
                                        $mail3->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
                                        $mail3->Port = 465;
                                        $mail3->isHTML(true);                                        // Set email format to HTML
                                        /////
                                        $mail3->Subject = $asunto3;                                  //Asunto
                                        $mail3->setFrom('honorarios@lasamericas.com.co','Honorarios Clinica Las Americas');

                                        //SELECCIONAR EMAIL(S) DESTINATARIO(S):
                                        $queryMail3 = "select correo1,correo2,correo3,correo4,nombre,cednit from equipos_000016 WHERE cednit = '$cedNitMtx3'";
                                        $commitMail3 = mysql_query($queryMail3, $conex) or die (mysql_errno() . " - en el query: " . $queryMail3 . " - " . mysql_error());
                                        $datoMail3 = mysql_fetch_array($commitMail3);

                                        $m13 = $datoMail3[0];    $m23 = $datoMail3[1];    $m33 = $datoMail3[2]; $m43 = $datoMail3[3];
                                        //$mail21 = $datoMail3[0];    $mail22 = $datoMail3[1];    $mail23 = $datoMail3[2]; $mail24 = $datoMail3[3];
                                        $nombreTer2 = $datoMail3[4];$nit17 = $datoMail3[5];

                                        if($m13 != '0'){$mail21 = $m13;} if($m23 != '0'){$mail22 = $m23;} if($m33 != '0'){$mail23 = $m33;} if($m43 != '0'){$mail24 = $m43;}

                                        //SELECCIONAR LOS CORREOS QUE EL TERCERO TENGA REGISTRADO EN MATRIX:
                                        if ($mail21 != null){$mail3->AddAddress($mail21, $nombreTer2); }
                                        if ($mail22 != null){$mail3->addCC($mail22, $nombreTer2); }
                                        if ($mail23 != null){$mail3->addCC($mail23, $nombreTer2); }
                                        if ($mail24 != null){$mail3->addCC($mail24, $nombreTer2); }

                                        if ($mail21 != null)
                                        {
                                            //adjuntar texto de cuerpo del correo:
                                            $mail3->Body = $body3.'<br><br>'.$resInfo3;
                                            //adjuntar archivo:
                                            $mail3->AddAttachment($archivo3, $nomArchivo3);

                                            $qryUlt = "select nit,vlrneto from equipos_000019 WHERE nit = '$cedNitMtx3'";
                                            $commitUlt = mysql_query($qryUlt, $conex) or die (mysql_errno() . " - en el query: " . $qryUlt . " - " . mysql_error());
                                            $datoUlt = mysql_fetch_array($commitUlt);
                                            $nitU = $datoUlt[0];    $vlrNetoU = $datoUlt[1];

                                            if($nitU != null)
                                            {
                                                if($vlrNetoU != null or $vlrNetoU != 0)
                                                {
                                                    $mail3->Send();
                                                    $contEmail3 = $contEmail3 + 1;
                                                    ?>
                                                    <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin-left: auto; margin-right: auto">
                                                        <h4 style="text-align: center">Email enviado exitosamente a: <?php echo $nit17.'-'.$nombreTer2 ?></h4>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                                <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 100%; margin: 10px auto 10px auto">
                                    <h4 style="text-align: center">Numero total de archivos generados y enviados: <?php echo $contEmail3 ?></h4>
                                </div>
                                <script>document.getElementById('loader').style.display = 'none'</script>
                                <?php
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>