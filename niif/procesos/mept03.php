<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        function copiarValor(nombre){
            opener.document.formNuevo.nombreconcepto.value = nombre;
            window.close();
        }
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
        $wactualiz = "1.0 11-abril-2017";
    }
    session_start();

    $codconcepto = $_GET['codconcepto'];
    $accion = $_GET['accion'];
    $codlinea = $_GET['codLinea'];
    ?>
</head>

<body>
<?php
if($accion == 1)
{
    $query_o2 = "SELECT temdes FROM intem WHERE temcod = '$codconcepto'";
    $err_o = odbc_do($conex_o, $query_o2);

    while($datoconcepto = odbc_fetch_row($err_o))
    {
        ?>
        <form>
            <table class="table">
                <tr>
                    <?php $descripcionconcepto=odbc_result($err_o, 1); ?>
                    <td><label id="lbldescripcionconcepto"><a href="#" ><?php echo $descripcionconcepto ?></a></label></td>
                    <script>copiarValor('<?php echo $descripcionconcepto ?>')</script>
                </tr>
            </table>
        </form>
        <?php
    }
}
?>
</body>
</html>