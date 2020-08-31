<?php
include_once("conex.php");
require_once("conex.php");
mysql_select_db( "matrix" );
$fecha_hoy   = date('Y-m-d');
$hora        = date("H:i:s");

$mensaje = "<br><br><div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_terminada'>
            <img src='../../images/medical/root/Advertencia.png'/>
            [?] Estimado Usuario, la sesion en el sistema Matrix ha sido terminada, por favor reingrese al sistema antes de usar el chat.
            </div>";
if( !isset( $_SESSION['user'] ) ){
    if( isset( $peticionAjax ) ){
        $array = array('error'=>3, 'mensaje'=>$mensaje );
        echo json_encode( $array );
        return;
    }else{
        echo $mensaje;
        return;
    }
}

function empresaEmpleado($wemp_pmla, $conex, $wbasedato, $cod_use_emp){
    $use_emp = '+';

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

function consultarDatosUsuario( $codigoUsuario ){

    global $conex, $wemp_pmla, $wtalhuma;

    $query = " SELECT Idecco CentroCostos, Ideuse Codigo, TRIM( Ideno1 ) nombre1, TRIM( Ideap1 ) apellido, Ideafc autorizacionChat, Idepfc permisosVerificados, Ideatc aceptaTerminos
                 FROM {$wtalhuma}_000013
                WHERE Ideuse  = '{$codigoUsuario}'
                  AND Ideest  = 'on'
                  AND Ideatc  = 'on'
                ORDER BY 1, 2";
    $rs = mysql_query( $query, $conex ) or die( mysql_error() );
    while( $row = mysql_fetch_array( $rs ) ){

        $aux                          = explode( "-", $row['Codigo']);
        $datos['codigo']              = $aux[0];
        $datos['nombre']              = $row['nombre1'];
        $datos['apellido']            = $row['apellido'];
        $datos['Autoriza']            = $row['autorizacionChat']; //-->autoriza uso de fotos.
        $datos['permisosVerificados'] = $row['permisosVerificados'];
        $datos['aceptaTerminos']      = $row['aceptaTerminos'];
    }

    return( $datos );
}

function consultarConversacionesHoy( $wcodigoUsuario ){

    global $wemp_pmla;
    global $conex;
    global $wbasedatos;
    global $fecha_hoy;

    $chatsDiarios = "<div id='contendor_conversaciones' style='display:none;'>";

    $query = " SELECT Chtcod, 0 posicion, Chttxt texto
                 FROM {$wbasedatos}_000224
                WHERE Fecha_data = '{$fecha_hoy}'
                  AND Chtcod like '%-{$wcodigoUsuario}'
                UNION ALL
                SELECT Chtcod, 1 posicion, Chttxt texto
                 FROM {$wbasedatos}_000224
                WHERE Fecha_data = '{$fecha_hoy}'
                  AND Chtcod like '{$wcodigoUsuario}-%'";
    $rs    = mysql_query( $query, $conex );
    $num   = mysql_num_rows( $rs );
    if( $num > 0 ){
        while( $row = mysql_fetch_array( $rs ) ){
            $usuario = explode( "-", $row['Chtcod'] );
            $usuario = $usuario[$row['posicion']];
            $chatsDiarios .= "<div class='conversacionesActivas' usuario='{$usuario}'>";
            $chatsDiarios .= $row['texto'];
            $chatsDiarios .= "</div>";
        }
    }
    $chatsDiarios .= "</div>";
    return( $chatsDiarios );
}

function encuentraFoto($wcedula = 'not_foto',$sex='M'){
    $extensiones_img = array( '.jpg','.Jpg','.jPg','.jpG','.JPg','.JpG','.JPG','.jPG','.png','.Png','.pNg','.pnG','.PNg','.PnG','.PNG','.pNG');
    $wruta_fotos     = "../../images/medical/tal_huma/";
    $wfoto           = "silueta".$sex.".png";

    $wfoto_em = '';
    $ext_arch = '';

    foreach($extensiones_img as $key => $value)
    {
        $ext_arch = $wruta_fotos.trim($wcedula).$value;
        // echo "<!-- Foto encontrada: $ext_arch -->";
        if (file_exists($ext_arch))
        {
            $wfoto_em = $ext_arch;
            break;
        }
    }

    if ($wfoto_em == '')
    {
        $wfoto_em = $wruta_fotos.$wfoto;
    }

    return $wfoto_em;
}

if( $peticionAjax == "consultarListadosUsuarios" ){

    $wemp_pmla       = $_REQUEST['wemp_pmla'];
    $wtalhuma        = $_REQUEST['wtalhuma'];
    $wusuario        = $_REQUEST['wusuario'];
    $wbasedatos      = $_REQUEST['wbasedatos'];
    $usuarios        = array();

    $query     = " SELECT Detval
                     FROM root_000051
                    WHERE Detapl = 'usuariosOmitidosChat'
                      AND Detemp = '{$wemp_pmla}'";
    $rs        = mysql_query( $query, $conex );

    $condicionOmitir = "";
    while( $row = mysql_fetch_array( $rs ) ){
        if( trim( $row['Detval'] ) != "" )
            $condicionOmitir = " AND Ideuse NOT IN ( {$row['Detval']} )";
    }

    //---> consulta de usuarios.
    $query     = " SELECT Idecco CentroCostos, Ideuse Codigo, Ideno1, Ideno2, Ideap1, Ideap2, Cardes, Idepfc permisoVerificado, Ideafc autorizaChat, Ideced cedula, Idegen sexo
                     FROM {$wtalhuma}_000013
                    INNER JOIN
                          root_000079 on (Ideccg = Carcod)
                    WHERE Ideuse like '%-{$wemp_pmla}'
                      AND Ideest = 'on'
                      AND Ideatc = 'on'
                      AND Ideno1 is not null
                          {$condicionOmitir}
                   HAVING ( Ideuse != '{$wusuario}-{$wemp_pmla}' )
                    ORDER BY 3,5,6";
    $rs        = mysql_query( $query, $conex ) or die( mysql_error() );
    $usuariosConectados = 0;


    $i = 0;
    while( $row = mysql_fetch_array( $rs ) ){

        $aux             = explode( "-", $row['Codigo'] );
        $row['Codigo']   = $aux[0];
        $numeroAleatorio = mt_rand(1,100);

        ( is_int( $numeroAleatorio/2 ) ) ? $estadoUsuarios = "conectados" : $estadoUsuarios = "desconectados";
        $estadoUsuarios = "conectados";

        ( strcmp($wusuario, $row['Codigo'] ) > 0 ) ? $codigoConversacion = $wusuario."-".$row['Codigo'] : $codigoConversacion = $row['Codigo']."-".$wusuario;
        ( $row['permisoVerificado'] == "off" or ( $row['permisoVerificado'] == "on" and $row['autorizaChat'] == "off" ) or ( trim( $row['autorizaChat'] ) == "") ) ? $permisoFoto = "off" : $permisoFoto = "on" ;

        $foto = "";
        if( $permisoFoto == "on" ){
            $wcedula = $row['cedula'];
        }else{
            $wcedula = "";
        }
        $urlFoto1 = encuentraFoto( $wcedula , $row['sexo'] );
        $urlFoto = '<img width="80" height="100px" src="'.$urlFoto1.'">';

        ( trim($row['Ideno2']) == "NO APLICA" or trim($row['Ideno2']) == "." ) ? $row['Ideno2'] = "" : $row['Ideno2'] = $row['Ideno2'];
        ( trim($row['Ideap2']) == "NO APLICA" or trim($row['Ideap2']) == "." ) ? $row['Ideap2'] = "" : $row['Ideap2'] = $row['Ideap2'];

        $usuarios[$i]['descripcion'] = trim( $row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'] );

        $foto = '<div align="center" class="detalleContacto fila1">';
            $foto .= '<table>';
                $foto .= '<tr>';
                    $foto .= '<td style="font-size:10px;"><b>Nombre:</b><br>'.$usuarios[$i]['descripcion'].'</td>';
                $foto .= '</tr>';
                $foto .= '<tr>';
                    $foto .= '<td>'.$urlFoto.'</td>';
                $foto .= '</tr>';
                $foto .= '<tr>';
                    $foto .= '<td style="font-size:10px;"><b>Cargo:</b><br>'.$row['Cardes'].'</td>';
                $foto .= '</tr>';
            $foto .= '</table>';
        $foto .= '</div>';
        $clase = "mostrarFoto";

        $usuarios[$i]['codigo']          = $row['Codigo'];
        $usuarios[$i]['nombre1']         = $row['Ideno1'];
        $usuarios[$i]['nombre2']         = $row['Ideno2'];
        $usuarios[$i]['apellido1']       = $row['Ideap1'];
        $usuarios[$i]['apellido2']       = $row['Ideap2'];
        $usuarios[$i]['cedula']          = $row['cedula'];
        $usuarios[$i]['estado']          = $estadoUsuarios;
        $usuarios[$i]['codConversacion'] = $codigoConversacion;
        $usuarios[$i]['cargo']           = $row['Cardes'];
        $usuarios[$i]['permisoFoto']     = $permisoFoto;
        $usuarios[$i]['titulo']          = $foto;
        $usuarios[$i]['clase']           = $clase;
        $usuarios[$i]['foto']            = $urlFoto1;
        $i++;

    }
    echo json_encode( $usuarios );
    return;
}

if( $peticionAjax == "consultarGrupos"){

    $wemp_pmla = $_REQUEST['wemp_pmla'];
    $wtalhuma  = $_REQUEST['wtalhuma'];
    $wusuario  = $_REQUEST['wusuario'];
    $wbasedatos= $_REQUEST['wbasedatos'];
    $grupos    = array();

    $query = " SELECT id Codigo, Gruint integrantes, Grudes nombre, Grutop
                 FROM {$wbasedatos}_000222
                WHERE Gruupr = '{$wusuario}'
                  AND Gruest = 'on'";

    $rs    = mysql_query( $query, $conex );
    $i = 0;
    while( $row = mysql_fetch_array( $rs ) ){

        $aux                       = explode( ",", $row['integrantes'] );
        $numMiembros               = count( $aux );
        $grupos[$i]['codigo']      = $row['Codigo'];
        $grupos[$i]['nombre']      = $row['nombre'];
        $grupos[$i]['miembros']    = $row['integrantes'];
        $grupos[$i]['topeCupos']   = $row['Grutop'];
        $grupos[$i]['numMiembros'] = $numMiembros;

        $i++;

    }
    echo json_encode( $grupos );
    return;
}

if( $peticionAjax == "enviarMsj" ){

    $wemp_pmla           = $_REQUEST['wemp_pmla'];
    $wtalhuma            = $_REQUEST['wtalhuma'];
    $wusuario            = $_REQUEST['wusuario'];
    $tipoMensaje         = $_REQUEST['tipoMensaje'];
    $wcodigoConversacion = $_REQUEST['wcodigoConversacion'];
    $wUsuarioReceptor    = $_REQUEST['wUsuarioReceptor'];
    $msj                 = htmlentities( $msj, ENT_QUOTES, "UTF-8" );
    $mensaje             = '<div usuarioEscritor="'.$wusuario.'"><font size="1">'.$hora.'...<br></font><font size="1"><b>'.$wnombreUsuario.' '.$wapellidoUsuario.' </b></font><br><font style="font: 110% sans-serif;">'.$msj.'</font><br></div>';
    $mensaje2            = '<div usuarioEscritor="'.$wusuario.'"><font size="1" color="#0F378F">'.$hora.'...<br></font><font size="1" color="#0F378F"><b>'.$wnombreUsuario.' '.$wapellidoUsuario.' </b></font><br><font style="font: 110% sans-serif;" color="#0F378F">'.$msj.'</font><br></div><br>';

    if( $tipoMensaje == "directo" ){//--> tipo de mensaje individual
        $query = "INSERT INTO `{$wbasedatos}_000223` (`Medico`,    `Fecha_data`,  `Hora_data`, `Msjuen`,        `Msjure`,       `Msjtxt`, `Msjest`,          `Msjcoc`,             `Seguridad`)
                                         VALUES ('{$wbasedatos}', '{$fecha_hoy}', '{$hora}', '{$wusuario}', '{$wUsuarioReceptor}', '{$mensaje}', 'on',    '{$wcodigoConversacion}', 'C-{$wusuario}');";
        $rs   = mysql_query( $query, $conex );
        $id   = mysql_insert_id();
    }else{//--> tipo de mensaje masivo, debe descomponer los miembros del grupo, generar los códigos de conversacion y almacenar los mensajes

        $auxiliar = explode( ",", $wUsuarioReceptor );
        foreach ( $auxiliar as $i => $wUsuarioReceptor2 ) {
            ( strcmp($wusuario, $wUsuarioReceptor2 ) > 0 ) ? $wcodigoConversacion = $wusuario."-".$wUsuarioReceptor2 : $wcodigoConversacion = $wUsuarioReceptor2."-".$wusuario;
            $query = "INSERT INTO `{$wbasedatos}_000223` (`Medico`,    `Fecha_data`,  `Hora_data`, `Msjuen`,        `Msjure`,       `Msjtxt`, `Msjest`,          `Msjcoc`,             `Seguridad`)
                                         VALUES ('{$wbasedatos}', '{$fecha_hoy}', '{$hora}', '{$wusuario}', '{$wUsuarioReceptor2}', '{$mensaje}', 'on',    '{$wcodigoConversacion}', 'C-{$wusuario}');";
            $rs   = mysql_query( $query, $conex );
            $id   = mysql_insert_id();
        }
    }

    ( $id > 0 ) ? $error = 0 : $error = 1;
    ( $id > 0 ) ? $msj   = "sin errores " : $error = " con errores ";
    $data = array(  'error'=>$error, 'msj'=>$mensaje2, 'identificador'=>$id, 'hora'=>$hora);
    echo json_encode( $data );
    return;
}

if( $peticionAjax == "guardarEditarGrupo" ){

    $nombreGrupo     = $_REQUEST['wnombre'];
    $codsIntegrantes = $_REQUEST['integrantes'];
    $wemp_pmla       = $_REQUEST['wemp_pmla'];
    $wbasedatos      = $_REQUEST['wbasedatos'];
    $wtalhuma        = $_REQUEST['wtalhuma'];
    $wusuario        = $_REQUEST['wusuario'];

    if( $accionAjax == "agregarNuevoGrupo" ){
        $query = "INSERT INTO {$wbasedatos}_000222 (   `Medico`,      `Fecha_data`,`Hora_data`,  `Gruupr`,      `Gruint`,           `Grudes`,  `Gruest`, `Seguridad`)
                                           VALUES ('{$wbasedatos}', '{$fecha_hoy}', '{$hora}', '{$wusuario}', '{$codsIntegrantes}', '{$wnombre}', 'on', 'C-{$wusuario}');";
        $rs = mysql_query( $query, $conex ) or die ($query);
        $id = mysql_insert_id();

    }else{
        $query = " UPDATE {$wbasedatos}_000222
                      SET Gruint = '{$codsIntegrantes}',
                          Grudes = '{$wnombre}'
                    WHERE id     = {$wcodGrup}";
        $rs   = mysql_query( $query, $conex ) or die( $query );
        $id   = mysql_affected_rows();
    }

    ( $id > 0 ) ? $error = 0 : $error = 1;
    ( $id > 0 ) ? $msj   = "sin errores " : $error = " con errores ";
    $data = array(  'error'=>$error, 'msj'=>$msj, 'identificador'=>$id );
    echo json_encode( $data );
    return;
}

if( $peticionAjax == "buscarMensajesSinLeer" ){
    $wemp_pmla       = $_REQUEST['wemp_pmla'];
    $wtalhuma        = $_REQUEST['wtalhuma'];
    $wusuario        = $_REQUEST['wusuario'];
    $wbasedatos      = $_REQUEST['wbasedatos'];
    $contenedor      = array();
    $mensajesSinLeer = array();
    $idsNotificados  = array();

    //---> consulta de msjs sin leer.
    $query = " SELECT Msjure, Msjuen, Fecha_data, Hora_data, msjcoc, Msjtxt, id, Msjnot
                 FROM {$wbasedatos}_000223
                WHERE Msjure = '{$wusuario}'
                  AND Msjest = 'on'
                ORDER BY 1,2,3,5 asc";
    $rs    =  mysql_query( $query, $conex ) or die(mysql_error());
    while( $row = mysql_fetch_array( $rs) ){
        if( !isset( $mensajesSinLeer[$row['Msjuen']] ) )
            $mensajesSinLeer[$row['Msjuen']] = array();

        $auxiliar                   = array();
        $auxiliar['texto']          = $row['Msjtxt'];
        $auxiliar['id']             = $row['id'];
        $auxiliar['notificado']     = $row['Msjnot'];

        array_push( $idsNotificados, "'{$row['id']}'");
        array_push( $mensajesSinLeer[$row['Msjuen']], $auxiliar );
    }
    if( count( $idsNotificados ) > 0 ){
        $idsNotificados  = implode( ",", $idsNotificados );
        $query = " UPDATE {$wbasedatos}_000223
                      SET Msjnot = 'on'
                    WHERE id in ({$idsNotificados})";
        $rs    = mysql_query( $query, $conex );
    }
    $aux = array( 'mensajes'=>$mensajesSinLeer );
    echo json_encode(  $mensajesSinLeer );
    return;
}

if( $peticionAjax == "eliminarMensajesSinLeer" ){

    $wemp_pmla         = $_REQUEST['wemp_pmla'];
    $wbasedatos        = $_REQUEST['wbasedatos'];
    $codigoReceptor    = $_REQUEST['codigoReceptor'];
    $codigoEnvia       = $_REQUEST['codigoEnvia'];
    $idsSinLeer        = $_REQUEST['idsSinLeer'];
    $nuevaConversacion = $_REQUEST['nuevaConversacion'];
    $auxiliar          = explode(",", $idsSinLeer);

    foreach ($auxiliar as $i => $value){
        $auxiliar[$i] = "'".$auxiliar[$i]."'";
    }

    $idsSinLeer = implode( ",", $auxiliar );

    $query = " SELECT Fecha_data, Hora_data, msjcoc, Msjtxt, id
                 FROM {$wbasedatos}_000223
                WHERE id IN ($idsSinLeer)
                ORDER BY 4 ";
    $rs    =  mysql_query( $query, $conex ) or die( $query );
    while( $row = mysql_fetch_array( $rs ) ){
        //concatenar los mensajes leídos y agregarlos a la conversación, despues se eliminan de la tabla de mensajes sin leer
        $codigoConversacion = $row['msjcoc'];
        $nuevoMsj           = $row['Msjtxt'];
        if( $nuevaConversacion != "on" ){
            $query2 = " UPDATE {$wbasedatos}_000224
                          SET Chttxt = concat( Chttxt, '{$nuevoMsj}'  )
                        WHERE Chtcod = '{$codigoConversacion}'
                          AND Fecha_data = '{$fecha_hoy}'";
       }else{
            $nuevaConversacion = "off";
            $texto = $nuevoMsj;
            $query2 = "INSERT INTO `cliame_000224` (`Medico`, `Fecha_data`, `Hora_data`, `Chtcod`, `Chttxt`, `Chtest`, `Seguridad`)
                                           VALUES ('{$wbasedatos}', '{$fecha_hoy}', '{$hora}', '{$codigoConversacion}', '{$texto}', 'on', 'C-{$codigoReceptor}');";
        }
        $rs2    = mysql_query( $query2, $conex ) or die();
    }

    //--> se guarda
    $query2 = "DELETE
                 FROM {$wbasedatos}_000223
                WHERE id IN ($idsSinLeer) ";
    echo $query2;
    $rs = mysql_query( $query2, $conex );

    //$id = mysql_affected_rows();
    ( $id > 0 ) ? $error = 0 : $error = 1;
    ( $id > 0 ) ? $msj   = " sin errores " : $error = $query1;
    $data = array(  'error'=>$error, 'msj'=>$msj, 'identificador'=>$id, 'hora'=>$hora);
    echo json_encode( $data );
    return;
}

if( $peticionAjax == "eliminarGrupo" ){

    $query = "UPDATE {$wbasedatos}_000222
                 SET Gruest = 'off'
               WHERE id = '{$wcodigoGrupo}'";
    $rs    = mysql_query( $query, $conex );
    $id    = mysql_affected_rows();

    ( $id > 0 ) ? $error = 0 : $error = 1;
    $aux = array( 'error'=>$error );
    echo json_encode(  $aux );
    return;
}

if( $peticionAjax == "modificarPermisos" ){

    $query = "UPDATE {$wtalhuma}_000013
                 SET Idepfc = 'on',
                     Ideafc = '{$wpermiso}'
               WHERE Ideuse = '{$wusuario}-{$wemp_pmla}'
                 AND Ideest = 'on'";
    $rs    = mysql_query( $query, $conex ) or die( $query );
    $id    = mysql_affected_rows();

    ( $id > 0 ) ? $error = 0 : $error = $query;
    $aux = array( 'error'=>$error );
    echo json_encode(  $aux );
    return;
}

if( $peticionAjax == "validarUsuario" ){

    $respuesta = 0;//--> no corresponden los datos
    if( $wusuario == $wuser ){
        $query = " SELECT Codigo, password
                     FROM usuarios
                    WHERE Codigo = '{$wusuario}'";
        $rs    = mysql_query( $query, $conex );
        while ( $row = mysql_fetch_array( $rs ) ){

            if( $row['Codigo'] == $wusuario and $row['password'] == $wpassword ){
                $respuesta = 1;
            }

        }
    }

    $aux = array( 'respuesta'=>$respuesta );
    echo json_encode(  $aux );
    return;
}

if( $peticionAjax == "pedirLogueo" ){
    $_SESSION['wpedirLog'] = $_REQUEST['wpedirlog'];
    return;
}

?>
<html lang='es-ES'>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title> CHAT LAS AMÉRICAS </title>
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

        #tooltip div{margin:0; width:250px}

        .botona{
            font-size:13px;
            font-family:Verdana,Helvetica;
            font-weight:bold;
            color:white;
            background:#638cb5;
            border:0px;
            width:600px;
            height:30px;
            margin-left: 1%;
            cursor: pointer;
            border-top-right-radius    : 10px;
            border-top-left-radius     : 10px;
         }

         .detalleContacto{
            font-size:8px;
            font-family:Verdana,Helvetica;
            font-weight:bold;
            color:white;
            border:0px;
            width:200px;
            margin-left: 1%;
            border-top-right-radius    : 6px;
            border-top-left-radius     : 6px;
         }

        #contenedorPpal{
            width                      : 780px;
            padding-left               : 15px;
            padding-right              : 15px;
            padding-bottom             : 30px;
            background-color           : #0F378F;
            color                      : #FFFFFF;
            border-top-right-radius    : 10px;
            border-top-left-radius     : 10px;
            border-bottom-right-radius : 10px;
            border-bottom-left-radius  : 10px;
        }

        #contenedorChat{
            height                     : 435px;
            width                      : 750px;
            border-top-right-radius    : 6px;
            border-top-left-radius     : 6px;
            background                 : #E4E5E7;
        }

        #div_pipe{
            width                   : 100%;
            height                  : 95%;
            border-top-right-radius : 6px;
            border-top-left-radius  : 6px;
            background              : #E4E5E7;
            color                   : black;
        }

        .div_listaUsuarios {
            float      : right;
            width      : 40%;
            height     : 100%;
            overflow   : auto;
            background : #E4E5E7;

        }

        .mensajes {
            float  : left;
            width  : 58%;
            height : 100%;
            border : 1px solid black;
        }

        .contenedorSecundario{
            padding-left            : 15px;
            padding-right           : 15px;
            padding-bottom          : 15px;
            padding-top             : 15px;
           /* height                  : 460px;*/
            height                  : 500px;
            width                   : 750px;
            background              : #E4E5E7;
            color                   : #FFFFFF;
            font-weight             : bold;
            border-top-right-radius : 6px;
            border-top-left-radius  : 6px;
            border-bottom-right-radius : 6px;
            border-bottom-left-radius  : 6px;
        }

        .div_agrupaciones_usuarios{
            background                : url("../../../include/root/jqueryui_1_9_2/cupertino/images/ui-bg_glass_50_3baae3_1x400.png") repeat-x scroll 50% 50% #3BAAE3;
            border                    : 1px solid #2694E8;
            height                    : 5%;
            color                     : #FFFFFF;
            font-weight               : bold;
            color                     : white;
            cursor: pointer;
        }

        #div_chats_activos{
            height : 7%;
            width  : 100%;
        }

        #div_panel_chat{
            height       : 82%;
            width        : 100%;
            background   : white;
            overflow-y   : auto;
        }

        #div_txt{
            padding-top : 2%;
            height      : 15%;
            width       : 100%;
        }

        .estilotextarea{
            width  :100%;
            height :95%;
            border : 1px solid ;
        }

        .botonAdd{
            background-image    : url(../../images/medical/root/plus.gif);
            background-repeat   : no-repeat;
            height              : 20px;
            width               : 20px;
            background-position : center;
        }

        .listasUsuarios{
            font-family: sans-serif;
            font-size: 10px;
            width: 100px
        }

        .conversando{
            background-color: #F7FE2E;
        }
    </style>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function(){
            //---> listado de usuarios conectados
            dimensionarVentana();
            consultarUsuarios();
            consultarGruposUsuario();

            if( $("#wpedirlog").val() == "on" ){
                pedirLogueo();
            }

            $("#form_ppal").on("focus click keyPress", function()
            {
                $("#wcontrol_sesion").html( "0" );
            });

            $(this).keydown(function(e){
                var code = (e.keyCode ? e.keyCode : e.which);
                if(code == 116) {
                    e.preventDefault();
                }
            });

            var i = 0;
            setInterval(function(){
                i++;
                buscarMensajesSinLeer();
                var valor = $("#wcontrol_sesion").html()*1 + 1;
                $("#wcontrol_sesion").html( valor );
                if( valor == 100 ){
                    pedirLogueo();
                }
            }, 3000);
        });

        function consultarGruposUsuario(){
            //--->consultar Grupos del usuario
            $.ajax({
                    url: "chat_corporativo.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "consultarGrupos",
                               wemp_pmla: $("#wemp_pmla").val(),
                                wtalhuma: $("#wtalhuma").val(),
                                wusuario: $("#wusuario").attr("codigo"),
                              wbasedatos: $("#wbasedatos").val()

                          },
                    success: function(data)
                    {
                        if( data.error == 3 ){
                            $("div").hide();
                            $("#div_estado_sesion").html( data.mensaje );
                            $("#div_estado_sesion").show();
                            return;
                        }
                        var i = 0;
                        $.each( data, function( posicion, grupo ){
                                grupo.nombre    = grupo.nombre.toUpperCase();
                                cuposDisponible = grupo.topeCupos*1 - grupo.numMiembros*1;
                                var nuevoGrupo  = " <li style='cursor:pointer;' codigo='"+grupo.codigo+"' nombre='"+grupo.nombre+"' miembros='"+grupo.miembros+"' tipo='grupo' numMiembros='"+grupo.numMiembros+"' topeCupos='"+grupo.topeCupos+"' cuposDisponibles='"+cuposDisponible+"'>"+grupo.nombre+"</li>";
                                $("#lista_grupos").append(nuevoGrupo);
                        });
                         $("#lista_grupos>li").each(function(){
                            $(this).bind('contextmenu', function(e) {
                                    e.preventDefault();
                                    clickEnGrupo(e, this);
                            });
                            $(this).bind('click', function(e) {
                                    clickEnGrupo(e, this);
                            });
                        });
                    },
                    dataType: "json"
            });
        }

        function consultarUsuarios(){
            $("#lista_usuarios_conectados").html("");
            $.ajax({
                    url: "chat_corporativo.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "consultarListadosUsuarios",
                               wemp_pmla: $("#wemp_pmla").val(),
                                wtalhuma: $("#wtalhuma").val(),
                              wbasedatos: $("#wbasedatos").val(),
                                wusuario: $("#wusuario").attr("codigo")

                          },
                    success: function(data)
                    {
                        if( data.error == 3 ){
                            $("div").hide();
                            $("#div_estado_sesion").html( data.mensaje );
                            $("#div_estado_sesion").show();
                            return;
                        }
                        var i = 0;
                        $.each( data, function( posicion, usuario ){
                                dialog = "";
                                var nuevoUsuario       = "<li style='cursor:pointer;' codigo='"+usuario.codigo+"' tipo='usuario'  listaGrupos='off' mensajePendiente='off' class='"+usuario.clase+"' permisoFoto='"+usuario.permisoFoto+"' codConversacion='"+usuario.codConversacion+"' nombre='"+usuario.nombre1+" "+ usuario.nombre2+" "+ usuario.apellido1+" "+ usuario.apellido2+"' title='"+usuario.titulo+"' onclick='nuevoChat(this)'><img width='30px' height='30px' style='display:none;' src='"+usuario.foto+"'><font>&nbsp;"+usuario.nombre1+" "+ usuario.nombre2+" "+ usuario.apellido1+" "+ usuario.apellido2+"</font></li>";
                                $("#lista_usuarios_conectados").append(nuevoUsuario);
                                var nuevoUsuarioXgrupo = " <li style='cursor:pointer;' tipo='usuario' title='"+usuario.titulo+"' listaGrupos='on' class='"+usuario.clase+"' codigo='"+usuario.codigo+"' nombre='"+usuario.nombre1+" "+ usuario.nombre2+" "+ usuario.apellido1+" "+ usuario.apellido2+"' onclick='agregarQuitarUsuarioAgrupo(this, \"add\")'>"+usuario.nombre1+" "+ usuario.nombre2+" "+ usuario.apellido1+" "+ usuario.apellido2+"</li>";
                                $("#listaUsuarios_grupos").append(nuevoUsuarioXgrupo);

                        });
                        $(".mostrarFoto").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
                    },
                    dataType: "json"
            });
        }

        function buscarMensajesSinLeer(){
            var rango_superior = 245;
            var rango_inferior = 11;
            var aleatorio      = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
            usuarioConvActual = $("#div_panel_chat").attr( "codigoReceptor" );
            tipoChatActual    = $("#div_panel_chat").attr( "tipoChat" );
            $.ajax({
                    url: "chat_corporativo.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "buscarMensajesSinLeer",
                               wemp_pmla: $("#wemp_pmla").val(),
                                wtalhuma: $("#wtalhuma").val(),
                              wbasedatos: $("#wbasedatos").val(),
                                wusuario: $("#wusuario").attr("codigo"),
                               aleatorio: aleatorio

                          },
                    success: function(data)
                    {
                        if( data.error == 3 ){
                            $("div").hide();
                            $("#div_estado_sesion").html( data.mensaje );
                            $("#div_estado_sesion").show();
                            return;
                        }
                        var i = 0;
                        $.each( data, function( usuarioEnvia, mensajes ){
                                if( tipoChatActual == "usuario" || tipoChatActual == "" ){
                                    if( usuarioEnvia == usuarioConvActual  ){
                                        var idsSinLeer = "";
                                        var texto2     = "";
                                        $.each(mensajes, function( numeroMensaje, mensaje ){
                                            if(idsSinLeer == ""){
                                                idsSinLeer = mensaje.id;
                                            }else{
                                                idsSinLeer = idsSinLeer+","+mensaje.id;
                                            }
                                            texto2 += "<div usuarioEscritor='"+usuarioConvActual+"'>"+mensaje.texto+"</div><br>";
                                        });
                                        eliminarMensajesSinLeer( $("#wusuario").attr("codigo"), usuarioEnvia, idsSinLeer );

                                        if( $(".conversacionesActivas[usuario='"+usuarioConvActual+"']").attr("usuario") == undefined ){
                                            var nuevaConversacion = "<div class='conversacionesActivas' usuario='"+usuarioConvActual+"'>"+texto2+"</div>";
                                            $("#contendor_conversaciones").html( $("#contendor_conversaciones").html() + nuevaConversacion );
                                        }else{
                                            $(".conversacionesActivas[usuario='"+usuarioConvActual+"']").html( $(".conversacionesActivas[usuario='"+usuarioConvActual+"']").html() + texto2 );
                                        }

                                        $("li[codigo='"+usuarioEnvia+"']").attr("mensajePendiente", "off" );
                                        $("#mensajePendiente"+usuarioEnvia).remove();
                                        mostrarConversacion( usuarioConvActual, "usuario" );
                                        $("#audio_fb")[0].play();
                                    }else{
                                        var lioriginal =  $("#lista_usuarios_conectados>li[codigo='"+usuarioEnvia+"'][mensajePendiente='off'][listaGrupos='off']");
                                        if( lioriginal != undefined ){
                                            if( $(lioriginal).is(":visible") || $(lioriginal).attr("conversacionReciente") == "on" ){
                                            }
                                            $.each(mensajes, function( numeroMensaje, mensaje ){
                                                if( mensaje.notificado == "off" ){
                                                    $("#audio_fb")[0].play();

                                                }
                                            });
                                            li = lioriginal.clone(true);
                                            li.html( li.html() + " <img id='mensajePendiente"+usuarioEnvia+"' width='15px' src='../../images/medical/root/conversacion.png'> " );
                                            li.attr("mensajePendiente", "on");
                                            li.show();
                                            li.tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
                                            lioriginal.attr("mensajePendiente", "on");
                                            lioriginal.attr("conversacionReciente", "on");
                                            lioriginal.hide();
                                            $("#lista_conversaciones").append(li);
                                            $("#lista_conversaciones").show();
                                        }

                                    }
                                }
                        });
                    },
                    dataType: "json"
            });
        }

        function mostrarMenuNuevoGrupo( accion ){

            if( accion == "nuevo" ){
                var wtope = $("#wtopeMax").val();
                $("#tope").attr("valor", wtope);
                $("#tope").html("TOPE: &nbsp;" + wtope);
            }
            $("#div_nuevoGrupo").dialog({
                 title: " CREAR UN NUEVO GRUPO ",
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                        $("#td_eliminar_grupo").hide();
                        cerrar = guardarNuevoGrupo( accion );
                        if(!cerrar){
                            setTimeout( function(){
                                mostrarMenuNuevoGrupo( accion );
                            }, 1600 );
                        }
                        else
                            return;
                    }
                 },
                 closeOnEscape: false,
                 show: {
                    effect   : "blind",
                    duration : 500
                 },
                 hide: {
                    effect   : "blind",
                    duration : 500
                },
                height    : 550,
                width     : 600,
                rezisable : true
            });
            $(".ui-dialog-titlebar-close").hide();//-->oculto la x de cerrar, por defecto
        }

        function enviarMsj(){

            var msj      = $.trim( $("#txt_nuevoMensaje").val() );
            var nombre   = $("#wusuario").attr("nombre");
            var apellido = $("#wusuario").attr("apellido");

            if( $.trim( msj ) == "" )
                return;
            //---> acá se hace el manejo en el servidor, y se recible la hora
            var tipoChat            = $("#div_panel_chat").attr("tipoChat");
            var wcodigoConversacion = $("#div_panel_chat").attr("codigoConversacion");
            var wUsuarioReceptor    = $("#div_panel_chat").attr("codigoReceptor");

            if( tipoChat == "usuario" ){
                tipoMensaje = "directo";
            }else{
                tipoMensaje = "masivo";
            }
            //-->almacenamiento del nuevo grupo.
            $.ajax({
                    url: "chat_corporativo.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "enviarMsj",
                               wemp_pmla: $("#wemp_pmla").val(),
                              wbasedatos: $("#wbasedatos").val(),
                                wusuario: $("#wusuario").attr("codigo"),
                             tipoMensaje: tipoMensaje,
                     wcodigoConversacion: wcodigoConversacion,
                        wUsuarioReceptor: wUsuarioReceptor,
                          wnombreUsuario: nombre,
                        wapellidoUsuario: apellido,
                                     msj: msj

                          },
                    success: function(data)
                    {
                        if( data.error == 3 ){
                            $("div").hide();
                            $("#div_estado_sesion").html( data.mensaje );
                            $("#div_estado_sesion").show();
                            return;
                        }
                        if( data.error == 0 ){

                            msjRetornado = data.msj;
                            msjRetornado = msjRetornado.replace(/\\&quot;/g, '&quot;');
                            msjRetornado = msjRetornado.replace(/\\&#039;/g, '&#039;');

                            texto        = "<div align='right'>"+msjRetornado+"</div><br>";
                            texto2       = "<div usuarioEscritor='"+wUsuarioReceptor+"'>"+msjRetornado+"</div>";
                            if( $(".conversacionesActivas[usuario='"+wUsuarioReceptor+"']").attr("usuario") == undefined ){
                                var nuevaConversacion = "<div class='conversacionesActivas' usuario='"+wUsuarioReceptor+"'>"+texto2+"</div>";
                                $("#contendor_conversaciones").html( $("#contendor_conversaciones").html() + nuevaConversacion);
                            }else{
                                $(".conversacionesActivas[usuario='"+wUsuarioReceptor+"']").html( $(".conversacionesActivas[usuario='"+wUsuarioReceptor+"']").html() + texto2 );
                            }
                            $("#div_panel_chat").html( $("#div_panel_chat").html() + texto );
                            $("#txt_nuevoMensaje").val("");
                           mostrarConversacion(wUsuarioReceptor, "usuario");
                        }

                    },
                    dataType: "json"
                });
        }

        function validarTecla(e, textarea){
               $("#wcontrol_sesion").html( "0" );
               var esIE=(document.all);
               var esNS=(document.layers);
               var tecla=(esIE) ? event.keyCode : e.which;
               if (tecla==13){
                    enviarMsj();
               }
        }

        function ocultarMostrarElemento( id, tipo ){
            $("#wcontrol_sesion").html( "0" );
            $("#"+id).toggle();
            if( tipo == "busqueda"  &&  $("#"+id).is(":visible") ){
                $("#"+id).find("input[type='text']").focus();
            }
        }

        function agregarQuitarUsuarioAgrupo( obj, tipoMovimiento ){

            var codigo = $( obj ).attr("codigo");
            var nombre = $( obj ).attr("nombre");

            if( tipoMovimiento == "add"){

                var tope = $("#tope").attr("valor");
                var numMiembros = ( $("#wnumMiembros").html() )*1;
                if( tope < ( numMiembros + 1 ) ){
                    $("#div_nuevoGrupo").dialog( "close" );
                    alerta(" La cantidad de Usuarios excede el tope permitido ");
                    setTimeout( function(){
                        mostrarMenuNuevoGrupo( "nuevo" );
                    }, 1600 );
                    return;
                }else{
                    $("#wnumMiembros").html( $("#wnumMiembros").html()*1 + 1 );
                }

                var nuevoUsuarioXgrupo = $(obj).clone(true);
                $(nuevoUsuarioXgrupo).attr("onclick", "agregarQuitarUsuarioAgrupo( this, \"rmv\")" );
                $(nuevoUsuarioXgrupo).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
                $("#lista_usuariosEnGrupo").append(nuevoUsuarioXgrupo);


            }else{
                $("#wnumMiembros").html( $("#wnumMiembros").html()*1 - 1 );
                var nuevoUsuarioXgrupo = $(obj).clone(true);
                $(nuevoUsuarioXgrupo).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
                $(nuevoUsuarioXgrupo).attr("onclick", " agregarQuitarUsuarioAgrupo( this, \"add\") " );
                $("#listaUsuarios_grupos").append(nuevoUsuarioXgrupo);
            }
            $(obj).tooltip("destroy");
            $(obj).remove();
        }

        function guardarNuevoGrupo( accion ){

             var nombre = $("#wnameNuevoGrupo").val();

             if( $.trim(nombre) == "" ){
                if( $("#lista_usuariosEnGrupo>li").length == 0 ){
                    $("#wcodigoGrupo").val("");
                    return(true); //cerrar
                }else{
                    alerta( "Debe ingresar El nombre del grupo" );
                    return(false); //cerrar
                }

             }else{

                    if( $("#lista_usuariosEnGrupo>li").length == 0 ){
                        alerta( "Debe ingresar los Usuarios del grupo");
                        return(false); //cerrar

                    }else{//--> se va a guardar un nuevo grupo

                            var j = 0;
                            $("#lista_usuariosEnGrupo>li").each(function(){
                                j++;
                                if( j == 1 ){
                                   codigos  = $(this).attr("codigo");
                                }else{
                                    codigos += ","+$(this).attr("codigo");
                                }
                            });

                            var numMiembros      = $("#wnumMiembros").html();
                            var topeCupos        = $("#tope").attr("valor");
                            var cuposDisponibles = topeCupos*1 - numMiembros*1 ;

                            $("#wnameNuevoGrupo").val("");
                            $("#lista_usuariosEnGrupo>li").each(function(){
                                $(this).click();
                            });

                            if(accion == "nuevo"){
                                accionAjax     = "agregarNuevoGrupo";
                                codigoGrupo    = "";

                            }else{
                                accionAjax  =  "editarNuevoGrupo";
                                codigoGrupo =  $("#wcodigoGrupo").val();
                            }

                            //-->almacenamiento del nuevo grupo.
                            $.ajax({
                                    url: "chat_corporativo.php",
                                   type: "POST",
                                  async: true,
                                   data: {
                                            peticionAjax: "guardarEditarGrupo",
                                               wemp_pmla: $("#wemp_pmla").val(),
                                              wbasedatos: $("#wbasedatos").val(),
                                                wusuario: $("#wusuario").attr("codigo"),
                                             integrantes: codigos,
                                                 wnombre: nombre,
                                                wcodGrup: codigoGrupo,
                                              accionAjax: accionAjax

                                          },
                                    success: function(data)
                                    {
                                        if( data.error == 3 ){
                                            $("div").hide();
                                            $("#div_estado_sesion").html( data.mensaje );
                                            $("#div_estado_sesion").show();
                                            return;
                                        }
                                        if( data.error == 0 ){
                                            $("#lista_grupos>li").remove();
                                            consultarGruposUsuario();
                                        }

                                    },
                                    dataType: "json"
                                });
                    }
                    $("#lista_grupos").show();
                    return(true);
             }
        }

        function alerta( txt ){
            $("#textoAlerta").text( txt );
            $.blockUI({ message: $('#msjAlerta') });
                setTimeout( function(){
                    $.unblockUI();
                }, 1600 );
        }

        function mostrarGrupo( obj ){

            $("#lista_usuariosEnGrupo").html("");
            var nombre      = $(obj).attr("nombre");
            var codigo      = $(obj).attr("codigo");
            var miembros    = $(obj).attr("miembros");
            var numMiembros = $(obj).attr("numMiembros");
            var wtope       = $(obj).attr("topecupos");

            $("#wnameNuevoGrupo").val(nombre);
            $("#wcodigoGrupo").val(codigo);
            $("#td_eliminar_grupo").show();
            $("#tope").attr("valor", wtope);
            $("#tope").html("TOPE: &nbsp;" + wtope);
            $("#wnumMiembros").html("0");
            miembros = miembros.split(",");
            for( i in miembros ){
                $("#listaUsuarios_grupos>li[tipo='usuario'][codigo='"+miembros[i]+"']").each(function(){
                    $(this).click();
                })
            }
            mostrarMenuNuevoGrupo( "editar" );
        }

        function nuevoChat( obj ){

            if( $(obj).parent().hasClass("lista_conversaciones_recientes") ){
                objMsjSinleer = $("#lista_conversaciones>li[codigo='"+$(obj).attr("codigo")+"']");
                if($(objMsjSinleer).attr("codigo") != undefined ){
                    $(objMsjSinleer).click();
                    return;
                }
            }

            padreReceptor =  $(obj).parent();
            var tipoDeReceptor = $(obj).attr("tipo");
            if( tipoDeReceptor == "usuario" ){
                var codigoConversacion = $(obj).attr("codconversacion");
                var codigoReceptor     = $(obj).attr("codigo");
            }else{
                var codigoConversacion = $(obj).attr("codigo");
                var codigoReceptor     = $(obj).attr("miembros");
            }
            var nombre = $(obj).attr("nombre");

            $("#receptor_actual").html("conversando con: "+nombre);

            tipoConversacionAnterior   = $("#div_panel_chat").attr("tipoChat");
            codigoConversacionAnterior = $("#div_panel_chat").attr("codigoReceptor");

            if( $(obj).attr("conversacionReciente") != "on" && tipoDeReceptor == "usuario"){
                var nuevoObjeto2 = $(obj).clone(true);
                $( obj ).attr("conversacionReciente", "on");
                $( nuevoObjeto2 ).attr("conversacionReciente", "on");
                $( nuevoObjeto2 ).attr("mensajePendiente", "off" );;
                agregarEnConversacionesActivas( nuevoObjeto2, obj );
            }

            if( $(padreReceptor).attr("id") == "lista_conversaciones" ){
                $(obj).tooltip("destroy");
                $(obj).remove();
            }

            $("#div_panel_chat").attr("tipoChat", tipoDeReceptor );
            $("#div_panel_chat").attr("codigoReceptor", codigoReceptor );
            $("#div_panel_chat").attr("codigoConversacion", codigoConversacion );
            $("#txt_nuevoMensaje").attr("disabled", false);
            $("#btn_enviar").attr("disabled", false);
            mostrarConversacion( codigoReceptor, tipoDeReceptor );
            nuevaPosicion = $("#div_panel_chat").prop('scrollTopMax');
            $("#div_panel_chat").animate({ scrollTop: nuevaPosicion}, 1000);
        }

        function clickEnGrupo( e, objeto ){
            $("#wcontrol_sesion").html( "0" );
            var ev=e || event;
            ev.preventDefault();
            boton = ev.button
            if( $.trim(boton) == "2")
                mostrarGrupo(objeto);
            if( $.trim(boton) == "0")
                nuevoChat(objeto);
        }

        function eliminarMensajesSinLeer( usuarioReceptor, UsuarioEnvia, idsSinLeer ){
            var rango_superior = 245;
            var rango_inferior = 11;
            var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
            conversacion = $(".conversacionesActivas[usuario='"+UsuarioEnvia+"']");
            if( $(conversacion).attr("usuario") == undefined ){
                nuevaConversacion = "on";
            }else{
                nuevaConversacion = "off";
            }

            $.ajax({
                    url: "chat_corporativo.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "eliminarMensajesSinLeer",
                               wemp_pmla: $("#wemp_pmla").val(),
                              wbasedatos: $("#wbasedatos").val(),
                          codigoReceptor: usuarioReceptor,
                             codigoEnvia: UsuarioEnvia,
                              idsSinLeer: idsSinLeer,
                       nuevaConversacion: nuevaConversacion,
                               aleatorio: aleatorio

                          },
                    success: function(data)
                    {
                        if( data.error == 3 ){
                            $("div").hide();
                            $("#div_estado_sesion").html( data.mensaje );
                            $("#div_estado_sesion").show();
                            return;
                        }
                        if( data.error == 0 ){
                            console.log(data.msj);
                        }

                    },
                    dataType: "json"
                });
        }

        function buscarElementos( e, input ){

            var lista = $(input).attr("buscaren");
            if( $.trim($(input).val()) == "" ){
                $("#"+lista+">li[conversacionReciente!='on']").show();
                return;
            }else{
                $("#"+lista).show();
                $("#"+lista+">li").hide();
                var visibles = $("#"+lista+">li[nombre*='"+$(input).val().toUpperCase()+"'][conversacionReciente!='on']");
                $(visibles).show();
                if( $(visibles).length == 1 ){
                    var esIE=(document.all);
                    var esNS=(document.layers);
                    var tecla=(esIE) ? event.keyCode : e.which;
                    if (tecla==13){
                        $(visibles).click();
                        $("#txt_nuevoMensaje").focus();
                    }
                }
            }
        }

        function mostrarConversacion( codigoReceptor, tipoDeReceptor ){
            $("#wcontrol_sesion").html( "0" );

            if( $(".conversacionesActivas[usuario='"+codigoReceptor+"']") != undefined ){
                conversacionClone = $(".conversacionesActivas[usuario='"+codigoReceptor+"']").clone(true);
                $(conversacionClone).find("div").each(function(){

                    if( $(this).attr("usuarioEscritor") == codigoReceptor ){
                        $(this).attr("align", "left");
                    }else{
                        $(this).attr("align", "right");
                        $(this).find("font").attr("color", "#0F378F");
                        $(this).find("font").eq(1).html("YO:");
                    }
                });
                $("#div_panel_chat").html( conversacionClone.html() );
                conversacionClone.remove();
            }
            if( tipoDeReceptor != "usuario" ){
                $("#div_panel_chat").html("");
            }
            $("#div_panel_chat").animate({ scrollTop: $("#div_panel_chat").prop('scrollTopMax')}, 1000);
        }

        function eliminarGrupo(){
            $("#wcontrol_sesion").html( "0" );
            var codigoGrupo = $("#wcodigoGrupo").val();
            if(confirm( "¿Está usted seguro de eliminar este grupo?" )){
                $.ajax({
                        url: "chat_corporativo.php",
                       type: "POST",
                      async: true,
                       data: {
                                peticionAjax: "eliminarGrupo",
                                consultaAjax: "si",
                                   wemp_pmla: $("#wemp_pmla").val(),
                                  wbasedatos: $("#wbasedatos").val(),
                                wcodigoGrupo: codigoGrupo

                              },
                        success: function(data)
                        {
                            if( data.error == 3 ){
                                $("div").hide();
                                $("#div_estado_sesion").html( data.mensaje );
                                $("#div_estado_sesion").show();
                                return;
                            }
                            if( data.error == 0 ){
                                $("li[tipo='grupo'][codigo='"+codigoGrupo+"']").hide();
                                $("li[tipo='grupo'][codigo='"+codigoGrupo+"']").remove();
                                $("#div_nuevoGrupo").dialog( "close" );
                            }

                        },
                        dataType: "json"
                    });
            }else{ return; }
        }

        function mostrarFormularioAutorizacion(){
            $("#wcontrol_sesion").html( "0" );
            $("#formulario_permisos").dialog({
                 title: " PERMISOS DE USUARIO ",
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                        cambiarPermisosUsoFoto();
                    }
                 },
                 show: {
                    effect   : "blind",
                    duration : 500
                 },
                 hide: {
                    effect   : "blind",
                    duration : 500
                },
                height    : 400,
                width     : 700,
                rezisable : true
            });
            $(".ui-dialog-titlebar-close").hide();//-->oculto la x de cerrar, por defecto
        }

        function cambiarPermisosUsoFoto(){

            var valor   = $("input[type='radio'][name='permisosTalhuma']:checked").val();
            var usuario = $("#wusuario").attr("codigo");
            $.ajax({
                    url: "chat_corporativo.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "modificarPermisos",
                            consultaAjax: "si",
                               wemp_pmla: $("#wemp_pmla").val(),
                                wtalhuma: $("#wtalhuma").val(),
                                wusuario: usuario,
                                wpermiso: valor

                          },
                    success: function(data)
                    {
                        if( data.error == 3 ){
                            $("div").hide();
                            $("#div_estado_sesion").html( data.mensaje );
                            $("#div_estado_sesion").show();
                            return;
                        }
                        if( data.error == 0 ){
                            console.log( "actualizado" );
                        }

                    },
                    dataType: "json"
                });
        }

        function pedirLogueo(){
            controlSesion("on");
            $("#formulario_logueo").dialog({
                 title: " Logueo",
                 modal: true,
                 buttons: {
                    Ok: function() {
                        validarUsuario( $(this) );
                    }
                 },
                 closeOnEscape: false,
                 show: {
                    effect   : "blind",
                    duration : 500
                 },
                 hide: {
                    effect   : "blind",
                    duration : 500
                },
                height    : 400,
                width     : 700,
                rezisable : true
            });
            $(".ui-dialog-titlebar-close").hide();//-->oculto la x de cerrar, por defecto
        }

        function loguin( e, input ){
           var esIE=(document.all);
           var esNS=(document.layers);
           var tecla=(esIE) ? event.keyCode : e.which;
           if (tecla==13){
                validarUsuario( $("#formulario_logueo") );
           }
        }

        function validarUsuario( obj ){
            var usuario = $("#wusuarioInactivdad").val();
            var password = $("#wpassInactividad").val();
            $.ajax({
                    url: "chat_corporativo.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "validarUsuario",
                            consultaAjax: "si",
                               wemp_pmla: $("#wemp_pmla").val(),
                                wtalhuma: $("#wtalhuma").val(),
                                wusuario: usuario,
                                   wuser: $("#wusuario").attr("codigoNomina"),
                               wpassword: password

                          },
                    success: function(data)
                    {
                        if( data.error == 3 ){
                            $("div").hide();
                            $("#div_estado_sesion").html( data.mensaje );
                            $("#div_estado_sesion").show();
                            return;
                        }
                        if( data.respuesta != 0 ){
                            $( obj ).dialog( "close" );
                            $("#wpassInactividad").val("")
                            $("#wusuarioInactivdad").val("")
                            $("#wcontrol_sesion").html( "0" );
                            $("#wpedirlog").val( "off" );
                            controlSesion("off");
                            //consultarUsuarios();
                        }else{
                            $(".logueo_equivocado").show();
                            setTimeout( function(){
                                $(".logueo_equivocado").hide();
                            }, 2000 );
                        }

                    },
                    dataType: "json"
            });
        }

        function controlSesion( valor ){
            $.ajax({
                    url: "chat_corporativo.php",
                   type: "POST",
                   async : false,
                   data: {
                            peticionAjax: "pedirLogueo",
                            consultaAjax: "si",
                               wpedirlog: valor

                          },
                    success: function(data)
                    {
                        if( data.error == 3 ){
                            $("div").hide();
                            $("#div_estado_sesion").html( data.mensaje );
                            $("#div_estado_sesion").show();
                            return;
                        }
                        $("#wpedirlog").val("on");
                    }
            });
        }

        function agregarEnConversacionesActivas( obj, original ){

            if( $(original).attr("conversacionReciente") == "on" ){
                $(original).hide();
                $(original).tooltip("destroy");
                var ultimaConversacion = $(".lista_conversaciones_recientes[listaNumero='5']>li");
                if( ultimaConversacion != undefined ){
                    var codigo = $(ultimaConversacion).attr("codigo");
                    $("#lista_usuarios_conectados >li[tipo='usuario'][codigo='"+codigo+"']").attr("conversacionReciente", "off");
                    $("#lista_usuarios_conectados >li[tipo='usuario'][codigo='"+codigo+"']").show();
                    $("#lista_usuarios_conectados >li[tipo='usuario'][codigo='"+codigo+"']").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
                    $(".lista_conversaciones_recientes[listaNumero='5']>li").remove();
                    for( var i=4; i > 0; i-- ){
                        $(".lista_conversaciones_recientes[listaNumero='"+i+"']>li").each(function(){
                            var obj2 = $(this).clone( true );
                            var j = i + 1;
                            $(".lista_conversaciones_recientes[listaNumero='"+j+"']").append( obj2 );
                            $(this).tooltip("destroy");
                            $(this).remove();
                        });
                    }
                    $(".lista_conversaciones_recientes[listaNumero='1']>li").remove();
                    $(obj).find("img").show();
                    $(obj).find("font").hide();
                    $(".lista_conversaciones_recientes[listaNumero='1']").append(obj);
                    $(obj).tooltip("destroy");
                    $(obj).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
                }

            }else{

            }
        }

        function dimensionarVentana(){
            $("html").css("overflow-x", "hidden");
            $("html").css("overflow-y", "hidden");
        }

    </script>
</head>
<body onload='javascript:resizeTo(850,770);' align='center'>
<?php

    include_once( "root/comun.php" );
    $wemp_use       = (isset($_SESSION['user'])) ? empresaEmpleado($wemp_pmla, $conex, $wbasedato, $_SESSION['user']) : '+';
    $wtalhuma       = consultarAliasPorAplicacion( $conex, $wemp_pmla, "talhuma");
    $wbasedatos     = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion");
    $pos            = strpos($user,"-");
    $wuser          = substr($user,$pos+1,strlen($user));
    $usuario        = consultarDatosUsuario( $wemp_use );

    ( strlen($user_session) > 5) ? substr($user_session,-5) :
    ( !isset( $_SESSION['wpedirLog'] ) or $_SESSION['wpedirLog'] != "on" ) ? $_SESSION['wpedirLog'] = "off" : $_SESSION['wpedirLog'] = $_SESSION['wpedirLog'];

    if( $usuario['aceptaTerminos'] != "on" ){
        encabezado( " CHAT CORPORATIVO ", $wactualiz, "clinica" );

        echo "<br><br><div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
        [?] Estimado Usuario, usted no ha aceptado los terminos y condiciones de uso del chat corporativo, por favor dirijase a la página principal
        del sistema matrix, y diligencie el formulario de autorizaci&oacute;n.
        </div>";
        return;
    }
    ///************************** VERIFICACION DE PERMISOS Y ACEPTACIÓN DE TERMINOS Y CONDICIONES DE USO DEL CHAT ****************************//

    $conversActivas = consultarConversacionesHoy( $usuario['codigo'] );
    echo $conversActivas;

    $query = " SELECT Detval
                 FROM root_000051
                WHERE Detemp = '{$wemp_pmla}'
                  AND Detapl = 'topeMiembrosGrupo'";
    $rs    = mysql_query( $query, $conex );
    $row   = mysql_fetch_array( $rs );
    $wtopeMiembros = $row['Detval'];
?>
    <form align='center' id='form_ppal'><br>
        <input type='hidden' name='wcontrol_sesion' id='wcontrol_sesion' value='0'>
        <input type='hidden' name='wemp_pmla'  id='wemp_pmla' value='<?php echo $wemp_pmla; ?>'>
        <input type='hidden' name='wbasedatos' id='wbasedatos' value='<?php echo $wbasedatos; ?>'>
        <input type='hidden' name='wtalhuma'   id='wtalhuma' value='<?php echo $wtalhuma; ?>'>
        <input type='hidden' name='wusuario'   id='wusuario' codigo='<?php echo $usuario['codigo']; ?>' codigoNomina='<?php echo $wuser; ?>' nombre='<?php echo $usuario['nombre']; ?>' apellido='<?php echo $usuario['apellido']; ?>'>
        <input type='hidden' name='wautoriza'  id='wautoriza' value='<?php echo $usuario['autoriza']; ?>'>
        <input type='hidden' name='permisosVerificados'  id='permisosVerificados' value='<?php echo $usuario['permisosVerificados']; ?>'>
        <input type='hidden' name='wtopeMax'   id='wtopeMax' value='<?php echo $wtopeMiembros; ?>'>
        <input type='hidden' name='wpedirlog'   id='wpedirlog' value='<?php echo $_SESSION['wpedirLog']; ?>'>
        <div id='contenedorPpal' align='center' style='display:block'>
            <div width='100%' height='50px' align='left'><!-- div del encabezado del contenedor -->
                <div width='100%' height='100%' style='background-color: #0F378F;color:#FFFFFF;'>&nbsp;&nbsp;&nbsp;
                        <img width="100" heigth="35" src="../../images/medical/root/logo_matrix_azul.jpg">
                        <font style='font:150% sans-serif;' size='8'>Chat Institucional</font>
                </div>
            </div>
            <div class='contenedorSecundario' align='center'>
                <div align='left'>
                    <span class='subtituloPagina2' style='cursor:pointer; background-color:#ffff33;'><font size='2' id='receptor_actual'></font></span>
                </div>
                <div id='contenedorChat'>
                    <div id='div_pipe'>
                        <div id='div_mensajes' class='fila2 mensajes'>
                            <div id='div_panel_chat' align='left' tipoChat='' codigoReceptor='' codigoConversacion=''></div>
                            <div id='div_txt' align='left'>
                                <div style='float:left; width:80%; height:90%;'>
                                    <textarea id='txt_nuevoMensaje'disabled style='resize:none;' class='estilotextarea' onKeypress='validarTecla(event, this);'></textarea>
                                </div>
                                <div style='float:right; width:20%; height:90%;' align='left'>
                                    <input type='button' value='Enviar' id='btn_enviar' disabled onclick='enviarMsj();'>
                                </div>
                            </div>
                                <div style='width:100%; height:10%; padding-top:8px;' align='left'>
                                    <div ><span class='subtituloPagina2' style='cursor:pointer;'><font size='2'><b>Conversaciones recientes</b></font></span></div>
                                    <table style='border:1; width:60%;'>
                                        <!-- listas de conversaciones recientes-->
                                        <tr>
                                            <td style='widht:40px;' nowrap='nowrap'><lu class='lista_conversaciones_recientes listasUsuarios' style='width:30px; list-style-image: url("../../images/medical/root/conectados.png");' listaNumero='1'></lu></td>
                                            <td style='widht:40px;' nowrap='nowrap'><lu class='lista_conversaciones_recientes listasUsuarios' style='width:30px; list-style-image: url("../../images/medical/root/conectados.png");' listaNumero='2'></lu></td>
                                            <td style='widht:40px;' nowrap='nowrap'><lu class='lista_conversaciones_recientes listasUsuarios' style='width:30px; list-style-image: url("../../images/medical/root/conectados.png");' listaNumero='3'></lu></td>
                                            <td style='widht:40px;' nowrap='nowrap'><lu class='lista_conversaciones_recientes listasUsuarios' style='width:30px; list-style-image: url("../../images/medical/root/conectados.png");' listaNumero='4'></lu></td>
                                            <td style='widht:40px;' nowrap='nowrap'><lu class='lista_conversaciones_recientes listasUsuarios' style='width:30px; list-style-image: url("../../images/medical/root/conectados.png");' listaNumero='5'></lu></td>
                                            <td style='widht:40px;' nowrap='nowrap'><lu class='lista_conversaciones_recientes listasUsuarios' style='width:30px; list-style-image: url("../../images/medical/root/conectados.png");' listaNumero='6'></lu></td>
                                        </tr>
                                    </table>
                                </div>
                        </div>
                        <div id='div_listaUsuarios'  align='left' class='div_listaUsuarios'>
                            <div ><span class='subtituloPagina2' style='cursor:pointer;'><font size='2'><b>Mensajes sin leer</b></font></span></div>
                            <div id='div_msjsSinLeer' style='height:30%; border-bottom: 1px solid #2694E8; overflow-y: auto;'>
                                <lu id='lista_conversaciones' class='listasUsuarios' style='list-style-image: url("../../images/medical/root/conectados.png"); display:none;' align='left'></lu>
                            </div><br>

                            <!-- listas de usuarios total-->
                            <div ><span class='subtituloPagina2' style='cursor:pointer;'><font size='2'><b>Nueva conversación</b></font></span></div>
                            <div class='div_agrupaciones_usuarios' >
                                <div align='left' style='float:left;height:100%; width:80%;' onclick='ocultarMostrarElemento( "lista_usuarios_conectados", "" )'> Usuarios </div>
                                <div align='right' style='float:right; height:100%;'><img onclick='ocultarMostrarElemento("txt_buscar_paciente", "busqueda")' width="25px" height="20px" title="Buscar" src="../../images/medical/root/lupa.png"></div>
                            </div>
                            <div align='left' id='txt_buscar_paciente' style='display:none;'><span class='subtituloPagina2'><font size='2'>Buscar usuario: &nbsp;&nbsp;</font></span><input type='text' buscaren='lista_usuarios_conectados' value='' onkeyup='buscarElementos(event,this);'></div>
                            <lu id='lista_usuarios_conectados'  class='listasUsuarios' style='list-style-image: url("../../images/medical/root/conectados.png"); display:none;' align='left'></lu><br>
                            <!-- fin listas de usuarios total -->

                            <!-- listas de grupos -->
                            <div class='div_agrupaciones_usuarios' align='left'>
                                <div align='left' style='float:left; height:100%; width:80%;' onclick='ocultarMostrarElemento( "lista_grupos", "" )'> Grupos del usuario <input width='10px' type='button' class='botonAdd' onclick='mostrarMenuNuevoGrupo( "nuevo" );'></div>
                                <div align='right' style='float:right; height:100%;'><img onclick='ocultarMostrarElemento("txt_buscar_grupo", "busqueda")' width="25px" height="20px" title="Buscar" src="../../images/medical/root/lupa.png"></div>
                            </div>
                            <div align='left' id='txt_buscar_grupo'  style='display:none;'><span class='subtituloPagina2'><font size='2'>Buscar Grupo: &nbsp;&nbsp;</font></span><input  type='text' buscaren='lista_grupos' onkeyup='buscarElementos(this);' value=''></div>
                            <lu id='lista_grupos' class='listasUsuarios' style='list-style-image: url("../../images/medical/root/conectados.png "); display:none;' align='left'></lu>
                            <!-- fin listas de grupos -->
                        </div>
                    </div>
                </div>
            </div>
            <div align='center'>
                <audio id="audio_fb"><source src="../../images/medical/root/alertaMensaje.mp3" type="audio/mp3"></audio>
                <br><input type='button' id='btn_cerrar' name='btn_generar' value='CERRAR' onclick='window.close();'>
            </div>
        </div>
        <!-- fin contenedor principal-->
    </form>
    <!--  DIVS CON FORMULARIOS QUE  SON EMERGENTES -->
    <div id='div_nuevoGrupo' style='display:none;'>
        <input type='hidden' id='wtipoMovimientoGrupo' value=''>
        <input type='hidden' id='wcodigoGrupo' value=''>
        <br>
        <center><table>
            <tr><td class='encabezadotabla'> Nombre del grupo: </td><td> <input type='text' id='wnameNuevoGrupo' size='40' value=''></td><td style='cursor:pointer; display:none;' id='td_eliminar_grupo' onclick='eliminarGrupo()'><img onclick='' width="20" height="20" title="Eliminar Grupo" src="../../images/medical/root/borrar.png"></td></tr>
            <tr><td class='encabezadotabla'> Num Miembros </td><td> <div id='wnumMiembros' align='left' style='width:10%; height:100%; background-color:white; font-size:12px;float:left;padding-top:0px;padding-bottom:0px;'>0</div><div align='left' style='float:right;width:90%;height:100%;'><font size='1' color='blue' valor='<?php echo $wtopeMiembros; ?>' id='tope'>TOPE: &nbsp; <?php echo $wtopeMiembros; ?></font></div></td></tr>
        </table></center>
        <br>
        <center><table style='width:500px; height:200px;'>
            <tr style='height:10%'>
                <td style='width:50%; font-size:13px;'>
                    <div style='height:100%; width:100%;' class='div_agrupaciones_usuarios'>
                    <div align='left' style='float:left;height:100%; width:80%;'> Lista de Usuarios</div>
                    <div align='right' style='float:right; height:100%;'><img onclick='ocultarMostrarElemento("txt_buscar_usuario_para_grupo", "")' width="25px" height="20px" title="Buscar" src="../../images/medical/root/lupa.png"></div>
                    </div>
                </td>
                <td style='width:50%; font-size:13px;'><div style='width:100%; height:100%;' class='div_agrupaciones_usuarios'> Lista de Usuarios En el grupo</div></td>
            </tr>
            <tr style='height:90%'>
                <td style='height:90%'>
                    <div style='width:100%; height:300px; overflow-y: auto;'>
                        <div align='left' id='txt_buscar_usuario_para_grupo'  style='display:none;'><span class='subtituloPagina2'><font size='2'>Buscar Usuario: &nbsp;&nbsp;</font></span><input  buscaren='listaUsuarios_grupos' onkeyup='buscarElementos(this);' value=''></div>
                        <lu id='listaUsuarios_grupos' style='list-style-image: url("../../images/medical/root/flecha_agregar.jpg");' class='listasUsuarios'></lu>
                    </div>
                </td>
                <td valign='top'>
                    <lu id='lista_usuariosEnGrupo' class='listasUsuarios' style='list-style-image: url("../../images/medical/root/flecha_retirar.png");'></lu>
                </td>
            </tr>
        </table></center>
    </div>
    <div id='div_pruebas'></div>
    <div id='msjAlerta' style='display:none;'>
        <br>
        <img src='../../images/medical/root/Advertencia.png'/>
        <br><br><div id='textoAlerta'></div><br><br>
    </div>
    <div id='formulario_logueo' style='display:none;' align='center'>
        <table>
            <tr>
                <td style='font-size:13px;' colspan='2' align='center' class='botona'>
                    LOGUEO POR INACTIVIDAD
                </td>
            </tr>
            <tr>
                <td style='font-size:13px; height:20px;' colspan='2' align='center' class='fila2'>
                     Señor usuario, ha permanecido inactivo en el chat por un periodo de tiempo prolongado, <br> por favor, ingrese Nuevamente sus datos.
                </td>
            </tr>

            <tr class='fila1 logueo'>
                <td style='font-size:13px;' align='left'>
                   <b>USUARIO:</b>
                </td>
                <td align='left'>
                    <input type='text' id='wusuarioInactivdad' value=''>
                </td>
            </tr>
            <tr class="fila1 logueo">
                <td align='left'>
                     <b>CONTRASEÑA:</b>
                </td>
                <td align='left'>
                    <input type='password' id='wpassInactividad' value='' onkeypress='loguin( event, this )'>
                </td>
            </tr>

            <tr class='logueo_equivocado' style='display:none;'>
                <td style='font-size:13px; height:20px;' colspan='2' align='center' class='fila2'>
                     <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;'>
                        <img src='../../images/medical/root/Advertencia.png'/>
                        Estimado Usuario, los datos ingresados est&aacute;n err&oacute;neos.
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div id='div_estado_sesion' align='center' style='width:100%;'>
    </div>
</body>
</html>