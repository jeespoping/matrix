<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
include_once("../root/validator.php");
include_once("../root/requestResponse.php");
include_once("../root/authorization.php");

$wemp_pmla = "02";
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
$whce      = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wtalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");
$auth      = new Authorization();
$respuesta = array('message'=>'', 'status'=>0 );
$headers   = getallheaders();
$validador = new Validator();

//---> validar el token.
try{
    $auth->validateToken( $headers['Authorization'] );
}catch( Exception $e ){
    $respuesta['message'] = $e->getMessage();
    endRoutine( $respuesta, 401 );
}

if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){

    $arrayParametrosRequeridos = array(
        "historia",
        "ingreso",
        "codigoProcedimiento",
        "cedulaFuncionario",
        "fechaVisita",
        "horaVisita");

    $validador->validarParametros( $arrayParametrosRequeridos, $_GET );
    if( !$validador->valido ){
        $respuesta['message'] = "The required parameter: {$validador->faltante} was not provided ";
        endRoutine($respuesta,400);
    }
    consultarFormulariosFirmados();
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
    endRoutine( $respuesta, 400 );
}

function consultarFormulariosFirmados(){
    global $conex,
           $wbasedato,
           $respuesta,
           $wemp_pmla,
           $whce,
           $_GET;

    $codigoUsuario = consultarDatosUsario( $_GET['cedulaFuncionario'] );


    if( $codigoUsuario == "" ){
        $respuesta['message'] = " Documento de funcionario no encontrado ";
        endRoutine( $respuesta, 404 );
    }

    $formularios = consultarFormulariosPertinentes( $_GET['codigoProcedimiento'] );

    $query = "SELECT count(distinct(Firpro)) cantidadFirmados
                FROM {$whce}_000036
               WHERE Firhis = '{$_GET['historia']}'
                 AND Firing = '{$_GET['ingreso']}'
                 AND cast(concat(Fecha_data, ' ', Hora_data) as datetime) > cast(('{$_GET['fechaVisita']} {$_GET['horaVisita']}') as datetime)
                 AND Firusu = '{$codigoUsuario}'
                 AND Firpro in ( {$formularios['formsQuery']} )";

    $rs    = mysql_query( $query, $conex ) or die( respuestaErrorHttp() );
    $row   = mysql_fetch_assoc( $rs );
    $firmados = $row['cantidadFirmados'];

    if( $firmados >= $formularios['formsObligatorios'] ){
        $respuesta['message']   = true;
        endRoutine( $respuesta, 200 );
    }else{
        $restantes = $formularios['formsObligatorios'] - $firmados;
        $respuesta['message']   = false;
        $respuesta['result']    = "Faltan {$restantes} formularios por firmar";
        endRoutine( $respuesta, 200 );
    }
    return;
}

function consultarDatosUsario( $documento ){
    global $conex,
           $wtalhuma,
           $respuesta,
           $wemp_pmla;

    $usuario = "";
    $query = "SELECT Ideuse, Ideno1, Ideno2, Ideap1, Ideap2, Idecco
                FROM {$wtalhuma}_000013
               WHERE Ideced = '{$documento}'";
    $rs    = mysql_query( $query, $conex ) or die( respuestaErrorHttp() ) ;
    $row   = mysql_fetch_assoc( $rs );


    if( $row ){

        $opcionCodigo = explode( "-", $row['Ideuse'] );
        $opcion1      = $opcionCodigo[0];
        $opcion2      = $opcionCodigo[1].$opcionCodigo[0];

        $query2 = "SELECT Codigo
                     FROM usuarios
                    WHERE codigo in ( '{$opcion1}', '{$opcion2}' ) ";

        $rs2     = mysql_query( $query2, $conex ) or die( respuestaErrorHttp() );
        $row2    = mysql_fetch_assoc( $rs2 );
        $usuario = $row2['Codigo'];
    }
    return( $usuario );
}

function consultarFormulariosPertinentes( $codigoProcedimiento ){
    global $conex,
           $wtalhuma,
           $respuesta,
           $wemp_pmla,
           $wbasedato;
    $forPertinentes = array();
    $sinFormularios = true;

    $query = " SELECT Pmdcod, Pmdhce, Pmdfob, Pmdnom
                 FROM {$wbasedato}_000328
                WHERE pmdest = 'on'
                  AND pmdcod = '{$codigoProcedimiento}'";
    $rs    = mysql_query( $query, $conex ) or die( respuestaErrorHttp() );

    while( $row = mysql_fetch_assoc( $rs ) ){

        $sinFormularios = false;
        $formsAux       = $row['Pmdhce'];
        $formsAux2      = explode( ",", $formsAux );

        if( !isset( $forObligatorios[$row['Pmdcod']] ) ){
            $forObligatorios['formsCorrespondientes'] = array();
            $forObligatorios['formsCorrespondientes'] = $formsAux2;
            $forObligatorios['formsObligatorios']     = $row['Pmdfob']*1;
            $forObligatorios['todos']                 = ( $row['Pmdfob']*1 == count($formsAux2) ) ? true : false;
            $forObligatorios['nombre']                = $row['Pmdnom'];
        }

        foreach( $formsAux2 as $i=> $formularioInd ){

            if( !in_array( "'".$formularioInd."'", $forPertinentes ) )
                array_push( $forPertinentes, "'".$formularioInd."'" );
        }
    }
    if( $sinFormularios ){
        $respuesta['message'] = " El procedimiento no tiene formularios de historia clinica asociados ";
        endRoutine( $respuesta, 404 );
    }

    $forObligatorios['formsQuery'] = implode( ",", $forPertinentes );
    return( $forObligatorios );
}

?>