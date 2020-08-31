<?php
include_once("conex.php");
/*********************************************************
*       UTILIZACION DE CAMAS POR SERVICIO HABITACION     *
*       PORCENTAJES DE UTILIZACION SERVICIO Y CLINICA    *
*                    CONEX, FREE => OK                   *
*********************************************************/

/**********************************************************ACTUALIZACIONES****************************************
2017-05-08 Arleyda Insignares C. se cambia conexion ODBC y el group by (remplazar numeros por el nombre del campo)
2015-10-14 Camilo zz. se corrigió el programa para que tenga en cuenta todos los servicios de los cuales un usuario es coordinador,
                      consultando esto en el árbol de relación
***********************************************************ACTUALIZACIONES************************/
//==================================================================================================================================================//
// Este programa genera un reporte de ventas similar al comprobante. solo que busca los datos por periodo y no por dia
//=====================================================================================
include_once( "root/comun.php" );
require_once("conex.php");
mysql_select_db( "matrix" );
define( "diasRiesgo", 30 );
define( "diasSinRiesgo", 15 );
$wfecha     = date('Y-m-d');
$hora       = date("H:i:s");
$wactualiz  = "2017-05-08";
$wbasedatos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nomina');
$wmovhos    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wtalhuma   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
$wccos      = centroCostoEmpleado();

if( !isset($_SESSION['user']) ){//session muerta en una petición ajax

      echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
             </div>";
      return;
}
///--------------------------->  FUNCIONES <--------------------------------------------------------------------//

function diasEntreFechas($fechainicio, $fechafin)
{
    return (((strtotime($fechafin)-strtotime($fechainicio))/86400)+1);
}

function empresaEmpleado($wemp_pmla, $conex, $wbasedato, $cod_use_emp){
    global $wtalhuma;
    $use_emp = '';

    $user_session = explode('-',$cod_use_emp);
    $user_session = (count($user_session) > 1) ? $user_session[1] : $user_session[0];

    $q = "  SELECT  Codigo, Empresa
            FROM    usuarios
            WHERE   codigo = '".$user_session."'
                    AND Activo = 'A'";
    $res = mysql_query($q,$conex);
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

        $use_emp = $user_session.'-'.$row['Empresa']; // concatena los últimos 5 digitos del código del usuario con el código de la empresa a la que pertenece.
    }else{
        $q = " SELECT Ideuse
                 FROM {$wtalhuma}_000013
                WHERE ideuse = '{$user_session}-{$wemp_pmla}'";
        $rs = mysql_query( $q, $conex );
        $row = mysql_fetch_array($rs);
        if( $row[0] != "" ){
            $use_emp = $user_session.'-'.$wemp_pmla;
        }
    }
    return $use_emp;
}

function centroCostoEmpleado()
{
    global $conex, $wemp_pmla;
    $centrosCostos = array();
    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    if( $row = mysql_fetch_array($res))
    {
        $tabla_CCO = $row['Emptcc'];
        switch ($tabla_CCO)
        {
            case "clisur_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    clisur_000003 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                ORDER BY nombre";
                    break;
            case "farstore_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    farstore_000003 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                ORDER BY nombre";
                    break;
            case "costosyp_000005":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    costosyp_000005 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                GROUP BY tb1.Ccocod
                                ORDER BY nombre";
                    break;
            case "uvglobal_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    uvglobal_000003 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                GROUP BY tb1.Ccocod
                                ORDER BY nombre";
                    break;
            default:
                    $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    costosyp_000005 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                GROUP BY tb1.Ccocod
                                ORDER BY nombre";
        }
        //echo $query;
        $result = mysql_query( $query, $conex ) or die(mysql_error());
        while($row2 = mysql_fetch_array($result)){
            $centrosCostos[$row2['codigo']] = $row2['nombre'];
        }
    }
    return $centrosCostos;
}

function diasLicenciaNoRemunerada( $wfechaInicio, $wfechaFinal, $codigo ){

    global $conexunix;
    $diasLicencia = 0;
    $tuvoLicencia = false;
    //$diasLicencia = array();

    $query  = " SELECT inccod, TO_CHAR(incrfi, 'YYYY-MM-DD'), TO_CHAR(incrff, 'YYYY-MM-DD')
                  FROM noinc
                 WHERE inccod = '{$codigo}'
                   AND incfin >= TO_DATE('{$wfechaInicio}', 'YYYY-MM-DD') and incfin <= TO_DATE('{$wfechaFinal}', 'YYYY-MM-DD')
                   AND ( inccon = '0011' or inccon = '0010' )";

    $rs     = odbc_do( $conexunix,$query );

    while ( odbc_fetch_row($rs) ){
        //echo odbc_field_name($rs,$i)." - ".odbc_result($err_o,$i)."<br>";
        $tuvoLicencia  = true;
        $diasLicencia += diasEntreFechas( odbc_result($rs,2), odbc_result($rs,3) );
    }
    if( !$tuvoLicencia )
        $diasLicencia = -1;
    return $diasLicencia;
}

/**
 * Esta función busca el historial de riesgo del empleado, cuantos dias estuvo en riesgo y cuanto tiempo estuvo sin este.
 *
 * @param unknown $wemp_pmla
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param date $fecha_inicial : Fecha en la que empieza  el periodo
 * @param date $fecha_final : Fecha en la que finaliza el periodo
 * @return string (código del usuario en sus últimos 5 digitos con el código de la empresa concatenado al final xxxxx-xx)
 */
function diasDisponiblesPeriodo( $fecha_inicial, $fecha_final, $wcedula, $arrayPeriodosRiesgo, &$arreglo_empleado, $codigoEmpleado ){

    $inicioPeriodoEfectivoDeRiesgo        = "";
    $finalPeriodoEfectivoDeRiesgo         = "";
    $periodoEnRiesgoConsultado            = array();

    if( !isset( $arreglo_empleado['periodosEnRiesgoDetalle'] ) )
        $arreglo_empleado['periodosEnRiesgoDetalle']= array();

    $meses['riesgo']    = 0;
    $meses['sinRiesgo'] = diasEntreFechas( $fecha_inicial, $fecha_final );

    foreach ( $arrayPeriodosRiesgo as $i => $periodoEnRiesgo ) {
        $omitirPeriodoRiesgo =  false;

        $diasValidacionInicial = diasEntreFechas( $fecha_final, $periodoEnRiesgo['fechaIngreso'] );
        if( $diasValidacionInicial > 0 ){//-->este periodo termina antes de que inicie este periodo en riesgo
            $omitirPeriodoRiesgo = true;
        }

        if( $periodoEnRiesgo['fechaSalida'] != "0000-00-00" and !$omitirPeriodoRiesgo ){


            $diasValidacionInicial = diasEntreFechas( $fecha_inicial, $periodoEnRiesgo['fechaSalida'] );//--> dia inicial del periodo buscado y dia de salida del dia del periodo en riesgo
            if( $diasValidacionInicial < 0  or $omitirPeriodoRiesgo ){//-->este periodo empieza posteriormente a este periodo en riesgo
                $omitirPeriodoRiesgo = true;
            }

        }

        if( !$omitirPeriodoRiesgo ){

            $diferenciaEnDias = diasEntreFechas( $fecha_inicial, $periodoEnRiesgo['fechaIngreso'] ); //--> diferencia entre el inicio del periodo consultado y el inicio del periodo en riesgo
            if( $diferenciaEnDias <= 0 ){//--> quiere decir que el periodo de ingreso al riesgo es anterior al periodo consultado
                $inicioPeriodoEfectivoDeRiesgo = $fecha_inicial;
            }else{
                $inicioPeriodoEfectivoDeRiesgo = $periodoEnRiesgo['fechaIngreso'];
            }

            if( $periodoEnRiesgo['fechaSalida'] == "0000-00-00"  and $periodoEnRiesgo['estado'] == "on" ){
                $finalPeriodoEfectivoDeRiesgo = $fecha_final;
            }

            $diferenciaEnDias = diasEntreFechas( $fecha_final, $periodoEnRiesgo['fechaSalida'] );
            if( $diferenciaEnDias <= 0 ){//--> quiere decir que el periodo de ingreso al riesgo es anterior al periodo consultado
                $finalPeriodoEfectivoDeRiesgo = $periodoEnRiesgo['fechaSalida'];
            }else{
                $finalPeriodoEfectivoDeRiesgo = $fecha_final;
            }
            $diasEnRiesgoAux  = diasEntreFechas( $inicioPeriodoEfectivoDeRiesgo, $finalPeriodoEfectivoDeRiesgo );
            $meses['riesgo']  = $diasEnRiesgoAux;
            $periodoEnRiesgoConsultado['periodo']  = " $inicioPeriodoEfectivoDeRiesgo ---> $finalPeriodoEfectivoDeRiesgo ";
            //$periodoEnRiesgoConsultado['canDias']  = $meses['riesgo'];
            $periodoEnRiesgoConsultado['canDias']  = $diasEnRiesgoAux;

            if( !isset( $arrayPeriodosRiesgoXEmp[$codigoEmpleado]) or !in_array( $inicioPeriodoEfectivoDeRiesgo." ---> ".$finalPeriodoEfectivoDeRiesgo, $arrayPeriodosRiesgoXEmp[$codigoEmpleado] ) ){
                array_push( $arreglo_empleado['periodosEnRiesgoDetalle'], $periodoEnRiesgoConsultado );
                if( !isset( $arrayPeriodosRiesgoXEmp[$codigoEmpleado]) ){
                    $arrayPeriodosRiesgoXEmp[$codigoEmpleado] = array();
                }
                array_push( $arrayPeriodosRiesgoXEmp[$codigoEmpleado], $inicioPeriodoEfectivoDeRiesgo." ---> ".$finalPeriodoEfectivoDeRiesgo);
            }

        }

    }
    $meses['sinRiesgo'] = $meses['sinRiesgo'] - $meses['riesgo'];
    return( $meses );
}

/**
 * se calcula cantidad de dias a los que el empleado tiene derecho. Esto se calcula según la cantidad estandar de dias de vacaciones merecidos por el tipo de riesgo( 15/360 sin riesgo y 30/360 con riesgo ) y la cantidad de dias que el empleado estuvo expuesto o no en un centro, .
 * de costos de alto riesgo.
 *
 * @param unknown $wemp_pmla
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param date $tipoRiesgo : riesgo, sinRiesgo
 * @param date $diasEnTipo : dias del empleado en un centro de costos con el respectivo tipo de riesgo
 * @return float (dias de vacaciones a los que tiene derecho el empleado por el tipo de riesgo)
 */
function calcularDiasGanados( $tipoRiesgo, $diasEnTipo ){
    if( $tipoRiesgo == "riesgo" ){
        $diasGanados = ( diasRiesgo*$diasEnTipo)/360;
    }else{
        $diasGanados = ( diasSinRiesgo*$diasEnTipo)/360;
    }
    return( $diasGanados);
}

function periodosEmpleado( $wcodigoUsuario, $codigoTalhum, $datosEmpleado){

    global $wbasedatos, $conex, $conexunix, $arreglo_empleados, $wfecha, $fechaLimiteBusqueda, $wcodCoordinador;
    $wfecha = $fechaLimiteBusqueda;
    $arrayPeriodosRiesgo                                                 = array();
    $wccoUsuario                                                         = $datosEmpleado['centroCostos'];
    $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['nombre']          = $datosEmpleado['nombre'];
    $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['periodosDetalle'] = array();//--> arreglo que guardará el detalle de los periodos vencidos
    $haDisfrutadoVacaciones                                              = false;
    $query = " SELECT Rieusu usuario, Riefei fechaIngreso, Riefes fechaSalida, Rieest estado
                 FROM {$wbasedatos}_000013
                WHERE Rieusu = '{$wcodigoUsuario}'
                  AND Rieest = 'on'
                ORDER BY id";

    $rs   = mysql_query( $query, $conex ) or die( $query );
    while( $row = mysql_fetch_assoc( $rs ) ){

        if( $row['fechaSalida'] == "0000-00-00" )
            $row['fechaSalida'] = $fechaLimiteBusqueda;

        $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['estuvoEnRiesgo'] = "on";
        $aux['fechaIngreso']                                  = $row['fechaIngreso'];
        $aux['fechaSalida']                                   = $row['fechaSalida'];
        $aux['estado']                                        = $row['estado'];
        $aux['diasRiesgo']                                    = diasEntreFechas( $row['fechaIngreso'], $row['fechaSalida'] );
        array_push( $arrayPeriodosRiesgo, $aux );
    }

    $q = "  SELECT  TO_CHAR(pvadetfci, 'YYYY-MM-DD'), TO_CHAR(pvadetfcf, 'YYYY-MM-DD'), pvadetdia, TO_CHAR(pvafin, 'YYYY-MM-DD'), TO_CHAR(pvaffi, 'YYYY-MM-DD'), pvadia
            FROM    nopvadet, nopva
            WHERE   pvadetcod = '{$wcodigoUsuario}'
                    AND pvadetcod = pvacod
                    AND pvadetsec = pvasec
            ORDER BY pvadetfci ";
    $res = odbc_do($conexunix,$q);

    $i                = 1;
    $wfcumI           = "";
    $wfcumF           = "";
    $wtotdias         = 0;
    $control          = 0;

    //--->sección que pinta las vacaciones ya disfrutadas
    while(odbc_fetch_row($res))
    {
        $control ++;
        $haDisfrutadoVacaciones = true;
        if ($wfcumI=="" && $wfcumF=="")
        {
            $wfcumI=odbc_result($res,1);   //Variable auxiliar para la fecha inicial cumplida
            $wfcumF=odbc_result($res,2);   //Variable auxiliar para la fecha final cumplida
            $wtotdias=odbc_result($res,3);
        }
        else
        {
            if ($wfcumI==odbc_result($res,1) && $wfcumF==odbc_result($res,2))
            {
                $wtotdias=$wtotdias+odbc_result($res,3);
            }
            else
            {
                $wfcumI=odbc_result($res,1);   //Variable auxiliar para la fecha inicial cumplida
                $wfcumF=odbc_result($res,2);   //Variable auxiliar para la fecha final cumplida
                $wtotdias=odbc_result($res,3);
            }
        }
    }

    if( $haDisfrutadoVacaciones ){
        //---> estas son las últimas vacaciones disfrutadas ( de que periodo son )
        $wfec_i=odbc_result($res,1);
        $wfec_f=odbc_result($res,2);


        //--> si en este periodo quedaron dias pendientes y adicionalmente hubo licencias no remuneradas, se mueve la fecha de terminación del periodo.
        $diasLicencia = diasLicenciaNoRemunerada( $wfec_i, $wfec_f, $wcodigoUsuario )+1;//mas uno porque se incluye el dia final
        $wfec_f       = date("Y-m-d", strtotime($wfec_f."+ {$diasLicencia} day")); //se mueve  la Fecha Final próximo período la cantidad de dias que se han dado de licencia
        $aux          = diasDisponiblesPeriodo( $wfec_i, $wfec_f, "", $arrayPeriodosRiesgo, $arreglo_empleados[$wccoUsuario][$wcodigoUsuario],$wcodigoUsuario );//dias a los que se tiene derecho en el último periodo disfrutado
        foreach ($aux as $keyTipo => $dias ) {
            $diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
            $diasDisponibles +=  $diasDisponiblesPeriodo;
            $diasDisponibles = round( $diasDisponibles );
        }
        $diasDisponibles = $diasDisponibles - $wtotdias;
        if( $diasDisponibles > 0 ){

            $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['diasAcumulados']              += $diasDisponibles ;
            $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['periodosVencidos']            += 1;
            $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['diasEnRiesgo']                += $aux['riesgo'];
            $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['periodoDosCompletosVencidos'] += 1;
            $numeroSolcitudesPendientesAux                                                    =  tieneSolicitudPendiente( $wfec_i, $wfec_f, $codigoTalhum );
            $numeroSolcitudesPendientes                                                      += $numeroSolcitudesPendientesAux['solicitudesPendiente'];
            $numeroSolcitudesVencidas                                                        += $numeroSolcitudesPendientesAux['solicitudesVencidas'];
            $numeroSolcitudesAprobadas                                                       += $numeroSolcitudesPendientesAux['solicitudesAprobadas'];
             $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['numeroSolcitudesPendientes'] += $numeroSolcitudesPendientes;
             $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['numeroSolcitudesVencidas']   += $numeroSolcitudesVencidas;
             $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['numeroSolcitudesAprobadas']  += $numeroSolcitudesAprobadas;
            if( $numeroSolcitudesPendientes > 0 ){
                $periodoConSolicitudPendiente = true;
            }else{
                $periodoConSolicitudPendiente = false;
            }

            if( $numeroSolcitudesAprobadas > 0 ){
                $periodoConSolicitudAprobada = "on";
            }else{
                $periodoConSolicitudAprobada = "off";
            }
            $auxiliar = array('periodo' => " $wfec_i ---> $wfec_f", 'diasDisponibles' => $diasDisponibles, 'vencido' => 'on', 'solicitudPendiente'=>$periodoConSolicitudPendiente, 'solicitudAprobada'=>$periodoConSolicitudAprobada );
            array_push( $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['periodosDetalle'], $auxiliar );
        }
    }

    if( !$haDisfrutadoVacaciones ){
        $wfec_f = $datosEmpleado['fechaIngresoInstitucion'];
    }
    $wfecproF  = $wfec_f;
    $wdias     = (diasEntreFechas($wfecproF, $wfecha));
    $wperiodos = ceil($wdias/360);

    for ( $i = 1; $i <= $wperiodos; $i++ ){

        //--> se mueve hacia el siguiente periodo
        $periodoCompleto = false;
        $wfecproI        = date("Y-m-d", strtotime($wfecproF."+1 day"));
        $wfecproF        = date("Y-m-d", strtotime($wfecproF."+1 year"));

        $wfecproFR = $wfecproF;
        if( diasEntreFechas($wfecproF, $wfecha) < 0 ){
            $wfecproF  = $wfecha;
        }else{
             $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['periodoDosCompletosVencidos'] += 1;
             $periodoCompleto = true;
        }

        $diasLicencia    = diasLicenciaNoRemunerada( $wfecproI, $wfecproF, $wcodigoUsuario )+1;//mas uno porque se incluye el dia final
        $wfecproF        = date("Y-m-d", strtotime($wfecproF."+ {$diasLicencia} day")); //se mueve  la Fecha Final próximo período la cantidad de dias que se han dado de licencia
        $diasDisponibles = 0;
        $aux             = diasDisponiblesPeriodo( $wfecproI, $wfecproF, "", $arrayPeriodosRiesgo, $arreglo_empleados[$wccoUsuario][$wcodigoUsuario], $wcodigoUsuario );//dias a los que se tiene derecho en el último periodo disfrutado
        if($aux['riesgo']>0){
            $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['estuvoEnRiesgo'] = "on";
        }

        foreach ($aux as $keyTipo => $dias ) {
            $diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
            $diasDisponibles +=  $diasDisponiblesPeriodo;
            //( $diasDisponibles*1 >= 5 ) ? $diasDisponibles = round( $diasDisponibles ) : $diasDisponibles = 0;
			( $diasDisponibles*1 >= 5 ) ? $diasDisponibles = round( $diasDisponibles ) : $diasDisponibles = round( $diasDisponibles );
        }
        if( $diasDisponibles > 0 ){
            $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['diasAcumulados']   += $diasDisponibles ;
            $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['periodosVencidos'] += 1;
            $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['diasEnRiesgo']     += $aux['riesgo'];
            $numeroSolcitudesPendientes                                            = tieneSolicitudPendiente( $wfecproI, $wfecproFR, $codigoTalhum );
             $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['numeroSolcitudesPendientes'] += $numeroSolcitudesPendientes['solicitudesPendiente'];
             $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['numeroSolcitudesVencidas']   += $numeroSolcitudesPendientes['solicitudesVencidas'];
             $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['numeroSolcitudesAprobadas']  += $numeroSolcitudesPendientes['solicitudesAprobadas'];
            if( $numeroSolcitudesPendientes['solicitudesPendiente'] > 0 ){
                $periodoConSolicitudPendiente = true;
            }else{
                $periodoConSolicitudPendiente = false;
            }
            if( $numeroSolcitudesPendientes['solicitudesVencidas'] > 0 ){
                $periodoConSolicitudVencida = true;
            }else{
                $periodoConSolicitudVencida = false;
            }
            if( $numeroSolcitudesPendientes['solicitudesAprobadas'] > 0 ){
                $periodoConSolicitudAprobada = "on";
            }else{
                $periodoConSolicitudAprobada = "off";
            }
            $auxiliar = array('periodo' => " $wfecproI ---> $wfecproFR", 'diasDisponibles' => $diasDisponibles, 'solicitudPendiente'=>$periodoConSolicitudPendiente, 'solicitudVencida'=>$periodoConSolicitudVencida, 'solicitudAprobada'=>$periodoConSolicitudAprobada );
            ( $periodoCompleto ) ? $auxiliar['vencido'] = 'on' : $auxiliar['vencido'] = 'off';
            array_push( $arreglo_empleados[$wccoUsuario][$wcodigoUsuario]['periodosDetalle'], $auxiliar );
        }
    }
    return;
}

function consultarDatosEmpleados( $tipoBusqueda, $codigoTalhum, $cco ){
    global $conex, $wtalhuma, $conexunix;
    $arreglo     = array();
    $codCompleto = "";
    $codAuxiliar = "";

    if( $tipoBusqueda == "individual" ){
        $query = " SELECT Ideuse codigo, concat( Ideno1, ' ', Ideno2, ' ', Ideap1, ' ', Ideap2 ) as nombre, Idecco centroCostos
                     FROM {$wtalhuma}_000013
                    WHERE Ideuse = '{$codigoTalhum }'
                      AND Ideest = 'on'";
    }else{
        if( $cco ==  "%" ){
            $condicionCco = "";
        }else{
            $auxCco = explode(",", $cco );
            if( count( $auxCco) == 1 )
                $condicionCco = " AND Idecco = '{$cco}' ";
            else{
                $auxCcos = array();
                foreach ($auxCco as $key => $dcco ) {
                    array_push( $auxCcos, "'".$dcco."'" );
                }
                $cco = implode( ",", $auxCcos );
                $condicionCco = " AND Idecco in ( {$cco} ) ";
            }
        }
        $query = " SELECT Ideuse codigo, concat( Ideno1, ' ', Ideno2, ' ', Ideap1, ' ', Ideap2 ) as nombre, Idecco centroCostos
                     FROM {$wtalhuma}_000013
                    WHERE Idecco != ''
                      AND Idecco is not null
                      {$condicionCco}
                      AND Ideest = 'on'";
    }
    $rs    = mysql_query( $query, $conex ) or die ( mysql_error()."   <br>    ".$query."<br>");
    while( $row = mysql_fetch_assoc( $rs ) ){
        $arreglo[$row['codigo']]['nombre']       = str_replace( "NO APLICA", "", $row['nombre'] );
        $arreglo[$row['codigo']]['centroCostos'] = $row['centroCostos'];
        $codCompleto = $row['codigo'];
        $codAuxiliar = explode( "-", $row['codigo'] );
        $codAuxiliar = $codAuxiliar[0];
        $query  = " SELECT perfin
                     FROM noper
                    WHERE percod = '{$codAuxiliar}'";
        $rsfi     = odbc_do( $conexunix,$query );
        while ( odbc_fetch_row($rsfi) ){
            $fechaIngreso =  odbc_result($rsfi,1);
            $arreglo[$codCompleto]['fechaIngresoInstitucion'] = $fechaIngreso;
        }
    }


    return( $arreglo );
}

function tieneSolicitudPendiente( $wfechaInicio, $wfechaFinal, $wcodigoTalhum ){
    global $conex, $wemp_pmla, $wbasedatos, $wcodCoordinador;
    $tiempoReferencia = strtotime( date('Y-m-d') );
    $solicitudes      = array();
    if( $wcodCoordinador == "" or empty( $wcodCoordinador ) ){
        $query =  " SELECT *
                      FROM {$wbasedatos}_000012
                     WHERE Dvause = '{$wcodigoTalhum}'
                       AND Dvapfi = '$wfechaInicio'
                       AND Dvapff = '$wfechaFinal'
                       AND Dvaerc = 'APROBADO'
                       AND dvaern <> 'RECHAZADO' AND dvaern <> 'APROBADO'
                       AND Dvaest = 'on' ";
    }else{
        $query =  " SELECT a.*, 'pendiente' as tipo
                      FROM {$wbasedatos}_000012 a
                     WHERE Dvause = '{$wcodigoTalhum}'
                       AND Dvapfi = '$wfechaInicio'
                       AND Dvapff = '$wfechaFinal'
                       AND (Dvaerc <> 'RECHAZADO' AND dvaerc <> 'APROBADO')
                       AND (dvaern <> 'RECHAZADO' AND dvaern <> 'APROBADO')
                       AND Dvaest = 'on'
                     UNION
                     SELECT a.*, 'aprobada' as tipo
                      FROM {$wbasedatos}_000012 a
                     WHERE Dvause = '{$wcodigoTalhum}'
                       AND Dvapfi = '$wfechaInicio'
                       AND Dvapff = '$wfechaFinal'
                       AND Dvaerc = 'APROBADO'
                       AND (dvaern <> 'APROBADO' OR dvaern <> '')
                       AND Dvaest = 'on'";
    }
    /*if( $wcodigoTalhum == "08230-01" ){
        echo "<pre>".print_r( $query, true )."</pre>";
    }*/
    $rs    = mysql_query( $query, $conex );
    while( $row   = mysql_fetch_assoc( $rs ) ){
        $vencida = false;
        if( ( strtotime( $row['Dvafid'] ) - ( $tiempoReferencia ) ) < 0  and $row['tipo'] == "pendiente"){
            $vencida = true;
            $solicitudes['solicitudesVencidas'] ++;
        }
        if( $wcodCoordinador != "" and !empty( $wcodCoordinador ) ){
            if( $row['tipo'] == "pendiente" ){
                $solicitudes['solicitudesPendiente'] ++;
            }else{
                $solicitudes['solicitudesAprobadas'] ++;
            }
        }else{
            $solicitudes['solicitudesPendiente'] ++;
            $solicitudes['solicitudesAprobadas'] = 0;
        }
    }
    return( $solicitudes );
}

function centroCostoEmpleadoUnico($conex, $wemp_pmla, $wbasedato, $wtalhuma, $cod_talhuma ){
    $arrayAux = array();

    $q        = " SELECT distinct(Ajecco)
                    FROM {$wtalhuma}_000008
                   WHERE Ajeucr = '{$cod_talhuma}'";

    $rs       = mysql_query( $q, $conex );

    while( $row = mysql_fetch_array($rs) ){
        if( $row[0] != "" ){
            array_push( $arrayAux, $row[0] );
        }
    }

    $centroCostoEmpleado = implode( ",", $arrayAux );
    return $centroCostoEmpleado;
}

function buscarCodigoMatrix($conex, $wemp_pmla, $wbasedato, $usuario_talhuma)
{
    $codigo_usumatrix = "";
    $expl = explode("-", $usuario_talhuma);
    $sql = "SELECT  Codigo
            FROM    usuarios
            WHERE   Codigo like '%{$expl['0']}'
                    AND Empresa = '{$expl['1']}'
                    AND Activo = 'A'";
    $result = mysql_query($sql, $conex) or die ("Error árbol de realción ".mysql_error().' => '.$sql);
    if(mysql_num_rows($result) > 0)
    {
        $row = mysql_fetch_array($result);
        $codigo_usumatrix = $row['Codigo'];
    }
    return $codigo_usumatrix;
}

function validarUsuarioCoordinador($conex, $wemp_pmla, $wbasedato, $wtalhuma, $wusuario)
{
    $usuario_coordinador = array("codigo_coordinador"=>"", "lista_solicitantes"=>array());
    $cod_use_emp         = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $wusuario);

    $sql = "SELECT  tlh08.Ajeuco AS subordinado, tlh08.Ajecoo AS sub_coordinador
                    , tlh13.Ideno1 AS nombre1, tlh13.Ideno2 AS nombre2, tlh13.Ideap1 AS apellido1, tlh13.Ideap2 AS apellido2
            FROM    {$wtalhuma}_000008 AS tlh08
                    INNER JOIN
                    {$wtalhuma}_000013 AS tlh13 ON (tlh13.Ideuse = tlh08.Ajeuco AND tlh13.Ideest = 'on')
            WHERE   tlh08.Ajeucr = '{$cod_use_emp}'
                    AND tlh08.Forest = 'on'
            ORDER BY tlh13.Ideno1, tlh13.Ideno2, tlh13.Ideap1, tlh13.Ideap2";

    $result = mysql_query($sql, $conex) or die ("Error árbol de realción ".mysql_error().' => '.$sql);
    if(mysql_num_rows($result) > 0)
    {
        $usuario_coordinador["codigo_coordinador"] = $wusuario;
        while($row = mysql_fetch_array($result))
        {
            $codigo_usumatrix = buscarCodigoMatrix($conex, $wemp_pmla, $wbasedato, $row['subordinado']);

            //---> esto no estaba antes
            $row['subordinadoAux'] = explode( "-", $row['subordinado'] );
            $row['subordinadoAux'] = $row['subordinadoAux'][0];
            //---> hasta acá sobra
            if(!array_key_exists($codigo_usumatrix, $usuario_coordinador["lista_solicitantes"]))
            {
                $usuario_coordinador["lista_solicitantes"][$row['subordinado']] = array();// antes $usuario_coordinador["lista_solicitantes"][$codigo_usumatrix]
            }

            $usuario_coordinador["lista_solicitantes"][$row['subordinadoAux']] = // antes $usuario_coordinador["lista_solicitantes"][$codigo_usumatrix]
                                                array(  "cod_tahuma"      => $row['subordinado'],
                                                        "cod_matrix"      => $codigo_usumatrix,
                                                        "sub_coordinador" => $row['sub_coordinador'],
                                                        "nombre1"         => $row['nombre1'],
                                                        "nombre2"         => str_replace( "NO APLICA", "", $row['nombre2'] ),
                                                        "apellido1"       => $row['apellido1'],
                                                        "apellido2"       => str_replace( "NO APLICA", "", $row['apellido2'] ) );
        }
    }

    $query = "SELECT tlh13.Idedvo AS directivo, tlh13.Idedmv AS diasMinimos, tlh13.Ideced AS cedula
                FROM {$wtalhuma}_000013 AS tlh13
               WHERE tlh13.Ideuse = '{$cod_use_emp}'
                 AND tlh13.Ideest = 'on'";
    $rs    = mysql_query( $query, $conex );
    while( $row = mysql_fetch_assoc($rs) ){
        $usuario_coordinador["directivo"]   = $row['directivo'];
        $usuario_coordinador["diasMinimos"] = $row['diasMinimos'];
        $usuario_coordinador['cedula']      = $row['cedula'];
    }
    return $usuario_coordinador;
}

function inicializarArreglos(){

    global $centrosCostos;
    global $conex;
    global $wbasedato;
    global $wemp_pmla;
    global $empleados;

    $caracteres    = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
    $caracteres2   = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");

    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    if( $row = mysql_fetch_array($res))
    {
        $tabla_CCO = $row['Emptcc'];
        switch ($tabla_CCO)
        {
            case "clisur_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    clisur_000003 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                ORDER BY nombre";
                    break;
            case "farstore_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    farstore_000003 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                ORDER BY nombre";
                    break;
            case "costosyp_000005":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    costosyp_000005 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                GROUP BY tb1.Ccocod
                                ORDER BY nombre";
                    break;
            case "uvglobal_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    uvglobal_000003 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                GROUP BY tb1.Ccocod
                                ORDER BY nombre";
                    break;
            default:
                    $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    costosyp_000005 AS tb1
                                WHERE   tb1.Ccoest = 'on'
                                GROUP BY tb1.Ccocod
                                ORDER BY nombre";
        }
        $result = mysql_query( $query, $conex ) or die(mysql_error());
        while($row2 = mysql_fetch_array($result)){
             $row2['nombre'] = utf8_encode( $row2['nombre'] );
             $row2['nombre'] = str_replace( $caracteres, $caracteres2, $row2['nombre'] );
             array_push( $centrosCostos, trim($row2['codigo']).", ".trim($row2['nombre']) );
             /*$centrosCostos['valores'] = array( "codigo"=>$row2['codigo'], "nombre"=>$row2['nombre'])
             $centrosCostos['mostrar'] =$row2['codigo']",".$row2['nombre'];*/

        }
    }

    $q = " SELECT codigo, descripcion
             FROM usuarios
            WHERE Empresa = '$wemp_pmla'
              AND Activo  = 'A'";

    $result = mysql_query( $q, $conex ) or die(mysql_error());
    while($row2 = mysql_fetch_array($result)){
         $row2['descripcion'] = utf8_encode( $row2['descripcion'] );
         $row2['descripcion'] = str_replace( $caracteres, $caracteres2, $row2['descripcion'] );
         array_push( $empleados, trim($row2['codigo']).", ".trim($row2['descripcion']) );
         /*$centrosCostos['valores'] = array( "codigo"=>$row2['codigo'], "nombre"=>$row2['nombre'])
         $centrosCostos['mostrar'] =$row2['codigo']",".$row2['nombre'];*/

    }
}

function vacacionesAdisfrutar( $codigosBuscados, $wfechaInicioConsulta, $wfechaFinalConsulta ){
    global $conex, $wemp_pmla, $wbasedatos;
    $empleadosConVacaciones = array();

    $condicionCodigos = ( $codigosBuscados != "" ) ? " Dvause IN ($codigosBuscados) AND ": "";
    $query = " SELECT Dvause empleado, Dvafid fechaInicioDisfrute, Dvaffd fechaFinalDisfrute
                 FROM {$wbasedatos}_000012
                WHERE {$condicionCodigos}
                      Dvafid BETWEEN '{$wfechaInicioConsulta}' AND '{$wfechaFinalConsulta}'
                  AND Dvaerc = 'APROBADO'
                  AND Dvaest = 'on'
                UNION
               SELECT Dvause empleado, Dvafid fechaInicioDisfrute, Dvaffd fechaFinalDisfrute
                 FROM {$wbasedatos}_000012
                WHERE {$condicionCodigos}
                      Dvaffd BETWEEN '{$wfechaInicioConsulta}' AND '{$wfechaFinalConsulta}'
                  AND Dvaerc = 'APROBADO'
                  AND Dvaest = 'on'
                GROUP BY empleado,fechaInicioDisfrute,fechaFinalDisfrute
                ORDER BY fechaInicioDisfrute asc, fechaFinalDisfrute asc ";
    $rs = mysql_query( $query, $conex ) or die( "<pre>".print_r( $query, true )."</pre>");
    while( $row = mysql_fetch_assoc( $rs ) ){
        if( !isset($empleadosConVacaciones[$row['empleado']] ) ){
            $empleadosConVacaciones[$row['empleado']] = array();
        }
        $aux = array( 'fechaIniDisfrute'=> $row['fechaInicioDisfrute'], 'fechaFinDisfrute'=> $row['fechaFinalDisfrute'], "diasAusenteEnPeriodo"=>0 );
        array_push( $empleadosConVacaciones[$row['empleado']], $aux );
    }
    return( $empleadosConVacaciones );
}

if( $peticionAjax == "consultarDatos" ){

  //$conexunix                              = odbc_connect('nomina','informix','sco') or die("No se realizó Conexion con el Unix");
  $conexunix                              = odbc_connect('queryx7','','') or die("No se realizó Conexion con el Unix");
  $arreglo_empleados_periodos_vencidos    = array();
  $arreglo_empleados_solicitudes_liquidar = array();
  $arreglo_empleados_solicitudes_vencidas = array();
  $arreglo_empleados                      = array();
  $arrayPeriodosRiesgoXEmp                = array();//--> variable para llevar el control de los periodos en riesgo que se han reportado ya.
  $empleadosDosPeriodosVenc               = 0;//--> variable que cuenta la cantidad de empleados con mas de dos periodos vencidos
  $empleadosEnRiesgo                      = 0;//--> variable que cuenta la cantidad de empleados que han estado en riesgo
  $solicitudesXliquidar                   = 0;//--> variable que cuenta la cantidad solicitudes aprobadas por coordinadores, y que no se han liquidado
  $tabla_respuesta                        = "";
  if( $wcodCoordinador != ""){
     $usuario_coordinador   = validarUsuarioCoordinador($conex, $wemp_pmla, $wbasedato, $wtalhuma, $wcodCoordinador);
  }
  if( !isset( $fechaLimiteBusqueda ) or $fechaLimiteBusqueda == "" )
    $fechaLimiteBusqueda = date('Y-m-d');

  if( $buscarAdisfrutar == "off"){

     if( $wtipoBusqueda == "individual" ){

        $cod_use_Talhuma = empresaEmpleado($wemp_pmla, $conex, $wbasedatos, $wcodigoUsuario);
        $wcodigoUsuario  = substr($wcodigoUsuario, -5);
        $datosEmpleado   = consultarDatosEmpleados( $wtipoBusqueda, $cod_use_Talhuma, $wcco );
        periodosEmpleado( $wcodigoUsuario, $cod_use_Talhuma, $datosEmpleado[$cod_use_Talhuma] );
     }else{//--> si buscan por centro de costos

        $datosEmpleado   = consultarDatosEmpleados( $wtipoBusqueda, $cod_use_Talhuma, $wcco );
        foreach ($datosEmpleado as $codigoTalhuma => $datos ) {
            $wcodigoUsuario = explode("-", $codigoTalhuma );
            $wcodigoUsuario = $wcodigoUsuario[0];
            periodosEmpleado( $wcodigoUsuario, $codigoTalhuma, $datosEmpleado[$codigoTalhuma]  );
        }
      }
      $i = 0;

      if(  count( $arreglo_empleados ) > 0 ){
        //$tabla_respuesta .= "<br><div align='right' style='width:70%;'><table><tr><td align='right'><img src='../../images/medical/root/lupa.png' height='20' width='20' border='0'/></td><td align='right'><input type='text' id='input_buscador' value='' onkeypress='ocultarDetalles( this );' onkeyup='ocultarTablasVacias( this )'></td></tr></table></div>";
        foreach ($wccos as $keyCco => $nombre ) {
            if(count( $arreglo_empleados[$keyCco]) >0 ){
                if( $wcodCoordinador != "" ){
                    $td_aprobadas_titulo = "<td align='center'> Aprobadas </td>";
                    $td_historial_titulo = "<td align='center'> ver <br> historial disfrutadas </td>";
                }else{
                    $td_aprobadas_titulo = "";
                }
                $tabla_respuesta .= "<div class='div_contenedor_cco' align='center' style='width:90%;'>";
                    $tabla_respuesta .= "<h3>CENTRO DE COSTOS: $keyCco - $nombre </h3>";
                    $tabla_respuesta .= "<table class='tbl_empleados'>";
                        $tabla_respuesta .= "<tr class='encabezadotabla' tipo='titulo'><td align='center'>Código</td><td align='center'>Nombre</td><td align='center'>Periodos Completos<br> Cumplidos</td><td align='center'> Dias Acumulados<br>pendientes</td><td align='center'>Estuvo en <br> Riesgo</td><td align='center'>Solicitudes <br>sin Revisar</td><td align='center'>Solicitudes <br>vencidas</td>{$td_aprobadas_titulo}{$td_historial_titulo}</tr>";
                        foreach ( $arreglo_empleados[$keyCco] as $codigoEmpleado => $datos ){
                            if( $wcodCoordinador == "" or ( array_key_exists( $codigoEmpleado, $usuario_coordinador['lista_solicitantes'] ) ) ){
                                $totalEmpleados++;
                                ( $datos['estuvoEnRiesgo'] == "on" ) ? $estuvoEnRiesgoChk = "<img width:'15px' height='15px' src='/matrix/images/medical/movhos/checkmrk.ico'>" : $estuvoEnRiesgoChk = " &nbsp; ";

                                ( $datos['numeroSolcitudesPendientes']*1 > 0 ) ? $datos['numeroSolcitudesPendientes'] = $datos['numeroSolcitudesPendientes'] : $datos['numeroSolcitudesPendientes'] = " &nbsp; ";

                                $solicitudesXvencidas += $datos['numeroSolcitudesVencidas']*1;
                                ( $datos['numeroSolcitudesVencidas']*1 > 0 ) ? $datos['numeroSolcitudesVencidas'] = $datos['numeroSolcitudesVencidas'] : $datos['numeroSolcitudesVencidas'] = " &nbsp; ";

                                $solicitudesXaprobadas += $datos['numeroSolcitudesAprobadas']*1;
                                ( $datos['numeroSolcitudesAprobadas']*1 > 0 ) ? $datos['numeroSolcitudesAprobadas'] = $datos['numeroSolcitudesAprobadas'] : $datos['numeroSolcitudesAprobadas'] = " &nbsp; ";

                                if( $wcodCoordinador != "" ){
                                    $td_aprobados = "<td align='center'>{$datos['numeroSolcitudesAprobadas']}</td>";
                                    //$td_historial = "<td align='center'><font color='blue' size='3' onclick='mostrarHistorialVacaciones(\"$codigoEmpleado\");'>ver</font></td>";
                                    $td_historial = "<td align='center'><a href='../procesos/vacaciones.php?wemp_pmla={$wemp_pmla}&wconsultaExterna=on&wusuarioConsultado={$codigoEmpleado}' target='_blank'><font color='blue'>ver</font></a></td>";
                                }
                                $tabla_respuesta .= "<tr class='fila2 resumen' style='cursor:pointer;'><td align='left'>{$codigoEmpleado}</td><td align='left'>{$datos['nombre']}</td><td align='center'>{$datos['periodoDosCompletosVencidos']}</td><td align='center'>{$datos['diasAcumulados']}</td><td align='center'>$estuvoEnRiesgoChk</td><td align='center'>{$datos['numeroSolcitudesPendientes']}</td><td align='center'>{$datos['numeroSolcitudesVencidas']}</td>{$td_aprobados}{$td_historial}</tr>";
                                if( $datos['periodoDosCompletosVencidos'] >= 2 ){
                                    $empleadosDosPeriodosVenc++;
                                }
                                if( $datos['estuvoEnRiesgo'] == "on" ){
                                    $empleadosEnRiesgo++;
                                }
                                // segmento del detalle
                                if( $wcodCoordinador != "" ){
                                    $colspan1             = "9";
                                }else{
                                    $colspan1             = "7";
                                }
                                $tabla_respuesta .= "<tr style='display:none;' tipo='titulo' class='tr_detalle'><td align='center' colspan='$colspan1' class=' fila1 detalle '>";
                                    $tabla_respuesta .= "<div><br>";
                                    $tabla_respuesta .= "<span class='subtituloPagina2'><font size='2'>*{$datos['nombre']}*</font></span><br><br>";
                                        if( count( $datos['periodosDetalle'] ) == 0 ){
                                            $tabla_respuesta .= " <span class='subtituloPagina2'>Sin periodos cumplidos con dias pendientes por disfrutar</span><br><br>";
                                        }else{
                                            if( $wcodCoordinador != "" ){
                                                $td_aprobadas_titulo = "<td align='center'> Aprobada </td>";
                                                $colspan             = "7";
                                                }else{
                                                    $td_aprobadas_titulo = "";
                                                    $colspan             = "5";
                                                }
                                            $tabla_respuesta .= "<table>";
                                                $tabla_respuesta .= "<tr tipo='titulo' class='encabezadotabla'><td align='center' colspan='$colspan'> Detalle periodos cumplidos con dias por Disfrutar</td></tr>";
                                                $tabla_respuesta .= "<tr tipo='titulo' class='encabezadotabla'><td align='center'> Periodo</td><td align='center'> Dias</td><td align='center'> Cumplido </td><td align='center'> Solicitud Pendiente </td><td align='center'> Solicitud Vencida </td>{$td_aprobadas_titulo}</tr>";
                                                foreach ( $datos['periodosDetalle'] as $j => $datosPeriodo ) {
                                                    ( $datosPeriodo['vencido'] == "on" ) ? $vencido = "<img width:'15px' height='15px' src='/matrix/images/medical/movhos/checkmrk.ico'>" : $vencido = " En curso ";
                                                    if( $datosPeriodo['solicitudPendiente'] ){
                                                        $solicitudPendienteIco = "<img width:'15px' height='15px' src='/matrix/images/medical/movhos/checkmrk.ico'>";
                                                        $solicitudesXliquidar++;
                                                    }else{
                                                        $solicitudPendienteIco = " &nbsp; ";
                                                    }
                                                    if( $datosPeriodo['solicitudVencida'] ){
                                                        $solicitudVencidaIco = "<img width:'15px' height='15px' src='/matrix/images/medical/movhos/checkmrk.ico'>";
                                                    }else{
                                                        $solicitudVencidaIco = " &nbsp; ";
                                                    }

                                                    if( $datosPeriodo['solicitudAprobada'] == "on" and $wcodCoordinador != "" ){
                                                        $solicitudAprobadaIco = "<td align='center'><img width:'15px' height='15px' src='/matrix/images/medical/movhos/checkmrk.ico'></td>";
                                                    }else{
                                                        $solicitudAprobadaIco = "<td align='center'>&nbsp;</td>";
                                                    }
                                                    if( $wcodCoordinador == "" ){
                                                        $solicitudAprobadaIco = "";
                                                    }
                                                    $tabla_respuesta .= "<tr tipo='titulo' class='fila2'><td align='center'>{$datosPeriodo['periodo']}</td><td align='center'> {$datosPeriodo['diasDisponibles']}</td><td align='center'>".$vencido."</td><td align='center'>".$solicitudPendienteIco."</td><td align='center'>".$solicitudVencidaIco."</td>{$solicitudAprobadaIco}</tr>";
                                                }
                                            $tabla_respuesta .= "</table><br>";
                                        }
                                        if( count( $datos['periodosEnRiesgoDetalle'] ) == 0 ){
                                            $tabla_respuesta .= " <span class='subtituloPagina2'>El empleado no estuvo en riesgo</span><br><br>";
                                        }else{
                                            $tabla_respuesta .= "<table>";
                                                $tabla_respuesta .= "<tr class='encabezadotabla'><td align='center' colspan='2'> Detalle de periodos en los que el empleado estuvo en riesgo</td></tr>";
                                                $tabla_respuesta .= "<tr class='encabezadotabla'><td align='center'> Periodo</td><td align='center'> Dias</td></tr>";
                                                foreach ( $datos['periodosEnRiesgoDetalle'] as $k => $datosPeriodoRiesgo ) {
                                                    $tabla_respuesta .= "<tr class='fila2'><td align='center'>{$datosPeriodoRiesgo['periodo']}</td><td align='center'> {$datosPeriodoRiesgo['canDias']}</td></tr>";
                                                }
                                            $tabla_respuesta .= "</table><br>";
                                        }
                                    $tabla_respuesta .= "</div>";
                                $tabla_respuesta .= "</td></tr>";
                                // final del detalle
                            }
                        }
                    $tabla_respuesta .= "</table>";
                $tabla_respuesta .= "</div><br>";
            }
        }
        if( $wcodCoordinador == ""){
            $tipoDeAccion = "liquidar";
        }else{
            $tipoDeAccion = "revisar";
        }
        if( $wtipoBusqueda != "individual" ){
            $tabla_respuesta .= "<br><div align='center' style='width:70%;'>";
                $tabla_respuesta .= "<table style='width:70%;'>";
                $tabla_respuesta .= "<tr class='encabezadotabla'><td> INDICADOR </td><td>VALOR</td></tr>";
                    $tabla_respuesta .= "<tr class='fila2'><td> TOTAL Empleados consultados </td><td align='center'>$totalEmpleados</td></tr>";
                    $tabla_respuesta .= "<tr class='fila2'><td> Empleados con 2 o mas periodos vencidos </td><td align='center'>$empleadosDosPeriodosVenc</td></tr>";
                    $tabla_respuesta .= "<tr class='fila2'><td> Empleados que han estado en riesgo </td><td align='center'>$empleadosEnRiesgo</td></tr>";
                    $tabla_respuesta .= "<tr class='fila2'><td> Solicitudes X {$tipoDeAccion} </td><td align='center'>$solicitudesXliquidar</td></tr>";
                    $tabla_respuesta .= "<tr class='fila2'><td> Solicitudes Vencidas </td><td align='center'>$solicitudesXvencidas</td></tr>";
                    if( $wcodCoordinador != "" )
                        $tabla_respuesta .= "<tr class='fila2'><td> Solicitudes Aprobadas </td><td align='center'>$solicitudesXaprobadas</td></tr>";
                $tabla_respuesta .= "</table>";
            $tabla_respuesta .= "</div>";
        }

      }
  }else{//---> entra acá si se está consultando

    $datosEmpleado   = consultarDatosEmpleados( $wtipoBusqueda, $cod_use_Talhuma, $wcco );
    $codigosBuscados = array();
    foreach ($datosEmpleado as $codigoTalhuma => $datos ) {
        $wcodigoUsuario = explode("-", $codigoTalhuma );
        $wcodigoUsuario = $wcodigoUsuario[0];
        if( $wcodCoordinador == "" or ( array_key_exists( $codigoTalhuma, $usuario_coordinador['lista_solicitantes'] ) ) ){
            //periodosEmpleado( $wcodigoUsuario, $codigoTalhuma, $datosEmpleado[$codigoTalhuma]  );
            array_push( $codigosBuscados, "'".$codigoTalhuma."'");
        }
    }
    //echo "<pre>".print_r( $datosEmpleado, true )."</pre>";
    if( count($codigosBuscados) > 0 ){
        $codigosBuscados = implode( ",", $codigosBuscados );
        $vacacionesSolicitadas = vacacionesAdisfrutar( $codigosBuscados, $wfechaIniDisfrute, $wfechaFinDisfrute );
        if( count( $vacacionesSolicitadas ) > 0 ){
            $tabla_respuesta = "<table>";
            $tabla_respuesta .= "<tr class='encabezadotabla'><td align='center' colspan='5'> EMPLEADOS CON VACACIONES POR DISFRUTAR EN EL PERIODO $wfechaIniDisfrute HASTA $wfechaFinDisfrute </td></tr>";
            //$tabla_respuesta .= "<tr class='encabezadotabla'><td align='center'> Codigo </td><td align='center'> Nombre </td><td align='center'> PERIODO A DISFRUTAR </td><td align='center'> DIAS AUSENTE </td></tr>";
            $tabla_respuesta .= "<tr class='encabezadotabla'><td align='center'> Codigo </td><td align='center'> Nombre </td><td align='center'> PERIODO A DISFRUTAR </td></tr>";
            foreach ($vacacionesSolicitadas as $codEmpleadoVaciones => $arrayVacacionesSolicitadas) {
                foreach ($arrayVacacionesSolicitadas as $keyVacaciones => $vacacionesSolicitadas) {
                    $tabla_respuesta .= "<tr class='fila1'>";
                        $tabla_respuesta .= "<td align='center'>$codEmpleadoVaciones</td>";
                        $tabla_respuesta .= "<td align='left'>{$datosEmpleado[$codEmpleadoVaciones]['nombre']}</td>";
                        $tabla_respuesta .= "<td align='center'>{$vacacionesSolicitadas['fechaIniDisfrute']} --> {$vacacionesSolicitadas['fechaFinDisfrute']}</td>";
                        //$tabla_respuesta .= "<td align='center'>{$vacacionesSolicitadas['diasAusenteEnPeriodo']}</td>";
                    $tabla_respuesta .= "</tr>";
                }
            }
            $tabla_respuesta .= "</table>";
        }else{
            echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] No hay registro de vacaciones pendientes de disfrutar para el periodo de tiempo consultado.
             </div>";
            return;
        }
    }else{
        echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] No hay registro de vacaciones pendientes de disfrutar para el periodo de tiempo consultado.
             </div>";
        return;
    }
  }
    echo $tabla_respuesta ;

	odbc_close($conexunix);
	odbc_close_all();

  return;
}

if( $peticionAjax == "fechaLimiteConsulta" ){

    $fechaLimiteConsulta = date('Y-m-d', strtotime( $fechaInicioConsulta."+6 month" ) );
    echo $fechaLimiteConsulta;
    return;
}
?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title>  REPORTE VACACIONES PENDIENTES POR LIQUIDAR </title>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
    <style type="text/css">
        #tooltip{
            color: #2A5DB0;
            font-family: Arial,Helvetica,sans-serif;
            position:absolute;
            z-index:3000;
            border:1px solid #2A5DB0;
            background-color:#FFFFFF;
            padding:5px;
            opacity:1;}
        #tooltip div{margin:0; width:450px}
    </style>
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
    <script>
        $(document).ready(function(){
            ccoCoordinador = $("#wccoCoordinador").val();
            if( $.trim(ccoCoordinador) != "" ){
                $("#div_formulario_ppal").hide();
                $("#btn_generar").click();
            }

            $("#fecha_limite_calculo").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true
            });
            autocompletarEnFormularios();
            $("#td_fechaCorte").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
        });

        (function($){
            $.fn.extend({
                detallar: function(){
                    this.each(function(){
                        $(this).click(function(){
                            $(this).next('tr').toggle();
                        })
                    })
                }
            });
        })(jQuery)

        function generarReporte(){

          var wcodigoUsuario       = $("#wcodigo").val();
          var wcodCoordinador      = $("#wcodCoordinador").val();
          var wcco                 = $("#wcco").val();
          var fechaFinal           = $("#fecha_limite_calculo").val();
          var buscarDisfrute       = $("#wversolicitudes").is(":checked");
          var fechaIniDisfrute     = $("#fecha_inicio_disfrute").val();
          var fechaFinDisfrute     = $("#fecha_final_disfrute").val();
          var buscarSoloAdisfrutar = "off";
          if( buscarDisfrute ){
            if( $.trim(fechaFinDisfrute) == "" || $.trim(fechaFinDisfrute) == "" ){
                alert( "Si desea consultar vacaciones a disfrutar, debe seleccionar el rango de fechas" );
                return;
            }
            buscarSoloAdisfrutar = "on";
          }

          if( $.trim(wcodigoUsuario) != "" ){
            tipoBusqueda = "individual";
          }else{
            tipoBusqueda = "masiva";
          }
          $("#div_respuesta").html("");
          $("#msjEspere").toggle();
          $.ajax({
                    url: "rep_vacaciones.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "consultarDatos",
                            consultaAjax: "si",
                           wtipoBusqueda: tipoBusqueda,
                          wcodigoUsuario: wcodigoUsuario,
                         wcodCoordinador: wcodCoordinador,
                               wemp_pmla: $("#wemp_pmla").val(),
                                    wcco: $("#wcco").val(),
                     fechaLimiteBusqueda: fechaFinal,
                        buscarAdisfrutar: buscarSoloAdisfrutar,
                       wfechaIniDisfrute: $("#fecha_inicio_disfrute").val(),
                       wfechaFinDisfrute: $("#fecha_final_disfrute").val()
                          },
                    success: function(data)
                    {
                        $("#div_respuesta").html(data);
                        $("#input_buscador").quicksearch(".tbl_empleados tbody tr[tipo!='titulo']" );
                        $("#msjEspere").toggle();
                        $(".div_contenedor_cco").accordion({
                            collapsible: true,
                            active:0,
                            heightStyle: "content",
                            icons: null
                        });
                        $(".resumen").detallar();

                    }
          });
        }

        function ocultarDetalles( input ){
            $(".tr_detalle").hide();
        }

        function autocompletarEnFormularios(){

            empleados_nombres_array = new Array();
            var datosEmpleados = eval( $("#array_empleados").val() );
            for( i in datosEmpleados ){
                empleados_nombres_array.push( datosEmpleados[i] );
            }

            ccos_nombres_array = new Array();
            var datosCcos = eval( $("#array_ccos").val() );
            for( i in datosCcos ){
                ccos_nombres_array.push( datosCcos[i] );
            }

             $( "#input_cco" ).autocomplete({
                    source    : ccos_nombres_array,
                    minLength : 2,
                    select: function( event, ui ) {
                        var ccoSeleccionado = ui.item.value;
                        if( $.trim(ccoSeleccionado) != "" ){
                            ccoSeleccionado = ccoSeleccionado.split(",");
                            ccoSeleccionado = $.trim( ccoSeleccionado[0] );
                            $(this).parent().find("#wcco").val(ccoSeleccionado);
                        }
                    }
            });

            $( "#input_empleados" ).autocomplete({
                    source    : empleados_nombres_array,
                    minLength : 2,
                    select: function( event, ui ) {
                        var emplSeleccionado = ui.item.value;
                        if( $.trim(emplSeleccionado) != "" ){
                            emplSeleccionado = emplSeleccionado.split(",");
                            emplSeleccionado = $.trim( emplSeleccionado[0] );
                            $(this).parent().find("#wcodigo").val( emplSeleccionado );
                        }
                    }
            });
        }

        function validarVacioParametro( obj, id ){
            if( $.trim( $(obj).val() ) == "" ){
                if( id != "wcodigo" )
                    $(obj).parent().find("#"+id).val("%");
                    else
                        $(obj).parent().find("#"+id).val("");
            }
        }

        function calcularFechaLimiteConsulta( fechaInicioConsulta ){

            var fechafinalcalculada = '';
            $.ajax({
                    url: "rep_vacaciones.php",
                   type: "POST",
                  async: false,
                   data: {
                            peticionAjax: "fechaLimiteConsulta",
                            consultaAjax: "si",
                               wemp_pmla: $("#wemp_pmla").val(),
                     fechaInicioConsulta: fechaInicioConsulta
                          },
                    success: function(data)
                    {
                        fechafinalcalculada =  data;
                    }
            });
            return(fechafinalcalculada);
        }

        function verificarBusquedaAdisfrutar( obj ){
            if( $(obj).is(":checked") ){
                $("#div_formulario_ppal").hide();
                $("#fecha_inicio_disfrute").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                minDate: '+0d'
                , onSelect: function(dateText, inst ) {
                     $("#fecha_final_disfrute").val("");
                     $("#fecha_final_disfrute").datepicker("destroy");
                     var fechalimite = calcularFechaLimiteConsulta( dateText );
                     $("#fecha_final_disfrute").datepicker({
                        showOn: "button",
                        buttonImage: "../../images/medical/root/calendar.gif",
                        dateFormat: 'yy-mm-dd',
                        buttonImageOnly: true,
                        changeMonth: true,
                        changeYear: true,
                        minDate: dateText,
                        maxDate: fechalimite
                    });
                }
            });

            $("#fecha_final_disfrute").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                minDate: '+0d',
                maxDate: '+6m'
            });
            }else{
                $("#fecha_inicio_disfrute").datepicker("destroy");
                $("#fecha_inicio_disfrute").val("");
                $("#fecha_final_disfrute").datepicker("destroy");
                $("#fecha_final_disfrute").val("");
                if( $("#wccoCoordinador").val() == "" )
                    $("#div_formulario_ppal").show();
            }
        }

        function mostrarHistorialVacaciones( codigoEmpleado ){
            location.href = "../procesos/vacaciones.php?wemp_pmla="+$("#wemp_pmla").val()+"&wconsultaExterna=on&wusuarioConsultado="+codigoEmpleado;
        }
    </script>
</head>
<body>
    <?php
        $centrosCostos = array();
        $empleados     = array();
        inicializarArreglos();
        $centrosCostos = json_encode( $centrosCostos );
        $empleados     = json_encode( $empleados );
        $ccoInicial    = "%";
        $titulo        = " REPORTE VACACIONES PENDIENTES POR LIQUIDAR";

        if( !empty($coordinador) ){
            $cod_use_Talhuma = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $coordinador);
            $wccoCoordinador = centroCostoEmpleadoUnico($conex, $wemp_pmla, $wbasedato, $wtalhuma, $cod_use_Talhuma);
            $ccoInicial      = $wccoCoordinador;
            $titulo          = "REPORTE DE VACACIONES PENDIENTES DE APROBAR O DE DISFRUTAR <br> PERSONAL A CARGO.";
        }

        $title = '<div>
                        Esta fecha de corte facilita el cálculo estimado del estado de dias causados por el personal a una fecha futura,
                        sin embargo se debe tener en cuenta los siguientes puntos:<br>
                        1. Para los empleados en riesgo la estimación solo considera el tiempo en exposición al dia de hoy, para cálculos <br>
                              futuros se asume que el empleado no se encontrará en riesgo<br>
                        2. No se tienen en cuenta las vacaciones a disfrutar solicitadas a menos de que estas se encuentren aprobadas.<br>
                  </div>';
    ?>
    <?php encabezado( " {$titulo} ", $wactualiz, "clinica" ); ?>
    <input type='hidden' name='array_ccos' id='array_ccos' value='<?=$centrosCostos?>'>
    <input type='hidden' name='array_empleados' id='array_empleados' value='<?=$empleados?>'>
    <input type='hidden' id='wemp_pmla'       value='<?=$wemp_pmla;?>'>
    <input type='hidden' id='wccoCoordinador' value='<?=$wccoCoordinador;?>'>
    <input type='hidden' id='wcodCoordinador' value='<?=$coordinador;?>'>
    <div id='div_formulario_ppal' align='center'>
        <table>
            <tr class='encabezadotabla'><td colspan='2'> SELECCIONE DATOS DE CONSULTA DEL REPORTE </td></tr>
            <tr class='fila2'>
                <?php

                ?>
                <td><b>Centro Costos:</b></td>
                <td>
                  <input id='input_cco' type='text' value='' size='40'   placeholder="Deje en blanco para ver todos" onkeyup='validarVacioParametro(this, "wcco");'>
                  <input type='hidden' id='wcco' name='wcco' value='<?=$ccoInicial;?>'>
                </td>
              </tr>
              <tr class='fila2'>
                <td><b>Codigo Empleado:</b> </td>
                <td>
                    <input type='text' style='width:300px;' id='input_empleados' value='' placeholder="Deje en blanco para ver todos" onkeyup='validarVacioParametro(this, "wcodigo");'>
                    <input type='hidden'  id='wcodigo' value=''>
                </td>
              </tr>
              <tr class='fila2'>
                <td id='td_fechaCorte' title='<?=$title;?>' style='cursor:pointer;'><b>Fecha de corte:</b></td>
                <td>
                    <input type='text' size='10' id='fecha_limite_calculo' vaue=''>
                </td>
              </tr>
              <!--<tr><td colspan='2' align='center'><input type='button' value='Generar' id='btn_generar' onclick='generarReporte();'></td></tr>!-->
        </table>
    </div>
    <br>
    <?php  if( isset($coordinador) ){ ?>
        <div id='div_consulta_vacacionesDisfrutar' align='center'>
            <table>
                <tr class='encabezadotabla'><td colspan='2'> CONSULTAR VACACIONES PROGRAMADAS </td></tr>
                <tr class='fila2'>
                    <td><b><input type='checkbox' id='wversolicitudes' name='wversolicitudes' onclick='verificarBusquedaAdisfrutar( this );'> Consultar Personal en vacaciones :</b></td>
                    <td>
                        <input  type='text' size='10' class='fecha1' id='fecha_inicio_disfrute' disabled name='fecha_inicio_disfrute' value=''> hasta <input  type='text' size='10' disabled class='fecha2' id='fecha_final_disfrute' name='fecha_final_disfrute' value=''>
                    </td>
                  </tr>
            </table>
        </div>
    <?php } ?>
    <div align='center'>
        <table>
               <tr><td align='center'><input type='button' value='Generar' id='btn_generar' onclick='generarReporte();'></td></tr>
        </table>
    </div>
    <div id='div_respuesta' align='center'>
    </div>
    <center><div id='msjEspere' style='display:none;'>
        <br>
        <img src='../../images/medical/ajax-loader5.gif'/>
        <br><br> Por favor espere un momento ... <br><br>
    </div></center>
    <center><br><br><input type='button' value='Cerrar' id='btn_cerrar' onclick='window.close();'></center>
</body>
</html>
