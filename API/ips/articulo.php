<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
include_once("../root/requestResponse.php");
include_once("../root/validator.php");
include_once("../root/authorization.php");

$wemp_pmla              = "01";
$wbasedato              = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$wmovhos                = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$bd                     = $wmovhos;
$fechaActual            = date('Y-m-d');
$horaActual             = date('H:i:s');
$conceptoIngreso        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'conceptoIngresoInventario');
$conceptoTraslado       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'conceptoTrasladoInventario');
$servicioIngreso        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoIngresoXconsignacion');
$servicioTrasladosCons  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoTrasladosXconsignacion');
$fuenteEntXconsignacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fuenteEntradasXconsignacion');
$fuenteTrasladosConsign = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fuenteTrasladosXconsignacion');
$fuenteOrdenCompra      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fuenteOrdenesCompra');
$respuesta              = array('message'=>'', 'result'=>array(), 'status'=>'' );
$hay_unix               = consultarUnixAccesible();
$validador              = new Validator();
$auth                   = new Authorization();
$headers                = getallheaders();
$userPl                 = null;//--> userpayload
if( $hay_unix ){
  $conex_o = @odbc_pconnect('inventarios','informix','sco') or die( respuestaErrorHttp() );
}

//---> validar el token.
//var_dump( $headers );
try{
  $auth->validateToken( $headers['Authorization'] );
  $userPl = $auth->getPayload()->data;
  //var_dump( $userPl );
}catch( Exception $e ){
  $respuesta['message'] = $e->getMessage();
  endRoutine( $respuesta, 401 );
}
/*endRoutine( $respuesta, 401 );
return;*/


if ( $_SERVER['REQUEST_METHOD'] == 'GET' ){

    if ( isset( $_GET['id'] ) ){

        consultarDatosArticulo( $_GET['id'] );
        endRoutine($respuesta);
    }

    if( isset( $_GET['provider'] ) ){
      consultarArticulosXproveedor( $_GET['provider'] );
      endRoutine($respuesta);
    }

    $respuesta['message'] = 'The required parameter was not provided';
    endRoutine($respuesta, 400);
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){

    $_POST      = json_decode(file_get_contents("php://input"));
    $_POST      = json_encode( $_POST );
    $_POST      = json_decode( $_POST, true );

    /*if ( $_POST['movement'] == "income" ){

      $arrayParametrosRequeridos = array(
        "productCode",
        "provider",
        "replacementOrderNumber",
        "amount",
        "lot",
        "expiry",
        "providerProductCode",
        "movement",
        "item",
        "unitCost",
        "user");

      $validador->validarParametros( $arrayParametrosRequeridos, $_POST );
      if( !$validador->valido ){
        $respuesta['message'] = "The required parameter: {$validador->faltante} was not provided ";
        endRoutine($respuesta,400);
      }

      if( !$conex_o && $_POST['reqSource'] != "interno" ){
        guardarPeticionParaEjecucionPosterior( json_encode($_POST), $userPl->userCode, "POST" );
      }

      realizarIngresoInsumo();
      endRoutine(  $respuesta  );

    }*/
    if( $_POST['movement'] == "income" ){
      if( !$hay_unix or (!$conex_o && $_POST['reqSource'] != "interno") ){
          guardarPeticionParaEjecucionPosterior( json_encode($_POST), $userPl->userCode, "POST" );
      }

      $fuenteXdocumento = array();
      $fuenteXdocumento = obtenerFuenteDocumentoUnix( $fuenteEntXconsignacion );
      $documentoRemision = "";

      foreach( $_POST['items'] as $index => $producData ){
        $arrayParametrosRequeridos = array(
          "productCode",
          "provider",
          "replacementOrderNumber",
          "amount",
          "lot",
          "expiry",
          "providerProductCode",
          "item",
          "unitCost",
          "user");

        $validador->validarParametros( $arrayParametrosRequeridos, $producData );
        if( !$validador->valido ){
          $respuesta['message'] = "The required parameter: {$validador->faltante} for product:{$producData['providerProductCode']} was not provided ";
          endRoutine($respuesta,400);
        }


        $homologado = validarHomologacion( $codigoArticuloProveedor, $codigoArticuloInterno, $nitProveedor );

        if( $homologado ){
          realizarIngresoInsumo( $producData, $fuenteXdocumento, $index*1+1 );
        }else{
          $respuesta['message'] = "Proceso de homologacion requerido";
          $respuesta['status']  = 500;
        }
      }
      endRoutine(  $respuesta  );
    }else if( $_POST['movement'] == "consumption" ){

      $desde_CargosPDA = true;
      $emp             = $wemp_pmla;
      $pac             = array();
      $usuario         = "";
      $cco             = array();
      $arrayParametrosRequeridos = array(
        "patient_clinical_id",
        "productCode",
        "amount",
        "lot",
        "movement",
        "departmentCode",
        "user");

      $validador->validarParametros( $arrayParametrosRequeridos, $_POST );
      if( !$validador->valido ){
        $respuesta['message'] = "The required parameter: '{$validador->faltante}' was not provided ";
        endRoutine($respuesta,400);
      }

      if( !$hay_unix or ( !$conex_o && $_POST['reqSource'] != "interno" ) ){
        guardarPeticionParaEjecucionPosterior( json_encode($_POST), $userPl->userCode, "POST" );
      }

      realizarConsumoInsumo("C");
      endRoutine($respuesta);

    }else if( $_POST['movement'] == "undoing" ){

      $desde_CargosPDA = true;
      $emp             = $wemp_pmla;
      $pac             = array();
      $usuario         = "";
      $cco             = array();
      $arrayParametrosRequeridos = array(
        "patient_clinical_id",
        "productCode",
        "amount",
        "lot",
        "movement",
        "departmentCode",
        "user");

      $validador->validarParametros( $arrayParametrosRequeridos, $_POST );
      if( !$validador->valido ){
        $respuesta['message'] = "The required parameter: '{$validador->faltante}' was not provided ";
        endRoutine($respuesta,400);
      }

      if( !$hay_unix or ( !$conex_o && $_POST['reqSource'] != "interno" ) ){
        guardarPeticionParaEjecucionPosterior( json_encode($_POST), $userPl->userCode, "POST" );
      }

      realizarConsumoInsumo("D");
      endRoutine($respuesta);

    }else if( $_POST['movement'] == "transfer" ){
      $desde_CargosPDA = true;
      $arrayParametrosRequeridos = array(
        "origin",
        "destination",
        "amount",
        "lot",
        "movement",
        "productCode",
        "providerProductCode",
        "stc",
        "user");
      $arrayParametrosOpcionales = array(
        "origin"
      );

      $validador->validarParametros( $arrayParametrosRequeridos, $_POST, $arrayParametrosOpcionales );
      if( !$validador->valido ){
        $respuesta['message'] = "The required parameter: '{$validador->faltante}' was not provided ";
        endRoutine($respuesta,400);
      }

      if( !$hay_unix or ( !$conex_o && $_POST['reqSource'] != "interno" ) ){
        guardarPeticionParaEjecucionPosterior( json_encode($_POST), $userPl->userCode, "POST" );
      }
      /* logic */
      $fuenteXdocumento = array();
      $fuenteXdocumento = obtenerFuenteDocumentoUnix( $fuenteTrasladosConsign );
      realizarTrasladoInsumo( $fuenteXdocumento );


    }else if( $_POST['movement'] == "homologate" ){
      
      $arrayParametrosRequeridos = array(
        "internalProductCode",
        "provider",
        "providerProductCode",
        "movement",
        "user");
      $validador->validarParametros( $arrayParametrosRequeridos, $_POST );
      if( !$validador->valido ){
        $respuesta['message'] = "The required parameter: {$validador->faltante} was not provided ";
        endRoutine($respuesta,400);
      }

      homologarArticuloInternoProveedor();
      endRoutine(  $respuesta  );

    }else if( $_POST['movement'] == "PurchaseOrder" ){
      
      if( !$hay_unix or (!$conex_o && $_POST['reqSource'] != "interno") ){
          guardarPeticionParaEjecucionPosterior( json_encode($_POST), $userPl->userCode, "POST" );
      }

      $fuenteXdocumento = array();
      $fuenteXdocumento = obtenerFuentesInventarioUnix( $fuenteOrdenCompra, $servicioTrasladosCons );
      $documentoRemision = "";

      $cantidadArticulos = count($_POST['items']);
      $costoBrutoTotal = 0;
      $valorIvaTotal   = 0;
      foreach( $_POST['items'] as $index => $producData ){
        $arrayParametrosRequeridos = array(
          "productCode",
          "provider",
          "providerProductCode",
          "amount",
          "cost");

        $validador->validarParametros( $arrayParametrosRequeridos, $producData );
        if( !$validador->valido ){
          $respuesta['message'] = "The required parameter: {$validador->faltante} for product:{$producData['productCode']} was not provided ";
          endRoutine($respuesta,400);
        }
        generarOrdenDeCompra( $producData, $fuenteXdocumento, $index*1+1, $cantidadArticulos );
      }

      endRoutine(  $respuesta  );
    } else {
      $respuesta['message'] = "The required parameter: 'movement' was not provided ";
      endRoutine($respuesta,400);
    }
}

function consultarDatosArticulo( $articulo ){
    global $conex,
           $wbasedato,
           $wmovhos,
           $fechaActual,
           $respuesta;
    $articuloEncontrado = array();

    $query = " SELECT Axpcpr codigoProveedor, Pronom nombreProveedor, Cincon consumoHoy, Salsal saldoActual
                 FROM {$wbasedato}_000009
                INNER JOIN
                      {$wbasedato}_000006 on ( Axpart = '{$articulo}' AND pronit = axpcpr )
                LEFT  JOIN
                      {$wbasedato}_000321 on ( Cinart = Axpart AND Cinfec = '{$fechaActual}' )
                INNER JOIN
                      {$wbasedato}_000322 on ( Salart = Axpart )
                LIMIT 1";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_assoc( $rs );


    if( $row ){
        $articuloEncontrado['internalId']       = $articulo;
        $articuloEncontrado['provider']         = $row['nombreProveedor'];
        $articuloEncontrado['todayConsumption'] = $row['consumoHoy'];
        $articuloEncontrado['currentStock']     = $row['saldoActual'];
        $respuesta['status']                    = 200;
        $respuesta['result']                    = $articuloEncontrado;
    }else{
        $respuesta['status'] = 404;
    }
}

function consultarArticulosXproveedor( $proveedor ){

  global $conex,
           $wbasedato,
           $wmovhos,
           $fechaActual,
           $respuesta;
    $articulos      = array();
    $huboResultados = false;

    $query = " SELECT Axpcpr codigoProveedor, Pronom nombreProveedor, Artcod codigoArticulo, Artcom nombreComercialArticulo, Artgen nombreGenerico,
                       salsal saldoActual, Axpcba as codigoUniversalInsumo
                 FROM {$wbasedato}_000009
                INNER JOIN
                      {$wmovhos}_000026 on ( axpcpr = '{$proveedor}' AND axpart = artcod )
                INNER JOIN
                      {$wbasedato}_000006 on ( pronit = axpcpr )
                INNER JOIN
                      {$wbasedato}_000322 on ( salart = Artcod )
                GROUP by 1, 2, 3, 4, 5";

    $rs    = mysql_query( $query, $conex );

    while( $row   = mysql_fetch_assoc( $rs ) ){

        $huboResultados            = true;
        $articulo                  = array();
        $articulo['provider']      = utf8_encode(trim($row['nombreProveedor']));
        $articulo['internalId']    = utf8_encode(trim($row['codigoArticulo']));
        $articulo['comercialName'] = utf8_encode(trim($row['nombreComercialArticulo']));
        $articulo['genericName']   = utf8_encode(trim($row['nombreGenerico']));
        $articulo['currentStock']  = utf8_encode(trim($row['saldoActual']));
        $articulo['upn']           = utf8_encode(trim($row['codigoUniversalInsumo']));
        array_push( $articulos, $articulo );
    }
    if($huboResultados){
      $respuesta['result'] = $articulos;
      $respuesta['status'] = 200;
      }else
        $respuesta['status'] =404;
}

/*function realizarIngresoInsumo(){
  global $_POST,
         $wemp_pmla,
         $wbasedato,
         $conex,
         $conex_o,
         $respuesta,
         $horaActual,
         $fechaActual,
         $conceptoIngreso,
         $servicioIngreso,
         $fuenteEntXconsignacion;
  $nitProveedor            = $_POST['provider'];
  $documentoRemision       = $_POST['replacementOrderNumber'];
  $codigoArticuloInterno   = $_POST['productCode'];
  $codigoArticuloProveedor = $_POST['providerProductCode'];
  $lote                    = $_POST['lot'];
  $fechaVencimiento        = $_POST['expiry'];
  $cantidad                = $_POST['amount'];
  $precioUnitario          = $_POST['unitCost'];
  $usuario                 = $_POST['user'];
  $item                    = 1;
  $descuento               = 0;
  $fuenteXdocumento        = array();
  $homologado              = validarHomologacion( $codigoArticuloProveedor, $codigoArticuloInterno, $nitProveedor );

  if ($homologado) {
    $fuenteXdocumento = obtenerFuenteDocumentoUnix( $fuenteEntXconsignacion );
    $fecAux = explode( "-", $fechaActual );
    $ano    = $fecAux[0];
    $mes    = $fecAux[1];

    //--> traer datos del sistema asociados: Uni,  valor/uni, Dcto. total, Iva total, Vlr/Neto
    $query = " SELECT artuni, artiva
                 FROM ivart
                WHERE artcod = '{$codigoArticuloInterno}'
                  AND artact = 'S'";
    $resFac    = odbc_exec( $conex_o, $query );
    $row       = odbc_fetch_row($resFac);
    $unidad    = odbc_result($resFac,'artuni');
    $factorIva = odbc_result($resFac,'artiva');

    $ivaTotal  = ($precioUnitario*( $factorIva/100 ))*$cantidad;
    $total     = $ivaTotal + ( $precioUnitario*$cantidad );
    $costo     = $total;

    //Grabación del encabezado del movimiento en ivmov
    $query  = " INSERT INTO ivmov ( movfue, movdoc, movano, movmes, movfec, movcon, movser, movnit, movdni, movanu )
                      VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', '{$ano}', '{$mes}', '{$fechaActual}', '{$conceptoIngreso}', '{$servicioIngreso}', '{$nitProveedor}', '{$documentoRemision}', 0 )";
    $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

    //Grabación del detalle del movimiento en ivmovdet 
    $query  = " INSERT INTO ivmovdet ( movdetfue, movdetdoc, movdetite, movdetano, movdetmes, movdetcon, movdetart, movdetcan, movdetuni, movdetpre, movdetdes, movdetiva, movdettot, movdetcos, movdetanu  )
                      VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', {$item}, '{$ano}', '{$mes}', '{$conceptoIngreso}', '{$codigoArticuloInterno}', {$cantidad}, '{$unidad}', {$precioUnitario}, {$descuento}, {$ivaTotal}, {$total}, {$costo}, 0  )";
    $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

    //Actualización del 
    $query  = " UPDATE ivsal
                   SET salent = salent + {$cantidad}
                 WHERE salano = '{$ano}'
                   AND salmes = '{$mes}'
                   AND salser = '{$servicioIngreso}'
                   AND salart = '{$codigoArticuloInterno}'";
    $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

    grabarMovimientoInventarioMatrix( $fuenteXdocumento, $conceptoIngreso,$servicioIngreso,$nitProveedor,$codigoArticuloInterno,$codigoArticuloProveedor,$lote,$fechaVencimiento,$cantidad,$precioUnitario,$usuario );

  }else{
    $respuesta['message'] = "Proceso de homologacion requerido";
    $respuesta['status']  = 500;
  }
}*/

function realizarIngresoInsumo( $producData, $fuenteXdocumento, $item ){
  global $wemp_pmla,
         $wbasedato,
         $conex,
         $conex_o,
         $respuesta,
         $horaActual,
         $fechaActual,
         $conceptoIngreso,
         $servicioIngreso,
         $fuenteEntXconsignacion,
         $remisionActual;
  $nitProveedor            =$producData['provider'];
  $codigoProveedorUnix     = consultarTipoEmpresa($producData['provider']);
  $codigoProveedorUnix     = $codigoProveedorUnix['tipo'].$codigoProveedorUnix['codigo'];
  $documentoRemision       = $producData['replacementOrderNumber'];
  $codigoArticuloInterno   = $producData['productCode'];
  $codigoArticuloProveedor = $producData['providerProductCode'];
  $lote                    = $producData['lot'];
  $fechaVencimiento        = $producData['expiry'];
  $cantidad                = $producData['amount'];
  $precioUnitario          = $producData['unitCost'];
  $usuario                 = $producData['user'];
  $descuento               = 0;
  $fecAux                  = explode( "-", $fechaActual );
  $ano                     = $fecAux[0];
  $mes                     = $fecAux[1];

  //--> traer datos del sistema asociados: Uni,  valor/uni, Dcto. total, Iva total, Vlr/Neto
  $datosArticulo  = consultarArticuloUnix( $codigoArticuloInterno );
  $unidad    = $datosArticulo['unidad'];
  $factorIva = $datosArticulo['factorIva'];

  $ivaTotal  = ($precioUnitario*( $factorIva/100 ))*$cantidad;
  $total     = $ivaTotal + ( $precioUnitario*$cantidad );
  $costo     = $total;

  /*Grabación del encabezado del movimiento en ivmov*/
  if( $remisionActual == "" ){
    $remisionAux = substr( $documentoRemision, -6, 6);
    $query  = " INSERT INTO ivmov ( movfue, movdoc, movano, movmes, movfec, movcon, movser, movnit, movdni, movanu )
                      VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', '{$ano}', '{$mes}', '{$fechaActual}', '{$conceptoIngreso}', '{$servicioIngreso}', '{$codigoProveedorUnix}', '{$remisionAux}', 0 )";
    $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );
    $remisionActual = $documentoRemision;
  }

  /*Grabación del detalle del movimiento en ivmovdet */
  $query  = " INSERT INTO ivmovdet ( movdetfue, movdetdoc, movdetite, movdetano, movdetmes, movdetcon, movdetart, movdetcan, movdetuni, movdetpre, movdetdes, movdetiva, movdettot, movdetcos, movdetanu  )
                    VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', {$item}, '{$ano}', '{$mes}', '{$conceptoIngreso}', '{$codigoArticuloInterno}', {$cantidad}, '{$unidad}', {$precioUnitario}, {$descuento}, {$ivaTotal}, {$total}, {$costo}, 0  )";

  $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

  /*Actualización del */
  $query  = " UPDATE ivsal
                 SET salent = salent + {$cantidad}
               WHERE salano = '{$ano}'
                 AND salmes = '{$mes}'
                 AND salser = '{$servicioIngreso}'
                 AND salart = '{$codigoArticuloInterno}'";
  $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

  grabarMovimientoInventarioMatrix( $fuenteXdocumento, $conceptoIngreso,$servicioIngreso,$nitProveedor,$codigoArticuloInterno,$codigoArticuloProveedor,$lote,$fechaVencimiento,$cantidad,$precioUnitario,$usuario );
}

function realizarConsumoInsumo( $tipTrans ){

  include_once("movhos/registro_tablas.php");
  include_once("movhos/otros.php");
  include_once("movhos/cargosSF.inc.php");
  include_once("ips/funciones_facturacionERP.php");

  global $_POST,
         $wemp_pmla,
         $wbasedato,
         $conex,
         $conex_o,
         $respuesta,
         $horaActual,
         $fechaActual,
         $fuenteEntXconsignacion,
         $wmovhos,
         $desde_CargosPDA,
         $emp,
         $pac,
         $usuario,
         $cco;

  $cco['cod'] = $_POST['departmentCode'];
  $pac['his'] = $_POST['patient_clinical_id'];
  $pac['ing'] = encontrarIngresoActual( $_POST['patient_clinical_id'] );

  $articulo = array(
    'cod' => $_POST['productCode'], //Codigo del insumo
    'nom' => "",  //nombre comercial
    'can' => $_POST['amount'], //Cantidad a cargar
    'uni' => "",
    'lot' => '',    //El lote solo es para productos de CM, por tanto es vacio
    'ini' => $_POST['productCode'], //Codigo leido inicial, puede ser codigo de barras. Siempre se envia el codigo matrix
    'ubi' => 'M',   //M de Matrix
    'ser' => "", //Servicio de destiono, es decir, el cco del paciente
    'dis' => 'off',   //Indica si está en el carro, como es aplicacion en piso no hay carro. Siempre es off
  );

  $dronum         = "";
  $drolin         = "";
  $error          = "";
  $inactivar      = false;
  $aprov          = false;
  $cns            = "";
  $docXvez        = false;

  $esImplantable  = validarMedicamentoImplantable( $articulo );
  consultarDatosArticuloConsumo( $articulo, $pac['his'], $pac['ing'] );
  getCco($cco, $tipTrans, $wemp_pmla);

  $fuente         = $cco['fue'];
  $fuenteAux      = array('fuente'=>$fuente, "documento"=>0 );
  $conceptoSalida = consultarConceptoSalida( $fuente, $articulo );

  Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, $fechaActual, $cns, $dronum, $drolin, $docXvez, $_POST['user'], $error);
  registrarDetalleCargo ($fechaActual, $dronum, $drolin, $articulo, $_POST['user'], $error, $tabla = "000003" );

  if( $esImplantable ){
    $turno          = "";
    $lote           = $_POST['lot'];
    $devolucion     = ( $tipTrans == "D" ) ? $_POST['amount'] : 0;
    $ValorCargado   = ( $tipTrans == "C" ) ? $_POST['amount'] : 0;
    $medicoTratante = consultar_MedicoTratante( $conex, $wmovhos, $pac['his'], $pac['ing'], $fechaActual );

    registrarLote( $conex, $wbasedato, $_POST['departmentCode'], $_POST['productCode'], $lote, $ValorCargado, $devolucion, $fechaActual, $horaActual, $_POST['user'], "on", $pac['his'], $pac['ing'], 'on', $_POST['departmentCode'], $medicoTratante );
  }
  $registroItdro = registrarItdro($dronum, $drolin, $fuente, $fechaActual, $cco, $pac, $articulo, $error);


  if( $registroItdro ){
    $emp             = $wemp_pmla;
    $usuario         = $_POST['user'];
    $wuse            = $_POST['user'];
    $desde_CargosPDA = true;
    CargarCargosErp( $conex, $wmovhos, $wbasedato, $articulo, $tipTrans, $dronum, $drolin );
  }

  if( !$cco['apl'] ){
    registrarSaldosNoApl($pac, $articulo, $cco, $aprov, $_POST['user'], $tipTrans, $inactivar, $error);
  }
  else{
    registrarSaldosAplicacion($pac, $articulo, $cco, $aprov, $_POST['user'], $tipTrans, $inactivar, $error);
  }

  grabarMovimientoInventarioMatrix( $fuenteAux, $conceptoSalida ,$cco['cod'],"",$_POST['productCode'],"",$_POST['lot'],$_POST['expiry'],$_POST['amount'],0,$usuario, $dronum, $drolin, $pac );
  if( trim( $error ) == "" ){

  }else{
    $respuesta['message'] = $error;
    $respuesta['status']  = 500;
  }
}

function validarMedicamentoImplantable( &$articulo ){
  global $conex,
         $bd;
  $query = " SELECT arteim
               FROM {$bd}_000026
              WHERE artcod = '{$articulo['cod']}'";
  $rs    = mysql_query( $query, $conex  );
  $row   = mysql_fetch_assoc( $rs );
  $esImplantable = ( $row['arteim'] == "on" ) ? true : false;
  return( $esImplantable );
}

function validarHomologacion( $codigoArticuloProveedor, $codigoArticuloInterno, $nitProveedor ){
  global $conex_o;
  $homologado = false;
  $coditoArtProAux = substr( $codigoArticuloProveedor, 0, 13);

  $query =  " SELECT count(*) as homologados
                FROM ivartcba
               WHERE artcbacba = '{$coditoArtProAux}'
                 AND artcbaart = '{$codigoArticuloInterno}'";

  $resFac      = odbc_exec($conex_o,$query);
  $row         = odbc_fetch_row($resFac);
  $homologados = odbc_result($resFac,'homologados');

  if( $homologados > 0 ){
      $homologado = true;
  }else{
    //--> realizar la homologación.
    $query =  " SELECT artnom nombre, artuni unidad, artfxu fxu, artdes artcbacon, 'I' artcbasiv, 'S' artcbaact
                  FROM ivart
                 WHERE artcod = '{$codigoArticuloInterno}'";

    $resFac    = odbc_exec($conex_o,$query);
    $row       = odbc_fetch_row($resFac);
    $nombre    = trim(odbc_result($resFac,'nombre'));
    $unidad    = odbc_result($resFac,'unidad');
    $fxu       = odbc_result($resFac,'fxu');
    $artcbacon = odbc_result($resFac,'artcbacon');
    $artcbasiv = "I";
    $artcbaact = "S";

    $query = " INSERT INTO ivartcba ( artcbaart, artcbacba, artcbanom, artcbanit, artcbauni, artcbafxu, artcbacon, artcbasiv, artcbaact)
                    VALUES ( '{$codigoArticuloInterno}', '{$coditoArtProAux}', '{$nombre}', '{$nitProveedor}', '{$unidad}', '{$fxu}', '{$artcbacon}', '{$artcbasiv}', '{$artcbaact}')";
    $resFac    = odbc_exec( $conex_o,$query );
    $homologado = true;
  }
  return $homologado;
}

function obtenerFuenteDocumentoUnix( $fuenteBuscada ){
  global $conex_o;

  $datosFuente = array();

  $query = "SELECT fuecod, fuesec
              FROM sifue
             WHERE fuecod = '{$fuenteBuscada}'";

  $resFac                   = odbc_exec($conex_o,$query);
  $row                      = odbc_fetch_row($resFac);
  $datosFuente['fuente']    = odbc_result($resFac,'fuecod');
  $datosFuente['documento'] = odbc_result($resFac,'fuesec');

  $query = "UPDATE sifue
               SET fuesec = fuesec + 1
             WHERE fuecod = '{$fuenteBuscada}'";

  $resFac = odbc_exec($conex_o,$query);

  return( $datosFuente );
}

function obtenerFuentesInventarioUnix( $fuenteBuscada, $ccoDestino='' ){
  global $conex_o;

  $datosFuente = array();
  $documentoDisponible = false;

  while( !$documentoDisponible ){
    $query = "SELECT fuecod, fuesec
                FROM ivfue
              WHERE fuecod = '{$fuenteBuscada}'
                AND fuecco = '1060'";
    

    $resFac                   = odbc_exec($conex_o,$query);
    $row                      = odbc_fetch_row($resFac);
    $datosFuente['fuente']    = odbc_result($resFac,'fuecod');
    $datosFuente['documento'] = odbc_result($resFac,'fuesec')+1;


    $query = "UPDATE ivfue
                SET fuesec = fuesec + 1
              WHERE fuecod = '{$fuenteBuscada}'
                AND fuecco = '1060'";

    $resFac = odbc_exec($conex_o,$query);

    $query = "SELECT COUNT(*) as cantidad
                FROM ivord
               WHERE ordfue = '{$datosFuente['fuente']}' 
                 AND orddoc = '{$datosFuente['documento']}'";
    
    $resFac = odbc_exec( $conex_o,$query );
    $row    = odbc_fetch_row( $resFac );
    $documentoDisponible = ( odbc_result($resFac,'cantidad') > 0 ) ? false : true  ;
    
  }

  return( $datosFuente );
}

function realizarTrasladoInsumo( $fuenteXdocumento ){
  global $_POST,
         $wemp_pmla,
         $wbasedato,
         $conex,
         $conex_o,
         $respuesta,
         $horaActual,
         $fechaActual,
         $fuenteTrasladosConsign,
         $conceptoTraslado,
         $wmovhos,
         $servicioTrasladosCons;

  $fecAux = explode( "-", $fechaActual );
  $ano    = $fecAux[0];
  $mes    = $fecAux[1];
  $ccoDestino = $_POST['destination'];
  $item  = 1;
  $codigoArticuloInterno = $_POST['productCode'];
  $ccoOrigen = ( $_POST['stc'] == "on" ) ? $servicioTrasladosCons : $_POST['origin'];
  $cantidad = $_POST['amount'];
  
  //--> traer datos del sistema asociados: Uni,  valor/uni, Dcto. total, Iva total, Vlr/Neto
  $datosArticulo  = consultarArticuloUnix( $codigoArticuloInterno );
  $unidad    = $datosArticulo['unidad'];
  $precio    = $datosArticulo['costo'];
  $valorMovimiento = $cantidad*$precio*1;

  //--> traer datos del sistema asociados: precio y costo
  $query = " SELECT salpro
               FROM ivsal
              WHERE salart = '{$codigoArticuloInterno}'
                AND salano = '{$ano}'
                AND salmes = '{$mes}'
                AND salser = '{$ccoDestino}'";
  $resFac    = odbc_exec( $conex_o, $query );
  $row       = odbc_fetch_row($resFac);
  $ultimoPrecio = odbc_result($resFac,'salpro');
  $ultimoPrecio = ( $ultimoPrecio == "" or $ultimoPrecio == 0 )  ? $precio : $ultimoPrecio;
  
  /*Grabación del encabezado del movimiento en ivmov*/
  $query  = " INSERT INTO ivmov ( movfue, movdoc, movano, movmes, movfec, movcon, movser, movse1, movanu )
                      VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', '{$ano}', '{$mes}', '{$fechaActual}', '{$conceptoTraslado}', '{$ccoOrigen}', '{$ccoDestino}', 0 )";
  $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

  /*Grabación del detalle del movimiento en ivmovdet */
  $query  = " INSERT INTO ivmovdet ( movdetfue, movdetdoc, movdetite, movdetano, movdetmes, movdetcon, movdetart, movdetcan, movdetuni, movdetpre, movdetdes, movdetiva, movdettot, movdetcos, movdetanu  )
                    VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', {$item}, '{$ano}', '{$mes}', '{$conceptoTraslado}', '{$codigoArticuloInterno}', {$cantidad}, '{$unidad}', 0, 0, 0, {$ultimoPrecio}, 0, 0  )";

  $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

  /*Verificación de la existencia de saldos previa, si el producto está recién creado estos registros no existen y el proceso fallará*/
  //saldos en centro de costos del que salen las existencias
  $query = " SELECT COUNT(*) cantidad
               FROM ivsal 
              WHERE salano = '{$ano}'
                AND salmes = '{$mes}'
                AND salser = '{$ccoOrigen}'";
  $resFac  = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );
  $row     = odbc_fetch_row($resFac);
  $cantidadMesActualOrigen  = odbc_result($resFac,'cantidad');
  
  if( $cantidadMesActualOrigen == 0 ){
    $query  = " INSERT INTO ivsal ( salano, salmes, salser, salart, saluni, salant, salvan, salent, salven, salsal, salvsa, salpro, salaju  )
                    VALUES ( '{$ano}', '{$mes}', {$ccoOrigen}, '{$codigoArticuloInterno}', '{$unidad}', 0, 0, 0, 0, {$cantidad}, {$valorMovimiento}, {$precio}, 0  )";
    $resFac  = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );  
  }
  //saldos en centro de costos al que entran las existencias
  $query = " SELECT COUNT(*) cantidad
               FROM ivsal 
              WHERE salano = '{$ano}'
                AND salmes = '{$mes}'
                AND salser = '{$ccoDestino}'";
  $resFac  = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );
  $row     = odbc_fetch_row($resFac);
  $cantidadMesActualDestino  = odbc_result($resFac,'cantidad');
  if( $cantidadMesActualDestino == 0 ){
    $query  = " INSERT INTO ivsal ( salano, salmes, salser, salart, saluni, salant, salvan, salent, salven, salsal, salvsa, salpro, salaju  )
                    VALUES ( '{$ano}', '{$mes}', {$ccoDestino}, '{$codigoArticuloInterno}', '{$unidad}', 0, 0, {$cantidad}, {$valorMovimiento}, 0, 0, {$precio}, 0  )";
    $resFac  = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );
  }
  /*Actualización del inventario en el destino */
  if( $cantidadMesActualOrigen >0 ){
    $query  = " UPDATE ivsal
                  SET salsal = salsal + {$cantidad},
                      salvsa = salvsa + {$valorMovimiento}
                WHERE salano = '{$ano}'
                  AND salmes = '{$mes}'
                  AND salser = '{$ccoOrigen}'
                  AND salart = '{$codigoArticuloInterno}'";
    $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );
  }
  
  /*Actualización del inventario en el destino */
  if( $cantidadMesActualDestino > 0 ){
    $query  = " UPDATE ivsal
                  SET salent = salent + {$cantidad},
                      salven = salven + {$valorMovimiento}
                WHERE salano = '{$ano}'
                  AND salmes = '{$mes}'
                  AND salser = '{$ccoDestino}'
                  AND salart = '{$codigoArticuloInterno}'";
    $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );
  }

  grabarMovimientoInventarioMatrix( $fuenteXdocumento, $conceptoTraslado,$ccoDestino,$_POST['provider'],$codigoArticuloInterno,$_POST['providerProductCode'],$_POST['lot'],"",$cantidad,0,$_POST['user'] );
  endRoutine(  $respuesta  );
  return;
}

function registrarErrorApi(){
  header("HTTP/1.1 500 Internal server error");
  echo json_encode( $respuesta  );
  exit();
}


function consultarDatosArticuloConsumo( &$articulo, $historia, $ingreso ){
  global $conex,
         $bd,
         $pac;

  $query = " SELECT Artuni, Artcom, Artgru
               FROM {$bd}_000026
              WHERE artcod = '{$articulo['cod']}'";
  $rs    = mysql_query( $query, $conex );
  $row   = mysql_fetch_assoc( $rs );
  $articulo['uni'] = $row['Artuni'];
  $articulo['nom'] = $row['Artcom'];
  $articulo['gru'] = explode( "-", $row['Artgru'] )[0];

  $query = " SELECT Ubisac
               FROM {$bd}_000018
              WHERE ubihis = '{$historia}'
                AND ubiing = '{$ingreso}'";
  $rs    = mysql_query( $query, $conex );
  $row   = mysql_fetch_assoc( $rs );
  $articulo['ser'] = $row['Ubisac'];
  $pac['sac']      = $row['Ubisac'];
  return;
}

function grabarMovimientoInventarioMatrix( $fuenteXdocumento, $conceptoIngreso,$servicioIngreso,$nitProveedor,$codigoArticuloInterno,$codigoArticuloProveedor,$lote,$fechaVencimiento,$cantidad,$precioUnitario,$usuario, $dronum = 0, $drolin = 0, $pac = array() ){

  global $conex,
         $_POST,
         $wbasedato,
         $horaActual,
         $fechaActual,
         $respuesta,
         $servicioTrasladosCons;
    $cantidadEncabezado = 0;
    
    switch( $_POST['movement'] ){
      case "income":
        $cantidadEncabezado = $cantidad*1;
        $ccoOrigen = ""; 
        $mueveCantidad = true;
        break;
      case "consumption":
        $cantidadEncabezado = $cantidad*(-1);
        $ccoOrigen = ""; 
        $mueveCantidad = true;
        break;
      case "undoing":
        $cantidadEncabezado = $cantidad*1;
        $_POST['origin'] = ""; 
        $mueveCantidad = true;
        break;
      case "transfer":
        $ccoOrigen = ( $_POST['stc'] == "on" ) ? $servicioTrasladosCons : $_POST['origin'];
        $mueveCantidad = false;
      default:
        break;
    }

    $query  = " INSERT INTO {$wbasedato}_000324 ( Medico, Fecha_data, Hora_data, movfue, movdoc, movcon, movcco, movnit, movart, movcba, movlot, movfve, movcan, movpre, movnum, movlin, movcci, Seguridad )
                       VALUES ( '{$wbasedato}','{$fechaActual}','{$horaActual}','{$fuenteXdocumento['fuente']}','{$fuenteXdocumento['documento']}','{$conceptoIngreso}','{$servicioIngreso}','{$nitProveedor}','{$codigoArticuloInterno}','{$codigoArticuloProveedor}','{$lote}','{$fechaVencimiento}','{$cantidad}',{$precioUnitario}, $dronum, $drolin, '{$ccoOrigen}', 'C-{$usuario}')";
    $rs     = mysql_query( $query, $conex ) or ( registrarErrorApi() );

    if( $mueveCantidad ){
      $query  = " SELECT count(*)
                    FROM {$wbasedato}_000325
                  WHERE Salart = '{$codigoArticuloInterno}'
                    AND Sallot = '{$lote}'
                    AND Salest = 'on'";

      $rs     = mysql_query( $query, $conex )or ( registrarErrorApi() );
      $rowCan = mysql_fetch_array( $rs );
      if( $rowCan[0]*1 > 0 ){
        $query = " UPDATE {$wbasedato}_000325
                      SET salsal = salsal + ({$cantidadEncabezado})
                    WHERE salart = '{$codigoArticuloInterno}'
                      AND sallot = '{$lote}'";
      }else{
        $query = " INSERT INTO {$wbasedato}_000325 (Medico, Fecha_data, Hora_data, Sallot, Salart, Salfev, Salnit, Salsal, Seguridad)
                        VALUES ('{$wbasedato}', '{$fechaActual}', '{$horaActual}', '{$lote}', '{$codigoArticuloInterno}', '{$fechaVencimiento}', '{$nitProveedor}', {$cantidadEncabezado}, 'C-{$wbasedato}'); ";
      }
      $rs     = mysql_query( $query, $conex ) or ( registrarErrorApi() );
    }
    $objetoRespuesta                            = array();
    if( $_POST['movement'] == "income"){
      $objetoRespuesta['fuente']                  = $fuenteXdocumento['fuente'];
      $objetoRespuesta['documento']               = $fuenteXdocumento['documento'];
      $objetoRespuesta['codigoArticuloInterno']   = $codigoArticuloInterno;
      $objetoRespuesta['codigoArticuloProveedor'] = $codigoArticuloProveedor;
      $objetoRespuesta['lote']                    = $lote;
      $objetoRespuesta['fechaVencimiento']        = $fechaVencimiento;
      $objetoRespuesta['cantidad']                = $cantidad;
      $objetoRespuesta['nitProveedor']            = $nitProveedor;
      $objetoRespuesta['precioUnitario']          = $precioUnitario;
      $objetoRespuesta['total']                   = $cantidad*$precioUnitario;
    } else if( $_POST['movement'] == "consumption" or $_POST['movement'] == "undoing" ){
      $objetoRespuesta['numeroTransaccion'] = $dronum;
      $objetoRespuesta['linea']             = $drolin;
      $objetoRespuesta['fuente']            = $fuenteXdocumento['fuente'];
      $objetoRespuesta['fecha']             = $fechaActual;
      $objetoRespuesta['historia']          = $pac['his'];
      $objetoRespuesta['ingreso']           = $pac['ing'];
      $objetoRespuesta['articulo']          = $_POST['productCode'];
    }else if( $_POST['movement'] == "transfer" ){
      $objetoRespuesta['fuente']    = $fuenteXdocumento['fuente'];
      $objetoRespuesta['documento'] = $fuenteXdocumento['documento'];
    }else if( $_POST['movement'] == "PurchaseOrder" ){
      $objetoRespuesta['fuente']    = $fuenteXdocumento['fuente'];
      $objetoRespuesta['documento'] = $fuenteXdocumento['documento'];
    }

    $respuesta['message'] = "The movement finished up successfully";
    array_push($respuesta['result'], $objetoRespuesta);
    $respuesta['status']  = 200;
}

function consultarConceptoSalida( $fuente, $articulo ){
  global $conex_o;

  $query = " SELECT fuecob
               FROM sifue
              WHERE fuecod = '{$fuente}'";
  $resFac      = odbc_exec($conex_o,$query);
  $row         = odbc_fetch_row($resFac);
  $conceptoInv = odbc_result($resFac,'fuecob');
  return( $conceptoInv );
  // EN CASO DE NECESITAR EL CONCEPTO DE FACTURACIÓN DESCOMENTAR EL SIGUIENTE CÓDIGO
  /*$query = " SELECT congrufac
               FROM ivcongru
              WHERE congrucon = '{$conceptoInv}'
                AND congrugru = '{$articulo['gru']}'";
  $resFac      = odbc_exec($conex_o,$query);
  $row         = odbc_fetch_row($resFac);
  $conceptoFac = odbc_result($resFac,'fuecod');
  return( $conceptoFac );*/
}

function guardarPeticionParaEjecucionPosterior( $datosRequest, $userPl, $tipoRequest ){

  global $conex,
         $wbasedato,
         $fechaActual,
         $horaActual;

  $query = " INSERT INTO {$wbasedato}_000326 ( Medico, Fecha_data, Hora_data, Miptip, Mipusu, Mippar, Mipest, Seguridad)
                      VALUES ('{$wbasedato}', '{$fechaActual}', '{$horaActual}', '{$tipoRequest}', '{$userPl}', '{$datosRequest}', 'on', 'C-{$wbasedato}' )";
  $rs    = mysql_query( $query, $conex ) or die( respuestaErrorHttp() ) ;

  $objetoRespuesta      = array();
  $respuesta['message'] = "The movement finished up";
  $respuesta['result']  = $objetoRespuesta;
  $respuesta['status']  = 200;
  endRoutine($respuesta,200);
}

function encontrarIngresoActual( $historia ){
  global $conex,
         $wbasedato,
         $fechaActual,
         $horaActual;
  $query  = "SELECT Max(ingnin*1) as ingreso
               FROM {$wbasedato}_000101
              WHERE inghis = '{$historia}'";
  $rs     = mysql_query( $query, $conex );
  $row    = mysql_fetch_assoc( $rs );
  if(  $row['ingreso'] !== NULL ){
    return( $row['ingreso'] );
  }else{
    $respuesta['message'] = " El paciente no se encuentra activo ";
    endRoutine($respuesta,404);
  }

}

function consultarUnixAccesible(){
  global $conex,
         $wmovhos;

  $query = "SELECT *
              FROM {$wmovhos}_000012
             WHERE bd = 'inventarios'";
  $rs    = mysql_query( $query, $conex );
  $row   = mysql_fetch_assoc( $rs );
  $res   = ( $row['Odbc'] == "on" ) ? true: false;
  return( $res );
}

function homologarArticuloInternoProveedor(){
  global $conex,
          $wbasedato,
          $wmovhos,
          $fechaActual,
          $horaActual,
          $_POST, 
          $respuesta;

  //verifico que exista el articulo
  $query  = "SELECT count(*) cantidad
               FROM {$wmovhos}_000026
              WHERE Artcod = '{$_POST['internalProductCode']}'
                AND Artest = 'on'";
  $rs     = mysql_query( $query, $conex );
  $row    = mysql_fetch_assoc( $rs );
  $creado = ( $row['cantidad'] > 0 ) ? true : false;
  
  if( !$creado ){
    $respuesta['message'] = " Internal Code does not exist ";
    $respuesta['status'] = 404;
    endRoutine($respuesta,404);
  }

  //verifico homologación existente para actualizaciones o inserciones.
  $query  = "SELECT count(*) cantidad
               FROM {$wbasedato}_000009
              WHERE Axpart = '{$_POST['internalProductCode']}'
                AND Axpest = 'on'";
  $rs     = mysql_query( $query, $conex );
  $row    = mysql_fetch_assoc( $rs );
  $homologado = ( $row['cantidad'] >0 ) ? true : false;
  
  if( $homologado ){
    $query = " UPDATE {$wbasedato}_000009
                  SET Axpcba = '{$_POST['providerProductCode']}'
                WHERE Axpart = '{$_POST['internalProductCode']}'
                  AND Axpest = 'on'";
  }else{
    $query = "INSERT INTO {$wbasedato}_000009 ( Medico, Fecha_data, Hora_data, Axpart, Axpcpr, Axpest, Axpref, Axpcmp, Axpepp, Axpcon, Axpcba, Seguridad )
    VALUES ( '{$wbasedato}', '{$fechaActual}', '{$horaActual}', '{$_POST['internalProductCode']}', '{$_POST['provider']}', 'on', 'off', '1', 'PR', 'off', '{$_POST['providerProductCode']}', 'C-{$_POST['user']}')";
  }
  $rs  = mysql_query( $query, $conex ) or ( registrarErrorApi() );
  $nid = mysql_insert_id( $conex );

  $query  = "SELECT count(*) cantidad
               FROM {$wbasedato}_000322
              WHERE Salart = '{$_POST['internalProductCode']}'
                AND Salest = 'on'";
  $rs     = mysql_query( $query, $conex );
  $row    = mysql_fetch_assoc( $rs );
  $homologado = ( $row['cantidad'] >0 ) ? true : false;
  
  if( !$homologado ){
    $query = "INSERT INTO {$wbasedato}_000322 ( Medico, Fecha_data, Hora_data, Salart, Salsal, Salest, Seguridad )
    VALUES ( '{$wbasedato}', '{$fechaActual}', '{$horaActual}', '{$_POST['internalProductCode']}', '0', 'on', 'C-{$_POST['user']}')";
  }
  $rs  = mysql_query( $query, $conex ) or ( registrarErrorApi() );
  
  $respuesta['result'] = array( 'internalCode'=>$_POST['internalProductCode'],'providerProductCode'=>$_POST['providerProductCode'], 'id'=> $nid );
  $respuesta['message'] = " The Codes have been homologated successfully ";
  $respuesta['status'] = 200;
  endRoutine($respuesta,200);

}

function generarOrdenDeCompra( $producData, $fuenteXdocumento, $item, $cantItems ){
  global $wemp_pmla,
         $wbasedato,
         $conex,
         $conex_o,
         $respuesta,
         $horaActual,
         $fechaActual,
         $servicioTrasladosCons,
         $costoBrutoTotal,
         $valorIvaTotal;
  
  //consulta de la información del iva y el costo para el producto.
  $datosArticulo   = consultarArticuloUnix( $producData['productCode'] );
  $valorIva        = (($datosArticulo['costo']*$datosArticulo['factorIva'])/100)*$producData['amount'];
  $costoBrutoTotal += $datosArticulo['costo']*$producData['amount'];
  $valorIvaTotal   += $valorIva;

  //detalle
  $query  = " INSERT INTO ivorddet ( orddetfue, orddetdoc, orddetite, orddetart, orddetcan, orddetcre, orddetpru, orddetpde, orddetvde, orddetpiv, orddetviv, orddetest, orddetanu )
                VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', {$item}, '{$producData['productCode']}', {$producData['amount']}, 0, {$datosArticulo['costo']}, 0, 0, {$datosArticulo['factorIva']}, $valorIva, 'P', 0 )";
  $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

  $query = " INSERT INTO ivordent ( ordentfue, ordentdoc, ordentite, ordentcan, ordentanu )
                    VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', '{$item}', {$producData['amount']}, 0 )";
  $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

  $query = " INSERT INTO ivordmar ( ordmarfue, ordmardoc, ordmarite, ordmaranu )
                    VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', '{$item}', 0 )";
  $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

  if( $item == $cantItems ){//--> construcción del encabezado de la orden en unix con los datos acumulados;
    $datosProveedor = consultarTipoEmpresa( $producData['provider'] );
    $producData['providerErp'] = $datosProveedor['tipo'].$datosProveedor['codigo'];
    $query  = " INSERT INTO ivord ( ordfue, orddoc, ordfec, ordtip, ordpro, ordcco, ordbru, orddes, ordiva, ordest, ordfes, ordanu )
                VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', '{$fechaActual}', 'O', '{$producData['providerErp']}', '{$servicioTrasladosCons}', {$costoBrutoTotal}, 0, $valorIvaTotal, 'P', '{$fechaActual}',0 )";
    //var_dump( $query );
    $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );

    $query = " INSERT INTO ivordobs ( ordobsfue, ordobsdoc, ordobsnum, ordobsdes, ordobsanu )
                    VALUES ( '{$fuenteXdocumento['fuente']}', '{$fuenteXdocumento['documento']}', 1, 'Orden generada por Aplicacion boston', 0 )";
    $resFac = odbc_exec( $conex_o, $query ) or ( registrarErrorApi() );
  }
  grabarMovimientoInventarioMatrix( $fuenteXdocumento, "",$servicioTrasladosCons,$producData['provider'],$producData['productCode'],'','','',$producData['amount'],$datosArticulo['costo'],'boston' );
}

function consultarArticuloUnix( $codigoArticulo ){
  global $conex_o;
  $datosArticulo = array();

  $query = " SELECT artuni, artiva, artcos, artnom
               FROM ivart
              WHERE artcod = '{$codigoArticulo}'
                AND artact = 'S'";
  $resFac    = odbc_exec( $conex_o, $query );
  
  if( odbc_fetch_row( $resFac ) ){
    $datosArticulo['unidad']    = odbc_result($resFac,'artuni');
    $datosArticulo['factorIva'] = odbc_result($resFac,'artiva');
    $datosArticulo['costo']     = odbc_result($resFac,'artcos');
    $datosArticulo['nombre']     = odbc_result($resFac,'artnom');
  }
  return( $datosArticulo );
  
}

function consultarTipoEmpresa( $nitProveedor ){
  global $conex_o;
  $datosProveedor = array();

  $query = " SELECT procod, protip
               FROM cppro
              WHERE procod = '{$nitProveedor}'
                AND proact = 'S'";
  $resFac    = odbc_exec( $conex_o, $query );
  
  if( odbc_fetch_row( $resFac ) ){
    $datosProveedor['codigo']    = odbc_result($resFac,'procod');
    $datosProveedor['tipo'] = odbc_result($resFac,'protip');
  }
  return( $datosProveedor );
}
?>
