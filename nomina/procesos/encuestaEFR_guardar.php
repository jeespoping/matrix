<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Registro Guardado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="EstilosEFR.css" rel="stylesheet">
    <style type="text/css"></style>
    <script src="jsEFR.js"></script>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
    <?php
include_once("conex.php");
    include_once("nomina/LibraryEFR.php");
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

    $p1a=$_GET['p1a'];$p1b=$_GET['p1b'];$p1c=$_GET['p1c'];
    $p2a=$_GET['p2a'];$p2b=$_GET['p2b'];$p2c=$_GET['p2c'];
    $p3a=$_GET['p3a'];$p3b=$_GET['p3b'];$p3c=$_GET['p3c'];
    $p4a=$_GET['p4a'];$p4b=$_GET['p4b'];$p4c=$_GET['p4c'];
    $p5a=$_GET['p5a'];$p5b=$_GET['p5b'];$p5c=$_GET['p5c'];
    $p6a=$_GET['p6a'];$p6b=$_GET['p6b'];
    ?>
</head>
<body onload="centrar()">
    <?php
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');
    $seguridad = $wuse;
    guardar($fecha,$hora,$p1a,$p1b,$p1c,$p2a,$p2b,$p2c,$p3a,$p3b,$p3c,$p4a,$p4b,$p4c,$p5a,$p5b,$p5c,$p6a,$p6b,$seguridad)
    ?>
</body>
</html>