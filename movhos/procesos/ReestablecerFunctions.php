<?php
ob_start();

include_once("conex.php");
include_once("root/comun.php");

$whisconsultada = $_POST['whisconsultada'];
$wingconsultada = $_POST['wingconsultada'];
$wemp_pmla = $_REQUEST['wemp_pmla'];

$conex = obtenerConexionBD("matrix");
$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
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

    $ing = $ingreso - 1;

    $query = "SELECT * FROM {$tabla} WHERE {$chistoria} = '{$historia}' AND {$cingreso} = '{$ing}' AND Tipo_egre_serv = 'ALTA'";
    $res = mysql_query($query, $conex);
    $num = mysql_num_rows($res);
    if ($num > 0){
        while ($row = mysql_fetch_assoc($res)) {
            array_push($historias, $row);
        }
    }

    return $historias;
}

function ingresoMasAltoMovhos33($historia, $tabla, $chistoria){
    global $conex;
    $historias = array();

    $ing = $ingreso - 1;

    $query = "SELECT MAX(Num_ingreso) as ingreso FROM {$tabla} WHERE {$chistoria} = '{$historia}' AND Tipo_egre_serv = 'ALTA'";
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

function ping_unix(){
    global $conex;
    global $wemp_pmla;

    $ret = false;

    $direccion_ipunix = consultarAliasPorAplicacion($conex, $wemp_pmla, "ipdbunix");
    if ($direccion_ipunix != "") {
        $cmd_result = shell_exec("ping -c 1 -w 1 " . $direccion_ipunix);
        $result = explode(",", $cmd_result);
        $recibidos = explode('=', $result[1]); // para windows
        if (preg_match('/(1 received)/', $result[1]) or $recibidos[1] > 1) {
            $ret = true;
        }
    }
    return $ret;
}

function verificarConexionUnix(){
    global $conex;
    global $wemp_pmla;
    $tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
    $ping_unix = ping_unix();
    $res = false;

    if($tieneConexionUnix == 'on' && $ping_unix ){
        $res = true;
    }
    
    return $res;
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

function obtenerDatosDeMatrix($historia, $ingreso){
    global $conex;
    global $wcliame;
    $fechaIngreso = '';

    $query = "SELECT Ingfei FROM {$wcliame}_000101 WHERE Inghis = '{$historia}' AND Ingnin = '{$ingreso}'";
    $res = mysql_query($query, $conex);
    $num = mysql_num_rows($res);
    if ($num > 0){
        while ($row = mysql_fetch_assoc($res)) {
            $fechaIngreso = $row['Ingfei'];
        }
    }

    return $fechaIngreso;
}

function obtenerDatosInpac( $historia, $ingreso ){

    $data = array();

    $conexionUnix = verificarConexionUnix();
    if ($conexionUnix == true) {

        $a = new admisiones_erp('traerDatosInpaci', $historia);
        $dataU = $a->data;

        if(!count($dataU['data'])){

            $a = new admisiones_erp('traerDatosInpac', $historia);
            $dataU = $a->data;
    
            $data = $dataU['data'];
    
            $fechaIngreso = obtenerDatosDeMatrix( $historia, $ingreso );
        
            array_push( $data, $fechaIngreso );
        
            $result['pachis'] = trim($data[0]);
            $result['pacced'] = trim($data[1]);
            $result['pactid'] = trim($data[2]);
            $result['pacnui'] = trim($data[3]);
            $result['pacap1'] = trim($data[4]);
            $result['pacap2'] = trim($data[5]);
            $result['pacnom'] = trim($data[6]);
            $result['pacsex'] = trim($data[7]);
            $result['pacnac'] = trim($data[8]);
            $result['pacnac'] = str_replace("-", "/", $result['pacnac']);
            $result['paclug'] = trim($data[9]);
            $result['pacest'] = trim($data[10]);
            $result['pacdir'] = trim($data[11]);
            $result['pactel'] = trim($data[12]);
            $result['paczon'] = trim($data[13]);
            $result['pacmun'] = trim($data[14]);
            $result['pactra'] = trim($data[15]);
            $result['pacniv'] = trim($data[16]);
            $result['pacpad'] = trim($data[17]);
            $result['pacing'] = trim($data[19]);
            $result['pacing'] = str_replace("-", "/", $result['pacing']);
            $result['pacnum'] = trim($data[18] - 1);
        
            $_POST['datosInpaci'] = $result;
            $a = new admisiones_erp('insertarEnInpaci');
            $dataU = $a->data;
            
            if ($dataU['data'] == true) {
                $IngresoInpaci = trim($data[18] - 1);
                autoIncremento_movhos_000033( $historia, $ingreso, $IngresoInpaci );
                $respuesta['error'] = 'no error';
                $respuesta['data'] = "$historia - $ingreso - $IngresoInpaci";
                $respuesta['mensaje'] = 'Insersion en inpaci ok';
                return $respuesta;
            }else{
                $respuesta['error'] = 'error';
                $respuesta['data'] = "$historia - $ingreso";
                $respuesta['mensaje'] = 'Error al insertar en inpaci';
                return $respuesta;
            }
        }else{
            $data = $dataU['data'];
            $IngresoInpaci = trim($data[19]);
            autoIncremento_movhos_000033( $historia, $ingreso, $IngresoInpaci );
            $respuesta['error'] = 'error';
            $respuesta['data'] = "$historia - $ingreso - $IngresoInpaci";
            $respuesta['mensaje'] = 'Historia ya existe en inpaci.';
            return $respuesta;
        }
    }else{
        $respuesta['error'] = 'error';
        $respuesta['data'] = "$historia - $ingreso";
        $respuesta['mensaje'] = 'No hay conexión a unix, intentar luego.';
        return $respuesta;
    }
}

function autoIncremento_movhos_000033( $historia, $ingreso, $IngresoInpaci ){
    global $conex;
    global $wmovhos;
    global $wcliame;
    global $wemp_pmla;

    $user = $_SESSION['user'];
    $usuario = explode("-", $user);

    $error = array();
    $consulta = consultarColumnasTabla('movhos', '_000033');
    array_pop($consulta);
    $columnasTabla = implode(', ', $consulta);

    
    $ingresoAltoMovhos33 = ingresoMasAltoMovhos33($historia, 'movhos_000033', 'Historia_clinica');

    if( $IngresoInpaci > $ingresoAltoMovhos33 ){
        while ( $ingresoAltoMovhos33 < $IngresoInpaci ) {
            $datos = obtener_datos($historia, $ingresoAltoMovhos33, 'movhos_000033', 'Historia_clinica', 'Num_ingreso');
            
            $ingresoAltoMovhos33++;
            $dataMovhos18 = obtenerFechaMovhos18( $historia, $ingresoAltoMovhos33 );
            $wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos" );
            
            if(count($datos) > 0){
                $query = "INSERT INTO movhos_000033 ({$columnasTabla})
                        VALUES ('{$datos[0]['Medico']}', '{$dataMovhos18[0][0]}', 
                        '{$dataMovhos18[0][1]}', '{$datos[0]['Historia_clinica']}', '{$ingresoAltoMovhos33}', 
                        '{$dataMovhos18[0][2]}', '{$datos[0]['Num_ing_serv']}', '{$dataMovhos18[0][0]}', 
                        '{$dataMovhos18[0][1]}', '{$datos[0]['Tipo_egre_serv']}', '{$datos[0]['Dias_estan_serv']}', 
                        'C-{$usuario[1]}')";
                $res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            }else{
                $query = "INSERT INTO movhos_000033 ({$columnasTabla})
                        VALUES ('{$wbasedatoMovhos}', '{$dataMovhos18[0][0]}', 
                        '{$dataMovhos18[0][1]}', '{$historia}', '1', '{$dataMovhos18[0][2]}', '1', '{$dataMovhos18[0][0]}', 
                        '{$dataMovhos18[0][1]}', 'ALTA', '0', 'C-{$usuario[1]}')";
                $res = mysql_query($query,$conex)  or ($descripcion = utf8_encode("Error: " . mysql_error()));
            }
        }
    }
}

$historias = HC_e_ingresos_de_Egresos_automaticos();

$result = array();
foreach ($historias as $historiaIngreso) {
    $res = obtenerDatosInpac( $historiaIngreso[0], $historiaIngreso[1] );
    // $res = obtenerDatosInpac( '836440', '4' );
    array_push( $result, $res);
}

ob_end_clean();
print_r(json_encode($result, JSON_UNESCAPED_SLASHES));

?>