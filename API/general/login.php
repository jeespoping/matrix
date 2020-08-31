<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
include_once("../root/requestResponse.php");
include_once("../root/validator.php");
include_once("../root/authorization.php");

$wemp_pmla = "01";
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$wtalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");
$respuesta = array('message'=>'', 'result'=>array(), 'status'=>'' );
$validador = new Validator();
$auth      = new Authorization();



if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){

    $_POST      = json_decode(file_get_contents("php://input"));
    $_POST      = json_encode( $_POST );
    $_POST      = json_decode( $_POST, true );

    $arrayParametrosRequeridos = array(
        "userCode",
        "password");
    $validador->validarParametros( $arrayParametrosRequeridos, $_POST );

    if( !$validador->valido ){
        $respuesta['message'] = "The required parameter: '{$validador->faltante}' was not provided";
        endRoutine($respuesta, 400);
    }

    $user = validarUsuario( $_POST['userCode'], $_POST['password'] );

    if( $user === null ){
        $respuesta['message'] = "The user couldn't be found";
        endRoutine( $respuesta, 404 );
    }else{
        $respuesta['result']['token'] = $auth->getToken( $user );
        $respuesta['message'] = "The user was found successfully";
        endRoutine( $respuesta, 200 );
    }
}

function validarUsuario( $userCode, $password ){
    global $conex,
           $wbasedato,
           $wtalhuma,
           $respuesta,
           $wemp_pmla;

    $usuario = null;
    $query = "SELECT apucod, apunom, apuvni, aputex
                FROM root_000126
               WHERE apucod = '{$userCode}'
                 AND apupas = '{$password}'";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_assoc( $rs );


    if( $row ){
        $usuario                 = array();
        $usuario['userCode']     = $row['apucod'];
        $usuario['userName']     = $row['apunom'];
        $usuario['validateIP']   = $row['apuvni'];
        $usuario['tokenExpires'] = $row['aputex'];
    }
    return( $usuario );
}

?>