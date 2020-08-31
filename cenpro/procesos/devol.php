<?php
include_once("conex.php");

/**
* 
* @version $Id$
* @copyright 2007
*/





	
$query = "SELECT Appcod, SUM(Appexi) from cenpro_000009 ";
$query .= "    group by Appcod ";

$err1 = mysql_query($query, $conex);
$num1 = mysql_num_rows($err1);

echo "<b>Registros de movimientos : " . $num1 . "</b><br><br>";
for ($i = 0;$i < $num1;$i++)
{
    $row1 = mysql_fetch_array($err1);

    $q = "   UPDATE cenpro_000005 "
     . "      SET karexi = " . $row1[1] . " "
     . "    WHERE  karcod= '" .$row1[0] . "' ";
     
     $res1 = mysql_query($q, $conex) or die (mysql_errno() . " -NO SE HA PODIDO ACTUALIZAR EL INSUMO EN LA TABLA DE SALDOS " . mysql_error());
} 


?>