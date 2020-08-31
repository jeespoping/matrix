<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>REPORTES CARACTERIZACION EMPLEADOS - MATRIX</title>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk2.css" rel="stylesheet">-->
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link href="http://mtx.lasamericas.com.co/matrix/Library/Css/Bootstrap_v3.0.0.css" rel="stylesheet">
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/bootstrap_v3.0.0.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/jQuery_v1.11.1.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/Bootstrap_v3.1.0.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/GoogleCharts.js" type="text/javascript"></script>
    <link href="http://mtx.lasamericas.com.co/matrix/talento/procesos/carEmp_style.css" rel="stylesheet">
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
        <div class="panel-heading encabezado">
            <div class="panel-title">Matrix - Reportes Caracterización Empleados</div>
        </div>

        <form method="post">
            <div class="titulo"><label>IDENTIFICACION GENERAL:</label></div>
            <div id="idGeneral" class="input-group">
                <table align="center" border="0" style="width: 100%">
                    <tr>
                        <td>
                            <div class="input-group" style="margin-left: 10px">
                                <span class="input-group-addon input-sm"><label for="nombreEmpl">EMPLEADO:</label></span>
                                <input type="text" id="nombreEmpl" name="nombreEmpl" class="form-control form-sm">
                            </div>
                        </td>
                    </tr>
                </table>

                <label for="chViArr" class="container12">LISTO
                    <?php
                    if($varGAS1 == 'on'){?><input type="checkbox" id="chViArr" name="chViArr" checked="checked" value="on"
                                                  onclick="contOp('chViArr'); document.form[0].submit(true)"><?php }
                    else{?><input type="checkbox" id="chViArr" name="chViArr" value="off" onclick="this.form.submit()"><?php }
                    ?>
                    <span class="checkmark12"></span>
                </label>
            </div>
        </form>

        <!--
                <tr>
                    <td><label>FECHA DE NACIMIENTO:</label></td>
                    <td><label>GENERO:</label></td>
                    <td><label>NUMERO DE CEDULA:</label></td>
                    <td><label>CODIGO DE NOMINA:</label></td>
                    <td><label>TIENE PASAPORTE:</label></td>
                    <td><label>TIENE VISA:</label></td>
                </tr>
                <tr>
                    <td><?php datUserxEmp($codigoEmp, $conex, 1) ?></td>
                    <td><?php $Idegen = datUserxEmp($codigoEmp, $conex, 23);
        if ($Idegen == 'M') {
            $Idegen = 'Masculino';
        } else {
            $Idegen = 'Femenino';
        }
        echo $Idegen ?></td>
                    <td><?php datUserxEmp($codigoEmp, $conex, 3) ?></td>
                    <td><?php echo $searchEmp ?></td>
                    <td><?php $Idepas = datUserxEmp($codigoEmp, $conex, 5);
        echo $Idepas ?></td>
                    <td><?php $Idevis = datUserxEmp($codigoEmp, $conex, 6);
        echo $Idevis ?></td>
                </tr>
                <tr>
                    <td><label>ESTADO CIVIL:</label></td>
                    <td><label>ESTRATO:</label></td>
                    <td><label>LUGAR DE NACIMIENTO:</label></td>
                    <td><label>DIRECCION VIVIENDA:</label></td>
                    <td><label>MUNICIPIO DE RESIDENCIA:</label></td>
                    <td><label>BARRIO:</label></td>
                </tr>
                <tr>
                    <td><?php $Ideciv = datUserxEmp($codigoEmp, $conex, 9);
        if ($Ideciv == '01') {
            $Ideciv = 'Soltero(a)';
        }
        if ($Ideciv == '02') {
            $Ideciv = 'Casado(a)';
        }
        if ($Ideciv == '03') {
            $Ideciv = 'Union libre';
        }
        if ($Ideciv == '04') {
            $Ideciv = 'Separado(a)';
        }
        if ($Ideciv == '05') {
            $Ideciv = 'Divorciado(a)';
        }
        if ($Ideciv == '06') {
            $Ideciv = 'Viudo(a)';
        }
        echo $Ideciv
        ?>
                    </td>
                    <td><?php datUserxEmp($codigoEmp, $conex, 12) ?></td>
                    <td><?php datUserxEmp($codigoEmp, $conex, 11) ?></td>
                    <td><?php datUserxEmp($codigoEmp, $conex, 10) ?></td>
                    <td><?php $codMuni = datUserxEmp($codigoEmp, $conex, 13);    //OBTENER CODIGO MUNICIPIO
        $nombreMuni = datosMunicipio($codMuni, $conex);  //OBTENER NOMBRE MUNICIPIO
        echo $nombreMuni
        ?>
                    </td>
                    <td>
                        <?php $codBarrio = datUserxEmp($codigoEmp, $conex, 14);  //OBTENER CODIGO DEL BARRIO
        $nomBarrio = datosBarrio($codBarrio, $codMuni, $conex); //OBTENER NOMBRE DEL BARRIO SEGUN SU CODIGO
        echo $nomBarrio
        ?>
                    </td>
                </tr>
                <tr>
                    <td><label>NUMERO TELEFONICO:</label></td>
                    <td><label>CELULAR:</label></td>
                    <td><label>CORREO ELECTRONICO:</label></td>
                    <td><label>TIPO DE SANGRE:</label></td>
                    <td><label>EXTENSION:</label></td>
                    <td><label>CONTACTO DE EMERGENCIA:</label></td>
                </tr>
                <tr>
                    <td><?php datUserxEmp($codigoEmp, $conex, 15) ?></td>
                    <td><?php datUserxEmp($codigoEmp, $conex, 16) ?></td>
                    <td><?php datUserxEmp($codigoEmp, $conex, 17) ?></td>
                    <td><?php $tisanUser = datUserxEmp($codigoEmp, $conex, 18);
        echo $tisanUser ?></td>
                    <td><?php datUserxEmp($codigoEmp, $conex, 19) ?></td>
                    <td><?php datUserxEmp($codigoEmp, $conex, 25) ?></td>
                </tr>
                <tr>
                    <td><label>TELEFONO CONTACTO</label></td>
                    <td><label>PERTENENCIA ETNICA:</label></td>
                </tr>
                <tr>
                    <td><?php $telEmer = datUserxEmp($codigoEmp, $conex, 27);
        echo $telEmer ?></td>
                    <td><?php $Ideraz = datUserxEmp($codigoEmp, $conex, 26);
        echo $Ideraz ?></td>
                </tr>
                -->
        <!--
        <div class="panel panel-info" id="divPaso1">
            <h4 class="labelTitulo">Seleccion de parametros</h4>
            <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 70%; margin: 10px auto 10px auto">
                <form method="post" action="RepTalhuma_01.php">
                    <div class="input-group" style="margin: 10px auto 10px auto; width: 70%">
                        <span class="input-group-addon input-sm"><label for="searchEmp">EMPLEADO:</label></span>
                        <input type="text" id="searchEmp" name="searchEmp" class="form-control form-sm">
                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>
                        <span class="input-group-addon input-sm"><label for="searchAll">TODOS:</label></span>
                        <select id="searchAll" name="searchAll" class="form-control form-sm" onchange="limpiarEmp(this.value)">
                            <option value="1">SI</option>
                            <option value="0" selected>NO</option>
                        </select>
                    </div>
                    <div align="center" class="input-group" style="margin: auto auto 10px auto">
                        <input type="submit" id="submit1" name="submit1" class="btn btn-info btn-sm" value="> > >" title="Ejecutar">
                    </div>
                </form>
            </div>
        </div>
        -->

        <?php
        $searchEmp = $_POST['searchEmp'];   $searchAll = $_POST['searchAll'];
        //echo 'CODEMPL='.$searchEmp;

        if($searchAll == 0)
        {
            $queryCedTal = "select codEmp from talento_000008 where codEmp = '$searchEmp' AND estEmp = 'on'";
            $commQryCedTal = mysql_query($queryCedTal, $conex) or die (mysql_errno()." - en el query: ".$queryCedTal." - ".mysql_error());
        }
        if($searchAll == 1)
        {
            $queryCedTal = "select codEmp from talento_000008 where estEmp = 'on'";
            $commQryCedTal = mysql_query($queryCedTal, $conex) or die (mysql_errno()." - en el query: ".$queryCedTal." - ".mysql_error());
        }
        while($datoTal08 = mysql_fetch_assoc($commQryCedTal))
        {
        $codigoEmp = $datoTal08['codEmp'];
        $queryEmpresa = "select Empresa from usuarios where Codigo = '$codigoEmp'";
        $commQryEmpresa = mysql_query($queryEmpresa, $conex) or die (mysql_errno() . " - en el query: " . $queryEmpresa . " - " . mysql_error());
        $datoEmpresa = mysql_fetch_assoc($commQryEmpresa);
        $empresa = $datoEmpresa['Empresa'];
        $userOtrTbl = $codigoEmp . '-' . $empresa;
        ?>

            <!--
        <div class="titulo"><label>EDUCACION:</label></div>
        <div align="center"><label>ESTUDIOS SUPERIORES</label></div>
        <div id="idGeneral" class="input-group">
            <table align="center" border="0" style="width: 100%">
                <tr>
                    <td><label>GRADO ESCOLAR:</label></td>
                    <td><label>TITULO OBTENIDO:</label></td>
                    <td><label>NOMBRE DE LA INSTITUCION:</label></td>
                    <td><label>FECHA:</label></td>
                </tr>
                <?php
                $queryEduUser = "select * from talhuma_000014 WHERE Eduuse = '$userOtrTbl' AND Eduest = 'on'";
                $commitQryEduUser = mysql_query($queryEduUser, $conex) or die (mysql_errno() . " - en el query: " . $queryEduUser . " - " . mysql_error());

                while ($datoEduUser = mysql_fetch_array($commitQryEduUser))
                {
                    $Edugrd = $datoEduUser['Edugrd'];   $gradoEsc = datoEscolar($Edugrd, $conex);   $Edutit = $datoEduUser['Edutit'];
                    $Eduins = $datoEduUser['Eduins'];   $Eduani = $datoEduUser['Eduani'];           $idEstud = $datoEduUser['id'];
                    ?>
                    <tr>
                        <td><?php echo $Edugrd ?></td>
                        <td><?php echo $Edutit ?></td>
                        <td><?php echo $Eduins ?></td>
                        <td><?php echo $Eduani ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>

        <div align="center"><label>MANEJO DE OTROS IDIOMAS</label></div>
        <div id="idGeneral" class="input-group">
            <table align="center" border="0" style="width: 100%">
                <tr>
                    <td><label>IDIOMA</label></td>
                    <td><label>LO HABLA</label></td>
                    <td><label>LO LEE</label></td>
                    <td><label>LO ESCRIBE</label></td>
                </tr>
                <?php
                //QUERY PARA SELECCIONAR LOS REGISTROS DE IDIOMAS DEL USUARIO:
                $queryIdioma = "select * from talhuma_000015 WHERE Idiuse = '$userOtrTbl' AND Idiest = 'on'";
                $commitQryIdioma = mysql_query($queryIdioma, $conex) or die (mysql_errno() . " - en el query: " . $queryIdioma . " - " . mysql_error());

                while ($datoIdioma = mysql_fetch_array($commitQryIdioma))
                {
                    $IdiomaNom = $datoIdioma['Idides']; $idiomaHab = $datoIdioma['Idihab']; $idiomaLee = $datoIdioma['Idilee'];
                    $idiomaEsc = $datoIdioma['Idiesc']; $idiomaId = $datoIdioma['id'];

                    if($idiomaHab == 'on'){$idiomaHab = 'SI';} else{$idiomaHab = 'NO';}
                    if($idiomaLee == 'on'){$idiomaLee = 'SI';} else{$idiomaLee = 'NO';}
                    if($idiomaEsc == 'on'){$idiomaEsc = 'SI';} else{$idiomaEsc = 'NO';}
                    ?>
                    <tr>
                        <td><?php echo $IdiomaNom ?></td>
                        <td><?php echo $idiomaHab ?></td>
                        <td><?php echo $idiomaLee ?></td>
                        <td><?php echo $idiomaEsc ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
            -->
        <?php
        }
        ?>

    </div>

    <div class="input-group" style="margin-left: 10px">
        <div align="center" class="input-group" style="border: 1px solid #428BCA; width: 70%; margin: 10px auto 10px auto">
            <div align="center" class="input-group" style="margin: auto auto 10px auto">
                <input type="submit" id="submit1" name="submit1" class="btn btn-info btn-sm" value="> > >" title="Generar">
            </div>
        </div>
    </div>
</div>
<!------ FUNCIONES: -------->
<?php
function obtenerCodigosTal()
{

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