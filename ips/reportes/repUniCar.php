<?php
include_once("conex.php");
    /*
        2016-05-19 camilo zapata: creación del script
        //ACTUALIZACIONES.
        2016-10-05 camilo zapata: consolidacion de totales por usuario en cargos no facturables y adicion del campo tipo de facturacion en todos los reportes
    */
    if(!isset($_SESSION['user'])){
        echo "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
            [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>";
        return;
    }
    include_once( "root/comun.php" );
    require_once("conex.php");
    mysql_select_db( "matrix" );
    $wactualiz   = "2016-10-05";
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedato   = strtolower( $institucion->baseDeDatos );
    $wentidad    = $institucion->nombre;

    if (isset($accion) and $accion == 'generarReporte'){
        $data = array('error'=>0,'mensaje'=>'','html'=>'','valor'=>'','usu'=>'');
        $json = generarReporte();
        echo $json;
        exit;
    }

    function inicializarArreglos(){

        global $empresas;
        global $conex;
        global $wbasedato;
        global $wemp_pmla;
        global $usuarios;

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

        $query = "   SELECT codigo, descripcion
                       FROM usuarios, {$wbasedato}_000030
                      WHERE activo = 'A'
                        AND codigo = cjeusu
                      ORDER BY codigo, descripcion";
        $res2  = mysql_query($query,$conex);
        $num2  = mysql_num_rows($res2);
        while($row2 = mysql_fetch_array($res2)){
             $row2['descripcion'] = utf8_encode( $row2['descripcion'] );
             $row2['descripcion'] = str_replace( $caracteres, $caracteres2, $row2['descripcion'] );
             array_push( $usuarios, trim($row2['codigo']).", ".trim($row2['descripcion']) );
        }
    }

    function buscarCargosAnulados(  $historia, $ingreso, $fecha_inicia, $fecha_final, $usuario ){
        global $wbasedato;
        global $conex;

        $html    = "";
        $wtotfac = 0;
        $wtotexe = 0;
        $wtotrec = 0;
        $condicionUsuario = "";
        $condicionIngreso = "";

        if( $historia != "" and $ingreso != "" ){
            $condicionIngreso = " tcarhis='{$historia}' AND tcaring = '{$ingreso}' ";
            $condicionFechas  = "";
        }else{
            $condicionIngreso = "";
            $condicionFechas  = " tcarfec between '{$fecha_inicia}' AND '{$fecha_final}' ";
        }

        ( $usuario == "" ) ? $condicionUsuario = "" : $condicionUsuario=" AND tcarusu = '{$usuario}'";

        $q =  " SELECT {$wbasedato}_000106.id, tcarhis, tcaring, tcarfec, tcarser, tcarconcod, tcarconnom, tcarprocod, tcarpronom, tcarcan, tcarvto,tcarfex, tcarfre, tcarusu, descripcion, tcartfa
                  FROM {$wbasedato}_000106, usuarios
                 WHERE {$condicionFechas}
                       {$condicionIngreso}
                   AND tcarest = 'off'
                       {$condicionUsuario}
                   AND tcarusu = codigo
                 order by tcarusu,tcarhis,{$wbasedato}_000106.id, tcaring,tcarfec";

        $rs  = mysql_query( $q, $conex );
        $num = mysql_num_rows( $rs );
        $empleadoActual = "";

        if( $num > 0 ){
             $html .= "<table>";
        }
        while( $row = mysql_fetch_array($rs) ){
            $empleadoNuevo = $row[13];

            if( $empleadoNuevo != $empleadoActual ){

                $i = 0;
                if($empleadoActual !="" ){//--> si ya se habian impreso datos entonces se imprimen los totales
                    $html .= "<tr>";
                        $html .= "<th align='left'  class='encabezadoTabla' colspan='10'><font size=3 color='FFFFFF'>TOTAL USUARIO</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
                    $html .= "</tr>";
                    $whisfac = 0;
                    $whisexe = 0;
                    $whisrec = 0;
                }

                $html .= "<tr><td align='center' colspan='2' class='encabezadoTabla'>USUARIO :</td>";
                $html .= "<td align='center' colspan='11' class='fila1'>{$row[13]} - {$row[14]}</td></tr>";
                $html .= "<tr class='encabezadoTabla'>";
                $html .= "<th align='center'>NRO REGISTRO</th>";
                $html .= "<th align='center'>HISTORIA</th>";
                $html .= "<th align='center'>INGRESO</th>";
                $html .= "<th align='center'>FECHA CARGO</th>";
                $html .= "<th align='center'>CENTRO COSTOS</th>";
                $html .= "<th align='center'>CONCEPTO</th>";
                $html .= "<th align='center'>DESCRIPCION</th>";
                $html .= "<th align='center'>PROCEDIMIENTO</th>";
                $html .= "<th align='center'>DESCRIPCION</th>";
                $html .= "<th align='center'>CANTIDAD</th>";
                $html .= "<th align='center'>VALOR TOTAL</th>";
                $html .= "<th align='center'>RECONOCIDO</th>";
                $html .= "<th align='center'>EXEDENTE</th>";
                $html .= "<th align='center'>TIP. FACT</th>";
                $html .= '</tr>';

                $empleadoActual = $empleadoNuevo;
            }

            ( is_int( $i/2) ) ? $wclass = "fila1" : $wclass="fila2";
            $i++;
            $html .= "<tr>";
                $html .= "<td align='center' class='{$wclass}'>".$row[0]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[1]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[2]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[3]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[4]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[5]."</td>";
                $html .= "<td align='left' class='{$wclass}'>".$row[6]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[7]."</td>";
                $html .= "<td align='left' class='{$wclass}'>".$row[8]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[9]."</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[10],0,'.',',')."</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[11],0,'.',',')."</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[12],0,'.',',')."</td>";
                $html .= "<td align='right' class='{$wclass}'>".$row[15]."</td>";
            $html .= "</tr>";

            $whisfac = $whisfac + $row[10];
            $whisexe = $whisexe + $row[11];
            $whisrec = $whisrec + $row[12];

            $wtotfac = $wtotfac+$row[10];
            $wtotexe = $wtotexe+$row[11];
            $wtotrec = $wtotrec+$row[12];
        }


        if( $num > 0 ){
             $html .= "<tr>";
                $html .= "<th align='left'  class='encabezadoTabla' colspan='10'><font size=3 color='FFFFFF'>TOTAL USUARIO</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
            $html .= "</tr>";
             $html .= "<tr>";
                $html .= "<th align='left'  class='encabezadoTabla' colspan='10'><font size=3 color='FFFFFF'>TOTAL GENERAL</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotexe,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotrec,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
            $html .= "</tr>";
            $html .= "</table>";
        }else{
            $html .= "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                    [?] No se encontraron datos para la consulta.<br />
                    </div>";
        }
        echo $html;
        return $html;
    }

    function buscarCargosSinFacturar( $historia, $ingreso, $fecha_inicia, $fecha_final, $wempresa ){

        global $wbasedato;
        global $conex;

        $html    = "";
        $wtotfac = 0;
        $wtotexe = 0;
        $wtotrec = 0;
        $condicionIngreso = "";

        if( $historia != "" and $ingreso != "" ){
            $condicionIngreso = " inghis='{$historia}' AND ingnin = '{$ingreso}' ";
            $condicionFechas  = "";
        }else{
            $condicionIngreso = "";
            $condicionFechas  = " ingfei between '{$fecha_inicia}' AND '{$fecha_final}' ";
        }

        ( $wempresa == "" ) ? $condicionEmpresa = "" : $condicionEmpresa =" AND mid(tcarres,1,instr(tcarres,'-')-1) = '".trim($wempresa)."'";
        $q =  " SELECT {$wbasedato}_000106.id, inghis, tcaring, tcarfec, tcarser, tcarconcod, tcarconnom, tcarprocod, tcarpronom, tcarcan, tcarvto,tcarfex,
                        tcarfre, tcarusu, concat_ws(' ',tcarno1,tcarno2,tcarap1,tcarap2) as tcarnom, tcarres, tcartfa
                  FROM  {$wbasedato}_000101, {$wbasedato}_000106
                 WHERE  {$condicionFechas}
                        {$condicionIngreso}
                   AND tcarvto != tcarfex + tcarfre
                   AND inghis = tcarhis
                   AND ingnin = tcaring
                   AND tcarfac = 'S'
                   AND tcarest = 'on'
                   {$condicionEmpresa}
             ORDER BY inghis, {$wbasedato}_000106.id, tcaring,tcarfec";

        $rs  = mysql_query( $q, $conex );
        $num = mysql_num_rows( $rs );
        $historiaActual = "";

        if( $num > 0 ){
             $html .= "<table>";
        }

        while( $row = mysql_fetch_array($rs) ){

            $historiaNueva = $row[1]."-".$row[2];
            if( $historiaNueva != $historiaActual ){

                if($historiaActual !="" ){//--> si ya se habian impreso datos entonces se imprimen los totales
                    $html .= "<tr>";
                        $html .= "<th align='left'  class='encabezadoTabla' colspan='11'><font size=3 color='FFFFFF'>TOTAL USUARIO</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whiscan,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla' colspan='4'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
                    $html .= "</tr>";
                    $whiscan = 0;
                    $whisfac = 0;
                    $whisexe = 0;
                    $whisrec = 0;
                }

                $html .= "<tr class='encabezadoTabla'>";
                $html .= "<td align='center'>NRO REGISTRO</td>";
                $html .= "<td align='center'>HISTORIA</td>";
                $html .= "<td align='center'>INGRESO</td>";
                $html .= "<td align='center'>FECHA CARGO</td>";
                $html .= "<td align='center'>CENTRO COSTOS</td>";
                $html .= "<td align='center'>NOMBRE DE PACIENTE</td>" ;
                $html .= "<td align='center'>EMPRESA RESPONSABLE</td>";
                $html .= "<td align='center'>CONCEPTO</td>";
                $html .= "<td align='center'>DESCRIPCION</td>";
                $html .= "<td align='center'>PROCEDIMIENTO</td>";
                $html .= "<td align='center'>DESCRIPCION</td>";
                $html .= "<td align='center'>CANTIDAD</td>";
                $html .= "<td align='center'>VALOR TOTAL</td>";
                $html .= "<td align='center'>RECONOCIDO</td>";
                $html .= "<td align='center'>EXEDENTE</td>";
                $html .= "<td align='center'>TIP. FACT</td>";
                $html .= "<td align='center'>USUARIO</td>";
                $html .= "</tr>";
                $historiaActual = $historiaNueva;
            }

            ( is_int( $i/2) ) ? $wclass = "fila1" : $wclass="fila2";
            $i++;
            $whiscan = $whiscan + $row[9]*1;
            $whisfac = $whisfac + $row[10]*1;
            $whisexe = $whisexe + $row[11]*1;
            $whisrec = $whisrec + $row[12]*1;

            $html .= "<tr>";
                $html .= "<td align='center' class='{$wclass}'>".$row[0]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[1]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[2]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[3]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[4]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[14]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[15]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[5]."</td>";
                $html .= "<td align='left' class='{$wclass}'>".$row[6]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[7]."</td>";
                $html .= "<td align='left' class='{$wclass}'>".$row[8]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[9]."</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[10],0,'.',',')."</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[11],0,'.',',')."</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[12],0,'.',',')."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[16]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[13]."</td>";
            $html .= "</tr>";

            $wtotcan = $wtotcan+$row[9]*1;
            $wtotfac = $wtotfac+$row[10]*1;
            $wtotexe = $wtotexe+$row[11]*1;
            $wtotrec = $wtotrec+$row[12]*1;

        }

        if( $num > 0 ){
             $html .= "<tr>";
                $html .= "<th align='left'  class='encabezadoTabla' colspan='11'><font size=3 color='FFFFFF'>TOTAL USUARIO</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whiscan,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla' colspan='4'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
            $html .= "</tr>";
             $html .= "<tr>";
                $html .= "<th align='left'  class='encabezadoTabla' colspan='11'><font size=3 color='FFFFFF'>TOTAL GENERAL</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotcan,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotexe,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotrec,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla' colspan='4'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
            $html .= "</tr>";
            $html .= "</table>";
        }else{
            $html .= "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                    [?] No se encontraron datos para la consulta.<br />
                    </div>";
        }

        echo $html;
        return($html);
    }

    function buscarCargosNoFacturables( $historia, $ingreso, $fecha_inicia, $fecha_final, $usuario ){
        global $wbasedato;
        global $conex;


        if( $historia != "" and $ingreso != "" ){
            $condicionIngreso = " tcarhis='{$historia}' AND tcaring = '{$ingreso}' ";
            $condicionFechas  = "";
        }else{
            $condicionIngreso = "";
            $condicionFechas  = " Tcarfec between '{$fecha_inicia}' AND '{$fecha_final}' ";
        }

        $html    = "";
        $q =  " SELECT Tcarhis,Tcaring,Tcarfec,Tcarsin, CONCAT(Tcarno1,' ',Tcarno2,' ',Tcarap1,' ',Tcarap2) nombrepaciente,Tcarres,
                       Tcarconcod,Tcarconnom,Tcarprocod,Tcarpronom,Tcarcan,Tcarvun,
                       Tcarvto,Tcarest,Tcarapr,Tcarusu, tcartfa
                  FROM {$wbasedato}_000106
                 WHERE {$condicionIngreso}
                       {$condicionFechas}
                   AND Tcarfac = 'N'
                   AND Tcarest = 'on' ";
        $rs  = mysql_query( $q, $conex );
        $num = mysql_num_rows( $rs );
        $historiaActual = "";

        if( $num > 0 ){
             $html .= "<table>";
        }
        $whiscan = 0;
        $whisfac = 0;
        $whisexe = 0;
        $whisrec = 0;
        while( $row = mysql_fetch_array($rs) ){

            $historiaNueva = $row[1]."-".$row[2];
            if( $historiaNueva != $historiaActual ){

                if($historiaActual !="" ){//--> si ya se habian impreso datos entonces se imprimen los totales
                    $html .= "<tr>";
                        $html .= "<th align='left'  class='encabezadoTabla' colspan='10'><font size=3 color='FFFFFF'>TOTAL USUARIO</font></th>";
                        //$html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whiscan,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";
                        $html .= "<th align='right' class='encabezadoTabla' colspan='4'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
                    $html .= "</tr>";
                    $whiscan = 0;
                    $whisfac = 0;
                    $whisexe = 0;
                    $whisrec = 0;
                }

                /*
                Historia (Tcarhis=)
                Ingreso (Tcaring=)
                Fecha del cargo (Tcarfec=)
                Servicio de ingreso (Tcarsin=)
                Nombre paciente (Tcarno1=)(Tcarno2=)(Tcarap1=)(Tcarap2=)
                Empresa Responsable (Tcarres=)
                Concepto (Tcarconcod=)
                Nombre concepto (Tcarconnom=)
                Procedimiento (Tcarprocod=)
                Nombre Procedimiento (Tcarpronom=)
                Cantidad (Tcarcan=)
                Valor Unitario (Tcarvun=)
                Valor Total (Tcarvto=)
                Estado (Tcarest=)
                Aprovechamiento (Tcarapr=)
                Usuario (Tcarusu=)
                */

                $html .= "<tr class='encabezadoTabla'>";
                $html .= "<td align='center'>HISTORIA</td>";
                $html .= "<td align='center'>INGRESO</td>";
                $html .= "<td align='center'>FECHA CARGO</td>";
                $html .= "<td align='center'>SERVICIO ING.</td>";
                $html .= "<td align='center'>NOMBRE DE PACIENTE</td>" ;
                $html .= "<td align='center'>EMPRESA RESPONSABLE</td>";
                $html .= "<td align='center'>CONCEPTO</td>";
                $html .= "<td align='center'>DESCRIPCION</td>";
                $html .= "<td align='center'>PROCEDIMIENTO</td>";
                $html .= "<td align='center'>DESCRIPCION</td>";
                $html .= "<td align='center'>CANTIDAD</td>";
                $html .= "<td align='center'>VALOR UNITARIO</td>";
                $html .= "<td align='center'>VALOR TOTAL</td>";
                $html .= "<td align='center'>ESTADO</td>";
                $html .= "<td align='center'>APROVECHAMIENTO</td>";
                $html .= "<td align='center'>TIP. FACT</td>";
                $html .= "<td align='center'>USUARIO</td>";
                $html .= "</tr>";
                $historiaActual = $historiaNueva;
            }

            ( is_int( $i/2) ) ? $wclass = "fila1" : $wclass="fila2";
            $i++;

            //$whiscan = $whiscan + $row[9]*1;
            $whisfac = $whisfac + $row[10];
            $whisexe = $whisexe + $row[11];
            $whisrec = $whisrec + $row[12];

            $html .= "<tr>";
                $html .= "<td align='center' class='{$wclass}'>".$row[0]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[1]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[2]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[3]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[4]."</td>";;
                $html .= "<td align='center' class='{$wclass}'>".$row[5]."</td>";
                $html .= "<td align='left' class='{$wclass}'>".$row[6]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[7]."</td>";
                $html .= "<td align='left' class='{$wclass}'>".$row[8]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[9]."wepaa</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[10],0,'.',',')."</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[11],0,'.',',')."</td>";
                $html .= "<td align='right' class='{$wclass}'>".number_format($row[12],0,'.',',')."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[13]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[14]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[16]."</td>";
                $html .= "<td align='center' class='{$wclass}'>".$row[15]."</td>";

            $html .= "</tr>";

            //$wtotcan = $wtotcan+$row[9]*1;
            $wtotfac = $wtotfac+$row[10]*1;
            $wtotexe = $wtotexe+$row[11]*1;
            $wtotrec = $wtotrec+$row[12]*1;

        }

        if( $num > 0 ){
             $html .= "<tr>";
                $html .= "<th align='left'  class='encabezadoTabla' colspan='10'><font size=3 color='FFFFFF'>TOTAL USUARIO</font></th>";
                //$html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whiscan,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla' colspan='4'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
            $html .= "</tr>";
             $html .= "<tr>";
                $html .= "<th align='left'  class='encabezadoTabla' colspan='10'><font size=3 color='FFFFFF'>TOTAL GENERAL</font></th>";
                //$html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotcan,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotexe,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla'><font size=2 color='FFFFFF'>".number_format($wtotrec,0,'.',',')."</font></th>";
                $html .= "<th align='right' class='encabezadoTabla' colspan='4'><font size=2 color='FFFFFF'>&nbsp;</font></th>";
            $html .= "</tr>";
            $html .= "</table>";
        }else{
            $html .= "<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                    [?] No se encontraron datos para la consulta.<br />
                    </div>";
        }

        echo $html;
        return;
    }

    function generarReporte(){
        global $data;
        global $wbasedato;
        global $parametros;
        global $tipoReporte;

        $error       = 0;
        $parametros  = str_replace("\\", "", $parametros );
        $parametros  = json_decode($parametros);

        switch ($tipoReporte) {

            case '1'://-->Cargos anulados
                $respuesta = buscarCargosAnulados( $parametros->whistoria, $parametros->wingreso, $parametros->fecha_inicial, $parametros->fecha_final, $parametros->usuario);
                break;
            case '2'://-->Cargos sin facturar
                $respuesta = buscarCargosSinFacturar( $parametros->whistoria, $parametros->wingreso, $parametros->fecha_inicial, $parametros->fecha_final, $parametros->empresa);
                break;
            case '3'://--> cargos no facturables
                $respuesta = buscarCargosNoFacturables( $parametros->whistoria, $parametros->wingreso, $parametros->fecha_inicial, $parametros->fecha_final, $parametros->usuario);
                break;

            default:

                break;
        }
        /*$data = array( 'respuesta' => $respuesta, 'error'=>$error );
        echo json_encode( $data );*/
        return;
    }


?>

<!DOCTYPE html>
<html>
<head>
    <title> REPORTE UNIFICADO DE CARGOS </title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
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
    <!--<script src="../../../include/root/toJson.js" type="text/javascript"></script>-->
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
    <script type="text/javascript" charset="utf-8">

        //--> codigo javascript propio
        $(document).ready(function(){

            empresas_nombres_array = new Array();
            var empresas = eval( $("#array_empresas").val() );
            for( i in empresas ){
                empresas_nombres_array.push( empresas[i] );
            }

            $("#inp_empresa").on("keyup", function(){
               if( $(this).val() == "" ){
                   console.log( $(this).val() );
                   $(this).parent().find("#wempresa").val("");
               }
            });

            $( "#inp_empresa" ).autocomplete({
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

            usuarios_nombres_array = new Array();
            var usuarios = eval( $("#array_usuarios").val() );
            for( i in usuarios ){
                usuarios_nombres_array.push( usuarios[i] );
            }

            $("#inp_usuario").on("keyup", function(){
               if( $(this).val() == "" ){
                   console.log( $(this).val() );
                   $(this).parent().find("#wusuario").val("");
               }
            });

            $( "#inp_usuario" ).autocomplete({
                    source    : usuarios_nombres_array,
                    minLength : 2,
                    messages: {
                        noResults: '',
                        results: function() {}
                    },
                    select: function( event, ui ) {
                        var usuarioseleccionado = ui.item.value;
                        if( $.trim(usuarioseleccionado) != "" ){
                            usuarioseleccionado = usuarioseleccionado.split(",");
                            usuarioseleccionado = $.trim( usuarioseleccionado[0] );
                            $(this).parent().find("#wusuario").val(usuarioseleccionado);
                        }else{
                            $(this).parent().find("#wusuario").val("");
                        }
                    }
            });


            $("#inp_fecha_inicial").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
                buttonText: "",
                maxDate: "+0m +0w"
            });

            $("#inp_fecha_final").datepicker({
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

        function elegirTipoReporte( obj ){
            var tipoReporte = $( obj ).val();
            $("#div_formulario_parametros").find("tr[utilizado!='*'][utilizado!='"+tipoReporte+"'][tipo!='encabezado']").hide();
            $("#div_formulario_parametros").find("tr[utilizado='"+tipoReporte+"'][tipo!='encabezado']").show();
             $("#div_resultados").html( "" );

        }

        function generarReporte(){

            if( $("input[name='tipoReporte']:checked").val() == undefined ){
                alerta("debe seleccionar un tipo de reporte");
                return;
            }

            var parametros = new Object();

            $("input:visible").each(function(){
                parametros[ $(this).attr("name") ] = $("#"+$(this).attr("objeto")+"").val();
            });
            JsonParametros = JSON.stringify( parametros );

            $.ajax({
                url: "repUniCar.php",
               type: "POST",
             before: $.blockUI({ message: $('#msjEspere') }),
               data: {
                     consultaAjax: "on",
                           accion: "generarReporte",
                        wemp_pmla: $("#wemp_pmla").val(),
                     tipoReporte : $("input[name='tipoReporte']:checked").val(),
                       parametros: JsonParametros
                      },
                success: function(data)
                {
                    $.unblockUI();
                    if( data == "NO" ){
                        alerta( "Sin pacientes Asociados" );
                    }else{
                        $("#div_resultados").html( data );
                        $("#div_resultados").show();
                    }
                }
            });
        }

        function alerta( txt ){
            $("#textoAlerta").text( txt );
            $.blockUI({ message: $('#msjAlerta') });
                setTimeout( function(){
                    $.unblockUI();
                }, 4000 );
        }
    </script>

</head>
<body>
    <?php
        $empresas    = array();
        $usuarios    = array();
        inicializarArreglos();
        $empresas    = json_encode( $empresas );
        $usuarios    = json_encode( $usuarios );
    ?>
    <?php encabezado( " REPORTE UNIFICADO DE CARGOS ", $wactualiz, "clinica" ); ?>

    <input type='hidden' id='wemp_pmla' value='<?=$wemp_pmla;?>'>
    <input type='hidden' name='array_empresas' id='array_empresas' value='<?=$empresas?>'>
    <input type='hidden' name='array_usuarios' id='array_usuarios' value='<?=$usuarios?>'>

    <div id='div_formulario_parametros' align='center'>

         <table style='width:600px;'>
            <thead>
                <tr class='encabezadoTabla' tipo='encabezado'>
                    <th colspan='3'>TIPO DE FORMULARIO</th>
                </tr>
            </thead>
            <tbody>
                <tr tipo='encabezado'>
                    <td><input type="radio" name="tipoReporte" value="1" onClick="elegirTipoReporte( this );">Cargos Anulados</td>
                    <td><input type="radio" name="tipoReporte" value="2" onClick="elegirTipoReporte( this );">Cargos sin Facturar</td>
                    <td><input type="radio" name="tipoReporte" value="3" onClick="elegirTipoReporte( this );">Cargos No Facturables</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style='width:600px;'>
            <thead>
                <tr class='encabezadoTabla' tipo='encabezado'>
                    <th colspan='2'>Parámetros</th>
                </tr>
            </thead>
            <tbody>
                <tr utilizado='*'>
                    <td style='width:30%;' class='td_item_buscar'>Historia:</td><td> <input type="text" objeto='whistoria' name="whistoria" id="whistoria" value=""> </td>
                </tr>
                <tr utilizado='*'>
                    <td style='width:30%;' class='td_item_buscar'>Ingreso:</td><td> <input type="text" objeto='wingreso' name="wingreso" id="wingreso" value=""> </td>
                </tr>
                <tr utilizado='*'>
                    <td style='width:30%;' class='td_item_buscar'>Rango:</td><td> <input type="text" objeto='inp_fecha_inicial' name="fecha_inicial" id="inp_fecha_inicial" disabled value="<?=date('Y-m-d')?>"> hasta <input type="text" objeto='inp_fecha_final' name="fecha_final" id="inp_fecha_final" disabled value="<?=date('Y-m-d')?>"></td>
                </tr>
                <tr utilizado='2'>
                    <td style='width:30%;' class='td_item_buscar'>Empresa:</td><td> <input type="text" objeto='wempresa' name="empresa" id="inp_empresa" value=""> <input type='hidden' name='wempresa' id='wempresa' value=''> </td>
                </tr>
                <tr utilizado='1'>
                    <td style='width:30%;' class='td_item_buscar'>Usuario:</td><td> <input type="text" objeto='wusuario' name="usuario" id="inp_usuario" value=""> <input type='hidden' name='wusuario' id='wusuario' value=''> </td>
                </tr>
            </tbody>
        </table>
        <br>
        <center><input type="button" name="btn_buscar" value="GENERAR" onclick="generarReporte();"></center><br><br>
    </div>
    <div id='div_resultados' style='display:none;'></div><br>

    <div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>

    <div id='msjEspere' style='display:none;'>
        <br>
        <img src='../../images/medical/ajax-loader5.gif'/>
        <br><br> Por favor espere un momento ... <br><br>
    </div>

    <div id='msjAlerta' style='display:none;'>
        <br>
        <img src='../../images/medical/root/Advertencia.png'/>
        <br><br><div id='textoAlerta'></div><br><br>
    </div>
</body>
</html>