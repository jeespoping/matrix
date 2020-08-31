<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Registro Guardado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="Estilos.css" rel="stylesheet">
    <style type="text/css"></style>
    <script src="js.js"></script>
    <script src="js2.js"></script>
    <script src="js3.js"></script>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
    <?php
include_once("conex.php");
    include_once("cenest/Library2.php");
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

    $f1_1=$_GET['f1-1'];$f1_2=$_GET['f1-2'];$f1_3=$_GET['f1-3'];$f1_4=$_GET['f1-4'];$f1_5=$_GET['f1-5'];
    $f2_1=$_GET['f2-1'];$f2_2=$_GET['f2-2'];$f2_3=$_GET['f2-3'];$f2_4=$_GET['f2-4'];$f2_5=$_GET['f2-5'];
    $f3_1=$_GET['f3-1'];$f3_2=$_GET['f3-2'];$f3_3=$_GET['f3-3'];$f3_4=$_GET['f3-4'];$f3_5=$_GET['f3-5'];
    $f4_1=$_GET['f4-1'];$f4_2=$_GET['f4-2'];$f4_3=$_GET['f4-3'];$f4_4=$_GET['f4-4'];$f4_5=$_GET['f4-5'];
    $f5_1=$_GET['f5-1'];$f5_2=$_GET['f5-2'];$f5_3=$_GET['f5-3'];$f5_4=$_GET['f5-4'];$f5_5=$_GET['f5-5'];
    $f6_1=$_GET['f6-1'];$f6_2=$_GET['f6-2'];$f6_3=$_GET['f6-3'];$f6_4=$_GET['f6-4'];$f6_5=$_GET['f6-5'];
    $cc=$_GET['cc'];$sugest=$_GET['sugest'];
    ?>
</head>
<body onload="centrar()">
    <?php
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');
    $seguridad = $wuse;
    guardar($fecha,$hora,$f1_1,$f1_2,$f1_3,$f1_4,$f1_5,$f2_1,$f2_2,$f2_3,$f2_4,$f2_5,$f3_1,$f3_2,$f3_3,$f3_4,$f3_5,$f4_1,$f4_2,$f4_3,$f4_4,$f4_5,$f5_1,$f5_2,$f5_3,$f5_4,$f5_5,$f6_1,$f6_2,$f6_3,$f6_4,$f6_5,$cc,$sugest,$seguridad)
    ?>
</body>
</html>