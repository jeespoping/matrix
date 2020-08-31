<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        DILIGENCIAR PREANESTESIA
//=========================================================================================================================================\\
//DESCRIPCION:          Sofware que permite a un usuario consultar los procedimientos realizados por medicos con especialidades oncològicas
//AUTOR:                Camilo Zapata
//FECHA DE CREACION:    2017-06-28
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//2017-06-07 camilo zapata: se controla el doble click para que no haya errores con los consecutivos de la 204 y el formulario
//2017-05-30 camilo zapata: la busqueda del consecutivo de historia temporal se cambia a un llamado asíncrono.
//2017-05-24 camilo zapata: modificacion del software con la opción de consulta de la preanestesia realizada.
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
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


$wactualiz    = "2017-06-22";
$conex        = obtenerConexionBD("matrix");
$wcliame      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$wmovhos      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wtcx         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
$whce         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wfecha       = date("Y-m-d");
$whora        = date("H:i:s");
$user_session = explode('-',$_SESSION['user']);
$wuse         = $user_session[1];
$caracteres   = array( "á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","\\","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??","?£", "°", "-");
$caracteres2  = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute","&ntilde;","&Ntilde;","u","U","","","a","e","i","o","u","A","E","I","O","U","A","S","", "", "N", "N", "U", "", "");

if( isset( $consultaAjax ) ){
    switch ( $consulta ) {
        case 'fechaMaximaConsulta':
            $hoy         = date( "Y-m-d" );
            $restarUnDia = false;
            $hoyseg      = strtotime( $hoy );
            $limite      = strtotime( $fecha_inicio."+6 month" );
            if( $limite > $hoyseg ){
                $limite = $hoyseg;
                //$restarUnDia = true;
            }else{
                $restarUnDia = true;
            }
            $fecha_final = date( "Y-m-d", $limite );
            if( $restarUnDia )
                $fecha_final = date( "Y-m-d", strtotime( $fecha_final."-1 day" ) );
            echo $fecha_final;
            break;
        case 'generarReporte':

            $array_especialista  = array();
            $wespecialista       = str_replace("\\", "", $wespecialista);
            $wespecialista       = json_decode( $wespecialista, true );
            $arr_x_especialista  = array();
            $arr_x_procedimiento = array();
            $aux_especialistas_cir = array();

            foreach( $wespecialista as $codEspecialista => $datosEspecialista ){

                array_push( $array_especialista, "'".$codEspecialista."'" );

            }
            $array_especialista = implode( ",", $array_especialista );
            $condicion_especialistas = " promed IN ( {$array_especialista} ) ";

            $query = " SELECT Prohis, Proing, Profec,  egp.Procod, Promed, Proesm, Proser, Egrest, concat( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) nombre, pronom, egp.id id
                         FROM {$wcliame}_000110 egp
                        INNER JOIN
                              {$wcliame}_000108 egr on ( profec BETWEEN '{$fecha_inicio}' AND '{$fecha_final}' AND proqui = 'S' AND {$condicion_especialistas} AND Egrhis = Prohis
                                                        AND Egring = Proing)
                        INNER JOIN
                              {$wcliame}_000100 pac on ( pachis = prohis )
                        INNER JOIN
                              {$wcliame}_000103 pro on ( pro.procod = egp.procod )
                        GROUP BY 1,2,3,4,5,6,7,8,9,10,11
                        ORDER BY 1,2,3";

            $rs    = mysql_query( $query, $conex );
            $i     = 0;
            $historiaAnterior = "";
            $historiaNueva    = "";
            while( $row = mysql_fetch_assoc( $rs ) ){

                if( !isset( $arr_x_especialista[$row['Promed']] ) ){
                    $arr_x_especialista[$row['Promed']]                   = array();
                    $arr_x_especialista[$row['Promed']]['cirugias']       = 0;
                    $aux_especialistas_cir[$row['Promed']]                = 0;
                    $arr_x_especialista[$row['Promed']]['diasEstancia']   = 0;
                    $arr_x_especialista[$row['Promed']]['complicaciones'] = 0;
                    $arr_x_especialista[$row['Promed']]['detalle']        = array();
                }

                $historiaNueva = $row['Prohis']."_".$row['Proing'];

                if( $historiaNueva != $historiaAnterior ){
                    $arr_x_especialista[$row['Promed']]['diasEstancia'] += $row['Egrest']*1;
                    $totalEstancia += $row['Egrest']*1;
                }
                $historiaAnterior = $historiaNueva;
                $arr_x_especialista[$row['Promed']]['cirugias']++;
                $aux_especialistas_cir[$row['Promed']]++;
                $totalCirugias++;

                if( !isset($arr_x_especialista[$row['Promed']]['detalle'][$row['Prohis']."_".$row['Proing']] ) ){
                    $arr_x_especialista[$row['Promed']]['detalle'][$row['Prohis']."_".$row['Proing']]                   = array();
                    $arr_x_especialista[$row['Promed']]['detalle'][$row['Prohis']."_".$row['Proing']]['historia']       = $row['Prohis'];
                    $arr_x_especialista[$row['Promed']]['detalle'][$row['Prohis']."_".$row['Proing']]['ingreso']        = $row['Proing'];
                    $arr_x_especialista[$row['Promed']]['detalle'][$row['Prohis']."_".$row['Proing']]['nomPaciente']    = $row['nombre'];
                    $arr_x_especialista[$row['Promed']]['detalle'][$row['Prohis']."_".$row['Proing']]['diasEstancia']   = $row['Egrest'];
                    $arr_x_especialista[$row['Promed']]['detalle'][$row['Prohis']."_".$row['Proing']]['procedimientos'] = array();
                }
                $aux['procedimiento'] = $row['pronom'];
                $aux['fechaProcedim'] = $row['Profec'];
                $aux['idproced'] = $row['id'];
                array_push( $arr_x_especialista[$row['Promed']]['detalle'][$row['Prohis']."_".$row['Proing']]['procedimientos'], $aux );

            }

            arsort( $aux_especialistas_cir );
            //echo "<pre>".print_r( $arr_x_especialista, true )."</pre>";
            $html  = "";
            if( count( $aux_especialistas_cir ) > 0 ){
                $html .= "<table width='80%'>";

                    $html .= "<tr>";
                        $html .= "<td align='left' colspan='4'  > <span class='subtituloPagina2'>CIRUGIAS REALIZADAS</span> </td>";
                    $html .= "</tr>";
                    $html .= "<tr tipo='encabezado' class='encabezadotabla'>";
                        $html .= "<td align='center'> Medico </td>";
                        $html .= "<td align='center'> Especialidad </td>";
                        $html .= "<td align='center'> Cirugias </td>";
                        $html .= "<td align='center'> Estancia </td>";
                    $html .= "</tr>";
                    //foreach ( $arr_x_especialista as $codigoEspecialista => $datos ) {
                    foreach ( $aux_especialistas_cir as $codigoEspecialista => $datosAux ) {
                        $datos = $arr_x_especialista[$codigoEspecialista];
                        $i++;
                        $wclass  = ( is_int($i/2) ) ? "fila1" : "fila2";
                        $wclass2 = ( is_int($i/2) ) ? "fila2" : "fila1";
                        $html .= "<tr tipo='reporte' class='{$wclass}' style='cursor:pointer;' onclick='abrirDetalle(this);' mostrandoDetalle='off'>";
                            $html .= "<td align='left'> {$wespecialista[$codigoEspecialista]['nombre']} </td>";
                            $especialidad = explode(",",$wespecialista[$codigoEspecialista]['especialidadNom']);
                            $especialidad = trim( $especialidad[1]);
                            $html .= "<td align='left'> {$especialidad} </td>";
                            $html .= "<td align='center'> {$datos['cirugias']} </td>";
                            $html .= "<td align='center'> {$datos['diasEstancia']} </td>";
                        $html .= "</tr>";

                        $html .= "<tr tipo='detalle' class='{$wclass}' style='display:none;' >";
                            $html .= "<td align='center' colspan='5'>";
                                $html .= "<div align='center' class='{$wclass}'><br>";
                                    $html .= "<table>";
                                        $html .= "<tr tipo='encabezado' class='encabezadotabla'>";
                                            $html .= "<td align='center'> Historia </td>";
                                            $html .= "<td align='center'> Ingreso </td>";
                                            $html .= "<td align='center'> Nombre </td>";
                                            $html .= "<td align='center'> Estancia(dias) </td>";
                                            $html .= "<td align='center'> Procedimiento </td>";
                                            $html .= "<td align='center' title='fecha procedimiento'> Fecha Proc. </td>";
                                        $html .= "</tr>";
                                        $idet = 0;
                                        foreach ($datos['detalle'] as $key => $datosDetalle ) {
                                            $idet++;
                                            $wclass3 = ( is_int($idet/2) ) ? "fila2" : "fila1";
                                            $procedimientos = count( $datosDetalle['procedimientos'] );
                                                $html .= "<tr class='{$wclass3}'>";
                                                    $html .= "<td align='center' rowspan='{$procedimientos}'> {$datosDetalle['historia']} </td>";
                                                    $html .= "<td align='center' rowspan='{$procedimientos}'> {$datosDetalle['ingreso']} </td>";
                                                    $html .= "<td align='left' rowspan='{$procedimientos}'> {$datosDetalle['nomPaciente']} </td>";
                                                    $html .= "<td align='center' rowspan='{$procedimientos}'> {$datosDetalle['diasEstancia']} </td>";
                                                    foreach ($datosDetalle['procedimientos'] as $key => $value) {
                                                            $html .= "<td align='left' > ->{$value['procedimiento']} </td>";
                                                            $html .= "<td align='center' > {$value['fechaProcedim']} </td>";
                                                        break;
                                                    }
                                                $html .= "</tr>";
                                            $iteracion = 0;
                                            foreach ($datosDetalle['procedimientos'] as $key => $value) {
                                                $iteracion++;
                                                if( $iteracion > 1 ){
                                                    $html .= "<tr class='{$wclass3}'>";
                                                        $html .= "<td align='left' > ->{$value['procedimiento']} </td>";
                                                        $html .= "<td align='center' > {$value['fechaProcedim']} </td>";
                                                    $html .= "</tr>";
                                                }
                                            }
                                        }
                                    $html .= "</table><br>";
                                $html .= "</div>";
                            $html .= "</td>";
                        $html .= "</tr>";
                    }
                    $html .= "<tr class='encabezadotabla'>";
                        $html .= "<td align='left' colspan='2'> TOTAL </td>";
                        $html .= "<td align='center'> {$totalCirugias} </td>";
                        $html .= "<td align='center'> {$totalEstancia} </td>";
                    $html .= "</tr>";
                $html .= "</table>";
            }else{
                $html .= "<center><br><span class='subtituloPagina2'> NO HAY CIRUGIAS REALIZADAS EN ESTE RANGO DE FECHAS </span><br></center>";
            }
            echo utf8_encode($html);
            break;
        default:
            # code;
            break;
    }
    return;
}

//-----------------------------------------> FUNCIONES <---------------------------------------------------------------------//

function inicializarArreglos(){

    global $array_especialidades;
    global $array_especialistas;
    global $conex;
    global $wmovhos;
    global $wemp_pmla;

    $caracteres    = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
    $caracteres2   = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");
    $array_aux     = array(); // auxiliar para buscar los especialistas

    $query = " SELECT espcod codigo, espnom nombre
                 FROM {$wmovhos}_000044
                WHERE espnom LIKE '%onco%'";

    $result = mysql_query( $query, $conex ) or die(mysql_error());
    while($row2 = mysql_fetch_array($result)){

         $row2['nombre'] = utf8_encode( $row2['nombre'] );
         $row2['nombre'] = str_replace( $caracteres, $caracteres2, $row2['nombre'] );
         array_push( $array_especialidades, trim($row2['codigo']).", ".trim($row2['nombre']) );
         array_push( $array_aux, "'{$row2['codigo']}'");
    }

    $condicion = implode( ",", $array_aux );

    $query = "SELECT Medtdo, Meddoc codigo, Meduma, CONCAT( Medno1, ' ', Medno2, ' ', Medap1, Medap2 ) nombre, Medesp
                FROM {$wmovhos}_000048 a
               INNER JOIN
                     {$wmovhos}_000044 b on ( a.Medesp = b.Espcod AND espcod IN ( $condicion ) )
               ORDER BY Medesp";
    $rs    = mysql_query( $query, $conex ) or die( mysql_error() );
    while($row2 = mysql_fetch_array($rs)){

         $row2['nombre'] = utf8_encode( $row2['nombre'] );
         $row2['nombre'] = str_replace( $caracteres, $caracteres2, $row2['nombre'] );
         $array_especialistas[$row2['codigo']]['especialidad'] = $row2['Medesp'];
         $array_especialistas[$row2['codigo']]['nombre']       = trim($row2['nombre']);
         //array_push( $array_especialistas, trim($row2['codigo']).", ".trim($row2['nombre']) );
         array_push( $array_aux, "'{$row2['codigo']}'");
    }

}

//---------------------------------------> FIN FUNCIONES <-------------------------------------------------------------------//
?>
<html>
    <head>
      <title>Reporte Procedimientos Oncol&oacute;gicos</title>
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
            /*border-style:     solid;
            border-width:     2px;*/
            width:            80%;
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
    <style>
        /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
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

        #tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
        #tooltip h3, #tooltip div{margin:0; width:auto}
        .amarilloSuave{
            background-color: #F7D358;
        }
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <script src='../../../include/root/toJson.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" /> <!-- Tooltip -->
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
    <script type="text/javascript" charset="utf-8" async defer>
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
    <script type="text/javascript" charset="utf-8">
        $(document).ready(function(){

            $("#fecha_inicio").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                buttonText: "",
                maxDate: "+0m +0w",
                onSelect: function(dateText, inst ) {
                    fechaMaximaConsulta =  consultarFechaMaxima( dateText );
                    $("#fecha_final").val(fechaMaximaConsulta);
                    $("#fecha_final").datepicker("destroy");
                    $("#fecha_final").datepicker({
                        showOn: "button",
                        buttonImage: "../../images/medical/root/calendar.gif",
                        dateFormat: 'yy-mm-dd',
                        buttonImageOnly: true,
                        changeMonth: true,
                        changeYear: true,
                        minDate: dateText,
                        maxDate: fechaMaximaConsulta,
                        buttonText: ""
                    });
                }
            });

        });

        function consultarFechaMaxima( fecha_inicio1 ){
            var fecha = '';
            $.ajax({
                url  : "rep_cirugias_por_especialidad.php",
                type : "post",
                async: false,
                 data:
                {
                    consultaAjax  : "on",
                    consulta      : "fechaMaximaConsulta",
                    wemp_pmla     : $("#wemp_pmla").val(),
                    fecha_inicio  : fecha_inicio1
                },
                success : function(data){
                    if(data != "")
                    {
                        fecha = data;
                    }else{
                    }
                }
            });
            return( fecha );
        }

        function generarReporte(){

            var especialidad  = $("#wespecialidad > option:selected ").val();
            var especialistas = {};

            if( $("#wespecialista > option:selected").val() == "%" ){//--> si se están buscando todos los especialistas se crea un arreglo con todos ellos;

                $("#wespecialista > option[value!='%']:visible").each(function(){
                    var codigoEspecialista = $(this).val();
                    especialistas[codigoEspecialista] = {};
                    especialistas[codigoEspecialista]['codigo'] = $(this).val();
                    especialistas[codigoEspecialista]['nombre'] = $(this).html();
                    especialistas[codigoEspecialista]['especialidadCod'] = $(this).attr("especialidad");
                    especialistas[codigoEspecialista]['especialidadNom'] = $("#wespecialidad > option[value='"+$(this).attr("especialidad")+"']").html();
                });

            }else{

                var selected = $("#wespecialista > option:selected");
                var codigoEspecialista = $(selected).val();
                especialistas[codigoEspecialista] = {};
                especialistas[codigoEspecialista]['codigo'] = $(selected).val();
                especialistas[codigoEspecialista]['nombre'] = $(selected).html();
            }

            var especialistasJson = $.toJSON( especialistas );

            $("#div_respuesta").hide();
            $("#msjEspereSolicitud").show();
            $.ajax({
                url  : "rep_cirugias_por_especialidad.php",
                type : "post",
                async: false,
                 data:
                {
                    consultaAjax : "on",
                    consulta     : "generarReporte",
                    wemp_pmla    : $("#wemp_pmla").val(),
                    wespecialidad: especialidad,
                    wespecialista: especialistasJson,
                    fecha_inicio : $("#fecha_inicio").val(),
                    fecha_final  : $("#fecha_final").val()
                },
                success : function(data){
                    if(data != "")
                    {
                        $("#div_respuesta").html(data);
                        $("#div_respuesta").show();
                        /*$("td[tipo='porcentaje']").each(function(){
                          value = $(this).prev("td").prev("td").html()*1;
                          total = $(this).parent().parent().find("td[tipo='acumulado']").html()*1;
                          valor = (value/total) * 100;
                          //$(this).html(Math.round(valor));
                          $(this).html(valor.toFixed(2));
                        });*/
                        /*$("#div_respuesta").show();
                        $(".div_accordion").accordion({
                            collapsible: true,
                            active:0,
                            heightStyle: "content",
                            icons: null
                        });*/
                        $("#msjEspereSolicitud").hide();
                    }else{
                    }
                }
            });
        }

        function filtrarEspecialistas( objeto ){

            var especialidad = $(objeto).val();
            if( especialidad ==  "%"){
                $("#wespecialista > option[especialidad!='"+especialidad+"'] ").show();
            }else{
                $("#wespecialista > option[especialidad!='"+especialidad+"']").hide();
                $("#wespecialista > option[especialidad='"+especialidad+"']").show();
            }
        }

        function abrirDetalle( tr_padre ){

            if( $(tr_padre).attr("mostrandoDetalle") == "off" ){

                $(tr_padre).addClass('fondoAmarillo');
                $(tr_padre).attr('mostrandoDetalle','on');
                $(tr_padre).next("tr[tipo='detalle']").show();
            }else{

                $(tr_padre).removeClass('fondoAmarillo');
                $(tr_padre).attr('mostrandoDetalle','off');
                $(tr_padre).next("tr[tipo='detalle']").hide();

            }

        }
    </script>

    <body>
    <?php
        $array_especialidades = array();
        $array_especialistas  = array();
        inicializarArreglos();
        //$centrosCostos = json_encode( $centrosCostos );
        $ccoInicial    = "%";
    ?>
    <?php encabezado( " REPORTE PROCEDIMIENTOS ONCOL&Oacute;GICOS", $wactualiz, "clinica" ); ?>
    <br><br><br>

    <input type='hidden' id='wemp_pmla'       value='<?=$wemp_pmla;?>'>
    <input type='hidden' id='fecha_hoy'       value='<?=date("Y-m-d");?>'>
    <input type='hidden' name='array_ccos' id='array_ccos' value='<?=$centrosCostos?>'>

    <div id='div_formulario' align='center' class='div_formulario'>
        <span class="subtituloPagina2">Par&aacute;metros de consulta</span><br><br>
        <table class='tabla_formulario'>

            <tr>
                <td class='subEncabezado fila1'> ESPECIALIDAD: </td>
                <!--<td class='fila2'> <input type='text' id='input_cco' keyup='validarVacio( this );' size='30' > <input type='hidden' id='wcco' name='wcco' value=''></td>-->
                <td class='fila2'>
                    <SELECT id='wespecialidad'>
                        <option value='%' selected onclick='filtrarEspecialistas( this );' > %-TODOS </option>
                        <?php
                            foreach( $array_especialidades as $i => $datosEspecialidad ){
                                $codigo = explode(",", $datosEspecialidad );
                                $codigo = trim( $codigo[0] );
                                echo "<option value='{$codigo}' onclick='filtrarEspecialistas( this );'> $datosEspecialidad </option>";
                            }
                        ?>
                    </SELECT>
                </td>
            </tr>

            <tr>
                <td class='subEncabezado fila1'> MEDICO: </td>
                <!--<td class='fila2'> <input type='text' id='input_cco' keyup='validarVacio( this );' size='30' > <input type='hidden' id='wcco' name='wcco' value=''></td>-->
                <td class='fila2'>
                    <SELECT id='wespecialista'>
                        <option value='%' selected  > %-TODOS </option>
                        <?php
                            foreach( $array_especialistas as $codigo => $datosEspecialista ){
                                /*$codigo = explode(",", $datosEspecialista );
                                $codigo = trim( $codigo[0] );*/
                                echo "<option value='{$codigo}' especialidad='{$datosEspecialista['especialidad']}' > {$datosEspecialista['nombre']} </option>";
                            }
                        ?>
                    </SELECT>
                </td>
            </tr>
            <tr>
                <td class='subEncabezado fila1'> PERIODO: </td><td class='fila2'><input id='fecha_inicio' disabled size='12' type='text' value='<?=date("Y-m-d");?>'> Hasta <input id='fecha_final' size='12' disabled type='text' value='<?=date("Y-m-d");?>'></td>
            </tr>
        </table>
        <br>
        <input type="button" onclick="generarReporte();" value="BUSCAR" class="botona" id="btn_consultar">
    </div><br>
    <center><div id='div_respuesta' style='with:80%;' align='center'>
    </div></center><br>
    <center><input type="button" value='Cerrar Ventana' onclick='cerrarVentana()'></center>
    <div id='contenedor_auxiliar' style='display:none;'></div>
    <div id='contenedor_graficador' style='display:none;'></div>
    <div id='msjEspereSolicitud' align='center' style='display:none;'>
        <br /><br />
          <img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Consultando la información (Espere un momento por favor, la operación puede tardar)...</font>
        <br /><br /><br />
    </div>
    </body>
</html>