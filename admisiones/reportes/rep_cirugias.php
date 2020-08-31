<?php
include_once("conex.php");
include_once( "root/comun.php" );
require_once("conex.php");
mysql_select_db( "matrix" );
$wfecha     = date('Y-m-d');
$datoActual = explode( "-", $wfecha );
$anioActual = $datoActual[0];
$mesActual  = $datoActual[1];
$hora       = date("H:i:s");
$wactualiz  = "2015-08-25";
$wcliame    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$wmovhos    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wtcx       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
$whce       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');

if( !isset($_SESSION['user']) ){//session muerta en una petición ajax

      echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
             </div>";
      return;
}

if( isset( $consultaAjax ) ){
    switch ( $consulta ) {
        case 'fechaMaximaConsulta':
            $fecha_final = date( "Y-m-d", strtotime( $fecha_inicio."+3 month" ) );
            $fecha_final = date( "Y-m-d", strtotime( $fecha_final."-1 day" ) );
            echo $fecha_final;
            break;
        case 'generarReporte':

            $quirofanosCco                = array();
            $maestroCcos                  = array();
            $ccoQuirofano                 = array();
            $array_urgencias_hos          = array();
            $array_urgencias_amb          = array();
            $array_urgencias_partos       = array();
            $array_programadas_hos        = array();
            $array_programadas_amb        = array();
            $array_programadas_partos     = array();
            $turnosUrgenciasProgramados   = array();//--> cirugias programadas de pacientes que ingresaron por urgencias
            $ingresosUrgenciasProgramados = array();//--> cirugias programadas de pacientes que ingresaron por urgencias
            $codigosPartos                = array();//--> cirugias programadas de pacientes que ingresaron por urgencias
            $alias                        = "movhos";
            $aplicacion                   = consultarAplicacion($conex,$wemp_pmla,$alias);
            $cco_urgencias                = "";
            $anios_meses                  = array();
            $hayDatos                     = false;

            /* construcción de datos iniciales y paramétricos */
              $condicionCcoTcx11 = ( $wcco == "%" ) ? "" : " WHERE Quicco = '{$wcco}' ";
              $queryCco = " SELECT Quicod, Quicco
                              FROM {$wtcx}_000012
                              {$condicionCcoTcx11}";

              $rsCco    = mysql_query( $queryCco, $conex );
              while( $rowCco = mysql_fetch_assoc( $rsCco ) ){
                  $ccoQuirofano [$rowCco['Quicod']] = $rowCco['Quicco'];
                  if( !isset( $quirofanosCco[$rowCco['Quicco']] ) ){
                      $quirofanosCco[$rowCco['Quicco']] = array();
                  }
                  array_push( $quirofanosCco[$rowCco['Quicco']], "'".$rowCco['Quicod']."'" );
              }

              $q = " SELECT Ccocod,Cconom, Ccocod, Ccosei
                       FROM {$aplicacion}_000011
                      WHERE ccoing='on'
                        AND ccourg='on'
                        AND ccoest='on'
                      ORDER BY Cconom";
              $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
              $row = mysql_fetch_assoc( $res );
              $cco_urgencias = $row['Ccocod'];

              $q = " SELECT Ccocod,Cconom, Ccocod, Ccosei
                       FROM {$aplicacion}_000011
                      WHERE Ccocir='on'
                        AND Ccoest='on'
                      ORDER BY Cconom";
              $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
              while( $row = mysql_fetch_assoc( $res ) ){
                $maestroCcos[$row['Ccocod']]['nombre'] = $row['Cconom'];
              }

              $nombre_meses = array( '01'=>"Enero",'02'=>"Febrero",'03'=>"Marzo",'04'=>"Abril",'05'=>"Mayo",'06'=>"Junio",
                                     '07'=>"Julio",'08'=>"Agosto",'09'=>"Septiembre",'10'=>"Octubre",'11'=>"Noviembre",'12'=>"Diciembre",);
              $anios_meses = construirArregloFechas( $fecha_inicio, $fecha_final );

              $rangoCupsPartos = consultarAplicacion( $conex, $wemp_pmla, "rangoCupsPartos" );
              $rangoCupsPartos = explode( "_", $rangoCupsPartos );
              $query       = " SELECT Procod
                                 FROM {$wcliame}_000103
                                WHERE Procup between '{$rangoCupsPartos[0]}' and '{$rangoCupsPartos[1]}'
                                  AND Proest = 'on' ";
              $res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
              while( $row = mysql_fetch_assoc( $res ) ){
                array_push( $codigosPartos, $row['Procod'] );
              }
              //echo "<pre>".print_r( $codigosPartos, true )."</pre>";


            /* final contrucción de datos iniciales y paramétricos*/

             //--> construir tabla temporal para las demas consultas

            if( $wcco != "%" ){
                $ccosBuscados              = implode(",", $quirofanosCco[$wcco] );
                $condicionCcoTcx11_clia199 = " AND Turqui IN ( $ccosBuscados ) ";
            }else{
                $condicionCcoTcx11_clia199 = "";
            }

            /*
                se consultan los procedimientos liquidados( _000199 ), que sean cirugías,
                se hace un join con la tabla de turnos( tcx_000011) para obtener los datos básicos de dicho procedimiento los cuales incluyen
                fecha del turno, y el quirófano en el cual se realizó el procedimiento.
                Estos datos se almacenan en una tabla temporal para mejorar el rendimiento de las búsquedas posteriores

            */
            /* creacion de tabla temporal con los datos de la cirugias liquidadas*/
             $tmp_cirs_liq = " tmp_11_199".time('His');

             $query = " DROP TABLE IF EXISTS {$tmp_cirs_liq}";
             $rs    = mysql_query( $query, $conex );
             $query = " CREATE TEMPORARY TABLE IF NOT EXISTS {$tmp_cirs_liq}
                        ( INDEX( Enlhis, Enling ) )";

             /*$query .= " SELECT Enlhis, Enling, Enltur, Turfec fechaCirugia, Turhin horaCirugia, Turqui, Ubiald egresado
                           FROM {$wcliame}_000199 a
                          INNER JOIN
                                {$wtcx}_000011 b on ( a.Enltur = b.turtur and turest = 'on' and a.Enlest = 'on' AND b.turfec between '{$fecha_inicio}' and '{$fecha_final}' $condicionCcoTcx11_clia199 )
                          INNER JOIN
                                {$wmovhos}_000018 c on ( c.ubihis = a.Enlhis and c.ubiing = a.Enling )
                          GROUP BY 1,2,3,4,5";*/

            $query .= " SELECT Enlhis, Enling, Enltur, Enlpro, Turfec fechaCirugia, Turhin horaCirugia, Turqui, Ubiald egresado
                           FROM {$wcliame}_000199 a
                          INNER JOIN
                                {$wcliame}_000103 d on ( a.Enlpro = d.Procod and d.Proqui='on')
                          INNER JOIN
                                {$wtcx}_000011 b on ( a.Enltur = b.turtur and turest = 'on' and a.Enlest = 'on' AND b.turfec between '{$fecha_inicio}' and '{$fecha_final}' $condicionCcoTcx11_clia199 )
                          INNER JOIN
                                {$wmovhos}_000018 c on ( c.ubihis = a.Enlhis and c.ubiing = a.Enling )
                          GROUP BY 1,2,3,4,5";
             $rsprocir  = mysql_query( $query, $conex ) or die( mysql_error()." - ".$query );
             //echo " <br> EDB --> <BR> <pre>".print_r( $query, true )."</pre>";
             $faltantes = 0;

             /* Creamos una tabla temporal compuesta de los últimos movimientos de cada historia ingreso que tiene cirugias liquidadas */
             $tmp_mov17_total = " tmp_17_199_max".time('His');
             $query           = " DROP TABLE IF EXISTS {$tmp_mov17_total}";
             $rs              = mysql_query( $query, $conex );
             $query           = " CREATE TEMPORARY TABLE IF NOT EXISTS {$tmp_mov17_total}
                                  ( INDEX( Eyrhis, Eyring ) )";
             $query          .= " SELECT Eyrhis, Eyring, Eyrtip, Max( convert( concat( Fecha_data,' ', Hora_data ), DATETIME ) ) as fechaTraslado, max(id) as id
                                    FROM {$wmovhos}_000017, $tmp_cirs_liq
                                   WHERE Eyrhis = Enlhis
                                     AND Eyring = Enling
                                     AND Eyrtip = 'Entrega'
                                     AND Eyrest = 'on'
                                  GROUP BY 1,2,3";

             $rsprocir  = mysql_query( $query, $conex ) or die( mysql_error()." - <pre>".print_r( $query, true )."</pre>" );
             /*****
                Se realizan las diferentes consultas para construir el reporte, según la clase y el tipo de cirugías consultadas
             *****/

            if( $wclase == "%" or $wclase == "u" or $wclase == "P" ){
              /* CIRUGIAS DE URGENCIAS */
                  //--> la identificación de las cirugias hechas por urgencias consiste en identificar las cirugias de aquellos pacientes qué
                  //    entraron por urgencias, y se les hace una cirugía en las posteriores 24 horas

                  $array_cirugias_urgentes = array();
                  $tmpPacUrg = " tmp_urg_".time('His');
                  $query     = " DROP TABLE IF EXISTS {$tmpPacUrg}";
                  $rs        = mysql_query( $query, $conex );
                  $query     = " CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpPacUrg}
                                 ( INDEX( Enlhis, Enling ) )";
                  $query          .= " SELECT b.*, a.Mtrfco fechaConsulta, a.Mtrhco horaConsulta, a.Mtrcci ccoIngreso
                                         FROM {$whce}_000022 a
                                        INNER JOIN
                                              {$tmp_cirs_liq} b on ( a.Mtrhis = b.Enlhis and a.Mtring = b.Enling and a.Mtrcci ='$cco_urgencias' )
                                        GROUP BY 1,2,3,4,5,6,7 ";
                  $rsUrg       = mysql_query( $query, $conex ) or die( mysql_error() );


                  $querySelect = "SELECT a.*, b.Fecha_data fechaTraslado, b.Hora_data horaTraslado, b.id idTraslado
                                    FROM {$tmpPacUrg} a
                                    LEFT JOIN
                                         {$wmovhos}_000017 b on ( Eyrhis = Enlhis and Eyring = Enling and Eyrtip='Entrega' and Eyrsor='$cco_urgencias' and Eyrest='on')
                                   ORDER BY Enlhis, Enling, b.Fecha_data, b.Hora_data";
                  $rsUrg       = mysql_query( $querySelect, $conex ) or die( mysql_error() );
                  while( $rowUrg = mysql_fetch_assoc( $rsUrg ) ){

                      $hayDatos = true;
                      $idx      = $rowUrg['Enlhis']."_".$rowUrg['Enling'];
                      $anio_mes = explode( "-", $rowUrg['fechaCirugia'] );
                      $anio     = $anio_mes[0];
                      $mes      = $anio_mes[1];
                      /* cálculo de que se hizo primero si la cirugía o el traslado */
                      if( $rowUrg['idTraslado'] != "" ){//--> si tiene traslados desde cirugia

                          $momentoCirugia  = strtotime( $rowUrg['fechaCirugia']." ".$rowUrg['horaCirugia'] );
                          $momentoTraslado = strtotime( $rowUrg['fechaTraslado']." ".$rowUrg['horaTraslado'] );
                          if( $momentoCirugia - $momentoTraslado <= 0 ){
                              $aux = array("turno"       => $rowUrg['Enltur'],
                                           "historia"    => $rowUrg['Enlhis'],
                                           "ingreso"     => $rowUrg['Enling'],
                                           "fechaInicio" => $rowUrg['fechaCirugia'],
                                           "horaInicio"  => $rowUrg['horaCirugia'],
                                           "quirofano"   => $rowUrg['Turqui'],
                                           "centroCostos"=> $ccoQuirofano[$rowUrg['Turqui']],
                                           "tipoCirugia" => "hos" );

                              if( in_array( $rowUrg['Enlpro'], $codigosPartos ) ){
                                //array_urgencias_partos
                                $guardarEnTotal = ( $wtipo == "%" or $wtipo == "p" ) ? true : false;
                                guardarEnArreglos( $array_urgencias_partos, $array_urgencias_total, $ccoQuirofano[$rowUrg['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                              }else{
                                $guardarEnTotal = ( $wtipo == "%" or $wtipo == "h" ) ? true : false;
                                guardarEnArreglos( $array_urgencias_hos, $array_urgencias_total, $ccoQuirofano[$rowUrg['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                              }

                          }else{

                              //--> si entra por acá es porque la cirugia se realizó estando ya en otro piso, por lo tanto se clasificará en las
                              //--> hospitalarias, así haya ingresado por urgencias.
                              $aux = array("turno"       => $rowUrg['Enltur'],
                                           "historia"    => $rowUrg['Enlhis'],
                                           "ingreso"     => $rowUrg['Enling'],
                                           "fechaInicio" => $rowUrg['fechaCirugia'],
                                           "horaInicio"  => $rowUrg['horaCirugia'],
                                           "quirofano"   => $rowUrg['Turqui'],
                                           "centroCostos"=> $ccoQuirofano[$rowUrg['Turqui']],
                                           "tipoCirugia" => "mover a programadas" );

                              if( !in_array( "'".$rowUrg['Enltur']."'", $turnosUrgenciasProgramados ) )
                                array_push( $turnosUrgenciasProgramados, "'".$rowUrg['Enltur']."'" );
                              if( !in_array( "'".$rowUrg['Enlhis'].$rowUrg['Enling']."'", $ingresosUrgenciasProgramados ) )
                                array_push( $ingresosUrgenciasProgramados, "'".$rowUrg['Enlhis'].$rowUrg['Enling']."'" );
                          }
                      }else{//--> si no tiene traslados desde urgencias, entonces entró a cirugia y fue ambulatoria
                          $aux = array("turno"       => $rowUrg['Enltur'],
                                       "historia"    => $rowUrg['Enlhis'],
                                       "ingreso"     => $rowUrg['Enling'],
                                       "fechaInicio" => $rowUrg['fechaCirugia'],
                                       "horaInicio"  => $rowUrg['horaCirugia'],
                                       "quirofano"   => $rowUrg['Turqui'],
                                       "centroCostos"=> $ccoQuirofano[$rowUrg['Turqui']],
                                       "tipoCirugia" => "ham" );
                          $guardarEnTotal = ( $wtipo == "%" or $wtipo == "a" ) ? true : false;
                          if( in_array( $rowUrg['Enlpro'], $codigosPartos ) ){
                            //array_urgencias_partos
                            $guardarEnTotal = ( $wtipo == "%" or $wtipo == "p" ) ? true : false;
                            guardarEnArreglos( $array_urgencias_partos, $array_urgencias_total, $ccoQuirofano[$rowUrg['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                          }else{
                            $guardarEnTotal = ( $wtipo == "%" or $wtipo == "a" ) ? true : false;
                            guardarEnArreglos( $array_urgencias_amb, $array_urgencias_total, $ccoQuirofano[$rowUrg['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                          }

                      }
                      /* fin calculo*/
                  }
              /* FIN CIRUGIAS DE URGENCIAS*/
            }

            if( $wclase == "%" or $wclase == "P" ){
              /* TRATAMIENTO DE CIRUGIAS PROGRAMADAS DE PACIENTES QUE INGRESARON POR URGENCIAS*/
                if( count( $turnosUrgenciasProgramados ) ){

                  $turnosUrgenciasProgramados   = implode( ",", $turnosUrgenciasProgramados );
                  $ingresosUrgenciasProgramados = implode( ",", $ingresosUrgenciasProgramados );
                  $tmpPacPro = " tmp_pro_".time('His')."urg";
                  $query     = " DROP TABLE IF EXISTS {$tmpPacPro}";
                  $rs        = mysql_query( $query, $conex );
                  $query     = " CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpPacPro}
                                 ( INDEX( Enlhis, Enling ) )";
                  $query    .= " SELECT b.*, a.Mtrfco fechaConsulta, a.Mtrhco horaConsulta, a.Mtrcci ccoIngreso
                                   FROM {$whce}_000022 a
                                  INNER JOIN
                                        {$tmp_cirs_liq} b on ( b.Enltur in ({$turnosUrgenciasProgramados}) and a.Mtrhis = b.Enlhis and a.Mtring = b.Enling )
                                  GROUP BY 1,2,3,4,5,6,7 ";
                  $rsPro     = mysql_query( $query, $conex ) or die( mysql_error() );
                  //echo "<pre>".print_r( $query, true)."<pre><br><br>";


                  $tmpmv17   = " tmp_mov17_".time('His')."urg";
                  $query     = " DROP TABLE IF EXISTS {$tmpmv17}";
                  $rs        = mysql_query( $query, $conex );
                  $query     = " CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpmv17}
                                 ( INDEX( Eyrhis, Eyring ) )";
                  $query    .= " SELECT Eyrhis, Eyring, Eyrtip, fechaTraslado, id
                                   FROM {$tmp_mov17_total} a
                                  WHERE concat( Eyrhis,Eyring ) IN ( {$ingresosUrgenciasProgramados} )
                                  GROUP BY 1,2,3 ";
                  $rsPro     = mysql_query( $query, $conex ) or die( mysql_error() );


                  $querySelect = "SELECT a.*, b.fechaTraslado , b.id idTraslado
                                        FROM {$tmpPacPro} a
                                        LEFT JOIN
                                             {$tmpmv17} b on ( Eyrhis = Enlhis and Eyring = Enling)
                                       GROUP BY 1,2,3,4,5,6,7,8
                                       ORDER BY Enlhis, Enling, b.fechaTraslado";
                  $rsUrg       = mysql_query( $querySelect, $conex ) or die( mysql_error() );
                  $i = 0;
                  $ambulatorias  = 0;
                  $hospitalarias = 0;
                  while( $rowPro = mysql_fetch_assoc( $rsUrg ) ){
                    $hayDatos = true;

                      $idx = $rowPro['Enlhis']."_".$rowPro['Enling'];
                      $anio_mes        = explode( "-", $rowPro['fechaCirugia'] );
                      $anio            = $anio_mes[0];
                      $mes             = $anio_mes[1];
                      // cálculo de que se hizo primero si la cirugía o el traslado
                      // dado que el  cirugia es programada, miramos si es ambulatoria u hospitalaria
                      $momentoCirugia  = strtotime( $rowPro['fechaCirugia']." ".$rowPro['horaCirugia'] );
                      $momentoTraslado = strtotime( $rowPro['fechaTraslado'] );
                      /*if( $momentoCirugia - $momentoTraslado <= 0){
                          $aux = array("turno"       => $rowPro['Enltur'],
                                       "historia"    => $rowPro['Enlhis']."*",
                                       "ingreso"     => $rowPro['Enling'],
                                       "fechaInicio" => $rowPro['fechaCirugia'],
                                       "horaInicio"  => $rowPro['horaCirugia'],
                                       "quirofano"   => $rowPro['Turqui'],
                                       "centroCostos"=> $ccoQuirofano[$rowPro['Turqui']],
                                       "tipoCirugia" => "hos" );
                          $guardarEnTotal = () ? true : false;
                          guardarEnArreglos( &$array_programadas_hos, &$array_programadas_total, $ccoQuirofano[$rowUrg['Turqui']], $anio, $mes, $aux, $guardarEnTotal );

                      }else{
                          //--> si entra por acá es porque la cirugia se realizó estando ya en otro piso, por lo tanto se clasificará en las
                          //--> programadas, así haya ingresado por urgencias.
                          $aux = array("turno"       => $rowPro['Enltur'],
                                       "historia"    => $rowPro['Enlhis']."*",
                                       "ingreso"     => $rowPro['Enling'],
                                       "fechaInicio" => $rowPro['fechaCirugia'],
                                       "horaInicio"  => $rowPro['horaCirugia'],
                                       "quirofano"   => $rowPro['Turqui'],
                                       "centroCostos"=> $ccoQuirofano[$rowPro['Turqui']],
                                       "tipoCirugia" => "ham" );
                          $guardarEnTotal = () ? true : false;
                          guardarEnArreglos( &$array_programadas_amb, &$array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                      }*/
                      //--> se parte de la premisa de que si tiene movimiento en la 17 ya es programada y hospitalaria
                      $aux = array("turno"       => $rowPro['Enltur'],
                                   "historia"    => $rowPro['Enlhis']."*",
                                   "ingreso"     => $rowPro['Enling'],
                                   "fechaInicio" => $rowPro['fechaCirugia'],
                                   "horaInicio"  => $rowPro['horaCirugia'],
                                   "quirofano"   => $rowPro['Turqui'],
                                   "centroCostos"=> $ccoQuirofano[$rowPro['Turqui']],
                                   "tipoCirugia" => "hos" );
                      $guardarEnTotal = ( $wtipo == "%" or $wtipo == "h" ) ? true : false;

                      if( in_array( $rowPro['Enlpro'], $codigosPartos ) ){
                        //array_urgencias_partos
                        $guardarEnTotal = ( $wtipo == "%" or $wtipo == "p" ) ? true : false;
                        guardarEnArreglos( $array_programadas_partos, $array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                      }else{
                        $guardarEnTotal = ( $wtipo == "%" or $wtipo == "h" ) ? true : false;
                        guardarEnArreglos( $array_programadas_hos, $array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                      }
                      //guardarEnArreglos( &$array_programadas_hos, &$array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                      // fin calculo
                  }
                }
               /*FIN TRATAMIENTO DE CIRUGIAS PROGRAMADAS DE PACIENTES QUE INGRESARON POR URGENCIAS*/


              /* CIRUGIAS PROGRAMADAS */
                $tmpPacPro = " tmp_pro_".time('His');
                $query     = " DROP TABLE IF EXISTS {$tmpPacPro}";
                $rs        = mysql_query( $query, $conex );
                $query     = " CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpPacPro}
                               ( INDEX( Enlhis, Enling ) )";
                $query    .= " SELECT b.*, a.Mtrfco fechaConsulta, a.Mtrhco horaConsulta, a.Mtrcci ccoIngreso
                                 FROM {$whce}_000022 a
                                INNER JOIN
                                      {$tmp_cirs_liq} b on ( a.Mtrhis = b.Enlhis and a.Mtring = b.Enling and a.Mtrcci !='$cco_urgencias' )
                                GROUP BY 1,2,3,4,5,6,7 ";
                $rsPro     = mysql_query( $query, $conex ) or die( mysql_error() );


                $querySelect = "SELECT a.*,  b.fechaTraslado, b.id idTraslado
                                      FROM {$tmpPacPro} a
                                      LEFT JOIN
                                           {$tmp_mov17_total} b on ( Eyrhis = Enlhis and Eyring = Enling )
                                     ORDER BY Enlhis, Enling";

                $rsUrg       = mysql_query( $querySelect, $conex ) or die( mysql_error()."<br> fue aca 1 <br>" );
                while( $rowPro = mysql_fetch_assoc( $rsUrg ) ){
                  $hayDatos = true;

                    $idx = $rowPro['Enlhis']."_".$rowPro['Enling'];
                    $anio_mes        = explode( "-", $rowPro['fechaCirugia'] );
                    $anio            = $anio_mes[0];
                    $mes             = $anio_mes[1];
                    // cálculo de que se hizo primero si la cirugía o el traslado
                    if( $rowPro['idTraslado'] != "" ){//--> si tiene traslados

                        $momentoCirugia  = strtotime( $rowPro['fechaCirugia']." ".$rowPro['horaCirugia'] );
                        $momentoTraslado = strtotime( $rowPro['fechaTraslado'] );

                        //--> por la premisa de que si tiene movimiento en la 17 ya es hospitalaria
                        $aux = array("turno"       => $rowPro['Enltur'],
                                     "historia"    => $rowPro['Enlhis'],
                                     "ingreso"     => $rowPro['Enling'],
                                     "fechaInicio" => $rowPro['fechaCirugia'],
                                     "horaInicio"  => $rowPro['horaCirugia'],
                                     "quirofano"   => $rowPro['Turqui'],
                                     "centroCostos"=> $ccoQuirofano[$rowPro['Turqui']],
                                     "tipoCirugia" => "hos" );

                        if( in_array( $rowPro['Enlpro'], $codigosPartos ) ){
                          //array_urgencias_partos
                          $guardarEnTotal = ( $wtipo == "%" or $wtipo == "p" ) ? true : false;
                          guardarEnArreglos( $array_programadas_partos, $array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                        }else{
                          $guardarEnTotal = ( $wtipo == "%" or $wtipo == "h" ) ? true : false;
                          guardarEnArreglos( $array_programadas_hos, $array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                        }
                        //guardarEnArreglos( &$array_programadas_hos, &$array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );

                    }else{//--> si no tiene traslados entonces fue programada y ambulatoria
                        if( $rowPro['egresado'] == "on" ){
                          $aux = array("turno"       => $rowPro['Enltur'],
                                       "historia"    => $rowPro['Enlhis'],
                                       "ingreso"     => $rowPro['Enling'],
                                       "fechaInicio" => $rowPro['fechaCirugia'],
                                       "horaInicio"  => $rowPro['horaCirugia'],
                                       "quirofano"   => $rowPro['Turqui'],
                                       "centroCostos"=> $ccoQuirofano[$rowPro['Turqui']],
                                       "tipoCirugia" => "ham" );

                          if( in_array( $rowPro['Enlpro'], $codigosPartos ) ){
                              //array_urgencias_partos
                              $guardarEnTotal = ( $wtipo == "%" or $wtipo == "p" ) ? true : false;
                              guardarEnArreglos( $array_programadas_partos, $array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                            }else{
                              $guardarEnTotal = ( $wtipo == "%" or $wtipo == "a" ) ? true : false;
                              guardarEnArreglos( $array_programadas_amb, $array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                            }
                          //guardarEnArreglos( &$array_programadas_amb, &$array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                        }else{
                            $aux = array("turno"       => $rowPro['Enltur'],
                                         "historia"    => $rowPro['Enlhis'],
                                         "ingreso"     => $rowPro['Enling'],
                                         "fechaInicio" => $rowPro['fechaCirugia'],
                                         "horaInicio"  => $rowPro['horaCirugia'],
                                         "quirofano"   => $rowPro['Turqui'],
                                         "centroCostos"=> $ccoQuirofano[$rowPro['Turqui']],
                                         "tipoCirugia" => "hos" );

                            if( in_array( $rowPro['Enlpro'], $codigosPartos ) ){
                              //array_urgencias_partos
                              $guardarEnTotal = ( $wtipo == "%" or $wtipo == "p" ) ? true : false;
                              guardarEnArreglos( $array_programadas_partos, $array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                            }else{
                              $guardarEnTotal = ( $wtipo == "%" or $wtipo == "h" ) ? true : false;
                              guardarEnArreglos( $array_programadas_hos, $array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                            }
                            //guardarEnArreglos( &$array_programadas_hos, &$array_programadas_total, $ccoQuirofano[$rowPro['Turqui']], $anio, $mes, $aux, $guardarEnTotal );
                        }
                    }
                    // fin calculo
                }
              /* FIN CIRUGIAS PROGRAMADAS */
            }

            //--> pintura del reporte<----//
            $html = "";
            $columnasTotales = 2;
            foreach ( $anios_meses as $keyAnio => $meses ) {
                $columnasTotales = count( $meses );
                $cantidadMeses   = $columnasTotales;
            }
            if( $hayDatos ){

              if( $wclase == "%" or $wclase == "u" ){//--> si se pidió impresión de urgencias
                $html .= "<br><div class='div_accordion' style='width:100%;'>";
                  $html .= "<h3>Cirugias realizadas por Urgencias</h3>";
                    $html  .= "<div>";
                  foreach ( $maestroCcos as $keyCco => $datosCcos ){
                      if( $wcco == "%" or $wcco == $keyCco){
                          $html  .= "<table style='width:80%' class='tbl_contenedora' >";
                            $html .= "<tr>";
                              $html  .= "<td colspan='$columnasTotales'> <img height='19' src='../../images/medical/root/chart.png' tooltip='si' style='background-color: #FFFFFF;cursor:pointer;' titlesegmento='&lt;span style=&quot;color: #000000;font-size: 10pt;&quot;&gt;Graficar&lt;/span&gt;' alt='' cco='{$keyCco}' clase='u' onclick='graficar( this );'> $keyCco - {$maestroCcos[$keyCco]['nombre']} </td>";
                            $html .= "</tr>";
                            $html .= "<tr class='tr_encabezado_reporte'>";
                                $html .= "<td align='center' class='td_subtitulo encabezadoTabla  td_tipo_datos'> A&ntilde;o: </td>";
                                foreach ( $anios_meses as $keyAnio => $meses ) {
                                  $html .= "<td align='center' class='td_subtitulo encabezadoTabla ' colspan='".count( $meses )."' >$keyAnio</td>";
                                }
                                $html .= "<td class='td_subtitulo encabezadoTabla '>&nbsp;</td>";
                                $html .= "<td class='td_subtitulo encabezadoTabla '>&nbsp;</td>";
                                $html .= "<td class='td_subtitulo encabezadoTabla '>&nbsp;</td>";
                             $html .= "</tr>";
                             $html .= "<tr class='tr_encabezado_reporte'>";
                              $html .= "<td align='center' class='td_subtitulo encabezadoTabla  td_tipo_datos'> Mes: </td>";
                              foreach ( $anios_meses as $keyAnio => $meses ) {
                                foreach ($meses as $keyMes => $datos) {
                                         $cantidad = count( $anios_meses[$keyAnio][$keyMes] );
                                        $html .= "<td align='center' class='td_subtitulo encabezadoTabla td_mes' clase='u' cco='{$keyCco}' mes='{$keyAnio}_{$keyMes}'>".$nombre_meses[$keyMes]."</td>";
                                }
                                  //echo $keyAnio;
                              }
                               $html .= "<td align='center' class='td_subtitulo encabezadoTabla ' >Acum.</td>";
                               $html .= "<td align='center' class='td_subtitulo encabezadoTabla ' >Prom. mes</td>";
                               $html .= "<td align='center' class='td_subtitulo encabezadoTabla ' >%</td>";
                             $html .= "</tr>";
                             if( $wtipo == "%" or $wtipo == "h"){
                                $html .= "<tr>";
                                  $html .= "<td class='td_subtitulo encabezadoTabla  td_tipo_datos tipoAgraficar' tipo='h'> Hospitalarias </td>";
                                  $acumulado = 0;
                                  foreach ( $anios_meses as $keyAnio => $meses ) {
                                    foreach ($meses as $keyMes => $datos) {
                                      $cantidad = count( $array_urgencias_hos[$keyCco][$keyAnio][$keyMes] );
                                      $acumulado += $cantidad;
                                      $detalle = construirDetalle( $keyAnio, $keyMes, $keyCco ,"u","h", $array_urgencias_hos[$keyCco][$keyAnio][$keyMes] );
                                      $nombre  = "Cirugias Urgencias hospitalarias";
                                      $html .= "<td align='center' class='td_dato fila1' cco='{$keyCco}' mes='{$keyAnio}_{$keyMes}' clase='u' tipo='h' onclick='mostrar_detalle( \"$keyAnio\",\"$keyMes\",\"$keyCco\",\"u\",\"h\", \" DETALLE {$nombre} {$keyAnio}, {$keyMes}\")' dato='{$cantidad}' >$cantidad $detalle</td>";
                                    }
                                  }
                                  $html .= "<td align='center' class='td_dato fila1'> $acumulado </td>";
                                  $promedio = round( $acumulado / $cantidadMeses );
                                  $html .= "<td align='center' class='td_dato fila1' > $promedio </td>";
                                  $html .= "<td align='center' class='td_dato fila1' tipo='porcentaje' > &nbsp; </td>";
                                $html .= "</tr>";
                             }

                             if( $wtipo == "%" or $wtipo == "a"){
                               $html .= "<tr>";
                               $acumulado = 0;
                                $html .= "<td class='td_subtitulo encabezadoTabla  td_tipo_datos tipoAgraficar' tipo='a'> Ambulatorias </td>";
                                foreach ( $anios_meses as $keyAnio => $meses ) {
                                  foreach ($meses as $keyMes => $datos) {
                                    $cantidad = count( $array_urgencias_amb[$keyCco][$keyAnio][$keyMes] );
                                    $acumulado += $cantidad;
                                    $detalle = construirDetalle( $keyAnio, $keyMes, $keyCco ,"u","a", $array_urgencias_amb[$keyCco][$keyAnio][$keyMes] );
                                    $nombre  = "Cirugias Urgencias ambulatorias";
                                    $html .= "<td align='center' class='td_dato fila1' cco='{$keyCco}' mes='{$keyAnio}_{$keyMes}' clase='u' tipo='a' onclick='mostrar_detalle( \"$keyAnio\",\"$keyMes\",\"$keyCco\",\"u\",\"a\", \" DETALLE {$nombre} {$keyAnio}, {$keyMes}\")' dato='{$cantidad}' >$cantidad $detalle</td>";
                                  }
                                  //echo $keyAnio;
                                }
                                  $html .= "<td align='center' class='td_dato fila1' > $acumulado </td>";
                                  $promedio = round( $acumulado / $cantidadMeses );
                                  $html .= "<td align='center' class='td_dato fila1' > $promedio </td>";
                                  $html .= "<td align='center' class='td_dato fila1' tipo='porcentaje' > &nbsp; </td>";
                                $html .= "</tr>";
                             }

                             if( $wtipo == "%" or $wtipo == "p" ){
                                 $html .= "<tr>";
                                  $html .= "<td class='td_subtitulo encabezadoTabla  td_tipo_datos tipoAgraficar' tipo='p'> Partos </td>";
                                  $acumulado = 0;
                                  foreach ( $anios_meses as $keyAnio => $meses ) {
                                    foreach ($meses as $keyMes => $datos) {
                                      $cantidad = count( $array_urgencias_partos[$keyCco][$keyAnio][$keyMes] );
                                      $acumulado += $cantidad;
                                      $detalle = construirDetalle( $keyAnio, $keyMes, $keyCco ,"u","p", $array_urgencias_partos[$keyCco][$keyAnio][$keyMes] );
                                      $nombre  = "Partos Urgencias";
                                      $html .= "<td align='center' class='td_dato fila1' cco='{$keyCco}' mes='{$keyAnio}_{$keyMes}' clase='u' tipo='p' onclick='mostrar_detalle( \"$keyAnio\",\"$keyMes\",\"$keyCco\",\"u\",\"p\", \" DETALLE {$nombre} {$keyAnio}, {$keyMes}\")' dato='{$cantidad}' >$cantidad $detalle</td>";
                                    }
                                    //echo $keyAnio;
                                  }
                                    $html .= "<td align='center' class='td_dato fila1' > $acumulado </td>";
                                    $promedio = round( $acumulado / $cantidadMeses );
                                    $html .= "<td align='center' class='td_dato fila1' > $promedio </td>";
                                    $html .= "<td align='center' class='td_dato fila1' tipo='porcentaje' > &nbsp; </td>";
                                  $html .= "</tr>";
                              }

                              $html .= "<tr>";
                                $html .= "<td class='td_subtitulo encabezadoTabla  td_tipo_datos' tipo='t'> TOTALES </td>";
                                $acumulado = 0;
                                foreach ( $anios_meses as $keyAnio => $meses ) {
                                  foreach ($meses as $keyMes => $datos) {
                                    $cantidad = count( $array_urgencias_total[$keyCco][$keyAnio][$keyMes] );
                                    $acumulado += $cantidad;
                                    $nombre  = "Cirugias Urgencias";
                                    $detalle = construirDetalle( $keyAnio, $keyMes, $keyCco ,"u","t", $array_urgencias_total[$keyCco][$keyAnio][$keyMes] );
                                    $html .= "<td align='center' class='td_dato fila1' cco='{$keyCco}' mes='{$keyAnio}_{$keyMes}' clase='u' tipo='t' onclick='mostrar_detalle( \"$keyAnio\",\"$keyMes\",\"$keyCco\",\"u\",\"t\", \" DETALLE {$nombre} {$keyAnio}, {$keyMes}\")' dato='{$cantidad}' >$cantidad $detalle</td>";
                                  }
                                }
                                $html .= "<td align='center' class='td_dato fila1' tipo='acumulado' > $acumulado </td>";
                                $promedio = round( $acumulado / $cantidadMeses );
                                $html .= "<td align='center' class='td_dato fila1' > $promedio </td>";
                                $html .= "<td align='center' class='td_dato fila1' > 100 </td>";
                              $html .= "</tr>";
                          $html  .= "</table>";
                        $html  .= "<br>";
                      }
                  }
                $html .= "</div></div>";
              }

              if( $wclase == "%" or $wclase == "P" ){//--> si se pidió impresión de urgencias
                $html .= "<br><br>";
                $html .= "<div class='div_accordion' style='width:100%;'>";
                  $html .= "<h3>Cirugias Programadas</h3>";
                    $html  .= "<div>";
                  foreach ( $maestroCcos as $keyCco => $datosCcos ) {
                    if( $wcco == "%" or $wcco == $keyCco ){
                      //echo "<br>----->".$wclase."----><br>";
                        $html  .= "<table style='width:80%' class='tbl_contenedora' >";
                          $html .= "<tr>";
                            $html  .= "<td colspan='$columnasTotales'> <img height='19' src='../../images/medical/root/chart.png' tooltip='si' style='background-color: #FFFFFF;cursor:pointer;' titlesegmento='&lt;span style=&quot;color: #000000;font-size: 10pt;&quot;&gt;Graficar&lt;/span&gt;' alt='' cco='{$keyCco}' clase='p' onclick='graficar( this );'> $keyCco - {$maestroCcos[$keyCco]['nombre']} </td>";
                          $html .= "</tr>";
                          $html .= "<tr class='tr_encabezado_reporte'>";
                              $html .= "<td align='center' class='td_subtitulo encabezadoTabla  td_tipo_datos'> A&ntilde;o: </td>";
                              foreach ( $anios_meses as $keyAnio => $meses ) {
                                $html .= "<td align='center' class='td_subtitulo encabezadoTabla ' colspan='".count( $meses )."' >$keyAnio</td>";
                              }
                              $html .= "<td class='td_subtitulo encabezadoTabla '>&nbsp;</td>";
                              $html .= "<td class='td_subtitulo encabezadoTabla '>&nbsp;</td>";
                              $html .= "<td class='td_subtitulo encabezadoTabla '>&nbsp;</td>";
                           $html .= "</tr>";

                           $html .= "<tr class='tr_encabezado_reporte'>";
                            $html .= "<td align='center' class='td_subtitulo encabezadoTabla  td_tipo_datos'> Mes: </td>";
                            foreach ( $anios_meses as $keyAnio => $meses ) {
                              foreach ($meses as $keyMes => $datos) {
                                       $cantidad = count( $anios_meses[$keyAnio][$keyMes] );
                                      $html .= "<td align='center' class='td_subtitulo encabezadoTabla td_mes' clase='p' cco='{$keyCco}' mes='{$keyAnio}_{$keyMes}'>".$nombre_meses[$keyMes]."</td>";
                              }
                                //echo $keyAnio;
                            }
                            $html .= "<td align='center' class='td_subtitulo encabezadoTabla ' >Acum.</td>";
                            $html .= "<td align='center' class='td_subtitulo encabezadoTabla ' >Prom. mes</td>";
                            $html .= "<td align='center' class='td_subtitulo encabezadoTabla ' tipo='porcenteje' >%</td>";
                           $html .= "</tr>";

                           if( $wtipo == "%" or $wtipo == "h" ){
                              $html .= "<tr>";
                                $html .= "<td class='td_subtitulo encabezadoTabla  td_tipo_datos tipoAgraficar' tipo='h'> Hospitalarias </td>";
                                $acumulado = 0;
                                foreach ( $anios_meses as $keyAnio => $meses ) {
                                  foreach ($meses as $keyMes => $datos) {
                                    $cantidad = count( $array_programadas_hos[$keyCco][$keyAnio][$keyMes] );
                                    $acumulado += $cantidad;
                                    $nombre  = "Cirugias Programadas hospitalarias";
                                    $detalle = construirDetalle( $keyAnio, $keyMes, $keyCco ,"p","h", $array_programadas_hos[$keyCco][$keyAnio][$keyMes] );
                                    $html .= "<td align='center' cco='{$keyCco}' clase='p' tipo='h' class='td_dato fila1' mes='{$keyAnio}_{$keyMes}' onclick='mostrar_detalle( \"$keyAnio\",\"$keyMes\",\"$keyCco\",\"p\",\"h\", \" DETALLE {$nombre} {$keyAnio}, {$keyMes}\")' dato='{$cantidad}' >$cantidad $detalle</td>";
                                  }
                                }
                                $html .= "<td align='center' class='td_dato fila1' > $acumulado </td>";
                                $promedio = round( $acumulado / $cantidadMeses );
                                $html .= "<td align='center' class='td_dato fila1' > $promedio </td>";
                                $html .= "<td align='center' class='td_dato fila1' tipo='porcentaje' > &nbsp; </td>";
                              $html .= "</tr>";
                           }

                           if( $wtipo == "%" or $wtipo == "a" ){
                             $html .= "<tr>";
                              $html .= "<td class='td_subtitulo encabezadoTabla  td_tipo_datos tipoAgraficar' tipo='a'> Ambulatorias </td>";
                              $acumulado = 0;
                              foreach ( $anios_meses as $keyAnio => $meses ) {
                                foreach ($meses as $keyMes => $datos) {
                                  $cantidad = count( $array_programadas_amb[$keyCco][$keyAnio][$keyMes] );
                                  $acumulado += $cantidad;
                                  $detalle = construirDetalle( $keyAnio, $keyMes, $keyCco ,"p","a", $array_programadas_amb[$keyCco][$keyAnio][$keyMes] );
                                  $nombre  = "Cirugias Programadas ambulatorias";
                                  $html .= "<td align='center' class='td_dato fila1' cco='{$keyCco}' mes='{$keyAnio}_{$keyMes}' clase='p' tipo='a' onclick='mostrar_detalle( \"$keyAnio\",\"$keyMes\",\"$keyCco\",\"p\",\"a\", \" DETALLE {$nombre} {$keyAnio}, {$keyMes}\")' dato='{$cantidad}' >$cantidad $detalle</td>";
                                }
                                //echo $keyAnio;
                              }
                                $html .= "<td align='center' class='td_dato fila1' > $acumulado </td>";
                                $promedio = round( $acumulado / $cantidadMeses );
                                $html .= "<td align='center' class='td_dato fila1' > $promedio </td>";
                                $html .= "<td align='center' class='td_dato fila1' tipo='porcentaje' > &nbsp; </td>";
                              $html .= "</tr>";
                           }

                           if( $wtipo == "%" or $wtipo == "p" ){
                             $html .= "<tr>";
                              $html .= "<td class='td_subtitulo encabezadoTabla  td_tipo_datos tipoAgraficar' tipo='p'> Partos </td>";
                              $acumulado = 0;
                              foreach ( $anios_meses as $keyAnio => $meses ) {
                                foreach ($meses as $keyMes => $datos) {
                                  $cantidad = count( $array_programadas_partos[$keyCco][$keyAnio][$keyMes] );
                                  $acumulado += $cantidad;
                                  $detalle = construirDetalle( $keyAnio, $keyMes, $keyCco ,"p","p", $array_programadas_partos[$keyCco][$keyAnio][$keyMes] );
                                  $nombre  = "Partos Programados";
                                  $html .= "<td align='center' class='td_dato fila1' cco='{$keyCco}' mes='{$keyAnio}_{$keyMes}' clase='p' tipo='p' onclick='mostrar_detalle( \"$keyAnio\",\"$keyMes\",\"$keyCco\",\"p\",\"p\", \" DETALLE {$nombre} {$keyAnio}, {$keyMes}\")' dato='{$cantidad}' >$cantidad $detalle</td>";
                                }
                                //echo $keyAnio;
                              }
                                $html .= "<td align='center' class='td_dato fila1' > $acumulado </td>";
                                $promedio = round( $acumulado / $cantidadMeses );
                                $html .= "<td align='center' class='td_dato fila1' > $promedio </td>";
                                $html .= "<td align='center' class='td_dato fila1' tipo='porcentaje' > &nbsp; </td>";
                              $html .= "</tr>";
                           }

                           $html .= "<tr>";
                             $html .= "<td class='td_subtitulo encabezadoTabla  td_tipo_datos' tipo='t'> TOTALES </td>";
                             $acumulado = 0;
                             foreach ( $anios_meses as $keyAnio => $meses ) {
                              foreach ($meses as $keyMes => $datos) {
                                 $cantidad = count( $array_programadas_total[$keyCco][$keyAnio][$keyMes] );
                                 $acumulado += $cantidad;
                                 $nombre  = "Cirugias Programadas";
                                 $detalle = construirDetalle( $keyAnio, $keyMes, $keyCco ,"p","t", $array_programadas_total[$keyCco][$keyAnio][$keyMes] );
                                 $html .= "<td align='center' class='td_dato fila1' dato='{$cantidad}' cco='{$keyCco}' clase='p' tipo='t' onclick='mostrar_detalle( \"$keyAnio\",\"$keyMes\",\"$keyCco\",\"p\",\"t\", \" DETALLE {$nombre} {$keyAnio}, {$keyMes}\")' dato='{$cantidad}' >$cantidad $detalle</td>";
                               }
                             }
                             $html .= "<td align='center' class='td_dato fila1'  tipo='acumulado'> $acumulado </td>";
                             $promedio = round( $acumulado / $cantidadMeses );
                                $html .= "<td align='center' class='td_dato fila1' > $promedio </td>";
                                $html .= "<td align='center' class='td_dato fila1' > 100 </td>";
                            $html .= "</tr>";
                        $html  .= "</table>";
                      $html  .= "<br>";
                    }
                  }
                $html .= "</div></div><br>";
              }

              echo $html;
            }else{
               echo "<br /><br /><br /><br />
                    <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                        [?] No hay cirugias liquidadas para el periodo de tiempo consultado.
                   </div>";
            }
            //--> final pintura del reporte<----//



        default:
            # code...
            break;
    }
    return;
}

/////----> FUNCIONES
function consultarAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){
    $q = " SELECT
                Detval
            FROM
                root_000051
            WHERE
                Detemp = '".$codigoInstitucion."'
                AND Detapl = '".$nombreAplicacion."'";

    //  echo $q;
    $res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
    $num = mysql_num_rows($res);

    $alias = "";
    if ($num > 0)
    {
        $rs = mysql_fetch_array($res);

        $alias = $rs['Detval'];
    }

    return $alias;
}

function inicializarArreglos(){

    global $centrosCostos;
    global $conex;
    global $wmovhos;
    global $wemp_pmla;

    $caracteres    = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
    $caracteres2   = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");

    $query = "  SELECT tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                  FROM {$wmovhos}_000011 AS tb1
                 WHERE tb1.Ccoest = 'on'
                   AND tb1.Ccocir = 'on'
                 ORDER BY nombre";

    $result = mysql_query( $query, $conex ) or die(mysql_error());
    while($row2 = mysql_fetch_array($result)){
         $row2['nombre'] = utf8_encode( $row2['nombre'] );
         $row2['nombre'] = str_replace( $caracteres, $caracteres2, $row2['nombre'] );
         array_push( $centrosCostos, trim($row2['codigo']).", ".trim($row2['nombre']) );
         /*$centrosCostos['valores'] = array( "codigo"=>$row2['codigo'], "nombre"=>$row2['nombre'])
         $centrosCostos['mostrar'] =$row2['codigo']",".$row2['nombre'];*/

    }
}

function construirArregloFechas( $fecha_inicio, $fecha_final ){

  $aux1             = explode( "-", $fecha_inicio );
  $aux2             = explode( "-", $fecha_final  );
  $anioI            = $aux1[0]*1;
  $mesI             = $aux1[1]*1;
  $anioF            = $aux2[0]*1;
  $mesF             = $aux2[1]*1;
  $arregloAnioMeses = array();

  for ( $i = $anioI; $i <= $anioF ; $i++ ) {

    ( $i     == $anioF ) ? $limiteMesS = $mesF : $limiteMesS = 12;
    ( $i     == $anioI ) ? $limiteMesI = $mesI : $limiteMesI = 1;

    for( $j = $limiteMesI; $j <= $limiteMesS ; $j++){
        $indiceMes = ( $j < 10 ) ? "0{$j}" : "{$j}";
        if( !isset($arregloAnioMeses["{$i}"]) )
          $arregloAnioMeses["{$i}"] = array();

        $arregloAnioMeses["{$i}"][$indiceMes] = "";
      }
  }
  //echo "<pre>".print_r( $arregloAnioMeses, true )."</pre>";
  return( $arregloAnioMeses );

}

function construirDetalle( $keyAnio, $keyMes, $keyCco , $wtipo , $wclase , $arregloDetalle, $nombreDetalle = "" ){

  $divDetalle = "<div id='div_{$keyCco}_{$keyAnio}_{$keyMes}_{$wtipo}_{$wclase}' style='display:none;' align='center' class='fila1'>";
  if( count( $arregloDetalle) > 0 ){
    $divDetalle .= "<br /><br />
                      <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;'>
                          [?] el simbolo(*) al lado de la historia indica que el paciente ingres&oacute; por urgencias pero que la cirugia fue programada.
                     </div>";
    $divDetalle .= "<br><table>";
      $divDetalle .= "<tr class='encabezadoTabla'>";
        $divDetalle .= "<td colspan='4'> DETALLE {$nombreDetalle}</td>";
      $divDetalle .= "</tr>";
      $divDetalle .= "<tr class='encabezadoTabla'>";
        $divDetalle .= "<td align='center'> Historia </td>";
        $divDetalle .= "<td align='center'> Ingreso </td>";
        $divDetalle .= "<td align='center'> Num. turno </td>";
        $divDetalle .= "<td align='center'> Fecha </td>";
      $divDetalle .= "</tr>";
      foreach ( $arregloDetalle as $key => $datos ) {
        $divDetalle .= "<tr class='fila2'>";
          $divDetalle .= "<td align='center'> {$datos['historia']}</td>";
          $divDetalle .= "<td align='center'> {$datos['ingreso']} </td>";
          $divDetalle .= "<td align='center'> {$datos['turno']} </td>";
          $divDetalle .= "<td align='center'> {$datos['fechaInicio']} </td>";
        $divDetalle .= "</tr>";
      }
    $divDetalle .= "</table>";
  }else{
    $divDetalle .= "<br /><br />
                      <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                          [?] sin detalle para mostrar.
                     </div>";
  }
  $divDetalle .= "</div>";
  return($divDetalle);
}

function guardarEnArreglos( &$arregloAmodificar, &$arreglototales, $keyCco, $anio, $mes, $aux, $guardarEnTotal ){
  global $wtipo, $wclase;

  if( !isset($arregloAmodificar[$keyCco]) ){
    $arregloAmodificar[$keyCco] = array();
    if( !isset($arreglototales[$keyCco]) )
      $arreglototales[$keyCco] = array();
  }

  if( !isset($arregloAmodificar[$keyCco][$anio]) ){
    $arregloAmodificar[$keyCco][$anio] = array();
    if( !isset($arreglototales[$keyCco][$anio]) )
      $arreglototales[$keyCco][$anio] = array();
  }

  if( !isset($arregloAmodificar[$keyCco][$anio][$mes]) ){
    $arregloAmodificar[$keyCco][$anio][$mes] = array();
    if( !isset($arreglototales[$keyCco][$anio][$mes]) )
      $arreglototales[$keyCco][$anio][$mes] = array();
  }

  array_push( $arregloAmodificar[$keyCco][$anio][$mes] , $aux );
  if( $guardarEnTotal )
    array_push( $arreglototales[$keyCco][$anio][$mes] , $aux );
}
////-----> FIN FUNCIONES
?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1">
    <title> REPORTE DE CIRUGIAS </title>
    <style type="text/css">
        .tabla_formulario{
            border-spacing: 1;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        .subEncabezado{
            /*background-color: #C1E6F2;*/
        }
        .botona{
            font-size:13px;
            font-family:Verdana,Helvetica;
            font-weight:bold;
            color:white;
            background:#638cb5;
            border:0px;
            height:30px;
            margin-left: 1%;
            cursor: pointer;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
         }
         .tbl_contenedora{
            font-family: verdana,arial,sans-serif;
            color:#333333;
            border-style: solid;
            border-width: 1px;
            border-color: #666666;

         }
         .tbl_contenedora td.td_dato {
            border-width: 1px;
            padding: 4px;
            border-style: solid;
            border-color: #666666;
            cursor:pointer;
        }

        .td_subtitulo{
           color:white;
           border-width: 1px;
           padding: 4px;
           border-style: solid;
           border-color: #666666;
        }

        .td_tipo_datos{
          width: 20%;
        }
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
    <script src="../../../include/root/LeerTablaAmericas.js"  type="text/javascript"></script>
    <script src="../../../include/root/amcharts/amcharts.js"  type="text/javascript"></script>
    <script type="text/javascript">//codigo javascript propio
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
    <script type="text/javascript"> // funciones propias

        $(document).ready(function(){

            ccos_nombres_array = new Array();
            var datosCcos = eval( $("#array_ccos").val() );
            for( i in datosCcos ){
                ccos_nombres_array.push( datosCcos[i] );
            }

             $( "#input_cco" ).autocomplete({
                    source    : ccos_nombres_array,
                    minLength : 2,
                    messages: {
                        noResults: '',
                        results: function() {}
                    },
                    select: function( event, ui ) {
                        var ccoSeleccionado = ui.item.value;
                        if( $.trim(ccoSeleccionado) != "" ){
                            ccoSeleccionado = ccoSeleccionado.split(",");
                            ccoSeleccionado = $.trim( ccoSeleccionado[0] );
                            $(this).parent().find("#wcco").val(ccoSeleccionado);
                        }
                    }
            });

            $("#fecha_inicio").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                buttonText: "",
                maxDate: "+0m +0w",
                onSelect: function(dateText, inst ) {
                    fechaMaximaConsulta =  consultarFechaMaxima( dateText );
                    $("#fecha_final").val(fechaMaximaConsulta);
                    $("#fecha_final").datepicker("destroy");
                    $("#fecha_final").datepicker({
                        showOn: "button",
                        buttonImage: "../../images/medical/root/calendar.gif",
                        dateFormat: 'yy-mm-dd',
                        buttonImageOnly: true,
                        changeMonth: true,
                        changeYear: true,
                        minDate: dateText,
                        maxDate: fechaMaximaConsulta,
                        buttonText: ""
                    });
                }
            });
        });

        function consultarFechaMaxima( fecha_inicio1 ){
            var fecha = '';
            $.ajax({
                url  : "rep_cirugias.php",
                type : "post",
                async: false,
                 data:
                {
                    consultaAjax  : "on",
                    consulta      : "fechaMaximaConsulta",
                    wemp_pmla     : $("#wemp_pmla").val(),
                    fecha_inicio  : fecha_inicio1
                },
                success : function(data){
                    if(data != "")
                    {
                        fecha = data;
                    }else{
                    }
                }
            });
            return( fecha );
        }

        function validarVacio( obj ){
            if( $.trim( $(obj).val() ) == "" ){
                $("#wcco").val("%");
            }
        }

        function generarReporte(){

            $("#div_respuesta").hide();
            $("#msjEspereSolicitud").show();
            $.ajax({
                url  : "rep_cirugias.php",
                type : "post",
                async: false,
                 data:
                {
                    consultaAjax : "on",
                    consulta     : "generarReporte",
                    wemp_pmla    : $("#wemp_pmla").val(),
                    wclase       : $("#wclase").val(),
                    wtipo        : $("#wtipo").val(),
                    wcco         : $("#wcco").val(),
                    fecha_inicio : $("#fecha_inicio").val(),
                    fecha_final  : $("#fecha_final").val()
                },
                success : function(data){
                    if(data != "")
                    {
                        $("#div_respuesta").html(data);
                        $("td[tipo='porcentaje']").each(function(){
                          value = $(this).prev("td").prev("td").html()*1;
                          total = $(this).parent().parent().find("td[tipo='acumulado']").html()*1;
                          valor = (value/total) * 100;
                          //$(this).html(Math.round(valor));
                          $(this).html(valor.toFixed(2));
                        });
                        $("#div_respuesta").show();
                        $(".div_accordion").accordion({
                            collapsible: true,
                            active:0,
                            heightStyle: "content",
                            icons: null
                        });
                        $("#msjEspereSolicitud").hide();
                    }else{
                    }
                }
            });
        }

        function mostrar_detalle( keyAnio, keyMes, keyCco, wtipo, wclase, nombreDetalle ){
          var nombre =  "div_"+keyCco+"_"+keyAnio+"_"+keyMes+"_"+wtipo+"_"+wclase;
          $("#"+nombre).dialog({
              title: "<font size='1'> "+nombreDetalle+" </font>",
              modal: true,
              buttons: {
                        Ok: function() {
                        $( this ).dialog( "close" );
                        }
              },
              show: {
                effect: "blind",
                duration: 500
              },
              hide: {
                effect: "blind",
                duration: 500
              },
              height: 400,
              width: 400,
              rezisable: true
            });
        }

        function graficar( obj ){

            var tablapad = $( obj ).parent().parent().parent().parent();
            var campos   = $( tablapad ).find("td.tipoAgraficar").length;
            var clase    = $(obj).attr("clase");
            var tabla    = "";
            var cco      = $(obj).attr("cco");

            if( campos == 0 ){
                alert( "Seleccione los campos que desea comparar" );
                return;
            }

            var arrayCampos = new Array();
            var encabezado  = "<tr><td rowspan='2'>campo</td>";
                encabezado  += "<td colspan='"+( campos  ) +"'> Tabla auxiliar comparativa </td></tr>";
            campos          = campos +1;
            encabezado += "<tr>";
            $(tablapad).find("td.tipoAgraficar").each(function(){
                encabezado += "<td>"+$(this).html().substring(1,4)+"</td>";
                arrayCampos.push( $(this).attr("tipo") );
            });
            encabezado += "</tr>";
            console.log( tabla );
            tabla  = "<table id='comparativa_"+cco+"_"+clase+"' border='1'>";
            tabla += encabezado;
            // buscar filas marcadas, invertir, construir tabla y graficar como en el ejemplo del manual
            $(".td_mes[cco='"+cco+"'][clase='"+clase+"']").each(function(){//las horas reportadas

                var agregar = false;
                fila = "<tr><td>"+ $(this).text() +"</td>";
                for (var i in arrayCampos ) {
                  valor = $(".td_dato[cco='"+cco+"'][clase='"+clase+"'][tipo='"+arrayCampos[i]+"'][mes='"+$(this).attr("mes")+"']").attr("dato");
                  if( $.trim(valor) != "&nbsp;" ){
                    agregar = true;

                  }
                  fila += "<td>"+ valor +" </td>";
                }
                fila += "</tr>";

                if( agregar == true ){
                    tabla += fila;
                }

            });
            tabla += "</table>";
            $("#contenedor_auxiliar").html( tabla );

            $('#contenedor_graficador').html("<center><div id='amchart1' style='border: 1px solid #999999; width:600px; height:350px;'></div></center><br>");
            $("#botonmas").val("Opciones");

            $("#comparativa_"+cco+"_"+clase).LeerTablaAmericas({
                        empezardesdefila : 2,
                        titulo           : 'Grafica Comparativa' ,
                        tituloy          : 'cantidad',
                        filaencabezado   : [1,0],
                        datosadicionales : "todos",
                        rotulos          : "si",
                        tipografico      : 'line'
            });

            $("#contenedor_graficador").dialog({
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                 },
                 show: {
                     effect: "blind",
                     duration: 500
                  },
                 hide: {
                     effect: "blind",
                     duration: 500
                  },
                 height: 600,
                 width: 800,
                 rezisable: true
            });
            $("input[type='radio'][value='line']").trigger("click");
            //$("input[type='radio'][value='line']").trigger("click");
        }

        function cerrarVentana()
        {
            window.close();
        }
    </script>
</head>
<body>
    <?php
        $centrosCostos = array();
        inicializarArreglos();
        //$centrosCostos = json_encode( $centrosCostos );
        $ccoInicial    = "%";
    ?>
    <?php encabezado( " REPORTE DE CIRUGIAS<font size='1'>(liquidadas)</font> ", $wactualiz, "clinica" ); ?>
    <br><br><br>

    <input type='hidden' id='wemp_pmla'       value='<?=$wemp_pmla;?>'>
    <input type='hidden' id='fecha_hoy'       value='<?=date("Y-m-d");?>'>
    <input type='hidden' name='array_ccos' id='array_ccos' value='<?=$centrosCostos?>'>

    <div id='div_formulario' align='center' class='div_formulario'>
        <span class="subtituloPagina2">Par&aacute;metros de consulta</span><br><br>
        <table class='tabla_formulario'>
            <tr>
                <td class='subEncabezado fila1'> CENTRO DE COSTOS: </td>
                <!--<td class='fila2'> <input type='text' id='input_cco' keyup='validarVacio( this );' size='30' > <input type='hidden' id='wcco' name='wcco' value=''></td>-->
                <td class='fila2'>
                    <SELECT id='wcco'>
                        <option value='%' selected > %-TODOS </option>
                        <?php
                            foreach( $centrosCostos as $i => $datosCcos ){
                                $codigo = explode(",", $datosCcos);
                                $codigo = trim( $codigo[0] );
                                echo "<option value='{$codigo}'> $datosCcos </option>";
                            }
                        ?>
                    </SELECT>
                </td>
            </tr>
            <!--<tr>
                <td class='subEncabezado fila1' size='30'> NIVEL: </td>
                <td class='fila2'> <input type='text' id='wnivel'  </td>
            </tr>-->
            <tr>
                <td class='subEncabezado fila1'> CLASE: </td>
                <td class='fila2'>
                    <SELECT id='wclase'>
                        <option value='%'> TODAS </option>
                        <option value='u'> Urgencias </option>
                        <option value='P'> Programadas </option>
                    </SELECT>
                </td>
            </tr>
            <tr>
                <td class='subEncabezado fila1'> TIPO: </td>
                <td class='fila2'>
                    <SELECT id='wtipo'>
                        <option value='%'> TODOS </option>
                        <option value='h'> Hospitalizado </option>
                        <option value='a'> Ambulatorio </option>
                        <option value='p'> Partos </option>
                    </SELECT>
                </td>
            </tr>
            <tr>
                <td class='subEncabezado fila1'> PERIODO: </td><td class='fila2'><input id='fecha_inicio' size='12' type='text' value='<?=date("Y-m-d");?>'> Hasta <input id='fecha_final' size='12' type='text' value='<?=date("Y-m-d");?>'></td>
            </tr>
        </table>
        <br>
        <input type="button" onclick="generarReporte();" value="BUSCAR" class="botona" id="btn_consultar">
    </div><br>
    <center><div id='div_respuesta' style='with:80%;' align='center' class='fila2'>
    </div></center><br>
    <center><input type="button" value='Cerrar Ventana' onclick='cerrarVentana()'></center>
    <div id='contenedor_auxiliar' style='display:none;'></div>
    <div id='contenedor_graficador' style='display:none;'></div>
    <div id='msjEspereSolicitud' align='center' style='display:none;'>
        <br /><br />
          <img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Consultando la informaci&oacute;n (Espere un momento por favor, la operaci&oacute;n puede tardar)...</font>
          <br /><br /><br />
      </div>

</body>
</html>