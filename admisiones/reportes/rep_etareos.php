<?php
//=========================================================================================================================================\\
//        DILIGENCIAR PREANESTESIA
//=========================================================================================================================================\\
//DESCRIPCION:          Sofware que permite a un usuario consultar los diagnosticos por grupo etareo
//AUTOR:                Camilo Zapata
//FECHA DE CREACION:    2017-08-30
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//2017-06-07 camilo zapata:
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

include("conex.php");
include("root/comun.php");
mysql_select_db("matrix");
$wactualiz    = "2017-08-30";
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
            $arrayCcos              = crearArreglosClasificatorios( $condicion, $condicionCCo );
            $clasificacionReal      = $arrayCcos['clasificacionReal'];
            $condicionCcoConsultado = $arrayCcos['condicionCcoConsultado'];
            $arrayCcos              = $arrayCcos['arrayCcosMostrados'];
            $gruposEtareos          = obtenerArreglosEdades( $tipo_consultado, $arrayCcos );
            $gruposEtareosEnc       = $gruposEtareos;
            $categorias_diagnos     = array();

            foreach( $wdiagnosticos as $i => $codigoDiagnostico ){
                array_push( $array_diagnosticos2, "'".$codigoDiagnostico."'" );
            }
            $condicion_diagnosticos2 = implode( ",", $array_diagnosticos2 );

            if( $incluirTodosDx == "no" ){

                foreach( $wdiagnosticos as $i => $codigoDiagnostico ){
                    array_push( $array_diagnosticos, "'".$codigoDiagnostico."'" );
                }

                $array_diagnosticos     = implode( ",", $array_diagnosticos );
                $condicion_diagnosticos = " AND diacod IN ( {$array_diagnosticos} ) ";

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


            $query = " SELECT Diahis, Diaing, egrfee,  egd.Diacod, Egrest, concat( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) nombre, dia.descripcion, egd.id id, pac.pacfna, pacsex, sev.sercod, egr.egrcae, dia.Capitulo
                         FROM {$wcliame}_000109 egd
                        INNER JOIN
                              {$wcliame}_000108 egr on ( egr.egrfee BETWEEN '{$fecha_inicio}' AND '{$fecha_final}' AND Egrhis = Diahis
                                                        AND Egring = Diaing {$condicion_diagnosticos} AND egd.diatip = 'P' )
                        INNER JOIN
                              {$wcliame}_000112 sev on ( sev.serhis = egr.egrhis AND sev.sering = egr.egring AND sev.seregr = 'on' )
                        INNER JOIN
                              {$wcliame}_000100 pac on ( pachis = diahis )
                        INNER JOIN
                              root_000011 dia on ( dia.codigo = egd.diacod )
                        {$condicionCcoConsultado}
                        GROUP BY 1,2,3,4,5,6,7,8,9
                        ORDER BY egd.Diacod desc, nombre asc";

            $rs    = mysql_query( $query, $conex ) or die( mysql_error() );
            $i     = 0;
            $historiaAnterior = "";
            $historiaNueva    = "";
            while( $row = mysql_fetch_assoc( $rs ) ){

                $nacimientoSegundos = strtotime( $row['pacfna'] )*1;
                $egresoSegundos     = strtotime( $row['egrfee'] )*1;
                $edadSegundos       = $egresoSegundos - $nacimientoSegundos;//---> edad en el momento del egreso
                ubicarEnArreglos( $edadSegundos, $row, $gruposEtareos, $arrayCcos, $array_diagnosticos, $incluirTodosDx );

            }

            if( $difSexo == "si" ){
                $arraySex = array( "M", "F");
                $colspanSex = 2;
            }else{
                $arraySex = array( "A" );
                $colspanSex = 1;
            }

            $cantidad_categorias = count( $categorias_diagnos );
            $html = "<div id='div_tipos_diagnosticos'>";
                $html .= "<p><span class='subtituloPagina2'><font size='3'>CATEGORIAS DE DIAGN&Oacute;STICOS CONSULTADAS</font></span></p>";
                $i = 0;
                foreach( $categorias_diagnos as $nombreCategoria => $cantidadXcategoria ){
                    $i++;
                    $salto = is_int( $i/4 ) ? " <br> " : "";
                    $coma  = ( $i < $cantidad_categorias and $cantidad_categorias > 1 ) ? "," : "";
                    $html .= "<span class='subtituloPagina2'><font size='2'>{$nombreCategoria}{$coma}</font></span>".$salto;

                }

            $html .= "</div><br><br>";

            foreach( $gruposEtareosEnc as $keyGrupoEtareoEnc => $datosGrupoEnc ){

                $i                   = 0;
                $totalHombres        = 0;
                $totalMujeres        = 0;
                $totalVivos          = 0;
                $totalMuertes        = 0;
                $totalHospitalizados = 0;
                $totalUrgencias      = 0;
                $htmlS        = "";
                $htmlDetalles = "";
                $html .= "<table style='border:1px solid black;'>";

                    $html .= "<tr class='encabezadotabla'>";
                        $html  .= "<td align='center' rowspan='2'> RANGO DE FECHA </td>";
                        $html  .= "<td align='center' rowspan='2'>TIPO SERVICIO</td>";
                    foreach ($datosGrupoEnc['datosGrupo'] as $key => $detalleGrupo ) {
                        $html  .= "<td width='80' colspan='{$colspanSex}' align='center'>".utf8_decode($detalleGrupo['descripcion'])."</td>";
                        if( $difSexo == "si" ){
                            $htmlS .= "<td align='center' class='fila1'>M</td><td align='center' class='fila2'>F</td>";
                        }else{
                            $htmlS .= "<td align='center' class='encabezadotabla'>&nbsp;</td>";
                        }
                    }
                    $html .= "</tr>";
                    $html .= "<tr>{$htmlS}</tr>";
                    $numGruposCco = count( $arrayCcos );
                    $iteracion    = 0;
                    foreach( $arrayCcos as $keyCcos => $datosAux ){

                        $datos = $datos['ccos'];
                        $iteracion++;
                        $auxCco = explode("_", $keyCcos );
                        $classTotal = ( $auxCco[0] == "TOTAL" ) ? "encabezadoTabla" : "";
                        $html .= "<tr class='$classTotal'>";
                        if( $iteracion == 1){
                            $html .= "<td align='center' class='fila2' rowspan='{$numGruposCco}'>{$fecha_inicio} <br>HASTA<br> {$fecha_final}</td>";
                        }

                        $classTotal = ( $auxCco[0] == "TOTAL" ) ? "encabezadoTabla" : "fila2";
                        $classF1    = ( $auxCco[0] == "TOTAL" ) ? "encabezadoTabla" : "fila1";
                        $classF2    = ( $auxCco[0] == "TOTAL" ) ? "encabezadoTabla" : "fila2";
                        $html .= "<td align='left' class='$classTotal'>{$auxCco[0]}</td>";
                        $datosGrupoEtareo = $gruposEtareos[$keyGrupoEtareoEnc];
                        foreach ($datosGrupoEtareo['datosGrupo'] as $keyGrupoEtareo => $detalleAux ) {
                            $detalle = $detalleAux[$keyCcos];
                            //echo "<br>edb-><br>-->$keyCcos<pre>".print_r($detalle, true)."</pre>";
                            if( $difSexo == "si" ){
                                $html .= "<td align='center' class='$classF1' style='cursor: pointer;' onclick='mostrarDetalle( \"".utf8_encode($datosGrupoEnc['datosGrupo'][$keyGrupoEtareo]['descripcion'])."\", \"M\", \"{$keyGrupoEtareoEnc}_{$keyGrupoEtareo}_M_{$keyCcos}\")'>".$detalle['M']."</td><td align='center' class='$classF2' style='cursor: pointer;' onclick='mostrarDetalle( \"".utf8_encode($detalle['descripcion'])."\", \"F\", \"{$keyGrupoEtareoEnc}_{$keyGrupoEtareo}_F_{$keyCcos}\")'>".$detalle['F']."</td>";
                            }else{
                                $html .= "<td align='center' class='$classF1' style='cursor: pointer;' onclick='mostrarDetalle( \"".utf8_encode($datosGrupoEnc['datosGrupo'][$keyGrupoEtareo]['descripcion'])."\", \"A\", \"{$keyGrupoEtareoEnc}_{$keyGrupoEtareo}_A_{$keyCcos}\")'>".$detalle['A']."</td>";
                            }
                            $totalMujeres += $detalle['F']*1;
                            $totalHombres += $detalle['M']*1;
                            foreach( $arraySex as $keySex => $dato ){
                                $indice = "egresos".$dato;
                                if( count( $detalle[$indice] ) > 0 ){
                                    $divDet = "<div align='center' id='div_{$keyGrupoEtareoEnc}_{$keyGrupoEtareo}_{$dato}_{$keyCcos}' class='div_detalle' style='display:none;'> ";
                                        $divDet .= "<table>";
                                            $divDet .= "<tr class='encabezadotabla'>";
                                                $divDet .= "<td align='center'&nbsp;</td>";
                                                $divDet .= "<td align='center'>Historia</td>";
                                                $divDet .= "<td align='center'>Ingreso</td>";
                                                $divDet .= "<td align='center'>Nombre</td>";
                                                $divDet .= "<td align='center'>Diagnostico</td>";
                                                //$divDet .= "<td align='center'>Edad</td>";
                                            $divDet .= "</tr>";
                                        $ctrClass = 0;
                                        foreach( $detalle[$indice] as $keyDetalle=> $datoDetalle ){
                                            $ctrClass++;
                                            $classDet = ( is_int($ctrClass/2) ) ? "fila1" : "fila2";
                                            $divDet .= "<tr class='$classDet'>";
                                                $divDet .= "<td align='center'>{$ctrClass}</td>";
                                                $divDet .= "<td align='center'>{$datoDetalle['Diahis']}</td>";
                                                $divDet .= "<td align='center'>{$datoDetalle['Diaing']}</td>";
                                                $divDet .= "<td align='left'>{$datoDetalle['nombre']}</td>";
                                                $divDet .= "<td align='left'>{$datoDetalle['descripcion']}</td>";
                                                //$divDet .= "<td align='center'>{$datoDetalle['fechaNacimiento']} edad</td>";
                                            $divDet .= "</tr>";
                                            if( $datoDetalle['egrcae'] == "+48" or $datoDetalle['egrcae'] == "-48" ){
                                                $totalMuertes++;
                                            }else{
                                                $totalVivos++;
                                            }
                                            if( $clasificacionReal[$datoDetalle['sercod']] == "ccohos" ){
                                                $totalHospitalizados++;
                                            }
                                            if( $clasificacionReal[$datoDetalle['sercod']] == "ccourg" ){
                                                $totalUrgencias++;
                                            }
                                        }
                                        $divDet .= "</table>";
                                    $divDet .= "</div>";
                                }else{
                                    $divDet = "<div align='center' id='div_{$keyGrupoEtareoEnc}_{$keyGrupoEtareo}_{$dato}_{$keyCcos}' style='display:none;' class='div_detalle'>";
                                        $divDet .= "<span class='subtituloPagina2'>SIN DATOS</span>";
                                    $divDet .= "</div>";
                                }
                                $htmlDetalles .= $divDet;
                            }
                        }
                        $html .= "</tr>";
                }
                $html .= "</table>";
                if( $difSexo  == "si" and $incluirTodosDx == "no" ){
                    $html .= "<div class='div_resumen'>";
                    $html .= "<table class='tablaColapsada'>";
                        $html .= "<tr class='fila1'><td align='left'> TOTAL HOMBRES </td><td align='center'>{$totalHombres}</td></tr>";
                        $html .= "<tr class='fila2'><td align='left'> TOTAL MUJERES </td><td align='center'>{$totalMujeres}</td></tr>";
                        $html .= "<tr class='fila1'><td align='left'> TOTAL VIVOS </td><td align='center'>{$totalVivos}</td></tr>";
                        $html .= "<tr class='fila2'><td align='left'> TOTAL MUERTOS </td><td align='center'>{$totalMuertes}</td></tr>";
                        $html .= "<tr class='fila1'><td align='left'> TOTAL HOSPITALIZADOS</td><td align='center'>{$totalHospitalizados}</td></tr>";
                        $html .= "<tr class='fila2'><td align='left'> TOTAL URGENCIAS</td><td align='center'>{$totalUrgencias}</td></tr>";
                    $html .= "</table>";
                    $html .= "</div>";
                }
                $html .= $htmlDetalles;
                $html .= "<br><br>";

            }

            echo $html;
            break;
        default:
            # code;
            break;
    }
    return;
}

//-----------------------------------------> FUNCIONES <---------------------------------------------------------------------//

function ubicarEnArreglos( $edadSegundos, $row, &$gruposEtareos, &$array_ccos, $array_diagnosticos, $incluirTodosDx ){

    global $condicion, $difSexo;

    foreach( $gruposEtareos as $keyGrupoEtareo => $datosGrupoEtareo ){
        foreach ($datosGrupoEtareo['limitesEnSegundos'] as $key => $limiteSuperior ) {

            if( $limiteSuperior < $edadSegundos ){

            }else{//--> la primera vez que entre acá indica que el limite superior de este rango es mayor o igual lo que indica que debe ir en esta posición
                 $clasificacionCco = "";
                 $sumarEnTotal     = false;

                 if( $condicion == "sep_uci_mue" and ( $row['egrcae'] == "+48" or $row['egrcae'] == "-48" ) ){
                    if( $incluirTodosDx == "no" ){
                        $clasificacionCco = "muertes";
                    }else{
                        ( in_array( $row['Diacod'], $array_diagnosticos ) ) ? $clasificacionCco = "muertes" : $clasificacionCco = "TOTAL_muertes";
                    }
                 }

                 if( $clasificacionCco == "" ){

                     foreach( $array_ccos as $keyCcos => $serviciosIncluidosAux ){
                        $serviciosIncluidos = $serviciosIncluidosAux['ccos'];
                         if( in_array( $row['sercod'], $serviciosIncluidos ) ){

                            if( $incluirTodosDx == "si" ){

                                $keyCcosAux = explode( "_", $keyCcos );

                                if( $keyCcosAux[0] == "TOTAL" ){

                                    if( !in_array( $row['Diacod'], $array_diagnosticos ) ){
                                        $clasificacionCco = $keyCcos;
                                        break;
                                    }

                                }else{

                                    if( in_array( $row['Diacod'], $array_diagnosticos ) ){
                                        $clasificacionCco = $keyCcos;
                                        $sumarEnTotal = true;
                                        break;
                                    }

                                }

                            }else{
                                $clasificacionCco = $keyCcos;
                                break;
                            }

                         }
                     }
                 }
                 $gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior][$clasificacionCco]['cantidad']++;

                 if( $difSexo == "si" ){
                    if( $incluirTodosDx == "si" and $sumarEnTotal ){
                        $gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior]["TOTAL_".$clasificacionCco][$row['pacsex']]++;
                    }
                    $gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior][$clasificacionCco][$row['pacsex']]++;

                 }else{

                    $gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior][$clasificacionCco]['A']++;

                 }
                 if( $clasificacionCco != "" ){

                    if( $difSexo == "si" ){
                         if( $row['pacsex'] == "M" ){
                            if( $incluirTodosDx == "si" and $sumarEnTotal ){
                                 array_push($gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior]["TOTAL_".$clasificacionCco]['egresosM'], $row);
                            }
                            array_push($gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior][$clasificacionCco]['egresosM'], $row);
                         }else{
                            if( $incluirTodosDx == "si" and $sumarEnTotal ){
                                 array_push($gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior]["TOTAL_".$clasificacionCco]['egresosF'], $row);
                            }
                            array_push($gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior][$clasificacionCco]['egresosF'], $row);
                         }
                    }else{
                         if( $incluirTodosDx == "si" and $sumarEnTotal ){
                             array_push($gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior]["TOTAL_".$clasificacionCco]['egresosA'], $row);
                         }
                         array_push($gruposEtareos[$keyGrupoEtareo]['datosGrupo'][$limiteSuperior][$clasificacionCco]['egresosA'], $row);
                    }

                 }else{

                 }
                 break;
            }
        }
    }

}

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
    foreach( $auxDiagnostico as $key => $datos ){

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

function obtenerArreglosEdades( $tipo_consultado, $arrayCcos ){

    global  $conex, $wemp_pmla, $wcliame;
    $arr_principal = array();

    //---> FILTRAR POR TIPO GRUPO ETAREO CONSULTADO.
    $query = " SELECT a.geecod, a.geedes, b.gedlii, b.gedlis, b.geddes
                 FROM {$wcliame}_000291 a
                INNER JOIN
                      {$wcliame}_000292 b ON ( a.Geecod = b.Gedcod )
                ORDER BY gedlii asc";
    $rs    = mysql_query( $query, $conex );
    while( $row = mysql_fetch_assoc( $rs ) ){

        $lisSecs = $row['gedlis']*360*24*60*60;

        if( !isset( $arr_principal[$row['geecod']] ) ){
            $arr_principal[$row['geecod']]                      = array();
            $arr_principal[$row['geecod']]['limitesEnSegundos'] = array();
            $arr_principal[$row['geecod']]['datosGrupo']        = array();
        }
        if( !isset($arr_principal[$row['geecod']]['datosGrupo'][$lisSecs]) )
            $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs] = array();

        $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs]['descripcion'] = $row['geddes'];
        foreach( $arrayCcos as $key=> $datos ){
            $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs][$key]['cantidad']    = 0;
            $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs][$key]['M']           = 0;
            $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs][$key]['F']           = 0;
            $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs][$key]['A']           = 0;
            $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs][$key]['egresosM']     = array();
            $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs][$key]['egresosF']     = array();
            $arr_principal[$row['geecod']]['datosGrupo'][$lisSecs][$key]['egresosA']     = array();
        }

        array_push( $arr_principal[$row['geecod']]['limitesEnSegundos'], $lisSecs );
    }

    return( $arr_principal );

}

function crearArreglosClasificatorios( $condicion, $condicionCCo ){

    global $conex, $wmovhos, $ccoUci, $incluirTodosDx;
    $array_respuesta                         = array();
    $array_respuestaContenedora              = array();
    $condicionCcoConsultado                  = "";
    $arrayCcosConsultadosAux                 = array();
    $ccosTipoReales                          = array();

    if( $condicion == "" ){
        if( $condicionCCo == "" or $condicionCCo == "ccourg" ){

            $array_respuesta['urgencias']['ccos']       = array();
            $array_respuesta['urgencias']['total']       = false;
            if( $incluirTodosDx == "si" ){
                 $array_respuesta['TOTAL_urgencias']['ccos']       = array();
                 $array_respuesta['TOTAL_urgencias']['total']       = false;
            }
        }

        if( $condicionCCo == "" or $condicionCCo == "ccohos" ){
            $array_respuesta['hospitalizacion']['ccos'] = array();
            $array_respuesta['hospitalizacion']['total'] = false;
            if( $incluirTodosDx == "si" ){
                $array_respuesta['TOTAL_hospitalizacion']['ccos'] = array();
                $array_respuesta['TOTAL_hospitalizacion']['total'] = false;
            }
        }
    }

    if( $condicion == "sep_uci_mue" ){//--> separar uci y muertes como servicios
        if( $condicionCCo == "" or $condicionCCo == "ccourg" ){
            $array_respuesta['urgencias']['ccos']       = array();
            $array_respuesta['urgencias']['total']       = false;
            if( $incluirTodosDx == "si" ){
                $array_respuesta['TOTAL_urgencias']['ccos']       = array();
                $array_respuesta['TOTAL_urgencias']['total']       = false;
            }
        }
        if( $condicionCCo == "" or $condicionCCo == "ccohos" ){
            $array_respuesta['hospitalizacion']['ccos'] = array();
            $array_respuesta['hospitalizacion']['total'] = false;
            if( $incluirTodosDx == "si" ){
                $array_respuesta['TOTAL_hospitalizacion']['ccos'] = array();
                $array_respuesta['TOTAL_hospitalizacion']['total'] = false;
            }
            $array_respuesta['UCI']['ccos'] = array();
            $array_respuesta['UCI']['total'] = false;
            if( $incluirTodosDx == "si" ){
                $array_respuesta['TOTAL_UCI']['ccos'] = array();
                $array_respuesta['TOTAL_UCI']['total'] = false;
            }
        }
        $array_respuesta['muertes']['ccos'] = array();
        $array_respuesta['muertes']['total'] = false;
        if( $incluirTodosDx == "si" ){
            $array_respuesta['TOTAL_muertes']['ccos'] = array();
            $array_respuesta['TOTAL_muertes']['total'] = false;
        }

    }

    $query = " SELECT ccohos, ccourg, ccocod
                 FROM {$wmovhos}_000011
                WHERE ccourg = 'on'
                   OR ccohos = 'on'
                GROUP BY 1,2,3";
    $rs    = mysql_query( $query, $conex );

    if( !isset($array_respuesta['hospitalizacion']['ccos']) )
        $array_respuesta['hospitalizacion']['ccos'] = array();
    if( !isset($array_respuesta['urgencias']['ccos']) )
        $array_respuesta['urgencias']['ccos'] = array();
    if( !isset($array_respuesta['TOTAL_urgencias']['ccos']) )
        $array_respuesta['TOTAL_urgencias']['ccos'] = array();
    while( $row = mysql_fetch_assoc($rs) ){


        if( $row['ccohos'] == "on" ){
            $ccosTipoReales[$row['ccocod']] = "ccohos";
        }else if( $row['ccourg'] == "on" ){
            $ccosTipoReales[$row['ccocod']] = "ccourg";
        }

        if( $condicion == "" ){

            if( $row['ccohos'] == "on" ){
                array_push($array_respuesta['hospitalizacion']['ccos'], $row['ccocod'] );
                if( $incluirTodosDx == "si" )
                    array_push($array_respuesta['TOTAL_hospitalizacion']['ccos'], $row['ccocod'] );
            }else if( $row['ccourg'] == "on" ){
                array_push($array_respuesta['urgencias']['ccos'], $row['ccocod'] );
                if( $incluirTodosDx == "si" )
                    array_push($array_respuesta['TOTAL_urgencias']['ccos'], $row['ccocod'] );
            }

        }

        if( $condicion == "sep_uci_mue" ){//--> separar uci y muertes como servicios

            if( $row['ccocod'] == $ccoUci and ($condicionCCo == "" or $condicionCCo == "ccohos" ) ){
                array_push($array_respuesta['UCI']['ccos'], $row['ccocod'] );
                if( $incluirTodosDx == "si" )
                    array_push($array_respuesta['TOTAL_UCI']['ccos'], $row['ccocod'] );
            }else{
                if( $row['ccohos'] == "on" and ($condicionCCo == "" or $condicionCCo == "ccohos" ) ){
                    array_push($array_respuesta['hospitalizacion']['ccos'], $row['ccocod'] );
                    if( $incluirTodosDx == "si" )
                        array_push($array_respuesta['TOTAL_hospitalizacion']['ccos'], $row['ccocod'] );
                }else if( $row['ccourg'] == "on" and ($condicionCCo == "" or $condicionCCo == "ccourg" ) ) {
                    array_push($array_respuesta['urgencias']['ccos'], $row['ccocod'] );
                    if( $incluirTodosDx == "si" )
                        array_push($array_respuesta['TOTAL_urgencias']['ccos'], $row['ccocod'] );
                }
            }
        }
        if( $condicionCCo != ""  and $row[$condicionCCo] == "on" ){
            array_push( $arrayCcosConsultadosAux, "'".$row['ccocod']."'" );
        }
    }
    if( count( $arrayCcosConsultadosAux ) > 0 ){
        $condicionCcoConsultado = " WHERE sercod IN (".implode( ", ",$arrayCcosConsultadosAux ).") ";
    }
    $array_respuestaContenedora['arrayCcosMostrados']     = $array_respuesta;
    $array_respuestaContenedora['condicionCcoConsultado'] = $condicionCcoConsultado;
    $array_respuestaContenedora['clasificacionReal']      = $ccosTipoReales;
    return( $array_respuestaContenedora );
}
//---------------------------------------> FIN FUNCIONES <-------------------------------------------------------------------//
?>

<!DOCTYPE html>
<html>
    <head>
        <title>REPORTE DIAGNOSTICOS POR GRUPOS ETAREOS </title>
    </head>
    <!--<meta charset="UTF-8">-->
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
                url  : "rep_etareos.php",
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
                url  : "rep_etareos.php",
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
                    difSexo        : $("input[type='radio'][name='diferenciarSexo']:checked").val(),
                    incluirTodosDx : $("input[type='radio'][name='incluirTodosDx']:checked").val(),
                    condicionCCo   : $("#lista_grupos > option:selected").val()
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

    <?php encabezado( " REPORTE DIAGNOSTICOS POR GRUPOS HETAREOS ", $wactualiz, "clinica" ); ?>
    <br><br><br>

    <input type='hidden' id='wemp_pmla'       value='<?=$wemp_pmla;?>'>
    <input type='hidden' id='fecha_hoy'       value='<?=date("Y-m-d");?>'>
    <input type='hidden' name='array_ccos' id='array_ccos' value='<?=$centrosCostos?>'>

    <div id='div_formulario' align='center' class='div_formulario'>
        <span class="subtituloPagina2">Par&aacute;metros de consulta</span><br><br>
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

            <tr>
                <td class='subEncabezado fila1'> SERVICIOS: </td>
                <!--<td class='fila2'> <input type='text' id='input_cco' keyup='validarVacio( this );' size='30' > <input type='hidden' id='wcco' name='wcco' value=''></td>-->
                <td class='fila2'>
                    <select id='lista_grupos'  align='center' style='".$width_sel." margin:5px;'>
                        <option value=''>TODOS</option>
                         <?php
                            foreach ($wgruposccos as $codigog=>$nombreg)
                                echo "<option value='".$codigog."' tipoOpcion='grupo'>".$nombreg."</option>";
                        ?>
                        <?php
                            /*foreach ($wccosCirIndividuales as $codigoCco=>$nombreCco)
                               echo "<option value='".$codigoCco."' tipoOpcion='cco'>".$nombreCco."</option>";*/
                        ?>
                    </SELECT>
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
                <td class='subEncabezado fila1'> INCLUIR TOTALIDAD DIAGNOSTICOS: </td>
                <!--<td class='fila2'> <input type='text' id='input_cco' keyup='validarVacio( this );' size='30' > <input type='hidden' id='wcco' name='wcco' value=''></td>-->
                <td class='fila2'>
                    <input type='radio' name='incluirTodosDx' value='si'>  SI.   &nbsp;&nbsp; <input type='radio' checked name='incluirTodosDx' value='no'> NO.
                </td>
            </tr>

            <tr>
                <td class='subEncabezado fila1'> DIFERENCIAR SEXO: </td>
                <!--<td class='fila2'> <input type='text' id='input_cco' keyup='validarVacio( this );' size='30' > <input type='hidden' id='wcco' name='wcco' value=''></td>-->
                <td class='fila2'>
                    <input type='radio' name='diferenciarSexo'  checked value='si'> SI.   &nbsp;&nbsp; <input type='radio' name='diferenciarSexo'  value='no'> NO.
                </td>
            </tr>

            <tr>
                <td class='subEncabezado fila1'> PERIODO: </td><td class='fila2'><input id='fecha_inicio' disabled size='12' type='text' value='<?=date("Y-m-d");?>'> Hasta <input id='fecha_final' size='12' disabled type='text' value='<?=date("Y-m-d");?>'></td>
            </tr>
        </table>
        <br>
        <input type="button" onclick="generarReporte();" value="BUSCAR" class="botona" id="btn_consultar">
    </div><br><br><br>
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