<?php
include_once("conex.php");
include_once("root/class.ezpdf.php");
$pdf =& new Cezpdf();
$pdf->selectFont('../../../include/root/fonts/Helvetica.afm');
$pdf->ezText('mondaaaaaaaaaaaaaaaaa!',50);
$pdf->ezStream();
?>