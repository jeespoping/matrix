<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        DILIGENCIAR PREANESTESIA
//=========================================================================================================================================\\
//DESCRIPCION:          Sofware que permite a un usuario diligenciar la preadnestesía de un paciente sin ingreso real lo cual implica que no tiene asignada una historia o ingreso.
//AUTOR:                Camilo Zapata
//FECHA DE CREACION:    2017-04-28
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
            $wactualiz='2017-04-28';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------
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


$wactualiz      = "2017-04-24";
$conex          = obtenerConexionBD("matrix");
$wbasedato      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbdtcx         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
$wbdhce         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wfecha         = date("Y-m-d");
$whora          = date("H:i:s");
$user_session   = explode('-',$_SESSION['user']);
$wuse           = $user_session[1];


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
               AND Detapl = 'consecutivoHistoriaTemporalTriage'
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
                     WHERE Detapl = 'consecutivoHistoriaTemporalTriage'
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
            $respuesta = array("Error" => false, "Mensaje" => "");
            // --> Si ya existe relacion
            $sqlExiste = "
            SELECT id
              FROM ".$wbasedato."_000204
             WHERE Ahttur = '".$turno."'
               AND Ahtori = 'preanestesia'
            ";
            $resExiste = mysql_query($sqlExiste, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlExiste):</b><br>".mysql_error());
            if($rowExiste = mysql_fetch_array($resExiste))
            {
                // --> Actualizar relacion
                $guardarRel = "
                  UPDATE ".$wbasedato."_000204
                   SET Medico = '".$wbasedato."',
                   Fecha_data = '".date('Y-m-d')."',
                    Hora_data = '".date("H:i:s")."',
                       Ahtdoc = '".$documento."',
                       Ahttdo = '".$tipoDoc."',
                       Ahthte = '".$numHistoriaTemp."',
                       Ahttur = '".$turno."',
                       Ahtest = 'on',
                    Seguridad = 'C-".$wuse."'
                     WHERE id = '".$rowExiste['id']."'
                ";
                mysql_query($guardarRel, $conex) or die("<b>ERROR EN QUERY MATRIX(guardarRel):</b><br>".mysql_error());
            }
            else
            {
                // --> Guardar relacion
                $guardarRel = "
                  INSERT INTO ".$wbasedato."_000204
                   SET Medico = '".$wbasedato."',
                   Fecha_data = '".date('Y-m-d')."',
                    Hora_data = '".date("H:i:s")."',
                       Ahtdoc = '".$documento."',
                       Ahttdo = '".$tipoDoc."',
                       Ahthte = '".$numHistoriaTemp."',
                       Ahttur = '".$turno."',
                       Ahtest = 'on',
                       Ahtori = 'preanestesia'
                    Seguridad = 'C-".$wuse."'
                ";
                mysql_query($guardarRel, $conex) or die("<b>ERROR EN QUERY MATRIX(guardarRel):</b><br>".mysql_error());
            }
            echo json_encode($respuesta);
            return;
        }

        case 'buscarNombrePaciente':
        {
            $query = " SELECT turnom
                         FROM {$wbdtcx}_000011
                        WHERE turtdo = '{$tipoDoc}'
                          AND turdoc = '{$documento}'
                          AND turhis = '0'
                          AND turnin = '0'";
            $rs   = mysql_query( $query, $conex );
            $num  = mysql_num_rows( $rs );
            if( $num == 0 ){
                echo "no Encontrado";
            }else{
                $row = mysql_fetch_array($rs);
                echo $row['turnom'];
            }
            return;
        }

        case 'consultarPreAnestesiasRealizadas';
        {
            $fecha = ( isset( $fechaConsultada ) and $fechaConsultada != "" ) ? $fechaConsultada : date("Y-m-d");

            $query = " SELECT ahtdoc, ahttdo, ahthte, Turnom, b.Turmed, b.Turfec, b.Turhin, ahttur
                         FROM {$wbasedato}_000204 a, {$wbdtcx}_000011 b
                        WHERE a.fecha_data = '{$fecha}'
                          AND ahttur     = concat( 'cir_', turtur )
                          AND turtdo     = ahttdo
                          AND turdoc     = ahtdoc
                          AND ahtori     = 'preanestesia'";
            $rs    = mysql_query( $query, $conex ) or die( mysql_error());

            if( mysql_num_rows( $rs ) > 0 ){
                echo "
                    <table style='width:80%;' border='1'>
                        <thead>
                            <tr class='encabezadotabla'>
                                <th align='center' width='10%'>Tipo<br>Documento</th>
                                <th align='center' width='15%'>Documento</th>
                                <th align='center' width='30%'>Nombre</th>
                                <th align='center' width='8%'>Fecha Cirugia</th>
                                <th align='center' width='8%'>Hora Inicio</th>
                                <th align='center' width='30%'>Médico cirugia.</th>
                                <th align='center' width='30%'>Anular</th>
                            </tr>
                        </thead>
                        <tbody>";

                    while( $row = mysql_fetch_array($rs) ){

                        echo "
                            <tr tipo='tr_base_nuevo' class='fila1'>
                                <td align='center'>{$row['ahtdoc']}</td>
                                <td align='center'>{$row['ahttdo']}</td>
                                <td align='left'>{$row['Turnom']}</td>
                                <td align='center'>{$row['Turfec']}</td>
                                <td align='center'>{$row['Turhin']}</td>
                                <td align='left'>{$row['Turmed']}</td>
                                <td align='center'><input type='radio' name='rd_anular' onclick='anularPreAnestesia( \"{$row['ahttdo']}\", \"{$row['ahtdoc']}\", \"{$row['ahttur']}\", \"{$row['ahthte']}\" )'></td>
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
            padding-right:    5%;
            padding-left:     5%;
            padding-bottom:   5%;
            padding-top:      2%;
            border-radius:    0.4em;
            border-style:     solid;
            border-width:     2px;
            width:            60%;
            max-height: 200px;
        }
        .tbl_prea_realizadas{
            max-height: 200px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
        }
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
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
                    consultarPreAnestesiasRealizadas();
                }
            });

            parpadear();
            ajustarResolucioPantalla();
            consultarPreAnestesiasRealizadas();

        });

        //-------------------------------------------------------------------
        //  --> Reiniciar pantalla para permitir ingresar un nuevo turno
        //-------------------------------------------------------------------
        function reiniciarPantalla(){
            // --> Limpiar campos
            $("#numDocumento").val("");
            $("#inputNombrePaciente").val("");
            $("#edadPaciente").val("");
            $("[name=tipDocumento]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
            $("[name=categoriaEmp]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
            $("[name=tipoEdad]:checked").removeAttr("checked").next().attr("class", "ui-button ui-widget ui-state-default ui-button-text-only");
            $("#divMensaje").html("&nbsp;");
            $("#textoLector").val("");
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
        function abrirPreAnestesia(){

            if($("[name=tipDocumento]:checked").val() == undefined){
                $("#divMensaje").html("Debe seleccionar el tipo de documento.");
                return;
            }

            // --> Si no han ingresado el numero de documento
            if($("#numDocumento").val() == ""){
                $("#divMensaje").html("Debe ingresar el numero de documento.");
                return;
            }

            // --> Si no han ingresado el nombre
            if($.trim($("#inputNombrePaciente").val()) == ""){
                $("#divMensaje").html("Debe ingresar el nombre.");
                return;
            }

            $("#divMensaje").html("");
            tipoDocumento = $("[name=tipDocumento]:checked").val();
            numDocumento  = $("#numDocumento").val();
            nombrePaciente = $("#inputNombrePaciente").val();
            //----------------------------------------------------------------------
            // --> Funcion que abre el formulario hce para realizar el triage
            //----------------------------------------------------------------------
            //function realizarTriage(turno, tipoDoc, documento, nombre, edad, categoria)
                // --> Obtener el consecutivo de historia temporal y guardar log del inicio del triage
                $.post("preAnestesia.php",
                {
                    consultaAjax:           '',
                    accion:                 'obtenerHistoriaTemporal',
                    wemp_pmla:              $('#wemp_pmla').val(),
                    puestoTrabajo:          "preAnes"
                }, function(respuesta){

                    $("#divFormularioHce").dialog("destroy");

                    if(respuesta.Error)
                    {
                        jAlert("<span style='color:red'>Error obteniendo el número de historia temporal.<br>Por favor reporte la inconsistencia.</span>", "Mensaje");
                        return;
                    }

                    var formTipoOrden   = '000075';
                    var numHistoriaTemp = 'TEMP'+$.trim(respuesta.Historia);
                    var urlform         = '/matrix/hce/procesos/HCE.php?accion=M&ok=0&empresa=hce&origen='+$('#wemp_pmla').val()+'&wdbmhos=movhos&wformulario='+formTipoOrden+'&wcedula='+numDocumento+'&wtipodoc='+tipoDocumento+'&whis='+numHistoriaTemp+'&wing=1';

                    infoPaciente = ""
                    +"<fieldset align='center' style='padding:6px;'>"
                    +"<legend class='fieldset'>Información del paciente:</legend>"
                    +"<table width=100% id='infoPacEnTriage'>"
                        +"<tr>"
                            //+"<td class=fila1>Turno</td><td class=fila2>"+turnoTemp[1]+"</td>"
                            +"<td class=fila1>Documento</td>"
                            +"<td class=fila2>"+tipoDocumento+"-"+numDocumento+"</td>"
                            +"<td class=fila1>Paciente</td><td class=fila2>"+nombrePaciente+"</td>"
                        +"</tr>"
                    +"</table>"
                    +"</fieldset>";

                    // --> Cargar el iframe
                    $("#divFormularioHce").html("<div align=center>"+infoPaciente+"</div><iframe id='frameFormularioTriage' name='frameFormularioTriage' src='"+urlform+"' height='900px' width='950px' scrolling=yes frameborder='0'></iframe>");
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
                                if( selectAux != "Seleccione"  )
                                    seleccionTurCir ="cir_"+selectAux;
                            if(texto != "" && texto.search("DATOS GRABADOS OK") >= 0)
                            {
                                //--> en seleccionturcir se captura el turno de cirugia para el cual se está haciendo la preanestesia
                                if( !guardado ){
                                    guardarRelacionPacienteHistoriaTemp("preAnes", tipoDocumento, numDocumento, numHistoriaTemp, seleccionTurCir );
                                    guardado = true;
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
                                $(this).dialog("close");
                            }
                        }],
                        close: function( event, ui ) {
                            ///-->
                        }
                    });
                    $("#divFormularioHce").parent().css({"z-index":"999"}).next().css({"z-index":"998"});

                }, 'json');
        }

        //------------------------------------------------------------------------------
        // --> Guardar informacion de la relacion del paciente y la historia temporal
        //------------------------------------------------------------------------------
        function guardarRelacionPacienteHistoriaTemp(turno, tipoDoc, documento, numHistoriaTemp, turnoCirugia){
            $.post("preAnestesia.php",
            {
                consultaAjax:           '',
                accion:                 'guardarRelacionPacienteHistoriaTemp',
                wemp_pmla:              $('#wemp_pmla').val(),
                numHistoriaTemp:        numHistoriaTemp,
                documento:              documento,
                tipoDoc:                tipoDoc,
                turno:                  turnoCirugia
            }, function(respuesta){
                consultarPreAnestesiasRealizadas();
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
            }, function(respuesta){
                if( respuesta == "no Encontrado" ){
                    $("#inputNombrePaciente").val( "" );
                    alerta( "No existen cirugias programadas para este paciente" );
                    $("#botonAceptar").attr("disabled","disabled");
                    return;
                }else{
                    $("#botonAceptar").removeAttr("disabled");
                    $("#inputNombrePaciente").val( respuesta );
                }
            });
        }


        function alerta( txt ){
            $("#textoAlerta").text( txt );
            $.blockUI({ message: $('#msjAlerta') });
                setTimeout( function(){
                    $.unblockUI();
                }, 2500 );
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

        function consultarPreAnestesiasRealizadas(){

            $.post("preAnestesia.php",
            {
                consultaAjax:           '',
                accion:                 'consultarPreAnestesiasRealizadas',
                wemp_pmla:              $('#wemp_pmla').val(),
                fechaConsultada:        $("#fechaConsultada").val()
            }, function(data){
                $("#tbl_prea_realizadas").html(data);
            });
        }

        function anularPreAnestesia( tipoDocumento, documento, turnoCirugia, historiaTemporal ){

            if( confirm( "Desea anular la admisión?" ) ){
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
                consultarPreAnestesiasRealizadas();
            },
            "json"
            );
        }

        function cerrarModal(){
            $.unblockUI();
        }

    </script>
    <?php
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
        <input type='hidden' id='wemp_pmla'      value='<?=$wemp_pmla?>'>
        <input type='hidden' id='codigoTurnero'     value='<?=trim($codigoTurnero)?>'>
        <input type='hidden' id='wdiahoy'     value='<?=trim($fechaHoy)?>'>
        <input type='hidden' id='textoLector'       value='' numTabs='0'>


        <div id='accordionPrincipal' align='center' style='margin: auto auto; height:900px;'>
            <h1 style='font-size: 3rem;background:#75C3EB' align='center'>
                <img width='125' heigth='61' src='../../images/medical/root/logoClinicaGrande.png' >
                &nbsp;
                Realizaci&oacute;n de preanestesia
                &nbsp;
                <img width='120' heigth='100' src='../../images/medical/root/Logo_MatrixAzulClaro.png'>
            </h1>
            <div style='color:#000000;font-family: verdana;font-weight: normal;font-size: 2rem;' id='div_contenedor_2' align='center'>
                <table style='width:80%;margin-top:0px;margin-bottom:2px;font-family: verdana;font-weight: normal;font-size: 2rem;'>
                    <tr>
                        <td id='divMensaje' colspan='2' style='padding:2px;color:#F79391;' align='center'>&nbsp;</td>
                    </tr>
                    <tr align='left'>
                        <td colspan='2'>TIPO DE DOCUMENTO:</td>
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
                        <td><input id='numDocumento' type='text' tipo='obligatorio' onblur='buscarNombrePaciente( this );' style='margin-top:5px;background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:38rem;font-size: 2rem' placeholder='M&aacute;ximo 11 caractéres' ></td>
                    </tr>
                    <tr>
                        <td align='left'>NOMBRE:</td>
                        <td><input id='inputNombrePaciente' type='text' tipo='obligatorio'  style='margin-top:5px;background:#FCFCED;color:#000000;border-radius: 4px;border:1px solid #AFAFAF;width:38rem;font-size: 2rem'></td>
                    </tr>
                </table>
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
                            <button id='botonAceptar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 1rem;' onclick='abrirPreAnestesia()'>Aceptar</button>
                            <button id='botonLimpiar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 1rem;' onclick='reiniciarPantalla()'>Limpiar</button>
                            <button id='botonLimpiar' style='margin-top:1.3rem;color:#333333;font-family: verdana;font-size: 1rem;' onclick='salir()'>Salir</button>
                        </td>
                        <td width='30%' align='right'></td>
                    </tr>
                </table>

                <br><br>
                <center>
                <div id='div_listado' class='div_contenedor fila2'>
                    <div style='width:100%;left:100%;' align='left'><span  class='subtituloPagina2'><font size='3'>PREANESTESIAS REALIZADAS:&nbsp;</font></span><input type='text' id='fechaConsultada' value='<?=$fechaHoy?>'><br><br></div>
                    <center>
                        <div id='tbl_prea_realizadas' class='tbl_prea_realizadas'>
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
    </body>
</html>
