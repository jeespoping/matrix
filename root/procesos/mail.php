<?php
include_once("conex.php");
$headers = "Content-type: text/html; charset=iso-8859-1\r\n";
$message="<html>";
$message=$message."<table border=1>".chr(13).chr(10);
$message=$message. "<tr><td rowspan=4 align=center><IMG SRC='http://lasamericas.ath.cx/MATRIX/images/medical/root/americas10.jpg' ></td>".chr(13).chr(10);
$message=$message."<td colspan=4 align=center><font size=5>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>".chr(13).chr(10);
$message=$message. "</table></html>".chr(13).chr(10);
mail("paulomorales@pmamericas.com","Prueba",$message,$headers);
?>