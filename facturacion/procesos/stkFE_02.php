<?php
include_once("conex.php");
?>
<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - Codigos de Facturas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <?php
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

    $numFactura = $_GET['numFactura'];
    $parametro = 1;
    ?>
</head>

<body style="height: 354px; max-height: 354px; width: 900px; border: none" onload="window.print();window.close();"> <!-- 437px = 3.70cm -->
<div id="divBarCode" style="border: none; text-align: center; margin-top: -50px">
    <table border="0" style="width: 90%; height: 320px">
        <tr>
            <td>
                <iframe src="barcodeFE.php?factura=<?php echo $numFactura?>&parametro=<?echo $parametro?>" frameborder="0" height="280px" width="60%" scrolling="no"></iframe>
            </td>
        </tr>
        <tr>
            <td>
                <div style="text-align: center; margin-top: -100px; margin-left: -80px; border: none">
                    <label style="font-size: 100px"><?php echo $numFactura ?></label>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>