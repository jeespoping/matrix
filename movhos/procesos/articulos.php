<?php
include_once("conex.php");
/*
 PROGRAMA                   : articulos.php
 AUTOR                      : Sin autor.
 FECHA CREACION             : Sin fecha de creación.

 DESCRIPCIÓN:
 Este programa se encarga de consultar articulos desde unix y actualizarlos en la tabla de articulos de matrix o insertar los que sean nuevos, asi también
 como inactivar los articulos en matrix que ya no se hayan leído desde unix.

 ************************************************************************************************************************************
 * NOTA:
 * - Cualquier modificación en este script debe replicarse en include/movhos/otros.php en la función ivart.
 ************************************************************************************************************************************
 ACTUALIZACIONES:

* Enero 10 de 2017, Edwar Jaramillo:
    -   Se empieza a leer el código CUM para artículos desde unix "Artcum" y se almacena en el nuevo campo Artcum de movhos_000026, este campo inicialmente será
        usado en la generación de archivos planos FURIPS para el área de glosas, pues los artículos se deben generar en el archivo plano solo con códigos CUMS.
    -   Adición al proceso de actialización en unix a la tabla "ivart" para que los insumos que son creados nuevos en unix no queden con los campos "artcge" y "artgen"
        vacíos, este update se estaba haciendo una vez cada 24 horas desde el programa otros.php, por tanto si un artículo era creado nuevo en unix, no pasaba
        a matrix hasta el día siguiente aún cuando se ejecutara la actualización de insumos con el programa articulos.php
    -   Se agrega la función "construirQueryUnix" para hacer de una forma más automática la construcción de UNION cuando se necesitan escapar los valores null en campos unix.

 * Enero 4 de 2017
	Edwin MG		   : Si un articulo no está en UNIX pero se encuentra en matrix, no se desactiva el articulo en matrix.

 *  Marzo 08 de 2013
    Eimer Castro       : Modificación del script en la función ivart() se elimina el punto (.) para artreg en el caso de IS NOT NULL.

*  Marzo 08 de 2013
    Edwar Jaramillo    : Modificación del script para que no se borre siempre la tabla de artículos en matrix sino que valíde si actualiza o inserta.
                            Solo se trunca la tabla si la tabla log (000151) esta vacía o no tienen registros activos relacionados a la carga de artículos.
                            Estos cambios fueron replicados en include/movhos/otros.php : funcion ivart(), cualquier modificación al proceso debe ser replicado
                            en ambos archivos.

 *  Sin fecha de creación
    xxxxxxxxxxxxxxx    : Sin autor de la creación.

*/
$tinicio = time();
$VER_DETALLE = true; // en true para ver con más detalle si se insertaron nuevos, se actualizaron, se inactivaron, o si se presentaron errores.




$conex_o = odbc_connect('inventarios','','');

// $query = "TRUNCATE ".$bd."_000026 "; // bloqueo la tabla
// $err = mysql_query($query,$conex);

// Para generar una carga inicial, o realizar una reconfiguración inicial, se deberían inactivar los registros de LOG Logtem = 'carga_articulos' en la tabla de log
// Esto sería entendido por el programa con una instrucción para truncar la tabla 000026 y generar datos iniciales.
$q_ctrl = "SELECT id FROM ".$bd."_000151 WHERE Logtem = 'carga_articulos' AND Logest = 'on' LIMIT 1"; // Para verificar si es la primera vez que se va a correr el proceso de actualización de articulos.
if($err = mysql_query($q_ctrl,$conex))
{
    // No hay datos de log en la tabla entonces se hace truncate a la tabla 000026 solo por esta vez para cargar los datos iniciales
    // Despues de esto solo se realizarán actualizaciones sobre la tabla 000026.
    if(mysql_num_rows($err) == 0)
    {
        $query = "TRUNCATE ".$bd."_000026 "; // Se limpia la tabla para realizar la carga inicial.
        $err = mysql_query($query,$conex);
    }
}
else
{
    echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".mysql_errno().") ".mysql_error()."</b><br>".$q_ctrl."</font><br>";
    return;
}

$query = "lock table ".$bd."_000026 "; // bloqueo la tabla
$err = mysql_query($query,$conex);

$rr=1;

/*******************************************************************************************************************
 * Actualización (Msanchez): 13-jul-09 ->  Se adicionan a la consulta los campos:  Artpos, Artreg, Artfar y Artubi
 *
 * Articulos del maestro de la tabla movhos_000026
 *******************************************************************************************************************/
// Cuando un artículo se crea nuevo en unix, el campo artcge y artgen quedan vacíos, por eso se ejecutan estos update para llenarlos y
// evitar que el artículo corra el riesgo de no migrar a matrix. El campo artcge se necesita en la función "function ivart(" en el archivo
// include/movhos/otros.php o matrix/movhos/procesos/articulos.php para hacer la importación del artículo de unix hacia matrix.
$query=" UPDATE ivart
            SET artcge = artcod
          WHERE artcge is null ";
$err_o= odbc_do($conex_o,$query)or die (odbc_errormsg());

$query=" UPDATE ivart
            SET artgen = artnom
          WHERE artgen is null ";
$err_o= odbc_do($conex_o,$query)or die (odbc_errormsg());

/*$query= "SELECT artcod,artnom,artgen,artuni,artgru, artact,
                       grunom, artpos,artreg, genfar
                  FROM ivgru,ivart,ivgen
                    WHERE artcge  = gencod
                    AND grucod  = artgru
                    AND artreg is not null

                 UNION

                SELECT artcod,artnom,artgen,artuni,artgru, artact,
                       grunom, artpos,'.' AS artreg, genfar
                  FROM ivgru,ivart,ivgen
                     WHERE artcge = gencod
                       AND grucod = artgru
                       AND artreg is null

                 UNION

                SELECT artcod,artnom,artgen,artuni,artgru, artact,
                       grunom, artpos,'.' AS artreg, '.' AS genfar
                  FROM ivgru,ivart
                     WHERE grucod = artgru
                       AND artreg is null
                       AND artcge NOT IN (SELECT gencod
                                            FROM ivgen)

                 UNION

                SELECT artcod,artnom,artgen,artuni,artgru, artact,
                       grunom, artpos, artreg, '.' AS genfar
                  FROM ivgru,ivart
                     WHERE grucod = artgru
                       AND artreg is not null
                       AND artcge NOT IN (SELECT gencod
                                            FROM ivgen)
                ";*/ // Se comenta este query para crearlo por medio de la función "construirQueryUnix" que automáticamente crea "union" para validar campos con valores NULL

// >>> INICIO CONSTRUCCIÓN QUERY PARA TRAER ARTÍCULOS DE UNIX, VALIDANDO NULL PARA ARTREG Y ARTCUM
$campos        = "artcod,artnom,artgen,artuni,artgru,artact,grunom,artpos,artreg,genfar,artcum";
$campos_nulos  = "artreg,artcum";
$defectoCampos = "'',''";
$tablas        = "ivgru,ivart,ivgen";
$where         = " artcge = gencod
                    AND grucod  = artgru";
$q1            = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );

$campos        = "artcod,artnom,artgen,artuni,artgru,artact,grunom,artpos,artreg,'' AS genfar,artcum";
$campos_nulos  = "artreg,artcum";
$defectoCampos = "'',''";
$tablas        = "ivgru,ivart";
$where         = "grucod = artgru
                  AND artcge NOT IN (SELECT gencod
                                      FROM ivgen)";
$q2            = construirQueryUnix( $tablas,$campos_nulos,$campos,$where, $defectoCampos );

$query = $q1.PHP_EOL.' UNION '.PHP_EOL.$q2;
// <<< FIN CONSTRUCCIÓN QUERY PARA TRAER ARTÍCULOS DE UNIX

$err_o= odbc_do($conex_o,$query);

/**********************************************************************************************************
    Se inserta en una tabla temporal en matrix, los articulos leídos desde unix.
***********************************************************************************************************/
$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];

$nmtemp = $bd."_".date("his");
$filas_unix = "";
$coma = '';
$ctl_lote = 1000;
$lote = $ctl_lote;
$total_lotes = 0;
while(odbc_fetch_row($err_o)) // && $total_lotes < 5000
{
    if(odbc_result($err_o,6) == "S"){
        $est='on';
    } else {
        $est='off';
    }

    if($lote == 0)
    {
        // Inserta lote en temporal
        if(insertarTemporal($conex,$nmtemp,$filas_unix,$total_lotes,'temporal'))
        { }
        $filas_unix = "";
        $lote = $ctl_lote;
        $coma="";
    }

    $artcum = trim(str_replace("\\","\\\\",trim(odbc_result($err_o,'artcum'))));
    $artcum = ($artcum == 'N/A') ? '': $artcum;
    $filas_unix .= $coma." (
                        '".$bd."',
                        '".date('Y-m-d')."',
                        '".date('H:i:s')."',
                        '".odbc_result($err_o,1)."',
                        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,2)))."',
                        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,3)))."',
                        '".odbc_result($err_o,4)."',
                        '".odbc_result($err_o,5)."-".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,7)))."',
                        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,8)))."',
                        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,9)))."',
                        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,10)))."',
                        '".$artcum."',
                        '".$est."',
                        '',
                    'A-".$bd."' )";
    $coma = ',';
    $rr++;
    $total_lotes++;
    $lote--;

    // Esta sección de código era originalmente la manera de cargar los articulos desde unix directamente a matrix
    /*$q = "INSERT INTO ".$bd."_000026(
        Medico,
        Fecha_data,
        Hora_data,
        Artcod,
        Artcom,
        Artgen,
        Artuni,
        Artgru,
        Artpos,
        Artreg,
        Artfar,
        Artest,
        Seguridad
    ) VALUES (
        '".$bd."',
        '".date('Y-m-d')."',
        '".date('H:i:s')."',
        '".odbc_result($err_o,1)."',
        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,2)))."',
        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,3)))."',
        '".odbc_result($err_o,4)."',
        '".odbc_result($err_o,5)."-".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,7)))."',
        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,8)))."',
        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,9)))."',
        '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,10)))."',
        '".$est."',
        'A-movhos' )";

    $err = mysql_query($q,$conex);
    if (($errComun=mysql_error()) != "")
    {
        echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$rr.") ".$errComun."</b><br>".$q."</font><br>";
        $rr++;
    }*/
}

// Puede quedar un resultante de lote menor al valor configurado en el controlador de lote, pero que debe ser insertado, por eso debe inserta el lote faltante.
if($lote >= 0 && $filas_unix != "")
{
    // echo '<br>FALTANTES!!..<br>';
    // Inserta lote en temporal
    if(insertarTemporal($conex,$nmtemp,$filas_unix,$total_lotes,'temporal'))
    { }
    $filas_unix = "";
    echo (!$VER_DETALLE) ? "" : "<br><font color='#57C8D5' face='Arial'>Leídos de unix: ".$total_lotes."</font><br>";
}

/* Si se insertaron datos desde Unix, continúa para realizar la comparación contra articulo en Matrix */
if($total_lotes > 0)
{
    // consultar los articulos en matrix
    $consulta_ok_mx = true;
    $num_row_mx = 0;
    $qmx = "
                SELECT  Artcod,
                        Artcom,
                        Artgen,
                        Artuni,
                        Artgru,
                        Artpos,
                        Artreg,
                        Artfar,
                        Artcum,
                        Artest,
                        Artesm
                FROM    ".$bd."_000026 AS t1";
    if($resmx = mysql_query($qmx,$conex))
    {
        $consulta_ok_mx = true;
        $num_row_mx = mysql_num_rows($resmx);
    }
    else
    {
        $consulta_ok_mx = false;
    }

    // consulta los articulo que fueron guardados desde unix
    $q_un = "
                SELECT  u.Artcod,
                        u.Artcom,
                        u.Artgen,
                        u.Artuni,
                        u.Artgru,
                        u.Artpos,
                        u.Artreg,
                        u.Artfar,
                        u.Artcum,
                        u.Artest,
                        g.id AS grupo_med
                FROM    ".$nmtemp."_arts_unix AS u
                        LEFT JOIN
                        ".$bd."_000066 AS g ON (g.Melgru = SUBSTRING_INDEX(u.Artgru, '-', 1) AND g.Melest = 'on')
                GROUP BY u.Artcod";
    $arr_arts_unix = array(); // Array para guardar articulo de unix
    if($res = mysql_query($q_un,$conex))
    {
        // Si la tabla de articulos en la 000026 de matrix está vacía, entonces todos los articulos que no tengan grupo se marcan como es medicamento en 'off' sino ''
        $no_medicamento = ($num_row_mx == 0) ? 'off': '';
        while($row = mysql_fetch_array($res))
        {
            // if(!array_key_exists($row['Artcod'], $arr_arts_unix))
            // {
            //     $arr_arts_unix[$row['Artcod']] = array();
            // }
            $arr_arts_unix[$row['Artcod']] = array(
                                                'Artcod' => $row['Artcod'],
                                                'Artcom' => $row['Artcom'],
                                                'Artgen' => $row['Artgen'],
                                                'Artuni' => $row['Artuni'],
                                                'Artgru' => $row['Artgru'],
                                                'Artpos' => $row['Artpos'],
                                                'Artreg' => $row['Artreg'],
                                                'Artfar' => $row['Artfar'],
                                                'Artcum' => $row['Artcum'],
                                                'Artest' => $row['Artest'],
                                                'Artesm' => ((trim($row['grupo_med']) != "") ? 'on': $no_medicamento) // Si tiene grupo de medicamento => el articulo es medicamento.
                                                    );
        }

        // consultar los articulos en matrix
        $arr_arts_matrix = array(); // Array para guardar articulo de matrix
        if($consulta_ok_mx)
        {
            while($row = mysql_fetch_array($resmx))
            {
                // if(!array_key_exists($row['Artcod'], $arr_arts_matrix))
                // {
                //     $arr_arts_matrix[$row['Artcod']] = array();
                // }
                $arr_arts_matrix[$row['Artcod']] = array(
                                                    'Artcod' => $row['Artcod'],
                                                    'Artcom' => $row['Artcom'],
                                                    'Artgen' => $row['Artgen'],
                                                    'Artuni' => $row['Artuni'],
                                                    'Artgru' => $row['Artgru'],
                                                    'Artpos' => $row['Artpos'],
                                                    'Artreg' => $row['Artreg'],
                                                    'Artfar' => $row['Artfar'],
                                                    'Artcum' => $row['Artcum'],
                                                    'Artest' => $row['Artest']
                                                        );
            }

            // Si hay articulos en unix, continúa con el proceso.
            if(count($arr_arts_unix)>0)
            {
                $filas_unix = "";
                $coma = '';
                $lote = $ctl_lote;
                $total_lotes = 0;
                $total_updt = 0;
                foreach($arr_arts_unix as $key => $arr_art)
                {
                    $es_medicamento_un = $arr_arts_unix[$key]['Artesm'];
                    unset($arr_arts_unix[$key]['Artesm']);

                    // $es_medicamento_mx = $arr_arts_matrix[$key]['Artesm'];
                    // unset($arr_arts_matrix[$key]['Artesm']);

                    if(!array_key_exists($key, $arr_arts_matrix))
                    {
                        if($lote == 0)
                        {
                            // Inserta lote en la 26
                            if(insertarTemporal($conex,$bd,$filas_unix,$total_lotes))
                            {
                                registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estan en unix y no en matrix. Secuencia lote: $total_lotes");
                            }

                            $filas_unix = "";
                            $lote = $ctl_lote;
                            $coma="";
                        }

                        $filas_unix .= $coma." (
                                        '".$bd."',
                                        '".date('Y-m-d')."',
                                        '".date('H:i:s')."',
                                        '".$arr_arts_unix[$key]['Artcod']."',
                                        '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artcom']))."',
                                        '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artgen']))."',
                                        '".$arr_arts_unix[$key]['Artuni']."',
                                        '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artgru']))."',
                                        '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artpos']))."',
                                        '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artreg']))."',
                                        '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artfar']))."',
                                        '".$arr_arts_unix[$key]['Artcum']."',
                                        '".$arr_arts_unix[$key]['Artest']."',
                                        '".$es_medicamento_un."',
                                    'A-".$bd."' )";
                        $coma = ',';
                        $total_lotes++;
                        $lote--;

                        echo (!$VER_DETALLE) ? "" : "<br>Nuevo en mx: ".$key;
                    }
                    elseif(array_key_exists($key, $arr_arts_matrix) && $arr_arts_matrix[$key] != $arr_arts_unix[$key])
                    {
                        // $es_medicamento = $es_medicamento_un;
                        // if($es_medicamento_un == '' && $es_medicamento_mx != '')
                        // { $es_medicamento = $es_medicamento_mx; }

                        /*  Si al actualizar muchos articulos, el script tiende a tardar, es más por la cantidad de veces que se debe hacer update
                            y no por la cantidad de articulos cargados en los arrays de comparación*/
                        $query = "
                            UPDATE  ".$bd."_000026
                                    SET Artcom = '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artcom']))."',
                                        Artgen = '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artgen']))."',
                                        Artuni = '".$arr_arts_unix[$key]['Artuni']."',
                                        Artgru = '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artgru']))."',
                                        Artpos = '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artpos']))."',
                                        Artreg = '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artreg']))."',
                                        Artfar = '".str_replace("'","\'",str_replace("\\","\\\\",$arr_arts_unix[$key]['Artfar']))."',
                                        Artcum = '".$arr_arts_unix[$key]['Artcum']."',
                                        Artest = '".$arr_arts_unix[$key]['Artest']."'
                            WHERE   Artcod = '".$arr_arts_unix[$key]['Artcod']."'";
                        if(actualizaMatrix($conex,$bd,$query))
                        {
                            $total_updt++;
                            echo (!$VER_DETALLE) ? "" : "<br>actualizó articulo en matrix: ".$arr_arts_unix[$key]['Artcod']."";
                        }
                        else
                        {
                            echo (!$VER_DETALLE) ? "" : "<br><span style='color:red;'>NO</span> actualizó articulo matrix: ".$arr_arts_unix[$key]['Artcod']."";
                        }
                    }

                    if(array_key_exists($key, $arr_arts_matrix))
                    {
                        unset($arr_arts_matrix[$key]); // Los articulos que queden en este array luego de terminar el ciclo, deben cambiar a estado inactivo en matrix.
                    }
                }

                if($lote >= 0 && $filas_unix != "")
                {
                    //echo '<br>FALTANTES!!..<br>';
                    // Inserta lote en la 26
                    if(insertarTemporal($conex,$bd,$filas_unix,$total_lotes))
                    {
                        registrarLog($conex, $bd, $user_session, "insert", "inserta_articulo", "carga_articulos", "", "", "Inserta articulos que estas en unix y no en matrix. Secuencia lote: $total_lotes");
                    }
                    $filas_unix = "";
                }

				//Enero 4 de 2017
				//Se comenta este bloque
				//No se debe desactivar los medicamentos que no se encuentran en unix.
				//Si el medicamento se encuentra en matrix y no se encontró en unix, no se debe tocar el medicamento
                // if (count($arr_arts_matrix) > 0)
                // {
                    // /*Es posible mejorar este array y eliminar los indices cuyos articulos ya estén en estado off para que no tenga que hacer una actualización no necesaria
                    // para esos articulos.*/
                    // $query = "
                        // UPDATE  ".$bd."_000026
                                // SET Artest = 'off'
                        // WHERE   Artcod IN ('".implode("','", array_keys($arr_arts_matrix))."')";
                    // if($res = mysql_query($query,$conex))
                    // {
                        // echo (!$VER_DETALLE) ? "" : "<br><font color='#57C8D5' face='Arial'>(Articulos en Matrix que ya no se leyeron en Unix) Desactivados en matrix: ".count($arr_arts_matrix)."<br>[".implode(",", array_keys($arr_arts_matrix))."]</font>";
                        // registrarLog($conex, $bd, $user_session, "update", "inactiva_articulos", "carga_articulos", "", "", "Actualiza articulos en Matrix a estado off porque ya no aparecen en unix.");
                    // }
                    // else
                    // {
                        // echo (!$VER_DETALLE) ? "" : "<br><span style='color:red;'>NO</span> se pudo desactivar articulos en Matrix que ya no se leyeron en Unix: ".count($arr_arts_matrix)."<br>[".implode(",", array_keys($arr_arts_matrix))."]";
                    // }
                // }

                if($total_lotes>0)
                {
                    echo (!$VER_DETALLE) ? "" : "<br>Articulos nuevos: ".$total_lotes;
                }
                if($total_updt>0)
                {
                    echo (!$VER_DETALLE) ? "" : "<br><font color='#57C8D5' face='Arial'>Articulos actualizados: ".$total_updt."</font>";
                    registrarLog($conex, $bd, $user_session, "update", "actualiza_articulos", "carga_articulos", "", "", "Actualiza articulos en Matrix que esta modificados en unix. Modificados: $total_updt");
                }
            }
        }
        else
        {
            echo (!$VER_DETALLE) ? "" : "<br><font color='red' face='Arial'>No pudo consulta ".$bd."_000026<br>".$qmx."</font>";
        }
    }
    else
    {
        echo (!$VER_DETALLE) ? "" : "<br><font color='#57C8D5' face='Arial'>No pudo consultar temporal de articulos leídos desde unix.<br>".$q_un."</font>";
    }
}


/*******************************************************************************************************************
 * Actualizacion de la ubicacion de cada articulo en el maestro de la tabla movhos_000026
 *******************************************************************************************************************/
$query= "SELECT
            artubiart, artubiubi
        FROM
            ivartubi
        WHERE
            artubiser = '1050'
            AND artubicla = 'P'";

$err_o= odbc_do($conex_o,$query);

while(odbc_fetch_row($err_o))
{
    $q = "UPDATE ".$bd."_000026 SET
            Artubi = '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,2)))."'
        WHERE
            Artcod = '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,1)))."';";

    $err = mysql_query($q,$conex);

    if (($errComun=mysql_error()) != "")
    {
        //echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$rr.") ".$errComun."</b><br>".$q."</font><br>";
        $rr++;
    }
}

/*******************************************************************************************************************
 * Formas farmaceuticas
 *******************************************************************************************************************/
$query = "TRUNCATE ".$bd."_000046 "; //Bloqueo la tabla
$err = mysql_query($query,$conex);

$query = "lock table ".$bd."_000046 "; //Bloqueo la tabla
$err = mysql_query($query,$conex);

$rr=1;
$query= "SELECT
            farcod,farnom
        FROM
            ivfar
        WHERE
            faract = 'S'";

$err_o = odbc_do($conex_o,$query);

while(odbc_fetch_row($err_o)){
    $q = "INSERT INTO ".$bd."_000046(
            Medico,
            Fecha_data,
            Hora_data,
            Ffacod,
            Ffanom,
            Seguridad
        ) VALUES (
            '".$bd."',
            '".date('Y-m-d')."',
            '".date('H:i:s')."',
            '".odbc_result($err_o,1)."',
            '".str_replace("'","\'",str_replace("\\","\\\\",odbc_result($err_o,2)))."',
            'A-movhos'
        );";

    $err = mysql_query($q,$conex);
    if (($errComun=mysql_error()) != "")
    {
        //echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$rr.") ".$errComun."</b><br>".$q."</font><br>";
        $rr++;
    }
}

$query = "unlock tables";
$err = mysql_query($query,$conex);

// Elimina la tabla temporal creada. Esta no se volverá a usar.
$q = "DROP TEMPORARY TABLE IF EXISTS ".$nmtemp."_arts_unix"; //
$result = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());

$tfinal = time();
echo (!$VER_DETALLE) ? "" : '<br><font color="#57C8D5" face="Arial">Termina ejecución: '.date("H:i:s")."</font>";
echo (!$VER_DETALLE) ? "" : '<br><font color="#57C8D5" face="Arial">Tiempo ejecución: '.hace($tinicio)."</font>";

echo "<br><font color='#57C8D5' face='Arial'><b>Tiempo de ejecucion ivart:".(time())."</b></font><br>";

//odbc_close($conex_o);

odbc_close($conex_o);
odbc_close_all();

/**
    insertarTemporal(), esta función se encarga de insertar lotes en la tabla temporal de los datos leidos desde unix, o de insertar los lotes
    de articulos nuevos para insertar en matrix.

    @param link     conex       : link de conexión a la base de datos matrix.
    @param string   bd          : prefijo de la tabla donde se debe insertar.
    @param string   query       : sql del lote de datos que se van a insertar.
    @param int      total_lotes : la cantidad acumulada de articulos que se ha insertado por todos los lotes.
    @param string   tabla       : indica donde se debe insertar el lote, si en la tabla de articulos de matrix o en la tabla temporal de articulos leídos desde unix.
    @return unknown
*/
function insertarTemporal($conex,$bd,$query,$total_lotes,$tabla='000026')
{
    if($tabla == 'temporal')
    {
        // Inserta en la tabla remporal
        $q = "
            CREATE TEMPORARY TABLE IF NOT EXISTS ".$bd."_arts_unix
                 (
                  Medico VARCHAR(8) NOT NULL DEFAULT '',
                  Fecha_data DATE NOT NULL DEFAULT '0000-00-00',
                  Hora_data TIME NOT NULL DEFAULT '00:00:00',
                  Artcod VARCHAR(80) NOT NULL DEFAULT '',
                  Artcom VARCHAR(80) NOT NULL DEFAULT '',
                  Artgen VARCHAR(80) NOT NULL DEFAULT '',
                  Artuni VARCHAR(80) NOT NULL DEFAULT '',
                  Artgru VARCHAR(80) NOT NULL DEFAULT '',
                  Artcum VARCHAR(20) DEFAULT '',
                  Artest CHAR(3) NOT NULL DEFAULT '',
                  Artpos CHAR(3) NOT NULL DEFAULT '',
                  Artreg VARCHAR(80) NOT NULL DEFAULT '',
                  Artfar VARCHAR(4) NOT NULL DEFAULT '',
                  Artubi VARCHAR(80) NOT NULL DEFAULT '',
                  Artesm VARCHAR(80) DEFAULT '',
                  Seguridad VARCHAR(10) NOT NULL DEFAULT '',
                  id BIGINT(20) NOT NULL AUTO_INCREMENT,
                  PRIMARY KEY (id),
                  UNIQUE KEY artcod_idx (Artcod)
                )";
                  //
        if($err = mysql_query($q,$conex))
        {
            //echo "Insertó correctamente en remporal 'arts_unix' (registros: ".($rr).") ";
            $q = "
                INSERT INTO ".$bd."_arts_unix(
                    Medico,
                    Fecha_data,
                    Hora_data,
                    Artcod,
                    Artcom,
                    Artgen,
                    Artuni,
                    Artgru,
                    Artpos,
                    Artreg,
                    Artfar,
                    Artcum,
                    Artest,
                    Artesm,
                    Seguridad
                ) VALUES ".$query.';';
            if($err = mysql_query($q,$conex))
            {
                // echo "<br>Insertó correctamente en temporal 'arts_unix' (registros: $total_lotes)";
                return true;
            }
            else
            {
                // echo "<br>Error al insertar en tabla temporal (registros: $total_lotes).<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
                echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
                return false;
            }
        }
        else
        {
            // echo "<br>Error creando tabla temporal.<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
            echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
            return false;
        }
    }
    elseif($tabla=='000026')
    {
        //Inserta en la 000026
        $q = "
            INSERT INTO ".$bd."_000026(
            Medico,
            Fecha_data,
            Hora_data,
            Artcod,
            Artcom,
            Artgen,
            Artuni,
            Artgru,
            Artpos,
            Artreg,
            Artfar,
            Artcum,
            Artest,
            Artesm,
            Seguridad
        ) VALUES ".$query.';';
        if($err = mysql_query($q,$conex))
        {
            // echo "<br>Insertó correctamente en '000026' (registros: $total_lotes) <br>";
            return true;
        }
        else
        {
            //echo "<br>Error al insertar en tabla '000026 (registros: $total_lotes)'.<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
            echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: ".$total_lotes.") ".mysql_error()."</b><br>".$q."</font><br>";
            return false;
        }
    }
}

function actualizaMatrix($conex,$bd,$query)
{
    //Actualiza en la 000026
    $q = $query;
    if($err = mysql_query($q,$conex))
    {
        // echo "<br>Insertó correctamente en '000026' (registros: $total_lotes) <br>";
        return true;
    }
    else
    {
        //echo "<br>Error al insertar en tabla '000026 (registros: $total_lotes)'.<br><pre>$q</pre>".mysql_errno().'--'.mysql_error();
        echo "<font color='#FF0000' face='Arial'><b>COMUNICAR A SISTEMAS: inconveniente al actualizar articulos.) ".mysql_error()."</b><br>".$q."</font><br>";
        return false;
    }
}

function registrarLog($conex, $bd, $Logres, $Logacc, $Logfrm, $Logtem, $Logerr, $Logsqe, $Logdes)
{
    $q = "
        INSERT INTO ".$bd."_000151(
            Medico,
            Fecha_data,
            Hora_data,
            Logres,
            Logacc,
            Logfrm,
            Logtem,
            Logerr,
            Logsqe,
            Logdes,
            Logest,
            Seguridad)
        VALUES ('".$bd."','".date("Y-m-d")."','".date("H:i:s")."','".$Logres."','".$Logacc."','".$Logfrm."','".$Logtem."','".$Logerr."','".$Logsqe."','".$Logdes."','on','C-".$bd."');";
    if($err = mysql_query($q,$conex))
    {
        // echo "<br>Insertó correctamente en '000026' (registros: $total_lotes) <br>";
    }
    else
    {
        //echo "<font color='#FF0000' face='Arial'><b>Error - guardar en log) ".mysql_error()."</b><br>".$q."</font><br>";
    }
}

function hace($fecha_unix)
{
        //obtener la hora en formato unix
        $ahora=time();

        //obtener la diferencia de segundos
        $segundos=$ahora-$fecha_unix;

        //dias es la division de n segs entre 86400 segundos que representa un dia;
        $dias=floor($segundos/86400);

        //mod_hora es el sobrante, en horas, de la division de días;
        $mod_hora=$segundos%86400;

        //hora es la division entre el sobrante de horas y 3600 segundos que representa una hora;
        $horas=floor($mod_hora/3600);

        //mod_minuto es el sobrante, en minutos, de la division de horas;
        $mod_minuto=$mod_hora%3600;

        //minuto es la division entre el sobrante y 60 segundos que representa un minuto;
        $minutos=floor($mod_minuto/60);

        // falta calcular segundos sobrantes cuando hay más de 60 segundos, p.e. 65sg deberia ser 1min 5sgs

        if($minutos<=0){
                return $segundos." segundos";
        }elseif($horas<=0){
                return $minutos." minutos ".$segundos." segundos";
        }elseif($dias<=0){
                return $horas." horas ".$minutos." minutos ".$segundos." segundos";
        }else{
                return $dias." dias ".$horas." horas ".$minutos." minutos ".$segundos." segundos";
        }
}

//Constructor de Queries UNIX no se pueden mas de 9 campos para verificar si son nulos o no
function construirQueryUnix( $tablas, $campos_nulos, $campos_todos='', $condicionesWhere='',$defecto_campos_nulos=''){

    $condicionesWhere = trim($condicionesWhere);

    if( $campos_nulos == NULL || $campos_nulos == "" ){
        $campos_nulos = array("");
    }

    if( $tablas == "" ){ //Debe existir al menos una tabla
        return false;
    }

    if(gettype($tablas) == "array"){
        $tablas = implode(",",$tablas);
    }

    $pos = strpos($tablas, ",");
    if( $pos !== false && $condicionesWhere == ""){ //Si hay mas de una tabla, debe mandar condicioneswhere
        return false;
    }

    //Si recibe un string, convertirlo a un array
    if( gettype($campos_nulos) == "string" )
        $campos_nulos = explode(",",$campos_nulos);

    $campos_todos_arr = array();

    //Por cual string se reemplazan los campos nulos en el query
    if( $defecto_campos_nulos == "" ){
        $defecto_campos_nulos = array();
        foreach( $campos_nulos as $posxy=>$valorxy ){
            array_push($defecto_campos_nulos, "''");
        }
    }else{
        if(gettype($defecto_campos_nulos) == "string"){
            $defecto_campos_nulos = explode(",",$defecto_campos_nulos);
        }
        if(  count( $defecto_campos_nulos ) == 1 ){ //Significa que todos los campos nulos van a ser reemplazados con el mismo valor
            $defecto_campos_nulos_aux = array();
            foreach( $campos_nulos as $posxyc=>$valorxyc ){
                array_push($defecto_campos_nulos_aux, $defecto_campos_nulos[0]);
            }
            $defecto_campos_nulos = $defecto_campos_nulos_aux;
        }else if(  count( $defecto_campos_nulos ) != count( $campos_nulos ) ){
            return false;
        }
    }

    if( gettype($campos_todos) == "string" ){
        $campos_todos_arr = explode(",",trim($campos_todos));
    }else if(gettype($campos_todos) == "array"){
        $campos_todos_arr = $campos_todos;
        $campos_todos = implode(",",$campos_todos);
    }
    foreach( $campos_todos_arr as $pos22=>$valor ){ //quitar espacios a cada valor
        $campos_todos_arr[$pos22] = trim($valor);
    }
    foreach( $campos_nulos as $pos221=>$valor1 ){ //quitar espacios a cada valor
        $campos_nulos[$pos221] = trim($valor1);

        //Si el campo nulo no existe en el arreglo de todos los campos, agregarlo al final
        $clavex = array_search(trim($valor1), $campos_todos_arr);
        if( $clavex === false ){
            array_push($campos_todos_arr,trim($valor1));
        }
    }
    //Quitar la palabra and, si las condiciones empiezan asi.
    if( substr($condicionesWhere, 0, 3)  == "AND" || substr($condicionesWhere, 0, 3) == "and" ){
        $condicionesWhere = substr($condicionesWhere, 3);
    }
    $condicionesWhere = str_replace("WHERE", "", $condicionesWhere); //Que no tenga la palabra WHERE
    $condicionesWhere = str_replace("where", "", $condicionesWhere); //Que no tenga la palabra WHERE

    $query = "";

    $bits = count( $campos_nulos );
    if( $bits >= 10 ){ //No pueden haber más de 10 campos nulos
        return false;
    }

    if( $bits == 1 && $campos_nulos[0] == "" ){ //retornar el query normal
        $query = "SELECT ".$campos_todos ." FROM ".$tablas;
        if( $condicionesWhere != "" )
            $query.= " WHERE ".$condicionesWhere;
        return $query;
    }

    $max = (1 << $bits);
    $fila_bits = array();
    for ($i = 0; $i < $max; $i++){
        /*-->decbin Entrega el valor binario del decimal $i,
          -->str_pad Rellena el string hasta una longitud $bits con el caracter 0 por la izquierda:
           EJEMPLO $input = "Alien" str_pad($input, 10, "-=", STR_PAD_LEFT);  // produce "-=-=-Alien", rellena por la izquierda hasta juntar 10 caracteres
          -->str_split Convierte un string (el entregado por str_pad) en un array, asi tengo el arreglo con el codigo binario generado
        */
        $campos_todos_arr_copia = array();
        $campos_todos_arr_copia = $campos_todos_arr;

        $fila_bits = str_split( str_pad(decbin($i), $bits, '0', STR_PAD_LEFT) );
        $select = "SELECT ";
        $where = " WHERE ";
        if( $condicionesWhere != "" )
            $where.= $condicionesWhere." AND ";

        for($pos = 0; $pos < count($fila_bits); $pos++ ){
            if($pos!=0) $where.= " AND ";
            if( $fila_bits[$pos] == 0 ){
                $clave = array_search($campos_nulos[$pos], $campos_todos_arr_copia);
                //if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = "'.' as ".$campos_nulos[$pos];
                if( $clave !== false ) $campos_todos_arr_copia[ $clave ] = $defecto_campos_nulos[$pos]." as ".$campos_nulos[$pos];
                $where.= $campos_nulos[$pos]." IS NULL ";
            }else{
                $where.= $campos_nulos[$pos]." IS NOT NULL ";
            }
        }

        $select.= implode(",",$campos_todos_arr_copia);
        $query.= $select." FROM ".$tablas.$where;
        if( ($i+1) < $max ) $query.= " UNION ";
    }
    return $query;
}
?>