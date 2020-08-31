<?php
include_once("conex.php");
/*echo "".date("Y-m-d H:i:s").": Inicio migraci&oacute;n<br>";





if(isset($truncar))
{
    $sql = "TRUNCATE TABLE cliame_000252";
    $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
}

$sql = "SELECT DISTINCT(Mpatur)
        FROM cliame_000207
        WHERE Mpaest = 'on'";

$result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
$arr_turnos = array();
while ($row = mysql_fetch_assoc($result))
{
    $turno = $row["Mpatur"];
    $arr_turnos[$turno] = array();
}

$sql = "SELECT Turtur AS Turtur_tmp, Enltur
        FROM    cliame_000207
                INNER JOIN
                tcx_000011 ON Mpatur = Turtur
                INNER JOIN
                cliame_000199 ON Enltur = Turtur AND Enlest = 'on'
        WHERE   Turest = 'on'
                AND Mpaest = 'on'
        GROUP BY Turtur";
$result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
$arr_turnos_liquidados = array();
while ($row = mysql_fetch_assoc($result))
{
    $turno = $row["Turtur_tmp"];
    $arr_turnos_liquidados[$turno] = array();
}

$fecha = date("Y-m-d");
$hora = date("H:i:s");
foreach ($arr_turnos as $turno => $value) {
    $liquidado = (array_key_exists($turno, $arr_turnos_liquidados)) ? 'on': 'off';
    $auditoria = (array_key_exists($turno, $arr_turnos_liquidados)) ? 'on': 'off';
    if($liquidado == 'on')
    {
        $sql = "INSERT INTO cliame_000252   (   Medico,
                                                Fecha_data,
                                                Hora_data,
                                                Auetur,
                                                Aueaud,
                                                Aueliq,
                                                Aueest,
                                                Seguridad)
                                VALUES      (   'cliame',
                                                '{$fecha}',
                                                '{$hora}',
                                                '{$turno}',
                                                '{$auditoria}',
                                                '{$liquidado}',
                                                'on',
                                                'C-cliame') ";
        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
    }
    else
    {
        $sql = "UPDATE tcx_000011 SET Turaud='off' where Turtur = '{$turno}'";
        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
    }
}

echo "".date("Y-m-d H:i:s").': Fin migraci&oacute;n'.PHP_EOL;*/
?>