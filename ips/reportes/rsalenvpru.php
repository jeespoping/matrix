<html>

<head>
<title>REPORTE DE SALDOS DE CARTERA</title>

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>

<style type="text/css">

body {
    background: white url(portal.gif) transparent center no-repeat scroll;
}

.titulo1 {
    color: #FFFFFF;
    background: #006699;
    font-size: 20pt;
    font-family: Arial;
    font-weight: bold;
    text-align: center;
}

.titulo2 {
    color: #003366;
    background: #A4E1E8;
    font-size: 12pt;
    font-family: Arial;
    font-weight: bold;
    text-align: center;
}

.titulo3 {
    color: #003366;
    background: #57C8D5;
    font-size: 8pt;
    font-family: Tahoma;
    font-weight: bold;
    text-align: center;
}

.titulo4 {
    color: #003366;
    font-size: 12pt;
    font-family: Arial;
    font-weight: bold;
    text-align: left;
}

.texto1 {
    color: #006699;
    background: #FFFFFF;
    font-size: 8pt;
    font-family: Tahoma;
    text-align: left;
}

.texto2 {
    color: #006699;
    background: #f5f5dc;
    font-size: 8pt;
    font-family: Tahoma;
    text-align: left;
}

.texto3 {
    color: #006699;
    background: #A4E1E8;
    font-size: 9pt;
    font-family: Tahoma;
    text-align: center;
}

.texto4 {
    color: #006699;
    background: #FFFFFF;
    font-size: 8pt;
    font-family: Tahoma;
    text-align: right;
}

.texto5 {
    color: #006699;
    background: #f5f5dc;
    font-size: 8pt;
    font-family: Tahoma;
    text-align: right;
}

.acumulado1 {
    color: #003366;
    background: #FFCC66;
    font-size: 9pt;
    font-family: Tahoma;
    font-weight: bold;
    text-align: right;
}

.acumulado2 {
    color: #003366;
    background: #FFCC66;
    font-size: 9pt;
    font-family: Tahoma;
    font-weight: bold;
    text-align: center;
}

.acumulado3 {
    color: #003366;
    background: #FFDBA8;
    font-size: 9pt;
    font-family: Tahoma;
    font-weight: bold;
    text-align: center;
}

.error1 {
    color: #FF0000;
    font-size: 10pt;
    font-family: Tahoma;
    font-weight: bold;
    text-align: center;
}
</style>

<script type="text/javascript">

function Seleccionar()
{
    $.blockUI({ message: $('#msjEspere') });
    document.forma.submit();
}

function check( control ){

    if ( control.checked ){
        this.value = "on";
    }
    else{
        this.value = "off";
    }
}
</SCRIPT>

</head>

<body>

<?php
include_once("conex.php");
//ini_set("memory_limit","130M");
/**
 * NOMBRE:  REPORTE DE CARTERA
 *
 * PROGRAMA: rsalenvpru.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION:Este reporte presenta la lista de facturas con sus notas credito, debito, estado y carta de cobro entre dos fechas, para una o todas las empresas
 *
 * HISTORIAL DE ACTUALIZACIONES:
 * 2015-11-04 Camilo ZZ se quemó que cuando el wemp_pmla =='09' farmastore, el valor de la factura no suma el valor del iva.
 * 2013-05-28 Edwar. Se diseñó un proceso complementario donde se creó una copia de la función "consultarGlosas" llamada "consultarGlosasOptima", y una copia de la función "consultarFacturas"
                        que se llama "consultarFacturasOptima", estas dos funciones ayudan a optimizar el uso de recursos a base de datos, en la sección principal del programa
                        se agregó un condicional con la variable "$no_optimizar" y lo que hace es determinar si la consulta del reporte se hace con las funciones originalmente creadas
                        antes de crear la otras funciones optimizadas, o si por el contrario se realiza el reporte con esas funciones optimizadas, por defecto el reporte siempre se hará
                        con las consultas optimizadas, en el formulario del reporte aparece un checkbox "Sin optimizar consultas" que si al estar chequeado, se hace la consulta con base a
                        las funciones no optimizadas (esto tardará más tiempo en ejecutarse el reporte pero puede usarse para comparar este resultado que se garantiza que es correcto
                        y se puede comparar con el resultado optimizado, con el fin de constatar que no hay diferencias), esta opción o checkbox solo está activo para el codigo de usuario "03150"
                        y para nadie más.
 * 2013-03-06 Frederick. Se agregan dos columnas que faltaban en el archivo plano, y se muestra el mensaje de cargando
 * 2012-09-11 Camilo Zapata. Se agregó la condición de saldo de factura =! de cero en el query principal
 *                           se cambio el valor de la factura, ahora es fenval + fenviv(valor del iva).
 * 2012-08-24 Mario Cadavid. En el query principal se modificó el JOIN de las tablas 000018 y 000024 para que se haga por código de responsable
 *            y no por código de empresa, esto para que se consulten todas las facturas de las empresas incluyendo las de sus empleados
 * 2012-08-24 Mario Cadavid. Se agregó la función cambiarCadena para que los caracteres especiales se muestren correctamente en el reporte
 * 2012-07-05 Camilo Zapata. Se agregaron la linea que modifica el tamaño de la memoria permitido a usar por parte del script linea 146.
 * 2012-07-05 Camilo Zapata. Se agregaron las columnas No. radicacion y fecha de radicación, para que el reporte muestre los datos de la radicación aunque no esté en dicho estado.
 * 2012-07-05 Camilo Zapata. Se adicionaron una serie de tablas temporales para mejorar el rendimiento de las consultas, ademas se corrigieron las causas, que en principio
              se pensó, se referian a las observaciones de la 20, sin embargo esto es un error, ya que las justificaciones están en las tablas 71 y 72.
 * 2012-07-04 Camilo Zapata. Se adicionaron las columnas NO. NOTA CREDITO Y NO. NOTA DEBITOS, las cuales presentan las notas correspondientes a los totales presentados
              ademas de sus respectivas justificaciones.
 * 2011-11-01 Mario Cadavid. Se adicionaron las columnas NO. DOCUMENTO Y FECHA DOCUMENTO de modo que se puede ver el número
 *            y fecha del estado actual de la factura
 * 2009-06-08 Edwin Molina. Se Anexa los Campo correspondientes a glosa (Fecha, Numero de glosa, Glosa, Valor aceptado,
 *            Recobrar) y se cambia el aspecto de la aplicaciòn.
 * 2006-10-12 carolina castano, creacion del script
 *
 * Tablas que utiliza:
 * $wbasedato."_000018: select en tabla de facturas
 * $wbasedato."_000020: select en tabla de encabezado de cartera
 * $wbasedato."_000021: select en tabla de detalle de cartera
 * $wbasedato."_000024: select en tabla de empresas
 * $wbasedato."_000040: select en tabla de >
 * $wbasedato."_000101: select en tabla de ingresos
 *
 * VARIABLES:
 * wnumSol : Integer -- Numero de la solicitud
 * wnomSol : String -- nombre del Solicitud
 * wfreSol : Date -- Fecha de la Solicitud
 * wuniSol : Real -- unidad solicitante
 * wperSol : String -- nombre de la persona solicitante
 * wdesSol : String -- descripción de la solicitud
 * wgrupSol : String -- grupo al que pertenece la solicitud
 * wtipSol : String -- tipo de solicitud
 * wtamsol : String -- tamaño de la solicitud
 * wpriSol : String -- pioridad de la solicitud
 * wanaSol : String -- Analista líder de la solicitud
 * winiSol : Date -- Fecha de incio estimada de la solicitud
 * wentSol : Date -- entrega estimada de la solicitud
 * wnomEta : String -- Nombre de la Etapa
 * weinEta : Date -- incio estimado de la etapa
 * wefiEta : Date -- fecha estimada de terminación de la etapa
 * wanaEta : String -- analista responsable de la etapa
 * wrinEta : Date -- incio real de la etapa
 * wrteEta : Real -- terminacion real de la etapa
 * wcumEta : String -- porcentaje de cumplimiento de la etapa
 * wcodEta : Integer -- codigo de la etapa
 * wnumSol : 000031 infsol -- Numero de la solicitud
 * accion  : lleva el hilo de ejecución de la aplicación
 *
 * @author ccastano
 * @package defaultPackage
 *
 */

echo "<div id='msjEspere' style='display:none;'>";
echo '<br>';
echo "<img src='../../images/medical/ajax-loader5.gif'/>";
echo "<br><br> Por favor espere un momento ... <br><br>";
echo '</div>';

echo "<script>";
echo "$.blockUI({ message: $('#msjEspere') });";
echo "</script>";

//=================================================================================================================================
//funciones de persistencia


function consultarPoliza( $his, $ing ){

    global $wbasedato;
    global $conex;

    if( !empty( $his ) && !empty( $ing ) ){

        $sql = "SELECT
                    Ingpol
                FROM
                    {$wbasedato}_000101
                WHERE
                    inghis = '$his'
                    AND ingnin = '$ing'
                ";

        $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

        if( $rows = mysql_fetch_array( $res ) ){
            return $rows['Ingpol'];
        }
        else{
            return "";
        }
    }
    else{
        return "";
    }
}

function cambiarCadena ($message) {
$search = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ã¡,Ã©,Ã­,Ã³,Ãº,Ã±,ÃÃ¡,ÃÃ©,ÃÃ­,ÃÃ³,ÃÃº,ÃÃ±,Ã?Â?,ÃƒÂƒÃ,Ã,ÂƒƒÂ,Â±");
 $replace = explode(",","á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,á,é,í,ó,ú,ñ,Á,É,Í,Ó,Ú,Ñ,Ñ,,,,Ñ");
 $message= str_replace($search, $replace, $message);
 $message= str_replace("‚", "", $message);
 return $message;
}

/**
 * Consulta el estado de la factura
 *
 * @param unknown_type $fac: Numero de factura
 * @param unknown_type $ffa: factura
 * @param unknown_type $esf: Estado factura (siglas)
 * @return vector
 *
 * Devuelve un vector con los datos del estado de la factura (No. del documento y Fecha del Documento)
 *
 * Valores de retorno
 *
 * Array $resest: Contiene un array de retorno con todos los valores correspondiente a la glosa
 *
 * estfec       Fecha en que se generé el estado
 * estnum       Número del documento
 */

function consultarEstado( $fac, $ffa, $esf )
{

    global $wbasedato;
    global $conex;

    $estfec = "";
    $estnum = "";
    $resest = array();  //Array a devolver con los datos del estado de la factura

    //Consulto el estado de la factura
    $sql = "SELECT  a.Fecha_data, Rdefue, Rdenum "
    . "     FROM  {$wbasedato}_000021 a, {$wbasedato}_000040 "
    . "     WHERE rdefac= '" . $fac . "' "
    . "       AND rdeffa= '" . $ffa . "' "
    . "       AND rdeest = 'on' "
    . "       AND rdefue = carfue "
    . "       AND caresf= '" . $esf . "' "
    . "     group by rdenum, rdefue  ";

    $result = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta - ".mysql_error() );
    $rows = mysql_fetch_array($result);

    $resest['estfec'] = $rows['Fecha_data'];
    $resest['estnum'] = $rows['Rdefue']."-".$rows['Rdenum'];

    return $resest;
}


/**
 * Consulta la glosa para una factura
 *
 * @param unknown_type $fac: Numero de factura
 * @param unknown_type $ffa: factura
 * @return vector
 *
 * Devuelve un vector con los datos de la glosa (No. de Glosa, Fecha de la Glosa,
 * Valor de la glosa, valor aceptado de la glosa y recobrar de la glosa
 *
 * Valores de retorno
 *
 * Array $resglo: Contiene un array de retorno con todos los valores correspondiente a la glosa
 *
 * glofec       Fecha en que se genero la glosa
 * glonog       Número de la glosa
 * gloval       Glosa
 * gloace       Valor aceptado de la glosa
 * glorec       Recobrar de la glosa
 */

function consultarGlosas( $fac, $ffa )
{

    global $wbasedato;
    global $conex;

    $glofec = "";
    $glonog = "";
    $glosa = 0;
    $aceptado = 0;
    $resglo = array();  //Array a devolver con los datos de la glos
    $glorec = 0;        //Valor a recobrar, diferencia entre la glosa y el aceptado

    //Hallando la glosa y el total aceptado
    //Consulta 15
    $sql = "SELECT  rdevca, carfue, carglo, carncr, carndb, rdenum, rdefac, carrec, rdereg, carabo, carcfa, carcca, rdevco, rdeglo, a.fecha_data "
    . "     FROM  {$wbasedato}_000021 a, {$wbasedato}_000040 "
    . "     WHERE rdefac= '" . $fac . "' "
    . "       AND rdeffa= '" . $ffa . "' "
    . "       AND rdeest = 'on' "
    . "       AND rdefue = carfue "
    . "       AND carglo = 'on'  "
    . "     group by rdenum, rdefue  ";

    $result = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0015 - ".mysql_error() );

    $rows = array();
    for(; $rows = mysql_fetch_array($result);){
        //echo "entro<br>".$rows[0];

        if( $rows['carglo'] == 'on' ){
            $glosa += $rows[0];
            $glonog = $rows['carfue'] ."-". $rows['rdenum'];
            $glofec = $rows['fecha_data'];

            //Hallando el total aceptado de la glosa
            //Consulta No. 0016

            $sql = "SELECT fdevco, rdefac, rdefue, rdenum "
            ."FROM {$wbasedato}_000021 a, {$wbasedato}_000065 b, {$wbasedato}_000040 c "
            ."WHERE rdefac = '{$rows['rdefac']}' "
            ."  AND rdeglo = '{$rows['carfue']}-{$rows['rdenum']}'"
            ."  AND carfue = rdefue "
            ."  AND fdefue = rdefue "
            ."  AND fdedoc = rdenum "
            ."  AND rdeest = 'on' "
            ."  AND rdeglo <> '' "
            ."  AND carncr = 'on' "
            ."  AND carcfa = 'on' "
            ;

            $result2 = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0016 - ".mysql_error() );

            $rows2 = "";
            for(; $rows2 = mysql_fetch_array($result2);){
                $aceptado += $rows2[0];
            }

            $sql = "SELECT rdevco, rdefac, rdefue, rdenum "
            ."FROM {$wbasedato}_000021 a,  {$wbasedato}_000040 b "
            ."WHERE rdeglo = '{$rows['carfue']}-{$rows['rdenum']}' "
            ."  AND rdefac = '{$rows['rdefac']}' "
            ."  AND rdeest = 'on' "
            ."  AND rdefue = carfue "
            ."  AND carncr = 'on' "
            ."  AND carcca = 'on' ";
            //          ."  AND rdeglo <> '' ";
            //          ."AND a.fecha_data <= '$wfeccor'";
            //echo $sql;
            //exit;
            $result2 = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0016 - ".mysql_error() );

            $rows2 = "";
            for(; $rows2 = mysql_fetch_array($result2);){
                $aceptado += $rows2[0];
            }
        }
    }

    $resglo['glofec'] = $glofec;
    $resglo['glonog'] = $glonog;
    $resglo['gloace'] = $aceptado;
    $resglo['gloval'] = $glosa;
    $resglo['glorec'] = $glosa - $aceptado;

    return $resglo;
}


/**
 * Consulta la glosa para una factura
 *
 * @param unknown_type $fac: Numero de factura
 * @param unknown_type $ffa: factura
 * @return vector
 *
 * Devuelve un vector con los datos de la glosa (No. de Glosa, Fecha de la Glosa,
 * Valor de la glosa, valor aceptado de la glosa y recobrar de la glosa
 *
 * Valores de retorno
 *
 * Array $resglo: Contiene un array de retorno con todos los valores correspondiente a la glosa
 *
 * glofec		Fecha en que se genero la glosa
 * glonog		Número de la glosa
 * gloval		Glosa
 * gloace		Valor aceptado de la glosa
 * glorec		Recobrar de la glosa
 */
function consultarGlosasOptima($fac, $ffa, $arr_glosas)
{
    global $wbasedato;
    global $conex;

    $glofec = "";
    $glonog = "";
    $glosa = 0;
    $aceptado = 0;
    $resglo = array();  //Array a devolver con los datos de la glosa
    $glorec = 0;        //Valor a recobrar, diferencia entre la glosa y el aceptado

    //Hallando la glosa y el total aceptado
    //Consulta 15
    // $sql = "SELECT  rdevca, carfue, carglo, carncr, carndb, rdenum, rdefac, carrec, rdereg, carabo, carcfa, carcca, rdevco, rdeglo, a.fecha_data "
    // . "     FROM  {$wbasedato}_000021 a, {$wbasedato}_000040 "
    // . "   	WHERE rdefac= '" . $fac . "' "
    // . "   	  AND rdeffa= '" . $ffa . "' "
    // . "       AND rdeest = 'on' "
    // . "       AND rdefue = carfue "
    // . "       AND carglo = 'on'  "
    // . "     group by rdenum, rdefue  ";

    // $result = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0015 - ".mysql_error() );
    //if($fac == 'CS-241975') { echo "...........<pre>".$ffa.'--'.$fac.'>>>'; print_r($arr_glosas[$ffa]); echo "</pre>"; exit(); }
    //$rows = array();
    // for(; $rows = mysql_fetch_array($result);)
    // {
    if(array_key_exists($ffa, $arr_glosas) && array_key_exists($fac, $arr_glosas[$ffa]))
    {
        foreach($arr_glosas[$ffa][$fac] as $key_idx => $arr_facts)
        {
            $rows = $arr_facts;
            /*
                $arr_glosas =
                Array(
                        [20] => Array
                            (
                                [CS-27382] => Array
                                    (
                                        [0] => Array
                                            (
                                                [rdevca] => 54100
                                                [carfue] => 85
                                                [carglo] => on
                                                [carncr] => off
                                                [carndb] => off
                                                [rdenum] => 1
                                                [carrec] => off
                                                [rdereg] => 0
                                                [carabo] => off
                                                [carcfa] => off
                                                [carcca] => off
                                                [rdevco] => 0
                                                [rdeglo] =>
                                                [fecha_data] => 2007-04-26
                                                [rdeffa] => 20
                                                [rdefac] => CS-27382
                                            )
                                    )
            */
            //echo "entro<br>".$rows[0];

            if( $rows['carglo'] == 'on' ){
                $glosa += $rows["rdevca"];
                $glonog = $rows['carfue'] ."-". $rows['rdenum'];
                $glofec = $rows['fecha_data'];

                //Hallando el total aceptado de la glosa
                //Consulta No. 0016

                $sql = "SELECT fdevco, rdefac, rdefue, rdenum "
                ."FROM {$wbasedato}_000021 a, {$wbasedato}_000065 b, {$wbasedato}_000040 c "
                ."WHERE rdefac = '{$rows['rdefac']}' "
                ."  AND rdeglo = '{$rows['carfue']}-{$rows['rdenum']}'"
                ."  AND carfue = rdefue "
                ."  AND fdefue = rdefue "
                ."  AND fdedoc = rdenum "
                ."  AND rdeest = 'on' "
                ."  AND rdeglo <> '' "
                ."	AND carncr = 'on' "
                ."	AND carcfa = 'on' "
                ;

                $result2 = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0016 - ".mysql_error() );

                $rows2 = "";
                for(; $rows2 = mysql_fetch_array($result2);){
                    $aceptado += $rows2[0];
                }

                $sql = "SELECT rdevco, rdefac, rdefue, rdenum "
                ."FROM {$wbasedato}_000021 a,  {$wbasedato}_000040 b "
                ."WHERE rdeglo = '{$rows['carfue']}-{$rows['rdenum']}' "
                ."  AND rdefac = '{$rows['rdefac']}' "
                ."  AND rdeest = 'on' "
                ."  AND rdefue = carfue "
                ."  AND carncr = 'on' "
                ."  AND carcca = 'on' ";
                //  ."  AND rdeglo <> '' ";
                //  ."  AND a.fecha_data <= '$wfeccor'";

                $result2 = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0016 - ".mysql_error() );

                $rows2 = "";
                for(; $rows2 = mysql_fetch_array($result2);){
                    $aceptado += $rows2[0];
                }
            }
        }
    }

    $resglo['glofec'] = $glofec;
    $resglo['glonog'] = $glonog;
    $resglo['gloace'] = $aceptado;
    $resglo['gloval'] = $glosa;
    $resglo['glorec'] = $glosa - $aceptado;

    return $resglo;
}

/**
 * Consulta la glosa para todas las facturas y crea un array con la respuesta, esto evita tener que hacer una consulta por cada factura, lo que ahorra accesos a la base de datos.
 *
 * @param unknown_type $conex: conexión a la base de datos
 * @param unknown_type $wbasedato: prefijo de tabla de la empresa
 * @return vector
 *
 * Devuelve un vector con primer indice 'rdeffa' y subarrays con indices 'rdefac' los cuales tienen otros subarrays con los datos de cada glosa de cada factura.
 *
 * Valores de retorno
 *
 * Array $arr_glosas: Contiene un array de fuentes subarray de facturas y arrays de información de glosas
 *
 */
function getArrayGlosas($conex, $wbasedato)
{
    $sql = "SELECT  c21.rdeffa, c21.rdefac, c21.rdevca, c40.carfue, c40.carglo, c40.carncr, c40.carndb, c21.rdenum, c21.rdefac, c40.carrec,
                    c21.rdereg, c40.carabo, c40.carcfa, c40.carcca, c21.rdevco, c21.rdeglo, c21.fecha_data
            FROM    {$wbasedato}_000021 AS c21
                    INNER JOIN
                    {$wbasedato}_000040 AS c40 ON (c40.carfue = c21.rdefue AND c40.carglo = 'on')
            WHERE   c21.rdeest = 'on'
            GROUP BY c21.rdenum, c21.rdefue, c21.rdeffa,c21.rdefac ";

    /*
        BASADA EN LA SIGUIENTE CONSULTA.
        $sql = "SELECT  rdevca, carfue, carglo, carncr, carndb, rdenum, rdefac, carrec, rdereg, carabo, carcfa, carcca, rdevco, rdeglo, a.fecha_data
                FROM    {$wbasedato}_000021 a, {$wbasedato}_000040
                WHERE   rdefac= '" . $fac . "'
                        AND rdeffa= '" . $ffa . "'
                        AND rdeest = 'on'
                        AND rdefue = carfue
                        AND carglo = 'on'
                group by rdenum, rdefue  ";
    */

    $result = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0015 - ".mysql_error() );
    $arr_glosas = array();
    while ($row = mysql_fetch_array($result))
    {
        $factura_num = trim(strtoupper($row["rdefac"]));
        if(!array_key_exists($row["rdeffa"], $arr_glosas))
        {
            $arr_glosas[$row["rdeffa"]] = array();
        }

        if(!array_key_exists($factura_num, $arr_glosas[$row["rdeffa"]]))
        {
            $arr_glosas[$row["rdeffa"]][$factura_num] = array();
        }

        $arr_glosas[$row["rdeffa"]][$factura_num][] = array(
                                                            "rdevca"     => $row["rdevca"],
                                                            "carfue"     => $row["carfue"],
                                                            "carglo"     => $row["carglo"],
                                                            "carncr"     => $row["carncr"],
                                                            "carndb"     => $row["carndb"],
                                                            "rdenum"     => $row["rdenum"],
                                                            "carrec"     => $row["carrec"],
                                                            "rdereg"     => $row["rdereg"],
                                                            "carabo"     => $row["carabo"],
                                                            "carcfa"     => $row["carcfa"],
                                                            "carcca"     => $row["carcca"],
                                                            "rdevco"     => $row["rdevco"],
                                                            "rdeglo"     => $row["rdeglo"],
                                                            "fecha_data" => $row["fecha_data"],
                                                            "rdeffa"     => $row["rdeffa"],
                                                            "rdefac"     => $factura_num);
    }
    return $arr_glosas;
}

/**
 * función para consultar una factura, con el nombre del paciente, su poliza y vlores asociados a la factura
 *
 * @param unknown_type $wfecini: fecha de incio para la busqueda de facturas
 * @param unknown_type $wfecfin: fecha final para la busqueda de facturas
 * @param unknown_type $wempCod: codigo de la empresa para busqueda de las facturas
 * @return unknown
 */

function consultarFacturasOptima($wfecini, $wfecfin, $wempCod, $wesf)
{
    global $conex;
    global $wbasedato;
    global $plano;
    global $wemp_pmla;


    if( $wemp_pmla == "09" ){
        $valorFactura = " fenval ";
    }else{
        $valorFactura = " ( fenval + fenviv )";
    }

    //if($wcco=='Todos')
        $filwcco='';
        /*else
        $filwcco="AND fencco='".$wcco."'";*/

    if($wesf=='Todos')
        $filwesf='';
        else
        $filwesf="AND a.fenesf='".$wesf."'";
    $q = "DROP TABLE IF EXISTS tmppal";
    $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());
    $q = "  CREATE TABLE IF NOT EXISTS tmppal
            (INDEX idx(fenffa, fenfac))"; //temporal que almacena los datos de las facturas correspondientes al rango de fechas.
    if ($wempCod=='')
    {
        $q .= " SELECT  a.fenfac, a.fenfec, $valorFactura AS sum_val_viv , a.fensal, a.fenvnd, a.fenvnc, a.fenrbo, a.fenesf, a.fencco, a.fenffa, a.fennit,
                        a.fendpa, a.fennpa, '-' as ingpol, d.empnom, d.emptem, fenhis, fening, a.seguridad
                FROM    ".$wbasedato."_000018 a
                        INNER JOIN
                        ".$wbasedato."_000024 d ON (d.empcod = a.fencod AND d.emptem = a.fentip AND a.fenres = d.empres)
                WHERE   a.fenfec between '".$wfecini."'AND '".$wfecfin."'
                        AND a.fensal <> 0
                        AND a.fencco <> ''
                        AND a.fenest = 'on'
                        ".$filwesf."
                        ".$filwcco."
                ORDER BY  a.fenfac ";
           /*
                AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on')
                AND empcod = empres
           */
    }
    else
    {
        //echo "<br>entró con wempCod = ".$wempCod;
        $exp = explode( "-", $wempCod );
        if( trim( $exp[0] ) != 'EMP' )
        {
            // 2012-08-24
            $qemp = "   SELECT  b.empcod
                        FROM    ".$wbasedato."_000024 a, ".$wbasedato."_000024 b
                        WHERE   a.empcod = '" . $wempCod . "'
                                AND a.empnit = b.empnit";
            $resemp = mysql_query($qemp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qemp . " - " . mysql_error());
            $numemp = mysql_num_rows($resemp);

            if($numemp>0)
                $regemp = "(";
            else
                $regemp = "('')";

            while($rowemp = mysql_fetch_array($resemp))
            {
                if($rowemp[0]!="NO APLICA" && $rowemp[0]!="" && $rowemp[0]!=NULL)
                    $regemp .= "'".$rowemp[0]."',";
            }

            if($numemp>0)
            {
                if($regemp!="(")
                {
                    $regemp .= ")";
                    $regemp = str_replace(",)",")",$regemp);
                }
                else
                    $regemp = "('')";
            }

             $q .= "SELECT  a.fenfac, a.fenfec, (fenval+fenviv) AS sum_val_viv, a.fensal, a.fenvnd, a.fenvnc, a.fenrbo, a.fenesf, a.fencco, a.fenffa, a.fennit,
                            a.fendpa, a.fennpa, '-' as ingpol, d.empnom, d.emptem, fenhis, fening, a.seguridad
                    FROM    ".$wbasedato."_000018 a
                            INNER JOIN
                            ".$wbasedato."_000024 d ON (d.empres = a.fenres)
                    WHERE   a.fenfec between '".$wfecini."' AND '".$wfecfin."'
                            AND a.fenres IN ".$regemp."
                            AND a.fensal <> 0
                            AND a.fenval <> 0
                            AND a.fenest = 'on'
                            ".$filwesf."
                            AND a.fencco <> ''
                            ".$filwcco."
                    GROUP BY  a.fenfac
                    ORDER BY  a.fenfac ";
                    /*
                        AND a.fencod = fenres       // 2012-08-24
                        AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on')
                    */
        }
        else
        {
            $q .= " SELECT  a.fenfac, a.fenfec, {$valorFactura}, a.fensal, a.fenvnd, a.fenvnc, a.fenrbo, a.fenesf, a.fencco, a.fenffa, a.fennit,
                            a.fendpa, a.fennpa, '-' as ingpol, d.empnom, d.emptem, fenhis, fening, a.seguridad
                    FROM    ".$wbasedato."_000018 a, ".$wbasedato."_000024 d
                    WHERE   a.fenfec between '".$wfecini."' AND '".$wfecfin."'
                            AND a.fensal != 0
                           ".$filwesf."
                            AND a.fentip = '".trim( $exp[1] )."-".trim( $exp[2] )."'
                            AND a.fenest = 'on'
                            AND a.fentip=d.emptem
                            AND empcod != empres
                            AND fenres = empres
                            AND fencod = empcod

                            AND fencco != ''
                            ".$filwcco."
                    ORDER BY  a.fenfac ";
                /*
                    AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on')
                */
        }
    }


    $err = mysql_query($q,$conex)or die(mysql_error()."-".mysql_errno());;
    //$num = mysql_num_rows($err);

    $q = "  SELECT *
            FROM tmppal";
    $err = mysql_query($q,$conex) or die(mysql_error()."-".mysql_errno());
    $num = mysql_num_rows($err);


    $vector = array();
    if ($num>0)
    {
        //if($wcco=='Todos')
            $filwcco='';
            /*else
            $filwcco="AND Rencco='".$wcco."'";*/

        $q = "DROP TABLE IF EXISTS tmp1";
        $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());

        /*********** OPTIMO: Agregó indice INDEX idx2_tmp(Renfue, Rennum) ***********/
        //fuentes para notas credito y notas debito
        //temporal que almacena los datos de los documentos que pertencen a las facturas que fueron filtradas por fecha
       $qtmp = "CREATE  TABLE IF NOT EXISTS tmp1
                (INDEX idx(Rdefac, Carncr), INDEX idx2_tmp1(Renfue, Rennum))
                SELECT  c20.Renfue, c20.Rennum, c20.Renvca, c21.Rdefac, c40.Carncr, c40.Carndb, c21.Rdevco
                FROM    ".$wbasedato."_000021 AS c21
                        INNER JOIN
                        ".$wbasedato."_000020 AS c20 ON (c20.Rennum = c21.Rdenum AND c20.Renfue = c21.Rdefue AND c20.Rencco = c21.Rdecco)
                        INNER JOIN
                        ".$wbasedato."_000040 AS c40 ON (c40.Carfue = c21.Rdefue)
                        INNER JOIN
                        tmppal AS tm ON (tm.fenfac = c21.Rdefac AND tm.fenffa = c21.Rdeffa)
                WHERE   c21.Rdeest ='on'
                        ".$filwcco."
                        AND (c40.Carncr='on' OR c40.Carndb='on')";

        $rstmp=mysql_query($qtmp, $conex) or die(mysql_error()."-".mysql_errno());

        /*********** OPTIMO: Se elimina creación intermedia de tabla temporal y en una sola consulta se hace los que hacían dos consultas ***********/
        /*
        $q = "DROP TABLE IF EXISTS tmpca";
        $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());

        $qcaus="CREATE TABLE IF NOT EXISTS tmpca " // temporal que almacena las asociaciones de los documentos con sus respectivas causas.
             ."(INDEX idx(Docfue, Docnum))"
             ." SELECT Docfue, Docnum, Doccau, Docest"
             ."   FROM ".$wbasedato."_000071, tmp"
             ."  WHERE Docfue = Renfue"
             ."    AND Docnum = Rennum"
             ."    AND Docest ='on'";
        $rsca=mysql_query($qcaus, $conex) or die(mysql_error()."-".mysql_errno());
        */

        /*$q = "DROP TABLE IF EXISTS tmpcato";
        $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());

        $qcaus="CREATE TABLE IF NOT EXISTS tmpcato " // temporal que guarda los datos(codigo y nombre) de las causas.
                ."(INDEX idx(Docfue, Docnum))"
                ."SELECT Caucod, Caunom, Docfue, Docnum "
                ."  FROM tmpca, ".$wbasedato."_000072"
                ." WHERE Doccau=Caucod";
        $rsca=mysql_query($qcaus, $conex) or die(mysql_error()."-".mysql_errno());*/

        $q = "DROP TABLE IF EXISTS tmpcato";
        $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());

        // temporal que guarda los datos(codigo y nombre) de las causas.
        $qcaus="CREATE TABLE IF NOT EXISTS tmpcato
                (INDEX idx(Docfue, Docnum))
                SELECT  c72.Caucod, c72.Caunom, c71.Docfue, c71.Docnum, c71.Doccau, c71.Docest
                FROM    ".$wbasedato."_000071 AS c71
                        INNER JOIN
                        tmp1 ON (tmp1.Rennum = c71.Docnum AND tmp1.Renfue = c71.Docfue)
                        INNER JOIN
                        ".$wbasedato."_000072 AS c72 ON (c72.Caucod= c71.Doccau)
                WHERE   c71.Docest ='on'";
        $rsca=mysql_query($qcaus, $conex) or die(mysql_error()."-".mysql_errno());

        // Consultar una única vez la tabla tm
        $q_causas=" SELECT Renfue, Rennum,SUM(Rdevco) AS suma_vco, Rdefac, Carncr, Carndb
                    FROM tmp1
                    GROUP BY  1, 2, Rdefac";

        $rs=mysql_query($q_causas, $conex) or die( mysql_error());
        //$num_fuentes=mysql_num_rows($rs);
        $arr_facturas_fue = array();
        // Crear un array de la tabla temporal tmp1
        while($rowf = mysql_fetch_array($rs))
        {
            if(!array_key_exists($rowf['Rdefac'], $arr_facturas_fue))
            {
                $arr_facturas_fue[$rowf['Rdefac']] = array("creditos"=>array(),"debitos"=>array());
            }

            if($rowf['Carncr'] == 'on')
            {
                $arr_facturas_fue[$rowf['Rdefac']]["creditos"][] = array("Renfue"   => $rowf['Renfue'],
                                                                        "Rennum"   => $rowf['Rennum'],
                                                                        "suma_vco" => $rowf['suma_vco'],
                                                                        "Rdefac"   => $rowf['Rdefac'],
                                                                        "Carncr"   => $rowf['Carncr'],
                                                                        "Carndb"   => $rowf['Carndb']);
            }
            else if($rowf['Carndb'] == 'on')
            {
                $arr_facturas_fue[$rowf['Rdefac']]["debitos"][] = array( "Renfue"   => $rowf['Renfue'],
                                                                        "Rennum"   => $rowf['Rennum'],
                                                                        "suma_vco" => $rowf['suma_vco'],
                                                                        "Rdefac"   => $rowf['Rdefac'],
                                                                        "Carncr"   => $rowf['Carncr'],
                                                                        "Carndb"   => $rowf['Carndb']);
            }
        }

        // construir el vector de justificaciones.
        $arr_justificacion = array();
        $qCausas="  SELECT  Caucod, Caunom, Docfue, Docnum
                    FROM    tmpcato
                    GROUP BY 1, 2, 3, 4";
        $rscaus = mysql_query($qCausas,$conex) or die(mysql_error());
        /*
            ALERTA!!!! aquí se detectó que el campo "Docfue" a veces tiene un espacio "xx" "xx "  lo cual hace que un codigo no sea igual [28 ] [28]
            Esto no se corrigió porque asi estaba en la función original (no se cambia con el fin de luego de las modificaciones comparar los resultados al final y ver que son iguales
            antes y despues de todos los cambios).
        */
        while($rowC = mysql_fetch_array($rscaus))
        {
            if(!array_key_exists($rowC["Docfue"], $arr_justificacion))
            {
                $arr_justificacion[$rowC["Docfue"]] = array();
            }

            if(!array_key_exists($rowC["Docnum"], $arr_justificacion[$rowC["Docfue"]]))
            {
                $arr_justificacion[$rowC["Docfue"]][$rowC["Docnum"]] = array();
            }

            $arr_justificacion[$rowC["Docfue"]][$rowC["Docnum"]][] = array( "Caucod" => $rowC["Caucod"],
                                                                            "Caunom" => $rowC["Caunom"],
                                                                            "Docfue" => $rowC["Docfue"],
                                                                            "Docnum" => $rowC["Docnum"]);
        }

        $arr_glosas = getArrayGlosas($conex, $wbasedato);

        $i=0;
        //el while
        while($row = mysql_fetch_array($err))
        {
            $factura_num = $row['fenfac'];

            //$glosa = consultarGlosas( $factura_num, $row['fenffa'] );
            $glosa = consultarGlosasOptima( $factura_num, $row['fenffa'], $arr_glosas);

            $radicacion = consultarRadicacion( $factura_num, $row['fenffa']);
            $row['ingpol'] = consultarPoliza( $row['fenhis'], $row['fening'] );
            $estado = consultarEstado( $factura_num, $row['fenffa'], $row['fenesf']  );

            if(!array_key_exists($factura_num, $vector))
            {
                $vector[$factura_num] = array();
            }
            $vector[$factura_num] = array(
                                            "numfac"     => $factura_num,
                                            "fecfac"     => $row['fenfec'],
                                            "valfac"     => $row['sum_val_viv'],
                                            "salfac"     => $row['fensal'],
                                            "debfac"     => $row['fenvnd'],                             //notas debito
                                            "debfacd"    => array("debNum"=>array(),"debJus"=>array()), //array que almacena el detalle de las notas debito.
                                            "debdocnums" => "",                                         //variable que almacenara todas los documentos asociados a la nota dédito como un string
                                            "debcausas"  => "",                                         //variable que almacenara todas las causas de los documentos asociados a la nota dédito como un string
                                            "crefac"     => $row['fenvnc'],                             //notas credito
                                            "credocnums" => "",                                         //variable que almacenara todas los documentos asociados a la nota crédito como un string
                                            "crecausas"  => "",                                         //variable que almacenara todas las causas de los documentos asociados a la nota crédito como un string
                                            "crefacd"    => array("creNum"=>array(),"CreJus"=>array()), //array que almacena el detalle de las notas credito.
                                            "ccofac"     => $row['fencco'],
                                            "recfac"     => "",
                                            "estfac"     => "",
                                            "fuefac"     => "",
                                            "nitfac"     => "",
                                            "cedula"     => "",
                                            "nombre"     => "",
                                            "poliza"     => "",
                                            "empfac"     => "",
                                            "emptip"     => "",
                                            "usergen"    => "",
                                            "facffa"     => "",
                                            "radnum"     => "",
                                            "radfec"     => "",
                                            "glonog"     => "",
                                            "glofec"     => "",
                                            "gloval"     => "",
                                            "gloace"     => "",
                                            "glorec"     => "",
                                            "estnum"     => "",
                                            "estfec"     => ""
                                            );

            //consultar la causa de la N debito o la N credito
            if($vector[$factura_num]['crefac'] != 0 && array_key_exists($factura_num, $arr_facturas_fue)) //si hay notas credito o notas debito
            {
                /*
                    $q_causas=" SELECT Renfue, Rennum, SUM(Rdevco)"
                            ." FROM tmp"
                            ." WHERE Rdefac='".$row['fenfac']."'"
                            ."   AND Carncr='on'"
                            ." GROUP BY  1, 2";


                    $rs=mysql_query($q_causas, $conex) or die( mysql_error());
                    $num_fuentes=mysql_num_rows($rs);
                */

                    // $causas="";
                    // $causas2="";
                    //$row_fuentes = mysql_fetch_array($rs);
                    //$vector['crefacd'][$i]['creNum'][$y]=$row_fuentes["Renfue"]."-".$row_fuentes["Rennum"];   /******  DIFERENTE!!  *******/
                    foreach ($arr_facturas_fue[$factura_num]["creditos"] as $idxnota => $arr_dlle)
                    {
                        $causas="";
                        $causas2="";
                        $idx = $arr_dlle["Renfue"]."-".$arr_dlle["Rennum"];
                        $vector[$factura_num]['crefacd']['creNum'][$idx] = $idx;   /******  DIFERENTE!!  *******/
                        /*
                        //construir el vector de justificaciones.
                        $qCausas="SELECT Caucod, Caunom "
                                ."  FROM tmpcato"
                                ." WHERE Docfue='".$arr_dlle["Renfue"]."'"
                                ."   AND Docnum='".$arr_dlle["Rennum"]."'"
                                ." GROUP BY 1, 2";
                        $rscaus=mysql_query($qCausas,$conex)or die(mysql_error());
                        $numCaus=mysql_num_rows($rscaus);
                        */
                        if(array_key_exists($arr_dlle["Renfue"], $arr_justificacion) && array_key_exists($arr_dlle["Rennum"], $arr_justificacion[$arr_dlle["Renfue"]]))
                        {
                            $separador = "";
                            foreach($arr_justificacion[$arr_dlle["Renfue"]][$arr_dlle["Rennum"]] as $rennum => $arr_just_dlle)
                            {
                                //if($factura_num == 'CS-241948') {echo "<br>"; print_r($arr_just_dlle); }
                                /*
                                    arr_justificacion
                                    [27 ] => Array
                                        (
                                            [10166] => Array
                                                (
                                                    [Caucod] => 107
                                                    [Caunom] => MEDICAMENTOS
                                                    [Docfue] => 27
                                                    [Docnum] => 10166
                                                )

                                            [10383] => Array
                                                (
                                                    [Caucod] => 112
                                                    [Caunom] => FACTURA EXCEDE TOPES AUTORIZADOS
                                                    [Docfue] => 27
                                                    [Docnum] => 10383
                                                )
                                */

                                $rowCau = $arr_just_dlle;
                                $causas=$causas.$separador." ".$rowCau["Caucod"]."-".$rowCau["Caunom"];
                                $causas2=$causas2.$separador." ".$rowCau["Caucod"]."-".$rowCau["Caunom"];
                                $separador = ";";
                            }
                            $causas2 = "|".$causas2."|";
                        }
                        //hasta acá va el vector de justificaciones.
                        if($causas=='')
                        {
                            $causas='Falt&oacute; causa';
                            $causas2='|Falt&oacute; causa|';
                        }
                        else
                        {
                             $causas=$causas;
                             $causas2=$causas2;
                        }
                        //$vector[$factura_num]['crefacd'][$i]['CreJus'][$y]=$causas;    /******  DIFERENTE!!  *******/
                        $vector[$factura_num]['crefacd']['CreJus'][$idx]=$causas;        /******  DIFERENTE!!  *******/
                        //$vector['crefacd'][$i]['CreJus'][$y]=$arr_dlle["Renfue"];      /******  DIFERENTE!!  *******/

                        $coma='|';
                        // if($y==$num_fuentes-1)
                        //     $coma='|';
                        // else
                        //     $coma='|';                                               /******  DIFERENTE!!  *******/

                        $vector[$factura_num]['credocnums'].="|".$arr_dlle["Renfue"]."-".$arr_dlle["Rennum"]."".$coma."";   /******  DIFERENTE!!  *******/
                        $vector[$factura_num]['crecausas'].= $causas2;                                                      /******  DIFERENTE!!  *******/
                    }
                // }
            }
            else
            {
                $vector[$factura_num]['crefacd']['creNum'] = array();
                $vector[$factura_num]['crefacd']['CreJus'] = array();
                $vector[$factura_num]['crecausas']="";
            }

            if($vector[$factura_num]['debfac'] != 0 && array_key_exists($factura_num, $arr_facturas_fue)) //si hay notas credito o notas debito
            {
                /*
                $q_1=" SELECT Renfue, Rennum,SUM(Rdevco) "
                        ." FROM tmp"
                        ." WHERE Rdefac='".$row['fenfac']."'"
                        ."   AND Carndb='on'"
                        ." GROUP BY 1, 2";

                $rs=mysql_query($q_1, $conex) or die( mysql_error());
                $num_fuentes=mysql_num_rows($rs);
                */
                    /*
                        [CS-228036] => Array
                        (
                            [creditos] => Array
                                (
                                )

                            [debitos] => Array
                                (
                                    [0] => Array
                                        (
                                            [Renfue] => 25
                                            [Rennum] => 25
                                            [suma_vco] => 33230
                                            [Rdefac] => CS-228036
                                            [Carncr] => off
                                            [Carndb] => on
                                        )
                                )
                        )
                    */
                    // $causas="";
                    // $causas2="";
                    foreach ($arr_facturas_fue[$factura_num]["debitos"] as $idxnota => $arr_dlle)
                    {
                        $causas="";
                        $causas2="";
                        $idx = $arr_dlle["Renfue"]."-".$arr_dlle["Rennum"];
                        $vector[$factura_num]['debfacd']['debNum'][$idx] = $idx;   /******  DIFERENTE!!  *******/
                        /*
                        //construir el vector de justificaciones.
                        $qCausas="SELECT Caucod, Caunom "
                                ."  FROM tmpcato"
                                ." WHERE Docfue='".$arr_dlle["Renfue"]."'"
                                ."   AND Docnum='".$arr_dlle["Rennum"]."'"
                                ." GROUP BY 1, 2";
                        $rscaus=mysql_query($qCausas,$conex)or die(mysql_error());
                        $numCaus=mysql_num_rows($rscaus);
                        */
                        if(array_key_exists($arr_dlle["Renfue"], $arr_justificacion) && array_key_exists($arr_dlle["Rennum"], $arr_justificacion[$arr_dlle["Renfue"]]))
                        {
                            $separador = "";
                            foreach($arr_justificacion[$arr_dlle["Renfue"]][$arr_dlle["Rennum"]] as $rennum => $arr_just_dlle)
                            {
                                /*
                                    arr_justificacion
                                    [27 ] => Array
                                        (
                                            [10166] => Array
                                                (
                                                    [Caucod] => 107
                                                    [Caunom] => MEDICAMENTOS
                                                    [Docfue] => 27
                                                    [Docnum] => 10166
                                                )

                                            [10383] => Array
                                                (
                                                    [Caucod] => 112
                                                    [Caunom] => FACTURA EXCEDE TOPES AUTORIZADOS
                                                    [Docfue] => 27
                                                    [Docnum] => 10383
                                                )
                                */

                                $rowCau = $arr_just_dlle;
                                $causas=$causas.$separador." ".$rowCau["Caucod"]."-".$rowCau["Caunom"];
                                $causas2=$causas2.$separador." ".$rowCau["Caucod"]."-".$rowCau["Caunom"];
                                $separador = ";";
                            }
                            $causas2 = "|".$causas2."|";
                        }
                        //hasta acá va el vector de justificaciones.
                        if($causas=='')
                        {
                            $causas='Falt&oacute; causa';
                            $causas2='|Falt&oacute; causa|';
                        }
                        else
                        {
                             $causas=$causas;
                             $causas2=$causas2;
                        }
                        //$vector[$factura_num]['debfacd'][$i]['debJus'][$y]=$causas;                       /******  DIFERENTE!!  *******/
                        $vector[$factura_num]['debfacd']['debJus'][$idx]=$causas;                           /******  DIFERENTE!!  *******/
                        //$vector['debfacd'][$i]['debJus'][$y]=$arr_dlle["Renfue"];                         /******  DIFERENTE!!  *******/

                        $coma='|';
                        // if($y==$num_fuentes-1)
                        //     $coma='|';
                        // else
                        //     $coma='|';                                                                   /******  DIFERENTE!!  *******/

                        $vector[$factura_num]['debdocnums'].="|".$arr_dlle["Renfue"]."-".$arr_dlle["Rennum"]."".$coma."";   /******  DIFERENTE!!  *******/
                        $vector[$factura_num]['debcausas'].= $causas2;                                                      /******  DIFERENTE!!  *******/
                    }
                //}
            }
            else
            {
                $vector[$factura_num]['debfacd']['debNum'] = array();
                $vector[$factura_num]['debfacd']['debJus'] = array();
                $vector[$factura_num]['debcausas']="";
            }

            //fin de consulta de causas.
            $vector[$factura_num]['recfac']=$row['fenrbo'];
            $vector[$factura_num]['estfac']=$row['fenesf'];
            //$vector[$factura_num]['ccofac']=$row['fencco'];
            $vector[$factura_num]['fuefac']=$row['fenffa'];
            $vector[$factura_num]['nitfac']=$row['fennit'];
            $vector[$factura_num]['cedula']=$row['fendpa'];
            $vector[$factura_num]['nombre']=$row['fennpa'];
            $vector[$factura_num]['poliza']=$row['ingpol'];
            $vector[$factura_num]['empfac']=$row['empnom'];
            $vector[$factura_num]['emptip']=$row['emptem'];
            $aux=explode("-",$row['seguridad']);
            $vector[$factura_num]['usergen']=$aux[1];
            $vector[$factura_num]['facffa']=$row['fenffa'];
            $vector[$factura_num]['radnum']=$radicacion['radnum'];
            $vector[$factura_num]['radfec']=$radicacion['radfec'];
            $vector[$factura_num]['glonog']=$glosa['glonog'];
            $vector[$factura_num]['glofec']=$glosa['glofec'];
            $vector[$factura_num]['gloval']=$glosa['gloval'];
            $vector[$factura_num]['gloace']=$glosa['gloace'];
            $vector[$factura_num]['glorec'] = $glosa['gloval'] - $glosa['gloace'];
            $vector[$factura_num]['estnum']=$estado['estnum'];
            $vector[$factura_num]['estfec']=$estado['estfec'];

            if( $vector[$factura_num]['glorec'] > $vector[$factura_num]['salfac'] ){
                $vector[$factura_num]['glorec'] = $vector[$factura_num]['salfac'];
            }

            if( $vector[$factura_num]['salfac'] < 0){
                $vector[$factura_num]['glorec'] = 0;
            }
            $i++;

            unset($glosa);
            unset($radicacion);
            unset($estado);
        }
    }
    return $vector;
}


function consultarFacturas($wfecini, $wfecfin, $wempCod, $wesf)
{
    global $conex;
    global $wbasedato;
    global $plano;
    global $wemp_pmla;


    if( $wemp_pmla == "09" ){
        $valorFactura = " fenval ";
    }else{
        $valorFactura = " ( fenval + fenviv ) ";
    }

    //if($wcco=='Todos')
        $filwcco='';
        /*else
        $filwcco="AND fencco='".$wcco."'";*/

    if($wesf=='Todos')
        $filwesf='';
        else
        $filwesf="AND fenesf='".$wesf."'";
    $q = "DROP TABLE IF EXISTS tmppal";
    $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());
    $q = " CREATE TABLE IF NOT EXISTS tmppal "; //temporal que almacena los datos de las facturas correspondientes al rango de fechas.
    if ($wempCod=='')
    {
        $q .= " SELECT a.fenfac, a.fenfec, {$valorFactura} , a.fensal, a.fenvnd, a.fenvnc, a.fenrbo, a.fenesf, a.fencco, a.fenffa, a.fennit, "
        ."            a.fendpa, a.fennpa, '-' as ingpol, d.empnom, d.emptem, fenhis, fening, a.seguridad "
        ."    FROM  ".$wbasedato."_000018 a,".$wbasedato."_000024 d  "
        ."    WHERE  a.fenfec between '".$wfecini."'"
        ."     AND '".$wfecfin."'"
        ."     AND a.fensal != 0 "
        ."     ".$filwesf.""
        ."     AND a.fenest = 'on' "
        ."     AND a.fencod=d.empcod "
        ."     AND a.fenres=d.empres "
        ."     AND a.fentip=d.emptem "
        //      ."     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
        ."     AND fencco != '' "
        ."     ".$filwcco.""
        //      ."     AND empcod = empres "
        ."    ORDER BY  a.fenfac ";
    }
    else
    {
        //echo "<br>entró con wempCod = ".$wempCod;
        $exp = explode( "-", $wempCod );
      if( trim( $exp[0] ) != 'EMP' )
      {
        // 2012-08-24
        $qemp = "   SELECT b.empcod
                    FROM ".$wbasedato."_000024 a, ".$wbasedato."_000024 b
                    WHERE a.empcod = '" . $wempCod . "'
                    AND a.empnit = b.empnit";
        $resemp = mysql_query($qemp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qemp . " - " . mysql_error());
        $numemp = mysql_num_rows($resemp);

        if($numemp>0)
            $regemp = "(";
        else
            $regemp = "('')";

        while($rowemp = mysql_fetch_array($resemp))
        {
            if($rowemp[0]!="NO APLICA" && $rowemp[0]!="" && $rowemp[0]!=NULL)
                $regemp .= "'".$rowemp[0]."',";
        }

        if($numemp>0)
        {
            if($regemp!="(")
            {
                $regemp .= ")";
                $regemp = str_replace(",)",")",$regemp);
            }
            else
                $regemp = "('')";
        }

         $q .= " SELECT  a.fenfac, a.fenfec, {$valorFactura}, a.fensal, a.fenvnd, a.fenvnc, a.fenrbo, a.fenesf, a.fencco, a.fenffa, a.fennit, "
            ."             a.fendpa, a.fennpa, '-' as ingpol, d.empnom, d.emptem, fenhis, fening, a.seguridad "
            ."    FROM  ".$wbasedato."_000018 a, ".$wbasedato."_000024 d "
            ."      WHERE  a.fenfec between '".$wfecini."'"
            ."     AND '".$wfecfin."'"
            ."     AND a.fenval<>0 "
            ."     AND a.fensal != 0 "
            ."     ".$filwesf.""
            ."     AND a.fenres IN ".$regemp." "
            ."     AND a.fenest = 'on' "
            //          ."     AND a.fencod = fenres "      // 2012-08-24
            ."     AND a.fenres=d.empres "
            //          ."     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
            ."     AND fencco<>'' "
            ."     ".$filwcco.""
            ."     GROUP BY a.fenfac "
            ."     ORDER BY  a.fenfac ";
      }
      else
      {
            $q .= " SELECT  a.fenfac, a.fenfec, $valorFactura, a.fensal, a.fenvnd, a.fenvnc, a.fenrbo, a.fenesf, a.fencco, a.fenffa, a.fennit, "
            ."             a.fendpa, a.fennpa, '-' as ingpol, d.empnom, d.emptem, fenhis, fening, a.seguridad "
            ."    FROM  ".$wbasedato."_000018 a, ".$wbasedato."_000024 d "
            ."      WHERE  a.fenfec between '".$wfecini."'"
            ."     AND '".$wfecfin."'"
            ."     AND a.fensal != 0 "
            ."     ".$filwesf.""
            ."     AND a.fentip = '".trim( $exp[1] )."-".trim( $exp[2] )."' "
            ."     AND a.fenest = 'on' "
            ."     AND a.fentip=d.emptem "
            ."     AND empcod != empres "
            ."     AND fenres = empres "
            ."     AND fencod = empcod "
            //          ."     AND a.fencco not in (select ccocod from " . $wbasedato . "_000003 where ccotip='P' and ccoest='on') "
            ."     AND fencco != '' "
            ."     ".$filwcco.""
            ."     ORDER BY  a.fenfac ";
        }
    }
    $err = mysql_query($q,$conex)or die(mysql_error()."-".mysql_errno());;
    //$num = mysql_num_rows($err);

    $q = "SELECT *"
        ."  FROM tmppal";
    $err = mysql_query($q,$conex) or die(mysql_error()."-".mysql_errno());
    $num = mysql_num_rows($err);

    if ($num>0)
    {


        //if($wcco=='Todos')
            $filwcco='';
            /*else
            $filwcco="AND Rencco='".$wcco."'";*/

        $q = "DROP TABLE IF EXISTS tmp1";
        $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());

        //fuentes para notas credito y notas debito
        $qtmp = "CREATE  TABLE IF NOT EXISTS tmp1 " //temporal que almacena los datos de los documentos que pertencen a las facturas que fueron filtradas por fecha
                ."(INDEX idx(Rdefac, Carncr))"
                ." SELECT Renfue, Rennum,Renvca, Rdefac, Carncr, Carndb, Rdevco "
                 ." FROM ".$wbasedato."_000021, ".$wbasedato."_000020, ".$wbasedato."_000040, tmppal"
                ." WHERE Rennum=Rdenum"
                ."   AND Renfue=Rdefue"
                ."   AND Rdefac=fenfac"
                ."   AND Rdeffa=fenffa"
                ."   AND Rdefue= Carfue"
                ."     ".$filwcco.""
                ."   AND Rdecco= Rencco"
                ."   AND Rdeest='on'"
                ."   AND (Carncr='on' or Carndb='on')";
        $rstmp=mysql_query($qtmp, $conex) or die(mysql_error()."-".mysql_errno());

        $q = "DROP TABLE IF EXISTS tmpca";
        $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());

        $qcaus="CREATE TABLE IF NOT EXISTS tmpca " // temporal que almacena las asociaciones de los documentos con sus respectivas causas.
             ."(INDEX idx(Docfue, Docnum))"
             ." SELECT Docfue, Docnum, Doccau, Docest"
             ."   FROM ".$wbasedato."_000071, tmp1"
             ."  WHERE Docfue = Renfue"
             ."    AND Docnum = Rennum"
             ."    AND Docest ='on'";
        $rsca=mysql_query($qcaus, $conex) or die(mysql_error()."-".mysql_errno());

        $q = "DROP TABLE IF EXISTS tmpcato";
        $rq=mysql_query($q, $conex) or die(mysql_error()."-".mysql_errno());

        $qcaus="CREATE TABLE IF NOT EXISTS tmpcato " // temporal que guarda los datos(codigo y nombre) de las causas.
                ."(INDEX idx(Docfue, Docnum))"
                ."SELECT Caucod, Caunom, Docfue, Docnum "
                ."  FROM tmpca, ".$wbasedato."_000072"
                ." WHERE Doccau=Caucod";
        $rsca=mysql_query($qcaus, $conex) or die(mysql_error()."-".mysql_errno());


        for ($i=0; $i<$num; $i++)
        {
            $row = mysql_fetch_array($err);

            $glosa = consultarGlosas( $row['fenfac'], $row['fenffa'] );
            $radicacion = consultarRadicacion( $row['fenfac'], $row['fenffa']);
            $row[13] = consultarPoliza( $row['fenhis'], $row['fening'] );
            $estado = consultarEstado( $row['fenfac'], $row['fenffa'], $row['fenesf']  );

            $vector['numfac'][$i]=$row[0];
            $vector['fecfac'][$i]=$row[1];
            $vector['valfac'][$i]=$row[2];
            $vector['salfac'][$i]=$row[3];
            $vector['debfac'][$i]=$row[4];              //notas debito
            $vector['debfacd'][$i]=array();             //array que almacena el detalle de las notas debito.
            $vector['debdocnums'][$i]='';               //variable que almacenara todas los documentos asociados a la nota dédito como un string
            $vector['debcausas'][$i]='';                //variable que almacenara todas las causas de los documentos asociados a la nota dédito como un string
            $vector['crefac'][$i]=$row[5];              //notas credito
            $vector['credocnums'][$i]='';               //variable que almacenara todas los documentos asociados a la nota crédito como un string
            $vector['crecausas'][$i]='';                //variable que almacenara todas las causas de los documentos asociados a la nota crédito como un string
            $vector['crefacd'][$i]=array();             //array que almacena el detalle de las notas credito.
            $vector['ccofac'][$i]=$row[8];

            //consultar la causa de la N debito o la N credito
            if($vector['crefac'][$i] != 0) //si hay notas credito o notas debito
                {
                  $q_causas=" SELECT Renfue, Rennum,SUM(Rdevco)"
                            ." FROM tmp1"
                            ." WHERE Rdefac='".$row[0]."'"
                            ."   AND Carncr='on'"
                            ." GROUP BY  1, 2";


                  $rs=mysql_query($q_causas, $conex) or die( mysql_error());
                  $num_fuentes=mysql_num_rows($rs);
                  for ($y=0; $y<$num_fuentes; $y++)
                    {
                        $causas="";
                        $causas2="";
                        $row_fuentes = mysql_fetch_array($rs);
                        $vector['crefacd'][$i]['creNum'][$y]=$row_fuentes[0]."-".$row_fuentes[1];

                        // construir el vector de justificaciones.
                        $qCausas="SELECT Caucod, Caunom "
                                ."  FROM tmpcato"
                                ." WHERE Docfue='".$row_fuentes[0]."'"
                                ."   AND Docnum='".$row_fuentes[1]."'"
                                ." GROUP BY 1, 2";
                        $rscaus=mysql_query($qCausas,$conex) or die(mysql_error());
                        $numCaus=mysql_num_rows($rscaus);
                        for($k=0;$k<$numCaus;$k++)
                         {
                            $rowCau=mysql_fetch_array($rscaus);
                            if($k==0){
                                $causas=$rowCau[0]."-".$rowCau[1];
                                $fin1='';
                                if($k==$numCaus-1)
                                    $fin1='|';
                                    $causas2='|'.$rowCau[0]."-".$rowCau[1].$fin1;
                                }else{
                                    $fin='';
                                    if($k==$numCaus-1)
                                     $fin='|';
                                     $causas=$causas."; ".$rowCau[0]."-".$rowCau[1];
                                     $causas2=$causas2."; ".$rowCau[0]."-".$rowCau[1].$fin;
                                    }
                         }
                         //hasta acá va el vector de justificaciones.
                         if($causas==''){
                             $causas='Faltó causa';
                             $causas2='|Faltó causa|';
                            }else{
                                $causas=$causas;
                                $causas2=$causas2;
                                }
                         $vector['crefacd'][$i]['CreJus'][$y]=$causas;
                        //$vector['crefacd'][$i]['CreJus'][$y]=$row_fuentes[0];
                        //$vector['crecausas'][$i].= $causas;

                        if($y==$num_fuentes-1)
                            $coma='|';
                            else
                                $coma='|';
                        $vector['credocnums'][$i].="|".$row_fuentes[0]."-".$row_fuentes[1]."".$coma."";
                        $vector['crecausas'][$i].= $causas2;
                    }
                }else
                    {
                        $vector['crefacd'][$i]['creNum'][0]=" ";
                        $vector['crefacd'][$i]['CreJus'][0]= " ";
                        $vector['crecausas'][$i]="";
                    }
            if($vector['debfac'][$i] != 0) //si hay notas credito o notas debito
            {
              $q_1=" SELECT Renfue, Rennum,SUM(Rdevco) "
                        ." FROM tmp1"
                        ." WHERE Rdefac='".$row[0]."'"
                        ."   AND Carndb='on'"
                        ." GROUP BY 1, 2";

              $rs=mysql_query($q_1, $conex) or die( mysql_error());
              $num_fuentes=mysql_num_rows($rs);

              for ($y=0; $y<$num_fuentes; $y++)
                {
                    $causas="";
                    $causas2="";
                    $row_fuentes = mysql_fetch_array($rs);
                    $vector['debfacd'][$i]['debNum'][$y]=$row_fuentes[0]."-".$row_fuentes[1];
                    // construir el vector de justificaciones.
                    $qCausas="SELECT Caucod, Caunom "
                            ."  FROM tmpcato"
                            ." WHERE Docfue='".$row_fuentes[0]."'"
                            ."   AND Docnum='".$row_fuentes[1]."'"
                            ." GROUP BY 1,2";
                    $rscaus=mysql_query($qCausas,$conex);
                    $numCaus=mysql_num_rows($rscaus);
                    for($k=0;$k<$numCaus;$k++)
                     {
                        $rowCau=mysql_fetch_array($rscaus);
                        if($k==0){
                            $causas=$rowCau[0]."-".$rowCau[1];
                            $fin1='';
                                if($k==$numCaus-1)
                                    $fin1='|';
                                    $causas2='|'.$rowCau[0]."-".$rowCau[1].$fin1;
                            }else
                            {
                                $causas=$causas."; ".$rowCau[0]."-".$rowCau[1];
                                $fin='';
                                if($k==$numCaus-1)
                                    $fin='|';
                                $causas2=$causas2."; ".$rowCau[0]."-".$rowCau[1].$fin;
                            }
                     }
                     //hasta acá va el vector de justificaciones.
                     if($causas==''){
                         $causas='Faltó causa';
                         $causas2='|Faltó causa|';
                        }
                        else{
                          $causas=$causas;
                          $causas2=$causas2;
                        }
                    $vector['debfacd'][$i]['debJus'][$y]=$causas;
                    //$vector['debfacd'][$i]['debJus'][$y]=$row_fuentes[0];

                    $coma='';
                    if($y==$num_fuentes-1)
                        $coma='|';
                    $vector['debdocnums'][$i].="|".$row_fuentes[0]."-".$row_fuentes[1]."".$coma;
                    $vector['debcausas'][$i].=$causas2;
                }
            }else
                    {
                        $vector['debfacd'][$i]['debNum'][0]=" ";
                        $vector['debfacd'][$i]['debJus'][0]= " ";
                        $vector['debcausas'][$i]="";
                    }

            //fin de consulta de causas.
            $vector['recfac'][$i]=$row[6];
            $vector['estfac'][$i]=$row[7];
            //$vector['ccofac'][$i]=$row[8];
            $vector['fuefac'][$i]=$row[9];
            $vector['nitfac'][$i]=$row[10];
            $vector['cedula'][$i]=$row[11];
            $vector['nombre'][$i]=$row[12];
            $vector['poliza'][$i]=$row[13];
            $vector['empfac'][$i]=$row[14];
            $vector['emptip'][$i]=$row[15];
            $aux=explode("-",$row[18]);
            $vector['usergen'][$i]=$aux[1];
            $vector['facffa'][$i]=$row['fenffa'];
            $vector['radnum'][$i]=$radicacion['radnum'];
            $vector['radfec'][$i]=$radicacion['radfec'];
            $vector['glonog'][$i]=$glosa['glonog'];
            $vector['glofec'][$i]=$glosa['glofec'];
            $vector['gloval'][$i]=$glosa['gloval'];
            $vector['gloace'][$i]=$glosa['gloace'];
            $vector['glorec'][$i] = $glosa['gloval'] - $glosa['gloace'];
            $vector['estnum'][$i]=$estado['estnum'];
            $vector['estfec'][$i]=$estado['estfec'];

            if( $vector['glorec'][$i] > $vector['salfac'][$i] ){
                $vector['glorec'][$i] = $vector['salfac'][$i];
            }

            if( $vector['salfac'][$i] < 0){
                $vector['glorec'][$i] = 0;
            }
        }
    }
    else
    {
        $vector['numfac'][0]=false;
    }
    return $vector;
}


/**
 *Consulta el numero y la fecha de envio de una consulta, si no la encuentra, devuelve esos valores como vacios
 *
 * @param unknown_type $wcco: centro de costos de la factura
 * @param unknown_type $wnumfac: numero de la factura
 * @param unknown_type $wfuefac: fuente de la factura
 * @param unknown_type $fuentes: vector que alberga las fuentes para envíos
 * @param unknown_type $wnumEnv: numero del envio
 * @param unknown_type $wfecEnv: fecha del envio
 */
function consultarEnvio($wcco, $wnumfac, $wfuefac, $fuentes, &$wnumEnv, &$wfecEnv)
{
    global $conex;
    global $wbasedato;

    $contador=0; //me indica si encontro el numero de envio para esa factura

    foreach ($fuentes as $cod_fue => $value)
    {
        ////   AND rdecco = '".$wcco."' "
        $q = "  SELECT  rdenum, renfec
                FROM    ".$wbasedato."_000021 AS c21
                        INNER JOIN
                        ".$wbasedato."_000020 AS c20 ON (c20.renfue = c21.rdefue AND c20.rennum = c21.rdenum AND c20.rencco = c21.rdecco AND c20.renest = 'on')
                WHERE   c21.rdefue='".$cod_fue."'

                        AND c21.rdefac = '".$wnumfac."'
                        AND c21.rdeffa = '".$wfuefac."'
                        AND c21.rdeest = 'on'
                ORDER BY c21.rdenum desc";

        $err2 = mysql_query($q,$conex);
        $num2 = mysql_num_rows($err2);

        if ($num2>0)
        {
            $row = mysql_fetch_array($err2);
            $wnumEnv=$row[0];
            $wfecEnv=$row[1];
            $contador=$contador+1;
        }
    }

    if ($contador==0)
    {
        $wnumEnv='';
        $wfecEnv='';
    }

}

function consultarRadicacion($fac, $ffa)
{
    global $wbasedato;
    global $conex;

    $radfec = "";
    $radnnum = "";
    $resrad = array();  //Array a devolver con los datos de la glos

    //Hallando la radicacion y el total aceptado
    //Consulta 15
    $sql = "SELECT  rdevca, carfue, carrad, rdenum, rdefac, rdefue, a.fecha_data "
    . "     FROM  {$wbasedato}_000021 a, {$wbasedato}_000040 "
    . "     WHERE rdefac= '" . $fac . "' "
    . "       AND rdeffa= '" . $ffa . "' "
    . "       AND rdeest = 'on' "
    . "       AND rdefue = carfue "
    . "       AND carrad = 'on'  "
    . "     group by rdenum, rdefue  ";

    $result = mysql_query($sql,$conex) or die( mysql_errno(). " - Error en la consulta 0015 - ".mysql_error() );

    $rows = array();
    for(; $rows = mysql_fetch_array($result);){
        //echo "entro<br>".$rows[0];

        if( $rows['carrad'] == 'on' )
        {
            $radnnum = $rows['carfue'] ."-". $rows['rdenum'];
            $radfec = $rows['fecha_data'];
        }
    }

    $resrad['radfec'] = $radfec;
    $resrad['radnum'] = $radnnum;


    return $resrad;
}


//=================================================================================================================================
include_once("root/comun.php");

if( !isset($plano) )
    $plano = "off";

if( !isset($no_optimizar) )
    $no_optimizar = "off";
@session_start();
if(!isset($_SESSION["user"]))
    echo "error";
else
{
    if(!isset($wemp_pmla)){
        terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
    }

    $key = substr($user,2,strlen($user));

    $conex = obtenerConexionBD("matrix");

    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

    $wbasedato = strtolower($institucion->baseDeDatos);
    $wentidad = $institucion->nombre;

    echo "<form action='rsalenvpru.php' method=post name='forma'>";

    echo "<input type='HIDDEN' NAME= 'wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";

    /**
     * fecha actual para incialización de opciones de fecha
     */
    $wfecha=date("Y-m-d");

    if (!isset($wfecini) or !isset($wfecfin)  or !isset($wemp) or !isset ($resultado))
    {

        encabezado("REPORTE DE SALDOS DE CARTERA A LA FECHA", "2015-11-04", "logo_".$wbasedato );
        echo "<br><br>";

        echo "<center><table>";
        //      echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100></td></tr>";
        //      echo "<tr><td align=center class='titulo1'>REPORTE DE SALDOS DE CARTERA</td></tr>";


        //INGRESO DE VARIABLES PARA EL REPORTE//
        if (!isset ($bandera))
        {
            $wfecini=$wfecha;
            $wfecfin=$wfecha;
        }

        echo "<tr>";
        echo "<td align=center class='fila1'><b>FECHA INICIAL DE FACTURACION: </font></b>";
        campoFechaDefecto("wfecini", $wfecini);
        echo "</td>";
        echo "<td align=center class='fila1'><b>FECHA FINAL DE FACTURACION: </font></b>";
        campoFechaDefecto("wfecfin", $wfecfin);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='fila2'>";
        //SELECCIONAR EMPRESA
        if (isset($wemp) && substr($wemp,0,3) != 'EMP'  )
        {
            echo "<td align=center colspan=2 width='70%'><b>Responsable: <br></b><select name='wemp'>";

            if ($wemp!='% - Todas las empresas')
            {
                $q= "   SELECT count(*) "
                ."     FROM ".$wbasedato."_000024 "
                ."    WHERE empcod = (mid('".$wemp."',1,instr('".$wemp."','-')-1)) "
                ."      AND empcod = empres ";
                $res1 = mysql_query($q,$conex);
                $num1 = mysql_num_rows($res1);
                $row1 = mysql_fetch_array($res1);
            }else
            {
                $row1[0] =1;
            }

            if ($row1[0] > 0 )
            {
                echo "<option selected>".$wemp."</option>";
                if ($wemp!='% - Todas las empresas')
                {
                    echo "<option>% - Todas las empresas</option>";
                }

                $q= "   SELECT count(*) "
                ."     FROM ".$wbasedato."_000024 "
                ."    WHERE empcod != (mid('".$wemp."',1,instr('".$wemp."','-')-1)) "
                ."      AND empcod = empres ";
                $res = mysql_query($q,$conex);
                $num = mysql_num_rows($res);
                $row = mysql_fetch_array($res);
                if ($row[0] > 0)
                {
                    $q= "   SELECT empcod, empnit, empnom "
                    ."     FROM ".$wbasedato."_000024 "
                    ."    WHERE empcod != (mid('".$wemp."',1,instr('".$wemp."','-')-1)) "
                    ."      AND empcod = empres order by 3";
                    $res1 = mysql_query($q,$conex);
                    $num1 = mysql_num_rows($res1);
                    for ($i=1;$i<=$num1;$i++)
                    {
                        $row1 = mysql_fetch_array($res1);
                        echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
                    }
                }
            }

            $q = " SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom "
            . "   FROM " . $wbasedato . "_000024 "
            . "  WHERE empcod != empres "
            . "  GROUP BY emptem "
            . "  ORDER BY empnom ";

            $res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
            $num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());

            for ($i = 0;$i < $num;$i++)
            {
                $row = mysql_fetch_array($res);
                if( "EMP - " . $row[1] . " - " . $row[2] == $wemp ){
                    echo "<option selected>EMP - " . $row[1] . " - " . $row[2] . "</option>";
                }
                else{
                    echo "<option>EMP - " . $row[1] . " - " . $row[2] . "</option>";
                }
            }

            echo "</select></td>";
        }
        else
        {

            echo "<td align=center colspan=2 width='70%' ><b>Responsable: <br></b><select name='wemp'>";

            $q =  " SELECT empcod, empnit, empnom "
            ."   FROM ".$wbasedato."_000024 "
            ."  WHERE empcod = empres "
            ."  ORDER BY empnom ";

            $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
            $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

            echo "<option>% - Todas las empresas</option>";
            for ($i=1;$i<=$num;$i++)
            {
                $row = mysql_fetch_array($res);
                echo "<option>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
            }

            $q = " SELECT SUBSTRING_INDEX( emptem , '-', 1 ) as empcod, SUBSTRING_INDEX( emptem , '-', 1 ) as empnit, SUBSTRING_INDEX( emptem , '-', -1 ) as empnom "
            . "   FROM " . $wbasedato . "_000024 "
            . "  WHERE empcod != empres "
            . "  GROUP BY emptem "
            . "  ORDER BY empnom ";

            $res = mysql_query($q, $conex); // or die (mysql_errno()." - ".mysql_error());
            $num = mysql_num_rows($res); // or die (mysql_errno()." - ".mysql_error());

            for ($i = 0;$i < $num;$i++)
            {
                $row = mysql_fetch_array($res);
                if( "EMP - " . $row[1] . " - " . $row[2] == $wemp ){
                    echo "<option selected>EMP - " . $row[1] . " - " . $row[2] . "</option>";
                }
                else{
                    echo "<option>EMP - " . $row[1] . " - " . $row[2] . "</option>";
                }
            }

            echo "</select></td>";
        }
        echo "</tr>";
            $q =  " SELECT ccocod, ccodes "
            ."   FROM ".$wbasedato."_000003 "
            ."   WHERE ccoest='on'"
            ."  ORDER BY 1 ";
        $res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);


        echo "<tr class='fila1'>";
        /*echo "<td align='center' colspan=1>SELECCIONE LA SUCURSAL: ";
        echo "<select name='wcco'>";
        echo "<option value='Todos'>Todos</option>";
        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($res);
            echo "<option value=".$row[0]."-".$row[1].">".$row[0]."-".$row[1]."</option>";
        }
        echo "</select></td>";*/

        $q =  " SELECT estcod, estdes "
            ."   FROM ".$wbasedato."_000144 "
            ."   WHERE estest='on'"
            ."  ORDER BY 1 ";
        $res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);

        echo "<td align='center' colspan=2>SELECCIONE ESTADO FACTURAS: ";
        echo "<select name='wesf'>";
        echo "<option value='Todos'>Todos</option>";
        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($res);
            echo "<option value=".$row[0]."-".$row[1].">".$row[0]."-".$row[1]."</option>";
        }
        echo "</select></td>";

        echo "</tr>";

        echo "<tr class='fila2'><td align='center' colspan=2><INPUT type='checkbox' name='plano' onChange='javascript:check(this);'>Generar archivo plano</INPUT></td></tr>";

        if(isset($_SESSION['user']) && $_SESSION['user'] == '1-03150')
        {
            echo "<tr class='fila2'><td align='center' colspan=2><INPUT type='checkbox' name='no_optimizar' id='no_optimizar' onChange='javascript:check(this);'>Sin optimizar consultas</INPUT></td></tr>";
        }

        echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

        echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
        echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

        echo "<tr><td align=center class='fila1'COLSPAN='2'><b>";
        echo "<input type='button' style='width:100' name='vol' value='Generar' onclick='Seleccionar()'>&nbsp;&nbsp;&nbsp;<input style='width:100' type=button value='Cerrar' onclick='javascript:window.close();'>";  //submit
        echo "</b></td></tr></table></br>";

    }
    //MUESTRA DE DATOS DEL REPORTE
    else
    {
        if( $plano != "on" || 1){
            $encabezado = "encabezadotabla";
            $filaenc = "fila1";
        }
        else{
            $encabezado = "";
            $filaenc = "";
        }

        if( $plano != "on" || 1)
            encabezado("REPORTE DE SALDOS DE CARTERA A LA FECHA", "Septiembre 11 de 2012", "logo_".$wbasedato );

        echo "<table  align=center width='60%'>";
        // echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
        echo "<tr><td ><B>Reporte generado el: ".date('Y-m-d')."</B></td></tr>";

        // echo "<tr><td><B>REPORTE DE SALDOS DE CARTERA</B></td></tr>";
        echo "</table>";

        echo "<br><table align=center>
            <tr class='$encabezado' align=center>
                <td width=210><b>Fecha Inicial de Facturaci&oacute;n</b></td>
                <td width=210><b>Fecha Final de Facturaci&oacute;n</b></td>
            </tr><tr class='$filaenc' align=center>
                <td>$wfecini</td>
                <td>$wfecfin</td>
            </tr><tr class='$filaenc' align=center>
                <td colspan='2'>Empresa: ".$wemp."</td>
            </tr><tr class='$filaenc' align=center><td colspan='2'>Facturas en estado: ".$wesf."</td>
            </tr></table><br>";

        echo "<table  align=center width='60%'>";
        if( $plano != "on" )echo "<tr><td align=center><A href='rsalenvpru.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wemp=".$wemp."&amp;bandera='1'><span style='font-size:12px;'>VOLVER</span></A></td></tr><tr align='center'><td><input type=button value='Cerrar' style='width:100' onclick='javascript:window.close();'></td></tr>";
        // echo "<tr><td><tr><td>Fecha inicial: ".$wfecini."</td></tr>";
        // echo "<tr><td>Fecha final: ".$wfecfin."</td></tr>";
        echo "</table></br>";

        echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
        echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
        echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
        echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

        /***********************************Consulto lo pedido ********************/

        // si la empresa es diferente a todas las empresas, la meto en el vector solo
        // si es todas las empresas meto todas en un vector para luego preguntarlas en un for

        if ($wemp !='% - Todas las empresas')
        {
            $print=explode('-', $wemp);

            if( trim ($print[0]) != 'EMP' ){
                $wempCod=trim ($print[0]);
            }
            else{
                $wempCod=$wemp;
            }
        }
        else
        {
            $wempCod='';
        }

        $q = " SELECT  carfue "
        ."    FROM  ".$wbasedato."_000040  "
        ."     WHERE  carenv='on' "
        ."     AND carest = 'on' ";

        $err = mysql_query($q,$conex);
        //$num = mysql_num_rows($err);

        while($row = mysql_fetch_array($err))
        {
            $fuentes[$row["carfue"]] = $row["carfue"];
        }

        /*$wcco2=explode("-",$wcco);
        $wcco2=$wcco2[0];*/

        $wesf2=explode("-",$wesf);
        $wesf2=$wesf2[0];

        // Si no_optimizar esta en on significa que se ejecutarán las consultas si haber realizado ningún cambio, tal cual como estaban antes de hace algún tipo de optimización al código
        if($no_optimizar != 'on')
        {
            $vector=consultarFacturasOptima($wfecini, $wfecfin, $wempCod, $wesf2);
            $vect_facturas = count($vector);
            $html = "";

            if ($vect_facturas > 0) // se encontro un vector de facturas
            {
                $total1 = 0;
                $total2 = 0;
                $total3=0;
                $total4=0;
                $total5=0;

                $total6=0;
                $total7=0;
                $total8=0;

                //Creando archivo plano

                $errorfile = false; //indica si hubo error al generar el archivo

                $ruta = "./../../planos/ips/SC.csv";

                if( $plano == "on" ){

                    if( file_exists( $ruta ) ){
                        if( !is_writable($ruta) ){
                            $errorfile = true;
                        }
                    }

                    if( !$errorfile ){
                        if( !$file = fopen( $ruta, "w" ) ){
                            $errorfile = true;
                        }
                    }
                }

                if( $plano == "on" && !$errorfile ){

                    fwrite( $file, "Reporte generado el,".date("Y-m-d")."\r" );
                    fwrite( $file, "Fecha inicial,$wfecini\r" );
                    fwrite( $file, "Fecha final,$wfecfin\r" );
                    fwrite( $file, "Para,$wemp\r\r" );

                    //fwrite( $file, "CENTRO COSTO," );
                    fwrite( $file, "USUARIO QUE FACTURA," );
                    fwrite( $file, "FACTURA," );
                    fwrite( $file, "CEDULA USUARIO," );
                    fwrite( $file, "NOMBRE USUARIO," );
                    fwrite( $file, "FECHA FACTURA," );
                    fwrite( $file, "VALOR FACTURA," );
                    fwrite( $file, "RECIBOS DE CAJA," );
                    fwrite( $file, "NOTAS DEBITO," );
                    //fwrite( $file, "N0S NOTA DEBITO," );
                    //fwrite( $file, "CAUSAS NOTA DEBITO," );
                    fwrite( $file, "NOTAS CREDITO," );
                    //fwrite( $file, "N0S NOTA CREDITO," );
                    //fwrite( $file, "CAUSAS NOTA CREDITO," );
                    fwrite( $file, "SALDO FACTURA," );
                    fwrite( $file, "CARTA DE COBRO," );
                    fwrite( $file, "FECHA CARTA DE COBRO," );
                    fwrite( $file, "ESTADO FACTURA," );

                    fwrite( $file, "No. RADICACION," ); //2013-03-06
                    fwrite( $file, "FECHA DOCUMENTO RADICACION," ); //2013-03-06

                    fwrite( $file, "NO. GLOSA," );
                    fwrite( $file, "FECHA GLOSA," );
                    fwrite( $file, "GLOSA," );
                    fwrite( $file, "ACEPTADO," );
                    fwrite( $file, "RECOBRAR," );
                    fwrite( $file, "NIT," );
                    fwrite( $file, "EMPRESA," );
                    fwrite( $file, "TIPO," );
                    fwrite( $file, "POLIZA\r" );
                }

                if( $plano != "on" ){
                    $html .= "<table align=center width=2550>";
                    $html .= "<tr class='$encabezado'>";
                    //$html .= "<th align=CENTER width=90>CENTRO DE COSTO</th>";
                    $html .= "<th align=CENTER width=90>USUARIO <br>QUE FACTURA</th>";
                    $html .= "<th align=CENTER width=90>FACTURA</th>";
                    $html .= "<th align=CENTER width=90>CEDULA USUARIO</th>";
                    $html .= "<th align=CENTER width=250>NOMBRE USUARIO</th>";
                    $html .= "<th align=CENTER width=90>FECHA FACTURA</th>";
                    $html .= "<th align=CENTER width=100>VALOR FACTURA</th>";
                    $html .= "<th align=CENTER width=100>RECIBOS DE CAJA</th>";
                    $html .= "<th align=CENTER width=100>NOTAS DEBITO</th>";
                    $html .= "<th align=CENTER width=100>No. NOTA DEBITO</th>";
                    $html .= "<th align=CENTER width=100>NOTAS CREDITO</th>";
                    $html .= "<th align=CENTER width=100>No. NOTA CREDITO</th>";
                    $html .= "<th align=CENTER width=100>SALDO FACTURA</th>";
                    $html .= "<th align=CENTER width=100>CARTA DE COBRO</th>";
                    $html .= "<th align=CENTER width=90>FECHA CARTA DE COBRO</th>";
                    $html .= "<th align=CENTER width=100>ESTADO FACTURA</th>";
                    //$html .= "<th align=CENTER width=100>No. DOCUMENTO ACTUAL</th>";
                    //$html .= "<th align=CENTER width=90>FECHA DOCUMENTO ACTUAL</th>";
                    $html .= "<th align=CENTER width=100>No. RADICACION</th>";
                    $html .= "<th align=CENTER width=90>FECHA DOCUMENTO RADICACION</th>";
                    $html .= "<th align=CENTER width=100>No. GLOSA</th>";
                    $html .= "<th align=CENTER width=90>FECHA GLOSA</th>";
                    $html .= "<th align=CENTER width=100>GLOSA</th>";
                    $html .= "<th align=CENTER width=100>ACEPTADO</th>";
                    $html .= "<th align=CENTER width=100>RECOBRAR</th>";
                    $html .= "<th align=CENTER width=100>NIT</th>";
                    $html .= "<th align=CENTER width=250>EMPRESA</th>";
                    $html .= "<th align=CENTER width=200>TIPO</th>";
                    $html .= "<th align=CENTER>POLIZA</th>";
                }

                //$cantidad_vector = count($vector['numfac']);
                $j = 0;
                $ctrl_print = 0;
                foreach ($vector as $num_factura => $info_factura)
                {
                    /*
                        $vector[factura_num] =
                        Array
                        (
                            [numfac] => CS-100051
                            [fecfac] => 2009-07-27
                            [valfac] => 24000
                            [salfac] => 24000
                            [debfac] => 0
                            [debfacd] => Array
                                (
                                    [debNum] => Array
                                        (
                                        )
                                    [debJus] => Array
                                        (
                                        )
                                )
                            [debdocnums] =>
                            [debcausas] =>
                            [crefac] => 0
                            [credocnums] =>
                            [crecausas] =>
                            [crefacd] => Array
                                (
                                    [creNum] => Array
                                        (
                                        )
                                    [CreJus] => Array
                                        (
                                        )
                                )
                            [ccofac] => 2300
                            [recfac] => 0
                            [estfac] => RD
                            [fuefac] => 20
                            [nitfac] => 900156264
                            [cedula] => 3342170
                            [nombre] => PEDRO LUIS PALACIO ESTRADA
                            [poliza] => 0
                            [empfac] => NUEVA EMPRESA PROMOTORA DE SALUD EPS S. A.
                            [emptip] => 21-ENTIDADES OFICIALES
                            [usergen] => 0207790
                            [facffa] => 20
                            [radnum] => 82-2844
                            [radfec] => 2009-08-06
                            [glonog] =>
                            [glofec] =>
                            [gloval] => 0
                            [gloace] => 0
                            [glorec] => 0
                            [estnum] => 82-2844
                            [estfec] => 2009-08-06
                        )
                    */
                    consultarEnvio($info_factura['ccofac'], $info_factura['numfac'], $info_factura['fuefac'], $fuentes, $info_factura['numEnv'],$info_factura['fecEnv']);
                    $nc=count($info_factura['crefacd']['creNum']);//cantidad de notas crédito que tiene la factura.
                    $nd=count($info_factura['debfacd']['debNum']); // cantidad de notas débito que tiene la factura.
                    if($nc>=$nd)
                    {
                        $rowspan="rowspan=".$nc;
                    }
                    else
                    {
                        $rowspan="rowspan=".$nd;
                    }

                    if( $plano != "on" )
                    {
                        if( $plano != "on" || 1 )
                        {
                            if (is_int ($j/2))
                            {
                                $clase="class='texto1'";
                                $clase2="class='texto4'";
                                $clase="class='fila2' style='font-size:10pt;font-weight:normal'";
                                $clase2="class='fila2' style='font-size:10pt;font-weight:normal'";
                            }
                            else
                            {
                                $clase="class='texto2'";
                                $clase2="class='texto5'";
                                $clase="class='fila1' style='font-size:10pt;font-weight:normal'";
                                $clase2="class='fila1' style='font-size:10pt;font-weight:normal'";
                            }
                        }

                        $html .= '<tr>';
                        //echo "<td align=CENTER ".$clase.">".$info_factura['ccofac']."</font></td>";
                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['usergen']."</font></td>";
                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['numfac']."</font></td>";
                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['cedula']."</font></td>";
                        $html .=  "<td align=LEFT width=250 ".$clase.">".cambiarCadena($info_factura['nombre'])."</font></td>";
                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['fecfac']."</font></td>";
                        $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['valfac'],0,'.',',')."</font></td>";
                        $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['recfac'],0,'.',',')."</font></td>";
                        $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['debfac'],0,'.',',')."</font></td>";

                        //detalle de las notas dédito
                        $html .=  "<td  ".$clase." height='100%'><center><table>";
                        foreach($info_factura['debfacd']['debJus'] as $idx_jus => $value_jus)
                        {
                            $html .=  "<tr valing='middle'>";
                             $html .=  "<td align=center ".$clase2." onmouseover=this.style.color='red' onmouseout=this.style.color='black'><span title='".$value_jus."'>".$info_factura['debfacd']['debNum'][$idx_jus]."</font></td>";
                            $html .=  "</tr>";
                        }
                        $html .= "</table></center></td>";
                        //fin detalle de las notad dédito

                        $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['crefac'],0,'.',',')."</font></td>";

                        //detalle de las notas crédito
                        $html .=  "<td  ".$clase." height='100%'><center><table height='100%'>";
                        foreach($info_factura['crefacd']['CreJus'] as $idx_jus => $value_jus)
                        {
                            $html .=  "<tr valing='middle'>";
                             $html .=  "<td align=center ".$clase2." onmouseover=this.style.color='red' onmouseout=this.style.color='black'><span title='".$value_jus."'>".$info_factura['crefacd']['creNum'][$idx_jus]."</font></td>";
                            $html .=  "</tr>";
                        }
                        $html .= "</table></center></td>";
                        //fin detalle de las notas crédito
                    }

                    if( $plano == "on" && !$errorfile ){
                        //fwrite( $file, $info_factura['ccofac']."," );
                        fwrite( $file, $info_factura['usergen']."," );
                        fwrite( $file, $info_factura['numfac']."," );
                        fwrite( $file, $info_factura['cedula']."," );
                        fwrite( $file, utf8_decode(str_replace( ",", " ", $info_factura['nombre'])."," ));
                        fwrite( $file, $info_factura['fecfac']."," );
                        fwrite( $file, number_format($info_factura['valfac'],0,'.','')."," );
                        fwrite( $file, number_format($info_factura['recfac'],0,'.','')."," );
                        fwrite( $file, number_format($info_factura['debfac'],0,'.','')."," );
                        //fwrite( $file, $info_factura['debdocnums'].",");//NOS DE LAS NOTAS DÉBITO
                        //fwrite( $file,$info_factura['debcausas'].",");//CAUSAS DE LAS NOTAS DÉBITO
                        fwrite( $file, number_format($info_factura['crefac'],0,'.','')."," );
                        //fwrite( $file,$info_factura['credocnums'].",");//NOS DE LAS NOTAS CRÉDITO
                        //fwrite( $file,$info_factura['crecausas'].",");//CAUSAS DE LAS NOTAS CRÉDITO

                        if( $info_factura['salfac'] > 0 && $info_factura['salfac'] < 1 ){
                            fwrite( $file, number_format($info_factura['salfac'],2,'.','')."," );
                        }
                        else{
                            fwrite( $file, number_format($info_factura['salfac'],0,'.','')."," );
                        }

                        fwrite( $file, $info_factura['numEnv']."," );
                        fwrite( $file, $info_factura['fecEnv']."," );

                        switch ($info_factura['estfac'])
                        {
                            case 'GE':
                                $estado = "Generada";
                                break;

                            case 'EV':
                                $estado = "Enviada";
                                break;

                            case 'RD':
                                $estado = "Radicada";
                                break;

                            case 'DV':
                                $estado = "Devuelta";
                                break;

                            case 'GL':
                                $estado = "Glosada";
                                break;

                            default:
                                $estado = "";
                                break;
                        }

                        fwrite( $file, $estado."," );

                        fwrite( $file, $info_factura['radnum']."," );//2013-03-06
                        fwrite( $file, $info_factura['radfec']."," );//2013-03-06


                        fwrite( $file, $info_factura['glonog']."," );
                        fwrite( $file, $info_factura['glofec']."," );
                        fwrite( $file, number_format($info_factura['gloval'],0,'.','')."," );
                        fwrite( $file, number_format($info_factura['gloace'],0,'.','')."," );
                        fwrite( $file, number_format($info_factura['glorec'],0,'.','')."," );
                        fwrite( $file, $info_factura['nitfac']."," );
                        fwrite( $file, $info_factura['empfac']."," );
                        fwrite( $file, $info_factura['emptip']."," );

                        if ($info_factura['poliza']==0)
                            fwrite( $file, "\r" );
                        else
                            fwrite( $file, str_replace(","," ",$info_factura['poliza'])."\r" );
                    }

                    if( $plano != "on" ){

                        if( $info_factura['salfac'] > 0 && $info_factura['salfac'] < 1 ){
                            $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['salfac'],2,'.',',')."</font></td>";
                        }
                        else{
                            $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['salfac'],0,'.',',')."</font></td>";
                        }

                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['numEnv']."</td>";
                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['fecEnv']."</td>";

                        switch ($info_factura['estfac'])
                        {
                            case 'GE':
                                $html .=  "<td align=CENTER ".$clase.">Generada</font></td>";
                                break;

                            case 'EV':
                                $html .=  "<td align=CENTER ".$clase.">Enviada</font></td>";
                                break;

                            case 'RD':
                                $html .=  "<td align=CENTER ".$clase.">Radicada</font></td>";
                                break;

                            case 'DV':
                                $html .=  "<td align=CENTER ".$clase.">Devuelta</font></td>";
                                break;

                            case 'GL':
                                $html .=  "<td align=CENTER ".$clase.">Glosada</font></td>";
                                break;

                            default:
                                $html .=  "<td align=CENTER ".$clase.">&nbsp;</font></td>";
                                break;
                        }

                        //$html .=  "<td align=CENTER  ".$clase2.">".$info_factura['estnum']."</font></td>";
                        //$html .=  "<td align=CENTER ".$clase2.">".$info_factura['estfec']."</font></td>";
                        $html .=  "<td align=RIGHT  ".$clase2.">".$info_factura['radnum']."</font></td>";
                        $html .=  "<td align=CENTER ".$clase2.">".$info_factura['radfec']."</font></td>";
                        $html .=  "<td align=RIGHT  ".$clase2.">".$info_factura['glonog']."</font></td>";
                        $html .=  "<td align=CENTER ".$clase2.">".$info_factura['glofec']."</font></td>";
                        $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['gloval'],0,'.',',')."</font></td>";
                        $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['gloace'],0,'.',',')."</font></td>";

                        $html .=  "<td align=RIGHT ".$clase2.">".number_format($info_factura['glorec'],0,'.',',')."</font></td>";

                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['nitfac']."</font></td>";
                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['empfac']."</font></td>";
                        $html .=  "<td align=CENTER ".$clase.">".$info_factura['emptip']."</font></td>";

                        if ($info_factura['poliza']==0)
                        {
                            $html .=  "<td align=CENTER ".$clase."></font></td>";
                        }
                        else
                        {
                            $html .=  "<td align=CENTER ".$clase.">".$info_factura['poliza']."</font></td>";
                        }

                        $html .=  '</tr>';
                    }

                    $total1=$total1+$info_factura['valfac'];
                    $total2=$total2+$info_factura['recfac'];
                    $total3=$total3+$info_factura['debfac'];
                    $total4=$total4+$info_factura['crefac'];
                    $total5=$total5+$info_factura['salfac'];

                    $total6=$total6+$info_factura['gloval'];
                    $total7=$total7+$info_factura['gloace'];
                    $total8=$total8+$info_factura['glorec'];

                    $j++;
                    $ctrl_print++;
                    if($ctrl_print > 1000) // Cantidad aproximadas de filas a concatenar antes de enviarlas a la vista.
                    {
                        echo $html;
                        $html       = "";
                        $ctrl_print = 0;
                    }
                }

                if($html != '')
                {
                    echo $html;
                }

                if( $plano != "on" && !$errorfile ){
                echo "<tr class='$encabezado'>";
                echo "<td align=CENTER colspan=5>TOTALES</font></th>";
                echo "<td align=RIGHT>".number_format($total1,0,'.',',')."</font></td>";
                echo "<td align=RIGHT>".number_format($total2,0,'.',',')."</font></td>";
                echo "<td align=RIGHT>".number_format($total3,0,'.',',')."</font></td>";
                echo "<td align=RIGHT> </font></td>";
                echo "<td align=RIGHT>".number_format($total4,0,'.',',')."</font></td>";
                echo "<td align=RIGHT> </font></td>";
                echo "<td align=RIGHT>".number_format($total5,0,'.',',')."</font></td>";
                echo "<td align=CENTER colspan='5'></th>";
                //echo "<td align=CENTER></th>";
                //echo "<td align=CENTER></th>";
                echo "<td align=CENTER></th>";
                echo "<td align=CENTER></th>";
                echo "<td align=RIGHT>".number_format($total6,0,'.',',')."</font></td>";
                echo "<td align=RIGHT>".number_format($total7,0,'.',',')."</font></td>";
                echo "<td align=RIGHT>".number_format($total8,0,'.',',')."</font></td>";
                echo "<td align=RIGHT colspan='4'></td>";

                echo '</tr>';
                echo "</table>";
                }

                if( $plano == "on" && !$errorfile ){
                    fwrite( $file, "TOTALES,,,,," );
                    fwrite( $file, number_format($total1,0,'.','')."," );
                    fwrite( $file, number_format($total2,0,'.','')."," );
                    fwrite( $file, number_format($total3,0,'.','')."," );
                    //fwrite( $file, ",," );
                    fwrite( $file, number_format($total4,0,'.','')."," );
                    //fwrite( $file, ",,," );
                    fwrite( $file, number_format($total5,0,'.','')."," );
                    fwrite( $file, ",,,,," );
                    fwrite( $file, number_format($total6,0,'.','')."," );
                    fwrite( $file, number_format($total7,0,'.','')."," );
                    fwrite( $file, number_format($total8,0,'.','')."," );
                    fwrite( $file, ",,,\r" );

                    fclose( $file );
                }

            }
            else
            {
                echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
                echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ninguna factura generada en el rango de fechas seleccionado</td><tr>";
                echo "</table>";
            }
        }
        else
        {
            $vector=consultarFacturas($wfecini, $wfecfin, $wempCod, $wesf2); // Esta función tienen la lógica original antes de realizarle modificaciones para mejorar el tiempo de respueta.
            $errorfile = false;

            if ($vector['numfac'][0]!=false) // se encontro un vector de facturas
            {
                $total1 = 0;
                $total2 = 0;
                $total3=0;
                $total4=0;
                $total5=0;

                $total6=0;
                $total7=0;
                $total8=0;

                //Creando archivo plano

                $errorfile = false; //indica si hubo error al generar el archivo

                $ruta = "./../../planos/ips/SC.csv";

                if( $plano == "on" ){

                    if( file_exists( $ruta ) ){
                        if( !is_writable($ruta) ){
                            $errorfile = true;
                        }
                    }

                    if( !$errorfile ){
                        if( !$file = fopen( $ruta, "w" ) ){
                            $errorfile = true;
                        }
                    }
                }

                if( $plano == "on" && !$errorfile ){

                    fwrite( $file, "Reporte generado el,".date("Y-m-d")."\r" );
                    fwrite( $file, "Fecha inicial,$wfecini\r" );
                    fwrite( $file, "Fecha final,$wfecfin\r" );
                    fwrite( $file, "Para,$wemp\r\r" );

                    //fwrite( $file, "CENTRO COSTO," );
                    fwrite( $file, "USUARIO QUE FACTURA," );
                    fwrite( $file, "FACTURA," );
                    fwrite( $file, "CEDULA USUARIO," );
                    fwrite( $file, "NOMBRE USUARIO," );
                    fwrite( $file, "FECHA FACTURA," );
                    fwrite( $file, "VALOR FACTURA," );
                    fwrite( $file, "RECIBOS DE CAJA," );
                    fwrite( $file, "NOTAS DEBITO," );
                    //fwrite( $file, "N0S NOTA DEBITO," );
                    //fwrite( $file, "CAUSAS NOTA DEBITO," );
                    fwrite( $file, "NOTAS CREDITO," );
                    //fwrite( $file, "N0S NOTA CREDITO," );
                    //fwrite( $file, "CAUSAS NOTA CREDITO," );
                    fwrite( $file, "SALDO FACTURA," );
                    fwrite( $file, "CARTA DE COBRO," );
                    fwrite( $file, "FECHA CARTA DE COBRO," );
                    fwrite( $file, "ESTADO FACTURA," );

                    fwrite( $file, "No. RADICACION," ); //2013-03-06
                    fwrite( $file, "FECHA DOCUMENTO RADICACION," ); //2013-03-06

                    fwrite( $file, "NO. GLOSA," );
                    fwrite( $file, "FECHA GLOSA," );
                    fwrite( $file, "GLOSA," );
                    fwrite( $file, "ACEPTADO," );
                    fwrite( $file, "RECOBRAR," );
                    fwrite( $file, "NIT," );
                    fwrite( $file, "EMPRESA," );
                    fwrite( $file, "TIPO," );
                    fwrite( $file, "POLIZA\r" );
                }

                if( $plano != "on" ){
                    echo "<table align=center width=2550>";
                    echo "<tr class='$encabezado'>";
                    //echo "<th align=CENTER width=90>CENTRO DE COSTO</th>";
                    echo "<th align=CENTER width=90>USUARIO <br>QUE FACTURA</th>";
                    echo "<th align=CENTER width=90>FACTURA</th>";
                    echo "<th align=CENTER width=90>CEDULA USUARIO</th>";
                    echo "<th align=CENTER width=250>NOMBRE USUARIO</th>";
                    echo "<th align=CENTER width=90>FECHA FACTURA</th>";
                    echo "<th align=CENTER width=100>VALOR FACTURA</th>";
                    echo "<th align=CENTER width=100>RECIBOS DE CAJA</th>";
                    echo "<th align=CENTER width=100>NOTAS DEBITO</th>";
                    echo "<th align=CENTER width=100>No. NOTA DEBITO</th>";
                    echo "<th align=CENTER width=100>NOTAS CREDITO</th>";
                    echo "<th align=CENTER width=100>No. NOTA CREDITO</th>";
                    echo "<th align=CENTER width=100>SALDO FACTURA</th>";
                    echo "<th align=CENTER width=100>CARTA DE COBRO</th>";
                    echo "<th align=CENTER width=90>FECHA CARTA DE COBRO</th>";
                    echo "<th align=CENTER width=100>ESTADO FACTURA</th>";
                    //echo "<th align=CENTER width=100>No. DOCUMENTO ACTUAL</th>";
                    //echo "<th align=CENTER width=90>FECHA DOCUMENTO ACTUAL</th>";
                    echo "<th align=CENTER width=100>No. RADICACION</th>";
                    echo "<th align=CENTER width=90>FECHA DOCUMENTO RADICACION</th>";
                    echo "<th align=CENTER width=100>No. GLOSA</th>";
                    echo "<th align=CENTER width=90>FECHA GLOSA</th>";
                    echo "<th align=CENTER width=100>GLOSA</th>";
                    echo "<th align=CENTER width=100>ACEPTADO</th>";
                    echo "<th align=CENTER width=100>RECOBRAR</th>";
                    echo "<th align=CENTER width=100>NIT</th>";
                    echo "<th align=CENTER width=250>EMPRESA</th>";
                    echo "<th align=CENTER width=200>TIPO</th>";
                    echo "<th align=CENTER>POLIZA</th>";
                }

                for ($j=0;$j<count($vector['numfac']);$j++)
                {
                    consultarEnvio($vector['ccofac'][$j], $vector['numfac'][$j], $vector['fuefac'][$j], $fuentes, $vector['numEnv'][$j],$vector['fecEnv'][$j]);
                    $nc=count($vector['crefacd'][$j]['creNum']);//cantidad de notas crédito que tiene la factura.
                    $nd=count($vector['debfacd'][$j]['debNum']); // cantidad de notas débito que tiene la factura.
                    if($nc>=$nd)
                        $rowspan="rowspan=".$nc;
                        else
                        $rowspan="rowspan=".$nd;

                    if( $plano != "on" ){
                        if( $plano != "on" || 1 ){
                            if (is_int ($j/2))
                            {
                                $clase="class='texto1'";
                                $clase2="class='texto4'";
                                $clase="class='fila2' style='font-size:10pt;font-weight:normal'";
                                $clase2="class='fila2' style='font-size:10pt;font-weight:normal'";
                            }
                            else
                            {
                                $clase="class='texto2'";
                                $clase2="class='texto5'";
                                $clase="class='fila1' style='font-size:10pt;font-weight:normal'";
                                $clase2="class='fila1' style='font-size:10pt;font-weight:normal'";
                            }
                        }

                        echo '<tr>';
                        //echo "<td align=CENTER ".$clase.">".$vector['ccofac'][$j]."</font></td>";
                        echo "<td align=CENTER ".$clase.">".$vector['usergen'][$j]."</font></td>";
                        echo "<td align=CENTER ".$clase.">".$vector['numfac'][$j]."</font></td>";
                        echo "<td align=CENTER ".$clase.">".$vector['cedula'][$j]."</font></td>";
                        echo "<td align=LEFT width=250 ".$clase.">".cambiarCadena($vector['nombre'][$j])."</font></td>";
                        echo "<td align=CENTER ".$clase.">".$vector['fecfac'][$j]."</font></td>";
                        echo "<td align=RIGHT ".$clase2.">".number_format($vector['valfac'][$j],0,'.',',')."</font></td>";
                        echo "<td align=RIGHT ".$clase2.">".number_format($vector['recfac'][$j],0,'.',',')."</font></td>";
                        echo "<td align=RIGHT ".$clase2.">".number_format($vector['debfac'][$j],0,'.',',')."</font></td>";

                        //detalle de las notad dédito
                        echo "<td  ".$clase." height='100%'><center><table>";
                        for($l=0; $l<$nd; $l++)
                        {
                            echo "<tr valing='middle'>";
                             echo "<td align=center ".$clase2." onmouseover=this.style.color='red' onmouseout=this.style.color='black'><span title='".$vector['debfacd'][$j]['debJus'][$l]."'>".$vector['debfacd'][$j]['debNum'][$l]."</font></td>";
                            echo "</tr>";
                        }
                        echo"</table></center></td>";
                        //fin detalle de las notad dédito

                        echo "<td align=RIGHT ".$clase2.">".number_format($vector['crefac'][$j],0,'.',',')."</font></td>";

                        //detalle de las notas crédito
                        echo "<td  ".$clase." height='100%'><center><table height='100%'>";
                        for($l=0; $l<$nc; $l++)
                        {
                            echo "<tr valing='middle'>";
                             echo "<td align=center ".$clase2." onmouseover=this.style.color='red' onmouseout=this.style.color='black'><span title='".$vector['crefacd'][$j]['CreJus'][$l]."'>".$vector['crefacd'][$j]['creNum'][$l]."</font></td>";
                            echo "</tr>";
                        }
                        echo"</table></center></td>";
                        //fin detalle de las notas crédito
                    }

                    if( $plano == "on" && !$errorfile ){
                        //fwrite( $file, $vector['ccofac'][$j]."," );
                        fwrite( $file, $vector['usergen'][$j]."," );
                        fwrite( $file, $vector['numfac'][$j]."," );
                        fwrite( $file, $vector['cedula'][$j]."," );
                        fwrite( $file, utf8_decode(str_replace( ",", " ", $vector['nombre'][$j])."," ));
                        fwrite( $file, $vector['fecfac'][$j]."," );
                        fwrite( $file, number_format($vector['valfac'][$j],0,'.','')."," );
                        fwrite( $file, number_format($vector['recfac'][$j],0,'.','')."," );
                        fwrite( $file, number_format($vector['debfac'][$j],0,'.','')."," );
                        //fwrite( $file, $vector['debdocnums'][$j].",");//NOS DE LAS NOTAS DÉBITO
                        //fwrite( $file,$vector['debcausas'][$j].",");//CAUSAS DE LAS NOTAS DÉBITO
                        fwrite( $file, number_format($vector['crefac'][$j],0,'.','')."," );
                        //fwrite( $file,$vector['credocnums'][$j].",");//NOS DE LAS NOTAS CRÉDITO
                        //fwrite( $file,$vector['crecausas'][$j].",");//CAUSAS DE LAS NOTAS CRÉDITO

                        if( $vector['salfac'][$j] > 0 && $vector['salfac'][$j] < 1 ){
                            fwrite( $file, number_format($vector['salfac'][$j],2,'.','')."," );
                        }
                        else{
                            fwrite( $file, number_format($vector['salfac'][$j],0,'.','')."," );
                        }

                        fwrite( $file, $vector['numEnv'][$j]."," );
                        fwrite( $file, $vector['fecEnv'][$j]."," );

                        switch ($vector['estfac'][$j])
                        {
                            case 'GE':
                                $estado = "Generada";
                                break;

                            case 'EV':
                                $estado = "Enviada";
                                break;

                            case 'RD':
                                $estado = "Radicada";
                                break;

                            case 'DV':
                                $estado = "Devuelta";
                                break;

                            case 'GL':
                                $estado = "Glosada";
                                break;

                            default:
                                $estado = "";
                                break;
                        }

                        fwrite( $file, $estado."," );

                        fwrite( $file, $vector['radnum'][$j]."," );//2013-03-06
                        fwrite( $file, $vector['radfec'][$j]."," );//2013-03-06


                        fwrite( $file, $vector['glonog'][$j]."," );
                        fwrite( $file, $vector['glofec'][$j]."," );
                        fwrite( $file, number_format($vector['gloval'][$j],0,'.','')."," );
                        fwrite( $file, number_format($vector['gloace'][$j],0,'.','')."," );
                        fwrite( $file, number_format($vector['glorec'][$j],0,'.','')."," );
                        fwrite( $file, $vector['nitfac'][$j]."," );
                        fwrite( $file, $vector['empfac'][$j]."," );
                        fwrite( $file, $vector['emptip'][$j]."," );

                        if ($vector['poliza'][$j]==0)
                            fwrite( $file, "\r" );
                        else
                            fwrite( $file, str_replace(","," ",$vector['poliza'][$j])."\r" );
                    }

                    if( $plano != "on" ){

                        if( $vector['salfac'][$j] > 0 && $vector['salfac'][$j] < 1 ){
                            echo "<td align=RIGHT ".$clase2.">".number_format($vector['salfac'][$j],2,'.',',')."</font></td>";
                        }
                        else{
                            echo "<td align=RIGHT ".$clase2.">".number_format($vector['salfac'][$j],0,'.',',')."</font></td>";
                        }

                        echo "<td align=CENTER ".$clase.">".$vector['numEnv'][$j]."</td>";
                        echo "<td align=CENTER ".$clase.">".$vector['fecEnv'][$j]."</td>";

                        switch ($vector['estfac'][$j])
                        {
                            case 'GE':
                                echo "<td align=CENTER ".$clase.">Generada</font></td>";
                                break;

                            case 'EV':
                                echo "<td align=CENTER ".$clase.">Enviada</font></td>";
                                break;

                            case 'RD':
                                echo "<td align=CENTER ".$clase.">Radicada</font></td>";
                                break;

                            case 'DV':
                                echo "<td align=CENTER ".$clase.">Devuelta</font></td>";
                                break;

                            case 'GL':
                                echo "<td align=CENTER ".$clase.">Glosada</font></td>";
                                break;

                            default:
                                echo "<td align=CENTER ".$clase.">&nbsp;</font></td>";
                                break;
                        }

                        //echo "<td align=CENTER  ".$clase2.">".$vector['estnum'][$j]."</font></td>";
                        //echo "<td align=CENTER ".$clase2.">".$vector['estfec'][$j]."</font></td>";
                        echo "<td align=RIGHT  ".$clase2.">".$vector['radnum'][$j]."</font></td>";
                        echo "<td align=CENTER ".$clase2.">".$vector['radfec'][$j]."</font></td>";
                        echo "<td align=RIGHT  ".$clase2.">".$vector['glonog'][$j]."</font></td>";
                        echo "<td align=CENTER ".$clase2.">".$vector['glofec'][$j]."</font></td>";
                        echo "<td align=RIGHT ".$clase2.">".number_format($vector['gloval'][$j],0,'.',',')."</font></td>";
                        echo "<td align=RIGHT ".$clase2.">".number_format($vector['gloace'][$j],0,'.',',')."</font></td>";

                        echo "<td align=RIGHT ".$clase2.">".number_format($vector['glorec'][$j],0,'.',',')."</font></td>";

                        echo "<td align=CENTER ".$clase.">".$vector['nitfac'][$j]."</font></td>";
                        echo "<td align=CENTER ".$clase.">".$vector['empfac'][$j]."</font></td>";
                        echo "<td align=CENTER ".$clase.">".$vector['emptip'][$j]."</font></td>";

                        if ($vector['poliza'][$j]==0)
                        {
                            echo "<td align=CENTER ".$clase."></font></td>";
                        }
                        else
                        {
                            echo "<td align=CENTER ".$clase.">".$vector['poliza'][$j]."</font></td>";
                        }

                        echo '</tr>';
                    }

                    $total1=$total1+$vector['valfac'][$j];
                    $total2=$total2+$vector['recfac'][$j];
                    $total3=$total3+$vector['debfac'][$j];
                    $total4=$total4+$vector['crefac'][$j];
                    $total5=$total5+$vector['salfac'][$j];

                    $total6=$total6+$vector['gloval'][$j];
                    $total7=$total7+$vector['gloace'][$j];
                    $total8=$total8+$vector['glorec'][$j];

                }
                if( $plano != "on" && !$errorfile ){
                echo "<tr class='$encabezado'>";
                echo "<td align=CENTER colspan=5>TOTALES</font></th>";
                echo "<td align=RIGHT>".number_format($total1,0,'.',',')."</font></td>";
                echo "<td align=RIGHT>".number_format($total2,0,'.',',')."</font></td>";
                echo "<td align=RIGHT>".number_format($total3,0,'.',',')."</font></td>";
                echo "<td align=RIGHT> </font></td>";
                echo "<td align=RIGHT>".number_format($total4,0,'.',',')."</font></td>";
                echo "<td align=RIGHT> </font></td>";
                echo "<td align=RIGHT>".number_format($total5,0,'.',',')."</font></td>";
                echo "<td align=CENTER colspan='5'></th>";
                //echo "<td align=CENTER></th>";
                //echo "<td align=CENTER></th>";
                echo "<td align=CENTER></th>";
                echo "<td align=CENTER></th>";
                echo "<td align=RIGHT>".number_format($total6,0,'.',',')."</font></td>";
                echo "<td align=RIGHT>".number_format($total7,0,'.',',')."</font></td>";
                echo "<td align=RIGHT>".number_format($total8,0,'.',',')."</font></td>";
                echo "<td align=RIGHT colspan='4'></td>";

                echo '</tr>';
                echo "</table>";
                }

                if( $plano == "on" && !$errorfile ){
                    fwrite( $file, "TOTALES,,,,," );
                    fwrite( $file, number_format($total1,0,'.','')."," );
                    fwrite( $file, number_format($total2,0,'.','')."," );
                    fwrite( $file, number_format($total3,0,'.','')."," );
                    //fwrite( $file, ",," );
                    fwrite( $file, number_format($total4,0,'.','')."," );
                    //fwrite( $file, ",,," );
                    fwrite( $file, number_format($total5,0,'.','')."," );
                    fwrite( $file, ",,,,," );
                    fwrite( $file, number_format($total6,0,'.','')."," );
                    fwrite( $file, number_format($total7,0,'.','')."," );
                    fwrite( $file, number_format($total8,0,'.','')."," );
                    fwrite( $file, ",,,\r" );

                    fclose( $file );
                }

            }else
            {
                echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
                echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ninguna factura generada en el rango de fechas seleccionado</td><tr>";
                echo "</table>";
            }
        }

        echo "<input type='HIDDEN' NAME= 'plano' value='".$plano."'>";

        if( $errorfile )
            echo "<p align=center>No se pudo crear el archivo plano</p>";
        else if( $plano == "on" ){
            echo "<p align=center><a href='$ruta'>Descargar Archivo Plano</a></p>";
        }

        if( $plano == "on" ){
            echo "<p align='center'>
            <br><b>Nota:</b> Revise que el archivo plano generado sea correspondiente a su consulta.<br>
            <br>Si dos personas generan el archivo plano al mismo tiempo, prevalece la ultima consulta en ser generada.</p>";
        }
        echo "<br><center><A href='rsalenvpru.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wemp=".$wemp."&amp;bandera='1'><span style='font-size:12px;'>VOLVER</span></A></center>";
        echo "<br><div align='center'><input type=button style='width:100' value='Cerrar' onclick='javascript:window.close();'></div>";
    }

    echo "<script>";
    echo "$.unblockUI();";
    echo "</script>";
}
liberarConexionBD($conex);
?>
</body>
</html>
