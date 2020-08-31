<?php

include_once("conex.php");
$query = "SELECT *
            FROM farpmla_000244
           WHERE cumcod != ''";
$rs    = mysql_query( $query, $conex );

while( $row = mysql_fetch_assoc( $rs ) ){

    $newCum = ltrim( $row['Cumcod'],  "0" );
    echo "<br> Cumint: ".$row['Cumint']." | Cumcod: ".$row['Cumcod']." | cum limpio: ".$newCum;
    $query2 = "UPDATE farpmla_000244
                 SET cumcod = '{$newCum}'
               WHERE cumint = '{$row['Cumint']}'";
    echo "<br> query: {$query2}";
    $rs2    = mysql_query( $query2, $conex );
}

$query = "SELECT *
            FROM clisur_000244
           WHERE cumcod != ''";
$rs    = mysql_query( $query, $conex );

while( $row = mysql_fetch_assoc( $rs ) ){

    $newCum = ltrim( $row['Cumcod'],  "0" );
    echo "<br> Cumint: ".$row['Cumint']." | Cumcod: ".$row['Cumcod']." | cum limpio: ".$newCum;
    $query2 = "UPDATE clisur_000244
                 SET cumcod = '{$newCum}'
               WHERE cumint = '{$row['Cumint']}'";
    echo "<br> query: {$query2}";
    $rs2    = mysql_query( $query2, $conex );
}

?>