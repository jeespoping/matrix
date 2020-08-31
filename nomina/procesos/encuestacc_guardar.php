<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Registro Guardado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="estilos_encuestacc.css" rel="stylesheet">
    <style type="text/css"></style>
    <script src="js_encuestacc.js"></script>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
    <?php
include_once("conex.php");
    include_once("nomina/libreria_encuestacc.php");
    if(!isset($_SESSION['user']))
    {
        ?>
        <label>Usuario no autenticado en el sistema.</label>
        <br />
        <label>Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
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

    $comfama=$_GET['comfama'];$comfenalco=$_GET['comfenalco'];
    ?>
</head>
<body onload="centrar()">
    <?php
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');
    $seguridad = $wuse;
    guardar($fecha,$hora,$comfama,$comfenalco,$seguridad)
    ?>
</body>
</html>