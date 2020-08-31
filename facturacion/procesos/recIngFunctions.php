<?php
/////////////////FUNCIONES////////////////
function basico($numero)
{
    $valor = array ('uno','dos','tres','cuatro','cinco','seis','siete','ocho','nueve','diez','once','doce',
        'trece','catorce','quince','dieciseis','diecisiete','dieciocho','diecinueve','veinte',
        'veintinuo','veintidos','veintitres','veinticuatro','veinticinco','veintiséis','veintisiete',
        'veintiocho','veintinueve');
    return $valor[$numero - 1];
}

function decenas($n)
{
    $decenas = array (30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',70=>'setenta',80=>'ochenta',90=>'noventa');
    if( $n <= 29) return basico($n);
    $x = $n % 10;
    if ( $x == 0 )
    {
        return $decenas[$n];
    } else return $decenas[$n - $x].' y '. basico($x);
}

function centenas($n) {
    $cientos = array (100 =>'cien',200 =>'doscientos',300=>'trecientos',400=>'cuatrocientos', 500=>'quinientos',600=>'seiscientos',
        700=>'setecientos',800=>'ochocientos', 900 =>'novecientos');
    if( $n >= 100) {
        if ( $n % 100 == 0 ) {
            return $cientos[$n];
        } else {
            $u = (int) substr($n,0,1);
            $d = (int) substr($n,1,2);
            return (($u == 1)?'ciento':$cientos[$u*100]).' '.decenas($d);
        }
    } else return decenas($n);
}

function miles($n) {
    if($n > 999) {
        if( $n == 1000) {return 'mil';}
        else {
            $l = strlen($n);
            $c = (int)substr($n,0,$l-3);
            $x = (int)substr($n,-3);
            if($c == 1) {$cadena = 'mil '.centenas($x);}
            else if($x != 0) {$cadena = centenas($c).' mil '.centenas($x);}
            else $cadena = centenas($c). ' mil';
            return $cadena;
        }
    } else return centenas($n);
}

function millones($n) {
    if($n == 1000000) {return 'un millón';}
    else {
        $l = strlen($n);
        $c = (int)substr($n,0,$l-6);
        $x = (int)substr($n,-6);
        if($c == 1) {
            $cadena = ' millón ';
        } else {
            $cadena = ' millones ';
        }
        return miles($c).$cadena.(($x > 0)?miles($x):'');
    }
}

function convertir($n) {
    switch (true) {
        case ( $n >= 1 && $n <= 29) : return basico($n); break;
        case ( $n >= 30 && $n < 100) : return decenas($n); break;
        case ( $n >= 100 && $n < 1000) : return centenas($n); break;
        case ($n >= 1000 && $n <= 999999): return miles($n); break;
        case ($n >= 1000000): return millones($n);
    }
}

function mostrarData($fte,$fac,$conex,$conex_o,$offset)
{
    $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT 50 OFFSET $offset";
    $dato_3 = mysql_query($query_3, $conex) or die (mysql_errno()." - en el query: ".$query_3." - ".mysql_error());
    ?>
    <table class="tblDetalle" border="0" align="left">
        <thead>
        <tr align="center">
            <td style="background-color: #C3D9FF"><label>&ensp;FACTURA</label></td>
            <td style="background-color: #C3D9FF"><label>&ensp;VALOR</label></td>
        </tr>
        </thead>
        <?php
        while($datoEq211 = mysql_fetch_assoc($dato_3))
        {
            $numFac1 = $datoEq211['carfac'];  $valFac1 = $datoEq211['carval']; $valFac1 = number_format($valFac1,0);    $carcco = $datoEq211['carcco'];
            $carfca = $datoEq211['carfca'];

            $query_5 = "select carfue,carcco from cacar WHERE carfue = '$carfca' AND cardoc = '$numFac1'";
            $dato_5 = odbc_do($conex_o,$query_5);
            $carfue3 = odbc_result($dato_5,1);  $carcco3 = odbc_result($dato_5,2);

            $query_6 = "select fuecse from cafue WHERE fuecod = '$carfue3' AND fuecco = '$carcco3'";
            $dato_6 = odbc_do($conex_o,$query_6);
            $fuesec = odbc_result($dato_6,1);
            ?>
            <tbody>
            <tr>
                <td class="tdDetalle" width="40" align="left"><label class="lblNormal" id="tipoPago"><?php echo $fuesec.$numFac1 ?></label></td>
                <td class="tdDetalle" width="60" style="text-align: right"><label class="lblNormal" id="banco"><?php echo $valFac1 ?></label>&ensp;</td>
            </tr>
            </tbody>
            <?php
        }
        ?>
    </table>
    <?php
}

function mostrarPie($fte,$fac,$conex_o)
{
    ?>
    <table style="max-height: 10px; margin-top: 1px" border="1">
        <tr align="center">
            <td colspan="8">FORMA DE PAGO:</td>
        </tr>
        <tr style="background-color: #EEEEEE; border-bottom: inset">
            <td>F-RE</td>
            <td>BANCO</td>
            <td>PLA</td>
            <td>POB</td>
            <td>NUM-DOC</td>
            <td>CUE-BANC</td>
            <td>VALOR</td>
            <td>BCO-CON</td>
        </tr>
        <?php
        $query_4 = "SELECT * FROM cbmovdet WHERE movdetfue = '$fte' AND movdetdoc = '$fac'";
        $dato_4 = odbc_do($conex_o,$query_4);

        while(odbc_fetch_row($dato_4))
        {
            $fre = odbc_result($dato_4,'movdetfpa');    $banco = odbc_result($dato_4,'movdetban');  $pla = odbc_result($dato_4,'movdetpla');
            $pob = odbc_result($dato_4,'movdetpob');    $numdoc = odbc_result($dato_4,'movdetdpa'); $cuebanc = odbc_result($dato_4,'movdetcue');
            $valor = odbc_result($dato_4,'movdetval');  $bcocon = odbc_result($dato_4,'movdetbco'); $valor2 = $valor;

            ?>
            <tr>
                <td><label class="lblNormal"><?php echo $fre ?></label></td>
                <td><label class="lblNormal"><?php echo $banco ?></label></td>
                <td><label class="lblNormal"><?php echo $pla ?></label></td>
                <td><label class="lblNormal"><?php echo $pob ?></label></td>
                <td><label class="lblNormal"><?php echo $numdoc ?></label></td>
                <td><label class="lblNormal"><?php echo $cuebanc ?></label></td>
                <td><label class="lblNormal"><?php echo number_format($valor2,2) ?></label></td>
                <td><label class="lblNormal"><?php echo $bcocon ?></label></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}

function detalleConceptos($fte,$fac,$conex_o,$parametro)
{
    $qryConConcep = "select count(*) from cacar WHERE carfue = '$fte' AND cardoc = '$fac' AND caranu = '0' AND carval <= '0'";
    $datoConConcep = odbc_do($conex_o, $qryConConcep);
    $cantConNeg = odbc_result($datoConConcep,1);

    if($cantConNeg > 0)
    {
        $qryConCargos = "SELECT count(*) FROM cacar WHERE carfue = '$fte' AND cardoc = '$fac' AND caranu = '0' AND carval > 0";
        $datoConCargos = odbc_do($conex_o,$qryConCargos);
        $contCargos = odbc_result($datoConCargos,1);

        if($parametro >= $contCargos)
        {
            $query_1 = "SELECT * FROM cacar WHERE carfue = '$fte' AND cardoc = '$fac' AND caranu = '0' AND carval <= 0";
            $datoQuery_1 = odbc_do($conex_o,$query_1);
            ?>
            <table style="min-height: 10px" border="1">
                <tr align="center">
                    <td colspan="3">MENOS DETALLE DE CONCEPTOS:</td>
                </tr>
                <?php
                while(odbc_fetch_row($datoQuery_1))
                {
                    $carConc = odbc_result($datoQuery_1,'carcon');  $carvlc = odbc_result($datoQuery_1,'carvlc');
                    $carvlc2 = $carvlc;

                    $query_2 = "select cocnom from cococ where coccod = '$carConc'";
                    $datoQuery_2 = odbc_do($conex_o,$query_2);
                    $nomConcep = odbc_result($datoQuery_2,1);

                    ?>
                    <tr>
                        <td><?php echo $carConc ?></td>
                        <td><?php echo $nomConcep ?></td>
                        <td><?php echo number_format($carvlc2,2) ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }
    }
}

function mostrarData2($fte,$fac,$conex,$conex_o,$offset,$div)
{
    //offset -> a partir de cual registro se muestra
    //LIMIT -> cuantos registros por columna

    //CONTAR CUANTOS CARGOS CON VALOR NEGATIVO O CERO TIENE EL DOCUMENTO.
    //PARA DETERMINAR CUANTOS REGISTROS SE VAN A MOSTRAR POR HOJA, CON EL FIN
    //DE QUE EL CONTENIDO OCUPE EXACTAMENTE UNA HOJA TAMAÑO CARTA:
    $qryConConcep = "select count(*) from cacar WHERE carfue = '$fte' AND cardoc = '$fac' AND caranu = '0' AND carval <= 0";
    $datoConConcep = odbc_do($conex_o, $qryConConcep);
    $cantConcNeg = odbc_result($datoConConcep,1);

    //OBTENER EL TOTAL DE CARGOS POSITIVOS:
    $qryConCargos = "SELECT count(*) FROM cacar WHERE carfue = '$fte' AND cardoc = '$fac' AND caranu = '0' AND carval > 0";
    $datoConCargos = odbc_do($conex_o,$qryConCargos);
    $totalConc = odbc_result($datoConCargos,1);
    ?>
    <input type="hidden" id="regNeg" name="regNeg" value="<?php echo $cantConcNeg ?>">
    <script>regNeg = document.getElementById('regNeg').value; regNeg = regNeg * 10;</script> <!-- 1 registro = 5 pixels aproximadamente -->
    <?php

    //TIENE CONCEPTOS NEGATIVOS:
    if($cantConcNeg > 0)
    {
        //SI EXISTEN REGISTROS NEGATIVOS, RESTAR ESTA CANTIDAD A LA CANTIDAD DE REGISTROS POR PAGINA,
        //50 SON LOS REGISTROS POR DEFECTO X COLUMNA :
        $regxCol = (50 - ($cantConcNeg + 2)); // + 1 LINEA DE TITULO

        //DESDE DÓNDE SE VA A MOSTRAR EN LA ULTIMA COLUMNA + REGISTROS POR COLUMNA
        $van = $regxCol * 4;
        $van = $van + $regxCol;

        //SI ES LA ULTIMA HOJA:
        if($van > $totalConc and $div == 1)
        {
            $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT $regxCol OFFSET $offset";

            ?>
            <script>
                sizePanel = document.getElementById('divDatosEspecificos1').style.height;
                sizeFinal = (parseInt(sizePanel) - (parseInt(regNeg)));
                document.getElementById('divDatosEspecificos1').style.minHeight = sizeFinal+'px';
            </script>
            <?php
        }
        if($van > $totalConc and $div == 2)
        {
            $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT $regxCol OFFSET $offset";

            ?>
            <script>
                sizePanel = document.getElementById('divDatosEspecificos2').style.height;
                //alert('SIZE PANEL 2 =');
                sizeFinal = (parseInt(sizePanel) - (parseInt(regNeg)));
                document.getElementById('divDatosEspecificos2').style.minHeight = sizeFinal+'px';
            </script>
            <?php
        }
        if($van > $totalConc and $div == 3)
        {
            $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT $regxCol OFFSET $offset";

            ?>
            <script>
                sizePanel = document.getElementById('divDatosEspecificos3').style.height;
                sizeFinal = (parseInt(sizePanel) - (parseInt(regNeg)));
                document.getElementById('divDatosEspecificos3').style.minHeight = sizeFinal+'px';
            </script>
            <?php
        }
        if($van > $totalConc and $div == 4)
        {
            $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT $regxCol OFFSET $offset";

            ?>
            <script>
                sizePanel = document.getElementById('divDatosEspecificos4').style.height;
                sizeFinal = (parseInt(sizePanel) - (parseInt(regNeg)));
                document.getElementById('divDatosEspecificos4').style.minHeight = sizeFinal+'px';
            </script>
            <?php
        }
        //SI NO ES LA ULTIMA:
        if($van < $totalConc and $div == 1)
        {
            $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT 50 OFFSET $offset";
        }
        if($van < $totalConc and $div == 2)
        {
            $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT 50 OFFSET $offset";
        }
        if($van < $totalConc and $div == 3)
        {
            $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT 50 OFFSET $offset";
        }
        if($van < $totalConc and $div == 4)
        {
            $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT 50 OFFSET $offset";
        }

    }
    //NO TIENE CONCEPTOS NEGATIVOS
    else
    {
        $query_3 = "SELECT * FROM equipos_000021 WHERE carfue = '$fte' AND cardoc = '$fac' LIMIT 50 OFFSET $offset";
    }

    $dato_3 = mysql_query($query_3, $conex) or die (mysql_errno()." - en el query: ".$query_3." - ".mysql_error());
    ?>
    <table class="tblDetalle" border="0" align="left">
        <thead>
        <tr align="center">
            <td style="background-color: #C3D9FF"><label>&ensp;FACTURA</label></td>
            <td style="background-color: #C3D9FF"><label>&ensp;VALOR</label></td>
        </tr>
        </thead>
        <?php
        while($datoEq211 = mysql_fetch_assoc($dato_3))
        {
            $numFac1 = $datoEq211['carfac'];  $valFac1 = $datoEq211['carval']; $valFac1 = number_format($valFac1,0);
            $carfca = $datoEq211['carfca'];

            $query_5 = "select carfue,carcco from cacar WHERE carfue = '$carfca' AND cardoc = '$numFac1'";
            $dato_5 = odbc_do($conex_o,$query_5);
            $carfue3 = odbc_result($dato_5,1);  $carcco3 = odbc_result($dato_5,2);

            $query_6 = "select fuecse from cafue WHERE fuecod = '$carfue3' AND fuecco = '$carcco3'";
            $dato_6 = odbc_do($conex_o,$query_6);
            $fuesec = odbc_result($dato_6,1);
            ?>
            <tbody>
            <tr>
                <td class="tdDetalle" width="40" align="left"><label class="lblNormal" id="tipoPago"><?php echo $fuesec.$numFac1 ?></label></td>
                <td class="tdDetalle" width="60" style="text-align: right"><label class="lblNormal" id="banco"><?php echo $valFac1 ?></label>&ensp;</td>
            </tr>
            </tbody>
            <?php
        }
        ?>
    </table>
    <?php
}
?>