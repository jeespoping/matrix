
<?php
include("conex.php");
include ("root/comun.php");
mysql_select_db("matrix");
?>
<!DOCTYPE html>
<html>
<head>
    <title>EMERGENCIES TESTING </title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8" async defer>
$(document).ready(function(){
    console.log(" the real slim shady please stand up");
});

function ejecutar( path ){
    console.log( path );
    window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
}
</script>
<body>
<?php
    $conex               = obtenerConexionBD("matrix");
    $wbasedato           = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $wcliame             = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
    $whce                = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
    $ccoRegistrosMedicos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoRegistrosMedicos');
    $pos                 = strpos($user,"-");
    $user                = $_SESSION['user'];
    $wuser               = explode("-",$user);

    $query =  "select Historia_clinica as historia, Num_ingreso as ingreso, Servicio as Ubisac, pactdo as Pactid, pacdoc as Pacced, fecha_egre_serv, Hora_egr_serv
                  from movhos_000033 a, cliame_000100
                 where a.fecha_data > '2019-02-01'
                   and tipo_egre_serv = 'ALTA'
                   and servicio = '1130'
                   and pachis   = Historia_clinica
                   and pacact   = 'on'";
    $rs    = mysql_query( $query, $conex );
?>
    <table>
            <tr>
                <th>historia</th>
                <th>ingreso</th>
                <th>egresar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while( $row = mysql_fetch_array($rs) ){
                $fechaAltDefinitiva = $row['fecha_egre_serv'];
                $horaAltaDefinitiva  = $row['Hora_egr_serv'];
                $destino = "/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']."&ccoEgreso={$row['Ubisac']}&fechaAltDefinitiva={$fechaAltDefinitiva}&horaAltDefinitiva={$horaAltaDefinitiva}&egresoUrgencias=on";
            ?>
                <tr>
                    <td><?=$row['historia']?></td>
                    <td><?=$row['ingreso']?></td>
                    <td><input type="radio" name="" value="" placeholder="egresar" onClick="ejecutar('<?=$destino?>')">
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>

</body>
</html>