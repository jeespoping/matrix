<?php
include_once("conex.php");
if(isset($peticionAjax))
{
    if(isset($peticionAjax) && $peticionAjax == 'exportar_excel') // se debe diferenciar por los dos o por otro diferente a $accion puesto que desde talento.php ya esta seteado $accion
    {
        header("Content-type: application/ms-excel; name='excel'");
        header("Content-Disposition: attachment; filename=comprobante_periodico_".$wfechaInicio."_".$wfechaFinal.".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $_POST['datos_a_enviar'];
        return;
    }
}
/**
*       UTILIZACION DE CAMAS POR SERVICIO HABITACION     *
*       PORCENTAJES DE UTILIZACION SERVICIO Y CLINICA    *
*                    CONEX, FREE => OK                   *
*********************************************************/

//==================================================================================================================================================//
// Este programa genera un reporte de ventas similar al comprobante. solo que busca los datos por periodo y no por dia
//=====================================================================================
include_once( "root/comun.php" );
require_once("conex.php");
mysql_select_db( "matrix" );
$fecha_hoy   = date('Y-m-d');
$hora        = date("H:i:s");
$wactualiz   = "2014-10-29";

if( !isset($_SESSION['user']) ){//session muerta en una petición ajax

      echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
             </div>";
      return;
}

if( $peticionAjax == "consultarDatos" ){

    $wfechaInicio                  = $year."-".$mes."-01";
    $wfechaFinal                   = date( "Y-m-d", (strtotime( $wfechaInicio." +2 month ") - 3600) ); //--> fecha inicial + 2 meses menos una hora, para que quede ubicada en el último dia del segundo mes
    $tarifasIva                    = array();
    /*$wfechaInicio                  = "2012-01-01";
    $wfechaFinal                   = "2012-01-01";*/
    $fuentesFacturacion            = array();
    $equivalenciaFormaPagoConcepto = array();
    $conceptosMediosPag            = array();
    /*$conceptosMediosPag            = array(
                                       '1'=> 'Efectivo',
                                       '2'=> 'Tarjeta',
                                       '3'=> 'Cheques',
                                       '4'=> 'Ventas cr&eacute;dito',
                                       '5'=> 'Bonos',
                                       '6'=> 'Vales',
                                       '7'=> 'Otros',
                                     );*/
    $facturasRevisadas             = array();//--> va apilando las facturas cuyos recibos y formas de pago han sido consultados.

    $query = " SELECT Cfpcod codigo, Cfpdes descripcion
                 FROM {$wbasedatos}_000234
                WHERE Cfpest = 'on' ";

    $rs = mysql_query( $query, $conex ) or die( mysql_error()." en query: ".$query );
    while( $row = mysql_fetch_assoc( $rs ) ){
      $conceptosMediosPag[$row['codigo']] = $row['descripcion'];
    }


    //--> se consultan todos los números de conceptos de pago que están asociados a la tabla de formas de pago
    $query = "SELECT Fpacod codigo, Fpanco numeroConcepto
                FROM {$wbasedatos}_000023
                WHERE Fpaest = 'on'";
    $rs    = mysql_query( $query, $conex );
    while( $row = mysql_fetch_array( $rs ) ){
        $equivalenciaFormaPagoConcepto[$row['codigo']] = $row['numeroConcepto'];
    }

    //---> se consultan las diferentes fuentes de ventas asociados a los centros de costos
    $query = "SELECT Ccofnc fuenteNotaCredito, Ccofnd fuenteNotaDebito, Ccofrc fuenteRecibo
                FROM {$wbasedatos}_000003
                WHERE Ccoest = 'on'
                  AND Ccofnc != 'NO APLICA'
                  AND Ccofnd != 'NO APLICA'
                  AND Ccofrc != 'NO APLICA'";
    $rs    = mysql_query( $query, $conex );
    while( $row = mysql_fetch_array( $rs ) ){

        $fuenteNc = "'".$row['fuenteNotaCredito']."'";
        $fuenteNd = "'".$row['fuenteNotaDebito']."'";
        $fuenteRc = "'".$row['fuenteRecibo']."'";
        if(!in_array( $fuenteRc, $fuentesFacturacion ) )
            array_push( $fuentesFacturacion, $fuenteRc );
    }

    $condicionFuentes = implode( ",", $fuentesFacturacion );
    $tmventas        = "tmpVen16".date('His');
    $tmventasDetalle = "tmpDetVen17".date('His');
    //echo $tmventas."  -  ".$tmventasDetalle."<br>";
    $qaux            = "DROP TABLE IF EXISTS $tmventas";
    $resdr           = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
    $qaux            = "DROP TABLE IF EXISTS $tmventasDetalle";
    $resdr           = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());

    //---> creación tabla temporal con los datos de las ventas realizadas en el periodo

    //-----------------------------------------------------------------------QUERY ORIGINAL
    $query = "CREATE TEMPORARY TABLE IF NOT  EXISTS $tmventas "
                            ."(INDEX idx(fuenteFactura, numeroFactura),
                               INDEX idx2( numeroVenta ))
              SELECT Vencco centroCostos, Vencaj caja, Fecha_data fechaVenta, Hora_data horaVenta, Venmes, Venffa fuenteFactura, Vennfa numeroFactura, Venano, Vennum numeroVenta
                FROM ".$wbasedatos."_000016
               WHERE Fecha_data BETWEEN '".$wfechaInicio."' AND '".$wfechaFinal."'
                 AND Venest ='on'
               ORDER BY  1,2,3,4";


    //---> tratar de usar esto para mejoar el rendimiento del reporte, resulta que no todas las ventas tienen recibos
    //----------------------------------------------------------------------- SEGUNDO QUERY
    /*$query = "CREATE TEMPORARY TABLE IF NOT  EXISTS $tmventas "
                            ."(INDEX idx(fuenteFactura, numeroFactura),
                               INDEX idx2( numeroVenta ))
              SELECT Vencco centroCostos, Vencaj caja, a.Fecha_data fechaVenta, a.Hora_data horaVenta, Venmes, Venffa fuenteFactura, Vennfa numeroFactura, Venano, Vennum numeroVenta
                FROM {$wbasedatos}_000016 a
                INNER JOIN
                    {$wbasedatos}_000021 b on ( rdeffa = venffa AND rdefac = Vennfa AND a.Fecha_data BETWEEN '$wfechaInicio' AND '$wfechaFinal' AND venest = 'on' AND rdefue in ($condicionFuentes) )
               ORDER BY  1,2,3,4";
    */


    //----------------------------------------------------------------------- TERCERO QUERY
    /*$query = "CREATE TEMPORARY TABLE IF NOT  EXISTS $tmventas "
                            ."(INDEX idx(fuenteFactura, numeroFactura),
                               INDEX idx2( numeroVenta ))
              SELECT Vencco centroCostos, Vencaj caja, a.Fecha_data fechaVenta, a.Hora_data horaVenta, Venmes, Venffa fuenteFactura, Vennfa numeroFactura,  Venano, Vennum numeroVenta
               FROM {$wbasedatos}_000016 a
               INNER JOIN
                    {$wbasedatos}_000021 b on ( rdeffa = venffa AND rdefac = Vennfa AND a.Fecha_data BETWEEN '$wfechaInicio' AND '$wfechaFinal' AND venest = 'on' AND rdefue in ($condicionFuentes) AND rdecco = vencco)
               INNER JOIN
                    {$wbasedatos}_000022 c on ( Rfpfue = rdefue AND rfpnum = rdenum  AND rfpcco = rdecco )
               GROUP BY 1,2,3,4,5,6,7,8,9
               ORDER BY  1,2,3,4";*/


    //--> tabla temporal con el detalle de las ventas realizadas en el periodo y sus articulos especifican si es excento o excluido( maestro de articulos, tabla 1)/
    $rs    = mysql_query( $query, $conex ) or die( mysql_error()." <br> ".$query );


    /* para corregir
    select Sum( Vdevun* Vdecan ) valorConIva
 from farpmla_000016
 inner join
        farpmla_000017 on (vdenum = vennum )
 where vennfa in ( 'A-150926','A-150981','A-150985','A-150995' )
    **/

    $query = "CREATE TEMPORARY TABLE IF NOT  EXISTS $tmventasDetalle "
                            ."(INDEX idx2( numeroVenta ))
              SELECT Vdenum numeroVenta, Vdepiv tasaIva, Arttiv tipoIva, Sum( Vdevun* Vdecan ) valorConIva, (SUM(Vdecan*Vdevun/(Vdepiv+100))*100) valorSinIva, SUM(Vdedes) descuentoSinIva, SUM(Vdedes*Vdepiv/100) descuentoConIva,
                     count(distinct(Vdeart)) cantidadArticulos
                FROM {$wbasedatos}_000017 a
               INNER JOIN
                     {$wbasedatos}_000001 b on ( Vdeart = Artcod and Artest = 'on' )
               WHERE a.Fecha_data BETWEEN '{$wfechaInicio}' AND '{$wfechaFinal}'
                 AND Vdeest ='on'
               GROUP BY 1,2,3";
    $rs    = mysql_query( $query, $conex ) or die( mysql_error()." <br> ".$query );

    $query = "SELECT centroCostos, caja, fechaVenta, horaVenta, Venmes, fuenteFactura, numeroFactura,
                     Venano, a.numeroVenta, tasaIva, tipoIva, valorConIva, valorSinIva, cantidadArticulos, descuentoSinIva, descuentoConIva
                FROM {$tmventas} a
               INNER JOIN
                     {$tmventasDetalle} b ON ( a.numeroVenta = b.numeroVenta )
               GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16
               ORDER BY 1,2,3,4,5";
    $rs    = mysql_query( $query, $conex ) or die( mysql_error() );
    $num   = mysql_num_rows( $rs );
    /*echo "<pre>";
        print_r( $equivalenciaFormaPagoConcepto );
    echo "</pre>";*/
    $id = "";
    while( $row = mysql_fetch_assoc( $rs ) ){

        $aux = explode("-", $row['numeroFactura']);
        $aux2 = $row['fuenteFactura'];
        $entro = false;//--> variable que indica si hay o no recibos y formas de pago, si no hay es porque se hizo a crédito
        if( !in_array( $row['tasaIva'], $tarifasIva ) ){
            array_push( $tarifasIva, $row['tasaIva'] );
        }
        if( !isset( $arregloVentas[$row['centroCostos']][$row['caja']] ) ){

            $arregloVentas[$row['centroCostos']][$row['caja']]['prefijo']                = $aux2;
            $arregloVentas[$row['centroCostos']][$row['caja']]['prefijoActual']          = $aux2;
            $arregloVentas[$row['centroCostos']][$row['caja']]['excentas']               = 0;
            $arregloVentas[$row['centroCostos']][$row['caja']]['excluidas']              = 0;
            $arregloVentas[$row['centroCostos']][$row['caja']]['Gravadas']               = 0;
            $arregloVentas[$row['centroCostos']][$row['caja']]['FacturaInicial']         = $row['numeroFactura'];
            $arregloVentas[$row['centroCostos']][$row['caja']]['tasas'][$row['tasaIva']]['conIva'] = 0;
            $arregloVentas[$row['centroCostos']][$row['caja']]['tasas'][$row['tasaIva']]['sinIva'] = 0;
            $arregloVentas[$row['centroCostos']][$row['caja']]['descuentoConIva']        = 0;

            foreach ($conceptosMediosPag as $keyConcepto => $value) {
                $arregloVentas[$row['centroCostos']][$row['caja']]['conceptoFormaPago-'.$keyConcepto]['movimientos'] = 0;
                $arregloVentas[$row['centroCostos']][$row['caja']]['conceptoFormaPago-'.$keyConcepto]['total']       = 0;
            }

        }else{

            if( $aux2 != $arregloVentas[$row['centroCostos']][$row['caja']]['prefijoActual']){
                $arregloVentas[$row['centroCostos']][$row['caja']]['prefijo']       .= ",".$aux2;
                $arregloVentas[$row['centroCostos']][$row['caja']]['prefijoActual']  = $aux2;
            }

        }

        switch ($row['tipoIva']) {
            case 'E-EXENTO':
                $arregloVentas[$row['centroCostos']][$row['caja']]['excentas'] += $row['valorConIva'];
                break;
            case 'X-EXCLUIDO':
                $arregloVentas[$row['centroCostos']][$row['caja']]['excluidas'] += $row['valorConIva'];
                break;
            default:
                # code...
                break;
        }

        ///para mejorar el script podría tratar de apilar los ids de las formas de pago, en lugar de las facturas
        if( !isset( $facturasRevisadas[$row['centroCostos'].$row['caja']] ) or (!in_array($row['fuenteFactura'].$row['numeroFactura'], $facturasRevisadas[$row['centroCostos'].$row['caja']] ) ) ){
            if( !isset($facturasRevisadas[$row['centroCostos'].$row['caja']] ) )
                $facturasRevisadas[$row['centroCostos'].$row['caja']] = array();
            //--> por cada factura, se miran los recibos y sus respectivas formas de pago, para sumar la cantidad de casos y el total de ventas representadas en cada una de las formas de pago
            $query21 = " SELECT Rfpfpa, Rfpvfp, c.id
                           FROM
                                {$wbasedatos}_000021
                          INNER JOIN
                                {$wbasedatos}_000022 c on (  rdeffa = '{$row['fuenteFactura']}' AND rdefac = '{$row['numeroFactura']}' AND rdecco = '{$row['centroCostos']}' AND rdefue in ($condicionFuentes)
                                                         AND Rfpfue = rdefue AND rfpnum = rdenum and rfpcco = rdecco AND rdeest = 'on' AND rfpest = 'on' )";
            $rs21    = mysql_query( $query21, $conex ) or die( $query21 );

            while( $row21 = mysql_fetch_assoc($rs21) ){
                $entro = true;
                if( !isset($equivalenciaFormaPagoConcepto[$row21['Rfpfpa']]) ){//--> si hay recibos pero no tienen un numero de concepto asociado como abono, tarjeta, etc.... se suma, en otros conceptos de pago
                    $equivalenciaFormaPagoConcepto[$row21['Rfpfpa']] = '7';
                }
                $arregloVentas[$row['centroCostos']][$row['caja']]['conceptoFormaPago-'.$equivalenciaFormaPagoConcepto[$row21['Rfpfpa']]]['movimientos']++;
                $arregloVentas[$row['centroCostos']][$row['caja']]['conceptoFormaPago-'.$equivalenciaFormaPagoConcepto[$row21['Rfpfpa']]]['total'] += $row21['Rfpvfp'];
            }

            if( !$entro ){//-->si se hizo a crédito se suma acá
                $arregloVentas[$row['centroCostos']][$row['caja']]['conceptoFormaPago-4']['movimientos']++;
                $arregloVentas[$row['centroCostos']][$row['caja']]['conceptoFormaPago-4']['total'] += $row['valorConIva'];
            }else{
              array_push( $facturasRevisadas[$row['centroCostos'].$row['caja']], $row['fuenteFactura'].$row['numeroFactura']);
            }
        }
        $arregloVentas[$row['centroCostos']][$row['caja']]['FacturaFinal']                      = $row['numeroFactura'];
        $arregloVentas[$row['centroCostos']][$row['caja']]['tasas'][$row['tasaIva']]['conIva'] += $row['valorConIva'];
        $arregloVentas[$row['centroCostos']][$row['caja']]['tasas'][$row['tasaIva']]['sinIva'] += $row['valorSinIva'];
        $arregloVentas[$row['centroCostos']][$row['caja']]['descuentoConIva']                  += $row['descuentoConIva'];
        $arregloVentas[$row['centroCostos']][$row['caja']]['ivaDescuento']                     += ( $row['descuentoSinIva'] - $row['descuentoConIva']);
    }

    //echo $id;
    array_multisort($tarifasIva);
    /*echo "<pre>";
        print_r($arregloVentas);
    echo "</pre>";*/

    if( $num > 0 ){
        $resultado .=  "<div align='left' style='text-align:left;'>
                            <span style='color:#999999;font-size:14pt;'>Resultado de la consulta:</span>
                            <div id='div_exportar' style='display:block;text-align:left;'>
                                <form action='rep_comprobante_periodo.php?form=&peticionAjax=exportar_excel&wfechaInicio=$wfechaInicio&wfechaFinal=$wfechaFinal' method='post' target='_blank' id='FormularioExportacion'>
                                    <span style='color:#999999;'>Exportar</span>  <img width='28' height='14' border='0' src='../../images/medical/root/export_to_excel.gif' class='botonExcel' style='cursor:pointer;' />
                                    <input type='hidden' id='datos_a_enviar' name='datos_a_enviar' />
                                </form>

                            </div>
                        </div>";
        $resultado .= "<div align='left'><span class='subtituloPagina2'><font size='2'> Datos incluidos: $wfechaInicio hasta $wfechaFinal</font></span></div>";
        $resultado .= "<table id='tbl_resultados'>";
        $resultado .= "<tr class='encabezadotabla'>";
            $resultado .= "<td> ID. Computador </td>";
            $resultado .= "<td> Prefijo </td>";
            $resultado .= "<td> Factura<br>Inicial </td>";
            $resultado .= "<td> Factura<br>Final </td>";
            $resultado .= "<td> Ventas exentas </td>";
            $resultado .= "<td> Ventas excluidas </td>";
            foreach ($tarifasIva as $i => $tasa ) {
                //( $tasa*1 == 0 ) ? $resultado .= "<td> Ventas excluidas </td>" : $resultado .= "<td> Ventas <br> Gravadas {$tasa}% </td><td> iva {$tasa}% </td>";
                $resultado .= "<td> Ventas <br> Gravadas {$tasa}% </td><td> iva {$tasa}% </td>";
            }
            $resultado .= "<td align='center'> Descuentos </td>";
            $resultado .= "<td align='center'> iva Descuentos </td>";
            foreach ($conceptosMediosPag as $keyConcepto => $nombre ) {
                //( $tasa*1 == 0 ) ? $resultado .= "<td> Ventas excluidas </td>" : $resultado .= "<td> Ventas <br> Gravadas {$tasa}% </td><td> iva {$tasa}% </td>";
                $resultado .= "<td> Forma de pago: <br> {$nombre} </td><td> TOTAL {$nombre} </td>";
            }
        $resultado .= "</tr>";
    }else{
      echo "<br /><br /><br /><br />
              <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                  [?] No hay ventas Registadas en el periodo de tiempo seleccionado.
             </div>";
      return;
    }
    $i = 0;
    foreach ( $arregloVentas as $keyCco => $cajas ){
        foreach ($cajas as $keyCaja => $registro ) {
            $i++;
            ( is_int( $i/2 ) ) ? $wclass = "fila1" : $wclass = "fila2";
            $resultado .= "<tr class='$wclass'>";
                $resultado .= "<td align='left'> $keyCco-$keyCaja </td>";
                $resultado .= "<td align='center'> {$registro['prefijo']} </td>";
                $resultado .= "<td align='center'> {$registro['FacturaInicial']} </td>";
                $resultado .= "<td align='center'> {$registro['FacturaFinal']} </td>";
                $resultado .= "<td align='right'> ".number_format( $registro['excentas'], 0, '.', '.' )." </td>";
                $resultado .= "<td align='right'> ".number_format( $registro['excluidas'], 0, '.', '.' )." </td>";
                foreach ($tarifasIva as $j => $tasa ) {
                   /* if( $tasa*1 == 0)
                        ( isset( $registro['tasas'][$tasa]) ) ? $resultado .= "<td align='right'> ".number_format( $registro['tasas'][$tasa], 0, '.', '.' )." </td>" : $resultado .= "<td align='right'> 0 </td>";
                    else*/
                    if( $tasa*1 == 0 ){
                        $registro['tasas'][$tasa]['conIva'] = $registro['tasas'][$tasa]['conIva'] - $registro['excentas'] - $registro['excluidas'];
                        $registro['tasas'][$tasa]['sinIva'] = $registro['tasas'][$tasa]['sinIva'] - $registro['excentas'] - $registro['excluidas'];
                    }
                        //( isset( $registro['tasas'][$tasa]) ) ? $resultado .= "<td align='right'> ".number_format( $registro['tasas'][$tasa], 0, '.', '.' )." </td><td align='right'> ".number_format( ($registro['tasas'][$tasa]) - ( ($registro['tasas'][$tasa])/(1+($tasa/100))), 0, '.', '.' )." </td>" : $resultado .= "<td align='right'> 0 </td><td align='right'> 0 </td>";
                        ( isset( $registro['tasas'][$tasa]) ) ? $resultado .= "<td align='right'> ".number_format( $registro['tasas'][$tasa]['conIva'], 0, '.', '.' )." </td><td align='right'> ".number_format( ($registro['tasas'][$tasa]['conIva'] - $registro['tasas'][$tasa]['sinIva']), 0, '.', '.' )." </td>" : $resultado .= "<td align='right'> 0 </td><td align='right'> 0 </td>";
                }
                $resultado .= "<td align='center'> ".round( $registro['descuentoConIva'] )."</td>";
                $resultado .= "<td align='center'> {$registro['ivaDescuento']} </td>";
                foreach ($conceptosMediosPag as $keyConcepto => $nombre ) {
                    ( isset( $registro['conceptoFormaPago-'.$keyConcepto]) ) ? $resultado .= "<td align='center'> ".$registro['conceptoFormaPago-'.$keyConcepto]['movimientos']." </td><td align='right'> ".number_format( ($registro['conceptoFormaPago-'.$keyConcepto]['total']), 0, '.', '.' )." </td>" : $resultado .= "<td align='right'> 0 </td><td align='right'> 0 </td>";
                }
            $resultado .= "</tr>";
        }
    }
    if( $num > 0 ){
        $resultado .= "</table>";
    }
    echo $resultado;

    return;
}


$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedatos  = strtolower($institucion->baseDeDatos);

?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title>  REPORTE DE VENTAS POR PERIODO </title>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <script>
        function generarReporte(){
                $("#msjEspere").toggle();
                $.ajax({
                    url: "rep_comprobante_periodo.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "consultarDatos",
                            consultaAjax: "si",
                               wemp_pmla: $("#wemp_pmla").val(),
                                    year: $("#wanho").val(),
                                     mes: $("#wmes").val(),
                              wbasedatos: $("#wbasedatos").val(),
                                 wcliame: $("#wcliame").val()

                          },
                    success: function(data)
                    {
                        $("#div_respuesta").html(data);
                        configurarBotonExportar();
                        $("#msjEspere").toggle();
                    }
            });
        }

        function configurarBotonExportar()//FUNCION QUE PERMITE QUE LA TABLA HTML GENERADA SE PUEDA EXPORTAR
        {
            /**
                Inicializa la funcionalidad para generar la exportación a excel.
            */
            $(".botonExcel").click(function(event) {
                var tabla = $("#tbl_resultados").eq(0).clone();
                $("#datos_a_enviar").val( $("<div>").append( tabla ).html());
                $("#FormularioExportacion").submit();
            });
        }
    </script>
</head>
<body>
    <?php encabezado( " COMPROBANTE DE VENTAS BIMESTRAL ", $wactualiz, "logo_".$wbasedatos ); ?>
    <input type='hidden' id='wemp_pmla' value='<?php echo $wemp_pmla; ?>'>
    <input type='hidden' id='wbasedatos' value='<?php echo $wbasedatos; ?>'>
    <div align='center'><span class='subtituloPagina2'><font size='2'>El programa, realiza la consulta desde el primer dia del mes elegido, hasta el último dia del mes siguiente,<br> garantizando asi que se realice la consulta de forma bimestral</font></span></div>
    <div id='div_formulario_ppal' align='center'>
        <table>
            <tr class='encabezadotabla'><td colspan='4'> SELECCIONE DATOS DE INICIO DEL REPORTE </td></tr>
            <tr class='fila2'>
                <td> Año: </td>
                <td>
                    <select id='wanho'>
                        <?php
                            $anhoActual = date('Y');
                            for( $i = $anhoActual; $i > ( $anhoActual - 3 ); $i-- ){
                                ( $i == $anhoActual ) ? $selected = " selected " : $selected = "";
                                ?>
                                    <option <?php echo $selected; ?> value='<?php echo $i?>'><?php echo $i; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </td>
                <td> Mes: </td>
                <td>
                    <select id='wmes'>
                        <?php
                            for( $i = 1; $i <= 12 ; $i++ ){
                                ( $i == 1 ) ? $selected = " selected " : $selected = "";
                                ( $i < 10 ) ? $j = "0".$i : $j = $i;
                                ?>
                                    <option <?php echo $selected; ?> value='<?php echo $j?>'><?php echo $j; ?></option>
                                <?php
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr><td colspan='4' align='center'><input type='button' value='Generar' onclick='generarReporte();'></td></tr>
        </table>
    </div>
    <div id='div_respuesta' align='center'>
    </div>
    <center><div id='msjEspere' style='display:none;'>
        <br>
        <img src='../../images/medical/ajax-loader5.gif'/>
        <br><br> Por favor espere un momento ... <br><br>
    </div></center>
</body>
</html>
