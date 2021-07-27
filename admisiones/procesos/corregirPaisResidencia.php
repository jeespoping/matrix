<html>
<head>
    <title> CORREGIR PAIS DE NACIMIENTO UNIX</title>
</head>
<body>
<?php
include_once("conex.php");
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];

$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
    

    

    $codigoColombia = consultarAliasPorAplicacion($conex, $wemp_pmla,"codigoColombia");
    $i              = 0;
    $j              = 0;
    $conexunix      = odbc_connect('admisiones','informix','sco') or die("No se realizó Conexion con el Unix");

    $query = " SELECT pactdo, pacdoc, pacpah
                 FROM ".$wcliame."_000100
                WHERE pacpah != '{$codigoColombia}'
                  AND pacpah != ''
                  AND pacpah != '01'";
    $rs    = mysql_query( $query, $conex ) or die( $query );
    while( $row = mysql_fetch_assoc( $rs ) ){
        //echo "<br> ---> ".$row['pactdo']." - ".$row['pacdoc'];
        $i++;
         $q = " UPDATE inpac
                   SET pacmun = '01{$row[pacpah]}'
                 WHERE pactid = '{$row['pactdo']}'
                   AND pacced = '{$row['pacdoc']}'
                   AND pacmun != '01{$row['pacpah']}'";

        /*$q = "  SELECT pachis, pacced, pacmun
                  FROM inpac
                 WHERE pactid = '{$row['pactdo']}'
                   AND pacced = '{$row['pacdoc']}'
                   AND pacmun != '01{$row['pacpah']}'";*/
        $res1 = odbc_do($conexunix,$q);
        $j  += odbc_num_rows($res1);

        /*while(odbc_fetch_row($res)){
            echo "<br>  historia: ".odbc_result( $res, 1)." | cedula: ".odbc_result( $res, 2 )." | lugar de nacimiento: ".odbc_result( $res , 3)." | pais de nacimiento: {$row['pacpah']}";
            $j++;
        }*/

        $q = "  UPDATE inpaci
                   SET pacmun = '01{$row[pacpah]}'
                 WHERE pactid = '{$row['pactdo']}'
                   AND pacced = '{$row['pacdoc']}'
                   AND pacmun != '01{$row['pacpah']}'";

        /*$q = "  SELECT pachis, pacced, pacmun
                  FROM inpaci
                 WHERE pactid = '{$row['pactdo']}'
                   AND pacced = '{$row['pacdoc']}'
                   AND pacmun != '01{$row['pacpah']}'";*/
        $res2 = odbc_do($conexunix,$q);
        $j  += odbc_num_rows($res2);

        /*while(odbc_fetch_row($res)){
            echo "<br>  historia: ".odbc_result( $res, 1)." | cedula: ".odbc_result( $res, 2 )." | lugar de nacimiento: ".odbc_result( $res , 3)." | pais de nacimiento: {$row['pacpah']}";
            $j++;
        }*/
    }
    echo "<br> PACIENTES EXTRANJEROS DE MATRIX: {$i}";
    odbc_close($conexunix);
    odbc_close_all();
?>
</body>
</html>