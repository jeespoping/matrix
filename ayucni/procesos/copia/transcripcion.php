<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : transcripcion.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 22 Febrero de 2016

 DESCRIPCION: Programa para generar las transcripciones de cardiología.


 Notas:
 --
*/ $wactualiza = "2020-04-20"; /*
 ACTUALIZACIONES:
 2020-04-20: Jessica Madrid Mejía
 Se deshabilita el select que permite agregar nuevos examenes si el paciente esta inactivo en el ingreso seleccionado.
 2019-10-29: Jerson Trujillo
 Se inhabilita el boton para agregar nuevo examen mientras este cargando la creacion de un nuevo examen.
 Marzo 5 2019 Freddy Saenz
  Se modifica el script para que al momento de grabar el examen le asigne la fecha del cargo y no la fecha actual.
  SALVEDADES 
   1. Si no se encuentra el CUPS  o no corresponde al examen seleccionado continua con la fecha actual 
   y no la del cargo
   2. El cargo no esta grabado todavia , lo graba con la fecha actual .
   3. Si un cargo esta sin grabar puede que tome la fecha del penunltimo examen , el ultimo no ha sido grabado
    puede ocurrir que el 2 vaya antes que el 1.
	
 Octubre 31 2017 Edwar Jaramillo:
    * Cuando se consulta el maestro de opciones para los campos autocompletar, se verifica si tienen el caracter "\n" y se reemplaza por vacío porque ese caracter
        genera una falla al cargar los datos y no permite que se inicialicen los campos autocompletar ni los campos del ckeditor.
    * Se modifica la forma en que se crean y se consultan los campos autocompletar que tienen una cantidad de opciones muy grande, se valida si el select del autocompletar
        tiene más de 1000 opciones de selección entonces no se cargan esos datos del select desde el inicio sino que se consultar mediante ajax cuando usen el campo
        autocompletar (se restringe la consulta ajax a digitar como mínimo 4 caracteres para que se ejecute el ajax y la consulta en base de datos se limita a máximo 50
        registros por consulta para que sea mucho más rápida la respuesta).
 Julio 25 2017 Edwar Jaramillo:
    * Adición de validación para no permitir la impresión de valores de números o formulas cuyo resultado sea negativo.
 Marzo 13 2017 Edwar Jaramillo:
    * Alineación y tamaño de imagen para firma del médico.
 Enero 20 2017 Edwar Jaramillo:
    * Al momento de imprimir vista previa se valída si en los campos normales hay puntos, pues en los formularios ese campo es obligatorio
        pero algunos cardiólogos no dictan todos los campos por lo que las secretarias deben colocar un punto "." para que les permita imprimir,
        es en esos casos en los que se está validando si el valor del normal es un punto para tenerlo en cuenta como vacío y no salga en la impresión
        y de esa forma evitar que la secretaria lo tenga que eliminar manualmente.
 Octubre 27 2016 Edwar Jaramillo:
    * Correción para que el botón "btn_guardar_examen" creado en la modal de textos normales para que no genere más de una petición de guardado y evitar que quede información repetida en la
        tabla de encabezado y detalle del examen.
    * Se adiciona la entidad a la impresión de la transcripción.
    * Al cargar el encabezado de los datos del paciente se valída si el responsable en la admisión es particular o entidad, en caso de ser particular, en el nombre de entidad responsable se muestra "Particular".
    * En la impresión se aumentan espacios antes de la firma del médico y se disminuyen despues de la firma.
 Octubre 17 2016 Edwar Jaramillo:
    * En el encabezado de datos del paciente ahora se muestra la entidad responsable del paciente, (PENDIENTE VALIDAR CUANDO LA EMPRESA ES PARTICULAR, MOSTRAR NOMBRE CORRECTO).
    * Ayuda en los campos normales para indicar que se puede maximizar o minimizar todo el campo o seleccionar todo el texto con atajo de teclado.
    * Al inicializar los campos ckeditor (campos normales editables) se incluyen .js que configuran la barra de herramientas, se hace una pequeña modificación para que cada vez que se
        cargue el js no se haga desde cache y se tomen los cambios realizados en el script de configuración.
    * En la barra de título de la página y la ventana modal de campos normales se muestra el número de documento del paciente.
    * Se habilita botón "Guardar parcialmente" en la ventana flotante de campos normales para que esté visible todo el tiempo.
    * Los títulos de los campos normales se cambian de ubicación para que el campo de texto editable tenga más espacio para editar.
    * Se habilita el redimencionar en los campos de texto de los campos normales.
 Octubre 11 2016 Edwar Jaramillo:
    * Parámetro "ckeditor_ok_ayucni" para activar o inactivar el funcionamiento del plugin ckeditor para los campos textarea.
    * se incorpora nuevo plugin CKEditor para dar formato al texto incluído en los textarea.
    * Al leer los campos de texto memo desde la BD se cambian todos los saltos de línea "\n" por <br>.
    * Se crea un nuevo campo en ayucni_000003 para guardar los campos normales pero con etiquetas de html para poder mostrar tablas en los campos textarea, se lee cuando el
        parámetro "ckeditor_ok_ayucni" esté activo.
 Septiembre 29 2016 Edwar Jaramillo:
    * Validación en query que consulta los exámenes realizados, no era eficiente cuado la historia e ingreso eran vacíos.
    * Ventana flotante de campos normales se ajusta automáticamente en margen derecho cuando cambia la resolución del monitor.
 Septiembre 06 2016 Edwar Jaramillo:
    * Las transcripciones migradas ahora tienen en cuenta el usuario que finaliza el examen con el código de usuario que tenía en el programa antiguo.
 Septiembre 02 2016 Edwar Jaramillo:
    * En la consulta de estudios no se tiene en cuenta solo la historia e ingreso sino también unión por documento y tipo de documento, se creó un tercer unión para buscar por documento solo para los registros migrados.
    * Nuevo campo para seleccionar equipo.
    * Se le adiciona un número secuencial a los campos de examen para que las secretarias puedan identificar los campos por secuencia.
    * Al imprimir se busca el servicio del paciente y habitación.
 Agosto 23 2016 Edwar Jaramillo:
    * Los campos tipo título ahora permiten ver u ocultar los campos que estan relacionados con el en la sección detalle del examen.
    * Nuevo campo para guardar el usuario que cierra el examen y se guarda fecha-hora de cierre, el usuario responsable de la transcripción es quien cierra el examen.
    * Nueva opción para inactivar/activar un examen.
    * En los resultados de las formulas en el formulario de transcripción se resaltan los valores con un color diferente.
    * En la opción de imprimir resultado se adiciona la ubicación del paciente y la habitación si corresponde.
    * Se mejora la lógica para asignar los permisos de usuario en la interfaz.
 Agosto 09 2016 Edwar Jaramillo:
    * Se adiciona un nuevo estado para saber si el examen está activo o está cerrado, se desarrolla funcionalidad para permitir editar o no de acuerdo al estado del examen,
         esto se complementa con el uso de permisos de usuario, los que no tienen permiso de editar, no lo podrán hacer una vez finalicen el examen.
    * Se desarrolló la opción de solicitar la firma del médico que da el visto bueno de la transcripción solicitando la clave hce de ese médico pero por ahora no está activa esa
        parte del código, se está controlando con el parámetro habilitar_cerrar_con_firma desde javascript, si en algún momento lo solicitan se puede activar desde esa variable.
        Si el médico es el que firma, en ese momento se cambia el examen a estado cerrado, pero si no está habilitada esta opción entonces quien finalmente cierra el examen es la secretaria.
 Febrero 22 de 2015 Edwar Jaramillo:
    *Fecha de la creación del programa.

**/

$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");






if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
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
// $user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);

/*****  DICCIONARIO LOCAL *****/
// define('PROCEDIMIENTO'      ,'Procedimiento');

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
 * [existeTabla: Función para consultar si una tabla está creada o no en la base de datos] [updt-83]
 * @param  [type] $conex     [description]
 * @param  [type] $wemp_pmla [description]
 * @param  [type] $wbasedato [Prefijo de la tabla en la que se quere buscar]
 * @param  [type] $tabla     [Tabla sin prefijo que se quiere verificar]
 * @return [type]            [description]
 */
function existeTabla($conex, $wemp_pmla, $wbasedato, $tabla)
{
    $tabla  = $wbasedato.'_'.$tabla;
    $sql    = "SHOW TABLES LIKE '{$tabla}'";
    $result = mysql_query($sql);
    return (mysql_num_rows($result) == 1) ? true: false;
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
 * [nombreDiaSemana description: Esta funci? recibe los n?mero de a?, m? y d?, y devuelve el nombre del d? de la semana en la fecha indicada]
 * @param  [type] $ano [description]
 * @param  [type] $mes [description]
 * @param  [type] $dia [description]
 * @return [type]      [String, nombre del d? de la semana]
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
 * [calcularEdadPaciente: función para calcular la edad, dada la fecha de nacimiento]
 * @param  [type] $fecha_nacimiento [description]
 * @return [type]                   [description]
 */
function calcularEdadPaciente($fecha_nacimiento)
{
    //Edad
    // $fecha_nacimiento = "1984-09-13";
    $ann=(integer)substr($fecha_nacimiento,0,4)*360 +(integer)substr($fecha_nacimiento,5,2)*30 + (integer)substr($fecha_nacimiento,8,2);
    $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
    $ann1=($aa - $ann)/360;
    $meses=(($aa - $ann) % 360)/30;
    if ($ann1<1){
        $dias1=(($aa - $ann) % 360) % 30;
        $wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
    } else {
        $dias1=(($aa - $ann) % 360) % 30;
        // $wedad=(string)(integer)$ann1." año(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)"; // En años meses y días
        $wedad=(string)(integer)$ann1." año(s) ";// Solo en años
    }

    // en años
    return $wedad;
}

/**
 * Crea un array con los menús y submenús, en una posición esta el array (árbol) solo con los códigos de los menús y en otra posición están los códigos con
 * la descripción de esos menús y las urls.
 * Primero se hace una consulta de menús principales, luego una consulta de submenús y posteriormente se unen para armar el arbol completo con menús y sus submenús.
 *
 * @param string $wemp
 * @param id# $conex
 * @param array $submenus_sobrantes: no esta operando pero si en algún caso sobrara un menú es porque esta mal asociado en la tabla y se debería informar.
 * @param array $info_use : Filtros para ver a que opciones tendrá acceso.
 * @return unknown
 */
function crearArbolMenus($wbasedato_ayu, $wemp_pmla, $conex, $wcodigo_examen, &$submenus_sobrantes, $caso_arbol = 'administrar_menus')
{
    $arbol_menu = getMenusQuery($wbasedato_ayu, $wemp_pmla, $conex, 'principal', $wcodigo_examen, $caso_arbol);
    $sub_menu = getMenusQuery($wbasedato_ayu, $wemp_pmla, $conex, 'submenus', $wcodigo_examen, $caso_arbol);
    // echo "<pre>".print_r($arbol_menu,true)."</pre>";
    // echo "<pre>sub_menu: ".print_r($sub_menu,true)."</pre>";
    if($caso_arbol != '' && $caso_arbol == 'administrar_menus')
    {
        // echo "<pre>".print_r($sub_menu['menus_cod']["submenus"],true)."</pre>";
        $submenus_sobrantes = armarArbolMenu($arbol_menu['menus_cod'], $sub_menu['menus_cod']["submenus"], $caso_arbol, '');
    }
    else
    {
        $submenus_sobrantes = armarArbolMenu($arbol_menu['menus_cod'], $sub_menu['menus_cod'], $caso_arbol, '');
    }

    // $merge_info = array_merge($arbol_menu['menus_info'], $sub_menu['menus_info']); // no usar merge puesto que al hacerlo se dañan algunos indices del array p.e. '10' queda como '0'
    $merge_info = array();
    $merge_ids = array();
    foreach($arbol_menu['menus_info'] as $key => $value)
    {
        $merge_info[$key] = $value;
    }

    foreach($sub_menu['menus_info'] as $key => $value)
    {
        $merge_info[$key] = $value;
    }

    foreach($arbol_menu['menus_ids'] as $key => $value)
    {
        $merge_ids[$key][] = $value[0];
    }

    foreach($sub_menu['menus_ids'] as $key => $value)
    {
        $merge_ids[$key][] = $value[0];
    }

    $arbol_completo = array('menus_cod'=>$arbol_menu['menus_cod'],'menus_info'=>$merge_info,'menus_ids'=>$merge_ids);
    // echo "<pre>".print_r($arbol_menu,true)."</pre>";
    // echo "<pre>".print_r($arbol_completo,true)."</pre>";
    return $arbol_completo;
}


/**
 * getMenus()
 * retorna un array con la estructura del menú principal, o de la lista de submenús o subpestañas,
 * consulta en las tablas relacionadas a menús los menús que de acuerdo a los filtros ingresdos están habilitados para ver.
 *
 * @param string $wemp_pmla : código de la empresa.
 * @param link $conex       : link de conexión a la base de datos.
 * @param string $tipo      : define que tipo de menú retornar, por defecto si no se especifíca se retorna el menú principal.
 * @return array
 */
function getMenusQuery($wbasedato_ayu, $wemp_pmla, $conex, $tipo = 'principal', $wcodigo_examen, $caso_arbol)
{
    $qMp = '';
    switch($tipo)
    {
        case 'principal':
                /* LISTA LOS MENUS PRINCIPALES */
                $qMp = "SELECT  Dcfcca AS cod_tab, Dcfnom AS nombre_subtab, Dcfcca AS cod_subtab, id AS id_campo, (Dcford * 1) AS orden
                        FROM    {$wbasedato_ayu}_000002
                        WHERE   Dcfenc = '{$wcodigo_examen}'
                                AND Dcfdep = ''
                                AND Dcfest = 'on'
                        ORDER BY (Dcford * 1)";
                break;
        case 'submenus' :
                /* LISTA LOS SUBMENUS */
                $qMp = "SELECT  Dcfcca AS cod_subtab, Dcfnom AS nombre_subtab, Dcfdep AS cod_tab, id AS id_campo, (Dcford * 1) AS orden
                        FROM    {$wbasedato_ayu}_000002
                        WHERE   Dcfenc = '{$wcodigo_examen}'
                                AND Dcfdep <> ''
                                AND Dcfest = 'on'
                        ORDER BY (Dcford * 1)";
                break;
    }

    $menus_cod = array();
    $menus_info= array();
    $menus_ids = array();
    $menus = array();
    if($qMp != '')
    {
        // echo "<pre>$tipo: ".$qMp."</pre>";
        $resp = mysql_query($qMp,$conex);

        while( $row = mysql_fetch_array($resp) )
        {
            $llave = $row['cod_tab'].'->'.$row['cod_subtab'];
            if ($tipo == 'principal')
            { $llave = $row['cod_subtab']; }

            // $menus_cod[$llave] = array("id_campo"=>$row['id_campo'], "submenus"=>array());
            if($caso_arbol != '' && $caso_arbol == 'administrar_menus')
            {
                $menus_cod[$llave] = array("id_campo"=>$row['id_campo'], "submenus"=>array(), 'insert_antes_de'=>"");
            }
            else
            {
                $menus_cod[$llave] = array();
            }

            $menus_info[$row['cod_subtab']]['info'] = array(    'cod_tab'       =>$row['cod_tab'],
                                                                'cod_subtab'    =>$row['cod_subtab'],
                                                                'nombre_subtab' =>utf8_encode($row['nombre_subtab']),
                                                                'orden'         =>$row['orden'],
                                                                'id_campo'      =>$row['id_campo'],
                                                                );

            $menus_ids[$row['cod_subtab'].'_'.$row['id_campo']][] = $menus_info[$row['cod_subtab']]['info'];
            // if($info_use != '')
            // {
            //     $menus_info[$row['cod_subtab']]['info']['cod_costo'] = $row['cod_costo'];
            //     $menus_info[$row['cod_subtab']]['info']['cod_cargo'] = $row['cod_cargo'];
            // }
        }
        if($caso_arbol != '' && $caso_arbol == 'administrar_menus')
        {
            $menus['menus_cod']["submenus"] = $menus_cod;
        }
        else
        {
            $menus['menus_cod'] = $menus_cod;
        }
        $menus['menus_info']= $menus_info;
        $menus['menus_ids'] = $menus_ids;
    }
    // echo '<pre>';print_r($menus);
    return $menus;
}

/**
 * A partir de las opciones de menú principales, esta función empieza a ubicar cada submenú dentro de cada menú principal.
 *
 * @param array $arbol          : array con los códigos de menús principales.
 * @param array $arr_submenus   : array con los códigos de submenús.
 * @return array: completo, con todos los submenús asociados a menús principales.
 */
function armarArbolMenu(&$arbol, $arr_submenus, $caso_arbol='', $sobrante="")
{
    $arr_sobran = array();
    foreach($arr_submenus as $key => $value)
    {
        // echo '<br>';echo "<div style='text-align:left;'><pre><br />en armar $key: ";print_r($arbol["submenus"]);echo '</pre></div>';
        if(!buscarPosicionMenu($arbol, $key, $value, $caso_arbol, $sobrante))
        {
            $arr_sobran[$key] = $value;
        }
    }

    $sin_ubicar = array();
    if(count($arr_sobran) > 0 && (count($arr_sobran) == count($arr_submenus)))
    {
        $arr_sobran = array();
        $sin_ubicar = $arr_sobran;
    }

    if(count($arr_sobran) > 0)
    { armarArbolMenu($arbol, $arr_sobran, $caso_arbol, "on"); }

    return $sin_ubicar;
}

/**
 * buscarPosicionMenu()
 * Busca posicionar un submenú en un menú principal.
 *
 * @param array $arbol      :   Parte de un array (hijos)
 * @param string $cod_menu  :   Código o llave del tipo 05->06 (significa que el menu 05 contiene al submenú 06)
 * @return boolean          retorna valor de verdad, y $arbol modificado en caso de acomodar una posición.
 */
function buscarPosicionMenu(&$arbol, $cod_menu, $info_submenu, $caso_arbol='', $sobrante='')
{
    if(is_array($arbol) && count($arbol) > 0)
    {
        $explode = explode('->',$cod_menu);
        $padre = $explode[0];
        $hijo  = $explode[1];
        // echo $padre.'|';
        // echo $cod_menu.'|'.PHP_EOL;
        if($caso_arbol != '' && $caso_arbol == 'administrar_menus')
        {
                // echo "<pre>[$padre][submenus][$hijo]"."</pre>";
                // echo "<pre>".print_r($arbol["submenus"],true)."</pre>";
            if(array_key_exists($padre,$arbol["submenus"]))
            {
                // Si se está intentando insetar un menu sobrante despues de haber recorrido la primer vez la función armarArbolMenu (arr_sobran)
                if($sobrante == 'on' && $arbol["submenus"][$padre]["insert_antes_de"] == '')
                {
                    $indices_array = array_keys($arbol["submenus"][$padre]["submenus"]);
                    $arbol["submenus"][$padre]["insert_antes_de"] = (count($indices_array) > 0) ? $indices_array[0]: "";
                }

                if($sobrante == 'on' && $arbol["submenus"][$padre]["insert_antes_de"] != '')
                {
                    $insert_antes_de = $arbol["submenus"][$padre]["insert_antes_de"];
                    $insert = array($hijo=>$info_submenu);
                    $result = array_insert_before($insert_antes_de,$arbol["submenus"][$padre]["submenus"], $insert);
                    $arbol["submenus"][$padre]["submenus"] = $result;
                }
                else
                {
                    $arbol["submenus"][$padre]["submenus"][$hijo] = $info_submenu;
                }

                return true;
            }
            elseif(count($arbol["submenus"])>0)
            {
                $encontro_padre = false;
                foreach($arbol["submenus"] as $key => $value)
                {
                    if($encontro_padre = buscarPosicionMenu($arbol["submenus"][$key],$cod_menu, $info_submenu, $caso_arbol, $sobrante))
                    { break; }
                }
                return $encontro_padre;
            }
            else
            { return false; }
        }
        else
        {
            if(array_key_exists($padre,$arbol))
            {
                $arbol[$padre][$hijo] = array();
                return true;
            }
            elseif(count($arbol)>0)
            {
                $encontro_padre = false;
                foreach($arbol as $key => $value)
                {
                    if($encontro_padre = buscarPosicionMenu($arbol[$key],$cod_menu, $info_submenu, $caso_arbol, $sobrante))
                    { break; }
                }
                return $encontro_padre;
            }
            else
            { return false; }
        }
    }
    else
    { return false; }

    return false;
}

/**
 * Inserts a new key/value before the key in the array.
 *
 * @param $key  The key to insert before.
 * @param $array  An array to insert in to.
 * @param $new_key  The key/array to insert.
 * @param $new_value  An value to insert.
 * @return array
 */
function array_insert_before($key, array $array, $new_key, $new_value = null) {
    if (array_key_exists($key, $array)) {
        $new = array();
        foreach($array as $k => $value) {
            if ($k === $key) {
                if (is_array($new_key) && count($new_key) > 0) {
                    $new = array_merge($new, $new_key);
                } else {
                    $new[$new_key] = $new_value;
                }
            }
            $new[$k] = $value;
        }
        return $new;
    }
    return false;
}

/**
 * [valoresGuardadosExamen: Según los tipos de campos, esta función se encarga de generar el código html que se va a mostrar en el plugin Cleditor (WYSIGYG)]
 * @param  [type] $wbasedato_ayu                [Prefijo de la base de datos]
 * @param  [type] $wemp_pmla                    [Empresa]
 * @param  [type] $conex                        [Link de conexión a la base de datos]
 * @param  [type] $arr_campos_examen            [Configuración de los campos del examen]
 * @param  [type] $arr_campos_examen_respuestas [Respuestas guardadas para cada unos de los campos del examen]
 * @param  [type] $cmp_resultado                [Valor resultado guardado para el campo del examen]
 * @param  [type] $arr_maestro_tablas           [Maestro de tablas de selección - Opciones que se usan en los campos select o listas de selección]
 * @param  [type] $wcod_campo                   [Código del campo del examen]
 * @param  [type] $sangria                      [Cantidad de pixeles que se debe mover hacia la derecha la alineación html (style margen izquierdo)]
 * @return [type]                               [tr html]
 */
function valoresGuardadosExamen($ckeditor_ok_ayucni, $wbasedato_ayu, $wemp_pmla, $conex, $arr_campos_examen, $arr_campos_examen_respuestas, $cmp_resultado, $arr_maestro_tablas, $wcod_campo, $sangria)
{
    $arr_campo_resultado = array();
    $tr = "";

    $cmp_resultado = (array_key_exists($wcod_campo, $arr_campos_examen_respuestas)) ? $arr_campos_examen_respuestas[$wcod_campo]: "";

    // foreach ($arr_campos_examen as $wcod_campo => $arr_config)
    {
        $arr_config = $arr_campos_examen[$wcod_campo];
        // $cmp_resultado = (array_key_exists($wcod_campo, $arr_campos_examen_respuestas)) ? $arr_campos_examen_respuestas[$wcod_campo]: "";

        $tipo_dato = $arr_config["tip_campo"];

        $css                = '';//($count_filas % 2 == 0) ? 'fila1': 'fila2';
        $cssLabel           = 'font_label';
        $campo_requeridocss = "";
        $campo_reqspn       = "";
        $campo_margenIzr    = "";
        $rang_unid          = ($arr_config["rang_unid"] != '') ? ' <span style="font-size:8pt;">'.($arr_config["rang_unid"]).'</span>' : '';
        $se_imprime         = $arr_config["se_imprime"];
        $cod_defecto        = '';
        $nom_defecto        = '';
        $observacion_add    = '';

        $imprimir_negativo = true;
        if(is_numeric($cmp_resultado) && ($cmp_resultado*1 < 0))
        {
            $imprimir_negativo = false;
        }

        if($se_imprime == 'on')
        {
            switch (strtolower($tipo_dato)) {
                case 'archivo':
                case 'encabezado':
                        // select
                        $select = "";
                        if($arr_config["maestro_tablas"] != '' && array_key_exists($arr_config["maestro_tablas"], $arr_maestro_tablas))
                        {
                            $cod_defecto = $cmp_resultado;
                            $nom_defecto = (array_key_exists($cod_defecto, $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"])) ? ($arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"][$cod_defecto]) : '';
                        }

                        if($cmp_resultado != "")
                        {
                            if($arr_config["imprime_seguido"] == 'on')
                            {
                                $tr = ' <tr>
                                            <td colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="padding-left: '.$sangria.'px;">'.($arr_config["nom_campo"]).': '.$nom_defecto.$rang_unid.'</td>
                                        </tr>';
                            }
                            else
                            {
                                $tr = ' <tr>
                                            <td colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left: '.$sangria.'px;">'.($arr_config["nom_campo"]).'</td>
                                            <td>'.$nom_defecto.'</td>
                                            <td>'.$rang_unid.'</td>
                                        </tr>';
                            }
                        }
                    break;

                case 'pie_de_pagina':
                        // select
                        $select    = "";
                        $img_firma = "";
                        if($arr_config["maestro_tablas"] != '' && array_key_exists($arr_config["maestro_tablas"], $arr_maestro_tablas))
                        {
                            $cod_defecto = $cmp_resultado;
                            $nom_defecto = (array_key_exists($cod_defecto, $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"])) ? ($arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"][$cod_defecto]) : '';
                            $observacion_add = (array_key_exists($cod_defecto, $arr_maestro_tablas[$arr_config["maestro_tablas"]]["observaciones_opciones"])) ? ($arr_maestro_tablas[$arr_config["maestro_tablas"]]["observaciones_opciones"][$cod_defecto]) : '';

                            $sqlPie = " SELECT  Procmx AS codigo_matrix
                                        FROM    {$wbasedato_ayu}_000015
                                        WHERE   Protbl = '{$arr_config['maestro_tablas']}'
                                                AND Procod = '{$cod_defecto}'";
                            if($resultCod = mysql_query($sqlPie, $conex))
                            {
                                if(mysql_num_rows($resultCod) > 0)
                                {
                                    $row_cod = mysql_fetch_assoc($resultCod);
                                    $codigo_matrix = $row_cod["codigo_matrix"];

                                    $dir = '../../images/medical/hce/Firmas';
                                    $arr_archivos_r = array();
                                    $arr_archivos_nombres = array();
                                    if (is_dir($dir))
                                    {
                                        if ($gd = opendir($dir))
                                        {
                                            while ($archivo = readdir($gd)) {
                                                //echo "nombre de archivo: $archivo : tipo de archivo: " . filetype($dir . $archivo) . "\n";
                                                if($archivo != '.' && $archivo != '..'){
                                                    $arr_archivos_r[] = $dir."/".$archivo;
                                                    $archivo_expl     = explode(".", $archivo);

                                                    $pos_ext         = count($archivo_expl)-1;
                                                    $extension       = $archivo_expl[$pos_ext];
                                                     // echo "count:".$pos_ext.' - '.print_r($archivo_expl, true).PHP_EOL;
                                                    unset($archivo_expl[$pos_ext]); //elimina la última posición que debe ser la extensión
                                                    $nombre_archivo1 = implode("_", $archivo_expl);
                                                    $nombre_archivo2 = implode(".", $archivo_expl); //Se concatenan de nuevo con puntos por si el nombre tambien tiene puntos a parte de la extensión
                                                    if(!empty($nombre_archivo1) && !array_key_exists($nombre_archivo1, $arr_archivos_nombres))
                                                    {
                                                        $arr_archivos_nombres[$nombre_archivo1] = array("directorio" => $dir, "nombre_archivo"=>$nombre_archivo2, "extension"=> $extension);
                                                    }
                                                    // $arr_archivos_nombres[] = $dir."/".$archivo;
                                                }
                                            }
                                            closedir($gd);
                                        }

                                        // echo print_r($arr_archivos_nombres,true);

                                        $codigo_matrix = str_replace(".", "_", $codigo_matrix);//se cambian los puntos en el nombre por "_" pues el nombre en el array está en el indice
                                                                                                // y puede fallar si el nombre tiene puntos.
                                        if(array_key_exists($codigo_matrix, $arr_archivos_nombres))
                                        {
                                            $img_firma = $arr_archivos_nombres[$codigo_matrix]["directorio"].'/';
                                            $img_firma .= $arr_archivos_nombres[$codigo_matrix]["nombre_archivo"];
                                            $img_firma .= ".".$arr_archivos_nombres[$codigo_matrix]["extension"];
                                            $img_firma = '<img src="'.$img_firma.'" alt="" style="max-height: 130px;max-width:210px;"><br>';
                                        }
                                    }
                                    else
                                    {
                                        //echo ">[2] ".$dir.PHP_EOL;
                                    }
                                }
                            }
                            else
                            {
                                //Error
                            }
                        }

                        if($cmp_resultado != "")
                        {
                            if($arr_config["imprime_seguido"] == 'on')
                            {
                                // $tr = ' <tr>
                                //             <td colspan="3">&nbsp;</td>
                                //         </tr>
                                //         <tr>
                                //             <td colspan="3">&nbsp;</td>
                                //         </tr>
                                //         <tr>
                                //             <td colspan="3" style="padding-left: '.$sangria.'px;"><b>'.$nom_defecto.$rang_unid.'</b><br>'.$observacion_add.'</td>
                                //         </tr>';
                                $tr = ' <div style="">
                                            <table>
                                            <tr>
                                                <td style="text-align:center;">
                                                    '.$img_firma.'
                                                </td>
                                            </tr>
                                                <td style="">
                                                    <b>'.$nom_defecto.$rang_unid.'</b><br>'.$observacion_add.'
                                                </td>
                                            </table>
                                        </div>';
                            }
                            else
                            {
                                $tr = ' <tr>
                                            <td colspan="3">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left: '.$sangria.'px;"><b>'.($arr_config["nom_campo"]).'</b></td>
                                            <td>'.$nom_defecto.'</td>
                                            <td>'.$rang_unid.'</td>
                                        </tr>';
                            }
                        }
                    break;

                case 'boleano':
                        // checkbox
                        $campo_reqspn = ($cmp_resultado == 'on') ? 'Si': 'No';
                        $tr = ' <tr>
                                    <td style="padding-left: '.$sangria.'px;">'.($arr_config["nom_campo"]).'</td>
                                    <td>'.$campo_reqspn.'</td>
                                    <td>'.$rang_unid.'</td>
                                </tr>';
                    break;

                case 'conjunto':
                    break;

                case 'formula':
                        if($cmp_resultado != '' && $imprimir_negativo)
                        {
                            $tr = ' <tr>
                                        <td style="padding-left: '.$sangria.'px;">'.($arr_config["nom_campo"]).'</td>
                                        <td>'.$cmp_resultado.'</td>
                                        <td>'.$rang_unid.'</td>
                                    </tr>';
                        }
                    break;

                case 'memo':
                        // textarea
                        if($cmp_resultado != '')
                        {
                            $txt = ($cmp_resultado != '') ? $cmp_resultado: "";
                            // if($ckeditor_ok_ayucni != 'on')
                            {
                                $txt = preg_replace("/[\n]+/", '<br>', $txt);
                            }

                            $tr = ' <tr>
                                            <td colspan="3">'.($arr_config["nom_campo"]).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="padding-left: '.$sangria.'px;"><p style="text-align:justify;">'.$txt.'</p></td>
                                    </tr>';
                        }
                    break;

                case 'multiseleccion':
                        // select > div_multiselect
                        $cmp_resultado = ($cmp_resultado != '') ? explode(",", $cmp_resultado) : array();

                        $optsDiv = array();
                        if(count($cmp_resultado) > 0)
                        {
                            foreach ($cmp_resultado as $key_idx => $value_res)
                            {
                                $res_cod = $value_res;
                                $res_nom = "";
                                if(array_key_exists($arr_config["maestro_tablas"], $arr_maestro_tablas) && array_key_exists($value_res, $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"]))
                                {
                                    $res_nom = $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"][$value_res];
                                }
                                $optsDiv[] = '<span>'.$res_nom.'</span>';
                            }
                        }

                        $divdep = implode(", ", $optsDiv);
                        if(count($cmp_resultado) > 0)
                        {
                            if($arr_config["imprime_seguido"] == 'on')
                            {
                                $tr = ' <tr>
                                            <td colspan="3" style="padding-left: '.$sangria.'px;">'.($arr_config["nom_campo"]).': '.$divdep.$rang_unid.'</td>
                                        </tr>';
                            }
                            else
                            {
                                $tr = ' <tr>
                                            <td style="padding-left: '.$sangria.'px;">'.($arr_config["nom_campo"]).'</td>
                                            <td>'.$divdep.'</td>
                                            <td>'.$rang_unid.'</td>
                                        </tr>';
                            }
                        }
                    break;

                case 'numero':
                        // input-text decimal
                        if($cmp_resultado != '' && $imprimir_negativo)
                        {
                            $tr = ' <tr>
                                        <td style="padding-left: '.$sangria.'px;">'.($arr_config["nom_campo"]).'</td>
                                        <td>'.$cmp_resultado.'</td>
                                        <td>'.$rang_unid.'</td>
                                    </tr>';
                        }
                    break;

                case 'texto':
                        $css = '';
                        $tr = ' <tr>
                                    <td colspan="3" style="padding-left: '.$sangria.'px;">&nbsp;</td>
                                </tr>';
                    break;

                case 'titulo':
                        $txt = ($arr_config["nom_campo"]);
                        $tr = ' <tr>
                                    <td colspan="3">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="padding-left: '.$sangria.'px;"><b>'.($arr_config["nom_campo"]).'</b></td>
                                </tr>';
                    break;

                case 'normal':
                        $txt = ($cmp_resultado != '') ? $cmp_resultado: "";
                        // if($ckeditor_ok_ayucni != 'on')
                        {
                            // En muy pocos casos un médico no dicta campos normales y esos campos normales son obligatorios, las secretarias deben poner un punto
                            // para que el programa deje grabar pero luego deben borrar al imprimir el examen, para evitar que la secretaria lo haga entonces se valída si
                            // van solo puntos en el texto y se asume como campo vacío.
                            $txtX = trim(preg_replace('/[ ]+/', '', $txt));
                            $txtX = trim(preg_replace("/[\n]+/", '', $txtX));
                            $txtX = trim(preg_replace("/[.]+/", '', $txtX));

                            $txt = ($txtX == '') ? '': $txt;

                            $txt = preg_replace("/[\n]+/", '<br>', $txt);
                        }
                        if($txt != "")
                        {
                            $tr = ' <tr>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="padding-left: '.$sangria.'px;"><b>'.(strtr(strtoupper($arr_config["nom_campo"]),"àèìòùáéíóúñ","ÀÈÌÒÙÁÉÍÓÚÑ")).'</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><p style="text-align:justify;">'.$txt.'</p></td>
                                    </tr>';
                        }
                    break;

                default:
                    # code...
                    break;
            }
        }
    }

    return $tr;
}

/**
 * [pintarMenuSeleccion: Se encarga de recorrer todas las respuestas de los campos del examen y generar las filas (tr's) html para mostrar el resultado completo del examen
 *                         en el navegador web]
 * @param  [type] $arr_campos_examen_respuestas [description]
 * @param  [type] $arr_maestro_tablas           [description]
 * @param  [type] $arr_campos_examen            [description]
 * @param  [type] $wcodigo_examen               [description]
 * @param  [type] $wbasedato_ayu                [description]
 * @param  [type] $wemp_pmla                    [description]
 * @param  [type] $conex                        [description]
 * @param  [type] $arbol                        [description]
 * @param  [type] $arbol_info                   [description]
 * @param  [type] &$divs_include                [description]
 * @param  string $modoImprimir                 [description]
 * @param  [type] &$pie_de_pagina               [Contiene el html de todos los campos tipos pie_de_pagina, para luego imprimirlo en columnas si es necesario]
 * @param  string $codmenu                      [description]
 * @param  string $sangria                      [description]
 * @param  string $padre                        [description]
 * @param  array  $propiedades                  [description]
 * @param  string $propiedad_ok                 [description]
 * @param  string $es_indicador                 [description]
 * @param  string $pro_especial                 [description]
 * @param  string $prop_especifica              [description]
 * @param  string $tabla_propiedades            [description]
 * @return [type]                               [description]
 */
function pintarMenuSeleccion($ckeditor_ok_ayucni, $arr_campos_examen_respuestas, $arr_maestro_tablas, $arr_campos_examen, $wcodigo_examen, $wbasedato_ayu, $wemp_pmla, $conex, $arbol, $arbol_info, &$divs_include, $modoImprimir = 'checks', &$pie_de_pagina, $codmenu = '', $sangria = '0', $padre = '', $propiedades = array(), $propiedad_ok = '', $es_indicador = '', $pro_especial = '', $prop_especifica = '', $tabla_propiedades = '')
{
    // echo "<pre>".print_r($arbol,true)."</pre>";
    $result = '';
    if($arbol == '' || (is_array($arbol) && count($arbol) == 0) )
    {
        return;
    }
    else
    {
        $si_propiedad = array();

        foreach($arbol["submenus"] as $wcod_campo => $value)
        {
            if($modoImprimir == 'tabs')
            {
                $key_info       = $wcod_campo.'_'.$value["id_campo"];
                $idx            = '';
                $codtab         = $wcod_campo;
                $buscame        = '';
                $indica_submenu = '';
                if($padre == '')
                {
                    $idx = $wcod_campo;
                }
                else{ $idx = $padre; }

                $funcionesJS = "";

                $tr = valoresGuardadosExamen($ckeditor_ok_ayucni, $wbasedato_ayu, $wemp_pmla, $conex, $arr_campos_examen, $arr_campos_examen_respuestas, '', $arr_maestro_tablas, $wcod_campo, $sangria);

                if($tr != '')
                {
                    $tr_dependiente = pintarMenuSeleccion($ckeditor_ok_ayucni, $arr_campos_examen_respuestas, $arr_maestro_tablas, $arr_campos_examen, $wcodigo_examen, $wbasedato_ayu, $wemp_pmla, $conex, $arbol["submenus"][$wcod_campo], $arbol_info, $divs_include, $modoImprimir, $pie_de_pagina, $wcod_campo, $sangria+20, $idx);
                    if(strtolower($arr_campos_examen[$wcod_campo]["tip_campo"]) == 'titulo')
                    {
                        if($tr_dependiente != '')
                        {
                            $result .= $tr.$tr_dependiente;
                        }
                    }
                    elseif(strtolower($arr_campos_examen[$wcod_campo]["tip_campo"]) == 'pie_de_pagina')
                    {
                        $pie_de_pagina[] = $tr.$tr_dependiente;
                    }
                    else
                    {
                        $result .= $tr.$tr_dependiente;
                    }
                }
            }
        }
    }
    return $result;
}

/**
 * [cargarEncabezadoPaciente: Consulta los datos principales del paciente para acceder a ellos desde javascript u otros llamados ajax sin tener que repetir la búsqueda de estos datos]
 * @param  [type] $conex            [description]
 * @param  [type] $wbasedato_ayu    [description]
 * @param  [type] $wbasedato_cliame [description]
 * @param  [type] $wbasedato_movhos [description]
 * @param  [type] $whistoria        [description]
 * @param  [type] $wingreso         [description]
 * @param  [type] &$data            [description]
 * @return [type]                   [description]
 */
function cargarEncabezadoPaciente($conex, $wemp_pmla, $codigoempresaparticular, $wbasedato_ayu, $wbasedato_cliame, $wbasedato_movhos, $whistoria, $wingreso, &$data)
{
    $data["flujo_consulta"] = 'cargar_paciente';
    $sql = "SELECT  c100.Pachis, c100.Pacact AS pac_activo, c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2, c100.Pacdoc, c100.Pactdo AS wtipo_documento,
                    c100.Pactel, c100.Pacdir, c100.Pacfna, c100.Pacsex,
                    c101.Ingcem, c101.Ingfei,
                    c101.Ingtpa AS tipo_Paciente, c101.Ingsei, c101.Ingnin, c101.Ingtar, c100.Pactam, c101.Ingcla, c24.Emptem, c24.Empnit, c24.Empnom, m18.Ubisac, c101.Ingtin, m18.Ubiald
            FROM    {$wbasedato_cliame}_000100 AS c100
                    INNER JOIN
                    {$wbasedato_cliame}_000101 AS c101 ON (c100.Pachis = c101.Inghis)
                    LEFT JOIN
                    {$wbasedato_movhos}_000018 AS m18 ON (m18.Ubihis = c101.Inghis AND m18.Ubiing = c101.Ingnin)
                    LEFT JOIN
                    {$wbasedato_cliame}_000024 AS c24 ON (c101.Ingcem = c24.Empcod)
            WHERE   c100.Pachis = '{$whistoria}'
                    AND c101.Ingnin = '{$wingreso}'";

    if($result = mysql_query($sql,$conex))
    {
        $data["sql"] = $sql;
        if(mysql_num_rows($result) > 0)
        {
            $row = mysql_fetch_assoc($result);

            // Si el tipo de paciente es particular (P), se especifíca este como responsable, en caso contrario se usa el código de responsable que trae la consulta.
            if($row["tipo_Paciente"] == 'P')
            {
                $row["Empnit"] = $codigoempresaparticular;
                $row["Empnom"] = 'Particular';
            }
			
			$estPaciente = "Inactivo";
			if($row["Ubiald"]=="off")
			{
				$estPaciente = "Activo";
			}

            $data["arr_encabezado"] = array("whistoria"  => $row["Pachis"],
                                            "wingreso"   => $row["Ingnin"],
                                            "wtipodoc"   => $row["wtipo_documento"],
                                            "wdocumento" => $row["Pacdoc"],
                                            "wnombre1"   => utf8_encode(limpiarString($row["Pacno1"])),
                                            "wnombre2"   => utf8_encode(limpiarString($row["Pacno2"])),
                                            "wapellido1" => utf8_encode(limpiarString($row["Pacap1"])),
                                            "wapellido2" => utf8_encode(limpiarString($row["Pacap2"])),
                                            "westadopac" => $row["pac_activo"],
                                            "wtelefono"  => utf8_encode(limpiarString($row["Pactel"])),
                                            "wdireccion" => utf8_encode(limpiarString($row["Pacdir"])),
                                            "wfechaNac"  => $row["Pacfna"],
                                            "wedad"      => calcularEdadPaciente($row["Pacfna"]),
                                            "wsexo"      => $row["Pacsex"],
                                            "wcodempresa"=> $row["Empnit"],
                                            "wnomempresa"=> utf8_encode($row["Empnom"]),
                                            "wempresa"   => $row["Empnit"].'-'.utf8_encode($row["Empnom"]),
                                            "westPac"	 => $estPaciente);
            $data["arr_encabezado_b64"] = $data["arr_encabezado"];
        }
    }
    else
    {
        $data["sql"] = $sql.' >> '.mysql_error();
        $data["error"] = 1;
        $data["mensaje"] = "Se presentó un inconveniente al momento de consultar los datos";
    }
}

/**
 * [buscarIngresosPorHistoria: Busca todos los ingresos para la historia que llega como parámetro, genera una tabla html con los posibles ingresos de esa historia
 *                             si es que encontró más de uno]
 * @param  [type] $conex            [description]
 * @param  [type] $wbasedato_ayu    [description]
 * @param  [type] $wbasedato_cliame [description]
 * @param  [type] $wbasedato_movhos [description]
 * @param  [type] $whistoria        [description]
 * @param  [type] &$data            [description]
 * @return [type]                   [description]
 */
function buscarIngresosPorHistoria($conex, $wemp_pmla, $codigoempresaparticular, $wbasedato_ayu, $wbasedato_cliame, $wbasedato_movhos, $whistoria, &$data)
{
    $sql = "SELECT  c100.Pachis, c100.Pacact AS pac_activo, c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2, c100.Pacdoc, c100.Pactdo AS wtipo_documento, c101.Ingcem, c101.Ingfei, c101.Inghin,
                    c101.Ingtpa AS tipo_Paciente, c101.Ingsei, c101.Ingnin, c101.Ingtar, c100.Pactam ,c101.Ingcla, c24.Emptem, c24.Empnit, c24.Empnom, m18.Ubisac, c101.Ingtin, m18.Ubiald
            FROM    {$wbasedato_cliame}_000100 AS c100
                    INNER JOIN
                    {$wbasedato_cliame}_000101 AS c101 ON (c100.Pachis = c101.Inghis)
                    LEFT JOIN
                    {$wbasedato_movhos}_000018 AS m18 ON (m18.Ubihis = c101.Inghis AND m18.Ubiing = c101.Ingnin)
                    LEFT JOIN
                    {$wbasedato_cliame}_000024 AS c24 ON (c101.Ingcem = c24.Empcod)
            WHERE   c100.Pachis = '{$whistoria}'
            ORDER BY c100.Pachis, convert(c101.Ingnin,unsigned) DESC";
    $data["flujo_consulta"] = 'cargar_ingresos';
    //------------------- GENERAR HTML PARA LAS OPCIONES DE ELECCIÓN
    if($result = mysql_query($sql,$conex))
    {
        $numrows = mysql_num_rows($result);
        if($numrows == 1)
        {
            $row = mysql_fetch_assoc($result);
            cargarEncabezadoPaciente($conex, $wemp_pmla, $codigoempresaparticular, $wbasedato_ayu, $wbasedato_cliame, $wbasedato_movhos, $whistoria, $row["Ingnin"], $data);
            $data["flujo_consulta"] = 'cargar_paciente';
        }
        elseif($numrows > 1)
        {
            $cont = 1;
            $html_ings = '';
            while($row = mysql_fetch_assoc($result))
            {
                $css       = ($cont % 2 == 0) ? "fila1": "fila2";
                $b_ingreso = $row["Ingnin"];
                $b_fecha   = utf8_encode($row["Ingfei"].' '.$row["Inghin"]);
                $b_activo  = ($row["Ubiald"] != 'on') ? "Activo": "Inactivo";
                $html_ings .= ' <tr class="'.$css.'">
                                    <td style="font-weight:bold;">'.$b_ingreso.'</td>
                                    <td style="text-align:left;">'.$b_fecha.'</td>
                                    <td style="text-align:center;">'.$b_activo.'</td>
                                    <td><input type="button" value="Ver" onclick="buscarHistoriaIngresoJS(\''.$whistoria.'\',\''.$b_ingreso.'\');"></td>
                                </tr>';
                $css++;
            }

            if($html_ings != '')
            {
                $data["html"] = '  <table align="center" cellspacing="1" cellpadding="0" border="0">
                                    <tr class="encabezadoTabla">
                                        <td>Ingreso</td>
                                        <td>Fecha</td>
                                        <td>Estado</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    '.$html_ings.'
                                </table>';
            }
        }
        else
        {
            $data["html"] = '<div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                                No hay resultados en la búsqueda
                            </div>';
        }
    }
    else
    {
        $data["sql"] = $sql.' >> '.mysql_error();
        $data["error"] = 1;
        $data["mensaje"] = "Problemas al consultar datos cargar_ingresos";
    }
}

/**
 * [consultarCamposPorEstudio: Según un código de examen que llega por parámetro, se busca la configuración de todos los campos que pertenecen a ese examen,
 *                             se retorna un arreglo con los códigos de los campos, descripción, tipo de campo y demás parámetros de configuración.]
 * @param  [type] $conex                    [description]
 * @param  [type] $wbasedato_ayu            [description]
 * @param  [type] $wbasedato_cliame         [description]
 * @param  [type] $wbasedato_movhos         [description]
 * @param  [type] $wcodigo_examen           [Código del examen al que se le buscará la configuración de cada campo]
 * @param  [type] &$data                    [description]
 * @param  [type] &$arr_campos_examen       [description]
 * @param  [type] &$arr_campos_examen_html  [description]
 * @param  [type] &$arr_campos_examenSelect [description]
 * @param  [type] &$arr_maestro_tablas      [En este array se guarda la tabla-opción que se necesita para que en los campos tipo selección se muestren todas sus opciones (options-html)]
 * @return [type]                           [description]
 */
function consultarCamposPorEstudio($conex, $wbasedato_ayu, $wbasedato_cliame, $wbasedato_movhos, $wcodigo_examen, &$data, &$arr_campos_examen, &$arr_campos_examen_html, &$arr_campos_examenSelect, &$arr_maestro_tablas)
{
    $existen_campos_configurados = true;
    $sqlCmps = "   SELECT   Dcfcca AS cod_campo, Dcfnom AS nom_campo, Dcftip AS tip_campo, Dcfctb AS maestro_tablas, Dcfreq AS campo_requerido,
                            Dcfvrf AS rang_unid, Dcfvdf AS valor_defecto, Dcffor AS formula, Dcfest AS estado_campo, Dcfcol AS sangria,
                            Dcfimp AS se_imprime, Dcfcon AS imprime_seguido, Dcfdep AS campo_depende
                    FROM    {$wbasedato_ayu}_000002 AS a2
                    WHERE   a2.Dcfenc = '{$wcodigo_examen}'
                            AND a2.Dcfest = 'on'
                    ORDER BY Dcford";
    if($result = mysql_query($sqlCmps,$conex))
    {
        if(mysql_num_rows($result) > 0)
        {
            while ($row = mysql_fetch_assoc($result))
            {
                $cod_campo      = $row["cod_campo"];
                $maestro_tablas = $row["maestro_tablas"];
                if(!array_key_exists($cod_campo, $arr_campos_examen))
                {
                    $arr_campos_examen[$cod_campo] = array( "cod_campo"       => $row["cod_campo"],
                                                            "nom_campo"       => utf8_encode(limpiarString($row["nom_campo"])),
                                                            "tip_campo"       => utf8_encode(limpiarString($row["tip_campo"])),
                                                            "maestro_tablas"  => $maestro_tablas,
                                                            "campo_requerido" => $row["campo_requerido"],
                                                            "rang_unid"       => utf8_encode(limpiarString($row["rang_unid"])),
                                                            "valor_defecto"   => utf8_encode(limpiarString($row["valor_defecto"])),
                                                            "formula"         => utf8_encode($row["formula"]),
                                                            "estado_campo"    => $row["estado_campo"],
                                                            "sangria"         => $row["sangria"],
                                                            "se_imprime"      => $row["se_imprime"],
                                                            "imprime_seguido" => $row["imprime_seguido"],
                                                            "campo_depende"   => $row["campo_depende"],
                                                            "valor_guardado"  => "",
                                                            );
                    $arr_campos_examen_html[$cod_campo] = array( "tip_campo"       => utf8_encode(limpiarString($row["tip_campo"])),
                                                            "campo_requerido" => $row["campo_requerido"],
                                                            "valor_guardado"  => "",
                                                            );
                }

                if(!array_key_exists($cod_campo, $arr_campos_examenSelect))
                {
                    $arr_campos_examenSelect[$cod_campo] = $cod_campo;
                }

                if($maestro_tablas != '' && !array_key_exists($maestro_tablas, $arr_maestro_tablas))
                {
                    $arr_maestro_tablas[$maestro_tablas] = array("nombre_tabla"=>"", "opciones_tabla"=>array(), "observaciones_opciones"=>array());
                }
            }
        }
        else
        {
            $existen_campos_configurados = false;
            $data["error"] = 1;
            $data["mensaje"] = "No fue posible consultar los campos del examen";
        }
    }
    else
    {
        $existen_campos_configurados = false;
        $data["error"] = 1;
        $data["mensaje"] = "No fue posible consultar parámetros adicionales del examen";
    }

    return $existen_campos_configurados;
}

/**
 * Se encarga de buscar el código de la empresa a la que pertenece el empleado, y al final retorna un código de empleado de 5 digitos pero concatenando al final el código de la empresa
 *
 * @param unknown $wemp_pmla
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param string $cod_use_emp : Código de sesión del usuario que está autenticado en el sistema, este código puede tener 5 o más digitos.
 * @return string (código del usuario en sus últimos 5 digitos con el código de la empresa concatenado al final xxxxx-xx)
 */
function empresaEmpleado($wemp_pmla, $conex, $wbasedato, $cod_use_emp)
{
    $use_emp = '+';

    $user_session = explode('-',$cod_use_emp);
    $user_session = (count($user_session) > 1) ? $user_session[1] : $user_session[0];

    $q = "  SELECT  Codigo, Empresa
            FROM    usuarios
            WHERE   codigo = '".$user_session."'
                    AND Activo = 'A'";
    $res = mysql_query($q,$conex);
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

        $use_emp = $user_session.'-'.$row['Empresa']; // concatena los últimos 5 digitos del código del usuario con el código de la empresa a la que pertenece.
    }
    return $use_emp;
}

/**
 * --> FUNCION QUE GENERA UN QUERY CON TODAS LA POSIBLES COMBINACIONES, BAJO LA MODALIDAD QUE PRIMERO
 *     SE BUSCA POR UN VALOR ESPECIFICO Y SI NO POR EL VALOR *
 *     Autor:                  Edward jaramillo, Jerson trujillo, felipe alvarez.
 *     Ultima Modificacion:    2014-07-21.
 *                             Los filtros "comodin_izq" "comodin_der" "operador" si son enviados en el array de variables serán tenidas en cuenta para
 *                             manipular el query por ejemplo cambiando el operador "=" (por defecto) por un "LIKE" por ejemplo y los comodines pueden
 *                             ser usados para indicar "%xx" a la izquierda o "xx%" a las derecha incluso ambos "%xx%"
 */
function generarQueryCombinado($variables, $tabla)
{
    global $conex;

    $selectQuery    = "SELECT id";
    $fromQuery      = "  FROM ".$tabla;
    $whereQuery     = " WHERE";
    $orderByQuery   = " ORDER BY";

    foreach($variables as $campo => $valores)
    {
        // --> SQL fijo en el query
        if(array_key_exists('SQL', $valores) && $valores['SQL'])
        {
            if($whereQuery == " WHERE")
                $whereQuery.= $valores['valor'];
            else
                $whereQuery.= "AND ".$valores['valor'];
        }
        else
        {
            $comodin_izq    = (array_key_exists("comodin_izq", $valores)) ? $valores['comodin_izq'] : '';
            $comodin_der    = (array_key_exists("comodin_der", $valores)) ? $valores['comodin_der'] : '';
            $operador       = (array_key_exists("operador", $valores) && !empty($valores['operador'])) ? $valores['operador'] : '=';

            // --> Agregar un OR al filtro con busqueda por valor *
            if($valores['combinar'])
            {
                // --> Armar el select del query
                $selectQuery.=  ", ".$campo;

                // --> Armar el where del query
                $whereQueryTemp =  " (".$campo." ".$operador." '".$comodin_izq.$valores['valor'].$comodin_der."' OR ".$campo." ".$operador." '".$comodin_izq."*".$comodin_der."')";

                if($whereQuery == " WHERE")
                    $whereQuery.= $whereQueryTemp;
                else
                    $whereQuery.= " AND ".$whereQueryTemp;

                // --> Armar el order by del query
                if($orderByQuery == " ORDER BY")
                    $orderByQuery.= ' '.$campo.' DESC';
                else
                    $orderByQuery.= ', '.$campo.' DESC';
            }
            else
            {
                // --> Armar el where del query
                $whereQueryTemp =  " ".$campo." ".$operador." '".$comodin_izq.$valores['valor'].$comodin_der."'";

                if($whereQuery == " WHERE")
                    $whereQuery.= $whereQueryTemp;
                else
                    $whereQuery.= " AND".$whereQueryTemp;
            }
        }
    }

    return $queryGeneral = $selectQuery.$fromQuery.$whereQuery.$orderByQuery;
}


function consultar_opciones_equipo($conex, $wemp_pmla, $wbasedato_ayu, $cod_equipo)
{
    $arr_equipos = array();
    $filtro_estado = ($cod_equipo != '') ? '': "AND Opcest = 'on'";
    $sql = "SELECT  Opccod, Opcdes
            FROM    {$wbasedato_ayu}_000005
            WHERE   Opctbl = 'EQUIPO' {$filtro_estado}";

    if($result = mysql_query($sql,$conex))
    {
        while($row = mysql_fetch_assoc($result))
        {
            if(!array_key_exists($row['Opccod'], $arr_equipos))
            {
                $arr_equipos[$row['Opccod']] = utf8_encode($row['Opcdes']);
            }
        }
    }
    else
    {
        // $data["sql"] = $sql.' >> '.mysql_error();
        // $data["error"] = 1;
        // $data["mensaje"] = "Se presentó un inconveniente al momento de consultar los datos";
    }
    return $arr_equipos;
}

/********************** FIN DE FUNCIONES *************************/

/**
 * ********************************************************************************************************************************************************
 * Lógica, procesos de los llamados AJAX de todo el programa - INICIO DEL PROGRAMA
 * ********************************************************************************************************************************************************
 */
if(isset($accion) && isset($form))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                default :
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                break;
            }
            echo json_encode($data);
            break;

        case 'update' :
            switch($form)
            {
                case 'guardar_datos_examen':
                        $arr_encabezado             = unserialize(base64_decode($arr_encabezado)); // Son los datos de paciente
                        $arr_encabezado_examen      = unserialize(base64_decode($arr_encabezado_examen)); // Datos de encabezado del exámen
                        $data["id_examen_paciente"] = $id_examen_paciente;
                        $data["wconsecutivo"]       = $wconsecutivo;
                        $data["spn_fecha_estudio"]  = "";

                        // Si no hay ID de registro entonces se debe insertar regitro nuevo
                        if($id_examen_paciente == "")
                        {
                            $campos_insert = array();
                            foreach ($arr_campos_examen as $wcod_campo => $info_campo)
                            {
                                if(!array_key_exists($wcod_campo, $campos_insert))
                                {
                                    $campos_insert[$wcod_campo] = utf8_decode($info_campo['valor_guardado']);
                                }
                            }

                            if(count($campos_insert) > 0)
                            {
                                // Consultar consecutivo CONSECUTIVO_EXAMEN, para incrementarlo y asignarlo al nuevo estudio
                                $sqlConsec = "  SELECT  Opcdes AS consecutivo
                                                FROM    {$wbasedato_ayu}_000005
                                                WHERE   Opctbl = 'CONSECUTIVO_EXAMEN'
                                                        AND Opccod = 'CONSECUTIVO_EXAMEN'";
                                if($resultCons = mysql_query($sqlConsec,$conex))
                                {
                                    if(mysql_num_rows($resultCons) > 0)
                                    {
                                        $rowCosec = mysql_fetch_assoc($resultCons);
                                        $consecutivo_examen = ($rowCosec['consecutivo']*1) + 1;

                                        $sqlUpdConsec = "   UPDATE  {$wbasedato_ayu}_000005
                                                                    SET Opcdes = '{$consecutivo_examen}'
                                                            WHERE   Opctbl = 'CONSECUTIVO_EXAMEN'
                                                                    AND Opccod = 'CONSECUTIVO_EXAMEN'";
                                        if($resUpdConsec = mysql_query($sqlUpdConsec,$conex))
                                        {
                                            $Espfce = ''; // Fecha cierra examen
                                            $Esphce = ''; // Hora cierra examen
                                            $Espuce = ''; // Usuario cierra examen
                                            $Esplog = ''; // Log
                                            if($westado_estudio == 'off')
                                            {
                                                $nombre_usuario = '';
                                                $sqlEncUs = "   SELECT  Descripcion
                                                                FROM    usuarios
                                                                WHERE   Codigo = '{$user_session}'";
                                                if($resultUs = mysql_query($sqlEncUs,$conex))
                                                {
                                                    $row = mysql_fetch_assoc($resultUs);
                                                    $nombre_usuario = $row["Descripcion"];
                                                }

                                                $Esplog = "|Cierra examen: {$fecha_actual} {$hora_actual} (usu:{$user_session}-{$nombre_usuario})";
                                                $Espfce = $fecha_actual;
                                                $Esphce = $hora_actual;
                                                $Espuce = $user_session;
                                            }

//5 de Marzo 2019 , Freddy Saenz , se modifica para que busque la fecha de ingreso del cargo
// y en caso de que no exista el cargo  use la fecha actual, como funcionaba anteriormente .
											$vfechaxestudio = $fecha_actual;
											$vhoraxestudio =  $hora_actual;
											$qcupsexam = "SELECT Enfcup FROM {$wbasedato_ayu}_000001 WHERE Enfcod = '{$wcodigo_examen}' ";

											$qUltCargoCups = "";

											if ($concepto == ""){//0700 concepto
												
											}elseif($ccosto == ""){//1250 o centro de costo
												//cliame_
											}elseif($resultCupsExam = mysql_query($qcupsexam,$conex)){
												$rowCups = mysql_fetch_array($resultCupsExam);
												$codigoCups = $rowCups["Enfcup"];
												if ($codigoCups != ""){
														$qUltCargoCups = " SELECT Tcarfec , Hora_data 
																			FROM {$wbasedato_cliame}_000106 
																			WHERE Tcarconcod = '{$concepto}'
																			  AND tcarprocod = '{$codigoCups}' 
																			  AND Tcarhis = '{$arr_encabezado['whistoria']}' 
																			  AND Tcaring = '{$arr_encabezado['wingreso']}'
																			  AND Tcarser = '{$ccosto}' 
																			ORDER BY Tcarfec desc ";
																			

																			
														if ($resultUltCargo = mysql_query($qUltCargoCups,$conex)){
															$rowUltCargo = mysql_fetch_array($resultUltCargo);	
															if ($rowUltCargo["Tcarfec"] == ""){
															}else{
																$vfechaxestudio = $rowUltCargo["Tcarfec"];
																$vhoraxestudio = $rowUltCargo["Hora_data"];
															}
														}
													
												}
												
											}

									
											
											
                                            $data["sqlUpdConsec"] = $sqlUpdConsec;
                                            // SE INSERTA EL ENCABEZADO DEL ESTUDIO PARA EL PACIENTE
                                            $sqlIns = " INSERT INTO {$wbasedato_ayu}_000006
                                                                    (Medico, Fecha_data, Hora_data, Espcod, Esphis, Esping, Esptdo, Espdoc,
                                                                    Espenf, Espudg, Espequ, Espesp,
                                                                    Esplog, Espfce, Esphce, Espuce, Espest, Seguridad )
                                                        VALUES
                                                                    ('{$wbasedato_ayu}', '{$vfechaxestudio}', '{$vhoraxestudio}', '{$consecutivo_examen}', '{$arr_encabezado['whistoria']}', '{$arr_encabezado['wingreso']}', '{$arr_encabezado['wtipodoc']}', '{$arr_encabezado['wdocumento']}',
                                                                    '{$wcodigo_examen}', '{$wubicacion_digital}', '{$wequipo}', '{$westado_estudio}',
                                                                    '{$Esplog}', '{$Espfce}', '{$Esphce}', '{$Espuce}', 'on', 'C-{$user_session}')";
                                            
//echo '<pre>'; print_r($sqlIns); echo '</pre><hr>';											
											if($resultEnPac = mysql_query($sqlIns,$conex))
                                            {
                                                $id_examen_paciente         = mysql_insert_id();
                                                $data["id_examen_paciente"] = $id_examen_paciente;
                                                $data["wconsecutivo"]       = $consecutivo_examen;
                                                $data["spn_fecha_estudio"]  = $fecha_actual.' '.$hora_actual;

                                                $into = implode(",", array_keys($campos_insert));
                                                $values = implode("','", $campos_insert);

                                                // SE INSERTA EL DETALLE PARA EL ESTUDIO DEL PACIENTE
                                                $sqlInsDll = "  INSERT INTO {$wbasedato_ayu}_{$arr_encabezado_examen['tabla']}
                                                                        (Medico, Fecha_data, Hora_data, Exahis, Exaing, Exacon,
                                                                        {$into},
                                                                        Exaest, Seguridad)
                                                                VALUES ('{$wbasedato_ayu}', '{$fecha_actual}', '{$hora_actual}', '{$arr_encabezado['whistoria']}', '{$arr_encabezado['wingreso']}', '{$consecutivo_examen}',
                                                                        '{$values}',
                                                                        'on', 'C-{$user_session}')";
                                                if($resultDllPac = mysql_query($sqlInsDll,$conex))
                                                {
                                                    $data["mensaje"] = "Datos del examen guardados correctamente";
                                                }
                                                else
                                                {
                                                    $data["error"] = 1;
                                                    $data["mensaje"] = "No fue posible guardar los datos ingresados en el examen para este paciente.";
                                                    $data["sqlInsDll"] = $sqlInsDll.' > '.mysql_error();
                                                }
                                            }
                                            else
                                            {
                                                $data["error"] = 1;
                                                $data["mensaje"] = "No fue posible guardar el encabezado del examen para este paciente.";
                                                $data["sqlIns"] = $sqlIns.' > '.mysql_error();
                                            }
                                        }
                                        else
                                        {
                                            $data["error"] = 1;
                                            $data["mensaje"] = "No se pudo generar consecutivo para este examen, no se pudo guardar.";
                                            $data["sql_consec"] = $sqlUpdConsec.' > '.mysql_error();
                                        }
                                        // $sqlUpd = "UPDATE {wbasedato_ayu}_{$arr_encabezado_examen['tabla']}";
                                    }
                                    else
                                    {
                                        $data["error"] = 1;
                                        $data["mensaje"] = "No se pudo encontrar el consecutivo [COD_ERR:02]";
                                        $data["sql_consec"] = $sqlConsec;
                                    }
                                }
                                else
                                {
                                    $data["error"] = 1;
                                    $data["mensaje"] = "No se pudo consultar el consecutivo de exámenes [COD_ERR:01]";
                                    $data["sql_consec"] = $sqlConsec.' > '.mysql_error();
                                }
                            }
                            else
                            {
                                $data["error"] = 1;
                                $data["mensaje"] = "El sistema no pudo leer los datos ingresados en el formulario del examen";
                                $data["campos_insert"] = $campos_insert;
                            }
                        }
                        else
                        { // Si hay ID de registro entonces se debe actualizar.
                            $setDatos = '';
                            if($westado_estudio == 'off')
                            {
                                $nombre_usuario = '';
                                $sqlEncUs = "   SELECT  Descripcion
                                                FROM    usuarios
                                                WHERE   Codigo = '{$user_session}'";
                                if($resultUs = mysql_query($sqlEncUs,$conex))
                                {
                                    $row = mysql_fetch_assoc($resultUs);
                                    $nombre_usuario = $row["Descripcion"];
                                }

                                $setDatos .= ", Espfce = '{$fecha_actual}'";
                                $setDatos .= ", Esphce = '{$hora_actual}'";
                                $setDatos .= ", Espuce = '{$user_session}'";
                                $setDatos .= ", Esplog = CONCAT(Esplog,'|','Cierra examen: {$fecha_actual} {$hora_actual} (usu:{$user_session}-{$nombre_usuario})')";
                            }

                            $wubicacion_digital = utf8_decode($wubicacion_digital);
                            $wequipo            = utf8_decode($wequipo);
                            $sqlUpdt = "UPDATE  {$wbasedato_ayu}_000006
                                        SET     Espudg = '{$wubicacion_digital}',
                                                Espequ = '{$wequipo}',
                                                Espesp = '{$westado_estudio}',
                                                Espusm = '{$user_session}',
                                                Espfmo = '{$fecha_actual}',
                                                Esphmo = '{$hora_actual}'
                                                {$setDatos}
                                        WHERE   id = '{$id_examen_paciente}'";
                            $data["sqlUpdt"] = $sqlUpdt;
                            if($resultUpdt = mysql_query($sqlUpdt,$conex))
                            {
                                //
                            }
                            else
                            {
                                $data["error"] = 1;
                                $data["mensaje"] = "No se pudo editar el encabezado del examen";
                                $data["sqlUpdt"] = $sqlUpdt.' > '.mysql_error();
                            }

                            if($data["error"] != 1)
                            {
                                $sqlConsec = "  SELECT  Espcod AS consecutivo
                                                FROM    {$wbasedato_ayu}_000006
                                                WHERE   id = '{$id_examen_paciente}'";
                                if($resultCons = mysql_query($sqlConsec,$conex))
                                {
                                    if(mysql_num_rows($resultCons) > 0)
                                    {
                                        // $rowCons = mysql_fetch_assoc($resultCons);
                                        // $consecutivo = $rowCons["consecutivo"];

                                        $campos_update = array();
                                        foreach ($arr_campos_examen as $wcod_campo => $info_campo)
                                        {
                                            if(!array_key_exists($wcod_campo, $campos_update))
                                            {
                                                $campos_update[$wcod_campo] = "{$wcod_campo} = '".utf8_decode($info_campo['valor_guardado'])."'";
                                            }
                                        }

                                        if(count($campos_update) > 0)
                                        {
                                            $campos_actualizables = implode(",", $campos_update);
                                            $sqlUpdtDll = " UPDATE {$wbasedato_ayu}_{$arr_encabezado_examen['tabla']}
                                                                    SET {$campos_actualizables},
                                                                        Exafmo = '{$fecha_actual}',
                                                                        Exahmo = '{$hora_actual}',
                                                                        Exausm = '{$user_session}'
                                                            WHERE Exacon = '{$wconsecutivo}'";
                                                                        // Exalog = ''
                                            if($resultCons = mysql_query($sqlUpdtDll,$conex))
                                            {
                                                $data["mensaje"] = "Datos del examen actualizados correctamente.";
                                            }
                                            else
                                            {
                                                $data["error"] = 1;
                                                $data["mensaje"] = "No se pudo encontrar el consecutivo para la actualización del examen para este paciente.";
                                                $data["sqlConsec"] = $sqlUpdtDll.' > '.mysql_error();
                                            }
                                        }
                                        else
                                        {
                                            $data["error"] = 1;
                                            $data["mensaje"] = "El sistema no pudo leer los datos ingresados en el formulario del examen que se deben actualizar";
                                            $data["campos_update"] = $campos_update;
                                        }
                                    }
                                    else
                                    {
                                        $data["error"] = 1;
                                        $data["mensaje"] = "No se pudo encontrar el consecutivo para la actualización del examen para este paciente.";
                                        $data["sqlConsec"] = $sqlConsec;
                                    }
                                }
                                else
                                {
                                    $data["error"] = 1;
                                    $data["mensaje"] = "No se pudo consultar el consecutivo para la actualización del examen para este paciente.";
                                    $data["sqlConsec"] = $resultCons.' > '.mysql_error();
                                }
                            }
                        }
                    break;

                case 'validar_medico':
                        // cod_medico Es el código de médico que se usa para cardiología, pero ese mismo código está homologado a un código de usuario matrix para poder buscar la clave en hce_20
                        $sql = "SELECT  ay15.Procmx, hce20.Usucla, hce20.Usufve, hce20.id
                                FROM    {$wbasedato_ayu}_000015 AS ay15
                                        INNER JOIN
                                        {$wbasedato_HCE}_000020 AS hce20 ON (hce20.Usucod = ay15.Procmx)
                                WHERE   ay15.Procod = '{$cod_medico}'
                                        AND hce20.Usufve >= '{$fecha_actual}'
                                        AND hce20.Usuest = 'on'";
                        $data["sqlfir"] = $sql;
                        if($result = mysql_query($sql,$conex))
                        {
                            if(mysql_num_rows($result) > 0)
                            {
                                $row = mysql_fetch_assoc($result);
                                if (sha1($firma_medico) === $row["Usucla"])
                                {
                                    $cod_mx = $row["Procmx"];
                                    $sqlUpdt = "UPDATE  {$wbasedato_ayu}_000006
                                                SET     Espesp = '{$westado_estudio}',
                                                        Espfir = '{$cod_mx}'
                                                WHERE   id = '{$id_examen_paciente}'";
                                    $data["sqlUpdt"] = $sqlUpdt;
                                    if($resultUpdt = mysql_query($sqlUpdt,$conex))
                                    {
                                        //
                                    }
                                    else
                                    {
                                        $data["error"] = 1;
                                        $data["mensaje"] = "No fue posible marcar el examen como terminado.";
                                        $data["sqlUpdtErr"] = $sqlUpdt.' > '.mysql_error();
                                    }
                                }
                                else
                                {
                                    $data["error"] = 2;
                                    $data["mensaje"] = "La clave digitada no es válida, está vencida o no tiene una clave creada para Historia Clínica.";
                                }
                            }
                            else
                            {
                                $data["error"] = 1;
                                $data["mensaje"] = "No tiene una clave creada para Historia Clínica.";
                            }
                        }
                        else
                        {
                            $data["error"] = 1;
                            $data["mensaje"] = "No se pudo consultar la validez de su clave.";
                            $data["sqlConsec"] = $sql.' > '.mysql_error();
                        }
                    break;

                case 'inactivar_examen':
                            $nombre_usuario = '';
                            $sqlEncUs = "   SELECT  Descripcion
                                            FROM    usuarios
                                            WHERE   Codigo = '{$user_session}'";
                            if($resultUs = mysql_query($sqlEncUs,$conex))
                            {
                                $row = mysql_fetch_assoc($resultUs);
                                $nombre_usuario = $row["Descripcion"];
                            }

                            $setDatos = "";
                            if($estado_reg_activo == 'off')
                            {
                                $setDatos = ", Esplog = CONCAT(Esplog,'|','Inactiva examen: {$fecha_actual} {$hora_actual} (usu:{$user_session}-{$nombre_usuario})')";
                            }
                            else
                            {
                                $setDatos = ", Esplog = CONCAT(Esplog,'|','Activa examen: {$fecha_actual} {$hora_actual} (usu:{$user_session}-{$nombre_usuario})')";
                            }

                            $sqlUpdt = "UPDATE  {$wbasedato_ayu}_000006
                                        SET     Espest = '{$estado_reg_activo}'
                                                {$setDatos}
                                        WHERE   id = '{$id_examen_paciente}'";
                            $data["sqlUpdt"] = $sqlUpdt;
                            if($resultUpdt = mysql_query($sqlUpdt,$conex))
                            {
                                //
                            }
                    break;

                default :
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'load' :
            switch($form)
            {
                // Tanto para texto_imprimir como para cosultar_detalle_examen, se deben consultar los campos del examen, las tablas de opciones, solo se diferencian en la
                // forma de pintar los campos, cosultar_detalle_examen muestra los campos para el formulario html del examen, texto_imprimir muestra un html pero para ser impreso
                // en un archivo pdf.
                case 'texto_imprimir':
                case 'cosultar_detalle_examen':
                        $arr_encabezado = unserialize(base64_decode($arr_encabezado));
                        // [1] >> Consultar configuración-encabezado del examen y tabla de respuestas
                        // [2] >> Consultar la configuración de los campos según el examen seleccionado
                        // [3] >> Consultar maestro de tablas asociados a los campos del examen y su listados de valores-opciones de tabla
                        // [4] >> Consultar maestro por defecto de campos normales
                        // [5] >> Consultar detalle respuestas examen paciente si existe un consecutivo y un ID de examen del paciente, con la ayuda del encabezado del examen
                        //          se consulta en la tabla de respuestas correspondiente al examen.
                        // [6] >> Crear la vista-campos html del examen, si hay detalle de respuestan entonces asignar el valor guardado de cada campo,
                        //          crear campo oculto para guardar consecutivo si existe, ID encabezado respuesta e ID de detalle de la respuesta.

                        $data["campos_normales"] = '';
                        $texto_cleditor          = (isset($texto_cleditor)) ? true: false;
                        $existe_tabla_respuestas = true;
                        $wtabla_respuesta_examen = "";
                        $wnombre_examen          = "";
                        $arr_campos_examen       = array();
                        $arr_campos_examenSelect = array();
                        $ubicacion_digital       = "";
                        $wequipo                 = "";
                        $westado_estudio         = "";
                        $estado_reg_activo       = "";
                        $data["wfecha_estudio"]  = '';
                        $wfecha_estudio          = '';
                        $ingreso_examen          = '';

                        // Este arreglo luego será usado para enviar por POST en jquery, tiene menos campos que arr_campos_examen, para evitar que por configuración del número de variables en php.ini
                        // se envíen datos incompletos.
                        $arr_campos_examen_html       = array();

                        $arr_encabezado_examen        = array("codigo"=>"", "tabla"=>"","description"=>"", "plantilla"=>"","estado"=>"");
                        $arr_maestro_tablas           = array();
                        $arr_obs_maestro_tablas       = array();
                        $arr_maestroDef_normales      = array();
                        $arr_campos_normales          = array();
                        $arr_campos_examen_respuestas = array(); // Según los campos configurados para el formulario, en este array se guardar los valores de respuesta asociados a ese mismo foramto en la tabla de respuestas.
                        $arr_funciones_autocompletar  = array(); // crearAutocomplete('arr_procedimientos', 'wprocedimiento','','',0);
                        $usuario_trancribe            = "";
                        $usuario_modifica             = "";
                        $profesional_firma            = "";

                        $msjErrTablaResp         = "En este momento no existe un formulario para almacenar datos del examen<br>Debe comunicarse con el administrador del programa para reportar este caso";
                        // [1] >> configuración-encabezado del examen
                            $sqlEncEx = "   SELECT  Enfcod, Enffor, Enfdes, Enfpll, Enfest
                                            FROM    {$wbasedato_ayu}_000001
                                            WHERE   Enfcod = '{$wcodigo_examen}'";
                            if($result = mysql_query($sqlEncEx,$conex))
                            {
                                $rowEnE                               = mysql_fetch_assoc($result);
                                $wtabla_respuesta_examen              = $rowEnE["Enffor"];
                                $wnombre_examen                       = utf8_encode(limpiarString($rowEnE["Enfdes"]));
                                $arr_encabezado_examen["codigo"]      = $rowEnE["Enfcod"];
                                $arr_encabezado_examen["tabla"]       = $wtabla_respuesta_examen;
                                $arr_encabezado_examen["description"] = $wnombre_examen;
                                $arr_encabezado_examen["plantilla"]   = $rowEnE["Enfpll"];
                                $arr_encabezado_examen["estado"]      = $rowEnE["Enfest"];

                                if($wtabla_respuesta_examen != '')
                                {
                                    if(existeTabla($conex, $wemp_pmla, $wbasedato_ayu, $wtabla_respuesta_examen))
                                    {} // Tabla de respuestas OK!
                                    else
                                    {
                                        $existe_tabla_respuestas = false;
                                        $data["error"] = 1;
                                        $data["mensaje"] = $msjErrTablaResp;
                                    }
                                }
                                else
                                {
                                    $existe_tabla_respuestas = false;
                                    $data["error"] = 1;
                                    $data["mensaje"] = $msjErrTablaResp;
                                }
                            }
                            else
                            {
                                $existe_tabla_respuestas = false;
                                $data["error"] = 1;
                                $data["mensaje"] = "No fue posible consultar parámetros adicionales del examen";
                            }
                        // << [1]

                        if($existe_tabla_respuestas)
                        {
							$vfecha_impresion_examen = '0000-00-00' ;//6 de marzo 2019 Freddy Saenz ,para que use la fecha del cargo al momento
							//de imprimir y no la fecha actual (hoy) con que se grababa antes el examen
                            $existen_campos_configurados = true;
                            // [2] >> configuración de los campos
                                $existen_campos_configurados = consultarCamposPorEstudio($conex, $wbasedato_ayu, $wbasedato_cliame, $wbasedato_movhos, $wcodigo_examen, $data, $arr_campos_examen, $arr_campos_examen_html, $arr_campos_examenSelect, $arr_maestro_tablas);
                            // << [2]

                            if($existen_campos_configurados)
                            {
                                // [3] >> maestro de tablas
                                    if(count($arr_maestro_tablas) > 0)
                                    {
                                        $cods_tablas = array_keys($arr_maestro_tablas);
                                        $tablas_usar = implode("','", $cods_tablas);
                                        $sqlOpcTabla = "SELECT  aTab.Tblcod AS cod_tabla, aTab.Tblnom AS nombre_tabla, aOpc.Opccod AS cod_opcion, aOpc.Opcdes AS nombre_opcion, aOpc.Opcobs AS observacion_add
                                                        FROM    {$wbasedato_ayu}_000004 AS aTab
                                                                INNER JOIN
                                                                {$wbasedato_ayu}_000005 AS aOpc ON ( aTab.Tblcod = aOpc.Opctbl)
                                                        WHERE   aTab.Tblcod IN ('{$tablas_usar}')
                                                        ORDER BY aTab.Tblcod, aOpc.Opcdes";
                                        $data["sql_Mtablas"] = $sqlOpcTabla;
                                        if($result = mysql_query($sqlOpcTabla,$conex))
                                        {
                                            while ($row = mysql_fetch_assoc($result))
                                            {
                                                if(array_key_exists($row["cod_tabla"], $arr_maestro_tablas))
                                                {
                                                    if($arr_maestro_tablas[$row["cod_tabla"]]["nombre_tabla"] == '')
                                                    {
                                                        $arr_maestro_tablas[$row["cod_tabla"]]["nombre_tabla"] = utf8_encode(limpiarString($row["nombre_tabla"]));
                                                    }

                                                    if(!array_key_exists($row["cod_opcion"], $arr_maestro_tablas[$row["cod_tabla"]]["opciones_tabla"]))
                                                    {
                                                        $nombre_opcion =  preg_replace("/[\n]+/", '', utf8_encode(limpiarString($row["nombre_opcion"])));
                                                        $arr_maestro_tablas[$row["cod_tabla"]]["opciones_tabla"][$row["cod_opcion"]] = $nombre_opcion;

                                                        if(!array_key_exists($row["cod_opcion"], $arr_maestro_tablas[$row["cod_tabla"]]["observaciones_opciones"]))
                                                        {
                                                            $arr_maestro_tablas[$row["cod_tabla"]]["observaciones_opciones"][$row["cod_opcion"]] = utf8_encode(limpiarString($row["observacion_add"]));
                                                        }
                                                    }
                                                }
                                            }
                                            // echo "<pre>".print_r($arr_maestro_tablas,true);
                                        }
                                    }
                                // << [3]

                                // [4] >> campos normales
                                    $sqlNor = " SELECT  Cnoenf AS wcodigo_examen, Cnocod AS codigo_reg, Cnotit AS titulo, Cnomed AS identificador_medico, Cnotra AS txt_transcrip, Cnotth AS txt_transcrip_html, Cnocca AS cod_campo_aplica
                                                FROM    {$wbasedato_ayu}_000003
                                                WHERE   Cnoenf = '{$wcodigo_examen}'
                                                        AND Cnoest = 'on'";
                                    if($result = mysql_query($sqlNor, $conex))
                                    {
                                        while ($row = mysql_fetch_assoc($result))
                                        {
                                            $cod_index = $row["cod_campo_aplica"];
                                            $cod_indexMed = $row["identificador_medico"];
                                            if(!array_key_exists($cod_index, $arr_campos_normales))
                                            {
                                                $arr_campos_normales[$cod_index] = array();
                                            }
                                            if(!array_key_exists($cod_index, $arr_campos_normales[$cod_index]))
                                            {
                                                $arr_campos_normales[$cod_index][$cod_indexMed] = array();
                                            }

                                            $txt_normales = ($ckeditor_ok_ayucni == 'on') ? utf8_encode($row["txt_transcrip_html"]) : utf8_encode($row["txt_transcrip"]);
                                            $arr_campos_normales[$cod_index][$cod_indexMed] = array("titulo"               => $row["titulo"],
                                                                                                    "identificador_medico" => $row["identificador_medico"],
                                                                                                    "txt_transcrip"        => $txt_normales,
                                                                                                    "cod_campo_aplica"     => $row["cod_campo_aplica"], // Código del campo configurado en 000002 en el que se puede seleccionar este campo normal
                                                                                                    );
                                        }
                                    }
                                    // $data["arr_campos_normales"] = $arr_campos_normales;
                                // << [4]

                                // [5] >> respuestas examen paciente
                                    if($id_examen_paciente != '' && ($id_examen_paciente*1) > 0)
                                    {
                                        // Consultar encabezado de estudio paciente
                                        $sqlRes = " SELECT  Espcod AS wconsecutivo, Espenf AS wcodigo_examen, Espesp AS westado_estudio, Espfmo AS fec_modif, Esphmo AS hor_modif, Espusm AS usu_modif, Espudg AS ubic_digital, Espequ AS wequipo,
                                                            Fecha_data, Hora_data, Espfir AS profesional_firma, Espest AS estado_registro_tabla, Espfce AS fecha_cierre, Esphce AS hora_cierre, Espuce AS usuario_cerro,
                                                            Esping AS ingreso_examen
                                                    FROM    {$wbasedato_ayu}_000006
                                                    WHERE   id = {$id_examen_paciente}";
                                        if($result = mysql_query($sqlRes, $conex))
                                        {
                                            if(mysql_num_rows($result) > 0)
                                            {
                                                $arr_enc_estudio_paciente = mysql_fetch_assoc($result);

                                                $ingreso_examen = $arr_enc_estudio_paciente['ingreso_examen'];
												
												$vfecha_impresion_examen = $arr_enc_estudio_paciente['Fecha_data'] . ' ' . $arr_enc_estudio_paciente['Hora_data'];//6 de marzo 2019 Freddy Saenz , modificacion para que use la fecha del cargo.
												
                                                $wfecha_estudio = ' <span style="font-size:8pt;">[' . $vfecha_impresion_examen . ']</span>';
                                               //$wfecha_estudio = ' <span style="font-size:8pt;">['.$arr_enc_estudio_paciente['Fecha_data'].' '.$arr_enc_estudio_paciente['Hora_data'].']</span>';


                                                $data["arr_enc_estudio_paciente"] = $arr_enc_estudio_paciente;
                                                $ubicacion_digital                = $arr_enc_estudio_paciente["ubic_digital"];
                                                $wequipo                          = $arr_enc_estudio_paciente["wequipo"];
                                                $westado_estudio                  = $arr_enc_estudio_paciente["westado_estudio"];
                                                $estado_reg_activo                = $arr_enc_estudio_paciente["estado_registro_tabla"];
                                                $usuario_trancribe                = $arr_enc_estudio_paciente["usuario_cerro"];

                                                if(!empty($arr_enc_estudio_paciente['profesional_firma']))
                                                {
                                                    $sqlNomUsu      = "SELECT Descripcion, Codigo FROM usuarios WHERE Codigo = '{$profesional_firma}'";
                                                    $resNomUsu      = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
                                                    if(mysql_num_rows($resNomUsu) > 0)
                                                    {
                                                        $row_nomfir = mysql_fetch_assoc($resNomUsu);

                                                        $profesional_firma = limpiarString(utf8_encode(ucwords(strtolower($row_nomfir['Descripcion']))));
                                                    }
                                                }
                                                // $data["arr_enc_estudio_paciente"] = array_keys($arr_campos_examen);

                                                $campos_select = implode(",", $arr_campos_examenSelect);
                                                if(count($campos_select) > 0)
                                                {
                                                    $sqlResDet = "  SELECT {$campos_select}, Fecha_data, Hora_data, Exafmo AS fecha_modificado, Exahmo AS hora_modificado, Exausm AS usuario_modifico, Seguridad
                                                                    FROM {$wbasedato_ayu}_{$wtabla_respuesta_examen}
                                                                    WHERE Exacon = '{$wconsecutivo}'";
                                                    if($resultDlle = mysql_query($sqlResDet, $conex))
                                                    {
                                                        if(mysql_num_rows($resultDlle) > 0)
                                                        {
                                                            $rowDetalle = mysql_fetch_assoc($resultDlle);
                                                            foreach ($rowDetalle as $wcod_campo_resp => $valor_resp)
                                                            {
                                                                if(!array_key_exists($wcod_campo_resp, $arr_campos_examen_respuestas))
                                                                {
                                                                    $arr_campos_examen_respuestas[$wcod_campo_resp] = utf8_encode($valor_resp);
                                                                }

                                                                if($wcod_campo_resp == 'usuario_modifico')
                                                                {
                                                                    $usuario_modifica = (!empty($valor_resp)) ? $valor_resp: '';
                                                                }
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $data["error"] = 1;
                                                            $data["mensaje"] = "[5] No se encontró detalle del examen que está intentando consultar";
                                                            $data["sqlResDet"] = PHP_EOL." Detalle examen del paciente: ".$sqlResDet;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $data["error"] = 1;
                                                        $data["mensaje"] = "[4] No se encontró el estudio que está intentando consultar";
                                                        $data["sqlResDet"] = PHP_EOL." Detalle examen del paciente: ".$sqlResDet." > ".mysql_error();
                                                    }
                                                }
                                                else
                                                {
                                                    $data["error"] = 1;
                                                    $data["mensaje"] = "[3] No se encontró el estudio que está intentando consultar";
                                                    $data["campos_select"] = PHP_EOL." Examen del paciente: ".$campos_select;
                                                }
                                            }
                                            else
                                            {
                                                $data["error"] = 1;
                                                $data["mensaje"] = "[2] No se encontró el estudio que está intentando consultar";
                                                $data["sqlRes"] = PHP_EOL." Exámenes del paciente: ".$sqlRes;
                                            }
                                        }
                                        else
                                        {
                                            $data["error"] = 1;
                                            $data["mensaje"] = "[1] No se encontró el estudio que está intentando consultar";
                                            $data["sqlRes"] = PHP_EOL." Exámenes del paciente: ".$sqlRes.' > '.mysql_error();
                                        }
                                    }
                                // << [5]

                                $arr_opciones_equipo = consultar_opciones_equipo($conex, $wemp_pmla, $wbasedato_ayu, $wequipo);

                                // >> IMPRIMIR CAMPOS HTML PARA FORMULARIO
                                // >> IMPRIMIR TEXTO RESULTADO PARA CLEDITOR
                                if(!$texto_cleditor)
                                {
                                    // [6] >> vista-campos html
                                        $html_campos = "";
                                        $html_campos_normales = array();
                                        // Reorganizar el array de campos para que unos campos puedan depender de otros
                                        // $reog_arr_campos_examen = array();
                                        // $arr_campos_pendientes = array();
                                        //[6.1] verificar de quién depende

                                        $info_cke = '<table>
                                                            <tr>
                                                            <td style=\'border-bottom:1px solid;\'>Maximizar</td><td style=\'border-bottom:1px solid;\'>[Ctrl]+[Shift]+M</td>
                                                            </tr>
                                                            <tr>
                                                            <td style=\'border-bottom:1px solid;\'>Seleccionar todo</td><td style=\'border-bottom:1px solid;\'>[Ctrl]+A</td>
                                                            </tr>
                                                    </table>';
                                        $img_ayuda = '  <img width="15" height="15" src="../../images/medical/root/info.png" title="'.utf8_encode($info_cke).'" class="tooltip" />';

                                        if(count($arr_campos_examen) > 0)
                                        {
                                            $count_filas = 0;
                                            foreach ($arr_campos_examen as $wcod_campo => $arr_config)
                                            {
                                                $count_filas++;
                                                $tipo_dato = $arr_config["tip_campo"];

                                                $css                = '';//($count_filas % 2 == 0) ? 'fila1': 'fila2';
                                                $cssLabel           = 'font_label';
                                                $spnLabelCampo      = ($arr_config["nom_campo"] != '') ? ':' : '';//'<span style="font-size:7pt;">('.$wcod_campo.')</span>';
                                                $campo_requeridocss = ($arr_config["campo_requerido"]=='on') ? "requerido": "";
                                                $campo_reqspn       = ($arr_config["campo_requerido"]=='on') ? '<span style="color:red;font-size:11pt;">*</span>': "";
                                                $campo_margenIzr    = ($arr_config["campo_requerido"]!='on') ? 'margin-left:10px;': "margin-left:0px;";
                                                $rang_unid          = ($arr_config["rang_unid"] != '') ? ' '.($arr_config["rang_unid"]) : '';
                                                $sangria            = (($arr_config["sangria"]*1) > 1) ? "margin-left:".($arr_config["sangria"]*10)."px;" : '';
                                                $vlr_default        = ($arr_config["valor_defecto"] != '') ? ($arr_config["valor_defecto"]) : '';
                                                $campo_depende      = ($arr_config["campo_depende"] != '') ? ($arr_config["campo_depende"]) : '';

                                                $td_consec_campo    = '<td>'.$count_filas.'</td>';

                                                switch (strtolower($tipo_dato)) {
                                                    case 'archivo':
                                                    case 'encabezado':
                                                    case 'pie_de_pagina':
                                                            // select
                                                            $cmp_resultado = (array_key_exists($wcod_campo, $arr_campos_examen_respuestas)) ? $arr_campos_examen_respuestas[$wcod_campo]: "";

                                                            $wcod_campo = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $wcod_campo): $wcod_campo;
                                                            $select = "";
                                                            $option = '<option value="">Seleccione..</option>';
                                                            $arr_opciones_autocompletar = array();

                                                            if($arr_config["maestro_tablas"] != '' && array_key_exists($arr_config["maestro_tablas"], $arr_maestro_tablas))
                                                            {
                                                                $arr_opciones_autocompletar = $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"];
                                                                $cod_defecto = $cmp_resultado;
                                                                $nom_defecto = (array_key_exists($cod_defecto, $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"])) ? ($cod_defecto.'-'.$arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"][$cod_defecto]) : '';

                                                                $limite_busca = 0;
                                                                $busqueda_ajax = 0;
                                                                $tabla_opc_ajax = "";
                                                                $cantidad_opcs = count($arr_opciones_autocompletar);
                                                                if($cantidad_opcs > 100) { $limite_busca  = 2; }
                                                                if($cantidad_opcs > 500) { $limite_busca  = 3; }
                                                                if($cantidad_opcs > 1000) {
                                                                    $limite_busca = 4;
                                                                    $busqueda_ajax = 1;
                                                                    $tabla_opc_ajax = $arr_config["maestro_tablas"];
                                                                }

                                                                $arr_funciones_autocompletar[] = 'crearAutocomplete(\'arr_'.$wcod_campo.'\',\''.$wcod_campo.'\',\''.$cod_defecto.'\',\''.$nom_defecto.'\','.$limite_busca.', \''.$busqueda_ajax.'\',\''.$tabla_opc_ajax.'\')';
                                                            }

                                                            $select = '<input name="'.$wcod_campo.'" id="'.$wcod_campo.'" tipo_dato="'.$tipo_dato.'" cod_tabla="'.$arr_config["maestro_tablas"].'" class="ui-widget-content '.$campo_requeridocss.' inputs examen_autocomplete" placeholder="Buscar" onfocus="classOnFocus(this);" codigo="'.$cod_defecto.'" nombre="'.$nom_defecto.'" style="'.$campo_margenIzr.'" value="'.$nom_defecto.'">';
                                                            if($busqueda_ajax == 0){
                                                                $select .= ' <input type="hidden" name="arr_'.$wcod_campo.'" id="arr_'.$wcod_campo.'" value=\''.json_encode($arr_opciones_autocompletar).'\'>';
                                                            }

                                                            $html_campos .= '<tr class="css_'.$campo_depende.'" >
                                                                                '.$td_consec_campo.'
                                                                                <td class="fila1 '.$cssLabel.'"><span style="font-weight:bold;">'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.'</td>
                                                                                <td class="fila2">'.$campo_reqspn.$select.$rang_unid.'</td>
                                                                            </tr>';
                                                        break;

                                                    case 'boleano':
                                                            // checkbox
                                                            $cmp_resultado = (array_key_exists($wcod_campo, $arr_campos_examen_respuestas)) ? $arr_campos_examen_respuestas[$wcod_campo]: "";
                                                            $wcod_campo    = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $wcod_campo): $wcod_campo;
                                                            $checked       = ($cmp_resultado == 'on') ? 'checked="checked"': '';

                                                            $checkbox = '
                                                                        <input name="'.$wcod_campo.'" id="'.$wcod_campo.'" tipo_dato="'.$tipo_dato.'" type="checkbox" value="'.$cmp_resultado.'" '.$checked.' class="'.$campo_requeridocss.' inputs" >';
                                                            $html_campos .= '<tr class="css_'.$campo_depende.'" >
                                                                                '.$td_consec_campo.'
                                                                                <td class="fila1 '.$cssLabel.'"><span style="font-weight:bold;">'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.'</td>
                                                                                <td class="fila2">'.$campo_reqspn.$checkbox.'</td>
                                                                            </tr>';
                                                        break;

                                                    case 'conjunto':
                                                            // fielset
                                                            // $html_campos .= '<hr>'
                                                            //                 .'<span style="font-weight:bold;">'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.': </br>';
                                                        break;

                                                    case 'formula':
                                                            // eval > span attr-val-formula
                                                            $wcod_campo    = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $wcod_campo): $wcod_campo;
                                                            $formula_tlt   = $arr_config["formula"];
                                                            $formula_tlt   = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $formula_tlt): $formula_tlt;
                                                            $formula_title = $formula_tlt;
                                                            $spn_cod_campo = ' <span style="color:#a0a0a0;display:none;font-size:8pt;" class="spn_fomula_campo spn_fomula_campo_'.$wcod_campo.'">'.$wcod_campo.'</span>';

                                                            $sujeto                 = $formula_tlt;
                                                            $patron                 = ($ver_en_modal != '') ? '/'.$ver_en_modal.'([a-z0-9\_]*)/i' : '/Exa([a-z0-9\_]*)/i';
                                                            $campos_formula_exp_reg = array();
                                                            $coincidencias          = preg_match_all($patron, $sujeto, $campos_formula_exp_reg, PREG_PATTERN_ORDER);
                                                            $campos_formula = array();
                                                            if(count($campos_formula_exp_reg) > 0)
                                                            {
                                                                if(count($campos_formula_exp_reg[0]) > 0)
                                                                {
                                                                    foreach ($campos_formula_exp_reg[0] as $key_frm => $campo_en_formula)
                                                                    {
                                                                        if(!in_array($campo_en_formula, $campos_formula))
                                                                        {
                                                                            $campos_formula[] = $campo_en_formula;
                                                                        }
                                                                    }

                                                                    foreach ($campos_formula as $key_frm => $campo_en_formula)
                                                                    {
                                                                        // echo "<pre>campos_formula: ".print_r($campo_en_formula,true)."</pre>";
                                                                        $formula_title = str_replace($campo_en_formula, '<span class="spn_fomula_campo_over" cod_campoFormula="'.$campo_en_formula.'">'.$campo_en_formula.'</span>', $formula_title); //spn_fomula_campo spn_fomula_campo_'.$campo_en_formula.'
                                                                        // echo "<pre>campos_formula: ".print_r($formula_title,true)."</pre>".PHP_EOL;
                                                                    }
                                                                }
                                                            }

                                                            $spn = '
                                                                    <div id="'.$wcod_campo.'" esformula="on" formula="'.$formula_tlt.'" formula_parcial="" style="font-weight:bold;" class="formula partea" valor_calculado="" ></div>
                                                                    <div class="rang_unid_formula parteb">
                                                                        '.$rang_unid.'
                                                                        <span id="spn_fomula_calc_'.$wcod_campo.'" style="color:#cbcbcb;display:none;" class="spn_fomula_calc"></span>
                                                                        <span id="spn_fomula_campo_'.$wcod_campo.'" title="Doble click sobre la formula para resaltar los campos" style="color:#a0a0a0;display:none;cursor:pointer;" class="tooltip spn_fomula_campo" onmouseover="formulaOver(\'spn_fomula_campo_'.$wcod_campo.'\');" onmouseout="formulaOut(\'spn_fomula_campo_'.$wcod_campo.'\');" ondblclick="resaltarCamposFormula(\'spn_fomula_campo_'.$wcod_campo.'\');" >'.$formula_title.'</span>
                                                                    </div>';
                                                            $html_campos .= '<tr class="css_'.$campo_depende.' '.$css.'" >
                                                                                '.$td_consec_campo.'
                                                                                <td class="fila1 '.$cssLabel.' tooltip" title="'.$formula_tlt.'" ><span style="font-weight:bold;">'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.$spn_cod_campo.'</td>
                                                                                <td class="fila2 td_formula">'.$campo_reqspn.$spn.'</td>
                                                                            </tr>';
                                                        break;

                                                    case 'memo':
                                                            // textarea
                                                            $cmp_resultado = (array_key_exists($wcod_campo, $arr_campos_examen_respuestas)) ? $arr_campos_examen_respuestas[$wcod_campo]: "";
                                                            $wcod_campo    = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $wcod_campo): $wcod_campo;
                                                            $cmp_resultado = ($id_examen_paciente == '' && $cmp_resultado == '') ? $vlr_default : $cmp_resultado;
                                                            $txt           = ($cmp_resultado != '') ? $cmp_resultado: "";
                                                            $txt = '
                                                                    <textarea name="'.$wcod_campo.'" id="'.$wcod_campo.'" tipo_dato="'.$tipo_dato.'" style="" class="'.$campo_requeridocss.' css_memo" onfocus="classOnFocus(this);">'.$txt.'</textarea>';
                                                            $html_campos .= '<tr class="css_'.$campo_depende.'" >
                                                                                '.$td_consec_campo.'
                                                                                <td class="fila1 '.$cssLabel.'"><span style="font-weight:bold;">'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.'</td>
                                                                                <td class="fila2">'.$campo_reqspn.$txt.'</td>
                                                                            </tr>';
                                                        break;

                                                    case 'multiseleccion':
                                                            // select > div_multiselect
                                                            $cmp_resultado              = (array_key_exists($wcod_campo, $arr_campos_examen_respuestas)) ? $arr_campos_examen_respuestas[$wcod_campo]: "";
                                                            $cmp_resultado              = ($cmp_resultado != '') ? explode(",", $cmp_resultado) : array();
                                                            $wcod_campo                 = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $wcod_campo): $wcod_campo;
                                                            $arr_opciones_autocompletar = array();
                                                            $select                     = "";
                                                            $busqueda_ajax = 0;
                                                            $tabla_opc_ajax = "";

                                                            if($arr_config["maestro_tablas"] != '' && array_key_exists($arr_config["maestro_tablas"], $arr_maestro_tablas))
                                                            {
                                                                $arr_opciones_autocompletar = $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"];
                                                                $cod_defecto                = '';
                                                                $nom_defecto                = '';
                                                                $limite_busca               = 0;
                                                                $cantidad_opcs              = count($arr_opciones_autocompletar);


                                                                if($cantidad_opcs > 100) { $limite_busca  = 2; }
                                                                if($cantidad_opcs > 500) { $limite_busca  = 3; }
                                                                if($cantidad_opcs > 1000) {
                                                                    $limite_busca = 4;
                                                                    $busqueda_ajax = 1;
                                                                    $tabla_opc_ajax = $arr_config["maestro_tablas"];
                                                                }
                                                                $arr_funciones_autocompletar[] = 'crearAutocomplete(\'arr_'.$wcod_campo.'\',\'dep_'.$wcod_campo.'\',\''.$cod_defecto.'\',\''.$nom_defecto.'\','.$limite_busca.', \''.$busqueda_ajax.'\',\''.$tabla_opc_ajax.'\')';
                                                            }

                                                            $select = '<input name="dep_'.$wcod_campo.'" id="dep_'.$wcod_campo.'" wcod_campo="'.$wcod_campo.'" tipo_dato="'.$tipo_dato.'" class="ui-widget-content '.$campo_requeridocss.' inputs examen_autocomplete" placeholder="Buscar" onfocus="classOnFocus(this);" codigo="" nombre="" style="'.$campo_margenIzr.'" value="" >';
                                                            if($busqueda_ajax == 0){
                                                                $select .= ' <input type="hidden" name="arr_'.$wcod_campo.'" id="arr_'.$wcod_campo.'" value=\''.json_encode($arr_opciones_autocompletar).'\'>';
                                                            }

                                                            $optsDiv = "";
                                                            if(count($cmp_resultado) > 0)
                                                            {
                                                                foreach ($cmp_resultado as $key_idx => $value_res)
                                                                {
                                                                    $res_cod = $value_res;
                                                                    $res_nom = "";
                                                                    if(array_key_exists($arr_config["maestro_tablas"], $arr_maestro_tablas) && array_key_exists($value_res, $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"]))
                                                                    {
                                                                        $res_nom = $arr_maestro_tablas[$arr_config["maestro_tablas"]]["opciones_tabla"][$value_res];
                                                                    }
                                                                    $imgeliminar = '<img class="btn_eliminar_opc" style="cursor:pointer;" src="../../images/medical/eliminar1.png" alt="Eliminar" onclick="eliminarOpcionCampo(\''.$wcod_campo.'_'.$value_res.'\');" />';
                                                                    $optsDiv .= '<div id="'.$wcod_campo.'_'.$value_res.'" codigo="'.$value_res.'" onmouseover="trOver(this);" onmouseout="trOut(this);" >'.$imgeliminar.' '.$res_cod.'-'.$res_nom.'</div>';
                                                                }
                                                            }

                                                            $divdep = '<div id="'.$wcod_campo.'" class="'.$campo_requeridocss.'" >'.$optsDiv.'</div>';
                                                            $html_campos .= '<tr class="css_'.$campo_depende.'" >
                                                                                '.$td_consec_campo.'
                                                                                <td class="fila1 '.$cssLabel.'"><span style="font-weight:bold;">'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.'</td>
                                                                                <td class="fila2">'.$campo_reqspn.$select.$divdep.$rang_unid.'</td>
                                                                            </tr>';
                                                        break;

                                                    case 'numero':
                                                            // input-text decimal
                                                            $cmp_resultado = (array_key_exists($wcod_campo, $arr_campos_examen_respuestas)) ? $arr_campos_examen_respuestas[$wcod_campo]: "";
                                                            $cmp_resultado = ($cmp_resultado != '') ? $cmp_resultado: "";
                                                            $wcod_campo    = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $wcod_campo): $wcod_campo;
                                                            $spn_cod_campo = ' <span style="color:#a0a0a0;display:none;font-size:8pt;" class="spn_fomula_campo spn_fomula_campo_'.$wcod_campo.'">'.$wcod_campo.'</span>';

                                                            // if($ckeditor_ok_ayucni != 'on')
                                                            {
                                                                $cmp_resultado = preg_replace("/[\n]+/", '<br>', $cmp_resultado);
                                                            }

                                                            $input = '<input type="text" name="'.$wcod_campo.'" id="'.$wcod_campo.'" tipo_dato="'.$tipo_dato.'" class="number-only '.$campo_requeridocss.' inputs" onfocus="classOnFocus(this);" onkeyup="if(!campoSiguiente(this, event)){ recalcularFormulas(this); }" value="'.$cmp_resultado.'" placeholder="#">';
                                                            $html_campos .= '<tr class="css_'.$campo_depende.' '.$css.'" >
                                                                                '.$td_consec_campo.'
                                                                                <td class="fila1 '.$cssLabel.'"><span style="font-weight:bold;">'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.$spn_cod_campo.'</td>
                                                                                <td class="fila2">'.$campo_reqspn.$input.$rang_unid.'</td>
                                                                            </tr>';
                                                        break;

                                                    case 'texto':
                                                            // Salto línea
                                                            $css = '';
                                                            $html_campos .= '<tr class="css_'.$campo_depende.' '.$css.'" >
                                                                                '.$td_consec_campo.'
                                                                                <td colspan="2">&nbsp;</td>
                                                                            </tr>';
                                                        break;

                                                    case 'titulo':
                                                            // html > h3
                                                            $wcod_campo = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $wcod_campo): $wcod_campo;
                                                            $css = '';
                                                            $txt = '<h4 style="'.$sangria.'" name="'.$wcod_campo.'" id="'.$wcod_campo.'" tipo_dato="'.$tipo_dato.'">'.($arr_config["nom_campo"]).'</h4>';
                                                            $html_campos .= '<tr class="toggle_tr css_'.$campo_depende.' '.$css.'" css_toggle="'.$wcod_campo.'" onmouseover="trOver(this);" onmouseout="trOut(this);">
                                                                                '.$td_consec_campo.'
                                                                                <td colspan="2">'.$txt.'</td>
                                                                            </tr>';
                                                        break;

                                                    case 'normal':
                                                            // textarea
                                                            // CONSULTAR LOS CAMPOS NORMALES QUE SE PUEDE SELECCIONAR POR DEFECTO
                                                            // $arr_campos_normales[$cod_index] = array("titulo"               => $row["titulo"],
                                                            //                                     "identificador_medico" => $row["identificador_medico"],
                                                            //                                     "txt_transcrip"        => utf8_encode($row["txt_transcrip"]),
                                                            //                                     "cod_campo_aplica"     => $row["cod_campo_aplica"], // Código del campo configurado en 000002 en el que se puede seleccionar este campo normal
                                                            //                                     );

                                                            $opciones_transcripcion = '';
                                                            $wcod_campo_html = ($ver_en_modal != '') ? str_replace("Exa", $ver_en_modal, $wcod_campo): $wcod_campo;
                                                            if(array_key_exists($wcod_campo, $arr_campos_normales))
                                                            {
                                                                if(array_key_exists('*', $arr_campos_normales[$wcod_campo]))
                                                                {
                                                                    // Para mostrar el boton de normal general
                                                                    $opciones_transcripcion = '
                                                                            <input type="button" class="inputs" id="btn_opciones_transcr_gral_'.$wcod_campo_html.'" value="Normal General" onclick="verOpcionesTranscripcion(\''.$wcod_campo.'\',\''.$wcodigo_examen.'\',\'*\');">';
                                                                    unset($arr_campos_normales[$wcod_campo]['*']);
                                                                }

                                                                if(count($arr_campos_normales[$wcod_campo]) > 0)
                                                                {
                                                                    // Para mostrar el botón de normal por médico
                                                                    $opciones_transcripcion .= '
                                                                            <input type="button" class="inputs" id="btn_opciones_transcr_person_'.$wcod_campo_html.'" value="Normal médico" onclick="verOpcionesTranscripcion(\''.$wcod_campo.'\',\''.$wcodigo_examen.'\',\'\');">';
                                                                }
                                                            }

                                                            if($opciones_transcripcion != '')
                                                            {
                                                                $opciones_transcripcion .= '
                                                                            <input type="button" class="inputs" id="btn_opciones_transcr_borrar_'.$wcod_campo_html.'" value="Borrar texto" onclick="borrarTextoNormal(\''.$wcod_campo.'\');">';
                                                            }

                                                            $cmp_resultado = (array_key_exists($wcod_campo, $arr_campos_examen_respuestas)) ? $arr_campos_examen_respuestas[$wcod_campo]: "";
                                                            $txt           = ($cmp_resultado != '') ? $cmp_resultado: "";
                                                            $txt = '<br>
                                                                    <textarea name="'.$wcod_campo_html.'" id="'.$wcod_campo_html.'" tipo_dato="'.$tipo_dato.'" style="" class="'.$campo_requeridocss.' css_memo" onfocus="classOnFocus(this);" >'.$txt.'</textarea>';

                                                            // if($ckeditor_ok_ayucni != 'on')
                                                            {
                                                                $txt = preg_replace("/[\n]+/", '<br>', $txt);
                                                            }

                                                            $html_campos_normales[] = '
                                                                            <tr class="css_'.$campo_depende.'" >
                                                                                '.$td_consec_campo.'
                                                                                <td class="fila2" colspan="2" >'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.' '.$campo_reqspn.$opciones_transcripcion.$img_ayuda.$txt.'</td>
                                                                            </tr>';
                                                                                // <td class="fila1 '.$cssLabel.'"><span style="font-weight:bold;">'.($arr_config["nom_campo"]).'</span> '.$spnLabelCampo.'</td>
                                                        break;

                                                    default:
                                                        break;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            // Ya esta capturando error desde la consulta si no se genera la consulta o si la consulta no arroja resultados.
                                        }

                                        // El arr_campos_examen es utilizado para luego recorrer todos los campos en javascript al momento de guardar
                                        // para verificar qué campos guardar, los tipos de campo para saber como capturar la información.

                                        $btns_examen = '<div class="encabezadoTabla" style="text-align:center;">
                                                            <input type="button" class="btn_guardar_examen" value="Guardar parcialmente">
                                                            <input type="button" class="btn_cerrar_examen" value="Finalizar examen">
                                                            <input type="button" class="btn_inactivar_examen" value="Inactivar" style="display:none;">
                                                            <input type="button" class="btn_activar_examen" value="Activar" style="display:none;">
                                                            <input type="button" class="btn_imprimir_examen" value="Imprimir">
                                                        </div>';
                                        $convenciones_examen = '
                                                                    <span style="color:red;font-size:8pt;">(*)</span> <span style="font-size:8pt;">Campos requeridos</span> |
                                                                    <span style="color:green;font-size:8pt;">Avanzar un campo</span> <span style="font-size:8pt;">[Enter], [Av pág.]</span> |
                                                                    <span style="color:blue;font-size:8pt;">Retroceder un campo</span> <span style="font-size:8pt;">[Re pág.]</span> |
                                                                    <span style="font-size:8pt;">Tamaño texto</span>
                                                                    <img style="cursor:pointer;" title="Disminuir" id="txt_menos" width="15" height="15" src="../../images/medical/root/menos.png" alt="Menos" />
                                                                    <img style="cursor:pointer;" title="Aumentar" id="txt_mas" width="15" height="15" src="../../images/medical/root/mas.png" alt="Mas" /> |
                                                                    <span style="font-size:8pt;">Valores Formula: </span><input type="checkbox" id="ver_formula_calc'.$ver_en_modal.'" onclick="verFormulaCalculada(this);"> |
                                                                    <span style="font-size:8pt;">Campos Formula: </span><input type="checkbox" id="ver_formula_campos'.$ver_en_modal.'" onclick="verFormulaCampos(this);"> ';
                                        if($ver_en_modal == '' && count($html_campos_normales) > 0)
                                        {
                                            $data["html_campos_normales"] = '<table align="center" cellspacing="1" cellpadding="0" border="0" width="100%">
                                                                                '.implode("", $html_campos_normales).'
                                                                            </table>';
                                        }
                                        elseif($ver_en_modal != '')
                                        {
                                            // Si se debe mostrar en una modal entonces muestre los campos normales seguido a los demás campos y no en un div flotante
                                            // pues el examen solo se está en modo lectura y se muestra sobre un examen que se está diligenciando y no se ha guardado.
                                            $html_campos .= implode("", $html_campos_normales);
                                            $btns_examen = '';
                                            $convenciones_examen = '';
                                        }

                                        // $campo_firma = '';
                                        // if(empty($profesional_firma))
                                        // {
                                        //     $campo_firma = '<input type="text">';
                                        // }
                                        $style_spn_est = "font-weight:bold;font-size:9pt;";
                                        // $estado_examen_paciente = ($westado_estudio == 'on') ? '<span id="spn_estado_examen" style="color:green;'.$style_spn_est.'">ACTIVO</span>': '<span id="spn_estado_examen" style="color:red;'.$style_spn_est.'">CERRADO</span>';
                                        $estado_examen_paciente = ', <span style="font-weight:bold;font-size:9pt;">Estado:</span> <span class="spn_estado_examen" style="'.$style_spn_est.'"></span>';
                                        // if($westado_estudio == '')
                                        // {
                                        //     $estado_examen_paciente = '';
                                        // }

                                        $nom_defectoeq                 = (array_key_exists($wequipo, $arr_opciones_equipo)) ? $wequipo.'-'.$arr_opciones_equipo[$wequipo]: '';
                                        $arr_funciones_autocompletar[] = 'crearAutocomplete(\'arr_wequipo'.$ver_en_modal.'\',\'wequipo'.$ver_en_modal.'\',\''.$wequipo.'\',\''.$nom_defectoeq.'\',\'0\', false, \'\')';

                                        $data["html"] = '
                                                        <fieldset>
                                                            <legend align="left"><span style="font-weight:bold;font-size:9pt;" >Detalle examen, consecutivo: </span><span id="spn_wconsecutivo'.$ver_en_modal.'" style="font-weight:bold;font-size:9pt;" >'.$wconsecutivo.'</span> <span id="spn_fecha_estudio'.$ver_en_modal.'" style="font-weight:bold;font-size:9pt;" >'.$wfecha_estudio.'</span>'.$estado_examen_paciente.'</legend>
                                                                <div id="div_formulario_detalle'.$ver_en_modal.'">
                                                                    <input type="hidden" id="id_examen_paciente'.$ver_en_modal.'" name="id_examen_paciente" value="'.$id_examen_paciente.'" >
                                                                    <input type="hidden" id="westado_estudio'.$ver_en_modal.'" name="westado_estudio" value="'.$westado_estudio.'" >
                                                                    <input type="hidden" id="estado_reg_activo'.$ver_en_modal.'" name="estado_reg_activo" value="'.$estado_reg_activo.'" >
                                                                    <input type="hidden" id="wconsecutivo'.$ver_en_modal.'" name="wconsecutivo" value="'.$wconsecutivo.'" >
                                                                    <input type="hidden" id="arr_campos_examen'.$ver_en_modal.'" name="arr_campos_examen" value=\''.json_encode($arr_campos_examen).'\' >
                                                                    <input type="hidden" id="arr_campos_examen_html'.$ver_en_modal.'" name="arr_campos_examen_html" value=\''.json_encode($arr_campos_examen_html).'\' >
                                                                    <input type="hidden" id="wcodigo_examen'.$ver_en_modal.'" name="wcodigo_examen" value="'.$wcodigo_examen.'" >


 
															
																	
																	
                                                                    '.$convenciones_examen.'
                                                                    <br>
                                                                    '.$btns_examen.'
                                                                    <table align="center" cellspacing="1" cellpadding="0" border="0" width="100%">
                                                                        <tr>
                                                                            <td colspan="3" style="text-align:center;font-weight:bold;font-size:12pt;" class="encabezadoTabla">'.$wnombre_examen.'</td>
                                                                        </tr>
                                                                        <tr class="" >
                                                                            <td style="width:2.4%;">&nbsp;</td>
                                                                            <td class="fila1 font_label" style="width:35%;"><span style="font-weight:bold;">Ubicación digital:</td>
                                                                            <td class="fila2">
                                                                                <input type="text" name="wubicacion_digital" id="wubicacion_digital'.$ver_en_modal.'" tipo_dato="add" class="inputs" onfocus="classOnFocus(this);" onkeyup="if(!campoSiguiente(this, event)){ recalcularFormulas(this); }" value="'.utf8_encode($ubicacion_digital).'" placeholder="Texto" style="width: 270px;" maxlength="80">
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="" >
                                                                            <td style="">&nbsp;</td>
                                                                            <td class="fila1 font_label" style=""><span style="font-weight:bold;">Equipo:</td>
                                                                            <td class="fila2">
                                                                                <input type="text" name="wequipo" id="wequipo'.$ver_en_modal.'" tipo_dato="add" class="examen_autocomplete inputs" onfocus="classOnFocus(this);" onkeyup="if(!campoSiguiente(this, event)){ recalcularFormulas(this); }" value="'.utf8_encode($wequipo).'" placeholder="Buscar" style="width: 270px; margin-left: 12px;" maxlength="80">
                                                                                <input type="hidden" name="arr_wequipo'.$ver_en_modal.'" id="arr_wequipo'.$ver_en_modal.'" value=\''.json_encode($arr_opciones_equipo).'\'>
                                                                            </td>
                                                                        </tr>
                                                                        '.$html_campos.'
                                                                    </table>
                                                                    <br>
                                                                    '.$btns_examen.'
                                                                </div>

                                                                <div id="div_imprimir_examen'.$ver_en_modal.'" style="display:none;text-align:center;">
                                                                    <div id="div_edicion_formato_contenedor" class="seccionContenido" style="display:;">
                                                                        <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
                                                                            <tr>
                                                                                <td align="center">
                                                                                    <div id="div_nuevo_comentario" style="text-align:center;background-color:#f2f2f2;">
                                                                                        <div style="text-align:left;color:#999999;display:;">
                                                                                        <!-- <a class="button prominent add installer" href="https://addons.mozilla.org/firefox/downloads/latest/7020/addon-7020-latest.xpi?src=ss" data-hash="sha256:0f4155e386849670e7c2b77b7fc5b5898b4309a8bed6cfea8b42f8418f6c02be">
                                                                                            <b></b>
                                                                                            <span>Agregar a Firefox</span>
                                                                                            </a> -->
                                                                                        <span>Modificar estilos del texto:</span></div>
                                                                                        <table align="center">
                                                                                            <tr><td>
                                                                                            <textarea id="txt_resultado_examen" name="txt_resultado_examen" style="height: 100px;width: 900px;"></textarea>
                                                                                            </td></tr>
                                                                                            <tr>
                                                                                                <td style="text-align:right;">
                                                                                                    <input id="btngenerar_pdf" name="btngenerar_pdf" class="st_boton" type="button" value="Generar PDF" onclick="">
                                                                                                    <input id="btncancelar_imprimir" name="btncancelar_imprimir" class="st_boton" type="button" value="Cancelar" onclick="">
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr><td align="center">
                                                                                                    <div id="td_img_carga_pdf" style="display:none;text-align:center;"><img width="14 " height="14" border="0" src="../../images/medical/ajax-loader9.gif" /> Generando PDF...</div>
                                                                                            </td></tr>
                                                                                        </table>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div id="div_vista_previa_contenedor" class="seccionContenido" style="display:none;text-align:center;">
                                                                                        <div id="div_contenedor_pdf" style="height: auto;text-align:center;" class="parrafo1" >
                                                                                            <br>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                       </fieldset>';
                                    // << [6]
                                }
                                else
                                {
                                    $pie_de_pagina = array();
                                    $html_campos = '';

                                    $whistoria           = $arr_encabezado['whistoria'];
                                    $wingreso            = $arr_encabezado['wingreso'];
                                    $servicio_paciente   = '';
                                    $habitacion_paciente = '';
                                    $codigo_cco          = '';
                                    $habitacion          = '';
                                    $filtro_ingreso        = ($ingreso_examen != '') ? $ingreso_examen: $wingreso;

                                    //echo "<pre>".print_r($arr_encabezado,true)."</pre>";exit();
                                    //Consultar si el paciente esta en urgencias
                                    $q_tabla = "SELECT Habcco, Habcod
                                                FROM   {$wbasedato_movhos}_000020
                                                WHERE  Habhis = '{$whistoria}'
                                                       AND Habing = '{$filtro_ingreso}'";
                                    $r_tabla = mysql_query($q_tabla,$conex);
                                    if($row_tabla = mysql_fetch_assoc($r_tabla))
                                    {
                                        $codigo_cco = $row_tabla['Habcco'];
                                        $habitacion = $row_tabla['Habcod'];
                                    }

                                    // Si no se encontró en urgencias entonces se busca la última ubicación en movhos_18
                                    if($codigo_cco == '')
                                    {
                                        $q_tabla = "SELECT Ubisac, Ubihac
                                                    FROM   {$wbasedato_movhos}_000018
                                                    WHERE  Ubihis = '{$whistoria}'
                                                           AND Ubiing = '{$filtro_ingreso}'";
                                        $r_tabla = mysql_query($q_tabla,$conex);
                                        if($row_tabla = mysql_fetch_assoc($r_tabla))
                                        {
                                            $codigo_cco = $row_tabla['Ubisac'];
                                            $habitacion = $row_tabla['Ubihac'];
                                        }
                                    }

                                    $html_ubicacion_pac = '&nbsp;';
                                    if($codigo_cco != '')
                                    {
                                        $q_tabla = "SELECT Emptcc
                                                    FROM   root_000050
                                                    WHERE  Empcod = '{$wemp_pmla}'";
                                        $r_tabla = mysql_query($q_tabla,$conex);

                                        if($row_tabla = mysql_fetch_assoc($r_tabla))
                                        {
                                            $TablaCco = $row_tabla['Emptcc'];
                                            $campo    = "Ccodes";
                                            if($TablaCco == 'costosyp_000005')
                                            {
                                                $campo      = "Cconom";
                                                $TablaCco   = $wbasedato_movhos.'_000011';
                                            }

                                            if ($TablaCco != 'NO APLICA')
                                            {
                                                $q_cco = "  SELECT  Ccocod AS codigo, {$campo} AS nombre
                                                            FROM    {$TablaCco}
                                                            WHERE   Ccocod = '{$codigo_cco}'
                                                            ORDER BY nombre ";
                                                $r_cco = mysql_query($q_cco,$conex) or die("Error en el query: ".$q_cco."<br>Tipo Error:".mysql_error());

                                                $row_cco = mysql_fetch_assoc($r_cco);
                                                $servicio_paciente = ucfirst(strtolower(limpiarString($row_cco['nombre'])));

                                                $habitacion = ($habitacion != '') ? 'Hab. '.$habitacion: $habitacion;
                                                $html_ubicacion_pac = 'Servicio: '.$servicio_paciente.' '.$habitacion;
                                            }
                                        }
                                    }

                                    $wequipo_td = (array_key_exists($wequipo, $arr_opciones_equipo)) ? 'Equipo: '.utf8_encode($arr_opciones_equipo[$wequipo]): '';
                                    $wequipo_td = str_replace($wequipo.'-', '', $wequipo_td);

                                    $nombres_paciente = limpiarString($arr_encabezado["wnombre1"].' '.$arr_encabezado["wnombre2"].' '.$arr_encabezado["wapellido1"].' '.$arr_encabezado["wapellido2"]);
                                    $sexo = $arr_encabezado["wsexo"];
                                    if($sexo == "M")
                                    {
                                        $sexo = "Masculino";
                                    }
                                    else if($sexo == "F")
                                    {
                                        $sexo = "Femenino";
                                    }
                                    else
                                    {
                                        $sexo = "--";
                                    }

                                    $fecha_examen = "";
                                    if(count($arr_campos_examen_respuestas) > 0)
                                    {
                                        if($arr_campos_examen_respuestas["fecha_modificado"] != '0000-00-00')
                                        {
                                            $fecha_examen = $arr_campos_examen_respuestas["fecha_modificado"].' '.$arr_campos_examen_respuestas["hora_modificado"];
                                        }
                                        else
                                        {
                                            $fecha_examen = $arr_campos_examen_respuestas["Fecha_data"].' '.$arr_campos_examen_respuestas["Hora_data"];
                                        }
                                    }
									//Modificacion 6 de marzo 2019 Freddy Saenz , para que use la fecha del cargo
									//si existe , en caso contrario usar la fecha actual (fecha de modificacion o grabacion de informaicon)
									if (($vfecha_impresion_examen == '0000-00-00') && ( $fecha_examen != "")){
											//$vfecha_impresion_examen = $fecha_examen;//si no tenia informacion 
									}

                                    $menus_sin_ubicar = array();
                                    $arbol_menu = crearArbolMenus($wbasedato_ayu, $wemp_pmla, $conex, $wcodigo_examen, $menus_sin_ubicar);
                                    $div = '';
                                    $resultado_examen = pintarMenuSeleccion($ckeditor_ok_ayucni, $arr_campos_examen_respuestas, $arr_maestro_tablas, $arr_campos_examen, $wcodigo_examen, $wbasedato_ayu, $wemp_pmla, $conex, $arbol_menu['menus_cod'],$arbol_menu['menus_ids'], $div, 'tabs', $pie_de_pagina);

                                    // $arbol = "<pre>".print_r($resultado_examen,true)."</pre>";
                                    // $data["html"] = $resultado_examen;

                                    $html_pie_pagina = '';
                                    // En esta sección se consulta cuántos tipos pié de página hay y se imprimen en columnas
                                    if(count($pie_de_pagina) > 0)
                                    {
                                        $columnas_pie_pagina = 2;
                                        $porcentaje_width    = 100/$columnas_pie_pagina;
                                        $array_chunk         = array_chunk($pie_de_pagina, $columnas_pie_pagina);// Se divide el arreglo de pie_paginas en la cantidad de columnas que se quiere.

                                        $trs_pie_pag = '';
                                        foreach ($array_chunk as $key_1 => $arr_sub_pie_pags)
                                        {
                                            $tds_pie_pag = '';
                                            $cantidad_tds = count($arr_sub_pie_pags);
                                            foreach($arr_sub_pie_pags as $key_2 => $arr_info_pie_pags)
                                            {
                                                $tds_pie_pag .= '<td style="vertical-align: text-top;width:'.$porcentaje_width.'%; text-align:left;">'.$arr_info_pie_pags.'</td>';
                                            }

                                            // Si la cantidad de pie_paginas por fila a pintar no alcanzaron a llegar al límite máximo de columnas por fila
                                            // entonces se completan las columnas con vacíos para completar la estructura de la tabla.
                                            if($cantidad_tds < $columnas_pie_pagina)
                                            {
                                                for ($i=$cantidad_tds; $i <= $columnas_pie_pagina; $i++)
                                                {
                                                    $tds_pie_pag .= '<td style="vertical-align: text-top;">&nbsp;</td>';
                                                }
                                            }
                                            $trs_pie_pag .= '   <tr>
                                                                    <td colspan="'.$columnas_pie_pagina.'">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    '.$tds_pie_pag.'
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="'.$columnas_pie_pagina.'">&nbsp;</td>
                                                                </tr>';
                                        }

                                        $html_pie_pagina = '<tr>
                                                                <td colspan="3">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">&nbsp;</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">
                                                                    <table align="center" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                                        '.$trs_pie_pag.'
                                                                    </table>
                                                                </td>
                                                            </tr>';
                                    }
//$fecha_examen se cambio por $vfecha_impresion_examen en la informacion de $data["html"]
                                    $data["html"] = '<table align="left" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                        <tr>
                                                            <td colspan="3" style="text-align:center;font-weight:bold;font-size:14pt;">'.$wnombre_examen.'</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" style="font-weight:bold;" >Paciente: '.$nombres_paciente.'</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Edad: '.$arr_encabezado["wedad"].'</td>
                                                            <td colspan="2" style="width: 50%">'.$vfecha_impresion_examen.'</td> 
                                                        </tr>
                                                        <tr>
                                                            <td>Sexo: '.$arr_encabezado["wsexo"].'</td>
                                                            <td colspan="2">Examen: '.$wconsecutivo.'</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Documento: '.$arr_encabezado["wtipodoc"].' '.$arr_encabezado["wdocumento"].'</td>
                                                            <td colspan="2">Ubicación digital: '.utf8_encode($ubicacion_digital).'</td>
                                                        </tr>
                                                        <tr>
                                                            <td>'.$wequipo_td.'</td>
                                                            <td colspan="2" style="width: 50%">'.$html_ubicacion_pac.'</td>
                                                        </tr>
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td colspan="2">Entidad: '.$arr_encabezado['wempresa'].'</td>
                                                        </tr>
                                                        '.$resultado_examen.'<br>'.$html_pie_pagina.'
                                                        <tr>
                                                            <td colspan="3">
                                                                <p style="font-size:10pt;">(*)  Señor Usuario: Este resultado debe conocerlo el médico que lo ordenó y/o su médico tratante, quien decidirá la conducta a seguir.</p>
                                                            </td>
                                                        </tr>
                                                    </table>';
                                }
                            }
                        }

                        $sqlNomUsu = "SELECT Descripcion, Codigo FROM usuarios WHERE Codigo IN ('{$usuario_trancribe}', '{$usuario_modifica}') ";
                        $resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
                        $us_ok     = false;
                        while($rowNomUsu = mysql_fetch_assoc($resNomUsu))
                        {
                            if($rowNomUsu['Codigo'] == $usuario_trancribe)
                            {
                                if($usuario_trancribe == $usuario_modifica)
                                {
                                    $usuario_modifica = utf8_encode(ucwords(strtolower($rowNomUsu['Descripcion'])));
                                }
                                $usuario_trancribe = utf8_encode(ucwords(strtolower($rowNomUsu['Descripcion'])));
                                $us_ok = true;
                            }
                            elseif($rowNomUsu['Codigo'] == $usuario_modifica)
                            {
                                $usuario_modifica = utf8_encode(ucwords(strtolower($rowNomUsu['Descripcion'])));
                                $us_ok = true;
                            }
                        }

                        if(!$us_ok)
                        {
                            $sqlNomUsuMig = "SELECT Opcdes AS Descripcion, Opccod AS Codigo FROM {$wbasedato_ayu}_000005 WHERE Opctbl = 'USUARIOS_MIGRACION' AND Opccod IN ('{$usuario_trancribe}', '{$usuario_modifica}')";
                            $resNomUsuMig = mysql_query($sqlNomUsuMig, $conex) or die("<b>ERROR EN QUERY MATRIX(resNomUsuMig):</b><br>".$sqlNomUsuMig.' > '.mysql_error());
                            $rowNomUsuMig = mysql_fetch_assoc($resNomUsuMig);
                            if($rowNomUsuMig['Codigo'] == $usuario_trancribe)
                            {
                                if($usuario_trancribe == $usuario_modifica)
                                {
                                    $usuario_modifica = utf8_encode(ucwords(strtolower($rowNomUsuMig['Descripcion'])));
                                }
                                $usuario_trancribe = utf8_encode(ucwords(strtolower($rowNomUsuMig['Descripcion'])));
                            }
                            elseif($rowNomUsuMig['Codigo'] == $usuario_modifica)
                            {
                                $usuario_modifica = utf8_encode(ucwords(strtolower($rowNomUsuMig['Descripcion'])));
                            }
                        }

                        $data["usuario_trancribe"]           = $usuario_trancribe;
                        $data["usuario_modifica"]            = $usuario_modifica;
                        $data["arr_funciones_autocompletar"] = $arr_funciones_autocompletar;
                        $data["arr_encabezado_examen"]       = base64_encode(serialize($arr_encabezado_examen));
                        $data["westado_estudio"]             = $westado_estudio;
                        $data["estado_reg_activo"]           = $estado_reg_activo;
                    break;

                case 'buscar_paciente':
                        $codigoempresaparticular = (!isset($codigoempresaparticular)) ? '': $codigoempresaparticular; // se realiza esta validación que será útil solo cuando se publique esta versión y mientras no se recargue la página en los computadores de trabajo de cardiología.
                        //Consultar permisos de usuario en el programa
                        $arr_permisos_usuario = array("grabar"=>"off", "modificar"=>"off", "imprimir"=>"off", "borrar"=>"off", "ver_consultar"=>"off", );

                        $user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato_tal, $user_session);
                        $sqlccg = " SELECT  Ideccg
                                    FROM    {$wbasedato_tal}_000013
                                    WHERE   Ideuse='{$user_session_wemp}'";
                        $ccg_emp = "";
                        if($result = mysql_query($sqlccg,$conex))
                        {
                            if(mysql_num_rows($result) > 0)
                            {
                                $rowccg = mysql_fetch_assoc($result);
                                $ccg_emp = $rowccg["Ideccg"];
                            }
                        }

                        $variables = array();
                        $variables['Perusu']['combinar']    = true;
                        $variables['Perusu']['valor']       = $user_session;
                        $variables['Perusu']['operador']    = "LIKE";
                        $variables['Perusu']['comodin_izq'] = "%";
                        $variables['Perusu']['comodin_der'] = "%";

                        $variables['Perccg']['combinar'] = true;
                        $variables['Perccg']['valor']    = $ccg_emp;

                        $variables['Perest']['combinar'] = false;
                        $variables['Perest']['valor']    = "on";

                        // De acuerdo a parámetros generales se combinan para encontrar por lo menos una plantilla que aplique a la liquidación
                        $sqlPer = generarQueryCombinado($variables, "{$wbasedato_ayu}_000007");
                        $resultPer = mysql_query($sqlPer,$conex) or die(mysql_errno().' - '.mysql_error().' SQL:'.$sqlPer);
                        if(mysql_num_rows($resultPer) > 0)
                        {
                            $row_per = mysql_fetch_assoc($resultPer);
                            $id_permiso = $row_per["id"];
                            $SqlPermiso = " SELECT  Pergra AS grabar, Permod AS modificar, Perimp AS imprimir, Perbrr AS borrar, Perver AS ver_consultar
                                            FROM    {$wbasedato_ayu}_000007
                                            WHERE   id = '{$id_permiso}'";

                            $respermiso           = mysql_query($SqlPermiso, $conex) or die("Error en el query: ".$SqlPermiso."<br>Tipo Error:".mysql_error());
                            $arr_permisos_usuario = mysql_fetch_assoc($respermiso);
                        }
                        $data["arr_permisos_usuario"] = $arr_permisos_usuario;

                        // Consultar paciente en matrix
                        // Esta variable permite en javascript mostrar una ventana DIALOG, que dependiendo de la respuesta puede mostrar todos los pacientes
                        // que coinciden con la busqueda o todas los ingresos de un paciente en caso de no elegir un número de ingreso específico,
                        // o por el contrario, si ya hay historia o ingreso específico entonces muestra los datos en el encabezado del programa.
                        $data["flujo_consulta"] = 'cargar_paciente';

                        $data["arr_encabezado"] = array("whistoria"  => "",
                                                        "wingreso"   => "",
                                                        "wtipodoc"   => "",
                                                        "wdocumento" => "",
                                                        "wnombre1"   => "",
                                                        "wnombre2"   => "",
                                                        "wapellido1" => "",
                                                        "wapellido2" => "",
                                                        "westadopac" => "",
                                                        "wtelefono"  => "",
                                                        "wdireccion" => "",
                                                        "wfechaNac"  => "",
                                                        "wedad"      => "",
                                                        "wsexo"      => "",
                                                        "wcodempresa"=> "",
                                                        "wnomempresa"=> "");
                        $data["arr_encabezado_b64"] = $data["arr_encabezado"];

                        $filtros = "";
                        $and = "";
                        if($whistoria != '' && $wingreso != '')
                        {
                            cargarEncabezadoPaciente($conex, $wemp_pmla, $codigoempresaparticular, $wbasedato_ayu, $wbasedato_cliame, $wbasedato_movhos, $whistoria, $wingreso, $data);
                        }
                        elseif($whistoria != '')
                        {
                            //$sql = queryIngresosHistoria();
                            buscarIngresosPorHistoria($conex, $wemp_pmla, $codigoempresaparticular, $wbasedato_ayu, $wbasedato_cliame, $wbasedato_movhos, $whistoria, $data);
                        }
                        else
                        {
                            if($wdocumento != '')
                            {
                                $filtros .= " {$and} c100.Pacdoc LIKE '%$wdocumento%'";
                                $and = "AND";
                            }

                            if($wnombre1 != '')
                            {
                                $filtros .= " {$and} c100.Pacno1 LIKE '%$wnombre1%'";
                                $and = "AND";
                            }
                            if($wnombre2 != '')
                            {
                                $filtros .= " {$and} c100.Pacno2 LIKE '%$wnombre2%'";
                                $and = "AND";
                            }
                            if($wapellido1 != '')
                            {
                                $filtros .= " {$and} c100.Pacap1 LIKE '%$wapellido1%'";
                                $and = "AND";
                            }
                            if($wapellido2 != '')
                            {
                                $filtros .= " {$and} c100.Pacap2 LIKE '%$wapellido2%'";
                                $and = "AND";
                            }

                            if($filtros != '')
                            {
                                $html_pacs = "";
                                $data["flujo_consulta"] = 'cargar_lista_pacientes';
                                $sql = "SELECT  c100.Pachis, c100.Pacact AS pac_activo, c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2, c100.Pacdoc, c100.Pactdo AS wtipo_documento, c100.Pactam
                                        FROM    {$wbasedato_cliame}_000100 AS c100
                                        WHERE   {$filtros}
                                        ORDER BY c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2
                                        LIMIT 50";
                                //------------------- GENERAR HTML PARA LAS OPCIONES DE ELECCIÓN
                                if($result = mysql_query($sql,$conex))
                                {
                                    $cont = 1;
                                    $numrows = mysql_num_rows($result);
                                    if($numrows == 1)
                                    {
                                        $row = mysql_fetch_assoc($result);
                                        buscarIngresosPorHistoria($conex, $wemp_pmla, $codigoempresaparticular, $wbasedato_ayu, $wbasedato_cliame, $wbasedato_movhos, $row["Pachis"], $data);
                                    }
                                    elseif($numrows > 1)
                                    {
                                        while($row = mysql_fetch_assoc($result))
                                        {
                                            $css         = ($cont % 2 == 0) ? "fila1": "fila2";
                                            $b_historia  = $row["Pachis"];
                                            $b_nombres   = utf8_encode(limpiarString($row["Pacno1"].' '.$row["Pacno2"].' '.$row["Pacap1"].' '.$row["Pacap2"]));
                                            $b_documento = $row["wtipo_documento"].' '.$row["Pacdoc"];
                                            $b_activo    = ($row["pac_activo"] == '') ? "Activo": "Inactivo";
                                            $html_pacs .= ' <tr class="'.$css.'">
                                                                <td style="font-weight:bold;">'.$b_historia.'</td>
                                                                <td style="text-align:left;">'.$b_nombres.'</td>
                                                                <td style="text-align:left;">'.$b_documento.'</td>
                                                                <td style="text-align:center;">'.$b_activo.'</td>
                                                                <td><input type="button" value="Ver" onclick="filtrarPacienteJS(\''.$b_historia.'\');"></td>
                                                            </tr>';
                                            $css++;
                                        }

                                        if($html_pacs != '')
                                        {
                                            $html_pacs = '  <span style="font-weight:bold;font-size:8pt;font-family:cursive;">Solo se mostrarán máximo 50 registros.</span>
                                                            <table align="center" cellspacing="1" cellpadding="0" border="0">
                                                                <tr class="encabezadoTabla">
                                                                    <td>Historia</td>
                                                                    <td>Nombres</td>
                                                                    <td>Documento</td>
                                                                    <td>Estado</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                                '.$html_pacs.'
                                                            </table>';
                                        }
                                        $data["html"] = $html_pacs;
                                    }
                                    else
                                    {
                                        $data["html"] = '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                                                            No hay resultados en la búsqueda
                                                        </div>';
                                    }
                                }
                                else
                                {
                                    $data["sql"] = $sql.' >> '.mysql_error();
                                    $data["error"] = 1;
                                    $data["mensaje"] = "Problemas al consultar datos cargar_lista_pacientes";
                                }
                            }
                            else
                            {
                                $data["error"] = 1;
                                $data["mensaje"] = "No escribió ningún dato para la búsqueda";
                            }
                        }

                        /*if($data["html"] == '')
                        {
                            $data["html"] = '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                                                    No hay resultados en la búsqueda de los datos del paciente en el sistema Matrix.
                                                </div>';
                        }*/

                        $data["arr_encabezado_b64"] = base64_encode(serialize($data["arr_encabezado_b64"]));
                    break;

                case 'buscar_examenes_paciente':
                        $arr_datos_paciente = (is_array($arr_datos_paciente)) ? $arr_datos_paciente: unserialize(base64_decode($arr_datos_paciente));
                        // Consultar maestro de examenes con sus datos
                        $arr_maestro_examenes  = array(); // Maestro de los examenes configurados
                        $arr_examenes_paciente = array(); // Listado de examenes realizados al paciente
                        $options_examenes      = "<option value=''>Seleccione nuevo examen..</option>";
                        $whistoria             = $arr_datos_paciente["whistoria"];
                        $wingreso              = $arr_datos_paciente["wingreso"];
                        $wtipodoc              = $arr_datos_paciente["wtipodoc"];
                        $wdocumento            = $arr_datos_paciente["wdocumento"];
                        $westPac			   = $arr_datos_paciente["westPac"];

                        $sqlM = "   SELECT  Enfcod, Enffor, Enfdes, Enfest
                                    FROM    {$wbasedato_ayu}_000001";
                        if($result = mysql_query($sqlM,$conex))
                        {
                            $numrows = mysql_num_rows($result);
                            if($numrows > 0)
                            {
                                while ($row = mysql_fetch_assoc($result))
                                {
                                    $nombre_examen = utf8_encode(limpiarString($row["Enfdes"]));
                                    if(!array_key_exists($row["Enfcod"], $arr_maestro_examenes))
                                    {
                                        $arr_maestro_examenes[$row["Enfcod"]] = array();
                                        if ($row["Enfest"] == 'on') {
                                            $options_examenes .= '<option value="'.$row["Enfcod"].'">'.$row["Enfcod"].'-'.$nombre_examen.'</option>';
                                        }
                                    }
                                    $arr_maestro_examenes[$row["Enfcod"]] = array(  "wcodigo_examen"          => $row["Enfcod"],
                                                                                    "wtabla_resultado_examen" => $row["Enffor"],
                                                                                    "wnombre_examen"          => $nombre_examen);
                                }

                                // Consultar en la tabla de examenes del paciente, por cada examen del array arr_maestro_examenes, crear un query que consulte
                                // todas las tablas asociadas al examen y extraiga los resultados de los examenes asociados al paciente en cada una de ellas

                                $sql_his_ing = '';
                                if($whistoria != '' && $wingreso != '')
                                {
                                    $sql_his_ing = "SELECT  a6.Espcod AS wconsecutivo, a6.Esphis, a6.Esping, a6.Esptdo, a6.Espdoc,
                                                            a6.Espenf AS wcodigo_examen, a6.Espesp, a6.Espfmo, a6.Esphmo, a6.Espusm,
                                                            a6.Esplog, a6.Fecha_data, a6.Hora_data, a6.id AS ID_6, a6.Espest
                                                    FROM    {$wbasedato_ayu}_000006 AS a6
                                                    WHERE   a6.Esphis = '{$whistoria}'
                                                            AND a6.Esping = '{$wingreso}'

                                                    UNION";
                                }

                                // Consulta encabezado examenes paciente
                                // 1. Exámenes por historia e ingreso
                                // 2. Exámenes por tipo documento y documento
                                // 3. Exámenes por documento, solo registros migrados
                                $sqlEP = "  {$sql_his_ing}

                                            SELECT  a6.Espcod AS wconsecutivo, a6.Esphis, a6.Esping, a6.Esptdo, a6.Espdoc,
                                                    a6.Espenf AS wcodigo_examen, a6.Espesp, a6.Espfmo, a6.Esphmo, a6.Espusm,
                                                    a6.Esplog, a6.Fecha_data, a6.Hora_data, a6.id AS ID_6, a6.Espest
                                            FROM    {$wbasedato_ayu}_000006 AS a6
                                            WHERE   a6.Esptdo = '{$wtipodoc}'
                                                    AND a6.Espdoc = '{$wdocumento}'

                                            UNION

                                            SELECT  a6.Espcod AS wconsecutivo, a6.Esphis, a6.Esping, a6.Esptdo, a6.Espdoc,
                                                    a6.Espenf AS wcodigo_examen, a6.Espesp, a6.Espfmo, a6.Esphmo, a6.Espusm,
                                                    a6.Esplog, a6.Fecha_data, a6.Hora_data, a6.id AS ID_6, a6.Espest
                                            FROM    {$wbasedato_ayu}_000006 AS a6
                                            WHERE   a6.Espdoc = '{$wdocumento}'
                                                    AND a6.Espfcm <> '0000-00-00'

                                            ORDER BY Fecha_data DESC, Hora_data DESC";
                                $data["sql_examen"] = $sqlEP;
								
								//echo '<pre>'; print_r($sqlEP); echo '</pre><hr>';
								
                                if($result = mysql_query($sqlEP,$conex))
                                {
                                    $numrows = mysql_num_rows($result);
                                    if($numrows > 0)
                                    {
                                        while ($row = mysql_fetch_assoc($result))
                                        {
                                            $arr_examenes_paciente[] = array(   "wcodigo_examen"      => $row["wcodigo_examen"],
                                                                                "wconsecutivo"        => $row["wconsecutivo"],
                                                                                "whistoria"           => $row["Esphis"],
                                                                                "wingreso"            => $row["Esping"],
                                                                                "wtipodoc"            => $row["Esptdo"],
                                                                                "wdocumento"          => $row["Espdoc"],
                                                                                "westadoexamenpac"    => $row["Espesp"],
                                                                                "wfechamodificado"    => $row["Espfmo"],
                                                                                "whoramodificado"     => $row["Esphmo"],
                                                                                "wfechacreado"        => $row["Fecha_data"],
                                                                                "whoracreado"         => $row["Hora_data"],
                                                                                "estado_reg_activo"   => $row["Espest"],
                                                                                "id_examen_paciente"  => $row["ID_6"]);
                                        }

                                        if(count($arr_examenes_paciente) > 0)
                                        {
                                            $html_examenespac = "";
                                            $cont = 1;
                                            foreach ($arr_examenes_paciente as $key_exam => $arr_infoexa)
                                            {
                                                $nombre_examen = "---";
                                                if(array_key_exists($arr_infoexa['wcodigo_examen'], $arr_maestro_examenes))
                                                {
                                                    $nombre_examen = $arr_maestro_examenes[$arr_infoexa['wcodigo_examen']]['wnombre_examen'];
                                                }

                                                $bg_est = ($arr_infoexa['westadoexamenpac'] == 'on') ? 'green': 'red';
                                                $bg_est = ($arr_infoexa['estado_reg_activo'] == 'off') ? 'black': $bg_est;

                                                $css = ($cont % 2 == 0) ? "fila1": "fila2";
                                                $html_examenespac .= '<tr class="'.$css.' find" onmouseover="trOver(this);" onmouseout="trOut(this);">
                                                                        <td style="font-weight:bold;"><span style="background-color:'.$bg_est.';">&nbsp;&nbsp;&nbsp;</span></td>
                                                                        <td style="font-weight:bold;">'.$arr_infoexa['whistoria'].'-'.$arr_infoexa['wingreso'].'</td>
                                                                        <td>'.$arr_infoexa['wcodigo_examen'].'-'.$nombre_examen.'</td>
                                                                        <td>'.$arr_infoexa['wconsecutivo'].'</td>
                                                                        <td>'.$arr_infoexa['wfechacreado'].' '.$arr_infoexa['whoracreado'].'</td>
                                                                        <td><input id="btn_ver_examen_'.$arr_infoexa['id_examen_paciente'].'" type="button" class="btn_ver_examen" onclick="cargarDetalleExamenPaciente(\''.$arr_infoexa['whistoria'].'\',\''.$arr_infoexa['wingreso'].'\',\''.$arr_infoexa['wcodigo_examen'].'\',\''.$arr_infoexa['wconsecutivo'].'\',\''.$arr_infoexa['id_examen_paciente'].'\');" value="Ver"></td>
                                                                    </tr>';
                                                $cont++;
                                            }

                                            $data["html"] = '   <fieldset>
                                                                    <legend align="left"><span style="font-weight:bold;font-size:9pt;" >Exámenes realizados al paciente</span></legend>
                                                                        <div class="encabezadoTabla" style="text-align:left;">
                                                                            Filtrar listado:<input id="id_search_examenes_guardados" type="text" value="" size="25" name="id_search_examenes_guardados" placeholder="Buscar en exámenes realizados">
                                                                        </div>
                                                                        <div style="width:100%; height: 300px; overflow:auto;" >
                                                                        <table width="100%" style="border: 0px solid #999999;" id="tabla_examanes_realizados">
                                                                            <tr class="encabezadoTabla">
                                                                                <td>Est.</td>
                                                                                <td>Historia/Ingreso</td>
                                                                                <td>Código examen</td>
                                                                                <td>Consecutivo</td>
                                                                                <td>Fecha creado</td>
                                                                                <td>&nbsp;</td>
                                                                            </tr>
                                                                            '.$html_examenespac.'
                                                                        </table>
                                                                        </div>
                                                                </fieldset>';
                                        }
                                        else
                                        {
                                            $data["html"] = '<div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                                                                El paciente no tiene exámenes realizados ni en proceso
                                                            </div>';
                                        }
                                    }
                                    else
                                    {
                                        // $data["error"] = 1;
                                        $data["html"] = '<div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                                                            El paciente no tiene exámenes realizados ni en proceso
                                                        </div>';
                                    }
									
									$estilosSelectExamenNuevo = "";
									if($westPac=="Inactivo")
									{
										$estilosSelectExamenNuevo = "disabled title='El paciente debe estar activo para agregar un nuevo examen.'";
									}

                                    $data["html"] = '  <div style="background-color: #fffee2;padding: 3px;text-align:left;" class="div_crear_nuevo_examen">
                                                            <span style="font-weight:bold;">Nuevo: </span><select name="wnuevo_examen" id="wnuevo_examen" onchange="verBtnNuevoExam(this);" '.$estilosSelectExamenNuevo.'>
                                                                '.$options_examenes.'
                                                            </select>
                                                            <input style="display:none;" type="button" id="btn_nuevo_examen" value="Agregar nuevo examen" onclick="crearNuevoExamenPaciente(\''.$whistoria.'\',\''.$wingreso.'\',\'wnuevo_examen\');">
                                                        </div>'.$data["html"];
                                }
                                else
                                {
                                    $data["error"] = 1;
                                    $data["mensaje"] = "Problemas al consultar exámenes del paciente";
                                    $data["sql_err"] = $sqlEP.' > '.mysql_error();
                                }
                            }
                            else
                            {
                                $data["error"] = 1;
                                $data["mensaje"] = "El maestro de exémenes no tiene datos para esta aplicación";
                            }
                        }
                        else
                        {
                            $data["error"] = 1;
                            $data["mensaje"] = "No se pudo consultar el maestro de exámenes";
                        }
                    break;

                case 'cosultar_opciones_transcripcion':
                        $opciones_tabla = array();
                        $filtros = ($general != '') ? "AND Cnomed = '{$general}'" : "AND Cnomed <> '*'"; // Cuando solo se quiere consultar normal general
                        $sqlNor = " SELECT  Cnoenf AS wcodigo_examen, Cnocod AS codigo_reg, Cnotit AS titulo, Cnomed AS identificador_medico, Cnotra AS txt_transcrip, Cnotth AS txt_transcrip_html, Cnocca AS cod_campo_aplica
                                    FROM    {$wbasedato_ayu}_000003
                                    WHERE   Cnoenf = '{$wcodigo_examen}'
                                            AND Cnocca = '{$wcod_campo}'
                                            {$filtros}
                                            AND Cnoest = 'on'";
                        $opciones = '';//'<option value="">Seleccione..</option>';
                        $arr_campos_normales = array();

                        $cont = 0;
                        if($result = mysql_query($sqlNor, $conex))
                        {
                            //consultar los médicos con normales preconfiguradas para mostrarlos en la lista
                            $sqlOpcTabla = "SELECT  aTab.Tblcod AS cod_tabla, aTab.Tblnom AS nombre_tabla, aOpc.Opccod AS cod_opcion, aOpc.Opcdes AS nombre_opcion, aOpc.Opcobs AS observacion_add
                                            FROM    {$wbasedato_ayu}_000004 AS aTab
                                                    INNER JOIN
                                                    {$wbasedato_ayu}_000005 AS aOpc ON ( aTab.Tblcod = aOpc.Opctbl)
                                            WHERE   aTab.Tblcod IN ('CARDIOLOGOS')
                                            ORDER BY aTab.Tblcod, aOpc.Opcdes";
                            // $data["sql_Mtablas"] = $sqlOpcTabla;
                            if($resultMedicos = mysql_query($sqlOpcTabla,$conex))
                            {
                                while ($rowMed = mysql_fetch_assoc($resultMedicos))
                                {
                                    if(!array_key_exists($rowMed["cod_opcion"], $opciones_tabla))
                                    {
                                        $opciones_tabla[$rowMed["cod_opcion"]] = array("nombre_opcion"=>utf8_encode(limpiarString($rowMed["nombre_opcion"])), "observacion_add"=>utf8_encode(limpiarString($rowMed["observacion_add"])));
                                    }
                                }
                            }

                            while ($row = mysql_fetch_assoc($result))
                            {
                                $css = ($cont % 2 == 0) ? 'fila1': 'fila2';
                                $identificador_medico = $row["identificador_medico"];
                                $nomb_medico = (array_key_exists($identificador_medico, $opciones_tabla)) ? $opciones_tabla[$identificador_medico]['nombre_opcion']: ' -- ';
                                if(!array_key_exists($identificador_medico, $arr_campos_normales))
                                {
                                    $txt_normales = ($ckeditor_ok_ayucni == 'on') ? $row["txt_transcrip_html"] : $row["txt_transcrip"];
                                    // if($ckeditor_ok_ayucni != 'on')
                                    {
                                        $txt_normales = preg_replace("/[\n]+/", '<br>', $txt_normales);
                                    }
                                    $arr_campos_normales[$identificador_medico] = base64_encode($txt_normales);
                                }
                                $opciones .= '<div class="'.$css.'" style="cursor:pointer;font-size:16px;" onmouseover="trOver(this);" onmouseout="trOut(this);" onclick="seleccionTranscripcion(\''.$wcod_campo.'\',\''.$identificador_medico.'\');"><span>'.$identificador_medico.'</span>-<span>'.$nomb_medico.'</span></div>';
                                // $opciones .= '<option value="'.$identificador_medico.'">'.$identificador_medico.'-'.substr(utf8_encode($row["txt_transcrip"]), 0, 80).'</option>';
                                $cont++;
                            }
                        }
                        $data['normales'] = $arr_campos_normales;
                                                // Ver <select name="select_opcion_transcrip_'.$wcod_campo.'" id="select_opcion_transcrip_'.$wcod_campo.'" onchange="previsualizarTranscripcion(\''.$wcod_campo.'\',\'txt_previsualizarTranscripcion_'.$wcod_campo.'\',\'div_previsualizarTranscripcion_'.$wcod_campo.'\');">
                                                //     '.$opciones.'
                                                // </select>
                        $html_opciones = '  <div class="encabezadoTabla" style="font-weight:bold;text-align:center;margin-bottom:2px;font-size:14px;">
                                                Elegir médico
                                            </div>
                                            <div style="font-weight:bold;font-size:14px;">
                                                <input type="hidden" id="select_opcion_transcrip_'.$wcod_campo.'" >
                                                '.$opciones.'
                                            </div>
                                            <input type="hidden" value=\''.json_encode($arr_campos_normales).'\' id="txt_previsualizarTranscripcion_'.$wcod_campo.'">
                                            <div id="div_previsualizarTranscripcion_'.$wcod_campo.'" style="width:600px;text-align:center;"></div>';
                        $data["html"] = $html_opciones;
                    break;

                case 'buscar_ajx_opciones':
                        $arr_ajx_opciones = array();
                        $tablas_usar = $tabla_opc_ajax;
                        $term = utf8_decode($term);
                        $sqlOpcTabla = "SELECT  aTab.Tblcod AS cod_tabla, aTab.Tblnom AS nombre_tabla, aOpc.Opccod AS cod_opcion, aOpc.Opcdes AS nombre_opcion, aOpc.Opcobs AS observacion_add
                                        FROM    {$wbasedato_ayu}_000004 AS aTab
                                                INNER JOIN
                                                {$wbasedato_ayu}_000005 AS aOpc ON ( aTab.Tblcod = aOpc.Opctbl)
                                        WHERE   aTab.Tblcod IN ('{$tablas_usar}')
                                                AND CONCAT(aOpc.Opccod,'-',aOpc.Opcdes) like '%{$term}%'
                                        ORDER BY aTab.Tblcod, aOpc.Opcdes
                                        LIMIT 50";
                        if($result = mysql_query($sqlOpcTabla,$conex))
                        {
                            while ($row = mysql_fetch_assoc($result))
                            {
                                $nombre_opcion = preg_replace("/[\n]+/", '', utf8_encode(limpiarString($row["nombre_opcion"])));
                                $cod_opcion = $row["cod_opcion"];
                                $arr_ajx_opciones[] = array("value"  => $cod_opcion.'-'.$nombre_opcion,
                                                            "label"  => $cod_opcion.'-'.$nombre_opcion,
                                                            "codigo" => $cod_opcion,
                                                            "nombre" => $cod_opcion.'-'.$nombre_opcion);
                            }
                        }
                        $data = $arr_ajx_opciones;
                    break;

                default:
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'delete' :
            switch ($form)
            {
                /*case 'CODIGO_EJEMPLO':
                        $query = "  UPDATE  ".$wbasedato."_".OBSERVACIONES_ORDEN."
                                            SET Segest = 'off'
                                    WHERE   id = '".$id_observ."'";
                        if($result = mysql_query( $conex,$query))
                        {

                        }
                        else
                        {
                            debug_log_inline('',"<span class=\"error\">ERROR</span> Error al borrar obsrvaci? de la orden: $worden Fuente: $wfuente <br>&raquo; ".$query."<br>&raquo;No. ".mysql_errno().'<br>&raquo;Err: '.mysql_error()."<br>");
                            $descripcion = "(".mysql_errno().') '.mysql_error()."|obs:'Error al borrar obsrvaci? de la orden: $worden Fuente: $wfuente";
                            // insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wfuente.'-'.$worden, $query);
                            $data['mensaje'] = 'No se pudo eliminar la observaci?.';
                            $data['error'] = 1;
                        }
                        $data['debug_log'] = utf8_encode(debug_log_inline());
                    break;*/

                default:
                    $data['mensaje'] = 'No se ejecutó ningúna rutina interna del programa';
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
$wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
$wbasedato_ayu    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ayudas_diag');
$wbasedato_ayu    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ayudas_diag');
$wbasedato_tal    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
$ckeditor_ok_ayucni= consultarAliasPorAplicacion($conex, $wemp_pmla, 'ckeditor_ok_ayucni');
$codigoempresaparticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');

$whis = (!isset($whis)) ? '': $whis;
$wing = (!isset($wing)) ? '': $wing;

?>
<html lang="es-ES">
<head>
    <title>Transcripción</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librería para detectar compatibilidad HTML5 con varios navegadores -->
    <!-- <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script> -->

    <!-- Librerías para editar formato -->
    <?php
    if($ckeditor_ok_ayucni == 'on'){
    ?>
    <link rel="stylesheet" href="../../../include/root/jquery.cleditor.css" type="text/css">
    <script src="../../../include/root/jquery.cleditor.js" type="text/javascript"></script>
    <?php
    }
    ?>
    <script src="../../../include/root/ckeditor/ckeditor.js" type="text/javascript"></script>
    <script src="../../../include/root/ckeditor/adapters/jquery.js" type="text/javascript"></script>

    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

    <script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
    <link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
    <script src="../../../include/root/toJson.js" type="text/javascript"></script>


    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;

        var usuario_trancribe = '';
        var usuario_modifica  = '';
        var ckeditor_ok = false;

        function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
        {
            var msj_extra = '';
            msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
            jAlert($("#failJquery").val()+msj_extra, "Mensaje");
            $("#div_error_interno").html(xhr.responseText);
            // console.log(xhr);
            // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
            fnModalLoading_Cerrar();
            $(".bloquear_todo").removeAttr("disabled");
        }

        $(function(){
            $('.campo_autocomplete').on({
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

            reiniciarCamposSoloNumericos();

            iniciarInputsSize_acciones();
        });

        $(document).ready( function ()
        {
            ckeditor_ok = ($("#ckeditor_ok_ayucni").val() == 'on') ? true: false; //variable global javascript declarada inicialmente false.
            $("#accordionDatosPaciente, #accordionExamenesPaciente, #accordionExameneDetalle").accordion({
                collapsible: true,
                heightStyle: "content"
            });

            //Verificar si hay parametros GET de entrada e intentar cargar datos automaticamente del paciente
            var get_whistoria = $("#get_whistoria").val();
            var get_wingreso  = $("#get_wingreso").val();

            if(get_whistoria != "")
            {
                buscarPacienteJS(get_whistoria, get_wingreso, '', '', '', '', '');
            }
        });

        function inicializarCamposFlotantes()
        {
            /**
                Inicializa el div flotante deslizante que está desplegado en la parte superior izquierda de la pantalla, sobre la cuál aparecen opciones adicionales para el reporte.
            */
            $("#caja_campos_flotantes").show();
            var posicion_query = $("#caja_campos_flotantes").offset();
            var margenSuperior_query = 15;
             $(window).scroll(function() {
                 if ($(window).scrollTop() > posicion_query.top) {
                     $("#caja_campos_flotantes").stop().animate({
                         marginTop: $(window).scrollTop() - posicion_query.top + margenSuperior_query
                     });
                 } else {
                     $("#caja_campos_flotantes").stop().animate({
                         marginTop: 0
                     });
                 };
             });

            // $( "#caja_campos_flotantes" ).resizable({
            //   ghost: true
            // });
        }

        /**
         * [iniciarInputsSize_acciones: Se usa para inicializar todos los objetos html que deben reaccionar ante los botones de zoom
         *                                 adicionalmente se inicializan las acciones sobre el formulario p.ej. Guardar, Imprimir, ...]
         * @return {[type]} [description]
         */
        function iniciarInputsSize_acciones()
        {
            $("#txt_mas").click(function(){
                // console.log($(".font_label, .inputs"));
                $(".font_label, .inputs, h4").animate({'font-size': '+=10%'}, "slow");
            });

            $("#txt_menos").click(function(){
                $(".font_label, .inputs, h4").animate({'font-size': '-=10%'}, "slow");
            });

            reinicarAcordeonDetalleExamenPaciente();

            inicarCamposMemo('');

            $("#txt_menos").click(function(){
                $(".font_label, .inputs, h4").animate({'font-size': '-=10%'}, "slow");
            });

            $(".toggle_tr").css({"cursor":"pointer"}).click(function(){
                var index_css = $(this).attr("css_toggle");
                if($(".css_"+index_css).length)
                {

                    $(".css_"+index_css).toggle();
                    /*$(".css_"+index_css).slideToggle( "slow", function() {
                        // Animation complete.
                    });*/
                }
            });

            var btn_guardar_examen   = $(".btn_guardar_examen");
            var btn_cerrar_examen    = $(".btn_cerrar_examen");
            var btn_inactivar_examen = $(".btn_inactivar_examen");
            var btn_activar_examen   = $(".btn_activar_examen");
            var btn_imprimir_examen  = $(".btn_imprimir_examen");
            var btngenerar_pdf       = $("#btngenerar_pdf");
            var btncancelar_imprimir = $("#btncancelar_imprimir");

            btn_guardar_examen.off('click').on('click',function(){
                var id_examen_paciente = $("#id_examen_paciente").val();
                var wconsecutivo = $("#wconsecutivo").val();
                var westado_estudio = 'on';
                validarGuardarExamen(id_examen_paciente, wconsecutivo, westado_estudio);
            });

            btn_cerrar_examen.click(function(){
                var id_examen_paciente = $("#id_examen_paciente").val();
                var wconsecutivo = $("#wconsecutivo").val();
                //Validar firma del médico para cerrar el examen.
                // validarFirmaMedico(id_examen_paciente, wconsecutivo);
                var westado_estudio = 'off';
                validarGuardarExamen(id_examen_paciente, wconsecutivo, westado_estudio);
            });

            btn_inactivar_examen.click(function(){
                var id_examen_paciente = $("#id_examen_paciente").val();
                var wconsecutivo = $("#wconsecutivo").val();

                if(id_examen_paciente != '')
                {
                    confirmarActivarInactivar(id_examen_paciente, wconsecutivo, 'off');
                }
                else
                {
                    jAlert("El examen actual no ha sido guardado, no es posible inactivar.","Mensaje");
                }
            });

            btn_activar_examen.click(function(){
                var id_examen_paciente = $("#id_examen_paciente").val();
                var wconsecutivo = $("#wconsecutivo").val();

                confirmarActivarInactivar(id_examen_paciente, wconsecutivo, 'on');
            });

            btn_imprimir_examen.click(function(){
                var id_examen_paciente = $("#id_examen_paciente").val();
                var wconsecutivo = $("#wconsecutivo").val();

                if(id_examen_paciente != '')
                {
                    $("#caja_campos_flotantes").hide();
                    generarTextoImprimir(id_examen_paciente, wconsecutivo);
                }
                else
                {
                    jAlert("Primero debe guardar la transcripción para imprimir", "Mensaje");
                }
            });

            btngenerar_pdf.click(function(){
                var id_examen_paciente = $("#id_examen_paciente").val();
                var wconsecutivo = $("#wconsecutivo").val();
                generarArchivoPdfMostrar(id_examen_paciente, wconsecutivo);
            });

            btncancelar_imprimir.click(function(){
                $("#div_imprimir_examen").hide("slide", { direction: "up" }, 300, function(){
                    $("#div_formulario_detalle").show("slide", { direction: "left" }, 300,function(){
                        $("#caja_campos_flotantes").show();
                    });
                });

                for(name in CKEDITOR.instances)
                {
                    CKEDITOR.instances[name].destroy();
                    // CKEDITOR.remove(CKEDITOR.instances[name]);
                }
                inicarCamposMemo('');
            });
        }

        function confirmarActivarInactivar(id_examen_paciente, wconsecutivo, estado_reg_activo)
        {
            // $("#div_confirmar_inactivar").val("");
            // fnModalLoading();
            var msj_estado_reg= '<span style="font-weight:bold;">Con esta opción se inactivará el examen, no será posible imprimir ni realizar modificaciones.</span>'+
                                '<br>'+
                                '<br>'+
                                '<span style="font-weight:bold;">¿Desea continuar con la operación?</span>';
            if(estado_reg_activo == 'on')
            {
                msj_estado_reg= '<span style="font-weight:bold;">Con esta opción se activará el examen.</span>'+
                                '<br>'+
                                '<br>'+
                                '<span style="font-weight:bold;">¿Desea continuar con la operación?</span>';
            }

            $("#div_confirmar_inactivar" ).html(msj_estado_reg);

            $("#div_confirmar_inactivar" ).dialog({
                "closeOnEscape": false,
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 220,
                // maxHeight: 400,
                width:  'auto',//800,
                buttons: {
                    "Aceptar": function() {
                        // console.log($("#firma_medico").val());
                        // console.log(cod_medico);
                        var obJson                   = parametrosComunes();
                        obJson['accion']             = 'update';
                        obJson['form']               = 'inactivar_examen';
                        obJson['estado_reg_activo']  = estado_reg_activo;
                        obJson['id_examen_paciente'] = id_examen_paciente;

                        $(".bloquear_todo").attr("disabled","disabled");
                        fnModalLoading();

                        $.post("transcripcion.php", obJson,
                            function(data){
                                if(data.error == 1)
                                {
                                    fnModalLoading_Cerrar();
                                    jAlert(data.mensaje, "Mensaje");
                                }
                                else
                                {
                                    $("#estado_reg_activo").val(estado_reg_activo);
                                }
                                return data;
                        },"json").done(function(data){
                                fnModalLoading_Cerrar();
                                if(data.error == 0)
                                {
                                    $("#div_examen_detalle").find(".spn_estado_examen").attr("esta_activo","off");

                                    $("#btn_ver_examen_"+id_examen_paciente).trigger("click");
                                    // pintarEstadoExamen('', 'div_examen_detalle');
                                    // // deshabilitarEdicion('div_examen_detalle', estado_reg_activo);
                                    // // deshabilitarEdicion('caja_campos_flotantes', estado_reg_activo);
                                    consultarExamenesPaciente('on', $("#arr_encabezado_b64").val());
                                }
                                $("#div_confirmar_inactivar").dialog("close");
                        }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
                    },
                    "Cancelar": function() {
                      $( this ).dialog( "close" );
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Activar/Inactivar examen",
                beforeClose: function( event, ui ) {
                    //$(".bloquear_todo").removeAttr("disabled");
                },
                create: function() {
                   // $(this).closest('.ui-dialog').on('keydown', function(ev) {
                   //     if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                   //         $("#div_seleccionar_paciente" ).dialog('close');
                   //     }
                   // });
                }
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        function inicarCamposMemo(div_contenedor_examen)
        {
            if(ckeditor_ok == true)
            {
                // console.log(div_contenedor_examen);
                if(div_contenedor_examen != 'div_examen_detalle_modal')
                {
                    var selector_memos = (div_contenedor_examen != '') ? $("#"+div_contenedor_examen).find('textarea.css_memo') : $('textarea.css_memo');
                    selector_memos.ckeditor({ customConfig: '../../ayucni/configcke_transcripcion_cni_memo.js?v='+Math.random(), timestamp : 'something_random' }); // js en include/ayucni
                    selector_memos.each(function(){
                        var id_memo = $(this).attr("id");
                        // console.log(id_memo);
                        CKEDITOR.instances[id_memo].on("instanceReady", function (e) {
                            // $("#cke_" + id_memo + " [id$='_bottom']").hide();
                            $("#cke_" + id_memo + " [id$='_top']").hide();
                            this.on("focus", function () {
                                $("#cke_" + id_memo + " [id$='_top']").show();
                                // $("#cke_" + id_memo + " [id$='_bottom']").show();
                            });
                            this.on("blur", function () {
                                $("#cke_" + id_memo + " [id$='_top']").hide();
                                // $("#cke_" + id_memo + " [id$='_bottom']").hide();
                            });

                            // this.keystrokeHandler.keystrokes[ CKEDITOR.CTRL + CKEDITOR.SHIFT + 77 ] = 'maximize'; //CTRL+SHIFT

                            // var writer = this.dataProcessor.writer;

                            // // The character sequence to use for every indentation step.
                            // writer.indentationChars = '\t';

                            // // The way to close self closing tags, like <br />.
                            // writer.selfClosingEnd = ' />';

                            // // The character sequence to be used for line breaks.
                            // writer.lineBreakChars = '\r\n';

                            // // The writing rules for the <p> tag.
                            // writer.setRules( 'table,tr,th,td,span,strong',
                            //     {
                            //         // Indicates that this tag causes indentation on line breaks inside of it.
                            //         indent : true,
                            //         // Inserts a line break before the <p> opening tag.
                            //         // breakBeforeOpen : true,
                            //         // // Inserts a line break after the <p> opening tag.
                            //         // breakAfterOpen : true,
                            //         // // Inserts a line break before the </p> closing tag.
                            //         // breakBeforeClose : false,
                            //         // // Inserts a line break after the </p> closing tag.
                            //         // breakAfterClose : true
                            //     });

                            // this.dataProcessor.writer.indentationChars = '\t';
                            // this.dataProcessor.writer.lineBreakChars = '\n';

                            // this.dataProcessor.writer.setRules('p', {
                            //         indent          : false,
                            //         breakBeforeOpen : false,
                            //         breakAfterOpen  : false,
                            //         breakBeforeClose: false,
                            //         breakAfterClose : false
                            // });
                        });
                        CKEDITOR.instances[id_memo].updateElement();
                    });
                }
                else
                {
                    var selector_memos = (div_contenedor_examen != '') ? $("#"+div_contenedor_examen).find('textarea.css_memo') : $('textarea.css_memo');
                    selector_memos.ckeditor({ customConfig: '../../ayucni/configcke_transcripcion_cni_memo.js?v='+Math.random(),readOnly : true, timestamp : 'something_random' }); // js en include/ayucni
                }
            }
            else
            {
                var txt_memo = (div_contenedor_examen != '') ? $("#"+div_contenedor_examen).find(".css_memo") : $(".css_memo");
                txt_memo.dblclick(function() {
                    // txt_memo.toggleClass( "memoCss" );1
                    var txt_this = $(this);

                    if(txt_this.hasClass("memoCss"))
                    {
                        // txt_this.attr({rows:15, cols: 50});
                        txt_this.removeClass("memoCss")
                    }
                    else
                    {
                        // txt_this.attr({rows:4, cols: 20});//width: 580px; height: 60px;
                        txt_this.addClass("memoCss")
                    }
                    // console.log(txt_this);
                });
            }
        }

        function generarTextoImprimir(id_examen_paciente, wconsecutivo)
        {
            var arr_campos_examen_html = eval('(' + $("#arr_campos_examen_html").val() + ')');

            $("#div_vista_previa_contenedor").hide();
            $("#div_contenedor_pdf").empty();

            var obJson                      = parametrosComunes();
            obJson['accion']                = 'load';
            obJson['form']                  = 'texto_imprimir';
            obJson['id_examen_paciente']    = id_examen_paciente;
            obJson['wconsecutivo']          = wconsecutivo;
            obJson['wcodigo_examen']        = $("#wcodigo_examen").val();
            obJson['arr_encabezado']        = $("#arr_encabezado_b64").val();
            obJson['arr_encabezado_examen'] = $("#arr_encabezado_examen").val();
            obJson['arr_campos_examen']     = arr_campos_examen_html;
            obJson['texto_cleditor']        = "on";
            obJson['ckeditor_ok_ayucni']    = $("#ckeditor_ok_ayucni").val();
            $(".bloquear_todo").attr("disabled","disabled");
            fnModalLoading();

            $.post("transcripcion.php", obJson,
                function(data){
                    if(data.error == 1)
                    {
                        fnModalLoading_Cerrar();
                        jAlert(data.mensaje, "Mensaje");
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    else
                    {
                        usuario_trancribe = data.usuario_trancribe;
                        usuario_modifica  = data.usuario_modifica;

                        fnModalLoading_Cerrar();
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    return data;
            },"json").done(function(data){
                if(data.westado_estudio == 'off')
                {
                    $("#div_formulario_detalle").hide("slide", { direction: "left" }, 300, function(){
                        $("#div_imprimir_examen").show("slide", { direction: "up" }, 300,function(){
                            if(ckeditor_ok == true)
                            {
                                // txt_normal = txt_normal.replace(/[\t\n]/gi,"<br>");
                                // CKEDITOR.instances["txt_resultado_examen"].setData(data.html);
                                $("#txt_resultado_examen").val(data.html);
                                inicializarCleditor($("#div_imprimir_examen"));
                                CKEDITOR.instances["txt_resultado_examen"].updateElement();
                            }
                            else
                            {
                                $("#txt_resultado_examen").val(data.html);
                                inicializarCleditor($("#div_imprimir_examen"));
                            }
                        });
                    });
                }
                else
                {
                    jAlert("El examen no ha sido cerrado o fue reabierto, no es posible imprimir en este momento.", "Mensaje");
                }
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        function validarGuardarExamen(id_examen_paciente, wconsecutivo, westado_estudio)
        {
            $(".campoRequerido").removeClass("campoRequerido");
            $("div[id^=cke_] iframe").contents().find(".cke_editable").css("background-color","");
            $(".divs_requerido").remove();

            var arr_campos_examen = eval('(' + $("#arr_campos_examen").val() + ')');

            // Este array tiene menos campos que su original arr_campos_examen, para evitar problemas al enviar este array por POST, pues la cantidad variables del POST puede estar reducida a 1000 en php.ini
            var arr_campos_examen_html = eval('(' + $("#arr_campos_examen_html").val() + ')');

            // console.log(arr_campos_examen);
            var arr_campos_examen_fn = arr_campos_examen;
            var todo_ok = true;
            for(var wcod_campo in arr_campos_examen_fn)
            {
                var info_campo = arr_campos_examen_fn[wcod_campo];
                // console.log(info_campo);
                var tip_campo = info_campo['tip_campo'];
                var campo_form = $("#"+wcod_campo);
                switch (tip_campo.toLowerCase()) {
                    case 'archivo':
                    case 'encabezado':
                    case 'pie_de_pagina':
                            // console.log(wcod_campo);
                            if(info_campo['campo_requerido'] == 'on' && campo_form.attr("codigo") == '')
                            {
                                $("#"+wcod_campo).addClass("campoRequerido");
                                todo_ok = false;
                            }
                            else
                            {
                                arr_campos_examen_html[wcod_campo]["valor_guardado"] = campo_form.attr("codigo");
                            }
                            // console.log(wcod_campo+">: "+arr_campos_examen_html[wcod_campo]["valor_guardado"]);
                        break;

                    case 'boleano':
                            var check = (campo_form.attr("checked") == 'checked') ? 'on': 'off';
                            arr_campos_examen_html[wcod_campo]["valor_guardado"] = check;
                        break;

                    case 'conjunto':
                        break;

                    case 'formula':
                            var valor_calculado = (campo_form.attr("valor_calculado") == '') ? "" : campo_form.attr("valor_calculado");
                            arr_campos_examen_html[wcod_campo]["valor_guardado"] = valor_calculado;
                        break;

                    case 'memo':
                            var valor_campo = campo_form.val().replace(/ /gi, "");
                            if(ckeditor_ok == true && $("#cke_"+wcod_campo).length > 0)
                            {
                                valor_campo = CKEDITOR.instances[wcod_campo].getData();
                            }

                            if(info_campo['campo_requerido'] == 'on' && valor_campo == '')
                            {
                                if(ckeditor_ok == true && $("#cke_"+wcod_campo).length > 0)
                                {
                                    $("#cke_"+wcod_campo+" iframe").contents().find(".cke_editable").css("background-color","lightyellow"); // Si tiene ckeditor
                                }
                                else
                                {
                                    $("#"+wcod_campo).addClass("campoRequerido");
                                }
                                todo_ok = false;
                            }
                            else
                            {
                                valor_campo = (campo_form.val().replace(/ /gi, "") == "") ? "": campo_form.val(); // en el caso que no sea requerido e intenten guardar solo espacios, se quitan los espacios.
                                if(ckeditor_ok == true && $("#cke_"+wcod_campo).length > 0)
                                {
                                    valor_campo = CKEDITOR.instances[wcod_campo].getData();
                                    if(valor_campo.split("<br").length >= 1)
                                    {
                                        valor_campo = valor_campo.replace(/(?:\r\n|\n)/gi, ''); //(/(?:\r\n|\r|\n)/gi, '')
                                    }
                                }
                                arr_campos_examen_html[wcod_campo]["valor_guardado"] = valor_campo;
                            }
                        break;

                    case 'multiseleccion':
                            var codigos_seleccion = '';
                            var coma_ = "";
                            $("#"+wcod_campo).find("[id^="+wcod_campo+"_]").each(function(){
                                var codigo_seleccionado = $(this).attr("codigo");
                                codigos_seleccion = codigos_seleccion+coma_+codigo_seleccionado;
                                coma_ = ',';
                            });

                            if(info_campo['campo_requerido'] == 'on' && codigos_seleccion == '')
                            {
                                $("#"+wcod_campo).addClass("campoRequerido").html('<div class="divs_requerido" style="width:100%;">&nbsp</div>');
                                todo_ok = false;
                            }
                            else
                            {
                                arr_campos_examen_html[wcod_campo]["valor_guardado"] = codigos_seleccion;
                            }
                        break;

                    case 'numero':
                            var valor_campo = campo_form.val().replace(/ /gi, "");
                            if(info_campo['campo_requerido'] == 'on' && valor_campo == '')
                            {
                                $("#"+wcod_campo).addClass("campoRequerido");
                                todo_ok = false;
                            }
                            else
                            {
                                valor_campo = (valor_campo == '') ? "": valor_campo;
                                arr_campos_examen_html[wcod_campo]["valor_guardado"] = valor_campo;
                            }
                        break;

                    case 'texto':
                        break;

                    case 'titulo':
                        break;

                    case 'normal':
                            var valor_campo = campo_form.val().replace(/ /gi, "");
                            if(ckeditor_ok == true && $("#cke_"+wcod_campo).length > 0)
                            {
                                valor_campo = CKEDITOR.instances[wcod_campo].getData();
                                //valor_campo = valor_campo.replace(/[\n]/gi,"");
                            }
                            // console.log(valor_campo.search(/[<br]/gi));
                            // valor_campo = valor_campo.replace(/(?:\r\n|\r|\n)/gi, '');
                            // valor_campo = valor_campo.replace(/[<br>]|[<br />]/gi, '');

                            if(info_campo['campo_requerido'] == 'on' && valor_campo == '')
                            {
                                if(ckeditor_ok == true && $("#cke_"+wcod_campo).length > 0)
                                {
                                    $("#cke_"+wcod_campo+" iframe").contents().find(".cke_editable").css("background-color","lightyellow"); // Si tiene ckeditor
                                }
                                else
                                {
                                    $("#"+wcod_campo).addClass("campoRequerido");
                                }
                                todo_ok = false;
                            }
                            else
                            {
                                valor_campo = (campo_form.val().replace(/ /gi, "") == "") ? "": campo_form.val(); // en el caso que no sea requerido e intenten guardar solo espacios, se quitan los espacios.
                                if(ckeditor_ok == true && $("#cke_"+wcod_campo).length > 0)
                                {
                                    valor_campo = CKEDITOR.instances[wcod_campo].getData();
                                    if(valor_campo.split("<br").length >= 1)
                                    {
                                        valor_campo = valor_campo.replace(/(?:\r\n|\n)/gi, ''); //(/(?:\r\n|\r|\n)/gi, '')
                                    }
                                }
                                arr_campos_examen_html[wcod_campo]["valor_guardado"] = valor_campo;
                            }
                            // console.log(wcod_campo+">: "+arr_campos_examen_html[wcod_campo]["valor_guardado"]);
                        break;

                    default:
                        //# code...
                        break;
                }
            }

            if(westado_estudio == 'on')
            {
                todo_ok = true;
            }

            if(todo_ok) //todo_ok
            {
                var habilitar_cerrar_con_firma = false; // Se pidió por parte de cardiología no pedir la firma del médico para cerrar un examen pues los médicos no
                                                        // van a revisar la transcripción y no se permitiría cerrar e imprimir (sería un cuello de botella)
                //Si ingresó aqui es porque la variable westado_estudio=off y toda la validación estuvo OK.
                //incluso asi no se debe cambiar el estado westado_estudio a off en la base de datos hasta que se ingrese la clave le médico y sea correcta,
                //en ese caso se debe cambiar el estado westado_estudio a off en la BD y guardar el código del médico que firmó.
                var pedir_firma_medico = false;
                if(westado_estudio == 'off' && habilitar_cerrar_con_firma)
                {
                    westado_estudio = 'on'; // El examen debe continuar activo hasta que se ingrese una clave correcta del médico, en ese momento si se debe actualizar el estado a off.
                                            // Esta instrucción en off inicialmente sirve para saber en que momento precionaron el boton "cerrar examen" y pedir la firma del médico.
                                            // Se cambia el estado a on para permitir que se guarden los datos del formulario y no se pierdan las posibles modificaciones, pero sin cerrar el examen hasta la validación de la firma.
                    pedir_firma_medico = true;
                }

                // var arr_campos_examen_html_json  = $.toJSON(arr_campos_examen_html);
                var obJson                      = parametrosComunes();
                obJson['accion']                = 'update';
                obJson['form']                  = 'guardar_datos_examen';
                obJson['id_examen_paciente']    = id_examen_paciente;
                obJson['wconsecutivo']          = wconsecutivo;
                obJson['wcodigo_examen']        = $("#wcodigo_examen").val();
                obJson['wubicacion_digital']    = $("#wubicacion_digital").val();
                obJson['wequipo']               = $("#wequipo").attr("codigo");
                obJson['arr_encabezado']        = $("#arr_encabezado_b64").val();
                obJson['arr_encabezado_examen'] = $("#arr_encabezado_examen").val();
                obJson['arr_campos_examen']     = arr_campos_examen_html;
                obJson['westado_estudio']       = westado_estudio;
				
				obJson['ccosto']                = $("#ccosto").val();//Modificacion 6 de Marzo 2019,
				obJson['concepto']              = $("#concepto").val();//Modificacion 6 de Marzo 2019,
				
                $(".bloquear_todo").attr("disabled","disabled");
                fnModalLoading();

                $.post("transcripcion.php", obJson,
                    function(data){
                        if(data.error == 1)
                        {
                            fnModalLoading_Cerrar();
                            jAlert(data.mensaje, "Mensaje");
                            $(".bloquear_todo").removeAttr("disabled");
                        }
                        else
                        {
                            if(id_examen_paciente == '')
                            {
                                $("#id_examen_paciente").val(data.id_examen_paciente);
                            }

                            $("#westado_estudio").val(westado_estudio);

                            if(wconsecutivo == '')
                            {
                                $("#wconsecutivo").val(data.wconsecutivo);
                                $("#spn_wconsecutivo").html(data.wconsecutivo);
                                $("#spn_fecha_estudio").html('<span style="font-size:8pt;">['+data.spn_fecha_estudio+']</span>');
                            }
                            fnModalLoading_Cerrar();
                            $(".bloquear_todo").removeAttr("disabled");

                            if(!pedir_firma_medico)
                            {
                                jAlert(data.mensaje, "Mensaje");
                            }
                        }
                        return data;
                },"json").done(function(data){
                    if(data.error != 1)
                    {
                        pintarEstadoExamen('', 'div_examen_detalle')
                        deshabilitarEdicion('div_examen_detalle', westado_estudio, '');
                        deshabilitarEdicion('caja_campos_flotantes', westado_estudio, '');

                        consultarExamenesPaciente('on', $("#arr_encabezado_b64").val());

                        if(pedir_firma_medico)
                        {
                            validarFirmaMedico(id_examen_paciente, wconsecutivo);
                        }
                    }
                }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
            }
            else
            {
                var msjalert = '<div style="font-size:10pt;">Campos obligatorios sin diligenciar<br>verifique los campos resaltados <span class="campoRequerido">_____</span></div>';
                jAlert(msjalert, "Mensaje");
            }
        }

        function validarFirmaMedico(id_examen_paciente, wconsecutivo)
        {
            var westado_estudio = 'off';
            var cod_medico      = $(":input[cod_tabla=CARDIOLOGOS]").first().attr("codigo");
            var nombre_medico   = $(":input[cod_tabla=CARDIOLOGOS]").first().attr("nombre");
            $("#div_msj_firma_err").html("");
            $("#spn_medico_cierre_examen").html(nombre_medico);
            $("#firma_medico").val("");

            $("#div_firma_medico" ).dialog({
                "closeOnEscape": false,
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 220,
                // maxHeight: 400,
                width:  'auto',//800,
                buttons: {
                    "Cancelar": function() {
                      $( this ).dialog( "close" );
                    },
                    "Guardar": function() {
                        var firma_medico = $("#firma_medico").val();

                        // console.log($("#firma_medico").val());
                        // console.log(cod_medico);
                        if(firma_medico.replace(/ /gi, " ") == '')
                        {
                            $("#div_msj_firma_err").html("La clave está vacía");
                        }
                        else
                        {
                            // return false;

                            var obJson                   = parametrosComunes();
                            obJson['accion']             = 'update';
                            obJson['form']               = 'validar_medico';
                            obJson['cod_medico']         = cod_medico;
                            obJson['firma_medico']       = firma_medico;
                            obJson['id_examen_paciente'] = id_examen_paciente;
                            obJson['westado_estudio']    = westado_estudio;

				//obJson['ccosto']                = $("#ccosto").val();;//Modificacion 6 de Marzo 2019,
				//obJson['concepto']              = $("#concepto").val();;//Modificacion 6 de Marzo 2019,

							
                            $(".bloquear_todo").attr("disabled","disabled");
                            fnModalLoading();

                            $.post("transcripcion.php", obJson,
                                function(data){
                                    if(data.error == 1)
                                    {
                                        fnModalLoading_Cerrar();
                                        jAlert(data.mensaje, "Mensaje");
                                    }
                                    else
                                    {
                                        if(data.error == 2)
                                        {
                                            $("#div_msj_firma_err").html(data.mensaje);
                                        }
                                    }
                                    return data;
                            },"json").done(function(data){
                                    $("#firma_medico").val("");
                                    fnModalLoading_Cerrar();
                                    if(data.error == 0)
                                    {
                                        $("#div_firma_medico").dialog("close");

                                        pintarEstadoExamen('', 'div_examen_detalle');
                                        deshabilitarEdicion('div_examen_detalle', westado_estudio, '');
                                        deshabilitarEdicion('caja_campos_flotantes', westado_estudio, '');
                                        consultarExamenesPaciente('on', $("#arr_encabezado_b64").val());
                                    }
                            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
                        }
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Firma electrónica especialista",
                beforeClose: function( event, ui ) {
                    //$(".bloquear_todo").removeAttr("disabled");
                },
                create: function() {
                   // $(this).closest('.ui-dialog').on('keydown', function(ev) {
                   //     if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                   //         $("#div_seleccionar_paciente" ).dialog('close');
                   //     }
                   // });
                }
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        function reiniciarCamposSoloNumericos()
        {
            $('.number-only').bind('keyup', function(e) {
                if(this.value!='-')
                  while(isNaN(this.value))
                    this.value = this.value.split('').reverse().join('').replace(/[\D]/i,'')
                                           .split('').reverse().join('');
            })
            .on("cut copy paste",function(e){
                e.preventDefault();
            });
        }

        function reinicarAcordeonExamenesPaciente()
        {
            $("#accordionExamenesPaciente").accordion({
                collapsible: true,
                heightStyle: "content"
            });
        }

        function reinicarAcordeonDetalleExamenPaciente()
        {
            $("#accordionExameneDetalle").accordion({
                collapsible: true,
                heightStyle: "content"
            });
        }

        function fnModalSeleccionarPaciente()
        {
            $("#div_seleccionar_paciente" ).dialog({
                "closeOnEscape": false,
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 500,
                // maxHeight: 400,
                width:  'auto',//800,
                buttons: {
                    "Cerrar": function() {
                      $( this ).dialog( "close" );
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Elegir una opción para continuar",
                beforeClose: function( event, ui ) {
                    $(".bloquear_todo").removeAttr("disabled");
                },
                create: function() {
                   $(this).closest('.ui-dialog').on('keydown', function(ev) {
                       if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                           $("#div_seleccionar_paciente" ).dialog('close');
                       }
                   });
                }
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        /**
         * [fnModalLoading: Es función se encarga de mostrar una ventana modal cada vez que se hace un llamado ajax con el fin de bloquear la página web hasta que se
         *                    se genere una respuesta y evitar que el usuario genere más eventos (click) sin terminar la petición anterior y evitar problemas
         *                    en la veracidad de datos]
         * @return {[type]} [description]
         */
        function fnModalLoading()
        {
            $( "#div_loading" ).dialog({
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 'auto',
                // maxHeight: 600,
                width:  'auto',//800,
                // buttons: {
                //     "Cerrar": function() {
                //       $( this ).dialog( "close" );
                //     }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Consultando ...",
                beforeClose: function( event, ui ) {
                    //
                },
                create: function() {
                   $(this).closest('.ui-dialog').on('keydown', function(ev) {
                       if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                           $( "#div_loading" ).dialog('close');
                       }
                   });
                },
                "closeOnEscape": false,
                "closeX": false
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        function fnModalSeleccionarPaciente_Cerrar()
        {
            if($("#div_seleccionar_paciente").is(":visible"))
            {
                $("#div_seleccionar_paciente").dialog('close');
            }
        }

        /**
         * [fnModalLoading_Cerrar: complemento a la función fnModalLoading, esta se encarga de cerrar la ventana modal]
         * @return {[type]} [description]
         */
        function fnModalLoading_Cerrar()
        {
            if($("#div_loading").is(":visible"))
            {
                $("#div_loading").dialog('close');
            }
        }

        /**
         * [limpiarEncabezado: Limpia de la pantalla todos los datos cargados para un paciente, datos personales, exámenes y detalle de examen cargados en pantalla]
         * @return {[type]} [description]
         */
        function limpiarEncabezado()
        {
            $(".limpiar").each(function(index, value) {
                var index = $(this).attr("id");
                if ($("#"+index).length > 0)
                {
                    if($("#"+index).is("input,select") && $("#"+index).attr("type") != 'checkbox' && $("#"+index).attr('multiple') == undefined) // Si es input o select entonces escribe en un campo u opci? de un select sino escribe en html.
                    {
                        $("#"+index).val("");
                    }
                    else if($("#"+index).attr("type") == 'checkbox')
                    {
                        if($("#"+index).attr("checked") == 'checked') { $("#"+index).removeAttr("checked"); }
                    }
                    else if($("#"+index).attr('multiple') != undefined)
                    {
                        $("#"+index+" option").each(function(){
                                $(this).removeAttr("selected");
                        });
                    }
                    else
                    { $("#"+index).html("-"); }

                    if($("#"+index).is("codigo")){
                        $("#"+index).attr("codigo","");
                        $("#"+index).attr("nombre","");
                    }
                }
            });
            $("#div_examenes_paciente").html("");
            $("#div_examen_detalle").html("");
            $("#caja_campos_flotantes").hide("");
            $("#cont_caja_flotante_query").html("");
            $('html head').find('title').text("Transcripción");
            $('#spn_nombre_paciente').html("");
        }

        var arr_permisos_usuario = new Array(); // Variable que guarda de forma global en javascript los datos de permisos sobre el usuario que ingresó al programa
                                                // Este arreglo es compartido con los llamados ajax para controlar permisos tambien sobre secciones de código generadas en
                                                // el servidor (PHP).
        /**
         * [buscarPacienteJS: Según los datos digitados en el encabezado de datos del paciente, el programa genera una búsqueda por medio de ajax en la base de datos
         *                     para encontrar resultados]
         * @param  {[type]} whistoria  [description]
         * @param  {[type]} wingreso   [description]
         * @param  {[type]} wdocumento [description]
         * @param  {[type]} wnombre1   [description]
         * @param  {[type]} wnombre2   [description]
         * @param  {[type]} wapellido1 [description]
         * @param  {[type]} wapellido2 [description]
         * @return {[type]}            [description]
         */
        function buscarPacienteJS(whistoria, wingreso, wdocumento, wnombre1, wnombre2, wapellido1, wapellido2)
        {
            var obJson           = parametrosComunes();
            obJson['accion']     = 'load';
            obJson['form']       = 'buscar_paciente';
            obJson['whistoria']  = whistoria;
            obJson['wingreso']   = wingreso;
            obJson['wdocumento'] = wdocumento;
            obJson['wnombre1']   = wnombre1;
            obJson['wnombre2']   = wnombre2;
            obJson['wapellido1'] = wapellido1;
            obJson['wapellido2'] = wapellido2;
            obJson['codigoempresaparticular'] = $("#codigoempresaparticular").val();

			obJson['ccosto']                = $("#ccosto").val();//Modificacion 6 de Marzo 2019,
			obJson['concepto']              = $("#concepto").val();//Modificacion 6 de Marzo 2019,

            $(".bloquear_todo").attr("disabled","disabled");
            fnModalLoading();

            $.post("transcripcion.php", obJson,
                function(data){
                    if(data.error == 1)
                    {
                        fnModalLoading_Cerrar();
                        fnModalSeleccionarPaciente_Cerrar();
                        jAlert(data.mensaje, "Mensaje");
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    else
                    {
                        if(data.flujo_consulta == 'cargar_ingresos')
                        {
                            $("#div_seleccionar_paciente").html(data.html);
                        }
                        else if(data.flujo_consulta == 'cargar_lista_pacientes')
                        {
                            $("#div_seleccionar_paciente").html(data.html);
                        }
                        else
                        {
                            $("#arr_encabezado_b64").val(data.arr_encabezado_b64);
                            setearCamposHtml(data.arr_encabezado);
                            if(data.arr_encabezado['whistoria'] != '')
                            {
                                consultarExamenesPaciente('off', data.arr_encabezado);
                            }
                            fnModalSeleccionarPaciente_Cerrar();
                            fnModalLoading_Cerrar();
                            $(".bloquear_todo").removeAttr("disabled");
                        }

                        arr_permisos_usuario = data.arr_permisos_usuario;
                        // console.log("arr_permisos_usuario: ");
                        // console.log(arr_permisos_usuario);

                        var nombres_pac = data.arr_encabezado['wtipodoc']+"-"+data.arr_encabezado['wdocumento']+", "+data.arr_encabezado['wnombre1']+" "+data.arr_encabezado['wnombre2']+" "+data.arr_encabezado['wapellido1']+" "+data.arr_encabezado['wapellido2'];
                        nombres_pac = nombres_pac.replace(/ /gi, " ");
                        $('html head').find('title').text(nombres_pac);
                        $('#spn_nombre_paciente').html(nombres_pac);
                    }
                    return data;
            },"json").done(function(data){

                //Esta sección va aquí porque por momento no se alcanza a setear el html en el div y al iniciar
                //el dialog no encuentra aun listo el html para calcular el alto de la modal.
                if(data.flujo_consulta == 'cargar_ingresos')
                {
                    fnModalLoading_Cerrar();
                    fnModalSeleccionarPaciente();
                }
                else if(data.flujo_consulta == 'cargar_lista_pacientes')
                {
                    fnModalLoading_Cerrar();
                    fnModalSeleccionarPaciente();
                }
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        function btn_consultarDatos()
        {
            var whistoria  = $("#whistoria").val();
            var wingreso   = $("#wingreso").val();
            var wdocumento = $("#wdocumento").val();
            var wnombre1   = $("#wnombre1").val();
            var wnombre2   = $("#wnombre2").val();
            var wapellido1 = $("#wapellido1").val();
            var wapellido2 = $("#wapellido2").val();

            var concatena = whistoria+wingreso+wdocumento+wnombre1+wnombre2+wapellido1+wapellido2;

            if(whistoria.replace(/ /gi, "") == '' && wingreso.replace(/ /gi, "") != '')
            {
                jAlert("Si escribe número de ingreso debe escribir un número de historia","Alerta");
                return;
            }

            if(concatena.replace(/ /gi, "") == '')
            {
                jAlert("No hay datos de búsqueda","Alerta");
                return;
            }

            buscarPacienteJS(whistoria, wingreso, wdocumento, wnombre1, wnombre2, wapellido1, wapellido2);
        }

        function buscarHistoriaIngresoJS(whistoria, wingreso)
        {
            buscarPacienteJS(whistoria, wingreso, '', '', '', '', '');
        }

        function filtrarPacienteJS(whistoria)
        {
            buscarPacienteJS(whistoria, '', '', '', '', '', '');
        }

        function consultarExamenesPaciente(grabado, arr_datos_paciente)
        {
            var obJson                   = parametrosComunes();
            obJson['accion']             = 'load';
            obJson['form']               = 'buscar_examenes_paciente';
            obJson['arr_datos_paciente'] = arr_datos_paciente;

            // console.log(obJson);
            $(".bloquear_todo").attr("disabled","disabled");
            fnModalLoading();

            $.post("transcripcion.php", obJson,
                function(data){
                    if(data.error == 1)
                    {
                        fnModalLoading_Cerrar();
                        jAlert(data.mensaje, "Mensaje");
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    else
                    {
                        $("#div_examenes_paciente").html(data.html);
                        fnModalLoading_Cerrar();
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    return data;
            },"json").done(function(data){
                reinicarAcordeonExamenesPaciente();
                $('input#id_search_examenes_guardados').quicksearch('#tabla_examanes_realizados .find');
                if(arr_permisos_usuario['ver_consultar'] == 'off')
                {
                    $(".btn_ver_examen").attr("disabled","disabled");
                }

                if(arr_permisos_usuario['grabar'] == 'off')
                {
                    $(".div_crear_nuevo_examen").find("input").attr("disabled","disabled");
                }

                if($("#id_examen_paciente").length > 0 && $("#id_examen_paciente").val() == '')
                {
                    var msj_elim = "Detalle de examen nuevo sin guardar. ¿Desea continuar sin guardar?";
                    jConfirm(msj_elim, 'Solicitud de confirmación', function(r) {
                        if(r)
                        {
                            // var div = $("#"+wcod_campo_eliminar);
                            // div.css({color: 'red'});
                            // div.hide("slow",
                            //             function(){
                            //                 $(this).remove();
                            //             }
                            //         );
                            limpiarAreaDetalleExamen();
                        }
                    });
                }
                else
                {
                    if(grabado != 'on')
                    {
                        limpiarAreaDetalleExamen();
                    }
                }
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        function limpiarAreaDetalleExamen()
        {
            $("#div_examen_detalle").html("");
            $("#caja_campos_flotantes").hide("");
            $("#cont_caja_flotante_query").html("");
            // $('#spn_nombre_paciente').html("");
        }

        function cargarDetalleExamenPaciente(whistoria, wingreso, wcodigo_examen, wconsecutivo, id_examen_paciente)
        {
            var id_examen_paciente_ctrl = ($("#id_examen_paciente").length > 0) ? $("#id_examen_paciente").length : 0;
            var ver_en_modal = '';
            var div_contenedor_examen = "div_examen_detalle";

            // Significa que se está creando un nuevo estudio y no ha sido guardado. Si la variable id_examen_paciente que llega por parámetro tiene un valor
            // es porque se está intentando ver un examen sin haber guardado el nuevo que está en pantalla, en ese caso entonces el examen que se está intentando ver
            // se mostrará en una ventana modal sin sobreescribir el formulario que aún está sin guardar.
            if(id_examen_paciente_ctrl > 0 && (($("#id_examen_paciente").val() == '' && id_examen_paciente != '') || $("#div_examen_detalle").find(".spn_estado_examen").attr("esta_activo") == 'on'))
            {
                ver_en_modal = 'modal';
                div_contenedor_examen = "div_examen_detalle_modal";
            }

            var obJson                   = parametrosComunes();
            obJson['accion']             = 'load';
            obJson['form']               = 'cosultar_detalle_examen';
            obJson['whistoria']          = whistoria;
            obJson['wingreso']           = wingreso;
            obJson['arr_encabezado']     = $("#arr_encabezado_b64").val();
            obJson['wcodigo_examen']     = wcodigo_examen;
            obJson['wconsecutivo']       = wconsecutivo;
            obJson['id_examen_paciente'] = id_examen_paciente;
            obJson['ver_en_modal']       = ver_en_modal;
            obJson['ckeditor_ok_ayucni'] = $("#ckeditor_ok_ayucni").val();

            // console.log(obJson);
            $(".bloquear_todo").attr("disabled","disabled");
            fnModalLoading();

            $.post("transcripcion.php", obJson,
                function(data){
                    if(data.error == 1)
                    {
                        fnModalLoading_Cerrar();
                        jAlert(data.mensaje, "Mensaje");
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    else
                    {
                        if(div_contenedor_examen == 'div_examen_detalle_modal')
                        {
                            $("#div_examen_detalle_modal").html(data.html);
                            modalPrevisualizarOtroExamen();
                        }
                        else
                        {
                            $("#arr_encabezado_examen").val(data.arr_encabezado_examen);
                            $("#div_examen_detalle").html(data.html);
                            $("#cont_caja_flotante_query").html(data.html_campos_normales);
                            inicializarCamposFlotantes();
                        }
                        fnModalLoading_Cerrar();
                        $(".bloquear_todo").removeAttr("disabled");
                    }
                    return data;
            },"json").done(function(data){
                reiniciarCamposSoloNumericos();
                reinicarAcordeonDetalleExamenPaciente();

                if(id_examen_paciente == '')
                {
                    $("#estado_reg_activo"+ver_en_modal).val('on');
                }

                // Este código hace que al presionar la tecla enter o flecha hacia abajo cambie el focus del campo al siguiente,
                // si es fecha hacia arriba entonces se devuelva un campo.
                if(div_contenedor_examen != 'div_examen_detalle_modal')
                {
                    $("#"+div_contenedor_examen).find('.inputs').keydown(function (evt) {
                        var charCode = (evt.which) ? evt.which : event.keyCode;
                        var esAutocompletar = ($(this).hasClass("examen_autocomplete"));

                        // Si es un campo autocompletar no capturar el enter para cambiar de campo porque el enter
                        // ya está reservado por el autocompletar para seleccionar las opciones de la lista.
                        if(esAutocompletar && charCode == 13)
                        {
                            // No hacer nada.
                        }
                        else
                        {
                            if (charCode === 13 || charCode === 34) {
                                var index = $('.inputs').index(this) + 1;
                                $('.inputs').eq(index).focus();
                            }
                            else if (charCode === 33) {
                                var index = $('.inputs').index(this) - 1;
                                if(index >= 0)
                                {
                                   $('.inputs').eq(index).focus();
                                }
                            }
                        }
                    });

                    // En esta sección se inicializan todos los campos autocompletar de los campos select, multiseleccion con los array
                    // ocultos que contienen las opciones del autocompletar por cada campo.
                    var funciones_autocompletar = (data.error == 0) ? data.arr_funciones_autocompletar : new Array();
                    if(funciones_autocompletar.length > 0)
                    {
                        for (var key_idx in funciones_autocompletar)
                        {
                            // console.log(funciones_autocompletar[key_idx]);
                            eval(funciones_autocompletar[key_idx]);
                        }
                    }
                    iniciarInputsSize_acciones();
                    initAutocomplete();

                    // Esta función lo que hace es un autoscroll hasta donde esta el medicamento que se ha modificado la cantidad.
                    jQuery.fn.scrollTo = function(elem, speed) {
                        $(this).animate({
                            scrollTop: $(elem).offset().top - 80
                            // scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top
                        }, speed == undefined ? 1000 : speed);
                        return true;
                    };
                    // console.log($(".inputs").first());
                    $("html").scrollTo($(".inputs").first(), 1000);
                    $(".inputs").first().focus();
                }
                else
                {
                    inicarCamposMemo(div_contenedor_examen);
                }

                reiniciarTooltip();

                // // Luego de setear los campos numéricos, recalcular las formulas.
                recalcularFormulas("");


                // Despues de guardar la primera vez no se debe dejar editar el formulario.
                if(data.error != 1)
                {
                    var estado_reg_activo = $("#estado_reg_activo"+ver_en_modal).val();
                    pintarEstadoExamen(ver_en_modal, div_contenedor_examen);
                    deshabilitarEdicion(div_contenedor_examen, data.westado_estudio, estado_reg_activo);

                    if(div_contenedor_examen == "div_examen_detalle")
                    {
                        deshabilitarEdicion("caja_campos_flotantes", data.westado_estudio, estado_reg_activo);
                    }
                }

                if(arr_permisos_usuario['imprimir'] == 'off')
                {
                    $(".btn_imprimir_examen").attr("disabled","disabled");
                }
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        function modalPrevisualizarOtroExamen()
        {
            $("#div_examen_detalle_modal").dialog({
                position: 'top',
                show: {
                    // effect: "blind",
                    effect: "slide",
                    duration: 400 //100
                },
                hide: {
                    effect: "slide",
                    duration: 400 //100
                },
                height: '700',
                // maxHeight: 600,
                width:  'auto',//800,
                buttons: {
                    "Cerrar": function() {
                        $( this ).dialog( "close" );
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Vista previa examen",
                beforeClose: function( event, ui ) {
                    $("#div_examen_detalle_modal").html("");
                },
                create: function() {
                    $(this).closest('.ui-dialog').on('keydown', function(ev) {
                        if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                            $( "#div_examen_detalle_modal" ).dialog('close');
                        }
                    });
                },
                open: function(event, ui){
                    //
                },
                "closeOnEscape": false,
                "closeX": false
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }

        function deshabilitarEdicion(div_contenedor_examen, westado_estudio, estado_reg_activo)
        {
            if(div_contenedor_examen != 'div_examen_detalle_modal')
            {
                $("#txt_div_normal").find(".btn_guardar_examen").removeAttr("disabled");
            }

            if(westado_estudio == 'off' || estado_reg_activo == 'off' || div_contenedor_examen == 'div_examen_detalle_modal')
            {
                if(arr_permisos_usuario['modificar'] == 'off' || estado_reg_activo == 'off' || div_contenedor_examen == 'div_examen_detalle_modal')
                {
                    // No puede imprimir si no tiene permiso de imprimir o si el examen aún no esta cerrado definitivamente.
                    // if(arr_permisos_usuario['imprimir'] == 'off' || $("#westado_cerrado").val() == 'off')
                    if(arr_permisos_usuario['imprimir'] == 'off')
                    {
                        $("#"+div_contenedor_examen).find(".btn_imprimir_examen").attr("disabled","disabled");
                    }

                    // $(".btn_imprimir_examen").attr("disabled","disabled");
                    // $(".inputs:not(:button)").attr("disabled","disabled").css({"background":"#ffffff", "color":"#000000"}); // Todos los input menos los tipo boton
                    $("#"+div_contenedor_examen).find(".inputs:not(:button)").keydown(function( event ) {
                        return false;
                    });
                    $("#"+div_contenedor_examen).find(".inputs:button").attr("disabled","disabled");// Tipo boton

                    $("#"+div_contenedor_examen).find(".inputs:checkbox").attr("disabled","disabled");// Tipo checkbox
                    $("#"+div_contenedor_examen).find(".btn_guardar_examen").attr("disabled","disabled");//.css({"background":"#ffffff", "color":"#000000"});
                    $("#"+div_contenedor_examen).find(".btn_cerrar_examen").attr("disabled","disabled");
                    // $(".css_memo").attr("disabled","disabled").css({"background":"#ffffff", "color":"#000000"});

                    if(div_contenedor_examen != 'div_examen_detalle_modal')
                    {
                        $("#txt_div_normal").find(".btn_guardar_examen").attr("disabled","disabled");
                    }
                    // Textarea no permitir escribir nada, a cambio de no dejar el campo disabled
                    $("#"+div_contenedor_examen).find(".css_memo").keydown(function( event ) {
                        return false;
                    });

                    $("#"+div_contenedor_examen).find(".btn_eliminar_opc").hide();
                }

                if(arr_permisos_usuario['imprimir'] == 'on' && div_contenedor_examen != 'div_examen_detalle_modal')
                {
                    $("#"+div_contenedor_examen).find(".btn_imprimir_examen").removeAttr("disabled");
                }
            }
            else
            {
                $("#"+div_contenedor_examen).find(".btn_imprimir_examen").attr("disabled","disabled");
            }

            if(arr_permisos_usuario['borrar'] == 'on' && div_contenedor_examen != 'div_examen_detalle_modal')
            {
                $("#"+div_contenedor_examen).find(".btn_inactivar_examen").removeAttr("disabled");
                $("#"+div_contenedor_examen).find(".btn_activar_examen").removeAttr("disabled");
            }
            else
            {
                $("#"+div_contenedor_examen).find(".btn_inactivar_examen").attr("disabled","disabled");
                $("#"+div_contenedor_examen).find(".btn_activar_examen").attr("disabled","disabled");
            }
        }

        /**
         * [pintarEstadoExamen]
         * @param  {[type]} div_contenedor_examen [description]
         * @param  {[type]} ver_en_modal          [Indica si se estan viendos los campos de la ventana modal para diferenciarlos del formulario principal]
         * @param  {[type]} westado_estudio       [Estado del proceso, activo, cerrado-terminado]
         * @param  {[type]} estado_reg_activo     [Estado del registro en base de datos, on-off activo o inactivo]
         * @return {[type]}                       [description]
         */
        function pintarEstadoExamen(ver_en_modal, div_contenedor_examen, westado_estudio, estado_reg_activo)
        {
            var westado_estudio     = $("#westado_estudio"+ver_en_modal).val();
            var estado_reg_activo   = $("#estado_reg_activo"+ver_en_modal).val();

            if(estado_reg_activo == 'on')
            {
                $("#"+div_contenedor_examen).find(".btn_guardar_examen").show();
                $("#"+div_contenedor_examen).find(".btn_cerrar_examen").show();
                $("#"+div_contenedor_examen).find(".btn_activar_examen").hide();
                $("#"+div_contenedor_examen).find(".btn_inactivar_examen").show();
                $("#"+div_contenedor_examen).find(".btn_imprimir_examen").show();

                if(westado_estudio == 'off')
                {
                    $("#"+div_contenedor_examen).find(".spn_estado_examen").css({"color":"red"}).html("FINALIZADO");
                    $("#"+div_contenedor_examen).find(".spn_estado_examen").attr("esta_activo","off");
                }
                else if(westado_estudio == 'on')
                {
                    $("#"+div_contenedor_examen).find(".spn_estado_examen").css({"color":"green"}).html("ACTIVO");
                    $("#"+div_contenedor_examen).find(".spn_estado_examen").attr("esta_activo","on");
                }
                else
                {
                    $("#"+div_contenedor_examen).find(".spn_estado_examen").css({"color":"orange"}).html("SIN GUARDAR");
                    $("#"+div_contenedor_examen).find(".spn_estado_examen").attr("esta_activo","on");
                }
            }
            else
            {
                $("#"+div_contenedor_examen).find(".spn_estado_examen").css({"color":"black"}).html("INACTIVO");
                // $("#"+div_contenedor_examen).find(".spn_estado_examen").attr("esta_activo","off");

                $("#"+div_contenedor_examen).find(".btn_guardar_examen").hide();
                $("#"+div_contenedor_examen).find(".btn_cerrar_examen").hide();
                $("#"+div_contenedor_examen).find(".btn_activar_examen").show();
                $("#"+div_contenedor_examen).find(".btn_inactivar_examen").hide();
                $("#"+div_contenedor_examen).find(".btn_imprimir_examen").hide();
            }
        }

        function initAutocomplete()
        {
            $('.examen_autocomplete').on({
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
        }

        function crearNuevoExamenPaciente(whistoria ,wingreso ,campo_wnuevo_examen)
        {
			$("#btn_nuevo_examen").hide(0);
			
            var wnuevo_examen = $("#"+campo_wnuevo_examen).val();
            if(wnuevo_examen != '')
            {
                var id_examen_paciente_ctrl = ($("#id_examen_paciente").length > 0) ? $("#id_examen_paciente").length : 0;
                var westado_estudio = ($("#westado_estudio").length > 0) ? $("#westado_estudio").val() : '';
                var estado_reg_activo = ($("#estado_reg_activo").length > 0) ? $("#estado_reg_activo").val() : '';

                // Si hay un examen cargado y está en estado activo, pedir que se guarde y se preione botón consultar para iniciar la sección de transcripción.
                if(id_examen_paciente_ctrl > 0 && $("#id_examen_paciente").val() == '')
                {
                    // Si no se ha guardado la transcripción actual
                    jAlert("Debe guardar la transcripción actual para no perder los cambios, luego presionar el botón -Consultar- para inicar una nueva transcripción", "Mensaje");
					$("#btn_nuevo_examen").show(0);
				}
                else if(id_examen_paciente_ctrl > 0 && $("#id_examen_paciente").val() != '' && westado_estudio == 'on' && estado_reg_activo != 'off')
                {
                    // Si ya se había guardado la transcripción actual pero está en estado activo
                    jAlert("La transcripción actual en DETALLE EXAMEN esta en estado ACTIVO, guarde los cambios, luego presione el botón -Consultar- para inicar una nueva transcripción", "Mensaje");
					$("#btn_nuevo_examen").show(0);
				}
                else
                {
                    cargarDetalleExamenPaciente(whistoria, wingreso, wnuevo_examen, '', '');
                    $("#"+campo_wnuevo_examen).val("");
                    $("#btn_nuevo_examen").hide(500);
                }

            }
            else
            {
                jAlert("Debe seleccionar un nuevo examen para el paciente", "Mensaje");
				$("#btn_nuevo_examen").show(0);
            }
        }

        /**
         * [verOpcionesTranscripcion: Muestra una ventana modal para seleccionar uno de los textos preconfigurados para la transcripción]
         * @param  {[type]} wcod_campo     [description]
         * @param  {[type]} wcodigo_examen [description]
         * @return {[type]}                [description]
         */
        function verOpcionesTranscripcion(wcod_campo,wcodigo_examen, general)
        {
            var campo_txt = $("#"+wcod_campo).val();
            if(campo_txt.replace(/ /gi,"") == '')
            {
                var obJson                   = parametrosComunes();
                obJson['accion']             = 'load';
                obJson['form']               = 'cosultar_opciones_transcripcion';
                obJson['wcod_campo']         = wcod_campo;
                obJson['wcodigo_examen']     = wcodigo_examen;
                obJson['general']            = general;
                obJson['ckeditor_ok_ayucni'] = $("#ckeditor_ok_ayucni").val();

                // console.log(obJson);
                $(".bloquear_todo").attr("disabled","disabled");
                fnModalLoading();

                $.post("transcripcion.php", obJson,
                    function(data){
                        if(data.error == 1)
                        {
                            fnModalLoading_Cerrar();
                            jAlert(data.mensaje, "Mensaje");
                            $(".bloquear_todo").removeAttr("disabled");
                        }
                        else
                        {
                            $("#div_opciones_transcripcion").html(data.html);
                            fnModalLoading_Cerrar();
                            $(".bloquear_todo").removeAttr("disabled");
                        }
                        return data;
                },"json").done(function(data){
                    if(general == '')
                    {
                        fnModalOpcionesTranscripcion(wcod_campo);
                    }
                    else
                    {
                        seleccionTranscripcion(wcod_campo,'*');
                    }
                    // reinicarAcordeonDetalleExamenPaciente();
                }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
            }
            else
            {
                jAlert("Primero borre el texto del campo normal","Alerta");
            }
        }

        function fnModalOpcionesTranscripcion(wcod_campo)
        {
            $( "#div_opciones_transcripcion" ).dialog({
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 'auto',
                // maxHeight: 600,
                width:  'auto',//800,
                buttons: {
                    /*"Usar texto": function() {
                        seleccionTranscripcion(wcod_campo,'');
                        $( this ).dialog( "close" );
                    },*/
                    "Cancelar": function() {
                        $( this ).dialog( "close" );
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Normales",
                beforeClose: function( event, ui ) {
                    //
                },
                create: function() {
                   $(this).closest('.ui-dialog').on('keydown', function(ev) {
                       if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                           $( "#div_opciones_transcripcion" ).dialog('close');
                       }
                   });
                },
                "closeOnEscape": false,
                "closeX": false
            }).on("dialogopen", function( event, ui ) {
                    // var fc = $("#div_errores_liquidacion").find("[id^=busc_nom_medicamento_fiqx_]").attr("id");
                    // $("#"+fc).val("").focus();
            });
        }

        var ckinit = false;
        function fnModalImprimirExamen(id_examen_paciente, wconsecutivo)
        {
            $("#div_imprimir_examen").dialog({
                position: 'top',
                show: {
                    // effect: "blind",
                    effect: "slide",
                    duration: 400 //100
                },
                hide: {
                    effect: "slide",
                    duration: 400 //100
                },
                height: 'auto',
                // maxHeight: 600,
                width:  'auto',//800,
                buttons: {
                    "Archivo PDF": function() {
                        // seleccionTranscripcion(wcod_campo,'');
                        // $( this ).dialog( "close" );
                        generarArchivoPdfMostrar(id_examen_paciente, wconsecutivo);
                    },
                    "Cancelar": function() {
                        $( this ).dialog( "close" );
                    }},
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Generar resultado del examen",
                beforeClose: function( event, ui ) {
                    //
                },
                create: function() {
                    $(this).closest('.ui-dialog').on('keydown', function(ev) {
                        if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                            $( "#div_imprimir_examen" ).dialog('close');
                        }
                    });
                },
                open: function(event, ui){
                    $(this).parent().promise().done(function () {
                        if (!ckinit) {
                            // print source, source es para modificar en html
                            var areaCleditor = $("#txt_resultado_examen").cleditor({
                                            width:      900, // width not including margins, borders or padding
                                            height:     250,
                                            controls:   // controls to add to the toolbar
                                                        "bold italic underline strikethrough subscript superscript | font size " +
                                                        "style | color highlight removeformat | outdent " +
                                                        "indent | alignleft center alignright justify | undo redo | " +
                                                        "rule | print",
                                            docType: // Document type contained within the editor
                                                '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'
                                        })[0];
                            ckinit = true;
                        }
                        else {
                            $("#txt_resultado_examen").cleditor()[0].updateFrame().disable(false).refresh();
                        }
                    });
                },
                "closeOnEscape": false,
                "closeX": false
            }).on("dialogopen", function( event, ui ) {
                //
            });

            // dialogImprime.dialog('open');
        }

        function inicializarCleditor(elem)
        {
            if(ckeditor_ok == true)
            {
                // (CKEDITOR.instances.txt_resultado_examen != undefined) ? CKEDITOR.instances.txt_resultado_examen.destroy():false;
                CKEDITOR.replace('txt_resultado_examen', { customConfig: '../../ayucni/configcke_transcripcion_cni_resultado.js?v='+Math.random(), timestamp : 'something_random' }); // js en include/ayucni
            }
            else
            {
                $(elem).parent().promise().done(function () {
                    if (!ckinit) {
                        // print source, source es para modificar en html
                        var areaCleditor = $("#txt_resultado_examen").cleditor({
                                        width:      900, // width not including margins, borders or padding
                                        height:     550,
                                        controls:   // controls to add to the toolbar
                                                    "bold italic underline strikethrough subscript superscript | font size " +
                                                    "style | color highlight removeformat | outdent " +
                                                    "indent | alignleft center alignright justify | undo redo | " +
                                                    "rule | ",
                                        docType: // Document type contained within the editor
                                            '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'
                                    })[0];
                        ckinit = true;
                    }
                    else {
                        // $('#Comment').cleditor()[0].refresh();

                        // $("#txt_resultado_examen").cleditor()[0].updateFrame();
                        $.cleditor.defaultOptions.width    = 900;
                        $.cleditor.defaultOptions.height   = 550;
                        $.cleditor.defaultOptions.controls = "bold italic underline strikethrough subscript superscript | font size " +
                                                            "style | color highlight removeformat | outdent " +
                                                            "indent | alignleft center alignright justify | undo redo | " +
                                                            "rule | ";
                        $.cleditor.defaultOptions.docType   = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
                        $("#txt_resultado_examen").cleditor()[0].updateFrame().disable(false).refresh();
                    }
                });
            }
        }

        function generarArchivoPdfMostrar(id_examen_paciente, wconsecutivo)
        {
            $("#td_img_carga_pdf").show();
            var contenido_pdf = "";
            if(ckeditor_ok == true)
            {
                contenido_pdf = CKEDITOR.instances.txt_resultado_examen.getData();
            }
            else
            {
                contenido_pdf = $('#txt_resultado_examen').val();
            }

            var obJson                  = parametrosComunes();
            obJson['accion']            = 'load';
            obJson['form']              = 'generar_pdf';
            obJson['wconsecutivo']      = wconsecutivo;
            obJson['contenido_pdf']     = btoa(contenido_pdf);
            obJson['nombre_logo']       = 'clinica';
            obJson['arr_encabezado']    = $("#arr_encabezado_b64").val();
            obJson['usuario_trancribe'] = usuario_trancribe;
            obJson['usuario_modifica']  = usuario_modifica;

            // console.log(obJson);
            $(".bloquear_todo").attr("disabled","disabled");
            fnModalLoading();

            var ruta_pdfs = "resultados/resultado_examen_"+wconsecutivo+".pdf";
            $.post("generar_pdf.php", obJson,
                function(data){
                    fnModalLoading_Cerrar();
                    $(".bloquear_todo").removeAttr("disabled");
                },
                "json"
            ).done(function()   {
                // $("#div_vista_previa").show();
                $("#div_vista_previa_contenedor").show();
                $("#div_contenedor_pdf").empty();
                var object= '<br>'
                            +'<object type="application/pdf" data="'+ruta_pdfs+'#toolbar=1&amp;navpanes=0&amp;scrollbar=1" width="900" height="700">'
                                +'<param name="src" value="resultados/resultado_laboratorio.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1" />'
                                +'<p style="text-align:center; width: 60%;">'
                                    +'Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />'
                                    +'<a href="http://get.adobe.com/es/reader/" onclick="this.target=\'_blank\'">'
                                        +'<img src="../../images/medical/root/prohibido.gif" alt="Descargar Adobe Reader" width="32" height="32" style="border: none;" />'
                                    +'</a>'
                                +'</p>'
                            +'</object>';

                $("#div_contenedor_pdf").html(object);
                $("#td_img_carga_pdf").hide(1500);
            }).fail(function(xhr, textStatus, errorThrown) { $("#td_img_carga_pdf").hide(1500); mensajeFailAlert('', xhr, textStatus, errorThrown); });
        }

        function generarContenidoEditable()
        {
            // console.log("...");

            var txt = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quis id, cumque provident. Voluptatibus ullam adipisci, recusandae optio eaque, beatae. Deleniti aliquam pariatur et tempora doloremque nemo asperiores error repudiandae deserunt!';
            $("#txt_resultado_examen").val(txt);
            $("#txt_resultado_examen").cleditor()[0].disable(false).refresh();

            $(".cleditorMain iframe").contents().find('body').bind({
                    'keypress': function(event){
                            var charCode = (event.which) ? event.which : event.keyCode;
                        }
                }
            );
        }

        function seleccionTranscripcion(wcod_campo,opcion_especifica)
        {
            var cod_normal = (opcion_especifica != '') ? opcion_especifica : $("#select_opcion_transcrip_"+wcod_campo).val();
            if(cod_normal != '')
            {
                var arr_txt = eval('(' + $("#txt_previsualizarTranscripcion_"+wcod_campo).val() + ')');
                // Los textos del array estan codificados a base64 porque si tiene comillas o caracteres especiales falla al intentar agregarlo al campo
                // con la instrucción atob se decodifica.
                var txt_normal = atob(arr_txt[cod_normal]);

                if(ckeditor_ok == true)
                {
                    // txt_normal = txt_normal.replace(/[\t\n]/gi,"<br>");
                    CKEDITOR.instances[wcod_campo].setData(txt_normal);
                }
                else
                {
                    $("#"+wcod_campo).val(txt_normal);
                }
            }
            else
            {
                // $("#"+wcod_campo).val("");
            }

            fnModalOpcionesTranscripcion_cerrar();
        }

        function borrarTextoNormal(wcod_campo)
        {
            $("#"+wcod_campo).val("");
        }

        function fnModalOpcionesTranscripcion_cerrar()
        {
            if($("#div_opciones_transcripcion").is(":visible"))
            {
                $("#div_opciones_transcripcion").dialog('close');
            }
        }

        function previsualizarTranscripcion(wcod_campo,txt_previsualizarTranscripcion,div_previsualizarTranscripcion)
        {
            var cod_normal = $("#select_opcion_transcrip_"+wcod_campo).val();
            // console.log($("#txt_previsualizarTranscripcion_"+wcod_campo).val());
            if(cod_normal != '')
            {
                var arr_txt = eval('(' + $("#txt_previsualizarTranscripcion_"+wcod_campo).val() + ')');
                var txtprev = '<textarea readonly style="font-family:Arial;font-size:8pt;text-align:justify;width: 592px; height: 179px;background-color: #f2f2f2;border: 0 none;">'+arr_txt[cod_normal]+'</textarea>';
                $("#div_previsualizarTranscripcion_"+wcod_campo).html(txtprev);
            }
            else
            {
                $("#div_previsualizarTranscripcion_"+wcod_campo).html("");
            }
        }

        function recalcularFormulas(elem)
        {
            $(".onfocus").removeClass("onfocus");
            $(".onfocus").animate({color: 'blue','font-weight': 'bold'}, 1000);
            if(elem != '')
            {
                $(elem).addClass("onfocus");
                var wcod_campo = $(elem).attr("id");
            }
            var pwExp = new RegExp("pow", "gi");// la instrucción de exponencial se cambia por la equivalente en javascript

            //reinicar calculo parcial de formulas
            $(".formula").each(function(){
                var formula = $(this).attr("formula");
                $(this).attr("formula_parcial",formula);
                $(this).removeClass("formula_completa");//Esta clase indica si con los valores reemplazados en la formula ya se puede calcular un valor real o aún falta un dato.
                // console.log($(this));
            });

            // recorrer cada campo y verificar si afecta alguna formula, si la afecta entonces en formula_parcial se copia la formula o se lee
            // y se cambia el código del campo por el valor calculado.
            $("input[tipo_dato=Numero]").each(function(){
                var codigo_campo = $(this).attr("id");
                var valor_campo = $(this).val();
                if(valor_campo != undefined && valor_campo != '')
                {
                    $(".formula[formula*="+codigo_campo+"]").each(function(){
                        var re = new RegExp(codigo_campo, "gi");
                        var formula = $(this).attr("formula_parcial");
                        formula = formula.replace(re,$("#"+codigo_campo).val());
                        $(this).attr("formula_parcial",formula);
                        // $(this).html(formula); // SI SE QUIERE VER COMO VA ACAMBIANDO LA FORMULA A MEDIDA QUE SE VAN ESCRIBIENDO LOS DATOS
                        // console.log(this);
                    });
                }
            });

            // recorrer todas las formulas y verificar si ese campo de formula afecta otra formula
            $(".formula").each(function(){
                var codigo_campo = $(this).attr("id");
                // var valor_campo = $(this).val();
                var valor_campo_formula = $(this).attr("formula_parcial");
                if(valor_campo_formula != undefined && valor_campo_formula != '')
                {
                    valor_campo_formula = valor_campo_formula.replace(pwExp,"Math.pow");
                    if($.isNumeric(eval(valor_campo_formula)))
                    {
                        // var valor_formula = eval(valor_campo_formula);
                        var valor_formula = roundTo(eval(valor_campo_formula), 3); //Valor calculado de la formula que se requiere para usar en otra formula
                        $(".formula[formula*="+codigo_campo+"]").each(function(){
                            var re = new RegExp(codigo_campo, "gi");
                            // var pwExp = new RegExp("pow", "gi");// la instrucción de exponencial se cambia por la equivalente en javascript
                            var campo_subformula = $(this).attr("id");
                            var formula = $(this).attr("formula_parcial");
                            formula = formula.replace(re,valor_formula);
                            $(this).attr("formula_parcial",formula);
                        });
                    }
                }
            });

            // Calculo todos los valores con eval
            $(".formula").each(function(){
                var codigo_campo = $(this).attr("id");
                var formula = $(this).attr("formula_parcial");
                if(formula != '')
                {
                    formula = formula.replace(pwExp,"Math.pow");
                    // console.log(codigo_campo+": "+formula);
                    if($.isNumeric(eval(formula)))
                    {
                        var redondeo = roundTo(eval(""+formula+""), 2);
                        var valor_spn = '<span style="" class="font_label">'+redondeo+'</span>';
                        var formula_spn = formula;
                        $("#"+codigo_campo).html(valor_spn);
                        $("#"+codigo_campo).attr("valor_calculado",redondeo);

                        $("#spn_fomula_calc_"+codigo_campo).html(formula_spn);
                        $("#"+codigo_campo).addClass("formula_completa");//Esta clase indica si con los valores reemplazados en la formula ya se puede calcular un valor real o aún falta un dato.
                    }
                    else
                    {
                        $("#"+codigo_campo).html("[?]");
                        $("#"+codigo_campo).attr("valor_calculado","");
                        $("#spn_fomula_calc_"+codigo_campo).html("");
                    }
                }
            });
        }

        function verBtnNuevoExam(elem)
        {
            var opc = $(elem).val();
            if(opc != '')
            {
                $("#btn_nuevo_examen").show(500);
            }
            else
            {
                $("#btn_nuevo_examen").hide(500);
            }
        }

        function campoSiguiente(elem, evt)
        {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            // console.log(charCode);
            if (charCode == 13 || charCode == 33 || charCode == 34 )
            {
                // console.log($(elem));
                // console.log($(elem).next('.inputs').focus());
                return true;
            }
            else
            {
                return false;
            }
        }

        function roundTo(num, decimales) {
            return +(Math.round(num + "e+"+decimales)  + "e-"+decimales);
        }

        function classOnFocus(elem)
        {
            $(".onfocus").removeClass("onfocus");
            $(elem).addClass("onfocus");
        }

        function agregarElementoLista(elem, wcod_campo)
        {
            var valor_opc = $(elem).attr("codigo");
            if(valor_opc != '')
            {
                var texto_opc = $(elem).attr("nombre");
                if($("#"+wcod_campo).find("#"+wcod_campo+'_'+valor_opc+'').length == 0)
                {
                    var imgeliminar = '<img style="cursor:pointer;" src="../../images/medical/eliminar1.png" alt="Eliminar" onclick="eliminarOpcionCampo(\''+wcod_campo+'_'+valor_opc+'\');" />';
                    var nuevoDiv = '<div id="'+wcod_campo+'_'+valor_opc+'" codigo="'+valor_opc+'" onmouseover="trOver(this);" onmouseout="trOut(this);" >'+imgeliminar+' '+texto_opc+'</div>';
                    $("#"+wcod_campo).append(nuevoDiv);
                }
                else
                {
                    var div = $("#"+wcod_campo+'_'+valor_opc);
                    div.animate({color: 'blue','font-weight': 'bold'}, 1000);
                    div.animate({color: 'black','font-weight': ''}, 0);
                    div.animate({color: 'blue','font-weight': 'bold'}, 2000);
                    div.animate({color: 'black','font-weight': ''}, 0);
                }
            }
        }

        function eliminarOpcionCampo(wcod_campo_eliminar)
        {

            var div = $("#"+wcod_campo_eliminar).clone();
            div.find("img").remove();
            var msj_elim = 'Está intentando eliminar la opción: <span style="color:red; font-weight:bold;">'+div.html()+'</span>';

            jConfirm(msj_elim, 'Solicitud de confirmación', function(r) {
                // jAlert('Confirmed: ' + r, 'Confirmation Results');
                if(r)
                {
                    var div = $("#"+wcod_campo_eliminar);
                    div.css({color: 'red'});
                    div.hide("slow",
                                function(){
                                    $(this).remove();
                                }
                            );
                }
            });
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
            obJson['wbasedato_ayu']    = $("#wbasedato_ayu").val();
            obJson['wbasedato_tal']    = $("#wbasedato_tal").val();
            // obJson['wbasedato_tcx']    = $("#wbasedato_tcx").val();
            obJson['consultaAjax']     = '';
            return obJson;
        }

        function enterBuscar(e)
        {
            var tecla = (document.all) ? e.keyCode : e.which;
            if(tecla == 13)
            {
                btn_consultarDatos();
            }
        }

        function reiniciarTooltip()
        {
            $('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
        }

        /**
         * [crearAutocomplete: Inicializa las listas seleccionables en los campos que se definene como autocompletar]
         * @param  {[type]} accion                 [Acción o comportamiento especial que debe asumir la función dado el valor que llega en este parámetro]
         * @param  {[type]} arr_opciones_seleccion [Array de las opciones que debe desplegar el campo autocompletar]
         * @param  {[type]} campo_autocomplete     [ID html del campo que será y se iniciará como autocomplete]
         * @param  {[type]} codigo_default         [Código por defecto con el que podría iniciar el autocomplete]
         * @param  {[type]} nombre_default         [Nombre por defecto con el que podría iniciar el autocomplete]
         * @param  {[type]} limite_buscar          [Límite mínimo de caracteres con el que debería empezar a funcionar el autocomplete]
         * @param  {[type]} busqueda_ajax          [Se realiza consulta mediante ajax cada que se escriba cierta cantidad inicial de caracteres, sobre todo cuando son maestros muy grandes y no se cargan al html]
         * @param  {[type]} tabla_opc_ajax         [Tabla de opciones de transcripción en la que se va a buscar]
         * @return {[type]}                        [description]
         */
        function crearAutocomplete(arr_opciones_seleccion, campo_autocomplete, codigo_default, nombre_default, limite_buscar, busqueda_ajax, tabla_opc_ajax)
        {
            $("#"+campo_autocomplete).val(nombre_default);
            $("#"+campo_autocomplete).attr("codigo",codigo_default);
            $("#"+campo_autocomplete).attr("nombre",nombre_default);

            arr_datos = new Array();
            //var datos = arr_wempresp;//eval( $("#arr_wempresp").val() );
            if(busqueda_ajax == 0){
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
            } else {
                var fn_ajax_buscar = "transcripcion.php?accion=load&form=buscar_ajx_opciones&wemp_pmla="+$("#wemp_pmla").val()+"&wbasedato_ayu="+$("#wbasedato_ayu").val()+"&consultaAjax=&tabla_opc_ajax="+tabla_opc_ajax;
            }


            // console.log(arr_datos);
            if($("#"+campo_autocomplete).length > 0)
            {
                var params_auto = {
                        minLength : limite_buscar,
                        select: function( event, ui ) {
                                    // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                                    var cod_sel = ui.item.codigo;
                                    var nom_sel = ui.item.nombre;
                                    $("#"+campo_autocomplete).attr("codigo",cod_sel);
                                    $("#"+campo_autocomplete).attr("nombre",nom_sel);
                                    if($(this).attr("tipo_dato") == 'MultiSeleccion')
                                    {
                                        agregarElementoLista(this,$(this).attr("wcod_campo"));
                                    }
                                    // cargarConceptosPorProcedimientos(cod_sel);
                                },
                        close: function( event, ui ) {
                            if($(this).attr("tipo_dato") == 'MultiSeleccion')
                            {
                                $(this).val("");
                                $(this).attr("codigo","");
                                $(this).attr("nombre","");
                            }
                        }
                };

                if(busqueda_ajax == 0){ params_auto.source = arr_datos; }
                else { params_auto.source = fn_ajax_buscar; }
                $("#"+campo_autocomplete).autocomplete(params_auto);//.addClass("ui-autocomplete-loading");
            }
            else if($("."+campo_autocomplete).length > 0)
            {
                var params_auto = {
                        minLength : limite_buscar,
                        select: function( event, ui ) {
                                    var cod_sel = ui.item.codigo;
                                    var nom_sel = ui.item.nombre;
                                    var id_el = $(this).attr("id");
                                    $("#"+id_el).attr("codigo",cod_sel);
                                    $("#"+id_el).attr("nombre",nom_sel);
                                    if($(this).attr("tipo_dato") == 'MultiSeleccion')
                                    {
                                        agregarElementoLista(this,$(this).attr("wcod_campo"));
                                    }
                                },
                        close: function( event, ui ) {
                            if($(this).attr("tipo_dato") == 'MultiSeleccion')
                            {
                                $(this).val("");
                                $(this).attr("codigo","");
                                $(this).attr("nombre","");
                            }
                        }
                };

                if(busqueda_ajax == 0){ params_auto.source = arr_datos; }
                else { params_auto.source = fn_ajax_buscar; }
                $("."+campo_autocomplete).autocomplete(params_auto);//.addClass("ui-autocomplete-loading");
            }
        }

        function verFormulaCalculada(elem)
        {
            var chk = ($(elem).is(":checked")) ? true: false;
            $(".spn_fomula_calc").hide();
            if(chk)
            {
                $(".spn_fomula_calc").show();
            }
        }

        function verFormulaCampos(elem)
        {
            var chk = ($(elem).is(":checked")) ? true: false;
            $(".spn_fomula_campo").hide();
            if(chk)
            {
                $(".spn_fomula_campo").show();
            }
        }

    </script>

    <script type="text/javascript">

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

        function mostrar_mensajeAccion(mensaje,div,tipo_msj)
        {
            var colormej = (tipo_msj != "") ? tipo_msj: "";
            $("#"+div).html("<img width='15' height='15' src='../../images/medical/root/info.png' />&nbsp;<span style='color:"+colormej+";'>"+mensaje+"</span>");
            $("#"+div).css({"width":"250","opacity":" 0.6","fontSize":"11px"});
            $("#"+div).hide();

            $("#"+div).effect("pulsate", {}, 2000);
                setTimeout(function() {
                $("#"+div).hide(500);
            }, 1000);
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
            $(grupo).addClass('classOver');
        }

        function trOut(grupo)
        {
            $(grupo).removeClass('classOver');
        }

        /**
         * [formulaOver: función para resaltar las formulas y los campos que contiene para ubicarlo en el formulario]
         * @param  {[type]} classFormula [description]
         * @return {[type]}              [description]
         */
        function formulaOver(classFormula)
        {
            // console.log($("#"+classFormula).find(".spn_fomula_campo_over").length);
            $("#"+classFormula).find(".spn_fomula_campo_over").each(function(){
                var cod_campoFormula = $(this).attr('cod_campoFormula');
                // console.log(cod_campoFormula);
                $(".spn_fomula_campo_"+cod_campoFormula).addClass('classOver');
            });
            $("#"+classFormula).addClass('classOver');
        }

        function formulaOut(classFormula)
        {
            $("#"+classFormula).find(".spn_fomula_campo_over").each(function(){
                var cod_campoFormula = $(this).attr('cod_campoFormula');
                // console.log(cod_campoFormula);
                $(".spn_fomula_campo_"+cod_campoFormula).removeClass('classOver');
            });
            $("#"+classFormula).removeClass('classOver');
        }

        function resaltarCamposFormula(classFormula)
        {
            // console.log($("#"+classFormula).hasClass("classOverFormula"));
            if($("#"+classFormula).hasClass("classOverFormula"))
            {
                $("#"+classFormula).find(".spn_fomula_campo_over").each(function(){
                    var cod_campoFormula = $(this).attr('cod_campoFormula');
                    // console.log(cod_campoFormula);
                    $(".spn_fomula_campo_"+cod_campoFormula).removeClass('classOverFormula');
                });
                $("#"+classFormula).removeClass('classOverFormula');
            }
            else
            {
                $(".classOverFormula").removeClass("classOverFormula");
                $("#"+classFormula).find(".spn_fomula_campo_over").each(function(){
                    var cod_campoFormula = $(this).attr('cod_campoFormula');
                    // console.log(cod_campoFormula);
                    $(".spn_fomula_campo_"+cod_campoFormula).addClass('classOverFormula');
                });
                $("#"+classFormula).addClass('classOverFormula');
            }
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

                    if($("#"+index).is("input,select") && $("#"+index).attr("type") != 'checkbox' && $("#"+index).attr('multiple') == undefined) // Si es input o select entonces escribe en un campo u opci? de un select sino escribe en html.
                    {
                        $("#"+index).val(value);
                        //Si es un select y adicionalmente tiene el evento onchange entonces debe ejecutar el evento para que el select dependiente se cargue con las opciones v?idas.
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

        function cerrarVentanaPpal()
        {
            window.close();
        }

    </script>

    <style type="text/css">
        .placeholder
        {
          color: #aaa;
        }

        .classOver{
            background-color: #CCCCCC;
        }

        .classOverFormula{
            background-color: #B5EFA6;
        }

        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px }
        .tipo3V:hover {color: #000066; background: #999999;}

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
        .disminuir{
            font-size:11pt;
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

        /* NOTIFICACI? */
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

        .bordesmarcos{
            border: 2px solid #e2e2e2;
        }

        .onfocus{
            background-color: lightyellow;
        }

        .td_formula {
            position: relative;
            display: block;
        }

        .td_formula span.spn_fomula_calc {
            /*float: left;
            position:relative;
            left: 50px;*/
            /*position: absolute;*/
            /*top: 20px;*/
            left: 100px;
            /*color:red;*/
        }

        /*.td_formula > .formula {
            color: #F0665E;
            margin-left: 12px;
        }

        .td_formula > .rang_unid_formula {
            display: inline;
            margin-left: 81px;
        }*/

        .partea {
            float: left;
            /*padding-left: 2%;*/
            margin-left: 2%;
            width: 16.5%;
            /*background-color: orange;*/
            color: #F0665E;
            /*text-align: center*/
            background-color: #E8EEF7;
        }

        .parteb {
            float: left;
            text-align: left;
            width: 81.5%;
            /*background-color: red;*/
            background-color: #E8EEF7;
        }

        .font_label{
            font-size: 16px;
            white-space: nowrap;
        }

        h4{
            margin: 0px;
            font-size: 18px;
        }

        .inputs:not(select):not([type=button]):not(.examen_autocomplete){
            width: 100px;
            height: 15px:;
            font-size: 12px;
            margin-left:12px;
        }

        textarea.memoCss  {
            width:570px; /*700px;*/
            height: 400px;
            padding-left: 10px;
            font-size: 2.5em;
            /*font-weight: bold;*/
            font-family: Verdana, Arial, Helvetica, sans-serif;
        }

        .css_memo {
            width: 580px; height: 100px;
        }

        .examen_autocomplete {
            width: 270px;
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
           height: 30px;
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

        .texto {
            display: block;
            float: left;
            width: 60px;
            }

        ul{
            margin:0;
            padding:0;
            list-style-type:none;
        }

        #caja_campos_flotantes{
            /*position: fixed;*/
            /*bottom: 0;*/
            left: 49%;
            position: fixed;
            /*top:0;*/
            right: 0;
            bottom: 10px;
            /*left: 10px;*/
            border: 2px solid #999999;
            /*background-color: #f2f2f2;*/
            background-color: #FFFFE0;
            color: black;
            font-weight:bold;
            /*height:200px;*/
            /*overflow:scroll;*/
            width:760px;
            /*margin-left: 10px;*/
            margin-left: auto;
        }

        #caja_nombrepac{
            /*position: fixed;*/
            /*bottom: 0;*/
            left: 50%;
            position: fixed;
            top:0;
            right: 0;
            /*bottom: 10px;*/
            /*left: 10px;*/
            border: 2px solid #999999;
            /*background-color: #f2f2f2;*/
            background-color: #FFFFE0;
            color: black;
            font-weight:bold;
            /*height:200px;*/
            /*overflow:scroll;*/
            width:730px;
            /*margin-left: 10px;*/
        }

        .ui-autocomplete-loading {
            background: white url("../../images/medical/ajax-loader5.gif") right center no-repeat;
            background-size: 15px 15px;
        }

        .ui-autocomplete {
            max-height: 150px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            font-size:  9pt;
        }

        /*.ui-autocomplete{
            max-width:  100px;
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
            font-size:  9pt;
        }*/

    </style>
</head>
<body>
<?php
    encabezado("<div class='titulopagina2'>Transcripción</div>", $wactualiza, "clinica");
?>
<input type='hidden' name='wbasedato' id='wbasedato' value="<?=$wbasedato?>">
<input type='hidden' name='wemp_pmla' id='wemp_pmla' value="<?=$wemp_pmla?>">
<input type='hidden' name='get_whistoria' id='get_whistoria' value="<?=$whis?>">
<input type='hidden' name='get_wingreso' id='get_wingreso' value="<?=$wing?>">
<input type='hidden' name='wbasedato_HCE' id='wbasedato_HCE' value="<?=$wbasedato_HCE?>">
<input type='hidden' name='wbasedato_movhos' id='wbasedato_movhos' value="<?=$wbasedato_movhos?>">
<input type='hidden' name='wbasedato_cliame' id='wbasedato_cliame' value="<?=$wbasedato_cliame?>">
<input type='hidden' name='wbasedato_ayu' id='wbasedato_ayu' value="<?=$wbasedato_ayu?>">
<input type='hidden' name='wbasedato_tal' id='wbasedato_tal' value="<?=$wbasedato_tal?>">
<input type='hidden' name='codigoempresaparticular' id='codigoempresaparticular' value="<?=$codigoempresaparticular?>">
<input type='hidden' name='arr_encabezado_examen' id='arr_encabezado_examen' value="<?=base64_encode(serialize(array()))?>">
<input type='hidden' name='arr_encabezado_b64' id='arr_encabezado_b64' value="<?=base64_encode(serialize(array()))?>">
<input type='hidden' name='ckeditor_ok_ayucni' id='ckeditor_ok_ayucni' value="<?=$ckeditor_ok_ayucni?>">


<input type='hidden' name='concepto' id='concepto' value="<?=$concepto?>">
<input type='hidden' name='ccosto' id='ccosto' value="<?=$ccosto?>">


<table align="center" style="width:95%;">
    <tr>
        <td style="text-align:left;">
            <div id="contenedor_programa_transcripcion" align="left">
                <div width='' id='accordionDatosPaciente' style="text-align:left;width: 1250px;" class="div_alinear">
                    <h3>DATOS DEL PACIENTE</h3>
                    <div class='pad' align='center' id='DatosPaciente'>
                        <table width='100%' style='border: 1px solid #999999;'>
                            <tr class="encabezadoTabla">
                                <td>Historia-Ingreso</td>
                                <td>Documento</td>
                                <td>Primer nombre</td>
                                <td>Segundo nombre</td>
                                <td>Primer apellido</td>
                                <td>Segundo Apellido</td>
                            </tr>
                            <tr class="fila2">
                                <td>
                                    <input type="text" id="whistoria" name="whistoria" class="limpiar bloquear_todo" size="7" onkeypress="enterBuscar(event);" placeholder="Historia" limpiar="si" value="">-
                                    <input type="text" id="wingreso" name="wingreso" class="limpiar bloquear_todo" size="3" onkeypress="enterBuscar(event);" placeholder="Ing." limpiar="si" value="">
                                </td>
                                <td>
                                    <span id="wtipodoc" class="limpiar" >-</span>
                                    <input type="text" id="wdocumento" name="wdocumento" class="limpiar bloquear_todo" size="12" onkeypress="enterBuscar(event);" placeholder="Documento" limpiar="si" value="">
                                </td>
                                <td><input type="text" id="wnombre1" name="wnombre1" class="limpiar bloquear_todo" size="20" onkeypress="enterBuscar(event);" placeholder="1° Nombre" limpiar="si" value=""></td>
                                <td><input type="text" id="wnombre2" name="wnombre2" class="limpiar bloquear_todo" size="20" onkeypress="enterBuscar(event);" placeholder="2° Nombre" limpiar="si" value=""></td>
                                <td><input type="text" id="wapellido1" name="wapellido1" class="limpiar bloquear_todo" size="20" onkeypress="enterBuscar(event);" placeholder="1° Apellido" limpiar="si" value=""></td>
                                <td><input type="text" id="wapellido2" name="wapellido2" class="limpiar bloquear_todo" size="20" onkeypress="enterBuscar(event);" placeholder="2° Apellido" limpiar="si" value=""></td>
                            </tr>
                            <tr class="encabezadoTabla">
                                <td>Fecha nacimiento</td>
                                <td>Edad</td>
                                <td>Sexo</td>
                                <td>Teléfono</td>
                                <td colspan="1">Empresa responsable</td>
                                <td>Estado del paciente</td>
                            </tr>
                            <tr class="fila2">
                                <td><span id="wfechaNac" class="limpiar" >-</span></td>
                                <td><span id="wedad" class="limpiar" >-</span></td>
                                <td><span id="wsexo" class="limpiar" >-</span></td>
                                <td><span id="wtelefono" class="limpiar" >-</span></td>
                                <td colspan="1"><span id="wempresa" class="limpiar" >-</span></td>
                                <td colspan="1"><span id="westPac" class="limpiar" >-</span></td>
                            </tr>
                            <tr class="fila2">
                                <td colspan="6" style="text-align: center;">
                                    <input type="button" id="btn_consultar_paciente" onclick="btn_consultarDatos();" value="Consultar">
                                    <input type="button" id="btn_limpiar" onclick="limpiarEncabezado();" value="Iniciar campos">
                                    <input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();">
									
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div width='' id='accordionExamenesPaciente' style="text-align:left;width: 1250px;" class="div_alinear">
                    <h3>EXAMENES DEL PACIENTE</h3>
                    <div class='pad' align='center' id='div_examenes_paciente'>
                        <!--  -->
                    </div>
                </div>
                <br>
                <div width='' id='accordionExameneDetalle' style="text-align:left;width: 1250px;" class="div_alinear">
                    <h3>DETALLE EXAMEN</h3>
                    <div class='pad' align='center' id='div_examen_detalle' style="text-align: left;">
                        <!--  -->
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>
<br />
<br />
<table align='center'>
    <tr><td align="center" colspan="9"><input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();"></td></tr>
</table>
<br />
<br />
<div id="div_seleccionar_paciente" style="display:none;"></div>
<div id="div_opciones_transcripcion" style="display:none;"></div>

<div id="buffer_formato" style="display:none"></div>

<div id="div_loading" style="display:none;"><img width="15" height="15" src="../../images/medical/ajax-loader5.gif" /> Consultando datos, espere un momento por favor...</div>
<input type='hidden' name='failJquery' id='failJquery' value='El programa terminó de ejecutarse pero con algunos inconvenientes <br>(El proceso no se completó correctamente)' >
<div id="caja_campos_flotantes" style="display:none;" align="right">
    <div id="cont_caja_flotante_query" style="padding: 2px;text-align:left;">
    </div>
    <div id="txt_div_normal" style="cursor: pointer;font-size:9pt;" class="encabezadoTabla">
        <table style="width:100%;">
            <tr>
                <td style="width:25%;text-align:left;"><input type="button" class="btn_guardar_examen" value="Guardar parcialmente" style="font-size:8pt;" ></td>
                <td style="text-align:right;" onmouseover="trOver(this);" onmouseout="trOut(this);" onclick="verOcultarLista('cont_caja_flotante_query');" ><span id="spn_nombre_paciente"></span> | <input type="button" value="Min/Max" style="cursor: pointer;font-size:8pt;" ></td>
            </tr>
        </table>
    </div>
</div>
<div id="caja_nombrepac" style="display:none;" align="left"> </div>
<div class='pad' align='center' id='div_examen_detalle_modal' style="text-align: left;">
    <!--  -->
</div>
<div id="div_firma_medico" style="display:none;">
    <div style="width:100%; text-align:center;">
        <span id="spn_medico_cierre_examen" style="font-weight:bold;"></span>
        <br>
        Clave especialista: <input type="password" id="firma_medico" value="">
        <br>
        <div id="div_msj_firma_err" style="font-weight:bold;color:red;width:310px;"></div>
    </div>
</div>
<div id="div_confirmar_inactivar" style="display:none;text-align:center;">
    <!-- <div style="width:100%; text-align:center;font-weight:bold;" id="msj_estado_reg">
    </div> -->
</div>
</body>
</html>