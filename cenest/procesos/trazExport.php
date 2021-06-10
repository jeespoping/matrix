<?php
$fecIniReporteAUX = $_REQUEST['fecIniExcel'];    
$fecFinReporteAUX = $_REQUEST['fecFinExcel'];  
$filtroCco = $_REQUEST['CcounidadExcel'];
$filtroDispo = $_REQUEST['DispoExcel'];
$filtroCodReu = $_REQUEST['CodReuExcel'];
$accion = $_REQUEST['accion'];
$wemp_pmla = $_REQUEST['wemp_pmla'];
// headers para que pueda funcionar el exporte a excel
header('Content-type: application/vnd.ms-excel; charset=UTF-8');
header("Content-disposition: attachment; filename=trazabilidad_{$fecIniReporteAUX}_{$filtroCco}.xls");
header('Pragma: no-cache');
header('Expires: 0');
$fecIniReporte = $fecIniReporteAUX;
$fecFinReporte = $fecFinReporteAUX;
?>
<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Reporte de Reuso de Dispositivos Medicos - Matrix</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    // /*
    ob_start();
    include_once("conex.php");
    include_once("trazFunctions.php");
    ob_end_clean();
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
        ob_start();
        include_once("root/comun.php");
        ob_end_clean();
        $conex = obtenerConexionBD("matrix");
    }
    $bdCenest = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenest');
    ?>
</head>

<body>
<?php

if($fecIniReporteAUX =! null && $fecFinReporteAUX =! null)
{   
    if ($filtroCco != 'TODOSCCO' && $filtroCco != null){
        $conteoReCard = existeReuso($filtroCco,$conex,$fecIniReporte,$fecFinReporte);
        $adQuery = "C.Ccostos = '$filtroCco' AND ";
        if ($filtroDispo != 'TODOSDISPO' && $filtroDispo != null){
            $adQuery .= " A.Coddispo = '$filtroDispo' AND ";
            if ($filtroCodReu != 'TODOSREUSOS' && $filtroCodReu != null){
                list($codReusoaux,$idReuso) = explode("_",$filtroCodReu);
                $adQuery .= " A.id = '$idReuso' AND ";
            }
        }
    }else {
        $conteoReCard = 1;
    }
    if ($conteoReCard > 0){
        $q = "SELECT C.* , A.* , C.Observacion AS Observacion2
         FROM {$bdCenest}_000014 C 
         INNER JOIN {$bdCenest}_000012 A
                ON C.Codigo = A.id  
         WHERE {$adQuery} C.Fechauso BETWEEN '$fecIniReporte' AND '$fecFinReporte' ORDER BY C.Fechauso ASC";
        $res = mysql_query($q, $conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        if (mysql_num_rows($res) > 0){
            ?>
                <table border="1">
                    <tr align="center" style="font-weight: bolder">
                        <td colspan="14">
                            REPORTE DE REUSO DE DISPOSITIVOS MEDICOS
                            <br>
                            PERIODO: <?php echo $fecIniReporte ?> AL <?php echo $fecFinReporte ?>
                        </td>
                    </tr>
                    <tr style="font-weight: bold; background-color: #89CBF3">
                        <td>SERVICIO</td>               <td>FECHA DE UTILIZACION</td>   <td>CODIGO DISPOSITIVO MEDICO</td>          <td>CALIBRE</td>
                        <td>INVIMA</td>                 <td>NUMERO DE USOS</td>         <td>NOMBRE DE USUARIO</td>                  <td>NUMERO DE IDENTIFICACION</td>
                        <td>NUMERO QUIROFANO</td>       <td>OBSERVACIONES</td>          <td>FUNCIONARIO ENTREGA ESTERILIZACION</td> <td>FUNCIONARIO RECIBE EN ESTERILIZACION</td>
                        <td>FECHA ESTERILIZACION</td>   <td>EQUIPO ESTERILIZADOR</td>   <td>METODO DE ESTERILIZACION</td>           <td>CICLO DE ESTERILIZACION</td>
                        <td>NOVEDAD DISPOSITIVO</td> <td>RESPONSABLE DE ESTERILIZACION</td>  <td>RESPONSABLE DE DILIGENCIAMIENTO</td>
                    </tr>
            <?php
            while($resultado = mysql_fetch_array($res)){
                $codigo = $resultado['Codigo'];
                $costo13 = $resultado['Ccostos'];       $fechauso = $resultado['Fechauso'];         $codigo = $resultado['Codreuso'];     $calibre = $resultado['Ncalibre'];
                $invima = $resultado['Invima'];         $numUsos = $resultado['Numuso'];    $nomUsu = htmlentities($resultado['Nomusuario']);         $numId = $resultado['Docusuario'];
                $numQui = $resultado['NumQuirofano'];   $observa = $resultado['Observacion2'];            $usrecibe = htmlentities(datosUsrMtx2($resultado['UserEsteril'],$conex));
                $fEster = $resultado['FechaEsteril'];   $eqEster = $resultado['EquipoEsteril'];     $metEster = $resultado['MetodoEsteril'];   $cicEster = $resultado['CicloEsteril'];
                $rEster = $resultado['RespEsteril'];    $respDil = $resultado['RespDiligen'];       $novDispo = $resultado['novDispo'];
                $rEster = htmlentities(datosUsrMtx2($rEster,$conex));  $respDil = htmlentities(datosUsrMtx2($respDil,$conex));
                $funEntre = htmlentities(datosUsrMtx2($resultado['UserServicio'],$conex));
                ?>
                <tr>
                    <td><?=datosUnidadxCco2($costo13,$conex,1)?></td>   <td><?php echo $fechauso ?></td>    <td><?php echo $codigo ?></td>  <td><?php echo $calibre ?></td>
                    <td><?php echo $invima ?></td>                          <td><?php echo $numUsos ?></td>     <td><?php echo $nomUsu ?></td>  <td><?php echo $numId ?></td>
                    <td><?php echo $numQui ?></td>                          <td><?php echo $observa ?></td>     <td><?php echo $funEntre ?></td><td><?php echo $usrecibe ?></td>
                    <td><?php echo $fEster ?></td>                          <td><?php echo $eqEster ?></td>     <td><?php echo $metEster ?></td><td><?php echo $cicEster?></td>
                    <td><?=$novDispo?></td>                          <td><?php echo $rEster ?></td>      <td><?php echo $respDil ?></td>
                </tr>
                <?php
            }
                ?>
            </table>
            <?php
        

        }
    }
}
 
/*******************************
 * Funcion que trae los datos del dispositivo de reuso
 */
function datosReuso2($codReu13,$conex,$parametro)
{
    global $bdCenest;
    $query22 = "select * from {$bdCenest}_000012 WHERE id = '$codReu13'";
    $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno()." - en el query: ".$query22." - ".mysql_error());
    $datoReuso = mysql_fetch_array($commitQuery22);
    $Codreuso = $datoReuso['Codreuso']; $calibre = $datoReuso['Ncalibre'];  $invima = $datoReuso['Invima'];
    $numUsos = $datoReuso['Numuso'];

    switch($parametro)
    {
        case 1: return $Codreuso; break;
        case 2: return $calibre; break;
        case 3: return $invima; break;
        case 4: return $numUsos; break;
    }
}

/*******************************
 * funcion que retorna el nombre del usuario por codigo de matrix
 */
function datosUsrMtx2($funEnt13,$conex)
{
    $query22 = "select * from usuarios WHERE Codigo = '$funEnt13'";
    $commitQuery22 = mysql_query($query22, $conex) or die (mysql_errno()." - en el query: ".$query22." - ".mysql_error());
    $datoUsuario = mysql_fetch_array($commitQuery22);
    $nomUsrMtx = $datoUsuario['Descripcion'];
    if ($nomUsrMtx != '')return $nomUsrMtx;
    else return $funEnt13;
}
?>
</body>
</html>
