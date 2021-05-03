<?php
$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$ccoRegistrosMedicos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoRegistrosMedicos');
$pos = strpos($user, "-");
$user = $_SESSION['user'];
$wuser = explode("-", $user);
/****************************************************************************
 * Funciones
 *****************************************************************************/
function consultarDiagnosticosPaciente($historia, $ingreso)
{
    global $conex, $aplicacion, $medicosIngEgr, $wemp_pmla;
    $respuesta = array("diagnosticos" => array(), "medicoIngreso" => "", "medicoDeEgreso" => "");
    $diagnosticos = array();

    $query = " SELECT Detval
                 FROM root_000051
                WHERE Detapl = 'formulariosDiagnosticosIngreso'
                  AND Detemp = '{$wemp_pmla}'";

    $rs = mysql_query($query, $conex);
    $rowFi = mysql_fetch_assoc($rs);
    $formsIng = $rowFi['Detval'];
    $formsIng = explode(",", $formsIng);


    $query = " SELECT Diacod, Diacco, Diausu, Diafhc, Diafor, id
                 FROM {$aplicacion}_000272
                WHERE Diahis = '{$historia}'
                  AND Diaing = '{$ingreso}'
                  AND Diaest = 'on'
                 ORDER BY id asc";

    $rs = mysql_query($query, $conex);
    while ($row = mysql_fetch_assoc($rs)) {
        if (!isset($respuesta['diagnosticos'][$row['Diacod']])) {
            $rsNombre = mysql_fetch_assoc(consultaNombreImpDiag($row['Diacod']));
            $respuesta['diagnosticos'][$row['Diacod']] = array(
                "descripcion" => $rsNombre['Descripcion'],
                "centroCostos" => $row['Diacco'],
                "medico" => $row['Diausu'],
                "notificar" => $rsNombre['Notificar']
            );
            if ($respuesta['medicoIngreso'] == "" and in_array($row['Diafor'], $formsIng)) {
                $respuesta['medicoIngreso'] = $row['Diausu'];
            }
        }
        $respuesta['medicoDeEgreso'] = $row['Diausu'];
    }
    return ($respuesta);
}


function consultaMaestros($tabla, $campos, $where, $group, $order, $cant = 1)
{
    global $conex;
    global $wbasedato;
    global $prueba;


    if ($cant == 1) {
        $q = " SELECT " . $campos . "
                    FROM " . $tabla . "";
        if ($where != "") {
            $q .= " WHERE " . $where . "";
        }
    } else {

        $q = " SELECT " . $campos . "
                FROM " . $wbasedato . "_" . $tabla . "";
        if ($where != "") {
            $q .= " WHERE " . $where . "";
        }
    }

    if ($group != "") {
        $q .= "   GROUP BY " . $group . " ";
    }
    if ($order != "") {
        $q .= " ORDER BY " . $order . " ";
    }

    $res1 = mysql_query($q, $conex) or die(" Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
    // $num1 = mysql_num_rows($res1);

    return $res1;
}


/**********************************************************************************
 * Crea un select con el id y name
 **********************************************************************************/
function crearSelectHTMLAcc($res, $id, $name, $style = "", $atributos = "")
{

    $select = "<SELECT id='$id' name='$name' $atributos $style>";
    $select .= "<option value=''>Seleccione...</option>";

    $num = mysql_num_rows($res);

    if ($num > 0) {

        while ($rows = mysql_fetch_assoc($res)) {

            $value = "";
            $des = "";

            $i = 0;
            foreach ($rows as $key => $val) {

                if ($i == 0) {
                    $value = $val;
                } else {
                    $des .= "-" . $val;
                }

                $i++;
            }

            $select .= "<option value='{$value}'>" . substr($des, 1) . "</option>";
        }
    }

    $select .= "</SELECT>";

    return $select;
}


function consultarAplicacion($conexion, $codigoInstitucion, $nombreAplicacion)
{
    $q = " SELECT Detval
             FROM root_000051
            WHERE Detemp = '" . $codigoInstitucion . "'
              AND Detapl = '" . $nombreAplicacion . "'";

    $res = mysql_query($q, $conexion) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
    $num = mysql_num_rows($res);

    $alias = "";
    if ($num > 0) {
        $rs = mysql_fetch_array($res);
        $alias = $rs['Detval'];
    }
    return $alias;
}

function consultarCC($alias, $where)
{

    global $conex;

    $q = " SELECT Ccocod,Cconom, Ccorel
            FROM " . $alias . "_000011
            WHERE " . $where . "
            order by Cconom";


    $res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

    return $res;
}


function consultarMedicos($med, $wbasedato, $aplicacion, $especialidad = "")
{

    global $conex;


    $val = "";
    $data = "";

    if ($aplicacion == "") {
        //medico
        $and_especialidad = "";
        if ($especialidad != "")
            $and_especialidad = " AND Espcod = '" . $especialidad . "'";

        $sql = "SELECT Medcod, Mednom,Medesp,Espnom
                    FROM " . $wbasedato . "_000051 LEFT JOIN " . $wbasedato . "_000053 ON (Medesp=Espcod)
                    WHERE (Medcod LIKE '%" . utf8_decode($med) . "%' or Mednom like '%" . utf8_decode($med) . "%')
                    " . $and_especialidad . "
                    AND Medest ='on'
                    ORDER BY Mednom
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Medcod'] = trim(utf8_encode($rows['Medcod']));
                $rows['Mednom'] = trim(utf8_encode($rows['Mednom']));
                $rows['Medesp'] = trim(utf8_encode($rows['Medesp']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                $pos = strpos($rows['Medesp'], "-");
                if ($pos !== false) {
                    $aux = explode("-", $rows['Medesp']);
                    $rows['Medesp'] = $aux[0];
                    $rows['Espnom'] = $aux[1];
                }

                if ($rows['Medesp'] == "") $rows['Espnom'] = "00000";
                if ($rows['Espnom'] == "") $rows['Espnom'] = "SIN DATOS";

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Medcod'], "des" => $rows['Mednom'], "codesp" => $rows['Medesp'], "desesp" => $rows['Espnom']);    //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows['Medcod']}-{$rows['Mednom']}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";
            }
        }
    } else {

        $med = str_replace(" ", ".*", $med);

        $and_especialidad = "";
        if ($especialidad != "")
            $and_especialidad = " AND Espcod = '" . $especialidad . "'";

        //WHERE (Medno1 LIKE '%".utf8_decode($med)."%' or Meddoc LIKE '%".utf8_decode($med)."%' or Medno2 like '%".utf8_decode($med)."%' or Medap1 LIKE '%".utf8_decode($med)."%' or Medap2 like '%".utf8_decode($med)."%')

        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2,Medesp,Espnom
                    FROM " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
                    WHERE ( concat(Medno1,' ',Medno2,' ',Medap1,' ',Medap2) regexp '" . utf8_decode($med) . "' or Meddoc LIKE '%" . utf8_decode($med) . "%' )
                    " . $and_especialidad . "
                    AND Medest ='on'
                    ORDER BY Medno1,Medno2,Medap1,Medap2
                    LIMIT 30
                    "; //AND Meduma != ''

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);
        if (!isset($data) or trim($data) == "")
            $data = array();

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Meddoc'] = trim(utf8_encode($rows['Meddoc']));
                $rows['Medno1'] = trim(utf8_encode($rows['Medno1']));
                $rows['Medno2'] = trim(utf8_encode($rows['Medno2']));
                $rows['Medap1'] = trim(utf8_encode($rows['Medap1']));
                $rows['Medap2'] = trim(utf8_encode($rows['Medap2']));
                $rows['Medesp'] = trim(utf8_encode($rows['Medesp']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                $pos = strpos($rows['Medesp'], "-");
                if ($pos !== false) {
                    $aux = explode("-", $rows['Medesp']);
                    $rows['Medesp'] = $aux[0];
                    $rows['Espnom'] = $aux[1];
                }

                if ($rows['Medesp'] == "") $rows['Espnom'] = "00000";
                if ($rows['Espnom'] == "") $rows['Espnom'] = "SIN DATOS";

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array(
                    "cod" => $rows['Meddoc'],
                    "des" => $rows['Medno1'] . " " . $rows['Medno2'] . " " . $rows['Medap1'] . " " . $rows['Medap2'],
                    "codesp" => $rows['Medesp'],
                    "desesp" => $rows['Espnom']
                );  //Este es el dato a procesar en javascript
                $data['usu'] = $rows['Medno1'] . " " . $rows['Medno2'] . " " . $rows['Medap1'] . " " . $rows['Medap2'];   //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";
            }
        }
    }

    return $val;
}

function consultarMedicoEspecifico($med, $wbasedato, $aplicacion, $especialidad = "")
{

    global $conex;


    $val = "";
    $data = "";

    if ($aplicacion == "") {
        //medico
        $and_especialidad = "";
        if ($especialidad != "")
            $and_especialidad = " AND Espcod = '" . $especialidad . "'";

        $sql = "SELECT Medcod, Mednom,Medesp,Espnom
                    FROM " . $wbasedato . "_000051 LEFT JOIN " . $wbasedato . "_000053 ON (Medesp=Espcod)
                    WHERE Medcod = '" . utf8_decode($med) . "'
                    " . $and_especialidad . "
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Medcod'] = trim(utf8_encode($rows['Medcod']));
                $rows['Mednom'] = trim(utf8_encode($rows['Mednom']));
                $rows['Medesp'] = trim(utf8_encode($rows['Medesp']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                $pos = strpos($rows['Medesp'], "-");
                if ($pos !== false) {
                    $aux = explode("-", $rows['Medesp']);
                    $rows['Medesp'] = $aux[0];
                    $rows['Espnom'] = $aux[1];
                }

                if ($rows['Medesp'] == "") $rows['Espnom'] = "00000";
                if ($rows['Espnom'] == "") $rows['Espnom'] = "SIN DATOS";

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Medcod'], "des" => $rows['Mednom'], "codesp" => $rows['Medesp'], "desesp" => $rows['Espnom']);    //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows['Medcod']}-{$rows['Mednom']}"; //Este es el que ve el usuario
            }
        }
    } else {
        //medico
        $and_especialidad = "";
        if ($especialidad != "")
            $and_especialidad = " AND Espcod = '" . $especialidad . "'";

        if (!isset($data) or trim($data) == "")
            $data = array();

        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2,Medesp,Espnom
                    FROM " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
                    WHERE Meddoc = '" . utf8_decode($med) . "'
                    " . $and_especialidad . "
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Meddoc'] = trim(utf8_encode($rows['Meddoc']));
                $rows['Medno1'] = trim(utf8_encode($rows['Medno1']));
                $rows['Medno2'] = trim(utf8_encode($rows['Medno2']));
                $rows['Medap1'] = trim(utf8_encode($rows['Medap1']));
                $rows['Medap2'] = trim(utf8_encode($rows['Medap2']));
                $rows['Medesp'] = trim(utf8_encode($rows['Medesp']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                $pos = strpos($rows['Medesp'], "-");
                if ($pos !== false) {
                    $aux = explode("-", $rows['Medesp']);
                    $rows['Medesp'] = $aux[0];
                    $rows['Espnom'] = $aux[1];
                }

                if ($rows['Medesp'] == "") $rows['Espnom'] = "00000";
                if ($rows['Espnom'] == "") $rows['Espnom'] = "SIN DATOS";

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array(
                    "cod" => $rows['Meddoc'],
                    "des" => $rows['Medno1'] . " " . $rows['Medno2'] . " " . $rows['Medap1'] . " " . $rows['Medap2'],
                    "codesp" => $rows['Medesp'],
                    "desesp" => $rows['Espnom']
                );  //Este es el dato a procesar en javascript
                $data['usu'] = $rows['Medno1'] . " " . $rows['Medno2'] . " " . $rows['Medap1'] . " " . $rows['Medap2'];   //Este es el que ve el usuario
            }
        }
    }

    return $data;
}

function consultarDiagnosticos($diag)
{

    global $conex;
    global $sexoPaciente;


    $val = "";

    $condicionSexo = ($sexoPaciente == "M" or $sexoPaciente == "F") ? " AND ( Sexo = 'A' or Sexo = '{$sexoPaciente}' ) " : "";
    //Diagnostico
    $sql = "SELECT Codigo, Descripcion
                FROM root_000011
                WHERE (Descripcion LIKE '%" . utf8_decode($diag) . "%' or Codigo like '%" . utf8_decode($diag) . "%') {$condicionSexo}
                  AND estado = 'on'
                ORDER BY Descripcion
                LIMIT 30
                ";

    $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0) {

        for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

            $rows['Codigo'] = trim(utf8_encode($rows['Codigo']));
            $rows['Descripcion'] = trim(utf8_encode($rows['Descripcion']));

            //Creo el resultado como un json
            //Primero creo un array con los valores necesarios
            $data['valor'] = array("cod" => $rows['Codigo'], "des" => $rows['Descripcion']);  //Este es el dato a procesar en javascript
            $data['usu'] = "{$rows['Codigo']}-{$rows['Descripcion']}";    //Este es el que ve el usuario
            $dat = array();
            $dat[] = $data;

            $val .= json_encode($dat) . "\n";
        }
    }

    return $val;
}

function consultarProcedimientos($proc, $wbasedato)
{

    global $conex;


    $val = "";

    //Diagnostico
    $sql = "SELECT Procod,Pronom,Procup
                FROM " . $wbasedato . "_000103
                WHERE (Pronom LIKE '%" . utf8_decode($proc) . "%' or Procod like '%" . utf8_decode($proc) . "%')
                AND Proest = 'on'
                AND char_length(Procod) >= 6
                ORDER BY Pronom
                LIMIT 30
                ";

    $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0) {

        for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

            $rows['Procod'] = trim(utf8_encode($rows['Procod']));
            $rows['Pronom'] = trim(utf8_encode($rows['Pronom']));

            //Creo el resultado como un json
            //Primero creo un array con los valores necesarios
            $data['valor'] = array("cod" => $rows['Procod'], "des" => $rows['Pronom']);   //Este es el dato a procesar en javascript
            $data['usu'] = "{$rows['Procod']}-{$rows['Pronom']}"; //Este es el que ve el usuario
            $dat = array();
            $dat[] = $data;

            $val .= json_encode($dat) . "\n";
        }
    }

    return $val;
}

function consultarEspecialidades($espe, $wbasedato, $aplicacion)
{

    global $conex;


    $val = "";

    if ($aplicacion == "") {
        //especialidad
        $sql = "SELECT Selcod,Seldes
                    FROM " . $wbasedato . "_000105
                    WHERE (Seldes LIKE '%" . utf8_decode($espe) . "%' or Selcod like '%" . utf8_decode($espe) . "%')
                    AND Seltip='11'
                    AND Selest='on'
                    ORDER BY Seldes
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Selcod'] = trim(utf8_encode($rows['Selcod']));
                $rows['Seldes'] = trim(utf8_encode($rows['Seldes']));

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Selcod'], "des" => $rows['Seldes']);   //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows['Selcod']}-{$rows['Seldes']}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";
            }
        }
    } else {
        $sql = "SELECT  Espcod, Espnom
                    FROM " . $aplicacion . "_000044
                    WHERE (Espcod LIKE '%" . utf8_decode($espe) . "%' or Espnom like '%" . utf8_decode($espe) . "%')
                    ORDER BY Espnom
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Espcod'] = trim(utf8_encode($rows['Espcod']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Espcod'], "des" => $rows['Espnom']);   //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows['Espcod']}-{$rows['Espnom']}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";
            }
        }
    }

    return $val;
}

function consultarServicios($serv, $wbasedato, $aplicacion)
{

    global $conex;


    $val = "";

    if ($aplicacion == "") {
        //servicios
        $sql = "SELECT Ccocod,Ccodes
                    FROM " . $wbasedato . "_000003
                    WHERE (Ccodes LIKE '%" . utf8_decode($serv) . "%' or Ccocod like '%" . utf8_decode($serv) . "%')
                    AND (Ccotip='A' or Ccotip='H')
                    ORDER BY Ccodes
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Ccocod'] = trim(utf8_encode($rows['Ccocod']));
                $rows['Ccodes'] = trim(utf8_encode($rows['Ccodes']));

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Ccocod'], "des" => $rows['Ccodes']);   //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows['Ccocod']}-{$rows['Ccodes']}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";
            }
        }
    } else {
        $sql = "SELECT  Ccocod, Cconom
                    FROM " . $aplicacion . "_000011
                    WHERE (Ccocod LIKE '%" . utf8_decode($serv) . "%' or Cconom like '%" . utf8_decode($serv) . "%')
                    AND (Ccohos = 'on' or Ccourg = 'on' or Ccoing = 'on' or Ccocir = 'on' or Ccoayu ='on')
                    AND Ccoest='on'
                    ORDER BY Cconom
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Ccocod'] = trim(utf8_encode($rows['Ccocod']));
                $rows['Cconom'] = trim(utf8_encode($rows['Cconom']));

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Ccocod'], "des" => $rows['Cconom']);   //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows['Ccocod']}-{$rows['Cconom']}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";
            }
        }
    }

    return $val;
}

/************************************************************************************************
 * Crea un array de datos que hace los siguiente.
 *
 * Toma todas las variables enviadas por Post, y las convierte en un array. Este array puede ser
 * procesado por las funciones crearStringInsert y crearStringInsert
 *
 * Explicacion:
 * Toma todas las variables enviadas por Post que comiencen con $prefijoHtml, creando un array
 * donde su clave o posicion comiencen con $prefijoBD concatenado con $longitud de caracteres
 * despues del $prefijoHtml y dandole como valor el valor de la variable enviada por Post
 *
 * Ejemplo:
 *
 * La variable Post es: indpersonas = 'Armando Calle'
 * Ejecutando la funcion: $a = crearArrayDatos( 'movhos', 'Per', 'ind', 3 );
 *
 * El array que retorna la función es:
 *                      $a[ 'Perper' ] = 'Armando Calle'
 *                      $a[ 'Medico' ] = 'movhos'
 *                      $a[ 'Fecha_data' ] = '2013-05-22'
 *                      $a[ 'Hora_data' ] = '05:30:24'
 *                      $a[ 'Seguridad' ] = 'C-movhos'
 ************************************************************************************************/
function crearArrayDatos($wbasedato, $prefijoBD, $prefijoHtml, $longitud, $datos = '')
{

    $val = array();

    if (empty($datos)) {
        $datos = $_POST;
    }

    $crearDatosExtras = false;

    $lenHtml = strlen($prefijoHtml);

    foreach ($datos as $keyPost => $valuePost) {

        if (substr($keyPost, 0, $lenHtml) == $prefijoHtml) {

            if (substr($keyPost, $lenHtml, $longitud) != 'id') {
                $val[$prefijoBD . substr($keyPost, $lenHtml, $longitud)] = utf8_decode($valuePost);
            } else {
                $val[substr($keyPost, $lenHtml, $longitud)] = utf8_decode($valuePost);
            }
            $crearDatosExtras = true;
        }
    }

    //Estos campos se llenan automáticamente y toda tabla debe tener esots campos
    if ($crearDatosExtras) {
        global $user;
        $user2 = explode("-", $user);
        (isset($user2[1])) ? $user2 = $user2[1] : $user2 = $user2[0];
        if ($user2 == "")
            $user2 = $wbasedato;
        $val['Medico'] = $wbasedato;
        $val['Fecha_data'] = date("Y-m-d");
        $val['Hora_data'] = date("H:i:s");
        $val['Seguridad'] = "C-$user2";
    }


    return $val;
}

/***************************************************************************************
 * inserta los datos a la tabla
 *
 * $datos   Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla   Nombre de la tabla a la que se va a insertar los datos
 ***************************************************************************************/
function crearStringInsert($tabla, $datos)
{

    $stPartInsert = "";
    $stPartValues = "";

    foreach ($datos as $keyDatos => $valueDatos) {

        $stPartInsert .= ",$keyDatos";
        $stPartValues .= ",'$valueDatos'";
    }


    $stPartInsert = "INSERT INTO $tabla(" . substr($stPartInsert, 1) . ")";   //quito la coma inicial
    $stPartValues = " VALUES (" . substr($stPartValues, 1) . ")";

    return $stPartInsert . $stPartValues;
}

function consultaNombreImpDiag($codImpDiag)
{
    global $conex;

    //consultar codigo impresion diagnostica
    $sql1 = "select Codigo, Descripcion, Notificar
                FROM root_000011
                where Codigo = '" . $codImpDiag . "'
                ";
    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla de diagnosticos " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));

    return $res1;
}

function consultaNombreEntidad($codImpDiag)
{
    global $conex;
    global $wbasedato;

    //consultar codigo impresion diagnostica
    $sql1 = "select Empcod as Codigo, Empnom as Descripcion
                FROM " . $wbasedato . "_000024
                where Empcod = '" . $codImpDiag . "'
                ";
    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla de diagnosticos " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));

    return $res1;
}

function consultaNombreProce($codProce)
{
    global $conex;
    global $wbasedato;

    //consultar codigo impresion diagnostica
    $sql1 = "select Procod,Pronom
                FROM " . $wbasedato . "_000103
                where Procod = '" . $codProce . "'
                ";
    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla de procedimientos " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));

    return $res1;
}

function consultaNombreEspe($codEspe, $aplicacion = "movhos")
{
    global $conex;
    global $wbasedato;
    $aplicacion = "movhos";
    $res = "";
    if ($aplicacion == "") {
        //especialidad
        $sql = "SELECT Selcod as Espcod,Seldes as Espnom
                FROM " . $wbasedato . "_000105
                WEHRE Selcod = '" . $codEspe . "'
                AND Seltip='11'
                AND Selest='on'
                ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    } else {
        $sql = "SELECT  Espcod, Espnom
                FROM " . $aplicacion . "_000044
                WHERE Espcod = '" . $codEspe . "'
                ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    }

    return $res;
}

function consultarAnestesiologo($codigoAntestesiologo, $wbasedato)
{
    global $conex;

    $query = "SELECT Medcod, Mednom,Medesp,Espnom
                    FROM {$wbasedato}_000051 LEFT JOIN {$wbasedato}_000053 ON (Medesp=Espcod)
                    WHERE Medcod = '$codigoAntestesiologo'
                    ORDER BY Mednom";
    $rs = mysql_query($query, $conex);

    while ($row = mysql_fetch_array($rs)) {
        $pos = strpos($row['Medesp'], "-");
        if ($pos !== false) {
            $aux = explode("-", $row['Medesp']);
            $row['Medesp'] = $aux[0];
            $row['Espnom'] = $aux[1];
        }

        if ($row['Medesp'] == "") $row['Espnom'] = "00000";
        if ($row['Espnom'] == "") $row['Espnom'] = "SIN DATOS";
    }
    return ($row);
}

function consultaNombreServ($codServ, $aplicacion)
{
    global $conex;
    global $aplicacion;
    global $wbasedato;

    if ($aplicacion == "") {
        //consultar codigo del servicio
        $sql1 = "SELECT Ccocod,Ccodes
                FROM " . $wbasedato . "_000003
                WHERE Ccocod = '" . $codServ . "'";
    } else {
        $sql1 = " SELECT  Ccocod, Cconom
                    FROM " . $aplicacion . "_000011
                    WHERE Ccocod = '" . $codServ . "'";
    }
    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla de servicios " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));

    return $res1;
}

function consultaNombreMedicos2($codMed, $aplicacion)
{
    global $conex;
    global $wbasedato;
    global $mostrarSalida;

    $wmedico = array(
        'n_medico' => '',
        'c_especialidad' => '',
        'n_especialidad' => ''
    );

    $sql = "";

    if ($aplicacion == "") {
        $sql = "SELECT Medcod, Mednom, Medesp, Espnom
                FROM " . $wbasedato . "_000051 LEFT JOIN " . $wbasedato . "_000053 ON (Medesp=Espcod)
                WHERE Medcod = '" . $codMed . "'";
    } else {
        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2, Medesp, Espnom
                FROM " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
                WHERE Meddoc = '" . $codMed . "'";
    }
    $res4 = mysql_query($sql, $conex);
    if ($res4) {
        $num4 = mysql_num_rows($res4);
        if ($num4 > 0) {
            $rows4 = mysql_fetch_array($res4);
            if ($aplicacion != "")
                $wmedico['n_medico'] = $rows4['Medno1'] . " " . $rows4['Medno2'] . " " . $rows4['Medap1'] . " " . $rows4['Medap2'];
            else
                $wmedico['n_medico'] = utf8_encode($rows4['Mednom']);
            $wmedico['c_especialidad'] = $rows4['Medesp'];
            $wmedico['n_especialidad'] = utf8_encode($rows4['Espnom']);
        }
    }
    return $wmedico;
}

function consultaNombreMedicos($codMed, $aplicacion)
{
    global $conex;
    global $wbasedato;

    $sql = "";
    if ($aplicacion == "") {
        $sql = "SELECT Medcod, Mednom
                FROM " . $wbasedato . "_000051
                WHERE Medcod = '" . $codMed . "'
                AND Medest ='on'
                ORDER BY Mednom
                ";
    } else {
        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2
                FROM " . $aplicacion . "_000048
                WHERE Meddoc = '" . $codMed . "'
                AND Medest ='on'
                AND Meddoc != ''
                ORDER BY Medno1,Medno2,Medap1,Medap2
                ";
    }

    $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    return $res;
}

function crearStringUpdate($tabla, $datos)
{

    $stPartInsert = "";
    $stPartValues = "";

    //campos que no se actualizan
    $prohibidos["Medico"] = true;
    $prohibidos["Fecha_data"] = true;
    $prohibidos["Hora_data"] = true;
    $prohibidos["Seguridad"] = true;
    $prohibidos["id"] = true;

    foreach ($datos as $keyDatos => $valueDatos) {

        if (!isset($prohibidos[$keyDatos])) {
            $stPartInsert .= ",$keyDatos = '$valueDatos' ";
        }
    }

    $stPartInsert = "UPDATE $tabla SET " . substr($stPartInsert, 1);    //quito la coma inicial
    $stPartValues = " WHERE id = '{$datos['id']}'";

    return $stPartInsert . $stPartValues;

    //UPDATE  `matrix`.`movhos_000138` SET  `Dprest` =  'off' WHERE  `movhos_000138`.`id` =82;
}

function logEgreso($des, $historia, $ingreso, $documento, $tipoDocumento, $paciente)
{
    global $key;
    global $conex;
    global $wbasedato;
    global $wcliame;
    global $user;

    $data = array('error' => 0, 'mensaje' => '', 'html' => '');

    $fecha = date("Y-m-d");
    $hora = (string)date("H:i:s");

    $sql = "INSERT INTO " . $wcliame . "_000343 (historia, ingreso, documento_paciente, medico, descripcion, usuario, fecha_creado, hora_creado,egresado,tipo_documento,paciente) 
            VALUES ('" . $historia . "','" . $ingreso . "','" . $documento . "','" . $wbasedato . "','" . $des . "','" . $user . "','" . $fecha . "','" . $hora . "',true,'" . $tipoDocumento . "','" . $paciente . "')";

    $res = mysql_query($sql, $conex) or ($data['mensaje'] = utf8_encode("Error insertando en la tabla de log egreso automático" . $wbasedato . "_autlog" . mysql_errno() . " - Error en el query $sql - " . mysql_error()));
    if (!$res) {
        $data['error'] = 1; //sale el mensaje de error
    }

    return $data;
}

function quitarEtiquetasCadena($cadena)
{
    // $cadena = ' <OPTION selected>C-.infeccion tracto urinario</OPTION><OPTION value=C-.sindrome de intestino irritable>C-.sindrome de intestino irritable</OPTION><OPTION value=C-.Gastritis>C-.Gastritis</OPTION>';
    $cadena = str_replace("><", ">\n<", $cadena);
    $cadena = strip_tags($cadena);

    return $cadena;
}

function consultarCcoAyuda($cco_buscado)
{
    global $conex, $wemp_pmla, $aplicacion;

    $query = "SELECT ccoayu
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$cco_buscado}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    $ccoAyu = ($row['ccoayu'] == "on") ? true : false;
    return ($ccoAyu);
}

function consultarCcoHos($cco_buscado)
{
    global $conex, $wemp_pmla, $aplicacion;

    $query = "SELECT ccohos
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$cco_buscado}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    $ccoHos = ($row['ccohos'] == "on") ? true : false;
    return ($ccoHos);
}

function consultarNombreDiagnostico($diagnostico)
{
    global $conex, $wemp_pmla, $aplicacion;

    $query = "SELECT ccoayu
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$cco_buscado}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    $ccoAyu = ($row['ccoayu'] == "on") ? true : false;
    return ($ccoAyu);
}

function consultarCcoUrgencias($cco_buscado)
{
    global $conex, $wemp_pmla, $aplicacion;

    $query = "SELECT ccourg
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$cco_buscado}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    $ccourg = ($row['ccourg'] == "on") ? true : false;
    return ($ccourg);
}

function consultarCausaEgresoUrgencias($historia, $ingreso, $fechaIngreso, $horaIngreso)
{
    global $conex, $wemp_pmla, $aplicacion;
    $causaEgreso = "A";

    $query = "SELECT Ubihis, Ubiing, Ubifap, Ubihap, Ubimue
                FROM {$aplicacion}_000018
               WHERE Ubihis = '{$historia}'
                 AND Ubiing = '{$ingreso}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    if ($row['Ubimue'] == "on") {
        //--> calcular tiempo estancia.
        $tiempoUnixEgreso = strtotime($row['Ubifap'] . " " . $row['Ubihap']);
        $tiempoUnixIngreso = strtotime($fechaIngreso . " " . $horaIngreso);
        $tiempoDiferencia = ceil(($tiempoUnixEgreso - $tiempoUnixIngreso) / 3600);
        $causaEgreso = ($tiempoDiferencia >= 48) ? "+48" : "-48";
    }
    return ($causaEgreso);
}

//--> aca en medicos tratantes podríamos consultar los centros de costos por los que pasó el paciente
function consultarMedicosTratantes($historia, $ingreso, $aplicacion)
{
    global $conex;
    $arr_medicos = array();
    $query = "SELECT Meddoc as cod, CONCAT( Medno1,' ',Medno2,' ',Medap1,' ',Medap2) as nom, Medesp as esp_cod, Espnom as esp_nom
              FROM " . $aplicacion . "_000047, " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
             WHERE Methis = '" . $historia . "'
               AND Meting = '" . $ingreso . "'
               AND Metdoc = Meddoc
               AND (Medgen = 'on' or Medees = 'on')
               GROUP BY Meddoc";

    $res = mysql_query($query, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $aplicacionHce . "_000022 y " . $aplicacion . "_000048 " . mysql_errno() . " - Error en el query $sql - " . mysql_error()));
    if ($res) {
        $num = mysql_num_rows($res);
        if ($num > 0) {
            while ($rows = mysql_fetch_assoc($res)) {
                array_push($arr_medicos, $rows);
            }
        }
    }
    return $arr_medicos;
}

function calcularEdad($fecha_nacimiento)
{
    list($y, $m, $d) = explode("-", $fecha_nacimiento);
    $y_dif = date("Y") - $y;
    $m_dif = date("m") - $m;
    $d_dif = date("d") - $d;
    if ((($d_dif < 0) && ($m_dif == 0)) || ($m_dif < 0))
        $y_dif--;
    return $y_dif;
}

function ping_unix()
{
    global $conex;
    global $wemp_pmla;

    $ret = false;

    $direccion_ipunix = consultarAliasPorAplicacion($conex, $wemp_pmla, "ipdbunix");
    if ($direccion_ipunix != "") {
        $cmd_result = shell_exec("ping -c 1 -w 1 " . $direccion_ipunix);
        $result = explode(",", $cmd_result);
        $recibidos = explode('=', $result[1]); // para windows
        if (preg_match('/(1 received)/', $result[1]) or $recibidos[1] > 1) {
            $ret = true;
        }
    }
    return $ret;
}

function consultarServiciosDiagnosticos($servicioEgreso)
{
    global $conex, $wemp_pmla, $wbasedato, $aplicacion;

    $data = array();

    if ($aplicacion == "") {
        $sql = "SELECT Ccocod,Ccodes
                FROM " . $wbasedato . "_000003
                WHERE (Ccodes LIKE '%" . utf8_decode($serv) . "%' or Ccocod like '%" . utf8_decode($serv) . "%')
                AND (Ccotip='A' or Ccotip='H')
                ORDER BY Ccodes";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $data[$rows['Ccocod']]['codigo'] = trim(utf8_encode($rows['Ccocod']));
                $data[$rows['Ccocod']]['descripcion'] = trim(utf8_encode($rows['Ccodes']));
            }
        }
    } else {
        $queryEgreso = " SELECT Ccoayu
                           FROM {$aplicacion}_000011
                          WHERE Ccocod = '{$servicioEgreso}'";
        $rsEgre = mysql_query($queryEgreso, $conex);
        $rowEgreso = mysql_fetch_assoc($rsEgre);

        ($rowEgreso['Ccoayu'] == "on") ? $condicionCcos = " AND Ccoayu ='on' " : $condicionCcos = " AND (Ccohos = 'on' or Ccourg = 'on' or Ccocir = 'on' or Ccoayu ='on') ";
        $sql = "SELECT  Ccocod, Cconom
                FROM " . $aplicacion . "_000011
                WHERE (Ccocod LIKE '%" . utf8_decode($serv) . "%' or Cconom like '%" . utf8_decode($serv) . "%')
                {$condicionCcos}
                AND Ccoest='on'
                ORDER BY Cconom";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $data[$rows['Ccocod']]['codigo'] = trim(utf8_encode($rows['Ccocod']));
                $data[$rows['Ccocod']]['descripcion'] = trim(utf8_encode($rows['Cconom']));
            }
        }
    }
    return $data;
}

function consultarEgresoAnulado($historia, $ingreso)
{
    global $conex, $wemp_pmla, $wbasedato, $aplicacion;

    $sql = "SELECT Egrhis,Egring,id,Egract
               FROM {$wbasedato}_000108
              WHERE Egrhis = '{$historia}'
                AND Egring = '{$ingreso}'";

    $rs = mysql_query($sql, $conex);
    $row = mysql_fetch_assoc($rs);
    return (($row['Egract'] == "off") ? true : false);
}

function consultarServiciosCirugia()
{

    global $conex, $aplicacion;
    $ccoCir = array();
    $query = "SELECT ccocod
                 FROM {$aplicacion}_000011
                WHERE ccocir = 'on'
                UNION ALL
               SELECT ccocod
                 FROM {$aplicacion}_000011
                WHERE Ccopeg = 'off'";
    $rs = mysql_query($query, $conex);
    while ($row = mysql_fetch_assoc($rs)) {
        array_push($ccoCir, "'" . $row['ccocod'] . "'");
    }
    array_push($ccoCir, "''");
    $ccoCir = implode(",", $ccoCir);
    return ($ccoCir);
}

function consultarMedicoDiagnostico($historia, $ingreso, $formularioEvolucionUrgencias, $usuario = "")
{

    global $conex, $wemp_pmla, $wbasedato, $aplicacionHce, $aplicacion;

    if ($usuario == "") {
        $query = " SELECT Firusu, id
                     FROM {$aplicacionHce}_000036
                    WHERE firhis = '{$historia}'
                      AND firing = '{$ingreso}'
                      AND firpro = '{$formularioEvolucionUrgencias}'
                      AND firfir = 'on'
                    ORDER BY id desc
                    LIMIT 1";
        $rs = mysql_query($query, $conex);
        $row = mysql_fetch_assoc($rs);

        $codigoMedicoUrgencias = $row['Firusu'];
    } else {
        $codigoMedicoUrgencias = $usuario;
    }

    $query = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2, Medesp, Espnom, Meduma
                FROM {$aplicacion}_000048
                LEFT JOIN
                     {$aplicacion}_000044 ON (Medesp=Espcod)
               WHERE Meduma = '{$codigoMedicoUrgencias}'";
    $rs = mysql_query($query, $conex);

    return (mysql_fetch_assoc($rs));
}

/***--------------------------------------------------------***/
function consultarCentrosCostosDomiciliaria()
{
    global $wbasedato;
    global $conex;

    $ccocodIds = [];
    $ccocodNames = [];

    $sql = "SELECT Ccocod, Cconom FROM " . $wbasedato . "_000011 G WHERE Ccohos = 'on' and ccodom ='on' ORDER BY Cconom";
    $query = mysql_query($sql, $conex);

    while ($row = mysql_fetch_array($query)) {
        array_push($ccocodIds, $row['Ccocod']);
        array_push($ccocodNames, $row['Cconom']);
    }

    $arrayCentroCostos = [
        'ids' => $ccocodIds,
        'names' => $ccocodNames
    ];

    return $arrayCentroCostos;
}


function generarSelectCentroCostos()
{
    $valoresSelect = consultarCentrosCostosDomiciliaria();
    $ids = $valoresSelect['ids'];
    $names = $valoresSelect['names'];

    $htmlSelect = "";


    if (count($ids)) {
        $htmlSelect .= "<option value ='todos' selected>Todos</option>";
        foreach ($ids as $key => $id) {
            $htmlSelect .= "<option value='" . $id . "'>" . $id . " - " . $names[$key] . "</option>";
        }
    }

    echo $htmlSelect;
}

function consultarPacientesEgresoSQL($wbasedato, $wcliame, $wemp_pmla, $whisconsultada, $wcco0, $cemp)
{
    global $wtablacliame;

    $centrosCostoDomi = consultarCentrosCostosDomiciliaria();
    $idsCCD = implode("','", $centrosCostoDomi['ids']);

    $q = "SELECT 
                UB.ubimue,
                UB.ubifad,
                UB.ubifap,
                A.pacced,
                A.pactid,
                A.pacno1,
			    A.pacno2,
			    A.pacap1,
			    A.pacap2,
                UB.ubihis AS historia,
                UB.ubiing AS ingreso,
                UB.ubisac,
                EGR.egrhis,
                UB.ubihap,
                UB.ubihad
        FROM  " . $wcliame . "_000101 tbIng
        INNER JOIN root_000037 B ON  B.orihis =tbIng.inghis AND B.Oriing = tbIng.ingnin
        INNER JOIN  " . $wbasedato . "_000018 UB ON UB.ubihis = tbIng.inghis  AND UB.ubiing = tbIng.ingnin
        INNER JOIN root_000036 A  ON A.pacced = B.oriced AND A.pactid = B.oritid
        INNER JOIN " . $wbasedato . "_000011 G ON   G.ccocod = UB.ubisac
        LEFT JOIN " . $wcliame . "_000108 EGR ON EGR.egrhis = tbIng.inghis AND EGR.egring = tbIng.ingnin  AND egract = 'on'
        WHERE 
        EGR.egrhis IS NULL
        AND UB.ubiald != 'on' -- Paciente activo en la clinica
        AND UB.Ubimue != 'on' -- No está muerto
        AND G.Ccohos = 'on'  -- Centro de costos hospitalario disponible
        AND G.ccodom ='on'  -- Centro de costo domiciliario
        AND B.Oriori = '" . $wemp_pmla . "'
        AND UB.Fecha_data > (SELECT Detval FROM root_000051 WHERE Detemp='{$wemp_pmla}' AND Detapl = 'fecha_limite_egresos')";

    if ($whisconsultada != "") {
        $q .= " AND UB.ubihis = '" . $whisconsultada . "'";
    }

    if ((count($wcco0) > 0 && $wcco0[0] != "todos") or count($wcco0) > 1) {
        $arrayLimpio = array_diff($wcco0, array("todos"));
        $wcco0Array = implode("','", $arrayLimpio);
        $q .= " AND G.ccocod IN ('" . $wcco0Array . "') ";
    }

    /** Si selecciona una EPS */
    if ((count($cemp) > 0 && $cemp[0] != "todos") or count($cemp) > 1) {
        $arrayLimpio = array_diff($cemp, array("todos"));
        $cempArray = implode("','", $arrayLimpio);
        $q .= " AND tbIng.Ingcem IN ('" . $cempArray . "')";
    }

    return $q;
}

function dias_pasados($fecha_inicial, $fecha_final)
{
    $dias = (strtotime($fecha_inicial) - strtotime($fecha_final)) / 86400;
    $dias = abs($dias);
    $dias = floor($dias);
    return $dias;
}

function generarSelectEPS()
{
    global $wcliame;

    $htmlSelect = "";
    $htmlSelect .= "<option value ='todos' selected>Todos</option>";

    $sql = "SELECT Empcod, Empnom FROM {$wcliame}_000024";
    $query = mysql_query($sql, $conex);

    while ($row = mysql_fetch_array($query)) {
        $htmlSelect .= "<option value='" . $row['Empcod'] . "'>" . $row['Empcod'] . " - " . $row['Empnom'] . "</option>";
    }

    echo $htmlSelect;
}
