<head>
  <title>LIQUIDACION DE TERCEROS POR RECAUDOS</title>

   <!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/fancyblue.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

<!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
  
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

function enter3()
{
	document.forma.submit();
}

fecini = new Date();

window.onload = function(){
	fecfin = new Date();
	
	alert( ( fecfin.getTime() - fecini.getTime() )/1000 );
}

</SCRIPT>

</head>

<?php
include_once("conex.php");

/**
* NOMBRE:  REPORTE DE lIQUIDACION DE TERCEROS POR RECAUDOS
* 
* PROGRAMA: liqter.php
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

$wautor = "Carolina Castano P.";
echo $wautor;
// =================================================================================================================================
session_start();
if (!isset($_SESSION['user']))
    echo "error";
else
{
	
//    $wbasedato = 'clisur';
    $key = substr($user, 2, strlen($user));

    /**
    * include de conexión a base de datos
    */
    

    


    echo "<form action='liqter.php' method=post name='forma'>";

    $wfecha = date("Y-m-d");
    $wano = date("Y");
    $wmes = date("m");

    echo "<input type='HIDDEN' NAME= 'wbasedato' value='" . $wbasedato . "'>";
    echo "<input type='HIDDEN' NAME= 'wbandera' value='0'>"; //indica si el programa debe cargar los datos de inicio o el la consulta 
    // de esta manera entro a la primera pagina, es decir, donde se piden los datos
    if (!isset($wfecini) or !isset($wfecfin) or !isset($wcon) or $wcon == '' or !isset ($resultado) or $bandera != 2)
    {
        echo "<center><table border=2>";
        echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=500 HEIGHT=100></td></tr>";
        echo "<tr><td class='titulo1'>LIQUIDACION DE TERCEROS POR RECAUDOS</td></tr>"; 
        // INGRESO DE VARIABLES PARA EL REPORTE//
        if (!isset ($bandera))
        {
            $wfecfin = $wfecha;
            $wfecini = $wano . '-' . $wmes . '-01';
        } 

        echo "<tr>";
        $cal = "calendario('wfecini','1')";
        echo "<td align=center class='texto3'><b>FECHA INICIAL: </font></b><INPUT TYPE='text' readonly='readonly' NAME='wfecini' value=" . $wfecini . " SIZE=10><input type='button' name='envio1' value='...' onclick=" . $cal . "' size=10 maxlength=10></td>";

        ?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecini',button:'envio1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
				//]]></script>
				<?php

        echo "<td  align=center class='texto3'><b>FECHA FINAL: </font></b><INPUT TYPE='text' readonly='readonly' NAME='wfecfin' value=" . $wfecfin . " SIZE=10><input type='button' name='envio2' value='...' onclick=" . $cal . "' size=10 maxlength=10></td>";

        ?>
				<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecfin',button:'envio2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
				//]]></script>
				<?php
        echo "</tr>";

        echo "<tr>"; 
        // SELECCION DE NIT O CEDULA DEL TERCERO
        if (isset($wter))
        {
            echo "<td align=center class='texto3'>NIT O CEDULA DEL TERCERO: <br><select name='wter' onchange='javascript:enter3()'>";

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
                     . "       order by 2";
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
            echo "<td align=center class='texto3' >CONCEPTO: ";
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
            echo "<td class='texto3'> NIT O CEDULA DEL TERCERO: <br><select name='wter' onchange='javascript:enter3()'>";

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
            echo "<td align=center class='texto3' >CONCEPTO: ";
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

        echo "<tr>";
        if (isset ($wreten))
        {
            echo "<td align=center class='texto3'>% RETENCION A EMPRESA: <INPUT TYPE='text' NAME='wreten' VALUE='" . $wreten . "' size='5'></td>  ";
        } 
        else
        {
            echo "<td align=center class='texto3'>RETENCION A EMPRESA: <INPUT TYPE='text' NAME='wreten' VALUE='0' size='5'></td>  ";
        } 
        echo "<td align=center class='texto3' >LIQUIDACION FACTURA A:  ";
        echo "<input type='radio' name='vol' value='1' onclick='Seleccionar()' >EMPRESAS&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<input type='radio' name='vol' value='2' onclick='Seleccionar()' >PARTICULARES&nbsp;&nbsp;";
        echo "<input type='radio' name='vol' value='3' onclick='Seleccionar()' checked>AMBOS&nbsp;&nbsp;";
        echo "</b></td></tr></table></br>";
        echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
    } 
    else 
        // MUESTRA DE DATOS DEL REPORTE
        {
            echo "<table  align=center width='60%'>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_" . $wbasedato . ".png' WIDTH=340 HEIGHT=100></td></tr>";
        echo "<tr><td>&nbsp;</td></tr>";
        echo "<tr><td><B>Fecha: " . date('Y-m-d') . "</B></td></tr>";
        switch ($vol)
        {
            case 1:
                echo "<tr><td><B>Liquidacion de facturado para empresa</B></td></tr>";
                break;

            case 2:
                echo "<tr><td><B>Liquidacion de facturado para particular</B></td></tr>";
                break;

            case 3:
                echo "<tr><td><B>Liquidacion de facturado para empresas y particulares</B></td></tr>";
                break;
        } 

        echo "</tr><td align=right ><A href='liqter.php?wbasedato=$wbasedato&wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wter=" . $wter . "&amp;wreten=" . $wreten . "&amp;bandera='1'>VOLVER</A></td></tr>";
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
        // busco todas las facturas canceladas en el rango de fechas y las meto en un vector el numero, la fuente
        // busco primero las facturas que su saldo es cero y se generaron en el rango de fechas
        switch ($vol)
        {
            case 1:
                $q = " SELECT  fenfac, fenffa, fdeter, fdecon, mednom  "
                 . "     FROM  " . $wbasedato . "_000018, " . $wbasedato . "_000065,  " . $wbasedato . "_000051   "
                 . "     WHERE   fenfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND fenest= 'on' "
                 . "     AND fensal=0 "
                 . "     AND fentip<>'01-PARTICULAR' "
                 . "     AND fdedoc=fenfac "
                 . "     AND fdefue=fenffa "
                 . "     AND fdeest= 'on' "
                 . "     AND fdeter like '" . $ter . "' "
                 . "     AND fdecon like '" . $con . "' "
                 . "     AND fdeter = meddoc "
                 . "     GROUP BY  4,3,1,2 "
                 . "     ORDER BY  5,4,1,2 ";
                break;

            case 2:
                $q = " SELECT  fenfac, fenffa, fdeter, fdecon, mednom  "
                 . "     FROM  " . $wbasedato . "_000018, " . $wbasedato . "_000065,  " . $wbasedato . "_000051   "
                 . "     WHERE   fenfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND fenest= 'on' "
                 . "     AND fensal=0 "
                 . "     AND fentip='01-PARTICULAR' "
                 . "     AND fdedoc=fenfac "
                 . "     AND fdefue=fenffa "
                 . "     AND fdeest= 'on' "
                 . "     AND fdeter like '" . $ter . "' "
                 . "     AND fdecon like '" . $con . "' "
                 . "     AND fdeter = meddoc "
                 . "     GROUP BY  4,3,1,2 "
                 . "     ORDER BY 5,4,1,2 ";
                break;

            case 3:
                $q = " SELECT  fenfac, fenffa, fdeter, fdecon, mednom  "
                 . "     FROM  " . $wbasedato . "_000018, " . $wbasedato . "_000065,  " . $wbasedato . "_000051   "
                 . "     WHERE   fenfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND fenest= 'on' "
                 . "     AND fensal=0 "
                 . "     AND fdedoc=fenfac "
                 . "     AND fdefue=fenffa "
                 . "     AND fdeest= 'on' "
                 . "     AND fdeter like '" . $ter . "' "
                 . "     AND fdecon like '" . $con . "' "
                 . "     AND fdeter = meddoc "
                 . "     GROUP BY  4,3,1,2 "
                 . "     ORDER BY  5,4,1,2";
                break;
        } 

        $err2 = mysql_query($q, $conex);
        $num3 = mysql_num_rows($err2);
        $j = 0; 
        // el resultado lo debo organizar en un vector
        for ($l = 0;$l < $num3;$l++)
        {
            $row2 = mysql_fetch_array($err2); 
            // me aseguro que fueron canceladas por abono cuando no tienen documento en la 21 con rdereg=0
            $q = " SELECT * "
             . "    FROM  " . $wbasedato . "_000021 "
             . "    WHERE  rdeest= 'on' "
             . "     AND rdefac= '" . $row2[0] . "' "
             . "     AND rdeffa= '" . $row2[1] . "' "
             . "     AND rdereg=0 ";

            $err4 = mysql_query($q, $conex);
            $num4 = mysql_num_rows($err4);

            if ($num4 <= 0)
            {
                $numfac1[$j] = $row2[0];
                $fuefac1[$j] = $row2[1];
                $terfac1[$j] = $row2[2];
                $confac1[$j] = $row2[3];
                $valfac1[$j] = 0;
                $nomfac1[$j] = $row2[4];
                $j++;
            } 
        } 
        // despues busco en la 21 las notas o recibos que dejaron el saldo en cero dentro de ese rango de fechas para guardar las facturas afectadas
        // esto se realiza dependiendo si no importa el tipo de empresa o si tiene que ser particular o no.
        switch ($vol)
        {
            case 1:
                $q = "  CREATE TEMPORARY TABLE if not exists caro1 as "
                 . " SELECT  b.rdefac as rdefac, b.rdeffa as rdeffa, b.id as ids, (fenval+fenviv+fencop+fencmo+fendes+fenabo) as suma, b.rdefue as rdefue, b.rdenum as rdenum "
                 . "    FROM  " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b, " . $wbasedato . "_000018 c   "
                 . "   	WHERE   renfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND renest= 'on' "
                 . "     AND rdefue= renfue "
                 . "     AND rdenum= rennum "
                 . "     AND rdecco= rencco "
                 . "     AND rdeest= 'on' "
                 . "     AND rdesfa=0 "
                 . "     AND b.rdesfa<>'' "
                 . "     AND fenfac= rdefac "
                 . "     AND fenffa= rdeffa "
                 . "     AND fentip<>'01-PARTICULAR' "
                 . "     AND rdereg=0 ";
                break;

            case 2:
                $q = "  CREATE TEMPORARY TABLE if not exists caro1 as "
                 . " SELECT  b.rdefac as rdefac, b.rdeffa as rdeffa, b.id as ids, (fenval+fenviv+fencop+fencmo+fendes+fenabo) as suma, b.rdefue as rdefue, b.rdenum as rdenum "
                 . "    FROM  " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b, " . $wbasedato . "_000018 c  "
                 . "   	WHERE   a.renfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND a.renest= 'on' "
                 . "     AND b.rdefue= a.renfue "
                 . "     AND b.rdenum= a.rennum "
                 . "     AND b.rdecco= a.rencco "
                 . "     AND b.rdeest= 'on' "
                 . "     AND b.rdesfa=0 "
                 . "     AND b.rdesfa<>'' "
                 . "     AND c.fenfac= b.rdefac "
                 . "     AND c.fenffa= b.rdeffa "
                 . "     AND c.fentip='01-PARTICULAR' "
                 . "     AND b.rdereg=0 ";
                break;

            case 3:
                $q = "  CREATE TEMPORARY TABLE if not exists caro1 as "
                 . " SELECT  b.rdefac as rdefac, b.rdeffa as rdeffa, b.id as ids, (fenval+fenviv+fencop+fencmo+fendes+fenabo) as suma, b.rdefue as rdefue, b.rdenum as rdenum "
                 . "    FROM  " . $wbasedato . "_000020 a, " . $wbasedato . "_000021 b, " . $wbasedato . "_000018 e "
                 . "   	WHERE   a.renfec  between  '" . $wfecini . "' and '" . $wfecfin . "' "
                 . "     AND a.renest= 'on' "
                 . "     AND b.rdefue= a.renfue "
                 . "     AND b.rdenum= a.rennum "
                 . "     AND b.rdecco= a.rencco "
                 . "     AND b.rdeest= 'on' "
                 . "     AND b.rdesfa=0 "
                 . "     AND b.rdesfa<>'' "
                 . "     AND b.rdereg=0 "
                 . "     AND e.fenfac= b.rdefac "
                 . "     AND e.fenffa= b.rdeffa ";
                break;
        } 

        $err = mysql_query($q, $conex) or die (mysql_errno() . " - " . mysql_error());

        $q = " SELECT  rdefac, rdeffa, ids, suma, fdeter, fdecon, mednom, rdefue, rdenum  "
         . "    FROM caro1, " . $wbasedato . "_000065,  " . $wbasedato . "_000051  "
         . "   	WHERE  fdedoc=rdefac "
         . "     AND fdefue=rdeffa "
         . "     AND fdeter like '" . $ter . "' "
         . "     AND fdecon like '" . $con . "' "
         . "     AND fdeest= 'on' "
         . "     AND fdeter = meddoc "
         . "     GROUP BY  6,5,3,1,2 "
         . "     ORDER BY  7,6,1,2";

        $err2 = mysql_query($q, $conex);
        $num3 = mysql_num_rows($err2);
        $j = 0; 
        // el resultado lo debo organizar en un vector pero que no queden facturas repetidas
        for ($l = 0;$l < $num3;$l++)
        {
            $row2 = mysql_fetch_array($err2); 
            // veo si hay otro documento superior a la fecha
            $q = " SELECT * "
             . "    FROM  " . $wbasedato . "_000021    "
             . "   	WHERE   id > '" . $row2[2] . "' "
             . "     AND rdeest= 'on' "
             . "     AND rdefac= '" . $row2[0] . "' "
             . "     AND rdeffa= '" . $row2[1] . "' "
             . "     AND rdesfa<> '' "
             . "     AND rdefue<> '" . $row2[7] . "' "
             . "     AND rdenum<> '" . $row2[8] . "' ";

            $err4 = mysql_query($q, $conex);
            $num4 = mysql_num_rows($err4);

            if ($num4 <= 0)
            { 
                // 2007-05-03  se mejora query de busqueda de notas por conceptos de facturacion
                if ($j == 0)
                {
                    $numfac[$j] = $row2[0];
                    $fuefac[$j] = $row2[1];
                    $terfac[$j] = $row2[4];
                    $confac[$j] = $row2[5];
                    $nomfac[$j] = $row2[6];
                    $q = " SELECT sum(renvca) "
                     . "  FROM " . $wbasedato . "_000021, " . $wbasedato . "_000020, " . $wbasedato . "_000040 "
                     . "WHERE rdeffa = '" . $fuefac[$j] . "' "
                     . "  AND rdefac = '" . $numfac[$j] . "' "
                     . "  and  rdeest='on' "
                     . "  and rdecon='' "
                     . "  and rdevco='0' "
                     . "  and renfue=rdefue "
                     . "  and rennum=rdenum "
                     . "  and renest='on' "
                     . "  and carfue=rdefue  "
                     . "  and carest='on' "
                     . "  and carncr='on' ";

                    $resval = mysql_query($q, $conex) ;
                    $rowval = mysql_fetch_row($resval);

                    $q = " SELECT sum(renvca) "
                     . "  FROM " . $wbasedato . "_000021, " . $wbasedato . "_000020, " . $wbasedato . "_000040 "
                     . "WHERE rdeffa = '" . $fuefac[$j] . "' "
                     . "  AND rdefac = '" . $numfac[$j] . "' "
                     . "  and  rdeest='on' "
                     . "  and rdecon='' "
                     . "  and rdevco='0' "
                     . "  and renfue=rdefue "
                     . "  and rennum=rdenum "
                     . "  and renest='on' "
                     . "  and carfue=rdefue  "
                     . "  and carest='on' "
                     . "  and carndb='on' ";
                    $resval1 = mysql_query($q, $conex) ;
                    $rowval1 = mysql_fetch_row($resval1);
                    $valfac[$j] = $row2[3] + $rowval1[0] - $rowval[0];
                    $j++;
                } 
                else if ( strtoupper( $numfac[$j-1] ) != strtoupper( $row2[0] ) or $fuefac[$j-1] != $row2[1] or $terfac[$j-1] != $row2[4] or $confac[$j-1] != $row2[5])
                {
                    $numfac[$j] = $row2[0];
                    $fuefac[$j] = $row2[1];
                    $terfac[$j] = $row2[4];
                    $confac[$j] = $row2[5];
                    $nomfac[$j] = $row2[6];

                    $q = " SELECT sum(renvca) "
                     . "  FROM " . $wbasedato . "_000021, " . $wbasedato . "_000020, " . $wbasedato . "_000040 "
                     . "WHERE rdeffa = '" . $fuefac[$j] . "' "
                     . "  AND rdefac = '" . $numfac[$j] . "' "
                     . "  and  rdeest='on' "
                     . "  and rdecon='' "
                     . "  and rdevco='0' "
                     . "  and renfue=rdefue "
                     . "  and rennum=rdenum "
                     . "  and renest='on' "
                     . "  and carfue=rdefue  "
                     . "  and carest='on' "
                     . "  and carncr='on' ";
                    $resval = mysql_query($q, $conex) ;
                    $rowval = mysql_fetch_row($resval);

                    $q = " SELECT sum(renvca) "
                     . "  FROM " . $wbasedato . "_000021, " . $wbasedato . "_000020, " . $wbasedato . "_000040 "
                     . "WHERE rdeffa = '" . $fuefac[$j] . "' "
                     . "  AND rdefac = '" . $numfac[$j] . "' "
                     . "  and  rdeest='on' "
                     . "  and rdecon='' "
                     . "  and rdevco='0' "
                     . "  and renfue=rdefue "
                     . "  and rennum=rdenum "
                     . "  and renest='on' "
                     . "  and carfue=rdefue  "
                     . "  and carest='on' "
                     . "  and carndb='on' ";
                    $resval1 = mysql_query($q, $conex) ;
                    $rowval1 = mysql_fetch_row($resval1);

                    $valfac[$j] = $row2[3] + $rowval1[0] - $rowval[0];
					
					$j++;
                }
				
				
            } 
        } 

        $clase1 = "class='texto1'";
        $clase2 = "class='texto4'";
        $pinfin = 0; //indica si debe mostrar totales cuando esta en uno
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

        if (!isset($numfac1))
        {
            $var1 = 0;
        } 
        else
        {
            $var1 = count($numfac1);
        } 

        if (!isset($numfac))
        {
            $var2 = 0;
        } 
        else
        {
            $var2 = count($numfac);
        } 
        // seordenan y juntan las facturas
        for ($k = 0;$k < $var1 + $var2;$k++) // se recorren las facturas
        {
            if (isset($numfac) and isset($numfac1) and $i < count($numfac) and $j < count($numfac1))
            {
                if (strnatcasecmp($nomfac[$i], $nomfac1[$j]) < 0)
                {
                    $numfac2[$k] = $numfac[$i];
                    $fuefac2[$k] = $fuefac[$i];
                    $terfac2[$k] = $terfac[$i];
                    $confac2[$k] = $confac[$i];
                    $nomfac2[$k] = $nomfac[$i];
                    $valfac2[$k] = $valfac[$i];
                    $i++;
                } 
                else if (strnatcasecmp($nomfac[$i], $nomfac1[$j]) > 0)
                {
                    $numfac2[$k] = $numfac1[$j];
                    $fuefac2[$k] = $fuefac1[$j];
                    $terfac2[$k] = $terfac1[$j];
                    $confac2[$k] = $confac1[$j];
                    $nomfac2[$k] = $nomfac1[$j];
                    $valfac2[$k] = $valfac1[$j];
                    $j++;
                } 
                else if (strnatcasecmp($nomfac[$i], $nomfac1[$j]) == 0)
                {
                    if ($confac[$i] < $confac1[$j])
                    {
                        $numfac2[$k] = $numfac[$i];
                        $fuefac2[$k] = $fuefac[$i];
                        $terfac2[$k] = $terfac[$i];
                        $confac2[$k] = $confac[$i];
                        $nomfac2[$k] = $nomfac[$i];
                        $valfac2[$k] = $valfac[$i];
                        $i++;
                    } 
                    else if ($confac[$i] > $confac1[$j])
                    {
                        $numfac2[$k] = $numfac1[$j];
                        $fuefac2[$k] = $fuefac1[$j];
                        $terfac2[$k] = $terfac1[$j];
                        $confac2[$k] = $confac1[$j];
                        $nomfac2[$k] = $nomfac1[$j];
                        $valfac2[$k] = $valfac1[$j];
                        $j++;
                    } 
                    else if ($confac[$i] == $confac1[$j])
                    {
                        if ($numfac[$i] < $numfac1[$j])
                        {
                            $numfac2[$k] = $numfac[$i];
                            $fuefac2[$k] = $fuefac[$i];
                            $terfac2[$k] = $terfac[$i];
                            $confac2[$k] = $confac[$i];
                            $nomfac2[$k] = $nomfac[$i];
                            $valfac2[$k] = $valfac[$i];
                            $i++;
                        } 
                        else if ($numfac[$i] > $numfac1[$j])
                        {
                            $numfac2[$k] = $numfac1[$j];
                            $fuefac2[$k] = $fuefac1[$j];
                            $terfac2[$k] = $terfac1[$j];
                            $confac2[$k] = $confac1[$j];
                            $nomfac2[$k] = $nomfac1[$j];
                            $valfac2[$k] = $valfac1[$j];
                            $j++;
                        } 
                    } 
                } 
            } 
            else if (!isset($numfac) or $i >= count($numfac))
            {
                $numfac2[$k] = $numfac1[$j];
                $fuefac2[$k] = $fuefac1[$j];
                $terfac2[$k] = $terfac1[$j];
                $confac2[$k] = $confac1[$j];
                $nomfac2[$k] = $nomfac1[$j];
                $valfac2[$k] = $valfac1[$j];
                $j++;
            } 
            else if (!isset($numfac1) or $j >= count($numfac1))
            {
                $numfac2[$k] = $numfac[$i];
                $fuefac2[$k] = $fuefac[$i];
                $terfac2[$k] = $terfac[$i];
                $confac2[$k] = $confac[$i];
                $nomfac2[$k] = $nomfac[$i];
                $valfac2[$k] = $valfac[$i];
                $i++;
            } 

            $q = " SELECT  fdevco, fdepte, fdevde, fdecco "
             . "    FROM  " . $wbasedato . "_000065 "
             . "   	WHERE   fdedoc= '" . $numfac2[$k] . "' "
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
                            echo "<th align=CENTER class='acumulado4' >" . number_format($cval, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($cdes, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($ccre, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado4' >&nbsp;</th>";
                            echo "<th align=CENTER class='acumulado4' >" . number_format($cpag, 0, '.', ',') . "</th><tr>"; 
                            // realizo la suma o acumulacion de todos los valores para el tercero
                            $tval = $tval + $cval;
                            $tdes = $tdes + $cdes;
                            $tdeb = $tdeb + $cdeb;
                            $tcre = $tcre + $ccre;
                            $tpag = $tpag + $cpag;

                            $pinter++;

                            echo "<tr><th align=CENTER class='acumulado2' colspan='5' >TOTAL TERCER0</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tval, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tdes, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tdeb, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tcre, 0, '.', ',') . "</th>";
                            echo "<th align=CENTER class='acumulado6' >&nbsp;</th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><tr>"; 
                            // realizo la suma o acumulacion de todos los valores finales
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
                            echo "<TR><th align=CENTER class='acumulado5' >- RETENCION EN LA FUENTE EMPRESA DEL " . $wreten . "%: </th>"; 
                            // calculo el procentaje de la empresa para desontar de retención en la fuente
                            $tcalculo = $talemp * $wreten / 100;
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tcalculo, 0, '.', ',') . "</th><TR>";
                            echo "<TR><th align=CENTER class='acumulado5' >= TOTAL A PAGAR: </th>";
                            echo "<th align=CENTER class='acumulado6' >" . number_format($tpag - $tcalculo, 0, '.', ',') . "</th><TR>";
                            echo "</table></br>";
                        } 
                        echo "<table  align=center width='1000'>";
                        echo "<tr><td class=titulo6 colspan=11><B>Tercero:</B> " . $terfac2[$k] . "-" . $nomfac2[$k] . "</td></tr>";
                        $conan = 0;
                        $teran = $terfac2[$k];
                        $pinfin = 1;
                        $pinter = 1;

                        $cval = 0;
                        $cdes = 0;
                        $cdeb = 0;
                        $ccre = 0;
                        $cpag = 0;
                        $pincon = 0;

                        $tval = 0;
                        $tdes = 0;
                        $tdeb = 0;
                        $tcre = 0;
                        $tpag = 0;
                        $talemp = 0;
                        $talpar = 0;
                    } 

                    if ($conan != $confac2[$k]) // se pone el titutlo de la tabla en caso de ser la primera factura que se encuentra
                        {
                            if ($pincon != 0)
                            {
                                echo "<tr><th align=CENTER class='acumulado3' colspan='5' >TOTAL CONCEPTO</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($cval, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($cdes, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($ccre, 0, '.', ',') . "</th>";
                                echo "<th align=CENTER class='acumulado4' >&nbsp;</th>";
                                echo "<th align=CENTER class='acumulado4' >" . number_format($cpag, 0, '.', ',') . "</th><tr>"; 
                                // realizo la suma o acumulacion de todos los valores para el tercero
                                $tval = $tval + $cval;
                                $tdes = $tdes + $cdes;
                                $tdeb = $tdeb + $cdeb;
                                $tcre = $tcre + $ccre;
                                $tpag = $tpag + $cpag;
                                $pincon = 1;
                                $pinter++;

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
                             . "     FROM " . $wbasedato . "_000004 "
                             . "    WHERE gruest= 'on' and grucod='" . $confac2[$k] . "' ";

                            $gru = mysql_query($q, $conex);
                            $tip = mysql_fetch_row($gru);

                            IF ($tip[0] == 'C')
                            {
                                echo "<tr><td class=titulo3 COLSPAN=11 ><B>Concepto:</B> " . $confac2[$k] . "-" . $tip[1] . "</td></tr>";
                                echo "<tr><td align=CENTER class='titulo2'><B>NUMERO FACTURA</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>EMPRESA DE LA FACTURA</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>PACIENTE</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>HISTORIA</B></td>";
                                echo "<td align=CENTER class='titulo2'><B>FECHA CARGO</B></td>";
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
                             . "   	WHERE  fenffa='" . $fuefac2[$k] . "' and fenfac='" . $numfac2[$k] . "' and fenest='on' "
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
                             . "   	WHERE  rcfffa='" . $fuefac2[$k] . "' and rcffac='" . $numfac2[$k] . "' and rcfest='on' "
                             . "     AND b.id=rcfreg ";

                            $err4 = mysql_query($q, $conex);
                            $row4 = mysql_fetch_array($err4);

                            echo "<td align=CENTER " . $clase1 . " width='10%'>" . $row4[0] . "</td>"; 
                            // ahora pitamos los datos propios del cargo (valor, descuento y porcentaje)
                            $row2 = mysql_fetch_array($err2);

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
                                $q = " SELECT sum(fdevco) FROM " . $wbasedato . "_000021, " . $wbasedato . "_000065 WHERE rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "' and rdefue='" . $rowdeb[0] . "' AND rdeest='on' and rdecon='' and rdevco='0' and fdefue= rdefue and fdedoc= rdenum and fdecon= '" . $confac2[$k] . "' and fdeter= '" . $terfac2[$k] . "' and fdeest='on' and fdecco= '" . $row2[3] . "' ";

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
                                $q = " SELECT sum(fdevco) FROM " . $wbasedato . "_000021, " . $wbasedato . "_000065 WHERE rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "' and rdefue='" . $rowcre[0] . "' AND rdeest='on' and rdecon='' and rdevco='0' and fdefue= rdefue and fdedoc= rdenum and fdecon= '" . $confac2[$k] . "' and fdeter= '" . $terfac2[$k] . "' and fdeest='on' and fdecco= '" . $row2[3] . "'";
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
                                $q = " SELECT sum(rdevco) FROM " . $wbasedato . "_000021, " . $wbasedato . "_000044  WHERE rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "' and rdefue='" . $deb[$d] . "' AND rdeest='on' and rdecon<>'' and rdevco<>'0' and concod=  (mid(rdecon,1,instr(rdecon,'-')-1)) and conest= 'on' and conacp='on' ";
                                $resdeb = mysql_query($q, $conex);
                                $row2deb = mysql_fetch_row($resdeb); //para sumar el total de nota debito
                                $wvaldeb = round($wvaldeb + $row2deb[0] * $porcen / 100);
                            } 
                            // ahora vamos a consultar las notas credito por conceptos de cartera y decidir si se descuentan o no
                            for ($d = 1;$d <= count($cre);$d++) // para cada fuente busco notas
                            {
                                $q = " SELECT sum(rdevco) FROM " . $wbasedato . "_000021, " . $wbasedato . "_000044  WHERE rdeffa = '" . $fuefac2[$k] . "' AND rdefac = '" . $numfac2[$k] . "' and rdefue='" . $cre[$d] . "' AND rdeest='on' and rdecon<>'' and rdevco<>'0' and concod=  (mid(rdecon,1,instr(rdecon,'-')-1)) and conest= 'on' and conacp='on' ";
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
                echo "<th align=CENTER class='acumulado4' >" . number_format($cval, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($cdes, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($cdeb, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($ccre, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado4' >&nbsp;</th>";
                echo "<th align=CENTER class='acumulado4' >" . number_format($cpag, 0, '.', ',') . "</th><tr>"; 
                // realizo la suma o acumulacion de todos los valores para el tercero
                $tval = $tval + $cval;
                $tdes = $tdes + $cdes;
                $tdeb = $tdeb + $cdeb;
                $tcre = $tcre + $ccre;
                $tpag = $tpag + $cpag;
                $pincon = 0;
                $pinter++;

                echo "<tr><th align=CENTER class='acumulado2' colspan='5' >TOTAL TERCER0</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tval, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tdes, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tdeb, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tcre, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado6' >&nbsp;</th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tpag, 0, '.', ',') . "</th><tr>"; 
                // realizo la suma o acumulacion de todos los valores finales
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
                echo "<TR><th align=CENTER class='acumulado5' >- RETENCION EN LA FUENTE EMPRESA DEL " . $wreten . "%: </th>"; 
                // calculo el procentaje de la empresa para desontar de retención en la fuente
                $tcalculo = $talemp * $wreten / 100;
                echo "<th align=CENTER class='acumulado6' >" . number_format($tcalculo, 0, '.', ',') . "</th><TR>";
                echo "<TR><th align=CENTER class='acumulado5' >= TOTAL A PAGAR: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($tpag - $tcalculo, 0, '.', ',') . "</th><TR>";
                echo "</table></br>";

                echo "<table align='center' width='1000' >";
                echo "<tr><th align=CENTER class='acumulado1' colspan='5' >TOTALES</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fval, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fdes, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fdeb, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fcre, 0, '.', ',') . "</th>";
                echo "<th align=CENTER class='acumulado1' >&nbsp;</th>";
                echo "<th align=CENTER class='acumulado1' >" . number_format($fpag, 0, '.', ',') . "</th><tr>";
                echo "</table></br>";

                echo "<table align='center' >";
                echo "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A EMPRESAS: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($valemp, 0, '.', ',') . "</th>";
                echo "<TR><th align=CENTER class='acumulado5' >VALOR FACTURADO A PARTICULARES: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($valpar, 0, '.', ',') . "</th><TR>";
                echo "<TR><th align=CENTER class='acumulado5' >=VALOR FACTURADO TOTAL: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($fpag, 0, '.', ',') . "</th><TR>";
                echo "<TR><th align=CENTER class='acumulado5' >- RETENCION EN LA FUENTE EMPRESA DEL " . $wreten . "%: </th>"; 
                // calculo el procentaje de la empresa para desontar de retención en la fuente
                $calculo = $valemp * $wreten / 100;
                echo "<th align=CENTER class='acumulado6' >" . number_format($calculo, 0, '.', ',') . "</th><TR>";
                echo "<TR><th align=CENTER class='acumulado5' >= TOTAL A PAGAR: </th>";
                echo "<th align=CENTER class='acumulado6' >" . number_format($fpag - $calculo, 0, '.', ',') . "</th><TR>";
                echo "</table></br>";
            } 
            else
            {
                echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
                echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningun documento que coincida con los paremtros seleccionados</td></tr>";
                echo "</table></br>";
            } 

            echo "<center><A href='liqter.php?wbasedato=$wbasedato&wfecini=" . $wfecini . "&amp;wfecfin=" . $wfecfin . "&amp;wter=" . $wter . "&amp;wreten=" . $wreten . "&amp;bandera='1'>VOLVER</A></center>";
            echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
        } 
    } 

    ?>
</body>
</html>