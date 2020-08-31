<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
include_once("../root/requestResponse.php");
include_once("../root/authorization.php");
include_once("../root/validator.php");

$wemp_pmla = "01";
$wmovhos   = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$auth      = new Authorization();
$validador = new Validator();
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

    $arrayParametrosRequeridos = array(
        "rqSource"
    );
    $validador->validarParametros( $arrayParametrosRequeridos, $_GET );
    if( !$validador->valido ){
        $respuesta['message'] = "The required parameter: {$validador->faltante} was not provided ";
        endRoutine($respuesta,400);
    }
    consultarCentroCostos( $_GET['rqSource'] );
    endRoutine( $respuesta );

}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){

        endRoutine( $respuesta, 400);

}

function consultarCentroCostos( $condicionCco ){
    global $conex,
           $wmovhos,
           $respuesta,
           $wemp_pmla;

    $encontroDatos = false;
    $ccos          = array();

    $query = "SELECT Ccocod, Cconom
                FROM {$wmovhos}_000011
               WHERE cco{$condicionCco} = 'on'
                 AND ccoest = 'on'";
    $rs    = mysql_query( $query, $conex ) or ( respuestaErrorHttp( "No data was found" ) );

    $aux = array( "code"=>"", "name"=>"" );
    while( $row = mysql_fetch_assoc( $rs ) ){

        $encontroDatos = true;
        $aux["code"]   = $row['Ccocod'];
        $aux["name"]   = $row['Cconom'];
        array_push( $ccos, $aux );
    }



    if( $encontroDatos ){
        $respuesta['message']  = " Data found successfully";
        $respuesta['result']   = $ccos;
        $respuesta['status']   = 200;
    }else{
        $respuesta['message']  = " No data was found ";
        $respuesta['status'] = 404 ;
    }
}

?>