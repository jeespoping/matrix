<?php
include_once("conex.php");
if(!isset($_SESSION['user'])){
  echo "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
        [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
        </div>";
  return;
}
include_once( "root/comun.php" );
require_once("conex.php");
mysql_select_db( "matrix" );
$fecha_hoy   = date('Y-m-d');
$hora        = date("H:i:s");
$wactualiz   = "2014-09-04";

if( $peticionAjax == "generarReporte" ){

    $nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
    $tablaConceptos   = $wbasedatos.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
    $wfue             = $_REQUEST['wfuente'];
    $wfac             = $_REQUEST['wfactura'];
    $arregloResumen   = array();
    $arregloDetalle   = array();
    $arregloPaquetes  = array();

    //encabezado de la factura.
    $q = " SELECT fenffa, fenfac, fenfec, fencod, fennit, empnom, fenval, fenviv, fencop, fencmo, fendes, fenabo, fenvnd, fenvnc, fensal, fenesf, "
     ."           fenhis, fening, fencre, fenpde, fenrec, fentop, fenrln, fenest, fennpa, cfgnom, fentip, fendpa "
     ."      FROM ".$wbasedatos."_000018, ".$wbasedatos."_000024, ".$wbasedatos."_000049 " //.$wbasedatos."_000100 "
     ."     WHERE fenffa like '".$wfue."'"
     ."       AND fenfac like '".$wfac."'"
     ."       AND fencod = empcod "
     ."       AND fencco = cfgcco ";
     $res  = mysql_query($q,$conex);
     $num  = mysql_num_rows($res);
     $row  = mysql_fetch_array($res);
     $wfue = $row[0];
     $wfac = $row[1];
     $wfec = $row[2];
     $wcem = $row[3];
     $wnit = $row[4];
     $wnem = $row[5];
     $wval = $row[6];
     $wiva = $row[7];
     $wcop = $row[8];
     $wcmo = $row[9];
     $wdes = $row[10];
     $wabo = $row[11];
     $wvnd = $row[12];
     $wvnc = $row[13];
     $wsal = $row[14];
     $wesf = $row[15];
     $whis = $row[16];
     $wing = $row[17];
     $wcre = $row[18];
     $wpde = $row[19];
     $wrec = $row[20];
     $wtop = $row[21];
     $wrln = $row[22];
     $west = $row[23];
     $wnpa = $row[24];
     $wins = $row[25];  //Institucion o clinica
     $wtfa = $row[26];  //Tipo de factura
     $wdoc = $row[27];  //Identificacion del paciente

     $encabezadoFactura  = "<br>";
     $encabezadoFactura .= "<center><table border=0>";
     $encabezadoFactura .= "<tr>";
     $encabezadoFactura .= "<td align=left class='fila2' colspan=1><b>FACTURACION</td>";
     $encabezadoFactura .= "<td align=center class='fila2' colspan=1><b>".$wins."</b></td>";
     $encabezadoFactura .= "<td align=right class='fila2' colspan=1><b> Fecha:".$fecha_hoy."</b></td>";
     $encabezadoFactura .= "</tr>";

     $encabezadoFactura .= "<tr>";
     $encabezadoFactura .= "<td align=left class='fila2' colspan=1>&nbsp;<b>rep_resumido_cargos.php</b>&nbsp;</td>";
     $encabezadoFactura .= "<td align=center class='fila2' colspan=1>&nbsp;<b>DETALLE DE CARGOS POR FACTURA RESUMIDO</b>&nbsp;</td>";
     $encabezadoFactura .= "<td align=right class='fila2' colspan=1>&nbsp;<b>Hora:".$hora."</b>&nbsp;</td>";
     $encabezadoFactura .= "</tr>";

     //ACA PINTO EL ENCABEZADO DE LA FACTURA
     $encabezadoFactura .= "<tr>";
     $encabezadoFactura .= "<td align=center colspan=3 class='fila2' colspan=1> Fuente: &nbsp&nbsp <b>".$wfue."</b> &nbsp&nbsp Factura: &nbsp&nbsp <b>".$wfac."</b> &nbsp&nbsp Fecha: &nbsp&nbsp <b>".$wfec."</b></td>";
     $encabezadoFactura .= "</tr>";
     $encabezadoFactura .= "<tr>";
     $encabezadoFactura .= "<td align=center colspan=3 class='fila2' colspan=1> Paciente:<b>&nbsp;&nbsp;".$wnpa."</b> &nbsp&nbsp Historia: &nbsp&nbsp<b>".$whis."-".$wing."</b> &nbsp&nbsp Identificación: &nbsp&nbsp<b>".$wdoc."</b></td>";
     $encabezadoFactura .= "</tr>";
     $encabezadoFactura .= "<tr>";
     $encabezadoFactura .= "</table><br>";

     echo $encabezadoFactura;

    //generación del reporte
    //Busco si la empresa actual factura Hospitalario o No
    $q = " SELECT emphos "
        ."   FROM root_000050"
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' ";

    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);
    $row = mysql_fetch_array($res);

    if($row[0] == "on"){//Facturacion Hospitalaria
         $q = "  SELECT rcfreg, tcarconcod, grudes, tcarfec, tcarprocod, pronom, tcartercod, tcarcan, tcarvun, tcarvto, tcarfex, tcarfre, rcfval, tcarusu  "
             ."    FROM ".$wbasedatos."_000106, ".$wbasedatos."_000066, ".$tablaConceptos.", ".$wbasedatos."_000103 "
             ."   WHERE rcfffa      = '".$wfue."'"
             ."     AND rcffac      = '".$wfac."'"
             ."     AND rcfreg      = ".$wbasedatos."_000106.id "
             ."     AND tcarconcod  = grucod "
             ."     AND grutab     != 'on' "
             ."     AND tcarprocod  = procod "
             ."     AND tcarprocod  = procod "
             ."     AND proest = 'on' "
             ." UNION "

             ."  SELECT rcfreg, tcarconcod, grudes, tcarfec, tcarprocod, artnom, tcartercod, tcarcan, tcarvun, tcarvto, tcarfex, tcarfre, rcfval, tcarusu  "
             ."    FROM ".$wbasedatos."_000106, ".$wbasedatos."_000066, ".$tablaConceptos.", ".$wbasedatos."_000001 "
             ."   WHERE rcfffa      = '".$wfue."'"
             ."     AND rcffac      = '".$wfac."'"
             ."     AND rcfreg      = ".$wbasedatos."_000106.id "
             ."     AND tcarconcod  = grucod "
             ."     AND grutab     != 'on' "
             ."     AND tcarprocod  = artcod "
             ."     AND artest = 'on' "
             ."   ORDER BY 2, 4 ";
         $res1 = mysql_query($q,$conex);
         $num1 = mysql_num_rows($res1);
    }else{ //Facturacion POS
       // Nota: Aqui el uso de la tabla 4 es para manejo de grupos de inventario, no como conceptos de facturacion. 2013-12-24, Jerson trujillo.
       $q = "  SELECT rcfreg, artgru, grudes, ".$wbasedatos."_000017.fecha_data, artcod, artnom, '', vdecan, vdevun, vdecan*vdevun, 0, 0, rcfval, ".$wbasedatos."_000017.seguridad  "
           ."    FROM ".$wbasedatos."_000017, ".$wbasedatos."_000066, ".$wbasedatos."_000004, ".$wbasedatos."_000001 "
           ."   WHERE rcfffa      = '".$wfue."'"
           ."     AND rcffac      = '".$wfac."'"
           ."     AND rcfreg      = ".$wbasedatos."_000017.id "
           ."     AND vdeart      = artcod "
           ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
           ."     AND grutab     != 'on' ";
       $res1 = mysql_query($q,$conex);
       $num1 = mysql_num_rows($res1);
    }

    while( $row1 = mysql_fetch_array( $res1 ) ){

        //===========================================================
        $wtiene_paq="off";
        // Aca busco si la factura corresponde a un paquete
        //==========================================================

        $q = " SELECT movpaqcod, paqnom, sum(rcfval) "
             ."  FROM ".$wbasedatos."_000066, ".$wbasedatos."_000115, ".$wbasedatos."_000018, ".$wbasedatos."_000113 "
             ." WHERE rcfffa    = '".$wfue."'"
             ."   AND rcffac    = '".$wfac."'"
             ."   AND rcfreg    = movpaqreg "
             ."   AND rcfffa    = fenffa "
             ."   AND rcffac    = fenfac "
             ."   AND paqcod    = movpaqcod "
             ."   AND movpaqest = 'on' "
             ." GROUP BY 1, 2 ";
         $res_paq = mysql_query($q,$conex);
         $num_paq = mysql_num_rows($res_paq);

         if($num_paq > 0){
             $wtiene_paq="on";
             for($i=1;$i<=$num_paq;$i++){

                 $row_paq = mysql_fetch_array($res_paq);
                 $wcodpaq[$i]=$row_paq[0];
                 $wnompaq[$i]=$row_paq[1];
                 $wvalpaq[$i]=$row_paq[2];

             }
         }

         if ($wtiene_paq == "off"){

            //Aca busco si el procedimiento y la empresa tienen algun registro en la tabla relacion procedimeintos-empresas
            //e imprimo con el codigo de la empresa.
            $q = " SELECT proemppro, proempnom "
                ."   FROM ".$wbasedatos."_000070 "
                ."  WHERE proempcod = '".$row1[4]."'"
                ."    AND proempemp = '".$wcem."'"
                ."    AND proempest = 'on' ";

            $res2 = mysql_query($q,$conex) or die(mysql_error());
            $num2 = mysql_num_rows($res2);

            if ($num2 > 0){
                $row2 = mysql_fetch_array($res2);
                $wcodpro = $row2[0];
                $wnompro = $row2[1];
            }else{
                $wcodpro = $row1[4];
                $wnompro = $row1[5];
            }
            ( $row1[9] < 0 ) ? $wfactor = -1 : $wfactor = 1;
            //if( $row1[9]*1 > 0 ){
                $conceptos[$row1[1]] = $row1[2];
                $arregloResumen[$row1[1]][$wcodpro]['nombre']          = $wnompro;
                $arregloResumen[$row1[1]][$wcodpro]['cantidad']        = $arregloResumen[$row1[1]][$wcodpro]['cantidad']   + ($row1[7]*$wfactor);
                $arregloResumen[$row1[1]][$wcodpro]['valorTotal']      = $arregloResumen[$row1[1]][$wcodpro]['valorTotal'] + $row1[9];
                $arregloResumen[$row1[1]][$wcodpro]['valorReconocido'] = $arregloResumen[$row1[1]][$wcodpro]['valorReconocido'] + $row1[12];
                if( !isset($arregloResumen[$row1[1]][$wcodpro]['registros']) )
                     $arregloResumen[$row1[1]][$wcodpro]['registros'] = array();
                array_push($arregloResumen[$row1[1]][$wcodpro]['registros'], $row1[0]);

                              /*concepto*/           /*reg*/
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['fecha']          = $row1[3];
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['procedimiento']  = $wcodpro." - ".$wnompro;
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['tercero']        = $row1[6];
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['cantidad']       = number_format($row1[7],2,'.',',');
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['valorUnitario']  = number_format($row1[8],0,'.',',');
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['valorTotal']     = number_format($row1[9],0,'.',',');
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['valorFacturado'] = number_format($row1[12],0,'.',',');
                if ($wtfa != "01-PARTICULAR")
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['valorExcedente']    = number_format(($row1[9]-$row1[12]),0,'.',',');
                $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['usuarioGraba']   = $row1[13];
             //}
         }else{// si es un paquete entra por acá
                //2007-11-27
                // para traer los conceptos que no pertenecen al paquete pero que estan en la factura
                $q = " SELECT distinct(Tcarconcod), Grudes, fdeter, fdepte, fdevco, grutip"
                  ."     FROM ".$wbasedatos."_000066, ".$wbasedatos."_000106, ".$tablaConceptos.", ".$wbasedatos."_000065"
                  ."    WHERE rcfffa    = '".$wfue."'"
                  ."      AND rcffac    = '".$wfac."'"
                  ."      AND fdefue = rcfffa"
                  ."      AND fdedoc = rcffac"
                  ."      AND grucod = fdecon"
                  ."      AND ".$wbasedatos."_000106.id =Rcfreg"
                  ."      AND Tcarconcod = Grucod"
                  ."      and  Tcartfa !='PAQUETE'"
                  ."      and  Tcartfa !='ABONO'"
                  ."      and  Tcarfac ='S' "
                  ."      AND Tcarest = 'on'"
                  ."      AND Rcfest ='on'"
                  ."      AND Fdeest ='on'"
                  ."      AND Gruabo='off'"
                  ."      AND Rcfreg  not in (SELECT Movpaqreg from ".$wbasedatos."_000115 where Movpaqhis=".$whis." and Movpaqing=".$wing." and  Movpaqcod='".$row_paq[0]."' and Movpaqest='on')";
                $res_reg = mysql_query($q,$conex);
                $num_reg = mysql_num_rows($res_reg);

                while( $row_reg = mysql_fetch_array($res_reg) ){

                     $q1 = " SELECT rcfreg, tcarconcod, grudes, tcarfec, tcarprocod, tcarpronom, tcartercod, tcarcan, tcarvun, tcarvto, tcarfex, tcarfre, rcfval, tcarusu  "
                         ."    FROM ".$wbasedatos."_000106, ".$tablaConceptos.", ".$wbasedatos."_000066"
                         ."   WHERE tcarhis     = '".$whis."'"
                         ."     AND tcaring     = '".$wing."'"
                         ."     AND Tcarconcod  = '".$row_reg[0]."'"
                         ."     AND Tcartfa !='PAQUETE'"
                         ."     AND tcarfac = 'S' "
                         ."     AND tcarfre != 0 " // el query estaba < 0, debido a que era una devolucion, tenia que tenerla en cuenta 2010-12-23 (CS-161779)
                         ."     AND tcarconcod  = grucod "
                         ."     AND Tcarest = 'on'"
                         ."     AND rcfreg      = ".$wbasedatos."_000106.id "
                         ."     AND rcffac      = '".$wfac."'" // se anexo esto porque estaba trayento todos los conceptos del ingreso 2010-12-21
                         ."   GROUP BY 1"
                         ."   ORDER BY 2, 4 ";
                     $res1 = mysql_query($q1,$conex);
                     $num1 = mysql_num_rows($res1);

                     while ( $row1 = mysql_fetch_array($res1) ) {
                         //Aca busco si el procedimiento y la empresa tienen algun registro en la tabla relacion procedimeintos-empresas
                         //e imprimo con el codigo de la empresa.
                         $q = " SELECT proemppro, proempnom "
                             ."   FROM ".$wbasedatos."_000070 "
                             ."  WHERE proempcod = '".$row1[4]."'"
                             ."    AND proempemp = '".$wcem."'"
                             ."    AND proempest = 'on' ";
                         $res2 = mysql_query($q,$conex);
                         $num2 = mysql_num_rows($res2);

                         if($num2 > 0){
                             $row2 = mysql_fetch_array($res2);
                             $wcodpro = $row2[0];
                             $wnompro = $row2[1];
                         }else{
                             $wcodpro = $row1[4];
                             $wnompro = $row1[5];
                         }

                         ( $row1[9] < 0 ) ? $wfactor = -1 : $wfactor = 1;
                         //if( $row1[9]*1 > 0 ){
                             $conceptos[$row1[1]] = $row1[2];
                             $arregloResumen[$row1[1]][$wcodpro]['nombre']          = $wnompro;
                             $arregloResumen[$row1[1]][$wcodpro]['cantidad']        = $arregloResumen[$row1[1]][$wcodpro]['cantidad']   + ($row1[7]*$wfactor);
                             $arregloResumen[$row1[1]][$wcodpro]['valorTotal']      = $arregloResumen[$row1[1]][$wcodpro]['valorTotal'] + $row1[9];
                             $arregloResumen[$row1[1]][$wcodpro]['valorReconocido'] = $arregloResumen[$row1[1]][$wcodpro]['valorReconocido'] + $row1[12];
                             if( !isset($arregloResumen[$row1[1]][$wcodpro]['registros']) )
                                 $arregloResumen[$row1[1]][$wcodpro]['registros'] = array();
                             array_push($arregloResumen[$row1[1]][$wcodpro]['registros'], $row1[0]);

                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['fecha']          = $row1[3];
                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['procedimiento']  = $wcodpro." - ".$wnompro;
                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['tercero']        = $row1[6];
                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['cantidad']       = number_format($row1[7],2,'.',',');
                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['valorUnitario']  = number_format($row1[8],0,'.',',');
                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['valorTotal']     = number_format($row1[9],0,'.',',');
                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['valorFacturado'] = number_format($row1[12],0,'.',',');
                             if ($wtfa != "01-PARTICULAR")
                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['valorExcedente']    = number_format(($row1[9]-$row1[12]),0,'.',',');
                             $arregloDetalle[$row1[1]]['cargos'][$row1[0]]['usuarioGraba']   = $row1[13];
                         //}

                     }

                     for ($i=1;$i<=$num_paq;$i++){
                        $qcan = "  SELECT Tcarconcod, SUM(Tcarcan) AS cant  "
                                ."    FROM ".$wbasedatos."_000106 a, ".$wbasedatos."_000066 b "
                                ."   WHERE tcarhis = '".$whis."'"
                                ."     AND tcaring = '".$wing."'"
                                ."     AND Tcarprocod = '".$wcodpaq[$i]."' "
                                ."     AND Tcartfa = 'PAQUETE'"
                                ."     AND tcarfac = 'S' "
                                ."     AND tcarfre != 0 " // el query estaba < 0, debido a que era una devolucion, tenia que tenerla en cuenta 2010-12-23 (CS-161779)
                                ."     AND Tcarest = 'on'"
                                ."     AND a.id = Rcfreg"
                                ."     AND Rcffac = '".$wfac."'"
                                ."     AND Rcfffa = '".$wfue."'"
                                ."   GROUP BY Tcarconcod "
                                ."   ORDER BY cant DESC ";
                        $rescan = mysql_query($qcan,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcan." - ".mysql_error());;
                        $rowcan = mysql_fetch_array($rescan);

                        $cantidad = $rowcan['cant'];

                        if(!isset($cantidad) || !$cantidad || $cantidad=="" || $cantidad==0)
                              $cantidad = 1;
                        $valor_unitario = $wvalpaq[$i] / $cantidad;

                        $arregloPaquetes[$wcodpaq[$i]]['nombre']        = $wnompaq[$i];
                        $arregloPaquetes[$wcodpaq[$i]]['cantidad']      = $cantidad;
                        $arregloPaquetes[$wcodpaq[$i]]['valorUnitario'] = number_format($valor_unitario,0,'.',',');
                        $arregloPaquetes[$wcodpaq[$i]]['valor']         = number_format($wvalpaq[$i],0,'.',',');
                    }
                }

         }

    }

    if( count($arregloResumen) > 0 ){
        echo "<table>";
        echo "<tr class='detallado'><td align='center'>PROCEDIMIENTO</td><td>Cantidad</td><td align='center'>Valor Unitario</td><td align='center'>Valor total</td><td align='center'>Valor Reconocido</td></tr>";
         foreach ($arregloResumen as $keyConcepto => $procedimientos ){
             $totalConcepto           = 0;
             $totalReconocidoConcepto = 0;

             echo "<tr class='encabezadoTabla'><td align='left' colspan='5'>Concepto: $keyConcepto - {$conceptos[$keyConcepto]}</td></tr>";
             $i = 0;
             foreach ($procedimientos as $keyProcedimiento => $datos) {
                if( $datos['cantidad'] > 0 ){
                    $i++;
                    $datos['valorUnitario']        = $datos['valorTotal']/$datos['cantidad'];
                    $totalConcepto                += $datos['valorTotal'];
                    $totalReconocidoConcepto      += $datos['valorReconocido'];
                    $totalConceptoTotal           += $datos['valorTotal'];
                    $totalReconocidoConceptoTotal += $datos['valorReconocido'];
                    ( is_int($i/2) ) ? $wclass = "fila1" : $wclass = "fila2";
                    ( is_int($i/2) ) ? $wclass2 = "fila2" : $wclass2 = "fila1";
                     echo "<tr class='$wclass' claseOriginal='$wclass' style='cursor:pointer;' onclick='verOcultarDetalle(this)'><td align='left'>{$keyProcedimiento}-{$datos['nombre']}</td><td align='center'>{$datos['cantidad']}</td><td align='right'>".number_format($datos['valorUnitario'],0,'.',',')."</td><td align='right'>".number_format($datos['valorTotal'],0,'.',',')."</td><td align='right'>".number_format($datos['valorReconocido'],0,'.',',')."</td></tr>";
                     echo "<tr style='display:none;''><td colspan='5' align='center'>";
                     if( count($datos['registros']) > 0 ){
                        echo "<div align='center' class='$wclass div_detalle'>";
                            echo "<table>";
                                echo "<tr class='encabezadoTabla'><td>Registro</td><td>Fecha</td><td>Procedimiento o Articulo</td><td>Tercero</td><td>Cantidad</td><td>Valor unit.</td><td>Valor Total</td><td>Valor Reconocido</td><td>Valor excedente</td><td>Usuario grabo</td></tr>";
                                foreach ($datos['registros'] as $keyRegistro => $id) {
                                    echo "<tr class='$wclass2' >
                                    <td>$id</td>
                                    <td align='center'>{$arregloDetalle[$keyConcepto]['cargos'][$id]['fecha']}</td>
                                    <td align='left'>{$keyProcedimiento} - {$datos['nombre']}</td>
                                    <td align='center'>{$arregloDetalle[$keyConcepto]['cargos'][$id]['tercero']}</td>
                                    <td align='center'>{$arregloDetalle[$keyConcepto]['cargos'][$id]['cantidad']}</td>
                                    <td align='right'>{$arregloDetalle[$keyConcepto]['cargos'][$id]['valorUnitario']}</td>
                                    <td align='right'>{$arregloDetalle[$keyConcepto]['cargos'][$id]['valorTotal']}</td>
                                    <td align='right'>{$arregloDetalle[$keyConcepto]['cargos'][$id]['valorFacturado']}</td>
                                    <td align='right'>{$arregloDetalle[$keyConcepto]['cargos'][$id]['valorExcedente']}</td>
                                    <td align='right'>{$arregloDetalle[$keyConcepto]['cargos'][$id]['usuarioGraba']}</td>
                                    </tr>";
                                }
                            echo "</table>";
                        echo "</div>";
                     }
                     echo "</td><tr>";
                }
             }
             echo "<tr class='encabezadoTabla'><td colspan='2' align='left'> TOTAL {$conceptos[$keyConcepto]}</td><td align='right'>".number_format($totalConcepto,0,'.',',')."</td><td align='right'>".number_format($totalReconocidoConcepto,0,'.',',')."</td><td>&nbsp;</td></tr>";
         }
         if( count( $arregloPaquetes ) > 0 ){
             $i = 0;
             foreach ($arregloPaquetes as $keyPaquete => $datos) {
                ( is_int($i/2) ) ? $wclass = "fila1" : $wclass = "fila2";
                 //echo "<tr class='$wclass'><td align='left'>Paquete: $keyPaquete - {$datos['nombre']}</td><td align='center'>{$datos['cantidad']}</td><td>{$datos['valorUnitario']}</td><td>{$datos['valor']}</td><td>{$datos['valor']}</td></tr>";
                 echo "<tr class='$wclass'><td align='left'>Paquete: $keyPaquete - {$datos['nombre']}</td><td align='center'>{$datos['cantidad']}</td><td>&nbsp;</td><td align='right'>{$datos['valor']}</td><td align='right'>{$datos['valor']}</td></tr>";
                 $auxiliar   = str_replace(",", "", $datos['valor']);
                 $totalConceptoTotal           = $totalConceptoTotal + $auxiliar*1;
                 $totalReconocidoConceptoTotal = $totalReconocidoConceptoTotal + $auxiliar*1;
             }
         }
         echo "<tr class='encabezadoTabla'><td colspan='3' align='left'> TOTAL CUENTA </td><td align='right'>".number_format($totalConceptoTotal,0,'.',',')."</td><td align='right'>".number_format($totalReconocidoConceptoTotal,0,'.',',')."</td></tr>";
         echo "</table><br>";
    }
    echo "<br>";
    echo "<center><input  type='button' id='btn_retornar' value='Retornar' onclick='retornar();'>";
    echo "</center>";

    return;
}

?>
<html>
<head>
    <title> Reporte resumido de cargos </title>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <style type="text/css">
    .div_detalle{
        padding-top    : 20px;
        padding-left   : 20px;
        padding-right  : 20px;
        padding-bottom : 20px;
    }
    .detallado{
        color:white;
        background:#638cb5;
        border:0px;
        margin-left: 1%;
        cursor: pointer;
    }
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function(){

            if( $("#wfuente").val() != "" && $("#wfactura").val() != "" ){
                $("#btn_generar").click();
            }else{
                $("#div_menu_ppal").show();
            }
        });

        function generarReporte(){
            var fuente  =  $("#wfuente").val();
            var factura =  $("#wfactura").val();
            if( $.trim(fuente) == "" || $.trim(factura) == "" ){
                return
            }
            $.ajax({
                    url: "rep_resumido_cargos.php",
                   type: "POST",
                  async: true,
                   data: {
                            peticionAjax: "generarReporte",
                            consultaAjax: "si",
                              wemp_pmla : $("#wemp_pmla").val(),
                              wbasedatos: $("#wbasedatos").val(),
                              wfuente   : fuente,
                              wfactura  : factura
                          },
                    success: function(data)
                    {
                        if( data != "" ){
                            $("#div_menu_ppal").hide();
                            $("#div_resultados").html(data);
                            $("#div_resultados").show();
                        }
                    }
            });
        }

        function verOcultarDetalle(tr){

            var hijo = $(tr).next("tr");
            hijo.toggle();
            if( $(hijo).is(":visible") ){
                $(tr).addClass("detallado");
            }else{
                $(tr).removeClass("detallado");
                $(tr).addClass( $(tr).attr("claseOriginal") );
            }
        }

        function retornar(){
            $("#div_resultados").html("");
            $("#div_resultados").hide();
            $("#div_menu_ppal").show();
        }
    </script>
</head>
<?php
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedatos  = $institucion->baseDeDatos;
    encabezado( " REPORTE RESUMIDO CARGOS DE FACTURA ", $wactualiz, "logo_".$wbasedatos );
?>
<body>
    <input id='wemp_pmla'  type='hidden' value='<?php echo $wemp_pmla; ?>'>
    <input id='wbasedatos' type='hidden' value='<?php echo $wbasedatos; ?>'>
    <center><div id='div_menu_ppal' align='left' style='width:30%;display:none;' class='fila2'>
        <table>
            <tr class='fila1'>
                <td align='left' style='width:30%;'> Fuente: </td>
                <td align='center'><input type='text' id='wfuente' value='<?php echo $wfuente; ?>' size='4'></td>
                <td align='left' style='width:30%;'> Num. Factura:</td>
                <td align='center'><input type='text' id='wfactura' value='<?php echo $wfactura; ?>' size='15'></td>
            </tr>
        </table><br>
        <center><input type='button' id='btn_generar' value='Consultar' onclick='generarReporte();'></center>
    </div></center>
    <br>
    <div id='div_resultados' align='center'>
    </div><br>
    <center><input type='button' id='btn_cerrar' value='Cerrar' onclick='window.close();'></center>
</body>
</html>