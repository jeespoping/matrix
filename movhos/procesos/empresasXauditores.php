<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa     :   Administraci&oacute;n de empresas cenimp
 * Fecha        :   2013-06-14
 * Por          :   Camilo Zapata
 * Descripcion  :
 * Condiciones  :
 *********************************************************************************************************
 Acerca de este programa:  este programa es una modificación de empresas HCE solo que mueve las tablas en movhos
 en las que se asocian los auditores corporativos con las entidades promotoras de servicios de salud


 **********************************************************************************************************
 ACTUALIZACIONES.

 2015-05-13 Jessica Madrid. Se modifica el envío de parámetros get por post para corregir problema con url muy larga que no permite insertar entidades.
 2015-04-07 camilo zz. se corrigieron los tooltips de las entidades ajenas( asociadas a otro auditor )
 **********************************************************************************************************/

$wactualiz = "2015-05-13";

if(!isset($_SESSION['user'])){
    echo "error";
    return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');


include_once("root/comun.php");
include_once("movhos/movhos.inc.php");



$conex           = obtenerConexionBD("matrix");
$wbasedato       = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenimp");
$wmovhosbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos");
$wcliamebasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame");
$rolAuditor      = consultarAliasPorAplicacion( $conex, $wemp_pmla, "codigoRolAuditores" );
$hce             = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
$pos             = strpos($user,"-");
$wuser           = substr($user,$pos+1,strlen($user));
$auditores       = datosAuditores( $rolAuditor );
( array_key_exists( $wuser, $auditores ) ) ? $usuarioAuditor = "on" : $usuarioAuditor = "off";

if( isset($consultaAjax) == false ){

?>
    <html>
    <head>
    <title>Empresas X Auditor</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

<script>

//************cuando la pagina este lista...**********//
    $(document).ready(function() {
        //agregar eventos a campos de la pagina
        $("#enlace_retornar").hide();
        $("#enlace_retornar").click(function() {
            restablecer_pagina();
            $("#lista_auditores option").eq(0).attr('selected',true);
        });

        $("#lista_auditores").change(function() {
            realizarConsultaEntidades($(this));
        });
        //$("#entidades input").attr("disabled",true);
        $("#div_tabla_filtros").hide();

        //Cuando presionen la tecla Enter dentro del input "buscar"
        $("#buscador").on("keyup", function(e) {
            if(e.which == 13){
                filtrarConBusqueda( $(this).val() );
            }
        });
            seleccionarEntidadesIniciales();
            $(document).ready(function () {
               $("input#input_buscador").quicksearch("table#entidades tbody tr.entidadesEncontradas");
            });
            try{$(".entidadAjena").tooltip("destroy");}catch(e){ console.log("excepcion") }
            $(".entidadAjena[title!='']").tooltip({track: false, delay: 0, showURL: false, opacity: 0.95, left: -50, duration: 1500 });

            $( this ).scroll(function() {
                $( "div[id^='ui-tooltip-']" ).hide();
            });

    });

    function guardar_estado_agrupacion( obj ){
        var codigoGrupo = $("#lista_agrupaciones").val();
        var wemp_pmla = $("#wemp_pmla").val();

        obj = jQuery(obj);

        estado = obj.is(':checked');
        if( estado == true )
            estado='on';
        else
            estado='off';

        if( codigoGrupo == "" ){
            return;
        }
        $.blockUI({ message: $('#msjEspere') });

        //se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
        var rango_superior = 245;
        var rango_inferior = 11;
        var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

        //Realiza el llamado ajax con los parametros de busqueda
        $.get('empresasXauditores.php', { wemp_pmla: wemp_pmla, action: "guardarEstadoAgrupacion", grupo: codigoGrupo, estado:estado, consultaAjax: aleatorio} ,
            function(data) {
                $.unblockUI();
                if( data == 'OK' ){
                    $("#lista_agrupaciones option:selected").attr('estado', estado); //Le cambio el atributo "estado" a la opcion del select
                    if( estado == 'on' )
                        alerta("La agrupaci&oacute;n ha sido ACTIVADA");
                    else
                        alerta("La agrupaci&oacute;n ha sido INACTIVADA");
                }else{
                    alert('Error al cambiar el estado de la agrupaci&oacute;n \n '+data);
                }
            });
    }

    function eliminar_agrupacion(){

    }

    function filtrarConBusqueda( valor ){

        valor = $.trim( valor );
        valor = valor.toUpperCase();
        if( valor.length < 4 ){
            alerta("Ingrese al menos 4 caracteres para realizar la busqueda");
            return;
        }
        $("#div_tabla_filtros").show();

        $.blockUI({ message: $('#msjEspere') });

        var identificaciones_entidades = new Array();

        var patt1 = new RegExp( valor , "g" );

        $('.parabuscar').each(function(){
            texto = $(this).text();
            texto = $.trim(texto);
            if ( patt1.test( texto ) ) {
                identificador = $(this).attr("empresa");
                if( inArray( identificador, identificaciones_entidades) == false) {
                    identificaciones_entidades.push( identificador );
                }
            }
        });
        var i=0;
        var html_codigo = "";
        for( i in identificaciones_entidades ){
            html_codigo+="<tr class='fila1'>";
            $("#entidades [empresa="+identificaciones_entidades[i]+"]").each(function(){
                html_codigo+="<td empresa='"+identificaciones_entidades[i]+"'>"+$(this).html()+"</td>";
            });
            html_codigo+="</tr>";
        }
        $("#tabla_filtros tr:not(.noborrar)").remove();
        $("#tabla_filtros").append( html_codigo );

        $("#tabla_filtros input:checked").parent().addClass('fondoAmarillo');
        $("#tabla_filtros input:checked").parent().next().addClass('fondoAmarillo');
        $("#tabla_filtros input:checked").parent().next().next().addClass('fondoAmarillo');
        $("#tabla_filtros input:checked").parent().next().next().next().addClass('fondoAmarillo');
        $.unblockUI();
    }

    //Funcion que se activa cuando se presiona el enlace "retornar"
    function restablecer_pagina(){
        //$("#lista_agrupaciones option").eq(0).attr('selected',true);
        $("#entidades input").attr("disabled",true);
        $("#tabla_filtros input").attr("disabled",true);
        $("#form_nueva_agrupacion").hide( 'drop', {}, 500 );
        $("#enlace_retornar").hide();
        $("#entidades .entidad").attr('checked', false );
        $("#tabla_filtros .entidad").attr('checked', false );
        $("#entidades .fondoAmarillo").removeClass('fondoAmarillo');
        $("#tabla_filtros .fondoAmarillo").removeClass('fondoAmarillo');
        $("#tr_estado_agrupacion").hide();
        borrarEntidadesElegidas();
    }

    //funcion que luego de elegir el centro de costos, me trae los pacientes que se encuentran en el
    function realizarConsultaEntidades( select ){

        var wemp_pmla     = $("#wemp_pmla").val();
        var codigoAuditor = $("#lista_auditores").val();
        var nombreAuditor = $(select).find("option:selected").html();
        if(codigoAuditor == "")
            return;

        //muestra el mensaje de cargando
        $.blockUI({ message: $('#msjEspere') });
        $("#enlace_retornar").fadeIn('slow');

        //se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
        var rango_superior = 245;
        var rango_inferior = 11;
        var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

        //Realiza el llamado ajax con los parametros de busqueda
        $.get('empresasXauditores.php', { wemp_pmla: wemp_pmla, action: "mostrarListaEntidades", codigoAuditor: codigoAuditor, consultaAjax: aleatorio} ,
            function(data) {
                $.unblockUI();
                $(".tablaClonada").remove();
                nuevaTabla = $("#tabla_seleccionadas").clone();
                $(nuevaTabla).attr("id", "tabla_auditor_"+codigoAuditor);
                $(nuevaTabla).addClass("tablaClonada");
                $(nuevaTabla).show();
                var fila = 0;
                $(nuevaTabla).find("tr").each(function(){
                    if( fila > 1 ){
                        $(this).remove();
                    }
                    fila++;
                })
                $("#dialogo_datosAuditores").append(nuevaTabla);

                parsearEntidades( data, nuevaTabla, nombreAuditor );
            });
    }

    function parsearEntidades( entidades, nuevaTabla, nombreAuditor ){
        if( $.trim(entidades) == "" ){
            alerta("No se cargaron entidades asociadas al auditor");
            return;
        }
        entidades = entidades.split(",");

        for( var i = 0; i < entidades.length; i++ ){
           entidadAuditor = $("input.entidad[value='"+entidades[i]+"']").parent().next("td");
           var codigo = entidadAuditor.html();
           var nit    = $(entidadAuditor).next("td").html();
           var nombre = $(entidadAuditor).next("td").next("td").html();
           entidadAuditor = "<tr class='fila1'><td>&nbsp;</td><td>"+codigo+"</td><td>"+nit+"</td><td>"+nombre+"</td></tr>";
           $(nuevaTabla).append( entidadAuditor );
        }

        $("#dialogo_datosAuditores").dialog({
                 title: " ENTIDADES ASOCIADAS AL AUDITOR: "+nombreAuditor,
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                        $( "#lista_auditores>[value='']" ).attr("selected", true);
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

    function elegir_entidad( ele ){
        ele = jQuery( ele );
        var valor = ele.val();
        setTimeout(function(){
            var empresa = ele.parent().attr("empresa");
            if( ele.is(':checked') ){
                $("[empresa="+empresa+"]").addClass('fondoAmarillo');
                agregarEntidadElegidas( empresa );
                $("input[value="+valor+"]").attr("checked", true);
            }else{
                $("[empresa="+empresa+"]").removeClass('fondoAmarillo');
                quitarEntidadElegidas( empresa );
                $("input[value="+valor+"]").attr("checked", false);
            }
            if(!$("#contenedor_estadoActual").is(":visible"))
                $("#contenedor_estadoActual").show();
         }, 500);
    }

    function agregarEntidadElegidas( codigo_empresa ){

            html_codigo ="<tr class='fila1' empresaelegida="+codigo_empresa+">";
            $("#entidades [empresa="+codigo_empresa+"]").each(function(){
                    html_codigo+="<td class='fondoAmarillo' empresa='"+codigo_empresa+"' >"+$(this).html()+"</td>";
            });
            html_codigo+="</tr>";

            $("#tabla_seleccionadas").append( html_codigo );
    }

    function quitarEntidadElegidas( codigo_empresa ){
        $("#tabla_seleccionadas").find("tr[empresaelegida="+codigo_empresa+"]").remove();
    }

    function guardarEntidades(){
        var cadena = "";
        var ind=0;
        var wemp_pmla = $("#wemp_pmla").val();

        var agregadas = new Array();

        $("#entidades").find(".entidad:checked").each(function(){
                if(ind>0)
                    cadena+=",";

                cadena+= ""+$(this).val();
                ind++;
        });

        var auditor = $("#wuser").val();
        if( auditor == "" ){
            return;
        }

        $.blockUI({ message: $('#msjEspere') });
        $("#enlace_retornar").fadeIn('slow');

        //se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
        var rango_superior = 245;
        var rango_inferior = 11;
        var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

        //Realiza el llamado ajax con los parametros de busqueda
        $.post('empresasXauditores.php', { wemp_pmla: wemp_pmla, action: "guardarEntidades", wcodaud: auditor, entidades: cadena, consultaAjax: aleatorio} ,
        function(data) {
            $.unblockUI();
            data = $.trim(data);
            if( data == 'OK' ){
                alerta("Exito al guardar");
            }else{
                alerta("Ha ocurrido un error al guardar");
                //restablecer_pagina();
            }
        });
    }

    function borrarEntidadesElegidas(){
        $("#tabla_seleccionadas tr:not(.noborrar)").remove();
    }

    function recargarEntidades(){
        var opcion = $("#select_filtrar").val();
        var wemp_pmla = $("#wemp_pmla").val();
        $.blockUI({ message: $('#msjEspere') });

        //se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
        var rango_superior = 245;
        var rango_inferior = 11;
        var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

        //Realiza el llamado ajax con los parametros de busqueda
        $.get('empresasXauditores.php', { wemp_pmla: wemp_pmla, action: "ordenarEntidades", parametro: opcion, consultaAjax: aleatorio, wuser: $("#wuser").val()} ,
        function(data) {
            $.unblockUI();
            if( data == 'NO' ){
                alerta("Error al guardar la nueva agrupacion");
            }else{
                $("#div_tabla_entidades").html("");
                $("#div_tabla_entidades").html(data);
                $(document).ready(function () {
                   $("input#input_buscador").quicksearch("table#entidades tbody tr.entidadesEncontradas");
                   try{$(".entidadAjena").tooltip("destroy");}catch(e){ console.log("excepcion") }
                   $(".entidadAjena[title!='']").tooltip({track: false, delay: 0, showURL: false, opacity: 0.95, left: -50, duration: 1500 });
                });
                /*if( $("#lista_agrupaciones").val() == '' )
                    $("#entidades input").attr("disabled",true);*/
                //Todos los que esten seleccionados en la tabla elegidos se deben recargar en la tabla entidades
                $("#tabla_seleccionadas .entidad").each(function(){
                    var empresa = $(this).parent().attr("empresa");
                    var valor = $(this).val();
                    $("#div_tabla_entidades [empresa="+empresa+"]").addClass('fondoAmarillo');
                    $("#div_tabla_entidades input[value="+valor+"]").attr("checked", true);
                });
            }
        });
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(typeof haystack[i] == 'object') {
                if(arrayCompare(haystack[i], needle)) return true;
            } else {
                if(haystack[i] == needle) return true;
            }
        }
        return false;
    }

    function isJson(value) {
        try {
            eval('(' + value + ')');
            return true;
        } catch (ex) {
            return false;
        }
    }

    function alerta( txt ){
        $("#textoAlerta").text( txt );
        $.blockUI({ message: $('#msjAlerta') });
            setTimeout( function(){
                            $.unblockUI();
                        }, 1600 );
    }

    function mostrarOcultarElemento( id ){
        $("#"+id).toggle();
    }

    function seleccionarEntidadesIniciales(){
        var entidadesUsuario = $("#entidades_usuario").val();
        entidadesUsuario     = entidadesUsuario.split(",");
        for( var i = 0; i < entidadesUsuario.length; i++ ){
            $("input[type='checkbox'].entidad[value='"+entidadesUsuario[i]+"']").trigger("click");
        }
    }
</script>
</head>

<?php

}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//

/*


include_once("root/comun.php");
include_once("movhos/movhos.inc.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenimp");
$wmovhosbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcliamebasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));*/

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
    $action = $_REQUEST['action'];
    if( $action == "mostrarListaEntidades"){
        consultarEntidadesAuditor( $codigoAuditor );
    }else if( $action == "guardarEntidades"){
        guardarEntidades( $_REQUEST['wcodaud'], $_REQUEST['entidades'] );
    }else if( $action == 'ordenarEntidades' ){
        mostrarTablaEntidades( $wuser, $_REQUEST['parametro'] );
    }else if( $action == 'guardarEstadoAgrupacion' ){
        cambiarEstadoAgrupacion( $_REQUEST['grupo'], $_REQUEST['estado'] );
    }
    return;
}
//FIN*LLAMADOS*AJAX******************************************************************
    function guardarEntidades($wcodaud, $wentidades){
        global $conex;
        global $wbasedato;
        global $wmovhosbasedato;
        global $wemp_pmla;


        $actualizados    = 0;
        $arregloEmpresas = explode( ",", $wentidades );
        if( count($arregloEmpresas) > 0 ){
            $query = "DELETE
                        FROM {$wmovhosbasedato}_000167
                       WHERE Eaucau = '{$wcodaud}'";
            $rs    = mysql_query( $query, $conex );
        }
        foreach ($arregloEmpresas as $i => $codigo ){
            $query = " INSERT INTO {$wmovhosbasedato}_000167 (`Medico`, `Fecha_data`, `Hora_data`, `Eaucau`, `Eauemp`, `Eauest`, `Seguridad`)
                                                      VALUES ( '{$wmovhosbasedato}', '".date('Y-m-d')."', '".date("H:i:s")."', '{$wcodaud}', '{$codigo}', 'on', 'C-{$wcodaud}')";
            $rs    = mysql_query( $query, $conex );
            if( mysql_insert_id() > 0 ){
                $actualizados++;
            }
        }
        if( $actualizados > 0 ){
            echo "OK";
        }else{
            echo "No se actualizo";
        }
    }

    function guardarAgrupacion($wdescripcion){

        global $conex;
        global $wbasedato;
        global $wmovhosbasedato;
        global $wemp_pmla;
        global $wusuario;

        $wcod_grupo = "1";
        //Busco que codigo sigue en las agrupaciones
        $q = " SELECT Empcod as codigo "
             ."  FROM ".$wbasedato."_000008 "
             ." ORDER BY Empcod DESC LIMIT 1";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0){
            $row = mysql_fetch_array($res);
            $wcod_grupo = $row[0]+1;
        }
        while ( strlen($wcod_grupo) < 3 ){
            $wcod_grupo = "0".$wcod_grupo;
        }

        $q = "    INSERT INTO ".$wbasedato."_000008
                                  (Medico, Fecha_data, Hora_data, Empcod, Empdes, Empemp, Empest, Seguridad)
                           VALUES
                                  ('".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$wcod_grupo."', '".$wdescripcion."', '', 'on',  'C-".$wusuario."')";
        $resl2 = mysql_query($q, $conex);

        $guardo = mysql_insert_id();

        if( $guardo ){
            echo $wcod_grupo;
        }else{
            echo "NO";
        }
    }

    function mostrarTablaEntidades($wuser, $wparametro='descripcion'){

        global $conex;
        global $wbasedato;
        global $wmovhosbasedato;
        global $wcliamebasedato;
        global $wemp_pmla;
        global $action;
        global $auditores;
        global $usuarioAuditor;

        $q= " SELECT Empcod as codigo, Empnit as nit, Empnom as descripcion, a.id as empresa, Eaucau codigoAuditor "
            ."  FROM ".$wcliamebasedato."_000024 a "
            ."  LEFT JOIN "
            ."       ".$wmovhosbasedato."_000167 on Eauemp = Empcod"
            ." WHERE Empest = 'on'"
            ." GROUP BY 1, 2, 3, 4"
            ." ORDER BY ".$wparametro." ";

        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);


        $ind = 1;

        $tablaEntidades  = "<div id='div_tabla_entidades'>";

        $tablaEntidades .= "<table id='entidades' align='center'>";
        $tablaEntidades .= "<tr><td colspan=7 align='left'>";
        $tablaEntidades .= "Ordernar por:";
        //El select con las opciones por las que se puede ordenar la tabla, IMPORTANTE: las opciones tienen como value el alias para la consulta
        $tablaEntidades .= "<select id='select_filtrar' onchange='recargarEntidades()'>";
        if( $wparametro == 'descripcion' ){
            $tablaEntidades .= "<option value='descripcion' selected>Descripci&oacute;n</option>";
            $tablaEntidades .= "<option value='codigo'>C&oacute;digo</option>";
            $tablaEntidades .= "<option value='nit'>NIT</option>";
        }else if( $wparametro == 'codigo' ){
            $tablaEntidades .= "<option value='codigo' selected>C&oacute;digo</option>";
            $tablaEntidades .= "<option value='descripcion'>Descripci&oacute;n</option>";
            $tablaEntidades .= "<option value='nit'>NIT</option>";
        }else{
            $tablaEntidades .= "<option value='nit' selected>NIT</option>";
            $tablaEntidades .= "<option value='codigo'>C&oacute;digo</option>";
            $tablaEntidades .= "<option value='descripcion'>Descripci&oacute;n</option>";
        }
        $tablaEntidades .= "</select>";
        $tablaEntidades .= "</td><td align='right'>";
        $tablaEntidades .= "<input type='text' id='input_buscador' placeholder='Buscar'><img width='25px' height='20px' title='Buscar' src='../../images/medical/root/lupa.png'>";
        $tablaEntidades .= "</td></tr>";
        $tablaEntidades .= "<tr class='encabezadotabla'><td colspan=8 align='center'><font size=5><b>LISTA DE ENTIDADES</b></font></td></tr>";
        $tablaEntidades .= "<tr class='fila2' style='font-weight:bold;'><td align='center'>Sel.</td><td align='center'>Codigo</td><td align='center'>Nit</td><td align='center'>Entidad</td><td align='center'>Sel.</td><td align='center'>Codigo</td><td align='center'>Nit</td><td align='center'>Entidad</td></tr>";

        if ($num > 0){
            $cierratr = false;

            while( $row = mysql_fetch_assoc($res) ){
                    $disabled = "";
                    $checked  = "";
                    $title    = "";
                    if( $row['codigoAuditor'] != $wuser ){
                        if( trim( $row['codigoAuditor'] ) != "" ){
                            $disabled      = "disabled";
                            $nombreAuditor = $auditores[$row['codigoAuditor']]['nombre'];
                            $title         = '<div class="fila2"><span class="subtituloPagina2"><font size="2" >Auditor Responsable:</font><font size="1" >'.$nombreAuditor.'</font></span></div>';
                            $checked = "checked";
                        }
                    }else{
                        ( !isset( $entidadesUsuario ) ) ? $entidadesUsuario = $row['codigo'] : $entidadesUsuario .= ",".$row['codigo'];
                    }
                    if( $ind == 1 ){
                        $tablaEntidades .= "<tr class='fila1 entidadesEncontradas'>";
                        $cierratr = false;
                    }
                    ( $usuarioAuditor == "on" ) ? $habilitadoUsuario = "" : $habilitadoUsuario = " disabled ";
                    $tablaEntidades .= "<td title='$title' class='entidadAjena' empresa='".$row['empresa']."'><input type='checkbox' class='entidad' $checked $habilitadoUsuario {$disabled} onclick='elegir_entidad(this)' value='".trim($row['codigo'])."' /></td><td class='parabuscar' empresa='".$row['empresa']."'>".$row['codigo']."</td><td class='parabuscar' empresa='".$row['empresa']."'>".$row['nit']."</td><td class='parabuscar' empresa='".$row['empresa']."'>".$row['descripcion']."</td>";
                    if( $ind  == 2 ){
                        $tablaEntidades .= "</tr>";
                        $ind = 0;
                    }
                    $ind++;
            }
        }
        // $tablaEntidades .= "<tr class='fila2'><td colspan=8 align='center'><input type='button' value='Nuevo' onclick='nueva_agrupacion()' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Guardar' onclick='guardarEntidades()'/></td></tr>";
        $tablaEntidades .= "</table>";
        $tablaEntidades .= "<input type='hidden' id='entidades_usuario' value='{$entidadesUsuario}'>";
        $tablaEntidades .= "<br><br>";

        $tablaEntidades .= "<center>";
       // $tablaEntidades .= "<input type='button' value='Nuevo' onclick='nueva_agrupacion()' />";
        $tablaEntidades .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $tablaEntidades .= "<input type='button' value='Guardar' onclick='guardarEntidades()'/>";
        $tablaEntidades .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $tablaEntidades .= "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
        $tablaEntidades .= "</center>";

        $tablaEntidades .= "</div>";
        if( isset( $action ) and trim( $action ) != "" ){
            echo $tablaEntidades;
        }else{
            return( $tablaEntidades );
        }
    }

    function consultarEntidadesAuditor( $codigoAuditor ){
        global $conex;
        global $wbasedato, $wmovhosbasedato;
        global $wemp_pmla;

        $arrayAuxiliar = array();

        $q = " SELECT Eauemp as empresa"
            ."   FROM ".$wmovhosbasedato."_000167 "
            ."  WHERE Eaucau = '".$codigoAuditor."'
                  AND Eauest = 'on'";

        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        while( $row = mysql_fetch_array( $res ) ){
            array_push( $arrayAuxiliar, $row['empresa'] );
        }
        $respuesta = implode( ",", $arrayAuxiliar );
        echo $respuesta;
    }

    function datosAuditores( $rolAuditor ){
        global $wemp_pmla, $conex, $hce;
        $rolAux = explode(",",$rolAuditor);
        $arrAux = array();
        foreach ( $rolAux as $i => $value ) {
            array_push( $arrAux, "'".$rolAux[$i]."'" );
        }
        $rolAuditor = implode( ",", $arrAux );
        $query = " SELECT descripcion, codigo, usurol, roldes
                     FROM {$hce}_000020, {$hce}_000019, usuarios
                    WHERE usurol IN ({$rolAuditor})
                      AND usuest = 'on'
                      AND rolcod = usurol
                      AND codigo = usucod
                      AND empresa = '{$wemp_pmla}'
                      AND Activo = 'A'";
        $rs    = mysql_query( $query, $conex );
        while( $row  = mysql_fetch_array( $rs ) ){
            $datos[$row['codigo']]['nombre']      = $row['descripcion'];
            $datos[$row['codigo']]['rol']         = $row['usurol'];
            $datos[$row['codigo']]['descripcion'] = $row['roldes'];
        }
        return( $datos );
    }

    //Funcion que imprime el formulario cuando se carga la pagina
    function vistaInicial( $auditores, $wuser ){

        global $wemp_pmla;
        global $wactualiz;
        global $usuarioAuditor;

        $width_sel = " width: 95%; ";
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/MSIE/i',$u_agent))
            $width_sel = "";

        echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
        echo "<input type='hidden' id ='wuser' value='".$wuser."'/>";

        encabezado("EMPRESAS X AUDITOR CORPORATIVO", $wactualiz, "clinica");


        echo '<div style="width: 100%">';
        echo "<div align='left'>";

            echo "<div align='left' style='padding-left:50px;'>";
                echo "<span class='subtituloPagina2' style='cursor:pointer;'  onClick='mostrarOcultarElemento( \"tbl_auditores\");'><font size='1' >Ver entidades asociadas a otros auditores</font></span>";
                        echo "<table id='tbl_auditores' style='display:none;'>";
                        echo "<tr>";
                        echo '<td class="encabezadotabla" width="80px">Auditor:</td>';
                        //LISTA DE ENTIDADES
                        echo '<td class="fila1" width="auto">';
                        echo "<div align='center'>";
                        echo "<select id='lista_auditores' class='ui-corner-all' align='center' style='".$width_sel." margin:5px;'>";
                        echo "<option value=''></option>";
                        foreach($auditores as $codigoAuditor =>$datos){
                            if( $codigoAuditor != $wuser ){
                                echo "<option value='{$codigoAuditor}'>{$datos['nombre']}</option>";
                            }
                        }
                        echo '</select>';
                        echo '</div>';
                        echo "</td>";
                        echo "</tr>";
                        echo "</table>";
            echo "</div>";
        echo "</div>";
        //------------TABLA DE PARAMETROS-------------
        echo "<br>";
        if( $usuarioAuditor == "on" )
            echo "<div align='center'> <span class='subtituloPagina2'><font size='2' id='auditor_actual'> AUDITOR: {$auditores[$wuser]['nombre']}</font></span> </div>";
        echo "<center><div id='contenedor_estadoActual' style='display:none;'>";
        echo "<table id='tabla_seleccionadas'>";
        echo "<tr class='noborrar'><td align='center' colspan='4'><br><font size='2'><b>ENTIDADES ASOCIADAS AL AUDITOR</b></font></td></tr>";
        echo '<tr style="font-weight:bold;" class="fila2 noborrar">';
        echo '<td align="center">Sel.</td><td align="center">Codigo</td><td align="center">Nit</td><td align="center">Entidad</td>';
        echo "</tr>";
        echo "</table>";
        echo "<br><br>";
        echo "<center>";
       //echo "<input type='button' value='Nueva Agrupaci&oacute;n' onclick='nueva_agrupacion()' />";
       echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
       echo "<input type='button' value='Guardar' onclick='guardarEntidades()'/>";
       echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
       echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
       echo "</center>";
       echo "<br><br>";
        echo "</div></center>";

        $tablaEmpresas = mostrarTablaEntidades($wuser);
        echo $tablaEmpresas;


        //------FIN FORMULARIO------
        echo "</div>";//Gran contenedor
        echo '<center>';
        echo "<br><br>";
        echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
        echo "<br><br>";

        echo "<br><br>";
        echo "<br><br>";
        //Mensaje de espera
        echo "<div id='msjEspere' style='display:none;'>";
        echo '<br>';
        echo "<img src='../../images/medical/ajax-loader5.gif'/>";
        echo "<br><br> Por favor espere un momento ... <br><br>";
        echo '</div>';
        //Mensaje de alertas
        echo "<div id='msjAlerta' style='display:none;'>";
        echo '<br>';
        echo "<img src='../../images/medical/root/Advertencia.png'/>";
        echo "<br><br><div id='textoAlerta'></div><br><br>";
        echo '</div>';
        echo '</center>';
    }

?>
 <body>
        <!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
            <?php
                    vistaInicial( $auditores, $wuser );
            ?>
        <div id='dialogo_datosAuditores' align='center' style='display:none'></div>
    </body>
</html>