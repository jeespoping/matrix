<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : repEnConsignacion_InsumosLotes.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 13 Febrero de 2015

 DESCRIPCION: Reporte para mostrar por historia e ingreso los insumo que tienen n?mero de lotes asociados.


 Notas:
 --
 */ $wactualiza = "(Marzo 09 de 2015)"; /*
 ACTUALIZACIONES:

 * Marzo 09 2015
    Edwar Jaramillo     : Codificación utf8 en nombres de insumos y paciente para evitar dañar la respuesta ajax.

 *  Febrero 17 de 2015
    Edwar Jaramillo     : Fecha de la creación del reporte.

**/
// global $ccotema;
// global $wbasedato_HCE, $bordemenu;

$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");






// include_once("../../gesapl/procesos/gestor_aplicaciones_config.php");
// include_once("../../gesapl/procesos/gesapl_funciones.php");
// $wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
// include_once("ips/funciones_facturacionERP.php");


if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
$user_session      = explode('-',$_SESSION['user']);
$user_session      = $user_session[1];
// $user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);

// Incluye variables globales de tablas de parametrizaci? y funciones comunes al m?ulo.
// include_once ("ayudasdx_config.php");


/*****  DICCIONARIO LOCAL *****/
// define('PROCEDIMIENTO'      ,'Procedimiento');


$caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");

/**
 * [seguimiento description: Funci? para uso solo de desarrollo, en ambiente local, crea un archivo de texto donde se imprimen variables y arrays para su seguimiento]
 * @param  [type] $seguir [Cadena de texto a guardar en el archivo, para guardar array recordar usar print_r($al_array, true), puede usar saltos de l?ea PHP as?PHP_EOL ]
 * @return [type]         [description]
 */
function seguimiento($seguir)
{
    $fp = fopen("seguimiento.txt","a+");
    fwrite($fp, "[".date("Y-m-d H:i:s")."]".PHP_EOL.$seguir);
    fclose($fp);
}

/**
 * [limpiarString: quita multiples espacios y espacios al final del string]
 * @param  [type] $string_ [description]
 * @return [type]          [description]
 */
function limpiarString($string_)
{
    return trim(preg_replace('/[ ]+/', ' ', $string_));
}

/**
 * [nombreDiaSemana description: Esta funci? recibe los n?mero de a?, m? y d?, y devuelve el nombre del d? de la semana en la fecha indicada]
 * @param  [type] $ano [description]
 * @param  [type] $mes [description]
 * @param  [type] $dia [description]
 * @return [type]      [String, nombre del d? de la semana]
 */
function nombreDiaSemana($ano,$mes,$dia)
{
    $nameDias[] = 'Domingo';
    $nameDias[] = 'Lunes';
    $nameDias[] = 'Martes';
    $nameDias[] = 'Miercoles';
    $nameDias[] = 'Jueves';
    $nameDias[] = 'Viernes';
    $nameDias[] = 'Sabado';
    // 0->domingo    | 6->sabado
    $dia= date("w",mktime(0, 0, 0, $mes, $dia, $ano));
    return $nameDias[$dia];
}

if(isset($accion) && isset($form))
{
    include_once("ips/ValidacionGrabacionCargosERP.php");

    $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                default :
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                break;
            }
            echo json_encode($data);
            break;

        case 'update' :
            switch($form)
            {
                default :
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'load' :
            switch($form)
            {
                case 'generar_reporte_lotes':
                        $whistoria = limpiarString($whistoria);
                        $wingreso = limpiarString($wingreso);
                        $wdocumento = limpiarString($wdocumento);
                        $filtros = "";
                        $and = "";
                        if($whistoria != '')
                        {
                            $filtros = $and." c207.Mpahis = '".$whistoria."'";
                            $and = "AND";
                        }
                        if($wingreso != '')
                        {
                            $filtros = $and." c207.Mpaing = '".$wingreso."'";
                            $and = "AND";
                        }
                        if($wdocumento != '')
                        {
                            $filtros = $and." c207.Mpadoc = '".$wdocumento."'";
                            $and = "AND";
                        }

                        $html_encabezado = "";
                        $html_resp = "";
                        $arr_html_rep = array();

                        /*$sql = "SELECT  tcx11.Turfec, tcx11.Turtur, c207.Mpahis, c207.Mpaing, tcx11.Turnom, c207.Mpadoc, c240.Lotins, c240.Lotlot, (c240.Lotcan - c240.Lotdev) AS cantidad,
                                        m26.Artcom
                                FROM    {$wbasedato}_000207 AS c207
                                        INNER JOIN
                                        {$wbasedato_tcx}_000011 AS tcx11 ON (c207.Mpatur = tcx11.Turtur)
                                        INNER JOIN
                                        {$wbasedato}_000240 AS c240 ON (c240.Lottur = c207.Mpatur AND c240.Lotins = c207.Mpacom AND c240.Lotest = 'on')
                                        INNER JOIN
                                        {$wbasedato_movhos}_000026 AS m26 ON (c240.Lotins  = m26.Artcod)
                                WHERE   {$filtros}
                                        {$and} tcx11.Turfec BETWEEN '{$fecha_inicio} 00:00:00' AND '{$fecha_final} 23:59:59'
                                        AND (c240.Lotcan - c240.Lotdev) > 0
                                GROUP BY tcx11.Turtur, c240.Lotins, c240.Lotlot
                                ORDER BY tcx11.Turfec, tcx11.Turtur, c207.Mpahis, c207.Mpaing";*/
                        $sql = "SELECT  tcx11.Turfec, tcx11.Turtur, c207.Mpahis, c207.Mpaing, tcx11.Turnom, c207.Mpadoc, c240.Lotins, c240.Lotlot, (c240.Lotcan - c240.Lotdev) AS cantidad, m26.Artcom
                                FROM
                                        {$wbasedato}_000240 AS c240
                                        INNER JOIN
                                        {$wbasedato}_000207 AS c207 ON (c240.Lottur = c207.Mpatur AND c240.Lotins = c207.Mpacom)
                                        INNER JOIN
                                        {$wbasedato_tcx}_000011 AS tcx11 ON (c240.Lottur = tcx11.Turtur AND tcx11.Turfec BETWEEN '{$fecha_inicio} 00:00:00' AND '{$fecha_final} 23:59:59')
                                        INNER JOIN
                                        {$wbasedato_movhos}_000026 AS m26 ON (c240.Lotins  = m26.Artcod)
                                WHERE   (c240.Lotcan - c240.Lotdev) > 0
                                        AND c240.Lotest = 'on'
                                GROUP BY c240.Lottur, c240.Lotins, c240.Lotlot
                                ORDER BY tcx11.Turfec, c240.Lottur, c207.Mpahis, c207.Mpaing";
                        // echo "<pre>".print_r($sql,true)."</pre>";

                        if($result = mysql_query($sql, $conex))
                        {
                            $arr_turnos = array();
                            $arr_html_turnos = array();
                            while($row = mysql_fetch_array($result))
                            {
                                $codigo_insumo = $row['Lotins'];
                                $turno_cx = $row["Turtur"];
                                if(!array_key_exists($turno_cx, $arr_turnos))
                                {
                                    $arr_turnos[$turno_cx] = array("encabezado"=>"","insumos"=>array());
                                    // $arr_html_turnos[$turno_cx] = "";
                                    $html_encabezado = '<table id="tabla_resultado_reporte_'.$turno_cx.'" class="tabla_resultado_reporte" align="center" style="width:100%" >
                                                            <tr class="fila2">
                                                                <td colspan="3">
                                                                    <table>
                                                                        <tr>
                                                                            <td>
                                                                                Historia:
                                                                                <span style="font-weight:bold; font-size:11pt;" id="turn_histo_cx'.$turno_cx.'">'.$row['Mpahis'].'</span>
                                                                            </td>
                                                                            <td>Ingreso: <span style="font-weight:bold; font-size:11pt;" id="turn_ing_cx'.$turno_cx.'">'.$row['Mpaing'].'</span></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Nombre:
                                                                                <span style="font-weight:bold; font-size:11pt;" id="turn_nom_cx'.$turno_cx.'">'.utf8_encode($row['Turnom']).'</span>
                                                                            </td>
                                                                            <td>
                                                                                Documento:
                                                                                <span style="font-weight:bold; font-size:11pt;" id="turn_doc_cx'.$turno_cx.'">'.$row['Mpadoc'].'</span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                Fecha Cirugía:
                                                                                <span style="font-weight:bold; font-size:11pt;" id="turn_fectur_cx'.$turno_cx.'">'.$row['Turfec'].'</span>
                                                                            </td>
                                                                            <td>
                                                                                Turno:
                                                                                <span style="font-weight:bold; font-size:11pt;" id="turn_turn_cx'.$turno_cx.'">'.$row['Turtur'].'</span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                            <tr class="encabezadoTabla">
                                                                <td>Insumo</td>
                                                                <td>Lote</td>
                                                                <td>Cantidad</td>
                                                            </tr>';
                                    $arr_turnos[$turno_cx]["encabezado"] .= $html_encabezado;
                                }

                                if(!array_key_exists($codigo_insumo, $arr_turnos[$turno_cx]["insumos"]))
                                {
                                    $arr_turnos[$turno_cx]["insumos"][$codigo_insumo] = array();
                                }

                                $arr_turnos[$turno_cx]["insumos"][$codigo_insumo][] = array("insumo"        => $codigo_insumo,
                                                                                            "nombre_insumo" => $row['Artcom'],
                                                                                            "lote"          => $row['Lotlot'],
                                                                                            "cantidad"      => $row['cantidad']);

                                // $html = '       <tr class="find">
                                //                     <td></td>
                                //                     <td></td>
                                //                     <td></td>
                                //                 </tr>
                                //             </table>';
                            }

                            // print_r($arr_turnos);

                            if(count($arr_turnos) > 0)
                            {
                                $cont_trs = 0;
                                $cont_tr_enc = 0;
                                foreach ($arr_turnos as $codturno => $arr_info)
                                {
                                    $html_resp .= $arr_info["encabezado"];
                                    foreach ($arr_info["insumos"] as $codigo_insumo => $arr_insumos)
                                    {
                                        $ctrl_primerTr = false;
                                        $css_trEnc = ($cont_tr_enc % 2 == 0) ? 'fila1': 'fila2';
                                        foreach ($arr_insumos as $keyX => $arr_lotes)
                                        {
                                            $css_tr = ($cont_trs % 2 == 0) ? 'fila1': 'fila2';
                                            if(!$ctrl_primerTr)
                                            {
                                                $ctrl_primerTr = true;
                                                $html_resp .= ' <tr class="find '.$css_tr.'">
                                                                    <td rowspan="'.count($arr_insumos).'" class="'.$css_trEnc.'">'.$codigo_insumo.'-'.utf8_encode($arr_lotes['nombre_insumo']).'</td>
                                                                    <td>'.$arr_lotes['lote'].'</td>
                                                                    <td>'.$arr_lotes['cantidad'].'</td>
                                                                </tr>';
                                                $cont_tr_enc++;
                                            }
                                            else
                                            {
                                                $html_resp .= ' <tr class="find '.$css_tr.'">
                                                                    <td>'.$arr_lotes['lote'].'</td>
                                                                    <td>'.$arr_lotes['cantidad'].'</td>
                                                                </tr>';
                                            }
                                            $cont_trs++;
                                        }
                                    }
                                    $html_resp .= '</table><br/>';
                                }
                                $data["html"] = $html_resp;
                            }
                            else
                            {
                                $data["html"] = '<table id="tabla_resultado_reporte_" align="center">
                                                    <tr class="encabezadoTabla">
                                                        <td>NO SE ENCONTRARON DATOS!</td>
                                                    </tr>
                                                </table>';
                            }
                        }
                        else
                        {
                            $data["error"] = 1;
                            $data["mensaje"] = "No se pudo ejecutar la consulta para generar el reporte";
                            $data["sql"] = mysql_errno()." ".print_r($sql,true)." - ".mysql_error();
                        }
                    break;
                default:
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'delete' :
            switch ($form)
            {
                /*case 'CODIGO_EJEMPLO':
                        $query = "  UPDATE  ".$wbasedato."_".OBSERVACIONES_ORDEN."
                                            SET Segest = 'off'
                                    WHERE   id = '".$id_observ."'";
                        if($result = mysql_query($query, $conex))
                        {

                        }
                        else
                        {
                            debug_log_inline('',"<span class=\"error\">ERROR</span> Error al borrar obsrvaci? de la orden: $worden Fuente: $wfuente <br>&raquo; ".$query."<br>&raquo;No. ".mysql_errno().'<br>&raquo;Err: '.mysql_error()."<br>");
                            $descripcion = "(".mysql_errno().') '.mysql_error()."|obs:'Error al borrar obsrvaci? de la orden: $worden Fuente: $wfuente";
                            // insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wfuente.'-'.$worden, $query);
                            $data['mensaje'] = 'No se pudo eliminar la observaci?.';
                            $data['error'] = 1;
                        }
                        $data['debug_log'] = utf8_encode(debug_log_inline());
                    break;*/

                default:
                    $data['mensaje'] = 'No se ejecutó ningúna rutina interna del programa';
                    break;
            }
            echo json_encode($data);
            break;
        default : break;
    }
    return;
}

include_once("root/comun.php");
$wbasedato_HCE    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbasedato        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
$wbasedato_tcx        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');

?>
<html lang="es-ES">
<head>
    <title>Reporte-En consignaci&oacute;n</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librer? para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

    <script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
    <link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>

    <!--<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript">-->


    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;

        // Inicializar primer acorde?
        $(function(){
            // $("#div_datos_basicos").attr("acordeon", "");
            // $("#div_datos_basicos").accordion({
            //      collapsible: true
            //     ,heightStyle: "content"
            //     //,active: -1
            // });

            // $('.numerico').on({
            //     keypress: function(e) {
            //         var r = soloNumeros(e);
            //         if(r==true)
            //         {
            //             var codeentr = (e.which) ? e.which : e.keyCode; /*if(codeentr == 13) { buscarDatosBasicos(); }*/
            //             return true;
            //         }
            //         return false;
            //     }
            // });
        });

        $(document).ready( function ()
        {
            reiniciarTooltip();

            $("#fecha_inicio_rep").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
                maxDate:"+0D"
            });

            $("#fecha_final_rep").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
                maxDate:"+0D"
            });

            actualizarSearch();
        });

        function actualizarSearch()
        {
            $('input#id_search_consulta').quicksearch('.tabla_resultado_reporte .find');
        }

        function generarReporte()
        {
            var continuar = false;
            $(".datoreq").each(function(){
                var dato_val = $(this).val();
                if(dato_val.replace(/ /gi, "") != '')
                {
                    continuar = true;
                }
            });

            var msj_fechas = "";
            if($("#fecha_inicio_rep").val().replace(/ /gi, "") == '' || $("#fecha_final_rep").val().replace(/ /gi, "") == '')
            {
                msj_fechas = "\nLos campos de fechas no deben estar vacíos";
                continuar = false;
            }

            if(continuar)
            {
                var obJson                 = parametrosComunes();
                obJson['accion']           = 'load';
                obJson['form']             = 'generar_reporte_lotes';
                obJson['fecha_inicio']     = $("#fecha_inicio_rep").val();
                obJson['fecha_final']      = $("#fecha_final_rep").val();
                obJson['whistoria']        = $("#whistoria_rep").val();
                obJson['wingreso']         = $("#ingreso_rep").val();
                obJson['wdocumento']       = $("#wdocumento_rep").val();
                obJson['wbasedato_movhos'] = $("#wbasedato_movhos").val();
                $.post("repEnConsignacion_InsumosLotes.php",
                    obJson,
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            //console.log("Se modificaron las posiciones de las cirugías ")
                            $("#td_contenedor_rep").html(data.html);
                        }
                    },
                    "json"
                ).done(function(){
                    actualizarSearch();
                });
            }
            else
            {
                alert("Debe ingresar datos en los filtros del reporte para generarlo."+msj_fechas);
            }
        }

        function parametrosComunes()
        {
            var obJson              = {};
            obJson['wemp_pmla']     = $("#wemp_pmla").val();
            obJson['wbasedato']     = $("#wbasedato").val();
            obJson['wbasedato_tcx'] = $("#wbasedato_tcx").val();
            return obJson;
        }

        function reiniciarTooltip()
        {
            $('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
        }

        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: '',
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);



    </script>

    <script type="text/javascript">

        function verOcultarLista(id_elem)
        {
            if($("#"+id_elem).is(":visible"))
            {
                $("#"+id_elem).hide(300);
            }
            else
            {
                $("#"+id_elem).show(300);
            }
        }

        function simularPlaceHolder()
        {
            // P?ina con etiquetas de html5 de las que se podr? verificar su compatibilidad
            // https://github.com/Modernizr/Modernizr/wiki/HTML5-Cross-browser-Polyfills
            // http://geeks.ms/blogs/gperez/archive/2012/01/10/modernizr-ejemplo-pr-225-ctico-1-utilizando-placeholder.aspx
            // http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
            if(!Modernizr.input.placeholder)
            {
                console.log("NAVEGADOR NO COMPATIBLE CON placeholder de HTML5, Se sim?la atributo placeholder.");
                $('[placeholder]').focus(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                       input.val('');
                       input.removeClass('placeholder');
                    }
                }).blur(function() {
                    var input = $(this);
                    if (input.val() == '' || input.val() == input.attr('placeholder')) {
                        input.addClass('placeholder');
                        input.val(input.attr('placeholder'));
                    }
                }).blur();
                $('[placeholder]').parents('form').submit(function() {
                    $(this).find('[placeholder]').each(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                       input.val('');
                    }
                  })
                });
            }
        }

        function validar_cifra_decimal(elem)
        {
            $(elem).removeClass("campoRequerido");
            var cantidad = $(elem).val();
            if ( regExDecimal.test( cantidad ) && cantidad != '')
            {
                esok = true;
            }
            else
            {
                esok = false;
                $(elem).addClass("campoRequerido");
            }
            return esok;
        }

        function aplicarAcordeon(id_div)
        {
            $("#"+id_div).accordion({
                collapsible: true
                ,autoHeight: false
                // ,clearStyle: true
                // ,heightStyle: "content"
                // ,active: -1
            });
        }

        function isset ( strVariableName ) {
            try {
                eval( strVariableName );
            } catch( err ) {
                if ( err instanceof ReferenceError )
                   return false;
            }
            return true;
        }

        function resetStylePrefijo(prefijo)
        {
            var cont = 0;
            var cs = 'fila1';
            $("tr").find("[id^="+prefijo+"]").each(function(){
                    $(this).removeClass("fila1 fila2");
                    if(cont % 2 == 0)
                        cs = 'fila1';
                    else
                        cs = 'fila2';

                    $(this).addClass(cs);
                    cont = cont+1;
                }
            );
        }

        //Function to convert hex format to a rgb color
        function rgb2hex(rgb){
         rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
         return "#" +
          ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
          ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
          ("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
        }

        function cambioImagen(img1, img2)
        {
            $('#'+img1).hide(1000);
            $('#'+img2).show(1000);
        }

        function ocultarElemnto(elemento){
            $("#"+elemento).hide(1000);
        }

        function soloNumeros(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            //alert(charCode);
             if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36 && charCode != 46) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
                return false;

             return true;
        }

        function soloNumerosDecimales(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            //alert(charCode);
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
                return false;

             return true;
        }

        function soloNumerosLetras(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            // alert(charCode);
             // if (charCode > 31 && (charCode < 48 || charCode > 57))
             // if ((charCode < 48 && charCode > 57) || (charCode < 65 && charCode > 90) || (charCode < 97 && charCode > 122 ))
             if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) || charCode == 8 || charCode == 9)
                return true;

             return false;
        }

        /**
         * Para aceptar caracteres num?icos, letras y algunos otros caracteres permitidos
         *
         * @return unknown
         */
        function soloCaracteresPermitidos(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            // alert(charCode);
            /*
                (charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) // N?meros, letras minusculas y mayusculas
                (charCode >= 40 && charCode <= 46 ) //    )(*+,-.
                charCode == 8 // tecla borrar
                charCode == 32 // caracter espacio
                charCode == 95 // caracter _
            */
            if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 )
                    || (charCode >= 40 && charCode <= 46 )
                    || charCode == 8
                    || charCode == 32
                    || charCode == 95)
            {
                return true;
            }

             return false;
        }

        function resetStylePrefijo(pref)
        {
            var cont = 0;
            var cs = 'fila1';
            $("tr").find("[id^="+pref+"]").each(function(){
                    $(this).removeClass("fila1 fila2");
                    if(cont % 2 == 0)
                        cs = 'fila1';
                    else
                        cs = 'fila2';

                    $(this).addClass(cs);
                    cont = cont+1;
                }
            );
        }

        function resetStyleSufijo(sufijo)
        {
            var cont = 0;
            var cs = 'fila1';
            $("tr").find("[id$="+sufijo+"]").each(function(){
                    $(this).removeClass("fila1 fila2");
                    if(cont % 2 == 0)
                        cs = 'fila1';
                    else
                        cs = 'fila2';

                    $(this).addClass(cs);
                    cont = cont+1;
                }
            );
        }

        function trOver(grupo)
        {
            $("#"+grupo.id).addClass('classOver');
        }

        function trOut(grupo)
        {
            $("#"+grupo.id).removeClass('classOver');
        }

        function validarRequeridos(contenedor)
        {
            var vacioR = true;
            $("#"+contenedor).find(".requerido").each(
                function(){
                    $(this).removeClass('campoRequerido');
                    var valor = $(this).val();

                    if(valor.replace(/ /gi,"") == '')
                    {
                        $(this).addClass('campoRequerido');
                        vacioR = false;
                    }
                }
            );
            return vacioR;
        }


        /**
         * Se encarga de recorrer los id de la respuesta y setear los valores en cada uno de los campos o input html.
         *
         * @return unknown
         */
        var arregloDependientes = new Array(); // arreglo de selects que son dependientes de otros selects.
        function setearCamposHtml(arr)
        {
            var ejecutarDepend = new Array();
            $.each(arr, function(index, value) {
                if ($("#"+index).length > 0)
                {
                    // if(index == 'wfracciones59_defvia_edit') { alert(index+'-'+value+'|'+$("#"+index).attr('multiple')); }

                    if($("#"+index).is("input,select") && $("#"+index).attr("type") != 'checkbox' && $("#"+index).attr('multiple') == undefined) // Si es input o select entonces escribe en un campo u opci? de un select sino escribe en html.
                    {
                        $("#"+index).val(value);
                        //Si es un select y adicionalmente tiene el evento onchange entonces debe ejecutar el evento para que el select dependiente se cargue con las opciones v?idas.
                        if($("#"+index).is("select") && $("#"+index).attr('onchange'))
                        { ejecutarDepend.push( index );  }// "i"=antioquia
                        if($("#"+index).is('.dependiente')) //Municipios
                        { arregloDependientes[index] = value;}//Arreglo en la posicion "wmuni"=medellin
                    }
                    else if($("#"+index).attr("type") == 'checkbox')
                    {
                        if(value == 'on') { $("#"+index).attr("checked","checked"); }
                        else if(value == 'off') { $("#"+index).removeAttr("checked"); }
                    }
                    else if($("#"+index).attr('multiple') != undefined)
                    {
                        var opciones = value.split(",");

                        $("#"+index+" option").each(function(){
                                //alert(jQuery.inArray($(this).val(), opciones));
                                //if(opciones.indexOf($(this).val()) != -1) { $(this).attr("selected","selected"); } // No funciona en IE  >:(
                                if((jQuery.inArray($(this).val(), opciones)) != -1) { $(this).attr("selected","selected"); }
                        });
                    }
                    else
                    { $("#"+index).html(value); }
                }
            });
            for (var i = 0, elemento; elemento = ejecutarDepend[i]; i++) {
                $("#"+elemento).trigger("change");
            }
        }

        function cerrarVentanaPpal()
        {
            window.close();
        }

    </script>

    <style type="text/css">
        .placeholder
        {
          color: #aaa;
        }

        /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMA?  */
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
            width: 200px; /*must have*/
            height: 200px; /*must have*/
        }

        .classOver{
            background-color: #CCCCCC;
        }
        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px }
        .tipo3V:hover {color: #000066; background: #999999;}

        .bgGris1{
            background-color:#F6F6F6;
        }

        .tbold{
            font-weight:bold;
            text-align:left;
        }
        .alng{
            text-align:left;
        }
        .disminuir{
            font-size:11pt;
        }
        .fondoEncabezado{
            background-color: #2A5DB0;
            color: #FFFFFF;
            font-size: 10pt;
            font-weight: bold;
        }

        .campoRequerido{
            border: 1px orange solid;
            background-color:lightyellow;
        }

        .mayuscula{
            text-transform: uppercase;
        }

        #tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        /*#tooltip{
            color: #FE2E2E;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
        }*/
        #tooltip h3, #tooltip div{
            margin:0; width:auto
        }

        #tooltip_pro{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        /*#tooltip{
            color: #FE2E2E;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
        }*/
        #tooltip_pro h3, #tooltip_pro div{
            margin:0; width:auto
        }

        .error{
            font-weight: bold;
            color: red;
        }
        .correct{
            font-weight: bold;
            color: green;
        }
        .endlog{
            font-weight: bold;
            color: orange;
        }

        .ui-autocomplete{
            max-width:  230px;
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
            font-size:  9pt;
        }

        /* NOTIFICACI? */
        #notificacion {
            background-color: #F2F2F2;
            background-repeat: no-repeat;
            font-family: Helvetica;
            font-size: 20px;
            line-height: 30px;
            position: absolute;
            text-align: center;
            width: 30%;
            left: 35%;
            top: -30px;
        }
        .fixed-dialog{
             position: fixed;
             top: 100px;
             left: 100px;
        }

        .ui-dialog
        {
            background: #FFFEEB;
        }

        .texto_add{
            font-size: 8pt;
        }

        .submit{
            text-align: center;
            background: #C3D9FF;
        }
        .pad{
            padding:    4px;
        }

        .margen-superior-eventos{
            margin-top:15px;
            border:2px #2A5DB0 solid;
        }

        .datos-adds-eventos{
            text-align:left; border: 1px solid #cccccc;
        }

        ul{
            margin:0;
            padding:0;
            list-style-type:none;
        }

        table[id^='tabla_lista_cxs_'] td {
            font-size: 8.5pt;
        }

        .alinear_derecha {
            display: block;
            float:right;
            width: 70px;
            text-align: center;
            /*color: #FF2F00;*/
        }

        .div_alinear{
            margin-left: 10px;
        }


        .titulopagina2
        {
            border-bottom-width: 1px;
            /*border-color: <?=$bordemenu?>;*/
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

    </style>
</head>
<body>
<?php
    encabezado("<div class='titulopagina2'>Reporte en consignaci&oacute;n</div>", $wactualiza, "clinica");
?>
<!-- <div style="color:red; font-weight:bold; text-align:center;font-size:14pt;"><img border="0" src="../../images/medical/root/CONSTRUC.GIF"width="30" height="30">[SE EST? DESARROLLANDO ACTUALMENTE]<img border="0" src="../../images/medical/root/CONSTRUC.GIF"width="30" height="30"></div> -->
<!-- <div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?=$wactualiza?></div> -->
<input type='hidden' name='wbasedato_HCE' id='wbasedato_HCE' value="<?=$wbasedato_HCE?>">
<input type='hidden' name='wbasedato_movhos' id='wbasedato_movhos' value="<?=$wbasedato_movhos?>">
<input type='hidden' name='wbasedato_tcx' id='wbasedato_tcx' value="<?=$wbasedato_tcx?>">
<input type='hidden' name='wbasedato' id='wbasedato' value="<?=$wbasedato?>">
<input type='hidden' name='wemp_pmla' id='wemp_pmla' value="<?=$wemp_pmla?>">

<table align="center" style="width:95%;">
    <tr>
        <td style="text-align:left;">
            <div id="contenedor_programa_reporte" align="left">
                <div id="div_filtros" style="width:100%;" align="center">
                    <table id="tabla_filtros" align="center" >
                        <tr>
                            <td colspan="4" class="encabezadoTabla" style="text-align:center;">Filtros del reporte</td>
                        </tr>
                        <tr class="tooltip" title="Fechas de los turnos de cirugías">
                            <td class="encabezadoTabla">Fecha inicio</td>
                            <td class="fila2"><input type="text" class="datoreq" id="fecha_inicio_rep" name="fecha_inicio_rep" value="<?=date("Y-m-d")?>" size="8" ></td>
                            <td class="encabezadoTabla">Fecha fin</td>
                            <td class="fila2"><input type="text" class="datoreq" id="fecha_final_rep" name="fecha_final_rep" value="<?=date("Y-m-d")?>" size="8" ></td>
                        </tr>
                        <tr>
                            <td class="encabezadoTabla">Historia</td>
                            <td class="fila2"><input type="text" class="datoreq" id="whistoria_rep" name="whistoria_rep" value="" size="8" ></td>
                            <td class="encabezadoTabla">Ingreso</td>
                            <td class="fila2"><input type="text" class="datoreq" id="ingreso_rep" name="ingreso_rep" value="" size="8" ></td>
                        </tr>
                        <tr>
                            <td class="encabezadoTabla">Documento</td>
                            <td class="fila2"><input type="text" class="datoreq" id="wdocumento_rep" name="wdocumento_rep" value="" size="8" ></td>
                            <td class="encabezadoTabla">&nbsp;</td>
                            <td class="fila2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="encabezadoTabla" style="text-align:center;"><input type="button" id="btn_generar_rep" name="btn_generar_rep" value="Consultar" onclick="generarReporte();"></td>
                        </tr>
                    </table>
                </div>
                <div id="div_reporte" style="width:100%;" align="center">
                    <table id="tabla_contenedor_rep">
                        <tr class="encabezadoTabla">
                            <td>Filtrar insumos: <input type="text" id="id_search_consulta" name="id_search_consulta" value="" ></td>
                        </tr>
                        <tr>
                            <td id="td_contenedor_rep">
                                <table id="tabla_resultado_reporte" align="center">
                                    <tr class="encabezadoTabla">
                                        <td>
                                            USE LOS FILTROS DEL REPORTE PARA CONSULTAR
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
    </tr>
</table>
<br />
<br />
<table align='center'>
    <tr><td align="center" colspan="9"><input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();"></td></tr>
</table>
<br />
<br />
</body>
</html>