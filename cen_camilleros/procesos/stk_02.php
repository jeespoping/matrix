<html>
<head>
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
    }
    session_start();

    $numRadicado = $_GET['numRadicado'];
    $fechahoraRadicado = $_GET['fechahoraRadicado'];
    $usuRadica = $_GET['usuRadica'];
    $size = $_GET['size'];
    ?>
    <style>
        .left{
            float: left;
        }
    </style>
</head>

<?php
if($size == 1)
{
    ?>
    <body style="max-height: 10px">
    <div id="sticker" class="left" style="border: groove; width: 400px; margin-left: -5px" align="left">
        <table align="center" style="width: 400px">
            <thead>
            <tr>
                <td align="center"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="70"></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>&nbsp;<label style="font-weight: bold">Radicado N°:</label>&nbsp;<?php echo $numRadicado ?></td>
                <td rowspan="4" align="center"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="70"></td>
            </tr>
            <tr>
                <td>&nbsp;<label style="font-weight: bold">Fecha y Hora:</label>&nbsp;<?php echo $fechahoraRadicado?></td>
            </tr>
            <tr><td></td></tr>
            <tr align="center">
                <td>
                    <label style="font-weight: bold"><?php echo $usuRadica ?></label><br><label style="font-weight: bold">Administracion de Documentos</label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="display: none">
        <script>
            window.print();
            window.close();
        </script>
    </div>
    </body>
    <?php
}

elseif($size == 2)
{
    ?>
    <body style="max-height: 0px">
    <div id="sticker" class="left" style="border: groove; width: 400px; margin-left: 0px; margin-top: 0px" align="center">
        <table align="center" style="font-size: small; width: 400px">
            <tbody>
            <tr>
                <td align="center">&nbsp;<label style="font-weight: bold">Radicado N°:</label>&nbsp;<?php echo $numRadicado ?></td>
                <td rowspan="4" align="center"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="70"></td>
            </tr>
            <tr>
                <td align="center">&nbsp;<label style="font-weight: bold">Fecha:</label>&nbsp;<?php echo $fechahoraRadicado?></td>
            </tr>
            <tr><td></td></tr>
            <tr align="center">
                <td>
                    <label style="font-weight: bold; font-size: x-small"><?php echo $usuRadica ?></label>
                    <br>
                    <label style="font-weight: bold">Administracion de Documentos</label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="display: none">
        <script>
            window.print();
            window.close();
        </script>
    </div>
    </body>
    <?php
}
?>
</html>