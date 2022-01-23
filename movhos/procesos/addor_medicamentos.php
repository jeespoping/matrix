<?php
include_once("conex.php");
$wactualiz = "Enero 14 de 2021";
/*
 PROGRAMA                   : addor_medicamentos.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 11 Marzo de 2013

 DESCRIPCIÓN:
 Este programa se encarga de listar los articulos que se cargan desde unix e informa cuales artiulos están pendientes por configurar, también muestra una lista de
 articulos guardados sobre los que se pueden hacer modificaciones y permite realizar una búsqueda por nombre de artiulo o código del articulo.

 ACTUALIZACIONES:
 *  Abril 10 de 2013
    Edwar Jaramillo     :   - En la edición de artículos modifica la sección dedicada a asociar el producto o medicamento en edición a otros artículos,
                                antes solo se estaba permitiendo seleccionar medicamentos configurados pero ahora se permite seleccionar cualquier artículo activo
                                del maestreo de artículos (000026), tambien se modificó el buscador de artículos para que búsque al precionar la tecla ENTER y se
                                muestre un gif indicando que está buscando.

 *  Abril 09 de 2013
    Edwar Jaramillo     :   - En la edición de artículos se crea una nueva sección dedicada a asociar el producto o medicamento en edición a otros artículos,
                                de los cuales se puede asociar uno o varios al mismo medicamento. Esta nueva sección adiciona a la lista de asociados el artículo
                                la cantidad y el estado del registro (on-off).

 *  Marzo 19 de 2013
    Edwar Jaramillo     :   - Adiciona regitrar en tabla LOG cuando se configura o se edita un medicamento.
                            - Se adiciona columna de estado de medicamento en las listas de edición de artículos.
                            - Ahora, en los formulario de ingreso y edición, se muestra el nombre del grupo al que pertenece el medicamento.
                            - Se adiciona campo de estado del medicamento al editar un artículo, antes solo estaba el campo para marcar si era o no medicamento.
                            - Antes, al momento de inactivar un medicamento, se actualizaba a estado inactivo los registros en las tablas donde se asociaba el artículo,
                                SI se inactivan los registros asociados en las otras tablas si se marcó el artículo como -NO ES MEDICAMENTO-.
                            - Se adiciona función para insertar en la tabla de LOG, insertLog().
                            - Al cargar los medicamentos editados, se quitan algunos filtros de estados para que se muestren esos artículos en las listas,
                                como es el caso de movhos_000059, un medicamento puede tener fracción pero esta inactiva su fracción. esto no es un error y debe
                                identificarse como que el medicamento si tiene fracción.
                            - Se cambian campos obligatorios en la sección de definición de fracciones, p.e. ya no se valídan -Requeridos IPOD-.

 *  Marzo 15 de 2013
    Edwar Jaramillo     :   - Se crea un array y una función para administrar las ayudas que se mostrarán en el formulario al lado de los campos donde se solicitan datos,
                                las modificaciones en los textos de ayuda del array, se reflejarán en los campos donde esté citado la ayuda para ese campo.

                            - Cambio al campo "Cantidad variable por Centro Costo", ahora puede seleccionar la opción [TODOS], lo que implica guardarse en la base de datos
                                el caracter "*" y que hace relación a "Todos los centros de costos" u opciones de ese select.

                            - Se incorpora la librería .js tooltip para mostrar con formato el texto de los mensajes de ayuda de cada campo.

                            - Se cambia la lógica de funcionamiento para la sección IPOD, si el campo "NO se aplica con IPOD" no está chequeado entonces sus demás campos
                                relacionados estarán activos, si se chequea entonces sus campos relacionados se desactivan y se borra su contenido.

 *  Marzo 11 de 2013
    Edwar Jaramillo     : Fecha de la creación del programa.

*/




include_once("root/comun.php");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

$ccoSF=ccoUnificadoSF();


if(!isset($_SESSION['user']))
{
    // Implementado para hacer algunas pruebas, a veces es necesario realizar algunas pruebas sin tener que estar loqueado.
    if(isset($user_session))
    {
        $_SESSION['user'] = $user_session;
    }
}

if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamnte en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
elseif(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];

/** DICCIONARIO **/
define("LATIN1_UC_CHARS", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ");
define("LATIN1_LC_CHARS", "àáâãäåæçèéêëìíîïðñòóôõöøùúûüý");
define("CENTRO_COSTO_FIJO", $ccoSF);
define("MSJ1_CODIGO_CUM", "Ej. 123456789-01.");
define("MSJ1_CONVERSION_CUM", "Este n&uacute;mero permite convertir la presentaci&oacute;n interna (Unidad interna) de un articulo a la presentaci&oacute;n CUM (Unidad nacional).");
define("MSJ2_CONVERSION_CUM", "(Debe ser un n&uacute;mero entre cero y uno Ej. 0, 0.001, 0.5, 1, 1.00)");


// Texto de ayudas
$arr_ayuda = array(
                    "t26_artcod"        => "Código interno del artículo.",
                    "t26_artcom"        => "Nombre común o nombre comercial del medicamento.",
                    "t26_artuni"        => "Unidad de manejo del medicamento para servicio farmaceutico.",
                    "t26_artesm"        => "Si es un artículo nuevo y no está configurado como medicamento, de sebe activar o chequear este campo para guardar el artículo
                                            como medicamento, si el artículo se configura o se guarda sin chequear esta opción, el artículo se guardará como -NO ES MEDICAMENTO-",
                    "t26_artest"        => "Estado del medicamento en la base de datos.",
                    "t26_artgru"        => "Grupo de medicamentos en el que se clasifica el artículo.",
                    "t66_tipo"          => "Clasifica el artículo como Medicamento como líquido endovenoso.",
                    "t115_familia"      => "Familia principal o general de medicamento a la que pertenece el artículo.",
                    "secc_def_fraccion" => "Sección para definir la unidad y la fracción equivalente
                                            en el servicio de enfermería.",
                    "t59_cco"           => "",
                    "t59_unidad"        => "Unidad de medida mínima para enfermería.",
                    "t59_fraccion"      => "Concentración mínima del medicamento.",
                    "t59_estado"        => "Esta opción debe estar activa para que el medicamento se
                                            configure con la fracción actual, de lo contrario, el
                                            medicamento quedará sin fracción",
                    "t59_vence"         => "Indica si el medicamento tiene caducidad.",
                    "t59_dias_estable"  => "Si tiene vencimiento, en este campo se indican el tiempo de estabilidad del medicamento para su uso antes del vencimiento",
                    "t59_dispensable"   => "",
                    "t59_duplica"       => "Indica si el medicamento puede estar más de una vez en el Kardex.",
                    "t59_cofirma"       => "Permite confirmar la preparación del medicamento, aplica solo para Central de Mezclas.",
                    "t59_nokardex"      => "Indica si el medicamento puede ser dispensado sin necesidad de estar en el perfil farmacoterapeutico.",
                    "t59_diastrat"      => "Días de tratamiento por defecto del medicamento para el Kardex de enfermería.",
                    "t59_dosismax"      => "Dósis máxima por defecto del medicamento para el Kardex de enfermería.",
                    "t59_viasadmin"     => "Vías de administración por donde se suministra el medicamento. Se permite seleccionar varias vías presionando
                                            la tecla Control (Ctrl)+Clic en los nombres de la lista.",
                    "t59_noipod"        => "Indica si el medicamento no se aplica mediante el dispositivo IPOD.",
                    "t59_reqcant"       => "Indica que al aplicar mediante el dispositivo IPOD, la dósis es o no variable.",
                    "t59_caninfer"      => "Cantidad mínima de dósis del medicamento, cuando la cantidad de dósis es variable por IPOD.",
                    "t59_cansuper"      => "Cantidad MÁXIMA de dósis del medicamento, cuando la cantidad de dósis es variable por IPOD.",
                    "t59_escala"        => "Es la escala de incremento entre la cantidad inferior y superior cuando la cantidad de dósis
                                            del medicamento es variable en el IPOD.",
                    "t59_cant_porcco"   => "Son los centros de costos en los que al aplicar por IPOD, el medicamento tiene una cantidad inferior y superior y una escala",
                    "t59_cant_framin"   => "Dósis mínima que el sistema valída en las ordenes de medicamentos.",
                    "t59_cant_framax"   => "Dósis MÁXIMA que el sistema valída en las ordenes de medicamentos.",
                    "t91_destock"       => "Indica si el medecamento debe estar o no disponible en el Stock del piso (o centros de costos) seleccionados en la lista.
                                            Se pueden seleccionar varios centros de costos de la lista presionando la tecla Control (Ctrl)+clic y seleccionando
                                            los nombres de centros de costos necesarios.",
                    "t08_medic_especial"=> "Indica si el medicamento en su unidad de manejo, se puede dispensar en una cantidad variable.",
                    "secc_especial"     => "En esta sección se configuran las condiciones especiales del medicamento a dispensar, aquí se configuran las cantidades
                                            variables especificando por centro de costos.",
                    "t08_cco"           => "Centro de costo para el que se configuran condiciones especiales del medicamento para su dispensanción.",
                    "t08_cantvaria"     => "Indica si el medicamento se puede dispensar en una cantidad variable.",
                    "t08_cantdefecto"   => "Cantidad por defecto a dispensar. (Si la cantidad es variable, la cantidad por defecto debe ser múltiplo de la cantidad máxima,
                                            de lo contrario, deben ser iguales).",
                    "t08_cantmax"       => "Cantidad máxima a dispensar. (Si la cantidad es variable, la cantidad por defecto debe ser múltiplo de la cantidad máxima,
                                            de lo contrario, deben ser iguales).",
                    "t08_negativos"     => "Indica si se pueden guardar valores negativos al momento de grabar.",
                    "t08_aplicaauto"    => "El medicamento se aplica automáticamente.",
                    "tequ_medic_equivalente"    => "Al seleccionar esta opción, se puede asociar otros artículos al medicamento que se está editando.",
                    "secc_equivalente"          => "Sección para adicionar uno o varios artículos que se van a asociar al medicamento actual.",
                    "tequ_art"                  => "Código-Artículo que se va a asociar al medicamento actual.",
                    "tequ_art_b"                => "Campo para buscar artículos por código o por nombre (puede usar el caracter [*] como comodín).",
                    "tequ_cant"                 => "Cantidad para asociar.",
                    "tequ_est"                  => "Estado del registro que asocia el medicamento seleccionado al medicamento que se está editando.");


function strtoupper_mx($str) { return strtoupper(strtr($str, LATIN1_LC_CHARS, LATIN1_UC_CHARS)); }

function printHelp($arr_ayuda,$dato,$edit='')
{
    $style_help = 'style="font-size:7pt;"';
    $help = '';
    if(array_key_exists($dato, $arr_ayuda) && trim($arr_ayuda[$dato]) != "")
    {
        $txt = "<div style='font-size:8pt;width:150px;text-align:justify;'>".$arr_ayuda[$dato]."</div>";
        $help = '<span '.$style_help.' title="'.$txt.'" class="msg_tooltip_'.$dato.$edit.'">[?]</span> ';
    }
    return $help;
}

/**
    Función para insertar restro de operación en el log de eventos.
*/
function insertLog($conex, $wbasedato, $user_session, $accion, $form, $tema, $err, $descripcion, $user_update, $sql_error = '')
{
    $descripcion = str_replace("'",'"',$descripcion);
    // $sql_error = str_replace(PHP_EOL,'',$sql_error); // elimina los cambios de línea.
    $sql_error = ereg_replace('([ ]+)',' ',$sql_error);

    $insert = " INSERT INTO ".$wbasedato."_000151
                    (Medico, Fecha_data, Hora_data, Logres, Logacc, Logfrm, Logtem, Logerr, Logsqe, Logdes, Logest, Seguridad)
                VALUES
                    ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode($user_update)."','".utf8_decode($accion)."','".$form."','".$tema."','".$err."',\"".$sql_error."\",'".utf8_decode($descripcion)."','on','C-".$user_session."')";
    $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());
}

/**
 * Adiciona contenido a archivo temporal en memoria - Debug
 *
 * @param unknown $log      : Contenido string.
 * @param unknown $reset    : Determina si borra o mantiene el el contenido anterior almacenado en el buffer $DEBUG_LOG_FUNCIONES.
 * @return unknown
 */
 global $DEBUG_LOG_FUNCIONES;
function debug_log_inline($return_log = 'on', $log = '', $reset = false)
{
    global $DEBUG_LOG_FUNCIONES, $DEBUG;
    if($DEBUG!='' && $DEBUG=='true') { $log=(!$reset && $log=='')? ' <span class="endlog">FIN</span> SUB_LOG.<br>': $log;$DEBUG_LOG_FUNCIONES = ((!$reset) ? $DEBUG_LOG_FUNCIONES.'<br>* ('.date("Y-m-d H:i:s").') '.$log: '<br>'.$log); }
    if($return_log == 'on') { return $DEBUG_LOG_FUNCIONES; }
}

function getArrayCUMS($conex, $wemp_pmla)
{
    $arr_cums = array();
    $q = "  SELECT  Cumint, id
            FROM    root_000064
            WHERE   Cumemp = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());
    while ($row = mysql_fetch_array($res)) {
        $arr_cums[$row['Cumint']] = $row['id'];
    }
    return $arr_cums;
}

function ejecutarQueryNoConfigurados($conex, $wemp_pmla, $wbasedato, &$temp, $letra='', $wtexto='')
{
    $temp = "articulos_no_configurados_".date("d").date("His");
    $q = "DROP TEMPORARY TABLE IF EXISTS ".$temp;
    $result = mysql_query($q,$conex) or die("Error: ".mysql_errno()." - ".$q." - ".mysql_error());

    $arr_cums = getArrayCUMS($conex, $wemp_pmla);
    /*  Consultar los medicamentos que tienen casi todo configurado menos el Código CUMS */

    /*
        Se quita el filtro de estado de fracciones de movhos_000059, si esta inactivo entonces si tiene fracción pero inactiva.
        *  AND f.Defest = 'on'
        MODIFICACION 2013-03-18 17:37
    */
    $q_cum = "
                SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH '\'' FROM TRIM(a.Artgen)))) artgen_format, a.Artuni, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artgru, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi, f.Deffru AS unidad_59,f.Deffra AS fraccion_59,f.id AS id_59, a.Artesm AS esmedicamento_26, d.id AS id_familia_115, d.Relfam AS cod_familia_115
                FROM    ".$wbasedato."_000026 AS a
                        INNER JOIN
                        ".$wbasedato."_000059 AS f ON (f.Defcco = '".CENTRO_COSTO_FIJO."' AND f.Defart = a.Artcod)
                        INNER JOIN
                        ".$wbasedato."_000115 AS d ON (a.Artcod = d.Relart AND d.Relest = 'on')
                WHERE   a.Artesm = 'on'
                        AND a.Artest = 'on'
                GROUP BY Artcod";
    $res_nocum = mysql_query($q_cum, $conex) or die("Error: ".mysql_errno()." -  ".$q_cum." - ".mysql_error());
    $arr_articulos_nocum = array();
    /*  Este ciclo lo que hace es generar una secuencia de selects en código sql para sumarlo al query que esta mas abajo y que realiza todo el consolidado de los
        articulos que les hace falta algún tipo de configuración, este ciclo reune exclusivamente los articulos a los que solo les falta crearle el codigo CUM
    */
    while($row = mysql_fetch_array($res_nocum))
    {
        if(!array_key_exists($row['Artcod'], $arr_cums))
        {
            $arr_articulos_nocum[$row['Artcod']] = "SELECT  '".$row['Artcod']."' AS Artcod, '".$row['Artcom']."' AS Artcom, '".$row['Artgen']."' AS Artgen,
                                                            '".$row['artgen_format']."' AS artgen_format, '".$row['Artuni']."' AS Artuni, '".$row['Artgru']."' AS Artgru,
                                                            '".$row['wgrupo26']."' AS wgrupo26, '".$row['Artest']."' AS Artest, '".$row['Artpos']."' AS Artpos,
                                                            '".$row['Artreg']."' AS Artreg, '".$row['Artfar']."' AS Artfar, '".$row['Artubi']."' AS Artubi,
                                                            '".$row['unidad_59']."' AS unidad_59, '".$row['fraccion_59']."' AS fraccion_59, '".$row['id_59']."' AS id_59,
                                                            '".$row['esmedicamento_26']."' AS esmedicamento_26, '".$row['id_familia_115']."' AS id_familia_115,
                                                            '".$row['cod_familia_115']."' AS cod_familia_115";
        }
    }

    // Para consultar los articulo que no tienen familia o fracción definida, o no es medicamento o no tiene detalle de familia
    /*  1. Los que no se sabe aún si es medicamento o no, tienen el campo Artesm en vacío.
        2. Los que no tienen fracción definida pero si tienen detalle de familia.
        3. Los que no tienen fracción definida ni detalle de familia.
        4. Los que tienen fracción definida pero no tienen detalle de familia.
        5. Los medicamentos que solo les falta configurar el código CUMS.

        NOTA: en 5. la consulta con codigos CUMS es la que hace retardar la ejecución del query
    */

    // Complemento articulos sin CUMS
    $complemento_select = '';
    if(count($arr_articulos_nocum))
    {
        $union_all = "
            UNION ALL
            ";
        $complemento_select = "
            UNION ALL
            ".implode($union_all, $arr_articulos_nocum);
    }

    // Se quita el filtro relacionado al estado de la fracción en movhos_000059 puesto que si el articulo tiene fracción asi el registro esté inactivo
    // en movhos_000059 significa que SI tiene fracción pero por algún motivo la inactivaron, ese estado se podría modificar en la sección de Edición de Medicamentos.
    /*
        Se quita filtro estado de gracción en:
        1.  AND f.Defest = 'on'
        2.  AND f.Defest = 'on'
        3.  AND f.Defest = 'on'
        4.  AND f.Defest = 'on'

        MODIFICACION 2013-03-15 17:37
    */
    $sql = "
            CREATE TEMPORARY TABLE ".$temp." AS
            SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH '\'' FROM TRIM(a.Artgen)))) artgen_format, a.Artuni, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artgru, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi, f.Deffru AS unidad_59,f.Deffra AS fraccion_59,f.id AS id_59, a.Artesm AS esmedicamento_26, '' AS id_familia_115, '' AS cod_familia_115
            FROM    ".$wbasedato."_000026 AS a
                    LEFT JOIN
                    ".$wbasedato."_000059 AS f ON (f.Defcco = '".CENTRO_COSTO_FIJO."' AND f.Defart = a.Artcod)
            WHERE   a.Artesm = ''
                    AND a.Artest = 'on'
            GROUP BY Artcod

            UNION ALL

            SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH '\'' FROM TRIM(a.Artgen)))) artgen_format, a.Artuni, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artgru, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi, f.Deffru AS unidad_59, f.Deffra AS fraccion_59,f.id AS id_59, a.Artesm AS esmedicamento_26, d.id AS id_familia_115, d.Relfam AS cod_familia_115
            FROM    ".$wbasedato."_000026 AS a
                    LEFT JOIN
                    ".$wbasedato."_000059 AS f ON (f.Defcco = '".CENTRO_COSTO_FIJO."' AND f.Defart = a.Artcod)
                    LEFT JOIN
                    ".$wbasedato."_000115 AS d ON (a.Artcod = d.Relart AND d.Relest = 'on')
            WHERE   a.Artesm = 'on'
                    AND a.Artest = 'on'
                    AND f.id IS NULL
                    AND d.id IS NOT NULL
            GROUP BY Artcod

            UNION ALL

            SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH '\'' FROM TRIM(a.Artgen)))) artgen_format, a.Artuni, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artgru, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi, f.Deffru AS unidad_59, f.Deffra AS fraccion_59,f.id AS id_59, a.Artesm AS esmedicamento_26, d.id AS id_familia_115, d.Relfam AS cod_familia_115
            FROM    ".$wbasedato."_000026 AS a
                    LEFT JOIN
                    ".$wbasedato."_000059 AS f ON (f.Defcco = '".CENTRO_COSTO_FIJO."' AND f.Defart = a.Artcod)
                    LEFT JOIN
                    ".$wbasedato."_000115 AS d ON (a.Artcod = d.Relart AND d.Relest = 'on')
            WHERE   a.Artesm = 'on'
                    AND a.Artest = 'on'
                    AND f.id IS NULL
                    AND d.id IS NULL
            GROUP BY Artcod

            UNION ALL

            SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH '\'' FROM TRIM(a.Artgen)))) artgen_format, a.Artuni, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artgru, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi, f.Deffru AS unidad_59, f.Deffra AS fraccion_59,f.id AS id_59, a.Artesm AS esmedicamento_26, d.id AS id_familia_115, d.Relfam AS cod_familia_115
            FROM    ".$wbasedato."_000026 AS a
                    LEFT JOIN
                    ".$wbasedato."_000059 AS f ON (f.Defcco = '".CENTRO_COSTO_FIJO."' AND f.Defart = a.Artcod)
                    LEFT JOIN
                    ".$wbasedato."_000115 AS d ON (a.Artcod = d.Relart AND d.Relest = 'on')
            WHERE   a.Artesm = 'on'
                    AND a.Artest = 'on'
                    AND f.id IS NOT NULL
                    AND d.id IS NULL
            GROUP BY Artcod

            ".$complemento_select."

            ORDER BY TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH '\'' FROM TRIM(Artgen))))";
    // echo "<pre>";print_r($sql);echo "</pre>";

    $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." -  ".$sql." - ".mysql_error());

    // Construir WHERE complementario
    $where = "";
    if($letra != '')
    {
        // Los nombres genericos que empiecen por una letra
        $where = "WHERE TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(t_26.Artgen)))) LIKE (\"".$letra."%\")";
    }
    elseif($wtexto != '')
    {
        $wtexto = str_replace("*", "%", $wtexto);
        // Los nombres genericos que tengan el texto en cualquier parte del nombre genérico o que el texto sea exactamente un código de un articulo.
        $where = "WHERE TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(t_26.Artgen)))) LIKE (\"".$wtexto."%\")
                        OR t_26.Artcod = '".$wtexto."'";
    }

    // Para consultar qué articulos estan o no asociados a un código CUM en root_000064
    $sql = "
            SELECT t_26.Artcod, r_64.root_64, t_26.Artcom, t_26.Artgen, t_26.artgen_format, t_26.Artuni, t_26.Artgru, t_26.wgrupo26, t_26.Artgru, t_26.Artest, t_26.Artpos, t_26.Artreg, t_26.Artfar, t_26.Artubi, t_26.unidad_59, t_26.fraccion_59, t_26.id_59, t_26.esmedicamento_26, t_26.id_familia_115, t_26.cod_familia_115
            FROM
            (
                SELECT  Artcod, '' AS root_64, Artcom, Artgen, artgen_format, Artuni, Artgru, wgrupo26, Artest, Artpos, Artreg, Artfar, Artubi, unidad_59, fraccion_59, id_59, esmedicamento_26, id_familia_115, cod_familia_115
                FROM    ".$temp."
            ) AS t_26
            LEFT JOIN
            (
                SELECT  c.Cumint AS Artcod, c.id AS root_64, '' AS Artcom,  '' AS Artgen, '' AS artgen_format,  '' AS Artuni,  '' AS Artgru, '' AS wgrupo26,  '' AS Artest,  '' AS Artpos,  '' AS Artreg,  '' AS Artfar,  '' AS Artubi, '' AS unidad_59, '' AS fraccion_59, '' AS id_59, '' AS esmedicamento_26, '' AS id_familia_115, '' AS cod_familia_115
                FROM    root_000064 AS c
                WHERE   c.Cumemp = '".$wemp_pmla."'
                        AND c.Cumint <> ''
            ) AS r_64 ON (r_64.Artcod = t_26.Artcod)
            ".$where."
            GROUP BY t_26.Artcod
            ORDER BY t_26.artgen_format";
    // echo "<pre>";print_r($sql);echo "</pre>";

    return $sql;
}

/**

*/
function consultarArticulosSinConfigurar($conex, $wemp_pmla, $wbasedato, &$ptes)
{
    $temp = '';
    $sql = ejecutarQueryNoConfigurados($conex, $wemp_pmla, $wbasedato, $temp);
    $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." -  ".$sql." - ".mysql_error());
    $ptes = mysql_num_rows($result);
    $cont = 0;
    $oden_alfanumerico = array();

    while($row = mysql_fetch_array($result))
    {
        $index_ordena = strtoupper_mx($row['artgen_format'][0]);
        if(!array_key_exists($index_ordena,$oden_alfanumerico))
        {
            $oden_alfanumerico[$index_ordena] = array();
        }

        $arr_articulo = array(
                        'Artcod'        => $row['Artcod'],
                        'Artcom'        => $row['Artcom'],
                        'Artgen'        => $row['Artgen'],
                        'artgen_format' => $row['artgen_format'],
                        'Artuni'        => $row['Artuni'],
                        'Artgru'        => $row['Artgru'],
                        'Artest'        => $row['Artest'],
                        'Artpos'        => $row['Artpos'],
                        'Artreg'        => $row['Artreg'],
                        'Artfar'        => $row['Artfar'],
                        'Artubi'        => $row['Artubi'],
                        'wgrupo26'      => $row['wgrupo26'],
                        'unidad_59'     => $row['unidad_59'],
                        'fraccion_59'   => $row['fraccion_59'],
                        'id_59'         => $row['id_59'],
                        'esmedicamento_26' => $row['esmedicamento_26'],
                        'id_familia_115'=> $row['id_familia_115'],
                        'cod_familia_115'=> $row['cod_familia_115'],
                        'id_root_64'    => $row['root_64']
                    );
        $oden_alfanumerico[$index_ordena][] = $arr_articulo;
    }

    $html_trs = "";
    $cont = 0;
    foreach($oden_alfanumerico as $idx => $articulos)
    {
        // $css = ($html_trs == '') ? 'display:block;': 'display:none;';
        $css = 'display:none;';
        $html_trs .= '
            <div id="div_noconf_name_'.str_replace('=','_99_',base64_encode($idx)).'" class="fondo_banda2" style="margin-bottom: 2px;">
                <div onclick="verListaLetra(\'div_noconf_content_'.str_replace('=','_99_',base64_encode($idx)).'\',\''.$idx.'\')" style="cursor:pointer;">
                    <div style="width:100px;" class="fila2 textosSmall">
                    '.$idx.' ('.count($articulos).')
                    </div>
                </div>
                <div id="div_noconf_content_'.str_replace('=','_99_',base64_encode($idx)).'"" style="'.$css.'">
                    ';
                    // <table border="0" cellspacing="1" cellpadding="0" class="" style="width:100%;">
                    //     <tr class="encabezadoTabla">
                    //         <!--<td align="center">#</td>-->
                    //         <td align="center">Nombre generico</td>
                    //         <td align="center">Nombre comercial</td>
                    //         <td align="center">C&oacute;digo articulo</td>
                    //     </tr>

        foreach ($articulos as $key => $arr_articulo)
        {
            $css = ($cont % 2 == 0) ? "fila1" : "fila2";
            $esmedicamento_26 = $arr_articulo['esmedicamento_26'];
            $id_59          = $arr_articulo['id_59'];
            $id_familia_115 = $arr_articulo['id_familia_115'];
            $wcodarticulo   = $arr_articulo['Artcod'];
            $wpresentacion  = trim($arr_articulo['Artfar']);

            $wnomgenerico   = utf8_encode(str_replace('"','', $arr_articulo['Artgen']));
            $wnomgenerico   = str_replace("'","-",$wnomgenerico);
            $wnomcomercial  = utf8_encode(str_replace('"','', $arr_articulo['Artcom']));
            $wnomcomercial   = str_replace("'","-",$wnomcomercial);

            $wunidad26      = $arr_articulo['Artuni'];
            $unidad_59      = $arr_articulo['unidad_59'];
            $fraccion_59    = $arr_articulo['fraccion_59'];
            $wgrupo26       = $arr_articulo['wgrupo26'];
            $cod_familia_115= $arr_articulo['cod_familia_115'];//<!--<td>".($cont+1)."</td>-->
            $id_root_64     = $arr_articulo['id_root_64'];

            // $html_trs .= '
            //             <tr style="cursor:pointer;" class="'.$css.' textosSmall" id="tr_articulo_'.$wcodarticulo.'" onclick="verConfigurar(\''.$esmedicamento_26.'\',\''.$id_59.'\',\''.$id_familia_115.'\',\''.$wcodarticulo.'\',\''.$wpresentacion.'\',\''.$wnomgenerico.'\',\''.$wnomcomercial.'\',\''.$wunidad26.'\',\''.$unidad_59.'\',\''.$fraccion_59.'\',\''.$wgrupo26.'\',\''.$cod_familia_115.'\',\''.$id_root_64.'\');" onmouseout="trOut(this);" onmouseover="trOver(this);">
            //                 <td>'.$arr_articulo['Artgen'].'</td>
            //                 <td>'.$arr_articulo['Artcom'].'</td>
            //                 <td>'.$arr_articulo['Artcod'].'</td>
            //             </tr>';

            $cont++;
        }

                        //</table>
        $html_trs .= '
                </div>
            </div>';
    }

    $q = "DROP TEMPORARY TABLE IF EXISTS ".$temp;
    $result = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());

    return $html_trs;
}


function ejecutarQueryArtiulosConfigurados($conex, $wemp_pmla, $wbasedato, &$temp, $letra='', $wtexto='')
{
    // $temp = "articulos_no_configurados_".date("d").date("His");
    // $q = "DROP TEMPORARY TABLE IF EXISTS ".$temp;
    // $result = mysql_query($q,$conex) or die("Error: ".mysql_errno()." - ".$q." - ".mysql_error());

    // Construir WHERE complementario
    $and = "";
    if($letra != '')
    {
        // Los nombres genericos que empiecen por una letra
        $and = "AND TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) LIKE (\"".$letra."%\")";
    }
    elseif($wtexto != '')
    {
        $wtexto = str_replace("*", "%", $wtexto);
        // Los nombres genericos que tengan el texto en cualquier parte del nombre genérico o que el texto sea exactamente un código de un articulo.
        $and = "AND TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) LIKE (\"".$wtexto."%\")
                OR a.Artcod = '".$wtexto."'";
    }

    // Se quita el filtro relacionado al estado de la fracción en movhos_000059 puesto que si el articulo tiene fracción asi el registro esté inactivo
    // en movhos_000059 significa que SI tiene fracción pero por algún motivo la inactivaron, ese estado se podría modificar en la sección de Edición de Medicamentos.
    /*
         AND f.Defest = 'on'
         AND d.Relest = 'on'

        MODIFICACION 2013-03-15 17:37
    */
    $sql = "
            SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) artgen_format,
                    a.Artuni, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artgru, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi,
                    f.Deffru AS unidad_59, f.Deffra AS fraccion_59,f.id AS id_59,
                    '' AS id_66_esmedicamento,a.Artesm AS esmedicamento, d.id AS id_familia_115, d.Relfam AS cod_familia_115
            FROM    ".$wbasedato."_000026 AS a
                    INNER JOIN
                    ".$wbasedato."_000059 AS f ON (f.Defcco = '".CENTRO_COSTO_FIJO."' AND f.Defart = a.Artcod)
                    INNER JOIN
                    ".$wbasedato."_000115 AS d ON (a.Artcod = d.Relart)
                    INNER JOIN
                    root_000064 AS c ON (c.Cumemp = '".$wemp_pmla."' AND c.Cumint = a.Artcod)
            WHERE   a.Artesm = 'on'
                    ".$and."
            GROUP BY Artcod
            ORDER BY a.Artgen";

    // echo "<pre>";print_r($sql);echo "</pre>";

    // Para consultar qué articulos estan o no asociados a un código CUM en root_000064
    // $sql = "
    //         SELECT t_26.Artcod, r_64.root_64, t_26.Artcom, t_26.Artgen, t_26.artgen_format, t_26.Artuni, t_26.Artgru, t_26.wgrupo26, t_26.Artest, t_26.Artpos, t_26.Artreg, t_26.Artfar, t_26.Artubi, t_26.unidad_59, t_26.fraccion_59, t_26.id_59, t_26.esmedicamento_26, t_26.id_familia_115, t_26.cod_familia_115
    //         FROM
    //         (
    //             SELECT  Artcod, '' AS root_64, Artcom, Artgen, artgen_format, Artuni, Artgru, wgrupo26, Artest, Artpos, Artreg, Artfar, Artubi, unidad_59, fraccion_59, id_59, esmedicamento_26, id_familia_115, cod_familia_115
    //             FROM    ".$temp."
    //         ) AS t_26
    //         LEFT JOIN
    //         (
    //             SELECT  c.Cumint AS Artcod, c.id AS root_64, '' AS Artcom,  '' AS Artgen, '' AS artgen_format,  '' AS Artuni,  '' AS Artgru, '' AS wgrupo26,  '' AS Artest,  '' AS Artpos,  '' AS Artreg,  '' AS Artfar,  '' AS Artubi, '' AS unidad_59, '' AS fraccion_59, '' AS id_59, '' AS esmedicamento_26, '' AS id_familia_115, '' AS cod_familia_115
    //             FROM    root_000064 AS c
    //             WHERE   c.Cumemp = '".$wemp_pmla."'
    //                     AND c.Cumint <> ''
    //         ) AS r_64 ON (r_64.Artcod = t_26.Artcod)
    //         ".$where."
    //         GROUP BY t_26.Artcod
    //         ORDER BY t_26.artgen_format";
    // echo "<pre>";print_r($sql);echo "</pre>";

    return $sql;
}

function ejecutarQueryArtiulos26($conex, $wemp_pmla, $wbasedato, &$temp, $letra='', $wtexto='')
{
    // $temp = "articulos_no_configurados_".date("d").date("His");
    // $q = "DROP TEMPORARY TABLE IF EXISTS ".$temp;
    // $result = mysql_query($q,$conex) or die("Error: ".mysql_errno()." - ".$q." - ".mysql_error());

    // Construir WHERE complementario
    $and = "";
    if($letra != '')
    {
        // Los nombres genericos que empiecen por una letra
        $and = "AND TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) LIKE (\"".$letra."%\")";
    }
    elseif($wtexto != '')
    {
        $wtexto = str_replace("*", "%", $wtexto);
        // Los nombres genericos que tengan el texto en cualquier parte del nombre genérico o que el texto sea exactamente un código de un articulo.
        $and = "AND TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) LIKE (\"".$wtexto."%\")
                OR a.Artcod = '".$wtexto."'";
    }
    $sql = "
            SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) artgen_format,
                    a.Artuni, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artgru, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi ,a.Artesm AS esmedicamento
            FROM    ".$wbasedato."_000026 AS a
            WHERE   a.Artesm = 'on'
                    ".$and."
            GROUP BY Artcod
            ORDER BY a.Artgen";
    return $sql;
}

/**

*/
function consultarArticulosConfigurados($conex, $wemp_pmla, $wbasedato, &$ptes)
{
    $temp = '';
    $sql = ejecutarQueryArtiulosConfigurados($conex, $wemp_pmla, $wbasedato, $temp);
    $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." -  ".$sql." - ".mysql_error());
    $ptes = mysql_num_rows($result);
    $cont = 0;
    $oden_alfanumerico = array();

    while($row = mysql_fetch_array($result))
    {
        $index_ordena = strtoupper_mx($row['artgen_format'][0]);
        if(!array_key_exists($index_ordena,$oden_alfanumerico))
        {
            $oden_alfanumerico[$index_ordena] = array();
        }
        $oden_alfanumerico[$index_ordena] = $index_ordena;
    }
    // $q = "DROP TEMPORARY TABLE IF EXISTS ".$temp;
    // $result = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());

    return $oden_alfanumerico;
}

function crearListaAfabetica($conex, $wemp_pmla, $wbasedato, &$ptes)
{
    //$arr_alfabetico = unserialize(base64_decode($arr_alfabetico));
    $html_trs = "";
    $arr_alfabetico = consultarArticulosConfigurados($conex, $wemp_pmla, $wbasedato, $ptes);
    foreach($arr_alfabetico as $idx => $value)
    {
        $css = 'display:none;';
        $html_trs .= '
            <div id="div_editable_name_'.str_replace('=','_99_',base64_encode($idx)).'" class="fondo_banda2" style="margin-bottom: 2px;">
                <div onclick="verListaEditables(\'div_editable_content_'.str_replace('=','_99_',base64_encode($idx)).'\',\''.$idx.'\');" style="cursor:pointer;width:100%;">'.$idx.'</div>
                <div id="div_editable_content_'.str_replace('=','_99_',base64_encode($idx)).'"" style="'.$css.'">
                    &nbsp;
                </div>
            </div>';
    }

    return $html_trs;
}

function getMaestroFamilia($conex, $wbasedato, &$cont, $modo='html', $prefijo_campo='')
{
    $q = "  SELECT  id, Famcod, Famnom
            FROM    ".$wbasedato."_000114
            WHERE   Famest = 'on'
            ORDER BY Famnom";
    $res = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());
    $html = '';

    while($row = mysql_fetch_array($res))
    {
        if($modo == 'html')
        {
            $css = ($cont % 2 == 0) ? 'background-color:#f2f2f2;': 'background-color:#f6f6f6;';
            $html .= '
                    <tr style="'.$css.'">
                        <td>'.$row['Famcod'].' <input type="hidden" id="wfamilia114_'.$row['id'].$prefijo_campo.'" value="'.$row['Famcod'].$prefijo_campo.'"></td>
                        <td>'.$row['Famnom'].'</td>
                    </tr>';
            $css++;
        }
        else
        {
            if($html == '')
            { $html = '<option value="">Seleccione..</option>'; }
            $html .= '<option value="'.$row['Famcod'].'">'.$row['Famcod'].'-'.$row['Famnom'].'</option>';
        }
        $cont++;
    }
    return $html;
}

function getMaestroUnidades($conex, $wbasedato, &$cont, $modo='html', $prefijo_campo='')
{
    $q = "  SELECT  id, Unicod as codigo, Unides as nombre
            FROM    ".$wbasedato."_000027
            WHERE   Uniest = 'on'
            ORDER BY Unicod";
    $res = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());
    $html = '';

    while($row = mysql_fetch_array($res))
    {
        if($modo == 'html')
        {
            $css = ($cont % 2 == 0) ? 'background-color:#f2f2f2;': 'background-color:#f6f6f6;';
            $html .= '
                    <tr style="'.$css.'">
                        <td>'.$row['codigo'].' <input type="hidden" id="wunidad27_'.$row['id'].$prefijo_campo.'" name="wunidad27_'.$row['id'].$prefijo_campo.'" value="'.utf8_encode($row['codigo']).'"></td>
                        <td>'.$row['nombre'].'</td>
                    </tr>';
            $css++;
        }
        else
        {
            if($html == '')
            { $html = '<option value="">Seleccione..</option>'; }
            $html .= '<option value="'.$row['codigo'].'">'.$row['codigo'].'-'.utf8_encode($row['nombre']).'</option>';
        }
        $cont++;
    }
    return $html;
}

function getMaestroViasAdmin($conex, $wbasedato, &$cont, $modo='html')
{
    $q = "  SELECT  id, Viacod as codigo, Viades as nombre
            FROM    ".$wbasedato."_000040
            ORDER BY Viades";
    $res = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());
    $html = '';

    while($row = mysql_fetch_array($res))
    {
        if($modo == 'html')
        {
            $css = ($cont % 2 == 0) ? 'background-color:#f2f2f2;': 'background-color:#f6f6f6;';
            $html .= '
                    <tr style="'.$css.'">
                        <td>'.$row['codigo'].' <input type="hidden" id="wunidad27_'.$row['id'].'" name="wunidad27_'.$row['id'].'" value="'.$row['codigo'].'"></td>
                        <td>'.utf8_encode($row['nombre']).'</td>
                    </tr>';
            $css++;
        }
        else
        {
            // if($html == '')
            // { $html = '<option value="">Seleccione..</option>'; }
            $html .= '<option value="'.$row['codigo'].'">'.$row['codigo'].'-'.utf8_encode($row['nombre']).'</option>';
        }
        $cont++;
    }
    return $html;
}

function getMaestroCentroCostosH($conex, $wbasedato, &$cont, $modo='html')
{
    /* Filtrado solo por centro de costos hospitalarios, cirugía, urgencias, traslados que facturan*/
    $q = "  SELECT  id, Ccocod as codigo, Cconom as nombre
            FROM    ".$wbasedato."_000011
            WHERE   Ccoest = 'on'
                    AND (   Ccohos = 'on'
                        OR Ccocir = 'on'
                        OR Ccourg = 'on'
                        OR (Ccotra = 'on' AND Ccofac = 'on')
                    )
            ORDER BY Cconom";
    $res = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());
    $html = '';

    while($row = mysql_fetch_array($res))
    {
        if($modo == 'html')
        {
            $css = ($cont % 2 == 0) ? 'background-color:#f2f2f2;': 'background-color:#f6f6f6;';
            $html .= '
                    <tr style="'.$css.'">
                        <td>'.$row['codigo'].' <input type="hidden" id="wunidad27_'.$row['id'].'" name="wunidad27_'.$row['id'].'" value="'.utf8_encode($row['nombre']).'"></td>
                        <td>'.$row['nombre'].'</td>
                    </tr>';
            $css++;
        }
        elseif($modo == 'multiple')
        {
            $html .= '<option value="'.$row['codigo'].'">'.$row['codigo'].'-'.utf8_encode($row['nombre']).'</option>';
        }
        else
        {
            if($html == '')
            { $html = '<option value="">Seleccione..</option>'; }
            $cco_fijo = (CENTRO_COSTO_FIJO == $row['codigo']) ? 'selected': '';
            $html .= '<option value="'.$row['codigo'].'" '.$cco_fijo.'>'.$row['codigo'].'-'.utf8_encode($row['nombre']).'</option>';
        }
        $cont++;
    }
    return $html;
}

function getMaestroArticulos26($conex, $wemp_pmla, $wbasedato, &$cont, $modo='html', $wtexto="")
{
    $temp = '';
    $sql = ejecutarQueryArtiulos26($conex, $wemp_pmla, $wbasedato, $temp, '', $wtexto);
    $res = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." -  ".$sql." - ".mysql_error());
    $html = '';

    while($row = mysql_fetch_array($res))
    {
        $codigo = $row['Artcod'];
        $nombre = $row['artgen_format'];
        if($modo == 'html')
        {
            $css = ($cont % 2 == 0) ? 'background-color:#f2f2f2;': 'background-color:#f6f6f6;';
            $html .= '
                    <tr style="'.$css.'">
                        <td>'.$codigo.' <input type="hidden" id="warticulo_equivalente_'.$codigo.'" name="warticulo_equivalente_'.$codigo.'" value="'.utf8_encode($nombre).'"></td>
                        <td>'.$nombre.'</td>
                    </tr>';
            $css++;
        }
        elseif($modo == 'multiple')
        {
            $html .= '<option value="'.$codigo.'">'.$codigo.'-'.utf8_encode($nombre).'</option>';
        }
        else
        {
            if($html == '')
            { $html = '<option value="">Seleccione..</option>'; }
            $html .= '<option value="'.$codigo.'" >'.$codigo.'-'.utf8_encode($nombre).'</option>';
        }
        $cont++;
    }

    if($html == '' && $modo == 'options')
    { $html = '<option value="">Seleccione..</option>'; }
    return $html;
}

function getFilaMedicamentoEspecial($wespecial08_arecco, $texto_cco, $wespecial08_arecva, $wespecial08_arecde, $wespecial08_arecma, $wespecial08_areneg, $wespecial08_areapl)
{
    $fila = '   <tr id="tr_especial_'.$wespecial08_arecco.'" class="fila2 textosSmall">
                    <td align="left">
                        <input type="hidden" id="wespecial08_id_'.$wespecial08_arecco.'" id="wespecial08_id_'.$wespecial08_arecco.'" value="'.$wespecial08_arecco.'">
                        <input type="hidden" id="wespecial08_arecco_'.$wespecial08_arecco.'" id="wespecial08_arecco_'.$wespecial08_arecco.'" value="'.$wespecial08_arecco.'">
                        <span id="wespecial08_id_sp">'.$texto_cco.'</span>
                    </td>
                    <td align="center">
                        <input type="hidden" id="wespecial08_arecva_'.$wespecial08_arecco.'" id="wespecial08_arecva_'.$wespecial08_arecco.'" value="'.$wespecial08_arecva.'">
                        <span id="wespecial08_arecva_sp_'.$wespecial08_arecco.'">'.$wespecial08_arecva.'</span>
                    </td>
                    <td align="center">
                        <input type="hidden" id="wespecial08_arecde_'.$wespecial08_arecco.'" id="wespecial08_arecde_'.$wespecial08_arecco.'" value="'.$wespecial08_arecde.'">
                        <span id="wespecial08_arecde_sp_'.$wespecial08_arecco.'">'.$wespecial08_arecde.'</span>
                    </td>
                    <td align="center">
                        <input type="hidden" id="wespecial08_arecma_'.$wespecial08_arecco.'" id="wespecial08_arecma_'.$wespecial08_arecco.'" value="'.$wespecial08_arecma.'">
                        <span id="wespecial08_arecma_sp_'.$wespecial08_arecco.'">'.$wespecial08_arecma.'</span>
                    </td>
                    <td align="center">
                        <input type="hidden" id="wespecial08_areneg_'.$wespecial08_arecco.'" id="wespecial08_areneg_'.$wespecial08_arecco.'" value="'.$wespecial08_areneg.'">
                        <span id="wespecial08_areneg_sp_'.$wespecial08_arecco.'">'.$wespecial08_areneg.'</span>
                    </td>
                    <td align="center">
                        <input type="hidden" id="wespecial08_areapl_'.$wespecial08_arecco.'" id="wespecial08_areapl_'.$wespecial08_arecco.'" value="'.$wespecial08_areapl.'">
                        <span id="wespecial08_areapl_sp_'.$wespecial08_arecco.'">'.$wespecial08_areapl.'</span>
                    </td>
                    <td align="center">
                        <span id="editar_tr_especial_'.$wespecial08_arecco.'" onclick="editarTrCcoEspecial(\''.$wespecial08_arecco.'\')"
                        onmouseover="trOver(this);" onmouseout="trOut(this);" class="fondo_banda1" style="cursor:pointer;">
                            Editar
                        </span>
                    </td>
                    <td align="center">
                        <span id="elimina_tr_especial_'.$wespecial08_arecco.'" onclick="eliminarTrCcoEspecial(\'tr_especial_'.$wespecial08_arecco.'\')"
                        onmouseover="trOver(this);" onmouseout="trOut(this);" class="fondo_banda1" style="cursor:pointer;">
                            Eliminar
                        </span>
                    </td>
                </tr>';
    return $fila;
}

function getFilaMedicamentoEquivalente($wequivalente_acpart, $texto_art, $wequivalente_acpcan, $wequivalente_acpest)
{
    $fila = '   <tr id="tr_equivalente_'.$wequivalente_acpart.'" class="fila2 textosSmall">
                    <td align="left">
                        <input type="hidden" id="wequivalente_id_'.$wequivalente_acpart.'" id="wequivalente_id_'.$wequivalente_acpart.'" value="'.$wequivalente_acpart.'">
                        <input type="hidden" id="wequivalente_acpart_'.$wequivalente_acpart.'" id="wequivalente_acpart_'.$wequivalente_acpart.'" value="'.$wequivalente_acpart.'">
                        <span id="wespecial08_id_sp">'.$texto_art.'</span>
                    </td>
                    <td align="center">
                        <input type="hidden" id="wequivalente_acpcan_'.$wequivalente_acpart.'" id=wequivalente_acpcan_'.$wequivalente_acpart.'" value="'.$wequivalente_acpcan.'">
                        <span id="wequivalente_acpcan_sp_'.$wequivalente_acpart.'">'.$wequivalente_acpcan.'</span>
                    </td>
                    <td align="center">
                        <input type="hidden" id="wequivalente_acpest_'.$wequivalente_acpart.'" id="wequivalente_acpest_'.$wequivalente_acpart.'" value="'.$wequivalente_acpest.'">
                        <span id="wequivalente_acpest_sp_'.$wequivalente_acpart.'">'.$wequivalente_acpest.'</span>
                    </td>
                    <td align="center">
                        <span id="editar_tr_equivalente_'.$wequivalente_acpart.'" onclick="editarTrEquivalente(\''.$wequivalente_acpart.'\')"
                        onmouseover="trOver(this);" onmouseout="trOut(this);" class="fondo_banda1" style="cursor:pointer;">
                            Editar
                        </span>
                    </td>
                    <td align="center">
                        <span id="elimina_tr_equivalente_'.$wequivalente_acpart.'" onclick="eliminarTrEquivalente(\'tr_equivalente_'.$wequivalente_acpart.'\')"
                        onmouseover="trOver(this);" onmouseout="trOut(this);" class="fondo_banda1" style="cursor:pointer;">
                            Eliminar
                        </span>
                    </td>
                </tr>';
    return $fila;
}


if(isset($accion) && isset($form)) // se debe diferenciar por los dos o por otro diferente a $accion puesto que desde talento.php ya esta seteado $accion
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                case 'nueva_familia':
                    $data['opciones'] = "";
                    $q = "  INSERT  INTO ".$wbasedato."_000114 (Medico,Fecha_data,Hora_data,Famcod,Famnom,Famest,Seguridad)
                            VALUES  ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".strtoupper_mx($codigo_flia)."','".strtoupper_mx($nombre_flia)."','on','C-".$user_session."')";
                    if($res = mysql_query($q,$conex))
                    {
                        $id = mysql_insert_id();
                        $html = '
                            <tr style="background-color:#f6f6f6;">
                                <td>'.strtoupper_mx($codigo_flia).' <input type="hidden" id="wfamilia114_'.$id.$prefijo_campo.'" name="wfamilia114_'.$id.$prefijo_campo.'" value="'.strtoupper_mx($codigo_flia).'"></td>
                                <td>'.strtoupper_mx($nombre_flia).'</td>
                            </tr>';
                        $data['html'] = $html;

                        $cont = 0;
                        $data['opciones'] = getMaestroFamilia($conex, $wbasedato, $cont, 'options');
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "[!] No se pudo guardar el registro en el MAESTRO de familia de medicamentos";
                    }
                    $data['mensaje'] = utf8_encode($data['mensaje']);
                    echo json_encode($data);
                    break;

                case 'nueva_unidad':
                    $data['opciones'] = "";
                    $q = "  INSERT  INTO ".$wbasedato."_000027 (Medico,Fecha_data,Hora_data,Unicod,Unides,Uniest,Seguridad)
                            VALUES  ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".strtoupper_mx($codigo)."','".strtoupper_mx($nombre)."','on','C-".$user_session."')";
                    if($res = mysql_query($q,$conex))
                    {
                        $id = mysql_insert_id();
                        $html = '
                            <tr style="background-color:#f6f6f6;">
                                <td>'.strtoupper_mx($codigo).' <input type="hidden" id="wunidad27_'.$id.$prefijo_campo.'" name="wunidad27_'.$id.$prefijo_campo.'" value="'.strtoupper_mx($codigo).'"></td>
                                <td>'.strtoupper_mx(utf8_encode($nombre)).'</td>
                            </tr>';
                        $data['html'] = $html;

                        $cont = 0;
                        $data['opciones'] = getMaestroUnidades($conex, $wbasedato, $cont, 'options');
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "[!] No se pudo guardar el registro en el MAESTRO de Unidades";
                    }
                    $data['mensaje'] = utf8_encode($data['mensaje']);
                    echo json_encode($data);
                    break;

                case 'insertar_en_59':
                    $data['id_59'] = '';
                    $q59 = "
                            SELECT  id, Defest
                            FROM    ".$wbasedato."_000059
                            WHERE   Defcco = '".$wfracciones59_defcco."'
                                    AND Defart = '".$wcodarticulo."'";
                    $existe_cums = 0;
                    $id_existe_59 = '';
                    $estado_59 = '';
                    if($res59 = mysql_query($q59,$conex))
                    {
                        $existe_cums = mysql_num_rows($res59);
                        if($existe_cums > 0)
                        {
                            $row = mysql_fetch_array($res59);
                            $id_existe_59 = $row['id'];
                            $data['id_59'] = $row['id'];
                            $estado_59 = $row['Defest'];
                        }
                    }

                    // Si ya existía en off un registro en la 59 para este centro de costro y ese mismo código de medicamento, se debe cambiar de nuevo a on
                    // su estado y actualizar los datos, no se puede insertar uno nuevo porque sale un error de llave duplicada de centro_costo-cod_articulo.
                    if($id_existe_59 != '' && $estado_59 == 'off')//
                    {
                        if(in_array("*", $wfracciones59_defavc))
                        {
                            $wfracciones59_defavc = array("*");
                        }
                        // Editar datos "000059" => definición de fracciones.
                        $sql_upd = "
                            UPDATE ".$wbasedato."_000059
                                    SET Defcco = '".$wfracciones59_defcco."', Defart = '".$wcodarticulo."',
                                        Deffru = '".$wfracciones59_deffru."', Deffra = '".$wfracciones59_deffra."',
                                        Defest = '".$wfracciones59_defest."', Defven = '".$wfracciones59_defven."',
                                        Defdie = '".$wfracciones59_defdie."', Defdis = '".$wfracciones59_defdis."',
                                        Defdup = '".$wfracciones59_defdup."', Defcon = '".$wfracciones59_defcon."',
                                        Defnka = '".$wfracciones59_defnka."', Defdim = '".$wfracciones59_defdim."',
                                        Defdom = '".$wfracciones59_defdom."', Defvia = '".(($wfracciones59_defvia != '') ? implode(",",$wfracciones59_defvia) : '')."',
                                        Defipo = '".$wfracciones59_defipo."', Defrci = '".$wfracciones59_defrci."',
                                        Defcai = '".$wfracciones59_defcai."', Defcas = '".$wfracciones59_defcas."',
                                        Defesc = '".$wfracciones59_defesc."', Defavc = '".(($wfracciones59_defavc != '') ? implode(",",$wfracciones59_defavc) : '')."',
                                        Defmin = '".$wfracciones59_defmin."', Defmax = '".$wfracciones59_defmax."',
                                        Defest = '".$wfracciones59_defest."',
                                        Seguridad = 'C-".$user_session."'
                            WHERE id = '".$id_existe_59."'";
                        if($res = mysql_query($sql_upd, $conex))
                        {
                            $descripcion = "tabla:'".$wbasedato."_000059'|id:'$id_existe_59'|columnUpd:'TODOS'|columnFiltro:'id'|valueFiltro:'$id_existe_59'|obs:'Al guardar fracción se encontró una existente, Actualiza todos los campos de fracción del artículo ($wcodarticulo)'";
                            insertLog($conex, $wbasedato, $user_session, $accion, $form, 'configura_medicamento', '', $descripcion, $user_session);
                            debug_log_inline('',"<span class=\"correct\">OK</span> (Ya existía un registro en 000059 para cco $wfracciones59_defcco y articulo $wcodarticulo, estaba en estado off, se actualizó a estado -$wfracciones59_defest-) Se editó la tabla de definición de fracciones para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql_upd."<br>");
                            $data['id_59'] = $id_existe_59;
                        }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] .= "\n[!] No se pudo editar la tabla de definición de fracciones para el articulo (".$wcodarticulo.").";
                            debug_log_inline('',"<span class=\"error\">ERROR</span> (Ya existía un registro en 000059 para el cco $wfracciones59_defcco y articulo $wcodarticulo pero no se pudo editar) No se pudo editar la tabla de definición de fracciones para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql_upd."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                        }
                    }
                    elseif($id_existe_59 == '')
                    {
                        if(in_array("*", $wfracciones59_defavc))
                        {
                            $wfracciones59_defavc = array("*");
                        }
                        // se inserta un registro en la 000059 por primera vez si no existe.
                        $q = "  INSERT  INTO ".$wbasedato."_000059 (Medico,Fecha_data,Hora_data,
                                                                    Defcco,Defart,
                                                                    Deffru,Deffra,
                                                                    Defest,Defven,
                                                                    Defdie,Defdis,
                                                                    Defdup,Defcon,
                                                                    Defnka,Defdim,
                                                                    Defdom,Defvia,
                                                                    Defipo,Defrci,
                                                                    Defcai,Defcas,
                                                                    Defesc,Defavc,
                                                                    Defmin,Defmax,Seguridad)
                                VALUES  ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."',
                                                                    '".$wfracciones59_defcco."', '".$wcodarticulo."',
                                                                    '".$wfracciones59_deffru."', '".$wfracciones59_deffra."',
                                                                    '".$wfracciones59_defest."', '".$wfracciones59_defven."',
                                                                    '".$wfracciones59_defdie."', '".$wfracciones59_defdis."',
                                                                    '".$wfracciones59_defdup."', '".$wfracciones59_defcon."',
                                                                    '".$wfracciones59_defnka."', '".$wfracciones59_defdim."',
                                                                    '".$wfracciones59_defdom."', '".(($wfracciones59_defvia != '') ? implode(",",$wfracciones59_defvia) : '')."',
                                                                    '".$wfracciones59_defipo."', '".$wfracciones59_defrci."',
                                                                    '".$wfracciones59_defcai."', '".$wfracciones59_defcas."',
                                                                    '".$wfracciones59_defesc."', '".(($wfracciones59_defavc != '') ? implode(",",$wfracciones59_defavc) : '')."',
                                                                    '".$wfracciones59_defmin."', '".$wfracciones59_defmax."','C-".$user_session."')";
                        if($res = mysql_query($q,$conex))
                        {
                            $id = mysql_insert_id();
                            $data['id_59'] = $id;
                            $descripcion = "tabla:'".$wbasedato."_000059'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta artículo ($wcodarticulo)'";
                            insertLog($conex, $wbasedato, $user_session, $accion, $form, 'configura_medicamento', '', $descripcion, $user_session);
                            debug_log_inline('',"<span class=\"correct\">OK</span> Se insertó un nuevo registro en la 59: id-$id<br>&raquo; ".$q."<br>");
                        }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] = "[!] No se pudo guardar el registro en el MAESTRO de Unidades";
                            debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo insertar registro en la 59: <br>&raquo; ".$q."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                        }
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "[!] No se pudo guardar el registro en DEFINICIÓN FRACCIONES ARTICULOS, porque ya existe un registro activo para el centro de costo $wfracciones59_defcco y articulo $wcodarticulo";
                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo guardar el registro en DEFINICIÓN FRACCIONES ARTICULOS, porque ya existe un registro activo para el centro de costo $wfracciones59_defcco y articulo $wcodarticulo: <br>&raquo; ".$q59."<br>");
                    }
                    $data['mensaje'] = utf8_encode($data['mensaje']);
                    $data['debug_log'] = debug_log_inline();
                    echo json_encode($data);
                    break;

                case 'configurar_articulo_1':
                    /*  esmedicamento_26    : esmedicamento_26,
                        wtipomedicamento66  : wtipomedicamento66,
                        wfamilia26          : wfamilia26,
                        id_59               : id_59,
                        id_familia_115      : id_familia_115,
                        wcodarticulo        : wcodarticulo,
                        wpresentacion       : wpresentacion,
                        unidad_59           : unidad_59,
                        fraccion_59         : fraccion_59
                        wgrupo26*/
                    if($esmedicamento_26 == 'on' || $esmedicamento_26 == '')
                    {
                        $data['esmedicamento_26'] = $esmedicamento_26;
                        $data['id_familia_115'] = $id_familia_115;
                        $data['id_root_64'] = $id_root_64;
                        $continuar = true;
                        if($id_59 == '')
                        {
                            $continuar = false;
                        }

                        if($continuar)
                        {
                            if($esmedicamento_26 == '')
                            {
                                /*  Insertar el medicamento en la tabla "_000068" si el centro de costo del articulo es de lactario ccolac=on en "000011" y el grupo del medicamento esta en uno de los cco de ccogka de "000011"*/
                                $qlac = "
                                        SELECT  Ccocod, Ccolac, Ccogka
                                        FROM    ".$wbasedato."_000011
                                        WHERE   Ccolac = 'on'";
                                if($reslac = mysql_query($qlac,$conex))
                                {
                                    $es_de_lactario = false;
                                    /* Recorre la consulta para verificar si el grupo del articulo esta contenido en alguno de los grupos que graban a kardex (que son de lactario). */
                                    while($row_lac = mysql_fetch_array($reslac))
                                    {
                                        $grupos_lac = explode(",", $row_lac['Ccogka']);
                                        if(in_array($wgrupo26, $grupos_lac))
                                        {
                                            $es_de_lactario = true;
                                            debug_log_inline('',"<span class=\"correct\">**</span> Se determina que el grupo del articulo es de lactario porque su centro de costo es lactario y el grupo de articulo graba en kardex. <br>&raquo; ".$qlac."<br>");
                                        }
                                    }

                                    if($es_de_lactario)
                                    {
                                        /*  Consulta si el medicamento ya existe -ARTICULOS ESPECIALES DEL KARDEX- por ser un articulo o medicamento de lactario */
                                        $qlac68 = "
                                                SELECT  Arkcod, id
                                                FROM    ".$wbasedato."_000068
                                                WHERE   Arkcod = '".$wcodarticulo."'
                                                        AND Arktip = 'LC'";
                                        if($reslac68 = mysql_query($qlac68,$conex))
                                        {
                                            $existe_68 = mysql_num_rows($reslac68);
                                            /* Si el medicamento de lactario aún no existe en la tabla 000068 entonces la inserta. */
                                            if($existe_68 == 0)
                                            {
                                                debug_log_inline('',"<span class=\"correct\">OK</span> El articulo ($wcodarticulo) Es de lactario y no existe en -ARTICULOS ESPECIALES DEL KARDEX- en 000068: <br>&raquo; ".$qlac68."<br>");
                                                /*Inserta articulo en la tabla "000068" articulos especiales de kardex por determinarse que es de lactario.*/
                                                $qnlac = "
                                                        INSERT  INTO ".$wbasedato."_000068
                                                                (Medico,Fecha_data,Hora_data,
                                                                    Arkcod,Arkest,Arkcco,Arktip,Seguridad)
                                                        VALUES  ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."',
                                                                    '".$wcodarticulo."','on','".CENTRO_COSTO_FIJO."','LC','C-".$user_session."')";
                                                if($resnlac = mysql_query($qnlac,$conex))
                                                {
                                                    $id = mysql_insert_id();
                                                    $data['mensaje'] .= "\n* EL grupo (Grupo-$wgrupo26) del articulo ($wcodarticulo) Es de lactario. Este articulo se configuró también como -ARTICULOS ESPECIALES DEL KARDEX-";

                                                    $descripcion = "tabla:'".$wbasedato."_000068'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta artículo ($wcodarticulo) con id ($id)'";
                                                    insertLog($conex, $wbasedato, $user_session, $accion, $form, 'configura_medicamento', '', $descripcion, $user_session);
                                                    debug_log_inline('',"<span class=\"correct\">OK</span> EL grupo (Grupo-$wgrupo26) del articulo ($wcodarticulo) Es de lactario. Este articulo se configuró también como -ARTICULOS ESPECIALES DEL KARDEX- en 000068: id-$id<br>&raquo; ".$qnlac."<br>");
                                                }
                                                else
                                                {
                                                    $data['mensaje'] .= "\n[!] EL grupo (Grupo-$wgrupo26) del articulo ($wcodarticulo) Es de lactario. Este articulo NO SE PUDO configurar como -ARTICULOS ESPECIALES DEL KARDEX-";
                                                    debug_log_inline('',"<span class=\"error\">ERROR</span> EL grupo (Grupo-$wgrupo26) del articulo ($wcodarticulo) Es de lactario. Este articulo NO SE PUDO configurar como -ARTICULOS ESPECIALES DEL KARDEX- (000068): <br>&raquo; ".$qnlac."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                                }
                                            }
                                            else
                                            {
                                                debug_log_inline('',"<span class=\"correct\">OK</span> El articulo ($wcodarticulo) Es de lactario y YA existe en -ARTICULOS ESPECIALES DEL KARDEX- en 000068: <br>&raquo; ".$qlac68."<br>");
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo consultar los centros de costos que son de lactario (000066): <br>&raquo; ".$qlac."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                }

                                /*  Insertar el grupo del medicamento en la tabla "_000066" */
                                $q = "  INSERT  INTO ".$wbasedato."_000066
                                                (Medico,Fecha_data,Hora_data,
                                                    Melgru,Meltip,Melest,Seguridad)
                                        VALUES  ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."',
                                                    '".$wgrupo26."', '".$wtipomedicamento66."','on','C-".$user_session."')";
                                if($res = mysql_query($q,$conex))
                                {
                                    $id = mysql_insert_id();
                                    $data['esmedicamento_26'] = $id;
                                    $data['mensaje'] .= "\n* Se guardó el grupo del articulo en la tabla de GRUPOS DE MEDICAMENTOS (Grupo-$wgrupo26) ";

                                    $descripcion = "tabla:'".$wbasedato."_000066'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta grupo ($wgrupo26) con id ($id)'";
                                    insertLog($conex, $wbasedato, $user_session, $accion, $form, 'configura_medicamento', '', $descripcion, $user_session);
                                    debug_log_inline('',"<span class=\"correct\">OK</span> Se insertó un nuevo grupo de medicamento ($wgrupo26) en 000066: id-$id<br>&raquo; ".$q."<br>");
                                }
                                else
                                {
                                    $data['error'] = 1;
                                    $data['mensaje'] .= "\n[!] No se pudo guardar el nuevo grupo del articulo en al tabla de GRUPOS DE MEDICAMENTOS";
                                    debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo guardar el nuevo grupo del articulo en al tabla de GRUPOS DE MEDICAMENTOS (000066): <br>&raquo; ".$q."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                }
                            }

                            // Si no tiene detalle de familia, insertar nuevo detalle.
                            if($id_familia_115 == '')
                            {
                                $wpresentacion = (trim($wpresentacion) == '' || trim($wpresentacion) == '.') ? '00': $wpresentacion;
                                /*  Insertar el detalle de la familia para el articulo en la tabla "_000115" */
                                $q = "  INSERT  INTO ".$wbasedato."_000115
                                                (Medico,Fecha_data,Hora_data,
                                                    Relfam,Reluni,Relart,
                                                    Relcon,Relpre,Relest,Seguridad)
                                        VALUES  ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."',
                                                    '".$wfamilia26."','".$unidad_59."','".$wcodarticulo."',
                                                    '".$fraccion_59."','".$wpresentacion."','on','C-".$user_session."')";
                                if($res = mysql_query($q,$conex))
                                {
                                    $id = mysql_insert_id();
                                    $data['id_familia_115'] = $id;
                                    $data['mensaje'] .= "\n* Se guardó el detalle de familia para le articulo ($wcodarticulo)";

                                    $descripcion = "tabla:'".$wbasedato."_000115'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta artículo ($wcodarticulo) con id ($id) familia ($wfamilia26) fracción ($fraccion_59)'";
                                    insertLog($conex, $wbasedato, $user_session, $accion, $form, 'configura_medicamento', '', $descripcion, $user_session);
                                    debug_log_inline('',"<span class=\"correct\">OK</span> Se insertó el detalle de familia para el articulo $wcodarticulo en la tabla 000115: id-$id<br>&raquo; ".$q."<br>");
                                }
                                else
                                {
                                    $data['error'] = 1;
                                    $data['mensaje'] .= "\n[!] No se pudo guardar el detalle de familia para el articulo ($wcodarticulo).";
                                    debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo guardar el detalle de familia para el articulo ($wcodarticulo) (000115): <br>&raquo; ".$q."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                }
                            }

                            // Si no codigo CUMS en root_000064, insertar.
                            if($id_root_64 == '')
                            {
                                /*  id_root_64          : id_root_64,
                                    wcum64codigo        : wcum64codigo,
                                    wcum64equivale      : wcum64equivale,
                                    nomgen_root_64      : nomgen_root_64*/
                                /*  Insertar el articulo en la tabla "root_000064" */
                                $q = "  INSERT  INTO root_000064
                                                    (Medico, Fecha_data, Hora_data,
                                                    Cumcod, Cumdes, Cumequ, Cumint, Cumemp, Seguridad)
                                        VALUES  ('root','".date("Y-m-d")."','".date("H:i:s")."',
                                                    '".$wcum64codigo."', '".trim($nomgen_root_64)."', '".$wcum64equivale."', '".$wcodarticulo."', '".$wemp_pmla."', 'C-root')";
                                if($res = mysql_query($q,$conex))
                                {
                                    $id = mysql_insert_id();
                                    $data['id_root_64'] = $id;
                                    $data['mensaje'] .= "\n* Se guardó código CUM para el articulo ($wcodarticulo)";

                                    $descripcion = "tabla:'root_000064'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta artículo ($wcodarticulo) con id ($id) ($wcum64codigo,$wcodarticulo,$wemp_pmla)'";
                                    insertLog($conex, $wbasedato, $user_session, $accion, $form, 'configura_medicamento', '', $descripcion, $user_session);
                                    debug_log_inline('',"<span class=\"correct\">OK</span> Se guardó código CUM para el articulo ($wcodarticulo) en la tabla root_000064: id-$id<br>&raquo; ".$q."<br>");
                                }
                                else
                                {
                                    $data['error'] = 1;
                                    $data['mensaje'] .= "\n[!] No se pudo guardar código CUM para el articulo ($wcodarticulo).";
                                    debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo guardar código CUM para el articulo ($wcodarticulo) (root_000064): <br>&raquo; ".$q."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                }
                            }

                        }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] = "\n[!] No se pudo guardar la configuración porque el articulo no tiene una definición de fracciones.";
                            debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo guardar la configuración porque el articulo no tiene una definición de fracciones. (en la tabla 000059): <br>&raquo; ".$q."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                        }
                    }
                    elseif($esmedicamento_26 == 'off')
                    {
                        $q = "  UPDATE  ".$wbasedato."_000026
                                        SET Artesm = 'off'
                                WHERE   Artcod = '".$wcodarticulo."'";
                        if($res = mysql_query($q,$conex))
                        {
                            $data['mensaje'] .= "\n* El articulo con código: $wcodarticulo se configuró como -No es medicamento-";
                            $descripcion = "tabla:'".$wbasedato."_000026'|id:''|columnUpd:'Artesm'|columnFiltro:'Artcod'|valueFiltro:'$wcodarticulo'|obs:'Actualiza artículo ($wcodarticulo) Cambia -Es Medicamento- a off'";
                            insertLog($conex, $wbasedato, $user_session, $accion, $form, 'configura_medicamento', '', $descripcion, $user_session);
                            debug_log_inline('',"<span class=\"correct\">OK</span> El articulo con código: $wcodarticulo se configuró como -No es medicamento- ".$wbasedato."_000064: <br>&raquo; ".$q."<br>");
                        }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] .= "\n[!] El articulo con código: $wcodarticulo se pudo configurar como -No es medicamento- ocurrió algún problema al guardar.";
                            debug_log_inline('',"<span class=\"error\">ERROR</span> El articulo con código: $wcodarticulo se pudo configurar como -No es medicamento- ocurrió algún problema al guardar.: <br>&raquo; ".$q."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                        }
                    }

                    $data['mensaje'] = $data['mensaje'];
                    $data['debug_log'] = debug_log_inline();
                    echo json_encode($data);
                    break;

                default:
                    $data['mensaje'] = $no_exec_sub;
                    $data['error'] = 1;
                    echo json_encode($data);
                break;
            }
            break;
        case 'load':
            switch ($form) {
                case 'listar_por_letra':
                    $temp = '';
                    $wtexto = (isset($wtexto)) ? $wtexto: '';
                    $sql = ejecutarQueryArtiulosConfigurados($conex, $wemp_pmla, $wbasedato, $temp, $wletra, $wtexto);

                    // $sql = "
                    //         SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) artgen_format,
                    //                 a.Artuni, a.Artgru, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi,
                    //                 f.Deffru AS unidad_59, f.Deffra AS fraccion_59,f.id AS id_59,
                    //                 g.id AS id_66_esmedicamento,g.Melgru AS esmedicamento, d.id AS id_familia_115, d.Relfam AS cod_familia_115
                    //         FROM    ".$wbasedato."_000026 AS a
                    //                 INNER JOIN
                    //                 ".$wbasedato."_000066 AS g ON (g.Melgru = SUBSTRING_INDEX(a.Artgru, '-', 1) AND a.Artest = 'on' AND g.Melest = 'on')
                    //                 INNER JOIN
                    //                 ".$wbasedato."_000059 AS f ON (f.Defcco = '".CENTRO_COSTO_FIJO."' AND f.Defart = a.Artcod AND f.Defest = 'on')
                    //                 INNER JOIN
                    //                 ".$wbasedato."_000115 AS d ON (a.Artcod = d.Relart AND d.Relest = 'on')
                    //         WHERE   f.id IS NOT NULL
                    //                 AND d.id IS NOT NULL
                    //                 AND TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) LIKE (\"".$wletra."%\")
                    //         GROUP BY Artcod
                    //         ORDER BY a.Artgen";
                    $html_trs = '';
                    if($res = mysql_query($sql, $conex))
                    {
                        $cod_html = '
                            <table border="0" cellspacing="1" cellpadding="0" class="" style="width:100%;">
                                <tr class="encabezadoTabla">
                                    <!--<td align="center">#</td>-->
                                    <td align="center">Nombre generico</td>
                                    <td align="center">Nombre comercial</td>
                                    <td align="center">C&oacute;digo articulo</td>
                                    <td align="center">Estado</td>
                                </tr>';

                        $pref_buscar = "";
                        if($wtexto=='')
                        {
                            $html_trs .= $cod_html;
                        }
                        else
                        { $pref_buscar = "busca_noconf_"; }

                        $cont = 0;
                        while($row = mysql_fetch_array($res))
                        {
                            $css = ($cont % 2 == 0) ? "fila1" : "fila2";
                            // $esmedicamento_26 = $row['esmedicamento'];
                            // $wpresentacion  = trim($row['Artfar']);

                            // $wnomgenerico   = utf8_encode(str_replace('"','', $row['Artgen']));
                            // $wnomgenerico   = str_replace("'","-",$wnomgenerico);
                            // $wnomcomercial  = utf8_encode(str_replace('"','', $row['Artcom']));
                            // $wnomcomercial   = str_replace("'","-",$wnomcomercial);

                            // $wunidad26      = $row['Artuni'];
                            // $unidad_59      = $row['unidad_59'];
                            // $fraccion_59    = $row['fraccion_59'];
                            // $wgrupo26       = $row['wgrupo26'];
                            // $cod_familia_115= $row['cod_familia_115'];//<!--<td>".($cont+1)."</td>-->
                            // $id_66_esmedicamento= $row['id_66_esmedicamento'];//<!--<td>".($cont+1)."</td>-->
                            $wcodarticulo   = $row['Artcod'];
                            $id_59          = $row['id_59'];
                            $id_familia_115 = $row['id_familia_115'];

                            $html_trs .= '
                                <tr style="cursor:pointer;" class="'.$css.' textosSmall" id="tr_editable_articulo_'.$pref_buscar.$wcodarticulo.'" onclick="verEditar(\''.$wcodarticulo.'\',\''.$id_59.'\',\''.$id_familia_115.'\');" onmouseout="trOut(this);" onmouseover="trOver(this);">
                                    <td>'.($cont+1).') '.$row['Artgen'].'</td>
                                    <td>'.utf8_encode($row['Artcom']).'</td>
                                    <td>'.utf8_encode($row['Artcod']).'</td>
                                    <td align="center">'.(($row['Artest']=='on') ? '<span style="color:green;font-weight:bold;">Activo</span>': '<span style="color:Orange;font-weight:bold;">Inactivo</span>').'</td>
                                </tr>';
                            $cont++;
                        }

                        $cod_html = '
                            </table>';

                        if($wtexto=='')
                        {
                            $html_trs .= $cod_html;
                        }

                        if($cont == '')
                        {
                            $html_trs = '<div style="text-align:center;font-weight:bold;">No hay datos..</div>';
                        }
                        elseif($wtexto!='')
                        {
                            $css_busc = ($cont > 15) ? "height: 250px;overflow:scroll;": "";
                            $html_trs ='
                                <div style="text-align:left;'.$css_busc.'">
                                    <table border="0" cellspacing="1" cellpadding="0" class="" style="width:100%;">
                                        <tr class="encabezadoTabla">
                                            <!--<td align="center">#</td>-->
                                            <td align="center">Nombre generico</td>
                                            <td align="center">Nombre comercial</td>
                                            <td align="center">C&oacute;digo articulo</td>
                                            <td align="center">Estado</td>
                                        </tr>
                                        '.$html_trs.'
                                    </table>
                                </div>';
                        }

                        $data['html'] = $html_trs;
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "\n[!] No se pudo generar la lista iniciando por (".$wletra.").";
                        debug_log_inline('',"<span class=\"error\">ERROR</span>No se pudo generar la lista iniciando por (".$wletra.").: <br>&raquo; ".$sql."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                    }
                    // $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." -  ".$sql." - ".mysql_error());
                    // $ptes = mysql_num_rows($result);
                    // $data['mensaje'] = $no_exec_sub;
                    // $data['error'] = 1;
                    break;

                case 'listar_por_letra_noconf':

                    $temp = '';
                    $wtexto = (isset($wtexto)) ? $wtexto: '';
                    $sql = ejecutarQueryNoConfigurados($conex, $wemp_pmla, $wbasedato, $temp, $wletra, $wtexto);
                    // $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." -  ".$sql." - ".mysql_error());
                    // $ptes = mysql_num_rows($result);


                    $oden_alfanumerico = array();
                    if($result = mysql_query($sql, $conex))
                    {

                        while($row = mysql_fetch_array($result))
                        {
                            $index_ordena = strtoupper_mx($row['artgen_format'][0]);
                            if(!array_key_exists($index_ordena,$oden_alfanumerico))
                            {
                                $oden_alfanumerico[$index_ordena] = array();
                            }

                            $arr_articulo = array(
                                            'Artcod'        => $row['Artcod'],
                                            'Artcom'        => $row['Artcom'],
                                            'Artgen'        => $row['Artgen'],
                                            'artgen_format' => $row['artgen_format'],
                                            'Artuni'        => $row['Artuni'],
                                            'Artgru'        => $row['Artgru'],
                                            'Artest'        => $row['Artest'],
                                            'Artpos'        => $row['Artpos'],
                                            'Artreg'        => $row['Artreg'],
                                            'Artfar'        => $row['Artfar'],
                                            'Artubi'        => $row['Artubi'],
                                            'wgrupo26'      => $row['wgrupo26'],
                                            'unidad_59'     => $row['unidad_59'],
                                            'fraccion_59'   => $row['fraccion_59'],
                                            'id_59'         => $row['id_59'],
                                            'esmedicamento_26' => $row['esmedicamento_26'],
                                            'id_familia_115'=> $row['id_familia_115'],
                                            'cod_familia_115'=> $row['cod_familia_115'],
                                            'id_root_64'    => $row['root_64']
                                        );
                            $oden_alfanumerico[$index_ordena][] = $arr_articulo;
                        }

                        $html_trs = "";
                        $cont = 0;
                        foreach($oden_alfanumerico as $idx => $articulos)
                        {
                            $css = ($html_trs == '') ? 'display:block;': 'display:none;';

                            $cod_html = '
                                            <table border="0" cellspacing="1" cellpadding="0" class="" style="width:100%;">
                                                <tr class="encabezadoTabla">
                                                    <!--<td align="center">#</td>-->
                                                    <td align="center">Nombre generico</td>
                                                    <td align="center">Nombre comercial</td>
                                                    <td align="center">C&oacute;digo articulo</td>
                                                    <td align="center">&nbsp;</td>
                                                </tr>';

                            $pref_buscar = "";
                            if($wtexto=='')
                            {
                                $html_trs .= $cod_html;
                            }
                            else
                            { $pref_buscar = "busca_noconf_"; }

                            foreach ($articulos as $key => $arr_articulo)
                            {
                                $css = ($cont % 2 == 0) ? "fila1" : "fila2";
                                $esmedicamento_26 = $arr_articulo['esmedicamento_26'];
                                $id_59          = $arr_articulo['id_59'];
                                $id_familia_115 = $arr_articulo['id_familia_115'];
                                $wcodarticulo   = $arr_articulo['Artcod'];
                                $wpresentacion  = trim($arr_articulo['Artfar']);

                                $wnomgenerico   = utf8_encode(str_replace('"','', $arr_articulo['Artgen']));
                                $wnomgenerico   = str_replace("'","-",$wnomgenerico);
                                $wnomcomercial  = utf8_encode(str_replace('"','', $arr_articulo['Artcom']));
                                $wnomcomercial   = str_replace("'","-",$wnomcomercial);

                                $wunidad26      = $arr_articulo['Artuni'];
                                $unidad_59      = $arr_articulo['unidad_59'];
                                $fraccion_59    = $arr_articulo['fraccion_59'];
                                $wgrupo26       = $arr_articulo['wgrupo26'];
                                $wnomgrupo26    = $arr_articulo['Artgru'];
                                $cod_familia_115= $arr_articulo['cod_familia_115'];//<!--<td>".($cont+1)."</td>-->
                                $id_root_64     = $arr_articulo['id_root_64'];

                                $falta_conf = '';
                                $falta_conf .= '&nbsp;<span class="'.(($esmedicamento_26 == '') ? 'faltaEsMed' : '').'">&nbsp;</span>';
                                $falta_conf .= '&nbsp;<span class="'.(($id_59 == '') ? 'falta59' : '').'">&nbsp;</span>';
                                $falta_conf .= '&nbsp;<span class="'.(($id_familia_115 == '') ? 'falta115' : '').'">&nbsp;</span>';
                                $falta_conf .= '&nbsp;<span class="'.(($id_root_64 == '') ? 'faltaCum' : '').'">&nbsp;</span>';

                                $html_trs .= '
                                            <tr style="cursor:pointer;" class="'.$css.' textosSmall" id="tr_articulo_'.$pref_buscar.$wcodarticulo.'" onclick="verConfigurar(\''.$esmedicamento_26.'\',\''.$id_59.'\',\''.$id_familia_115.'\',\''.$wcodarticulo.'\',\''.$wpresentacion.'\',\''.$wnomgenerico.'\',\''.$wnomcomercial.'\',\''.$wunidad26.'\',\''.$unidad_59.'\',\''.$fraccion_59.'\',\''.$wgrupo26.'\',\''.$wnomgrupo26.'\',\''.$cod_familia_115.'\',\''.$id_root_64.'\');" onmouseout="trOut(this);" onmouseover="trOver(this);">
                                                <td>'.($cont+1).') '.utf8_encode($arr_articulo['Artgen']).'</td>
                                                <td>'.utf8_encode($arr_articulo['Artcom']).'</td>
                                                <td>'.$arr_articulo['Artcod'].'</td>
                                                <td>'.$falta_conf.'</td>
                                            </tr>';
                                $cont++;
                            }

                            $cod_html = '
                                    </table>';

                            if($wtexto=='')
                            {
                                $html_trs .= $cod_html;
                            }
                        }

                        if($cont == 0)
                        {
                            $html_trs = '<div style="text-align:center;font-weight:bold;">No hay datos..</div>';
                        }
                        elseif($wtexto!='')
                        {
                            // Solo si es búsqueda entonces se crea un nuevo encabezado, el anterior se repite creando el orden alfabético.
                            $css_busc = ($cont > 15) ? "height: 250px;overflow:scroll;": "";
                            $html_trs ='
                                <div style="text-align:left;'.$css_busc.'">
                                    <table border="0" cellspacing="1" cellpadding="0" class="" style="width:100%;">
                                        <tr class="encabezadoTabla">
                                            <!--<td align="center">#</td>-->
                                            <td align="center">Nombre generico</td>
                                            <td align="center">Nombre comercial</td>
                                            <td align="center">C&oacute;digo articulo</td>
                                            <td align="center">&nbsp;</td>
                                        </tr>
                                        '.$html_trs.'
                                    </table>
                                </div>';
                        }

                        $data['html'] = $html_trs;

                        $q = "DROP TEMPORARY TABLE IF EXISTS ".$temp;
                        $result = mysql_query($q,$conex) or die("Error: ".mysql_errno()." -  ".$q." - ".mysql_error());
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "\n[!] No se pudo generar la lista iniciando por (".$wletra.").";
                        debug_log_inline('',"<span class=\"error\">ERROR</span>No se pudo generar la lista iniciando por (".$wletra.").: <br>&raquo; ".$sql."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                    }
                    // $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." -  ".$sql." - ".mysql_error());
                    // $ptes = mysql_num_rows($result);
                    // $data['mensaje'] = $no_exec_sub;
                    // $data['error'] = 1;
                    break;

                case 'cargar_editar_articulo':
                    /*
                    wcodarticulo: wcodarticulo,
                    id_66_esmedicamento:id_66_esmedicamento,
                    id_59       : id_59,
                    id_familia_115:id_familia_115*/

                    // Se quita el filtro relacionado al estado ".$wbasedato."_000066
                    /*
                         AND a.Artest = 'on' AND g.Melest = 'on'

                        MODIFICACION 2013-03-15 17:37
                    */
                    $sql = "
                            SELECT  a.Artcod, a.Artcom, a.Artgen, TRIM(BOTH '\"' FROM TRIM(TRIM(BOTH \"'\" FROM TRIM(a.Artgen)))) artgen_format,
                                    a.Artuni, SUBSTRING_INDEX(a.Artgru, '-', 1) AS wgrupo26, a.Artgru, a.Artest, a.Artpos, a.Artreg, a.Artfar, a.Artubi,
                                    f.Deffru AS unidad_59, f.Deffra AS fraccion_59,f.id AS id_59,
                                    f.Defcco, f.Defart, f.Deffra, f.Deffru, f.Defest, f.Defven, f.Defdie,
                                    f.Defdis, f.Defdup, f.Defcon, f.Defnka, f.Defdim, f.Defdom, f.Defvia,
                                    f.Defipo, f.Defrci, f.Defcai, f.Defcas, f.Defesc, f.Defavc, f.Defmin, f.Defmax,
                                    d.id AS id_66_esmedicamento, d.id AS id_familia_115, d.Relfam AS cod_familia_115,
                                    a.Artesm AS esmedicamento, g.Meltip AS wtipomedicamento66,
                                    lac.id as id_68_edit,
                                    c.id as id_root_64, c.Cumcod AS wcum64codigo, c.Cumdes AS wcum64descripcion, c.Cumequ AS wcum64equivale
                            FROM    ".$wbasedato."_000026 AS a
                                    INNER JOIN
                                    ".$wbasedato."_000066 AS g ON (g.Melgru = SUBSTRING_INDEX(a.Artgru, '-', 1))
                                    INNER JOIN
                                    ".$wbasedato."_000059 AS f ON (f.id = '".$id_59."')
                                    INNER JOIN
                                    ".$wbasedato."_000115 AS d ON (d.id = '".$id_familia_115."')
                                    LEFT JOIN
                                    ".$wbasedato."_000068 AS lac ON (lac.Arkcod = '".$wcodarticulo."' AND lac.Arkcco = '".CENTRO_COSTO_FIJO."' AND Arkest = 'on')
                                    LEFT JOIN
                                    root_000064 AS c ON (c.Cumint = a.Artcod AND c.Cumemp = '".$wemp_pmla."')
                            WHERE   a.Artcod = '".$wcodarticulo."'
                                    AND a.Artesm = 'on'
                            GROUP BY Artcod
                            ORDER BY a.Artgen";
                    debug_log_inline('',"<span class=\"correct\">SQL</span> SQL: <br>&raquo; ".$sql."<br>");

                    // $data['html'] = "<pre>".$sql."</pre>";
                    $html_trs = '';
                    $campos_articulo = array(
                                                "westado_medicamento_edit"  => "",
                                                "div_estado_edit"           => "",
                                                "wesmedicamento_edit"       => "",
                                                "wnomgrupo26"               => "",
                                                "wnomgenerico_edit"         => "",
                                                "wnomcomercial_edit"        => "",
                                                "wcodarticulo_view_edit"    => "",
                                                "wnomcomercial_view_edit"   => "",
                                                "wunidad26_view_edit"       => "",
                                                "wtipomedicamento66_edit"   => "",
                                                "wfamilia26_edit"           => "",
                                                "id_68_edit"                => "",
                                                "wpresentacion_edit"        => "",
                                                "wcodarticulo_fraccion_view_edit" => "",
                                                "wfracciones59_defcco_edit" => "",
                                                "wfracciones59_defart_edit" => "",
                                                "wfracciones59_deffra_edit" => "",
                                                "wfracciones59_deffru_edit" => "",
                                                "wfracciones59_defest_edit" => "",
                                                "wfracciones59_defven_edit" => "",
                                                "wfracciones59_defdie_edit" => "",
                                                "wfracciones59_defdis_edit" => "",
                                                "wfracciones59_defdup_edit" => "",
                                                "wfracciones59_defcon_edit" => "",
                                                "wfracciones59_defnka_edit" => "",
                                                "wfracciones59_defdim_edit" => "",
                                                "wfracciones59_defdom_edit" => "",
                                                "wfracciones59_defvia_edit" => "", // multiple
                                                "wfracciones59_defipo_edit" => "",
                                                "wfracciones59_defrci_edit" => "",
                                                "wfracciones59_defcai_edit" => "",
                                                "wfracciones59_defcas_edit" => "",
                                                "wfracciones59_defesc_edit" => "",
                                                "wfracciones59_defavc_edit" => "",  //multiple
                                                "wfracciones59_defmin_edit" => "",
                                                "wfracciones59_defmax_edit" => "",
                                                "wesdestock91_edit"         => "off",  //checkbox
                                                "wcentrocostos91_edit"      => "",     //multiple
                                                "wespecial08_edit"          => "off",  //multiple
                                                "wequivalente_edit"         => "off",  //multiple
                                                "id_root_64_edit"           => "",
                                                "wcum64codigo_edit"         => "",
                                                "wcum64equivale_edit"       => "",
                                                "wcum64descripcion_edit"    => "",
                                                "nomgen_root_64_edit"       => ""
                                                );
                    if($res = mysql_query($sql, $conex))
                    {
                        if(mysql_num_rows($res) > 0)
                        {
                            $row = mysql_fetch_array($res);

                            $std = (($row["Artest"] == 'on') ? ' <span style="color:green;font-weight:bold;">Activo</span>': ' <span style="color:orange;font-weight:bold;">Inactivo</span>');

                            $campos_articulo["westado_medicamento_edit"       ] = $row["Artest"];
                            $campos_articulo["div_estado_edit"                ] = $std;
                            $campos_articulo["wesmedicamento_edit"            ] = (($row["esmedicamento"] != '' && $row["esmedicamento"] != 'off') ? 'on': 'off');
                            $campos_articulo["wnomgrupo26_edit"               ] = $row["Artgru"];
                            $campos_articulo["wnomgenerico_edit"              ] = $row["Artgen"];
                            $campos_articulo["wnomcomercial_edit"             ] = $row["Artcom"];
                            $campos_articulo["wcodarticulo_view_edit"         ] = $row["Artcod"];
                            $campos_articulo["wnomcomercial_view_edit"        ] = $row["Artcom"];
                            $campos_articulo["wunidad26_view_edit"            ] = $row["Artuni"];
                            $campos_articulo["wtipomedicamento66_edit"        ] = $row["wtipomedicamento66"];
                            $campos_articulo["wfamilia26_edit"                ] = $row["cod_familia_115"];
                            $campos_articulo["id_68_edit"                     ] = $row["id_68_edit"];
                            $campos_articulo["wpresentacion_edit"             ] = trim($row["Artfar"]);
                            $campos_articulo["wcodarticulo_fraccion_view_edit"] = $row["Artcod"];
                            $campos_articulo["wfracciones59_defcco_edit"      ] = $row["Defcco"];
                            $campos_articulo["wfracciones59_defart_edit"      ] = $row["Defart"];
                            $campos_articulo["wfracciones59_deffra_edit"      ] = $row["Deffra"];
                            $campos_articulo["wfracciones59_deffru_edit"      ] = $row["Deffru"];
                            $campos_articulo["wfracciones59_defest_edit"      ] = $row["Defest"]; // chec
                            $campos_articulo["wfracciones59_defven_edit"      ] = $row["Defven"]; // check
                            $campos_articulo["wfracciones59_defdie_edit"      ] = $row["Defdie"];
                            $campos_articulo["wfracciones59_defdis_edit"      ] = $row["Defdis"]; // chec
                            $campos_articulo["wfracciones59_defdup_edit"      ] = $row["Defdup"]; // chec
                            $campos_articulo["wfracciones59_defcon_edit"      ] = $row["Defcon"]; // chec
                            $campos_articulo["wfracciones59_defnka_edit"      ] = $row["Defnka"]; // check
                            $campos_articulo["wfracciones59_defdim_edit"      ] = trim($row["Defdim"]);
                            $campos_articulo["wfracciones59_defdom_edit"      ] = trim($row["Defdom"]);
                            $campos_articulo["wfracciones59_defvia_edit"      ] = $row["Defvia"]; // multple
                            $campos_articulo["wfracciones59_defipo_edit"      ] = $row["Defipo"]; // chec
                            $campos_articulo["wfracciones59_defrci_edit"      ] = $row["Defrci"]; // check
                            $campos_articulo["wfracciones59_defcai_edit"      ] = $row["Defcai"];
                            $campos_articulo["wfracciones59_defcas_edit"      ] = $row["Defcas"];
                            $campos_articulo["wfracciones59_defesc_edit"      ] = $row["Defesc"];
                            $campos_articulo["wfracciones59_defavc_edit"      ] = $row["Defavc"]; //multiple
                            $campos_articulo["wfracciones59_defmin_edit"      ] = $row["Defmin"];
                            $campos_articulo["wfracciones59_defmax_edit"      ] = $row["Defmax"];
                            $campos_articulo["id_root_64_edit"                ] = $row["id_root_64"];
                            $campos_articulo["wcum64codigo_edit"              ] = $row["wcum64codigo"];
                            $campos_articulo["wcum64equivale_edit"            ] = $row["wcum64equivale"];
                            $campos_articulo["wcum64descripcion_edit"         ] = ($row["id_root_64"] == '') ? trim($row["Artgen"]) : $row["wcum64descripcion"];
                            $campos_articulo["nomgen_root_64_edit"            ] = ($row["id_root_64"] == '') ? trim($row["Artgen"]) : $row["wcum64descripcion"];

                            // Consulta datos de STOCK
                            $q_stk = "  SELECT  id, Arscod, Arscco
                                        FROM    ".$wbasedato."_000091
                                        WHERE   Arscod = '".$wcodarticulo."'
                                                AND Arsest = 'on'";
                            if($resstk = mysql_query($q_stk, $conex))
                            {
                                debug_log_inline('',"<span class=\"correct\">OK</span> Buscando datos de STOCK articulo (".$wcodarticulo.") 000091: <br>&raquo; ".$q_stk."<br>");
                                $arr_ccos_stock = array();
                                while($rowstk = mysql_fetch_array($resstk))
                                {
                                    $arr_ccos_stock[] = $rowstk['Arscco'];
                                }

                                if(count($arr_ccos_stock) > 0)
                                {
                                    $campos_articulo['wesdestock91_edit'] = 'on';
                                    $campos_articulo['wcentrocostos91_edit'] = implode(",", $arr_ccos_stock);
                                    debug_log_inline('',"<span class=\"correct\">OK</span> SI HAY Datos en STOCK articulo (".$wcodarticulo.") 000091: <br>&raquo; ".$q_stk."<br>");
                                }
                            }
                            else
                            {
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo cargar posibles datos del STOCK articulo (".$wcodarticulo.") 000091: <br>&raquo; ".$q_stk."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }

                            // Consulta datos de medicamentos especiales
                            $q_esp = "  SELECT  id, Arecco, Areces, Arecod, Arecva, Arecde, Arecma, Areneg, Areapl
                                        FROM    ".$wbasedato."_000008
                                        WHERE   Arecod = '".$wcodarticulo."'";
                            if($resesp = mysql_query($q_esp, $conex))
                            {
                                /**************************************************************/
                                // Consulta centros de costos para recuperar sus nombres
                                $qc = "  SELECT  id, Ccocod as codigo, Cconom as nombre
                                        FROM    ".$wbasedato."_000011
                                        WHERE   Ccoest = 'on'
                                        ORDER BY Cconom";
                                $resc = mysql_query($qc,$conex);
                                $nombs_cco = array();

                                while($rowc = mysql_fetch_array($resc))
                                {
                                    $nombs_cco[$rowc['codigo']] = $rowc['nombre'];
                                }
                                /**************************************************************/

                                debug_log_inline('',"<span class=\"correct\">OK</span> Buscando datos de medicamentos especiales para el articulo (".$wcodarticulo.") 000008: <br>&raquo; ".$q_esp."<br>");
                                $arr_ccos_esp = array();
                                while($roweps = mysql_fetch_array($resesp))
                                {
                                    $wespecial08_arecco = $roweps['Arecco'];
                                    //$texto_cco = $roweps[''];
                                    $wespecial08_arecva = $roweps['Arecva'];
                                    $wespecial08_arecde = $roweps['Arecde'];
                                    $wespecial08_arecma = $roweps['Arecma'];
                                    $wespecial08_areneg = $roweps['Areneg'];
                                    $wespecial08_areapl = $roweps['Areapl'];

                                    $arr_ccos_esp[] = getFilaMedicamentoEspecial($wespecial08_arecco,$nombs_cco[$roweps['Arecco']], $wespecial08_arecva, $wespecial08_arecde, $wespecial08_arecma, $wespecial08_areneg, $wespecial08_areapl);
                                }

                                if(count($arr_ccos_esp) > 0)
                                {
                                    $campos_articulo['wespecial08_edit'] = 'on';
                                    $campos_articulo['wespecial08_edit_html'] = implode("", $arr_ccos_esp);
                                    debug_log_inline('',"<span class=\"correct\">OK</span> SI HAY Datos de medicamentos especiales del articulo (".$wcodarticulo.") 000008: <br>&raquo; ".$q_esp."<br>");
                                }
                            }
                            else
                            {
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo cargar posibles datos de medicamentos especiales para el articulo (".$wcodarticulo.") 000008: <br>&raquo; ".$q_esp."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }

                            // Consulta registro de articulos relacionados al medicamento.
                            $q_esp = "  SELECT  id, Acppro, Acpart, Acpcan, Acpest
                                        FROM    ".$wbasedato."_000153
                                        WHERE   Acppro = '".$wcodarticulo."'";
                            if($resesp = mysql_query($q_esp, $conex))
                            {
                                /**************************************************************/
                                // Consulta medicamentos para consultar sus nombres
                                /*$qc = "  SELECT  id, Ccocod as codigo, Cconom as nombre
                                        FROM    ".$wbasedato."_000011
                                        WHERE   Ccoest = 'on'
                                        ORDER BY Cconom";*/
                                $temp = '';
                                $sql = ejecutarQueryArtiulos26($conex, $wemp_pmla, $wbasedato, $temp);
                                $resc = mysql_query($sql,$conex);
                                $nombs_art = array();

                                while($rowc = mysql_fetch_array($resc))
                                {
                                    $nombs_art[$rowc['Artcod']] = $rowc['artgen_format'];
                                }
                                /**************************************************************/

                                debug_log_inline('',"<span class=\"correct\">OK</span> Buscando datos de medicamentos equivalentes para el articulo (".$wcodarticulo.") 000153: <br>&raquo; ".$q_esp."<br>");
                                $arr_equivalente = array();
                                while($roweps = mysql_fetch_array($resesp))
                                {
                                    $wequivalente_acpart = $roweps['Acpart'];
                                    //$texto_cco = $roweps[''];
                                    $wequivalente_acpcan = $roweps['Acpcan'];
                                    $wequivalente_acpest = $roweps['Acpest'];

                                    $arr_equivalente[] = getFilaMedicamentoEquivalente($wequivalente_acpart, $nombs_art[$roweps['Acpart']], $wequivalente_acpcan, $wequivalente_acpest);
                                }

                                if(count($arr_equivalente) > 0)
                                {
                                    $campos_articulo['wequivalente_edit'] = 'on';
                                    $campos_articulo['wequivalente_edit_html'] = implode("", $arr_equivalente);
                                    debug_log_inline('',"<span class=\"correct\">OK</span> SI HAY Datos de articulos asociados al medicamento (".$wcodarticulo.") 000153: <br>&raquo; ".$q_esp."<br>");
                                }
                            }
                            else
                            {
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo cargar posibles registros de articulos asociados al medicamento (".$wcodarticulo.") 000153: <br>&raquo; ".$q_esp."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "\n[!] No se pudo cargar los datos para el articulo (".$wcodarticulo.").";
                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo cargar los datos para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                    }
                    $data['debug_log'] = debug_log_inline();
                    $data['campos_articulo'] = $campos_articulo;
                    break;

                case 'agregar_especiales':
                    /*wespecial08_id : wespecial08_id,
                    wespecial08_arecco : wespecial08_arecco,
                    wespecial08_arecva : wespecial08_arecva,
                    wespecial08_arecde : wespecial08_arecde,
                    wespecial08_arecma : wespecial08_arecma,
                    wespecial08_areneg : wespecial08_areneg,
                    wespecial08_areapl : wespecial08_areapl*/
                    $data['html'] = getFilaMedicamentoEspecial($wespecial08_arecco, $texto_cco, $wespecial08_arecva, $wespecial08_arecde, $wespecial08_arecma, $wespecial08_areneg, $wespecial08_areapl);
                    debug_log_inline('',"<span class=\"correct\">OK</span> Se adiciona nuevo centro de costo a la lista de especiales.<br>");
                    $data['debug_log'] = debug_log_inline();
                    break;

                case 'agregar_equivalentes':
                    /*wespecial08_id : wespecial08_id,
                    wespecial08_arecco : wespecial08_arecco,
                    wespecial08_arecva : wespecial08_arecva,
                    wespecial08_arecde : wespecial08_arecde,
                    wespecial08_arecma : wespecial08_arecma,
                    wespecial08_areneg : wespecial08_areneg,
                    wespecial08_areapl : wespecial08_areapl*/
                    $data['html'] = getFilaMedicamentoEquivalente($wequivalente_acpart, $texto_art, $wequivalente_acpcan, $wequivalente_acpest);
                    debug_log_inline('',"<span class=\"correct\">OK</span> Se adiciona nuevo articulo asociado al medicamento actual.<br>");
                    $data['debug_log'] = debug_log_inline();
                    break;

                case 'buscar_equivalente':
                    $cont = 0;
                    $data['html'] = getMaestroArticulos26($conex, $wemp_pmla, $wbasedato, $cont, 'options', $wtexto);
                    debug_log_inline('',"<span class=\"correct\">OK</span> Se adiciona nuevo centro de costo a la lista de especiales.<br>");
                    $data['debug_log'] = debug_log_inline();
                    break;

                default:
                    $data['mensaje'] = $no_exec_sub;
                    $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;
        case 'update':
            switch ($form) {
                case 'editar_articulo_1':
                    $data['id_root_64_edit'] = '';
                    $data['estado_med'] = '';
                    $estado_registros = 'on';
                    if($westado_medicamento=='on')
                    {
                        $qEst = "
                            UPDATE ".$wbasedato."_000026
                                    SET Artest = '".$westado_medicamento."'
                            WHERE Artcod = '".$wcodarticulo."'";
                        if($resEst = mysql_query($qEst, $conex))
                        {
                            $data['estado_med'] = $westado_medicamento;
                            $todo_ok = false; //Para que sólo se inactive el medicamento pero no sus registros en las demás tablas donde tenga relación
                            // $data['error'] = 1;
                            // $data['mensaje'] .= "\n[!] Se inactivó el medicamento (".$wcodarticulo.").";
                            $descripcion = "tabla:'".$wbasedato."_000026'|id:''|columnUpd:'Artest'|columnFiltro:'Artcod'|valueFiltro:'$wcodarticulo'|obs:'Actualiza estado de artículo ($wcodarticulo) a ($westado_medicamento)'";
                            insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                            debug_log_inline('',"<span class=\"correct\">OK</span> Se inactivó el medicamento (".$wcodarticulo."): <br>&raquo; ".$qEst."<br>");
                        }
                        else
                        {
                            $todo_ok = false;
                            $data['error'] = 1;
                            $data['mensaje'] .= "\n[!] No se pudo establecer el estado del medecamento (".$wcodarticulo.").";
                            debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo establecer el estado del medecamento (".$wcodarticulo."): <br>&raquo; ".$qEst."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                        }
                    }

                    $todo_ok = true;
                    if(($esmedicamento_26 == 'off' || $esmedicamento_26 == '') || ($westado_medicamento == 'off'))
                    {
                        //$estado_registros = 'off'; // Si deja de ser medicamento o es medicamento pero se inactiva. 2013-03-18 Se comenta para no inactivar
                        // los registros en otras tablas asociados al medicamento. Solo de debería inactivar el medicamento en la tabla movhos_000026.

                        //Ya no es medicamento entonces sus centros de costos en stock se deben inactivar
                        $wesdestock91 = 'off';
                        $wlista_especiales = '';

                        $q = "
                            SELECT  id
                            FROM    ".$wbasedato."_000054
                            WHERE   Kadfec = '".date("Y-m-d")."'
                                    AND Kadart = '".$wcodarticulo."'
                                    AND Kadest = 'on'
                                    AND Kadori = 'SF'";
                        if($res = mysql_query($q, $conex))
                        {
                            if(mysql_num_rows($res) > 0)
                            {
                                $todo_ok = false;
                                $data['error'] = 1;
                                $data['mensaje'] .= "\n[!] No se puede inactivar el articulo (".$wcodarticulo.") porque está activo en el Kardex del día actual (".date("Y-m-d").").";
                                debug_log_inline('',"<span class=\"correct\">OK</span> No se puede inactivar el articulo (".$wcodarticulo.") porque está activo en el Kardex del día actual (".date("Y-m-d")."): <br>&raquo; ".$q."<br>");
                            }
                            else
                            {
                                // Si se inactiva medicamento westado_medicamento=off
                                if($westado_medicamento == 'off')
                                {
                                    $qEst = "
                                        UPDATE ".$wbasedato."_000026
                                                SET Artest = '".$westado_medicamento."'
                                        WHERE Artcod = '".$wcodarticulo."'";
                                    if($resEst = mysql_query($qEst, $conex))
                                    {
                                        $data['estado_med'] = $westado_medicamento;
                                        $todo_ok = false; //Para que sólo se inactive el medicamento pero no sus registros en las demás tablas donde tenga relación
                                        $data['error'] = 1;
                                        $data['mensaje'] .= "\n[!] Se inactivó el medicamento (".$wcodarticulo.").";

                                        $descripcion = "tabla:'".$wbasedato."_000026'|id:''|columnUpd:'Artest'|columnFiltro:'Artcod'|valueFiltro:'$wcodarticulo'|obs:'Actualiza estado de artículo ($wcodarticulo) a ($westado_medicamento)'";
                                        insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                        debug_log_inline('',"<span class=\"correct\">OK</span> Se inactivó el medicamento (".$wcodarticulo."): <br>&raquo; ".$qEst."<br>");
                                    }
                                    else
                                    {
                                        $todo_ok = false;
                                        $data['error'] = 1;
                                        $data['mensaje'] .= "\n[!] No se pudo inactivar el medecamento (".$wcodarticulo.").";
                                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo inactivar el medecamento (".$wcodarticulo."): <br>&raquo; ".$qEst."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                    }
                                }
                            }
                        }
                        else
                        {
                            $todo_ok = false;
                            $data['error'] = 1;
                            $data['mensaje'] .= "\n[!] No se pudo establecer si el articulo (".$wcodarticulo.") está activo para el Kardex diario (".date("Y-m-d").").";
                            debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo establecer si el articulo (".$wcodarticulo.") está activo para el Kardex diario (".date("Y-m-d").") (000054): <br>&raquo; ".$q."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                        }
                    }

                    if($todo_ok)
                    {
                        // si tiene id en la "000058" Articulos especiales de Kardex, y el articulo se acaba de marcar NO es medicamento entonces inactivar.
                        if($id_68_edit != '')
                        {
                            $sql_upd = "
                                UPDATE ".$wbasedato."_000068
                                        SET Arkest = '".$estado_registros."',
                                            Arkcco = '".CENTRO_COSTO_FIJO."',
                                            Arktip = 'LC',
                                            Seguridad = 'C-".$user_session."'
                                WHERE id = '".$id_68_edit."'";
                            if($res = mysql_query($sql_upd, $conex))
                            {
                                $descripcion = "tabla:'".$wbasedato."_000068'|id:'$id_68_edit'|columnUpd:''|columnFiltro:'id'|valueFiltro:'$id_68_edit'|obs:'Actualiza estado a ($estado_registros)'";
                                insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                debug_log_inline('',"<span class=\"correct\">OK</span> Se editó Articulos especiales de Kardex para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql_upd."<br>");
                            }
                            else
                            {
                                $data['error'] = 1;
                                //$data['mensaje'] .= "\n[!] No se pudo editar Articulos especiales de Kardex para el articulo (".$wcodarticulo.").";
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo editar Articulos especiales de Kardex para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql_upd."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }

                        if(is_array($wfracciones59_defavc) && in_array("*", $wfracciones59_defavc))
                        {
                            $wfracciones59_defavc = array("*");
                        }

                        // Editar datos "000059" => definición de fracciones.
                        $sql_upd = "
                            UPDATE ".$wbasedato."_000059
                                    SET Defcco = '".$wfracciones59_defcco."', Defart = '".$wcodarticulo."',
                                        Deffru = '".$wfracciones59_deffru."', Deffra = '".$wfracciones59_deffra."',
                                        Defest = '".$wfracciones59_defest."', Defven = '".$wfracciones59_defven."',
                                        Defdie = '".$wfracciones59_defdie."', Defdis = '".$wfracciones59_defdis."',
                                        Defdup = '".$wfracciones59_defdup."', Defcon = '".$wfracciones59_defcon."',
                                        Defnka = '".$wfracciones59_defnka."', Defdim = '".$wfracciones59_defdim."',
                                        Defdom = '".$wfracciones59_defdom."', Defvia = '".(($wfracciones59_defvia != '') ? implode(",",$wfracciones59_defvia) : '')."',
                                        Defipo = '".$wfracciones59_defipo."', Defrci = '".$wfracciones59_defrci."',
                                        Defcai = '".$wfracciones59_defcai."', Defcas = '".$wfracciones59_defcas."',
                                        Defesc = '".$wfracciones59_defesc."', Defavc = '".(($wfracciones59_defavc != '') ? implode(",",$wfracciones59_defavc) : '')."',
                                        Defmin = '".$wfracciones59_defmin."', Defmax = '".$wfracciones59_defmax."',
                                        Seguridad = 'C-".$user_session."'
                            WHERE id = '".$id_59."'";
                        if($res = mysql_query($sql_upd, $conex))
                        {
                            debug_log_inline('',"<span class=\"correct\">OK</span> Se editó la tabla de definición de fracciones para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql_upd."<br>");
                            // Editar datos "000115" => detalle de familias. Se edita solo si se pudo editar la definicion de fracciones "000059".
                            $wpresentacion = (trim($wpresentacion) == '' || trim($wpresentacion) == '.') ? '00': $wpresentacion;
                            $sql_upd = "
                                UPDATE ".$wbasedato."_000115
                                        SET Relfam = '".$wfamilia26."',Reluni = '".$wfracciones59_deffru."',Relart = '".$wcodarticulo."',
                                            Relcon = '".$wfracciones59_deffra."',Relpre = '".$wpresentacion."',Relest = '".$estado_registros."',Seguridad = 'C-".$user_session."'
                                WHERE id = '".$id_familia_115."'";
                            if($res = mysql_query($sql_upd, $conex))
                            {
                                $descripcion = "tabla:'".$wbasedato."_000068'|id:'$id_familia_115'|columnUpd:'TODOS'|columnFiltro:'id'|valueFiltro:'$id_familia_115'|obs:'Actualiza todos los campos relacionados al artículo ($wcodarticulo)'";
                                insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                debug_log_inline('',"<span class=\"correct\">OK</span> Se editó el detalle de familia para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql_upd."<br>");
                            }
                            else
                            {
                                $data['error'] = 1;
                                $data['mensaje'] .= "\n[!] No se pudo editar el detalle de familia para el articulo (".$wcodarticulo.").";
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo editar el detalle de familia para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql_upd."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] .= "\n[!] No se pudo editar la tabla de definición de fracciones para el articulo (".$wcodarticulo.").";
                            debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo editar la tabla de definición de fracciones para el articulo (".$wcodarticulo."): <br>&raquo; ".$sql_upd."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                        }

                        /*Acciones para el STOCK
                        wesdestock91
                        wtipostock91
                        wcentrocostos91
                        Si se dice que es de stock se continúa para configurar posibles centros de costos para el articulo, de lo contrario inactiva posibles
                        centros de costos en 000091 asociados al código del articulo.*/
                        if($wesdestock91 == 'on')
                        {
                            $q_stk = "  SELECT  id, Arscod, Arscco
                                        FROM    ".$wbasedato."_000091
                                        WHERE   Arscod = '".$wcodarticulo."'
                                                AND Arsest = 'on'";
                            if($res = mysql_query($q_stk, $conex))
                            {
                                debug_log_inline('',"<span class=\"correct\">OK</span> Se consultó el articulo (".$wcodarticulo.") en stock (000091): <br>&raquo; ".$q_stk."<br>");

                                $arr_ccos_stock_existen =  array();
                                while($row = mysql_fetch_array($res))
                                {
                                    $arr_ccos_stock_existen[$row['Arscco']] = $row['id'];
                                }

                                $arr_inserts_stock = array();
                                foreach ($wcentrocostos91 as $key => $value) {
                                    if(array_key_exists($value, $arr_ccos_stock_existen))
                                    {
                                        unset($arr_ccos_stock_existen[$value]);
                                    }
                                    else
                                    {
                                        $arr_inserts_stock[$value] = "('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$value."','".$wcodarticulo."','on','".$wtipostock91."','C-".$user_session."')";
                                    }
                                }
                                // print_r($arr_ccos_stock_existen);
                                // print_r($wcentrocostos91);

                                // INACTIVAR en STOCK los centro costos que ya no se relacionan al articulo.
                                if(count($arr_ccos_stock_existen) > 0)
                                {
                                    $qinact = "UPDATE ".$wbasedato."_000091 SET Arsest = 'off' WHERE id IN ('".implode("','", $arr_ccos_stock_existen)."')";
                                    if($res = mysql_query($qinact, $conex))
                                    {
                                        $descripcion = "tabla:'".$wbasedato."_000091'|id:'".implode("','", $arr_ccos_stock_existen)."'|columnUpd:'Arsest'|columnFiltro:'id'|valueFiltro:'".implode("','", $arr_ccos_stock_existen)."'|obs:'Actualiza a estado off'";
                                        insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                        debug_log_inline('',"<span class=\"correct\">OK</span> Se inactivó centros de costos en stock (".implode(",", $arr_ccos_stock_existen).") para el articulo (".$wcodarticulo.") en stock (000091): <br>&raquo; ".$qinact."<br>");
                                    }
                                    else
                                    {
                                        $data['mensaje'] .= "\n[!] No se pudo Inactivar en stock los centros costos (".implode(",", $arr_ccos_stock_existen).") para el articulo (".$wcodarticulo.").";
                                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo Inactivar en stock los centros costos (".implode(",", $arr_ccos_stock_existen).") para el articulo (".$wcodarticulo."): <br>&raquo; ".$qinact."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                    }
                                }

                                //Insertar nuevos centros de costos en stock para un articulo
                                if(count($arr_inserts_stock) > 0)
                                {
                                    $qstock = " INSERT INTO ".$wbasedato."_000091 (Medico,Fecha_data,Hora_data,Arscco,Arscod,Arsest,Arstip,Seguridad)
                                                VALUES ".implode(",", $arr_inserts_stock).";";

                                    if($res = mysql_query($qstock, $conex))
                                    {
                                        $descripcion = "tabla:'".$wbasedato."_000091'|id:''|columnUpd:'TODOS'|columnFiltro:''|valueFiltro:''|obs:'Inserta centros de costos de stock para el artículo ($wcodarticulo)'";
                                        insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                        debug_log_inline('',"<span class=\"correct\">OK</span> Se configuró nuevos centros de costos en stock (".implode(",", $wcentrocostos91).") para el articulo (".$wcodarticulo.") en stock (000091): <br>&raquo; ".$qstock."<br>");
                                    }
                                    else
                                    {
                                        $data['mensaje'] .= "\n[!] No se pudo configurar nuevos centros de costos en stock (".implode(",", $wcentrocostos91).") para el articulo (".$wcodarticulo.").";
                                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo configurar nuevos centros de costos en stock (".implode(",", $wcentrocostos91).") para el articulo (".$wcodarticulo."): <br>&raquo; ".$qstock."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                    }
                                }
                            }
                            else
                            {
                                $data['mensaje'] .= "\n[!] No se pudo Verificar si el articulo (".$wcodarticulo.") ya estaba configurado en el STOCK para los centros de costos seleccionados.";
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo Verificar si el articulo (".$wcodarticulo.") ya estaba configurado en el STOCK para los centros de costos seleccionados: <br>&raquo; ".$q_stk."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }
                        else
                        {
                            $qupd_stk = "   UPDATE ".$wbasedato."_000091 SET Arsest = 'off' WHERE Arscod = '".$wcodarticulo."'";
                            if($res = mysql_query($qupd_stk, $conex))
                            {
                                $descripcion = "tabla:'".$wbasedato."_000091'|id:''|columnUpd:'Arsest'|columnFiltro:'Arscod'|valueFiltro:'$wcodarticulo'|obs:'Actualiza estado de todos los centros de costos de stock para el artículo ($wcodarticulo) cambia a off'";
                                insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                debug_log_inline('',"<span class=\"correct\">OK</span> Se inactivó cualquier centros de costos en stock para el articulo (".$wcodarticulo.") en (000091): <br>&raquo; ".$qupd_stk."<br>");
                            }
                            else
                            {
                                $data['mensaje'] .= "\n[!] No se pudo inactivar posibles centros de costos en stock para el articulo (".$wcodarticulo.").";
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo inactivar posibles centros de costos en stock para el articulo (".$wcodarticulo."): <br>&raquo; ".$qupd_stk."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }

                        /*En esta sección se guardan los centros de costos que asocian al medicamento marcado como especial.
                        $wlista_especiales esta vacío entonces se borran los registros del medicamento en la tabla "000008" */
                        if($wlista_especiales != '')
                        {
                            $q_esp = "  SELECT  Arecco, Arecod, Arecva, Arecde, Arecma, Areneg, Areapl
                                        FROM    movhos_000008
                                        WHERE   Arecod = '".$wcodarticulo."'";
                            if($res = mysql_query($q_esp, $conex))
                            {
                                debug_log_inline('',"<span class=\"correct\">OK</span> Se consulta datos especiales del articulo (".$wcodarticulo.") en 000008: <br>&raquo; ".$q_esp."<br>");
                                $arr_ccos_esp_BD = array();
                                while ($row = mysql_fetch_array($res)) {
                                    // if(!array_key_exists($row['Arecco'], $arr_ccos_esp_actual))
                                    // {
                                    //     $arr_ccos_esp_actual[$row['Arecco'] = array();
                                    // }
                                    $arr_ccos_esp_BD[$row['Arecco']] = array("Arecco"=>$row['Arecco'], "Arecod"=>$row['Arecod'], "Arecva"=>$row['Arecva'], "Arecde"=>$row['Arecde'], "Arecma"=>$row['Arecma'], "Areneg"=>$row['Areneg'], "Areapl"=>$row['Areapl']);
                                }

                                $arr_ccos_validar = explode("=>", $wlista_especiales);
                                $arr_ccos_esp_nuevo = array();
                                foreach ($arr_ccos_validar as $key => $datos_reg) {
                                    // cco+"|"+ cva+"|"+ cde+"|"+ cma+"|"+ neg+"|"+ apl
                                    $rowesp = explode("|", $datos_reg);
                                    $arr_ccos_esp_nuevo[$rowesp[0]] = array("Arecco"=>$rowesp[0], "Arecod"=>$wcodarticulo, "Arecva"=>$rowesp[1], "Arecde"=>$rowesp[2], "Arecma"=>$rowesp[3], "Areneg"=>$rowesp[4], "Areapl"=>$rowesp[5]);
                                }

                                $arr_inserts_esp = array();
                                foreach ($arr_ccos_esp_nuevo as $cco => $value) {
                                    // Si el centro de costo que llega del formulario de edición, ya existe en la tabla 000008 entonces se procede a actualizar el de de BD en la tabla 000008
                                    if(array_key_exists($cco, $arr_ccos_esp_BD))
                                    {
                                        if($arr_ccos_esp_BD[$cco] != $arr_ccos_esp_nuevo[$cco]) // Si son iguales no se debe tomar ninguna acción.
                                        {
                                            //Actualizar en 000008 si el cco que llega ya existe en BD para ese articulo.
                                            $sqlupd = " UPDATE ".$wbasedato."_000008
                                                                SET Arecva = '".$arr_ccos_esp_nuevo[$cco]['Arecva']."',
                                                                    Arecde = '".$arr_ccos_esp_nuevo[$cco]['Arecde']."',
                                                                    Arecma = '".$arr_ccos_esp_nuevo[$cco]['Arecma']."',
                                                                    Areneg = '".$arr_ccos_esp_nuevo[$cco]['Areneg']."',
                                                                    Areapl = '".$arr_ccos_esp_nuevo[$cco]['Areapl']."'
                                                        WHERE   Arecod = '".$wcodarticulo."'
                                                                AND Arecco = '".$cco."'";
                                            if($res_new = mysql_query($sqlupd, $conex))
                                            {
                                                $descripcion = "tabla:'".$wbasedato."_000008'|id:''|columnUpd:'TODOS'|columnFiltro:'Arecod-Arecco'|valueFiltro:'$wcodarticulo,$cco'|obs:'Actualiza todos los campos relacionados al artículo ($wcodarticulo) y cco ($cco)'";
                                                insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                                debug_log_inline('',"<span class=\"correct\">OK</span> Se actualizó el articulo (".$wcodarticulo.") y centro costo (".$cco.") en (000008): <br>&raquo; ".$sqlupd."<br>");
                                            }
                                            else
                                            {
                                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo actualizar el articulo (".$wcodarticulo.") y centro costo (".$cco.") en (000008): <br>&raquo; ".$sqlupd."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                            }
                                        }
                                        unset($arr_ccos_esp_BD[$cco]);//Al final de todo, lo que quede en este array se debe eliminar o borrar de la tabla.
                                    }
                                    else
                                    {
                                        // Si no existe en base de datos en la tabla "000008" entonces se debe preparar para insertar.
                                        $arr_inserts_esp[] = "('".$wbasedato."',
                                                                '".date('Y-m-d')."',
                                                                '".date('H:i:s')."',
                                                                '".$cco."',
                                                                '".$wcodarticulo."',
                                                                '".$wcodarticulo."',
                                                                '".$arr_ccos_esp_nuevo[$cco]['Arecva']."',
                                                                '".$arr_ccos_esp_nuevo[$cco]['Arecde']."',
                                                                '".$arr_ccos_esp_nuevo[$cco]['Arecma']."',
                                                                '".$arr_ccos_esp_nuevo[$cco]['Areneg']."',
                                                                '".$arr_ccos_esp_nuevo[$cco]['Areapl']."',
                                                                'C-".$user_session."' )";
                                    }
                                }

                                // Si hay datos para insertar se continúa para hacer guardar en "000008"
                                if(count($arr_inserts_esp) > 0)
                                {
                                    //Insertar en 000008 nuevos centros de costos para el articulo.
                                    $sqlins = " INSERT INTO ".$wbasedato."_000008
                                                (Medico, Fecha_data, Hora_data, Arecco, Areces, Arecod, Arecva, Arecde, Arecma, Areneg, Areapl, Seguridad)
                                                VALUES  ".implode(",", $arr_inserts_esp).";";
                                    if($res_new = mysql_query($sqlins, $conex))
                                    {
                                        $descripcion = "tabla:'".$wbasedato."_000008'|id:''|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta nuevos registros relacionados al artículo ($wcodarticulo)'";
                                        insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                        debug_log_inline('',"<span class=\"correct\">OK</span> Se Insertaron nuevos datos de medicamento especial para el articulo (".$wcodarticulo.") en (000008): <br>&raquo; ".$sqlins."<br>");
                                    }
                                    else
                                    {
                                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se Insertaron nuevos datos de medicamento especial para el articulo (".$wcodarticulo.") en (000008): <br>&raquo; ".$sqlins."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                    }
                                }

                                // Si en el array donde se consultó inicialmente posibles datos especiales, quedan elementos, esos se deben inactivar o borrar de la tabla.
                                if(count($arr_ccos_esp_BD) > 0)
                                {
                                    // Eliminar de la tabla 000008
                                    $arr_ccos = array_keys($arr_ccos_esp_BD);

                                    //Eliminar en 000008 los registros que ya no estan en el formulario de edición del medicamento en la sección de medicamento especial.
                                    $sqldel = " DELETE
                                                FROM    ".$wbasedato."_000008
                                                WHERE   Arecod = '".$wcodarticulo."'
                                                        AND Arecco IN ('".implode(",", $arr_ccos)."')";
                                    if($res_del = mysql_query($sqldel, $conex))
                                    {
                                        $descripcion = "tabla:'".$wbasedato."_000008'|id:''|columnUpd:''|columnFiltro:'Arecod-Arecco'|valueFiltro:'$wcodarticulo,".implode(",", $arr_ccos)."'|obs:'Elimina registros relacionados al artículo ($wcodarticulo)'";
                                        insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                        debug_log_inline('',"<span class=\"correct\">OK</span> Se eliminaron centros de costos asociados al articulo (".$wcodarticulo.") en (000008): <br>&raquo; ".$sqldel."<br>");
                                    }
                                    else
                                    {
                                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo eliminar centros de costos asociados al articulo (".$wcodarticulo.") en (000008): <br>&raquo; ".$sqldel."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                    }
                                }
                            }
                            else
                            {
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo consultar datos especiales del articulo (".$wcodarticulo.") en 000008: <br>&raquo; ".$qstock."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }
                        else
                        {
                            //Eliminar en 000008 los registros que ya no estan en el formulario de edición del medicamento en la sección de medicamento especial.
                            $sqldel = " DELETE
                                        FROM    ".$wbasedato."_000008
                                        WHERE   Arecod = '".$wcodarticulo."'";
                            if($res_del = mysql_query($sqldel, $conex))
                            {
                                $descripcion = "tabla:'".$wbasedato."_000008'|id:''|columnUpd:''|columnFiltro:'Arecod'|valueFiltro:'$wcodarticulo'|obs:'Elimina registros relacionados al artículo ($wcodarticulo)'";
                                insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                debug_log_inline('',"<span class=\"correct\">OK</span> Se eliminaron todos los registros de centros de costos asociados al articulo (".$wcodarticulo.") en (000008): <br>&raquo; ".$sqldel."<br>");
                            }
                            else
                            {
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo eliminar todos los registros de centros de costos asociados al articulo (".$wcodarticulo.") en (000008): <br>&raquo; ".$sqldel."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }

                        /*En esta sección se guardan los medicamentos que se asocian con el articulo a editar.
                        $wlista_equivalente esta vacío entonces se borran los registros del medicamento en la tabla "000153" */
                        if($wlista_equivalente != '')
                        {
                            $q_esp = "  SELECT  Acppro, Acpart, Acpcan, Acpest
                                        FROM    movhos_000153
                                        WHERE   Acppro = '".$wcodarticulo."'";
                            if($res = mysql_query($q_esp, $conex))
                            {
                                debug_log_inline('',"<span class=\"correct\">OK</span> Se consulta articulos asociados del producto-medicamento (".$wcodarticulo.") en 000153: <br>&raquo; ".$q_esp."<br>");
                                $arr_art_equiv_BD = array();
                                while ($row = mysql_fetch_array($res)) {
                                    // if(!array_key_exists($row['Arecco'], $arr_ccos_esp_actual))
                                    // {
                                    //     $arr_ccos_esp_actual[$row['Arecco'] = array();
                                    // }
                                    $arr_art_equiv_BD[$row['Acpart']] = array("Acppro"=>$row['Acppro'], "Acpart"=>$row['Acpart'], "Acpcan"=>$row['Acpcan'], "Acpest"=>$row['Acpest']);
                                }

                                $arr_equivs_nuevo = array();
                                $arr_equivalente_validar = explode("=>", $wlista_equivalente);
                                foreach ($arr_equivalente_validar as $key => $datos_reg) {
                                    // cco+"|"+ cva+"|"+ cde+"|"+ cma+"|"+ neg+"|"+ apl
                                    $rowesp = explode("|", $datos_reg);
                                    $arr_equivs_nuevo[$rowesp[0]] = array("Acppro"=>$wcodarticulo, "Acpart"=>$rowesp[0], "Acpcan"=>$rowesp[1], "Acpest"=>$rowesp[2]);
                                }

                                $arr_inserts_esp = array();
                                foreach ($arr_equivs_nuevo as $art => $value) {
                                    // Si el centro de costo que llega del formulario de edición, ya existe en la tabla 000153 entonces se procede a actualizar el de de BD en la tabla 000153
                                    if(array_key_exists($art, $arr_art_equiv_BD))
                                    {
                                        if($arr_art_equiv_BD[$art] != $arr_equivs_nuevo[$art]) // Si son iguales no se debe tomar ninguna acción.
                                        {
                                            //Actualizar en 000153 si el cco que llega ya existe en BD para ese articulo.
                                            $sqlupd = " UPDATE ".$wbasedato."_000153
                                                                SET Acpcan = '".$arr_equivs_nuevo[$art]['Acpcan']."',
                                                                    Acpest = '".$arr_equivs_nuevo[$art]['Acpest']."'
                                                        WHERE   Acppro = '".$wcodarticulo."'
                                                                AND Acpart = '".$art."'";
                                            if($res_new = mysql_query($sqlupd, $conex))
                                            {
                                                $descripcion = "tabla:'".$wbasedato."_000153'|id:''|columnUpd:'Acpcan,Acpest'|columnFiltro:'Acppro-Acpart'|valueFiltro:'$wcodarticulo,$art'|obs:'Actualiza articulos asociados al medicamento ($wcodarticulo) y articulo asociado ($art)'";
                                                insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                                debug_log_inline('',"<span class=\"correct\">OK</span> Se actualizó el medicamento (".$wcodarticulo.") y articulo asociado (".$art.") en (000153): <br>&raquo; ".$sqlupd."<br>");
                                            }
                                            else
                                            {
                                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo actualizar el medicamento (".$wcodarticulo.") y acticulo asociado (".$art.") en (000153): <br>&raquo; ".$sqlupd."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                            }
                                        }
                                        unset($arr_art_equiv_BD[$art]);//Al final de todo, lo que quede en este array se debe eliminar o borrar de la tabla.
                                    }
                                    else
                                    {
                                        // Si no existe en base de datos en la tabla "000153" entonces se debe preparar para insertar.
                                        $arr_inserts_esp[] = "('".$wbasedato."',
                                                                '".date('Y-m-d')."',
                                                                '".date('H:i:s')."',
                                                                '".$wcodarticulo."',
                                                                '".$art."',
                                                                '".$arr_equivs_nuevo[$art]['Acpcan']."',
                                                                '".$arr_equivs_nuevo[$art]['Acpest']."',
                                                                'C-".$user_session."' )";
                                    }
                                }

                                // Si hay datos para insertar se continúa para hacer guardar en "000153"
                                if(count($arr_inserts_esp) > 0)
                                {
                                    //Insertar en 000153 nuevos centros de costos para el articulo.
                                    $sqlins = " INSERT INTO ".$wbasedato."_000153
                                                (Medico, Fecha_data, Hora_data, Acppro, Acpart, Acpcan, Acpest, Seguridad)
                                                VALUES  ".implode(",", $arr_inserts_esp).";";
                                    if($res_new = mysql_query($sqlins, $conex))
                                    {
                                        $descripcion = "tabla:'".$wbasedato."_000153'|id:''|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta nuevos registros relacionados al artículo ($wcodarticulo)'";
                                        insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                        debug_log_inline('',"<span class=\"correct\">OK</span> Se Insertaron nuevos datos de medicamento especial para el articulo (".$wcodarticulo.") en (000153): <br>&raquo; ".$sqlins."<br>");
                                    }
                                    else
                                    {
                                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se Insertaron nuevos datos de medicamento especial para el articulo (".$wcodarticulo.") en (000153): <br>&raquo; ".$sqlins."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                    }
                                }

                                // Si en el array donde se consultó inicialmente posibles datos especiales, quedan elementos, esos se deben inactivar o borrar de la tabla.
                                if(count($arr_art_equiv_BD) > 0)
                                {
                                    // Eliminar de la tabla 000153
                                    $arr_arts = array_keys($arr_art_equiv_BD);

                                    //Eliminar en 000153 los registros que ya no estan en el formulario de edición del medicamento en la sección de medicamento especial.
                                    /*$sqldel = " DELETE
                                                FROM    ".$wbasedato."_000153
                                                WHERE   Acppro = '".$wcodarticulo."'
                                                        AND Acpart IN ('".implode(",", $arr_arts)."')";*/
                                    $sqldel = " UPDATE ".$wbasedato."_000153
                                                        SET Acpest = 'off'
                                                WHERE   Acppro = '".$wcodarticulo."'
                                                        AND Acpart IN ('".implode(",", $arr_arts)."')";
                                    if($res_del = mysql_query($sqldel, $conex))
                                    {
                                        $descripcion = "tabla:'".$wbasedato."_000153'|id:''|columnUpd:'Acpest'|columnFiltro:'Acppro-Acpart'|valueFiltro:'$wcodarticulo,".implode(",", $arr_arts)."'|obs:'Elimina/Inactiva medicamentos relacionados al artículo ($wcodarticulo)'";
                                        insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                        debug_log_inline('',"<span class=\"correct\">OK</span> Se elimina/inactiva registro de medicamentos asociados articulo (".$wcodarticulo.") en (000153): <br>&raquo; ".$sqldel."<br>");
                                    }
                                    else
                                    {
                                        debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo eliminar/inactivar registro de articulos asociados al medicamento (".$wcodarticulo.") en (000153): <br>&raquo; ".$sqldel."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                    }
                                }
                            }
                            else
                            {
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo consultar datos de articulos asociados al medicamento (".$wcodarticulo.") en 000153: <br>&raquo; ".$qstock."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }
                        else
                        {
                            //Eliminar en 000153 los registros que ya no estan en el formulario de edición del medicamento en la sección de medicamento especial.
                            /*$sqldel = " DELETE
                                        FROM    ".$wbasedato."_000153
                                        WHERE   Acppro = '".$wcodarticulo."'";*/

                            $sqldel = " UPDATE ".$wbasedato."_000153
                                                SET Acpest = 'off'
                                        WHERE   Acppro = '".$wcodarticulo."'";
                            if($res_del = mysql_query($sqldel, $conex))
                            {
                                $descripcion = "tabla:'".$wbasedato."_000153'|id:''|columnUpd:''|columnFiltro:'Arecod'|valueFiltro:'$wcodarticulo'|obs:'Elimina/Inactiva registros de articulos relacionados al medicamento ($wcodarticulo)'";
                                insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                debug_log_inline('',"<span class=\"correct\">OK</span> Se eliminaron/inactivaron registros de articulos asociados al medicamento (".$wcodarticulo.") en (000153): <br>&raquo; ".$sqldel."<br>");
                            }
                            else
                            {
                                debug_log_inline('',"<span class=\"error\">ERROR</span> No se pudo eliminar/inactivar todos los registros de articulos relacionados al medicamento (".$wcodarticulo.") en (000153): <br>&raquo; ".$sqldel."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                            }
                        }

                        if($estado_registros == 'on') // Si el medicamento aún se encuentra activo entonces continúa con las verificaciones de CUMS.
                        {
                            /*  Consulta si el medicamento ya existe en -CUMS- si ya existe se actualiza pero sino entonces se inserta. */
                            /*  id_root_64
                                wcum64codigo,
                                wcum64equivale,
                                nomgen_root_64*/
                            $qcum = "
                                    SELECT  Cumint, Cumemp, id
                                    FROM    root_000064
                                    WHERE   Cumint = '".$wcodarticulo."'
                                            AND Cumemp = '".$wemp_pmla."'";
                            $existe_cums = 0;
                            if($rescums = mysql_query($qcum,$conex))
                            {
                                $existe_cums = mysql_num_rows($rescums);
                            }

                            /* Si el medicamento aún no existe en la tabla root_000064 CUMS entonces lo inserta. */
                            if($id_root_64 == '' && $existe_cums == 0)
                            {
                                debug_log_inline('',"<span class=\"correct\">OK</span> El articulo ($wcodarticulo) aún no existe en la tabla de CUMS root_000064, se procede a insertar: <br>&raquo; ".$qcum."<br>");
                                /*Inserta articulo en la tabla "root_000064" articulos con código CUMS.*/
                                $qicums = "
                                        INSERT  INTO root_000064
                                                (Medico,Fecha_data,Hora_data,
                                                    Cumcod,Cumdes,Cumequ,Cumint,Cumemp,Seguridad)
                                        VALUES  ('root','".date("Y-m-d")."','".date("H:i:s")."',
                                                    '".$wcum64codigo."','".$nomgen_root_64."','".$wcum64equivale."','".$wcodarticulo."','".$wemp_pmla."','C-root')";
                                if($resicums = mysql_query($qicums,$conex))
                                {
                                    $id = mysql_insert_id();
                                    $data['id_root_64_edit'] = $id;
                                    $data['mensaje'] .= "\n* EL articulo ($wcodarticulo) se configuró también en la tabla de CUMS";

                                    $descripcion = "tabla:'root_000064'|id:''|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta registro relacionado al artículo ($wcodarticulo) ($wcum64codigo,$wcodarticulo,$wemp_pmla)'";
                                    insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                    debug_log_inline('',"<span class=\"correct\">OK</span>  EL articulo ($wcodarticulo) se configuró también en la tabla de CUMS (root_000064): id-$id<br>&raquo; ".$qicums."<br>");
                                }
                                else
                                {
                                    $data['mensaje'] .= "\n[!] EL articulo ($wcodarticulo) NO SE PUDO configurar en la tabla de códigos CUMS";
                                    debug_log_inline('',"<span class=\"error\">ERROR</span>  EL articulo ($wcodarticulo) NO SE PUDO configurar en la tabla de códigos CUMS (root_000064): <br>&raquo; ".$qicums."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                }
                            }
                            else if(trim($id_root_64) != '')
                            {
                                debug_log_inline('',"<span class=\"correct\">OK</span> El articulo ($wcodarticulo) YA existe en la tabla de CUMS root_000064, se procede a actualizar: <br>&raquo; con id:".$id_root_64."<br>");
                                /*Inserta articulo en la tabla "root_000064" articulos con código CUMS.*/
                                $qucums = "
                                        UPDATE  root_000064
                                                SET Cumcod = '".$wcum64codigo."',Cumdes = '".$nomgen_root_64."',
                                                    Cumequ = '".$wcum64equivale."',Cumint = '".$wcodarticulo."',
                                                    Cumemp = '".$wemp_pmla."'
                                        WHERE   id='".$id_root_64."'";
                                if($resucums = mysql_query($qucums,$conex))
                                {
                                    //$data['mensaje'] .= "\n* EL articulo ($wcodarticulo) se actualizó en la tabla de CUMS";
                                    $descripcion = "tabla:'root_000064'|id:'$id_root_64'|columnUpd:'TODOS'|columnFiltro:'id'|valueFiltro:'$id_root_64'|obs:'Actualiza todos los campos deregistro relacionado al artículo ($wcodarticulo) ($wcum64codigo,$wcodarticulo,$wemp_pmla)'";
                                    insertLog($conex, $wbasedato, $user_session, $accion, $form, 'edita_medicamento', '', $descripcion, $user_session);
                                    debug_log_inline('',"<span class=\"correct\">OK</span>  EL articulo ($wcodarticulo) se actualizó en la tabla de CUMS (root_000064): <br>&raquo; ".$qucums."<br>");
                                }
                                else
                                {
                                    $data['error'] = 1;
                                    $data['mensaje'] .= "\n[!] EL articulo ($wcodarticulo) NO SE PUDO actualizar en la tabla de códigos CUMS";
                                    debug_log_inline('',"<span class=\"error\">ERROR</span>  EL articulo ($wcodarticulo) NO SE PUDO actualizar en la tabla de códigos CUMS (root_000064): <br>&raquo; ".$qucums."<br>".mysql_errno().'<br>'.mysql_error()."<br>");
                                }
                            }
                        }
                    }

                    $data['debug_log'] = debug_log_inline();
                    break;

                default:
                    $data['mensaje'] = $no_exec_sub;
                    $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;
        default:
            $data['mensaje'] = $no_exec_sub;
            $data['error'] = 1;
            echo json_encode($data);
            break;
    }
    return;
}

/*Array alfabético para ordenas los articulos de acuerdo a su letra inicial. */
// $arr_alfabetico = array('-'=>'', '0'=>'', '1'=>'', '2'=>'', '3'=>'', '4'=>'', '5'=>'', '6'=>'', '7'=>'', '8'=>'', '9'=>'',
//                         'A'=>'', 'B'=>'', 'C'=>'', 'D'=>'', 'E'=>'', 'F'=>'', 'G'=>'', 'H'=>'', 'I'=>'', 'J'=>'', 'K'=>'', 'L'=>'', 'M'=>'',
//                         'N'=>'', 'Ñ'=>'', 'O'=>'', 'P'=>'', 'Q'=>'', 'R'=>'', 'S'=>'', 'T'=>'', 'U'=>'', 'V'=>'', 'W'=>'', 'Y'=>'', 'Z'=>'');
// $arr_alfabetico = base64_encode(serialize($arr_alfabetico));
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
    <title>Configurar Medicamentos</title>
    <meta charset="utf-8">
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

    <script language="Javascript">
    var regEx_eq = /(^[1]$)|(^[0]$)|(^[0]\.{1}\d)|(^[1]\.{1}[0]+$)/; // PARA VALIDAR QUE EL VALOR DE EQUIVALENCIA EN CUMS SEA UN NÚMERO CORRECTO
    var regEx_cum = /(^\d{1,9}\-{1}\d{2}$)/; // PARA VALIDAR QUE EL VALOR DE CÓDIGO CUM SEA UN NÚMERO CORRECTO

    $(document).ready( function () {
        reiniciarSeccionDefinirFraccion();
        reiniciarSeccionDefinirFraccionEditable();
        reiniciarCamposConfiguracion();
        reiniciarCamposEditar();
        $("#wgenerando_lista_noconf").val('');
        $("#wgenerando_lista").val('');

        var arr_ayudas = <?=json_encode($arr_ayuda)?>;
        $.each(arr_ayudas, function(index, value) {
            if(value!='')
            { $(".msg_tooltip_"+index).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 }); }
        });
    });

    $(function(){
        $('#wbuscar_noconf').on({
            //blur : function() { buscarDatosBasicos(); },
            //change: function() { buscarDatosBasicos(); },
            keypress: function(e) {
                var codeentr = (e.which) ? e.which : e.keyCode;
                if(codeentr == 13) { verListaBuscar('div_encontrado_noconf'); }
            },
            keyup:function(e) {
                var buscarnf = $("#wbuscar_noconf").val();
                $("#wbuscar_noconf").val(validarCampoBuscar(buscarnf));
            }
        });

        $('#wbuscar_config').on({
            //blur : function() { buscarDatosBasicos(); },
            //change: function() { buscarDatosBasicos(); },
            keypress: function(e) {
                var codeentr = (e.which) ? e.which : e.keyCode;
                if(codeentr == 13) { verListaBuscarEditable('div_encontrado_config'); }
            },
            keyup:function(e) {
                var buscarnf = $("#wbuscar_config").val();
                $("#wbuscar_config").val(validarCampoBuscar(buscarnf));
            }
        });

        $('#wbuscar_equivalente').on({
            //blur : function() { buscarDatosBasicos(); },
            //change: function() { buscarDatosBasicos(); },
            keypress: function(e) {
                var codeentr = (e.which) ? e.which : e.keyCode;
                if(codeentr == 13) { buscarArticuloEquivalente('wequivalente_arcod_add'); }
            },
            keyup:function(e) {
                var buscarnf = $("#wbuscar_equivalente").val();
                $("#wbuscar_equivalente").val(validarCampoBuscar(buscarnf));
            }
        });

    });

    function validarCampoBuscar(dato)
    {
        var c = /'/gi;
        dato = dato.replace(c, "");
        c = /´/gi;
        dato = dato.replace(c, "");
        c = /"/gi;
        dato = dato.replace(c, "");
        c = /\//gi;
        dato = dato.replace(c, "");
        c = /\\/gi;
        dato = dato.replace(c, "");
        return dato;
    }

    function verSeccionArt(id){
        $("#"+id).toggle("normal");
    }

    function reset(id)
    {
        $("#"+id).html("");
    }

    function trOver(grupo)
    {
        $("#"+grupo.id).addClass('classOver');
    }

    function trOut(grupo)
    {
        $("#"+grupo.id).removeClass('classOver');
    }

    function verListaEditables(lista_id,letra)
    {
        var wgenerando_lista = $("#wgenerando_lista").val();
        if(wgenerando_lista == '')
        {
            var tiene_contenido = $("#"+lista_id).find("table").length;

            if(tiene_contenido == 0)
            {
                $("#"+lista_id).html('<img  width="13" height="13" src="../../images/medical/animated_loading.gif" />&nbsp;<font style="font-weight:bold; color:#2A5DB0; font-size:8pt" >Generando listado (Espere un momento por favor)...</font>');
            }

            if ($('#'+lista_id).is(":visible"))
            {
                $('#'+lista_id).hide();
            }
            else
            {
                $("div:visible[id^=div_editable_content_]").hide();
                $("div:visible[id^=div_editable_name_]").addClass("fondo_banda2");
                $('#'+lista_id).show();
                $('#'+lista_id).parent().removeClass("fondo_banda2");

                // if(tiene_contenido == 0) //Si no se requiere recargar toda la lista si ya existe una tabla de resultado entonces se activa este if para que no consulte de nuevo.
                //{
                    $("#wgenerando_lista").val('OK');
                    $.post("addor_medicamentos.php",
                    {
                        consultaAjax: '',
                        wemp_pmla   : $("#wemp_pmla").val(),
                        accion      : 'load',
                        form        : 'listar_por_letra',
                        wletra      : letra
                    },
                    function(data){
                            if(data.error == 1) { alert(data.mensaje); }
                            else
                            {
                                $("#"+lista_id).html(data.html);
                                $("#wgenerando_lista").val('');
                            }
                        },
                        "json"
                    );
                //}
            }
        }
        else
        {
            alert("Ya se esta generando una consulta, espere un momento hasta que termine la anterior para evitar sobrecargar al servidor.");
        }
    }

    function buscarArticuloEquivalente(lista_id)
    {
        // if ($('#'+lista_id).is(":visible"))
        // {
        //     $('#'+lista_id).hide();
        // }
        // else
        // {
        //     $("div:visible[id^=div_noconf_content_]").hide();
        //     $('#'+lista_id).show();
        // }

        cambioImagen("ccsel", "ccload");
        var wtexto = $("#wbuscar_equivalente").val();
        wtexto = validarCampoBuscar(wtexto);
        $("#wbuscar_equivalente").val(wtexto);

        if(wtexto != '')
        {

            $.post("addor_medicamentos.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                accion      : 'load',
                form        : 'buscar_equivalente',
                wletra      : '',
                wtexto      : wtexto
            },
            function(data){
                    $("#wgenerando_lista").val('');
                    if(data.error == 1) {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#"+lista_id).html(data.html);
                        $("#"+lista_id).focus();
                        cambioImagen("ccload", "ccsel");
                    }
                },
                "json"
            );
        }
    }

    function verListaBuscarEditable(lista_id)
    {
        // if ($('#'+lista_id).is(":visible"))
        // {
        //     $('#'+lista_id).hide();
        // }
        // else
        // {
        //     $("div:visible[id^=div_noconf_content_]").hide();
        //     $('#'+lista_id).show();
        // }

        var wtexto = $("#wbuscar_config").val();
        wtexto = validarCampoBuscar(wtexto);
        $("#wbuscar_config").val(wtexto);

        if(wtexto != '')
        {
            var wgenerando_lista = $("#wgenerando_lista").val();
            if(wgenerando_lista == '')
            {
                var tiene_contenido = $("#"+lista_id).find("tr[id^=tr_articulo_]").length;

                if(tiene_contenido == 0)
                {
                    $("#"+lista_id).html('<img  width="13" height="13" src="../../images/medical/animated_loading.gif" />&nbsp;<font style="font-weight:bold; color:#2A5DB0; font-size:8pt" >Generando listado (Espere un momento por favor)...</font>');
                }

                // $("div:visible[id^=div_noconf_content_]").hide();
                // $("div:visible[id^=div_noconf_name_]").addClass("fondo_banda2");
                $('#'+lista_id).show();
                // $('#'+lista_id).parent().removeClass("fondo_banda2");

                // if(tiene_contenido == 0) //Si no se requiere recargar toda la lista si ya existe una tabla de resultado entonces se activa este if para que no consulte de nuevo.
                //{
                    $("#wgenerando_lista").val('OK');
                    $.post("addor_medicamentos.php",
                    {
                        consultaAjax: '',
                        wemp_pmla   : $("#wemp_pmla").val(),
                        accion      : 'load',
                        form        : 'listar_por_letra',
                        wletra      : '',
                        wtexto      : wtexto
                    },
                    function(data){
                            $("#wgenerando_lista").val('');
                            if(data.error == 1) {
                                alert(data.mensaje);
                            }
                            else
                            {
                                $("#"+lista_id).html(data.html);
                            }
                        },
                        "json"
                    );
                //}
            }
            else
            {
                alert("Ya se esta generando una consulta, espere un momento hasta que termine la anterior para evitar sobrecargar al servidor.");
            }
        }
    }

    function verListaBuscar(lista_id)
    {
        // if ($('#'+lista_id).is(":visible"))
        // {
        //     $('#'+lista_id).hide();
        // }
        // else
        // {
        //     $("div:visible[id^=div_noconf_content_]").hide();
        //     $('#'+lista_id).show();
        // }

        var wtexto = $("#wbuscar_noconf").val();
        wtexto = validarCampoBuscar(wtexto);
        $("#wbuscar_noconf").val(wtexto);

        if(wtexto != '')
        {
            var wgenerando_lista_noconf = $("#wgenerando_lista_noconf").val();
            if(wgenerando_lista_noconf == '')
            {
                var tiene_contenido = $("#"+lista_id).find("tr[id^=tr_articulo_]").length;

                if(tiene_contenido == 0)
                {
                    $("#"+lista_id).html('<img  width="13" height="13" src="../../images/medical/animated_loading.gif" />&nbsp;<font style="font-weight:bold; color:#2A5DB0; font-size:8pt" >Generando listado (Espere un momento por favor)...</font>');
                }

                // $("div:visible[id^=div_noconf_content_]").hide();
                // $("div:visible[id^=div_noconf_name_]").addClass("fondo_banda2");
                $('#'+lista_id).show();
                // $('#'+lista_id).parent().removeClass("fondo_banda2");

                // if(tiene_contenido == 0) //Si no se requiere recargar toda la lista si ya existe una tabla de resultado entonces se activa este if para que no consulte de nuevo.
                //{
                    $("#wgenerando_lista_noconf").val('OK');
                    $.post("addor_medicamentos.php",
                    {
                        consultaAjax: '',
                        wemp_pmla   : $("#wemp_pmla").val(),
                        accion      : 'load',
                        form        : 'listar_por_letra_noconf',
                        wletra      : '',
                        wtexto      : wtexto
                    },
                    function(data){
                            if(data.error == 1) {
                                $("#wgenerando_lista_noconf").val('');
                                alert(data.mensaje);
                            }
                            else
                            {
                                $("#"+lista_id).html(data.html);
                                $("#wgenerando_lista_noconf").val('');
                            }
                        },
                        "json"
                    );
                //}
            }
            else
            {
                alert("Ya se esta generando una consulta, espere un momento hasta que termine la anterior para evitar sobrecargar al servidor.");
            }
        }
    }

    function verListaLetra(lista_id,letra)
    {
        // if ($('#'+lista_id).is(":visible"))
        // {
        //     $('#'+lista_id).hide();
        // }
        // else
        // {
        //     $("div:visible[id^=div_noconf_content_]").hide();
        //     $('#'+lista_id).show();
        // }

        var wgenerando_lista_noconf = $("#wgenerando_lista_noconf").val();
        if(wgenerando_lista_noconf == '')
        {
            var tiene_contenido = $("#"+lista_id).find("tr[id^=tr_articulo_]").length;

            if(tiene_contenido == 0)
            {
                $("#"+lista_id).html('<img  width="13" height="13" src="../../images/medical/animated_loading.gif" />&nbsp;<font style="font-weight:bold; color:#2A5DB0; font-size:8pt" >Generando listado (Espere un momento por favor)...</font>');
            }

            if ($('#'+lista_id).is(":visible"))
            {
                $('#'+lista_id).hide();
            }
            else
            {
                $("div:visible[id^=div_noconf_content_]").hide();
                $("div:visible[id^=div_noconf_name_]").addClass("fondo_banda2");
                $('#'+lista_id).show();
                $('#'+lista_id).parent().removeClass("fondo_banda2");

                // if(tiene_contenido == 0) //Si no se requiere recargar toda la lista si ya existe una tabla de resultado entonces se activa este if para que no consulte de nuevo.
                //{
                    $("#wgenerando_lista_noconf").val('OK');
                    $.post("addor_medicamentos.php",
                    {
                        consultaAjax: '',
                        wemp_pmla   : $("#wemp_pmla").val(),
                        accion      : 'load',
                        form        : 'listar_por_letra_noconf',
                        wletra      : letra
                    },
                    function(data){
                            if(data.error == 1) {
                                $("#wgenerando_lista_noconf").val('');
                                alert(data.mensaje);
                            }
                            else
                            {
                                $("#"+lista_id).html(data.html);
                                $("#wgenerando_lista_noconf").val('');
                            }
                        },
                        "json"
                    );
                //}
            }
        }
        else
        {
            alert("Ya se esta generando una consulta, espere un momento hasta que termine la anterior para evitar sobrecargar al servidor.");
        }

    }

    function verEditar(wcodarticulo,id_59,id_familia_115)
    {
        reiniciarCamposEditar();
        $("#wcodarticulo_edit").val(wcodarticulo);
        //$("#id_66_esmedicamento_edit").val(id_66_esmedicamento);
        $("#id_59_edit").val(id_59);
        $("#id_familia_115_edit").val(id_familia_115);
        $("#wnomgenerico_edit").html(wcodarticulo);
        $("#wcodarticulo_fraccion_view_edit").html(wcodarticulo);

        // Elimina posibles centros de costos configurados como especiales para un articulo.
        $("#tabla_especiales08_editable").find("tr[id^=tr_especial_]").each(function(){
            $(this).remove();
        });

        // Elimina posibles articulos asociados a un medicamento.
        $("#tabla_equivalente_editable").find("tr[id^=tr_equivalente_]").each(function(){
            $(this).remove();
        });

        $.post("addor_medicamentos.php",
        {
            consultaAjax: '',
            wemp_pmla   : $("#wemp_pmla").val(),
            DEBUG       : $("#DEBUG").val(),
            accion      : 'load',
            form        : 'cargar_editar_articulo',
            wcodarticulo: wcodarticulo,
            //id_66_esmedicamento:id_66_esmedicamento,
            id_59       : id_59,
            id_familia_115:id_familia_115
        },
        function(data){
                if(data.error == 1)
                {
                    $("#div_debug_log").append(data.debug_log);
                    alert(data.mensaje);
                }
                else
                {
                    // Reiniciar los selects multiple porque al no recargarse quedan seleccionadas opciones que ya no deberían aparecer.
                    $("#wfracciones59_defvia_edit option").each(function(){
                           $("#wfracciones59_defvia_edit option").removeAttr("selected");
                    });
                    $("#wfracciones59_defavc_edit option").each(function(){
                           $("#wfracciones59_defavc_edit option").removeAttr("selected");
                    });
                    $("#wcentrocostos91_edit option").each(function(){
                           $("#wcentrocostos91_edit option").removeAttr("selected");
                    });

                    setearCamposHtml(data.campos_articulo);
                    $("#div_debug_log").append(data.debug_log);
                    // if(data.campos_articulo.wfracciones59_defipo_edit == 'on')
                    // {
                        activarCamposIPODEditable();
                    // }

                    if(data.campos_articulo.wesdestock91_edit == 'on')
                    {
                        $("#div_form_crear_stock_editable").show();
                    }
                    else
                    {
                        $("#wcentrocostos91_edit").removeClass('campoRequerido');
                        $("#div_form_crear_stock_editable").hide();
                    }

                    if(data.campos_articulo.wespecial08_edit == 'on')
                    {
                        $("#tabla_especiales08_editable > tbody").append(data.campos_articulo.wespecial08_edit_html);
                        $("#tr_definir_articulo_especial_editable").show();
                    }

                    if(data.campos_articulo.wequivalente_edit == 'on')
                    {
                        $("#tabla_equivalente_editable > tbody").append(data.campos_articulo.wequivalente_edit_html);
                        $("#tr_definir_articulo_equivalente_editable").show();
                    }

                    $("#tr_definir_familia_articulo_editable").show("500");
                    $("#tr_definir_tipo_articulo_editable").show("500");
                    $("#tr_definir_fraccion_articulo_editable").show("500");
                    $("#tr_btn_editar_articulo").show("500");
                    $('#td_contenedor_lista_editable').hide();
                    $("#td_contenedor_editable").show();
                }
            },
            "json"
        );
    }

    function verConfigurar(esmedicamento_26,id_59,id_familia_115,wcodarticulo,wpresentacion,wnomgenerico,wnomcomercial,wunidad26,unidad_59,fraccion_59,wgrupo26,wnomgrupo26,cod_familia_115,id_root_64)
    {
        reiniciarCamposConfiguracion();
        $("#esmedicamento_26").val(esmedicamento_26);
        $("#id_59").val(id_59);
        $("#id_familia_115").val(id_familia_115);
        $("#wcodarticulo").val(wcodarticulo);
        $("#wpresentacion").val(wpresentacion);
        $("#unidad_59").val(unidad_59);
        $("#fraccion_59").val(fraccion_59);
        $("#wgrupo26").val(wgrupo26);
        $("#wnomgrupo26").html(wnomgrupo26);
        $("#wfracciones59_defart").val(wcodarticulo);
        $("#id_root_64").val(id_root_64);
        $("#nomgen_root_64").val(wnomgenerico);
        $("#wcum64descripcion").html(wnomgenerico);

        $("#wnomgenerico").html(wnomgenerico);
        $("#wnomcomercial").html(wnomcomercial);

        $("#wcodarticulo_view").html(wcodarticulo);
        $("#wcodarticulo_fraccion_view").html(wcodarticulo);
        $("#wnomcomercial_view").html(wnomcomercial);
        $("#wunidad26_view").html(wunidad26);

        if(esmedicamento_26 != '' && esmedicamento_26 != 'off')
        {
            /*  Se inactiva si se detecta que el articulo ya es medicamento, si es asi no se deja cambiar porque otros medicamentos pueden depender o tener ese mismo tipo
                y si se cambia el tipo entonces se cambiaría tambien para los demás que tengas el mismo grupo, solo se dejará activo si no es medicamento
                porque su grupo no existe en "000066".*/
            $("#wtipomedicamento66").attr("disabled","disabled");

            $("#wesmedicamento").attr("checked","checked");
            $("#wesmedicamento").attr("disabled","disabled");
            $("#tr_definir_familia_articulo").show();
            $("#tr_definir_tipo_articulo").show();
            if(cod_familia_115 != '' && id_59 == '')
            {
                $("#tr_definir_fraccion_articulo").show();
            }
            if(id_root_64 == '')
            {
                $("#tr_definir_cums").show();
            }
            $("#tr_btn_configurar_articulo").show();
        }
        else
        {
            $("#wesmedicamento").removeAttr("checked");
            $("#wesmedicamento").removeAttr("disabled");
            $("#tr_definir_cums").hide();
            $("#wcum64codigo").val('');
            $("#wcum64equivale").val('');
        }

        $("#wfamilia26").val(cod_familia_115);

        $('#td_contenedor_noconfigurados').hide();
        $("#td_contenedor_configuracion").show();
    }

    function reiniciarCamposEditar()
    {
        $("#wcodarticulo_edit").val('');
        $("#id_66_esmedicamento_edit").val('');
        $("#id_59_edit").val('');
        $("#id_familia_115_edit").val('');
        $("#id_68_edit").val('');
        $("#id_root_64_edit").val('');


        $("#wnomgenerico_edit").html('');
        $("#wnomcomercial_edit").html('');
        $("#wcodarticulo_view_edit").html('');
        $("#wcodarticulo_fraccion_view_edit").html('');
        $("#wnomcomercial_view_edit").html('');
        $("#wunidad26_view_edit").html('');
    }

    function reiniciarCamposConfiguracion()
    {
        $("#esmedicamento_26").val('off');
        $("#id_59").val('');
        $("#id_familia_115").val('');
        $("#wcodarticulo").val('');
        $("#wpresentacion").val('');
        $("#unidad_59").val('');
        $("#fraccion_59").val('');
        $("#wgrupo26").val('');
        $("#wnomgrupo26").html('');
        $("#id_root_64").val('');
        $("#wcum64descripcion").html('');


        $("#wnomgenerico").html('');
        $("#wnomcomercial").html('');
        $("#wcodarticulo_view").html('');
        $("#wcodarticulo_fraccion_view").html('');
        $("#wnomcomercial_view").html('');
        $("#wunidad26_view").html('');
    }

    function volverAListaEditable()
    {
        // alert('ok');
        $("#tr_definir_familia_articulo_editable").hide();
        $("#tr_definir_tipo_articulo_editable").hide();
        $("#tr_definir_fraccion_articulo_editable").hide();
        $("#tr_definir_articulo_especial_editable").hide();
        $("#tr_definir_articulo_equivalente_editable").hide();
        $("#tr_btn_editar_articulo").hide();

        $("#td_contenedor_editable").hide();
        $('#td_contenedor_lista_editable').show();
        reiniciarSeccionDefinirFraccionEditable();
    }

    function volverALista()
    {
        //alert('ok');
        $("#tr_definir_familia_articulo").hide();
        $("#tr_definir_tipo_articulo").hide();
        $("#tr_definir_fraccion_articulo").hide();
        $("#tr_definir_cums").hide();
        $("#tr_btn_configurar_articulo").hide();

        $('#td_contenedor_configuracion').hide();
        $("#td_contenedor_noconfigurados").show();
        reiniciarSeccionDefinirFraccion();
    }


    function soloNumeros(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
    }

    function soloNumerosLetras(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
         if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) || charCode == 8)
            return true;

         return false;
    }

    /**
     * Para aceptar caracteres numéricos, letras y algunos otros caracteres permitidos
     *
     * @return unknown
     */
    function soloCaracteresPermitidos(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        // alert(charCode);
        /*
            (charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) // Números, letras minusculas y mayusculas
            (charCode >= 40 && charCode <= 46 ) //    )(*+,-.
            charCode == 8 // tecla borrar
            charCode == 32 // caracter espacio
            charCode == 95 // caracter _
        */
        if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 )
                || (charCode >= 40 && charCode <= 46 )
                || charCode == 8
                || charCode == 32
                || charCode == 95)
        {
            return true;
        }

         return false;
    }

    function guardarMaestroFamilia(prefijo_campo,prefino_cont)
    {
        var codigo_flia = $("#wcod_newflia"+prefijo_campo).val();
        var nombre_flia = $("#wnom_newflia"+prefijo_campo).val();

        if(codigo_flia != '' && nombre_flia != '')
        {
            var existe_flia = false;
            $("#div_form_crear_flia"+prefino_cont).find("input:hidden[id^=wfamilia114_]").each(function(){
                if($(this).val() == codigo_flia)
                { existe_flia = true; }
            });
            if(!existe_flia)
            {
                $.post("addor_medicamentos.php",
                {
                    consultaAjax: '',
                    wemp_pmla   : $("#wemp_pmla").val(),
                    accion      : 'insert',
                    form        : 'nueva_familia',
                    codigo_flia : codigo_flia,
                    nombre_flia : nombre_flia,
                    prefijo_campo: prefijo_campo,
                    prefino_cont: prefino_cont
                },
                function(data){
                        if(data.error == 1) { alert(data.mensaje); }
                        else
                        {
                            verOcultarForm('div_form_crear_flia'+prefino_cont);
                            $("#tabla_familias"+prefino_cont+" > tbody").append(data.html);

                            if(prefino_cont != ''){
                                $("#tabla_familias > tbody").append(data.html);// actualiza en formulario de configuracion
                            }else if (prefino_cont == ''){
                                $("#tabla_familias_editable > tbody").append(data.html);// actualiza en formulario de edición
                            }

                            if(prefijo_campo != ''){
                                $("#wfamilia26").html(data.opciones); // actualiza en formulario de configuracion
                            }else if(prefijo_campo == ''){
                                $("#wfamilia26_edit").html(data.opciones); // actualiza en formulario de edición
                            }

                            $("#wfamilia26"+prefijo_campo).html(data.opciones);

                            $("#wcod_newflia"+prefijo_campo).val('');
                            $("#wnom_newflia"+prefijo_campo).val('');
                        }
                    },
                    "json"
                );
            }
            else
            {
                alert("[!] El codigo que escribio para la familia de medicamentos ya existe.")
            }
        }
        else
        {
            alert("[?] Va a crear una nueva Familia pero los campos estan incompletos.");
        }
    }

    function guardarMaestroUnidades(prefijo_campo,prefino_cont)
    {
        var codigo_unid = $('#wcod_newunidad'+prefijo_campo).val();
        var nombre_unid = $('#wnom_newunidad'+prefijo_campo).val();
        codigo_unid = codigo_unid.toUpperCase();
        nombre_unid = nombre_unid.toUpperCase();

        if(codigo_unid != '' && nombre_unid != '')
        {
            var existe_unidad = false;
            $("#div_form_crear_unidad"+prefino_cont).find("input:hidden[id^=wunidad27_]").each(function(){
                if($(this).val() == codigo_unid)
                { existe_unidad = true; }
            });

            if(!existe_unidad)
            {
                $.post("addor_medicamentos.php",
                {
                    consultaAjax: '',
                    wemp_pmla   : $("#wemp_pmla").val(),
                    accion      : 'insert',
                    form        : 'nueva_unidad',
                    codigo : codigo_unid,
                    nombre : nombre_unid,
                    prefijo_campo : prefijo_campo,
                    prefino_cont  : prefino_cont
                },
                function(data){
                        if(data.error == 1) { alert(data.mensaje); }
                        else
                        {
                            verOcultarForm('div_form_crear_unidad'+prefino_cont);
                            $("#tabla_unidades"+prefino_cont+" > tbody").append(data.html);
                            if(prefino_cont != ''){
                                $("#tabla_unidades > tbody").append(data.html); // actualiza en formulario de configuracion
                            }else if (prefino_cont == ''){
                                $("#tabla_unidades_editable > tbody").append(data.html); // actualiza en formulario de edición
                            }

                            if(prefijo_campo != ''){
                                $("#wfracciones59_deffru").html(data.opciones); // actualiza en formulario de configuracion
                            }else if (prefijo_campo == ''){
                                $("#wfracciones59_deffru_edit").html(data.opciones); // actualiza en formulario de edición
                            }

                            $("#wfracciones59_deffru"+prefijo_campo).html(data.opciones);

                            $("#wcod_newunidad"+prefijo_campo).val('');
                            $("#wnom_newunidad"+prefijo_campo).val('');
                        }
                    },
                    "json"
                );
            }
            else
            {
                alert("[!] El codigo que escribio para la nueva unidad ya existe.")
            }
        }
        else
        {
            alert("[?] Va a crear una nueva Unidad pero los campos estan incompletos.");
        }
    }

    function verOcultarForm(form)
    {
        if ($('#'+form).is(":visible"))
        { $('#'+form).hide('slow'); }
        else
        { $('#'+form).show('slow'); }
    }

    function soloNumerosDecimales(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        //alert(charCode);
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
    }

    function validarExcluyente(incluido, excluido)
    {
        var datoE = $("#"+excluido).val();
        if(datoE != '')
        {
            $("#"+excluido).val("");
            // $("#"+incluido).attr("disabled",true);
            // $("#"+excluido).attr("disabled",false);
        }
    }

    function activarCamposIPODEditable()
    {
        if(!$("#wfracciones59_defipo_edit").is(":checked"))
        {
            $("#wfracciones59_defrci_edit").attr("disabled",false);
            $("#wfracciones59_defcai_edit").attr("disabled",false);
            $("#wfracciones59_defcas_edit").attr("disabled",false);
            $("#wfracciones59_defesc_edit").attr("disabled",false);
            $("#wfracciones59_defavc_edit").attr("disabled",false);
        }
        else
        {
            $("#wfracciones59_defrci_edit").attr("disabled",true);
            $("#wfracciones59_defrci_edit").removeAttr('checked');
            $("#wfracciones59_defcai_edit").val('');
            $("#wfracciones59_defcai_edit").attr("disabled",true);
            $("#wfracciones59_defcas_edit").val('');
            $("#wfracciones59_defcas_edit").attr("disabled",true);
            $("#wfracciones59_defesc_edit").val('');
            $("#wfracciones59_defesc_edit").attr("disabled",true);
            $("#wfracciones59_defavc_edit").val('');
            $("#wfracciones59_defavc_edit").attr("disabled",true);
            $("#div_tabla_fracciones59_editable").find(":input.requeridoIPOD").removeClass("campoRequerido");
        }
    }

    function activarCamposIPOD()
    {
        if(!$("#wfracciones59_defipo").is(":checked"))
        {
            $("#wfracciones59_defrci").attr("disabled",false);
            $("#wfracciones59_defcai").attr("disabled",false);
            $("#wfracciones59_defcas").attr("disabled",false);
            $("#wfracciones59_defesc").attr("disabled",false);
            $("#wfracciones59_defavc").attr("disabled",false);
        }
        else
        {
            $("#wfracciones59_defrci").attr("disabled",true);
            $("#wfracciones59_defrci").removeAttr('checked');
            $("#wfracciones59_defcai").val('');
            $("#wfracciones59_defcai").attr("disabled",true);
            $("#wfracciones59_defcas").val('');
            $("#wfracciones59_defcas").attr("disabled",true);
            $("#wfracciones59_defesc").val('');
            $("#wfracciones59_defesc").attr("disabled",true);
            $("#wfracciones59_defavc").val('');
            $("#wfracciones59_defavc").attr("disabled",true);
            $("#div_tabla_fracciones59").find(":input.requeridoIPOD").removeClass("campoRequerido");
        }
    }

    function guardarDefinicionFraccion()
    {
        /*  En ciertos casos aunque aparentemente no haga falta ningún campo por llenar y que sean obligatorios, puede aparecer el mansaje
            de alerta diciendo que hace falta información, esto sucede seguramente porque el campo oculto que tiene el código del articulo
            esta vacío, y por seguridad no deja que continúe el programa para que no se guarde en la 59 sin el código de articulo.*/
        var todo_ok = true;
        var wcodarticulo = $("#wcodarticulo").val();
        $("#div_tabla_fracciones59").find(":input.requerido").each(function(){
            var campo_id = $(this).attr("id");
            var valor = $("#"+campo_id).val();

            if(campo_id == 'wfracciones59_defdim' && valor == '')
            {
                valor = ($("#wfracciones59_defdom").val() == '') ? '': '-1';
            }
            else if(campo_id == 'wfracciones59_defdom' && valor == '')
            {
                valor = ($("#wfracciones59_defdim").val() == '') ? '': '-1';
            }

            if(campo_id == 'wfracciones59_deffra' && valor != '')
            {
                var regex_d = /^\d+(\.[0-9]?\d+)?$/; // si hay número entonces valída que si es decimal este bien escrito.
                valor = (regex_d.test(valor) == '') ? '': valor;
            }

            if(valor == '' || valor == null)
            {
                todo_ok = false;
                $(this).addClass('campoRequerido');
            }
            else
            {
                $(this).removeClass('campoRequerido');
            }
        });

        if(todo_ok)
        {
            var wfracciones59_defcco = $("#wfracciones59_defcco").val();
            var wfracciones59_defart = $("#wfracciones59_defart").val();
            var wfracciones59_deffru = $("#wfracciones59_deffru").val();
            var wfracciones59_deffra = $("#wfracciones59_deffra").val();
            var wfracciones59_defest = ($("#wfracciones59_defest").is(":checked")) ? 'on': 'off';
            var wfracciones59_defven = ($("#wfracciones59_defven").is(":checked")) ? 'on': 'off';
            var wfracciones59_defdie = $("#wfracciones59_defdie").val();
            var wfracciones59_defdis = ($("#wfracciones59_defdis").is(":checked")) ? 'on': 'off';
            var wfracciones59_defdup = ($("#wfracciones59_defdup").is(":checked")) ? 'on': 'off';
            var wfracciones59_defcon = ($("#wfracciones59_defcon").is(":checked")) ? 'on': 'off';
            var wfracciones59_defnka = ($("#wfracciones59_defnka").is(":checked")) ? 'on': 'off';
            var wfracciones59_defdim = $("#wfracciones59_defdim").val();
            var wfracciones59_defdom = $("#wfracciones59_defdom").val();
            var wfracciones59_defvia = ($("#wfracciones59_defvia").val() == null) ? '': $("#wfracciones59_defvia").val();
            var wfracciones59_defipo = ($("#wfracciones59_defipo").is(":checked")) ? 'on': 'off';

            if(wfracciones59_defipo == 'off')
            {
                todo_ok = true;
                // ACTIVAR ESTA SECCIÓN DE CÓDIGO SI SE REQUIERE QUE SE VALÍDE LA SECCIÓN DE IPOD
                /*
                $("#div_tabla_fracciones59").find(":input.requeridoIPOD").each(function(){
                    var campo_id = $(this).attr("id");
                    var valor = $("#"+campo_id).val();

                    if(valor == '' || valor == null)
                    {
                        todo_ok = false;
                        $(this).addClass('campoRequerido');
                    }
                    else
                    {
                        $(this).removeClass('campoRequerido');
                    }
                });*/
            }
            var wfracciones59_defrci = ($("#wfracciones59_defrci").is(":checked")) ? 'on': 'off';
            var wfracciones59_defcai = $("#wfracciones59_defcai").val();
            var wfracciones59_defcas = $("#wfracciones59_defcas").val();
            var wfracciones59_defesc = $("#wfracciones59_defesc").val();
            var wfracciones59_defavc = ($("#wfracciones59_defavc").val() == null) ? '': $("#wfracciones59_defavc").val();
            var wfracciones59_defmin = $("#wfracciones59_defmin").val();
            var wfracciones59_defmax = $("#wfracciones59_defmax").val();

            if(todo_ok)
            {
                $.post("addor_medicamentos.php",
                {
                    consultaAjax: '',
                    wemp_pmla   : $("#wemp_pmla").val(),
                    accion      : 'insert',
                    form        : 'insertar_en_59',
                    DEBUG       : $("#DEBUG").val(),
                    wcodarticulo: wcodarticulo,
                    wfracciones59_defcco : wfracciones59_defcco,
                    wfracciones59_defart : wfracciones59_defart,
                    wfracciones59_deffru : wfracciones59_deffru,
                    wfracciones59_deffra : wfracciones59_deffra,
                    wfracciones59_defest : wfracciones59_defest,
                    wfracciones59_defven : wfracciones59_defven,
                    wfracciones59_defdie : wfracciones59_defdie,
                    wfracciones59_defdis : wfracciones59_defdis,
                    wfracciones59_defdup : wfracciones59_defdup,
                    wfracciones59_defcon : wfracciones59_defcon,
                    wfracciones59_defnka : wfracciones59_defnka,
                    wfracciones59_defdim : wfracciones59_defdim,
                    wfracciones59_defdom : wfracciones59_defdom,
                    wfracciones59_defvia : wfracciones59_defvia,
                    wfracciones59_defipo : wfracciones59_defipo,
                    wfracciones59_defrci : wfracciones59_defrci,
                    wfracciones59_defcai : wfracciones59_defcai,
                    wfracciones59_defcas : wfracciones59_defcas,
                    wfracciones59_defesc : wfracciones59_defesc,
                    wfracciones59_defavc : wfracciones59_defavc,
                    wfracciones59_defmin : wfracciones59_defmin,
                    wfracciones59_defmax : wfracciones59_defmax
                },
                function(data){
                        if(data.error == 1) { alert(data.mensaje); $("#div_debug_log").append(data.debug_log); }
                        else
                        {
                            $("#unidad_59").val(wfracciones59_deffru);
                            $("#fraccion_59").val(wfracciones59_deffra);

                            $("#id_59").val(data.id_59);
                            $("#div_debug_log").append(data.debug_log);
                            $("#tr_definir_fraccion_articulo").hide('500');
                            reiniciarSeccionDefinirFraccion();
                        }
                    },
                    "json"
                );
            }
        }

        if(todo_ok == false){ alert("Verifique los campos, faltan campos por completar.");}
    }

    function editarArticulo()
    {
        var esmedicamento_26    = (($("#wesmedicamento_edit").is(":checked")) ? 'on': 'off');
        var westado_medicamento = (($("#westado_medicamento_edit").is(":checked")) ? 'on': 'off');
        var wtipomedicamento66  = $("#wtipomedicamento66_edit").val();
        var wfamilia26          = $("#wfamilia26_edit").val();
        var id_59               = $("#id_59_edit").val();
        var id_familia_115      = $("#id_familia_115_edit").val();
        var id_68_edit          = $("#id_68_edit").val();
        var wcodarticulo        = $("#wcodarticulo_edit").val();
        var wpresentacion       = $("#wpresentacion_edit").val();
        var unidad_59           = $("#unidad_59_edit").val();
        var fraccion_59         = $("#fraccion_59_edit").val();
        var wgrupo26            = $("#wgrupo26_edit").val();
        var wespecial08_edit    = (($("#wespecial08_edit").is(":checked")) ? 'on': 'off');
        var wequivalente_edit    = (($("#wequivalente_edit").is(":checked")) ? 'on': 'off');

        /*  En ciertos casos aunque aparentemente no haga falta ningún campo por llenar y que sean obligatorios, puede aparecer el mansaje
            de alerta diciendo que hace falta información, esto sucede seguramente porque el campo oculto que tiene el código del articulo
            esta vacío, y por seguridad no deja que continúe el programa para que no se guarde en la 59 sin el código de articulo.*/
        var todo_ok = true;
        $("#div_tabla_fracciones59_editable").find(":input.requerido").each(function(){
            var campo_id = $(this).attr("id");
            var valor = $("#"+campo_id).val();

            if(campo_id == 'wfracciones59_defdim_edit' && valor == '')
            {
                valor = ($("#wfracciones59_defdom_edit").val() == '') ? '': '-1';
            }
            else if(campo_id == 'wfracciones59_defdom_edit' && valor == '')
            {
                valor = ($("#wfracciones59_defdim_edit").val() == '') ? '': '-1';
            }

            if(campo_id == 'wfracciones59_deffra_edit' && valor != '')
            {
                var regex_c = /^\d+(\.[0-9]?\d+)?$/; // si hay número entonces valída que si es decimal este bien escrito.
                valor = (regex_c.test(valor) == '') ? '': valor;
            }

            if(valor == '' || valor == null)
            {
                todo_ok = false;
                $(this).addClass('campoRequerido');
            }
            else
            {
                $(this).removeClass('campoRequerido');
            }
        });

        if(wfamilia26 == '') { todo_ok = false; }

        if(todo_ok)
        {
            /* Captura y validaciones para la sección de fracciones */
            var wfracciones59_defcco = $("#wfracciones59_defcco_edit").val();
            var wfracciones59_defart = $("#wfracciones59_defart_edit").val();
            var wfracciones59_deffru = $("#wfracciones59_deffru_edit").val();
            var wfracciones59_deffra = $("#wfracciones59_deffra_edit").val();
            var wfracciones59_defest = ($("#wfracciones59_defest_edit").is(":checked")) ? 'on': 'off';
            var wfracciones59_defven = ($("#wfracciones59_defven_edit").is(":checked")) ? 'on': 'off';
            var wfracciones59_defdie = $("#wfracciones59_defdie_edit").val();
            var wfracciones59_defdis = ($("#wfracciones59_defdis_edit").is(":checked")) ? 'on': 'off';
            var wfracciones59_defdup = ($("#wfracciones59_defdup_edit").is(":checked")) ? 'on': 'off';
            var wfracciones59_defcon = ($("#wfracciones59_defcon_edit").is(":checked")) ? 'on': 'off';
            var wfracciones59_defnka = ($("#wfracciones59_defnka_edit").is(":checked")) ? 'on': 'off';
            var wfracciones59_defdim = $("#wfracciones59_defdim_edit").val();
            var wfracciones59_defdom = $("#wfracciones59_defdom_edit").val();
            var wfracciones59_defvia = ($("#wfracciones59_defvia_edit").val() == null) ? '': $("#wfracciones59_defvia_edit").val();
            var wfracciones59_defipo = ($("#wfracciones59_defipo_edit").is(":checked")) ? 'on': 'off';

            if(wfracciones59_defipo == 'off')
            {
                todo_ok = true;
                // ACTIVAR ESTA SECCIÓN DE CÓDIGO SI SE REQUIERE QUE SE VALÍDE LA SECCIÓN DE IPOD
                /*
                $("#div_tabla_fracciones59_editable").find(":input.requeridoIPOD").each(function(){
                    var campo_id = $(this).attr("id");
                    var valor = $("#"+campo_id).val();

                    if(valor == '' || valor == null)
                    {
                        todo_ok = false;
                        $(this).addClass('campoRequerido');
                    }
                    else
                    {
                        $(this).removeClass('campoRequerido');
                    }
                });
                */
            }
            var wfracciones59_defrci = ($("#wfracciones59_defrci_edit").is(":checked")) ? 'on': 'off';
            var wfracciones59_defcai = $("#wfracciones59_defcai_edit").val();
            var wfracciones59_defcas = $("#wfracciones59_defcas_edit").val();
            var wfracciones59_defesc = $("#wfracciones59_defesc_edit").val();
            var wfracciones59_defavc = ($("#wfracciones59_defavc_edit").val() == null) ? '': $("#wfracciones59_defavc_edit").val();
            var wfracciones59_defmin = $("#wfracciones59_defmin_edit").val();
            var wfracciones59_defmax = $("#wfracciones59_defmax_edit").val();

            /* Captura y validaciones para la sección de STOCK */
            var wesdestock91    = (($("#wesdestock91_edit").is(":checked")) ? 'on': 'off');
            var wtipostock91    = $("#wtipostock91_edit").val();
            var wcentrocostos91 = ($("#wcentrocostos91_edit").val() == null) ? '': $("#wcentrocostos91_edit").val();

            if(wesdestock91 == 'on' && wcentrocostos91 == '')
            {
                todo_ok = false;
            }
            $("#div_form_crear_stock_editable").find(":input.requeridoSTOCK").each(function(){
                    var campo_id = $(this).attr("id");
                    var valor = $("#"+campo_id).val();

                    if(valor == '' || valor == null)
                    {
                        $(this).addClass('campoRequerido');
                    }
                    else
                    {
                        $(this).removeClass('campoRequerido');
                    }
                });


            // Elimina posibles centros de costos configurados como especiales para un articulo.
            var tr_especiales = $("#tabla_especiales08_editable").find("tr[id^=tr_especial_]").length;
            var wlista_especiales = '';
            if(wespecial08_edit == 'on' && tr_especiales == 0)
            {
                todo_ok = false;
                alert("El articulo esta marcado como medicamento especial pero no se han especificado centros de costos.");
            }
            else
            {
                var separador = "";
                $("#tabla_especiales08_editable").find("tr[id^=tr_especial_]").each(function(){
                    var id_cco = $(this).find("td:first-child > input:hidden").attr("id"); // id del primer centro de costo.
                    var val_cco = $("#"+id_cco).val();
                    var cco = $("#wespecial08_arecco_"+val_cco).val();
                    var cva = $("#wespecial08_arecva_"+val_cco).val();
                    var cde = $("#wespecial08_arecde_"+val_cco).val();
                    var cma = $("#wespecial08_arecma_"+val_cco).val();
                    var neg = $("#wespecial08_areneg_"+val_cco).val();
                    var apl = $("#wespecial08_areapl_"+val_cco).val();

                    wlista_especiales = wlista_especiales+separador+cco+"|"+ cva+"|"+ cde+"|"+ cma+"|"+ neg+"|"+ apl;
                    separador = '=>';
                });
            }

            // Elimina posibles articulos configurados como equivalentes para un articulo.
            var tr_equivalente = $("#tabla_equivalente_editable").find("tr[id^=tr_equivalente_]").length;
            var wlista_equivalente = '';
            if(wequivalente_edit == 'on' && tr_equivalente == 0)
            {
                todo_ok = false;
                alert("El articulo esta marcado como => Requiere estar asociado a otros medicamentos. \n[!]Pero no se ha especificado un medicamento.");
            }
            else
            {
                var separador = "";
                $("#tabla_equivalente_editable").find("tr[id^=tr_equivalente_]").each(function(){
                    var id_cco = $(this).find("td:first-child > input:hidden").attr("id"); // id del primer centro de costo.
                    var val_art = $("#"+id_cco).val();
                    var art = $("#wequivalente_acpart_"+val_art).val();
                    var can = $("#wequivalente_acpcan_"+val_art).val();
                    var est = $("#wequivalente_acpest_"+val_art).val();

                    wlista_equivalente = wlista_equivalente+separador+art+"|"+ can+"|"+ est;
                    separador = '=>';
                });
            }

            var id_root_64          = $("#id_root_64_edit").val();
            var wcum64codigo        = $("#wcum64codigo_edit").val();
            var wcum64equivale      = $("#wcum64equivale_edit").val();
            var nomgen_root_64      = $("#nomgen_root_64_edit").val();

            if ( regEx_cum.test( wcum64codigo) )
            {//valido
                $("#wcum64codigo_edit").removeClass("campoRequerido");
            }
            else
            {//invalido
                todo_ok = false;
                $("#wcum64codigo_edit").addClass("campoRequerido");
            }

            if ( regEx_eq.test( wcum64equivale) )
            {//valido
                $("#wcum64equivale_edit").removeClass("campoRequerido");
            }
            else
            {//invalido
                todo_ok = false;
                $("#wcum64equivale_edit").addClass("campoRequerido");
            }

            if(todo_ok)
            {
                $.post("addor_medicamentos.php",
                    {
                        consultaAjax: '',
                        wemp_pmla   : $("#wemp_pmla").val(),
                        accion      : 'update',
                        form        : 'editar_articulo_1',
                        DEBUG       : $("#DEBUG").val(),
                        westado_medicamento : westado_medicamento,
                        esmedicamento_26    : esmedicamento_26,
                        wtipomedicamento66  : wtipomedicamento66,
                        wfamilia26          : wfamilia26,
                        id_59               : id_59,
                        id_familia_115      : id_familia_115,
                        id_68_edit          : id_68_edit,
                        wcodarticulo        : wcodarticulo,
                        wpresentacion       : wpresentacion,
                        unidad_59           : wfracciones59_deffru,
                        fraccion_59         : wfracciones59_deffra,
                        wgrupo26            : wgrupo26,
                        wfracciones59_defcco: wfracciones59_defcco,
                        wfracciones59_defart: wfracciones59_defart,
                        wfracciones59_deffru: wfracciones59_deffru,
                        wfracciones59_deffra: wfracciones59_deffra,
                        wfracciones59_defest: wfracciones59_defest,
                        wfracciones59_defven: wfracciones59_defven,
                        wfracciones59_defdie: wfracciones59_defdie,
                        wfracciones59_defdis: wfracciones59_defdis,
                        wfracciones59_defdup: wfracciones59_defdup,
                        wfracciones59_defcon: wfracciones59_defcon,
                        wfracciones59_defnka: wfracciones59_defnka,
                        wfracciones59_defdim: wfracciones59_defdim,
                        wfracciones59_defdom: wfracciones59_defdom,
                        wfracciones59_defvia: wfracciones59_defvia,
                        wfracciones59_defipo: wfracciones59_defipo,
                        wfracciones59_defrci: wfracciones59_defrci,
                        wfracciones59_defcai: wfracciones59_defcai,
                        wfracciones59_defcas: wfracciones59_defcas,
                        wfracciones59_defesc: wfracciones59_defesc,
                        wfracciones59_defavc: wfracciones59_defavc,
                        wfracciones59_defmin: wfracciones59_defmin,
                        wfracciones59_defmax: wfracciones59_defmax,
                        wesdestock91        : wesdestock91,
                        wtipostock91        : wtipostock91,
                        wcentrocostos91     : wcentrocostos91,
                        wlista_especiales   : wlista_especiales,
                        wlista_equivalente  : wlista_equivalente,
                        id_root_64          : id_root_64,
                        wcum64codigo        : wcum64codigo,
                        wcum64equivale      : wcum64equivale,
                        nomgen_root_64      : nomgen_root_64
                    },
                    function(data){
                            if(data.estado_med!='')
                            {
                                std = ((data.estado_med == 'on') ? ' <span style="color:green;font-weight:bold;">Activo</span>': ' <span style="color:orange;font-weight:bold;">Inactivo</span>');
                                $("#div_estado_edit").html(std);
                            }

                            if(data.error == 1)
                            {
                                // $("#esmedicamento_26").val(data.esmedicamento_26);
                                // $("#id_familia_115").val(data.id_familia_115);
                                // if(data.esmedicamento_26 != '')
                                // {
                                //     $("#wesmedicamento").attr("disabled","disabled");
                                // }
                                alert(data.mensaje);
                                $("#div_debug_log").append(data.debug_log);
                            }
                            else
                            {
                                if(id_root_64 == '')
                                {
                                    $("#id_root_64_edit").val(data.id_root_64_edit);
                                }
                                $("#div_debug_log").append(data.debug_log);

                                alert(data.mensaje+"\n[!] Medicamento editado.");
                                /*if(data.mensaje != '')
                                {
                                    alert(data.mensaje);
                                    // var txt = $("#tr_articulo_"+wcodarticulo+" td:first-child").html();
                                    // $("#tr_articulo_"+wcodarticulo+" td:first-child").html('<span class="correct">([!] Configurado)</span> '+txt);
                                    // $("#tr_articulo_"+wcodarticulo).removeAttr('onclick').click(function () { alert("Ya se configuro este articulo ("+wcodarticulo+")"); });
                                }*/
                            }
                        },
                        "json"
                    );
            }
        }

        if(todo_ok == false){ alert("Verifique los campos, faltan campos por completar.");}
    }

    function configurarArticulo()
    {
        var esmedicamento_26    = $("#esmedicamento_26").val();
        var wtipomedicamento66  = $("#wtipomedicamento66").val();
        var wfamilia26          = $("#wfamilia26").val();
        var id_59               = $("#id_59").val();
        var id_familia_115      = $("#id_familia_115").val();
        var wcodarticulo        = $("#wcodarticulo").val();
        var wpresentacion       = $("#wpresentacion").val();
        var unidad_59           = $("#unidad_59").val();
        var fraccion_59         = $("#fraccion_59").val();
        var wgrupo26            = $("#wgrupo26").val();
        var id_root_64          = $("#id_root_64").val();
        var wcum64codigo        = $("#wcum64codigo").val();
        var wcum64equivale      = $("#wcum64equivale").val();
        var nomgen_root_64      = $("#nomgen_root_64").val();

        var confirm_nomedicamento = false;
        if(esmedicamento_26 == 'off')
        {
            if(confirm("Si continua, el articulo ("+wcodarticulo+") sera guardado como -No es medicamento-\n¿Desea guardarlo como -No es medicamento- ?"))
            { confirm_nomedicamento = true; }
        }

        if((wfamilia26 != '' && id_59 != '') || confirm_nomedicamento)
        {
            var es_ok = true;
            // if(id_root_64 == '' && (wcum64codigo == '' || wcum64equivale == ''))
            // {
            //     es_ok = false;
            // }

            if(id_root_64 == '' && !confirm_nomedicamento)
            {
                if ( regEx_cum.test( wcum64codigo) )
                {//valido
                    $("#wcum64codigo").removeClass("campoRequerido");
                }
                else
                {//invalido
                    es_ok = false;
                    $("#wcum64codigo").addClass("campoRequerido");
                }

                if ( regEx_eq.test( wcum64equivale) )
                {//valido
                    $("#wcum64equivale").removeClass("campoRequerido");
                }
                else
                {//invalido
                    es_ok = false;
                    $("#wcum64equivale").addClass("campoRequerido");
                }
            }

            if(es_ok || confirm_nomedicamento)
            {
                $.post("addor_medicamentos.php",
                {
                    consultaAjax: '',
                    wemp_pmla   : $("#wemp_pmla").val(),
                    accion      : 'insert',
                    form        : 'configurar_articulo_1',
                    DEBUG       : $("#DEBUG").val(),
                    esmedicamento_26    : esmedicamento_26,
                    wtipomedicamento66  : wtipomedicamento66,
                    wfamilia26          : wfamilia26,
                    id_59               : id_59,
                    id_familia_115      : id_familia_115,
                    wcodarticulo        : wcodarticulo,
                    wpresentacion       : wpresentacion,
                    unidad_59           : unidad_59,
                    fraccion_59         : fraccion_59,
                    wgrupo26            : wgrupo26,
                    id_root_64          : id_root_64,
                    wcum64codigo        : wcum64codigo,
                    wcum64equivale      : wcum64equivale,
                    nomgen_root_64      : nomgen_root_64
                },
                function(data){
                        if(data.error == 1)
                        {
                            $("#esmedicamento_26").val(data.esmedicamento_26);
                            $("#id_familia_115").val(data.id_familia_115);
                            if(data.esmedicamento_26 != '' && data.esmedicamento_26 != 'off')
                            {
                                $("#wesmedicamento").attr("disabled","disabled");
                            }
                            alert(data.mensaje);
                            $("#div_debug_log").append(data.debug_log);
                        }
                        else
                        {
                            $("#esmedicamento_26").val(data.esmedicamento_26);
                            $("#id_familia_115").val(data.id_familia_115);
                            $("#id_root_64").val(data.id_root_64);
                            if(data.id_root_64 != '')
                            {
                                $("#tr_definir_cums").hide("500");
                            }

                            if(data.esmedicamento_26 != '' && data.esmedicamento_26 != 'off')
                            {
                                $("#wesmedicamento").attr("disabled","disabled");
                                $("#wcum64codigo").val("");
                                $("#wcum64equivale").val("");
                            }
                            $("#div_debug_log").append(data.debug_log);
                            reiniciarSeccionDefinirFraccion();
                            if(data.mensaje != '')
                            {
                                var txt = $("#tr_articulo_"+wcodarticulo+" td:first-child").html();
                                var txtb = $("#tr_articulo_busca_noconf_"+wcodarticulo+" td:first-child").html();

                                // En la lista ordenada de no configurados.
                                $("#tr_articulo_"+wcodarticulo+" td:first-child").html('<span class="correct">([!] Configurado)</span> '+txt);
                                $("#tr_articulo_"+wcodarticulo).removeAttr('onclick').click(function () { alert("Ya se configuro este articulo ("+wcodarticulo+")"); });

                                // En la lista de búsqueda de no configurados.
                                $("#tr_articulo_busca_noconf_"+wcodarticulo+" td:first-child").html('<span class="correct">([!] Configurado)</span> '+txtb);
                                $("#tr_articulo_busca_noconf_"+wcodarticulo).removeAttr('onclick').click(function () { alert("Ya se configuro este articulo ("+wcodarticulo+")\nPara editarlo identifiquelo en la lista de medicamentos configurados."); });

                                alert(data.mensaje);
                                volverALista();
                            }
                        }
                    },
                    "json"
                );
            }
            else
            {
                alert("No se puede configurar, faltan datos del CUMS.");
            }
        }
        else
        {
            alert("No se pudo configurar, es posible que falte seleccionar la familia del articulo o falte definir la fraccion del articulo.");
        }
    }

    function reiniciarSeccionDefinirFraccionEditable()
    {
        //$("#wfracciones59_defcco").val('');
        $("#wfracciones59_defart_edit").val('');
        $("#wfracciones59_deffru_edit").val('');
        $("#wfracciones59_deffra_edit").val('');
        $("#wfracciones59_defest_edit").attr("checked","checked");
        $("#wfracciones59_defven_edit").removeAttr("checked");
        $("#wfracciones59_defdie_edit").val('');
        $("#wfracciones59_defdis_edit").removeAttr("checked");
        $("#wfracciones59_defdup_edit").removeAttr("checked");
        $("#wfracciones59_defcon_edit").removeAttr("checked");
        $("#wfracciones59_defnka_edit").removeAttr("checked");
        $("#wfracciones59_defdim_edit").val('');
        $("#wfracciones59_defdom_edit").val('');
        $("#wfracciones59_defvia_edit").val('');
        $("#wfracciones59_defipo_edit").removeAttr("checked");
        $("#wfracciones59_defrci_edit").removeAttr("checked");
        activarCamposIPODEditable();
    }

    function reiniciarSeccionDefinirFraccion()
    {
        //$("#wfracciones59_defcco").val('');
        $("#wfracciones59_defart").val('');
        $("#wfracciones59_deffru").val('');
        $("#wfracciones59_deffra").val('');
        $("#wfracciones59_defest").attr("checked","checked");
        $("#wfracciones59_defven").removeAttr("checked");
        $("#wfracciones59_defdie").val('');
        $("#wfracciones59_defdis").removeAttr("checked");
        $("#wfracciones59_defdup").removeAttr("checked");
        $("#wfracciones59_defcon").removeAttr("checked");
        $("#wfracciones59_defnka").removeAttr("checked");
        $("#wfracciones59_defdim").val('');
        $("#wfracciones59_defdom").val('');
        $("#wfracciones59_defvia").val('');
        $("#wfracciones59_defipo").removeAttr("checked");
        $("#wfracciones59_defrci").removeAttr("checked");
            activarCamposIPOD();
    }

    function confirmarMedicamentoEditable()
    {
        var wcodarticulo = $("#wcodarticulo_edit").val();
        var wfamilia26 = $("#wfamilia26").val();
        var id_59 = $("#id_59").val();

        if(wcodarticulo != '' && $("#wesmedicamento").is(":checked"))
        {
            $("#tr_definir_familia_articulo").show('500');
            $("#tr_definir_tipo_articulo").show('500');
            if(wfamilia26 != '' && id_59 == '')
            {
                $("#tr_definir_fraccion_articulo").show('500');
            }
            $("#tr_btn_configurar_articulo").show('500');
        }
        else if(wcodarticulo == '')
        {
            alert("No es posible leer el codigo del articulo, No es posible continuar con la configuracion.");
            $("#wesmedicamento").removeAttr("checked")
        }
        else if(!$("#wesmedicamento").is(":checked"))
        {
            $("#tr_definir_familia_articulo").hide('300');
            $("#tr_definir_tipo_articulo").hide('300');
            $("#wfamilia26").val('');
            $("#tr_definir_fraccion_articulo").hide('500');
            reiniciarSeccionDefinirFraccion();
            //$("#tr_btn_configurar_articulo").hide('300');
        }
    }

    function confirmarMedicamento()
    {
        var wcodarticulo = $("#wcodarticulo").val();
        var wfamilia26 = $("#wfamilia26").val();
        var id_59 = $("#id_59").val();
        var id_root_64 = $("#id_root_64").val();

        if(wcodarticulo != '' && $("#wesmedicamento").is(":checked"))
        {
            $("#esmedicamento_26").val('on');
            $("#tr_definir_familia_articulo").show('500');
            $("#tr_definir_tipo_articulo").show('500');
            if(wfamilia26 != '' && id_59 == '')
            {
                $("#tr_definir_fraccion_articulo").show('500');
            }
            if(id_root_64 == '')
            {
                $("#tr_definir_cums").show('500');
            }
            $("#tr_btn_configurar_articulo").show('500');
        }
        else if(wcodarticulo == '')
        {
            alert("No es posible leer el codigo del articulo, No es posible continuar con la configuracion.");
            $("#wesmedicamento").removeAttr("checked")
        }
        else if(!$("#wesmedicamento").is(":checked"))
        {
            $("#esmedicamento_26").val('off');
            $("#tr_definir_familia_articulo").hide('300');
            $("#tr_definir_tipo_articulo").hide('300');
            $("#wfamilia26").val('');
            $("#tr_definir_fraccion_articulo").hide('500');
            reiniciarSeccionDefinirFraccion();
            //$("#tr_btn_configurar_articulo").hide('300');
            $("#tr_definir_cums").hide('300');
            $("#wcum64codigo").val('');
            $("#wcum64equivale").val('');
        }
    }

    function verDefinirFraccionEditable()
    {
        var id_59 = $("#id_59_edit").val();
        var option = $("#wfamilia26_edit").val();
        var idfamilia = $("#id_familia_115_edit").val();
        // if(option != '' && id_59 == '')
        // {
        //     $("#tr_definir_fraccion_articulo_editable").show('500');
        // }
        // else
        // {
        //     $("#tr_definir_fraccion_articulo_editable").hide('500');
        //     reiniciarSeccionDefinirFraccionEditable();
        // }
    }

    function verFormularioStockEditable(div_id)
    {
        if($("#wesdestock91_edit").is(":checked"))
        {
            $("#"+div_id).show('500');
        }
        else
        {
            $("#"+div_id).hide('500');
            $("#wcentrocostos91_edit").removeClass('campoRequerido');
            $("#wcentrocostos91_edit").val('');
            //reiniciarSeccionStockEditable();
        }
    }

    function verFormularioEspecialEditable(div_id)
    {
        if($("#wespecial08_edit").is(":checked"))
        {
            $("#"+div_id).show('500');
        }
        else
        {
            $("#"+div_id).hide('500');
            // Elimina posibles centros de costos configurados como especiales para un articulo.
            $("#tabla_especiales08_editable").find("tr[id^=tr_especial_]").each(function(){
                $(this).remove();
            });
            //reiniciarSeccionStockEditable();
        }
    }

    function verFormularioEquivalenteEditable(div_id)
    {
        if($("#wequivalente_edit").is(":checked"))
        {
            $("#"+div_id).show('500');
        }
        else
        {
            $("#"+div_id).hide('500');
            // Elimina posibles centros de costos configurados como especiales para un articulo.
            $("#tabla_equivalente_editable").find("tr[id^=tr_equivalente_]").each(function(){
                $(this).remove();
            });
            //reiniciarSeccionStockEditable();
        }
    }

    function verDefinirFraccion()
    {
        var id_59 = $("#id_59").val();
        var option = $("#wfamilia26").val();
        var idfamilia = $("#id_familia_115").val();
        if(option != '' && id_59 == '')
        {
            $("#tr_definir_fraccion_articulo").show('500');
        }
        else
        {
            $("#tr_definir_fraccion_articulo").hide('500');
            reiniciarSeccionDefinirFraccion();
        }
    }

    /**
     * Se encarga de recorrer los id de la respuesta y setear los valores en cada uno de los campos o input html.
     *
     * @return unknown
     */
    var arregloDependientes = new Array(); // arreglo de selects que son dependientes de otros selects.
    function setearCamposHtml(arr)
    {
        var ejecutarDepend = new Array();
        $.each(arr, function(index, value) {
            if ($("#"+index).length > 0)
            {
                // if(index == 'wfracciones59_defvia_edit') { alert(index+'-'+value+'|'+$("#"+index).attr('multiple')); }

                if($("#"+index).is("input,select") && $("#"+index).attr("type") != 'checkbox' && $("#"+index).attr('multiple') == undefined) // Si es input o select entonces escribe en un campo u opción de un select sino escribe en html.
                {
                    $("#"+index).val(value);
                    //Si es un select y adicionalmente tiene el evento onchange entonces debe ejecutar el evento para que el select dependiente se cargue con las opciones válidas.
                    if($("#"+index).is("select") && $("#"+index).attr('onchange'))
                    { ejecutarDepend.push( index );  }// "i"=antioquia
                    if($("#"+index).is('.dependiente')) //Municipios
                    { arregloDependientes[index] = value;}//Arreglo en la posicion "wmuni"=medellin
                }
                else if($("#"+index).attr("type") == 'checkbox')
                {
                    if(value == 'on') { $("#"+index).attr("checked","checked"); }
                    else if(value == 'off') { $("#"+index).removeAttr("checked"); }
                }
                else if($("#"+index).attr('multiple') != undefined)
                {
                    var opciones = value.split(",");

                    $("#"+index+" option").each(function(){
                            //alert(jQuery.inArray($(this).val(), opciones));
                            //if(opciones.indexOf($(this).val()) != -1) { $(this).attr("selected","selected"); } // No funciona en IE  >:(
                            if((jQuery.inArray($(this).val(), opciones)) != -1) { $(this).attr("selected","selected"); }
                    });
                }
                else
                { $("#"+index).html(value); }
            }
        });
        for (var i = 0, elemento; elemento = ejecutarDepend[i]; i++) {
            $("#"+elemento).trigger("change");
        }
    }

    function adicionarCCOEspeciales()
    {
        /*
        wespecial08_id
        wespecial08_arecco
        wespecial08_arecva
        wespecial08_arecde
        wespecial08_arecma
        wespecial08_areneg
        wespecial08_areapl
        */
        var wespecial08_id     = $("#wespecial08_id_add").val();
        var wespecial08_arecco = $("#wespecial08_arecco_add").val();
        var texto_cco          = $("#wespecial08_arecco_add option:selected").text();
        var wespecial08_arecva = ($("#wespecial08_arecva_add").is(":checked")) ? 'on': 'off';
        var wespecial08_areneg = ($("#wespecial08_areneg_add").is(":checked")) ? 'on': 'off';
        var wespecial08_areapl = ($("#wespecial08_areapl_add").is(":checked")) ? 'on': 'off';

        var wespecial08_arecde = parseInt($("#wespecial08_arecde_add").val());
        var wespecial08_arecma = parseInt($("#wespecial08_arecma_add").val());
        $("#wespecial08_arecde_add").removeClass("campoRequerido");
        $("#wespecial08_arecma_add").removeClass("campoRequerido");
        $("#wespecial08_arecco_add").removeClass("campoRequerido");

        if(wespecial08_arecde == '') { $("#wespecial08_arecde_add").addClass("campoRequerido"); }
        if(wespecial08_arecma == '') { $("#wespecial08_arecma_add").addClass("campoRequerido"); }
        if(wespecial08_arecco == '') { $("#wespecial08_arecco_add").addClass("campoRequerido"); }

        var existe_cco = $("#wespecial08_id_"+wespecial08_arecco).length;
        if(existe_cco == 0 || wespecial08_id == wespecial08_arecco)
        {
            if(wespecial08_arecde != "" && wespecial08_arecma != "" && wespecial08_arecco != "")
            {
                var valid_ok = true;
                if(wespecial08_arecva == 'on')
                {
                    if(wespecial08_arecde > wespecial08_arecma) // Si es cantidad variable entonces la cantidad por defecto no puede sobrepasar la cantidad máxima.
                    {
                        valid_ok = false;
                        alert("Cuando esta seleccionado cantidad variable, la cantidad por defecto no puede superar la cantidad maxima.");
                    }
                    else
                    {
                        // var modulo_ = parseInt(wespecial08_arecma) % parseInt(wespecial08_arecde);

                        //alert(wespecial08_arecde+'%'+wespecial08_arecma+'-'+modulo_);
                        if((wespecial08_arecma % wespecial08_arecde) != 0)
                        {
                            valid_ok = false;
                            alert("No se puede adicionar a la lista, la cantidad maxima debe ser multiplo de la cantidad por defecto.");
                        }
                    }
                }
                else
                {
                    if(wespecial08_arecde != wespecial08_arecma)
                    {
                        valid_ok = false;
                        alert("la cantidad por defecto y la cantidad maxima deben ser iguales.");
                    }
                }

                if(valid_ok)
                {
                    if(wespecial08_id == '')
                    {
                        $.post("addor_medicamentos.php",
                        {
                            consultaAjax: '',
                            wemp_pmla   : $("#wemp_pmla").val(),
                            accion      : 'load',
                            form        : 'agregar_especiales',
                            DEBUG       : $("#DEBUG").val(),
                            wespecial08_id : wespecial08_id,
                            wespecial08_arecco : wespecial08_arecco,
                            texto_cco          : texto_cco,
                            wespecial08_arecva : wespecial08_arecva,
                            wespecial08_arecde : wespecial08_arecde,
                            wespecial08_arecma : wespecial08_arecma,
                            wespecial08_areneg : wespecial08_areneg,
                            wespecial08_areapl : wespecial08_areapl
                        },
                        function(data){
                                if(data.error == 1)
                                {
                                    alert(data.mensaje);
                                    $("#div_debug_log").append(data.debug_log);
                                }
                                else
                                {
                                    $("#tabla_especiales08_editable > tbody").append(data.html);
                                }
                            },
                            "json"
                        );
                    }
                    else
                    {
                        // $("#wespecial08_id_add").val("");
                        // $("#wespecial08_arecco_add").val('');
                        $("#wespecial08_arecva_"+wespecial08_id).val(wespecial08_arecva);
                        $("#wespecial08_arecva_sp_"+wespecial08_id).html(wespecial08_arecva);

                        $("#wespecial08_arecde_"+wespecial08_id).val(wespecial08_arecde);
                        $("#wespecial08_arecde_sp_"+wespecial08_id).html(wespecial08_arecde);

                        $("#wespecial08_arecma_"+wespecial08_id).val(wespecial08_arecma);
                        $("#wespecial08_arecma_sp_"+wespecial08_id).html(wespecial08_arecma);

                        $("#wespecial08_areneg_"+wespecial08_id).val(wespecial08_areneg);
                        $("#wespecial08_areneg_sp_"+wespecial08_id).html(wespecial08_areneg);

                        $("#wespecial08_areapl_"+wespecial08_id).val(wespecial08_areapl);
                        $("#wespecial08_areapl_sp_"+wespecial08_id).html(wespecial08_areapl);
                    }

                    $("#wespecial08_id_add").val("");
                    $("#wespecial08_arecco_add").val('');
                    $("#wespecial08_arecva_add").removeAttr("checked");
                    $("#wespecial08_areneg_add").removeAttr("checked");
                    $("#wespecial08_areapl_add").removeAttr("checked");
                    $("#wespecial08_arecde_add").val('');
                    $("#wespecial08_arecma_add").val('');
                }
            }
            else
            {
                alert("No se puede adicionar registro a la lista, hacen falta datos.");
            }
        }
        else
        {
            alert("El centro de costo ya esta en la lista.")
        }
    }

    function adicionarEquivalente()
    {
        /*
        wespecial08_id
        wespecial08_arecco
        wespecial08_arecva
        wespecial08_arecde
        wespecial08_arecma
        wespecial08_areneg
        wespecial08_areapl
        */
        var wequivalente_id     = $("#wequivalente_id_add").val(); // tiene un codigo de articulo que se está editando
        var wequivalente_acpart = $("#wequivalente_acpart_add").val(); // Un artículo que se ha seleccionado.
        var texto_art          = $("#wequivalente_acpart_add option:selected").text();
        var wequivalente_acpest = ($("#wequivalente_acpest_add").is(":checked")) ? 'on': 'off';

        //var wequivalente_acpcan = parseInt($("#wequivalente_acpcan_add").val());
        var wequivalente_acpcan = $("#wequivalente_acpcan_add").val();

        $("#wequivalente_acpart_add").removeClass("campoRequerido");
        $("#wequivalente_acpcan_add").removeClass("campoRequerido");

        if(wequivalente_acpart == '') { $("#wequivalente_acpart_add").addClass("campoRequerido"); }
        if(wequivalente_acpcan == '' || parseInt(wequivalente_acpcan) <= 0) { $("#wequivalente_acpcan_add").addClass("campoRequerido"); }

        var existe_art = $("#wequivalente_id_"+wequivalente_acpart).length;
        if(existe_art == 0 || wequivalente_id == wequivalente_acpart)
        {
            if(wequivalente_acpart != "" && (wequivalente_acpcan != "" && parseInt(wequivalente_acpcan) > 0))
            {
                var valid_ok = true;

                if(valid_ok)
                {
                    if(wequivalente_id == '')
                    {
                        $.post("addor_medicamentos.php",
                        {
                            consultaAjax: '',
                            wemp_pmla   : $("#wemp_pmla").val(),
                            accion      : 'load',
                            form        : 'agregar_equivalentes',
                            DEBUG       : $("#DEBUG").val(),
                            wequivalente_id : wequivalente_id,
                            wequivalente_acpart : wequivalente_acpart,
                            texto_art           : texto_art,
                            wequivalente_acpcan : wequivalente_acpcan,
                            wequivalente_acpest : wequivalente_acpest
                        },
                        function(data){
                                if(data.error == 1)
                                {
                                    alert(data.mensaje);
                                    $("#div_debug_log").append(data.debug_log);
                                }
                                else
                                {
                                    $("#tabla_equivalente_editable > tbody").append(data.html);
                                }
                            },
                            "json"
                        );
                    }
                    else
                    {
                        // $("#wespecial08_id_add").val("");
                        // $("#wespecial08_arecco_add").val('');
                        $("#wequivalente_acpcan_"+wequivalente_id).val(wequivalente_acpcan);
                        $("#wequivalente_acpcan_sp_"+wequivalente_id).html(wequivalente_acpcan);

                        $("#wequivalente_acpest_"+wequivalente_id).val(wequivalente_acpest);
                        $("#wequivalente_acpest_sp_"+wequivalente_id).html(wequivalente_acpest);
                    }

                    $("#wequivalente_id_add").val("");
                    $("#wequivalente_acpart_add").val('');
                    $("#wequivalente_acpest_add").removeAttr("checked");
                    $("#wequivalente_acpcan_add").val('');
                }
            }
            else
            {
                alert("No se puede adicionar registro a la lista, hacen falta datos.");
            }
        }
        else
        {
            alert("El Articulo ya esta en la lista.")
        }
    }

    function eliminarTrCcoEspecial(id_tr)
    {
        $("#"+id_tr).hide('slow');
        $("#"+id_tr).remove();
    }

    function eliminarTrEquivalente(id_tr)
    {
        $("#"+id_tr).hide('slow');
        $("#"+id_tr).remove();
    }

    function editarTrCcoEspecial(cco)
    {
        $("#wespecial08_id_add").val($("#wespecial08_id_"+cco).val());
        $("#wespecial08_arecco_add").val($("#wespecial08_arecco_"+cco).val());
        $("#wespecial08_arecde_add").val($("#wespecial08_arecde_"+cco).val());
        $("#wespecial08_arecma_add").val($("#wespecial08_arecma_"+cco).val());


        var wespecial08_arecva = $("#wespecial08_arecva_"+cco).val();
        var wespecial08_areneg = $("#wespecial08_areneg_"+cco).val();
        var wespecial08_areapl = $("#wespecial08_areapl_"+cco).val();

        if(wespecial08_arecva == 'on') { $("#wespecial08_arecva_add").attr("checked","checked"); }
        else { $("#wespecial08_arecva_add").removeAttr("checked"); }

        if(wespecial08_areneg == 'on') { $("#wespecial08_areneg_add").attr("checked","checked"); }
        else { $("#wespecial08_areneg_add").removeAttr("checked"); }

        if(wespecial08_areapl == 'on') { $("#wespecial08_areapl_add").attr("checked","checked"); }
        else { $("#wespecial08_areapl_add").removeAttr("checked"); }
    }

    function editarTrEquivalente(art)
    {
        $("#wequivalente_id_add").val($("#wequivalente_id_"+art).val());
        $("#wequivalente_acpart_add").val($("#wequivalente_acpart_"+art).val());
        $("#wequivalente_acpcan_add").val($("#wequivalente_acpcan_"+art).val());


        var wequivalente_acpest = $("#wequivalente_acpest_"+art).val();

        if(wequivalente_acpest == 'on') { $("#wequivalente_acpest_add").attr("checked","checked"); }
        else { $("#wequivalente_acpest_add").removeAttr("checked"); }
    }

    function validarArticuloEquivalente(ele)
    {
        var codart_equ = $("#"+ele.id).val();
        var codart_edit = $("#wcodarticulo_edit").val();
        if(codart_equ == codart_edit)
        {
            $("#"+ele.id).val('');
            alert("[!] No se permite asociar este articulo al mismo medicamento que se esta editando.");
        }
    }

    /**
     * Usada para desencadenar el evento de busqueda de un centro de costo de un cargo o de un usuario al momento de presionar la tecla enter en los campos de búsqueda.
     *
     * @return boolean
     */
    function enterBuscar(hijo,e)
    {
        tecla = (document.all) ? e.keyCode : e.which;
        if(tecla==13)
        {
            buscarArticuloEquivalente(hijo);
        }
        else { return true; }
        return false;
    }

    function cambioImagen(img1, img2)
    {
        $('#'+img1).css("display","none");
        $('#'+img2).css("display","block");
    }

    </script>


    <style type="text/css">
        .campoRequerido{
            border: 1px orange solid;
            background-color:lightyellow;
        }
        .fondo_banda1{
            background-color: #cccccc;
        }
        .fondo_banda2{
            background-color: #f2f2f2;
        }
        .textosSmall{
            font-size: 8pt;
        }
        .classOver{
            background-color: #ffffff;
        }
        .mayuscula{
        text-transform: uppercase;
        }
        .st_boton{
            /*font-size:10px;
            font-family:Verdana,Helvetica;
            font-weight:bold;
            color:white;
            background:#638cb5;
            border:0px;
            width:80px;
            height:19px;*/

           background-color: #4D90FE;
           background-image: -webkit-gradient(linear,left top,left bottom,from(#4D90FE),to(#4787ED));
           background-image: -moz-linear-gradient(top,#4D90FE,#4787ED);
           background-image: -ms-linear-gradient(top,#4D90FE,#4787ED);
           background-image: -o-linear-gradient(top,#4D90FE,#4787ED);
           background-image: -webkit-linear-gradient(top,#4D90FE,#4787ED);
           background-image: linear-gradient(top,#4D90FE,#4787ED);
          filter: progid:DXImageTransform.Microsoft.gradient
           (startColorStr='#4d90fe',EndColorStr='#4787ed');
           border: 1px solid #3079ED;
           -moz-border-radius: 2px;
           -webkit-border-radius: 2px;
           border-radius: 2px;
           -moz-user-select: none;
           -webkit-user-select: none;
           color: white;
           display: inline-block;
           font-weight: bold;
           height: 25px;
           line-height: 20px;
           text-align: center;
           text-decoration: none;
           padding: 0 8px;
           margin: 0px auto;
          font: 13px/27px Arial,sans-serif;
          cursor:pointer;
        }
        .error{
            font-weight: bold;
            color: red;
        }
        .correct{
            font-weight: bold;
            color: green;
        }
        .endlog{
            font-weight: bold;
            color: orange;
        }

        .faltaEsMed{
            background-color: #F781BE;
        }
        .falta59{
            background-color: #5882FA;
        }
        .falta115{
            background-color: #D7DF01;
        }
        .faltaCum{
            background-color: #3ADF00;
        }
        #tooltip{
            color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:2px;opacity:0.9;font-size: 8pt;
        }
        #tooltip h3, #tooltip div{
            margin:0; width:auto
        }
    </style>
</head>
<body>
<?php
echo encabezado("CONFIGURACI&Oacute;N DE MEDICAMENTOS", $wactualiz, "clinica");

if(isset($DEBUG) && $DEBUG == 'true')
{
    echo "  <div align='left' style='background-color:#f2f2f2;font-size:8pt;'>
                <div style='background-color:#cccccc;cursor:pointer;' onclick='verSeccionArt(\"div_debug_log\");' id='d1v_s3cc10n_10g'><span>Debug: [<span onclick='reset(\"div_debug_log\")' id='r353t_sp' onmouseout='trOut(this);' onmouseover='trOver(this);' >Reset</span>]</span></div>
                <div id='div_debug_log' style='display:none;'>&nbsp;</div><br>...
            </div>";
}
else
{ $DEBUG = false;}
?>
<div id="contenedor_programa">
    <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="<?=$wemp_pmla?>">
    <input type="hidden" id="DEBUG" name="DEBUG" value="<?=$DEBUG?>">
    <table style="width:1020px;" align="center"  border="0" cellspacing="0" cellpadding="0" >
        <tr>
            <td>
                <div id="div_dispensacion" style="background-color:#2A5DB0;text-align:left;font-weight:bold;width:100%;cursor:pointer;" onclick="verOcultarForm('tabla_contenedor_noconfigurados');">
                    <table style="width:100%;">
                        <tr>
                            <td style="width:30%;color:#ffffff;">
                                <img width="10 " height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">
                                Dispensaci&oacute;n
                            </td>
                            <td align="center" style="font-weight:bold;color:#ffffff;">ARTICULOS SIN CONFIGURAR</td>
                            <td style="width:30%;">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

        <tr>
            <td valign="top" align="center" style="background-color:#FBFBFB;">
                <table style="width:80%;" id="tabla_contenedor_noconfigurados">
                    <tr>
                        <td>
                            <input type="hidden" id="wgenerando_lista_noconf" name="wgenerando_lista_noconf" value="">
                            <div id="td_contenedor_noconfigurados" style="display:;">

                                <table id="table_contenedor_noconfigurados" align="center" class="textosSmall" style="width:100%;">
                                    <!-- <tr>
                                        <td colspan="2" class="encabezadoTabla">Buscar: <input type="text" id="wbuscar_art_noconfigurado" name="wbuscar_art_noconfigurado" value=""></td>
                                    </tr> -->
                                    <tr>
                                        <td>
                                            <div style="background-color:#CCCCCC;color:gray;font-weight:bold;text-align:justify;">
                                                En la secci&oacute;n siguiente se puede identificar los articulos que tienen pendientes algun tipo de configuraci&oacute;n,
                                                estos articulos se pueden observar mediante la opci&oacute;n de b&uacute;squeda o la opci&oacute;n de ver lista ordenada
                                                alfab&eacute;ticamente por el nombre gen&eacute;rico de cada articulo.
                                            </div>
                                            <div style="text-align:left;"> Configuraci&oacute;n pendiente.<br>
                                                Es medicamento:<span class="faltaEsMed">_</span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;Fracci&oacute;n:<span class="falta59">_</span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;Familia:<span class="falta115">_</span>
                                                &nbsp;&nbsp;&nbsp;&nbsp;CUMS:<span class="faltaCum">_</span>
                                            </div>
                                            <br>
                                            <div id="div_buscar_no_configurados">
                                                <table style="width:100%;" class="textosSmall">
                                                    <tr>
                                                        <td style="width:240px;" class="encabezadoTabla">
                                                            Buscar articulos sin configurar:
                                                            <br>
                                                            <span style="font-size:7pt;">(Se busca el nombre gen&eacute;rico o c&oacute;digo del articulo)</span>
                                                        </td>
                                                        <td class="fila2">
                                                            <input id="wbuscar_noconf" name="wbuscar_noconf" value="" size="60">
                                                            <input id="wbtn_buscar_noconf" name="wbtn_buscar_noconf" class="st_boton" type="button" value="Buscar" onclick="verListaBuscar('div_encontrado_noconf');">
                                                            <input id="wbtn_limpiar_noconf" name="wbtn_limpiar_noconf" class="st_boton" type="button" value="Limpiar" onclick="$('#div_encontrado_noconf').html('&nbsp;');$('#wbuscar_noconf').val('');">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div id="div_encontrado_noconf">&nbsp;</div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <br>
                                            <span id="spn_lista_ordenada" onclick="verOcultarForm('div_lista_pre_cargada');" class="fondo_banda1" onmouseover="trOver(this);" onmouseout="trOut(this);"style="cursor:pointer;">Ver por orden alfab&eacute;tico.</span>
                                            <table align="center" style="width:100%;" class="textosSmall" >
                                                <tr>
                                                    <td align="center" style="border: 2px solid #2A5DB0;">
                                                        <?php
                                                            $ptes = 0;
                                                            $html_tr = '';
                                                            $html_tr = consultarArticulosSinConfigurar($conex, $wemp_pmla, $wbasedato, $ptes);
                                                            $css = ($ptes>5) ? "height: 250px;overflow:scroll;" : '';
                                                        ?>
                                                        <div id="div_lista_pre_cargada" style="display:none;">
                                                            <div style="background-color:#CCCCCC;font-size:10pt;font-weight:bold;">Sin configurar: <?=$ptes?></div>
                                                            <div id="div_cont_arts_noconfig" style="text-align:left;<?=$css?>">
                                                                <?=$html_tr?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td>
                            <div id="td_contenedor_configuracion" style="display:none;">
                                <div id="div_contenedor_configuracion">
                                    <table id="table_econtenedor_configuracion" align="center" class="textosSmall" style="width:100%;">
                                        <tr>
                                            <td>
                                                <span id="spn_volver" style="cursor:pointer;" onclick="volverALista();" onmouseout='trOut(this);' onmouseover='trOver(this);' class="fondo_banda1">&laquo;Volver a lista</span>
                                                <div id="div_nom_articulo_config" class="encabezadoTabla">
                                                    <input type="hidden" id="warr_articulo" name="warr_articulo" value="">
                                                    <table style="width:100%;">
                                                        <tr>
                                                            <td style="width:20%;color:#ffffff;">Configurar</td>
                                                            <td align="justify"  style="font-weight:bold;color:#ffffff;font-size:10pt;">
                                                                Nombre generico: <span id="wnomgenerico"></span><br>
                                                                [Nombre Comercial: <span id="wnomcomercial"></span>]
                                                                <input type="hidden" id="id_59" name="id_59" value="">
                                                                <input type="hidden" id="esmedicamento_26" name="esmedicamento_26" value="off">
                                                                <input type="hidden" id="id_familia_115" name="id_familia_115" value="">
                                                                <input type="hidden" id="wcodarticulo" name="wcodarticulo" value="">
                                                                <input type="hidden" id="wpresentacion" name="wpresentacion" value="">
                                                                <input type="hidden" id="unidad_59" name="unidad_59" value="">
                                                                <input type="hidden" id="fraccion_59" name="fraccion_59" value="">
                                                                <input type="hidden" id="wgrupo26" name="wgrupo26" value="">
                                                                <input type="hidden" id="id_root_64" name="id_root_64" value="">
                                                                <input type="hidden" id="nomgen_root_64" name="nomgen_root_64" value="">
                                                            </td>
                                                            <td style="width:20%;">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                    <div style="background-color:#f6f6f6;">
                                                    <table align="center">
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artcod')?>C&oacute;digo: </td>
                                                            <td class="fila2">
                                                                <span id="wcodarticulo_view">...</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artcom')?>Nombre comercial: </td>
                                                            <td class="fila1"><span id="wnomcomercial_view">...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artuni')?>Unidad de manejo: </td>
                                                            <td class="fila2"><span id="wunidad26_view">...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artgru')?>Grupo: </td>
                                                            <td class="fila2"><span id="wnomgrupo26">...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artesm')?>Es medicamento?: </td>
                                                            <td class="fila1">
                                                                <input type="checkbox" id="wesmedicamento" name="wesmedicamento" value="on" onclick="confirmarMedicamento();">
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_tipo_articulo" style="display:none;">
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t66_tipo')?>Tipo: </td>
                                                            <td class="fila2">
                                                                <select id="wtipomedicamento66" name="wtipomedicamento66" class="textosSmall">
                                                                    <option value="M" selected="selected">(M) Medicamentos</option>
                                                                    <option value="L">(L) Liquidos</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_familia_articulo" style="display:none;">
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t115_familia')?>Familia: </td>
                                                            <td class="fila1">
                                                                <div>
                                                                    <select id="wfamilia26" name="wfamilia26" class="textosSmall" onchange="verDefinirFraccion();">
                                                                        <?php
                                                                            $cont = 0;
                                                                            echo getMaestroFamilia($conex, $wbasedato, $cont, 'options');
                                                                        ?>
                                                                    </select>
                                                                    <span class="fondo_banda1" id="new_flia_sp" style="cursor:pointer;" onclick="verOcultarForm('div_form_crear_flia');"
                                                                            onmouseout='trOut(this);' onmouseover='trOver(this);' >...</span>
                                                                </div>
                                                                <div id="div_form_crear_flia" style="display:none;border:2px solid #f2f2f2;margin: 10px;">
                                                                    &nbsp;
                                                                    <?php
                                                                        $cont = 0;
                                                                        $html = getMaestroFamilia($conex, $wbasedato, $cont);
                                                                    ?>
                                                                    <div id="div_tabla_familias" style="<?=(($cont > 5) ? 'height: 150px;overflow:scroll;': '')?>">
                                                                        <table class="textosSmall" id="tabla_familias" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                            <tr class="encabezadoTabla">
                                                                                <td>C&oacute;digo</td>
                                                                                <td>Nombre</td>
                                                                            </tr>
                                                                            <?=$html?>
                                                                        </table>
                                                                    </div>
                                                                    <table class="textosSmall" id="" border="0" cellspacing="1" cellpadding="0" align="center">
                                                                        <tr>
                                                                            <td>
                                                                                <input id="wcod_newflia" name="wcod_newflia" value="" onkeypress="return soloNumerosLetras(event);" class="textosSmall mayuscula" size="10">
                                                                            </td>
                                                                            <td>
                                                                                <input id="wnom_newflia" name="wnom_newflia" value="" onkeypress="return soloNumerosLetras(event);" class="textosSmall mayuscula" size="10">
                                                                            </td>
                                                                            <td>
                                                                                <span id="btn_new_flia_sp" style="cursor:pointer;" onclick="guardarMaestroFamilia('','');"
                                                                                    onmouseout='trOut(this);' onmouseover='trOver(this);' class="fondo_banda1">Adicionar</span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_fraccion_articulo" style="display:none;">
                                                            <td colspan="2">
                                                                <table border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                    <tr>
                                                                        <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'secc_def_fraccion')?>Definir fracci&oacute;n:</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fila1">
                                                                            <div id="div_tabla_fracciones59">
                                                                                <table class="textosSmall" id="tabla_fracciones59" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                                    <!-- <tr class="encabezadoTabla">
                                                                                        <td align="center" colspan="2">Definici&oacute;n de fracciones</td>
                                                                                    </tr> -->
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_cco')?>Centro costos</td>
                                                                                        <td class="fila2 requerido">
                                                                                            <select id="wfracciones59_defcco" name="wfracciones59_defcco" disabled="disabled" class="requerido">
                                                                                                <?php
                                                                                                    $cont = 0;
                                                                                                    echo getMaestroCentroCostosH($conex, $wbasedato, $cont, 'options');
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t26_artcod')?>Articulo</td>
                                                                                        <td class="fila2"><span id="wcodarticulo_fraccion_view">###</span> <input type="hidden" class="requerido" id="wfracciones59_defart" id="wfracciones59_defart" value=""></td>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_unidad')?>Unidad de fracci&oacute;n</td>
                                                                                        <td class="fila1 requerido">
                                                                                            <div>
                                                                                                <select id="wfracciones59_deffru" name="wfracciones59_deffru" class="textosSmall requerido">
                                                                                                    <?php
                                                                                                        $cont = 0;
                                                                                                        echo getMaestroUnidades($conex, $wbasedato, $cont, 'options');
                                                                                                    ?>
                                                                                                </select>
                                                                                                <span class="fondo_banda1" id="new_unid_sp" style="cursor:pointer;" onclick="verOcultarForm('div_form_crear_unidad');"
                                                                                                        onmouseout='trOut(this);' onmouseover='trOver(this);' >...</span>
                                                                                            </div>
                                                                                            <div id="div_form_crear_unidad" style="display:none;border:2px solid #f2f2f2;margin: 10px;">
                                                                                                &nbsp;
                                                                                                <?php
                                                                                                    $cont = 0;
                                                                                                    $html = getMaestroUnidades($conex, $wbasedato, $cont);
                                                                                                ?>
                                                                                                <div id="div_tabla_unidades" style="<?=(($cont > 5) ? 'height: 150px;overflow:scroll;': '')?>">
                                                                                                    <table class="textosSmall" id="tabla_unidades" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                                                        <tr class="encabezadoTabla">
                                                                                                            <td>C&oacute;digo</td>
                                                                                                            <td>Nombre</td>
                                                                                                        </tr>
                                                                                                        <?=$html?>
                                                                                                    </table>
                                                                                                </div>
                                                                                                <table class="textosSmall" id="" border="0" cellspacing="1" cellpadding="0" align="center">
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <input id="wcod_newunidad" name="wcod_newunidad" value="" onkeypress="return soloNumerosLetras(event);" class="textosSmall mayuscula" size="10">
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <input id="wnom_newunidad" name="wnom_newunidad" value="" onkeypress="return soloCaracteresPermitidos(event);" class="textosSmall mayuscula" size="10">
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <span id="btn_new_unid_sp" style="cursor:pointer;" onclick="guardarMaestroUnidades('','');"
                                                                                                                onmouseout='trOut(this);' onmouseover='trOver(this);' class="fondo_banda1">Adicionar</span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_fraccion')?>Fracci&oacute;n</td>
                                                                                        <td class="fila2 requerido"><input class="textosSmall requerido" type="text" id="wfracciones59_deffra" name="wfracciones59_deffra" value="" onkeypress="return soloNumerosDecimales(event);"></td>
                                                                                    </tr>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_estado')?>Estado registro</td>
                                                                                        <td class="fila1"><input type="checkbox" id="wfracciones59_defest" name="wfracciones59_defest" checked="checked" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_vence')?>¿Tiene vencimiento?</td>
                                                                                        <td class="fila2"><input type="checkbox" id="wfracciones59_defven" name="wfracciones59_defven" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_dias_estable')?>D&iacute;as de estabilidad</td>
                                                                                        <td class="fila1 requerido"><input type="text" class="" id="wfracciones59_defdie" name="wfracciones59_defdie" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_dispensable')?>Dispensable</td>
                                                                                        <td class="fila2"><input type="checkbox" id="wfracciones59_defdis" name="wfracciones59_defdis" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_duplica')?>Duplicable</td>
                                                                                        <td class="fila1"><input type="checkbox" id="wfracciones59_defdup" name="wfracciones59_defdup" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_cofirma')?>Confirmable</td>
                                                                                        <td class="fila2"><input type="checkbox" id="wfracciones59_defcon" name="wfracciones59_defcon" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_nokardex')?>No kardex</td>
                                                                                        <td class="fila1"><input type="checkbox" id="wfracciones59_defnka" name="wfracciones59_defnka" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_diastrat')?>D&iacute;as de tratamiento</td>
                                                                                        <td class="fila2 requerido"><input type="text" class="" id="wfracciones59_defdim" name="wfracciones59_defdim" conchange="validarExcluyente('wfracciones59_defdim','wfracciones59_defdom');" onkeypress="validarExcluyente('wfracciones59_defdim','wfracciones59_defdom'); return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_dosismax')?>D&oacute;sis m&aacute;xima</td>
                                                                                        <td class="fila1 requerido">
                                                                                            <div ><input type="text" class="" id="wfracciones59_defdom" name="wfracciones59_defdom" conchange="validarExcluyente('wfracciones59_defdom','wfracciones59_defdim');" onkeypress="validarExcluyente('wfracciones59_defdom','wfracciones59_defdim'); return soloNumeros(event);" value=""></div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_viasadmin')?>V&iacute;as de administraci&oacute;n</td>
                                                                                        <td class="fila2">
                                                                                            <select id="wfracciones59_defvia" name="wfracciones59_defvia" class="requerido" multiple style="width:313px;height:120px;">
                                                                                                <?php
                                                                                                    $cont = 0;
                                                                                                    echo getMaestroViasAdmin($conex, $wbasedato, $cont, 'options');
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_noipod')?>NO se aplica con IPOD</td>
                                                                                        <td class="fila1"><input type="checkbox" id="wfracciones59_defipo" name="wfracciones59_defipo" value="off" onclick="activarCamposIPOD();"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_reqcant')?>Requiere cantidad</td>
                                                                                        <td class="fila2"><input disabled="disabled" type="checkbox" class="requeridoIPOD" id="wfracciones59_defrci" name="wfracciones59_defrci" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_caninfer')?>Cantidad inferior</td>
                                                                                        <td class="fila1"><input disabled="disabled" type="text" class="requeridoIPOD" id="wfracciones59_defcai" name="wfracciones59_defcai" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_cansuper')?>Cantidad superior</td>
                                                                                        <td class="fila2"><input disabled="disabled" type="text" class="requeridoIPOD" id="wfracciones59_defcas" name="wfracciones59_defcas" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_escala')?>Escala</td>
                                                                                        <td class="fila1"><input disabled="disabled" type="text" class="requeridoIPOD" id="wfracciones59_defesc" name="wfracciones59_defesc" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_cant_porcco')?>Cantidad variable por Centro Costo</td>
                                                                                        <td class="fila2">
                                                                                            <select disabled="disabled" class="requeridoIPOD" id="wfracciones59_defavc" name="wfracciones59_defavc" multiple style="height:120px;">
                                                                                                <option value="*">[TODOS]</option>
                                                                                                <?php
                                                                                                    $cont = 0;
                                                                                                    echo getMaestroCentroCostosH($conex, $wbasedato, $cont, 'multiple');
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_cant_framin')?>Cantidad de fracci&oacute;n m&iacute;nima</td>
                                                                                        <td class="fila1"><input type="text" class="" id="wfracciones59_defmin" name="wfracciones59_defmin" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_cant_framax')?>Cantidad de fracci&oacute;n m&aacute;xima</td>
                                                                                        <td class="fila2"><input type="text" class="" id="wfracciones59_defmax" name="wfracciones59_defmax" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td colspan="2" align="center" class="fondo_banda1" style="font-weight:bold;">
                                                                                            <input class="st_boton" type="button" name="wbtnfracciones_guardar" id="wbtnfracciones_guardar" onclick="guardarDefinicionFraccion();" value="Guardar Definici&oacute;n">
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_cums" style="display:none;">
                                                            <td colspan="2">
                                                                <table border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                    <tr>
                                                                        <td class="encabezadoTabla">Informaci&oacute;n CUMS:</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fila2">
                                                                            <div id="div_tabla_cums">
                                                                                <table align="center" class="textosSmall" id="tabla_cums" border="0" cellspacing="1" cellpadding="0" style="width:60%;">
                                                                                    <tr class="fila1">
                                                                                        <td style="font-weight:bold;" class="fondo_banda1">
                                                                                            C&oacute;digo CUM:
                                                                                            <br>
                                                                                            <span style="font-size:7pt;color:gray;font-weight:bold">
                                                                                                <?=MSJ1_CODIGO_CUM?>
                                                                                            </span>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" id="wcum64codigo" id="wcum64codigo" value="">
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr class="fila1">
                                                                                        <td style="font-weight:bold;" class="fondo_banda1">Decripci&oacute;n:</td>
                                                                                        <td>
                                                                                            <span id="wcum64descripcion" style="font-weight:bold;">nombre generico</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr class="fila1">
                                                                                        <td style="font-weight:bold;" class="fondo_banda1">
                                                                                            Factor de conversion:
                                                                                            <br>
                                                                                            <span style="font-size:7pt;color:gray;font-weight:bold">
                                                                                                <?=MSJ1_CONVERSION_CUM?>
                                                                                            </span>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" id="wcum64equivale" id="wcum64equivale" value="" onkeypress="return soloNumerosDecimales(event);">
                                                                                            <br><span style="font-size:7pt;color:red;font-weight:bold">*</span>
                                                                                            <span style="font-size:7pt;color:gray;font-weight:bold"><?=MSJ2_CONVERSION_CUM?></span>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_btn_configurar_articulo" style="display:;">
                                                            <td colspan="2" align="center" class="fondo_banda1" style="font-weight:bold;">
                                                                <input class="st_boton" type="button" name="wbtn_configurar_articulo" id="wbtn_configurar_articulo" onclick="configurarArticulo();" value="Configurar articulo">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>




        <tr>
            <td>
                <br>
                <br>
                <div style="background-color:#2A5DB0;text-align:center;font-weight:bold;color:#ffffff;width:100%;cursor:pointer;" onclick="verOcultarForm('tabla_contenedor_lista_editable');">
                    <table style="width:100%;">
                        <tr>
                            <td style="width:30%;color:#ffffff;">
                                <img width="10 " height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;
                            </td>
                            <td align="center" style="font-weight:bold;color:#ffffff;">LISTA DE MEDICAMENTOS CONFIGURADOS</td>
                            <td style="width:30%;">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>




        <tr>
            <td valign="top" align="center" style="background-color:#FBFBFB;">
                <table style="width:80%;display:none;" id="tabla_contenedor_lista_editable" >
                    <tr>
                        <td valign="top">
                            <!-- <input type="hidden" id="arr_alfabetico" name="arr_alfabetico" value="<?=$arr_alfabetico?>"> -->
                            <input type="hidden" id="wgenerando_lista" name="wgenerando_lista" value="">
                            <div id="td_contenedor_lista_editable" style="display:;">

                                <table id="table_contenedor_lista_editable" align="center" class="textosSmall" style="width:100%;">
                                    <!-- <tr>
                                        <td colspan="2" class="encabezadoTabla">Buscar: <input type="text" id="wbuscar_art_editable" name="wbuscar_art_editable" value=""></td>
                                    </tr> -->
                                    <tr>
                                        <td>
                                            <div style="background-color:#CCCCCC;color:gray;font-weight:bold;">
                                                En esta secci&oacute;n se pueden buscar medicamentos para editarlos,
                                                estos medicamentos se pueden observar mediante una lista ordenada alfab&eacute;ticamente por el nombre gen&eacute;rico de cada articulo.
                                            </div>
                                            <br>
                                            <div id="div_buscar_configurados">
                                                <table style="width:100%;" class="textosSmall">
                                                    <tr>
                                                        <td style="width:240px;" class="encabezadoTabla">
                                                            Buscar medicamentos:
                                                            <br>
                                                            <span style="font-size:7pt;">(Se busca el nombre gen&eacute;rico o c&oacute;digo del medicamento)</span>
                                                        </td>
                                                        <td class="fila2">
                                                            <input id="wbuscar_config" name="wbuscar_config" value="" size="60">
                                                            <input id="wbtn_buscar_config" name="wbtn_buscar_config" class="st_boton" type="button" value="Buscar" onclick="verListaBuscarEditable('div_encontrado_config');">
                                                            <input id="wbtn_limpiar_noconf" name="wbtn_limpiar_noconf" class="st_boton" type="button" value="Limpiar" onclick="$('#div_encontrado_config').html('&nbsp;');$('#wbuscar_config').val('');">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div id="div_encontrado_config">&nbsp;</div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <br>
                                            <span id="spn_lista_ordenada_conf" onclick="verOcultarForm('div_lista_pre_cargada_medic');" class="fondo_banda1" onmouseover="trOver(this);" onmouseout="trOut(this);"style="cursor:pointer;">Medicamentos por orden alfab&eacute;tico.</span>
                                            <table align="center" style="width:100%;" class="textosSmall" >
                                                <tr>
                                                    <td align="center" style="border: 2px solid #2A5DB0;">
                                                        <?php
                                                            $ptes = 0;
                                                            $html_tr = '';
                                                            $html_tr = crearListaAfabetica($conex, $wemp_pmla, $wbasedato, $ptes);
                                                            // $css = ($ptes>5) ? "height: 250px;overflow:scroll;" : '';
                                                            //$css = "height: 280px;overflow:scroll;";
                                                            $css = ($ptes>5) ? "height: 250px;overflow:scroll;" : '';
                                                        ?>
                                                        <div id="div_lista_pre_cargada_medic" style="display:none;">
                                                            <div style="background-color:#CCCCCC;font-size:10pt;font-weight:bold;">Medicamentos configurados: <?=$ptes?></div>
                                                            <div id="div_cont_arts_editable" style="text-align:left;<?=$css?>">
                                                                <?=$html_tr?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td valign="top">
                            <div id="td_contenedor_editable" style="display:none;">
                                <div id="div_contenedor_editable">
                                    <table id="table_contenedor_editable" align="center" class="textosSmall" style="width:100%;">
                                        <tr>
                                            <td>
                                                <span id="spn_volver_ed" style="cursor:pointer;" onclick="volverAListaEditable();" onmouseout='trOut(this);' onmouseover='trOver(this);' class="fondo_banda1">&laquo;Volver a lista</span>
                                                <div id="div_nom_articulo_editable" class="encabezadoTabla">
                                                    <input type="hidden" id="warr_articulo_editable" name="warr_articulo_editable" value="">
                                                    <table style="width:100%;">
                                                        <tr>
                                                            <td style="width:20%;color:#ffffff;">Editar</td>
                                                            <td align="justify"  style="font-weight:bold;color:#ffffff;font-size:10pt;">
                                                                Nombre generico: <span id="wnomgenerico_edit"></span><br>
                                                                [Nombre Comercial: <span id="wnomcomercial_edit"></span>]
                                                                <input type="hidden" id="wcodarticulo_edit" name="wcodarticulo_edit" value="">
                                                                <input type="hidden" id="id_66_esmedicamento_edit" name="id_66_esmedicamento_edit" value="">
                                                                <input type="hidden" id="id_59_edit" name="id_59_edit" value="">
                                                                <input type="hidden" id="id_familia_115_edit" name="id_familia_115_edit" value="">
                                                                <input type="hidden" id="id_68_edit" name="id_68_edit" value="">
                                                                <input type="hidden" id="wpresentacion_edit" name="wpresentacion_edit" value="">
                                                                <input type="hidden" id="id_root_64_edit" name="id_root_64_edit" value="">
                                                                <input type="hidden" id="nomgen_root_64_edit" name="nomgen_root_64_edit" value="">
                                                            </td>
                                                            <td style="width:20%;">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                    <div style="background-color:#f6f6f6;">
                                                    <table align="center">
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artcod')?>C&oacute;digo: </td>
                                                            <td class="fila2">
                                                                <span id="wcodarticulo_view_edit">...</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artcom')?>Nombre comercial: </td>
                                                            <td class="fila1"><span id="wnomcomercial_view_edit">...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artuni')?>Unidad de manejo: </td>
                                                            <td class="fila2"><span id="wunidad26_view_edit">...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artest')?>Activa/Inactiva Medicamento: </td>
                                                            <td class="fila1">
                                                                <input type="checkbox" id="westado_medicamento_edit" name="westado_medicamento_edit" value="on">
                                                                <div id="div_estado_edit" style="display:inline;"></div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artgru')?>Grupo: </td>
                                                            <td class="fila2"><span id="wnomgrupo26_edit">...</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t26_artesm')?>Es medicamento?: </td>
                                                            <td class="fila1">
                                                                <input type="checkbox" id="wesmedicamento_edit" name="wesmedicamento_edit" value="on" onclick="confirmarMedicamentoEditable();">
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_tipo_articulo_editable" style="display:none;">
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t66_tipo')?>Tipo: </td>
                                                            <td class="fila2">
                                                                <!-- El tipo de medicamento se desactiva para no dejarlo editar porque si se cambia el tipo para un medicamento
                                                                se cambia también para todos los demas medicamentos que tienen el mismo grupo -->
                                                                <select id="wtipomedicamento66_edit" name="wtipomedicamento66_edit" class="textosSmall" disabled="disabled">
                                                                    <option value="M" selected="selected">(M) Medicamentos</option>
                                                                    <option value="L">(L) Liquidos</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_familia_articulo_editable" style="display:none;">
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t115_familia')?>Familia: </td>
                                                            <td class="fila1">
                                                                <div>
                                                                    <select id="wfamilia26_edit" name="wfamilia26_edit" class="textosSmall" onchange="verDefinirFraccionEditable();">
                                                                        <?php
                                                                            $cont = 0;
                                                                            echo getMaestroFamilia($conex, $wbasedato, $cont, 'options');
                                                                        ?>
                                                                    </select>
                                                                    <span class="fondo_banda1" id="new_flia_sp_edit" style="cursor:pointer;" onclick="verOcultarForm('div_form_crear_flia_editable');"
                                                                            onmouseout='trOut(this);' onmouseover='trOver(this);' >...</span>
                                                                </div>
                                                                <div id="div_form_crear_flia_editable" style="display:none;border:2px solid #f2f2f2;margin: 10px;">
                                                                    &nbsp;
                                                                    <?php
                                                                        $cont = 0;
                                                                        $html = getMaestroFamilia($conex, $wbasedato, $cont, 'html','_edit');
                                                                    ?>
                                                                    <div id="div_tabla_familias_editable" style="<?=(($cont > 5) ? 'height: 150px;overflow:scroll;': '')?>">
                                                                        <table class="textosSmall" id="tabla_familias_editable" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                            <tr class="encabezadoTabla">
                                                                                <td>C&oacute;digo</td>
                                                                                <td>Nombre</td>
                                                                            </tr>
                                                                            <?=$html?>
                                                                        </table>
                                                                    </div>
                                                                    <table class="textosSmall" id="" border="0" cellspacing="1" cellpadding="0" align="center">
                                                                        <tr>
                                                                            <td>
                                                                                <input id="wcod_newflia_edit" name="wcod_newflia_edit" value="" onkeypress="return soloNumerosLetras(event);" class="textosSmall mayuscula" size="10">
                                                                            </td>
                                                                            <td>
                                                                                <input id="wnom_newflia_edit" name="wnom_newflia_edit" value="" onkeypress="return soloNumerosLetras(event);" class="textosSmall mayuscula" size="10">
                                                                            </td>
                                                                            <td>
                                                                                <span id="btn_new_flia_sp_edit" style="cursor:pointer;" onclick="guardarMaestroFamilia('_edit','_editable');"
                                                                                    onmouseout='trOut(this);' onmouseover='trOver(this);' class="fondo_banda1">Adicionar</span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_fraccion_articulo_editable" style="display:none;">
                                                            <td colspan="2">
                                                                <table border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                    <tr>
                                                                        <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'secc_def_fraccion')?>Definir fracci&oacute;n:</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fila1">
                                                                            <div id="div_tabla_fracciones59_editable">
                                                                                <table class="textosSmall" id="tabla_fracciones59_editable" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                                    <!-- <tr class="encabezadoTabla">
                                                                                        <td align="center" colspan="2">Definici&oacute;n de fracciones</td>
                                                                                    </tr> -->
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_cco')?>Centro costos</td>
                                                                                        <td class="fila2 requerido">
                                                                                            <select id="wfracciones59_defcco_edit" name="wfracciones59_defcco_edit" disabled="disabled" class="requerido">
                                                                                                <?php
                                                                                                    $cont = 0;
                                                                                                    echo getMaestroCentroCostosH($conex, $wbasedato, $cont, 'options');
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t26_artcod')?>Articulo</td>
                                                                                        <td class="fila2"><span id="wcodarticulo_fraccion_view_edit">###</span> <input type="hidden" class="requerido" id="wfracciones59_defart_edit" id="wfracciones59_defart_edit" value=""></td>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_unidad')?>Unidad de fracci&oacute;n</td>
                                                                                        <td class="fila1 requerido">
                                                                                            <div>
                                                                                                <select id="wfracciones59_deffru_edit" name="wfracciones59_deffru_edit" class="textosSmall requerido">
                                                                                                    <?php
                                                                                                        $cont = 0;
                                                                                                        echo getMaestroUnidades($conex, $wbasedato, $cont, 'options');
                                                                                                    ?>
                                                                                                </select>
                                                                                                <span class="fondo_banda1" id="new_unid_sp_edit" style="cursor:pointer;" onclick="verOcultarForm('div_form_crear_unidad_editable');"
                                                                                                        onmouseout='trOut(this);' onmouseover='trOver(this);' >...</span>
                                                                                            </div>
                                                                                            <div id="div_form_crear_unidad_editable" style="display:none;border:2px solid #f2f2f2;margin: 10px;">
                                                                                                &nbsp;
                                                                                                <?php
                                                                                                    $cont = 0;
                                                                                                    $html = getMaestroUnidades($conex, $wbasedato, $cont, 'html', '_edit');
                                                                                                ?>
                                                                                                <div id="div_tabla_unidades_editable" style="<?=(($cont > 5) ? 'height: 150px;overflow:scroll;': '')?>">
                                                                                                    <table class="textosSmall" id="tabla_unidades_editable" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                                                        <tr class="encabezadoTabla">
                                                                                                            <td>C&oacute;digo</td>
                                                                                                            <td>Nombre</td>
                                                                                                        </tr>
                                                                                                        <?=$html?>
                                                                                                    </table>
                                                                                                </div>
                                                                                                <table class="textosSmall" id="" border="0" cellspacing="1" cellpadding="0" align="center">
                                                                                                    <tr>
                                                                                                        <td>
                                                                                                            <input id="wcod_newunidad_edit" name="wcod_newunidad_edit" value="" onkeypress="return soloNumerosLetras(event);" class="textosSmall mayuscula" size="10">
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <input id="wnom_newunidad_edit" name="wnom_newunidad_edit" value="" onkeypress="return soloCaracteresPermitidos(event);" class="textosSmall mayuscula" size="10">
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <span id="btn_new_unid_sp_edit" style="cursor:pointer;" onclick="guardarMaestroUnidades('_edit','_editable');"
                                                                                                                onmouseout='trOut(this);' onmouseover='trOver(this);' class="fondo_banda1">Adicionar</span>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_fraccion')?>Fracci&oacute;n</td>
                                                                                        <td class="fila2 requerido"><input class="textosSmall requerido" type="text" id="wfracciones59_deffra_edit" name="wfracciones59_deffra_edit" value="" onkeypress="return soloNumerosDecimales(event);"></td>
                                                                                    </tr>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_estado')?>Estado registro</td>
                                                                                        <td class="fila1"><input type="checkbox" id="wfracciones59_defest_edit" name="wfracciones59_defest_edit" checked="checked" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_vence')?>¿Tiene vencimiento?</td>
                                                                                        <td class="fila2"><input type="checkbox" id="wfracciones59_defven_edit" name="wfracciones59_defven_edit" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_dias_estable')?>D&iacute;as de estabilidad</td>
                                                                                        <td class="fila1 requerido"><input type="text" class="" id="wfracciones59_defdie_edit" name="wfracciones59_defdie_edit" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_dispensable')?>Dispensable</td>
                                                                                        <td class="fila2"><input type="checkbox" id="wfracciones59_defdis_edit" name="wfracciones59_defdis_edit" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_duplica')?>Duplicable</td>
                                                                                        <td class="fila1"><input type="checkbox" id="wfracciones59_defdup_edit" name="wfracciones59_defdup_edit" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_cofirma')?>Confirmable</td>
                                                                                        <td class="fila2"><input type="checkbox" id="wfracciones59_defcon_edit" name="wfracciones59_defcon_edit" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_nokardex')?>No kardex</td>
                                                                                        <td class="fila1"><input type="checkbox" id="wfracciones59_defnka_edit" name="wfracciones59_defnka_edit" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_diastrat')?>D&iacute;as de tratamiento</td>
                                                                                        <td class="fila2 requerido"><input type="text" class="" id="wfracciones59_defdim_edit" name="wfracciones59_defdim_edit" conchange="validarExcluyente('wfracciones59_defdim_edit','wfracciones59_defdom_edit');" onkeypress="validarExcluyente('wfracciones59_defdim_edit','wfracciones59_defdom_edit'); return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_dosismax')?>D&oacute;sis m&aacute;xima</td>
                                                                                        <td class="fila1 requerido">
                                                                                            <div ><input type="text" class="" id="wfracciones59_defdom_edit" name="wfracciones59_defdom_edit" conchange="validarExcluyente('wfracciones59_defdom_edit','wfracciones59_defdim_edit');" onkeypress="validarExcluyente('wfracciones59_defdom_edit','wfracciones59_defdim_edit'); return soloNumeros(event);" value=""></div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_viasadmin')?>V&iacute;as de administraci&oacute;n</td>
                                                                                        <td class="fila2">
                                                                                            <select id="wfracciones59_defvia_edit" name="wfracciones59_defvia_edit" class="requerido" multiple="multiple" style="width:313px;height:120px;">
                                                                                                <?php
                                                                                                    $cont = 0;
                                                                                                    echo getMaestroViasAdmin($conex, $wbasedato, $cont, 'options');
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_noipod')?>NO se aplica con IPOD</td>
                                                                                        <td class="fila1"><input type="checkbox" id="wfracciones59_defipo_edit" name="wfracciones59_defipo_edit" value="off" onclick="activarCamposIPODEditable();"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_reqcant')?>Requiere cantidad</td>
                                                                                        <td class="fila2"><input disabled="disabled" type="checkbox" class="requeridoIPOD" id="wfracciones59_defrci_edit" name="wfracciones59_defrci_edit" value="on"></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_caninfer')?>Cantidad inferior</td>
                                                                                        <td class="fila1"><input disabled="disabled" type="text" class="requeridoIPOD" id="wfracciones59_defcai_edit" name="wfracciones59_defcai_edit" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_cansuper')?>Cantidad superior</td>
                                                                                        <td class="fila2"><input disabled="disabled" type="text" class="requeridoIPOD" id="wfracciones59_defcas_edit" name="wfracciones59_defcas_edit" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_escala')?>Escala</td>
                                                                                        <td class="fila1"><input disabled="disabled" type="text" class="requeridoIPOD" id="wfracciones59_defesc_edit" name="wfracciones59_defesc_edit" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;background-color:#b7b7b7;"><?=printHelp($arr_ayuda,'t59_cant_porcco')?>Cantidad variable por Centro Costo</td>
                                                                                        <td class="fila2">
                                                                                            <select disabled="disabled" class="requeridoIPOD" id="wfracciones59_defavc_edit" name="wfracciones59_defavc_edit" multiple style="height:120px;">
                                                                                                <option value="*">[TODOS]</option>
                                                                                                <?php
                                                                                                    $cont = 0;
                                                                                                    echo getMaestroCentroCostosH($conex, $wbasedato, $cont, 'multiple');
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_cant_framin')?>Cantidad de fracci&oacute;n  m&iacute;nima</td>
                                                                                        <td class="fila1"><input type="text" class="" id="wfracciones59_defmin_edit" name="wfracciones59_defmin_edit" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td class="fondo_banda1" style="font-weight:bold;"><?=printHelp($arr_ayuda,'t59_cant_framax')?>Cantidad de fracci&oacute;n  m&aacute;xima</td>
                                                                                        <td class="fila2"><input type="text" class="" id="wfracciones59_defmax_edit" name="wfracciones59_defmax_edit" onkeypress="return soloNumeros(event);" value=""></td>
                                                                                    </tr>
                                                                                    <!-- <tr>
                                                                                        <td colspan="2" align="center" class="fondo_banda1" style="font-weight:bold;">
                                                                                            <input class="st_boton" type="button" name="wbtnfracciones_guardar" id="wbtnfracciones_guardar" onclick="guardarDefinicionFraccion();" value="Guardar Definición">
                                                                                        </td>
                                                                                    </tr> -->
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_stock_articulo_editable" style="display:;">
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t91_destock')?>Medicamento es del STOCK: </td>
                                                            <td class="fila1">
                                                                <div>
                                                                    <input type="checkbox" id="wesdestock91_edit" name="wesdestock91_edit" onclick="verFormularioStockEditable('div_form_crear_stock_editable');">
                                                                    (Seleccionar si es de stock)
                                                                </div>
                                                                <div id="div_form_crear_stock_editable" style="display:none;border:2px solid #f2f2f2;margin: 10px;text-align:center;">
                                                                    &nbsp;
                                                                    <input type="hidden" id="wtipostock91_edit" id="wtipostock91_edit" value="N">
                                                                    <select class="requeridoSTOCK" id="wcentrocostos91_edit" name="wcentrocostos91_edit" multiple style="height:120px;">
                                                                        <?php
                                                                            $cont = 0;
                                                                            echo getMaestroCentroCostosH($conex, $wbasedato, $cont, 'multiple');
                                                                        ?>
                                                                    </select>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_especial_editable" style="display:;">
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'t08_medic_especial')?>Medicamento es especial al dispensar: </td>
                                                            <td class="fila2">
                                                                <div>
                                                                    <input type="checkbox" id="wespecial08_edit" name="wespecial08_edit" onclick="verFormularioEspecialEditable('tr_definir_articulo_especial_editable');">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_articulo_especial_editable" style="display:none;">
                                                            <td colspan="2">
                                                                <table border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                    <tr>
                                                                        <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'secc_especial')?>Definici&oacute;n de medicamento especial:</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fila1">
                                                                            <div id="div_tabla_especiales08_editable">
                                                                                <table class="textosSmall" id="tabla_addespeciales08_editable" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                                    <tr class="encabezadoTabla">
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'t08_cco')?>Centro costo</td>
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'t08_cantvaria')?>Cant. variable</td>
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'t08_cantdefecto')?>Cant. por defecto</td>
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'t08_cantmax')?>Cant. m&aacute;xima</td>
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'t08_negativos')?>Permite negativos</td>
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'t08_aplicaauto')?>Aplica automatica/.</td>
                                                                                        <td align="center">[+]</td>
                                                                                    </tr>
                                                                                    <tr class="fila2" id="tr_adicionar_especial">
                                                                                        <td align="center">
                                                                                            <input type="hidden" id="wespecial08_id_add" id="wespecial08_id_add" value="">
                                                                                            <select id="wespecial08_arecco_add" name="wespecial08_arecco_add" class="">
                                                                                           <?php
                                                                                                $cont = 0;
                                                                                                echo getMaestroCentroCostosH($conex, $wbasedato, $cont, 'options');
                                                                                            ?>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <input type="checkbox" id="wespecial08_arecva_add" id="wespecial08_arecva_add" value="on">
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <input type="text" id="wespecial08_arecde_add" id="wespecial08_arecde_add" value="" size="10">
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <input type="text" id="wespecial08_arecma_add" id="wespecial08_arecma_add" value="" size="10">
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <input type="checkbox" id="wespecial08_areneg_add" id="wespecial08_areneg_add" value="on">
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <input type="checkbox" id="wespecial08_areapl_add" id="wespecial08_areapl_add" value="on">
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <span id="adiconar_especiales" onmouseover="trOver(this);" onmouseout="trOut(this);" class="fondo_banda1" style="cursor:pointer;" onclick="adicionarCCOEspeciales();">Adicionar</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <br>
                                                                                <table align="center" class="" id="tabla_especiales08_editable" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                                    <tr class="encabezadoTabla textosSmall">
                                                                                        <td align="center">Centro costo</td>
                                                                                        <td align="center">Cant. variable</td>
                                                                                        <td align="center">Cant. por defecto</td>
                                                                                        <td align="center">Cnt. m&aacute;xima</td>
                                                                                        <td align="center">Permite negativos</td>
                                                                                        <td align="center">Aplica automatica/.</td>
                                                                                        <td align="center">&nbsp;</td>
                                                                                        <td align="center">&nbsp;</td>
                                                                                    </tr>
                                                                                </table>
                                                                                <br>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_equivalente_editable" style="display:;">
                                                            <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'tequ_medic_equivalente')?>Medicamento requiere asociarle otros art&iacute;culos: </td>
                                                            <td class="fila1">
                                                                <div>
                                                                    <input type="checkbox" id="wequivalente_edit" name="wequivalente_edit" onclick="verFormularioEquivalenteEditable('tr_definir_articulo_equivalente_editable');">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_articulo_equivalente_editable" style="display:none;">
                                                            <td colspan="2">
                                                                <table border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                    <tr>
                                                                        <td class="encabezadoTabla"><?=printHelp($arr_ayuda,'secc_equivalente')?>Medicamentos asociados:</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fila1">
                                                                            <div id="div_tabla_equivalente_editable">
                                                                                <table class="textosSmall" id="tabla_addequivalente_editable" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                                    <tr class="encabezadoTabla">
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'tequ_art')?>Art&iacute;culo</td>
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'tequ_cant')?>Cantidad</td>
                                                                                        <td align="center"><?=printHelp($arr_ayuda,'tequ_est')?>Estado</td>
                                                                                        <td align="center">[+]</td>
                                                                                    </tr>
                                                                                    <tr class="fila2">
                                                                                        <td align="center">
                                                                                            <table border="0" cellspacing="1" cellpadding="0">
                                                                                                <tr>
                                                                                                    <td><?=printHelp($arr_ayuda,'tequ_art_b')?>Buscar&nbsp;</td>
                                                                                                    <td>
                                                                                                        <div id='ccsel' style="display:block;"><img title='Seleccione un centro de costos' width='14 ' height='14' border='0' src='../../images/medical/HCE/lupa.PNG' /></div>
                                                                                                        <div id='ccload' style='display:none;' ><img width='14 ' height='14' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
                                                                                                    </td>
                                                                                                    <td>
                                                                                                        <input size="20" value="" name="wbuscar_equivalente" id="wbuscar_equivalente" onkeypress='return enterBuscar("wequivalente_acpart_add",event);'>
                                                                                                        <input type="button" onclick="buscarArticuloEquivalente('wequivalente_acpart_add');" value="Buscar" class="st_boton" name="wbtn_buscar_equivalente_config" id="wbtn_buscar_equivalente_config">
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </table>
                                                                                        </td>
                                                                                        <td>&nbsp;</td>
                                                                                        <td>&nbsp;</td>
                                                                                        <td>&nbsp;</td>
                                                                                    </tr>
                                                                                    <tr class="fila2" id="tr_adicionar_equivalente">
                                                                                        <td align="center">
                                                                                            <input type="hidden" id="wequivalente_id_add" id="wequivalente_id_add" value="">
                                                                                            <select id="wequivalente_acpart_add" name="wequivalente_acpart_add" class="" onchange="validarArticuloEquivalente(this);">
                                                                                               <?php
                                                                                                    $cont = 0;
                                                                                                    echo getMaestroArticulos26($conex, $wemp_pmla, $wbasedato, $cont, 'options');
                                                                                                ?>
                                                                                            </select>
                                                                                            <!-- <div id="div_encontrado_equivalente_config">
                                                                                            </div> -->
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <input type="text" id="wequivalente_acpcan_add" id="wequivalente_acpcan_add" value="" size="10" onkeypress="return soloNumeros(event);">
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <input type="checkbox" id="wequivalente_acpest_add" id="wequivalente_acpest_add" value="on">
                                                                                        </td>
                                                                                        <td align="center">
                                                                                            <span id="adiconar_equivalente" onmouseover="trOver(this);" onmouseout="trOut(this);" class="fondo_banda1" style="cursor:pointer;" onclick="adicionarEquivalente();">Adicionar</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                                <br>
                                                                                <table align="center" class="" id="tabla_equivalente_editable" border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                                    <tr class="encabezadoTabla textosSmall">
                                                                                        <td align="center">Art&iacute;culo</td>
                                                                                        <td align="center">Cantidad</td>
                                                                                        <td align="center">Estado</td>
                                                                                        <td align="center">&nbsp;</td>
                                                                                        <td align="center">&nbsp;</td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_definir_cums_editable" style="display:;">
                                                            <td colspan="2">
                                                                <table border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                                                                    <tr>
                                                                        <td class="encabezadoTabla">Informaci&oacute;n CUMS:</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="fila2">
                                                                            <div id="div_tabla_cums_editable">
                                                                                <table align="center" class="textosSmall" id="tabla_cums" border="0" cellspacing="1" cellpadding="0" style="width:60%;">
                                                                                    <tr class="fila1">
                                                                                        <td style="font-weight:bold;" class="fondo_banda1">
                                                                                            C&oacute;digo CUM:
                                                                                            <br>
                                                                                            <span style="font-size:7pt;color:gray;font-weight:bold">
                                                                                                <?=MSJ1_CODIGO_CUM?>
                                                                                            </span>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" id="wcum64codigo_edit" id="wcum64codigo_edit" value="">
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr class="fila1">
                                                                                        <td style="font-weight:bold;" class="fondo_banda1">Decripci&oacute;n:</td>
                                                                                        <td>
                                                                                            <span id="wcum64descripcion_edit" style="font-weight:bold;">nombre generico</span>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr class="fila1">
                                                                                        <td style="font-weight:bold;" class="fondo_banda1">
                                                                                            Factor de conversion:
                                                                                            <br>
                                                                                            <span style="font-size:7pt;color:gray;font-weight:bold">
                                                                                                <?=MSJ1_CONVERSION_CUM?>
                                                                                            </span>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" id="wcum64equivale_edit" id="wcum64equivale_edit" value="" onkeypress="return soloNumerosDecimales(event);">
                                                                                            <br><span style="font-size:7pt;color:red;font-weight:bold">*</span>
                                                                                            <span style="font-size:7pt;color:gray;font-weight:bold"><?=MSJ2_CONVERSION_CUM?></span>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr id="tr_btn_editar_articulo" style="display:none;">
                                                            <td colspan="2" align="center" class="fondo_banda1" style="font-weight:bold;">
                                                                <input class="st_boton" type="button" name="wbtn_editar_articulo" id="wbtn_editar_articulo" onclick="editarArticulo();" value="Editar articulo">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <br>
    <table align="center">
    <tr>
        <td align="center" ><input type="button" onclick="cerrarVentana();" value="Cerrar Ventana"></td>
    </tr>
    </table>
</div>
</body>
</html>