<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Registro Guardado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="estilosHabeas.css" rel="stylesheet">
    <style type="text/css"></style>
    <?php
include_once("conex.php");
    include_once("habeasdb/libreriaHabeas.php");
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

    $Fecha_data = $_POST['fecha'];
    $Tipo = $_POST['tipo'];
    $Descripcion = $_POST['descripcion'];
    $Nombre = $_POST['nombre'];
    $Cedula = $_POST['cedula'];
    $Datoscontacto = $_POST['contacto'];
    $Estado = $_POST['estado'];
    $Usuarioregistra = $_POST['usuarioRegistra'];
    ?>
</head>
<body onload="centrar()">
<?php
$Hora_data = date('H:i:s');
$seguridad = $wuse;
guardar($Fecha_data,$Hora_data,$Tipo,$Descripcion,$Nombre,$Cedula,$Datoscontacto,$Estado,$Usuarioregistra,$seguridad)
?>
</body>
</html>