<?php
ob_start();

include_once("conex.php");
include_once("root/comun.php");

$whisconsultada = $_POST['whisconsultada'];
$wingconsultada = $_POST['wingconsultada'];
$wemp_pmla = $_REQUEST['wemp_pmla'];

$conex = obtenerConexionBD("matrix");
$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
ob_end_clean();

function HC_e_ingresos_de_Egresos_automaticos(){
    global $conex;
    global $wcliame;
    $historias_ingresos = array();

    $query = "SELECT historia, ingreso FROM {$wcliame}_000343 WHERE egresado = 1 and ingresado = 1";
    $res = mysql_query($query, $conex);
    while ($row = mysql_fetch_assoc($res)) {
        array_push($historias_ingresos, array($row['historia'], $row['ingreso']));
    }
    return $historias_ingresos;
}

function obtener_datos($historia, $ingreso, $tabla, $chistoria, $cingreso){
    global $conex;
    $historias = array();

    $query = "SELECT * FROM {$tabla} WHERE {$chistoria} = '{$historia}' AND {$cingreso} = '{$ingreso}'";
    $res = mysql_query($query, $conex);
    $num = mysql_num_rows($res);
    if ($num > 0){
        while ($row = mysql_fetch_assoc($res)) {
            array_push($historias, $row);
        }
    }

    return $historias;
}

function ingresoMasAltoMovhos18( $historia ){
    global $conex;

    $query = "SELECT MAX(Ubiing*1) as ingreso FROM movhos_000018 WHERE Ubihis = '{$historia}'";
    $res = mysql_query($query, $conex);
    $num = mysql_num_rows($res);
    if ($num > 0){
        $row = mysql_fetch_assoc($res);
    }

    return $row['ingreso'];
}

function ingresoMasAltoHce22( $historia ){
    global $conex;

    $query = "SELECT MAX(Mtring*1) as ingreso FROM hce_000022 WHERE Mtrhis = '{$historia}'";
    $res = mysql_query($query, $conex);
    $num = mysql_num_rows($res);
    if ($num > 0){
        $row = mysql_fetch_assoc($res);
    }

    return $row['ingreso'];
}

function consultarColumnasTabla($tabla, $c_tabla){
    global $conex;
    $arrayColumnas = array();

    $query = "SHOW FIELDS FROM {$tabla}{$c_tabla}";
    $res = mysql_query($query, $conex);
    $num = mysql_num_rows($res);
    if ($num > 0){
        while( $info = mysql_fetch_array( $res ) ){
            array_push($arrayColumnas,$info['Field']); 
        }
    }
    return $arrayColumnas;
}

function obtenerFechaMovhos18($historia, $ingreso){
    global $conex;
    global $wcliame;
    $data = array();

    $query = "SELECT Fecha_data, Hora_data, Ubisac FROM movhos_000018 WHERE Ubihis = '{$historia}' AND Ubiing = '{$ingreso}'";
    $res = mysql_query($query, $conex);
    $num = mysql_num_rows($res);
    if ($num > 0){
        while ($row = mysql_fetch_assoc($res)) {
            array_push($data, array($row['Fecha_data'], $row['Hora_data'], $row['Ubisac']));
        }
    }

    return $data;
}

function autoIncremento_hce_000022( $historia ){
    global $conex;
    global $wmovhos;
    global $whce;
    global $wcliame;
    global $wemp_pmla;

    $user = $_SESSION['user'];
    $usuario = explode("-", $user);

    $error = array();
    $consulta = consultarColumnasTabla('hce', '_000022');
    array_pop($consulta);
    $columnasTabla = implode(', ', $consulta);

    
    $ingresoAltoMovhos18 = ingresoMasAltoMovhos18( $historia );
    $ingresoAltoHce22 = ingresoMasAltoHce22( $historia );

    if( $ingresoAltoMovhos18 > $ingresoAltoHce22 ){
        while ( $ingresoAltoMovhos18 > $ingresoAltoHce22 ) {
            $datos = obtener_datos($historia, $ingresoAltoHce22, 'hce_000022', 'Mtrhis', 'Mtring');
            
            $ingresoAltoHce22++;
            $dataMovhos18 = obtenerFechaMovhos18( $historia, $ingresoAltoHce22 );
            
            if(count($datos) > 0){
                $query = "INSERT INTO {$whce}_000022 ({$columnasTabla})
                    VALUES ('{$datos[0]['Medico']}', '{$dataMovhos18[0][0]}', '{$dataMovhos18[0][1]}', '{$datos[0]['Mtrhis']}', '{$ingresoAltoHce22}', '{$datos[0]['Mtrmed']}',
                    '{$datos[0]['Mtrest']}', '{$datos[0]['Mtrtra']}', '{$datos[0]['Mtreme']}', '{$datos[0]['Mtretr']}', '{$datos[0]['Mtrcon']}', '{$datos[0]['Mtrcur']}', '0000-00-00', 
                    '00:00:00', '0000-00-00', '00:00:00', '0000-00-00', '00:00:00', '{$datos[0]['Mtrtri']}', '{$dataMovhos18[0][2]}', 
                    '0000-00-00', '00:00:00', '{$datos[0]['Mtrcua']}', '{$datos[0]['Mtrsal']}', '{$datos[0]['Mtrccu']}', '0000-00-00', '00:00:00', 
                    '{$datos[0]['Mtrtur']}', '{$datos[0]['Mtrgme']}', '0000-00-00', '00:00:00', '{$dataMovhos18[0][0]}', '{$dataMovhos18[0][1]}', '{$datos[0]['Mtraut']}', 
                    '0000-00-00', '00:00:00', '{$datos[0]['Mtruau']}', 'C-{$usuario[1]}')";
                $res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            }else{
                $query = "INSERT INTO {$whce}_000022 ({$columnasTabla})
                    VALUES ('{$datos[0]['Medico']}', '{$fecha}', '{$hora}', '{$historia}', '1', '',
                    'on', 'off', '', '', '', 'off', '0000-00-00', '00:00:00', '0000-00-00', '00:00:00', '0000-00-00', '00:00:00', '', '{$dataMovhos18[0][2]}', 
                    '0000-00-00', '00:00:00', '', '', '', '0000-00-00', '00:00:00', '', '', '0000-00-00', '00:00:00', '{$fecha}', '{$hora}', '', 
                    '0000-00-00', '00:00:00', '', 'C-{$usuario[1]}')";
                $res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            }
        }
    }
}

$historias = HC_e_ingresos_de_Egresos_automaticos();

$result = array();
foreach ($historias as $historiaIngreso) {
    $res = autoIncremento_hce_000022( $historiaIngreso[0] );
    // $res = autoIncremento_hce_000022( '103654' );
    array_push( $result, $res);
}

ob_end_clean();
print_r(json_encode($result, JSON_UNESCAPED_SLASHES));

?>