<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>HELPDESK - MATRIX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//matrixtest.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <style>
        body {overflow-x:hidden;}
        .alternar:hover
        {
            background-color: #D9EDF7;
        }
    </style>
    <script>
        function actualizar()
        {
            /*document.getElementById('divDatosConsulta').style.display = 'none';*/
            document.getElementById('datosActualizar').style.display = 'block';
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
    }
    $codUsuario = $_GET['codUsuario'];  $nomUsuario = $_GET['nomUsuario'];  $ccosUsuario = $_GET['ccosUsuario'];
    $carUsuario = $_GET['carUsuario'];  $ccosReq = $_GET['ccosReq'];        $tipoReq = $_GET['tipoReq'];
    $usuReq = $_GET['usuReq'];          $desReq = $_GET['desReq'];          $fecIniReq = $_GET['fecIniReq'];
    $horIniReq = $_GET['horIniReq'];    $fecFinReq = $_GET['fecFinReq'];    $horFinReq = $_GET['horFinReq'];
    $seguridad = 'C-'.$codUsuario;      $usuarioReq = $_GET['idUsuario'];

    $accion = $_GET['accion'];
    ?>
</head>

<body>
<?php
if($accion == 'guardar')
{
    mysql_queryV("INSERT INTO equipos_000006 (Medico,Fecha_data,Hora_data,Nomusu,Ccousu,Carusu,Ccoreq,Tipreq,Nomreq,Desreq,Fecini,Horini,Fecfin,Horfin,Seguridad)
                                     VALUES ('Equipos','$fecIniReq','$horIniReq','$nomUsuario','$ccosUsuario','$carUsuario','$ccosReq','$tipoReq','$usuReq','$desReq','$fecIniReq',
                                            '$horIniReq','$fecFinReq','$horFinReq','$seguridad') ");

    ?>
    <div style="margin-top: 100px; margin-left: -600px; text-align: center">
        <form method="post" action="helpDesk_01.php">
            <label style="color: #080808">Datos Almacenados Correctamente</label>
            <br><br>
            <input type="submit" class="text-success" value="ACEPTAR"/>
        </form>
    </div>
    <?php
}

if($accion == 'consultar')
{
    $queryReq = mysql_queryV("SELECT * from equipos_000006 WHERE Seguridad = '$usuarioReq' ORDER BY Fecini ASC");
    ?>
    <div id="datosConsultar" class="datosConsultar" style="width: 628px">
        <table align="center" border="0">
            <?php
            while($datoReq = mysql_fetch_array($queryReq))
            {
                $fecha = $datoReq[1];   $solicitante = $datoReq[8]; $uni = $datoReq[6];
                $tipo = $datoReq[7];    $descripcion = $datoReq[9];
                $queryCco = mysql_queryV("select Cconom from costosyp_000005 WHERE Ccocod = '$uni'");
                $datoCco = mysql_fetch_array($queryCco);
                $unidad = $datoCco[0];

                $descripcion = substr($descripcion, 0, 50)."...";
                ?>
                <tbody>
                <tr class="alternar" style="border-bottom: groove">
                    <td style="width: 50px">
                        <label style="font-weight: normal">
                            <a href="helpDeskProcesos.php" style="text-decoration: none" onclick="actualizar()"><?php echo $fecha ?></a>
                        </label>
                    </td>
                    <td style="width: 130px">&ensp;<label style="font-weight: normal"><?php echo $solicitante ?></label></td>
                    <td style="width: 130px">&ensp;<label style="font-weight: normal"><?php echo $unidad ?></label></td>
                    <td style="width: 100px">&ensp;<label style="font-weight: normal"><?php echo $tipo ?></label></td>
                    <td><label style="font-weight: normal"><?php echo $descripcion ?></label></td>
                </tr>
                </tbody>
                <?php
            }
            ?>
        </table>
    </div>

    <div id="datosActualizar" class="datosActualizar" style="width: 628px; display: none; border: dotted">
        <h3>Actualizar</h3>
    </div>
    <?php
}

function consultarDatosUsuario($codUsuario,$variable)
{
    $usua = str_replace("01","", $codUsuario);
    $usuario = $usua.'-01';

    $queryDatosUsuario = mysql_queryV("SELECT Ideced,Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg from talhuma_000013 WHERE Ideuse = '$usuario'");
    $datoUsuario = mysql_fetch_array($queryDatosUsuario);

    if($datoUsuario == null)
    {
        $usuario = $codUsuario.'-01';
        $queryDatosUsuario = mysql_queryV("SELECT Ideced,Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg from talhuma_000013 WHERE Ideuse = '$usuario'");
        $datoUsuario = mysql_fetch_array($queryDatosUsuario);
    }
    $documento = $datoUsuario[0];   $nombre1 = $datoUsuario[1];     $nombre2 = $datoUsuario[2];
    $apellido1 = $datoUsuario[3];   $apellido2 = $datoUsuario[4];   $cCoctos = $datoUsuario[5];
    $codCargo = $datoUsuario[6];
    $nombreCompleto = $nombre1.' '.$nombre2.' '.$apellido1.' '.$apellido2;

    $queryCargoUsuario = mysql_queryV("SELECT Cardes from root_000079 WHERE Carcod = '$codCargo'");
    $datoCargo = mysql_fetch_array($queryCargoUsuario);
    $descCargo = $datoCargo[0];

    switch($variable)
    {
        case 1: echo $nombreCompleto;
            break;
        case 2: echo $cCoctos;
            break;
        case 3: echo $descCargo;
            break;
    }
}
?>
</body>
</html>
