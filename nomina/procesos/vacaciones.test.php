<?php
include_once("conex.php");
/*=======================================================================================================================================
Programa       : Vacaciones.test.php
Autor          : Juan Carlos Hernández M - Coordinador Desarrollo de Sistemas de Información
Fecha creación : Enero 29 de 2014
=========================================================================================================================================
ACTUALIZACIONES
2019-11-22 Camilo Zapata: - Para presentar las vacaciones disfrutadas se incluye la tabla noper y el campo perfin(fecha Inicio del contrato), para garantizar que
                            solo se incluya el contrato actual, puesto que para los empleados con reingreso el programa estaba teniendo en cuenta ambos periodos laborales.
                          - los periodos y dias pendientes pasan a ser responsabilidad de SQL por lo tanto a través de la función
                            consultarperiodosDisponiblesPendientes(), se consultan y se pintan, así se garantiza que el calculo de vacaciones causadas para el
                            personal de alto riesgo se calcule de manera correcta y se habilitan para que puedan realizar la solicitud de vacaciones por este medio.
2018-08-10 Arleyda Insignares C.: Se Modifica variable 'wfecproM' para que en el rango de fecha Período cumplido se visualicen los dias de licencia no remunerada.
2017-10-03 Arleyda Insignares C.: Se adiciona control para que no se puedan pedir vacaciones antes de cumplir el periodo en curso.
2017-07-13 Arleyda Insignares C.: se adiciona la columna 'pvadetddin' a la vista nopvadet, para obtener el numero de dias cuando las vacaciones son
                          pagadas en dinero.
2017-04-18 Arleyda Insignares C.: Actualizacion ODBC direccionado a SQL Software, Order by en Query no debe utilizar numeros.
2016-02-25 camilo zapata: se corrige el caso en que el ultimo periodo liquidado tenga 366 dias, por se bisiesto, se reajusta para continuar con los cálculos.
2016-01-18 camilo zapata: se corrige la funcion "diasLicenciaNoRemunerada" ya que no estaba acumulando en los dias no contados, cuando había mas de una licencia en el mismo periodo,
                          adicionalmente, se verifica si en el último periodo liquidado hubo licencia, y si fue así, si esta ya movió el dia final del periodo, para así evitar sumar los dias de licencia dos veces.
                          para verificar estos cambios buscar la fecha "2016-01-18"
2015-11-25 camilo zapata: Modificación del software para restringir el acceso al programa a aquellos empleados que tienen comportamientos especiales en los saldos guardados en nómina( generalmente usuarios de riesgo 5),
                          tambien se adiciona código para que los dias que quedaron pendientes por liquidar de un periodo anterior a la liquidación de uno posterior, se acumulen en el primer periodo sin liquidar.
2015-11-10 Camilo zapata: se deshabilita la actualización de la cantidad de dias solicitados por parte del coordinador, y se corrige el calculo de los dias mínimos y máximos para el periodo en curso, bajo la premisa de que los dias máximos siempre seran,
                          los causados, menos los disfrutados, y los mínimos serán 8 o los que le queden disponibles despues de restar los disfrutados( buscar $WMAXIMO_DIAS Y $WMINIMO_DIAS)
2015-09-30 Camilo zapata: de no habilitar nunca la autorización automática, ni cuando sea el gerente quien solicite las vacaciones.
2015-09-29 Camilo zapata: se corrigió el armado del arreglo con las solicitudes hechas, para que no incluya aquellas que ya están disfrutadas ( aprobadas por coordinador y nómina, ademas de fecha de regreso de disfrute inferior al dia actual)
2015-09-22 Camilo zapata: se corrigió calculo de dias disfrutados por periodo traido de unix, calculandole a los liquidados tambien sus licencias.
2015-09-17 Camilo zapata: se corrigió calculo de dias disfrutados por periodo, buscar la fecha
2015-09-08 Camilo zapata: se corrigió la función crearIndiceUnico para que no incluya la cantidad de dias dias disponibles, ya que esto generaba
                          probleas con periodos en curso, ya que si se había solicitado las vacaciones con mucha anterioridad son diferentes los dias
                          disponibles hoy a los de ese momento. por esta razón no se pintaba en la sección de vacaciones por disfrutar.
2015-09-10 Camilo zapata: se corrigió el segmento de codigo encargado de calcular los dias pendientes de disfrutar del último periodo liquidado, puesto
                          que este calculo debía realizarse a partir de los dias pendientes de disfrutar desde liquidaciones anteriores, teniendo en cuenta
                          que un periodo podía cambiar su fecha final en el caso de que el empleado hubiese disfrutado de algún tipo de licencia no remunerada.
                          por lo tanto si habia un periodo disfrutado parcialmente y despues un disfrute de licencia, el software asumía que el periodo posterior
                          se trataba de uno completamente independiente, asignandole 15 dias causados( no contaba el disfrute parcial antes de la licencia)

=========================================================================================================================================
*/
                                                       // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
$wactualiz="2019-11-22";                               // Aca se coloca la última fecha de actualización de este programa //
                                                       // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

$wfecha = date("Y-m-d");
$whora  = (string)date("H:i:s");
if( !isset( $fechaMesAtras ) or $fechaMesAtras == "" )
    $fechaMesAtras = date( "Y-m-d", strtotime( $wfecha."-6 month" ) );

if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}

$arr_estados_respuesta = array("APROBADO"=>"Aprobado", "RECHAZADO"=>"Rechazado");
global $wfecha, $whora, $arr_estados_respuesta;

$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];
if( $wconsultaExterna == "on" ){
    $user_session = $wusuarioConsultado;
}






define( "diasRiesgo", 30 );
define( "diasSinRiesgo", 15 );

//=======================================================================================================//
// ***** F U N C I O N E S *****
//=======================================================================================================//
function diasEntreFechas($fechainicio, $fechafin)
{

    return (((strtotime($fechafin)-strtotime($fechainicio))/86400)+1);
}

function fila_periodo_disponible($wclass, $fecha_inicio, $fecha_fin, $dias_disponibles, $consecutivo, $codigo_empleado, $wcentro_costo_empleado, $arr_solicitudesEnviadas, $div_acordeon, $es_un_coordinador, $WMAXIMO_DIAS, $WMINIMO_DIAS, $diasLicencia, $detalleDias, $diasDisponiblesReales, $wfechaFinRealPeriodo='', $diasDisfrutadosHoy = 0)
{

    global $arr_estados_respuesta, $wfecha;
    $html_tr = "";

    $idx_diferenciador = crearIndiceUnico($codigo_empleado, $fecha_inicio, $wfechaFinRealPeriodo, $dias_disponibles);//$idx_diferenciador = crearIndiceUnico($codigo_empleado, $fecha_inicio, $fecha_fin, $dias_disponibles);
    //echo '$idx_diferenciador '.$idx_diferenciador;
    $idx_diferenciador_html = $idx_diferenciador;//'__'.$div_acordeon;
    $arr_periodo = crearArrayDatosSolicitud($codigo_empleado, $fecha_inicio, $wfechaFinRealPeriodo, $dias_disponibles
                                            , "", "", "", ""
                                            , "", $wcentro_costo_empleado, "");/*$arr_periodo = crearArrayDatosSolicitud($codigo_empleado, $fecha_inicio, $fecha_fin, $dias_disponibles
                                            , "", "", "", ""
                                            , "", $wcentro_costo_empleado, "");*/

    $inactivar_solicitado   = "";
    $wdias_disfrutar        = $dias_disponibles;
    $fecha_solicitud_inicio = "";
    $fecha_solicitud_fin    = "";
    $class_solicitado       = "activo_no_solicitado";
    $btn_solicitar          = '';
    $btn_actualizar         = 'display:none;';
    $id_solicitud           = "&nbsp;";
    $claseDisfrutada        = "";
    $esDirectivo            = consultarEsDirectivo( $codigo_empleado );


    // Si solo se va a mostrar la información de solicitudes personales
    if(array_key_exists($idx_diferenciador, $arr_solicitudesEnviadas))
    {
        $arr_periodo              = $arr_solicitudesEnviadas[$idx_diferenciador];
        $btn_solicitar            = 'display:none;';
        $inactivar_solicitado     = 'disabled="disabled"';
        $wdias_disfrutar          = $arr_solicitudesEnviadas[$idx_diferenciador]['dias_solicitados'];
        $id_solicitud_sol         = $arr_solicitudesEnviadas[$idx_diferenciador]['id_solicitud'];
        $fecha_Creacion_Solicitud = $arr_solicitudesEnviadas[$idx_diferenciador]['fecha_Creacion_Solicitud'];
        $fecha_solicitud_inicio   = $arr_solicitudesEnviadas[$idx_diferenciador]['fecha_inicio_solicitud'];
        $fecha_solicitud_fin      = $arr_solicitudesEnviadas[$idx_diferenciador]['fecha_fin_solicitud'];
        $estadoSolicitud          = $arr_solicitudesEnviadas[$idx_diferenciador]['estadoSolicitud'];

        //if($arr_solicitudesEnviadas[$idx_diferenciador]['respuesta_coordinador'] == '' && $arr_solicitudesEnviadas[$idx_diferenciador]['respuesta_nomina'] == '')
        if( ( $arr_solicitudesEnviadas[$idx_diferenciador]['respuesta_coordinador'] == 'RECHAZADO' ||   $arr_solicitudesEnviadas[$idx_diferenciador]['respuesta_nomina'] == 'RECHAZADO') OR ( $arr_solicitudesEnviadas[$idx_diferenciador]['respuesta_coordinador'] == '' && $arr_solicitudesEnviadas[$idx_diferenciador]['respuesta_nomina'] == '') OR ( $arr_solicitudesEnviadas[$idx_diferenciador]['respuesta_coordinador'] == 'APROBADO' && $esDirectivo && $arr_solicitudesEnviadas[$idx_diferenciador]['respuesta_nomina'] == '' ) )
        {
            $btn_actualizar       = "";
            $inactivar_solicitado = "";
            $class_solicitado = "tr_solicitado_sin_respuesta";
        }
        else
        {
            $class_solicitado = "tr_solicitado_con_respuesta";
        }
    }
    //
    $pendiente = ( empty( $id_solicitud_sol ) ) ? "--" : "Pendiente" ;
    $respuesta_coordinador = (array_key_exists($arr_periodo['respuesta_coordinador'], $arr_estados_respuesta)) ? $arr_estados_respuesta[$arr_periodo['respuesta_coordinador']]:  $pendiente;
    $respuesta_nomina = (array_key_exists($arr_periodo['respuesta_nomina'], $arr_estados_respuesta)) ? $arr_estados_respuesta[$arr_periodo['respuesta_nomina']]: $pendiente;
    $sinRespuestaNomina = ( $respuesta_nomina == "Pendiente" ) ? "on" : "off";
    if($estadoSolicitud == "vencida"){
        //if( $arr_periodo['respuesta_nomina'] == "" or $arr_periodo['respuesta_nomina'] == "RECHAZADO" ){
        if( $arr_periodo['respuesta_nomina'] == "" ){
                $claseSolicitud  = ' fondorojo ';
                $deshabGuardar   = 'disabled';
                $respuesta_nomina = " Vencida ";
                $solicitud_vencida = "on";
                if( $respuesta_coordinador == "Pendiente"){
                    $sinRespuestaCoord = "on";
                    $respuesta_coordinador = "Vencida";
                    $claseSolicitudCoordi  = ' fondorojo ';
                }
        }else{
            if( $arr_periodo['respuesta_nomina'] == "APROBADO" )
                $claseDisfrutada = " periodoDisfrutado ";
        }
    }else{
        $solicitud_vencida = "off";
    }
    $habilitarSiguiente = "off";
    if( $wdias_disfrutar == $diasDisponiblesReales and trim($id_solicitud_sol) != "" AND $respuesta_coordinador != "Rechazado" ){
        $habilitarSiguiente = "on";
    }else{
        $habilitarSiguiente = "off";
    }
    if( empty( $id_solicitud_sol ) ){
        $id_solicitud_sol = "";
    }

    //echo "<br> edb -> maximos: $WMAXIMO_DIAS - disfrutados: $diasDisfrutadosHoy -> disponibles: $dias_disponibles";
    $WMAXIMO_DIAS = $WMAXIMO_DIAS - $diasDisfrutadosHoy;

    //-> control de maximo y minimo de dias solicitados
    if($dias_disponibles <= $WMAXIMO_DIAS)
    {
        if( trim($id_solicitud_sol) != ""){//--> si tiene solicitud pendiente de aprobar, entonces modificamos por el valor de dias solicitadas registrado.
            if( ( strtotime( date('Y-m-d') ) - strtotime( $wfechaFinRealPeriodo ) )  < 0 ){
                $diasDisponiblesAux =  buscarFechaRegreso( $codigo_empleado, $fecha_inicio, $fecha_solicitud_inicio, $fecha_fin, "fecha" ,$wdias_disfrutar );
                $diasDisponiblesAux = $diasDisponiblesAux['diasDisfrutar'];
                $WMAXIMO_DIAS = $diasDisponiblesAux - $diasDisfrutadosHoy;
            }else{
                 $WMAXIMO_DIAS = $dias_disponibles;
            }
        }else{
            $WMAXIMO_DIAS = $dias_disponibles;//--> sino tiene solicitud se pone los dias disponibles.
        }
        if($dias_disponibles > 0 && $dias_disponibles < $WMINIMO_DIAS )
        {
            if( ( strtotime( date('Y-m-d') ) - strtotime( $wfechaFinRealPeriodo ) )  < 0 ){
                //--> esto quiere decir que es el periodo en curso, por lo tanto debe tener el mínimo de dias establecido para poder ser solicitado
                if( ( $dias_disponibles + $diasDisfrutadosHoy ) >= 8  ){
                    $WMINIMO_DIAS = $dias_disponibles;
                }
            }else{
                $WMINIMO_DIAS = $dias_disponibles;
            }
        }
    }


    $periodoEnCurso =  ( ( strtotime( date('Y-m-d') ) - strtotime( $wfechaFinRealPeriodo ) )  < 0 ) ? "on" : "off";

    $html_tr .= '
            <tr align="center" class="'.$wclass.' '.$class_solicitado.' tr_solicitudes_personales solicitudesPendietes '.$claseDisfrutada.'" id="tr_'.$idx_diferenciador_html.'" fechaInicioPendiente="'.$fecha_inicio.'" fechaFinalPendiente="'.$fecha_fin.'" disabled="disabled" vencida="'.$solicitud_vencida.'">
                <td align="center" id="fecha_inicial_periodo_'.$idx_diferenciador_html.'">'.substr($fecha_inicio,0,10).'</td>
                <td align="center"><---></td>
                <td align="center" id="fecha_final_periodo_'.$idx_diferenciador_html.'">'.substr($wfechaFinRealPeriodo,0,10).'</td>
                <td align="center" class="diasDisponibles" title="'.$detalleDias.'">'.$diasDisponiblesReales.'</td>
                <td align="center" >
                    <input type="text" id="fecha_solicitud_inicio_'.$idx_diferenciador_html.'" name="fecha_solicitud_inicio_'.$idx_diferenciador_html.'" value="'.$fecha_solicitud_inicio.'" fechaAprobadaActual="'.$fecha_solicitud_inicio.'" class="campo_fecha_min"  fechaInicioPendiente="'.$fecha_inicio.'" fechaFinalPendiente="'.$fecha_fin.'" size="10" disabled="disabled"  >
                    <input type="hidden" id="dias_disfrutados_hoy_'.$idx_diferenciador_html.'" name="dias_disfrutados_hoy_'.$idx_diferenciador_html.'" value="'.$diasDisfrutadosHoy.'">
                </td>
                <td align="center"><input type="number" class="input_blq" porAprobar="off" name="wdias_disfrutar_'.$idx_diferenciador_html.'" id="wdias_disfrutar_'.$idx_diferenciador_html.'" value="'.$wdias_disfrutar.'" size="3" onkeypress="return soloNumeros(event, this);" onblur="validarValor(this);" periodoEnCurso="'.$periodoEnCurso.'" min="'.$WMINIMO_DIAS.'" max="'.$WMAXIMO_DIAS.'" step="1" '.$inactivar_solicitado.' class="input_blq" style="width: 40px;"  onchange="calcularPeriodoFechas( this )"></td>

                <td align="center" >
                    <input type="text" id="fecha_solicitud_fin_'.$idx_diferenciador_html.'" name="fecha_solicitud_fin_'.$idx_diferenciador_html.'" value="'.$fecha_solicitud_fin.'" class="campo_fecha_max" size="10" disabled="disabled" >
                </td>
                <td align="center" name="td_respuesta_coordinador" sinRespuesta="'.$sinRespuestaCoord.'" class="'.$claseSolicitudCoordi.'">'.$respuesta_coordinador.'</td>
                <td align="center" name="td_respuesta_nomina"  sinRespuesta="'.$sinRespuestaNomina.'" class="'.$claseSolicitud.'">'.$respuesta_nomina.'</td>

                <td align="center" nowrap="nowrap">
                    <input type="hidden" value=\''.base64_encode(serialize($arr_periodo)).'\' id="arr_solicitudEnviada_'.$idx_diferenciador_html.'" name="arr_solicitudEnviada_'.$idx_diferenciador_html.'" >
                    <input style="'.$btn_solicitar.'" id="btn_solicitar_'.$idx_diferenciador_html.'" class="input_blq" type="button" value="Solicitar" onclick="solicitarPeriodo(\''.$dias_disponibles.'\',\''.$idx_diferenciador_html.'\', \''.$codigo_empleado.'\', \'solicitar\',\''.$div_acordeon.'\',\''.$es_un_coordinador.'\', \'\', \'\');" >
                    <input style="'.$btn_actualizar.'" id="btn_actualizar_'.$idx_diferenciador_html.'" class="input_blq" type="button" value="Aplicar cambios" onclick="solicitarPeriodo(\''.$dias_disponibles.'\',\''.$idx_diferenciador_html.'\', \''.$codigo_empleado.'\', \'actualizar\',\''.$div_acordeon.'\',\''.$es_un_coordinador.'\', \''.$id_solicitud_sol.'\', \'\');" >
                    <input style="'.$btn_actualizar.'" id="btn_cancelar_'.$idx_diferenciador_html.'" class="input_blq" type="button" value="Cancelar" onclick="cancelarPeriodo(\''.$idx_diferenciador_html.'\', \''.$codigo_empleado.'\',\''.$div_acordeon.'\',\''.$es_un_coordinador.'\');" >
                </td>
                <td align="center" id="id_solicitud_'.$idx_diferenciador_html.'">'.$id_solicitud_sol.'</td>
                <td align="center" id="id_solicitud_'.$idx_diferenciador_html.'">'.$fecha_Creacion_Solicitud.'</td>
                <input type="hidden" id="wdias_disponibles_'.$idx_diferenciador_html.'" value= "'.$diasDisponiblesReales.'" >
                <input type="hidden" id="habilitarSiguiente_'.$idx_diferenciador_html.'" value= "'.$habilitarSiguiente.'" >
            </tr>';
    return $html_tr;
}

function fila_vacaciones_por_aprobar($wclass, $fecha_inicio, $fecha_fin, $dias_disponibles, $consecutivo, $codigo_empleado, $arr_solicitudesEnviadas, $div_acordeon, $arr_empleado, $es_un_coordinador, $WMAXIMO_DIAS, $WMINIMO_DIAS, $idSolicitud, $estado_solicitud, $fechaCreacionSolitud)
{
    global $arr_estados_respuesta;
    global $usuSolicitudesVencidas;
    global $wbasedato;
    global $conex;
    $html_tr = "";
    $idx_diferenciador = crearIndiceUnico($codigo_empleado, $fecha_inicio, $fecha_fin, $dias_disponibles).'__'.$arr_solicitudesEnviadas['id_solicitud'];
    $idx_diferenciador_html = $idx_diferenciador;//.'__'.$div_acordeon;
    $arr_periodo = $arr_solicitudesEnviadas;
    // $arr_periodo = array(   "wusuario_solicitud"     => "",
    //                         "fecha_inicio_pendiente" => $fecha_inicio,
    //                         "fecha_fin_pendiente"    => $fecha_fin,
    //                         "dias_disponibles"       => $dias_disponibles,
    //                         "diferenciador"          => $idx_diferenciador,
    //                         "respuesta_coordinador"  => "",
    //                         "respuesta_nomina"       => "",
    //                         "dias_solicitados"       => "",
    //                         "fecha_inicio_solicitud" => "",
    //                         "fecha_fin_solicitud"    => "",
    //                         "id_solicitud"           => "");

    $inactivar_solicitado         = "";
    $wdias_disfrutar              = $dias_disponibles;
    $fecha_solicitud_inicio       = "";
    $fecha_solicitud_fin          = "";
    $class_solicitado             = "solicitado_aprobado_coordinador";
    $btn_solicitar                = '';
    $btn_actualizar               = '';
    $bloqueadaPorVencimientoAjeno = 'off';
    $vencidaHtml                  = 'off';

    /*consultar el minimo de dias para este empleado ---> 2015-09-30 */
    $codigoTalhuma = explode("-", $codigo_empleado);
    $codigoTalhuma =  $codigoTalhuma[0];
    $query = " SELECT Dimnud, Dimfel, 1 as tipo
                 FROM {$wbasedato}_000016
                WHERE Dimusu = '{$codigoTalhuma}'
                  AND Dimfel <= '".date('Y-m-d')."'
                  AND Dimest = 'on'
                UNION
               SELECT Dimnud, Dimfel, 2 as tipo
                 FROM {$wbasedato}_000016
                WHERE Dimusu = '{$codigoTalhuma}'
                  AND Dimfel = '0000-00-00'
                  AND Dimest = 'on'
                ORDER BY tipo desc, Dimfel desc
                LIMIT 1";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_assoc( $rs );
    if( $row['Dimnud']*1 > 0 ){
        $WMINIMO_DIAS = $row['Dimnud'];
    }

    $WMINIMO_DIAS = $arr_solicitudesEnviadas["dias_solicitados"]*1;
    $WMAXIMO_DIAS = $arr_solicitudesEnviadas["dias_solicitados"]*1;


    if( empty($fecha_finAux ) )
        $fecha_finAux = $fecha_fin;

    if($arr_solicitudesEnviadas['respuesta_nomina'] != '')
    {
        $class_solicitado = "solicitado_respuesta_nomina";
    }

    $nombres = trim($arr_empleado['nombre1'].' '.$arr_empleado['nombre2'].' '.$arr_empleado['apellido1'].' '.$arr_empleado['apellido2']);

    $options_estados = '<option value="">Seleccione..</option>';
    foreach ($arr_estados_respuesta as $key => $value)
    {
        $seleccionado = ($key == $arr_solicitudesEnviadas['respuesta_coordinador']) ? 'selected="selected"': '';
        $options_estados .= '<option value="'.$key.'" '.$seleccionado.' >'.$value.'</option>';
    }

    $estadoSinRevisar     = ( $arr_periodo['respuesta_coordinador'] == "" ) ? "on" : "off";
    $estadoNomiSolicR     = ( $arr_periodo['respuesta_nomina'] == "RECHAZADO") ? "on" : "off";
    $estadoNomiSolicA     = ( $arr_periodo['respuesta_nomina'] == "APROBADO") ? "on" : "off";
    $estadoCordiSoliR     = ( $arr_periodo['respuesta_coordinador'] == "RECHAZADO") ? "on" : "off";
    $estadoCordiSoliA     = ( $arr_periodo['respuesta_coordinador'] == "APROBADO") ? "on" : "off";
    $vencida              = ( $arr_periodo['respuesta_coordinador'] == "APROBADO") ? "on" : "off";
    $fechaCreacionSolitud = ( $fechaCreacionSolitud != "") ? $fechaCreacionSolitud : "&nbsp";
    $classMensajeBloqueo  = '';
    $titleModificarFechas = '';

    if($estado_solicitud == "vencida"){
        $arr_periodo['respuesta_nomina'] = ' Vencida ';
        $claseSolicitud                  = ' class="fondorojo mensajeModificacion"';
        $solicitudVencida                = "on";
        $deshabilitarAprCoor             = ' disabled="disabled"';
        array_push( $usuSolicitudesVencidas, $arr_periodo['wusuario_solicitud'] );
        $vencidaHtml = 'on';
        $classMensajeBloqueo = ' coord_msg_bloqueo ';
        $titleModificarFechas = "<div class='fila2'><span class='subtituloPagina2'><font size='2'>Debe modificar la fecha de inicio de disfrute</font></span><br></div>";
    }else{
        $solicitudVencida    = "off";
        $deshabilitarAprCoor = '"';
        if( in_array( $arr_periodo['wusuario_solicitud'], $usuSolicitudesVencidas) ){
            $deshabilitarAprCoor             = ' disabled="disabled"';
            $claseSolicitud                  = ' class="fondorojo"';
            $bloqueadaPorVencimientoAjeno = "on";
            $classMensajeBloqueo = ' coord_msg_bloqueo ';
        }
    }
    $periodoEnCurso = ( ( strtotime( date('Y-m-d') ) - strtotime( $fecha_fin ) )  < 0 ) ? "on" : "off";

    //onmouseover="notificarBloqueo(this);" onmouseover="notificarBloqueoEstadoJefe(this);  "

    if ($fecha_inicio !== '0000-00-00' && $fecha_fin !== '0000-00-00' ){
        $html_tr .= '
                <tr align="center" class="'.$wclass.' '.$class_solicitado.' tr_solicitudes_aprobar" vencida="'.$vencidaHtml.'" bloqueadaPorVencimientoAjeno="'.$bloqueadaPorVencimientoAjeno.'" usuarioSolicitante="'.$arr_periodo['wusuario_solicitud'].'" id="tr_'.$idx_diferenciador_html.'" rechazadaNomina="'.$estadoNomiSolicR.'" aprobadaNomina="'.$estadoNomiSolicA.'" rechazadaCoordinador="'.$estadoCordiSoliR.'" aprobadaCoordinador="'.$estadoCordiSoliA.'" vencida="'.$solicitudVencida.'" sinRevisar="'.$estadoSinRevisar.'">
                    <td align="left">'.utf8_encode($nombres).'</td>
                    <td align="center" id="fecha_inicial_periodo_'.$idx_diferenciador_html.'">'.$fecha_inicio.'</td>
                    <td align="center"><---></td>
                    <td align="center" id="fecha_final_periodo_'.$idx_diferenciador_html.'">'.$fecha_fin.'</td>
                    <td align="center">'.$dias_disponibles.'</td>
                    <td align="center" >
                        <input type="text" id="fecha_solicitud_inicio_'.$idx_diferenciador_html.'" porAprobar="on" disabled="disabled" name="fecha_solicitud_inicio_'.$idx_diferenciador_html.'" value="'.$arr_solicitudesEnviadas["fecha_inicio_solicitud"].'" class="campo_fecha_min" porAprobar="on" fechaAprobadaActual="'.$arr_solicitudesEnviadas["fecha_inicio_solicitud"].'" size="10" disabled="disabled"  onchange="habilitarActualizar(this);" fechaInicioPendiente="'.$fecha_inicio.'" fechaFinalPendiente="'.$fecha_finAux.'" >
                    </td>
                     <td align="center"  notificado="off" ><input type="number" porAprobar="on" name="wdias_disfrutar_'.$idx_diferenciador_html.'" id="wdias_disfrutar_'.$idx_diferenciador_html.'" value="'.$arr_solicitudesEnviadas["dias_solicitados"].'" onkeypress="return soloNumeros(event, this);" onblur="validarValor(this);" periodoEnCurso="'.$periodoEnCurso.'" min="'.$WMINIMO_DIAS.'" max="'.$WMAXIMO_DIAS.'" step="1" '.$inactivar_solicitado.' class="input_blq" style="width: 40px;" onchange="calcularPeriodoFechas(this); habilitarActualizar(this);" id_solicitud="'.$idSolicitud.'"></td>
                    <td align="center" >
                        <input type="text" id="fecha_solicitud_fin_'.$idx_diferenciador_html.'" name="fecha_solicitud_fin_'.$idx_diferenciador_html.'" value="'.$arr_solicitudesEnviadas["fecha_fin_solicitud"].'" class="campo_fecha_max" size="10" disabled="disabled" >
                    </td>
                    <td align="center" style="font-size:10pt;"  notificado="off">
                        <select id="estado_jefe_'.$idx_diferenciador_html.'" class="input_blq '.$classMensajeBloqueo.' " '.$deshabilitarAprCoor.' valorActual="'.$arr_solicitudesEnviadas['respuesta_coordinador'].'" onchange="habilitarActualizar(this);" id_solicitud="'.$idSolicitud.'" >
                            '.$options_estados.'
                        </select>
                    </td>
                    <td align="center" nowrap="nowrap">
                        <input type="hidden" value="'.base64_encode(serialize($arr_periodo)).'" id="arr_solicitudEnviada_'.$idx_diferenciador_html.'" name="arr_solicitudEnviada_'.$idx_diferenciador_html.'" >
                        <input style="'.$btn_actualizar.'" id="btn_actualizar_'.$idx_diferenciador_html.'" disabled class="input_blq" type="button" value="Aplicar Cambios" onclick="solicitarPeriodo(\''.$dias_disponibles.'\',\''.$idx_diferenciador_html.'\', \''.$codigo_empleado.'\', \'actualizar\',\''.$div_acordeon.'\',\''.$es_un_coordinador.'\', '.$idSolicitud.', \'porAprobar\');" >
                    </td>
                    <td align="center" '.$claseSolicitud.' name="respuesta_coordinador" title="'.$titleModificarFechas.'">'.$arr_periodo['respuesta_nomina'].'</td>
                    <td align="center">'.$idSolicitud.'</td>
                    <td align="center">'.$fechaCreacionSolitud.'</td>
                </tr>';
    }

    return $html_tr;
}

function consultarEsDirectivo( $codigoUsuario ){

    global $conex, $wemp_pmla, $wbasedato;
    $codigoConsultado = explode( "-", $codigoUsuario );
    $query = " SELECT Dimdir
                 FROM {$wbasedato}_000016
                WHERE Dimusu = '{$codigoConsultado[0]}'
                  AND ( Dimfel >= '".date('Y-m-d')."' OR Dimfel = '0000-00-00' )
                  AND Dimest = 'on'
                UNION
                SELECT Dimdir
                 FROM {$wbasedato}_000016
                WHERE Dimusu = '".$codigoConsultado[1].$codigoConsultado[0]."'
                  AND ( Dimfel >= '".date('Y-m-d')."' OR Dimfel = '0000-00-00' )
                  AND Dimest = 'on'
                LIMIT 1";
    $rs    = mysql_query( $query, $conex );
    $rowrs = mysql_fetch_assoc( $rs );
    if( $rowrs['Dimdir'] == "on" )
        return( true );
        else
            return( false );
}

function solicitarVacaciones($conex, $wemp_pmla, $wbasedato, $data, $arr_parametros, $user_session, $accion)
{
    global $wfecha, $whora;

    //$wusuario = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);

    $fecha_inicio_pendiente = $arr_parametros['arr_solicitudEnviada']['fecha_inicio_pendiente'];
    $fecha_fin_pendiente    = $arr_parametros['arr_solicitudEnviada']['fecha_fin_pendiente'];
    $dias_disponibles       = $arr_parametros['arr_solicitudEnviada']['dias_disponibles'];
    $dias_solicitados       = $arr_parametros['dias_solicitados'];
    $fecha_inicio_solicitud = $arr_parametros['fecha_inicio_solicitud'];
    $fecha_fin_solicitud    = $arr_parametros['fecha_fin_solicitud'];
    $wcentro_costo_empleado = $arr_parametros['arr_solicitudEnviada']['wcentro_costo_empleado'];
    $esDirectivo            = consultarEsDirectivo($user_session);
    $insertarAprobacionAuto = ( $esDirectivo ) ? " Dvaerc, Dvafrc, Dvahrc, Dvacdc, " : "";
    $valuesAprobacionAuto   = ( $esDirectivo ) ? " 'APROBADO', '".date('Y-m-d')."', '".date('H:i:s')."', '{$user_session}', ": "";


    $sql = "INSERT INTO {$wbasedato}_000012
                (Medico, Fecha_data, Hora_data,
                Dvause, Dvapfi, Dvapff, Dvadpe,
                Dvadso, Dvafid, Dvaffd, Dvacco, {$insertarAprobacionAuto} Seguridad)
            VALUES
                ('{$wbasedato}', '{$wfecha}', '{$whora}',
                '{$user_session}', '{$fecha_inicio_pendiente}', '{$fecha_fin_pendiente}', '{$dias_disponibles}',
                '{$dias_solicitados}', '{$fecha_inicio_solicitud}', '{$fecha_fin_solicitud}', '{$wcentro_costo_empleado}', {$valuesAprobacionAuto} 'C-{$user_session}')";
    if($result = mysql_query($sql, $conex))
    {
        $id_solicitud = mysql_insert_id();
        $arr_solicitudEnviada =
                    crearArrayDatosSolicitud($user_session, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles
                                            , "", "", $dias_solicitados, $fecha_inicio_solicitud
                                            , $fecha_fin_solicitud, $wcentro_costo_empleado, $id_solicitud);

        $data["arr_solicitudEnviada"] = base64_encode(serialize($arr_solicitudEnviada));
        $data["mensaje"]              = utf8_encode("Solicitud de vacaciones REGISTRADA exitosamente.");
        $data["id_solicitud"]         = utf8_encode($id_solicitud);
        prepararLog($user_session, $arr_parametros, $accion, $id_solicitud);
    }
    else
    {
        $data["error"]        = 1;
        $data["mensaje"]      = utf8_encode("Su solicitud de vacaciones no pudo ser creada");
    }
    return $data;
}

function actualizarVacaciones($conex, $wemp_pmla, $wbasedato, $data, $arr_parametros, $user_session, $accion)
{
    global $wfecha, $whora, $fechaInicialPeriodo, $fechaFinalPeriodo;
    $dias_solicitados       = $arr_parametros['dias_solicitados'];
    $fecha_inicio_solicitud = $arr_parametros['fecha_inicio_solicitud'];
    $fecha_fin_solicitud    = $arr_parametros['fecha_fin_solicitud'];
    $es_un_coordinador      = $arr_parametros['es_un_coordinador'];
    $wcentro_costo_empleado = $arr_parametros["arr_solicitudEnviada"]['wcentro_costo_empleado'];
    $id_solicitud           = $arr_parametros["arr_solicitudEnviada"]["id_solicitud"];

    if(!permitirActualizarAlSolicitante($conex, $wemp_pmla, $wbasedato, $id_solicitud, $es_un_coordinador))
    {
        $data["error"]   = 1;
        $data["mensaje"] = utf8_encode("Su solicitud de vacaciones no pudo ser actualizada\n\nEl Coordinador o Nómina ya respondió su solicitud");
    }
    else
    {
        $rangoFechasOk = validarRangoFechas( $conex, $wemp_pmla, $wbasedato, $arr_parametros, $user_session, $id_solicitud );
        if( $rangoFechasOk ){

            $sql = "SELECT Dvause
                      FROM {$wbasedato}_000012
                     WHERE id = '$id_solicitud'";
            $rs  = mysql_query( $sql, $conex );
            $row = mysql_fetch_assoc( $rs );
            $usuario_solicitante = $row['Dvause'];

            $sql = "SELECT count(*) cantidad
                      FROM {$wbasedato}_000012
                     WHERE Dvause = '{$usuario_solicitante}'
                      AND  Dvapfi < '{$fechaInicialPeriodo}'
                      AND  Dvapff < '{$fechaFinalPeriodo}'
                      AND  Dvaern = ''
                      AND  Dvaest = 'on'
                      AND  Dvaerc = 'RECHAZADO'";
            $rs  = mysql_query( $sql, $conex );
            $row = mysql_fetch_assoc( $rs );
            $cant = $row['cantidad'];
            if( $cant > 0 ){
                $data["error"]        = 1;
                $data["mensaje"]      = utf8_encode("hay solicitudes rechazadas de periodos anteriores, pendientes");
                return $data;
            }

            $sql = " SELECT COUNT(*) cantidad
                       FROM {$wbasedato}_000012
                      WHERE id = '{$id_solicitud}'
                        AND Dvadso =  '{$dias_solicitados}'
                        AND Dvafid = '{$fecha_inicio_solicitud}'
                        AND Dvaffd = '{$fecha_fin_solicitud}'";
            $result = mysql_query($sql, $conex);
            $cant   = mysql_fetch_assoc( $result );
            if( $cant['cantidad']*1 > 0 ){
                $data["error"]   = 1;
                $data["mensaje"] = utf8_encode("No ha realizado ningún cambio en la solicitud");
                return $data;
            }

            $sql = "UPDATE {$wbasedato}_000012
                        SET   Dvadso = '{$dias_solicitados}'
                            , Dvafid = '{$fecha_inicio_solicitud}'
                            , Dvaffd = '{$fecha_fin_solicitud}'
                            , Dvaerc = ''
                            , Dvaern = ''
                    WHERE   id = '{$id_solicitud}'";
            if($result = mysql_query($sql, $conex))
            {

                $arr_solicitudEnviada = $arr_parametros["arr_solicitudEnviada"];
                $arr_solicitudEnviada["dias_solicitados"]       = $dias_solicitados;
                $arr_solicitudEnviada["fecha_inicio_solicitud"] = $fecha_inicio_solicitud;
                $arr_solicitudEnviada["fecha_fin_solicitud"]    = $fecha_fin_solicitud;
                $data["arr_solicitudEnviada"] = base64_encode(serialize($arr_solicitudEnviada));
                $data["mensaje"] = utf8_encode("Solicitud de vacaciones ACTUALIZADA exitosamente.");
                prepararLog($user_session, $arr_parametros, $accion, $id_solicitud);
            }
            else
            {
                $data["error"]   = 1;
                $data["mensaje"] = utf8_encode("Su solicitud de vacaciones no pudo ser actualizada");
            }
        }else{

            $data["error"]        = 1;
            $data["mensaje"]      = utf8_encode("Periodo a disfrutar, inválido 1");

        }
    }
    return $data;
}

function cancelarVacaciones($conex, $wemp_pmla, $wbasedato, $data, $arr_parametros, $user_session, $accion, $es_un_coordinador)
{
    $id_solicitud           = $arr_parametros["arr_solicitudEnviada"]["id_solicitud"];
    if(!permitirActualizarAlSolicitante($conex, $wemp_pmla, $wbasedato, $id_solicitud, $es_un_coordinador))
    {
        $data["error"]   = 1;
        $data["mensaje"] = utf8_encode("Su solicitud de vacaciones no pudo ser actualizada\n\nEl coordinador ya respondió su solicitud");
    }
    else
    {
        $sql = "UPDATE {$wbasedato}_000012
                    SET   Dvaest = 'off'
                WHERE   id = '{$id_solicitud}'";
        if($result = mysql_query($sql, $conex))
        {
            $arr_solicitudEnviada = $arr_parametros["arr_solicitudEnviada"];
            $arr_solicitudEnviada["wusuario_solicitud"]     = "";
            $arr_solicitudEnviada["respuesta_coordinador"]  = "";
            $arr_solicitudEnviada["respuesta_nomina"]       = "";
            $arr_solicitudEnviada["dias_solicitados"]       = "";
            $arr_solicitudEnviada["fecha_inicio_solicitud"] = "";
            $arr_solicitudEnviada["fecha_fin_solicitud"]    = "";
            $arr_solicitudEnviada["id_solicitud"]           = "";
            $data["arr_solicitudEnviada"] = base64_encode(serialize($arr_solicitudEnviada));
            $data["mensaje"] = utf8_encode("Solicitud de vacaciones CANCELADA exitosamente.");
            prepararLog($user_session, $arr_parametros, $accion, $id_solicitud);
        }
        else
        {
            $data["error"]   = 1;
            $data["mensaje"] = utf8_encode("Su solicitud de vacaciones no pudo ser cancelada");
        }
    }
    return $data;
}

function permitirActualizarAlSolicitante($conex, $wemp_pmla, $wbasedato, $id_solicitud, $es_un_coordinador='')
{
    $filtro = "
                AND  ( ( Dvaerc = '' or Dvaerc = 'RECHAZADO' ) OR ( Dvaerc = 'APROBADO' or Dvaern = 'RECHAZADO' ) )
            ";
    if(!empty($es_un_coordinador) && $es_un_coordinador == 'on')
    {
        $filtro = " AND (Dvaerc <> '' OR Dvaerc = '')
                    AND Dvaern = ''";
    }
    // Con este query se controla si miestras el empleado esta viendo la interfaz de sus solicitudes y quiere actualizar, pero durante esos minutos o segundos
    // el coordinador ya actualizó la solicitud con una respuesta, entonces el empleado no puede modificar ningún dato de la solicitud anterior
    $permitirActualizar = true;
    $sql = "SELECT  id
            FROM    {$wbasedato}_000012
            WHERE   id = '{$id_solicitud}'
                    {$filtro}";
    $resultRes = mysql_query($sql, $conex);
    if(mysql_num_rows($resultRes) == 0)
    {
        $permitirActualizar = false;
    }
    return $permitirActualizar;
}

function prepararLog($usuario_actualiza, $arr_parametros, $accion, $id_solicitud)
{
    global $wfecha, $whora, $wbasedato, $conex;
    if($accion == 'solicitarVacaciones-actualizar' || $accion == 'solicitarVacaciones-solicitar')
    {
        $sql = " INSERT INTO `{$wbasedato}_000015` (`Medico`, `Fecha_data`, `Hora_data`, `Logvns`, `Logvac`, `Logvcm`, `Logest`, `Seguridad`)
                      VALUES ('nomina', '{$wfecha}', '{$whora}', '{$id_solicitud}','{$accion}', 'Dvadso:{$arr_parametros['dias_solicitados']},Dvafid:{$arr_parametros['fecha_inicio_solicitud']},Dvaffd:{$arr_parametros['fecha_fin_solicitud']}', 'on', 'c-{$usuario_actualiza}');";
    }
    elseif($accion == 'cancelarVacaciones')
    {
        $sql = " INSERT INTO `{$wbasedato}_000015` (`Medico`, `Fecha_data`, `Hora_data`, `Logvns`, `Logvac`, `Logvcm`, `Logest`, `Seguridad`)
                      VALUES ('nomina', '{$wfecha}', '{$whora}', '{$id_solicitud}','{$accion}', 'Dvaest:off', 'on', 'c-{$usuario_actualiza}');";
    }
    elseif($accion == 'respuestaJefe')
    {
        $sql = " INSERT INTO `{$wbasedato}_000015` (`Medico`, `Fecha_data`, `Hora_data`, `Logvns`, `Logvac`, `Logvcm`, `Logest`, `Seguridad`)
                      VALUES ('nomina', '{$wfecha}', '{$whora}', '{$id_solicitud}','{$accion}', 'Dvaerc:{$arr_parametros['respuesta_coordinador']}', 'on', 'c-{$usuario_actualiza}');";
    }
    $rs = mysql_query( $sql, $conex );
}

function crearIndiceUnico($wusuario_solicitud, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles)
{
    $wusuario_solicitud = str_replace("-", "_", $wusuario_solicitud);
    $fecha_inicio_pendiente = str_replace("/", "_", $fecha_inicio_pendiente);
    $fecha_fin_pendiente    = str_replace("/", "_", $fecha_fin_pendiente);
    return $wusuario_solicitud.'__'.str_replace("-", "_", $fecha_inicio_pendiente).'__'.str_replace("-", "_", $fecha_fin_pendiente).'__';
}

function crearArrayDatosSolicitud($wusuario_solicitud, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles, $respuesta_coordinador, $respuesta_nomina, $dias_solicitados, $fecha_inicio_solicitud, $fecha_fin_solicitud, $wcentro_costo_empleado, $id_solicitud, $estadoSolicitud='', $fecha_Creacion_Solicitud='')
{
    return array(   "wusuario_solicitud"     => $wusuario_solicitud,
                    "fecha_inicio_pendiente" => $fecha_inicio_pendiente,
                    "fecha_fin_pendiente"    => $fecha_fin_pendiente,
                    "dias_disponibles"       => $dias_disponibles,
                    "respuesta_coordinador"  => $respuesta_coordinador,
                    "respuesta_nomina"       => $respuesta_nomina,
                    "dias_solicitados"       => $dias_solicitados,
                    "fecha_inicio_solicitud" => $fecha_inicio_solicitud,
                    "fecha_fin_solicitud"    => $fecha_fin_solicitud,
                    "id_solicitud"           => $id_solicitud,
                    "wcentro_costo_empleado" => $wcentro_costo_empleado,
                    "estadoSolicitud"        => $estadoSolicitud,
                    "fecha_Creacion_Solicitud"=> $fecha_Creacion_Solicitud
                    );
}

function periodosSolicitados($conex, $wemp_pmla, $wbasedato, $wusuario)
{
    //$wusuario = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $wusuario);
    $arr_solicitudEnviada = array();

    $sql = "SELECT  n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco as wcentro_costo_empleado, 'pendiente' estadoSolicitud, fecha_data fecha_Creacion_Solicitud
            FROM  {$wbasedato}_000012 AS n12
            WHERE n12.Dvause = '{$wusuario}'
              AND n12.Dvaest = 'on'
              AND n12.Dvafid >= '".date('Y-m-d')."'
            UNION
            SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco as wcentro_costo_empleado, 'vencida' estadoSolicitud, fecha_data fecha_Creacion_Solicitud
            FROM  {$wbasedato}_000012 AS n12
            WHERE n12.Dvause = '{$wusuario}'
              AND n12.Dvaest = 'on'
              AND n12.Dvafid < '".date('Y-m-d')."'
             ORDER  BY id_solicitud";

    if($result = mysql_query($sql, $conex))
    {
        while ($row = mysql_fetch_array($result))
        {

            if( $row['estadoSolicitud'] == "vencida" && $row['respuesta_nomina'] == "APROBADO" && ( strtotime(date('Y-m-d'))  - strtotime($row['fecha_fin_solicitud']) >= 0 ) ){//--> ya se disfrutó

            }else{

                $idx_solicitud = crearIndiceUnico($row['wusuario_solicitud'], $row['fecha_inicio_pendiente'], $row['fecha_fin_pendiente'], $row['dias_disponibles']);
                if(!array_key_exists($idx_solicitud, $arr_solicitudEnviada))
                {
                    $arr_solicitudEnviada[$idx_solicitud] = array();
                }

                $arr_solicitudEnviada[$idx_solicitud] =
                            crearArrayDatosSolicitud($row['wusuario_solicitud'], $row['fecha_inicio_pendiente'], $row['fecha_fin_pendiente'], $row['dias_disponibles']
                                                    , $row['respuesta_coordinador'], $row['respuesta_nomina'], $row['dias_solicitados'], $row['fecha_inicio_solicitud']
                                                    , $row['fecha_fin_solicitud'], $row['wcentro_costo_empleado'], $row['id_solicitud'], $row['estadoSolicitud'], $row['fecha_Creacion_Solicitud'], $row['fecha_Creacion_Solicitud']);
            }

        }
    }
    else
    {
        echo "Error ".mysql_error()." => ".$sql;
    }
    return $arr_solicitudEnviada;
}


function periodosSolicitadosPorAprobar($conex, $wemp_pmla, $wbasedato, $wusuario, $wtalhuma, $subempleado = '')
{
    global $fechaMesAtras;
    $fecha_actual = date("Y-m-d");
    // No se mostrarán las solicitudes que ya empezó el periodo de disfrute (Dvafid)
    $arr_solicitudEnviada = array();
    $sql = "SELECT  n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco AS wcentro_costo_empleado, 'pendiente' estadoSolicitud, n12.Fecha_data as fecha_Creacion_Solicitud
            FROM    {$wbasedato}_000012 AS n12
            WHERE   n12.Dvause = '{$wusuario}'
                    AND n12.Dvaest = 'on'
                    AND n12.Dvafid <> ''
                    AND n12.Dvaffd <> ''
                    AND n12.Dvaerc <> 'RECHAZADO'
                    AND n12.Dvaern <> 'RECHAZADO'
                    AND n12.Dvafid >= '{$fecha_actual}'
            UNION
            SELECT  n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco AS wcentro_costo_empleado, 'vencida' estadoSolicitud, n12.Fecha_data as fecha_Creacion_Solicitud
            FROM    {$wbasedato}_000012 AS n12
            WHERE   n12.Dvause = '{$wusuario}'
                    AND n12.Dvaest = 'on'
                    AND n12.Dvafid <> ''
                    AND n12.Dvaffd <> ''
                    AND n12.Dvaern <> 'RECHAZADO'
                    AND n12.Dvaern <> 'APROBADO'
                    AND n12.Dvaerc = 'APROBADO'
                    AND n12.Dvafid < '{$fecha_actual}'
            ORDER BY fecha_inicio_pendiente ASC";

        $sql = "SELECT  n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco AS wcentro_costo_empleado, 'pendiente' estadoSolicitud, n12.Fecha_data as fecha_Creacion_Solicitud
            FROM    {$wbasedato}_000012 AS n12
            WHERE   n12.Dvause = '{$wusuario}'
                    AND n12.Dvaest = 'on'
                    AND n12.Dvafid <> ''
                    AND n12.Dvaffd <> ''
                    AND n12.Dvafid >= '{$fecha_actual}'
                    AND n12.Fecha_data >= '{$fechaMesAtras}'
            UNION
            SELECT  n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco AS wcentro_costo_empleado, 'vencida' estadoSolicitud, n12.Fecha_data as fecha_Creacion_Solicitud
            FROM    {$wbasedato}_000012 AS n12
            WHERE   n12.Dvause = '{$wusuario}'
                    AND n12.Dvaest = 'on'
                    AND n12.Dvafid <> ''
                    AND n12.Dvaffd <> ''
                    AND n12.Dvaerc <> 'APROBADO'
                    AND n12.Dvaerc <> 'RECHAZADO'
                    AND n12.Dvafid < '{$fecha_actual}'
                    AND n12.Fecha_data >= '{$fechaMesAtras}'
            UNION
            SELECT  n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco AS wcentro_costo_empleado, 'vencida' estadoSolicitud, n12.fecha_data as fecha_Creacion_Solicitud
            FROM    {$wbasedato}_000012 AS n12
            WHERE   n12.Dvause = '{$wusuario}'
                    AND n12.Dvaest = 'on'
                    AND n12.Dvafid <> ''
                    AND n12.Dvaffd <> ''
                    AND n12.Dvaerc = 'APROBADO'
                    AND n12.Dvaern <> 'APROBADO'
                    AND n12.Dvaern <> 'RECHAZADO'
                    AND n12.Dvafid < '{$fecha_actual}'
                    AND n12.Fecha_data >= '{$fechaMesAtras}'
            ORDER BY fecha_inicio_pendiente ASC";

    if($result = mysql_query($sql, $conex))
    {
        while ($row = mysql_fetch_array($result))
        {
            $idx_solicitud = $row['id_solicitud'];
            if(!array_key_exists($idx_solicitud, $arr_solicitudEnviada))
            {
                $arr_solicitudEnviada[$idx_solicitud] = array();
            }
            if( strtotime( date('Y-m-d') ) - strtotime( $row['fecha_fin_pendiente'] ) <= 0 ){
                $arrayPeriodosRiesgo = buscarPeriodosEnRiesgo( $row['wusuario_solicitud'] );
                $datosDiasLicencia   = diasLicenciaNoRemunerada( $row['fecha_inicio_pendiente'], date('Y-m-d'), $row['wusuario_solicitud'] );//mas uno porque se incluye el dia final
                $diasLicencia        = $datosDiasLicencia['diasLicencia'];
                $wfec_f              = date("Y-m-d", strtotime( date('Y-m-d')."+ {$diasLicencia} day")); //se mueve  la Fecha Final próximo período la cantidad de dias que se han dado de licencia
                $aux                 = diasDisponiblesPeriodo( $row['fecha_inicio_pendiente'], $wfec_f, $cedulaUsuario, $arrayPeriodosRiesgo );//dias a los que se tiene derecho en el último periodo disfrutado
                foreach ($aux as $keyTipo => $dias ) {
                    $diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
                    $diasLicenciaPeriodo    = calcularDiasGanados( $keyTipo, $datosDiasLicencia['diasNoContados'][$keyTipo] );

                    $diasDisponiblesPeriodo = round($diasDisponiblesPeriodo);
                    $diasLicenciaPeriodo    = round($diasLicenciaPeriodo);
                    $diasDisponibles +=  ( $diasDisponiblesPeriodo - $diasLicenciaPeriodo );
                    $diasDisponibles = round( $diasDisponibles );
                    $row['dias_disponibles'] = $diasDisponibles;
                }
            }
            $arr_solicitudEnviada[$idx_solicitud] =
                        crearArrayDatosSolicitud($row['wusuario_solicitud'], $row['fecha_inicio_pendiente'], $row['fecha_fin_pendiente'], $row['dias_disponibles']
                                                , $row['respuesta_coordinador'], $row['respuesta_nomina'], $row['dias_solicitados'], $row['fecha_inicio_solicitud']
                                                , $row['fecha_fin_solicitud'], $row['wcentro_costo_empleado'], $row['id_solicitud'], $row['estadoSolicitud'], $row['fecha_Creacion_Solicitud'] );
        }
    }
    else
    {
        //error
    }

    return $arr_solicitudEnviada;
}

function centroCostoEmpleado($conex, $wemp_pmla, $wbasedato, $wtalhuma, $user_session_solicitante)
{
    $centroCostoEmpleado = array("codigo"=>"", "nombre"=>"");

    $cco_talhuma = buscarCodigoCcoTalhuma($conex, $wemp_pmla, $wbasedato, $wtalhuma, $user_session_solicitante);

    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    if($cco_talhuma != '' && $row = mysql_fetch_array($res))
    {
        $tabla_CCO = $row['Emptcc'];
        switch ($tabla_CCO)
        {
            case "clisur_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    clisur_000003 AS tb1
                                WHERE   tb1.Ccocod = '{$cco_talhuma}'";
                    break;
            case "farstore_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    farstore_000003 AS tb1
                                WHERE   tb1.Ccocod = '{$cco_talhuma}'";
                    break;
            case "costosyp_000005":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    costosyp_000005 AS tb1
                                WHERE   tb1.Ccocod = '{$cco_talhuma}'";
                    break;
            case "uvglobal_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    uvglobal_000003 AS tb1
                                WHERE   tb1.Ccocod = '{$cco_talhuma}'
                                GROUP BY    tb1.Ccocod
                                ORDER BY    tb1.Ccodes";
                    break;
            default:
                    $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    costosyp_000005 AS tb1
                                WHERE   tb1.Ccocod = '{$cco_talhuma}'
                                GROUP BY    tb1.Ccocod
                                ORDER BY    tb1.Cconom";
        }

        if($result = mysql_query($query,$conex))
        {
            $row = mysql_fetch_array($result);

            $centroCostoEmpleado['codigo'] = $row['codigo'];
            $centroCostoEmpleado['nombre'] = $row['nombre'];
        }
    }
    return $centroCostoEmpleado;
}

function consultarperiodosDisponiblesPendientes( $wusuario_solicitud_unix ){
    global $conexunix;
    $periodosPendientes = array();
    $query = "SELECT SO.COD_SOCIEDAD       SOCIEDAD_CODIGO,
                     SO.NOMBRE_SOCIEDAD    SOCIEDAD_NOMBRE,
                     CC.CENCOS_CODIGO      CENCOS_CODIGO,
                     CENCOS_NOMBRE         CENCOS_NOMBRE,
                     EM.EMP_CODIGO         EMP_CODIGO,
                     EMP_APELLIDO1         APELLIDO1,
                     EMP_APELLIDO2         APELLIDO2,
                     EMP_NOMBRE            NOMBRE,
                     NOMBRE_TIP_DOC        IDENTIFICACION_NOMBRE,
                     EMP_CEDULA            IDENTIFICACION_NRO,
                     DECODE(EMP_ESTADO,
                     'N','ACTIVO',
                     'R','RETIRADO',
                     'V','VACACIONES')    ESTADO,
                     VPEN_TIPO_LEG        VACACION_TIPO,
                     VPEN_PERIODO_INI     PERIODO_INICIAL,
                     VPEN_PERIODO_FIN     PERIODO_FINAL,
                     NVL(VPEN_DIAS,0)     DIAS_DERECHO,
                     NVL(VPEN_DIAS_PEND,0)     DIAS_PENDIENTES
                FROM TIP_DOC_IDENT, SOCIEDAD SO, CENTRO_COSTO CC,  EMPLEADO EM, TRH_VAC_PEN VP
               WHERE COD_TIP_DOC               = EMP_TIPO_IDENTIF
                 AND SO.COD_SOCIEDAD           = EM.EMP_SOCIEDAD
                 AND CC.CENCOS_CODIGO          = EM.EMP_CC_CONTABLE
                 AND EM.EMP_CODIGO             = VP.EMP_CODIGO
                 AND EM.EMP_CODIGO             = '{$wusuario_solicitud_unix}'
                 AND EM.EMP_EMPRESA            = VP.EN1_CODIGO
                 AND VPEN_PERIODO_FIN         <= SYSDATE
                 AND ( (EMP_ESTADO <> 'R') OR (EMP_ESTADO='R' AND EMP_FECHA_RETIRO>SYSDATE))
                 AND EXISTS (SELECT 'X' FROM TRH_CONCEPTO_TIPO_NOM WHERE CON_CODIGO = 145 AND TNOM_CODIGO = EMP_TIPO_NOMINA AND FLIQ_CODIGO='FOR')
                 AND EM.EMP_FECHA_INI_CONTRATO <= SYSDATE
               UNION
              SELECT SO.COD_SOCIEDAD       SOCIEDAD_CODIGO,
                     SO.NOMBRE_SOCIEDAD    SOCIEDAD_NOMBRE,
                     CC.CENCOS_CODIGO      CENCOS_CODIGO,
                     CENCOS_NOMBRE         CENCOS_NOMBRE,
                     EM.EMP_CODIGO         EMP_CODIGO,
                     EMP_APELLIDO1         APELLIDO1,
                     EMP_APELLIDO2         APELLIDO2,
                     EMP_NOMBRE            NOMBRE,
                     NOMBRE_TIP_DOC        IDENTIFICACION_NOMBRE,
                     EMP_CEDULA            IDENTIFICACION_NRO,
                     DECODE(EMP_ESTADO,
                     'N','ACTIVO',
                     'R','RETIRADO',
                     'V','VACACIONES')    ESTADO,
                     'LEG'                VACACION_TIPO,
                     FRH_VPE_FE_MA(EM.EMP_CODIGO,EM.EMP_EMPRESA,EM.EMP_FECHA_INI_CONTRATO,'LEG',SYSDATE) PERIODO_INICIAL,
                     SYSDATE      PERIODO_FINAL,
                     FRH_DVA_DV(NVL(EM.PLV_CODIGO,FRH_SLO_VC(EM.EMP_SOCIEDAD,EM.EMP_LOCALIDAD,'PVP')),EM.EMP_TIPO_NOMINA,1,'LEG') DIAS_DERECHO,
                     DECODE(EMP_ESTADO,'R',FRH_COP_CO_RF_VCA(EM.EMP_CODIGO,EM.EMP_EMPRESA,145,TRUNC(SYSDATE,'MM'),SYSDATE,'UND'),
                     (CASE WHEN
                                (ROUND(NVL(FRH_HVA_TL_TC_DI(EM.EMP_CODIGO,EM.EMP_EMPRESA,EM.EMP_TIPO_NOMINA,EM.EMP_TIPOLIQ,EM.EMP_FECHA_INI_CONTRATO,
                                                        EM.EMP_FECHA_FIN_CONTRATO, SYSDATE,EM.EMP_ANTIGUEDAD_ANT,FRH_TVF_FE_VA(3,14,SYSDATE),
                                                  NVL(EM.PLV_CODIGO,FRH_SLO_VC(EM.EMP_SOCIEDAD,EM.EMP_LOCALIDAD,'PVP')),'LEG','NET'),0),2)<0)
                                THEN
                          (SELECT NVL(SUM(VPEN_DIAS_PEND),0) FROM TRH_VAC_PEN VP WHERE VP.EMP_CODIGO=EM.EMP_CODIGO AND EM.EMP_CODIGO = '{$wusuario_solicitud_unix}' AND VPEN_PERIODO_FIN > SYSDATE AND VPEN_TIPO_LEG='LEG')+
                             ROUND(NVL(FRH_HVA_TL_TC_DI(EM.EMP_CODIGO,EM.EMP_EMPRESA,EM.EMP_TIPO_NOMINA,EM.EMP_TIPOLIQ,EM.EMP_FECHA_INI_CONTRATO,
                                                        EM.EMP_FECHA_FIN_CONTRATO, SYSDATE,EM.EMP_ANTIGUEDAD_ANT,FRH_TVF_FE_VA(3,14,SYSDATE),
                                                  NVL(EM.PLV_CODIGO,FRH_SLO_VC(EM.EMP_SOCIEDAD,EM.EMP_LOCALIDAD,'PVP')),'LEG','NET'),0),2)
                        ELSE
                                  ROUND(NVL(FRH_HVA_TL_TC_DI(EM.EMP_CODIGO,EM.EMP_EMPRESA,EM.EMP_TIPO_NOMINA,EM.EMP_TIPOLIQ,EM.EMP_FECHA_INI_CONTRATO,
                                                        EM.EMP_FECHA_FIN_CONTRATO, SYSDATE,EM.EMP_ANTIGUEDAD_ANT,FRH_TVF_FE_VA(3,14,SYSDATE),
                                                  NVL(EM.PLV_CODIGO,FRH_SLO_VC(EM.EMP_SOCIEDAD,EM.EMP_LOCALIDAD,'PVP')),'LEG','NET'),0),2)
                                END)+
                        (SELECT NVL(SUM(NVL(HVAC_DIAS_TIEMPO,0)+NVL(HVAC_DIAS_DINERO,0)),0)
                                 FROM   TRH_HIST_VAC HV
                                 WHERE  HV.EMP_CODIGO = EM.EMP_CODIGO
                                 AND    EM.EMP_CODIGO = '{$wusuario_solicitud_unix}'
                                 AND    HVAC_FECHA_FIN_PAGO > SYSDATE)) DIAS_PENDIENTES
                FROM TIP_DOC_IDENT, SOCIEDAD SO, CENTRO_COSTO CC, EMPLEADO EM
                WHERE COD_TIP_DOC                = EMP_TIPO_IDENTIF
                AND   SO.COD_SOCIEDAD            = EM.EMP_SOCIEDAD
                AND   CC.CENCOS_CODIGO           = EM.EMP_CC_CONTABLE
                AND   EM.EMP_CODIGO             = '{$wusuario_solicitud_unix}'
                AND   ( (EMP_ESTADO <> 'R') OR (EMP_ESTADO='R' AND EMP_FECHA_RETIRO>SYSDATE))
                AND   EXISTS (SELECT 'X' FROM TRH_CONCEPTO_TIPO_NOM WHERE CON_CODIGO = 145 AND TNOM_CODIGO = EMP_TIPO_NOMINA AND FLIQ_CODIGO='FOR')
                AND   EM.EMP_FECHA_INI_CONTRATO <= SYSDATE";

    $rs = odbc_do( $conexunix,$query );
    $periodo  = array();
    while ( odbc_fetch_row($rs) ){
        $periodo['wfecproI'] = date('Y-m-d',strtotime(odbc_result($rs,13)));
        $periodo['wfecproF'] = date('Y-m-d',strtotime(odbc_result($rs,14)));
        $periodo['diasDisponibles'] = round(odbc_result($rs,16));//--> se refiere a la cantidad de dias pendientes por disfrutar de ese periodo.
        $periodo['wfecproM'] = date('Y-m-d',strtotime(odbc_result($rs,13).' +1 year'));
        $periodo['wfecproM'] = date('Y-m-d',strtotime($periodo['wfecproM'].' 00:00:00 -1 day'));
        array_push( $periodosPendientes, $periodo );
    }
    return( $periodosPendientes );
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
                $usuario_coordinador["lista_solicitantes"][$row['subordinadoAux']] = array();// antes $usuario_coordinador["lista_solicitantes"][$codigo_usumatrix]
            }

            $usuario_coordinador["lista_solicitantes"][$row['subordinadoAux']] =
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

function buscarCodigoCcoTalhuma($conex, $wemp_pmla, $wbasedato, $wtalhuma, $user_session)
{
    $cco_usutalhuma = "";
    $usuario_talhuma = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);
    // $expl = explode("-", $usuario_talhuma);
    $sql = "SELECT  Idecco AS cco_talhuma
            FROM    {$wtalhuma}_000013
            WHERE   Ideuse = '{$usuario_talhuma}'
                    AND Ideest = 'on'";
    $result = mysql_query($sql, $conex) or die ("Error centro costo empleado ".mysql_error().' => '.$sql);
    if(mysql_num_rows($result) > 0)
    {
        $row = mysql_fetch_array($result);
        $cco_usutalhuma = $row['cco_talhuma'];
    }
    return $cco_usutalhuma;
}

/**
 * Se encarga de buscar el código de la empresa a la que pertenece el empleado, y al final retorna un código de empleado de 5 digitos pero concatenando al final el código de la empresa
 *
 * @param unknown $wemp_pmla
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param string $cod_use_emp : Código de sesión del usuario que está autenticado en el sistema, este código puede tener 5 o más digitos.
 * @return string (código del usuario en sus últimos 5 digitos con el código de la empresa concatenado al final xxxxx-xx)
 */
function empresaEmpleado($wemp_pmla, $conex, $wbasedato, $cod_use_emp)
{
    $use_emp = '';

    $user_session = explode('-',$cod_use_emp);
    $user_session = (count($user_session) > 1) ? $user_session[1] : $user_session[0];

    $q = "  SELECT  Codigo, Empresa
            FROM    usuarios
            WHERE   codigo = '{$user_session}'
                    AND Activo = 'A'
            UNION
            SELECT  Codigo, Empresa
            FROM    usuarios
            WHERE   codigo = '{$wemp_pmla}{$user_session}'
                    AND Activo = 'A'";
    $res = mysql_query($q,$conex);
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

        $use_emp = $user_session.'-'.$row['Empresa']; // concatena los últimos 5 digitos del código del usuario con el código de la empresa a la que pertenece.
    }
    return $use_emp;
}

function diasLicenciaNoRemunerada( $wfechaInicio, $wfechaFinal, $codigo ){

    //echo "llamado con los datos: $wfechaInicio, $wfechaFinal, $codigo"; conceptosLicenciasNoRemuneradas
    global $conexunix, $conex;
    global $arrayPeriodosRiesgo;
    $diasLicencia       = 0;
    $tuvoLicencia       = false;
    $datosLicencia      = array();
    $conceptosLicencias = array();
    //$diasLicencia = array();

    $query  = " SELECT Detval
                  FROM root_000051
                 WHERE Detapl = 'conceptosLicenciasNoRemuneradas'";
    $rs     = mysql_query( $query, $conex );
    while( $row = mysql_fetch_assoc( $rs ) ){
        $datos = explode( ",", $row['Detval'] );
        foreach ($datos as $key => $dato) {
            array_push( $conceptosLicencias, "'".$dato."'" );
        }

    }
    $conceptos = implode( ",", $conceptosLicencias );

    $query  = " SELECT inccod, TO_CHAR(incrfi, 'YYYY-MM-DD'), TO_CHAR(incrff, 'YYYY-MM-DD')
                  FROM noinc
                 WHERE inccod = '{$codigo}'
                   AND incrff >= TO_DATE('{$wfechaInicio}', 'YYYY-MM-DD')
                   AND incrfi <= TO_DATE('{$wfechaFinal}', 'YYYY-MM-DD')
                   AND inccon in ( $conceptos )
                 GROUP BY inccod, incrfi, incrff";

    $rs     = odbc_do( $conexunix,$query );

    while ( odbc_fetch_row($rs) ){


        $fecLimiteInferior  = ( strtotime(odbc_result($rs,2)) <= strtotime($wfechaInicio) ) ? $wfechaInicio : odbc_result($rs,2);
        $fecLimiteSuperior  = ( strtotime(odbc_result($rs,3)) >= strtotime($wfechaFinal) ) ? $wfechaFinal : odbc_result($rs,3);

        $tuvoLicencia  = true;
        $diasLicencia += diasEntreFechas( $fecLimiteInferior, $fecLimiteSuperior );
        $datosLicencia['diasLicencia']    += diasEntreFechas( $fecLimiteInferior, $fecLimiteSuperior );
        if( !isset($datosLicencia['diasNoContados'])){
            $datosLicencia['diasNoContados'] = array();
        }
        $datosLicencia['diasNoContados']  += diasDisponiblesPeriodo( $fecLimiteInferior, $fecLimiteSuperior, $cedulaUsuario, $arrayPeriodosRiesgo ); //2016-01-18
    }

    $datosLicencia['diasLicencia']++;

    if( !$tuvoLicencia ){
        $diasLicencia = 0;
        $datosLicencia['diasLicencia'] = 0;
    }else{
        $diasLicencia = $diasLicencia + 1;
    }
    return $datosLicencia;
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
function diasDisponiblesPeriodo( $fecha_inicial, $fecha_final, $wcedula, $arrayPeriodosRiesgo ){

    global $conexunix;
    $inicioPeriodoEfectivoDeRiesgo = "";
    $finalPeriodoEfectivoDeRiesgo  = "";
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

            if( $periodoEnRiesgo['fechaSalida'] == "0000-00-00"  and $periodoEnRiesgo['esado'] == "on" ){
                $finalPeriodoEfectivoDeRiesgo = $fecha_final;
            }

            $diferenciaEnDias = diasEntreFechas( $fecha_final, $periodoEnRiesgo['fechaSalida'] );
            if( $diferenciaEnDias <= 0 ){//--> quiere decir que el periodo de ingreso al riesgo es anterior al periodo consultado
                $finalPeriodoEfectivoDeRiesgo = $periodoEnRiesgo['fechaSalida'];
            }else{
                $finalPeriodoEfectivoDeRiesgo = $fecha_final;
            }
            $meses['riesgo'] = diasEntreFechas( $inicioPeriodoEfectivoDeRiesgo, $finalPeriodoEfectivoDeRiesgo );
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
        $diasGanados = ( diasRiesgo*$diasEnTipo)/365;
    }else{
        $diasGanados = ( diasSinRiesgo*$diasEnTipo)/365;
    }
    return( $diasGanados);
}


/**
 * Funcion que verifica que las fechas del periodo a disfrutar seleccionadas no se solapen en ningún punto con otras solicitudes, pendientes de aprobar o aprobadas
**/
function validarRangoFechas( $conex, $wemp_pmla, $wbasedato, $arr_parametros, $user_solicitud, $idSolicitud = '' ){

    $fecha_inicio_solicitud = $arr_parametros['fecha_inicio_solicitud'];
    $fecha_fin_solicitud    = $arr_parametros['fecha_fin_solicitud'];

    ( trim($idSolicitud) != "" ) ? $condicionId =  " AND id != '{$idSolicitud}'" : $condicionId =  "";

    //--> se valida que no se vaya a iniciar el periodo en un periodo ya solicitado
    $sql = "SELECT COUNT(*) cantidad
              FROM {$wbasedato}_000012
             WHERE Dvause = '{$user_solicitud}'
               AND  (
                        ( Dvafid <= '$fecha_inicio_solicitud' AND  Dvaffd >= '$fecha_inicio_solicitud' ) OR
                        ( Dvafid <= '$fecha_fin_solicitud'    AND  Dvaffd >= '$fecha_fin_solicitud' ) OR
                        ( Dvafid >= '$fecha_inicio_solicitud' AND  Dvaffd <= '$fecha_fin_solicitud' )
                    )
               AND Dvaerc != 'RECHAZADO'
               AND Dvaern != 'RECHAZADO'
               AND Dvaest  = 'on'
               {$condicionId}";
    $rs  = mysql_query( $sql, $conex );
    $row = mysql_fetch_assoc( $rs );
    if( $row['cantidad']*1 > 0 ){
        return( false );
    }else{
        return( true );
    }
}

function buscarFechaRegreso( $wusuario_solicitud, $wfec_i_periodo, $wfec_i, $wfec_f_periodo, $objSolicitud ,$diasDisfrutar, $diasDisfrutadosHoy=0 ){
    global $wemp_pmla, $conex, $wbasedato, $porAprobar ;
    $data = array();
    /* validamos si la fecha de inicio es un domingo o un festivo, para retornar de una vez la invalidez de la solicitud */
    $numeroDiaSemana = date("w", strtotime( $wfec_i ));

    if( $numeroDiaSemana*1 == 0 ){
        $data['mensaje'] = utf8_encode( "Fecha de Inicio Inadecuada ya que corresponde a un dia Domingo" );
        $data['error'] = 1;
        return($data);
    }

    $query = " SELECT count(*) diasFestivos
                 FROM root_000063
                WHERE Fecha = '{$wfec_i}'";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_assoc( $rs );
    if( $row['diasFestivos'] > 0 ){
        $data['mensaje'] = utf8_encode( "Fecha de Inicio Inadecuada, corresponde a un dia Festivo" );
        $data['error'] = 1;
        return($data);
    }

    //--> miramos si la fecha final del periodo a solicitar es el dia de hoy, y cuantos dias tiene derecho a disfrutar
    if( $porAprobar != "on"){
        if( $objSolicitud == "fecha" ){
            if( strtotime( $wfec_f_periodo ) >= strtotime( date("Y-m-d") ) ){
                //--> recalcular los dias a los que se tiene derecho desde el inicio de la solicitud
                if( $diasDisfrutar*1 <= 15 ){ //--> si los dias calculados hasta hoy dan menos de 15 dias, se verifica la fecha de inicio de la solicitud para saber si tendra derecho a mas dias en ese momento

                    $arrayPeriodosRiesgo = array();
                    $wfecha_inicio_aux   = $wfec_i_periodo;
                    $wfecha_final_aux    = $wfec_i;

                    $query = " SELECT Rieusu usuario, Riefei fechaIngreso, Riefes fechaSalida, Rieest estado
                                 FROM {$wbasedato}_000013
                                WHERE Rieusu = '{$wusuario_solicitud}'
                                  AND Rieest = 'on'
                                ORDER BY id";

                    $rs   = mysql_query( $query, $conex );
                    while( $row = mysql_fetch_assoc( $rs ) ){
                        if( $row['fechaSalida'] == "0000-00-00" );
                            $row['fechaSalida'] = date('Y-m-d');

                        $aux['fechaIngreso'] = $row['fechaIngreso'];
                        $aux['fechaSalida']  = $row['fechaSalida'];
                        $aux['estado']       = $row['estado'];
                        $aux['diasRiesgo']   = diasEntreFechas( $row['fechaIngreso'], $row['fechaSalida'] );
                        array_push( $arrayPeriodosRiesgo, $aux );
                    }

                    $aux = diasDisponiblesPeriodo( $wfecha_inicio_aux, $wfecha_final_aux, '', $arrayPeriodosRiesgo );
                    foreach ($aux as $keyTipo => $dias ) {
                        $diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
                        $diasDisponiblesPeriodo = round($diasDisponiblesPeriodo);
                        $diasDisponibles +=  $diasDisponiblesPeriodo;
                        $diasDisponibles = round( $diasDisponibles );
                    }

                    if( $diasDisponibles > 15 ){
                        $diasDisponibles = 15;
                    }

                    $diasDisponibles = $diasDisponibles - $diasDisfrutadosHoy;
                    $diasDisfrutar   = $diasDisponibles;
                }else{
                    $diasDisponibles = $diasDisfrutar;
                }


            }else{
                 $diasDisponibles = $diasDisfrutar;
            }
        }else{
            if( $porAprobar == "on" ){

                ///--> consultar si hay solicitudes de periodos posteriores
                $diasDisponibles = $diasDisfrutar;
            }else{
                $diasDisponibles = $diasDisfrutar;
            }
        }
    }

    if( $porAprobar == "on" ){
        ///--> consultar si hay solicitudes de periodos posteriores
        $diasDisponibles = $diasDisfrutar;
    }

    $diaEvaluado = $wfec_i; //--> el dia inicial elegido está aprobado
    $wfec_f      = $diaEvaluado;
    $diasDisfrutar --;


    while( $diasDisfrutar > 0 ){

        $diaEvaluar = strtotime( $diaEvaluado."+1 day" );
        $numeroDia  = date( "w", $diaEvaluar );
        $diaEvaluar = date( "Y-m-d", $diaEvaluar );
        if( $numeroDia*1 != 0 ){//--> si no es un domingo

            $query = " SELECT count(*) diasFestivos
                         FROM root_000063
                        WHERE Fecha = '{$diaEvaluar}'";

            $rs    =  mysql_query( $query, $conex );
            $row   =  mysql_fetch_assoc( $rs );
            if( $row['diasFestivos'] == 0 ){//--> no es domingo ni es festivo, se cuenta
                $diasDisfrutar --;
            }
        }
        $diaEvaluado = $diaEvaluar;
    }


    $data['mensaje']       = utf8_encode( $diaEvaluado );
    $data['error']         = 0;
    $data['diasDisfrutar'] = $diasDisponibles;
    return($data);
}

function validarCargoUsuario( $codigo_empleado ){
    global $conexunix, $conex, $wemp_pmla;
    $codigos = array();

    $query = " SELECT Detval
                 FROM root_000051
                WHERE Detapl = 'contratosSinVacaciones'
                  AND Detemp = '{$wemp_pmla}'";
    $rs    = mysql_query( $query, $conex );
    while( $row = mysql_fetch_array( $rs ) ){
        $codigos = explode( ",", $row[0] );
    }

    $sql = "SELECT percot
              FROM noper
             WHERE percod = '{$codigo_empleado}'";
    $res = @odbc_do($conexunix,$sql);
    while(odbc_fetch_row($res)){
        $tipoContrato = odbc_result($res,1);
        if( in_array( $tipoContrato, $codigos ) ){
            return( false );
        }else{
            return( true );
        }
    }
    return( true );
}

function buscarPeriodosEnRiesgo( $wusuario_solicitud ){
    global $wbasedato, $conex;
    $arrayPeriodosRiesgo = array();
    $query = " SELECT Rieusu usuario, Riefei fechaIngreso, Riefes fechaSalida, Rieest estado
                 FROM {$wbasedato}_000013
                WHERE Rieusu = '{$wusuario_solicitud}'
                  AND Rieest = 'on'
                ORDER BY id";

    $rs   = mysql_query( $query, $conex );
    while( $row = mysql_fetch_assoc( $rs ) ){
        if( $row['fechaSalida'] == "0000-00-00" )
            $row['fechaSalida'] = date('Y-m-d');

        $aux['fechaIngreso'] = $row['fechaIngreso'];
        $aux['fechaSalida']  = $row['fechaSalida'];
        $aux['estado']       = $row['estado'];
        $aux['diasRiesgo']   = diasEntreFechas( $row['fechaIngreso'], $row['fechaSalida'] );
        array_push( $arrayPeriodosRiesgo, $aux );
    }
    return( $arrayPeriodosRiesgo );
}

function buscarCoordinador( $wcodigoUsuarioTalhuma ){

    global $conex, $wemp_pmla, $wtalhuma;
    $sql = "SELECT  tlh08.Ajeucr AS coordinador, tlh08.Ajecoo AS sub_coordinador
                    , tlh13.Ideno1 AS nombre1, tlh13.Ideno2 AS nombre2, tlh13.Ideap1 AS apellido1, tlh13.Ideap2 AS apellido2
            FROM    {$wtalhuma}_000008 AS tlh08
                    INNER JOIN
                    {$wtalhuma}_000013 AS tlh13 ON (tlh13.Ideuse = tlh08.Ajeucr AND tlh13.Ideest = 'on')
            WHERE   tlh08.Ajeuco = '{$wcodigoUsuarioTalhuma}'
                    AND tlh08.Forest = 'on'
            ORDER BY tlh13.Ideno1, tlh13.Ideno2, tlh13.Ideap1, tlh13.Ideap2";
     $result = mysql_query($sql, $conex) or die ("Error árbol de relación ".mysql_error().' => '.$sql);
    if(mysql_num_rows($result) > 0)
    {
        while($row = mysql_fetch_array($result) )
        {
            $nombreCoordinador = $row['nombre1']." ".str_replace( "NO APLICA", "", $row['nombre2'] )." ".$row['apellido1']." ".str_replace( "NO APLICA", "", $row['apellido2'] );
        }
    }

    return( $nombreCoordinador );
}

function consultarNombreUsuario ( $wcodigoUsuarioTalhuma ){
    global $conex, $wemp_pmla, $wtalhuma;
    $sql = "SELECT  tlh13.Ideno1 AS nombre1, tlh13.Ideno2 AS nombre2, tlh13.Ideap1 AS apellido1, tlh13.Ideap2 AS apellido2
            FROM    {$wtalhuma}_000013 tlh13
            WHERE   tlh13.Ideuse = '$wcodigoUsuarioTalhuma'
            ORDER BY tlh13.Ideno1, tlh13.Ideno2, tlh13.Ideap1, tlh13.Ideap2";
     $result = mysql_query($sql, $conex) or die ("Error árbol de relación ".mysql_error().' => '.$sql);
    if(mysql_num_rows($result) > 0)
    {
        while($row = mysql_fetch_array($result) )
        {
            $nombreUsuario = $row['nombre1']." ".str_replace( "NO APLICA", "", $row['nombre2'] )." ".$row['apellido1']." ".str_replace( "NO APLICA", "", $row['apellido2'] );
        }
    }

    return( $nombreUsuario );
}

if(isset($accion) && isset($form)){
    $data = array('error'=>0,'mensaje'=>'','html'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                case 'solicitarVacaciones':
                    $rangoFechasOk  = false;
                    $arr_parametros = array();
                    $arr_parametros["arr_solicitudEnviada"]    = unserialize(base64_decode($arr_solicitudEnviada));
                    $arr_parametros["dias_solicitados"]        = $dias_solicitados;
                    $arr_parametros["fecha_inicio_solicitud"]  = $fecha_inicio_solicitud;
                    $arr_parametros["fecha_fin_solicitud"]     = $fecha_fin_solicitud;
                    $arr_parametros["es_un_coordinador"]       = $es_un_coordinador;
                    $arr_parametros["usuario_solicitud"]       = explode( "_",$idx_diferenciador );
                    $arr_parametros["usuario_solicitud"]       = $arr_parametros["usuario_solicitud"][0]."-".$arr_parametros["usuario_solicitud"][1];

                    //* VALIDACION DE LAS FECHAS DE LA SOLICITUD A REALIZAR, ESTA VALIDACIÓN TIENE EL PROPÓSITO DE NO PERMITIR QUE SE SOLAPEN FECHAS DE VARIAS SOLICITUDES*//
                    if($operacion == "solicitar")
                    {
                        $rangoFechasOk = validarRangoFechas( $conex, $wemp_pmla, $wbasedato, $arr_parametros, $arr_parametros["usuario_solicitud"] );
                        if( $rangoFechasOk ){
                            $data = solicitarVacaciones($conex, $wemp_pmla, $wbasedato, $data, $arr_parametros, $arr_parametros["usuario_solicitud"], $form.'-'.$operacion);

                        }else{

                            $data["error"]        = 1;
                            $data["mensaje"]      = utf8_encode("Periodo a disfrutar, inválido 2");

                        }
                    }
                    elseif($operacion == "actualizar")
                    {
                        $data = actualizarVacaciones($conex, $wemp_pmla, $wbasedato, $data, $arr_parametros, $arr_parametros["usuario_solicitud"], $form.'-'.$operacion);
                    }
                    break;

                default :
                    $data['mensaje'] = utf8_encode($no_exec_sub)." - ".$operacion;
                    $data['error'] = 1;
                    break;
            }
            break;

        /*default :
            $data['mensaje'] = utf8_encode($no_exec_sub);
            $data['error'] = 1;
            break;*/

        case 'update':
            switch($form)
            {
                case 'cancelarVacaciones':
                    $arr_parametros = array();
                    $arr_parametros["arr_solicitudEnviada"] = unserialize(base64_decode($arr_solicitudEnviada));
                    $data = cancelarVacaciones($conex, $wemp_pmla, $wbasedato, $data, $arr_parametros, $user_session, $form, $es_un_coordinador);
                    break;

                case 'respuestaJefe':
                    $arr_solicitudEnviada = unserialize(base64_decode($arr_solicitudEnviada));
                    $arr_solicitudEnviada_log = array("respuesta_coordinador"=>$respuesta_coordinador);

                    if(!permitirActualizarAlSolicitante($conex, $wemp_pmla, $wbasedato, $id_solicitud, 'on'))
                    {
                        $data["error"]   = 1;
                        $data["mensaje"] = utf8_encode("Su solicitud de vacaciones no pudo ser actualizada\n\nNómina ya respondió la solicitud");
                    }
                    else
                    {
                        $usu_coordinador= empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);
                        $sql = "SELECT Dvause
                                  FROM {$wbasedato}_000012
                                 WHERE id = '$id_solicitud'";
                        $rs  = mysql_query( $sql, $conex );
                        $row = mysql_fetch_assoc( $rs );
                        $usuario_solicitante = $row['Dvause'];

                        if( $respuesta_coordinador == "APROBADO" ){

                            $sql = "SELECT count(*) cantidad
                                      FROM {$wbasedato}_000012
                                     WHERE Dvause = '{$usuario_solicitante}'
                                      AND  Dvapfi < '{$fechaInicialPeriodo}'
                                      AND  Dvapff < '{$fechaFinalPeriodo}'
                                      AND  ( ( Dvaern = '' AND ( Dvaerc = '' or Dvaerc = 'RECHAZADO') ) OR ( Dvaern = 'RECHAZADO' AND  Dvaerc = 'APROBADO' ) )
                                      AND  Dvaest = 'on'";
                            $rs  = mysql_query( $sql, $conex );
                            $row = mysql_fetch_assoc( $rs );
                            $cant = $row['cantidad'];
                            if( $cant > 0 ){//-> hay solicitudes de periodos anteriores pendientes de revisar o rechazados
                                $data["error"]   = 2;
                                $data["mensaje"] = utf8_encode("La solicitud no puede ser aprobada puesto que hay alguna solicitud \n de un periodo anterior sin revisar y aprobar o rechazada por nomina");
                                include_once("free.php");
                                echo json_encode($data);
                                return;
                            }

                            $sql = "UPDATE {$wbasedato}_000012
                                        SET   Dvaerc = '{$respuesta_coordinador}'
                                            , Dvafrc = '{$wfecha}'
                                            , Dvahrc = '{$whora}'
                                            , Dvacdc = '{$usu_coordinador}'
                                    WHERE   id = '{$id_solicitud}'";
                            if($result = mysql_query($sql, $conex)){
                                $actualizados = true;

                                $arr_solicitudEnviada['respuesta_coordinador'] = $respuesta_coordinador;
                                $data["arr_solicitudEnviada"] = base64_encode(serialize($arr_solicitudEnviada));
                                $data["mensaje"] = utf8_encode("Se ha modificado la solicitud exitosamente.");

                                prepararLog($user_session, $arr_solicitudEnviada_log, $form, $id_solicitud);
                            }
                        }else if( $respuesta_coordinador == "RECHAZADO" ){

                            $actualizados = false;
                            $sql1 = "SELECT id
                                      FROM {$wbasedato}_000012
                                     WHERE Dvause = '{$usuario_solicitante}'
                                      AND  Dvapfi >= '{$fechaInicialPeriodo}'
                                      AND  Dvapff >= '{$fechaFinalPeriodo}'
                                      AND  Dvaern = ''
                                      AND  Dvaest = 'on'";
                            $res  = mysql_query( $sql1, $conex );

                            while( $rowrec = mysql_fetch_assoc( $res ) ){
                                $sql = "UPDATE {$wbasedato}_000012
                                       SET   Dvaerc = '{$respuesta_coordinador}'
                                           , Dvafrc = '{$wfecha}'
                                           , Dvahrc = '{$whora}'
                                           , Dvacdc = '{$usu_coordinador}'
                                    WHERE  id = '{$rowrec['id']}'";

                                if($result = mysql_query($sql, $conex)){
                                    $actualizados = true;

                                    $arr_solicitudEnviada['respuesta_coordinador'] = $respuesta_coordinador;
                                    $data["arr_solicitudEnviada"] = base64_encode(serialize($arr_solicitudEnviada));
                                    $data["mensaje"] = utf8_encode("Se ha modificado la solicitud exitosamente.");

                                    prepararLog($user_session, $arr_solicitudEnviada_log, $form, $rowrec['id']);
                                }
                            }
                        }

                        if( $actualizados )
                        {
                        }
                        else
                        {
                            $data["error"]   = 1;
                            $data["mensaje"] = utf8_encode("Su solicitud de vacaciones no pudo ser actualizada");
                        }
                    }
                    break;

                default :
                    $data['mensaje'] = utf8_encode($no_exec_sub);
                    $data['error'] = 1;
                    break;
            }
            break;

        default :
            $data['mensaje'] = utf8_encode($no_exec_sub);
            $data['error'] = 1;
            break;
    }
    include_once("free.php");
    echo json_encode($data);
    return;
}

//---> cambios de camilo
//funciones ajax fuera de los tópicos, update, insert y delete

if( $consultaAjax == "si" ){
    if( $peticion == "buscarFechaRegreso" ){
        /* empezar a buscar el dia de retorno*/
        $data = buscarFechaRegreso( $wusuario_solicitud, $wfec_i_periodo, $wfec_i, $wfec_f_periodo, $objSolicitud ,$diasDisfrutar, $diasDisfrutadosHoy );
        echo json_encode( $data );
    }

    return;
}

//=======================================================================================================//

  /***********************************************
   *      REPORTE Y SOLICITUD DE VACACIONES      *
   *              CONEX, FREE => OK              *
   ***********************************************/

    include_once("root/comun.php");
    // $conexunix = odbc_connect('nomina','informix','sco') or die("No se realizó Conexion con el Unix");
    $conexunix = odbc_connect('queryx7','','') or die("No se realizó Conexion con Oracle");

    $minimo_dias_vacaciones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'minimo_dias_vacaciones');
    $wbasedato              = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nomina');
    $wtalhuma               = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
    $WMAXIMO_DIAS           = 15;
    $WMINIMO_DIAS           = $minimo_dias_vacaciones;
    $arrayPeriodosRiesgo    = array();
    $haDisfrutadoVacaciones = false;

    //===============================================================================================================================================
    //ACA COMIENZA EL MAIN DEL PROGRAMA
    //===============================================================================================================================================

    //On
    // O J O **** O J O **** quitar comentario para dejar definitivo y quitar la sección de "prueba" que esta abajo
    $wcoduser=substr($user, -5);
    if(!isset($wusuario_solicitud) || empty($wusuario_solicitud))
    {
        $wusuario_solicitud  = $user_session;
    }

    encabezado("VACACIONES",$wactualiz, "clinica");

    $wusuario_solicitud_unix = substr($wusuario_solicitud, -5);
    $cod_use_Talhuma         = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $wusuario_solicitud);
    $wusuario_solicitud      = substr($wusuario_solicitud, -5);
    $usuario_coordinador     = validarUsuarioCoordinador($conex, $wemp_pmla, $wbasedato, $wtalhuma, $user_session);
    $aplicaVacaciones        = validarCargoUsuario( $wusuario_solicitud_unix );
    $coordinador_jefe        = buscarCoordinador( $cod_use_Talhuma );
    $nombreUsuarioConsultado =  consultarNombreUsuario( $cod_use_Talhuma );
    if( !$aplicaVacaciones ){
        echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                <br>
                <img src="../../images/medical/root/Advertencia.png"/>
                [?] El usuario tiene un tipo de contrato que no aplica vacaciones.
            </div>';
        return;
    }

    if($usuario_coordinador['codigo_coordinador'] != ''){
        $es_un_coordinador = "on";
    }

    if( isset($usuario_coordinador['diasMinimos']) and  trim($usuario_coordinador['diasMinimos']) != "" and $usuario_coordinador['directivo'] == "on" ){
        $WMINIMO_DIAS = $usuario_coordinador['diasMinimos'];
    }

    $query = " SELECT Dimnud, Dimfel, 1 as tipo
                 FROM {$wbasedato}_000016
                WHERE Dimusu = '{$wusuario_solicitud}'
                  AND Dimfel <= '".date('Y-m-d')."'
                  AND Dimest = 'on'
                UNION
               SELECT Dimnud, Dimfel, 2 as tipo
                 FROM {$wbasedato}_000016
                WHERE Dimusu = '{$wusuario_solicitud}'
                  AND Dimfel <= '0000-00-00'
                  AND Dimest = 'on'
                ORDER BY tipo desc, Dimfel desc
                LIMIT 1";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_assoc( $rs );
    if( $row['Dimnud']*1 > 0 ){
        $WMINIMO_DIAS = $row['Dimnud'];
    }

    $cedulaUsuario            = $usuario_coordinador['cedula'];
    $dia_despues              =  date("Y-m-d", strtotime(date('Y-m-d')."+1 day"));
    $wcentro_costoSolicitante = centroCostoEmpleado($conex, $wemp_pmla, $wbasedato, $wtalhuma, $user_session);
    $arr_solicitudesEnviadas  = periodosSolicitados($conex, $wemp_pmla, $wbasedato, $cod_use_Talhuma);
    //---> se consultan los periodos en que el empleado haya estado en riesgo
    $arrayPeriodosRiesgo      = buscarPeriodosEnRiesgo( $wusuario_solicitud );
    // Campos de las tablas en unix
    /*
        nopvadet:
            pvadetcod - 03636
            pvadetsec - 1
            pvadetfci - 2012-03-26
            pvadetfcf - 2013-03-25
            pvadetdia - 15.00
            pvadetcpc - 1

        nopva:
            pvacod - 03636
            pvacon - 0022
            pvafin - 2014-01-02
            pvaffi - 2014-01-20
            pvadia - 19.00
            pvaind - S
            pvahab - 15.00
            pvaval - 49325.86
            pvahre - 0.00
            pvavre - 0.00
            pvavpv - 0.00
            pvasec - 1
     */

     //Traigo el historial de periodos en riesgo del usuario

    //Traigo el historial de vacaciones de UNIX
    $q = "  SELECT pvadetfci, pvadetfcf, pvadetdia, pvafin, pvaffi, pvadia, pvadetddin
              FROM nopvadet, nopva, noper
             WHERE pvadetcod = '{$wusuario_solicitud_unix}'
               AND percod = pvadetcod
               AND pvadetfci >= perfin
               AND pvadetcod = pvacod
               AND pvadetsec = pvasec
             ORDER BY pvadetfci ";

    $res = @odbc_do($conexunix,$q);

    $i                = 1;
    $wfcumI           = "";
    $wfcumF           = "";
    $html_disfrutadas = '';
    $contcss          = 0;
    $wtotdias         = 0;
    $control          = 0;

    $pila_dias_pendientes      = array();
    $diasDisfrutadosPeriodo    = array();
    $liquidaciones             = 0;//--> cantidad de iteraciones, liquidaciones presentadas en pantalla
    $cambioPeriodo             = false;
    $fecha_anterior            = "";
    $fecha_ahora               = "";
    $diasLicenciaAnterior      = 0;
    $diasPendientesPorLiquidar = 0;
    while(odbc_fetch_row($res))
    {
        $pvadetfci = substr(odbc_result($res,1),0,10);
        $pvadetfcf = substr(odbc_result($res,2),0,10);

        $pvafin    = substr(odbc_result($res,4),0,10);
        $pvaffi    = substr(odbc_result($res,5),0,10);
        $haDisfrutadoVacaciones       = true;
        $diasDisfrutadosUltimoPeriodo = 0;
        $control ++;
        $wclass = ($contcss % 2 == 0) ? "fila1": "fila2";

        //--> 2015-09-10
        $aux               = diasDisponiblesPeriodo( $pvadetfci, $pvadetfcf, $cedulaUsuario, $arrayPeriodosRiesgo );//dias a los que se tiene derecho en el último periodo disfrutado
        //--> 2015-09-22
        $datosDiasLicencia = diasLicenciaNoRemunerada( $pvadetfci, $pvadetfcf, $wusuario_solicitud_unix );//mas uno porque se incluye el dia final
        $diasLicencia      = $datosDiasLicencia['diasLicencia'];//mas uno porque se incluye el dia final

        //-->2015-11-25
        if( $liquidaciones > 0 ){
            if( $fecha_anterior != $pvadetfci ){//--> si hubo cambio de fecha de inicio desde la iteración anterior a esta.
                if( $diasLicenciaAnterior*1 > 0 ){//--> si en la iteración anterior hubo dias de licencia no remunerada, pudo haber generado desplazamiento

                }else{//--> sino se desplazo el periodo a causa de licencias no remuneradas o suspenciones.
                    //--> en caso de que haya algo en la pila de dias pendientes, entonces se debe adicionar a lo que no se liquidó y debió liquidarse
                    if( count($pila_dias_pendientes) >0 ){
                        $diasPendientesPorLiquidar += $pila_dias_pendientes[count($pila_dias_pendientes)-1]['diasFaltantesPorDisfrutar'];
                        array_pop($pila_dias_pendientes);
                    }
                }
            }

        }
        $liquidaciones++;
        $fecha_anterior       = $pvadetfci;
        $diasLicenciaAnterior = $diasLicencia;

        $diasDisponibles   = 0;
        if( !isset( $diasDisfrutadosPeriodo[$pvadetfci."->".$pvadetfcf] ) ){
            $diasDisfrutadosPeriodo[$pvadetfci."->".$pvadetfcf] = 0;
        }
        foreach ($aux as $keyTipo => $dias ) {

            $diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
            $diasLicenciaPeriodo    = calcularDiasGanados( $keyTipo, $datosDiasLicencia['diasNoContados'][$keyTipo] );
            $diasDisponiblesPeriodo = $diasDisponiblesPeriodo - $diasLicenciaPeriodo;
            $diasDisponiblesPeriodo = round($diasDisponiblesPeriodo);
            $diasLicenciaPeriodo    = round($diasLicenciaPeriodo);
            $diasDisponibles +=  ( $diasDisponiblesPeriodo - $diasLicenciaPeriodo );
            $diasDisponibles = round( $diasDisponibles );
        }
        //--> 2015-09-10 <---

        $fecini_cumplido = $pvadetfci;
        $fecfin_cumplido = $pvadetfcf;

        $fecini_disfruta = $pvafin;
        $fecfin_disfruta = $pvaffi;
        $dias_total = ( (odbc_result($res,3)*1) + (odbc_result($res,7)*1) );
        $dias_totcal= ( (odbc_result($res,6)*1) + (odbc_result($res,7)*1) );

        $html_disfrutadas .= "
                        <tr align=center class=".$wclass.">
                            <td align=center>".$fecini_cumplido."</td>
                            <td align=center><---></td>
                            <td align=center>".$fecfin_cumplido."</td>
                            <td align=center>".$dias_total."</td>
                            <td align=center>".$fecini_disfruta."</td>
                            <td align=center><---></td>
                            <td align=center>".$fecfin_disfruta."</td>
                            <td align=center>".$dias_totcal."</td>
                        </tr>";


        //--> 2015-09-10
        $diasPendientesDisfrutar                                              =  $dias_total - $diasDisponibles;
        $diasDisfrutadosPeriodo[$pvadetfci."->".$pvadetfcf] += $dias_total;

        if( $diasPendientesDisfrutar < 0 ){
            if( count( $pila_dias_pendientes ) > 0 ){
                $diasDisfrutadosEnPeriodo = $dias_total;
                while( $diasPendientesDisfrutar < 0 and count( $pila_dias_pendientes ) > 0 ){

                    $diasAcumuladosDisfrutados = $pila_dias_pendientes[count($pila_dias_pendientes)-1]['diasDisfrutados'];
                    $diasDisfrutadosEnPeriodo  += $diasAcumuladosDisfrutados;
                    $diasPendientesDisfrutar   += $diasAcumuladosDisfrutados;
                    $diasFaltantesPorDisfrutar = $diasDisponibles - $diasDisfrutadosEnPeriodo;

                    $diasDisfrutadosPeriodo[$pvadetfci."->".$pvadetfcf] = $diasDisfrutadosEnPeriodo;

                    array_pop($pila_dias_pendientes);
                }

                if( $diasPendientesDisfrutar < 0 ){
                    $aux = array( 'periodo'=>$pvadetfci."  -->  ".$pvadetfcf, "diasDisfrutados"=>$diasDisfrutadosEnPeriodo, "diasFaltantesPorDisfrutar"=>$diasFaltantesPorDisfrutar );
                    array_push( $pila_dias_pendientes, $aux );
                }

            }else{

                $aux = array( 'periodo'=>$pvadetfci."  -->  ".$pvadetfcf, "diasDisfrutados"=>$dias_total );
                array_push( $pila_dias_pendientes, $aux );
            }

        }
        //--> 2015-09-10 <---
        $contcss++;
    } //fin while
    //---> estas son las últimas vacaciones disfrutadas ( de que periodo son )
    if( $haDisfrutadoVacaciones ){

        $wfec_i             = $pvadetfci;
        $wfec_f             = $pvadetfcf;

        $wdiasUltimoPeriodo = (diasEntreFechas($wfec_i, $wfec_f));

        $wtotdias           = $diasDisfrutadosPeriodo[$wfec_i."->".$wfec_f]; ////--> 2015-09-10 se pone como todal de dias los sumados en el acumulado anterior

        $datosDiasLicencia = diasLicenciaNoRemunerada( $wfec_i, $wfec_f, $wusuario_solicitud_unix );//mas uno porque se incluye el dia final
        $diasLicencia      = $datosDiasLicencia['diasLicencia'];//mas uno porque se incluye el dia final
        $auxDUP = $wdiasUltimoPeriodo;
        $auxDUP = $auxDUP  - $diasLicencia;
        if( ($wdiasUltimoPeriodo == 366 and $diasLicencia == 0 ) or ( $auxDUP == 366 and $diasLicencia > 0 ) ){//--> posiblemente sea un año bisiesto 2016-02-25
            $wdiasUltimoPeriodo = 365;
        }
        $wtotdiasSesgo      = $wdiasUltimoPeriodo - 365;
        if( ( $wtotdiasSesgo > 0 ) ){//--> 2016-01-18 esto se hace para que no se sume el doble de los dias de licencia, es decir, no acomodar la fecha si ya estaba liquidada incluyendo el movimiento de fechas en unix
            $diasLicencia = $diasLicencia - $wtotdiasSesgo;
        }

        $resdia  = $diasLicencia < 0  ? $diasLicencia .' day' : "+ " . $diasLicencia  . " day";

        $wfec_f  = date("Y-m-d",strtotime($wfec_f. $resdia)); //se mueve  la Fecha Final próximo período la cantidad de dias que se han dado de licencia

        $aux               = diasDisponiblesPeriodo( $wfec_i, $wfec_f, $cedulaUsuario, $arrayPeriodosRiesgo );//dias a los que se tiene derecho en el último periodo disfrutado
        $detalleDias       = "";
        $borrarTooltip     = false;
        $iteracion         = 0;

        $diasDisponibles = $diasPendientesPorLiquidar;
        foreach ($aux as $keyTipo => $dias ) {
            $diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
            $diasLicenciaPeriodo    = calcularDiasGanados( $keyTipo, $datosDiasLicencia['diasNoContados'][$keyTipo] );

            $diasDisponiblesPeriodo = round($diasDisponiblesPeriodo);
            $diasLicenciaPeriodo    = round($diasLicenciaPeriodo);

            $diasDisponibles +=  ( $diasDisponiblesPeriodo - $diasLicenciaPeriodo );
            $diasDisponibles = round( $diasDisponibles );
            $detalleDias .= "<div class='fila2'>";
            if( $keyTipo == "riesgo" ){
                if( $diasDisponiblesPeriodo == 0 ){
                    $borrarTooltip = true;
                }
                $detalleDias .= " <span class='subtituloPagina2'><font size='2'>Dias por Alto Riesgo: ".$diasDisponiblesPeriodo."</font></span><br>";
            }else{
                $detalleDias .= " <span class='subtituloPagina2'><font size='2'>Dias por Riesgo normal: ".$diasDisponiblesPeriodo."</font></span><br>";
            }
            if($iteracion == 1){
                $detalleDias .= " <span class='subtituloPagina2'><font size='2'>Dias disfrutados por este periodo: ".round($wtotdias)."</font></span><br>";
                if( $diasPendientesPorLiquidar >0 )
                    $detalleDias .= " <span class='subtituloPagina2'><font size='2'>Dias pendientes por liquidar periodos anteriores: ".round($diasPendientesPorLiquidar)."</font></span><br>";
            }
            $iteracion++;
            $detalleDias .= "</div>";
        }
        if( $borrarTooltip ){
            $detalleDias = "";
        }
        // ***********************************************************************************************************
        // ********************************** DÍAS - VACACIONES PENDIENTES DE DISFRUTAR EN EL ÚLTIMO PERIODO ******************************
        // ***********************************************************************************************************
        $contcss++;
        $wclass = ($contcss % 2 == 0) ? "fila1": "fila2";
        $html_pendientes_disfutar = "";
        $i=0;
        //===================================================================
        //DIAS PENDIENTES DE DISFRUTAR, DE UN PERIODO DISFRUTADO PARCIALMENTE
        //Si quedaron días pendientes de disfrutar aca los muestro


        if($wtotdias < $diasDisponibles && $wtotdias > 0)
        {

            $datosDiasLicencia        = diasLicenciaNoRemunerada( $wfec_i, $wfec_f, $wusuario_solicitud_unix );//mas uno porque se incluye el dia final

            $diasLicencia             = $datosDiasLicencia['diasLicencia'];//mas uno porque se incluye el dia final
            ( isset( $diasDisfrutadosPeriodo[$wfec_i."->".$wfec_f] ) ) ? $diasDisfrutadosHoy = $diasDisfrutadosPeriodo[$wfec_i."->".$wfec_f] : $diasDisfrutadosHoy = 0;

            $html_pendientes_disfutar .= fila_periodo_disponible($wclass, $wfec_i, $wfec_f, number_format(($diasDisponibles-$wtotdias),0), $i, $cod_use_Talhuma, $wcentro_costoSolicitante['codigo'], $arr_solicitudesEnviadas, "accordion_pendientes", "", $WMAXIMO_DIAS, $WMINIMO_DIAS, $diasLicencia, $detalleDias, number_format(($diasDisponibles-$wtotdias),0), $wfec_f, $diasDisfrutadosHoy );
        }
        //===================================================================
    }

    $query  = " SELECT perfin
                  FROM noper
                 WHERE percod = '{$wusuario_solicitud_unix}'";

    if( !$haDisfrutadoVacaciones ){

        $rs     = @odbc_do( $conexunix,$query );

        while ( odbc_fetch_row($rs) ){

            $fechaIngreso =  odbc_result($rs,1);
            $wfec_f =$fechaIngreso;
        }
    }

    // ****************************************************************************************************************************************
    // ********************************** EMPIEZO LA BUSQUEDA DE LOS PERIODOS QUE NO SE HAN EMPEZADO A DISFRUTAR ******************************
    // ****************************************************************************************************************************************

    //----> acá se miran cuantos periodos hay entre las fechas, y así se empiezan a recorrer buscando lo disfrutado*/
    $wfecproF  = $wfec_f;

    $wdias     = (diasEntreFechas($wfecproF, $wfecha));
    $wperiodos = ceil($wdias/365);

    //--> inicio cambios 2019-11-22
    //El deber de calcular los dias disponibles pasa a ser de SQL.
    $periodosDisponiblesPendientes = consultarperiodosDisponiblesPendientes( $wusuario_solicitud_unix );
    echo "<pre>".print_r( $periodosDisponiblesPendientes, true)."</pre>";
    $i = 1;
    foreach( $periodosDisponiblesPendientes as $i => $periodoPendiente ){

        $wclass                = ($i % 2 == 0) ? "fila2": "fila1";
        $wfecproI              = $periodoPendiente['wfecproI'];
        $wfecproF              = $periodoPendiente['wfecproF'];
        $wfecproM              = $periodoPendiente['wfecproM'];
        $diasDisponibles       = $periodoPendiente['diasDisponibles'];
        $diasDisponiblesReales = $diasDisponibles;
        $diasLicencia          = 0;
        $detalleDias           = "";
        $html_pendientes_disfutar .= fila_periodo_disponible($wclass, $wfecproI, $wfecproF, $diasDisponibles, $i, $cod_use_Talhuma, $wcentro_costoSolicitante['codigo'], $arr_solicitudesEnviadas, "accordion_pendientes", "", $WMAXIMO_DIAS, $WMINIMO_DIAS, $diasLicencia, $detalleDias, $diasDisponiblesReales, $wfecproM);
    }
    //--> fin cambios 2019-11-12


    /*for ( $i = 1; $i <= $wperiodos; $i++ ){

        $wclass        = ($i % 2 == 0) ? "fila1": "fila2";

        //--> se mueve hacia el siguiente periodo
        $wfecproI        = date("Y-m-d", strtotime($wfecproF."+1 day"));
        $wfecproF        = date("Y-m-d", strtotime($wfecproF."+1 year"));
        $wfecproM        = $wfecproF;

        if( diasEntreFechas($wfecproF, $wfecha) < 0 ){//--> fecha final no se ha cumplido, así que se pone igual al dia de hoy
            $wfecproF = $wfecha;
        }

        $datosDiasLicencia = diasLicenciaNoRemunerada( $wfecproI, $wfecproF, $wusuario_solicitud_unix );//mas uno porque se incluye el dia final
        $diasLicencia      = $datosDiasLicencia['diasLicencia'];//mas uno porque se incluye el dia final

        $resdia  = $diasLicencia < 0  ? $diasLicencia .' day' : "+ " . $diasLicencia  . " day";

        $wfecproF        = date("Y-m-d", strtotime($wfecproF.$resdia)); //se mueve  la Fecha Final próximo período la cantidad de dias que se han dado de licencia

        $wfecproM = date("Y-m-d", strtotime($wfecproM.$resdia));


        $diasDisponibles   = 0;
        $aux               = diasDisponiblesPeriodo( $wfecproI, $wfecproF, $cedulaUsuario, $arrayPeriodosRiesgo );//dias a los que se tiene derecho en el último periodo disfrutado

        $detalleDias           = "";
        $borrarTooltip         = false;
        $iteracion             = 0;
        $diasDisponiblesReales = 0;

        foreach ($aux as $keyTipo => $dias ) {
            $diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
            $diasLicenciaPeriodo    = calcularDiasGanados( $keyTipo, $datosDiasLicencia['diasNoContados'][$keyTipo] );

            $diasDisponiblesPeriodo = $diasDisponiblesPeriodo - $diasLicenciaPeriodo;
            $diasDisponiblesPeriodo = round($diasDisponiblesPeriodo);
            $diasLicenciaPeriodo    = round($diasLicenciaPeriodo);
            //$diasDisponibles       +=  $diasDisponiblesPeriodo;
            $diasDisponibles       +=  ( $diasDisponiblesPeriodo - $diasLicenciaPeriodo );
            $diasDisponiblesReales +=  ($diasDisponiblesPeriodo - $diasLicenciaPeriodo);
            ( $diasDisponibles*1 >= 5 ) ? $diasDisponibles = round( $diasDisponibles ) : $diasDisponibles = 0;
            $detalleDias .= "<div class='fila2'>";
            if( $keyTipo == "riesgo" ){
                if( $diasDisponiblesPeriodo == 0 ){
                    $borrarTooltip = true;
                }
                $detalleDias .= " <span class='subtituloPagina2'><font size='2'>Dias por Alto Riesgo: ".$diasDisponiblesPeriodo."</font></span><br>";
            }else{

                $detalleDias .= " <span class='subtituloPagina2'><font size='2'>Dias por Riesgo normal: ".$diasDisponiblesPeriodo."</font></span><br>";
            }
            if( $iteracion == 1 ){
                $detalleDias .= " <span class='subtituloPagina2'><font size='2'>Dias disfrutados por este periodo: 0 </font></span><br>";
            }
            $iteracion++;
            $detalleDias .= "</div>";
        }

        if( $borrarTooltip ){
            $detalleDias = "";
        }
        $html_pendientes_disfutar .= "<br>wclass: $wclass, wfecproI: $wfecproI, wfecproF: $wfecproF, diasDisponibles: $diasDisponibles, i: $i, cod_use_Talhuma: $cod_use_Talhuma, wcentro_costoSolicitante: {$wcentro_costoSolicitante['codigo']}, arr_solicitudesEnviadas: $arr_solicitudesEnviadas, WMAXIMO_DIAS: $WMAXIMO_DIAS, WMINIMO_DIAS: $WMINIMO_DIAS, diasLicencia: $diasLicencia, detalleDias: $detalleDias, diasDisponiblesReales: $diasDisponiblesReales, wfecproM: $wfecproM";
        $html_pendientes_disfutar .= fila_periodo_disponible($wclass, $wfecproI, $wfecproF, $diasDisponibles, $i, $cod_use_Talhuma, $wcentro_costoSolicitante['codigo'], $arr_solicitudesEnviadas, "accordion_pendientes", "", $WMAXIMO_DIAS, $WMINIMO_DIAS, $diasLicencia, $detalleDias, $diasDisponiblesReales, $wfecproM);
    }*/

    // ***********************************************************************************************************
    // ***************************** SOLICITUDES PENDIENTES DE LOS EMPLEADOS A CARGO *****************************
    // ***********************************************************************************************************
    $html_pendientes_aprobar = '';
    $contcss = 0;
    if($usuario_coordinador['codigo_coordinador'] != '')
    {
        $usuarios_solicitantes = array_keys($usuario_coordinador['lista_solicitantes']);
        $usuSolicitudesVencidas = array();
        // $impl = implode("','", $usuarios_solicitantes);
        // $sql = "SELECT *
        //         FROM    nomina_000012
        //         WHERE   Dvause IN ('{$impl}')
        //                 AND Dvaest = 'on'
        //                 AND Dvaerc <> 'RECHAZADO'
        //                 AND Dvaern <> 'RECHAZADO'
        //         GROUP BY Dvause, Dvapfi";
        foreach ($usuario_coordinador['lista_solicitantes'] as $wusuario_solicitud_aprobar => $arr_empleado)
        {
            //$wusuario_talhuma = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $wusuario_solicitud_aprobar);
            $wusuario_talhuma = $arr_empleado['cod_tahuma'];
            $arr_solicitudesEnviadasSubEmpleado = periodosSolicitadosPorAprobar($conex, $wemp_pmla, $wbasedato, $wusuario_talhuma, $wtalhuma, 'subempleado');

            foreach ($arr_solicitudesEnviadasSubEmpleado as $id_solicitud => $arr_solicitudEnviada)
            {

                $wclass = ($contcss % 2 == 0) ? "fila1": "fila2";

                $fecha_inicio_pendiente = $arr_solicitudEnviada['fecha_inicio_pendiente'];
                $fecha_fin_pendiente    = $arr_solicitudEnviada['fecha_fin_pendiente'];
                $dias_disponibles       = $arr_solicitudEnviada['dias_disponibles'];
                $estadoSolicitud        = $arr_solicitudEnviada['estadoSolicitud'];
                $idSolicitud            = $arr_solicitudEnviada['id_solicitud'];
                $fechaCreacionSolitud   = $arr_solicitudEnviada['fecha_Creacion_Solicitud'];

                $WMAXIMO_DIAS_emp = 15;
                $WMINIMO_DIAS_emp = $minimo_dias_vacaciones;

                $html_pendientes_aprobar .= fila_vacaciones_por_aprobar($wclass, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles, '', $wusuario_talhuma, $arr_solicitudEnviada, "accordion_aprobaciones", $arr_empleado, $es_un_coordinador, $WMAXIMO_DIAS_emp, $WMINIMO_DIAS_emp, $idSolicitud, $estadoSolicitud, $fechaCreacionSolitud);
                $contcss++;
            }
        }
    }


    $tituloVacionesDisfrutar = "HISTORIAL DE MIS VACACIONES DISFRUTADAS";
    if( $wconsultaExterna == "on" ){
        $tituloVacionesDisfrutar = " HISTORIAL VACACIONES DISFRUTADAS POR: {$nombreUsuarioConsultado}";
    }

    if(!isset($accion) and !isset($peticionAjax))
    {
        echo '<!DOCTYPE html>';
    }

    odbc_close($conexunix);
    odbc_close_all();
?>
<html lang="es-ES">
<head>
  <title>VACACIONES</title>
    <style type="text/css">
        .periodoDisfrutado{
            background-color: #3ADF00
        }
        .div_solicitudes_scroll{
            overflow-y: auto;
        }
        #tooltip{
            color: #2A5DB0;
            font-family: Arial,Helvetica,sans-serif;
            position:absolute;
            z-index:3000;
            border:1px solid #2A5DB0;
            background-color:#FFFFFF;
            padding:5px;
            opacity:1;}
        #tooltip div{margin:0; width:250px}
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librería para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
    <script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
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
    <script type="text/javascript">

        $(document).ready( function ()
        {
            var usuariosConSolicitudes = new Array();
            var colapsado              = true;
            var activo                 = 1;
            if( $("#wconsultaExterna").val() == "on" ){
                colapsado = false;
                active = 0;
                $("h3[aria-controls='disfrutadas']").click();
            }

            $("#accordion_disfrutadas").accordion({
                collapsible: colapsado,
                heightStyle: "content",
                active: activo
            });
            if( $("#wconsultaExterna").val() == "on" ){
                $("h3[aria-controls='disfrutadas']").click();
            }

            if( $("#consultaExterna").val() == "on" ){
                return;
            }

            $("#accordion_pendientes").accordion({
                collapsible: true,
                heightStyle: "content"
            });

            $("#accordion_aprobaciones").accordion({
                collapsible: true,
                heightStyle: "content",
                active: 0
            });

            habilitarInhabilitarFilas('accordion_pendientes');
            habilitarInhabilitarFilas('accordion_aprobaciones');
            $(".diasDisponibles").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
            $(".mensajeModificacion").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

            $("#filtrarSolicitudes").quicksearch("#tb_solicitudesPorAprobar tbody tr[tipo!='titulo']" );
            $("#filtrarSolicitudes").click(function(){
                $("input[checkbox][name='selectorEstadoSolicitudes']").attr("checked", false);
            });

            mensajeBloqueo = "<div class='fila2'><center><span class='subtituloPagina2'><font size='2'><img src='../../images/medical/root/Advertencia.png' height='15' width='15'>El usuario tiene vigentes otras solicitudes correspondientes a periodos laborales posteriores a este, <br> por lo tanto no se puede modificar la cantidad de dias solicitados en esta</font></span></center></div>";

            var solicitudes = $("#tb_solicitudesPorAprobar tr").length - 1;
            $("#tb_solicitudesPorAprobar").find("tr[tipo!='titulo']").each(function( i, obj ){
                usuarioActual = $(obj).attr("usuarioSolicitante");
                for( var j = i + 2; j <= solicitudes ; j++) {
                    usuarioPorRevisar = $( "#tb_solicitudesPorAprobar >tbody >tr").eq(j).attr("usuarioSolicitante")
                    if( usuarioActual == usuarioPorRevisar ){
                        $( obj ).find("input[type=number]").attr("disabled", true);
                        $( obj ).find("input[type=number]").attr("title", mensajeBloqueo);
                        $( obj ).find("input[type=number]").addClass("msg_bloqueo");
                        j = solicitudes;
                    }
                }
            });

            mensajeBloqueo = "<div class='fila2'><center><span class='subtituloPagina2'><font size='2'><img src='../../images/medical/root/Advertencia.png' height='15' width='15'>Esta solicitud no puede cambiar su estado, debido a que se encuentra vencida o hay una solicitud de un periodo anterior en este estado</font></span></center><div>";
            $(".coord_msg_bloqueo").attr("title", mensajeBloqueo );

            $(".msg_bloqueo:disabled, .coord_msg_bloqueo:disabled").after(function (e) {
                if( $( this ).prop("tagName") == "SELECT" ){
                    tipoBloqueado = "estado_coordinador";
                }else{
                    tipoBloqueado = "diasAsolicitar";
                }
                id = $(this).attr("id_solicitud");
                d = $("<div id='id_div_tooltip_"+tipoBloqueado+"_"+id+"'>");
                i = $(this);
                d.css({
                    height: i.outerHeight(),
                    width: i.outerWidth(),
                    position: "absolute",
                })
                d.css(i.offset());
                d.attr('title', i.attr("title"));
                i.attr("title","")
                d.tooltip({track: true, delay: 0, showURL: false, opacity: 0.97, left: -50 });
                return d;
            });

            $(".periodoDisfrutado").find("input_blq").attr("disabled", true);

        });

        function habilitarInhabilitarFilas(div_acordeon)
        {
            console.log("si está actualizando");
            // Se inactivan las filas con respuesta de coordinador o nómina
            // tr_solicitado_con_respuesta
            $("#"+div_acordeon).find(".tr_solicitado_con_respuesta").each(function(){
                $(this).find(".input_blq").attr("disabled","disabled");
            });

            // Se inactivan las filas sin solicitudes enviadas pero siempre debe quedar activa la primera para poder solicitarla
            // activo_no_solicitado
            var cont_no_solicitados = 0;
            var habilitarAnterior   = "";
            $("#"+div_acordeon).find(".solicitudesPendietes").each(function(){
                var id_tr            = $(this).attr("id");
                if(cont_no_solicitados > 0)
                {
                    if( habilitarAnterior == "off" ){
                        $("#"+id_tr).find(".input_blq").each(function( i, btn_evaluado ){
                                $( btn_evaluado ).attr("disabled","disabled");
                        });
                        $("#"+id_tr).find("input[type='number']").attr("disabled", "disabled");
                    }else{
                         $("#"+id_tr).find(".input_blq").each(function(){
                                $(this).removeAttr("disabled");
                        });
                         $("#"+id_tr).find("input[type='number']").removeAttr("disabled");
                    }
                    idHtml            = id_tr.substring( 3, id_tr.length );
                    if( habilitarAnterior != "off")
                        habilitarAnterior = $("#habilitarSiguiente_"+idHtml).val();
                }
                else
                {
                    $("#"+id_tr).find(".input_blq").each(function(){
                            $(this).removeAttr("disabled");
                    });
                     $("#"+id_tr).find("input[type='number']").removeAttr("disabled");


                    idHtml            = id_tr.substring( 3, id_tr.length );
                    if( habilitarAnterior != "off")
                        habilitarAnterior = $("#habilitarSiguiente_"+idHtml).val();

                }
                cont_no_solicitados++;
            });


            // Varias solicitudes enviadas sin respuesta solo se pueden ir cancelando de abajo hacia arriba es decir, el boton de cancelar
            // solo puede estar activo en la última solicitud enviada sin respuesta del coordinador ni de nómina
            // tr_solicitado_sin_respuesta
            var solicitados_sin_respuesta = $("#"+div_acordeon).find(".tr_solicitado_sin_respuesta").length;
            var cont_sin_solicitados = 1;
            $("#"+div_acordeon).find(".tr_solicitado_sin_respuesta").each(function(){
                var id_tr = $(this).attr("id");
                if(cont_sin_solicitados != solicitados_sin_respuesta)
                {
                    $("#"+id_tr).find("input[id^=btn_cancelar_]").attr("disabled","disabled");
                    $("#"+id_tr).find("input[id^=btn_actualizar_]").attr("disabled","disabled");
                    $("#"+id_tr).find("input[type='number']").attr("disabled", "disabled");
                }
                else
                {
                    $("#"+id_tr).find("input[id^=btn_cancelar_]").removeAttr("disabled");
                    $("#"+id_tr).find("input[id^=btn_actualizar_]").removeAttr("disabled");
                     $("#"+id_tr).find("input[type='number']").removeAttr("disabled");
                }
                cont_sin_solicitados++;
            });

            $("#"+div_acordeon).find(".solicitudesPendietes").each(function(){
                var solicitudVencida = $(this).attr("vencida");
                if( solicitudVencida == "on"  ){
                    if( $(this).find("input[type='button'][id^='btn_actualizar_']").is(":disabled") )
                        $(this).find("input[type='number']").attr("disabled", true);
                    $(this).find("input[type='button'][id^='btn_actualizar_']").removeAttr("disabled");
                }
            });

            // PARA SOLICITUDES QUE DEBE APROBAR EL COORDINADOR PARA SUS EMPLEADOS A CARGO

            // Se inactivan las filas con respuesta de coordinador o nómina
            // tr_solicitado_con_respuesta
            $("#"+div_acordeon).find(".solicitado_respuesta_nomina").each(function(){
                $(this).find(".input_blq").attr("disabled","disabled");
            });
        }

        $(function()
        {
            if( $("#consultaExterna").val() == "on" ){
                return;
            }
            $(".campo_fecha_min").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                minDate:$("#wdia_despues").val()
                , onSelect: function(dateText, inst ) {
                    console.log(inst);
                     var objDias    = $(inst).attr("input");
                     var porAprobar =  $(objDias).attr("porAprobar");
                    if(porAprobar == undefined ){
                        porAprobar = "";
                    }
                     consultarFechaRegreso( dateText, objDias, "fecha", porAprobar);
                     var boton  = $( objDias ).parent().parent().find("input[id^='btn_actualizar_']");
                     if( $(boton).is(":disabled") ){
                        $( objDias ).parent().parent().find("input[type='number']").attr("disabled", true);
                     }
                     $( boton ).attr("disabled",false);
                }
            });
             try{
                $("#fechaMesAtras").datepicker("destroy");
            }catch(e){

            }

            $("#fechaMesAtras").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                maxDate: "+0m +0w",
                onSelect: function(dateText, inst ) {
                    $("form").submit();
                }
            });
        });

        function url_ajax()
        {
            var wemp_pmla = $("#wemp_pmla").val();
            var wbasedato = $("#wbasedato").val();
            var wtalhuma = $("#wtalhuma").val();
            return "vacaciones.test.php?wemp_pmla="+wemp_pmla+"&wbasedato="+wbasedato+"&wtalhuma="+wtalhuma;
        }

        /**
         * [solicitarPeriodo: Función encargada de enviar los datos del periodo a solicitar para que sean guardados en la tabla de solicitudes]
         * @param  {[type]} idx_diferenciador [description]
         * @param  {[type]} codigo_empleado   [description]
         * @return {[type]}                   [description]
         */

        function solicitarPeriodo(dias_disponibles, idx_diferenciador, codigo_empleado, operacion, div_acordeon, es_un_coordinador, id_solicitud, tipoRegistro )
        {
            var diasDisponibles        = $("#wdias_disponibles_"+idx_diferenciador).val()*1;
            var dias_solicitados       = $("#wdias_disfrutar_"+idx_diferenciador).val()*1;
            var fecha_inicio_solicitud = $("#fecha_solicitud_inicio_"+idx_diferenciador).val();
            var fecha_fin_solicitud    = $("#fecha_solicitud_fin_"+idx_diferenciador).val();
            var minimo_dias            = $("#wdias_disfrutar_"+idx_diferenciador).attr('min')*1;
            var es_ok  = true;
            var fechaInicialPeriodo    = $("#fecha_inicial_periodo_"+idx_diferenciador).html();
            var fechaFinalPeriodo      = $("#fecha_final_periodo_"+idx_diferenciador).html();
            var guardarRechazo         = false;
            var date                   = new Date(fecha_inicio_solicitud);
            var dateper                = new Date(date.setDate(date.getDate() + 15));
            var fechacom               = dateper.getFullYear() + "-" + pad((dateper.getMonth() + 1),2) + "-" + pad((dateper.getDate()),2);

            if (dias_solicitados == '')
            {
                console.log(" falso en dias solicitados");
                es_ok = false;
            }

            if (fecha_inicio_solicitud == '')
            {
                console.log(" falso fecha_inicio_solicitud");
                es_ok = false;
            }

            if (fecha_fin_solicitud == '')
            {
                console.log(" falso fecha_fin_solicitud ");
                es_ok = false;
            }

            if (fechacom  < fechaFinalPeriodo)
            {
                alert("El periodo solicitado debe estar cumplido ");
                return;
            }

            if (dias_solicitados < minimo_dias)
            {
                if(!es_ok) { alert("El mínimo de días que puede solicitar para este periodo es: "+minimo_dias); }
                es_ok = false;
            }

            var arr_solicitudEnviada = $("#arr_solicitudEnviada_"+idx_diferenciador).val();

            if(es_ok)
            {
                $.post(url_ajax(),
                    {
                        consultaAjax            : '',
                        accion                  : 'insert',
                        form                    : 'solicitarVacaciones',
                        arr_solicitudEnviada    : arr_solicitudEnviada,
                        dias_solicitados        : dias_solicitados,
                        fecha_inicio_solicitud  : fecha_inicio_solicitud,
                        fecha_fin_solicitud     : fecha_fin_solicitud,
                        operacion               : operacion,
                        idx_diferenciador       : idx_diferenciador,
                        es_un_coordinador       : es_un_coordinador,
                        fechaInicialPeriodo     : fechaInicialPeriodo,
                        fechaFinalPeriodo       : fechaFinalPeriodo
                    },
                    function(data){
                        if(data.error == 1)
                        {
                            if( (tipoRegistro != 'porAprobar') || ( tipoRegistro == "porAprobar"  && ($("#estado_jefe_"+idx_diferenciador).val() == $("#estado_jefe_"+idx_diferenciador).attr("valorActual")) ))
                                alert(data.mensaje );
                            es_ok = false;
                        }
                        else
                        {
                            if( ( id_solicitud == "" && id_solicitud == undefined) || tipoRegistro != 'porAprobar' ){
                                alert(data.mensaje);
                                if( $("#tr_"+idx_diferenciador).attr("vencida") == "on" && ( $("#tr_"+idx_diferenciador).find("input[name^='fecha_solicitud_inicio_']").attr("fechaAprobadaActual") != $("#tr_"+idx_diferenciador).find("input[name^='fecha_solicitud_inicio_']").val() ) ){//--> se está modificando una solicitud vencida propia

                                    $("#tr_"+idx_diferenciador).attr("vencida", "off");
                                    $("#tr_"+idx_diferenciador).find("td[name='td_respuesta_coordinador'][sinRespuesta='on']").html("Pendiente");
                                    $("#tr_"+idx_diferenciador).find("td[name='td_respuesta_coordinador']").removeClass("fondorojo");
                                    $("#tr_"+idx_diferenciador).find("td[name='td_respuesta_nomina'][sinRespuesta='on']").html("Pendiente");
                                    $("#tr_"+idx_diferenciador).find("td[name='td_respuesta_nomina']").removeClass("fondorojo");
                                }
                            }else{
                                if( $("#estado_jefe_"+idx_diferenciador).val() == $("#estado_jefe_"+idx_diferenciador).attr("valorActual") ){
                                    alert(data.mensaje);
                                    usuarioSolicitante = $("#tr_"+idx_diferenciador).attr("usuarioSolicitante");
                                    vencidaIndividual  = $("#tr_"+idx_diferenciador).attr("vencida");//--> esta solicitud estaba vencida
                                    if( vencidaIndividual == "on" && ( $("#tr_"+idx_diferenciador).find("input[name^='fecha_solicitud_inicio_']").attr("fechaAprobadaActual") != $("#tr_"+idx_diferenciador).find("input[name^='fecha_solicitud_inicio_']").val() ) ){//--> si estaba vencida, miramos cuales solicitudes estaban vencidas por su culpa y las habilitamos para respuesta del coordinador
                                        $("#tr_"+idx_diferenciador).attr("vencida", "off");
                                        $("#tr_"+idx_diferenciador).find("select").attr("disabled", false);
                                        $("#id_div_tooltip_estado_coordinador_"+id_solicitud).tooltip("destroy");
                                        $("#id_div_tooltip_estado_coordinador_"+id_solicitud).remove();
                                        $("#tr_"+idx_diferenciador).find("td[name='respuesta_coordinador']").removeClass("fondorojo");
                                        $("#tr_"+idx_diferenciador).find("td[name='respuesta_coordinador']").tooltip("destroy");
                                        $("#tr_"+idx_diferenciador).find("td[name='respuesta_coordinador']").html("");


                                        $("#tb_solicitudesPorAprobar > tbody > tr[tipo!='titulo'][usuarioSolicitante='"+usuarioSolicitante+"']").each(function( i, tr_usuario ){
                                            if( $(tr_usuario).attr("bloqueadaPorVencimientoAjeno") == "on" ){
                                                $(tr_usuario).attr("bloqueadaPorVencimientoAjeno", "off");
                                                $(tr_usuario).find("select").attr("disabled", false);
                                                $(tr_usuario).find("td[name='respuesta_coordinador']").removeClass("fondorojo");
                                                $(tr_usuario).find("td[name='respuesta_coordinador']").html("");
                                                $("#id_div_tooltip_estado_coordinador_"+$(tr_usuario).find("select").attr("id_solicitud")).tooltip("destroy");
                                                $("#id_div_tooltip_estado_coordinador_"+$(tr_usuario).find("select").attr("id_solicitud")).remove();
                                            }
                                            if( $(tr_usuario).attr("vencida") == "on" ){
                                                return false;
                                            }
                                        });
                                    }
                                }
                            }
                            $("#btn_solicitar_"+idx_diferenciador).hide();
                            $("#btn_actualizar_"+idx_diferenciador).show();
                            $("#btn_cancelar_"+idx_diferenciador).show();
                            $("#arr_solicitudEnviada_"+idx_diferenciador).val(data.arr_solicitudEnviada);

                            $("#tr_"+idx_diferenciador).removeClass("activo_no_solicitado");

                            $("#tr_"+idx_diferenciador).removeClass("tr_solicitado_sin_respuesta");
                            $("#tr_"+idx_diferenciador).addClass("tr_solicitado_sin_respuesta");
                            $("#id_solicitud_"+idx_diferenciador).html(data.id_solicitud);
                            $("#btn_actualizar_"+idx_diferenciador).attr("disabled", true);
                        }
                    },
                    "json"
                ).done(function(){
                    // Se ativan o inactivan las filas de las solicitudes del empleado dependiendo
                    // de la cantidad de solicitudes, solo puede pedir de a un solo periodo y tambien solo puede llegar
                    // a cancelar de a un solo periodo al tiempo, a medica que solicita o inactiva, asi mismo se irán habilitando
                    // o no las demás filas.
                    var cambioRespuestaCoordinador = false;
                    if( $("#estado_jefe_"+idx_diferenciador).val() != $("#estado_jefe_"+idx_diferenciador).attr("valorActual") ){
                        cambioRespuestaCoordinador = true;
                    }

                    //if( ( es_ok || tipoRegistro == "porAprobar" )  || guardarRechazo ){
                    if( ( tipoRegistro == "porAprobar" && cambioRespuestaCoordinador ) || es_ok ){

                        if( id_solicitud != "" && id_solicitud != undefined  && tipoRegistro == 'porAprobar' && cambioRespuestaCoordinador ){
                            responderSolicitud(idx_diferenciador, id_solicitud, div_acordeon, es_un_coordinador, es_ok, tipoRegistro, guardarRechazo );
                            //location.reload();
                        }

                        if( dias_solicitados == diasDisponibles ){
                             $("#habilitarSiguiente_"+idx_diferenciador).val("on");
                        }else{
                            $("#habilitarSiguiente_"+idx_diferenciador).val("off");
                        }
                            habilitarInhabilitarFilas(div_acordeon);
                    }
                });
            }
            else
            {

                alert("Faltan campos por llenar necesarios para la solicitud de este periodo de vacaciones");
            }
        }

        function pad(input, length) {
            return Array(length - Math.floor(Math.log10(input))).join('0') + input;
        }


        function cancelarPeriodo(idx_diferenciador, codigo_empleado, operacion, div_acordeon, es_un_coordinador)
        {
            if(confirm("Va a cancelar su solicitud de vacaciones\n\n¿Desea cancelarlo realmente?"))
            {
                var arr_solicitudEnviada = $("#arr_solicitudEnviada_"+idx_diferenciador).val();
                $.post(url_ajax(),
                    {
                        consultaAjax         : '',
                        accion               : 'update',
                        form                 : 'cancelarVacaciones',
                        arr_solicitudEnviada : arr_solicitudEnviada,
                        es_un_coordinador    : es_un_coordinador
                    },
                    function(data){
                        if(data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            alert(data.mensaje);
                            location.reload();
                        }
                    },
                    "json"
                ).done(function(){
                    //
                });
            }
        }

        function responderSolicitud(idx_diferenciador, id_solicitud,div_acordeon, es_un_coordinador, es_ok, tipoRegistro, guardarRechazo)
        {
            var arr_solicitudEnviada = $("#arr_solicitudEnviada_"+idx_diferenciador).val();
            var estado_jefe          = $("#estado_jefe_"+idx_diferenciador).val();
            var fechaInicialPeriodo  = $("#fecha_inicial_periodo_"+idx_diferenciador).html();
            var fechaFinalPeriodo    = $("#fecha_final_periodo_"+idx_diferenciador).html();

            if(estado_jefe != '')
            {
                $.ajax({
                    url  : url_ajax(),
                    type : "post",
                    async: false,
                    data:
                    {
                        consultaAjax         : '',
                        accion               : 'update',
                        form                 : 'respuestaJefe',
                        arr_solicitudEnviada : arr_solicitudEnviada,
                        id_solicitud         : id_solicitud,
                        respuesta_coordinador: estado_jefe,
                        fechaInicialPeriodo  : fechaInicialPeriodo,
                        fechaFinalPeriodo    : fechaFinalPeriodo
                    },
                    success : function(data){
                        if(data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            if(data.error == 2){
                                alert(data.mensaje);
                                $("#estado_jefe_"+idx_diferenciador).find("option[value='']").attr("selected", true);
                            }else{
                                alert(data.mensaje);
                                $("#arr_solicitudEnviada_"+idx_diferenciador).val(data.arr_solicitudEnviada);
                                if( estado_jefe == "RECHAZADO"){
                                    location.reload();
                                }else{
                                    $("#estado_jefe_"+idx_diferenciador).attr("valorActual", "APROBADO");
                                    $("#estado_jefe_"+idx_diferenciador).parent().parent().attr("aprobadacoordinador", "on");
                                    $("#estado_jefe_"+idx_diferenciador).parent().parent().attr("sinRevisar", "off");
                                }
                            }
                        }
                    },
                    dataType : "json"
                });
            }
            else
            {
                alert("Debe elegir un estado");
            }
        }

        function cerrarVentana()
        {
            window.close();
        }

        function validarValor(obj){
            var val = $(obj).val();
            var max = $(obj).attr("max");
            var min = $(obj).attr("min");
            if( val*1 > max*1 ){
               $(obj).val( max );
               return;
            }

            if( val*1 < min*1 ){
               $(obj).val( min );
            }
        }

        function soloNumeros(evt, obj) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            // alert(charCode);
             if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36 || charCode == 46 || charCode == 44) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
                return false;
             return true;
        }

        function consultarFechaRegreso( fechaInicio, obj, objSolicitud, porAprobar ){
            //se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
            var rango_superior      = 245;
            var rango_inferior      = 11;
            var aleatorio           = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
            var padre               = obj.parent();
            var diasAdisfrutar      = $( padre ).next("td").find("input").val();//2015-03-26
            var diasDisfrutadosHoy  = $( padre ).find("input[type='hidden']").val();//2015-03-26
            var fechaFinalPeriodo   = $( obj ).attr( "fechafinalpendiente" );
            var fechaInicialPeriodo = $( obj ).attr( "fechainiciopendiente" );

            $.ajax({
                url     : url_ajax(),
                type    : "POST",
                async   : true,
                data    : {
                            peticion          : "buscarFechaRegreso",
                            consultaAjax      : "si",
                            wfec_i            : fechaInicio,
                            wfec_f_periodo    : fechaFinalPeriodo,
                            wfec_i_periodo    : fechaInicialPeriodo,
                            aleatorio         : aleatorio,
                            diasDisfrutar     : diasAdisfrutar,
                            diasDisfrutadosHoy: diasDisfrutadosHoy,
                            objSolicitud      : objSolicitud,
                            wusuario_solicitud: $("#wcod_nomina_usu").val(),
                            porAprobar        : porAprobar

                          },
                success : function(data){
                            if( data.error == "1" ){
                                alerta(data.mensaje);
                                $(obj).val( $(obj).attr("fechaAprobadaActual") );
                            }else{
                                //$(padre).next("td").find("input").val( data.mensaje );

                                if( objSolicitud == "fecha" ){
                                    var maximo =  $(padre).next("td").find("input").attr("max");//--> acá va el maximo para ese periodo del usuario---> pilas ps
                                    var minimo = $(padre).next("td").find("input").attr("min");//--> acá va el mínimo del usuario---> pilas ps
                                    if( diasDisfrutadosHoy*1 > 0 && data.diasDisfrutar*1 == maximo*1 ){
                                        minimo = data.diasDisfrutar*1;
                                    }
                                    if( data.diasDisfrutar*1 < minimo ){
                                        alerta( "Dias a disfrutar mínimos insuficientes. Debe tener mínimo "+ minimo +" dias disponibles" );
                                        $(obj).val( $(obj).attr("fechaAprobadaActual") );//2015-03-26
                                            return;
                                    }
                                    // minimo = data.diasDisfrutar;

                                    $(padre).next("td").find("input").attr("step", "1");
                                    $(padre).next("td").find("input").attr("min", minimo );

                                    if( data.diasDisfrutar*1 >= maximo ){
                                        $(padre).next("td").find("input").attr("max", data.diasDisfrutar );
                                    }else{

                                        ///-> verificar si es el periodo en curso
                                        if( $(padre).next("td").find("input").attr("periodoEnCurso") == "on" ){
                                            if( data.diasDisfrutar > $("#minimo_dias_vacaciones").val()*1 ){
                                                $(padre).next("td").find("input").attr("max", data.diasDisfrutar );
                                            }else{
                                                $(padre).next("td").find("input").attr("max",  $("#minimo_dias_vacaciones").val()*1 );
                                            }

                                        }
                                    }
                                    $(padre).next("td").find("input").val( data.diasDisfrutar );

                                }

                                $(padre).next("td").next("td").find("input").val( data.mensaje );//2015-03-26
                            }
                          },
                dataType: "json"
            });
        }

        function calcularPeriodoFechas( obj ){
            //var fecha_i = $(obj).parent().next("td").find("input");//2015-03-26
            if( $(obj).attr("type") == "number" ){
                validarValor( obj )
                var porAprobar =  $(obj).attr("porAprobar");
                if(porAprobar == undefined ){
                    porAprobar = "";
                }
            }
            var fecha_i = $(obj).parent().prev("td").find("input");
            if( $(fecha_i).val() != "" ){
                consultarFechaRegreso( $(fecha_i).val(), fecha_i, "dias", porAprobar );
            }else{
                return;
            }
        }

        function alerta( txt ){
            $("#textoAlerta").text( txt );
            $.blockUI({ message: $('#msjAlerta') });
                setTimeout( function(){
                                $.unblockUI();
                            }, 2500 );
        }

        function abrirReporteVacaciones( codigoCoordinador ){
            window.open ("../reportes/rep_vacaciones.php?wemp_pmla=01&coordinador="+codigoCoordinador );
        }

        function filtrarSolicitudesParametro(obj){
            var i = 0;
            condicion = "";
            $("filtrarSolicitudes").val("");
            checkedos =  $("[name='selectorEstadoSolicitudes']:checked").length;
            $("[name='selectorEstadoSolicitudes']:checked").each(function(){
                i++;
                var auxiliar = $(this).val();
                condicion += "["+auxiliar+"='on']";

            });
            $("#filtrarSolicitudes").val("");
            $("#filtrarSolicitudesNumId").val("");
            if( i > 0 ){
                $(".tr_solicitudes_aprobar").hide();
                $(".tr_solicitudes_aprobar"+condicion).show();
            }else{
                $(".tr_solicitudes_aprobar").show();
            }
            if( checkedos > 0){
                $("#filtrarSolicitudes").val("");
                $("#filtrarSolicitudes").attr("disabled", true);
            }else{
                $("#filtrarSolicitudes").val("");
                $("#filtrarSolicitudes").attr("disabled", false);
            }
        }

        function habilitarActualizar( obj ){
            if( $(obj).prop('tagName') == "SELECT" ){
                if( $(obj).val() != "" ){
                    //$( obj ).parent().parent().parent().parent().parent().parent().find("input[id^='btn_actualizar_']").click();
                }
                //var boton  = $( obj ).parent().parent().parent().parent().parent().parent().find("input[id^='btn_actualizar_']").attr("disabled",false);
                var boton  = $( obj ).parent().parent().find("input[id^='btn_actualizar_']").attr("disabled",false);

            }else{
                //var boton  = $( obj ).parent().parent().find("input[id^='btn_actualizar_']").attr("disabled",false);
                var boton  = $( obj ).parent().parent().find("input[id^='btn_actualizar_']").attr("disabled",false);
            }
        }

        function notificarBloqueo( obj ){
            if( $(obj).find("input[type='number']").is(":disabled") && $(obj).attr("notificado") == "off" ){
                alerta( "El usuario tiene periodos posteriores solicitados, \n por lo tanto no se puede modificar la cantidad de dias solicitados" );
                $(obj).attr("notificado", "on");
            }else{
                return;
            }
        }

        function notificarBloqueoEstadoJefe( obj ){
            if( $(obj).find("select").is(":disabled") && $(obj).attr("notificado") == "off" ){
                alerta( " Esta solicitud está vencida o Este usuario tiene solicitudes previas vencidas, favor modificar la fecha de inicio " );
                $(obj).attr("notificado", "on");
            }else{
                return;
            }
        }
    </script>

    <style type="text/css">
        .div_alinear{
            margin-left: 10px;
        }
    </style>
</head>
<body>

    <!-- //Mensaje de espera -->
    <div id='msjEspere' style='display:none;'>
        <br>
        <img src='../../images/medical/ajax-loader5.gif'/>
        <br><br> Por favor espere un momento ... <br><br>
    </div>

    <script>
    $.blockUI({ message: $('#msjEspere') });
    </script>

    <!-- //FORMA ================================================================ -->
    <form name='vacaciones' action='vacaciones.test.php' method=post >

        <input type='hidden' name='wemp_pmla' id='wemp_pmla' value='<?=$wemp_pmla?>'>
        <input type='hidden' name='wbasedato' id='wbasedato' value='<?=$wbasedato?>'>
        <input type='hidden' name='wtalhuma' id='wtalhuma' value='<?=$wtalhuma?>'>
        <input type='hidden' name='minimo_dias_vacaciones' id='minimo_dias_vacaciones' value='<?=$minimo_dias_vacaciones?>'>
        <input type='hidden' name='wcod_nomina_usu' id='wcod_nomina_usu' value='<?=$wcoduser?>'>
        <input type='hidden' name='wdia_despues' id='wdia_despues' value='<?=$dia_despues?>'>
        <input type='hidden' name='wdia_hoy' id='wdia_hoy' value='<?=date('Y-m-d')?>'>

        <!-- ////////prueba ////// -->
        <?php if(isset($admin) && $admin != ''){ ?>
        <table align="center">
            <tr class="fila1"><td>C&oacute;digo: <input name='wusuario_solicitud' id='wusuario_solicitud'></td></tr>
            <tr><td class="boton" align='center'><input type='submit' value='Buscar'></td></tr>
        </table>
        <?php } ?>

        <!-- ///////////////////// -->
        <div width='' id='accordion_disfrutadas' style="text-align:left;" class="div_alinear">
            <h3><?=$tituloVacionesDisfrutar?></h3>
            <div align=center id="disfrutadas" style='display:'>
                <table>
                    <tr align="center" class="encabezadoTabla">
                        <td colspan="3" align="center">Per&iacute;odo Cumplido</td>
                        <td align="center">D&iacute;as Causados disfrutados</td>
                        <td colspan="3" align="center">Fecha disfrutado</td>
                        <td align="center">D&iacute;as Calendario<br>Disfrutados</td>
                        <?=$html_disfrutadas?>
                    </tr>
                </table>
            </div>
        </div>

        <!-- si se está consultando el historial de un empleado -->
        <?php
            if( $wconsultaExterna == "on" ){
                echo "<input type='hidden' name='wconsultaExterna' id='wconsultaExterna' value='{$wconsultaExterna}'>";
                echo "</form>";
                echo "<script>
                        $.unblockUI();
                      </script>";
                return;
            }
        ?>

        <br><br>
        <div width='' id='accordion_pendientes' style="text-align:left;" class="div_alinear">
            <h3>MIS VACACIONES PENDIENTES DE DISFRUTAR</h3>
            <div align="center" id="pendientes" style='display:'>
                <br>
                <span class='subtituloPagina2'><font size='3'>Sus Solicitudes deben ser aprobadas por:<?=$coordinador_jefe?> </font><span>
                <br>
                <table>
                    <tr class="encabezadoTabla">
                        <td colspan="13" style="text-align:left;">
                            Su centro de costos actual: <?=$wcentro_costoSolicitante['codigo'].'-'.$wcentro_costoSolicitante['nombre']?>
                        </td>
                    </tr>
                    <tr align="center" class="encabezadoTabla">
                        <td colspan="3" align="center">Per&iacute;odo Cumplido</td>
                        <td align="center">D&iacute;as pendientes<br> Causados </td>
                        <td align="center">Fecha de Inicio a  disfrutar</td>
                        <td align="center">D&iacute;as Solicitados</td>
                        <td align="center">Fecha Final a  disfrutar</td>
                        <td align="center">Respuesta<br>coordinador</td>
                        <td align="center">Respuesta<br>n&oacute;mina</td>
                        <td align="center">Solicitar<br>Vacaciones</td>
                        <td align="center">N&uacute;mero<br>Solicitud</td>
                        <td align="center">Fecha <br>Creaci&oacute;n</td>
                    </tr>
                    <?=$html_pendientes_disfutar?>
                </table>
            </div>
        </div>

        <?php
        if($usuario_coordinador['codigo_coordinador'] != '')
        {
        ?>
        <br><br>
            <div width='' id='accordion_aprobaciones' style="text-align:left;" class="div_alinear">
                <h3>SOLICITUDES DE VACACIONES POR APROBAR DE PERSONAL A CARGO</h3>
            <?php
                if( trim($html_pendientes_aprobar) != "" ){
            ?>
                <div align="center" id="pendientes_otros_empleados" class='div_solicitudes_scroll' style='height:400px;'>
                    <br>
                    <table style='width:90%;'>
                        <tr>
                            <td colspan='10' align='center' > <CENTER><span class='subtituloPagina2'><font size='3'>filtrar Solicitudes </font><span></center></td>
                        </tr>
                        <tr style='font-size:10px;'>
                            <td> <input type='checkbox' name='selectorEstadoSolicitudes' value='sinRevisar' onclick='filtrarSolicitudesParametro(this)'>Sin revisar </td>
                            <td> <input type='checkbox' name='selectorEstadoSolicitudes' value='rechazadacoordinador' onclick='filtrarSolicitudesParametro(this)'> Rechazada por Coordinador </td>
                            <td> <input type='checkbox' name='selectorEstadoSolicitudes' value='aprobadacoordinador' onclick='filtrarSolicitudesParametro(this)'> Aprobadas por Coordinador </td>
                            <td> <input type='checkbox' name='selectorEstadoSolicitudes' value='rechazadanomina' onclick='filtrarSolicitudesParametro(this)'> Rechazada por Nomina </td>
                            <td> <input type='checkbox' name='selectorEstadoSolicitudes' value='aprobadanomina' onclick='filtrarSolicitudesParametro(this)'> Aprobadas por Nomina </td>
                            <td> <input type='checkbox' name='selectorEstadoSolicitudes' value='vencida' onclick='filtrarSolicitudesParametro(this)'> Vencidas </td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align='right'> Ver solicitudes creadas desde: <input id='fechaMesAtras' name='fechaMesAtras' value='<?php echo $fechaMesAtras; ?>'> </td>
                        <tr>
                        <tr style='font-size:10px;'>
                            <td colspan='9' nowrap='nowrap'>
                                Nombre Subalterno:<input type='text' size='30' name='filtrarSolicitudes' id='filtrarSolicitudes' value=''>&nbsp;&nbsp;
                                <!--# solicitud:<input type='text' size='4' name='filtrarSolicitudesNumId' id='filtrarSolicitudesNumId' value=''>-->
                            </td>
                        <tr>
                    </table>
                    <table id='tb_solicitudesPorAprobar'>
                        <tr align="center" class="encabezadoTabla" tipo='titulo'>
                            <td colspan="" align="center">Nombres</td>
                            <td colspan="3" align="center">Per&iacute;odo Cumplido</td>
                            <td align="center">D&iacute;as Causados</td>
                            <td align="center">Fecha de Inicio a  Disfrutar</td>
                            <td align="center">D&iacute;as Solicitados</td>
                            <td align="center">Fecha Final a  Disfrutar</td>
                            <td align="center">Estado</td>
                            <td align="center">&nbsp;</td>
                            <td align="center">Respuesta<br>n&oacute;mina</td>
                            <td align="center">Num. <br>solicitud</td>
                            <td align="center">Fecha <br>Solicitud</td>
                        </tr>
                        <?=$html_pendientes_aprobar?>
                    </table>
                </div>
                <?php
                    }else{
                        echo "<CENTER><span class='subtituloPagina2'><font size='3'>* SIN SOLICITUDES PENDIENTES POR APROBAR*</font></span><CENTER><br>";
                    }
                ?>
            </div><br><br>

            <div><span class='subtituloPagina2' style='cursor:pointer;' onclick='abrirReporteVacaciones("<?=$usuario_coordinador['codigo_coordinador'];?>");'><font size='3'>Consultar Vacaciones sin liquidar de Personal a cargo</font></span><CENTER></div>
        <?php
        }
        ?>

            <br><br>
            <table align="center">
                <tr class="boton"><td align="center"><input type="button" value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>
            </table>
            <div id='msjAlerta' style='display:none;'>
                <br>
                <img src='../../images/medical/root/Advertencia.png'/>
                <br><br><div id='textoAlerta'></div><br><br>
            </div>
    </form>

    <script>
    $.unblockUI();
    </script>
<?php include_once("free.php"); ?>
</body>
</html>
<!--
drop table nomina_000012;
CREATE TABLE IF NOT EXISTS nomina_000012 (
    Medico VARCHAR(8) NOT NULL,
    Fecha_data DATE NOT NULL,
    Hora_data TIME NOT NULL,
    Dvause VARCHAR(80) NOT NULL,
    Dvapfi DATE NOT NULL,
    Dvapff DATE NOT NULL,
    Dvadpe INT(3) NOT NULL,
    Dvadso INT(3) NOT NULL,
    Dvafid DATE NOT NULL,
    Dvaffd DATE NOT NULL,
    Dvafmd DATE,
    Dvalog TEXT,
    Dvacco VARCHAR(80) NOT NULL,
    Dvaerc VARCHAR(20) DEFAULT '',
    Dvafrc DATE,
    Dvahrc TIME,
    Dvacdc VARCHAR(80) DEFAULT '',
    Dvaern VARCHAR(20) DEFAULT '',
    Dvanfr date,
    Dvanhr time,
    Dvacun VARCHAR(80) DEFAULT '',
    Dvadnm TEXT,
    Dvaest VARCHAR(3) DEFAULT 'on',
    Seguridad VARCHAR(10) DEFAULT '',
    id bigint(20) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    INDEX idx_dvausepfipffdpe(Dvause, Dvapfi, Dvapff, Dvadpe)
);

DELETE FROM det_formulario WHERE medico = 'nomina' AND codigo = '000012';
DELETE FROM root_000030 WHERE Dic_Usuario = 'nomina' AND Dic_Formulario = '000012';

/*DET_FORMULARIO - campos*/
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0001', 'Dvause', '0', 1, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0002', 'Dvapfi', '3', 2, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0003', 'Dvapff', '3', 3, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0004', 'Dvadpe', '1', 4, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0005', 'Dvadso', '1', 5, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0006', 'Dvafid', '3', 6, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0007', 'Dvaffd', '3', 7, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0008', 'Dvafmd', '3', 8, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0009', 'Dvalog', '4', 9, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0010', 'Dvacco', '0', 10, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0011', 'Dvaerc', '0', 11, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0012', 'Dvafrc', '3', 12, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0013', 'Dvahrc', '11', 13, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0014', 'Dvacdc', '0', 14, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0015', 'Dvaern', '0', 15, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0016', 'Dvanfr', '3', 16, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0017', 'Dvanhr', '11', 17, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0018', 'Dvacun', '0', 18, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0019', 'Dvadnm', '4', 19, '', 'A');
INSERT INTO `det_formulario` (`medico`, `codigo`, `campo`, `descripcion`, `tipo`, `posicion`,`comentarios`, `activo`) VALUES ('nomina', '000012', '0020', 'Dvaest', '10', 20, '', 'A');


/* DICCIONARIO DE DATOS */
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0001', 'Código empleado', 'Código del empleado que esta haciendo la solicitud', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0002', 'Fecha inicio disponible', 'Fecha de inicio de un periodo disponible', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0003', 'Fecha final disponible', 'Fecha final del periodo diponible', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0004', 'Días disponibles', 'Total de días disponibles dentro de las fechas del periodo disponible pendiente por disfrutar', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0005', 'Días solicitados', 'Días solicitados de vacaciones por el empleado dentro de los días disponibles', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0006', 'Fecha inicio vacaciones', 'Fecha inicial para el difrute de las vacaciones', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0007', 'Fecha final vacaciones', 'Fecha final para el disfrute de las vacaciones', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0008', 'Fecha de modificación vacaciones', 'Fecha última modificación periodo y días de difrute', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0009', 'Log de cambios', 'Log donde se registran los cambios a las fechas de disfrute y aprobaciones', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0010', 'Centro de costo empleado', 'Código del centro de costos de empleado solicitante', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0011', 'Estado respuesta del coordinador', 'Estado de respuesta del jefe, aprueba o desaprueba la solicitud de vacaciones', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0012', 'Fecha respuesta del coordinador', 'Fecha en que el jefe responde la solicitud', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0013', 'Hora respuesta coordinador', 'Hora en que el jefe responde la solicitud', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0014', 'Código del coordinador', 'Código del coordinador que responde la solicitud', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0015', 'Estado respuesta de nómina', 'Estado de la respuesta de nómina, aprueba o rechaza la solicitud', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0016', 'Fecha respuesta nómina', 'Fecha en que nómina responde la solicitud, aprueba o rechaza', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0017', 'Hora respuesta nómina', 'Hora en que nómina responde la solicitud aprobando o rechazando', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0018', 'Código funcionario de nómina', 'Código del funcionario de nómina que responde la solicitud', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0019', 'Descripción', 'Descripción o comentario que puede hacer el funcionario de nómina', 'C-root');
INSERT INTO root_000030 ( Medico , Fecha_data , Hora_data , Dic_Usuario , Dic_Formulario , Dic_Campo , Dic_Descripcion , Dic_Comentario , Seguridad)
VALUES                  ('root', '2015-04-23', '10:21:30', 'nomina', '000012', '0020', 'Estado', '', 'C-root');


INSERT INTO `root_000051` (`Medico`, `Fecha_data`, `Hora_data`, `Detemp`, `Detapl`, `Detval`, `Detdes`, `Seguridad`) VALUES
('root', '2014-08-20', '10:45:00', '01', 'minimo_dias_vacaciones', '8', 'Cantidad de días mínimo de vacaciones que puede solicitar un empleado para un periodo activo', 'C-root');
 -->