<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : actualizacion_cups.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 14 Septiembre de 2017

 DESCRIPCION: Programa para actualizar cups en unix y matrix, reemplazar por nuevo cup.

 Notas:
 --
*/ $wactualiza = "(Julio 10 de 2019)"; /*
 ACTUALIZACIONES:
 - Julio 10 2019: Jerson Trujillo:
	En el proceso de reemplazar cups, se agregan las tablas de matrix para que tambien busque el cups y haga el reemplazo.
	cliame_000114, cliame_000155 cliame_000156 cliame_000157 cliame_000158 cliame_000180 cliame_000181 cliame_000182 
	cliame_000183 cliame_000186 cliame_000192 cliame_000225 cliame_000226 cliame_000242 cliame_000249 cliame_000257 
	cliame_000260
 - Febrero 26 de 2018 Edwar Jaramillo:
    * La fuente de consulta de los procedimientos se cambia del maestro cliame_000103 al maestro root_000012.
    * Nueva funcionalidad (botón) para actualizar el maestro de cups root_12 con el maestro de cups de unix incup, se insertan
        los cups que no estén y se actualizan si están direfentes en cuanto a estado, nombre.
    * Cuando se realiza el proceso de reemplazar un cups por otro nuevo y el cups nuevo ya existe en la tabla inpro o inexa se actualiza
         el nombre, grupo y uvr del cups nuevo con el cups anterior.
 - Octubre 18 de 2017 Edwar Jaramillo:
    * Nueva funcionalidad para cargar archivo plano y realizar carga masiva de cups.
    * Se realiza validación de cada par cups para verificar si existen en el maestro y el formato del archivo es correcto.
    * Eliminación de código y declaración de clases css sin uso.
    * Se crea el parámetro modo_pruebas que llega desde url para realizar simulaciones o pruebas sin hacer cambios reales en las tablas.
 - Octubre 05 de 2017 Edwar Jaramillo:
    * Nueva función para actualizar la relación empresa procedimiento y empresa examen.
    * Nueva fución para guardar un respaldo de los registros que se van a actualizar, pero en este momento está sin uso pues se debe
        modificar para no generar un archivo en unix (no funciona unload por odbc) entonces se creará una tabla para guardar el respaldo.
 - Octubre 02 de 2017 Edwar Jaramillo:
    * Primera publicación.
 - Septiembre 14 de 2017 Edwar Jaramillo:
    * Fecha de la creación del programa.

**/
$consultaAjax='';
$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");






if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la p&aacute;gina principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
elseif(!isset($accion) && !array_key_exists('user',$_SESSION))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
$user_session      = explode('-',$_SESSION['user']);
$user_session      = $user_session[1];
$maestro_cups_root = true;
/*****  DICCIONARIO LOCAL *****/
// define('PROCEDIMIENTO','Procedimiento');

/********************** INICIO DE FUNCIONES *************************/

/**
 * [seguimiento description: Función para uso solo de desarrollo, en ambiente local, crea un archivo de texto donde se imprimen variables y arrays para su seguimiento]
 * @param  [type] $seguir [Cadena de texto a guardar en el archivo, para guardar array recordar usar print_r($al_array, true), puede usar saltos de líea PHP así PHP_EOL ]
 * @return [type]         [description]
 */
function seguimiento($seguir)
{
    $fp = fopen("seguimiento.txt","a+");
    fwrite($fp, "[".date("Y-m-d H:i:s")."]".PHP_EOL.$seguir);
    fclose($fp);
}

/**
 * [limpiarString: quita multiples espacios y espacios al final del string]
 * @param  [type] $string_ [description]
 * @return [type]          [description]
 */
function limpiarString($string_)
{
    return trim(preg_replace('/[ ]+/', ' ', $string_));
}

/**
 * [conexionUnixFn: Función para validar se puede o se hizo conexión a unix, retorna un link con la conexión a unix.]
 * @param  [type] $hay_unix   [description]
 * @param  [type] &$conexUnix [description]
 * @param  [type] &$data      [description]
 * @return [type]             [description]
 */
function conexionUnixFn($hay_unix, &$conexUnix, &$data){
    $conectado_a_unix = false;
    if(!$conectado_a_unix && $hay_unix)
    {
        if($conexUnix = @odbc_connect('facturacion','informix','sco'))
        {
            $conectado_a_unix = true;
        }
        else
        {
            $data['mensaje'] = "Problemas en la conexión a Unix.";
            $data['error']   = 1;
        }
    }
    return $conectado_a_unix;
}


function actualizarRoot12Cups($conex, $wemp_pmla, $wbasedato, $hay_unix, $user_session, &$data){
    if($hay_unix){
        $conexUnix = '';
        $conectado_a_unix = conexionUnixFn($hay_unix, $conexUnix, $data);

        if($conectado_a_unix){
            $arr_cups_matrix = consultarMestroCupsMatrix($conex, $wemp_pmla, $wbasedato, $data);
            $actualizar_maestro_matrix = comparar_MaestroMatrixUnix($conex, $wemp_pmla, $conexUnix, $wbasedato, $arr_cups_matrix, $data);

            if(count($actualizar_maestro_matrix) > 0){
                actualizarMaestroCUPSMatrix($conex, $wemp_pmla, $wbasedato, $actualizar_maestro_matrix, $user_session, $data);
            }
        }

        if($conectado_a_unix) {
            odbc_close($conexUnix);
            odbc_close_all();
        }
    }
}

function comparar_MaestroMatrixUnix($conex, $wemp_pmla, $conexUnix, $wbasedato, $arr_cups_matrix, &$data){
    $arr_cups_unix     = array();
    $arr_nuevos_matrix = array();
    $arr_modif_matrix  = array();
    $query_cupUx = "SELECT  cupcod AS codigo, cupdes AS nombre, cupact AS estado
                    FROM    incup";
    if($result_unx = @odbc_exec($conexUnix,$query_cupUx))
    {
        while(odbc_fetch_row($result_unx))
        {
            $codigo = odbc_result($result_unx, 'codigo');
            $nombre = utf8_encode(trim(odbc_result($result_unx, 'nombre')));
            $estado = odbc_result($result_unx, 'estado');

            $estado = ($estado == 'S') ? 'on':'off';

            if(!array_key_exists($codigo, $arr_cups_unix)){
                $arr_cups_unix[$codigo] = array();
            }
            $arr_cups_unix[$codigo] = array("codigo"=>$codigo,"nombre"=>$nombre,"estado"=>$estado);

            // $arr_compare1 = $arr_cups_unix[$codigo];
            // $arr_compare2 = $arr_cups_matrix[$codigo];

            // 1. Si el código de unix no existe en matrix, entonces se debe agregar
            // 2. Si existe en ambos pero no son igual entonces se debe actualizar
            if(!array_key_exists($codigo, $arr_cups_matrix)){
                $arr_nuevos_matrix[] = $arr_cups_unix[$codigo];
            } elseif ($arr_cups_unix[$codigo] !== $arr_cups_matrix[$codigo]) {
                // $uno = (!isset($uno)) ? (print_r(array($arr_cups_unix[$codigo],$arr_cups_matrix[$codigo]))): '';
                $arr_modif_matrix[] = $arr_cups_unix[$codigo];
            }
        }
    } else {
            $data["err"] = $query_cupUx.odbc_errormsg();
    }

    $actualizar_maestro_matrix = array();
    if(count($arr_nuevos_matrix) > 0){
        $actualizar_maestro_matrix["arr_nuevos_matrix"] = $arr_nuevos_matrix;
    }

    if(count($arr_modif_matrix) > 0){
        $actualizar_maestro_matrix["arr_modif_matrix"] = $arr_modif_matrix;
    }

    return $actualizar_maestro_matrix;
}

function consultarMestroCupsMatrix($conex, $wemp_pmla, $wbasedato, &$data){
    $arr_cups_matrix = array();
    $query_cupMx = "SELECT  Codigo AS codigo, Nombre AS nombre, Estado AS estado
                    FROM    root_000012";
    if($result_unx = mysql_query($query_cupMx,$conex))
    {
        while($row_mx = mysql_fetch_assoc($result_unx))
        {
            $codigo = $row_mx['codigo'];
            $nombre = utf8_encode(trim($row_mx['nombre']));
            $estado = $row_mx['estado'];

            if(!array_key_exists($codigo, $arr_cups_matrix)){
                $arr_cups_matrix[$codigo] = array();
            }
            $arr_cups_matrix[$codigo] = array("codigo"=>$codigo,"nombre"=>$nombre,"estado"=>$estado);
        }
    }

    return $arr_cups_matrix;
}

function insertBloque($conex, $wemp_pmla, $wbasedato, $string_insert, &$data){
    $ins = "INSERT INTO root_000012 (Medico, Fecha_data, Hora_data, Codigo, Nombre, Estado, Seguridad) VALUES ".implode(",", $string_insert);
    if($result = mysql_query($ins,$conex))
    {
        //
    } else {
        $data["errores_migracion"][] = array("mensaje"=>"Error insertando bloque de cups", "sql_error"=>$ins, "error_desc"=>mysql_error());
    }
}

function actualizarMaestroCUPSMatrix($conex, $wemp_pmla, $wbasedato, $actualizar_maestro_matrix, $user_session, &$data){

    $fecha_actual = date("Y-m-d");
    if(array_key_exists('arr_nuevos_matrix', $actualizar_maestro_matrix)){
        $arr_nuevos_matrix = $actualizar_maestro_matrix['arr_nuevos_matrix'];
        $bloque_max      = 500;
        $contador_bloque = 0;
        $contador_total  = 0;
        $total_array     = count($arr_nuevos_matrix);
        $string_insert   = array();

        foreach ($arr_nuevos_matrix as $key_cups => $arr_cups) {
            $contador_total++;
            $contador_bloque++;
            $hora_actual = date("H:i:s");

            $codigo = $arr_cups['codigo'];
            $nombre = utf8_decode($arr_cups['nombre']);
            $estado = $arr_cups['estado'];

            $string_insert[] = "('root', '{$fecha_actual}', '{$hora_actual}', '{$codigo}', '{$nombre}', '{$estado}', 'C-{$user_session}')";

            // Si ya se llegó al final de arreglo o si ya hay un bloque de inserts que se pueden insertar.
            if($contador_total == $total_array || $contador_bloque == $bloque_max){

                insertBloque($conex, $wemp_pmla, $wbasedato, $string_insert, $data);

                $contador_bloque = 0;
                $string_insert = array();
            }
        }

        $data['insertados'] = $contador_total;
    }

    if(array_key_exists('arr_modif_matrix', $actualizar_maestro_matrix)){
        $arr_modif_matrix = $actualizar_maestro_matrix['arr_modif_matrix'];

        foreach ($arr_modif_matrix as $key_cups => $arr_cups) {
            $hora_actual = date("H:i:s");

            $codigo = $arr_cups['codigo'];
            $nombre = utf8_decode($arr_cups['nombre']);
            $estado = $arr_cups['estado'];

            $updt = "UPDATE root_000012
                            SET Fecha_data = '{$fecha_actual}',
                                Hora_data = '{$hora_actual}',
                                Nombre = '{$nombre}',
                                Estado = '{$estado}'
                    WHERE   Codigo = '{$codigo}'" ;
            if($result = mysql_query($updt,$conex))
            {
                //
            } else {
                $data["errores_migracion"][] = array("mensaje"=>"Error actualizando cups [{$codigo}]", "sql_error"=>$updt, "error_desc"=>mysql_error());
            }
        }

        $data['actualizados'] = count($arr_modif_matrix);
    }
}

/**
    obtener_array_procedimientos_buscar()
    Retorna un arreglo con los códigos y nombre de procedimientos.
    @param link $conex       : link de conexión a la base de datos.
    @param string $wemp_pmla : código de la empresa.
    @param string $wbasedato : Prefijo de las tablas a consultar.
    @return array
 */
function obtener_array_procedimientos_buscar($conex, $wemp_pmla, $wbasedato, $search, $maestro_cups_root)
{
    $qPro = "";
    if($maestro_cups_root){
        $qPro= "SELECT  r12.Codigo AS codigo, r12.Nombre AS nombre
                FROM    root_000012 AS r12
                WHERE   CONCAT(r12.Codigo,'-',r12.Nombre) like '%{$search}%'"; // AND r12.Estado = 'on'
    }else{
        $qPro= "SELECT  t103.Procod AS codigo, t103.Pronom AS nombre
                FROM    {$wbasedato}_000103 AS t103
                WHERE   CONCAT(t103.Procod,'-',t103.Pronom) like '%{$search}%'"; // AND t103.Proest = 'on'
    }
            // Se muestran incluso los inactivos por si la actualización fue por unix directamente, entonces se permita seleccionar
            // nuevamente para actualizar por medio de este programa el lenguaje américas.
    $resPro = mysql_query($qPro,$conex) or die("Error: ".mysql_errno()." ".$qPro." - ".mysql_error());
    $fn_arr_procedimientos = array();
    while ($row = mysql_fetch_array($resPro))
    {
        $fn_arr_procedimientos[] = array("id"=>$row['codigo'],"text"=>$row['codigo']."-".utf8_encode($row['nombre']));
    }
    return $fn_arr_procedimientos;
}

/**
    obtener_array_procedimientos()
    Retorna un arreglo con los códigos y nombre de procedimientos.
    @param link $conex       : link de conexión a la base de datos.
    @param string $wemp_pmla : código de la empresa.
    @return array
 */
function obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato, $maestro_cups_root)
{
    $qPro = "";
    if($maestro_cups_root){
        $qPro= "SELECT  r12.Codigo AS codigo, r12.Nombre AS nombre
                FROM    root_000012 AS r12";
    }else{
        $qPro= "SELECT  t103.Procod AS codigo, t103.Pronom AS nombre
                FROM    {$wbasedato}_000103 AS t103";
    }

    $resPro = mysql_query($qPro,$conex) or die("Error: ".mysql_errno()." ".$qPro." - ".mysql_error());
    $fn_arr_procedimientos = array();
    while ($row = mysql_fetch_array($resPro))
    {
        $fn_arr_procedimientos[$row['codigo']] = $row['codigo']."-".utf8_encode($row['nombre']);
    }
    return $fn_arr_procedimientos;
}

/**
 * [arreglarNulosTemp: esta función es una alternativa a "construirQueryUnix" pues en mucho casos la cantidad de campos nulos en el select
 *                      supera los 10 campos y "construirQueryUnix" solo acepta 10, esta nueva función empieza a usar tablas temporales
 *                      y updates sobre campos nulos temporales para arreglar dichos campos evitando que se dañe el programa]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $conexUnix         [Id conexión a base de datos informix en unix => facturacion]
 * @param  [type] $temp_             [Identificador de la tabla temporal]
 * @param  [type] $campos_nulos      [Nombre de campos nulos en la tabla separados por coma]
 * @param  [type] $campos_tipo_fecha [Nombres de los campos en la tabla del tipo fecha contenidos en un array]
 * @return [type]                    [description]
 */
function arreglarNulosTemp($conex, $conexUnix, $temp_, $campos_nulos, $campos_tipo_fecha)
{
    $campos_nulos_expl = explode(",", $campos_nulos);
    foreach($campos_nulos_expl AS $key => $posible_campo_nulo)
    {
        $val_dafault = "";
        if(in_array(trim($posible_campo_nulo), $campos_tipo_fecha))
        {
            $val_dafault = "1900/01/01"; // Los tipo fecha no aceptan valor vacío, un campo tipo fecha con valor vacío lo sigue interpretando como NULL
        }

        $query_upt_null = " UPDATE  {$temp_}
                                    SET {$posible_campo_nulo} = '{$val_dafault}'
                            WHERE   {$posible_campo_nulo} IS NULL";
        // echo $query_acc; return;
        if($result_acc = @odbc_exec($conexUnix,$query_upt_null))
        {
        }
        else
        {
            // echo "error";
        }
    }
}

/**
 * [actualizarInsertarNuevoCupsUnix:
 *         Esta función se encarga de buscar que exista el nuevo código cups como código propio y código anexo en un mismo registro
 *         de maestro de procedimientos o exámenes, si existe inactivo entonces lo activa, si existe y está activo informa que ya
 *         estaba activo, si realmente no existe entonces consulta el código anterior, copia todos sus datos e inserta un registro
 *         nuevo actualizando el valor del código propio y código anexo por el valor del nuevo cups.]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $conexUnix         [Id conexión a base de datos informix en unix => facturacion]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] $defaultMaestro    [Parámetros por defecto para permitir utilizar la tabla unix correcta para procedimientos y exámenes]
 * @param  [type] $suf_pref          [Prefijo que identifica si se va a usar el maestro de procedimiento o examen]
 * @param  [type] $nombre_maestro    [Nombre entendible del maestro que se está usando]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type] $nuevoCupActivo    [Retorna true para saber si el nuevo cups ya está activo en el maestro, bien sea porque se cambió de estado o se insertó.]
 */
function actualizarInsertarNuevoCupsUnix($conex, $conexUnix, $modo_pruebas, $cupAnterior, $cupNuevo, $defaultMaestro, $suf_pref, $nombre_maestro, &$resultadoProceso){
    $nuevoCupActivo = false;
    $fecha_actualizado = date("Y-m-d H:i:s");
    $index_res = "in{$suf_pref}";

    // Consulta si existe ya el código propio-anexo con el nuevo cups.
    // No consultó más campos para no tener que realizar comprobación de campos nulos para compararlo con los campos del cups anterior
    // y saber si se tiene que actualizar el nombre, grupo o uvrs, simplemente se consulto todos los datos del cups anterior al que si
    // se le comprueban valores nulos y de existir el cups nuevo se hace update a los campos necesarios de una vez.
    $sql_inproCupProp = "SELECT {$suf_pref}act AS estado FROM in{$suf_pref} WHERE {$suf_pref}ane = '{$cupNuevo}' AND {$suf_pref}cod = '{$cupNuevo}'";
    if($result_inpro = @odbc_exec($conexUnix,$sql_inproCupProp)) {

        $campos_tabla_data = array();
        // Si el nuevo cups existe y se debe actualizar o no existe como propio-enaxo entonces se consulta el anterior cups y se inserta de nuevo
        // pero actualizando el código propio-anexo con el nuevo cups

        // INICIO#>>>>>>>>>>> CONSULTA DATOS DEL CUPS ANTERIOR <<<<<<<<<<<<<<<<<<<<<
        // [[ ** Proceso para consultar todos los campos de un registro en unix sin tener problemas con los valores nulos.
        $temp_cup = "t".$suf_pref.trim($cupAnterior);//.trim($cupNuevo);
        $arr_camposSelect = array();

        // La tabla de exámenes y procedimientos no tienen los mismo campos, es por eso que según sea el caso
        // se construye el select con los campos de cada tabla parametrizados en un arreglo.
        if($suf_pref == 'pro'){
            // Procedimiento
            $arr_camposSelect = array(  "no_null"    => "procod,pronom,protip,promay,procla,proane,proact",
                                        "tipo_fecha" => array("profad", "profmo"),
                                        "nulos"      => "prodes,proqui,proniv,prouni,proesp,propan,proliq,profac,prouad,profad,proumo,profmo");
        } else {
            // Examen
            $arr_camposSelect = array(  "no_null"    => "exacod,exanom,exaact",
                                        "tipo_fecha" => array("exafad", "exafmo"),
                                        "nulos"      => "exades,exagex,exaniv,exaane,exaliq,exauni,exafac,exauad,exafad,exaumo,exafmo");
        }

        $campos_select_no_null = $arr_camposSelect["no_null"];
        $campos_tipo_fecha     = $arr_camposSelect["tipo_fecha"];
        $campos_nulos          = $arr_camposSelect["nulos"];

        $query_proTm = "SELECT  {$campos_select_no_null}, {$campos_nulos}
                        FROM    in{$suf_pref}
                        WHERE   {$suf_pref}ane = '{$cupAnterior}' AND {$suf_pref}cod = '{$cupAnterior}'
                        INTO temp {$temp_cup}";
        // ]] validar valores nulos
        if($result_proTm = @odbc_exec($conexUnix,$query_proTm))
        {
            // Función creada para asignar un valor por defecto a los campos nulos en una tabla temporal y poderlos leer desde php por medio
            // del odbc sin problemas.
            arreglarNulosTemp($conex, $conexUnix, $temp_cup, $campos_nulos, $campos_tipo_fecha);

            $implcampos_NoNull = explode(",", $arr_camposSelect["no_null"]);
            $implcampos_Null   = explode(",", $arr_camposSelect["nulos"]);
            $campos_tabla      = array_merge($implcampos_NoNull, $implcampos_Null);

            $sql_inproCupProp = "   SELECT  {$campos_select_no_null}, {$campos_nulos}
                                    FROM    {$temp_cup}";
            if($result_unx = @odbc_exec($conexUnix,$sql_inproCupProp))
            {
                if(odbc_fetch_row($result_unx))
                {
                    // Se crea un array asociativo donde los index son los nombres de cada campo en la tabla y el value es el dato leído de la tabla temporal

                    foreach ($campos_tabla as $key_ => $campo_in) {
                        $campos_tabla_data[$campo_in] = odbc_result($result_unx,$suf_pref.'act');
                    }

                    $campos_tabla_data["{$suf_pref}fad"] = date("Y-m-d H:i:s");
                    $campos_tabla_data["{$suf_pref}fad"] = date("Y-m-d H:i:s");
                    // Se actualiza el código propio y código anexo con el nuevo cups antes de insertar el nuevo registro.
                    $campos_tabla_data["{$suf_pref}cod"] = $cupNuevo;
                    $campos_tabla_data["{$suf_pref}ane"] = $cupNuevo;
                }
            } else {
                $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_inproCupProp, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error intentando consultar el CUPS [{$cupAnterior}] como código anexo y código propio en maestro de {$nombre_maestro} Unix <small><u>[Error:valores nulos]</u></small>.");
            }
        } else {
            $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($query_proTm, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error intentando comprobar si el código CUPS [{$cupAnterior}] existe como código anexo y código propio en maestro de {$nombre_maestro} Unix <small><u>[Error:1]</u></small>.");
        }
        // FIN#>>>>>>>>>>> CONSULTA DATOS DEL CUPS ANTERIOR <<<<<<<<<<<<<<<<<<<<<

        // Después de consultar los datos del cupsAnterior se verifica si el cups nuevo existe y se debe actualizar o si se debe insertar como nuevo.
        if(odbc_fetch_row($result_inpro)) {
            // Se valída que se consultó correctamente el cups anterior y se procede a encontrar algunas diferencia con el cups nuevo para actualizarlas.
            $campos_update_add = "";
            if(count($campos_tabla_data) > 0){
                if($suf_pref == 'pro'){
                    $campos_update_add = "  , {$suf_pref}nom = '".$campos_tabla_data["{$suf_pref}nom"]."'
                                            , {$suf_pref}des = '".$campos_tabla_data["{$suf_pref}des"]."'
                                            , {$suf_pref}qui = '".$campos_tabla_data["{$suf_pref}qui"]."'
                                            , {$suf_pref}uni = '".$campos_tabla_data["{$suf_pref}uni"]."'";
                } else {
                    $campos_update_add = "  , {$suf_pref}nom = '".$campos_tabla_data["{$suf_pref}nom"]."'
                                            , {$suf_pref}des = '".$campos_tabla_data["{$suf_pref}des"]."'
                                            , {$suf_pref}gex = '".$campos_tabla_data["{$suf_pref}gex"]."'
                                            , {$suf_pref}uni = '".$campos_tabla_data["{$suf_pref}uni"]."'";
                }
            }

            // Si ya existe el nuevo código como propio-anexo verifica si está activo o no.
            $estado_cup = odbc_result($result_inpro,'estado');

            // Si el código pripio-anexo esta inactivo entonces se intenta cambiar el estado a activo.
            $sqlUpd_inproCupProp = "UPDATE in{$suf_pref} SET {$suf_pref}act = 'S', {$suf_pref}fmo = '{$fecha_actualizado}' {$campos_update_add} WHERE {$suf_pref}ane = '{$cupNuevo}' AND {$suf_pref}cod = '{$cupNuevo}'";
            ($modo_pruebas) ? ($sqlUpd_inproCupProp = "UPDATE in{$suf_pref} SET {$suf_pref}act = 'S', {$suf_pref}fmo = '{$fecha_actualizado}' {$campos_update_add} WHERE 1<>1") : '';
            if($resultUpd_inpro = @odbc_exec($conexUnix,$sqlUpd_inproCupProp)) {
                $nuevoCupActivo = true;

                $msj_cambio_estado = ($estado_cup == 'N') ? "estaba inactivo y <u>se ACTIVÓ nuevamente</u>, ": "";
                $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Se encontró el CUPS [{$cupNuevo}] en un mismo registro como código anexo y código propio, {$msj_cambio_estado}se actualizó descripción, grupo, uvr si era necesario en maestro {$nombre_maestro} Unix.");
            } else {
                $msj_cambio_estado = ($estado_cup == 'N') ? "estaba inactivo y <u>se ACTIVÓ nuevamente</u>, ": "";
                $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sqlUpd_inproCupProp, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Se encontró el CUPS [{$cupNuevo}] en un mismo registro como código anexo y código propio pero no se pudo hacer actualización de estado, descripción, grupo o uvr si era necesario en maestro {$nombre_maestro} Unix.");
            }

            if($estado_cup != 'N'){
                $nuevoCupActivo = true;
                $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"info", "mensaje" => "Se encontró el CUPS [{$cupNuevo}] en un mismo registro como código anexo y código propio. No fué necesario crearlo nuevamente en el maestro {$nombre_maestro} Unix.");
            }
        } else {

            if(count($campos_tabla_data) > 0){
                // $campos_tabla_data = "insert into tabla (".implode(",",array_keys($campos_tabla_data)).") <br> values ('".implode("','", $campos_tabla_data)."')";

                $labelsUx_insert = implode(",",array_keys($campos_tabla_data));
                $valuesUx_insert = implode("','", $campos_tabla_data);

                $sqlIns_cup = " INSERT INTO in{$suf_pref}
                                            ({$labelsUx_insert})
                                VALUES      ('{$valuesUx_insert}')";
                ($modo_pruebas) ? ($sqlIns_cup = "INSERT INTO in{$suf_pref}eeee () VALUES ()") : '';
                if($resultIns_inpro = @odbc_exec($conexUnix,$sqlIns_cup)) {
                    $nuevoCupActivo = true;
                    $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Código CUPS [{$cupNuevo}] se <mark>INSERTÓ</mark> como código anexo y código propio en el maestro {$nombre_maestro}.");
                } else {
                    $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sqlIns_cup, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error intentando guardar el CUPS [{$cupNuevo}] como código anexo y código propio en maestro de {$nombre_maestro} Unix <small><u>[Error:No insertó]</u></small>.");
                }
            }
        }
    } else {
        $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_inproCupProp, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error intentando comprobar si el código CUPS [{$cupNuevo}] existe como código anexo y código propio en maestro de {$nombre_maestro} Unix <small><u>[Error:2]</u></small>.");
    }
}

/**
 * [actualizarInsertarNuevoCupsMatrix:
 *         Esta función se encarga de buscar que exista el nuevo código cups como código propio y código anexo en un mismo registro
 *         de maestro de procedimientos y exámenes, si existe inactivo entonces lo activa, si existe y está activo informa que ya
 *         estaba activo, si realmente no existe entonces consulta el código anterior, copia todos sus datos e inserta un registro
 *         nuevo actualizando el valor del código propio y código anexo por el valor del nuevo cups.]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $user_session      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $wbasedato_cliame  [Prefijo de base de datos para maestro matrix]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function actualizarInsertarNuevoCupsMatrix($conex, $modo_pruebas,  $user_session, $wbasedato_cliame, $cupAnterior, $cupNuevo, &$resultadoProceso){
    $fecha_actualizado = date("Y-m-d H:i:s");
    // Consulta si existe ya el código propio-anexo con el nuevo cups.
    $sql_inproCupProp = "SELECT Proest AS estado FROM {$wbasedato_cliame}_000103 WHERE Procup = '{$cupNuevo}' AND Procod = '{$cupNuevo}'";
    if($result_inpro = mysql_query($sql_inproCupProp,$conex)) {
        if($row1 = mysql_fetch_assoc($result_inpro)) {
            // Si ya existe el nuevo código como propio-anexo verifica si está activo o no.
            $estado_cup = $row1["estado"];
            if($estado_cup == 'off'){
                // Si el código pripio-anexo esta inactivo entonces se intenta cambiar el estado a activo.
                $sqlUpd_inproCupProp = "UPDATE {$wbasedato_cliame}_000103 SET Proest = 'on' WHERE Procup = '{$cupNuevo}' AND Procod = '{$cupNuevo}'";
                ($modo_pruebas) ? ($sqlUpd_inproCupProp = "UPDATE {$wbasedato_cliame}_000103 SET Proest = 'on' WHERE 1<>1") : '';
                if($resultUpd_inpro = mysql_query($sqlUpd_inproCupProp,$conex)) {
                    $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Se encontró el CUPS [{$cupNuevo}] en un mismo registro como código anexo y código propio, estaba inactivo y <mark>se ACTIVÓ nuevamente</mark> en maestro procedimientos-exámenes Matrix.");
                } else {
                    $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Se encontró el CUPS [{$cupNuevo}] en un mismo registro como código anexo y código propio pero <mark>INACTIVO</mark>, no se puedo cambiar el estado a ACTIVO en maestro procedimientos-exámenes Matrix.");
                }
            } else {
                $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"info", "mensaje" => "Se encontró el CUPS [{$cupNuevo}] en un mismo registro como código anexo y código propio. No fué necesario crearlo nuevamente en el maestro procedimientos-exámenes Matrix.");
            }
        } else {
            $query_proTm = "INSERT  INTO {$wbasedato_cliame}_000103
                                    (Medico, Fecha_data, Hora_data, Procod, Pronom, Protfa, Progqx, Propun, Protip, Proest, Procup, Procpg, Promdu, Proqui, Protmm, Pronoc, Proopo, Seguridad)
                            SELECT  Medico, curdate(), curtime(), '{$cupNuevo}', Pronom, Protfa, Progqx, Propun, Protip, 'on', '{$cupNuevo}', Procpg, Promdu, Proqui, Protmm, Pronoc, Proopo, '{$user_session}'
                            FROM    {$wbasedato_cliame}_000103
                            WHERE   Procup = '{$cupNuevo}' AND Procod = '{$cupNuevo}'";
            ($modo_pruebas) ? ($query_proTm="INSERT  INTO {$wbasedato_cliame}_000103 () VALUES ()") : '';
            if($result_proTm = mysql_query($query_proTm,$conex)) {
                $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Código CUPS [{$cupNuevo}] se <mark>INSERTÓ</mark> como código anexo y código propio en el maestro procedimientos-exámenes Matrix.");
            } else {
                $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error intentando comprobar si el código CUPS [{$cupNuevo}] existe como código anexo y código propio en maestro de procedimientos-exámenes Matrix. <small><u>[Error:1]</u></small>.");
            }
        }
    } else {
        $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error intentando comprobar si el código CUPS [{$cupNuevo}] existe como código anexo y código propio en maestro procedimientos-exámenes Matrix. <small><u>[Error:2]</u></small>.");
    }
}

/**
 * [actualizarCupTarifasUnix: Función encargada de buscar las tarifas de procedimientos y exámenes para reemplazar el código cups anterior por el nuevo cups.]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $conexUnix         [Id conexión a base de datos informix en unix => facturacion]
 * @param  [type] $user_session      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] $defaultMaestro    [Parámetros por defecto para permitir utilizar la tabla unix correcta para procedimientos y exámenes]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function actualizarCupTarifasUnix($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, &$resultadoProceso){
    $suf_pref          = $defaultMaestro["sufijo_prefijo"];
    $nombre_maestro    = $defaultMaestro["nombre_maestro"];
    $fecha_actualizado = date("Y-m-d H:i:s");
    $index_res         = "in{$suf_pref}_tarifa";
    $tabla_unx         = "in{$suf_pref}tar";
    $where_unx         = "{$suf_pref}tar{$suf_pref} = '{$cupAnterior}'";
    $actualizacion_correcta = false;
    $resultadoProceso[$index_res] = array("nombre"=> "[UNIX] Maestro tarifas de {$nombre_maestro}", "mensajes" => array()); // Parámetro para mensajes de respuesta de esta función

    // Consultar las tarifas asociadas al cups anterior.
    $tarifas_actualizables = 0;
    $msj_actualizables     = "";
    $sql_cupTarCount       = "SELECT count(*) as count_tar FROM {$tabla_unx} WHERE {$where_unx}";
    if($result_countTar = @odbc_exec($conexUnix,$sql_cupTarCount)) {
        if(odbc_fetch_row($result_countTar)) {
            $tarifas_actualizables = odbc_result($result_countTar,'count_tar')*1;

            if($tarifas_actualizables > 0){
                // Backup de registros a actualizar
                $nombre_archivo = "bkUMx_{$tabla_unx}_{$cupAnterior}";
                backupRegistrosOriginalesUnx($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $tabla_unx, $where_unx, $nombre_archivo, $resultadoProceso);

                $sql_inTar = "UPDATE {$tabla_unx} SET {$suf_pref}tar{$suf_pref} = '{$cupNuevo}', {$suf_pref}tarfmo = '{$fecha_actualizado}' WHERE {$where_unx}"; // Actualizar el código de la tarifa.
                ($modo_pruebas) ? ($sql_inTar = "UPDATE {$tabla_unx} SET {$suf_pref}tar{$suf_pref} = '{$cupNuevo}' WHERE 1<>1") : '';
                if($result_inTar = @odbc_exec($conexUnix,$sql_inTar)) {
                        $actualizacion_correcta = true;
                        $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Las tarifas asociadas al código propio [{$cupAnterior}] se actualizaron con el código [{$cupNuevo}] en maestro tarifas de {$nombre_maestro} Unix.","updated"=>"Actualizadas: ".$tarifas_actualizables);
                } else {
                    $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_inTar, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "No existen registros de tarifas de {$nombre_maestro} con el código [{$cupAnterior}] y que se puedan actualizar al nuevo código [{$cupNuevo}].");
                }
            }
        }
    } else {
        // $msj_actualizables = "<br><small><cite>No se pudo establecer cuántos registros de tarifas pudieron ser actualizadas.</cite></small>";
        $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_cupTarCount, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error, No se pudo establecer cuántos registros de tarifas de {$nombre_maestro} existen para actualizar.");
    }
}

/**
 * [actualizarCupTarifasHabUnix: Función encargada de buscar tarifas de habitaciones que tengan el código cups a inactivar y se reemplaza por el nuevo cups.]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $conexUnix         [Id conexión a base de datos informix en unix => facturacion]
 * @param  [type] $user_session      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] $defaultMaestro    [Parámetros por defecto para permitir utilizar la tabla unix correcta para procedimientos y exámenes]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function actualizarCupTarifasHabUnix($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, &$resultadoProceso){
    $suf_pref          = $defaultMaestro["sufijo_prefijo"];
    $nombre_maestro    = $defaultMaestro["nombre_maestro"];
    $fecha_actualizado = date("Y-m-d H:i:s");
    $index_res         = "in{$suf_pref}_tarifa_hab";
    $tabla_unx         = "intip";
    $where_unx         = "tipnal = '{$cupAnterior}'";
    $actualizacion_correcta = false;

    // Tanto para procedimientos como para exámenes se va a llamar esta misma función, pero sin importar si el código a reemplazar aparezca como procedimiento o examen,
    // en el maestro de tarifas solo va a estar una sola vez, es por eso que se valida que si este tipo de actualización ya se agrego al arreglo de respuesta,
    // no se vuelva a ejecutar para no repetir el proceso que ya se hizo en la primer iteración.
    if(!array_key_exists($index_res, $resultadoProceso)){
        $resultadoProceso[$index_res] = array("nombre"=> "[UNIX] Maestro tarifa de habitaciones", "mensajes" => array()); // Parámetro para mensajes de respuesta de esta función

        // Consultar las tarifas asociadas al cups anterior.
        $tarifas_actualizablesHab = 0;
        $msj_actualizables     = "";
        $sql_cupTarHabCount       = "SELECT count(*) as count_tar FROM {$tabla_unx} WHERE {$where_unx}";
        if($result_countTarHab = @odbc_exec($conexUnix,$sql_cupTarHabCount)) {
            if(odbc_fetch_row($result_countTarHab)) {
                $tarifas_actualizablesHab = odbc_result($result_countTarHab,'count_tar')*1;

                if($tarifas_actualizablesHab > 0){
                    // Backup de registros a actualizar
                    $nombre_archivo = "bkUMx_{$tabla_unx}_{$cupAnterior}";
                    backupRegistrosOriginalesUnx($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $tabla_unx, $where_unx, $nombre_archivo, $resultadoProceso);

                    $sql_inTarHab = "UPDATE {$tabla_unx} SET tipnal = '{$cupNuevo}', tipfmo = '{$fecha_actualizado}' WHERE {$where_unx}"; // Actualizar el código de la tarifa de habitación.
                    ($modo_pruebas) ? ($sql_inTarHab = "UPDATE {$tabla_unx} SET tipnal = '{$cupNuevo}' WHERE 1<>1") : '';
                    if($result_inTarHab = @odbc_exec($conexUnix,$sql_inTarHab)) {
                            $actualizacion_correcta = true;
                            $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Las tarifas de habitación asociadas al CUPS [{$cupAnterior}] se actualizaron con el código [{$cupNuevo}] en maestro tarifas de habitaciones Unix.","updated"=>"Actualizadas: ".$tarifas_actualizablesHab);
                    } else {
                        $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_inTarHab, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error actualizando el código [{$cupAnterior}] al nuevo código [{$cupNuevo}] en el maestro de tarifas de habitaciones Unix.","updated"=>"Sin actualizar: ".$tarifas_actualizablesHab);
                    }
                } else {
                    $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"info", "mensaje" => "No existen registros de tarifas de habitación con el código [{$cupAnterior}] y que se puedan actualizar al nuevo código [{$cupNuevo}].");
                }
            }
        } else {
            $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_cupTarHabCount, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error, No se pudo establecer cuántos registros de tarifas de habitación existen para actualizar.");
        }
    }
}

/**
 * [actualizarCupRelacionEmpresa: Realiza una búsqueda del CUPS en las tablas de relación por empresa y reemplaza el anterior cups por el nuevo código cups.]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $conexUnix         [Id conexión a base de datos informix en unix => facturacion]
 * @param  [type] $user_session      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] $defaultMaestro    [Parámetros por defecto para permitir utilizar la tabla unix correcta para procedimientos y exámenes]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function actualizarCupRelacionEmpresa($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, &$resultadoProceso){
    $suf_pref          = $defaultMaestro["sufijo_prefijo"];
    $nombre_maestro    = $defaultMaestro["nombre_maestro"];
    $fecha_actualizado = date("Y-m-d H:i:s");
    $index_res         = "in{$suf_pref}_emp"; // Relación con empresa
    $tabla_unx         = "in{$suf_pref}emp";
    $where_unx         = "{$suf_pref}emp{$suf_pref} = '{$cupAnterior}'";
    $actualizacion_correcta = false;

    // Se consulta cuántos registros hay donde el anexo sea igual al código cupAnterior que se va a actualizar.
    $sqlSel_inpro = "SELECT count(*) as registros FROM {$tabla_unx} WHERE {$where_unx}";
    $resultadoProceso[$index_res] = array("nombre"=> "[UNIX] Relación empresa {$nombre_maestro}", "mensajes" => array()); // Parámetro para mensajes de respuesta de esta función
    if($resultSel_inpro = @odbc_exec($conexUnix,$sqlSel_inpro)) {
        if(odbc_fetch_row($resultSel_inpro)) {
            $anexos_encontrados = odbc_result($resultSel_inpro,'registros');
            // Si efectivamente existen registros se continúa con el reemplazo.
            if($anexos_encontrados > 0){
                // Backup de registros a actualizar
                $nombre_archivo = "bkUMx_{$tabla_unx}_{$cupAnterior}";
                backupRegistrosOriginalesUnx($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $tabla_unx, $where_unx, $nombre_archivo, $resultadoProceso);

                // En estas tablas el código CUPS está en el campo de código principal y en el campo de anexo está el código propio.
                $sql_inpro = "UPDATE {$tabla_unx} SET {$suf_pref}emp{$suf_pref} = '{$cupNuevo}', {$suf_pref}empfmo = '{$fecha_actualizado}' WHERE {$where_unx}"; // Actualizar todo lo que en anexo y código sea diferente
                ($modo_pruebas) ? ($sql_inpro = "UPDATE {$tabla_unx} SET {$suf_pref}emp{$suf_pref} = '{$cupNuevo}', {$suf_pref}empfmo = '{$fecha_actualizado}' WHERE 1<>1") : '';
                if($result_inpro = @odbc_exec($conexUnix,$sql_inpro)) {
                    $cantidadActualizada = $anexos_encontrados;
                    $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Actualización satisfactoria en relación empresa {$nombre_maestro} Unix. Código [{$cupAnterior}] se cambió a [{$cupNuevo}].","updated"=>"Actualizó ~: ".$cantidadActualizada);
                    $actualizacion_correcta = true;
                } else {
                    $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_inpro, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error actualizando relación empresa {$nombre_maestro} Unix.","updated"=>"Sin actualizar: ".$anexos_encontrados);
                }
            } else {
                $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"info", "mensaje" => "Código CUPS [{$cupAnterior}] no se encontró en la relación empresa {$nombre_maestro}.");
            }
        } else {
            $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error recuperando respuesta código CUPS [{$cupAnterior}] en relación empresa {$nombre_maestro} Unix.");
        }
    }else{
        $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sqlSel_inpro, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error consultando código CUPS [{$cupAnterior}] en relación empresa {$nombre_maestro} Unix.");
    }
    return $actualizacion_correcta;
}

/**
 * [backupRegistrosOriginalesUnx: Función que se encarga de realizar un respaldo de los registros que se van a actualizar]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $conexUnix         [Id conexión a base de datos informix en unix => facturacion]
 * @param  [type] $modo_pruebas      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $user_session      [Código del cups a inactivar]
 * @param  [type] $cupAnterior       [Código del cups que reemplaza el inactivo]
 * @param  [type] $cupNuevo          [Parámetros por defecto para permitir utilizar la tabla unix correcta para procedimientos y exámenes]
 * @param  [type] $defaultMaestro    [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @param  [type] $tabla_bkp         [Nombre de la tabla unix desde donde se van a respaldar los registros]
 * @param  [type] $where             [Filtros para seleccionar los registros a respaldar]
 * @param  [type] $nombre_archivo    [Nombre del archivo donde se realizará el respaldo]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function backupRegistrosOriginalesUnx($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $tabla_bkp, $where, $nombre_archivo, &$resultadoProceso) {

    return false;
    /*$suf_pref          = $defaultMaestro["sufijo_prefijo"];
    $nombre_maestro    = $defaultMaestro["nombre_maestro"];
    $fecha_actualizado = date("Y-m-d H:i:s");
    $actualizacion_correcta = false;
    $index_res = "backups"; // Relación con empresa

    $sqlSel_bkp = "unload to '{$nombre_archivo}.txt' SELECT * FROM $tabla_bkp WHERE {$where}";
    $sqlSel_bkp = "SELECT * INTO OUTFILE '{$nombre_archivo}.txt' FROM $tabla_bkp WHERE {$where}";
    $resultadoProceso[$index_res] = array("nombre"=> "[UNIX] backup registros [$tabla_bkp - {$nombre_maestro}]", "mensajes" => array()); // Parámetro para mensajes de respuesta de esta función
    if($resultSel_bkp = @odbc_exec($conexUnix,$sqlSel_bkp)) {
        $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Backup correcto para registros de la tabla [$tabla_bkp - {$nombre_maestro}] filtros [{$where}]. Código [{$cupAnterior}] se cambió a [{$cupNuevo}].");
    }else{
        $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sqlSel_bkp, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error generando backup en [$tabla_bkp - {$nombre_maestro}] filtros [{$where}].");
    }*/
}

/**
 * [actualizarMaestroUnix: Realiza una búsqueda del CUPS a inactivar y se actualiza por el nuevo CUPS. Se varifica si se debe insertar el nuevo CUPS como principal]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $conexUnix         [Id conexión a base de datos informix en unix => facturacion]
 * @param  [type] $user_session      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] $defaultMaestro    [Parámetros por defecto para permitir utilizar la tabla unix correcta para procedimientos y exámenes]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function actualizarMaestroUnix($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, &$resultadoProceso, &$nuevoCupActivo){
    $suf_pref          = $defaultMaestro["sufijo_prefijo"];
    $nombre_maestro    = $defaultMaestro["nombre_maestro"];
    $fecha_actualizado = date("Y-m-d H:i:s");
    $actualizacion_correcta = false;
    $index_res = "in{$suf_pref}";
    $tabla_unx = "in{$suf_pref}";

    // Se consulta cuántos registros hay donde el anexo sea igual al código cupAnterior que se va a actualizar.
    $sqlSel_inpro = "SELECT count(*) as registros FROM {$tabla_unx} WHERE {$suf_pref}ane = '{$cupAnterior}'";
    $resultadoProceso[$index_res] = array("nombre"=> "[UNIX] Maestro {$nombre_maestro}", "mensajes" => array()); // Parámetro para mensajes de respuesta de esta función
    if($resultSel_inpro = @odbc_exec($conexUnix,$sqlSel_inpro)) {
        if(odbc_fetch_row($resultSel_inpro)) {
            $anexos_encontrados = odbc_result($resultSel_inpro,'registros');
            // Si efectivamente existen registros se continúa con el reemplazo.
            if($anexos_encontrados > 0){
                // Backup de registros a actualizar
                $where_unx = "{$suf_pref}ane = '{$cupAnterior}'";
                $nombre_archivo = "bkUMx_{$tabla_unx}_{$cupAnterior}";
                backupRegistrosOriginalesUnx($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $tabla_unx, $where_unx, $nombre_archivo, $resultadoProceso);

                // NOTA: solo se actualizan los registros que correspondan al anexo igual al cupAnterior y donde código propio sea diferente a cupAnterior,
                // pues si existe un registro con código propio-anexo igual al cupAnterior, entonces ese se debe inactivar y crear uno nuevo donde propio-anexo sean igual a cupNuevo
                $sql_inpro = "UPDATE {$tabla_unx} SET {$suf_pref}ane = '{$cupNuevo}', {$suf_pref}fmo = '{$fecha_actualizado}' WHERE {$suf_pref}ane = '{$cupAnterior}' AND {$suf_pref}cod <> '{$cupAnterior}'"; // Actualizar todo lo que en anexo y código sea diferente
                ($modo_pruebas) ? ($sql_inpro = "UPDATE {$tabla_unx} SET {$suf_pref}ane = '{$cupNuevo}' WHERE 1<>1") : '';
                if($result_inpro = @odbc_exec($conexUnix,$sql_inpro)) {
                    // Nuevo cups activo, donde código propio-anexo es igual al nuevo código.
                    $nuevoCupActivo = actualizarInsertarNuevoCupsUnix($conex, $conexUnix, $modo_pruebas, $cupAnterior, $cupNuevo, $defaultMaestro, $suf_pref, $nombre_maestro, $resultadoProceso);

                    $sql_inproCupProp = "UPDATE {$tabla_unx} SET {$suf_pref}act = 'N', {$suf_pref}fmo = '{$fecha_actualizado}' WHERE {$suf_pref}ane = '{$cupAnterior}' AND {$suf_pref}cod = '{$cupAnterior}'";
                    ($modo_pruebas) ? ($sql_inproCupProp = "UPDATE {$tabla_unx} SET {$suf_pref}act = 'N', {$suf_pref}fmo = '{$fecha_actualizado}' WHERE 1<>1") : '';
                    if($result_inpro = @odbc_exec($conexUnix,$sql_inproCupProp)) {
                        $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Se <u>INACTIVÓ CUPS [{$cupAnterior}]</u> en maestro {$nombre_maestro} Unix."); //,"updated" => "Actualizados: ".odbc_num_rows($result_inpro)
                    } else {
                        $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_inproCupProp, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "No se pudo inactivar el CUPS [{$cupAnterior}] en maestro {$nombre_maestro} Unix.");
                    }

                    $cantidadActualizada = ($anexos_encontrados > 0) ? ($anexos_encontrados*1 - 1): 0; // se resta el registro donde propio-anexo es igual, pues para ese registro se hace otro proceso de actualización en actualizarInsertarNuevoCupsUnix
                    $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Actualización satisfactoria en maestro de {$nombre_maestro} Unix. Código anexo [{$cupAnterior}] se cambió a [{$cupNuevo}] en por lo menos <u>{$cantidadActualizada}</u> registros."); //,"updated" => "Actualizados: ".odbc_num_rows($result_inpro)
                    $actualizacion_correcta = true;
                } else {
                    $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sql_inpro, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error actualizando maestro de {$nombre_maestro} Unix.","updated"=>"Sin actualizar: ".$anexos_encontrados);
                }
            } else {
                $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"info", "mensaje" => "Código CUPS [{$cupAnterior}] no se encontró como {$nombre_maestro}.");
            }
        } else {
            $resultadoProceso[$index_res]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error recuperando respuesta código CUPS [{$cupAnterior}] en campo anexo del maestro de {$nombre_maestro} Unix.");
        }

        // >> ACTUALIZACIÓN DE TARIFAS
        // Si existe ya el nuevo cups como activo se actualizan las tarifas, cambiando el cups anterior por el nuevo cup.
        // if($nuevoCupActivo)
        {
            $actualizoTarifas    = actualizarCupTarifasUnix($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $resultadoProceso);
        }
        // Si ya se hizo para procedimiento, no lo vuelva a hacer para examen porque es lo mismo.
        if(!array_key_exists("inexa", $resultadoProceso)){
            $actualizoTarifasHab = actualizarCupTarifasHabUnix($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $resultadoProceso);
        }

        // >> ACTUALIZACIÓN DE RELACIÓN EMPRESA PROCEDIMIENTOS/EXAMENES
        $actualizoRelacionEmp = actualizarCupRelacionEmpresa($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $resultadoProceso);
    }else{
        $resultadoProceso[$index_res]["mensajes"][] = array("err_sql"=>htmlentities($sqlSel_inpro, ENT_QUOTES).">".odbc_errormsg(),"tip_class"=>"danger", "mensaje" => "Error consultando código CUPS [{$cupAnterior}] en campo anexo del maestro de {$nombre_maestro} Unix.");
    }
    return $actualizacion_correcta;
}

/**
 * [actualizarMaestroMatrix: Busca todos los registros en cliame_103 donde en el campo anexo encuentre el anterior código cups y los actualiza con el nuevo cups]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $user_session      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $wbasedato_cliame  [Prefijo de base de datos para maestro matrix]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function actualizarMaestroMatrix($conex, $modo_pruebas, $user_session, $wbasedato_cliame, $cupAnterior, $cupNuevo, &$resultadoProceso){
    $actualizacion_correcta = false;

    $sqlSel_103 = "SELECT count(*) as registros FROM {$wbasedato_cliame}_000103 WHERE Procup = '{$cupAnterior}'";
    $resultadoProceso["cli_103"] = array("nombre"=> "[Matrix] Maestro procedimientos y exámenes", "mensajes" => array());
    if($resultSel_103 = mysql_query($sqlSel_103,$conex)) {
        if($row_regs = mysql_fetch_assoc($resultSel_103)) {
            $anexos_encontrados = $row_regs["registros"];
            if($anexos_encontrados > 0){
                $sqlUpd_103 = "UPDATE {$wbasedato_cliame}_000103 SET Procup = '{$cupNuevo}' WHERE Procuppp = '{$cupAnterior}' AND Procod <> '{$cupAnterior}'";
                ($modo_pruebas) ? ($sqlUpd_103 = "UPDATE {$wbasedato_cliame}_000103 SET Procup = '{$cupNuevo}' WHERE 1<>1") : '';
                if($resultSel_103 = mysql_query($sqlUpd_103,$conex)) {
                    $respuestaInsertNuevoCup = actualizarInsertarNuevoCupsMatrix($conex, $modo_pruebas, $user_session, $wbasedato_cliame, $cupAnterior, $cupNuevo, $resultadoProceso);

                    $sql_inproCupProp = "UPDATE {$wbasedato_cliame}_000103 SET Proest = 'off' WHERE Procod = '{$cupAnterior}' AND Procup = '{$cupAnterior}'";
                    ($modo_pruebas) ? ($sql_inproCupProp = "UPDATE {$wbasedato_cliame}_000103 SET Proest = 'off' WHERE 1<>1") : '';
                    if($result_inpro = mysql_query($sql_inproCupProp,$conex)) {
                        $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Se <mark>INACTIVÓ CUPS [{$cupAnterior}]</mark> en maestro de procedimientos-exámenes Matrix.","updated" => "Actualizó: ".mysql_affected_rows());
                    } else {
                        $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "No se pudo inactivar el CUPS [{$cupAnterior}] en maestro de procedimientos-exámenes Matrix.");
                    }

                    $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Actualización satisfactoria en maestro de procedimientos-exámenes Matrix.","updated" => "Actualizados: ".mysql_affected_rows());
                    $actualizacion_correcta = true;
                } else {
                    $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error actualizando maestro de procedimientos-exámenes Matrix.","updated"=>"Sin actualizar: ".$anexos_encontrados);
                }
            } else {
                $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"info", "mensaje" => "Código CUPS [{$cupAnterior}] no se encontró anexo en maestro de procedimientos-exámenes Matrix.");
            }
        } else {
            $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error recuperando respuesta código CUPS [{$cupAnterior}] en campo anexo del maestro de procedimientos-exámenes Matrix.");
        }
    }else{
        $resultadoProceso["cli_103"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error consultando código CUPS [{$cupAnterior}] en campo anexo del maestro procedimientos-exámenes Matrix.");
    }
    return $actualizacion_correcta;
}

/**
 * [reactivarCodigosLenguajeAmericas: Se encarga de buscar en la tabla hce_17 registros que se deben insertar (reactivar) en el maestro hce_47
 *                                     y se eliminan de hce_17]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $user_session      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $arr_wbases_dato   [Array con todos los prefijos de tablas en matrix que se pueden usar en los sql]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function reactivarCodigosLenguajeAmericas($conex, $modo_pruebas, $user_session, $arr_wbases_dato, $cupAnterior, $cupNuevo, &$resultadoProceso){
    // Se crean como locales a la función las variables de wbase_dato
    foreach ($arr_wbases_dato as $key_ => $value_) {
        $$key_ = $value_;
    }

    // Aqui se deben ingresar todos los campos que tenga la tabla hce_17, si más adelante se crea un nuevo campo en la tabla,
    // también se debe ingresar a este arreglo, gracias a este arreglo esta función puede consultar todos los campos en hce_17
    // e insertarlos en hce_47, asi no es necesario modificar los encabezados select o insert sino solo el array.
    $arr_campos17 = array(  "Medico"           => "",
                            "Fecha_data"       => "",
                            "Hora_data"        => "",
                            "Codigo"           => "",
                            "Descripcion"      => "",
                            "Servicio"         => "",
                            "Tipoestudio"      => "",
                            "Anatomia"         => "",
                            "Codcups"          => "",
                            "Protocolo"        => "",
                            "Estado"           => "",
                            "Clase"            => "",
                            "NoPos"            => "",
                            "Nuevo"            => "",
                            "Descripcion_unix" => "",
                            "Homologado_hce"   => "",
                            "Seguridad"        => "");

    $labels_select = implode(", h17.", array_keys($arr_campos17));

    // La primera vez que se actualiza un código por otro, es posible que se encuentre un registro donde el campo de cups y el campo propio
    // tengan el mismo código, en ese caso ese registro se va a conservar en la tabla hce_17 y no se borra, los demás registro se borran de
    // hce_17 pero pasan a hce_47, pero si un cups cambia por segunda vez por un cups que nuevamente lo reemplaza, en ese caso si se va a borrar
    // de hce_17 pero pasará tambien a hce_47. (Según Edwin que conocía mejor el proceso, comenta que no habría nungun problema).
    $sel17 = "  SELECT  h17.{$labels_select}, h17.id, h47.Codigo
                FROM    {$wbasedato_HCE}_000017 AS h17
                        LEFT JOIN
                        {$wbasedato_HCE}_000047 AS h47 ON (h47.Codigo = h17.Codigo)
                WHERE   h17.Codcups = '{$cupAnterior}'
                        AND h17.Codigo <> h17.Codcups
                HAVING  h47.Codigo IS NULL"; // Solo se seleccionan los códigos que no existan en hce_47
    if($resultSel_17 = mysql_query($sel17,$conex)) {
        $regs_reactivar = mysql_num_rows($resultSel_17);
        if($regs_reactivar > 0){
            $arr_reactivacion = array();
            $arr_err_reactivacion = array();
            $arr_err_delete17 = array();

            $arr_errs = array("arr_err_reactivacion"=>array(), "arr_err_delete17"=>array());
            while ($row17 = mysql_fetch_assoc($resultSel_17)) {
                foreach ($arr_campos17 as $label => $value) {
                    $arr_campos17[$label] = $row17[$label];
                }

                $arr_campos17["Fecha_data"] = date("Y-m-d");
                $arr_campos17["Hora_data"]  = date("H:i:s");
                $arr_campos17["Estado"]     = "on";
                $arr_campos17["Nuevo"]      = "off";
                $arr_campos17["Seguridad"]  = "C-".$user_session;

                $labels_insert = implode(",",array_keys($arr_campos17));
                $values_insert = implode("','", $arr_campos17);

                $sqlIns = " INSERT INTO {$wbasedato_HCE}_000047
                                        ({$labels_insert})
                            VALUES      ('{$values_insert}')";
                ($modo_pruebas) ? ($sqlIns = " INSERT INTO {$wbasedato_HCE}_000047 () VALUES ()") : '';
                if($resultIns_47 = mysql_query($sqlIns,$conex)) {
                    $sqlDel17 = "   DELETE  FROM {$wbasedato_HCE}_000017
                                            WHERE id = '{$row17["id"]}'";
                    ($modo_pruebas) ? ($sqlDel17 = "DELETE  FROM {$wbasedato_HCE}_000017e WHERE 1<>1") : '';
                    if($resultDel_17 = mysql_query($sqlDel17,$conex)) {
                        $arr_reactivacion[] = $arr_campos17["Codigo"]."-".utf8_encode($arr_campos17["Descripcion"]);
                    } else {
                        $arr_err_delete17[] = $arr_campos17["Codigo"]."-".utf8_encode($arr_campos17["Descripcion"]);
                        $arr_errs["arr_err_delete17"][] = $sqlDel17.": ".mysql_error();
                    }
                } else {
                    $arr_err_reactivacion[] = $arr_campos17["Codigo"]."-".utf8_encode($arr_campos17["Descripcion"]);
                    $arr_errs["arr_err_reactivacion"][] = $sqlIns.": ".mysql_error();
                }
            }

            if(count($arr_reactivacion) > 0){
                    $impld = implode("], [", $arr_reactivacion);
                    $resultadoProceso["leng_americas"]["mensajes"][] = array("err_sql"=>"","tip_class"=>"success", "mensaje" => "Se reactivaron los códigos <small>[{$impld}]</small> asociados al nuevo CUPS [{$cupNuevo}] en el <b>Lenguaje américas</b>.");
            }
            if(count($arr_err_delete17) > 0){
                    $impld = implode("], [", $arr_err_delete17);
                    $resultadoProceso["leng_americas"]["mensajes"][] = array("err_sql"=>htmlentities(implode("|", $arr_errs["arr_err_delete17"]), ENT_QUOTES),"tip_class"=>"danger", "mensaje" => "Error eliminando los códigos <small>[{$impld}]</small> asociados al nuevo CUPS [{$cupNuevo}] en reactivación del <b>Lenguaje américas</b>.");
            }
            if(count($arr_err_reactivacion) > 0){
                    $impld = implode("], [", $arr_err_reactivacion);
                    // print_r(utf8_decode(implode("|", $arr_errs["arr_err_reactivacion"])));
                    $resultadoProceso["leng_americas"]["mensajes"][] = array("err_sql"=>htmlentities(implode("|", $arr_errs["arr_err_reactivacion"]), ENT_QUOTES),"tip_class"=>"danger", "mensaje" => "Error reactivando los códigos <small>[{$impld}]</small> asociados al nuevo CUPS [{$cupNuevo}] en el <b>Lenguaje américas</b>.");
                    // $resultadoProceso["leng_americas"]["mensajes"][] = array("err_sql"=>"???????","tip_class"=>"success", "mensaje" => "Se reactivaron los códigos <small></small> asociados al nuevo CUPS [{$cupNuevo}] en el <b>Lenguaje américas</b>.");
            }
        }
    }else{
        $resultadoProceso["leng_americas"]["mensajes"][] = array("err_sql"=>htmlentities($sel17, ENT_QUOTES)." >> ".mysql_error(),"tip_class"=>"danger", "mensaje" => "Error buscando código CUPS [{$cupNuevo}] para reactivarlo en el <b>Lenguaje américas</b>.");
    }
    // select on as estado, off as cup_nuevo, *
    // from 17
    // where cups = viejo  y codigo != cups
    // if( si >0 ){
    //     insert 47 de select *
    //     delete 17 where id = select id
    // }
}

/**
 * [actualizarLenguajeAmericas: El objetivo de esta función es buscar en las tablas hce_17 y hce_47 que componen el lenguaje americas
 *                                 para identificar registros que tengan el código cups anterior y sean reemplazados por el cups nuevo,
 *                                 además usa otra función encargada de reactivas códigos del lenguaje américas que pudieron ser desactivados.]
 * @param  [type] $conex             [Id conexión a base de datos matrix]
 * @param  [type] $user_session      [Código del usuario matrix que está realizando la acción]
 * @param  [type] $arr_wbases_dato   [Array con todos los prefijos de tablas en matrix que se pueden usar en los sql]
 * @param  [type] $cupAnterior       [Código del cups a inactivar]
 * @param  [type] $cupNuevo          [Código del cups que reemplaza el inactivo]
 * @param  [type] &$resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @return [type]                    [description]
 */
function actualizarLenguajeAmericas($conex, $modo_pruebas, $user_session, $arr_wbases_dato, $cupAnterior, $cupNuevo, &$resultadoProceso) {
    // Se crean como locales a la función las variables de wbase_dato
    foreach ($arr_wbases_dato as $key_ => $value_) {
        $$key_ = $value_;
    }
    $actualizacion_correcta = true;

    $resultadoProceso["leng_americas"] = array("nombre"=> "[Matrix] Lenguaje Américas", "mensajes" => array());

    /*select 17 union 47 cups = viejo
    u tabal1,tabla2 set t1cup = '', t2.cup = '' where t1.cup = viejo and t2.cup viejo
    update 17 y 47 cups viejo por cup_nuevo

    select on as estado, off as cup_nuevo, *
    from 17
    where cups = viejo  y codigo != cups

    if( si >0 ){
        insert 47 de select *

        delete 17 where id = select id

    }*/
    $sel17_47 = "   SELECT  h17.id
                    FROM    {$wbasedato_HCE}_000017 AS h17
                    WHERE   h17.Codcups = '{$cupAnterior}'

                    UNION

                    SELECT  h47.id
                    FROM    {$wbasedato_HCE}_000047 AS h47
                    WHERE   h47.Codcups = '{$cupAnterior}'";

    if($resultSel_1747 = mysql_query($sel17_47,$conex)) {
        $regs_leng_americas = mysql_num_rows($resultSel_1747);
        if($regs_leng_americas > 0){
            // Intentar reactivar los códigos de hce_17 en el lenguaje américas hce_47.
            reactivarCodigosLenguajeAmericas($conex, $modo_pruebas, $user_session, $arr_wbases_dato, $cupAnterior, $cupNuevo, $resultadoProceso);

            $sqlUpd17 = "UPDATE {$wbasedato_HCE}_000017 SET Codcups = '{$cupNuevo}' WHERE Codcups = '$cupAnterior'";
            ($modo_pruebas) ? ($sqlUpd17 = "UPDATE {$wbasedato_HCE}_000017 SET Codcups = '{$cupNuevo}' WHERE 1<>1") : '';
            if($resultSel_17 = mysql_query($sqlUpd17,$conex)) {
                $resultadoProceso["leng_americas"]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Actualización satisfactoria en maestro de estudios y ayudas diagnósticas en Matrix.","updated" => "Actualizados: ".mysql_affected_rows());
            } else {
                $resultadoProceso["leng_americas"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error actualizando maestro de estudios y ayudas diagnósticas en Matrix.<br>","updated"=>"Sin actualizar: ".$regs_leng_americas);
                // $actualizacion_correcta = true;
            }

            $sqlUpd47 = "UPDATE {$wbasedato_HCE}_000047 SET Codcups = '{$cupNuevo}' WHERE Codcups = '$cupAnterior'";
            ($modo_pruebas) ? ($sqlUpd47 = "UPDATE {$wbasedato_HCE}_000047 SET Codcups = '{$cupNuevo}' WHERE 1<>1") : '';
            if($resultSel_47 = mysql_query($sqlUpd47,$conex)) {
                $resultadoProceso["leng_americas"]["mensajes"][] = array("tip_class"=>"success", "mensaje" => "Actualización satisfactoria en <b>Maestro de exámenes lenguaje américas</b>.","updated" => "Actualizados: ".mysql_affected_rows());
            } else {
                $resultadoProceso["leng_americas"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error actualizando <b>Maestro de exámenes lenguaje américas</b> en Matrix.","updated"=>"Sin actualizar: ".$regs_leng_americas);
                // $actualizacion_correcta = true;
            }

        } else {
            $resultadoProceso["leng_americas"]["mensajes"][] = array("tip_class"=>"warning", "mensaje" => "Se consultó código CUPS [{$cupAnterior}] en el <b>Lenguaje américas</b> pero el CUPS no existe en ese maestro.");
        }
    }else{
        $resultadoProceso["leng_americas"]["mensajes"][] = array("tip_class"=>"danger", "mensaje" => "Error consultando código CUPS [{$cupAnterior}] en el <b>Lenguaje américas</b>.");
    }
    // print_r($resultadoProceso);
	
	// ------------------------------------------------------------------------------------------------
	// --> 2019-07-10: Jerson trujillo, actualizar otras tablas donde tambien tenga relación el cups.
	// ------------------------------------------------------------------------------------------------
	// cliame_000114 = Paquetes
	$sql114 = "UPDATE ".$wbasedato_cliame."_000114 SET Paqdetpro = '".$cupNuevo."' WHERE Paqdetpro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql114, "Paquetes", $resultadoProceso);
	
	//	cliame_000155 = encabezado de politicas
	$sql155 = "UPDATE ".$wbasedato_cliame."_000155 SET Polpro = '".$cupNuevo."' WHERE Polpro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql155, "encabezado de politicas (Polpro)", $resultadoProceso);
	
	$sql155Polprn = "UPDATE ".$wbasedato_cliame."_000155 SET Polprn = '".$cupNuevo."' WHERE Polprn = '".$cupAnterior."' ";
	ejecQuery($conex, $sql155Polprn, "encabezado de politicas (Polprn)", $resultadoProceso);
	
	$sql155Polprf = "UPDATE ".$wbasedato_cliame."_000155 SET Polprf = '".$cupNuevo."' WHERE Polprf = '".$cupAnterior."' ";
	ejecQuery($conex, $sql155Polprf, "encabezado de politicas (Polprf)", $resultadoProceso);
	
	$sql155Polprd = "UPDATE ".$wbasedato_cliame."_000155 SET Polprd = '".$cupNuevo."' WHERE Polprd = '".$cupAnterior."' ";
	ejecQuery($conex, $sql155Polprd, "encabezado de politicas (Polprd)", $resultadoProceso);	
	
	$sql155Polcad = "UPDATE ".$wbasedato_cliame."_000155 SET Polcad = REPLACE(Polcad, '".$cupAnterior."', '".$cupNuevo."') WHERE Polcad LIKE '%".$cupAnterior."%' ";
	ejecQuery($conex, $sql155Polcad, "encabezado de politicas (Polcad)", $resultadoProceso);	
	
	//	cliame_000156 = Politicas, restricciones de dias
	$sql156 = "UPDATE ".$wbasedato_cliame."_000156 SET Rdicpr = '".$cupNuevo."' WHERE Rdicpr = '".$cupAnterior."' ";
	ejecQuery($conex, $sql156, "Politicas, restricciones de dias (Rdicpr)", $resultadoProceso);
	
	//	cliame_000157 = Politicas, restricciones de no facturables
	$sql157 = "UPDATE ".$wbasedato_cliame."_000157 SET Rcacpc = '".$cupNuevo."' WHERE Rcacpc = '".$cupAnterior."' ";
	ejecQuery($conex, $sql157, "Politicas, restricciones de no facturables (Rcacpc)", $resultadoProceso);
	
	$sql157Rcacpa = "UPDATE ".$wbasedato_cliame."_000157 SET Rcacpa = REPLACE(Rcacpa, '".$cupAnterior."', '".$cupNuevo."') WHERE Rcacpa LIKE '%".$cupAnterior."%' ";
	ejecQuery($conex, $sql157Rcacpa, "Politicas, restricciones de no facturables (Rcacpa)", $resultadoProceso);
	
	//	cliame_000158 = Politicas, restricciones de rango de cantidades
	$sql158 = "UPDATE ".$wbasedato_cliame."_000158 SET Rrhcpr = '".$cupNuevo."' WHERE Rrhcpr = '".$cupAnterior."' ";
	ejecQuery($conex, $sql158, "Politicas, restricciones de rango de cantidades (Rrhcpr)", $resultadoProceso);
	
	//	cliame_000180 = Plantilla Cirugia - Encabezado Politicas Cirugia
	$sql180 = "UPDATE ".$wbasedato_cliame."_000180 SET Encpro = REPLACE(Encpro, '".$cupAnterior."', '".$cupNuevo."') WHERE Encpro LIKE '%".$cupAnterior."%' ";
	ejecQuery($conex, $sql180, "Plantilla Cirugia - Encabezado Politicas Cirugia", $resultadoProceso);
	
	//	cliame_000181 = Plantilla Cirugia - Cobro por horas
	$sql181 = "UPDATE ".$wbasedato_cliame."_000181 SET Cphpro = '".$cupNuevo."' WHERE Cphpro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql181, "Plantilla Cirugia - Cobro por horas", $resultadoProceso);
	
	//	cliame_000182 = Plantilla Cirugia - Cobro por tipo anestesia
	$sql182 = "UPDATE ".$wbasedato_cliame."_000182 SET Anepro = '".$cupNuevo."' WHERE Anepro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql182, "Plantilla Cirugia - Cobro por tipo anestesia", $resultadoProceso);
	
	//	cliame_000183 = Plantilla Cirugia - Cobro por tiempo de uso
	$sql183 = "UPDATE ".$wbasedato_cliame."_000183 SET Usopro = '".$cupNuevo."' WHERE Usopro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql183, "Plantilla Cirugia - Cobro por tiempo de uso", $resultadoProceso);
	
	//	cliame_000186 = Cirugias multiples - Bases de liquidacion
	$sql186 = "UPDATE ".$wbasedato_cliame."_000186 SET Blqpro = '".$cupNuevo."' WHERE Blqpro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql186, "Cirugias multiples - Bases de liquidacion", $resultadoProceso);
	
	//	cliame_000192 = Homologacion a unix
	$sql192 = "UPDATE ".$wbasedato_cliame."_000192 SET Hompom = '".$cupNuevo."' WHERE Hompom = '".$cupAnterior."' ";
	ejecQuery($conex, $sql192, "Homologacion a unix (Hompom)", $resultadoProceso);
	
	$sql192Hompos = "UPDATE ".$wbasedato_cliame."_000192 SET Hompos = '".$cupNuevo."' WHERE Hompos = '".$cupAnterior."' ";
	ejecQuery($conex, $sql192Hompos, "Homologacion a unix (Hompos)", $resultadoProceso);
	
	//	cliame_000225 = Cirugias multiples - Procedimientos relacionados
	$sql225 = "UPDATE ".$wbasedato_cliame."_000225 SET Rnfpro = '".$cupNuevo."' WHERE Rnfpro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql225, "Cirugias multiples - Procedimientos relacionados (Rnfpro)", $resultadoProceso);
	
	$sql225_2 = "UPDATE ".$wbasedato_cliame."_000225 SET Rnfrel = REPLACE(Rnfrel, '".$cupAnterior."', '".$cupNuevo."') WHERE Rnfrel LIKE '%".$cupAnterior."%' ";
	ejecQuery($conex, $sql225_2, "Cirugias multiples - Procedimientos relacionados (Rnfrel)", $resultadoProceso);
	
	//	cliame_000226 = Políticas Quirúrgicas
	$sql226 = "UPDATE ".$wbasedato_cliame."_000226 SET Pcipnf = '".$cupNuevo."' WHERE Pcipnf = '".$cupAnterior."' ";
	ejecQuery($conex, $sql226, "Políticas Quirúrgicas (Pcipnf)", $resultadoProceso);
	
	$sql226 = "UPDATE ".$wbasedato_cliame."_000226 SET Pciccr = '".$cupNuevo."' WHERE Pciccr = '".$cupAnterior."' ";
	ejecQuery($conex, $sql226, "Políticas Quirúrgicas (Pciccr)", $resultadoProceso);
	
	$sql226 = "UPDATE ".$wbasedato_cliame."_000226 SET Pciccp = '".$cupNuevo."' WHERE Pciccp = '".$cupAnterior."' ";
	ejecQuery($conex, $sql226, "Políticas Quirúrgicas (Pciccp)", $resultadoProceso);
	
	//	cliame_000242 = Excepción tarifaria ERP - terceros
	$sql242 = "UPDATE ".$wbasedato_cliame."_000242 SET Extpro = '".$cupNuevo."' WHERE Extpro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql242, "Excepción tarifaria ERP - terceros", $resultadoProceso);
	
	//	cliame_000249 = Procedimientos NO POS por empresa
	$sql249 = "UPDATE ".$wbasedato_cliame."_000249 SET Pnppro = '".$cupNuevo."' WHERE Pnppro = '".$cupAnterior."' ";
	ejecQuery($conex, $sql249, "Procedimientos NO POS por empresa", $resultadoProceso);
	
	//	cliame_000257 = Excepciones monitor autorizaciones
	$sql257 = "UPDATE ".$wbasedato_cliame."_000257 SET Emacod = '".$cupNuevo."' WHERE Emacod = '".$cupAnterior."' AND Ematpr = 'on' ";
	ejecQuery($conex, $sql257, "Excepciones monitor autorizaciones", $resultadoProceso);
	
	//	cliame_000260 = Configuracion de Autorizaciones de Procedimientos
	$sql260 = "UPDATE ".$wbasedato_cliame."_000260 SET Paucop = '".$cupNuevo."' WHERE Paucop = '".$cupAnterior."' ";
	ejecQuery($conex, $sql260, "Configuracion de Autorizaciones de Procedimientos", $resultadoProceso);

	
    return $actualizacion_correcta;
}

function ejecQuery($conex, $sqlQuery, $desTabla, &$resultadoProceso){
	
	// if(false)
	if($reSqlQuery = mysql_query($sqlQuery,$conex))
		$resultadoProceso["leng_americas"]["mensajes"][] = array("SQL"=>$sqlQuery, "tip_class"=>"success", "mensaje" => "Actualización satisfactoria en <b>".$desTabla."</b>.","updated" => "Actualizados: ".mysql_affected_rows());
	else
		$resultadoProceso["leng_americas"]["mensajes"][] = array("SQL"=>$sqlQuery, "tip_class"=>"danger", "mensaje" => "Error actualizando <b>".$desTabla."</b> en Matrix.","updated"=>"Sin actualizar.. ");
}

/**
 * [actualizarUnixMatrixCUPS: Esta función se encarga de desplegar la actualización del código CUPS en unix y matrix, actualizando el código inactivo por el nuevo código que lo reemplaza]
 * @param  [type] $conex           [Id conexión a base de datos matrix]
 * @param  [type] $conexUnix       [Id conexión a base de datos informix en unix => facturacion]
 * @param  [type] $arr_wbases_dato [Prefijos de bases de datos en matrix p.e. cliame, hce, ...]
 * @param  [type] $cupAnterior     [Código del cups a inactivar]
 * @param  [type] $cupNuevo        [Código del cups que reemplaza el inactivo]
 * @param  [type] &$data           [Arreglo para la respuesta JSON para la vista cliente]
 * @return [type]                  [description]
 */
function actualizarUnixMatrixCUPS($conex, $conexUnix, $modo_pruebas, $arr_wbases_dato, $cupAnterior, $cupNuevo, &$data)
{
    // Se crean como locales a la función las variables de wbase_dato
    foreach ($arr_wbases_dato as $key_ => $value_) {
        $$key_ = $value_;
    }
    $resultadoProceso = array();
    $nuevoCupActivo = false;

    // Buscar y actualizar en procedimientos, este arreglo ayuda a determinar qué tabla usar dentro de la función actualizarMaestroUnix
    $defaultMaestro = array("sufijo_prefijo"=>"pro", "nombre_maestro"=>"procedimiento");
    $actualizar_matrix_pro = actualizarMaestroUnix($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $resultadoProceso, $nuevoCupActivo);

    // Buscar y actualizar en exámenes
    $defaultMaestro = array("sufijo_prefijo"=>"exa", "nombre_maestro"=>"examen");
    $actualizar_matrix_exa = actualizarMaestroUnix($conex, $conexUnix, $modo_pruebas, $user_session, $cupAnterior, $cupNuevo, $defaultMaestro, $resultadoProceso, $nuevoCupActivo);

    // Si encontró y reemplazó el anterior CUPS en procedimientos o exámenes, entonces se intenta hacer lo mismo en los maestros de Matrix.
    // if($actualizar_matrix_pro || actualizar_matrix_exa)
    {
        // Esta operación se encargará de hacerla el cron que actualiza todos los procedimientos en matrix desde unix. Esta función solo actualiza cliame_103
        // pero el cron si actualiza todas las tablas que relacionan el cups.
        // $actualizar_matrix = actualizarMaestroMatrix($conex, $modo_pruebas $user_session, $wbasedato_cliame, $cupAnterior, $cupNuevo, $resultadoProceso);

        actualizarLenguajeAmericas($conex, $modo_pruebas, $user_session, $arr_wbases_dato, $cupAnterior, $cupNuevo, $resultadoProceso);
    }

    $data["resultadoProceso"] = $resultadoProceso;
}

/**
 * [guardarLogActualizacion description]
 * @param  [type] $conex            [Id conexión a base de datos matrix]
 * @param  [type] $wbasedato_cliame [Prefijo de base de datos para maestro matrix]
 * @param  [type] $user_session     [Código del usuario matrix que está realizando la acción]
 * @param  [type] $resultadoProceso [Parámetro de repuesta de eventos fallidos o satisfactorios en el proceso]
 * @param  [type] $cupAnterior      [Código del cups a inactivar]
 * @param  [type] $cupNuevo         [Código del cups que reemplaza el inactivo]
 * @param  [type] &$data            [Arreglo para la respuesta JSON para la vista cliente]
 * @return [type]                   [description]
 */
function guardarLogActualizacion($conex, $wbasedato_cliame, $user_session, $resultadoProceso, $cupAnterior, $cupNuevo, &$data){
    $Lacfec = date("Y-m-d H:i:s");
    $log_guardado = true;
    $fecha_actual = date("Y-m-d");
    $hora_actual = date("H:i:s");

    $resultadoProceso = json_encode($resultadoProceso);
    $resultadoProceso = str_replace("'", "", $resultadoProceso);
    $sqlLog = " INSERT INTO {$wbasedato_cliame}_000294
                            (Medico,Fecha_data,Hora_data,Lacant, Lacnue, Lacfec, Lacusu, Lactlg, Seguridad)
                VALUES      ('{$wbasedato_cliame}','{$fecha_actual}','{$hora_actual}','{$cupAnterior}', '{$cupNuevo}', '{$Lacfec}', '{$user_session}', '{$resultadoProceso}', 'C-{$user_session}')";
    if($resultLog = mysql_query($sqlLog,$conex)) {
    } else {
        $log_guardado = false;
        $data["error_log_save"] = $sqlLog." >> ".mysql_error();
    }

    $sqlLog = " SELECT Lactlg from {$wbasedato_cliame}_000294
                order by id desc";
    $resultLog = mysql_query($sqlLog,$conex);
    $rw = mysql_fetch_assoc($resultLog);

    $data["log_guardado_rw"] = json_decode($rw["Lactlg"]);

    $data["log_guardado"] = $log_guardado;
}

/********************** FIN DE FUNCIONES *************************/

/**
 * ********************************************************************************************************************************************************
 * Lógica, procesos de los llamados AJAX de todo el programa - INICIO DEL PROGRAMA
 * ********************************************************************************************************************************************************
 */
if(isset($accion))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';
    switch($accion)
    {
        case 'carga_maestro_cups':
            $data = array();
            $data["results"] = obtener_array_procedimientos_buscar($conex, $wemp_pmla, $wbasedato, $search, $maestro_cups_root);
        break;

        case 'leer_archivo_plano':
            $arrFile = $_FILES["fileSelector"];

            $arr_parejasCups = array();
            $arr_errores_formato = array();

            if(isset($arrFile["name"]) && $arrFile["name"] != ""){
                $arr_maestroCups = obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato_cliame, $maestro_cups_root);
                $rutaplanos = "../../planos/actualizar_cups/";
                $ruta_provisional = $arrFile["tmp_name"];
                $newRuta = $rutaplanos . $arrFile["name"];

                if(is_dir($rutaplanos)){ }
                else { mkdir($rutaplanos,0777); }

                if (file_exists($newRuta)) {
                    unlink($newRuta);
                }

                if(move_uploaded_file($ruta_provisional, $newRuta)){
                    if($myfile = fopen($newRuta, "r")){
                        // Output one line until end-of-file
                        $num_fila = 1;
                        while(!feof($myfile)) {
                            $row_file = trim(fgets($myfile));
                            // $row_file = str_replace("\n", "", $row_file);
                            // $row_file = str_replace("\t", "", $row_file);
                            $caracter_explode = "";
                            if(strpos($row_file, ",")){
                                $caracter_explode = ",";
                            } elseif(strpos($row_file, ";")) {
                                $caracter_explode = ";";
                            } else {
                                $arr_errores_formato[] = "Línea [{$num_fila}]>> Error caracter separador (permitidos [,] [;]): [".$row_file."]";
                            }

                            if($caracter_explode != "") {
                                $explRow = explode($caracter_explode, $row_file);
                                if(count($explRow) == 2){
                                    $explRow[0] = trim($explRow[0]);
                                    $explRow[1] = trim($explRow[1]);
                                    $idx_row = trim(implode("_", $explRow));

                                    //Validar que cada cups existe o no en el maestro de matrix cliame_000103. Si
                                    $anterior_NoExiste = (!array_key_exists($explRow[0], $arr_maestroCups)) ? $explRow[0]: "";
                                    $nuevo_NoExiste    = (!array_key_exists($explRow[1], $arr_maestroCups)) ? $explRow[1]: "";

                                    if($anterior_NoExiste == '' && $nuevo_NoExiste == ''){
                                        if(!array_key_exists($idx_row, $arr_parejasCups)){
                                            // echo "<pre>".print_r($idx_row,true)."</pre>";
                                            if($explRow[0] != $explRow[1]){
                                                $arr_parejasCups[$idx_row] = implode(",",$explRow);
                                            } else {
                                                $arr_errores_formato[] = "Línea [{$num_fila}]>> Pareja de códigos son iguales: [".implode("][", $explRow)."]";
                                            }
                                        } else {
                                            $arr_errores_formato[] = "Línea [{$num_fila}]>> Pareja de códigos se repiten en este mismo archivo: [".implode("][", $explRow)."]";
                                        }
                                    } else {
                                        if($anterior_NoExiste != ''){
                                            $arr_errores_formato[] = "Línea [{$num_fila}]>> Código [{$anterior_NoExiste}] NO EXISTE en maestro exámenes y procedimientos de Matrix";
                                        }
                                        if($nuevo_NoExiste != ''){
                                            $arr_errores_formato[] = "Línea [{$num_fila}]>> Código [{$nuevo_NoExiste}] NO EXISTE en maestro exámenes y procedimientos de Matrix";
                                        }
                                    }
                                } else {
                                    $arr_errores_formato[] = "Línea [{$num_fila}]>> Columnas inválidas: [".implode("][", $explRow)."]";
                                }
                            }
                            $num_fila++;
                        }
                        fclose($myfile);
                    } else {
                        $data["error"] = 1;
                        $data["Mensaje"] = "No se puede leer el archivo.";
                    }
                } else {
                    $data["error"] = 1;
                    $data["Mensaje"] = "No se pudo cargar el archivo.";
                }
            } else {
                $data["error"] = 1;
                $data["mensaje"] = 'No se pudo leer el archivo.';
            }

            // echo "<pre>".print_r($arr_parejasCups,true)."</pre>";
            $data["arr_parejasCups"] = $arr_parejasCups;
            $data["arr_errores_formato"] = $arr_errores_formato;
            if($data["error"] == 0 && count($arr_errores_formato) > 0){
                $data["error"] = 2;
                $data["mensaje"] = "Errores en los datos del archivo";
            }
        break;

        case 'reemplazar_cups':
            $modo_pruebas = (isset($modo_pruebas) && $modo_pruebas == 'on') ? true: false; // No realiza updates, inserts, cuando esta activo.
            // sleep(4);
            include_once("root/comun.php");
            $data["resultadoProceso"] = array();
            $conexUnix = '';
            $conectado_a_unix = false;
            $conectado_a_unix = conexionUnixFn($hay_unix, $conexUnix, $data);
            $arr_wbases_dato  = array(  "wbasedato_HCE"    => $wbasedato_HCE,
                                        "wbasedato_movhos" => $wbasedato_movhos,
                                        "wbasedato_cliame" => $wbasedato_cliame,
                                        "user_session"     => $user_session);
            if($conectado_a_unix){
                actualizarUnixMatrixCUPS($conex, $conexUnix, $modo_pruebas, $arr_wbases_dato, $cupAnterior, $cupNuevo, $data);
            }

            if($conectado_a_unix) {
                odbc_close($conexUnix);
                odbc_close_all();
            }
			// echo "<pre>";
			// print_r($data["resultadoProceso"]);
			// echo "</pre>";
			
            guardarLogActualizacion($conex, $wbasedato_cliame, $user_session, $data["resultadoProceso"], $cupAnterior, $cupNuevo, $data);
        break;

        case 'actualizar_cups_maestro_matrix':
                $data['insertados'] = 0;
                $data['actualizados'] = 0;
                include_once("root/comun.php");
                actualizarRoot12Cups($conex, $wemp_pmla, $wbasedato, $hay_unix, $user_session, $data);
            break;

        default :
                $data['mensaje'] = $no_exec_sub;
                $data['error'] = 1;
        break;
    }
    echo json_encode($data);
    return;
}

include_once("root/comun.php");
$wbasedato_HCE    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$modo_pruebas = (isset($modo_pruebas)) ? $modo_pruebas: '';
?>
<html lang="es-ES">
<head>
    <title>Actualizaci&oacute;n CUPS</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery.min.js"></script>

    <script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>

    <script src="../../../include/root/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

    <link type="text/css" href="../../../include/root/select2/select2.min.css" rel="stylesheet"/>
    <script type='text/javascript' src='../../../include/root/select2/select2.min.js'></script>

    <!-- <link type="text/css" href="../../../include/root/matrix.css" rel="stylesheet"/> -->

    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;

        var total_parejas_cups        = 0;
        var arrMaestroCups            = new Array();
        var arr_parejasCupsMasivo     = new Array();
        var arr_tipMensajeCupsMasivo  = new Array();
        var arr_tipMensajesPorParCups = new Array();

        var height_window;
        $(function(){
            height_window = ($(window).height()-250);
            // height_window = ($(window).height() - 100);
            // $('#scrollbox').css('max-height', height+'px');
        });

        function isValid(str) {
            return !/[~`´._!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
        }

        $(document).ready( function ()
        {
            $("#accordionDatosCups, #accordionCargaArchivoCups").accordion({
                collapsible: true,
                heightStyle: "content"
            });

            iniciarAccionesBotones();
            iniciarSelects();
        });

        /**
         * [jAlert Simula el JAlert usado en las anteriores versiones de JQuery]
         * @param  {[type]} html   [description]
         * @param  {[type]} titulo [description]
         * @return {[type]}        [description]
         */
        function jAlert(html,titulo){
            $("#jAlert").find(".modal-header").removeClass("bg-danger");

            $("#jAlert").find("#alertModalLabel").html(titulo);
            $("#jAlert").find(".modal-body").html(html);
            var bg = (titulo.toLowerCase() == 'alerta') ? 'bg-danger': 'bg-primary';
            $("#jAlert").find(".modal-header").addClass(bg);
            $("#jAlert").modal({ backdrop: 'static',
                                 keyboard: false}).css("z-index", 2030);
            if((titulo.toLowerCase() == 'alerta')) { $("#jAlert").css("z-index", 2030); }
        }

        /**
         * [mensajeFailAlert: Muestra un mensaje en pantalla cuando se generó un error en la respuesta ajax]
         * @param  {[type]} mensaje     [description]
         * @param  {[type]} xhr         [description]
         * @param  {[type]} textStatus  [description]
         * @param  {[type]} errorThrown [description]
         * @return {[type]}             [description]
         */
        function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
        {
            var msj_extra = '';
            msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
            $(".alert").alert("Mensaje");
            // jAlert($("#failJquery").val()+msj_extra, "Mensaje");
            jAlert($("#failJquery").val()+msj_extra, "Alerta");
            $("#div_error_interno").html(xhr.responseText);
            // console.log(xhr);
            // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
            fnModalLoading_Cerrar();
            // $(".bloquear_todo").removeAttr("disabled");
        }

        /**
         * [validarFormulario: Se encarga de validar que los campos de cups a inactivar y cups que reemplaza no estén vacíos o sean iguales]
         * @return {[type]} [description]
         */
        function validarFormulario() {
            var novalido = false;

            // $(".form-group").removeClass('has-error');
            $("#needs-validation-cups").find("span.bg-warning").removeClass("bg-warning");
            $("#needs-validation-cups").find(".form-control").each(function(){
                if($(this).val() === null || $(this).val().replace(/ /gi, "") == '') {
                    var id_spn = $(this).attr('id');
                    // console.log(id_spn);
                    // console.log($("#needs-validation-cups").find("span[id=select2-"+id_spn+"-container]"));
                    novalido = true;
                    $("#needs-validation-cups").find("span[id=select2-"+id_spn+"-container]").addClass("bg-warning");
                    // select2-cupAnterior-container
                }
            });

            if(!novalido && $("#cupAnterior").val() == $("#cupNuevo").val()){
                novalido = true;
                jAlert('Ambos códigos deben ser diferentes','Alerta');
            }

            var cupAnterior = $("#cupAnterior").val();
            var cupNuevo = $("#cupNuevo").val();

            if(novalido){
                // $(".form-group").find(".select2-offscreen").addClass('has-error');
            } else {
                reemplazarCups(cupAnterior, cupNuevo, false);
            }
        }

        /**
         * [actualizarMatrixCron: Función encargada de hacer un llamado mediante ajax al cron que realiza la actualización de maestros de unix a matrix]
         * @param  {[type]} btn [description]
         * @return {[type]}     [description]
         */
        function actualizarMatrixCron(btn) {
            var obJson           = {};
            obJson['tiempoEjec'] = 'TARIFAS';
            obJson['consultaAjax'] = '';

            // $(".bloquear_todo").attr("disabled","disabled");
            // fnModalLoading();

            $.post("../../root/procesos/migrarMaestrosUnix.php", obJson,
                function(data){
                    $("#updateModalMatrix").find(".modal-body").html(data);
                    $("#updateModalMatrix").find(".modal-body").append('<br><br><span class="text-success">Termin&oacute;</span>');
            }).done(function(){
                    btn.button('reset');
                    $("#btnCancelUpdateMatrix").removeClass("disabled");
                    $("#btnCancelUpdateMatrix").removeAttr("disabled");
                    clearInterval(intervalSetArr);
                    // fnModalLoading_Cerrar();
                    //
            }).fail(function(xhr, textStatus, errorThrown) {
                clearInterval(intervalSetArr);
                btn.button('reset');
                $("#btnCancelUpdateMatrix").removeClass("disabled");
                $("#btnCancelUpdateMatrix").removeAttr("disabled");
                mensajeFailAlert('', xhr, textStatus, errorThrown);
            });
        }

        /**
         * [actualizarMatrixCron: Función encargada de hacer un llamado mediante ajax al cron que realiza la actualización de maestros de unix a matrix]
         * @param  {[type]} btn [description]
         * @return {[type]}     [description]
         */
        function actualizarMatrixMaestroCups(btn) {
            var obJson          = parametrosComunes();
            obJson['accion']    = 'actualizar_cups_maestro_matrix';
            obJson['wbasedato'] = $("#wbasedato_cliame").val();

            // $(".bloquear_todo").attr("disabled","disabled");
            // fnModalLoading();

            $.post("actualizacion_cups.php", obJson,
                function(data){
                    var html_proceso = '<ul><li class="text-info">Actualizados: '+data.actualizados+'</li> <li class="text-warning">Nuevos: '+data.insertados+'</li><ul>';
                    $("#updateModalActualizaMatrixMaestro").find(".modal-body").html("");
                    $("#updateModalActualizaMatrixMaestro").find(".modal-body").append('<br><br><span class="text-success">Termin&oacute;</span>');
                    $("#updateModalActualizaMatrixMaestro").find(".modal-body").append(html_proceso);
            },'json').done(function(){
                    btn.button('reset');
                    $("#btnCancelUpdateMatrixMaestro").removeClass("disabled");
                    $("#btnCancelUpdateMatrixMaestro").removeAttr("disabled");
                    clearInterval(intervalSetArr);
                    // fnModalLoading_Cerrar();
                    //
            }).fail(function(xhr, textStatus, errorThrown) {
                clearInterval(intervalSetArr);
                btn.button('reset');
                $("#btnCancelUpdateMatrixMaestro").removeClass("disabled");
                $("#btnCancelUpdateMatrixMaestro").removeAttr("disabled");
                mensajeFailAlert('', xhr, textStatus, errorThrown);
            });
        }

        /**
         * [iniciarSelects: configuración e inicialización de campos select para elegir el cups a desactivar y el cups de reemplazo.
         *                  Esta misma función realiza la búsqueda de códigos cuando se escribe en los campos de cups.]
         * @return {[type]} [description]
         */
        function iniciarSelects(){
            var language = new Object ();
            language =  {
                            inputTooShort: function(args) {
                            // args.minimum is the minimum required length
                            // args.input is the user-typed text
                            // return "Type more stuff";
                            // console.log(args);
                            var b=args.minimum-args.input.length
                            return "Por favor ingrese "+b+" o mas caracteres";
                            },
                            // inputTooLong: function(args) {
                            //     // console.log(">>");
                            //     // console.log(args);
                            // // args.maximum is the maximum allowed length
                            // // args.input is the user-typed text
                            // // return "You typed too much";
                            // return "You typed too much";
                            // },
                            errorLoading: function() {
                            return "Error cargando resultados";
                            },
                            loadingMore: function() {
                            return "Cargando mas resultados";
                            },
                            noResults: function() {
                            return "No hay resultados";
                            },
                            searching: function() {
                            return "Buscando...";
                            },
                            maximumSelected: function(args) {
                            // args.maximum is the maximum number of items the user may select
                            return "Error cargando resultados";
                            }
                        };

            var minimumInputLength = 5;
            var _url = 'actualizacion_cups.php';

            $('#cupAnterior').select2({
                placeholder: 'Seleccione CUPS a inactivar',
                allowClear: true,
                minimumInputLength: minimumInputLength,
                ajax: {
                    url: _url,
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search    : params.term,
                            type      : 'public',
                            accion    : 'carga_maestro_cups',
                            wbasedato : $("#wbasedato_cliame").val(),
                            wemp_pmla : $("#wemp_pmla").val()
                        }
                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    }
                },
                language: language
            });

            $('#cupNuevo').select2({
                placeholder: 'Seleccione nuevo CUPS de reemplazo',
                allowClear: true,
                minimumInputLength: minimumInputLength,
                ajax: {
                    url: _url,
                    dataType: 'json',
                    data: function (params) {
                        var query = {
                            search    : params.term,
                            type      : 'public',
                            accion    : 'carga_maestro_cups',
                            wbasedato : $("#wbasedato_cliame").val(),
                            wemp_pmla : $("#wemp_pmla").val()
                        }
                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    }
                },
                language: language
            });
        }

        /**
         * [cargarMaestroCups description]
         * @return {[type]} [description]
         */
        function cargarMaestroCups(){
            var obJson          = parametrosComunes();
            obJson['accion']    = 'carga_maestro_cups';
            obJson['wbasedato'] = $("#wbasedato_cliame").val();

            // $(".bloquear_todo").attr("disabled","disabled");
            fnModalLoading("Cargando Maestro CUPS");

            $.post("actualizacion_cups.php", obJson,
                function(data){
                    if(data.error == 1)
                    {
                        fnModalLoading_Cerrar();
                        jAlert(data.mensaje, "Mensaje");
                    }
                    else
                    {
                        arrMaestroCups = data.arrMaestroCups;
                        iniciarSelects();
                    }
                    return data;
            },"json").done(function(data){
                    fnModalLoading_Cerrar();
                    //
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        /**
         * [reemplazarCups: función encargada de llamar mediante ajax todo el proceso php que realiza el reemplazo de un cups por otros,
         *                  la misma función puede ser llamada varias veces por ejemplo en la actualización masiva de códigos cups desde un archivo plano.]
         * @param  {[type]} cupAnterior [description]
         * @param  {[type]} cupNuevo    [description]
         * @param  {[type]} masivo      [description]
         * @return {[type]}             [description]
         */
        function reemplazarCups(cupAnterior, cupNuevo, masivo){
            var obJson            = parametrosComunes();
            obJson['accion']      = 'reemplazar_cups';
            obJson['cupAnterior'] = cupAnterior;
            obJson['cupNuevo']    = cupNuevo;
            obJson['modo_pruebas']= $("#modo_pruebas").val();

            // $(".bloquear_todo").attr("disabled","disabled");
            if(!masivo){
                fnModalLoading("Realizando actualizacion");
            }

            $.post("actualizacion_cups.php", obJson,
                function(data){
                    if(data.error == 1)
                    {
                        if(masivo){
                            contarTiposMensajes(cupAnterior, cupNuevo, "danger");
                        } else {
                            fnModalLoading_Cerrar();
                        }
                        jAlert(data.mensaje, "Mensaje");
                    }
                    else
                    {
                        var results_proceso = "";
                        var convenciones = "";
                        var alto_maximo = "360";
                        var alto = "height:360px;";
                        if(!masivo){
                            alto_maximo = height_window;
                            alto = "";
                            convenciones =   '<span class="small text-success">Correcto</span> | '
                                            +'<span class="small text-info">Informativo</span> | '
                                            +'<span class="small text-danger">Error</span> | '
                                            +'<span class="small text-warning">Otros</span><br>';
                        }
                        var resultadoProceso = data.resultadoProceso;
                        for (var tabla in resultadoProceso) {
                            var mensajes_proceso = resultadoProceso[tabla];
                            if(mensajes_proceso["mensajes"].length > 0){
                                results_proceso += '<ul class="list-group">'
                                                    +'<li class="list-group-item disabled"><strong>'+mensajes_proceso["nombre"]+'</strong></li>';
                                var arr_mensajes = mensajes_proceso["mensajes"];
                                for (var msj_idx in arr_mensajes) {
                                    var arr_msj = arr_mensajes[msj_idx];
                                    var tipo_msj = arr_msj["tip_class"];
                                    var updates = (arr_msj["updated"] != undefined) ? ' <span class="bg-'+tipo_msj+' badge">'+arr_msj["updated"]+'</span><br>': '';
                                    results_proceso += '<li class="list-group-item text-'+tipo_msj+' list-group-item-'+tipo_msj+' text-justify">'+arr_msj["mensaje"]+updates+'</li>';
                                    contarTiposMensajes(cupAnterior, cupNuevo, tipo_msj);
                                }
                                results_proceso += '</ul>';
                            }
                        }
                        results_proceso = '<div class="container-fluid" style="'+alto+' max-height: '+alto_maximo+'px; overflow-y: auto;">'+convenciones+results_proceso+'</div>';

                    if(!masivo){
                        fnModalLoading_Cerrar();
                        jAlert(results_proceso,"Mensaje");
                    } else {
                        // cupAnterior cupNuevo
                        $("#collapse_"+cupAnterior+"_"+cupNuevo).find(".panel-body").html(results_proceso);
                    }
                }
                    return data;
            },"json").done(function(data){
                    if(masivo){
                        iniciarActualizacionMasivaCUPS(cupAnterior, cupNuevo);
                        pintarCantidadTipoMensajesGlobal(cupAnterior, cupNuevo);
                    }
            }).fail(function(xhr, textStatus, errorThrown) {
                mensajeFailAlert('', xhr, textStatus, errorThrown);
                if(masivo){
                    contarTiposMensajes(cupAnterior, cupNuevo, "danger");
                    iniciarActualizacionMasivaCUPS(cupAnterior, cupNuevo);
                    pintarCantidadTipoMensajesGlobal(cupAnterior, cupNuevo);
                }
            });
        }

        /**
         * [contarTiposMensajes: dutante el proceso de actualización masiva de cups, esta función va contando los tipos de mensajes para mostrar un indicador
         *                       de posibles mensajes de error.]
         * @param  {[type]} cupAnterior [description]
         * @param  {[type]} cupNuevo    [description]
         * @param  {[type]} tipo_msj    [description]
         * @return {[type]}             [description]
         */
        function contarTiposMensajes(cupAnterior, cupNuevo, tipo_msj){
            var idx_parcups = cupAnterior+'_'+cupNuevo;

            // Array para contar todos los tipos de mensajes durante la carga masiva para poder mostrar el total de mensajes por tipo generados.
            if(arr_tipMensajeCupsMasivo[tipo_msj] == undefined) { arr_tipMensajeCupsMasivo[tipo_msj] = 0; }
            arr_tipMensajeCupsMasivo[tipo_msj] += 1;

            // Array para mostrar la cantidad de tipos de mensajes para un par de cups donde uno reemplazo a otro.
            if(arr_tipMensajesPorParCups[idx_parcups] == undefined) { arr_tipMensajesPorParCups[idx_parcups] = {"info":0, "danger":0,"warning":0,"success":0}; }
            switch(tipo_msj){
                case "info"    : arr_tipMensajesPorParCups[idx_parcups].info += 1;
                                 break;
                case "danger"  : arr_tipMensajesPorParCups[idx_parcups].danger += 1;
                                 break;
                case "warning" : arr_tipMensajesPorParCups[idx_parcups].warning += 1;
                                 break;
                case "success" : arr_tipMensajesPorParCups[idx_parcups].success += 1;
                                 break;
            }

            for (var tipo_msj_msv in arr_tipMensajeCupsMasivo)
            {
                switch(tipo_msj_msv){
                    case "info"    : $("#spn_masivo_"+tipo_msj_msv).html(arr_tipMensajeCupsMasivo[tipo_msj_msv]);
                                     break;
                    case "danger"  : $("#spn_masivo_"+tipo_msj_msv).html(arr_tipMensajeCupsMasivo[tipo_msj_msv]);
                                     break;
                    case "warning" : $("#spn_masivo_"+tipo_msj_msv).html(arr_tipMensajeCupsMasivo[tipo_msj_msv]);
                                     break;
                    case "success" : $("#spn_masivo_"+tipo_msj_msv).html(arr_tipMensajeCupsMasivo[tipo_msj_msv]);
                                     break;
                }
            }
        }

        /**
         * [pintarCantidadTipoMensajesGlobal: despues de hacer un conteo global de todos los mensajes en una carga masiva, esta función muestra la cantidad
         *                                    de mensajes despues de terminado el proceso o actualiza el indicador de mensajes despues de cada cups actualizado.]
         * @param  {[type]} cupAnterior [description]
         * @param  {[type]} cupNuevo    [description]
         * @return {[type]}             [description]
         */
        function pintarCantidadTipoMensajesGlobal(cupAnterior, cupNuevo){
            var idx_parcups = cupAnterior+'_'+cupNuevo;
            if(arr_tipMensajesPorParCups[idx_parcups] != undefined){
                $("#spn_masivo_info_"+idx_parcups).html(arr_tipMensajesPorParCups[idx_parcups].info);
                $("#spn_masivo_danger_"+idx_parcups).html(arr_tipMensajesPorParCups[idx_parcups].danger);
                $("#spn_masivo_warning_"+idx_parcups).html(arr_tipMensajesPorParCups[idx_parcups].warning);
                $("#spn_masivo_success_"+idx_parcups).html(arr_tipMensajesPorParCups[idx_parcups].success);
                $("#div_parCupsMensajes_"+idx_parcups).addClass("bg-success");
                $("#div_parCupsMensajes_"+idx_parcups).show(100);
            }
        }

        /**
         * [iniciarAccionesBotones: Se usa para inicializar todos los objetos html que deben reaccionar ante los botones de zoom
         *                                 adicionalmente se inicializan las acciones sobre el formulario p.ej. Guardar, Imprimir, ...]
         * @return {[type]} [description]
         */
        function iniciarAccionesBotones()
        {
            var btn_guardar_examen             = $(".btnActualizarCUPS");
            var btn_guardar_actModalMatrix     = $(".btnActualizarCUPSMatrix");
            var btnActualizarCUPSMatrixMaestro = $(".btnActualizarCUPSMatrixMaestro");
            var updateMatrixMaestro            = $(".updateMatrixMaestro");
            var btn_guardar_actMatrix          = $(".updateMatrix");
           // var btnActualizaMasivo           = $(".btnActualizaMasivo");
            var iniciarActualizacionMasiva     = $(".iniciarActualizacionMasiva");

            btn_guardar_examen.off('click').on('click',function(){
                validarFormulario();
            });

            btn_guardar_actModalMatrix.off('click').on('click',function(){
                $("#updateModalMatrix").find(".modal-header").removeClass("bg-danger");
                $("#updateModalMatrix").find(".modal-body").html('<p>Tenga en cuenta que este proceso puede tardar varios minutos.</p> <span class="text-info" id="spnTemporizador"><span>');
                $("#updateModalMatrix").modal({ backdrop: 'static',
                                                keyboard: false});
            });

            btn_guardar_actMatrix.off('click').on('click',function(){
                $("#updateModalMatrix").find(".modal-body").html('<p>Tenga en cuenta que este proceso puede tardar varios minutos.</p> <span class="text-info" id="spnTemporizador"><span>');
                interval_b($("#spnTemporizador"));
                $("#btnCancelUpdateMatrix").addClass("disabled");
                $("#btnCancelUpdateMatrix").attr("disabled","disabled");
                var $btn = $(this).button('loading');
                actualizarMatrixCron($btn);
            });

            btnActualizarCUPSMatrixMaestro.off('click').on('click',function(){
                $("#updateModalActualizaMatrixMaestro").find(".modal-header").removeClass("bg-danger");
                $("#updateModalActualizaMatrixMaestro").find(".modal-body").html('<p>Tenga en cuenta que este proceso puede tardar varios segundos.</p> <span class="text-info" id="spnTemporizadorMaestro"><span>');
                $("#updateModalActualizaMatrixMaestro").modal({ backdrop: 'static',
                                                                keyboard: false});
            });

            updateMatrixMaestro.off('click').on('click',function(){
                $("#updateModalActualizaMatrixMaestro").find(".modal-body").html('<p>Tenga en cuenta que este proceso puede tardar varios segundos.</p> <span class="text-info" id="spnTemporizadorMaestro"><span>');
                interval_b($("#spnTemporizadorMaestro"));
                $("#btnCancelUpdateMatrixMaestro").addClass("disabled");
                $("#btnCancelUpdateMatrixMaestro").attr("disabled","disabled");
                var $btn = $(this).button('loading');
                actualizarMatrixMaestroCups($btn);
            });

            iniciarActualizacionMasiva.off('click').on('click',function(){
                iniciarActualizacionMasivaCUPS("","");
            });
        }

        /**
         * [cargarArchivoActualizacionMasiva: función encargada de iniciar la carga del archivo plano y llamar el proceso php para validar el contenido del archivo de cups.]
         * @param  {[type]} extension [description]
         * @return {[type]}           [description]
         */
        function cargarArchivoActualizacionMasiva(extension){
            var formData = new FormData($("#needs-validation-cups")[0]);

            // Inicializar variables para que no se acumule si se carga un nuevo archivo.
            total_parejas_cups        = 0;
            arr_parejasCupsMasivo     = new Array();
            arr_tipMensajeCupsMasivo  = new Array();
            arr_tipMensajesPorParCups = new Array();

            formData.append('fileSelector', document.forms[0].fileSelector.files[0], 'lista_cups_actualizar.'+extension);
            formData.append("accion","leer_archivo_plano");
            formData.append("wemp_pmla", $("#wemp_pmla").val());
            formData.append("wbasedato_HCE", $("#wbasedato_HCE").val());
            formData.append("wbasedato_movhos", $("#wbasedato_movhos").val());
            formData.append("wbasedato_cliame", $("#wbasedato_cliame").val());
            formData.append("consultaAjax", '');

            // $("#guardarNuevo").prop("disabled", true);
            fnModalLoading("Leyendo archivo");
            $.ajax({
                url: "actualizacion_cups.php",
                type: "POST",
                dataType: "json",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data)
                {
                    if(data.error > 1){
                        if(data.error == 1){
                            jAlert(data.mensaje, "Alerta");
                        } else if(data.error == 2){
                            var errores_formato = '<span class="text-danger">Errores en los datos del archivo, corregir y volver a cargar el archivo.</span><br>';
                            for (var idx in data.arr_errores_formato) {
                                var msjerr = data.arr_errores_formato[idx];
                                errores_formato += '<p class="small text-info">'+msjerr+'.</p>';
                            }
                            var results_proceso =  '<div class="container-fluid" style="max-height: '+height_window+'px; overflow-y: auto;">'+errores_formato+'</div>';
                            fnModalLoading_Cerrar();
                            jAlert(results_proceso, "Alerta");
                        }
                    }else{
                        fnModalLoading_Cerrar();
                        msj_alerta = '<p class="text-danger">Antes de realizar la actualización recuerde haber hecho o solicitado realizar copia en Unix de los maestros:</p>'
                                      +'<ul style="list-style-type: inherit;margin-left: 15px;">'
                                      +'<li class="text-info">Procedimientos</li>'
                                      +'<li class="text-info">Exámenes</li>'
                                      +'<li class="text-info">Tarifas de procedimientos</li>'
                                      +'<li class="text-info">Tarifas de exámenes</li>'
                                      +'<li class="text-info">Relación empresa procedimiento</li>'
                                      +'<li class="text-info">Relación empresa exámenes</li>'
                                      +'<li class="text-info">Maestro tarifa de habitaciones</li>'
                                      +'</ul>';
                        jAlert(msj_alerta, "Alerta");
                        var results_proceso = "";
                        var convenciones =  'Mensajes: '
                                            +'<span class="small text-success">Correcto</span> <span id="spn_masivo_success" class="badge btn-success active">0</span>| '
                                            +'<span class="small text-info">Informativo</span> <span id="spn_masivo_info" class="badge btn-info active">0</span>| '
                                            +'<span class="small text-danger">Error</span> <span id="spn_masivo_danger" class="badge btn-danger active">0</span>| '
                                            +'<span class="small text-warning">Otros</span> <span id="spn_masivo_warning" class="badge btn-warning active">0</span><br>';
                        arr_parejasCupsMasivo = data.arr_parejasCups;
                        total_parejas_cups = Object.getOwnPropertyNames(arr_parejasCupsMasivo).length;

                        results_proceso += '<div class="panel-group" id="accordion">';
                        for(var index_pareja in arr_parejasCupsMasivo) {
                            var parejasCups = arr_parejasCupsMasivo[index_pareja];
                            var split_cups = parejasCups.split(",");
                            results_proceso +=  '<div class="panel panel-default">'
                                               +        '<div id="div_parCupsMensajes_'+index_pareja+'" class="text-right" style="display:none;">'
                                               +'           <span id="spn_masivo_success_'+index_pareja+'" class="badge btn-success active">0</span> '
                                               +'           <span id="spn_masivo_info_'+index_pareja+'" class="badge btn-info active">0</span> '
                                               +'           <span id="spn_masivo_danger_'+index_pareja+'" class="badge btn-danger active">0</span> '
                                               +'           <span id="spn_masivo_warning_'+index_pareja+'" class="badge btn-warning active">0</span>'
                                               +        '</div>'
                                               +'   <div class="panel-heading">'
                                               +'       <h4 class="panel-title">'
                                               +'           <a data-toggle="collapse" data-parent="#accordion" href="#collapse_'+index_pareja+'" class="small text-info">'
                                               +'               <strong>['+split_cups[0]+'] será reemplazado por ['+split_cups[1]+']</strong>'
                                               +'           </a>'
                                               +'       </h4>'
                                               +'   </div>'
                                               +'   <div id="collapse_'+index_pareja+'" class="panel-collapse collapse">'
                                               +'       <div class="panel-body"></div>'
                                               +'   </div>'
                                               +'</div>';
                        }
                        results_proceso += '</div>';
                        results_proceso =  '<div class="container-fluid" style="max-height: '+height_window+'px; overflow-y: auto;">'+convenciones+results_proceso+'</div>';
                        // Barra de progreso
                        results_proceso += '<div class="progress progreso-masivo-content">'
                                          +'  <div class="progreso-masivo progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'
                                          +'    0%'
                                          +'  </div>'
                                          +'</div>';
                        $("#cargarArchivoCUPS").find(".modal-body").html(results_proceso);
                        $("#cargarArchivoCUPS").modal({ backdrop: 'static',
                                                        keyboard: false});
                    }
                }
            }).done(function() {
                // alert( "success" );
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        /**
         * [iniciarActualizacionMasivaCUPS: Según la cantidad de parejas de cups a actualizar, esta función realiza el llamado paso a paso para actualizar cada pareja de cups.
         *                                  Esta misma función realizar el calculo para mostrar el progreso de todo el proceso de actualización según la cantidad de cups a actualizar.]
         * @param  {[type]} cupAnterior [description]
         * @param  {[type]} cupNuevo    [description]
         * @return {[type]}             [description]
         */
        function iniciarActualizacionMasivaCUPS(cupAnterior, cupNuevo){
            var parejas = 0;
            var $btn = $(".iniciarActualizacionMasiva");
            var index_eliminar = (cupAnterior != "" && cupNuevo != "") ? cupAnterior+"_"+cupNuevo: "";

            if(index_eliminar != ""){
                delete arr_parejasCupsMasivo[index_eliminar];
            } else {
                $("#btnCancelActualizaMasivo").addClass("disabled");
                $("#btnCancelActualizaMasivo").attr("disabled","disabled");
                $btn.button('loading');
                $("#cargarArchivoCUPS").find(".progreso-masivo-content").show();
                $("#cargarArchivoCUPS").find(".progreso-masivo").addClass("active");
            }

            // verifica cuántas parejas de cups quedan en el array pendientes por actualizar (cuenta los indices).
            var pendientes = Object.getOwnPropertyNames(arr_parejasCupsMasivo).length;
            var porcentaje_avance = 0;

            porcentaje_avance = (((total_parejas_cups - pendientes) * 100) / total_parejas_cups);
            porcentaje_avance = porcentaje_avance.toFixed(0);

            var terminar = "";
            terminar = (porcentaje_avance == 100) ? " Terminó": "";

            $("#cargarArchivoCUPS").find(".progreso-masivo").attr("aria-valuenow",porcentaje_avance);
            $("#cargarArchivoCUPS").find(".progreso-masivo").css("width",porcentaje_avance+"%");
            $("#cargarArchivoCUPS").find(".progreso-masivo").html(porcentaje_avance+"%"+terminar);

            // Se verifica si hay una pareja siguiente
            if(Object.keys(arr_parejasCupsMasivo)[0] !== undefined){
                var index_siguiente = Object.keys(arr_parejasCupsMasivo)[0];
                var parejasCups = arr_parejasCupsMasivo[index_siguiente];
                var split_cups = parejasCups.split(",");
                cupAnterior = split_cups[0];
                cupNuevo    = split_cups[1];
                reemplazarCups(cupAnterior, cupNuevo, true);
            } else {
                desbloquearBotonesCargaMasiva();
                // $("#cargarArchivoCUPS").find(".progreso-masivo-content").hide(6000);
            }
        }

        /**
         * [desbloquearBotonesCargaMasiva: cuando se inicia el proceso de actualización masiva de cups, se bloquean los botones, el objetivo de esta función
         *                                 es desbloquear los botones en la ventana de carga masiva despues de terminar el proceso de actualización.]
         * @return {[type]} [description]
         */
        function desbloquearBotonesCargaMasiva(){
            var $btn = $(".iniciarActualizacionMasiva");
            $btn.button('reset');
            $("#btnCancelActualizaMasivo").removeClass("disabled");
            $("#btnCancelActualizaMasivo").removeAttr("disabled");
            $("#cargarArchivoCUPS").find(".progreso-masivo").removeClass("active");
        }

        var intervalSetArr;
        function interval_b(objeto_contenedor){
            clearInterval(intervalSetArr);
            t=1;
            min=0;
            intervalSetArr = setInterval(function(){
                var coneto_seg=t++;
                var coneto_min=min;
                if(coneto_seg >= 59) {
                    min+=1;
                    t=0;
                }
                var tiempo = 'Transcurrido: '+coneto_min+':'+coneto_seg;
                objeto_contenedor.html(tiempo);
            },1000,"JavaScript");
        }

        /**
         * [fnModalLoading: Es función se encarga de mostrar una ventana modal cada vez que se hace un llamado ajax con el fin de bloquear la página web hasta que se
         *                    se genere una respuesta y evitar que el usuario genere más eventos (click) sin terminar la petición anterior y evitar problemas
         *                    en la veracidad de datos]
         * @return {[type]} [description]
         */
        function fnModalLoading(msje_anexo)
        {
            var msj = (msje_anexo == undefined) ? '': msje_anexo;
            $("#div_loading").find("#msj_anexo_loading").html(msj);
            $("#div_loading").modal({backdrop: 'static',
                                    keyboard: false});
        }

        /**
         * [fnModalLoading_Cerrar: complemento a la función fnModalLoading, esta se encarga de cerrar la ventana modal]
         * @return {[type]} [description]
         */
        function fnModalLoading_Cerrar()
        {
            $("#div_loading").modal('hide');
            $("#div_loading").find("#msj_anexo_loading").html("");
            /*if($("#div_loading").is(":visible"))
            {
                console.log("ok");
                $("#div_loading").modal('hide');
                // $("#div_loading").dialog('close');
                // $('#myModal').hide();
                // $('.modal-backdrop').hide();
            }else{
                console.log("ok2");
            }*/
            // console.log($("#div_loading"));
        }

        function validarTipoArchivo(contador){
            var fileName = $("#fileSelector"+contador).val();

            if(fileName != ""){
                var arrFile = fileName.split(".");
                var cont = arrFile.length;
                var cont = cont-1;
                var extension = arrFile[cont];

                if(extension != "txt" && extension != "csv"){
                    var msj = "Solo esta permitido procesar archivos con extensiones .txt o .csv";
                    jAlert(msj, "Alerta");
                    $('#fileSelector'+contador).val("");
                } else {
                    cargarArchivoActualizacionMasiva(extension);
                }
            }
        }

        function classOnFocus(elem)
        {
            $(".onfocus").removeClass("onfocus");
            $(elem).addClass("onfocus");
        }

        /**
         * [parametrosComunes: Genera un json con las variables más comunes que se deben enviar en los llamados ajax, evitando tener que crear los mismos parámetros de envío
         *                     en cada llamado ajax de forma manual.]
         * @return {[type]} [description]
         */
        function parametrosComunes()
        {
            var obJson                 = {};
            obJson['wemp_pmla']        = $("#wemp_pmla").val();
            obJson['wbasedato_HCE']    = $("#wbasedato_HCE").val();
            obJson['wbasedato_movhos'] = $("#wbasedato_movhos").val();
            obJson['wbasedato_cliame'] = $("#wbasedato_cliame").val();
            obJson['consultaAjax']     = '';
            return obJson;
        }

        function enterBuscar(e)
        {
            var tecla = (document.all) ? e.keyCode : e.which;
            if(tecla == 13)
            {
                // btn_consultarDatos();
            }
        }

        function reiniciarTooltip()
        {
            $('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
        }
    </script>

    <script type="text/javascript">

        function isset ( strVariableName ) {
            try {
                eval( strVariableName );
            } catch( err ) {
                if ( err instanceof ReferenceError )
                   return false;
            }
            return true;
        }


        function ocultarElemnto(elemento){
            $("#"+elemento).hide(1000);
        }

        function soloNumeros(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            // console.log(charCode);
             if ((charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36) || (charCode == 46)) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
                return false;

             return true;
        }

        function soloNumerosDecimales(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            //alert(charCode);
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
                return false;

             return true;
        }

        function soloNumerosLetras(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            // console.log(charCode);
             // if (charCode > 31 && (charCode < 48 || charCode > 57))
             // if ((charCode < 48 && charCode > 57) || (charCode < 65 && charCode > 90) || (charCode < 97 && charCode > 122 ))
             if (((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) || charCode == 8 || charCode == 9) && (charCode != 46))
                return true;

             return false;
        }

        /**
         * Para aceptar caracteres numéicos, letras y algunos otros caracteres permitidos
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

        function trOver(grupo)
        {
            $(grupo).addClass('classOver');
        }

        function trOut(grupo)
        {
            $(grupo).removeClass('classOver');
        }

        function cerrarVentanaPpal()
        {
            window.close();
        }

    </script>

    <style type="text/css">
        .fila1 {
            background-color:   #C3D9FF;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .fila2 {
            background-color:   #E8EEF7;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }
        .encabezadoTabla {
            background-color : #2a5db0;
            color            : #ffffff;
            font-size        : 9pt;
            font-weight      : bold;
            padding          : 1px;
            font-family      : verdana;
        }

        .classOver{
            background-color: #CCCCCC;
        }

        .classOverFormula{
            background-color: #B5EFA6;
        }

        .bgGris1{
            background-color:#F6F6F6;
        }

        .fondoEncabezado{
            background-color: #2A5DB0;
            color: #FFFFFF;
            font-size: 10pt;
            font-weight: bold;
        }

        .campoRequerido{
            border: 1px orange solid;
            background-color:lightyellow;
        }

        .mayuscula{
            text-transform: uppercase;
        }

        #tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        #tooltip h3, #tooltip div{
            margin:0; width:auto
        }

        #tooltip_pro{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        #tooltip_pro h3, #tooltip_pro div{
            margin:0; width:auto
        }

        .ui-autocomplete{
            max-width:  230px;
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
            font-size:  9pt;
        }

        .fixed-dialog{
             position: fixed;
             top: 100px;
             left: 100px;
        }

        .ui-dialog
        {
            background: #FFFEEB;
        }

        .texto_add{
            font-size: 8pt;
        }

        .submit{
            text-align: center;
            background: #C3D9FF;
        }

        .titulopagina2
        {
            border-bottom-width: 1px;
            /*border-color: <?=$bordemenu?>;*/
            border-left-width: 1px;
            border-top-width: 1px;
            font-family: verdana;
            font-size: 18pt;
            font-weight: bold;
            height: 30px;
            margin: 2pt;
            overflow: hidden;
            text-transform: uppercase;
        }

        .onfocus{
            background-color: lightyellow;
        }

        .font_label{
            font-size: 16px;
            white-space: nowrap;
        }

        fieldset{
            border: 2px solid #e2e2e2;
        }

        legend{
            border: 2px solid #e2e2e2;
            border-top: 0px;
            font-family: Verdana;
            background-color: #e6e6e6;
            font-size: 11pt;
        }

        ul{
            margin:0;
            padding:0;
            list-style-type:none;
        }

        .loader {
            border: 5px solid #f3f3f3;
            border-radius: 50%;
            border-top: 5px solid #3498db;
            border-bottom: 5px solid #3498db;
            width: 30px;
            height: 30px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
          0% { -webkit-transform: rotate(0deg); }
          100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body width="100%">
<?php
    encabezado("<div class='titulopagina2'>Actualizaci&oacute;n de CUPS UNIX/MATRIX</div>", $wactualiza, "clinica");
?>
<input type='hidden' name='wemp_pmla' id='wemp_pmla' value="<?=$wemp_pmla?>">
<input type='hidden' name='wbasedato_HCE' id='wbasedato_HCE' value="<?=$wbasedato_HCE?>">
<input type='hidden' name='wbasedato_movhos' id='wbasedato_movhos' value="<?=$wbasedato_movhos?>">
<input type='hidden' name='wbasedato_cliame' id='wbasedato_cliame' value="<?=$wbasedato_cliame?>">
<input type='hidden' name='modo_pruebas' id='modo_pruebas' value="<?=$modo_pruebas?>">

<div class="container">
    <table align="center" style="width:95%;">
        <tr>
            <td style="text-align:left;">
                <div id="contenedor_programa_transcripcion" class="container">
                    <div width='' id='accordionDatosCups' style="" class="">
                        <h3>Datos para la actualizaci&oacute;n</h3>
                        <div class='' id='DatosPaciente' >
                            <form id="needs-validation-cups" novalidate enctype='multipart/form-data' method='post'>
                                <div class="">
                                    <div class="form-group row justify-content-md-center">
                                        <div class="col-3 col-md-3"></div>
                                        <div class="col-3 col-md-6">
                                            <label for="cupAnterior">C&oacute;digo CUPS a inactivar</label>
                                            <!-- <input type="text" class="form-control" required id="cupAnterior" placeholder="CUPS a inactivar" onkeypress="return soloNumerosLetras(event);"> -->
                                            <select class="form-control" required id="cupAnterior" placeholder="CUPS a inactivar">
                                            </select>
                                            <!-- <div class="invalid-feedback">
                                                Por favor escriba el código a inactivar
                                            </div> -->
                                        </div>
                                        <div class="col-3 col-md-3"></div>
                                    </div>
                                    <div class="form-group row justify-content-md-center">
                                        <div class="col-3 col-md-3"></div>
                                        <div class="col-3 col-md-6">
                                            <label for="cupNuevo">Nuevo c&oacute;digo CUPS de reemplazo</label>
                                            <!-- <input type="text" class="form-control" required id="cupNuevo" aria-describedby="cupNuevoHelp" placeholder="Nuevo CUPS de reemplazo" onkeypress="return soloNumerosLetras(event);"> -->
                                            <select class="form-control" required id="cupNuevo" aria-describedby="cupNuevoHelp" placeholder="Nuevo CUPS de reemplazo" >
                                            </select>
                                            <small id="cupNuevoHelp" class="form-text text-muted">Se inactivar&aacute; el CUPS antiguo y se reemplazar&aacute; por el c&oacute;digo nuevo en todas las relaciones o anexos existentes tanto en Unix como en Matrix.</small>
                                            <br>
                                            <small id="cupNuevoHelp" class="form-text text-muted text-danger">* Si un CUPS no est&aacute; en el seleccionador pero se sabe que est&aacute; en el maestro de CUPS Unix, use el bot&oacute;n "Actualizar (Maestro CUPS)" e intente seleccionarlo nuevamente.</small>
                                        </div>
                                        <div class="col-3 col-md-3"></div>
                                    </div>
                                    <div class="form-group row justify-content-md-center">
                                        <div class="col-3 col-md-3"></div>
                                        <div class="col-3 col-md-6">
                                            <!-- <button type="button" class="st_boton">Actualizar CUPS</button> -->
                                            <button type="button" class="btnActualizarCUPS btn btn-danger">Reemplazar CUPS</button>
                                        </div>
                                        <div class="col-3 col-md-3"></div>
                                    </div>
                                    <hr>
                                    <div class="form-group row justify-content-md-center">
                                        <div class="col-3 col-md-3"><p class="text-info">Actualizaci&oacute;n masiva de c&oacute;digos CUPS</p></div>
                                        <div class="col-3 col-md-6">
                                            <!-- <button type="button" class="st_boton">Actualizar CUPS</button> -->
                                            <!-- <button type="button" class="btnActualizaMasivo btn btn-info">Seleccionar archivo</button> -->
                                            <label class="btn btn-info" for="fileSelector">
                                                <input id="fileSelector" type="file" style="display:none"
                                                onchange="$('#upload-file-info').html(this.files[0].name); validarTipoArchivo('');">
                                                Seleccionar archivo
                                            </label>
                                            <span class='label label-info' id="upload-file-info"></span>
                                        </div>
                                        <div class="col-3 col-md-3"></div>
                                    </div>
                                    <hr>
                                    <div class="form-group row justify-content-md-center">
                                        <div class="col-3 col-md-3"><p class="text-info">Actualizaci&oacute;n solo maestro de CUPS en Matrix desde Unix</p></div>
                                        <div class="col-3 col-md-6">
                                            <!-- <button type="button" class="st_boton">Actualizar CUPS</button> -->
                                            <button type="button" class="btnActualizarCUPSMatrixMaestro btn btn-info">Actualizar (Maestro CUPS)</button>
                                        </div>
                                        <div class="col-3 col-md-3"></div>
                                    </div>
                                    <hr>
                                    <div class="form-group row justify-content-md-center">
                                        <div class="col-3 col-md-3"><p class="text-info">Actualizar procedimientos, ex&aacute;menes, tar&iacute;fas en Matrix</p></div>
                                        <div class="col-3 col-md-6">
                                            <!-- <button type="button" class="st_boton">Actualizar CUPS</button> -->
                                            <button type="button" class="btnActualizarCUPSMatrix btn btn-info">Actualizar (todo)</button>
                                        </div>
                                        <div class="col-3 col-md-3"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <br>
                </div>
            </td>
        </tr>
    </table>
    <br />
    <br />
    <table align='center'>
        <tr><td align="center" colspan="9"><input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();"></td></tr>
    </table>
</div>
<br />
<br />

<!-- <div id="div_loading" style="display:none;"><img width="15" height="15" src="../../images/medical/ajax-loader5.gif" /> Consultando datos, espere un momento por favor...</div> -->
<input type='hidden' name='failJquery' id='failJquery' value='El programa termin&oacute; de ejecutarse pero con algunos inconvenientes <br>(El proceso no se complet&oacute; correctamente)' >

<!-- Modal loading -->
<div class="modal fade" id="div_loading" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <h4 class="modal-title" id="loadingModalLabel">Procesando ...</h4>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 col-sm-4">
                    <div class="loader pull-right"></div>
                </div>
                <div class="col-md-6 col-sm-6">Espere un momento por favor... <span class="text-info" id="msj_anexo_loading"></span></div>
            </div>
        </div>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div> -->
    </div>
  </div>
</div>

<!-- Modal jAlert -->
<div class="modal fade" id="jAlert" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="alertModalLabel">Modal title</h4>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body" >
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>


<!-- Modal updateModalMatrix -->
<div class="modal fade" id="updateModalMatrix" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
        <h4 class="modal-title">Actualizar CUPS Matrix</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnCancelUpdateMatrix" data-dismiss="modal">Retornar</button>
        <button type="button" class="updateMatrix btn btn-info" data-loading-text="Actualizando!">Iniciar actualizaci&oacute;n</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal updateModalActualizaMatrixMaestro -->
<div class="modal fade" id="updateModalActualizaMatrixMaestro" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
        <h4 class="modal-title">Actualizar maestro de CUPS en Matrix con el maestro CUPS Unix</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnCancelUpdateMatrixMaestro" data-dismiss="modal">Retornar</button>
        <button type="button" class="updateMatrixMaestro btn btn-info" data-loading-text="Actualizando!">Iniciar actualizaci&oacute;n</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal cargarArchivoCUPS -->
<div class="modal fade" id="cargarArchivoCUPS" tabindex="0" role="dialog">
  <div class="modal-dialog" role="document" style="width:900px; height:700px;">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
        <h4 class="modal-title">Actualizaci&oacute;n masiva de CUPS</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="btnCancelActualizaMasivo" data-dismiss="modal">Retornar</button>
        <button type="button" class="iniciarActualizacionMasiva btn btn-info" data-loading-text="Actualizando!">Iniciar actualización</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</body>
</html>