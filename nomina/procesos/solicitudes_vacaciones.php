<?php
include_once("conex.php");
/*
Programa       : solicitudes_vacaciones.php
Autor          : Camilo zapata
Fecha creación : Agosto 26 de 2014


ACTUALIZACIONES
2017-10-24 Arleyda Insignares C. Se adicionan dos botones para generar en presentación listado y una segunda opción para exportar en
           formato CSV.
2016-01-05 edwin mg:  Se cambian queries para que no se tenga en cuenta la fecha de inicio de disfrute de vacaciones en la funcion periodosSolicitadosPorAprobar
2015-09-17 camilo zz: Se hacen totalmente excluyentes la busqueda por fecha de disfrute y las demas consultas.
2014-08-26 camilo zz: Creación del programa.

Descripción : Este programa se encarga de listas todas las solicitudes de vacaciones enviadas por los empleados y aprobadas por sus coordinadores
*/
$wactualiz="2017-10-24";

$wfecha = date("Y-m-d");
$whora  = (string)date("H:i:s");

if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}

$arr_estados_respuesta = array("APROBADO"=>"Liquidada", "RECHAZADO"=>"Rechazada");
global $wfecha, $whora, $arr_estados_respuesta;

$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];






//=======================================================================================================//
//                                   ***** F U N C I O N E S *****
//=======================================================================================================//
function diasEntreFechas($fechainicio, $fechafin)
{
    return ((strtotime($fechafin)-strtotime($fechainicio))/86400);
}

function codigoMatrix( $codigoTalhuma ){
    global $conex;
    $codBuscado  = explode( "-", $codigoTalhuma );
    $codBuscado2 = $codBuscado[0];

    $query = " SELECT COUNT(*) FROM usuarios where codigo = '{$codBuscado2}' ";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_array( $rs );
    if( $row[0] > 0 )
        return( $codBuscado2 );
    else
        return( $codBuscado[1].$codBuscado2 );
}

function fila_vacaciones_por_aprobar($id_solicitud, $wclass, $fecha_inicio, $fecha_fin, $dias_disponibles, $consecutivo, $codigo_empleado, $arr_solicitudesEnviadas, $div_acordeon, $arr_empleado, $WMAXIMO_DIAS, $WMINIMO_DIAS, $estado_solicitud, $fechaSolicitud,$centro_costos)
{
    global $arr_estados_respuesta, $usuariosSolicitudesVencidas;
    $html_tr                     = "";
    $html_csv                    = "";
    $idx_diferenciador           = $id_solicitud; //crearIndiceUnico($codigo_empleado, $fecha_inicio, $fecha_fin, $dias_disponibles).'__'.$arr_solicitudesEnviadas['id_solicitud'];
    $idx_diferenciador_html      = $idx_diferenciador;//.'__'.$div_acordeon;
    $arr_periodo                 = $arr_solicitudesEnviadas;
    $resultado                   = '';

    $inactivar_solicitado        = "";
    $wdias_disfrutar             = $dias_disponibles;
    $fecha_solicitud_inicio      = "";
    $fecha_solicitud_fin         = "";
    $class_solicitado            = "";
    $btn_solicitar               = '';
    $btn_actualizar              = '';
    $claseSolicitud              = '';
    $deshabGuardar               = '';

    if($dias_disponibles < $WMAXIMO_DIAS)
    {
        $WMAXIMO_DIAS = $dias_disponibles;
        if($dias_disponibles < $WMAXIMO_DIAS)
        {
            $WMAXIMO_DIAS = $dias_disponibles;
            if($dias_disponibles > 0 && $dias_disponibles < $WMINIMO_DIAS )
            {
                $WMINIMO_DIAS = $dias_disponibles;
            }
        }
    }

    if($arr_solicitudesEnviadas['respuesta_nomina'] != '')
    {
        $class_solicitado = "solicitado_respuesta_nomina";
    }
    $titleBloqueo    = "";
    $classBloqueo    = "";
    if($estado_solicitud == "vencida"){
        $claseSolicitud  = ' fondorojo ';
        $deshabGuardar   = 'disabled';
        $titleBloqueo    = "<div class='fila2'><span class='subtituloPagina2'><font size='2'><img src='../../images/medical/root/Advertencia.png' height='15' width='15'>Esta solicitud no puede cambiar su estado, debido a que se encuentra vencida o hay una solicitud de un periodo anterior en este estado</font></span><br></div>";
        $classBloqueo    = " estadoBloqueado ";
        array_push( $usuariosSolicitudesVencidas, $codigo_empleado);
    }else{
        if( in_array( $codigo_empleado, $usuariosSolicitudesVencidas ) ){
            $claseSolicitud  = ' fondorojo ';
            $deshabGuardar   = 'disabled';
            $titleBloqueo    = "<div class='fila2'><span class='subtituloPagina2'><font size='2'><img src='../../images/medical/root/Advertencia.png' height='15' width='15'>Esta solicitud no puede cambiar su estado, debido a que se encuentra vencida o hay una solicitud de un periodo anterior en este estado</font></span><br></div>";
            $classBloqueo    = " estadoBloqueado ";
        }
    }
    if( $arr_solicitudesEnviadas['respuesta_nomina'] != "" ){
        $deshabGuardar = 'disabled';
    }

    $nombres = trim($arr_empleado['nombre1'].' '.$arr_empleado['nombre2'].' '.$arr_empleado['apellido1'].' '.$arr_empleado['apellido2']);

    $options_estados = '<option value="">Seleccione..</option>';
    foreach ($arr_estados_respuesta as $key => $value)
    {
        $seleccionado = ($key == $arr_solicitudesEnviadas['respuesta_nomina']) ? 'selected="selected"': '';
        $options_estados .= '<option value="'.$key.'" '.$seleccionado.' >'.$value.'</option>';
    }
    $title = "<div class='fila2'><span class='subtituloPagina2'><font size='2'>Código: ".$codigo_empleado."</font></span><br></div>";
    $html_tr .= '
            <tr align="center" class="'.$wclass.' '.$class_solicitado.' tr_solicitudes_aprobar '.$claseSolicitud.'" id="tr_'.$idx_diferenciador_html.'" >
                <td align="center">'.codigoMatrix($codigo_empleado).'</td>
                <td align="left" class=" td_usuario " title="'.$title.'">'.utf8_encode($nombres).'</td>
                <td align="left" >'.utf8_encode($centro_costos).'</td>
                <td align="center">'.$fecha_inicio.'</td>
                <td align="center"><---></td>
                <td align="center">'.$fecha_fin.'</td>
                <td align="center">'.$dias_disponibles.'</td>

                <td align="center">
                    '.$arr_solicitudesEnviadas["dias_solicitados"].'
                </td>
                <td align="center" >
                    '.$arr_solicitudesEnviadas["fecha_inicio_solicitud"].'
                </td>
                <td align="center" >
                    '.$arr_solicitudesEnviadas["fecha_fin_solicitud"].'
                </td>
                <td align="center">
                    <select id="estado_nomina_'.$idx_diferenciador_html.'" id_solicitud="'.$id_solicitud.'" class="input_blq '.$classBloqueo.' " title="'.$titleBloqueo.'" '.$deshabGuardar.' onchange="responderSolicitud(\''.$idx_diferenciador_html.'\', \''.$id_solicitud.'\',\''.$div_acordeon.'\',\''.$fecha_inicio.'\',\''.$codigo_empleado.'\', this);" >
                        '.$options_estados.'
                    </select>
                </td>
                <td align="center" >
                    '.$id_solicitud.'
                </td>
                <td align="center" >
                    '.$fechaSolicitud.'
                </td>
                <input type="hidden" value="'.base64_encode(serialize($arr_periodo)).'" id="arr_solicitudEnviada_'.$idx_diferenciador_html.'" name="arr_solicitudEnviada_'.$idx_diferenciador_html.'" >
            </tr>';

    $html_csv .= 'saltolinea'.substr($codigo_empleado,0,5).";".$arr_solicitudesEnviadas["fecha_inicio_solicitud"].';'.$arr_solicitudesEnviadas["dias_solicitados"].';'.'0;1;';


    $resultado = $html_tr.'|'.$html_csv;

    return $resultado;

    //--><input style="'.$btn_solicitar.'" id="btn_aprobar_'.$idx_diferenciador_html.'" class="input_blq" '.$deshabGuardar.' type="button" value="Guardar" onclick="responderSolicitud(\''.$idx_diferenciador_html.'\', \''.$id_solicitud.'\',\''.$div_acordeon.'\',\''.$fecha_inicio.'\',\''.$codigo_empleado.'\');" >
}

function prepararLog($usuario_actualiza, $arr_parametros, $accion, $id_solicitud)
{
    global $wfecha, $whora, $wbasedato, $conex;
    if($accion == 'solicitarVacaciones-actualizar' || $accion == 'solicitarVacaciones-solicitar')
    {
        $sql = " INSERT INTO `{$wbasedato}_000015` (`Medico`, `Fecha_data`, `Hora_data`, `Logvns`, `Logvac`, `Logvcm`, `Logest`, `Seguridad`)
                      VALUES ('{$usuario_actualiza}', '{$wfecha}', '{$whora}', '{$id_solicitud}','{$accion}', 'Dvadso:{$arr_parametros['dias_solicitados']},Dvafid:{$arr_parametros['fecha_inicio_solicitud']},Dvaffd:{$arr_parametros['fecha_fin_solicitud']}', 'on', 'c-{$usuario_actualiza}');";
    }
    elseif($accion == 'cancelarVacaciones')
    {
        $sql = " INSERT INTO `{$wbasedato}_000015` (`Medico`, `Fecha_data`, `Hora_data`, `Logvns`, `Logvac`, `Logvcm`, `Logest`, `Seguridad`)
                      VALUES ('{$usuario_actualiza}', '{$wfecha}', '{$whora}', '{$id_solicitud}','{$accion}', 'Dvaest:off', 'on', 'c-{$usuario_actualiza}');";
    }
    elseif($accion == 'respuestaNomina')
    {
        $sql = " INSERT INTO `{$wbasedato}_000015` (`Medico`, `Fecha_data`, `Hora_data`, `Logvns`, `Logvac`, `Logvcm`, `Logest`, `Seguridad`)
                      VALUES ('{$usuario_actualiza}', '{$wfecha}', '{$whora}', '{$id_solicitud}','{$accion}', 'Dvaern:{$arr_parametros['respuesta_nomina']}', 'on', 'c-{$usuario_actualiza}');";
    }
    $rs = mysql_query( $sql, $conex );
}

function crearIndiceUnico($wusuario_solicitud, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles)
{
    $wusuario_solicitud = str_replace("-", "_", $wusuario_solicitud);
    $fecha_inicio_pendiente = str_replace("/", "_", $fecha_inicio_pendiente);
    $fecha_fin_pendiente    = str_replace("/", "_", $fecha_fin_pendiente);
    return $wusuario_solicitud.'__'.str_replace("-", "_", $fecha_inicio_pendiente).'__'.str_replace("-", "_", $fecha_fin_pendiente).'__'.$dias_disponibles;
}

function crearArrayDatosSolicitud($wusuario_solicitud, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles, $respuesta_coordinador, $respuesta_nomina, $dias_solicitados, $fecha_inicio_solicitud, $fecha_fin_solicitud, $wcentro_costo_empleado, $id_solicitud, $wcodigo_coordinador, $estado_solicitud, $fechaSolicitud)
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
                    "wcodigo_coordinador"    => $wcodigo_coordinador,
                    "estadoSolicitud"        => $estado_solicitud,
                    "fechaSolicitud"         => $fechaSolicitud
                    );
}

function periodosSolicitadosPorAprobar($conex, $wemp_pmla, $wbasedato, $wtalhuma, &$arr_centroCostos, &$arr_coordinadores, &$arr_solicitantes)
{
    $fecha_actual = date("Y-m-d");  //OJO
    global $wcodigo;
    global $wnumeroSolicitud;
    global $westadoBuscado;
    global $buscarPorDisfrute;
    global $fechaDisfruteIni;
    global $fechaDisfruteFin;
    global $fechaMesAtras;

    ( empty($westadoBuscado) ) ? $westadoBuscado = "sinRevisar" : $westadoBuscado = $westadoBuscado;
    ( empty($fechaMesAtras) ) ? $wcondFechaCreacion = "" : $wcondFechaCreacion = " AND n12.fecha_data >= '{$fechaMesAtras}'";
    $condicionEmpleado        = ( empty( $wcodigo ) or trim( $wcodigo )=="" ) ? "" : " AND Dvause = '".empresaEmpleado($wemp_pmla, $conex, $wbasedato, $wcodigo )."'";
    $condicionNumeroSolicitud = ( empty( $wnumeroSolicitud ) or trim( $wnumeroSolicitud )=="" ) ? "" : " AND id = '{$wnumeroSolicitud}'";
    // No se mostrarán las solicitudes que ya empezó el periodo de disfrute (Dvafid)
    $arr_solicitudesEnviadas = array();
    if( $wnumeroSolicitud != "" ){
        $condicionEmpleado = "";
    }

    if( $wnumeroSolicitud != "" ){
        $sql = "SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                        , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                        , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                        , n12.id AS id_solicitud, n12.Dvacco AS wcentro_costo_empleado, n12.Dvacdc AS wcodigo_coordinador, 'pendiente' estadoSolicitud, fecha_data fechaSolicitud
                FROM    {$wbasedato}_000012 AS n12
                WHERE   n12.Dvaest = 'on'
                        AND n12.Dvaerc = 'APROBADO'
                        AND n12.Dvafid >= '{$fecha_actual}'
                        {$wcondFechaCreacion}
                        {$condicionNumeroSolicitud}
                UNION
                SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                        , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                        , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                        , n12.id AS id_solicitud, n12.Dvacco AS wcentro_costo_empleado, n12.Dvacdc AS wcodigo_coordinador, 'vencida' estadoSolicitud, fecha_data fechaSolicitud
                FROM    {$wbasedato}_000012 AS n12
                WHERE   n12.Dvaest = 'on'
                        AND n12.Dvaerc = 'APROBADO'
                        AND n12.Dvaern = ''
                        AND n12.Dvafid < '{$fecha_actual}'
                        {$wcondFechaCreacion}
                        {$condicionNumeroSolicitud}";
    }else{
        if( $buscarPorDisfrute == "on" ){
            $westadoBuscado          = "sinRevisar";
            $condicionFechasDisfrute = " AND Dvafid between '{$fechaDisfruteIni}' and '{$fechaDisfruteFin}' ";
            $wcondFechaCreacion      = "";//--> se agregó 2015-09-17 para hacer totalmente excluyentes la busqueda por fecha de disfrute y las demas
        }
        if( $westadoBuscado == "sinRevisar" ){
                $sql = "SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                                    , n12.id AS id_solicitud, n12.Dvacco AS wcentro_costo_empleado, n12.Dvacdc AS wcodigo_coordinador, 'pendiente' estadoSolicitud, fecha_data fechaSolicitud
                            FROM    {$wbasedato}_000012 AS n12
                            WHERE   n12.Dvaest = 'on'
                                    AND n12.Dvaerc = 'APROBADO'
                                    AND n12.Dvaern <> 'RECHAZADO'
                                    AND n12.Dvaern <> 'APROBADO'
                                    AND n12.Dvafid >= '{$fecha_actual}'
                                    {$wcondFechaCreacion}
                                    {$condicionFechasDisfrute}
                                    {$condicionEmpleado}
                            UNION
                            SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                                    , n12.id AS id_solicitud, n12.Dvacco AS wcentro_costo_empleado, n12.Dvacdc AS wcodigo_coordinador, 'vencida' estadoSolicitud, fecha_data fechaSolicitud
                            FROM    {$wbasedato}_000012 AS n12
                            WHERE   n12.Dvaest = 'on'
                                    AND n12.Dvaerc = 'APROBADO'
                                    AND n12.Dvaern <> 'RECHAZADO'
                                    AND n12.Dvaern <> 'APROBADO'
                                    AND n12.Dvafid < '{$fecha_actual}'
                                    {$wcondFechaCreacion}
                                    {$condicionFechasDisfrute}
                                    {$condicionEmpleado}";

                if( $buscarPorDisfrute == "on"){

                    $sql = "SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                                    , n12.id AS id_solicitud, n12.Dvacco AS wcentro_costo_empleado, n12.Dvacdc AS wcodigo_coordinador, 'pendiente' estadoSolicitud, fecha_data fechaSolicitud
                            FROM    {$wbasedato}_000012 AS n12
                            WHERE   n12.Dvaest = 'on'
                                    AND n12.Dvaerc = 'APROBADO' "
                                    // AND n12.Dvaern <> 'RECHAZADO'
                                    // AND n12.Dvaern <> 'APROBADO'
                            ."        {$condicionFechasDisfrute}";
                }

        }else if( $westadoBuscado == "rechazada" ){

            $sql = "SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                            , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                            , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                            , n12.id AS id_solicitud, n12.Dvacco AS wcentro_costo_empleado, n12.Dvacdc AS wcodigo_coordinador, 'pendiente' estadoSolicitud, fecha_data fechaSolicitud
                    FROM    {$wbasedato}_000012 AS n12
                    WHERE   n12.Dvaest = 'on'
                            AND n12.Dvaerc = 'APROBADO'
                            AND n12.Dvaern = 'RECHAZADO' "
                            // AND n12.Dvafid >= '{$fecha_actual}'
                    ."        {$wcondFechaCreacion}
                            {$condicionEmpleado}
                            {$condicionNumeroSolicitud}";

        }else if( $westadoBuscado == "aprobada" ){

             $sql = "SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                                , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                                , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                                , n12.id AS id_solicitud, n12.Dvacco AS wcentro_costo_empleado, n12.Dvacdc AS wcodigo_coordinador, 'pendiente' estadoSolicitud, fecha_data fechaSolicitud
                        FROM    {$wbasedato}_000012 AS n12
                        WHERE   n12.Dvaest = 'on'
                                AND n12.Dvaerc = 'APROBADO'
                                AND n12.Dvaern = 'APROBADO' "
                                // AND n12.Dvafid >= '{$fecha_actual}'
                    ."            {$wcondFechaCreacion}
                                {$condicionEmpleado}
                                {$condicionNumeroSolicitud}";

        }else if( $westadoBuscado == "vencida" ){
            $sql = "SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                            , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                            , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                            , n12.id AS id_solicitud, n12.Dvacco AS wcentro_costo_empleado, n12.Dvacdc AS wcodigo_coordinador, 'vencida' estadoSolicitud, fecha_data fechaSolicitud
                    FROM    {$wbasedato}_000012 AS n12
                    WHERE   n12.Dvaest = 'on'
                            AND n12.Dvaerc = 'APROBADO'
                            AND n12.Dvaern = ''
                            AND n12.Dvafid < '{$fecha_actual}'
                            {$wcondFechaCreacion}
                            {$condicionEmpleado}
                            {$condicionNumeroSolicitud}";
        }
    }

    $sql .= " ORDER BY wcentro_costo_empleado, wusuario_solicitud, fecha_inicio_pendiente ";
    //echo "<pre>".print_r( $sql, true )."</pre>";

    if($result = mysql_query($sql, $conex))
    {
        while ($row = mysql_fetch_array($result))
        {
            if( $buscarPorDisfrute == "on" || $westadoBuscado == "rechazada" || $westadoBuscado == "aprobada" ){
                if( strtotime( $row['fecha_inicio_solicitud'] ) - strtotime( $fecha_actual ) < 0 ){
                    $row['estadoSolicitud'] = "vencida";
                }else{
                    $row['estadoSolicitud'] = "pendiente";
                }
            }
            $centro_costo = $row['wcentro_costo_empleado'];
            if(!array_key_exists($centro_costo, $arr_solicitudesEnviadas))
            {
                $arr_solicitudesEnviadas[$centro_costo] = array();
            }

            $idx_solicitud = $row['id_solicitud'];
            if(!array_key_exists($idx_solicitud, $arr_solicitudesEnviadas[$centro_costo]))
            {
                $arr_solicitudesEnviadas[$centro_costo][$idx_solicitud] = array();
            }

            // Incluye solo los centros de costos de los empleados que tiene solicitudes activas.
            if(!array_key_exists($centro_costo, $arr_centroCostos))
            {
                $arr_centroCostos[$centro_costo] = $centro_costo;
            }

            // Se crea un array solo con los coordinadores que autorizaron solicitudes
            if(!array_key_exists($row['wcodigo_coordinador'], $arr_coordinadores))
            {
                $arr_coordinadores[$row['wcodigo_coordinador']] = $row['wcodigo_coordinador'];
            }

            if(!array_key_exists($row['wusuario_solicitud'], $arr_solicitantes))
            {
                $arr_solicitantes[$row['wusuario_solicitud']] = $row['wusuario_solicitud'];
            }

            $arr_solicitudesEnviadas[$centro_costo][$idx_solicitud] =
                        crearArrayDatosSolicitud($row['wusuario_solicitud'], $row['fecha_inicio_pendiente'], $row['fecha_fin_pendiente'], $row['dias_disponibles']
                                                , $row['respuesta_coordinador'], $row['respuesta_nomina'], $row['dias_solicitados'], $row['fecha_inicio_solicitud']
                                                , $row['fecha_fin_solicitud'], $centro_costo, $row['id_solicitud'], $row['wcodigo_coordinador'], $row['estadoSolicitud'], $row['fechaSolicitud']);
        }
    }
    else
    {
        echo "Error ".mysql_error()." => ".$sql;
    }
    return $arr_solicitudesEnviadas;
}

function consultarCentroCostos($conex, $wemp_pmla, $wbasedato, $wtalhuma, $arr_centroCostos)
{
    $arr_codigos_ccos = array_keys($arr_centroCostos);
    $impld_ccos = implode("','", $arr_codigos_ccos);
    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    if($impld_ccos != '' && $row = mysql_fetch_array($res))
    {
        $tabla_CCO = $row['Emptcc'];
        $aux       = explode("_", $tabla_CCO );
        $aux       = $aux[1];
        switch ( $aux ) {
            case '000003':
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    $tabla_CCO AS tb1
                                WHERE   tb1.Ccocod IN ('{$impld_ccos}')";
                break;
            case '000005':
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    $tabla_CCO AS tb1
                                WHERE   tb1.Ccocod IN ('{$impld_ccos}')";
                break;

            default:
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    $tabla_CCO AS tb1
                                WHERE   tb1.Ccocod IN ('{$impld_ccos}')";
        }

        if($result = mysql_query($query,$conex))
        {
            while($row = mysql_fetch_array($result))
            {
                if(!array_key_exists($row['codigo'], $arr_centroCostos))
                {
                    $arr_centroCostos[$row['codigo']] = '';
                }
                $arr_centroCostos[$row['codigo']] = utf8_encode($row['nombre']);
            }
        }
    }
    return $arr_centroCostos;
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
            WHERE   codigo = '".$user_session."'
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


function inicializarArreglos(){

    global $centrosCostos;
    global $conex;
    global $wbasedato;
    global $wemp_pmla;
    global $empleados;
    global $centroCostosActual;
    global $empleadoActual;
    global $wcco;
    global $wcodigo;

    $caracteres    = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
    $caracteres2   = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");

    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    if( $row = mysql_fetch_array($res))
    {
        $tabla_CCO = $row['Emptcc'];
        $aux       = explode( "_", $tabla_CCO );
        $aux       = $aux[1];
        switch ( $aux ) {
            case '000003':
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                              FROM  $tabla_CCO AS tb1
                             WHERE  tb1.Ccoest = 'on'
                             ORDER BY nombre ";
                break;
            case '000005':
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                              FROM  $tabla_CCO AS tb1
                             WHERE  tb1.Ccoest = 'on'
                             GROUP BY tb1.Ccocod
                             ORDER BY nombre";
                break;

            default:
                $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                              FROM  $tabla_CCO AS tb1
                             WHERE  tb1.Ccoest = 'on'
                             GROUP BY tb1.Ccocod
                             ORDER BY nombre";
        }

        $result = mysql_query( $query, $conex ) or die(mysql_error());
        while($row2 = mysql_fetch_array($result)){
             $row2['nombre'] = utf8_encode( $row2['nombre'] );
             $row2['nombre'] = str_replace( $caracteres, $caracteres2, $row2['nombre'] );
             array_push( $centrosCostos, trim($row2['codigo']).", ".trim($row2['nombre']) );
             if( $row2['codigo'] == $wcco and $wcco != "" and !empty( $wcco ) ){
                $centroCostosActual = "{$row2['codigo']}, ".trim($row2['nombre']);
             }
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
         if( $row2['codigo'] == $wcodigo and $wcodigo != "" and !empty( $wcodigo ) ){
            $empleadoActual = "{$row2['codigo']}, ".trim($row2['descripcion']);
         }
         /*$centrosCostos['valores'] = array( "codigo"=>$row2['codigo'], "nombre"=>$row2['nombre'])
         $centrosCostos['mostrar'] =$row2['codigo']",".$row2['nombre'];*/

    }
}

if(isset($accion) && isset($form))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                default :
                    $data['mensaje'] = utf8_encode($no_exec_sub);
                    $data['error'] = 1;
                    break;
            }
            break;



        case 'update':
            switch($form)
            {
                case 'respuestaNomina':
                    $arr_solicitudEnviada_log = array("respuesta_nomina"=>$estado_nomina);
                    $user_sesion_talhuma      = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);
                    if( $estado_nomina == "APROBADO" ){
                        $sql = " SELECT count( * ) solicitudesPreviasPendientes
                                   FROM {$wbasedato}_000012
                                  WHERE Dvause = '{$usuarioSolicitud}'
                                    AND Dvapfi < '{$fechainicioP}'
                                    AND Dvaerc = 'APROBADO'
                                    AND Dvaern = ''
                                    AND Dvaest = 'on'
                                    AND Dvafid >= '".date('Y-m-d')."'";
                        //echo "<pre>".print_r( $sql, true)."<pre>";
                        $rs  = mysql_query( $sql, $conex );
                        $row = mysql_fetch_assoc( $rs );
                        if( $row['solicitudesPreviasPendientes']*1 > 0 ){
                            $data["error"]   = 1;
                            $data["mensaje"] = utf8_encode(" Este usuario tiene solicitudes pendientes por aprobar de periodos anteriores a este, \n debe aprobarlas");
                            break;
                        }
                    }else if( $estado_nomina == "RECHAZADO" ){
                        $sql = " SELECT count( * ) solicitudesPreviasPendientes
                                   FROM {$wbasedato}_000012
                                  WHERE Dvause = '{$usuarioSolicitud}'
                                    AND Dvapfi > '{$fechainicioP}'
                                    AND Dvaerc = 'APROBADO'
                                    AND Dvaern = ''
                                    AND Dvaest = 'on'
                                    AND Dvafid >= '".date('Y-m-d')."'";
                        //echo "<pre>".print_r( $sql, true)."<pre>";
                        $rs  = mysql_query( $sql, $conex );
                        $row = mysql_fetch_assoc( $rs );
                        if( $row['solicitudesPreviasPendientes']*1 > 0 ){
                            $data["error"]   = 1;
                            $data["mensaje"] = utf8_encode(" Este usuario tiene solicitudes de periodos posteriores sin revisar, \n debe rechazarlas primero");
                            break;
                        }
                    }


                    //$log = prepararLog($user_sesion_talhuma, $arr_solicitudEnviada_log, $form);
                    $sql = "UPDATE {$wbasedato}_000012
                                SET   Dvaern = '{$estado_nomina}'
                                    , Dvanfr = '{$wfecha}'
                                    , Dvanhr = '{$whora}'
                                    , Dvacun = '{$user_sesion_talhuma}'
                            WHERE   id = '{$id_solicitud}'";
                    if($result = mysql_query($sql, $conex))
                    {
                        prepararLog($user_sesion_talhuma, $arr_solicitudEnviada_log, $form, $id_solicitud);
                        $data["mensaje"] = utf8_encode("Se ha dado una respuesta a la solicitud exitosamente.");
                    }
                    else
                    {
                        $data["error"]   = 1;
                        $data["mensaje"] = utf8_encode("Su solicitud de vacaciones no pudo ser actualizada");
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
    echo json_encode($data);
    return;
}

//=======================================================================================================//
    include_once("root/comun.php");
    // $conexunix = odbc_pconnect('nomina','informix','sco') or die("No se ralizo Conexion con el Unix");

    $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nomina');
    $wtalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

    // if (strpos($user,"-") > 0) $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

    if(!isset($wusuario_solicitud) || empty($wusuario_solicitud))
    {
        $wusuario_solicitud = $user_session;
    }

    $arr_centroCostos            = array();
    $arr_coordinadores           = array();
    $arr_solicitantes            = array();
    $usuariosSolicitudesVencidas = array();

    // ***********************************************************************************************************
    // ********************************* SOLICITUDES PENDIENTES DE LOS EMPLEADOS *********************************
    // ***********************************************************************************************************
    if( $wnumeroSolicitud != "" ){
        $wcodigo = "";
        $wcco = "";
    }else{
        if( $wcodigo != "" )
            $wcco = "";
    }
    $html_pendientes_aprobar   = '';
    $html_pendientes_detalle   = '';
    $html_pendientes_csv       = '';

    $contcss                   = 0;
    $arr_solicitudesVacaciones = periodosSolicitadosPorAprobar($conex, $wemp_pmla, $wbasedato, $wtalhuma, $arr_centroCostos, $arr_coordinadores, $arr_solicitantes);
    $arr_centroCostos          = consultarCentroCostos($conex, $wemp_pmla, $wbasedato, $wtalhuma, $arr_centroCostos);
    $impl_use                  = implode("','", $arr_solicitantes);
    $condicionCco              = ( empty( $wcco ) or trim( $wcco )=="" ) ? "" : " AND Idecco = '$wcco' ";
    $condicionEmpleado         = ( empty( $wcodigo ) or trim( $wcodigo )=="" ) ? "" : " AND Ideuse = '".empresaEmpleado($wemp_pmla, $conex, $wbasedato, $wcodigo )."' ";


    // Consultar los nombres de los empleados en la tabla de 000013
    $sql = "SELECT  tlh13.Ideuse AS codigo_use,tlh13.Ideno1 AS nombre1, tlh13.Ideno2 AS nombre2, tlh13.Ideap1 AS apellido1, tlh13.Ideap2 AS apellido2
            FROM    {$wtalhuma}_000013 AS tlh13
            WHERE   tlh13.Ideuse IN ('{$impl_use}')
                    AND tlh13.Ideest = 'on'
                    {$condicionCco}
                    {$condicionEmpleado}
            ORDER BY tlh13.Ideno1, tlh13.Ideno2, tlh13.Ideap1, tlh13.Ideap2";
    $result = mysql_query($sql, $conex) or die ("Error árbol de relación ".mysql_error().' => '.$sql);
    if(mysql_num_rows($result) > 0)
    {
        while ($row = mysql_fetch_array($result))
        {
            if(!array_key_exists($row['codigo_use'], $arr_solicitantes))
            {
                $arr_solicitantes[$row['codigo_use']] =  array();
            }
            if( $row['nombre2'] == "NO APLICA")
                $row['nombre2'] = "";

            $arr_solicitantes[$row['codigo_use']] =  array( "nombre1"         => $row['nombre1'],
                                                            "nombre2"         => $row['nombre2'],
                                                            "apellido1"       => $row['apellido1'],
                                                            "apellido2"       => $row['apellido2']);
        }
    }
    //echo "<pre>".print_r($arr_solicitudesVacaciones)."</pre>";

    $html_pendientes_detalle = '<table id="tbllistado" style="display:none;" >
                                <tr align="center" class="encabezadoTabla">
                                    <td colspan="" align="center">Código</td>
                                    <td colspan="" align="center">Nombres</td>
                                    <td colspan="" align="center">Centro de Costos</td>
                                    <td colspan="3" align="center">Período Cumplido</td>
                                    <td align="center">Días pendientes<br>de Disfrutar</td>
                                    <td align="center">Días Solicitados</td>
                                    <td align="center" colspan="2" style="width: 250px;">Rango de Fechas a Disfrutar</td>
                                    <td align="center">Responder</td>
                                    <td align="center">Numero Solicitud</td>
                                    <td align="center">Fecha Solicitud</td>
                                </tr>';
    $salto =  "saltolinea";

    $html_pendientes_csv = 'Codigo empleado;Fecha Salida;Dias Tiempo;Dias Dinero;Plan Vac;';

    foreach ($arr_solicitudesVacaciones as $cod_cco => $arr_solicitudes)
    {
        if( $wcco == "" or empty( $wcco ) or $wcco == $cod_cco ){

            $solicitudes_cco = '';
            $solicitudes_csv = '';
            foreach ($arr_solicitudes as $id_solicitud => $arr_solicitudEnviada)
            {
                    $wclass = ($contcss % 2 == 0) ? "fila1": "fila2";
                    $fecha_inicio_pendiente = $arr_solicitudEnviada['fecha_inicio_pendiente'];
                    $fecha_fin_pendiente    = $arr_solicitudEnviada['fecha_fin_pendiente'];
                    $centro_costos          = $arr_centroCostos[$cod_cco];
                    $dias_disponibles       = $arr_solicitudEnviada['dias_disponibles'];
                    $wusuario_solicitud     = $arr_solicitudEnviada['wusuario_solicitud'];
                    $wcodigo_coordinador    = $arr_solicitudEnviada['wcodigo_coordinador'];
                    $arr_empleado           = $arr_solicitantes[$wusuario_solicitud];
                    $estadoSolicitud        = $arr_solicitudEnviada['estadoSolicitud'];
                    $fechaSolicitud         = $arr_solicitudEnviada['fechaSolicitud'];
                    // echo "<pre>".print_r($arr_solicitudEnviada,true)."</pre>";

                    $WMAXIMO_DIAS_emp = 15;
                    $WMINIMO_DIAS_emp = 8;

                    $solicitudes_ant = fila_vacaciones_por_aprobar($id_solicitud, $wclass, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles, '', $wusuario_solicitud, $arr_solicitudEnviada, "accordion_aprobaciones", $arr_empleado, $WMAXIMO_DIAS_emp, $WMINIMO_DIAS_emp, $estadoSolicitud, $fechaSolicitud,$centro_costos);

                    list($solicitudes_ant1,$solicitudes_ant2) = explode('|', $solicitudes_ant);


                    $solicitudes_cco .= $solicitudes_ant1;

                    $solicitudes_csv .= $solicitudes_ant2;

                    $contcss++;
            }


            $wusuario                 = substr($wusuario_solicitud,0,5);

            $html_pendientes_detalle .= $solicitudes_cco;

            $html_pendientes_csv     .= $solicitudes_csv;

            $html_pendientes_aprobar .= '
                    <div width="" id="accordion_aprobaciones_'.$cod_cco.'" style="text-align:left;" class="div_alinear div_solicitud_cco">
                        <h3>Centro costo: '.utf8_encode($arr_centroCostos[$cod_cco]).'</h3>
                        <div align="center" id="pendientes_otros_empleados" style="display:">
                            <table>
                                <tr align="center" class="encabezadoTabla">
                                    <td colspan="" align="center">Código</td>
                                    <td colspan="" align="center">Nombres</td>
                                    <td colspan="" align="center">Servicio</td>
                                    <td colspan="3" align="center">Período Cumplido</td>
                                    <td align="center">Días pendientes<br>de Disfrutar</td>
                                    <td align="center">Días Solicitados</td>
                                    <td align="center" colspan="2" style="width: 250px;">Rango de Fechas a Disfrutar</td>
                                    <td align="center">Responder</td>
                                    <td align="center">Numero Solicitud</td>
                                    <td align="center">Fecha Solicitud</td>
                                </tr>
                                '.$solicitudes_cco.'
                            </table>
                        </div>
                    </div>
            ';
        }
    }

    $html_pendientes_detalle .= '</table>';

    if(empty($html_pendientes_aprobar))
    {
        $html_pendientes_aprobar = '<br /><br /><br /><br />
            <div style="font-weight:bold; font-size:14pt; color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                <br /><br />
                DE MOMENTO NO HAY SOLICITUDES DE VACACIONES
                <br /><br /><br />
            </div>
            <br /><br /><br /><br />';
    }
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
  <title>SOLICITUDES VACACIONES</title>
    <style type="text/css">
        .div_alinear{
            margin-left: 10px;
        }
        .tituloPpal{
            border-radius: 15px;
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
        #tooltip div{margin:0; width:150px}
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
            autocompletarEnFormularios();

            $(".div_solicitud_cco").accordion({
                collapsible: true,
                heightStyle: "content",
                active: 0
            });
            //$(".fondorojo").hide();

            try{
                $("#fechaMesAtras").datepicker("destroy");
            }catch(e){

            }

            $(".input_fecha").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                maxDate: "+0m +0w",
                onSelect: function(dateText, inst ) {
                    //$("form").submit();
                }
            });

            $("#fechaDisfruteIni, #fechaDisfruteFin").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                onSelect: function(dateText, inst ) {
                    //$("form").submit();
                }
            });

            $(".soloNumeros").on({
                keypress: function(e){
                    var key = e.keyCode || e.which;

                    if( key != 9 && key != 8 ){
                        if( String.fromCharCode(key).search( /[0-9]/g ) == -1 ){
                            e.preventDefault();
                        }
                    }
                }
            });

            var estadoBuscado  = $( "#westadoBuscado" ).val();
            if( estadoBuscado == "" ){
                $( "#westadoBuscado" ).val( "sinRevisar" );
                $("input[type='radio'][value='sinRevisar']").attr("checked", true);
            }else{
                $("input[type='radio'][value='"+estadoBuscado+"']").attr("checked", true);
            }

             $(".estadoBloqueado").after(function (e) {

                i  = $(this);
                id = $(this).attr("id_solicitud");
                d  = $("<div>");
                d.attr('title', i.attr("title"));
                i.attr("title","")
                //d.css(i.offset());
                offset = i.offset();
                d.css({
                    height   : i.outerHeight(),
                    width    : i.outerWidth(),
                    position : "absolute",
                    top      : offset.top,
                    left     : offset.left
                })
                d.css(i.offset());
                d.tooltip({track: true, delay: 0, showURL: false, opacity: 0.97, left: -50 });
                return d;
            });
            $(".td_usuario").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

            console.log( $("#buscarPorDisfrute" ).val() )
            if( $("#buscarPorDisfrute" ).val() == "on" ){
                $("#tr_fechasDisfrute" ).show();
                $("#tr_fechasDisfrute" ).attr("mostrando","on");
                $("#tr_porOtrosEstados" ).hide();
                $("#tr_porOtrosEstados" ).attr("mostrando","off");
            }else{
                $("#tr_fechasDisfrute" ).hide();
                $("#tr_fechasDisfrute" ).attr("mostrando","off");
                $("#tr_porOtrosEstados" ).show();
                $("#tr_porOtrosEstados" ).attr("mostrando","on");

            }
        });


        function url_ajax()
        {
            var wemp_pmla = $("#wemp_pmla").val();
            var wbasedato = $("#wbasedato").val();
            var wtalhuma  = $("#wtalhuma").val();
            return "solicitudes_vacaciones.php?wemp_pmla="+wemp_pmla+"&wbasedato="+wbasedato+"&wtalhuma="+wtalhuma;
        }

        function responderSolicitud(idx_diferenciador, id_solicitud,div_acordeon, fechainicioP, usuario, obj)
        {

            //var arr_solicitudEnviada = $("#arr_solicitudEnviada_"+idx_diferenciador).val();
            var estado_nomina          = obj.value;
            //$("#estado_nomina_"+idx_diferenciador).val();
            if( estado_nomina == "APROBADO"){
                mensaje = "Esta solicitud no podrá ser modificada posteriormente, ¿Está seguro que desea liquidarla ?";
            }

            if( estado_nomina == "RECHAZADO" ){
                mensaje = "Esta solicitud no podrá ser modificada posteriormente, ¿Está seguro que desea rechazarla ?";
            }
            if( !confirm( mensaje ) && estado_nomina != "" ){
                $( obj ).find("option[value='']").attr( "selected", true );
                return;
            }

            if(estado_nomina != '')
            {
                $.post(url_ajax(),
                    {
                        consultaAjax  : '',
                        accion        : 'update',
                        form          : 'respuestaNomina',
                        id_solicitud  : id_solicitud,
                        estado_nomina : estado_nomina,
                        fechainicioP  : fechainicioP,
                        usuarioSolicitud  : usuario
                    },
                    function(data){
                        if(data.error == 1)
                        {
                            alert(data.mensaje);
                            $( obj ).find("option[value='']").attr( "selected", true );
                        }
                        else
                        {
                            alert(data.mensaje);
                            $( obj ).attr("disabled", true);
                        }
                    },
                    "json"
                ).done(function(){

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

        function soloNumeros(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            // alert(charCode);
             if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36 || charCode == 46 || charCode == 44) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
                return false;

             return true;
        }

        function mostrarOcultarsolicitudesVencidas(){
            $(".fondorojo").toggle();
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
                $(obj).parent().find("#"+id).val("");
            }
        }

        function generarReporte(){

            if( $("#buscarPorDisfrute").val() == "on" ){
                if( $("#fechaDisfruteIni").val() == "" || $("#fechaDisfruteFin").val() == "" ){
                    alert( "Debe seleccionar el rango de fechas a consultar ");
                    return;
                }else{
                    $("form").submit();
                }
            }else{
                $("form").submit();
            }
        }

        function replaceAll(str, term, replacement) {

            return str.replace(new RegExp(escapeRegExp(term), 'g'), replacement);
        }


        function escapeRegExp(string){

            return string.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
        }


        // Función para mostrar el detalle tipo listado (sin formato acordeón)
        function generarRepdetalle(){

            $("#tbllistado").show();
            $(".div_solicitud_cco").hide();

        }


        // Función para exportar el detalle en formato Excel
        function generarReportecsv(){

            var strexportar = $("#wreporte").val();
            var resexportar = replaceAll(strexportar, 'saltolinea', '\n')
            $("#tbllistado").show();
            $(".div_solicitud_cco").hide();

            var usu = document.createElement('a');
            usu.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(resexportar));
            usu.setAttribute('download','reporte_vacaciones.csv');

            if (document.createEvent) {
                var event = document.createEvent('MouseEvents');
                event.initEvent('click', true, true);
                usu.dispatchEvent(event);
            }
            else {
                usu.click();
            }

        }


        // Función para exportar la tabla 'tblexcel'
        function exportar(){

            //Creamos un Elemento Temporal en forma de enlace
            var tmpElemento = document.createElement('a');
            var data_type = 'data:application/vnd.ms-excel'; //Formato anterior xls

            var tabla_div = document.getElementById('tbllistado');
            var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');

            tmpElemento.href = data_type + ', ' + tabla_html;

            // Asignamos el nombre al archivo en formato xls
            tmpElemento.download = 'listado_vacaciones.xls';

            // Simulamos el click al elemento creado para descargarlo
            tmpElemento.click();

        }


        function seleccionarTipo( obj ){
            $("#westadoBuscado").val( $( obj ).val() );
        }

        function mostrarOcultarFiltros( mostrar, ocultar ){

            if( mostrar == "fechasDisfrute" && $("#buscarPorDisfrute").val() == "off" ){
                $("#buscarPorDisfrute").val("on");
            }else if( mostrar != "fechasDisfrute" & $("#buscarPorDisfrute").val() == "on"){
                $("#buscarPorDisfrute").val("off");
            }else if( mostrar == "fechasDisfrute" & $("#buscarPorDisfrute").val() == "on"){
                $("#buscarPorDisfrute").val("off");
            }
            if( $("#tr_"+mostrar ).is(":visible") ){
                $("#tr_"+mostrar ).hide();
                $("#tr_"+mostrar ).attr("mostrando", "off");
            }else{
                $("#tr_"+ocultar ).hide();
                $("#tr_"+ocultar ).attr("mostrando", "off");
                $("#tr_"+mostrar ).show();
                $("#tr_"+mostrar ).attr("mostrando", "on");

            }
        }

    </script>
</head>
<body>
    <?=encabezado("SOLICITUDES DE VACACIONES",$wactualiz, "clinica")?>

    <!-- //Mensaje de espera -->
    <div id='msjEspere' style='display:none;'>
        <br>
        <img src='../../images/medical/ajax-loader5.gif'/>
        <br><br> Por favor espere un momento ... <br><br>
    </div>

    <script>
    $.blockUI({ message: $('#msjEspere') });
    </script>

    <?php
        $wfecha             = date("Y-m-d");
        $whora              = (string)date("H:i:s");
        $centrosCostos      = array();
        $empleados          = array();
        $centroCostosActual = "";
        $empleadoActual     = "";
        inicializarArreglos();
        $centrosCostos      = json_encode( $centrosCostos );
        $empleados          = json_encode( $empleados );
        if( !isset( $fechaMesAtras ) or $fechaMesAtras == "" )
            $fechaMesAtras = date( "Y-m-d", strtotime( $wfecha."-1 month" ) );
        if( !isset( $buscarPorDisfrute ) )
            $buscarPorDisfrute = 'off';
    ?>
    <!-- //FORMA ================================================================ -->
    <form name='vacaciones' action='solicitudes_vacaciones.php' method=post>
        <input type='hidden' name='array_ccos' id='array_ccos' value='<?=$centrosCostos?>'>
        <input type='hidden' name='array_empleados' id='array_empleados' value='<?=$empleados?>'>
        <input type='hidden' name='wemp_pmla' id='wemp_pmla' value='<?=$wemp_pmla?>'>
        <input type='hidden' name='wbasedato' id='wbasedato' value='<?=$wbasedato?>'>
        <input type='hidden' name='wtalhuma' id='wtalhuma' value='<?=$wtalhuma?>'>
        <input type='hidden' name='wusuario_solicitud' id='wusuario_solicitud' value='<?=$wusuario_solicitud?>'>
        <input type='hidden' name='westadoBuscado' id='westadoBuscado' value='<?=$westadoBuscado?>'>

        <div id='div_formulario_ppal' style='' align='center'>
            <br>
            <table style='display:<?=$mostrar = ( empty( $bandera ) or $bandera ==   "") ? "" : "none"; ?>'>
                <tr class='encabezadotabla tituloPpal'><td colspan='2' align='center'> FILTROS PARA LA BUSQUEDA DE SOLICITUDES. </td></tr>
                <tr class='fila2'>
                    <?php

                    ?>
                    <td style='width:150px;'><span class='subtituloPagina2'><font size='2'>Centro Costos:</font></span></b></td>
                    <td>
                      <input id='input_cco' type='text' value='<?=$centroCostosActual?>' size='40'   placeholder="Deje en blanco para ver todos" onkeyup='validarVacioParametro(this, "wcco");'>
                      <input type='hidden' id='wcco' name='wcco' value='<?=$wcco?>'>
                    </td>
                  </tr>
                  <tr class='fila2'>
                    <td><span class='subtituloPagina2'><font size='2'>Codigo Empleado:</font></span></b> </td>
                    <td>
                        <input type='text' style='width:300px;' id='input_empleados' value='<?=$empleadoActual?>' placeholder="Deje en blanco para ver todos" onkeyup='validarVacioParametro(this, "wcodigo");'>
                        <input type='hidden'  id='wcodigo' name='wcodigo' value='<?=$wcodigo?>'>
                    </td>
                  </tr>
                  <tr class='fila2'>
                    <td><span class='subtituloPagina2'><font size='2'>N&uacute;mero de Solicitud:</font></span></b> </td>
                    <td>
                        <input type='text' style='width:100px;' id='wnumeroSolicitud' name='wnumeroSolicitud' value='<?=$wnumeroSolicitud?>' class='soloNumeros'>
                    </td>
                  </tr>
                  <tr>
                    <td colspan='2' class='fila2' align='center'>
                            <table style='display:<?=$mostrar = ( empty( $bandera ) or $bandera ==   "") ? "" : "none"; ?>'>
                                <tr class='fila2'>
                                    <td colspan='9' align='center'><span class='subtituloPagina2' style='cursor:pointer;' onclick='mostrarOcultarFiltros( "porOtrosEstados", "fechasDisfrute");'><font size='2'> BUSCAR POR ESTADO DE SOLICITUD </font></span></td>
                                </tr>
                                <tr class='fila2'  mostrando='on' id='tr_porOtrosEstados'>
                                  <td nowrap='nowrap'> <input type='radio' name='selectorEstadoSolicitudes' onclick='seleccionarTipo( this );' value='sinRevisar'>Sin revisar </td>
                                  <td nowrap='nowrap'> <input type='radio' name='selectorEstadoSolicitudes' onclick='seleccionarTipo( this );' value='rechazada'> Rechazada </td>
                                  <td nowrap='nowrap'> <input type='radio' name='selectorEstadoSolicitudes' onclick='seleccionarTipo( this );' value='aprobada'> Liquidada </td>
                                  <td nowrap='nowrap'> <input type='radio' name='selectorEstadoSolicitudes' onclick='seleccionarTipo( this );' value='vencida'> Vencidas </td>
                                  <td nowrap='nowrap'>&nbsp;</td>
                                  <td nowrap='nowrap'>&nbsp;</td>
                                  <td  nowrap='nowrap' align='right'> Ver solicitudes creadas desde: <input class='input_fecha soloNumeros' id='fechaMesAtras' name='fechaMesAtras' size='12' value='<?php echo $fechaMesAtras; ?>'> </td>
                                </tr>
                            </table>
                    </td>
                  </tr>
                  <tr>
                    <td colspan='2' class='fila2' align='center'>
                            <table style='display:<?=$mostrar = ( empty( $bandera ) or $bandera ==   "") ? "" : "none"; ?>'>
                                <tr class='fila2'>
                                    <td colspan='9'><CENTER><span class='subtituloPagina2' mostrando='off' style='cursor:pointer;' onclick='mostrarOcultarFiltros( "fechasDisfrute", "porOtrosEstados");' ><font size='2'>BUSCAR POR FECHA DE INICIO DISFRUTE - SOLICITUDES SIN LIQUIDAR</font><span></center><input type='hidden' id='buscarPorDisfrute' name='buscarPorDisfrute' value='<?=$buscarPorDisfrute?>'></td>
                                  </tr>
                                  <tr style='font-size:10px; display:none;' mostrando='off' id='tr_fechasDisfrute'>
                                        <td  nowrap='nowrap' align='center'><input class='soloNumeros' id='fechaDisfruteIni' name='fechaDisfruteIni' size='12' value='<?=$fechaDisfruteIni?>'> HASTA <input class='soloNumeros' id='fechaDisfruteFin' name='fechaDisfruteFin' size='12' value='<?=$fechaDisfruteFin?>'></td>
                                  </tr>
                            </table>
                    </td>
                  </tr>
                  <tr><td colspan='2' align='center'><input type='button' value='Generar' id='btn_generar' onclick='generarReporte();'>
                  &nbsp;<input type='button' value='Listado' id='btn_generar2' onclick='generarRepdetalle();'>
                  &nbsp;<input type='button' value='CSV'     id='btn_generar3' onclick='generarReportecsv();'></td></tr>
            </table>
            <br>
        </div>
        <br>
        <?=$html_pendientes_aprobar?>
        <?=$html_pendientes_detalle?>
        <br><br>
        <table align="center">
            <tr class="boton"><td align="center"><input type="button" value='Cerrar Ventana' onclick='cerrarVentana();'></td></tr>
        </table>
         <input type="hidden" name="wreporte"  id="wreporte"  value='<?=$html_pendientes_csv?>'>
    </form>

    <script>
    $.unblockUI();
    </script>
</body>
</html>