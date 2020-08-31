<?php
include_once("conex.php");
header('Content-type: text/html; charset=ISO-8859-1');
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : recepcion_camilleros.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 05 Agosto de 2014

 DESCRIPCION: En este programa se da el visto bueno o aceptación de los envíos que realizan los camilleros a las direfentes unidades,
    Mediante una firma (código y clave) de quien recibe lo que entrega el camillero, se confirma el recibido.


 Notas:

 */ $wactualiz = "(Octubre 22 de 2015)"; /*
 ACTUALIZACIONES:

 *  Octubre 22 de 2015
    Eimer Castro        :   Se modifica la consulta que muestra los maestros de destinos para mostrar la central de ADMINISTRACION DE DOCUMENTOS, puesto que este
                                se encuentra configurado con Uso='A' en la tabla cencam_000004 y la consulta solo mostraba los resultados con Uso='I'.
                            Se modifica la consulta que muestra la información de la solicitud de los documentos. En el campo de observaciones muestra el contenido
                                del campo observaciones y habitación de origen.

 *  Octubre 09 de 2015
    Eimer Castro        :   Se crea un nuevo campo de recibido con un checkbox para cada uno de los documentos que recibe una persona y firma la recepción de este.

 *  Octubre 07 de 2014
    Edwar Jaramillo     :   * Se crea un parámetro en root_51 "recibo_electronico_de_documentacion" para tener en cuenta solo las solicitudes creadas superiores
                                a esta fecha del parametró en root, esto se hace porque al momento de crear el programa de recpción camillero ya existían muchos
                                registros y puede que esos no sean registros completos (En los listados se está mostrando documentación muy antigua), para este programa
                                solo se tendrán en cuenta los registros creados apartir de la fecha ingresada en el parámetro root.

 *  Agosto 05 de 2014
    Edwar Jaramillo     : Fecha de la creación del programa.
**/






if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}

if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = utf8_encode("Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.");
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}

$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];

$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");


//**********************************************************************************************************************
//*****************************************************  FUNCIONES  ****************************************************
//**********************************************************************************************************************

/**
 * [consultarEntregasCamillero: Esta función se encarga de consultar las entregas que tiene pendientes hacia las unidades destino,
 *                                 solo se mostrarán los registros que el mensajero ya ha marcado como reclamado en el Origen (ya tienen fecha de llegada)]
 * @param  [index]  $conex      [Conexión a la base de datos]
 * @param  [string] $wbasedato  [Prefijo de las tablas a usar]
 * @param  [string] $wemp_pmla  [Código de empresa de promotora]
 * @param  [string] $central    [Código de la central de servicios al que pertenecen las solicitudes]
 * @param  [string] $wmensajero [Código ínterno del maestro de mensajeros (tener en cuenta que no el es código de acceso a matrix)]
 * @param  [string] $wdestino   [Nombre del destino, los destinos en la tabla 000003 no se identifican por código sino que tienen el nombre o texto completo]
 * @return [array]              [Array con la lista de resgistros que están listos para entregar en los destinos finales]
 */
function consultarEntregasCamillero($conex, $wbasedato, $wemp_pmla, $central, $wmensajero, $wdestino, $consultar_apartirDe)
{
    $arr_entregar = array();
    if(!empty($wmensajero))
    {
        // Este query muestra los registros que se asociaron a un camillero o mensajero en particular y que pertenece a una central en particular.
        // Mostrar los registros que tienen fecha llegada y hora llegada diferente de ceros, esto supone que no puede entregar algo
        // que no ha reclamado aún, además que la fecha y hora de entrega sea diferente de ceros (sin entregar en el punto de destino)
        // NO SE INCLUYE hora de llegada o de cumplimiento porque por algún motivo puede entregarse a las 00:00:00 horas
        $sql = "SELECT  Fecha_data, Hora_data, Origen, Observacion, Solicito, Destino, id, Habitacion
                FROM    {$wbasedato}_000003
                WHERE   central = '{$central}'
                        AND Camillero LIKE '{$wmensajero}%'
                        AND Destino = '{$wdestino}'
                        AND Fecha_llegada <> '0000-00-00'
                        AND Fecha_cumplimiento = '0000-00-00'
                        AND Fecha_data > '{$consultar_apartirDe}'";
        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
        while ($row = mysql_fetch_array($result))
        {
            if(!array_key_exists($row['id'], $arr_entregar))
            {
                $arr_entregar[$row['id']] = array();
            }
            if(($row["Observacion"] != '' || $row["Observacion"] != "" || $row["Observacion"] != null)
                && ($row["Habitacion"] != '' || $row["Habitacion"] != "" || $row["Habitacion"] != null)) {
                $row["Observacion"] = $row["Habitacion"] . '<br /><br /><b>Observaciones:</b><br />' . $row["Observacion"];
            }
            else if (($row["Observacion"] != '' || $row["Observacion"] != "" || $row["Observacion"] != null)
                    && ($row["Habitacion"] == '' || $row["Habitacion"] == "" || $row["Habitacion"] == null)) {
                $row["Observacion"] = $row["Observacion"];
            }
            else {
                $row["Observacion"] = $row["Habitacion"];
            }
            $arr_entregar[$row['id']] = array(  "Fecha_data"  => $row["Fecha_data"],
                                                "Hora_data"   => $row["Hora_data"],
                                                "Origen"      => $row["Origen"],
                                                "Observacion" => $row["Observacion"],
                                                "Solicito"    => $row["Solicito"],
                                                "Destino"     => $row["Destino"],
                                                "id"          => $row["id"]);
        }
    }
    return $arr_entregar;
}

/**
 * [destinosPorVisitar: Esta función se encarga de retornar los nombres de los destinos que debe visitar un mensajero para hacer entregas activas,
 *                     se muestran los destinos en los que hay pendientes paquetes por entregar y que ya han sido reclamados por el mensajero.]
 * @param  [index]  $conex      [Conexión a la base de datos]
 * @param  [string] $wbasedato  [Prefijo de las tablas a usar]
 * @param  [string] $wemp_pmla  [Código de empresa de promotora]
 * @param  [string] $central    [Código de la central de servicios al que pertenecen las solicitudes]
 * @param  [string] $wmensajero [Código ínterno del maestro de mensajeros (tener en cuenta que no el es código de acceso a matrix)]
 * @return [array]              [Array solo con los nombres de los destinos que el mensajero debe visitar de acuerdo con los paquetes que ya debe entregar]
 */
function destinosPorVisitar($conex, $wbasedato, $wemp_pmla, $central, $wmensajero, $consultar_apartirDe)
{
    $arr_destinos = array();
    if(!empty($wmensajero))
    {
        // Este query muestra los destinos que el mensajero tiene por visitar para entregar documentos ya reclamados
        // Mostrar los registros que tienen fecha llegada y hora llegada diferente de ceros, esto supone que no puede entregar algo
        // que no ha reclamado aún, además que la fecha y hora de entrega sea diferente de ceros (sin entregar en el punto de destino)
        // NO SE INCLUYE hora de llegada o de cumplimiento porque por algún motivo puede entregarse a las 00:00:00 horas
        $sql = "SELECT  Destino
                FROM    {$wbasedato}_000003
                WHERE   central = '{$central}'
                        AND Camillero LIKE '{$wmensajero}%'
                        AND Fecha_llegada <> '0000-00-00'
                        AND Fecha_cumplimiento = '0000-00-00'
                        AND Fecha_data > '{$consultar_apartirDe}'
                GROUP BY Central, Camillero, Destino";
        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
        while ($row = mysql_fetch_array($result))
        {
            $arr_destinos[] = $row["Destino"];
        }
    }
    return $arr_destinos;
}

/**
 * [pintarHtmlEntregasCamilleto: Función encargada de crear las filas html para mostrar las solicitudes que tiene pendiente un mensajero por entregar]
 * @param  [Array] $arr_entregar [Array de registro de entregas]
 * @return [html]                [Código html de las filas de tabla (tr) para mostrar en la interfaz de usuario]
 */
function pintarHtmlEntregasCamilleto($arr_entregar)
{
    $html = "";
    $cont = 0;
    foreach ($arr_entregar as $id_sol => $arr_info)
    {
        $q = "  SELECT Descripcion AS nombre_usu
                FROM usuarios
                WHERE Codigo = '{$arr_info['Solicito']}'
                AND Activo = 'A'";
        $res2 = mysql_query($q) or die(mysql_errno().":".mysql_error());
        $row1 = mysql_fetch_array($res2);

        $css = ($cont % 2 == 0) ? 'fila1': 'fila2';
        $html .= '  <tr class="'.$css.' tr_entrega">
                        <td style="text-align: center;"><input type="checkbox" name="recibido" id_reg="'.$id_sol.'" id="checkRecibido_'.$id_sol.'" value="checkRecibido" style="display: inline;" onclick="validarTodosChecks();"></td>
                        <td>'.$arr_info['Fecha_data'].'</td>
                        <td>'.$arr_info['Hora_data'].'</td>
                        <td>'.utf8_encode($arr_info['Origen']).'</td>
                        <td>'.utf8_encode($arr_info['Observacion']).'</td>
                        <td>'.utf8_encode($row1['nombre_usu']).'</td>
                    </tr>';
        $cont++;
    }

    if(empty($html))
    {
        $msj = utf8_encode("NO HAY SOLICITUDES PENDIENTES PARA ESTE DESTINO<br>O POSIBLEMENTE NO HAN SIDO RECOGIDAS");
        $html = '<tr class="tr_entrega fila1" ><td colspan="6" style="text-align:center">'.$msj.'</td></tr>';
    }
    return $html;
}


/**
 * Condicional para controlar todas las peticiones AJAX que se realizan mediante JQUERY
 */
if(isset($accion) && isset($form))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'update':
            switch($form)
            {
                case 'lista_entrega_unidad':
                    $data['arr_entregas'] = array();
                    $arr_entregas         = consultarEntregasCamillero($conex, $wbasedato, $wemp_pmla, $central, $wmensajero, $wdestino, $consultar_apartirDe);
                    $data['html']         = pintarHtmlEntregasCamilleto($arr_entregas);
                    $data['arr_entregas'] = base64_encode(serialize($arr_entregas));
                    break;

                case 'validar_firma':
                    $data['arr_entregas'] = array();

                    if(count($arr_entregasIds) > 0)
                    {
                        //Consultar si existe el código del usuario y verificar si la clave corresponde al usuario
                        $sql = "SELECT  Activo, Password, Descripcion
                                FROM    usuarios
                                WHERE   Codigo = '{$wfirma_codigo}'
                                        AND Password = '{$wfirma_passw}'
                                        AND Activo = 'A'";
                        if($result = mysql_query($sql,$conex))
                        {
                            if(mysql_num_rows($result) > 0)
                            {
                                // Este array recibe los ids de los registros que fueron selecccionados con el checked.
                                $implode_ids = implode("','", $arr_entregasIds);

                                // UPDATE PARA MARCAR LAS SOLICITUDES COMO LEÍDAS
                                $sql = "UPDATE  {$wbasedato}_000003
                                        SET     Fecha_cumplimiento = '{$fecha_actual}',
                                                Hora_cumplimiento = '{$hora_actual}',
                                                Usuario_recibe = '{$wfirma_codigo}'
                                        WHERE   id IN ('{$implode_ids}')";

                                if($result = mysql_query($sql,$conex))
                                {
                                    $data['mensaje'] = utf8_encode("Datos correctos\n\nAceptó recibir el envío");
                                }

                                $arr_entregas = array();
                                $data['arr_entregas'] = base64_encode(serialize($arr_entregas));;
                            }
                            else
                            {
                                $data['mensaje'] = utf8_encode("El código y la clave de usuario no son correctos\n\n¡ Intente de nuevo !");
                                $data['error'] = 1;
                            }
                        }
                        else
                        {
                            $data['mensaje'] = utf8_encode("Error al consultar los datos de firma");
                            $data['error'] = 1;
                        }
                    }
                    else
                    {
                        $data['mensaje'] = utf8_encode("No se ha seleccionado ningún documento para entregar.");
                        $data['error'] = 1;
                    }
                    break;

                case 'buscar_nombre_firma':
                    $sql = "SELECT  Activo, Password, Descripcion
                            FROM    usuarios
                            WHERE   Codigo = '{$wfirma_codigo}'
                                    AND Activo = 'A'";
                    if($result = mysql_query($sql,$conex))
                    {
                        $row = mysql_fetch_array($result);
                        $data['html'] = utf8_encode($row['Descripcion']);
                    }
                    else
                    {
                        $data['mensaje'] = utf8_encode("No se pudo encontrar un nombre de usuario para el Código escrito");
                        $data['error'] = 1;
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

include_once("root/comun.php");
$wbasedato = (empty($wbasedato)) ? consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros') : $wbasedato;
$consultar_apartirDe = consultarAliasPorAplicacion($conex, $wemp_pmla, 'recibo_electronico_de_documentacion');

// Maestro de CAMILLEROS
$sql = "SELECT  codced AS codigo, Codigo AS cod_registro, Nombre
        FROM    {$wbasedato}_000002
        WHERE   EnTurno = 'on'
                AND Central = '{$central}'
        ORDER BY Nombre ASC";
$result_cmll = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());


// Crear los option html para mostrar una lista de camilleros o mensajeros
$wmensajero      = '';
$html_mensajeros = '<option value="">Mensajero no encontrado</option>';
while ($row = mysql_fetch_array($result_cmll))
{
    $selected = "";
    // if(($user_session == $row["codigo"]) || (substr($user_session,-5) == $row["codigo"])){
    if($user_session == $row["codigo"]){
        $selected = 'selected="selected"';
        $wmensajero = (empty($wmensajero)) ? $row["cod_registro"] : $wmensajero; // si está vació entonces inicialicelo con el código del camillero que esta viendo el programa
    }
    $html_mensajeros .= '<option value="'.$row['cod_registro'].'" '.$selected.' >'.utf8_encode($row['Nombre']).'</option>';
}

// SOLO LOS DESTINOS ASOCIADOS A UN MENSAJERO QUE TIENE PENDIENTE POR VISITAR
$arr_detinos_mensajero = destinosPorVisitar($conex, $wbasedato, $wemp_pmla, $central, $wmensajero, $consultar_apartirDe);

// MAESTRO DE DESTINOS
$sql = "SELECT  id AS codigo, Nombre as nombre
        FROM    {$wbasedato}_000004
        WHERE   Estado = 'on'
                AND Uso IN('I', 'A')
        ORDER BY Nombre ASC";
$result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
$arr_destinos = array();
while ($row = mysql_fetch_array($result))
{
    // Esta validación permite mostar solo los destinos
    if(in_array($row["nombre"], $arr_detinos_mensajero))
    {
        if(!array_key_exists($row['codigo'], $arr_destinos))
        {
            $arr_destinos[$row['codigo']] = $row['nombre'];
        }
    }
}

// Creo los option para mostrar un select con los destinos o punto de llegada donde está el mensajero para hacer su entrega.
$wdestino = '';
$html_destinos = '<option value="">Seleccione..</option>';
foreach ($arr_destinos as $key => $value) {
    // if(empty($wdestino)) { $wdestino = utf8_encode($value); }
    $html_destinos .= '<option value="'.$key.'">'.utf8_encode($value).'</option>';
}

// Listado de solicitudes que va a hacer firmar el camillero o mensajero en el destino de entrega.
$arr_entregas = array(); //consultarEntregasCamillero($conex, $wbasedato, $wemp_pmla, $central, $wmensajero, $wdestino);

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirmaci&oacute;n entrega</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.5"/>

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librería para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <script src="../../../include/root/toJson.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

    <script type="text/javascript">
        $(document).ready( function ()
        {
            $("#wfirma_codigo").val('');
            $("#wfirma_passw").val('');
            $("#spn_nombre_firma").html("");

        });

        function marcarTodos() {
            if ($("#checkMarcarTodos").is(':checked')) {
                //$("input[type=checkbox]").prop('checked', true); //todos los check del archivo
                $("#tabla_lista_entrega input[type=checkbox]").prop('checked', true); //solo los check del objeto #tabla_lista_entrega
            } else {
                //$("input[type=checkbox]").prop('checked', false);//todos los check del archivo
                $("#tabla_lista_entrega input[type=checkbox]").prop('checked', false);//solo los check del objeto #tabla_lista_entrega
            }
        }

        function cambiarEstadoCheckTodos(opcion) {
            if(opcion == '1') {
                $("#checkMarcarTodos").removeAttr('checked');
            }
            if(opcion == '2') {
                $("#checkMarcarTodos").attr('checked', 'checked');
            }
        }

        function validarTodosChecks() {
            var cantidadChecks = $('#tabla_lista_entrega >tbody').find("[id^=checkRecibido_]").length;
            var cantidadChecked = $('#tabla_lista_entrega >tbody').find("[id^=checkRecibido_]:checked").length;
            if(cantidadChecked == cantidadChecks) {
                cambiarEstadoCheckTodos('2');
            } else {
                cambiarEstadoCheckTodos('1');
            }
        }

        function url_ajax()
        {
            var wbasedato = $("#wbasedato").val();
            var wemp_pmla = $("#wemp_pmla").val();
            var central   = $("#central").val();
            return "recepcion_camillero.php?wbasedato="+wbasedato+"&wemp_pmla="+wemp_pmla+"&central="+central;
        }

        function cargarEntrega()
        {
            // var wdestino   = $("#wdestino").val(); // No se puede usar el codigo del destino porque en la tabla de consulta de documentos aparece es el nombre y no del código de destino
            var wdestino   = $("#wdestino option:selected").text();
            var wmensajero = $("#wmensajero").val();
            $("#tabla_lista_entrega").find(".tr_entrega").remove();
            $("#tabla_lista_entrega > tbody").append('<tr class="tr_entrega fila1" ><td colspan="5" style="text-align:center">Espere un momento mientras se muestra la lista...</td></tr>');
            $.post(url_ajax(),
                {
                    accion       : "update",
                    form         : "lista_entrega_unidad",
                    consultaAjax : "",
                    wdestino     : wdestino,
                    wmensajero   : wmensajero,
                    consultar_apartirDe : $("#consultar_apartirDe").val()
                },
                function(data){
                    if(data.error == 1)
                    {
                        jAlert(data.mensaje, "Alerta");
                    }
                    else
                    {
                        $("#tabla_lista_entrega").find(".tr_entrega").remove();
                        $("#tabla_lista_entrega > tbody").append(data.html);
                        $("#arr_entregas").val(data.arr_entregas);
                    }
                },
                "json"
            ).done(function(data){
                //
            });
        }

        function validarFirma()
        {
            //Array que permite guardar los ids de los registros seleccionados con checked que se desean guardar con la firma de la persona que recibe los documentos.
            var arr_entregasIds = new Array();
            //Selecciona todos los registros seleccionados con checked que se desean guardar con la firma de la persona que recibe los documentos.
            $("#tabla_lista_entrega").find(":checked[id^=checkRecibido_]").each(function(){
                var inputCheck = $(this);
                id_cod = $(inputCheck).attr('id_reg');
                arr_entregasIds.push( id_cod );
            });

            var wfirma_codigo = $("#wfirma_codigo").val();
            var wfirma_passw  = $("#wfirma_passw").val();

            var campos_ok = true;
            if(wfirma_codigo.replace(/ /gi , "") == '') { campos_ok = false; }
            if(wfirma_passw.replace(/ /gi  , "") == '') { campos_ok = false; }

            if(campos_ok)
            {
                $.post(url_ajax(),
                    {
                        accion        : "update",
                        form          : "validar_firma",
                        consultaAjax  : "",
                        wfirma_codigo : wfirma_codigo,
                        wfirma_passw  : wfirma_passw,
                        arr_entregas  : $("#arr_entregas").val(),
                        arr_entregasIds: arr_entregasIds
                    },
                    function(data){
                        if(data.error == 1)
                        {
                            jAlert(data.mensaje, "Alerta");
                        }
                        else
                        {
                            $("#arr_entregas").val(data.arr_entregas);
                            jAlert(data.mensaje, "Información");
                            $("#wfirma_codigo").val("");
                            $("#wfirma_passw").val("");
                            $("#spn_nombre_firma").html("");
                            cargarEntrega();
                        }
                    },
                    "json"
                ).done(function(data){
                    //
                });
            }
            else
            {
                jAlert("El 'Código usuario Matrix' o 'Clave usuario Matrix' está vacío\n\nDebe escribir los datos correctos para firmar.", "Alerta");
            }
        }

        $(function(){
            var isMobile = {
                Android: function() {
                    return navigator.userAgent.match(/Android/i);
                },
                BlackBerry: function() {
                    return navigator.userAgent.match(/BlackBerry/i);
                },
                iOS: function() {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                },
                Opera: function() {
                    return navigator.userAgent.match(/Opera Mini/i);
                },
                Windows: function() {
                    return navigator.userAgent.match(/IEMobile/i);
                },
             };

             var es_navegador_mobil = false;
             if (isMobile.Android())
             {
                //"Android";
                es_navegador_mobil = true;
             }
             else if (isMobile.BlackBerry())
             {
                //"BlackBerry";
                es_navegador_mobil = true;
             }
             else if (isMobile.iOS())
             {
                //"Iphone";
                es_navegador_mobil = true;
             }
             else if (isMobile.Opera())
             {
                //"Opera";
                es_navegador_mobil = true;
             }
             else if (isMobile.Windows())
             {
                //"IEMobile";
                es_navegador_mobil = true;
             }
             else
             {
                //"Default";
                // es_navegador_mobil = true;
             }
        });

        function cerrarVentanaPpal()
        {
            window.close();
        }

        /**
         * [onEnter: Al presionar enter se envían los datos de firma, si todo es correcto entonces se hace la entrega de todos los documentos.
         *             Si se presionó la tecla tabulador entonces se intenta traer el nombre de la persona a la que pertecene el código de usuario]
         * @param  {[type]} e [description]
         * @return {[type]}   [description]
         */
        function onEnter(e)
        {
            var charCode = (e.which) ? e.which : e.keyCode;
            if(charCode == 13 ) // Enter
            {
                nombreUsuarioRecibe();
                validarFirma();
            }
            else if(charCode == 9) // TAB
            {
                nombreUsuarioRecibe();
            }
        }

        /**
         * [nombreUsuarioRecibe: Verifica si en el campo de código de firma hay un valor e intenta buscar un nombre de usuario
         *                         asociado a ese código escrito]
         * @return {[type]} [description]
         */
        function nombreUsuarioRecibe()
        {
            $("#spn_nombre_firma").html("");
            var wfirma_codigo = $("#wfirma_codigo").val();
            if(wfirma_codigo.replace(/ /gi , "") != '')
            {
                $.post(url_ajax(),
                    {
                        accion        : "update",
                        form          : "buscar_nombre_firma",
                        consultaAjax  : "",
                        wfirma_codigo : wfirma_codigo
                    },
                    function(data){
                        if(data.error == 1)
                        {
                            jAlert(data.mensaje, "Alerta");
                        }
                        else
                        {
                            $("#spn_nombre_firma").html(data.html);
                        }
                    },
                    "json"
                ).done(function(data){
                    //
                });
            }
        }

    </script>

    <style type="text/css">
        .titulopagina2
        {
            border-bottom-width: 1px;
            border-left-width: 1px;
            border-top-width: 1px;
            font-family: verdana;
            font-size: 18pt;
            font-weight: bold;
            height: 30px;
            margin: 2pt;
            overflow: hidden;
            text-transform: uppercase;
        }

        /**{
            font-size: 15pt;
        }*/
    </style>
</head>
<body style="width: 628px;">
    <input type="hidden" id="wbasedato" name="wbasedato" value="<?=$wbasedato?>">
    <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="<?=$wemp_pmla?>">
    <input type="hidden" id="central" name="central" value="<?=$central?>">
    <input type="hidden" id="arr_entregas" name="arr_entregas" value="<?=base64_encode(serialize($arr_entregas))?>">
    <input type="hidden" id="consultar_apartirDe" name="consultar_apartirDe" value="<?=$consultar_apartirDe?>">
    <?=encabezado("<div class='titulopagina2'>CONFIRMACI&Oacute;N ENTREGA</div>",$wactualiz, "clinica")?>
    <div align="center" style="width:100%; background-color:#FFFEE2;" >
        <table align="center">
            <tr>
                <td class="encabezadoTabla">Mensajero</td>
                <td class="fila1">
                    <select name="wmensajero" id="wmensajero" disabled="disabled">
                        <?=$html_mensajeros?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="encabezadoTabla">Destino</td>
                <td class="fila1">
                    <select name="wdestino" id="wdestino" onchange="cargarEntrega();">
                        <?=$html_destinos?>
                    </select></td>
            </tr>
        </table>
    </div>
    <br>
    <div align="center">
        <table align="center" id="tabla_lista_entrega" >
            <tr class="encabezadoTabla">
                <td style="text-align: center;">Recibido<br><input type="checkbox" name="checkMarcarTodos" id="checkMarcarTodos" value="checkRecibidosTodos" align="center" style="display: inline;" onclick="marcarTodos();"></td>
                <td>Fecha</td>
                <td>Hora</td>
                <td>Origen</td>
                <td>Observaci&oacute;n</td>
                <td>Solicitado por</td>
            </tr>
            <?=utf8_decode(pintarHtmlEntregasCamilleto($arr_entregas))?>
        </table>
    </div>
    <br>
    <div align="center" style="width:100%; background-color:#FFFEE2;" >
        <table align="center" border="0" cellpadding="2" cellspacing="1">
            <tr class="encabezadoTabla">
                <td colspan="3" style="text-align:center;">FIRMA DE QUIEN RECIBE</td>
            </tr>
            <tr>
                <td class="encabezadoTabla">C&oacute;digo usuario Matrix</td>
                <td class="encabezadoTabla">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="encabezadoTabla">Clave usuario Matrix</td>
            </tr>
            <tr>
                <td class="fila2" style="text-align:center;"><input type="text" id="wfirma_codigo" name="wfirma_codigo" value="" size="10" onkeypress="onEnter(event);" onblur="nombreUsuarioRecibe();"></td>
                <td class="fila2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td class="fila2" style="text-align:center;"><input type="password" id="wfirma_passw" name="wfirma_passw" value="" size="10" onkeypress="onEnter(event);"></td>
            </tr>
            <tr>
                <td class="fila2" colspan="3" style="text-align:center;font-weight:bold;"><span id="spn_nombre_firma"></span></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:center;"><input type="button" value="OK" onclick="validarFirma();"></td>
            </tr>
        </table>
    </div>
    <br><br>
    <div style="width:100%;text-align:center;"><input type='button' value='Cerrar Ventana' onclick='cerrarVentanaPpal();'></div>
</body>
</html>