<?php
include_once("conex.php");
/**
*       UTILIZACION DE CAMAS POR SERVICIO HABITACION     *
*       PORCENTAJES DE UTILIZACION SERVICIO Y CLINICA    *
*                    CONEX, FREE => OK                   *
*********************************************************/

//==================================================================================================================================================//
// Este programa muestra la ocupación total de la clinica y de cada una de sus unidades de hospitalización, cirugia y urgencias, asi como de los
// pacientes que se les hace ingreso y solo vienen a realizarse examenes.
//
// FINALMENTE: Este programa está basado en el anterior reporte de utilización de camas, solo que ya no consulta en unix, haciendolo únicamente en
// matrix
//=====================================================================================
include_once( "root/comun.php" );
require_once("conex.php");
mysql_select_db( "matrix" );
$fecha_hoy   = date('Y-m-d');
$hora        = date("H:i:s");
$wactualiz   = "2014-10-15";
if( !isset($_SESSION['user']) ){//session muerta en una petición ajax

      echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
             </div>";
      return;
}

function consultarFactoresConsulta(){
    global $wmovhos, $wcliame, $wemp_pmla, $conex, $caracteres, $caracteres2;
    $datos = array();

    $query = " SELECT Ccocod codigo, Cconom nombre
                 FROM {$wmovhos}_000011
                WHERE ccoest = 'on'
                  AND Ccocod != '*'
                  AND ( Ccohos = 'on' or Ccocir = 'on' or Ccourg = 'on' )
                ORDER BY 2";
    $rs    = mysql_query( $query, $conex );
    while( $row =  mysql_fetch_array( $rs ) ){
        $datos['centrosCostos'][$row['codigo']] = $row['nombre'];
    }

    $query = " SELECT Habcod codigo, Habcod nombre, Habcco  centrocostos
                 FROM {$wmovhos}_000020
                WHERE Habest = 'on'
                ORDER BY 3";
    $rs    = mysql_query( $query, $conex );
    while( $row =  mysql_fetch_array( $rs ) ){
        $datos['habitaciones'][$row['codigo']]['nombre'] = $row['nombre'];
        $datos['habitaciones'][$row['codigo']]['cco']    = $row['centrocostos'];
    }

    $query = " SELECT Empcod codigo, Empnom nombre, Empnit nit
                 FROM {$wcliame}_000024
                WHERE Empest = 'on'
                ORDER BY 2";
    $rs    = mysql_query( $query, $conex ) or die( mysql_error() );

    $num = mysql_num_rows($rs);
    $arreglo = array();
    if ($num > 0 ){
            while( $row = mysql_fetch_assoc($rs) ){
                $row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
                $row['nombre'] = utf8_decode( $row['nombre'] );
                array_push($arreglo,  trim($row['codigo']).", ".trim($row['nit']).", ".trim($row['nombre']) );
            }
        array_push( $arreglo, "%, Todos" );
    }
    $datos['entidades'] = $arreglo;

    return( $datos );
}

//utilizar consultaAjax para evitar  la inclución de root nuevamente
if( $peticionAjax == "consultarDatos" ){

    $arrayCcos             = array(); //array que contiene los datos de los centros de costos
    $arrayCcoUrg           = array(); //array que contiene los datos de los centros de costos de urgencias
    $arrayCcoUrgAux        = array(); //array que contiene los datos de los centros de costos de urgencias para control de impresion en pantalla
    $arrayEmps             = array(); //array que contiene los codigos y los nombres de las entidades responsables de los pacientes
    $totalHabitaciones     = 0;
    $totalOcupadasCli      = 0;
    $totalDesocupadasCli   = 0;
    $totalFueraServicioCli = 0;
    $busquedaParticular    = false;

    //---> condiciones de usuario  <------//
    $condicionCco11 = "";
    $condicionCco20 = "";
    //( $wcco == "%") ? $condicionCco11 = "" : $condicionCco11 = " AND Ccocod = '{$wcco}' ";
    //( $wcco == "%") ? $condicionCco20 = "" : $condicionCco20 = " AND Habcco = '{$wcco}' ";
    //( $wcco == "%") ? $condicionCco20 = "" : $condicionCco20 = " AND Habcco = '{$wcco}' ";
    if( !$busquedaParticular and $wcco != "%" )
        $busquedaParticular = true;

    if( !$busquedaParticular and $wres != "%" ){
        $busquedaParticular = true;
        $wres = explode( ",", $wres );
        $wres = trim($wres[0]);
        $condicionResponsable20 = " AND Empcod = '{$wres}'";
        $condicionResponsable16 = " AND Ingres = '{$wres}'";
    }

    //-----> entidades responsables <------//
    $query = " SELECT Empcod codigo, Empnom nombre, Empnit nit
                 FROM {$wcliame}_000024
                WHERE Empest = 'on'
                {$condicionResponsable}
                ORDER BY 2";
    $rs    = mysql_query( $query, $conex ) or die( mysql_error() );

    $num = mysql_num_rows($rs);
    while( $row = mysql_fetch_assoc($rs) ){
        //$row['nombre']                   = str_replace( $caracteres, $caracteres2, $row['nombre'] );
        $row['nombre']                   = utf8_encode( $row['nombre'] );
        $arrayEmps[trim($row['codigo'])] = trim($row['nombre']);
    }

    //-----> centros de costos <------/
    $query = " SELECT Ccocod codigo, Cconom nombre, Ccohos, Ccocir, Ccourg
                 FROM {$wmovhos}_000011
                WHERE ccoest = 'on'
                      {$condicionCco11}
                  AND Ccocod != '*'
                  AND ( Ccohos = 'on' or Ccocir = 'on' or Ccourg = 'on' )
                ORDER BY 2";
    //echo $query;
    $rs    = mysql_query( $query, $conex );

    while( $row =  mysql_fetch_assoc( $rs ) ){
        $arrayCcos[$row['codigo']]['nombre'] = $row['nombre'];
        if( !isset( $arrayCcos[$row['codigo']]['resumen'] ) )
            $arrayCcos[$row['codigo']]['resumen']['totalHabitaciones']  = 0;
            $arrayCcos[$row['codigo']]['resumen']['totalDisponibles']   = 0;
            $arrayCcos[$row['codigo']]['resumen']['totalFueraServicio'] = 0;
            $arrayCcos[$row['codigo']]['resumen']['totalOcupadas']      = 0;

        if( $row['Ccocir'] == "on" or $row['Ccourg'] == "on" ){
            array_push( $arrayCcoUrg, "'".$row['codigo']."'" );
            array_push( $arrayCcoUrgAux, $row['codigo']  );
        }
    }
    $ccosUrg = implode( ",", $arrayCcoUrg );

    //--> consulta y armado de información de los centros de costos hospitalarios
    $query = " SELECT Habcco centroCostos, Habhis, Habing, habdis disponible, Habpro procesoOcupacion, Habord orden, Habali alistamiento, habcod habitacion,
                      Ingres responsable, Concat( Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2 ) nombre
                 FROM {$wmovhos}_000020
                 LEFT JOIN
                      {$wmovhos}_000016 on ( inghis = habhis AND Inging = habing {$condicionCco20} )
                 LEFT JOIN
                      root_000037 on ( Habhis = orihis AND oriori = '{$wemp_pmla}')
                 LEFT JOIN
                      root_000036 on ( Pactid = Oritid AND Pacced = Oriced )
                WHERE Habest = 'on'
                ORDER BY centroCostos, habitacion asc";
  //  echo $query;
    /*{$condicionCco20}
    {$condicionResponsable16}´*/
    $rs    = mysql_query( $query, $conex );

    while( $row = mysql_fetch_assoc( $rs ) ){

        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['historia']            = $row['Habhis'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['ingreso']             = $row['Habing'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['responsable']         = $arrayEmps[$row['responsable']];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['codResponsa']         = $row['responsable'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['nombre']              = utf8_encode( $row['nombre'] );
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['disponible']          = $row['disponible'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['procesoOcupacion']    = $row['procesoOcupacion'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['procesoAlistamiento'] = $row['alistamiento'];

        $arrayCcos[$row['centroCostos']]['resumen']['totalHabitaciones']++;
        if( $row['disponible'] == "on" ){
            $arrayCcos[$row['centroCostos']]['resumen']['totalDisponibles']++;
            $totalDesocupadasCli++;
        }else{

            if( $row['alistamiento'] == "on" ){
                $arrayCcos[$row['centroCostos']]['resumen']['totalFueraServicio']++;
                $totalFueraServicioCli++;
            }
            if(  $row['Habhis'] != "" and $row['Habing'] != "" ){
                $arrayCcos[$row['centroCostos']]['resumen']['totalOcupadas']++;
                $totalOcupadasCli++;
            }
            if( $row['alistamiento'] == "off" and $row['Habhis'] == "" and $row['Habing'] == "" ){
                $arrayCcos[$row['centroCostos']]['resumen']['totalFueraServicio']++;
                $totalFueraServicioCli++;
            }
        }
        //--> variables para el resumen TOTAL del reporte
        $totalHabitaciones++;
    }

    $query = " SELECT Ubisac centroCostos, Mtrhis, Mtring, 'NA' disponible, 'NA' procesoOcupacion, 'NA' orden, 'NA' alistamiento, concat( a.fecha_data,' ', a.hora_data ) habitacion,
                      ingres responsable, Concat( Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2 ) nombre
                 FROM {$wmovhos}_000018 a
                INNER JOIN
                      {$whce}_000022    on ( ubihis = mtrhis AND ubiing = Mtring AND ubisac in ($ccosUrg) AND Ubimue != 'on' AND Ubiald != 'on')
                INNER JOIN
                      {$wmovhos}_000016 on ( inghis = Ubihis AND Inging = Ubiing {$condicionCco20}  )
                INNER JOIN
                      root_000037       on ( Mtrhis = orihis AND oriori = '{$wemp_pmla}')
                INNER JOIN
                      root_000036       on ( Pactid = Oritid AND Pacced = Oriced )
                WHERE 1
                ORDER BY centroCostos, habitacion asc";

    $rs     = mysql_query( $query, $conex );
    while( $row = mysql_fetch_assoc( $rs ) ){

        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['historia']            = $row['Mtrhis'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['ingreso']             = $row['Mtring'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['responsable']         = $arrayEmps[$row['responsable']];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['codResponsa']         = $row['responsable'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['nombre']              = utf8_encode( $row['nombre'] );
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['disponible']          = $row['disponible'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['procesoOcupacion']    = $row['procesoOcupacion'];
        $arrayCcos[$row['centroCostos']]['habitaciones'][$row['habitacion']]['procesoAlistamiento'] = $row['alistamiento'];

        $arrayCcos[$row['centroCostos']]['resumen']['totalHabitaciones']++;
        if( $row['disponible'] == "on" ){
            $arrayCcos[$row['centroCostos']]['resumen']['totalDisponibles']++;
            $totalDesocupadasCli++;
        }else{

            if( $row['alistamiento'] == "on" ){
                $arrayCcos[$row['centroCostos']]['resumen']['totalFueraServicio'] = 0;
                $totalFueraServicioCli++;
            }
            if(  $row['Habhis'] != "" and $row['Habing'] != "" ){
                $arrayCcos[$row['centroCostos']]['resumen']['totalOcupadas'] = 0;
                $totalOcupadasCli++;
            }
            if( $row['alistamiento'] == "off" and $row['Habhis'] == "" and $row['Habing'] == "" ){
                $arrayCcos[$row['centroCostos']]['resumen']['totalFueraServicio'] = 0;
                $totalFueraServicioCli++;
            }
        }
    }
   /* echo "<pre>";
        print_r( $arrayCcos );
    echo "</pre>"; */

    $resumen = "";
    $respuesta .= "<div align='right' id='buscador' style='width:70%; display:none;'><input type='text' id='buscador'><img width='25px' height='15px' title='Buscar' src='../../images/medical/root/lupa.png'></div>";
    $respuesta .= "<table id='tbl_resultados'>";
    foreach ( $arrayCcos as $codigoCco => $datos ) {
         $i = 0;
         if( $datos['resumen']['totalHabitaciones'] > 0 ){
            $porcentajeOcupacionCco = ( $datos['resumen']['totalOcupadas']*1 / $datos['resumen']['totalHabitaciones']*1 )*100;
            $porcentajeOcupacionCli = ( $datos['resumen']['totalOcupadas']*1 / $totalHabitaciones )*100;
            ( $wcco != "%" and $wcco != $codigoCco ) ? $claseOculta = "on" : $claseOculta = "off";
            $resumen  = "<div id='div_resumen_{$codigoCco}'  align='center' style='display:block;'>";
            $resumen .= "<br>";
            $resumen .= "<span class='subtituloPagina2'><font size='2'> Resumen centro de costos: ".utf8_decode( $datos['nombre'] )."</font></span>";
            $resumen .= "<table>";
            $resumen .= "<tr resumen='on' centrosCostos='{$codigoCco}'><td> &nbsp; </td><td class='botona'>Camas clínica: {$totalHabitaciones}</td></tr>";
            $resumen .= "<tr class='encabezadoTabla' resumen='on' centrosCostos='{$codigoCco}'><td align='center'> Indicador </td><td align='center'>Valor</td></tr>";
            $resumen .= "<tr class='fila2' resumen='on' centrosCostos='{$codigoCco}' tipo='titulo'><td> Num. Camas Totales </td><td align='center'>{$datos['resumen']['totalHabitaciones']}</td></tr>";
            $resumen .= "<tr class='fila2' resumen='on' centrosCostos='{$codigoCco}' tipo='titulo'><td> Num. Camas Fuera de Servicio </td><td align='center'>{$datos['resumen']['totalFueraServicio']}</td></tr>";
            $resumen .= "<tr class='fila2' resumen='on' centrosCostos='{$codigoCco}' tipo='titulo'><td> Num. Camas disponibles </td><td align='center'>{$datos['resumen']['totalDisponibles']}</td></tr>";
            $resumen .= "<tr class='fila2' resumen='on' centrosCostos='{$codigoCco}' tipo='titulo'><td> Num. Camas Ocupadas </td><td align='center'>{$datos['resumen']['totalOcupadas']}</td></tr>";
            $resumen .= "<tr class='fila2' resumen='on' centrosCostos='{$codigoCco}' tipo='titulo'><td> % de ocupación servicio </td><td align='center'>".number_format( $porcentajeOcupacionCco, 1, '.', '.' )."</td></tr>";
            $resumen .= "<tr class='fila2' resumen='on' centrosCostos='{$codigoCco}' tipo='titulo'><td> % de ocupación Respecto a Clínica </td><td align='center'>".number_format( $porcentajeOcupacionCli, 1, '.', '.' )."</td></tr>";
            $resumen .= "</table><br>";
            $resumen .= "</div>";
            ( in_array( $codigoCco, $arrayCcoUrgAux ) ) ? $habitacion   = " Ingreso " : $habitacion = " Habitaci&oacute;n ";
            ( in_array( $codigoCco, $arrayCcoUrgAux ) ) ? $hospitalario = " hospitalario = 'off' " : $hospitalario = " hospitalario = 'on' ";
            ( in_array( $codigoCco, $arrayCcoUrgAux ) ) ? $oculto2      = " oculto = 'off' " : $oculto2 = " ";
            ( in_array( $codigoCco, $arrayCcoUrgAux ) ) ? $numPacientes = " -  Num. Pacientes: {$datos['resumen']['totalHabitaciones']}" : $numPacientes = " ";
            ( in_array( $codigoCco, $arrayCcoUrgAux ) ) ? $fontsize = " size='1' " : $fontsize = " ";
            ( !$busquedaParticular or $busquedaParticular ) ? $mostrarResumen = "" : $mostrarResumen = " <span class='subtituloPagina2' style='cursor:pointer;' onclick='mostrarResumen( \"$codigoCco\", \"".utf8_decode( $datos['nombre'] )."\" );'><font size='2' color='white' > &nbsp;&nbsp; ( Resumen ocupación ) </font></span> ";
            $respuesta .= "<tr $hospitalario $oculto2 class='encabezadoTabla' tipo='titulo' centrocostos='{$codigoCco}' $style><td align='left' colspan='5'><b><font size='3px'>".utf8_decode( $datos['nombre'] )."</font>$mostrarResumen $numPacientes</b></td></tr>";
            $respuesta .= "<tr $hospitalario $oculto2 class='encabezadoTabla' tipo='titulo' centrocostos='{$codigoCco}' $style><td align='center'> {$habitacion} </td><td align='center'> Historia </td><td align='center'>  Nombre paciente  </td><td align='center'> Alistamiento </td><td align='center'> Responsable </td></tr>";
            foreach( $datos['habitaciones'] as $codigoHabitacion => $datosHabitacion ) {
                if( $claseOculta == "off")
                    $i++;
                ( is_int( $i/2 ) ) ? $wclass = "fila1" : $wclass = "fila2";
                ( !empty( $datosHabitacion['historia'] ) )   ? $whistoria   = " {$datosHabitacion['historia']} - {$datosHabitacion['ingreso']} " : $whistoria = " &nbsp; ";
                $walistamiento = "&nbsp;";
                if( $datosHabitacion['procesoAlistamiento'] == "on" ){
                    $walistamiento = " En proceso ";
                }else{
                    if( trim($datosHabitacion['disponible']) == "on" )
                        $walistamiento = "<img width:'15px' height='15px' src='/matrix/images/medical/movhos/checkmrk.ico'>";
                }

                ( $wres != "%" and $wres != $datosHabitacion['codResponsa'] ) ? $claseOculta = "on" : $claseOculta = "off";
                $respuesta .= "<tr $hospitalario  class='$wclass' disponible='{$datosHabitacion['disponible']}' oculto='$claseOculta' centrocostos='{$codigoCco}' responsable='{$datosHabitacion['codResponsa']}'>";
                        $respuesta .= "<td align='left' width='120px' nowrap='nowrap'><b><font color='003300' $fontsize>{$codigoHabitacion}</font></b></td>";
                        $respuesta .= "<td align='center'>{$whistoria}</td>";
                        $respuesta .= "<td align='left'>{$datosHabitacion['nombre']}</td>";
                        $respuesta .= "<td align='center'><b><font color='003300'>{$walistamiento}</font></b></td>";
                        $respuesta .= "<td align='center'>{$datosHabitacion['responsable']}</td>";
                $respuesta .= "</tr>";
            }
            if( !(in_array( $codigoCco, $arrayCcoUrgAux ) ) ){
                $respuesta .= "<tr $hospitalario  oculto='$claseOculta' centrocostos='$codigoCco'><td colspan='5'>".$resumen."</td></tr>";
            }else{
                $respuesta .= "<tr $hospitalario  oculto='$claseOculta' centrocostos='$codigoCco'><td colspan='5'>&nbsp;</td></tr>";
            }

         }
     }
     $respuesta .= "</table><br>";
     //if( !$busquedaParticular ){
        $porcentajeOcupacionCco = ( $totalOcupadasCli*1 / $totalHabitaciones*1 )*100;
        $respuesta .= "<table id='resumen total'>";
            $respuesta .= "<tr class='encabezadoTabla'><td align='center' colspan='2'> TOTAL INDICADORES CLÍNICA </td></tr>";
            $respuesta .= "<tr class='encabezadoTabla'><td align='center'> Indicador </td><td align='center'>Valor</td></tr>";
            $respuesta .= "<tr class='fila2'><td> Num. Camas Totales </td><td align='center'>{$totalHabitaciones}</td></tr>";
            $respuesta .= "<tr class='fila2'><td> Num. Camas Fuera de Servicio </td><td align='center'>{$totalFueraServicioCli}</td></tr>";
            $respuesta .= "<tr class='fila2'><td> Num. Camas Desocupadas </td><td align='center'>{$totalDesocupadasCli}</td></tr>";
            $respuesta .= "<tr class='fila2'><td> Num. Camas Ocupadas </td><td align='center'>{$totalOcupadasCli}</td></tr>";
            $respuesta .= "<tr class='fila2'><td> % de ocupación servicio </td><td align='center'>".number_format( $porcentajeOcupacionCco, 1, '.', '.' )."</td></tr>";
           // $respuesta .= "<tr class='fila2'><td> % de ocupación Respecto a Clínica </td><td align='center'>".number_format( $porcentajeOcupacionCli, 1, '.', '.' )."</td></tr>";
        $respuesta .= "</table><br>";
     //}
     echo $respuesta;
     return;
}
?>

<!-- fragmento de pantalla inicial -->
<?php
$wcliame          = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame");
$whce             = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce");
$wmovhos          = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos");
$factoresConsulta = consultarfactoresConsulta();

encabezado( " OCUPACION HOSPITALARIA POR SERVICIO Y HABITACION ", $wactualiz, "clinica" );

?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title> Utilización de camas </title>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <style>
    .botona{
            font-size:13px;
            font-family:Verdana,Helvetica;
            font-weight:bold;
            color:white;
            background:#638cb5;
            border:0px;
            height:30px;
            margin-left: 1%;
            cursor: pointer;
         }
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <script type="text/javascript">

        $(document).ready(function(){

            var entidades_array = new Array();
            //Selecciona la variable con las entidades y llena el arreglo para mostrarse en el input de "Entidad"
            var entidadesx = $("#entidades_json").val();
            var datos = eval ( entidadesx );
            for( i in datos ){
                entidades_array.push( datos[i] );
            }
            //Autocompletar para las entidades responsables, cuando seleccione uno llama a buscarcodigoentidad
            $( "#wresponsable" ).autocomplete({
                source: entidades_array,
                minLength : 2
            });
        });

        function filtrarHabitaciones(){
            var cco = $("#wcco").val();
            if( cco != "%" ){
                $("#whab > option[centrocostos!='"+cco+"']").hide();
                $("#whab > option[centrocostos ='"+cco+"']").show();
                $("#whab > option[centrocostos ='%']").show();
                $("#whab > option[centrocostos ='%']").attr("selected", "selected");
            }else{
                $("#whab > option").show();
            }
        }

        function ocultarMostrar( id ){
            $("#"+id).toggle();
        }

        function consultarDatos(){
            var centrocostos = $("#wcco").val();
            var habitacion   = $("#whab").val();
            var wresponsable = $("#wresponsable").val();
            if( $.trim(wresponsable) == "" ){
                wresponsable = "%";
                wresponsable = $("#wresponsable").val(wresponsable);
            }
            $("#msjEspere").show();

            $.ajax({
                    url: "utilizacion_camas.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "consultarDatos",
                            consultaAjax: "si",
                               wemp_pmla: $("#wemp_pmla").val(),
                                 wmovhos: $("#wmovhos").val(),
                                 wcliame: $("#wcliame").val(),
                                    whce: $("#whce").val(),
                                    wcco: centrocostos,
                                    whab: habitacion,
                                    wres: wresponsable

                          },
                    success: function(data)
                    {
                        $("#div_respuesta").html("");
                        $( ".desplegables" ).accordion("destroy");
                        $("#div_respuesta").html(data);
                        $("#buscador").show();
                        $('input#buscador').quicksearch("destroy");
                        $( ".desplegables" ).accordion({
                                collapsible: true,
                                active:0,
                                heightStyle: "content",
                                icons: null
                        });
                         $("#msjEspere").hide();
                        $("#div_respuesta").show();
                        //$('input#buscador').quicksearch("table#tbl_resultados tbody tr");
                        if( $("#whab_dis").is(":checked") ){
                            $("#tbl_resultados tbody tr[disponible='off']").hide();
                        }
                        if( $("#wcco").val() != "%" ){
                            $("#tbl_resultados tbody tr[centrocostos!='"+$("#wcco").val()+"'][resumen!='on']").hide();
                            $("#tbl_resultados tbody tr[centrocostos!='"+$("#wcco").val()+"'][resumen!='on']").attr("oculto","on");
                        }

                        if( $("#wresponsable").val() != "%" ){
                            var responsable = $("#wresponsable").val().split(",");
                            responsable = $.trim( responsable[0] );
                            $("#tbl_resultados tbody tr[responsable!='"+responsable+"'][tipo!='titulo']").hide();
                        }
                        $('input#buscador').quicksearch("table#tbl_resultados tbody tr[tipo!='titulo'][oculto='off']");
                    }
            });
        }

        function mostrarResumen( cco, nombreCco ){
            $("#div_resumen_"+cco).dialog({
                 title: " Estadisticas: "+nombreCco,
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
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
                height    : 300,
                width     : 500,
                rezisable : true
            });
            $(".ui-dialog-titlebar-close").hide();//-->oculto la x de cerrar, por defecto
        }

        function mostrarOcultarHabitaciones(){
            if( !$("#whab_dis").is(":checked") ){
                $("#tbl_resultados tbody tr[oculto='off']").show();
            }else{
                $("#tbl_resultados tbody tr[disponible='off']").hide();
                $("#tbl_resultados tbody tr[hospitalario='off']").hide();
            }
        }

    </script>
</head>
<body>
    <input type='hidden' id='wemp_pmla' value='<?php echo $wemp_pmla; ?>'>
    <input type='hidden' id='wcliame'   value='<?php echo $wcliame; ?>'>
    <input type='hidden' id='whce'      value='<?php echo $whce; ?>'>
    <input type='hidden' id='wmovhos'   value='<?php echo $wmovhos; ?>'>
    <div id='formularios_ppales'>
        <div align='center'><span class='subtituloPagina2'><font size='2' id='receptor_actual'>(Este reporte incluye los servicios de Urgencias, Cirugia y Hospitalización pero no afectan los porcentajes de ocupación hospitalaria)</font></span></div>
        <br><br>
        <center><table>
            <tr class="encabezadoTabla">
                <td align="center" colspan="8"><b>Parámetros de Consulta</b></td>
            </tr>
            <tr class="Fila1">
                <td align='left' width='70px'>
                    <b>Servicio :</b>
                </td>
                <td colspan="7">
                    <!--<select id="wcco" onchange='filtrarHabitaciones();'>-->
                    <select id="wcco">
                        <option selected value='%' >Todos los Servicios </option>
                        <?php
                            foreach( $factoresConsulta['centrosCostos'] as $keyCco => $nombre ) {
                                echo " <option value='$keyCco'>$keyCco - $nombre </option>";
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <!--<tr class="Fila2">
                <td align='left'>
                    <b>Habitacion :</b>
                </td>
                <td colspan="7">
                    <select id="whab">
                        <option selected value='%' centrocostos='%' >Todas los Habitaciones </option>
                        <?php
                            /*foreach( $factoresConsulta['habitaciones'] as $keyHab => $datos ) {
                                echo " <option value='$keyHab' centrocostos='{$datos['cco']}' >$keyHab</option>";
                            }*/
                        ?>
                    </select>
                </td>
            </tr>-->
            <tr class="Fila2">
                <td align='left'>
                    <b>Responsable:</b>
                </td>
                <td colspan="7">
                    <input type='text' placeholder='Digite el nombre de la entidad (% para todos )'  id='wresponsable' style='width:80%;' required='required' value='%' />
                        <?php
                            //Imprime un json en una variable oculta con las entidades
                            $wentid = $factoresConsulta['entidades'];
                            $ent    = json_encode( $wentid );
                        ?>
                    <input type='hidden' id='entidades_json' value='<?php echo $ent; ?>' />
                </td>
            </tr>
            <tr class="Fila2">
                <td align="center" colspan="7">
                    <font class="textoMedio"><b>Mostrar solo habitaciones disponibles</b>
                        <input type="checkbox" id="whab_dis" onclick='mostrarOcultarHabitaciones();'>
                </td>
            </tr>
            <tr class="Fila1">
                    <td align="center" colspan="8">
                        <input type="button" onclick="consultarDatos()" value="CONSULTAR">
                    </td>
                </tr>
        </table></center>
        <br>

        <!--<div align='left'><input type='text' id='buscador'><span class='subtituloPagina2' style='cursor:pointer;' onclick='ocultarMostrar("formulario_paciente")'><font size='2'>Consultar paciente</font></span></div>-->
        <div align='center' id='formulario_paciente' style='display:none;'>
            <center><table>
                <tr class="encabezadoTabla">
                    <td align="center" colspan="8">
                        <b>DATOS DEL PACIENTE</b>
                    </td>
                </tr>
                <tr class="Fila1">
                    <td>
                        <b>Historia : </b>
                            <input type="text" value="" id="whis">
                    </td>
                    <td colspan="2">
                        <b>Identificacion : </b>
                            <input type="text" size="23" value="" id="wced">
                    </td>
                </tr>
                <tr class="Fila2">
                    <td>
                        <b>Primer apellido : </b>
                            <input type="text" size="20" value="" id="wap1">
                    </td>
                    <td>
                        <b>Segundo apellido : </b>
                            <input type="text" size="20" value="" id="wap2">
                    </td>
                    <td>
                        <b>Nombres : </b>
                            <input type="text" size="30" value="" id="wnom">
                    </td>
                </tr>
                <tr class="Fila1">
                    <td align="center" colspan="8">
                        <input type="button" onclick="validar(whis.value)" value="CONSULTAR">
                    </td>
                </tr>
            </table></center>
        </div>
        <center><div id='msjEspere' name='msjEspere' style='display:none;'>
            <br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />
        </div></center>
        <div id='div_respuesta' align='center' style='display:none;'></div>
        <br><br>
        <center><input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()'></center>
        <br><br>
    </div>
</body>
</html>