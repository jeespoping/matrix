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

    if ( isset( $_GET['id'] ) ){
        consultarDatosUsario( $_GET['id'] );
        endRoutine( $respuesta );

    } else {

        $respuesta['message'] = 'The required parameter was not provided';
        endRoutine( $respuesta, 400);
    }
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){

    if ( isset( $_POST['id'] ) ){
        consultarDatosUsario( $_POST['id'] );
        endRoutine( $respuesta );

    } else {

        $respuesta['message'] = 'The required parameter was not provided';
        endRoutine( $respuesta, 400);
    }

}

function consultarDatosUsario( $documento ){
    global $conex,
           $wbasedato,
           $wtalhuma,
           $respuesta,
           $wemp_pmla;

    $usuario = array();
    $query = "SELECT Ideuse, Ideno1, Ideno2, Ideap1, Ideap2, Idecco
                FROM {$wtalhuma}_000013
               WHERE Ideced = '{$documento}'";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_assoc( $rs );


    if( $row ){

        $opcionCodigo = explode( "-", $row['Ideuse'] );
        $opcion1      = $opcionCodigo[0];
        $opcion2      = $opcionCodigo[1].$opcionCodigo[0];

        $query2 = "SELECT Codigo
                     FROM usuarios
                    WHERE codigo in ( '{$opcion1}', '{$opcion2}' ) ";

        $rs2    = mysql_query( $query2, $conex );
        $row2   = mysql_fetch_assoc( $rs2 );
        $usuario['internalId'] = $row2['Codigo'];
        $usuario['userName']   = $row['Ideno1'].' '.$row['Ideno2'].' '.$row['Ideap1'].' '.$row['Ideap2'];
        $usuario['deparment']  = consultarCentroCostos( $conex, $wemp_pmla, $row['Idecco'] );
        $respuesta['result']   = $usuario;
        $respuesta['status']   = 200;
    }else{
        $respuesta['status'] = 404 ;
    }
}

function consultarCentroCostos( &$conex, $wemp_pmla, $centroCostos ){
    $centroCostosUsuario = "";
    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    if( $row = mysql_fetch_array($res) )
    {
        $tabla_CCO = $row['Emptcc'];
        $aux       = explode("_", $tabla_CCO );
        $aux       = $aux[1];
        switch ( $aux ) {
            case '000003':
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    $tabla_CCO AS tb1
                                WHERE   tb1.Ccocod IN ('{$centroCostos}')";
                break;
            case '000005':
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    $tabla_CCO AS tb1
                                WHERE   tb1.Ccocod IN ('{$centroCostos}')";
                break;

            default:
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    $tabla_CCO AS tb1
                                WHERE   tb1.Ccocod IN ('{$centroCostos}')";
        }

        if($result = mysql_query($query,$conex))
        {
            while($row = mysql_fetch_array($result))
            {
                $centroCostosUsuario = utf8_encode($row['nombre']);
            }
        }
    }
    return $centroCostosUsuario;
}

?>