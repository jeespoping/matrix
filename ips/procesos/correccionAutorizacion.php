<?php
include_once("conex.php");

include_once("root/comun.php");


$conex          = obtenerConexionBD("matrix");
$conexunix      = odbc_connect('admisiones','informix','sco') or die("No se realizó Conexion con el Unix");
$fecha_hoy      = date('Y-m-d');
$hoy_segundos   = strtotime( $fecha_hoy );
$fechaIniMinima = date("Y-m-d", strtotime($fecha_hoy."-2 month"));

if( $peticionAjax ){
    switch ( $peticionAjax ) {
        case 'corregir':
            corregirDatos( $fechaInicial, $fechaFinal );
            break;
        case 'fechaLimiteConsulta':
            $movimiento   = ( $origen == "fechaFinal") ? "-2 month" : "+2 month";
            $segundosBase = strtotime( $fechaInicioConsulta.$movimiento );
            $segundosBase = ( $segundosBase >= $hoy_segundos ) ? $hoy_segundos : $segundosBase;
            if( $origen == "fechaFinal" ){
                if( strtotime( $fechaInicialActual ) >= $segundosBase ){
                    echo $fechaInicialActual;
                    break;
                }
            }else{
                if( strtotime( $fechaFinalActual ) <= $segundosBase ){
                    echo $fechaFinalActual;
                    break;
                }
            }
            $fechaLimiteConsulta = date('Y-m-d', $segundosBase );
            echo $fechaLimiteConsulta;
            break;
        default:
            # code...
            break;
    }
    return;
}

function corregirDatos( $fechaInicial, $fechaFinal ){
    global $conex, $conexunix;
    $query = "SELECT Reshis as Inghis, Resing as Ingnin, Resaut as Ingord, Resnit
                FROM cliame_000205
               WHERE fecha_data between '{$fechaInicial}' and '{$fechaFinal}'
                 AND Resord = 1
                 AND Resdes = 'off'
                 AND Resaut != ''";
    echo $query;
    $rs    = mysql_query( $query, $conex );

    $corregidosOrd  = 0;
    $corregidosDet  = 0;
    $afectadosMsate = 0;

    while( $row = mysql_fetch_assoc( $rs ) ){

        //CORRECCIÓN DE DATOS EN MSATE
        $q = "SELECT count(*)
                FROM msate
               WHERE atehis = '{$row['Inghis']}'
                 AND ateing = '{$row['Ingnin']}'
                 AND ateaut is null";
        $res1 = odbc_do($conexunix,$q);
        $afectadosMsate += odbc_result($res1,1 );

        if( odbc_result($res1,1 ) > 0 ){
            $q = "UPDATE msate
                     SET ateaut = '{$row['Ingord']}'
                   WHERE atehis = '{$row['Inghis']}'
                     AND ateing = '{$row['Ingnin']}'";
            echo "<br> MSATE edb-> {$row['Inghis']}, {$row['Ingnin']}";
            $res = odbc_do($conexunix,$q);
        }

        //CORRECCIÓN DE DATOS EN MSATE
        $q = "SELECT count(*)
                FROM INPACORD
               WHERE pacordhis = '{$row['Inghis']}'
                 AND pacordnum = '{$row['Ingnin']}'";
        $res = odbc_do($conexunix,$q);

        if( odbc_result($res,1 ) == 0 ){

            $sql = "INSERT INTO INPACORD( pacordhis , pacordnum, pacordord, pacordest )
                                 VALUES ( '{$row['Inghis']}','{$row['Ingnin']}','{$row['Ingord']}','A')";
            $res = odbc_do($conexunix,$sql);
            echo "<br> INPACORD edb-> {$row['Inghis']}, {$row['Ingnin']}";
            $corregidosOrd++;

        }else{

        }

        //CORRECCIÓN DE DATOS EN MSATE
        $q = "SELECT count(*)
                FROM INORDDET
               WHERE orddethis = '{$row['Inghis']}'
                 AND orddetnum = '{$row['Ingnin']}'";

        $res = odbc_do($conexunix,$q);
        if( odbc_result($res,1 ) == 0 ){

            $sql = "INSERT INTO INORDDET( orddethis ,orddetnum , orddetord, orddetest)
                                  VALUES( '{$row['Inghis']}', '{$row['Ingnin']}', '{$row['Ingord']}', 'A' )";
            $res = odbc_do($conexunix,$sql);
            $corregidosDet++;

        }else{

        }

        //CORRECCIÓN DE DATOS EN MSATE
        $q = "SELECT count(*)
                FROM INORDDET
               WHERE orddethis = '{$row['Inghis']}'
                 AND orddetnum = '{$row['Ingnin']}'
                 AND orddetcer = '800067065-9'";

        $res = odbc_do($conexunix,$q);
        if( odbc_result($res,1 ) == 1 ){

            $sql = "UPDATE INORDDET
                     SET orddetcer = '{$row['Resnit']}'
                   WHERE orddethis = '{$row['Inghis']}'
                     AND orddetnum = '{$row['Ingnin']}'
                     AND orddetcer = '800067065-9'";
            echo "<br> INORDDET edb-> {$row['Inghis']}, {$row['Ingnin']} POR CODIGO CLINICA ";
            /*$sql = "INSERT INTO INORDDET( orddethis ,orddetnum , orddetord, orddetest)
                                  VALUES( '{$row['Inghis']}', '{$row['Ingnin']}', '{$row['Ingord']}', 'A' )";*/
            $res = odbc_do($conexunix,$sql);
            $corregidosDet++;

        }else{

        }

    }
    echo "<br>edb->corregidos en msate: {$afectadosMsate} - corregidos en inpacord: {$corregidosOrd} - corregidos en INORDDET: {$corregidosDet} ";
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" >
    <title></title>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librería para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
    <script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
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
    <script type="text/javascript" charset="utf-8" async defer>

        $(document).ready(function(){

            $("#fechaInicial").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                maxDate:  $("#fecha_hoy").val(),
                //minDate:  $("#fechaIniMinima").val(),
                onSelect: function(dateText){
                    fechaLimite = consultarFechaLimite( dateText, $(this).attr("id") );
                    reestablecerLimites( $("#fechaFinal"), $("#fecha_hoy").val(), dateText, fechaLimite );
                }
            });

            $("#fechaFinal").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                maxDate:  $("#fecha_hoy").val(),
                //minDate:  $("#fechaInicial").val(),
                onSelect: function(dateText){
                    fechaLimite = consultarFechaLimite( dateText, $(this).attr("id") );
                    reestablecerLimites( $("#fechaInicial"), dateText, fechaLimite, fechaLimite );
                }
            });
        });

        function reestablecerLimites( obj, fechaMaxima, fechaMinima, fechaDefecto ){

            $( obj ).datepicker( "destroy" );
            $( obj ).val( fechaDefecto );
            $( obj ).datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                maxDate: fechaMaxima,
                minDate: fechaMinima,
                onSelect: function(dateText){
                    fechaLimite = consultarFechaLimite( dateText, $(this).attr("id") );
                    if( $(this).attr("id") == "fechaFinal" ){
                        reestablecerLimites( $("#fechaInicial"), dateText, '2008-01-01', fechaLimite );
                    }else{
                        reestablecerLimites( $("#fechaFinal"), $("#fecha_hoy").val(), dateText, fechaLimite );
                    }
                }
            });
        }

        function consultarFechaLimite( fechaBase, origen ){
            var fechafinalcalculada = '';
            $.ajax({
                    url: "correccionAutorizacion.php",
                   type: "POST",
                  async: false,
                   data: {
                            peticionAjax: "fechaLimiteConsulta",
                            consultaAjax: "si",
                     fechaInicioConsulta: fechaBase,
                                  origen: origen,
                      fechaInicialActual: $("#fechaInicial").val(),
                        fechaFinalActual: $("#fechaFinal").val()
                          },
                    success: function(data)
                    {
                        fechafinalcalculada =  data;
                    }
            });
            return(fechafinalcalculada);
        }

        function corregirAutorizacionesFaltantes(){
            $("#div_respuesta").html("");
            $("#msjEspere").show();
            $.ajax({
                    url: "correccionAutorizacion.php",
                   type: "POST",
                  async: false,
                   data: {
                            peticionAjax: "corregir",
                            consultaAjax: "si",
                            fechaInicial: $("#fechaInicial").val(),
                              fechaFinal: $("#fechaFinal").val()
                          },
                    success: function(data)
                    {
                        $("#msjEspere").hide();
                        $("#div_respuesta").html(data);
                    }
            });
        }

    </script>
</head>
<body>
<?php encabezado( " CORREGIR NÚMERO DE ORDEN EN UNIX ", $wactualiz, "clinica" ); ?>

    <input type='hidden' id='fecha_hoy' value='<?=$fecha_hoy;?>'>
    <input type='hidden' id='fechaIniMinima' value='<?=$fechaIniMinima;?>'>
    <div id='div_formulario_ppal' align='center'>
        <table>
            <tr class='encabezadotabla'><td colspan='2'> SELECCIONE DATOS DE CONSULTA DEL REPORTE </td></tr>
            <tr class='fila2'>
                <?php

                ?>
              <tr class='fila2'>
                <td id='td_fechaCorte' title='<?=$title;?>' style='cursor:pointer;'><b>Rango de fechas:</b></td>
                <td>
                    <input type='text' size='10' id='fechaInicial' value='<?=$fecha_hoy;?>'> hasta <input type='text' size='10' id='fechaFinal' value='<?=$fecha_hoy;?>'>
                </td>
              </tr>
        </table>
    </div>
    <br>
    <div align='center'>
        <table>
               <tr><td align='center'><input type='button' value='Generar' id='btn_generar' onclick='corregirAutorizacionesFaltantes();'></td></tr>
        </table>
    </div>
    <div id='div_respuesta' align='center'>
    </div>
    <center><div id='msjEspere' style='display:none;'>
        <br>
        <img src='../../images/medical/ajax-loader5.gif'/>
        <br><br> Por favor espere un momento ... <br><br>
    </div></center>
    <center><br><br><input type='button' value='Cerrar' id='btn_cerrar' onclick='window.close();'></center>
</body>
</html>
