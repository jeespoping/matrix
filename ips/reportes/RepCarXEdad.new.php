<?php
include_once("conex.php");
/**
    * NOMBRE:  REPORTE DE CARTERA POR EDADES
    *
    * PROGRAMA: RepCarXEdad.php
    * TIPO DE SCRIPT: PRINCIPAL
    * //DESCRIPCION:Este reporte presenta la lista de notas debito o notas credito o recibos de caja con sus detalles por empresa o para todas las empresas
    *    con sus saldos de cartera por edades o rangos de tiempo escogidos por el usuario
    *
    * HISTORIAL DE ACTAULIZACIONES:
    * 2014-02-10 Camilo Zapata  - Se hizo una corrección a los cambios realizados por edwin, los cuales estaban usando las fechas de la última radicación de cada movimiento para sacar la edad,
    *                             cuando se debe usar es la fecha de radicación y el corte.
    * 2012-11-30 Edwin Molina G - Se modifica el reporte para que las edades las cuenta a partir de la ultima fecha de radicado
    * 2012-09-26 Camilo Zapata. - se modificó el script para que permita seleccionar el estado actual de las facturas(radicada-glosada-generada-devuelta)
    * 2012-09-24 Camilo Zapata. - se mejoró el query para que tenga en cuenta que los movimientos correspondan al mismo responsable de la factura consultando los posibles rencod en la 24.
    * 2012-09-20 Camilo Zapata. - se comentó el cambio anterior.
    *                           - se cambiaron los querys que consultan la 21 para que tenga en cuenta el último movimiento realizado, no el de menor saldo lineas(553 y 1056)
    * 2012-09-14 Camilo Zapata. se modificó el script para que compare por saldo de la factura de la 18 en caso de no encontrar valor de saldo en la 21(buscar donde dice ojo)
    * 2006-06-20 carolina castano, creacion del script
    * 2006-10-12 carolina castano, cambios de forma, presentación
    * 2007-02-20 carolina castano, se adecua para que los rangos de las edades sean escogidos por el usuario de los configurados en base de datos
    * 2007-08-15 carolina castano, se muestra el tipo de empresa en el reporte resumido
    * 2008-03-28 se muestra comenta el query que retomaba la fecha de corte
    * 2011-07-22 Creación de tablas temporales para mejorar la velocidad en consulta del script - Mario Cadavid
    * 2011-07-25 Modificación de diseño adaptando el reporte a la hoja de estilos actual de Matrix - Mario Cadavid
    * 2011-07-26 Modificación de los case's que definen los rangos de edades y se deshace el cambio hecho en 2008-03-28
    *            ya que se debe tener la fecha de corte por cada ciclo - Mario Cadavid
    * 2012-08-24 Se agregó la consulta de los registros de empresas con el mismo NIT de la empresa responsable, esto para
    *            que en las consultas por NIT muestre los registros asociados al NIT de la empresa responsable, es decir, no solo la
    *            cartera directa de la empresa sino tambien la de sus empleados. - Mario Cadavid
    *
    * Tablas que utiliza:
    * $wbasedato."_000024: Maestro de Fuentes, select
    * $wbasedato."_000018: select de facturas entre dos fechas
    * $wbasedato."_000020: select en encabezado de cartera
    * $wbasedato."_000021: select en detalle de cartera
    * $wbasedato."_000080: select de rangos para las edades
    *
    * @author ccastano
    * @package defaultPackage
*/
?>
<?php
session_start();

if( !isset($_SESSION['user']) ){
    echo "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
            [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
        </div>";
    return;
}





//********************  FUNCIONES ********************************************
/** FUNCIONES **/
function buscarRangosEdades( $wran ){

    global $wbasedato, $conex;
    $rangos = array();
    $select = "";

    if (isset($wran)){
        $q = "   SELECT rcacod, rcarai, rcaraf, rcaord "
         . "     FROM " . $wbasedato . "_000080 "
         . "    WHERE rcacod != (mid('" . $wran . "',1,instr('" . $wran . "','-')-1)) "
         . "      AND rcaest = 'on' order by rcacod, rcaord";
    }else{
        $q = "   SELECT rcacod, rcarai, rcaraf, rcaord "
             . "     FROM " . $wbasedato . "_000080 "
             . "    WHERE rcaest = 'on' order by rcacod, rcaord";
    }
    $res1 = mysql_query($q, $conex) or die( mysql_error() );
    $num1 = mysql_num_rows($res1);

    while( $row = mysql_fetch_array( $res1 ) ){

        if( !( isset( $rangos[ $row['rcacod'] ] ) ) )
            $rangos[ $row['rcacod'] ] = array();

        if( !in_array( $row['rcarai'], $rangos[ $row['rcacod'] ] ) )
            array_push( $rangos[ $row['rcacod'] ], $row['rcarai'] );

    }

    foreach( $rangos as $keyRango => $datosRango ){
        $datosDelRango = implode( ", ", $datosRango );
        $select .=  "<option value='".$keyRango."-".$datosDelRango."'>{$keyRango} - Rango {$keyRango} - ( $datosDelRango ) </option>";
    }
    return( $select );
}

function listadoResponsables( $wemp, $crearSelect ){

    global $wbasedato, $conex;
    $select              = "";
    $empresas            = array();
    $empresas['nombres'] = array();
    $q = " SELECT empcod, empnit, empnom, '' prefijo, empres, emptem "
        ."   FROM " . $wbasedato . "_000024 "
        ."  WHERE empcod = empres "
        ."  UNION ALL"
        ." SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom, 'EMP -' prefijo, empres, emptem "
        . "  FROM " . $wbasedato . "_000024 "
        . " WHERE empcod != empres "
        . " GROUP BY emptem "
        . " ORDER BY empnom ";

    $res = mysql_query($q, $conex); //or die (mysql_errno()." - ".mysql_error());
    $num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());
    if( $crearSelect == "si" ){
        if( $num > 0 )
            $select = "<option codigo='%' nit='%'>% - Todas las empresas</option>";
        while( $row = mysql_fetch_array( $res ) ){
            $select .=  "<option codigo='{$row['empcod']}' nit='{$row['empnit']}'> ".$row['prefijo']." " . $row['empcod'] . " - " . $row['empnit'] . " - " . $row['empnom'] . "</option>";
        }
    }else{
            while( $row = mysql_fetch_array( $res ) ){
                if(!isset($empresas['nits'][$row['empnit']]['codigos']))
                    $empresas['nits'][$row['empnit']]['codigos'] = array();

                array_push( $empresas['nits'][$row['empnit']]['codigos'], $row['empcod'] );

                if(!isset($empresas['nits'][$row['empnit']]['responsable']))
                    $empresas['nits'][$row['empnit']]['responsable'] = array();

                array_push( $empresas['nits'][$row['empnit']]['responsable'], $row['empres'] );

                $empresas['nombres'][$row['empnit']."-".$row['empcod']] = $row['empnom'];
                $empresas['codigos'][$row['empcod']] = $row['empnit'];
                $empresas['tipos'][$row['empcod']] = $row['emptem'];
            }
            return( $empresas );
    }
    return( $select );
}

function listadoEstadoFactura( $westado ){
    global $conex, $wbasedato;
    $select = "";

    $q = " SELECT estcod, estdes "
        ."   FROM ".$wbasedato."_000144 "
        ."  WHERE estest='on'"
        ."  ORDER BY 1 ";
        $res = mysql_query( $q, $conex );
        $num = mysql_num_rows( $res );
    $select .= "<option value='%-Todos'>Todos</option>";
    for ($i=1;$i<=$num;$i++)
    {
        $row = mysql_fetch_array($res);
        $select .= "<option value=".$row[0]."-".$row[1].">".$row[0]."-".$row[1]."</option>";
    }
    return( $select );
}

function consultarEstadoFactura( $wesf ){
    global $wbasedato, $conex;
    $filtroEstadoFactura = "";
    if($wesf!='Todos')
    {
        $wesf=explode("-",$wesf);
        $wesfConsultar=$wesf[0];
        $wesf=$wesf[1];
        $estados[$wesfConsultar]=$wesf;
        $filtroEstadoFactura=" AND Fenesf = '{$wesfConsultar}'";
    }else
        {
            $q =  " SELECT estcod, estdes "
                ."    FROM ".$wbasedato."_000144 "
                ."   WHERE estest='on'"
                ."   ORDER BY 1 ";
                $res = mysql_query($q,$conex) or die( mysql_error() );
                $num = mysql_num_rows($res);
            while($row=mysql_fetch_array($res))
            {
                $estados[$row[0]]=$row[1];
            }

        }

    return( $filtroEstadoFactura );
}

function construirArregloRangos( $datosRango ){

    $arreglo   = array();
    $aux       = explode( "-", $datosRango );
    $aux       = explode( ",",$aux[1] );
    $limiteInf = "";

    for( $i = 0; $i <= count($aux); $i++ ){
        $limite = $aux[$i];
        if( $i == 0 ){
            $limiteInf = $limite;
        }else{
            if( $i == count( $aux ) ){
                $arreglo[( (trim($aux[$i-1]))*1 + 1 )." + "]['limiteInferior'] = (trim($aux[$i-1]))*1;

            }else{
                $arreglo[trim($limiteInf)."-".trim($limite)." DIAS"]['limiteInferior'] = $limiteInf*1;
                $arreglo[trim($limiteInf)."-".trim($limite)." DIAS"]['limiteSuperior'] = $limite*1;
                $limiteInf = $limite + 1 ;
            }
        }
    }
    return( $arreglo );
}

function construirArregloEstadosFacturas( $wesf ){

    global $wbasedato, $conex;
    $estados = array();
    $q =  " SELECT estcod, estdes "
        ."    FROM ".$wbasedato."_000144 "
        ."   WHERE estest='on'"
        ."   ORDER BY 1 ";
        $res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);
    while($row=mysql_fetch_array($res))
    {
        $estados[$row[0]]=$row[1];
    }
    return( $estados );
}

function construirArregloEdadesDetalle( $entidad, $arregloRangos ){
    global $carteraEdadesXentidad;
    foreach( $arregloRangos as $keyRango=>$datos ){
        $carteraEdadesXentidad[$entidad][$keyRango] = 0;
    }
}

function imprimirTotalesEntidad( $empresaAnterior, $arregloRangos, $sumaParcial ){
    global $carteraEdadesParcial;
    $total .= "<tr class='encabezadotabla'><td colspan='4'> TOTAL ".$empresaAnterior."</td>";
    foreach( $arregloRangos as $keyRango => $datos ){

        $total .= "<td align='right'>".number_format($carteraEdadesParcial[$keyRango], 0, '.', ',')."</td>";
        $carteraEdadesParcial[$keyRango] = 0;
    }
    $total .= "<td align='right'>".number_format($sumaParcial, 0, '.', ',')."</td>";
    $total .= "</tr>";
    return( $total );
}

function imprimirTotalesCartera( $arregloRangos, $arregloTotales, $sumaTotal ){
    global $carteraEdadesParcial;
    $total .= "<tr class='encabezadotabla'><td colspan='4' align='center'> TOTAL CARTERA</td>";
    foreach( $arregloRangos as $keyRango => $datos ){

        $total .= "<td align='right'>".number_format($arregloTotales[$keyRango], 0, '.', ',')."</td>";
    }
    $total .= "<td align='right'>".number_format($sumaTotal, 0, '.', ',')."</td>";
    $total .= "</tr>";
    return( $total );
}

function arreglosFuentes(){

    global $conex, $wbasedato;
    $arregloPpal                        = array();
    $arregloPpal['fuentesAfectanSaldo'] = "";
    $arregloPpal['fuentesCreditos']     = "";
    $arregloPpal['fuentesDebitos']      = "";

    $query =  " SELECT  carfue, Carncr, Carndb, Carrec
                  FROM {$wbasedato}_000040
                 WHERE Carncr='on'
                    OR Carndb='on'
                    OR Carrec='on'
                   AND Carest='on'
                 GROUP BY 1,2
                 ORDER BY carfue ";

    $rs    = mysql_query( $query, $conex );
    while( $row = mysql_fetch_array( $rs ) ){

        ( $arregloPpal['fuentesAfectanSaldo'] == "" ) ? $arregloPpal['fuentesAfectanSaldo'] = "'{$row['carfue']}'" : $arregloPpal['fuentesAfectanSaldo'] .= " ,'{$row['carfue']}'";

        if( $row['Carncr'] == "on"  or $row['Carrec'] == "on" )
            ( $arregloPpal['fuentesCreditos'] == "" ) ? $arregloPpal['fuentesCreditos'] = "'{$row['carfue']}'" : $arregloPpal['fuentesCreditos'] .= " ,'{$row['carfue']}'";

        if( $row['Carndb'] == "on" )
            ( $arregloPpal['fuentesDebitos'] == "" ) ? $arregloPpal['fuentesDebitos'] = "'{$row['carfue']}'" : $arregloPpal['fuentesDebitos'] .= " ,'{$row['carfue']}'";

    }
    return( $arregloPpal );
}

function construirReporteResumido( $arregloResultante, $arregloRangos ){

    $sumaParcial   = 0;
    $sumaTotal     = 0;
    $parcialEdades = array();
    $totalEdades   = array();

    $tabla .="<center><table id='tbl_informe' width='100%'>";
    $tabla .= "<tr class='encabezadotabla'><td colspan='4' width='60%'>&nbsp;</td><td align='center' width='10%'> TIPO </td>";

    foreach( $arregloRangos as $keyRango => $datos ){

        $tabla .= "<td align='center'>$keyRango</td>";

    }

    $tabla .= "<td align='center'>TOTAL CARTERA</td>";
    $tabla .= "</tr>";
    foreach ($arregloResultante as $tipoResponsable => $empresas ){

        $i         = 0;
        $sumaParcial = 0;

        foreach( $empresas as $keyCodigo => $datosEmpresa ){

            if( count($empresas[$keyCodigo] > 0 )){
                $i++;
                ( is_int($i/2) ) ? $wclass = "class='fila1'" : $wclass = "class='fila2'";
                $sumaParcial += $datosEmpresa['totalCartera'];
                $sumaTotal   += $datosEmpresa['totalCartera'];
                $tabla .= "<tr {$wclass}><td colspan='4'><b>{$datosEmpresa['nombre']}</b></td><td align='center'><b>{$tipoResponsable}</b></td>";
                foreach( $arregloRangos as $keyRango => $datos ){

                    $tabla .= "<td align='right'>".number_format($datosEmpresa['edades'][$keyRango], 0, '.', ',')."</td>";
                    $parcialEdades[$keyRango] += $datosEmpresa['edades'][$keyRango];

                }
                $tabla .= "<td align='right'><b>".number_format($datosEmpresa['totalCartera'], 0, '.', ',')."</b></td>";
                $tabla .= "</tr>";
            }

        }

        if( count($empresas) > 0 ){

             $tabla .= "<tr class='encabezadotabla'><td colspan='5'> TOTAL ".$tipoResponsable."</td>";
             foreach( $arregloRangos as $keyRango => $datos ){

                 $tabla .= "<td align='right'>".number_format($parcialEdades[$keyRango], 0, '.', ',')."</td>";
                 $totalEdades[$keyRango]   += $parcialEdades[$keyRango];
                 $parcialEdades[$keyRango] = 0;

             }
             $tabla .= "<td align='right'>".number_format($sumaParcial, 0, '.', ',')."</td>";
             $tabla .= "</tr>";
        }
    }
    $tabla .= "<tr class='encabezadotabla'><td colspan='5' align='left'> CARTERA TOTAL TODAS LAS ENTIDADES: &nbsp;</td>";

    foreach( $arregloRangos as $keyRango => $datos ){

        $tabla .= "<td align='center'>".number_format($totalEdades[$keyRango], 0, '.', ',')."</td>";

    }
    $tabla .= "<td align='center'>".number_format($sumaTotal, 0, '.', ',')."</td>";
    $tabla .= "</tr>";

    $tabla .= "</table>";
    return( $tabla );
}

function calcularEdad( $fuente, $factura, $radicaciones, $fechaFactura ){

    global $conex, $wbasedato, $wfeccor;
    $fechaBase = "";

    /*$query = " SELECT fecha_data
                 FROM {$radicaciones}
                WHERE rdeffa = '{$fuente}'
                  AND rdefac = '{$factura}'";*/
    $query = "SELECT
                a.Fecha_data
            FROM
                {$wbasedato}_000021 a, {$wbasedato}_000040 b
            WHERE rdeffa = '$fuente'
                AND Rdefac = '$factura'
                AND rdefue = carfue
                AND carest = 'on'
                AND carrad = 'on'
                AND a.Fecha_data <= '$wfeccor'
            ORDER BY
                a.fecha_data desc
                Limit 1;
            ";
    $rs = mysql_query( $query, $conex ) or die( mysql_error() );

    if( mysql_num_rows( $rs ) > 0 ){

        $row       = mysql_fetch_array( $rs );
        $fechaBase = $row['Fecha_data'];
    }
    if( $fechaBase == "" )
        $fechaBase = $fechaFactura;

     // parto la fecha de generacion de la factura
    $dia       = substr( $fechaBase, 8, 2 ); // pasar el dia a una variable
    $mes       = substr( $fechaBase, 5, 2 ); // pasar el mes a una variable
    $anyo      = substr( $fechaBase, 0, 4 ); // pasar el año a una variable

    // hasta la fecha de corte
    $diacor       = substr( $wfeccor, 8, 2 ); // pasar el dia a una variable
    $mescor       = substr( $wfeccor, 5, 2 ); // pasar el mes a una variable
    $anyocor      = substr( $wfeccor, 0, 4 ); // pasar el año a una variable

    $segundosbase  = mktime(0, 0, 0, $mes, $dia, $anyo); // calcular cuantos segundos han pasado desde 1970
    $segundoscorte = mktime(0, 0, 0, $mescor, $diacor, $anyocor); // calcular cuantos segundos han pasado desde 1970

    $segundosEdad = $segundoscorte - $segundosbase;
    $segundosEdad = $segundosEdad / 86400;
    return( $segundosEdad );
}

/**  SEGMENTO PARA PROCESAR PETICIONES AJAX **/
if( $peticionAjax == "generarReporte" ){

    $wfecini               = $_REQUEST['wfecini'];
    $wfecfin               = $_REQUEST['wfecfin'];
    $wfeccor               = $_REQUEST['wfeccor'];
    $wtip                  = $_REQUEST['wtip'];
    $wran                  = $_REQUEST['wran'];
    $wemp                  = $_REQUEST['wemp'];
    $wesf                  = $_REQUEST['wesf'];
    $arregloEmpresas       = listadoResponsables( $wemp, "no" );
    $arregloRangos         = construirArregloRangos( $wran );
    $filtroEstadoFactura   = consultarEstadoFactura( $wesf );
    $estados               = construirArregloEstadosFacturas( $wesf );
    $arregloPpal           = arreglosFuentes();
    $arregloFacturas       = array();
    $arregloDocumentos     = array();
    $arregloTotalEmpresas  = array();
    $carteraEdadesXentidad = array();
    $carteraEdadesParcial  = array();
    $carteraEdadesTotal    = array();
    $wauxEsf1              = explode( "-", $wesf );
    $wesf1                 = $wauxEsf1[0];
    $wesf2                 = $wauxEsf1[1];
    $condicionBusqueda18   = "";
    $condicionBusqueda2021 = "";
    $ordenamiento1         = "";
    $ordenamiento2         = "";

    if( $wtipoRepo == "NO"){ //significa que se va a generar un reporte resumido

        $arregloEntidadResumen = array();
        $arregloTiposEmpresa   = array();

        $query = " SELECT distinct( emptem )
                     FROM {$wbasedato}_000024
                    WHERE empest = 'on'
                    ORDER by emptem ";
        $rs    = mysql_query( $query, $conex );

        while( $row = mysql_fetch_array( $rs ) ){

            $arregloTiposEmpresa[strtoupper(trim( $row['emptem'] ))] =  array();
        }
        $ordernarTipo = "tipoResponsable, ";
    }else{
        $ordernarTipo = "";
    }

    /** encabezado del informe**/
    echo "<table  align=center width='60%'>";
        echo "<tr><td class='textoNormal'><B>Fecha: " . date('Y-m-d') . "</B></td></tr>";
        if ($vol == 'SI')
            echo "<tr><td class='textoNormal'><B>REPORTE DE CARTERA POR EDADES DETALLADO</B></td></tr>";
        if ($vol == 'NO')
            echo "<tr><td><B>REPORTE DE CARTERA POR EDADES  RESUMIDO</B></td></tr>";
        if ($vol == 'RE')
            echo "<tr><td><B>REPORTE DE CARTERA POR EDADES  RESUMIDO CON PARTICULARES DETALLADO</B></td></tr>";
        echo "<tr><td class='fila2' height='27'><b>Fecha inicial:</b> " . $wfecini . " &nbsp; &nbsp;&nbsp; &nbsp; <b>Fecha final:</b> " . $wfecfin . " &nbsp; &nbsp;&nbsp; &nbsp; <b>Fecha de corte:</b> " . $wfeccor . "</td></tr>";
        echo "<tr><td class='fila2' height='27'><b>Clasificado por:</b> " . $wtip . "</td></tr>";
        echo "<tr><td class='fila2' height='27'><b>Estado de Facturas:</b> " . $wesf2 . "</td></tr>";
    echo "</table><br/>";

    /** Se organizan las condiciones de busqueda para cuando se filtra por una empresa y por un tipo de busqueda de esta,**/
    if( $wemp != "%" ){
        ( $wtip == 'codigo' ) ? $condicionBusqueda18   = " AND fencod = '{$wemp}' " : $condicionBusqueda18   = " AND fennit = '{$wemp}' ";
        ( $wtip == 'codigo' ) ? $condicionBusqueda2021 = " AND rencod = '{$wemp}' " : $condicionBusqueda2021 = " AND rencod IN '{$arregloEmpresas['nits'][$wemp]['codigos']}' ";
    }

    /*if( $wtip == "nit" )    $ordenamiento1 = "nitResponsable" ;   $ordenamiento2 = "codigoResponsable";
    if( $wtip == "codigo" ) $ordenamiento1 = "codigoResponsable"; $ordenamiento2 = "nitResponsable";*/
     if( $wtip == "nit" )    $ordenamiento1 = "nitResponsable" ;   $ordenamiento2 = "Fenres";
    if( $wtip == "codigo" ) $ordenamiento1 = "Fenres"; $ordenamiento2 = "nitResponsable";
    ( $wesf1 != "%" ) ? $filtroEstadoFactura = " AND fenesf = '{$wesf1}' " : $filtroEstadoFactura = "";

    ( $wtipoRepo == "NO" ) ? $resumido = "style='display:none;' " : $resumido = "";

    /** Query: consulta todas las facturas Generadas dentro del rango de fechas definido fecha inicial - fecha final **/
    $temp= "Temp18".date("His");

    $query="CREATE TEMPORARY TABLE IF NOT EXISTS $temp
           (INDEX idx( codigoResponsable, tipoResponsable ), INDEX idx2( codigoResponsable, nitResponsable ), INDEX idx3( codigoResponsable ), INDEX idx7( fechaFactura ) )

           SELECT Fensal saldoFactura, Fecha_data, Hora_data, Fenfec fechaFactura, Fenffa fuente, Fenfac factura, Fentip tipoResponsable, Fennit nitResponsable,
                  Fencod codigoResponsable, Fenres, Fenval valorFactura, id, fenesf estadoFactura
             FROM {$wbasedato}_000018
            WHERE fenfec between '$wfecini' AND '$wfecfin'
                  {$condicionBusqueda18}
              AND fenest = 'on'
              AND fencco <> ''
              {$filtroEstadoFactura}";
    //echo $query."<br>";

    $resFactura = mysql_query( $query, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

    /** Query: consulta todos los movimientos que mueven saldo dentro del rando de fecha inicial y fecha de corte **/
    $temp2021= "Temp2021".date("His");

    $query = "CREATE TEMPORARY TABLE IF NOT EXISTS $temp2021
                      (INDEX idx5( fuenteFactura, factura ), INDEX idx( fuenteDocumento(2), numeroDocumento, factura), INDEX idx2( factura ),
                       INDEX idx6( fuenteEncabezado(2), numeroEncabezado ), INDEX idx7( fechaMovimiento ) )
              SELECT b.rdesfa saldo, a.renfec fechaMovimiento, b.rdefue fuenteDocumento, b.rdenum numeroDocumento, b.rdefac factura, b.rdeffa fuenteFactura,
                     a.renfue fuenteEncabezado, a.rennum numeroEncabezado, b.id, rencod codigoResponsable
                FROM {$wbasedato}_000020 a
               INNER JOIN
                     {$wbasedato}_000021 b  on ( renfue = rdefue AND rennum = rdenum AND rencco = rdecco )
               WHERE renfec <= '{$wfeccor}'
                 AND rdeest = 'on'
                 AND renest = 'on'
                 AND rdesfa <>''
                 AND rdefue in ( {$arregloPpal['fuentesAfectanSaldo']} )
                 AND rdereg = 0
               ORDER BY factura, b.id desc";
    //echo $query."<br> renfec between '{$wfecini}' and '{$wfeccor}' reemplazar para mejorar";

    $res = mysql_query( $query, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

     /** Query: join entre las facturas que hay entre el rango de fechas y los movimientos hasta la fecha de corte **/
    $consolidado= "Tempconsolidado".date("His");
    $queryConso = "CREATE TABLE IF NOT EXISTS $consolidado
                          (INDEX idx5( fuenteFactura, factura ), INDEX idx( fuenteDocumento(2), numeroDocumento, factura ), INDEX idx2( factura ),
                           INDEX idx7( fechaMovimiento ) )
                  SELECT a.fuente fuenteFactura, a.factura , valorFactura, estadoFactura, fuenteDocumento, numeroDocumento, saldo saldoRecibo, fechaMovimiento,
                         nitResponsable, a.codigoResponsable, Fenres, b.codigoResponsable responsableMovimiento, tipoResponsable, b.id idDocumento, a.fechaFactura
                    FROM {$temp} a
                    LEFT JOIN
                         {$temp2021} b  on ( a.fuente = b.fuenteFactura and a.factura = b.factura )";
    //echo $query."<br>";

    $res = mysql_query( $queryConso, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

    if( trim( $arregloPpal['fuentesDebitos'] ) != "" ){
        $unionFuentesDebito = "UNION
                                SELECT fuenteFactura, factura,valorFactura, estadoFactura, fuenteDocumento, numeroDocumento, MAX(saldoRecibo*1) saldoRecibo, fechaMovimiento, nitResponsable, codigoResponsable, Fenres, tipoResponsable, responsableMovimiento, fechaFactura, max(idDocumento) idDocumento
                                 FROM  {$consolidado}
                                WHERE fuenteDocumento in ( {$arregloPpal['fuentesDebitos']} )
                                GROUP BY fuenteFactura, factura,valorFactura, estadoFactura, fuenteDocumento, numeroDocumento, fechaMovimiento, nitResponsable, codigoResponsable, Fenres, ResponsableMovimiento, fechaFactura";
    }else{
        $unionFuentesDebito = "";
    }
    $queryFinal = " SELECT *
                      FROM (
                               SELECT fuenteFactura, factura,valorFactura, estadoFactura, fuenteDocumento, numeroDocumento, MIN(saldoRecibo*1) saldoRecibo, fechaMovimiento, nitResponsable, codigoResponsable, Fenres, tipoResponsable, responsableMovimiento, fechaFactura, max(idDocumento) idDocumento
                                 FROM  {$consolidado}
                                WHERE fuenteDocumento in ( {$arregloPpal['fuentesCreditos']} )
                                GROUP BY fuenteFactura, factura,valorFactura, estadoFactura, fuenteDocumento, numeroDocumento, fechaMovimiento, nitResponsable, codigoResponsable, Fenres, ResponsableMovimiento, fechaFactura
                                {$unionFuentesDebito}
                                UNION
                                SELECT fuenteFactura, factura,valorFactura, estadoFactura, fuenteDocumento, numeroDocumento, saldoRecibo*1, fechaMovimiento, nitResponsable, codigoResponsable, Fenres, tipoResponsable, responsableMovimiento, fechaFactura, idDocumento
                                  FROM {$consolidado}
                                 WHERE fuenteDocumento IS NULL
                            ) a
                    ORDER BY {$ordenamiento1} desc, {$ordenamiento2}, factura, fechaMovimiento desc, idDocumento desc";
                    //ORDER BY {$ordernarTipo} {$ordenamiento1} desc, {$ordenamiento2}, factura, fechaMovimiento desc, idDocumento desc";

    //echo $queryFinal."<br>";
    $resFinal   = mysql_query( $queryFinal, $conex ) or die( mysql_error()."<br>".$queryFinal );

    $radicaciones = "TempRadicaciones".date("His");
   /* $query = "CREATE TABLE IF NOT EXISTS $radicaciones
                          (INDEX idx( rdeffa, rdefac )
              SELECT a.rdeffa, a.rdefac, MAX(a.Fecha_data)
                FROM
                    {$wbasedato}_000040 b, {$wbasedato}_000021 a
               WHERE carrad = 'on'
                 AND carest = 'on'
                 AND rdefue = carfue
                 AND a.Fecha_data <= '$wfeccor'
               GROUP BY 1,2";
    echo $query;
    $rs    = mysql_query( $query, $conex );*/

    /** se arma un arreglo con todas las facturas y sus datos, los necesarios para armar el reporte **/
    $facturaAnterior        = "";
    $empresaAnteriorControl = "";
    $carteraResponsables    = array();
    $sumaParcial            = 0; // esta variable va a ir sumando la cartera de cada entidad.
    $numeroEmpresa          = 0; //variable intera que se incrementa para contar las cantidad de empresas encontradas

    $tablaDetalle .= "<center><table id='tbl_informe' width='100%'>";
     $k = 0;
    while( $row = mysql_fetch_array( $resFinal ) ){
        $k++;
        $facturaActual     = $row['fuenteFactura']."-".$row['factura'];
        $verificarNit      = true;
        $asignarValorTotal = true;

        if( $facturaActual != $facturaAnterior ){
            /* if( $row['factura'] == 'A-207405' )
                echo "<br> 1 ".$row['codigoResponsable'];*/
             if($row['codigoResponsable'] != $row['Fenres']){
                $row['codigoResponsable'] = $row['Fenres'];
                $row['nitResponsable']    = $arregloEmpresas['codigos'][$row['codigoResponsable']];
             }
             $row['tipoResponsable'] = $arregloEmpresas['tipos'][$row['codigoResponsable']];

             /*if( $row['factura'] == 'A-207405' )
                echo "<br> 2 ".$row['codigoResponsable']." - ".$row['nitResponsable'];*/

            if( $row['idDocumento'] == "(NULL)" or trim( $row['idDocumento'] ) == "" ){
                $asignarValorTotal = true;

            }else{

                if( !isset( $arregloEmpresas['nits'][trim($row['nitResponsable'])]['codigos'] ) )
                    $arregloEmpresas['nits'][trim($row['nitResponsable'])]['codigos'] = array();

                if( $row['responsableMovimiento'] == $row['codigoResponsable'] ){
                    $verificarNit      = false;
                    $asignarValorTotal = false;
                    $sumar             = $row['saldoRecibo'];
                }

                if( $verificarNit AND in_array( $row['responsableMovimiento'], $arregloEmpresas['nits'][trim($row['nitResponsable'])]['codigos'] ) ){
                    $sumar             = $row['saldoRecibo'];
                    $asignarValorTotal = false;
                }
            }
            if( $asignarValorTotal ){
                    $sumar = $row['valorFactura'];
            }
            if( $sumar > 0 or $sumar < 0){

                if( !isset($arregloTotalEmpresas[$row[$ordenamiento1]] ) ){

                    $empresaActual = $row[$ordenamiento1];
                    /** aca se verifica el cambio de responsable para mostrar el total de este**/
                    $empresaActualControl =  $row[$ordenamiento1]."-".$row[$ordenamiento2]."-".$arregloEmpresas['nombres'][$row['nitResponsable']."-".$row['codigoResponsable']];

                    if( $empresaActual != $empresaAnterior && $empresaAnterior != "" ){

                       $numeroEmpresa++;
                       $tablaDetalle .= imprimirTotalesEntidad( $empresaAnteriorControl, $arregloRangos, $sumaParcial );
                       $sumaParcial = 0;
                    }

                    $arregloTotalEmpresas[$row[$ordenamiento1]] += $sumar;
                    $tablaDetalle .= "<tr class='fila1' numeroEmpresa='{$numeroEmpresa}' tipo='padre' style='cursor:pointer;' onclick='ocultarMostrarDetalle( this );'><td colspan='".(count($arregloRangos)+6)."'> <b>EMPRESA: ".$empresaActualControl."</b></td></tr>";
                    $tablaDetalle .= "<tr class='encabezadotabla'><td width='5%' align='center'> FUENTE<br>FACTURA</td><td width='15%' align='center'> FACTURA</td><td width='10%' align='center'>FECHA FACTURA</td><td width='10%' align='center'>ESTADO<br> FACTURA</td>";
                    foreach( $arregloRangos as $keyRango => $datos ){

                        $tablaDetalle .= "<td width='10%' align='center'>$keyRango</td>";

                    }
                        $tablaDetalle .= "<td width='20%' align='center'>TOTAL</td>";
                    $tablaDetalle .= "</tr>";
                    $i = 0;
                }

                $i++;
                $edad = calcularEdad( $row['fuenteFactura'], $row['factura'], $radicaciones, $row['fechaFactura'] );
                ( is_int($i/2) ) ? $wclass = "class='fila1'" : $wclass = "class='fila2'";
                $tablaDetalle .= "<tr {$wclass} numeroEmpresa='{$numeroEmpresa}' tipo='hijo' {$resumido}>";
                    $tablaDetalle .= "<td align='center'>{$row['fuenteFactura']}</td>";
                    $tablaDetalle .= "<td align='center'>{$row['factura']}</td>";
                    $tablaDetalle .= "<td align='center'>{$row['fechaFactura']}</td>";
                    $tablaDetalle .= "<td align='center'>".$estados[$row['estadoFactura']]."</td>";

                $l = 0;
                foreach( $arregloRangos as $keyRango => $datos ){
                    $l++;
                    if( $l == count($arregloRangos) ){
                         if( $edad >= $datos['limiteInferior'] ){
                            $escribir = number_format($sumar, 0, '.', ',');
                            $carteraEdadesParcial[$keyRango] += $sumar;
                            $carteraEdadesTotal[$keyRango]   += $sumar;
                            $arregloTiposEmpresa[strtoupper(trim($row['tipoResponsable']))][$row[$ordenamiento1]]['edades'][$keyRango] += $sumar;
                         }else{
                                 $escribir = 0;
                                 $carteraEdadesParcial[$keyRango] += 0;
                                 $carteraEdadesTotal[$keyRango]   += 0;
                                 $arregloTiposEmpresa[strtoupper(trim($row['tipoResponsable']))][$row[$ordenamiento1]]['edades'][$keyRango] += 0;
                              }
                    }else{
                        if( $edad >= $datos['limiteInferior']*1 and $edad <= $datos['limiteSuperior']*1 ){
                            $escribir = number_format($sumar, 0, '.', ',');
                            $carteraEdadesParcial[$keyRango] += $sumar;
                            $carteraEdadesTotal[$keyRango]   += $sumar;
                            $arregloTiposEmpresa[strtoupper(trim($row['tipoResponsable']))][$row[$ordenamiento1]]['edades'][$keyRango] += $sumar;
                        }else{
                            $carteraEdadesParcial[$keyRango] += 0;
                            $carteraEdadesTotal[$keyRango]   += 0;
                            $arregloTiposEmpresa[strtoupper(trim($row['tipoResponsable']))][$row[$ordenamiento1]]['edades'][$keyRango] += 0;
                            $escribir = 0;
                        }
                    }
                    $tablaDetalle .= "<td align='right'>{$escribir}</td>";

                }
                    $tablaDetalle .= "<td align='right'>".number_format($sumar, 0, '.', ',')."</td>";
                $tablaDetalle .= "</tr>";


                $carteraResponsables[$row[$ordenamiento1]] += $sumar;

                $arregloTiposEmpresa[strtoupper(trim($row['tipoResponsable']))][$row[$ordenamiento1]]['totalCartera'] += $sumar;

                if( !isset( $arregloTiposEmpresa[strtoupper(trim($row['tipoResponsable']))][$row[$ordenamiento1]]['nombre']) )
                    $arregloTiposEmpresa[strtoupper(trim($row['tipoResponsable']))][$row[$ordenamiento1]]['nombre'] = $empresaActualControl;
                $carteraTotal                              += $sumar;
                $sumaParcial                               += $sumar;
            }
        }
        $facturaAnterior        = $facturaActual;
        $empresaAnterior        = $empresaActual; // para controlar el encabezado
        $empresaAnteriorControl = $empresaActualControl; // para controlar el pie con el total
    }

    $query = " DROP TABLE IF EXISTS {$consolidado}";
    $rs    = mysql_query( $query, $conex );
    $query = " DROP TABLE IF EXISTS {$radicaciones}";
    $rs    = mysql_query( $query, $conex );

    $tablaDetalle .= imprimirTotalesEntidad( $empresaAnteriorControl, $arregloRangos, $sumaParcial );
    $tablaDetalle .= imprimirTotalesCartera( $arregloRangos, $carteraEdadesTotal, $carteraTotal );
    $tablaDetalle .= "</table></center>";

    if( $wtipoRepo == "SI")
        echo $tablaDetalle;

    if( $wtipoRepo == "NO")
       echo construirReporteResumido( $arregloTiposEmpresa, $arregloRangos );
    return;
}

?>
<html>
<head>
    <title>REPORTE DE CARTERA POR EDADES</title>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script type="text/javascript">

$( document ).ready(function(){
    fechaInicial = $("#wfecini"); // fecha inicial de creación de facturas
    fechaFinal   = $("#wfecfin"); // fecha final de creación de facturas
    fechaCorte   = $("#wfeccor"); // fecha de corte para consulta de recibos
    tipoBusqueda = $("#wtip");    // tipo de busqueda de empresa( codigo o nit )
    rangoEdades  = $("#wran");    //rango de edades
    empresa      = $("#wemp");    //empresa a consultar
    estadoFactura= $("#wesf");    // estado de factura
    wbasedato    = $("#wbasedato");
});

function generarReporte( obj ){

    //$.blockUI({ message: $('#msjEspere') });
    wtip = tipoBusqueda.val();
    wemp = empresa.find("option:selected").attr( wtip );

    $("#msjEspere").show();
    $("#div_ppal").toggle();
    $.ajax({
                url: "RepCarXEdad.new.php",
                type: "POST",
                data: {

                   peticionAjax: "generarReporte",
                        wfecini: fechaInicial.val(),
                        wfecfin: fechaFinal .val(),
                        wfeccor: fechaCorte.val(),
                           wtip: wtip,
                           wran: rangoEdades.val(),
                           wemp: wemp,
                           wesf: estadoFactura.val(),
                      wbasedato: wbasedato.val(),
                      wtipoRepo: $(obj).val()
                      },
                success: function(data)
                {
                    $("#msjEspere").hide();
                    $("#div_contenedor_respuesta").html( data );
                    $("#div_respuesta").toggle();
                }
            });
}

function retornar(){
    $("input[type='checkbox'][name='vol']").attr( "checked", false )
    $("#div_respuesta").toggle();
    $("#div_ppal").toggle();
}

function validarExistenciaParametros( txt ){
    $("div [id!='div_sesion_muerta']").hide();
    $("#div_sesion_muerta").show();
}

function ocultarMostrarDetalle( tr ){
    var identificador = $(tr).attr("numeroEmpresa");
    $("tr[numeroEmpresa='"+identificador+"'][tipo!='padre']").toggle();
}
</script>
</head>
<?php

include_once("root/comun.php");
$conex       = obtenerConexionBD("matrix");
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato   = strtolower($institucion->baseDeDatos);
$wentidad    = $institucion->nombre;
$hoy         = date('Y-m-d');
( !isset( $wfecini ) ) ? $wfecini = $hoy : $wfecini = $wfecini ;
( !isset( $wfecfin ) ) ? $wfecfin = $hoy : $wfecfin = $wfecfin ;
( !isset( $wfeccor ) ) ? $wfeccor = $hoy : $wfeccor = $wfeccor ;

?>

<body>
    <?php
        $wactualiz = "2014-02-10";
        encabezado("REPORTE DE CARTERA POR EDADES", $wactualiz, "logo_".$wbasedato);
    ?>
   <div id='div_ppal'>
   <center>
   <table border=0 width='90%'>
        <tr height='34'>
            <td align='center' class='fila2' nowrap='nowrap'>FECHA INICIAL DE FACTURACION:
            <?php campoFechaDefecto("wfecini", $wfecini); ?>
            </td>
            <td  align='center' class='fila2' nowrap='nowrap'>FECHA FINAL DE FACTURACION:
            <?php campoFechaDefecto("wfecfin", $wfecfin); ?>
            </td>
            <td class='fila2' align='center' nowrap='nowrap'>FECHA DE CORTE:
            <?php campoFechaDefecto("wfeccor", $wfeccor); ?>
            </td>
        </tr>
        <tr height='34'>
            <td align=center class='fila2' >PARAMETROS DEL REPORTE:
            <select name='wtip' id='wtip'> <option value='codigo'>CODIGO</option> <option value='nit'>NIT</option></select>
            </td>
            <td align=right class='fila2' colspan=2 > SELECCIONE LOS RANGOS DE EDADES:
                <select name='wran' id='wran'><?php echo buscarRangosEdades( $wran ) ?></select> &nbsp; &nbsp;&nbsp;
            </td>
        </tr>
        <tr height='34'>
            <td align=center class='fila2' colspan=2 width='60%' >RESPONSABLE:
                <select name='wemp' id='wemp'><?php echo listadoResponsables( $wemp, "si" ) ?></select>
            </td>
            <td align='center' colspan=2 class='fila2'>SELECCIONE ESTADO:
                <select name='wesf' id='wesf'> <?php echo listadoEstadoFactura( $westado ) ?> </select>
            </td>
        </tr>
        <input type='HIDDEN' NAME= 'wbasedato' id= 'wbasedato' value='<?php echo $wbasedato ?>'>
        <input type='HIDDEN' NAME= 'bandera' id= 'bandera' value='1'>
        <input type='HIDDEN' NAME= 'resultado' id= 'resultado' value='1'>
        <tr>
            <td align=center class='fila2' COLSPAN='3' width='90%'>
                <input type='radio' name='vol' value='SI' onclick='generarReporte( this )'> DESPLEGAR REPORTE DETALLADO&nbsp;&nbsp;&nbsp;&nbsp;
                <input type='radio' name='vol' value='NO' onclick='generarReporte( this )'> DESPLEGAR REPORTE RESUMIDO&nbsp;&nbsp;
                <input type='radio' name='vol' value='RE' onclick='generarReporte( this )'> DESPLEGAR REPORTE RESUMIDO CON PARTICULAR DETALLADO&nbsp;&nbsp;
            </td>
        </tr>
        </table>
        </div>
        <div id='div_respuesta'  style='display:none;'>
            <center><input type='button' value='Retornar' onclick='retornar();'></center><br>
            <div id='div_contenedor_respuesta'></div><br>
            <center><input type='button' value='Retornar' onclick='retornar();'></center><br>
        </div>
        <div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>
        <center><div id='msjEspere' style='display:none;'>
            <br /><br />
            <img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Espere por Favor</font>
            <br /><br /><br />
        </div></center>
</body>
</html>
