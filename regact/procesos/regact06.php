<?php
include_once("conex.php");
include_once("regact/regact02.php");
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

$id_Archivo = $_GET['id'];
$id_Registro = $_GET['id_Registro'];
$id_File = $_GET['id_File'];
$acccion = $_GET['accion'];

if($acccion == 1)
{
    ?>
    <html>
    <head>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
    </head>
    <?php
    $query = mysql_queryV("UPDATE regact_000002 SET estado = 'off' WHERE id = '$id_Archivo'");
    ?>
    <body onload="centrar()">
        <br><br><br>
        <div style="border-left: 100px">
            Archivo Eliminado!<br><br><input type="button" value="Aceptar" onClick="window.close()">
        </div>
    </body>
    </html>
    <?php
}
elseif($acccion == 2)
{
    ?>
    <html>
    <head>
        <script>
            function centrar() {
                iz=(screen.width-document.body.clientWidth) / 2;
                de=(screen.height-document.body.clientHeight) / 2;
                moveTo(iz,de);
            }
        </script>
    </head>
    <?php
    $query = mysql_queryV("UPDATE regact_000001 SET Estado = 'off' WHERE id = '$id_File'");
    ?>
    <body onload="centrar()">
    <br><br><br>
    <div style="border-left: 100px">
        Registro Eliminado!<br><br><input type="button" value="Aceptar" onClick="window.close()">
    </div>
    </body>
    </html>
    <?php
    //echo 'BORRAR ACTIVIDAD';
}
elseif($acccion != 1 or $acccion != 2)
{
    $id_Registro=$_POST['id_Registro'];
    $archivo = $_FILES["files"]["tmp_name"]; //nombre temporal del archivo
    $tamanio = $_FILES["files"]["size"];
    $tipo    = $_FILES["files"]["type"];
    $nombre  = $_FILES["files"]["name"];  //nombre del archivo
    $titulo  = $_POST["titulo"];

    $real= $_FILES['files']['name'];
    $files=$_FILES['files']['tmp_name'];
    $ruta="/var/www/matrix/images/medical/regact/";  //reemplazar regact por $grupo

    $dh=opendir($ruta);
    if(readdir($dh) == false)mkdir($ruta,0777);

    if (!isset($ruta) or !copy($files, $ruta.$real))
    {
        echo "ERROR LA COPIA NO PUDO HACERSE<br>";
    }
    else
    {
        echo "<table border=0 align=center>";
        echo "<tr><td align=center bgcolor=#DDDDDD>LA PUBLICACION EXITOSA</td></tr>";
        echo "</table>";
    }

    if ( $archivo != "none" )
    {
        $fp = fopen($archivo, "rb");
        $contenido = fread($fp, $tamanio);
        $contenido = addslashes($contenido);
        fclose($fp);

        $conex = obtenerConexionBD("matrix");
        $fecha = date('Y-m-d');
        $hora = date('H:i:s');

        $qry = "INSERT INTO regact_000002 (Medico,Fecha_data,Hora_data,Nombre_archivo,Contenido,Tipo,Id_registro,estado,Seguridad)
						VALUES ('regact','$fecha','$hora','$nombre','$ruta','$tipo','$id_Registro','on','$wuse')";

        mysql_query($qry);

        if(mysql_affected_rows($conex) > 0)
            print "Se ha guardado el archivo en la base de datos...";
        else
            print "NO se ha podido guardar el archivo en la base de datos.";
    }
    else
    {
        print "No se ha podido subir el archivo al servidor";
    }
    ?>
    <br><br><br>
    <center><a href="regact01.php">aceptar</a></center>
    <?php
}
?>

 