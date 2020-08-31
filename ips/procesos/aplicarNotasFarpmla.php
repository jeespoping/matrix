<html>
<head>
    <title> DEVOLVER CAMBIOS EN FACTURAS DE FARPMLA </title>
</head>
<body>
<?php
include_once("conex.php");
    

    


    $fuentesCartera    = array();
    $facturasAfectadas1 = 0;
    $facturasAfectadas2 = 0;
    $facturasAvaladas1  = 0;
    $facturasAvaladas2 = 0;

    if( $devolver != "on" ){
        $queryfu = " SELECT Carfue
                       FROM farpmla_000040
                      WHERE Carncr='on' or Carrec='on'";
        $rsfue   = mysql_query( $queryfu, $conex ) or die( mysql_error() );
        while( $rowfue = mysql_fetch_assoc( $rsfue ) ){
            array_push( $fuentesCartera, "'".$rowfue['Carfue']."'" );
        }
        $fuentesCartera = implode( ",", $fuentesCartera);
        echo $fuentesCartera;

        $query = "SELECT a.id, a.fensal, a.fenffa, a.fenfac, renfec, fencod, rencod, renfue, Rdevca
                    FROM farpmla_000018 a, farpmla_000021 b,farpmla_000020
                    WHERE a.fenfec between '2000-05-01' AND '2015-05-28'
                      AND rdeffa = fenffa
                      AND rdefac = fenfac
                      AND a.fensal <> 0
                      AND a.fenval <> 0
                      AND a.fenest = 'on'
                      AND a.fencco <> ''
                      AND b.Rdefue IN ( $fuentesCartera )
                      AND renfue = rdefue
                      AND rennum = rdenum
                      AND Rdevca = fensal
                      AND rdeest = 'on'
                      AND renest = 'on'
                      AND fencod != rencod";
        $rs = mysql_query( $query, $conex );
        while( $row = mysql_fetch_assoc( $rs ) ){

                $queryn = " SELECT count(*) movimientosPosteriores
                              FROM farpmla_000021, farpmla_000020
                             WHERE Rdeffa = '{$row['fenffa']}'
                               AND Rdefac = '{$row['fenfac']}'
                               AND Rdefue in ( $fuentesCartera )
                               AND Renfue = rdefue
                               AND rennum = rdenum
                               AND renfec > '{$row[renfec]}'";
                $rsn    = mysql_query( $queryn, $conex );
                $rown   = mysql_fetch_assoc( $rsn );
                if( $rown['movimientosPosteriores'] == 0 ){
                    $facturasAvaladas1++;
                    $qinsert = "INSERT INTO `log_mod_saldos` (`Medico`, `Fecha_data`, `Hora_data`, `regbad`, `regtab`, `regcam`, `regidr`, `regval`, `Seguridad`)
                                                      VALUES ('Farpmla', '".date('Y-m-d')."', '".date("H:i:s")."', 'farmpla', '000018', 'fensal', {$row['id']}, {$row['fensal']}, 'C-farpmla');";
                    $rsinsert = mysql_query( $qinsert, $conex );
                    $qapli = " UPDATE farpmla_000018
                                  SET fensal = fensal - {$row['Rdevca']}
                                WHERE id = '{$row['id']}'";
                    $rsapli = mysql_query( $qapli, $conex );
                    $facturasAfectadas1 += mysql_affected_rows();
                }
        }
        echo "<br> facturas avaladas( ultimo movimiento una nota crédito de igual valor al saldo de la factura, pero que no la afectó)  {$facturasAvaladas1} <br>";
        echo "<br> facturas afectadas {$facturasAfectadas1} <br>";

        $query = "SELECT a.id, a.fensal, a.fenffa, a.fenfac, renfec, fencod, rencod, renfue, Rdevco
                    FROM farpmla_000018 a, farpmla_000021 b,farpmla_000020
                    WHERE a.fenfec between '2000-05-01' AND '2015-05-28'
                      AND rdeffa = fenffa
                      AND rdefac = fenfac
                      AND a.fensal <> 0
                      AND a.fenval <> 0
                      AND a.fenest = 'on'
                      AND a.fencco <> ''
                      AND b.Rdefue IN ( $fuentesCartera )
                      AND renfue = rdefue
                      AND rennum = rdenum
                      AND Rdevco = fensal
                      AND Rdevca = 0
                      AND Rdesfa = 0
                      AND rdeest = 'on'
                      AND renest = 'on'
                      AND fencod != rencod";
        $rs = mysql_query( $query, $conex );
        while( $row = mysql_fetch_assoc( $rs ) ){

                $queryn = " SELECT count(*) movimientosPosteriores
                              FROM farpmla_000021, farpmla_000020
                             WHERE Rdeffa = '{$row['fenffa']}'
                               AND Rdefac = '{$row['fenfac']}'
                               AND Rdefue in ( $fuentesCartera )
                               AND Renfue = rdefue
                               AND rennum = rdenum
                               AND renfec > '{$row[renfec]}'";
                $rsn    = mysql_query( $queryn, $conex );
                $rown   = mysql_fetch_assoc( $rsn );
                if( $rown['movimientosPosteriores'] == 0 ){

                    $facturasAvaladas2++;
                    $qinsert = "INSERT INTO `log_mod_saldos` (`Medico`, `Fecha_data`, `Hora_data`, `regbad`, `regtab`, `regcam`, `regidr`, `regval`, `Seguridad`)
                                                      VALUES ('Farpmla', '".date('Y-m-d')."', '".date("H:i:s")."', 'farmpla', '000018', 'fensal', {$row['id']}, {$row['fensal']}, 'C-farpmla');";
                    $rsinsert = mysql_query( $qinsert, $conex );
                    $qapli = " UPDATE farpmla_000018
                                  SET fensal = fensal - {$row['Rdevco']}
                                WHERE id = '{$row['id']}'";
                    $rsapli = mysql_query( $qapli, $conex );
                    $facturasAfectadas2 += mysql_affected_rows();
                }
        }


        echo "<br> facturas avaladas( ultimo movimiento una nota crédito de igual valor al saldo de la factura, pero que no la afectó)  {$facturasAvaladas2} <br>";
        echo "<br> facturas afectadas {$facturasAfectadas2} <br>";
    }else{
        $facturasDevueltas = 0;
        $query = " SELECT regcam, regval, regidr
                     FROM log_mod_saldos
                    WHERE 1";
        $rs = mysql_query( $query, $conex );
        while ( $row = mysql_fetch_assoc( $rs ) ) {
            $sqrg = " UPDATE farpmla_000018
                         SET {$row['regcam']} = '{$row['regval']}'
                       WHERE id = {$row['regidr']}";
            $rsrg = mysql_query( $sqrg, $conex );
            $facturasDevueltas ++;
        }
        echo "<br> facturas devueltas a su valor original  {$facturasDevueltas} <br>";
    }
?>
</body>
</html>