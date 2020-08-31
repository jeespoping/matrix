<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Encuesta de Satisfaccion Central de Esterilizacion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="estilos_encuestacc.css" rel="stylesheet">
    <style type="text/css"></style>
    <script src="js_encuestacc.js"></script>
    <script type="text/javascript">
        validarCampos()
    </script>
    <?php
include_once("conex.php");
    include_once("nomina/libreria_encuestacc.php");
    if(!isset($_SESSION['user']))
    {
        ?>
        <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix; Inicie sesion nuevamente.</label>
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
    ?>
</head>
<body>
<?php verificar_guardado($wuse) ?>
<div class="container" style="margin-top: -30px">
    <div id="loginbox" style="margin-top:50px;" class="">
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">Encuesta Cajas de Compensacion</div>
            </div>

            <div style="padding-top:30px" class="panel-body" >

                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                </div>

                <form id="encuestaform" name="encuestaform" class="form-horizontal" role="form" method="post" action="encuestacc.php">
                    <div align="center">
                        <div style="margin-bottom: 25px" class="input-group">
                            <div class="input-group">
                                <span><label>De estas dos Cajas de Compensacion Familiar, marque cual prefiere:</label></span>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px" class="input-group">
                            <div class="input-group">
                                <input id="comfama" name="comfama" type="image" src="/matrix/images/medical/nomina/comf1.png" width="150" height="150" value="0" onclick="seleccionar1();return false" >
                                <div class="input-group-addon" style="background-color: #ffffff; width: 200px; border: none"></div>
                                <input id="comfenalco" name="comfenalco" type="image" src="/matrix/images/medical/nomina/comfe1.png" width="150" height="150" value="0" onclick="seleccionar2();return false" >
                            </div>
                        </div>

                        <div class="input-group" style="margin-left: 170px; margin-right: 130px">
                            <span class="input-group-addon"><label>COMFAMA</label></span>
                            <div class="input-group-addon" style="background-color: #ffffff; border: none"></div>
                            <span class="input-group-addon"><label>COMFENALCO</label></span>
                        </div>
                    </div>

                    <div style="margin-top:70px" class="form-group" align="center">
                        <!-- Button -->
                        <div class="col-sm-12 controls">
                            <input type="submit" class="btn btn-success" style="margin-left: 40px" value="GUARDAR" onclick="guardar(comfama,comfenalco)">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>