<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REPORTES CARACTERIZACION EMPLEADOS - MATRIX</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="RepTal_style.css" rel="stylesheet">
    <script src="RepTal_Js.js"></script>
    <style>
        .container12 {
            display: block;
            position: relative;
            padding-left: 10px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 13px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default checkbox */
        .container12 input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Create a custom checkbox */
        .checkmark12 {
            position: absolute;
            top: 3px;
            left: 355px;
            height: 25px;
            width: 25px;
            background-color: #eee;
        }

        /* On mouse-over, add a grey background color */
        .container12:hover input ~ .checkmark12 {
            background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .container12 input:checked ~ .checkmark12 {
            background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark12:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .container12 input:checked ~ .checkmark12:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .container12 .checkmark12:after {
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }
    </style>
    <!------ ESTILOS: ------>
    <style>
        .select1
        {
            width: 80px;
            background-color: #E3E4E1;
        }

        .tblRegistros
        {
            border: 1px solid #999;
            border-collapse: collapse;
            table-layout: fixed;
            width: 625px;
        }

        .tblRegistros td
        {
            border-bottom: 1px solid #999;
        }

        #tblDatos tr td
        {
            padding-left: 5px;
        }

        .tblRegistros2
        {
            border: 1px solid #999;
            border-collapse: collapse;
            /*table-layout: fixed;*/
            width: 625px;
        }

        .tblRegistros2 td
        {
            border-bottom: 1px solid #999;
        }

        .tblRegistros3
        {
            border: 1px solid #999;
            border-collapse: collapse;
            table-layout: fixed;
            width: 1200px;
        }

        .tblRegistros3 td
        {
            border-bottom: 1px solid #999;
        }

        .tblRegistros4
        {
            border: 1px solid #999;
            border-collapse: collapse;
            table-layout: fixed;
            width: 800px;
        }

        .tblRegistros4 td
        {
            border-bottom: 1px solid #999;
        }



    </style>
    <?php
    include("conex.php");
    include("root/comun.php");
    include('carEmp_Functions.php'); //publicacion local

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
    }
    $fechaActual = date('Y-m-d');
    $fechaActual = '2020-04-17';
    $horaActual = date('H:i:s');
    ?>
</head>

<body>
<div class="container main">
    <div class="panel panel-info contenido">
        <div class="panel-heading encabezado"">
            <div class="panel-title">BUSCAME</div>
        </div>

        <!-- /////////////////////////////////// BUSCAME ///////////////////////////////////// -->
        <div class="panel-group" id="accordion" style="margin-bottom: 20px">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title tabsGen">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse0">BUSQUEDA RAPIDA</a>
                    </h4>
                </div>
            </div>
            <?php
            if($buscarActivo == 1) {?><div id="collapse0" class="panel-collapse collapse in"> <?php }
            else{ ?><div id="collapse0" class="panel-collapse collapse"> <?php }
            ?>

                <form method="post" action="RepTalhuma_01.php">
                    <table align="center" border="0" style="width: 34%; margin-bottom: 10px; margin-top: 10px">
                        <tr align="center">
                            <td style="background-color: #2461B2; color: white"><label for="buscameCod">Codigo</label></td>
                            <td style="background-color: #2461B2; color: white"><label for="buscameCed">Cedula</label></td>
                            <td>&ensp;</td>
                            <td rowspan="2"><input type="submit" name="submit0" id="submit0" value="Buscar"></td>
                        </tr>
                        <tr align="center">
                            <td><input type="text" name="buscameCod" id="buscameCod"></td>
                            <td><input type="text" name="buscameCed" id="buscameCed"></td>
                            <td>&ensp;</td>
                            <td><input type="hidden" name="buscarActivo" id="buscarActivo" value="1"></td>
                        </tr>
                    </table>
                </form>
                <?php
                if(isset($_POST["submit0"]))
                {
                    $codBusca = $_POST['buscameCod'];   $cedBusca = $_POST['buscameCed'];   $buscarActivo = $_POST['buscarActivo'];
                    $codBusca = '%'.$codBusca.'%';

                    if($codBusca != null or $codBusca != '%%'){ $queryBus = "select * from talhuma_000013 WHERE Ideuse LIKE '$codBusca'"; }
                    if($cedBusca != null){ $queryBus = "select * from talhuma_000013 WHERE Ideced = '$cedBusca'"; }

                    $commitBusca = mysql_query($queryBus, $conex) or die (mysql_errno()." - en el query: ".$queryBus." - ".mysql_error());
                    $numBusca = mysql_num_rows($commitBusca);
                    $datoBusca = mysql_fetch_assoc($commitBusca);

                    if($cedBusca == ''){$cedBusca = $datoBusca['Ideced'];}
                    $nom1Busca = $datoBusca['Ideno1'];  $nom2Busca = $datoBusca['Ideno2'];          $ape1Busca = $datoBusca['Ideap1'];  $ape2Busca = $datoBusca['Ideap2'];
                    $codigUsu = $datoBusca['Ideuse'];   $datUsu = explode("-",$codigUsu);           $emprBusca = $datUsu[1];            $empresaBus = obtenerEmpresa($emprBusca,$conex);
                    $carBus = $datoBusca['Ideccg'];     $cargoBusca = obtenerCargo($carBus,$conex); $ccoBusca = $datoBusca['Idecco'];   $cCostBusca = obtenerCcosto($ccoBusca,$conex);
                    $fecInBusca = $datoBusca['Idefin']; $estBusca = $datoBusca['Ideest'];           if($estBusca == 'on'){$estBusca = 'Activo';} else{$estBusca = 'Inactivo';}
                    $fnacBusca = $datoBusca['Idefnc'];  $fecCumple = obtenerCumple($fnacBusca);
                    $extBusca = $datoBusca['Ideext'];   $mailBusca = $datoBusca['Ideeml'];

                    if($numBusca > 0)
                    {
                        ?>
                        <div style="border: solid ;width: 800px; margin-left: auto; margin-right: auto; border-color: #2461B2; border-width: 1px; background-color: #F7F8F5">
                            <table align="center" border="0">
                                <tr style="background-color: #91D4F1">
                                    <td colspan="3" align="center"><label class="lblTitulo"><?php echo $nom1Busca.' '.$nom2Busca.' '.$ape1Busca.' '.$ape2Busca ?></label></td>
                                </tr>
                                <tr style="background-color: #D8EEF8">
                                    <td colspan="3" align="center"><label>Empresa: </label>&ensp;<label class="lblImportante"><?php echo $empresaBus ?></label></td>
                                </tr>
                                <tr>
                                    <td rowspan="10">
                                        <!-- FOTO -->
                                        <div id="divFoto">
                                            <img src="http://mtx.lasamericas.com.co/matrix/images/medical/tal_huma/<?php echo $cedBusca ?>.jpg"
                                                 style="width:200px; height:200px">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Cargo actual:</label></td>
                                    <td>&ensp;<label><?php echo $cargoBusca ?></label></td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Código:</label></td>
                                    <td>&ensp;<label><?php echo $codigUsu ?></label></td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Documento:</label></td>
                                    <td>&ensp;<label><?php echo $cedBusca ?></label></td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Centro de costo:</label></td>
                                    <td>&ensp;<label><?php echo $cCostBusca ?></label></td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Fecha ingreso:</label></td>
                                    <td>&ensp;<label><?php echo $fecInBusca ?></label></td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Estado:</label></td>
                                    <td>&ensp;<label class="lblImportante"><?php echo $estBusca ?></label></td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Fecha cumpleaños:</label></td>
                                    <td>&ensp;<label><?php echo $fecCumple ?></label></td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Extensión:</label></td>
                                    <td>&ensp;<label><?php echo $extBusca ?></label></td>
                                </tr>
                                <tr>
                                    <td>&ensp;<label>Email:</label></td>
                                    <td>&ensp;<label><?php echo $mailBusca ?></label></td>
                                </tr>
                            </table>
                        </div>
                        <?php

                    }
                    else
                    {
                        ?>
                        <div align="center" style="border: solid ;width: 800px; margin-left: auto; margin-right: auto; border-color: #2461B2; border-width: 1px; background-color: #F7F8F5">
                            <label class="lblTitulo">No se encontraron registros con el dato ingresado</label>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>

        <!-- /////////////////////////////////// REPORTE GENERAL ///////////////////////////////////// -->
        <div class="panel-heading encabezado">
            <div class="panel-title">Reporte General Caracterización Empleados</div>
        </div>

        <!-- PARAMETROS: -->
        <form method="post" action="RepTalhuma_01.php">
            <div class="panel-group" id="accordion">
                <!------------------------ IDENTIFICACION GENERAL: ------------------------->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title tabsGen">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">IDENTIFICACION GENERAL</a>
                        </h4>
                    </div>
                </div>
                <div id="collapse1" class="panel-collapse collapse">
                    <h4 class="labelTitulo">Datos Basicos</h4>
                    <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                        <tr>
                            <td><label for="nombre1Empl">PRIMER NOMBRE:</label></td>
                            <td>
                                <select id="selNom1" name="selNom1" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="nombre1Empl" name="nombre1Empl">
                            </td>
                            <td><label for="nombre2Empl">SEGUNDO NOMBRE:</label></td>
                            <td>
                                <select id="selNom2" name="selNom2" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="nombre2Empl" name="nombre2Empl">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="apel1Empl">PRIMER APELLIDO:</label></td>
                            <td>
                                <select id="selApe1" name="selApe1" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="apel1Empl" name="apel1Empl">
                            </td>
                            <td><label for="apel2Empl">SEGUNDO APELLIDO:</label></td>
                            <td>
                                <select id="selApe2" name="selApe2" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="apel2Empl" name="apel2Empl">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="fecNacEmpl">FECHA DE NACIMIENTO:</label></td>
                            <td>
                                <select id="selFnac" name="selFnac" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="fecNacEmpl" name="fecNacEmpl">
                            </td>
                            <td><label for="geneEmpl">GENERO:</label></td>
                            <td>
                                <select id="selGene" name="selGene" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <select id="geneEmpl" name="geneEmpl">
                                    <option value="M">MASCULINO</option>
                                    <option value="F">FEMENINO</option>
                                    <option selected></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="docuEmpl">NUMERO DE CEDULA:</label></td>
                            <td>
                                <select id="selDocu" name="selDocu" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="docuEmpl" name="docuEmpl">
                            </td>
                            <td><label for="codiEmpl">CODIGO DE NOMINA:</label></td>
                            <td>
                                <select id="selCode" name="selCode" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="codiEmpl" name="codiEmpl">
                            </td>
                        </tr>
                        <tr>
                            <td><label for="passEmpl">TIENE PASAPORTE:</label></td>
                            <td>
                                <select id="selPasa" name="selPasa" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <select id="passEmpl" name="passEmpl">
                                    <option value="on">SI</option>
                                    <option value="off">NO</option>
                                    <option selected></option>
                                </select>
                            </td>
                            <td><label for="visaEmpl">TIENE VISA:</label></td>
                            <td>
                                <select id="selVisa" name="selVisa" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            <td>
                                <select id="visaEmpl" name="visaEmpl">
                                    <option value="on">SI</option>
                                    <option value="off">NO</option>
                                    <option selected></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="estCivil">ESTADO CIVIL:</label></td>
                            <td>
                                <select id="selEstc" name="selEstc" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <select id="estCivil" name="estCivil">
                                    <option value="02">Casado(a)</option>
                                    <option value="05">Divorciado(a)</option>
                                    <option value="04">Separado(a)</option>
                                    <option value="01">Soltero(a)</option>
                                    <option value="03">Union Libre</option>
                                    <option value="06">Viudo(a)</option>
                                    <option selected></option>
                                </select>
                            </td>
                            <td><label for="estrato">ESTRATO:</label></td>
                            <td>
                                <select id="selEst" name="selEst" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            <td>
                                <select id="estrato" name="estrato" class="select1">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                    <option>6</option>
                                    <option selected></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="lugNac">LUGAR DE NACIMIENTO</label></td>
                            <td>
                                <select id="selLuNa" name="selLuNa" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="lugNac" name="lugNac">
                            </td>
                            <td><label for="munRes">MUNICIPIO DE RESIDENCIA</label></td>
                            <td>
                                <select id="selMunRes" name="selMunRes" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <select id="munRes" name="munRes">
                                    <?php
                                    $queryMuni = "select Codigo, Nombre from root_000006";
                                    $commitQryMuni = mysql_query($queryMuni, $conex) or die (mysql_errno()." - en el query: ".$queryMuni." - ".mysql_error());
                                    while($datoMuni = mysql_fetch_assoc($commitQryMuni))
                                    {
                                        $codMuni = $datoMuni['Codigo']; $descMuni = $datoMuni['Nombre'];
                                        ?>
                                        <option value="<?php echo $codMuni ?>"><?php echo $descMuni ?></option>
                                        <?php
                                    }
                                    ?>
                                    <option selected></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="barrio">BARRIO</label></td>
                            <td>
                                <select id="selBarrio" name="selBarrio" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <select id="barrio" name="barrio">
                                    <?php
                                    $queryBarr = "select Barcod, Bardes from root_000034 where Barcod NOT IN ('00999','00001')";
                                    $commitQryBarr = mysql_query($queryBarr, $conex) or die (mysql_errno()." - en el query: ".$queryBarr." - ".mysql_error());
                                    while($datoBarr = mysql_fetch_assoc($commitQryBarr))
                                    {
                                        $codBarr = $datoBarr['Barcod']; $descBarr = $datoBarr['Bardes'];
                                        ?>
                                        <option id="optBarr" value="<?php echo $codBarr.'-'.$descBarr ?>"><?php echo $descBarr ?></option>
                                        <?php
                                    }
                                    ?>
                                    <option selected></option>
                                </select>
                            </td>
                            <td><label for="tipoSan">TIPO DE SANGRE</label></td>
                            <td>
                                <select id="selTipoSa" name="selTipoSa" class="select1">
                                    <option>=</option>
                                    <option>LIKE</option>
                                </select>
                            </td>
                            <td>
                                <select id="tipoSan" name="tipoSan">
                                    <option>O positivo</option>
                                    <option>O negativo</option>
                                    <option>A positivo</option>
                                    <option>A negativo</option>
                                    <option>B positivo</option>
                                    <option>B negativo</option>
                                    <option>AB positivo</option>
                                    <option>AB negativo</option>
                                    <option selected></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <!------------------------ EDUCACION: -------------------------------------->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title tabsGen">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">EDUCACION</a>
                        </h4>
                    </div>
                </div>
                <div id="collapse2" class="panel-collapse collapse">
                    <div class="panel-body">
                        <h4 class="labelTitulo">Nivel educativo</h4>
                        <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                            <tr>
                                <td><label for="graEscEmp">GRADO ESCOLAR</label></td>
                                <td>
                                    <select id="selGrEsEm" name="selGrEsEm" class="select1">
                                        <option>=</option>
                                        <option selected>LIKE</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="graEscEmp" name="graEscEmp">
                                        <?php
                                        $queryEscolar = "select Scodes,Scocod from talento_000007 WHERE Scoest = 'on' ORDER BY Scodes ASC ";
                                        $comQryEscolar = mysql_query($queryEscolar, $conex) or die (mysql_errno()." - en el query: ".$queryEscolar." - ".mysql_error());
                                        while($datoEscolar = mysql_fetch_assoc($comQryEscolar))
                                        {
                                            $codGradoEsc = $datoEscolar['Scocod'];  $gradoEscolar = $datoEscolar['Scodes'];
                                            ?><option value="<?php echo $codGradoEsc ?>"><?php echo $gradoEscolar ?></option><?php
                                        }
                                        ?>
                                        <option selected></option>
                                    </select>
                                </td>

                                <td><label for="titObEmp">TITULO OBTENIDO</label></td>
                                <td>
                                    <select id="selTitOb" name="selTitOb" class="select1">
                                        <option>=</option>
                                        <option selected>LIKE</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="titObEmp" name="titObEmp" style="width: 200px">
                                        <?php
                                        $queryInst = "select Nompro from talento_000005 WHERE Activo = 'on' ORDER BY Nompro ASC ";
                                        $comQryInst = mysql_query($queryInst, $conex) or die (mysql_errno()." - en el query: ".$queryInst." - ".mysql_error());
                                        while($datoInst = mysql_fetch_assoc($comQryInst))
                                        {
                                            $nomProfesion = $datoInst['Nompro'];
                                            ?><option><?php echo $nomProfesion ?></option><?php
                                        }
                                        ?>
                                        <option selected></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="nomInst">NOMBRE DE LA INSTITUCION</label></td>
                                <td>
                                    <select id="selNoInst" name="selNoInst" class="select1">
                                        <option>=</option>
                                        <option selected>LIKE</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" id="nomInst" name="nomInst">
                                </td>

                                <td><label for="fecTit">FECHA</label></td>
                                <td>
                                    <select id="selFecTit" name="selFecTit" class="select1">
                                        <option>=</option>
                                        <option>LIKE</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="date" id="fecTit" name="fecTit">
                                </td>
                            </tr>
                        </table>
                        <h4 class="labelTitulo">Manejo de otros idiomas</h4>
                        <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                            <tr>
                                <td><label for="usuIdioma">IDIOMA</label></td>
                                <td>
                                    <select id="selIdioma" name="selIdioma" class="select1">
                                        <option>=</option>
                                        <option>LIKE</option>
                                    </select>
                                </td>
                                <td><input type="text" id="usuIdioma" name="usuIdioma"></td>

                                <td><label for="usuIdiHab">LO HABLA</label></td>
                                <td>
                                    <select id="selIdiHab" name="selIdiHab" class="select1">
                                        <option>=</option>
                                        <option>LIKE</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="usuIdiHab" name="usuIdiHab">
                                        <option value="on">SI</option>
                                        <option value="off">NO</option>
                                        <option selected></option>
                                    </select>
                                </td>

                                <td><label for="usuIdiLee">LO LEE</label></td>
                                <td>
                                    <select id="selIdiLee" name="selIdiLee" class="select1">
                                        <option>=</option>
                                        <option>LIKE</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="usuIdiLee" name="usuIdiLee">
                                        <option value="on">SI</option>
                                        <option value="off">NO</option>
                                        <option selected></option>
                                    </select>
                                </td>

                                <td><label for="usuIdiEsc">LO ESCRIBE</label></td>
                                <td>
                                    <select id="selIdiEsc" name="selIdiEsc" class="select1">
                                        <option>=</option>
                                        <option>LIKE</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="usuIdiEsc" name="usuIdiEsc">
                                        <option value="on">SI</option>
                                        <option value="off">NO</option>
                                        <option selected></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <h4 class="labelTitulo">Estudios actuales</h4>
                        <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                            <tr>
                                <td><label for="usuEstAct">QUE ESTUDIA</label></td>
                                <td>
                                    <select id="selEstAct" name="selEstAct" class="select1">
                                        <option>=</option>
                                        <option selected>LIKE</option>
                                        <option></option>
                                    </select>
                                </td>
                                <td><input type="text" id="usuEstAct" name="usuEstAct"></td>

                                <td><label for="usuEstInst">INSTITUCION EDUCATIVA</label></td>
                                <td>
                                    <select id="selEstInst" name="selEstInst" class="select1">
                                        <option>=</option>
                                        <option selected>LIKE</option>
                                        <option></option>
                                    </select>
                                </td>
                                <td><input type="text" id="usuEstInst" name="usuEstInst"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!------------------------ INFORMACIÓN FAMILIAR: --------------------------->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title tabsGen">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">INFORMACION FAMILIAR</a>
                        </h4>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse">
                        <div class="panel-body" align="center">

                            <!-- //////////////////// DATOS FAMILIARES: ////////////////////////// -->
                            <h4 class="labelTitulo">Datos Familiares</h4>
                            <table class="tblNivE" style="width: 100%; border: none; margin-bottom: 20px">
                                <thead>
                                <tr align="center">
                                    <td colspan="3" style="background-color: white">&ensp;</td>
                                    <td colspan="3" style="width: 660px; background-color: white; color: #001629"><label for="usuVive">CON QUIÉN VIVE</label></td>
                                    <td colspan="3" style="background-color: white">&ensp;</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr align="center">
                                    <td colspan="9">
                                        <select id="usuVive" name="usuVive" style="width: 250px">
                                            <option value="03">SOLO</option>
                                            <option value="02">CON AMIGOS</option>
                                            <option value="01">CON SU FAMILIA</option>
                                            <option selected></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr><td>&ensp;</td></tr>
                                <tr>
                                    <td><label for="usuCabFam">ES CABEZA DE FAMILIA</label></td>
                                    <td>
                                        <select id="selCabFam" name="selCabFam" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="usuCabFam" name="usuCabFam">
                                            <option value="on">SI</option>
                                            <option value="off">NO</option>
                                            <option selected></option>
                                        </select>
                                    </td>
                                    <td>&ensp;<label for="usuNiCar">NIÑOS A CARGO</label></td>
                                    <td>
                                        <select id="selNiCar" name="selNiCar" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input id="usuNiCar" name="usuNiCar" type="number" min="0" style="width: 60px">
                                    </td>
                                    <td><label for="usuAdCar">ADULTOS A CARGO</label></td>
                                    <td>
                                        <select id="selAdCar" name="selAdCar" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input id="usuAdCar" name="usuAdCar" type="number" min="0" style="width: 60px">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <h4 class="labelTitulo">Nucleo Familiar</h4>
                            <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                <tr>
                                    <td><label for="generoNu">GENERO</label></td>
                                    <td>
                                        <select id="selGenNu" name="selGenNu" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="generoNu" name="generoNu">
                                            <option>FEMENINO</option>
                                            <option>MASCULINO</option>
                                            <option selected></option>
                                        </select>
                                    </td>

                                    <td><label for="parNu">PARENTESCO</label></td>
                                    <td>
                                        <select id="selParNu" name="selParNu" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="parNu" name="parNu">
                                            <option value="01">MADRE</option>
                                            <option value="02">PADRE</option>
                                            <option value="03">HERMANO(A)</option>
                                            <option value="010">SOBRINO(A)</option>
                                            <option value="05">TIO(A)</option>
                                            <option value="06">ABRUELO(A)</option>
                                            <option value="07">HIJO(A)</option>
                                            <option value="08">CONYUGE</option>
                                            <option value="09">PRIMO(A)</option>
                                            <option value="011">SUEGRO(A)</option>
                                            <option value="012">CUÑADO(A)</option>
                                            <option value="013">YERNO(A)</option>
                                            <option value="014">NUERA</option>
                                            <option value="015">NIETO(A)</option>
                                            <option selected></option>
                                        </select>
                                    </td>

                                    <td><label for="fnacNu">FECHA NACIMIENTO</label></td>
                                    <td>
                                        <select id="selFecNu" name="selFecNu" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td><input type="date" id="fnacNu" name="fnacNu"></td>
                                </tr>

                                <tr>
                                    <td><label for="niEdNu">NIVEL EDUCATIVO</label></td>
                                    <td>
                                        <select id="selNivNu" name="selNivNu" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="niEdNu" name="niEdNu">
                                            <?php
                                            $queryEscolar = "select Scodes,Scocod from talento_000007 WHERE Scoest = 'on' ORDER BY Scodes ASC ";
                                            $comQryEscolar = mysql_query($queryEscolar, $conex) or die (mysql_errno()." - en el query: ".$queryEscolar." - ".mysql_error());
                                            while($datoEscolar = mysql_fetch_assoc($comQryEscolar))
                                            {
                                                $codGradoEsc = $datoEscolar['Scocod'];  $gradoEscolar = $datoEscolar['Scodes'];
                                                ?><option value="<?php echo $codGradoEsc ?>"><?php echo $gradoEscolar ?></option><?php
                                            }
                                            ?>
                                            <option selected></option>
                                        </select>
                                    </td>

                                    <td><label for="nucOcupa">OCUPACION</label></td>
                                    <td>
                                        <select id="selOcuNu" name="selOcuNu" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="nucOcupa" name="nucOcupa">
                                            <?php
                                            $queryOcupa = "select Ocucod,Ocudes from root_000078 WHERE Ocuest = 'on'";
                                            $comQryOcupa = mysql_query($queryOcupa, $conex) or die (mysql_errno()." - en el query: ".$queryOcupa." - ".mysql_error());
                                            while($datoOcupa = mysql_fetch_assoc($comQryOcupa))
                                            {
                                                $codOcupa = $datoOcupa['Ocucod'];
                                                $desOcupa = $datoOcupa['Ocudes'];
                                                ?><option value="<?php echo $codOcupa ?>"><?php echo $desOcupa ?></option><?php
                                            }
                                            ?>
                                            <option selected></option>
                                        </select>
                                    </td>

                                    <td>
                                        <label for="nuVive">VIVE CON USTED ?</label>
                                    </td>
                                    <td>
                                        <select id="selVivNu" name="selVivNu" class="select1">
                                            <option>=</option>
                                            <option selected>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="nuVive" name="nuVive">
                                            <option>SI</option>
                                            <option>NO</option>
                                            <option selected></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>

                            <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                <tr>
                                    <td><label for="nuDis">PERSONAS CON DISCAPACIDAD</label></td>
                                    <td>
                                        <select id="selDisNu" name="selDisNu" class="select1">
                                            <option>=</option>
                                            <option>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="nuDis" name="nuDis">
                                            <option value="on">SI</option>
                                            <option value="off">NO</option>
                                            <option selected></option>
                                        </select>
                                    </td>

                                    <td><label for="nuMas">TIENE MASCOTA</label></td>
                                    <td>
                                        <select id="selMasNu" name="selMasNu" class="select1">
                                            <option>=</option>
                                            <option>LIKE</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select id="nuMas" name="nuMas">
                                            <option value="on">SI</option>
                                            <option value="off">NO</option>
                                            <option selected></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!------------------------ OTROS ASPECTOS FAMILIARES: ---------------------->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title tabsGen">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">OTROS ASPECTOS FAMILIARES</a>
                        </h4>
                        <div id="collapse4" class="panel-collapse collapse">
                            <div class="panel-body" align="center">
                                <!-- //////////////////// INFORMACION FAMILIAR ADICIONAL - SALUD: ////////////////////////// -->
                                <h4 class="labelTitulo">Salud</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td><label for="epsAct">EPS actual</label></td>
                                        <td>
                                            <select id="selEpsAct" name="selEpsAct" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="epsAct" name="epsAct">
                                                <?php
                                                $queryEps = "select Epscod,Epsnom from root_000073 WHERE Epsest = 'on'";
                                                $commitQryEps = mysql_query($queryEps, $conex) or die (mysql_errno()." - en el query: ".$queryEps." - ".mysql_error());
                                                while($datoEps = mysql_fetch_assoc($commitQryEps))
                                                {
                                                    $codEps = $datoEps['Epscod']; $desEps = $datoEps['Epsnom'];
                                                    ?><option value="<?php echo $codEps ?>"><?php echo $codEps.'-'.$desEps ?></option><?php
                                                }
                                                ?>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td><label for="epsCom">Plan de salud complementario</label></td>
                                        <td>
                                            <select id="selEpsCom" name="selEpsCom" class="select1">
                                                <option>=</option>
                                                <option selected>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" id="epsCom" name="epsCom">
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// INFORMACION FAMILIAR ADICIONAL - GASTOS: ////////////////////////// -->
                                <h4 class="labelTitulo">Aspectos principales en los que usted gasta sus ingresos</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="gasViv">Vivienda - Arriendo</label>&ensp;</td>
                                        <td>
                                            <select id="selGasViv" name="selGasViv" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasViv" name="gasViv">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="gasCuo">Vivienda - Pago cuotas crédito</label>&ensp;</td>
                                        <td>
                                            <select id="selGasCuo" name="selGasCuo" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasCuo" name="gasCuo">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="gasAli">Alimentacion</label>&ensp;</td>
                                        <td>
                                            <select id="selGasAli" name="selGasAli" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasAli" name="gasAli">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="gasSer">Servicios Públicos</label>&ensp;</td>
                                        <td>
                                            <select id="selGasSer" name="selGasSer" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasSer" name="gasSer">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="gasTra">Transporte</label>&ensp;</td>
                                        <td>
                                            <select id="selGasTra" name="selGasTra" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasTra" name="gasTra">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="gasEdp">Educación Propia</label>&ensp;</td>
                                        <td>
                                            <select id="selGasEdp" name="selGasEdp" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasEdp" name="gasEdp">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="gasEdh">Educación de los Hijos</label>&ensp;</td>
                                        <td>
                                            <select id="selGasEdh" name="selGasEdh" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasEdh" name="gasEdh">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="gasPac">Pago de Credito</label>&ensp;</td>
                                        <td>
                                            <select id="selGasPac" name="selGasPac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasPac" name="gasPac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="gasTil">Recreación - Tiempo Libre</label>&ensp;</td>
                                        <td>
                                            <select id="selGasTil" name="selGasTil" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasTil" name="gasTil">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="gasVes">Vestuario</label>&ensp;</td>
                                        <td>
                                            <select id="selGasVes" name="selGasVes" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasVes" name="gasVes">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="gasSal">Salud</label>&ensp;</td>
                                        <td>
                                            <select id="selGasSal" name="selGasSal" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasSal" name="gasSal">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="gasCel">Pago de Celular</label>&ensp;</td>
                                        <td>
                                            <select id="selGasCel" name="selGasCel" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasCel" name="gasCel">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="gasPtc">Pago Tarjetas de Crédito</label>&ensp;</td>
                                        <td>
                                            <select id="selGasPtc" name="selGasPtc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasPtc" name="gasPtc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="gasCte">Compra de Tecnología</label>&ensp;</td>
                                        <td>
                                            <select id="selGasCte" name="selGasCte" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasCte" name="gasCte">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="gasBel">Cuidado Personal y Belleza</label>&ensp;</td>
                                        <td>
                                            <select id="selGasBel" name="selGasBel" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="gasBel" name="gasBel">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// INFORMACION FAMILIAR ADICIONAL - GASTOS: ////////////////////////// -->
                                <h4 class="labelTitulo">Situaciones Presentes en su Vida Familiar</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="sitDeu">Deudas que Superan Ingresos</label>&ensp;</td>
                                        <td>
                                            <select id="selSitPtc" name="selSitDeu" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitDeu" name="sitDeu">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="sitPco">Problemas Conducta Hijos</label>&ensp;</td>
                                        <td>
                                            <select id="selSitPco" name="selSitPco" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitPco" name="sitPco">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="sitDec">Dificultades Económicas</label>&ensp;</td>
                                        <td>
                                            <select id="selSitDec" name="selSitDec" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitDec" name="sitDec">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="sitDef">Desempleo de Algun miembro de su familia</label>&ensp;</td>
                                        <td>
                                            <select id="selSitDef" name="selSitDef" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitDef" name="sitDef">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="sitHch">Hijos Adolescentes en Embarazo o con Hijos</label>&ensp;</td>
                                        <td>
                                            <select id="selSitHch" name="selSitHch" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitHch" name="sitHch">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="sitSep">Separación - Divorcio</label>&ensp;</td>
                                        <td>
                                            <select id="selSitSep" name="selSitSep" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitSep" name="sitSep">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="sitVio">Violencia Intrafamiliar</label>&ensp;</td>
                                        <td>
                                            <select id="selSitVio" name="selSitVio" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitVio" name="sitVio">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="sitAdi">Adicciones</label>&ensp;</td>
                                        <td>
                                            <select id="selSitAdi" name="selSitAdi" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitAdi" name="sitAdi">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="sitMsq">Muerte de Seres Queridos</label>&ensp;</td>
                                        <td>
                                            <select id="selSitMsq" name="selSitMsq" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitMsq" name="sitMsq">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="sitEng">Enfermedad Grave de Algún Miembro de la Familia</label>&ensp;</td>
                                        <td>
                                            <select id="selSitEng" name="selSitEng" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitEng" name="sitEng">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="sitNin">Ninguna</label>&ensp;</td>
                                        <td>
                                            <select id="selSitNin" name="selSitNin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="sitNin" name="sitNin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// POSICION EN EL GRUPO FAMILIAR: ////////////////////////// -->
                                <h4 class="labelTitulo">Cuál es su posición Dentro del Grupo Familiar</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td>
                                            <select id="posFam" name="posFam">
                                                <option VALUE="1">PROVEEDOR PRINCIPAL DE RECURSOS ECONOMICOS</option>
                                                <option value="2">COMPARTE CON SU CONYUGUE O PAREJA LAS RESPONSABILIDADES ECONOMICAS</option>
                                                <option value="3">CONTRIBUYE CON LOS GASTOS FAMILIARES</option>
                                                <option value="4">DEPENDIENTE ECONOMICAMENTE DE OTRO MIEMBRO DE LA FAMILIA</option>
                                                <option value="5">OTRO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// QUIEN QUEDA AL CUIDADO DE HIJOS: ////////////////////////// -->
                                <h4 class="labelTitulo">Quién Queda al Cuidado de tus Hijos Durante tu Asencia</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="cuhAbu">Abuelos de los Niños</label>&ensp;</td>
                                        <td>
                                            <select id="selCuhAbu" name="selCuhAbu" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="cuhAbu" name="cuhAbu">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="cuhPma">Padre o Madre de los Niños</label>&ensp;</td>
                                        <td>
                                            <select id="selCuhPma" name="selCuhPma" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="cuhPma" name="cuhPma">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="cuhVec">Vecinos</label>&ensp;</td>
                                        <td>
                                            <select id="selCuhVec" name="selCuhVec" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="cuhVec" name="cuhVec">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="cuhGui">Guardería o Instit. Edicativa</label>&ensp;</td>
                                        <td>
                                            <select id="selCuhGui" name="selCuhGui" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="cuhGui" name="cuhGui">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="cuhEmd">Empleada Doméstica</label>&ensp;</td>
                                        <td>
                                            <select id="selCuhEmd" name="selCuhEmd" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="cuhEmd" name="cuhEmd">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="cuhFam">Un Familiar</label>&ensp;</td>
                                        <td>
                                            <select id="selCuhFam" name="selCuhFam" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="cuhFam" name="cuhFam">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="cuhQso">Se Queda Solo</label>&ensp;</td>
                                        <td>
                                            <select id="selCuhQso" name="selCuhQso" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="cuhQso" name="cuhQso">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="cuhOtr">Otro</label>&ensp;</td>
                                        <td>
                                            <select id="selCuhOtr" name="selCuhOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="cuhOtr" name="cuhOtr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!------------------------ HABITAT Y CONSTRUCCION DE PATRIMONIO: ------------>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title tabsGen">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">HABITAT Y CONSTRUCCION DE PATRIMONIO</a>
                        </h4>
                        <div id="collapse5" class="panel-collapse collapse">
                            <div class="panel-body" align="center">
                                <!-- //////////////////// VIVIENDA: ////////////////////////// -->
                                <h4 class="labelTitulo">Vivienda</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="vivTen">Tenencia de Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selVivTen" name="selVivTen" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="vivTen" name="vivTen">
                                                <?php
                                                $queryTenViv = "select Tencod,Tendes from root_000068 WHERE Tenest = 'on'";
                                                $commTenViv = mysql_query($queryTenViv, $conex) or die (mysql_errno()." - en el query: ".$queryTenViv." - ".mysql_error());
                                                while($datoTenViv = mysql_fetch_assoc($commTenViv))
                                                {
                                                    $codTvivi = $datoTenViv['Tencod'];  $desTvivi = $datoTenViv['Tendes'];
                                                    //$tenVivienda = $codTvivi.'-'.$desTvivi;
                                                    ?>
                                                    <option value="<?php echo $codTvivi ?>"><?php echo $desTvivi ?></option>
                                                    <?php
                                                }
                                                ?>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="vivTiv">Tipo de Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selVivTiv" name="selVivTiv" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="vivTiv" name="vivTiv">
                                                <?php
                                                $queryTipViv = "select Tpvcod,Tpvdes from root_000069 WHERE Tpvest = 'on'";
                                                $commTipViv = mysql_query($queryTipViv, $conex) or die (mysql_errno()." - en el query: ".$queryTipViv." - ".mysql_error());
                                                while($datoTipViv = mysql_fetch_assoc($commTipViv))
                                                {
                                                    $codTViv = $datoTipViv['Tpvcod']; $desTviv = $datoTipViv['Tpvdes'];
                                                    //$tipoVivienda = $codTViv.'-' .$desTviv;
                                                    ?>
                                                    <option value="<?php echo $codTViv ?>"><?php echo $desTviv ?></option>
                                                    <?php
                                                }
                                                ?>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="vivTte">Tiene Terraza Propia</label>&ensp;</td>
                                        <td>
                                            <select id="selVivTte" name="selVivTte" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="vivTte" name="vivTte">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><label for="vivTlo">Tiene Lote Propio</label>&ensp;</td>
                                        <td>
                                            <select id="selVivTlo" name="selVivTlo" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="vivTlo" name="vivTlo">
                                               <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="vivEst">Estado de la Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selVivEst" name="selVivEst" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="vivEst" name="vivEst">
                                                <?php
                                                $queryEstViv = "select Esvcod,Esvdes from root_000070 WHERE Esvest = 'on'";
                                                $commEstViv = mysql_query($queryEstViv, $conex) or die (mysql_errno()." - en el query: ".$queryEstViv." - ".mysql_error());
                                                while($datoEstViv = mysql_fetch_assoc($commEstViv))
                                                {
                                                    $codEViv = $datoEstViv['Esvcod']; $desEviv = $datoEstViv['Esvdes'];
                                                    //$estadoVivienda = $codEViv.'-' .$desEviv;
                                                    ?>
                                                    <option value="<?php echo $codEViv ?>"><?php echo $desEviv ?></option>
                                                    <?php
                                                }
                                                ?>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// ACCESO A SERVICIOS PUBLICOS: //////// -->
                                <h4 class="labelTitulo">Acceso a Servicios Publicos</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="serAcu">Acueducto</label>&ensp;</td>
                                        <td>
                                            <select id="selSerAcu" name="selSerAcu" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="serAcu" name="serAcu">
                                                <option value="03">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="serAlc">Alcantarillado</label>&ensp;</td>
                                        <td>
                                            <select id="selSerAlc" name="selSerAlc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="serAlc" name="serAlc">
                                                <option value="04">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="serAse">Aseo</label>&ensp;</td>
                                        <td>
                                            <select id="selSerAse" name="selSerAse" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="serAse" name="serAse">
                                                <option value="05">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="serEne">Energia</label>&ensp;</td>
                                        <td>
                                            <select id="selSerEne" name="selSerEne" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="serEne" name="serEne">
                                                <option value="01">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="serInt">Internet</label>&ensp;</td>
                                        <td>
                                            <select id="selSerInt" name="selSerInt" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="serInt" name="serInt">
                                                <option value="07">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="serRga">Red de Gas</label>&ensp;</td>
                                        <td>
                                            <select id="selSerRga" name="selSerRga" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="serRga" name="serRga">
                                                <option value="06">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="serTel">Telefono</label>&ensp;</td>
                                        <td>
                                            <select id="selSerTel" name="selSerTel" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="serTel" name="serTel">
                                                <option value="02">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// BENEFICIARIO SUBSIDIO VIVIENDA: //////// -->
                                <h4 class="labelTitulo">Subsidio de Vivienda / Ahorro Compra Vivienda</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="ahvSub">Ha Sido Beneficiado Con Algún Tipo de Subsidio de Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selAhvSub" name="selAhvSub" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ahvSub" name="ahvSub" style="width: 550px">
                                                <option value="SI">SI</option>
                                                <option value="NO">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right"><label for="ahvAho">Ahorra Para la Compra de Una Vivienda Propia</label>&ensp;</td>
                                        <td>
                                            <select id="selAhvAho" name="selAhvAho" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ahvAho" name="ahvAho" style="width: 550px">
                                                <option value="SI">SI</option>
                                                <option value="NO">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center"><label for="ahvCua">Cuánto Ahorro Tiene Disponible Para la Compra de Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selAhvCua" name="selAhvCua" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ahvCua" name="ahvCua" style="width: 550px">
                                                <option>Menos de $100.000</option>
                                                <option>Entre $100.000 y $1.000.000</option>
                                                <option>Entre $1.000.000 y $3.000.000</option>
                                                <option>Entre $3.000.000 y $5.000.000</option>
                                                <option>Mas de $5.000.000</option>
                                                <option>No ahorra</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// FACTORES DE RIESGO VIVIENDA: //////// -->
                                <h4 class="labelTitulo">Su Vivienda Presenta Alguno de Estos Factores de Riesgo</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="farInu">Inundaciones</label>&ensp;</td>
                                        <td>
                                            <select id="selFarInu" name="selFarInu" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="farInu" name="farInu">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="farCon">Contaminación</label>&ensp;</td>
                                        <td>
                                            <select id="selFarCon" name="selFarCon" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="farCon" name="farCon">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="farRia">Riesgos Ambientales</label>&ensp;</td>
                                        <td>
                                            <select id="selFarRia" name="selFarRia" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="farRia" name="farRia">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="farRie">Riesgos Estructurales</label>&ensp;</td>
                                        <td>
                                            <select id="selFarRie" name="selFarRie" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="farRie" name="farRie">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="farRis">Riesgos Sanitarios</label>&ensp;</td>
                                        <td>
                                            <select id="selFarRis" name="selFarRis" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="farRis" name="farRis">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="farRip">Riesgo Público</label>&ensp;</td>
                                        <td>
                                            <select id="selFarRip" name="selFarRip" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="farRip" name="farRip">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="farNot">No Tiene Factores de Riesgo</label>&ensp;</td>
                                        <td>
                                            <select id="selFarNot" name="selFarNot" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="farNot" name="farNot">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// NECESIDADES DE MEJORAMIENTO: //////// -->
                                <h4 class="labelTitulo">Necesidades de Mejoramiento Identificadas en tu Vivienda</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="nemEst">Estéticas</label>&ensp;</td>
                                        <td>
                                            <select id="selNemEst" name="selNemEst" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemEst" name="nemEst">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="nemMue">Muebles</label>&ensp;</td>
                                        <td>
                                            <select id="selNemMue" name="selNemMue" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemMue" name="nemMue">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="nemEle">Electrodomésticos</label>&ensp;</td>
                                        <td>
                                            <select id="selNemEle" name="selNemEle" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemEle" name="nemEle">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="nemPis">Piso</label>&ensp;</td>
                                        <td>
                                            <select id="selNemPis" name="selNemPis" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemPis" name="nemPis">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="nemPar">Paredes</label>&ensp;</td>
                                        <td>
                                            <select id="selNemPar" name="selNemPar" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemPar" name="nemPar">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="nemCol">Columnas</label>&ensp;</td>
                                        <td>
                                            <select id="selNemCol" name="selNemCol" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemCol" name="nemCol">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="nemHum">Humedades</label>&ensp;</td>
                                        <td>
                                            <select id="selNemHum" name="selNemHum" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemHum" name="nemHum">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="nemFac">Fachada</label>&ensp;</td>
                                        <td>
                                            <select id="selNemFac" name="selNemFac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemFac" name="nemFac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="nemTec">Techo</label>&ensp;</td>
                                        <td>
                                            <select id="selNemTec" name="selNemTec" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemTec" name="nemTec">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="nemBan">Baños</label>&ensp;</td>
                                        <td>
                                            <select id="selNemBan" name="selNemBan" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemBan" name="nemBan">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="nemCoc">Cocina</label>&ensp;</td>
                                        <td>
                                            <select id="selNemCoc" name="selNemCoc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemCoc" name="nemCoc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="nemAmp">Ampliación</label>&ensp;</td>
                                        <td>
                                            <select id="selNemAmp" name="selNemAmp" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemAmp" name="nemAmp">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="nemNot">No Tiene Necesidades</label>&ensp;</td>
                                        <td>
                                            <select id="selNemNot" name="selNemNot" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nemNot" name="nemNot">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// CREDITOS: //////////////////////// -->
                                <h4 class="labelTitulo">Créditos</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="creAct">Actualmente tiene usted algún crédito</label>&ensp;</td>
                                        <td>
                                            <select id="selCreAct" name="selCreAct" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="creAct" name="creAct" style="width: 550px">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////// PRODUCTOS FINANCIEROS: /////////////////// -->
                                <h4 class="labelTitulo">Con Cuáles de los Siguientes Productos Financieros Cuenta Cctualmente</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="nemEst">Cuenta de ahorros/Nómina</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfCan" name="selPrfCan" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfCan" name="prfCan">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="prfCuc">Cuenta Corriente</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfCuc" name="selPrfCuc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfCuc" name="prfCuc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="prfTac">Tarjeta de Crédito</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfTac" name="selPrfTac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfTac" name="prfTac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="prfCrc">Crédito de Consumo/Libre Inversión</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfCrc" name="selPrfCrc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfCrc" name="prfCrc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="prfCrh">Crédito Hipotecario de Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfCrh" name="selPrfCrh" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfCrh" name="prfCrh">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="prfCrv">Crédito de vehículo/Carro - Moto</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfCrv" name="selPrfCrv" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfCrv" name="prfCrv">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="prfInv">Inversiones</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfInv" name="selPrfInv" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfInv" name="prfInv">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="prfSeg">Seguros</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfSeg" name="selPrfSeg" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfSeg" name="prfSeg">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="prfNin">Ninguno</label>&ensp;</td>
                                        <td>
                                            <select id="selPrfNin" name="selPrfNin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="prfNin" name="prfNin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////// MOTIVO DE CREDITOS: /////////////////// -->
                                <h4 class="labelTitulo">Cuál es el Motivo de sus Créditos Actuales</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="mocViv">Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selMocViv" name="selMocViv" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocViv" name="mocViv">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocTec">Tecnología</label>&ensp;</td>
                                        <td>
                                            <select id="selMocTec" name="selMocTec" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocTec" name="mocTec">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocMue">Muebles</label>&ensp;</td>
                                        <td>
                                            <select id="selMocMue" name="selMocMue" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocMue" name="mocMue">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="mocEle">Electrodomésticos</label>&ensp;</td>
                                        <td>
                                            <select id="selMocEle" name="selMocEle" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocEle" name="mocEle">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocVeh">Vehículo</label>&ensp;</td>
                                        <td>
                                            <select id="selMocVeh" name="selMocVeh" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocVeh" name="mocVeh">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocSal">Salud</label>&ensp;</td>
                                        <td>
                                            <select id="selMocSal" name="selMocSal" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocSal" name="mocSal">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="mocCir">Cirugías Estéticas</label>&ensp;</td>
                                        <td>
                                            <select id="selMocCir" name="selMocCir" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocCir" name="mocCir">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocTur">Turismo</label>&ensp;</td>
                                        <td>
                                            <select id="selMocTur" name="selMocTur" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocTur" name="mocTur">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocLib">Libre Inversión</label>&ensp;</td>
                                        <td>
                                            <select id="selMocLib" name="selMocLib" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocLib" name="mocLib">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="mocGas">Gastos del Hogar</label>&ensp;</td>
                                        <td>
                                            <select id="selMocGas" name="selMocGas" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocGas" name="mocGas">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocTac">Tarjeta de Crédito</label>&ensp;</td>
                                        <td>
                                            <select id="selMocTac" name="selMocTac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocTac" name="mocTac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocEdp">Educación Propia</label>&ensp;</td>
                                        <td>
                                            <select id="selMocEdp" name="selMocEdp" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocEdp" name="mocEdp">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="mocEdf">Educación Familia</label>&ensp;</td>
                                        <td>
                                            <select id="selMocEdf" name="selMocEdf" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocEdf" name="mocEdf">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocCre">Créditos Empresariales o Para Emprendimientos</label>&ensp;</td>
                                        <td>
                                            <select id="selMocCre" name="selMocCre" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocCre" name="mocCre">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="mocNin">Ninguno</label>&ensp;</td>
                                        <td>
                                            <select id="selMocNin" name="selMocNin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="mocNin" name="mocNin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////// A DONDE ACCEDE PARA CREDITOS: /////////////////// -->
                                <h4 class="labelTitulo">A Qué Entidades o Personas Acude para Accder a Créditos</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="eacBac">Bancos y Cooperativas</label>&ensp;</td>
                                        <td>
                                            <select id="selEacBac" name="selEacBac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacBac" name="eacBac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="eacFoe">Fondos de Empleados</label>&ensp;</td>
                                        <td>
                                            <select id="selEacFoe" name="selEacFoe" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacFoe" name="eacFoe">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="aecFom">Fondo Mutuo</label>&ensp;</td>
                                        <td>
                                            <select id="selEacFom" name="selEacFom" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacFom" name="eacFom">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="eacPgg">Paga Diario o Gota a Gota</label>&ensp;</td>
                                        <td>
                                            <select id="selEacPgg" name="selEacPgg" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacPgg" name="eacPgg">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="eacFam">Familiares o Amigos</label>&ensp;</td>
                                        <td>
                                            <select id="selEacFam" name="selEacFam" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacFam" name="eacFam">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="eacCra">Créditos en Almacenes</label>&ensp;</td>
                                        <td>
                                            <select id="selEacCra" name="selEacCra" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacCra" name="eacCra">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="eacCac">Cajas de Compensación</label>&ensp;</td>
                                        <td>
                                            <select id="selEacCac" name="selEacCac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacCac" name="eacCac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="eacEml">Empresa en la que Labora</label>&ensp;</td>
                                        <td>
                                            <select id="selEacEml" name="selEacEml" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacEml" name="eacEml">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="eacNat">Natillera con Amigos o Familia</label>&ensp;</td>
                                        <td>
                                            <select id="selEacNat" name="selEacNat" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacNat" name="eacNat">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="eacOtr">Otro</label>&ensp;</td>
                                        <td>
                                            <select id="selEacOtr" name="selEacOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacOtr" name="eacOtr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="right"><label for="eacNin">Ninguno</label>&ensp;</td>
                                        <td>
                                            <select id="selEacNin" name="selEacNin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="eacNin" name="eacNin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////// LINEAS DE CREDITO DE INTERES: /////////////////// -->
                                <h4 class="labelTitulo">líneas de Crédito/Préstamo de Dinero, que son de su Interés</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="lciViv">Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selLciViv" name="selLciViv" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciViv" name="lciViv">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="lciVeh">Vehículo/Carro/Moto</label>&ensp;</td>
                                        <td>
                                            <select id="selLciVeh" name="selLciVeh" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciVeh" name="lciVeh">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="lciSal">Salud</label>&ensp;</td>
                                        <td>
                                            <select id="selLciSal" name="selLciSal" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciSal" name="lciSal">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="lciCie">Cirugías Estéticas</label>&ensp;</td>
                                        <td>
                                            <select id="selLciCie" name="selLciCie" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciCie" name="lciCie">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="lciTur">Turismo</label>&ensp;</td>
                                        <td>
                                            <select id="selLciTur" name="selLciTur" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciTur" name="lciTur">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="lciEdf">Educación de la Familia</label>&ensp;</td>
                                        <td>
                                            <select id="selLciEdf" name="selLciEdf" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciEdf" name="lciEdf">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="lciEdp">Educación Propia</label>&ensp;</td>
                                        <td>
                                            <select id="selLciEdp" name="selLciEdp" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciEdp" name="lciEdp">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="lciCem">Créditos Empresariales/Emprendimientos</label>&ensp;</td>
                                        <td>
                                            <select id="selLciCem" name="selLciCem" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciCem" name="lciCem">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="lciMev">Mejoramiento de Vivienda</label>&ensp;</td>
                                        <td>
                                            <select id="selLciMev" name="selLciMev" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciMev" name="lciMev">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="lciCrr">Crédito Rotativo</label>&ensp;</td>
                                        <td>
                                            <select id="selLciCrr" name="selLciCrr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciCrr" name="lciCrr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="lciLib">Libre Inversión</label>&ensp;</td>
                                        <td>
                                            <select id="selLciLib" name="selLciLib" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciLib" name="lciLib">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="lciTac">Tarjeta de Crédito</label>&ensp;</td>
                                        <td>
                                            <select id="selLciTac" name="selLciTac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciTac" name="lciTac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="lciNin">Ninguno</label>&ensp;</td>
                                        <td>
                                            <select id="selLciNin" name="selLciNin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="lciNin" name="lciNin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////// A TRAVES DE QUE INSTIT AHORRA: /////////////////// -->
                                <h4 class="labelTitulo">A Través de qué Instituciones Ahorra</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="inaInv">inversiones</label>&ensp;</td>
                                        <td>
                                            <select id="selInaInv" name="selInaInv" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaInv" name="inaInv">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="inaBan">Bancos</label>&ensp;</td>
                                        <td>
                                            <select id="selInaBan" name="selInaBan" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaBan" name="inaBan">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="inaNat">Natilleras</label>&ensp;</td>
                                        <td>
                                            <select id="selInaNat" name="selInaNat" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaNat" name="inaNat">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="inaCoo">Cooperativas de Ahorro y Crédito</label>&ensp;</td>
                                        <td>
                                            <select id="selInaCoo" name="selInaCoo" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaCoo" name="inaCoo">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="inaFoe">Fondo de Empleados</label>&ensp;</td>
                                        <td>
                                            <select id="selInaFoe" name="selInaFoe" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaFoe" name="inaFoe">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="inaFom">Fondo Mutuo</label>&ensp;</td>
                                        <td>
                                            <select id="selInaFom" name="selInaFom" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaFom" name="inaFom">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="inaFvo">Fondo Voluntario de Pensiones</label>&ensp;</td>
                                        <td>
                                            <select id="selInaFvo" name="selInaFvo" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaFvo" name="inaFvo">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="inaOtr">Otro</label>&ensp;</td>
                                        <td>
                                            <select id="selInaOtr" name="selInaOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaOtr" name="inaOtr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="inaNoa">No ahorra</label>&ensp;</td>
                                        <td>
                                            <select id="selInaNoa" name="selInaNoa" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="inaNoa" name="inaNoa">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////// TRANSPORTE: /////////////////// -->
                                <h4 class="labelTitulo">El Transporte Habitual que Utiliza Para ir a su Lugar de Trabajo Es</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="trhBic">Bicicleta</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhBic" name="selTrhBic" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhBic" name="trhBic">
                                                <option value="07">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="trhBus">Bus</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhBus" name="selTrhBus" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhBus" name="trhBus">
                                                <option value="01">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="trhCam">Caminando</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhCam" name="selTrhCam" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhCam" name="trhCam">
                                                <option value="08">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="trhCap">Carro Particular</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhCap" name="selTrhCap" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhCap" name="trhCap">
                                                <option value="03">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="trhMet">Metro</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhMet" name="selTrhMet" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhMet" name="trhMet">
                                                <option value="02">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="trhMot">Moto</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhMot" name="selTrhMot" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhMot" name="trhMot">
                                                <option value="04">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="trhOtr">Otro</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhOtr" name="selTrhOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhOtr" name="trhOtr">
                                                <option value="09">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="trhTax">Taxi</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhTax" name="selTrhTax" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhTax" name="trhTax">
                                                <option value="05">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="trhTrc">Transporte Contratado</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhTrc" name="selTrhTrc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="trhTrc" name="trhTrc">
                                                <option value="06">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="trhOtc">Otro Transporte, Cuál</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhOtc" name="selTrhOtc" class="select1">
                                                <option>=</option>
                                                <option selected>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <input type="text" id="trhOtc" name="trhOtc" style="width: 550px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="trhPar">Si Viene en Trasporte Particular<br>o Moto, en que Lugar Parquea</label>&ensp;</td>
                                        <td>
                                            <select id="selTrhPar" name="selTrhPar" class="select1">
                                                <option>=</option>
                                                <option selected>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <input type="text" id="trhPar" name="trhPar" style="width: 550px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="trhTid">Cuánto tiempo en promedio se demora<br>en cada desplazamiento a la empresa</label></td>
                                        <td>
                                            <select id="selTrhTid" name="selTrhTid" class="select1">
                                                <option>=</option>
                                                <option selected>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <select id="trhTid" name="trhTid" style="width: 550px">
                                                <option value="1">5 - 30 minutos</option>
                                                <option value="2">31 - 60 minutos (1 hora)</option>
                                                <option value="3">61 - 180 minutos (1,5 horas)</option>
                                                <option value="4">mas de 180 minutos</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="trhTur">Seleccione su turno habitual de trabajo</label></td>
                                        <td>
                                            <select id="selTrhTur" name="selTrhTur" class="select1">
                                                <option>=</option>
                                                <option selected>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <select id="trhTur" name="trhTur" style="width: 550px">
                                                <option value="D">DIURNO</option>
                                                <option value="N">NOCTURNO</option>
                                                <option value="M">MIXTO (Diurno y nocturno)</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!------------------------ CALIDAD DE VIDA: ------------>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title tabsGen">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse6">CALIDAD DE VIDA</a>
                        </h4>
                        <div id="collapse6" class="panel-collapse collapse">
                            <div class="panel-body" align="center">
                                <!-- //////////////////// ACTIVIDADES TIEMPO EXTRA: ////////////////////////// -->
                                <h4 class="labelTitulo">Actividad Laboral</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="aclAte">Actividades Que Realiza en su Tiempo Extralaboral</label>&ensp;</td>
                                        <td>
                                            <select id="selAclAte" name="selAclAte" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <select id="aclAte" name="aclAte" style="width: 550px">
                                                <option value="1">Trabaja en otra empresa</option>
                                                <option value="2">Es docente</option>
                                                <option value="3">Es asesor</option>
                                                <option value="4">Es cuidador doméstico</option>
                                                <option value="5">Otras, cúales</option>
                                                <option value="6">Ninguna</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="aclOta">Otra Actividad, Cuál ?</label>&ensp;</td>
                                        <td>
                                            <select id="selAclOta" name="selAclOta" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <input type="text" id="aclOta" name="aclOta" style="width: 550px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="aclRan">Rango salarial en que se encuentra</label>&ensp;</td>
                                        <td>
                                            <select id="selAclRan" name="selAclRan" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <select id="aclRan" name="aclRan" style="width: 550px">
                                                <option value="1">Menos de 1 SMMLV</option>
                                                <option value="2">1 a 2 SMMLV</option>
                                                <option value="3">Hasta 4 SMMLV</option>
                                                <option value="4">Hasta 6 SMMLV</option>
                                                <option value="5">Más de 6 SMMLV</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// NECESIDAD DE FORMACION: //////////////////////////// -->
                                <h4 class="labelTitulo">Formación<br><br>Cuál de las Siguientes Necesidades de Formación o Capacitación, es para usted la más Prioritaria</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="nefCae">Capacitación Empresarial</label>&ensp;</td>
                                        <td>
                                            <select id="selNefCae" name="selNefCae" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefCae" name="nefCae">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="nefDec">Desarrollo de Competencias</label>&ensp;</td>
                                        <td>
                                            <select id="selNefDec" name="selNefDec" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefDec" name="nefDec">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="nefRef">Relaciones Familiares</label>&ensp;</td>
                                        <td>
                                            <select id="selNefRef" name="selNefRef" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefRef" name="nefRef">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="nefMac">Manejo de Conflictos</label>&ensp;</td>
                                        <td>
                                            <select id="selNefMac" name="selNefMac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefMac" name="nefMac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="nefFip">Finanzas Personales</label>&ensp;</td>
                                        <td>
                                            <select id="selNefFip" name="selNefFip" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefFip" name="nefFip">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="nefFtt">Formación Técnica para el Trabajo</label>&ensp;</td>
                                        <td>
                                            <select id="selNefFtt" name="selNefFtt" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefFtt" name="nefFtt">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="nefIdi">Idiomas</label>&ensp;</td>
                                        <td>
                                            <select id="selNefIdi" name="selNefIdi" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefIdi" name="nefIdi">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="nefInt">Informática y Nuevas Tecnologías</label>&ensp;</td>
                                        <td>
                                            <select id="selNefInt" name="selNefInt" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefInt" name="nefInt">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="nefFcr">Formación en Conocimientos Relacionados con su Profesión</label>&ensp;</td>
                                        <td>
                                            <select id="selNefFcr" name="selNefFcr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefFcr" name="nefFcr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="nefOtr">Otro</label>&ensp;</td>
                                        <td>
                                            <select id="selNefOtr" name="selNefOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefOtr" name="nefOtr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="nefNot">No Tiene Necesidades</label>&ensp;</td>
                                        <td>
                                            <select id="selNefNot" name="selNefNot" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefNot" name="nefNot">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// INTERES GENERAL - ALMUERZO: ///////////////////// -->
                                <h4 class="labelTitulo">Interés General<br><br>Usted habitualmente a la hora del almuerzo</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="ingTac">Trae sus alimentos de Casa</label>&ensp;</td>
                                        <td>
                                            <select id="selIngTac" name="selIngTac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ingTac" name="ingTac">
                                                <option value="01">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="ingCob">Compra en Bocatos</label>&ensp;</td>
                                        <td>
                                            <select id="selIngCob" name="selIngCob" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ingCob" name="ingCob">
                                                <option value="02">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="ingCoo">Compra en Otros Lugares</label>&ensp;</td>
                                        <td>
                                            <select id="selIngCoo" name="selIngCoo" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ingCoo" name="ingCoo">
                                                <option value="03">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="ingVac">Va a su Casa</label>&ensp;</td>
                                        <td>
                                            <select id="selIngVac" name="selIngVac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ingVac" name="ingVac">
                                                <option value="04">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="ingOtr">Otros</label>&ensp;</td>
                                        <td>
                                            <select id="selIngOtr" name="selIngOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ingOtr" name="ingOtr">
                                                <option value="05">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// INTERES GENERAL - ACTIVIDADES: ///////////////////// -->
                                <h4 class="labelTitulo">En cuáles Actividades Participaría Activamente</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="acpTob">Torneo de Bolos</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpTob" name="selAcpTob" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpTob" name="acpTob">
                                                <option value="01">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpTop">Torneo de PlayStation</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpTop" name="selAcpTop" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpTop" name="acpTop">
                                                <option value="02">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpTov">Torneo de Voleibol</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpTov" name="selAcpTov" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpTov" name="acpTov">
                                                <option value="03">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="acpTba">Torneo de Baloncesto</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpTba" name="selAcpTba" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpTba" name="acpTba">
                                                <option value="04">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpTot">Torneo de Tenis de Campo</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpTot" name="selAcpTot" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpTot" name="acpTot">
                                                <option value="05">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpCam">Caminatas</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpCam" name="selAcpCam" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpCam" name="acpCam">
                                                <option value="06">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="acpBai">Baile</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpBai" name="selAcpBai" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpBai" name="acpBai">
                                                <option value="07">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpYog">Yoga</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpYog" name="selAcpYog" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpYog" name="acpYog">
                                                <option value="08">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="actEnp">Encuentro de Parejas</label>&ensp;</td>
                                        <td>
                                            <select id="selActEnp" name="selActEnp" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="actEnp" name="actEnp">
                                                <option value="09">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="acpCic">Ciclo Paseos</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpCic" name="selAcpCic" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpCic" name="acpCic">
                                                <option value="10">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpMar">Maratones</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpMar" name="selAcpMar" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpMar" name="acpMar">
                                                <option value="11">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpTho">Tarde de Hobbies</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpTho" name="selAcpTho" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpTho" name="acpTho">
                                                <option value="12">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="acpGte">Grupo de Teatro</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpGte" name="selAcpGte" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpGte" name="acpGte">
                                                <option value="13">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpArp">Artes Plasticas</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpArp" name="selAcpArp" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpArp" name="acpArp">
                                                <option value="14">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpMnu">Manualidades</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpMnu" name="selAcpMnu" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpMnu" name="acpMnu">
                                                <option value="15">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="acpGas">Gastronomía</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpGas" name="selAcpGas" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpGas" name="acpGas">
                                                <option value="16">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpCli">Clases de Inglés</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpCli" name="selAcpCli" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpCli" name="acpCli">
                                                <option value="17">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpCop">Conciencia Plena (Mindfulnes)</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpCop" name="selAcpCop" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpCop" name="acpCop">
                                                <option value="18">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right"><label for="acpTpi">Tardes de Picnic</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpTpi" name="selAcpTpi" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpTpi" name="acpTpi">
                                                <option value="19">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="acpOtr">Otros</label>&ensp;</td>
                                        <td>
                                            <select id="selAcpOtr" name="selAcpOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="acpOtr" name="acpOtr">
                                                <option value="20">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// DIAS PARTICIPACION - ACTIVIDADES: ///////////////////// -->
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="pacDia">Qué Dias de la Semana le Sería Posible Participar de estas Actividades</label></td>
                                        <td>
                                            <select id="selPacDia" name="selPacDia" class="select1">
                                                <option>=</option>
                                                <option selected>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <select id="pacDia" name="pacDia" style="width: 550px">
                                                <option value="LV">De lunes a viernes</option>
                                                <option value="S">Sábados</option>
                                                <option value="D">Domingos</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="pacHor">En qué horario le será posible participar de estas actividades</label></td>
                                        <td>
                                            <select id="selPacHor" name="selPacHor" class="select1">
                                                <option>=</option>
                                                <option selected>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <select id="pacHor" name="pacHor" style="width: 550px">
                                                <option value="67">06:00 am a 07:00 am</option>
                                                <option value="78">07:00 am a 08:00 am</option>
                                                <option value="89">08:00 am a 09:00 am</option>
                                                <option value="910">09:00 am a 10:00 am</option>
                                                <option value="1011">10:00 am a 11:00 am</option>
                                                <option value="1112">11:00 am a 12:00 pm</option>
                                                <option value="1213">12:00 pm a 01:00 pm</option>
                                                <option value="1415">02:00 pm a 03:00 pm</option>
                                                <option value="1516">03:00 pm a 04:00 pm</option>
                                                <option value="1617">04:00 pm a 05:00 pm</option>
                                                <option value="1718">05:00 pm a 06:00 pm</option>
                                                <option value="1819">06:00 pm a 07:00 pm</option>
                                                <option value="1920">07:00 pm a 08:00 pm</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// QUE HACE EN SU TIEMPO LIBRE: ///////////////////// -->
                                <h4 class="labelTitulo">Qué hace en su tiempo libre</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="qhtCin">Cine</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtCin" name="selQhtCin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtCin" name="qhtCin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="nefDec">Deporte</label>&ensp;</td>
                                        <td>
                                            <select id="selNefDec" name="selNefDec" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="nefDec" name="nefDec">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtVij">Video Juegos</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtVij" name="selQhtVij" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtVij" name="qhtVij">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhtVte">Ver Televisión</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtVte" name="selQhtVte" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtVte" name="qhtVte">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtNai">Navegar en Internet</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtNai" name="selQhtNai" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtNai" name="qhtNai">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtIcc">Ir a un Centro Comercial</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtIcc" name="selQhtIcc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtIcc" name="qhtIcc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhtIpa">Ir a un Parque</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtIpa" name="selQhtIpa" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtIpa" name="qhtIpa">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtIfi">Ir a Fiestas con sus Amigos</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtIfi" name="selQhtIfi" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtIfi" name="qhtIfi">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtCle">Clases ExtraCurriculares</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtCle" name="selQhtCle" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtCle" name="qhtCle">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhtDed">Descansar/Dormir</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtDed" name="selQhtDed" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtDed" name="qhtDed">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtJar">Jardinería</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtJar" name="selQhtJar" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtJar" name="qhtJar">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtCon">Conciertos</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtCon" name="selQhtCon" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtCon" name="qhtCon">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhtPin">Pintura</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtPin" name="selQhtPin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtPin" name="qhtPin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtEsc">Escultura</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtEsc" name="selQhtEsc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtEsc" name="qhtEsc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtFot">Fotografía</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtFot" name="selQhtFot" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtFot" name="qhtFot">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhtVim">Visitar Museos</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtVim" name="selQhtVim" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtVim" name="qhtVim">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtVib">Visitar Bibliotecas</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtVib" name="selQhtVib" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtVib" name="qhtVib">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtEac">Espectáculos Artísticos y Culturales</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtEac" name="selQhtEac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtEac" name="qhtEac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhtDan">Danzas</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtDan" name="selQhtDan" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtDan" name="qhtDan">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtTim">Tocar un Instrumento Musical</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtTim" name="selQhtTim" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtTim" name="qhtTim">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtCoc">Cocina</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtCoc" name="selQhtCoc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtCoc" name="qhtCoc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhtMnu">Manualidades</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtMnu" name="selQhtMnu" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtMnu" name="qhtMnu">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtOtr">Otro</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtOtr" name="selQhtOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtOtr" name="qhtOtr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhtNin">Ninguno</label>&ensp;</td>
                                        <td>
                                            <select id="selQhtNin" name="selQhtNin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhtNin" name="qhtNin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// CON QUIEN PASA SU TIEMPO LIBRE: ///////////////////// -->
                                <h4 class="labelTitulo">Con Quién Pasa la Mayor Parte de su Tiempo de Esparcimiento/Tiempo por fuera de sus Actividades Laborales</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="ptlHij">Hijos / Hijas</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlHij" name="selPtlHij" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlHij" name="ptlHij">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="ptlAmi">Amigos / Amigas</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlAmi" name="selPtlAmi" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlAmi" name="ptlAmi">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="ptlMas">Mascotas</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlMas" name="selPtlMas" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlMas" name="ptlMas">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="ptlSol">Solo</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlSol" name="selPtlSol" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlSol" name="ptlSol">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="ptlFam">Familia</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlFam" name="selPtlFam" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlFam" name="ptlFam">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="ptlAmo">Amigos o Amigas on Line</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlAmo" name="selPtlAmo" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlAmo" name="ptlAmo">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="ptlPar">Pareja (Novio o Conyuge)</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlPar" name="selPtlPar" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlPar" name="ptlPar">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="ptlCot">Compañeros de Trabajo</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlCot" name="selPtlCot" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlCot" name="ptlCot">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="ptlOtr">Otro</label>&ensp;</td>
                                        <td>
                                            <select id="selPtlOtr" name="selPtlOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="ptlOtr" name="ptlOtr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// ACTIVIDADES PREFIEREN HIJOS TIEMPO LIBRE: ///////////////////// -->
                                <h4 class="labelTitulo">Qué Actividades Prefieren Realizar sus Hijos en el Tiempo Libre</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="qhhCin">Cine</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhCin" name="selQhhCin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhCin" name="qhhCin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhDep">Deporte</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhDep" name="selQhhDep" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhDep" name="qhhDep">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhVij">Video Juegos</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhVij" name="selQhhVij" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhVij" name="qhhVij">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhhVte">Ver Televisión</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhVte" name="selQhhVte" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhVte" name="qhhVte">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhNai">Navegar en Internet</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhNai" name="selQhhNai" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhNai" name="qhhNai">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhIcc">Ir a un Centro Comercial</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhIcc" name="selQhhIcc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhIcc" name="qhhIcc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhhIpa">Ir a un Parque</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhIpa" name="selQhhIpa" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhIpa" name="qhhIpa">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhIfi">Ir a Fiestas con sus Amigos</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhIfi" name="selQhhIfi" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhIfi" name="qhhIfi">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhCle">Clases ExtraCurriculares</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhCle" name="selQhhCle" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhCle" name="qhhCle">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhhDed">Descansar/Dormir</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhDed" name="selQhhDed" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhDed" name="qhhDed">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhJar">Jardinería</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhJar" name="selQhhJar" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhJar" name="qhhJar">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhCon">Conciertos</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhCon" name="selQhhCon" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhCon" name="qhhCon">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhhPin">Pintura</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhPin" name="selQhhPin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhPin" name="qhhPin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhEsc">Escultura</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhEsc" name="selQhhEsc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhEsc" name="qhhEsc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhFot">Fotografía</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhFot" name="selQhhFot" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhFot" name="qhhFot">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhhVim">Visitar Museos</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhVim" name="selQhhVim" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhVim" name="qhhVim">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhVib">Visitar Bibliotecas</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhVib" name="selQhhVib" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhVib" name="qhhVib">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhEac">Espectáculos Artísticos y Culturales</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhEac" name="selQhhEac" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhEac" name="qhhEac">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhhDan">Danzas</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhDan" name="selQhhDan" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhDan" name="qhhDan">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhTim">Tocar un Instrumento Musical</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhTim" name="selQhhTim" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhTim" name="qhhTim">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhCoc">Cocina</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhCoc" name="selQhhCoc" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhCoc" name="qhhCoc">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="qhhMnu">Manualidades</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhMnu" name="selQhhMnu" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhMnu" name="qhhMnu">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhOtr">Otro</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhOtr" name="selQhhOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhOtr" name="qhhOtr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="qhhNin">Ninguno</label>&ensp;</td>
                                        <td>
                                            <select id="selQhhNin" name="selQhhNin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="qhhNin" name="qhhNin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// BARRERAS USO TIEMPO LIBRE: ///////////////////// -->
                                <h4 class="labelTitulo">Cuáles son las Barreras que se le Presentan Generalmente en el Uso del Tiempo Libre</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="barFdi">Falta de Dinero</label>&ensp;</td>
                                        <td>
                                            <select id="selBarFdi" name="selBarFdi" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="barFdi" name="barFdi">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="barNcd">No Coincidir la Disponibilidad del Tiempo con la Familia</label>&ensp;</td>
                                        <td>
                                            <select id="selBarNcd" name="selBarNcd" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="barNcd" name="barNcd">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="barDap">Desconocimiento de Actividades y Programas</label>&ensp;</td>
                                        <td>
                                            <select id="selBarDap" name="selBarDap" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="barDap" name="barDap">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="barFms">Falta Motivación para Salir</label>&ensp;</td>
                                        <td>
                                            <select id="selBarFms" name="selBarFms" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="barFms" name="barFms">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="barNdt">No Disponer de Tiempo Libre</label>&ensp;</td>
                                        <td>
                                            <select id="selBarNdt" name="selBarNdt" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="barNdt" name="barNdt">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="barOtr">otro</label>&ensp;</td>
                                        <td>
                                            <select id="selBarOtr" name="selBarOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="barOtr" name="barOtr">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="barNin">Ninguno</label>&ensp;</td>
                                        <td>
                                            <select id="selBarNin" name="selBarNin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="barNin" name="barNin">
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// ROL: ///////////////////// -->
                                <h4 class="labelTitulo">Rol que Desempeña en la Institución Adicional a su Cargo</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="right"><label for="rolAud">Es Auditor Interno de Calidad</label>&ensp;</td>
                                        <td>
                                            <select id="selRolAud" name="selRolAud" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="rolAud" name="rolAud">
                                                <option value="01">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="rolBre">Hace Parte de la Brigada de Emergencia</label>&ensp;</td>
                                        <td>
                                            <select id="selRolBre" name="selRolBre" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="rolBre" name="rolBre">
                                                <option value="03">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                        <td align="right"><label for="rolOtr">Otros</label>&ensp;</td>
                                        <td>
                                            <select id="selRolOtr" name="selRolOtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="rolOtr" name="rolOtr">
                                                <option value="04">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center"><label for="rotOtc">Otros, Cuáles</label></td>
                                        <td>
                                            <select id="selRotOtc" name="selRotOtc" class="select1">
                                                <option>=</option>
                                                <option selected>LIKE</option>
                                            </select>
                                        </td>
                                        <td colspan="7">
                                            <input type="text" id="rotOtc" name="rotOtc" style="width: 550px">
                                        </td>
                                    </tr>
                                </table>

                                <!-- ///////////////// PERTENENCIA A COMITES: ///////////////////// -->
                                <h4 class="labelTitulo">Pertenece a Alguno de los Siguientes Comités</h4>
                                <table align="center" border="0" style="width: 100%; margin-bottom: 10px">
                                    <tr>
                                        <td align="center"><label for="pcoCco">Comité de Convivencia</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoCco" name="selPcoCco" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoCco" name="pcoCco">
                                                <option value="01">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoGei">Gestión de la Información</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoGei" name="selPcoGei" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoGei" name="pcoGei">
                                                <option value="02">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoCdo">Comité de Docencia</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoCdo" name="selPcoCdo" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoCdo" name="pcoCdo">
                                                <option value="03">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="pcoGte">Gestión de la Tecnología</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoGte" name="selPcoGte" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoGte" name="pcoGte">
                                                <option value="04">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoCin">Comité de Investigaciones</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoCin" name="selPcoCin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoCin" name="pcoCin">
                                                <option value="05">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoHca">Historia Clínica y Auditoría</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoHca" name="selPcoHca" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoHca" name="pcoHca">
                                                <option value="06">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="pcoCom">Compras</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoCom" name="selPcoCom" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoCom" name="pcoCom">
                                                <option value="07">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoIin">Infecciones Intrahospitalarias</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoIin" name="selPcoIin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoIin" name="pcoIin">
                                                <option value="08">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoCop">COPASST</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoCop" name="selPcoCop" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoCop" name="pcoCop">
                                                <option value="09">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="pcoMtr">Medicina Transfusional</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoMtr" name="selPcoMtr" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoMtr" name="pcoMtr">
                                                <option value="10">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoCre">Credencialización</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoCre" name="selPcoCre" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoCre" name="pcoCre">
                                                <option value="11">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoMco">Mejoramiento Continúo</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoMco" name="selPcoMco" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoMco" name="pcoMco">
                                                <option value="12">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="pcoEin">Etica en Investigación</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoEin" name="selPcoEin" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoEin" name="pcoEin">
                                                <option value="13">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoMss">Movilidad y Seguridad Sostenible</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoMss" name="selPcoMss" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoMss" name="pcoMss">
                                                <option value="14">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoEho">Etica Hospitalaria</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoEho" name="selPcoEho" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoEho" name="pcoEho">
                                                <option value="15">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="pcoSpa">Seguridad del paciente</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoSpa" name="selPcoSpa" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoSpa" name="pcoSpa">
                                                <option value="16">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoEca">Evaluación de la Calidad en la Atención Médica</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoEca" name="selPcoEca" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoEca" name="pcoEca">
                                                <option value="17">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoTra">Transplantes</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoTra" name="selPcoTra" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoTra" name="pcoTra">
                                                <option value="18">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><label for="pcoFte">Farmacia y Terapéutica</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoFte" name="selPcoFte" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoFte" name="pcoFte">
                                                <option value="19">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoVep">Vigilancia Epidemiológica</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoVep" name="selPcoVep" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoVep" name="pcoVep">
                                                <option value="20">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>

                                        <td align="center"><label for="pcoGam">Gestión Ambiental</label>&ensp;</td>
                                        <td>
                                            <select id="selPcoGam" name="selPcoGam" class="select1">
                                                <option>=</option>
                                                <option>LIKE</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select id="pcoGam" name="pcoGam">
                                                <option value="21">SI</option>
                                                <option value="00">NO</option>
                                                <option selected></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-group" style="width: 100%">
                <div align="center" class="input-group" style="border: 0 solid #428BCA; width: 70%; margin: 10px auto 10px auto">
                    <div align="center" class="input-group" style="margin: auto auto 10px auto">
                        <input type="submit" id="submit1" name="submit1" class="btn btn-info btn-sm" value="> > >" title="Generar">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php
    if(isset($_POST["submit1"]))
    {
        ?>
        <!----------------- RECEPCION DE PARAMETROS: ------------------------->
        <section>
            <?php
            $c1=0;  $c2=0; $c3 = 0; $c4 = 0;
            $nom1Emple = $_POST['nombre1Empl']; $operNom1 = $_POST['selNom1'];      if($nom1Emple != null){$campo1 = 'Ideno1'; $c1 = 1;}
            $nom2Emple = $_POST['nombre2Empl']; $operNom2 = $_POST['selNom2'];      if($nom2Emple != null){$campo2 = 'Ideno2'; $c2 = 1;}
            $apel1Emple = $_POST['apel1Empl'];  $operApe1 = $_POST['selApe1'];      if($apel1Emple != null){$campo3 = 'Ideap1'; $c3 = 1;}
            $apel2Emple = $_POST['apel2Empl'];  $operApe2 = $_POST['selApe2'];      if($apel2Emple != null){$campo4 = 'Ideap2'; $c4 = 1;}
            $fecNacEmpl = $_POST['fecNacEmpl']; $operFnac = $_POST['selFnac'];      if($fecNacEmpl != null){$campo5 = 'Idefnc'; $c5 = 1;}
            $generoEmpl = $_POST['geneEmpl'];   $operGene = $_POST['selGene'];      if($generoEmpl != null){$campo6 = 'Idegen'; $c6 = 1;}
            $documEmpl = $_POST['docuEmpl'];    $operDocu = $_POST['selDocu'];      if($documEmpl != null){$campo7 = 'Ideced'; $c7 = 1;}
            $codNoEmpl = $_POST['codiEmpl'];    $operCode = $_POST['selCode'];      if($codNoEmpl != null){$campo8 = 'Ideuse'; $c8 = 1;}
            $tienePass = $_POST['passEmpl'];    $operPasa = $_POST['selPasa'];      if($tienePass != null){$campo9 = 'Idepas'; $c9 = 1;}
            $tieneVisa = $_POST['visaEmpl'];    $operVisa = $_POST['selVisa'];      if($tieneVisa != null){$campo10 = 'Idevis'; $c10 = 1;}
            $estaCivil = $_POST['estCivil'];    $operEsCi = $_POST['selEstc'];      if($estaCivil != null){$campo11 = 'Ideesc'; $c11 = 1;}
            $estrato = $_POST['estrato'];       $operEstr = $_POST['selEst'];       if($estrato != null){$campo12 = 'Idestt'; $c12 = 1;}
            $luNa = $_POST['lugNac'];           $operLuNa = $_POST['selLuNa'];      if($luNa != null){$campo13 = 'Ideinc'; $c13 = 1;}
            $munRes = $_POST['munRes'];         $operMunR = $_POST['selMunRes'];    if($munRes != null){$campo14 = 'Idempo'; $c14 = 1;}
            $bar = $_POST['barrio'];            $barr = explode("-", $bar);         $desBarrio = $barr[1];
            $barrio = $barr[0];                 $operBarr = $_POST['selBarrio'];    if($barrio != null){$campo15 = 'Idebrr'; $c15 = 1;}
            $tipoSan = $_POST['tipoSan'];       $operTiSa = $_POST['selTipoSa'];    if($tipoSan != null){$campo16 = 'Idesrh'; $c16 = 1;}
            $graEsEmp = $_POST['graEscEmp'];    $operGrae = $_POST['selGrEsEm'];    if($graEsEmp != null){$campo17 = 'Edugrd'; $c17 = 1;}
            $titObEmp = $_POST['titObEmp'];     $operTiOb = $_POST['selTitOb'];     if($titObEmp != null){$campo18 = 'Edutit'; $c18 = 1;}
            $nomInsti = $_POST['nomInst'];      $operNoIn = $_POST['selNoInst'];    if($nomInsti != null){$campo19 = 'Eduins'; $c19 = 1;}
            $fecTit = $_POST['fecTit'];         $operFeTi = $_POST['selFecTit'];    if($fecTit != null){$campo20 = 'Eduani'; $c20 = 1;}
            $idiEmp = $_POST['usuIdioma'];      $operIdio = $_POST['selIdioma'];    if($idiEmp != null){$campo21 = 'Idides'; $c21 = 1;}
            $idiHab = $_POST['usuIdiHab'];      $operIdih = $_POST['selIdiHab'];    if($idiHab != null){$campo22 = 'Idihab'; $c22 = 1;}
            $idiLee = $_POST['usuIdiLee'];      $operIdiL = $_POST['selIdiLee'];    if($idiLee != null){$campo23 = 'Idilee'; $c23 = 1;}
            $idiEsc = $_POST['usuIdiEsc'];      $operIdiE = $_POST['selIdiEsc'];    if($idiEsc != null){$campo24 = 'Idiesc'; $c24 = 1;}
            $estAct = $_POST['usuEstAct'];      $operEsAc = $_POST['selEstAct'];    if($estAct != null){$campo25 = 'Nesdes'; $c25 = 1;}
            $estInst = $_POST['usuEstInst'];    $operEsIn = $_POST['selEstInst'];   if($estInst != null){$campo26 = 'Nesins'; $c26 = 1;}
            $viveEmp = $_POST['usuVive'];                                           if($viveEmp != null){$campo27 = 'Famaco'; $c27 = 1;}
            $cabFami = $_POST['usuCabFam'];     $operCabFami = $_POST['selCabFam']; if($cabFami != null){$campo28 = 'Famcab'; $c28 = 1;}
            $niCargo = $_POST['usuNiCar'];      $operNiCargo = $_POST['selNiCar'];  if($niCargo != null){$campo29 = 'Fammac'; $c29 = 1;}
            $adCargo = $_POST['usuAdCar'];      $operAdCargo = $_POST['selAdCar'];  if($adCargo != null){$campo30 = 'Famaac'; $c30 = 1;}
            $generoNu = $_POST['generoNu'];     $operGenNu = $_POST['selGenNu'];    if($generoNu != null){$campo31 = 'Grugen'; $c31 = 1;}
            $parNu = $_POST['parNu'];           $operParNu = $_POST['selParNu'];    if($parNu != null){$campo32 = 'Grupar'; $c32 = 1;}
            $fnacNu = $_POST['fnacNu'];         $operFecNu = $_POST['selFecNu'];    if($fnacNu != null){$campo33 = 'Grufna'; $c33 = 1;}
            $niEdNu = $_POST['niEdNu'];         $operNivNu = $_POST['selNivNu'];    if($niEdNu != null){$campo34 = 'Gruesc'; $c34 = 1;}
            $nucOcupa = $_POST['nucOcupa'];     $operOcuNu = $_POST['selOcuNu'];    if($nucOcupa != null){$campo35 = 'Gruocu'; $c35 = 1;}
            $nuVive = $_POST['nuVive'];         $operVivNu = $_POST['selVivNu'];    if($nuVive != null){$campo36 = 'Grucom'; $c36 = 1;}
            $nuDisca = $_POST['nuDis'];         $operDisNu = $_POST['selDisNu'];    if($nuDisca != null){$campo37 = 'Famtpd'; $c37 = 1;}
            $nuMasco = $_POST['nuMas'];         $operMasNu = $_POST['selMasNu'];    if($nuMasco != null){$campo38 = 'Famtms'; $c38 = 1;}
            $epsAct = $_POST['epsAct'];         $operEpsAct = $_POST['selEpsAct'];  if($epsAct != null){$campo39 = 'Ideeps'; $c39 = 1;}
            $epsCom = $_POST['epsCom'];         $opeEpsCom = $_POST['selEpsCom'];   if($epsCom != null){$campo40 = 'Idescs'; $c40 = 1;}
            $gasViv = $_POST['gasViv'];         $operGasViv = $_POST['selGasViv'];  if($gasViv != null){$campo41 = 'Usugasto'; $c41 = 1;}
            $gasCuo = $_POST['gasCuo'];         $selGasCuo = $_POST['selGasCuo'];   if($gasCuo != null){$campo42 = 'Usugasto'; $c42 = 1;}
            $gasAli = $_POST['gasAli'];         $selGasAli = $_POST['selGasAli'];   if($gasAli != null){$campo43 = 'Usugasto'; $c43 = 1;}
            $gasSer = $_POST['gasSer'];         $operGasSer = $_POST['selGasSer'];  if($gasSer != null){$campo44 = 'Usugasto'; $c44 = 1;}
            $gasTra = $_POST['gasTra'];         $operGasTra = $_POST['selGasTra'];  if($gasTra != null){$campo45 = 'Usugasto'; $c45 = 1;}
            $gasEdp = $_POST['gasEdp'];         $operGasEdp = $_POST['selGasEdp'];  if($gasEdp != null){$campo46 = 'Usugasto'; $c46 = 1;}
            $gasEdh = $_POST['gasEdh'];         $operGasEdh = $_POST['selGasEdh'];  if($gasEdh != null){$campo47 = 'Usugasto'; $c47 = 1;}
            $gasPac = $_POST['gasPac'];         $operGasPac = $_POST['selGasPac'];  if($gasPac != null){$campo48 = 'Usugasto'; $c48 = 1;}
            $gasTil = $_POST['gasTil'];         $operGasTil = $_POST['selGasTil'];  if($gasTil != null){$campo49 = 'Usugasto'; $c49 = 1;}
            $gasVes = $_POST['gasVes'];         $operGasVes = $_POST['selGasVes'];  if($gasVes != null){$campo50 = 'Usugasto'; $c50 = 1;}
            $gasSal = $_POST['gasSal'];         $operGasSal = $_POST['selGasSal'];  if($gasSal != null){$campo51 = 'Usugasto'; $c51 = 1;}
            $gasCel = $_POST['gasCel'];         $operGasCel = $_POST['selGasCel'];  if($gasCel != null){$campo52 = 'Usugasto'; $c52 = 1;}
            $gasPtc = $_POST['gasPtc'];         $operGasPtc = $_POST['selGasPtc'];  if($gasPtc != null){$campo53 = 'Usugasto'; $c53 = 1;}
            $gasCte = $_POST['gasCte'];         $operGasCte = $_POST['selGasCte'];  if($gasCte != null){$campo54 = 'Usugasto'; $c54 = 1;}
            $gasBel = $_POST['gasBel'];         $operGasBel = $_POST['selGasBel'];  if($gasBel != null){$campo55 = 'Usugasto'; $c55 = 1;}
            $sitDeu = $_POST['sitDeu'];         $operSitPtc = $_POST['selSitPtc'];  if($sitDeu != null){$campo56 = 'Ususitua'; $c56 = 1;}
            $sitPco = $_POST['sitPco'];         $operSitPco = $_POST['selSitPco'];  if($sitPco != null){$campo57 = 'Ususitua'; $c57 = 1;}
            $sitDec = $_POST['sitDec'];         $operSitDec = $_POST['selSitDec'];  if($sitDec != null){$campo58 = 'Ususitua'; $c58 = 1;}
            $sitDef = $_POST['sitDef'];         $operSitDef = $_POST['selSitDef'];  if($sitDef != null){$campo59 = 'Ususitua'; $c59 = 1;}
            $sitHch = $_POST['sitHch'];         $operSitHch = $_POST['selSitHch'];  if($sitHch != null){$campo60 = 'Ususitua'; $c60 = 1;}
            $sitSep = $_POST['sitSep'];         $operSitSep = $_POST['selSitSep'];  if($sitSep != null){$campo61 = 'Ususitua'; $c61 = 1;}
            $sitVio = $_POST['sitVio'];         $operSitVio = $_POST['selSitVio'];  if($sitVio != null){$campo62 = 'Ususitua'; $c62 = 1;}
            $sitAdi = $_POST['sitAdi'];         $operSitAdi = $_POST['selSitAdi'];  if($sitAdi != null){$campo63 = 'Ususitua'; $c63 = 1;}
            $sitMsq = $_POST['sitMsq'];         $operSitMsq = $_POST['selSitMsq'];  if($sitMsq != null){$campo64 = 'Ususitua'; $c64 = 1;}
            ?>
        </section>
        <section>
            <?php
            $sitEng = $_POST['sitEng'];         $operSitEng = $_POST['selSitEng'];  if($sitEng != null){$campo65 = 'Ususitua'; $c65 = 1;}
            $sitNin = $_POST['sitNin'];         $operSitNin = $_POST['selSitNin'];  if($sitNin != null){$campo66 = 'Ususitua'; $c66 = 1;}
            $posFam = $_POST['posFam'];                                             if($posFam != null){$campo67 = 'Usuposfam'; $c67 = 1;}
            $cuhAbu = $_POST['cuhAbu'];         $operCuhAbu = $_POST['selCuhAbu'];  if($cuhAbu != null){$campo68 = 'Usucuhij'; $c68 = 1;}
            $cuhPma = $_POST['cuhPma'];         $operCuhPma = $_POST['selCuhPma'];  if($cuhPma != null){$campo69 = 'Usucuhij'; $c69 = 1;}
            $cuhVec = $_POST['cuhVec'];         $operCuhVec = $_POST['selCuhVec'];  if($cuhVec != null){$campo70 = 'Usucuhij'; $c70 = 1;}
            $cuhGui = $_POST['cuhGui'];         $operCuhGui = $_POST['selCuhGui'];  if($cuhGui != null){$campo71 = 'Usucuhij'; $c71 = 1;}
            $cuhEmd = $_POST['cuhEmd'];         $operCuhEmd = $_POST['selCuhEmd'];  if($cuhEmd != null){$campo72 = 'Usucuhij'; $c72 = 1;}
            $cuhFam = $_POST['cuhFam'];         $operCuhFam = $_POST['selCuhFam'];  if($cuhFam != null){$campo73 = 'Usucuhij'; $c73 = 1;}
            $cuhQso = $_POST['cuhQso'];         $operCuhQso = $_POST['selCuhQso'];  if($cuhQso != null){$campo74 = 'Usucuhij'; $c74 = 1;}
            $cuhOtr = $_POST['cuhOtr'];         $operCuhOtr = $_POST['selCuhOtr'];  if($cuhOtr != null){$campo75 = 'Usucuhij'; $c75 = 1;}
            $vivTen = $_POST['vivTen'];         $operVivTen = $_POST['selVivTen'];  if($vivTen != null){$campo76 = 'Cviviv'; $c76 = 1;}
            $vivTiv = $_POST['vivTiv'];         $operVivTiv = $_POST['selVivTiv'];  if($vivTiv != null){$campo77 = 'Cvitvi'; $c77 = 1;}
            $vivTte = $_POST['vivTte'];         $operVivTte = $_POST['selVivTte'];  if($vivTte != null){$campo78 = 'Cvitrz'; $c78 = 1;}
            $vivTlo = $_POST['vivTlo'];         $operVivTlo = $_POST['selVivTlo'];  if($vivTlo != null){$campo79 = 'Cvilot'; $c79 = 1;}
            $vivEst = $_POST['vivEst'];         $operVivEst = $_POST['selVivEst'];  if($vivEst != null){$campo80 = 'Cvisvi'; $c80 = 1;}
            $serAcu = $_POST['serAcu'];         $operSerAcu = $_POST['selSerAcu'];  if($serAcu != null){$campo81 = 'Cvissp'; $c81 = 1;}
            $serAlc = $_POST['serAlc'];         $operSerAlc = $_POST['selSerAlc'];  if($serAlc != null){$campo82 = 'Cvissp'; $c82 = 1;}
            $serAse = $_POST['serAse'];         $operSerAse = $_POST['selSerAse'];  if($serAse != null){$campo83 = 'Cvissp'; $c83 = 1;}
            $serEne = $_POST['serEne'];         $operSerEne = $_POST['selSerEne'];  if($serEne != null){$campo84 = 'Cvissp'; $c84 = 1;}
            $serInt = $_POST['serInt'];         $operSerInt = $_POST['selSerInt'];  if($serInt != null){$campo85 = 'Cvissp'; $c85 = 1;}
            $serRga = $_POST['serRga'];         $operSerRga = $_POST['selSerRga'];  if($serRga != null){$campo86 = 'Cvissp'; $c86 = 1;}
            $serTel = $_POST['serTel'];         $operSerTel = $_POST['selSerTel'];  if($serTel != null){$campo87 = 'Cvissp'; $c87 = 1;}
            $ahvSub = $_POST['ahvSub'];         $operAhvSub = $_POST['selAhvSub'];  if($ahvSub != null){$campo88 = 'Ususubvi'; $c88 = 1;}
            $ahvAho = $_POST['ahvAho'];         $operAhvAho = $_POST['selAhvAho'];  if($ahvAho != null){$campo89 = 'Usuahviv'; $c89 = 1;}
            $ahvCua = $_POST['ahvCua'];         $operAhvCua = $_POST['selAhvCua'];  if($ahvCua != null){$campo90 = 'Usumonaho'; $c90 = 1;}
            $farInu = $_POST['farInu'];         $operFarInu = $_POST['selFarInu'];  if($farInu != null){$campo91 = 'Usfariesvi'; $c91 = 1;}
            $farCon = $_POST['farCon'];         $operFarCon = $_POST['selFarCon'];  if($farCon != null){$campo92 = 'Usfariesvi'; $c92 = 1;}
            $farRia = $_POST['farRia'];         $operFarRia = $_POST['selFarRia'];  if($farRia != null){$campo93 = 'Usfariesvi'; $c93 = 1;}
            $farRie = $_POST['farRie'];         $operFarRie = $_POST['selFarRie'];  if($farRie != null){$campo94 = 'Usfariesvi'; $c94 = 1;}
            $farRis = $_POST['farRis'];         $operFarRis = $_POST['selFarRis'];  if($farRis != null){$campo95 = 'Usfariesvi'; $c95 = 1;}
            $farRip = $_POST['farRip'];         $operFarRip = $_POST['selFarRip'];  if($farRip != null){$campo96 = 'Usfariesvi'; $c96 = 1;}
            $farNot = $_POST['farNot'];         $operFarNot = $_POST['selFarNot'];  if($farNot != null){$campo97 = 'Usfariesvi'; $c97 = 1;}
            $nemEst = $_POST['nemEst'];                                             if($nemEst != null){$campo98 = 'Usumejovi'; $c98 = 1;}
            $nemMue = $_POST['nemMue'];                                             if($nemMue != null){$campo99 = 'Usumejovi'; $c99 = 1;}
            $nemEle = $_POST['nemEle'];                                             if($nemEle != null){$campo100 = 'Usumejovi'; $c100 = 1;}
            $nemPis = $_POST['nemPis'];                                             if($nemPis != null){$campo101 = 'Usumejovi'; $c101 = 1;}
            $nemPar = $_POST['nemPar'];                                             if($nemPar != null){$campo102 = 'Usumejovi'; $c102 = 1;}
            $nemCol = $_POST['nemCol'];                                             if($nemCol != null){$campo103 = 'Usumejovi'; $c103 = 1;}
            $nemHum = $_POST['nemHum'];                                             if($nemHum != null){$campo104 = 'Usumejovi'; $c104 = 1;}
            $nemFac = $_POST['nemFac'];                                             if($nemFac != null){$campo105 = 'Usumejovi'; $c105 = 1;}
            $nemTec = $_POST['nemTec'];                                             if($nemTec != null){$campo106 = 'Usumejovi'; $c106 = 1;}
            $nemBan = $_POST['nemBan'];                                             if($nemBan != null){$campo107 = 'Usumejovi'; $c107 = 1;}
            $nemCoc = $_POST['nemCoc'];                                             if($nemCoc != null){$campo108 = 'Usumejovi'; $c108 = 1;}
            $nemAmp = $_POST['nemAmp'];                                             if($nemAmp != null){$campo109 = 'Usumejovi'; $c109 = 1;}
            $nemNot = $_POST['nemNot'];                                             if($nemNot != null){$campo110 = 'Usumejovi'; $c110 = 1;}
            $creAct = $_POST['creAct'];         $operCreAct = $_POST['selCreAct'];  if($creAct != null){$campo111 = 'Cremot'; $c111 = 1;}
            $prfCan = $_POST['prfCan'];         $operPrfCan = $_POST['selPrfCan'];  if($prfCan != null){$campo112 = 'Usuprofi'; $c112 = 1;}
            $prfCuc = $_POST['prfCuc'];         $operPrfCuc = $_POST['selPrfCuc'];  if($prfCuc != null){$campo113 = 'Usuprofi'; $c113 = 1;}
            $prfTac = $_POST['prfTac'];         $operPrfTac = $_POST['selPrfTac'];  if($prfTac != null){$campo114 = 'Usuprofi'; $c114 = 1;}
            $prfCrc = $_POST['prfCrc'];         $operPrfCrc = $_POST['selPrfCrc'];  if($prfCrc != null){$campo115 = 'Usuprofi'; $c115 = 1;}
            $prfCrh = $_POST['prfCrh'];         $operPrfCrh = $_POST['selPrfCrh'];  if($prfCrh != null){$campo116 = 'Usuprofi'; $c116 = 1;}
            $prfCrv = $_POST['prfCrv'];         $operPrfCrv = $_POST['selPrfCrv'];  if($prfCrv != null){$campo117 = 'Usuprofi'; $c117 = 1;}
            $prfInv = $_POST['prfInv'];         $operPrfInv = $_POST['selPrfInv'];  if($prfInv != null){$campo118 = 'Usuprofi'; $c118 = 1;}
            $prfSeg = $_POST['prfSeg'];         $operPrfSeg = $_POST['selPrfSeg'];  if($prfSeg != null){$campo119 = 'Usuprofi'; $c119 = 1;}
            $prfNin = $_POST['prfNin'];         $operPrfNin = $_POST['selPrfNin'];  if($prfNin != null){$campo120 = 'Usuprofi'; $c120 = 1;}
            ?>
        </section>
        <section>
            <?php
            $mocViv = $_POST['mocViv'];         $operMocViv = $_POST['selMocViv'];  if($mocViv != null){$campo121 = 'Usumocre'; $c121 = 1;}
            $mocTec = $_POST['mocTec'];         $operMocTec = $_POST['selMocTec'];  if($mocTec != null){$campo122 = 'Usumocre'; $c122 = 1;}
            $mocMue = $_POST['mocMue'];         $operMocMue = $_POST['selMocMue'];  if($mocMue != null){$campo123 = 'Usumocre'; $c123 = 1;}
            $mocEle = $_POST['mocEle'];         $operMocEle = $_POST['selMocEle'];  if($mocEle != null){$campo124 = 'Usumocre'; $c124 = 1;}
            $mocVeh = $_POST['mocVeh'];         $operMocVeh = $_POST['selMocVeh'];  if($mocVeh != null){$campo125 = 'Usumocre'; $c125 = 1;}
            $mocSal = $_POST['mocSal'];         $operMocSal = $_POST['selMocSal'];  if($mocSal != null){$campo126 = 'Usumocre'; $c126 = 1;}
            $mocCir = $_POST['mocCir'];         $operMocCir = $_POST['selMocCir'];  if($mocCir != null){$campo127 = 'Usumocre'; $c127 = 1;}
            $mocTur = $_POST['mocTur'];         $operMocTur = $_POST['selMocTur'];  if($mocTur != null){$campo128 = 'Usumocre'; $c128 = 1;}
            $mocLib = $_POST['mocLib'];         $operMocLib = $_POST['selMocLib'];  if($mocLib != null){$campo129 = 'Usumocre'; $c129 = 1;}
            $mocGas = $_POST['mocGas'];         $operMocGas = $_POST['selMocGas'];  if($mocGas != null){$campo130 = 'Usumocre'; $c130 = 1;}
            $mocTac = $_POST['mocTac'];         $operMocTac = $_POST['selMocTac'];  if($mocTac != null){$campo131 = 'Usumocre'; $c131 = 1;}
            $mocEdp = $_POST['mocEdp'];         $operMocEdp = $_POST['selMocEdp'];  if($mocEdp != null){$campo132 = 'Usumocre'; $c132 = 1;}
            $mocEdf = $_POST['mocEdf'];         $operMocEdf = $_POST['selMocEdf'];  if($mocEdf != null){$campo133 = 'Usumocre'; $c133 = 1;}
            $mocCre = $_POST['mocCre'];         $operMocCre = $_POST['selMocCre'];  if($mocCre != null){$campo134 = 'Usumocre'; $c134 = 1;}
            $mocNin = $_POST['mocNin'];         $operMocNin = $_POST['selMocNin'];  if($mocNin != null){$campo135 = 'Usumocre'; $c135 = 1;}
            $eacBac = $_POST['eacBac'];         $operEacBac = $_POST['selEacBac'];  if($eacBac != null){$campo136 = 'Usepacu'; $c136 = 1;}
            $eacFoe = $_POST['eacFoe'];         $operEacFoe = $_POST['selEacFoe'];  if($eacFoe != null){$campo137 = 'Usepacu'; $c137 = 1;}
            $aecFom = $_POST['aecFom'];         $operEacFom = $_POST['selEacFom'];  if($aecFom != null){$campo138 = 'Usepacu'; $c138 = 1;}
            $eacPgg = $_POST['eacPgg'];         $operEacPgg = $_POST['selEacPgg'];  if($eacPgg != null){$campo139 = 'Usepacu'; $c139 = 1;}
            $eacFam = $_POST['eacFam'];         $operEacFam = $_POST['selEacFam'];  if($eacFam != null){$campo140 = 'Usepacu'; $c140 = 1;}
            $eacCra = $_POST['eacCra'];         $operEacCra = $_POST['selEacCra'];  if($eacCra != null){$campo141 = 'Usepacu'; $c141 = 1;}
            $eacCac = $_POST['eacCac'];         $operEacCac = $_POST['selEacCac'];  if($eacCac != null){$campo142 = 'Usepacu'; $c142 = 1;}
            $eacEml = $_POST['eacEml'];         $operEacEml = $_POST['selEacEml'];  if($eacEml != null){$campo143 = 'Usepacu'; $c143 = 1;}
            $eacNat = $_POST['eacNat'];         $operEacNat = $_POST['selEacNat'];  if($eacNat != null){$campo144 = 'Usepacu'; $c144 = 1;}
            $eacOtr = $_POST['eacOtr'];         $operEacOtr = $_POST['selEacOtr'];  if($eacOtr != null){$campo145 = 'Usepacu'; $c145 = 1;}
            $eacNin = $_POST['eacNin'];         $operEacNin = $_POST['selEacNin'];  if($eacNin != null){$campo146 = 'Usepacu'; $c146 = 1;}
            $lciViv = $_POST['lciViv'];         $operLciViv = $_POST['selLciViv'];  if($lciViv != null){$campo147 = 'UsLcInt'; $c147 = 1;}
            $lciVeh = $_POST['lciVeh'];         $operLciVeh = $_POST['selLciVeh'];  if($lciVeh != null){$campo148 = 'UsLcInt'; $c148 = 1;}
            $lciSal = $_POST['lciSal'];         $operLciSal = $_POST['selLciSal'];  if($lciSal != null){$campo149 = 'UsLcInt'; $c149 = 1;}
            $lciCie = $_POST['lciCie'];         $operLciCie = $_POST['selLciCie'];  if($lciCie != null){$campo150 = 'UsLcInt'; $c150 = 1;}
            $lciTur = $_POST['lciTur'];         $operLciTur = $_POST['selLciTur'];  if($lciTur != null){$campo151 = 'UsLcInt'; $c151 = 1;}
            $lciEdf = $_POST['lciEdf'];         $operLciEdf = $_POST['selLciEdf'];  if($lciEdf != null){$campo152 = 'UsLcInt'; $c152 = 1;}
            $lciEdp = $_POST['lciEdp'];         $operLciEdp = $_POST['selLciEdp'];  if($lciEdp != null){$campo153 = 'UsLcInt'; $c153 = 1;}
            $lciCem = $_POST['lciCem'];         $operLciCem = $_POST['selLciCem'];  if($lciCem != null){$campo154 = 'UsLcInt'; $c154 = 1;}
            $lciMev = $_POST['lciMev'];         $operLciMev = $_POST['selLciMev'];  if($lciMev != null){$campo155 = 'UsLcInt'; $c155 = 1;}
            $lciCrr = $_POST['lciCrr'];         $operLciCrr = $_POST['selLciCrr'];  if($lciCrr != null){$campo156 = 'UsLcInt'; $c156 = 1;}
            $lciLib = $_POST['lciLib'];         $operLciLib = $_POST['selLciLib'];  if($lciLib != null){$campo157 = 'UsLcInt'; $c157 = 1;}
            $lciTac = $_POST['lciTac'];         $operLciTac = $_POST['selLciTac'];  if($lciTac != null){$campo158 = 'UsLcInt'; $c158 = 1;}
            $lciNin = $_POST['lciNin'];         $operLciNin = $_POST['selLciNin'];  if($lciNin != null){$campo159 = 'UsLcInt'; $c159 = 1;}
            $inaInv = $_POST['inaInv'];         $operInaInv = $_POST['selInaInv'];  if($inaInv != null){$campo160 = 'UsIaAho'; $c160 = 1;}
            $inaBan = $_POST['inaBan'];         $operInaBan = $_POST['selInaBan'];  if($inaBan != null){$campo161 = 'UsIaAho'; $c161 = 1;}
            $inaNat = $_POST['inaNat'];         $operInaNat = $_POST['selInaNat'];  if($inaNat != null){$campo162 = 'UsIaAho'; $c162 = 1;}
            $inaCoo = $_POST['inaCoo'];         $operInaCoo = $_POST['selInaCoo'];  if($inaCoo != null){$campo163 = 'UsIaAho'; $c163 = 1;}
            $inaFoe = $_POST['inaFoe'];         $operInaFoe = $_POST['selInaFoe'];  if($inaFoe != null){$campo164 = 'UsIaAho'; $c164 = 1;}
            $inaFom = $_POST['inaFom'];         $operInaFom = $_POST['selInaFom'];  if($inaFom != null){$campo165 = 'UsIaAho'; $c165 = 1;}
            $inaFvo = $_POST['inaFvo'];         $operInaFvo = $_POST['selInaFvo'];  if($inaFvo != null){$campo166 = 'UsIaAho'; $c166 = 1;}
            $inaOtr = $_POST['inaOtr'];         $operInaOtr = $_POST['selInaOtr'];  if($inaOtr != null){$campo167 = 'UsIaAho'; $c167 = 1;}
            $inaNoa = $_POST['inaNoa'];         $operInaNoa = $_POST['selInaNoa'];  if($inaNoa != null){$campo168 = 'UsIaAho'; $c168 = 1;}
            ?>
        </section>
        <section>
            <?php
            $trhBic = $_POST['trhBic'];         $operTrhBic = $_POST['selTrhBic'];  if($trhBic != null){$campo169 = 'Cvitra'; $c169 = 1;}
            $trhBus = $_POST['trhBus'];         $operTrhBus = $_POST['selTrhBus'];  if($trhBus != null){$campo170 = 'Cvitra'; $c170 = 1;}
            $trhCam = $_POST['trhCam'];         $operTrhCam = $_POST['selTrhCam'];  if($trhCam != null){$campo171 = 'Cvitra'; $c171 = 1;}
            $trhCap = $_POST['trhCap'];         $operTrhCap = $_POST['selTrhCap'];  if($trhCap != null){$campo172 = 'Cvitra'; $c172 = 1;}
            $trhMet = $_POST['trhMet'];         $operTrhMet = $_POST['selTrhMet'];  if($trhMet != null){$campo173 = 'Cvitra'; $c173 = 1;}
            $trhMot = $_POST['trhMot'];         $operTrhMot = $_POST['selTrhMot'];  if($trhMot != null){$campo174 = 'Cvitra'; $c174 = 1;}
            $trhOtr = $_POST['trhOtr'];         $operTrhOtr = $_POST['selTrhOtr'];  if($trhOtr != null){$campo175 = 'Cvitra'; $c175 = 1;}
            $trhTax = $_POST['trhTax'];         $operTrhTax = $_POST['selTrhTax'];  if($trhTax != null){$campo176 = 'Cvitra'; $c176 = 1;}
            $trhTrc = $_POST['trhTrc'];         $operTrhTrc = $_POST['selTrhTrc'];  if($trhTrc != null){$campo177 = 'Cvitra'; $c177 = 1;}
            $trhOtc = $_POST['trhOtc'];         $operTrhOtc = $_POST['selTrhOtc'];  if($trhOtc != null){$campo178 = 'Cviotr'; $c178 = 1;}
            $trhPar = $_POST['trhPar'];         $operTrhPar = $_POST['selTrhPar'];  if($trhPar != null){$campo179 = 'Otrpar'; $c179 = 1;}
            $trhTid = $_POST['trhTid'];         $operTrhTid = $_POST['selTrhTid'];  if($trhTid != null){$campo180 = 'OtrTime'; $c180 = 1;}
            $trhTur = $_POST['trhTur'];         $operTrhTur = $_POST['selTrhTur'];  if($trhTur != null){$campo181 = 'OtrTurno'; $c181 = 1;}
            $aclAte = $_POST['aclAte'];         $operAclAte = $_POST['selAclAte'];  if($aclAte != null){$campo182 = 'OtrExtra'; $c182 = 1;}
            $aclOta = $_POST['aclOta'];         $operAclOta = $_POST['selAclOta'];  if($aclOta != null){$campo183 = 'OtrExOtra'; $c183 = 1;}
            $aclRan = $_POST['aclRan'];         $operAclRan = $_POST['selAclRan'];  if($aclRan != null){$campo184 = 'OtrSalar'; $c184 = 1;}
            $nefCae = $_POST['nefCae'];         $operNefCae = $_POST['selNefCae'];  if($nefCae != null){$campo185 = 'UsNefor'; $c185 = 1;}
            $nefDec = $_POST['nefDec'];         $operNefDec = $_POST['selNefDec'];  if($nefDec != null){$campo186 = 'UsNefor'; $c186 = 1;}
            $nefRef = $_POST['nefRef'];         $operNefRef = $_POST['selNefRef'];  if($nefRef != null){$campo187 = 'UsNefor'; $c187 = 1;}
            $nefMac = $_POST['nefMac'];         $operNefMac = $_POST['selNefMac'];  if($nefMac != null){$campo188 = 'UsNefor'; $c188 = 1;}
            $nefFip = $_POST['nefFip'];         $operNefFip = $_POST['selNefFip'];  if($nefFip != null){$campo189 = 'UsNefor'; $c189 = 1;}
            $nefFtt = $_POST['nefFtt'];         $operNefFtt = $_POST['selNefFtt'];  if($nefFtt != null){$campo190 = 'UsNefor'; $c190 = 1;}
            $nefIdi = $_POST['nefIdi'];         $operNefIdi = $_POST['selNefIdi'];  if($nefIdi != null){$campo191 = 'UsNefor'; $c191 = 1;}
            $nefInt = $_POST['nefInt'];         $operNefInt = $_POST['selNefInt'];  if($nefInt != null){$campo192 = 'UsNefor'; $c192 = 1;}
            $nefFcr = $_POST['nefFcr'];         $operNefFcr = $_POST['selNefFcr'];  if($nefFcr != null){$campo193 = 'UsNefor'; $c193 = 1;}
            $nefOtr = $_POST['nefOtr'];         $operNefOtr = $_POST['selNefOtr'];  if($nefOtr != null){$campo194 = 'UsNefor'; $c194 = 1;}
            $nefNot = $_POST['nefNot'];         $operNefNot = $_POST['selNefNot'];  if($nefNot != null){$campo195 = 'UsNefor'; $c195 = 1;}
            $ingTac = $_POST['ingTac'];         $operIngTac = $_POST['selIngTac'];  if($ingTac != null){$campo196 = 'Cvical'; $c196 = 1;}
            $ingCob = $_POST['ingCob'];         $operIngCob = $_POST['selIngCob'];  if($ingCob != null){$campo197 = 'Cvical'; $c197 = 1;}
            $ingCoo = $_POST['ingCoo'];         $operIngCoo = $_POST['selIngCoo'];  if($ingCoo != null){$campo198 = 'Cvical'; $c198 = 1;}
            $ingVac = $_POST['ingVac'];         $operIngVac = $_POST['selIngVac'];  if($ingVac != null){$campo199 = 'Cvical'; $c199 = 1;}
            $ingOtr = $_POST['ingOtr'];         $operIngOtr = $_POST['selIngOtr'];  if($ingOtr != null){$campo200 = 'Cvical'; $c200 = 1;}
            $acpTob = $_POST['acpTob'];         $operAcpTob = $_POST['selAcpTob'];  if($acpTob != null){$campo201 = 'Otractrec'; $c201 = 1;}
            $acpTop = $_POST['acpTop'];         $operAcpTop = $_POST['selAcpTop'];  if($acpTop != null){$campo202 = 'Otractrec'; $c202 = 1;}
            $acpTov = $_POST['acpTov'];         $operAcpTov = $_POST['selAcpTov'];  if($acpTov != null){$campo203 = 'Otractrec'; $c203 = 1;}
            $acpTba = $_POST['acpTba'];         $operAcpTba = $_POST['selAcpTba'];  if($acpTba != null){$campo204 = 'Otractrec'; $c204 = 1;}
            $acpTot = $_POST['acpTot'];         $operAcpTot = $_POST['selAcpTot'];  if($acpTot != null){$campo205 = 'Otractrec'; $c205 = 1;}
            $acpCam = $_POST['acpCam'];         $operAcpCam = $_POST['selAcpCam'];  if($acpCam != null){$campo206 = 'Otractrec'; $c206 = 1;}
            $acpBai = $_POST['acpBai'];         $operAcpBai = $_POST['selAcpBai'];  if($acpBai != null){$campo207 = 'Otractrec'; $c207 = 1;}
            $acpYog = $_POST['acpYog'];         $operAcpYog = $_POST['selAcpYog'];  if($acpYog != null){$campo208 = 'Otractrec'; $c208 = 1;}
            $actEnp = $_POST['actEnp'];         $operActEnp = $_POST['selActEnp'];  if($actEnp != null){$campo209 = 'Otractrec'; $c209 = 1;}
            $acpCic = $_POST['acpCic'];         $operAcpCic = $_POST['selAcpCic'];  if($acpCic != null){$campo210 = 'Otractrec'; $c210 = 1;}
            $acpMar = $_POST['acpMar'];         $operAcpMar = $_POST['selAcpMar'];  if($acpMar != null){$campo211 = 'Otractrec'; $c211 = 1;}
            $acpTho = $_POST['acpTho'];         $operAcpTho = $_POST['selAcpTho'];  if($acpTho != null){$campo212 = 'Otractrec'; $c212 = 1;}
            $acpGte = $_POST['acpGte'];         $operAcpGte = $_POST['selAcpGte'];  if($acpGte != null){$campo213 = 'Otractrec'; $c213 = 1;}
            $acpArp = $_POST['acpArp'];         $operAcpArp = $_POST['selAcpArp'];  if($acpArp != null){$campo214 = 'Otractrec'; $c214 = 1;}
            $acpMnu = $_POST['acpMnu'];         $operAcpMnu = $_POST['selAcpMnu'];  if($acpMnu != null){$campo215 = 'Otractrec'; $c215 = 1;}
            $acpGas = $_POST['acpGas'];         $operAcpGas = $_POST['selAcpGas'];  if($acpGas != null){$campo216 = 'Otractrec'; $c216 = 1;}
            $acpCli = $_POST['acpCli'];         $operAcpCli = $_POST['selAcpCli'];  if($acpCli != null){$campo217 = 'Otractrec'; $c217 = 1;}
            $acpCop = $_POST['acpCop'];         $operAcpCop = $_POST['selAcpCop'];  if($acpCop != null){$campo218 = 'Otractrec'; $c218 = 1;}
            $acpTpi = $_POST['acpTpi'];         $operAcpTpi = $_POST['selAcpTpi'];  if($acpTpi != null){$campo219 = 'Otractrec'; $c219 = 1;}
            $acpOtr = $_POST['acpOtr'];         $operAcpOtr = $_POST['selAcpOtr'];  if($acpOtr != null){$campo220 = 'Otractrec'; $c220 = 1;}
            $pacDia = $_POST['pacDia'];         $operPacDia = $_POST['selPacDia'];  if($pacDia != null){$campo221 = 'Otractrechor'; $c221 = 1;}
            $pacHor = $_POST['pacHor'];         $operPacHor = $_POST['selPacHor'];  if($pacHor != null){$campo222 = 'Otractcul'; $c222 = 1;}
            ?>
        </section>
        <section>
            <?php
            $qhtCin = $_POST['qhtCin'];         $operQhtCin = $_POST['selQhtCin'];  if($qhtCin != null){$campo223 = 'UsHobie'; $c223 = 1;}
            $nefDec = $_POST['nefDec'];         $operNefDec = $_POST['selNefDec'];  if($nefDec != null){$campo224 = 'UsHobie'; $c224 = 1;}
            $qhtVij = $_POST['qhtVij'];         $operQhtVij = $_POST['selQhtVij'];  if($qhtVij != null){$campo225 = 'UsHobie'; $c225 = 1;}
            $qhtVte = $_POST['qhtVte'];         $operQhtVte = $_POST['selQhtVte'];  if($qhtVte != null){$campo226 = 'UsHobie'; $c226 = 1;}
            $qhtNai = $_POST['qhtNai'];         $operQhtNai = $_POST['selQhtNai'];  if($qhtNai != null){$campo227 = 'UsHobie'; $c227 = 1;}
            $qhtIcc = $_POST['qhtIcc'];         $operQhtIcc = $_POST['selQhtIcc'];  if($qhtIcc != null){$campo228 = 'UsHobie'; $c228 = 1;}
            $qhtIpa = $_POST['qhtIpa'];         $operQhtIpa = $_POST['selQhtIpa'];  if($qhtIpa != null){$campo229 = 'UsHobie'; $c229 = 1;}
            $qhtIfi = $_POST['qhtIfi'];         $operQhtIfi = $_POST['selQhtIfi'];  if($qhtIfi != null){$campo230 = 'UsHobie'; $c230 = 1;}
            $qhtCle = $_POST['qhtCle'];         $operQhtCle = $_POST['selQhtCle'];  if($qhtCle != null){$campo231 = 'UsHobie'; $c231 = 1;}
            $qhtDed = $_POST['qhtDed'];         $operQhtDed = $_POST['selQhtDed'];  if($qhtDed != null){$campo232 = 'UsHobie'; $c232 = 1;}
            $qhtJar = $_POST['qhtJar'];         $operQhtJar = $_POST['selQhtJar'];  if($qhtJar != null){$campo233 = 'UsHobie'; $c233 = 1;}
            $qhtCon = $_POST['qhtCon'];         $operQhtCon = $_POST['selQhtCon'];  if($qhtCon != null){$campo234 = 'UsHobie'; $c234 = 1;}
            $qhtPin = $_POST['qhtPin'];         $operQhtPin = $_POST['selQhtPin'];  if($qhtPin != null){$campo235 = 'UsHobie'; $c235 = 1;}
            $qhtEsc = $_POST['qhtEsc'];         $operQhtEsc = $_POST['selQhtEsc'];  if($qhtEsc != null){$campo236 = 'UsHobie'; $c236 = 1;}
            $qhtFot = $_POST['qhtFot'];         $operQhtFot = $_POST['selQhtFot'];  if($qhtFot != null){$campo237 = 'UsHobie'; $c237 = 1;}
            $qhtVim = $_POST['qhtVim'];         $operQhtVim = $_POST['selQhtVim'];  if($qhtVim != null){$campo238 = 'UsHobie'; $c238 = 1;}
            $qhtVib = $_POST['qhtVib'];         $operQhtVib = $_POST['selQhtVib'];  if($qhtVib != null){$campo239 = 'UsHobie'; $c239 = 1;}
            $qhtEac = $_POST['qhtEac'];         $operQhtEac = $_POST['selQhtEac'];  if($qhtEac != null){$campo240 = 'UsHobie'; $c240 = 1;}
            $qhtDan = $_POST['qhtDan'];         $operQhtDan = $_POST['selQhtDan'];  if($qhtDan != null){$campo241 = 'UsHobie'; $c241 = 1;}
            $qhtTim = $_POST['qhtTim'];         $operQhtTim = $_POST['selQhtTim'];  if($qhtTim != null){$campo242 = 'UsHobie'; $c242 = 1;}
            $qhtCoc = $_POST['qhtCoc'];         $operQhtCoc = $_POST['selQhtCoc'];  if($qhtCoc != null){$campo243 = 'UsHobie'; $c243 = 1;}
            $qhtMnu = $_POST['qhtMnu'];         $operQhtMnu = $_POST['selQhtMnu'];  if($qhtMnu != null){$campo244 = 'UsHobie'; $c244 = 1;}
            $qhtOtr = $_POST['qhtOtr'];         $operQhtOtr = $_POST['selQhtOtr'];  if($qhtOtr != null){$campo245 = 'UsHobie'; $c245 = 1;}
            $qhtNin = $_POST['qhtNin'];         $operQhtNin = $_POST['selQhtNin'];  if($qhtNin != null){$campo246 = 'UsHobie'; $c246 = 1;}
            $ptlHij = $_POST['ptlHij'];         $operPtlHij = $_POST['selPtlHij'];  if($ptlHij != null){$campo247 = 'UsQpTie'; $c247 = 1;}
            $ptlAmi = $_POST['ptlAmi'];         $operPtlAmi = $_POST['selPtlAmi'];  if($ptlAmi != null){$campo248 = 'UsQpTie'; $c248 = 1;}
            $ptlMas = $_POST['ptlMas'];         $operPtlMas = $_POST['selPtlMas'];  if($ptlMas != null){$campo249 = 'UsQpTie'; $c249 = 1;}
            $ptlSol = $_POST['ptlSol'];         $operPtlSol = $_POST['selPtlSol'];  if($ptlSol != null){$campo250 = 'UsQpTie'; $c250 = 1;}
            $ptlFam = $_POST['ptlFam'];         $operPtlFam = $_POST['selPtlFam'];  if($ptlFam != null){$campo251 = 'UsQpTie'; $c251 = 1;}
            $ptlAmo = $_POST['ptlAmo'];         $operPtlAmo = $_POST['selPtlAmo'];  if($ptlAmo != null){$campo252 = 'UsQpTie'; $c252 = 1;}
            $ptlPar = $_POST['ptlPar'];         $operPtlPar = $_POST['selPtlPar'];  if($ptlPar != null){$campo253 = 'UsQpTie'; $c253 = 1;}
            $ptlCot = $_POST['ptlCot'];         $operPtlCot = $_POST['selPtlCot'];  if($ptlCot != null){$campo254 = 'UsQpTie'; $c254 = 1;}
            $ptlOtr = $_POST['ptlOtr'];         $operPtlOtr = $_POST['selPtlOtr'];  if($ptlOtr != null){$campo255 = 'UsQpTie'; $c255 = 1;}
            ?>
        </section>
        <section>
            <?php
            $qhhCin = $_POST['qhhCin'];         $operQhhCin = $_POST['selQhhCin'];  if($qhhCin != null){$campo256 = 'UsHhTli'; $c256 = 1;}
            $qhhDep = $_POST['qhhDep'];         $operQhhDep = $_POST['selQhhDep'];  if($qhhDep != null){$campo257 = 'UsHhTli'; $c257 = 1;}
            $qhhVij = $_POST['qhhVij'];         $operQhhVij = $_POST['selQhhVij'];  if($qhhVij != null){$campo258 = 'UsHhTli'; $c258 = 1;}
            $qhhVte = $_POST['qhhVte'];         $operQhhVte = $_POST['selQhhVte'];  if($qhhVte != null){$campo259 = 'UsHhTli'; $c259 = 1;}
            $qhhNai = $_POST['qhhNai'];         $operQhhNai = $_POST['selQhhNai'];  if($qhhNai != null){$campo260 = 'UsHhTli'; $c260 = 1;}
            $qhhIcc = $_POST['qhhIcc'];         $operQhhIcc = $_POST['selQhhIcc'];  if($qhhIcc != null){$campo261 = 'UsHhTli'; $c261 = 1;}
            $qhhIpa = $_POST['qhhIpa'];         $operQhhIpa = $_POST['selQhhIpa'];  if($qhhIpa != null){$campo262 = 'UsHhTli'; $c262 = 1;}
            $qhhIfi = $_POST['qhhIfi'];         $operQhhIfi = $_POST['selQhhIfi'];  if($qhhIfi != null){$campo263 = 'UsHhTli'; $c263 = 1;}
            $qhhCle = $_POST['qhhCle'];         $operQhhCle = $_POST['selQhhCle'];  if($qhhCle != null){$campo264 = 'UsHhTli'; $c264 = 1;}
            $qhhDed = $_POST['qhhDed'];         $operQhhDed = $_POST['selQhhDed'];  if($qhhDed != null){$campo265 = 'UsHhTli'; $c265 = 1;}
            $qhhJar = $_POST['qhhJar'];         $operQhhJar = $_POST['selQhhJar'];  if($qhhJar != null){$campo266 = 'UsHhTli'; $c266 = 1;}
            $qhhCon = $_POST['qhhCon'];         $operQhhCon = $_POST['selQhhCon'];  if($qhhCon != null){$campo267 = 'UsHhTli'; $c267 = 1;}
            $qhhPin = $_POST['qhhPin'];         $operQhhPin = $_POST['selQhhPin'];  if($qhhPin != null){$campo268 = 'UsHhTli'; $c268 = 1;}
            $qhhEsc = $_POST['qhhEsc'];         $operQhhEsc = $_POST['selQhhEsc'];  if($qhhEsc != null){$campo269 = 'UsHhTli'; $c269 = 1;}
            $qhhFot = $_POST['qhhFot'];         $operQhhFot = $_POST['selQhhFot'];  if($qhhFot != null){$campo270 = 'UsHhTli'; $c270 = 1;}
            $qhhVim = $_POST['qhhVim'];         $operQhhVim = $_POST['selQhhVim'];  if($qhhVim != null){$campo271 = 'UsHhTli'; $c271 = 1;}
            $qhhVib = $_POST['qhhVib'];         $operQhhVib = $_POST['selQhhVib'];  if($qhhVib != null){$campo272 = 'UsHhTli'; $c272 = 1;}
            $qhhEac = $_POST['qhhEac'];         $operQhhEac = $_POST['selQhhEac'];  if($qhhEac != null){$campo273 = 'UsHhTli'; $c273 = 1;}
            $qhhDan = $_POST['qhhDan'];         $operQhhDan = $_POST['selQhhDan'];  if($qhhDan != null){$campo274 = 'UsHhTli'; $c274 = 1;}
            $qhhTim = $_POST['qhhTim'];         $operQhhTim = $_POST['selQhhTim'];  if($qhhTim != null){$campo275 = 'UsHhTli'; $c275 = 1;}
            $qhhCoc = $_POST['qhhCoc'];         $operQhhCoc = $_POST['selQhhCoc'];  if($qhhCoc != null){$campo276 = 'UsHhTli'; $c276 = 1;}
            $qhhMnu = $_POST['qhhMnu'];         $operQhhMnu = $_POST['selQhhMnu'];  if($qhhMnu != null){$campo277 = 'UsHhTli'; $c277 = 1;}
            $qhhOtr = $_POST['qhhOtr'];         $operQhhOtr = $_POST['selQhhOtr'];  if($qhhOtr != null){$campo278 = 'UsHhTli'; $c278 = 1;}
            $qhhNin = $_POST['qhhNin'];         $operQhhNin = $_POST['selQhhNin'];  if($qhhNin != null){$campo279 = 'UsHhTli'; $c279 = 1;}
            $barFdi = $_POST['barFdi'];         $operBarFdi = $_POST['selBarFdi'];  if($barFdi != null){$campo280 = 'UsBuTli'; $c280 = 1;}
            $barNcd = $_POST['barNcd'];         $operBarNcd = $_POST['selBarNcd'];  if($barNcd != null){$campo281 = 'UsBuTli'; $c281 = 1;}
            $barDap = $_POST['barDap'];         $operBarDap = $_POST['selBarDap'];  if($barDap != null){$campo282 = 'UsBuTli'; $c282 = 1;}
            $barFms = $_POST['barFms'];         $operBarFms = $_POST['selBarFms'];  if($barFms != null){$campo283 = 'UsBuTli'; $c283 = 1;}
            $barNdt = $_POST['barNdt'];         $operBarNdt = $_POST['selBarNdt'];  if($barNdt != null){$campo284 = 'UsBuTli'; $c284 = 1;}
            $barOtr = $_POST['barOtr'];         $operBarOtr = $_POST['selBarOtr'];  if($barOtr != null){$campo285 = 'UsBuTli'; $c285 = 1;}
            $barNin = $_POST['barNin'];         $operBarNin = $_POST['selBarNin'];  if($barNin != null){$campo286 = 'UsBuTli'; $c286 = 1;}
            $rolAud = $_POST['rolAud'];         $operRolAud = $_POST['selRolAud'];  if($rolAud != null){$campo287 = 'Otrrol'; $c287 = 1;}
            $rolBre = $_POST['rolBre'];         $operRolBre = $_POST['selRolBre'];  if($rolBre != null){$campo288 = 'Otrrol'; $c288 = 1;}
            $rolOtr = $_POST['rolOtr'];         $operRolOtr = $_POST['selRolOtr'];  if($rolOtr != null){$campo289 = 'Otrrol'; $c289 = 1;}
            $rotOtc = $_POST['rotOtc'];         $operRotOtc = $_POST['selRotOtc'];  if($rotOtc != null){$campo290 = 'Otrroles'; $c290 = 1;}
            ?>
        </section>
        <section>
            <?php
            $pcoCco = $_POST['pcoCco'];        if($pcoCco != null){$c291 = 1;}
            $pcoGei = $_POST['pcoGei'];        if($pcoGei != null){$c292 = 1;}
            $pcoCdo = $_POST['pcoCdo'];        if($pcoCdo != null){$c293 = 1;}
            $pcoGte = $_POST['pcoGte'];        if($pcoGte != null){$c294 = 1;}
            $pcoCin = $_POST['pcoCin'];        if($pcoCin != null){$c295 = 1;}
            $pcoHca = $_POST['pcoHca'];        if($pcoHca != null){$c296 = 1;}
            $pcoCom = $_POST['pcoCom'];        if($pcoCom != null){$c297 = 1;}
            $pcoIin = $_POST['pcoIin'];        if($pcoIin != null){$c298 = 1;}
            $pcoCop = $_POST['pcoCop'];        if($pcoCop != null){$c299 = 1;}
            $pcoMtr = $_POST['pcoMtr'];        if($pcoMtr != null){$c300 = 1;}
            $pcoCre = $_POST['pcoCre'];        if($pcoCre != null){$c301 = 1;}
            $pcoMco = $_POST['pcoMco'];        if($pcoMco != null){$c302 = 1;}
            $pcoEin = $_POST['pcoEin'];        if($pcoEin != null){$c303 = 1;}
            $pcoMss = $_POST['pcoMss'];        if($pcoMss != null){$c304 = 1;}
            $barNin = $_POST['barNin'];        if($barNin != null){$c305 = 1;}
            $pcoSpa = $_POST['pcoSpa'];        if($pcoSpa != null){$c306 = 1;}
            $pcoEca = $_POST['pcoEca'];        if($pcoEca != null){$c307 = 1;}
            $pcoTra = $_POST['pcoTra'];        if($pcoTra != null){$c308 = 1;}
            $pcoFte = $_POST['pcoFte'];        if($pcoFte != null){$c309 = 1;}
            $pcoVep = $_POST['pcoVep'];        if($pcoVep != null){$c310 = 1;}
            $pcoGam = $_POST['pcoGam'];        if($pcoGam != null){$c311 = 1;}
            ?>
        </section>
        <section>
            <?php
            $q1 = "select * from talhuma_000013 WHERE Ideest = 'on'";
            if($c1 == 1){$par1 = "and $campo1 $operNom1 '$nom1Emple'";} else{$par1 = '';}       if($c2 == 1){$par2 = "and $campo2 $operNom2 '$nom2Emple'";} else{$par2 = '';}
            if($c3 == 1){$par3 = "and $campo3 $operApe1 '$apel1Emple'";} else{$par3 = '';}      if($c4 == 1){$par4 = "and $campo4 $operApe2 '$apel2Emple'";} else{$par4 = '';}
            if($c5 == 1){$par5 = "and $campo5 $operFnac '$fecNacEmpl'";} else{$par5 = '';}      if($c6 == 1){$par6 = "and $campo6 $operGene '$generoEmpl'";} else{$par6 = '';}
            if($c7 == 1){$par7 = "and $campo7 $operDocu '$documEmpl'";} else{$par7 = '';}       if($c8 == 1){$par8 = "and $campo8 $operCode '$codNoEmpl'";} else{$par8 = '';}
            if($c9 == 1){$par9 = "and $campo9 $operPasa '$tienePass'";} else{$par9 = '';}       if($c10 == 1){$par10 = "and $campo10 $operVisa '$tieneVisa'";} else{$par10 = '';}
            if($c11 == 1){$par11 = "and $campo11 $operEsCi '$estaCivil'";} else{$par11 = '';}   if($c12 == 1){$par12 = "and $campo12 $operEstr '$estrato'";} else{$par12 = '';}
            if($c13 == 1){$par13 = "and $campo13 $operLuNa '$luNa'";} else{$par13 = '';}        if($c14 == 1){$par14 = "and $campo14 $operMunR '$munRes'";} else{$par14 = '';}
            if($c15 == 1){$par15 = "and $campo15 $operBarr '$barrio'";}else{$par15 = '';}       if($c16 == 1){$par16 = "and $campo16 $operTiSa '$tipoSan'";} else{$par16 = '';}

            $query1User = "$q1 $par1 $par2 $par3 $par4 $par5 $par6 $par7 $par8 $par9 $par10 $par11 $par12 $par13 $par14 $par15 $par16";
            $queryFinal = mysql_query($query1User, $conex) or die (mysql_errno()." - en el query: ".$query1User." - ".mysql_error());
            $numUsers = mysql_num_rows($queryFinal);       //SABER SI HAY REGISTROS
            ?>
        </section>
        <!--  ///////////////////////////////////////////////////////////// -->

        <!-- INTERFACE: -->
        <!--<div style="width: 1470px; height: 700px; overflow-y: scroll; overflow-x: scroll; margin-left: -150px ;border: double">-->
            <div style="margin: auto auto auto 5px">
                <table id="tblDatos" border="1" style="padding-left: 5px" align="center">
                    <tr style="background-color: #2A5DB0; color:#ffffff" align="center">
                        <td><label>NOMBRE 1</label></td>                                    <td><label>NOMBRE 2</label></td>                                            <td><label>APELLIDO 1</label></td>
                        <td><label>APELLIDO 2</label></td>                                  <td><label>FECHA DE NACIMIENTO</label></td>                                 <td><label>GENERO</label></td>
                        <td><label>DOCUMENTO</label></td>                                   <td><label>CODIGO DE NOMINA</label></td>                                    <td><label>TIENE PASAPORTE</label></td>
                        <td><label>TIENE VISA</label></td>                                  <td><label>ESTADO CIVIL</label></td>                                        <td><label>ESTRATO</label></td>
                        <td><label>LUGAR DE NACIMIENTO</label></td>                         <td><label>MUNICIPIO DE RESIDENCIA</label></td>                             <td><label>BARRIO DE RESIDENCIA</label></td>
                        <td><label>TIPO DE SANGRE</label></td>                              <td><label>NIVEL EDUCATIVO</label></td>                                     <td><label>MANEJO DE OTROS IDIOMAS</label></td>
                        <td><label>ESTUDIOS ACTUALES</label></td>                           <td><label>CON QUIEN VIVE</label></td>                                      <td><label>PERSONAS CON DISCAPACIDAD</label></td>
                        <td><label>NUCLEO FAMILIAR</label></td>                             <td><label>EPS</label></td>                                                 <td><label>ASPECTOS PRINCIPALES EN LOS QUE GASTA SUS INGRESOS</label></td>
                        <td><label>SITUACIONES PRESENTES EN SU VIDA FAMILIAR</label></td>   <td><label>POSICION EN EL GRUPO FAMILIAR</label></td>                       <td><label>QUIEN QUEDA AL CUIDADO DE SUS HIJOS EN SU AUSENCIA </label></td>
                        <td><label>VIVIENDA</label></td>                                    <td><label>ACCESO A SERVICIOS PUBLICOS</label></td>                         <td><label>SUBSIDIOS / AHORROS PARA VIVIENDA</label></td>
                        <td><label>FACTORES DE RIESGO DE LA VIVIENDA</label></td>           <td><label>NECESIDADES DE MEJORAMIENTO DE LA VIVIENDA</label></td>          <td><label>INFORMACION CREDITOS</label></td>
                        <td><label>PRODUCTOS FINANCIEROS QUE POSEE</label></td>             <td><label>MOTIVO DE SUS CREDITOS ACTUALES</label></td>                     <td><label>A QUE ENTIDADES O PERSONAS ACUDE PARA ACCEDER A CREDITOS</label></td>
                        <td><label>LINEAS DE CREDITO DE INTERES</label></td>                <td><label>A TRAVES DE QUE INSTITUCIONES AHORRA</label></td>                <td><label>TRANSPORTE HABITUAL</label></td>
                        <td><label>OTRO TRANSPORTE</label></td>                             <td><label>LUGAR DONDE PARQUEA</label></td>                                 <td><label>TIEMPO DE DESPLAZAMIENTO A LA EMPRESA</label></td>
                        <td><label>TURNO HABITUAL DE TRABAJO</label></td>                   <td><label>ACTIVIDADES QUE REALIZA EN SU TIEMPO EXTRALABORAL</label></td>   <td><label>OTRA ACTIVIDAD LABORAL</label></td>
                        <td><label>RANGO SALARIAL</label></td>                              <td><label>NECESIDADES DE FORMACION</label></td>                            <td><label>HABITUALMENTE, A LA HORA DEL ALMUERZO</label></td>
                        <td><label>ACTIVIDADES EN LAS QUE PARTICIPARIA ACTIVAMENTE</label></td> <td><label>DIA / HORA DE PARTICIPACION EN ACTIVIDADES</label></td>      <td><label>QUE HACE EN SU TIEMPO LIBRE</label></td>
                        <td><label>CON QUIEN PASA SU TIEMPO LIBRE</label></td>              <td><label>QUE HACEN SUS HIJOS EN SU TIEMPO LIBRE</label></td>              <td><label>BARRERAS EN EL USO DEL TIEMPO LIBRE</label></td>
                        <td><label>ROL DESEMPEÑADO EN LA INSTITUCION</label></td>           <td><label>PERTENENCIA A COMITÉS</label></td>
                    </tr>
                    <?php
                    $row = 0;   //ID PARA LOS TR QUE SE VAN A BORRAR
                    while($datoUser = mysql_fetch_assoc($queryFinal))
                    {
                        /////////////////////////////////// ADECUACION PARAMETROS ///////////////////////////////////////////
                        $graEsEmp = '%'.$graEsEmp.'%';          $titObEmp = '%'.$titObEmp.'%';      if($operNoIn == 'LIKE'){$nomInsti = '%'.$nomInsti.'%';}
                        $estAct = '%'.$estAct.'%';              $estInst = '%'.$estInst.'%';        $viveEmp = '%'.$viveEmp.'%';    $epsCom = '%'.$epsCom.'%';
                        $trhOtc = '%'.$trhOtc.'%';              $trhPar = '%'.$trhPar.'%';          $aclOta = '%'.$aclOta.'%';
                        $rotOtc = '%'.$rotOtc.'%';
                        ?>
                        <!--///////////////////////////////////INFORMACION GENERAL//////////////////////////////////////-->
                        <section>
                            <?php
                            $nombre1Emp = $datoUser['Ideno1'];  $nombre2Emp = $datoUser['Ideno2'];  $apellido1 = $datoUser['Ideap1'];   $apellido2 = $datoUser['Ideap2'];
                            $fecNac = $datoUser['Idefnc'];      $generoEmp = $datoUser['Idegen'];   $docEmp = $datoUser['Ideced'];      $codNoEmp = $datoUser['Ideuse'];
                            $tiPasa = $datoUser['Idepas'];      if($tiPasa == 'on'){$tiPasa = 'SI';}else{$tiPasa = 'NO';}
                            $tiVisa = $datoUser['Idevis'];      if($tiVisa == 'on'){$tiVisa = 'SI';}else{$tiVisa = 'NO';}
                            $estCiv = $datoUser['Ideesc'];      if($estCiv == '01'){$estCiv = 'SOLTERO';}   if($estCiv == '02'){$estCiv = 'CASADO';}    if($estCiv == '03'){$estCiv = 'UNION LIBRE';}
                            if($estCiv == '04'){$estCiv = 'SEPARADO';}  if($estCiv == '05'){$estCiv = 'DIVORCIADO';}    if($estCiv == '06'){$estCiv = 'VIUDO';}
                            $estrat = $datoUser['Idestt'];      $lugarNac = $datoUser['Ideinc'];    $munReside = $datoUser['Idempo'];   $munResid = obtenerDescMuni($munReside,$conex);
                            $barRes = $datoUser['Idebrr'];      $barRes = obtenerDescBarr($munReside,$barRes,$conex,$desBarrio,$munRes);        $tipSangre = $datoUser['Idesrh'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY EDUCACION//////////////////////////////////////////-->
                        <section>
                            <?php
                            $q2 = "select * from talhuma_000014 WHERE Eduuse = '$codNoEmp' AND Eduest = 'on'";
                            if($c17 == 1){$par17 = "and $campo17 $operGrae '$graEsEmp'";}else{$par17 = '';}     if($c18 == 1){$par18 = "and $campo18 $operTiOb '$titObEmp'";} else{$par18 = '';}
                            if($c19 == 1){$par19 = "and $campo19 $operNoIn '$nomInsti'";}else{$par19 = '';}     if($c20 == 1){$par20 = "and $campo20 $operFeTi '$fecTit'";} else{$par20 = '';}

                            $queryEdu = "$q2 $par17 $par18 $par19 $par20";
                            $queryFinal2 = mysql_query($queryEdu, $conex) or die (mysql_errno()." - en el query: ".$queryEdu." - ".mysql_error());
                            $numEduca = mysql_num_rows($queryFinal2);       //SABER SI HAY REGISTROS
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY IDIOMAS////////////////////////////////////////////-->
                        <section>
                            <?php
                            $q3 = "select * from talhuma_000015 WHERE Idiuse = '$codNoEmp' AND Idiest = 'on'";
                            if($c21 == 1){$par21 = "and $campo21 $operIdio '$idiEmp'";}else{$par21 = '';}       if($c22 == 1){$par22 = "and $campo22 $operIdih '$idiHab'";} else{$par22 = '';}
                            if($c23 == 1){$par23 = "and $campo23 $operIdiL '$idiLee'";}else{$par23 = '';}       if($c24 == 1){$par24 = "and $campo24 $operIdiE '$idiEsc'";} else{$par24 = '';}

                            $queryIdioma = "$q3 $par21 $par22 $par23 $par24";
                            $queryFinal3 = mysql_query($queryIdioma, $conex) or die (mysql_errno()." - en el query: ".$queryIdioma." - ".mysql_error());
                            $numIdioma = mysql_num_rows($queryFinal3);       //SABER SI HAY REGISTROS
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY ESTUDIOS ACTUALES//////////////////////////////////-->
                        <section>
                            <?php
                            $q4 = "select * from talhuma_000016 where Nesuse = '$codNoEmp' AND Nesest = 'on'";
                            if($c25 == 1){$par25 = "and $campo25 $operEsAc '$estAct'";}else{$par25 = '';}       if($c26 == 1){$par26 = "and $campo26 $operEsIn '$estInst'";}else{$par26 = '';}

                            $queryEstuAc = "$q4 $par25 $par26";
                            $queryFinal4 = mysql_query($queryEstuAc, $conex) or die (mysql_errno()." - en el query: ".$queryEstuAc." - ".mysql_error());
                            $numEstuAct = mysql_num_rows($queryFinal4);       //SABER SI HAY REGISTROS
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY INFORMACION FAMILIAR///////////////////////////////-->
                        <section>
                            <?php
                            $q5 = "select * from talhuma_000019 where Famuse = '$codNoEmp' AND Famest = 'on'";
                            if($c27 == 1){$par27 = "and $campo27 like '$viveEmp'";}else{$par27 = '';}           if($c28 == 1){$par28 = "and $campo28 $operCabFami '$cabFami'";}else{$par28 = '';}
                            if($c29 == 1){$par29 = "and $campo29 $operNiCargo '$niCargo'";}else{$par29 = '';}   if($c30 == 1){$par30 = "and $campo30 $operAdCargo '$adCargo'";}else{$par30 = '';}

                            $queryInFam = "$q5 $par27 $par28 $par29 $par30";
                            $queryFinal5 = mysql_query($queryInFam, $conex) or die (mysql_errno()." - en el query: ".$queryInFam." - ".mysql_error());
                            $numDatoFam = mysql_num_rows($queryFinal5);       //SABER SI HAY REGISTROS

                            $q7 = "select * from talhuma_000019 where Famuse = '$codNoEmp' AND Famest = 'on'";
                            if($nuMasco == 'on'){$operMasNu = '<>'; $nuMasco = '';}
                            if($c37 == 1){$par37 = "and $campo37 $operDisNu '$nuDisca'";}else{$par37 = '';} if($c38 == 1){$par38 = "and $campo38 $operMasNu '$nuMasco'";}else{$par38 = '';}
                            $queryInFam2 = "$q7 $par37 $par38";
                            $queryFinal7 = mysql_query($queryInFam2, $conex) or die (mysql_errno()." - en el query: ".$queryInFam2." - ".mysql_error());
                            $numDatoFam2 = mysql_num_rows($queryFinal7);       //SABER SI HAY REGISTROS
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY NUCLEO FAMILIAR////////////////////////////////////-->
                        <section>
                            <?php
                            $q6 = "select * from talhuma_000021 WHERE Gruuse = '$codNoEmp' AND Gruest = 'on'";
                            if($c31 == 1){$par31 = "and $campo31 $operGenNu '$generoNu'";}else{$par31 = '';}    if($c32 == 1){$par32 = "and $campo32 $operParNu '$parNu'";}else{$par32 = '';}
                            if($c33 == 1){$par33 = "and $campo33 $operFecNu '$fnacNu";}else{$par33 = '';}       if($c34 == 1){$par34 = "and $campo34 $operNivNu '$niEdNu'";}else{$par34 = '';}
                            if($c35 == 1){$par35 = "and $campo35 $operOcuNu '$nucOcupa'";}else{$par35 = '';}    if($c36 == 1){$par36 = "and $campo36 $operVivNu '$nuVive'";}else{$par36 = '';}

                            $queryNuc = "$q6 $par31 $par32 $par33 $par34 $par35 $par36";
                            $queryFinal6 = mysql_query($queryNuc, $conex) or die (mysql_errno()." - en el query: ".$queryNuc." - ".mysql_error());
                            $numDatoNuc = mysql_num_rows($queryFinal6);       //SABER SI HAY REGISTROS
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY EPS - PLAN COMPLEMENTARIO//////////////////////////-->
                        <section>
                            <?php
                            $q8 = "select Ideeps,Idescs from talhuma_000013 WHERE Ideuse = '$codNoEmp'";
                            if($c39 == 1){$par39 = "and $campo39 $operEpsAct '$epsAct'";}else{$par39 = '';} if($c40 == 1){$par40 = "and $campo40 $opeEpsCom '$epsCom'";}else{$par40 = '';}
                            $queryEps = "$q8 $par39 $par40";
                            $queryFinal8 = mysql_query($queryEps, $conex) or die (mysql_errno()." - en el query: ".$queryEps." - ".mysql_error());
                            $numDatoEps = mysql_num_rows($queryFinal8);       //SABER SI HAY REGISTROS
                            $datoEps = mysql_fetch_assoc($queryFinal8);
                            $epsActual = $datoEps['Ideeps'];    $epsComple = $datoEps['Idescs'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY DATOS FAMILIARES ADICIONALES://////////////////////-->
                        <section>
                            <?php
                            $codMatrixU = explode('-',$codNoEmp);    $parteCod1 = $codMatrixU[1];    $parteCod2 = $codMatrixU[0];  $codMatrix = $parteCod1.$parteCod2;
                            $parteCod2 = '%'.$parteCod2.'%';

                            $q9 = "select Usugasto,Ususitua,Usuposfam,Usucuhij from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal9 = mysql_query($q9, $conex) or die (mysql_errno()." - en el query: ".$q9." - ".mysql_error());
                            $numDatoFamAd = mysql_num_rows($queryFinal9);       //SABER SI HAY REGISTROS
                            $datoFamAd = mysql_fetch_assoc($queryFinal9);
                            $usuGasto =  $datoFamAd['Usugasto'];    $usuGasto = explode(',',$usuGasto);
                            $param1 = $usuGasto[0];     $param2 = $usuGasto[1];     $param3 = $usuGasto[2];     $param4 = $usuGasto[3];     $param5 = $usuGasto[4];
                            $param6 = $usuGasto[5];     $param7 = $usuGasto[6];     $param8 = $usuGasto[7];     $param9 = $usuGasto[8];     $param10 = $usuGasto[9];
                            $param11 = $usuGasto[10];   $param12 = $usuGasto[11];   $param13 = $usuGasto[12];   $param14 = $usuGasto[13];   $param15 = $usuGasto[14];

                            $usuSitua = $datoFamAd['Ususitua'];     $usuSitua = explode(',',$usuSitua);
                            $param16 = $usuSitua[0];    $param17 = $usuSitua[1];    $param18 = $usuSitua[2];    $param19 = $usuSitua[3];    $param20 = $usuSitua[4];
                            $param21 = $usuSitua[5];    $param22 = $usuSitua[6];    $param23 = $usuSitua[7];    $param24 = $usuSitua[8];    $param25 = $usuSitua[9];
                            $param26 = $usuSitua[10];

                            $usuPosi = $datoFamAd['Usuposfam'];     $param27 = $usuPosi;

                            $usuCuHij = $datoFamAd['Usucuhij'];     $usuCuHij = explode(',',$usuCuHij);
                            $param28 = $usuCuHij[0];    $param29 = $usuCuHij[1];    $param30 = $usuCuHij[2];    $param31 = $usuCuHij[3];    $param32 = $usuCuHij[4];
                            $param33 = $usuCuHij[5];    $param34 = $usuCuHij[6];    $param35 = $usuCuHij[7];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY DATOS VIVIENDA Y SERVICIOS PUBLICOS:///////////////-->
                        <section>
                            <?php
                            $q10 = "select * from talhuma_000024 WHERE Cviuse = '$codNoEmp' AND Cviest = 'on'";
                            if($c76 == 1){$par76 = "and $campo76 $operVivTen '$vivTen'";}else{$par76 = '';} if($c77 == 1){$par77 = "and $campo77 $operVivTiv '$vivTiv'";}else{$par77 = '';}
                            if($c78 == 1){$par78 = "and $campo78 $operVivTte '$vivTte'";}else{$par78 = '';} if($c79 == 1){$par79 = "and $campo79 $operVivTlo '$vivTlo'";}else{$par79 = '';}
                            if($c80 == 1){$par80 = "and $campo80 $operVivEst '$vivEst'";}else{$par80 = '';}
                            $queryVivSer = "$q10 $par76 $par77 $par78 $par79 $par80";
                            $queryFinal10 = mysql_query($queryVivSer, $conex) or die (mysql_errno()." - en el query: ".$queryVivSer." - ".mysql_error());
                            $numDatoVivSer = mysql_num_rows($queryFinal10);       //SABER SI HAY REGISTROS

                            $q11 = "select Cvissp from talhuma_000024 WHERE Cviuse = '$codNoEmp' AND Cviest = 'on'";
                            $queryFinal11 = mysql_query($q11, $conex) or die (mysql_errno()." - en el query: ".$q11." - ".mysql_error());
                            $numDatoVivSer2 = mysql_num_rows($queryFinal11);       //SABER SI HAY REGISTROS
                            $datoViv2 = mysql_fetch_assoc($queryFinal11);
                            $usuServi2 = $datoViv2['Cvissp'];    $usuServi2 = explode(',',$usuServi2);
                            $paramS1 = $usuServi2[0];    $paramS2 = $usuServi2[1];    $paramS3 = $usuServi2[2];    $paramS4 = $usuServi2[3];    $paramS5 = $usuServi2[4];
                            $paramS6 = $usuServi2[5];    $paramS7 = $usuServi2[6];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY DATOS SUBSIDIO Y AHORRO VIVIENDA://////////////////-->
                        <section>
                            <?php
                            $q12 = "select Ususubvi,Usuahviv,Usumonaho from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            if($c88 == 1){$par88 = "and $campo88 $operAhvSub '$ahvSub'";}else{$par88 = '';} if($c89 == 1){$par89 = "and $campo89 $operAhvAho '$ahvAho'";}else{$par89 = '';}
                            if($c90 == 1){$par90 = "and $campo90 $operAhvCua '$ahvCua'";}else{$par90 = '';}
                            $queryAhoViv = "$q12 $par88 $par89 $par90";
                            $queryFinal12 = mysql_query($queryAhoViv, $conex) or die (mysql_errno()." - en el query: ".$queryAhoViv." - ".mysql_error());
                            $numDatoAhoViv = mysql_num_rows($queryFinal12);       //SABER SI HAY REGISTROS
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY DATOS RIESGOS EN LA VIVIENDA://////////////////////-->
                        <section>
                            <?php
                            $q13 = "select Usfariesvi from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal13 = mysql_query($q13, $conex) or die (mysql_errno()." - en el query: ".$q13." - ".mysql_error());
                            $numDatoVivRiesgo = mysql_num_rows($queryFinal13);       //SABER SI HAY REGISTROS
                            $datoVivRies = mysql_fetch_assoc($queryFinal13);
                            $usuRiesVi =  $datoVivRies['Usfariesvi'];    $usuRiesVi = explode(',',$usuRiesVi);
                            $param91 = $usuRiesVi[0];   $param92 = $usuRiesVi[1];   $param93 = $usuRiesVi[2];   $param94 = $usuRiesVi[3];
                            $param95 = $usuRiesVi[4];   $param96 = $usuRiesVi[5];   $param97 = $usuRiesVi[6];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY DATOS NECESIDADES DE MEJORA VIVIENDA:///////////////-->
                        <section>
                            <?php
                            $q14 = "select Usumejovi from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal14 = mysql_query($q14, $conex) or die (mysql_errno()." - en el query: ".$q14." - ".mysql_error());
                            $numDatoNemViv = mysql_num_rows($queryFinal14);       //SABER SI HAY REGISTROS
                            $datoNemViv = mysql_fetch_assoc($queryFinal14);
                            $usuNemViv = $datoNemViv['Usumejovi'];      $usuNemViv = explode(',',$usuNemViv);
                            $param98 = $usuNemViv[0];   $param99 = $usuNemViv[1];   $param100 = $usuNemViv[2];  $param101 = $usuNemViv[3];
                            $param102 = $usuNemViv[4];  $param103 = $usuNemViv[5];  $param104 = $usuNemViv[6];  $param105 = $usuNemViv[7];
                            $param106 = $usuNemViv[8];  $param107 = $usuNemViv[9];  $param108 = $usuNemViv[10]; $param109 = $usuNemViv[11];
                            $param110 = $usuNemViv[12];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY DATOS CREDITOS://///////////////////////////////////-->
                        <section>
                            <?php
                            $q15 = "select * from talhuma_000025 WHERE Creuse = '$codNoEmp' ";
                            $queryFinal15 = mysql_query($q15, $conex) or die (mysql_errno()." - en el query: ".$q15." - ".mysql_error());
                            $numDatoCred = mysql_num_rows($queryFinal15);       //SABER SI HAY REGISTROS
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY PRODUCTOS FINANCIEROS://////////////////////////////-->
                        <section>
                            <?php
                            $q16 = "select Usuprofi from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal16 = mysql_query($q16, $conex) or die (mysql_errno()." - en el query: ".$q16." - ".mysql_error());
                            $numDatoProFi = mysql_num_rows($queryFinal16);       //SABER SI HAY REGISTROS
                            $datoProFi = mysql_fetch_assoc($queryFinal16);
                            $usuProFi = $datoProFi['Usuprofi'];         $usuProFi = explode(',',$usuProFi);
                            $param112 = $usuProFi[0];   $param113 = $usuProFi[1];   $param114 = $usuProFi[2];   $param115 = $usuProFi[3];
                            $param116 = $usuProFi[4];   $param117 = $usuProFi[5];   $param118 = $usuProFi[6];   $param119 = $usuProFi[7];
                            $param120 = $usuProFi[8];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY MOTIVO CREDITOS:////////////////////////////////////-->
                        <section>
                            <?php
                            $q17 = "select Usumocre from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal17 = mysql_query($q17, $conex) or die (mysql_errno()." - en el query: ".$q17." - ".mysql_error());
                            $numDatoMoCre = mysql_num_rows($queryFinal17);       //SABER SI HAY REGISTROS
                            $datoMoCre = mysql_fetch_assoc($queryFinal17);
                            $usuMoCre = $datoMoCre['Usumocre'];         $usuMoCre = explode(',',$usuMoCre);
                            $param121 = $usuMoCre[0];   $param122 = $usuMoCre[1];   $param123 = $usuMoCre[2];   $param124 = $usuMoCre[3];
                            $param125 = $usuMoCre[4];   $param126 = $usuMoCre[5];   $param127 = $usuMoCre[6];   $param128 = $usuMoCre[7];
                            $param129 = $usuMoCre[8];   $param130 = $usuMoCre[9];   $param131 = $usuMoCre[10];  $param132 = $usuMoCre[11];
                            $param133 = $usuMoCre[12];  $param134 = $usuMoCre[13];  $param135 = $usuMoCre[14];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY ACUDE PARA CREDITOS:////////////////////////////////-->
                        <section>
                            <?php
                            $q18 = "select Usepacu from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal18 = mysql_query($q18, $conex) or die (mysql_errno()." - en el query: ".$q18." - ".mysql_error());
                            $numDatoEpaCre = mysql_num_rows($queryFinal18);       //SABER SI HAY REGISTROS
                            $datoEpaCre = mysql_fetch_assoc($queryFinal18);
                            $usuEpaCre = $datoEpaCre['Usepacu'];        $usuEpaCre = explode(',',$usuEpaCre);
                            $param136 = $usuEpaCre[0];  $param137 = $usuEpaCre[1];  $param138 = $usuEpaCre[2];  $param139 = $usuEpaCre[3];
                            $param140 = $usuEpaCre[4];  $param141 = $usuEpaCre[5];  $param142 = $usuEpaCre[6];  $param143 = $usuEpaCre[7];
                            $param144 = $usuEpaCre[8];  $param145 = $usuEpaCre[9];  $param146 = $usuEpaCre[10];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY LINEAS DE CREDITO DE INTERES:///////////////////////-->
                        <section>
                            <?php
                            $q19 = "select UsLcInt from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal19 = mysql_query($q19, $conex) or die (mysql_errno()." - en el query: ".$q19." - ".mysql_error());
                            $numDatoLiCreIn = mysql_num_rows($queryFinal19);       //SABER SI HAY REGISTROS
                            $datoLiCreIn = mysql_fetch_assoc($queryFinal19);
                            $usuLiCreIn = $datoLiCreIn['UsLcInt'];      $usuLiCreIn = explode(',',$usuLiCreIn);
                            $param147 = $usuLiCreIn[0]; $param148 = $usuLiCreIn[1]; $param149 = $usuLiCreIn[2];     $param150 = $usuLiCreIn[3];
                            $param151 = $usuLiCreIn[4]; $param152 = $usuLiCreIn[5]; $param153 = $usuLiCreIn[6];     $param154 = $usuLiCreIn[7];
                            $param155 = $usuLiCreIn[8]; $param156 = $usuLiCreIn[9]; $param157 = $usuLiCreIn[10];    $param158 = $usuLiCreIn[11];
                            $param159 = $usuLiCreIn[12];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY INSTITUCIONES AHORRA:///////////////////////////////-->
                        <section>
                            <?php
                            $q20 = "select UsIaAho from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal20 = mysql_query($q20, $conex) or die (mysql_errno()." - en el query: ".$q20." - ".mysql_error());
                            $numDatoInAho = mysql_num_rows($queryFinal20);       //SABER SI HAY REGISTROS
                            $datoInAho = mysql_fetch_assoc($queryFinal20);
                            $usuInAho = $datoInAho['UsIaAho'];      $usuInAho = explode(',',$usuInAho);
                            $param160 = $usuInAho[0];   $param161 = $usuInAho[1];   $param162 = $usuInAho[2];   $param163 = $usuInAho[3];
                            $param164 = $usuInAho[4];   $param165 = $usuInAho[5];   $param166 = $usuInAho[6];   $param167 = $usuInAho[7];
                            $param168 = $usuInAho[8];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY TRANSPORTE HABITUAL:///////////////////////////////-->
                        <section>
                            <?php
                            $q21 = "select Cvitra from talhuma_000024 WHERE Cviuse = '$codNoEmp' AND Cviest = 'on'";
                            $queryFinal21 = mysql_query($q21, $conex) or die (mysql_errno()." - en el query: ".$q21." - ".mysql_error());
                            $numDatoTrans = mysql_num_rows($queryFinal21);       //SABER SI HAY REGISTROS
                            $datoTrans = mysql_fetch_assoc($queryFinal21);
                            $usuTrans = $datoTrans['Cvitra'];       $usuTrans = explode(',',$usuTrans);
                            $param169 = $usuTrans[0];   $param170 = $usuTrans[1];   $param171 = $usuTrans[2];   $param172 = $usuTrans[3];
                            $param173 = $usuTrans[4];   $param174 = $usuTrans[5];   $param175 = $usuTrans[6];   $param176 = $usuTrans[7];
                            $param177 = $usuTrans[8];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY OTRO TRANSPORTE:///////////////////////////////-->
                        <section>
                            <?php
                            $q22 = "select Cviotr from talhuma_000024 WHERE Cviuse = '$codNoEmp' AND Cviest = 'on'";
                            if($c178 == 1){$par178 = "and $campo178 $operTrhOtc '$trhOtc'";}else{$par178 = '';}
                            $queryOtrt = "$q22 $par178";
                            $queryFinal22 = mysql_query($queryOtrt, $conex) or die (mysql_errno()." - en el query: ".$queryOtrt." - ".mysql_error());
                            $numDatoOtrt = mysql_num_rows($queryFinal22);       //SABER SI HAY REGISTROS
                            $datoOtrt = mysql_fetch_assoc($queryFinal22);
                            $param178 = $datoOtrt['Cviotr'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY LUGAR PARQUEA:///////////////////////////////-->
                        <section>
                            <?php
                            $q23 = "select Otrpar from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            if($c179 == 1){$par179 = "and $campo179 $operTrhPar '$trhPar'";}else{$par179 = '';}
                            $queryLugPar = "$q23 $par179";
                            $queryFinal23 =  mysql_query($queryLugPar, $conex) or die (mysql_errno()." - en el query: ".$queryLugPar." - ".mysql_error());
                            $numDatoLugPar = mysql_num_rows($queryFinal23);       //SABER SI HAY REGISTROS
                            $datoLugPar = mysql_fetch_assoc($queryFinal23);
                            $param179 = $datoLugPar['Otrpar'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY TIEMPO DESPLAZAMIENTO:////////////////////////////-->
                        <section>
                            <?php
                            $q24 = "select OtrTime from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            if($c180 == 1){$par180 = "and $campo180 $operTrhTid '$trhTid'";}else{$par180 = '';}
                            $queryTieDes = "$q24 $par180";
                            $queryFinal24 =  mysql_query($queryTieDes, $conex) or die (mysql_errno()." - en el query: ".$queryTieDes." - ".mysql_error());
                            $numDatoTieDes = mysql_num_rows($queryFinal24);       //SABER SI HAY REGISTROS
                            $datoTieDes = mysql_fetch_assoc($queryFinal24);
                            $param180 = $datoTieDes['OtrTime'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY TURNO HABITUAL:///////////////////////////////////-->
                        <section>
                            <?php
                            $q25 = "select OtrTurno from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            if($c181 == 1){$par181 = "and $campo181 $operTrhTur '$trhTur'";}else{$par181 = '';}
                            $queryTurno = "$q25 $par181";
                            $queryFinal25 =  mysql_query($queryTurno, $conex) or die (mysql_errno()." - en el query: ".$queryTurno." - ".mysql_error());
                            $numDatoTurno = mysql_num_rows($queryFinal25);       //SABER SI HAY REGISTROS
                            $datoTurno = mysql_fetch_assoc($queryFinal25);
                            $param181 = $datoTurno['OtrTurno'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY OTRA ACTIVIDAD LABORAL:////////////////////////////-->
                        <section>
                            <?php
                            $q26 = "select OtrExtra from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            if($c182 == 1){$par182 = "and $campo182 $operAclAte '$aclAte'";}else{$par182 = '';}
                            $queryActExt = "$q26 $par182";
                            $queryFinal26 =  mysql_query($queryActExt, $conex) or die (mysql_errno()." - en el query: ".$queryActExt." - ".mysql_error());
                            $numDatoActExt = mysql_num_rows($queryFinal26);       //SABER SI HAY REGISTROS
                            $datoActExt = mysql_fetch_assoc($queryFinal26);
                            $param182 = $datoActExt['OtrExtra'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY OTRA ACTIVIDAD CUAL:////////////////////////////-->
                        <section>
                            <?php
                            $q27 = "select OtrExOtra from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            if($c183 == 1){$par183 = "and $campo183 $operAclOta '$aclOta'";}else{$par183 = '';}
                            $queryActOtr = "$q27 $par183";
                            $queryFinal27 =  mysql_query($queryActOtr, $conex) or die (mysql_errno()." - en el query: ".$queryActOtr." - ".mysql_error());
                            $numDatoActOtr = mysql_num_rows($queryFinal27);       //SABER SI HAY REGISTROS
                            $datoActOtr = mysql_fetch_assoc($queryFinal27);
                            $param183 = $datoActOtr['OtrExOtra'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY RANGO SALARIAL:////////////////////////////-->
                        <section>
                            <?php
                            $q28 = "select OtrSalar from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            if($c184 == 1){$par184 = "and $campo184 $operAclRan '$aclRan'";}else{$par184 = '';}
                            $queryRanSal = "$q28 $par184";
                            $queryFinal28 =  mysql_query($queryRanSal, $conex) or die (mysql_errno()." - en el query: ".$queryRanSal." - ".mysql_error());
                            $numDatoRanSal = mysql_num_rows($queryFinal28);       //SABER SI HAY REGISTROS
                            $datoRanSal = mysql_fetch_assoc($queryFinal28);
                            $param184 = $datoRanSal['OtrSalar'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY NECESIDAD DE FORMACION://///////////////////////////-->
                        <section>
                            <?php
                            $q29 = "select UsNefor from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal29 = mysql_query($q29, $conex) or die (mysql_errno()." - en el query: ".$q29." - ".mysql_error());
                            $numDatoNeFor = mysql_num_rows($queryFinal29);       //SABER SI HAY REGISTROS
                            $datoNeFor = mysql_fetch_assoc($queryFinal29);
                            $usuNeFor = $datoNeFor['UsNefor'];       $usuNeFor = explode(',',$usuNeFor);
                            $param185 = $usuNeFor[0];   $param186 = $usuNeFor[1];   $param187 = $usuNeFor[2];   $param188 = $usuNeFor[3];
                            $param189 = $usuNeFor[4];   $param190 = $usuNeFor[5];   $param191 = $usuNeFor[6];   $param192 = $usuNeFor[7];
                            $param193 = $usuNeFor[8];   $param194 = $usuNeFor[9];   $param195 = $usuNeFor[10];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY ALIMENTACION - ALMUERZO://///////////////////////////-->
                        <section>
                            <?php
                            $q30 = "select Cvical from talhuma_000024 WHERE Cviuse = '$codNoEmp' AND Cviest = 'on'";
                            $queryFinal30 = mysql_query($q30, $conex) or die (mysql_errno()." - en el query: ".$q30." - ".mysql_error());
                            $numDatoAliAlm = mysql_num_rows($queryFinal30);       //SABER SI HAY REGISTROS
                            $datoAliAlm = mysql_fetch_assoc($queryFinal30);
                            $usuAliAlm = $datoAliAlm['Cvical'];       $usuAliAlm = explode(',',$usuAliAlm);
                            $param196 = $usuAliAlm[0];   $param197 = $usuAliAlm[1];   $param198 = $usuAliAlm[2];   $param199 = $usuAliAlm[3];
                            $param200 = $usuAliAlm[4];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY ACTIVIDADES PARTICIPARIA://///////////////////////////-->
                        <section>
                            <?php
                            $q31 = "select Otractrec from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            $queryFinal31 =  mysql_query($q31, $conex) or die (mysql_errno()." - en el query: ".$q31." - ".mysql_error());
                            $numDatoActPar = mysql_num_rows($queryFinal31);       //SABER SI HAY REGISTROS
                            $datoActPar = mysql_fetch_assoc($queryFinal31);
                            $usuActPar = $datoActPar['Otractrec'];    $usuActPar = explode(',',$usuActPar);
                            $param201 = $usuActPar[0];  $param202 = $usuActPar[1];  $param203 = $usuActPar[2];  $param204 = $usuActPar[3];
                            $param205 = $usuActPar[4];  $param206 = $usuActPar[5];  $param207 = $usuActPar[6];  $param208 = $usuActPar[7];
                            $param209 = $usuActPar[8];  $param210 = $usuActPar[9];  $param211 = $usuActPar[10]; $param212 = $usuActPar[11];
                            $param213 = $usuActPar[12]; $param214 = $usuActPar[13]; $param215 = $usuActPar[14]; $param216 = $usuActPar[15];
                            $param217 = $usuActPar[16]; $param218 = $usuActPar[17]; $param219 = $usuActPar[18]; $param220 = $usuActPar[19];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY DIA Y HORA PARTICIPACION ACTIVIDADES://///////////////-->
                        <section>
                            <?php
                            $q32 = "select Otractrechor,Otractcul from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            $queryFinal32 =  mysql_query($q32, $conex) or die (mysql_errno()." - en el query: ".$q32." - ".mysql_error());
                            $numDatoHodPar = mysql_num_rows($queryFinal32);       //SABER SI HAY REGISTROS
                            $datoHodPar = mysql_fetch_assoc($queryFinal32);
                            $param221 = $datoHodPar['Otractrechor'];   $param222 = $datoHodPar['Otractcul'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY QUE HACE EN SU TIEMPO LIBRE://///////////////-->
                        <section>
                            <?php
                            $q33 = "select UsHobie from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal33 =  mysql_query($q33, $conex) or die (mysql_errno()." - en el query: ".$q33." - ".mysql_error());
                            $numDatoHaTil = mysql_num_rows($queryFinal33);       //SABER SI HAY REGISTROS
                            $datoHaTil = mysql_fetch_assoc($queryFinal33);
                            $usuHaTil = $datoHaTil['UsHobie'];    $usuHaTil = explode(',',$usuHaTil);
                            $param223 = $usuHaTil[0];  $param224 = $usuHaTil[1];  $param225 = $usuHaTil[2];  $param226 = $usuHaTil[3];
                            $param227 = $usuHaTil[4];  $param228 = $usuHaTil[5];  $param229 = $usuHaTil[6];  $param230 = $usuHaTil[7];
                            $param231 = $usuHaTil[8];  $param232 = $usuHaTil[9];  $param233 = $usuHaTil[10]; $param234 = $usuHaTil[11];
                            $param235 = $usuHaTil[12]; $param236 = $usuHaTil[13]; $param237 = $usuHaTil[14]; $param238 = $usuHaTil[15];
                            $param239 = $usuHaTil[16]; $param240 = $usuHaTil[17]; $param241 = $usuHaTil[18]; $param242 = $usuHaTil[19];
                            $param243 = $usuHaTil[20]; $param244 = $usuHaTil[21]; $param245 = $usuHaTil[22]; $param246 = $usuHaTil[23];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY CON QUIEN PASA SU TIEMPO LIBRE://///////////////-->
                        <section>
                            <?php
                            $q34 = "select UsQpTie from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal34 =  mysql_query($q34, $conex) or die (mysql_errno()." - en el query: ".$q34." - ".mysql_error());
                            $numDatoQptLib = mysql_num_rows($queryFinal34);       //SABER SI HAY REGISTROS
                            $datoQptLib = mysql_fetch_assoc($queryFinal34);
                            $usuQptLib = $datoQptLib['UsQpTie'];    $usuQptLib = explode(',',$usuQptLib);
                            $param247 = $usuQptLib[0];  $param248 = $usuQptLib[1];  $param249 = $usuQptLib[2];  $param250 = $usuQptLib[3];
                            $param251 = $usuQptLib[4];  $param252 = $usuQptLib[5];  $param253 = $usuQptLib[6];  $param254 = $usuQptLib[7];
                            $param255 = $usuQptLib[8];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY QUE HACEN SUS HIJOS EN SU TIEMPO LIBRE://///////////////-->
                        <section>
                            <?php
                            $q35 = "select UsHhTli from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal35 =  mysql_query($q35, $conex) or die (mysql_errno()." - en el query: ".$q35." - ".mysql_error());
                            $numDatoHhTil = mysql_num_rows($queryFinal35);       //SABER SI HAY REGISTROS
                            $datoHhTil = mysql_fetch_assoc($queryFinal35);
                            $usuHhTil = $datoHhTil['UsHhTli'];    $usuHhTil = explode(',',$usuHhTil);
                            $param256 = $usuHhTil[0];  $param257 = $usuHhTil[1];  $param258 = $usuHhTil[2];  $param259 = $usuHhTil[3];
                            $param260 = $usuHhTil[4];  $param261 = $usuHhTil[5];  $param262 = $usuHhTil[6];  $param263 = $usuHhTil[7];
                            $param264 = $usuHhTil[8];  $param265 = $usuHhTil[9];  $param266 = $usuHhTil[10]; $param267 = $usuHhTil[11];
                            $param268 = $usuHhTil[12]; $param269 = $usuHhTil[13]; $param270 = $usuHhTil[14]; $param271 = $usuHhTil[15];
                            $param272 = $usuHhTil[16]; $param273 = $usuHhTil[17]; $param274 = $usuHhTil[18]; $param275 = $usuHhTil[19];
                            $param276 = $usuHhTil[20]; $param277 = $usuHhTil[21]; $param278 = $usuHhTil[22]; $param279 = $usuHhTil[23];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY BARRERAS SU TIEMPO LIBRE://///////////////-->
                        <section>
                            <?php
                            $q36 = "select UsBuTli from talento_000006 WHERE Usucod LIKE '$parteCod2'";
                            $queryFinal36 =  mysql_query($q36, $conex) or die (mysql_errno()." - en el query: ".$q36." - ".mysql_error());
                            $numDatoBarUtl = mysql_num_rows($queryFinal36);       //SABER SI HAY REGISTROS
                            $datoBarUtl = mysql_fetch_assoc($queryFinal36);
                            $usuBarUtl = $datoBarUtl['UsBuTli'];    $usuBarUtl = explode(',',$usuBarUtl);
                            $param280 = $usuBarUtl[0];  $param281 = $usuBarUtl[1];  $param282 = $usuBarUtl[2];  $param283 = $usuBarUtl[3];
                            $param284 = $usuBarUtl[4];  $param285 = $usuBarUtl[5];  $param286 = $usuBarUtl[6];
                            ?>
                        </section>
                        <!--///////////////////////////////////ROL EMPLEADO - OTRO CUAL://///////////////////////////-->
                        <section>
                            <?php
                            $q37 = "select * from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            if($c290 == 1){$par290 = "and $campo290 $operRotOtc '$rotOtc'";}
                            $queryRol = "$q37 $par290";
                            $queryFinal37 = mysql_query($queryRol, $conex) or die (mysql_errno()." - en el query: ".$queryRol." - ".mysql_error());
                            $numDatoRolCua = mysql_num_rows($queryFinal37);       //SABER SI HAY REGISTROS
                            $datoRolCua = mysql_fetch_assoc($queryFinal37);
                            $usuRolEmp = $datoRolCua['Otrrol'];       $usuRolEmp = explode(',',$usuRolEmp);
                            $param287 = $usuRolEmp[0];   $param288 = $usuRolEmp[2];   $param289 = $usuRolEmp[3];

                            $param290 = $datoRolCua['Otrroles'];
                            ?>
                        </section>
                        <!--///////////////////////////////////QUERY PERTENENCIA A COMITES://///////////////-->
                        <section>
                            <?php
                            $q38 = "select Otrcomite from talhuma_000060 WHERE Otruse = '$codNoEmp'";
                            $queryFinal38 =  mysql_query($q38, $conex) or die (mysql_errno()." - en el query: ".$q38." - ".mysql_error());
                            $numDatoPerCom = mysql_num_rows($queryFinal38);       //SABER SI HAY REGISTROS
                            $datoPerCom = mysql_fetch_assoc($queryFinal38);
                            $usuPerCom = $datoPerCom['Otrcomite'];    $usuPerCom = explode(',',$usuPerCom);
                            $param291 = $usuPerCom[0];  $param292 = $usuPerCom[1];  $param293 = $usuPerCom[2];  $param294 = $usuPerCom[3];
                            $param295 = $usuPerCom[4];  $param296 = $usuPerCom[5];  $param297 = $usuPerCom[6];  $param298 = $usuPerCom[7];
                            $param299 = $usuPerCom[8];  $param300 = $usuPerCom[9];  $param301 = $usuPerCom[10]; $param302 = $usuPerCom[11];
                            $param303 = $usuPerCom[12]; $param304 = $usuPerCom[13]; $param305 = $usuPerCom[14]; $param306 = $usuPerCom[15];
                            $param307 = $usuPerCom[16]; $param308 = $usuPerCom[17]; $param309 = $usuPerCom[18]; $param310 = $usuPerCom[19];
                            $param311 = $usuPerCom[20];
                            ?>
                        </section>
                        <?php
                        //////////////////////////////////////////////////////////////////////////////////////////////////
                        //////////////////////////////////// RESULTADOS: /////////////////////////////////////////////////
                        //////////////////////////////////////////////////////////////////////////////////////////////////
                        ?>
                        <tr class="alternar" id="<?php echo $row ?>" style="vertical-align: top"
                            title="<?php echo $nombre1Emp.' '.$apellido1.' '.$apellido2.' Codigo: '.$codNoEmp.' Documento: '.$docEmp.' ...'; ?>">
                            <td><?php echo $nombre1Emp ?></td>  <td><?php echo $nombre2Emp ?></td>  <td><?php echo $apellido1 ?></td>   <td><?php echo $apellido2 ?></td>
                            <td><?php echo $fecNac ?></td>      <td><?php echo $generoEmp ?></td>   <td><?php echo $docEmp ?></td>      <td><?php echo $codNoEmp ?></td>
                            <td><?php echo $tiPasa ?></td>      <td><?php echo $tiVisa ?></td>      <td><?php echo $estCiv ?></td>      <td><?php echo $estrat ?></td>
                            <td><?php echo $lugarNac ?></td>    <td><?php echo $munResid ?></td>    <td><?php echo $barRes ?></td>      <td><?php echo $tipSangre ?></td>
                            <!----------EDUCACION-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numEduca != 0)
                                {
                                    ?>
                                    <table class="tblRegistros">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>GRADO ESCOLARIDAD</label></td>
                                            <td><label>TITULO OBTENIDO</label></td>
                                            <td><label>NOMBRE INSTITUCION</label></td>
                                            <td><label>AÑO DEL TITULO</label></td>
                                        </tr>
                                        <?php
                                        while($datoEdu = mysql_fetch_assoc($queryFinal2))
                                        {
                                            $gradoEs = $datoEdu['Edugrd'];  $gradoEs = explode("-",$gradoEs);   $gradoEsc = $gradoEs[0];
                                            $titObten = $datoEdu['Edutit']; $nomInst = $datoEdu['Eduins'];      $fecTitulo = $datoEdu['Eduani'];

                                            if($gradoEsc == '01'){$gradoEsc = 'Primaria';}                      if($gradoEsc == '02'){$gradoEsc = 'Bachillerato Incompleto';}
                                            if($gradoEsc == '03'){$gradoEsc = 'Bachillerato Completo';}         if($gradoEsc == '04'){$gradoEsc = 'Tecnico';}
                                            if($gradoEsc == '05'){$gradoEsc = 'Tecnologo';}                     if($gradoEsc == '06'){$gradoEsc = 'Universitario';}
                                            if($gradoEsc == '07'){$gradoEsc = 'Posgrado o Especializacion';}    if($gradoEsc == '08'){$gradoEsc = 'Subespecializacion';}
                                            if($gradoEsc == '09'){$gradoEsc = 'Maestria';}                      if($gradoEsc == '10'){$gradoEsc = 'Doctorado';}
                                            ?>
                                            <tr align="center">
                                                <td><?php echo $gradoEsc ?></td>
                                                <td><?php echo $titObten ?></td>
                                                <td><?php echo $nomInst ?></td>
                                                <td><?php echo $fecTitulo ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                elseif($numEduca == 0)
                                {
                                    if($c17==1 || $c18==1 || $c19==1 || $c20==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else {echo 'Sin Dato'; }
                                }
                                ?>
                            </td>
                            <!----------IDIOMAS-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numIdioma != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="width: 300px">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>IDIOMA</label></td>
                                            <td><label>LO HABLA</label></td>
                                            <td><label>LO LEE</label></td>
                                            <td><label>LO ESCRIBE</label></td>
                                        </tr>
                                        <?php
                                        while($datoIdio = mysql_fetch_assoc($queryFinal3))
                                        {
                                            $idioma = $datoIdio['Idides'];  $idiomaHabla = $datoIdio['Idihab'];    $idiomaLee = $datoIdio['Idilee'];    $idiomaEscribe = $datoIdio['Idiesc'];
                                            if($idiomaHabla == 'on'){$idiomaHabla = 'SI';}      else{$idiomaHabla = 'NO';}
                                            if($idiomaLee == 'on'){$idiomaLee = 'SI';}          else{$idiomaLee = 'NO';}
                                            if($idiomaEscribe == 'on'){$idiomaEscribe = 'SI';}  else{$idiomaEscribe = 'NO';}
                                            ?>
                                            <tr align="center">
                                                <td><?php echo $idioma ?></td>
                                                <td><?php echo $idiomaHabla ?></td>
                                                <td><?php echo $idiomaLee ?></td>
                                                <td><?php echo $idiomaEscribe ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                elseif($numIdioma == 0)
                                {
                                if($c21==1 || $c22==1 || $c23==1 || $c24==1)
                                {
                                ?>
                                    <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                    <?php
                                }
                                else{ echo 'Sin Dato'; }
                                }
                                ?>
                            </td>
                            <!----------ESTUDIOS-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numEstuAct != 0)
                                {
                                    ?>
                                    <table class="tblRegistros">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>QUÉ ESTUDIA</label></td>
                                            <td><label>DURACION</label></td>
                                            <td><label>INSTITUCION</label></td>
                                            <td><label>NIVEL ACTUAL</label></td>
                                            <td><label>HORARIO</label></td>
                                        </tr>
                                        <?php
                                        while($datoEsAc = mysql_fetch_assoc($queryFinal4))
                                        {
                                            $estAcF = $datoEsAc['Nesdes'];      $estAcDura = $datoEsAc['Nesdur'];    $estAcIns = $datoEsAc['Nesins'];
                                            $estAcNiv = $datoEsAc['Nesniv'];    $estAcHora = $datoEsAc['Neshor'];
                                            ?>
                                            <tr align="center">
                                                <td><?php echo $estAcF ?></td>
                                                <td><?php echo $estAcDura ?></td>
                                                <td><?php echo $estAcIns ?></td>
                                                <td><?php echo $estAcNiv ?></td>
                                                <td><?php echo $estAcHora ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                elseif($numEstuAct == 0)
                                {
                                if($c25==1 || $c26==1)
                                {
                                ?>
                                    <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                    <?php
                                }
                                else{ echo 'Sin Dato'; }
                                }
                                ?>
                            </td>
                            <!----------CON QUIEN VIVE-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoFam != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>CON QUIEN VIVE</label></td>
                                            <td><label>ES CABEZA DE FAMILIA</label></td>
                                            <td><label>MENORES A CARGO</label></td>
                                            <td><label>ADULTOS A CARGO</label></td>
                                        </tr>
                                        <?php
                                        while($datoFami = mysql_fetch_assoc($queryFinal5))
                                        {
                                            $acompa = $datoFami['Famaco'];  $cabFamilia = $datoFami['Famcab']; $menorCargo = $datoFami['Fammac'];   $adultCargo = $datoFami['Famaac'];
                                            if($acompa == 01){$acompa = 'CON SU FAMILIA';}
                                            if($acompa == 02){$acompa = 'CON AMIGOS';}
                                            if($acompa == 03){$acompa = 'SOLO';}
                                            if($cabFamilia == 'on'){$cabFamilia = 'SI';}    else{$cabFamilia = 'NO';}
                                            ?>
                                            <tr align="center">
                                                <td><?php echo $acompa ?></td>
                                                <td><?php echo $cabFamilia ?></td>
                                                <td><?php echo $menorCargo ?></td>
                                                <td><?php echo $adultCargo ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                elseif($numDatoFam == 0)
                                {
                                    if($c27==1 || $c28==1 || $c29==1 || $c30==1)
                                    {
                                    ?>
                                    <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                    <?php
                                    }
                                    else{ echo 'Sin dato para INFORMACION FAMILIAR'.'<br>'; }
                                }
                                ?>
                            </td>
                            <!----------PERSONAS CON DISCAPACIDAD-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoFam2 != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>PERSONAS CON DISCAPACIDAD</label></td>
                                            <td><label>TIENE MASCOTA</label></td>
                                        </tr>
                                        <?php
                                        while($datoFami2 = mysql_fetch_assoc($queryFinal7))
                                        {
                                            $perDisca = $datoFami2['Famtpd'];  $tieMasco = $datoFami2['Famtms'];
                                            if($perDisca == 'on'){$perDisca = 'SI';}    else{$perDisca = 'NO';}
                                            if($tieMasco == '' || $tieMasco == 'off'){$tieMasco = 'NO TIENE';}
                                            ?>
                                            <tr align="center">
                                                <td><?php echo $perDisca ?></td>
                                                <td><?php echo $tieMasco ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                elseif($numDatoFam2 == 0)
                                {
                                    if($c37==1 || $c38==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else{ echo 'Sin dato'.'<br>'; }
                                }
                                ?>
                            </td>
                            <!----------NUCLEO FAMILIAR-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoNuc != 0)
                                {
                                    ?>
                                    <table class="tblRegistros">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>NOMBRE</label></td>
                                            <td><label>GENERO</label></td>
                                            <td><label>PARENTESCO<label></td>
                                            <td><label>FECHA NACIMIENTO</label></td>
                                            <td><label>NIVEL EDUCATIVO</label></td>
                                            <td><label>OCUPACION</label></td>
                                            <td><label>VIVE CON USTED</label></td>
                                        </tr>
                                        <?php
                                        while($datoNucleo = mysql_fetch_assoc($queryFinal6))
                                        {
                                            $nomNucleo = $datoNucleo['Grunom']; $apeNucleo = $datoNucleo['Gruape']; $genNucleo = $datoNucleo['Grugen']; $parNucleo = $datoNucleo['Grupar'];
                                            $fecNucleo = $datoNucleo['Grufna']; $nivNucleo = $datoNucleo['Gruesc']; $ocuNucleo = $datoNucleo['Gruocu']; $vivNucleo = $datoNucleo['Grucom'];
                                            if($parNucleo == '01'){$parNucleo = 'MADRE';}       if($parNucleo == '02'){$parNucleo = 'PADRE';}
                                            if($parNucleo == '03'){$parNucleo = 'HERMANO(A)';}  if($parNucleo == '010'){$parNucleo = 'SOBRINO(A)';}
                                            if($parNucleo == '05'){$parNucleo = 'TIO(A)';}      if($parNucleo == '06'){$parNucleo = 'ABUELO(A)';}
                                            if($parNucleo == '07'){$parNucleo = 'HIJO(A)';}     if($parNucleo == '08'){$parNucleo = 'CONYUGE';}
                                            if($parNucleo == '09'){$parNucleo = 'PRIMO(A))';}   if($parNucleo == '011'){$parNucleo = 'SUEGRO(A)';}
                                            if($parNucleo == '012'){$parNucleo = 'CUÑADO(A)';}  if($parNucleo == '013'){$parNucleo = 'YERNO';}
                                            if($parNucleo == '014'){$parNucleo = 'NUERA';}      if($parNucleo == '015'){$parNucleo = 'NIETO(A)';}

                                            if($nivNucleo == '01'){$nivNucleo = 'Primaria';}                    if($nivNucleo == '02'){$nivNucleo = 'Bachillerato Incompleto';}
                                            if($nivNucleo == '03'){$nivNucleo = 'Bachillerato Completo';}       if($nivNucleo == '04'){$nivNucleo = 'Tecnico';}
                                            if($nivNucleo == '05'){$nivNucleo = 'Tecnologo';}                   if($nivNucleo == '06'){$nivNucleo = 'Universitario';}
                                            if($nivNucleo == '07'){$nivNucleo = 'Posgrado o Especializacion';}  if($nivNucleo == '08'){$nivNucleo = 'Subespecializacion';}
                                            if($nivNucleo == '09'){$nivNucleo = 'Maestria';}                    if($nivNucleo == '10'){$nivNucleo = 'Doctorado';}

                                            if($ocuNucleo == '01'){$ocuNucleo = 'Empleado';}    if($ocuNucleo == '02'){$ocuNucleo = 'Estudiante';}
                                            if($ocuNucleo == '03'){$ocuNucleo = 'Ama de Casa';} if($ocuNucleo == '04'){$ocuNucleo = 'Desempleado';}
                                            if($ocuNucleo == '05'){$ocuNucleo =  'No Aplica';}  if($ocuNucleo == '06'){$ocuNucleo = 'Independiente';}

                                            if($vivNucleo == 'on'){$vivNucleo = 'SI';}          else{$vivNucleo = 'NO';}
                                            ?>
                                            <tr align="center">
                                                <td><?php echo $nomNucleo.' '.$apeNucleo ?></td>
                                                <td><?php echo $genNucleo ?></td>
                                                <td><?php echo $parNucleo ?></td>
                                                <td><?php echo $fecNucleo ?></td>
                                                <td><?php echo $nivNucleo ?></td>
                                                <td><?php echo $ocuNucleo ?></td>
                                                <td><?php echo $vivNucleo ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                elseif($numDatoNuc == 0)
                                {
                                    if($c31==1 || $c32==1 || $c33==1 || $c34==1 || $c35==1 || $c36==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else{ echo 'Sin dato para NUCLEO FAMILIAR'.'<br>'; }
                                }
                                ?>
                            </td>
                            <!----------EPS-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($epsActual != null || $epsComple != null)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td>EPS ACTUAL</td> <td>PLAN COMPLEMENTARIO</td>
                                        </tr>
                                        <?php
                                        $queryFinal88 = mysql_query($queryEps, $conex) or die (mysql_errno()." - en el query: ".$queryEps." - ".mysql_error());
                                        while($datoEps2 = mysql_fetch_assoc($queryFinal88))
                                        {
                                            $epsActual2 = $datoEps2['Ideeps'];    $epsComple2 = $datoEps2['Idescs'];
                                            $epsActual2 = obtenerEPS($epsActual2,$conex);
                                            ?>
                                            <tr align="center">
                                                <td><?php echo $epsActual2 ?></td> <td><?php echo $epsComple2 ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                elseif($epsActual == null || $epsComple == null)
                                {
                                    if($c39==1 || $c40==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para EPS ACTUAL / PLAN COMPLEMENTARIO';
                                    }
                                }
                                ?>
                            </td>
                            <!----------GASTOS FAMILIARES-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS FAMILIARES GASTOS:
                                if($numDatoFamAd != null)
                                {
                                    ?>
                                    <table class="tblRegistros3" style="margin-bottom: 15px">
                                        <?php
                                        if($c41==1 || $c42== 1 || $c43==1 || $c44==1 || $c45==1 || $c46==1 || $c47==1 || $c48==1 || $c49==1 || $c50==1 || $c51==1 || $c52==1
                                        || $c53==1 || $c54==1 || $c55==1)
                                        {
                                            if($gasViv == $param1 || $gasCuo == $param2 || $gasAli == $param3 || $gasSer == $param4 || $gasTra == $param5 || $gasEdp == $param6
                                            || $gasEdh == $param7 || $gasPac == $param8 || $gasTil == $param9 || $gasVes == $param10 || $gasSal == $param11 || $gasCel == $param12
                                            || $gasPtc == $param13 || $gasCte == $param14 || $gasBel == $param15)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Vivienda-Arriendo</td>      <td>Vivienda-Pago Cuotas</td>   <td>Alimentacion</td>
                                                    <td>Servicios Publicos</td>     <td>Transporte</td>             <td>Educacion Propia</td>
                                                    <td>Educación de los Hijos</td> <td>Pago de Crédito</td>        <td>Recreación - Tiempo Libre</td>
                                                    <td>Vestuario</td>              <td>Salud</td>                  <td>Pago de Celular</td>
                                                    <td>Pago Tarjetas Credito</td>  <td>Compra de tecnología</td>   <td>Cuidado Personal y Belleza</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param1 != null){convertValor($param1);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param2 != null){convertValor($param2);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param3 != null){convertValor($param3);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param4 != null){convertValor($param4);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param5 != null){convertValor($param5);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param6 != null){convertValor($param6);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param7 != null){convertValor($param7);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param8 != null){convertValor($param8);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param9 != null){convertValor($param9);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param10 != null){convertValor($param10);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param11 != null){convertValor($param11);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param12 != null){convertValor($param12);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param13 != null){convertValor($param13);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param14 != null){convertValor($param14);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param15 != null){convertValor($param15);}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($gasViv != $param1 || $gasCuo != $param2 || $gasAli != $param3 || $gasSer != $param4 || $gasTra != $param5 || $gasEdp != $param6
                                                || $gasEdh != $param7 || $gasPac != $param8 || $gasTil != $param9 || $gasVes != $param10 || $gasSal != $param11 || $gasCel != $param12
                                                || $gasPtc != $param13 || $gasCte != $param14 || $gasBel != $param15)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }

                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c41!=1 and $c42!= 1 and $c43!=1 and $c44!=1 and $c45!=1 and $c46!=1 and $c47!=1 and $c48!=1 and $c49!=1 and $c50!=1 and $c51!=1 and $c52!=1 and $c53!=1 and $c54!=1 and $c55!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Vivienda-Arriendo</td>      <td>Vivienda-Pago Cuotas</td>   <td>Alimentacion</td>
                                                <td>Servicios Publicos</td>     <td>Transporte</td>             <td>Educacion Propia</td>
                                                <td>Educación de los Hijos</td> <td>Pago de Crédito</td>        <td>Recreación - Tiempo Libre</td>
                                                <td>Vestuario</td>              <td>Salud</td>                  <td>Pago de Celular</td>
                                                <td>Pago Tarjetas Credito</td>  <td>Compra de tecnología</td>   <td>Cuidado Personal y Belleza</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param1 != null){convertValor($param1);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param2 != null){convertValor($param2);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param3 != null){convertValor($param3);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param4 != null){convertValor($param4);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param5 != null){convertValor($param5);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param6 != null){convertValor($param6);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param7 != null){convertValor($param7);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param8 != null){convertValor($param8);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param9 != null){convertValor($param9);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param10 != null){convertValor($param10);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param11 != null){convertValor($param11);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param12 != null){convertValor($param12);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param13 != null){convertValor($param13);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param14 != null){convertValor($param14);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param15 != null){convertValor($param15);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS FAMILIARES GASTOS:
                                elseif($numDatoFamAd == 0)
                                {
                                    if($c41==1 || $c42==1 || $c43==1 || $c44==1 || $c45==1 || $c46==1 || $c47==1 || $c48==1 || $c49==1 || $c50==1 || $c51==1 || $c52==1  || $c53==1 || $c54==1 || $c55==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para GASTOS FAMILIARES'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------SITUACIONES FAMILIARES-->
                            <td style="padding: 5px 10px">
                                <?php
                                ///////////////////////////////////////////////////////////////////////
                                //////////////////////////////////////////////////////////////////////
                                //SI SI TIENE DATOS FAMILIARES SITUACIONES
                                if($numDatoFamAd != 0)
                                {
                                    ?>
                                    <table class="tblRegistros3" style="margin-bottom: 15px">
                                        <?php
                                        if($c56==1 || $c57==1 || $c58==1 || $c59==1 || $c60==1 || $c61==1 || $c62==1 || $c63==1 || $c64==1 || $c65==1 || $c66==1)
                                        {
                                            if($sitDeu == $param16 || $sitPco == $param17 || $sitDec == $param18 || $sitDef == $param19 || $sitHch == $param20 || $sitSep == $param21
                                            || $sitVio == $param22 || $sitAdi == $param23 || $sitMsq == $param24 || $sitEng == $param25 || $sitNin == $param26)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Deudas que Superan Ingresos</td>  <td>Problemas de Conducta con sus Hijos</td>   <td>Dificultades Económicas</td>
                                                    <td>Desempleo de algun Miembro de su Familia</td>   <td>Hijos Adolescentes en Embarazo o con Hijos</td> <td>Separación - Divorcio</td>
                                                    <td>Violencia Intrafamiliar</td>  <td>Adicciones</td>   <td>Muerte de Seres Queridos</td>
                                                    <td>Enfermedad Grave de Algun Miembro de la Familia</td>  <td>Ninguno</td>   <td></td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param16 != null){convertValor($param16);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param17 != null){convertValor($param17);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param18 != null){convertValor($param18);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param19 != null){convertValor($param19);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param20 != null){convertValor($param20);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param21 != null){convertValor($param21);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param22 != null){convertValor($param22);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param23 != null){convertValor($param23);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param24 != null){convertValor($param24);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param25 != null){convertValor($param25);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param26 != null){convertValor($param26);}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($sitDeu != $param16 || $sitPco != $param17 || $sitDec != $param18 || $sitDef != $param19 || $sitHch != $param20 || $sitSep != $param21
                                                || $sitVio != $param22 || $sitAdi != $param23 || $sitMsq != $param24 || $sitEng != $param25 || $sitNin != $param26)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }

                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c56!=1 and $c57!=1 and $c58!=1 and $c59!=1 and $c60!=1 and $c61!=1 and $c62!=1 and $c63!=1 and $c64!=1 and $c65!=1 and $c66!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Deudas que Superan Ingresos</td>  <td>Problemas de Conducta con sus Hijos</td>   <td>Dificultades Económicas</td>
                                                <td>Desempleo de algun Miembro de su Familia</td>   <td>Hijos Adolescentes en Embarazo o con Hijos</td> <td>Separación - Divorcio</td>
                                                <td>Violencia Intrafamiliar</td>  <td>Adicciones</td>   <td>Muerte de Seres Queridos</td>
                                                <td>Enfermedad Grave de Algun Miembro de la Familia</td>  <td>Ninguno</td>   <td></td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param16 != null){convertValor($param16);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param17 != null){convertValor($param17);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param18 != null){convertValor($param18);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param19 != null){convertValor($param19);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param20 != null){convertValor($param20);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param21 != null){convertValor($param21);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param22 != null){convertValor($param22);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param23 != null){convertValor($param23);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param24 != null){convertValor($param24);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param25 != null){convertValor($param25);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param26 != null){convertValor($param26);}    else{echo 'Sin Dato';}  ?></td>
                                                <td></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS FAMILIARES SITUACIONES
                                elseif($numDatoFamAd == 0)
                                {
                                    if($c56==1 || $C57==1 || $c58==1 || $c59==1 || $C60==1 || $c61==1 || $c62==1 || $c63==1 || $c64==1 || $c65==1 || $c66==1)
                                    {
                                    ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para SITUACIONES FAMILIARES'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------POSICION GRUPO FAMILIAR-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS POSICION EN LA FAMILIA:
                                if($numDatoFamAd != 0)
                                {
                                    ?>
                                    <table class="tblRegistros2" style="margin-bottom: 15px">
                                        <?php
                                        if($c67==1)
                                        {
                                            if($posFam == $param27)
                                            {
                                                ?>
                                                <tr align="center">
                                                    <td align="center"><?php if($param27 != null){convertValor2($param27);}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php

                                            }
                                            //SI SI TIENE DATOS PERO ESTE NO COINCIDE CON LA BUSQUEDA:
                                            elseif($posFam != $param27)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c67!=1)
                                        {
                                            ?>
                                            <tr align="center">
                                                <td><?php if($param27 != null){convertValor2($param27);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS POSICION EN LA FAMILIA:
                                elseif($numDatoFamAd == 0)
                                {
                                    if($c67==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para POSICION EN LA FAMILIA'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------QUIEN CUIDA SUS HIJOS EN SU AUSENCIA-->
                            <td style="padding: 5px 10px">
                                <?php
                                //QUIEN QUEDA AL CUIDADO DE LOS HIJOS:
                                if($numDatoFamAd != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 15px">
                                        <?php
                                        if($c68==1 || $c69==1 || $c70==1 || $c71==1 || $c72==1 || $c73==1 || $c74==1 || $c75==1)
                                        {
                                            if($cuhAbu == $param28 || $cuhPma == $param29 || $cuhVec == $param30 || $cuhGui == $param31 || $cuhEmd == $param32 || $cuhFam == $param33
                                            || $cuhQso == $param34 || $cuhOtr == $param35)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Abuelos de los Niños</td>  <td>Padre o Madre de los Niños</td>   <td>Vecinos</td>
                                                    <td>Guardería o Instit. Educativa</td>  <td>Empleada Doméstica</td>   <td>Un Familiar</td>
                                                    <td>Se Queda Solo</td>  <td>Otro</td>   <td></td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param28 != null){convertValor($param28);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param29 != null){convertValor($param29);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param30 != null){convertValor($param30);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param31 != null){convertValor($param31);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param32 != null){convertValor($param32);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param33 != null){convertValor($param33);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param34 != null){convertValor($param34);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param35 != null){convertValor($param35);}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($cuhAbu != $param28 || $cuhPma != $param29 || $cuhVec != $param30 || $cuhGui != $param31 || $cuhEmd != $param32 || $cuhFam != $param33
                                                || $cuhQso != $param34 || $cuhOtr != $param35)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c68!=1 and $c69!=1 and $c70!=1 and $c71!=1 and $c72!=1 and $c73!=1 and $c74!=1 and $c75!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Abuelos de los Niños</td>           <td>Padre o Madre de los Niños</td> <td>Vecinos</td>
                                                <td>Guardería o Instit. Educativa</td>  <td>Empleada Doméstica</td>         <td>Un Familiar</td>
                                                <td>Se Queda Solo</td>                  <td>Otro</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param28 != null){convertValor($param28);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param29 != null){convertValor($param29);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param30 != null){convertValor($param30);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param31 != null){convertValor($param31);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param32 != null){convertValor($param32);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param33 != null){convertValor($param33);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param34 != null){convertValor($param34);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param35 != null){convertValor($param35);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS QUIEN QUEDA AL CUIDADO DE LOS HIJOS:
                                elseif($numDatoFamAd == 0)
                                {
                                    if($c68==1 || $c69==1 || $c70==1 || $c71==1 || $c72==1 || $c73==1 || $c74==1 || $c75==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para QUIEN QUEDA AL CUIDADO DE LOS HIJOS'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------VIVIENDA-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoVivSer != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>Tenencia de Vivienda</label></td>
                                            <td><label>Tipo de Vivienda</label></td>
                                            <td><label>Tiene Terraza Propia</label></td>
                                            <td><label>Tiene Lote Propio</label></td>
                                            <td><label>Estado de la Vivienda</label></td>
                                        </tr>
                                        <?php
                                        while($datoViv = mysql_fetch_assoc($queryFinal10))
                                        {
                                            $teVi = $datoViv['Cviviv']; $tiVi = $datoViv['Cvitvi']; $tiTe = $datoViv['Cvitrz']; $tiLo = $datoViv['Cvilot']; $esVi = $datoViv['Cvisvi'];
                                            if($tiTe == 'on'){$tiTe = 'SI';}    else{$tiTe = 'NO';} if($tiLo == 'on'){$tiLo = 'SI';}    else{$tiLo = 'NO';}
                                            ?>
                                            <tr align="center">
                                                <td><?php valTenViv($teVi,$conex); ?></td>
                                                <td><?php valTipViv($tiVi,$conex); ?></td>
                                                <td><?php echo $tiTe ?></td>
                                                <td><?php echo $tiLo ?></td>
                                                <td><?php valEstViv($esVi,$conex); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                elseif($numDatoVivSer == 0)
                                {
                                    if($c76==1 || $c77==1 || $c78==1 || $c79==1 || $c80==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin dato'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------ACCESO A SERVICIOS PUBLICOS-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoVivSer2 != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 15px">
                                        <?php
                                        if($c81==1 || $c82==1 || $c83==1 || $c84==1 || $c85==1 || $c86==1 || $c87==1)
                                        {
                                            if($serAcu == ''){$serAcu = 'off';} if($serAlc == ''){$serAlc = 'off';} if($serAse == ''){$serAse = 'off';}
                                            if($serEne == ''){$serEne = 'off';} if($serInt == ''){$serInt = 'off';} if($serRga == ''){$serRga = 'off';}
                                            if($serTel == ''){$serTel = 'off';}

                                            if($serAcu == $paramS1 || $serAlc == $paramS2 || $serAse == $paramS3 || $serEne == $paramS4 || $serInt == $paramS5 || $serRga == $paramS6 || $serTel == $paramS7)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Acueducto</td>  <td>Alcantarillado</td>   <td>Aseo</td> <td>Energía</td>  <td>Internet</td>   <td>Red de Gas</td>   <td>Teléfono</td>
                                                </tr>
                                                <tr>
                                                    <td><?php valServicio($paramS1);?></td>
                                                    <td><?php valServicio($paramS2); ?></td>
                                                    <td><?php valServicio($paramS3); ?></td>
                                                    <td><?php valServicio($paramS4); ?></td>
                                                    <td><?php valServicio($paramS5); ?></td>
                                                    <td><?php valServicio($paramS6); ?></td>
                                                    <td><?php valServicio($paramS7); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c81!=1 and $c82!=1 and $c83!=1 and $c84!=1 and $c85!=1 and $c86!=1 and $c87!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Acueducto</td>  <td>Alcantarillado</td>   <td>Aseo</td> <td>Energía</td>  <td>Internet</td>   <td>Red de Gas</td>   <td>Teléfono</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php valServicio($paramS1); ?></td>
                                                <td><?php valServicio($paramS2); ?></td>
                                                <td><?php valServicio($paramS3); ?></td>
                                                <td><?php valServicio($paramS4); ?></td>
                                                <td><?php valServicio($paramS5); ?></td>
                                                <td><?php valServicio($paramS6); ?></td>
                                                <td><?php valServicio($paramS7); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS ACCESO A SERVICIOS PUBLICOS:
                                elseif($numDatoVivSer2 == 0)
                                {
                                    if($c81==1 || $c82==1 || $c83==1 || $c84==1 || $c85==1 || $c86==1 || $c87==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para ACCESO A SERVICIOS PUBLICOS'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------SUBSIDIO / AHORRO VIVIENDA-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoAhoViv != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>Ha Sido Beneficiado Con Algún Tipo de Subsidio de Vivienda</label></td>
                                            <td><label>Ahorra Para la Compra de Una Vivienda Propia</label></td>
                                            <td><label>Cuánto Ahorro Tiene Disponible Para la Compra de Vivienda</label></td>
                                        </tr>
                                            <?php
                                            while($datoSubAh = mysql_fetch_assoc($queryFinal12))
                                            {
                                                $subsidio = $datoSubAh['Ususubvi'];   $ahorroVi = $datoSubAh['Usuahviv']; $montAho = $datoSubAh['Usumonaho'];
                                                ?>
                                                <tr align="center">
                                                    <td><?php echo $subsidio ?></td>
                                                    <td><?php echo $ahorroVi ?></td>
                                                    <td><?php echo $montAho ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                    </table>
                                    <?php
                                }
                                elseif($numDatoAhoViv == 0)
                                {
                                    if($c88==1 || $c89==1 || $c90==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin dato para SUBSIDIO / AHORRO VIVIENDA'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------RIESGOS EN LA VIVIENDA-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS RIESGOS:
                                if($numDatoVivRiesgo != null)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 15px">
                                        <?php
                                        if($c91==1 || $c92==1 || $c93==1 || $c94==1 || $c95==1 || $c96==1 || $c97==1)
                                        {
                                            if($farInu == $param91 || $farCon == $param92 || $farRia == $param93 || $farRie == $param94 || $farRis == $param95 || $farRip == $param96 || $farNot == $param97)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Inundaciones</td>       <td>Contaminación</td>  <td>Riesgos Ambientales</td>        <td>Riesgos estructurales</td>
                                                    <td>Riesgos Sanitarios</td> <td>Riesgo Público</td> <td>No Tiene Factores de Riesgo</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param91 != null){convertValor($param91);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param92 != null){convertValor($param92);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param93 != null){convertValor($param93);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param94 != null){convertValor($param94);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param95 != null){convertValor($param95);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param96 != null){convertValor($param96);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param97 != null){convertValor($param97);}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($farInu != $param91 || $farCon != $param92 || $farRia != $param93 || $farRie != $param94 || $farRis != $param95 || $farRip != $param96 || $farNot != $param97)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c91!=1 and $c92!=1 and $c93!=1 and $c94!=1 and $c95!=1 and $c96!=1 and $c97!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Inundaciones</td>       <td>Contaminación</td>  <td>Riesgos Ambientales</td>        <td>Riesgos estructurales</td>
                                                <td>Riesgos Sanitarios</td> <td>Riesgo Público</td> <td>No Tiene Factores de Riesgo</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param91 != null){convertValor($param91);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param92 != null){convertValor($param92);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param93 != null){convertValor($param93);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param94 != null){convertValor($param94);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param95 != null){convertValor($param95);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param96 != null){convertValor($param96);}    else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param97 != null){convertValor($param97);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS RIESGOS VIVIENDA:
                                elseif($numDatoVivRiesgo == 0)
                                {
                                    if($c91==1 || $c92==1 || $c93==1 || $c94==1 || $c95==1 || $c96==1 || $c97==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para RIESGOS DE LA VIVIENDA'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----NECESIDADES DE MEJORAMIENTO DE LA VIVIENDA-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS MEJORAMIENTO:
                                if($numDatoNemViv != null)
                                {
                                    ?>
                                    <table class="tblRegistros4" style="margin-bottom: 15px">
                                        <?php
                                        if($c98==1 || $c99==1 || $c100==1 || $c101==1 || $c102==1 || $c103==1 || $c104==1 || $c105==1 || $c106==1 || $c107==1 || $c108==1 || $c109==1 || $c110==1)
                                        {
                                            if($nemEst == $param98 || $nemMue == $param99 || $nemEle == $param100 || $nemPis == $param101 || $nemPar == $param102 || $nemCol == $param103
                                            || $nemHum == $param104 || $nemFac == $param105 || $nemTec == $param106 || $nemBan == $param107 || $nemCoc == $param108 || $nemAmp == $param109
                                            || $nemNot == $param110)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Estéticas</td>  <td>Muebles</td>    <td>Electrodomésticos</td>  <td>Piso</td>
                                                    <td>Paredes</td>    <td>Columnas</td>   <td>Humedades</td>          <td>Fachada</td>
                                                    <td>Techo</td>      <td>Baños</td>      <td>Cocina</td>             <td>Ampliación</td>
                                                    <td>No Tiene Necesidades</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param98 != null){convertValor($param98);}     else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param99 != null){convertValor($param99);}     else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param100 != null){convertValor($param100);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param101 != null){convertValor($param101);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param102 != null){convertValor($param102);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param103 != null){convertValor($param103);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param104 != null){convertValor($param104);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param105 != null){convertValor($param105);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param106 != null){convertValor($param106);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param107 != null){convertValor($param107);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param108 != null){convertValor($param108);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param109 != null){convertValor($param109);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param110 != null){convertValor($param110);}   else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($nemEst != $param98 || $nemMue != $param99 || $nemEle != $param100 || $nemPis != $param101 || $nemPar != $param102 || $nemCol != $param103
                                                || $nemHum != $param104 || $nemFac != $param105 || $nemTec != $param106 || $nemBan != $param107 || $nemCoc != $param108 || $nemAmp != $param109
                                                || $nemNot != $param110)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c98!=1 and $c99!=1 and $c100!=1 and $c101!=1 and $c102!=1 and $c103!=1 and $c104!=1 and $c105!=1 and $c106!=1 and $c107!=1 and $c108!=1 and $c109!=1 and $c110!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Estéticas</td>  <td>Muebles</td>    <td>Electrodomésticos</td>  <td>Piso</td>
                                                <td>Paredes</td>    <td>Columnas</td>   <td>Humedades</td>          <td>Fachada</td>
                                                <td>Techo</td>      <td>Baños</td>      <td>Cocina</td>             <td>Ampliación</td>
                                                <td>No Tiene Necesidades</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param98 != null){convertValor($param98);}     else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param99 != null){convertValor($param99);}     else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param100 != null){convertValor($param100);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param101 != null){convertValor($param101);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param102 != null){convertValor($param102);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param103 != null){convertValor($param103);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param104 != null){convertValor($param104);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param105 != null){convertValor($param105);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param106 != null){convertValor($param106);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param107 != null){convertValor($param107);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param108 != null){convertValor($param108);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param109 != null){convertValor($param109);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param110 != null){convertValor($param110);}   else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS MEJORAMIENTO:
                                elseif($numDatoNemViv == 0)
                                {
                                    if($c98==1 || $c99==1 || $c100==1 || $c101==1 || $c102==1 || $c103==1 || $c104==1 || $c105==1 || $c106==1 || $c107==1 || $c108==1 || $c109==1 || $c110==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para NECESIDADES DE MEJORAMIENTO DE VIVIENDA'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------INFORMACION CREDITOS-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS CREDITOS:
                                if($numDatoCred != 0)
                                {
                                    ?>
                                    <table class="tblRegistros">
                                        <tr align="center" style="background-color: #295DB1; color: white">
                                            <td><label>MOTIVO</label></td>
                                            <td><label>ENTIDAD</label></td>
                                            <td><label>VALOR TOTAL CREDITO<label></td>
                                            <td><label>CUOTA MENSUAL</label></td>
                                        </tr>
                                        <?php
                                        while($datoUsuCre = mysql_fetch_assoc($queryFinal15))
                                        {
                                            $motCred = $datoUsuCre['Cremot'];   $entCred = $datoUsuCre['Creent'];   $valCred = $datoUsuCre['Creval'];   $cuoCred = $datoUsuCre['Crecuo'];
                                            ?>
                                            <tr align="center">
                                                <td><?php if($motCred != null){echo $motCred; } else{echo 'SIN DATO';} ?></td>
                                                <td><?php if($entCred != null){echo $entCred; } else{echo 'SIN DATO';} ?></td>
                                                <td><?php if($valCred != null){echo $valCred; } else{echo 'SIN DATO';} ?></td>
                                                <td><?php if($cuoCred != null){echo $cuoCred; } else{echo 'SIN DATO';} ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS MEJORAMIENTO:
                                elseif($numDatoCred == 0)
                                {
                                    if($creAct == 'on')
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin dato para INFORMACION CREDITOS'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------INFORMACION PRODUCTOS FINANCIEROS-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS PRODUCTOS FINANCIEROS
                                if($numDatoProFi != null)
                                {
                                    ?>
                                    <table class="tblRegistros4" style="margin-bottom: 15px">
                                        <?php
                                        if($c112==1 || $c113==1 || $c114==1 || $c115==1 || $c116==1 || $c117==1 || $c118==1 || $c119==1 || $c120==1)
                                        {
                                            if($prfCan == $param112 || $prfCuc == $param113 || $prfTac == $param114 || $prfCrc == $param115 || $prfCrh == $param116
                                            || $prfCrv == $param117 || $prfInv == $param118 || $prfSeg == $param119 || $prfNin == $param120)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Cuenta de Ahorros - Nómina</td>         <td>Cuenta Corriente</td>                   <td>Tarjeta de Crédito</td> <td>Crédito de Consumo/Libre Inversión</td>
                                                    <td>Crédito Hipotecario de Vivienda</td>    <td>Crédito de Vehículo/Carro o Moto</td>   <td>Inversiones</td>        <td>Seguros</td>
                                                    <td>Ninguno</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param112 != null){convertValor($param112);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param113 != null){convertValor($param113);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param114 != null){convertValor($param114);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param115 != null){convertValor($param115);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param116 != null){convertValor($param116);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param117 != null){convertValor($param117);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param118 != null){convertValor($param118);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param119 != null){convertValor($param119);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param120 != null){convertValor($param120);}   else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($prfCan != $param112 || $prfCuc != $param113 || $prfTac != $param114 || $prfCrc != $param115 || $prfCrh != $param116
                                                || $prfCrv != $param117 || $prfInv != $param118 || $prfSeg != $param119 || $prfNin != $param120)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c112!=1 and $c113!=1 and $c114!=1 and $c115!=1 and $c116!=1 and $c117!=1 and $c118!=1 and $c119!=1 and $c120!=1)
                                        {
                                                ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Cuenta de Ahorros - Nómina</td>         <td>Cuenta Corriente</td>                   <td>Tarjeta de Crédito</td> <td>Crédito de Consumo/Libre Inversión</td>
                                                <td>Crédito Hipotecario de Vivienda</td>    <td>Crédito de Vehículo/Carro o Moto</td>   <td>Inversiones</td>        <td>Seguros</td>
                                                <td>Ninguno</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param112 != null){convertValor($param112);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param113 != null){convertValor($param113);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param114 != null){convertValor($param114);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param115 != null){convertValor($param115);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param116 != null){convertValor($param116);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param117 != null){convertValor($param117);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param118 != null){convertValor($param118);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param119 != null){convertValor($param119);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param120 != null){convertValor($param120);}   else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS MEJORAMIENTO:
                                elseif($numDatoProFi == 0)
                                {
                                    if($c112==1 || $c113==1 || $c114==1 || $c115==1 || $c116==1 || $c117==1 || $c118==1 || $c119==1 || $c120==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para PRODUCTOS FINANCIEROS'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------INFORMACION MOTIVO DE CREDITOS-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS MOTIVO CREDITOS
                                if($numDatoMoCre != null)
                                {
                                    ?>
                                    <table class="tblRegistros3" style="margin-bottom: 15px">
                                        <?php
                                        if($c121==1 || $c122==1 || $c123==1 || $c124==1 || $c125==1 || $c126==1 || $c127==1 || $c128==1 || $c129==1 || $c130==1 || $c131==1
                                        || $c132==1 || $c133==1 || $c134==1 || $c135==1)
                                        {
                                            if($mocViv==$param121 || $mocTec==$param122 || $mocMue==$param123 || $mocEle==$param124 || $mocVeh==$param125 || $mocSal==$param126
                                            || $mocCir==$param127 || $mocTur==$param128 || $mocLib==$param129 || $mocGas==$param130 || $mocTac==$param131 || $mocEdp==$param132
                                            || $mocEdf==$param133 || $mocCre==$param134 || $mocNin==$param135)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Vivienda</td>           <td>Tecnología</td>             <td>Muebles</td>            <td>Electrodomésticos</td>
                                                    <td>Vehículo</td>           <td>Salud</td>                  <td>Cirugías Estéticas</td> <td>Turismo</td>
                                                    <td>Libre Inversión</td>    <td>Gastos del Hogar</td>       <td>Tarjeta de Crédito</td> <td>Educación Propia</td>
                                                    <td>Educación Familiar</td> <td>Créditos Empresariales</td> <td>Ninguno</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param121 != null){convertValor($param121);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param122 != null){convertValor($param122);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param123 != null){convertValor($param123);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param124 != null){convertValor($param124);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param125 != null){convertValor($param125);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param126 != null){convertValor($param126);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param127 != null){convertValor($param127);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param128 != null){convertValor($param128);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param129 != null){convertValor($param129);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param130 != null){convertValor($param130);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param131 != null){convertValor($param131);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param132 != null){convertValor($param132);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param133 != null){convertValor($param133);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param134 != null){convertValor($param134);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param135 != null){convertValor($param135);}   else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($mocViv!=$param121 || $mocTec!=$param122 || $mocMue!=$param123 || $mocEle!=$param124 || $mocVeh!=$param125 || $mocSal!=$param126
                                                || $mocCir!=$param127 || $mocTur!=$param128 || $mocLib!=$param129 || $mocGas!=$param130 || $mocTac!=$param131 || $mocEdp!=$param132
                                                || $mocEdf!=$param133 || $mocCre!=$param134 || $mocNin!=$param135)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c121!=1 and $c122!=1 and $c123!=1 and $c124!=1 and $c125!=1 and $c126!=1 and $c127!=1 and $c128!=1 and $c129!=1 and $c130!=1 and $c131!=1
                                            || $c132!=1 and $c133!=1 and $c134!=1 and $c135!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Vivienda</td>           <td>Tecnología</td>             <td>Muebles</td>            <td>Electrodomésticos</td>
                                                <td>Vehículo</td>           <td>Salud</td>                  <td>Cirugías Estéticas</td> <td>Turismo</td>
                                                <td>Libre Inversión</td>    <td>Gastos del Hogar</td>       <td>Tarjeta de Crédito</td> <td>Educación Propia</td>
                                                <td>Educación Familiar</td> <td>Créditos Empresariales</td> <td>Ninguno</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param121 != null){convertValor($param121);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param122 != null){convertValor($param122);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param123 != null){convertValor($param123);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param124 != null){convertValor($param124);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param125 != null){convertValor($param125);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param126 != null){convertValor($param126);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param127 != null){convertValor($param127);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param128 != null){convertValor($param128);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param129 != null){convertValor($param129);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param130 != null){convertValor($param130);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param131 != null){convertValor($param131);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param132 != null){convertValor($param132);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param133 != null){convertValor($param133);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param134 != null){convertValor($param134);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param135 != null){convertValor($param135);}   else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS MOTIVO CREDITOS:
                                elseif($numDatoMoCre == 0)
                                {
                                    if($c121==1 || $c122==1 || $c123==1 || $c124==1 || $c125==1 || $c126==1 || $c127==1 || $c128==1 || $c129==1 || $c130==1 || $c131==1
                                        || $c132==1 || $c133==1 || $c134==1 || $c135==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para MOTIVO DE CREDITOS ACTUALES'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------A QUIEN ACUDE PARA CREDITOS-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS ACUDE PARA CREDITOS
                                if($numDatoEpaCre != null)
                                {
                                    ?>
                                    <table class="tblRegistros4" style="margin-bottom: 15px">
                                        <?php
                                        if($c136==1 || $c137==1 || $c138==1 || $c139==1 || $c140==1 || $c141==1 || $c142==1 || $c143==1 || $c144==1 || $c145==1 || $c146==1)
                                        {
                                            if($eacBac==$param136 || $eacFoe==$param137 || $aecFom==$param138 || $eacPgg==$param139 || $eacFam==$param140 || $eacCra==$param141
                                            || $eacCac==$param142 ||$eacEml==$param143 || $eacNat==$param144 || $eacOtr==$param145 || $eacNin==$param146)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Bancos Cooperativas de Ahorro y Crédito</td>    <td>Fondos de Empleados</td>        <td>Fondo Mutuo</td>
                                                    <td>Paga Diario o Gota a Gota</td>                  <td>Familiares o Amigos</td>        <td>Créditos en Almacenes</td>
                                                    <td>Cajas de Compensación</td>                      <td>Empresa en la que Labora</td>   <td>Natillera con Amigos o Familia</td>
                                                    <td>Otro</td>                                       <td>Ninguno</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param136 != null){convertValor($param136);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param137 != null){convertValor($param137);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param138 != null){convertValor($param138);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param139 != null){convertValor($param139);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param140 != null){convertValor($param140);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param141 != null){convertValor($param141);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param142 != null){convertValor($param142);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param143 != null){convertValor($param143);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param144 != null){convertValor($param144);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param145 != null){convertValor($param145);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param146 != null){convertValor($param146);}   else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($eacBac!=$param136 || $eacFoe!=$param137 || $aecFom!=$param138 || $eacPgg!=$param139 || $eacFam!=$param140 || $eacCra!=$param141
                                                || $eacCac!=$param142 ||$eacEml!=$param143 || $eacNat!=$param144 || $eacOtr!=$param145 || $eacNin!=$param146)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c136!=1 and $c137!=1 and $c138!=1 and $c139!=1 and $c140!=1 and $c141!=1 and $c142!=1 and $c143!=1 and $c144!=1 and $c145!=1 and $c146!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Bancos Cooperativas de Ahorro y Crédito</td>    <td>Fondos de Empleados</td>        <td>Fondo Mutuo</td>
                                                <td>Paga Diario o Gota a Gota</td>                  <td>Familiares o Amigos</td>        <td>Créditos en Almacenes</td>
                                                <td>Cajas de Compensación</td>                      <td>Empresa en la que Labora</td>   <td>Natillera con Amigos o Familia</td>
                                                <td>Otro</td>                                       <td>Ninguno</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param136 != null){convertValor($param136);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param137 != null){convertValor($param137);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param138 != null){convertValor($param138);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param139 != null){convertValor($param139);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param140 != null){convertValor($param140);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param141 != null){convertValor($param141);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param142 != null){convertValor($param142);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param143 != null){convertValor($param143);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param144 != null){convertValor($param144);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param145 != null){convertValor($param145);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param146 != null){convertValor($param146);}   else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS ACUDE PARA CREDITOS:
                                elseif($numDatoEpaCre == 0)
                                {
                                    if($c136==1 || $c137==1 || $c138==1 || $c139==1 || $c140==1 || $c141==1 || $c142==1 || $c143==1 || $c144==1 || $c145==1 || $c146==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para A QUIEN ACUDE PARA OBTENER CREDITOS'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------LINEAS DE CREDITO DE INTERÉS-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS LINEAS DE CREDITO
                                if($numDatoLiCreIn != null)
                                {
                                    ?>
                                    <table class="tblRegistros3" style="margin-bottom: 15px">
                                        <?php
                                        if($c147==1 || $c148==1 || $c149==1 || $c150==1 || $c151==1 || $c152==1 || $c153==1 || $c154==1 || $c155==1 || $c156==1 || $c157==1 || $c158==1 || $c159==1)
                                        {
                                            if($lciViv==$param147 || $lciVeh==$param148 || $lciSal==$param149 || $lciCie==$param150 || $lciTur==$param151 || $lciEdf==$param152 || $lciEdp==$param153
                                            || $lciCem==$param154 || $lciMev==$param155 || $lciCrr==$param156 || $lciLib==$param157 || $lciTac==$param158 || $lciNin==$param159)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Vivienda</td>           <td>Vehículo/ Carro/ Moto</td>                          <td>Salud</td>
                                                    <td>Cirugías Estéticas</td> <td>Turismo</td>                                        <td>Educación de la Familia</td>
                                                    <td>Educación Propia</td>   <td>Créditos Empresariales o para Emprendimientos</td>  <td>Mejoramiento de Vivienda</td>
                                                    <td>Crédito Rotativo</td>   <td>Libre Inversión</td>                                <td>Tarjeta de Crédito</td>
                                                    <td>Ninguna</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param147 != null){convertValor($param147);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param148 != null){convertValor($param148);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param149 != null){convertValor($param149);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param150 != null){convertValor($param150);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param151 != null){convertValor($param151);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param152 != null){convertValor($param152);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param153 != null){convertValor($param153);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param154 != null){convertValor($param154);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param155 != null){convertValor($param155);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param156 != null){convertValor($param156);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param157 != null){convertValor($param157);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param158 != null){convertValor($param158);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param159 != null){convertValor($param159);}   else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($lciViv!=$param147 || $lciVeh!=$param148 || $lciSal!=$param149 || $lciCie!=$param150 || $lciTur!=$param151 || $lciEdf!=$param152 || $lciEdp!=$param153
                                                || $lciCem!=$param154 || $lciMev!=$param155 || $lciCrr!=$param156 || $lciLib!=$param157 || $lciTac!=$param158 || $lciNin!=$param159)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c147!=1 and $c148!=1 and $c149!=1 and $c150!=1 and $c151!=1 and $c152!=1 and $c153!=1 and $c154!=1 and $c155!=1 and $c156!=1 and $c157!=1 and $c158!=1 and $c159!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Vivienda</td>           <td>Vehículo/ Carro/ Moto</td>                          <td>Salud</td>
                                                <td>Cirugías Estéticas</td> <td>Turismo</td>                                        <td>Educación de la Familia</td>
                                                <td>Educación Propia</td>   <td>Créditos Empresariales o para Emprendimientos</td>  <td>Mejoramiento de Vivienda</td>
                                                <td>Crédito Rotativo</td>   <td>Libre Inversión</td>                                <td>Tarjeta de Crédito</td>
                                                <td>Ninguna</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param147 != null){convertValor($param147);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param148 != null){convertValor($param148);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param149 != null){convertValor($param149);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param150 != null){convertValor($param150);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param151 != null){convertValor($param151);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param152 != null){convertValor($param152);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param153 != null){convertValor($param153);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param154 != null){convertValor($param154);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param155 != null){convertValor($param155);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param156 != null){convertValor($param156);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param157 != null){convertValor($param157);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param158 != null){convertValor($param158);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param159 != null){convertValor($param159);}   else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS LINEAS DE CREDITO
                                elseif($numDatoLiCreIn == 0)
                                {
                                    if($c147==1 || $c148==1 || $c149==1 || $c150==1 || $c151==1 || $c152==1 || $c153==1 || $c154==1 || $c155==1 || $c156==1 || $c157==1 || $c158==1 || $c159==1)
                                    {
                                        ?>
                                        <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para LINEAS DE CREDITO DE INTERES'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------A TRAVES DE QUE INST. AHORRA-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS INSTIT. AHORRA
                                if($numDatoInAho != null)
                                {
                                    ?>
                                    <table class="tblRegistros4" style="margin-bottom: 15px">
                                        <?php
                                        if($c160==1 || $c161==1 || $c162==1 || $c163==1 || $c164==1 || $c165==1 || $c166==1 || $c167==1 || $c168==1)
                                        {
                                            if($inaInv==$param160 || $inaBan==$param161 || $inaNat==$param162 || $inaCoo==$param163 || $inaFoe==$param164 || $inaFom==$param165
                                            || $inaFvo==$param166 || $inaOtr==$param167 || $inaNoa==$param168)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Inversiones</td>                        <td>Bancos</td>             <td>Natilleras</td>
                                                    <td>Cooperativas de Ahorro y Crédito</td>   <td>Fondo de Empleados</td> <td>Fondo Mutuo</td>
                                                    <td>Fondo Voluntario de Pensiones</td>      <td>Otro</td>               <td>No Ahorra</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param160 != null){convertValor($param160);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param161 != null){convertValor($param161);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param162 != null){convertValor($param162);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param163 != null){convertValor($param163);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param164 != null){convertValor($param164);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param165 != null){convertValor($param165);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param166 != null){convertValor($param166);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param167 != null){convertValor($param167);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param168 != null){convertValor($param168);}   else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($inaInv!=$param160 || $inaBan!=$param161 || $inaNat!=$param162 || $inaCoo!=$param163 || $inaFoe!=$param164 || $inaFom!=$param165
                                                || $inaFvo!=$param166 || $inaOtr!=$param167 || $inaNoa!=$param168)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c160!=1 and $c161!=1 and $c162!=1 and $c163!=1 and $c164!=1 and $c165!=1 and $c166!=1 and $c167!=1 and $c168!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Inversiones</td>                        <td>Bancos</td>             <td>Natilleras</td>
                                                <td>Cooperativas de Ahorro y Crédito</td>   <td>Fondo de Empleados</td> <td>Fondo Mutuo</td>
                                                <td>Fondo Voluntario de Pensiones</td>      <td>Otro</td>               <td>No Ahorra</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param160 != null){convertValor($param160);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param161 != null){convertValor($param161);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param162 != null){convertValor($param162);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param163 != null){convertValor($param163);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param164 != null){convertValor($param164);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param165 != null){convertValor($param165);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param166 != null){convertValor($param166);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param167 != null){convertValor($param167);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param168 != null){convertValor($param168);}   else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI SI TIENE DATOS INSTIT. AHORRA
                                elseif($numDatoInAho == 0)
                                {
                                    if($c160==1 || $c161==1 || $c162==1 || $c163==1 || $c164==1 || $c165==1 || $c166==1 || $c167==1 || $c168==1)
                                    {
                                        ?>
                                        <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para A TRAVES DE QUE INSTITUCIONES AHORRA'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------TRANSPORTE HABITUAL-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoTrans != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 15px">
                                        <?php
                                        if($c169==1 || $c170==1 || $c171==1 || $c172==1 || $c173==1 || $c174==1 || $c175==1 || $c176==1 || $c177==1)
                                        {
                                            if($trhBic == ''){$trhBic = 'off';} if($trhBus == ''){$trhBus = 'off';} if($trhCam == ''){$trhCam = 'off';} if($trhCap == ''){$trhCap = 'off';}
                                            if($trhMet == ''){$trhMet = 'off';} if($trhMot == ''){$trhMot = 'off';} if($trhOtr == ''){$trhOtr = 'off';} if($trhTax == ''){$trhTax = 'off';}
                                            if($trhTrc == ''){$trhTrc = 'off';}

                                            if($trhBic == $param169 || $trhBus == $param170 || $trhCam == $param171 || $trhCap == $param172 || $trhMet == $param173 || $trhMot == $param174
                                            || $trhOtr == $param175 || $trhTax == $param176 || $trhTrc == $param177)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Bicicleta</td>  <td>Bus</td>                <td>Caminando</td>              <td>Carro Particular</td>       <td>Metro</td>
                                                    <td>Moto</td>       <td>Otro</td>               <td>Taxi</td>                   <td>Transporte Contratado</td>
                                                </tr>
                                                <tr>
                                                    <td><?php valServicio($param169);?></td>
                                                    <td><?php valServicio($param170); ?></td>
                                                    <td><?php valServicio($param171); ?></td>
                                                    <td><?php valServicio($param172); ?></td>
                                                    <td><?php valServicio($param173); ?></td>
                                                    <td><?php valServicio($param174); ?></td>
                                                    <td><?php valServicio($param175); ?></td>
                                                    <td><?php valServicio($param176); ?></td>
                                                    <td><?php valServicio($param177); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c169!=1 and $c170!=1 and $c171!=1 and $c172!=1 and $c173!=1 and $c174!=1 and $c175!=1 and $c176!=1 and $c177!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Bicicleta</td>  <td>Bus</td>                <td>Caminando</td>              <td>Carro Particular</td>       <td>Metro</td>
                                                <td>Moto</td>       <td>Otro</td>               <td>Taxi</td>                   <td>Transporte Contratado</td>
                                            </tr>
                                            <tr>
                                                <td><?php valServicio($param169);?></td>
                                                <td><?php valServicio($param170); ?></td>
                                                <td><?php valServicio($param171); ?></td>
                                                <td><?php valServicio($param172); ?></td>
                                                <td><?php valServicio($param173); ?></td>
                                                <td><?php valServicio($param174); ?></td>
                                                <td><?php valServicio($param175); ?></td>
                                                <td><?php valServicio($param176); ?></td>
                                                <td><?php valServicio($param177); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS TRANSPORTE HABITUAL:
                                elseif($numDatoTrans == 0)
                                {
                                    if($c169==1 || $c170==1 || $c171==1 || $c172==1 || $c173==1 || $c174==1 || $c175==1 || $c176==1 || $c177==1)
                                    {
                                        ?>
                                        <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para TRANSPORTE HABITUAL'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------OTRO TRANSPORTE-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoOtrt != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <?php
                                        if($c178 == 1)
                                        {
                                            if($param178 != null)
                                            {
                                                ?>
                                                <tr align="center">
                                                    <td align="center"><?php if($param178 != null){echo $param178;}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            //SI SI TIENE DATOS PERO ESTE NO COINCIDE CON LA BUSQUEDA:
                                            elseif($param179 == null)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }

                                        }
                                        ///SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c178!=1)
                                        {
                                            ?>
                                            <tr align="center">
                                                <td><?php if($param178 != null){echo $param178;}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS OTRO TRANSPORTE:
                                elseif($numDatoOtrt == 0)
                                {
                                    if($c178==1)
                                    {
                                        ?>
                                        <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para OTRO TRANSPORTE'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------LUGAR DONDE PARQUEA-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoLugPar != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <?php
                                        if($c179 == 1)
                                        {
                                            if($param179 != null)
                                            {
                                                ?>
                                                <tr align="center">
                                                    <td align="center"><?php if($param179 != null){echo $param179;}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                            <?php
                                            }
                                            //SI SI TIENE DATOS PERO ESTE NO COINCIDE CON LA BUSQUEDA:
                                            elseif($param179 == null)
                                            {
                                                ?>
                                                    <script>
                                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                    </script>
                                                <?php
                                            }

                                        }
                                        ///SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c179!=1)
                                        {
                                        ?>
                                            <tr align="center">
                                                <td><?php if($param179 != null){echo $param179;}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS LUGAR PARQUEA:
                                elseif($numDatoLugPar == 0)
                                {
                                    if($c179==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para LUGAR DONDE PARQUEA'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------LUGAR TIEMPO DE DESPLAZAMIENTO-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoTieDes != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <?php
                                        if($c180 == 1)
                                        {
                                            if($param180 != null)
                                            {
                                                ?>
                                                <tr align="center">
                                                    <td align="center"><?php if($param180 != null){convertValor3($param180);}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            //SI SI TIENE DATOS PERO ESTE NO COINCIDE CON LA BUSQUEDA:
                                            elseif($param180 == null)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }

                                        }
                                        ///SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c180!=1)
                                        {
                                            ?>
                                            <tr align="center">
                                                <td><?php if($param180 != null){convertValor3($param180);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                            }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS TIEMPO DESPLAZAMIENTO:
                                elseif($numDatoTieDes == 0)
                                {
                                    if($c180==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para TIEMPO DE DESPLAZAMIENTO'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------TURNO HABITUAL-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoTurno != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <?php
                                        if($c181 == 1)
                                        {
                                        if($param181 != null)
                                        {
                                            ?>
                                            <tr align="center">
                                                <td align="center"><?php if($param181 != null){convertValor4($param181);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                        <?php
                                        }
                                        //SI SI TIENE DATOS PERO ESTE NO COINCIDE CON LA BUSQUEDA:
                                        elseif($param181 == null)
                                        {
                                        ?>
                                            <script>
                                                document.getElementById(<?php echo $row ?>).style.display = 'none';
                                            </script>
                                        <?php
                                        }

                                        }
                                        ///SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c181!=1)
                                        {
                                        ?>
                                            <tr align="center">
                                                <td><?php if($param181 != null){convertValor4($param181);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS TIEMPO DESPLAZAMIENTO:
                                elseif($numDatoTurno == 0)
                                {
                                if($c181==1)
                                {
                                ?>
                                    <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                    <?php
                                }
                                else
                                {
                                    echo 'Sin Dato para TURNO HABITUAL DE TRABAJO'.'<br>';
                                }
                                }
                                ?>
                            </td>
                            <!----------ACTIVIDAD LABORAL EXTRA-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoActExt != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <?php
                                        if($c182 == 1)
                                        {
                                            if($param182 != null)
                                            {
                                                ?>
                                                <tr align="center">
                                                    <td align="center"><?php if($param182 != null){convertValor5($param182);}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            //SI SI TIENE DATOS PERO ESTE NO COINCIDE CON LA BUSQUEDA:
                                            elseif($param182 == null)
                                            {
                                                ?>
                                                <script>
                                                document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }

                                        }
                                        ///SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c182!=1)
                                        {
                                            ?>
                                            <tr align="center">
                                                <td><?php if($param182 != null){convertValor5($param182);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS TIEMPO DESPLAZAMIENTO:
                                elseif($numDatoActExt == 0)
                                {
                                    if($c182==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para ACTIVIDAD LABORAL EXTRA'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------OTRA ACTIVIDAD LABORAL-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoActOtr != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <?php
                                        if($c183 == 1)
                                        {
                                            if($param183 != null)
                                            {
                                                ?>
                                                <tr align="center">
                                                    <td align="center"><?php if($param183 != null){echo $param183;}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                            <?php
                                            }
                                            //SI SI TIENE DATOS PERO ESTE NO COINCIDE CON LA BUSQUEDA:
                                            elseif($param183 == null)
                                            {
                                            ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                            <?php
                                            }

                                        }
                                        ///SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c183!=1)
                                        {
                                            ?>
                                            <tr align="center">
                                                <td><?php if($param183 != null){echo $param183;}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS TIEMPO DESPLAZAMIENTO:
                                elseif($numDatoActOtr == 0)
                                {
                                    if($c183==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para OTRA ACTIVIDAD LABORAL EXTRA'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------RANGO SALARIAL-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoRanSal != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 20px">
                                        <?php
                                        if($c184 == 1)
                                        {
                                        if($param184 != null)
                                        {
                                            ?>
                                            <tr align="center">
                                                <td align="center"><?php if($param184 != null){convertValor6($param184);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                        <?php
                                        }
                                        //SI SI TIENE DATOS PERO ESTE NO COINCIDE CON LA BUSQUEDA:
                                        elseif($param184 == null)
                                        {
                                        ?>
                                            <script>
                                                document.getElementById(<?php echo $row ?>).style.display = 'none';
                                            </script>
                                        <?php
                                        }

                                        }
                                        ///SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c184!=1)
                                        {
                                        ?>
                                            <tr align="center">
                                                <td><?php if($param184 != null){convertValor6($param184);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                //SI NO TIENE DATOS TIEMPO DESPLAZAMIENTO:
                                elseif($numDatoRanSal == 0)
                                {
                                if($c184==1)
                                {
                                ?>
                                    <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                    <?php
                                }
                                else
                                {
                                    echo 'Sin Dato para RANGO SALARIAL'.'<br>';
                                }
                                }
                                ?>
                            </td>
                            <!----------NECESIDAD DE FORMACION-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS NECESIDAD DE FORMACION
                                if($numDatoNeFor != null)
                                {
                                    ?>
                                    <table class="tblRegistros3" style="margin-bottom: 15px">
                                        <?php
                                        if($c185==1 || $c186==1 || $c187==1 || $c188==1 || $c189==1 || $c190==1 || $c191==1 || $c192==1 || $c193==1 || $c194==1 || $c195==1)
                                        {
                                            if($nefCae==$param185 || $nefDec==$param186 || $nefRef==$param187 || $nefMac==$param188 || $nefFip==$param189 || $nefFtt==$param190
                                                || $nefIdi==$param191 || $nefInt==$param192 || $nefFcr==$param193 || $nefOtr==$param194 || $nefNot==$param195)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Capacitación Empresarial</td>   <td>Desarrollo de Competencias</td>         <td>Relaciones Familiares</td>
                                                    <td>Manejo de Conflictos</td>       <td>Finanzas Personales</td>                <td>Formación Técnica para el Trabajo</td>
                                                    <td>Idiomas</td>                    <td>Informática y Nuevas Tecnologías</td>   <td>Formación en Conocimientos Relacionados con su Profesión</td>
                                                    <td>Otro</td>                           <td>No Tiene Necesidades</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param185 != null){convertValor($param185);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param186 != null){convertValor($param186);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param187 != null){convertValor($param187);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param188 != null){convertValor($param188);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param189 != null){convertValor($param189);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param190 != null){convertValor($param190);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param191 != null){convertValor($param191);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param192 != null){convertValor($param192);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param193 != null){convertValor($param193);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param194 != null){convertValor($param194);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param195 != null){convertValor($param195);}   else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($nefCae!=$param185 || $nefDec!=$param186 || $nefRef!=$param187 || $nefMac!=$param188 || $nefFip!=$param189 || $nefFtt!=$param190
                                                || $nefIdi!=$param191 || $nefInt!=$param192 || $nefFcr!=$param193 || $nefOtr!=$param194 || $nefNot!=$param195)
                                            {
                                                ?>
                                                    <script>
                                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                    </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($nefCae!=$param185 and $nefDec!=$param186 and $nefRef!=$param187 and $nefMac!=$param188 and $nefFip!=$param189 and $nefFtt!=$param190
                                               and $nefIdi!=$param191 and $nefInt!=$param192 and $nefFcr!=$param193 and $nefOtr!=$param194 and $nefNot!=$param195)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Capacitación Empresarial</td>   <td>Desarrollo de Competencias</td>         <td>Relaciones Familiares</td>
                                                <td>Manejo de Conflictos</td>       <td>Finanzas Personales</td>                <td>Formación Técnica para el Trabajo</td>
                                                <td>Idiomas</td>                    <td>Informática y Nuevas Tecnologías</td>   <td>Formación en Conocimientos Relacionados con su Profesión</td>
                                                <td>Otro</td>                           <td>No Tiene Necesidades</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param185 != null){convertValor($param185);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param186 != null){convertValor($param186);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param187 != null){convertValor($param187);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param188 != null){convertValor($param188);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param189 != null){convertValor($param189);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param190 != null){convertValor($param190);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param191 != null){convertValor($param191);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param192 != null){convertValor($param192);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param193 != null){convertValor($param193);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param194 != null){convertValor($param194);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param195 != null){convertValor($param195);}   else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS NECESIDAD DE FORMACION
                                elseif($numDatoNeFor == 0)
                                {
                                    if($c185==1 || $c186==1 || $c187==1 || $c188==1 || $c189==1 || $c190==1 || $c191==1 || $c192==1 || $c193==1 || $c194==1 || $c195==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para NECESIDADES DE FORMACION'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------ALIMENTACION ALMUERZO-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS ALIMENTACION ALMUERZO
                                if($numDatoAliAlm != null)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 15px">
                                        <?php
                                        if($c196==1 || $c197==1 || $c198==1 || $c199==1 || $c200==1)
                                        {
                                            if($ingTac == ''){$ingTac = 'off';} if($ingCob == ''){$ingCob = 'off';} if($ingCoo == ''){$ingCoo = 'off';}
                                            if($ingVac == ''){$ingVac = 'off';} if($ingOtr == ''){$ingOtr = 'off';}

                                            if($ingTac==$param196 || $ingCob==$param197 || $ingCoo==$param198 || $ingVac==$param199 || $ingOtr==$param1200)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Trae sus Alimentos de la Casa</td>  <td>Compra en la Cafetería Bocatos</td> <td>Compra en Otros Lugares</td>
                                                    <td>Va a su Casa</td>                   <td>Otros</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param196 != null){valServicio($param196);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param197 != null){valServicio($param197);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param198 != null){valServicio($param198);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param199 != null){valServicio($param199);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param1200 != null){valServicio($param200);}   else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($ingTac!=$param196 || $ingCob!=$param197 || $ingCoo!=$param198 || $ingVac!=$param199 || $ingOtr!=$param1200)
                                            {
                                                ?>
                                                    <script>
                                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                    </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c196!=1 and $c197!=1 and $c198!=1 and $c199!=1 and $c200!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Trae sus Alimentos de la Casa</td>  <td>Compra en la Cafetería Bocatos</td> <td>Compra en Otros Lugares</td>
                                                <td>Va a su Casa</td>                   <td>Otros</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param196 != null){valServicio($param196);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param197 != null){valServicio($param197);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param198 != null){valServicio($param198);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param199 != null){valServicio($param199);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param1200 != null){valServicio($param200);}   else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                //SI NO TIENE DATOS ALIMENTACION ALMUERZO
                                elseif($numDatoAliAlm == 0)
                                {
                                if($c196==1 || $c197==1 || $c198==1 || $c199==1 || $c200==1)
                                {
                                ?>
                                    <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                    <?php
                                }
                                else
                                {
                                    echo 'Sin Dato para HABITUALMENTE A LA HORA DEL ALMUERZO'.'<br>';
                                }
                                }
                                ?>
                            </td>
                            <!------ACTIVIDADES EN LAS QUE PARTICIPARIA-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS ACTIVIDADES:
                                if($numDatoActPar != 0)
                                {
                                    ?>
                                    <table class="tblRegistros2" style="margin-bottom: 15px">
                                        <?php
                                        if($c201==1 || $c202==1 || $c203==1 || $c204==1 || $c205==1 || $c206==1 || $c207==1 || $c208==1 || $c209==1 || $c210==1 || $c211==1
                                        || $c212==1 || $c213==1 || $c214==1 || $c215==1 || $c216==1 || $c217==1 || $c218==1 || $c219==1 || $c220==1)
                                        {
                                            if($acpTob == ''){$acpTob = 'off';} if($acpTop == ''){$acpTop = 'off';} if($acpTov == ''){$acpTov = 'off';}
                                            if($acpTba == ''){$acpTba = 'off';} if($acpTot == ''){$acpTot = 'off';} if($acpCam == ''){$acpCam = 'off';}
                                            if($acpBai == ''){$acpBai = 'off';} if($acpYog == ''){$acpYog = 'off';} if($actEnp == ''){$actEnp = 'off';}
                                            if($acpCic == ''){$acpCic = 'off';} if($acpMar == ''){$acpMar = 'off';} if($acpTho == ''){$acpTho = 'off';}
                                            if($acpGte == ''){$acpGte = 'off';} if($acpArp == ''){$acpArp = 'off';} if($acpMnu == ''){$acpMnu = 'off';}
                                            if($acpGas == ''){$acpGas = 'off';} if($acpCli == ''){$acpCli = 'off';} if($acpCop == ''){$acpCop = 'off';}
                                            if($acpTpi == ''){$acpTpi = 'off';} if($acpOtr == ''){$acpOtr = 'off';}

                                            if($acpTob == $param201 || $acpTop == $param202 || $acpTov == $param203 || $acpTba == $param204 || $acpTot == $param205
                                            || $acpCam == $param206 || $acpBai == $param207 || $acpYog == $param208 || $actEnp == $param209 || $acpCic == $param210
                                            || $acpMar == $param211 || $acpTho == $param212 || $acpGte == $param213 || $acpArp == $param214 || $acpMnu == $param215
                                            || $acpGas == $param216 || $acpCli == $param217 || $acpCop == $param218 || $acpTpi == $param219 || $acpOtr == $param220)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Torneo de Bolos</td>    <td>Torneo de PlayStation</td>  <td>Torneo de Voleibol</td>             <td>Torneo de Baloncesto</td>   <td>Torneo de Tenis de Campo</td>
                                                    <td>Caminatas</td>          <td>Baile</td>                  <td>Yoga</td>                           <td>Encuentro de Parejas</td>   <td>Ciclo Paseos</td>
                                                    <td>Maratones</td>          <td>Tarde de Hobbies</td>       <td>Grupo de Teatro</td>                <td>Artes Plasticas</td>        <td>Manualidades</td>
                                                    <td>Gastronomía</td>        <td>Clases de Inglés</td>       <td>Conciencia Plena (Mindfulnes)</td>  <td>Tardes de Picnic</td>       <td>Otros</td>
                                                </tr>
                                            <tr align="center">
                                                <td><?php valServicio2($param201);?></td>     <td><?php valServicio2($param202); ?></td>    <td><?php valServicio2($param203); ?></td>
                                                <td><?php valServicio2($param204); ?></td>    <td><?php valServicio2($param205); ?></td>    <td><?php valServicio2($param206); ?></td>
                                                <td><?php valServicio2($param207);?></td>     <td><?php valServicio2($param208); ?></td>    <td><?php valServicio2($param209); ?></td>
                                                <td><?php valServicio2($param210); ?></td>    <td><?php valServicio2($param211); ?></td>    <td><?php valServicio2($param212); ?></td>
                                                <td><?php valServicio2($param213);?></td>     <td><?php valServicio2($param214); ?></td>    <td><?php valServicio2($param215); ?></td>
                                                <td><?php valServicio2($param216); ?></td>    <td><?php valServicio2($param217); ?></td>    <td><?php valServicio2($param218); ?></td>
                                                <td><?php valServicio2($param219);?></td>     <td><?php valServicio2($param220); ?></td>
                                            </tr>
                                                <?php
                                            }
                                            else
                                            {
                                            ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                            <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c201!=1 and $c202!=1 and $c203!=1 and $c204!=1 and $c205!=1 and $c206!=1 and $c207!=1 and $c208!=1 and $c209!=1 and $c210!=1 and $c211!=1
                                           and $c212!=1 and $c213!=1 and $c214!=1 and $c215!=1 and $c216!=1 and $c217!=1 and $c218!=1 and $c219!=1 and $c220!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Torneo de Bolos</td>    <td>Torneo de PlayStation</td>  <td>Torneo de Voleibol</td>             <td>Torneo de Baloncesto</td>   <td>Torneo de Tenis de Campo</td>
                                                <td>Caminatas</td>          <td>Baile</td>                  <td>Yoga</td>                           <td>Encuentro de Parejas</td>   <td>Ciclo Paseos</td>
                                                <td>Maratones</td>          <td>Tarde de Hobbies</td>       <td>Grupo de Teatro</td>                <td>Artes Plasticas</td>        <td>Manualidades</td>
                                                <td>Gastronomía</td>        <td>Clases de Inglés</td>       <td>Conciencia Plena (Mindfulnes)</td>  <td>Tardes de Picnic</td>       <td>Otros</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php valServicio2($param201);?></td>     <td><?php valServicio2($param202); ?></td>    <td><?php valServicio2($param203); ?></td>
                                                <td><?php valServicio2($param204); ?></td>    <td><?php valServicio2($param205); ?></td>    <td><?php valServicio2($param206); ?></td>
                                                <td><?php valServicio2($param207);?></td>     <td><?php valServicio2($param208); ?></td>    <td><?php valServicio2($param209); ?></td>
                                                <td><?php valServicio2($param210); ?></td>    <td><?php valServicio2($param211); ?></td>    <td><?php valServicio2($param212); ?></td>
                                                <td><?php valServicio2($param213);?></td>     <td><?php valServicio2($param214); ?></td>    <td><?php valServicio2($param215); ?></td>
                                                <td><?php valServicio2($param216); ?></td>    <td><?php valServicio2($param217); ?></td>    <td><?php valServicio2($param218); ?></td>
                                                <td><?php valServicio2($param219);?></td>     <td><?php valServicio2($param220); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS ACTIVIDADES:
                                elseif($numDatoActPar == 0)
                                {
                                    if($c201==1 || $c202==1 || $c203==1 || $c204==1 || $c205==1 || $c206==1 || $c207==1 || $c208==1 || $c209==1 || $c210==1 || $c211==1
                                    || $c212==1 || $c213==1 || $c214==1 || $c215==1 || $c216==1 || $c217==1 || $c218==1 || $c219==1 || $c220==1)
                                    {
                                        ?>
                                        <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para ACTIVIDADES EN LAS QUE PARTICIPARIA ACTIVAMENTE'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!------DIA HORA PARTICIPACION EN ACTIVIDADES-->
                            <td style="padding: 5px 10px">
                                <?php
                                if($numDatoHodPar != 0)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 15px">
                                        <?php
                                        if($c221==1 || $c222==1)
                                        {
                                            if($pacDia == ''){$pacDia = 'off';} if($pacHor == ''){$pacHor = 'off';}

                                            if($pacDia == $param221 || $pacHor == $param222)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Dia</td>    <td>Hora</td>
                                                </tr>
                                                <tr align="center">
                                                    <td align="center"><?php if($param221 != null){convertValor7($param221);}    else{echo 'Sin Dato';}  ?></td>
                                                    <td align="center"><?php if($param222 != null){convertValor7($param222);}    else{echo 'Sin Dato';}  ?></td>
                                                </tr>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                    <script>
                                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                    </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c221!=1 and $c222!=1)
                                        {
                                        ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Dia</td>    <td>Hora</td>
                                            </tr>
                                            <tr align="center">
                                                <td align="center"><?php if($param221 != null){convertValor7($param221);}    else{echo 'Sin Dato';}  ?></td>
                                                <td align="center"><?php if($param222 != null){convertValor7($param222);}    else{echo 'Sin Dato';}  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                //SI NO TIENE DATOS TRANSPORTE HABITUAL:
                                elseif($numDatoHodPar == 0)
                                {
                                if($c221==1 || $c222==1)
                                {
                                ?>
                                    <script>
                                        document.getElementById(<?php echo $row ?>).style.display = 'none';
                                    </script>
                                    <?php
                                }
                                else
                                {
                                    echo 'Sin Dato para DIA / HORA ASISTENCIA A ACTIVIDADES'.'<br>';
                                }
                                }
                                ?>
                            </td>
                            <!------QUE HACE EN SU TIEMPO LIBRE-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS QUE HACE EN SU TIEMPO LIBRE:
                                if($numDatoHaTil != 0)
                                {
                                    ?>
                                    <table class="tblRegistros2" style="margin-bottom: 15px">
                                        <?php
                                        if($c223==1 || $c224==1 || $c225==1 || $c226==1 || $c227==1 || $c228==1 || $c229==1 || $c230==1 || $c231==1 || $c232==1 || $c233==1 || $c234==1
                                        || $c235==1 || $c236==1 || $c237==1 || $c238==1 || $c239==1 || $c240==1 || $c241==1 || $c242==1 || $c243==1 || $c244==1 || $c245==1 || $c246==1)
                                        {
                                            if($qhtCin == ''){$qhtCin = 'off';} if($nefDec == ''){$nefDec = 'off';} if($qhtVij == ''){$qhtVij = 'off';}
                                            if($qhtVte == ''){$qhtVte = 'off';} if($qhtNai == ''){$qhtNai = 'off';} if($qhtIcc == ''){$qhtIcc = 'off';}
                                            if($qhtIpa == ''){$qhtIpa = 'off';} if($qhtIfi == ''){$qhtIfi = 'off';} if($qhtCle == ''){$qhtCle = 'off';}
                                            if($qhtDed == ''){$qhtDed = 'off';} if($qhtJar == ''){$qhtJar = 'off';} if($qhtCon == ''){$qhtCon = 'off';}
                                            if($qhtPin == ''){$qhtPin = 'off';} if($qhtEsc == ''){$qhtEsc = 'off';} if($qhtFot == ''){$qhtFot = 'off';}
                                            if($qhtVim == ''){$qhtVim = 'off';} if($qhtVib == ''){$qhtVib = 'off';} if($qhtEac == ''){$qhtEac = 'off';}
                                            if($qhtDan == ''){$qhtDan = 'off';} if($qhtTim == ''){$qhtTim = 'off';} if($qhtCoc == ''){$qhtCoc = 'off';}
                                            if($qhtMnu == ''){$qhtMnu = 'off';} if($qhtOtr == ''){$qhtOtr = 'off';} if($qhtNin == ''){$qhtNin = 'off';}

                                            if($qhtCin == $param223 || $nefDec == $param224 || $qhtVij == $param225 || $qhtVte == $param226 || $qhtNai == $param227 || $qhtIcc == $param228
                                            || $qhtIpa == $param229 || $qhtIfi == $param230 || $qhtCle == $param231 || $qhtDed == $param232 || $qhtJar == $param233 || $qhtCon == $param234
                                            || $qhtPin == $param235 || $qhtEsc == $param236 || $qhtFot == $param237 || $qhtVim == $param238 || $qhtVib == $param239 || $qhtEac == $param240
                                            || $qhtDan == $param241 || $qhtTim == $param242 || $qhtCoc == $param243 || $qhtMnu == $param244 || $qhtOtr == $param245 || $qhtNin == $param246)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Cine</td>                       <td>Deporte</td>                                <td>Video Juegos</td>   <td>Ver Televisión</td>
                                                    <td>Navegar en Internet</td>        <td>Ir a un Centro Comercial</td>               <td>Ir a un Parque</td> <td>Ir a Fiestas con sus Amigos</td>
                                                    <td>Clases ExtraCurriculares</td>   <td>Descansar/Dormir</td>                       <td>Jardinería</td>     <td>Conciertos</td>
                                                    <td>Pintura</td>                    <td>Escultura</td>                              <td>Fotografía</td>     <td>Visitar Museos</td>
                                                    <td>Visitar Bibliotecas</td>        <td>Espectáculos Artísticos y Culturales</td>   <td>Danzas</td>         <td>Tocar un Instrumento Musical</td>
                                                    <td>Cocina</td>                     <td>Manualidades</td>                           <td>Otro</td>           <td>Ninguno</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php convertValor($param223);?></td>     <td><?php convertValor($param224); ?></td>    <td><?php convertValor($param225); ?></td>
                                                    <td><?php convertValor($param226); ?></td>    <td><?php convertValor($param227); ?></td>    <td><?php convertValor($param228); ?></td>
                                                    <td><?php convertValor($param229);?></td>     <td><?php convertValor($param230); ?></td>    <td><?php convertValor($param231); ?></td>
                                                    <td><?php convertValor($param232); ?></td>    <td><?php convertValor($param233); ?></td>    <td><?php convertValor($param234); ?></td>
                                                    <td><?php convertValor($param235);?></td>     <td><?php convertValor($param236); ?></td>    <td><?php convertValor($param237); ?></td>
                                                    <td><?php convertValor($param238); ?></td>    <td><?php convertValor($param239); ?></td>    <td><?php convertValor($param240); ?></td>
                                                    <td><?php convertValor($param241);?></td>     <td><?php convertValor($param242); ?></td>    <td><?php convertValor($param243); ?></td>
                                                    <td><?php convertValor($param244);?></td>     <td><?php convertValor($param245); ?></td>    <td><?php convertValor($param246); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c223!=1 and $c224!=1 and $c225!=1 and $c226!=1 and $c227!=1 and $c228!=1 and $c229!=1 and $c230!=1 and $c231!=1 and $c232!=1 and $c233!=1 and $c234!=1
                                           and $c235!=1 and $c236!=1 and $c237!=1 and $c238!=1 and $c239!=1 and $c240!=1 and $c241!=1 and $c242!=1 and $c243!=1 and $c244!=1 and $c245!=1 and $c246!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Cine</td>                       <td>Deporte</td>                                <td>Video Juegos</td>   <td>Ver Televisión</td>
                                                <td>Navegar en Internet</td>        <td>Ir a un Centro Comercial</td>               <td>Ir a un Parque</td> <td>Ir a Fiestas con sus Amigos</td>
                                                <td>Clases ExtraCurriculares</td>   <td>Descansar/Dormir</td>                       <td>Jardinería</td>     <td>Conciertos</td>
                                                <td>Pintura</td>                    <td>Escultura</td>                              <td>Fotografía</td>     <td>Visitar Museos</td>
                                                <td>Visitar Bibliotecas</td>        <td>Espectáculos Artísticos y Culturales</td>   <td>Danzas</td>         <td>Tocar un Instrumento Musical</td>
                                                <td>Cocina</td>                     <td>Manualidades</td>                           <td>Otro</td>           <td>Ninguno</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php convertValor($param223);?></td>     <td><?php convertValor($param224); ?></td>    <td><?php convertValor($param225); ?></td>
                                                <td><?php convertValor($param226); ?></td>    <td><?php convertValor($param227); ?></td>    <td><?php convertValor($param228); ?></td>
                                                <td><?php convertValor($param229);?></td>     <td><?php convertValor($param230); ?></td>    <td><?php convertValor($param231); ?></td>
                                                <td><?php convertValor($param232); ?></td>    <td><?php convertValor($param233); ?></td>    <td><?php convertValor($param234); ?></td>
                                                <td><?php convertValor($param235);?></td>     <td><?php convertValor($param236); ?></td>    <td><?php convertValor($param237); ?></td>
                                                <td><?php convertValor($param238); ?></td>    <td><?php convertValor($param239); ?></td>    <td><?php convertValor($param240); ?></td>
                                                <td><?php convertValor($param241);?></td>     <td><?php convertValor($param242); ?></td>    <td><?php convertValor($param243); ?></td>
                                                <td><?php convertValor($param244);?></td>     <td><?php convertValor($param245); ?></td>    <td><?php convertValor($param246); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                //SI NO TIENE DATOS QUE HACE EN SU TIEMPO LIBRE:
                                elseif($numDatoHaTil == 0)
                                {
                                    if($c223==1 || $c224==1 || $c225==1 || $c226==1 || $c227==1 || $c228==1 || $c229==1 || $c230==1 || $c231==1 || $c232==1 || $c233==1 || $c234==1
                                    || $c235==1 || $c236==1 || $c237==1 || $c238==1 || $c239==1 || $c240==1 || $c241==1 || $c242==1 || $c243==1 || $c244==1 || $c245==1 || $c246==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para QUÉ HACE EN SU TIEMPO LIBRE'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!------CON QUIEN PASA SU TIEMPO LIBRE-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS CON QUIEN PASA SU TIEMPO LIBRE:
                                if($numDatoQptLib != 0)
                                {
                                    ?>
                                    <table class="tblRegistros2" style="margin-bottom: 15px">
                                        <?php
                                        if($c247==1 || $c248==1 || $c249==1 || $c250==1 || $c251==1 || $c252==1 || $c253==1 || $c254==1 || $c255==1)
                                        {
                                            /*
                                            if($ptlHij == ''){$ptlHij = 'off';} if($ptlAmi == ''){$ptlAmi = 'off';} if($ptlMas == ''){$ptlMas = 'off';}
                                            if($ptlSol == ''){$ptlSol = 'off';} if($ptlFam == ''){$ptlFam = 'off';} if($ptlAmo == ''){$ptlAmo = 'off';}
                                            if($ptlPar == ''){$ptlPar = 'off';} if($ptlCot == ''){$ptlCot = 'off';} if($ptlOtr == ''){$ptlOtr = 'off';}
                                            */

                                            if($ptlHij == $param247 || $ptlAmi == $param248 || $ptlMas == $param249 || $ptlSol == $param250 || $ptlFam == $param251
                                            || $ptlAmo == $param252 || $ptlPar == $param253 || $ptlCot == $param254 || $ptlOtr == $param255)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Hijos / Hijas</td>  <td>Amigos / Amigas</td>            <td>Mascotas</td>                   <td>Solo</td>
                                                    <td>Familia</td>        <td>Amigos o Amigas on Line</td>    <td>Pareja (Novio o Conyuge)</td>   <td>Compañeros de Trabajo</td>
                                                    <td>Otro</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php convertValor($param247);?></td>     <td><?php convertValor($param248); ?></td>    <td><?php convertValor($param249); ?></td>
                                                    <td><?php convertValor($param250); ?></td>    <td><?php convertValor($param251); ?></td>    <td><?php convertValor($param252); ?></td>
                                                    <td><?php convertValor($param253);?></td>     <td><?php convertValor($param254); ?></td>    <td><?php convertValor($param255); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c247!=1 and $c248!=1 and $c249!=1 and $c250!=1 and $c251!=1 and $c252!=1 and $c253!=1 and $c254!=1 and $c255!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Hijos / Hijas</td>  <td>Amigos / Amigas</td>            <td>Mascotas</td>                   <td>Solo</td>
                                                <td>Familia</td>        <td>Amigos o Amigas on Line</td>    <td>Pareja (Novio o Conyuge)</td>   <td>Compañeros de Trabajo</td>
                                                <td>Otro</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php convertValor($param247);?></td>     <td><?php convertValor($param248); ?></td>    <td><?php convertValor($param249); ?></td>
                                                <td><?php convertValor($param250); ?></td>    <td><?php convertValor($param251); ?></td>    <td><?php convertValor($param252); ?></td>
                                                <td><?php convertValor($param253);?></td>     <td><?php convertValor($param254); ?></td>    <td><?php convertValor($param255); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                //SI NO TIENE DATOS CON QUIEN PASA SU TIEMPO LIBRE:
                                elseif($numDatoQptLib == 0)
                                {
                                    if($c247==1 || $c248==1 || $c249==1 || $c250==1 || $c251==1 || $c252==1 || $c253==1 || $c254==1 || $c255==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para CON QUIÉN PASA SU TIEMPO LIBRE'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!------QUE HACEN SUS HIJOS EN SU TIEMPO LIBRE-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS QUE HACE EN SU TIEMPO LIBRE:
                                if($numDatoHhTil != 0)
                                {
                                    ?>
                                    <table class="tblRegistros2" style="margin-bottom: 15px">
                                        <?php
                                        if($c256==1 || $c257==1 || $c258==1 || $c259==1 || $c260==1 || $c261==1 || $c262==1 || $c263==1 || $c264==1 || $c265==1 || $c266==1 || $c267==1
                                        || $c268==1 || $c269==1 || $c270==1 || $c271==1 || $c272==1 || $c273==1 || $c274==1 || $c275==1 || $c276==1 || $c277==1 || $c278==1 || $c279==1)
                                        {
                                            /*
                                            if($qhhCin == ''){$qhhCin = 'off';} if($qhhDep == ''){$qhhDep = 'off';} if($qhhVij == ''){$qhhVij = 'off';}
                                            if($qhhVte == ''){$qhhVte = 'off';} if($qhhNai == ''){$qhhNai = 'off';} if($qhhIcc == ''){$qhhIcc = 'off';}
                                            if($qhhIpa == ''){$qhhIpa = 'off';} if($qhhIfi == ''){$qhhIfi = 'off';} if($qhhCle == ''){$qhhCle = 'off';}
                                            if($qhhDed == ''){$qhhDed = 'off';} if($qhhJar == ''){$qhhJar = 'off';} if($qhhCon == ''){$qhhCon = 'off';}
                                            if($qhhPin == ''){$qhhPin = 'off';} if($qhhEsc == ''){$qhhEsc = 'off';} if($qhhFot == ''){$qhhFot = 'off';}
                                            if($qhhVim == ''){$qhhVim = 'off';} if($qhhVib == ''){$qhhVib = 'off';} if($qhhEac == ''){$qhhEac = 'off';}
                                            if($qhhDan == ''){$qhhDan = 'off';} if($qhhTim == ''){$qhhTim = 'off';} if($qhhCoc == ''){$qhhCoc = 'off';}
                                            if($qhhMnu == ''){$qhhMnu = 'off';} if($qhhOtr == ''){$qhhOtr = 'off';} if($qhhNin == ''){$qhhNin = 'off';}
                                            */

                                            if($qhhCin == $param256 || $qhhDep == $param257 || $qhhVij == $param258 || $qhhVte == $param259 || $qhhNai == $param260 || $qhhIcc == $param261
                                            || $qhhIpa == $param262 || $qhhIfi == $param263 || $qhhCle == $param264 || $qhhDed == $param265 || $qhhJar == $param266 || $qhhCon == $param267
                                            || $qhhPin == $param268 || $qhhEsc == $param269 || $qhhFot == $param270 || $qhhVim == $param271 || $qhhVib == $param272 || $qhhEac == $param273
                                            || $qhhDan == $param274 || $qhhTim == $param275 || $qhhCoc == $param276 || $qhhMnu == $param277 || $qhhOtr == $param278 || $qhhNin == $param279)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Cine</td>                       <td>Deporte</td>                                <td>Video Juegos</td>   <td>Ver Televisión</td>
                                                <td>Navegar en Internet</td>        <td>Ir a un Centro Comercial</td>               <td>Ir a un Parque</td> <td>Ir a Fiestas con sus Amigos</td>
                                                <td>Clases ExtraCurriculares</td>   <td>Descansar/Dormir</td>                       <td>Jardinería</td>     <td>Conciertos</td>
                                                <td>Pintura</td>                    <td>Escultura</td>                              <td>Fotografía</td>     <td>Visitar Museos</td>
                                                <td>Visitar Bibliotecas</td>        <td>Espectáculos Artísticos y Culturales</td>   <td>Danzas</td>         <td>Tocar un Instrumento Musical</td>
                                                <td>Cocina</td>                     <td>Manualidades</td>                           <td>Otro</td>           <td>Ninguno</td>
                                            </tr>
                                                <tr align="center">
                                                    <td><?php convertValor($param256); ?></td>     <td><?php convertValor($param257); ?></td>    <td><?php convertValor($param258); ?></td>
                                                    <td><?php convertValor($param259); ?></td>     <td><?php convertValor($param260); ?></td>    <td><?php convertValor($param261); ?></td>
                                                    <td><?php convertValor($param262); ?></td>     <td><?php convertValor($param263); ?></td>    <td><?php convertValor($param264); ?></td>
                                                    <td><?php convertValor($param265); ?></td>     <td><?php convertValor($param266); ?></td>    <td><?php convertValor($param267); ?></td>
                                                    <td><?php convertValor($param268); ?></td>     <td><?php convertValor($param269); ?></td>    <td><?php convertValor($param270); ?></td>
                                                    <td><?php convertValor($param271); ?></td>     <td><?php convertValor($param272); ?></td>    <td><?php convertValor($param273); ?></td>
                                                    <td><?php convertValor($param274); ?></td>     <td><?php convertValor($param275); ?></td>    <td><?php convertValor($param276); ?></td>
                                                    <td><?php convertValor($param277); ?></td>     <td><?php convertValor($param278); ?></td>    <td><?php convertValor($param279); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c256!=1 and $c257!=1 and $c258!=1 and $c259!=1 and $c260!=1 and $c261!=1 and $c262!=1 and $c263!=1 and $c264!=1 and $c265!=1 and $c266!=1 and $c267!=1
                                            and $c268!=1 and $c269!=1 and $c270!=1 and $c271!=1 and $c272!=1 and $c273!=1 and $c274!=1 and $c275!=1 and $c276!=1 and $c277!=1 and $c278!=1 and $c279!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Cine</td>                       <td>Deporte</td>                                <td>Video Juegos</td>   <td>Ver Televisión</td>
                                                <td>Navegar en Internet</td>        <td>Ir a un Centro Comercial</td>               <td>Ir a un Parque</td> <td>Ir a Fiestas con sus Amigos</td>
                                                <td>Clases ExtraCurriculares</td>   <td>Descansar/Dormir</td>                       <td>Jardinería</td>     <td>Conciertos</td>
                                                <td>Pintura</td>                    <td>Escultura</td>                              <td>Fotografía</td>     <td>Visitar Museos</td>
                                                <td>Visitar Bibliotecas</td>        <td>Espectáculos Artísticos y Culturales</td>   <td>Danzas</td>         <td>Tocar un Instrumento Musical</td>
                                                <td>Cocina</td>                     <td>Manualidades</td>                           <td>Otro</td>           <td>Ninguno</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php convertValor($param256); ?></td>     <td><?php convertValor($param257); ?></td>    <td><?php convertValor($param258); ?></td>
                                                <td><?php convertValor($param259); ?></td>     <td><?php convertValor($param260); ?></td>    <td><?php convertValor($param261); ?></td>
                                                <td><?php convertValor($param262); ?></td>     <td><?php convertValor($param263); ?></td>    <td><?php convertValor($param264); ?></td>
                                                <td><?php convertValor($param265); ?></td>     <td><?php convertValor($param266); ?></td>    <td><?php convertValor($param267); ?></td>
                                                <td><?php convertValor($param268); ?></td>     <td><?php convertValor($param269); ?></td>    <td><?php convertValor($param270); ?></td>
                                                <td><?php convertValor($param271); ?></td>     <td><?php convertValor($param272); ?></td>    <td><?php convertValor($param273); ?></td>
                                                <td><?php convertValor($param274); ?></td>     <td><?php convertValor($param275); ?></td>    <td><?php convertValor($param276); ?></td>
                                                <td><?php convertValor($param277); ?></td>     <td><?php convertValor($param278); ?></td>    <td><?php convertValor($param279); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS QUE HACE EN SU TIEMPO LIBRE:
                                elseif($numDatoHhTil == 0)
                                {
                                    if($c256==1 || $c257==1 || $c258==1 || $c259==1 || $c260==1 || $c261==1 || $c262==1 || $c263==1 || $c264==1 || $c265==1 || $c266==1 || $c267==1
                                    || $c268==1 || $c269==1 || $c270==1 || $c271==1 || $c272==1 || $c273==1 || $c274==1 || $c275==1 || $c276==1 || $c277==1 || $c278==1 || $c279==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para QUÉ HACEN SUS HIJOS EN SU TIEMPO LIBRE'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!------BARRERAS USO TIEMPO LIBRE-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS QUE HACE EN SU TIEMPO LIBRE:
                                if($numDatoBarUtl != 0)
                                {
                                    ?>
                                    <table class="tblRegistros2" style="margin-bottom: 15px">
                                        <?php
                                        if($c280==1 || $c281==1 || $c282==1 || $c283==1 || $c284==1 || $c285==1 || $c286==1)
                                        {
                                            if($barFdi == $param280 || $barNcd == $param281 || $barDap == $param282 || $barFms == $param283 || $barNdt == $param284 || $barOtr == $param285
                                            || $barNin == $param286)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Falta de Dinero</td>                            <td>No Coincidir la Disponibilidad del Tiempo Libre con la Familia</td>
                                                    <td>Desconocimiento de Actividades y Programas</td> <td>Falta Motivación para Salir</td>
                                                    <td>No Disponer de Tiempo Libre</td>                <td>Otro</td>
                                                    <td>Ninguno</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php convertValor($param280); ?></td>     <td><?php convertValor($param281); ?></td>    <td><?php convertValor($param282); ?></td>
                                                    <td><?php convertValor($param283); ?></td>     <td><?php convertValor($param284); ?></td>    <td><?php convertValor($param285); ?></td>
                                                    <td><?php convertValor($param286); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c280!=1 and $c281!=1 and $c282!=1 and $c283!=1 and $c284!=1 and $c285!=1 and $c286!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Falta de Dinero</td>                            <td>No Coincidir la Disponibilidad del Tiempo Libre con la Familia</td>
                                                <td>Desconocimiento de Actividades y Programas</td> <td>Falta Motivación para Salir</td>
                                                <td>No Disponer de Tiempo Libre</td>                <td>Otro</td>
                                                <td>Ninguno</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php convertValor($param280); ?></td>     <td><?php convertValor($param281); ?></td>    <td><?php convertValor($param282); ?></td>
                                                <td><?php convertValor($param283); ?></td>     <td><?php convertValor($param284); ?></td>    <td><?php convertValor($param285); ?></td>
                                                <td><?php convertValor($param286); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                //SI NO TIENE DATOS QUE HACE EN SU TIEMPO LIBRE:
                                elseif($numDatoBarUtl == 0)
                                {
                                    if($c280==1 || $c281==1 || $c282==1 || $c283==1 || $c284==1 || $c285==1 || $c286==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para BARRERAS EN EL USO DEL TIEMPO LIBRE'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!----------ROLES EMPLEADO-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS ALIMENTACION ALMUERZO
                                if($numDatoRolCua != null)
                                {
                                    ?>
                                    <table class="tblRegistros" style="margin-bottom: 15px">
                                        <?php
                                        if($c287==1 || $c288==1 || $c289==1 || $c290==1)
                                        {
                                            if($rolAud == ''){$rolAud = 'off';} if($rolBre == ''){$rolBre = 'off';} if($rolOtr == ''){$rolOtr = 'off';}

                                            if($rolAud==$param287 || $rolBre==$param288 || $rolOtr==$param289 || $param290 != null)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Es Auditor Interno de Calidad</td>  <td>Hace Parte de la Brigada de Emergencia</td>
                                                    <td>Otro</td>                           <td>Otro, Cúal</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php if($param287 != null){valServicio($param287);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param288 != null){valServicio($param288);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php if($param289 != null){valServicio($param289);}   else{echo 'Sin Dato';}  ?></td>
                                                    <td><?php echo $param290 ?></td>
                                                </tr>
                                                <?php
                                            }
                                            elseif($rolAud!=$param287 || $rolBre!=$param288 || $rolOtr!=$param289)
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c287!=1 and $c288!=1 and $c289!=1 and $c290!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Es Auditor Interno de Calidad</td>  <td>Hace Parte de la Brigada de Emergencia</td>
                                                <td>Otro</td>                           <td>Otro, Cúal</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php if($param287 != null){valServicio($param287);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param288 != null){valServicio($param288);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php if($param289 != null){valServicio($param289);}   else{echo 'Sin Dato';}  ?></td>
                                                <td><?php echo $param290;  ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS ALIMENTACION ALMUERZO
                                elseif($numDatoRolCua == 0)
                                {
                                    if($c287==1 || $c288==1 || $c299==1 || $c290==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para ROL DESEMPEÑADO EN LA INSTITUCION'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                            <!------PERTENENCIA A COMITES-->
                            <td style="padding: 5px 10px">
                                <?php
                                //SI SI TIENE DATOS PERTENENCIA A COMITES:
                                if($numDatoPerCom != 0)
                                {
                                    ?>
                                    <table class="tblRegistros2" style="margin-bottom: 15px">
                                        <?php
                                        if($c291==1 || $c292==1 || $c293==1 || $c294==1 || $c295==1 || $c296==1 || $c297==1 || $c298==1 || $c299==1 || $c300==1 || $c301==1 || $c302==1
                                        || $c303==1 || $c304==1 || $c305==1 || $c306==1 || $c307==1 || $c308==1 || $c309==1 || $c310==1 || $c311==1)
                                        {
                                            //if($pcoCco == ''){$pcoCco = 'off';} if($pcoGei == ''){$pcoGei = 'off';} if($pcoCdo == ''){$pcoCdo = 'off';}

                                            if($pcoCco == $param291 || $pcoGei == $param292 || $pcoCdo == $param293 || $pcoGte == $param294 || $pcoCin == $param295 || $pcoHca == $param296
                                            || $pcoCom == $param297 || $pcoIin == $param298 || $pcoCop == $param299 || $pcoMtr == $param300 || $pcoCre == $param301 || $pcoMco == $param302
                                            || $pcoEin == $param303 || $pcoMss == $param304 || $barNin == $param305 || $pcoSpa == $param306 || $pcoEca == $param307 || $pcoTra == $param308
                                            || $pcoFte == $param309 || $pcoVep == $param310 || $pcoGam == $param311)
                                            {
                                                ?>
                                                <tr align="center" style="background-color: #295DB1; color: white">
                                                    <td>Comité de Convivencia</td>                          <td>Gestión de la Información</td>          <td>Comité de Docencia</td>     <td>Gestión de Tecnología</td>
                                                    <td>Comité de Investigaciones</td>                      <td>Historia Clínica y Auditoría</td>       <td>Compras</td>                <td>Infecciones Intrahospitalarias</td>
                                                    <td>COPASST</td>                                        <td>Medicina Transfusional</td>             <td>Credencialización</td>      <td>Mejoramiento Continúo</td>
                                                    <td>Etica en Investigación</td>                         <td>Movilidad y Seguridad Sostenible</td>   <td>Etica Hospitalaria</td>     <td>Seguridad del paciente</td>
                                                    <td>Evaluación de la Calidad en la Atención Médica</td> <td>Transplantes</td>                       <td>Farmacia y Terapéutica</td> <td>Vigilancia Epidemiológica</td>
                                                    <td>Gestión Ambiental</td>
                                                </tr>
                                                <tr align="center">
                                                    <td><?php valServicio2($param291); ?></td>     <td><?php valServicio2($param292); ?></td>    <td><?php valServicio2($param293); ?></td>
                                                    <td><?php valServicio2($param294); ?></td>     <td><?php valServicio2($param295); ?></td>    <td><?php valServicio2($param296); ?></td>
                                                    <td><?php valServicio2($param297); ?></td>     <td><?php valServicio2($param298); ?></td>    <td><?php valServicio2($param299); ?></td>
                                                    <td><?php valServicio2($param300); ?></td>     <td><?php valServicio2($param301); ?></td>    <td><?php valServicio2($param302); ?></td>
                                                    <td><?php valServicio2($param303); ?></td>     <td><?php valServicio2($param304); ?></td>    <td><?php valServicio2($param305); ?></td>
                                                    <td><?php valServicio2($param306); ?></td>     <td><?php valServicio2($param307); ?></td>    <td><?php valServicio2($param308); ?></td>
                                                    <td><?php valServicio2($param309); ?></td>     <td><?php valServicio2($param310); ?></td>    <td><?php valServicio2($param311); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <script>
                                                    document.getElementById(<?php echo $row ?>).style.display = 'none';
                                                </script>
                                                <?php
                                            }
                                        }
                                        //SI NINGUNO ESTA SELECCIONADO, MOSTRAR TODOS LOS CAMPOS:
                                        elseif($c291!=1 and $c292!=1 and $c293!=1 and $c294!=1 and $c295!=1 and $c296!=1 and $c297!=1 and $c298!=1 and $c299!=1 and $c300!=1 and $c301!=1 and $c302!=1
                                           and $c303!=1 and $c304!=1 and $c305!=1 and $c306!=1 and $c307!=1 and $c308!=1 and $c309!=1 and $c310!=1 and $c311!=1)
                                        {
                                            ?>
                                            <tr align="center" style="background-color: #295DB1; color: white">
                                                <td>Comité de Convivencia</td>                          <td>Gestión de la Información</td>          <td>Comité de Docencia</td>     <td>Gestión de Tecnología</td>
                                                <td>Comité de Investigaciones</td>                      <td>Historia Clínica y Auditoría</td>       <td>Compras</td>                <td>Infecciones Intrahospitalarias</td>
                                                <td>COPASST</td>                                        <td>Medicina Transfusional</td>             <td>Credencialización</td>      <td>Mejoramiento Continúo</td>
                                                <td>Etica en Investigación</td>                         <td>Movilidad y Seguridad Sostenible</td>   <td>Etica Hospitalaria</td>     <td>Seguridad del paciente</td>
                                                <td>Evaluación de la Calidad en la Atención Médica</td> <td>Transplantes</td>                       <td>Farmacia y Terapéutica</td> <td>Vigilancia Epidemiológica</td>
                                                <td>Gestión Ambiental</td>
                                            </tr>
                                            <tr align="center">
                                                <td><?php valServicio2($param291); ?></td>     <td><?php valServicio2($param292); ?></td>    <td><?php valServicio2($param293); ?></td>
                                                <td><?php valServicio2($param294); ?></td>     <td><?php valServicio2($param295); ?></td>    <td><?php valServicio2($param296); ?></td>
                                                <td><?php valServicio2($param297); ?></td>     <td><?php valServicio2($param298); ?></td>    <td><?php valServicio2($param299); ?></td>
                                                <td><?php valServicio2($param300); ?></td>     <td><?php valServicio2($param301); ?></td>    <td><?php valServicio2($param302); ?></td>
                                                <td><?php valServicio2($param303); ?></td>     <td><?php valServicio2($param304); ?></td>    <td><?php valServicio2($param305); ?></td>
                                                <td><?php valServicio2($param306); ?></td>     <td><?php valServicio2($param307); ?></td>    <td><?php valServicio2($param308); ?></td>
                                                <td><?php valServicio2($param309); ?></td>     <td><?php valServicio2($param310); ?></td>    <td><?php valServicio2($param311); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                }
                                //SI NO TIENE DATOS PERTENENCIA A COMITES:
                                elseif($numDatoPerCom == 0)
                                {
                                    if($c291==1 || $c292==1 || $c293==1 || $c294==1 || $c295==1 || $c296==1 || $c297==1 || $c298==1 || $c299==1 || $c300==1 || $c301==1 || $c302==1
                                    || $c303==1 || $c304==1 || $c305==1 || $c306==1 || $c307==1 || $c308==1 || $c309==1 || $c310==1 || $c311==1)
                                    {
                                        ?>
                                        <script>
                                            document.getElementById(<?php echo $row ?>).style.display = 'none';
                                        </script>
                                        <?php
                                    }
                                    else
                                    {
                                        echo 'Sin Dato para PERTENENCIA A COMITÉS'.'<br>';
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $row = $row + 1;    //ENUMERADOR DE FILAS (cuando hay que eliminar alguna)
                    }
                    ?>
                </table>
        </div>
        <!--</div>-->
        <?php
    }
    ?>
</div>


<!------ FUNCIONES: -------->
<?php
function obtenerDescMuni($munResid,$conex)
{
    $queryMuni = "select Nombre from root_000006 WHERE Codigo = '$munResid'";
    $commitQryMuni = mysql_query($queryMuni, $conex) or die (mysql_errno()." - en el query: ".$queryMuni." - ".mysql_error());
    $datoMuni = mysql_fetch_assoc($commitQryMuni);
    $descMuni = $datoMuni['Nombre'];
    return $descMuni;
}

function obtenerDescBarr($munReside,$barRes,$conex,$desBarrio,$munRes)
{
    if($munRes != null)
    {
        $queryBarr = "select Bardes from root_000034 where Barmun = '$munReside' and Barcod = '$barRes'";
    }
    elseif($munRes == null)
    {
        $queryBarr = "select Bardes from root_000034 where Bardes LIKE '$desBarrio'";
    }

    $commitQryBarr = mysql_query($queryBarr, $conex) or die (mysql_errno()." - en el query: ".$queryBarr." - ".mysql_error());
    $datoBarr = mysql_fetch_assoc($commitQryBarr);
    $descBarrio = $datoBarr['Bardes'];
    return $descBarrio;
}

function obtenerEmpresa($emprBusca,$conex)
{
    $queryEmpresa = "select Empdes from root_000050 WHERE Empcod = '$emprBusca'";
    $commitEmpresa = mysql_query($queryEmpresa, $conex) or die (mysql_errno()." - en el query: ".$queryEmpresa." - ".mysql_error());
    $datoEmpresa = mysql_fetch_assoc($commitEmpresa);
    $empresa = $datoEmpresa['Empdes'];
    return $empresa;
}

function obtenerCargo($carBus,$conex)
{
    $queryCargo = "select Cardes from root_000079 WHERE Carcod = '$carBus'";
    $commitCargo = mysql_query($queryCargo, $conex) or die (mysql_errno()." - en el query: ".$queryCargo." - ".mysql_error());
    $datoCargo = mysql_fetch_assoc($commitCargo);
    $cargo = $datoCargo['Cardes'];
    return $cargo;
}

function obtenerCcosto($ccoBusca,$conex)
{
    $queryCco = "select Cconom from movhos_000011 WHERE Ccocod = '$ccoBusca'";
    $commitCco = mysql_query($queryCco, $conex) or die (mysql_errno()." - en el query: ".$queryCco." - ".mysql_error());
    $numRows = mysql_num_rows($commitCco);
    $datoCco = mysql_fetch_assoc($commitCco);
    $cCostos = $datoCco['Cconom'];

    if($numRows == 0)
    {
        $queryCco = "select Cconom from costosyp_000005 WHERE Ccocod = '$ccoBusca'";
        $commitCco = mysql_query($queryCco, $conex) or die (mysql_errno()." - en el query: ".$queryCco." - ".mysql_error());
        $datoCco = mysql_fetch_assoc($commitCco);
        $cCostos = $datoCco['Cconom'];
    }

    return $cCostos;
}

function obtenerCumple($fnacBusca)
{
    $fecNac = explode("-",$fnacBusca);
    $mesNac = $fecNac[1];   $diaNac = $fecNac[2];
    switch($mesNac)
    {
        case '01': $mesNac = 'Enero'; break;    case '02': $mesNac = 'Febrero'; break;      case '03': $mesNac = 'Marzo'; break;
        case '04': $mesNac = 'Abril'; break;    case '05': $mesNac = 'Mayo'; break;         case '06': $mesNac = 'Junio'; break;
        case '07': $mesNac = 'Julio'; break;    case '08': $mesNac = 'Agosto'; break;       case '09': $mesNac = 'Septiembre'; break;
        case '10': $mesNac = 'Octubre'; break;  case '11': $mesNac = 'Noviembre'; break;    case '12': $mesNac = 'Diciembre'; break;
    }

    $fechaCumple = $mesNac.' '.$diaNac;
    return $fechaCumple;
}

function obtenerEPS($codEps,$conex)
{
    $queryEps = "select Epscod,Epsnom from root_000073 WHERE Epsest = 'on' AND Epscod = '$codEps'";
    $commitQryEps = mysql_query($queryEps, $conex) or die (mysql_errno()." - en el query: ".$queryEps." - ".mysql_error());
    $datoEps = mysql_fetch_assoc($commitQryEps);

    $descEps = $datoEps['Epsnom'];

    return $descEps;
}

function convertValor($parametro)
{
    if($parametro == 'on'){$parametro = 'SI';}
    else{$parametro = 'NO';}
    echo $parametro;
}

function convertValor2($parametro)
{
    if($parametro == '1'){$parametro = 'PROVEEDOR PRINCIPAL DE RECURSOS ECONÓMICOS';}
    if($parametro == '2'){$parametro = 'COMPARTE CON SU CONYUGUE O PAREJA LAS RESPONSABILIDADES ECONOMICAS';}
    if($parametro == '3'){$parametro = 'CONTRIBUYE CON LOS GASTOS FAMILIARES';}
    if($parametro == '4'){$parametro = 'DEPENDIENTE ECONOMICAMENTE DE OTRO MIEMBRO DE LA FAMILIA';}
    if($parametro == '5'){$parametro = 'OTRO';}

    echo $parametro;
}

function convertValor3($parametro)
{
    if($parametro == '1'){$parametro = '5 - 30 minutos';}
    if($parametro == '2'){$parametro = '31 - 60 minutos (1 hora)';}
    if($parametro == '3'){$parametro = '61 - 180 minutos (1,5 horas)';}
    if($parametro == '4'){$parametro = 'mas de 180 minutos';}

    echo $parametro;
}

function convertValor4($parametro)
{
    if($parametro == 'D'){$parametro = 'DIURNO';}
    if($parametro == 'N'){$parametro = 'NOCTURNO';}
    if($parametro == 'M'){$parametro = 'MIXTO (Diurno y nocturno)';}

    echo $parametro;
}

function convertValor5($parametro)
{
    if($parametro == '1'){$parametro = 'Trabaja en otra empresa';}
    if($parametro == '2'){$parametro = 'Es docente';}
    if($parametro == '3'){$parametro = 'Es asesor';}
    if($parametro == '4'){$parametro = 'Es cuidador doméstico';}
    if($parametro == '5'){$parametro = 'Otras';}
    if($parametro == '6'){$parametro = 'Ninguna';}

    echo $parametro;
}

function convertValor6($parametro)
{
    if($parametro == '1'){$parametro = 'Menos de 1 SMMLV';}
    if($parametro == '2'){$parametro = '1 a 2 SMMLV';}
    if($parametro == '3'){$parametro = 'Hasta 4 SMMLV';}
    if($parametro == '4'){$parametro = 'Hasta 6 SMMLV';}
    if($parametro == '5'){$parametro = 'Más de 6 SMMLV';}

    echo $parametro;
}

function convertValor7($parametro)
{
    if($parametro == 'LV'){$parametro = 'DE LUNES A VIERNES';}
    if($parametro == 'S'){$parametro = 'SABADOS';}
    if($parametro == 'D'){$parametro = 'DOMINGOS';}
    if($parametro == '67'){$parametro = '06:00 am a 07:00 am';}
    if($parametro == '78'){$parametro = '07:00 am a 08:00 am';}
    if($parametro == '89'){$parametro = '08:00 am a 09:00 am';}
    if($parametro == '910'){$parametro = '09:00 am a 10:00 am';}
    if($parametro == '1011'){$parametro = '10:00 am a 11:00 am';}
    if($parametro == '1112'){$parametro = '11:00 am a 12:00 pm';}
    if($parametro == '1213'){$parametro = '12:00 pm a 01:00 pm';}
    if($parametro == '1415'){$parametro = '02:00 pm a 03:00 pm';}
    if($parametro == '1516'){$parametro = '03:00 pm a 04:00 pm';}
    if($parametro == '1617'){$parametro = '04:00 pm a 05:00 pm';}
    if($parametro == '1718'){$parametro = '05:00 pm a 06:00 pm';}
    if($parametro == '1819'){$parametro = '06:00 pm a 07:00 pm';}
    if($parametro == '1920'){$parametro = '07:00 pm a 08:00 pm';}

    echo $parametro;
}

function valServicio($parametro)
{
    switch($parametro)
    {
        case '01': $valor = 'SI'; break;
        case '02': $valor = 'SI'; break;
        case '03': $valor = 'SI'; break;
        case '04': $valor = 'SI'; break;
        case '05': $valor = 'SI'; break;
        case '06': $valor = 'SI'; break;
        case '07': $valor = 'SI'; break;
        case '08': $valor = 'SI'; break;
        default: $valor = 'NO'; break;
    }
    echo $valor;
}

function valServicio2($parametro)
{
    switch($parametro)
    {
        case '01': $valor = 'SI'; break;
        case '02': $valor = 'SI'; break;
        case '03': $valor = 'SI'; break;
        case '04': $valor = 'SI'; break;
        case '05': $valor = 'SI'; break;
        case '06': $valor = 'SI'; break;
        case '07': $valor = 'SI'; break;
        case '08': $valor = 'SI'; break;
        case '09': $valor = 'SI'; break;
        case '10': $valor = 'SI'; break;
        case '11': $valor = 'SI'; break;
        case '12': $valor = 'SI'; break;
        case '13': $valor = 'SI'; break;
        case '14': $valor = 'SI'; break;
        case '15': $valor = 'SI'; break;
        case '16': $valor = 'SI'; break;
        case '17': $valor = 'SI'; break;
        case '18': $valor = 'SI'; break;
        case '19': $valor = 'SI'; break;
        case '20': $valor = 'SI'; break;
        case '21': $valor = 'SI'; break;
        default: $valor = 'NO'; break;
    }
    echo $valor;
}

function valTenViv($variable,$conex)
{
    $queryTenViv = "select Tendes from root_000068 WHERE Tenest = 'on' AND Tencod = '$variable'";
    $commTenViv = mysql_query($queryTenViv, $conex) or die (mysql_errno()." - en el query: ".$queryTenViv." - ".mysql_error());
    $datoTenViv = mysql_fetch_assoc($commTenViv);
    $desTvivi = $datoTenViv['Tendes'];
    echo $desTvivi;
}

function valTipViv($variable,$conex)
{
    $queryTipViv = "select Tpvdes from root_000069 WHERE Tpvest = 'on' AND Tpvcod = '$variable'";
    $commTipViv = mysql_query($queryTipViv, $conex) or die (mysql_errno()." - en el query: ".$queryTipViv." - ".mysql_error());
    $datoTipViv = mysql_fetch_assoc($commTipViv);
    $desTipViv = $datoTipViv['Tpvdes'];
    echo $desTipViv;
}

function valEstViv($variable,$conex)
{
    $queryEstViv = "select Esvdes from root_000070 WHERE Esvest = 'on' AND Esvcod = '$variable'";
    $commEstViv = mysql_query($queryEstViv, $conex) or die (mysql_errno()." - en el query: ".$queryEstViv." - ".mysql_error());
    $datoEstViv = mysql_fetch_assoc($commEstViv);
    $desEstViv = $datoEstViv['Esvdes'];
    echo $desEstViv;
}
?>

<!------ JAVASCRIPT -->
<script>
    function limpiarEmp(valor)
    {
        //alert(valor);
        if(valor == '1')
        {
            document.getElementById('searchEmp').value = '';
            document.getElementById('searchEmp').disabled = true;
        }
        if(valor == '0')
        {
            document.getElementById('searchEmp').disabled = false;
        }
    }
</script>
</body>
</html>