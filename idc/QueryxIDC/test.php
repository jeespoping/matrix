<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$conexunix = odbc_connect('queryx7', '', '') or die("No se realizó Conexion con Oracle");
$conexunix = odbc_connect('queryx7IDC', '', '') or die("No se realizó Conexion con Oracle");

$SQLQuery = "SELECT pagano, pagmes,  pagtip, pagsec, 
percod, perced, perno1, perno2, perap1, perap2, ofinom,  perbme,
pagfec,  concod, connom , paghor, pagval FROM SRHIDCQ7.nopagcmp INNER JOIN noper ON pagcod = percod INNER JOIN nocon ON PAGCON = concod
INNER JOIN noofi ON oficod = perofi WHERE pagano= 2020 AND pagcod = '00491'
ORDER BY pagano, pagmes,  pagsec;";
//$SQLQuery= "SELECT * FROM noper";
$RecordSet = odbc_exec($conexunix, $SQLQuery);
while (odbc_fetch_row($RecordSet)) {
    $result = odbc_result_all($RecordSet, "border=1");
}
echo "<br><br>"; 

$SQLQuery = "SELECT 
        MAX(CAST(MOVENCANO AS INT)) AS ANO,
        MAX(CAST(MOVENCMES AS INT)) AS MES,
        MOVENCFUE AS COMPROBANTE,
        MOVENCDOC AS DOCUMENTO
        FROM COMOVENC
        WHERE MOVENCANO = 2020
        GROUP BY MOVENCFUE, MOVENCDOC";
$RecordSet = odbc_exec($conexunix, $SQLQuery);
while (odbc_fetch_row($RecordSet)) {
    $result = odbc_result_all($RecordSet, "border=1");
}
odbc_close($conexunix);
?>
