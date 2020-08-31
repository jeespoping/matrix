<?php
include_once("conex.php");
include_once("root/comun.php");

$caracteres    = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£");
$caracteres2   = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U");
function archivoLog($log, $num, $reset=false)
{
    /*if($reset)
    {
        $fp = fopen("log.txt","w+");
    }else
    {
        $fp = fopen("log.txt","a+");
    }
    fwrite($fp, $num.":**:".PHP_EOL.(print_r($log,true)).PHP_EOL.PHP_EOL);
    fclose($fp);*/
}

if(isset($accion))
{


    $data = array('error'=>0,'mensaje'=>'','html'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'iniciar_reporte':
                // Consultar con los mismos filtros del reporte sólo la información de terceros.
                $nombre_tercero_generando = '';
                $generando_tercero = "";
                {
                    $data['html'] .= "
                        <table  align=center width='60%'>
                            <tr><td>&nbsp;</td></tr>
                            <!-- <tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=340 HEIGHT=100></td></tr> -->
                            <tr><td>&nbsp;</td></tr>
                            <tr><td><B>Fecha: " . date('Y-m-d') . "</B></td></tr>";
                    switch ($vol)
                    {
                        case 1:
                            $data['html'] .= "<tr><td><B>Saldo para Liquidacion de facturado para empresa</B></td></tr>";
                            break;

                        case 2:
                            $data['html'] .= "<tr><td><B>Saldo para Liquidacion de facturado para particular</B></td></tr>";
                            break;

                        case 3:
                            $data['html'] .= "<tr><td><B>Saldo para Liquidacion de facturado para empresas y particulares</B></td></tr>";
                            break;
                    }

                    $data['html'] .= "
                            </tr><td align=right ><A href='penter.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wter=" . $wter . "&amp;bandera='1'>VOLVER</A></td></tr>
                            <tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>
                            <tr><td><tr><td>Fecha inicial: " . $wfecini . "</td></tr>
                            <tr><td>Fecha final: " . $wfecfin . "</td></tr>
                            </table></br>
                            <input type='HIDDEN' NAME= 'wfecini' value='" . $wfecini . "'>
                            <input type='HIDDEN' NAME= 'wfecfin' value='" . $wfecfin . "'>
                            <input type='HIDDEN' NAME= 'wemp' value='" . $wter . "'>
                            <input type='HIDDEN' NAME= 'bandera' value='1'>

                            <div id='div_contenedor_terceros'></div>";

                    if ($wter != '%-Todos los terceros')
                    {
                        $print = explode('-', $wter);
                        $ter = trim ($print[0]);
                    }
                    else
                    {
                        $ter = '%';
                    }
                    // se organiza el vector de conceptos que tambien se va a recorrer dentro del for de terceros
                    if ($wcon != '%-Todos los conceptos')
                    {
                        $print = explode('-', $wcon);
                        $con = trim ($print[0]);
                    }
                    else
                    {
                        $con = '%';
                    }

                    $filtroTercero='';
                    $filtroConcepto='';
                    if($ter!='%')
                    {
                        $filtroTercero="AND fdeter = '" . $ter . "' ";
                    }
                    if($con!='%')
                    {
                        $filtroConcepto="AND fdecon = '" . $con . "' ";
                    }
                    // busco todas las facturas generadas en el rango de fechas

                    $filtro_tipo_particular = "";
                    switch ($vol)
                    {
                        case 1:
                             $filtro_tipo_particular = "AND fentip<>'01-PARTICULAR'";
                            break;

                        case 2:
                             $filtro_tipo_particular = "AND fentip='01-PARTICULAR'";
                            break;

                        case 3:
                            break;
                    }

                    $q = "  SELECT  c18.fenfac, c18.fenffa, c65.fdeter, c65.fdecon, c51.Mednom, c18.fenval, c18.fensal
                            FROM    {$wbasedato}_000018 AS c18
                                    INNER JOIN
                                    {$wbasedato}_000065 AS c65 ON ( c65.fdedoc = c18.fenfac AND c65.fdefue=c18.fenffa)
                                    INNER JOIN
                                    {$wbasedato}_000051 AS c51 ON (c65.fdeter = c51.Meddoc AND c65.fdeest= 'on')
                            WHERE   c18.fenfec  BETWEEN  '{$wfecini}' AND '{$wfecfin}'
                                    AND c18.fenest = 'on'
                                    AND c18.fencco <> ''
                                    AND c18.fenval > 0
                                    {$filtro_tipo_particular}
                                    {$filtroTercero}
                                    {$filtroConcepto}
                            GROUP BY  c65.fdeter
                            ORDER BY  c51.Mednom,c65.fdecon,c18.fenfac,c18.fenffa";

                    // echo "<pre>"; print_r($q); echo "</pre>";exit();

                    $arr_terceros = array();
                    // $num3 = mysql_num_rows($err2);
                    if($err2 = mysql_query($q, $conex))
                    {
                        if(mysql_num_rows($err2) > 0)
                        {
                            while($row = mysql_fetch_array($err2))
                            {
                                $cod_tercero = $row['fdeter'];
                                if($cod_tercero != '' && $cod_tercero != '.' && $cod_tercero != '0' && $cod_tercero != 'NO APLICA')
                                {
                                    if(!array_key_exists($cod_tercero, $arr_terceros))
                                    {
                                        $arr_terceros[$cod_tercero] = str_replace( $caracteres, $caracteres2, $row2['Mednom'] );
                                        if($nombre_tercero_generando == '')
                                        {
                                            $nombre_tercero_generando = $cod_tercero.'-'.str_replace( $caracteres, $caracteres2, $row2['Mednom'] );
                                            $generando_tercero = $cod_tercero;
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            $data['error']   = 1;
                            $data['mensaje'] = utf8_encode("No se encontraron resultados de terceros que coincidan con la búsqueda");
                        }
                    }
                    else
                    {
                        $data['html']    = $q;
                        $data['error']   = 1;
                        $data['mensaje'] = utf8_encode("[!] Error al consultar Terceros, no se pudo generar el reporte.");
                    }
                    $data['nombre_tercero_generando'] = ucwords($nombre_tercero_generando);
                    $data['generando_tercero']        = $generando_tercero;
                    $data['arr_terceros']             = base64_encode(serialize($arr_terceros));
                    $data['cantidad_terceros']        = count($arr_terceros);
                    liberarConexionBD($conex);
                }
        break;

        case 'consultar_tercero':
                $html = "";
                $arr_terceros = unserialize(base64_decode($arr_terceros));
                $arr_key = array_keys($arr_terceros);
                $tercero_pila = $arr_key[0];
                $nombre_tercero_generando = '';
                $tercero_actual_llamado  = $generando_tercero;

                if(!isset($arr_totales) || (!is_array($arr_totales) && $arr_totales == ''))
                {
                    $arr_totales = array();
                    $arr_totales['valff']  = 0;
                    $arr_totales['salff']  = 0;
                    $arr_totales['fval']   = 0;
                    $arr_totales['fdes']   = 0;
                    $arr_totales['fdeb']   = 0;
                    $arr_totales['fcre']   = 0;
                    $arr_totales['valemp'] = 0;
                    $arr_totales['valpar'] = 0;
                    $arr_totales['fpag']   = 0;
                }
                else
                {
                    $arr_totales = unserialize(base64_decode($arr_totales));
                }

                $generando_tercero = "";
                if(array_key_exists(1, $arr_key))
                {
                    $generando_tercero = $arr_key[1];
                    $nombre_tercero_generando = ucwords($generando_tercero.'-'.$arr_terceros[$generando_tercero]);
                }

                {
                    $cod_tercero = $tercero_pila;

                    if ($wter != '%-Todos los terceros')
                    {
                        $print = explode('-', $wter);
                        $ter = trim ($print[0]);
                    }
                    else
                    {
                        $ter = '%';
                    }
                    // se organiza el vector de conceptos que tambien se va a recorrer dentro del for de terceros
                    if ($wcon != '%-Todos los conceptos')
                    {
                        $print = explode('-', $wcon);
                        $con = trim ($print[0]);
                    }
                    else
                    {
                        $con = '%';
                    }

                    $filtroTercero='';
                    $filtroConcepto='';
                    if($ter!='%')
                    {
                        $filtroTercero="AND fdeter = '" . $ter . "' ";
                    }
                    if($con!='%')
                    {
                        $filtroConcepto="AND fdecon = '" . $con . "' ";
                    }
                    // busco todas las facturas generadas en el rango de fechas

                    $filtro_tipo_particular = "";
                    switch ($vol)
                    {
                        case 1:
                             $filtro_tipo_particular = "AND fentip<>'01-PARTICULAR'";
                            break;

                        case 2:
                             $filtro_tipo_particular = "AND fentip='01-PARTICULAR'";
                            break;

                        case 3:
                            break;
                    }

                    $q = "  SELECT  c18.fenfac, c18.fenffa, c65.fdeter, c65.fdecon, c51.Mednom, c18.fenval, c18.fensal
                            FROM    {$wbasedato}_000018 AS c18
                                    INNER JOIN
                                    {$wbasedato}_000065 AS c65 ON (c65.fdeter = '{$cod_tercero}' AND c65.fdedoc = c18.fenfac AND c65.fdefue=c18.fenffa)
                                    INNER JOIN
                                    {$wbasedato}_000051 AS c51 ON (c65.fdeter = c51.Meddoc AND c65.fdeest= 'on')
                            WHERE   c18.fenfec  BETWEEN  '{$wfecini}' AND '{$wfecfin}'
                                    AND c18.fenest= 'on'
                                    AND c18.fencco<>''
                                    AND c18.fenval>0
                                    {$filtro_tipo_particular}
                                    {$filtroTercero}
                                    {$filtroConcepto}
                            GROUP BY  c65.fdecon,c65.fdeter,c18.fenfac,c18.fenffa
                            ORDER BY  c51.Mednom,c65.fdecon,c18.fenfac,c18.fenffa";
                    /*if( $tercero_actual_llamado == "71776133" ){
                        echo "edb 5 ->".$tercero_pila;
                        print_r($q);
                    }*/
                    //archivoLog($q, '1');
                    // $fp = fopen("log.txt","a+");
                    // fwrite($fp, print_r($q,true).PHP_EOL.PHP_EOL);
                    // fclose($fp);
                    //$query = $q;
                    // echo "<pre>"; print_r($q); echo "</pre>";exit();
                    $err2 = mysql_query($q, $conex);
                    $num3 = mysql_num_rows($err2);
                    $j = 0;
                    $numfac2 = array();
                    $fuefac2 = array();
                    $terfac2 = array();
                    $confac2 = array();
                    $valfac2 = array();
                    $salfac2 = array();
                    $nomfac2 = array();
                    // el resultado lo debo organizar en un vector y consultar el saldo a la fecha de corte
                    // for ($l = 0;$l < $num3;$l++)

                    while ($row2 = mysql_fetch_array($err2))
                    {
                        // $row2 = mysql_fetch_array($err2);
                        $q = "  SELECT  c21.rdesfa, c21.rdefue, c21.rdenum, c21.rdefac, c21.rdeffa
                                FROM    ".$wbasedato."_000021 AS c21
                                        INNER JOIN
                                        ".$wbasedato."_000020 AS c20 ON (c20.renfue=c21.rdefue AND c20.rennum=c21.rdenum AND c20.rencco=c21.rdecco AND c20.renfec <= '".$wfecfin."')
                                WHERE   c21.rdefac= '".$row2['fenfac']."'
                                        AND c21.rdeffa= '".$row2['fenffa']."'
                                        AND c21.rdeest= 'on'
                                        AND c21.rdesfa<>''
                                        AND c21.rdereg=0
                                ORDER BY  c21.id DESC";

                        //archivoLog($q, '2');
                        // echo "<pre>"; print_r($q); echo "</pre>";exit();
                        $err3 = mysql_query($q, $conex);
                        $y = mysql_num_rows($err3);
                        // $y = $row2['rdesfa'];
                        $row3 = mysql_fetch_array($err3);

                        if ($y > 0)
                        {
                            if ($row3[0] > 0)
                            {
                                $q = "  SELECT  MIN(cast(rdesfa as UNSIGNED)) AS rdesfa
                                        FROM    " . $wbasedato . "_000021
                                        WHERE   rdefac= '" . $row3['rdefac'] . "'
                                                AND rdeffa= '" . $row3['rdeffa'] . "'
                                                AND rdeest= 'on'
                                                AND rdesfa<>''
                                                AND rdereg=0
                                                AND rdefue='" . $row3['rdefue'] . "'
                                                AND rdenum='" . $row3['rdenum'] . "'
                                                group by rdenum, rdefue";
                                // echo "<pre>"; print_r($q); echo "</pre>";exit();

                                 //archivoLog($q, '3');
                                $err3 = mysql_query($q, $conex);
                                $row3 = mysql_fetch_array($err3);
                            }
                        }

                        if ($y > 0)
                        {
                            if ($row3[0] > 0)
                            {
                                $numfac2[$j] = $row2['fenfac'];
                                $fuefac2[$j] = $row2['fenffa'];
                                $terfac2[$j] = $row2['fdeter'];
                                $confac2[$j] = $row2['fdecon'];
                                $valfac2[$j] = $row2['fenval'];
                                $salfac2[$j] = $row3[0];
                                $nomfac2[$j] = str_replace( $caracteres, $caracteres2, $row2['Mednom'] );
                                $j++;
                            }
                        }
                        else
                        {
                            $numfac2[$j] = $row2['fenfac'];
                            $fuefac2[$j] = $row2['fenffa'];
                            $terfac2[$j] = $row2['fdeter'];
                            $confac2[$j] = $row2['fdecon'];
                            $valfac2[$j] = $row2['fenval'];
                            $salfac2[$j] = $row2['fenval'];
                            $nomfac2[$j] = str_replace( $caracteres, $caracteres2, $row2['Mednom'] );
                            $j++;
                        }
                    }

                    /*if($tercero_pila == '13501564')
                    {
                        // echo "<pre>l: "; print_r($l); echo "</pre>";
                        echo "<pre>J: "; print_r($j); echo "</pre>";
                        echo "<pre>"; print_r($numfac2); echo "</pre>";
                        echo "<pre>"; print_r(count($fuefac2)); echo "</pre>";
                        echo "<pre>"; print_r(count($terfac2)); echo "</pre>";
                        echo "<pre>"; print_r(count($confac2)); echo "</pre>";
                        echo "<pre>"; print_r(count($valfac2)); echo "</pre>";
                        echo "<pre>"; print_r(count($salfac2)); echo "</pre>";
                        echo "<pre>"; print_r(count($nomfac2)); echo "</pre>";
                        echo "<pre>"; print_r($query); echo "</pre>";
                        exit();
                    } */

                    $clase1 = "class='texto1'";
                    $clase2 = "class='texto4'";
                    $pinfin = 0; //indica si debe mostrar totales cuando esta en uno
                    $valff = 0;
                    $salff = 0;
                    $fval = 0;
                    $fdes = 0;
                    $fdeb = 0;
                    $fcre = 0;
                    $fpag = 0;
                    $pinfin = 0; //indica si debe mostrar totales
                    $valemp = 0;
                    $valpar = 0;

                    $pinter = 0; //indica si debe mostrar totales de tercero cuando esta en uno
                    $teran = 0;
                    $conan = 0;

                    $i = 0;
                    $j = 0;

                    for ($k = 0;$k < count($numfac2);$k++) // se recorren las facturas
                    {
                        $q = " SELECT   fdevco, fdepte, fdevde, fdecco
                               FROM     " . $wbasedato . "_000065
                               WHERE    fdedoc= '" . $numfac2[$k] . "'
                                        AND fdefue= '" . $fuefac2[$k] . "'
                                        AND fdeest= 'on'
                                        AND fdeter='" . $terfac2[$k] . "'
                                        AND fdecon = '" . $confac2[$k] . "'  ";

                        //archivoLog($q, '4');
                        $err2 = mysql_query($q, $conex);
                        $y = mysql_num_rows($err2);

                        if ($y > 0) // si se encuentra el registro, se pinta
                        {
                            for ($t = 0;$t < $y;$t++) // se recorren los conceptos
                            {
                                if ($teran != $terfac2[$k])
                                {
                                    if ($pinter > 1)
                                    {
                                        $html .= "<tr>
                                                  <th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' colspan='5' >TOTAL CONCEPTO</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($valfc, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($salfc, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cval, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cdes, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($ccre, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >&nbsp;</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cpag, 0, '.', ',') . "</th><tr>";
                                        // realizo la suma o acumulacion de todos los valores para el tercero
                                        $valft = $valft + $valfc;
                                        $salft = $salft + $salfc;
                                        $tval = $tval + $cval;
                                        $tdes = $tdes + $cdes;
                                        $tdeb = $tdeb + $cdeb;
                                        $tcre = $tcre + $ccre;
                                        $tpag = $tpag + $cpag;

                                        $pinter++;

                                        $html .= "<tr>
                                                  <th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' colspan='5' >TOTAL TERCERO</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($valft, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($salft, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tval, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tdes, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tdeb, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tcre, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >&nbsp;</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tpag, 0, '.', ',') . "</th><tr>";
                                        // realizo la suma o acumulacion de todos los valores finales
                                        $valff = $valff + $valft;
                                        $salff = $salff + $salft;
                                        $fval = $fval + $tval;
                                        $fdes = $fdes + $tdes;
                                        $fdeb = $fdeb + $tdeb;
                                        $fcre = $fcre + $tcre;
                                        $fpag = $fpag + $tpag;

                                        $html .= "</table></br>";

                                        $html .= "<table align='center' >";
                                        $html .= "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A EMPRESAS: </th>";
                                        $html .= "<th align=CENTER class='acumulado6' >" . number_format($talemp, 0, '.', ',') . "</th>";
                                        $html .= "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A PARTICULARES: </th>";
                                        $html .= "<th align=CENTER class='acumulado6' >" . number_format($talpar, 0, '.', ',') . "</th><TR>";
                                        $html .= "<TR><th align=CENTER class='acumulado5' >=VALOR FACTURADO TOTAL: </th>";
                                        $html .= "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><TR>";

                                        $html .= "<TR><th align=CENTER class='acumulado5' >= TOTAL A PAGAR: </th>";
                                        $html .= "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><TR>";
                                        $html .= "</table></br>";
                                    }

                                    $html .= "<table  align=center width='1000'>";
                                    $html .= "<tr class='encabezadoTabla'><td style='font-size:13pt;text-align:center;' colspan=13><B>Tercero:</B> " . $terfac2[$k] . "-" . $nomfac2[$k] . "</td></tr>";
                                    $conan  = 0;
                                    $teran  = $terfac2[$k];
                                    $pinfin = 1;
                                    $pinter = 1;

                                    $valfc  = 0;
                                    $salfc  = 0;
                                    $cval   = 0;
                                    $cdes   = 0;
                                    $cdeb   = 0;
                                    $ccre   = 0;
                                    $cpag   = 0;
                                    $pincon = 0;

                                    $valft  = 0;
                                    $salft  = 0;
                                    $tval   = 0;
                                    $tdes   = 0;
                                    $tdeb   = 0;
                                    $tcre   = 0;
                                    $tpag   = 0;
                                    $talemp = 0;
                                    $talpar = 0;
                                }

                                if ($conan != $confac2[$k]) // se pone el titulo de la tabla en caso de ser la primera factura que se encuentra
                                {
                                    if ($pincon != 0)
                                    {
                                        $html .= "<tr>
                                                  <th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' colspan='5' >TOTAL CONCEPTO</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($valfc, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($salfc, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cval, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cdes, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($ccre, 0, '.', ',') . "</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >&nbsp;</th>";
                                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cpag, 0, '.', ',') . "</th><tr>";
                                        // realizo la suma o acumulacion de todos los valores para el tercero
                                        $valft = $valft + $valfc;
                                        $salft = $salft + $salfc;
                                        $tval = $tval + $cval;
                                        $tdes = $tdes + $cdes;
                                        $tdeb = $tdeb + $cdeb;
                                        $tcre = $tcre + $ccre;
                                        $tpag = $tpag + $cpag;
                                        $pincon = 1;
                                        $pinter++;

                                        $valfc = 0;
                                        $salfc = 0;
                                        $cval = 0;
                                        $cdes = 0;
                                        $cdeb = 0;
                                        $ccre = 0;
                                        $cpag = 0;
                                    }
                                    else
                                    {
                                        $pincon = 1;
                                        $pinter++;
                                    }

                                    $q = "  SELECT  grutip, grudes
                                            FROM    " . $tablaConceptos . "
                                            WHERE   gruest= 'on' and grucod='" . $confac2[$k] . "' ";

                                    //archivoLog($q, '5');

                                    $gru = mysql_query($q, $conex);
                                    $tip = mysql_fetch_row($gru);

                                    if ($tip[0] == 'C')
                                    {
                                        $html .= "<tr>
                                                  <td style='background-color:#C6C6C6;font-size:10pt;' class='' COLSPAN=13 ><B>Concepto:</B> " . $confac2[$k] . "-" . $tip[1] . "</td></tr>";
                                        $html .= "<tr class='encabezadoTabla'><td align=CENTER class=''><B>NUMERO FACTURA</B></td>";
                                        $html .= "<td align=CENTER class=''><B>EMPRESA DE LA FACTURA</B></td>";
                                        $html .= "<td align=CENTER class=''><B>PACIENTE</B></td>";
                                        $html .= "<td align=CENTER class=''><B>HISTORIA</B></td>";
                                        $html .= "<td align=CENTER class=''><B>FECHA CARGO</B></td>";
                                        $html .= "<td align=CENTER class=''><B>VALOR FACTURA</B></td>";
                                        $html .= "<td align=CENTER class=''><B>SALDO FACTURA</B></td>";
                                        $html .= "<td align=CENTER class=''><B>VALOR CARGO</B></td>";
                                        $html .= "<td align=CENTER class=''><B>VALOR DESCUENTO</B></td>";
                                        $html .= "<td align=CENTER class=''><B>NOTAS DEBITO</B></td>";
                                        $html .= "<td align=CENTER class=''><B>NOTAS CREDITO</B></td>";
                                        $html .= "<td align=CENTER class=''><B>% DE PARTICIPACION</B></td>";
                                        $html .= "<td align=CENTER class=''><B>VALOR A PAGAR</B></td></tr>";
                                    }
                                    $conan = $confac2[$k];
                                }

                                if ($tip[0] == 'C')
                                {
                                    if ($clase1 == "class='texto1'")
                                    {
                                        $clase1 = "class='texto6'";
                                        $clase2 = "class='texto7'";
                                    }
                                    else
                                    {
                                        $clase1 = "class='texto1'";
                                        $clase2 = "class='texto4'";
                                    }

                                    $clase1_ = '';
                                    $clase2_ = '';

                                    $css = ($k % 2 == 0) ? 'fila1': 'fila2';

                                    $html .= "<tr class='".$css."'><td align=CENTER " . $clase1_ . " width='10%'>" . $fuefac2[$k] . "-" . $numfac2[$k] . "</td>";
                                    // vamos a buscar la empresa, el paciente y la historia de la factura
                                    $q = " SELECT   a.fendpa, a.fennpa, fenhis, empcod, empnom, fentip
                                           FROM     " . $wbasedato . "_000018 a, " . $wbasedato . "_000024 b
                                           WHERE    fenffa='" . $fuefac2[$k] . "' and fenfac='" . $numfac2[$k] . "' and fenest='on'
                                                    AND fencod=empcod
                                                    AND empcod=empres
                                                    AND empest='on' ";

                                     //archivoLog($q, '6');
                                    $err3 = mysql_query($q, $conex);
                                    $row3 = mysql_fetch_array($err3);

                                    $html .= "<td align=CENTER " . $clase1_ . " width='10%'>" . $row3[3] . "-" . utf8_encode($row3[4]) . "</td>";
                                    $html .= "<td align=CENTER " . $clase1_ . " width='10%'>" . $row3[0] . "-" . utf8_encode($row3[1]) . "</td>";
                                    $html .= "<td align=CENTER " . $clase1_ . " width='10%'>" . $row3[2] . "</td>";
                                    // vamos a buscar la fecha del cargo
                                    $q = "  SELECT  tcarfec
                                            FROM    ".$wbasedato."_000066 a,".$wbasedato."_000106 b
                                            WHERE   rcfffa='" . $fuefac2[$k] . "'
                                                    AND rcffac='" . $numfac2[$k] . "'
                                                    AND rcfest='on'
                                                    AND b.id=rcfreg ";

                                                    // AND b.Tcartercod = '{$cod_tercero}'
                                    //archivoLog($q, '7');

                                    $err4 = mysql_query($q, $conex);
                                    $row4 = mysql_fetch_array($err4);

                                    $html .= "<td align=CENTER " . $clase1_ . " width='10%'>" . $row4[0] . "</td>";
                                    // ahora pitamos los datos propios del cargo (valor, descuento y porcentaje)
                                    $row2 = mysql_fetch_array($err2);

                                    $html .= "<td align=left " . $clase1_ . " width='10%'>" . number_format($valfac2[$k], 0, '.', ',') . "</td>";
                                    $html .= "<td align=left " . $clase1_ . " width='10%'>" . number_format($salfac2[$k], 0, '.', ',') . "</td>";
                                    $html .= "<td align=left " . $clase2_ . " width='10%'>" . number_format($row2[0], 0, '.', ',') . "</td>";
                                    $html .= "<td align=left " . $clase2_ . " width='10%'>" . number_format($row2[2], 0, '.', ',') . "</td>";
                                    // ahora debo buscar el valor de las notas debito a ese cargo
                                    // consulto las fuentes para notas debito
                                    $q = "  SELECT carfue
                                            FROM " . $wbasedato . "_000040
                                            WHERE carndb = 'on'" ;

                                    //archivoLog($q, '8');
                                    $errdeb = mysql_query($q, $conex);
                                    $numdeb = mysql_num_rows($errdeb);
                                    $wvaldeb = 0;

                                    for ($d = 1;$d <= $numdeb;$d++) // para cada fuente busco notas
                                    {
                                        $rowdeb = mysql_fetch_array($errdeb);
                                        // consulto las notas debito por concepto de facturación y voy sumando
                                        /*   // ORIGINAL SIN OPTIMIZAR
                                        $q = "  SELECT  sum(fdevco)
                                                FROM    " . $wbasedato . "_000021 A, " . $wbasedato . "_000065
                                                WHERE   rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "'
                                                        and rdefue='" . $rowdeb[0] . "' AND rdeest='on' and rdecon=''
                                                        and rdevco='0' and fdefue= rdefue and fdedoc= rdenum and fdecon= '" . $confac2[$k] . "'
                                                        and fdeter= '" . $terfac2[$k] . "' and fdeest='on' and fdecco= '" . $row2[3] . "'
                                                        and  A.Fecha_data<='" . $wfecfin . "' ";*/


                                        /*// SIGUE QUERY LENTO CUANDO NO ENCUENTRA PARIDAD EN TABLAS CRUZADAS
                                        $q2 = "  SELECT  SUM(c65.fdevco)
                                                FROM    {$wbasedato}_000021 AS c21
                                                        INNER JOIN
                                                        {$wbasedato}_000065 AS c65 ON ( c65.fdefue= c21.rdefue
                                                                                        AND c65.fdedoc = c21.rdenum
                                                                                        AND c65.fdecco = '{$row2[3]}'
                                                                                        AND c65.fdecon = '{$confac2[$k]}'
                                                                                        AND c65.fdeter = '{$terfac2[$k]}')
                                                WHERE   c21.rdeffa = '{$fuefac2[$k]}'
                                                        AND c21.rdefac = '{$numfac2[$k]}'
                                                        AND c21.rdefue='{$rowcre[0]}'
                                                        AND c21.rdeest='on'
                                                        AND c21.rdecon=''
                                                        AND c21.rdevco='0'
                                                        AND c65.fdeest='on'
                                                        AND  c21.Fecha_data <= '{$wfecfin}'
                                                GROUP BY c21.rdeffa, c21.rdefac";*/

                                        //FUNCIONA BIEN LOS VALORES CREDITOS-DEBITOS
                                        $q = "  SELECT  c21.rdefue, c21.rdenum
                                                FROM    clisur_000021 AS c21
                                                WHERE   c21.rdeffa = '{$fuefac2[$k]}'
                                                        AND c21.rdefac = '{$numfac2[$k]}'
                                                        AND c21.rdefue='{$rowdeb[0]}'
                                                        AND c21.rdeest='on'
                                                        AND c21.rdecon=''
                                                        AND c21.rdevco='0'
                                                        AND  c21.Fecha_data <= '{$wfecfin}'";//GROUP BY c21.rdeffa, c21.rdefac

                                        $result21 = mysql_query($q, $conex) ;
                                        while($row21deb = mysql_fetch_array($result21))
                                        {
                                            // FUNCIONA BIEN LOS VALORES CREDITOS-DEBITOS
                                            $q = "  SELECT  sum(fdevco)
                                                    FROM    {$wbasedato}_000065 AS c65
                                                    WHERE   c65.fdefue= '{$row21deb['rdefue']}'
                                                            AND c65.fdedoc= '{$row21deb['rdenum']}'
                                                            AND c65.fdecco= '{$row2[3]}'
                                                            AND c65.fdecon= '{$confac2[$k]}'
                                                            AND c65.fdeter= '{$terfac2[$k]}'";
                                            $resdeb = mysql_query($q, $conex) ;
                                            $row2deb = mysql_fetch_row($resdeb);

                                            $wvaldeb = $row2deb + $row2deb[0]*1;
                                        }
                                        //$wvaldeb = $wvaldeb + $row2deb[0];
                                        $deb[$d] = $rowdeb[0];
                                    }

                                    // ahora debo buscar el valor de las notas credito a ese cargo
                                    $q = "  SELECT  carfue
                                            FROM    " . $wbasedato . "_000040
                                            WHERE   carncr = 'on'";

                                    //archivoLog($q, '10');

                                    $errcre = mysql_query($q, $conex);
                                    $numcre = mysql_num_rows($errcre);
                                    $wvalcre = 0; //para sumar el total de nota credito
                                    for ($c = 1;$c <= $numcre;$c++)
                                    {
                                        $rowcre = mysql_fetch_array($errcre);
                                        // consulto las notas credito por concepto de facturación y voy sumando
                                        /*  // ORIGINAL SIN OPTIMIZAR
                                        $q = "  SELECT  sum(fdevco)
                                                FROM    " . $wbasedato . "_000021 A, " . $wbasedato . "_000065
                                                WHERE   rdeffa = '" . $fuefac2[$k] . "'
                                                        AND rdefac = '" . $numfac2[$k] . "'
                                                        AND rdefue='" . $rowcre[0] . "'
                                                        AND rdeest='on'
                                                        AND rdecon=''
                                                        AND rdevco='0'
                                                        AND fdefue= rdefue
                                                        AND fdedoc= rdenum
                                                        AND fdecon= '" . $confac2[$k] . "'
                                                        AND fdeter= '" . $terfac2[$k] . "'
                                                        AND fdeest='on'
                                                        AND fdecco= '" . $row2[3] . "'
                                                        AND  A.Fecha_data<='" . $wfecfin . "' ";*/

                                        /*// SIGUE QUERY LENTO CUANDO NO ENCUENTRA PARIDAD EN TABLAS CRUZADAS
                                        $q2 = "  SELECT  SUM(c65.fdevco)
                                                FROM    {$wbasedato}_000021 AS c21
                                                        INNER JOIN
                                                        {$wbasedato}_000065 AS c65 ON ( c65.fdefue= c21.rdefue
                                                                                        AND c65.fdedoc = c21.rdenum
                                                                                        AND c65.fdecco = '{$row2[3]}'
                                                                                        AND c65.fdecon = '{$confac2[$k]}'
                                                                                        AND c65.fdeter = '{$terfac2[$k]}')
                                                WHERE   c21.rdeffa = '{$fuefac2[$k]}'
                                                        AND c21.rdefac = '{$numfac2[$k]}'
                                                        AND c21.rdefue='{$rowcre[0]}'
                                                        AND c21.rdeest='on'
                                                        AND c21.rdecon=''
                                                        AND c21.rdevco='0'
                                                        AND c65.fdeest='on'
                                                        AND  c21.Fecha_data <= '{$wfecfin}'
                                                GROUP BY c21.rdeffa, c21.rdefac";*/

                                        $q = "  SELECT  c21.rdefue, c21.rdenum
                                                FROM    clisur_000021 AS c21
                                                WHERE   c21.rdeffa = '{$fuefac2[$k]}'
                                                        AND c21.rdefac = '{$numfac2[$k]}'
                                                        AND c21.rdefue='{$rowcre[0]}'
                                                        AND c21.rdeest='on'
                                                        AND c21.rdecon=''
                                                        AND c21.rdevco='0'
                                                        AND  c21.Fecha_data <= '{$wfecfin}'";//GROUP BY c21.rdeffa, c21.rdefac
                                        $result21 = mysql_query($q, $conex) ;

                                        while($row21cre = mysql_fetch_array($result21))
                                        {
                                            // FUNCIONA BIEN LOS VALORES CREDITOS-DEBITOS
                                            $q = "  SELECT  sum(fdevco)
                                                    FROM    {$wbasedato}_000065 AS c65
                                                    WHERE   c65.fdefue= '{$row21cre['rdefue']}'
                                                            AND c65.fdedoc= '{$row21cre['rdenum']}'
                                                            AND c65.fdecco= '{$row2[3]}'
                                                            AND c65.fdecon= '{$confac2[$k]}'
                                                            AND c65.fdeter= '{$terfac2[$k]}'";
                                            $rescre = mysql_query($q, $conex) ;
                                            $row2cre = mysql_fetch_row($rescre);

                                            $wvalcre = $wvalcre + $row2cre[0]*1;
                                        }
                                        // $wvalcre = $wvalcre + $row2cre[0]*1;
                                        $cre[$c] = $rowcre[0];
                                    }

                                    // ahora voy a hallar el porcentaje para ese concepto
                                    $valor = $row2[0] + $wvaldeb - $wvalcre;

                                    if ($valfac2[$k] != 0)
                                    {
                                        $porcen = $valor * 100 / $valfac2[$k];
                                    }
                                    else
                                    {
                                        $porcen = 0;
                                    }

                                    // ahora vamos a consultar las notas debito por conceptos de cartera que cuentan por configuracion en el maestro
                                    for ($d = 1;$d <= count($deb);$d++) // para cada fuente busco notas
                                    {
                                        $q = "  SELECT  sum(rdevco)
                                                FROM    {$wbasedato}_000021 A, {$wbasedato}_000044
                                                WHERE   rdeffa = '{$fuefac2[$k]}'
                                                        AND rdefac = '{$numfac2[$k]}'
                                                        AND rdefue = '{$deb[$d]}'
                                                        AND rdeest = 'on'
                                                        AND rdecon <> ''
                                                        AND rdevco <> '0'
                                                        AND concod = (mid(rdecon,1,instr(rdecon,'-')-1))
                                                        AND conest= 'on'
                                                        AND conacp='on'
                                                        AND A.Fecha_data <= '{$wfecfin}'";

                                        //archivoLog($q, '12');

                                        $resdeb = mysql_query($q, $conex);
                                        $row2deb = mysql_fetch_row($resdeb); //para sumar el total de nota debito
                                        $wvaldeb = round($wvaldeb + ($row2deb[0]*1) * $porcen / 100);
                                    }

                                    // ahora vamos a consultar las notas credito por conceptos de cartera y decidir si se descuentan o no
                                    for ($d = 1;$d <= count($cre);$d++) // para cada fuente busco notas
                                    {
                                        $q = "  SELECT  sum(rdevco)
                                                FROM    {$wbasedato}_000021 A, {$wbasedato}_000044
                                                WHERE   rdeffa = '{$fuefac2[$k]}'
                                                        AND rdefac = '{$numfac2[$k]}'
                                                        AND rdefue = '{$cre[$d]}'
                                                        AND rdeest = 'on'
                                                        AND rdecon <> ''
                                                        AND rdevco <> '0'
                                                        AND concod = (mid(rdecon,1,instr(rdecon,'-')-1))
                                                        AND conest= 'on'
                                                        AND conacp='on'
                                                        AND A.Fecha_data <= '{$wfecfin}'";

                                        //archivoLog($q, '13');

                                        $rescre = mysql_query($q, $conex) ;
                                        $row2cre = mysql_fetch_row($rescre);
                                        $wvalcre = round($wvalcre + ($row2cre[0]*1) * $porcen / 100);
                                    }

                                    $html .= "<td align=left " . $clase1_ . " width='10%'>" . number_format($wvaldeb, 0, '.', ',') . "</td>";
                                    $html .= "<td align=left " . $clase1_ . " width='10%'>" . number_format($wvalcre, 0, '.', ',') . "</td>";
                                    $html .= "<td align=left " . $clase1_ . " width='10%'>" . number_format($row2[1], 0, '.', ',') . "</td>";
                                    // calculo el valor a pagar
                                    $wvalpag = round(($row2[0] + $wvaldeb - $wvalcre - $row2[2]) * $row2[1] / 100);
                                    $html .= "<td align=left " . $clase2_ . " width='10%'>" . number_format($wvalpag, 0, '.', ',') . "</td></tr>";
                                    // realizo la suma o acumulacion de todos los valores para el concepto
                                    $valfc = $valfc + $valfac2[$k];
                                    $salfc = $salfc + $salfac2[$k];
                                    $cval = $cval + $row2[0];
                                    $cdes = $cdes + $row2[2];
                                    $cdeb = $cdeb + $wvaldeb;
                                    $ccre = $ccre + $wvalcre;
                                    $cpag = $cpag + $wvalpag;
                                    $pincon = 1;

                                    // realizo la acumulacion del valor a pagar para empresa o particular
                                    if ($row3[5] == '01-PARTICULAR')
                                    {
                                        $valpar = $valpar + $wvalpag;
                                        $talpar = $talpar + $wvalpag;
                                    }
                                    else
                                    {
                                        $valemp = $valemp + $wvalpag;
                                        $talemp = $talemp + $wvalpag;
                                    }
                                }
                            }
                        }
                    }

                    if ($pinfin == 1)
                    {
                        $html .= "<tr>
                                  <th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' colspan='5' >TOTAL CONCEPTO</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($valfc, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($salfc, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cval, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cdes, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($ccre, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >&nbsp;</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#F2F2F2;font-size:8pt;' >" . number_format($cpag, 0, '.', ',') . "</th><tr>";
                        // realizo la suma o acumulacion de todos los valores para el tercero
                        $valft = $valft + $valfc;
                        $salft = $salft + $salfc;
                        $tval = $tval + $cval;
                        $tdes = $tdes + $cdes;
                        $tdeb = $tdeb + $cdeb;
                        $tcre = $tcre + $ccre;
                        $tpag = $tpag + $cpag;
                        $pincon = 0;
                        $pinter++;

                        $html .= "<tr>
                                  <th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' colspan='5' >TOTAL TERCERO</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($valft, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($salft, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tval, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tdes, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tdeb, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tcre, 0, '.', ',') . "</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >&nbsp;</th>";
                        $html .= "<th align=CENTER class='' style='background-color:#C6C6C6;font-size:10pt;' >" . number_format($tpag, 0, '.', ',') . "</th><tr>";
                        // realizo la suma o acumulacion de todos los valores finales
                        $valff = $valff + $valft;
                        $salff = $salff + $salft;
                        $fval = $fval + $tval;
                        $fdes = $fdes + $tdes;
                        $fdeb = $fdeb + $tdeb;
                        $fcre = $fcre + $tcre;
                        $fpag = $fpag + $tpag;

                        $html .= "</table></br>";

                        $html .= "<table align='center' >";
                        $html .= "<TR><th align='left' class='fila1' >VALOR FACTURADO A EMPRESAS: </th>";
                        $html .= "<th align='right' class='fila2' >" . number_format($talemp, 0, '.', ',') . "</th>";
                        $html .= "<TR><th align='left' class='fila1' >VALOR FACTURADO A PARTICULARES: </th>";
                        $html .= "<th align='right' class='fila2' >" . number_format($talpar, 0, '.', ',') . "</th><TR>";
                        $html .= "<TR><th align='left' class='fila1' >=VALOR FACTURADO TOTAL: </th>";
                        $html .= "<th align='right' class='fila2' >" . number_format($tpag, 0, '.', ',') . "</th><TR>";

                        $html .= "<TR class='encabezadoTabla'><th align=CENTER class='' >= TOTAL A PAGAR: </th>";
                        $html .= "<th align=CENTER class='' >" . number_format($tpag, 0, '.', ',') . "</th><TR>";
                        $html .= "</table></br>";

                        $arr_totales['valff']  = ($arr_totales['valff']*1) + $valff;
                        $arr_totales['salff']  = ($arr_totales['salff']*1) + $salff;
                        $arr_totales['fval']   = ($arr_totales['fval']*1) + $fval;
                        $arr_totales['fdes']   = ($arr_totales['fdes']*1) + $fdes;
                        $arr_totales['fdeb']   = ($arr_totales['fdeb']*1) + $fdeb;
                        $arr_totales['fcre']   = ($arr_totales['fcre']*1) + $fcre;
                        $arr_totales['valemp'] = ($arr_totales['valemp']*1) + $valemp;
                        $arr_totales['valpar'] = ($arr_totales['valpar']*1) + $valpar;
                        $arr_totales['fpag']   = ($arr_totales['fpag']*1) + $fpag;

                        // $html .= "<table align='center' width='1000' >";
                        // $html .= "<tr><td align=CENTER class='titulo2'>&nbsp;</td>";
                        // $html .= "<td align=CENTER class='titulo2'><B>VALOR FACTURA</B></td>";
                        // $html .= "<td align=CENTER class='titulo2'><B>SALDO FACTURA</B></td>";
                        // $html .= "<td align=CENTER class='titulo2'><B>VALOR CARGO</B></td>";
                        // $html .= "<td align=CENTER class='titulo2'><B>VALOR DESCUENTO</B></td>";
                        // $html .= "<td align=CENTER class='titulo2'><B>NOTAS DEBITO</B></td>";
                        // $html .= "<td align=CENTER class='titulo2'><B>NOTAS CREDITO</B></td>";
                        // $html .= "<td align=CENTER class='titulo2'><B>VALOR A PAGAR</B></td></tr>";
                        // $html .= "<tr><th align=CENTER class='acumulado1'>TOTALES</th>";
                        // $html .= "<th align=CENTER class='acumulado1' >" . number_format($valff, 0, '.', ',') . "</th>";
                        // $html .= "<th align=CENTER class='acumulado1' >" . number_format($salff, 0, '.', ',') . "</th>";
                        // $html .= "<th align=CENTER class='acumulado1' >" . number_format($fval, 0, '.', ',') . "</th>";
                        // $html .= "<th align=CENTER class='acumulado1' >" . number_format($fdes, 0, '.', ',') . "</th>";
                        // $html .= "<th align=CENTER class='acumulado1' >" . number_format($fdeb, 0, '.', ',') . "</th>";
                        // $html .= "<th align=CENTER class='acumulado1' >" . number_format($fcre, 0, '.', ',') . "</th>";
                        // $html .= "<th align=CENTER class='acumulado1' >" . number_format($fpag, 0, '.', ',') . "</th><tr>";
                        // $html .= "</table></br>";

                        // $html .= "<table align='center' >";
                        // $html .= "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A EMPRESAS: </th>";
                        // $html .= "<th align=CENTER class='acumulado6' >" . number_format($valemp, 0, '.', ',') . "</th>";
                        // $html .= "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A PARTICULARES: </th>";
                        // $html .= "<th align=CENTER class='acumulado6' >" . number_format($valpar, 0, '.', ',') . "</th><TR>";
                        // $html .= "<TR><th align=CENTER class='acumulado5' >=VALOR FACTURADO TOTAL: </th>";
                        // $html .= "<th align=CENTER class='acumulado6' >" . number_format($fpag, 0, '.', ',') . "</th><TR>";

                        // $html .= "<TR><th align=CENTER class='acumulado5' >= TOTAL A PAGAR: </th>";
                        // $html .= "<th align=CENTER class='acumulado6' >" . number_format($fpag, 0, '.', ',') . "</th><TR>";
                        // $html .= "</table></br>";
                    }
                    else
                    {
                        $html .= "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
                        $html .= "  <tr><td colspan='2' align='center'>
                                        <font size=3 color='#000080' face='arial'>
                                            <span>Tercero: ".$cod_tercero.'-'.$arr_terceros[$cod_tercero]."</span>
                                            <b>Sin ningun documento que coincida con los paremetros seleccionados</td>
                                    </tr>";
                        $html .= "</table></br>";
                    }

                    $data['html'] = $html;
                }
                unset($arr_terceros[$tercero_pila]);
                //$cantidad_terceros = count($arr_terceros);
                $terceros_generados++;

                /*if( $tercero_actual_llamado == "71776133" ){
                        echo "edb 5 ->".$tercero_pila;
                        print_r($arr_terceros);
                    }*/

                $data['nombre_tercero_generando'] = str_replace( $caracteres, $caracteres2, $nombre_tercero_generando );
                $data['generando_tercero']        = str_replace( $caracteres, $caracteres2, $generando_tercero );
                $data['arr_terceros']             = base64_encode(serialize($arr_terceros));
                $data['arr_totales']              = base64_encode(serialize($arr_totales));
                $data['cantidad_terceros']        = $cantidad_terceros;
                $data['terceros_generados']       = $terceros_generados;
                liberarConexionBD($conex);
        break;

        case 'cerrar_reporte':
                $arr_totales = unserialize(base64_decode($arr_totales));
                $html = "";
                $html .= "<table align='center' width='1000' >";
                $html .= "<tr class='encabezadoTabla'>
                          <td align=CENTER class=''>&nbsp;</td>";
                $html .= "<td align=CENTER class=''><B>VALOR FACTURA</B></td>";
                $html .= "<td align=CENTER class=''><B>SALDO FACTURA</B></td>";
                $html .= "<td align=CENTER class=''><B>VALOR CARGO</B></td>";
                $html .= "<td align=CENTER class=''><B>VALOR DESCUENTO</B></td>";
                $html .= "<td align=CENTER class=''><B>NOTAS DEBITO</B></td>";
                $html .= "<td align=CENTER class=''><B>NOTAS CREDITO</B></td>";
                $html .= "<td align=CENTER class=''><B>VALOR A PAGAR</B></td></tr>";
                $html .= "<tr class='fila2'>
                          <th align='center' class=''>TOTALES</th>";
                $html .= "<th align='center' class='' >" . number_format(($arr_totales['valff']*1), 0, '.', ',') . "</th>";
                $html .= "<th align='center' class='' >" . number_format(($arr_totales['salff']*1), 0, '.', ',') . "</th>";
                $html .= "<th align='center' class='' >" . number_format(($arr_totales['fval']*1), 0, '.', ',') . "</th>";
                $html .= "<th align='center' class='' >" . number_format(($arr_totales['fdes']*1), 0, '.', ',') . "</th>";
                $html .= "<th align='center' class='' >" . number_format(($arr_totales['fdeb']*1), 0, '.', ',') . "</th>";
                $html .= "<th align='center' class='' >" . number_format(($arr_totales['fcre']*1), 0, '.', ',') . "</th>";
                $html .= "<th align='center' class='' >" . number_format(($arr_totales['fpag']*1), 0, '.', ',') . "</th><tr>";
                $html .= "</table></br>";

                $html .= "<table align='center' >";
                $html .= "<TR>
                                <th align='left' class='fila1' >VALOR FACTURADO A EMPRESAS: </th>";
                $html .= "      <th align=right class='fila2' >" . number_format(($arr_totales['valemp']*1), 0, '.', ',') . "</th>";
                $html .= "<TR>
                                <th align='left' class='fila1' >VALOR FACTURADO A PARTICULARES: </th>";
                $html .= "      <th align=right class='fila2' >" . number_format(($arr_totales['valpar']*1), 0, '.', ',') . "</th><TR>";
                $html .= "<TR>
                                <th align='left' class='fila1' >=VALOR FACTURADO TOTAL: </th>";
                $html .= "      <th align=right class='fila2' >" . number_format(($arr_totales['fpag']*1), 0, '.', ',') . "</th><TR>";

                $html .= "<TR class='encabezadoTabla'><th align=CENTER class='' >= TOTAL A PAGAR: </th>";
                $html .= "<th align='right' class='' >" . number_format(($arr_totales['fpag']*1), 0, '.', ',') . "</th><TR>";
                $html .= "</table></br>";
                $data['html'] = $html;
        break;

        default:
            $data['error']   = 1;
            $data['mensaje'] = utf8_encode($no_exec_sub);
        break;
    }
    echo json_encode($data);
    return;
}
?>
<!doctype html>
<html lang="es-ES">
<head>
    <title>SALDOS PENDIENTES PARA LIQUIDACION DE TERCEROS POR RECAUDOS</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}

    	.titulo1{color:#FFFFFF;background:#006699;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo5{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.titulo6{color:#003366;background:#FFCC66;font-size:12pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.texto1{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;text-align:center;}
    	.texto2{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Arial;text-align:center;}
    	.texto3{color:#006699;background:#A4E1E8;font-size:9pt;font-weight:bold;font-family:Arial;text-align:center;}
    	.texto4{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;text-align:right;}
    	.texto5{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Arial;text-align:right;}
    	.texto6{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Arial;text-align:center;}
    	.texto7{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Arial;text-align:right;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado3{color:#003366;background:#57C8D5;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado4{color:#003366;background:#57C8D5;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
    	.acumulado5{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.acumulado6{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}

    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>


<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->


function Seleccionar()
{
    document.forma.bandera.value=2;
    document.forma.submit();
}

function SeleccionarAjax()
{
	document.forma.bandera.value=2;
	//document.forma.submit();
    iniciarReporte();
}

function iniciarReporte()
{
    $("#div_contenedor_progreso").show(500);
    $("#img_progreso").show(500);
    $.post("penter.php",
        {
            accion       : 'iniciar_reporte',
            consultaAjax : '',
            wemp_pmla    : $(":input[name=wemp_pmla]").val(),
            wbandera     : $(":input[name=wbandera]").val(),
            wfecini      : $(":input[name=wfecini]").val(),
            wfecfin      : $(":input[name=wfecfin]").val(),
            wter         : $(":input[name=wter]").val(),
            wcon         : $(":input[name=wcon]").val(),
            wbasedato    : $(":input[name=wbasedato]").val(),
            bandera      : $(":input[name=bandera]").val(),
            resultado    : $(":input[name=resultado]").val(),
            vol          : $(":input[name=vol]:checked").val()
        },
        function(data){
            return data;
            /*console.log(data);
            return
            if(data.error == 1)
            {
                alert(data.mensaje);
            }
            else
            {
                $("#tabla_filtros_reporte").hide();
                $("#div_resultado_reporte").html(data.html);
                consultarTercero(data.arr_terceros, data.cantidad_terceros);
            }*/
        },
        "json"
    ).done(function(data){
            // console.log(data);
            if(data.error == 1)
            {
                // $("#div_resultado_reporte").html(data.html);
                alert(data.mensaje);
                $("#div_contenedor_progreso").hide(500);
                $("#img_progreso").hide();
            }
            else
            {
                $("#tabla_filtros_reporte").hide();
                $("#div_resultado_reporte").append(data.html);
                $("#tbl_nombre_tercero").show();
                consultarTercero(data.arr_terceros, data.cantidad_terceros, 0, data.generando_tercero, '', data.nombre_tercero_generando);
            }
    });
}

function consultarTercero(arr_terceros, cantidad_terceros, terceros_generados, generando_tercero, arr_totales, nombre_tercero_generando)
{
    var faltante = cantidad_terceros - terceros_generados;
    if(faltante > 0)// && terceros_generados < 20
    {
        $("#spn_nombre_tercero_generando").html(nombre_tercero_generando);
        //console.log(nombre_tercero_generando+'  '+generando_tercero);
        $.post("penter.php",
            {
                accion            : 'consultar_tercero',
                consultaAjax      : '',
                wemp_pmla         : $(":input[name=wemp_pmla]").val(),
                tablaConceptos    : $("#tablaConceptos").val(),
                wbandera          : $(":input[name=wbandera]").val(),
                wfecini           : $(":input[name=wfecini]").val(),
                wfecfin           : $(":input[name=wfecfin]").val(),
                wter              : $(":input[name=wter]").val(),
                wcon              : $(":input[name=wcon]").val(),
                wbasedato         : $(":input[name=wbasedato]").val(),
                bandera           : $(":input[name=bandera]").val(),
                resultado         : $(":input[name=resultado]").val(),
                vol               : $(":input[name=vol]:checked").val(),
                arr_terceros      : arr_terceros,
                cantidad_terceros : cantidad_terceros,
                terceros_generados: terceros_generados,
                generando_tercero : generando_tercero,
                arr_totales       : arr_totales
            },
            function(data){
                return data;
            },
            "json"
        ).done(function(data){
                var porcentaje = (100 * data.terceros_generados) / cantidad_terceros;
                var porcentaje_formato = parseFloat(Math.round(porcentaje * 100) / 100);//.toFixed(2);
                $("#div_barra_progreso").css("width",""+porcentaje_formato+"%");
                $("#spn_porcentaje").html(porcentaje_formato+"%");
                //console.log("(100 * "+data.terceros_generados+") / "+cantidad_terceros+" = "+porcentaje_formato);

                if(data.error == 1)
                {
                    alert(data.mensaje);
                    $("#img_progreso").hide(500);
                }
                else
                {
                    $("#div_resultado_reporte").append(data.html);
                    // return;
                    if(terceros_generados == data.terceros_generados)
                    {
                        alert("Se detectó un ciclo infinito en la generación del reporte.\n\nLa generación del reporte no puede continuar.");
                        $("#img_progreso").hide(500);
                        $("#div_contenedor_progreso").hide(500);
                        return;
                    }
                    else
                    {
                        consultarTercero(data.arr_terceros, data.cantidad_terceros, data.terceros_generados, data.generando_tercero, data.arr_totales, data.nombre_tercero_generando);
                    }
                }
            }
        );
    }
    else
    {
        if(terceros_generados > 0)
        {
            $.post("penter.php",
                {
                    accion            : 'cerrar_reporte',
                    consultaAjax      : '',
                    arr_totales       : arr_totales
                },
                function(data){
                    return data;
                },
                "json"
            ).done(function(data){
                    if(data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#div_resultado_reporte").append(data.html);
                        $("#spn_nombre_tercero_generando").html('');
                        $("#tbl_nombre_tercero").hide();
                        $("#div_contenedor_progreso").hide(500);
                    }
                    // console.log("Reporte generado al 100%");
                }
            );
        }
        $("#img_progreso").hide(500);
    }
}

function enter3()
{
	document.forma.submit();
}


</SCRIPT>

</head>

<?php

/**
* NOMBRE:  REPORTE DE lIQUIDACION DE TERCEROS POR RECAUDOS
*
* PROGRAMA: penter.php
* TIPO DE SCRIPT: REPORTE
* //DESCRIPCION:Este reporte presenta la liquidación para terceros agrupada por concepto.
*
*
* Tablas que utiliza:
* $wbasedato."_000024: Maestro de Fuentes, select
* $wbasedato."_000018: select de facturas entre dos fechas
* $wbasedato."_000020: select en encabezado de cartera
* $wbasedato."_000021: select en detalle de cartera
*
* @author ccastano
* @created 2006-12-14
* @version 2007-05-03  //se mejora query de busqueda de conceptos de facturacion
* @version 2007-04-15  //se incluye el descuento o suma de notas por cartera si el campo conacp esta en on
* @version 2007-08-14  //se vuelve a hacer el reporte haciendolo mas efectivo
* @var $wano 	 Ano del momento de utilización del programa, con ella se inicializa la fecha inicial del rango en que se muestra la liquidación
* @var $wfecfin fecha final para el rango en que se mostrara la liquidacion, entrada por el usuario.
* @var $wfecini fecha inicial para el rango en que se mostrara la liquidacion, entrada por el usuario.
* @var $wfecha  fecha del momento de utilización del programa, con ella se inicializa la fecha final del rango en que se muestra la liquidación
* @var $wmes  	 Mes del momento de utilización del programa, con ella se inicializa la fecha inicial del rango en que se muestra la liquidación
* @var $wter	 Tercero seleccionado por el usuario para realizar liquidacion (puede ser todos los terceros)
* @var $wcon    Concepto elegido por el usuario para desplegar la liquidacion (puede ser todos los concpetos del tercero)
* @var $terdoc[0] vector de documentos de los terceros que seran desplegados en el reporte
* @var $ternom[0] vector de nombres de los terceros que seran desplegados en el reporte
* @var $tercero[] vector de documento-nombre de los terceros que seran desplegados en el reporte
* @var $concod[0] vector de codigos de los conceptos que seran desplegados en el reporte
* @var $codnom[0] vector de nombres de los conceptos que seran desplegados en el reporte
* @var $concepto[] vector de codigo-concepto de los terceros que seran desplegados en el reporte
* @var $vol indica si el reporte es para factura para empresas, particulares o ambos
* @var $pintado indica si se encontro la primera factura para el concepto de manera que s epinte su nombre
* @var $numfac[] vector de numeros de factura que fueron canceladas en el rango de fechas
* @var $fuefac[] vector de fuentes de factura que fueron canceladas en el rango de fechas
* @var $wvaldeb  valor de las notas debito de un concepto
* @var $wvalcre  valor de las notas credito de un concepto
* @var $wvalpag  valor a pagar de un concepto= (valor del cargo-valor del descuento+ notas debito - notas credito)* porcentaje del tercero
* @var $cval acumulado del valor del concepto para un concepto determinado y un tercero determinado
* @var $cdes acumulado del valor del decuento para un concepto determinado y un tercero determinado
* @var $cdeb acumulado del valor de notas debito para un concepto determinado y un tercero determinado
* @var $ccre acumulado del valor del notas credito para un concepto determinado y un tercero determinado
* @var $cpag acumulado del valor a pagar para un concepto determinado y un tercero determinado
* @var $tval acumulado del valor del concepto para  un tercero determinado
* @var $tdes acumulado del valor del descuento para  un tercero determinado
* @var $tdeb acumulado del valor de notas debito para  un tercero determinado
* @var $tcre acumulado del valor de notas credito para un tercero determinado
* @var $tpag acumulado del valor a pagar para un tercero determinado
* @var $pinter //indica si debe mostrar totales de tercero cuando esta en uno
* @var $fval acumulado del valor del concepto
* @var $fdes acumulado del valor del descuento
* @var $fdeb acumulado del valor de notas debito
* @var $fcre acumulado del valor de notas credito
* @var $fpag acumulado del valor a pagar
* @var $pinfin //indica si debe mostrar totales generales cuando esta en uno
* @var $valemp  //valor a pagar de empresa
* @var $valpar  //valor a pagar de particular
*/


/* MODIFICACIONES:
//-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------

* Agosto 22 de 2013:
    Edwar Jaramillo:    * Se optimiza el reporte para que no ejecute una sola consulta que sature el motor de base de datos,
                            lo que se hace es realizar un primer llamado ajax con el fin de leer todos los terceros que se encuentren bajo los filtros seleccionados,
                            luego mediante llamados ajax por cada uno de los terceros encontrados se hace la consulta y se devuelve el resultado al navegador,
                            el reporte se construye tercero por tercero, este proceso aliviana la carga al motor de base de datos a diferencia de la manera como
                            estaba construido antes el query, que lo que hacía era consultar todos los terceros de una vez.
                        * Es reporte puede ejecutar tanto el proceso como estaba originalmente antes de realizar cualquier modificación a la fecha como también ejecutar
                            por defecto el reporte con el nuevo proceso tercero por tercero, para ejecutar el reporte completo en un solo llamado, en la url adicionar el parámetro
                            "&rep_original=x" igualado a cualquier dato con tal que no sea vacío, con ese parámetro no se hacen llamados ajax sino que se ejecutan las
                            consultas de un solo 'tiro'.
                        * Se adicionó una barra de progreso en la parte superior del reporte para indicar cuántos datos o porcentaje del reporte se ha generado.

*	* Agosto 27 de 2012.	Camilo Zapata. Se modifica el programa para que las busquedas por nit y concepto las agua con '=' y si es para todos, omita la condición.
*/
$wautor = "Carolina Castano P.";
// =================================================================================================================================
$wactualiz = "Diciembre 24 de 2013";

// session_start();
if (!isset($_SESSION["user"]))
    echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$key = substr($user, 2, strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;

	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	echo $tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	//----------------------------------------------------------------------------------------------

	echo "<form action='penter.php' method=post name='forma'>";

    $wfecha = date("Y-m-d");
    $wano = date("Y");
    $wmes = date("m");

    echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' NAME= 'tablaConceptos' id='tablaConceptos' value='".$tablaConceptos."'>";

    echo "<input type='HIDDEN' NAME= 'wbandera' value='0'>"; //indica si el programa debe cargar los datos de inicio o el la consulta
    // de esta manera entro a la primera pagina, es decir, donde se piden los datos
    if (!isset($wfecini) or !isset($wfecfin) or !isset($wcon) or $wcon == '' or !isset ($resultado) or $bandera != 2)
    {
        $nombre_tema = "SALDOS PENDIENTES PARA LIQUIDACION DE TERCEROS POR RECAUDOS";
        encabezado("<div class='titulopagina2'>".$nombre_tema."</div>", $wactualiz, $wbasedato);
        echo "<center><table border='0' id='tabla_filtros_reporte' style=''>";
        // echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=500 HEIGHT=100></td></tr>";
        // echo "<tr><td class='titulo1'>SALDOS PENDIENTES PARA LIQUIDACION DE TERCEROS POR RECAUDOS</td></tr>";

        //INGRESO DE VARIABLES PARA EL REPORTE//
        if (!isset ($bandera))
        {
            $wfecfin = $wfecha;
            $wfecini = $wano . '-' . $wmes . '-01';
        }

        echo "<tr class='fila1'>";
        echo "<td align=center class='' style='width:400px;'><b>FECHA INICIAL: </font></b>";
        campoFecha("wfecini");
        echo "</td>";

        echo "<td  align=center class='' style='width:760px;'><b>FECHA FINAL: </font></b>";
		campoFecha("wfecfin");
        echo "</td>";

        echo "</tr>";

        echo "<tr class='fila2'>";
        // SELECCION DE NIT O CEDULA DEL TERCERO
        if (isset($wter))
        {
            echo "<td align=center class=''>NIT O CEDULA DEL TERCERO: <br><select name='wter' onchange='javascript:enter3()'>";

            if ($wter != '%-Todos los terceros')
            {
                $q = "   SELECT count(*) "
                 . "     FROM " . $wbasedato . "_000051 "
                 . "    WHERE meddoc = (mid('" . $wter . "',1,instr('" . $wter . "','-')-1)) ";

                $ver = $q;
                $res1 = mysql_query($q, $conex);
                $num1 = mysql_num_rows($res1);
                $row1 = mysql_fetch_array($res1);
            }
            else
            {
                $row1[0] = 1;
            }

            if ($row1[0] > 0)
            {
                echo "<option selected>" . $wter . "</option>";
                if ($wter != '% - Todas las empresas')
                {
                    echo "<option>%-Todos los terceros</option>";
                }

                $q = "   SELECT count(*) "
                 . "     FROM " . $wbasedato . "_000051 "
                 . "    WHERE meddoc != (mid('" . $wter . "',1,instr('" . $wter . "','-')-1)) ";
                $res = mysql_query($q, $conex);
                $num = mysql_num_rows($res);
                $row = mysql_fetch_array($res);
                if ($row[0] > 0)
                {
                    $q = "   SELECT meddoc, mednom "
                     . "     FROM " . $wbasedato . "_000051"
                     . "    WHERE meddoc != (mid('" . $wter . "',1,instr('" . $wter . "','-')-1)) "
                     . "    order by 2";
                    $res1 = mysql_query($q, $conex);
                    $num1 = mysql_num_rows($res1);
                    for ($i = 1;$i <= $num1;$i++)
                    {
                        $row1 = mysql_fetch_array($res1);
                        echo "<option>" . $row1[0] . "-" . $row1[1] . "</option>";
                    }
                }
            }
            echo "</select></td>";
            // SELECCIONAR concepto
            echo "<td align=center class='' >CONCEPTO: ";
            echo "<select name='wcon'>";

            if ($wter != '%-Todos los terceros')
            {
                $q = "   SELECT relgru "
                 . "     FROM " . $wbasedato . "_000102 "
                 . "    WHERE relmed = '" . $wter . "' "
                 . "      AND relest= 'on' order by relgru";
            }
            else
            {
                $q = "   SELECT distinct relgru "
                 . "     FROM " . $wbasedato . "_000102 "
                 . "    WHERE relest= 'on' and relgru<>'NO APLICA' order by relgru";
            }
            $res = mysql_query($q, $conex);
            $num = mysql_num_rows($res);

            echo "<option>%-Todos los conceptos</option>";
            for ($i = 1;$i <= $num;$i++)
            {
                $row = mysql_fetch_array($res);
                echo "<option>" . $row[0] . "</option>";
            }

            echo "</select></td></tr>";
        }
        else
        {
            echo "<td class=''> NIT O CEDULA DEL TERCERO: <br><select name='wter' onchange='javascript:enter3()'>";

            $q = " SELECT meddoc, mednom "
             . "   FROM " . $wbasedato . "_000051 "
             . "  ORDER BY mednom ";

            $res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
            $num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());
            echo "<option>%-Todos los terceros</option>";
            for ($i = 1;$i <= $num;$i++)
            {
                $row = mysql_fetch_array($res);
                echo "<option>" . $row[0] . "-" . $row[1] . "</option>";
            }
            echo "</select></td>";
            // encabezado de concepto
            echo "<td align=center class='' >CONCEPTO: ";
            echo "<select name='wcon'>";
            echo "<option>%-Todos los conceptos</option>";

            $q = "   SELECT distinct relgru "
             . "     FROM " . $wbasedato . "_000102 "
             . "    WHERE relest= 'on' and relgru<>'NO APLICA' order by relgru";

            $res = mysql_query($q, $conex);
            $num = mysql_num_rows($res);

            for ($i = 1;$i <= $num;$i++)
            {
                $row = mysql_fetch_array($res);
                echo "<option>" . $row[0] . "</option>";
            }
            echo "</select></td></tr>";
        }

        echo "<input type='HIDDEN' NAME= 'wbasedato' value='" . $wbasedato . "'>";
        echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
        echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

        echo "<tr class='fila1'>";
        echo "<td align=center COLSPAN='2' class='' >LIQUIDACION FACTURA A:  ";

        if(!isset($rep_original) || $rep_original == '')
        {
            echo "<input type='radio' name='vol' value='2' onclick='SeleccionarAjax()' >PARTICULARES&nbsp;&nbsp;";
            echo "<input type='radio' name='vol' value='1' onclick='SeleccionarAjax()' >EMPRESAS&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<input type='radio' name='vol' value='3' onclick='SeleccionarAjax()' checked>AMBOS&nbsp;&nbsp;";
            echo "</b></td></tr></table></br>
                    <div id='div_contenedor_progreso' align='center' style='display:none;'>
                        <table align='center' id='tbl_nombre_tercero' style='display:none;'>
                            <tr>
                                <td align='center' style='font-size:8pt;color:#cccccc;'>Generando:&nbsp;<span id='spn_nombre_tercero_generando'></span>...</td>
                            </tr>
                        </table>
                        <table align='center'>
                            <tr>
                                <td style='font-size:8pt;font-weight:bold;'>Reporte generado: </td>
                                <td style='width:200px;border:2px solid #2A5DB0'>
                                    <div id='div_barra_progreso' style='background-color:#A2DFA2; width: 0%; text-align:right;'>
                                        <span id='spn_porcentaje' style='font-size:8pt;font-weight:bold;'>0%</span>
                                    </div>
                                </td>
                                <td style='width:50px';><img style='display:none;' id='img_progreso' width='18' height='18' border='0' src='../../images/medical/ajax-loader5.gif'></td>
                            </tr>
                        </table>
                    </div>
                    <div id='div_resultado_reporte'></div>";
        }
        else
        {
            echo "<input type='radio' name='vol' value='2' onclick='Seleccionar()' >PARTICULARES&nbsp;&nbsp;";
            echo "<input type='radio' name='vol' value='1' onclick='Seleccionar()' >EMPRESAS&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "<input type='radio' name='vol' value='3' onclick='Seleccionar()' checked>AMBOS&nbsp;&nbsp;";
            echo "<input type='HIDDEN' NAME= 'rep_original' value='".((!isset($rep_original)) ? '': $rep_original)."'>";
        }
        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
    }
    elseif(isset($rep_original) && $rep_original != '')
    {
        // MUESTRA DE DATOS DEL REPORTE
        echo "<table  align=center width='60%'>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=340 HEIGHT=100></td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td><B>Fecha: " . date('Y-m-d') . "</B></td></tr>";
        switch ($vol)
        {
            case 1:
                echo "<tr><td><B>Saldo para Liquidacion de facturado para empresa</B></td></tr>";
                break;

            case 2:
                echo "<tr><td><B>Saldo para Liquidacion de facturado para particular</B></td></tr>";
                break;

            case 3:
                echo "<tr><td><B>Saldo para Liquidacion de facturado para empresas y particulares</B></td></tr>";
                break;
        }

        echo "</tr><td align=right ><A href='penter.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wter=" . $wter . "&amp;bandera='1'>VOLVER</A></td></tr>";
        echo "<tr align='center'><td><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
        echo "<tr><td><tr><td>Fecha inicial: " . $wfecini . "</td></tr>";
        echo "<tr><td>Fecha final: " . $wfecfin . "</td></tr>";
        echo "</table></br>";

        echo "<input type='HIDDEN' NAME= 'wfecini' value='" . $wfecini . "'>";
        echo "<input type='HIDDEN' NAME= 'wfecfin' value='" . $wfecfin . "'>";
        echo "<input type='HIDDEN' NAME= 'wemp' value='" . $wter . "'>";
        echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

        /**
        * **********************************Consulto lo pedido *******************
        */

        if ($wter != '%-Todos los terceros')
        {
            $print = explode('-', $wter);
            $ter = trim ($print[0]);
        }
        else
        {
            $ter = '%';
        }
        // se organiza el vector de conceptos que tambien se va a recorrer dentro del for de terceros
        if ($wcon != '%-Todos los conceptos')
        {
            $print = explode('-', $wcon);
            $con = trim ($print[0]);
        }
        else
        {
            $con = '%';
        }

        $filtroTercero='';
        $filtroConcepto='';
        if($ter!='%')
        {
            $filtroTercero="AND fdeter = '" . $ter . "' ";
        }
        if($con!='%')
        {
            $filtroConcepto="AND fdecon = '" . $con . "' ";
        }
        // busco todas las facturas generadas en el rango de fechas
        switch ($vol)
        {
            case 1:
                $q = " SELECT  fenfac, fenffa, fdeter, fdecon, mednom, fenval, fensal  "
                 . "     FROM  " . $wbasedato . "_000018, " . $wbasedato . "_000065,  " . $wbasedato . "_000051   "
                 . "     WHERE   fenfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND fenest= 'on' "
                 . "     AND fencco<>'' "
                 . "     AND fenval>0 "
                 . "     AND fentip<>'01-PARTICULAR' "
                 . "     AND fdedoc=fenfac "
                 . "     AND fdefue=fenffa "
                 . "     AND fdeest= 'on' "
                 .$filtroTercero
                 .$filtroConcepto
                 . "     AND fdeter = meddoc "
                 . "     GROUP BY  4,3,1,2 "
                 . "     ORDER BY  5,4,1,2 ";
                break;

            case 2:
                $q = " SELECT  fenfac, fenffa, fdeter, fdecon, mednom, fenval, fensal  "
                 . "     FROM  " . $wbasedato . "_000018, " . $wbasedato . "_000065,  " . $wbasedato . "_000051   "
                 . "     WHERE   fenfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND fenest= 'on' "
                 . "     AND fencco<>'' "
                 . "     AND fenval>0 "
                 . "     AND fentip='01-PARTICULAR' "
                 . "     AND fdedoc=fenfac "
                 . "     AND fdefue=fenffa "
                 . "     AND fdeest= 'on' "
                 .$filtroTercero
                 .$filtroConcepto
                 . "     AND fdeter = meddoc "
                 . "     GROUP BY  4,3,1,2 "
                 . "     ORDER BY 5,4,1,2 ";
                break;

            case 3:
                $q = " SELECT  fenfac, fenffa, fdeter, fdecon, mednom, fenval, fensal  "
                 . "     FROM  " . $wbasedato . "_000018, " . $wbasedato . "_000065,  " . $wbasedato . "_000051   "
                 . "     WHERE   fenfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND fenest= 'on' "
                 . "     AND fencco<>'' "
                 . "     AND fenval>0 "
                 . "     AND fdedoc=fenfac "
                 . "     AND fdefue=fenffa "
                 . "     AND fdeest= 'on' "
                 .$filtroTercero
                 .$filtroConcepto
                 . "     AND fdeter = meddoc "
                 . "     GROUP BY  4,3,1,2 "
                 . "     ORDER BY  5,4,1,2";

                break;
        }

        $err2 = mysql_query($q, $conex);
        $num3 = mysql_num_rows($err2);
        $j = 0;
        // el resultado lo debo organizar en un vector y consultar el saldo a la fecha de corte
        for ($l = 0;$l < $num3;$l++)
        {
            $row2 = mysql_fetch_array($err2);

            $q = " SELECT  b.rdesfa, b.rdefue, b.rdenum, b.rdefac, b.rdeffa "
             . "    FROM  " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b   "
             . "    WHERE   rdefac= '" . $row2[0] . "' "
             . "     AND rdeffa= '" . $row2[1] . "' "
             . "     AND rdeest= 'on' "
             . "     AND rdesfa<>'' "
             . "     AND rdereg=0 "
             . "     AND renfec <= '" . $wfecfin . "'  "
            // ."     AND rencco = '".$row[4]."'  "
            . "     AND renfue=rdefue  "
             . "     AND rennum=rdenum  "
             . "     AND rencco=rdecco  "
             . "     ORDER BY  b.id desc";

            $err3 = mysql_query($q, $conex);
            $y = mysql_num_rows($err3);
            $row3 = mysql_fetch_array($err3);

            if ($y > 0)
            {
                if ($row3[0] > 0)
                {
                    $q = " SELECT  MIN(cast(rdesfa as UNSIGNED)) "
                     . "    FROM   " . $wbasedato . "_000021 "
                     . "    WHERE   rdefac= '" . $row3[3] . "' "
                     . "     AND rdeffa= '" . $row3[4] . "' "
                     . "     AND rdeest= 'on' "
                     . "     AND rdesfa<>'' "
                     . "     AND rdereg=0 "
                     . "     AND rdefue='" . $row3[1] . "'  "
                     . "     AND rdenum='" . $row3[2] . "'  "
                     . "     group by rdenum, rdefue  ";

                    $err3 = mysql_query($q, $conex);
                    $row3 = mysql_fetch_array($err3);
                }
            }

            if ($y > 0)
            {
                if ($row3[0] > 0)
                {
                    $numfac2[$j] = $row2[0];
                    $fuefac2[$j] = $row2[1];
                    $terfac2[$j] = $row2[2];
                    $confac2[$j] = $row2[3];
                    $valfac2[$j] = $row2[5];
                    $salfac2[$j] = $row3[0];
                    $nomfac2[$j] = $row2[4];
                    $j++;
                }
            }
            else
            {
                $numfac2[$j] = $row2[0];
                $fuefac2[$j] = $row2[1];
                $terfac2[$j] = $row2[2];
                $confac2[$j] = $row2[3];
                $valfac2[$j] = $row2[5];
                $salfac2[$j] = $row2[5];
                $nomfac2[$j] = $row2[4];
                $j++;
            }
        }

        $clase1 = "class='texto1'";
        $clase2 = "class='texto4'";
        $pinfin = 0; //indica si debe mostrar totales cuando esta en uno
        $valff = 0;
        $salff = 0;
        $fval = 0;
        $fdes = 0;
        $fdeb = 0;
        $fcre = 0;
        $fpag = 0;
        $pinfin = 0; //indica si debe mostrar totales
        $valemp = 0;
        $valpar = 0;

        $pinter = 0; //indica si debe mostrar totales de tercero cuando esta en uno
        $teran = 0;
        $conan = 0;

        $i = 0;
        $j = 0;

        for ($k = 0;$k < count($numfac2);$k++) // se recorren las facturas
        {
            $q = " SELECT  fdevco, fdepte, fdevde, fdecco "
             . "    FROM  " . $wbasedato . "_000065 "
             . "    WHERE   fdedoc= '" . $numfac2[$k] . "' "
             . "     AND fdefue= '" . $fuefac2[$k] . "' "
             . "     AND fdeest= 'on' "
             . "     AND fdeter='" . $terfac2[$k] . "' "
             . "     AND fdecon = '" . $confac2[$k] . "'  ";
            $err2 = mysql_query($q, $conex);
            $y = mysql_num_rows($err2);

            if ($y > 0) // si se encuentra el registro, se pinta
                {
                    for ($t = 0;$t < $y;$t++) // se recorren los conceptos
                {
                    if ($teran != $terfac2[$k])
                    {
                        if ($pinter > 1)
                        {
                            echo "<tr><th align=CENTER class='acumulado3' colspan='5' >TOTAL CONCEPTO</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($valfc, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($salfc, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($cval, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($cdes, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($ccre, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >&nbsp;</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($cpag, 0, '.', ',') . "</th><tr>";
                            // realizo la suma o acumulacion de todos los valores para el tercero
                            $valft = $valft + $valfc;
                            $salft = $salft + $salfc;
                            $tval = $tval + $cval;
                            $tdes = $tdes + $cdes;
                            $tdeb = $tdeb + $cdeb;
                            $tcre = $tcre + $ccre;
                            $tpag = $tpag + $cpag;

                            $pinter++;

                            echo "<tr><th align=CENTER class='acumulado2' colspan='5' >TOTAL TERCER0</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($valft, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($salft, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tval, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tdes, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tdeb, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tcre, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >&nbsp;</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><tr>";
                            // realizo la suma o acumulacion de todos los valores finales
                            $valff = $valff + $valft;
                            $salff = $salff + $salft;
                            $fval = $fval + $tval;
                            $fdes = $fdes + $tdes;
                            $fdeb = $fdeb + $tdeb;
                            $fcre = $fcre + $tcre;
                            $fpag = $fpag + $tpag;

                            echo "</table></br>";

                            echo "<table align='center' >";
                            echo "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A EMPRESAS: </th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($talemp, 0, '.', ',') . "</th>";
                            echo "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A PARTICULARES: </th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($talpar, 0, '.', ',') . "</th><TR>";
                            echo "<TR><th align=CENTER class='acumulado5' >=VALOR FACTURADO TOTAL: </th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><TR>";

                            echo "<TR><th align=CENTER class='acumulado5' >= TOTAL A PAGAR: </th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><TR>";
                            echo "</table></br>";
                        }
                        echo "<table  align=center width='1000'>";
                        echo "<tr><td class=titulo6 colspan=13><B>Tercero:</B> " . $terfac2[$k] . "-" . $nomfac2[$k] . "</td></tr>";
                        $conan = 0;
                        $teran = $terfac2[$k];
                        $pinfin = 1;
                        $pinter = 1;

                        $valfc = 0;
                        $salfc = 0;
                        $cval = 0;
                        $cdes = 0;
                        $cdeb = 0;
                        $ccre = 0;
                        $cpag = 0;
                        $pincon = 0;

                        $valft = 0;
                        $salft = 0;
                        $tval = 0;
                        $tdes = 0;
                        $tdeb = 0;
                        $tcre = 0;
                        $tpag = 0;
                        $talemp = 0;
                        $talpar = 0;
                    }

                    if ($conan != $confac2[$k]) // se pone el titulo de la tabla en caso de ser la primera factura que se encuentra
                        {
                            if ($pincon != 0)
                            {
                                echo "<tr><th align=CENTER class='acumulado3' colspan='5' >TOTAL CONCEPTO</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($valfc, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($salfc, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($cval, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($cdes, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($ccre, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >&nbsp;</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($cpag, 0, '.', ',') . "</th><tr>";
                                // realizo la suma o acumulacion de todos los valores para el tercero
                                $valft = $valft + $valfc;
                                $salft = $salft + $salfc;
                                $tval = $tval + $cval;
                                $tdes = $tdes + $cdes;
                                $tdeb = $tdeb + $cdeb;
                                $tcre = $tcre + $ccre;
                                $tpag = $tpag + $cpag;
                                $pincon = 1;
                                $pinter++;

                                $valfc = 0;
                                $salfc = 0;
                                $cval = 0;
                                $cdes = 0;
                                $cdeb = 0;
                                $ccre = 0;
                                $cpag = 0;
                            }
                            else
                            {
                                $pincon = 1;
                                $pinter++;
                            }

                            $q = "   SELECT grutip, grudes"
                             . "     FROM " . $tablaConceptos . " "
                             . "    WHERE gruest= 'on' and grucod='" . $confac2[$k] . "' ";

                            $gru = mysql_query($q, $conex);
                            $tip = mysql_fetch_row($gru);

                            IF ($tip[0] == 'C')
                            {
                                echo "<tr><td class=titulo3 COLSPAN=13 ><B>Concepto:</B> " . $confac2[$k] . "-" . $tip[1] . "</td></tr>";
                                echo "<tr><td align=CENTER class='titulo2'><B>NUMERO FACTURA</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>EMPRESA DE LA FACTURA</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>PACIENTE</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>HISTORIA</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>FECHA CARGO</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>VALOR FACTURA</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>SALDO FACTURA</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>VALOR CARGO</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>VALOR DESCUENTO</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>NOTAS DEBITO</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>NOTAS CREDITO</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>% DE PARTICIPACION</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>VALOR A PAGAR</B></td></tr>";
                            }
                            $conan = $confac2[$k];
                        }

                        IF ($tip[0] == 'C')
                        {
                            if ($clase1 == "class='texto1'")
                            {
                                $clase1 = "class='texto6'";
                                $clase2 = "class='texto7'";
                            }
                            else
                            {
                                $clase1 = "class='texto1'";
                                $clase2 = "class='texto4'";
                            }

                            echo "<td align=CENTER " . $clase1 . " width='10%'>" . $fuefac2[$k] . "-" . $numfac2[$k] . "</td>";
                            // vamos a buscar la empresa, el paciente y la historia de la factura
                            $q = " SELECT a.fendpa, a.fennpa, fenhis, empcod, empnom, fentip "
                             . "    FROM  " . $wbasedato . "_000018 a, " . $wbasedato . "_000024 b   "
                             . "    WHERE  fenffa='" . $fuefac2[$k] . "' and fenfac='" . $numfac2[$k] . "' and fenest='on' "
                             . "     AND fencod=empcod "
                             . "     AND empcod=empres "
                             . "     AND empest='on' ";

                            $err3 = mysql_query($q, $conex);
                            $row3 = mysql_fetch_array($err3);

                            echo "<td align=CENTER " . $clase1 . " width='10%'>" . $row3[3] . "-" . $row3[4] . "</td>";
                            echo "<td align=CENTER " . $clase1 . " width='10%'>" . $row3[0] . "-" . $row3[1] . "</td>";
                            echo "<td align=CENTER " . $clase1 . " width='10%'>" . $row3[2] . "</td>";
                            // vamos a buscar la fecha del cargo
                            $q = " SELECT tcarfec "
                             . "    FROM  " . $wbasedato . "_000066 a, " . $wbasedato . "_000106 b   "
                             . "    WHERE  rcfffa='" . $fuefac2[$k] . "' and rcffac='" . $numfac2[$k] . "' and rcfest='on' "
                             . "     AND b.id=rcfreg ";

                            $err4 = mysql_query($q, $conex);
                            $row4 = mysql_fetch_array($err4);

                            echo "<td align=CENTER " . $clase1 . " width='10%'>" . $row4[0] . "</td>";
                            // ahora pitamos los datos propios del cargo (valor, descuento y porcentaje)
                            $row2 = mysql_fetch_array($err2);

                            echo "<td align=left " . $clase1 . " width='10%'>" . number_format($valfac2[$k], 0, '.', ',') . "</td>";
                            echo "<td align=left " . $clase1 . " width='10%'>" . number_format($salfac2[$k], 0, '.', ',') . "</td>";
                            echo "<td align=left " . $clase2 . " width='10%'>" . number_format($row2[0], 0, '.', ',') . "</td>";
                            echo "<td align=left " . $clase2 . " width='10%'>" . number_format($row2[2], 0, '.', ',') . "</td>";
                            // ahora debo buscar el valor de las notas debito a ese cargo
                            // consulto las fuentes para notas debito
                            $q = "  SELECT carfue "
                             . "    FROM " . $wbasedato . "_000040 "
                             . "   WHERE carndb = 'on'" ;
                            $errdeb = mysql_query($q, $conex);
                            $numdeb = mysql_num_rows($errdeb);
                            $wvaldeb = 0;

                            for ($d = 1;$d <= $numdeb;$d++) // para cada fuente busco notas
                            {
                                $rowdeb = mysql_fetch_array($errdeb);
                                // consulto las notas debito por concepto de facturación y voy sumando
                                $q = " SELECT sum(fdevco) FROM " . $wbasedato . "_000021 A, " . $wbasedato . "_000065 WHERE rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "' and rdefue='" . $rowdeb[0] . "' AND rdeest='on' and rdecon='' and rdevco='0' and fdefue= rdefue and fdedoc= rdenum and fdecon= '" . $confac2[$k] . "' and fdeter= '" . $terfac2[$k] . "' and fdeest='on' and fdecco= '" . $row2[3] . "' and  A.Fecha_data<='" . $wfecfin . "' ";

                                $resdeb = mysql_query($q, $conex);
                                $row2deb = mysql_fetch_row($resdeb); //para sumar el total de nota debito
                                $wvaldeb = $wvaldeb + $row2deb[0];
                                $deb[$d] = $rowdeb[0];
                            }
                            // ahora debo buscar el valor de las notas credito a ese cargo
                            $q = "  SELECT carfue "
                             . "    FROM " . $wbasedato . "_000040 "
                             . "   WHERE carncr = 'on'";

                            $errcre = mysql_query($q, $conex);
                            $numcre = mysql_num_rows($errcre);
                            $wvalcre = 0; //para sumar el total de nota credito
                            for ($c = 1;$c <= $numcre;$c++)
                            {
                                $rowcre = mysql_fetch_array($errcre);
                                // consulto las notas credito por concepto de facturación y voy sumando
                                $q = " SELECT sum(fdevco) FROM " . $wbasedato . "_000021 A, " . $wbasedato . "_000065 WHERE rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "' and rdefue='" . $rowcre[0] . "' AND rdeest='on' and rdecon='' and rdevco='0' and fdefue= rdefue and fdedoc= rdenum and fdecon= '" . $confac2[$k] . "' and fdeter= '" . $terfac2[$k] . "' and fdeest='on' and fdecco= '" . $row2[3] . "' and  A.Fecha_data<='" . $wfecfin . "' ";
                                $rescre = mysql_query($q, $conex) ;
                                $row2cre = mysql_fetch_row($rescre);
                                $wvalcre = $wvalcre + $row2cre[0];
                                $cre[$c] = $rowcre[0];
                            }
                            // ahora voy a hallar el porcentaje para ese concepto
                            $valor = $row2[0] + $wvaldeb - $wvalcre;

                            if ($valfac2[$k] != 0)
                            {
                                $porcen = $valor * 100 / $valfac2[$k];
                            }
                            else
                            {
                                $porcen = 0;
                            }
                            // ahora vamos a consultar las notas debito por conceptos de cartera que cuentan por configuracion en el maestro
                            for ($d = 1;$d <= count($deb);$d++) // para cada fuente busco notas
                            {
                                $q = " SELECT sum(rdevco) FROM " . $wbasedato . "_000021 A, " . $wbasedato . "_000044  WHERE rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "' and rdefue='" . $deb[$d] . "' AND rdeest='on' and rdecon<>'' and rdevco<>'0' and concod=  (mid(rdecon,1,instr(rdecon,'-')-1)) and conest= 'on' and conacp='on' and  A.Fecha_data<='" . $wfecfin . "'";
                                $resdeb = mysql_query($q, $conex);
                                $row2deb = mysql_fetch_row($resdeb); //para sumar el total de nota debito
                                $wvaldeb = round($wvaldeb + $row2deb[0] * $porcen / 100);
                            }
                            // ahora vamos a consultar las notas credito por conceptos de cartera y decidir si se descuentan o no
                            for ($d = 1;$d <= count($cre);$d++) // para cada fuente busco notas
                            {
                                $q = " SELECT sum(rdevco) FROM " . $wbasedato . "_000021 A, " . $wbasedato . "_000044  WHERE rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "' and rdefue='" . $cre[$d] . "' AND rdeest='on' and rdecon<>'' and rdevco<>'0' and concod=  (mid(rdecon,1,instr(rdecon,'-')-1)) and conest= 'on' and conacp='on' and  A.Fecha_data<='" . $wfecfin . "' ";
                                $rescre = mysql_query($q, $conex) ;
                                $row2cre = mysql_fetch_row($rescre);
                                $wvalcre = round($wvalcre + $row2cre[0] * $porcen / 100);
                            }

                            echo "<td align=left " . $clase1 . " width='10%'>" . number_format($wvaldeb, 0, '.', ',') . "</td>";
                            echo "<td align=left " . $clase1 . " width='10%'>" . number_format($wvalcre, 0, '.', ',') . "</td>";
                            echo "<td align=left " . $clase1 . " width='10%'>" . number_format($row2[1], 0, '.', ',') . "</td>";
                            // calculo el valor a pagar
                            $wvalpag = round(($row2[0] + $wvaldeb - $wvalcre - $row2[2]) * $row2[1] / 100);
                            echo "<td align=left " . $clase2 . " width='10%'>" . number_format($wvalpag, 0, '.', ',') . "</td></tr>";
                            // realizo la suma o acumulacion de todos los valores para el concepto
                            $valfc = $valfc + $valfac2[$k];
                            $salfc = $salfc + $salfac2[$k];
                            $cval = $cval + $row2[0];
                            $cdes = $cdes + $row2[2];
                            $cdeb = $cdeb + $wvaldeb;
                            $ccre = $ccre + $wvalcre;
                            $cpag = $cpag + $wvalpag;
                            $pincon = 1;
                            // realizo la acumulacion del valor a pagar para empresa o particular
                            if ($row3[5] == '01-PARTICULAR')
                            {
                                $valpar = $valpar + $wvalpag;
                                $talpar = $talpar + $wvalpag;
                            }
                            else
                            {
                                $valemp = $valemp + $wvalpag;
                                $talemp = $talemp + $wvalpag;
                            }
                        }
                    }
                }
            }

            if ($pinfin == 1)
            {
                echo "<tr><th align=CENTER class='acumulado3' colspan='5' >TOTAL CONCEPTO</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($valfc, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($salfc, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($cval, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($cdes, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($ccre, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >&nbsp;</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($cpag, 0, '.', ',') . "</th><tr>";
                // realizo la suma o acumulacion de todos los valores para el tercero
                $valft = $valft + $valfc;
                $salft = $salft + $salfc;
                $tval = $tval + $cval;
                $tdes = $tdes + $cdes;
                $tdeb = $tdeb + $cdeb;
                $tcre = $tcre + $ccre;
                $tpag = $tpag + $cpag;
                $pincon = 0;
                $pinter++;

                echo "<tr><th align=CENTER class='acumulado2' colspan='5' >TOTAL TERCER0</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($valft, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($salft, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tval, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tdes, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tdeb, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tcre, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >&nbsp;</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><tr>";
                // realizo la suma o acumulacion de todos los valores finales
                $valff = $valff + $valft;
                $salff = $salff + $salft;
                $fval = $fval + $tval;
                $fdes = $fdes + $tdes;
                $fdeb = $fdeb + $tdeb;
                $fcre = $fcre + $tcre;
                $fpag = $fpag + $tpag;

                echo "</table></br>";

                echo "<table align='center' >";
                echo "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A EMPRESAS: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($talemp, 0, '.', ',') . "</th>";
                echo "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A PARTICULARES: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($talpar, 0, '.', ',') . "</th><TR>";
                echo "<TR><th align=CENTER class='acumulado5' >=VALOR FACTURADO TOTAL: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><TR>";

                echo "<TR><th align=CENTER class='acumulado5' >= TOTAL A PAGAR: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><TR>";
                echo "</table></br>";

                echo "<table align='center' width='1000' >";
                echo "<tr><td align=CENTER class='titulo2'>&nbsp;</td>";
                echo "<td align=CENTER class='titulo2'><B>VALOR FACTURA</B></td>";
                echo "<td align=CENTER class='titulo2'><B>SALDO FACTURA</B></td>";
                echo "<td align=CENTER class='titulo2'><B>VALOR CARGO</B></td>";
                echo "<td align=CENTER class='titulo2'><B>VALOR DESCUENTO</B></td>";
                echo "<td align=CENTER class='titulo2'><B>NOTAS DEBITO</B></td>";
                echo "<td align=CENTER class='titulo2'><B>NOTAS CREDITO</B></td>";
                echo "<td align=CENTER class='titulo2'><B>VALOR A PAGAR</B></td></tr>";
                echo "<tr><th align=CENTER class='acumulado1'>TOTALES</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($valff, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($salff, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fval, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fdes, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fdeb, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fcre, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fpag, 0, '.', ',') . "</th><tr>";
                echo "</table></br>";

                echo "<table align='center' >";
                echo "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A EMPRESAS: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($valemp, 0, '.', ',') . "</th>";
                echo "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A PARTICULARES: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($valpar, 0, '.', ',') . "</th><TR>";
                echo "<TR><th align=CENTER class='acumulado5' >=VALOR FACTURADO TOTAL: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($fpag, 0, '.', ',') . "</th><TR>";

                echo "<TR><th align=CENTER class='acumulado5' >= TOTAL A PAGAR: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($fpag, 0, '.', ',') . "</th><TR>";
                echo "</table></br>";
            }
            else
            {
                echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
                echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento que coincida con los paremtros seleccionados</td></tr>";
                echo "</table></br>";
            }

            echo "<center><A href='penter.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wter=" . $wter . "&amp;bandera='1'>VOLVER</A></center>";
            echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
        }
    }
	liberarConexionBD($conex);
    ?>
</body>
</html>
