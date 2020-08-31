<?php
//=========================================================================================================================================\\
//        REPORTE POR RANGOS DE DIAGNOSTICOS
//=========================================================================================================================================\\
//DESCRIPCION:          Permite seleccionar pacientes que esten en  rangos de diagnosticos
//                      Es decir si un paciente ingreso a la clinica con un diagnostico que esta en el rango de busqueda 
//                       debe aparecer en el reporte.
//AUTOR:                Freddy Saenz , basado en el script del reporte de grupos etareos de Camilo Zapata
//FECHA DE CREACION:    2019-02-25
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES

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

include("conex.php");
include("root/comun.php");
mysql_select_db("matrix");
$wactualiz    = "2019-02-27"; 
$conex        = obtenerConexionBD("matrix");
$wcliame      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$wmovhos      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wtcx         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
$whce         = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$ccoUci       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoUCI');
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
            $limite      = strtotime( $fecha_inicio."+1 month" );
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

            $array_diagnosticos     = array();
            $array_diagnosticos2    = array();
            $wdiagnosticos          = str_replace("\\", "", $wdiagnosticos);
            $wdiagnosticos          = json_decode( $wdiagnosticos, true );
            //$arrayCcos              = crearArreglosClasificatorios( $condicion, $condicionCCo );
            $clasificacionReal      = $arrayCcos['clasificacionReal'];
            $condicionCcoConsultado = $arrayCcos['condicionCcoConsultado'];
            $arrayCcos              = $arrayCcos['arrayCcosMostrados'];
           // $gruposEtareos          = obtenerArreglosEdades( $tipo_consultado, $arrayCcos );
            $gruposEtareosEnc       = $gruposEtareos;
            $categorias_diagnos     = array();

            foreach( $wdiagnosticos as $i => $codigoDiagnostico ){
                array_push( $array_diagnosticos2, "'".$codigoDiagnostico."'" );
            }
            $condicion_diagnosticos2 = implode( ",", $array_diagnosticos2 );
			
			$incluirTodosDx = "no";
			
            if( $incluirTodosDx == "no" ){

                foreach( $wdiagnosticos as $i => $codigoDiagnostico ){
                    array_push( $array_diagnosticos, "'".$codigoDiagnostico."'" );
                }

                $array_diagnosticos     = implode( ",", $array_diagnosticos );
 //               $condicion_diagnosticos = " AND diacod IN ( {$array_diagnosticos} ) ";//
				$condicion_diagnosticos = " AND dia.codigo IN ( {$array_diagnosticos} ) ";//OJO CON EL ALIAS dia

            }else{

                foreach( $wdiagnosticos as $i => $codigoDiagnostico ){
                    array_push( $array_diagnosticos, $codigoDiagnostico );
                }
                $condicion_diagnosticos = "";
            }

            $query = " SELECT DISTINCT( Capitulo ) as Capitulo
                         FROM root_000011
                        WHERE codigo in ( {$condicion_diagnosticos2} ) ";

            $rsCaps = mysql_query( $query, $conex );

            while( $row = mysql_fetch_assoc( $rsCaps ) ){

                if( isset( $categorias_diagnos[utf8_encode($row['Capitulo'])] ) )
                    $categorias_diagnos[utf8_encode($row['Capitulo'])]++;
                    else
                        $categorias_diagnos[utf8_encode($row['Capitulo'])] = 1;
            }


            $query = " SELECT Diahis, Diaing, egrfee,  egd.Diacod Diacodigo, Egrest, concat( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) nombre, dia.descripcion Diadescripcion, egd.id id, pac.pacfna, pacsex, egr.egrcae, dia.Capitulo , ing.Ingfei Ingresofecha
                        
						FROM {$wcliame}_000109 egd
                        INNER JOIN
                              {$wcliame}_000108 egr on (  Egrhis = Diahis AND Egring = Diaing AND egd.diatip = 'P' )

                        INNER JOIN
                              {$wcliame}_000112 sev on ( sev.serhis = egr.egrhis AND sev.sering = egr.egring AND sev.seregr = 'on' )
							  
                        INNER JOIN
                              {$wcliame}_000100 pac on ( pachis = diahis )
                        INNER JOIN
                              root_000011 dia on ( dia.codigo = egd.diacod ) {$condicion_diagnosticos} 
							  
                        INNER JOIN
                              {$wcliame}_000101 ing on (   ing.inghis = egr.egrhis  AND  ing.ingnin = egr.egring  )
							  
                        {$condicionCcoConsultado}
						
						WHERE ( ing.Ingfei BETWEEN '{$fecha_inicio}' AND '{$fecha_final}' )
							 
							  
                        GROUP BY 1,2,3,4,5,6,7,8,9
						 
                        ORDER BY  ing.Ingfei , egrfee , egd.Diacod desc, nombre asc";

/*
             $queryoriginal = " SELECT Diahis, Diaing, egrfee,  egd.Diacod Diacodigo, Egrest, concat( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) nombre, dia.descripcion Diadescripcion, egd.id id, pac.pacfna, pacsex, sev.sercod, egr.egrcae, dia.Capitulo , ing.Ingfei Ingresofecha
                         FROM {$wcliame}_000109 egd
                        INNER JOIN
                              {$wcliame}_000108 egr on (  Egrhis = Diahis
                                                        AND Egring = Diaing {$condicion_diagnosticos} AND egd.diatip = 'P' )
                        INNER JOIN
                              {$wcliame}_000112 sev on ( sev.serhis = egr.egrhis AND sev.sering = egr.egring AND sev.seregr = 'on' )
                        INNER JOIN
                              {$wcliame}_000100 pac on ( pachis = diahis )
                        INNER JOIN
                              root_000011 dia on ( dia.codigo = egd.diacod )
							  
                        INNER JOIN
                              {$wcliame}_000101 ing on ( ing.inghis = egr.egrhis AND ing.ingnin = egr.egring  )
							  AND ( ing.Ingfei BETWEEN '{$fecha_inicio}' AND '{$fecha_final}' )
							  
                        {$condicionCcoConsultado}
                        GROUP BY 1,2,3,4,5,6,7,8,9
						 
                        ORDER BY  ing.Ingfei , egd.Diacod desc, nombre asc";
*/
 
 
//echo '<pre>'; print_r($query); echo '</pre><hr>';

            $rs    = mysql_query( $query, $conex ) or die( mysql_error() );
            $i     = 0;
            $historiaAnterior = "";
            $historiaNueva    = "";
			
			$vhtmlDiag = "";
			
			//Tabla de despliegue de la informacion
			$vhtmlDiag .= "<table style='border:1px solid black margin: 0 auto;'>";
			
			$vhtmlDiag .= "<tr class='encabezadotabla'>";

			$vhtmlDiag .= "<td align='center'>" . "#" . "</td>";
			
			$vhtmlDiag .= "<td>" . "Historia " . "</td>";
			$vhtmlDiag  .= "<td>" . "Ingreso " . "</td>";
			$vhtmlDiag  .= "<td>" . "Paciente ". "</td>";
			$vhtmlDiag  .= "<td>" . "Diagnostico " . "</td>";
			$vhtmlDiag  .= "<td>" . "&nbsp;&nbsp;F. Ingreso&nbsp;&nbsp;" . "</td>";
			$vhtmlDiag  .= "<td>" . "&nbsp;&nbsp;F. Egreso&nbsp;&nbsp;" . "</td>";			
			$vhtmlDiag  .= "</tr>";
			
			$ctrClass = 0;
		
			$array_diagnosticosPac = array();
            while( $row = mysql_fetch_assoc( $rs ) ){

//Esta es la informacion que se necesita en el listado.
//Historia Diahis
//Ingreso Diaing
//Nombre de paciente nombre
//Diagnostico(s) dia.descripcion egd.Diacod Diaing
//Fecha de ingreso   se hace join con {$wcliame}_000101 ingfei
//Fecha de EGRESO egrfee

				$viddiagnostico = $row['Diahis'] . "-" . $row['Diaing'] . "-"  . $row['Diacodigo'] ;
				//Mismo paciente mismo diagnostico , solo se debe registrar la primera vez(Marisol Jimenez Angelica Hernandez)

				if (!array_search($viddiagnostico, $array_diagnosticosPac))
				//No tener en cuenta diagnosticos repetidos para el mismo paciente
				{
					array_push( $array_diagnosticosPac, "'" . $viddiagnostico ."'" );
					$ctrClass++;
					$classDet = ( ($ctrClass%2)==0 ) ? "fila1" : "fila2";
					$vhtmlDiag  .= "<tr class='$classDet'>";
					
					$vhtmlDiag  .= "<td align='center'>" . $ctrClass . "</td>";
					
					$vhtmlDiag  .= "<td align='center'>" . $row['Diahis'] . "</td>";
					$vhtmlDiag  .= "<td align='center'>" . $row['Diaing'] . "</td>";
					$vhtmlDiag  .= "<td align='left'>" . $row['nombre'] . "</td>";
					$vhtmlDiag  .= "<td align='left'>" . $row['Diacodigo'] . "-" .  $row['Diadescripcion'] . "</td>";
					$vhtmlDiag  .= "<td align='center'>" . $row['Ingresofecha']  . "</td>";//$row['ingfei'] 
					$vhtmlDiag  .= "<td align='center'>" . $row['egrfee']  . "</td>";
					
					$vhtmlDiag  .= "</tr>";
					
				}//if (!array_search($viddiagnostico, $array_diagnosticosPac))


            }// while( $row = mysql_fetch_assoc( $rs ) ){

			$vhtmlDiag .= "</table>";
           
            break;//de case 'generarReporte':
        default:
            # code;
            break;
    }
	
	echo  $html  .  $vhtmlDiag ;
    return;
}

//-----------------------------------------> FUNCIONES <---------------------------------------------------------------------//

function inicializarArreglos(){

    global $conex;
    global $wmovhos;
    global $wemp_pmla;
    global $centrosCostos;
    global $diagnosticos;
    global $diagnosticosIncluidos;

    $auxDiagnostico = explode( "|", $diagnosticosIncluidos );
    $i = 0;
    $condicion = "";
	if ( ($diagnosticosIncluidos == "*")||($diagnosticosIncluidos == "%") ){
		$condicion = " 1 = 1 ";
	}else{
		foreach( $auxDiagnostico as $key => $datos )
		{
			$countIndividuales = 0;
			$countRango = 0;

			if( $i == 0 ){
				$condicion .= "";
			}else{
				$condicion .= " OR ";
			}
			$i++;

			$countRango = substr_count( $datos, "-" );

			if( $datos != "" and $countRango == 0 ){//--> es un individual
				$countIndividuales = 1;
			}

			if( $countIndividuales == 1 ){
				 $condicion .= " codigo = '{$datos}' ";
			}

			if( $countRango > 0 ){
				$auxDiagnostico2 = explode( "-", $datos );
				$condicion .= " ( codigo BETWEEN '{$auxDiagnostico2[0]}' AND '{$auxDiagnostico2[1]}' )";
			}

		}
		
	}

    $caracteres    = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
    $caracteres2   = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");

    $query = "  SELECT tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                  FROM {$wmovhos}_000011 AS tb1
                 WHERE tb1.Ccoest = 'on'
                   AND tb1.Ccocir = 'on'
                 ORDER BY nombre";

    $result = mysql_query( $query, $conex ) or die(mysql_error());
    while($row2 = mysql_fetch_array($result)){
         $row2['nombre'] = utf8_encode( $row2['nombre'] );
         $row2['nombre'] = str_replace( $caracteres, $caracteres2, $row2['nombre'] );
         array_push( $centrosCostos, trim($row2['codigo']).", ".trim($row2['nombre']) );
    }

    $query  = " SELECT codigo, Descripcion as nombre, Capitulo
                  FROM root_000011
                 WHERE ( $condicion )";

    $result = mysql_query( $query, $conex ) or die( '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] URL EQUIVOCADA.<br />No se especificaron correctamente los diagnoticos a consultar.
            </div>');
    while($row2 = mysql_fetch_array($result)){
         $row2['nombre'] = utf8_encode( $row2['nombre'] );
         $row2['nombre'] = str_replace( $caracteres, $caracteres2, $row2['nombre'] );
         array_push( $diagnosticos, trim($row2['codigo']).", ".trim($row2['nombre']) );
    }
}


//---------------------------------------> FIN FUNCIONES <-------------------------------------------------------------------//
?>

<!DOCTYPE html>
<html>
    <head>
        <title>REPORTE DIAGNOSTICOS POR GRUPOS ETAREOS </title>
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

        .tablaColapsada td{
            border:1px solid;
            border-collapse: collapse;
        }

        .div_resumen{
            padding-left: 15%;
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
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
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

            $('#incluir_diagnostico').multiselect({
               position: {
                  my: 'left bottom',
                  at: 'left top'

               },
            selectedText: "# of # seleccionados",
            }).multiselectfilter();

        });

        function consultarFechaMaxima( fecha_inicio1 ){
            var fecha = '';
            $.ajax({
                url  : "rep_rango_diagnosticos.php",
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

        function alerta( txt ){
            $("#textoAlerta").text( txt );
            $.blockUI({ message: $('#msjAlerta') });
                setTimeout( function(){
                    $.unblockUI();
                }, 3000 );
        }

        function generarReporte(){

            var diagnosticos = {};
            var selected = $("#incluir_diagnostico").val();

            if( selected == undefined ){
                alerta( "DEBE SELECCIONAR ALGUN DIAGNOSTICO" );
                return;
            }
            var diagnosticosJson = $.toJSON( selected );
            var condicion = $("input[type='radio'][name='tipoReporte']:checked").val();
            $("#div_respuesta").hide();
            $("#msjEspereSolicitud").show();
            $.ajax({
                url  : "rep_rango_diagnosticos.php",
                type : "post",
                async: false,
                 data:
                {
                    consultaAjax   : "on",
                    consulta       : 'generarReporte',
                    wemp_pmla      : $("#wemp_pmla").val(),
                    wdiagnosticos  : diagnosticosJson,
                    fecha_inicio   : $("#fecha_inicio").val(),
                    fecha_final    : $("#fecha_final").val(),
                    condicion      : condicion,
                    //difSexo        : $("input[type='radio'][name='diferenciarSexo']:checked").val(),
                    //incluirTodosDx : $("input[type='radio'][name='incluirTodosDx']:checked").val(),
                    //condicionCCo   : $("#lista_grupos > option:selected").val()
                },
                success : function(data){
                    if(data != "")
                    {
                        $("#div_respuesta").html(data);
                        $("#div_respuesta").show();
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


        function mostrarDetalle( descripcion, sexo,  id_div_detalle ){
            // --> Ventana dialog para cargar el iframe
            $("#div_"+id_div_detalle).dialog({
                show:{
                    effect: "blind",
                    duration: 0
                },
                hide:{
                    effect: "blind",
                    duration: 100
                },
                width:  '900px',
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "DETALLE "+descripcion+" SEXO:"+sexo,
                buttons:[
                {
                    text: "Cerrar",
                    icons:{
                            primary: "ui-icon-heart"
                    },
                    click: function(){
                        $(this).dialog("close");
                        $(this).dialog("destroy");
                    }
                }],
                close: function( event, ui ) {
                    ///-->
                }
            });
            $("#divFormularioHce").parent().css({"z-index":"999"}).next().css({"z-index":"998"});
        }
    </script>
    <body>
    <?php

        $centrosCostos = array();
        $diagnosticos  = array();
        $wgruposccos   = array( "ccourg"=>"URGENCIAS","ccohos"=>"HOSPITALIZACION" );
        $array_categorias = array();
        inicializarArreglos();
        //$centrosCostos = json_encode( $centrosCostos );
        $ccoInicial    = "%";
    ?>

    <?php encabezado( " REPORTE POR RANGO DE DIAGNOSTICOS ", $wactualiz, "clinica" ); ?>
    <br><br><br>

    <input type='hidden' id='wemp_pmla'       value='<?=$wemp_pmla;?>'>
    <input type='hidden' id='fecha_hoy'       value='<?=date("Y-m-d");?>'>
    <input type='hidden' name='array_ccos' id='array_ccos' value='<?=$centrosCostos?>'>

    <div id='div_formulario' align='center' class='div_formulario'>
        <span class="subtituloPagina2">Parámetros de consulta</span><br><br>
        <table class='tabla_formulario'>

            <!--<tr>
                <td class='subEncabezado fila1'> SERVICIO EGRESO: </td>
                <td class='fila2'>
                    <SELECT id='wcco_egreso'>
                        <option value='%' selected > %-TODOS </option>
                        <?php
                            /*foreach( $centrosCostos as $i => $datosCcos ){
                                $codigo = explode(",", $datosCcos);
                                $codigo = trim( $codigo[0] );
                                echo "<option value='{$codigo}'> $datosCcos </option>";
                            }*/
                        ?>
                    </SELECT>
                </td>
            </tr>-->
            <tr>
                <td class='subEncabezado fila1'> DIAGNOSTICOS: </td>
                <!--<td class='fila2'> <input type='text' id='input_cco' keyup='validarVacio( this );' size='30' > <input type='hidden' id='wcco' name='wcco' value=''></td>-->
                <td class='fila2'>
                    <div id="select_incluir_diagnostico" style="display: inline;">
                         <select id="incluir_diagnostico" multiple="multiple">
                        <?php
                          foreach( $diagnosticos as $key => $value ){
                            $codigoDiagnostico  = explode( ",", $value );
                            $codigoDiagnostico  = trim($codigoDiagnostico[0]);
                            echo '<option value="'.$codigoDiagnostico.'" name="diagnostico">'.$value.'</option>';
                          }
                        ?>
                        </select>
                    </div>
                </td>
            </tr>



            <!--
            <tr>
                <td class='fila2'>
                    <input type='radio' name='tipoReporte' nombre='EA' checked value=''> TIPO EA &nbsp;&nbsp; <input type='radio' name='tipoReporte' nombre='RESPIRATORIAS' value='sep_uci_mue'> TIPO Respiratorias.
                </td>
            </tr>
            -->

 


            <tr>
                <td class='subEncabezado fila1'> PERIODO: </td><td class='fila2'><input id='fecha_inicio' disabled size='12' type='text' value='<?=date("Y-m-d");?>'> Hasta <input id='fecha_final' size='12' disabled type='text' value='<?=date("Y-m-d");?>'></td>
            </tr>
        </table>
        <br>
        <input type="button" onclick="generarReporte();" value="BUSCAR" class="botona" id="btn_consultar">
    </div><br>
    <center>
        <div id='div_respuesta' style='width:120%;' align='left'></div>
    </center>
    <center><input type="button" value='Cerrar Ventana' onclick='cerrarVentana()'></center>
    <div id='msjAlerta' style='display:none;'>
        <br>
        <img src='../../images/medical/root/Advertencia.png'/>
        <br><br><div id='textoAlerta'></div><br><br>
    </div>
    </body>
</html>