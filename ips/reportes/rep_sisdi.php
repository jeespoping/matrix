    <?php
    include_once("conex.php");
/*   * *********************************************************
     *                 REPORTE PISIS                           *
     * ******************************************************* */
/*

    PROGRAMA                   : rep_sisdi.php
    AUTOR                      : Camilo Zapata.
    FECHA CREACION             : Noviembre 26 de 2018

    - DESCRIPCION:
        Programa que permite descargar el archivo .txt que contiene la información requerida para el PISIS
        Este programa solo requiere que se le proveea el rango de meses de documento que se desea generar, este
        documento responde a las exigencias hechas por la COMISIÓN NACIONAL DE PRECIOS DE MEDICAMENTOS Y DISPOSITIVOS MÉDICOS en la Circular 2
        del 11 de Agosto de 2017

    - ACTUALIZACIONES:
*/
 if(isset($ajaxdes))
{
    //http://www.solingest.com/blog/descarga-de-archivos-en-php
    $wdesc = trim( $wdesc );
    header ("Content-Disposition: attachment; filename=".$wdesc." ");
    header ("Content-Type: application/octet-stream");
    header ("Content-Length: ".filesize($wdesc));
    readfile($wdesc);
    unlink($wdesc);
    //header("Location: http://localhost/matrix/ips/reportes/rep_Sismed.php?wemp_pmla='{$wemp_pmla}'");
}else{

    include_once("root/comun.php");
    $wbasedatos = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
    $conexunix  = odbc_pconnect('facturacion','informix','sco') or die("No se ralizo Conexion con Unix");

    function crear_archivo($filename,$content,$cont){//funcion que crea el archivo.

       if($cont==1){
         if (file_exists($filename))
         {
            unlink($filename);
         }
         $modo1 = 'w';
         $modo2 = 'a';
       }
       else{
         $modo1 = 'w+';
         $modo2 = 'a';
       }

       if (!file_exists($filename)){
               $reffichero = fopen($filename, $modo1);
        }

       // Let's make sure the file exists and is writable first.
       if (is_writable($filename))
       {

               // In our example we're opening $filename in append mode.
               // The file pointer is at the bottom of the file hence
               // that's where $content will go when we fwrite() it.
               if (!$handle = fopen($filename, $modo2))
               {
                        //echo "Cannot open file ($filename)";
                        exit;
               }

               // Write $content to our opened file.
               if (fwrite($handle, $content) === FALSE)
               {
                       //echo "Cannot write to file ($filename)";
                       exit;
               }

               //echo "Success, wrote ($content) to file ($filename)";

               fclose($handle);

       }
       else
       {
               //echo "The file $filename is not writable";
       }
    }

    function imprimirDatos($datos, $tipo, $ano, $mesI, $mesF){//funcion que agrega cada registro al archivo.

        //calculo de la fecha de corte "ultimo dia del periodo"
        global $nit;
        /*echo $mesI."\n";
        echo $mesF."\n";*/
        $ultimoDia = mktime(0,0,0,(($mesF*1)+1),0,$ano);
        $fechaCorte = date("Ymd",$ultimoDia);

        //buscar fecha de corte
        $nombre_archivo = "DIS".$tipo."_".$ano."_".$mesI."_".$mesF.".txt"; //Sm(SisMed)(c o v compras o ventas) año y mes.
        switch($tipo)
        {
            case 'v':
                $fuenteTipo = "113DVEN"; //fuente y tipo correspondiente a las ventas.
                break;
            case 'c':
                $fuenteTipo = "114DINS"; //fuente y tipo correspondiente a las compras.
                break;
        }
        //MED{fuentey tipo}.{fecha de corte correspondiente al último dia del mes final para el reporte}{NI: tipo de indentificación nit}{numero de la identificación}
        $nombre_archivo = "DIS".$fuenteTipo."".$fechaCorte."NI000{$nit}.txt";
        $cont =0;
        $regs = sizeof($datos);
        for($i=0; $i<($regs); $i++)//empieza en -1 para que la primer vez que entre cree el archivo.
        {
            if($i==$regs-1)
                $contenido = $datos[$i];
            else
                $contenido = $datos[$i]."
";

            if($contenido != '')
            {
                $cont++;
                crear_archivo($nombre_archivo,$contenido,$cont); // lo crea en el mismo directorio.
            }
        }
         echo $nombre_archivo;
    }


    function limpia_espacios($cadena){
       $cadena = str_replace(' ', '', $cadena);
       return $cadena;
    }

    //busca caracteres especiales y los elimina de la cadena
    function eliminarCaracteresEspeciales($cadena){
        $caracteres = array("|",".","'\'","%","&","/","(",")","?","¿","¡","!","#",",");
        $cadena = str_replace($caracteres,'',$cadena);
        return($cadena);
    }

    function consolidarVentas(&$ventasServinte, &$ventasMatrix, $ano, $mesIni, $mesFin){
        global $nit;
        global $dver;
        $cumsAux        = array(); //este va registrando los diferentes cums que se van presentando
        $cantidadCums   = 0;
        $totalRegistros = 0;
        $ventasTotales  = 0;
        $registros      = array(); // quedará en la forma ya implementada para no cambiar el resto del código
        $parcial        = array(); //arreglo que se llenara por las claves

        //ACÁ SE GUARDAN EN EL PARCIAL LAS VENTAS TRAIDAS DE MATRIX.
        foreach($ventasMatrix as $keyMes=>$ventasCums)
        {
            foreach($ventasCums as $keyCum=>$datosMatrix)
                {
                    $parcial[$keyMes][$keyCum]=$datosMatrix;
                }
        }

        //ACÁ SE GUARDAN EN EL PARCIAL LAS VENTAS TRAIDAS DE UNIX.
        if(count($ventasServinte)>0)
        {
            foreach($ventasServinte as $keyMes=>$CumsMes)
            {
                foreach($CumsMes as $keyCum=>$ventas)
                {
                    if(!isset($parcial[$keyMes]))
                        $parcial[$keyMes]=array();
                    if(!array_key_exists($keyCum, $parcial[$keyMes])) //si el cum traido de unix no está para el mes desde matrix simplemente se agrega
                    {
                        $parcial[$keyMes][$keyCum]=$ventas;
                    }else
                        {//en caso de que se encuentre, hay que sumar los totales y comparar los precios minimos y máximos para verificar por

                            $parcial[$keyMes][$keyCum]['total'] += $ventas['total'];
                            $parcial[$keyMes][$keyCum]['unidades'] += $ventas['unidades'];
                            $minActual = $parcial[$keyMes][$keyCum]['minVenta'];
                            $maxActual = $parcial[$keyMes][$keyCum]['maxVenta'];

                            if($ventas['minVenta']<$minActual)
                            {
                                $parcial[$keyMes][$keyCum]['minVenta']=$ventas['minVenta'];
                                $parcial[$keyMes][$keyCum]['facMin']=$ventas['facMin'];
                            }

                            if($ventas['maxVenta']>$maxActual)
                            {
                                $parcial[$keyMes][$keyCum]['maxVenta']=$ventas['maxVenta'];
                                $parcial[$keyMes][$keyCum]['facMax']=$ventas['facMax'];
                            }

                        }
                }
            }
        }

        //ACÁ RECORREMOS EL ARREGLO ARMANDO A "REGISTROS"
        foreach($parcial as $keyMes=>$ventasCums)
        {
            foreach($ventasCums as $keyCum=>$datos)
            {
                if($datos['total']>0)
                {
                    if(!in_array(trim($keyCum), $cumsAux))
                        {
                            array_push($cumsAux, $keyCum);
                        }
                    $totalRegistros++;
                    $ventasTotales+=number_format($datos['total'],2,".","");
                    if( $datos['unidades'] == 1 ){
                        $datos['facMax']   = 0;
                        //$datos['maxVenta'] = 0;
                        //$datos['tdMax']    = 0;
                        //$datos['crMax']    = 0;
                        $datos['canMax']   = 0;
                    }

                    $mesAuxiliar = str_split($datos['mes']);
                    if( $mesAuxiliar[0] == "0" )
                        $datos['mes'] = $mesAuxiliar[1];

                    $registros[$totalRegistros]="2|".$totalRegistros."|".$datos['mes']."|INS|".limpia_espacios($datos['cum'])."|".number_format($datos['minVenta'],2,".","")."|".number_format($datos['maxVenta'],2,".","")."|".number_format($datos['total'],2,".","")."|".$datos['unidades']."|".trim( eliminarCaracteresEspeciales($datos['facMin']) )."|".$datos['tdMin']."|".$datos['crMin']."|".$datos['canMin']."|".trim( eliminarCaracteresEspeciales($datos['facMax']) )."|".$datos['tdMax']."|".$datos['crMax']."|".$datos['canMax'];
                }
            }
        }
        $cantidadCums = count($cumsAux);
        if(($mesIni*1)<10)
             $mesIni = "0"."{$mesIni}";
            else
                $mesIni = "{$mesIni}";
        $mesIni       = $ano."-".$mesIni."-01";
        $ultimoDia    = mktime(0,0,0,(($mesFin*1)+1),0,$ano);
        $mesFin       = date("Y-m-d",$ultimoDia);
        $registros[0]="1|NI|{$nit}|".$mesIni."|".$mesFin."|".$totalRegistros;
        return($registros);
     }

    //funcion que genera las ventas de matrix e invoca en caso der ser necesario la funcion que consolida las ventas en unix
    function generarInformeVentas($concepto ){
        global $wbasedatos;
        global $conex;
        global $wemp_pmla;
        global $wmesI;
        global $wmesF;
        global $wano;
        $registros                         = array();
        $registrosParciales                = array();
        $relacionArticuloEmpresaFacturable = array();
        //declaración de variables que contendran los datos para el registro de contról, este registro se construirá cuando se tenga toda la información
        $ano            = $wano;
        $mesIni         = $wmesI;
        $mesFin         = $wmesF;
        $cantidadCums   = 0;
        $totalRegistros = 0;
        $ventasTotales  = 0;
        $cumAct         = " ";
        //-----------------------------------------------------------------------------------------------------------------------------------------------
        $query = "SELECT Aemart, Aememp, Aemind
                    FROM {$wbasedatos}_000214
                   WHERE Aemest = 'on'";
        $rs  = mysql_query($query,$conex) or die (mysql_error());

        while( $row = mysql_fetch_array($rs) ){
            $relacionArticuloEmpresaFacturable[$row['Aemart']."_".$row['Aememp']] = $row['Aemind'];
        }

        $query = "SELECT Detval
                    FROM root_000051
                   WHERE Detemp = '{$wemp_pmla}'
                     AND Detapl = 'conVenMatrixSis'";
        $rs = mysql_query($query,$conex) or die (mysql_error());
        $row = mysql_fetch_array($rs);
        $conVent =  explode(",",$row['Detval']);
        foreach($conVent as $i=>$concepto)
        {
            if($ventas=='')
                $ventas.="'{$concepto}'";
                else
                $ventas.=",'{$concepto}'";
        }
        //en este punto se contruye la consulta que contiene la información solicitada por el SISMED."REGISTRO TIPO 2"
        //primero se crea una tabla temporal, para mejorar el rendimiento del script, esta tabla incluye los filtros y joins entre las tablas 16 y 17
        $tmventas = "tmpVen1617".date('s');
        $qaux     = "DROP TABLE IF EXISTS $tmventas";
        $resdr    = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());

        if(($mesIni*1)<10)
             $mesIni = "0"."{$mesIni}";
            else
                $mesIni = "{$mesIni}";

        $fechaInicial = $ano."-".$mesIni."-01";
        $ultimoDia    = mktime(0,0,0,(($mesFin*1)+1),0,$ano);
        $fechaFinal   = date("Y-m-d",$ultimoDia);

        $qtemp = "CREATE TEMPORARY TABLE IF NOT  EXISTS $tmventas "
                        ."(INDEX idx(Vdeart))   "
                ."SELECT Tcarprocod Vdeart,  SUBSTRING( tcarfec,6,2 ) Venmes, Tcarcan Vdecan, ABS(Tcarvun) Vdevun, '--' Venffa, '----' Vennfa, SUBSTRING( tcarfec,1,4 ) Venano, 1 fac, tcardun, tcarlin, Tcarres as empresa, tcarcan cantidadCargada, tcarfun
                    FROM {$wbasedatos}_000106 a
                   WHERE Tcarfec BETWEEN '{$fechaInicial}' AND '{$fechaFinal}'
                     AND tcarest     = 'on'
                     AND Tcarconcod in ({$ventas})
                     AND Tcardev = 'off'
                     AND Tcarfac = 'S'
                     AND Tcarvun*1 > 0
                     AND ( tcarfun like '11%' or tcarfun like 'GD%' )";
        //echo "<pre>".print_r( $qtemp, true )."</pre>";

        $rstemp = mysql_query($qtemp,$conex) or die (mysql_errno().":".mysql_error());
        $query = "SELECT  Cudcod cum, Vdeart art, Venmes mes, (SUM(Vdecan*fac)*Cudequ) unidades, (SUM(Vdecan*Vdevun*fac)) total, MIN(Vdevun/Cudequ) minVal, MAX(Vdevun/Cudequ) maxVal, Cudcod, Cudequ, Venano, tcardun, tcarlin, empresa, cantidadCargada
                   FROM {$tmventas}, {$wbasedatos}_000319
                  WHERE Cumint = Vdeart
                    AND cumemp = '{$wemp_pmla}'
                  GROUP BY 1, 2, 3";

         $query = "SELECT  Cudcod cum, Vdeart art, Venmes mes, ((Vdecan*fac)*Cudequ) unidades, (Vdecan*Vdevun*fac) total, (Vdevun/Cudequ) minVal, (Vdevun/Cudequ) maxVal, Cudcod, Cudequ, Venano, tcardun, tcarlin, empresa, cantidadCargada, tcarfun
                   FROM {$tmventas}, {$wbasedatos}_000319
                  WHERE Cudint = Vdeart
                  order by mes*1 asc, 1 ";
        //echo "<pre>".print_r( $query, true )."</pre>";
        $rs = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
        $num = mysql_num_rows($rs);

        while($reg = mysql_fetch_array($rs))
        {
            if( $relacionArticuloEmpresaFacturable[$reg['art']."_".$reg['empresa']] == "N" )
                continue;

            $cambioMin      = false;
            $cambioMax      = false;
            //variables para el registro de control.
            $totalRegistros++;
            $ventasTotales  += $reg[3];
            //ESTAS VARIABLES NO CAMBIAN
            $cumAux         = trim($reg['cum']);
            $cumAux         = explode("-",$cumAux);
            $cumAux[0]      = $cumAux[0] * 1;
            $cumAux         = implode("-",$cumAux);
            $funAux         = explode("-",$reg['tcarfun']);
            $reg['tcarfun'] = $funAux[0];

            $registrosParciales[$reg['mes']][$cumAux]['mes']   = $reg['mes'];
            $registrosParciales[$reg['mes']][$cumAux]['canal'] = 'INS';
            $registrosParciales[$reg['mes']][$cumAux]['cum']   = $cumAux;

            $registrosParciales[$reg['mes']][$cumAux]['comercial'] = 'SI';
            //ACÁ SE VERIFICA SI HAY O NO DATOS ACTUALES PARA EL CUM EN EL MES DADO
            if(!isset($registrosParciales[$reg['mes']][$cumAux]['total']) or ($registrosParciales[$reg['mes']][$cumAux]['total']==''))
            {
                $registrosParciales[$reg['mes']][$cumAux]['total'] = 0;
                $cantidadCums++;
            }
                $registrosParciales[$reg['mes']][$cumAux]['total'] += $reg['total'];

            if(!isset($registrosParciales[$reg['mes']][$cumAux]['unidades']) or ($registrosParciales[$reg['mes']][$cumAux]['unidades']==''))
                $registrosParciales[$reg['mes']][$cumAux]['unidades'] = 0;
                $registrosParciales[$reg['mes']][$cumAux]['unidades'] += $reg['unidades'];

            if(!isset($registrosParciales[$reg['mes']][$cumAux]['minVenta'])or($registrosParciales[$reg['mes']][$cumAux]['minVenta']==''))
            {
                $registrosParciales[$reg['mes']][$cumAux]['minVenta'] = $reg['minVal']*1;
                $cambioMin=true;
            }else
                {
                    $minActual = $registrosParciales[$reg['mes']][$cumAux]['minVenta']*1;
                    $minNuevo = $reg['minVal'];
                    if( $minNuevo*1<$minActual*1 )
                    {
                        $registrosParciales[$reg['mes']][$cumAux]['minVenta'] = $reg['minVal'];
                        $cambioMin=true;
                    }
                }

            if(!isset($registrosParciales[$reg['mes']][$cumAux]['maxVenta'])or($registrosParciales[$reg['mes']][$cumAux]['maxVenta']==''))
            {
                $registrosParciales[$reg['mes']][$cumAux]['maxVenta'] = $reg['maxVal'];
                $cambioMax = true;
            }else
                {
                    $maxActual = $registrosParciales[$reg['mes']][$cumAux]['maxVenta']*1;
                    $maxNuevo = $reg['maxVal']*1;
                    if( $maxNuevo*1>$maxActual*1 )
                    {
                        $registrosParciales[$reg['mes']][$cumAux]['maxVenta'] = $reg['maxVal'];
                        $cambioMax = true;
                    }
                }

            if($cambioMin)
            {
                $datosFactura            = consultarFacturaAsociadaUnix( $reg['tcardun'], $reg['tcarlin'], $reg['tcarfun'] );
                $datosFactura['tipoRes'] = ( $datosFactura['tipoRes'] == "E") ? "NI" : "CC";
                $facturaAsociada         = $datosFactura['factura'];
                $tipoResponsable         = $datosFactura['tipoRes'];
                $codigoResponsable       = $datosFactura['docRes'];
                $registrosParciales[$reg['mes']][$cumAux]['facMin'] = $facturaAsociada;
                $registrosParciales[$reg['mes']][$cumAux]['tdMin']  = $tipoResponsable; // tipo documento factura minima
                $registrosParciales[$reg['mes']][$cumAux]['crMin']  = $codigoResponsable;  // codigo responsable
                $registrosParciales[$reg['mes']][$cumAux]['canMin'] = $reg['cantidadCargada'];  // cantidad vendida con precio minimo
            }
            //en esta parte vamos y consultamos las facturas que evidencian los precios mínimos y máximos de compra
            if($cambioMax)
            {
                $encontrado = "";
                /*if( $reg['cum'] == "019943753-02" and $reg['maxVal'] == 4535 ){
                    //$encontrado = "**";
                }

                if( $reg['cum'] == "019943753-02" and $reg['maxVal'] == 4535 ){
                    //echo "<pre>".print_r( $datosFactura, true )."</pre>";
                    $encontrado = "**";
                }*/
                $datosFactura            = consultarFacturaAsociadaUnix( $reg['tcardun'], $reg['tcarlin'], $reg['tcarfun'] );
                $datosFactura['tipoRes'] = ( $datosFactura['tipoRes'] == "E") ? "NI" : "CC";
                $facturaAsociada         = $datosFactura['factura'];
                $tipoResponsable         = $datosFactura['tipoRes'];
                $codigoResponsable       = $datosFactura['docRes'];
                $registrosParciales[$reg['mes']][$cumAux]['facMax'] = $facturaAsociada;
                $registrosParciales[$reg['mes']][$cumAux]['tdMax']  = $tipoResponsable;
                $registrosParciales[$reg['mes']][$cumAux]['crMax']  = $codigoResponsable;
                $registrosParciales[$reg['mes']][$cumAux]['canMax'] = $reg['cantidadCargada'];

            }/*else
                {
                    $registrosParciales[$reg['mes']][$cumAux]['facMax'] = $registrosParciales[$reg['mes']][$cumAux]['facMin'];
                    $registrosParciales[$reg['mes']][$cumAux]['tdMax']  = $registrosParciales[$reg['mes']][$cumAux]['tdMin'];
                    $registrosParciales[$reg['mes']][$cumAux]['crMax']  = $registrosParciales[$reg['mes']][$cumAux]['crMin'];
                    $registrosParciales[$reg['mes']][$cumAux]['canMax'] =  $registrosParciales[$reg['mes']][$cumAux]['canMin'];
                }*/
        }
        $registros = count( $registrosParciales );
        rsort( $registrosParciales );

        $registros = consolidarVentas($ventasServinte, $registrosParciales, $ano, $wmesI, $wmesF);

        imprimirDatos($registros, "v",$ano, $wmesI, $wmesF);
     }

    function consultarFacturaAsociadaUnix( $documentoUnix, $lineaUnix, $fuenteUnix ){
        global $conex;
        global $wemp_pmla;
        global $wbasedatos;
        global $conexunix;

        $resultado = array();

        $query = " SELECT carfacfue, carfacdoc, movemp, empnit
                     FROM facardet, facarfac, famov, inemp
                    WHERE cardetfue = '{$fuenteUnix}'
                      AND cardetdoc = '{$documentoUnix}'
                      AND cardetlin = '{$lineaUnix}'
                      AND cardetanu = '0'
                      AND carfacreg = cardetreg
                      AND movfue    = carfacfue
                      AND movdoc    = carfacdoc
                      AND movanu    = '0'
                      AND Movcer    = empcod
                      AND Movemp    = 'E'
                    UNION
                   SELECT carfacfue, carfacdoc, movemp, Movcer
                     FROM facardet, facarfac, famov
                    WHERE cardetfue = '{$fuenteUnix}'
                      AND cardetdoc = '{$documentoUnix}'
                      AND cardetlin = '{$lineaUnix}'
                      AND cardetanu = '0'
                      AND carfacreg = cardetreg
                      AND movfue    = carfacfue
                      AND movdoc    = carfacdoc
                      AND movanu    = '0'
                      AND Movemp    = 'P'";
        //echo $query;
        $resFac = odbc_exec( $conexunix,$query );
        while($row=odbc_fetch_row($resFac)){
            $resultado['factura'] = odbc_result($resFac, 1)."-".odbc_result($resFac, 2);
            $resultado['tipoRes'] = trim(odbc_result($resFac, 3));
            $resultado['docRes']  = trim(odbc_result($resFac, 4));
        }
        return( $resultado );
    }

    if(isset($cajax)){//si se hizo una petición ajax.

        if(isset($wdoc)){
            $qnit = "SELECT Empnit
                       FROM root_000050
                      WHERE empcod = '{$wemp_pmla}'";
            $rsnit = mysql_query($qnit, $conex);
            $regnit = mysql_fetch_array($rsnit);
            $nit1 = explode("-",$regnit[0]);
            $nit = $nit1[0];
            $dver = $nit1[1];
            switch ($wdoc)
            {
                case 'ventas':
                    $query = " SELECT detval
                                 FROM root_000051
                                WHERE detapl = 'manejaSedes'
                                  AND detemp = '{$wemp_pmla}'";
                    $rs    = mysql_query( $query, $conex );
                    $row   = mysql_fetch_assoc( $rs );
                    $concepto = $row[0];
                    //generarInformeVentas($concepto, $consultarUnix, $consultarCargos);
                    generarInformeVentas( $concepto );
                    break;
                case 'compras';
                    $query = "SELECT Concod
                                FROM {$wbasedatos}_000008
                               WHERE Conind='1'
                                 AND Conaca='on'
                                 AND Conaco='on'
                                 AND Condan='on'
                                 AND Conauc='on'
                                 AND Congec='off'";
                    $rs = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
                    $reg = mysql_fetch_row($rs);
                    $concepto = $reg[0];
                    generarInformeCompras($concepto, $consultarUnix, $consultarCargos);
                    break;
            }
          }

        return;
    }
}

/*$wano   = "2018";
$wmesI = "1";
$wmesF = "1";
echo "<br> edb-> ajam";
generarInformeVentas("0626");*/
//odbc_close($conexunix);
odbc_close_all();
if( isset($consultaAjax) )
    return;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="">
    <script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
     <script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
     <script type="text/javascript">
        function quitarEnlace(ele)
        {
            $(ele).parent().hide();
        }

        function descargar()//funcion que hace la petición ajax y facilita la descarga del documento.
        {
            var nombreArchivo;
            var archivo;
            var link;
            var wano  = document.getElementById("wano").value
            var wfecI = document.getElementById("wmesI").value;
            var wfecF = document.getElementById("wmesF").value;
            if(wfecI>wfecF)
            {
                alert("La fecha inicial debe ser inferior a la fecha final");
            }else
             {
                var tipodoc    = "ventas";
                var wemp_pmla  = document.getElementById("wemp_pmla").value;
                var wbd        = document.getElementById("wbasedatos").value;
                var parametros = "cajax=rep_sisdi&consultaAjax=&wdoc="+tipodoc+"&wmesI="+wfecI+"&wemp_pmla="+wemp_pmla+"&wmesF="+wfecF+"&wbasedatos="+wbd+"&wano="+wano;

                try
                {
                    try
                    {
                        $.blockUI({ message: $('#msjEspere') });
                    } catch(e){ }
                    var ajax = nuevoAjax();
                    ajax.open("POST", "rep_sisdi.php",true);
                    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    ajax.send(parametros);
                    $("#desarc").hide();
                    ajax.onreadystatechange=function()
                    {
                        if (ajax.readyState==4)
                        {
                            nombreArchivo=ajax.responseText;
                            var href="rep_sisdi.php?ajaxdes=sisdi&consultaAjax=&wdesc="+nombreArchivo+"&wemp_pmla="+wemp_pmla;
                            console.log("entra por aca "+ajax.readyState );
                            $("#warchi").attr("href", href);
                            console.log( $("#desarc") );
                            $("#desarc").show();
                        }try{
                                $.unblockUI();
                            } catch(e){ }
                    }
                }catch(e){  }
            }
        }
     </script>
</head>
<body>
<?php
    $wactualiz = "2018-11-29";
    encabezado("GENERACI&oacute;N DE INFORME SISDI",$wactualiz, "clinica");
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedatos = $institucion->baseDeDatos;
?>
<form name='rep_sisdi' action='rep_sisdi.php?wemp_pmla=".$wemp_pmla."' method=post>
    <center>
    <table width=500>
        <tr>
            <td class='encabezadotabla' colspan=3 align=center>ELIJA EL RANGO DE FECHAS QUE DESEA GENERAR</td>
        </tr>
        <tr>
            <?php $año=date("Y"); ?>
            <td class='fila2' algin=center width=250> AÑO:
                <select id='wano' name='wano'>
            <?php
                for($i=$año; $i>1999; $i--){
            ?>
                    <option value='<?=$i?>'><?=$i?></option>
            <?php
                }
            ?>
                </select>

            <td class='fila2' algin=center width=250> MES INICIAL:
                <select id='wmesI' name='wmesI'>
            <?php
                for($i=1; $i<13; $i++)
                {
            ?>
                  <option value='<?=$i?>'><?=$i?></option>
            <?php
                }
            ?>
            </select>
            </td>

            <td class='fila2' algin=center width=250> MES FINAL:
            <select id='wmesF' name='wmesF'>
            <?php
                for($i=1; $i<13; $i++)
                {
            ?>
                  <option value='<?=$i?>'><?=$i?></option>
            <?php
                }
            ?>
            </select>
            </td>
        </tr>
        <tr align=center>
            <td colspan=3><input align='center' type='button' value='ACEPTAR' onclick='descargar()'></td>
        </tr>
    </table>
    </center>
    <input type=hidden name='wemp_pmla' id='wemp_pmla' value='<?=$wemp_pmla?>'>
    <input type=hidden name='wbasedatos' id='wbasedatos' value='<?=$wbasedatos?>'>
    <div id='desarc' align='center' style='display:none;'>
        <a href="" name="warchi" id="warchi" TARGET="_new" onClick="quitarEnlace(this);" >DESCARGAR ARCHIVO SISDI</a>
    </div>
</form>
<br>
<center><table>
<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>
</table></center>
<div id='msjEspere' name='msjEspere' style='display:none;'>
<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />
</div>

</body>
</html>