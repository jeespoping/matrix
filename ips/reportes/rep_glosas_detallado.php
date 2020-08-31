<?php
include_once("conex.php");
    /*

    2016-05-19 camilo zapata: creación del script: programa que permite la visualización de las facturas generadas en un periodo de tiempo x y que están o estuvieron glosadas, adicionalmente muestra
                              los valores asociados a dicha glosa, como valor glosado y valor aceptado por parte de la institución.

     */
    include_once( "root/comun.php" );
    require_once("conex.php");
    mysql_select_db( "matrix" );

    $wactualiz   = "2016-05-19";
    $empresas    = array();
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedato   = strtolower( $institucion->baseDeDatos );
    $wentidad    = $institucion->nombre;


    /* CONSULTAS AJAX */

    if(isset( $consultaAjax ) ){

        switch ($consultaAjax) {

            case 'generarReporte':
                generarReporteGlosas( $fecha_inicio, $fecha_final, $wcodEntidad, $wtipoReporte );
                break;
            case 'consultarFechaLimiteConsulta':
                consultarFechaLimiteConsulta( $fechaInicio );
                break;
            default:
                echo "<p> Petición ajax sin definir </p>";
                break;
        }

       return;
    }

    function inicializarArreglos(){

        global $empresas;
        global $conex;
        global $wbasedato;
        global $wemp_pmla;

        $caracteres    = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
        $caracteres2   = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");

        $query =  " SELECT Empcod codigo, Empnit, Empnom nombre
                      FROM {$wbasedato}_000024
                     WHERE Empcod = Empres
                       AND Empest='on'
                     ORDER BY 3";

        $result = mysql_query( $query, $conex ) or die(mysql_error());
        while($row2 = mysql_fetch_array($result)){
             $row2['nombre'] = utf8_encode( $row2['nombre'] );
             $row2['nombre'] = str_replace( $caracteres, $caracteres2, $row2['nombre'] );
             array_push( $empresas, trim($row2['codigo']).", ".trim($row2['nombre']) );
        }
        //echo "<pre>".print_r( $empresas, true )."</pre>";
    }

    function generarReporteGlosas( $fecha_inicial, $fecha_final, $wcodEntidad, $wtipoReporte ){

        global $wbasedato;
        global $conex;
        global $wemp_pmla;

        /* VARIABLES UTILIZADAS */

        $array_facturas_glosadas = array();//-> arreglo indexado por factura(ffa, fac), con los datos de la factura y adicional a eso un arreglo que apunta a las glosas que le fueron realizadas
        $array_res_glosas = array();

        /* FIN VARIABLES UTILIZADAS*/
        /* BUSQUEDA DE PARAMETROS PARA REALIZAR LAS CONSULTAS */
        $query = "SELECT carfue
                    FROM {$wbasedato}_000040
                   WHERE carglo = 'on'
                     AND carest = 'on'";
        $rs = mysql_query( $query, $conex );
        $wfueGlosa = mysql_fetch_assoc( $rs );
        $wfueGlosa = $wfueGlosa['carfue'];


        $query = "SELECT carfue
                    FROM {$wbasedato}_000040
                   WHERE carrad = 'on'
                     AND carest = 'on'";
        $rs = mysql_query( $query, $conex );
        $wfueRadicacion = mysql_fetch_assoc( $rs );
        $wfueRadicacion = $wfueRadicacion['carfue'];


        $query = "SELECT carfue
                    FROM {$wbasedato}_000040
                   WHERE carenv = 'on'
                     AND carest = 'on'";
        $rs = mysql_query( $query, $conex );
        $wenvio = mysql_fetch_assoc( $rs );
        $wenvio = $wenvio['carfue'];

        $query = " SELECT Empcod, Empnom
                     FROM {$wbasedato}_000024 ";
        $rs    = mysql_query( $query, $conex );
        while( $row = mysql_fetch_assoc($rs ) ){
            $array_empresas[$row['Empcod']] = $row['Empnom'];
        }

        $query = " SELECT Regcod, Regnom, Regori
                     FROM {$wbasedato}_000261 ";
        $rs    = mysql_query( $query, $conex ) or die(mysql_error());
        while( $row = mysql_fetch_assoc($rs ) ){
            $array_res_glosas[$row['Regcod']]['nombre'] = $row['Regnom'];
            $array_res_glosas[$row['Regcod']]['origen'] = $row['Regori'];
        }

        /* FINAL PARÁMETROS PARA REALIZAR CONSULTAS */

        // se construye una tabla temporal con las glosas que se hayan creado en el periodo consultado
        $condicionEntidad = ( $wcodEntidad != "" ) ? " AND fencod = '{$wcodEntidad}' " : "" ;

        $temp_18 = "tmp_{$wbasedato}_18_".date("H_i_s");
        $query   = "DROP TABLE IF EXISTS ".$temp_18;
        $rs      = mysql_query( $query, $conex );
        //echo "<br> edb-> tabla temporal $temp_18<br>";
        $query = " CREATE TEMPORARY TABLE IF NOT EXISTS {$temp_18}
                        ( INDEX( fuenteFactura, factura ) )";

        $query .= " SELECT b.fenffa fuenteFactura, b.fenfac factura, b.fencod codigoResponsable, b.fenval valorFactura,b.fencop valorCopago, (b.fenval - b.fencop - b.fencmo) valorNeto, fenhis historia, fening ingreso
                      FROM {$wbasedato}_000018 b
                     WHERE b.fenfec between '{$fecha_inicial}' and '{$fecha_final}'
                       AND b.fenest = 'on'
                       {$condicionEntidad}";
        $rsglos = mysql_query( $query, $conex ) or die( mysql_error()." - ".$query );


        $temp_18_21_glosas = "tmp_{$wbasedato}_18_21_".date("H_i_s");
        $query             = "DROP TABLE IF EXISTS ".$temp_18_21_glosas;
        $rs                = mysql_query( $query, $conex );
        //echo "<br> edb-> tabla temporal $temp_18_21_glosas<br>";
        $query = " CREATE TEMPORARY TABLE IF NOT EXISTS {$temp_18_21_glosas}
                        ( INDEX( historia, ingreso  ) )";

        $query .= " SELECT a.*, Renfue fuenteGlosa, Rennum numeroGlosa, Rencod codResponsable, Rennom nombreEntidad, Renusu usuarioGrabador, Renfec fechaGlosa,
                           Rdevca valorGlosa
                      FROM $temp_18 a
                     INNER JOIN
                           {$wbasedato}_000021 b on ( b.rdeffa=a.fuenteFactura and b.rdefac = a.factura and b.rdeest='on' and b.rdefue='{$wfueGlosa}')
                     INNER JOIN
                           {$wbasedato}_000020 c on ( rdefue = renfue and rdenum = rennum and renest = 'on') ";
        $rsglos = mysql_query( $query, $conex ) or die( mysql_error()." - ".$query );




        $temp_18_21_pacientes = "tmp_{$wbasedato}_18_21_pacientes".date("H_i_s");
        $query                = "DROP TABLE IF EXISTS ".$temp_18_21_pacientes;
        $rs                   = mysql_query( $query, $conex );
        //echo "<br> edb-> tabla temporal $temp_18_21_glosas<br>";
        $query = " CREATE TEMPORARY TABLE IF NOT EXISTS {$temp_18_21_pacientes}
                        ( INDEX( historia, ingreso  ) )";

        $query .= " SELECT a.*, b.ingfei fechaIngreso, CONCAT( pactdo, '-', pacdoc ) documento,  CONCAT(Pacno1, ' ', Pacno2, ' ', Pacap1, ' ', Pacap2, ' ') nombre, d.egrfee fechaEgreso
                      FROM $temp_18_21_glosas a
                     INNER JOIN
                           {$wbasedato}_000101 b on ( b.inghis=a.historia and b.ingnin = a.ingreso )
                     INNER JOIN
                           {$wbasedato}_000100 c on ( c.pachis = a.historia )
                      LEFT JOIN
                           {$wbasedato}_000108 d on ( d.egrhis = a.historia and d.egring = a.ingreso )";
        $rsglos = mysql_query( $query, $conex ) or die( mysql_error()." - ".$query );

        $query = " SELECT * FROM {$temp_18_21_pacientes} ORDER BY fuenteFactura,factura";
        $rs    = mysql_query($query, $conex);

        $respuesta = "";
        $html      = "";

        $num = mysql_num_rows($rs);

        if( $num > 0 and $wtipoReporte == "d" ){//--> si el reporte es detallado
            $html  .= "<div class='' align='left'><span class='subtituloPagina2'><font size='2'>FACTURAS GLOSADAS ENCONTRADAS: {$num}</font></span><br></div><br>";
            $html  .= "<div class='caja_flotante_query' width='100%'></div>";
            $html  .= "<table>";
                $html .= "<tr class='encabezadoTabla encabezadoFlotante'>";
                    $html .= "<td align='center'>Factura</td>";
                    $html .= "<td align='center'>Doc. paciente</td>";
                    $html .= "<td align='center'>Nom. Paciente</td>";
                    $html .= "<td align='center'>Historia</td>";
                    $html .= "<td align='center'>Ingreso</td>";
                    $html .= "<td align='center'>Responsable</td>";
                    $html .= "<td align='center'>Cod. Responsable</td>";
                    $html .= "<td align='center'>Cod. usuario glosa</td>";
                    $html .= "<td align='center'>Fecha<br>ingreso</td>";
                    $html .= "<td align='center'>Fecha<br>Egreso</td>";
                    $html .= "<td align='center'>Fecha de <br> Radicacion</td>";
                    $html .= "<td align='center'>Fecha de <br>glosa</td>";
                    $html .= "<td align='center'>Fecha respuesta <br> glosa</td>";
                    $html .= "<td align='center'>Valor Factura</td>";
                    $html .= "<td align='center'>Valor Copago</td>";
                    $html .= "<td align='center'>Valor neto</td>";
                    $html .= "<td align='center'>Valor Glosa parcial</td>";
                    $html .= "<td align='center'>Valor Glosa detalle</td>";
                    $html .= "<td align='center'>Valor Aceptado</td>";
                    $html .= "<td align='center'>Valor Respuesta parcial</td>";
                    $html .= "<td align='center'>Causa detalle</td>";
                    $html .= "<td align='center'>Detalle respuesta</td>";
                    $html .= "<td align='center'>Causa inherente</td>";
                $html .= "</tr>";
            $i = 0;
            while( $row = mysql_fetch_assoc( $rs ) ){

                $query = " SELECT Max(renfec) fechaRadicacion
                             FROM {$wbasedato}_000020
                            INNER JOIN
                                  {$wbasedato}_000021 on ( rdeffa = '{$row['fuenteFactura']}' and rdefac = '{$row['factura']}' and rdefue='{$wfueRadicacion}' and renfue = rdefue and rennum=rdenum and renfec <= '{$row['fechaGlosa']}')
                             ";
                $rsr   = mysql_query( $query, $conex );
                $rowr  = mysql_fetch_assoc($rsr);
                $row['fechaRadicacion'] = $rowr['fechaRadicacion'];

                $query = " SELECT Min(renfec) fechaEnvio
                             FROM {$wbasedato}_000020
                            INNER JOIN
                                  {$wbasedato}_000021 on ( rdeffa = '{$row['fuenteFactura']}' and rdefac = '{$row['factura']}' and rdefue='{$wenvio}' and renfue = rdefue and rennum=rdenum and renfec >= '{$row['fechaGlosa']}')
                             ";
                $rsr   = mysql_query( $query, $conex );
                $rowr  = mysql_fetch_assoc($rsr);
                $row['fechaEnvio'] = ( $rowr['fechaEnvio'] == "") ? "Sin reenviar" : $rowr['fechaEnvio'];
                $i++;
                $aux = $row;

                $querydetglo = " SELECT renfec fechaRespuesta, Rdevco valorConcepto, Rdecon concepto
                                   FROM {$wbasedato}_000021 a
                                  INNER JOIN
                                        {$wbasedato}_000020 b on (    a.rdeffa = '{$row['fuenteFactura']}' AND a.rdefac = '{$row['factura']}' AND a.rdeglo = '{$row['fuenteGlosa']}-{$row['numeroGlosa']}'
                                                                  and b.renfue = a.rdefue and b.rennum = a.rdenum and b.renest = 'on' and a.rdeest='on' )";

                $rsdetglo      = mysql_query($querydetglo, $conex);
                unset( $notaAux );
                $notaAux       = array();
                $valorAceptado = 0;
                $entro         = false;

                while( $rowdetglo = mysql_fetch_assoc( $rsdetglo ) ){
                    $entro = true;
                    $valorAceptado += $rowdetglo['valorConcepto'];
                    $detAux        =  array( 'valorConcepto'=>$rowdetglo['valorConcepto'], 'concepto'=>$rowdetglo['concepto'], 'fechaRespuesta'=>$rowdetglo['fechaRespuesta']);
                    array_push( $notaAux, $detAux );

                }

                $querycauglo = " SELECT Doccau Causa
                                   FROM {$wbasedato}_000071 a
                                  INNER JOIN
                                        {$wbasedato}_000072 b on (    a.Docfue = '{$row['fuenteGlosa']}' AND a.Docnum = '{$row['numeroGlosa']}' AND b.caucod = a.Doccau)";

                $rscauglo      = mysql_query($querycauglo, $conex) or die( mysql_error());

                /*$responsable = "";
                while( $rowcauglo = mysql_fetch_assoc( $rscauglo ) ){
                    if( $rowcauglo['origen'] == "I" ){
                        $reponsable = "Institución";
                    }
                }
                if( $responsable == "" ){
                    $responsable = "Entidad";
                }*/

                $queryResGlosa = " SELECT Rescod
                                     FROM {$wbasedato}_000262
                                    WHERE resffa = '{$row['fuenteFactura']}'
                                      AND resfac = '{$row['factura']}'
                                      AND resfue = '{$row['fuenteGlosa']}'
                                      AND resnum = '{$row['numeroGlosa']}'";
                $rsresglo      = mysql_query($queryResGlosa, $conex);
                $rowResGlosa   = mysql_fetch_array( $rsresglo );
                $respuestaGlosa= $rowResGlosa[0];
                $respuestaDes  = ( $respuestaGlosa != "" ) ? " <span class='subtituloPagina2'><font size='2'>{$array_res_glosas[$rowResGlosa[0]]['nombre']}</font></span>" : " <span class='subtituloPagina2'><font size='2'>Sin reenviar</font></span> ";

                //-> consultas de detalle de las glosas
                switch ( $array_res_glosas[$rowResGlosa[0]]['origen'] ) {
                    case 'C':
                        $responsable = "CLINICA";
                        break;
                    case 'E':
                        $responsable = "ENTIDAD";
                        break;
                    default:
                        $responsable = "MIXTA";
                        break;
                }
                $respuesta .= "<br>";

                $class = ( is_int($i/2) ) ? "fila1" : "fila2";
                $valorNoAceptado = $row['valorGlosa']*1 - $valorAceptado*1;
                if( $entro ){

                    $html .=  "<tr class='$class'>";
                        $html .= "<td align='center'>{$row['fuenteFactura']}-{$row['factura']}</td>";
                        $html .= "<td align='center'>{$row['documento']}</td>";
                        $html .= "<td align='center' nowrap='nowrap'>{$row['nombre']}</td>";
                        $html .= "<td align='center'>{$row['historia']}</td>";
                        $html .= "<td align='center'>{$row['ingreso']}</td>";
                        $html .= "<td align='center'>{$array_empresas[$row['codigoResponsable']]}</td>";
                        $html .= "<td align='center'>{$row['codigoResponsable']}</td>";
                        $html .= "<td align='center'>{$row['usuarioGrabador']}</td>";
                        $html .= "<td align='center'>{$row['fechaIngreso']}</td>";
                        $html .= "<td align='center'>{$row['fechaEgreso']}</td>";
                        $html .= "<td align='center'>{$row['fechaRadicacion']}</td>";
                        $html .= "<td align='center'>{$row['fechaGlosa']}</td>";
                        $html .= "<td align='center'>{$row['fechaEnvio']}</td>";//fecha respuesta de la glosa
                        $html .= "<td align='center'>".number_format($row['valorFactura'],0,',','.')."</td>";
                        $html .= "<td align='center'>".number_format($row['valorCopago'],0,',','.')."</td>";
                        $html .= "<td align='center'>".number_format($row['valorNeto'],0,',','.')."</td>";
                        $html .= "<td align='center'>".number_format($row['valorGlosa'],0,',','.')."</td>";
                        $html .= "<td align='center'>valor causa?</td>";//valor detalle
                        $html .= "<td align='center'>".number_format($valorAceptado,0,',','.')."</td>";//valor aceptado por clínica
                        $html .= "<td align='center'>".number_format($valorNoAceptado,0,',','.')."</td>";
                        $html .= "<td align='center'>detalle causa</td>";//causa detalle
                        $html .= '<td align="center" class="td_respuesta_glosa" title="'.utf8_encode($respuestaDes).'">'.$respuestaGlosa.'</td>';
                        $html .= "<td align='center'>{$responsable}</td>";
                    $html .= "</tr>";
                }

                if( !$entro ){
                    if( $row['fechaEnvio'] != "Sin reenviar" ){
                        $valorAceptado     = 0;
                        $valorGlosaDetalle = 0;
                        $valorNoAceptado   = 0;
                        $valorConcepto     = 0;
                    }else{
                        $valorAceptado     = "SR";
                        $valorGlosaDetalle = "SR";
                        $valorNoAceptado   = "SR";
                        $valorConcepto     = "SR";
                    }
                    $html .=  "<tr class='$class'>";
                        $html .= "<td align='center'>{$row['fuenteFactura']}-{$row['factura']}</td>";
                        $html .= "<td align='center'>{$row['documento']}</td>";
                        $html .= "<td align='center' nowrap='nowrap'>{$row['nombre']}</td>";
                        $html .= "<td align='center'>{$row['historia']}</td>";
                        $html .= "<td align='center'>{$row['ingreso']}</td>";
                        $html .= "<td align='center'>{$array_empresas[$row['codigoResponsable']]}</td>";
                        $html .= "<td align='center'>{$row['codigoResponsable']}</td>";
                        $html .= "<td align='center'>{$row['usuarioGrabador']}</td>";
                        $html .= "<td align='center'>{$row['fechaIngreso']}</td>";
                        $html .= "<td align='center'>{$row['fechaEgreso']}</td>";
                        $html .= "<td align='center'>{$row['fechaRadicacion']}</td>";
                        $html .= "<td align='center'>{$row['fechaGlosa']}</td>";
                        $html .= "<td align='center'>{$row['fechaEnvio']}</td>";//fecha respuesta de la glosa
                        $html .= "<td align='center'>".number_format($row['valorFactura'],0,',','.')."</td>";
                        $html .= "<td align='center'>".number_format($row['valorCopago'],0,',','.')."</td>";
                        $html .= "<td align='center'>".number_format($row['valorNeto'],0,',','.')."</td>";
                        $html .= "<td align='center'>".number_format($row['valorGlosa'],0,',','.')."</td>";
                        $html .= "<td align='center'>{$valorGlosaDetalle}</td>";
                        $html .= "<td align='center'>{$valorAceptado}</td>";//valor aceptado por clínica
                        $html .= "<td align='center'>{$valorNoAceptado}</td>";//Valor Respuesta parcial
                        $html .= "<td align='center'>{$valorConcepto}</td>";//causa detalle
                        $html .= '<td align="center" class="td_respuesta_glosa" title="'.$respuestaDes.'">SR</td>';
                        $html .= "<td align='center'>{$responsable}</td>";//Causa inherente
                    $html .= "</tr>";
                }
                $valorAceptadoSumar = ( !isset($valorAceptado) or trim($valorAceptado) == "SR" ) ? 0 : $valorAceptado;
                $valorNoAceptadoSumar = ( !isset($valorNoAceptado) or trim($valorNoAceptado) == "SR" ) ? 0 : $valorNoAceptado;
                $totalValorFactura  += $row['valorFactura']*1;
                $totalValorCopago   += $row['valorCopago']*1;
                $totalValorNeto     += $row['valorNeto']*1;
                $totalValorGlosa    += $row['valorGlosa']*1;
                $valorAceptadoTotal += $valorAceptadoSumar;
                $valorNoAceptadoTotal += $valorNoAceptadoSumar;
            }
             $html .=  "<tr class='encabezadoTabla'>";
                    $html .= "<td align='left' colspan='13'>TOTALES</td>";
                    $html .= "<td align='center'>".number_format($totalValorFactura,0,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($totalValorCopago,0,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($totalValorNeto,0,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($totalValorGlosa,0,',','.')."</td>";
                    $html .= "<td align='center'>&nbsp;</td>";
                    $html .= "<td align='center'>".number_format($valorAceptadoTotal,0,',','.')."</td>";//valor aceptado por clínica
                    $html .= "<td align='center'>".number_format($valorNoAceptadoTotal,0,',','.')."</td>";//Valor Respuesta parcial
                    $html .= "<td align='center'>&nbsp;</td>";//causa detalle
                    $html .= '<td align="center">&nbsp;</td>';
                    $html .= "<td align='center'>&nbsp;</td>";//Causa inherente
                $html .= "</tr>";
            $html  .= "</table>";
        }else if( $num > 0 and $wtipoReporte == "r" ){
             $html  .= "<div class='' align='left'><span class='subtituloPagina2'><font size='2'>FACTURAS GLOSADAS ENCONTRADAS: {$num}</font></span><br></div><br>";
             $html  .= "<div class='caja_flotante_query' width='100%'></div>";
             $html  .= "<table>";
                $html .= "<tr class='encabezadoTabla encabezadoFlotante'>";
                    $html .= "<td align='center'>Nombre Entidad</td>";
                    $html .= "<td align='center'>Cod. Entidad</td>";
                    $html .= "<td align='center'>Cantidad Facturas <br> glosadas</td>";
                    $html .= "<td align='center'>Valor Facturado</td>";
                    $html .= "<td align='center'>Valor glosas</td>";
                    $html .= "<td align='center'>Valor Aceptado</td>";
                    $html .= "<td align='center'>Valor no Aceptado</td>";
                    $html .= "<td align='center'>Glosas/Facturado</td>";
                    $html .= "<td align='center'>Glosa Aceptada/Facturado</td>";
                    $html .= "<td align='center'>Saldo pendiente</td>";
                $html .= "</tr>";
             $totalFacturas  = 0;
             $totalFacturado = 0;
             $totalGlosado   = 0;
             $totalAceptado   = 0;
             while( $row = mysql_fetch_assoc( $rs ) ){
                if( !isset( $resumido[$row['codigoResponsable']] ) ){
                    $resumido[$row['codigoResponsable']] = array('cantidadFacturas'=>0, 'totalFacturado'=>0, 'totalGlosado'=>0, 'totalAceptado'=>0, 'totalNoAceptado'=>0 );
                }

                $querydetglo = " SELECT renfec fechaRespuesta, Rdevco valorConcepto, Rdecon concepto
                                   FROM {$wbasedato}_000021 a
                                  INNER JOIN
                                        {$wbasedato}_000020 b on (    a.rdeffa = '{$row['fuenteFactura']}' AND a.rdefac = '{$row['factura']}' AND a.rdeglo = '{$row['fuenteGlosa']}-{$row['numeroGlosa']}'
                                                                  and b.renfue = a.rdefue and b.rennum = a.rdenum and b.renest = 'on' and a.rdeest='on' )";

                $rsdetglo      = mysql_query($querydetglo, $conex);
                $entro         = false;

                while( $rowdetglo = mysql_fetch_assoc( $rsdetglo ) ){
                    $entro = true;
                    $totalAceptado += $rowdetglo['valorConcepto'];
                    $resumido[$row['codigoResponsable']]['totalAceptado'] += $rowdetglo['valorConcepto'];

                }
                $resumido[$row['codigoResponsable']]['cantidadFacturas']++;
                $resumido[$row['codigoResponsable']]['totalFacturado'] += $row['valorFactura'];
                $resumido[$row['codigoResponsable']]['totalGlosado'] += $row['valorGlosa'];

                $totalFacturas ++;
                $totalFacturado += $row['valorFactura'];
                $totalGlosado   += $row['valorGlosa'];
             }
             $i = 0;
             foreach ($resumido as $codEntidad => $datosEntidad ) {
                $i++;
                $class = ( is_int($i/2) ) ? "fila1" : "fila2";
                 $html .= "<tr class='$class'>";
                    $html .= "<td align='center'>".$array_empresas[$codEntidad]."</td>";
                    $html .= "<td align='center'>".$codEntidad."</td>";
                    $html .= "<td align='center'>".number_format($datosEntidad['cantidadFacturas'],0,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($datosEntidad['totalFacturado'],0,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($datosEntidad['totalGlosado'],0,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($datosEntidad['totalAceptado'],0,',','.')."</td>";
                    $valorNoAceptado         = $datosEntidad['totalGlosado']*1 - $datosEntidad['totalAceptado']*1;
                    $valorGlosa_facturado    = $datosEntidad['totalGlosado']*1/$datosEntidad['totalFacturado']*1;
                    $valorAceptado_facturado = $datosEntidad['totalAceptado']*1/$datosEntidad['totalFacturado']*1;
                    $valorPendiente          = $valorNoAceptado*1;
                    $html .= "<td align='center'>".number_format($valorNoAceptado,0,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($valorGlosa_facturado,2,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($valorAceptado_facturado,2,',','.')."</td>";
                    $html .= "<td align='center'>".number_format($valorNoAceptado,0,',','.')."</td>";
                $html .= "</tr>";
             }

             $html .= "<tr class='encabezadoTabla'>";
                $html .= "<td align='center' colspan='2'>TOTALES</td>";
                $html .= "<td align='center'>".number_format($totalFacturas,0,',','.')."</td>";
                $html .= "<td align='center'>".number_format($totalFacturado,0,',','.')."</td>";
                $html .= "<td align='center'>".number_format($totalGlosado,0,',','.')."</td>";
                $html .= "<td align='center'>".number_format($totalAceptado,0,',','.')."</td>";
                $valorNoAceptado         = $totalGlosado*1 - $totalAceptado*1;
                $valorGlosa_facturado    = $totalGlosado*1/$totalFacturado*1;
                $valorAceptado_facturado = $totalAceptado*1/$totalFacturado*1;
                $valorPendiente          = $valorNoAceptado*1;
                $html .= "<td align='center'>".number_format($valorNoAceptado,0,',','.')."</td>";
                $html .= "<td align='center'>".number_format($valorGlosa_facturado,2,',','.')."</td>";
                $html .= "<td align='center'>".number_format($valorAceptado_facturado,2,',','.')."</td>";
                $html .= "<td align='center'>".number_format($valorNoAceptado,0,',','.')."</td>";
             $html .= "</tr>";
             $html .= "</table>";
            // echo "<pre>".print_r( $resumido, true )."</pre>";
        }else{
             $html  .= "<div class='' align='left'><span class='subtituloPagina2'><font size='2'>FACTURAS GLOSADAS ENCONTRADAS: {$num}</font></span><br></div><br>";
        }
        echo $html;

    }

    function consultarFechaLimiteConsulta( $fechaInicio ){
        $aux = strtotime( $fechaInicio."+6 month" ) - 24*60*60;
        if( ($aux - strtotime( date('Y-m-d') )) < 0 ){
            $fechaMesAtras = date( "Y-m-d", $aux );
        }else{
            $fechaMesAtras = date('Y-m-d');
        }
        echo $fechaMesAtras;
    }
    /* FIN CONSULTAS AJAX*/


?>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <title> REPORTE GENERAL DE FACTURAS GLOSADAS </title>
    <style>
        #tooltip{
            color           : #2A5DB0;
            font-family     : Arial,Helvetica,sans-serif;
            position        : absolute;
            z-index         : 3000;
            border          : 1px solid #2A5DB0;
            background-color: #FFFFFF;
            padding         : 5px;
            opacity         : 1;
        }
        #tooltip div{
            margin:0;
            width:250px;
        }
    </style>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
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
    <script type="text/javascript">
    $(document).ready(function(){

            empresas_nombres_array = new Array();
            var empresas = eval( $("#array_empresas").val() );
            for( i in empresas ){
                empresas_nombres_array.push( empresas[i] );
                //console.log( "--->"+empresas[i]);
            }
             $("#input_empresa").on("keyup", function(){
                if( $(this).val() == "" ){
                    console.log( $(this).val() );
                    $(this).parent().find("#wempresa").val("");
                }
             });

             $( "#input_empresa" ).autocomplete({
                    source    : empresas_nombres_array,
                    minLength : 2,
                    messages: {
                        noResults: '',
                        results: function() {}
                    },
                    select: function( event, ui ) {
                        var empresaSeleccionada = ui.item.value;
                        if( $.trim(empresaSeleccionada) != "" ){
                            empresaSeleccionada = empresaSeleccionada.split(",");
                            empresaSeleccionada = $.trim( empresaSeleccionada[0] );
                            $(this).parent().find("#wempresa").val(empresaSeleccionada);
                        }else{
                            $(this).parent().find("#wempresa").val("");
                        }
                    }
            });

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
                    consultarFinalPermitida( dateText );
                }
            });

             $("#fecha_final").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                buttonText: "",
                maxDate: "+0m +0w"
            });
        });


        function generarReporte(){

            $("#div_respuesta").hide();
            $("#msjEspereSolicitud").show();
            $.ajax({
                url  : "rep_glosas_detallado.php",
                type : "post",
                async: false,
                data:
                {
                    consultaAjax : "generarReporte",
                    wemp_pmla    : $("#wemp_pmla").val(),
                    fecha_inicio : $("#fecha_inicio").val(),
                    fecha_final  : $("#fecha_final").val(),
                    wcodEntidad  : $("#wempresa").val(),
                    wtipoReporte : $("input[type='radio']:checked").val()
                },
                success : function(data){
                    if(data != "")
                    {
                        $("#div_respuesta").html(data);
                        $("#div_respuesta").show();
                        $("#msjEspereSolicitud").hide();
                         $(".td_respuesta_glosa").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
                    }else{
                    }
                }
            });
        }


        function cerrarVentana()
        {
            window.close();
        }

        function consultarFinalPermitida( fechaInicio ){

            var rango_superior      = 245;
            var rango_inferior      = 11;
            var aleatorio           = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

            $.ajax({
                url     : "rep_glosas_detallado.php",
                type    : "POST",
                async   : true,
                data    : {
                            consultaAjax      : "consultarFechaLimiteConsulta",
                            fechaInicio       : fechaInicio,
                            wemp_pmla         : $("#wemp_pmla").val()
                          },
                success : function(data){

                        $("#fecha_final").val(data);
                        $("#fecha_final").datepicker( "destroy" );
                        $("#fecha_final").datepicker({
                              showOn: "button",
                              buttonImage: "../../images/medical/root/calendar.gif",
                              buttonImageOnly: true,
                              maxDate: data,
                              minDate: fechaInicio+" -1D"
                        });
                }
            });
        }

    </script>
</head>
<body>
    <?php
        inicializarArreglos();
        $empresas    = json_encode( $empresas );
    ?>
    <?php encabezado( " REPORTE GENERAL DE FACTURAS GLOSADAS ", $wactualiz, "clinica" ); ?>
    <br><br><br>

    <input type='hidden' id='wemp_pmla'       value='<?=$wemp_pmla;?>'>
    <input type='hidden' id='fecha_hoy'       value='<?=date("Y-m-d");?>'>
    <input type='hidden' name='array_empresas' id='array_empresas' value='<?=$empresas?>'>

    <div id='div_formulario' align='center' class='div_formulario'>
        <span class="subtituloPagina2">Parámetros de consulta</span><br><br>
        <table class='tabla_formulario'>
                <td class='subEncabezado fila1'> EMPRESA: </td>
                <td class='fila2'>
                    <input type='text' name='input_empresa' id='input_empresa' size='40' value=''>
                    <input type='hidden' name='wempresa' id='wempresa' value=''>
                </td>
            </tr>
            <tr>
                <td class='subEncabezado fila1'> PERIODO: </td><td class='fila2'><input id='fecha_inicio' size='12' type='text' value='<?=date("Y-m-d");?>'> Hasta <input id='fecha_final' size='12' type='text' value='<?=date("Y-m-d");?>'></td>
            </tr>
            <tr>
                <td class='subEncabezado fila1'> TIPO REPORTE: </td><td class='fila2' align='center'> <input type='radio' name='tipoReporte' value='r' placeholder="">Resumido &nbsp;&nbsp; <input type='radio' name='tipoReporte' value='d' checked="checked" placeholder="">Detallado</td>
            </tr>
        </table>
        <br>
        <input type="button" onclick="generarReporte();" value="BUSCAR" class="botona" id="btn_consultar">
    </div><br>
    <center><div id='div_respuesta' style='with:100%;' align='left'>
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