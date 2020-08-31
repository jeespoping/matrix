<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Registro Actualizado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/Estilos.css" rel="stylesheet">
    <style type="text/css"></style>
    <script src="js/js.js"></script>
    <script src="js/js2.js"></script>
    <script src="js/js3.js"></script>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
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

    $id_Reg = $_POST['id_Registro'];
    $id_Registro = trim($id_Reg);
    $caso = $_POST['descripcion'];
    $cas = $_POST['casoB'];
    $idCaso = $_POST['idCaso'];
    $dia = $_POST['dia_Registro'];
    $titulo = $_POST['titulo'];
    $responsable = $_POST['responsable'];
    $parametro = $_POST['selparam'];
    $palabraclave = $_POST['buscar'];
    ?>
</head>
<body onload="centrar()">
<div class="container" style="margin-top: -30px">
    <div id="loginbox" style="margin-top:40px;" class="">
        <div class="panel panel-info" >
            <div style="padding-top:30px" class="panel-body" >
                <?php
                actualizar($caso,$id_Registro,$cas,$idCaso,$dia,$titulo,$responsable,$parametro,$palabraclave)
                ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>