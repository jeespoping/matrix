<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Encuesta de Satisfaccion Central de Esterilizacion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="EstilosEFR.css" rel="stylesheet">
    <style type="text/css"></style>
    <script src="jsEFR.js"></script>
    <script type="text/javascript">
        validarCampos()
    </script>
    <?php
include_once("conex.php");
    include_once("nomina/LibraryEFR.php");
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
<div class="container" style="margin-top: -30px; margin-left: 10px">
    <div id="loginbox" style="margin-top:50px;" class="">
        <div class="panel panel-info" style="width: 1200px">
            <div class="panel-heading">
                <div class="panel-title">Cuestionario  para conocer el nivel de socializacion de EFR</div>
            </div>

            <div style="padding-top:30px" class="panel-body" >

                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                </div>

                <form id="encuestaform" name="encuestaform" class="form-horizontal" role="form" method="post" action="encuestaefr.php">
                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span><label>Marque la opcion correcta</label></span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 1100px; text-align: start">1) QUE ES EFR?</label></span>
                        </div>
                        <div style="height: 1px; margin-top: 10px">
                            <span>
                                <label>a. Empresas felices responsables</label>
                                <input id="p1a" name="p1a" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar1_a();return false">
                                <br>
                                <label>b. Empresa familiarmente responsable</label>
                                <input id="p1b" name="p1b" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar1_b();return false">
                                <br>
                                <label>c. Entidad facilitadora de respeto</label>
                                <input id="p1c" name="p1c" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar1_c();return false">
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px; margin-top: 120px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 1100px; text-align: start">2) EFR BUSCA ....</label></span>
                        </div>
                        <div style="height: 1px; margin-top: 10px">
                            <span>
                                <label>a. Manejo de la vida personal</label>
                                <input id="p2a" name="p2a" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar2_a();return false">
                                <br>
                                <label>b. Equilibrio entre la vida personal, laboral y familiar</label>
                                <input id="p2b" name="p2b" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar2_b();return false">
                                <br>
                                <label>c. Ninguna de las anteriores</label>
                                <input id="p2c" name="p2c" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar2_c();return false">
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px; margin-top: 120px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 1100px; text-align: start">3) DE QUE AREA DEPENDE EL MANAGER EFR?</label></span>
                        </div>
                        <div style="height: 1px; margin-top: 10px">
                            <span>
                                <label>a. Registros medicos</label>
                                <input id="p3a" name="p3a" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar3_a();return false">
                                <br>
                                <label>b. Direccion Cientifica</label>
                                <input id="p3b" name="p3b" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar3_b();return false">
                                <br>
                                <label>c. Bienestar Laboral</label>
                                <input id="p3c" name="p3c" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar3_c();return false">
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px; margin-top: 120px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 1100px; text-align: start">4) LOS RESPONSABLES POR LA GERENCIA SON:</label></span>
                        </div>
                        <div style="height: 1px; margin-top: 10px">
                            <span>
                                <label>a. Comunicaciones y Servicio Magenta</label>
                                <input id="p4a" name="p4a" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar4_a();return false">
                                <br>
                                <label>b. Direccion Medica y Auditoria Corporativa</label>
                                <input id="p4b" name="p4b" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar4_b();return false">
                                <br>
                                <label>c. Direccion de Talento Humano y Talento Humano Clinica</label>
                                <input id="p4c" name="p4c" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar4_c();return false">
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px; margin-top: 120px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 1100px; text-align: start">5) SEÑALE DE ESTAS OPCIONES A TRAVES DE CUALES PUEDE EXPRESAR SUS COMENTARIOS</label></span>
                        </div>
                        <div style="height: 1px; margin-top: 10px">
                            <span>
                                <label>a. Buzones y correo electronico</label>
                                <input id="p5a" name="p5a" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar5_a();return false">
                                <br>
                                <label>b. Carta a la gerencia y memorandos</label>
                                <input id="p5b" name="p5b" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar5_b();return false">
                                <br>
                                <label>c. Telefonicamente y Boletin Hola Clinica</label>
                                <input id="p5c" name="p5c" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar5_c();return false">
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px; margin-top: 120px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 1100px; text-align: start">6) CONSIDERA QUE LAS MEDIDAS EFR DE LA CLINICA LAS AMERICAS BUSCAN LA CONCILIACION O EQUILIBRIO DE LA VIDA FAMILIAR,<br>PERSONAL Y LABORAL DE LOS COLABORADORES?</label></span>
                        </div>
                        <div style="height: 1px; margin-top: 10px">
                            <span>
                                <label>Si</label>
                                <input id="p6a" name="p6a" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar6_a();return false">
                                <br>
                                <label>No</label>
                                <input id="p6b" name="p6b" type="image" src="/matrix/images/medical/nomina/image1EFR.png" width="20" height="20" style="" value="0" onclick="seleccionar6_b();return false">
                            </span>
                        </div>
                    </div>

                    <div style="margin-top: 100px" class="form-group" align="center">
                        <!-- Button -->
                        <div class="col-sm-12 controls">
                            <input type="submit" class="btn btn-success" style="margin-left: 50px" value="GUARDAR" onclick="guardar(p1a,p1b,p1c,p2a,p2b,p2c,p3a,p3b,p3c,p4a,p4b,p4c,p5a,p5b,p5c,p6a,p6b)">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>