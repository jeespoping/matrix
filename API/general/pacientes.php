<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
include_once("../root/requestResponse.php");
include_once("../root/authorization.php");

$wemp_pmla = "01";
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$wmovhos   = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wtalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");
$auth      = new Authorization();
$respuesta = array('message'=>'', 'result'=>array(), 'status'=>0 );
$headers   = getallheaders();

//---> validar el token.
try{
    $auth->validateToken( $headers['Authorization'] );
}catch( Exception $e ){
    $respuesta['message'] = $e->getMessage();
    endRoutine( $respuesta, 401 );
}

if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){

    $historia = ( isset( $_GET['patient_clinical_id'] ) and trim( $_GET['patient_clinical_id'] ) != ""  ) ? $_GET['patient_clinical_id'] : "";
    consultarPacientesActivos( $historia );
    endRoutine( $respuesta, $respuesta['status'] );
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){

    endRoutine( $respuesta, 400 );
}

function consultarPacientesActivos( $historia = "" ){
    global $conex,
           $wbasedato,
           $wtalhuma,
           $respuesta,
           $wemp_pmla;

    $pacientes     = array();
    $hayResultados = false;

    $condicionHistoria = ( $historia != "" ) ? " AND pachis = {$historia} " : "";

    $query = "SELECT Pactdo, Pacdoc, concat( Pacno1,' ', pacno2, ' ', pacap1, ' ', pacap2 ) as nombre, Paccor, Pachis
                FROM {$wbasedato}_000100
               WHERE pacact = 'on'
               {$condicionHistoria} ";
    $rs    = mysql_query( $query, $conex );

    while( $row   = mysql_fetch_assoc( $rs ) ){

        $hayResultados = true;
        $pacienteAux   = array();
        $pacienteAux['patient_id_Type']     = utf8_encode($row['Pactdo']);
        $pacienteAux['patient_id']          = utf8_encode($row['Pacdoc']);
        $pacienteAux['patient_clinical_id'] = utf8_encode($row['Pachis']);
        array_push( $pacientes, $pacienteAux );

    }


    if( $hayResultados ){
        $respuesta['result']   = $pacientes;
        $respuesta['status']   = 200;
    }else{
        $respuesta['message'] = "Paciente no encontrado";
        $respuesta['status'] = 404 ;
    }
    return;
}

?>