<!--<a href="http://mx.lasamericas.com.co/idc/QueryxIDC/index.php?id=<?php echo base64_encode('1201'); ?>" class="btn btn-warning" > Generar Comprobantes Contables Nomina </a><br>
<a href="http://mx.lasamericas.com.co/idc/QueryxIDC/index.php?id=<?php echo base64_encode('601'); ?>" class="btn btn-success"> Generar Comprobantes Contables Provisiones </a><br>
<a href="http://mx.lasamericas.com.co/idc/QueryxIDC/index.php?id=<?php echo base64_encode('602'); ?>" class="btn btn-primary"> Generar Comprobantes Contables Seguridad Social </a><br>-->
<?php
/**
 * incluye el archivo dSendMail2.inc.php para el envío de correos
 */
include('Classes/dSendMail2/dSendMail2.inc.php');

/*
 * Conexión a Base de Datos por medio de ODBC
 * Desarrollo: Fernando Montes Botero - IDC
 * 2020-02-01
 */
// $conexunix = odbc_connect('queryx7', '', '') or die("No se realizó Conexion con Oracle");
$conexunix = odbc_connect('queryx7IDC', '', '') or die("No se realizó Conexion con Oracle");

/* Variables */
$file_name = null;  // Nombre de Archivo 
$array_files = array(); // Almacenar todos los nombres de los arhivos creados
$date_log = date("Ymd_His"); // Registro de fecha de ejecución.

/* Array que contiene los correos a los que se va enviar el mensaje */
$mailTo = array(); // Enviar correos con información adjunta
$mailTo2 = array(); // Enviar notificación de ejecución para control

$emailFrom = "ofsistemas@idclasamericas.co";
$subject = "Comprobantes Contables - Instituto de Cancerologia S.A.S";
$body = utf8_decode("Cordial saludo,<br><br>
                Adjunto el comprobante contable generado automaticamente.<br><br>
                Para identificar los comprobantes Queryx utiliza la siguiente nomenclatura:<br><br>
                <ul>
                    <li>12.01 - Comprobante de Nomina</li>
                    <li>06.01 - Comprobante de Provisiones</li>
                    <li>06.02 - Comprobante de Seguridad Social</li>
                </ul>
                **************************************************<br><br>
                Este mensaje y cualquier archivo que se adjunte al mismo es para uso exclusivo y podría contener información reservada del Instituto de Cancerología S.A.S Si llegó a usted por error, por favor elimínelo absteniéndose de divulgarlo en cualquier forma y le agradecemos avisar al remitente. Las opiniones personales contenidas en este mensaje no necesariamente coinciden con las del Instituto de Cancerología S.A. Los sistemas del Instituto de Cancerología S.A cuentan con un programa para el control anti-virus, no obstante, el destinatario debe examinar sus mensajes con su programa anti-virus dado que el Instituto de Cancerología S.A. no se hará responsable por daños derivados de la recepción del presente mensaje.");

$body2 = utf8_decode("Se generó y se envió la información correspondiente a los comprobantes contables");

$m = new dSendMail2; // Correo para adjuntar información

/* Enviar notificación con archivos adjuntos */
array_push($mailTo, 'sasalinas@idclasamericas.co'); // Contador -> Sergio Andres Salinas 
array_push($mailTo, 'laalzate@idclasamericas.co'); // Analista de Contabilidad -> Laura Alzate Serna 
array_push($mailTo, 'fmontes@idclasamericas.co'); // Analista de Contabilidad -> Laura Alzate Serna 

/* Enviar notificación de generación */
// array_push($mailTo2, 'ofsistemas@idclasamericas.co'); // Oficina de Sistemas - Usado para verificación del proceso.
array_push($mailTo2, 'fmontes@idclasamericas.co'); // Fernando Montes Botero - Usado para verificación del proceso.

/*
 * Funcion: ConsultarUtlimoComprobante
 * Se consulta el último comprobante contable generado por Queryx.
 * Desarrollo: Fernando Montes Botero - IDC
 * 2020-02-01
 */

function ConsultarUtlimoComprobante($conexunix, $id_comprobante) {
    // Identificar el parametro del comprobante contable a generar
    if (base64_decode($id_comprobante) > 0) {
        $comprobante = base64_decode($id_comprobante);
        switch ($comprobante) {
            case '1201':
                $fuente = '12';
                $documento = '0000001';
                break;
            case '601':
                $fuente = '06';
                $documento = '0000001';
                break;
            case '602':
                $fuente = '06';
                $documento = '0000002';
                break;
            default:
                $fuente = '12';
                $documento = '0000001';
                break;
        }
    }

    $SQLQuery = "SELECT 
        MAX(CAST(MOVENCANO AS INT)) AS ANO,
        MAX(CAST(MOVENCMES AS INT)) AS MES,
        MOVENCFUE AS COMPROBANTE,
        MOVENCDOC AS DOCUMENTO
        FROM COMOVENC
        WHERE MOVENCANO = 2021 AND MOVENCFUE = '" . $fuente . "' AND MOVENCDOC = '" . $documento . "'
        GROUP BY MOVENCFUE, MOVENCDOC";

    $RecordSet = odbc_exec($conexunix, $SQLQuery);
        /*
    $SQLQuery = "SELECT  * FROM 
                SRHIDCQ7.comov 
                WHERE MOVANO = 2020"; 
    echo $SQLQuery;
    while (odbc_fetch_row($RecordSet)) {
        $result = odbc_result_all($RecordSet, "border=1");
    }
    exit; */
    return $RecordSet;
}

/*
 * Funcion: ComprobanteContable
 * Por cada comprobante contable generado, se consulta la información y se consolida agrupando por tercero.
 * Desarrollo: Fernando Montes Botero - IDC
 * 2020-02-01
 */

function ComprobanteContable($conexunix, $ano, $mes, $tipoComprobante, $documento) {
    $SQLQuery = "SELECT TBL.TCCODIGO,
            TBL.COMFECCOM,
            TBL.COMDETALLE,
            TBL.COMNUMDOCU,
            TBL.CUECODIGO,
            TBL.TERNUMDOC,
            TBL.CCCODIGO,
            SUM(TBL.CMMVALDEB) AS CMMVALDEB,
            SUM(TBL.CMMVALCRE) AS CMMVALCRE,
            TBL.COMDETALLED,
            TBL.CRCODIGO,
            TBL.CTCRPORCEN,
            TBL.CTCRVALBAS,
            TBL.CTCRVALFAC
            FROM (
                SELECT 
                MOVFUE as TCCODIGO,
                MOVFEC AS COMFECCOM,
                MOVDES AS COMDETALLE,
                1 AS COMNUMDOCU,
                MOVCUE AS CUECODIGO,
                MOVNIT AS TERNUMDOC,
                MOVIND,
                MOVCCO AS CCCODIGO, 
                CASE 
                    WHEN MOVIND = 1 THEN
                        CAST(REPLACE(MOVVAL,'.00','') AS INT)
                    ELSE 
                        0
                END AS CMMVALDEB,
                 CASE 
                    WHEN MOVIND = 2 THEN
                        CAST(REPLACE(MOVVAL,'.00','') AS INT)
                    ELSE 
                        0
                END AS CMMVALCRE,
                MOVDES AS COMDETALLED,
                0 AS CRCODIGO,
                0 AS CTCRPORCEN,
                0 AS CTCRVALBAS,
                0 AS CTCRVALFAC
                FROM comov 
                WHERE MOVFUE = '" . $tipoComprobante . "' AND 
                MOVANO = " . $ano . " AND
                MOVMES = " . $mes . " AND 
                MOVDOC  = '" . $documento . "' 
            )  TBL 
            GROUP BY TBL.TCCODIGO,
            TBL.COMFECCOM,
            TBL.COMDETALLE,
            TBL.COMNUMDOCU,
            TBL.CUECODIGO,
            TBL.TERNUMDOC, 
            TBL.MOVIND,
            TBL.CCCODIGO,
            TBL.COMDETALLED,
            TBL.CRCODIGO,
            TBL.CTCRPORCEN,
            TBL.CTCRVALBAS,
            TBL.CTCRVALFAC
            ORDER BY TERNUMDOC;";
    $RecordSet = odbc_exec($conexunix, $SQLQuery);
    return $RecordSet;
    //CAST(REPLACE(MOVCCO,'00','0') AS INT) AS CCCODIGO,

// echo $ano . '-' . $mes . '-' . $tipoComprobante;
}

$RecordSet = ConsultarUtlimoComprobante($conexunix, $_GET['id']);
while (odbc_fetch_row($RecordSet)) {
    $result = odbc_result_all($RecordSet, "border=1");
    $item_ano = odbc_result($RecordSet, "ano");
    $item_mes = odbc_result($RecordSet, "mes");
    $item_comprobante = odbc_result($RecordSet, "comprobante");
    $item_documento = odbc_result($RecordSet, "documento");
    /* Consultar Comprobante */

    $file_name = "Comprobantes/ComprobanteContable_" . $item_comprobante . "_" . $item_documento . "_" . $item_ano . "_" . $item_mes . "_" . $date_log . ".csv";
// echo $file_name . '<br>';

    if (!empty($file_name)) {
        array_push($array_files, $file_name);
        $file = fopen($file_name, 'w+');
        $data_csv = "TCCODIGO;COMFECCOM;COMDETALLE;COMNUMDOCU;CUECODIGO;TERNUMDOC;CCCODIGO;CMMVALDEB;CMMVALCRE;COMDETALLED;CRCODIGO;CTCRPORCEN;CTCRVALBAS;CTCRVALFAC\n";
        fwrite($file, $data_csv);

        $RecordSet2 = ComprobanteContable($conexunix, $item_ano, $item_mes, $item_comprobante, $item_documento);
        while (odbc_fetch_row($RecordSet2)) {
// $result2 = odbc_result_all($RecordSet2, "border=1");
            $item_tcccodigo = odbc_result($RecordSet2, "TCCODIGO");
            $item_comfeccom = odbc_result($RecordSet2, "COMFECCOM");
            $item_comdetalle = odbc_result($RecordSet2, "COMDETALLE");
            $item_comnumdocu = odbc_result($RecordSet2, "COMNUMDOCU");
            $item_cuecodigo = odbc_result($RecordSet2, "CUECODIGO");
            $item_ternumdoc = odbc_result($RecordSet2, "TERNUMDOC");
            $item_cccodigo = odbc_result($RecordSet2, "CCCODIGO");
            $item_cmmvaldeb = odbc_result($RecordSet2, "CMMVALDEB");
            $item_cmmvalcre = odbc_result($RecordSet2, "CMMVALCRE");
            $item_comdetalled = odbc_result($RecordSet2, "COMDETALLED");
            $item_crcodigo = odbc_result($RecordSet2, "CRCODIGO");
            $item_ctcrporcen = odbc_result($RecordSet2, "CTCRPORCEN");
            $item_ctcrvalbas = odbc_result($RecordSet2, "CTCRVALBAS");
            $item_ctcrvalfac = odbc_result($RecordSet2, "CTCRVALFAC");

            $data_csv = $item_tcccodigo . ";" .
                    $item_comfeccom . ";" .
                    $item_comdetalle . ";" .
                    $item_comnumdocu . ";" .
                    $item_cuecodigo . ";" .
                    $item_ternumdoc . ";" .
                    $item_cccodigo . ";" .
                    $item_cmmvaldeb . ";" .
                    $item_cmmvalcre . ";" .
                    $item_comdetalled . ";" .
                    $item_crcodigo . ";" .
                    $item_ctcrporcen . ";" .
                    $item_ctcrvalbas . ";" .
                    $item_ctcrvalfac;
            fwrite($file, utf8_decode($data_csv));
            fwrite($file, chr(13) . chr(10));
        }
        fclose($file);
    }
}
odbc_close($conexunix);
// phpinfo();
//exit;
/* Correos a los que se les va a adjuntar la información */
$m->setTo($mailTo);
$m->setFrom($emailFrom);
$m->setSubject($subject);
$m->setMessage($body);

/* Adjuntar los archivos al correo */
foreach ($array_files as $value) {
    /* Obtener contenido de los archivos */
    $contentArchivo = file_get_contents($value);
    /* Adjuntar los archivos al correo */
    $m->autoAttachFile($value, $contentArchivo);
}
$m->sendThroughSMTP('mail.idclasamericas.co', 25, "ofsistemas", "Idc800149026.", false);

/* Envio de correo electrónico */
$m->send(); // Adjuntar información
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        foreach ($array_files as $value) {
            echo "<div><a href='" . $value . "' target='_blank'>" . $value . "</a></div>";
        }
        ?>
    </body>
</html>