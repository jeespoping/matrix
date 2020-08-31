<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Registro Guardado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="estilospaf.css" rel="stylesheet">
    <style type="text/css"></style>
    <?php
include_once("conex.php");
    include_once("paf/librarypaf.php");
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

    $habitacion=$_POST['habitacion'];               $hc=$_POST['hc'];   $ingreso=$_POST['ingreso'];         $nombre_pac=$_POST['nombre_pac'];               $servicio=$_POST['servicio'];
    $sexo=$_POST['sexo'];                           $fecha_ingreso=$_POST['fecha_ingreso'];                 $reingreso=$_POST['reingreso'];                 $dx=$_POST['dx'];
    $comorb=$_POST['comorb'];                       $qx=$_POST['qx'];                                       $fecha_egreso=$_POST['fecha_egreso'];           $fecha_paf=$_POST['fecha_paf'];
    $retiro_paf=$_POST['retiro_paf'];               $reintegro_paf=$_POST['reintegro_paf'];                 $retiro_paf2=$_POST['retiro_paf2'];             $prog_ambu=$_POST['prog_ambu'];
    $indicacion_cx=$_POST['indicacion_cx'];         $fecha_cx=$_POST['fecha_cx'];                           $cx1=$_POST['cx1'];$cx2=$_POST['cx2'];          $cx3=$_POST['cx3'];
    $fecha_reint=$_POST['fecha_reint'];             $reint1=$_POST['reint1'];                               $reint2=$_POST['reint2'];                       $reint3=$_POST['reint3'];
    $ingreso_uci=$_POST['ingreso_uci'];             $egreso_uci=$_POST['egreso_uci'];                       $reingreso_uci=$_POST['reingreso_uci'];         $egreso2_uci=$_POST['egreso2_uci'];
    $prog_hemo=$_POST['prog_hemo'];                 $indicacion_hemod=$_POST['indicacion_hemod'];           $fecha_hemod=$_POST['fecha_hemod'];             $interv_hemod=$_POST['interv_hemod'];
    $prog_electrof=$_POST['prog_electrof'];         $indicacion_electrof=$_POST['indicacion_electrof'];     $fecha_electrof=$_POST['fecha_electrof'];       $interv_electrof=$_POST['interv_electrof'];
    $iso=$_POST['iso'];                             $observacion=$_POST['observacion'];                     $nota=$_POST['nota'];                           $responsable=$_POST['responsable'];
    $fechaNac=$_POST['fechaNac'];                   $observacion2=$_POST['observacion2'];                   $nota2=$_POST['nota2'];                         $responsable2=$_POST['responsable2'];
    $alerta=$_POST['alerta'];                       $tipo_egreso=$_POST['tipo_egreso'];                     $indicacion_hemod2=$_POST['indicacion_hemod2']; $fecha_hemod2=$_POST['fecha_hemod2'];
    $interv_hemod2=$_POST['interv_hemod2'];         $indicacion_electrof2=$_POST['indicacion_electrof2'];   $fecha_electrof2=$_POST['fecha_electrof2'];     $interv_electrof2=$_POST['interv_electrof2'];
    $iaas=$_POST['iaas'];


    switch($responsable2)
    {
        case 'NUEVA E.P.S PROGRAMA CARDIOV':
            $responsable = '900156264CV';
            break;
        case 'EPS SURA PROGRAMA CARDIOVASCUL':
            $responsable = '800088702CV';
            break;
        case 'SALUD TOTAL EPS S PROG. CARDIO':
            $responsable = '800130907CV';
            break;
        default:
            $responsable = $responsable2;
    }

    ?>
</head>
<body onload="centrar()">
<?php
$fecha = date('Y-m-d');
$hora = date('H:i:s');
$fecha_ronda = date('Y-m-d');
$seguridad = $wuse;

$query=mysql_query("select hc, ingreso from paf_000004 where hc = '$hc' AND ingreso = '$ingreso'");
$dato=mysql_fetch_array($query);
if($dato[0] != null)
{
    actualizar($fecha,$hora,$fecha_ronda,$habitacion,$hc,$ingreso,$nombre_pac,$servicio,$sexo,$fecha_ingreso,$reingreso,$dx,$comorb,$qx,$fecha_egreso,$fecha_paf,$retiro_paf,
        $reintegro_paf,$retiro_paf2,$prog_ambu,$indicacion_cx,$fecha_cx,$cx1,$cx2,$cx3,$fecha_reint,$reint1,$reint2,$reint3,$ingreso_uci,$egreso_uci,
        $reingreso_uci,$egreso2_uci,$prog_hemo,$indicacion_hemod,$fecha_hemod,$interv_hemod,$prog_electrof,$indicacion_electrof,$fecha_electrof,$interv_electrof,$iso,
        $observacion,$nota,$responsable,$fechaNac,$seguridad,$observacion2,$nota2,$alerta,$tipo_egreso,$indicacion_hemod2,$fecha_hemod2,$interv_hemod2,$indicacion_electrof2,
        $fecha_electrof2,$interv_electrof2,$iaas);
}
else
{
    guardar($fecha,$hora,$fecha_ronda,$habitacion,$hc,$ingreso,$nombre_pac,$servicio,$sexo,$fecha_ingreso,$reingreso,$dx,$comorb,$qx,$fecha_egreso,$fecha_paf,$retiro_paf,
        $reintegro_paf,$retiro_paf2,$prog_ambu,$indicacion_cx,$fecha_cx,$cx1,$cx2,$cx3,$fecha_reint,$reint1,$reint2,$reint3,$ingreso_uci,$egreso_uci,
        $reingreso_uci,$egreso2_uci,$prog_hemo,$indicacion_hemod,$fecha_hemod,$interv_hemod,$prog_electrof,$indicacion_electrof,$fecha_electrof,$interv_electrof,$iso,
        $observacion2,$nota2,$responsable,$fechaNac,$alerta,$tipo_egreso,$indicacion_hemod2,$fecha_hemod2,$interv_hemod2,$indicacion_electrof2,$fecha_electrof2,
        $interv_electrof2,$iaas,$seguridad);
}
?>
</body>
</html>