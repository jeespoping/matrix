<?php
include_once("conex.php");

//DILIGENCIAR PREANESTESIA
/*
DESCRIPCION:          Sofware que permite a un usuario diligenciar la preadnestesía de un paciente sin ingreso real lo cual implica que no tiene asignada una historia o ingreso.
AUTOR:                Camilo Zapata
FECHA DE CREACION:    2017-04-28
----------------------------------------------------------------------------------------------------------------------------------
                  ACTUALIZACIONES

16/03/2022 - Brigith Lagares: Se realiza estadarización del wemp_pmla.      
2018-08-08 camilo zapata: modificación de la función consultarPreAnestesiasRealizadas con el propósito de mejorar el rendimiento de la misma
2018-07-03 camilo zapata: se le pide al médico el servicio destino de dicha preanestesia, para garantizar que se asocie corectamente a los ingresos en ayudas
2017-11-22 camilo zapata: se cambia el manejo de los scrolls en los iframes, para que los medicos puedan agregar tantas filas como deseen en el formulario y que el botón guardar no se les pierda
2017-08-28 camilo zapata: se completa la condición en el query que consulta las preanestesias realizadas para que solo incluya el formulario 75
2017-08-14 camilo zapata: se adicionan funcionalidades para la adición de notas
2017-07-18 camilo zapata: se inserta en la 204 el numero de historia que quedó en el formulario grabado en lugar del que viene generado
2017-06-07 camilo zapata: se controla el doble click para que no haya errores con los consecutivos de la 204 y el formulario
2017-05-30 camilo zapata: la busqueda del consecutivo de historia temporal se cambia a un llamado asíncrono.
2017-05-24 camilo zapata: modificacion del software con la opción de consulta de la preanestesia realizada.
*/
//  EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------
if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}


include_once("root/comun.php");


$wactualiz='2022-03-16';
$conex          = obtenerConexionBD("matrix");
$wbasedato      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbdtcx         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
$wbdhce         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wbdcliame      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$wcodigoParti   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
$wccoInfor      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cco_informatica');
$wtalhuma       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
$wfecha         = date("Y-m-d");
$whora          = date("H:i:s");
$user_session   = explode('-',$_SESSION['user']);
$wuse           = $user_session[1];
$cco_usuario    = buscarCodigoCcoTalhuma( $_SESSION['user'] );
$caracteres = array( "á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","\\","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??","?£", "°", "-");
$caracteres2 = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute","&ntilde;","&Ntilde;","u","U","","","a","e","i","o","u","A","E","I","O","U","A","S","", "", "N", "N", "U", "", "");
//$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","","","a","e","i","o","u","A","E","I","O","U","A","S","", "", "N", "N", "U", "");

//------------------------------------> functiones <---------------------------------------
function calcularEdad( $fechaNacimiento ){
     $ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
    $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
    $ann1=($aa - $ann)/360;
    $meses=(($aa - $ann) % 360)/30;
    if ($ann1<1)
    {
        $dias1=(($aa - $ann) % 360) % 30;
        $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";
    }
    else
    {
        $dias1=(($aa - $ann) % 360) % 30;
        $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
    }
    return( $wedad );
}

function verificarDatosPreAnestesia( $historia, $ingreso ){

    global $conex;
    global $wbdhce;
    $query = " SELECT COUNT(*) cantidad
                 FROM {$wbdhce}_000036
                WHERE firpro = '000075'
                  AND firhis = '{$historia}'
                  AND firing = '{$ingreso}'
                  AND firfir = 'on'";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_assoc($rs);
    return( $row['cantidad'] );

}

function buscarCodigoCcoTalhuma( $user_session ){
    global $conex;
    global $wemp_pmla;
    global $wtalhuma;
    $cco_usutalhuma = "";
    $usuario_talhuma = empresaEmpleado($wemp_pmla, $conex, $user_session);
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

function empresaEmpleado($wemp_pmla, $conex, $cod_use_emp){
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

function construirDescripcionError( $historiaReal, $ingresoReal, $hisTemporal, $turnoCirugia ){
    global $conex, $wemp_pmla, $wbdhce, $wbasedato;
    $query = " SELECT movhis, moving
                 FROM {$wbdhce}_000075
                WHERE movcon = '7'
                  AND movdat = '{$turnoCirugia}'";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_assoc($rs);

    if( $historiaReal == $row['movhis'] ){
        //--> significa que lo malo es la firma.
        $mensaje = " LA FIRMA NO ESTA ASOCIADA CORRECTAMENTE ";
    }else if( $hisTemporal != $row['movhis'] ){
        //--> significa que en la grabacion la historia temporal quedó distinta en ambas tablas
        $mensaje = " ASOCIACIÓN DE HISTORIA TEMPORAL EQUIVOCADA<br> MOVHOS_000204: $hisTemporal --> HCE_000075: {$row['movhis']} ";
    }
    return( $mensaje );
}

function consultarCcosPosibles(){
    global $conex, $wemp_pmla, $wbasedato;
    $respuesta = array();
    $query = "SELECT ccocod, cconom
                FROM {$wbasedato}_000011
               WHERE ccoayu='on'
                 AND ccopre = 'on'";
    $rs    = mysql_query(  $query, $conex )or die( mysql_error()."->".$query);

    while( $row = mysql_fetch_assoc($rs) ){
         $respuesta[$row['ccocod']] = $row['cconom'];
    }
    $respuesta['cir'] = "CIRUGÍA";
    return( $respuesta );
}
//------------------------------------> functiones <---------------------------------------


//-------------------------------------> LLAMADOS AJAX <------------------------------------------
if( isset( $accion ) ){

    switch( $accion ){

        case 'obtenerHistoriaTemporal':
        {
            $respuesta = array("Error" => false, "Historia" => "");

            // --> Obtener valor del consecutivo
            $sqlConsec = "
            SELECT Detval
              FROM root_000051
             WHERE Detemp = '".$wemp_pmla."'
               AND Detapl = 'consecutivoHistoriaTemporalPrea'
            ";
            $resConsec = mysql_query($sqlConsec, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConsec):</b><br>".mysql_error());
            if($rowConsec = mysql_fetch_array($resConsec))
            {
                if(trim($rowConsec['Detval']) != '')
                {
                    $respuesta['Historia'] = trim($rowConsec['Detval'])+1;

                    // --> Actualizar consecutivo
                    $sqlActu = "
                    UPDATE root_000051
                       SET Detval = '".$respuesta['Historia']."'
                     WHERE Detapl = 'consecutivoHistoriaTemporalPrea'
                    ";
                    $resActu = mysql_query($sqlActu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActu):</b><br>".mysql_error());
                    if(!$resActu)
                        $respuesta['Error'] = true;

                    $respuesta['Historia'] = $respuesta['Historia']."C".$puestoTrabajo;
                }
                else
                    $respuesta['Error'] = true;
            }
            else
                $respuesta['Error'] = true;

            echo json_encode($respuesta);
            return;
        }

        case 'guardarRelacionPacienteHistoriaTemp':
        {
            $respuesta  = array("Error" => false, "Mensaje" => "");
            $ccoDestino = ( $ccoDestino == "cir" or $ccoDestino == "" ) ? "cir" : $ccoDestino;
            $sqlExiste  = "
            SELECT id
              FROM {$wbasedato}_000204
             WHERE Ahttur = '{$turno}'
               AND Ahtori = 'preanestesia'
            ";
            $resExiste = mysql_query($sqlExiste, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlExiste):</b><br>".mysql_error());
            if($rowExiste = mysql_fetch_array($resExiste))
            {
                // --> Actualizar relacion
                $guardarRel = "
                  UPDATE {$wbasedato}_000204
                   SET Medico = '{$wbasedato}',
                   Fecha_data = '".date('Y-m-d')."',
                    Hora_data = '".date("H:i:s")."',
                       Ahtdoc = '".$documento."',
                       Ahttdo = '".$tipoDoc."',
                       Ahthte = '".$numHistoriaTemp."',
                       Ahttur = '".$turno."',
                       Ahtest = 'on',
                       Ahtccd = '{$ccoDestino}'
                    Seguridad = 'C-".$wuse."'
                     WHERE id = '".$rowExiste['id']."'
                ";
                mysql_query($guardarRel, $conex) or die("<b>ERROR EN QUERY MATRIX(guardarRel) 1:</b><br>");
            }
            else
            {
                // --> Guardar relacion
                $guardarRel = "
                  INSERT INTO ".$wbasedato."_000204 ( Medico       ,   Fecha_data,   Hora_data,   Ahtdoc       ,   Ahttdo  ,   Ahthte  , Ahttur ,   Ahtest      ,   Ahtori      , Ahtccd ,  Seguridad )
                                             VALUES ('".$wbasedato."', '".date('Y-m-d')."', '".date("H:i:s")."', '".$documento."', '".$tipoDoc."','".$numHistoriaTemp."','".$turno."','on', 'preanestesia', '{$ccoDestino}', 'C-".$wuse."')";
                mysql_query($guardarRel, $conex) or die("<b>ERROR EN QUERY MATRIX(guardarRel) 1:</b><br>");
            }
            echo json_encode($respuesta);
            return;
        }

        case 'buscarNombrePaciente':
        {
            $data = array();
            $query = " SELECT turtur, turnom nombre, turfna fechaNacimiento, tureps entidad, turmed medicoRes
                         FROM {$wbdtcx}_000011
                        WHERE turtdo = '{$tipoDoc}'
                          AND turdoc = '{$documento}'
                        ORDER BY 1 desc
                        LIMIT 1";
            $rs   = mysql_query( $query, $conex );
            $num  = mysql_num_rows( $rs );
            if( $num == 0 ){
                $data['error'] = "no Encontrado";
            }else{

                $row = mysql_fetch_array($rs);
                if( $row['entidad'] == $wcodigoParti ){
                    $row['entidad'] = $row['entidad']." - PARTICULAR";
                }else{

                    $qresp = " SELECT empnom
                                 FROM {$wbdcliame}_000024
                                WHERE empcod = '{$row['entidad']}'";
                    $rsres = mysql_query($qresp,$conex);
                    $rowres= mysql_fetch_array( $rsres );
                    $row['entidad'] = $row['entidad']." - ".$rowres[0];
                }

                $queryP = " SELECT count(*)
                              FROM {$wbasedato}_000204
                             WHERE ahttdo = '{$tipoDoc}'
                               AND ahtdoc = '{$documento}'
                               AND ahttur = 'cir_{$row['turtur']}'";
                $rsP    = mysql_query( $queryP, $conex );
                $rowP   = mysql_fetch_array( $rsP );
                $data['preanestesiaPrevia'] = ( $rowP[0] > 0 ) ? "on" : "off";


                $data['nombre']          = utf8_encode($row['nombre']);
                $data['fechaNacimiento'] = $row['fechaNacimiento'];
                $data['entidad']         = $row['entidad'];
                $data['medicoRes']       = str_replace( $caracteres, $caracteres2, utf8_encode($row['medicoRes']) );
                $data['turno']           = $row['turtur'];
                $data['error']           = "";
            }
            echo json_encode($data);
            return;
        }

        case 'consultarPreAnestesiasRealizadas';
        {
            $ccosAyudas        = consultarCcosPosibles();
            $fecha             = ( isset( $fechaConsultada ) and $fechaConsultada != "" ) ? $fechaConsultada : date("Y-m-d");
            $responsables      = array();
            $condicionConsulta = ( $origenLLamado == "consultarPaciente" ) ? " turtdo = '{$tipoDocumento}' AND turdoc = '{$numDocumento}' AND turtur = '{$turno}' " : " a.fecha_data = '{$fecha}' ";
            $condicion_24      = ( $origenLLamado == "consultarPaciente" ) ? " ahttdo = '{$tipoDocumento}' AND ahtdoc = '{$numDocumento}' AND ahttur = concat( 'cir_', '{$turno}' ) " : " fecha_data = '{$fecha}' ";

            $horaAuxiliar   = (string)date("H_i_s");
            $nombreTemporal = "204_temp_".$horaAuxiliar;

            $query = " CREATE TEMPORARY TABLE {$nombreTemporal}
                       (INDEX idx( ahttdo, ahtdoc ) )
                       SELECT ahtdoc, ahttdo, ahthte, ahttur, ahthis, ahting, ahtahc, ahtori, ahtccd
                         FROM {$wbasedato}_000204
                        WHERE {$condicion_24}
                          AND ahtori = 'preanestesia'
                          AND ahtahc = 'on'
                        UNION
                        SELECT ahtdoc, ahttdo, ahthte, ahttur, ahthte as ahthis, '1' AS ahting, ahtahc, ahtori, ahtccd
                         FROM {$wbasedato}_000204
                        WHERE {$condicion_24}
                          AND ahtori = 'preanestesia'
                          AND ahtahc != 'on'";
            $rs    = mysql_query( $query, $conex );

            $query = " SELECT ahtdoc, ahttdo, ahthte, b.Turnom, b.Turmed, b.Turfec, b.Turhin, ahttur, b.turfna, b.Tureps, ahthis, ahting, ahtahc, c.fecha_data as fecha_dataF, c.hora_data as hora_dataF, ahtccd
                         FROM {$nombreTemporal} a
                        INNER  JOIN
                              {$wbdtcx}_000011 b ON (    turtdo = ahttdo
                                                     AND turdoc = ahtdoc
                                                     AND ahttur = concat( 'cir_', turtur ) )
                        INNER JOIN
                              {$wbdhce}_000036 c ON ( firhis = ahthis AND firing = ahting AND firpro = '000075' )";

            $rs    = mysql_query( $query, $conex ) or die( mysql_error());

            if( mysql_num_rows( $rs ) > 0 ){

                $qresp = " SELECT empnom, empcod
                             FROM {$wbdcliame}_000024
                            WHERE 1";
                $rsres = mysql_query($qresp,$conex);
                while( $rowres= mysql_fetch_array( $rsres ) ){
                    $responsables[$rowres[1]] = $rowres[0];
                }
                $responsables[$wcodigoParti] = "PARTICULAR";


                echo "
                    <table style='width:100%;'>
                        <thead>
                            <tr class='encabezadotabla'>
                                <th align='center' width='5%'> Tipo<br>Documento </th>
                                <th align='center' width='12%'>Documento</th>
                                <th align='center' width='12%'>Historia</th>
                                <th align='center' width='30%'>Nombre</th>
                                <th align='center' width='8%'>Fecha Cirugia</th>
                                <th align='center' width='8%'>Hora Inicio</th>
                                <th align='center' width='30%'>M&eacute;dico cirugia.</th>
                                <th align='center' width='14%'>Ver</th>
                                <th align='center' width='14%'>Adicionar<br> Nota</th>
                                <th align='center' width='14%'>Anular</th>
                            </tr>
                        </thead>
                        <tbody>";

                    while( $row = mysql_fetch_array($rs) ){

                        $classError         = "";
                        $titleError         = "";
                        $mostrarDescripcion = "";
                        if( $row['ahtahc'] == "on" ){
                            $hisTemporal   = $row['ahthte'];
                            $row['ahthte'] = $row['ahthis'];
                            $ingreso       = $row['ahting'];
                            $historia      = $row['ahthis']." - ".$row['ahting'];
                            $cantidadFirMados = verificarDatosPreAnestesia( $row['ahthis'], $row['ahting'] );
                            if( $cantidadFirMados < 1 ){
                                $classError = " fondoAmarillo claseError ";
                                $mensajeError = '<div class=fila2>';
                                     $mensajeError .= '<span class=subtituloPagina2><font size=2>No se asoció la preanesteia al ingreso del paciente. comunicarse con informática</font></span><br>';
                                $mensajeError .= '</div>';
                                $titleError = " title='$mensajeError' ";
                                if( $wccoInfor == $cco_usuario ){

                                    $turnoCirugia = explode("_",  $row['ahttur'] );
                                    $turnoCirugia = $turnoCirugia[1];
                                    $mensajeErrorDetalle = construirDescripcionError( $row['ahthis'], $row['ahting'], $hisTemporal, $turnoCirugia );
                                    $mostrarDescripcion = " onclick=' alert(\" este usuario es de informatica \") ' ";
                                    $mensajeError = '<div class=fila2>';
                                         $mensajeError .= '<span class=subtituloPagina2><font size=2>'.$mensajeErrorDetalle.'</font></span><br>';
                                    $mensajeError .= '</div>';
                                    $titleError = " title='$mensajeError' ";
                                }
                            }
                        }else{
                            $historia      = " &nbsp; ";
                            $ingreso       = "1";
                        }

                        $row['Turnom'] = $row['Turnom'] ;
                        $row['Turnom'] = strtoupper( $row['Turnom'] );
                        $row['Turmed'] = $row['Turmed'];
                        $row['Turmed'] = str_replace("-", "", $row['Turmed']);
                        $row['Tureps'] = $responsables[$row['Tureps']];
                        $edad          = calcularEdad( $row['turfna'] );
                        $ccoPreanestesia = ( $row['ahtccd'] == "cir" ) ? "CIRUGIA" : $ccosAyudas[$row['ahtccd']];

                        echo "
                            <tr tipo='tr_base_nuevo' class='fila1 $classError'  {$titleError} {$mostrarDescripcion} >
                                <td align='center'>{$row['ahttdo']}</td>
                                <td align='center'>{$row['ahtdoc']}</td>
                                <td align='center'>{$historia}</td>
                                <td align='left'>{$row['Turnom']}</td>
                                <td align='center'>{$row['Turfec']}</td>
                                <td align='center'>{$row['Turhin']}</td>
                                <td align='left'>{$row['Turmed']}</td>
                                <td align='center' width='25px'><input type='radio' name='rd_ver' onclick='consultarPreAnestesia( this, \"{$row['ahttdo']}\", \"{$row['ahtdoc']}\", \"{$row['ahttur']}\", \"{$row['ahthte']}\", \"{$row['Tureps']}\", \"{$edad}\", \"{$row['Turnom']}\", \"{$row['Turmed']}\", \"{$ingreso}\", \"{$ccoPreanestesia}\" )'></td>
                                <td align='center' width='25px'><input type='radio' name='rd_add_nota' onclick='AdicionarNota( this, \"{$row['ahttdo']}\", \"{$row['ahtdoc']}\", \"{$row['ahttur']}\", \"{$row['ahthte']}\", \"{$row['fecha_dataF']}\", \"{$row['hora_dataF']}\", \"{$ingreso}\", \"{$row['Turnom']}\", \"{$row['Turmed']}\", \"{$row['Tureps']}\", \"{$edad}\" )'></td>
                                <td align='center' width='60px' nowrap='nowrap'>&nbsp;Anular:&nbsp;<input type='radio' name='rd_anular' onclick='anularPreAnestesia( \"{$row['ahttdo']}\", \"{$row['ahtdoc']}\", \"{$row['ahttur']}\", \"{$row['ahthte']}\" )'></td>
                            </tr> ";

                    }
                 echo "
                          </tbody>
                   </table> ";
            }else{
                echo "<br>
                    <img src='../../images/medical/root/Advertencia.png'/>
                    <br><br>No se han realizado preAnestesias el dia: {$fecha}<br><br>";
            }
            return;
        }

        case 'anularPreAnestesia':
        {
            //---> verificar si ya fue admitido
            $query = " SELECT ahtahc admitido, ahthis historia, ahting ingreso, id
                         FROM {$wbasedato}_000204
                        WHERE ahttdo = '{$tipoDoc}'
                          AND ahtdoc = '{$documento}'
                          AND ahthte = '{$historiaTemporal}'
                          AND ahttur = '{$turnoCirugia}'
                          AND ahtori = 'preanestesia'";

            $rs    = mysql_query($query,$conex);
            $row   = mysql_fetch_assoc( $rs );
            if( $row['admitido'] == "on" ){

                $data['error'] = 1;
                $data['mensaje'] = "Paciente Actualmente Admitido";

            }else{

                $query = " DELETE
                             FROM {$wbdhce}_000075
                            WHERE movhis = '{$historiaTemporal}'
                              AND moving = '1'";
                $rs    = mysql_query( $query, $conex );

                $query = " DELETE
                             FROM {$wbasedato}_000204
                            WHERE id = '{$row['id']}'";
                $rs    = mysql_query( $query, $conex );

                $data['error'] = 0;
                $data['mensaje'] = "Anulación realizada";

            }
            echo json_encode($data);
            return;
        }
    }
}
// -----------------------------------> FIN LLAMADOS AJAX <---------------------------------------
?>
<html>
    <head>
      <title>PreAnestesia</title>
    </head>
    <meta charset="UTF-8">
    <link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

    <style type="text/css">
        // --> Estylo para los placeholder
        /*Chrome*/
        [tipo=obligatorio]::-webkit-input-placeholder {color:red; background:lightyellow;font-size:2rem}
        /*Firefox*/
        [tipo=obligatorio]::-moz-placeholder {color:red; background:lightyellow;font-size:2rem}
        /*Interner E*/
        [tipo=obligatorio]:-ms-input-placeholder {color:red; background:lightyellow;font-size:2rem}
        [tipo=obligatorio]:-moz-placeholder {color:red; background:lightyellow;font-size:2rem}

        .botonteclado {
            border:             1px solid #9CC5E2;
            background-color:   #E3F1FA;
            width:              3.3rem;
            height:             3rem;
            font-size:          4rem;
            font-weight:        normal;
            border-radius:      0.4em;
        }
        .botonteclado2 {
            border:             1px solid #333333;
            background-color:   #E3F1FA;
            width:              3.3rem;
            height:             3rem;
            font-size:          4rem;
            font-weight:        bold;
            border-radius:      0.4em;
        }
        .botonteclado:hover {
            position:           relative;
            top:                1px;
            left:               1px;
            background-color:   #75C3EB;
            color:              #ffffff;
        }
        .botonteclado2:hover {
            position:           relative;
            top:                1px;
            left:               1px;
            background-color:   #75C3EB;
            color:              #ffffff;
        }

        .div_contenedor{
            padding-right:    2%;
            padding-left:     2%;
            padding-bottom:   5%;
            padding-top:      2%;
            border-radius:    0.4em;
            /*border-style:     solid;
            border-width:     2px;*/
            width:            90%;
            /*max-height: 500px;*/
        }
        .tbl_prea_realizadas{
            max-height: 100%;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
        }
        .claseError{
            cursor: pointer;
        }
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" /> <!-- Tooltip -->
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
    <script src="/javascripts/application.js" type="text/javascript" charset="utf-8" async defer>
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

        $(function(){

            $("#accordionPrincipal").accordion({
                collapsible: false
            });
            $( "#accordionPrincipal" ).accordion( "option", "icons", {} );

            $(".radio").buttonset();

            // --> Activar teclado numerico
            $(".botonteclado").parent().hide();
            $("#botonBorrar").css("width","6.8rem").parent().attr("colspan", "2");

            // --> Esto es para cerrar el teclado cuando se de click en un area fuera de este.
            $(document).click(function(e){
                elemenClick = e.target;
                clase       = $(elemenClick).attr("class");
                if(clase != "botonteclado" && clase != "botonteclado2" && e.target.id != 'tecladoFlotante' && e.target.id != 'numDocumento' && e.target.id != 'inputNombrePaciente' && e.target.id != 'edadPaciente')
                {
                    $('#tecladoFlotante').hide();
                }
            });

            $("#fechaConsultada").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                maxDate: '0'
                , onSelect: function(dateText, inst ) {
                    consultarPreAnestesiasRealizadas('', '');
                }
            });

            parpadear();
            ajustarResolucioPantalla();
            consultarPreAnestesiasRealizadas('', '');

        });

        function AdicionarNota( chk, tipoDocumento, documento, turno, historiaTemporal, Fecha_data, Hora_data, ingreso, nombrePaciente,  medico, entidad, edad ){

            var formTipoOrden   = '000075';
            var numHistoriaTemp = historiaTemporal;
            var urlform         = '/matrix/hce/procesos/HCE_Notas.php?pre_anestesia=on&ok=1&notas=1&empresa='+<?=$wbdhce?>+'&wemp_pmla='+$('#wemp_pmla').val()+'&wdbmhos='+<?=$wbasedato?>+'&wservicio=*&wformulario='+formTipoOrden+'&wcedula='+documento+'&wtipodoc='+tipoDocumento+'&whis='+historiaTemporal+'&wing='+ingreso+'&wfecha_data='+Fecha_data+'&whora_data='+Hora_data;

            infoPaciente = ""
            +"<fieldset align='center' style='padding:6px;'>"
            +"<legend class='fieldset'>Informaci&oacute;n del paciente:</legend>"
            +"<table width=100% id='infoPacEnTriage'>"
                +"<tr>"
                    //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                    +"<td class=fila1>Documento</td>"
                    +"<td class=fila2><font size='4'>"+tipoDocumento+"-"+documento+"</font></td>"
                    +"<td class=fila1>Paciente</td><td class='fila2'><font size='5'>"+nombrePaciente+"</font></td>"
                +"</tr>"
                +"<tr>"
                    //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                    +"<td class=fila1>Edad</td>"
                    +"<td class=fila2><font size='4'>"+edad+" a&ntilde;os</font></td>"
                    +"<td class=fila1>Entidad</td><td class=fila2><font size='4'>"+entidad+"</font></td>"
                +"</tr>"
                +"<tr>"
                    //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                    +"<td class=fila1>Medico</td>"
                    +"<td class=fila2 colspan=2><font size='4'>"+medico+"</font></td>"
                +"</tr>"
                +"<tr>"
                    //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                    +"<td class=fila1>HISTORIA TEMPORAL</td>"
                    +"<td class=fila2 colspan=2><font size='4'>"+numHistoriaTemp+"</font></td>"
                +"</tr>"
            +"</table>"
            +"</fieldset>";

            // --> Cargar el iframe
            $("#divFormularioHce").html("<div align=center>"+infoPaciente+"</div><iframe id='frameFormularioTriage' name='frameFormularioTriage' src='"+urlform+"' height='900px' width='950px' scrolling=yes frameborder='0'></iframe>");
            //$("#divFormularioHce").html("<iframe id='frameFormularioTriage' name='frameFormularioTriage' src='"+urlform+"' height='500px' width='750px' scrolling=yes frameborder='0'></iframe>");
            var seleccionTurCir = "";
            var guardado        = false;
            // --> Validar si ya se guardo el formulario
            setTimeout( function(){
                frame1 = $('#frameFormularioTriage').contents();
                $(frame1).contents().find("iframe").height(3500);

                // -->  Cada segundo se valida si existe el elemento "tipoLGOK" con el texto "DATOS GRABADOS OK", si existen
                //      esto indica que se grabo el formulario con exito.
                interval = setInterval(function(){

                    var botonGrabadoOk  = $(frame1).contents().find("iframe").contents().find("#tipoLGOK");
                    var texto           = $.trim($(botonGrabadoOk).text());
                    var selectAux       = $(frame1).contents().find("iframe").contents().find("select").eq(0).val();
                    var numHistoriaTemp2 = $(frame1).contents().find("iframe").contents().find("input[name='whis']").val();
                        if( selectAux != "Seleccione"  )
                            seleccionTurCir ="cir_"+selectAux;
                    if(texto != "" && texto.search("DATOS GRABADOS OK") >= 0)
                    {
                        //--> en seleccionturcir se captura el turno de cirugia para el cual se está haciendo la preanestesia
                        if( !guardado ){                                                                 //2017-07-18--> se inserta en la 204 el numero de historia que quedó en el formulario grabado
                            guardarRelacionPacienteHistoriaTemp("preAnes", tipoDocumento, numDocumento, numHistoriaTemp2, seleccionTurCir, $("#inp_cco_destino").val() );
                            guardado = true;
                            $("#guardado").val("on");
                        }
                        $("#divPreAnesOk").show();
                    }
                }, 200);

            }, 1000);

            var htmlRelojTemp = "<table width='100%'><tr>"
            +"<td style='font-family:verdana;font-size: 11pt;color: #4C4C4C;font-weight:bold'>Formulario Preanestesia</td>"
            +"</tr></table>";



            // --> Ventana dialog para cargar el iframe
            $("#divFormularioHce").dialog({
                show:{
                    effect: "blind",
                    duration: 0
                },
                hide:{
                    effect: "blind",
                    duration: 100
                },
                width:  'auto',
                dialogClass: 'fixed-dialog',
                modal: true,
                title: htmlRelojTemp,
                buttons:[
                {
                    text: "Cerrar",
                    icons:{
                            primary: "ui-icon-heart"
                    },
                    click: function(){
                        if( $("#guardado").val() == "off" ){
                            if( confirm( "\u00bfEST\u00c1 SEGURO QUE QUIERE CERRAR SIN GUARDAR EL FORMULARIO?") ){
                                $(this).dialog("close");
                                $(this).dialog("destroy");
                                $(obj).removeAttr("disabled");
                                $("[name='select_cco_destino'] > option[value='']").attr("selected","selected");
                                $("#inp_cco_destino").val("");
                            }
                        }else{
                            $(this).dialog("close");
                            $(this).dialog("destroy");
                            $(obj).removeAttr("disabled");
                            $("#guardado").val("off");
                            $("[name='select_cco_destino'] > option[value='']").attr("selected","selected");
                            $("#inp_cco_destino").val("");
                        }
                    }
                }],
                close: function( event, ui ) {
                    ///-->
                }
            });
            $("#divFormularioHce").parent().css({"z-index":"999"}).next().css({"z-index":"998"});

        }

        //-------------------------------------------------------------------
        //  --> Reiniciar pantalla para permitir ingresar un nuevo turno
        //-------------------------------------------------------------------
        function reiniciarPantalla(){
            // --> Limpiar campos
            $("#numDocumento").val("");
            $("#inputNombrePaciente").val("");
            $("#turnoConPreAnestesia").val("");
            $("#pacienteConPreAnestesia").val("");
            $("#inp_cco_destino").val("");
            $("#edadPaciente").val("");
            $("[name=tipDocumento]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
            $("[name=categoriaEmp]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
            $("[name=tipoEdad]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
            $("#divMensaje").html("&nbsp;");
            $("#textoLector").val("");
            $("#guardado").val("off");
            $("#divPreAnesOk").hide();
            $('#tecladoFlotante').hide();
        }

        function salir(){
            window.close();
        }

        function checkearRadio(elemento){

            $("#numDocumento").val("");
            $("#inputNombrePaciente").val("");
            $("#divPreAnesOk").hide();
            nameRadios = $(elemento).prev().attr("name");
            $("[name="+nameRadios+"]").removeAttr("checked");
            $(elemento).prev().attr("checked", "checked");
            //2014-11-06 Para que no permita ingresar caracteres especiales en los documentos
            $("#numDocumento").keyup(function(){
                if ($(this).val() !=""){//2015-05-22
                    $(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
                    tam =  $(this).val().length;
                    if( tam > 11 ){
                        $(this).val( $(this).val().substring( 0, tam - 1) );
                    }
                }
            });
            tipoAlfaNumerico = $(elemento).prev().attr("alfanumerico");

            $("#numDocumento").keyup(function(){
                if ($(this).val() !=""){//2015-05-22
                    if( tipoAlfaNumerico == "on" ){
                        $(this).val($(this).val().replace(/[^\w\d\ ]/g, ""));
                    } else {
                        $(this).val($(this).val().replace(/[^\d\ ]/g, ""));
                    }
                    tam =  $(this).val().length;
                    if( tam > 11 ){
                        $(this).val( $(this).val().substring( 0, tam - 1) );
                    }
                }
            });
        }

        //-------------------------------------------------------------------
        //  --> Funcion que obtiene el nombre del paciente
        //-------------------------------------------------------------------
        function obtenerNombrePaciente(){
            return;
            if($("[name=tipDocumento]:checked").val() != undefined && $("#numDocumento").val() != ''){
                setTimeout(function(){
                    $.post("turnero.php",
                    {
                        consultaAjax:           '',
                        accion:                 'obtenerNombrePaciente',
                        wemp_pmla:              $('#wemp_pmla').val(),
                        numDocumento:           $("#numDocumento").val(),
                        tipDocumento:           $("[name=tipDocumento]:checked").val()
                    }, function(data){
                        data.nombrePac = $.trim(data.nombrePac);
                        nombrePac = ((data.nombrePac != '') ? data.nombrePac : "" );
                        $("#inputNombrePaciente").val(nombrePac);
                    }, 'json');
                }, 200);
            }
        }

        //------------------------------------------------------------------
        //--> Funcion que recoge los datos dilegenciados, permite la generación del número temporal de historia y habilita el formulario de preanestesia.
        function seleccionarDestinoPreanestesia( obj ){

            $( obj ).attr( "disabled", "disabled" );

            if($("[name=tipDocumento]:checked").val() == undefined){
                $("#divMensaje").html("Debe seleccionar el tipo de documento.");
                 $(obj).removeAttr("disabled");
                return;
            }

            // --> Si no han ingresado el numero de documento
            if($("#numDocumento").val() == ""){
                $("#divMensaje").html("Debe ingresar el numero de documento.");
                 $(obj).removeAttr("disabled");
                return;
            }

            // --> Si no han ingresado el nombre
            if($.trim($("#inputNombrePaciente").val()) == ""){
                $("#divMensaje").html("Debe ingresar el nombre.");
                 $(obj).removeAttr("disabled");
                return;
            }
            // --> Ventana dialog para cargar el iframe
            $("#divCcoDestino").dialog({
                show:{
                    effect: "blind",
                    duration: 0
                },
                hide:{
                    effect: "blind",
                    duration: 100
                },
                width:  300,
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Elija el destino de la preanestesia",
                buttons:[
                {
                    text: "guardar",
                    icons:{
                            primary: "ui-icon-heart"
                    },
                    click: function(){
                        seleccionado = $("[name='select_cco_destino'] > option:selected").val();
                        if( seleccionado != "" ){
                            $("#inp_cco_destino").val( seleccionado );
                            abrirPreAnestesia( obj );
                            $(this).dialog("destroy");
                        }else{
                            if( confirm( "¿ESTÁ SEGURO QUE QUIERE TERMINAR EL PROCESO?") ){
                                $(this).dialog("destroy");
                                $("#inp_cco_destino").val("");
                                $("[name='select_cco_destino'] > option[value='']").attr("selected","selected");
                            }
                        }

                    }
                }],
                close: function( event, ui ) {
                    ///-->
                    if( !confirm("¿ESTÁ SEGURO QUE QUIERE TERMINAR EL PROCESO?") ){
                        $("#inp_cco_destino").val("");
                        seleccionarDestinoPreanestesia();
                    }else{
                        $("#inp_cco_destino").val("");
                        $("[name='select_cco_destino'] > option[value='']").attr("selected","selected");
                        $("#inp_cco_destino").val("");
                    }
                }
            });
        }
        function abrirPreAnestesia( obj ){

            if( $("#pacienteConPreAnestesia").val() == "on" ){
                turnoPre = $("#turnoConPreAnestesia").val();
                consultarPreAnestesiasRealizadas( "consultarPaciente", turnoPre );
            }

            $("#divMensaje").html("");
            tipoDocumento  = $("[name=tipDocumento]:checked").val();
            numDocumento   = $("#numDocumento").val();
            nombrePaciente = $("#inputNombrePaciente").val();
            edad           = $("#inputEdadPaciente").val();
            medico         = $("#inputMedico").val();
            entidad        = $("#inputEntidad").val();
            //----------------------------------------------------------------------
            // --> Funcion que abre el formulario hce para realizar el triage
            //----------------------------------------------------------------------
            $.ajax({
                type: "POST",
                url : "preAnestesia.php",
                async: false,
                data:{
                    consultaAjax:           '',
                    accion:                 'obtenerHistoriaTemporal',
                    wemp_pmla:              $('#wemp_pmla').val(),
                    puestoTrabajo:          "preAnes"
                },
                success:function(respuesta){
                    $("#divFormularioHce").dialog("destroy");

                    if(respuesta.Error)
                    {
                        jAlert("<span style='color:red'>Error obteniendo el número de historia temporal.<br>Por favor reporte la inconsistencia.</span>", "Mensaje");
                        return;
                    }

                    var formTipoOrden   = '000075';
                    var numHistoriaTemp = 'TEMP'+$.trim(respuesta.Historia);
                    var urlform         = '/matrix/hce/procesos/HCE.php?accion=M&ok=0&empresa='+<?=$wbdhce?>+'&wemp_pmla='+$('#wemp_pmla').val()+'&wdbmhos='+<?=$wbasedato?>+'&wformulario='+formTipoOrden+'&wcedula='+numDocumento+'&wtipodoc='+tipoDocumento+'&whis='+numHistoriaTemp+'&wing=1';
                    var ccoDestino  = $("[name='select_cco_destino'] > option:selected").html();
                    var selectClon  = $("[name='select_cco_destino']").parent().html();
                    infoPaciente = ""
                    +"<fieldset align='center' style='padding:6px;'>"
                    +"<legend class='fieldset'>Informaci&oacute;n del paciente:</legend>"
                    +"<table width=100% id='infoPacEnTriage'>"
                        +"<tr>"
                            //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                            +"<td class=fila1>Documento</td>"
                            +"<td class=fila2><font size='4'>"+tipoDocumento+"-"+numDocumento+"</font></td>"
                            +"<td class=fila1>Paciente</td><td class='fila2'><font size='5'>"+nombrePaciente+"</font></td>"
                        +"</tr>"
                        +"<tr>"
                            //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                            +"<td class=fila1>Edad</td>"
                            +"<td class=fila2><font size='4'>"+edad+" a&ntilde;os</font></td>"
                            +"<td class=fila1>Entidad</td><td class=fila2><font size='4'>"+entidad+"</font></td>"
                        +"</tr>"
                        +"<tr>"
                            //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                            +"<td class='fila1'>Medico</td>"
                            +"<td class='fila2'><font size='4'>"+medico+"</font></td>"
                            +"<td class='fila2'>&nbsp;</td>"
                            +"<td class='fondoAmarillo' align='center'><font size='4' colspan='2'>SERVICIO DESTINO</font></td>"
                        +"</tr>"
                        +"<tr>"
                            //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                            +"<td class=fila1>HISTORIA TEMPORAL</td>"
                            +"<td class=fila2><font size='4'>"+numHistoriaTemp+"</font></td>"
                            +"<td class=fila2>&nbsp;</td>"
                            +"<td class='fondoAmarillo' align='center' id='td_cco_destino'>"+selectClon+"</td>"
                        +"</tr>"
                    +"</table>"
                    +"</fieldset>";

                    // --> Cargar el iframe
                    $("#divFormularioHce").html("<div align=center>"+infoPaciente+"</div><iframe id='frameFormularioTriage' name='frameFormularioTriage' src='"+urlform+"' height='900px' width='950px' scrolling='no' frameborder='0'></iframe>");
                    $("#td_cco_destino > select > option[value='"+$("#inp_cco_destino").val()+"']").attr("selected","selected")
                    var seleccionTurCir = "";
                    var guardado        = false;
                    // --> Validar si ya se guardo el formulario
                    setTimeout( function(){
                        frame1 = $('#frameFormularioTriage').contents();
                        $(frame1).contents().find("iframe").height(900);
                        $(frame1).contents().find("iframe").attr("scrolling", "yes");

                        // -->  Cada segundo se valida si existe el elemento "tipoLGOK" con el texto "DATOS GRABADOS OK", si existen
                        //      esto indica que se grabo el formulario con exito.
                        interval = setInterval(function(){

                            var botonGrabadoOk  = $(frame1).contents().find("iframe").contents().find("#tipoLGOK");
                            var texto           = $.trim($(botonGrabadoOk).text());
                            var selectAux       = $(frame1).contents().find("iframe").contents().find("select").eq(0).val();
                            var numHistoriaTemp2 = $(frame1).contents().find("iframe").contents().find("input[name='whis']").val();
                                if( selectAux != "Seleccione"  )
                                    seleccionTurCir ="cir_"+selectAux;
                            if(texto != "" && texto.search("DATOS GRABADOS OK") >= 0)
                            {
                                //--> en seleccionturcir se captura el turno de cirugia para el cual se está haciendo la preanestesia
                                if( !guardado ){                                                                 //2017-07-18--> se inserta en la 204 el numero de historia que quedó en el formulario grabado
                                    guardarRelacionPacienteHistoriaTemp("preAnes", tipoDocumento, numDocumento, numHistoriaTemp2, seleccionTurCir, $("#inp_cco_destino").val() );
                                    guardado = true;
                                    $("#guardado").val("on");
                                    $("[name='select_cco_destino'] > option[value='']").attr("selected","selected");
                                    $("#inp_cco_destino").val("");
                                }
                                $("#divPreAnesOk").show();
                            }
                        }, 200);

                    }, 1000);

                    var htmlRelojTemp = "<table width='100%'><tr>"
                    +"<td style='font-family:verdana;font-size: 11pt;color: #4C4C4C;font-weight:bold'>Formulario Preanestesia</td>"
                    +"</tr></table>";



                    // --> Ventana dialog para cargar el iframe
                    $("#divFormularioHce").dialog({
                        show:{
                            effect: "blind",
                            duration: 0
                        },
                        hide:{
                            effect: "blind",
                            duration: 100
                        },
                        width:  'auto',
                        dialogClass: 'fixed-dialog',
                        modal: true,
                        title: htmlRelojTemp,
                        buttons:[
                        {
                            text: "Cerrar",
                            icons:{
                                    primary: "ui-icon-heart"
                            },
                            click: function(){
                                if( $("#guardado").val() == "off" ){
                                    if( confirm( "\u00bfEST\u00c1 SEGURO QUE QUIERE CERRAR SIN GUARDAR EL FORMULARIO?") ){
                                        $(this).dialog("close");
                                        $(this).dialog("destroy");
                                        $(obj).removeAttr("disabled");
                                    }
                                }else{
                                    $(this).dialog("close");
                                    $(this).dialog("destroy");
                                    $(obj).removeAttr("disabled");
                                    $("#guardado").val("off");
                                }
                            }
                        }],
                        close: function( event, ui ) {
                            ///-->
                        }
                    });
                    $("#divFormularioHce").parent().css({"z-index":"999"}).next().css({"z-index":"998"});
                },
                dataType: "json"
            });
        }

        //------------------------------------------------------------------------------
        // --> Guardar informacion de la relacion del paciente y la historia temporal
        //------------------------------------------------------------------------------
        function guardarRelacionPacienteHistoriaTemp(turno, tipoDoc, documento, numHistoriaTemp, turnoCirugia, ccoDestino){
            $.post("preAnestesia.php",
            {
                consultaAjax   : '',
                accion         : 'guardarRelacionPacienteHistoriaTemp',
                wemp_pmla      : $('#wemp_pmla').val(),
                numHistoriaTemp: numHistoriaTemp,
                documento      : documento,
                tipoDoc        : tipoDoc,
                turno          : turnoCirugia,
                ccoDestino     : ccoDestino
            }, function(respuesta){
                consultarPreAnestesiasRealizadas('', '');
            }, 'json');
        }


        //------------------------------------------------------------------------------
        // --> Consultar los datos del paciente y verificación de turno.
        //------------------------------------------------------------------------------
        function buscarNombrePaciente( obj ){

            tipoDoc    = $("[name=tipDocumento]:checked").val();
            documento  = $("#numDocumento").val();
            if( $.trim(documento) == "" ){
                $("#inputNombrePaciente").val( "" );
                return;
            }

            $.post("preAnestesia.php",
            {
                consultaAjax:           '',
                accion:                 'buscarNombrePaciente',
                wemp_pmla:              $('#wemp_pmla').val(),
                documento:              documento,
                tipoDoc:                tipoDoc
            }, function(data){
                if( data.error == "no Encontrado" ){
                    $("#inputNombrePaciente").val( "" );
                    alerta( "No existen cirugias programadas para este paciente" );
                    $("#botonAceptar").attr("disabled","disabled");
                    return;
                }else{
                    $("#botonAceptar").removeAttr("disabled");
                    $("#inputNombrePaciente").val( data.nombre );
                    $("#inputEdadPaciente").val( calcular_edad(data.fechaNacimiento) );
                    $("#inputMedico").val( data.medicoRes );
                    $("#inputEntidad").val( data.entidad );
                    if( data.preanestesiaPrevia == "on" ){
                        consultarPreAnestesiasRealizadas( "consultarPaciente", data.turno );
                    }
                    $("#pacienteConPreAnestesia").val( data.preanestesiaPrevia );
                    $("#turnoConPreAnestesia").val( data.turno );
                }
            },
            "json");
        }

        function calcular_edad( fecha ){
            var today = new Date();
            var birthDate = new Date( fecha );
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
           return(age);
        }


        function alerta( txt ){
            $("#textoAlerta").text( txt );
            $.blockUI({ message: $('#msjAlerta') });
                setTimeout( function(){
                    $.unblockUI();
                }, 3000 );
        }

        function parpadear(){
            $('#div_intermitente').fadeIn(500).delay(250).fadeOut(500, parpadear)
        }

        function ajustarResolucioPantalla(){
            var height = 0;
            var width  = 0;
            if (self.screen){     // for NN4 and IE4
                width   = screen.width;
                height  = screen.height
            }
            else
                if (self.java){   // for NN3 with enabled Java
                    var jkit = java.awt.Toolkit.getDefaultToolkit();
                    var scrsize = jkit.getScreenSize();
                    width   = scrsize.width;
                    height  = scrsize.height;
                }

            width   = width*0.99;
            height  = height*0.90;


            if(width > 0 && height > 0)
                $("#accordionPrincipal").css({"width":width});
            else
                $("#accordionPrincipal").css({"width": "100 %"});


            $("#div_contenedor_2").height("900");
        }

        function consultarPreAnestesiasRealizadas( origenLLamado, turno ){

            if( origenLLamado != "" ){
                tipoDocumento  = $("[name=tipDocumento]:checked").val();
                numDocumento   = $("#numDocumento").val();
            }else{
                tipoDocumento = "";
                numDocumento  = "";
            }

            $("#div_espere").show();
            $("#tbl_prea_realizadas").hide();
            $.post("preAnestesia.php",
            {
                consultaAjax:           '',
                accion:                 'consultarPreAnestesiasRealizadas',
                wemp_pmla:              $('#wemp_pmla').val(),
                fechaConsultada:        $("#fechaConsultada").val(),
                origenLLamado:          origenLLamado,
                tipoDocumento:          tipoDocumento,
                numDocumento:           numDocumento,
                turno:                  turno

            }, function(data){
                $("#tbl_prea_realizadas").html(data);
                $("#tbl_prea_realizadas").show();
                $("#div_espere").hide();
                $(".claseError").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.55, left: -50 });
                if( origenLLamado == "consultarPaciente" ){
                    alerta( "ESTE PACIENTE YA TIENE UNA PREANESTESIA REALIZADA, POR FAVOR ADICIONE LAS NOTAS PERTINENTES ");
                    $("input[type='radio'][name='rd_add_nota']").click();
                }
            });
        }

        function anularPreAnestesia( tipoDocumento, documento, turnoCirugia, historiaTemporal ){

            if( confirm( "Desea anular la admisi\u00f3n?" ) ){
                continuar = true;
            }else{
                continuar = false;
                return;
            }

           $.post("preAnestesia.php",
            {
                consultaAjax:           '',
                accion:                 'anularPreAnestesia',
                wemp_pmla:              $('#wemp_pmla').val(),
                tipoDoc:                tipoDocumento,
                documento:              documento,
                historiaTemporal:       historiaTemporal,
                fechaConsultada:        $("#fechaConsultada").val(),
                turnoCirugia:           turnoCirugia
            }, function(data){
                alerta(data.mensaje);
                consultarPreAnestesiasRealizadas('','');
            },
            "json"
            );
        }

        function consultarPreAnestesia( obj, tipoDocumento, documento, turnoCirugia, historiaTemporal, entidad, edad, nombrePaciente, medico, ingreso, servicioDestino = "" ){

            var formTipoOrden   = '000075';
            var urlform         = '/matrix/hce/reportes/HCE_ImpPreIngreso.php?empresa='+<?=$wbdhce?>+'&wemp_pmla='+$('#wemp_pmla').val()+'&wdbmhos='+<?=$wbasedato?>+'&CLASE=C&wcedula='+documento+'&wtipodoc='+tipoDocumento+'&wfor1=000075&wfor2=000339&whis='+historiaTemporal+'&wing='+ingreso;

            infoPaciente = ""
                    +"<fieldset align='center' style='padding:6px;'>"
                    +"<legend class='fieldset'>Informaci&oacute;n del paciente:</legend>"
                    +"<table width=100% id='infoPacEnTriage'>"
                        +"<tr>"
                            //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                            +"<td class=fila1>Documento</td>"
                            +"<td class=fila2><font size='4'>"+tipoDocumento+"-"+documento+"</font></td>"
                            +"<td class=fila1>Paciente</td><td class='fila2'><font size='5'>"+nombrePaciente+"</font></td>"
                        +"</tr>"
                        +"<tr>"
                            //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                            +"<td class=fila1>Edad</td>"
                            +"<td class=fila2><font size='4'>"+edad+"</font></td>"
                            +"<td class=fila1>Entidad</td><td class=fila2><font size='4'>"+entidad+"</font></td>"
                        +"</tr>"
                        +"<tr>"
                            //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                            +"<td class=fila1>Medico</td>"
                            +"<td class=fila2><font size='4'>"+medico+"</font></td>"
                            +"<td class='fondoAmarillo' align='center'><font size='4'>SERVICIO DESTINO</font></td>"
                            +"<td class='fondoAmarillo' align='center'><font size='4'>"+servicioDestino+"</font></td>"
                        +"</tr>"
                    +"</table>"
                    +"</fieldset>";

            $("#divFormularioHce").html("<div align=center>"+infoPaciente+"</div><iframe id='frameFormularioTriage' name='frameFormularioTriage' src='"+urlform+"' height='900px' width='950px' scrolling=yes frameborder='0'></iframe>");
            // --> Ventana dialog para cargar el iframe
            $("#divFormularioHce").dialog({
                show:{
                    effect: "blind",
                    duration: 0
                },
                hide:{
                    effect: "blind",
                    duration: 100
                },
                width:  'auto',
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "preanestesia",
                buttons:[
                {
                    text: "Cerrar",
                    icons:{
                            primary: "ui-icon-heart"
                    },
                    click: function(){
                        $(this).dialog("close");
                    }
                }],
                close: function( event, ui ) {
                    ///-->
                }
            });
            $("#divFormularioHce").parent().css({"z-index":"999"}).next().css({"z-index":"998"});
            $(obj).removeAttr('checked');
        }

        function cerrarModal(){
            $.unblockUI();
        }

        function seleccionarEnTodos( obj ){
            console.log("está funcionando el llamado");
            seleccionado = $(obj).find("option:selected").val();
            $("[name='select_cco_destino'] > option[value='"+seleccionado+"']").attr("selected", "selected");
            $("#inp_cco_destino").val(seleccionado);
        }

    </script>
    <?php
        $ccosPosibles    = consultarCcosPosibles();
        $fechaHoy        = date('Y-m-d');
        $anchoAltoRadios = "width:2.5rem;height:2.1rem";
        $arrTipDoc       = array();
        // --> Obtener maestro de tipos de documento
        $sqlTipDoc = "SELECT Codigo, Descripcion, alfanumerico
                        FROM root_000007
                       WHERE Codigo IN('CC', 'TI', 'RC', 'NU', 'CE', 'PA')
        ";
        $resTipDoc = mysql_query($sqlTipDoc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipDoc):</b><br>".mysql_error());

        while($rowTipDoc = mysql_fetch_array($resTipDoc)){
            $arrTipDoc[$rowTipDoc['Codigo']]['descripcion'] = $rowTipDoc['Descripcion'];
            $arrTipDoc[$rowTipDoc['Codigo']]['alfanumerico'] = $rowTipDoc['alfanumerico'];
        }

        // --> Obtener maestro de categorias de pacientes
        $wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
        $arrCategEmp     = array();
        $sqlCatEmp = "SELECT Catcod, Catnom, Catord
                        FROM {$wbasedato}_000207
                       WHERE Catest = 'on'
                       ORDER BY Catord";

        $resCatEmp = mysql_query($sqlCatEmp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCatEmp):</b><br>".mysql_error());
        while($rowCatEmp = mysql_fetch_array($resCatEmp))
            $arrCategEmp[$rowCatEmp['Catcod']] = strtoupper($rowCatEmp['Catnom']);
    ?>
    <body>
        <input type='hidden' id='wemp_pmla'               value='<?=$wemp_pmla?>'>
        <input type='hidden' id='codigoTurnero'           value='<?=trim($codigoTurnero)?>'>
        <input type='hidden' id='wdiahoy'                 value='<?=trim($fechaHoy)?>'>
        <input type='hidden' id='pacienteConPreAnestesia' value=''>
        <input type='hidden' id='turnoConPreAnestesia'    value=''>
        <input type='hidden' id='textoLector'             value='' numTabs='0'>
        <input type='hidden' id='historiaTemporalActual'  value=''>
        <input type='hidden' id='inp_cco_destino'         value=''>


        <div id='accordionPrincipal' align='center' style='margin: auto auto; height:900px;'>
            <h1 style='font-size: 3rem;background:#75C3EB' align='center'>
                <img width='125' heigth='61' src='../../images/medical/root/logoClinicaGrande.png' >
                &nbsp;
                Realizaci&oacute;n de preanestesia
                &nbsp;
                <img width='120' heigth='100' src='../../images/medical/root/Logo_MatrixAzulClaro.png'>
            </h1>
            <div style='color:#000000;font-family: verdana;font-weight: normal;font-size: 2rem;' id='div_contenedor_2' align='center'>
                <table style='width:80%;margin-top:0px;margin-bottom:2px;font-family: verdana;font-weight: normal;font-size: 1rem;'>
                    <tr>
                        <td id='divMensaje' colspan='2' style='padding:2px;color:#F79391;' align='center'>&nbsp;</td>
                    </tr>
                    <tr align='left'>
                        <td colspan='2' style:'font-size:2px;'>TIPO DE DOCUMENTO:</td>
                    </tr>
                    <tr>
                        <td align='center' colspan='2'>
                            <table style='color:#333333;font-size:1rem;margin-top:4px;margin-bottom:2px;' class='radio'>
                            <?php
                            $x = 0;
                            foreach($arrTipDoc as $codTipDoc => $datosTipDoc)
                            {
                                $x++;
                                echo (($x == 1) ? "<tr>" : "");
                                ?>
                                <td style='padding:2px'>
                                    &nbsp;&nbsp;
                                    <input type='radio' style='<?=$anchoAltoRadios?>' name='tipDocumento' value='<?=$codTipDoc?>' alfanumerico='<?=$datosTipDoc['alfanumerico']?>' id='radio<?=$codTipDoc?>' />
                                    <label onClick='checkearRadio(this);obtenerNombrePaciente()' style='border-radius:0.4em;<?=$anchoAltoRadios?>' for='radio<?=$codTipDoc?>'>&nbsp;</label>&nbsp;&nbsp;<?=$datosTipDoc['descripcion']?>
                                </td>
                                <?php
                                echo (($x == 3) ? "</tr>" : "");

                                $x = (($x == 3) ? 0 : $x);
                            }
                            ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align='left'>DOCUMENTO:</td>
                        <td><input id='numDocumento' type='text' tipo='obligatorio' onblur='buscarNombrePaciente( this );' style='margin-top:5px;background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:38rem;font-size: 2rem' placeholder='M&aacute;ximo 11 caract&eacute;res' ></td>
                    </tr>
                    <tr>
                        <td align='left'>NOMBRE:</td>
                        <td><input id='inputNombrePaciente' type='text' tipo='obligatorio'  style='margin-top:5px;background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:38rem;font-size: 2rem'></td>
                    </tr>
                </table>
                <input type='hidden' id='inputEdadPaciente' value=''>
                <input type='hidden' id='inputMedico' value=''>
                <input type='hidden' id='inputEntidad' value=''>
                <input type='hidden' id='guardado' value='off'>
                <center>
                    <div id='divPreAnesOk' style='display:none' align='center'>
                        <div id='div_intermitente'>
                            <br>
                                <img src='../../images/medical/root/Advertencia.png'/><span  class='subtituloPagina2'> Preanestesia realizada con éxito </span>
                            <br>
                        </div>
                    </div>
                </center>
                <table width='100%'>
                    <tr>
                        <td width='30%'></td>
                        <td width='40%' align='center'>
                            <!--<button id='botonAceptar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 1rem;' onclick='abrirPreAnestesia( this )'>Aceptar</button>-->
                            <button id='botonAceptar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 1rem;' onclick='seleccionarDestinoPreanestesia()'>Aceptar</button>
                            <button id='botonLimpiar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 1rem;' onclick='reiniciarPantalla()'>Limpiar</button>
                            <button id='botonLimpiar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 1rem;' onclick='salir()'>Salir</button>
                        </td>
                        <td width='30%' align='right'></td>
                    </tr>
                </table>

                <br><br>
                <center>
                <div id='div_listado' class='div_contenedor fila2'>
                    <div style='width:100%;' align='left'><span  class='subtituloPagina2'><font size='3'>PREANESTESIAS REALIZADAS:&nbsp;</font></span><input type='text' id='fechaConsultada' value='<?=$fechaHoy?>'><br><br></div>
                    <center>
                        <div id='tbl_prea_realizadas' class='tbl_prea_realizadas'>
                        </div>
                        <div id='div_espere' align='center'>
                            <img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >
                        </div>
                    </center>
                </div>
                </center>
            </div>
        </div>
        <div id='divFormularioHce' style='display:none' align='center'></div>
        <div id='msjAlerta' style='display:none;'>
            <br>
            <img src='../../images/medical/root/Advertencia.png'/>
            <br><br><div id='textoAlerta'></div><br><br>
        </div>
        <div id='divCcoDestino'>
            <center>
                <select name='select_cco_destino' onchange='seleccionarEnTodos(this)'>
                    <option value="">seleccione</option>
                    <?php foreach( $ccosPosibles as $key=> $dato ){ ?>
                        <option value='<?=$key?>'><?=$ccosPosibles[$key]?></option>
                    <?php } ?>
                </select>
            </center>
        </div>
    </body>
</html>
