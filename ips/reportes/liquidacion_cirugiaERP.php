<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : liquidacion_cirugiaERP.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 20 Noviembre de 2013

 DESCRIPCION: Programa encargado de hacer la liquidación de paquetes y cirugías, aplicando manuales de porcentajes de cirugías múltiples, límites de UVR, GRUPO, Ayudantía
                Politicas.


 Notas:
 El valor del parámetro $ccotema es determinado en el programa gestor_aplicaciones.php
 */ $wactualiz = "(Octubre 09 de 2014)"; /*
 ACTUALIZACIONES:
 *  Octubre 09 de 2014
    Edwar Jaramillo     :   * Cuando se leen los porcentajes de porcentajes múltiples se tiene en cuenta el nuevo campo que indica hasta que cantidad máxima de cirugías se cobra un
                                concepto sin importar especialidad.

                            * Modificaciónes a la lógica en la liquidación de cirugías bilaterales y diferentes especialidades con diferentes vías:
                                - Cuando es diferente via y diferentes especialidad, ahora tiene en cuenta hasta que cantidad máxima de cirugía se puede cobrar un concepto (por ejemplo
                                    hasta la segunda cirugía mayor).
                                - Si una cirugía es mayor a una cirugía bilateral entonces se pierde la bilateralidad (pero se conservan las dos cirugías deribadas de la bilateral).
                                - Si hay diferente especialidad y hay una cirugía bilateral se pierde la bilateralidad.
                                - Si una cirugía bilateral tiene la misma cantidad de puntos que una tercer cirugía del mismo especialista, siempre se debe cobrar primero la cirugía
                                    bilateral.

                            * Modificanciones en la liquidación de materiales y medicamentos: Ahora si una sola cirugía de varias supera el límite de puntos entonces a ninguna se le
                                cobra el concepto 0168, pero si nunguna cirugia supera el límite entonces a todas las cirugías se les cobra el concepto 0168. Se está creando un solo
                                mercado sin importar a que procedimiento se le tenga grabado, solo importa un mercado y todos los insumos en la liquidación se asocian siempre al primer procedimiento de la liquidación.

                            * Modificaciones en el proceso para determinar el orden de las cirugías cuando la modalidad es por código, en este caso lo que se debe hacer es saber el
                                costo de cada cirugía, para esto se implementó un nuevo proceso encargado de sumar todos los conceptos de cada procedimiento, consultar su respectiva
                                tarifa y sumar en un solo valor dando como equivalente el precio total de cada cirugía y poder ordenarla de mayor a menor.

                            * Se implementó que cuando una tarifa o varias existan en un nuevo parámetro creado en la tabla root_51 determine que para los conceptos que pueden digitar
                                valor, siempre deben permitir digitar valor y no dejar de hacerlo si se crea una tarifa en el maestro de tarifas (siempre debe permitir escribir valor).

                            * Se implementa un nuevo proceso encargado de mostrar en pantalla todos los conceptos o cargos que no se les encontrón tarifa, esto hace que no se continúe
                                con la liquidación y se detenga pero permita mostrar el detalle de los parámetros usados para buscar una tarifa que no pudo ser encontrada en el maestro de tarifas.

                            * Se detectó y se corrigió un inconveniente cuando se pedía valor para un concepto, pues cuando habían varias cirugias y se escribía un valor y se
                                actualizaba la liquidación, el valor digitado inicialmente empezaba a dismuir tras cada actualización debido a que siempre le aplicaba el porcentaje equivalente
                                al manual de cirugías y no lo hacía sobre la tarifa originalemente digitada sino cada vez sobre el valor recalculado.

 *  Octubre 01 de 2014
    Edwar Jaramillo     :   * El tipo de procedimiento E:Examen, P:Procedimiento ya no tiene validéz, debído a esto en root_51 se marcan los conceptos
                                uso de equipos y ayudas diagnósticas para poder solicitar agregar equipos o exámenes cuando se escriben estos dos conceptos,
                                ya no hay posibilidad de filtrar solo equipos o solo exámenes, se modifican entonces las consultas para traer todos los códigos
                                de la tabla 103 y poder agregar el que se desee asociado a equipo o exámen.

 *  Septiembre 30 de 2014
    Edwar Jaramillo     :   * Se adicionan dos nuevos parámetros al llamado de la función "ValidarSiEsFacturable".

 *  Agosto 19 de 2014
    Edwar Jaramillo     :   * Mejora, cuando una história e ingreso tienen cirugías pendiente por liquidar por falta de tarifa y al digitar
                                la historia en el campo correspondiente pueden aparecer las opciones de "Recuperar" o "Nueva", cuando se escoge
                                nueva cirugía, esta acción hará que se desacarte la cirugía pendiente de liquidar por falta de tarífa para esa historia e ingreso.
                            * El campo de procedimientos fue mejorado para que muestre los códigos personalizados que se encuentran en la relación
                                empresa procedimiento, de esta menera se puede digitar el código CUPS o bien el codigo personalizado de una procedimiento.
                            * Se crean tres nuevos campos para la auditoria de cirugias liquidadas, se crean los campos de fecha, hora y usuario
                                que anula una liquidación (000198, 000199).
                            * Se tiene en cuenta el campo "codHomologar" que se usa cuando la función de tarifas devuelve un código personalizado
                                para un cargo, este código se usará en la grabación del cargo para ser enviado a unix  en cambio del código del procedimiento origianl.
                            * Cuando una liquidación le falta alguna tarífa para un concepto que pide valor, ese acto quirúrgico inmediatamente
                                se carga en la sección "Cirugías pendientes por liquidar por falta de tarifa", antes se debía recargar la página para
                                poder actualizar esa nueva sección.

 *  Agosto 12 de 2014
    Edwar Jaramillo     : Se documentan las funciones del programa que estaban pendientes por documentar.

 *  Noviembre 20 de 2013
    Edwar Jaramillo     : Fecha de la creación del programa.


*** NOTAS ***
Estados de cargos sin tarífas
    * PR: Pendiente revisión
    * CO: Corregido

**/
global $ccotema;
global $wbasedato_HCE, $bordemenu;

$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");






include_once("../../gesapl/procesos/gestor_aplicaciones_config.php");
include_once("../../gesapl/procesos/gesapl_funciones.php");
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
include_once("ips/funciones_facturacionERP.php");


if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
$user_session      = explode('-',$_SESSION['user']);
$user_session      = $user_session[1];
$user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);

// Incluye variables globales de tablas de parametrización y funciones comunes al módulo.
// include_once ("ayudasdx_config.php");


/*****  DICCIONARIO LOCAL *****/
define('PROCEDIMIENTO'      ,'Procedimiento');
define('CONCEPTO_LABEL'     ,'Concepto');
define('TARIFA'             ,'Tarífa');
define('ENTIDAD'            ,'Entidad');
define('TIPO_INGRESO'       ,'Tipo ingreso');
define('BASE_LIQUIDACION'   ,'Base liquidación');
define('NOMBRE_PLANTILLA'   ,'Nombre plantilla');
define('_VIA_'   ,'Vía');
define('ESPECIALISTA'   ,'Especialista');
define('ESPECILALIDAD'   ,'Especialidad');
define('ORGANO'   ,'Organo');
define('BILATERAL'   ,'Bilateral');
define('TIEMPO_MINUTOS'   ,'Tiempo (Minutos)');
define('TERCERO_LABEL'   ,'Tercero');

define('ANESTESIA'          ,'Anestesia');
define('TIEMPO_INICIAL_MTS' ,'Tiempo inicial (Minutos)');
define('TIEMPO_FINAL_MTS'   ,'Tiempo final (Minutos)');
define('CODIGO_RANGO'       ,'Código rango');
define('PREF_POL'           , 'PCX');
define('TIEMPO'             , 'Tiempo');
define('LB_PAQUETE'         , 'Paquete');

/**********  TABLAS  **********/
define('TB_ENCABEZADO'      ,'000180');
define('TB_COBRO_HORA'      ,'000181');
define('TB_COBRO_ANESTESIA' ,'000182');
define('TB_COBRO_USO'       ,'000183');

define('TB_BASE_LIQUIDACION','000186');
define('TB_BASE_LIQ_ACTOSQX','000187');
define('TB_BASE_LIQPORCENTA','000188');
define('TB_LIQUIDACIONES'   ,'000198');
define('TB_ENC_LIQUIDACION' ,'000199');
define('TB_BILATERALES'     ,'000201');
define('TIPO_FACTURACION_PAQUETE' ,'PAQUETE');
define('CANTIDAD_DECIMALES' ,1);

$caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");

/**
    Reiniciar datos sesión
*/
function reiniciarDatosSesion()
{
    $arr_politica = array(); //unset($_SESSION['arr_conceptos']);
    return $arr_politica;
}

/**
 * [seguimiento description: Función para uso solo de desarrollo, en ambiente local, crea un archivo de texto donde se imprimen variables y arrays para su seguimiento]
 * @param  [type] $seguir [Cadena de texto a guardar en el archivo, para guardar array recordar usar print_r($al_array, true), puede usar saltos de línea PHP así PHP_EOL ]
 * @return [type]         [description]
 */
function seguimiento($seguir)
{
    $fp = fopen("seguimiento.txt","a+");
    fwrite($fp, "[".date("Y-m-d H:i:s")."]".PHP_EOL.$seguir);
    fclose($fp);
}

function array_base_liquidacion($conex, $wbasedato)
{
    $sql = "SELECT  Blqclq AS codigo, Blqdes AS nombre
            FROM    {$wbasedato}_".TB_BASE_LIQUIDACION."
            WHERE   Blqest = 'on'
            ORDER BY Blqdes";
    $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
    $arr_liquidacion = array();
    while ($row = mysql_fetch_array($result))
    {
        $arr_liquidacion[$row['codigo']] = utf8_encode($row['nombre']);
    }
    return $arr_liquidacion;
}


/**
 * [nombreDiaSemana description: Esta función recibe los número de año, més y día, y devuelve el nombre del día de la semana en la fecha indicada]
 * @param  [type] $ano [description]
 * @param  [type] $mes [description]
 * @param  [type] $dia [description]
 * @return [type]      [String, nombre del día de la semana]
 */
function nombreDiaSemana($ano,$mes,$dia)
{
    $nameDias[] = 'Domingo';
    $nameDias[] = 'Lunes';
    $nameDias[] = 'Martes';
    $nameDias[] = 'Miercoles';
    $nameDias[] = 'Jueves';
    $nameDias[] = 'Viernes';
    $nameDias[] = 'Sabado';
    // 0->domingo    | 6->sabado
    $dia= date("w",mktime(0, 0, 0, $mes, $dia, $ano));
    return $nameDias[$dia];
}

/**
 * [crear_posicion_lista_equipo_examen: Se encarga de adicionar en el array "arr_conceptos_liquidar" las posiciones correspondientes a equipos o examenes
 * de manera separada y cada que se quiera agregar un nuevo equipo o examen, esta función se encarga de validar si está o no para agregarlo a la lista]
 * @param  [type] $arr_conceptos_liquidar [Array de conceptos a liquidar en el acto quirúrgico, se usan en la columna de conceptos en la pantalla de liquidación]
 * @param  [type] $concepto_codigo        [Código del concepto a agregar en la lista si no existe lo crea, si existe entonces solo agrega el examen o equipo]
 * @param  [type] $add_equipo             [Código del examen o equipo a gregar a la lista]
 * @param  [type] $add_equipo_nombre      [Nombre del examen o equipo que se agregó a la lista]
 * @param  [type] $nombre_lista           [Indica el indice al que se debe agregar el nuevo elemento de la lista, a la lista "lista_equipos" ó "lista_equipos"]
 * @return [type]                         [description]
 */
function crear_posicion_lista_equipo_examen(&$arr_conceptos_liquidar, $concepto_codigo, $add_equipo, $add_equipo_nombre, $nombre_lista)
{
    if(array_key_exists($concepto_codigo, $arr_conceptos_liquidar) && !array_key_exists($nombre_lista, $arr_conceptos_liquidar[$concepto_codigo]))
    {
        $arr_conceptos_liquidar[$concepto_codigo][$nombre_lista] = array();
    }

    if(!array_key_exists($add_equipo, $arr_conceptos_liquidar[$concepto_codigo][$nombre_lista]))
    {
        $arr_conceptos_liquidar[$concepto_codigo][$nombre_lista][$add_equipo] = array("nombre"=>$add_equipo_nombre, "codigo"=>$add_equipo);
    }
}

/**
 * [agregarEquiposExamenes: Esta función valida si el concepto a agregar a la lista de conceptos liquidados corresponde a equipos o examenes, de acuerdo a eso
 *         identifíca a que lista lo agregará, si a "lista_equipos" ó "lista_examenes", el array de "arr_datos_liquidados" es útil en esta función por que
 *         ese array contiene la información de los conceptos, procedimientos, examenes, equipos que ya estan en proceso de liquidación, es decir, ya presionó por lo menos una vez
 *         el boton liquidar y se hizo un preliquidación, si se presiona de nuevo el boton liquidar entonces se usan los datos de "arr_datos_liquidados" para reconstruir los datos
 *         que ya estaban en pantalla y no se pierda información, por ejemplo si agregaron un concepto que no estaba en plantillas o agregaron un nuevo equipo o un nuevo examen
 *         en la pantalla de liquidación mediante la opción o el campo "Agregar concepto"]
 * @param  [type] $arr_conceptos_liquidar     [Array de conceptos a liquidar en el acto quirúrgico, se usan en la columna de conceptos en la pantalla de liquidación]
 * @param  [type] $concepto_codigo            [Concepto para agregar a la lista]
 * @param  [type] $procedimiento_bilat_dif    [Si procedimiento liquidado es igual a procedimiento_add significa que el examen o equipo a adicionar debe hacerlo en esa posición
 *                                            y verificar si no existe la lista para crearla "crear_posicion_lista_equipo_examen" y adicionar el equipo-examen nuevo]
 * @param  [type] $procedimiento_liquidar_cod [Se dejó de necesitar en la función]
 * @param  [type] $wprocedimiento_add         [Si procedimiento liquidado es igual a procedimiento_add significa que el examen o equipo a adicionar debe hacerlo en esa posición
 *                                            y verificar si no existe la lista para crearla "crear_posicion_lista_equipo_examen" y adicionar el equipo-examen nuevo]
 * @param  [type] $arr_datos_liquidados       [Array buffer donde esta una posible liquidación en curso y contiene datos que se han ido agregando durante la pre-liquidación
 *                                            (equipos, examenes, nuevos conceptos que no estan plantillas)]
 * @return [type]                             [description]
 */
function agregarEquiposExamenes(&$arr_conceptos_liquidar, $concepto_codigo, $procedimiento_bilat_dif, $procedimiento_liquidar_cod, $wprocedimiento_add, $arr_datos_liquidados)
{
    global $id_concepto_uso_equipos, $id_concepto_examenes, $add_equipo, $add_examen, $add_equipo_nombre, $add_examen_nombre;

    if($concepto_codigo == $id_concepto_uso_equipos || $concepto_codigo == $id_concepto_examenes)
    {
        switch ($concepto_codigo)
        {
            case $id_concepto_uso_equipos: // Cuando se está añadiendo un equipo
                if(!empty($add_equipo) && $procedimiento_bilat_dif == $wprocedimiento_add)
                {
                    crear_posicion_lista_equipo_examen($arr_conceptos_liquidar, $concepto_codigo, $add_equipo, $add_equipo_nombre, "lista_equipos");
                }

                if(array_key_exists($procedimiento_bilat_dif, $arr_datos_liquidados) && array_key_exists($concepto_codigo, $arr_datos_liquidados[$procedimiento_bilat_dif]))
                {
                    foreach ($arr_datos_liquidados[$procedimiento_bilat_dif][$concepto_codigo] as $codigo_equipo_liquidado => $arr_equipos_liquidados)
                    {
                        $equipo_nombre_liquidado = $arr_equipos_liquidados['nombre_examen_equipo'];
                        crear_posicion_lista_equipo_examen($arr_conceptos_liquidar, $concepto_codigo, $codigo_equipo_liquidado, $equipo_nombre_liquidado, "lista_equipos");
                    }
                }
                break;

            case $id_concepto_examenes: // Cuando se está añadiendo un exámen
                if(!empty($add_examen) && $procedimiento_bilat_dif == $wprocedimiento_add)
                {
                    crear_posicion_lista_equipo_examen($arr_conceptos_liquidar, $concepto_codigo, $add_examen, $add_equipo_nombre, "lista_examenes");
                }

                if(array_key_exists($procedimiento_bilat_dif, $arr_datos_liquidados) && array_key_exists($concepto_codigo, $arr_datos_liquidados[$procedimiento_bilat_dif]))
                {
                    foreach ($arr_datos_liquidados[$procedimiento_bilat_dif][$concepto_codigo] as $codigo_examen_liquidado => $arr_equipos_liquidados)
                    {
                        $examen_nombre_liquidado = $arr_equipos_liquidados['nombre_examen_equipo'];
                        crear_posicion_lista_equipo_examen($arr_conceptos_liquidar, $concepto_codigo, $codigo_examen_liquidado, $examen_nombre_liquidado, "lista_examenes");
                    }
                }
                break;

            default:
                # code...
                break;
        }
    }
}

/**
 * [arreglar_procedimientos_bilaterales: Llega un código de procedimiento pero se encarga de revisar si tiene algún texto diferenciador
 *                                         usado por ejemplo cuando se liquidan bilaterales o se escoge una posición del organo,
 *                                         se elimina ese texto diferenciador para retornar el código exacto del procedimiento, si el código
 *                                         no tiene este tipo de diferenciadores entonces simplemente retorna el código normalmente]
 * @param  [type] $codigo_procedimiento [description]
 * @return [type]                       [description]
 */
function arreglar_procedimientos_bilaterales($codigo_procedimiento)
{
    $expl = explode("_", $codigo_procedimiento);
    if(count($expl) > 1)
    {
        $codigo_procedimiento = $expl[0];
    }
    return $codigo_procedimiento;
}


/**
 * [validar_material_medicamento_muevenInv: Función encargada de validar si un concepto que llega por parámetros es un concepto que mueve o no mueve inventarios]
 * @param  [String] $concepto_validar [Código de concepto a validar]
 * @return [Boolean]                  [Retorna true si es concepto que mueve inventario o false en caso contrario]
 */
function validar_material_medicamento_muevenInv($concepto_validar)
{
    global $concepto_medicamentos_mueven_inv, $concepto_materiales_mueven_inv;
    if($concepto_validar == $concepto_medicamentos_mueven_inv || $concepto_validar == $concepto_materiales_mueven_inv)
    {
        return true;
    }
    return false;
}

/**
 * [consultarConfiguracionPlantilla: Esta función se encarga de consultar y encontrar una plantilla adecuada para el procedimiento que se va a liquidar.
 *         Cuando se hacen paralelo puede haber la posibilidad de combinar hasta dos plantillas, para poder cobrar los conceptos adicionales que deba
 *         pagar la entidad que está haciendo el paralelo.
 *
 *         Se realizaron modificaciones para que esta misma función se encargue de consultar los conceptos que hacen parte de un PAQUETE siempre y cuando lo que
 *         se está realizando sea una liquidación de paquetes.]
 * @param  [index]  $conex                      [Conexión a la base de datos]
 * @param  [string] $wemp_pmla                  [Código de empresa Matrix-Promotora]
 * @param  [string] $wbasedato                  [Prefijo de las tablas a usar]
 * @param  [string] $procedimiento_liquidar_cod [Código de procedimiento a liquidar]
 * @param  [string] $procedimiento_bilat_dif    [Código del procedimiento con una marca diferenciadora, por ejemplo por posición del organo o bilateral]
 * @param  [array]  $data                       [log de respuesta AJAX - json]
 * @param  [array]  $arr_datos_liquidar         [Parametros generales de la liquidación ]
 * @param  [array]  $arr_conceptos_liquidar     [Conceptos preliquidados, aquí se puede estar vacío inicialmente y se puede ir complementando a medida que se lean plantillas o
 *                                                  se agreguen conceptos a la liquidación]
 * @param  [string] $wprocedimiento_add         [Código de procedimiento al que se le va a agregar un concepto, equipo o exámen de forma adicional]
 * @param  [string] $wconcepto_add              [Código de un concepto adicional que se va a agregar a la liquidación y que no esta en las plantillas]
 * @param  [array]  $arr_extras                 [Array de conceptos que se han agregado a la liquidación en curso. Cuando se da clic en liquidar varias veces, este array ayuda a recuperar
 *                                                  los conceptos que antes se han agregado y que no estan en plantillas]
 * @param  [array]  $arr_lista_conceptos        [Array de todos los conceptos que estan haciendo parte de la liquidación en curso, esto es para ayudar a crear la columna
 *                                                  de conceptos que se ve en la tabla de liquidación]
 * @param  [array]  $arr_conceptos_nombres      [description]
 * @param  [array]  $arr_datos_liquidados       [Este array contiene los procedimientos, conceptos, terceros, entre otros datos que se han generado temporalmente
 *                                                  después de haber daro clic en liquidar]
 * @param  [string] $wresponsable_eps_codigo    [Código de la empresa responsable del paciente]
 * @param  [string] $tarifa_original            [Código de la tarifa de la empresa responsable]
 * @param  [string] $tipoEmpresa                [Código del tipo de empresa responsable del paciente]
 * @param  [string] $wespecialidad              [Código de la especialidad del tercero encargado del procedimiento]
 * @param  [string] $es_paquete                 [Variable que indica con estado en on u off si la liquidación que se está haciendo corresponde
 *                                                  a una liquidación de paquetes o no]
 * @return [type]                               [Retorna array de conceptos a liquidar, parámetros de liquidación de cada concepto, lista de examenes y equipos]
 */
function consultarConfiguracionPlantilla($conex, $wemp_pmla, $wbasedato, $procedimiento_liquidar_cod, $procedimiento_bilat_dif, &$data, $arr_datos_liquidar, &$arr_conceptos_liquidar, $wprocedimiento_add, $wconcepto_add, &$arr_extras, &$arr_lista_conceptos, $arr_conceptos_nombres, $arr_datos_liquidados, $wresponsable_eps_codigo, $tarifa_original, $tipoEmpresa, $wespecialidad, $es_paquete)
{
    global $orden_conceptos_por_procedimiento, $id_concepto_uso_equipos, $id_concepto_examenes, $add_equipo, $add_examen, $add_equipo_nombre, $add_examen_nombre,
            $concepto_medicamentos_mueven_inv, $concepto_materiales_mueven_inv, $wfecha_cargo;

    if($es_paquete != 'on')
    {
        $plantila_encontrada = 'on';
        $TB_ENCABEZADO       = TB_ENCABEZADO;
        $arr_conf_liquidar = array();

        $variables = array();
        $variables['enc.Encent']['combinar'] = true;
        $variables['enc.Encent']['valor']    = $wresponsable_eps_codigo;

        $variables['enc.Encpro']['combinar']    = true;
        $variables['enc.Encpro']['valor']       = $procedimiento_liquidar_cod;
        $variables['enc.Encpro']['operador']    = "LIKE";
        $variables['enc.Encpro']['comodin_izq'] = "%";
        $variables['enc.Encpro']['comodin_der'] = "%";

        $variables['enc.Enctar']['combinar'] = true;
        $variables['enc.Enctar']['valor']    = $tarifa_original;

        $variables['enc.Encing']['combinar'] = true;
        $variables['enc.Encing']['valor']    = $tipoEmpresa;

        $variables['enc.Encesp']['combinar'] = true;
        $variables['enc.Encesp']['valor']    = $wespecialidad;

        $variables['enc.Encest']['combinar'] = false;
        $variables['enc.Encest']['valor']    = "on";

        // [*********************************************************************************************************************]
        // [************************************************ PASO 1 *************************************************************]
        // [*********************************************************************************************************************]
        // De acuerdo a parámetros generales se combinan para encontrar por lo menos una plantilla que aplique a la liquidación
        $sql = generarQueryCombinado($variables, "{$wbasedato}_{$TB_ENCABEZADO} AS enc");

        $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

        $plantila_encontrada = 'on';
        $id_plantilla_adecuada = "";
        if(mysql_num_rows($result) > 0)
        {
            $row_plantilla = mysql_fetch_array($result);
            $id_plantilla_adecuada = $row_plantilla['id'];
        }
        else
        {
            $plantila_encontrada = 'off';
        }

        // SI SE ENCONTRÓ ALGUNA PLANTILLA ENTONCES SE BUSCA EL RESTO DE CONFIGURACIÓN
        // Por ejemplo la forma de liquidar el concepto, si es por tiempo de uso, por horas, por tipo de anestesia.
        if($plantila_encontrada == 'on')
        {
            // [*********************************************************************************************************************]
            // [************************************************ PASO 2 *************************************************************]
            // [*********************************************************************************************************************]
            // Si en PASO 1 se encontró un registro, con el id de ese registro se consulta el resto de información en el siguiente QUERY
            // Consulta para ver si tiene plantilla configurada con entidad y procedimiento (*)
            $sql = "SELECT  Encpol AS plantilla_codigo, Encnom AS plantilla_nombre_politica, Encpro AS plantilla_wprocedimiento, Encent AS plantilla_wentidad
                    FROM    {$wbasedato}_{$TB_ENCABEZADO} AS enc
                    WHERE   id = '{$id_plantilla_adecuada}'";
            $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

            $row_plantilla                  = mysql_fetch_array($result);
            $plantilla_codigo               = $row_plantilla['plantilla_codigo'];
            $plantilla_nombre_politica      = $row_plantilla['plantilla_nombre_politica'];
            $plantilla_wprocedimiento       = $row_plantilla['plantilla_wprocedimiento'];
            $plantilla_wentidad             = $row_plantilla['plantilla_wentidad'];

            // [*********************************************************************************************************************]
            // [************************************************ PASO 3 *************************************************************]
            // [*********************************************************************************************************************]
            // Como la información de tipo de cobro de cada procedimiento está dividido en tres tablas diferentes, entonces en las siguientes consultas
            // se buscan los conceptos que se cobrar por cada uno de los "tipos de cobro"

            // [*********************************************************************************************************************]
            // [************************************************ PASO 3.1 ***********************************************************]
            // [*********************************************************************************************************************]
            // CONSULTA DE CONCEPTOS QUE SE COBRAN POR HORA

            // **** CONSULTAR LOS CONCEPTOS Y CONFIGURACIONES DE LA PLANTILLA ENCONTRADA ****
            // consulta si tiene configurado cobros por hora
            $sql = "SELECT  con.Grucod AS concepto_cod_ppal, con.Grudes AS concepto_nom_ppal, con.Grutip AS requiere_tercero, con.Grumva AS modifica_valor, con.Gruinv AS mueve_inventario, con.Gruarc AS tabla_valida_precios, con.Gruser AS wserv
                            , chora.Cphtmn AS tiempo_minimo, chora.Cphcph AS cobra_por_hora, chora.Cphcon AS cobro_concepto, chora.Cphpro AS cobro_procedimiento
                    FROM    {$wbasedato}_000181 AS chora
                            INNER JOIN
                            {$wbasedato}_000200 AS con ON (chora.Cphcpp = con.Grucod)
                    WHERE   chora.Cphpol = '{$plantilla_codigo}'
                            AND chora.Cphest = 'on'";
            // echo "<pre>"; print_r($sql); echo "</pre>";
            $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
            if(mysql_num_rows($result) > 0)
            {
                // $plantila_encontrada = 'off';
                while ($row = mysql_fetch_array($result))
                {
                    // Si en la plantilla esta guardado el procedimientos (*) significa que se cobra el procedimientos que se esté liquidando actualmente.
                    $cobro_procedimiento = $row['cobro_procedimiento'];
                    if($cobro_procedimiento == '*')
                    {
                        $cobro_procedimiento = $procedimiento_liquidar_cod;
                    }

                    $sql = "SELECT  proc.Procod AS procedimiento_cod,  proc.Pronom AS procedimiento_nom, proc.Protfa AS procedimiento_tarifa, proc.Progqx, proc.Propun as procedimiento_puntos
                            FROM    {$wbasedato}_000103 AS proc
                            WHERE   proc.Procod = '{$cobro_procedimiento}'
                                    AND proc.Proest = 'on'";
                    // echo "<pre>"; print_r($sql); echo "</pre>";
                    $resultDatProc = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

                    $rowDatProc = mysql_fetch_array($resultDatProc);
                    $row['procedimiento_cod']    = $rowDatProc['procedimiento_cod'];
                    $row['procedimiento_nom']    = $rowDatProc['procedimiento_nom'];
                    $row['procedimiento_tarifa'] = $rowDatProc['procedimiento_tarifa'];
                    $row['procedimiento_puntos'] = $rowDatProc['procedimiento_puntos'];
                    $row['Progqx'] = $rowDatProc['Progqx'];

                    // Valída si el concepto es material o medicamentos, solo se completan los parámetros
                    // de de cobro para si el concepto es diferente a material o medicamento, para estos dos conceptos se consultan
                    // sus tarifas o formas de cobro en otra rutina del programa.
                    if(!validar_material_medicamento_muevenInv($row['concepto_cod_ppal']))
                    {
                        if(!array_key_exists($row['concepto_cod_ppal'], $arr_conceptos_liquidar))
                        {
                            $cantidad_concepto = consultarCantidadesConceptosPlantilla($conex, $wbasedato, $plantilla_codigo, $row['concepto_cod_ppal']);
                            $arr_conceptos_liquidar[$row['concepto_cod_ppal']] = array( "cantidad_concepto"    => $cantidad_concepto[$row['concepto_cod_ppal']],
                                                                                        "wserv"                => $row['wserv'],
                                                                                        "tipo_cobro"           => "cobro_hora",
                                                                                        "requiere_tercero"     => $row["requiere_tercero"],
                                                                                        "tabla_valida_precios" => $row["tabla_valida_precios"],
                                                                                        "modifica_valor"       => $row["modifica_valor"],
                                                                                        "mueve_inventario"     => $row["mueve_inventario"],
                                                                                        "concepto_cod_ppal"    => $row['concepto_cod_ppal'],
                                                                                        "concepto_nom_ppal"    => $row['concepto_nom_ppal'],
                                                                                        "procedimiento_cod"    => $row['procedimiento_cod'],
                                                                                        "procedimiento_nom"    => $row['procedimiento_nom'],
                                                                                        "procedimiento_tarifa" => $row['procedimiento_tarifa'],
                                                                                        "procedimiento_puntos" => $row['procedimiento_puntos'],
                                                                                        "progqx_puntos_gqx"    => $row['Progqx'],
                                                                                        "tiempo_minimo"        => $row['tiempo_minimo'],
                                                                                        "cobra_por_hora"       => $row['cobra_por_hora'],
                                                                                        "cobro_concepto"       => $row['cobro_concepto'],
                                                                                        "cobro_procedimiento"  => $row['cobro_procedimiento'],
                                                                                        "arr_cobrar"           => array() );

                            if(array_key_exists($procedimiento_bilat_dif, $orden_conceptos_por_procedimiento) && !array_key_exists($row["concepto_cod_ppal"], $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif])) { $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif][$row["concepto_cod_ppal"]] = $row["concepto_cod_ppal"]; }

                            // Para cada concepto agregado se verifica si en segundo nivel se le deben agregar exámenes o equipos que deben ser asociados a ese concepto.
                            agregarEquiposExamenes($arr_conceptos_liquidar, $row["concepto_cod_ppal"], $procedimiento_bilat_dif, $procedimiento_liquidar_cod, $wprocedimiento_add, $arr_datos_liquidados);
                        }

                        if(!array_key_exists($row['concepto_cod_ppal'], $arr_lista_conceptos))
                        {
                            $arr_lista_conceptos[$row['concepto_cod_ppal']] = $row['concepto_nom_ppal'];
                        }
                    }
                    // $arr_conceptos_liquidar[$row['concepto_cod_ppal']]['arr_cobrar'][] =
                }
            }

            // [*********************************************************************************************************************]
            // [************************************************ PASO 3.2 ***********************************************************]
            // [*********************************************************************************************************************]
            // CONSULTA DE CONCEPTOS QUE SE COBRAN POR TIPO DE ANESTESIA

            // consulta si tiene configurado cobros por anestesia
            $sql = "SELECT  con.Grucod AS concepto_cod_ppal, con.Grudes AS concepto_nom_ppal, con.Grutip AS requiere_tercero, con.Grumva AS modifica_valor, con.Gruinv AS mueve_inventario, con.Gruarc AS tabla_valida_precios, con.Gruser AS wserv
                            , canest.Anecod AS tipo_anestesia, canest.Anetin AS tiempo_inicio, canest.Anetfn AS tiempo_final, canest.Anecon AS cobro_concepto, canest.Anepro AS cobro_procedimiento
                    FROM    {$wbasedato}_000182 AS canest
                            INNER JOIN
                            {$wbasedato}_000200 AS con ON (canest.Anecpp = con.Grucod)
                    WHERE   canest.Anepol = '{$plantilla_codigo}'
                            AND canest.Aneest = 'on'
                    ORDER BY canest.Anecod, canest.Anetin, canest.Anetfn";
            $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
            if(mysql_num_rows($result) > 0)
            {
                while ($row = mysql_fetch_array($result))
                {
                    $cobro_procedimiento = $row['cobro_procedimiento'];
                    if($cobro_procedimiento == '*')
                    {
                        $cobro_procedimiento = $procedimiento_liquidar_cod;
                    }

                    $sql = "SELECT  proc.Procod AS procedimiento_cod,  proc.Pronom AS procedimiento_nom, proc.Protfa AS procedimiento_tarifa, proc.Progqx, proc.Propun as procedimiento_puntos
                            FROM    {$wbasedato}_000103 AS proc
                            WHERE   proc.Procod = '{$cobro_procedimiento}'
                                    AND proc.Proest = 'on'";
                    // echo "<pre>"; print_r($sql); echo "</pre>";
                    $resultDatProc = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

                    $rowDatProc = mysql_fetch_array($resultDatProc);
                    $row['procedimiento_cod']    = $rowDatProc['procedimiento_cod'];
                    $row['procedimiento_nom']    = $rowDatProc['procedimiento_nom'];
                    $row['procedimiento_tarifa'] = $rowDatProc['procedimiento_tarifa'];
                    $row['procedimiento_puntos'] = $rowDatProc['procedimiento_puntos'];
                    $row['Progqx'] = $rowDatProc['Progqx'];

                    // Valída si el concepto es material o medicamentos, solo se completan los parámetros
                    // de de cobro para si el concepto es diferente a material o medicamento, para estos dos conceptos se consultan
                    // sus tarifas o formas de cobro en otra rutina del programa.
                    if(!validar_material_medicamento_muevenInv($row['concepto_cod_ppal']))
                    {
                        if(!array_key_exists($row['concepto_cod_ppal'], $arr_conceptos_liquidar))
                        {
                            $cantidad_concepto = consultarCantidadesConceptosPlantilla($conex, $wbasedato, $plantilla_codigo, $row['concepto_cod_ppal']);
                            $arr_conceptos_liquidar[$row['concepto_cod_ppal']] = array( "cantidad_concepto"    => $cantidad_concepto[$row['concepto_cod_ppal']],
                                                                                        "wserv"                => $row['wserv'],
                                                                                        "tipo_cobro"           => "cobro_anestesia",
                                                                                        "requiere_tercero"     => $row["requiere_tercero"],
                                                                                        "tabla_valida_precios" => $row["tabla_valida_precios"],
                                                                                        "modifica_valor"       => $row["modifica_valor"],
                                                                                        "mueve_inventario"     => $row["mueve_inventario"],
                                                                                        "concepto_cod_ppal"    => $row['concepto_cod_ppal'],
                                                                                        "concepto_nom_ppal"    => $row['concepto_nom_ppal'],
                                                                                        "procedimiento_cod"    => $row['procedimiento_cod'],
                                                                                        "procedimiento_nom"    => $row['procedimiento_nom'],
                                                                                        "procedimiento_tarifa" => $row['procedimiento_tarifa'],
                                                                                        "procedimiento_puntos" => $row['procedimiento_puntos'],
                                                                                        "progqx_puntos_gqx"    => $row['Progqx'],
                                                                                        "rangos"               => array() );

                            if(array_key_exists($procedimiento_bilat_dif, $orden_conceptos_por_procedimiento) && !array_key_exists($row["concepto_cod_ppal"], $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif])) { $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif][$row["concepto_cod_ppal"]] = $row["concepto_cod_ppal"]; }

                            // Para cada concepto agregado se verifica si en segundo nivel se le deben agregar exámenes o equipos que deben ser asociados a ese concepto.
                            agregarEquiposExamenes($arr_conceptos_liquidar, $row["concepto_cod_ppal"], $procedimiento_bilat_dif, $procedimiento_liquidar_cod, $wprocedimiento_add, $arr_datos_liquidados);
                        }

                        if(!array_key_exists($row['concepto_cod_ppal'], $arr_lista_conceptos))
                        {
                            $arr_lista_conceptos[$row['concepto_cod_ppal']] = $row['concepto_nom_ppal'];
                        }

                        $arr_conceptos_liquidar[$row['concepto_cod_ppal']]['rangos'][] = array( "tipo_anestesia"      => $row['tipo_anestesia'],
                                                                                                "tiempo_inicio"       => $row['tiempo_inicio'],
                                                                                                "tiempo_final"        => $row['tiempo_final'],
                                                                                                "cobro_concepto"      => $row['cobro_concepto'],
                                                                                                "cobro_procedimiento" => $row['cobro_procedimiento']);
                    }
                }
            }
            // echo "<pre>"; print_r($arr_conceptos_liquidar); echo "</pre>";

            // [*********************************************************************************************************************]
            // [************************************************ PASO 3.3 ***********************************************************]
            // [*********************************************************************************************************************]
            // CONSULTA DE CONCEPTOS QUE SE COBRAN POR TIEMPO DE USO (Por ejemplo tiempo de uso de equipos)

            $tb_uso = TB_COBRO_USO;
            // consulta si tiene configurado cobros por uso
            $sql = "SELECT  con.Grucod AS concepto_cod_ppal, con.Grudes AS concepto_nom_ppal, con.Grutip AS requiere_tercero, con.Grumva AS modifica_valor, con.Gruinv AS mueve_inventario, con.Gruarc AS tabla_valida_precios, con.Gruser AS wserv
                            , cuso.Usotmn AS tiempo_inicio, cuso.Usotfn AS tiempo_final, cuso.Usocon AS cobro_concepto, cuso.Usopro AS cobro_procedimiento
                    FROM    {$wbasedato}_{$tb_uso} AS cuso
                            INNER JOIN
                            {$wbasedato}_000200 AS con ON (cuso.Usocpp = con.Grucod)
                    WHERE   cuso.Usopol = '{$plantilla_codigo}'
                            AND cuso.Usoest = 'on'";
            $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
            if(mysql_num_rows($result) > 0)
            {
                while ($row = mysql_fetch_array($result))
                {
                    $cobro_procedimiento = $row['cobro_procedimiento'];
                    if($cobro_procedimiento == '*')
                    {
                        $cobro_procedimiento = $procedimiento_liquidar_cod;
                    }

                    $sql = "SELECT  proc.Procod AS procedimiento_cod,  proc.Pronom AS procedimiento_nom, proc.Protfa AS procedimiento_tarifa, proc.Progqx, proc.Propun as procedimiento_puntos
                            FROM    {$wbasedato}_000103 AS proc
                            WHERE   proc.Procod = '{$cobro_procedimiento}'
                                    AND proc.Proest = 'on'";
                    // echo "<pre>"; print_r($sql); echo "</pre>";
                    $resultDatProc = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

                    $rowDatProc = mysql_fetch_array($resultDatProc);
                    $row['procedimiento_cod']    = $rowDatProc['procedimiento_cod'];
                    $row['procedimiento_nom']    = $rowDatProc['procedimiento_nom'];
                    $row['procedimiento_tarifa'] = $rowDatProc['procedimiento_tarifa'];
                    $row['procedimiento_puntos'] = $rowDatProc['procedimiento_puntos'];
                    $row['Progqx'] = $rowDatProc['Progqx'];

                    // Valída si el concepto es material o medicamentos, solo se completan los parámetros
                    // de de cobro para si el concepto es diferente a material o medicamento, para estos dos conceptos se consultan
                    // sus tarifas o formas de cobro en otra rutina del programa.
                    if(!validar_material_medicamento_muevenInv($row['concepto_cod_ppal']))
                    {
                        if(!array_key_exists($row['concepto_cod_ppal'], $arr_conceptos_liquidar))
                        {
                            $cantidad_concepto = consultarCantidadesConceptosPlantilla($conex, $wbasedato, $plantilla_codigo, $row['concepto_cod_ppal']);
                            $arr_conceptos_liquidar[$row['concepto_cod_ppal']] = array( "cantidad_concepto"    => $cantidad_concepto[$row['concepto_cod_ppal']],
                                                                                        "wserv"                => $row['wserv'],
                                                                                        "tipo_cobro"           => "cobro_uso",
                                                                                        "requiere_tercero"     => $row["requiere_tercero"],
                                                                                        "tabla_valida_precios" => $row["tabla_valida_precios"],
                                                                                        "modifica_valor"       => $row["modifica_valor"],
                                                                                        "mueve_inventario"     => $row["mueve_inventario"],
                                                                                        "concepto_cod_ppal"    => $row['concepto_cod_ppal'],
                                                                                        "concepto_nom_ppal"    => $row['concepto_nom_ppal'],
                                                                                        "procedimiento_cod"    => $row['procedimiento_cod'],
                                                                                        "procedimiento_nom"    => $row['procedimiento_nom'],
                                                                                        "procedimiento_tarifa" => $row['procedimiento_tarifa'],
                                                                                        "procedimiento_puntos" => $row['procedimiento_puntos'],
                                                                                        "progqx_puntos_gqx"    => $row['Progqx'],
                                                                                        "rangos"               => array() );

                            if(array_key_exists($procedimiento_bilat_dif, $orden_conceptos_por_procedimiento) && !array_key_exists($row["concepto_cod_ppal"], $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif])) { $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif][$row["concepto_cod_ppal"]] = $row["concepto_cod_ppal"]; }

                            // Para cada concepto agregado se verifica si en segundo nivel se le deben agregar exámenes o equipos que deben ser asociados a ese concepto.
                            agregarEquiposExamenes($arr_conceptos_liquidar, $row["concepto_cod_ppal"], $procedimiento_bilat_dif, $procedimiento_liquidar_cod, $wprocedimiento_add, $arr_datos_liquidados);
                        }

                        if(!array_key_exists($row['concepto_cod_ppal'], $arr_lista_conceptos))
                        {
                            $arr_lista_conceptos[$row['concepto_cod_ppal']] = $row['concepto_nom_ppal'];
                        }

                        $arr_conceptos_liquidar[$row['concepto_cod_ppal']]['rangos'][] = array( "tiempo_inicio"       => $row['tiempo_inicio'],
                                                                                                "tiempo_final"        => $row['tiempo_final'],
                                                                                                "cobro_concepto"      => $row['cobro_concepto'],
                                                                                                "cobro_procedimiento" => $row['cobro_procedimiento']);
                    }
                }
            }
        }
        else
        {
            // $descripcion = "(".mysql_errno().') '.mysql_error()."|obs:'Error al cambiar el estado de aprobación de la solicitud de id. ".$wid_solpend;
            // insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wcedula, $sql);
            $data['mensaje'] .= trim('No se encontró una plantilla configurada para iniciar la liquidación.
                                Procedimiento: '.$procedimiento_liquidar_cod);
            // $data['error'] = 1;
        }

        // echo "$wprocedimiento_add && $wconcepto_add ||";
        // Si es un concepto que no está en la plantilla y se está agregando desde la vista de liquidación
        // $wprocedimiento_add, $wconcepto_add
        // $guardar = ": if($wprocedimiento_add != '' && $procedimiento_bilat_dif == $wprocedimiento_add && $wconcepto_add != '' && !array_key_exists($wconcepto_add, $arr_conceptos_liquidar)) ".PHP_EOL;
        // seguimiento($guardar);
        if(!validar_material_medicamento_muevenInv($wconcepto_add) && $wprocedimiento_add != '' && $procedimiento_bilat_dif == $wprocedimiento_add && $wconcepto_add != '' && !array_key_exists($wconcepto_add, $arr_conceptos_liquidar))
        {
            // Se guarda en el array de extras los conceptos que se agregan casualmente a la ventana de liquidación y que no hacen parte de la plantilla
            // Enseguida de recorre el array de extras para agregar a la configuración de la liquidación los conceptos adicionales y se pueda agregar a los conceptos de la lista del procedimiento
            // que se está liquidando.
            if(!array_key_exists($wprocedimiento_add, $arr_extras))
            {
                $arr_extras[$wprocedimiento_add] = array();
            }

            if(!array_key_exists($wconcepto_add, $arr_lista_conceptos))
            {
                $arr_lista_conceptos[$wconcepto_add] = $arr_conceptos_nombres[$wconcepto_add];
            }

            if(!array_key_exists($wconcepto_add, $arr_extras[$wprocedimiento_add]))
            {
                $arr_extras[$wprocedimiento_add][$wconcepto_add] = array();

                $codigo_proced = arreglar_procedimientos_bilaterales($wprocedimiento_add);

                $sql = "SELECT  proc.Procod AS procedimiento_cod,  proc.Pronom AS procedimiento_nom, proc.Protfa AS procedimiento_tarifa, proc.Progqx, proc.Propun as procedimiento_puntos
                        FROM    {$wbasedato}_000103 AS proc
                        WHERE   proc.Procod = '{$codigo_proced}'
                                AND proc.Proest = 'on'";
                $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                $rowPro = mysql_fetch_array($result);

                $sql = "SELECT  con.Grucod AS concepto_cod_ppal, con.Grudes AS concepto_nom_ppal, con.Grutip AS requiere_tercero, con.Grumva AS modifica_valor, con.Gruinv AS mueve_inventario, con.Gruarc AS tabla_valida_precios, con.Gruser AS wserv
                        FROM    {$wbasedato}_000200 AS con
                        WHERE   con.Grucod = '{$wconcepto_add}'";
                $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                $rowCon = mysql_fetch_array($result);

                $arr_extras[$wprocedimiento_add][$wconcepto_add] = array(   "cantidad_concepto"    => 1,
                                                                            "wserv"                => $rowCon['wserv'],
                                                                            "tipo_cobro"           => "",
                                                                            "requiere_tercero"     => $rowCon["requiere_tercero"],
                                                                            "tabla_valida_precios" => $rowCon["tabla_valida_precios"],
                                                                            "modifica_valor"       => $rowCon["modifica_valor"],
                                                                            "mueve_inventario"     => $rowCon["mueve_inventario"],
                                                                            "concepto_cod_ppal"    => $wconcepto_add,
                                                                            "concepto_nom_ppal"    => $rowCon['concepto_nom_ppal'],
                                                                            "procedimiento_cod"    => $rowPro['procedimiento_cod'],
                                                                            "procedimiento_nom"    => $rowPro['procedimiento_nom'],
                                                                            "procedimiento_tarifa" => $rowPro['procedimiento_tarifa'],
                                                                            "procedimiento_puntos" => $rowPro['procedimiento_puntos'],
                                                                            "progqx_puntos_gqx"    => $rowPro['Progqx'],
                                                                            "cobro_concepto"       => $wconcepto_add,
                                                                            "cobro_procedimiento"  => $codigo_proced,
                                                                            "arr_cobrar"           => array(),
                                                                            "es_concepto_extra"    => true,
                                                                            "lista_equipos"        => array(),
                                                                            "lista_examenes"       => array());
                // agregarEquiposExamenes($arr_conceptos_liquidar, $wconcepto_add, $procedimiento_bilat_dif, $procedimiento_liquidar_cod, $wprocedimiento_add, $arr_datos_liquidados);

                $arr_lista_conceptos[$wconcepto_add] = $rowCon['concepto_nom_ppal'];

                if(!array_key_exists($wconcepto_add, $orden_conceptos_por_procedimiento[$wprocedimiento_add])) { $orden_conceptos_por_procedimiento[$wprocedimiento_add][$wconcepto_add] = $wconcepto_add; }

            }
        }

        // print_r($arr_extras);
        // $guardar = "procedimiento_bilat_dif: $procedimiento_bilat_dif ".print_r($arr_extras,true).PHP_EOL;
        // seguimiento($guardar);
        if(array_key_exists($procedimiento_bilat_dif, $arr_extras))
        {
            foreach ($arr_extras[$procedimiento_bilat_dif] as $wconcepto => $arr_add)
            {
                // Adicionar el concepto extra a la lista de conceptos que se mostrarán por cada fila en la liquidación.
                if(!array_key_exists($wconcepto, $arr_lista_conceptos))
                {
                    $arr_lista_conceptos[$wconcepto] = $arr_conceptos_nombres[$wconcepto];
                }

                if(!array_key_exists($wconcepto, $arr_conceptos_liquidar))
                {
                    $arr_conceptos_liquidar[$wconcepto] = $arr_add;
                    agregarEquiposExamenes($arr_conceptos_liquidar, $wconcepto, $procedimiento_bilat_dif, $procedimiento_liquidar_cod, $wprocedimiento_add, $arr_datos_liquidados);
                }
                else
                {
                    // unset($arr_extras[$procedimiento_bilat_dif][$wconcepto]);
                }
            }
        }
    }
    else
    {
        // [*********************************************************************************************************************]
        // [************************************* SOLO SI ES LIQUIDACIÓN DE PAQUETES  *******************************************]
        // [*********************************************************************************************************************]
        $res_paquete = consultarPaquete($conex, $wbasedato, $wemp_pmla, $procedimiento_liquidar_cod, $tarifa_original);

        $arr_datos_procedimiento = array();
        while($row = mysql_fetch_array($res_paquete))
        {
            if(count($arr_datos_procedimiento) == 0)
            {
                $sql = "SELECT  proc.Procod AS procedimiento_cod,  proc.Pronom AS procedimiento_nom, proc.Protfa AS procedimiento_tarifa, proc.Progqx, proc.Propun as procedimiento_puntos
                        FROM    {$wbasedato}_000103 AS proc
                        WHERE   proc.Procod = '{$procedimiento_liquidar_cod}'
                                AND proc.Proest = 'on'";
                // echo "<pre>"; print_r($sql); echo "</pre>";
                $resultDatProc = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

                $rowDatProc = mysql_fetch_array($resultDatProc);
                $arr_datos_procedimiento['procedimiento_cod']    = $row['procedimiento_cod'];
                $arr_datos_procedimiento['procedimiento_nom']    = $rowDatProc['procedimiento_nom'];
                $arr_datos_procedimiento['procedimiento_tarifa'] = $rowDatProc['procedimiento_tarifa'];
                $arr_datos_procedimiento['procedimiento_puntos'] = $rowDatProc['procedimiento_puntos'];
                $arr_datos_procedimiento['progqx_puntos_gqx']    = $rowDatProc['Progqx'];
            }

            if (strtotime($wfecha_cargo) < strtotime($row['Paqdetfec']))
            {
                $wvaltar = $row['Paqdetvan'];
            }
            else
            {
                $wvaltar = $row['Paqdetvac'];
            }

            if(!array_key_exists($row['concepto_cod_ppal'], $arr_conceptos_liquidar))
            {
                // $cantidad_concepto = consultarCantidadesConceptosPlantilla($conex, $wbasedato, $plantilla_codigo, $row['concepto_cod_ppal']);
                $arr_conceptos_liquidar[$row['concepto_cod_ppal']] = array( "cantidad_concepto"    => $row['wcantidad'],
                                                                            "wserv"                => $row['wserv'],
                                                                            "tipo_cobro"           => "N/A",
                                                                            "requiere_tercero"     => $row["requiere_tercero"],
                                                                            "tabla_valida_precios" => $row["tabla_valida_precios"],
                                                                            "modifica_valor"       => $row["modifica_valor"],
                                                                            "mueve_inventario"     => $row["mueve_inventario"],
                                                                            "concepto_cod_ppal"    => $row['concepto_cod_ppal'],
                                                                            "concepto_nom_ppal"    => $row['concepto_nom_ppal'],
                                                                            "procedimiento_cod"    => $arr_datos_procedimiento['procedimiento_cod'], // Puede ser codigo de pricedimiento o de equipo
                                                                            "procedimiento_nom"    => $arr_datos_procedimiento['procedimiento_nom'],
                                                                            "procedimiento_tarifa" => $arr_datos_procedimiento['procedimiento_tarifa'],
                                                                            "procedimiento_puntos" => $arr_datos_procedimiento['procedimiento_puntos'],
                                                                            "progqx_puntos_gqx"    => $arr_datos_procedimiento['progqx_puntos_gqx'],
                                                                            "tiempo_minimo"        => "N/A",
                                                                            "cobra_por_hora"       => "N/A",
                                                                            "cobro_concepto"       => $row['concepto_cod_ppal'],
                                                                            "cobro_procedimiento"  => $row['procedimiento_cod'],
                                                                            "wtercero_default"     => $row['wtercero'],
                                                                            "wfacturable_default"  => $row['wfacturable'],
                                                                            "wvaltar"              => $wvaltar,
                                                                            "arr_cobrar"           => array() );

                if(!array_key_exists($row['concepto_cod_ppal'], $arr_lista_conceptos))
                {
                    $arr_lista_conceptos[$row['concepto_cod_ppal']] = $row['concepto_nom_ppal'];
                }

                if(!array_key_exists($row["concepto_cod_ppal"], $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif])) { $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif][$row["concepto_cod_ppal"]] = $row["concepto_cod_ppal"]; }
                // agregarEquiposExamenes($arr_conceptos_liquidar, $row["concepto_cod_ppal"], $procedimiento_bilat_dif, $procedimiento_liquidar_cod, $wprocedimiento_add, $arr_datos_liquidados);
            }
        }

    }
}

/**
 * [consultarPaquete: Función encargada de consultar los paquetes configurados para un procedimiento y tarifa y con esto saber qué conceptos
 *                     conforman el paquete]
 * @param  [index]  $conex                      [Conexión con la base de datos]
 * @param  [string] $wbasedato                  [Prefijo de las tablas a usar]
 * @param  [string] $wemp_pmla                  [Código de empresa de promotora]
 * @param  [string] $procedimiento_liquidar_cod [Código del procedimiento para paquetes asociados]
 * @param  [string] $tarifa_original            [Código de tarífa para filtrar los paquetes que correspondan a esa tarífa]
 * @return [index]                              [Índice de respuesta a la consulta de paquetes encontrados]
 */
function consultarPaquete($conex, $wbasedato, $wemp_pmla, $procedimiento_liquidar_cod, $tarifa_original)
{
    $q_paquete1 = " SELECT  Grucod AS concepto_cod_ppal, Grudes AS concepto_nom_ppal, Gruarc AS tabla_valida_precios, Gruser AS wserv, Grutip AS requiere_tercero, Grumva AS modifica_valor, Gruinv AS mueve_inventario, Gruabo AS es_abono, Grutab AS tipo_abono,
                            Propun as procedimiento_puntos, Grumca AS mueve_caja, Pronom AS procedimiento_nom, Paqdetcan AS wcantidad, Paqdetvan, Paqdetvac, Paqdetfec, Paqdetpro AS procedimiento_cod, Paqdetter AS wtercero, Paqdetpai, Paqdetfac AS wfacturable
                    FROM    {$wbasedato}_000114, {$wbasedato}_000200, {$wbasedato}_000103
                    WHERE   Paqdetcod        = '{$procedimiento_liquidar_cod}'
                            AND  TRIM(paqdettar)  = '{$tarifa_original}'
                            AND  Paqdetest        = 'on'
                            AND  Paqdetcon        = Grucod
                            AND  Paqdetpro        = Procod
                            AND  (Grutpr = '*' OR Grutpr = Protip)
                            AND  Proest = 'on'
                    ORDER BY  Grudes";
    $res_paquete    = mysql_query($q_paquete1,$conex) or die("Error en el query: ".$q_paquete1."<br>Tipo Error:".mysql_error());
    $num_detalles   = mysql_num_rows($res_paquete);
    // $guardar = "q_paquete1: ".print_r($q_paquete1,true).PHP_EOL;
    // seguimiento($guardar);
    // --> Si no existe un detalle para la tarifa, entonces traigo el detalle de la tarifa general (*).
    if($num_detalles < 1)
    {
        $q_paquete2 = " SELECT  Grucod AS concepto_cod_ppal, Grudes AS concepto_nom_ppal, Gruarc AS tabla_valida_precios, Gruser AS wserv, Grutip AS requiere_tercero, Grumva AS modifica_valor, Gruinv AS mueve_inventario, Gruabo AS es_abono, Grutab AS tipo_abono,
                                Propun as procedimiento_puntos, Grumca AS mueve_caja, Pronom AS procedimiento_nom, Paqdetcan AS wcantidad, Paqdetvan, Paqdetvac, Paqdetfec, Paqdetpro AS procedimiento_cod, Paqdetter AS wtercero, Paqdetpai, Paqdetfac AS wfacturable
                        FROM    {$wbasedato}_000114, {$wbasedato}_000200, {$wbasedato}_000103
                        WHERE   Paqdetcod        = '{$procedimiento_liquidar_cod}'
                                AND  TRIM(Paqdettar)  = '*'
                                AND  Paqdetest        = 'on'
                                AND  Paqdetcon        = Grucod
                                AND  Paqdetpro        = Procod
                                AND  (Grutpr = '*' OR Grutpr = Protip)
                                AND  Proest = 'on'
                        ORDER BY  Grudes";
        $res_paquete    = mysql_query($q_paquete2,$conex) or die("Error en el query: ".$q_paquete2."<br>Tipo Error:".mysql_error());
    }
    return $res_paquete;
}

/**
 * [listarDetalleLiquidaciones: * Función encargada de consultar el detalle de las liquidaciones de cirugías asociadas a una historia e ingreso de un paciente,
 *                                 esta función no mostrará los cargos que estén anulados.
 *                              * Se cosultan todos los eventos quirúrgicos activos realizados al paciente según la historia e ingreso consultado]
 * @param  [index]  $conex          [Conexión a la base de datos]
 * @param  [string] $wemp_pmla      [Código de empresa de promotora]
 * @param  [string] $wbasedato      [Prefijo de las tablas a consultar]
 * @param  [int]    $whistoria      [Número de historia del paciente que se va a consultar]
 * @param  [int]    $wing           [Número de ingreso de paciente asociado a la historia que se va a consultar]
 * @param  [array]  $arr_parametros [Array de parámetros generales para mostrar en el detalle de la liquidación]
 * @return [array]                  [Array con la información detallada almacenada en el log de liquidaciones de cirugía asociadas a una historia e ingreso, actos quirúrgicos del paciente]
 */
function listarDetalleLiquidaciones($conex, $wemp_pmla, $wbasedato, $whistoria, $wing, &$arr_parametros)
{
    $arr_parametros = array("arr_procedimientos" => array(), "arr_conceptos" => array(), "arr_terceros" => array(), "arr_bases_liquidacion" => array());
    $html = "";
    $TB_LIQUIDACIONES   = TB_LIQUIDACIONES;
    $TB_ENC_LIQUIDACION = TB_ENC_LIQUIDACION;
    $sql = "SELECT  t198.Liqcaq AS wdiferencia_acto_qx, t198.Liqhis AS whistoria, t198.Liqing AS wingreso, t198.Liqdoc AS wdocumento, t198.Liqpro AS wprocedimiento, t198.Liqcon AS wconcepto, t198.Liqter AS wtercero,
                    t198.Liqesp AS wespecialidad, t198.Liqpor AS wporcentaje, t198.Liqvlr AS wvalor, t198.Liqvlf AS wvalor_final, t198.Liqfac AS wfacturable, t198.Liqgra AS wgrabar, t198.Liqblq AS wbaseliquidacion, t198.Liqtta AS wtipo_facturacion,
                    t198.Fecha_data, t198.Hora_data, t198.Liqfca AS wfecha_cargo, t198.Liqhca AS whora_cargo,
                    t106.Tcarpronom AS wnombre_guardado, t106.id AS id_cargo_106, t198.id AS id_cargo_198
            FROM    {$wbasedato}_{$TB_ENC_LIQUIDACION} AS t199
                    INNER JOIN
                    {$wbasedato}_{$TB_LIQUIDACIONES} AS t198 ON (t198.Liqhis = t199.Enlhis AND t198.Liqing = t199.Enling AND t198.Liqdoc = t199.Enldoc AND t198.liqcaq = t199.Enlcaq)
                    INNER JOIN
                    {$wbasedato}_000106 AS t106 ON (t198.Liqidc = t106.id)
            WHERE   t199.Enlhis = '{$whistoria}'
                    AND t199.Enling = '{$wing}'
                    AND t198.Liqest = 'on'
                    AND t198.Liqgra = 'on'
                    AND t106.Tcarest = 'on'
                    AND t199.Enlpqt = 'off'
            ORDER BY t198.Liqcaq DESC, t198.Liqfca ASC, wdiferencia_acto_qx, t198.Liqpro, Hora_data, t198.Liqcon";
    $result = mysql_query($sql,$conex) OR die(mysql_errno().' - '.mysql_error().' > '.$sql);

    // echo "<div style='text-align:left'><br><pre>"; print_r($sql); echo "</pre></div>";

    $arr_liquidados = array();
    while($row = mysql_fetch_array($result))
    {
        if(!array_key_exists($row['wdiferencia_acto_qx'], $arr_liquidados))
        {
            $arr_liquidados[$row['wdiferencia_acto_qx']] = array("Fecha_data"           => $row['Fecha_data'],
                                                                    "Hora_data"         => $row['Hora_data'],
                                                                    "wingreso"          => $row['wingreso'],
                                                                    "wfecha_cargo"      => $row['wfecha_cargo'],
                                                                    "whora_cargo"       => $row['whora_cargo'],
                                                                    "arr_liquidaciones" => array());
        }

        $arr_liquidados[$row['wdiferencia_acto_qx']]['arr_liquidaciones'][] = array(
                                                                    "Fecha_data"        => $row['Fecha_data'],
                                                                    "Hora_data"         => $row['Hora_data'],
                                                                    "wingreso"          => $row['wingreso'],
                                                                    "wfecha_cargo"      => $row['wfecha_cargo'],
                                                                    "whora_cargo"       => $row['whora_cargo'],
                                                                    "wprocedimiento"    => $row['wprocedimiento'],
                                                                    "wprocedimiento_nom"=> $row['wnombre_guardado'],
                                                                    "wconcepto"         => $row['wconcepto'],
                                                                    "wtercero"          => $row['wtercero'],
                                                                    "wespecialidad"     => $row['wespecialidad'],
                                                                    "wporcentaje"       => $row['wporcentaje'],
                                                                    "wvalor"            => $row['wvalor'],
                                                                    "wvalor_final"      => $row['wvalor_final'],
                                                                    "wfacturable"       => $row['wfacturable'],
                                                                    "wbaseliquidacion"  => $row['wbaseliquidacion'],
                                                                    "wtipo_facturacion" => $row['wtipo_facturacion'],
                                                                    "id_cargo_106"      => $row['id_cargo_106'],
                                                                    "id_cargo_198"      => $row['id_cargo_198']);

        // Procedimientos
        if(!array_key_exists($row['wprocedimiento'], $arr_parametros['arr_procedimientos'])){ $arr_parametros['arr_procedimientos'][$row['wprocedimiento']] = $row['wnombre_guardado']; }
        // Conceptos
        if(!array_key_exists($row['wconcepto'], $arr_parametros['arr_conceptos'])){ $arr_parametros['arr_conceptos'][$row['wconcepto']] = $row['wconcepto']; }
        // Terceros
        // if(!array_key_exists($row['wtercero'], $arr_parametros['arr_terceros'])){ $arr_parametros['arr_terceros'][$row['wtercero']] = $row['wtercero']; }
        // Especialidades
        // if(!array_key_exists($row['wespecialidad'], $arr_parametros['arr_especialidades'])){ $arr_parametros['arr_especialidades'][$row['wespecialidad']] = $row['wespecialidad']; }
        // Bases liquidación
        if(!array_key_exists($row['wbaseliquidacion'], $arr_parametros['arr_bases_liquidacion'])){ $arr_parametros['arr_bases_liquidacion'][$row['wbaseliquidacion']] = $row['wbaseliquidacion']; }
    }

    if(count($arr_parametros) > 0)
    {
        $sql = "SELECT  Grucod AS codigo, Grudes AS nombre
                FROM    {$wbasedato}_000200
                WHERE   Grucod IN ('".implode("','", $arr_parametros['arr_conceptos'])."')";
        $result = mysql_query($sql,$conex) OR die(mysql_errno().' - '.mysql_error().' > '.$sql);
        while ($row = mysql_fetch_array($result))
        {
            $arr_parametros['arr_conceptos'][$row['codigo']] = $row['nombre'];
        }

        $sql = "SELECT  Blqclq AS codigo, Blqdes AS nombre
                FROM    {$wbasedato}_".TB_BASE_LIQUIDACION."
                WHERE   Blqclq IN ('".implode("','", $arr_parametros['arr_bases_liquidacion'])."')";
        $result = mysql_query($sql,$conex) OR die(mysql_errno().' - '.mysql_error().' > '.$sql);
        while ($row = mysql_fetch_array($result))
        {
            $arr_parametros['arr_bases_liquidacion'][$row['codigo']] = $row['nombre'];
        }
    }

    return $arr_liquidados;
}

/**
 * [pintarDetalleLiquidaciones: Esta función se encarga de recorrer el array generado en la función "listarDetalleLiquidaciones" y genera el código html para
 *                             mostrarlo en la pantalla del usuario que esta consultando el detalle de liquidaciones para un paciente determinado]
 * @param  [array] $arr_detalle_liquidaciones [Array con todos los parámetros del detalle de liquidaciones]
 * @param  [array] $arr_parametros            [Array con otros parámetros generales para mostrar en el detalle, por ejemplo los terceros involucrados en el procedimiento]
 * @return [html]                             [Texto en código html para mostrar en la interfaz de usuario]
 */
function pintarDetalleLiquidaciones($arr_detalle_liquidaciones, $arr_parametros)
{
    $html_actos = "";
    // Recorre todos los actos quirúrgicos realizados al paciente
    foreach ($arr_detalle_liquidaciones as $dif_acto => $arr_acto)
    {
        $whora_cargo  = $arr_acto['whora_cargo'];
        $wfecha_cargo = $arr_acto['wfecha_cargo'];
        $wfecha_cargo_exp = explode('-',$arr_acto['wfecha_cargo']);
        $anio = $wfecha_cargo_exp[0];
        $mex  = $wfecha_cargo_exp[1];
        $diax = $wfecha_cargo_exp[2];
        $nombre_dia = nombreDiaSemana($anio, $mex, $diax);
        $html_actos .= '<div id="div_contenedor_evento_qx_'.$dif_acto.'" class="margen-superior-eventos">
                            <div class="" style="width:100%;text-align:left;"><span class="encabezadoTabla">Evento Quirurgico: </span><span class="encabezadoTabla">'.$nombre_dia.' '.$wfecha_cargo.' '.$whora_cargo.' </span>&nbsp;<button style="font-size:9pt;" onclick="verOcultarLista(\'div_tabla_lista_cxs_'.$dif_acto.'\');">Ver / Ocultar cargos</button> <button class="alinear_derecha" style="font-size:9pt;" id="boton_add_cx" class="btn_loading" onclick="anularActoQuirurgico(\'hddn_cargo_anular_\',\''.$dif_acto.'\');" >Anular</button></div>
                            <div style="display:none; width:100%; height: 200px; overflow:auto;" id="div_tabla_lista_cxs_'.$dif_acto.'" >
                            <table id="tabla_lista_cxs_'.$dif_acto.'" align="center" width="100%;">
                                <tr class="encabezadoTabla">
                                <td style="text-align:center;">'.PROCEDIMIENTO.'</td>
                                <td style="text-align:center;">'.CONCEPTO_LABEL.'</td>
                                <td style="text-align:center;">'.TERCERO_LABEL.'</td>
                                <td style="text-align:center;">'.ESPECILALIDAD.'</td>
                                <td style="text-align:center;">%Liq.</td>
                                <!-- <td style="text-align:center;">Valor</td> -->
                                <td style="text-align:center;">Total</td>
                                <td style="text-align:center;">Facturable</td>
                                <td style="text-align:center;">Base Liq.</td>
                                <td style="text-align:center;">Tipo facturación</td>
                                <!-- <td style="text-align:center;">Anular <input type="checkbox" id="chk_anular_todos" name="chk_anular_todos" value="" ></td> -->
                            </tr>';

        // $arr_parametros = array("arr_procedimientos" => array(), "arr_conceptos" => array(), "arr_terceros" => array(), "arr_bases_liquidacion" => array());
        $sumatoria_total = 0;
        $cont = 0;
        // Por cada evento quirúrgico muestra los conceptos asociados a la liquidación
        foreach ($arr_acto['arr_liquidaciones'] as $key => $arr_registro)
        {
            $tercero = '';
            $especialidad = '';
            $css = ($cont % 2 == 0) ? 'fila1': 'fila2';

            if($arr_registro['wtercero'] != '')
            {
                $tercero = $arr_registro['wtercero'].'-'.$arr_parametros['arr_terceros'][$arr_registro['wtercero']]['nombre'];
                $exp_especialidades = explode(',', $arr_parametros['arr_terceros'][$arr_registro['wtercero']]['especialidad']);
                foreach ($exp_especialidades as $keyesp => $value_esps)
                {
                    $exp_esp = explode('-', $value_esps);
                    if($arr_registro['wespecialidad'] == $exp_esp[0])
                    {
                        $especialidad = $exp_esp[0].'-'.$exp_esp[1];
                        break;
                    }
                }
            }

            $id_cargo_106 = $arr_registro['id_cargo_106'];
            $id_cargo_198 = $arr_registro['id_cargo_198'];

            $valor_cargo         = $arr_registro['wvalor_final']*1;
            $valor_cargo_formato = number_format($valor_cargo,CANTIDAD_DECIMALES);
            $valor_cargo_html    = '<span style="font-weight:bold;" >'.$valor_cargo_formato.'</span>';
            if($arr_registro['wfacturable'] == "N")
            {
                $valor_cargo_html = '<span style=""><strike>'.$valor_cargo_formato.'</strike></span>';
                $valor_cargo = 0;
            }

            $html_actos .= '<tr class="'.$css.'">
                                <td>
                                    <input type="hidden" id="hddn_cargo_anular_'.$dif_acto.'_'.$id_cargo_198.'" name="hddn_cargo_anular_'.$dif_acto.'_'.$id_cargo_198.'" id_cargo_106="'.$id_cargo_106.'" id_cargo_198="'.$id_cargo_198.'" value="" >
                                    '.$arr_registro['wprocedimiento'].'-'.utf8_encode($arr_parametros['arr_procedimientos'][$arr_registro['wprocedimiento']]).'
                                </td>
                                <td>'.$arr_registro['wconcepto'].'-'.$arr_parametros['arr_conceptos'][$arr_registro['wconcepto']].'</td>
                                <td>'.$tercero.'</td>
                                <td>'.$especialidad.'</td>
                                <td style="text-align:right;">'.$arr_registro['wporcentaje'].'</td>
                                <!-- <td>'.number_format($arr_registro['wvalor'],CANTIDAD_DECIMALES).'</td> -->
                                <td style="text-align:right;">'.$valor_cargo_html.'</td>
                                <td>'.$arr_registro['wfacturable'].'</td>
                                <td>'.$arr_registro['wbaseliquidacion'].'-'.$arr_parametros['arr_bases_liquidacion'][$arr_registro['wbaseliquidacion']].'</td>
                                <td>'.$arr_registro['wtipo_facturacion'].'</td>
                                <!-- <td style="text-align:center;" >
                                    <input type="checkbox" id="chk_cargo_anular" name="chk_cargo_anular" id_cargo_106="'.$id_cargo_106.'" id_cargo_198="'.$id_cargo_198.'" value="" >
                                </td> -->
                            </tr>';
            $sumatoria_total += ($valor_cargo*1);
            $cont++;
        }
        $html_actos .=      '<tr class="encabezadoTabla">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <!-- <td>&nbsp;</td> -->
                                <td>'.number_format($sumatoria_total,CANTIDAD_DECIMALES).'</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <!-- <td>&nbsp;</td> -->
                            </tr>';
        $html_actos .= '    </table>
                            </div>
                        <!-- <div id="otros_datos_acto_qx_xx" class="datos-adds-eventos">
                            <table>
                                <tr>
                                    <td class="encabezadoTabla">Tipo anestesia</td>
                                    <td class="fila2">Lorem ipsum dolor sit amet</td>
                                    <td class="encabezadoTabla">Sala recuperación (horas)</td>
                                    <td class="fila2">Lorem ipsum dolor sit amet</td>
                                    <td class="encabezadoTabla">Tiempo uso equipos (horas)</td>
                                    <td class="fila2">Lorem ipsum dolor sit amet</td>
                                </tr>
                            </table>
                        </div> -->';
        $html_actos .= '</div>';
    }
    return $html_actos;
}

/**
 * [agregar_concepto_a_procedimiento: Función encargada de complementar los parámetros de liquidación de un concepto por ejemplo cuando este se agrega cuando se esta haciendo
 *                                     la liquidación y este no esta en la plantilla o en el caso en que se debe cobrar un concepto adicional si por ejemplo se debe cobrar un
 *                                     único valor por materiales y medicamentos (concepto de materiales y medicamentos no facturables)]
 * @param  [index]  $conex                         [Conexión a la base de datos]
 * @param  [string] $wemp_pmla                     [Código de empresa de promotora]
 * @param  [string] $wbasedato                     [Prefijo de las tablas de base de datos a usar]
 * @param  [string] $limite_concepto_cobro_insumos [Código del concepto que se cobra como único valor por ejemplo para materiales y medicamentos]
 * @param  [string] $procedimiento_liquidar_cod    [Código del procedimiento al que se le va a adicionar el nuevo concepto]
 * @param  [string] $procedimiento_bilat_dif       [Código del procedimiento diferenciando por la posición del organo o bilateralidad]
 * @param  [array]  $arr_lista_conceptos           [Array de conceptos para agregar el nuevo elemento a la lista]
 * @param  [array]  $arr_procedimientos_liquidar   [Array de procedimientos para agregar el nuevo concepto que hace parte del procedimiento y poderlo mostrar en pantalla]
 * @return [type]                                  [description]
 */
function agregar_concepto_a_procedimiento($conex, $wemp_pmla, $wbasedato, $limite_concepto_cobro_insumos, $procedimiento_liquidar_cod, $procedimiento_bilat_dif, &$arr_lista_conceptos, &$arr_procedimientos_liquidar)
{
    global $orden_conceptos_por_procedimiento;
    $sql = "SELECT  con.Grucod AS concepto_cod_ppal, con.Grudes AS concepto_nom_ppal, con.Grutip AS requiere_tercero, con.Grumva AS modifica_valor, con.Gruinv AS mueve_inventario, con.Gruarc AS tabla_valida_precios, con.Gruser AS wserv
            FROM    {$wbasedato}_000200 AS con
            WHERE   con.Grucod = '{$limite_concepto_cobro_insumos}'";
    $resultCon = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
    if(mysql_num_rows($resultCon) > 0)
    {
        $rowCon = mysql_fetch_array($resultCon);

        $arr_concepto_info = array( "cantidad_concepto"    => 1,
                                    "wserv"                => $rowCon['wserv'],
                                    "tipo_cobro"           => "",
                                    "requiere_tercero"     => $rowCon["requiere_tercero"],
                                    "tabla_valida_precios" => $rowCon["tabla_valida_precios"],
                                    "modifica_valor"       => $rowCon["modifica_valor"],
                                    "mueve_inventario"     => $rowCon["mueve_inventario"],
                                    "concepto_cod_ppal"    => $limite_concepto_cobro_insumos,
                                    "concepto_nom_ppal"    => $rowCon['concepto_nom_ppal'],
                                    "procedimiento_cod"    => $procedimiento_liquidar_cod,
                                    "procedimiento_nom"    => $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["nombre"],
                                    "procedimiento_tarifa" => $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["modalidad_facturacion"],
                                    "procedimiento_puntos" => $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["wnumero_puntos"],
                                    "progqx_puntos_gqx"    => $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["wgrupo_gqx"],
                                    "cobro_concepto"       => $limite_concepto_cobro_insumos,
                                    "cobro_procedimiento"  => $procedimiento_liquidar_cod,
                                    "arr_cobrar"           => array(),
                                    "wvaltar"              => 0,
                                    "es_concepto_extra"    => true);

        $arr_lista_conceptos[$limite_concepto_cobro_insumos] = $rowCon['concepto_nom_ppal'];

        if(!array_key_exists($limite_concepto_cobro_insumos, $arr_lista_conceptos))
        {
            $arr_lista_conceptos[$limite_concepto_cobro_insumos] = $rowCon['concepto_nom_ppal'];
        }

        if(!array_key_exists($limite_concepto_cobro_insumos, $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif])) { $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos] = $limite_concepto_cobro_insumos; }

        if(!array_key_exists($limite_concepto_cobro_insumos, $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["configuracion_liquidar"]))
        {
            $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["configuracion_liquidar"][$limite_concepto_cobro_insumos] = array();
        }
        $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["configuracion_liquidar"][$limite_concepto_cobro_insumos] = $arr_concepto_info;
    }
}


/**
 * [consultarConceptosManuales: Consulta los conceptos que estan configurados en los manuales de cirugías, para ayudar a saber si
 *                             el concepto que se esté liquidando corresponde a uno de los conceptos de manuales de cirugías multiples]
 * @param  [index]  $conex     [Conexión a la base de datos]
 * @param  [string] $wemp_pmla [Código de empresa de promotora]
 * @param  [string] $wbasedato [Prefijo de las tablas de base de datos a usar]
 * @return [array]             [Retorna un array con los códigos de los conceptos activos que conformarán las columnas de configuración para los porcentajes de cada cirugía]
 */
function consultarConceptosManuales($conex, $wemp_pmla, $wbasedato)
{
    $arr_conceptos_manuales = array();
    $sql = "SELECT  Concod
            FROM    {$wbasedato}_000208
            WHERE   Conest = 'on'
            ORDER BY Conord ASC";
    $result = mysql_query($sql,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$sql." - ".mysql_error());
    while($row = mysql_fetch_array($result))
    {
        $arr_conceptos_manuales[$row["Concod"]] = $row["Concod"];
    }
    return $arr_conceptos_manuales;
}

/**
 * [crearArrayPorcentajesMultiples: Función encargada de crear un array con los porcentajes de cirugías múltiples con base a los parámetros de entrada a la función (tipoEmpresa, wcod_empresa, wpolitraumatizado),
 *                                 el primer nivel del array tienen los indices on u off que indica si corresponde a un porcentaje bilateral o no bilateral,
 *                                 el nivel siguiente corresponde uno a la via_numeroEspecialidad teniendo al interior subarrays con los números de las cirugías y sus porcentajes.]
 * @param  [type] $conex                    [Conexión a la base de datos]
 * @param  [type] $wbasedato                [Código de empresa de promotora]
 * @param  [type] $tipoEmpresa              [Prefijo de las tablas de base de datos a usar]
 * @param  [type] $wcod_empresa             [Código de la empresa responsable del paciente]
 * @param  [type] $wpolitraumatizado        [La liquidación aplica para paciente politraumatizado o no politraumatizado (on u off)]
 * @param  [type] $CX_numero_vias           [YA NO SE USA EN ESTE PUNTO, antes se consultaba la cantidad de vías dependiendo de la cantidad de vías seleccionadas en los parámetros
 *                                           de entreda de la liquidación pero de ahora en adelante el número de la vía lo determina la cirugía mayor y la especialidad de la cirugía mayor,
 *                                           todas las cirugías menores se comparan con la cirugía mayor para determinar si fue por igual o diferente vía]
 * @param  [type] $CX_numero_especialidades [YA NO SE USA EN ESTE PUNTO, es el mismo caso que el número de vías (leer descripción del campo anterior)]
 * @param  array  $arr_parametros_extra     [Array por valor que guarda los parámetros más generales al manual de liquidación encontrado, corresponde al encabezado y otras restricciones generales
 *                                          que debe aplicar el manual]
 * @return [type]                           [Retorna el array con los porcentajes encontrados activos para el manual a utilizar en la liquidación.]
 */
function crearArrayPorcentajesMultiples($conex, $wbasedato, $tipoEmpresa, $wcod_empresa, $wpolitraumatizado, $CX_numero_vias, $CX_numero_especialidades, &$arr_parametros_extra = array())
{
    $arr_parametros_extra = array("wbaseliquidacion" => "", "wbaseliquidacion_nombre" => "", "wbaseliquidacion_acto_quirurgico" => "", "cx_multiples_requiere_tiempos" => "");
    $arr_porcentajes_multiples_fn = array();
    global $id_cx_multiples;

    $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
    $TB_BASE_LIQ_ACTOSQX = TB_BASE_LIQ_ACTOSQX;
    $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;

    $variables = array();
    $variables['enc.Blqtem']['combinar'] = true;
    $variables['enc.Blqtem']['valor']    = $tipoEmpresa;

    $variables['enc.Blqemp']['combinar'] = true;
    $variables['enc.Blqemp']['valor']    = $wcod_empresa;

    $variables['enc.Blqpol']['combinar'] = true;
    $variables['enc.Blqpol']['valor']    = $wpolitraumatizado;

    $variables['enc.Blqest']['combinar'] = false;
    $variables['enc.Blqest']['valor']    = "on";

    // Se consulta el manual que aplica según parámetros de encabezado
    $sql = generarQueryCombinado($variables, "{$wbasedato}_{$TB_BASE_LIQUIDACION} AS enc");

    $result = mysql_query($sql, $conex) OR die(mysql_errno()." - ".mysql_error().' > '.$sql);

    if(mysql_num_rows($result) > 0)
    {
        $row_CxMul = mysql_fetch_array($result);
        $id_cx_multiples = $row_CxMul['id'];

        // Este query consulta al mismo tiempo los porcentajes para cirugía bilateral y otros actos quirúrgicos
        // En esta función lo que se hace es crear el array separando los porcentajes que corresponden a lo bilateral y lo no bilateral
        $sql = "SELECT  t186.Blqclq AS codigo_liquidacion, t186.Blqdes AS nombre_liquidacion, t186.Blqtie AS requiere_tiempos, t186.Blqauv AS limite_ayudantia_uvr, t186.Blqagr AS limite_ayudantia_grupo,
                        t187.Cxxcod AS codigo_acto_qx, t187.Cxxdes AS nombre_acto_qx, t187.Cxxbil AS es_bilateral, t187.Cxxnvi AS numero_vias, t187.Cxxnsp AS numero_especialidades,
                        t188.Liqcon AS codigo_concepto, t188.Liqncx AS numero_cirugia, t188.Liqpor AS porcentaje_liquidar, t188.Liqcmy AS hasta_la_mayor_marcada, t188.Liqtcb AS cambio_tipo_cobro,
                        t188.Liqvia AS via_bilateral, t188.Liqesp AS especialidad_bilateral
                FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION} AS t186
                        INNER JOIN
                        {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX} AS t187 ON (t186.Blqclq = t187.Cxxblq)
                        INNER JOIN
                        {$wbasedato}_{$TB_BASE_LIQPORCENTA} AS t188 ON (t186.Blqclq = t188.Liqclq
                                                                        AND t188.Liqclq = t187.Cxxblq
                                                                        AND t187.Cxxcod = t188.Liqcxx
                                                                        AND t188.Liqest = 'on'
                                                                        AND t188.Liqbrr = 'off')
                WHERE   t186.id = '{$id_cx_multiples}'
                ORDER BY t187.Cxxblq, t187.Cxxcod, t188.Liqncx";
        $result = mysql_query($sql, $conex) OR die(mysql_errno()." - ".mysql_error().' > '.$sql);
        // echo "<pre>sql: ".print_r($sql,true)."</pre>";
        if(mysql_num_rows($result) > 0)
        {
            while($row = mysql_fetch_array($result))
            {
                $arr_parametros_extra['wbaseliquidacion']                 = $row['codigo_liquidacion'];
                $arr_parametros_extra['wbaseliquidacion_nombre']          = utf8_encode($row['nombre_liquidacion']);
                $arr_parametros_extra['wbaseliquidacion_acto_quirurgico'] = utf8_encode($row['nombre_acto_qx']);
                $arr_parametros_extra['cx_multiples_requiere_tiempos']    = $row['requiere_tiempos'];
                $arr_parametros_extra['limite_ayudantia_uvr']             = $row['limite_ayudantia_uvr'];
                $arr_parametros_extra['limite_ayudantia_grupo']           = $row['limite_ayudantia_grupo'];
                if(!array_key_exists($row['es_bilateral'], $arr_porcentajes_multiples_fn))
                {
                    $arr_porcentajes_multiples_fn[$row['es_bilateral']] = array();
                }

                $numero_cirugia = $row['numero_cirugia'];

                if($row['via_bilateral'] != '')
                {
                    // SE ESTA TRABAJANDO EN ESTA PARTE PARA PODER SALTAR ENTRE BILATERAL Y NO BILATERAL, ESTO PARA PODER APLICAR SI DESPUES DE UNA
                    // BILATERAL HAY OTRA CIRUGÍA POR IGUAL O DIFERENTE VIA, INCLUSO SABER QUE HACER SI HAY UNA CIRUGIA MAYOR ANTES DE LA BILATERAL
                    // EJEMPLOS:
                    // Cirugía 20ptos             100%
                    // Cirugia 15ptos    bilat1   60%        aplica manual para igual via igual especialista
                    // Cirugia 15ptos    bilat2   75%        aplica el porcentaje de la segunda cirugia de evento Qx. bilateral
                    // Cirugia <=15ptos           60% o 70%  aplica porcentaje de evento Qx bilatereal dependiendo de si es por igual o diferente vía a la bilateral
                    $via_bilateral = ($row['via_bilateral'] == '*') ? 'N': $row['via_bilateral'];
                    $especialidad_bilateral = ($row['especialidad_bilateral'] == '*') ? 'N': $row['especialidad_bilateral'];
                    $numero_cirugia = "VIA_".$via_bilateral.'_'.$especialidad_bilateral;
                }

                // Indice por bilateral, Especialidad igual o diferente y vía igual o diferente.
                $index_acto_qx     = "bilateral";
                $via_acto          = ($row['numero_vias'] == '*') ? 'N': $row['numero_vias'];
                $especialidad_acto = ($row['numero_especialidades'] == '*') ? 'N': $row['numero_especialidades'];
                if($row['es_bilateral'] != 'on')
                {
                    $index_acto_qx = $via_acto."_".$especialidad_acto;
                }

                if(!array_key_exists($index_acto_qx, $arr_porcentajes_multiples_fn[$row['es_bilateral']]))
                {
                    $arr_porcentajes_multiples_fn[$row['es_bilateral']][$index_acto_qx] = array();
                }

                if(!array_key_exists($numero_cirugia, $arr_porcentajes_multiples_fn[$row['es_bilateral']][$index_acto_qx]))
                {
                    $arr_porcentajes_multiples_fn[$row['es_bilateral']][$index_acto_qx][$numero_cirugia] = array();
                }

                if(!array_key_exists($row['codigo_concepto'], $arr_porcentajes_multiples_fn[$row['es_bilateral']][$index_acto_qx][$numero_cirugia]))
                {
                    $arr_porcentajes_multiples_fn[$row['es_bilateral']][$index_acto_qx][$numero_cirugia][$row['codigo_concepto']] = array();
                    $arr_porcentajes_multiples_fn[$row['es_bilateral']][$index_acto_qx][$numero_cirugia][$row['codigo_concepto']]["valor_porcentaje"] = $row['porcentaje_liquidar'];
                    // Este siguiente campo indica que el pocentaje a cobra debe ser solo hasta la cirugía mayor de todas en el nivel marcado si "hasta_la_mayor_marcada=on"
                    // Ejemplo si se marcó un concepto en la segunda cirugía indica que solo se debe cobrar la segunda cirugía mayor de todas en ese concepto sin importar la especialidad.
                    $arr_porcentajes_multiples_fn[$row['es_bilateral']][$index_acto_qx][$numero_cirugia][$row['codigo_concepto']]["hasta_la_mayor_marcada"] = ($row['hasta_la_mayor_marcada'] != 'on') ? 'off': $row['hasta_la_mayor_marcada'];
                }
            }
        }
        else
        {
            $sql = "SELECT  t186.Blqclq AS codigo_liquidacion, t186.Blqdes AS nombre_liquidacion, t186.Blqtie AS requiere_tiempos, t186.Blqauv AS limite_ayudantia_uvr, t186.Blqagr AS limite_ayudantia_grupo
                    FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION} AS t186
                    WHERE   t186.id = '{$id_cx_multiples}'";
            $result = mysql_query($sql, $conex) OR die(mysql_errno()." - ".mysql_error().' > '.$sql);
            $row = mysql_fetch_array($result);
            $arr_parametros_extra['wbaseliquidacion']                 = "";
            $arr_parametros_extra['wbaseliquidacion_nombre']          = "";
            $arr_parametros_extra['wbaseliquidacion_acto_quirurgico'] = "";
            $arr_parametros_extra['cx_multiples_requiere_tiempos']    = "";
            $arr_parametros_extra['limite_ayudantia_uvr']             = $row['limite_ayudantia_uvr'];
            $arr_parametros_extra['limite_ayudantia_grupo']           = $row['limite_ayudantia_grupo'];
        }
    }

    return $arr_porcentajes_multiples_fn;
}

/**
 * [arrayPorcentajePorNumeroCirugia: Función que de acuerdo al número de la cirugía, su bilateralidad o no y la especialidad, busca en el array de porcentajes del manual
 *                                     cuál es el acto quirúrgio que corresponde y devuelve el subarray solo con los porcentajes que debe aplicar a los conceptos
 *                                     que se estén liquidando en ese momento]
 * @param  [array] $arr_porcentajes_multiples [Array con todos los actos quirúrgicos de un manual y porcentajes posibles de cada acto quirúrgico configurado]
 * @return [array]                            [Retorna los porcentajes que debe usar para liquidar los conceptos, debe ser un subarray que está contenido en el array principal "$arr_porcentajes_multiples"]
 */
function arrayPorcentajePorNumeroCirugia($arr_porcentajes_multiples)
{
    global $arr_especialidades_liq, $wesbilateral, $numero_cirugia, $porcentajes_CX_bilaterales, $via_primera_bilateral, $especialid_primera_bilateral, $via_cirugia_en_curso, $especialidad_cirugia_en_curso, $ctrl_primer_via_procedimiento, $ctrl_primer_especialidad_procedimiento;
    $arr_porcentajes = array();

    // Esto es para determinar si se debe elegir el porcentaje para igual especialidad o diferente especialidad a la cirugía bilateral.
    $esp_bilat = "N";
    if($especialid_primera_bilateral == $especialidad_cirugia_en_curso)
    {
        $esp_bilat = "1";
    }

    $numero_cirugia_fn = $numero_cirugia;

    // Si se deben seleccionar los porcentajes de bilaterales, si no es cirugía bilateral (pero antes si hay bilaterales), y la cirugía es por la misma vía de la bilateral
    // entonces modificar el indice para tomar el porcentaje correcto relacionado con la vía de cirugías bilaterales.

    $index_acto_qx = 'bilateral';
    if($porcentajes_CX_bilaterales == 'on' && $wesbilateral != 'on' && $via_primera_bilateral == $via_cirugia_en_curso)
    {
        $numero_cirugia_fn = "VIA_1_".$esp_bilat;
    }
    elseif($porcentajes_CX_bilaterales == 'on' && $wesbilateral != 'on' && $via_primera_bilateral != $via_cirugia_en_curso)
    {
        $numero_cirugia_fn = "VIA_N_".$esp_bilat;
    }
    elseif($porcentajes_CX_bilaterales == 'off')
    {
        $via_acto          = ($via_cirugia_en_curso == $ctrl_primer_via_procedimiento) ? '1': 'N';
        $especialidad_acto = ($especialidad_cirugia_en_curso == $ctrl_primer_especialidad_procedimiento) ? '1': 'N';

        $index_acto_qx = $via_acto.'_'.$especialidad_acto;
    }

    // Siempre que hay un acto quirúrgico con más de una especialidad se toman como referencia los porcentajes de Diferente vía Diferente especialidad
    if(count($arr_especialidades_liq) > 1)
    {
        $index_acto_qx = 'N_N';
    }

    // $guardar = "porcentajes_CX_bilaterales: ".print_r($porcentajes_CX_bilaterales,true).PHP_EOL;
    // $guardar .= "index_acto_qx: ".print_r($index_acto_qx,true).PHP_EOL;
    // $guardar .= "numero_cirugia: ".print_r($numero_cirugia,true).PHP_EOL;
    // seguimiento($guardar);

    // Valída si existe el número de cirugía en el array de porcentajes múltiples, si no existe entonces trata de buscar el '*' para determinar
    // que todas las cirugía siguientes toman los porcentajes de '*'.
    if(array_key_exists($porcentajes_CX_bilaterales, $arr_porcentajes_multiples)
        && array_key_exists($index_acto_qx, $arr_porcentajes_multiples[$porcentajes_CX_bilaterales])
        && array_key_exists($numero_cirugia_fn, $arr_porcentajes_multiples[$porcentajes_CX_bilaterales][$index_acto_qx]))
    {
        // $guardar = "numero_cirugia: ".print_r($numero_cirugia,true).PHP_EOL;
        // seguimiento($guardar);
        $arr_porcentajes = $arr_porcentajes_multiples[$porcentajes_CX_bilaterales][$index_acto_qx][$numero_cirugia_fn];
    }
    elseif(array_key_exists($porcentajes_CX_bilaterales, $arr_porcentajes_multiples)
            && array_key_exists($index_acto_qx, $arr_porcentajes_multiples[$porcentajes_CX_bilaterales])
            && array_key_exists('*', $arr_porcentajes_multiples[$porcentajes_CX_bilaterales][$index_acto_qx]))
    {
        $arr_porcentajes = $arr_porcentajes_multiples[$porcentajes_CX_bilaterales][$index_acto_qx]['*'];
    }
    // elseif($wesbilateral == 'on' && array_key_exists($wesbilateral, $arr_porcentajes_multiples) && array_key_exists('VIA_', $arr_porcentajes_multiples[$wesbilateral]))
    // {
    //     $arr_porcentajes = $arr_porcentajes_multiples[$wesbilateral]['*'];
    // }
    // $guardar = "especialid_primera_bilateral:especialidad_cirugia_en_curso if($especialid_primera_bilateral == $especialidad_cirugia_en_curso)".PHP_EOL;
    // $guardar .= "porcentajes_CX_bilaterales: ".print_r($porcentajes_CX_bilaterales,true).PHP_EOL;
    // $guardar .= "numero_cirugia: ".print_r($numero_cirugia,true).PHP_EOL;
    // $guardar .= "arr_porcentajes: ".print_r($arr_porcentajes,true).PHP_EOL;
    // seguimiento($guardar);

    return $arr_porcentajes;
}

/**
 * [porcentajeCirugiaMultiple: Utiliza el array de porcentajes para un acto quirúrgico y busca qué porcentaje exactamente utilizar para calcular el valor a
 *                             cobrar del concepto que llega por parámetros a la función.]
 * @param  [string] $concepto_cod_ppal [Código del concepto que se está liquidando para un procedimientos en especial]
 * @param  [array]  $arr_porcentajes   [Array de porcentajes para un acto qurúrgico y número de cirugía]
 * @return [int]                       [Valor del porcentaje a aplicar para calcular el valor a cobrar el concepto en liquidación]
 */
function porcentajeCirugiaMultiple($concepto_cod_ppal, $arr_porcentajes)
{
    $porcentaje_cxMult  = 100;
    if(array_key_exists($concepto_cod_ppal, $arr_porcentajes))
    {
        $porcentaje_cxMult  = ($arr_porcentajes[$concepto_cod_ppal]["valor_porcentaje"])*1;
        // echo $porcentaje_cxMult.' < '.$concepto_cod_ppal.'|';
    }
    return $porcentaje_cxMult;
}

/**
 * [buscarLimitesEnManual: Busca un manual de cirugía para encontrar los posibles límites de uvr o código para materiales y medicamentos, asi como tambien límites
 *                         de cobro de ayudantía]
 * @param  [index]  $conex               [Conexión a la base de datos]
 * @param  [string] $wbasedato           [Código de empresa de promotora]
 * @param  [string] $tipoEmpresa         [Prefijo de las tablas de base de datos a usar]
 * @param  [string] $wcod_empresa        [Código de la empresa responsable del paciente]
 * @param  [string] $wpolitraumatizado   [La liquidación aplica para paciente politraumatizado o no politraumatizado (on u off)]
 * @param  [string] $TB_BASE_LIQUIDACION [Sufijo o consecutivo de la tabla donde se debe consultar el manual]
 * @return [array]                       [Array de campos consultados para el manual de liquidación]
 */
function buscarLimitesEnManual($conex, $wbasedato, $tipoEmpresa, $wcod_empresa, $wpolitraumatizado, $TB_BASE_LIQUIDACION)
{
    $row_limite = array();
    $variables = array();
    $variables['enc.Blqtem']['combinar'] = true;
    $variables['enc.Blqtem']['valor']    = $tipoEmpresa;

    $variables['enc.Blqemp']['combinar'] = true;
    $variables['enc.Blqemp']['valor']    = $wcod_empresa;

    $variables['enc.Blqpol']['combinar'] = true;
    $variables['enc.Blqpol']['valor']    = $wpolitraumatizado;

    $variables['enc.Blqest']['combinar'] = false;
    $variables['enc.Blqest']['valor']    = "on";

    $sql = generarQueryCombinado($variables, "{$wbasedato}_{$TB_BASE_LIQUIDACION} AS enc");
    // $guardar = "wcod_empresa: $wcod_empresa | sql :".print_r($sql,true).PHP_EOL.PHP_EOL;
    // seguimiento($guardar);
    $result_limite = mysql_query($sql, $conex) OR die(mysql_errno()." - ".mysql_error().' > '.$sql);

    if(mysql_num_rows($result_limite) > 0)
    {
        $row_CxMul = mysql_fetch_array($result_limite);
        $id_cx_multiples = $row_CxMul['id'];

        $result_limiteSelec = consultarManualDeLimite($conex, $wbasedato, $TB_BASE_LIQUIDACION, $id_cx_multiples);
        if(mysql_num_rows($result_limiteSelec) > 0)
        {
            $row_limite = mysql_fetch_array($result_limiteSelec);
        }
    }
    return $row_limite;
}

/**
 * [consultarManualDeLimite: Dado el ID de un manual, realiza un query para consultar todos los campos principales de un manual, por ejemplo
 *                           limites de uvr, grupo o ayudantía, clasificaciones de medicamentos que se deben facturar o no facturar tanto para uvr como para grupo]
 * @param  [index]  $conex               [conexión a la base de datos]
 * @param  [string] $wbasedato           [prefijo de tablas de base datos, según la empresa]
 * @param  [string] $TB_BASE_LIQUIDACION [Sufijo o consecutivo de la tabla donde se debe consultar el manual]
 * @param  [int]    $id_cx_multiples     [ID de base de datos correspondiente a un manual de liquidación]
 * @return [index]                       [Indice o puntero de una consulta a la base de datos que puede ser leída por fuera de esta función]
 */
function consultarManualDeLimite($conex, $wbasedato, $TB_BASE_LIQUIDACION, $id_cx_multiples)
{
    $sql = "SELECT  t186.Blqclq AS codigo_liquidacion, t186.Blqdes AS nombre_liquidacion,
                    t186.Blqtie AS requiere_tiempos, t186.Blquvr AS limite_med_uvr, t186.Blqccu AS cobro_limite_med_uvr, t186.Blqlgr AS limite_med_grupo, t186.Blqccg AS cobro_limite_med_grupo,
                    t186.Blqmuv AS limite_mat_uvr, t186.Blqcmu AS cobro_limite_mat_uvr, t186.Blqmgr AS limite_mat_grupo, t186.Blqcmg AS cobro_limite_mat_grupo,
                    t186.Blqcma AS excluido_medicamento_uvr, t186.Blqcmb AS excluido_material_uvr, t186.Blqcmc AS excluido_medicamento_grupo, t186.Blqcmd AS excluido_material_grupo,
                    t186.Blqnfa AS NoFactClase_medicamento_uvr, t186.Blqnfb AS NoFactClase_material_uvr, t186.Blqnfc AS NoFactClase_medicamento_grupo, t186.Blqnfd AS NoFactClase_material_grupo
            FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION} AS t186
            WHERE   t186.id = '{$id_cx_multiples}'";
    $result_limite = mysql_query($sql, $conex) OR die(mysql_errno()." - ".mysql_error().' > '.$sql);
    return $result_limite;
}


/**
 * [guardar_datos_temporales encargada de guardar en la tabla temporal aquellos parametros datos necesarios para recuperar una liquidación en curso
 * luego de un cierre inesperado]
 * @param  [string] $conex            [conexión a la base de datos]
 * @param  [string] $wbasedato        [prefijo de tablas de base datos, según la empresa]
 * @param  [array]  $datos_temporales [parametros temporales que se necesitan para ser recuperados, se guardan temporalmente en campos hidden de código html
 *                                     posteriormente guardados en una tabla de base de datos y desde allí poderlos recuperar ante un eventual cierre inesperado del programa]
 * @return [null]                     [No hay respuesta de la función, simplemente hace una actualización a una tabla de base de datos]
 */
function guardar_datos_temporales($conex, $wbasedato, $datos_temporales, $fecha_actual, $hora_actual, $whistoria, $wing, $user_session, $temporal)
{
    /*$datos_temporales = array(  "tabla_lista_cxs"            => $tabla_lista_cxs,
                                    "arr_datos_liquidar"         => $arr_datos_liquidar
                                    "arr_datos_liquidados"       => $arr_datos_liquidados
                                    "arr_extras"                 => $arr_extras
                                    "wnumvias"                   => $wnumvias,
                                    "wfecha_cargo"               => $wfecha_cargo,
                                    "whora_cargo"                => $whora_cargo,
                                    "wpolitraumatizado"          => $wpolitraumatizado,
                                    "wtipo_anestesia_cx"         => $wtipo_anestesia_cx,
                                    "wtiempo_sala_recuperarcion" => $wtiempo_sala_recuperarcion,
                                    "wtiempo_uso_minutos"        => $wtiempo_uso_minutos,
                                    "wtiempo_minutos_cx"         => $wtiempo_minutos_cx,
                                    "arr_CARGOS_PARA_GRABAR"     => $arr_CARGOS_PARA_GRABAR);*/
    $div_recuperacion_datos = ' <input type="hidden" id="arr_datos_liquidar_tempRecuperar" name="arr_datos_liquidar_tempRecuperar" value="'.$datos_temporales["arr_datos_liquidar"].'" />
                                <input type="hidden" id="arr_datos_liquidados_tempRecuperar" name="arr_datos_liquidados_tempRecuperar" value="'.$datos_temporales["arr_datos_liquidados"].'" />
                                <input type="hidden" id="arr_extras_tempRecuperar" name="arr_extras_tempRecuperar" value="'.$datos_temporales["arr_extras"].'" />
                                <input type="hidden" id="wnumvias_tempRecuperar" name="wnumvias_tempRecuperar" value="'.$datos_temporales["wnumvias"].'" />
                                <input type="hidden" id="wfecha_cargo_tempRecuperar" name="wfecha_cargo_tempRecuperar" value="'.$datos_temporales["wfecha_cargo"].'" />
                                <input type="hidden" id="whora_cargo_tempRecuperar" name="whora_cargo_tempRecuperar" value="'.$datos_temporales["whora_cargo"].'" />
                                <input type="hidden" id="wpolitraumatizado_tempRecuperar" name="wpolitraumatizado_tempRecuperar" value="'.$datos_temporales["wpolitraumatizado"].'" />
                                <input type="hidden" id="wtipo_anestesia_cx_tempRecuperar" name="wtipo_anestesia_cx_tempRecuperar" value="'.$datos_temporales["wtipo_anestesia_cx"].'" />
                                <input type="hidden" id="wtiempo_sala_recuperarcion_tempRecuperar" name="wtiempo_sala_recuperarcion_tempRecuperar" value="'.$datos_temporales["wtiempo_sala_recuperarcion"].'" />
                                <input type="hidden" id="wtiempo_uso_minutos_tempRecuperar" name="wtiempo_uso_minutos_tempRecuperar" value="'.$datos_temporales["wtiempo_uso_minutos"].'" />
                                <input type="hidden" id="wtiempo_minutos_cx_tempRecuperar" name="wtiempo_minutos_cx_tempRecuperar" value="'.$datos_temporales["wtiempo_minutos_cx"].'" />
                                <input type="hidden" id="wliq_paquete_tempRecuperar" name="wliq_paquete_tempRecuperar" value="'.$datos_temporales["wliq_paquete"].'" />
                                <input type="hidden" id="arr_CARGOS_PARA_GRABAR_tempRecuperar" name="arr_CARGOS_PARA_GRABAR_tempRecuperar" value="'.$datos_temporales["arr_CARGOS_PARA_GRABAR"].'" />
                                <input type="hidden" id="id_encabezado_sin_tarifa_tempRecuperar" name="id_encabezado_sin_tarifa_tempRecuperar" value="'.$datos_temporales["id_encabezado_sin_tarifa"].'" />
                                ';

    $html = $datos_temporales["tabla_lista_cxs"]."[*****]".$div_recuperacion_datos;
    $html = str_replace("<tbody>", "", $html);  $html = str_replace("</tbody>", "", $html);
    $html = addslashes($html);
    $html = utf8_decode($html);
    // seguimiento($html);

    switch ($temporal) {
        case '000160':
            $infoEncabezado = estadoCongelacionCuentaPaciente($whistoria, $wing);
            if($infoEncabezado['hayEncabezado'])
            {
                $infoValores    = $infoEncabezado['valores'];

                $sql = "UPDATE  {$wbasedato}_000160
                        SET Ecofec = '{$fecha_actual}',
                            Ecohor = '{$hora_actual}',
                            Ecotem = '".$html."'
                        WHERE  id     = '{$infoValores['id']}' ";
                mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX - ACTUALIZAR CAMPO TEMPORALES (000160):</b><br>".mysql_error()."<br>SQL: ".$sql);

                // html_entity_decode($salida);
            }
            break;

        case '000230':
            $id_encabezado_sin_tarifa = '';
            //Verificar si hay una cirugía pendiente de liquidar para una historia e ingreso
            if(empty($datos_temporales["id_encabezado_sin_tarifa"]))
            {
                $sql = "SELECT  id
                        FROM    {$wbasedato}_000230
                        WHERE   Ntrhis = '{$whistoria}'
                                AND Ntring = '{$wing}'
                                AND Ntrrcr = 'PR'
                                AND Ntrest = 'on'";

                $result = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX - CONSULTAR PENDIENTE (000230):</b><br>".mysql_error()."<br>SQL: ".$sql);
                if(mysql_num_rows($result) > 0)
                {
                    $rw = mysql_fetch_array($result);
                    $id_encabezado_sin_tarifa = $rw['id'];
                }
            }
            else
            {
                $id_encabezado_sin_tarifa = $datos_temporales["id_encabezado_sin_tarifa"];
            }

            // INSERTAR ENCABEZADO
            // Si ya hay un ID es porque se está editando un acto quirúrgico que con anterioridad ya había presentando algún error en tarifas
            // En ese caso no debe insertar un nuevo registro sino simplemente actualizar el detalle de errores o problema de tarifas
            if(empty($id_encabezado_sin_tarifa))
            {
                $sql = "INSERT INTO {$wbasedato}_000230
                            (Medico, Fecha_data, Hora_data, Ntrhis, Ntring, Ntrltm, Ntrrcr, Ntrest, Seguridad)
                            VALUES
                            ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}', '{$whistoria}', '{$wing}', '{$html}', 'PR', 'on', 'C-{$user_session}') ";
                mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX - INSERTAR CAMPO TEMPORALES (000230):</b><br>".mysql_error()."<br>SQL: ".$sql);
                $id_encabezado_sin_tarifa = mysql_insert_id();
            }
            else
            {
                $sql = "UPDATE  {$wbasedato}_000230
                        SET     Fecha_data = '{$fecha_actual}',
                                Hora_data  = '{$hora_actual}',
                                Seguridad  = 'C-{$user_session}',
                                Ntrltm     = '".$html."'
                        WHERE  id     = '{$id_encabezado_sin_tarifa}' ";
                mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX - ACTUALIZAR CAMPO TEMPORALES (000230):</b><br>".mysql_error()."<br>SQL: ".$sql);
            }
            return $id_encabezado_sin_tarifa;
            break;
    }
}


/**
 * [consultarGruposClasificados Permite consultar los grupos de medicamentos en la tabla 000004 a partir de la clasificación o clasificaciones concatenadas que llegan como parámetro
 *                              y son clasificaciones del maestro 000203]
 * @param  [type] $conex           [description]
 * @param  [type] $wbasedato       [description]
 * @param  [type] $clasificaciones [description]
 * @return [type]                  [description]
 */
function consultarGruposClasificados($conex, $wbasedato, $clasificaciones)
{
    $arr_grupos = array();
    if(!empty($clasificaciones))
    {
        $explode_grupos = explode("|", $clasificaciones);
        $filtro = implode("','", $explode_grupos);
        $sql_04 = " SELECT tc4.Grucod, tc4.Grudes
                    FROM {$wbasedato}_000004 tc4
                    WHERE tc4.Grucpg IN ('{$filtro}')
                            AND tc4.Gruest = 'on'";
        // $guardar = "sql_04: ".print_r($sql_04,true).PHP_EOL;
        // seguimiento($guardar);
        if($result_04 = mysql_query($sql_04))
        {
            while ($row_04 = mysql_fetch_array($result_04))
            {
                if(!array_key_exists($row_04['Grucod'], $arr_grupos))
                {
                    $arr_grupos[$row_04['Grucod']] = $row_04['Grucod'];
                }
            }
        }
    }
    return $arr_grupos;
}

/**
 * [generarArrayRelacionesNoFacturables: Para un manual de liquidación busca si existen configuradas restricciones de procedimientos relacionados,
 *                                     es decir, que pueden existir procedimientos que no se cobren si estan acompañados por otros procedimientos en específico.
 *                                     Esta función se encarga de crear un array con esas relaciones]
 * @param  [index]  $conex            [conexión a la base de datos]
 * @param  [string] $wbasedato        [prefijo de tablas de base datos, según la empresa]
 * @param  [int]    $wbaseliquidacion [ID de base de datos correspondiente a un manual de liquidación]
 * @return [array]                    [Array de procedimientos relacionados]
 */
function generarArrayRelacionesNoFacturables($conex, $wbasedato, $wbaseliquidacion)
{
    $arr_relaciones = array();
    $sql = "SELECT  Rnfpro, Rnfrel
            FROM    {$wbasedato}_000225
            WHERE   Rnfman = '{$wbaseliquidacion}'
                    AND Rnfest = 'on'
                    AND Rnfpro <> ''
                    AND Rnfrel <> ''";

    $result = mysql_query($sql) or die("Error ".mysql_errno()." => ".mysql_error()." => ".$sql);
    while($row = mysql_fetch_array($result))
    {
        if(!array_key_exists($row['Rnfpro'], $arr_relaciones))
        {
            $arr_relaciones[$row['Rnfpro']] = array();
        }
        $expl_arr = explode("|", $row['Rnfrel']);
        $arr_relaciones[$row['Rnfpro']] = $expl_arr;
    }
    return $arr_relaciones;
}

/**
 * [validarProcedimientoNoFacturable: Esta función se encarga de verificar si el procedimiento que se esta liquidando debe mostrarse como facturable o no facturable si y solo si
 *                                     encuentra que el código del procedimiento actual se encuentra relacionado en el mismo acto quirúrgico con otro procedimiento que se
 *                                     encuentra en la relación]
 * @param  [array] $arr_procedimientos_relacionados [Array de procedimientos liquidados en el acto quirúrgico relacionando cada procedimientos con cada uno de los otros procedimientos que lo
 *                                                    acompañan en la liquidación, este array se compara con el leído en el manual de cirugía y se verifica si
 *                                                    coincide alguna relación con la configuración leida en el manual]
 * @param  [type] $arr_proced_Manual_NoFact         [Array de procedimientos relacionados leídos en la configuración de manuales de cirugía]
 * @param  [type] $codigo_procedimiento             [Código del procedimiento que se está liquidando]
 * @return [type]                                   [Variable que indica facturable S o no facturable N]
 */
function validarProcedimientoNoFacturable($arr_procedimientos_relacionados, $arr_proced_Manual_NoFact, $codigo_procedimiento)
{
    $arr_result = array();
    if(array_key_exists($codigo_procedimiento, $arr_procedimientos_relacionados) && array_key_exists($codigo_procedimiento, $arr_proced_Manual_NoFact))
    {
        // Compara el array de procedimientos en liquidación que acompañan el procedimiento liquidado "$arr_procedimientos_relacionados[$codigo_procedimiento]"
        // contra la configuración de procedimientos del manual de cirugia "$arr_proced_Manual_NoFact[$codigo_procedimiento]", si entre los procedimientos que acompañan el procedimiento liquidado
        // hay equivalentes en el array de configuración, se va a devolver un array con todas las coincidencias y con que exista una coincidencia entonces
        // el procedimiento liquidado debe ser no facturable.
        $arr_result = array_intersect($arr_procedimientos_relacionados[$codigo_procedimiento], $arr_proced_Manual_NoFact[$codigo_procedimiento]);
    }

    // Si $arr_result es mayor a cero significa que un procedimiento que se está liquidando está acompañado por alguno de los procedimientos de la Lista de procedimientos facturables
    // que hace que el procedimiento actual sea NO FACTURABLE
    return (count($arr_result) > 0) ? "N": "";
}

/**
 * [marcarProcedimientosBilateralesParaOrdenar: Esta función se encarga de revisar si hay cirugias bilaterales y les suma una cantidad muy pequeña al número de puntos
 *     para poder ordenar primero las cirugías bilaterales que tienen igual cantidad de puntos que una tercer cirugía. "Las cirugías bilaterales deben quedar primero que una tercer cirugía de igual cantidad de puntos"]
 * @param  [type] $temporal_procedimientos_ordenados [description]
 * @return [type]                                    [description]
 */
function marcarProcedimientosBilateralesParaOrdenar(&$temporal_procedimientos_ordenados)
{
    $sum_temp_bilateral = 0.0001;
    $arr_temp_ordBilateralPrimeroConIgualPuntos = $temporal_procedimientos_ordenados;
    foreach ($arr_temp_ordBilateralPrimeroConIgualPuntos as $wespecialidad_ord => $arr_cxs)
    {
        foreach ($arr_cxs as $cod_key => $arr_value)
        {
            $dif_key = $cod_key;
            $pos = strpos($dif_key, "bilateral"); // Para veriricar si la posición corresponde a una cirugía derivada de una bilateral
                                                    // Entonces hay que intentar colocar las cirugías bilaterales en las primeras posiciones de la especialidad
                                                    // con esto las cirugías bilaterales que tienen igual pintaje que una tercer cirugía, deben quedar primero que
                                                    // las cirugías que no son bilaterales.

            if ($pos === false) {
            }
            else
            {
                $wnumero_puntos_tmp = $temporal_procedimientos_ordenados[$wespecialidad_ord][$cod_key]["wnumero_puntos"]*1;
                $wnumero_puntos_tmp = ($wnumero_puntos_tmp*1) + $sum_temp_bilateral;
                $temporal_procedimientos_ordenados[$wespecialidad_ord][$cod_key]["wnumero_puntos"] = $wnumero_puntos_tmp;
                // $guardar = "wnumero_puntos_tmp: ".print_r($wnumero_puntos_tmp,true).PHP_EOL;
                // seguimiento($guardar);
            }
        }
        arsort($temporal_procedimientos_ordenados[$wespecialidad_ord]);
    }
    // $guardar = "temporal_procedimientos_ordenados: ".print_r($temporal_procedimientos_ordenados,true).PHP_EOL;
    // seguimiento($guardar);
}

/**
 * [quitarMarcaProcedimientosBilateralesOrdenados: Esta función se encarga de modificar los números puntos de cada procedimientos específicamente cuando son puntos de cirugías bilaterales
 * porque al momento de ordenarlos en la función "marcarProcedimientosBilateralesParaOrdenar" se agregó un valor muy pequeño en la cantidad de puntos, en esta función se quita esa pequeña cantidad
 * de puntos para que no tengan efecto en ningún otro lado, solo en el ordenamiento.]
 * @param  [type] $temporal_procedimientos_ordenados [description]
 * @return [type]                                    [description]
 */
function quitarMarcaProcedimientosBilateralesOrdenados(&$temporal_procedimientos_ordenados)
{
    $arr_temp_ordBilateralPrimeroConIgualPuntos = $temporal_procedimientos_ordenados;
    foreach ($arr_temp_ordBilateralPrimeroConIgualPuntos as $wespecialidad_ord => $arr_cxs)
    {
        foreach ($arr_cxs as $cod_key => $arr_value)
        {
            $dif_key = $cod_key;
            $pos = strpos($dif_key, "bilateral");

            $dif_key = $temporal_procedimientos_ordenados[$wespecialidad_ord][$cod_key]["dif_key"];
            $pos = strpos($dif_key, "bilateral");
            if ($pos === false) {}
            else
            {
                $wnumero_puntos_tmp = $temporal_procedimientos_ordenados[$wespecialidad_ord][$cod_key]["wnumero_puntos"];
                $wnumero_puntos_tmp = str_replace(".0001", "", $wnumero_puntos_tmp);
                // Para ordenar primero los procedimientos bilaterales de igual cantidad de puntos que otras cirugías, se sumó un pequeño número para hacer la diferencia
                // en esta línea se quita esa pequeña diferencia para que el valor de puntos se muestre como llegó originalmente a esta función.
                $temporal_procedimientos_ordenados[$wespecialidad_ord][$cod_key]["wnumero_puntos"] = $wnumero_puntos_tmp;
            }
        }
    }
}

/**
 * [ordenarEspecialidadProcedimiento description: Este función se encarga de ordenar el array de mayor a menor puntos de procedimientos para cada especialidad y a su vez pone al principio
 *                                                 las especialidades que tienen los procedimientos mayores].
 * @param  [type] $temporal_procedimientos_ordenados [array de especialidades y procedimientos a ordenar]
 * @return [type]                                    [description]
 */
function ordenarEspecialidadProcedimiento(&$temporal_procedimientos_ordenados)
{
    // Ordenar bilaterales primero en comparación con cirugías que tengas igual cantidad de puntos que la bilateral
    marcarProcedimientosBilateralesParaOrdenar($temporal_procedimientos_ordenados);

    //arr_temporal para dejar primero la especialidad que tenga el procedimiento con el mayor número de puntos.
    $arr_temp_esp_puntos = array();

    // Este array ordena las cirugías por el número de puntos de cada especialidad.
    foreach ($temporal_procedimientos_ordenados as $wespecialidad_ord => $arr_cxs)
    {
        // ksort($temporal_procedimientos_ordenados[$wespecialidad_ord]);
        arsort($temporal_procedimientos_ordenados[$wespecialidad_ord]); // Ordena por el número de puntos de cada procedimiento, apesar de ser un array, esta función asume la primera posición como el valor por el que debe ordenar, en este caso el número de puntos
        // arsort($temporal_procedimientos_ordenados[$wespecialidad_ord]); // Ordena por el número de puntos de cada procedimiento, apesar de ser un array, esta función asume la primera posición como el valor por el que debe ordenar, en este caso el número de puntos
        $keys_arr_temp = array_keys($temporal_procedimientos_ordenados[$wespecialidad_ord]);

        if(count($keys_arr_temp) > 0)
        {
            $primer_procedimiento_especialidad = $keys_arr_temp[0];

            // $guardar = "primer_procedimiento_especialidad: ".print_r($primer_procedimiento_especialidad,true).PHP_EOL;
            // seguimiento($guardar);
            // Crea una llave con el numeroPunto_CodigoEspecialidad

            $numero_puntos = $temporal_procedimientos_ordenados[$wespecialidad_ord][$primer_procedimiento_especialidad]["wnumero_puntos"];

            $dif_key = $temporal_procedimientos_ordenados[$wespecialidad_ord][$primer_procedimiento_especialidad]["dif_key"];
            $pos = strpos($dif_key, "bilateral");
            if ($pos === false) {}
            else
            {
                $numero_puntos = str_replace(".0001", "", $numero_puntos)*1;
            }

            $llave_temp = $numero_puntos.'_'.$wespecialidad_ord;
            if(!array_key_exists($llave_temp, $arr_temp_esp_puntos))
            {
                $arr_temp_esp_puntos[$llave_temp] = $numero_puntos;
            }
        }

    }
    // ksort($temporal_procedimientos_ordenados); // Ordenas por la especialidad => // SE DEBIÓ IGNORAR ESTE ORDENAMIENTO PORQUE PRIMORDIALMENTE SE DEBE TENER EN CUENTA PONER DE PRIMERO LAS ESPECIALIDADES CON PROCEDIMIENTOS MAYORES !!!

    // Ordena de manor a mayor valor por número de puntos del procedimiento mayor de cada especialidad
    asort($arr_temp_esp_puntos);

    // Se invierte el orden realizado en asort para colocar la especialidad de mayor valor primero.
    arsort($arr_temp_esp_puntos);
    //Reescribir el array temporal_procedimientos_ordenados ahora ordenado colocando las especialidades con procedimientos mayores primero.
    $temp_ordenar = array();
    foreach ($arr_temp_esp_puntos as $key => $value)
    {
        $expl = explode("_", $key);
        $cod_esp = $expl[1];
        if(!array_key_exists($cod_esp, $temp_ordenar))
        {
            $temp_ordenar[$cod_esp] = array();
        }
        $temp_ordenar[$cod_esp] = $temporal_procedimientos_ordenados[$cod_esp];
    }
    $temporal_procedimientos_ordenados = $temp_ordenar;

    // Retirar el pequeño valor que se sumó a los puntos de las cirugías bilaterales.
    quitarMarcaProcedimientosBilateralesOrdenados($temporal_procedimientos_ordenados);
}


function modalidadPuntosProcedimiento($conex, $wbasedato, $wprocod, $wcodemp, $tipoEmpresa, $wccogra)
{
    $arr_procedimiento = array( "nombre"           => "PROCEDIMIENTO_NO_ENCONTRADO",
                                "tipo_facturacion" => "ERR_NO_MODO",
                                "wnumero_uvrs"     => 0,
                                "valor_grupo"      => 0);
    // --> Obtener si existe una modalidad de facturacion especifica para la empresa responsable.
    // -->  Armar array con las variables, para enviarlo a la funcion que me genera el query con todas las combinaciones posibles
    //      para asi obtener cual es el de mayor prioridad que le aplica.
    $variables = array();
    // --> Codigo del procedimiento
    $variables['Proempcod']['combinar'] = false;
    $variables['Proempcod']['valor']    = $wprocod;
    // --> Codigo de la empresa
    $variables['Proempemp']['combinar'] = true;
    $variables['Proempemp']['valor']    = $wcodemp;
    // --> Tipo de empresa
    $variables['Proemptip']['combinar'] = true;
    $variables['Proemptip']['valor']    = $tipoEmpresa;
    // --> Centro de costos
    $variables['Proempcco']['combinar'] = true;
    $variables['Proempcco']['valor']    = $wccogra;
    // --> Estado
    $variables['Proempest']['combinar'] = false;
    $variables['Proempest']['valor']    = 'on';

    // --> Obtener query
    $q_TipoFacEsp   = generarQueryCombinado($variables, $wbasedato."_000070");
    $res_TipoFacEsp = mysql_query($q_TipoFacEsp, $conex) or die("Error en el query: ".$q_TipoFacEsp."<br>Tipo Error:".mysql_error());

    $ModoFactura    = '';
    $valor_grupo    = '';
    $encontro_en_la_70 = false;
    // --> Si hay una modalidad especifica la tomo
    if ($row_TipoFacEsp = mysql_fetch_array($res_TipoFacEsp))
    {
        $qInfoMod = " SELECT Proemptfa, Proempgqx, Proemppun, Proempnom
                        FROM ".$wbasedato."_000070
                       WHERE id = '".$row_TipoFacEsp['id']."'";

        $resInfoMod     = mysql_query($qInfoMod, $conex) or die("Error en el query: ".$qInfoMod."<br>Tipo Error:".mysql_error());
        $rowInfoMod     = mysql_fetch_array($resInfoMod);
        $ModoFactura    = trim($rowInfoMod['Proemptfa']);
        $valor_grupo    = trim($rowInfoMod['Proempgqx']);

        $arr_procedimiento["nombre"]           = $rowInfoMod['Proempnom'];
        $arr_procedimiento["tipo_facturacion"] = trim($rowInfoMod['Proemptfa']);
        $arr_procedimiento["wnumero_uvrs"]     = $rowInfoMod['Proemppun'];
        $arr_procedimiento["valor_grupo"]      = $rowInfoMod['Proempgqx'];
        $encontro_en_la_70 = true;
    }

    // --> Si no hay modalidad en la 70, entonces se factura segun como este en la 103.
    if($ModoFactura == '')
    {
        $q_TipoFacGen =  "  SELECT  Pronom, Protfa, Propun, Progqx
                            FROM    {$wbasedato}_000103
                            WHERE   Procod = '{$wprocod}'
                                    AND Proest = 'on'";
        $res_TipoFacGen = mysql_query($q_TipoFacGen,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_TipoFacGen." - ".mysql_error());
        if ($row_TipoFacGen = mysql_fetch_array($res_TipoFacGen))
        {
            $ModoFactura = $row_TipoFacGen['Protfa'];
            $arr_procedimiento["nombre"]           = $row_TipoFacGen['Pronom'];
            $arr_procedimiento["tipo_facturacion"] = trim($row_TipoFacGen['Protfa']);
            $arr_procedimiento["wnumero_uvrs"]     = $row_TipoFacGen['Propun'];
            $arr_procedimiento["valor_grupo"]      = $row_TipoFacGen['Progqx'];
            $encontro_en_la_70 = false;
        }
    }

    // Si la modalidad es GQX y el valor de grupo en la tabla 70 es vacío si es que encontró la modadlidad en la 70, entonces se debe consultar el valor en la tabla 000103
    if($ModoFactura == 'GQX' && $encontro_en_la_70 && empty($valor_grupo))
    {
        $q_TipoFacGen =  "  SELECT  Pronom, Protfa, Propun, Progqx
                            FROM    {$wbasedato}_000103
                            WHERE   Procod = '{$wprocod}'
                                    AND Proest = 'on'";
        $res_TipoFacGen = mysql_query($q_TipoFacGen,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_TipoFacGen." - ".mysql_error());
        if ($row_TipoFacGen = mysql_fetch_array($res_TipoFacGen))
        {
            $arr_procedimiento["valor_grupo"] = $row_TipoFacGen['Progqx'];
        }
    }

    return $arr_procedimiento;
}

function cirugiasPendientesLiquidar($conex, $wbasedato, $whistoria, $wing)
{
    $arr_pendientes_liquidar = array();
    $filtros_his = '';
    if(!empty($whistoria))
    {
        $filtros_his = "c230.Ntrhis = '{$whistoria}'
                    AND c230.Ntring = '{$wing}'
                    AND ";
    }

    $sql = "SELECT  c230.id AS id_encabezado, c230.Ntrhis AS whistoria, c230.Ntring AS wing, c230.Ntrrcr AS estado_monitor, c230.Ntrltm AS html_procedimientos_liq
            FROM    {$wbasedato}_000230 AS c230
            WHERE   {$filtros_his} (c230.Ntrrcr = 'PR' OR c230.Ntrrcr = 'CO')
                    AND c230.Ntrest = 'on'
            ORDER BY c230.Ntrhis, c230.Ntring ";
    if($result = mysql_query($sql,$conex))
    {
        while ($row = mysql_fetch_array($result))
        {
            // consultar detalle de pendientes por liquidar
            $sql = "SELECT  c231.id AS id_detalle
            FROM    {$wbasedato}_000231 AS c231
            WHERE   c231.Tcaride = '{$row['id_encabezado']}'
                    AND c231.Tcarrcr = 'PR'
                    AND c231.Tcarest = 'on'";

            $estado_monitor = '';
            if($result_upd = mysql_query($sql,$conex))
            {
                $registros = mysql_num_rows($result_upd);
                if($registros == 0)
                {
                    $estado_monitor = 'CO';
                }
                elseif($registros > 0)
                {
                    $estado_monitor = 'PR';
                }
                $sql = "UPDATE  {$wbasedato}_000230
                            SET     Ntrrcr = '{$estado_monitor}'
                            WHERE  id      = '{$row['id_encabezado']}' ";
                mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX - ACTUALIZAR CAMPO TEMPORALES (000230):</b><br>".mysql_error()."<br>SQL: ".$sql);
            }

            if(!array_key_exists($row['id_encabezado'], $arr_pendientes_liquidar))
            {
                $arr_pendientes_liquidar[$row['id_encabezado']] = array();
            }
            $arr_pendientes_liquidar[$row['id_encabezado']] = array("whistoria"               => $row['whistoria'],
                                                                    "wing"                    => $row['wing'],
                                                                    "estado_monitor"          => $estado_monitor,
                                                                    "html_procedimientos_liq" => $row['html_procedimientos_liq']);
        }
    }
    return $arr_pendientes_liquidar;
}

function html_cirugiasPendientes($arr_pendientes_liquidar)
{
    $html = '';
    $cont = 0;
    foreach ($arr_pendientes_liquidar as $id_encabezado => $arr_pendiente)
    {
        $html_recuperado = str_replace("[+]", "'", $arr_pendiente['html_procedimientos_liq']);
        $html_recuperado = stripslashes($html_recuperado);
        $expl_acto = explode("[*****]", $html_recuperado);
        $encabezado_acto_qx = utf8_encode(trim($expl_acto[0]));
        $encabezado_acto_qx = str_replace("tr_liqAdd_cxs_", "tr_TMP_liqAdd_cxs_", $encabezado_acto_qx);
        $encabezado_acto_qx = str_replace("img_del1", "img_del1 hidden_img", $encabezado_acto_qx);

        if(empty($encabezado_acto_qx))
        {
            $encabezado_acto_qx = '<tr><td style="text-align:center;">NO SE ENCONTRÓ DETALLE DE CIRUGÍA</td></tr>';
        }

        $dif_fila = "tmp_".$id_encabezado;

        $estado_monitor = '';
        $fondo_bk = '';
        switch ($arr_pendiente['estado_monitor'])
        {
            case 'PR':
                $estado_monitor = 'Pendiente revisión';
                $fondo_bk = 'background-color: orange;';
                break;

            case 'CO':
                $estado_monitor = 'Corregido';
                $fondo_bk = 'background-color: green;';
                break;
        }

        $css = ($cont % 2 == 0) ? 'fila1': 'fila2';
        $identif_his_ing = $arr_pendiente['whistoria'].'_'.$arr_pendiente['wing'];
        $html .= '  <tr class="'.$css.' trs_temp_pend_'.$identif_his_ing.'" onmouseover="posicionElemento(this);" onmouseout="cerrarFlotante();" dif_fila="'.$dif_fila.'" style="cursor:pointer;" onclick="recuperarCirugiaEnRevision(\''.$arr_pendiente['whistoria'].'\', \''.$arr_pendiente['wing'].'\', \''.$id_encabezado.'\');">
                        <td>
                            '.$id_encabezado.'
                            <div id="caja_flotante_'.$dif_fila.'" class="caja_flotante" style="display:none; background-color: #FFFEE2; padding: 5px; z-index:1;">
                                <table>'.$encabezado_acto_qx.'</table>
                            </div>
                        </td>
                        <td>
                            '.$arr_pendiente['whistoria'].'
                        </td>
                        <td>'.$arr_pendiente['wing'].'</td>
                        <td style="'.$fondo_bk.' text-align:center;">'.$estado_monitor.'</td>
                    </tr>';
        $cont++;
    }

    if(empty($html))
    {
        $html = '<tr class="encabezadoTabla" >
                    <td colspan="4" >NO HAY CIRUGÍAS PENDIENTES EN REVISIÓN</td>
                </tr>';
    }

    $html = '<table align="center" >
                <tr class="encabezadoTabla" >
                    <td>Consecutivo pendiente</td>
                    <td>Historia</td>
                    <td>Ingreso</td>
                    <td>Estado revisión</td>
                </tr>
                '.$html.'
            </table>';
    return $html;
}


/**
 * [obtener_array_procedimientosEmpresa: Esta función se encarga de adicionar al array de procedimientos generales (000103) los procedimientos que
 * existan en la tabla 000070 para la empresa responsable del paciente, si se va a adicionar un código entonces se verifíca si el código ya existe en el array
 * que llega por parámetros y de ser así entonces actualiza el nombre del procedimiento tal como aparezca para esa empresa en 000070.]
 * @param  [type] $conex              [Conexión a la base de datos]
 * @param  [type] $wemp_pmla          [Código de la empresa de promotora las américas]
 * @param  [type] $wbasedato          [Prefijo de las tablas de la base de datos]
 * @param  [type] $wcod_empresa       [Código de la empresa para la que se van a buscar códigos de procedimientos en la relación procedimientos-empresa 000070]
 * @param  array  $arr_procedimientos [Array de procedimientos, pueden llegar inicialmente los procedimientos existentes en 000103]
 * @return [type]                     [Retorna un array con los procedimientos iniciales que hayan llegado por parámetros más los posibles procedimientos encontrados en la relación procedimiento-empresa]
 */
function obtener_array_procedimientosEmpresa($conex, $wemp_pmla, $wbasedato, $wcod_empresa, $arr_procedimientos = array())
{
    if(!empty($wcod_empresa))
    {
        $sql = "SELECT  Proempcod AS codigo, Proempnom AS nombre, Proemppro AS codigo_personalizado
                FROM    {$wbasedato}_000070
                WHERE   Proempemp = '{$wcod_empresa}'
                        AND Proempest = 'on'
                ORDER BY Proempnom";
        $resPro = mysql_query($sql,$conex) or die("Procedimientos por empresa - Error: ".mysql_errno()." ".$qPro." - ".mysql_error());
        while ($row = mysql_fetch_array($resPro))
        {
            $arr_procedimientos[$row['codigo']] = "(".$row['codigo_personalizado'].") ".utf8_encode($row['nombre']);
        }
    }
    return $arr_procedimientos;
}

/**
 * [validarCirugiaMayorACobrarConcepto: Función para ayudar a identificar hasta que número de cirugía máxima se debe cobrar un concepto]
 * @param  [type] $concepto_porcentaje          [description]
 * @param  [type] $cx_queNoSeCobran             [description]
 * @param  [type] $arr_conceptos_maximo_aCobrar [description]
 * @param  [type] $arr_procedimientos_orden     [description]
 * @param  [type] $indice_proced_concepNoCobro  [description]
 * @return [type]                               [description]
 */
function validarCirugiaMayorACobrarConcepto($concepto_porcentaje, $cx_queNoSeCobran, $arr_conceptos_maximo_aCobrar, $arr_procedimientos_orden, $indice_proced_concepNoCobro, &$cx_queSISeCobran, $valor_porcentaje)
{
    // $guardar = "cx_queNoSeCobran > ".print_r($cx_queNoSeCobran,true).PHP_EOL.PHP_EOL;
    // seguimiento($guardar);
    // $guardar = "indice_proced_concepNoCobro > ".print_r($indice_proced_concepNoCobro,true).PHP_EOL.PHP_EOL;
    // seguimiento($guardar);
    // if(!array_key_exists($indice_proced_concepNoCobro, $cx_queNoSeCobran))
    {
        $arr_cirugias_ordenadasTotal = array();
        foreach ($arr_procedimientos_orden as $esp_cod => $arr_esp)
        {
            $cont_cx = 1;
            foreach ($arr_esp as $procedCod => $puntos_cx)
            {
                $indice_proced_concepNoCobro_fn = $procedCod."_".$concepto_porcentaje;
                if(!array_key_exists($indice_proced_concepNoCobro_fn, $arr_cirugias_ordenadasTotal))
                {
                    $arr_cirugias_ordenadasTotal[$indice_proced_concepNoCobro_fn]                        = array();
                    $arr_cirugias_ordenadasTotal[$indice_proced_concepNoCobro_fn]['puntos_cx']           = $puntos_cx;
                    $arr_cirugias_ordenadasTotal[$indice_proced_concepNoCobro_fn]['concepto_porcentaje'] = $concepto_porcentaje;
                    $arr_cirugias_ordenadasTotal[$indice_proced_concepNoCobro_fn]['valor_porcentaje'] = $valor_porcentaje;
                }
            }
        }

        arsort($arr_cirugias_ordenadasTotal);
        // $guardar = "arr_cirugias_ordenadasTotal > ".print_r($arr_cirugias_ordenadasTotal,true).PHP_EOL.PHP_EOL;
        // seguimiento($guardar);

        // $guardar = "$ arr_conceptos_maximo_aCobrar[$ concepto_porcentaje] > ".print_r($arr_conceptos_maximo_aCobrar[$concepto_porcentaje],true).PHP_EOL.PHP_EOL;
        // seguimiento($guardar);

        $cont_Cirugias = 1;
        $cirugias_maximo_cobro = array();
        foreach ($arr_cirugias_ordenadasTotal as $indice_proced_concepNoCobro_fn => $arr_value)
        {
            if($cont_Cirugias <= ($arr_conceptos_maximo_aCobrar[$concepto_porcentaje]*1))
            {
                if(!array_key_exists($indice_proced_concepNoCobro_fn, $cx_queSISeCobran))
                {
                    $cx_queSISeCobran[$indice_proced_concepNoCobro_fn] = $arr_value['valor_porcentaje'];
                }
            }
            else
            {
                if(!array_key_exists($indice_proced_concepNoCobro_fn, $cx_queNoSeCobran))
                {
                    $cx_queNoSeCobran[$indice_proced_concepNoCobro_fn] = $arr_value['concepto_porcentaje'];
                }
            }
            $cont_Cirugias++;
        }

        // $guardar = "cx_queNoSeCobran > ".print_r($cx_queNoSeCobran,true).PHP_EOL.PHP_EOL;
        // seguimiento($guardar);
        // $cont_cx = 0;
        // $cirugias_maximo_cobro = array();
        // foreach ($arr_procedimientos_orden as $esp_cod => $arr_esp)
        // {
        //     $cont_cx = 1;
        //     foreach ($arr_esp as $procedCod => $puntos_cx)
        //     {
        //         $indice_proced_concepNoCobro_fn = $procedCod."_".$concepto_porcentaje;
        //         if(!array_key_exists($indice_proced_concepNoCobro_fn, $cx_queNoSeCobran))
        //         {
        //             if($cont_cx == $arr_conceptos_maximo_aCobrar[$concepto_porcentaje])
        //             {
        //                 if(!array_key_exists($indice_proced_concepNoCobro_fn, $cirugias_maximo_cobro))
        //                 {
        //                     $cirugias_maximo_cobro[$indice_proced_concepNoCobro_fn]                        = array();
        //                     $cirugias_maximo_cobro[$indice_proced_concepNoCobro_fn]['puntos_cx']           = $puntos_cx;
        //                     $cirugias_maximo_cobro[$indice_proced_concepNoCobro_fn]['concepto_porcentaje'] = $concepto_porcentaje;
        //                 }
        //             }

        //             if($cont_cx > $arr_conceptos_maximo_aCobrar[$concepto_porcentaje])
        //             {
        //                 if(!array_key_exists($indice_proced_concepNoCobro_fn, $cx_queNoSeCobran))
        //                 {
        //                     $cx_queNoSeCobran[$indice_proced_concepNoCobro_fn] = $concepto_porcentaje;
        //                 }
        //             }
        //         }
        //         $cont_cx++;
        //     }
        // }

        // arsort($cirugias_maximo_cobro);

        // $guardar = "cirugias_maximo_cobro > ".print_r($cirugias_maximo_cobro,true).PHP_EOL.PHP_EOL;
        // seguimiento($guardar);

        // $cx_maximo_cobro_primero = "";
        // foreach ($cirugias_maximo_cobro as $indice_proced_concepNoCobro_fn2 => $arr_conceptoCod)
        // {
        //     if(empty($cx_maximo_cobro_primero))
        //     {
        //         $cx_maximo_cobro_primero = $indice_proced_concepNoCobro_fn2;
        //     }
        //     else
        //     {
        //         if(!array_key_exists($indice_proced_concepNoCobro_fn2, $cx_queNoSeCobran))
        //         {
        //             $cx_queNoSeCobran[$indice_proced_concepNoCobro_fn2] = $arr_conceptoCod['concepto_porcentaje'];
        //         }
        //     }
        // }
    }

    return $cx_queNoSeCobran;
}


/**
 * [conceptoMatMedFacturable: Función encargada de agreagar el concepto 0168 a uno o varios procedimientos, ayuda a determinar si los materiales o medicamentos son facturables o no]
 * @param  [type] $conex                              [description]
 * @param  [type] $wemp_pmla                          [description]
 * @param  [type] $wbasedato                          [description]
 * @param  [type] $primer_procedimientos_mayor_puntos [description]
 * @param  [type] $limite_puntos_cobro_medicamento    [description]
 * @param  [type] $excluido_medicamento               [description]
 * @param  [type] $procedimiento_bilat_dif            [description]
 * @param  [type] $limite_concepto_cobro_medicamentos [description]
 * @param  [type] $procedimiento_liquidar_cod         [description]
 * @param  [type] $procedimiento_posicion_organo      [description]
 * @param  [type] $es_bilateral                       [description]
 * @param  [type] $arr_lista_conceptos                [description]
 * @param  [type] $arr_procedimientos_liquidar        [description]
 * @param  [type] $NoFactClase_medicamento            [description]
 * @param  [type] $limite_puntos_cobro_materiales     [description]
 * @param  [type] $NoFactClase_material               [description]
 * @param  [type] $matMedFacturable                   [description]
 * @param  [type] $limite_concepto_cobro_materiales   [description]
 * @return [type]                                     [description]
 */
function conceptoMatMedFacturable($conex, $wemp_pmla, $wbasedato, $primer_procedimientos_mayor_puntos, $limite_puntos_cobro_medicamento, $excluido_medicamento, $procedimiento_bilat_dif, $limite_concepto_cobro_medicamentos,
                                    $procedimiento_liquidar_cod, $procedimiento_posicion_organo, $es_bilateral, &$arr_lista_conceptos, &$arr_procedimientos_liquidar, $NoFactClase_medicamento, $limite_puntos_cobro_materiales, $NoFactClase_material,
                                    $matMedFacturable, $limite_concepto_cobro_materiales, $excluido_material)
{
    if($primer_procedimientos_mayor_puntos <= $limite_puntos_cobro_medicamento)
    {
        //$hay_medicamentos > 0 && // no es necesario validar si hay o no materiales y medicamentos por cada procedimientos, si solo un procedimiento tiene mercado, ese se cobra para todos los demás.
        $matMedFacturable["excluido_insumos_med"] = $excluido_medicamento;
        $index_control_mat    = $procedimiento_bilat_dif.'_'.$limite_concepto_cobro_medicamentos;
        $diferencia_bilaterales_organos = $procedimiento_liquidar_cod.'_'.$procedimiento_posicion_organo.'_'.$es_bilateral;
        // if(!array_key_exists($index_control, $arr_control_bilateral_concepto_extra))
        {
            if($es_bilateral == 'on') { $arr_control_bilateral_concepto_extra[$diferencia_bilaterales_organos] = $index_control_mat; }
            // echo "if(($wnumero_puntos*1) <= ($limite_puntos_cobro_medicamento*1))";
            $matMedFacturable["medicamentos_facturables"] = 'N';
            // Se debe crear un nuevo concepto encargado de cobrar todo el paquete que se guardará como no facturable.
            // $arr_conceptos_liquidar = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod]["configuracion_liquidar"];
            // $arr_procedimientos_liquidar[$procedimiento_liquidar_cod]["configuracion_liquidar"] = $arr_conceptos_liquidar;
            agregar_concepto_a_procedimiento($conex, $wemp_pmla, $wbasedato, $limite_concepto_cobro_medicamentos, $procedimiento_liquidar_cod, $procedimiento_bilat_dif, $arr_lista_conceptos, $arr_procedimientos_liquidar);
            // $guardar = "agregar_concepto_a_procedimiento($conex, $wemp_pmla, $wbasedato, $limite_concepto_cobro_medicamentos, $procedimiento_liquidar_cod, $procedimiento_bilat_dif, $arr_lista_conceptos, $arr_procedimientos_liquidar);".PHP_EOL;
            // seguimiento($guardar);
        }
    }
    elseif($primer_procedimientos_mayor_puntos > $limite_puntos_cobro_medicamento)
    {
        //$hay_medicamentos > 0 && // no es necesario validar si hay o no materiales y medicamentos por cada procedimiento, si solo un procedimiento tiene mercado, ese se cobra para todos los demás.
        $matMedFacturable["NoFactClase_med"]      = $NoFactClase_medicamento;
    }

    if($primer_procedimientos_mayor_puntos <= $limite_puntos_cobro_materiales)
    {
        //$hay_materiales > 0 && // no es necesario validar si hay o no materiales y medicamentos por cada procedimientos, si solo un procedimiento tiene mercado, ese se cobra para todos los demás.
        $matMedFacturable["excluido_insumos_mat"] = $excluido_material;
        $index_control_med    = $procedimiento_bilat_dif.'_'.$limite_concepto_cobro_materiales;
        $diferencia_bilaterales_organos = $procedimiento_liquidar_cod.'_'.$procedimiento_posicion_organo.'_'.$es_bilateral;
        // if(!array_key_exists($index_control, $arr_control_bilateral_concepto_extra))
        {
            if($es_bilateral == 'on') { $arr_control_bilateral_concepto_extra[$diferencia_bilaterales_organos] = $index_control_med; }
            $matMedFacturable["materiales_facturables"] = 'N';
            // Se debe crear un nuevo concepto encargado de cobrar todo el paquete que se guardará como no facturable.
            // $arr_conceptos_liquidar = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod]["configuracion_liquidar"];
            // $arr_procedimientos_liquidar[$procedimiento_liquidar_cod]["configuracion_liquidar"] = $arr_conceptos_liquidar;
            agregar_concepto_a_procedimiento($conex, $wemp_pmla, $wbasedato, $limite_concepto_cobro_materiales, $procedimiento_liquidar_cod, $procedimiento_bilat_dif, $arr_lista_conceptos, $arr_procedimientos_liquidar);
        }
    }
    elseif($primer_procedimientos_mayor_puntos > $limite_puntos_cobro_materiales)
    {
        //$hay_materiales > 0 && // no es necesario validar si hay o no materiales y medicamentos por cada procedimientos, si solo un procedimiento tiene mercado, ese se cobra para todos los demás.
        $matMedFacturable["NoFactClase_mat"]      = $NoFactClase_material;
    }
    return $matMedFacturable;
}


/**
 * [sumatoriaConceptosProcedimientoCODIGO: funcion encargada de sumar el valor de todos los conceptos de un procedimientos, retornando la sumatoria total en un solo valor que ayuda a determinar el
 *     orden de las cirugías liquidadas por código]
 * @param  [type] $conex                            [description]
 * @param  [type] $wemp_pmla                        [description]
 * @param  [type] $wbasedato                        [description]
 * @param  [type] $procedimiento_ord                [description]
 * @param  [type] $procedimiento_dif                [description]
 * @param  [type] $data_temp                        [description]
 * @param  [type] $arr_datos_liquidar               [description]
 * @param  [type] $arr_conceptos_liquidar_ModCODIGO [description]
 * @param  [type] $wprocedimiento_add               [description]
 * @param  [type] $wconcepto_add                    [description]
 * @param  [type] $arr_extras                       [description]
 * @param  [type] $arr_lista_conceptos_temp         [description]
 * @param  [type] $arr_conceptos_nombres            [description]
 * @param  [type] $arr_datos_liquidados             [description]
 * @param  [type] $wespecialidad                    [description]
 * @param  [type] $es_paquete                       [description]
 * @return [type]                                   [description]
 */
function sumatoriaConceptosProcedimientoCODIGO($conex, $wemp_pmla, $wbasedato, $procedimiento_ord, $procedimiento_dif, $data_temp, $arr_datos_liquidar, $arr_conceptos_liquidar_ModCODIGO, $wprocedimiento_add, $wconcepto_add, $arr_extras, $arr_lista_conceptos_temp, $arr_conceptos_nombres, $arr_datos_liquidados, $wespecialidad, $es_paquete, $wcentro_costo, $wcod_empresa, $wtipo_ingreso, $whora_cargo)
{
    global $orden_conceptos_por_procedimiento, $id_concepto_uso_equipos, $id_concepto_examenes, $add_equipo, $add_examen, $add_equipo_nombre, $add_examen_nombre,
            $concepto_medicamentos_mueven_inv, $concepto_materiales_mueven_inv, $wfecha_cargo, $RESPONSABLE_VIRTUAL_PACIENTE, $arr_tarifas_modifican_valor_erp;

    $sumatoriaConceptosProcedimiento = 0;

    consultarConfiguracionPlantilla($conex, $wemp_pmla, $wbasedato, $procedimiento_ord, $procedimiento_dif, $data_temp, $arr_datos_liquidar, $arr_conceptos_liquidar_ModCODIGO, $wprocedimiento_add, $wconcepto_add, $arr_extras, $arr_lista_conceptos_temp, $arr_conceptos_nombres, $arr_datos_liquidados, $arr_datos_liquidar['arr_datos_paciente']['wresponsable_eps_codigo'], $arr_datos_liquidar['arr_datos_paciente']['tarifa_original'], $arr_datos_liquidar['arr_datos_paciente']['tipoEmpresa'], $wespecialidad, $es_paquete);
    //optener la tarifa a los conceptos que traiga la plantilla
    // $guardar = "arr_conceptos_liquidar_ModCODIGO: ".print_r($arr_conceptos_liquidar_ModCODIGO,true).PHP_EOL;
    // seguimiento($guardar);

    foreach ($arr_conceptos_liquidar_ModCODIGO as $codigo_concepto => $arr_info_conf)
    {
        $modifica_valorCODIGO       = $arr_info_conf['modifica_valor'];
        $mueve_inventario           = $arr_info_conf['mueve_inventario'];
        $tipo_cobro                 = $arr_info_conf['tipo_cobro'];
        $es_concepto_extra          = (array_key_exists('es_concepto_extra', $arr_info_conf)) ? true: false;
        $especialidad_procedimiento = $wespecialidad;
        $cobro_concepto             = "";
        $cobro_procedimiento        = "";
        switch ($tipo_cobro) {
            case 'cobro_hora':
                $cobro_concepto = $arr_info_conf['cobro_concepto'];
                $cobro_procedimiento = $arr_info_conf['cobro_procedimiento'];
                break;
            case 'cobro_anestesia':

                    // array(  "tipo_anestesia"      => $row['tipo_anestesia'],
                    //         "tiempo_inicio"       => $row['tiempo_inicio'],
                    //         "tiempo_final"        => $row['tiempo_final'],
                    //         "cobro_concepto"      => $row['cobro_concepto'],
                    //         "cobro_procedimiento" => $row['cobro_procedimiento']);

                // $wtipo_anestesia_cx = $arr_datos_procedimiento['wtipo_anestesia_cx'];
                $valor_rango_buscar = $wtiempo_sala_recuperarcion; //$arr_datos_liquidar["arr_datos_paciente"]["wtiempo_sala_recuperarcion"];
                foreach ($arr_info_conf['rangos'] as $key => $arr_rango)
                {
                    if($wtipo_anestesia_cx == $arr_rango['tipo_anestesia'] && $valor_rango_buscar >= $arr_rango['tiempo_inicio'] && $valor_rango_buscar <= $arr_rango['tiempo_final'])
                    {
                        $cobro_concepto = $arr_rango['cobro_concepto'];
                        $cobro_procedimiento = $arr_rango['cobro_procedimiento'];
                    }
                }
                break;
            case 'cobro_uso':

                    // array(  "tiempo_inicio"       => $row['tiempo_inicio'],
                    //         "tiempo_final"        => $row['tiempo_final'],
                    //         "cobro_concepto"      => $row['cobro_concepto'],
                    //         "cobro_procedimiento" => $row['cobro_procedimiento']);

                $valor_rango_buscar = $wtiempo_uso_minutos; //$arr_datos_procedimiento['wtiempo_uso_minutos'];
                foreach ($arr_info_conf['rangos'] as $key => $arr_rango)
                {
                    if($valor_rango_buscar >= $arr_rango['tiempo_inicio'] && $valor_rango_buscar <= $arr_rango['tiempo_final'])
                    {
                        $cobro_concepto = $arr_rango['cobro_concepto'];
                        $cobro_procedimiento = $arr_rango['cobro_procedimiento'];
                    }
                }
                break;

            default:
                if($es_concepto_extra)
                {
                    $cobro_concepto      = $arr_info_conf['cobro_concepto'];
                    $cobro_procedimiento = $arr_info_conf['cobro_procedimiento'];
                }
                break;
        }

        if($cobro_procedimiento == '*')
        {
            $cobro_procedimiento = arreglar_procedimientos_bilaterales($procedimiento_dif);
        }

        if(!$es_paquete)
        {
            // MODIFICACIONES PARA BUSCAR TARIFA POR TERCERO (GRUPO DE MEDICOS)
            // * TENER EN CUENTA EL TERCERO POR DEFECTO PARA ENVIARLO DESDE LA PRIMER VEZ QUE SE LIQUIDE EL ACTO QUIRÚRGICO.
            // * TENER EN CUENTA EL TERCERO QUE SE INGRESÓ EN LA INTERFAZ PARA ENVIARLO A LA FUNCIÓN QUE BUSCA LA TARÍFA AL MOMENTO DE RELIQUIDAR.
            $codigo_wtercero_tarifa = '';
            if($cobro_concepto != $id_concepto_uso_equipos && $cobro_concepto != $id_concepto_examenes)
            {
                if(array_key_exists($procedimiento_dif, $arr_datos_liquidados))
                {
                    if(array_key_exists($cobro_concepto, $arr_datos_liquidados[$procedimiento_dif]))
                    {
                        $codigo_wtercero_tarifa = $arr_datos_liquidados[$procedimiento_dif][$cobro_concepto]['wtercero'];
                    }
                }

                // Si no hay información de tercero entonces intenta buscar un tercero por defecto para este concepto y con ese código buscar una tarifa.
                if(empty($codigo_wtercero_tarifa))
                {
                    $userCargo = explode('-',$_SESSION['user']);
                    $wuse      = $userCargo[1];
                    global $wbasedato, $wemp_pmla, $conex, $wuse;
                    $arr_tercero_xdefecto = traer_terceros_por_defecto($cobro_concepto);
                    $codigo_wtercero_tarifa = $arr_tercero_xdefecto['codigo'];
                }
            }

            $arr_valor_cobro = datos_desde_procedimiento($cobro_procedimiento, $cobro_concepto, $wcentro_costo, $wcentro_costo, $wcod_empresa, $wfecha_cargo, $wtipo_ingreso, $especialidad_procedimiento, 'on', false, $codigo_wtercero_tarifa, $wfecha_cargo, $whora_cargo);

            // Si el concepto permite modificar o escribir un valor y si no se ha encontrado una tarífa definida para el concepto
            // entonces puede retomar el valor que antes puedan haber digitado, si no hay valor digitado entonces el valor será vacío
            if(($modifica_valorCODIGO == 'S' && $arr_valor_cobro['error'] == '1') || ($modifica_valorCODIGO == 'S' && array_key_exists($RESPONSABLE_VIRTUAL_PACIENTE["Ingtar"], $arr_tarifas_modifican_valor_erp)))
            {
                $wvaltar = 0;
                if(array_key_exists($procedimiento_dif, $arr_datos_liquidados)
                    && array_key_exists($cobro_concepto, $arr_datos_liquidados[$procedimiento_dif]))
                {
                    if(array_key_exists("wvalor_digitado", $arr_datos_liquidados[$procedimiento_dif][$cobro_concepto]))
                    {
                        $wvaltar = $arr_datos_liquidados[$procedimiento_dif][$cobro_concepto]['wvalor_digitado'];
                    }
                }
                $sumatoriaConceptosProcedimiento += $wvaltar;
            }
            else
            {
                $sumatoriaConceptosProcedimiento += ($arr_valor_cobro['wvaltar'])*1;
            }
        }
        else
        {
            $sumatoriaConceptosProcedimiento += $arr_info_conf["wvaltar"]*1;
        }
    }

    $orden_conceptos_por_procedimiento = array(); // Se inicializa de nuevo porque más adelante este array debe iniciar normalmente puesto que
                                                    // en el anterior llamado a la función "consultar Configuracion Plantilla" solo se estaba haciendo una simulación
    return $sumatoriaConceptosProcedimiento;
}


if(isset($accion) && isset($form))
{
    include_once("ips/ValidacionGrabacionCargosERP.php");

    $data = array('error'=>0,'mensaje'=>'','html'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                case 'guardar_datos_liquidados':
                    include_once("root/comun.php");
                    $data["mensaje_local"] = "";
                    $data["error_cargo"] = 0;
                    $arr_datos_liquidados   = unserialize(base64_decode($arr_datos_liquidados));
                    $arr_CARGOS_PARA_GRABAR = unserialize(base64_decode($arr_CARGOS_PARA_GRABAR));
                    // $arr_procedimientos_orden_liquidar = unserialize(base64_decode($arr_procedimientos_orden_liquidar));

                    // $guardar = print_r($arr_CARGOS_PARA_GRABAR,true).PHP_EOL;
                    // seguimiento($guardar);
                    // exit();

                    // $guardar = print_r($arr_datos_liquidados,true).PHP_EOL;
                    // seguimiento($guardar);
                    // exit();

                    // $fp = fopen("seguimiento.txt","w+");

                    $diferenciaActoQx   = date("YmdHi");
                    $TB_LIQUIDACIONES   = TB_LIQUIDACIONES;
                    $TB_ENC_LIQUIDACION = TB_ENC_LIQUIDACION;
                    $concepto_medicamentos_mueven_inv = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_medicamentos_mueven_inv');
                    $concepto_materiales_mueven_inv   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_materiales_mueven_inv');

                    $arr_encabezado = array();
                    $arr_encabezado_conDlle = array();

                    foreach ($arr_CARGOS_PARA_GRABAR as $key_procedimiento => $arr_cargos_dll)
                    // foreach ($arr_procedimientos_orden_liquidar as $wprocedimiento => $value)
                    {
                        $wprocedimiento = $key_procedimiento;
                        // if(array_key_exists($wprocedimiento, $arr_datos_liquidados))
                        {
                            // $arr_conceptos = $arr_datos_liquidados[$wprocedimiento];
                            if(!array_key_exists($wprocedimiento, $arr_encabezado))
                            {
                                $arr_encabezado[$wprocedimiento] = array();
                            }
                            $arr_encabezado[$wprocedimiento] = array(   "whistoria"                  => "",
                                                                        "wing"                       => "",
                                                                        "wnum_documento_ID"          => "",
                                                                        "diferenciaActoQx"           => "",
                                                                        "wprocedimiento"             => arreglar_procedimientos_bilaterales($wprocedimiento),
                                                                        "wtipo_anestesia_cx"         => "",
                                                                        "wtiempo_sala_recuperarcion" => "",
                                                                        "wtiempo_uso_minutos"        => "",
                                                                        "wtiempo_minutos_cx"         => "",
                                                                        "wespecialista"              => "",
                                                                        "wespecialidad"              => "",
                                                                        "worgano"                    => "",
                                                                        "wbilateral"                 => "",
                                                                        "wvia"                       => "");


                            foreach ($arr_cargos_dll as $key_wconcepto_equipo_o_insumo => $arr_cargos_del_concepto)
                            {
                                // arr_guardar_aux es un array que se le crea una posición adicional, si es solo un concepto normal entonces se asigna a la posición 0 para guardarlo
                                // pero si es un concepto que implique tener medicamentos entonces se crea un array con varias posiciones con la misma estructura de un concepto
                                // pero para este caso lo que se va a empezar a guardar son medicamentos y materiales porque se deben grabar como cargos normales.
                                // $arr_guardar_aux = array();
                                // if($wconcepto == $concepto_medicamentos_mueven_inv || $wconcepto == $concepto_materiales_mueven_inv)
                                // {
                                //     $arr_guardar_aux = $arr_guardar_info; // Para grabar todos los medicamentos y materiales, todos los insumos del concepto materiales o concepto medicamentos.
                                // }
                                // elseif($wconcepto == $id_concepto_uso_equipos || $wconcepto == $id_concepto_examenes)
                                // {
                                //     $arr_guardar_aux = $arr_guardar_info; // Para grabar solo conceptos, uno por uno
                                // }
                                // else
                                // {
                                //     $arr_guardar_aux[] = $arr_guardar_info; // Para grabar solo conceptos, uno por uno
                                // }

                                $id_CargoAnterior = 0; // Este ID será útil cuando se requiera asociar dos cargos en paralelo.
                                foreach ($arr_cargos_del_concepto as $key_secuencia => $arr_CARGO)
                                {
                                    $procedimiento_log_cx = $arr_CARGO['procedimiento_liquidar_cod'];
                                    if(array_key_exists($wprocedimiento, $arr_encabezado) && $arr_encabezado[$wprocedimiento]["whistoria"] == '')
                                    {
                                        $arr_encabezado[$wprocedimiento]["whistoria"]                  = $whistoria;
                                        $arr_encabezado[$wprocedimiento]["wing"]                       = $wing;
                                        $arr_encabezado[$wprocedimiento]["wdoc"]                       = $wdoc;
                                        $arr_encabezado[$wprocedimiento]["diferenciaActoQx"]           = $diferenciaActoQx;
                                        $arr_encabezado[$wprocedimiento]["wtipo_anestesia_cx"]         = $arr_CARGO['wtipo_anestesia_cx'];
                                        $arr_encabezado[$wprocedimiento]["wtiempo_sala_recuperarcion"] = $arr_CARGO['wtiempo_sala_recuperarcion'];
                                        $arr_encabezado[$wprocedimiento]["wtiempo_uso_minutos"]        = $arr_CARGO['wtiempo_uso_minutos'];
                                        $arr_encabezado[$wprocedimiento]["wtiempo_minutos_cx"]         = $arr_CARGO['wtiempo_minutos_cx'];
                                        $arr_encabezado[$wprocedimiento]["wespecialista"]              = $arr_CARGO['arr_encabezado']['wespecialistas'];
                                        $arr_encabezado[$wprocedimiento]["wespecialidad"]              = $arr_CARGO['arr_encabezado']['wespecialidad'];
                                        $arr_encabezado[$wprocedimiento]["worgano"]                    = $arr_CARGO['arr_encabezado']['worgano'];
                                        $arr_encabezado[$wprocedimiento]["wbilateral"]                 = $arr_CARGO['arr_encabezado']['wbilateral'];
                                        $arr_encabezado[$wprocedimiento]["wvia"]                       = $arr_CARGO['arr_encabezado']['wvia'];
                                    }

                                    if($arr_CARGO['wgrabar'] == 'on')
                                    {
                                        // Verificar si el cargo esta relacionado a un Equipo o Exámen
                                        // En este caso se debe tener en cuenta el campo de detalle de la tabla de log de liquidación
                                        // En la tabla 106 en el campo procedimiento guarda es el código del equipo o exámen.
                                        $codigo_equipo_examen         = arreglar_procedimientos_bilaterales($arr_CARGO['wprocod']);
                                        $codigo_procedimiento_dequipo = $arr_CARGO['wprocod'];
                                        $exp_nombPro                  = ($arr_CARGO['wpronom'] != '' && count(explode('-',$arr_CARGO['wpronom'])) > 1) ? explode('-',$arr_CARGO['wpronom']) : $arr_CARGO['wpronom'];
                                        $exp_nombPro                  = (is_array($exp_nombPro) && count($exp_nombPro) > 1) ? $exp_nombPro[1]: $exp_nombPro;
                                        if($arr_CARGO["cargo_examen_o_equipo"] == 'on')
                                        {
                                            // ESTE ES UN EXAMEN O EQUIPO!
                                            $codigo_equipo_examen         = $arr_CARGO["examen_equipo_codigo"];
                                            $codigo_procedimiento_dequipo = $arr_CARGO["examen_equipo_codigo"];

                                            $exp_nombPro          = ($arr_CARGO['examen_equipo_nombre'] != '' && count(explode('-',$arr_CARGO['examen_equipo_nombre'])) > 1) ? explode('-',$arr_CARGO['examen_equipo_nombre']) : $arr_CARGO['examen_equipo_nombre'];
                                            $exp_nombPro          = (is_array($exp_nombPro) && count($exp_nombPro) > 1) ? $exp_nombPro[1]: $exp_nombPro;
                                            $procedimiento_log_cx = $arr_CARGO["procedimiento_liquidar_cod"];
                                        }

                                        $wfecha    = $fecha_actual;
                                        $whora     = $hora_actual;
                                        $wcantidad = $arr_CARGO['wcantidad']*1;
                                        $datosGrabarCargos                           = array();
                                        $datosGrabarCargos['whistoria']              = $whistoria;
                                        $datosGrabarCargos['wing']                   = $wing;
                                        $datosGrabarCargos['wno1']                   = $wno1;
                                        $datosGrabarCargos['wno2']                   = $wno2;
                                        $datosGrabarCargos['wap1']                   = $wap1;
                                        $datosGrabarCargos['wap2']                   = $wap2;
                                        $datosGrabarCargos['wdoc']                   = $wdoc;
                                        $datosGrabarCargos['wcodemp']                = $arr_CARGO['wcodemp'];
                                        $datosGrabarCargos['wnomemp']                = $arr_CARGO['wnomemp'];
                                        $datosGrabarCargos['wser']                   = $wser;
                                        $datosGrabarCargos['wfecing']                = $wfecing;
                                        $datosGrabarCargos['wtar']                   = $arr_CARGO['wtar'];

                                        $datosGrabarCargos['wcodcon']                = $arr_CARGO['wcodcon']; // !
                                        $datosGrabarCargos['wnomcon']                = $arr_CARGO['wnomcon']; // !

                                        $datosGrabarCargos['wprocod']                = arreglar_procedimientos_bilaterales($codigo_procedimiento_dequipo); // !
                                        $datosGrabarCargos['wpronom']                = trim($exp_nombPro); // !

                                        $datosGrabarCargos['wcodter']                = $arr_CARGO['wcodter']; // !
                                        $exp_nombTer                                 = ($arr_CARGO['wnomter'] != '' && count(explode('-',$arr_CARGO['wnomter'])) > 0) ? explode('-',$arr_CARGO['wnomter']) : $arr_CARGO['wnomter'];
                                        $exp_nombTer = ($exp_nombTer != '' && is_array($exp_nombTer) && count($exp_nombTer) > 1) ? $exp_nombTer[1]: $exp_nombTer;
                                        $datosGrabarCargos['wnomter']                = $exp_nombTer; // !

                                        $datosGrabarCargos['wporter']                = $arr_CARGO["wporter"]; // ?????????????
                                        $datosGrabarCargos['wcantidad']              = $wcantidad;
                                        $datosGrabarCargos['wvaltar']                = $arr_CARGO['wvaltar']; // !
                                        $datosGrabarCargos['wrecexc']                = $arr_CARGO['wrecexc']; // ??????????????  Antes tenía 'R'
                                        $datosGrabarCargos['wfacturable']            = $arr_CARGO['wfacturable']; // !
                                        $datosGrabarCargos['wcco']                   = $wcco;
                                        $datosGrabarCargos['wccogra']                = $wcco;
                                        $datosGrabarCargos['wfeccar']                = $wfecha_cargo;
                                        $datosGrabarCargos['whora_cargo']            = $whora_cargo;
                                        $datosGrabarCargos['wconinv']                = $arr_CARGO['wconinv']; // ??????
                                        $datosGrabarCargos['wcodpaq']                = '';
                                        $datosGrabarCargos['wpaquete']               = 'off';
                                        $datosGrabarCargos['wconabo']                = '';
                                        $datosGrabarCargos['wdevol']                 = '';
                                        $datosGrabarCargos['waprovecha']             = 'off';
                                        $datosGrabarCargos['wconmvto']               = '';
                                        $datosGrabarCargos['wexiste']                = $arr_CARGO['wexiste'];
                                        $datosGrabarCargos['wbod']                   = $wbod;
                                        $datosGrabarCargos['wconser']                = $arr_CARGO['wconser']; // para que es y de donde debe llegar???????????????????
                                        $datosGrabarCargos['wtipfac']                = $arr_CARGO['wtipfac'];
                                        $datosGrabarCargos['wexidev']                = 0;
                                        $datosGrabarCargos['wfecha']                 = $wfecha;
                                        $datosGrabarCargos['whora']                  = $whora;
                                        $datosGrabarCargos['wespecialidad']          = (empty($arr_CARGO['wespecialidad'])) ? ' ': $arr_CARGO['wespecialidad'];; // !
                                        $datosGrabarCargos['cobraHonorarios']        = $arr_CARGO['cobraHonorarios'] ;
                                        $datosGrabarCargos['wgraba_varios_terceros'] = false;
                                        $datosGrabarCargos['wcodcedula']             = '';

                                        $datosGrabarCargos['tipoEmpresa']            = $arr_CARGO['tipoEmpresa'];
                                        $datosGrabarCargos['nitEmpresa']             = $arr_CARGO['nitEmpresa'];
                                        $datosGrabarCargos['tipoIngreso']            = $wtipo_ingreso; // Para evitar warnings en funciones
                                        $datosGrabarCargos['tipoPaciente']           = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['warctar']                = $arr_CARGO['warctar']; // SE DEBE ENVIAR LA TABLA DE VALIDAR PRECIOS COMO SE HACE EN LA SIMULACIÓN -- Para evitar warnings en funciones
                                        $datosGrabarCargos['wvaltarReco']            = $arr_CARGO["wvaltarReco"];
                                        $datosGrabarCargos['topeGeneral']            = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['saldoTope']              = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['wvaltarExce']            = $arr_CARGO["wvaltarExce"];
                                        $datosGrabarCargos['nomCajero']              = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['idParalelo']             = ''; // Si hay paralelo entonces en este campo se debe guardar el id del cargo nuevo creado como paralelo al cargo original.
                                        $datosGrabarCargos['enParalelo']             = $arr_CARGO["enParalelo"];
                                        $datosGrabarCargos['wauto']                  = ''; // Para evitar warnings en funciones

                                        $datosGrabarCargos['tipoCuadroTurno']        = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['habitacion']             = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['fecIngHab']              = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['horIngHab']              = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['diasFacturados']         = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['fecEgrHab']              = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['horEgrHab']              = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['diasEstancia']           = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['logRegistroCargo']       = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['logUnix']                = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['estadoMonitor']          = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['pendRevicion']           = ''; // Para evitar warnings en funciones
                                        $datosGrabarCargos['ccoActualPac']           = $ccoActualPac;
                                        $datosGrabarCargos['idTope']           = (!empty($arr_CARGO["idTope"]) && $arr_CARGO["wfacturable"] == 'S') ? $arr_CARGO["idTope"] : '';


                                        // Asociar ID anterior como paralelo
                                        // El cargo actual que estaría marcado con "[enParalelo] => on" es en realidad el cargo original y el cargo grabado anterior debió ser el cargo nuevo generado para el paralero
                                        // Al cargo actual entonces se le asocial el ID del cargo anterior para que queden unidos y ante una eventual anulación entonces se anulen ambos cargos.
                                        if($id_CargoAnterior > 0 && $arr_CARGO['enParalelo'] == 'on')
                                        {
                                            $datosGrabarCargos['idParalelo'] = $id_CargoAnterior;
                                        }

                                        $userCargo = explode('-',$_SESSION['user']);
                                        $wuse      = $userCargo[1];
                                        global $wuse;
                                        // $guardar = "{$arr_guardar['wprocedimiento']} - {$arr_guardar['wconcepto']} - {$arr_guardar['wvalor_final']} - {$datosGrabarCargos['wvaltarReco']}".PHP_EOL;
                                        // fwrite($fp, $guardar);
                                        $idGuardadoBD = 0;  // Este ID es con el que se guarda el nuevo registro en la tabla de cargos 000106, se guarda en la tabla de liquidación de cirugía
                                                            // y con ese ID luego se puede anular un cargo tanto en las tablas de cirugía como en las de cargos.

                                        // Por política a veces se debe poner como no facturable un cargo que ya estaba grabado, al momento de aplicar la politica esta función
                                        // retorna el ID del cargo a modificar, aquí en este condicional se verifica si esa variable de ID está seteada y utiliza la función
                                        // ActualizarCargoComoNoFacturable()  para modificar el cargo.
                                        if(array_key_exists("IdNoFacturables", $datosGrabarCargos) && !empty($datosGrabarCargos['IdNoFacturables']))
                                        {
                                            ActualizarCargoComoNoFacturable($datosGrabarCargos['IdNoFacturables']);
                                        }

                                        $data['mensaje'] .= '<br>
                                                    '.$arr_CARGO['wnomcon'].' > '.$exp_nombPro.' - '.utf8_encode(GrabarCargo($datosGrabarCargos,$idGuardadoBD, false));


                                        // << Sección para guardar el cargo en la tabla de cargos 000106

                                        //************************ CAMBIAR DE RESPONSABLE ************************
                                        if(count($arr_CARGO['arr_cambio_responsable']) > 0)
                                        {
                                            // [arr_cambio_responsable] => Array
                                            // (
                                            //     [historia] => 184454
                                            //     [ingreso] => 1
                                            //     [newResposable] => 800088702
                                            //     [antResponsable] => 860002400
                                            //     [tipoRespAnterior] => 12
                                            //     [nomPaciente] => Alveiro Carlos Marquez
                                            //     [antNomResponsable] => LA PREVISORA S.A. SOAT
                                            //     [nomCajero] =>
                                            // )
                                            cambiarResponsablePaciente($arr_CARGO["arr_cambio_responsable"]["historia"], $arr_CARGO["arr_cambio_responsable"]["ingreso"], $arr_CARGO["arr_cambio_responsable"]["newResposable"], $arr_CARGO["arr_cambio_responsable"]["antResponsable"], $arr_CARGO["arr_cambio_responsable"]["tipoRespAnterior"], $arr_CARGO["arr_cambio_responsable"]["nomPaciente"], $arr_CARGO["arr_cambio_responsable"]["antNomResponsable"], $arr_CARGO["arr_cambio_responsable"]["nomCajero"]);
                                        }

                                        if($idGuardadoBD > 0)
                                        {
                                            $id_CargoAnterior = $idGuardadoBD;
                                            //-----------------------------------------------------
                                            //  --> ACA SE ACTUALIZA EL SALDO DEL TOPE, SI EXISTE
                                            //-----------------------------------------------------
                                            // Se comentó esta sección y se envió el idTope a la función de grabación para que actualice el saldo (tope) si es necesario.
                                            // PARA PODER QUE AL GRABAR EL CARGO TAMBIEN QUEDE GUARDADO EL ID DEL TOPE Y SI HAY UNA ANULACIÓN, EL PROGRAMA PUEDA SUMAR DE NUEVO
                                            // EL VALOR AL SALDO DEL RESPONSABLE QUE LE CORRESPONDE EL TOPE
                                            // if($arr_CARGO["idTope"] != '' && $arr_CARGO["wfacturable"] == 'S')
                                            // {
                                            //     $qActTope = "UPDATE  {$wbasedato}_000204 SET Topsal = (Topsal-(".$arr_CARGO["wvaltarReco"]."))  WHERE id = '".($arr_CARGO["idTope"]*1)."'";

                                            //     $res_update = mysql_query($qActTope, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$qActTope." - ".mysql_error());
                                            // }

                                            $valor_multiplicado = $arr_CARGO['wvaltarReco'];//      $arr_guardar['wvaltarReco']*$wcantidad;
                                            // Guardado en la tabla de liquidaciones de cirugía
                                            $procedimiento_log_cx_arreglado = arreglar_procedimientos_bilaterales($procedimiento_log_cx);
                                            $sql = "INSERT INTO {$wbasedato}_{$TB_LIQUIDACIONES}
                                                    (Medico, Fecha_data, Hora_data, Liqhis, Liqing,
                                                    Liqdoc, Liqcaq, Liqpro, Liqcon, Liqdll,
                                                    Liqter, Liqesp, Liqpor, Liqvlr,
                                                    Liqvlf, Liqfac, Liqgra, Liqblq,
                                                    Liqfca, Liqhca, Liqtta, Liqidc, Liqest, Seguridad)
                                                VALUES
                                                    ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}', '{$whistoria}', '{$wing}',
                                                    '{$wdoc}', '{$diferenciaActoQx}', '{$procedimiento_log_cx_arreglado}', '{$arr_CARGO['wcodcon']}', '{$codigo_equipo_examen}',
                                                    '{$arr_CARGO['wcodter']}', '{$arr_CARGO['wespecialidad']}', '{$arr_CARGO['porcentaje_cxMult_CARGO']}', '{$arr_CARGO['wvaltarReco']}',
                                                    '{$valor_multiplicado}', '{$arr_CARGO['wfacturable']}', '{$arr_CARGO['wgrabar']}', '{$arr_CARGO['wbaseliquidacion']}',
                                                    '{$wfecha_cargo}', '{$whora_cargo}', '{$arr_CARGO['wtipfac']}', '{$idGuardadoBD}', 'on', 'C-{$wbasedato}')";

                                            if($result = mysql_query($sql,$conex))
                                            {
                                                // Marcar insumos como liquidados
                                                $id_insumo_207 = $arr_CARGO["id_insumo"];
                                                if(!empty($id_insumo_207))
                                                {
                                                    $sql_liq = "UPDATE {$wbasedato}_000207 SET Mpaliq = 'on' WHERE id = '{$id_insumo_207}'";
                                                    if($result_liq = mysql_query($sql_liq,$conex))
                                                    {
                                                        // Se marca insumo como liquidado para no ternelo en cuenta en una nueva liquidación.
                                                    }
                                                }
                                                // Guardado en la tabla de liquidaciones de cirugía
                                                $data['mensaje_local'] = 'Datos guardados';
                                                if(!array_key_exists($procedimiento_log_cx, $arr_encabezado_conDlle))
                                                {
                                                    $arr_encabezado_conDlle[$procedimiento_log_cx] = array();
                                                }
                                                $arr_encabezado_conDlle[$procedimiento_log_cx][] = $idGuardadoBD;
                                            }
                                            else
                                            {
                                                unset($arr_encabezado[$wprocedimiento]); // Evíta que se guarde encabezado si no se pudo guardar el cargo.
                                            }
                                        }
                                        else
                                        {
                                            // unset($arr_encabezado[$wprocedimiento]); // Evíta que se guarde encabezado si no se pudo guardar el cargo.
                                            //$descripcion = "(".mysql_errno().') '.mysql_error()."|obs:'Error al cambiar el estado de aprobación de la solicitud de id. ".$wid_solpend;
                                            // insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wcedula, $sql);
                                            $data['mensaje'] .= "<br>
                                                                ".utf8_encode("No se pudo guardar cargo (". $datosGrabarCargos['wprocod'].") ".$datosGrabarCargos['wpronom']." => (".$datosGrabarCargos['wcodcon'].") ".$datosGrabarCargos['wnomcon']);
                                            $data['error'] = 1;
                                            $data['mensaje_local'] = "No se guardaron cargos";
                                            $data["error_cargo"] = 1;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    foreach ($arr_encabezado as $wprocedimiento => $arr_enc)
                    {
                        // Si se llegó a insertar por lo menos un cargo para ese procedimiento entonces se inserta el encabezado
                        if(array_key_exists($wprocedimiento, $arr_encabezado_conDlle) && count($arr_encabezado_conDlle[$wprocedimiento]) > 0)
                        {
                            $wprocedimiento = arreglar_procedimientos_bilaterales($wprocedimiento);
                            $sql = "INSERT INTO {$wbasedato}_{$TB_ENC_LIQUIDACION}
                                        (Medico, Fecha_data, Hora_data, Enlhis, Enling, Enldoc,
                                        Enlcaq, Enlpro, Enltan,
                                        Enltsr, Enltus, Enltcx,
                                        Enlter, Enlesp, Enlorg, Enlbil, Enlvia,
                                        Enlpqt, Enlest, Seguridad)
                                    VALUES
                                        ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}', '{$whistoria}', '{$wing}', '{$wdoc}',
                                        '{$diferenciaActoQx}', '{$wprocedimiento}', '{$arr_enc['wtipo_anestesia_cx']}',
                                        '{$arr_enc['wtiempo_sala_recuperarcion']}', '{$arr_enc['wtiempo_uso_minutos']}', '{$arr_enc['wtiempo_minutos_cx']}',
                                        '{$arr_enc['wespecialista']}', '{$arr_enc['wespecialidad']}', '{$arr_enc['worgano']}', '{$arr_enc['wbilateral']}', '{$arr_enc['wvia']}',
                                        '{$es_paquete}', 'on', 'C-{$wbasedato}')";

                            if($result = mysql_query($sql,$conex))
                            {
                                // $descripcion = "tabla:'".$wbasedato."_".SOLICITUDES."'|id:'$wid_solpend'|columnUpd:'Solapr'|columnFiltro:'Solfue-id'|valueFiltro:'".$wfuente."-".$wid_solpend."'|obs:' La solicitud con id. $wid_solpend combió su estado de aprobación a -$westadoapr-'";
                                // insertLog($conex, $wbasedato, $user_session, $accion, $form, '', $descripcion, $wcedula);
                                $data['mensaje'] = 'Datos guardados';
                            }
                            else
                            {
                                //$descripcion = "(".mysql_errno().') '.mysql_error()."|obs:'Error al cambiar el estado de aprobación de la solicitud de id. ".$wid_solpend;
                                // insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wcedula, $sql);
                                $data['mensaje'] .= utf8_encode("No se pudo pudo guardar encabezado de procedimientos para [".$arr_CARGO['wprocedimiento']."]\n.");
                                $data['error'] = 1;
                            }
                        }
                    }
                    // exit();
                break;

                default :
                        $data['mensaje'] = utf8_encode($no_exec_sub);
                        $data['error'] = 1;
                break;
            }
            echo json_encode($data);
            break;

        case 'update' :
            switch($form)
            {
                case 'modificar_datos_liquidados':
                    /*Array
                    (
                        [07730] => Array
                            (
                                [0_0022] => Array
                                    (
                                        [wprocedimiento] => 07730
                                        [wprocedimiento_nombre] => 07730-APENDICECTOMIA
                                        [wconcepto] => 0022
                                        [wconcepto_nombre] => DERECHOS SALA CIRUGIA
                                        [wtercero] =>
                                        [wtercero_nombre] =>
                                        [wespecialidad] =>
                                        [wespecialidad_nombre] =>
                                        [wporcentaje] => 100
                                        [wvalor] => 7060
                                        [wvalor_final] => 0
                                        [wfacturable] => S   (o 'N')
                                        [wgrabar] => on
                                    )*/
                    $arr_datos_liquidados = unserialize(base64_decode($arr_datos_liquidados));

                    //valor_nuevo
                    if($etiqueta == 'wtercero')
                    {
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto]['wtercero'] = $wtercero;
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto]['wtercero_nombre'] = $wtercero_nombre;
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto]['wespecialidad'] = $wespecialidad;
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto]['wespecialidad_nombre'] = $wespecialidad_nombre;
                    }
                    elseif($etiqueta == 'wvalor_digitado')
                    {
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto]['wvalor'] = $valor_nuevo;
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto]['wvalor_final'] = $valor_nuevo;
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto]['wvalor_digitado'] = $valor_nuevo;
                    }
                    elseif(!empty($wequip_examen))
                    {
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto][$wequip_examen][$etiqueta] = $valor_nuevo;
                    }
                    else
                    {
                        $arr_datos_liquidados[$wprocedimiento][$wconcepto][$etiqueta] = $valor_nuevo;
                    }
                    $data['arr_datos_liquidados'] = base64_encode(serialize($arr_datos_liquidados));
                break;

                case 'recargar_detalle':
                        include_once("root/comun.php");
                        global $conex, $wemp_pmla;
                        $arr_terceros_especialidad = obtener_array_terceros_especialidad();
                        $arr_parametros = array();
                        $arr_detalle_liquidaciones = listarDetalleLiquidaciones($conex, $wemp_pmla, $wbasedato, $whistoria, $wing, $arr_parametros);
                        $arr_parametros['arr_terceros'] = $arr_terceros_especialidad;

                        $data['html'] = pintarDetalleLiquidaciones($arr_detalle_liquidaciones, $arr_parametros);
                    break;

                case 'anular_cargos_cirugia':
                        include_once("root/comun.php");
                        $concepto_medicamentos_mueven_inv = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_medicamentos_mueven_inv');
                        $concepto_materiales_mueven_inv   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_materiales_mueven_inv');

                        // dif_acto
                        // ids_cargos_106
                        $expl_cargos = (!empty($ids_cargos_106)) ? explode("|", $ids_cargos_106) : array();
                        $wuse = $user_session;
                        $wfecha = date("Y-m-d");
                        $whora = date("H:i:s");

                        // inactivar y anular los cargos en la tabla cliame_106
                        if(count($expl_cargos) > 0)
                        {
                            global $conex, $wbasedato, $wemp_pmla, $wuse, $wfecha, $whora;
                            $error     = "";
                            $error_msj = "";
                            foreach ($expl_cargos as $key => $id_cargo)
                            {
                                $data_anular =  anular($id_cargo);
                                if(array_key_exists("Error", $data_anular))
                                {
                                    $error     = ($data_anular["Error"]) ? 1: 0;
                                    $error_msj = $data_anular["Mensaje"];
                                }
                            }
                            $data['error']   = $error;
                            $data['mensaje'] = $error_msj;

                            // Consultar los insumos y cambiarles el estado en la tabla de mercado
                            $sql = "SELECT  t198.Liqdll AS codigo_insumo, t198.Liqhis AS historia, t198.Liqing AS ingreso, t199.Enlpro AS procedimiento
                                    FROM    {$wbasedato}_000199 AS t199
                                            INNER JOIN
                                            {$wbasedato}_000198 AS t198 ON (t198.Liqhis = t199.Enlhis AND t198.Liqing = t199.Enling AND t198.Liqdoc = t199.Enldoc AND t198.liqcaq = t199.Enlcaq)
                                    WHERE   t199.Enlcaq = '{$dif_acto}'
                                            AND t198.Liqcon = '{$concepto_medicamentos_mueven_inv}' OR t198.Liqcon = '{$concepto_materiales_mueven_inv}'
                                            AND t198.Liqest = 'on'";
                            $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                            $arr_insumos = array();
                            while ($row = mysql_fetch_array($result))
                            {
                                if(!array_key_exists($row['procedimiento'], $arr_insumos))
                                {
                                    $arr_insumos[$row['procedimiento']] = array();
                                }

                                $index = $row['historia'].'_'.$row['ingreso'];
                                if(!array_key_exists($index, $arr_insumos[$row['procedimiento']]))
                                {
                                    $arr_insumos[$row['procedimiento']][$index] = array();
                                }
                                $arr_insumos[$row['procedimiento']][$index][] = $row['codigo_insumo'];
                            }

                            if(count($arr_insumos) > 0)
                            {
                                foreach ($arr_insumos as $wprocedimiento => $lista_historia_ingreso)
                                {
                                    foreach ($lista_historia_ingreso as $his_ing => $lista_insumos)
                                    {
                                        $codigos_insumo = implode("','", $lista_insumos);
                                        $expl_HI        = explode("_", $his_ing);
                                        $historia_in    = $expl_HI[0];
                                        $ingreso_in     = $expl_HI[1];
                                        $sql = "UPDATE  {$wbasedato}_000207
                                                SET     Mpaliq = 'off'
                                                WHERE   Mpahis = '{$historia_in}'
                                                        AND Mpaing = '{$ingreso_in}'
                                                        AND Mpapro = '{$wprocedimiento}'
                                                        AND Mpaest = 'on'
                                                        AND Mpacom IN ('{$codigos_insumo}')";
                                        $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                                    }
                                }
                            }

                            $sql = "UPDATE {$wbasedato}_000198 SET Liqest = 'off', Liqfan = '{$fecha_actual}', Liqhan = '{$hora_actual}', Liquan = 'C-{$user_session}'
                                    WHERE   Liqcaq = '{$dif_acto}'";
                            $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

                            $sql = "UPDATE {$wbasedato}_000199 SET Enlest = 'off', Enlfan = '{$fecha_actual}', Enlhan = '{$hora_actual}', Enluan = 'C-{$user_session}'
                                    WHERE   Enlcaq = '{$dif_acto}'";
                            $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

                        }
                    break;

                case 'estadoCuentaCongelada':
                        $wuse = $user_session; //$_SESSION['user'];
                        global $conex, $wbasedato, $wuse;
                        $data = estadoCongelacionCuentaPaciente($historia, $ingreso);
                        // --> Si hay un encabezado
                        if($data['hayEncabezado'])
                        {
                            $data = $data['valores'];
                        }
                        else
                        {
                            $data['Ecoest'] = 'off';
                        }
                        $data['wuse'] = $wuse;
                break;

                case 'congelarCuentaPaciente':
                        $wuse = $user_session; //$_SESSION['user'];
                        global $conex, $wbasedato, $wuse;
                        if(!empty($historia))
                        {
                            congelarCuentaPaciente($historia, $ingreso, 'QX', $congelar);
                        }
                break;

                case 'guardar_temporal':
                        $datos_temporales = array(  "tabla_lista_cxs"            => trim($tabla_lista_cxs),
                                                    "arr_datos_liquidar"         => $arr_datos_liquidar,
                                                    "arr_datos_liquidados"       => $arr_datos_liquidados,
                                                    "arr_extras"                 => $arr_extras,
                                                    "wnumvias"                   => $wnumvias,
                                                    "wfecha_cargo"               => $wfecha_cargo,
                                                    "whora_cargo"                => $whora_cargo,
                                                    "wpolitraumatizado"          => $wpolitraumatizado,
                                                    "wtipo_anestesia_cx"         => $wtipo_anestesia_cx,
                                                    "wtiempo_sala_recuperarcion" => $wtiempo_sala_recuperarcion,
                                                    "wtiempo_uso_minutos"        => $wtiempo_uso_minutos,
                                                    "wtiempo_minutos_cx"         => $wtiempo_minutos_cx,
                                                    "wliq_paquete"               => $wliq_paquete,
                                                    "id_encabezado_sin_tarifa"   => $id_encabezado_sin_tarifa,
                                                    "arr_CARGOS_PARA_GRABAR"     => $arr_CARGOS_PARA_GRABAR);
                        guardar_datos_temporales($conex, $wbasedato, $datos_temporales, $fecha_actual, $hora_actual, $whistoria, $wing, $user_session, $temporal);
                    break;

                default :
                        $data['mensaje'] = utf8_encode($no_exec_sub);
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'load' :
            switch($form)
            {
                case 'cargar_datos_paciente':
                    include_once("root/comun.php");
                    global $conex, $wemp_pmla;
                    $wuse = $user_session;
                    global $conex, $wemp_pmla, $wbasedato, $wuse;
                    $data = cargar_datos($whistoria, $wing, $wcargos_sin_facturar, $welemento);

                    if(!array_key_exists("wwing", $data))
                    {
                        $data["wwing"] = "";
                    }

                    $arr_datos_liquidar = array();
                    if(!empty($data["wdoc"]))
                    {
                        $arr_datos_liquidar = array("arr_datos_paciente" =>
                                                            array(
                                                                    "whistoria"               => $whistoria,
                                                                    "wing"                    => $wing,
                                                                    "wno1"                    => $data["wno1"],
                                                                    "wno2"                    => $data["wno2"],
                                                                    "wap1"                    => $data["wap1"],
                                                                    "wap2"                    => $data["wap2"],
                                                                    "wdoc"                    => $data["wdoc"],
                                                                    "wnomemp_eps"             => $data["wnomemp"],
                                                                    "tarifa_original"         => $data["wtar"],
                                                                    "wtip_paciente"           => $data["wtip_paciente"],
                                                                    "wtipo_ingreso"           => $data["tipo_ingreso"],
                                                                    "wtipo_ingreso_nom"       => $data["nombre_tipo_ingreso"],
                                                                    "wresponsable_eps_codigo" => $data["wcodemp"],
                                                                    "wresponsable_eps"        => $data["wcodemp"].'-'.$data["wnomemp"],
                                                                    "tipoEmpresa"             => $data["tipoEmpresa"]
                                                            ),
                                                    "wnumero_vias"               => "",
                                                    "wtipo_anestesia"            => "",
                                                    "wtiempo_sala_recuperarcion" => "",
                                                    "wtiempo_uso_minutos"        => "",
                                                    "wtiempo_minutos_cx"         => "",
                                                    "arr_para_liquidar"          => array());
                    }

                    // CREAR ARRAY DE PROCEDIMIENTOS Y DE PAQUETES QUE TENGAN AUTORIZACIÓN MÁS LOS QUE TENGAN MERCADO
                    // PROCEDIMIENTOS CON MERCADO
                    $arr_procedimientos = array();
                    $sql = "SELECT  t103.Procod AS codigo, t103.Pronom AS nombre
                            FROM    {$wbasedato}_000207 AS t207
                                    INNER JOIN
                                    {$wbasedato}_000103 AS t103 ON (t207.Mpapro = t103.Procod AND t103.Proest = 'on')
                            WHERE   t207.Mpahis = '{$whistoria}'
                                    AND t207.Mpaing = '{$data['wwing']}'
                                    AND LEFT(t207.Mpapro, 2) <> 'CP'
                            GROUP BY t103.Procod
                            ORDER BY  t103.Pronom";
                    $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                    while($row = mysql_fetch_array($result))
                    {
                        if(!array_key_exists($row['codigo'], $arr_procedimientos))
                        {
                            $arr_procedimientos[$row['codigo']] = array();
                        }
                        $arr_procedimientos[$row['codigo']] = utf8_encode($row['nombre']);
                    }

                    // PROCEDIMIENTOS AUTORIZADOS
                    $sql = "SELECT  codigo, nombre
                            FROM    {$wbasedato}_000205, {$wbasedato}_000209, root_000012
                            WHERE   Reshis = '{$whistoria}'
                                    AND Resing = '{$data['wwing']}'
                                    AND Resest = 'on'
                                    AND Resnit = Cprnit
                                    AND Resaut = Cpraut
                                    AND Cprest = 'on'
                                    AND Cprcup = Codigo";
                    $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                    while($row = mysql_fetch_array($result))
                    {
                        if(!array_key_exists($row['codigo'], $arr_procedimientos))
                        {
                            $arr_procedimientos[$row['codigo']] = array();
                        }
                        $arr_procedimientos[$row['codigo']] = utf8_encode($row['nombre']);
                    }

                    // Ordenar procedimientos autorizados y con mercado
                    natcasesort($arr_procedimientos);

                    $arr_procedimientos_orig = unserialize(base64_decode($arr_procedimientos_orig));
                    $arr_procedimientos_temp = obtener_array_procedimientosEmpresa($conex, $wemp_pmla, $wbasedato, $data['wcodemp'], $arr_procedimientos_orig);

                    $data["arr_procedimientos"]      = "".json_encode($arr_procedimientos)."";
                    $data["arr_procedimientos_temp"] = "".json_encode($arr_procedimientos_temp)."";

                    $data["arr_datos_liquidar"] = base64_encode(serialize($arr_datos_liquidar));
                    $data["arr_datos_liquidar_temp"] = base64_encode(serialize($arr_datos_liquidar));
                    // $guardar = "data: ".print_r($data,true).PHP_EOL;
                    // seguimiento($guardar);
                break;

                case 'generar_datos_liquidar':
                        include_once("root/comun.php");
                        /*  PARÁMETROS PRINCIPALES DEL PROGRAMA
                            $arr_datos_liquidar
                            $arr_datos_liquidados
                            $arr_extras
                            $orden_conceptos_por_procedimiento
                            $arr_procedimientos_orden
                            // id_concepto_uso_equipos
                            // id_concepto_examenes
                        */

                       $es_paquete = ($es_paquete == 'on') ? true: false;
                       $arr_cargos_sinTariga = array();
                       $cargosSinTarifas = array();

                        $arr_CARGOS_PARA_GRABAR = array(); // este array se encargará de guardar los cargos que no tienen tarifa para que puedan ser mostrados en el monitor de tarifas por crear.
                        global $arr_CARGOS_PARA_GRABAR, $arr_cargos_sinTariga;

                        $SALDO_VIRTUAL_PACIENTE       = 0;
                        $orden_conceptos_por_procedimiento = array(); // se debe tener el mimsmo orden tanto para liquidar como para grabar los cargos y poder que las cifras tengan un mayor margen de coincidencia.

                        $RESPONSABLE_VIRTUAL_PACIENTE     = array("Ingcem"=>$wcod_empresa, "Ingent"=>$wnomemp_tal, "Ingtar"=>$wtarifa_empresa, "tipoEmpre"=>$tipoEmpresa);
                        $codEmpParticular                 = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
                        $concepto_medicamentos_mueven_inv = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_medicamentos_mueven_inv');
                        $concepto_materiales_mueven_inv   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_materiales_mueven_inv');
                        $wbasedato_movhos                 = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
                        $concepto_honorario_ayudantia     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_honorario_ayudantia');
                        $tarifas_modifican_valor_erp      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tarifas_modifican_valor_erp');
                        $conceptoRepetirPorcentajeMultiple= consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_honorario_erp'); // aplica solo para diferente vía diferente especialidad cuando hay mas de una cirugía

                        $xpl_tar = (!empty($tarifas_modifican_valor_erp)) ? explode(",", $tarifas_modifican_valor_erp) : array();

                        // Array que indica cuales tarifas deben permitir escribir o modificar valor siempre, en los conceptos que permitan digitar valor
                        // no está limitado a que tenga que existir tarifa sino simplemente dejar escribir valor siempre.
                        $arr_tarifas_modifican_valor_erp = array();
                        foreach ($xpl_tar as $key => $value)
                        {
                            if(!array_key_exists($value, $arr_tarifas_modifican_valor_erp))
                            {
                                $arr_tarifas_modifican_valor_erp[$value] = $value;
                            }
                        }

                        $porcentajes_CX_bilaterales       = "off"; // Para controlar si se deben escoger los porcentajes de cirugías bilaterales o de un acto quirúrgico normal
                        $cont_CX_bilaterales              = 0; // Cuenta las cirugías bilaterales, es útil porque cuando el contado sea mayor a 1 entonces se debe cambiar la variable $porcentajes_CX_bilaterales a on y tomar los porcentajes de bilaterales
                        global $RESPONSABLE_VIRTUAL_PACIENTE, $orden_conceptos_por_procedimiento, $id_concepto_uso_equipos, $id_concepto_examenes, $add_equipo, $add_examen, $add_equipo_nombre, $add_examen_nombre,
                                $concepto_medicamentos_mueven_inv, $concepto_materiales_mueven_inv, $porcentajes_CX_bilaterales, $cont_CX_bilaterales, $wfecha_cargo, $arr_tarifas_modifican_valor_erp;

                        $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
                        $TB_BASE_LIQ_ACTOSQX = TB_BASE_LIQ_ACTOSQX;
                        $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;

                        $arr_datos_liquidar    = unserialize(base64_decode($arr_datos_liquidar));
                        $arr_datos_liquidados  = unserialize(base64_decode($arr_datos_liquidados));
                        $arr_extras            = unserialize(base64_decode($arr_extras));
                        $arr_conceptos_nombres = unserialize(base64_decode($arr_conceptos_nombres));
                        $data['procedimientos_materiales'] = array();

                        // $guardar = "arr_datos_liquidados: ".print_r($arr_datos_liquidados,true).PHP_EOL;
                        // seguimiento($guardar);

                        $arr_conceptos_manuales_cx = consultarConceptosManuales($conex, $wemp_pmla, $wbasedato);
                        $codTipoSoat    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresasoat');
                        $codEmpPartic   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
                        $codTipoMpa     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresampa');

                        $sql = "SELECT  Resord, Resnit, Emppar, Emptar, Emptem, Empnom, Empnit, Emppar
                                FROM    {$wbasedato}_000205, {$wbasedato}_000024
                                WHERE   Reshis = '{$whistoria}'
                                        AND Resing = '{$wing}'
                                        AND Resest = 'on'
                                        AND Resnit = Empcod
                                        AND Empest = 'on'
                                ORDER BY Resord ASC";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                        // Entidades responsables del paciente
                        $arr_procedimientos_relacionados = array();
                        $arr_entidades_responsables      = array();
                        $arr_entidades_ORDEN_SIMULADO    = array();
                        $arr_primero_segundo_responsable = array("primer_responsable"=>array("codigo"=>"", "tipoEmpresa"=>"", "tipoTarifa"=>"", "paralelo"=>""),"segundo_responsable"=>array("codigo"=>"", "tipoEmpresa"=>"", "tipoTarifa"=>"", "paralelo"=>""));
                        while ($row = mysql_fetch_array($result))
                        {
                            $nit_rep         = $row['Resnit'];
                            $orden_emp       = $row['Resord'];
                            $tipo_emp        = $row['Emptem'];
                            $tipo_tarifa     = $row['Emptar'];
                            $maneja_paralelo = $row['Emppar'];
                            if(!array_key_exists($nit_rep, $arr_entidades_responsables))
                            {
                                $arr_entidades_responsables[$nit_rep] = array("simulacion_campos"=>array(),"liquidacion_simulada"=>array());
                            }

                            // Asignar segundo responsable, se asigna solo si ya se asignó el primer responsable.
                            $arr_entidades_responsables[$nit_rep]["simulacion_campos"] = array("Resord"=>$row["Resord"], "Resnit"=>$row["Resnit"], "Emppar"=>$row["Emppar"], "Emptar"=>$row["Emptar"], "Emptem"=>$row["Emptem"], "Empnom"=>$row["Empnom"], "Empnit"=>$row["Empnit"]);
                            if(!empty($arr_primero_segundo_responsable["primer_responsable"]["codigo"]) && empty($arr_primero_segundo_responsable["segundo_responsable"]["codigo"]))
                            {
                                $arr_primero_segundo_responsable["segundo_responsable"]["codigo"]      = $nit_rep;
                                $arr_primero_segundo_responsable["segundo_responsable"]["tipoEmpresa"] = $tipo_emp;
                                $arr_primero_segundo_responsable["segundo_responsable"]["tipoTarifa"]  = $tipo_tarifa;
                                $arr_primero_segundo_responsable["segundo_responsable"]["paralelo"]  = $maneja_paralelo;
                            }

                            // Asignar primer responsable
                            if(empty($arr_primero_segundo_responsable["primer_responsable"]["codigo"]))
                            {
                                $arr_primero_segundo_responsable["primer_responsable"]["codigo"]      = $nit_rep;
                                $arr_primero_segundo_responsable["primer_responsable"]["tipoEmpresa"] = $tipo_emp;
                                $arr_primero_segundo_responsable["primer_responsable"]["tipoTarifa"]  = $tipo_tarifa;
                                $arr_primero_segundo_responsable["primer_responsable"]["paralelo"]  = $maneja_paralelo;
                            }

                            if(!array_key_exists($orden_emp, $arr_entidades_ORDEN_SIMULADO))
                            {
                                $arr_entidades_ORDEN_SIMULADO[$nit_rep] = $orden_emp;
                            }
                        }

                        // $guardar = print_r($arr_primero_segundo_responsable,true).PHP_EOL;
                        // seguimiento($guardar);

                        // <<<<<<<<<<<<<<<< ---------------------------------------------------------------- >>>>>>>>>>>>>>>>>>>
                        // <<<<<<<<<<<<<<<< Ordenar primero todos los procedimientos por el número de puntos >>>>>>>>>>>>>>>>>>>
                        $arr_procedimientos_orden     = array();
                        // Para no tener en cuenta en que especialidades quedaron los procedimientos, se crea este array temporal para recorrerlo más adelante y agregarlos al array
                        // arr_procedimientos_liquidar con toda la información
                        $temporal_procedimientos_ordenados = array();
                        // ingresa especialidades
                        $tarifa_original   = $arr_datos_liquidar["arr_datos_paciente"]["tarifa_original"];
                        foreach ($arr_datos_liquidar['arr_para_liquidar'] as $dif_key => $arr_info_temp)
                        {
                            $wespecialidad     = $arr_info_temp["wespecialidad"];
                            $procedimiento_ord = $arr_info_temp["wprocedimiento"];
                            $procedimiento_dif = $arr_info_temp["wprocedimiento_dif"];

                            if(!array_key_exists($procedimiento_ord, $arr_procedimientos_relacionados))
                            {
                                $arr_procedimientos_relacionados[$procedimiento_ord] = $procedimiento_ord;
                            }

                            // Consulta los uvrs y se crea un array para ordenarlo
                            $wnumero_puntos        = 0;
                            $valor_grupo           = "";
                            $modalidad_facturacion = "";
                            if(!$es_paquete)
                            {
                                $arr_mod_procedimiento = modalidadPuntosProcedimiento($conex, $wbasedato, $procedimiento_ord, $arr_primero_segundo_responsable["primer_responsable"]["codigo"], $arr_primero_segundo_responsable["primer_responsable"]["tipoEmpresa"], $wcentro_costo);

                                if($arr_mod_procedimiento['tipo_facturacion'] != 'ERR_NO_MODO')
                                {
                                    // $rowProced = mysql_fetch_array($resPro);
                                    $modalidad_facturacion = $arr_mod_procedimiento['tipo_facturacion'];
                                    if($modalidad_facturacion == 'UVR')
                                    {
                                        $wnumero_puntos = $arr_mod_procedimiento['wnumero_uvrs']*1;
                                    }
                                    elseif($modalidad_facturacion == 'GQX')
                                    {
                                        // Consulta el valor del grupo
                                        $valor_grupo = $arr_mod_procedimiento['valor_grupo'];
                                        $wnumero_puntos = $valor_grupo*1;
                                    }
                                    else
                                    {
                                        // CONSULTAR LA PLANTILLA, CONSULTAR LAS TARIFAS DE LOS PROCEDIMIENTOS ENCONTRADOS Y SUMAR EN UN SOLO TOTAL PARA CADA PROCEDIMIENTO
                                        $data_temp = array();
                                        $arr_conceptos_liquidar_ModCODIGO = array();
                                        $arr_lista_conceptos_temp = array();
                                        $sumatoriaConceptosProcedimiento = 0;

                                        $sumatoriaConceptosProcedimiento = sumatoriaConceptosProcedimientoCODIGO($conex, $wemp_pmla, $wbasedato, $procedimiento_ord, $procedimiento_dif, $data_temp, $arr_datos_liquidar, $arr_conceptos_liquidar_ModCODIGO, $wprocedimiento_add, $wconcepto_add, $arr_extras, $arr_lista_conceptos_temp, $arr_conceptos_nombres, $arr_datos_liquidados, $wespecialidad, $es_paquete, $wcentro_costo, $wcod_empresa, $wtipo_ingreso, $whora_cargo);

                                        $wnumero_puntos = $sumatoriaConceptosProcedimiento;
                                        // Consulta el valor del código
                                        // $qTarif= "  SELECT  t104.Tarvac, t104.Tarfec, t104.Tarvan
                                        //             FROM    {$wbasedato}_000104 AS t104
                                        //             WHERE   t104.Tarcod = '{$procedimiento_ord}'
                                        //                     AND t104.Tarest = 'on'";
                                        // $resTarif = mysql_query($qTarif,$conex) or die("Error: ".mysql_errno()." ".$qTarif." - ".mysql_error());
                                        // $rowTarif = mysql_fetch_array($resTarif);

                                        // $fechaConf = str_replace("-", "", $rowTarif['Tarfec'])*1;
                                        // $fechaAct = str_replace("-", "", $wfecha_cargo)*1;

                                        // // comparar las fechas para saber que valor se debe tomar, si el actual o el anterior
                                        // if($fechaAct >= $fechaConf)
                                        // {
                                        //     $wnumero_puntos = $rowTarif['Tarvac']*1;
                                        // }
                                        // else
                                        // {
                                        //     $wnumero_puntos = $rowTarif['Tarvan']*1;
                                        // }
                                    }
                                }
                            }
                            else
                            {
                                $res_paquete = consultarPaquete($conex, $wbasedato, $wemp_pmla, $procedimiento_ord, $tarifa_original);
                                $sumar_valores_paquete = 0;
                                while ($row_pte = mysql_fetch_array($res_paquete))
                                {
                                    $sumar_valores_paquete += $row_pte['Paqdetvac']*1;
                                }
                                $wnumero_puntos = $sumar_valores_paquete;
                            }

                            if(!array_key_exists($wespecialidad, $temporal_procedimientos_ordenados))
                            {
                                $temporal_procedimientos_ordenados[$wespecialidad] = array();
                            }

                            if(!array_key_exists($procedimiento_dif, $temporal_procedimientos_ordenados[$wespecialidad]))
                            {
                                $temporal_procedimientos_ordenados[$wespecialidad][$procedimiento_dif] = 0;
                            }
                            $temporal_procedimientos_ordenados[$wespecialidad][$procedimiento_dif] = array( "wnumero_puntos"=>$wnumero_puntos*1,
                                                                                                            "dif_key"=>$dif_key,
                                                                                                            "wprocedimiento"=>$dif_key,
                                                                                                            "wnumero_puntos" => $wnumero_puntos,
                                                                                                            "valor_grupo" => $valor_grupo,
                                                                                                            "modalidad_facturacion" => $modalidad_facturacion);
                        }

                        $temp_array = array();
                        foreach ($arr_procedimientos_relacionados as $cod_procedimiento => $value)
                        {
                            if(!array_key_exists($cod_procedimiento, $temp_array))
                            {
                                $temp_array[$cod_procedimiento] = $arr_procedimientos_relacionados;
                                unset($temp_array[$cod_procedimiento][$cod_procedimiento]);
                            }
                        }

                        // Este array crea una relación entre un procedimiento con todos los demás procedimientos del acto quirúrgico
                        $arr_procedimientos_relacionados = $temp_array;

                        // $guardar = "temporal_procedimientos_ordenados: ".print_r($temporal_procedimientos_ordenados,true).PHP_EOL;
                        // $guardar = "arr_procedimientos_relacionados: ".print_r($arr_procedimientos_relacionados,true).PHP_EOL;
                        // seguimiento($guardar);

                        ordenarEspecialidadProcedimiento($temporal_procedimientos_ordenados);

                        // <<<<<<<<<<<<<<<< Ordenar primero todos los procedimientos por el número de puntos >>>>>>>>>>>>>>>>>>>
                        // <<<<<<<<<<<<<<<< ---------------------------------------------------------------- >>>>>>>>>>>>>>>>>>>
                        // $guardar = "temporal_procedimientos_ordenados: ".print_r($temporal_procedimientos_ordenados,true).PHP_EOL;
                        // seguimiento($guardar);
                        $arr_contador_vias_diferentes = array(); // Para saber si hay mas de una vías y poder aplicar porcentajes de diferente vía
                        $arr_procedimientos_liquidar  = array();
                        $arr_especialidades_liq       = array();
                        $arr_contador_vias_diferentes_bilateral = array();  // este array se encarga de contar cuantas vías diferentes hay antes de encontrarse con un procedimiento bilateral
                                                                            // tendrá validez solo si y solo si hay un especialista.

                        // $guardar = "arr_datos_liquidar: ".print_r($arr_datos_liquidar,true).PHP_EOL;
                        // seguimiento($guardar);

                        $aplica_cirugias_multiples  = (count($arr_datos_liquidar['arr_para_liquidar']) > 1) ? true: false;
                        $arr_insumos_procedimiento  = array(); // Listado de medicamentos y materiales que se deben liquidar junto con el concepto-procedimiento
                        $arr_lista_conceptos        = array(); // En este array se guardan todos los conceptos en general que harán parte de la liquidación para poder organizar mejor la visualización
                        $primer_procedimiento       = "";
                        $ctrl_via_primera_bilateral = "";
                        $ctrl_primer_via_procedimiento = ""; //Para guardar el número de vía del primer pricedimiento en la liquidación
                        $ctrl_primer_especialidad_procedimiento = ""; // Para guardar el código de la especialidad del primer procedimiento de la liquidación
                        $arr_ordenProcedimientosTodos_NoEspecialidad = array();

                        foreach ($temporal_procedimientos_ordenados as $cod_espe => $arr_proceds)
                        {
                            foreach ($arr_proceds as $cod_proced => $rowProced)
                            {
                                $dif_key = $rowProced["dif_key"];
                                $arr_proc_liq = $arr_datos_liquidar['arr_para_liquidar'][$dif_key];
                                $arr_conceptos_liquidar     = array();
                                $procedimiento_liquidar_cod = $arr_proc_liq['wprocedimiento'];
                                $wprocedimiento_nombre      = $arr_proc_liq['wprocedimiento_nombre'];
                                $wespecialista              = $arr_proc_liq['wespecialistas'];
                                $wespecialidad              = $arr_proc_liq["wespecialidad"];
                                $numero_de_via              = $arr_proc_liq["wvia"];
                                $procedimiento_bilat_dif    = $arr_proc_liq["wprocedimiento_dif"];
                                $ctrl_wbilateral            = $arr_proc_liq["wbilateral"];

                                if(empty($ctrl_primer_via_procedimiento))
                                {
                                    $ctrl_primer_via_procedimiento = $numero_de_via;
                                }

                                if(empty($ctrl_primer_especialidad_procedimiento))
                                {
                                    $ctrl_primer_especialidad_procedimiento = $wespecialidad;
                                }

                                if($ctrl_wbilateral == 'on' && empty($ctrl_via_primera_bilateral))
                                {
                                    $ctrl_via_primera_bilateral = $numero_de_via;
                                }

                                $wnumero_puntos        = $rowProced["wnumero_puntos"];
                                $valor_grupo           = $rowProced["valor_grupo"];
                                $modalidad_facturacion = $rowProced["modalidad_facturacion"];

                                // $wesbilateral               = $arr_proc_liq['wbilateral'];
                                if($primer_procedimiento == "")
                                {
                                    $primer_procedimiento = $procedimiento_bilat_dif;
                                }

                                // Todo concepto adicional que no tenga asociado un procedimiento entonces ese concepto se le adicionará al primer procedimiento
                                // OJO, TENER EN CUENTA QUE SI SE SELECCIONÓ UN PROCEDIMIENTO DESDE EL FORMULARIO PERO RESULTA QUE EL CONCEPTO NO ESTÁ CONFIGURADO NI ACTIVO EN
                                // LOS CONCEPTOS DE MANUALES DE CIRUGÍAS MULTIPLES, POR REGLA SIEMPRE SE LE VA A LIQUIDAR ESE CONCEPTO AL PRIMER! PROCEDIMIENTO ASI SE HAYA SELECCIONADO OTRO.
                                if((!array_key_exists($wconcepto_add, $arr_conceptos_manuales_cx) || empty($wprocedimiento_add) && !empty($wconcepto_add)) && $procedimiento_bilat_dif == $primer_procedimiento)
                                {
                                    $wprocedimiento_add = $primer_procedimiento;
                                }

                                if(!array_key_exists($wespecialidad, $arr_procedimientos_orden))
                                {
                                    $arr_procedimientos_orden[$wespecialidad] = array();
                                }

                                // Consulta los uvrs y se crea un array para ordenarlo
                                if(!array_key_exists($procedimiento_bilat_dif, $arr_procedimientos_orden[$wespecialidad]))
                                {
                                    $arr_procedimientos_orden[$wespecialidad][$procedimiento_bilat_dif] = $wnumero_puntos;
                                }

                                if(!array_key_exists($wespecialidad, $arr_especialidades_liq))
                                {
                                    $arr_especialidades_liq[$wespecialidad] = $wespecialidad;
                                }

                                // En un array se ingresan todos los procedimientos con su número de puntos respectivos, esto con el fin de ordenar
                                // Todos los procedimientos de mayor a menor sin importar la especialidad, este dato será útil cuando se liquide
                                // diferente especialista y diferente vía, y poder cobrar por ejemplo el concepto de Ayudantía solo hasta el segundo
                                // procedimiento mayor en UVR o GRUPO y para las demás cirugías 0%
                                if(!array_key_exists($procedimiento_bilat_dif, $arr_ordenProcedimientosTodos_NoEspecialidad))
                                {
                                    $arr_ordenProcedimientosTodos_NoEspecialidad[$procedimiento_bilat_dif] = $wnumero_puntos;
                                }

                                // $ctrl_primer_via_procedimiento != $ctrl_via_primera_bilateral si estas dos variables son diferentes, entonces se debe tomar un acto quirúrgico
                                // donde los porcentajes correspondan a acto por diferente vía, las dos variables están indicando que desde la primer cirugía hasta que se encuentre una
                                // bilateral se conserva la misma vía, entonces se tiene en cuenta un acto quirúrgico por igual via (si despues de la bilateral hay por diferente vía
                                // esto ya se tendrá en cuenta con las vías de bilateralidad), si entre la primer cirugía y la primer bilateral hay cambio de vía entonces se tiene
                                // en cuenta un acto quirúrgico por diferente vía.
                                if(!array_key_exists($numero_de_via, $arr_contador_vias_diferentes_bilateral) && $ctrl_primer_via_procedimiento != $ctrl_via_primera_bilateral )
                                {
                                    $arr_contador_vias_diferentes_bilateral[$numero_de_via] = $numero_de_via;   // ESTE ARRAY ES PARA SABER LAS VÍAS DE LOS PROCEDIMIENTOS
                                                                                                                // QUE ESTAN RELACIONADOS A UNA ESPECIALIDAD Y HAY INVOLUCRADO UN PROCEDIMIENTO BILATERAL
                                }

                                if(!array_key_exists($numero_de_via, $arr_contador_vias_diferentes))
                                {
                                    $arr_contador_vias_diferentes[$numero_de_via] = $numero_de_via; // ESTE ARRAY ES GENERAL PARA TODOS LOS PROCEDIMIENTOS
                                }


                                $arr_datos_liquidar["wnumero_vias"] = count($arr_contador_vias_diferentes); // Asigna el número de vías encontradas pasa saber si aplica cirugías múltiples por diferente vía.

                                if(!array_key_exists($procedimiento_bilat_dif, $arr_procedimientos_liquidar))
                                {
                                    $via_ = (isset($arr_proc_liq['wvia'])) ? $arr_proc_liq['wvia']: '--';
                                    $arr_procedimientos_liquidar[$procedimiento_bilat_dif] = array( "procedimiento_dif"          => $procedimiento_bilat_dif,
                                                                                                    "codigo"                     => $procedimiento_liquidar_cod,
                                                                                                    "nombre"                     => $wprocedimiento_nombre,
                                                                                                    "wesbilateral"               => $arr_proc_liq['wbilateral'],
                                                                                                    "wposicion_organo_nom"       => $arr_proc_liq['wposicion_organo_nom'],
                                                                                                    "wvia"                       => $via_,
                                                                                                    "wtipo_anestesia_cx"         => $wtipo_anestesia_cx, //$arr_proc_liq['wtipo_anestesia_cx'],
                                                                                                    "wtiempo_sala_recuperarcion" => $wtiempo_sala_recuperarcion, //$arr_proc_liq['wtiempo_sala_recuperarcion'],
                                                                                                    "wtiempo_uso_minutos"        => $wtiempo_uso_minutos, //$arr_proc_liq['wtiempo_uso_minutos'],
                                                                                                    "wtiempo_minutos_cx"         => $wtiempo_minutos_cx, //$arr_proc_liq['wtiempo_minutos_cx'],
                                                                                                    "wespecialistas_nombre"      => $arr_proc_liq["wespecialistas_nombre"], // Estos datos son para crear un check en liquidar para cargar automaticamente el medico.
                                                                                                    "wespecialista"              => $wespecialista,                         // Estos datos son para crear un check en liquidar para cargar automaticamente el medico.
                                                                                                    "wespecialidad"              => $wespecialidad,                         // Estos datos son para crear un check en liquidar para cargar automaticamente el medico.
                                                                                                    "wespecialidad_nombre"       => $arr_proc_liq["wespecialidad_nombre"],  // Estos datos son para crear un check en liquidar para cargar automaticamente el medico.
                                                                                                    "wnumero_puntos"             => $wnumero_puntos,
                                                                                                    "wgrupo_gqx"                 => $valor_grupo,
                                                                                                    "modalidad_facturacion"      => $modalidad_facturacion,
                                                                                                    "configuracion_liquidar"     => array(),
                                                                                                    "especialistas"              => array(),
                                                                                                    "arr_encabezado"             => $arr_proc_liq );
                                }

                                if(!array_key_exists($wespecialista, $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["especialistas"]))
                                {
                                    $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["especialistas"][$wespecialista] = array( "wespecialistas_nombre"=> $arr_proc_liq["wespecialistas_nombre"],
                                                                                                                                        "wespecialista"        => $wespecialista,
                                                                                                                                        "wespecialidad"        => $wespecialidad,
                                                                                                                                        "wespecialidad_nombre" => $arr_proc_liq["wespecialidad_nombre"]);
                                }

                                $arr_conceptos_liquidar = $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["configuracion_liquidar"];

                                if(!array_key_exists($procedimiento_bilat_dif, $orden_conceptos_por_procedimiento)) { $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif] = array(); }
                                if(!empty($wprocedimiento_add) && !array_key_exists($wprocedimiento_add, $orden_conceptos_por_procedimiento)) { $orden_conceptos_por_procedimiento[$wprocedimiento_add] = array(); }

                                // llamar función para consultar configuración de plantilla para liquidación, esta función modifica el array '$arr_conceptos_liquidar'
                                consultarConfiguracionPlantilla($conex, $wemp_pmla, $wbasedato, $procedimiento_liquidar_cod, $procedimiento_bilat_dif, $data, $arr_datos_liquidar, $arr_conceptos_liquidar, $wprocedimiento_add, $wconcepto_add, $arr_extras, $arr_lista_conceptos, $arr_conceptos_nombres, $arr_datos_liquidados, $arr_datos_liquidar['arr_datos_paciente']['wresponsable_eps_codigo'], $arr_datos_liquidar['arr_datos_paciente']['tarifa_original'], $arr_datos_liquidar['arr_datos_paciente']['tipoEmpresa'], $wespecialidad, $es_paquete);

                                // Si el primer responsable es SOAT y el segundo es PREPAGADA, Adicionalmente si el segundo es paralelo entonces adicione los conceptos del paralelo al primer responsable
                                if($arr_primero_segundo_responsable['primer_responsable']['tipoEmpresa'] == $codTipoSoat
                                    && !empty($arr_primero_segundo_responsable["segundo_responsable"]["codigo"])
                                    && $arr_primero_segundo_responsable["segundo_responsable"]["tipoEmpresa"] == $codTipoMpa
                                    && $arr_primero_segundo_responsable["segundo_responsable"]["paralelo"] == 'on')
                                {
                                    $wresponsable_eps_codigo = $arr_primero_segundo_responsable["segundo_responsable"]["codigo"];
                                    $tipoEmpresaSegunda      = $arr_primero_segundo_responsable["segundo_responsable"]["tipoEmpresa"];
                                    $tarifa_original         = $arr_primero_segundo_responsable["segundo_responsable"]["tipoTarifa"];

                                    consultarConfiguracionPlantilla($conex, $wemp_pmla, $wbasedato, $procedimiento_liquidar_cod, $procedimiento_bilat_dif, $data, $arr_datos_liquidar, $arr_conceptos_liquidar, '', '', $arr_extras, $arr_lista_conceptos, $arr_conceptos_nombres, $arr_datos_liquidados, $wresponsable_eps_codigo, $tarifa_original, $tipoEmpresaSegunda, $wespecialidad, $es_paquete);
                                }
                                $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["configuracion_liquidar"] = $arr_conceptos_liquidar;
                            }
                        }

                        // si solo hay un especialista y hay una cirugía bilateral, entonces tiene en cuenta la cantidad de vías diferentes que hay entre la primer
                        // cirugía de la liquidación y la primer cirugía bilateral.
                        if(count($arr_especialidades_liq) == 1)
                        {
                            $arr_contador_vias_diferentes = $arr_contador_vias_diferentes_bilateral;
                            $arr_datos_liquidar["wnumero_vias"] = count($arr_contador_vias_diferentes);
                        }

                        // Se ordenan de mayor a menor cantidad de puntos todos los procedimientos sin importar las especialidades.
                        arsort($arr_ordenProcedimientosTodos_NoEspecialidad);
                        $arr_tempCx = $arr_ordenProcedimientosTodos_NoEspecialidad;
                        // Reescribe el array $arr_ordenProcedimientosTodos_NoEspecialidad para agregarle la posición de número de cirugía como índice;
                        $cont_orden = 1;
                        foreach ($arr_tempCx as $codpro_orden => $numeroPuntosOrden)
                        {
                            $arr_ordenProcedimientosTodos_NoEspecialidad[$codpro_orden] = $cont_orden;
                            $cont_orden++;
                        }

                        if(array_key_exists(0, $arr_ordenProcedimientosTodos_NoEspecialidad)) { unset($arr_ordenProcedimientosTodos_NoEspecialidad[0]); }

                        // <<<<<<<<<<<<<<<<<<<<<<<      DEBUG      >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
                        // $guardar = "arr_especialidades_liq :".print_r($arr_especialidades_liq,true).PHP_EOL;
                        // $guardar = "arr_procedimientos_liquidar :".print_r($arr_procedimientos_liquidar,true).PHP_EOL;
                        // $guardar = "orden_conceptos_por_procedimiento :".print_r($orden_conceptos_por_procedimiento,true).PHP_EOL;
                        // $guardar = "arr_lista_conceptos: ".print_r($arr_lista_conceptos,true).PHP_EOL;
                        // $guardar = "arr_extras: ".print_r($arr_extras,true).PHP_EOL;
                        // $guardar = "arr_datos_liquidar: ".print_r($arr_datos_liquidar,true).PHP_EOL;
                        // $guardar = "temporal_procedimientos_ordenados: ".print_r($temporal_procedimientos_ordenados,true).PHP_EOL;
                        // $guardar = "arr_ordenProcedimientosTodos_NoEspecialidad: ".print_r($arr_ordenProcedimientosTodos_NoEspecialidad,true).PHP_EOL;
                        // seguimiento($guardar);

                        $arr_cod_material_medicamento = array("material"=>'', "medicamento"=>'');
                        $arr_control_insumos_paralelo = array(); // Para controlar que un insumo se agrege solo una vez por ejemplo cuando hay paralelo, se agrege solo al primero.
                        $arr_control_bilateral_concepto_extra = array(); // Este array es para controlar que los conceptos adicionales que no mueven inventarios no se adicionen a todos los procdimientos bilaterales sino a uno solamente.
                        // Para todos los procedimientos de la lista buscar si hay medicamentos y materiales para liquidar
                        $arr_mercado_completo = array();

                        $limite_puntos_cobro_medicamento    = 0;
                        $limite_concepto_cobro_medicamentos = 0;
                        $limite_puntos_cobro_materiales     = 0;
                        $limite_concepto_cobro_materiales   = 0;

                        $excluido_medicamento               = "";
                        $excluido_material                  = "";

                        $NoFactClase_medicamento            = "";
                        $NoFactClase_material               = "";

                        $excluido_insumos_med               = "";
                        $excluido_insumos_mat               = "";

                        $NoFactClase_med                    = "";
                        $NoFactClase_mat                    = "";
                        $matMedFacturable = array("medicamentos_facturables"=>"S", "materiales_facturables" => "S"
                                                , "NoFactClase_med" => "", "NoFactClase_mat"=>""
                                                , "excluido_insumos_med"=>"", "excluido_insumos_mat"=>""
                                                , "excluido_medicamento"=>"", "excluido_material"=>"");

                        // if(mysql_num_rows($result_insumos) > 0) // no es necesario validar si hay o no insumos, el mercado no facturable por ejemplo se debe comprar para todos los procedimientos así solo este cargado para uno procedimiento en particular.
                        {
                            // $guardar = "sql :".print_r($sql,true).PHP_EOL.PHP_EOL;
                            // seguimiento($guardar);
                            $row_limite = buscarLimitesEnManual($conex, $wbasedato, $tipoEmpresa, $wcod_empresa, $wpolitraumatizado, $TB_BASE_LIQUIDACION);

                            if(count($row_limite) > 0)
                            {
                                // $row_limite = mysql_fetch_array($result_limite);
                                // if($modalidad_facturacion == 'UVR')
                                {
                                    // MEDICAMENTOS
                                    $limite_puntos_cobro_medicamento_UVR    = $row_limite["limite_med_uvr"]*1;
                                    $limite_concepto_cobro_medicamentos_UVR = $row_limite["cobro_limite_med_uvr"];

                                    // MATERIALES
                                    $limite_puntos_cobro_materiales_UVR     = $row_limite["limite_mat_uvr"]*1;
                                    $limite_concepto_cobro_materiales_UVR   = $row_limite["cobro_limite_mat_uvr"];

                                    // Clasificaciones de insumos que se deben facturar o no dependiendo si se supera o no el límite de facturable o no facturable para el concepto de insumos.
                                    $excluido_medicamento_UVR    = $row_limite["excluido_medicamento_uvr"];
                                    $excluido_material_UVR       = $row_limite["excluido_material_uvr"];

                                    $NoFactClase_medicamento_UVR = $row_limite["NoFactClase_medicamento_uvr"];
                                    $NoFactClase_material_UVR    = $row_limite["NoFactClase_material_uvr"];
                                }
                                // elseif($modalidad_facturacion == 'GQX')
                                {
                                    // MEDICAMENTOS
                                    $limite_puntos_cobro_medicamento_GQX    = $row_limite["limite_med_grupo"]*1;
                                    $limite_concepto_cobro_medicamentos_GQX = $row_limite["cobro_limite_med_grupo"];

                                    // MATERIALES
                                    $limite_puntos_cobro_materiales_GQX     = $row_limite["limite_mat_grupo"]*1;
                                    $limite_concepto_cobro_materiales_GQX   = $row_limite["cobro_limite_mat_grupo"];

                                    // Clasificaciones de insumos que se deben facturar o no dependiendo si se supera o no el límite de facturable o no facturable para el concepto de insumos.
                                    $excluido_medicamento_GQX    = $row_limite["excluido_medicamento_grupo"];
                                    $excluido_material_GQX       = $row_limite["excluido_material_grupo"];

                                    $NoFactClase_medicamento_GQX = $row_limite["NoFactClase_medicamento_grupo"];
                                    $NoFactClase_material_GQX    = $row_limite["NoFactClase_material_grupo"];
                                }
                            }
                        }

                        $primer_procedimientos_mayor        = "";
                        $primer_procedimientos_mayor_puntos = "";

                        foreach ($temporal_procedimientos_ordenados as $cod_espe => $arr_proceds)
                        {
                            // foreach ($arr_datos_liquidar['arr_para_liquidar'] as $dif_key => $arr_proc_liq)
                            foreach ($arr_proceds as $cod_proced => $rowProced)
                            {
                                //APROVECHA PARA VERIFICAR SI SE ACABA DE ADICIONAR UN NUEVO CONCEPTO, SE DEBE ADICIONAR PARA TODOS LOS PROCEDIMIENTOS, VERIFICA SI HACE FALTA EN ALGUNO, SI ES ASÍ ENTONCES LO ADICIONA
                                // if(!empty($wconcepto_add) && !array_key_exists($wconcepto_add, search))
                                // {

                                // }
                                $dif_key = $rowProced["dif_key"];
                                $arr_proc_liq = $arr_datos_liquidar['arr_para_liquidar'][$dif_key];

                                $procedimiento_liquidar_cod    = $arr_proc_liq['wprocedimiento'];
                                $procedimiento_bilat_dif       = $arr_proc_liq['wprocedimiento_dif'];
                                $procedimiento_posicion_organo = $arr_proc_liq['wposicion_organo'];
                                $es_bilateral                  = $arr_proc_liq['wbilateral'];
                                $modalidad_facturacion         = $rowProced["modalidad_facturacion"];

                                if($modalidad_facturacion == 'UVR')
                                {
                                    $limite_puntos_cobro_medicamento    = $limite_puntos_cobro_medicamento_UVR;
                                    $limite_concepto_cobro_medicamentos = $limite_concepto_cobro_medicamentos_UVR;
                                    $limite_puntos_cobro_materiales     = $limite_puntos_cobro_materiales_UVR;
                                    $limite_concepto_cobro_materiales   = $limite_concepto_cobro_materiales_UVR;
                                    $excluido_medicamento               = $excluido_medicamento_UVR;
                                    $excluido_material                  = $excluido_material_UVR;
                                    $NoFactClase_medicamento            = $NoFactClase_medicamento_UVR;
                                    $NoFactClase_material               = $NoFactClase_material_UVR;
                                }
                                elseif($modalidad_facturacion == 'GQX')
                                {
                                    $limite_puntos_cobro_medicamento    = $limite_puntos_cobro_medicamento_GQX;
                                    $limite_concepto_cobro_medicamentos = $limite_concepto_cobro_medicamentos_GQX;
                                    $limite_puntos_cobro_materiales     = $limite_puntos_cobro_materiales_GQX;
                                    $limite_concepto_cobro_materiales   = $limite_concepto_cobro_materiales_GQX;
                                    $excluido_medicamento               = $excluido_medicamento_GQX;
                                    $excluido_material                  = $excluido_material_GQX;
                                    $NoFactClase_medicamento            = $NoFactClase_medicamento_GQX;
                                    $NoFactClase_material               = $NoFactClase_material_GQX;
                                }

                                // Inicia el procedimientos para darle un orden de liquidación de sus conceptos.
                                if(!array_key_exists($procedimiento_bilat_dif, $orden_conceptos_por_procedimiento)) { $orden_conceptos_por_procedimiento[$procedimiento_bilat_dif] = array(); }

                                // consultar si hay medicamentos
                                $sql = "SELECT  count(Artesm) AS cantidad_materiales
                                        FROM    {$wbasedato}_000207, {$wbasedato_movhos}_000026
                                        WHERE   Mpahis = '{$whistoria}'
                                                AND Mpaing = '{$wing}'
                                                AND Mpaest = 'on'
                                                AND Mpapro = '{$procedimiento_liquidar_cod}'
                                                AND Mpacom = Artcod
                                                AND Mpaliq <> 'on'
                                                AND Artesm = 'on'";

                                $result_insumos_med = mysql_query($sql,$conex) OR die ($sql.' <br> '.mysql_error());
                                $hay_medicamentos = 0;
                                if(mysql_num_rows($result_insumos_med) > 0)
                                {
                                    $rw_mat_met = mysql_fetch_array($result_insumos_med);
                                    $hay_medicamentos = $rw_mat_met["cantidad_materiales"];
                                }

                                // consultar si hay materiales
                                $sql = "SELECT  count(Artesm) AS cantidad_materiales
                                        FROM    {$wbasedato}_000207, {$wbasedato_movhos}_000026
                                        WHERE   Mpahis = '{$whistoria}'
                                                AND Mpaing = '{$wing}'
                                                AND Mpaest = 'on'
                                                AND Mpapro = '{$procedimiento_liquidar_cod}'
                                                AND Mpacom = Artcod
                                                AND Mpaliq <> 'on'
                                                AND Artesm <> 'on'";

                                $result_insumos_mat = mysql_query($sql,$conex) OR die ($sql.' <br> '.mysql_error());
                                $hay_materiales = 0;
                                if(mysql_num_rows($result_insumos_mat) > 0)
                                {
                                    $rw_mat_met = mysql_fetch_array($result_insumos_mat);
                                    $hay_materiales = $rw_mat_met["cantidad_materiales"];
                                }

                                // **************************************** CONSULTAR INSUMOS ************************************************************************************************
                                // APROVECHAR ESTE CICLO PARA CONSULTAR SI EL PROCEDIMIENTO ACTUAL TIENE MEDICAMENTOS O MATERIALES QUE NECESITEN SER LIQUIDADOS
                                $sql = "SELECT  Mpapro, Mpacom AS codigo_insumo, Mpacan, Mpadev, (Mpacan-Mpadev) AS saldo_insumo, Artcom AS nombre_insumo, Artgru, Artesm, {$wbasedato}_000207.id AS id_insumo
                                        FROM    {$wbasedato}_000207, {$wbasedato_movhos}_000026
                                        WHERE   Mpahis = '{$whistoria}'
                                                AND Mpaing = '{$wing}'
                                                AND Mpaest = 'on'
                                                AND Mpapro = '{$procedimiento_liquidar_cod}'
                                                AND Mpacom = Artcod
                                                AND Mpaliq <> 'on'
                                        ORDER BY Artesm";

                                $result_insumos = mysql_query($sql,$conex) OR die ($sql.' <br> '.mysql_error());

                                // Consultar si hay límite de UVR o Código en los manuales de Cirugías múltiples

                                /**************************************************************************************************************************/
                                /**************************************************************************************************************************/
                                $wnumero_puntos = $arr_procedimientos_liquidar[$procedimiento_bilat_dif]["wnumero_puntos"];
                                if(empty($primer_procedimientos_mayor))
                                {
                                    $primer_procedimientos_mayor        = $procedimiento_bilat_dif;
                                    $primer_procedimientos_mayor_puntos = $wnumero_puntos;
                                }

                                $matMedFacturable = conceptoMatMedFacturable($conex, $wemp_pmla, $wbasedato, $primer_procedimientos_mayor_puntos, $limite_puntos_cobro_medicamento, $excluido_medicamento, $procedimiento_bilat_dif, $limite_concepto_cobro_medicamentos,
                                                    $procedimiento_liquidar_cod, $procedimiento_posicion_organo, $es_bilateral, $arr_lista_conceptos, $arr_procedimientos_liquidar, $NoFactClase_medicamento, $limite_puntos_cobro_materiales, $NoFactClase_material,
                                                    $matMedFacturable, $limite_concepto_cobro_materiales, $excluido_material);

                                /**************************************************************************************************************************/


                                while ($row_insumo = mysql_fetch_array($result_insumos))
                                {
                                    $insumos_facturables           = $matMedFacturable["medicamentos_facturables"];
                                    $limite_puntos_cobro_insumos   = $limite_puntos_cobro_medicamento;
                                    $limite_concepto_cobro_insumos = $limite_concepto_cobro_medicamentos;
                                    $es_medicamento                = $row_insumo["Artesm"];
                                    // $guardar = "es_medicamento".print_r($es_medicamento,true).PHP_EOL;
                                    // seguimiento($guardar);
                                    // $excluido_insumos              = $excluido_insumos_med;
                                    // $NoFactClase_insumo            = $NoFactClase_med;
                                    $arr_Excluye = array();
                                    if(!empty($matMedFacturable["excluido_insumos_med"])) { $arr_Excluye[] = $matMedFacturable["excluido_insumos_med"]; }
                                    if(!empty($matMedFacturable["excluido_insumos_mat"])) { $arr_Excluye[] = $matMedFacturable["excluido_insumos_mat"]; }

                                    $arr_NoFac = array();
                                    if(!empty($matMedFacturable["NoFactClase_med"])) { $arr_NoFac[] = $matMedFacturable["NoFactClase_med"]; }
                                    if(!empty($matMedFacturable["NoFactClase_mat"])) { $arr_NoFac[] = $matMedFacturable["NoFactClase_mat"]; }

                                    $excluido_insumos   = implode("|", $arr_Excluye); // Para unir clasificaciones tanto de medicamentos como de materiales.
                                    $NoFactClase_insumo = implode("|", $arr_NoFac); // Para unir clasificaciones tanto de medicamentos como de materiales.

                                    // $index_control = $index_control_mat;
                                    if($es_medicamento != 'on')
                                    {
                                        $insumos_facturables                      = $matMedFacturable["materiales_facturables"];
                                        $limite_puntos_cobro_insumos              = $limite_puntos_cobro_materiales;
                                        $limite_concepto_cobro_insumos            = $limite_concepto_cobro_materiales;
                                        $arr_cod_material_medicamento["material"] = $limite_concepto_cobro_insumos;
                                        // $index_control = $index_control_med;
                                        // $excluido_insumos                         = $excluido_insumos_mat;
                                        // $NoFactClase_insumo                       = $NoFactClase_mat;
                                    }
                                    else
                                    {
                                        $arr_cod_material_medicamento["medicamento"] = $limite_concepto_cobro_insumos;
                                    }

                                    if($es_paquete)
                                    {
                                        $insumos_facturables      = 'N'; // Siempre los insumos en paquetes van a ser no facturables.
                                        $matMedFacturable["medicamentos_facturables"] = 'N';
                                        $matMedFacturable["materiales_facturables"]   = 'N';
                                    }

                                    $diferencia_bilaterales_organos = $procedimiento_liquidar_cod.'_'.$procedimiento_posicion_organo.'_'.$es_bilateral;

                                    // Luego de separar medicamentos y materiales se debió quitar la validación "&& !in_array($diferencia_bilaterales_organos, $arr_control_bilateral_concepto_extra"
                                    // porque solamente estaba agregando un solo medicamento o un solo insumo y el resto no lo agregaba a la lista, aún asi, el programa siguió asociando todos los medicamentos
                                    // y materiales al primer procedimiento bilateral sin agregarlos al segundo bilateral como es debido (por ahora esta correcto).
                                    if(!array_key_exists($row_insumo['id_insumo'], $arr_control_insumos_paralelo) )//&& !in_array($diferencia_bilaterales_organos, $arr_control_bilateral_concepto_extra)
                                    {
                                        $index_control = $procedimiento_bilat_dif.'_'.$limite_concepto_cobro_insumos;

                                        if(array_key_exists($diferencia_bilaterales_organos, $arr_control_bilateral_concepto_extra))
                                        {
                                            // Esto es para garantizar que cuando hay un procedimiento bilateral solamente se adicionen los insumos para el primer
                                            // procedimiento osea el bilateral1 y para el bilateral2 no le adicione insumos
                                            // el arr_control_bilateral_concepto_extra en la posicion index_control ya tiene un valor pero esta compuesto por $procedimiento_bilat_dif.'_'.$limite_concepto_cobro_insumos;
                                            // aqui se esta modificando por diferencia_bilaterales_organos solamente, y se hace justo cuando se agregan los insumos para el primer procedimiento bilateral, cuando llegue al segundo
                                            // va a encontrar que ya tienen el valor diferencia_bilaterales_organos entonces no ingresará en el ciclo para agregar insumos al segundo bilateral.
                                            // Garantiza que si hay un mismo procedimiento agregado como bilateral con dos posiciones de organo diferentes (p.e. procedimiento tibia o peroné, serían dos procedimientos bilaterales
                                            // diferenciados por la posición del organo) entonces se pueda agregar los insumos solo a las primeras cirugías correspondientes a la bilateralidad.
                                            $arr_control_bilateral_concepto_extra[$diferencia_bilaterales_organos] = $diferencia_bilaterales_organos;
                                        }

                                        $arr_control_insumos_paralelo[$row_insumo['id_insumo']] = $row_insumo["codigo_insumo"];
                                        if(!array_key_exists($procedimiento_bilat_dif, $arr_insumos_procedimiento))
                                        {
                                            $arr_insumos_procedimiento[$procedimiento_bilat_dif] = array();
                                        }

                                        if(!array_key_exists($limite_concepto_cobro_insumos, $arr_insumos_procedimiento[$procedimiento_bilat_dif]))
                                        {
                                            // Consultar los grupos de mendicamentos y materiales a partir de la clasificación general
                                            $arr_FactClase_insumos_04 = array();
                                            $arr_FactClase_insumos_04 = ($es_paquete || empty($excluido_insumos)) ? array() : consultarGruposClasificados($conex, $wbasedato, $excluido_insumos);

                                            $NoFactClase_insumo_grupos_04 = array();
                                            $NoFactClase_insumo_grupos_04 = ($es_paquete || empty($NoFactClase_insumo)) ? array() : consultarGruposClasificados($conex, $wbasedato, $NoFactClase_insumo);

                                            $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos] = array("parametros"=>array(),"lista_insumos"=>array()
                                                                                                                                            ,"clasificacion_facturable"=>implode("|", $arr_FactClase_insumos_04)
                                                                                                                                            ,"grupos_no_facturables"=>implode("|", $NoFactClase_insumo_grupos_04));
                                        }

                                        $clave_insumo = $row_insumo["codigo_insumo"].'_'.$row_insumo["id_insumo"];
                                        if(!array_key_exists($clave_insumo, $arr_mercado_completo))
                                        {
                                            $arr_mercado_completo[$clave_insumo] = $row_insumo;
                                        }

                                        if(!array_key_exists($row_insumo["codigo_insumo"], $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos]["lista_insumos"]))
                                        {
                                            $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos]["lista_insumos"][$row_insumo["codigo_insumo"]] = array();
                                        }

                                        $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos]["lista_insumos"][$row_insumo["codigo_insumo"]] = $row_insumo;

                                        $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos]["parametros"]["insumos_facturables"]           = $insumos_facturables;
                                        $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos]["parametros"]["medicamentos_facturables"]      = $matMedFacturable["medicamentos_facturables"];
                                        $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos]["parametros"]["materiales_facturables"]        = $matMedFacturable["materiales_facturables"];
                                        $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos]["parametros"]["limite_puntos_cobro_insumos"]   = $limite_puntos_cobro_insumos;
                                        $arr_insumos_procedimiento[$procedimiento_bilat_dif][$limite_concepto_cobro_insumos]["parametros"]["limite_concepto_cobro_insumos"] = $limite_concepto_cobro_insumos;
                                    }
                                }
                                // **************************************** CONSULTAR INSUMOS ************************************************************************************************
                            }
                        }

                        $arr_mercado_completo_temp = $arr_mercado_completo;

                        // $guardar = "arr_control_bilateral_concepto_extra :".print_r($arr_control_bilateral_concepto_extra,true).PHP_EOL;
                        // $guardar = "arr_procedimientos_liquidar :".print_r($arr_procedimientos_liquidar,true).PHP_EOL;
                        // $guardar = "arr_lista_conceptos :".print_r($arr_lista_conceptos,true).PHP_EOL;
                        // $guardar = "arr_insumos_procedimiento :".print_r($arr_insumos_procedimiento,true).PHP_EOL;
                        // $guardar = "arr_mercado_completo :".print_r($arr_mercado_completo,true).PHP_EOL;
                        // $guardar = print_r($orden_conceptos_por_procedimiento,true).PHP_EOL;
                        // seguimiento($guardar);

                        // Ordena los conceptos de cada procedimiento
                        foreach ($orden_conceptos_por_procedimiento as $cod_procedimiento => $arr_conceps)
                        {
                            // Debe ser odenado según el mismo criterio que el array "$arr_lista_conceptos" para que te tenga el mismo orden que la simulación de la liquidación
                            natcasesort($orden_conceptos_por_procedimiento[$cod_procedimiento]);
                        }

                        natcasesort($arr_lista_conceptos);  // Ordena alfabéticamente los nombres de los conceptos

                        // Consultar el tipo de Cirugía Multiples
                        // si son varias cirugías entonces busca una configuración de cirugías multiples para determinar qué porcentajes se pagan.

                        $wbaseliquidacion = '';
                        $wbaseliquidacion_nombre = '';
                        $wbaseliquidacion_acto_quirurgico = '';
                        $cx_multiples_requiere_tiempos = "off"; // Para saber si se deben tener en cuenta los tiempos de uso equipos, cirugía, recuperación
                                                                // solo le cobrará tiempos a la primer cirugía y a las demás no.
                        $limite_ayudantia_uvr = '';
                        $limite_ayudantia_grupo = '';
                        $arr_control_primera_cx_tiempos = array();  // es un array que se encarga de guardar el primer procedimiento liquidado, si aplican cirugías multiples entonces

                        // >>>> ES POSIBLE QUE SUS VALORES CAMBIEN CUANDO SE ESTÉ HACIENDO LA SIMULACIÓN, CUANDO HAY CAMBIO DE RESPONSABLE ENTONCES SE CONSULTAN LOS PORCENTAJES PARA ESE TIPO DE EMPRESA Y/O CODIGO DE EMPRESA
                        $arr_porcentajes_multiples = array();
                        $arr_baseLiquidacion = array("wbaseliquidacion" => "", "wbaseliquidacion_nombre" => "", "wbaseliquidacion_acto_quirurgico" => "", "cx_multiples_requiere_tiempos" => "", "limite_ayudantia_uvr" => "", "limite_ayudantia_grupo" => "");
                        // <<<<<

                        $id_cx_multiples = '';
                        global $arr_porcentajes_multiples, $arr_baseLiquidacion, $wpolitraumatizado, $CX_numero_vias, $CX_numero_especialidades, $codEmpParticular, $id_cx_multiples;

                        $arr_parametros_extra      = array();
                        if($aplica_cirugias_multiples)
                        {
                            $CX_numero_vias = '1';
                            if($arr_datos_liquidar["wnumero_vias"] > 1)
                            {
                                $CX_numero_vias = '*';
                            }

                            $CX_numero_especialidades = '1';
                            if(count($arr_especialidades_liq) > 1)
                            {
                                $CX_numero_especialidades = '*';
                            }

                            $arr_porcentajes_multiples = crearArrayPorcentajesMultiples($conex, $wbasedato, $tipoEmpresa, $wcod_empresa, $wpolitraumatizado, $CX_numero_vias, $CX_numero_especialidades, $arr_parametros_extra);
                            $arr_baseLiquidacion = $arr_parametros_extra;

                            $wbaseliquidacion                 = $arr_parametros_extra['wbaseliquidacion'];                // Estos parámetros aplican para el primer responsable
                            $wbaseliquidacion_nombre          = $arr_parametros_extra['wbaseliquidacion_nombre'];         // Estos parámetros aplican para el primer responsable
                            $wbaseliquidacion_acto_quirurgico = $arr_parametros_extra['wbaseliquidacion_acto_quirurgico'];// Estos parámetros aplican para el primer responsable
                            $cx_multiples_requiere_tiempos    = $arr_parametros_extra['cx_multiples_requiere_tiempos'];   // Estos parámetros aplican para el primer responsable
                            // $limite_ayudantia_uvr             = $arr_parametros_extra['limite_ayudantia_uvr'];
                            // $limite_ayudantia_grupo           = $arr_parametros_extra['limite_ayudantia_grupo'];

                            /*arr_porcentajes_multiples
                                Array
                                (
                                    [on] => Array
                                        (
                                            [1] => Array
                                                (
                                                    [0076] => 100
                                                    [0075] => 100
                                                    [0072] => 100
                                                    [0024] => 100


                                            [2] => Array
                                                (
                                                    [0024] => 75
                                                    [0626] => 75
                                                    [0076] => 75
                                                    [0075] => 75
                                                    [0072] => 75
                                                )
                                        )
                                    [off] => Array
                                        (
                                            [1] => Array
                                                (
                                                    [0076] => 100
                                                    [0075] => 100
                                                    [0072] => 100
                                                    [0024] => 100
                                                    [0626] => 100
                                                )
                                            [*] => Array
                                                (
                                                    [0072] => 100
                                                )
                                        )

                                )
                            */
                        }
                        else
                        {
                            // De aquí es importante los parámetros resultantes del array $arr_parametros_extra por ejemplo para saber el límite de cobro de ayudantía
                            // para saber si es facturable o no
                            $arr_temp_porcent = crearArrayPorcentajesMultiples($conex, $wbasedato, $tipoEmpresa, $wcod_empresa, $wpolitraumatizado, "*", "*", $arr_parametros_extra);
                            $arr_parametros_extra['wbaseliquidacion']                 = "";
                            $arr_parametros_extra['wbaseliquidacion_nombre']          = "";
                            $arr_parametros_extra['wbaseliquidacion_acto_quirurgico'] = "";
                            $arr_parametros_extra['cx_multiples_requiere_tiempos']    = "";
                            $arr_baseLiquidacion = $arr_parametros_extra;
                        }

                        $arr_proced_Manual_NoFact = array();
                        // Si se encontró algun manual entonces crear un array de posibles "Procedimientos relacionados no facturables"
                        if(!empty($id_cx_multiples))
                        {
                            $arr_proced_Manual_NoFact = generarArrayRelacionesNoFacturables($conex, $wbasedato, $arr_baseLiquidacion['wbaseliquidacion']);
                        }

                        // $guardar = "arr_parametros_extra: ".print_r($arr_parametros_extra,true).PHP_EOL;
                        // $guardar = "arr_result: ".print_r($arr_result,true).PHP_EOL;
                        // $guardar = "arr_porcentajes_multiples: ".print_r($arr_porcentajes_multiples,true).PHP_EOL;
                        // seguimiento($guardar);

                        // $html = "";
                        // ántes se recorría "$arr_procedimientos_liquidar" normalmente, pero como se debe mostrar un orden de valores (p.e. de UVR)
                        // Entonces se corre el array ordenado y se lee el procedimiento correspondiente en "$arr_procedimientos_liquidar" para
                        // Recuperar el resto de información
                        // foreach ($arr_procedimientos_liquidar as $procedimiento_liquidar_cod => $arr_datos_procedimiento)

                        $sql = "SELECT  Topres, Tophis, Toping, Topres, Toptco, Topcla, Topcco, Topest, Toptop, Toprec, Topdia, Topsal, id AS TopID
                                FROM    {$wbasedato}_000204
                                WHERE   Tophis = '{$whistoria}'
                                        AND Toping = '{$wing}'
                                        AND Topest = 'on'";
                        $resultTopes = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);

                        $arr_TOPES_ENTIDADES = array();
                        while ($rowTope = mysql_fetch_array($resultTopes))
                        {
                            if(!array_key_exists($rowTope["TopID"], $arr_TOPES_ENTIDADES))
                            {
                                $arr_TOPES_ENTIDADES[$rowTope["TopID"]] = array();
                            }
                            $arr_TOPES_ENTIDADES[$rowTope["TopID"]] = array("Topres"=>$rowTope["Topres"],
                                                                            "Tophis"=>$rowTope["Tophis"],
                                                                            "Toping"=>$rowTope["Toping"],
                                                                            "Topres"=>$rowTope["Topres"],
                                                                            "Toptco"=>$rowTope["Toptco"],
                                                                            "Topcla"=>$rowTope["Topcla"],
                                                                            "Topcco"=>$rowTope["Topcco"],
                                                                            "Topest"=>$rowTope["Topest"],
                                                                            "Toptop"=>$rowTope["Toptop"],
                                                                            "Toprec"=>$rowTope["Toprec"],
                                                                            "Topdia"=>$rowTope["Topdia"],
                                                                            "Topsal"=>$rowTope["Topsal"],
                                                                            "TopID" =>$rowTope["TopID"],
                                                                            );
                        }

                        $arr_CargosGrabadosResponsables = array();
                        $arr_CargosGrabadosResponsables_insumos = array();
                        global $arr_entidades_responsables, $arr_entidades_ORDEN_SIMULADO, $arr_TOPES_ENTIDADES,
                               $arr_CargosGrabadosResponsables, $arr_CargosGrabadosResponsables_insumos;

                        // CONSULTAR LOS INSUMOS DE INVENTARIO QUE SE HAYA CONSUMIDO PARA CADA UNO DE LOS PROCEDIMIENTOS.
                        $sql = "SELECT Grucod, Grudes, Gruinv, Gruser, Gruarc
                                FROM {$wbasedato}_000200
                                WHERE Grucod IN ('$concepto_medicamentos_mueven_inv','$concepto_materiales_mueven_inv')";
                        $result = mysql_query($sql,$conex) OR die ($sql.' <br> '.mysql_error());
                        $arr_info_conceptos_inventario = array($concepto_medicamentos_mueven_inv => array(),$concepto_materiales_mueven_inv => array());
                        while ($row = mysql_fetch_array($result))
                        {
                            $arr_info_conceptos_inventario[$row["Grucod"]]["concepto_codigo"]      = $row["Grucod"];
                            $arr_info_conceptos_inventario[$row["Grucod"]]["concepto_nombre"]      = $row["Grudes"];
                            $arr_info_conceptos_inventario[$row["Grucod"]]["mueve_inventario"]     = $row["Gruinv"];
                            $arr_info_conceptos_inventario[$row["Grucod"]]["servicio"]             = $row["Gruser"];
                            $arr_info_conceptos_inventario[$row["Grucod"]]["tabla_valida_precios"] = $row["Gruarc"];
                        }

                        $html_tds_valores = array();
                        $html_tds_TOTALES = array();

                        $arr_procedimientos_orden_liquidar = array();
                        $concepto_no_facturar              = '';
                        $no_facturable_porcambio_manual    = '';
                        $especialid_primera_bilateral      = "";
                        $porcentajes_CX_bilaterales        = 'off';
                        $control_cx_mayor_esBilateral      = false;
                        $primera_cirugia_enLista           = '';
                        $arr_conceptos_maximo_aCobrar      = array();
                        $primer_cantidad_puntos_cambio_responsable = 0;
                        $cod_cambio_responsable            = "";
                        global $ctrl_primer_via_procedimiento, $ctrl_primer_especialidad_procedimiento;

                        $cx_queNoSeCobran = array(); // En este array se guarda la relación procedimiento concepto que no se cobra por ejemplo si hay un límite máximo de cobro de un concepto
                                                    // hasta un nivel máximo de cirugías, por ejemplo, solo se cobra hasta la primer cirugía mayor de de X concepto, todas las demas cirugias
                                                    // mayores en ese concepto se guardan en este array para tener el control que no se tienen que cobrar.
                        $cx_queSISeCobran = array();

                        $numero_cirugia = 0;
                        $esMultipleEspecialidadDifVia = ((count($arr_procedimientos_orden) > 1) && ($arr_datos_liquidar["wnumero_vias"] > 1)) ? true: false;
                        $porcentajeRepetible = "";
                        $arrRepetiblePorEspecialidad = array();
                        foreach ($arr_procedimientos_orden as $wcodigo_especialidad_orden => $arr_procedimientos_orden_por_especialidad)
                        {
                            $arr_procedimientos_bilaterales = array();
                            $arr_procedimientos_NObilaterales = array();
                            $total_procedimientos_especialidad = 0;
                            $via_primera_bilateral = "";
                            $via_cirugia_en_curso = "";
                            $cont_CX_bilaterales = 0;

                            foreach ($arr_procedimientos_orden_por_especialidad as $procedimiento_liquidar_cod_dif => $num_puntos)
                            {
                                $total_procedimientos_especialidad++;
                                //>> Orden a tener en cuenta para grabar los cargos originales al momento de liquidar.
                                if(!array_key_exists($procedimiento_liquidar_cod_dif, $arr_procedimientos_orden_liquidar))
                                {
                                    $arr_procedimientos_orden_liquidar[$procedimiento_liquidar_cod_dif] = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif];
                                }
                                //<<
                                $arr_datos_procedimiento = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif];
                                // $guardar = "arr_datos_procedimiento: ".print_r($arr_datos_procedimiento,true).PHP_EOL;
                                // seguimiento($guardar);

                                // Este control fue creado para detectar si se debe aplicar manual de Cirugías múltiples con porcentajes bilaterales
                                // solo si la cirugía mayor es bilateral y no hay mas de una especialidad, si hay más de una especialidad entonces se cobran porcentajes
                                // de diferente via diferente especialidad.
                                if(empty($primera_cirugia_enLista) && count($arr_especialidades_liq) <= 1)
                                {
                                    $primera_cirugia_enLista = $procedimiento_liquidar_cod_dif;
                                    if($arr_datos_procedimiento["wesbilateral"] == 'on')
                                    {
                                        $control_cx_mayor_esBilateral = true;
                                    }
                                }

                                // Si aplican condiciones para cirugías múltiples. BUSCA SI TIENE QUE APLICAR PORCENTAJES PARA CIRUGÍAS MÚLTIPLES.
                                $arr_porcentajes = array();
                                if(count($arr_porcentajes_multiples) > 0)
                                {
                                    // Averigua si el procedimiento actual es bilateral o no, si lo es entonces agrega el procedimiento al array de procedimientos bilaterales
                                    // sino entonces lo agrega al array de procedimientos NO bilaterales.

                                    // Para asignar los porcentajes según el número de la cirugía (en el orden de prioridad que debe ser), entonces se cuentan los elementos de cada
                                    // array y el valor que arroja será el número de la cirugía (1era cirugía, 2da cirugía).

                                    // Si en el array de porcentajes múltiples no se encuentra el número de la cirugía entonces se busca el '*' y se asignan los porcentajes que éste
                                    // tenga configurados.

                                    $wesbilateral                  = $arr_datos_procedimiento['wesbilateral'];
                                    $via_cirugia_en_curso          = $arr_datos_procedimiento['wvia'];
                                    $especialidad_cirugia_en_curso = $arr_datos_procedimiento['wespecialidad'];
                                    if(!$esMultipleEspecialidadDifVia)
                                    {
                                        // Si no es diferente especialidad, entonces reiniciar el conteo de cirugías y basarse en en la cantidad de cirugias guardadas en el array
                                        // "arr_procedimientos_bilaterales" que se encarga de guardar cada diferente cirugía y para saber cuantas cirugías van simplemente se hace
                                        // un count, con eso se sabe el número de cirugía, este array es útil porque cuando se está liquidando una cirugía bilateral
                                        // se encarga de controlar que se cobren dos cirugías de bilateral,
                                        $numero_cirugia = 0;
                                    }

                                    // Si es un procedimiento bilateral, suma al contador de bilaterales. SOLO SI LA CIRUGÍA MAYOR ES LA BILATERAL "control_cx_mayor_esBilateral=true"
                                    if($control_cx_mayor_esBilateral && $wesbilateral == 'on')
                                    {
                                        $cont_CX_bilaterales++;
                                        if(empty($via_primera_bilateral))
                                        {
                                            $via_primera_bilateral = $arr_datos_procedimiento['wvia'];
                                        }

                                        if(empty($especialid_primera_bilateral))
                                        {
                                            $especialid_primera_bilateral = $wcodigo_especialidad_orden;
                                        }
                                    }

                                    // Si el contador de bilaterales es mayor a 1 entonces cambie a los porcentajes de cirugías bilaterales seteando a 'on' la variable porcentajes_CX_bilaterales
                                    if($cont_CX_bilaterales > 1)
                                    {
                                        $porcentajes_CX_bilaterales = 'on';
                                        if($total_procedimientos_especialidad > $cont_CX_bilaterales)
                                        {
                                            // $numero_cirugia = 1;
                                            // Esta justificación ya no tiene validéz porque si hay una cirugía mayor a la bilateral, se pierde la bilateralidad, esto fué afirmado
                                            // en una de las reuniones de facturación y para el desarrollo se tomó como regla "Si hay diferente especialidad se pierde la bilateralidad pero se conservan
                                            // las dos cirugías resultantes y se aplica porcentajes de diferente vía diferente especialidad"
                                                    // Si hay cirugías no bilateralares ántes de llegar a las bilaterales
                                                    // implicaría un cambio de acto quirúrgico a bilaterales pero empezando en la cirugía número 2, no es la primera porque
                                                    // esa ya se debió haber tenido en cuenta en los porcentajes del acto quirúrgico anterior.
                                            // $arr_procedimientos_bilaterales = array(); // Reiniciar el array de conteo de procedimientos para que empiece desde cero
                                            // esto sucede cuando se deben cambiar los porcentajes a bilaterales, es como si se cambiara al acto quirúrgico bilateral
                                            // se guarda el primer procedimientos bilateral y $numero_cirugia valdrá por ejemplo 2, debído a que se inicializó en 1
                                            // esto ayuda que al momento de empezar a tomar los porcentajes de bilaterales, tome a partir de la segunda cirugía
                                            // porque la primera es posible que ya se haya liquidado con el porcentaje de otro evento quirúrgico si es que antes
                                            // había procedimiento mayor.
                                        }
                                    }

                                    if(!$esMultipleEspecialidadDifVia && !array_key_exists($procedimiento_liquidar_cod_dif, $arr_procedimientos_bilaterales))
                                    {
                                        $arr_procedimientos_bilaterales[$procedimiento_liquidar_cod_dif] = $procedimiento_liquidar_cod_dif;
                                        $numero_cirugia = count($arr_procedimientos_bilaterales)+$numero_cirugia;
                                    }
                                    elseif($esMultipleEspecialidadDifVia)
                                    {
                                        // Si es cirugias con diferente especialidad, simplemente contar las cirugias como si no existieran diferentes especialidadades.
                                        $numero_cirugia++;
                                    }

                                    global $arr_especialidades_liq, $wesbilateral, $numero_cirugia, $porcentajes_CX_bilaterales, $via_primera_bilateral, $via_cirugia_en_curso, $especialid_primera_bilateral, $especialidad_cirugia_en_curso;
                                    $arr_porcentajes = arrayPorcentajePorNumeroCirugia($arr_porcentajes_multiples);

                                    // Si hay más de una especialidad, verificar si hay conceptos que solo se deben cobrar una vez o
                                    // solo hasta una determinada cantidad de cirugías (CIRUGIAS MAYORES SIN IMPORTAR LA ESPECIALIDAD)
                                    if(count($arr_especialidades_liq) > 1)
                                    {
                                        // $arr_ordenProcedimientosTodos_NoEspecialidad
                                        foreach ($arr_porcentajes as $concepto_porcentaje => $arr_info_porcentaje)
                                        {
                                            $valor_porcentaje = 0;
                                            if($arr_info_porcentaje["hasta_la_mayor_marcada"] == 'on' && !array_key_exists($concepto_porcentaje, $arr_conceptos_maximo_aCobrar))
                                            {
                                                // Agregar el concepto y el número de cirugía a este array que tienen el valor "hasta_la_mayor_marcada=on"
                                                // Debe garantizar que ese concepto solo se debe cobrar hasta la cirugía mayor marcada, es decir, si ese concepto en el manual de cirugias multiples
                                                // esta marcado que solo debe cobrar hasta la cirugia 2, significa que ese concepto solo se va a cobrar hasta la segunda cirugía mayor sin importar especialidad,
                                                // para el resto de cirugías en ese concepto debe seguir cobrando 0%.
                                                $arr_conceptos_maximo_aCobrar[$concepto_porcentaje] = $numero_cirugia;// Es el número de cirugía hasta el que se debe cobrar el concepto.
                                                $valor_porcentaje = $arr_info_porcentaje['valor_porcentaje'];
                                            }

                                            $indice_proced_concepNoCobro = $procedimiento_liquidar_cod_dif."_".$concepto_porcentaje;

                                            // Este condicional-función se encarga de verificar los conceptos que no se deben cobrar si alguno de ellos esta marcado
                                            // en el manual de cirugías indicando que para ese concepto solo se debe cobrar hasta X cirugía unicamente cobrando
                                            // la mayor en ese nivel de cirugía. (p.e. Anestesia solo se cobra a la segunda cirugía mayor sin importar la especialidad.)
                                            if(array_key_exists($concepto_porcentaje, $arr_conceptos_maximo_aCobrar))
                                            {
                                                $cx_queNoSeCobran = validarCirugiaMayorACobrarConcepto($concepto_porcentaje, $cx_queNoSeCobran, $arr_conceptos_maximo_aCobrar, $arr_procedimientos_orden, $indice_proced_concepNoCobro, $cx_queSISeCobran, $valor_porcentaje);
                                            }

                                            if(array_key_exists($indice_proced_concepNoCobro, $cx_queNoSeCobran))
                                            {
                                                $arr_porcentajes[$concepto_porcentaje]["valor_porcentaje"] = 0;
                                            }
                                            elseif(array_key_exists($indice_proced_concepNoCobro, $cx_queSISeCobran))
                                            {
                                                $arr_porcentajes[$concepto_porcentaje]["valor_porcentaje"] = $cx_queSISeCobran[$indice_proced_concepNoCobro];
                                            }

                                            // $guardar = "procedimiento_liquidar_cod_dif: ".$procedimiento_liquidar_cod_dif.PHP_EOL;
                                            // $guardar .= "".@$numero_cirugia." > (".@$arr_conceptos_maximo_aCobrar[$concepto_porcentaje]." * 1))".PHP_EOL;
                                            // $guardar .= "arr_porcentajes[concepto_porcentaje][valor_porcentaje] : $concepto_porcentaje => ".print_r(@$arr_porcentajes[$concepto_porcentaje]["valor_porcentaje"],true).PHP_EOL;
                                            // $guardar .= "arr_ordenProcedimientosTodos_NoEspecialidad: $concepto_porcentaje => ".print_r(@$numero_cirugia,true).PHP_EOL;
                                            // $guardar .= "arr_conceptos_maximo_aCobrar: $concepto_porcentaje => ".print_r(@$arr_conceptos_maximo_aCobrar[$concepto_porcentaje],true).PHP_EOL;
                                            // $guardar .= "*****************************: ".print_r('******************',true).PHP_EOL;
                                            // seguimiento($guardar);
                                        }
                                    }

                                    // Repetir el primer porcentaje de honorarios por ejemplo. pero solo para dif. via dif. espe. solo para la primer cirugía de cada especialista.
                                    if($esMultipleEspecialidadDifVia && array_key_exists($conceptoRepetirPorcentajeMultiple, $arr_porcentajes) && !array_key_exists($wcodigo_especialidad_orden, $arrRepetiblePorEspecialidad))
                                    {
                                        $arrRepetiblePorEspecialidad[$wcodigo_especialidad_orden] = array();
                                        if(empty($porcentajeRepetible))
                                        {
                                            // Es el porcentaje a repetir, solo se captura la primer vez que se encuentre y se repite ese valor para el mismo concepto en los procedimientos siquientes
                                            $porcentajeRepetible = $arr_porcentajes[$conceptoRepetirPorcentajeMultiple]['valor_porcentaje'];
                                        }
                                    }

                                    // $arr_porcentajes = Array
                                    //                     (
                                    //                         [0024] => array(valor_porcentaje => 75, hasta_la_mayor_marcada => 'off'),
                                    //                         [0626] => array(valor_porcentaje => 75, hasta_la_mayor_marcada => 'off'),
                                    //                         [0076] => array(valor_porcentaje => 75, hasta_la_mayor_marcada => 'off'),
                                    //                         [0075] => array(valor_porcentaje => 75, hasta_la_mayor_marcada => 'off'),
                                    //                         [0072] => array(valor_porcentaje => 75, hasta_la_mayor_marcada => 'off')
                                    //                     )
                                }
                                // $guardar = "arr_porcentajes[concepto_porcentaje][valor_porcentaje] : $concepto_porcentaje => ".print_r($arr_porcentajes,true).PHP_EOL;
                                // seguimiento($guardar);

                                $codigo_procedimiento = $arr_datos_procedimiento['codigo'];
                                $arr_especialistas_proced = $arr_datos_procedimiento["especialistas"];

                                // Validar si por manual se deben poner como no facturable todos los conceptos (excepto insumos) para el procedimiento actual.
                                $procedimiento_No_Facturable = "";
                                $procedimiento_No_Facturable = validarProcedimientoNoFacturable($arr_procedimientos_relacionados, $arr_proced_Manual_NoFact, $codigo_procedimiento);


                                $responsable_actual_temp = $RESPONSABLE_VIRTUAL_PACIENTE["Ingcem"];
                                foreach ($arr_lista_conceptos as $codigo_concepto => $nombre_concepto)
                                {
                                    // VALIDAR SI CAMBIA EL RESPONSABLE Y SI ES ASÍ VERIFICAR SI EL CONCEPTO ACTUAL CORRESPONDE A MATERIALES-MEDICAMENTOS QUE NO MUEVEN INVENTARIOS
                                    // SI ES ASI Y PARA EL NUEVO RESPONSABLE NO SE DEBE COBRAR ENTONCES NO CONTINUAR EL CARGO SIGUIENTE SI ES QUE PARA ESE RESPONSABLE NO SE DEBE COBRAR
                                    // POR TANTO LOS INSUMOS SERÁN FACTURABLES.

                                    // Si hubo cambio de responsable y esto hizo se cambiaran las condiciones de limite de UVR o Grupo respecto al manual de Cx Multiples del nuevo
                                    // responsable, entonces para todos los demas procedimientos que le correspondan al nuevo responsable y tenga el concepto de no mueve inventario
                                    // entonces dirá para todos esos otros procedimientos si ese concepto queda como facturable o no mintras se mantenga ese mismo responsable.
                                    // Aplica por ejemplo si primero esta soat se cobran medicamentos como no facturable mas un concepto adicional, pero para el caso de la prepagada
                                    // si se le acabó el tope a soat entonces la prepagada ya no tiene que pagar un concepto de materiales y medicamentos sino que debe pagar
                                    // los insumos por consumo y así seguir para los demas procedimientos que tengan insumos y sea la prepagada el mismo responsable.
                                    $facturable_init = ($concepto_no_facturar != $codigo_concepto) ? "S": $no_facturable_porcambio_manual;;

                                    $limite_cobro_ayudantia = 0;
                                    if($responsable_actual_temp != $RESPONSABLE_VIRTUAL_PACIENTE["Ingcem"] )
                                    {
                                        //Hay cambio de responsable
                                        if(empty($cod_cambio_responsable))
                                        {
                                            $cod_cambio_responsable = $RESPONSABLE_VIRTUAL_PACIENTE["Ingcem"];
                                            $primer_cantidad_puntos_cambio_responsable = $num_puntos; // esta variable aplica para saber si los materiales y medicamentos para el segundo responsable se facturan o no.

                                        }
                                        $concepto_insumos_cobro = '';
                                        if(array_key_exists($procedimiento_liquidar_cod_dif, $arr_insumos_procedimiento) && array_key_exists($codigo_concepto, $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif]))
                                        {
                                            $row_insumos_control_responsable       = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["lista_insumos"];
                                            $facturable_insumo_control_responsable = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["insumos_facturables"];
                                            $insumo_medicamentos_facturables       = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["medicamentos_facturables"];
                                            $insumo_materiales_facturables         = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["materiales_facturables"];
                                            $concepto_insumos_cobro                = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["limite_concepto_cobro_insumos"];
                                        }

                                        // Si llegó a concepto de medicamentos no facturables, verifique si para el responsable actual se debe cobrar o no y cambiar los insumos a facturable o no
                                        if($codigo_concepto == $concepto_insumos_cobro)
                                        {
                                            $concepto_no_facturar = $codigo_concepto;
                                            $tipoEmpresa_nueva  = $RESPONSABLE_VIRTUAL_PACIENTE["tipoEmpre"];
                                            $wcod_empresa_nueva = $RESPONSABLE_VIRTUAL_PACIENTE["Ingcem"];
                                            $row_limite_new = buscarLimitesEnManual($conex, $wbasedato, $tipoEmpresa_nueva, $wcod_empresa_nueva, $wpolitraumatizado, $TB_BASE_LIQUIDACION);
                                            $modalidad_facturacion_nueva = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["configuracion_liquidar"][$codigo_concepto]["procedimiento_tarifa"];
                                            if(count($row_limite_new) > 0)
                                            {
                                                $limite_puntos_cobro_new   = 0;
                                                // $limite_concepto_cobro_medicamentos_new = 0;
                                                // $row_limite = mysql_fetch_array($result_limite);
                                                if($modalidad_facturacion_nueva == 'UVR')
                                                {
                                                    $limite_puntos_cobro_new    = $row_limite_new["limite_med_uvr"]*1;
                                                    // $limite_concepto_cobro_medicamentos_new = $row_limite_new["cobro_limite_med_uvr"];

                                                    if($arr_cod_material_medicamento["material"] == $codigo_concepto)
                                                    {
                                                        $limite_puntos_cobro_new    = $row_limite_new["limite_mat_uvr"]*1;
                                                    }
                                                    // $limite_concepto_cobro_material_new = $row_limite_new["cobro_limite_mat_uvr"];
                                                }
                                                elseif($modalidad_facturacion_nueva == 'GQX')
                                                {
                                                    $limite_puntos_cobro_new    = $row_limite_new["limite_med_grupo"]*1;
                                                    // $limite_concepto_cobro_medicamentos_new = $row_limite_new["cobro_limite_med_grupo"];

                                                    if($arr_cod_material_medicamento["material"] == $codigo_concepto)
                                                    {
                                                        $limite_puntos_cobro_new = $row_limite_new["limite_mat_grupo"];
                                                    }
                                                    // $limite_puntos_cobro_material_new    = $row_limite_new["cobro_limite_med_grupo"]*1;
                                                }

                                                if($primer_cantidad_puntos_cambio_responsable <= $limite_puntos_cobro_new)
                                                {
                                                    // $no_facturable_porcambio_manual = "N";
                                                    // $facturable_init = $no_facturable_porcambio_manual;
                                                    $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["insumos_facturables"]      = "N";
                                                    $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["medicamentos_facturables"] = "N";
                                                    $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["materiales_facturables"]   = "N";
                                                    $no_facturable_porcambio_manual = "S";
                                                    $facturable_init = $no_facturable_porcambio_manual;
                                                }
                                                else
                                                {
                                                    $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["insumos_facturables"]      = "S";
                                                    $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["medicamentos_facturables"] = "S";
                                                    $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$codigo_concepto]["parametros"]["materiales_facturables"]   = "S";
                                                    $no_facturable_porcambio_manual = "N";
                                                    $facturable_init = $no_facturable_porcambio_manual;
                                                }
                                            }
                                        }
                                    }

                                    $limite_ayudantia_uvr   = $arr_baseLiquidacion['limite_ayudantia_uvr'];
                                    $limite_ayudantia_grupo = $arr_baseLiquidacion['limite_ayudantia_grupo'];

                                    if(!$es_paquete && $codigo_concepto == $concepto_honorario_ayudantia
                                        && array_key_exists($codigo_concepto, $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["configuracion_liquidar"]))
                                    {
                                        $mod_facturacion = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["configuracion_liquidar"][$codigo_concepto]["procedimiento_tarifa"];
                                        if($mod_facturacion == 'UVR')
                                        {
                                            $limite_cobro_ayudantia = $limite_ayudantia_uvr*1;
                                        }
                                        elseif($mod_facturacion == 'GQX')
                                        {
                                            $limite_cobro_ayudantia = $limite_ayudantia_grupo*1;
                                        }
                                    }

                                    // Hay procedimientos que no se deben facturar si estan acompañados de otros, entonces sus conceptos serán no facturables
                                    // si la variable "procedimiento_No_Facturable" esta seteada (tendrá el valor "N") significa que el procedimiento no se debe cobrar
                                    if( !empty($procedimiento_No_Facturable)
                                        && $arr_cod_material_medicamento["material"] != $codigo_concepto
                                        && $arr_cod_material_medicamento["medicamento"] != $codigo_concepto
                                        )
                                    {
                                        $facturable_init = $procedimiento_No_Facturable;
                                    }

                                    // Aqui se define si el concepto HONORARIOS AYUDANTÍA SE DEBE COBRAR O NO, esto según un parámetro en el manual de cirugía donde se lee el límite de cobro para ayudandtía
                                    if($codigo_concepto == $concepto_honorario_ayudantia && $num_puntos < $limite_cobro_ayudantia)
                                    {
                                        $facturable_init = "N";
                                    }

                                    $responsable_actual_temp = $RESPONSABLE_VIRTUAL_PACIENTE["Ingcem"];

                                    // foreach     ($arr_datos_procedimiento['configuracion_liquidar'] as $codigo_concepto => $arr_info_conf)
                                    if(array_key_exists($codigo_concepto, $arr_datos_procedimiento['configuracion_liquidar']))
                                    {
                                        $arr_equipos_examenes_liquidar = array();

                                        if($codigo_concepto == $id_concepto_uso_equipos
                                            && array_key_exists($codigo_concepto, $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]['configuracion_liquidar'])
                                            && array_key_exists("lista_equipos", $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]['configuracion_liquidar'][$codigo_concepto]))
                                        {
                                            $arr_equipos_examenes_liquidar = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]['configuracion_liquidar'][$codigo_concepto]["lista_equipos"];
                                        }
                                        elseif($codigo_concepto == $id_concepto_examenes
                                            && array_key_exists($codigo_concepto, $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]['configuracion_liquidar'])
                                            && array_key_exists("lista_examenes", $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]['configuracion_liquidar'][$codigo_concepto]))
                                        {
                                            $arr_equipos_examenes_liquidar = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]['configuracion_liquidar'][$codigo_concepto]["lista_examenes"];
                                        }
                                        else
                                        {
                                            $arr_equipos_examenes_liquidar = array(""=>array("nombre"=>"","codigo"=>""));
                                        }

                                        foreach ($arr_equipos_examenes_liquidar as $codigo_equipo_examen => $arr_info_equipo_examen)
                                        {
                                            $arr_info_conf = $arr_datos_procedimiento['configuracion_liquidar'][$codigo_concepto];
                                            $concepto_cod_ppal = $codigo_concepto;
                                            $especialidad_procedimiento = $arr_datos_procedimiento['wespecialidad'];

                                            if(!array_key_exists($procedimiento_liquidar_cod_dif, $arr_CargosGrabadosResponsables))
                                            {
                                                $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif] = array();
                                            }

                                            if(!array_key_exists($concepto_cod_ppal, $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif]))
                                            {
                                                $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal] = array();
                                            }

                                            if(!array_key_exists($concepto_cod_ppal, $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif]))
                                            {
                                                $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal] = array();
                                            }

                                            // $requiere_tercero     = $arr_info_conf['requiere_tercero']; // Si es "C" o "P" entonces requiere tercero
                                            // $cantidad_pintar      = $arr_info_conf['cantidad_concepto'];
                                            $tabla_valida_precios = $arr_info_conf['tabla_valida_precios'];
                                            $modifica_valor       = $arr_info_conf['modifica_valor'];
                                            $mueve_inventario     = $arr_info_conf['mueve_inventario'];
                                            $tipo_cobro           = $arr_info_conf['tipo_cobro'];
                                            $es_concepto_extra    = (array_key_exists('es_concepto_extra', $arr_info_conf)) ? true: false;
                                            $cobro_concepto       = "";
                                            $cobro_procedimiento  = "";
                                            switch ($tipo_cobro) {
                                                case 'cobro_hora':
                                                    $cobro_concepto = $arr_info_conf['cobro_concepto'];
                                                    $cobro_procedimiento = $arr_info_conf['cobro_procedimiento'];
                                                    break;
                                                case 'cobro_anestesia':

                                                        // array(  "tipo_anestesia"      => $row['tipo_anestesia'],
                                                        //         "tiempo_inicio"       => $row['tiempo_inicio'],
                                                        //         "tiempo_final"        => $row['tiempo_final'],
                                                        //         "cobro_concepto"      => $row['cobro_concepto'],
                                                        //         "cobro_procedimiento" => $row['cobro_procedimiento']);

                                                    // $wtipo_anestesia_cx = $arr_datos_procedimiento['wtipo_anestesia_cx'];
                                                    $valor_rango_buscar = $wtiempo_sala_recuperarcion; //$arr_datos_liquidar["arr_datos_paciente"]["wtiempo_sala_recuperarcion"];
                                                    foreach ($arr_info_conf['rangos'] as $key => $arr_rango)
                                                    {
                                                        if($wtipo_anestesia_cx == $arr_rango['tipo_anestesia'] && $valor_rango_buscar >= $arr_rango['tiempo_inicio'] && $valor_rango_buscar <= $arr_rango['tiempo_final'])
                                                        {
                                                            $cobro_concepto = $arr_rango['cobro_concepto'];
                                                            $cobro_procedimiento = $arr_rango['cobro_procedimiento'];
                                                        }
                                                    }
                                                    break;
                                                case 'cobro_uso':

                                                        // array(  "tiempo_inicio"       => $row['tiempo_inicio'],
                                                        //         "tiempo_final"        => $row['tiempo_final'],
                                                        //         "cobro_concepto"      => $row['cobro_concepto'],
                                                        //         "cobro_procedimiento" => $row['cobro_procedimiento']);

                                                    $valor_rango_buscar = $wtiempo_uso_minutos; //$arr_datos_procedimiento['wtiempo_uso_minutos'];
                                                    foreach ($arr_info_conf['rangos'] as $key => $arr_rango)
                                                    {
                                                        if($valor_rango_buscar >= $arr_rango['tiempo_inicio'] && $valor_rango_buscar <= $arr_rango['tiempo_final'])
                                                        {
                                                            $cobro_concepto = $arr_rango['cobro_concepto'];
                                                            $cobro_procedimiento = $arr_rango['cobro_procedimiento'];
                                                        }
                                                    }
                                                    break;

                                                default:
                                                    if($es_concepto_extra)
                                                    {
                                                        $cobro_concepto      = $arr_info_conf['cobro_concepto'];
                                                        $cobro_procedimiento = $arr_info_conf['cobro_procedimiento'];
                                                    }
                                                    break;
                                            }

                                            if($cobro_procedimiento == '*')
                                            {
                                                $cobro_procedimiento = arreglar_procedimientos_bilaterales($procedimiento_liquidar_cod_dif);
                                            }

                                            // Inicializa el array para controlar que cuando sean CX multiples solo se cobre tiempos a la primer cirugía
                                            if($cx_multiples_requiere_tiempos == 'on' && count($arr_control_primera_cx_tiempos) == 0)
                                            {
                                                if(!array_key_exists($procedimiento_liquidar_cod_dif, $arr_control_primera_cx_tiempos))
                                                {
                                                    $arr_control_primera_cx_tiempos[$procedimiento_liquidar_cod_dif] = array();
                                                }
                                            }

                                            // Si se van a controlar tiempos para la primer cirugía entonces adicione, concepto a concepto, al array.
                                            if(array_key_exists($procedimiento_liquidar_cod_dif, $arr_control_primera_cx_tiempos))
                                            {
                                                if(!array_key_exists($codigo_concepto, $arr_control_primera_cx_tiempos[$procedimiento_liquidar_cod_dif]))
                                                {
                                                    $arr_control_primera_cx_tiempos[$procedimiento_liquidar_cod_dif][$codigo_concepto] = $codigo_concepto;
                                                }
                                            }

                                            if($mueve_inventario != 'on')
                                            {
                                                $cont_pint = 0;

                                                // $arr_valor_cobro = Array(
                                                //         [wexiste] => 0
                                                //         [wvaltar] => 7060.00
                                                //         [wexidev] =>
                                                //         [error] =>
                                                //         [aprovechamiento] => off
                                                //         [wtipfac] => UVR)
                                                $cobro_procedimiento = (!empty($codigo_equipo_examen)) ? $codigo_equipo_examen: $cobro_procedimiento;
                                                $arr_valor_cobro = array();
                                                if(!$es_paquete)
                                                {
                                                    // MODIFICACIONES PARA BUSCAR TARIFA POR TERCERO (GRUPO DE MEDICOS)
                                                    // * TENER EN CUENTA EL TERCERO POR DEFECTO PARA ENVIARLO DESDE LA PRIMER VEZ QUE SE LIQUIDE EL ACTO QUIRÚRGICO.
                                                    // * TENER EN CUENTA EL TERCERO QUE SE INGRESÓ EN LA INTERFAZ PARA ENVIARLO A LA FUNCIÓN QUE BUSCA LA TARÍFA AL MOMENTO DE RELIQUIDAR.
                                                    $codigo_wtercero_tarifa = '';
                                                    if($cobro_concepto != $id_concepto_uso_equipos && $cobro_concepto != $id_concepto_examenes)
                                                    {
                                                        if(array_key_exists($procedimiento_liquidar_cod_dif, $arr_datos_liquidados))
                                                        {
                                                            if(array_key_exists($cobro_concepto, $arr_datos_liquidados[$procedimiento_liquidar_cod_dif]))
                                                            {
                                                                $codigo_wtercero_tarifa = $arr_datos_liquidados[$procedimiento_liquidar_cod_dif][$cobro_concepto]['wtercero'];
                                                            }
                                                        }

                                                        // Si no hay información de tercero entonces intenta buscar un tercero por defecto para este concepto y con ese código buscar una tarifa.
                                                        if(empty($codigo_wtercero_tarifa))
                                                        {
                                                            $userCargo = explode('-',$_SESSION['user']);
                                                            $wuse      = $userCargo[1];
                                                            global $wbasedato, $wemp_pmla, $conex, $wuse;
                                                            $arr_tercero_xdefecto = traer_terceros_por_defecto($cobro_concepto);
                                                            $codigo_wtercero_tarifa = $arr_tercero_xdefecto['codigo'];
                                                        }
                                                    }

                                                    $arr_valor_cobro = datos_desde_procedimiento($cobro_procedimiento, $cobro_concepto, $wcentro_costo, $wcentro_costo, $wcod_empresa, $wfecha_cargo, $wtipo_ingreso, $especialidad_procedimiento, 'on', false, $codigo_wtercero_tarifa, $wfecha_cargo, $whora_cargo);

                                                    // Si el concepto permite modificar o escribir un valor y si no se ha encontrado una tarífa definida para el concepto
                                                    // entonces puede retomar el valor que antes puedan haber digitado, si no hay valor digitado entonces el valor será vacío
                                                    if(($modifica_valor == 'S' && $arr_valor_cobro['error'] == '1') || ($modifica_valor == 'S' && array_key_exists($RESPONSABLE_VIRTUAL_PACIENTE["Ingtar"], $arr_tarifas_modifican_valor_erp)))
                                                    {
                                                        $wtipfac = "_INDETERMINADO_";
                                                        if(array_key_exists($codigo_concepto, $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["configuracion_liquidar"]))
                                                        {
                                                            $wtipfac = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["configuracion_liquidar"][$codigo_concepto]["procedimiento_tarifa"];
                                                        }

                                                        $wvaltar = '';
                                                        if(array_key_exists($procedimiento_liquidar_cod_dif, $arr_datos_liquidados)
                                                            && array_key_exists($cobro_concepto, $arr_datos_liquidados[$procedimiento_liquidar_cod_dif]))
                                                        {
                                                            if(array_key_exists("wvalor_digitado", $arr_datos_liquidados[$procedimiento_liquidar_cod_dif][$cobro_concepto]))
                                                            {
                                                                $wvaltar = $arr_datos_liquidados[$procedimiento_liquidar_cod_dif][$cobro_concepto]['wvalor_digitado'];
                                                            }
                                                            else
                                                            {
                                                                $wvaltar = $arr_datos_liquidados[$procedimiento_liquidar_cod_dif][$cobro_concepto]['wvalor_final'];
                                                            }
                                                        }
                                                        $arr_valor_cobro['wvaltar']      = $wvaltar;
                                                        $arr_valor_cobro['wexiste']      = 0;
                                                        $arr_valor_cobro['wtipfac']      = $wtipfac;
                                                        $arr_valor_cobro['error']        = 0;
                                                        $arr_valor_cobro['error_tarifa'] = 1;
                                                        if($modifica_valor == 'S' && array_key_exists($RESPONSABLE_VIRTUAL_PACIENTE["Ingtar"], $arr_tarifas_modifican_valor_erp))
                                                        {
                                                            $arr_valor_cobro['error_tarifa'] = 0;
                                                        }
                                                    }

                                                    if($modifica_valor != 'S' && $arr_valor_cobro['error'] == '1')
                                                    {
                                                        // Identificar los cargos que no tienen tarifa para mostrarlos en pantalla y no continuar con la liquidación hasta crear las tarifas
                                                        // faltantes en el maestro de tarifas.
                                                        if(!array_key_exists($procedimiento_liquidar_cod_dif, $cargosSinTarifas))
                                                        {
                                                            $explode_dif_proced = explode("_", $procedimiento_liquidar_cod_dif);
                                                            $procedimiento_sin_posicion_org = $procedimiento_liquidar_cod_dif;

                                                            $cargosSinTarifas[$procedimiento_liquidar_cod_dif] = array("info_procedimiento"=>array(),"lista_cargos"=>array());
                                                            $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["info_procedimiento"]["procedimiento_liquidado_cod"] = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["codigo"];
                                                            $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["info_procedimiento"]["procedimiento_liquidado_nom"] = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["nombre"];
                                                            $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["info_procedimiento"]["modalidad_facturacion"]       = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["modalidad_facturacion"];
                                                        }
                                                        if(!array_key_exists($concepto_cod_ppal, $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"]))
                                                        {
                                                            $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal] = array();
                                                        }
                                                        //datos_desde_procedimiento($cobro_procedimiento, $cobro_concepto, $wcentro_costo, $wcentro_costo, $wcod_empresa, $wfecha_cargo, $wtipo_ingreso, $especialidad_procedimiento, 'on', false, $codigo_wtercero_tarifa, $wfecha_cargo, $whora_cargo);

                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["cobro_procedimiento"]        = $cobro_procedimiento;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["cobro_concepto_cod"]         = $cobro_concepto;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["cobro_concepto_nom"]         = $arr_conceptos_nombres[$cobro_concepto];
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["wcentro_costo"]              = $wcentro_costo;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["wcod_empresa"]               = $wcod_empresa;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["wfecha_cargo"]               = $wfecha_cargo;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["whora_cargo"]                = $whora_cargo;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["wtipo_ingreso"]              = $wtipo_ingreso;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["wtipo_ingreso_nom"]          = $wtipo_ingreso_nom;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["especialidad_procedimiento"] = $especialidad_procedimiento;
                                                        $cargosSinTarifas[$procedimiento_liquidar_cod_dif]["lista_cargos"][$concepto_cod_ppal]["codigo_wtercero_tarifa"]     = $codigo_wtercero_tarifa;
                                                    }
                                                }
                                                else
                                                {
                                                    $arr_valor_cobro['wvaltar'] = $arr_info_conf["wvaltar"];
                                                    $arr_valor_cobro['wexiste'] = 0;
                                                    $arr_valor_cobro['wtipfac'] = TIPO_FACTURACION_PAQUETE;
                                                    $arr_valor_cobro['error']   = 0;
                                                }

                                                if(!array_key_exists("error_tarifa", $arr_valor_cobro))
                                                {
                                                    $arr_valor_cobro['error_tarifa'] = 0;
                                                }

                                                // $arr_valor_cobro['wvaltar'] = 0 si solo se debe cobrar para el primer procedimiento, para el resto no se debe cobrar.
                                                // Si se requieren tiempos de uso, recuperación, cirugía, entonces verifique si no es la primer cirugía entonces cobre valor cero (0).
                                                if(!$es_paquete && $cx_multiples_requiere_tiempos == 'on'
                                                    && !array_key_exists($procedimiento_liquidar_cod_dif, $arr_control_primera_cx_tiempos)
                                                    //&& !array_key_exists($codigo_concepto, $arr_control_primera_cx_tiempos[$procedimiento_liquidar_cod_dif])
                                                   )
                                                {
                                                    $arr_valor_cobro['wvaltar'] = 0; // Si es multiple cirugía solo se cobra el valor de la primer cirugía, el resto no se cobra.
                                                }

                                                // Verifica si aplica un porcentaje diferente para el concepto por efectos de cirugías múltiples.
                                                $porcentaje_cxMult = porcentajeCirugiaMultiple($concepto_cod_ppal, $arr_porcentajes);
                                                if($esMultipleEspecialidadDifVia
                                                    && $concepto_cod_ppal == $conceptoRepetirPorcentajeMultiple
                                                    && array_key_exists($wcodigo_especialidad_orden, $arrRepetiblePorEspecialidad)
                                                    && !array_key_exists($concepto_cod_ppal, $arrRepetiblePorEspecialidad[$wcodigo_especialidad_orden]))
                                                {
                                                    //Si es multiple cirugía diferente especialidad y diferente via y es concepto y porcentaje repetible entonces
                                                    //modifique el porcentaje repetible por el porcentaje inicial
                                                    $porcentaje_cxMult = $porcentajeRepetible;

                                                    // Solo se puede repetir ese porcentaje una vez por la primer vez que se encuentre ese concepto para cada especialidad.
                                                    $arrRepetiblePorEspecialidad[$wcodigo_especialidad_orden][$concepto_cod_ppal] = $concepto_cod_ppal;
                                                }
                                                $concepto_control_porcentaje_cx_multiple = $concepto_cod_ppal; // Se usará en validacionTopesyParalelosERP

                                                $valor_tarifa  = ($arr_valor_cobro['wvaltar'])*1;
                                                $valor_final = ($porcentaje_cxMult < 100) ? (($porcentaje_cxMult * $valor_tarifa)/100) : $valor_tarifa;
                                                $valor_final = ($porcentaje_cxMult == 0) ? 0 : $valor_final; // Este valor se guarda en el array de liquidación listo para enviar a la función de GrabarCargos, se asigna a una variable de simulación.
                                                global $procedimiento_liquidar_cod_dif, $porcentaje_cxMult, $concepto_control_porcentaje_cx_multiple; // se debió poner en global porque en las funciones de simulación se pueden crear nuevos cargos en los que se debe aplicar porcentaje de descuento en su tarifa.

                                                $valor_final_simulado = $valor_final;

                                                // $guardar = "concepto_cod_ppal: ".print_r($concepto_cod_ppal,true).PHP_EOL;
                                                // $guardar = print_r($porcentaje_cxMult,true).PHP_EOL;
                                                // $guardar = print_r($RESPONSABLE_VIRTUAL_PACIENTE,true).PHP_EOL;
                                                // seguimiento($guardar);

                                                $tipo_facturacion = '';
                                                // INFORMA, DICE QUE SI TIENE O NO TARIFA
                                                $wexiste          = $arr_valor_cobro['wexiste'];

                                                $datosGrabarCargos = array();
                                                $info_tarifa = '';

                                                if(!array_key_exists($procedimiento_liquidar_cod_dif, $arr_CargosGrabadosResponsables))
                                                {
                                                    $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif] = array();
                                                }
                                                if(!array_key_exists($concepto_cod_ppal, $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif]))
                                                {
                                                    $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal] = array();
                                                }

                                                $CARGO_procedimiento = $procedimiento_liquidar_cod_dif; // !
                                                $CARGO_wprocod       = $procedimiento_liquidar_cod_dif; // !
                                                $CARGO_wpronom       = $arr_datos_procedimiento['nombre']; // !

                                                if(!empty($codigo_equipo_examen))
                                                {
                                                    $CARGO_procedimiento = $arr_info_equipo_examen["codigo"];
                                                    $CARGO_wprocod       = $arr_info_equipo_examen["codigo"];
                                                    $CARGO_wpronom       = $arr_info_equipo_examen["nombre"];
                                                }

                                                $datos_default = array( "wnomcon"           => $arr_lista_conceptos[$concepto_cod_ppal],
                                                                        "wcantidad"                  => 1,
                                                                        "wprocedimiento"             => $CARGO_procedimiento,
                                                                        "wprocedimiento_nombre"      => $CARGO_wpronom,
                                                                        "wcodcon"                    => $cobro_concepto,
                                                                        "wtercero"                   => "",
                                                                        "wtercero_nombre"            => "",
                                                                        "wespecialidad"              => "",
                                                                        "wespecialidad_nombre"       => "",
                                                                        "wporcentaje"                => 0,
                                                                        "wtipo_facturacion"          => "",
                                                                        "wvalor_final"               => 0,
                                                                                                     // "wfacturable"                => $facturable_insumo,//// FALTA DEFINIRLO SEGÚN LOS LIMITES DE UVRS Y POLITICAS CON LA EMPRESA SI CUBRE O NO CUBRE
                                                                        "wgrabar"                    => 'on',
                                                                                                     // "wbaseliquidacion"           => $wbaseliquidacion,
                                                                        "wvalor"                     => "",
                                                                        "wtipo_anestesia_cx"         => $arr_datos_procedimiento["wtipo_anestesia_cx"],
                                                                        "wtiempo_sala_recuperarcion" => $arr_datos_procedimiento["wtiempo_sala_recuperarcion"],
                                                                        "wtiempo_uso_minutos"        => $arr_datos_procedimiento["wtiempo_uso_minutos"],
                                                                        "wtiempo_minutos_cx"         => $arr_datos_procedimiento["wtiempo_minutos_cx"],
                                                                        "wexiste"                    => $arr_valor_cobro["wexiste"],
                                                                                                     // "wserv"                      => $gru_servicio,
                                                                        "mueve_inventario"           => $mueve_inventario,
                                                                                                     // "wes_medicamento"            => $row["Artesm"],
                                                                                                     // "id_insumo"                  => $row["id_insumo"],
                                                                        "porcentaje_cxMult_CARGO"          => $porcentaje_cxMult,
                                                                        "valor_final_original"       => 0,
                                                                        "wvaltarReco"                => 0,
                                                                        "wvaltarExce"                => 0,
                                                                        "wvalorTarifaBase"           => 0,
                                                                        "WTIENE_TARIFA"              => "off",
                                                                        "cargo_examen_o_equipo"      => ((!empty($codigo_equipo_examen)) ? 'on': 'off'),
                                                                        "examen_equipo_codigo"       => $arr_info_equipo_examen["codigo"],
                                                                        "examen_equipo_nombre"       => $arr_info_equipo_examen["nombre"]);

                                                // Si no hay tarifa entonces guardarlo en el array de cargos sin tarifas, pendientes por corregir
                                                // y se guardarán temporalmente en una tabla desde donde se mostrarán en el monitor de revisión.
                                                if(array_key_exists("error_tarifa", $arr_valor_cobro) && $arr_valor_cobro["error_tarifa"] == 1)
                                                {
                                                    // Agregar al array de cargos sin tarifa
                                                    $arr_cargos_sinTariga[] = array("whistoria"                => $whistoria,
                                                                                    "wingreso"                 => $wing,
                                                                                    "wserv_ingreso"            => $wser,
                                                                                    "wempresa_responsable"     => $wcod_empresa,
                                                                                    "wservicio_graba"          => $wcentro_costo,
                                                                                    "wconcepto"                => $cobro_concepto,
                                                                                    "wprocedimiento"           => $cobro_procedimiento,
                                                                                    "wtercero"                 => $codigo_wtercero_tarifa,
                                                                                    "wespecialidad"            => $especialidad_procedimiento,
                                                                                    "wmodalidad"               => $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["configuracion_liquidar"][$codigo_concepto]["procedimiento_tarifa"],
                                                                                    "wvalor"                   => $arr_valor_cobro['wvaltar'],
                                                                                    "wtarifa_empresa"          => $wtarifa_empresa,
                                                                                    "wes_insumo"               => "",
                                                                                    "id_encabezado_sin_tarifa" => '',
                                                                                    "id_detalle_sin_tarifa"    => ''
                                                                                    );
                                                }

                                                if($arr_valor_cobro['error'] == 1)
                                                {
                                                    if($concepto_cod_ppal == $id_concepto_uso_equipos || $concepto_cod_ppal== $id_concepto_examenes)
                                                    {
                                                        // $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal][$wcod_empresa]["lista_examen_equipo"] = array();

                                                        if(!array_key_exists($CARGO_procedimiento, $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal]))
                                                        {
                                                            $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal][$CARGO_procedimiento] = array();
                                                        }

                                                        if(!array_key_exists($wcod_empresa, $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal][$CARGO_procedimiento]))
                                                        {
                                                            $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal][$CARGO_procedimiento][$wcod_empresa] = array();
                                                        }
                                                            $arr_CargosGrabadosResponsables[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal][$CARGO_procedimiento][$wcod_empresa] = $datos_default;
                                                    }
                                                }
                                                else
                                                {
                                                    $tipo_facturacion = $arr_valor_cobro['wtipfac'];
                                                    // Validar terceros, topes, saldos
                                                    // if(strtoupper($wfacturable) == 'S' && strtoupper($wrecexc) == 'R')
                                                    {
                                                        // RESPONSABLE_VIRTUAL_PACIENTE Ingcem Ingent Ingtar
                                                        $explode_dif_proced = explode("_", $procedimiento_liquidar_cod_dif);
                                                        $procedimiento_sin_posicion_org = $procedimiento_liquidar_cod_dif;

                                                        // Es procedimiento bilateral y se diferencia porque tiene concatenada una posición
                                                        $guardar_temp_posicion = "";
                                                        if(count($explode_dif_proced) > 1)
                                                        {
                                                            $procedimiento_sin_posicion_org = $explode_dif_proced[0];
                                                            $guardar_temp_posicion          = $explode_dif_proced[1];
                                                        }

                                                        if($CARGO_wprocod == $procedimiento_liquidar_cod_dif)
                                                        {
                                                            $CARGO_procedimiento = $procedimiento_sin_posicion_org;
                                                            $CARGO_wprocod       = $procedimiento_sin_posicion_org;
                                                        }
                                                        // Este condicional se encarga de validar si en la interface se desmarcó el check de grabar, entonces no debe tener
                                                        // en cuenta ese valor para descontarlo del saldo del responsable durante la sumulación.
                                                        if( isset($arr_datos_liquidados)
                                                            && array_key_exists($procedimiento_liquidar_cod_dif, $arr_datos_liquidados)
                                                            && array_key_exists($concepto_cod_ppal, $arr_datos_liquidados[$procedimiento_liquidar_cod_dif])
                                                          )
                                                        {
                                                            if(array_key_exists($arr_info_equipo_examen["codigo"], $arr_datos_liquidados[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal])
                                                                && $arr_datos_liquidados[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal][$arr_info_equipo_examen["codigo"]]['wgrabar'] == 'off')
                                                            {
                                                                $facturable_init = 'N';
                                                            }
                                                            elseif($arr_datos_liquidados[$procedimiento_liquidar_cod_dif][$concepto_cod_ppal]['wgrabar'] == 'off')
                                                            {
                                                                $facturable_init = 'N';
                                                            }
                                                            // $facturable_init = 'N';
                                                        }


                                                        $wfecha = $fecha_actual;
                                                        $whora  = $hora_actual;
                                                        $wcantidad = 1;

                                                        if(array_key_exists("codHomologar", $arr_valor_cobro))
                                                        {
                                                            $datosGrabarCargos['codHomologar']           = $arr_valor_cobro['codHomologar'];
                                                        }

                                                        $datosGrabarCargos['valor_final_original']       = $valor_final;
                                                        $datosGrabarCargos['porcentaje_cxMult_CARGO']    = $porcentaje_cxMult;
                                                        $datosGrabarCargos['info_tarifa']                = '';
                                                        $datosGrabarCargos['error_tarifa']               = $arr_valor_cobro['error_tarifa'];
                                                        $datosGrabarCargos['tarifa_empresa_cargo']       = $RESPONSABLE_VIRTUAL_PACIENTE['Ingtar'];
                                                        $datosGrabarCargos['idTope']                     = '';
                                                        $datosGrabarCargos['enParalelo']                 = 'off';
                                                        $datosGrabarCargos['idParalelo']                 = '';
                                                        $datosGrabarCargos['cargo_examen_o_equipo']      = ((!empty($codigo_equipo_examen)) ? 'on': 'off');
                                                        $datosGrabarCargos['examen_equipo_codigo']       = $arr_info_equipo_examen["codigo"];
                                                        $datosGrabarCargos['examen_equipo_nombre']       = $arr_info_equipo_examen["nombre"];
                                                        $datosGrabarCargos['WTIENE_TARIFA']              = "on";

                                                        $datosGrabarCargos['historia']                   = $whistoria;
                                                        $datosGrabarCargos['ingreso']                    = $wing;
                                                        $datosGrabarCargos['entidad']                    = $nitEmpresa;
                                                        $datosGrabarCargos['cambio_responsable']         = $RESPONSABLE_VIRTUAL_PACIENTE['Ingcem'];

                                                        $datosGrabarCargos['whistoria']                  = $whistoria;
                                                        $datosGrabarCargos['wing']                       = $wing;
                                                        $datosGrabarCargos['wno1']                       = $wno1;
                                                        $datosGrabarCargos['wno2']                       = $wno2;
                                                        $datosGrabarCargos['wap1']                       = $wap1;
                                                        $datosGrabarCargos['wap2']                       = $wap2;
                                                        $datosGrabarCargos['wdoc']                       = $wdoc;
                                                        $datosGrabarCargos['wcodemp']                    = $wcod_empresa;
                                                        $datosGrabarCargos['wnomemp']                    = $wnomemp_tal;
                                                        $datosGrabarCargos['wser']                       = $wser;
                                                        $datosGrabarCargos['wfecing']                    = $wfecing;
                                                        $datosGrabarCargos['wtar']                       = $wtarifa_empresa;

                                                        $datosGrabarCargos['concepto']                   = $concepto_cod_ppal; //
                                                        $datosGrabarCargos['wcodcon']                    = $concepto_cod_ppal; //
                                                        $datosGrabarCargos['wnomcon']                    = $arr_lista_conceptos[$concepto_cod_ppal]; //

                                                        $datosGrabarCargos['procedimiento']              = $CARGO_procedimiento; //
                                                        $datosGrabarCargos['wprocod']                    = $CARGO_wprocod; //
                                                        $datosGrabarCargos['wpronom']                    = $CARGO_wpronom; //

                                                        $datosGrabarCargos['wcodter']                    = ""; //
                                                        $datosGrabarCargos['wnomter']                    = ""; //

                                                        $datosGrabarCargos['wvalorTarifaBase']           = $valor_tarifa; //
                                                        $datosGrabarCargos['wporter']                    = 0; //
                                                        $datosGrabarCargos['wcantidad']                  = $wcantidad;
                                                        $datosGrabarCargos['wvaltar']                    = $valor_final_simulado; //
                                                        $datosGrabarCargos['wrecexc']                    = 'R'; //
                                                        $datosGrabarCargos['wfacturable']                = $facturable_init; //

                                                        $datosGrabarCargos['centroCostos']               = $wcentro_costo;
                                                        $datosGrabarCargos['wcco']                       = $wcentro_costo;
                                                        $datosGrabarCargos['wccogra']                    = $wcentro_costo;
                                                        $datosGrabarCargos['wfeccar']                    = $wfecha_cargo;
                                                        $datosGrabarCargos['whora_cargo']                = $whora_cargo;
                                                        $datosGrabarCargos['wconinv']                    = $mueve_inventario; //
                                                        $datosGrabarCargos['wcodpaq']                    = '';
                                                        $datosGrabarCargos['wpaquete']                   = 'off';
                                                        $datosGrabarCargos['wconabo']                    = '';
                                                        $datosGrabarCargos['wdevol']                     = '';
                                                        $datosGrabarCargos['waprovecha']                 = 'off';
                                                        $datosGrabarCargos['wconmvto']                   = '';
                                                        $datosGrabarCargos['wexiste']                    = $wexiste;
                                                        $datosGrabarCargos['wbod']                       = $wbod;
                                                        $datosGrabarCargos['wconser']                    = "H" ; //$arr_guardar['wserv']
                                                        $datosGrabarCargos['wtipfac']                    = $tipo_facturacion;
                                                        $datosGrabarCargos['wexidev']                    = 0;
                                                        $datosGrabarCargos['wfecha']                     = $wfecha;
                                                        $datosGrabarCargos['whora']                      = $whora;
                                                        $datosGrabarCargos['wespecialidad']              = $especialidad_procedimiento; //
                                                        $datosGrabarCargos['cobraHonorarios']            = "on";
                                                        $datosGrabarCargos['wgraba_varios_terceros']     = false;
                                                        $datosGrabarCargos['wcodcedula']                 = '';

                                                        $datosGrabarCargos['tipoEmpresa']                = $tipoEmpresa;
                                                        $datosGrabarCargos['tipoIngreso']                = ''; // Para evitar warnings en funciones
                                                        $datosGrabarCargos['tipoPaciente']               = ''; // Para evitar warnings en funciones
                                                        $datosGrabarCargos['warctar']                    = $arr_info_conf['tabla_valida_precios'];
                                                        $datosGrabarCargos['wvaltarReco']                = 0; // Para evitar warnings en funciones
                                                        $datosGrabarCargos['saldoTope']                  = ''; // Para evitar warnings en funciones
                                                        $datosGrabarCargos['topeGeneral']                = ''; // Para evitar warnings en funciones

                                                                                                         // Para no enviarlos como parámetros a la función.
                                                        $datosGrabarCargos['wcod_empresa']               = $wcod_empresa;
                                                        $datosGrabarCargos['wfecha_cargo']               = $wfecha_cargo;
                                                        $datosGrabarCargos['cobro_concepto']             = $cobro_concepto;
                                                        $datosGrabarCargos['procedimiento_liquidar_cod'] = $procedimiento_sin_posicion_org;
                                                        $datosGrabarCargos['procedimiento_liquidar_nomb']= $arr_datos_procedimiento['nombre'];
                                                        $datosGrabarCargos['wcentro_costo']              = $wcentro_costo;
                                                        $datosGrabarCargos['nomCajero']                  = "";
                                                        $datosGrabarCargos['wvaltarExce']                = 0;

                                                        $datosGrabarCargos['wtipo_anestesia_cx']         = $arr_datos_procedimiento["wtipo_anestesia_cx"];
                                                        $datosGrabarCargos["wtiempo_sala_recuperarcion"] = $arr_datos_procedimiento["wtiempo_sala_recuperarcion"];
                                                        $datosGrabarCargos["wtiempo_uso_minutos"]        = $arr_datos_procedimiento["wtiempo_uso_minutos"];
                                                        $datosGrabarCargos["wtiempo_minutos_cx"]         = $arr_datos_procedimiento["wtiempo_minutos_cx"];

                                                        $datosGrabarCargos["IdNoFacturables"]            = "";
                                                        $datosGrabarCargos["codigo_especialidad"]        = "";
                                                        $datosGrabarCargos["wespecialidad_nombre"]       = "";
                                                        $datosGrabarCargos["wgrabar"]                    = "";
                                                        $datosGrabarCargos['nitEmpresa']                 = $nitEmpresa;
                                                        $datosGrabarCargos['id_insumo']                  = "";
                                                        $datosGrabarCargos["wbaseliquidacion"]           = $wbaseliquidacion;
                                                        $datosGrabarCargos["arr_cambio_responsable"]     = array();
                                                        $datosGrabarCargos["arr_encabezado"]             = $arr_datos_procedimiento["arr_encabezado"];
                                                        // $datosGrabarCargos["ccoActualPac"]               = $ccoActualPac;


                                                        $datosGrabarCargos["es_insumo"]         = "off";

                                                        // --> Si la empresa es particular esto se graba como excedente
                                                        if($wcod_empresa == $codEmpParticular)
                                                            $datosGrabarCargos['wrecexc'] = 'E';

                                                        // --> Valor excedente
                                                        if($datosGrabarCargos['wrecexc'] == 'E')
                                                            $datosGrabarCargos['wvaltarExce'] = round($wcantidad*($valor_final_simulado*1));
                                                        // --> Valor reconocido
                                                        else
                                                            $datosGrabarCargos['wvaltarReco'] = round($wcantidad*($valor_final_simulado*1));

                                                        $userCargo = explode('-',$_SESSION['user']);
                                                        $wuse      = $userCargo[1];
                                                        global $wuse;
                                                        // print_r($datosGrabarCargos);

                                                        // --> Validar politicas
                                                        $datosGrabarCargos['PermitirGrabar'] = true;
                                                        $CargosAnexos   =   array();
                                                        if(!$es_paquete)
                                                        {
                                                            ValidarGrabacion($datosGrabarCargos, $CargosAnexos, true);

                                                            $CodTipEmp = $RESPONSABLE_VIRTUAL_PACIENTE['tipoEmpre']; //'*';
                                                            $CodTar = $RESPONSABLE_VIRTUAL_PACIENTE['Ingtar']; //'*';
                                                            $CodNit = '*';
                                                            $CodEnt = $RESPONSABLE_VIRTUAL_PACIENTE['Ingcem']; //'*';
                                                            $CodEsp = ($datosGrabarCargos['wespecialidad'] == '') ? '*': $datosGrabarCargos['wespecialidad'];
                                                            $CodCco = $datosGrabarCargos['wcco'];
                                                            if(!ValidarSiEsFacturable($datosGrabarCargos['wcodcon'], $datosGrabarCargos['wprocod'], $CodTipEmp, $CodTar, $CodNit, $datosGrabarCargos['wcodemp'], $CodEsp, $CodCco, $whistoria, $wing))
                                                            {
                                                                $datosGrabarCargos['wfacturable'] = 'N';
                                                            }
                                                        }

                                                        $datosGrabarCargos['procedimiento']              = $datosGrabarCargos['procedimiento'].((!empty($guardar_temp_posicion)) ? "_$guardar_temp_posicion": ""); // Se completa la llave que diferencia a los procedimientos de igual código p.e.   01102_ARRIBA,   01102_ABAJO, ...
                                                        $datosGrabarCargos['wprocod']                    = $datosGrabarCargos['wprocod'].((!empty($guardar_temp_posicion)) ? "_$guardar_temp_posicion": ""); // Se completa la llave que diferencia a los procedimientos de igual código p.e.   01102_ARRIBA,   01102_ABAJO, ...
                                                        $datosGrabarCargos['procedimiento_liquidar_cod'] = $datosGrabarCargos['procedimiento_liquidar_cod'].((!empty($guardar_temp_posicion)) ? "_$guardar_temp_posicion": ""); // Se completa la llave que diferencia a los procedimientos de igual código p.e.   01102_ARRIBA,   01102_ABAJO, ...
                                                        if(isset($datosGrabarCargos['IdNoFacturables']))
                                                        {
                                                           // ActualizarCargoComoNoFacturable($datosGrabarCargos['IdNoFacturables']);
                                                        }
                                                        // if($datosGrabarCargos['PermitirGrabar'])
                                                        // {
                                                        //     SimularGrabarCargo($conex, $wemp_pmla, $wbasedato, $user_session, $datosGrabarCargos, $concepto_cod_ppal, $procedimiento_liquidar_cod_dif);
                                                        // }
                                                        if($datosGrabarCargos['PermitirGrabar'] )
                                                        {
                                                            // $datosGrabarCargos['procedimiento_liquidar_cod'] = $procedimiento_liquidar_cod_dif;
                                                            SimularGrabarCargo($conex, $wemp_pmla, $wbasedato, $user_session, $datosGrabarCargos, $concepto_cod_ppal, $procedimiento_liquidar_cod_dif);
                                                            if(count($CargosAnexos)>0)
                                                            {
                                                                foreach($CargosAnexos as $arr_variables_anexo)
                                                                {
                                                                    $arr_variables_anexo['procedimiento']              = $arr_variables_anexo['procedimiento'].((!empty($guardar_temp_posicion)) ? "_$guardar_temp_posicion": ""); // Se completa la llave que diferencia a los procedimientos de igual código p.e.   01102_ARRIBA,   01102_ABAJO, ...
                                                                    $arr_variables_anexo['wprocod']                    = $arr_variables_anexo['wprocod'].((!empty($guardar_temp_posicion)) ? "_$guardar_temp_posicion": ""); // Se completa la llave que diferencia a los procedimientos de igual código p.e.   01102_ARRIBA,   01102_ABAJO, ...
                                                                    $arr_variables_anexo['procedimiento_liquidar_cod'] = $arr_variables_anexo['procedimiento_liquidar_cod'].((!empty($guardar_temp_posicion)) ? "_$guardar_temp_posicion": ""); // Se completa la llave que diferencia a los procedimientos de igual código p.e.   01102_ARRIBA,   01102_ABAJO, ...
                                                                    // $arr_variables_anexo
                                                                    SimularGrabarCargo($conex, $wemp_pmla, $wbasedato, $user_session, $arr_variables_anexo, $concepto_cod_ppal, $procedimiento_liquidar_cod_dif);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                //
                                            }
                                        }
                                    }
                                    else
                                    {
                                        //
                                    }
                                }

                                // **********************************  BUSCAR Y SIMULAR INSUMOS ***********************************
                                // **********************************  BUSCAR Y SIMULAR INSUMOS ***********************************
                                $insumos_html = "";
                                $row_insumos = array();
                                $row_parametos_generales = array();
                                $facturable_insumo = "S";
                                if(array_key_exists($procedimiento_liquidar_cod_dif, $arr_insumos_procedimiento))
                                {
                                    foreach($arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif] as $es_medicamento => $arr_datos_insumos)
                                    {
                                        $row_insumos               = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$es_medicamento]["lista_insumos"];
                                        $facturable_insumo         = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$es_medicamento]["parametros"]["insumos_facturables"];
                                        $facturable_medicamentos   = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$es_medicamento]["parametros"]["medicamentos_facturables"];
                                        $facturable_materiales     = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$es_medicamento]["parametros"]["materiales_facturables"];

                                        $facturable_insumo_temp       = $facturable_insumo;
                                        $facturable_medicamentos_temp = $facturable_medicamentos;
                                        $facturable_materiales_temp   = $facturable_materiales;
                                        $excluir_clases               = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$es_medicamento]["clasificacion_facturable"];
                                        $grupos_no_facturables        = $arr_insumos_procedimiento[$procedimiento_liquidar_cod_dif][$es_medicamento]["grupos_no_facturables"]; // si los insumos por límite son facturables, estas clasificaciones NO lo serán.
                                        $arr_grupos_excluidos         = (!empty($excluir_clases)) ? explode("|",$excluir_clases) : array();
                                        $arr_grupos_no_facturables    = (!empty($grupos_no_facturables)) ? explode("|",$grupos_no_facturables) : array();

                                        foreach($arr_mercado_completo_temp as $codigo_insumo => $row)
                                        {
                                            $codigo_insumo = $row["codigo_insumo"];
                                            // $facturable_insumo = $facturable_insumo_temp;
                                            if($row["Artesm"] == 'on')
                                            {
                                                $facturable_insumo = $facturable_medicamentos_temp;
                                            }
                                            else
                                            {
                                                $facturable_insumo = $facturable_materiales_temp;
                                            }

                                            // Validar si los insumos son no facturables, si es así entonces revisar la clase o grupo del medicamento para ver si se debe excluir de los
                                            // no facturables
                                            if($facturable_insumo == 'N')
                                            {
                                                $grupo_art_exp = explode("-", $row["Artgru"]);
                                                $grupo_art = $grupo_art_exp[0];
                                                if(in_array($grupo_art, $arr_grupos_excluidos))
                                                {
                                                    $facturable_insumo = 'S';
                                                }
                                            }
                                            elseif($facturable_insumo == 'S')
                                            {
                                                $grupo_art_exp = explode("-", $row["Artgru"]);
                                                $grupo_art = $grupo_art_exp[0];
                                                if(in_array($grupo_art, $arr_grupos_no_facturables))
                                                {
                                                    $facturable_insumo = 'N';
                                                }
                                            }

                                            $arr_concepto_cobrar = array();
                                            $saldo_insumo = $row["saldo_insumo"]*1;
                                            if($saldo_insumo > 0)
                                            {
                                                if($row["Artesm"] == 'on')
                                                {
                                                    $arr_concepto_cobrar = $arr_info_conceptos_inventario[$concepto_medicamentos_mueven_inv];
                                                }
                                                else
                                                {
                                                    $arr_concepto_cobrar = $arr_info_conceptos_inventario[$concepto_materiales_mueven_inv];
                                                }

                                                // $wnumero_puntos
                                                // $modalidad_facturacion
                                                $tabla_valida_precios = $arr_concepto_cobrar["tabla_valida_precios"];
                                                $cobro_insumo         = $row["codigo_insumo"];
                                                $nombre_insumo        = $row["nombre_insumo"];
                                                $cobro_concepto       = $arr_concepto_cobrar["concepto_codigo"];
                                                                      // $wcentro_costo        = $wcentro_costo;
                                                                      // $wtarifa_empresa      = $wtarifa_empresa;
                                                                      // $wcod_empresa         = $wcod_empresa;
                                                $mueve_inventario     = $arr_concepto_cobrar["mueve_inventario"];
                                                $concepto_nombre      = $arr_concepto_cobrar["concepto_nombre"];
                                                $gru_servicio         = $arr_concepto_cobrar['servicio'];

                                                // $tipo_cobro           = $arr_concepto_cobrar["tipo_cobro"];
                                                                      // $wfecha_cargo         = $wfecha_cargo;
                                                                      // $wtipo_ingreso        = $wtipo_ingreso;
                                                // echo "datos_desde_procedimiento($tabla_valida_precios, $cobro_insumo, $cobro_concepto, $wcentro_costo, $wtarifa_empresa, $wcod_empresa, $mueve_inventario, $wfecha_cargo, $wtipo_ingreso);";
                                                $arr_valor_cobro_insumo = datos_desde_procedimiento($cobro_insumo, $cobro_concepto, $wcentro_costo, $wcentro_costo, $wcod_empresa, $wfecha_cargo, $wtipo_ingreso, "*", "on", false ,'', $wfecha_cargo, $whora_cargo);

                                                // Inicializar datos REALES de insumos
                                                // $arr_CargosGrabadosResponsables_insumos =    (procedimiento =>   (insumo =>  (empresa_responsable => arra_información
                                                //                                                                              )
                                                //                                                                  )
                                                //                                              )
                                                if(!array_key_exists($procedimiento_liquidar_cod_dif, $arr_CargosGrabadosResponsables_insumos))
                                                {
                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif] = array();
                                                }

                                                if(!array_key_exists($cobro_insumo, $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif]))
                                                {
                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo] = array("simulacion"=>array(), "datos_reales"=>array());
                                                }

                                                $datos_reales = array(  "wconcepto_nombre"           => $concepto_nombre,
                                                                        "wcantidad"                  => $saldo_insumo,
                                                                        "wprocedimiento"             => $cobro_insumo,
                                                                        "wprocedimiento_nombre"      => $nombre_insumo,
                                                                        "wconcepto"                  => $cobro_concepto,
                                                                        "wtercero"                   => "",
                                                                        "wtercero_nombre"            => "",
                                                                        "wespecialidad"              => "",
                                                                        "wespecialidad_nombre"       => "",
                                                                        "wporcentaje"                => 0,
                                                                        "wtipo_facturacion"          => "",
                                                                        "wvalor_final"               => 0,
                                                                        "wfacturable"                => $facturable_insumo,
                                                                        "wgrabar"                    => 'on',
                                                                        "wbaseliquidacion"           => $wbaseliquidacion,
                                                                        "wvalor"                     => "",
                                                                        "wtipo_anestesia_cx"         => $arr_datos_procedimiento["wtipo_anestesia_cx"],
                                                                        "wtiempo_sala_recuperarcion" => $arr_datos_procedimiento["wtiempo_sala_recuperarcion"],
                                                                        "wtiempo_uso_minutos"        => $arr_datos_procedimiento["wtiempo_uso_minutos"],
                                                                        "wtiempo_minutos_cx"         => $arr_datos_procedimiento["wtiempo_minutos_cx"],
                                                                        "wexiste"                    => $arr_valor_cobro_insumo["wexiste"],
                                                                        "wserv"                      => $gru_servicio,
                                                                        "mueve_inventario"           => $mueve_inventario,
                                                                        "wes_medicamento"            => $row["Artesm"],
                                                                        "id_insumo"                  => $row["id_insumo"],
                                                                        "WTIENE_TARIFA"              => "off");
                                                $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"] = $datos_reales;

                                                $info_tarifa = "";
                                                $tipo_facturacion = '';
                                                if($arr_valor_cobro_insumo['error'] == 1)
                                                {
                                                    $info_tarifa = 'off';
                                                    // Agregar al array de cargos sin tarifa
                                                    $arr_cargos_sinTariga[] = array("whistoria"                => $whistoria,
                                                                                    "wingreso"                 => $wing,
                                                                                    "wserv_ingreso"            => $wser,
                                                                                    "wempresa_responsable"     => $wcod_empresa,
                                                                                    "wservicio_graba"          => $wcentro_costo,
                                                                                    "wconcepto"                => $cobro_concepto,
                                                                                    "wprocedimiento"           => $cobro_insumo,
                                                                                    "wtercero"                 => '',
                                                                                    "wespecialidad"            => '',
                                                                                    "wmodalidad"               => $arr_procedimientos_liquidar[$procedimiento_liquidar_cod_dif]["configuracion_liquidar"][$cobro_concepto]["procedimiento_tarifa"],
                                                                                    "wtarifa_empresa"          => $wtarifa_empresa,
                                                                                    "wvalor"                   => 0,
                                                                                    "wes_insumo"               => "on",
                                                                                    "id_encabezado_sin_tarifa" => '',
                                                                                    "id_detalle_sin_tarifa"    => ''
                                                                                    );
                                                }
                                                else
                                                {
                                                    $tipo_facturacion = $arr_valor_cobro_insumo['wtipfac'];
                                                    $info_tarifa      = $arr_valor_cobro_insumo["wvaltar"]*1;
                                                    $valor_tarifa     = $arr_valor_cobro_insumo["wvaltar"]*1;
                                                    $wexiste          = $arr_valor_cobro_insumo["wexiste"];

                                                    // Verifica si aplica un porcentaje diferente para el concepto por efectos de cirugías multiples.
                                                    $porcentaje_cxMult  = 100;
                                                    if(array_key_exists($cobro_concepto, $arr_porcentajes))
                                                    {
                                                        $porcentaje_cxMult  = ($arr_porcentajes[$cobro_concepto])*1;
                                                    }
                                                    $valor_final_porcent = ($porcentaje_cxMult < 100) ? (($porcentaje_cxMult * $valor_tarifa)/100) : $valor_tarifa;
                                                    $valor_final = ($porcentaje_cxMult == 0) ? 0 : $valor_final_porcent; // Este valor se guarda en el array de liquidación listo para enviar a la función de GrabarCargos, se asigna a una variable de simulación.

                                                    $concepto_cod_ppal = $cobro_concepto;

                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"]["wporcentaje"]       = $porcentaje_cxMult;
                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"]["wvalor_final"]      = $valor_final;
                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"]["wtipo_facturacion"] = $tipo_facturacion;
                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"]["wvalor"]            = $valor_tarifa;
                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"]["wexiste"]           = $wexiste;
                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"]["WTIENE_TARIFA"]     = "on";
                                                    $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"]["cobraHonorarios"]   = "on";

                                                    if(($valor_final*1) > 0)
                                                    {

                                                        if($mueve_inventario == 'on')
                                                        {
                                                            global $porcentaje_cxMult; // se debió poner en global porque en las funciones de simulación se pueden crear nuevos cargos en los que se debe aplicar porcentaje de descuento en su tarifa.

                                                            $valor_final_simulado = $valor_final;

                                                            // INFORMA SI DICE QUE NO TIENE TARIFA

                                                            $datosGrabarCargos = array();
                                                            // $info_tarifa = '';

                                                            // $info_tarifa = '<img style="cursor:pointer" id="" class="tooltip" title="NO TIENE TARIFA" width="16" height="16" src="../../images/medical/sgc/Warning-32.png" />';
                                                            // $datosGrabarCargos['info_tarifa'] = $info_tarifa;
                                                            // $arr_CargosGrabadosResponsables_insumos[$cobro_insumo][$concepto_cod_ppal] = array();

                                                            // Validar terceros, topes, saldos
                                                            // if(strtoupper($wfacturable) == 'S' && strtoupper($wrecexc) == 'R')
                                                            {

                                                                $wfecha = $fecha_actual;
                                                                $whora  = $hora_actual;
                                                                $wcantidad = $saldo_insumo;

                                                                if(array_key_exists("codHomologar", $arr_valor_cobro_insumo))
                                                                {
                                                                    $datosGrabarCargos['codHomologar']       = $arr_valor_cobro_insumo['codHomologar'];
                                                                }

                                                                $datosGrabarCargos['valor_final_original']   = $valor_final;
                                                                $datosGrabarCargos['porcentaje_cxMult_CARGO']= $porcentaje_cxMult;
                                                                $datosGrabarCargos['info_tarifa']            = '';
                                                                $datosGrabarCargos['error_tarifa']           = '';
                                                                $datosGrabarCargos['tarifa_empresa_cargo']   = '';
                                                                $datosGrabarCargos['idTope']                 = '';
                                                                $datosGrabarCargos['enParalelo']             = 'off';
                                                                $datosGrabarCargos['idParalelo']             = '';
                                                                $datosGrabarCargos['cargo_examen_o_equipo']  = "";
                                                                $datosGrabarCargos['examen_equipo_codigo']   = "";
                                                                $datosGrabarCargos['examen_equipo_nombre']   = "";

                                                                $datosGrabarCargos['historia']               = $whistoria;
                                                                $datosGrabarCargos['ingreso']                = $wing;
                                                                $datosGrabarCargos['entidad']                = $nitEmpresa;
                                                                $datosGrabarCargos['cambio_responsable']     = $RESPONSABLE_VIRTUAL_PACIENTE['Ingcem'];

                                                                $datosGrabarCargos['whistoria']              = $whistoria;
                                                                $datosGrabarCargos['wing']                   = $wing;
                                                                $datosGrabarCargos['wno1']                   = $wno1;
                                                                $datosGrabarCargos['wno2']                   = $wno2;
                                                                $datosGrabarCargos['wap1']                   = $wap1;
                                                                $datosGrabarCargos['wap2']                   = $wap2;
                                                                $datosGrabarCargos['wdoc']                   = $wdoc;
                                                                $datosGrabarCargos['wcodemp']                = $wcod_empresa;
                                                                $datosGrabarCargos['wnomemp']                = $wnomemp_tal;
                                                                $datosGrabarCargos['wser']                   = $wser;
                                                                $datosGrabarCargos['wfecing']                = $wfecing;
                                                                $datosGrabarCargos['wtar']                   = $wtarifa_empresa;

                                                                $datosGrabarCargos['concepto']               = $concepto_cod_ppal; //
                                                                $datosGrabarCargos['wcodcon']                = $concepto_cod_ppal; //
                                                                $datosGrabarCargos['wnomcon']                = $concepto_nombre; //

                                                                $datosGrabarCargos['procedimiento']          = $cobro_insumo; //
                                                                $datosGrabarCargos['wprocod']                = $cobro_insumo; //
                                                                $datosGrabarCargos['wpronom']                = $nombre_insumo; //

                                                                $datosGrabarCargos['wcodter']                = ""; //
                                                                $datosGrabarCargos['wnomter']                = ""; //

                                                                $datosGrabarCargos['wvalorTarifaBase']       = $valor_tarifa; //
                                                                $datosGrabarCargos['wporter']                = 0; //
                                                                $datosGrabarCargos['wcantidad']              = $wcantidad;
                                                                $datosGrabarCargos['wvaltar']                = $valor_final_simulado; //
                                                                $datosGrabarCargos['wrecexc']                = 'R'; //
                                                                $datosGrabarCargos['wfacturable']            = $facturable_insumo; //

                                                                $datosGrabarCargos['centroCostos']           = $wcentro_costo;
                                                                $datosGrabarCargos['wcco']                   = $wcentro_costo;
                                                                $datosGrabarCargos['wccogra']                = $wcentro_costo;
                                                                $datosGrabarCargos['wfeccar']                = $wfecha_cargo;
                                                                $datosGrabarCargos['whora_cargo']            = $whora_cargo;
                                                                $datosGrabarCargos['wconinv']                = $mueve_inventario; //
                                                                $datosGrabarCargos['wcodpaq']                = '';
                                                                $datosGrabarCargos['wpaquete']               = 'off';
                                                                $datosGrabarCargos['wconabo']                = '';
                                                                $datosGrabarCargos['wdevol']                 = '';
                                                                $datosGrabarCargos['waprovecha']             = 'off';
                                                                $datosGrabarCargos['wconmvto']               = '';
                                                                $datosGrabarCargos['wexiste']                = $wexiste;
                                                                $datosGrabarCargos['wbod']                   = $wbod;
                                                                $datosGrabarCargos['wconser']                = $gru_servicio;
                                                                $datosGrabarCargos['wtipfac']                = $tipo_facturacion;
                                                                $datosGrabarCargos['wexidev']                = 0;
                                                                $datosGrabarCargos['wfecha']                 = $wfecha;
                                                                $datosGrabarCargos['whora']                  = $whora;
                                                                $datosGrabarCargos['wespecialidad']          = "*"; //
                                                                $datosGrabarCargos['cobraHonorarios']        = "on"; //
                                                                $datosGrabarCargos['wgraba_varios_terceros'] = false;
                                                                $datosGrabarCargos['wcodcedula']             = '';

                                                                $datosGrabarCargos['tipoEmpresa']            = $tipoEmpresa;
                                                                $datosGrabarCargos['tipoIngreso']            = ''; // Para evitar warnings en funciones
                                                                $datosGrabarCargos['tipoPaciente']           = ''; // Para evitar warnings en funciones
                                                                $datosGrabarCargos['warctar']                = $tabla_valida_precios;
                                                                $datosGrabarCargos['wvaltarReco']            = 0; // Para evitar warnings en funciones
                                                                $datosGrabarCargos['saldoTope']              = ''; // Para evitar warnings en funciones
                                                                $datosGrabarCargos['topeGeneral']            = ''; // Para evitar warnings en funciones

                                                                // Para no enviarlos como parámetros a la función.
                                                                $datosGrabarCargos['whistoria']                  = $whistoria;
                                                                $datosGrabarCargos['wing']                       = $wing;
                                                                $datosGrabarCargos['wcod_empresa']               = $wcod_empresa;
                                                                $datosGrabarCargos['wfecha_cargo']               = $wfecha_cargo;
                                                                $datosGrabarCargos['cobro_concepto']             = $concepto_cod_ppal;
                                                                $datosGrabarCargos['procedimiento_liquidar_cod'] = $procedimiento_liquidar_cod_dif;
                                                                $datosGrabarCargos['procedimiento_liquidar_nomb']= $arr_datos_procedimiento['nombre'];
                                                                $datosGrabarCargos['wcentro_costo']              = $wcentro_costo;
                                                                $datosGrabarCargos['nomCajero']                  = "";
                                                                $datosGrabarCargos['wvaltarExce']                = 0;

                                                                $datosGrabarCargos['wtipo_anestesia_cx']         = $arr_datos_procedimiento["wtipo_anestesia_cx"];
                                                                $datosGrabarCargos["wtiempo_sala_recuperarcion"] = $arr_datos_procedimiento["wtiempo_sala_recuperarcion"];
                                                                $datosGrabarCargos["wtiempo_uso_minutos"]        = $arr_datos_procedimiento["wtiempo_uso_minutos"];
                                                                $datosGrabarCargos["wtiempo_minutos_cx"]         = $arr_datos_procedimiento["wtiempo_minutos_cx"];

                                                                $datosGrabarCargos["codigo_especialidad"]        = "";
                                                                $datosGrabarCargos["wespecialidad_nombre"]       = "";
                                                                $datosGrabarCargos["wgrabar"]                    = "on";
                                                                $datosGrabarCargos['nitEmpresa']                 = $nitEmpresa;
                                                                $datosGrabarCargos['id_insumo']                  = $row["id_insumo"];
                                                                $datosGrabarCargos["wbaseliquidacion"]           = $wbaseliquidacion;
                                                                $datosGrabarCargos["arr_cambio_responsable"]     = array();
                                                                $datosGrabarCargos["arr_encabezado"]             = $arr_datos_procedimiento["arr_encabezado"];
                                                                // $datosGrabarCargos["ccoActualPac"]               = $ccoActualPac;

                                                                $datosGrabarCargos["es_insumo"]         = "on";


                                                                $userCargo = explode('-',$_SESSION['user']);
                                                                $wuse      = $userCargo[1];
                                                                global $wuse;
                                                                $condicion = CondicionMedicamento($cobro_insumo, $wcod_empresa, $wcentro_costo);
                                                                switch($condicion)
                                                                {
                                                                    //  P --> va para excedente (no lo cubre)
                                                                    //  N --> no facturable
                                                                    //  C --> lo cubre la entidad
                                                                    case "EXCEDENTE" :
                                                                        $datosGrabarCargos['wrecexc']     = 'E';
                                                                    break;
                                                                    case "NOFACTURABLE" :
                                                                        $datosGrabarCargos['wfacturable'] = "N";
                                                                        $arr_CargosGrabadosResponsables_insumos[$procedimiento_liquidar_cod_dif][$cobro_insumo]["datos_reales"]["wfacturable"] = "N";
                                                                    break;
                                                                }

                                                                // --> Si la empresa es particular esto se graba como excedente
                                                                if($wcod_empresa == $codEmpParticular)
                                                                    $datosGrabarCargos['wrecexc'] = 'E';

                                                                // --> Valor excedente
                                                                if($datosGrabarCargos['wrecexc'] == 'E')
                                                                    $datosGrabarCargos['wvaltarExce'] = round($wcantidad*($valor_final_simulado*1));
                                                                // --> Valor reconocido
                                                                else
                                                                    $datosGrabarCargos['wvaltarReco'] = round($wcantidad*($valor_final_simulado*1));

                                                                $userCargo = explode('-',$_SESSION['user']);
                                                                $wuse      = $userCargo[1];
                                                                global $wuse;

                                                                // No envío $cobro_insumo sino procedimiento_liquidar_cod_dif para poder asociar el medicamento al procedimiento original al que pertenece.
                                                                SimularGrabarCargo($conex, $wemp_pmla, $wbasedato, $user_session, $datosGrabarCargos, $concepto_cod_ppal, $procedimiento_liquidar_cod_dif);

                                                            }
                                                        }
                                                    }

                                                    // $valor_total_insumos += $info_tarifa;
                                                }
                                                /*$insumos_html .= '  <tr class="" >
                                                                        <td style="text-align:left;" >'.$row["codigo_insumo"]."-".$row["nombre_insumo"].'</td>
                                                                        <td style="text-align:right;" >'.$row["saldo_insumo"].'</td>
                                                                        <td>'.(($row["Artesm"]=='on') ? "[Medicamento]": "[Artículo]").'</td>
                                                                        <td>--</td>
                                                                        <td style="font-weight:bold;" >'.$info_tarifa.'</td>
                                                                    </tr>';*/
                                            }
                                        }
                                        $arr_mercado_completo_temp = array(); // Se inicializa el array del mercado para que no sea cobrado de nuevo, solo se muestre y se cobre el mercado una vez.
                                    }
                                }
                                // **********************************  BUSCAR Y SIMULAR INSUMOS ***********************************
                                // **********************************  BUSCAR Y SIMULAR INSUMOS ***********************************
                            }
                        }

                        //            print_r($arr_procedimientos_orden);
                        // $guardar = "arr_procedimientos_orden_liquidar :".print_r($arr_procedimientos_orden_liquidar,true).PHP_EOL;
                        // $guardar = print_r($arr_TOPES_ENTIDADES,true).PHP_EOL;
                        // $guardar = "arr_CargosGrabadosResponsables_insumos :".print_r($arr_CargosGrabadosResponsables_insumos,true).PHP_EOL;
                        // $guardar = "arr_CargosGrabadosResponsables: ".print_r($arr_CargosGrabadosResponsables,true).PHP_EOL;
                        // $guardar = "arr_CARGOS_PARA_GRABAR: ".print_r($arr_CARGOS_PARA_GRABAR,true).PHP_EOL;
                        // seguimiento($guardar);

                        // CONSULTAR LOS INSUMOS DE INVENTARIO QUE SE HAYA CONSUMIDO PARA CADA UNO DE LOS PROCEDIMIENTOS.
                        // print_r($arr_info_conceptos_inventario);
                        // print_r($arr_CargosGrabadosResponsables_insumos);
                        $cont_rowspan = 1;
                        // print_r($arr_procedimientos_orden_liquidar);
                        $valor_total_insumos = 0;
                        $suma_total_simulada = 0;
                        $arr_html_insumos_por_procedimiento = array();
                        $conteoProcedimientosPorCODIGO = array();
                        foreach ($arr_procedimientos_orden_liquidar as $codigo_procedimiento => $arr_info_procedimiento)
                        {
                            if(!array_key_exists($codigo_procedimiento, $arr_html_insumos_por_procedimiento))
                            {
                                $arr_html_insumos_por_procedimiento[$codigo_procedimiento] = array("html_medicamento"=>"","html_material"=>"", "sumatoria_material"=>0, "sumatoria_medicamento"=>0);
                            }

                            $html_insumos_consumidos = "";
                            $html_insumos_articulos = "";
                            if(array_key_exists($codigo_procedimiento, $arr_CargosGrabadosResponsables_insumos))
                            {
                                if(!array_key_exists($codigo_procedimiento, $arr_html_insumos_por_procedimiento))
                                {
                                    $arr_html_insumos_por_procedimiento[$codigo_procedimiento] = array("html_medicamento"=>"","html_material"=>"", "sumatoria_material"=>0, "sumatoria_medicamento"=>0);
                                }

                                $arr_insumos_mostrar = $arr_CargosGrabadosResponsables_insumos[$codigo_procedimiento];

                                if(!array_key_exists($codigo_procedimiento, $arr_datos_liquidados))
                                {
                                    $arr_datos_liquidados[$codigo_procedimiento] = array();
                                }
                                /*
                                [datos_reales] => Array
                                (
                                    [wcantidad] => 1
                                    [wprocedimiento] => 130777
                                    [wprocedimiento_nombre] => GASA TELA TEJIDA 5.0*5.0 MS
                                    [wconcepto] => 0626
                                    [wconcepto_nombre] => MATERIAL MEDICO
                                    [wtercero] =>
                                    [wtercero_nombre] =>
                                    [wespecialidad] =>
                                    [wespecialidad_nombre] =>
                                    [wporcentaje] => 0
                                    [wtipo_facturacion] =>
                                    [wvalor_final] => 0
                                    [wfacturable] =>
                                    [wgrabar] => on
                                    [wbaseliquidacion] => SOAT
                                    [wvalor] =>
                                    [wtipo_anestesia_cx] => 001
                                    [wtiempo_sala_recuperarcion] => 0
                                    [wtiempo_uso_minutos] => 0
                                    [wtiempo_minutos_cx] => 0
                                    [wexiste] =>
                                    [wserv] => H
                                    [mueve_inventario] => on
                                    [wes_medicamento] => off,
                                    [WTIENE_TARIFA] => off
                                )
                                 */
                                $css_cont = 0;
                                $cantidad_insumos = 1;
                                $insumos_html = "";
                                $insumos_html_articulo = "";

                                // Se eliminan las posiciones de conceptos relacionados a insumos para obligar a que sean adicionados de nuevo y queden al final del arreglo de conceptos del procedimiento actual
                                if(array_key_exists($concepto_medicamentos_mueven_inv, $arr_datos_liquidados[$codigo_procedimiento])) { unset($arr_datos_liquidados[$codigo_procedimiento][$concepto_medicamentos_mueven_inv]); }
                                if(array_key_exists($concepto_materiales_mueven_inv, $arr_datos_liquidados[$codigo_procedimiento])) { unset($arr_datos_liquidados[$codigo_procedimiento][$concepto_materiales_mueven_inv]); }
                                // $guardar = print_r($arr_insumos_mostrar,true).PHP_EOL;
                                // seguimiento($guardar);
                                foreach ($arr_insumos_mostrar as $codigo_insumo => $arr_datos_insumo)
                                {
                                    $datos_reales     = $arr_datos_insumo["datos_reales"];
                                    $datos_simulados  = $arr_datos_insumo["simulacion"];
                                    $concepto_insumos = $datos_reales["wconcepto"];
                                    $suma_simulada_total_responsables  = 0;
                                    $suma_simulada_total_TenerEnCuenta = 0;

                                    if($datos_reales["WTIENE_TARIFA"] == 'on')
                                    {
                                        if(!array_key_exists($concepto_insumos, $arr_datos_liquidados[$codigo_procedimiento]))
                                        {
                                            $arr_datos_liquidados[$codigo_procedimiento][$concepto_insumos] = array();
                                        }

                                        if(!array_key_exists($codigo_insumo, $arr_datos_liquidados[$codigo_procedimiento][$concepto_insumos]))
                                        {
                                            $arr_datos_liquidados[$codigo_procedimiento][$concepto_insumos][$codigo_insumo] = array();
                                        }

                                        $arr_datos_liquidados[$codigo_procedimiento][$concepto_insumos][$codigo_insumo] =
                                                array(  "wconcepto_nombre"           => $datos_reales["wconcepto_nombre"],
                                                        "id_insumo"                  => $datos_reales["id_insumo"],
                                                        "wcantidad"                  => $datos_reales["wcantidad"],
                                                        "wprocedimiento"             => $datos_reales["wprocedimiento"],
                                                        "wprocedimiento_nombre"      => $datos_reales["wprocedimiento_nombre"],
                                                        "wconcepto"                  => $concepto_insumos,
                                                        "wtercero"                   => "",
                                                        "wtercero_nombre"            => "",
                                                        "wespecialidad"              => "",
                                                        "cobraHonorarios"            => "on",
                                                        "wespecialidad_nombre"       => "",
                                                        "wporcentaje"                => $datos_reales["wporcentaje"],
                                                        "wvalor"                     => $datos_reales["wvalor"],
                                                        "wvalor_final"               => $datos_reales["wvalor_final"],
                                                        "wfacturable"                => $datos_reales["wfacturable"],
                                                        "wgrabar"                    => $datos_reales["wgrabar"],
                                                        "wbaseliquidacion"           => $datos_reales["wbaseliquidacion"],
                                                        "wtipo_facturacion"          => $datos_reales["wtipo_facturacion"],
                                                        "wtipo_anestesia_cx"         => $datos_reales["wtipo_anestesia_cx"],
                                                        "wtiempo_sala_recuperarcion" => $datos_reales["wtiempo_sala_recuperarcion"],
                                                        "wtiempo_uso_minutos"        => $datos_reales["wtiempo_uso_minutos"],
                                                        "wtiempo_minutos_cx"         => $datos_reales["wtiempo_minutos_cx"],
                                                        "wexiste"                    => $datos_reales["wexiste"],
                                                        "wserv"                      => $datos_reales['wserv'],
                                                        "mueve_inventario"           => $datos_reales['mueve_inventario']);
                                        // $arr_CARGOS_PARA_GRABAR[] = $arr_datos_liquidados[$codigo_procedimiento][$concepto_insumos][$codigo_insumo];
                                    }

                                    $html_responsables_insumos = "";
                                    $suma_excedentes = 0;
                                    $es_facturable = "N";
                                    foreach ($arr_entidades_responsables as $codigo_responsable => $info_responsable)
                                    {
                                        if(array_key_exists($codigo_responsable, $datos_simulados) && array_key_exists("valor_final_original", $datos_simulados[$codigo_responsable]))
                                        {
                                            $valor_final = $datos_simulados[$codigo_responsable]["valor_final_original"]*1; // Todos los reponsables simulados generados tienen el mismo valor_final_origial por eso no hay problema en tomar el valor de cualquiera de ellos
                                        }

                                        if(array_key_exists($codigo_responsable, $datos_simulados) && array_key_exists("wvalorTarifaBase", $datos_simulados[$codigo_responsable]))
                                        {
                                            $valor_tarifa = $datos_simulados[$codigo_responsable]["wvalorTarifaBase"]*1; // Todos los reponsables simulados generados tienen el mismo valor_final_origial por eso no hay problema en tomar el valor de cualquiera de ellos
                                        }

                                        if($codigo_responsable != $codEmpPartic)
                                        {
                                            if(array_key_exists($codigo_responsable, $datos_simulados) && ($datos_simulados[$codigo_responsable]["wfacturable"] == 'S'))
                                            {
                                                $es_facturable = "S";
                                            }

                                            $valor_cobrar       = 0;
                                            $valor_cobrar_total = 0;
                                            $valor_excedemte_total= 0;
                                            if(array_key_exists($codigo_responsable, $datos_simulados))
                                            {
                                                // if($datos_simulados[$codigo_responsable]["wfacturable"] == 'S')
                                                {
                                                    // $porcentaje_cxMult = $datos_simulados[$codigo_responsable]["porcentaje_cxMult_CARGO"]*1; // Todos los reponsables simulados generados tienen el mismo porcentaje_cxMult por eso no hay problema en tomar el valor de cualquiera de ellos
                                                    $valor_cobrar        = $datos_simulados[$codigo_responsable]["wvaltarReco"]*1;
                                                    $suma_excedentes     += $datos_simulados[$codigo_responsable]["wvaltarExce"]*1;
                                                    $valor_excedemte     = ($datos_simulados[$codigo_responsable]["wvaltarExce"]*1);
                                                    $suma_total_simulada += $valor_cobrar+$valor_excedemte;

                                                    $valor_cobrar_total = $valor_cobrar;
                                                    $valor_excedemte_total = $valor_excedemte;
                                                    if($datos_simulados[$codigo_responsable]["wfacturable"] == 'N')
                                                    {
                                                        $valor_cobrar_total = 0;
                                                        $valor_excedemte_total = 0;
                                                    }

                                                    $suma_simulada_total_TenerEnCuenta += $valor_cobrar_total+$valor_excedemte_total;
                                                    $suma_simulada_total_responsables  += $valor_cobrar+$valor_excedemte;

                                                }
                                            }

                                            $html_responsables_insumos .= number_format($valor_cobrar_total,CANTIDAD_DECIMALES).": ".utf8_encode($info_responsable["simulacion_campos"]['Empnom'])."<br>";
                                            // $html_responsables .= '<td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >'.utf8_encode($info_responsable["simulacion_campos"]['Empnom']).'</td>';
                                            // $html_valores_resp .= '<td class="" style="text-align:center;font-size:9pt;font-weight:bold;" >'.number_format($valor_cobrar,2).'</td>';
                                        }
                                    }
                                    // $html_responsables .= '<td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Excedente</td>';
                                    // $html_valores_resp .= '<td class="" style="text-align:center;font-size:9pt;font-weight:bold;" >'.number_format($suma_excedentes,2).'</td>';
                                    $html_responsables_insumos .= number_format($valor_excedemte_total,CANTIDAD_DECIMALES).": Excedente";


                                    $info_tarifa = "";
                                    $no_tiene_tarifa = "";
                                    if($datos_reales['WTIENE_TARIFA'] == "off")
                                    {
                                        $no_tiene_tarifa = "** NO TIENE TARIFA ** <br>--<br>";
                                        $info_tarifa = '<img style="cursor:pointer" id="" width="14" height="14" src="../../images/medical/sgc/Warning-32.png" />';
                                    }
                                    else
                                    {
                                        // print_r($datos_simulados);
                                        // $info_tarifa = number_format(($datos_reales["wvalor_final"]*1), 2);
                                        // $info_tarifa = $info_tarifa*($datos_reales["wcantidad"]*1);
                                        // $valor_total_insumos += $info_tarifa;
                                        $info_tarifa = number_format($suma_simulada_total_responsables,CANTIDAD_DECIMALES);
                                        if(array_key_exists($codigo_procedimiento, $arr_html_insumos_por_procedimiento))
                                        {
                                            if($datos_reales["wes_medicamento"]=='on')
                                            {
                                                $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["sumatoria_medicamento"] += $suma_simulada_total_TenerEnCuenta*1;
                                            }
                                            else
                                            {
                                                $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["sumatoria_material"] += $suma_simulada_total_TenerEnCuenta*1;
                                            }
                                        }
                                    }

                                    if(($datos_reales["wcantidad"]*1) > 0 && $datos_reales["wes_medicamento"]=='on')
                                    {
                                        $farturable_insumo = ($datos_reales['wfacturable'] == 'S') ? 'Si':'No';
                                        $css = ($css_cont % 2 == 0) ? "fila1": "fila2";
                                        $insumos_html .= '  <tr class="'.$css.'" >
                                                                <td style="font-size:7pt; text-align:left;" >'.$codigo_insumo."-".utf8_encode($datos_reales["wprocedimiento_nombre"]).'</td>
                                                                <td style="font-size:7pt; text-align:right;" >'.$datos_reales["wcantidad"].'</td>
                                                                <td style="font-size:7pt;" >'.$farturable_insumo.'</td>
                                                                <td style="font-weight:bold; text-align:right; font-size:7pt; " class="tooltip" title="'.$no_tiene_tarifa.$html_responsables_insumos.'" >'.$info_tarifa.'</td>
                                                            </tr>';
                                        $css_cont++;
                                        $cantidad_insumos++;
                                    }
                                    elseif(($datos_reales["wcantidad"]*1) > 0)
                                    {
                                        $farturable_insumo = ($datos_reales['wfacturable'] == 'S') ? 'Si':'No';
                                        $css = ($css_cont % 2 == 0) ? "fila1": "fila2";
                                        $insumos_html_articulo .= '  <tr class="'.$css.'" >
                                                                <td style="font-size:7pt; text-align:left;" >'.$codigo_insumo."-".utf8_encode($datos_reales["wprocedimiento_nombre"]).'</td>
                                                                <td style="font-size:7pt; text-align:right;" >'.$datos_reales["wcantidad"].'</td>
                                                                <td style="font-size:7pt;" >'.$farturable_insumo.'</td>
                                                                <td style="font-weight:bold; text-align:right; font-size:7pt; " class="tooltip" title="'.$no_tiene_tarifa.$html_responsables_insumos.'" >'.$info_tarifa.'</td>
                                                            </tr>';
                                        $css_cont++;
                                        $cantidad_insumos++;
                                    }
                                }
                                $img_tlp = '<img border=\'0\' width=\'10\' height=\'10\' style=\'display:;\' src=\'../../images/medical/root/info.png\' > '.utf8_encode($arr_info_procedimiento['nombre']);
                                // $css_rowspan = ($cont_rowspan % 2 == 0) ? "fila1": "fila2";
                                $html_insumos_consumidos .= $insumos_html;
                                $html_insumos_articulos .= $insumos_html_articulo;
                                // $cont_rowspan++;
                            }

                            $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["html_medicamento"] = "";
                            $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["html_material"] = "";
                            if(!empty($html_insumos_consumidos))
                            {
                                $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["html_medicamento"] = '<table style="width:100%;">
                                                                                                <tr class="encabezadoTabla" >
                                                                                                    <td>Nombre</td>
                                                                                                    <td>Cantidad</td>
                                                                                                    <td>Facturable</td>
                                                                                                    <td>Valor</td>
                                                                                                </tr>
                                                                                                '.$html_insumos_consumidos.'
                                                                                                <!-- <tr>
                                                                                                    <td>&nbsp;</td>
                                                                                                    <td>&nbsp;</td>
                                                                                                    <td>&nbsp;</td>
                                                                                                    <td style="text-align:right;" >Subtotal</td>
                                                                                                    <td class="encabezadoTabla" style="text-align:right;" >'.number_format($arr_html_insumos_por_procedimiento[$codigo_procedimiento]["sumatoria_medicamento"],CANTIDAD_DECIMALES).'</td>
                                                                                                </tr> -->
                                                                                            </table>';
                            }

                            if(!empty($html_insumos_articulos))
                            {
                                $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["html_material"] = '<table style="width:100%;">
                                                                                                <tr class="encabezadoTabla" >
                                                                                                    <td>Nombre</td>
                                                                                                    <td>Cantidad</td>
                                                                                                    <td>Facturable</td>
                                                                                                    <td>Valor</td>
                                                                                                </tr>
                                                                                                '.$html_insumos_articulos.'
                                                                                                <!-- <tr>
                                                                                                    <td>&nbsp;</td>
                                                                                                    <td>&nbsp;</td>
                                                                                                    <td>&nbsp;</td>
                                                                                                    <td style="text-align:right;" >Subtotal</td>
                                                                                                    <td class="encabezadoTabla" style="text-align:right;" >'.number_format($arr_html_insumos_por_procedimiento[$codigo_procedimiento]["sumatoria_material"],CANTIDAD_DECIMALES).'</td>
                                                                                                </tr> -->
                                                                                            </table>';
                            }
                        }

                        $div_caja_flotante_insumos = "";

                        $arr_encabezados_proced = array("colspan_nombre"=>array(),"columnas_valores"=>array(),"columnas_insumos"=>array(),"columnas_materiales"=>array());
                        $arr_concepto_proced = array();

                        foreach ($arr_lista_conceptos as $codigo_concepto => $nombre_concepto)
                        {
                            $fila_html = '
                                            <tr>
                                                <td class="encabezadoTabla" style="text-align:left;" >'.$codigo_concepto.'-'.$nombre_concepto.'</td>';

                            $contador_spn_td = 0;
                            foreach ($arr_procedimientos_orden as $wcodigo_especialidad_orden => $arr_procedimientos_orden_por_especialidad)
                            {
                                $arr_procedimientos_bilaterales = array();
                                $arr_procedimientos_NObilaterales = array();
                                foreach ($arr_procedimientos_orden_por_especialidad as $procedimiento_liquidar_cod => $num_puntos)
                                {
                                    if(!array_key_exists($procedimiento_liquidar_cod, $html_tds_TOTALES))
                                    {
                                        $html_tds_TOTALES[$procedimiento_liquidar_cod] = 0;
                                    }

                                    $tds_encabezado = '
                                            <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >'.TERCERO_LABEL.'</td>
                                            <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Especialidad</td>
                                            <!-- <td class="encabezadoTabla" >Modalidad</td> -->
                                            <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >%Liq.</td>
                                            <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Valor</td>
                                            <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Total</td>
                                            <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Facturable</td>
                                            <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Grabar</td>';
                                    $colspan = 7;
                                    $css_span_td = ($contador_spn_td % 2 == 0) ? 'fila1': 'fila2';

                                    $arr_datos_procedimiento = $arr_procedimientos_liquidar[$procedimiento_liquidar_cod];

                                    // incluye en array de liquidados un procedimiento a liquidar
                                    if(!array_key_exists($procedimiento_liquidar_cod, $arr_datos_liquidados))
                                    {
                                        $arr_datos_liquidados[$procedimiento_liquidar_cod] = array();
                                    }

                                    $codigo_procedimiento = $arr_datos_procedimiento['procedimiento_dif'];
                                    $codigo_procedimiento_consult = arreglar_procedimientos_bilaterales($codigo_procedimiento);

                                    // Si no es paquete entonces se pueden buscar los datos específicos para un procedimiento
                                    // Si es paquete vasta con especificar que la tarifa es PAQUETE.
                                    if(!$es_paquete)
                                    {
                                        // $sql = "SELECT  proc.Procod AS procedimiento_cod,  proc.Pronom AS procedimiento_nom, proc.Protfa AS procedimiento_tarifa, proc.Progqx, proc.Propun as procedimiento_puntos
                                        //         FROM    {$wbasedato}_000103 AS proc
                                        //         WHERE   proc.Procod = '{$codigo_procedimiento_consult}'
                                        //                 AND proc.Proest = 'on'";
                                        // $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                                        // $rowPro = mysql_fetch_array($result);
                                        $arr_mod_procedimiento = modalidadPuntosProcedimiento($conex, $wbasedato, $codigo_procedimiento_consult, $arr_primero_segundo_responsable["primer_responsable"]["codigo"], $arr_primero_segundo_responsable["primer_responsable"]["tipoEmpresa"], $wcentro_costo);

                                        $modalidad_facturacion = $arr_mod_procedimiento['tipo_facturacion'];
                                        if($modalidad_facturacion == 'UVR')
                                        {
                                            $wnumero_puntos = $arr_mod_procedimiento['wnumero_uvrs']*1;
                                        }
                                        elseif($modalidad_facturacion == 'GQX')
                                        {
                                            // Consulta el valor del grupo
                                            $valor_grupo = $arr_mod_procedimiento['valor_grupo'];
                                            // $wnumero_puntos = $valor_grupo*1;
                                        }

                                        $rowPro = array("procedimiento_tarifa"=>$modalidad_facturacion);
                                    }
                                    else
                                    {
                                        $rowPro = array("procedimiento_tarifa"=>"PAQUETE");
                                    }

                                    // // Esta sección de código es para intentar mantener el valor digitado original de los conceptos que permiten escribir valor
                                    $valorOriginalDigitado = '';
                                    if(array_key_exists($codigo_procedimiento, $arr_datos_liquidados)
                                        && array_key_exists($codigo_concepto, $arr_datos_liquidados[$codigo_procedimiento]))
                                    {
                                        if(array_key_exists("wvalor_digitado", $arr_datos_liquidados[$codigo_procedimiento][$codigo_concepto]))
                                        {
                                            $valorOriginalDigitado = $arr_datos_liquidados[$codigo_procedimiento][$codigo_concepto]['wvalor_digitado'];
                                        }
                                    }

                                    $arr_especialistas_proced = $arr_datos_procedimiento["especialistas"];
                                    // Columna donde va el nombre del procedimiento

                                    $mostrar_puntos = 0;
                                    if(!array_key_exists($procedimiento_liquidar_cod, $arr_encabezados_proced['colspan_nombre']))
                                    {
                                        // $fp = fopen("seguimiento.txt","a+");
                                        // $guardar = print_r($arr_datos_procedimiento,true).PHP_EOL;
                                        // fwrite($fp, $guardar);
                                        // fclose($fp);

                                        $mostrar_puntos = $arr_datos_procedimiento['wnumero_puntos'];
                                        if($arr_datos_procedimiento["modalidad_facturacion"] == "GQX")
                                        {
                                            $mostrar_puntos = $arr_datos_procedimiento["wgrupo_gqx"]; //.'('.$mostrar_puntos.')'
                                        }

                                        if($arr_datos_procedimiento["modalidad_facturacion"] == "CODIGO")
                                        {
                                            if(!array_key_exists($codigo_procedimiento, $conteoProcedimientosPorCODIGO))
                                            {
                                                $conteoProcedimientosPorCODIGO[$codigo_procedimiento] = $codigo_procedimiento;
                                                $mostrar_puntos = count($conteoProcedimientosPorCODIGO);
                                            }
                                        }

                                        $toolTip_bilat = "";
                                        if(!empty($arr_datos_procedimiento['wposicion_organo_nom']))
                                        {
                                            $toolTip_bilat = "[posición organo: ".$arr_datos_procedimiento['wposicion_organo_nom']."]";
                                        }

                                        $html_insumos              = "NO HAY MEDICAMENTOS PARA ESTE PROCEDIMIENTO";
                                        $html_materiales           = "NO HAY MATERIALES PARA ESTE PROCEDIMIENTO";
                                        $suma_subtotal_medicamento = number_format(0,CANTIDAD_DECIMALES);
                                        $suma_subtotal_material    = number_format(0,CANTIDAD_DECIMALES);
                                        if(array_key_exists($codigo_procedimiento, $arr_html_insumos_por_procedimiento) && !empty($arr_html_insumos_por_procedimiento[$codigo_procedimiento]["html_medicamento"]))
                                        {
                                            $html_insumos              = $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["html_medicamento"];
                                            $suma_subtotal_medicamento = number_format($arr_html_insumos_por_procedimiento[$codigo_procedimiento]["sumatoria_medicamento"],CANTIDAD_DECIMALES);
                                        }

                                        if(array_key_exists($codigo_procedimiento, $arr_html_insumos_por_procedimiento) && !empty($arr_html_insumos_por_procedimiento[$codigo_procedimiento]["html_material"]))
                                        {
                                            $html_materiales        = $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["html_material"];
                                            $suma_subtotal_material = number_format($arr_html_insumos_por_procedimiento[$codigo_procedimiento]["sumatoria_material"],CANTIDAD_DECIMALES);
                                        }

                                        // echo $procedimiento_liquidar_cod.'|';
                                        $arr_encabezados_proced['colspan_nombre'][$procedimiento_liquidar_cod] = '<td colspan="'.$colspan.'" class="'.$css_span_td.' tooltip" title="'.$toolTip_bilat.'" style="text-align:left;font-size:10pt;font-weight:bold;" >['.$rowPro['procedimiento_tarifa'].':'.$mostrar_puntos.'] [Vía : '.$arr_datos_procedimiento['wvia'].'] Procedimiento: '.$arr_datos_procedimiento['nombre'].'</td>';
                                        $arr_encabezados_proced['columnas_insumos'][$procedimiento_liquidar_cod] = '<td colspan="'.$colspan.'" class="'.$css_span_td.'" style="text-align:center;font-size:10pt;font-weight:bold;" valign="top" ><div class="ver_insumos" style="display:none;" >'.$html_insumos.'</div><div align="right" style="float: right; width:100%;text-align:right;font-weight:bold;font-size:10pt;">Medicamentos Subtotal: <div class="encabezadoTabla" style="float: right; width:100px;display:inline;">'.$suma_subtotal_medicamento.'</div></div></td>';
                                        $arr_encabezados_proced['columnas_materiales'][$procedimiento_liquidar_cod] = '<td colspan="'.$colspan.'" class="'.$css_span_td.'" style="text-align:center;font-size:10pt;font-weight:bold;" valign="top" ><div class="ver_materiales" style="display:none;" >'.$html_materiales.'</div><div align="right" style="float: right; width:100%;text-align:right;font-weight:bold;font-size:10pt;">Materiales Subtotal: <div class="encabezadoTabla" style="float: right; width:100px;display:inline;">'.$suma_subtotal_material.'</div></div></td>';
                                        $tds_encabezado = str_replace("CSS_REPLACE", $css_span_td, $tds_encabezado);
                                        $arr_encabezados_proced['columnas_valores'][$procedimiento_liquidar_cod] = $tds_encabezado;
                                    }


                                    $sbtotal = 0;
                                    $css_count = 0;
                                    // foreach ($arr_datos_procedimiento['configuracion_liquidar'] as $concepto_cod_ppal => $arr_info_conf)
                                    if(array_key_exists($codigo_concepto, $arr_datos_procedimiento['configuracion_liquidar']))
                                    {
                                        $arr_info_conf = $arr_datos_procedimiento['configuracion_liquidar'][$codigo_concepto];
                                        $concepto_cod_ppal = $codigo_concepto;

                                        $requiere_tercero     = $arr_info_conf['requiere_tercero']; // Si es "C" o "P" entonces requiere tercero
                                        $cantidad_pintar      = $arr_info_conf['cantidad_concepto'];
                                        $tabla_valida_precios = $arr_info_conf['tabla_valida_precios'];
                                        $modifica_valor       = $arr_info_conf['modifica_valor'];
                                        $mueve_inventario     = $arr_info_conf['mueve_inventario'];
                                        $tipo_cobro           = $arr_info_conf['tipo_cobro'];
                                        $es_concepto_extra    = (array_key_exists('es_concepto_extra', $arr_info_conf)) ? true: false;
                                        $cobro_concepto       = "";
                                        $cobro_procedimiento  = "";

                                        if($mueve_inventario != 'on')
                                        {
                                            $cont_pint = 0;
                                            // print_r($arr_CargosGrabadosResponsables);
                                            if(array_key_exists($codigo_procedimiento, $arr_CargosGrabadosResponsables) && array_key_exists($concepto_cod_ppal, $arr_CargosGrabadosResponsables[$codigo_procedimiento]))
                                            {
                                                // $guardar = "codigo_procedimiento:$codigo_procedimiento concepto_cod_ppal:$concepto_cod_ppal ".print_r($arr_CargosGrabadosResponsables,true).PHP_EOL.PHP_EOL;
                                                // seguimiento($guardar);
                                                $arr_CargosGrabadosResponsablesDetallado = array();
                                                $arr_equipos_y_examenes_temp = array();
                                                $html_equipos_examenes = "";
                                                if($concepto_cod_ppal == $id_concepto_uso_equipos || $concepto_cod_ppal== $id_concepto_examenes)
                                                {
                                                    // Array
                                                    // (
                                                    //     [wconcepto_nombre] => USO DE EQUIPOS
                                                    //     [wcantidad] => 1
                                                    //     [wprocedimiento] => 990073
                                                    //     [wprocedimiento_nombre] => 990073-NEURONAVEGADOR
                                                    //     [wconcepto] => 0034
                                                    //     [wtercero] =>
                                                    //     [wtercero_nombre] =>
                                                    //     [wespecialidad] =>
                                                    //     [wespecialidad_nombre] =>
                                                    //     [wporcentaje] => 0
                                                    //     [wtipo_facturacion] =>
                                                    //     [wvalor_final] => 0
                                                    //     [wgrabar] => on
                                                    //     [wvalor] =>
                                                    //     [wtipo_anestesia_cx] => 001
                                                    //     [wtiempo_sala_recuperarcion] => 0
                                                    //     [wtiempo_uso_minutos] => 0
                                                    //     [wtiempo_minutos_cx] => 0
                                                    //     [wexiste] =>
                                                    //     [mueve_inventario] => off
                                                    //     [porcentaje_cxMult] => 0
                                                    //     [wvaltarReco] => 0
                                                    //     [wvaltarExce] => 0
                                                    //     [wvalorTarifaBase] => 0
                                                    //     [WTIENE_TARIFA] => off
                                                    // )

                                                    $arr_equipos_y_examenes_temp = $arr_CargosGrabadosResponsables[$codigo_procedimiento][$concepto_cod_ppal];
                                                    $contetemp = 0;
                                                    // $arr_CargosGrabadosResponsablesDetallado = $arr_CargosGrabadosResponsables[$codigo_procedimiento][$concepto_cod_ppal];
                                                    $html_equipos_examenes = '
                                                                        <table style="width:100%">
                                                                            <tr >
                                                                                <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Nombre</td>
                                                                                <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >%Liq.</td>
                                                                                <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Valor</td>
                                                                                <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Total</td>
                                                                                <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Facturable</td>
                                                                                <td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Grabar</td>
                                                                            </tr>';
                                                    // $guardar = "arr_equipos_y_examenes_temp: ".print_r($arr_equipos_y_examenes_temp,true).PHP_EOL;
                                                    // seguimiento($guardar);
                                                    foreach ($arr_equipos_y_examenes_temp as $codigo_equipo_examen_temp => $lista_examen_equipo)
                                                    {
                                                        foreach ($lista_examen_equipo as $codigo_empresa_responsable => $arr_cargo)
                                                        {
                                                            // $codigo_equipo_examen_temp = $arr_equipos_examenes['wprocedimiento'];
                                                            if(!array_key_exists($concepto_cod_ppal, $arr_datos_liquidados[$procedimiento_liquidar_cod]))
                                                            {
                                                                $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal] = array();
                                                            }

                                                            // $codigo_equipo_examen_temp = $codigo_cargo; //$codigo_cargo debería ser el código de un examen o equipo

                                                            // if(!array_key_exists($codigo_equipo_examen_temp, $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal]))
                                                            // {
                                                            //     $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]= array();
                                                            // }

                                                            $temp_codigo_wtercero = "";
                                                            $temp_nombre_wtercero = "";
                                                            $temp_wespecialidad_ = "";
                                                            $temp_wespecialidad_nombre_ = "";

                                                            if(array_key_exists($procedimiento_liquidar_cod, $arr_datos_liquidados))
                                                            {
                                                                // if(array_key_exists($concepto_cod_ppal, $arr_datos_liquidados[$procedimiento_liquidar_cod])
                                                                //     // && array_key_exists $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal])
                                                                //     && array_key_exists($codigo_equipo_examen_temp, $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal])
                                                                //     && array_key_exists('wtercero', $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]))
                                                                {

                                                                    // $guardar = print_r($arr_datos_liquidados,true).PHP_EOL;
                                                                    // seguimiento($guardar);
                                                                    // $option_especialidad = '<option value="'.$arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal]['wespecialidad'].'" selected="selected">
                                                                    //                             '.$arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal]['wespecialidad_nombre'].'
                                                                    //                         </option>';

                                                                    // AQUI SE DEBE REVISAR COMO RECUPERAR DATOS GENERALES QUE HAYAN SIDO LIQUIDADOS.
                                                                    // $temp_codigo_wtercero       = $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wtercero'];
                                                                    // $temp_nombre_wtercero       = $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wtercero_nombre'];
                                                                    // $temp_wespecialidad_        = $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wespecialidad'];
                                                                    // $temp_wespecialidad_nombre_ = $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wespecialidad_nombre'];
                                                                }
                                                            }

                                                            $dif_fila_exam_equip = $codigo_procedimiento.'_'.$concepto_cod_ppal.'_'.$codigo_equipo_examen_temp;//.'_'.$cont_pint;

                                                            // MOSTRAR LOS RESPONSABLES QUE LES CORRESPONDE PAGAR EL CARGO DE EQUIPO O EXÁMEN
                                                            $html_responsables_exam_equip = '';
                                                            $html_valores_resp_exam_equip = '';
                                                            $suma_excedentes_exam_equip = 0;
                                                            $total_responsables_mayor_a_cero_exam_equip = 0;
                                                            $otra_tarifa_exam_equip = 0;
                                                            $total_responsables_cobro_exam_equip = 0;// Sumatoria de los valores de los responsables;
                                                            // print_r($arr_entidades_responsables);
                                                            $porcentaje_cxMult = 0;
                                                            foreach ($arr_entidades_responsables as $codigo_responsable => $info_responsable)
                                                            {
                                                                if($codigo_responsable != $codEmpPartic)
                                                                {
                                                                    $valor_cobrar = 0;
                                                                    $porcentaje_cxMult_exam_equip = 0;

                                                                    if( array_key_exists($codigo_responsable, $lista_examen_equipo)
                                                                        // && array_key_exists($codigo_responsable, $lista_examen_equipo[$codigo_equipo_examen_temp])
                                                                        && ($concepto_cod_ppal == $id_concepto_uso_equipos || $concepto_cod_ppal == $id_concepto_examenes))
                                                                    {
                                                                        $arr_cargo_resp = $lista_examen_equipo[$codigo_responsable];
                                                                        $porcentaje_cxMult_exam_equip        = $arr_cargo_resp["porcentaje_cxMult_CARGO"]*1; // Todos los responsables simulados generados tienen el mismo porcentaje_cxMult por eso no hay problema en tomar el valor de cualquiera de ellos
                                                                        $valor_cobrar             = $arr_cargo_resp["wvaltarReco"]*1;
                                                                        $suma_excedentes_exam_equip          += $arr_cargo_resp["wvaltarExce"]*1;
                                                                        $total_responsables_cobro_exam_equip += ($arr_cargo_resp["wvaltarExce"]*1)+$valor_cobrar;

                                                                        $otra_tarifa_exam_equip = $arr_cargo_resp["wvalorTarifaBase"]*1;
                                                                    }

                                                                    $html_responsables_exam_equip .= '<td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >'.utf8_encode($info_responsable["simulacion_campos"]['Empnom']).'</td>';
                                                                    $html_valores_resp_exam_equip .= '<td class="" style="text-align:center;font-size:9pt;font-weight:bold;" >('.$porcentaje_cxMult_exam_equip.'%) '.number_format($valor_cobrar,CANTIDAD_DECIMALES).'</td>';
                                                                    if($valor_cobrar > 0 && $suma_excedentes_exam_equip > 0) //Si hay valor a cobrar del responsable y adicionalmente hay excedente
                                                                    {
                                                                        $total_responsables_mayor_a_cero_exam_equip += 2;
                                                                    }
                                                                    elseif($valor_cobrar > 0)
                                                                    {
                                                                        $total_responsables_mayor_a_cero_exam_equip += 1;
                                                                    }
                                                                    elseif($suma_excedentes_exam_equip > 0)
                                                                    {
                                                                        $total_responsables_mayor_a_cero_exam_equip += 1;
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    // $suma_excedentes_exam_equip += $arr_cargo["wvaltarExce"]*1;
                                                                }
                                                            }
                                                            $html_responsables_exam_equip .= '<td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Excedente</td>';
                                                            $html_valores_resp_exam_equip .= '<td class="" style="text-align:center;font-size:9pt;font-weight:bold;" >'.number_format($suma_excedentes_exam_equip,CANTIDAD_DECIMALES).'</td>';
                                                            // echo $total_responsables_mayor_a_cero_exam_equip."|";
                                                            // echo "{ $procedimiento_liquidar_cod : $concepto_cod_ppal [$valor_tarifa]}";
                                                            $html_tds_TOTALES[$procedimiento_liquidar_cod] = ($html_tds_TOTALES[$procedimiento_liquidar_cod]*1)+$total_responsables_cobro_exam_equip;

                                                            // $inputs_valores = $valor_tarifa;
                                                            // Si es solo un responsable, se conserva entonces el valor del cargo a tarifa de ese responsable, es posible que el cargo se haya originado
                                                            // con un responsable pero por no tener tope entonces se le cobre al responsable siguiente a tarifa de ese nuevo responsable.
                                                            if($total_responsables_mayor_a_cero_exam_equip == 1)
                                                            {
                                                            }
                                                            $valor_final_simulado_exam_equip = $total_responsables_cobro_exam_equip;
                                                            $inputs_valores = $otra_tarifa_exam_equip;

                                                                                // flotante '.$dif_fila.'
                                                            $div_caja_flotante = '
                                                                            <div id="caja_flotante_'.$dif_fila_exam_equip.'" style="display:none; background-color: #FFFEE2; padding: 5px;" class="caja_flotante" >
                                                                                <div style="width:100%;text-align:left;" ><span style="font-weight:bold;color:red;" >^</span> <span class="alinear_derecha" style="width: 20px; cursor:pointer;" title="Cerrar" ><img src="../../images/medical/eliminar1.png" alt="Cerrar" onclick="cerrarFlotante();" /></span></div>
                                                                                <table>
                                                                                    <tr>
                                                                                        '.$html_responsables_exam_equip.'
                                                                                    </tr>
                                                                                    <tr class="fila1" >
                                                                                        '.$html_valores_resp_exam_equip.'
                                                                                    </tr>
                                                                                </table>
                                                                            </div>';

                                                            // $campos_localizacion_js = 'wconsec="'.$cont_pint.'" wprocedimiento="'.$codigo_procedimiento.'" wconcepto ="'.$concepto_cod_ppal.'"';
                                                            $campos_localizacion_js_exam_equip = ' wconsec="'.$cont_pint.'" wprocedimiento="'.$codigo_procedimiento.'" wconcepto ="'.$concepto_cod_ppal.'" wequip_examen ="'.$codigo_equipo_examen_temp.'" ';

                                                            $vlr_tarifa_temp = "";
                                                            $cheched_grabar_equip_exam = 'checked="checked"';
                                                            $select_fac_equip_exam = '';
                                                            $selected_factur_eqip_exam_ON = 'selected="selected"';
                                                            $selected_factur_equip_exam_OFF = '';

                                                            if($arr_cargo['WTIENE_TARIFA'] == 'off')
                                                            {
                                                                $vlr_tarifa_temp = '<img style="cursor:pointer" id="" class="tooltip" title="NO TIENE TARIFA" width="16" height="16" src="../../images/medical/sgc/Warning-32.png" /> ';

                                                                $selected_factur_eqip_exam_ON  = '';
                                                                $selected_factur_equip_exam_OFF = 'selected="selected"';

                                                                $cheched_grabar_equip_exam = 'disabled="disabled"';
                                                                $select_fac_equip_exam = 'disabled="disabled"';
                                                                // $inputs_valores = '
                                                                //         <input '.$campos_localizacion_js_exam_equip.' type="text" id="wvalor_'.$dif_fila.'" name="wvalor_'.$dif_fila.'" etiqueta="wvalor" value="" placeholder="$ valor" size="10" style="text-align:right;" class="numerico requerido" >';
                                                            }

                                                            $facturable_temp = "N";
                                                            if($arr_cargo['WTIENE_TARIFA'] == 'on')
                                                            {
                                                                $facturable_temp = $arr_cargo['wfacturable'];
                                                            }
                                                            $se_graba_temp = ($arr_cargo['WTIENE_TARIFA'] == 'on') ? 'on': 'off';

                                                            // $guardar = print_r($codigo_equipo_examen_temp,true).PHP_EOL;
                                                            // seguimiento($guardar);
                                                            // si no existe, cree un array e ingrese unos datos básicos
                                                            // y cree una posición de empresas que se hacen cargo de pagar el cargo
                                                            if(array_key_exists($codigo_equipo_examen_temp, $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal]))
                                                            {
                                                                // $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp] = array();
                                                                // $datos_equipo_examen = array("nombre_examen_equipo"=>$arr_cargo['examen_equipo_nombre'],"codigo_examen_equipo"=>$arr_cargo['examen_equipo_codigo'], "responsables_cargo" => array());
                                                                // $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]["nombre_examen_equipo"]= $arr_cargo['examen_equipo_nombre'];
                                                                // $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]["codigo_examen_equipo"]= $arr_cargo['examen_equipo_codigo'];
                                                                // $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]["responsables_cargo"]= array();
                                                            }

                                                            // if(array_key_exists("responsables_cargo", $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp])
                                                            //     && !array_key_exists($codigo_empresa_responsable, $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]["responsables_cargo"]))
                                                            // {
                                                            //     $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]["responsables_cargo"][$codigo_empresa_responsable] = array();
                                                            // }
                                                            // $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]["responsables_cargo"][$codigo_empresa_responsable] =

                                                            if(!empty($arr_cargo['examen_equipo_codigo']) && !array_key_exists($codigo_equipo_examen_temp, $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal]))
                                                            {
                                                                $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp] =
                                                                                                                            array("wconcepto_nombre"         => $arr_cargo['wnomcon'],
                                                                                                                                "id_insumo"                  => "", // Si tiene valor es porque es un medicamento o material
                                                                                                                                "wcantidad"                  => 1,
                                                                                                                                "nombre_examen_equipo"       => $arr_cargo['examen_equipo_nombre'],
                                                                                                                                "codigo_examen_equipo"       => $arr_cargo['examen_equipo_codigo'],
                                                                                                                                "wprocedimiento"             => $arr_cargo['examen_equipo_codigo'],
                                                                                                                                "wprocedimiento_nombre"      => $arr_cargo['examen_equipo_nombre'],
                                                                                                                                "wconcepto"                  => $arr_cargo['wcodcon'],
                                                                                                                                "wtercero"                   => $temp_codigo_wtercero,
                                                                                                                                "wtercero_nombre"            => $temp_nombre_wtercero,
                                                                                                                                "wespecialidad"              => $temp_wespecialidad_,
                                                                                                                                "cobraHonorarios"            => "on",
                                                                                                                                "wespecialidad_nombre"       => $temp_wespecialidad_nombre_,
                                                                                                                                "wporcentaje"                => $arr_cargo['porcentaje_cxMult_CARGO'],//$porcentaje_cxMult_exam_equip,
                                                                                                                                "wvalor"                     => $arr_cargo['valor_final_original'],
                                                                                                                                "wvalor_final"               => $arr_cargo['valor_final_original'],
                                                                                                                                "wfacturable"                => $facturable_temp,
                                                                                                                                "wgrabar"                    => $se_graba_temp,
                                                                                                                                "wbaseliquidacion"           => "", // PUEDE HACER FALTA!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!  REVISAR DE DONDE PUEDE SALIR ESTE VALOR CUANDO SE HAGA LA SIMULACIÓN
                                                                                                                                "wtipo_facturacion"          => $arr_info_conf['procedimiento_tarifa'],
                                                                                                                                "wtipo_anestesia_cx"         => $arr_datos_procedimiento["wtipo_anestesia_cx"],
                                                                                                                                "wtiempo_sala_recuperarcion" => $arr_datos_procedimiento["wtiempo_sala_recuperarcion"],
                                                                                                                                "wtiempo_uso_minutos"        => $arr_datos_procedimiento["wtiempo_uso_minutos"],
                                                                                                                                "wtiempo_minutos_cx"         => $arr_datos_procedimiento["wtiempo_minutos_cx"],
                                                                                                                                "wexiste"                    => "",
                                                                                                                                "wserv"                      => $arr_info_conf['wserv'],
                                                                                                                                "mueve_inventario"           => $arr_info_conf['mueve_inventario']);

                                                            }

                                                            $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wfacturable'] = $facturable_temp;
                                                            // Si existe en el de liquidados entonces inicializa los datos con esos.
                                                            // echo "arr_datos_liquidados [ $procedimiento_liquidar_cod ][$concepto_cod_ppal]";
                                                            if(array_key_exists($procedimiento_liquidar_cod, $arr_datos_liquidados))
                                                            {
                                                                if( array_key_exists($concepto_cod_ppal, $arr_datos_liquidados[$procedimiento_liquidar_cod])
                                                                    && array_key_exists($codigo_equipo_examen_temp, $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal])
                                                                    && $concepto_cod_ppal == $id_concepto_uso_equipos || $concepto_cod_ppal == $id_concepto_examenes)
                                                                {
                                                                    $selected_factur_eqip_exam_ON   = ($arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wfacturable'] == 'S') ? 'selected="selected"': '';
                                                                    $selected_factur_equip_exam_OFF = ($arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wfacturable'] == 'N') ? 'selected="selected"': '';
                                                                    $cheched_grabar_equip_exam      = ($arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wgrabar'] == 'on') ? 'checked="checked"': '';
                                                                    $cheched_grabar_equip_exam      = ($vlr_tarifa_temp != '') ? 'disabled="disabled"': $cheched_grabar_equip_exam;
                                                                }
                                                            }

                                                            if(array_key_exists($procedimiento_liquidar_cod, $arr_CARGOS_PARA_GRABAR) && array_key_exists($concepto_cod_ppal, $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod]))
                                                            {
                                                                $editar_cargos = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$concepto_cod_ppal];
                                                                foreach ($editar_cargos as $secuencia => $cargo_editar)
                                                                {
                                                                    if($cargo_editar["examen_equipo_codigo"] == $codigo_equipo_examen_temp)
                                                                    {
                                                                        $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$concepto_cod_ppal][$secuencia]["wfacturable"] = $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wfacturable'];
                                                                        $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$concepto_cod_ppal][$secuencia]["wgrabar"]     = $arr_datos_liquidados[$procedimiento_liquidar_cod][$concepto_cod_ppal][$codigo_equipo_examen_temp]['wgrabar'];
                                                                    }
                                                                }
                                                            }

                                                            // Recorres posiblemente el array resultante para extraer los equipos y examenes
                                                            // $css_temp = ($contetemp % 2 == 0) ? 'fila1': 'fila2';

                                                            $html_equipos_examenes .= '
                                                                                 <tr>
                                                                                    <td class="" style="text-align:justify;font-size:9pt;font-weight:bold;" >'.$arr_cargo['examen_equipo_nombre'].'</td>
                                                                                    <td class="" style="text-align:right;font-size:9pt;" >'.$porcentaje_cxMult_exam_equip.'%</td>
                                                                                    <td class="" style="text-align:right;font-size:9pt;" >'.number_format($inputs_valores,CANTIDAD_DECIMALES).'</td>
                                                                                    <td class="" style="text-align:right;font-size:9pt;font-weight:bold;" >
                                                                                        '.$div_caja_flotante.'
                                                                                        <a class="tooltip" title="Click para ver detalle" href="javascript:" onclick="posicionElemento(this);" dif_fila="'.$dif_fila_exam_equip.'" >'.number_format($valor_final_simulado_exam_equip, CANTIDAD_DECIMALES).'</a>
                                                                                    </td>
                                                                                    <td class="" style="text-align:center;font-size:9pt;" >
                                                                                        <select disabled="disabled" id="wfacturable_'.$dif_fila_exam_equip.'" name="wfacturable_'.$dif_fila_exam_equip.'" etiqueta="wfacturable" class="modificaLiquidado" '.$select_fac_equip_exam.' '.$campos_localizacion_js_exam_equip.' >
                                                                                            <option value="S" '.$selected_factur_eqip_exam_ON.'>Si</option>
                                                                                            <option value="N" '.$selected_factur_equip_exam_OFF.'>No</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class="" style="text-align:center;font-size:9pt;" >
                                                                                        '.$vlr_tarifa_temp.'<input type="checkbox" id="wgrabar_'.$dif_fila_exam_equip.'" name="wgrabar_'.$dif_fila_exam_equip.'" etiqueta="wgrabar" class="modificaLiquidado" value="on" '.$cheched_grabar_equip_exam.' '.$campos_localizacion_js_exam_equip.' />
                                                                                    </td>
                                                                                 </tr>';
                                                        }
                                                    }
                                                    $contetemp++;
                                                    $html_equipos_examenes .= '</table>';
                                                }
                                                else
                                                {
                                                    $arr_CargosGrabadosResponsablesDetallado = $arr_CargosGrabadosResponsables[$codigo_procedimiento][$concepto_cod_ppal];
                                                }
                                                // $info_tarifa = $arr_CargosGrabadosResponsablesDetallado['info_tarifa'];
                                                // $guardar = print_r($arr_CargosGrabadosResponsablesDetallado,true).PHP_EOL;
                                                // seguimiento($guardar);
                                                $info_tarifa = "";
                                                $valor_tarifa = 0;
                                                // $arr_CargosGrabadosResponsablesDetallado_TEMPORAL_CONTROL = array();
                                                if(count($arr_CargosGrabadosResponsablesDetallado) == 0)
                                                {
                                                    // $arr_CargosGrabadosResponsablesDetallado = array("temp"=>array("temp"=>array()));
                                                    $info_tarifa = '<img style="cursor:pointer" id="" class="tooltip" title="NO TIENE TARIFA" width="16" height="16" src="../../images/medical/sgc/Warning-32.png" />';
                                                }

                                                $cssFila = ($css_count % 2 == 0) ? 'fila1': 'fila2';

                                                // foreach ($arr_CargosGrabadosResponsablesDetallado_TEMPORAL_CONTROL as $codigo_empresa_responsable => $arr_cargos_reponsable)
                                                {
                                                    // Esto se hace para que cuando no sean equipos ni examanes, el ciclo siguiente se ejecute por lo menos una vez,
                                                    // para poder que se pinte la información de un concepto normal.

                                                    // foreach ($recorrer_cargos as $codigo_cargo => $arr_cargo)
                                                    {
                                                        $dif_concepto = $concepto_cod_ppal;
                                                        $campos_localizacion_js = ' wconsec="'.$cont_pint.'" wprocedimiento="'.$codigo_procedimiento.'" wconcepto ="'.$concepto_cod_ppal.'" wequip_examen ="" ';

                                                        $dif_fila           = $codigo_procedimiento.'_'.$concepto_cod_ppal.'_';//.'_'.$cont_pint;
                                                        $id_campo_ter       = 'wtercero_'.$dif_fila;
                                                        $id_campo_esp       = 'wespecialidad_'.$dif_fila;

                                                        // Si ya se había almacenado en el array de liquidados entonces toma el valor de liquidados.
                                                        $codigo_wtercero = "";
                                                        $nombre_wtercero = "";
                                                        $wespecialidad_ = "";
                                                        $wespecialidad_nombre_ = "";
                                                        $option_especialidad = "";
                                                        $datos_medico_oculto = "";
                                                        $mensaje_no_tarifa = "¡NO TIENE PORCENTAJE!";
                                                        // Si existe en el de liquidados entonces inicializa los datos con esos.
                                                        // echo "arr_datos_liquidados [ $procedimiento_liquidar_cod ][$dif_concepto]";
                                                        if(array_key_exists($procedimiento_liquidar_cod, $arr_datos_liquidados))
                                                        {
                                                            if(array_key_exists($dif_concepto, $arr_datos_liquidados[$procedimiento_liquidar_cod]) && $dif_concepto != $id_concepto_uso_equipos && $dif_concepto != $id_concepto_examenes)
                                                            {
                                                                $option_especialidad = '<option value="'.$arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wespecialidad'].'" selected="selected">
                                                                                            '.$arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wespecialidad_nombre'].'
                                                                                        </option>';
                                                                $codigo_wtercero = $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wtercero'];
                                                                $nombre_wtercero = $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wtercero_nombre'];
                                                                $wespecialidad_ = $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wespecialidad'];
                                                                $wespecialidad_nombre_ = $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wespecialidad_nombre'];
                                                                $mensaje_no_tarifa .= " ".$nombre_wtercero;
                                                            }

                                                            // Asignar un tercero por defecto cuando el campo está vacío
                                                            if(empty($codigo_wtercero))
                                                            {
                                                                $userCargo = explode('-',$_SESSION['user']);
                                                                $wuse      = $userCargo[1];
                                                                global $wbasedato, $wemp_pmla, $conex, $wuse;
                                                                $arr_tercero_xdefecto = array();
                                                                $arr_tercero_xdefecto = traer_terceros_por_defecto($concepto_cod_ppal);

                                                                $codigo_wtercero       = $arr_tercero_xdefecto["codigo"];
                                                                if(!empty($codigo_wtercero))
                                                                {
                                                                    // $guardar = print_r("codigo_wtercero:$codigo_wtercero",true).PHP_EOL;
                                                                    // seguimiento($guardar);
                                                                    $nombre_wtercero       = $codigo_wtercero.'-'.$arr_tercero_xdefecto["nombre"];
                                                                    $wespecialidad_        = "";
                                                                    $wespecialidad_nombre_ = "";
                                                                    $mensaje_no_tarifa .= " ".$nombre_wtercero;

                                                                    $arr_terceros_especialidadDef = obtener_array_terceros_especialidad();

                                                                    $option_especialidad = '';
                                                                    if($codigo_wtercero != '' && array_key_exists($codigo_wtercero, $arr_terceros_especialidadDef))
                                                                    {
                                                                        $arr_ter = $arr_terceros_especialidadDef[$codigo_wtercero];
                                                                        // $guardar = print_r("expl_espec: ",true).print_r($arr_ter['especialidad'],true).PHP_EOL;
                                                                        // seguimiento($guardar);
                                                                        $expl_espec = explode(",", $arr_ter['especialidad']);
                                                                        foreach ($expl_espec as $key => $codigoesp_nomesp)
                                                                        {
                                                                            $expl_cod_nom = explode("-", $codigoesp_nomesp);
                                                                            $option_especialidad .= '<option value="'.$expl_cod_nom[0].'" selected="selected">
                                                                                                    '.$expl_cod_nom[1].'
                                                                                                </option>';

                                                                            // Por defecto asigna la primer especialidad.
                                                                            if(empty($wespecialidad_))
                                                                            {
                                                                                $wespecialidad_        = $expl_cod_nom[0];
                                                                                $wespecialidad_nombre_ = $expl_cod_nom[1];
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                                // Esta parte && ($id_concepto_uso_equipos != $dif_concepto && $id_concepto_examenes != $dif_concepto)
                                                                // se pone para evitar un error cuando se da clic de nuevo en el boton liquidar y tienen que agregar los equipos o examenes
                                                                // que estan en el array de liquidados en la función agregarEquiposExamenes() porque intenta agregar
                                                                // wtercero wtercero_nombre wespecialidad wespecialidad_nombre como códigos de equipos o examenes
                                                                if(array_key_exists($dif_concepto, $arr_datos_liquidados[$procedimiento_liquidar_cod]) && ($id_concepto_uso_equipos != $dif_concepto && $id_concepto_examenes != $dif_concepto))
                                                                {
                                                                    $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wtercero']             = $codigo_wtercero;
                                                                    $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wtercero_nombre']      = $nombre_wtercero;
                                                                    $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wespecialidad']        = $wespecialidad_;
                                                                    $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wespecialidad_nombre'] = $wespecialidad_nombre_;
                                                                }

                                                            }

                                                            // Actualiza la información de tercero en el array para grabar
                                                            if(array_key_exists($procedimiento_liquidar_cod, $arr_CARGOS_PARA_GRABAR) && array_key_exists($dif_concepto, $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod]))
                                                            {
                                                                $cargos_editar = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto];
                                                                foreach ($cargos_editar as $secuencial => $arr_editar)
                                                                {
                                                                    $porcentaje_tercero = 0;
                                                                    if($requiere_tercero == "C")
                                                                    {
                                                                        // Por cada coargo verificar cual es el porcentaje de participación para el tercero.
                                                                        // se debe hacer por cada cargo puesto que pudo haber cambiado de responsable entonces las condiciones pueden variar.
                                                                        $wcodcon                 = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["wcodcon"];
                                                                        $wtipoempresaToTer       = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["tipoEmpresa"];
                                                                        $wtarifaToTer            = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["wtar"];
                                                                        $wempresaToTer           = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["wcodemp"];
                                                                        $wccoToTer               = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["centroCostos"];
                                                                        $wcod_procedimientoToTer = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["procedimiento_liquidar_cod"];
                                                                        $arr_porTer              = datos_desde_tercero($codigo_wtercero,$wespecialidad_,$wcodcon,$wtip_paciente,$whora_cargo,$wfecha_cargo,$wtipoempresaToTer,$wtarifaToTer,$wempresaToTer,$wccoToTer,$wcod_procedimientoToTer, "01");
                                                                        $porcentaje_tercero      = $arr_porTer["wporter"];

                                                                        if(empty($porcentaje_tercero))
                                                                        {
                                                                            $codigo_wtercero       = "";
                                                                            $nombre_wtercero       = "";
                                                                            $wespecialidad_        = "";
                                                                            $wespecialidad_nombre_ = "";
                                                                            $option_especialidad   = "";
                                                                        }
                                                                        else
                                                                        {
                                                                            $mensaje_no_tarifa = "";
                                                                        }
                                                                    }

                                                                    $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["wporter"]              = $porcentaje_tercero;
                                                                    $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["wcodter"]              = $codigo_wtercero;
                                                                    $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["wnomter"]              = $nombre_wtercero;
                                                                    $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["codigo_especialidad"]  = $wespecialidad_;
                                                                    $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["wespecialidad"]        = $wespecialidad_;
                                                                    $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencial]["wespecialidad_nombre"] = $wespecialidad_nombre_;
                                                                }
                                                            }
                                                        }

                                                        if($requiere_tercero == "C")
                                                        {
                                                            $fn_check            = 'onclick="cargarMedicoSeleccionado(\'check_'.$id_campo_ter.'\',\''.$id_campo_ter.'\',\''.$id_campo_esp.'\')"';
                                                            $params_check_med    = 'codigo_medico="'.$arr_datos_procedimiento['wespecialista'].'" nombre_medico="'.$arr_datos_procedimiento['wespecialistas_nombre'].'"';
                                                            $params_check_esp    = 'codigo_esp="'.$arr_datos_procedimiento['wespecialidad'].'" nombre_esp="'.$arr_datos_procedimiento['wespecialidad_nombre'].'"';
                                                            $datos_medico_oculto = ' <input id="check_'.$id_campo_ter.'" '.$fn_check.' type="checkbox" '.$params_check_med.' '.$params_check_esp.' /> ';
                                                        }

                                                        $campo_tercero      = ($requiere_tercero == 'C') ? $datos_medico_oculto.'<input '.$campos_localizacion_js.' title="'.$mensaje_no_tarifa.'" placeholder="'.$mensaje_no_tarifa.'" type="text" etiqueta="wtercero" id="'.$id_campo_ter.'" name="'.$id_campo_ter.'" value="'.$nombre_wtercero.'" class="tooltip modificaLiquidado liq_autocomplete requerido vacios_terceros" codigo="'.$codigo_wtercero.'" nombre="'.$nombre_wtercero.'" >' : '';
                                                        $campo_especialidad = ($requiere_tercero == 'C') ? '<select '.$campos_localizacion_js.' id="'.$id_campo_esp.'" name="'.$id_campo_esp.'" etiqueta="wespecialidad" value="" class="modificaLiquidado liq_depend_autocomplete requerido" >'.$option_especialidad.'</select>' : '';

                                                        $facturable = "S";
                                                        $se_graba = "on";
                                                        $cheched_grabar = 'checked="checked"';
                                                        $select_fac = '';
                                                        $selected_factur_ON = 'selected="selected"';
                                                        $selected_factur_OFF = '';
                                                        $parametros_no_facturables = false;

                                                        // if($dif_concepto == $id_concepto_uso_equipos || $dif_concepto == $id_concepto_examenes)
                                                        // {
                                                        //     $facturable = ($arr_cargo['WTIENE_TARIFA'] == 'on') ? 'S': 'N';
                                                        //     $se_graba = ($arr_cargo['WTIENE_TARIFA'] == 'on') ? 'on': 'off';
                                                        //     if($facturable=='N' || $se_graba == 'off')
                                                        //     {
                                                        //         $parametros_no_facturables = true;
                                                        //     }
                                                        // }
                                                        // else
                                                        if($info_tarifa != '')
                                                        {
                                                            $parametros_no_facturables = true;
                                                            $facturable   = "N";
                                                            $se_graba     = "off";
                                                        }

                                                        if($parametros_no_facturables)
                                                        {
                                                            $selected_factur_ON  = '';
                                                            $selected_factur_OFF = 'selected="selected"';

                                                            $cheched_grabar = 'disabled="disabled"';
                                                            $select_fac = 'disabled="disabled"';
                                                            $inputs_valores = '
                                                                    <input '.$campos_localizacion_js.' type="text" id="wvalor_'.$dif_fila.'" name="wvalor_'.$dif_fila.'" etiqueta="wvalor" value="" placeholder="$ valor" size="10" style="text-align:right;" class="numerico requerido" >';
                                                        }

                                                        $cargo_facturable = "S";
                                                        if(count($arr_CargosGrabadosResponsablesDetallado) == 1)
                                                        {
                                                            foreach ($arr_CargosGrabadosResponsablesDetallado as $cod_empresa => $arr_value_cargo)
                                                            {
                                                                $cargo_facturable = $arr_value_cargo['wfacturable'];
                                                            }

                                                            if(array_key_exists($procedimiento_liquidar_cod, $arr_datos_liquidados)
                                                                && array_key_exists($dif_concepto, $arr_datos_liquidados[$procedimiento_liquidar_cod]))
                                                            {
                                                                $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wfacturable'] = $cargo_facturable;
                                                            }
                                                        }

                                                        if($cargo_facturable == 'N')
                                                        {
                                                            $selected_factur_ON  = '';
                                                            $selected_factur_OFF = 'selected="selected"';
                                                        }

                                                        // Si existe en el de liquidados entonces inicializa los datos con esos.
                                                        // echo "arr_datos_liquidados [ $procedimiento_liquidar_cod ][$dif_concepto]";
                                                        if(array_key_exists($procedimiento_liquidar_cod, $arr_datos_liquidados))
                                                        {
                                                            if(array_key_exists($dif_concepto, $arr_datos_liquidados[$procedimiento_liquidar_cod]) && $dif_concepto != $id_concepto_uso_equipos && $dif_concepto != $id_concepto_examenes)
                                                            {
                                                                $selected_factur_ON  = '';
                                                                if($cargo_facturable == 'S')
                                                                {
                                                                    $selected_factur_ON  =  'selected="selected"';
                                                                }

                                                                $selected_factur_OFF = '';
                                                                if($cargo_facturable == 'N')
                                                                {
                                                                    $selected_factur_OFF = 'selected="selected"';
                                                                    $facturable = "N";
                                                                }
                                                                $cheched_grabar      = '';
                                                                if($arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]['wgrabar'] == 'on')
                                                                {
                                                                    $cheched_grabar      =  'checked="checked"';
                                                                }
                                                                else
                                                                {
                                                                    $se_graba = 'off';
                                                                }
                                                                $cheched_grabar      = ($info_tarifa != '') ? 'disabled="disabled"': $cheched_grabar;
                                                            }
                                                        }

                                                        $html_responsables = '';
                                                        $html_valores_resp = '';
                                                        $suma_excedentes = 0;
                                                        $total_responsables_mayor_a_cero = 0;
                                                        $otra_tarifa = $valor_tarifa;
                                                        $total_responsables_cobro = 0;// Sumatoria de los valores de los responsables;
                                                        // print_r($arr_entidades_responsables);
                                                        $porcentaje_cxMult_pintar = 0;
                                                        $error_tarifa = 0;
                                                        $tarifa_pide_valor = false;
                                                        foreach ($arr_entidades_responsables as $codigo_responsable => $info_responsable)
                                                        {
                                                            if(array_key_exists($codigo_responsable, $arr_CargosGrabadosResponsablesDetallado) && array_key_exists("valor_final_original", $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]))
                                                            {
                                                                $valor_final = $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["valor_final_original"]*1; // Todos los reponsables simulados generados tienen el mismo valor_final_origial por eso no hay problema en tomar el valor de cualquiera de ellos
                                                            }

                                                            if(array_key_exists($codigo_responsable, $arr_CargosGrabadosResponsablesDetallado) && array_key_exists("wvalorTarifaBase", $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]))
                                                            {
                                                                $valor_tarifa = $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["wvalorTarifaBase"]*1; // Todos los reponsables simulados generados tienen el mismo valor_final_origial por eso no hay problema en tomar el valor de cualquiera de ellos
                                                                // echo "[$valor_tarifa]";
                                                            }

                                                            if($codigo_responsable != $codEmpPartic)
                                                            {
                                                                $valor_cobrar = 0;

                                                                if(array_key_exists($codigo_responsable, $arr_CargosGrabadosResponsablesDetallado) && $dif_concepto != $id_concepto_uso_equipos && $dif_concepto != $id_concepto_examenes)
                                                                {
                                                                    $porcentaje_cxMult_pintar = $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["porcentaje_cxMult_CARGO"]*1; // Todos los responsables simulados generados tienen el mismo porcentaje_cxMult por eso no hay problema en tomar el valor de cualquiera de ellos
                                                                    $error_tarifa = $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["error_tarifa"]; // Todos los responsables simulados generados tienen el mismo error_tarifa por eso no hay problema en tomar el valor de cualquiera de ellos
                                                                    $tarifaEmpresa_cargo = $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["tarifa_empresa_cargo"];
                                                                    if(array_key_exists($codigo_responsable, $arr_CargosGrabadosResponsablesDetallado) && $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["wfacturable"] == 'S')
                                                                    {
                                                                        $valor_cobrar             = $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["wvaltarReco"]*1;
                                                                        $suma_excedentes          += $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["wvaltarExce"]*1;
                                                                        $total_responsables_cobro += ($arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["wvaltarExce"]*1)+$valor_cobrar;
                                                                    }

                                                                    if($modifica_valor == 'S' && array_key_exists($tarifaEmpresa_cargo, $arr_tarifas_modifican_valor_erp))
                                                                    {
                                                                        $tarifa_pide_valor = true;
                                                                    }

                                                                    $otra_tarifa = $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["wvalorTarifaBase"]*1;

                                                                }

                                                                if(array_key_exists($codigo_responsable, $arr_CargosGrabadosResponsablesDetallado) && $arr_CargosGrabadosResponsablesDetallado[$codigo_responsable]["wfacturable"] == 'S')
                                                                {
                                                                    if($valor_cobrar > 0 && $suma_excedentes > 0) //Si hay valor a cobrar del responsable y adicionalmente hay excedente
                                                                    {
                                                                        $total_responsables_mayor_a_cero += 2;
                                                                    }
                                                                    elseif($valor_cobrar > 0)
                                                                    {
                                                                        $total_responsables_mayor_a_cero += 1;
                                                                    }
                                                                    elseif($suma_excedentes > 0)
                                                                    {
                                                                        $total_responsables_mayor_a_cero += 1;
                                                                    }
                                                                }
                                                                else
                                                                {
                                                                    $valor_cobrar = 0;
                                                                }
                                                                $html_responsables .= '<td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >'.utf8_encode($info_responsable["simulacion_campos"]['Empnom']).'</td>';
                                                                $html_valores_resp .= '<td class="" style="text-align:center;font-size:9pt;font-weight:bold;" >'.number_format($valor_cobrar,CANTIDAD_DECIMALES).'</td>';
                                                            }
                                                            else
                                                            {
                                                                // $suma_excedentes += $arr_CargosGrabadosResponsables[$codigo_responsable]["wvaltarExce"]*1;
                                                            }
                                                        }
                                                        $html_responsables .= '<td class="encabezadoTabla" style="text-align:center;font-size:9pt;font-weight:bold;" >Excedente</td>';
                                                        $html_valores_resp .= '<td class="" style="text-align:center;font-size:9pt;font-weight:bold;" >'.number_format($suma_excedentes,CANTIDAD_DECIMALES).'</td>';

                                                        $html_tds_TOTALES[$procedimiento_liquidar_cod] = ($html_tds_TOTALES[$procedimiento_liquidar_cod]*1)+$total_responsables_cobro;

                                                        // $inputs_valores = $valor_tarifa;
                                                        // Si es solo un responsable, se conserva entonces el valor del cargo a tarifa de ese responsable, es posible que el cargo se haya originado
                                                        // con un responsable pero por no tener tope entonces se le cobre al responsable siguiente a tarifa de ese nuevo responsable.
                                                        if($total_responsables_mayor_a_cero == 1)
                                                        {
                                                        }
                                                        $valor_final_simulado = $total_responsables_cobro;
                                                        $inputs_valores = $otra_tarifa;

                                                                            // flotante '.$dif_fila.'
                                                        $div_caja_flotante = '
                                                                        <div id="caja_flotante_'.$dif_fila.'" style="display:none; background-color: #FFFEE2; padding: 5px;" class="caja_flotante" >
                                                                            <div style="width:100%;text-align:left;" ><span style="font-weight:bold;color:red;" >^</span> <span class="alinear_derecha" style="width: 20px; cursor:pointer;" title="Cerrar" ><img src="../../images/medical/eliminar1.png" alt="Cerrar" onclick="cerrarFlotante();" /></span></div>
                                                                            <table>
                                                                                <tr>
                                                                                    '.$html_responsables.'
                                                                                </tr>
                                                                                <tr class="fila1" >
                                                                                    '.$html_valores_resp.'
                                                                                </tr>
                                                                            </table>
                                                                        </div>';

                                                        // Si hay examenes y equipos por mostrar entonces se muestra solo un td con colspan
                                                        if(!empty($html_equipos_examenes))
                                                        {
                                                            $fila_html .= '
                                                                        <td class="'.$css_span_td.'" colspan="'.$colspan.'" style="font-size:8pt;" valign="top" >'.$html_equipos_examenes.'</td>';
                                                        }
                                                        else
                                                        {
                                                            $vlr_total_cargo = '<a class="tooltip" title="Click para ver detalle" href="javascript:" onclick="posicionElemento(this);" dif_fila="'.$dif_fila.'" >'.number_format($valor_final_simulado, CANTIDAD_DECIMALES).'</a>';
                                                            // Si el concepto modifica valor entonces se debe crear el campo para ingresar el valor
                                                            $campo_modifica_valor = '';
                                                            // Si es concepto que modifica valor y no tiene tarifa entonces permite escribir un valor.
                                                            // Ó si es un concepto que modifica valor y la tarífa esta marcada en root_51 inficando que siempre debe permitir escribir un valor, por ejemplo para el caso de tarífas particulares.
                                                            if(($modifica_valor == 'S' && $error_tarifa == 1) || ($modifica_valor == 'S' && $tarifa_pide_valor)) // && $info_tarifa != '' //&& $valor_final_simulado == ''
                                                            {
                                                                $valor_final_simulado = ($valor_final_simulado == '0') ? '': $valor_final_simulado;
                                                                $vlr_total_cargo = '<input size="4" '.$campos_localizacion_js.' title="Digitar el valor a cobrar" placeholder="Valor" type="text" etiqueta="wvalor_digitado" id="wvalor_digitado_'.$dif_fila.'" name="wvalor_digitado_'.$dif_fila.'" value="'.trim($valor_final_simulado).'" class="tooltip modificaLiquidado requerido" codigo="'.$codigo_wtercero.'" nombre="'.$nombre_wtercero.'" style="text-align:right;" onkeyup="actualizarEquivalenteHidden(this)" valorAnterior="'.$valorOriginalDigitado.'">';

                                                                if(!$tarifa_pide_valor)
                                                                {
                                                                    $info_tarifa = '<img style="cursor:pointer" id="" class="tooltip" title="NO TIENE TARIFA" width="16" height="16" src="../../images/medical/sgc/Warning-32.png" />';
                                                                    $cheched_grabar      = ($info_tarifa != '') ? 'disabled="disabled"': $cheched_grabar;
                                                                }
                                                            }

                                                            $fila_html .= '
                                                                        <td class="'.$css_span_td.'" style="font-size:8pt;" >'.$campo_tercero.'</td>
                                                                        <td class="'.$css_span_td.'" style="font-size:8pt;" >'.$campo_especialidad.'</td>
                                                                        <td class="'.$css_span_td.'" style="font-size:8pt;text-align:right;" >'.$porcentaje_cxMult_pintar.'%</td>
                                                                        <td class="'.$css_span_td.'" style="font-size:8pt;text-align:right;" >'.number_format($inputs_valores, CANTIDAD_DECIMALES).'</td>
                                                                        <td class="'.$css_span_td.'" style="font-size:11pt;font-weight:bold;text-align:right;" >
                                                                            '.$div_caja_flotante.'
                                                                            '.$vlr_total_cargo.'
                                                                        </td>
                                                                        <td class="'.$css_span_td.'" style="font-size:8pt;" >
                                                                                    <select disabled="disabled" id="wfacturable_'.$dif_fila.'" name="wfacturable_'.$dif_fila.'" etiqueta="wfacturable" class="modificaLiquidado" '.$select_fac.' '.$campos_localizacion_js.' >
                                                                                        <option value="S" '.$selected_factur_ON.'>Si</option>
                                                                                        <option value="N" '.$selected_factur_OFF.'>No</option>
                                                                                    </select>
                                                                        </td>
                                                                        <td class="'.$css_span_td.'" >
                                                                                    '.$info_tarifa.'<input type="checkbox" id="wgrabar_'.$dif_fila.'" name="wgrabar_'.$dif_fila.'" etiqueta="wgrabar" class="modificaLiquidado" value="on" '.$cheched_grabar.' '.$campos_localizacion_js.' />
                                                                        </td>';
                                                        }

                                                        if(!array_key_exists($dif_concepto, $arr_datos_liquidados[$procedimiento_liquidar_cod]))
                                                        {
                                                            $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto] = array();
                                                        }

                                                        if($dif_concepto != $id_concepto_uso_equipos && $dif_concepto != $id_concepto_examenes)
                                                        {
                                                            $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto] =
                                                                    array(  "wconcepto_nombre"           => $arr_datos_procedimiento['configuracion_liquidar'][$concepto_cod_ppal]['concepto_nom_ppal'],
                                                                            "id_insumo"                  => "", // Si tiene valor es porque es un medicamento o material
                                                                            "wcantidad"                  => 1,
                                                                            "wprocedimiento"             => $procedimiento_liquidar_cod,
                                                                            "wprocedimiento_nombre"      => $arr_procedimientos_liquidar[$procedimiento_liquidar_cod]['nombre'],
                                                                            "wconcepto"                  => $concepto_cod_ppal,
                                                                            "wtercero"                   => $codigo_wtercero,
                                                                            "wtercero_nombre"            => $nombre_wtercero,
                                                                            "wespecialidad"              => $wespecialidad_,
                                                                            "cobraHonorarios"            => "on",
                                                                            "wespecialidad_nombre"       => $wespecialidad_nombre_,
                                                                            "wporcentaje"                => $porcentaje_cxMult_pintar,
                                                                            "wvalor"                     => $valor_final,
                                                                            "wvalor_final"               => $valor_final,
                                                                            "wfacturable"                => $facturable,
                                                                            "wgrabar"                    => $se_graba,
                                                                            "wbaseliquidacion"           => $wbaseliquidacion,
                                                                            "wtipo_facturacion"          => $arr_info_conf['procedimiento_tarifa'],
                                                                            "wtipo_anestesia_cx"         => $arr_datos_procedimiento["wtipo_anestesia_cx"],
                                                                            "wtiempo_sala_recuperarcion" => $arr_datos_procedimiento["wtiempo_sala_recuperarcion"],
                                                                            "wtiempo_uso_minutos"        => $arr_datos_procedimiento["wtiempo_uso_minutos"],
                                                                            "wtiempo_minutos_cx"         => $arr_datos_procedimiento["wtiempo_minutos_cx"],
                                                                            "wexiste"                    => "",
                                                                            "wserv"                      => $arr_info_conf['wserv'],
                                                                            "mueve_inventario"           => $arr_info_conf['mueve_inventario']);

                                                            // Si hay un valor digitado para este procedimiento-concepto entonces inicializarlo de nuevo
                                                            // porque en algunos momento se pierde el valor en esta posicíon, es posible que sea porque en un proceso anterior
                                                            // en este código se reinicia a array vacío la posición de pricedimiento-concepto para obligar crear el array
                                                            // de cero con valores actualizados.
                                                            if(isset($valorOriginalDigitado) && !empty($valorOriginalDigitado))
                                                            {
                                                                $arr_datos_liquidados[$procedimiento_liquidar_cod][$dif_concepto]["wvalor_digitado"] = $valorOriginalDigitado;
                                                            }

                                                            if(array_key_exists($procedimiento_liquidar_cod, $arr_CARGOS_PARA_GRABAR) && array_key_exists($dif_concepto, $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod]))
                                                            {
                                                                $editar_cargos = $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto];
                                                                foreach ($editar_cargos as $secuencia => $cargo_editar)
                                                                {
                                                                    $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencia]["wfacturable"] = $facturable;
                                                                    $arr_CARGOS_PARA_GRABAR[$procedimiento_liquidar_cod][$dif_concepto][$secuencia]["wgrabar"]     = $se_graba;
                                                                }
                                                            }
                                                        }
                                                        else
                                                        {
                                                        }
                                                        $sbtotal += $valor_final*1;

                                                        $cont_pint++;
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $nombre_proc_exp = explode("-", $arr_procedimientos_liquidar[$procedimiento_liquidar_cod]['nombre']);
                                            $data['procedimientos_materiales'][$procedimiento_liquidar_cod] = $nombre_proc_exp[1];
                                        }
                                        $css_count++;
                                    }
                                    else
                                    {
                                        // $fila_html .= '<td colspan="'.$colspan.'" class="'.$css_span_td.'" >&nbsp;</td>';
                                        $fila_html .= ' <td class="'.$css_span_td.'" style="color:red;" >- -</td>
                                                        <td class="'.$css_span_td.'" style="color:red;" >- -</td>
                                                        <td class="'.$css_span_td.'" style="color:red;text-align:right;" >- -</td>
                                                        <td class="'.$css_span_td.'" style="color:red;text-align:right;" >- -</td>
                                                        <td class="'.$css_span_td.'" style="color:red;font-size:11pt;font-weight:bold;text-align:right;" >'.number_format("0", CANTIDAD_DECIMALES).'</td>
                                                        <td class="'.$css_span_td.'" style="color:red;" >- -</td>
                                                        <td class="'.$css_span_td.'" style="color:red;" >- -</td>';
                                                        // <td class="'.$css_span_td.'" >- -</td>
                                                        // <td class="'.$css_span_td.'" >- -</td>
                                                        // <td class="'.$css_span_td.'" >- -</td>
                                    }

                                    $contador_spn_td++;
                                }
                            }
                            $fila_html .= '</tr>';
                            $html_tds_valores[] = $fila_html;
                        }

                        //formatear números de totales
                        foreach ($html_tds_TOTALES as $codigo_procedimiento => $value) {
                            $sumar_insumos_valor = 0;
                            if(array_key_exists($codigo_procedimiento, $arr_insumos_procedimiento))
                            {
                                $sumar_insumos_valor = $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["sumatoria_medicamento"];
                                $value += $sumar_insumos_valor*1;
                                $sumar_material_valor = $arr_html_insumos_por_procedimiento[$codigo_procedimiento]["sumatoria_material"];
                                $value += $sumar_material_valor*1;
                            }
                            $html_tds_TOTALES[$codigo_procedimiento] = number_format($value,CANTIDAD_DECIMALES);
                        }

                        // Procedimientos seleccionables para agregarle conceptos
                        $options_procedimientos = '<option value=""></option>';
                        foreach ($arr_procedimientos_liquidar as $key_procedimiento => $arr_info_proc)
                        {
                            $class_join = $key_procedimiento; // Concatena todos los conceptos de un procedimiento, esto permite saber si para un procedimiento ya se agregó un concepto, justo cuando se está intentando adicionar un nuevo concepto
                            // Los procedimientos que ya tengan ese concepto agregado entonces no aparecen en la lista de selección.
                            $msjPosBilater = $arr_info_proc['codigo'];
                            // if($arr_info_proc['wesbilateral'] == 'on')
                            if(!empty($arr_info_proc['wposicion_organo_nom']))
                            {
                                $msjPosBilater = $arr_info_proc['codigo']." [".$arr_info_proc['wposicion_organo_nom']."] ";
                            }

                            foreach ($arr_info_proc["configuracion_liquidar"] as $concepto_del_procedimiento => $value)
                            {
                                $class_join .= "_".$concepto_del_procedimiento;
                            }
                            $texto_imp = str_replace('-','', $arr_info_proc['nombre']);
                            $texto_imp = str_replace($arr_info_proc['codigo'], $msjPosBilater, $texto_imp);
                            $options_procedimientos .= '<option value="'.$key_procedimiento.'" lista="'.$class_join.'" >'.trim($texto_imp).'</option>';
                        }

                        $manual_liquidacion = "";
                        if(!empty($wbaseliquidacion_nombre))
                        {
                            // Al consultar manuales por cada responsable, entonces puede ser que se apliquen varios manuales diferentes.
                            // entonces no tendría razón de ser de mostrar solo uno, puede generar inconvenientes en la interpreración.
                            // $img_tlp = '<img border=\'0\' width=\'10\' height=\'10\' style=\'display:;\' src=\'../../images/medical/root/info.png\' >';
                            // $manual_liquidacion = '<div style="width:100%;text-align: left;" class="fila1 tooltip" title="'.$img_tlp.' '.$wbaseliquidacion_acto_quirurgico.'" ><img border="0" width="10" height="10" style="display:;" src="../../images/medical/root/info.png" > Se usó el manual para cirugías multiples: ['.$wbaseliquidacion.'] '.$wbaseliquidacion_nombre.'</div>';
                        }

                        $noadd_con = ($es_paquete) ? 'display:none;': '';

                        $html = $manual_liquidacion.'
                                <div style="width:1235px; overflow:auto;" >
                                <table class="margen-superior-eventos" align="center" >
                                    <tr>
                                        <td>&nbsp;</td>
                                        '.implode("", $arr_encabezados_proced['colspan_nombre']).'
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        '.implode("", $arr_encabezados_proced['columnas_valores']).'
                                    </tr>
                                    '.implode("", $html_tds_valores).'
                                    <tr class="encabezadoTabla" >
                                        <td>
                                        MEDICAMENTOS
                                        <br>
                                        <span onclick="mostrarOcultarInsumos(\'ver_insumos\');" style="cursor:pointer;" >
                                        <u>[<img border="0" width="10" height="10" style="display:;" src="../../images/medical/root/info.png" >
                                            Ver/Ocultar]</u>
                                        </span>
                                        </td>
                                        '.implode("", $arr_encabezados_proced['columnas_insumos']).'
                                    </tr><tr class="encabezadoTabla" >
                                        <td>
                                        MATERIALES
                                        <br>
                                        <span onclick="mostrarOcultarInsumos(\'ver_materiales\');" style="cursor:pointer;" >
                                        <u>[<img border="0" width="10" height="10" style="display:;" src="../../images/medical/root/info.png" >
                                            Ver/Ocultar]</u>
                                        </span>
                                        </td>
                                        '.implode("", $arr_encabezados_proced['columnas_materiales']).'
                                    </tr>
                                    <tr class="encabezadoTabla" >
                                        <td>Totales</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                                        <td  style="font-size:11pt;font-weight:bold;">
                                            '.implode("</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>",$html_tds_TOTALES).'
                                        </td>
                                        <td>&nbsp;</td><td>&nbsp;</td>
                                    <tr/>
                                </table>
                                </div>
                                <!-- <div class="fila1" style="text-align:left;font-weight:bold;font-size:10pt;" >TOTAL INSUMOS: <a href="javascript:" onclick="posicionElemento(this);" dif_fila="insumos" style="" >'.number_format($suma_total_simulada, CANTIDAD_DECIMALES).'</a></div> -->

                                <table>
                                    <tr class="fila1" id="tr_add_" style="background-color:#FFFEE2;'.$noadd_con.'" >
                                        <td>Agregar concepto: </td>
                                        <td><input style="font-size:8pt" type="text" id="add_concepto" name="add_concepto" value="" placeholder="Concepto" codigo="" nombre="" class="concepto_autocomplete" ></td>
                                        <td id="td_add_equipo" style="display:none; " >Equipo:
                                            <input style="font-size:8pt" type="text" id="add_equipo" name="add_equipo" value="" placeholder="Equipo" codigo="" nombre="" class="add_equipos_examenes" >
                                        </td>
                                        <td id="td_add_examen" style="display:none; " >Exámen:
                                            <input style="font-size:8pt" type="text" id="add_examen" name="add_examen" value="" placeholder="Exámen" codigo="" nombre="" class="add_equipos_examenes" >
                                        </td>
                                        <td id="td_add_procedimiento" style="display:none;" >Procedimiento:
                                            <select style="font-size:8pt" name="add_procedimiento" id="add_procedimiento" onclange="validarSeleccionable(this);">
                                                '.$options_procedimientos.'
                                            </select>
                                        </td>
                                        <td>
                                            <input class="btn_loading" type="button" id="btn_add_concepto" name="btn_add_concepto" value="Adicionar" onclick="agregarConceptoNuevo(\'add_procedimiento\',\'add_concepto\',\'tabla_conceptos_liq_\');" />
                                        </td>
                                    </tr>
                                </table>';
                        // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
                        // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

                        // $guardar = print_r($arr_datos_liquidados,true).PHP_EOL;
                        // seguimiento($guardar);

                        //Ordenar arreglo liquidado según el orden de procedimientos correcto
                        $arr_auxiliar_procedimientos = array();
                        foreach ($arr_procedimientos_orden as $cod_especialidad => $arr_procedimientos_especialidad)
                        {
                            foreach ($arr_procedimientos_especialidad as $cod_procedimiento_ord => $valor_orden)
                            {
                                $arr_conceptos_procedimiento = (array_key_exists($cod_procedimiento_ord, $arr_datos_liquidados)) ? $arr_datos_liquidados[$cod_procedimiento_ord] : array();
                                if(!array_key_exists($cod_procedimiento_ord, $arr_auxiliar_procedimientos))
                                {
                                    $arr_auxiliar_procedimientos[$cod_procedimiento_ord] = array();
                                }

                                //Ordenar tambien los conceptos de cada procedimientos antes de agregarlo de nuevo al array
                                $arr_ordenar_conceptos_procedimiento = array();
                                foreach ($arr_lista_conceptos as $codigo_concepto_ord => $nombre_concepto)
                                {
                                    $arr_concepto_info = array();
                                    if(array_key_exists($codigo_concepto_ord, $arr_conceptos_procedimiento))
                                    {
                                        $arr_concepto_info = $arr_conceptos_procedimiento[$codigo_concepto_ord];
                                        if(!array_key_exists($codigo_concepto_ord, $arr_ordenar_conceptos_procedimiento))
                                        {
                                            $arr_ordenar_conceptos_procedimiento[$codigo_concepto_ord] = array();

                                            unset($arr_conceptos_procedimiento[$codigo_concepto_ord]);
                                            // Si al final del proceso sobran conceptos en este array, entonces se deben terminar de agregar
                                            // Esta caso sucede cuando por ejemplo se liquidan medicamentos, se asocian al concepto de insumos.
                                        }
                                        $arr_ordenar_conceptos_procedimiento[$codigo_concepto_ord] = $arr_concepto_info;
                                    }

                                }

                                // Comprobar si quedaron conceptos por asignar
                                foreach ($arr_conceptos_procedimiento as $codigo_concepto_ord => $arr_concepto_info)
                                {
                                    if(!array_key_exists($codigo_concepto_ord, $arr_ordenar_conceptos_procedimiento))
                                    {
                                        $arr_ordenar_conceptos_procedimiento[$codigo_concepto_ord] = array();
                                    }
                                    $arr_ordenar_conceptos_procedimiento[$codigo_concepto_ord] = $arr_concepto_info;
                                }

                                $arr_auxiliar_procedimientos[$cod_procedimiento_ord] = $arr_ordenar_conceptos_procedimiento;
                            }
                        }
                        $arr_procedimientos_orden = $arr_auxiliar_procedimientos;

                        $arr_datos_liquidados = $arr_procedimientos_orden;

                        // Guardar Encabezado y detalles para los cargos que no tienen tarifas
                        // $arr_cargos_sinTariga
                        // $guardar = "arr_cargos_sinTariga: ".print_r($arr_cargos_sinTariga,true).PHP_EOL;
                        // seguimiento($guardar);
                        $data["id_encabezado_sin_tarifa"] = '';
                        $data["wfaltan_tarifas"]          = 'off';
                        if(count($arr_cargos_sinTariga) > 0)
                        {
                            $data["wfaltan_tarifas"] = 'on';
                            $datos_temporales = array(  "tabla_lista_cxs"            => trim($tabla_lista_cxs),
                                                        "arr_datos_liquidar"         => base64_encode(serialize($arr_datos_liquidar)),
                                                        "arr_datos_liquidados"       => base64_encode(serialize($arr_datos_liquidados)),
                                                        "arr_extras"                 => base64_encode(serialize($arr_extras)),
                                                        "wnumvias"                   => $wnumvias,
                                                        "wfecha_cargo"               => $wfecha_cargo,
                                                        "whora_cargo"                => $whora_cargo,
                                                        "wpolitraumatizado"          => $wpolitraumatizado,
                                                        "wtipo_anestesia_cx"         => $wtipo_anestesia_cx,
                                                        "wtiempo_sala_recuperarcion" => $wtiempo_sala_recuperarcion,
                                                        "wtiempo_uso_minutos"        => $wtiempo_uso_minutos,
                                                        "wtiempo_minutos_cx"         => $wtiempo_minutos_cx,
                                                        "wliq_paquete"               => $es_paquete,
                                                        "id_encabezado_sin_tarifa"   => $id_encabezado_sin_tarifa,
                                                        "arr_CARGOS_PARA_GRABAR"     => base64_encode(serialize($arr_CARGOS_PARA_GRABAR)));
                            $id_encabezado_sin_tarifa = guardar_datos_temporales($conex, $wbasedato, $datos_temporales, $fecha_actual, $hora_actual, $whistoria, $wing, $user_session, '000230');

                            $data["id_encabezado_sin_tarifa"] = $id_encabezado_sin_tarifa;

                            $sql_tm ="  UPDATE  {$wbasedato}_000231
                                                SET Tcarest = 'off'
                                        WHERE   Tcaride = '{$id_encabezado_sin_tarifa}' ";
                            mysql_query($sql_tm, $conex) or die("<b>ERROR EN QUERY MATRIX - ACTUALIZAR INACTIVAR REGISTROS TEMPORALES ANTERIORES (000231):</b><br>".mysql_error()."<br>SQL: ".$sql_tm);

                            $arr_sql_tm_ins = "";
                            foreach ($arr_cargos_sinTariga as $key => $arr_cargo_notarifa)
                            {
                                /*
                                (
                                    [whistoria] => 510443
                                    [wingreso] => 1
                                    [wserv_ingreso] =>
                                    [wempresa_responsable] => 800088702-2
                                    [wservicio_graba] => 1300
                                    [wconcepto] => 0168
                                    [wprocedimiento] => 862801
                                    [wtercero] =>
                                    [wespecialidad] => 100121
                                    [wmodalidad] => UVR
                                    [wtarifa_empresa] => 91
                                    [wes_insumo] =>
                                    [id_encabezado_sin_tarifa] =>
                                    [id_detalle_sin_tarifa] =>
                                )
                                 */
                                $arr_sql_tm_ins[] = "
                                                ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}',
                                                '{$whistoria}', '{$wing}',
                                                '{$arr_cargo_notarifa['wserv_ingreso']}',
                                                '{$arr_cargo_notarifa['wempresa_responsable']}',
                                                '{$arr_cargo_notarifa['wservicio_graba']}',
                                                '{$arr_cargo_notarifa['wconcepto']}',
                                                '{$arr_cargo_notarifa['wprocedimiento']}',
                                                '{$arr_cargo_notarifa['wtercero']}',
                                                '{$arr_cargo_notarifa['wespecialidad']}',
                                                '{$arr_cargo_notarifa['wmodalidad']}',
                                                '{$arr_cargo_notarifa['wtarifa_empresa']}',
                                                '{$arr_cargo_notarifa['wes_insumo']}',
                                                'PR',
                                                '{$id_encabezado_sin_tarifa}',
                                                '{$arr_cargo_notarifa['wvalor']}',
                                                'on',
                                                'C-{$user_session}')";
                            }

                            if(count($arr_sql_tm_ins) > 0)
                            {
                                $sql_tm_ins = " INSERT INTO {$wbasedato}_000231
                                                (Medico, Fecha_data, Hora_data,
                                                Tcarhis, Tcaring,
                                                Tcarsin,
                                                Tcarres,
                                                Tcarser,
                                                Tcarcon,
                                                Tcarpro,
                                                Tcarter,
                                                Tcaresp,
                                                Tcartfa,
                                                Tcartar,
                                                Tcarins,
                                                Tcarrcr,
                                                Tcaride,
                                                Tcarval,
                                                Tcarest,
                                                Seguridad)
                                                VALUES ".implode(",", $arr_sql_tm_ins);
                                mysql_query($sql_tm_ins, $conex) or die("<b>ERROR EN QUERY MATRIX - INSERTAR DETALLE DE CARGOS TEMPORALES (000231):</b><br>".mysql_error()."<br>SQL: ".$sql_tm_ins);
                            }
                        }

                        // $guardar = "arr_CARGOS_PARA_GRABAR: ".print_r($arr_CARGOS_PARA_GRABAR,true).PHP_EOL;
                        // $guardar = "arr_CARGOS_PARA_GRABAR: ".print_r($arr_TOPES_ENTIDADES,true).PHP_EOL;
                        // $guardar = "arr_datos_liquidados: ".print_r($arr_datos_liquidados,true).PHP_EOL;
                        // $guardar = "arr_procedimientos_orden: ".print_r($arr_procedimientos_orden,true).PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
                        // $guardar = print_r($arr_procedimientos_orden_liquidar,true).PHP_EOL;
                        // $guardar = "arr_extras: ".print_r($arr_extras,true).PHP_EOL;
                        // $guardar = print_r($orden_conceptos_por_procedimiento,true).PHP_EOL;
                        // $guardar = print_r($arr_lista_conceptos,true).PHP_EOL;
                        // seguimiento($guardar);

                        // Si tiene por lo menos una posición es porque hay por lo menos un cargo sin tarífa.
                        $html_botones_guardar_actualizar = '
                                        <button id="btn_reliquidacion" onclick="reliquidar_cx(this);" class="" style="display:none;">Actualizar liquidación</button>
                                        <button id="btn_grabar_liquidacion" onclick="guardarLiquidacion(this);" class="btn_loading" >Grabar liquidación</button>';
                        if(count($cargosSinTarifas) > 0)
                        {
                            $html_noTarifa = '';
                            foreach ($cargosSinTarifas as $cod_procedSinTarifa_dif => $arr_infoProced)
                            {
                                $html_conceptosNoTar = '';
                                $cont_css = 0;
                                foreach ($arr_infoProced['lista_cargos'] as $key_codConcepto => $arr_infoCargo)
                                {
                                    $css_tm = ($cont_css % 2 == 0) ? 'fila1': 'fila2';
                                    $html_conceptosNoTar .= '<tr class="'.$css_tm.'">
                                                                <td style="text-align:left;" >'.$arr_infoCargo['cobro_procedimiento'].'</td>
                                                                <td style="text-align:left;" >'.$arr_infoCargo['cobro_concepto_cod'].'-'.utf8_encode($arr_infoCargo['cobro_concepto_nom']).'</td>
                                                                <td style="text-align:left;" >'.$arr_infoCargo['wcod_empresa'].'</td>
                                                                <td style="text-align:left;" >'.$arr_infoCargo['wcentro_costo'].'</td>
                                                                <td style="text-align:left;" >'.$arr_infoCargo['wfecha_cargo'].'</td>
                                                                <td style="text-align:left;" >'.$arr_infoCargo['whora_cargo'].'</td>
                                                                <td style="text-align:left;" >'.$arr_infoCargo['wtipo_ingreso'].'-'.utf8_encode($arr_infoCargo['wtipo_ingreso_nom']).'</td>
                                                                <td style="text-align:left;" >'.$arr_infoCargo['especialidad_procedimiento'].'</td>
                                                                <td style="text-align:left;" >'.$arr_infoCargo['codigo_wtercero_tarifa'].'</td>
                                                            </tr>';
                                    $cont_css++;
                                }
                                $html_noTarifa .= '<table class="margen-superior-eventos" align="center" >
                                                        <tr>
                                                            <td style="width:300px;text-align:left;" class="encabezadoTabla">Procedimiento en liquidación</td>
                                                            <td style="text-align:left;" class="fila2">'.utf8_encode($arr_infoProced['info_procedimiento']['procedimiento_liquidado_nom']).'</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:left;" class="encabezadoTabla">Modalidad de facturación</td>
                                                            <td style="text-align:left;" class="fila2">'.utf8_encode($arr_infoProced['info_procedimiento']['modalidad_facturacion']).'</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                                <div style="width:100%;">
                                                                    <table>
                                                                        <tr class="encabezadoTabla">
                                                                            <td>Código a cobrar</td>
                                                                            <td>Concepto a cobrar</td>
                                                                            <td>Código empresa</td>
                                                                            <td>Código centro de costos</td>
                                                                            <td>Fecha del cargo</td>
                                                                            <td>Hora del cargo</td>
                                                                            <td>Tipo de ingreso</td>
                                                                            <td>Código especialidad</td>
                                                                            <td>Código del tercero</td>
                                                                        </tr>
                                                                        '.$html_conceptosNoTar.'
                                                                    </table>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                </table>';
                            }
                            $html = '<div style="width:1235px; overflow:auto;background-color:#ffffcc; " >
                                        <div style="width:100%;text-align:center;color:red; font-weight:bold; width: 530px; margin-left: 28%;">
                                            NO HAY TARIFAS PARA LOS CONCEPTOS EN LA LISTA PARA ESA MODALIDAD DE FACTURACIÓN, ver el detalle a continuación.
                                        </div>
                                        <table class="margen-superior-eventos" align="center" >
                                            '.$html_noTarifa.'
                                        </table>
                                    </div>';
                            $html_botones_guardar_actualizar = '';
                            // $html = "<div align=left style='text-align:left'><pre>".print_r($cargosSinTarifas,true)."</pre></div>";
                        }

                        $data['html'] = '
                                    <div id="cont_dlle_modal" style="background-color: #FBFBFB; text-align:center;" >
                                        '.$html.'
                                        <br>
                                    </div>
                                    <div style="text-align:center;" class="fila1">
                                        '.$html_botones_guardar_actualizar.'
                                        <div id="div_msj_falta_tarifa" style="width:100%; text-align:center; color: red; font-weight:bold;"></div>
                                    </div> ';
                        $data['arr_datos_liquidados']              = base64_encode(serialize($arr_datos_liquidados));
                        $data['arr_procedimientos_orden_liquidar'] = base64_encode(serialize($arr_procedimientos_orden_liquidar));
                        $data['arr_extras']                        = base64_encode(serialize($arr_extras));
                        $data['arr_CARGOS_PARA_GRABAR']            = base64_encode(serialize($arr_CARGOS_PARA_GRABAR));
                    break;

                case 'add_arr_datos_liquidar':
                    /*
                        arr_datos_liquidar = array("wnumero_vias"               => 0,
                                                    "wtipo_anestesia"            => "",
                                                    "wtiempo_sala_recuperarcion" => "",
                                                    "wtiempo_uso_minutos"        => "",
                                                    "wtiempo_minutos_cx"         => "",
                                                    "arr_para_liquidar"          => array());
                    */
                    $data['fila'] = "";
                    $arr_datos_liquidar = unserialize(base64_decode($arr_datos_liquidar));

                    $wprocedimiento_dif = $wprocedimiento;
                    $add_msj_posicion = "";
                    $arr_control_proBilateral = array();
                    $arr_control_proBilateral[$wprocedimiento_dif] = $wprocedimiento_dif;
                    if(!empty($wposicion_organo))
                    {
                        $arr_control_proBilateral                        = array();
                        $wprocedimiento_dif = $wprocedimiento."_".$wposicion_organo;
                        $arr_control_proBilateral[$wprocedimiento_dif] = $wprocedimiento_dif;
                        $add_msj_posicion                               = ' ('.$wposicion_organo_nom.')';
                    }

                    if($wbilateral == 'on')
                    {
                        // $wprocedimiento_dif = $wprocedimiento_dif;
                        $arr_control_proBilateral                        = array();
                        $arr_control_proBilateral[$wprocedimiento_dif."_bilateral1"] = $wprocedimiento_dif;
                        $arr_control_proBilateral[$wprocedimiento_dif."_bilateral2"] = $wprocedimiento_dif;
                    }

                    $primer_elemento = "";
                    foreach ($arr_control_proBilateral as $wprocedimiento_dif => $value)
                    {
                        $dif_tr = $wprocedimiento_dif."_".$wespecialistas."_".$wespecialidad;
                        if(!array_key_exists($dif_tr, $arr_datos_liquidar['arr_para_liquidar']))
                        {
                            $arr_datos_liquidar['arr_para_liquidar'][$dif_tr] = array();
                        }

                        if($wbilateral == 'on')
                        {
                            $wposicion_organo_nom = $wprocedimiento_dif;
                        }

                        $arr_datos_liquidar['arr_para_liquidar'][$dif_tr] =
                                array(  "wprocedimiento_dif"         => $wprocedimiento_dif,
                                        "wprocedimiento"             => $wprocedimiento,
                                        "wprocedimiento_nombre"      => $wprocedimiento_nombre,
                                        "wespecialistas"             => $wespecialistas,
                                        "wespecialistas_nombre"      => $wespecialistas_nombre,
                                        "worgano"                    => $worgano,
                                        "wvia"                       => $wvia,
                                        "wespecialidad"              => $wespecialidad,
                                        "wespecialidad_nombre"       => $wespecialidad_nombre,
                                        "wbilateral"                 => $wbilateral,
                                        "wposicion_organo"           => $wposicion_organo,
                                        "wposicion_organo_nom"       => $wposicion_organo_nom,
                                        "wtipo_anestesia_cx"         => $wtipo_anestesia_cx,
                                        "wtiempo_sala_recuperarcion" => $wtiempo_sala_recuperarcion,
                                        "wtiempo_uso_minutos"        => $wtiempo_uso_minutos,
                                        "wtiempo_minutos_cx"         => $wtiempo_minutos_cx);

                        if(empty($primer_elemento))
                        {
                            $data['fila'] = '   <tr id="tr_liqAdd_cxs_'.$dif_tr.'" >
                                                    <td>'.$wprocedimiento_nombre.'</td>
                                                    <td>'.$wvia.'</td>
                                                    <td>'.$wespecialistas_nombre.'</td>
                                                    <td>'.$wespecialidad_nombre.'</td>
                                                    <td>'.$worgano_nombre.'</td>
                                                    <td>'.$wbilateral.$add_msj_posicion.'</td>
                                                    <!-- <td>'.$wtipo_anestesia_nombre.'</td>
                                                    <td>'.$wtiempo_sala_recuperarcion.'</td>
                                                    <td>'.$wtiempo_uso_minutos.'</td>
                                                    <td>'.$wtiempo_minutos_cx.'</td> -->
                                                    <td>
                                                        <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" onclick="eliminarFilaDatosLiq(\'tr_liqAdd_cxs_'.$dif_tr.'\', \'tr_liqAdd_cxs_\', \''.$dif_tr.'\', \''.$wprocedimiento_dif.'\');">
                                                    </td>
                                                </tr>';
                        }
                        $primer_elemento = $dif_tr;
                    }

                    // $guardar = "arr_datos_liquidar: ".print_r($arr_datos_liquidar,true).PHP_EOL;
                    // seguimiento($guardar);
                    $data['arr_datos_liquidar'] = base64_encode(serialize($arr_datos_liquidar));
                    break;

                case 'cargar_select_vias':
                        $option = '';
                        for($i = 1; $i <= $wnumvias; $i++)
                        {
                            $option .= '<option value="'.$i.'">'.$i.'</option>';
                        }
                        $data['html'] = $option;
                    break;

                case 'consultar_equipos_examenes' :
                        if($tipo_procedimiento == 'E')
                        {
                            $sql =" SELECT  Procod AS codigo, Pronom AS nombre, Procup AS codigo_cups
                                    FROM    {$wbasedato}_000103
                                    WHERE   Protip = 'E'
                                            AND Proest ='on'
                                    ORDER BY Pronom";
                        }
                        elseif($tipo_procedimiento == 'Q')
                        {
                            $sql =" SELECT  Procod AS codigo, Pronom AS nombre, Procup AS codigo_cups
                                    FROM    {$wbasedato}_000103
                                    WHERE   Protip = 'Q'
                                            AND Proest ='on'
                                    ORDER BY Pronom";
                        }
                        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
                        $arr_equipos_examenes = array();
                        while ($row = mysql_fetch_array($result))
                        {
                            if(!array_key_exists($row['codigo'], $arr_equipos_examenes))
                            {
                                $arr_equipos_examenes[$row['codigo']] = $row['nombre'];
                            }
                        }

                        $data["arr_equipos_examenes"] = $arr_equipos_examenes;
                    break;

                case 'obtenerLiquidacionTemporal':
                        $data["tabla_lista_cxs"]        = "";
                        $data["div_recuperacion_datos"] = "";

                        if($temporal == '000160')
                        {
                            $sql = "SELECT  Ecotem
                                    FROM    {$wbasedato}_000160
                                    WHERE   Ecohis = '{$historia}'
                                            AND  Ecoing = '{$ingreso}'
                                            AND  Ecotip = 'QX' ";

                            if($result = mysql_query($sql, $conex))
                            {
                                $row = mysql_fetch_array($result);
                                $html_recuperado = $row["Ecotem"];
                                $html_recuperado = str_replace("[+]", "'", $html_recuperado);
                                $html_recuperado = stripslashes($html_recuperado);
                                $html_explode = explode("[*****]", $html_recuperado);

                                // $guardar = "html_recuperado: ".print_r($html_explode,true).PHP_EOL;
                                // seguimiento($guardar);

                                $data["tabla_lista_cxs"]        = trim(utf8_encode($html_explode[0]));
                                $data["div_recuperacion_datos"] = utf8_encode($html_explode[1]);
                            }
                            else
                            {
                                $data["error"] = 1;
                                $data["mensaje"] = "Había una liquidación en proceso guardada temporalmente pero no se pudo recuperar.";
                                //or die("<b>ERROR EN QUERY MATRIX 2:</b><br>".mysql_error());
                            }
                        }
                        elseif($temporal == '000230')
                        {
                            //id_encabezado_sin_tarifa
                            //
                            $sql = "SELECT  c230.id AS id_encabezado, c230.Ntrhis AS whistoria, c230.Ntring AS wing, c230.Ntrrcr AS estado_monitor, c230.Ntrltm AS html_procedimientos_liq
                                    FROM    {$wbasedato}_000230 AS c230
                                    WHERE   c230.id = '{$id_encabezado_sin_tarifa}'
                                    ORDER BY c230.Ntrhis, c230.Ntring ";
                            if($result = mysql_query($sql, $conex))
                            {
                                $row = mysql_fetch_array($result);
                                $html_recuperado = $row["html_procedimientos_liq"];
                                $html_recuperado = str_replace("[+]", "'", $html_recuperado);
                                $html_recuperado = stripslashes($html_recuperado);
                                $html_explode = explode("[*****]", $html_recuperado);

                                // $guardar = "html_recuperado: ".print_r($html_explode,true).PHP_EOL;
                                // seguimiento($guardar);

                                $data["tabla_lista_cxs"]        = trim(utf8_encode($html_explode[0]));
                                $data["div_recuperacion_datos"] = utf8_encode($html_explode[1]);
                            }
                            else
                            {
                                $data["error"] = 1;
                                $data["mensaje"] = "Había una liquidación en proceso de revisión guardada temporalmente pero no se pudo recuperar.";
                                //or die("<b>ERROR EN QUERY MATRIX 2:</b><br>".mysql_error());
                            }
                        }
                    break;

                case 'carga_paquetes':
                        $data["arr_paquetes"] = array();
                        global $conex, $wemp_pmla, $wbasedato, $caracter_ok, $caracter_ma;

                        // Paquetes
                        $arr_paquetes = array();
                        $sql = "SELECT  t113.Paqcod AS codigo, t113.Paqnom AS nombre
                                FROM    {$wbasedato}_000207 AS t207
                                        INNER JOIN
                                        {$wbasedato}_000113 AS t113 ON (t207.Mpapro = t113.Paqcod)
                                WHERE   t207.Mpahis = '{$historia}'
                                        AND t207.Mpaing = '{$ingreso}'
                                        AND LEFT(t207.Mpapro, 2) = 'CP'
                                GROUP BY t113.Paqcod
                                ORDER BY  t113.Paqnom";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sql);
                        while($row = mysql_fetch_array($result))
                        {
                            if(!array_key_exists($row['codigo'], $arr_paquetes))
                            {
                                $arr_paquetes[$row['codigo']] = array();
                            }
                            $arr_paquetes[$row['codigo']] = utf8_encode($row['nombre']);
                        }

                        // $data["arr_paquetes"] = "".json_encode(Obtener_array_paquetes())."";
                        $data["arr_paquetes"] = "".json_encode($arr_paquetes)."";
                    break;

                case 'recargar_datos_temp_revision':
                    $data["html"] = html_cirugiasPendientes(cirugiasPendientesLiquidar($conex, $wbasedato, $whistoria, $wing));
                    break;

                case 'inactivar_pendientes':
                    $sql = "UPDATE  {$wbasedato}_000230
                                    SET Ntrest = 'off'
                            WHERE   Ntrhis = '$historia'
                                    AND Ntring = '$ingreso'";
                    if($result = mysql_query($sql,$conex))
                    {
                        $sql = "UPDATE  {$wbasedato}_000231
                                        SET Tcarest = 'off'
                                WHERE   Tcarhis = '$historia'
                                        AND Tcaring = '$ingreso'";
                        if($result = mysql_query($sql,$conex))
                        {
                            //
                        }
                        else
                        {
                            $data["error"] = 1;
                            $data['mensaje'] = utf8_encode("No se pudo inactivar el detalle de cirugía pendiente sin tarifa para esta historia");
                        }
                    }
                    else
                    {
                        $data["error"] = 1;
                        $data['mensaje'] = utf8_encode("No se pudo inactivar el encabezado de cirugía pendiente para esta historia");
                    }
                    break;

                default:
                        $data['mensaje'] = utf8_encode($no_exec_sub);
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'delete' :
            switch ($form)
            {
                case 'CODIGO_EJEMPLO':
                        $query = "  UPDATE  ".$wbasedato."_".OBSERVACIONES_ORDEN."
                                            SET Segest = 'off'
                                    WHERE   id = '".$id_observ."'";
                        if($result = mysql_query($query, $conex))
                        {

                        }
                        else
                        {
                            debug_log_inline('',"<span class=\"error\">ERROR</span> Error al borrar obsrvación de la orden: $worden Fuente: $wfuente <br>&raquo; ".$query."<br>&raquo;No. ".mysql_errno().'<br>&raquo;Err: '.mysql_error()."<br>");
                            $descripcion = "(".mysql_errno().') '.mysql_error()."|obs:'Error al borrar obsrvación de la orden: $worden Fuente: $wfuente";
                            // insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wfuente.'-'.$worden, $query);
                            $data['mensaje'] = 'No se pudo eliminar la observación.';
                            $data['error'] = 1;
                        }
                        $data['debug_log'] = utf8_encode(debug_log_inline());
                    break;

                case 'eliminar_dato_liquidacion':
                    // arr_datos_liquidar
                    // arr_datos_liquidar_temp
                    // arr_datos_liquidados
                    // arr_procedimientos_orden_liquidar
                    // arr_extras

                    $arr_datos_liquidar                = unserialize(base64_decode($arr_datos_liquidar));
                    $arr_datos_liquidados              = unserialize(base64_decode($arr_datos_liquidados));
                    $arr_procedimientos_orden_liquidar = unserialize(base64_decode($arr_procedimientos_orden_liquidar));
                    $arr_extras                        = unserialize(base64_decode($arr_extras));

                    // Como los procedimientos bilaterales generan dos registros (posición 1 y posición 2) entonces se hace la verificación
                    // para saber si es bilateral o no y generar las dos posiciones para borrar ambas y no solo la primera.
                    $arr_indice_eliminar = array();
                    $arr_indice_eliminar[$wprocedimiento] = $wprocedimiento;
                    $resultado = strpos($wprocedimiento, "_bilateral1");
                    if($resultado !== FALSE){
                        $arr_indice_eliminar[str_replace("_bilateral1","_bilateral2",$wprocedimiento)] = str_replace("_bilateral1","_bilateral2",$wprocedimiento);
                    }

                    foreach ($arr_indice_eliminar as $wprocedimiento => $value)
                    {
                        if(array_key_exists($wprocedimiento, $arr_datos_liquidados)) { unset($arr_datos_liquidados[$wprocedimiento]); } // Si ya existía en el array de liquidados entonces lo elimina
                        if(array_key_exists($wprocedimiento, $arr_extras)) { unset($arr_extras[$wprocedimiento]); } // Si ya existía en el array de conceptos extras del procedimiento
                        if(array_key_exists($wprocedimiento, $arr_procedimientos_orden_liquidar)) { unset($arr_procedimientos_orden_liquidar[$wprocedimiento]); } // Si ya existía en el array de conceptos extras del procedimiento
                    }

                    // ***********  PARA EL ARRAY arr_datos_liquidar específicamente  **************
                    // Como los procedimientos bilaterales generan dos registros (posición 1 y posición 2) entonces se hace la verificación
                    // para saber si es bilateral o no y generar las dos posiciones para borrar ambas y no solo la primera.
                    $arr_indice_eliminar = array();
                    $arr_indice_eliminar[$dif_key] = $dif_key;
                    $resultado = strpos($dif_key, "_bilateral1");
                    if($resultado !== FALSE){
                        $arr_indice_eliminar[str_replace("_bilateral1","_bilateral2",$dif_key)] = str_replace("_bilateral1","_bilateral2",$dif_key);
                    }

                    foreach ($arr_indice_eliminar as $dif_key => $value)
                    {
                        unset($arr_datos_liquidar['arr_para_liquidar'][$dif_key]);
                    }

                    $data['arr_datos_liquidar']                = base64_encode(serialize($arr_datos_liquidar));
                    $data['arr_datos_liquidar_temp']                = base64_encode(serialize($arr_datos_liquidar));
                    $data['arr_extras']                        = base64_encode(serialize($arr_extras));
                    $data['arr_datos_liquidados']              = base64_encode(serialize($arr_datos_liquidados));
                    $data['arr_procedimientos_orden_liquidar'] = base64_encode(serialize($arr_procedimientos_orden_liquidar));
                    // print_r($arr_datos_liquidar);
                    break;

                default:
                    $data['mensaje'] = utf8_encode('No se ejecutó ningúna rutina interna del programa');
                    break;
            }
            echo json_encode($data);
            break;
        default : break;
    }
    return;
}

include_once("root/comun.php");
$wbasedato_HCE    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$grupo_anestesia  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipos_anestesia'); // Consulta el código del grupo que corresponde a tipos de anestesia en maestro HCE
$concepto_ayudas_erp = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_ayudas_erp');
$concepto_equipos_erp = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_equipos_erp');

/***** terceros *****/
global $conex, $wemp_pmla;
$arr_terceros_especialidad = obtener_array_terceros_especialidad();

$arr_parametros = array();
$arr_detalle_liquidaciones = listarDetalleLiquidaciones($conex, $wemp_pmla, $wbasedato, $whistoria, $wing, $arr_parametros);
$arr_parametros['arr_terceros'] = $arr_terceros_especialidad;

/***** Procedimientos *****/
$arr_procedimientos = obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato);

/***** Organos *****/
$TB_BILATERALES = TB_BILATERALES;
$arr_organos = array();
$sql = "SELECT  hce16.Codigo AS codigo, hce16.Descripcion AS nombre, bilat.Orgbil AS esbilateral
        FROM    {$wbasedato_HCE}_000016 AS hce16
                LEFT JOIN
                {$wbasedato}_{$TB_BILATERALES} AS bilat ON (hce16.Codigo = bilat.Orgcod AND bilat.Orgbil = 'on')
        WHERE   hce16.Estado = 'on'
        ORDER BY hce16.Descripcion";
$result = mysql_query($sql,$conex) OR die ($sql.' <br> '.mysql_error());

$arr_organos = array();
while ($row = mysql_fetch_array($result))
{
    $esbilateral = ($row['esbilateral'] == 'on') ? 'on': 'off';
    $arr_organos[$row['codigo']] = array("nombre" => utf8_encode($row['nombre']), "bilateral" => $esbilateral);
}

/***** Posiciones del organo *****/
$posiciones_para_organos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'posiciones_organos');
$exp_posiciones = explode(";",$posiciones_para_organos);
$arr_posciones_organos = array();
foreach ($exp_posiciones as $key_tem => $value)
{
    $explode_tmp = explode("-", $value);
    if(!array_key_exists($explode_tmp[0], $arr_posciones_organos))
    {
        $arr_posciones_organos[$explode_tmp[0]] = "";
    }
    $arr_posciones_organos[$explode_tmp[0]] = $explode_tmp[1];
}

$arr_datos_liquidar = array("arr_datos_paciente"         => array(  "whistoria"                  => $whistoria,
                                                                    "wing"                       => $wing,
                                                                    "wno1"                       => $wno1,
                                                                    "wno2"                       => $wno2,
                                                                    "wap1"                       => $wap1,
                                                                    "wap2"                       => $wap2,
                                                                    "wdoc"                       => $wdoc,
                                                                    "wnomemp_eps"                => $wnomemp,
                                                                    "tarifa_original"            => $tarifa_original,
                                                                    "wtip_paciente"              => $wtip_paciente,
                                                                    "wtipo_ingreso"              => $wtipo_ingreso,
                                                                    "wtipo_ingreso_nom"          => $wtipo_ingreso_nom,
                                                                    "wresponsable_eps_codigo"    => $responsable_original,
                                                                    "wresponsable_eps"           => $div_responsable,
                                                                    "tipoEmpresa"                => $tipoEmpresa),
                            "wnumero_vias"               => "",
                            "wtipo_anestesia"            => "",
                            "wtiempo_sala_recuperarcion" => "",
                            "wtiempo_uso_minutos"        => "",
                            "wtiempo_minutos_cx"         => "",
                            "arr_para_liquidar"          => array());
$arr_datos_liquidar_temp = $arr_datos_liquidar;

/***** Tipos de anestesia *****/
$sql = "SELECT  Selcda AS codigo, Selnda AS nombre
        FROM    {$wbasedato_HCE}_000012
        WHERE   Seltab = '{$grupo_anestesia}'
                AND Selest = 'on'
        ORDER BY Selnda";
$result = mysql_query($sql,$conex) OR die ($sql.' <br> '.mysql_error());

$arr_tipo_anestesia = array();
while ($row = mysql_fetch_array($result))
{
    $arr_tipo_anestesia[$row['codigo']] = utf8_encode($row['nombre']);
}

/***** BASE DE LIQUIDACION *****/
$arr_liquidacion = array_base_liquidacion($conex, $wbasedato);

$arr_datos_liquidados = array();
$arr_vacio = array();

/***** Conceptos *****/
$arr_conceptos = obtener_array_conceptos();

// Consultar el concepto que corresponde a Uso de Equipos y a Exámenes
// $q_conceptos = "    SELECT  Grucod, Grudes, Gruarc, Grutip
//                     FROM    {$wbasedato}_000200
//                     WHERE   Gruest = 'on'
//                             AND Grutip in ('E','Q')
//                     ORDER BY grudes ";
// $result = mysql_query($q_conceptos,$conex) OR die ($q_conceptos.' <br> '.mysql_error());
$id_concepto_uso_equipos = $concepto_equipos_erp;
$id_concepto_examenes = $concepto_ayudas_erp;
// while ($row = mysql_fetch_array($result))
// {
//     if($row['Grutip'] == 'E') { $id_concepto_examenes = $row['Grucod']; }
//     if($row['Grutip'] == 'Q') { $id_concepto_uso_equipos = $row['Grucod']; }
// }

// Consultando lista de exámenes
// $sql =" SELECT  Procod AS codigo, Pronom AS nombre, Procup AS codigo_cups
//         FROM    {$wbasedato}_000103
//         WHERE   Protip = 'E'
//                 AND Proest ='on'
//         ORDER BY Pronom";
$sql =" SELECT  Procod AS codigo, Pronom AS nombre, Procup AS codigo_cups
        FROM    {$wbasedato}_000103
        WHERE   Proest ='on'
        ORDER BY Pronom";
$result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
$arr_examenes = array();
while ($row = mysql_fetch_array($result))
{
    if(!array_key_exists($row['codigo'], $arr_examenes))
    {
        $arr_examenes[$row['codigo']] = $row['nombre'];
    }
}

// Consultando lista de equipos
// $sql =" SELECT  Procod AS codigo, Pronom AS nombre, Procup AS codigo_cups
//         FROM    {$wbasedato}_000103
//         WHERE   Protip = 'Q'
//                 AND Proest ='on'
//         ORDER BY Pronom";
$sql =" SELECT  Procod AS codigo, Pronom AS nombre, Procup AS codigo_cups
        FROM    {$wbasedato}_000103
        WHERE   Proest ='on'
        ORDER BY Pronom";
$result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
$arr_equipos = array();
while ($row = mysql_fetch_array($result))
{
    if(!array_key_exists($row['codigo'], $arr_equipos))
    {
        $arr_equipos[$row['codigo']] = utf8_encode($row['nombre']);
    }
}

?>
<html lang="es-ES">
<head>
    <title>Liquidación Cirugía</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librería para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <link rel="stylesheet" href="../../../include/ips/facturacionERP.css">

    <!-- Función para pintar los insumos -->
    <script src="../../../include/ips/funcionInsumosqxERP.js" type="text/javascript"></script>

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

    <script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
    <link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

    <!--<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript">-->


    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;

        $(document).on('mousemove', function(e){
            $('.caja_flotante_temp').css({
               left:  e.pageX+12,
               top:   e.pageY
            });
        });

        // Inicializar primer acordeón
        $(function(){
            $("#div_datos_basicos").attr("acordeon", "");
            $("#div_datos_basicos").accordion({
                 collapsible: true
                ,heightStyle: "content"
                //,active: -1
            });

            $('#whora_cargo').timepicker({
                showPeriodLabels: false,
                hourText: 'Hora',
                minuteText: 'Minuto',
                amPmText: ['AM', 'PM'],
                closeButtonText: 'Aceptar',
                nowButtonText: 'Ahora',
                deselectButtonText: 'Deseleccionar',
                defaultTime: 'now'
            });
        });

        /***Limitar tamaño texto***/
        jQuery(document).ready(function(){

            $('.texto-cortado').each(function(){

            var longitud=25;

            if($(this).text().length > longitud){

                var texto=$(this).text().substring(0,longitud);
                var indiceUltimoEspacio= texto.lastIndexOf(' ');
                texto=texto.substring(0,indiceUltimoEspacio) +'<span class="puntos">...</span>';

                var primeraParte = '<span class="texto-mostrado">' + texto + '</span>';
                var segundaParte = '<span class="texto-ocultado" style="display:none;">' + $(this).text().substring(indiceUltimoEspacio,$(this).text().length - 1) + '</span>';

                $(this).html(primeraParte + segundaParte);
                $(this).after('<span class="boton_mas_info"><img width="12 " height="12" border="0" src="../../images/medical/plus.gif"></span>');
            };

            });

            $('.boton_mas_info').live('click',function(){
                if($(this).prev().find('.texto-ocultado').css('display') == 'none'){
                    $(this).prev().find('.texto-ocultado').css('display','inline');
                    $(this).prev().find('.puntos').css('display','none');
                    $(this).html('<img width="12 " height="12" border="0" src="../../images/medical/cambiar1.png">');
                }

                else{
                    $(this).prev().find('.texto-ocultado').css('display','none');
                    $(this).prev().find('.puntos').css('display','inline');
                    $(this).html('<img width="12" height="12" border="0" src="../../images/medical/plus.gif">');
                };
            });

            $(".hidden_img").hide();
        });

        function inicializarFlotantes()
        {
            /**
                Inicializa el div flotante fijo que se despliega al momento de dar clic en el operador lódigo o el operador de igualdad.
            */
            var elementos = $("div[id^=caja_flotante]").length;
            if(elementos > 0)
            {
                var posicion = $(".caja_flotante").offset();
                var margenSuperior = 15;
                 $(window).scroll(function() {
                     if ($(window).scrollTop() > posicion.top) {
                         $(".caja_flotante").stop().animate({
                             // marginTop: $(window).scrollTop() - posicion.top + margenSuperior
                         });
                     } else {
                         $(".caja_flotante").stop().animate({
                             marginTop: 0
                         });
                     };
                });
            }
        }

        function mostrarOcultarInsumos(ver_div)
        {
            if($("."+ver_div+":visible").length > 0)
            {
                $("."+ver_div).hide();
            }
            else
            {
                $("."+ver_div).show();
            }
        }

        function cerrarFlotante()
        {
            if($('#caja_flotante2_insumos').is(":visible"))
            {
                $('#caja_flotante2_insumos').hide();
            }
            else
            {
                $('.caja_flotante').hide();
            }
        }

        $(document).ready( function ()
        {
            cargarDatosPaciente('whistoria','');
            validarEstadoDeCuentaCongelada(false);
            $("#accordionDatosPaciente, #acordeon_basicos_liquidacion").accordion({
                collapsible: true,
                heightStyle: "content"
            });

            $("#accordionDetalles").accordion({
                collapsible: true,
                heightStyle: "content",
                active : false
            });

            $("#accordionPendientes").accordion({
                collapsible: true,
                heightStyle: "content",
                active : false
            });

            $("#query_ejemplo").accordion({
                collapsible: true,
                heightStyle: "content",
                active : false
            });

            var datos  = eval('(' + $('#arr_terceros_especialidad').val() + ')');
            var terceros      = new Array();
            var index         = -1;
            for (var cod_ter in datos)
            {
                index++;
                terceros[index]                = {};
                terceros[index].value          = cod_ter+'-'+datos[cod_ter]['nombre'];
                terceros[index].label          = cod_ter+'-'+datos[cod_ter]['nombre'];
                terceros[index].codigo         = cod_ter;
                terceros[index].nombre         = cod_ter+'-'+datos[cod_ter]['nombre'];
                terceros[index].especialidades = datos[cod_ter]['especialidad'];
            }

            $( "#wespecialistas" ).autocomplete({
                minLength:  0,
                source:     terceros,
                select:     function( event, ui ){
                    cargarSelectEspecialidades( ui.item.especialidades , 'wespecialidad', '');
                    var cod_sel = ui.item.codigo;
                    var nom_sel = ui.item.nombre;
                    $("#wespecialistas").attr("codigo",cod_sel);
                    $("#wespecialistas").attr("nombre",nom_sel);
                }
            });


            var datosO  = eval('(' + $('#arr_organos').val() + ')');
            var organosBil      = new Array();
            var index         = -1;
            for (var cod_org in datosO)
            {
                index++;
                organosBil[index]                = {};
                organosBil[index].value          = cod_org+'-'+datosO[cod_org]['nombre'];
                organosBil[index].label          = cod_org+'-'+datosO[cod_org]['nombre'];
                organosBil[index].codigo         = cod_org;
                organosBil[index].nombre         = cod_org+'-'+datosO[cod_org]['nombre'];
                organosBil[index].bilateral      = datosO[cod_org]['bilateral'];
            }


            $( "#worgano" ).autocomplete({
                minLength:  0,
                source:     organosBil,
                select:     function( event, ui ){
                    //cargarSelectEspecialidades( ui.item.especialidades , 'wespecialidad', '');
                    var cod_sel   = ui.item.codigo;
                    var nom_sel   = ui.item.nombre;
                    var bilateral = ui.item.bilateral;

                    $("#worgano").attr("codigo",cod_sel);
                    $("#worgano").attr("nombre",nom_sel);
                    $("#worgano").attr("bilateral",bilateral);

                    // Mostrar u oculrar el check de bilateral
                    if(bilateral == 'on')
                    {
                        $(".opcion_bilateral").show();
                    }
                    else
                    {
                        $(".opcion_bilateral").hide();
                        $("#wbilateral").removeAttr("checked");
                    }
                }
            });

            $('.tooltip_pro').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

            reiniciarCamposEncabezado();
        });

        function posicionElemento(ele)
        {
            cerrarFlotante();
            var elemento = $(ele);
            var posicion = elemento.offset();
            var dif_fila = elemento.attr("dif_fila");

            if($('#caja_flotante_'+dif_fila).length > 0)
            {
                $('#caja_flotante_'+dif_fila).css({'left':posicion.left+33,'top':posicion.top+15});
                $('#caja_flotante_'+dif_fila).show();
            }
            else if($('#caja_flotante2_'+dif_fila).length > 0)
            {
                $('#caja_flotante2_'+dif_fila).css({'left':posicion.left+33,'top':posicion.top});
                $('#caja_flotante2_'+dif_fila).show();
            }
        }

        function reiniciarCamposEncabezado()
        {
            /**>> Autocompletar "procedimientos" **/
            // crearAutocomplete('arr_procedimientos', 'wprocedimiento','','',1);

            // /**>> Autocompletar "entidades" **/
            // crearAutocomplete('arr_entidades', 'wentidad','*','* TODOS *',1);

            // /**>> Autocompletar "tarifas" **/
            // crearAutocomplete('arr_tarifas', 'wtarifa','*','* TODOS *',1);

            // /**>> Autocompletar "conceptos" **/
            // crearAutocomplete('arr_conceptos', 'wnuevo_concepto_gral','','',1);
        }

        function cargarDatosPaciente(elemento, enRevision)
        {
            // si la historia es vacia se inician los datos y no se continua la ejecucion de la función
            if($("#whistoria_tal").val()=='' && $("#whistoriaLocal").val()=='')
            {
                limpiarPantalla(true);
                return;
            }
            else
            {
                if($("#whistoriaLocal").val() == '')
                {
                    $("#whistoriaLocal").val($("#whistoria_tal").val());
                }
            }

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
            {
                consultaAjax            : '',
                wemp_pmla               : $('#wemp_pmla').val(),
                accion                  : 'load',
                form                    : 'cargar_datos_paciente',
                whistoria               : $('#whistoriaLocal').val(),
                wing                    : $('#wingLocal').val(),
                arr_procedimientos_orig : $('#arr_procedimientos_temp_noPrepa').val(),
                wcargos_sin_facturar    : 'ok',
                welemento               : elemento

            },function(data){

                // --> data.prueba valida si la historia existe
                if(data.prueba == 'no')
                {
                    alert('La historia no existe');
                    $('#whistoriaLocal').val('');
                    $('#wingLocal').val('');
                    limpiarPantalla(true);
                }
                else
                {
                    // --> data.error indica si hay un error  en el llamado de la funcion
                    if(data.error ==1)
                    {
                        alert('La historia no existe');
                        $('#whistoriaLocal').val('');
                        $('#wingLocal').val('');
                        $("#whistoria_tal").val('');
                        $("#wing_tal").val('');
                        limpiarPantalla(true);
                    }
                    else
                    {
                        // --> datos traidos desde la funcion
                        // $("#whistoria_tal").val($('#whistoriaLocal').val());
                        $("#whistoria_tal").val($('#whistoriaLocal').val());

                        $("#wingLocal").val(data.wwing);
                        $("#wing_tal").val(data.wwing);

                        $("#wno1").val(data.wno1);
                        $("#wno1_tal").val(data.wno1);

                        $("#wno2").val(data.wno2);
                        $("#wno2_tal").val(data.wno2);

                        $("#wap1").val(data.wap1);
                        $("#wap1_tal").val(data.wap1);

                        $("#wap2").val(data.wap2);
                        $("#wap2_tal").val(data.wap2);

                        $("#wdoc").val(data.wdoc);
                        $("#wdoc_tal").val(data.wdoc);

                        $("#wnomemp").val(data.wnomemp);
                        $("#wnomemp_tal").val(data.wnomemp);

                        $("#wfecing").html(data.wfecing);
                        $("#wfecing_tal").val(data.wfecing);

                        $("#wser").val(data.wser);
                        $("#wser_tal").val(data.wser);

                        // --> Ubicacion actual del paciente
                        $("#divCcoActualPac").html(data.ccoActualPac+"-"+data.nomCcoActualPac);
                        $("#ccoActualPac").val(data.ccoActualPac);
                        $("#nomCcoActualPac").val(data.nomCcoActualPac);
                        $("#ccoActualPac_tal").val(data.ccoActualPac);
                        $("#nomCcoActualPac_tal").val(data.nomCcoActualPac);

                        $("#wpactam").val(data.wpactam);
                        $("#wpactam_tal").val(data.wpactam);

                        $("#nomservicio").html(data.wnombreservicio);
                        $("#nomservicio_tal").html(data.wnombreservicio);

                        $("#div_tipo_servicio").html(data.wnombreservicio);

                        $("#div_responsable").html(data.responsable);
                        $("#div_responsable_tal").val(data.responsable);

                        $("#responsable_original").val(data.wcodemp);
                        $("#responsable_original_tal").val(data.wcodemp);

                        $("#td_responsable").html(data.responsable);

                        $("#hidden_responsable").val(data.wcodemp);

                        $("#div_tarifa").html(data.tarifa);
                        $("#div_tarifa_tal").val(data.tarifa);

                        $("#tarifa_original").val(data.wtar);
                        $("#tarifa_original_tal").val(data.wtar);

                        $("#td_tarifa").html(data.tarifa);
                        $("#hidden_tarifa").val(data.wtar);
                        $("#div_paciente").html(data.paciente);

                        // --> Pintar los otros responsables del paciente
                        $("#tableResponsables").html('');
                        $("#tableResponsables").append(data.otrosResponsables).show();

                        $("#div_documento").html(data.wdoc);
                        $("#div_documento_tal").val(data.wdoc);

                        $("#div_servicio").html($("#wcco_tal").val()+'-'+$("#div_servicio_tal").val());
                        $("#div_servicio_tal").val($("#div_servicio_tal").val());

                        $("#wtip_paciente").val(data.wtip_paciente);
                        $("#wtip_paciente_tal").val(data.wtip_paciente);

                        $("#wtipo_ingreso").val(data.tipo_ingreso);
                        $("#wtipo_ingreso_tal").val(data.tipo_ingreso);
                        $("#wtipo_ingreso_nom_tal").val(data.nombre_tipo_ingreso);

                        $("#div_tipo_ingreso").html(data.nombre_tipo_ingreso);

                        // --> Tipo de empresa
                        $("#tipoEmpresa").val(data.tipoEmpresa);
                        $("#tipoEmpresa_tal").val(data.tipoEmpresa);

                        // --> Nit de empresa
                        $("#nitEmpresa").val(data.nitEmpresa);
                        $("#nitEmpresa_tal").val(data.nitEmpresa);

                        // --> Pintar el detalle de la cuenta simple
                        $("#cargos_sin_facturar").val(data.cargos_sin_facturar);
                        $("#tabla_informativos_basicos").css("display" , "block");

                        $("#arr_datos_liquidar").val(data.arr_datos_liquidar);
                        $("#arr_datos_liquidar_temp").val(data.arr_datos_liquidar_temp);

                        $("#chk_otros_procedimientos").removeAttr("checked");

                        $("#arr_procedimientos_temp").val(data.arr_procedimientos_temp);

                        // Carga los procedimientos habilitados para la historia, autorizados o con mercado
                        var estado_chk = ($("#wliq_paquete").is(":checked")) ? 'on': 'off';
                        if(estado_chk != 'on')
                        {
                            $("#arr_procedimientos").val(data.arr_procedimientos);
                        }

                        recargarDetalleLiquidaciones();

                        if(validarEstadoDeCuentaCongelada(false))
                        {
                        }

                        // --> Pintar el detalle de la cuenta
                        // pintar_detalle_cuenta();
                    }
                }
            },
            'json').done(function(){
                crearAutocomplete('arr_procedimientos', 'wprocedimiento','','',0);
                if(enRevision != '' && enRevision == 'temporal_en_revision')
                {
                    cargarTemporalEnRevision();
                }
                recargarDivTemporaSinTarifa($('#whistoriaLocal').val(), $('#wingLocal').val());
            });
        }

        function recargarDivTemporaSinTarifa(whistoria, wing)
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
            {
                consultaAjax         : '',
                wemp_pmla            : $('#wemp_pmla').val(),
                accion               : 'load',
                form                 : 'recargar_datos_temp_revision',
                whistoria            : whistoria,
                wing                 : wing
            },function(data){
                if(data.error == 1)
                {
                    alert(data.mensaje);
                }
                else
                {
                    $("#div_contenedor_detalle_pendientes").html(data.html);
                }
            },
            'json'
            ).done(function(){
                //
            });
        }

        function bloquearBoton(this_btn)
        {
            if($(".cargando").length > 0)
            {
                // Si ya hay uno cargando entonces no haga nada hasta que termine
                return;
            }

            $(".btn_loading").attr("disabled","disabled");
            $(this_btn).addClass("cargando");
            $(this_btn).html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >');
        }

        function desbloquearBoton(txt)
        {
            var this_btn_x = $(".cargando");
            $(this_btn_x).removeClass("cargando");
            if(txt == '') $(this_btn_x).html("Adicionar");
            else $(this_btn_x).html(txt)
            $(".btn_loading").removeAttr("disabled");
        }

        function generarListaVias()
        {
            var wnumvias = $("#wnumvias").val();

            if(wnumvias == '' || wnumvias == 0)
            {
                $("#wnumvias").val('1');
                wnumvias = 1;
            }

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    consultaAjax : '',
                    accion       : 'load',
                    form         : 'cargar_select_vias',
                    wnumvias     : wnumvias
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#wvia").html(data.html);
                    }
                },
                "json"
            ).done(function(){
                //
            });
        }

        function adicionarProcedimiento(this_btn)
        {
            if($("#whistoriaLocal").val() != "" && $("#whistoriaLocal").val() == $("#whistoria_tal").val())
            {
                // true si puede continuar haciendo la liquidación.
                // if(validarEstadoDeCuentaCongelada(true))
                {
                    bloquearBoton(this_btn);
                    $("#wnumvias, #wtipo_anestesia_cx, #wtiempo_sala_recuperarcion, #wtiempo_uso_minutos, #wtiempo_minutos_cx, #wposicion_organo").removeClass("campoRequerido");

                    $("#tabla_add_lista_cxs").find("save_ok").removeClass("campoRequerido");
                    var wprocedimiento          = $("#wprocedimiento").attr("codigo");
                    var wprocedimiento_nombre   = $("#wprocedimiento").attr("nombre");
                    var wespecialistas          = $("#wespecialistas").attr("codigo");
                    var wespecialistas_nombre   = $("#wespecialistas").attr("nombre");
                    var worgano                 = $("#worgano").attr("codigo");
                    var worgano_nombre          = $("#worgano").attr("nombre");
                    var worgano_bilateral       = $("#worgano").attr("bilateral");
                    var wvia                    = $("#wvia").val();
                    var wespecialidad           = $("#wespecialidad").val();
                    var wespecialidad_nombre    = $("#wespecialidad option:selected").text();
                    var wbilateral              = ($("#wbilateral").is(":checked")) ? 'on': 'off';
                    var wposicion_organo     = $("#wposicion_organo").val();
                    var wposicion_organo_nom = $("#wposicion_organo option:selected").text();

                    var wtipo_anestesia_cx         = ""; // $("#wtipo_anestesia_cx").val();
                    var wtipo_anestesia_nombre     = ""; // $("#wtipo_anestesia_cx option:selected").text();
                    var wtiempo_sala_recuperarcion = ""; // $("#wtiempo_sala_recuperarcion").val();
                    var wtiempo_uso_minutos        = ""; // $("#wtiempo_uso_minutos").val();
                    var wtiempo_minutos_cx         = ""; // $("#wtiempo_minutos_cx").val();

                    var campos_ok = true;
                    if(wprocedimiento == '')
                    {
                        campos_ok = false;
                        $("#wprocedimiento").addClass("campoRequerido");
                    }

                    if(wespecialistas == '')
                    {
                        campos_ok = false;
                        $("#wespecialistas").addClass("campoRequerido");
                    }

                    if(worgano == '')
                    {
                        campos_ok = false;
                        $("#worgano").addClass("campoRequerido");
                    }

                    if(wvia == '')
                    {
                        campos_ok = false;
                        $("#wvia").addClass("campoRequerido");
                    }

                    if(wespecialidad == '')
                    {
                        campos_ok = false;
                        $("#wespecialidad").addClass("campoRequerido");
                    }

                    if(wposicion_organo == '')
                    {
                        wposicion_organo_nom = '';
                    }

                    // si hay algun bilateral debe contener '_bilateral1'
                    var unBilateral = '';
                    if(wbilateral == 'on')
                    {
                        unBilateral = '_bilateral1';
                    }

                    var js_wposicion_organo = '';
                    if(wposicion_organo != '')
                    {
                        js_wposicion_organo = '_'+js_wposicion_organo;
                    }

                    var existe_tr = "tr_liqAdd_cxs_"+wprocedimiento+unBilateral+js_wposicion_organo+"_"+wespecialistas+"_"+wespecialidad;
                    if($("#"+existe_tr).length > 0)
                    {
                        campos_ok = false;
                        alert("Ya hay un mismo Especialista, con la misma especialidad y para el mismo procedimiento y misma posición de organo");
                    }

                    if(campos_ok)
                    {
                        $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                            {
                                consultaAjax               : '',
                                accion                     : 'load',
                                form                       : 'add_arr_datos_liquidar',
                                arr_datos_liquidar         : $("#arr_datos_liquidar").val(),
                                wprocedimiento             : wprocedimiento,
                                wprocedimiento_nombre      : wprocedimiento_nombre,
                                wespecialistas             : wespecialistas,
                                wespecialistas_nombre      : wespecialistas_nombre,
                                worgano                    : worgano,
                                worgano_nombre             : worgano_nombre,
                                worgano_bilateral          : worgano_bilateral,
                                wvia                       : wvia,
                                wespecialidad              : wespecialidad,
                                wespecialidad_nombre       : wespecialidad_nombre,
                                wbilateral                 : wbilateral,
                                wposicion_organo           : wposicion_organo,
                                wtipo_anestesia_cx         : wtipo_anestesia_cx,
                                wtipo_anestesia_nombre     : wtipo_anestesia_nombre,
                                wtiempo_sala_recuperarcion : wtiempo_sala_recuperarcion,
                                wtiempo_uso_minutos        : wtiempo_uso_minutos,
                                wtiempo_minutos_cx         : wtiempo_minutos_cx,
                                wposicion_organo_nom       : wposicion_organo_nom
                            },
                            function(data){
                                if(isset(data.error) && data.error == 1)
                                {
                                    alert(data.mensaje);
                                }
                                else
                                {
                                    $("#arr_datos_liquidar").val(data.arr_datos_liquidar);
                                    $("#tabla_lista_cxs").append(data.fila);
                                }
                            },
                            "json"
                        ).done(function(){
                            $("#wprocedimiento").attr("codigo","");
                            $("#wprocedimiento").attr("nombre","");
                            $("#wprocedimiento").val("");
                            $("#wespecialistas").attr("codigo","");
                            $("#wespecialistas").attr("nombre","");
                            $("#wespecialistas").val("");
                            $("#worgano").attr("codigo","");
                            $("#worgano").attr("nombre","");
                            $("#worgano").attr("bilateral","");
                            $("#worgano").val("");
                            $("#wvia").val("");
                            $("#wespecialidad").val("");
                            $("#wbilateral").removeAttr("checked");

                            $("#tabla_add_lista_cxs").find("save_ok").removeClass("campoRequerido");
                            resetStylePrefijo("tr_liqAdd_cxs_");

                            desbloquearBoton('');

                            validarEstadoDeCuentaCongelada(true);

                            // comprobarMostrarCxMultiples();
                        });
                    }
                    else
                    {
                        desbloquearBoton('');
                    }

                    $("#wprocedimiento").focus();
                }
            }
            else
            {
                alert("Debe escribir un número de historia correcto");
            }
        }

        function eliminarFilaDatosLiq(id_fila, prefijoFila, dif_key, wprocedimiento) //tipo_cobro ,id_fila, identifica_concepto, prefijoFila, dif_rango
        {
            $("#"+id_fila).hide("slow",
                                function(){
                                    $(this).remove();
                                    $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                                        {
                                            accion                            : 'delete',
                                            form                              : 'eliminar_dato_liquidacion',
                                            consultaAjax                      : '',
                                            arr_datos_liquidar                : $("#arr_datos_liquidar").val(),
                                            arr_datos_liquidados              : $("#arr_datos_liquidados").val(),
                                            arr_procedimientos_orden_liquidar : $("#arr_procedimientos_orden_liquidar").val(),
                                            arr_extras                        : $("#arr_extras").val(),
                                            dif_key                           : dif_key,
                                            wprocedimiento                    : wprocedimiento
                                        },
                                        function(data){
                                            if(isset(data.error) && data.error == 1)
                                            {
                                                alert(data.mensaje);
                                            }
                                            else
                                            {
                                                $("#arr_datos_liquidar").val(data.arr_datos_liquidar);
                                                $("#arr_extras").val(data.arr_extras);
                                                $("#arr_datos_liquidados").val(data.arr_datos_liquidados);
                                                $("#arr_procedimientos_orden_liquidar").val(data.arr_procedimientos_orden_liquidar);
                                                resetStylePrefijo(prefijoFila);
                                            }
                                        },
                                        "json"
                                    ).done(function(){
                                        if(($("#tabla_lista_cxs").find("tr[id^=tr_liqAdd_cxs_]").length - 1) > 1)
                                        {
                                            // $("#div_param_baseliquidacion").show("slow");
                                        }
                                        else
                                        {
                                            $("#wbaseliquidacion").val("");
                                            $("#wbaseliquidacion").removeClass("campoRequerido");
                                            // $("#div_param_baseliquidacion").hide("slow");
                                        }
                                        $("#btn_liquidar_pop").click();
                                    });
                            });
        }

        function comprobarMostrarCxMultiples()
        {
            if($("#tabla_lista_cxs").find("tr[id^=tr_liqAdd_cxs_]").length > 1)
            {
                // $("#div_param_baseliquidacion").show("slow");
            }
            else
            {
                $("#wbaseliquidacion").val("");
                $("#wbaseliquidacion").removeClass("campoRequerido");
                // $("#div_param_baseliquidacion").hide("slow");
            }
        }

        function generarLiquidar(this_btn, wprocedimiento_add, wconcepto_add)
        {
            $("#wtipo_anestesia_cx").removeClass("campoRequerido");
            $("#wtiempo_sala_recuperarcion").removeClass("campoRequerido");
            $("#wtiempo_uso_minutos").removeClass("campoRequerido");
            $("#wtiempo_minutos_cx").removeClass("campoRequerido");

            $("#div_mensajes_alerta").html("");
            $("#div_mensajes_alerta").hide();
            bloquearBoton(this_btn);
            $("#wbaseliquidacion").removeClass("campoRequerido");

            var wnumvias                   = $("#wnumvias").val();
            var wtipo_anestesia_cx         = $("#wtipo_anestesia_cx").val();
            var wtiempo_sala_recuperarcion = $("#wtiempo_sala_recuperarcion").val();
            var wtiempo_uso_minutos        = $("#wtiempo_uso_minutos").val();
            var wtiempo_minutos_cx         = $("#wtiempo_minutos_cx").val();
            var id_encabezado_sin_tarifa   = $("#id_encabezado_sin_tarifa").val();
            var es_paquete                 = ($("#wliq_paquete").attr("checked") == 'checked') ? "on": "off";
            // var wbaseliquidacion           = $("#wbaseliquidacion").val();

            var campos_ok = true;


            if(wtipo_anestesia_cx == '')
            {
                campos_ok = false;
                $("#wtipo_anestesia_cx").addClass("campoRequerido");
            }

            if(wtiempo_sala_recuperarcion == '')
            {
                campos_ok = false;
                $("#wtiempo_sala_recuperarcion").addClass("campoRequerido");
            }

            if(wtiempo_uso_minutos == '')
            {
                campos_ok = false;
                $("#wtiempo_uso_minutos").addClass("campoRequerido");
            }

            if(wtiempo_minutos_cx == '')
            {
                campos_ok = false;
                $("#wtiempo_minutos_cx").addClass("campoRequerido");
            }

            if(campos_ok && $("#whistoriaLocal").val() == "" || $("#whistoriaLocal").val() != $("#whistoria_tal").val())
            {
                campos_ok = false;
                alert("Debe escribir un número de historia correcto");
            }

            // if(wbaseliquidacion == '' && $("#tabla_lista_cxs").find("tr[id^=tr_liqAdd_cxs_]").length > 1)
            // {
            //     campos_ok = false;
            //     $("#wbaseliquidacion").addClass("campoRequerido");
            // }

            if(wnumvias == '' || wnumvias == 0)
            {
                campos_ok = false;
                $("#wnumvias").addClass("campoRequerido");
            }

            if(campos_ok)
            {
                var wpolitraumatizado = $("#wpolitraumatizado").val(); //($("#wpolitraumatizado").is(":checked")) ? 'on': 'off';
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                     : 'load',
                        form                       : 'generar_datos_liquidar',
                        consultaAjax               : '',
                        arr_datos_liquidar         : $("#arr_datos_liquidar").val(),
                        arr_datos_liquidados       : $("#arr_datos_liquidados").val(),
                        arr_extras                 : $("#arr_extras").val(),
                        arr_conceptos_nombres      : $("#arr_conceptos_64").val(),
                        wnumvias                   : $("#wnumvias").val(),
                        wtipo_anestesia_cx         : wtipo_anestesia_cx,
                        wtiempo_sala_recuperarcion : wtiempo_sala_recuperarcion,
                        wtiempo_uso_minutos        : wtiempo_uso_minutos,
                        wtiempo_minutos_cx         : wtiempo_minutos_cx,
                        wcentro_costo              : $("#wcco_tal").val(),
                        wtarifa_empresa            : $("#tarifa_original_tal").val(),
                        wcod_empresa               : $("#responsable_original_tal").val(),
                        wnomemp_tal                : $("#wnomemp_tal").val(),
                        wfecha_cargo               : $("#wfecha_cargo").val(),
                        whora_cargo                : $("#whora_cargo").val(),
                        wtipo_ingreso              : $("#wtipo_ingreso_tal").val(),
                        wtipo_ingreso_nom          : $("#wtipo_ingreso_nom_tal").val(),
                        wpolitraumatizado          : wpolitraumatizado,
                                                   // wbaseliquidacion           : wbaseliquidacion,
                        wprocedimiento_add         : wprocedimiento_add,
                        wconcepto_add              : wconcepto_add,
                        tipoEmpresa                : $("#tipoEmpresa_tal").val(),
                        whistoria                  : $("#whistoriaLocal").val(),
                        wing                       : $("#wingLocal").val(),
                        nitEmpresa                 : $("#nitEmpresa_tal").val(),
                        id_concepto_uso_equipos    : $("#id_concepto_uso_equipos").val(),
                        id_concepto_examenes       : $("#id_concepto_examenes").val(),
                        add_equipo                 : $("#add_equipo").attr("codigo"),
                        add_examen                 : $("#add_examen").attr("codigo"),
                        add_equipo_nombre          : $("#add_equipo").attr("nombre"),
                        add_examen_nombre          : $("#add_examen").attr("nombre"),
                        tabla_lista_cxs            : $("#tabla_lista_cxs").html(),
                        id_encabezado_sin_tarifa   : id_encabezado_sin_tarifa,
                        wser                       : $("#wser_tal").val(),
                        // ccoActualPac               : $("#ccoActualPac_tal").val(),
                        es_paquete                 : es_paquete
                    },
                    function(data){

                        guardarDatosTemporales('000160');

                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#id_encabezado_sin_tarifa").val(data.id_encabezado_sin_tarifa);
                            $("#arr_CARGOS_PARA_GRABAR").val(data.arr_CARGOS_PARA_GRABAR);
                            $("#arr_datos_liquidados").val(data.arr_datos_liquidados);
                            $("#arr_extras").val(data.arr_extras);
                            $("#arr_procedimientos_orden_liquidar").val(data.arr_procedimientos_orden_liquidar);
                            $("#div_cont_liquidacion_modal").html(data.html);

                            if(data.mensaje != '') { alert(data.mensaje); }

                            /*$("#div_cont_liquidacion_modal" ).dialog({
                                show: {
                                    effect: "blind",
                                    duration: 100
                                },
                                hide: {
                                    effect: "blind",
                                    duration: 100
                                },
                                // maxHeight:600,
                                height:500,
                                // height: 'auto',
                                width:  'auto',
                                dialogClass: 'fixed-dialog',
                                modal: true,
                                title: "Liquidación de la cirugía: "
                                // ,open:function(){
                                //         var s = $('#cont_dlle_modal').height();
                                //         var s2 = $(this).dialog( "option", "maxHeight" );
                                //         if(s < s2){
                                //             $(this).height(s);
                                //           }
                                //       }
                            });*/

                            return data;
                        }
                    },
                    "json"
                ).done(function(data){
                    $("#btn_cancelar_cerrar_liquidacion").focus();
                    crearAutocomplete('arr_terceros_especialidad', 'liq_autocomplete', '', '',1);
                    crearAutocomplete('arr_conceptos', 'concepto_autocomplete', '', '',0);
                    crearAutocomplete('arr_examenes', 'add_examen', '', '',1);
                    crearAutocomplete('arr_equipos', 'add_equipo', '', '',1);

                    $('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

                    $('.modificaLiquidado').on({
                        focusout: function(e) {
                            // console.log($(this).val());
                            if($(this).val().replace(/ /gi, "") == '')
                            {
                                $(this).val("");
                                $(this).attr("codigo","");
                                $(this).attr("nombre","");
                            }
                            var campo = $(this);
                            actualizarDatoLiquidado(campo,'');
                        },
                        click: function() {
                            var campo = $(this);
                            /*if(campo.attr("type") == 'checkbox')
                            {
                                var valor = "off";
                                if(campo.is(":checked")) { var valor = "on"; }
                                actualizarDatoLiquidado(campo,valor);
                            }*/
                        }
                    });

                    $('.liq_autocomplete, .add_equipos_examenes').on({
                        focusout: function(e) {
                            if($(this).val().replace(/ /gi, "") == '')
                            {
                                $(this).val("");
                                $(this).attr("codigo","");
                                $(this).attr("nombre","");
                            }
                            else
                            {
                                $(this).val($(this).attr("nombre"));
                            }
                        }
                    });

                    $('.concepto_autocomplete').on({
                        focusout: function(e) {
                            if($(this).val().replace(/ /gi, "") == '')
                            {
                                $(this).val("");
                                $(this).attr("codigo","");
                                $(this).attr("nombre","");
                                ocultar_seleccion_adicionor_procedimiento();
                                $("#add_procedimiento").children("option").show(); // Si un concepto ya tiene un procedimiento entonces se oculta ese procedimiento, esta línea de código es para volverlos a mostrar todos.
                            }
                            else
                            {
                                $(this).val($(this).attr("nombre"));
                            }
                        }
                    });

                    desbloquearBoton('Liquidar');
                    // Desbloquear el boton de adicionar nuevo concepto.
                    $("#btn_add_concepto_"+wprocedimiento_add).removeClass("cargando");
                    $("#btn_add_concepto_"+wprocedimiento_add).removeAttr("disabled");
                    $("#btn_add_concepto_"+wprocedimiento_add).html("Adicionar");

                    // if(ConceptoInventario == 'on')
                    {
                        // esta array data.procedimientos_materiales tiene los conceptos que tienen inventario y para los cuales se les debe pintar la sección de medicamentos y materiales.
                        var arr_pro = data.procedimientos_materiales;
                        for (var cod_dato in arr_pro)
                        {
                            llamar_insumos("contenedor_inventario_"+cod_dato, 'ok', cod_dato, arr_pro[cod_dato], $("#responsable_original_tal"), $("#wnomemp_tal").val(), $("#tarifa_original_tal").val(), $("#div_tarifa_tal").val())
                        }
                    }

                    inicializarFlotantes(); // Inicializa el detalle de la información de la liquidación

                    ocultar_seleccion_adicionor_procedimiento();

                    var control_ultima_actualizacion = $("#control_ultima_actualizacion").val();
                    if(control_ultima_actualizacion == 'on')
                    {
                        // $("#control_ultima_actualizacion").val("on");
                        guardarLiquidacion($("#btn_grabar_liquidacion"));
                    }

                    // verificar que si faltan tarifas entonces inactivar el boton de guardar liquidación
                    if(data.wfaltan_tarifas == 'on')
                    {
                        $("#btn_grabar_liquidacion").attr("disabled","disabled");
                        $("#div_msj_falta_tarifa").html("<div style='background-color:#ffffcc; width: 530px; margin-left: 28%;' >Hay cargos sin tarífa, no se puede grabar la liquidación hasta crear las tarífas que faltan.</div>");
                    }

                    recargarDivTemporaSinTarifa($('#whistoriaLocal').val(), $('#wingLocal').val());
                });
            }
            else
            {
                desbloquearBoton('Liquidar');
                // Desbloquear el boton de adicionar nuevo concepto.
                $("#btn_add_concepto_"+wprocedimiento_add).removeClass("cargando");
                $("#btn_add_concepto_"+wprocedimiento_add).removeAttr("disabled");
                $("#btn_add_concepto_"+wprocedimiento_add).html("Adicionar");
                alert("Faltan campos por llenar");
            }
        }

        /**
         * [guardarDatosTemporales Esta función se encarga de guardar todos los datos principales necesarios para poder recuperar de nuevo
         *                         una liquidación en curso despues de un cierre inesperado, cambio de programa o recarga accidental.]
         * @return {[type]} [description]
         */
        function guardarDatosTemporales(temporal)
        {
            var es_paquete = ($("#wliq_paquete").attr("checked") == 'checked') ? "on": "off";
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion                     : "update",
                    form                       : "guardar_temporal",
                    consultaAjax               : "",
                    arr_datos_liquidar         : $("#arr_datos_liquidar").val(),
                    arr_datos_liquidados       : $("#arr_datos_liquidados").val(),
                    arr_extras                 : $("#arr_extras").val(),
                    wnumvias                   : $("#wnumvias").val(),
                    wtipo_anestesia_cx         : $("#wtipo_anestesia_cx").val(),
                    wtiempo_sala_recuperarcion : $("#wtiempo_sala_recuperarcion").val(),
                    wtiempo_uso_minutos        : $("#wtiempo_uso_minutos").val(),
                    wtiempo_minutos_cx         : $("#wtiempo_minutos_cx").val(),
                    tabla_lista_cxs            : $("#tabla_lista_cxs").html(),
                    whistoria                  : $("#whistoriaLocal").val(),
                    wing                       : $("#wingLocal").val(),
                    wfecha_cargo               : $("#wfecha_cargo").val(),
                    whora_cargo                : $("#whora_cargo").val(),
                    wpolitraumatizado          : $("#wpolitraumatizado").val(),
                    arr_CARGOS_PARA_GRABAR     : $("#arr_CARGOS_PARA_GRABAR").val(),
                    id_encabezado_sin_tarifa   : $("#id_encabezado_sin_tarifa").val(),
                    temporal                   : temporal,
                    wliq_paquete               : es_paquete
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        //
                    }
                },
                "json"
            ).done(function(data){
                $("#div_recuperacion_datos").html(""); // Si habían cambos temporales recien cargados entonces eliminelos para no ternerlos en cuenta y poder controlar
                                                        // mejor el campo de liquidar paquetes para saber si se reinicia o no la liquidación, en parte depende si
                                                        // el buffer de datos recuperados esta vacío o no.
            });
        }

        /**
         * [ocultar_seleccion_adicionor_procedimiento si esta desplegado el select de procedimientos para adicionar conceptos, entonces esta función la oculatará]
         * @return {[type]} [description]
         */
        function ocultar_seleccion_adicionor_procedimiento()
        {
            $("#add_procedimiento").val("");
            $("#td_add_procedimiento").hide(300); // Si no se ha seleccionado conceto entonces el campo de procedimiento no aparece
        }

        // function validarSeleccionable()

        function agregarConceptoNuevo(campo_procedimiento, campo_concepto, tabla_contenedor)
        {
            var wprocedimiento_add       = $("#"+campo_procedimiento).val();
            var nombre_procedimiento_add = $("#"+campo_procedimiento+" option:selected").text();
            var wconcepto_add            = $("#"+campo_concepto).attr("codigo");
            var nombre_concepto          = $("#"+campo_concepto).attr("nombre");
            bloquearBoton(("#btn_add_concepto_"+wprocedimiento_add));

            // Validar si debe escribir equipo o exámen
            requiere_equipo_examen = false;
            if(wconcepto_add == $("#id_concepto_examenes").val())
            {
                codigo_requerido = $("#add_examen").attr("codigo");
                if(codigo_requerido.replace(/ /gi, "") == '')
                {
                    requiere_equipo_examen = true;
                    msj_requiere_eq_ex = "Requiere seleccionar un exámen";
                }
            }
            else if(wconcepto_add == $("#id_concepto_uso_equipos").val())
            {
                codigo_requerido = $("#add_equipo").attr("codigo");
                if(codigo_requerido.replace(/ /gi, "") == '')
                {
                    requiere_equipo_examen = true;
                    msj_requiere_eq_ex = "Requiere seleccionar un equipo";
                }
            }

            if(wconcepto_add != '' && !requiere_equipo_examen)
            {
                generarLiquidar($("#btn_liquidar_pop"), wprocedimiento_add, wconcepto_add);
                // desbloquearBoton('Liquidar');
            }
            else
            {
                if(wconcepto_add=='') alert("Debe escribir el nombre de un concepto");
                else if(requiere_equipo_examen) alert(msj_requiere_eq_ex);
                desbloquearBoton('Adicionar');
            }
        }

        /*
            Esta función se encarga de actualizar el valor del campo que se modifique en la interfaz, por ejemplo si cambia el tercero se actualiza este valor en el array
            de liquidados que es el que finalmente se va a guardar en el base de datos.
            Tambien si se da clic en demarcar grabar se actualiza ese valor en el array de liquidados.
        */
        function actualizarDatoLiquidado(campo,valor_nuevo_def)
        {
            var etiqueta             = campo.attr("etiqueta");
            var wprocedimiento       = campo.attr("wprocedimiento");
            var wconcepto            = campo.attr("wconcepto");
            var wequip_examen        = campo.attr("wequip_examen");
            // var wconsec              = campo.attr("wconsec");
            var fila_tr              = wprocedimiento+'_'+wconcepto+'_'+wequip_examen;//+'_'+wconsec;
            var valor_nuevo          = (valor_nuevo_def=='') ? campo.val() : valor_nuevo_def;

            if(campo.attr("type") == 'checkbox')
            {
                var valor = "off";
                if(campo.is(":checked")) { valor = "on"; }
                valor_nuevo = valor;
            }
            // console.log(campo.attr("type")+'  -  '+valor_nuevo);

            var wtercero             = '';
            var wtercero_nombre      = '';
            var wespecialidad        = '';
            var wespecialidad_nombre = '';

            // Si en la fila hay un autocomplete entonces esta pidiendo tercero, en este caso debe capturar
            // el codigo del tercero y el codigo de la especialidad
            // console.log(fila_tr);
            // console.log($("#"+fila_tr).find('liq_autocomplete').length);
            if((etiqueta == 'wtercero' || etiqueta == 'wespecialidad')) //$("#"+fila_tr).find('.liq_autocomplete').length > 0 &&
            {
                wtercero             = $("#wtercero_"+fila_tr).attr("codigo");
                wtercero_nombre      = $("#wtercero_"+fila_tr).attr("nombre");
                wespecialidad        = $("#wespecialidad_"+fila_tr).val();
                wespecialidad_nombre = $("#wespecialidad_"+fila_tr+" option:selected").text();
                if(etiqueta == 'wtercero')
                {
                    $("#wtercero_"+fila_tr).removeAttr("title");
                    $("#wtercero_"+fila_tr).removeClass("tooltip");
                    valor_nuevo = wtercero; // el valor de campo a actualizar en el array de liquidados sería el mismo campo del tercero
                    if(valor_nuevo == '')
                    {
                        wespecialidad = '';
                        wespecialidad_nombre = '';
                        $("#wespecialidad_"+fila_tr).find("option").remove();
                    }
                }
                else
                {
                    valor_nuevo = wespecialidad; // el valor de campo a actualizar en el array de liquidados sería el mismo campo del tercero
                    if(valor_nuevo == '')
                    {
                        wespecialidad = '';
                        wespecialidad_nombre = '';
                        $("#wespecialidad_"+fila_tr).find("option").remove();

                    }
                }
            }

            if(etiqueta == 'wvalor_digitado')
            {
                valor_nuevo = campo.attr("valorAnterior");
            }

            // Esta función bloquea los botones que hace peticiones ajax hasta que las instrucciones en la función actual terminen, esto es con del fin de esperar una respuesta desde
            // el servidor y no permitir que se de clic varias veces sobre un botón.
            bloquearBoton($("#btn_grabar_liquidacion"));

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion               : 'update',
                    form                 : 'modificar_datos_liquidados',
                    consultaAjax         : '',
                    // arr_datos_liquidar   : $("#arr_datos_liquidar").val(),
                    arr_datos_liquidados : $("#arr_datos_liquidados").val(),
                    wprocedimiento       : wprocedimiento,
                    wconcepto            : wconcepto,
                    wequip_examen        : wequip_examen,
                    valor_nuevo          : valor_nuevo,
                    wtercero             : wtercero,
                    wespecialidad        : wespecialidad,
                    // wconsec              : wconsec,
                    wtercero_nombre      : wtercero_nombre,
                    wespecialidad_nombre : wespecialidad_nombre,
                    etiqueta             : etiqueta
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#arr_datos_liquidados").val(data.arr_datos_liquidados);
                        /*
                            td_porcentaje_
                            td_wvalor_
                            td_wvalorfinal_
                            td_grabable_
                        */
                        // resetStylePrefijo(prefijoFila);
                    }
                },
                "json"
            ).done(function(){
                desbloquearBoton('Grabar liquidación');

                //inactivar botón grabar liquidación para que quede activo el boton RELIQUIDAR
                $("#btn_reliquidacion").show();
                $("#btn_grabar_liquidacion").attr("disabled","disabled");
            });
        }

        function cargarMedicoSeleccionado(check_id_campo_ter, id_campo_ter, id_campo_esp)
        {
            var campoChk = $("#"+check_id_campo_ter);
            $("#"+id_campo_ter).focus();

            if(campoChk.is(":checked"))
            {
                var cod_medico       = $("#"+check_id_campo_ter).attr("codigo_medico");
                var nom_medico       = $("#"+check_id_campo_ter).attr("nombre_medico");
                var cod_especialidad = $("#"+check_id_campo_ter).attr("codigo_esp");
                var nom_especialidad = $("#"+check_id_campo_ter).attr("nombre_esp");
                $("#"+id_campo_ter).val(nom_medico);
                $("#"+id_campo_ter).attr("codigo",cod_medico);
                $("#"+id_campo_ter).attr("nombre",nom_medico);

                var options_esp = '<option value="'+cod_especialidad+'" >'+nom_especialidad+'</option>';
                $("#"+id_campo_esp).find("option").remove();
                $("#"+id_campo_esp).append(options_esp);
                $("#"+id_campo_esp).attr("codigo",cod_especialidad);
                $("#"+id_campo_esp).attr("nombre",nom_especialidad);
                $("#"+id_campo_esp).val(cod_especialidad);

            }
            else
            {
                $("#"+check_id_campo_ter).attr("codigo","");
                $("#"+check_id_campo_ter).attr("nombre","");
                $("#"+id_campo_ter).val("");
                $("#"+id_campo_esp).attr("codigo","");
                $("#"+id_campo_esp).attr("nombre","");
                $("#"+id_campo_esp).find("option").remove();
            }
            $("#btn_cancelar_cerrar_liquidacion").focus();
        }

        function cerrarLiquidacion()
        {
            // $('#div_cont_liquidacion_modal').dialog('close');
            $("#btn_liquidar_pop").focus();
        }

        function reliquidar_cx(btn)
        {
            $("#btn_liquidar_pop").click();
        }

        function guardarLiquidacion(this_btn)
        {
            var whistoria_tal          = $("#whistoria_tal").val();
            var wing_tal               = $("#wing_tal").val();
            var campos_vacios_terceros = false;
            $("div[id=cont_dlle_modal]").find(".vacios_terceros").each(function(){
                var campotercero   = $(this).attr("codigo");
                var wprocedimiento = $(this).attr("wprocedimiento");
                var wconcepto      = $(this).attr("wconcepto");
                var btn_grabar = $("#wgrabar_"+wprocedimiento+"_"+wconcepto+'_').attr("checked");
                if(btn_grabar == 'checked' && campotercero.replace(/ /gi, "") == '')
                {
                    campos_vacios_terceros = true;
                }
            });

            if(!campos_vacios_terceros)
            {
                // si esta en on significa que ya se realizó automaticamente una actualizacion al array global de cargos justo antes de ir a grabar todos esos cargos
                // gracias a este parámetro se llama a al función de liquidar y luego desde esa función se llama nuevamente esta funcion de grabarLiquidacion
                var control_ultima_actualizacion = $("#control_ultima_actualizacion").val();

                if(control_ultima_actualizacion != 'on')
                {
                    // Preliquidar primero para poder actualizar el array global de grabación para poder los ultimos cambios de elección de terceros
                    // o cambios en checks grabar o facturable/no facturable alcancen a quedar en actualizados en ese array global.
                    $("#control_ultima_actualizacion").val("on");
                    desbloquearBoton('Grabar liquidación');
                    generarLiquidar($("#btn_liquidar_pop"), "", "")
                }
                else
                {
                    $("#control_ultima_actualizacion").val("");
                    bloquearBoton(this_btn);

                    var wbaseliquidacion = $("#wbaseliquidacion").val();
                    var es_paquete       = ($("#wliq_paquete").is(":checked")) ? "on": "off";

                    $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                        {
                             accion                            : 'insert',
                             form                              : 'guardar_datos_liquidados',
                             consultaAjax                      : '',
                             whistoria                         : $("#whistoria_tal").val(),
                             wing                              : $("#wing_tal").val(),
                             wno1                              : $("#wno1_tal").val(),
                             wno2                              : $("#wno2_tal").val(),
                             wap1                              : $("#wap1_tal").val(),
                             wap2                              : $("#wap2_tal").val(),
                             wdoc                              : $("#wdoc_tal").val(),
                             wcodemp                           : $("#responsable_original_tal").val(),
                             wnomemp                           : $("#wnomemp_tal").val(),
                             wfecing                           : $("#wfecing_tal").val(),
                             wtar                              : $("#tarifa_original_tal").val(),
                             wser                              : $("#wser_tal").val(),

                             arr_datos_liquidados              : $("#arr_datos_liquidados").val(),
                             arr_CARGOS_PARA_GRABAR            : $("#arr_CARGOS_PARA_GRABAR").val(),
                             arr_procedimientos_orden_liquidar : $("#arr_procedimientos_orden_liquidar").val(),
                             wfecha_cargo                      : $("#wfecha_cargo").val(),
                             wtipo_ingreso                     : $("#wtipo_ingreso_tal_tal").val(),
                             whora_cargo                       : $("#whora_cargo").val(),

                             wcco                              : $("#wcco_tal").val(),
                             wccogra                           : $("#wccogra").val(),
                             wfeccar                           : $("#wfeccar").val(),
                             wbod                              : $("#wbod_tal").val(),
                             wtip_paciente                     : $("#wtip_paciente_tal").val(),
                             wbasedato_movhos                  : $("#wbasedato_movhos").val(),
                             wbaseliquidacion                  : wbaseliquidacion,
                             id_concepto_uso_equipos           : $("#id_concepto_uso_equipos").val(),
                             id_concepto_examenes              : $("#id_concepto_examenes").val(),
                             tipoEmpresa                       : $("#tipoEmpresa_tal").val(),
                             nitEmpresa                        : $("#nitEmpresa_tal").val(),
                             ccoActualPac                      : $("#ccoActualPac_tal").val(),
                             es_paquete                        : es_paquete
                             },
                        function(data){
                            if(isset(data.error) && data.error == 1)
                            {
                                alert(data.mensaje_local);
                                $("#div_mensajes_alerta").html(data.mensaje);

                                $("#div_mensajes_alerta").show(0, function(){
                                                                // $(this).hide(2000);
                                });

                                if(data.mensaje != '')
                                {
                                    $("#div_mensajes_alerta").css({"border":"1px solid red"});
                                }
                                else
                                {
                                    $("#div_mensajes_alerta").css({"border":"0px"});
                                }
                            }
                            else
                            {
                                // limpiar();
                                // $("#tabla_lista_cxs tr[id^=tr_liqAdd_cxs_]").remove();
                                // $("#arr_datos_liquidar").val($("#arr_datos_liquidar_temp").val());
                                // $("#arr_datos_liquidados").val($("#arr_vacio").val());
                                // $("#arr_procedimientos_orden_liquidar").val($("#arr_vacio").val());
                                // $("#arr_CARGOS_PARA_GRABAR").val($("#arr_vacio").val());
                                // $("#arr_extras").val($("#arr_vacio").val()); // Se llena cuando se agregan conceptos extras que no estan en la plantilla.
                                // // $("#div_cont_liquidacion_modal").hide("slow");
                                // $("#div_cont_liquidacion_modal").html("");

                                $("#div_mensajes_alerta").html(data.mensaje);
                                if(data.error_cargo == 1)
                                {
                                    $("#div_mensajes_alerta").css({"border":"1px solid red"});
                                }
                                else
                                {
                                    $("#div_mensajes_alerta").css({"border":"0px"});
                                }

                                $("#div_mensajes_alerta").show();
                                alert(data.mensaje_local);
                                $("#div_mensajes_alerta").show(0, function(){
                                                                $(this).hide(4000);
                                });

                                congelarCuentaPaciente('off');
                            }
                            return data;
                        },
                        "json"
                    ).done(function(data){
                        // $('#div_cont_liquidacion_modal').dialog('close');
                        // desbloquearBoton('');
                        desbloquearBoton('Grabar liquidación');
                        limpiarPantalla(true);

                        $("#whistoria_tal").val(whistoria_tal);
                        $("#wing_tal").val(wing_tal);

                        cargarDatosPaciente('whistoria','');
                        // recargarDetalleLiquidaciones();

                        if(isset(data.error) && data.error == 0)
                        {
                            // location.reload(true);
                            // window.location.reload(true);
                        }
                    });
                }
            }
            else
            {
                $("#control_ultima_actualizacion").val("");
                alert("No se han llenado todos los campos de tercero!");
            }
        }

        function recargarDetalleLiquidaciones()
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion       : 'update',
                    form         : 'recargar_detalle',
                    consultaAjax : '',
                    whistoria    : $("#whistoria_tal").val(),
                    wing         : $("#wing_tal").val()
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#div_contenedor_detalle").html(data.html);
                    }
                },
                "json"
            ).done(function(){
                //
            });
        }

        function cargarSelectEspecialidades( cadena , wespecialidad, elem_this)
        {
            var especialidades = cadena.split(",");
            var html_options = '<option value="">Seleccione..</option>';
            var frst = '';
            for( var i in especialidades ){
                var especialidad = especialidades[i].split("-");
                var sltd = '';
                if(frst == '')
                {
                    frst = especialidad[0];
                    sltd = 'selected="selected"';
                }
                html_options+='<option value="'+especialidad[0]+'" '+sltd+' >'+especialidad[1]+'</option>';
            }
            if($("#"+wespecialidad).length > 0)
            {
                $("#"+wespecialidad).html( html_options );
                $("#"+wespecialidad).removeClass('campoRequerido');
            }
            else if($("."+wespecialidad).length > 0)
            {
                var id_autocomp = elem_this.attr('id');
                var id_cmp_especialidad = $("#"+id_autocomp).parent().next('td').find('.liq_depend_autocomplete').attr('id');
                $("#"+id_cmp_especialidad).html( html_options );
                $("#"+id_cmp_especialidad).removeClass('campoRequerido');
                // console.log(elem_this.attr('id'));
                // console.log(elem_this.next('input'));
            }
        }

        $(function(){
            $('.numerico').on({
                keypress: function(e) {
                    var r = soloNumeros(e);
                    if(r==true)
                    {
                        var codeentr = (e.which) ? e.which : e.keyCode; /*if(codeentr == 13) { buscarDatosBasicos(); }*/
                        return true;
                    }
                    return false;
                }
            });

            $('#worgano').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                        $(this).attr("bilateral","off");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });


            $('#wprocedimiento, #wespecialistas').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });

            $('.requerido').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).addClass("campoRequerido");
                    }
                    else
                    {
                        $(this).removeClass("campoRequerido");
                    }
                }
            });

            $('.classminutos').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                    }
                    else
                    {
                        var valor_minuto = $(this).val();
                        if(valor_minuto > 0 && valor_minuto < 15)
                        {
                            //$(this).val();
                            alert("Recuerde que el número debe ser en minutos y no en horas, está escribiendo un número muy bajo.");
                        }
                    }
                }
            });
        });

        function crearAutocomplete(arr_opciones_seleccion, campo_autocomplete, codigo_default, nombre_default, limite_buscar)
        {
            $("#"+campo_autocomplete).val(nombre_default);
            $("#"+campo_autocomplete).attr("codigo",codigo_default);
            $("#"+campo_autocomplete).attr("nombre",nombre_default);

            arr_datos = new Array();
            //var datos = arr_wempresp;//eval( $("#arr_wempresp").val() );
            var datos = eval('(' + $("#"+arr_opciones_seleccion).val() + ')');
            var index = -1;
            for (var CodVal in datos)
            {
                index++;
                if(arr_opciones_seleccion == 'arr_terceros_especialidad')
                {
                    arr_datos[index]                = {};
                    arr_datos[index].value          = CodVal+'-'+datos[CodVal]['nombre'];
                    arr_datos[index].label          = CodVal+'-'+datos[CodVal]['nombre'];
                    arr_datos[index].codigo         = CodVal;
                    arr_datos[index].nombre         = CodVal+'-'+datos[CodVal]['nombre'];
                    arr_datos[index].especialidades = datos[CodVal]['especialidad'];
                }
                else
                {
                    arr_datos[index] = {};
                    arr_datos[index].value  = CodVal+'-'+datos[CodVal];
                    arr_datos[index].label  = CodVal+'-'+datos[CodVal];
                    arr_datos[index].codigo = CodVal;
                    arr_datos[index].nombre = CodVal+'-'+datos[CodVal];
                }
            }

            // if( arr_opciones_seleccion == "arr_procedimientos")
            // {
            //     limite_buscar = 0;
            // }

            // console.log(arr_datos);
            if($("#"+campo_autocomplete).length > 0)
            {
                $("#"+campo_autocomplete).autocomplete({
                        source: arr_datos, minLength : limite_buscar,
                        select: function( event, ui ) {
                                    // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                                    var cod_sel = ui.item.codigo;
                                    var nom_sel = ui.item.nombre;
                                    $("#"+campo_autocomplete).attr("codigo",cod_sel);
                                    $("#"+campo_autocomplete).attr("nombre",nom_sel);
                                    // cargarConceptosPorProcedimientos(cod_sel);
                                }
                });
            }
            else if($("."+campo_autocomplete).length > 0  || campo_autocomplete == 'concepto_autocomplete')
            {
                $("."+campo_autocomplete).autocomplete({
                        source: arr_datos, minLength : limite_buscar,
                        select: function( event, ui ) {
                                    var cod_sel = ui.item.codigo;
                                    var nom_sel = ui.item.nombre;
                                    var id_el = $(this).attr("id");
                                    $("#"+id_el).attr("codigo",cod_sel);
                                    $("#"+id_el).attr("nombre",nom_sel);
                                    $("#add_procedimiento").children("option").removeAttr("disabled").show(); // Si una concepto ya lo tiene un procedimiento entonces se oculta ese procedimiento, esta línea de código es para volverlos a mostrar todos.
                                    $("#td_add_equipo").hide();
                                    $("#td_add_examen").hide();

                                    $("#add_equipo").val("");
                                    $("#add_examen").val("");

                                    if(campo_autocomplete != 'concepto_autocomplete')
                                    {
                                        cargarSelectEspecialidades( ui.item.especialidades , 'liq_autocomplete', $(this));
                                    }
                                    else
                                    {
                                        //Validar si se trata del concepto de uso de equipos, si es así entonces consultar todos los equipos para seleccionar
                                        if($("#id_concepto_examenes").val() == cod_sel) //'E'
                                        {
                                            // Functión para consultar exámenes
                                            // select add_examen
                                            consultarListaExamenesEquipos(cod_sel, 'E', 'add_examen');
                                        }
                                        else if($("#id_concepto_uso_equipos").val() == cod_sel) //'Q'
                                        {
                                            // Functión para consultar equipos
                                            // select add_equipo
                                            consultarListaExamenesEquipos(cod_sel, 'Q', 'add_equipo');
                                        }


                                        // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                                        // $("#add_procedimiento > option").html();
                                        $("#add_procedimiento").children("option[lista*=" + cod_sel + "]").attr("disabled","disabled").hide();

                                        total_opciones_novacias = $("#add_procedimiento").children("option[lista]").length;
                                        opciones_tienen_elconcepto = $("#add_procedimiento").find("option[lista*=" + cod_sel + "]").length;
                                        contador_op_ctrl = total_opciones_novacias-opciones_tienen_elconcepto;

                                        // si la cantidad de procedimientos que no tienen el conceptos es igual a la misma cantidad de procedimientos como tal?
                                        // entonces no desplegar el select de procedimientos y simplemente envíese el concepto con procedimiento vacía y así se le agregará solo al
                                        // primer procedimiento.
                                        if(contador_op_ctrl == total_opciones_novacias)// si la cantidad de opciones que no tienen el concepto es igual a la cantidad total de opciones con valor no vacío, entonces no mostrar el campo precedimiento.
                                        {
                                            $("#td_add_procedimiento").hide();
                                        }
                                        else if(contador_op_ctrl > 0)
                                        {
                                            $("#td_add_procedimiento").show();
                                        }

                                        // Se debión poner que siempre que sea equipos o examenes se muestra el select de procedimientos
                                        // porque si se iba a adicionar otro equipo o exámen entonces no mostraba a quien se le podia asignar
                                        // porque ya tenia asociado el conceto examenes y equipos
                                        if($("#id_concepto_examenes").val() == cod_sel || $("#id_concepto_uso_equipos").val() == cod_sel)
                                        {
                                            $("#td_add_procedimiento").show();
                                            $("#add_procedimiento").children("option").removeAttr("disabled").show();
                                        }
                                    }


                                    // cargarConceptosPorProcedimientos(cod_sel);
                                }
                });
            }
        }

        function consultarListaExamenesEquipos(codigo_concepto, tipo_procedimiento, campo_select)
        {
            if(tipo_procedimiento == 'E')
            {
                $("#td_add_examen").show();
                // crearAutocomplete('arr_examenes', 'add_equipo', '', '', 1);
            }
            if(tipo_procedimiento == 'Q')
            {
                $("#td_add_equipo").show();
                // crearAutocomplete('arr_equipos', 'add_examen', '', '', 1);
            }
        }

        function anularActoQuirurgico(hddn_cargo_anular,dif_acto)
        {
            var msj_anular = "Va a anular todos los cargos de este acto quirúrgico\n\n¿Desea continuar?";
            if(confirm(msj_anular))
            {
                var ids_cargos_106 = "";
                var separador = "";
                $("#tabla_lista_cxs_"+dif_acto).find("[id^="+hddn_cargo_anular+dif_acto+"]").each(function(){
                    var id_cargo = $(this).attr("id_cargo_106");
                    ids_cargos_106 = ids_cargos_106+separador+id_cargo;
                    separador = '|';
                });
                // console.log(ids_cargos_106);

                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion         : 'update',
                        form           : 'anular_cargos_cirugia',
                        consultaAjax   : '',
                        dif_acto       : dif_acto,
                        ids_cargos_106 : ids_cargos_106
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#div_contenedor_evento_qx_"+dif_acto).hide();
                            $("#div_contenedor_evento_qx_"+dif_acto).html("");
                            alert(data.mensaje);
                        }
                    },
                    "json"
                ).done(function(){
                    //
                });
            }
        }

        //--------------------------------------------------------------------------------------------------
        // -->  Validar si la cuenta se encuentra congelada, ya que si ocurrio un cierre inesperado
        //      del programa, la cuenta puede quedar congelada y no se puede permitir que graben cargos
        //--------------------------------------------------------------------------------------------------
        function validarEstadoDeCuentaCongelada(desdeSelectorConcepto)
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
            {
                accion       : 'update',
                form         : 'estadoCuentaCongelada',
                consultaAjax : '',
                wemp_pmla    : $('#wemp_pmla_tal').val(),
                historia     : $("#whistoriaLocal").val(),
                ingreso      : $("#wingLocal").val()
            }, function(data){
               // --> Si la cuenta se encuentra congelada
                var control = true;
                if(data.Ecoest == 'on')
                {
                    // --> si el usuario que la congelo es diferente al actual
                        // console.log("esta bloqueado?");
                    if(data.Ecousu != data.wuse)
                    {
                        // --> No se permiten grabar cargos, ya que la cuenta esta congelada por otro usuario
                        var mensaje =   '<br>'+
                                        ' En este momento no se le pueden grabar cargos al paciente.<br>'+
                                        ' La cuenta se encuentra congelada por <b>'+data.nomUsuario+'</b>'+
                                        ', en un proceso de <b>liquidacion de '+data.Nomtip+'</b>.';

                        // --> Mostrar mensaje
                        $( '#divMsjCongelar').html(mensaje);
                        $( '#divMsjCongelar').dialog({
                            width:  500,
                            dialogClass: 'fixed-dialog',
                            modal: true,
                            title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
                            close: function( event, ui ) {
                                if(desdeSelectorConcepto)
                                {
                                    // $("#busc_concepto_1").val('');
                                    // $("#busc_concepto_1").attr('valor', '');
                                    // $("#busc_concepto_1").attr("nombre", '');
                                    // $("#busc_concepto_1").attr("polManejoTerceros", '');
                                }
                                else
                                {
                                }
                                    limpiarPantalla(true);
                                // limpiarPantalla();
                            }
                        });
                        control = false;
                    }
                    // --> Si es el mismo usuario que la congelo
                    else
                    {
                        if(!desdeSelectorConcepto)
                        {
                            // --> Si el usuario la congelo desde un programa diferente al de cargos
                            if(data.Ecotip != 'QX')
                            {
                                mensaje = "Usted tiene una liquidación de <b>"+data.Nomtip+"</b> en proceso.<br>Para conservar dicho proceso de Click en <b>Aceptar</b> y luego abra su programa correspondiente.<br>Si desea cancelar el proceso y poder grabarle cargos al paciente de Click en <b>Cancelar</b>.";
                                $( '#divMsjCongelar').html(mensaje);
                                $( '#divMsjCongelar').dialog({
                                    width:  680,
                                    dialogClass: 'fixed-dialog',
                                    modal: true,
                                    title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
                                    close: function( event, ui ) {
                                            limpiarPantalla(true);
                                    },
                                    buttons:{
                                        "Aceptar": function() {
                                            $(this).dialog("close");
                                        },
                                        Cancel: function() {
                                            congelarCuentaPaciente('off');
                                            $(this).dialog("destroy");
                                        }
                                     }
                                });
                                control = false;
                            }
                            // --> Si es desde el mismo programa de cargos que estaba congelada, entonces se descongela automaticamente
                            else
                            {
                                $( '#divMsjCongelar').html("Existe una liquidación de <b>Cirugía</b> en proceso.");
                                $( '#divMsjCongelar').dialog({
                                    width:  350,
                                    dialogClass: 'fixed-dialog',
                                    modal: true,
                                    title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
                                    close: function( event, ui ) {
                                            limpiarPantalla(true);
                                    },
                                    buttons:{
                                        "Recuperar": function() {
                                            congelarCuentaPaciente('on');
                                            // --> Obtener liquidacion temporal
                                            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                                            {
                                                accion       : 'load',
                                                form         : 'obtenerLiquidacionTemporal',
                                                consultaAjax : '',
                                                historia     : $("#whistoriaLocal").val(),
                                                ingreso      : $("#wingLocal").val(),
                                                temporal     : '000160'
                                            }, function(data){
                                                $("#tabla_lista_cxs").html(data.tabla_lista_cxs);
                                                $("#div_recuperacion_datos").html(data.div_recuperacion_datos);
                                                // console.log(data.div_recuperacion_datos);

                                                // pintarElementosPaquete(data);
                                                // $("#busc_paquete").attr('valor', $("#ContenidoPaquete").find("#hiddenCodPaquete").val());
                                                // $("#busc_paquete").attr('nombre', $("#ContenidoPaquete").find("#hiddenNomPaquete").val());
                                                // $("#busc_paquete").val($("#ContenidoPaquete").find("#hiddenNomPaquete").val());
                                            },
                                            "json").done(function(){
                                                setearParametrosDesdeTemporal();
                                                $("#btn_liquidar_pop").click();
                                            });
                                            $(this).dialog("destroy");
                                        },
                                        "Nueva": function() {
                                            inactivarCirugiasPendientes($("#whistoriaLocal").val(), $("#wingLocal").val());
                                            congelarCuentaPaciente('off');
                                            $(this).dialog("destroy");
                                        }
                                     }
                                });

                                // congelarCuentaPaciente('off');
                            }
                        }
                    }
                }
                // --> Si no esta congelada se congela
                else
                {
                    if(desdeSelectorConcepto)
                    {
                        congelarCuentaPaciente('on');
                    }
                }
                return control;
            }, 'json').done(function(control){
                return control;
            });
        }

        function inactivarCirugiasPendientes(whistoria, wingreso)
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
            {
                accion       : 'load',
                form         : 'inactivar_pendientes',
                consultaAjax : '',
                historia     : whistoria,
                ingreso      : wingreso
            }, function(data){
                if(data.error == 1)
                {
                    alert(data.mensaje);
                }
                else
                {
                    // Elimnar el registro de la tabla html de pendientes sin tarífa
                    $(".trs_temp_pend_"+whistoria+"_"+wingreso).remove();
                }
            },
            "json").done(function(){
                //
            });
        }

        /**
         * [cargarTemporalEnRevision: se usa el mismo proceso "estadoCuentaCongelada" que en la función "validarEstadoDeCuentaCongelada" con el fin de validar
         *                             si la cuenta esta congelada por otro usuario o no o si ya hay una cirugía en proceso pendiente por el mismo usuario que esta liquidando,
         *                             esto permite que si se va a continuar con la liquidación de una cirugía, no se liquide si otro usuario ya está haciendo
         *                             modificaciones en otros programas a esa misma historia e ingreso]
         * @return {[type]} [description]
         */
        function cargarTemporalEnRevision()
        {
            var desdeSelectorConcepto = false;
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
            {
                accion       : 'update',
                form         : 'estadoCuentaCongelada',
                consultaAjax : '',
                wemp_pmla    : $('#wemp_pmla_tal').val(),
                historia     : $("#whistoriaLocal").val(),
                ingreso      : $("#wingLocal").val()
            }, function(data){
               // --> Si la cuenta se encuentra congelada
                var control = true;
                // if(data.Ecoest == 'on')
                {
                    // --> si el usuario que la congelo es diferente al actual
                        // console.log("esta bloqueado?");
                    if(data.Ecoest == 'on' && data.Ecousu != data.wuse)
                    {
                        // --> No se permiten grabar cargos, ya que la cuenta esta congelada por otro usuario
                        var mensaje =   '<br>'+
                                        ' En este momento no se le pueden grabar cargos al paciente.<br>'+
                                        ' La cuenta se encuentra congelada por <b>'+data.nomUsuario+'</b>'+
                                        ', en un proceso de <b>liquidacion de '+data.Nomtip+'</b>.';

                        // --> Mostrar mensaje
                        $( '#divMsjCongelar').html(mensaje);
                        $( '#divMsjCongelar').dialog({
                            width:  500,
                            dialogClass: 'fixed-dialog',
                            modal: true,
                            title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
                            close: function( event, ui ) {
                                limpiarPantalla(true);
                            }
                        });
                        control = false;
                    }
                    // --> Si es el mismo usuario que la congelo
                    else
                    {
                        if(!desdeSelectorConcepto)
                        {
                            // --> Si el usuario la congelo desde un programa diferente al de cargos
                            if(data.Ecoest == 'on' && data.Ecotip != 'QX')
                            {
                                mensaje = "Usted tiene una liquidación de <b>"+data.Nomtip+"</b> en proceso.<br>Para conservar dicho proceso de Click en <b>Aceptar</b> y luego abra su programa correspondiente.<br>Si desea cancelar el proceso y poder grabarle cargos al paciente de Click en <b>Cancelar</b>.";
                                $( '#divMsjCongelar').html(mensaje);
                                $( '#divMsjCongelar').dialog({
                                    width:  680,
                                    dialogClass: 'fixed-dialog',
                                    modal: true,
                                    title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
                                    close: function( event, ui ) {
                                            limpiarPantalla(true);
                                    },
                                    buttons:{
                                        "Aceptar": function() {
                                            $(this).dialog("close");
                                        },
                                        Cancel: function() {
                                            congelarCuentaPaciente('off');
                                            $(this).dialog("destroy");
                                        }
                                     }
                                });
                                control = false;
                            }
                            // --> Si es desde el mismo programa de cargos que estaba congelada, entonces se descongela automaticamente
                            else
                            {
                                congelarCuentaPaciente('on');
                                // --> Obtener liquidacion temporal
                                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                                {
                                    accion                   : 'load',
                                    form                     : 'obtenerLiquidacionTemporal',
                                    consultaAjax             : '',
                                    historia                 : $("#whistoriaLocal").val(),
                                    ingreso                  : $("#wingLocal").val(),
                                    temporal                 : '000230',
                                    id_encabezado_sin_tarifa : $("#id_encabezado_sin_tarifa").val()
                                }, function(data){
                                    $("#tabla_lista_cxs").html(data.tabla_lista_cxs);
                                    $("#div_recuperacion_datos").html(data.div_recuperacion_datos);
                                    // console.log(data.div_recuperacion_datos);

                                    // pintarElementosPaquete(data);
                                    // $("#busc_paquete").attr('valor', $("#ContenidoPaquete").find("#hiddenCodPaquete").val());
                                    // $("#busc_paquete").attr('nombre', $("#ContenidoPaquete").find("#hiddenNomPaquete").val());
                                    // $("#busc_paquete").val($("#ContenidoPaquete").find("#hiddenNomPaquete").val());
                                },
                                "json").done(function(){
                                    setearParametrosDesdeTemporal();
                                    $("#btn_liquidar_pop").click();
                                });
                                // $( '#divMsjCongelar').html("Existe una liquidación de <b>Cirugía</b> en proceso.");
                                // $( '#divMsjCongelar').dialog({
                                //     width:  350,
                                //     dialogClass: 'fixed-dialog',
                                //     modal: true,
                                //     title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
                                //     close: function( event, ui ) {
                                //             limpiarPantalla(true);
                                //     },
                                //     buttons:{
                                //         "Recuperar": function() {

                                //             $(this).dialog("destroy");
                                //         },
                                //         "Nueva": function() {
                                //             congelarCuentaPaciente('off');
                                //             $(this).dialog("destroy");
                                //         }
                                //      }
                                // });

                                // congelarCuentaPaciente('off');
                            }
                        }
                    }
                }
                // --> Si no esta congelada se congela
                // else
                // {
                //     if(desdeSelectorConcepto)
                //     {
                //         congelarCuentaPaciente('on');
                //     }
                // }
                return control;
            }, 'json').done(function(control){
                return control;
            });
        }

        function recuperarCirugiaEnRevision(whistoria, wingreso, id_encabezado_sin_tarifa)
        {
            limpiarPantalla(true);
            $("#whistoriaLocal").val(whistoria);
            $("#wingLocal").val(wingreso);
            $("#id_encabezado_sin_tarifa").val(id_encabezado_sin_tarifa);
            cargarDatosPaciente('whistoria','temporal_en_revision');
        }

        function setearParametrosDesdeTemporal()
        {
            $("#div_recuperacion_datos").find("input").each(function(){
                var id_campo_temp = $(this).attr("id");
                var valor_temp = $(this).val();
                id_campo_real = id_campo_temp.replace("_tempRecuperar","");
                if($("#"+id_campo_real).attr("type") == 'checkbox' && id_campo_real == 'wliq_paquete')
                {
                    if(valor_temp == 'on')
                    {
                        if($("#"+id_campo_real).attr("checked") != "checked") { //valor_temp == 'on' &&
                            $("#"+id_campo_real).attr("checked","checked");
                        }
                        // El condicional anterior se requiere para que al hacer clic una de las función que estan en el evento on clic actúe de forma correcta y tenga en cuenta que
                        // el campo esta chequeado
                        $("#"+id_campo_real).trigger("click");

                        // Como en la línea enterior se simuló hacer clic, esto hará que si antes estaba chequeado, el clic simulado lo desmarcará de nuevo
                        // entonces con esta línea siguiente se valída que si quedó desmarcado entonces lo chequee de nuevo.
                        if($("#"+id_campo_real).attr("checked") != "checked") { //valor_temp == 'on' &&
                            $("#"+id_campo_real).attr("checked","checked");
                        }
                    }
                }
                else{
                    $("#"+id_campo_real).val(valor_temp);
                }
            });
        }

        //-------------------------------------------------------------------
        //  Realiza la congelacion de la cuenta del paciente
        //-------------------------------------------------------------------
        function congelarCuentaPaciente(congelar)
        {
            var estadoActual = $("#cuentaCongelada").val();

            if($("#whistoria").val() != '' && $("#wing").val() != '' && estadoActual != congelar)
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion       : 'update',
                    form         : 'congelarCuentaPaciente',
                    consultaAjax : '',
                    wemp_pmla    : $('#wemp_pmla_tal').val(),
                    historia     : $("#whistoriaLocal").val(),
                    ingreso      : $("#wingLocal").val(),
                    congelar     : congelar
                }, function(data){
                    $("#cuentaCongelada").val(congelar);
                });
            }
        }

        function limpiarPantalla(encabezado)
        {
            if(encabezado)
            {
                $("#whistoriaLocal").val('');
                $("#wingLocal").val('');
                $("#whistoria_tal").val('');
                $("#wing_tal").val('');
                // $("input[type='radio'][defecto='si']").attr("checked", true);
                $("#DatosPaciente").find("[limpiar=si]").html("&nbsp;");
                $("#div_contenedor_detalle").html('');
                $("#cont_dlle_modal").html('');
            }
            else
            {
                // esta linea se ejecuta cuando se da clic en el campo "Liquidación de paquetes"
                congelarCuentaPaciente('off');
            }

            $("#arr_datos_liquidar").val($("#arr_vacio").val());
            $("#tabla_lista_cxs tr[id^=tr_liqAdd_cxs_]").remove();
            $("#arr_datos_liquidar_temp").val($("#arr_vacio").val());
            $("#arr_datos_liquidados").val($("#arr_vacio").val());
            $("#arr_procedimientos_orden_liquidar").val($("#arr_vacio").val());
            $("#arr_CARGOS_PARA_GRABAR").val($("#arr_vacio").val());
            $("#arr_extras").val($("#arr_vacio").val()); // Se llena cuando se agregan conceptos extras que no estan en la plantilla.
            // $("#div_cont_liquidacion_modal").hide("slow");
            $("#div_cont_liquidacion_modal").html("");
            // $( "#accordionDetCuentaResumido" ).hide();
        }

        function CambiarFoco(e, Elemento)
        {
            var tecla = (document.all) ? e.keyCode : e.which;
            if(tecla == 13)
            {
                $('#'+Elemento).focus();
            }
        }

        function verOcultarLista(id_elem)
        {
            if($("#"+id_elem).is(":visible"))
            {
                $("#"+id_elem).hide(300);
            }
            else
            {
                $("#"+id_elem).show(300);
            }
        }

        function recargarCampoProcedimiento(chk)
        {
            // console.log(chk);
            var es_paquete = $("#"+chk).attr("checked");
            if(es_paquete == 'checked')
            {
                $("#chk_otros_procedimientos").removeAttr("checked");
                $(".tr_procedimientos_autorizados").hide();
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion       : 'load',
                        form         : 'carga_paquetes',
                        consultaAjax : '',
                        wemp_pmla    : $('#wemp_pmla_tal').val(),
                        historia     : $("#whistoriaLocal").val(),
                        ingreso      : $("#wingLocal").val()
                    }, function(data){
                        // $("#cuentaCongelada").val(congelar);
                        $("#arr_procedimientos").val(data.arr_paquetes);
                        return data;
                    },"json"
                    ).done(function(data){
                        //Inicializar de nuevo autocomplete de procedimientos pero con el array de paquetes
                        crearAutocomplete('arr_procedimientos', 'wprocedimiento','','',0);
                    });
            }
            else
            {
                if (!$(".tr_procedimientos_autorizados").is(":visible")) { $(".tr_procedimientos_autorizados").show(); }
                $("#arr_procedimientos").val($("#arr_procedimientos_temp").val());
                crearAutocomplete('arr_procedimientos', 'wprocedimiento','','',0);
            }
        }

        function validarLiquidacionEnCurso(elem)
        {
            // if($("#tabla_lista_cxs").find("tr[id^=tr_liqAdd_cxs_]") > 0)
            {
                var estado_chk = ($(elem).is(":checked")) ? 'on': 'off';
                if(estado_chk == 'off')
                {
                    $("#div_recuperacion_datos").html("");
                }

                var esta_cargando_temporal = false;
                if($("#div_recuperacion_datos").find("#arr_datos_liquidar_tempRecuperar").length > 0) { esta_cargando_temporal= true; }
                if(esta_cargando_temporal || $("#tabla_lista_cxs").find("tr[id^=tr_liqAdd_cxs_]").length == 0 || confirm("Esta acción hace que se reinicie toda la liquidación actual y perder datos sin guardar\n\n¿Quiere continuar?"))
                {
                    if(!esta_cargando_temporal) { limpiarPantalla(false); }
                    verOcultarLista('tr_add_');
                    recargarCampoProcedimiento('wliq_paquete');
                    if(!esta_cargando_temporal) { cargarDatosPaciente('whistoria',''); }

                    if(!esta_cargando_temporal)
                    {
                        $("#wtipo_anestesia_cx").val('').addClass("campoRequerido");
                        $("#wtiempo_sala_recuperarcion").val('').addClass("campoRequerido");
                        $("#wtiempo_uso_minutos").val(0).removeClass("campoRequerido");
                        $("#wtiempo_minutos_cx").val('').addClass("campoRequerido");
                    }
                }
                else
                {
                    if(estado_chk == 'on') { $(elem).removeAttr("checked"); }
                    else { $(elem).attr("checked","checked"); }
                }

                actualizarLabelProcedimiento(estado_chk);
            }
        }

        function actualizarLabelProcedimiento(estado_chk)
        {
            var txt = "<?=PROCEDIMIENTO?>";
            if(estado_chk == 'on')
            {
                txt = "<?=LB_PAQUETE?>";
                $("#span_otros_procedimientos").hide();
            }
            else
            {
                    $("#span_otros_procedimientos").show();
            }

            $(".label_procedimiento_phl").attr("placeholder",""+txt+"");
            $(".label_procedimiento").html(txt);
        }

        /**
         * [cargarTodosLosProcedimientos carga todos los procedimientos disponibles del maestro de procedimientos]
         * @param  {[type]} elem [description]
         * @return {[type]}      [description]
         */
        function cargarTodosLosProcedimientos(elem)
        {
            var todos = ($(elem).is(":checked")) ? 'on': 'off';
            if(todos == 'on')
            {
                $("#arr_procedimientosHistoria").val($("#arr_procedimientos").val()); // Los procedimientos asociados a las historia e ingreso se guardan en un campo temporal
                $("#arr_procedimientos").val($("#arr_procedimientos_temp").val()); // Los procedimientos actuales los reemplaza por el array de todos los procedimientos
                crearAutocomplete('arr_procedimientos', 'wprocedimiento','','',3); // Reinicia el autocompletar con todos los procedimientos del maestro de procedimientos
            }
            else
            {
                $("#arr_procedimientos").val($("#arr_procedimientosHistoria").val()); // Los procedimientos actuales los reemplaza solo con los procedimientos asociados a la historia (autorizados más con mercado)
                crearAutocomplete('arr_procedimientos', 'wprocedimiento','','',0); // Reinicia el autocompletar con los procedimientos asociados a las historia.
            }
        }

        function actualizarEquivalenteHidden(elem)
        {
            $(elem).attr("valorAnterior",$(elem).val());
        }

        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
            'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: '',
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);


        $("#wfecha_cargo").datepicker({
            showOn: "button",
            buttonImage: "../../images/medical/root/calendar.gif",
            buttonImageOnly: true,
            maxDate:"+0D"
        });

    </script>

    <script type="text/javascript">

        /**
         * [llamar_insumos Esta función se encarga de traer automáticamente los insumos que se usarán en la cirugía]
         * @param  {[type]} div_contenedor [description]
         * @param  {[type]} modal_ok       [description]
         * @return {[type]}                [description]
         */
        function llamar_insumos(div_contenedor,modal_ok, wprocedimiento_codigo, wprocedimiento_nombre, wentidad_codigo, wentidad_nombre, wtarifa_codigo, wtarifa_nombre)
        {
            var operacion         = 'grabar'; //proponerygrabar
            var modal             = modal_ok;
            var div               = div_contenedor;
            var procedimiento     = wprocedimiento_codigo;
            var nom_procedimiento = wprocedimiento_nombre;
            var entidad           = wentidad_codigo;
            var nom_entidad       = wentidad_nombre;
            var tarifa            = wtarifa_codigo;
            var nom_tarifa        = wtarifa_nombre;
            // console.log('proponerygrabar'+'|'+ 'si'+'|'+ 'div_insumo'+'|'+ $("#wprocedimiento").attr('codigo')+'|'+ $("#wprocedimiento").attr('nombre')+'|'+ $("#wentidad").attr('codigo')+'|'+ $("#wentidad").attr('nombre')+'|'+ $("#wtarifa").attr('codigo')+'|'+ $("#wtarifa").attr('nombre'));
            ventana_insumo(operacion,div,modal,procedimiento,entidad,tarifa,nom_procedimiento,nom_entidad,nom_tarifa);
        }


        function simularPlaceHolder()
        {
            // Página con etiquetas de html5 de las que se podría verificar su compatibilidad
            // https://github.com/Modernizr/Modernizr/wiki/HTML5-Cross-browser-Polyfills
            // http://geeks.ms/blogs/gperez/archive/2012/01/10/modernizr-ejemplo-pr-225-ctico-1-utilizando-placeholder.aspx
            // http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
            if(!Modernizr.input.placeholder)
            {
                console.log("NAVEGADOR NO COMPATIBLE CON placeholder de HTML5, Se simúla atributo placeholder.");
                $('[placeholder]').focus(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                       input.val('');
                       input.removeClass('placeholder');
                    }
                }).blur(function() {
                    var input = $(this);
                    if (input.val() == '' || input.val() == input.attr('placeholder')) {
                        input.addClass('placeholder');
                        input.val(input.attr('placeholder'));
                    }
                }).blur();
                $('[placeholder]').parents('form').submit(function() {
                    $(this).find('[placeholder]').each(function() {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                       input.val('');
                    }
                  })
                });
            }
        }

        function validar_cifra_decimal(elem)
        {
            $(elem).removeClass("campoRequerido");
            var cantidad = $(elem).val();
            if ( regExDecimal.test( cantidad ) && cantidad != '')
            {
                esok = true;
            }
            else
            {
                esok = false;
                $(elem).addClass("campoRequerido");
            }
            return esok;
        }

        function aplicarAcordeon(id_div)
        {
            $("#"+id_div).accordion({
                collapsible: true
                ,autoHeight: false
                // ,clearStyle: true
                // ,heightStyle: "content"
                // ,active: -1
            });
        }

        function eliminarFilaRango(tipo_cobro ,id_fila, identifica_concepto, prefijoFila, dif_rango)
        {
            $("#"+id_fila).hide("slow",
                                    function(){
                                        $(this).remove();
                                        $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                                            {
                                                accion              : 'delete',
                                                form                : 'eliminar_rango_concepto',
                                                consultaAjax        : '',
                                                arr_politica        : $("#arr_politica").val(),
                                                identifica_concepto : identifica_concepto,
                                                dif_rango           : dif_rango,
                                                tipo_cobro          : tipo_cobro
                                            },
                                            function(data){
                                                if(isset(data.error) && data.error == 1)
                                                {
                                                    alert(data.mensaje);
                                                }
                                                else
                                                {
                                                    $("#arr_politica").val(data.arr_politica);
                                                    resetStylePrefijo(prefijoFila);
                                                }
                                            },
                                            "json"
                                        ).done(function(){
                                        });
                                });

        }

        function isset ( strVariableName ) {
            try {
                eval( strVariableName );
            } catch( err ) {
                if ( err instanceof ReferenceError )
                   return false;
            }
            return true;
        }

        function resetStylePrefijo(prefijo)
        {
            var cont = 0;
            var cs = 'fila1';
            $("tr").find("[id^="+prefijo+"]").each(function(){
                    $(this).removeClass("fila1 fila2");
                    if(cont % 2 == 0)
                        cs = 'fila1';
                    else
                        cs = 'fila2';

                    $(this).addClass(cs);
                    cont = cont+1;
                }
            );
        }

        //Function to convert hex format to a rgb color
        function rgb2hex(rgb){
         rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
         return "#" +
          ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
          ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
          ("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
        }

        function cambioImagen(img1, img2)
        {
            $('#'+img1).hide(1000);
            $('#'+img2).show(1000);
        }

        function ocultarElemnto(elemento){
            $("#"+elemento).hide(1000);
        }

        function soloNumeros(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            //alert(charCode);
             if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 37 && charCode != 39 && charCode != 35 && charCode != 36 && charCode != 46) //37:teclaizquierda 39:tecladerecha 36:teclainicio 38:teclafin 46:suprimir
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
            // alert(charCode);
             // if (charCode > 31 && (charCode < 48 || charCode > 57))
             // if ((charCode < 48 && charCode > 57) || (charCode < 65 && charCode > 90) || (charCode < 97 && charCode > 122 ))
             if ((charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) || charCode == 8 || charCode == 9)
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

        function resetStylePrefijo(pref)
        {
            var cont = 0;
            var cs = 'fila1';
            $("tr").find("[id^="+pref+"]").each(function(){
                    $(this).removeClass("fila1 fila2");
                    if(cont % 2 == 0)
                        cs = 'fila1';
                    else
                        cs = 'fila2';

                    $(this).addClass(cs);
                    cont = cont+1;
                }
            );
        }

        function resetStyleSufijo(sufijo)
        {
            var cont = 0;
            var cs = 'fila1';
            $("tr").find("[id$="+sufijo+"]").each(function(){
                    $(this).removeClass("fila1 fila2");
                    if(cont % 2 == 0)
                        cs = 'fila1';
                    else
                        cs = 'fila2';

                    $(this).addClass(cs);
                    cont = cont+1;
                }
            );
        }

        function trOver(grupo)
        {
            $("#"+grupo.id).addClass('classOver');
        }

        function trOut(grupo)
        {
            $("#"+grupo.id).removeClass('classOver');
        }

        function validarRequeridos(contenedor)
        {
            var vacioR = true;
            $("#"+contenedor).find(".requerido").each(
                function(){
                    $(this).removeClass('campoRequerido');
                    var valor = $(this).val();

                    if(valor.replace(/ /gi,"") == '')
                    {
                        $(this).addClass('campoRequerido');
                        vacioR = false;
                    }
                }
            );
            return vacioR;
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

        function recargarDependiente(id_padre, id_hijo, accion, form, df)
        {
            //console.log(arregloDependientes);
            val = $("#"+id_padre).val();
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params+"&accion="+accion+"&id_padre="+val+"&form="+form+"&coddf="+df,
                function(data){
                    $('#'+id_hijo).html(data);
                    if(arregloDependientes[ id_hijo ] != undefined )//Hay un municipio pendiente?
                    {
                        $('#'+id_hijo).val( arregloDependientes[ id_hijo ] );//Selecciona medellin - municipio
                        arregloDependientes[ id_hijo ] = undefined;//Deja undefined el arreglo en la posicion wmuni
                    }
                }
            );
        }


        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
            'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: '',
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);


        /*$("#wfnace").datepicker({
          showOn: "button",
          buttonImage: "../../images/medical/root/calendar.gif",
          buttonImageOnly: true,
          maxDate:"+0D"
        });*/

        function verSeccion(id){
            $("#"+id).toggle("normal");
        }

        /*function verFlotante(id_flotante, referencia_id)
        {
            // var elemento = $("#"+referencia_id);
            // var posicion = elemento.offset();
            // $('.caja_flotante').css({'left':posicion.left+54,'top':posicion.top-0});
            $("#"+id_flotante).show();
        }*/

        /*function ocultarFlotanteSeguimiento(id_flotante)
        {
            $("#"+id_flotante).hide();
        }*/
    </script>

    <style type="text/css">
        .placeholder
        {
          color: #aaa;
        }

        .encTabla{
            text-align: center;
        }

        /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
        .ui-datepicker {font-size:12px;}
        /* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
        .ui-datepicker-cover {
            display: none; /*sorry for IE5*/
            display/**/: block; /*sorry for IE5*/
            position: absolute; /*must have*/
            z-index: -1; /*must have*/
            filter: mask(); /*must have*/
            top: -4px; /*must have*/
            left: -4px; /*must have*/
            width: 200px; /*must have*/
            height: 200px; /*must have*/
        }

        .classOver{
            background-color: #CCCCCC;
        }
        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px }
        .tipo3V:hover {color: #000066; background: #999999;}

        .brdtop {
            border-top-style: solid; border-top-width: 2px;
            border-color: #2A5BD0;
        }
        .brdleft{
            border-left-style: solid; border-left-width: 2px;
            border-color: #2A5BD0;
        }
        .brdright{
            border-right-style: solid; border-right-width: 2px;
            border-color: #2A5BD0;
        }
        .brdbottom{
            border-bottom-style: solid; border-bottom-width: 2px;
            border-color: #2A5BD0;
        }

        .alto{
            height: 140px;
        }

        .vr
        {
            display:inline;
            height:50px;
            width:1px;
            border:1px inset;
            /*margin:5px*/
            border-color: #2A5BD0;
        }

        .bgGris1{
            background-color:#F6F6F6;
        }

        .tbold{
            font-weight:bold;
            text-align:left;
        }
        .alng{
            text-align:left;
        }
        .img_fondo{
            background: url('../../images/medical/tal_huma/fondo.png');
            background-repeat: no-repeat;
        }
        .disminuir{
            font-size:11pt;
        }
        .imagen { width: 250px; height: auto;}
        .btnActivo { background-color: #0033ff; }
        .padding_info{
            padding-bottom: 4px;
        }
        .border_ppal{
            border: 2px solid #2A5DB0;
        }
        .txt1{
            /*color:#2A5DB0;*/
            font-weight:bold;
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

        .parrafo1{
            color: #333333;
            background-color: #cccccc;
            font-family: verdana;
            font-weight: bold;
            font-size: 10pt;
            text-align: left;
        }
        .no_save{
            border: red 1px solid;
        }
        .mayuscula{
            text-transform: uppercase;
        }

        #tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        /*#tooltip{
            color: #FE2E2E;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
        }*/
        #tooltip h3, #tooltip div{
            margin:0; width:auto
        }

        #tooltip_pro{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        /*#tooltip{
            color: #FE2E2E;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
        }*/
        #tooltip_pro h3, #tooltip_pro div{
            margin:0; width:auto
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

        #caja_flotante{
            position: absolute;
            /*top:0;*/
            /*left: 10px;*/
            border: 1px solid #CCC;
            background-color: #F2F2F2;
            /*width:150px;*/
        }

        .caja_flotante{
            position: absolute;
            /*top:0;*/
            /*left: 10px;*/
            border: 1px solid #CCC;
            background-color: #F2F2F2;
            /*width:150px;*/
        }

        /* TABS */
        ul.pestania {
            border-bottom: 1px solid #E5E5E5;
            float: left;
            font-size: 0;
            margin: 10px 0 -1px;
            padding: 0;
            width: 100%;
        }
        ul.pestania.left {
            text-align: left;
        }
        ul.pestania.center {
            text-align: center;
        }
        ul.pestania.right {
            text-align: right;
        }
        ul.pestania.right li {
            margin: 0 0 0 -2px;
        }
        ul.pestania li {
            display: inline-block;
            font-size: 14px;
            left: 0;
            list-style-type: none;
            margin: 0 -2px 0 0;
            padding: 0;
            position: relative;
            top: 0;
        }
        ul.pestania li a {
            -moz-border-bottom-colors: none;
            -moz-border-left-colors: none;
            -moz-border-right-colors: none;
            -moz-border-top-colors: none;
            background: none repeat scroll 0 0 #F5F5F5;
            border-color: #E5E5E5 #E5E5E5 -moz-use-text-color;
            border-image: none;
            border-style: solid solid none;
            border-width: 1px 1px 0;
            box-shadow: 0 -3px 3px rgba(0, 0, 0, 0.03) inset;
            color: #666666;
            display: inline-block;
            font-size: 0.9em;
            left: 0;
            line-height: 100%;
            padding: 9px 15px;
            position: relative;
            text-decoration: none;
            top: 0;
        }
        ul.pestania li a:hover {
            background: none repeat scroll 0 0 #FFFFFF;
        }
        ul.pestania li.current a {
            background: none repeat scroll 0 0 #FFFFFF;
            box-shadow: none;
            color: #222222;
            left: 0;
            position: relative;
            top: 1px;
        }

        .tab-content {
            background: none repeat scroll 0 0 #FFFFFF;
            border: 1px solid #E5E5E5;
            clear: both;
            margin: 0 0 3px;
            padding: 3px;
            /*margin: 0 0 40px;
            padding: 20px;*/
        }
        /* TABS */

        .ui-autocomplete{
            max-width:  230px;
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
            font-size:  9pt;
        }

        /* NOTIFICACIÓN */
        #notificacion {
            background-color: #F2F2F2;
            background-repeat: no-repeat;
            font-family: Helvetica;
            font-size: 20px;
            line-height: 30px;
            position: absolute;
            text-align: center;
            width: 30%;
            left: 35%;
            top: -30px;
        }
        .chat {
            background-image: url("../../images/medical/root/info.png");
        }

        /*.notificar {
            background-color: #59AADA;
            border-radius: 6px;
            border: 1px solid #60B4E5;
            color: #FFFFFF;
            display: block;
            font-size: 30px;
            font-weight: bold;
            letter-spacing: -2px;
            margin: 60px auto;
            padding: 20px;
            text-align: center;
            text-shadow: 1px 1px 0 #145982;
            width: 350px;
            cursor: pointer;
        }*/

        /*.notificar:hover {
            background-color: #4a94bf;
        }*/
        /* NOTIFICACIÓN */

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
        .pad{
            padding:    4px;
        }

        .margen-superior-eventos{
            margin-top:15px;
            border:2px #2A5DB0 solid;
        }

        .datos-adds-eventos{
            text-align:left; border: 1px solid #cccccc;
        }

        ul{
            margin:0;
            padding:0;
            list-style-type:none;
        }

        table[id^='tabla_lista_cxs_'] td {
            font-size: 8.5pt;
        }

        .alinear_derecha {
            display: block;
            float:right;
            width: 70px;
            text-align: center;
            /*color: #FF2F00;*/
        }

        .div_alinear{
            margin-left: 10px;
        }
    </style>
</head>
<body>
<!-- <div style="color:red; font-weight:bold; text-align:center;font-size:14pt;"><img border="0" src="../../images/medical/root/CONSTRUC.GIF"width="30" height="30">[SE ESTÁ DESARROLLANDO ACTUALMENTE]<img border="0" src="../../images/medical/root/CONSTRUC.GIF"width="30" height="30"></div> -->
<div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?=$wactualiz?></div>
<input type='hidden' name='wbasedato_HCE' id='wbasedato_HCE' value="<?=$wbasedato_HCE?>">
<input type='hidden' name='wbasedato_movhos' id='wbasedato_movhos' value="<?=$wbasedato_movhos?>">
<input type="hidden" name="arr_procedimientosHistoria" id="arr_procedimientosHistoria" value='<?=json_encode($arr_vacio)?>'>
<input type="hidden" name="arr_procedimientos" id="arr_procedimientos" value='<?=json_encode($arr_procedimientos)?>'>
<input type="hidden" name="arr_procedimientos_temp" id="arr_procedimientos_temp" value='<?=json_encode($arr_procedimientos)?>'>
<input type="hidden" name="arr_procedimientos_temp_noPrepa" id="arr_procedimientos_temp_noPrepa" value='<?=base64_encode(serialize($arr_procedimientos))?>'>
<input type="hidden" name="arr_terceros_especialidad" id="arr_terceros_especialidad" value='<?=json_encode($arr_terceros_especialidad)?>'>
<input type="hidden" name="arr_organos" id="arr_organos" value='<?=json_encode($arr_organos)?>'>
<input type="hidden" name="arr_datos_liquidados" id="arr_datos_liquidados" value='<?=base64_encode(serialize($arr_datos_liquidados))?>'>
<input type="hidden" name="arr_procedimientos_orden_liquidar" id="arr_procedimientos_orden_liquidar" value='<?=base64_encode(serialize($arr_vacio))?>'>
<input type="hidden" name="arr_vacio" id="arr_vacio" value='<?=base64_encode(serialize($arr_vacio))?>'>
<input type="hidden" name="arr_datos_liquidar_temp" id="arr_datos_liquidar_temp" value='<?=base64_encode(serialize($arr_datos_liquidar_temp))?>'>
<input type="hidden" name="arr_conceptos" id="arr_conceptos" value='<?=json_encode($arr_conceptos)?>'>
<input type="hidden" name="arr_examenes" id="arr_examenes" value='<?=json_encode($arr_examenes)?>'>
<input type="hidden" name="arr_equipos" id="arr_equipos" value='<?=json_encode($arr_equipos)?>'>
<input type="hidden" name="arr_conceptos_64" id="arr_conceptos_64" value='<?=base64_encode(serialize($arr_conceptos))?>'>
<input type="hidden" name="arr_extras" id="arr_extras" value='<?=base64_encode(serialize($arr_vacio))?>'>
<input type="hidden" name="arr_CARGOS_PARA_GRABAR" id="arr_CARGOS_PARA_GRABAR" value='<?=base64_encode(serialize($arr_vacio))?>'>
<input type="hidden" name="id_concepto_examenes" id="id_concepto_examenes" value='<?=$id_concepto_examenes?>'>
<input type="hidden" name="id_concepto_uso_equipos" id="id_concepto_uso_equipos" value='<?=$id_concepto_uso_equipos?>'>
<input type="hidden" name="control_ultima_actualizacion" id="control_ultima_actualizacion" value=''>
<input type='hidden' name='cuentaCongelada' id='cuentaCongelada' value='' >
<input type='hidden' name='id_encabezado_sin_tarifa' id='id_encabezado_sin_tarifa' value='' >

<div id='divMsjCongelar' align='center' style='display:none;font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 10pt;'>
        <br>
</div>

<table align="center" style="width:95%;">
    <tr>
        <td style="text-align:left;">
            <div id="contenedor_programa_liquidacion_cx" align="left">
                <div width='' id='accordionDatosPaciente' style="text-align:left;width: 1250px;" class="div_alinear">
                    <h3>DATOS DEL PACIENTE</h3>
                    <div class='pad' align='center' id='DatosPaciente'>
                        <table width='100%' style='border: 1px solid #999999;'>

                            <tr class="fila1" style="font-weight: bold;">
                                <td align="left" width="11%">
                                    <b>Historia:</b>
                                </td>
                                <td align="left" width="15%">
                                    <b>Ingreso Nro:</b>
                                </td>
                                <td align="left" colspan="2">
                                    <b>Paciente:</b>
                                </td>
                                <td align="left">
                                    <b>Documento:</b>
                                </td>
                                <td align="left">
                                    <b>Fecha Ingreso:</b>
                                </td>
                            </tr>
                            <tr class="fila2">
                                <td align="left">
                                    <input type="text" id="whistoriaLocal" size="15" onchange="cargarDatosPaciente('whistoria','')" onkeypress="CambiarFoco(event, 'wing');" limpiar="si" value="<?=$whistoria?>">
                                </td>
                                <td align="left">
                                    <input type="text" id="wingLocal"      size="3"  onchange="cargarDatosPaciente('wing','')" limpiar="si" value="<?=$wing?>">
                                </td>
                                <td align="left" colspan="2" id="div_paciente" limpiar="si">
                                </td>
                                <td align="left" id="div_documento" limpiar="si">
                                </td>
                                <td align="left" id="wfecing" limpiar="si">
                                </td>
                            </tr>
                            <tr class="fila1" style="font-weight: bold;">
                                <td align="left">
                                    <b>Servicio de Ing:</b>
                                </td>
                                <td align="left" width="12%">
                                    <b>Tipo de Ingreso:</b>
                                </td>
                                <td align="left">
                                    <b>Ubicación:</b>
                                </td>
                                <td align="left">
                                    <b>Servicio de facturación:</b>
                                </td>
                                <td align="center" colspan="2">
                                    <b>Responsables:</b>
                                </td>
                            </tr>
                            <tr class="fila2">
                                <td align="left" id="div_tipo_servicio" limpiar="si">
                                </td>
                                <td align="left" id="div_tipo_ingreso" limpiar="si">
                                </td>
                                <td align="left" id="divCcoActualPac" limpiar="si">
                                </td>
                                <td align="left" id="div_servicio">
                                </td>
                                <td align="left" colspan="2" style="font-size:8pt;" >
                                    <table width="100%" id="tableResponsables" style="background-color: #ffffff;display:none" limpiar="si">
                                    </table>
                                    <div id="div_responsable"   style="display:none"></div>
                                    <div id="div_tarifa"        style="display:none"></div>
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>

                <div width='' id="acordeon_basicos_liquidacion" style="text-align:left;width: 1250px;" class="div_alinear" >
                    <h3>DATOS BASICOS DE LIQUIDACIÓN</h3>
                    <div id="datos_basicos_liquidacion">
                        <fieldset id="">
                            <legend align="left">Datos de liquidación</legend>
                                <div style="background-color:#FFFEE2; padding: 3px;">
                                    <table id="tabla_add_lista_cxs" align="center"  border="0" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <td colspan="7">
                                                <table width="100%">
                                                    <tr>
                                                        <td class="encabezadoTabla"><span>Liquidar paquetes</span></td>
                                                        <td class="fila1" colspan="7"><input type="checkbox" id="wliq_paquete" name="wliq_paquete" size="5" value="on" onclick="validarLiquidacionEnCurso(this);" ></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="encabezadoTabla"><span>Número de vías</span></td>
                                                        <td class="fila1"><input type="text" id="wnumvias" size="5" class="numerico requerido" value="1" onchange="generarListaVias();"></td>
                                                        <td class="encabezadoTabla"><span>Fecha</span></td>
                                                        <td class="fila1"><input type="text" id="wfecha_cargo" name="wfecha_cargo" size="10" value="<?=date("Y-m-d")?>" disabled="disabled"></td>
                                                        <td class="encabezadoTabla"><span>Hora</span></td>
                                                        <td class="fila1"><input type="text" id="whora_cargo" name="whora_cargo" size="10" value="<?=date("H:i")?>" ></td>
                                                        <td class="encabezadoTabla">Paciente politraumatizado</td>
                                                        <td class="fila1">
                                                            <select name="wpolitraumatizado" id="wpolitraumatizado">
                                                                <option value="off">No</option>
                                                                <option value="on">Sí</option>
                                                                <!-- <option value="*">*</option> -->
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" >
                                                <table border="0" cellpadding="2" cellspacing="2" width="100%">
                                                    <tr class="encabezadoTabla">
                                                        <td style="text-align:center;">Tipo anestesia</td>
                                                        <td style="text-align:center;">Sala recuperación (Minutos)</td>
                                                        <td style="text-align:center;">Tiempo uso equipos (Minutos)</td>
                                                        <td style="text-align:center;">Tiempo cirugía (Minutos)</td>
                                                    </tr>
                                                    <tr class="fila1">
                                                        <td style="text-align:center;">
                                                            <select id="wtipo_anestesia_cx" name="wtipo_anestesia_cx" class="requerido campoRequerido">
                                                                <option value="">Seleccione</option>
                                                                <?php
                                                                foreach ($arr_tipo_anestesia as $key => $value) {
                                                                    echo '<option value="'.$key.'">'.utf8_encode($value).'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <input type="text" value="" id="wtiempo_sala_recuperarcion" name="wtiempo_sala_recuperarcion" class="save_ok numerico requerido classminutos campoRequerido" placeholder="Minutos" maxlength="3" style="width:58px;" />
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <input type="text" value="0" id="wtiempo_uso_minutos" name="wtiempo_uso_minutos" class="save_ok numerico requerido classminutos" placeholder="Minutos" maxlength="3" style="width:58px;" />
                                                        </td>
                                                        <td style="text-align:center;">
                                                            <input type="text" value="" id="wtiempo_minutos_cx" name="wtiempo_minutos_cx" class="save_ok numerico requerido classminutos campoRequerido" placeholder="Minutos" maxlength="3" style="width:58px;" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" >&nbsp;</td>
                                        </tr>
                                        <!-- <tr class="tr_procedimientos_autorizados">
                                            <td colspan="7" ><span class="encabezadoTabla">Procedimientos Autorizados:</span>
                                                <?php
                                                    // $dataCups = pintarCupsAutorizados($whistoria, $wing);
                                                    // if($dataCups['hayCups'])
                                                    // echo $dataCups['html'];
                                                ?>
                                            </td>
                                        </tr> -->
                                        <tr class="encabezadoTabla">
                                            <td style="text-align:center;">
                                                <img width="15" height="15" src="../../images/medical/root/info.png" class="tooltip_pro" title="* Flecha abajo para desplegar opciones.<br>* Recuerde primero escribir la historia en los datos del paciente.<br>* Si selecciona mostrar 'Otros procedimientos' debe escribir por lo menos tres caracteres." >
                                                &nbsp;<span class="label_procedimiento" ><?=PROCEDIMIENTO?></span>
                                                <span id="span_otros_procedimientos">&nbsp;[Otros procedimientos <input type="checkbox" id="chk_otros_procedimientos" name="chk_otros_procedimientos" onclick="cargarTodosLosProcedimientos(this);" >]</span>
                                            </td>
                                            <td style="text-align:center;"><?=_VIA_?></td>
                                            <td style="text-align:center;"><?=ESPECIALISTA?></td>
                                            <td style="text-align:center;"><?=ESPECILALIDAD?></td>
                                            <td style="text-align:center;">&nbsp;</td>
                                        </tr>
                                        <tr class="fila1">
                                            <td style="text-align:center;">
                                                <input type="text" value="" id="wprocedimiento" name="wprocedimiento" codigo="" nombre="" class="requerido save_ok label_procedimiento_phl" placeholder="<?=PROCEDIMIENTO?>" size="44" />
                                            </td>
                                            <td style="text-align:center;">
                                                <!-- <input type="text" value="" id="wvia" name="wvia" codigo="" nombre="" class="requerido save_ok numerico" placeholder="<?=_VIA_?>" maxlength="2" style="width:25px;" /> -->
                                                <select id="wvia" name="wvia save_ok" class="requerido">
                                                    <option value="1">1</option>
                                                </select>
                                            </td>
                                            <td style="text-align:center;">
                                                <input type="text" value="" id="wespecialistas" name="wespecialistas" codigo="" nombre="" class="requerido save_ok" placeholder="<?=ESPECIALISTA?>" size="32" />
                                            </td>
                                            <td style="text-align:center;">
                                                <!-- <input type="text" value="" id="wespecialidad" name="wespecialidad" codigo="" nombre="" placeholder="<?=ESPECILALIDAD?>" /> -->
                                                <select name="wespecialidad" id="wespecialidad" class="requerido save_ok">
                                                    <option value="">Seleccione..</option>
                                                </select>
                                            </td>
                                            <td style="text-align:center;">
                                                &nbsp;

                                                <!-- <input type="checkbox" value="" id="wbilateral" name="wbilateral" codigo="" nombre="" class="requerido save_ok" placeholder="<?=BILATERAL?>" /> -->
                                            </td>
                                            <!-- <td style="text-align:center;">
                                                <input type="text" value="" id="wtiempo_minutos_cx" name="wtiempo_minutos_cx" class="requerido save_ok" placeholder="<?=TIEMPO_MINUTOS?>" maxlength="3" style="width:45px;" />
                                            </td> -->
                                        </tr>
                                        <tr class="fila1">
                                            <td colspan="4" style="padding:0px;">
                                                <!-- AQUÍ DEBEN IR LOS CAMPOS DE TIEMPO Y ANESTESIA SI SE REQUIERE QUE SEA POR CADA PROCEDIMIENTO -->
                                                <table>
                                                    <tr>
                                                        <td class="encabezadoTabla"><?=ORGANO?></td>
                                                        <td>
                                                            <input type="text" value="" id="worgano" name="worgano" codigo="" nombre="" bilateral="" class="requerido save_ok" placeholder="<?=ORGANO?>" size="28" />
                                                        </td>
                                                        <td class="opcion_bilateral encabezadoTabla">Bilateral</td>
                                                        <td class="opcion_bilateral ">
                                                            <input type="checkbox" value="" id="wbilateral" name="wbilateral" codigo="" nombre="" class="requerido save_ok" placeholder="<?=BILATERAL?>" />
                                                        </td>
                                                        <td class="encabezadoTabla">Posición organo</td>
                                                        <td>
                                                            <select name="wposicion_organo" id="wposicion_organo">
                                                                <option value="">Seleccione</option>
                                                                <?php
                                                                    foreach ($arr_posciones_organos as $cod_pos => $nom_pos) {
                                                                        echo '<option value="'.$cod_pos.'">'.$nom_pos.'</option>';
                                                                    }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td>
                                                <!-- <td style="text-align:center;"> -->
                                                <input type="hidden" id="arr_datos_liquidar" name="arr_datos_liquidar" value="<?=base64_encode(serialize($arr_datos_liquidar))?>" >
                                                <button id="boton_add_cx" onclick="adicionarProcedimiento(this);" class="btn_loading" >Adicionar</button>
                                                    <!-- AQUÍ IBA EL BOTÓN DE ADICIONAR PROCEDIMIENTO -->
                                                <!-- </td> -->
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div id="div_contenedor_evento_qx_actual" class="margen-superior-eventos">
                                    <div class="encabezadoTabla" style="width:100%;text-align:left;"><span>Evento Quirurgico:</span> <span><?=nombreDiaSemana(date("Y"),date("m"),date("d"))?> <?=date("Y-m-d")?></span></div>
                                    <table id="tabla_lista_cxs" align="center" width="100%;">
                                        <tr class="encabezadoTabla">
                                            <td style="text-align:center;" class="label_procedimiento" ><?=PROCEDIMIENTO?></td>
                                            <td style="text-align:center;"><?=_VIA_?></td>
                                            <td style="text-align:center;"><?=ESPECIALISTA?></td>
                                            <td style="text-align:center;"><?=ESPECILALIDAD?></td>
                                            <td style="text-align:center;"><?=ORGANO?></td>
                                            <td style="text-align:center;"><?=BILATERAL?></td>
                                            <!-- <td style="text-align:center;">Tipo anestesia</td>
                                            <td style="text-align:center;">Sala recuperación (Minutos)</td>
                                            <td style="text-align:center;">Tiempo uso equipos (Minutos)</td>
                                            <td style="text-align:center;">Tiempo Cx. (Minutos)</td> -->
                                            <!-- <td style="text-align:center;"><?=TIEMPO_MINUTOS?></td> -->
                                            <td style="text-align:center;">
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </table>
                                    <div id="div_recuperacion_datos" style="display:none;"></div>
                                    <div id="otros_datos_acto_qx" class="datos-adds-eventos">
                                        <!-- <table>
                                            <tr>
                                                <td class="encabezadoTabla">Tipo anestesia</td>
                                                <td class="fila2">
                                                    <select id="wtipo_anestesia_cx" name="wtipo_anestesia_cx" class="requerido">
                                                        <option value="">Seleccione</option>
                                                        <?php
                                                        /*foreach ($arr_tipo_anestesia as $key => $value) {
                                                            echo '<option value="'.$key.'">'.utf8_encode($value).'</option>';
                                                        }*/
                                                        ?>
                                                    </select>
                                                </td>
                                                <td class="encabezadoTabla">Sala recuperación (horas)</td>
                                                <td class="fila2">
                                                    <input type="text" value="0" id="wtiempo_sala_recuperarcion" name="wtiempo_sala_recuperarcion" class="save_ok numerico requerido" placeholder="0-<?=TIEMPO?>" maxlength="3" style="width:58px;" />
                                                </td>
                                                <td class="encabezadoTabla">Tiempo uso equipos (horas)</td>
                                                <td class="fila2">
                                                    <input type="text" value="0" id="wtiempo_uso_minutos" name="wtiempo_uso_minutos" class="save_ok numerico requerido" placeholder="0-<?=TIEMPO?>" maxlength="3" style="width:58px;" />
                                                </td>
                                                <td class="encabezadoTabla"><?=TIEMPO_MINUTOS?> Cx.</td>
                                                <td class="fila2">
                                                    <input type="text" value="" id="wtiempo_minutos_cx" name="wtiempo_minutos_cx" class="save_ok numerico requerido" placeholder="0-<?=TIEMPO?>" maxlength="3" style="width:58px;" />
                                                </td>
                                            </tr>
                                        </table> -->
                                        <div id="div_param_baseliquidacion" style="display:none;">
                                            <ul>
                                                <li><span class="encabezadoTabla">Base liquidación (para Cx. Múltiples)</span>
                                                    <span>
                                                        <select id="wbaseliquidacion" name="wbaseliquidacion" class="requerido save_ok">
                                                            <option value="">Seleccione</option>
                                                            <?php
                                                            foreach ($arr_liquidacion as $key => $value) {
                                                                echo '<option value="'.$key.'">'.$key.'-'.utf8_encode($value).'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="" style="width:100%; text-align:center;">
                                        <!-- <span id="btn_liquidar_pop" class="st_boton" onclick="generarLiquidar();">Liquidar</span> -->
                                        <button id="btn_liquidar_pop" onclick="generarLiquidar(this,'','');" class="btn_loading">Liquidar</button>
                                    </div>
                                </div>
                        </fieldset>
                    </div>
                </div>

                <br />
                <fieldset>
                    <legend align="left">Liquidación <span class="label_procedimiento" >procedimientos</span></legend>
                    <div id="div_cont_liquidacion_modal" style="" ></div>
                </fieldset>
                <br />

                <table style="width: 100%;" >
                    <tr>
                        <td style="text-align:center;">
                            <div id="div_mensajes_alerta" style="font-size: 8pt; color:red; text-align:left;font-weight:bold;">&nbsp;</div>
                        </td>
                    </tr>
                </table>

                <div width='' id='accordionPendientes' style="text-align:left;width: 1250px;" class="div_alinear" >
                    <h3>CIRUGÍAS PENDIENTES DE LIQUIDAR POR FALTA DE TARIFAS</h3>
                    <div id="datos_pendientes">
                        <fieldset id="">
                            <legend align="left">Cirugías pendientes</legend>

                            <div id="div_contenedor_detalle_pendientes">
                                <?php
                                echo html_cirugiasPendientes(cirugiasPendientesLiquidar($conex, $wbasedato, '', ''));
                                ?>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <br>
                <br>

                <div width='' id='accordionDetalles' style="text-align:left;width: 1250px;" class="div_alinear" >
                    <h3>DETALLES LIQUIDACIONES</h3>
                    <div id="datos_lisquidaciones_detalle">
                        <fieldset id="">
                            <legend align="left">Liquidaciones</legend>

                            <div id="div_contenedor_detalle">
                                <?php
                                $html_actos = pintarDetalleLiquidaciones($arr_detalle_liquidaciones, $arr_parametros);
                                echo $html_actos;
                                ?>
                            </div>
                        </fieldset>
                    </div>
                </div>

            </div>
        </td>
    </tr>
</table>
</body>
</html>