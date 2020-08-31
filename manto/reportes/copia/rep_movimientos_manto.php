<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : rep_movimientos_manto.php
 AUTOR                      : Edwar Jaramillo - Jonatan Lopez.
 FECHA CREACION             : 12 Julio 2018

 DESCRIPCION: Reporte con los datos asociados al servicio web de mantenimiento, el servicio web se consume 
				con el programa ws_cliente_mantenimiento ubicado en /matrix/webservice/ws_cliente_mantenimiento.php

 Notas:
 --
*/ $wactualiza = "(Julio 12 de 2018)"; /*
 ACTUALIZACIONES:
 - Julio 12 de 2018 Edwar Jaramillo - Jonatan Lopez:
    * Fecha de la creación del script.

**/
$consultaAjax='';
$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");





include_once("root/comun.php");
$wbasedato_manto = consultarAliasPorAplicacion($conex, $wemp_pmla, 'mantenimiento');
$wcostosyp = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');

if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
elseif(!isset($accion) && !array_key_exists('user',$_SESSION))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
$user_session      = explode('-',$_SESSION['user']);
$user_session      = $user_session[1];
/*****  DICCIONARIO LOCAL *****/
// define('PROCEDIMIENTO','Procedimiento');

/********************** INICIO DE FUNCIONES *************************/

/**
 * [seguimiento description: Función para uso solo de desarrollo, en ambiente local, crea un archivo de texto donde se imprimen variables y arrays para su seguimiento]
 * @param  [type] $seguir [Cadena de texto a guardar en el archivo, para guardar array recordar usar print_r($al_array, true), puede usar saltos de líea PHP así PHP_EOL ]
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
 * [conexionUnixFn: Función para validar se puede o se hizo conexión a unix, retorna un link con la conexión a unix.]
 * @param  [type] $hay_unix   [description]
 * @param  [type] &$conexUnix [description]
 * @param  [type] &$data      [description]
 * @return [type]             [description]
 */
function conexionUnixFn($hay_unix, &$conexUnix, &$data){
    $conectado_a_unix = false;
    if(!$conectado_a_unix && $hay_unix)
    {
        if($conexUnix = @odbc_connect('facturacion','informix','sco'))
        {
            $conectado_a_unix = true;
        }
        else
        {
            $data['mensaje'] = "Problemas en la conexión a Unix.";
            $data['error']   = 1;
        }
    }
    return $conectado_a_unix;
}


/*
 * @param (string) SQL Query
 * @return multidim array containing data array(array('column1'=>value2,'column2'=>value2...))
 *
 */
function getData($conex, $sql){
    $result = mysql_query($sql, $conex) OR DIE ("Can't get Data from DB , check your SQL Query #>".$sql." - ".mysql_error());
    $data = array();
    while ($row = mysql_fetch_assoc($result)) {
        $fech_dlle = explode(" ", $row["FechaCreacionDlle"]);
        $row["FechaCreacionDlle"] = $fech_dlle[0];
        $row["valor_total"] = number_format($row["valor_total"]*1, 2);
        $data[] = $row; //array_values($row);
    }
    return $data;
}

/********************** FIN DE FUNCIONES *************************/

/**
 * ********************************************************************************************************************************************************
 * Lógica, procesos de los llamados AJAX de todo el programa - INICIO DEL PROGRAMA
 * ********************************************************************************************************************************************************
 */
if(isset($accion))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';
    switch($accion)
    {
        case 'buscar_ajx_opciones':
                $arr_ajx_opciones = array();				
                $sqlOpcTabla = "";
                if($tabla_opc_ajax == '000005'){
                    $sqlOpcTabla = "SELECT  Ccocod AS cod_opcion, Cconom AS nombre_opcion
                            FROM    costosyp_000005
                            WHERE   Ccoemp = '01'
                                    AND CONCAT(Ccocod,'-',Cconom) LIKE '%{$term}%'
                                    AND Ccoest = 'on'
                            ORDER BY Cconom
                            LIMIT   0, 50";
                }

                if($sqlOpcTabla != ''){
                    if($result = mysql_query($sqlOpcTabla,$conex))
                    {						
                        while ($row = mysql_fetch_assoc($result))
                        {
                            $nombre_opcion = preg_replace("/[\n]+/", '', utf8_encode(limpiarString($row["nombre_opcion"])));
                            $cod_opcion = $row["cod_opcion"];
                            $arr_ajx_opciones[] = array("value"  => $cod_opcion.'-'.$nombre_opcion,
                                                        "label"  => $cod_opcion.'-'.$nombre_opcion,
                                                        "codigo" => $cod_opcion,
                                                        "nombre" => $cod_opcion.'-'.$nombre_opcion);
                        }
                    }
                }
				
				if(count($arr_ajx_opciones) == 0){
					$arr_ajx_opciones[] = array("value"  => '%-Todos',
                                                        "label"  => '%-Todos',
                                                        "codigo" => '%',
                                                        "nombre" => '%-Todos');
				}
				
                $data = $arr_ajx_opciones;
            break;

        case 'crear_reporte_movimiento':
                //
                $sql = "SELECT  *
                        FROM    manto_000002
                        WHERE   CentroCostos = '{$wcentro_costo}-{$wemp_pmla}'
                                AND FechaCreacionDlle BETWEEN '{$periodo_ini}' AND '{$periodo_fin}'";
                $data["data"] = array();
                // $data["error_"] = 1;
                // $data["mensaje"] = "Error en consulta";
                for ($i = 1; $i <= 100; $i++) {
                    $data["data"][] = array("Tiger Nixon".$i, $i."System Architect", $i."Edinburgh", $i, "2011/04/25", "$ {$i}320");
                }
            break;

        case 'crear_server_reporte_movimiento':		
		
                $data["error_"]    = 0;
                $data["mensaje"]   = "";
                $data["totalData"] = array( "pageCount" => 0,
                                            "subTotal" => 0.0,
                                            "count"    => 0,
                                            "total"    => 0.0,);

                $sqlPpal = "SELECT  m2.Concepto, m2.Almacen, m2.CentroCostos, m3.NumKCDlle, CONCAT(m3.IdEncDlle,'-',m3.IdDlle) as IdDlle, m3.RepuestoDescripcionDlle, m3.AnhoOTDlle, m3.NumOTDlle, m3.CantidadDlle, m3.ValorUnitarioDlle, m3.IvaDlle, m3.FechaCreacionDlle, m3.FechaRegistroDlle, m3.ValorUnitarioRpDlle, (m3.CantidadDlle * m3.ValorUnitarioDlle) AS valor_total
                            FROM    ".$wbasedato_manto."_000002 AS m2
                                    INNER JOIN
                                   ".$wbasedato_manto."_000003 AS m3 ON (m2.IdEnc = m3.IdEncDlle AND m2.FechaCreacion BETWEEN '{$periodo_ini}' AND '{$periodo_fin}')
                            WHERE   m2.CentroCostos LIKE '%{$wcentro_costo}%'";

                $sqlPpalCount = "   SELECT  COUNT(*) AS recordsTotal, SUM(m3.CantidadDlle * m3.ValorUnitarioDlle) AS valorTotal
                                    FROM    ".$wbasedato_manto."_000002 AS m2
                                            INNER JOIN
                                            ".$wbasedato_manto."_000003 AS m3 ON (m2.IdEnc = m3.IdEncDlle AND m2.FechaCreacion BETWEEN '{$periodo_ini}' AND '{$periodo_fin}')
                                    WHERE   m2.CentroCostos LIKE '%{$wcentro_costo}%'";

                /* Useful $_POST Variables coming from the plugin */
                $draw               = $_POST["draw"];//counter used by DataTables to ensure that the Ajax returns from server-side processing requests are drawn in sequence by DataTables
                $orderByColumnIndex = $_POST['order'][0]['column'];// index of the sorting column (0 index based - i.e. 0 is the first record)
                $orderBy            = $_POST['columns'][$orderByColumnIndex]['data'];//Get name of the sorting column from its index
                $orderType          = $_POST['order'][0]['dir']; // ASC or DESC
                $start              = $_POST["start"];//Paging first record indicator.
                $length             = $_POST['length'];//Number of records that the table can display in the current draw
                $limit              = "";
                if($length != -1){
                    $limit = "limit ".$start.",".$length;
                }
                /* END of POST variables */

                // $recordsTotal = count(getData("SELECT * FROM ".MyTable));
                $resultCount = mysql_query($sqlPpalCount, $conex);
                $recordsTotalRow = mysql_fetch_assoc($resultCount);
                $recordsTotal = $recordsTotalRow["recordsTotal"];
                $data["totalData"]["total"] = "$".number_format($recordsTotalRow["valorTotal"],2);

                $rows = array();

                /* SEARCH CASE : Filtered data */
                if(!empty($_POST['search']['value'])){
                    $where_search = '';
                    /* WHERE Clause for searching */
                    for($i=0 ; $i<count($_POST['columns']);$i++){
                        $column = $_POST['columns'][$i]['data'];//we get the name of each column using its index from POST request
                        if($column != 'valor_total'){
                            $where_search[]="$column like '%".$_POST['search']['value']."%'";
                        } else {
                            $where_search[]="(CantidadDlle * ValorUnitarioDlle) like '%".$_POST['search']['value']."%'";
                        }
                    }
                    $where_search = " AND (".implode(" OR " , $where_search).")";// id like '%searchValue%' or name like '%searchValue%' ....
                    /* End WHERE */

                    $sqlCount = $sqlPpalCount." ".$where_search;//Search query without limit clause (No pagination)
                    $resultCountFilt            = mysql_query($sqlCount, $conex);//Count of search result
                    $recordsTotalFil            = mysql_fetch_assoc($resultCountFilt);
                    $recordsFiltered            = $recordsTotalFil["recordsTotal"];
                    $data["totalData"]["total"] = "$".number_format($recordsTotalFil["valorTotal"],2);

                    /* SQL Query for search with limit and orderBy clauses*/                   
                    $sql = $sqlPpal." ".$where_search." ORDER BY ".$orderBy." ".$orderType." ".$limit;
                    // $rows = getData($conex,$sql);

                    $result = mysql_query($sql, $conex) OR DIE ("Can't get Data from DB , check your SQL Query #>".$sql." - ".mysql_error());
                    $rows = array();
                    while ($row = mysql_fetch_assoc($result)) {
                        $fechReg_dlle                  = explode(" ", $row["FechaRegistroDlle"]);
                        $row["FechaRegistroDlle"]      = $fechReg_dlle[0];
                        $fech_dlle                     = explode(" ", $row["FechaCreacionDlle"]);
                        $row["FechaCreacionDlle"]      = $fech_dlle[0];
                        $data["totalData"]["subTotal"] += ($row["valor_total"])*1;
                        $row["valor_total"]            = "$".number_format(($row["valor_total"])*1,2);
                        $rows[]                        = $row; //array_values($row);
                    }
                }
                /* END SEARCH */
                else {
                    $sql    = $sqlPpal ."ORDER BY ".$orderBy." ".$orderType." ".$limit;
                    $result = mysql_query($sql, $conex) OR DIE ("Can't get Data from DB , check your SQL Query #>".$sql." - ".mysql_error());
                    $rows   = array();

                    while ($row = mysql_fetch_assoc($result)) {
                        $fechReg_dlle                  = explode(" ", $row["FechaRegistroDlle"]);
                        $row["FechaRegistroDlle"]      = $fechReg_dlle[0];
                        $fech_dlle                     = explode(" ", $row["FechaCreacionDlle"]);
                        $row["FechaCreacionDlle"]      = $fech_dlle[0];
                        $data["totalData"]["subTotal"] += ($row["valor_total"])*1;
                        $row["valor_total"]            = "$".number_format($row["valor_total"]*1,2);
                        $rows[]                        = $row;
                    }

                    $recordsFiltered = $recordsTotal;
                }

                $data["totalData"]["subTotal"] = "$".number_format($data["totalData"]["subTotal"],2);

                /* Response to client before JSON encoding */
                $data["draw"]            = intval($draw);
                $data["recordsTotal"]    = $recordsTotal;
                $data["recordsFiltered"] = $recordsFiltered;
                $data["data"]            = $rows;
            break;

        default :
                $data['mensaje'] = $no_exec_sub;
                $data['error'] = 1;
        break;
    }
    echo json_encode($data);
    return;
}


?>
<html lang="es-ES">
<head>
    <title>Movimientos mantenimiento</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery.min.js"></script>

    <script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>

    <script src="../../../include/root/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

    <link type="text/css" href="../../../include/root/select2/select2.min.css" rel="stylesheet"/>
    <script type='text/javascript' src='../../../include/root/select2/select2.min.js'></script>

    <link type="text/css" href="../../../include/root/datatables.min.css" rel="stylesheet"/>
    <script type='text/javascript' src='../../../include/root/datatables.min.js'></script>

    <!-- <link type="text/css" href="../../../include/root/matrix.css" rel="stylesheet"/> -->

    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;
        var dataTableReport;

        var height_window;
        $(function(){
            height_window = ($(window).height()-250);
            // height_window = ($(window).height() - 100);
            // $('#scrollbox').css('max-height', height+'px');
        });

        function isValid(str) {
            return !/[~`´._!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
        }

        function iniFechas(){

            $( "#periodo_ini" ).datepicker({
                defaultDate: "+1w",
                maxDate:"+0D",
                changeYear: true,
                changeMonth: true,
                numberOfMonths: 1,
                // showOn: "button",
                // buttonImage: "../../images/medical/root/calendar.gif",
                showButtonPanel: true,
                dateFormat: 'yy-mm-dd',
                currentText :'Hoy',
                closeText: 'Cerrar',
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                yearRange: '-4:+0',
                // onSelect: function( selectedDate ) {
                    // $( "#periodo_fin" ).datepicker( "option", "minDate", selectedDate );
                // }
                // ,
                // onClose: function(dateText, inst) {
                //     var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                //     var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                //     $(this).datepicker('setDate', new Date(year, month, 1));
                // }
            });

            $( "#periodo_fin" ).datepicker({
                defaultDate: "+1w",
                maxDate:"+0D",
                changeYear: true,
                changeMonth: true,
                numberOfMonths: 1,
                // showOn: "button",
                // buttonImage: "../../images/medical/root/calendar.gif",
                showButtonPanel: true,
                dateFormat: 'yy-mm-dd',
                currentText :'Hoy',
                closeText: 'Cerrar',
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                yearRange: '-4:+0',
                // onSelect: function( selectedDate ) {
                    // $( "#periodo_ini" ).datepicker( "option", "maxDate", selectedDate );
                // }
                // ,
                // onClose: function(dateText, inst) {
                //     var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                //     var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                //     $(this).datepicker('setDate', new Date(year, month, 1));
                // }
            });
        }

        $(document).ready( function ()
        {
            $("#wcentro_costo").val("");
            $("#accordionFiltros, #accordionreporte").accordion({
                collapsible: true,
                heightStyle: "content"
            });
            iniciarAccionesBotones();

            crearAutocomplete("", "campo_autocompletar", "", "", 3, 1, "000005");

            iniFechas();

            initAutocomplete();

            // initDataTableReport();
        });

        /**
         * [jAlert Simula el JAlert usado en las anteriores versiones de JQuery]
         * @param  {[type]} html   [description]
         * @param  {[type]} titulo [description]
         * @return {[type]}        [description]
         */
        function jAlert(html,titulo){
            if($("#jAlert").length == 0)
            {
              var div_jAlert = '<!-- Modal jAlert -->'
                                +'<div class="modal fade" id="jAlert" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">'
                                +'  <div class="modal-dialog" role="document">'
                                +'    <div class="modal-content">'
                                +'      <div class="modal-header">'
                                +'        <h4 class="modal-title" id="alertModalLabel">Modal title</h4>'
                                +'        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">'
                                +'          <span aria-hidden="true">&times;</span>'
                                +'        </button> -->'
                                +'      </div>'
                                +'      <div class="modal-body" >'
                                +'        ...'
                                +'      </div>'
                                +'      <div class="modal-footer">'
                                +'        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>'
                                +'        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->'
                                +'      </div>'
                                +'    </div>'
                                +'  </div>'
                                +'</div>';
              $("body").append(div_jAlert);
            }

            $("#jAlert").find(".modal-header").removeClass("bg-danger");

            $("#jAlert").find("#alertModalLabel").html(titulo);
            $("#jAlert").find(".modal-body").html(html);
            var bg = (titulo.toLowerCase() == 'alerta') ? 'bg-danger': 'bg-primary';
            $("#jAlert").find(".modal-header").addClass(bg);
            $("#jAlert").modal({ backdrop: 'static',
                                 keyboard: false}).css("z-index", 2030);
            if((titulo.toLowerCase() == 'alerta')) { $("#jAlert").css("z-index", 2030); }
        }

        /**
         * [mensajeFailAlert: Muestra un mensaje en pantalla cuando se generó un error en la respuesta ajax]
         * @param  {[type]} mensaje     [description]
         * @param  {[type]} xhr         [description]
         * @param  {[type]} textStatus  [description]
         * @param  {[type]} errorThrown [description]
         * @return {[type]}             [description]
         */
        function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
        {
            var msj_extra = '';
            msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
            $(".alert").alert("Mensaje");
            // jAlert($("#failJquery").val()+msj_extra, "Mensaje");
            jAlert($("#failJquery").val()+msj_extra, "Alerta");
            $("#div_error_interno").html(xhr.responseText);
            // console.log(xhr);
            // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
            fnModalLoading_Cerrar();
            // $(".bloquear_todo").removeAttr("disabled");
        }

        /**
         * [validarFormulario: Se encarga de validar que los campos de cups a inactivar y cups que reemplaza no estén vacíos o sean iguales]
         * @return {[type]} [description]
         */
        function validarFormulario() {
            var novalido = false;

            // $(".form-group").removeClass('has-error');
            $("#needs-validation-filtros").find(".bg-warning").removeClass("bg-warning");
            $("#needs-validation-filtros").find(".form-control").each(function(){
                if($(this).val() === null || $(this).val().replace(/ /gi, "") == '') {
                    var id_spn = $(this).attr('id');
                    console.log(id_spn);
                    // console.log($("#needs-validation-filtros").find("span[id=select2-"+id_spn+"-container]"));
                    novalido = true;
                    // $("#needs-validation-filtros").find("span[id=select2-"+id_spn+"-container]").addClass("bg-warning");
                    $("#needs-validation-filtros").find("input[id="+id_spn+"]").addClass("bg-warning");
                    // $("#needs-validation-filtros").find("input[id="+id_spn+"]").val("bg-warning");
                    // select2-cupAnterior-container
                }
            });

            var wcentro_costo = $("#wcentro_costo").attr("codigo");
            var periodo_ini   = $("#periodo_ini").val();
            var periodo_fin   = $("#periodo_fin").val();
			
			if(periodo_ini > periodo_fin){
				
				jAlert("La fecha final no puede ser menor a la inicial.","Mensaje");
				return;
			}
			
                // generarReporteMovimientos(wcentro_costo, periodo_ini, periodo_fin);
            if(novalido){
                jAlert('Faltan campos por diligenciar','Mensaje');
                // $(".form-group").find(".select2-offscreen").addClass('has-error');
            } else {
                generarReporteMovimientos(wcentro_costo, periodo_ini, periodo_fin);
            }
        }

        function generarReporteMovimientos(wcentro_costo, periodo_ini, periodo_fin){
           
            if(dataTableReport == undefined){
                // initDataTableReport();
                ServerInitDataTableReport();
            } else {
                dataTableReport.ajax.reload();
            }
        }

        /**
         * [initDataTableReport: carga todos los datos]
         * @return {[type]} [description]
         */
        function initDataTableReport(){

            var table = '<table id="dataTableReport" class="table table-striped table-bordered compact" cellspacing="0" width="100%" style="display:none;">'
                        +'        <thead>'
                        +'            <tr>'
                        +'                <th>First name</th>'
                        +'                <th>Last name</th>'
                        +'                <th>Position</th>'
                        +'                <th>Office</th>'
                        +'                <th>Start date</th>'
                        +'                <th>Salary</th>'
                        +'            </tr>'
                        +'        </thead>'
                        +'        <tfoot>'
                        +'            <tr>'
                        +'                <th>First name</th>'
                        +'                <th>Last name</th>'
                        +'                <th>Position</th>'
                        +'                <th>Office</th>'
                        +'                <th>Start date</th>'
                        +'                <th>Salary</th>'
                        +'            </tr>'
                        +'        </tfoot>'
                        +'    </table>';

            $("#datos_reporte").html(table);
            var configDataTable = {
                ajax : {
                            url: "rep_movimientos_manto.php",
                            type: "POST",
                            dataType: "json",
                            data: function(reqData) {
                                var obJson = parametrosComunes();
                                fnModalLoading("Consultando movimientos");
                                reqData.accion        = "crear_reporte_movimiento";
                                reqData.wemp_pmla     = obJson.wemp_pmla;
                                reqData.consultaAjax  = "";
                                reqData.wcentro_costo = $("#wcentro_costo").attr("codigo");
                                reqData.periodo_ini   = $("#periodo_ini").val();
                                reqData.periodo_fin   = $("#periodo_fin").val();
                                console.log(reqData);
                            },
                            dataSrc: function (data) {
                                if(data.error_ == 1)
                                {
                                    fnModalLoading_Cerrar();
                                    jAlert(data.mensaje, "Mensaje");
                                }
                                else
                                {
                                    fnModalLoading_Cerrar();
                                }
                                return( data.rows );
                            },
                            error: function (xhr, error, thrown) {
                                fnModalLoading_Cerrar();
                                mensajeFailAlert('', xhr, error, errorThrown);
                                // $(".datos_reporte-grid-error").html("");
                                // $("#datos_reporte").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                $("#dataTableReport_processing").css("display","none");
                                return false;
                            }
                },
                language   : { "url": "../../../include/root/dataTables.lang/dataTables.Spanish.lang.json?v="+Math.random() },
                buttons    : [  "pageLength",
                                "copy",
                                "excel",
                                "csv",
                                "pdf",
                                "print", {
                                    text: 'Actualizar',
                                    action: function ( e, dt, node, config ) {
                                        dt.ajax.reload();
                                    }
                                },
                                "colvis"
                            ],
                dom        : "Bfrtip",
                pageLength : 10,
                lengthMenu :    [
                                     [10, 25, 50],
                                     ['10 filas por página', '25 filas por página', '50 filas por página']
                                ],
                responsive :  true
            }

            dataTableReport = $('#dataTableReport').DataTable(configDataTable);

            $("#dataTableReport").show();
        }

        /**
         * [ServerInitDataTableReport: consultar por acción]
         * @return {[type]} [description]
         */
        function ServerInitDataTableReport(){
            // var table = '<table id="dataTableReport" class="compact table table-bordered table-hover order-column" cellspacing="0" cellspading="0" width="100%" style="display:none;">'
            var table = '<table id="dataTableReport" class="table table-striped table-bordered table-hover order-column" cellspacing="0" width="100%" style="display:none;">'
                        +'        <thead class="encabezadoTabla">'
                        +'            <tr>'
                        +'                <th>Concepto</th>'
                        +'                <th>Almacen</th>'
                        +'                <th>Centro costos</th>'
                        +'                <th>NumKC</th>'
                        +'                <th>Id Detalle</th>'
                        +'                <th>Repuesto descripción</th>'
                        +'                <th>Año OT</th>'
                        +'                <th>Número OT</th>'
                        +'                <th>Cantidad</th>'
                        +'                <th>Valor unitario</th>'
                        +'                <th>Iva</th>'
                        +'                <th>Fecha creacion</th>'
                        +'                <th>Fecha registro</th>'
                        +'                <th>Valor unitario Rp</th>'
                        +'                <th>Valor total</th>'
                        +'            </tr>'
                        +'        </thead>'
                        +'        <tfoot class="">'
                        +'            <tr class="css-sub-total bg-primary">'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th colspan="" style="text-align: right;"></th>'
                        +'                <th class="sub-total"  style="text-align: right;"></th>'
                        +'            </tr>'
                        +'            <tr class="css-total encabezadoTabla">'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th></th>'
                        +'                <th colspan="" style="text-align: right;"></th>'
                        +'                <th class="total"  style="text-align: right;"></th>'
                        +'            </tr>'
                        +'        </tfoot>'
                        +'    </table>';

            $("#datos_reporte").html(table);

            var configDataTable = {
                "processing": true,
                "serverSide": true,
                "order": [[ 0, "asc" ]],
                "scrollX": true,
                ajax : {
                            url: "rep_movimientos_manto.php",
                            type: "POST",
                            dataType: "json",
                            data: function(reqData) {
                                var obJson = parametrosComunes();
                                fnModalLoading("Consultando movimientos");
                                reqData.accion        = "crear_server_reporte_movimiento";
                                reqData.wemp_pmla     = obJson.wemp_pmla;
                                reqData.consultaAjax  = "";
                                reqData.wcentro_costo = $("#wcentro_costo").attr("codigo");
                                reqData.periodo_ini   = $("#periodo_ini").val();
                                reqData.periodo_fin   = $("#periodo_fin").val();
                            },
                            dataSrc: function (json) {
                                if(json.error_ == 1)
                                {
                                    fnModalLoading_Cerrar();
                                    jAlert(json.mensaje, "Mensaje");
                                }
                                else
                                {
                                    fnModalLoading_Cerrar();

                                    // this.api();
                                    var currency = $("#currency option:selected").text();
                                    var subTotal = $("#datos_reporte tfoot tr.css-sub-total");
                                    // var subTotal = $(dataTableReport.DataTable().table().footer()).find("tr.page-total");
                                    var total = $("#datos_reporte tfoot tr.css-total");
                                    /*if(json.totalData.hasOwnProperty("subTotal") ){
                                        subTotal.show();
                                        subTotal.find("th")[15].innerHTML = "Subtotal: "+json.totalData.subTotal + " " + currency;
                                    } else {
                                        subTotal.hide();
                                        subTotal.find("th")[15].innerHTML = "Subtotal: "+json.totalData.total + " " + currency;
                                    }*/
                                    // console.log(json.totalData.subTotal);
                                    // subTotal.find("th.sub-total").html("Subtotal: "+json.totalData.subTotal + " " + currency);
                                    subTotal.find("th.sub-total").html(json.totalData.subTotal);
                                    // console.log(subTotal.find("th").eq(15));
                                    // total.find("th.total").html("TOTAL: "+json.totalData.total + " " + currency );
                                    total.find("th.total").html(json.totalData.total);
                                }
                                // console.log(data.rows);
                                return( json.data );
                            },
                            error: function (xhr, error, errorThrown) {
                                fnModalLoading_Cerrar();
                                mensajeFailAlert('', xhr, error, errorThrown);
                                // $(".datos_reporte-grid-error").html("");
                                // $("#datos_reporte").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                $("#dataTableReport_processing").css("display","none");
                                return false;
                            }
                },
                language   : { "url": "../../../include/root/dataTables.lang/dataTables.Spanish.lang.json?v="+Math.random() },
                buttons    : [  "pageLength",
                                "colvis",
                                {extend :"copy", footer:true,exportOptions: {
                                                                columns: ':visible'
                                                            }},
                                {extend :"excel", footer:true,exportOptions: {
                                                                columns: ':visible'
                                                            }},
                                {extend :"csv", footer:true,exportOptions: {
                                                                columns: ':visible'
                                                            }},
                                {extend :"pdf", footer:true,exportOptions: {
                                                                columns: ':visible'
                                                            }},
                                {extend :"print", footer:true,exportOptions: {
                                                                columns: ':visible'
                                                            }},
                                {
                                    text: 'Actualizar',
                                    action: function ( e, dt, node, config ) {
                                        dt.ajax.reload();
                                    }
                                }
                            ],
                dom        : "Bfrtip",
                pageLength : 10,
                lengthMenu :    [
                                     [10, 25, 50],
                                     ['10 filas por página', '25 filas por página', '50 filas por página']
                                ],
                // responsive :  true,
                deferRender: true,
                columns : [
                    {"data" : "Concepto"},
                    {"data" : "Almacen"},
                    {"data" : "CentroCostos"},
                    {"data" : "NumKCDlle"},
                    {"data" : "IdDlle"},
                    {"data" : "RepuestoDescripcionDlle"},
                    {"data" : "AnhoOTDlle"},
                    {"data" : "NumOTDlle"},
                    {"data" : "CantidadDlle"},
                    {"data" : "ValorUnitarioDlle"},
                    {"data" : "IvaDlle"},
                    {"data" : "FechaCreacionDlle"},
                    {"data" : "FechaRegistroDlle"},
                    {"data" : "ValorUnitarioRpDlle"},
                    {"data" : "valor_total"},
                ],
                columnDefs: [
                  { className: "text-right", "targets": [9,10,11,12] }
                ],
                initComplete: function(settings, json) {
                    $('#dataTableReport_filter input').unbind();
                    $('#dataTableReport_filter input').bind('keyup', function(e) {
                        if(e.keyCode == 13) {
                            dataTableReport.search( this.value ).draw();
                        } else if ((e.keyCode == 8 || e.keyCode == 46) && this.value.replace(/ /gi, "") == "") {
                            // console.log(this.value);
                            $("dataTableReport_filter input").val("");
                            dataTableReport.search( "" ).draw();
                        }
                    });
                }
            }

            dataTableReport = $('#dataTableReport').DataTable(configDataTable);

            $("#dataTableReport").show();
        }

        /**
         * [iniciarAccionesBotones: Se usa para inicializar todos los objetos html que deben reaccionar ante los botones de zoom
         *                                 adicionalmente se inicializan las acciones sobre el formulario p.ej. Guardar, Imprimir, ...]
         * @return {[type]} [description]
         */
        function iniciarAccionesBotones()
        {
            var btn_generar_reporte = $(".btn_generar_reporte");
            btn_generar_reporte.off('click').on('click',function(){
                validarFormulario();
            });

            // var btn_generar_reporte = $(".btn_generar_reporte");
            // btn_generar_reporte.off('click').on('click',function(){
            //     validarFormulario();
            // });
        }

        function initAutocomplete()
        {
            $('.campo_autocompletar').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });
        }

        /**
         * [crearAutocomplete: Inicializa las listas seleccionables en los campos que se definene como autocompletar]
         * @param  {[type]} accion                 [Acción o comportamiento especial que debe asumir la función dado el valor que llega en este parámetro]
         * @param  {[type]} arr_opciones_seleccion [Array de las opciones que debe desplegar el campo autocompletar]
         * @param  {[type]} campo_autocomplete     [ID html del campo que será y se iniciará como autocomplete]
         * @param  {[type]} codigo_default         [Código por defecto con el que podría iniciar el autocomplete]
         * @param  {[type]} nombre_default         [Nombre por defecto con el que podría iniciar el autocomplete]
         * @param  {[type]} limite_buscar          [Límite mínimo de caracteres con el que debería empezar a funcionar el autocomplete]
         * @param  {[type]} busqueda_ajax          [Se realiza consulta mediante ajax cada que se escriba cierta cantidad inicial de caracteres, sobre todo cuando son maestros muy grandes y no se cargan al html]
         * @param  {[type]} tabla_opc_ajax         [Tabla de opciones de transcripción en la que se va a buscar]
         * @return {[type]}                        [description]
         */
        function crearAutocomplete(arr_opciones_seleccion, campo_autocomplete, codigo_default, nombre_default, limite_buscar, busqueda_ajax, tabla_opc_ajax)
        {
            $("#"+campo_autocomplete).val(nombre_default);
            $("#"+campo_autocomplete).attr("codigo",codigo_default);
            $("#"+campo_autocomplete).attr("nombre",nombre_default);

            arr_datos = new Array();
            //var datos = arr_wempresp;//eval( $("#arr_wempresp").val() );
            if(busqueda_ajax == 0){
                var datos = eval('(' + $("#"+arr_opciones_seleccion).val() + ')');
                var index = -1;
                for (var CodVal in datos)
                {
                    index++;
                    arr_datos[index] = {};
                    arr_datos[index].value  = CodVal+'-'+datos[CodVal];
                    arr_datos[index].label  = CodVal+'-'+datos[CodVal];
                    arr_datos[index].codigo = CodVal;
                    arr_datos[index].nombre = CodVal+'-'+datos[CodVal];
                }
            } else {
                var fn_ajax_buscar = "rep_movimientos_manto.php?accion=buscar_ajx_opciones&wemp_pmla="+$("#wemp_pmla").val()+"&consultaAjax=&tabla_opc_ajax="+tabla_opc_ajax;
            }

            // console.log(arr_datos);
            if($("#"+campo_autocomplete).length > 0)
            {
                var params_auto = {
                        minLength : limite_buscar,
                        select: function( event, ui ) {
                            // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            $("#"+campo_autocomplete).attr("codigo",cod_sel);
                            $("#"+campo_autocomplete).attr("nombre",nom_sel);
                        }
                };

                if(busqueda_ajax == 0){ params_auto.source = arr_datos; }
                else { params_auto.source = fn_ajax_buscar; }
                $("#"+campo_autocomplete).autocomplete(params_auto);//.addClass("ui-autocomplete-loading");
            }
            else if($("."+campo_autocomplete).length > 0)
            {
                var params_auto = {
                        minLength : limite_buscar,
                        select: function( event, ui ) {
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            var id_el = $(this).attr("id");
                            $("#"+id_el).attr("codigo",cod_sel);
                            $("#"+id_el).attr("nombre",nom_sel);
                        }
                };

                if(busqueda_ajax == 0){ params_auto.source = arr_datos; }
                else { params_auto.source = fn_ajax_buscar; }
                $("."+campo_autocomplete).autocomplete(params_auto);//.addClass("ui-autocomplete-loading");
            }
        }

        /**
         * [fnModalLoading: Es función se encarga de mostrar una ventana modal cada vez que se hace un llamado ajax con el fin de bloquear la página web hasta que se
         *                    se genere una respuesta y evitar que el usuario genere más eventos (click) sin terminar la petición anterior y evitar problemas
         *                    en la veracidad de datos]
         * @return {[type]} [description]
         */
        function fnModalLoading(msje_anexo)
        {
            var msj = (msje_anexo == undefined) ? '': msje_anexo;
            $("#div_loading").find("#msj_anexo_loading").html(msj);
            $("#div_loading").modal({backdrop: 'static',
                                    keyboard: false});
        }

        /**
         * [fnModalLoading_Cerrar: complemento a la función fnModalLoading, esta se encarga de cerrar la ventana modal]
         * @return {[type]} [description]
         */
        function fnModalLoading_Cerrar()
        {
            $("#div_loading").modal('hide');
            $("#div_loading").find("#msj_anexo_loading").html("");
            /*if($("#div_loading").is(":visible"))
            {
                console.log("ok");
                $("#div_loading").modal('hide');
                // $("#div_loading").dialog('close');
                // $('#myModal').hide();
                // $('.modal-backdrop').hide();
            }else{
                console.log("ok2");
            }*/
            // console.log($("#div_loading"));
        }

        /**
         * [parametrosComunes: Genera un json con las variables más comunes que se deben enviar en los llamados ajax, evitando tener que crear los mismos parámetros de envío
         *                     en cada llamado ajax de forma manual.]
         * @return {[type]} [description]
         */
        function parametrosComunes()
        {
            var obJson             = {};
            obJson['wemp_pmla']    = $("#wemp_pmla").val();
            obJson['wcostosyp']    = $("#wostosyp").val();
            obJson['consultaAjax'] = '';
            return obJson;
        }

        function enterBuscar(e)
        {
            var tecla = (document.all) ? e.keyCode : e.which;
            if(tecla == 13)
            {
                // btn_consultarDatos();
            }
        }

        function reiniciarTooltip()
        {
            $('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
        }

    </script>

    <script type="text/javascript">

        function isset ( strVariableName ) {
            try {
                eval( strVariableName );
            } catch( err ) {
                if ( err instanceof ReferenceError )
                   return false;
            }
            return true;
        }


        function ocultarElemnto(elemento){
            $("#"+elemento).hide(1000);
        }

        function soloNumeros(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            // console.log(charCode);
             if ((charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36) || (charCode == 46)) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
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
            // console.log(charCode);
             // if (charCode > 31 && (charCode < 48 || charCode > 57))
             // if ((charCode < 48 && charCode > 57) || (charCode < 65 && charCode > 90) || (charCode < 97 && charCode > 122 ))
             if (((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) || charCode == 8 || charCode == 9) && (charCode != 46))
                return true;

             return false;
        }

        /**
         * Para aceptar caracteres numéicos, letras y algunos otros caracteres permitidos
         *
         * @return unknown
         */
        function soloCaracteresPermitidos(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            // alert(charCode);
            /*
                (charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) // Números, letras minusculas y mayusculas
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

        function trOver(grupo)
        {
            $(grupo).addClass('classOver');
        }

        function trOut(grupo)
        {
            $(grupo).removeClass('classOver');
        }

        function cerrarVentanaPpal()
        {
            window.close();
        }

    </script>

    <style type="text/css">
        .fila1 {
            background-color:   #C3D9FF;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .fila2 {
            background-color:   #E8EEF7;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .encabezadoTabla {
            background-color : #2a5db0;
            color            : #ffffff;
            font-size        : 9pt;
            font-weight      : bold;
            padding          : 1px;
            font-family      : verdana;
        }

        .classOver{
            background-color: #CCCCCC;
        }

        .classOverFormula{
            background-color: #B5EFA6;
        }

        .bgGris1{
            background-color:#F6F6F6;
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

        #tooltip h3, #tooltip div{
            margin:0; width:auto
        }

        #tooltip_pro{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        #tooltip_pro h3, #tooltip_pro div{
            margin:0; width:auto
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

        .onfocus{
            background-color: lightyellow;
        }

        .font_label{
            font-size: 16px;
            white-space: nowrap;
        }

        fieldset{
            border: 2px solid #e2e2e2;
        }

        legend{
            border: 2px solid #e2e2e2;
            border-top: 0px;
            font-family: Verdana;
            background-color: #e6e6e6;
            font-size: 11pt;
        }

        ul{
            margin:0;
            padding:0;
            list-style-type:none;
        }

        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            border-bottom: 5px solid #3498db;
            width: 30px;
            height: 30px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
          0% { -webkit-transform: rotate(0deg); }
          100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

        fieldset.scheduler-border {
            border: solid 1px #DDD !important;
            padding: 0 10px 10px 10px;
            border-bottom: none;
            width: 50%;
        }

        legend.scheduler-border {
            width: auto !important;
            border: none;
            font-size: 14px;
        }

        .version {
            font-family: verdana;
            font-size: 8px;
        }

        .campo_autocompletar {
            width: 270px;
        }
        .ui-autocomplete-loading {
            background: white url("../../images/medical/ajax-loader5.gif") right center no-repeat;
            background-size: 15px 15px;
        }

        .ui-autocomplete {
            max-height: 150px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            font-size:  9pt;
        }

        /*.ui-autocomplete{
            max-width:  230px;
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
            font-size:  9pt;
        }*/

        /*.ui-datepicker-calendar {
            display: none;
        }*/

        .table-hover>tbody>tr:hover>td, .table-hover>tbody>tr:hover>th {
          background-color: #cccccc;
          /*color:#eeeeee;*/
        }

        table.dataTable.order-column tbody tr > .sorting_1,
        table.dataTable.order-column tbody tr > .sorting_2,
        table.dataTable.order-column tbody tr > .sorting_3 {
            background-color: #f2f2f2;
        }

        .css-sub-total{
            padding: 0px;
            font-size: 8pt;
        }
        .css-total{
            padding: 0px;
            font-size: 8pt;
        }

    </style>
</head>
<body width="100%">
<?php
    encabezado("<div class='titulopagina2'>Reporte de movimientos - mantenimiento</div>", $wactualiza, "clinica");
?>
<input type='hidden' name='wemp_pmla' id='wemp_pmla' value="<?=$wemp_pmla?>">
<input type='hidden' name='wbasedato_cliame' id='wbasedato_cliame' value="<?=$wbasedato_manto?>">
<input type='hidden' name='modo_pruebas' id='modo_pruebas' value="<?=$modo_pruebas?>">
<input type='hidden' name='wostosyp' id='wostosyp' value="<?=$wostosyp?>">

<div class="container">
    <table align="center">
        <tr>
            <td style="text-align:left;">
                <div id="contenedor_programa_rep_manto">                                                     
					<form id="needs-validation-filtros" style="margin-right: auto;margin-left: auto;" method='post'>
						<!-- <div class="control-group">
							<form class="form-horizontal">
								<div class="form-group fila1">
									<label class="col-sm-12" for="centro_costo">Centro de costo:</label>
									<div class="col-sm-6">
										<input type="text" class="form-control form-control-sm" id="centro_costo" placeholder="Centro de costo">
									</div>
								</div>
								<div class="form-group fila1">
									<div class="col-sm-6">
										<label class="control-label" for="periodo_ini">Año/Mes inicial:</label>
										<input type="text" class="form-control form-control-sm" id="periodo_ini" placeholder="Periodo inicial">
									</div>
									<div class="col-sm-6">
										<label class="control-label" for="periodo_fin">Año/Mes final:</label>
										<input type="text" class="form-control form-control-sm" id="periodo_fin" placeholder="Periodo final">
									</div>
								</div>
								<div class="form-group encabezadoTabla">
									<div class="col-sm-6">
										<button type="submit" class="btn btn-default">Generar</button>
									</div>
								</div>
							</form>
						</div> -->
						<div align="center">
							<table style='width:600px;' class='table'>
								<tbody>
									<tr>
										<td scope="row" class="encabezadoTabla">
											<label class="control-label" for="wcentro_costo">Centro de costo:</label>
										</td>
										<td class="fila1">
											<input type="text" class="form-control-sm campo_autocompletar" id="wcentro_costo" codigo="" nombre="" placeholder="Centro de costo" required >
										</td>
									</tr>
									<tr>
										<td scope="row" class="encabezadoTabla">
											<label class="control-label" for="periodo_ini">Año/Mes inicial:</label>
										</td>
										<td class="fila1">
											<input type="text" class="form-control-sm" id="periodo_ini" placeholder="AAAA/MM/DD Periodo inicial">
										</td>
									</tr>
									<tr>
										<td scope="row" class="encabezadoTabla">
											<label class="control-label" for="periodo_fin">Año/Mes final:</label>
										</td>
										<td class="fila1">
											<input type="text" class="form-control-sm" id="periodo_fin" placeholder="AAAA/MM/DD Periodo final">
										</td>
									</tr>
									<tr>
										<td align="center" scope="row" colspan="2" class=""><button type="button" class="btn btn-default btn_generar_reporte">Generar</button></td>
									</tr>
								</tbody>
							</table>
						</div>
					</form> 
                    <hr>                   
                        <h3>Resultado reporte</h3>
                        <div class='' id='datos_reporte'>
                            <!--  -->
                            <h3 style="margin-right: auto; margin-left: auto;text-align: center;" class="bg-info">Reporte sin generar</h3>
                        </div>
                   
                    <br>
                </div>
            </td>
        </tr>
    </table>
    <br />
    <br />
    <table align='center'>
        <tr><td align="center" colspan="9"><input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();"></td></tr>
    </table>
</div>
<br />
<br />

<!-- <div id="div_loading" style="display:none;"><img width="15" height="15" src="../../images/medical/ajax-loader5.gif" /> Consultando datos, espere un momento por favor...</div> -->
<input type='hidden' name='failJquery' id='failJquery' value='El programa terminó de ejecutarse pero con algunos inconvenientes <br>(El proceso no se completó correctamente)' >

<!-- Modal loading -->
<div class="modal fade" id="div_loading" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title" id="loadingModalLabel">Procesando ...</h4>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 col-sm-4">
                    <div class="loader pull-right"></div>
                </div>
                <div class="col-md-6 col-sm-6">Espere un momento por favor... <span class="text-info" id="msj_anexo_loading"></span></div>
            </div>
        </div>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div> -->
    </div>
  </div>
</div>

</body>
</html>