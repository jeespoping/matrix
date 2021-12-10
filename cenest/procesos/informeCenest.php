<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- This file has been downloaded from Bootsnipp.com. Enjoy! -->
    <title>Detalle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="Estilos.css" rel="stylesheet" type="text/css">
    <link href="EstilosCenest.css" rel="stylesheet" type="text/css">
    <link href="css/Buttons.css" rel="stylesheet" type="text/css">
    <link href="css/Css1.css" rel="stylesheet" type="text/css">
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <style type="text/css"></style>
    <script src="js/js.js"></script>
    <script src="js/js2.js"></script>
    <script src="js3.js"></script>
    <script src="js/js4.js"></script>
    <script src="js/js5.js"></script>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
    <style>
        .alternar:hover{ background-color:#e1edf7;}
    </style>
    <?php
include_once("conex.php");
    include_once("cenest/Library2.php");
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
    $parametro1 = $_GET['parametro1'];
    if($parametro1 == null){$parametro1=$_POST['parametro1'];}
    $parametro2 = $_GET['parametro2'];
    if($parametro2 == null){$parametro2=$_POST['parametro2'];}
    $valor = $_GET['valor'];
    if($valor == null){$valor=$_POST['valor'];}
    $pregunta = $_GET['pregunta'];
    /*
    if($pregunta == null){$pregunta=$_POST['pregunta'];}
    */
    ?>
</head>

<body onload="window.resizeTo(1000,1000);">
<div id="container">
    <div id="loginbox" style="margin-top:1px;">
        <div id="panel-info">
            <div class="panel-heading">
                <div class="panel-title">
                    <br>
                    <label><?php echo 'Pregunta: '.$pregunta ?></label>
                    <br>
                    <h4>Calificacion: <?php echo $valor ?></h4>
                </div>
            </div>
            <div style="padding-top:5px" class="panel-body" >
                <form>
                    <table class="table" style="width: 950px">
                        <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                        <tr>
                            <td>Fecha de Registro</td>
                            <td>Diligenciada por</td>
                            <td>Centro de Costos</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $query=mysql_queryV("
                                            select *
                                            from cenest_000004
                                            WHERE $parametro1 = '1' OR $parametro2 = '1'
                                           ");
                        while($dato=mysql_fetch_array($query))
                        {
                            $cencosto=$dato['cc'];
                            $usuario=$dato['Seguridad'];

                            $query2=mysql_queryV("select Cconom from movhos_000011 where Ccocod = '$cencosto'");
                            $dato2=mysql_fetch_array($query2);
                            $centroCostos=$dato2['0'];

                            $query3=mysql_queryV("select Descripcion from usuarios where Codigo = '$usuario'");
                            $dato3=mysql_fetch_array($query3);
                            $usuarioDiligencia=$dato3['0'];
                            ?>
                            <form id="formDATOS" method="post" action="informeCenest.php">
                                <tr id="rowDATOS" class="alternar">
                                    <td><?php echo $dato['Fecha_data']?></td>
                                    <td><?php echo $usuarioDiligencia ?></td>
                                    <?php
                                    if($centroCostos != '')
                                    {
                                        ?>
                                        <td><?php echo $centroCostos ?></td>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <td><?php echo $cencosto ?></td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                            </form>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>