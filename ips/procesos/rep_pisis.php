<?php
include_once("conex.php");
/** REPORTE DE FACTURAS RADICADAS ANTE LAS EMPRESAS RESPONSABLES DE PAGO, ERP, EPS, EPS-S, ETC **/
/**
*** FECHA DE CREACIÓN: 2014-02-28
*** AUTOR: ING. Camilo Zapata Z.
*** DESCRIPCION: Este programa genera un documento que responde a los requerimientos del ministerio de salud en la circular conjunta 000030 anexo técnico # 2,
***              donde se exige el reporte de las facturas presentadas ante las entidades responsables de pago(EPS).

** Modificaciones:
  * 2022-01-03 - Juan Rodriguez: Se quita wemp_pmla quemado
**/
?>
<?php
if(isset($ajaxdes))
 {
  //http://www.solingest.com/blog/descarga-de-archivos-en-php
  header ("Content-Disposition: attachment; filename=".$wdesc." ");
  header ("Content-Type: application/octet-stream");
  header ("Content-Length: ".filesize($wdesc));
  readfile($wdesc);
  unlink($wdesc);
 }

if(!isset($_SESSION['user'])){
  echo "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
        [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
        </div>";
  return;
}

include_once( "conex.php" );

$wemp_pmla = $_REQUEST['wemp_pmla'];
$pos        = strpos($user,"-");
$wuser      = substr($user,$pos+1,strlen($user));

/** funciones del programa **/

//---> funcion para crear el archivo.
function crear_archivo($filename,$content,$cont){//funcion que crea el archivo.
  if($cont==1){
    if (file_exists($filename)){
      unlink($filename);
    }
    $modo1 = 'w';
    $modo2 = 'a';
  }else{
    $modo1 = 'w+';
    $modo2 = 'a';
  }

  if (!file_exists($filename))
     $reffichero = fopen($filename, $modo1);

  // Let's make sure the file exists and is writable first.
  if (is_writable($filename)){

     // In our example we're opening $filename in append mode.
     // The file pointer is at the bottom of the file hence
     // that's where $content will go when we fwrite() it.
     if (!$handle = fopen($filename, $modo2)){
        //echo "Cannot open file ($filename)";
        exit;
     }

     // Write $content to our opened file.
     if (fwrite($handle, $content) === FALSE){
         //echo "Cannot write to file ($filename)";
         exit;
     }

     //echo "Success, wrote ($content) to file ($filename)";

     fclose($handle);

  }else{
     //echo "The file $filename is not writable";
  }
}

/** function que retorna datos sobre las fuentes pertinentes, cuales afectan saldo y de qué manera( débito o crédito )**/
function arreglosFuentes(){

    global $conex, $wbasedatos;
    $arregloPpal                        = array();
    $arregloPpal['fuentesAfectanSaldo'] = "";
    $arregloPpal['fuentesCreditos']     = "";
    $arregloPpal['fuentesDebitos']      = "";
    $arregloPpal['fuentesMovimientos']  = "";

    $query =  " SELECT carfue, Carrad, Cardev, Carglo, Carcoj, Carenv
                  FROM {$wbasedatos}_000040
                 WHERE Carrad='on'
                    OR Cardev='on'
                    OR Carglo='on'
                    OR Carcoj='on'
                    OR Carenv='on'
                   AND Carest='on'
                 GROUP BY 1,2
                 ORDER BY carfue ";

    $rs    = mysql_query( $query, $conex );
    while( $row = mysql_fetch_array( $rs ) ){

        ( $arregloPpal['fuentesMovimientos'] == "" ) ? $arregloPpal['fuentesMovimientos'] = "'{$row['carfue']}'" : $arregloPpal['fuentesMovimientos'] .= " ,'{$row['carfue']}'";

        if( $row['Carrad'] == "on" ){
            $arregloPpal['radicacion'] = $row['carfue'];
        }
        if( $row['Cardev'] == "on" ){
            $arregloPpal['devolucion'] = $row['carfue'];
        }
        if( $row['Carglo'] == "on" ){
            $arregloPpal['glosas'] = $row['carfue'];
        }
        if( $row['Carcoj'] == "on" ){
            $arregloPpal['cobroJuridico'] = $row['carfue'];
        }

        if( $row['Carenv'] == 'on' ){
            $arregloPpal['envio'] = $row['carfue'];
        }
    }

    $query =  " SELECT  carfue, Carncr, Carndb, Carrec, Carcca
                  FROM {$wbasedatos}_000040
                 WHERE Carncr='on'
                    OR Carndb='on'
                    OR Carrec='on'
                   AND Carest='on'
                   AND Carabo='off'
                 GROUP BY 1,2
                 ORDER BY carfue ";

    $rs    = mysql_query( $query, $conex );
    while( $row = mysql_fetch_array( $rs ) ){

        ( $arregloPpal['fuentesAfectanSaldo'] == "" ) ? $arregloPpal['fuentesAfectanSaldo'] = "'{$row['carfue']}'" : $arregloPpal['fuentesAfectanSaldo'] .= " ,'{$row['carfue']}'";

        if( $row['Carncr'] == "on"  or $row['Carrec'] == "on" )
            ( $arregloPpal['fuentesCreditos'] == "" ) ? $arregloPpal['fuentesCreditos'] = "'{$row['carfue']}'" : $arregloPpal['fuentesCreditos'] .= " ,'{$row['carfue']}'";

        if( $row['Carndb'] == "on" )
            ( $arregloPpal['fuentesDebitos'] == "" ) ? $arregloPpal['fuentesDebitos'] = "'{$row['carfue']}'" : $arregloPpal['fuentesDebitos'] .= " ,'{$row['carfue']}'";

        if( $row['Carncr'] == "on" ){
            if( $row['Carcca']  == "on" ){
             ( $arregloPpal['creditofuenteConceptoCartera']   == "" ) ?   $arregloPpal['creditofuenteConceptoCartera']  =  "'{$row['carfue']}'" : $arregloPpal['creditofuenteConceptoCartera'] .= " ,'{$row['carfue']}'";
            }else{
              ( $arregloPpal['creditoNOfuenteConceptoCartera']   == "" ) ?  $arregloPpal['creditoNOfuenteConceptoCartera']  =  "'{$row['carfue']}'" : $arregloPpal['creditoNOfuenteConceptoCartera'] .= " ,'{$row['carfue']}'";
            }
        }

    }

    return( $arregloPpal );
}

/** funcion que retorna datos sobre las entidades registraadas en el sistema, nit, codigos, codigos asociados a un mismo nit, etc.**/
function arregloEntidadesResponsablesPago(){

    global $wbasedatos, $conex;
    $empresas            = array();
    $empresas['nombres'] = array();

    //---> consulta de maestro de entidades
    $q = " SELECT empcod, empnit, Empraz as empnom, '' prefijo, empres "
        ."   FROM " . $wbasedatos . "_000024 "
        ."  WHERE empcod = empres "
        ."  UNION ALL"
        ." SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit, SUBSTRING_INDEX( Empraz , '-', -1 ) as empnom, 'EMP -' prefijo, empres "
        . "  FROM " . $wbasedatos . "_000024 "
        . " WHERE empcod != empres "
        . " GROUP BY emptem "
        . " ORDER BY empnom ";

    $res = mysql_query($q, $conex); //or die (mysql_errno()." - ".mysql_error());
    $num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());

    while( $row = mysql_fetch_array( $res ) ){

        if(!isset($empresas[$row['empnit']]['codigos']))
            $empresas[$row['empnit']]['codigos'] = array();

        array_push( $empresas[$row['empnit']]['codigos'], "'".$row['empcod']."'" );

        if(!isset($empresas[$row['empnit']]['responsable']))
            $empresas[$row['empnit']]['responsable'] = array();

        array_push( $empresas[$row['empnit']]['responsable'], $row['empres'] );

        $empresas['nombres'][$row['empnit']."-".$row['empcod']] = $row['empnom'];
    }
    foreach( $empresas as $key => $datos ){
      if( $key != "nombres" ){
        $empresas[$key]['codigos'] = implode(", ", $empresas[$key]['codigos'] );
      }
    }
    return( $empresas );

}

/****/
function buscarDatosEmpresa(){
    global $wbasedatos, $conex, $wemp_pmla;
    $query = "SELECT Empnit nit, Empdes razonSocial
                FROM root_000050
               WHERE Empcod = '{$wemp_pmla}'";
    $rs  = mysql_query( $query, $conex );
    $row = mysql_fetch_array( $rs );
    return( $row );
}

/* Funcion que busca por cada factura los movimientos( Radicaciones, Devoluciones, glosas y cobros jurídicos realizados a una factura )*/
function movimientosFactura( $fuenteFactura, $factura ){

    global $arregloPpal, $wbasedatos, $conex, $consolidado, $arregloEntidades, $temp2021notasGlosas, $temp2021estados;
    $datos      = array();
    $contestado = false;

    /** se consultan los movimientos de cada factura (Envio, Radicaciones, Devoluciones, Glosas)**/
    $query = " SELECT fuenteDocumento, max(fechaMovimiento) fechaMovimiento, 'radicacion' movimiento, '' valorRecibo, max(numeroDocumento*1) numeroDocumento, max( idDocumento )
                 FROM {$consolidado} a
                WHERE fuenteFactura   = '{$fuenteFactura}'
                  AND factura         = '{$factura}'
                  AND fuenteDocumento = '{$arregloPpal['radicacion']}'
                GROUP BY fuenteDocumento, movimiento, valorRecibo
                UNION ALL
               SELECT fuenteDocumento, max(fechaMovimiento) fechaMovimiento, 'devolucion' movimiento, '' valorRecibo, max(numeroDocumento*1) numeroDocumento, max( idDocumento )
                 FROM {$consolidado} a
                WHERE fuenteFactura   = '{$fuenteFactura}'
                  AND factura         = '{$factura}'
                  AND fuenteDocumento = '{$arregloPpal['devolucion']}'
                GROUP BY fuenteDocumento, movimiento, valorRecibo
                UNION ALL
               SELECT fuenteDocumento, max(fechaMovimiento) fechaMovimiento, 'glosas' movimiento, sum(valorRecibo), max(numeroDocumento*1) numeroDocumento, max( idDocumento )
                 FROM {$consolidado} a
                WHERE fuenteFactura   = '{$fuenteFactura}'
                  AND factura         = '{$factura}'
                  AND fuenteDocumento = '{$arregloPpal['glosas']}'
                GROUP BY fuenteDocumento, movimiento, valorRecibo
                UNION ALL
               SELECT fuenteDocumento, max(fechaMovimiento) fechaMovimiento, 'cobroJuridico' movimiento, '' valorRecibo, max(numeroDocumento*1) numeroDocumento, max( idDocumento )
                 FROM {$consolidado} a
                WHERE fuenteFactura   = '{$fuenteFactura}'
                  AND factura         = '{$factura}'
                  AND fuenteDocumento = '{$arregloPpal['cobroJuridico']}'
                GROUP BY fuenteDocumento, movimiento, valorRecibo";

    $rs1   = mysql_query( $query, $conex ) or die( mysql_error() );
    while( $row1  = mysql_fetch_array( $rs1 ) ){


        $datos[$row1['movimiento']]['fechaMovimiento'] = $row1['fechaMovimiento'];
        $datos[$row1['movimiento']]['fuenteDocumento'] = $row1['fuenteDocumento'];
        $datos[$row1['movimiento']]['valorCancelado']  = $row1['valorRecibo'];
        $datos[$row1['movimiento']]['contestado']      = "NO";

        if( $row1['movimiento'] == 'glosas' ){
          $datos[$row1['movimiento']]['valorCancelado']  = "";

          //acá voy a buscar si tiene una nota crédito asociada a esa glosa.
           $query = " SELECT valorCancelado
                        FROM {$temp2021notasGlosas}
                       WHERE Rdeglo = '{$row1['fuenteDocumento']}-{$row1['numeroDocumento']}'
                         AND fuenteFactura   = '{$fuenteFactura}'
                         AND factura         = '{$factura}' ";
           $rs2    = mysql_query( $query, $conex ) or die( mysql_error() );
           $datos['movimiento']['valorCancelado'] = "";
           while( $row2 = mysql_fetch_array( $rs2 ) ){
              $datos[$row1['movimiento']]['valorCancelado'] = $row2['valorCancelado'];
              $datos[$row1['movimiento']]['contestado'] = "SI";
              $contestado = true;
           }

           if( !$encontrado ){
              /* si la factura tiene glosas pero aún no tiene notas, se buscan reenvios para saber si fue contestada. */
              $query = " SELECT count(*)
                           FROM {$temp2021estados}
                          WHERE fuenteDocumento = '{$arregloPpal['envio']}'
                            AND fechaMovimiento > '".$row1['fechaMovimiento']."'
                            AND fuenteFactura   = '{$fuenteFactura}'
                            AND factura         = '{$factura}'";
              $rsrespuesta  = mysql_query( $query, $conex ) or die( mysql_error() );
              $rowrespuesta = mysql_fetch_array( $rsrespuesta );
              if( $rowrespuesta > 0 ){
                $datos[$row1['movimiento']]['valorCancelado'] = 0;
                $datos[$row1['movimiento']]['contestado'] = "SI";
              }else{
                $datos[$row1['movimiento']]['contestado'] = "NO";
              }
           }
        }
    }

    /** acá podría consultar todos los datos de las glosas **/
    return($datos);
}

/** en esta función se miran los movimientos de saldo( notas y recibos ) de una factura para verificar saldo a la fecha de corte **/
function movimientosSaldoFactura( $fuenteFactura, $factura, $nitResponsable, $codigoResponsable, $tablaMovimientos, $fechaRadicacion ){
    global $conex, $wbasedatos, $arregloPpal, $arregloEntidades;

    $arrayValores = array();
    $arrayValores['restarValor'] = 0;
    $arrayValores['sumerValor']  = 0;
    if( ( $arregloEntidades[$nitResponsable]['codigos'] ) != "" ){
      $query = " SELECT *
                   FROM (

                         SELECT fuenteDocumento, numeroDocumento, valor, fechaMovimiento, responsableMovimiento, max(idDocumento) idDocumento, 'restarValor' accion, 'credito' tipo
                           FROM  {$tablaMovimientos}
                          WHERE fuenteDocumento in ( {$arregloPpal['fuentesCreditos']} )
                            AND fuenteFactura = '{$fuenteFactura}'
                            AND factura       = '{$factura}'
                            AND fechaMovimiento < '{$fechaRadicacion}'
                            AND responsableMovimiento in (".$arregloEntidades[$nitResponsable]['codigos'].")
                          GROUP BY fuenteDocumento, numeroDocumento, fechaMovimiento, responsableMovimiento
                          UNION
                          SELECT fuenteDocumento, numeroDocumento, sum(valor) valor, fechaMovimiento, responsableMovimiento, max(idDocumento) idDocumento, 'restarValor' accion, 'debito' tipo
                           FROM  {$tablaMovimientos}
                          WHERE fuenteDocumento in ( {$arregloPpal['fuentesDebitos']} )
                            AND fuenteFactura = '{$fuenteFactura}'
                            AND factura       = '{$factura}'
                            AND fechaMovimiento < '{$fechaRadicacion}'
                            AND responsableMovimiento in (".$arregloEntidades[$nitResponsable]['codigos'].")
                          GROUP BY fuenteDocumento, numeroDocumento, fechaMovimiento, responsableMovimiento
                          UNION
                          SELECT fuenteDocumento, numeroDocumento, valor, fechaMovimiento, responsableMovimiento, max(idDocumento) idDocumento, 'sumarPagos' accion, 'credito' tipo
                           FROM  {$tablaMovimientos}
                          WHERE fuenteDocumento in ( {$arregloPpal['fuentesCreditos']} )
                            AND fuenteFactura = '{$fuenteFactura}'
                            AND factura       = '{$factura}'
                            AND fechaMovimiento >= '{$fechaRadicacion}'
                            AND responsableMovimiento in (".$arregloEntidades[$nitResponsable]['codigos'].")
                          GROUP BY fuenteDocumento, numeroDocumento, fechaMovimiento, responsableMovimiento
                          UNION
                          SELECT fuenteDocumento, numeroDocumento, valor, fechaMovimiento, responsableMovimiento, max(idDocumento) idDocumento, 'sumarPagos' accion, 'debito' tipo
                           FROM  {$tablaMovimientos}
                          WHERE fuenteDocumento in ( {$arregloPpal['fuentesDebitos']} )
                            AND fuenteFactura = '{$fuenteFactura}'
                            AND factura       = '{$factura}'
                            AND fechaMovimiento >= '{$fechaRadicacion}'
                            AND responsableMovimiento in (".$arregloEntidades[$nitResponsable]['codigos'].")
                          GROUP BY fuenteDocumento, numeroDocumento, fechaMovimiento, responsableMovimiento

                       ) a
                   ORDER BY fechaMovimiento desc, idDocumento desc";

      $rs = mysql_query( $query ) or die( mysql_error()."-<br>".$nitResponsable." este es el error" );
      while( $row = mysql_fetch_array( $rs ) ){

          if( $row['accion'] == "restarValor" ){

              if( $row['tipo'] == "credito" ){
                  $arrayValores['restarValor'] += $row['valor'];
              }else{
                  $arrayValores['sumarValor']  += $row['valor'];
              }
          }else{

              if( $row['tipo'] == "credito" ){
                  $arrayValores['sumarPagos']  += $row['valor'];
              }else{
                  $arrayValores['restarPagos'] += $row['valor'];
              }

          }
      }
    }else{
      $arrayValores['restarValor'] = 0;
      $arrayValores['sumarValor']  = 0;
      $arrayValores['sumarPagos']  = 0;
      $arrayValores['restarPagos'] = 0;
    }
    return( $arrayValores );
}

function verificarReenvio(){
}

function completarNit( $nit ){
  $cantidadCaracteres = strlen( $nit );
  for( $i = $cantidadCaracteres+1; $i <= 12; $i++ ){
    $nit = "0".$nit;
  }
  return( $nit );
}

function imprimirDatos($datos, $nombre_archivo){//funcion que agrega cada registro al archivo.
    $cont =0;
    $regs = sizeof($datos);
    for($i=0; $i<($regs); $i++)//empieza en -1 para que la primer vez que entre cree el archivo.
    {
      if($i==$regs-1)
        $contenido = $datos[$i];
      else
        $contenido = $datos[$i]."
";

      if($contenido != '')
      {
        $cont++;
        crear_archivo($nombre_archivo,$contenido,$cont); // lo crea en el mismo directorio.
      }
    }
}

function registrarFacturaReportada( $fuente, $factura, $wyear, $periodo ){
  global $conex, $wbasedatos, $wemp_pmla, $wuser, $accion, $wfechaIni;

  //if( $accion == "insertar"){
    $query = " INSERT INTO {$wbasedatos}_000212 ( Medico, Fecha_data, Hora_data, Repffa, Repfac, Repest, Repano, Repper, Repfei, Seguridad )
                                        VALUES  ( '{$wbasedatos}', '".date('Y-m-d')."','".date('h:i:s')."', '{$fuente}', '{$factura}', 'on', '{$wyear}', '{$periodo}','{$wfechaIni}', 'C-{$wuser}')";
    $rs    = mysql_query( $query, $conex ) or die( mysql_error() );
  //}
  return;
}

if( isset( $peticionAjax ) ){

    $wbasedatos       = $_REQUEST['wbasedatos'];
    $wfechaIni        = $_REQUEST['wfechaIni'];
    $wfechaFin        = $_REQUEST['wfechaFin'];
    $arregloPpal      = arreglosFuentes();
    $arregloEntidades = arregloEntidadesResponsablesPago();
    $datosEmpresa     = buscarDatosEmpresa();
    $nitEmpresa       = $datosEmpresa['nit'];
    $razonSocial      = $datosEmpresa['razonSocial'];
    $nitEmpresaTitulo = completarNit( $nitEmpresa );
    $fechaCorteTitulo = str_replace("-", "", $wfechaFin);
    $nombreDocumento  = "SAC165FIPS{$fechaCorteTitulo}NI{$nitEmpresaTitulo}.txt";
    $contenido        = "";
    $registros        = array();

    /*Esta tabla temporal contiene los documentos que son notas */
    $temp65= "temp65".date("His")."estados";

    $query = "CREATE  TABLE IF NOT EXISTS $temp65
                      (INDEX idx1( Fdefue, Fdedoc ), INDEX idx( Fdeffa, Fdefac) )
              SELECT Fdefue, Fdedoc, Fdeffa, Fdefac, Fdevco
                FROM {$wbasedatos}_000065
               WHERE Fdefue in ( {$arregloPpal['creditofuenteConceptoCartera']} )
                 AND Fdeest = 'on'";
    $rs    = mysql_query( $query, $conex );

    /** Query: hacemos una temporal de la tabla de movimientos en el rango de fechas */
    $temp20= "temp20".date("His")."estados";
    $query = "CREATE  TABLE IF NOT EXISTS $temp20
                      (INDEX idx1( renfue, rennum ) )
              SELECT renfec, renfue, rennum, rencod, renest, rencco, renvca
                FROM {$wbasedatos}_000020
               WHERE renfec between '{$wfechaIni}' and '{$wfechaFin}'
                 AND Renest = 'on'";
    $rs    = mysql_query( $query, $conex ) or die( mysql_error() );


    /** Query: consulta todos las notas crédito asociadas a glosas **/
    $temp2021notasGlosas= "notasGlosaspisis".date("His")."estados";

    $query = "CREATE  TABLE IF NOT EXISTS $temp2021notasGlosas
                      (INDEX idx1( Rdeglo ), INDEX idx5( fuenteFactura, factura ), INDEX idx( fuenteDocumento(2), numeroDocumento, factura), INDEX idx2( factura ),
                       INDEX idx6( fuenteEncabezado(2), numeroEncabezado ), INDEX idx7( fechaMovimiento ) )
              SELECT a.renfec fechaMovimiento, b.rdefue fuenteDocumento, b.rdenum numeroDocumento, b.rdefac factura,
                     b.rdeffa fuenteFactura, a.renfue fuenteEncabezado, a.rennum numeroEncabezado, rencod codigoResponsable, sum(fdevco) valorCancelado, Rdeglo, 'conceptos'
                FROM {$temp20} a
               INNER JOIN
                     {$wbasedatos}_000021 b  on ( renfue = rdefue AND rennum = rdenum AND rencco = rdecco )
               INNER JOIN
                     $temp65 c on ( fdeffa = rdeffa and fdefac = rdefac AND fdefue = Rdefue AND fdedoc = Rdenum )
               WHERE rdeest = 'on'
                 AND rdefue in ( {$arregloPpal['creditofuenteConceptoCartera']} )
                 AND rdeglo != '' and rdeglo != 'NO APLICA'
               GROUP BY 1,2,3,4,5,6,7, 8
               UNION ALL
               SELECT a.renfec fechaMovimiento, c.rdefue fuenteDocumento, c.rdenum numeroDocumento, c.rdefac factura,
                     c.rdeffa fuenteFactura, a.renfue fuenteEncabezado, a.rennum numeroEncabezado, rencod codigoResponsable, Renvca valorCancelado, Rdeglo, 'renvca'
                FROM {$temp20} a
               INNER JOIN
                     {$wbasedatos}_000021 c  on ( renfue = rdefue AND rennum = rdenum AND rencco = rdecco )
               WHERE rdeest = 'on'
                 AND rdefue in ( {$arregloPpal['creditoNOfuenteConceptoCartera']} )
                 AND rdeglo != '' and rdeglo != 'NO APLICA'
               GROUP BY 1,2,3,4,5,6,7, 8, 9";
    $res = mysql_query( $query, $conex ) or die( mysql_errno(). " - Error en el query $query - ".mysql_error() );


    /** busco todas las facturas que han sido radicadas en el rango de fechas, es decir las que han sido enviadas a la entiddad responsable de pago en el periodo definido**/

    $tempradicaciones= "radicacionespisis".date("His");
    $query = "CREATE temporary  TABLE IF NOT EXISTS $tempradicaciones
                      (INDEX idx5( fuenteFactura, factura ), INDEX idx( fuenteDocumento(2), numeroDocumento, factura), INDEX idx2( factura ),
                       INDEX idx6( fuenteEncabezado(2), numeroEncabezado ), INDEX idx7( fechaMovimiento ) )
              SELECT b.rdesfa saldo, a.renfec fechaMovimiento, b.rdefue fuenteDocumento, b.rdenum numeroDocumento, b.rdefac factura, b.rdeffa fuenteFactura,
                     a.renfue fuenteEncabezado, a.rennum numeroEncabezado, b.id, rencod codigoResponsable, rdevca valorRecibo
                FROM {$temp20} a
               INNER JOIN
                     {$wbasedatos}_000021 b  on ( renfue = rdefue AND rennum = rdenum AND rencco = rdecco )
               WHERE rdeest = 'on'
                 AND rdefue = '{$arregloPpal['radicacion']}'
               ORDER BY factura, b.id desc";

    $res = mysql_query( $query, $conex ) or die( mysql_errno(). " - Error en el query $query - ".mysql_error() );

    $temp= "Temp18pisis".date("His");

    $query="CREATE temporary TABLE IF NOT EXISTS $temp
           (INDEX idx( codigoResponsable, tipoResponsable ), INDEX idx2( codigoResponsable, nitResponsable ), INDEX idx3( codigoResponsable ), INDEX idx7( fechaFactura ) )

           SELECT Fensal saldoFactura, Fecha_data, Hora_data, Fenfec fechaFactura, Fenffa fuente, Fenfac factura, Fentip tipoResponsable, Fennit nitResponsable,
                  Fencod codigoResponsable, Fenres, Fenval valorFactura, a.id, fenesf estadoFactura
             FROM {$wbasedatos}_000018 a
             INNER JOIN
                  {$tempradicaciones} on ( fenffa = fuenteFactura and fenfac = factura )
             WHERE fenest = 'on'";

    $resFactura = mysql_query( $query, $conex ) or die( mysql_errno(). " - Error en el query $query - ".mysql_error() );

    /** Query: consulta todos los movimientos (radicaciones) **/
    $temp2021estados= "Temp2021pisis".date("His")."estados";

    $query = "CREATE  TABLE IF NOT EXISTS $temp2021estados
                      (INDEX idx5( fuenteFactura, factura ), INDEX idx( fuenteDocumento(2), numeroDocumento, factura), INDEX idx2( factura ),
                       INDEX idx6( fuenteEncabezado(2), numeroEncabezado ), INDEX idx7( fechaMovimiento ) )
              SELECT b.rdesfa saldo, a.renfec fechaMovimiento, b.rdefue fuenteDocumento, b.rdenum numeroDocumento, b.rdefac factura, b.rdeffa fuenteFactura,
                     a.renfue fuenteEncabezado, a.rennum numeroEncabezado, b.id, rencod codigoResponsable, rdevca valorRecibo
                FROM {$temp20} a
               INNER JOIN
                     {$wbasedatos}_000021 b  on ( renfue = rdefue AND rennum = rdenum AND rencco = rdecco )
               WHERE rdeest = 'on'
                 AND rdefue in ( {$arregloPpal['fuentesMovimientos']} )
               ORDER BY factura, b.id desc";

    $res = mysql_query( $query, $conex ) or die( mysql_errno(). " - Error en el query $query - ".mysql_error() );

    /** Query: consulta todos los movimientos que mueven saldo dentro del rando de fecha inicial y fecha de corte **/
    $temp2021movimientos= "Temp2021pisis".date("His")."movimientos";

    $query = "CREATE TABLE IF NOT EXISTS $temp2021movimientos
                      (INDEX idx5( fuenteFactura, factura ), INDEX idx( fuenteDocumento(2), numeroDocumento, factura), INDEX idx2( factura ),
                       INDEX idx7( fechaMovimiento ) )
              SELECT a.renfec fechaMovimiento,  b.rdefue fuenteDocumento, b.rdenum numeroDocumento, b.rdefac factura, b.rdeffa fuenteFactura,
                     b.id idDocumento, rencod responsableMovimiento, b.rdevca valor
                FROM {$temp20} a
               INNER JOIN
                     {$wbasedatos}_000021 b  on ( renfue = rdefue AND rennum = rdenum AND rencco = rdecco )
               WHERE renfec between '{$wfechaIni}' and '{$wfechaFin}'
                 AND rdeest = 'on'
                 AND renest = 'on'
                 AND rdefue in ( {$arregloPpal['fuentesAfectanSaldo']} )
                 AND rdeglo = ''
               ORDER BY factura, idDocumento desc";

    $res = mysql_query( $query, $conex ) or die( mysql_errno(). " - Error en el query $query - ".mysql_error() );

     /** Query: join entre las facturas que hay entre el rango de fechas y los movimientos hasta la fecha de corte **/
    $consolidado= "Tempconsolidadopisis".date("His");
    $queryConso = "CREATE TABLE IF NOT EXISTS $consolidado
                          (INDEX idx5( fuenteFactura, factura ), INDEX idx( fuenteDocumento(2), numeroDocumento, factura ), INDEX idx2( factura ),
                           INDEX idx7( fechaMovimiento ) )
                  SELECT a.fuente fuenteFactura, a.factura, valorFactura, estadoFactura, fuenteDocumento, numeroDocumento, saldo saldoRecibo, fechaMovimiento,
                         nitResponsable, a.codigoResponsable, Fenres, b.codigoResponsable responsableMovimiento, tipoResponsable, b.id idDocumento, a.fechaFactura, valorRecibo
                    FROM {$temp2021estados} b
                   INNER JOIN
                         {$temp} a  on ( a.fuente = b.fuenteFactura and a.factura = b.factura )";

    $res = mysql_query( $queryConso, $conex ) or die( mysql_errno(). " - Error en el query $query - ".mysql_error() );

    $queryFinal = " SELECT nitResponsable, codigoResponsable, fuenteFactura, factura, fechaFactura, valorFactura, valorRecibo, Repfei
                      FROM {$consolidado} a
                      LEFT JOIN
                           {$wbasedatos}_000212 on ( Repffa = fuenteFactura and Repfac = factura and Repest = 'on' and Repfei < '{$wfechaIni}')
                     GROUP BY nitResponsable, codigoResponsable, fuenteFactura, factura, fechaFactura, valorFactura
                     ORDER BY nitResponsable, codigoResponsable, fuenteFactura, factura";
    $resFinal   = mysql_query( $queryFinal, $conex ) or die( mysql_error() );
    $consecutivo = 0;
    //echo "<center><table style='font-size:10px;' border='1'>";
    //echo "<tr class='encabezadotabla'><td>tipo Registro</td>";
                //echo "<td>consecutivo</td>";
                //echo "<td>tipo<br>identificación</td>";
                //echo "<td>identificacion erp</td>";
                //echo "<td> razón social</td>";
                //echo "<td>tipo de identificación NIT</td>";
                //echo "<td>identificacion</td>";
                //echo "<td> tipo de cobro </td>";
                //echo "<td> prefijo factura</td>";
                //echo "<td> factura</td>";
                //echo "<td> actualización </td>";
                //echo "<td> valorFactura </td>";
                //echo "<td> fecha factura</td>";
                //echo "<td> fecha radicación</td>";
                //echo "<td> fecha Devolucion</td>";
                //echo "<td> total pagos </td>";
                //echo "<td> valor Glosa aceptada </td>";
                //echo "<td> </td>";
                //echo "<td> saldofactura </td>";
                //echo "<td> cobro jurídico NO </td>";
                //echo "<td> etapa jurídico </td>";
    //echo "</tr>";
    $nrofacturas = mysql_num_rows( $resFinal );
    if( $nrofacturas > 0 ){
      $query = " SELECT COUNT(*)
                 FROM {$wbasedatos}_000211
                WHERE Renano = '{$wyear}'
                  AND Renper = '{$periodo}'
                  AND Renest = 'on'";

      $rs  = mysql_query( $query, $conex );
      $num = mysql_fetch_array( $rs );

      if( $num[0] > 0 ){
        $accion = "actualizar";

        $query = " DELETE
                     FROM {$wbasedatos}_000212
                    WHERE Repano = '{$wyear}'
                      AND Repper = '{$periodo}'
                      AND Repest = 'on'";
        $rs  = mysql_query( $query, $conex );
        $query = " DELETE
                     FROM {$wbasedatos}_000211
                    WHERE Renano = '{$wyear}'
                      AND Renper = '{$periodo}'
                      AND Renest = 'on'";
        $rs  = mysql_query( $query, $conex );
      }else{
        $accion = "insertar";
      }

      //if( $accion == "insertar" ){
        $query = " INSERT INTO {$wbasedatos}_000211 ( Medico, Fecha_data, Hora_data, Renano, Renper, Renest, Seguridad )
                                 VALUES  ( '{$wbasedatos}', '".date('Y-m-d')."','".date('h:i:s')."', '{$wyear}', '{$periodo}', 'on', 'C-{$wuser}')";
        $rs    = mysql_query( $query, $conex ) or die( mysql_error() );
      //}
    }

    while( $row = mysql_fetch_array( $resFinal ) ){
        $movimientos         = movimientosFactura( $row['fuenteFactura'], $row['factura'] );
        $saldoRecibos        = movimientosSaldoFactura( $row['fuenteFactura'], $row['factura'], $row['nitResponsable'], $row['codigoResponsable'], $temp2021movimientos, $movimientos['radicacion']['fechaMovimiento'] );
        $pagosRecibidos      = $saldoRecibos['sumarPagos'] - $saldoRecibos['restarPagos'];
        $valorFactura        = $row['valorFactura']*1 - $saldoRecibos['restarValor']*1 + $saldoRecibos['sumarValor']*1;
        $row['saldoFactura'] = $valorFactura - $pagosRecibidos - $movimientos['glosas']['valorCancelado'];
        if( $valorFactura > $row['valorFactura']*1 )
          $valorFactura .= "apl" ;
         ( trim( $row['Repfei']) != "" ) ? $indicativo = "A" : $indicativo = "I";
        $consecutivo ++;
        //echo "<tr><td>2</td>"; //tipo registro
                //echo "<td>$consecutivo</td>"; //consecutivo registro
                //echo "<td>NIT</td>"; //tipo identificación erp
                //echo "<td>".$row['nitResponsable']."</td>"; //identificacion erp
                //echo "<td>".$arregloEntidades['nombres'][$row['nitResponsable']."-".$row['codigoResponsable']]."</td>"; // razón social erp
                //echo "<td>NIT</td>"; //tipo de identificación ips( clinica )
                //echo "<td>".$nitEmpresa."</td>"; //identificacion clinica
                //echo "<td>F</td>"; // tipo de cobro ????
                //echo "<td>".$row['fuenteFactura']."</td>"; // prefijo factura (# nro de recobro erp ???? )
                //echo "<td>".$row['factura']."</td>"; // número de la factura
                //echo "<td>".$indicativo."</td>"; // indicador de actualización de la factura



                //echo "<td>".$valorFactura."</td>"; // indicador de actualización de la factura
                //echo "<td>".$row['fechaFactura']."</td>"; // fecha de emisión de la factura
                //echo "<td>".$movimientos['radicacion']['fechaMovimiento']."</td>"; // fecha de presentación de la factura( fecha de la radicación?????)
                //echo "<td>".$movimientos['devolucion']['fechaMovimiento']."</td>"; // fecha de Devolucion

                //echo "<td>{$pagosRecibidos}</td>"; // valor total pagos aplicados a dicha factura

                //si no tiene registro de glosa aceptada el valor de la glosa es cero y se consulta si fue respondida
                if( !isset($movimientos['glosas']['valorCancelado']) ){
                    ///* si tiene envio posterior a qué?
                    $glosaRespondida = "NO";
                }else{
                    ( $movimientos['glosas']['valorCancelado']*1 > 0 ) ? $glosaRespondida = "SI" : $glosaRespondida = "NO";

                }

                //echo "<td> ".$movimientos['glosas']['valorCancelado']." </td>"; // valor Glosa aceptada
                //echo "<td> ".$glosaRespondida." </td>"; // glosa respondida???
                //echo "<td> ".$row['saldoFactura']." </td>"; // saldo de la factura
                ( isset($movimientos['cobroJuridico']) ) ? $cobroJuridico = "SI" : $cobroJuridico = "NO";
                //echo "<td>".$cobroJuridico."</td>"; // se encuentra en cobro jurídico
                //echo "<td> 1 </td>"; // etapa en la que se encuentra en cobro jurídico
        //echo "</tr>";

       /** registro que se va a almacenar en el archivo **/
        $aux     = explode( "-", $row['factura'] );
        $prefijo = $aux[0];
        $prefijo = $row['fuenteFactura'];
        $factura = $row['factura'];
        registrarFacturaReportada( $row['fuenteFactura'], $row['factura'], $wyear, $periodo );
        $registros[$consecutivo]  =  "2,{$consecutivo},NIT,".$row['nitResponsable'].",".$arregloEntidades['nombres'][$row['nitResponsable']."-".$row['codigoResponsable']].",NIT,".$nitEmpresa;
        $registros[$consecutivo] .= ",F,".$prefijo.",".$factura.",{$indicativo},".number_format($valorFactura,2,".","").",".$row['fechaFactura'].",".$movimientos['radicacion']['fechaMovimiento'];

        $registros[$consecutivo] .= ",".$movimientos['devolucion']['fechaMovimiento'].",".number_format($pagosRecibidos,2,".","")."";
                //si no tiene registro de glosa aceptada el valor de la glosa es cero y se consulta si fue respondida
                if( !isset($movimientos['glosas']['valorCancelado']) ){
                    ///* si tiene envio posterior a qué?
                    $movimientos['glosas']['valorCancelado'] = 0;
                    $glosaRespondida = "NO";
                }else{
                   $glosaRespondida = $movimientos['glosas']['contestado'];
                }
        $registros[$consecutivo] .= ",".number_format($movimientos['glosas']['valorCancelado'],2,".","").",".$glosaRespondida.",".number_format($row['saldoFactura'],2,".","");
                ( isset($movimientos['cobroJuridico']) ) ? $cobroJuridico = "SI" : $cobroJuridico = "NO";
        $registros[$consecutivo] .=",".$cobroJuridico.",1";

    }
    //echo "</table></center>";

    if( sizeof( $registros ) > 0 ){

      $registros[0] = "1,2,NI,{$nitEmpresa},{$razonSocial},{$wfechaIni},{$wfechaFin},".$consecutivo."";
      imprimirDatos( $registros, $nombreDocumento );
      echo "<div id='desarc' align='center'>
              <a id='warchi' onclick='quitarEnlace(this);' target='_new' name='warchi' href='rep_pisis.php?ajaxdes=pisis&wdesc={$nombreDocumento}&wemp_pmla=".$wemp_pmla."'>DESCARGAR ARCHIVO PISIS</a>
            </div>";

    }

    $query  = "DROP TABLE IF EXISTS {$consolidado};";
    $rs = mysql_query( $query, $conex );
    $query  = "DROP TABLE IF EXISTS {$temp2021estados};";
   // $rs = mysql_query( $query, $conex );
    $query = "DROP TABLE IF EXISTS {$temp2021movimientos};";
    $rs = mysql_query( $query, $conex );
    $query = "DROP TABLE IF EXISTS {$temp2021notasGlosas};";
    $rs = mysql_query( $query, $conex );
    $query = "DROP TABLE IF EXISTS {$temp20};";
    $rs = mysql_query( $query, $conex );
    $query = "DROP TABLE IF EXISTS {$temp65};";
    $rs = mysql_query( $query, $conex );
    return;
}

?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title>GENERACI&Oacute;N DOCUMENTO PARA EL FIPS</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<style type="text/css">
    /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
    .ui-datepicker {font-size:12px;}

    /* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
    .ui-datepicker-cover {
        display: none; /*sorry for IE5*/
        display/**/: block; /*sorry for IE5*/
        position: absolute; /*must have*/
        z-index: -1; /*must have*/
        filter: mask(); /*must have*/
        top: -4px; /*must have*/
        left: -4px; /*must have*/
        width: 100px; /*must have*/
        height: 100px; /*must have*/
    }
</style>
<script>
    $.datepicker.regional['esp'] = {
        closeText: 'Cerrar',
        prevText: 'Antes',
        nextText: 'Despues',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
        'Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['esp']);
</script>

<script type="text/javascript">

    $( document ).ready( function(){

        $(".inputFechas").datepicker({
             showOn: "button",
             buttonImage: "../../images/medical/root/calendar.gif",
             buttonImageOnly: true,
             changeYear:true,
             reverseYearRange: true,
             changeMonth: true,
             maxDate: $("#wfechahoy").val()
        });

        wbasedatos = $("#wbasedatos");
        wemp_pmla  = $("#wemp_pmla");
        wyear      = $("#wyear");
       /* wfecini    = $("#wfecini");
        wfecfin    = $("#wfecfin");*/
        usuario    = $("#wusuario");

    } );

    function generar(){
        periodo    = $("#wtrimestre").find("option:selected");
        wfecini    = $(periodo).attr("wfecini");
        wfecfin    = $(periodo).attr("wfecfin");
        periodoval = $("#wtrimestre").val();
        $("#msjEspere").show();
        $.ajax({

                   url: "rep_pisis.php",
                  type: "POST",
                  data: {
                          peticionAjax: "generarInforme",
                             wfechaIni: wyear.val()+wfecini,
                             wfechaFin: wyear.val()+wfecfin,
                                 wyear: wyear.val(),
                            wbasedatos: wbasedatos.val(),
                             wemp_pmla: wemp_pmla.val(),
                             usuario  : usuario.val(),
                             periodo  : periodoval
                        },
                  success: function(data)
                  {
                    $("#msjEspere").hide();
                    $("#msjRespuesta").html(data);
                    $("#msjRespuesta").show();
                  }

        });
    }

    function quitarEnlace(obj){
      obj.parentNode.removeChild(obj);
    }
</script>
<body>
<?php

include_once( "root/comun.php" );

$wactualiz   = "2022-01-03";
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedatos  = $institucion->baseDeDatos;
$wyear       = date("Y");

encabezado("GENERACI&Oacute;N DOCUMENTO FIPS",$wactualiz, $wbasedatos); //fips = facturas reportadas por ips.

?>
    <form name='rep_pisis' action='rep_pisis.php?wemp_pmla=<?php echo $wemp_pmla ?>' method='post'>
        <center>
            <table width='500'>
                <tr>
                    <td class='encabezadotabla' colspan='4' align='center'>ELIJA EL RANGO DE FECHAS QUE DESEA GENERAR</td>
                </tr>
                <!--<tr class='fila1'>
                    <td align='left'> FECHA INICIAL: </td><td> <input class='inputFechas' type='text' size='10' id='wfecini'> </td>
                    <td align='left'> FECHA FINAL:   </td><td> <input class='inputFechas' type='text' size='10'id='wfecfin'> </td>
                </tr>-->
                <tr class='fila1'>
                    <td align='left' colspan='1'> TRIMESTRE: </td>
                    <td colspan='1'>
                      <SELECT class='select' id='wtrimestre'>
                        <?php

                          $query = "SELECT Tricod codigo, Triini inicio, Trifin final, Tridef Definicion
                                      FROM {$wbasedatos}_000213
                                     WHERE Triest = 'on'
                                    ORDER BY codigo asc";
                          $rs    = mysql_query( $query, $conex );
                          while( $row = mysql_fetch_array( $rs ) ){
                            echo "<option wfecini='-{$row['inicio']}' wfecfin='-{$row['final']}' value='{$row['codigo']}'> {$row['Definicion']} </option>";
                          }
                        ?>
                      </SELECT>
                    </td>
                    <td align='left' colspan='1'> AÑO: </td>
                    <td colspan='1'>
                      <SELECT class='select' id='wyear'>
                          <?php

                          for ($i=2014; $i >= 2006 ; $i--) {
                            ( $i == 2014 ) ? $selected ='selected' : $selected = '';
                            echo "<option  $selected value='{$i}'> $i </option>";
                          }
                          ?>
                      </SELECT>
                    </td>
                </tr>
                <tr align=center>
                    <td colspan='4'><input align='center' type='button' value='ACEPTAR' onclick='generar()'></td>
                </tr>
            </table>
        </center>
        <input type='hidden' name='wemp_pmla' id='wemp_pmla' value='<?php echo $wemp_pmla ?>'>
        <input type='hidden' name='wbasedatos' id='wbasedatos' value='<?php echo $wbasedatos ?>'>
        <input type='hidden' name='wusuario' id='wusuario' value='<?php echo $wuser ?>'>
        <input type='hidden' name='wfechahoy' id='wfechahoy' value='<?php echo date('Y-m-d') ?>'>
        <!--<input type='hidden' name='wyear' id='wyear' value='<?php echo $wyear ?>'>-->
        <div id='desarc' align='center'></div>
    </form>
    <br>
    <center><div id='msjEspere' style='display:none;'>
        <br /><br />
        <img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Espere por Favor</font>
        <br /><br /><br />
    </div></center>
    <div id='msjRespuesta' name='msjRespuesta' style='display:none;'>
    </div>
    <center><table>
    <tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>
    </table></center>
</body>
</html>
