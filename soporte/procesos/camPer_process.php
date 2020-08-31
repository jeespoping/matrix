<?php
$accion = $_GET['accion'];  $fechaI = $_GET['fecIni'];  $fechaF = $_GET['fecFin'];
if($accion == null){$accion = $_POST['accion2'];}

if($accion == 'excel')
{
    header('Content-type: application/vnd.ms-excel; charset=UTF-8');
    header("Content-disposition: attachment; filename=retterce");
    header('Pragma: no-cache');
    header('Expires: 0');
    ?>
    <!DOCTYPE html>
    <html lang="esp" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <title>Reporte RETTERCE UNIX - Matrix</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        // /*
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
        }
        // */
        //include('../MATRIX/include/root/conex.php'); //publicacion local
        //include('../MATRIX/include/root/comun.php'); //publicacion local
        //mysql_select_db('matrix'); //publicacion local
        //$conex = obtenerConexionBD('matrix'); //publicacion local
        ?>
    </head>

    <body>
    <?php
    $query1 = "select * from equipos_000014";
    $commit = mysql_query($query1, $conex) or die (mysql_errno() . " - en el query: " . $query1 . " - " . mysql_error());
    ?>
    <table border="1">
        <tr align="center" style="font-weight: bolder">
            <td colspan="6">
                REPORTE DE TERCEROS - HONORARIOS
                <br>
                PERIODO: <?php echo $fechaI ?> AL <?php echo $fechaF ?>
            </td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #89CBF3">A&Ntilde;O</td>
            <td style="font-weight: bold; background-color: #89CBF3">MES</td>
            <td style="font-weight: bold; background-color: #89CBF3">TERCERO</td>
            <td style="font-weight: bold; background-color: #89CBF3">E/P</td>
            <td style="font-weight: bold; background-color: #89CBF3">VALOR_INGRESO</td>
            <td style="font-weight: bold; background-color: #89CBF3">TERCEROS_MARTA</td>
        </tr>
        <?php
        while ($resultado = mysql_fetch_array($commit))
        {
            $ano = $resultado[1];       $mes = $resultado[2];   $nit = $resultado[3];
            $empresa = $resultado[4];   $valor = $resultado[5]; $terMarta = $resultado[6];
            ?>
            <tr>
                <td><?php echo $ano ?></td>
                <td style="mso-number-format:'@'"><?php echo $mes ?></td>
                <td style="mso-number-format:'@'"><?php echo $nit ?></td>
                <td><?php echo $empresa ?></td>
                <td style="mso-number-format:'0.00'"><?php echo $valor ?></td>
                <td style="mso-number-format:'@'"><?php echo $terMarta ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}
?>