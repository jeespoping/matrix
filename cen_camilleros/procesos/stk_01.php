<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - Gestion de Stickers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <script>
        function mostrarop1(divBuscar,divBuscar2)
        {
            document.getElementById(divBuscar).style.display = 'block';
            document.getElementById(divBuscar2).style.display = 'none';
        }

        function mostrarop2(divBuscar,divBuscar2)
        {
            document.getElementById(divBuscar).style.display = 'none';
            document.getElementById(divBuscar2).style.display = 'block';
        }

        function ocultarPanel1()
        {
            document.getElementById('loginbox').style.display = 'none';
            document.getElementById('divInforme').style.display = 'none';
            document.getElementById('1').style.display = 'none';
        }

        function mostrarPanel()
        {
            document.getElementById('loginbox').style.display = 'block';
            document.getElementById('divInforme').style.display = 'block';
            document.getElementById('divCrear').style.display = 'none';
        }

        function mostrarPanel2()
        {
            document.getElementById('loginbox').style.display = 'block';
            document.getElementById('divInforme').style.display = 'none';
            document.getElementById('divCrear').style.display = 'none';
            document.getElementById('divQuery').style.display = 'none';
        }

        function ocultarPanel2()
        {
            document.getElementById('loginbox').style.display = 'block';
            document.getElementById('divInforme').style.display = 'block';
            document.getElementById('divCrear').style.display = 'none';
            document.getElementById('divQuery').style.display = 'none';
            document.getElementById('1').style.display = 'none';
        }

        function imprimir(numRadicado,fechahoraRadicado,usuRadica,size)
        {
            miPopup = window.open("stk_02.php?numRadicado="+numRadicado.value+"&fechahoraRadicado="+fechahoraRadicado.value+"&usuRadica="+usuRadica.value+"&size="+2,
                                  "miwin","width=500,height=200,scrollbars=no");
            miPopup.focus()
        }
    </script>
    <style>
        .alternar:hover{ background-color:#CADCFF;}
    </style>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker();
        });
    </script>
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
        $wactualiz = "1.0 12-julio-2017";
    }
    session_start();
    ?>
    <?php $fRadicado = $_POST['$fRadicado'] ?>
    <?php $nRadicado = $_POST['nRadicado'] ?>
    <?php $size = $_POST['size']; ?>
</head>

<body>
<div id="divEncabezado">
    <?php
    encabezado("<font style='font-size: x-large; font-weight: bold'>"."IMPRESION DE STICKERS"."</font>",$wactualiz,"clinica");
    ?>
</div>

<div id="container">
    <div id="loginbox" style="margin-top:1px; visibility: visible">
        <div id="panel-info">
            <div class="panel-heading">
            </div>
            <div style="padding-top:5px" class="panel-body" >
                <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="stk_01.php">
                    <table align="center" border="0">
                        <tr>
                            <td colspan="4" align="center">
                                <h5 class="text-primary"><strong>Parametro de busqueda: </strong></h5>
                            </td>
                        </tr>
                        <tr>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                            <td>
                                <div class="input-group">
                                    <input type="radio" checked name="selparam" id="selparam" value="0" onclick="mostrarop1('divBuscar','divBuscar2')">
                                    <label for="selparam">Por Numero de Radicado &nbsp;</label>
                                </div>
                            </td>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                            <td>
                                <div class="input-group">
                                    <input type="radio" name="selparam" id="selparam" value="1" onclick="mostrarop2('divBuscar','divBuscar2')">
                                    <label>Por Fecha &nbsp;</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div align="center" class="input-group" style="margin-left: 25px; width: 40%" id="divBuscar">
                                    <div class="input-group" style="margin: auto; border: none">
                                        <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                                        <span class="input-group-addon"><label>N° Radicado</label></span>
                                        <input id="habitacion" type="text" class="form-control" style="width: 200px" name="nRadicado" value="">
                                    </div>
                                </div>
                                <div align="center" class="input-group" style="display: none; margin-left: 20px; width: 40%" id="divBuscar2">
                                    <div class="input-group" style="margin: auto; border: none">
                                        <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                                        <span class="input-group-addon"><label>Fecha de Radicacion</label></span>
                                        <input id="datepicker1" type="text" class="form-control" style="width: 170px" name="fRadicado" value="">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="input-group-addon" style="background-color: #ffffff; border: none">
                                    <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                                </div>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="size" value="2">  <!-- Cambiar value = 1  si se requiere imprimir en etiqueta grande-->
                </form>
            </div>
        </div>
    </div>

    <!----Muestra el sticker con el numero de radicado digitado o recien creado: -------->

    <div align="center" id="divInforme" class="panel-body" style="padding-top: 1px">
        <?php $nRadicado = $_POST['nRadicado'] ?>
        <?php $size = $_POST['size'] ?>

        <form id="frmInforme" method="post" action="#">
            <?php
            if($nRadicado != null)
            {
                $queryRadicado = mysql_query("SELECT * from ameenv_000001 WHERE N_radicado = '$nRadicado'");
                $num_results = mysql_num_rows($queryRadicado);

                if($num_results > 0)
                {
                    ////////////// Etiqueta grande: ////////////
                    if($size == 1)
                    {
                        ?>
                        <br>
                        <div style="border: groove; width: 400px">
                            <table align="center" style="width: 400px">
                                <thead>
                                <tr>
                                    <td align="center"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                                </thead>
                                <tbody>
                                <?php
                                while($datoRadicado = mysql_fetch_array($queryRadicado))
                                {
                                    $numRadicado = $datoRadicado['N_radicado'];
                                    $fechRadicado = $datoRadicado['Fecha_data'];
                                    $horRadicado = $datoRadicado['Hora_data'];
                                    $us = $datoRadicado['Seguridad'];
                                    $usua = str_replace("01","", $us);
                                    $usuario = $usua.'-01';

                                    $queryUsuario = mysql_query("SELECT Ideno1,Ideno2,Ideap1,Ideap2 from talhuma_000013 WHERE Ideuse LIKE '$usuario'");
                                    $datoUsuario = mysql_fetch_array($queryUsuario);
                                    $usuRadica = $datoUsuario[0].' '.$datoUsuario[2].' '.$datoUsuario[3];
                                    ?>
                                    <tr>
                                        <td>&nbsp;<label>Radicado N°:</label>&nbsp;<?php echo $numRadicado ?></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;<label>Fecha y Hora:</label>&nbsp;<?php echo $fechRadicado.'  '.$horRadicado?></td>
                                    </tr>
                                    <tr><td></td></tr>
                                    <tr align="center">
                                        <td>
                                            <label><?php echo $usuRadica ?></label><br><label>Administracion de Documentos</label>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top: 20px">
                            <table>
                                <tr>
                                    <input type="hidden" name="numRadicado" value="<?php echo $numRadicado ?>">
                                    <input type="hidden" name="fechahoraRadicado" value="<?php echo $fechRadicado.'  '.$horRadicado ?>">
                                    <input type="hidden" name="usuRadica" value="<?php echo $usuRadica ?>"
                                    <td><input type="image" id="imprimir" title="IMPRIMIR" onclick="imprimir(numRadicado,fechahoraRadicado,usuRadica)" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25"></td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    }

                    ////////////// Etiqueta pequeña: ////////////
                    elseif($size == 2)
                    {
                        ?>
                        <br>
                        <div style="border: groove; width: 400px">
                            <table align="center" style="width: 400px">
                                <tbody>
                                <?php
                                while($datoRadicado = mysql_fetch_array($queryRadicado))
                                {
                                    $numRadicado = $datoRadicado['N_radicado'];
                                    $fechRadicado = $datoRadicado['Fecha_data'];
                                    $horRadicado = $datoRadicado['Hora_data'];
                                    $us = $datoRadicado['Seguridad'];
                                    $usua = str_replace("01","", $us);
                                    $usuario = $usua.'-01';

                                    $queryUsuario = mysql_query("SELECT Ideno1,Ideno2,Ideap1,Ideap2 from talhuma_000013 WHERE Ideuse LIKE '$usuario'");
                                    $datoUsuario = mysql_fetch_array($queryUsuario);
                                    $usuRadica = $datoUsuario[0].' '.$datoUsuario[2].' '.$datoUsuario[3];
                                    ?>
                                    <tr>
                                        <td>&nbsp;<label>Radicado N°:</label>&nbsp;<?php echo $numRadicado ?></td>
                                        <td rowspan="4" align="center"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;<label>Fecha y Hora:</label>&nbsp;<?php echo $fechRadicado.'  '.$horRadicado?></td>
                                    </tr>
                                    <tr><td></td></tr>
                                    <tr align="center">
                                        <td>
                                            <label><?php echo $usuRadica ?></label><br><label>Administracion de Documentos</label>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top: 20px">
                            <table>
                                <tr>
                                    <input type="hidden" name="numRadicado" value="<?php echo $numRadicado ?>">
                                    <input type="hidden" name="fechahoraRadicado" value="<?php echo $fechRadicado.'  '.$horRadicado ?>">
                                    <input type="hidden" name="usuRadica" value="<?php echo $usuRadica ?>">
                                    <input type="hidden" name="size" value="2">
                                    <td><input type="image" id="imprimir" title="IMPRIMIR" onclick="imprimir(numRadicado,fechahoraRadicado,usuRadica,size)" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25"></td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    }
                }
            }
            ?>
            <div align="center" style="margin-top: 30px">
                <input type="submit" name="btnNuevo" value="Nuevo">
            </div>
        </form>
    </div>

    <!--------------------------------- listar por fecha: ------------------------------->

    <div align="center" id="1" class="panel-body" style="padding-top: 1px">
        <?php $fRadicado = $_POST['fRadicado'] ?>
        <form id="frmInforme" method="post" action="stk_01.php">
            <?php
            if($fRadicado != null)
            {
                $queryRadicado = mysql_query("SELECT * from ameenv_000001 WHERE Fecha_data = '$fRadicado'");
                $num_results = mysql_num_rows($queryRadicado);

                if($num_results > 0) {
                    ?>
                    <table class="table" style="width: 600px">
                        <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                        <tr>
                            <td>Fecha de Radicacion</td>
                            <td>Hora de Radicacion</td>
                            <td>Numero de Radicado</td>
                            <td>Usuario que Radica</td>
                            <td></td>
                        </tr>
                        </thead>
                        <?php
                        while ($datoRadicado = mysql_fetch_array($queryRadicado)) {
                            ?>
                            <form method="post" action="stk_01.php">
                                <tbody>
                                <tr class="alternar">
                                    <td><?php echo $datoRadicado['Fecha_data'] ?></td>
                                    <td><?php echo $datoRadicado['Hora_data'] ?></td>
                                    <td><?php echo $datoRadicado['N_radicado'] ?></td>
                                    <td><?php echo $datoRadicado['Seguridad'] ?></td>
                                    <td><input type="image" id="btnVer" src="/matrix/images/medical/paf/selecPaf.png" width="20" height="20" title="Ver Sticker"></td>
                                    <input type="hidden" name="idSticker" value="<?php echo $datoRadicado['id'] ?>">
                                    <input type="hidden" name="fRadicado" value="<?php echo $datoRadicado['Fecha_data'] ?>">
                                    <input type="hidden" name="size" value="<?php echo $size ?>">
                                </tr>
                                </tbody>
                            </form>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                }
                else
                {
                    ?>
                    <div align="center">
                        <label>No se encontraron registros con el dato suministrado</label>
                    </div>
                    <?php
                }
            }
            ?>
        </form>
    </div>

    <!----------------------------------------------------------------------------------->

    <!-----------------se se seleccionó uno de los stickers listados arriba: ------------>

    <?php
    $idSticker = $_POST['idSticker'];
    $size = $_POST['size'];

    if($idSticker != null)
    {
        ?>
        <div align="center" id="2" class="panel-body" style="padding-top: 1px">
            <form id="frmInforme" method="post" action="#">
                <?php
                $queryRadicado = mysql_query("SELECT * from ameenv_000001 WHERE id = '$idSticker'");
                $num_results = mysql_num_rows($queryRadicado);

                if($num_results > 0)
                {
                    if($size == 1)
                    {
                        ?>
                        <br>
                        <div style="border: groove; width: 400px">
                            <table align="center" style="width: 400px">
                                <thead>
                                <tr>
                                    <td align="center"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                                </thead>
                                <tbody>
                                <?php
                                while($datoRadicado = mysql_fetch_array($queryRadicado))
                                {
                                    $numRadicado = $datoRadicado['N_radicado'];
                                    $fechRadicado = $datoRadicado['Fecha_data'];
                                    $horRadicado = $datoRadicado['Hora_data'];
                                    $us = $datoRadicado['Seguridad'];
                                    $usua = str_replace("01","", $us);
                                    $usuario = $usua.'-01';

                                    $queryUsuario = mysql_query("SELECT Ideno1,Ideno2,Ideap1,Ideap2 from talhuma_000013 WHERE Ideuse LIKE '$usuario'");
                                    $datoUsuario = mysql_fetch_array($queryUsuario);
                                    $usuRadica = $datoUsuario[0].' '.$datoUsuario[2].' '.$datoUsuario[3];
                                    ?>
                                    <tr>
                                        <td>&nbsp;<label>Radicado N°:</label>&nbsp;<?php echo $numRadicado ?></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;<label>Fecha y Hora:</label>&nbsp;<?php echo $fechRadicado.'  '.$horRadicado?></td>
                                    </tr>
                                    <tr><td></td></tr>
                                    <tr align="center">
                                        <td>
                                            <label><?php echo $usuRadica ?></label><br><label>Administracion de Documentos</label>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top: 20px">
                            <table>
                                <tr>
                                    <input type="hidden" name="numRadicado" value="<?php echo $numRadicado ?>">
                                    <input type="hidden" name="fechahoraRadicado" value="<?php echo $fechRadicado.'  '.$horRadicado ?>">
                                    <input type="hidden" name="usuRadica" value="<?php echo $usuRadica ?>"
                                    <input type="hidden" name="size" value="1">
                                    <td><input type="image" id="imprimir" title="IMPRIMIR" onclick="imprimir(numRadicado,fechahoraRadicado,usuRadica,size)" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25"></td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    }

                    elseif($size == 2)
                    {
                        ?>
                        <br>
                        <div style="border: groove; width: 400px">
                            <table align="center" style="width: 400px">
                                <tbody>
                                <?php
                                while($datoRadicado = mysql_fetch_array($queryRadicado))
                                {
                                    $numRadicado = $datoRadicado['N_radicado'];
                                    $fechRadicado = $datoRadicado['Fecha_data'];
                                    $horRadicado = $datoRadicado['Hora_data'];
                                    $us = $datoRadicado['Seguridad'];
                                    $usua = str_replace("01","", $us);
                                    $usuario = $usua.'-01';

                                    $queryUsuario = mysql_query("SELECT Ideno1,Ideno2,Ideap1,Ideap2 from talhuma_000013 WHERE Ideuse LIKE '$usuario'");
                                    $datoUsuario = mysql_fetch_array($queryUsuario);
                                    $usuRadica = $datoUsuario[0].' '.$datoUsuario[2].' '.$datoUsuario[3];
                                    ?>
                                    <tr>
                                        <td>&nbsp;<label>Radicado N°:</label>&nbsp;<?php echo $numRadicado ?></td>
                                        <td rowspan="4" align="center"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;<label>Fecha y Hora:</label>&nbsp;<?php echo $fechRadicado.'  '.$horRadicado?></td>
                                    </tr>
                                    <tr><td></td></tr>
                                    <tr align="center">
                                        <td>
                                            <label><?php echo $usuRadica ?></label><br><label>Administracion de Documentos</label>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top: 20px">
                            <table>
                                <tr>
                                    <input type="hidden" name="numRadicado" value="<?php echo $numRadicado ?>">
                                    <input type="hidden" name="fechahoraRadicado" value="<?php echo $fechRadicado.'  '.$horRadicado ?>">
                                    <input type="hidden" name="usuRadica" value="<?php echo $usuRadica ?>">
                                    <input type="hidden" name="size" value="2">
                                    <td><input type="image" id="imprimir" title="IMPRIMIR" onclick="imprimir(numRadicado,fechahoraRadicado,usuRadica,size)" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25"></td>
                                </tr>
                            </table>
                        </div>
                        <?php
                    }
                }

                else
                {
                    ?>
                    <div align="center">
                        <label>No se encontraron registros con el dato suministrado</label>
                    </div>
                    <?php
                }
                ?>
            </form>
        </div>
        <?php
    }

    ///////////////////////////////////////////////////////////////////////////////////////


    /////////////////////////////////Si se presionó el botón NUEVO: ///////////////////////

    if(isset($_POST['btnNuevo']))
    {
        $queryNumRadicado = mysql_query("SELECT N_radicado FROM ameenv_000001 ORDER BY id DESC");
        $datoNumRadicado = mysql_fetch_array($queryNumRadicado);
        $lastRadicado = $datoNumRadicado[0];
        //FUNCION PARA SUMAR 000001 AL NUMERO DE RADICADO ANTERIOR Y QUE EL RESULTADO SE MUESTRE CON TODOS LOS CEROS A LA IZQUIERDA:
        $adicion = 000001;
        $longitudActual = strlen($lastRadicado);
        $sumar = $lastRadicado+$adicion;
        $longitudFinal = strlen($sumar);
        $cantidadCeros = $longitudActual-$longitudFinal;
        $nextRadicado = str_repeat("0",$cantidadCeros).$sumar;
        ////////////////////////////////////////////////////
        ?>
        <script>ocultarPanel1()</script>
        <div id="divCrear" align="center">
            <br><br>
            <form method="post" action="stk_01.php">
                <table>
                    <thead>
                    <tr>
                        <th colspan="2" style="background-color: #C3D9FF">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;
                            <label>CREAR NUEVO STICKER</label></th>
                    </tr>
                    </thead>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <label>N. Radicado:</label>&nbsp;
                        </td>
                        <td>
                            <input type="text" name="nRadicado" value="<?php echo $nextRadicado ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Fecha:</label>
                        </td>
                        <td>
                            <input type="text" style="border: none" name="fRadicado" value="<?php echo date('Y-m-d') ?>" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Hora:</label>
                        </td>
                        <td>
                            <input type="text" style="border: none" name="hRadicado" value="<?php echo date('H:i:s') ?>">
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr align="center">
                        <td colspan="2">
                            <button type="submit" class="btn btn-success btn-xs" name="btnAceptar">Confirmar</button>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type="button" class="btn btn-primary btn-xs" name="btnCancelar" onclick="mostrarPanel()">Cancelar</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?php
    }

    //////////////Si se presionó ACEPTAR en la previsualizacion del nuevo sticker://///////
    /////////////Inserta los datos del nuevo sticker en la base de datos y ///////////////
    ///////////Genera la vista previa del nuevo sticker y abre la ventana de impresion:///

    if(isset($_POST['btnAceptar']))
    {
        $fechaRadicado = $_POST['fRadicado'];
        $horaRadicado = $_POST['hRadicado'];
        $numeroRadicado = $_POST['nRadicado'];

        ?>
        <script>ocultarPanel1()</script>
        <div align="center" id="divQuery">
            <?php
            $queryExiste = mysql_query("SELECT id FROM ameenv_000001 WHERE N_radicado = '$numeroRadicado'");
            $datoExiste = mysql_fetch_array($queryExiste);
            $existe = $datoExiste[0];

            if($existe == null)
            {
                $queryGuardarStk = mysql_query("INSERT INTO ameenv_000001(Medico,Fecha_data,Hora_data,N_radicado,Seguridad,id)
                                                                   VALUES('ameenv','$fechaRadicado','$horaRadicado','$numeroRadicado','$wuse','') ");
                if($queryGuardarStk)
                {
                    ?>
                    <script>ocultarPanel2()</script>
                    <form method="post" action="stk_01.php">
                        <input type="hidden" name="nRadicado" value="<?php echo $numeroRadicado ?>">
                        <input type="hidden" name="size" value="2">
                        <br><br><br>
                        <button class="btn btn-success btn-xs" onclick="mostrarPanel2()" name="btnExito" id="btnExito">Proceso Exitoso</button>
                    </form>
                <?php
                }
                else
                {
                    ?>
                    <label>No se ejecutó la solicitud</label>
                    <?php
                }
            }
            else
            {
                ?>
                <label>No se ejecutó la solicitud, EL NUMERO DE RADICADO DIGITADO YA EXISTE</label>
                <br>
                    <button class="btn btn-success btn-xs" onclick="mostrarPanel2()" name="btnExito" id="btnExito">ACEPTAR</button>
                <?php
            }
            ?>
        </div>
        <?php
    }
    ?>
</div>
</body>
</html>