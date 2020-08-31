<?php
include_once("conex.php");

echo "listo clisur";
return;


include_once("root/comun.php");

require_once("conex.php");




$q = "SELECT  Rdeffa, Rdefac, Renvca

        FROM  uvglobal_000020 a, uvglobal_000021 b

       WHERE  a.fecha_data = '2013-12-30'
         and  renfue ='30'
         and  Renfue = Rdefue
         and  Rennum = Rdenum
         and  Rdeffa != ''
         and  Rdefac != ''";

$rs = mysql_query( $q, $conex );

$i = 0;

while( $row = mysql_fetch_array( $rs ) ){


$q = " UPDATE uvglobal_000018

              SET fensal = fensal - ".$row['Renvca'].",

                  fenrbo = fenrbo + ".$row['Renvca']."

            WHERE Fenffa = '".$row['Rdeffa']."'

             and Fenfac = '".$row['Rdefac']."'";



  
  
              
    $respuesta = mysql_query( $q, $conex );
    //echo '<br>'.$q;

 $i++;

}

echo "Facturas corregidas: ".$i;

?>
