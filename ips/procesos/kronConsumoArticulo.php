<?php
/*
* Programa      :   kronConsumoArticulo.php
 * Fecha        :   2019-09-23
 * Por          :   Camilo Zapata Zapata
 * Descripcion  :   consolida los movimientos de inventarios en unix en matrix.

Modificaciones
 - 2019-11-05 Camilo Zapata: Teniendo en cuenta que este programa se conecta y se desconecta iterativamente puesto que traer datos desde unix
                             puede tomar mucho tiempo(provocando desconexiones automáticas), Se modifica el programa para que obtenga la ip a la que se conectará posteriormente desde el mismo $conex original por medio de la función mysqli_get_host_info().

 */
include_once("root/comun.php");
include_once("conex.php");
$wbasedato    = "cliame";
$wemp_pmla    = "01";
$conexunix    = odbc_pconnect('inventarios','informix','sco') or die("No se ralizo Conexion con Unix");
$fechaActual          = date('Y-m-d');
$horaActual           = date('H:i:s');
$fechaInicial         = strtotime('-1 year',strtotime($fechaActual));
$fechaInicial         = date('Y-m-d',$fechaInicial);
$fechaFinal           = strtotime('-11 month',strtotime($fechaActual));
$fechaFinal           = date('Y-m-d',$fechaFinal);
$iteracion            = 0;
$limite               = 12;
$estadoInventarioXmes = consultarEstadoInventarioXmes();
$ipServidor           = explode(" ",mysqli_get_host_info($conex));
$ipServidor           = $ipServidor[0];

$query = "SELECT Detval
            FROM root_000051
           WHERE Detemp = '{$wemp_pmla}'
             AND Detapl = 'conComUnixSisSuman'";
$rs          = mysql_query($query,$conex) or die (mysql_error());
$row         = mysql_fetch_array($rs);
$conComSuman =  explode(",",$row['Detval']);

$query = "SELECT Detval
            FROM root_000051
           WHERE Detemp = '{$wemp_pmla}'
             AND Detapl = 'conComUnixSisRestan'";
$rs           = mysql_query($query,$conex) or die (mysql_error());
$row          = mysql_fetch_array($rs);
$conComRestan =  explode(",",$row['Detval']);

echo "<br>edb-> fechas a Evaluar: fechaInicial: $fechaInicial - fechaFinal: $fechaFinal";
mysqli_close( $conex );

echo "<br> inicio: ".$fechaActual." ".$horaActual." ";
echo "<br> fechaInicial: ".$fechaInicial."  Final: ".$fechaActual;
if( !isset($consultar) ){
    echo "<br>traer datos";
    //traerDatosMes( $fechaInicial, $fechaFinal, $iteracion, $limite );
    foreach( $estadoInventarioXmes as $keyAno => $meses ){
        foreach( $meses as $keyMes => $datosMes ){

            if( !$datosMes['enMatrixDespuesDeCierre'] ){//--> no ha pasado a matrix despues del cierre
                echo "<br> llamar para el mes $keyAno-$keyMes: <pre>".print_r( $datosMes, true )."</pre>";
                borrarDatosConsumoMesXfaltaCierre( $datosMes['inicioMes'], $datosMes['finMes'] );
                traerDatosMesNuevo( $datosMes['inicioMes'], $datosMes['finMes'] );
                if( $datosMes['cerradoEnUnix'] ){
                    guardarMesCerradoEnUnix( $keyAno, $keyMes, $datosMes['fechaCierreUnix'] );
                }
            }else{
                echo "<br> el mes $keyAno-$keyMes:  ya est&aocute; grabado en matrix ";
            }
        }
    }


}else{
    echo "<br>consultar datos";
    consultarDatosMes( "2019-06-01", "2019-06-06", 0, 1, "N01A04" );
}

function traerDatosMesNuevo( $fechaInicial, $fechaFinal ){
    global $conexunix,
           $conComSuman,
           $conComRestan,
           $fechaActual,
           $horaActual,
           $wbasedato,
           $consumoDiario,
           $ipServidor;
    $resultadosDetalle = array();

    $query = "SELECT movdetart articulo,movdetcan cantidad, concod concepto, movfue fuente, movdoc documento,
                     contmo tipoMovimiento, '1'origen, movfec fecha, artpropro costoPromedio
                FROM ivmov,ivmovdet,ivcon, ivartpro
               WHERE movfec between '{$fechaInicial}' AND '{$fechaFinal}'
                 AND movcon = concod
                 AND coninv = 'S'
                 AND concod in ('104','105','106','107','108','110','113',
                    '115','119','120','121','122','123','128','129','130',
                    '802','804','CBF','CBO','CDF','CDI','PDI','TAC','004',
                    '005','006','007','008','013','017','018','020','021',
                    '022','023','025','026','028','029','102','801','803',
                    'DDI','RFS','TDC','001','101')
                 AND ( Contmo = 'E' OR Contmo = 'S' )
                 AND movanu = '0'
                 AND movfue = movdetfue
                 AND movdoc = movdetdoc
                 AND movdetcan > 0
                 AND artproart = movdetart
                 AND artproano = movano
                 AND artpromes = movmes
               UNION ALL
              SELECT drodetart articulo,cardetcan cantidad, concod concepto, cardetfue fuente,cardetdoc documento,
                     contmo tipoMovimiento, '2' origen, cardetfec fecha, artpropro costoPromedio
                FROM facardet, ivcon, ivdrodet, sifue, ivartpro
               WHERE concod = fuecob
                 AND coninv = 'S'
                 AND concod in ('104','105','106','107','108','110','113',
               '115','119','120','121','122','123','128','129','130','802',
               '804','CBF','CBO','CDF','CDI','PDI','TAC','004','005','006',
               '007','008','013','017','018','020','021','022','023','025',
               '026','028','029','102','801','803','DDI','RFS','TDC','001','101')
                 AND fuecod = cardetfue
                 AND cardetfec between '{$fechaInicial}' AND '{$fechaFinal}'
                 AND drodetfue = cardetfue
                 AND drodetdoc = cardetdoc
                 AND drodetite = cardetite
                 AND cardetanu = '0'
                 AND cardetcan > 0
                 AND artproart = drodetart
                 AND artproano = drodetano
                 AND artpromes = drodetmes";
    echo "<pre>".print_r($query, true);
    $resFac  = odbc_exec($conexunix,$query);
    $compras = 0;
    $entradas = 0;
    $salidas = 0;
    while($row=odbc_fetch_row($resFac)){


        $articulo       = odbc_result($resFac,'articulo');
        $tipoMovimiento = odbc_result($resFac,'tipoMovimiento');
        $origen         = odbc_result($resFac,'origen');
        $fecha          = odbc_result($resFac,'fecha');
        $concepto       = odbc_result($resFac,'concepto');
        $costoPromedio  = odbc_result($resFac,'costoPromedio');

        if( in_array( $concepto, $conComSuman ) ){
            $tipoMovimiento = "C";
            $operacion      = "+";
        }

        if( in_array( $concepto, $conComRestan ) ){
            $tipoMovimiento = "C";
            $operacion      = "-";
        }

        if( !isset( $resultadosDetalle[$articulo] ) ){
           $resultadosDetalle[$articulo]  = array();
           $resultadosDetalle[$articulos] = array();
        }

        if( !isset( $resultadosDetalle[$articulo][$fecha] ) )
           $resultadosDetalle[$articulo][$fecha] = array("E"=>0, "S"=>0, "C"=>0);//--> articulo x fecha( entradas, salidas y compras )

        $resultadosDetalle[$articulo][$fecha]['costoPromedio'] = $costoPromedio;
        if( $tipoMovimiento == "C" ){

            if( $operacion == "+" ){
                $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] += odbc_result($resFac,'cantidad')*1;
                $resultadosDetalle[$articulo][$fecha]["E"]             += odbc_result($resFac,'cantidad')*1;

            }else{
                $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] -= odbc_result($resFac,'cantidad')*1;
                $resultadosDetalle[$articulo][$fecha]["E"]             -= odbc_result($resFac,'cantidad')*1;
            }
        }else{
            $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] += odbc_result($resFac,'cantidad')*1;
        }
    }
    $conex = mysqli_connect( $ipServidor,'root','q6@nt6m', 'matrix') or die("No se realizo Conexion");
    foreach( $resultadosDetalle as $keyArticulo => $fechas ){
        foreach( $resultadosDetalle[$keyArticulo] as $keyFecha => $datos ){
            $entradas      = $datos['E'];
            $salidas       = $datos['S'];
            $compras       = $datos['C'];
            $consumo       = $salidas - $entradas + $compras;
            $costoPromedio = $datos['costoPromedio'];
            $insert   = "INSERT INTO `{$wbasedato}_000321` (`Medico`, `Fecha_data`, `Hora_data`, `Cinart`, `Cinfec`, `Cinent`, `Cinsal`, `Cincom`, `Cincon`, `Cincop`, `Seguridad`)   VALUES ('{$wbasedato}', '{$fechaActual}', '{$horaActual}', '{$keyArticulo}', '{$keyFecha}', {$entradas}, {$salidas}, {$compras}, {$consumo}, {$costoPromedio}, 'C-{$wbasedato}')";
            //echo "<br>".$insert;

            $rs = mysql_query( $insert, $conex ) or die( mysql_error() );
        }
    }
    mysqli_close( $conex );
}


function traerDatosMes( $fechaInicial, $fechaFinal, $iteracion, $limite ){
    global $conexunix,
           $conComSuman,
           $conComRestan,
           $fechaActual,
           $horaActual,
           $wbasedato,
           $consumoDiario;
    if( ( $limite - $iteracion ) == 1 ){
        $fechaFinal = strtotime('-1 day ',strtotime($fechaActual));
        $fechaFinal   = date('Y-m-d',$fechaFinal);
    }
    echo "<br> Inicio: $fechaInicial - $fechaFinal | iteracion: $iteracion | limite: $limite ";
    $resultadosDetalle = array();
    $consumob1ab02     = 0;

    $query = "SELECT movdetart articulo,movdetcan cantidad, concod concepto, movfue fuente, movdoc documento,
                     contmo tipoMovimiento, '1'origen, movfec fecha, artpropro costoPromedio
                FROM ivmov,ivmovdet,ivcon, ivartpro
               WHERE movfec between '{$fechaInicial}' AND '{$fechaFinal}'
                 AND movcon = concod
                 AND coninv = 'S'
                 AND concod in ('104','105','106','107','108','110','113',
                    '115','119','120','121','122','123','128','129','130',
                    '802','804','CBF','CBO','CDF','CDI','PDI','TAC','004',
                    '005','006','007','008','013','017','018','020','021',
                    '022','023','025','026','028','029','102','801','803',
                    'DDI','RFS','TDC','001','101')
                 AND ( Contmo = 'E' OR Contmo = 'S' )
                 AND movanu = '0'
                 AND movfue = movdetfue
                 AND movdoc = movdetdoc
                 AND movdetcan > 0
                 AND artproart = movdetart
                 AND artproano = movano
                 AND artpromes = movmes
               UNION ALL
              SELECT drodetart articulo,cardetcan cantidad, concod concepto, cardetfue fuente,cardetdoc documento,
                     contmo tipoMovimiento, '2' origen, cardetfec fecha, artpropro costoPromedio
                FROM facardet, ivcon, ivdrodet, sifue, ivartpro
               WHERE concod = fuecob
                 AND coninv = 'S'
                 AND concod in ('104','105','106','107','108','110','113',
               '115','119','120','121','122','123','128','129','130','802',
               '804','CBF','CBO','CDF','CDI','PDI','TAC','004','005','006',
               '007','008','013','017','018','020','021','022','023','025',
               '026','028','029','102','801','803','DDI','RFS','TDC','001','101')
                 AND fuecod = cardetfue
                 AND cardetfec between '{$fechaInicial}' AND '{$fechaFinal}'
                 AND drodetfue = cardetfue
                 AND drodetdoc = cardetdoc
                 AND drodetite = cardetite
                 AND cardetanu = '0'
                 AND cardetcan > 0
                 AND artproart = drodetart
                 AND artproano = drodetano
                 AND artpromes = drodetmes";
    echo "<pre>".print_r($query, true);
    $resFac  = odbc_exec($conexunix,$query);
    $compras = 0;
    $entradas = 0;
    $salidas = 0;
    while($row=odbc_fetch_row($resFac)){


        $articulo       = odbc_result($resFac,'articulo');
        $tipoMovimiento = odbc_result($resFac,'tipoMovimiento');
        $origen         = odbc_result($resFac,'origen');
        $fecha          = odbc_result($resFac,'fecha');
        $concepto       = odbc_result($resFac,'concepto');
        $costoPromedio  = odbc_result($resFac,'costoPromedio');

        if( in_array( $concepto, $conComSuman ) ){
            $tipoMovimiento = "C";
            $operacion      = "+";
        }

        if( in_array( $concepto, $conComRestan ) ){
            $tipoMovimiento = "C";
            $operacion      = "-";
        }

        if( !isset( $resultadosDetalle[$articulo] ) ){
           $resultadosDetalle[$articulo]  = array();
           $resultadosDetalle[$articulos] = array();
        }

        if( !isset( $resultadosDetalle[$articulo][$fecha] ) )
           $resultadosDetalle[$articulo][$fecha] = array("E"=>0, "S"=>0, "C"=>0);//--> articulo x fecha( entradas, salidas y compras )

        $resultadosDetalle[$articulo][$fecha]['costoPromedio'] = $costoPromedio;
        if( $tipoMovimiento == "C" ){

            if( $operacion == "+" ){
                $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] += odbc_result($resFac,'cantidad')*1;
                $resultadosDetalle[$articulo][$fecha]["E"]             += odbc_result($resFac,'cantidad')*1;

            }else{
                $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] -= odbc_result($resFac,'cantidad')*1;
                $resultadosDetalle[$articulo][$fecha]["E"]             -= odbc_result($resFac,'cantidad')*1;
            }
        }else{
            $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] += odbc_result($resFac,'cantidad')*1;
        }
    }
    $conex = mysqli_connect($ipServidor,'root','q6@nt6m', 'matrix') or die("No se realizo Conexion");
    foreach( $resultadosDetalle as $keyArticulo => $fechas ){
        foreach( $resultadosDetalle[$keyArticulo] as $keyFecha => $datos ){
            $entradas      = $datos['E'];
            $salidas       = $datos['S'];
            $compras       = $datos['C'];
            $consumo       = $salidas - $entradas + $compras;
            $costoPromedio = $datos['costoPromedio'];
            $insert   = "INSERT INTO `{$wbasedato}_000321` (`Medico`, `Fecha_data`, `Hora_data`, `Cinart`, `Cinfec`, `Cinent`, `Cinsal`, `Cincom`, `Cincon`, `Cincop`, `Seguridad`)   VALUES ('{$wbasedato}', '{$fechaActual}', '{$horaActual}', '{$keyArticulo}', '{$keyFecha}', {$entradas}, {$salidas}, {$compras}, {$consumo}, {$costoPromedio}, 'C-{$wbasedato}')";
            //echo "<br>".$insert;

            $rs = mysql_query( $insert, $conex ) or die( mysql_error() );
        }
    }
    mysqli_close( $conex );
    $iteracion++;
    if( $iteracion < $limite ){
        $fechaInicialN = strtotime('+1 day',strtotime($fechaFinal));
        $fechaInicialN = date( 'Y-m-d', $fechaInicialN );
        $fechaFinalN   = strtotime('+1 month',strtotime($fechaInicialN));
        $fechaFinalN   = date( 'Y-m-d', $fechaFinalN );
        traerDatosMes( $fechaInicialN, $fechaFinalN, $iteracion, $limite );
    }
    if( $iteracion == $limite ){
        //actualizarFechaCorte();
    }
}

function consultarDatosMes( $fechaInicial, $fechaFinal, $iteracion, $limite, $articulo ){
    global $conexunix,
           $conComSuman,
           $conComRestan,
           $fechaActual,
           $horaActual,
           $wbasedato,
           $consumoDiario;
    if( ( $limite - $iteracion ) == 1 ){
        $fechaFinal = strtotime('-1 day ',strtotime($fechaActual));
        $fechaFinal   = date('Y-m-d',$fechaFinal);
    }
    echo "<br> Inicio: $fechaInicial - $fechaFinal | iteracion: $iteracion | limite: $limite ";
    $resultadosDetalle = array();
    $consumob1ab02     = 0;

    $query = "SELECT movdetart articulo,movdetcan cantidad, concod concepto, movfue fuente, movdoc documento,
                     contmo tipoMovimiento, '1'origen, movfec fecha, artpropro costoPromedio
                FROM ivmov,ivmovdet,ivcon, ivartpro
               WHERE movfec between '{$fechaInicial}' AND '{$fechaFinal}'
                 AND movcon = concod
                 AND coninv = 'S'
                 AND concod in ('104','105','106','107','108','110','113',
                    '115','119','120','121','122','123','128','129','130',
                    '802','804','CBF','CBO','CDF','CDI','PDI','TAC','004',
                    '005','006','007','008','013','017','018','020','021',
                    '022','023','025','026','028','029','102','801','803',
                    'DDI','RFS','TDC','001','101')
                 AND ( Contmo = 'E' OR Contmo = 'S' )
                 AND movanu = '0'
                 AND movfue = movdetfue
                 AND movdoc = movdetdoc
                 AND movdetcan > 0
                 AND artproart = movdetart
                 AND artproano = movano
                 AND artpromes = movmes
                 AND movdetart = '{$articulo}'
               UNION ALL
              SELECT drodetart articulo,cardetcan cantidad, concod concepto, cardetfue fuente,cardetdoc documento,
                     contmo tipoMovimiento, '2' origen, cardetfec fecha, artpropro costoPromedio
                FROM facardet, ivcon, ivdrodet, sifue, ivartpro
               WHERE concod = fuecob
                 AND coninv = 'S'
                 AND concod in ('104','105','106','107','108','110','113',
               '115','119','120','121','122','123','128','129','130','802',
               '804','CBF','CBO','CDF','CDI','PDI','TAC','004','005','006',
               '007','008','013','017','018','020','021','022','023','025',
               '026','028','029','102','801','803','DDI','RFS','TDC','001','101')
                 AND fuecod = cardetfue
                 AND cardetfec between '{$fechaInicial}' AND '{$fechaFinal}'
                 AND drodetfue = cardetfue
                 AND drodetdoc = cardetdoc
                 AND drodetite = cardetite
                 AND cardetanu = '0'
                 AND cardetcan > 0
                 AND artproart = drodetart
                 AND artproano = drodetano
                 AND artpromes = drodetmes
                 AND drodetart = '{$articulo}'";
    echo "<pre>".print_r($query, true);
    $resFac  = odbc_exec($conexunix,$query);
    $compras = 0;
    $entradas = 0;
    $salidas = 0;
    while($row=odbc_fetch_row($resFac)){


        $articulo       = odbc_result($resFac,'articulo');
        $tipoMovimiento = odbc_result($resFac,'tipoMovimiento');
        $origen         = odbc_result($resFac,'origen');
        $fecha          = odbc_result($resFac,'fecha');
        $concepto       = odbc_result($resFac,'concepto');
        $costoPromedio  = odbc_result($resFac,'costoPromedio');

        if( in_array( $concepto, $conComSuman ) ){
            $tipoMovimiento = "C";
            $operacion      = "+";
        }

        if( in_array( $concepto, $conComRestan ) ){
            $tipoMovimiento = "C";
            $operacion      = "-";
        }

        if( !isset( $resultadosDetalle[$articulo] ) ){
           $resultadosDetalle[$articulo]  = array();
           $resultadosDetalle[$articulos] = array();
        }

        if( !isset( $resultadosDetalle[$articulo][$fecha] ) )
           $resultadosDetalle[$articulo][$fecha] = array("E"=>0, "S"=>0, "C"=>0);//--> articulo x fecha( entradas, salidas y compras )

        $resultadosDetalle[$articulo][$fecha]['costoPromedio'] = $costoPromedio;
        if( $tipoMovimiento == "C" ){

            if( $operacion == "+" ){
                $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] += odbc_result($resFac,'cantidad')*1;
                $resultadosDetalle[$articulo][$fecha]["E"]             += odbc_result($resFac,'cantidad')*1;

            }else{
                $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] -= odbc_result($resFac,'cantidad')*1;
                $resultadosDetalle[$articulo][$fecha]["E"]             -= odbc_result($resFac,'cantidad')*1;
            }
        }else{
            $resultadosDetalle[$articulo][$fecha][$tipoMovimiento] += odbc_result($resFac,'cantidad')*1;
        }
    }
    echo "<br><pre>".print_r( $resultadosDetalle, true )."</pre>";
    //$conex = mysqli_connect('132.1.18.95','root','q6@nt6m', 'matrix') or die("No se realizo Conexion");
    foreach( $resultadosDetalle as $keyArticulo => $fechas ){
        foreach( $resultadosDetalle[$keyArticulo] as $keyFecha => $datos ){
            $entradas      = $datos['E'];
            $salidas       = $datos['S'];
            $compras       = $datos['C'];
            $consumo       = $salidas - $entradas + $compras;
            $costoPromedio = $datos['costoPromedio'];
            /*$insert   = "INSERT INTO `{$wbasedato}_000321` (`Medico`, `Fecha_data`, `Hora_data`, `Cinart`, `Cinfec`, `Cinent`, `Cinsal`, `Cincom`, `Cincon`, `Cincop`, `Seguridad`)   VALUES ('{$wbasedato}', '{$fechaActual}', '{$horaActual}', '{$keyArticulo}', '{$keyFecha}', {$entradas}, {$salidas}, {$compras}, {$consumo}, {$costoPromedio}, 'C-{$wbasedato}')";
            //echo "<br>".$insert;

            $rs = mysql_query( $insert, $conex ) or die( mysql_error() );*/
        }
    }
    //mysqli_close( $conex );
    $iteracion++;
    if( $iteracion < $limite ){
        $fechaInicialN = strtotime('+1 day',strtotime($fechaFinal));
        $fechaInicialN = date( 'Y-m-d', $fechaInicialN );
        $fechaFinalN   = strtotime('+1 month',strtotime($fechaInicialN));
        $fechaFinalN   = date( 'Y-m-d', $fechaFinalN );
        traerDatosMes( $fechaInicialN, $fechaFinalN, $iteracion, $limite );
    }
    if( $iteracion == $limite ){
        //actualizarFechaCorte();
    }
}

function establecerFechaDeInicioConsulta(){
   global $conComSuman,
          $conComRestan,
          $fechaActual,
          $horaActual,
          $wbasedato,
          $conex;

    $arregloMeses = array();

    $query = "SELECT max(Cinfec) fechaInicial
                FROM {$wbasedato}_000321
               WHERE Cinest = 'on'";
    $rs           = mysql_query( $query, $conex );
    $row          = mysql_fetch_assoc( $rs );
    $fechaInicial = strtotime('+1 day', strtotime( $row['fechaInicial'] ) );
    $fechaInicial = date('Y-m-d', $fechaInicial);
    return( $fechaInicial );
}

function actualizarFechaCorte(){
   global $conComSuman,
          $conComRestan,
          $fechaActual,
          $horaActual,
          $wbasedato,
          $conex,
          $wemp_pmla;

    $query = "UPDATE {$wbasedato}_000051
                 SET detval = '{$fechaInicial}'
               WHERE detemp = '{$wemp_pmla}'
                 AND detapl = 'fechaCorteConsumosInventario'";
    $rs           = mysql_query( $query, $conex );
}

function consultarEstadoInventarioXmes(){

    global $conex,
           $conexunix,
           $fechaActual,
           $wbasedato;

    $estadoInventarioXmes = array();
    $fechaInicial         = strtotime( '-1 year', strtotime( $fechaActual ) );
    $fechaInicial         = date( 'Y-m-d', $fechaInicial );

    $start    = ( new DateTime( $fechaInicial ) )->modify('first day of this month');
    $end      = ( new DateTime( $fechaActual ) )->modify('first day of next month');
    $interval = DateInterval::createFromDateString('1 month');
    $period   = new DatePeriod($start, $interval, $end);

    foreach ($period as $dt) {

        $finalThisMonth = $dt->modify('last day of this month')->format("Y-m-d");
        $aux            = explode( "-", $dt->format("Y-m") );
        $ano            = $aux[0];
        $mes            = $aux[1];

        if( !isset( $estadoInventarioXmes[$ano] ) )
            $estadoInventarioXmes[$ano] = array();

        $estadoInventarioXmes[$ano][$mes] = array( 'cerradoEnUnix' =>false,
                                                   'fechaCierreUnix' =>'',
                                                   'enMatrixDespuesDeCierre' =>false,
                                                   'inicioMes' =>$dt->format("Y-m")."-01",
                                                   'finMes' =>$finalThisMonth
                                            );

        $query = " SELECT emifci, emigdc
                     FROM {$wbasedato}_000323
                    WHERE emiano = '{$ano}'
                      AND emimes = '{$mes}'";
        $rs    = mysql_query( $query, $conex );
        $row   = mysql_fetch_assoc( $rs );
        if( $row ){
            $estadoInventarioXmes[$ano][$mes]['enMatrixDespuesDeCierre'] = ( $row['emigdc'] == "on" ) ? true : false;
        }
        $query = " SELECT ciefec
                     FROM ivcie
                    WHERE ciecia = '01'
                      AND cieapl = 'INVENT'
                      AND cieano = '{$ano}'
                      AND ciemes = '{$mes}'
                    GROUP BY ciefec";
        $resUX   = odbc_exec($conexunix,$query);
        $row     = odbc_fetch_row( $resUX );
        $fechaUx = odbc_result( $resUX, 'ciefec' );
        if( trim( $fechaUx ) != "" ){
            $estadoInventarioXmes[$ano][$mes]['cerradoEnUnix']   = true;
            $estadoInventarioXmes[$ano][$mes]['fechaCierreUnix'] = $fechaUx;
        }
    }
    //echo "<br> estadoInventarioXmes: <pre>".print_r( $estadoInventarioXmes, true )."</pre>";
    return( $estadoInventarioXmes );
}

function borrarDatosConsumoMesXfaltaCierre( $fechaInicial, $fechaFinal ){
    global $wbasedato, $ipServidor;
    $conex2 = mysqli_connect($ipServidor,'root','q6@nt6m', 'matrix') or die("No se realizo Conexion");

    $query =  " DELETE FROM {$wbasedato}_000321 WHERE Cinfec BETWEEN '{$fechaInicial}' AND '{$fechaFinal}'";
    echo "<br> edb-> ".$query;
    $rs    = mysql_query( $query, $conex2 ) or die( "hubo error 2".mysql_error() );

    mysqli_close( $conex2 );
}

function guardarMesCerradoEnUnix( $ano, $mes, $fechaCierreUnix ){
    global $wbasedato, $fechaActual, $horaActual, $ipServidor;
    $conex3 = mysqli_connect($ipServidor,'root','q6@nt6m', 'matrix') or die("No se realizo Conexion");

    $query = "INSERT INTO `{$wbasedato}_000323` (`Medico`, `Fecha_data`, `Hora_data`, `Emiano`, `Emimes`, `Emifci`, `Emigdc`, `Emiest`, `Seguridad`)
                            VALUES ('{$wbasedato}', '{$fechaActual}', '{$horaActual}', '{$ano}', '{$mes}', '{$fechaCierreUnix}', 'on', 'on', 'C-{$wbasedato}')";
    echo "<br> edb-> ".$query;
    $rs    = mysql_query( $query, $conex3 )or die( "hubo error 1".mysql_error() );

    mysqli_close( $conex3 );
}

echo "<br> final: ".date('Y-m-d')." ".date('H:i:s')." ";
odbc_close_all();
?>