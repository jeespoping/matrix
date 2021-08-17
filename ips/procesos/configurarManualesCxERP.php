<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : configurarManualesCxERP.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 12 Febrero de 2014

 DESCRIPCION: Este programa es creador para realizar la configuración de los manuales de cirugías múltiples.

 En este programa se puede configurar el porcentaje que se debe cobrar por cada concepto dependiendo del número de cirugía y dependiente de la configuración del acto quirúrgico.


 Notas:
 El valor del parámetro $ccotema es determinado en el programa gestor_aplicaciones.php
 */ $wactualiza = "(Enero 04 de 2016)"; /* 9d.dllo
 
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  CAMBIOS PARA MIGRACION
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	CODIGO	|	FECHA		|	AUTOR 	|	DESCRIPCION	
----------------------------------------------------------------------------------------------------------------------------------------------
	MIGRA_1	|	2019-01-27	|	Jerson	|	Se coloca el utf8_encode 
	MIGRA_2	|	2019-01-27	|	Jerson	|	Se quita el utf8_encode 
----------------------------------------------------------------------------------------------------------------------------------------------
	
	
 ACTUALIZACIONES:
 * Julio 28 2021 Juan David Rodriguez:
    - Se agrega tabla de log para el manual de configuración.
 * Enero 04 2016 Edwar Jaramillo:
    - Para los manuales de urgencias se inactivan las opciones de bilateral y repetir porcentaje para algunos conceptos, se ocultan esas opciones para urgencias.
 * Noviembre 11 2015 Edwar Jaramillo:
    - Se habilita el campo "Redondeo centena más cercana" para los manuales de urgencias.
 * Octubre 30 2015 Edwar Jaramillo:
    - Nuevo arrayCentroCosto para diferenciar los manuales por centros de costos, los centros de costos de cirugía se dejaron en un mismo grupo y se identifican con '*'
    - El tipo de acto quirúrgico depende del centro de costo '*'-actividad, '1130'-urg, 'pate'-paquetes. Al seleccionar un centro de costos diferente a cirugía, se ocultan los
        demás campos de parametrización como limites de cobro y parámetros generales por ejemplo de redondeo entre otros.
    - Los conceptos que conforman los porcentajes de cada manual ahor ase permite diferenciar por centro de costo, para urgencias no se necesitan configurar todos los conceptos
        que se usan en cirugía.
 * Octubre 15 2015 Edwar Jaramillo:
    - Nuevo identificador del programa para diferenciar actos quirúrgicos y porcentajes por ejemplo sólo de actividades o de paquetes.
    - En las tablas 000187 y 000188 se crea un nuevo campo "xxxtaq" que indica que tipo de acto quirúrgico corresponde la información "pqte, actividad".
    - Los porcentajes de cirugías para paquetes solo tienen una sola columna a diferencia de los procedimientos o actividades que los porcentajes son para cada concepto.
 * Septiembre 30 2015 Edwar Jaramillo:
    - Actualización de estilo, vista más agradable.
 * Marzo 27 2015
    Edwar Jaramillo     : * Nueva opción para configurar si se deben redondear las centenas en las cifras de la liquidación de cirugía
 * Febrero de 2015
    Edwar Jaramillo     : * Se crea un nuevo campo en el encabezado de los manuales para indicar si los insumos se deben validar artículo por artículo en la tabla de relación empresa medicamento.
 * Diciembre 17 2014
    Edwar Jaramillo     : * En el encabezado de la configuración de los manuales se crea una nueva opción que para determinar si el mercado será no facturable por defecto
                            (todo el mercado no facturable), y las clasificaciones en este caso corresponderán a lo que SI será facturable (caso contrario si la opción no está marcada).
                            La opción lo que hace es cambiar la lógica de lo si y no facturable.
 * Octubre 28 de 2014
    Edwar Jaramillo     : * Se crea un nuevo campo para configurar los manuales tambien por procedimiento, por ejemplo para el procedimiento conización,
                            que aplica un manual espécifico para el IDC y el porcentaje para primera cirugía varía, no todos los conceptos son al 100%.
 *  Octubre 09 2014
    Edwar Jaramillo     : Se crea nuevo campo y funcionalidad para determinar hasta que máximo de cirugías se debe cobrar un concepto cuando hay cirugías múltiples.
 *  Enero 31 de 2014
    Edwar Jaramillo     : Fecha de la creación del programa.
**/
global $ccotema;
global $wbasedato_HCE, $bordemenu;
$fecha_data = date("Y-m-d");
$hora_data = date("H:i:s");
$fecha_hora = date("Y-m-d H:i:s");





include_once("../../gesapl/procesos/gestor_aplicaciones_config.php");
include_once("../../gesapl/procesos/gesapl_funciones.php");
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
include_once("ips/funciones_facturacionERP.php");


if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesi&oacute;n nuevamente en la p&aacute;gina principal de Matrix para que pueda seguir utilizando este programa normalmente.";
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

/**********  TABLAS  **********/
define('TB_BASE_LIQUIDACION','000186');
define('TB_BASE_LIQ_ACTOSQX','000187');
define('TB_BASE_LIQPORCENTA','000188');

$caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");

function nameDia($ano,$mes,$dia)
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

function arrayTipoEmpresas($conex, $wbasedato, $wemp_pmla)
{
    /***** Tipo de empresa *****/
    $sql = "SELECT  Temcod AS codigo, Temdes AS nombre
            FROM    {$wbasedato}_000029
            WHERE   Temest = 'on'
            ORDER BY Temdes ";
    $resS = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
    $arr_tipo_empresa = array();
    $arr_tipo_empresa['*'] = 'TODOS';
    while ($row = mysql_fetch_array($resS))
    {
        $arr_tipo_empresa[$row['codigo']] = utf8_encode($row['nombre']);
    }
    return $arr_tipo_empresa;
}

/**
 * [arrayCentroCosto: Todos los centros de costos de cirugía usan los mismos manuales, para eso se crea el centro de costo '*' que aplica para todos los cco de cirugía
 *                     pero adicionalmente se consultan los centros de costos de urgencias para que se puedan crear manuales manuales para urgencias]
 * @param  [type] $conex     [description]
 * @param  [type] $wbasedato [description]
 * @param  [type] $wemp_pmla [description]
 * @return [type]            [description]
 */
function arrayCentroCosto($conex, $wbasedato, $wemp_pmla)
{
    $wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    /***** Centro de costos *****/
    $sql = "SELECT  Ccocod AS codigo, Cconom AS nombre, Ccocir AS es_cirugia, Ccourg AS es_urgencias
            FROM    {$wbasedato_movhos}_000011
            WHERE   Ccourg = 'on'
                    AND Ccoest  = 'on'"; // (Ccocir = 'on' OR Ccourg = 'on')
    $resS = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
    $arr_centro_costo = array();
    $arr_centro_costo['*'] = array("nombre"=>'TODOS los de cirug&iacute;a',"tipo_cco"=>"cir");
    while ($row = mysql_fetch_array($resS))
    {
        $tipo_cco = "";
        if($row['es_cirugia'] == 'on')
        {
            $tipo_cco = "cir";
        }
        elseif($row['es_urgencias'] == 'on')
        {
            $tipo_cco = "urg";
        }

        $arr_centro_costo[$row['codigo']] = array("nombre"=>utf8_encode($row['nombre']), "tipo_cco"=>$tipo_cco);
    }
    return $arr_centro_costo;
}

function crearHtmlFilaCirugia($conex, $wbasedato, $wemp_pmla, $arr_conceptos_manuales , $no_existe_en_bd , $id_manual , $id_acto_qx , $wcodigo_manual , $codigo_acto_qx, $tipo_acto , $fecha_data , $hora_data , $user_session, $data, $bilateral)
{
    $arr_keys_conceptos = array_keys($arr_conceptos_manuales);
    $html = "";
    $fecha_hora = date("Y-m-d H:i:s");

    $span_desc_fila = "Cirug&iacute;a";
    if($tipo_acto == 'pqte')
    {
        $span_desc_fila = "Paquete";
        $arr_keys_conceptos = array(0=>"pqte");
        $arr_conceptos_manuales = array("pqte"=>"Porcentaje paquete");
    }

    $arr_cirugias_conceptos = array();
    if(count($arr_keys_conceptos) > 0)
    {
        $primer_concepto = $arr_keys_conceptos[0];

        $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;

        // Este es para diferenciar cuando se está insertando una nueva cirugía al presionar el boton de nueva cx o si simplemente
        // se están leyendo las cirugías ya guardadas y se quieren mostrar. En este último caso no entraría a este condicional
        if(isset($no_existe_en_bd) && $no_existe_en_bd == 'no_existe')
        {
            // Consultar la última posición creada de cirugía, consulta solo para un concepto, pués todos los conceptos deben tener las mismas
            // cantidades de cirugías.
            $sql = "SELECT  Liqncx AS numero_cirugia, id AS id_cirugia
                    FROM    {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                    WHERE   Liqclq = '{$wcodigo_manual}'
                            AND Liqtaq = '{$tipo_acto}'
                            AND Liqcxx = '{$codigo_acto_qx}'
                            AND Liqcon = '{$primer_concepto}'
                            AND Liqbrr <> 'on'
                    ORDER BY Liqncx DESC";
            $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
            $numero_cirugia_nueva     = '*';
            $numero_ultima_cirugia    = '';
            $id_numero_ultima_cirugia = '';
            $cirugias_cantidad = mysql_num_rows($result);
            if($cirugias_cantidad > 0)
            {
                // Para la última cirugía leída, se le asigna un número de consecutivo para actualizarlo en la base de datos, se supone que la última
                // cirugía debe tener (*) en su secuencia pero como se va a insertar una nuevo, es la nueva cirugía que debe quedar con (*)
                // y la anterior queda con un valor numérico, en esta sección se asigna a una variable el valor numérico que se debe asignar.
                $row = mysql_fetch_array($result);
                $numero_ultima_cirugia    = $cirugias_cantidad;
                $id_numero_ultima_cirugia = $row['id_cirugia'];
            }

            // inicializando la fila para mostrar campo de porcentajes de un número de cirugía
            foreach ($arr_conceptos_manuales as $wconcepto => $wnombre_concepto)
            {
                $numero_cirugia = ($numero_cirugia_nueva == '*') ? 'nxn': $numero_cirugia_nueva;
                if(!array_key_exists($numero_cirugia, $arr_cirugias_conceptos))
                {
                    $arr_cirugias_conceptos[$numero_cirugia] = array(); //array("id_cx" => '', "porcentaje" => '');
                }

                if(!array_key_exists($wconcepto, $arr_cirugias_conceptos[$numero_cirugia]))
                {
                    $arr_cirugias_conceptos[$numero_cirugia][$wconcepto] = array("id_cx" => '', "porcentaje" => '');
                }

                $arr_cirugias_conceptos[$numero_cirugia][$wconcepto]['id_cx']      = '';
                $arr_cirugias_conceptos[$numero_cirugia][$wconcepto]['porcentaje'] = '';

                // Se encarga de actualizar el número de cirugía para todos los conceptos que corresponden al manual y acto quirúrgico consultado
                if(!empty($id_numero_ultima_cirugia))
                {
                    $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                                    SET    Liqncx = '{$numero_ultima_cirugia}',
                                           Liqfed = '{$fecha_hora}',
                                           Seguridad = 'C-{$user_session}'
                            WHERE   Liqclq = '{$wcodigo_manual}'
                                    AND Liqtaq = '{$tipo_acto}'
                                    AND Liqcxx = '{$codigo_acto_qx}'
                                    AND Liqncx = '*'
                                    AND Liqbrr <> 'on'";
                    $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                }
            }
        }
        elseif(isset($no_existe_en_bd) && $no_existe_en_bd == 'incluir_cirugias')
        {
            // Consultar todas las cirugías de un manual y acto quirúrgico
            $sql = "SELECT  Liqncx AS numero_cirugia, id AS id_cirugia, Liqcon AS wconcepto, Liqpor AS porcentaje, Liqest AS estado, Liqvia AS via, Liqesp AS especialidad_bilat, Liqcmy AS hasta_la_cx_mayor
                    FROM    {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                    WHERE   Liqclq = '{$wcodigo_manual}'
                            AND Liqtaq = '{$tipo_acto}'
                            AND Liqcxx = '{$codigo_acto_qx}'
                            AND Liqbrr = 'off'
                    ORDER BY Liqcon, Liqncx ASC";
            $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);

            // $arr_primera_fila = array();

            if(mysql_num_rows($result) > 0)
            {
                $arr_asteriscos = array();
                while ($row = mysql_fetch_array($result))
                {
                    $numero_cirugia = ($row['numero_cirugia'] == '*') ? 'nxn': $row['numero_cirugia'];

                    // se guardan temporalmente en otro array las cirugias que sean asterisco.
                    if($numero_cirugia != 'nxn')
                    {
                        if(!array_key_exists($numero_cirugia, $arr_cirugias_conceptos))
                        {
                            $arr_cirugias_conceptos[$numero_cirugia] = array(); //array("id_cx" => '', "porcentaje" => '');
                        }

                        if(!array_key_exists($row['wconcepto'], $arr_cirugias_conceptos[$numero_cirugia]))
                        {
                            $arr_cirugias_conceptos[$numero_cirugia][$row['wconcepto']] = array("id_cx" => '', "porcentaje" => '', "estado" => '', "via" => '', "especialidad_bilat" => '', "hasta_la_cx_mayor" => '');
                        }

                        $arr_cirugias_conceptos[$numero_cirugia][$row['wconcepto']]['id_cx']              = $row['id_cirugia'];
                        $arr_cirugias_conceptos[$numero_cirugia][$row['wconcepto']]['porcentaje']         = $row['porcentaje'];
                        $arr_cirugias_conceptos[$numero_cirugia][$row['wconcepto']]['estado']             = $row['estado'];
                        $arr_cirugias_conceptos[$numero_cirugia][$row['wconcepto']]['via']                = $row['via'];
                        $arr_cirugias_conceptos[$numero_cirugia][$row['wconcepto']]['especialidad_bilat'] = $row['especialidad_bilat'];
                        $arr_cirugias_conceptos[$numero_cirugia][$row['wconcepto']]['hasta_la_cx_mayor']  = $row['hasta_la_cx_mayor'];
                    }
                    else
                    {
                        if(!array_key_exists($numero_cirugia, $arr_asteriscos))
                        {
                            $arr_asteriscos[$numero_cirugia] = array(); //array("id_cx" => '', "porcentaje" => '');
                        }

                        if(!array_key_exists($row['wconcepto'], $arr_asteriscos[$numero_cirugia]))
                        {
                            $arr_asteriscos[$numero_cirugia][$row['wconcepto']] = array("id_cx" => '', "porcentaje" => '', "estado" => '', "via" => '', "especialidad_bilat" => '', "hasta_la_cx_mayor" => '');
                        }

                        $arr_asteriscos[$numero_cirugia][$row['wconcepto']]['id_cx']              = $row['id_cirugia'];
                        $arr_asteriscos[$numero_cirugia][$row['wconcepto']]['porcentaje']         = $row['porcentaje'];
                        $arr_asteriscos[$numero_cirugia][$row['wconcepto']]['estado']             = $row['estado'];
                        $arr_asteriscos[$numero_cirugia][$row['wconcepto']]['via']                = $row['via'];
                        $arr_asteriscos[$numero_cirugia][$row['wconcepto']]['especialidad_bilat'] = $row['especialidad_bilat'];
                        $arr_asteriscos[$numero_cirugia][$row['wconcepto']]['hasta_la_cx_mayor']  = $row['hasta_la_cx_mayor'];
                    }
                }

                // las cirugias que son asterisco se unen al final del array arr_cirugias_conceptos, esto con el fin de que los asteriscos quedan al final del array y asi se muestren en la vista.
                foreach ($arr_asteriscos as $num_cx => $arr_resto)
                {
                    if(!array_key_exists($num_cx, $arr_cirugias_conceptos))
                    {
                        $arr_cirugias_conceptos[$num_cx] = array();
                    }
                    $arr_cirugias_conceptos[$num_cx] = $arr_resto;
                }
            }
        }

        // Se encarga de insertar una nueva fila para configurar porcentajes de cirugía, o también para recorrer
        // todas las filas que se leyeron para un acto quirúrgico.
        foreach ($arr_cirugias_conceptos as $numero_cirugia => $arr_datos_porcent)
        {
            // foreach para leer todos los conceptos configurados y por cada uno de ellos hacer un insert en la tabla de porcentajes
            $html_li            = '';
            $estado             = '';
            $wvia               = '';
            $especialidad_bilat = '';
            $hasta_la_cx_mayor  = '';
            $arr_ids_nuevos     = array();
            foreach ($arr_conceptos_manuales as $wconcepto => $wnombre_concepto)
            {
                $valor_porcentaje    = '0'; // Valor por defecto de porcentaje.
                $estado_grupo        = '';
                $wvia_grupo          = '';
                $wespecialidad_grupo = '';
                // Este es para diferenciar cuando se está insertando una nueva cirugía al presionar el boton de nueva cx o si simplemente
                // se están leyendo las cirugías ya guardadas y se quieren mostrar
                if(isset($no_existe_en_bd) && $no_existe_en_bd == 'no_existe')
                {
                    // Insertar un nuevo acto quirúrgico con el código consultado anteriormente aumentado en 1
                    $sql = "INSERT INTO {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                                    (Medico, Fecha_data, Hora_data, Liqclq, Liqtaq, Liqcxx,
                                    Liqcon, Liqncx, Liqpor, Liqest, Seguridad)
                            VALUES
                                    ('{$wbasedato}', '{$fecha_data}', '{$hora_data}', '{$wcodigo_manual}', '{$tipo_acto}', '{$codigo_acto_qx}',
                                    '{$wconcepto}', '{$numero_cirugia_nueva}', '0', 'on', 'C-{$user_session}')";
                    $result       = mysql_query($sql,$conex) or die (mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    $id_cx        = mysql_insert_id(); // Para cambiar el valor de porcentaje en la tabla de base de datos que contenga este ID
                    $estado_grupo = "on";
                }
                else
                {
                    if(!array_key_exists($wconcepto, $arr_datos_porcent))
                    {
                        // echo $id_manual."<<< <div style='text-align:left;'><pre>"; print_r($arr_datos_porcent); echo "</pre></div>";
                        $id_cx            = "";
                        $valor_porcentaje = "";
                    }
                    else
                    {
                        $id_cx               = $arr_datos_porcent[$wconcepto]['id_cx'];
                        $valor_porcentaje    = $arr_datos_porcent[$wconcepto]['porcentaje'];
                        $estado_grupo        = $arr_datos_porcent[$wconcepto]['estado'];
                        $wvia_grupo          = $arr_datos_porcent[$wconcepto]['via'];
                        $wespecialidad_grupo = $arr_datos_porcent[$wconcepto]['especialidad_bilat'];
                        $hasta_la_cx_mayor   = $arr_datos_porcent[$wconcepto]['hasta_la_cx_mayor'];
                    }
                }

                if(empty($estado))
                {
                    // Realmente se leen tantos registros como conceptos existan en el array de conceptos de manuales, pero todos los registros para un número de cirugía
                    // por cada concepto deben estar con el mismo estado, por ejemplo si para la cirugia 1 se tienen registros por cada concepto,
                    // Honorarios, ayudante, sala, anestesia,   si leo el estado para honorarios entonces ese mismo estado debe estar igual que todos los estados de los demás conceptos
                    // para ese mismo número de cirugía, es por eso que al leer el primer estado se asume que los demás registros también deben tener el mismo estado, y si inactivo una cirugía
                    // entonces tengo que inactivar todos los registros de cada concepto para esa cirugía.
                    $estado = $estado_grupo;
                }

                if(empty($wvia))
                {
                    $wvia = $wvia_grupo;
                }

                if(empty($especialidad_bilat))
                {
                    $especialidad_bilat = $wespecialidad_grupo;
                }

                if(!empty($id_cx))
                {
                    $arr_ids_nuevos[] = $id_cx;
                }

                $id_html_cx = $tipo_acto.$id_manual.'_'.$id_acto_qx.'_'.$wconcepto.'_'.$id_cx;

                // Si $valor_porcentaje es '' (vacío) es porque esa cirugía no tiene porcentaje configurado para ese concepto, (todos deberían tener porcentaje por cada concepto asi sea en valor cero (0))

                $chk_hasta_cx_mayor = (isset($hasta_la_cx_mayor) && $hasta_la_cx_mayor == 'on') ? 'checked="checked"': '';

                $check_hastaCxMayor = '<input '.$chk_hasta_cx_mayor.' type="checkbox" value="on" id="cirugia_mayor_'.$id_html_cx.'" name="cirugia_mayor_'.$id_html_cx.'" wconcepto="'.$wconcepto.'" id_registro="'.$id_cx.'" class="onBlurGrabar msj_tooltip"  actualiza="cirugia_mayor" id_manual="'.$id_manual.'" id_acto_qx="'.$id_acto_qx.'" tipo_acto="'.$tipo_acto.'" title="Solo se paga hasta esta cirug&iacute;a mayor sin importar la especialidad" >';
                if($tipo_acto == "urg")
                {
                    $check_hastaCxMayor = '';
                }

                // Este es el campo donde se escribe el porcentaje, esta dentro de una etiqueta li que hará parte de una columna dentro de un ul que se imprime más abajo.
                $html_li .= '<li>
                                <input type="text" id="wpocentaje_'.$id_html_cx.'" name="wpocentaje_'.$id_html_cx.'" wconcepto="'.$wconcepto.'" validar="porcentaje" id_registro="'.$id_cx.'" value="'.$valor_porcentaje.'" class="onBlurGrabar numerico" actualiza="cirugia" size="3" placeholder="%" id_manual="'.$id_manual.'" id_acto_qx="'.$id_acto_qx.'" tipo_acto="'.$tipo_acto.'" >
                                '.$check_hastaCxMayor.'
                            </li>';
            }

            if(count($arr_ids_nuevos) > 0)
            {
                $view_activo   = 'none';
                $view_inactivo = '';
                $desc_boton = "Activar";
                if($estado == 'on')
                {
                    $view_activo   = '';
                    $view_inactivo = 'none';
                    $desc_boton = "Inactivar";
                }

                $via_igual              = ($wvia == '1') ? 'selected="selected"': '';
                $via_diferente          = ($wvia == '*') ? 'selected="selected"': '';
                $especialidad_igual     = ($especialidad_bilat == '1') ? 'selected="selected"': '';
                $especialidad_diferente = ($especialidad_bilat == '*') ? 'selected="selected"': '';


                // $numero_cirugia_nueva = str_replace("*", "nxn", $numero_cirugia_nueva);
                $numero_cirugia = str_replace("nxn", "*", $numero_cirugia);
                $ids_cx_conceptos = implode("_", $arr_ids_nuevos); // para saber a que registros en la base de datos se deben modificar que coinciden con estos IDs en la tabla _000188
                $id_html_cx = $tipo_acto.$id_manual.'_'.$id_acto_qx.'_'.$ids_cx_conceptos; // para diferenciar los ids de las etiquetas ul

                $titulo = '<div style=\'color: red; font-weight:bold;font-size:8pt;\' >[Eliminar acto quir&uacute;rgico]</div>';
                $btn_eliminar = '<img id="img_eliminarCx_'.$id_html_cx.'_activo" style="cursor:pointer;" border="0" src="../../images/medical/eliminar1.png" width="10" height="10" style="display:;" class="msj_tooltip" title="'.$titulo.'" onclick="eliminarCirugia(\''.$wcodigo_manual.'\',\''.$codigo_acto_qx.'\',\''.$id_manual.'\',\''.$id_acto_qx.'\',\''.$ids_cx_conceptos.'\',\''.$tipo_acto.'\');" >';

                $visible_via = ($bilateral=='on') ? '': 'none';
                $html .= '  <ul id="'.$id_html_cx.'" class="columnas_conceptos fila1 cirugias_acto_qx" cx_posicion="'.$numero_cirugia.'" ids_cxs="'.$ids_cx_conceptos.'" >
                                        <li><input type="hidden" value="1" id="" >'.$span_desc_fila.' <span id="spn_num_cx_'.$id_html_cx.'" >'.$numero_cirugia.'</span></li>
                                        '.$html_li.'
                                        <li class="columna_vias_bilateral" style="width: 170px;">
                                            <div class="divtabla_vias_bilateral_'.$tipo_acto.$id_manual.'_'.$id_acto_qx.'"  style="display:'.$visible_via.';">
                                                <div style="display:inline-flex;" >
                                                    <table align="center" >
                                                        <tr>
                                                            <td style="text-align:left;" ><span class="msj_tooltip" title="V&iacute;a">V&iacute;a: </span></td>
                                                            <td>
                                                                <select class="div_vias_bilateral_'.$tipo_acto.$id_manual.'_'.$id_acto_qx.'" name="sel_via_bilateral_'.$id_html_cx.'" id="sel_via_bilateral_'.$id_html_cx.'" campo="Liqvia" onchange="actualizarViaBilateralCx(this,\''.$id_manual.'\',\''.$id_acto_qx.'\',\''.$ids_cx_conceptos.'\');" >
                                                                    <option value="">Seleccione</option>
                                                                    <option value="1" '.$via_igual.' >Igual</option>
                                                                    <option value="*" '.$via_diferente.' >Diferente</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:left;" ><span class="msj_tooltip" title="Especialidad" >Especialidad: </span></td>
                                                            <td>
                                                                <select class="div_esp_bilateral_'.$tipo_acto.$id_manual.'_'.$id_acto_qx.'" name="sel_especialid_bilateral_'.$id_html_cx.'" id="sel_especialid_bilateral_'.$id_html_cx.'" campo="Liqesp" onchange="actualizarViaBilateralCx(this,\''.$id_manual.'\',\''.$id_acto_qx.'\',\''.$ids_cx_conceptos.'\');" >
                                                                    <option value="">Seleccione</option>
                                                                    <option value="1" '.$especialidad_igual.' >Igual</option>
                                                                    <option value="*" '.$especialidad_diferente.' >Diferente</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </li>
                                        <li style="width: 170px;">
                                            <div>
                                                <table align="center" >
                                                    <tr>
                                                        <td>
                                                            <img id="img_estado_cirugia_'.$id_html_cx.'_activo" border="0" src="../../images/medical/root/activo.gif" width="10" height="10" style="display:'.$view_activo.';" >
                                                            <img id="img_estado_cirugia_'.$id_html_cx.'_inactivo" border="0" src="../../images/medical/root/inactivo.gif" width="10" height="10" style="display:'.$view_inactivo.';" >
                                                            <input type="button" id="btn_estado_cirugia_'.$id_html_cx.'" estado="'.$estado.'" onclick="cambiarEstadoCirugias(this,\''.$id_manual.'\',\''.$id_acto_qx.'\',\''.$ids_cx_conceptos.'\',\''.$tipo_acto.'\')" value="'.$desc_boton.'">
                                                            ['.$btn_eliminar.']
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </li>
                                    </ul>';
            }
            /*else
            {
                $data['error'] = 1;
                $data['mensaje'] = "No se pudo crear la siguiente fila para configurar número de cirugía.";
                //$data['log_error'] = (mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
            }*/
        }
    }

    return $html;
}


function crearHtmlActoQx($conex, $wbasedato, $wemp_pmla, $user_session, $arr_conceptos_manuales, $id_manual, $wcodigo_manual, $id_acto_qx, $tipo_acto, $codigo_acto_qx, $via, $especialista, $bilateral, $estado, $description, $incluir = "")
{
    $id_html_acto_qx = $tipo_acto.$id_manual.'_'.$id_acto_qx;

    $arr_via_select[1]          = ($via == '') ? 'selected="selected"': '';
    $arr_via_select[2]          = ($via == '1') ? 'selected="selected"': '';
    $arr_via_select[3]          = ($via == '*') ? 'selected="selected"': '';

    $arr_especialista_select[1] = ($especialista == '') ? 'selected="selected"': '';
    $arr_especialista_select[2] = ($especialista == '1') ? 'selected="selected"': '';
    $arr_especialista_select[3] = ($especialista == '*') ? 'selected="selected"': '';

    $verbilateral = "";
    $ver_via_especialidad = "";
    $es_bilateral = '';
    if($bilateral == 'on')
    {
        $es_bilateral         = 'checked=""checked';
        $ver_via_especialidad = "display:none;";
    }

    // Si bilateral es vacío entonces consultar entre los actos quirúrgicos del manual actual para verificar si ya hay uno bilateral, si esó entonces no pintar los campos de vías y especialista.
    if(!empty($wcodigo_manual))
    {
        $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
        $TB_BASE_LIQ_ACTOSQX = TB_BASE_LIQ_ACTOSQX;
        $sql = "SELECT  c187.Cxxblq, c187.Cxxcod AS codigo_acto_qx, c187.id AS id_acto_qx, c187.Cxxnvi AS via, c187.Cxxnsp AS especialista, c187.Cxxbil AS bilateral, c187.Cxxest AS estado
                        , c187.Cxxdes AS descripcion
                FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION} AS c186
                        INNER JOIN
                        {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX} AS c187 ON (c186.Blqclq = c187.Cxxblq AND c187.Cxxtaq = '{$tipo_acto}')
                WHERE   c186.id = '{$id_manual}'
                        AND c187.Cxxbrr = 'off'
                        AND c187.Cxxbil = 'on'
                ORDER BY c187.Cxxcod ASC";
        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);

        // Si hay bilaterales y no es el actual entonces ocultar el campo check bilateral
        if(mysql_num_rows($result) > 0 && $bilateral != 'on')
        {
            $verbilateral = "none";
        }
    }

    // if($bilateral == 'off') { $verbilateral = "none"; }

    // Esta subrutina es para poder crear una lista de cirugias ya configuradas para un acto quirurgico y manual específico, por ejemplo para crear la lista de manual ya
    // configurados, en el momento en que la página html se carga por primera vez.
    $html_lista_cirugias = "";
    if($incluir == "incluir_cirugias")
    {
        $html_lista_cirugias = crearHtmlFilaCirugia($conex, $wbasedato, $wemp_pmla, $arr_conceptos_manuales, "incluir_cirugias", $id_manual, $id_acto_qx, $wcodigo_manual, $codigo_acto_qx, $tipo_acto, '', '', $user_session, '', $bilateral);
    }

    $titulo = '<div style=\'color: red; font-weight:bold;font-size:8pt;\' >[Eliminar acto quir&uacute;rgico]</div>';
    $btn_eliminar = '<img id="img_eliminarActo_qx_'.$id_html_acto_qx.'_activo" style="cursor:pointer;" border="0" src="../../images/medical/eliminar1.png" width="10" height="10" style="display:;" class="msj_tooltip" title="'.$titulo.'" onclick="eliminarActoQx(\''.$wcodigo_manual.'\',\''.$codigo_acto_qx.'\',\''.$id_manual.'\',\''.$id_acto_qx.'\',\''.$tipo_acto.'\');" >';

    $view_activo   = 'none';
    $view_inactivo = '';
    $desc_boton = "Activar";
    if($estado == 'on')
    {
        $view_activo   = '';
        $view_inactivo = 'none';
        $desc_boton = "Inactivar";
    }

    $verbilateral = ($tipo_acto == 'urg') ? "none": $verbilateral;

    $html = '  <!-- TABLA DE UN NUEVO ACTO QUIRURGICO -->
                <table align="center" style="width:100%;" class="tabla_acto_qx" id="tabla_acto_qx_'.$id_html_acto_qx.'" >
                    <tr>
                        <td style="width: 72px; padding-top: 40px;" align="center" valign="top" >
                            <img id="img_estado_acto_qx_'.$id_html_acto_qx.'_activo" border="0" src="../../images/medical/root/activo.gif" width="10" height="10" style="display:'.$view_activo.';" >
                            <img id="img_estado_acto_qx_'.$id_html_acto_qx.'_inactivo" border="0" src="../../images/medical/root/inactivo.gif" width="10" height="10" style="display:'.$view_inactivo.';" >
                            <button id="btn_estado_acto_qx_'.$id_html_acto_qx.'" onclick="cambiarEstadoActoQx(this, \''.$id_manual.'\',\''.$id_acto_qx.'\',\''.$tipo_acto.'\');" estado="'.$estado.'" >'.$desc_boton.'</button>
                            '.$btn_eliminar.'
                        </td>
                        <td style="width: 13px;" class="td_img">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td style="width: 225px;" valign="top" align="left" >
                            <ul class="encabezadoTabla columnas_descripcion_acto_qx" style="font-size: 8pt;" >
                                <li> Descripci&oacute;n:</li>
                                <li><textarea id="wnombre_acto_qx_'.$id_html_acto_qx.'" name="wnombre_acto_qx_'.$id_html_acto_qx.'" id_registro="'.$id_acto_qx.'" class="onBlurGrabar" actualiza="desc_acto" style="font-size: 8pt; width: 129px; height: 45px; text-align: center;" >'./*MIGRA_2*/$description.'</textarea></li>
                            </ul>
                            <ul class="encabezadoTabla columnas_descripcion_acto_qx" style="font-size: 8pt; display:'.$verbilateral.';">
                                <li> Bilateral:</li>
                                <li><input type="checkbox" value="on" id="wbilateral_'.$id_html_acto_qx.'" name="wbilateral_'.$id_html_acto_qx.'" onclick="ocultarOtrosCamposBilateral(this,\'wbilateral_\',\''.$id_manual.'\',\''.$tipo_acto.'\');" '.$es_bilateral.' class="onBlurGrabar" id_registro="'.$id_acto_qx.'" actualiza="acto_bilateral" id_acto_qx="'.$id_acto_qx.'" id_manual="'.$id_manual.'" tipo_acto="'.$tipo_acto.'" ></li>
                            </ul>
                            <ul class="encabezadoTabla columnas_descripcion_acto_qx via_esp_'.$id_html_acto_qx.'" style="font-size: 8pt; '.$ver_via_especialidad.'" >
                                <li> V&iacute;as:</li>
                                <li>
                                    <select name="wnumero_via_'.$id_html_acto_qx.'" chk_bilat="wbilateral_'.$id_html_acto_qx.'" id="wnumero_via_'.$id_html_acto_qx.'" class="ancho_campos_descripcion" onchange="actualizarRegistro(this)" actualiza="numero_via" id_registro="'.$id_acto_qx.'" >
                                        <option value="" '.$arr_via_select[1].'>Seleccione..</option>
                                        <option value="1" '.$arr_via_select[2].'>Igual v&iacute;a</option>
                                        <option value="*" '.$arr_via_select[3].'>Diferente v&iacute;a</option>
                                    </select>
                                </li>
                            </ul>
                            <ul class="encabezadoTabla columnas_descripcion_acto_qx via_esp_'.$id_html_acto_qx.'" style="font-size: 8pt; '.$ver_via_especialidad.'" >
                                <li> Especialista:</li>
                                <li>
                                    <select name="wnumero_especialistas_'.$id_html_acto_qx.'" chk_bilat="wbilateral_'.$id_html_acto_qx.'" id="wnumero_especialistas_'.$id_html_acto_qx.'" class="ancho_campos_descripcion" onchange="actualizarRegistro(this)" actualiza="numero_especialista" id_registro="'.$id_acto_qx.'" >
                                        <option value="" '.$arr_especialista_select[1].' >Seleccione..</option>
                                        <option value="1" '.$arr_especialista_select[2].' >Igual especialista</option>
                                        <option value="*" '.$arr_especialista_select[3].' >Diferente especialista</option>
                                    </select>
                                </li>
                            </ul>
                        </td>
                        <td style="border-left: 2px solid #2A5DB0;border-right: 2px solid #2A5DB0;" valign="top" align="left">
                            <div id="div_cirugias_acto_qx_'.$id_html_acto_qx.'" class="div_cirugias_acto_qx">
                                <!-- AQUÍ VAN LAS CIRUGIAS -->
                                '.$html_lista_cirugias.'
                            </div>
                            <button onclick="crearNuevaCirugia(\''.$id_manual.'\',\''.$id_acto_qx.'\',\''.$wcodigo_manual.'\',\''.$codigo_acto_qx.'\',\''.$tipo_acto.'\');" >Agregar Cirug&iacute;a</button>
                        </td>
                    </tr>
                </table>
                <!-- TABLA DE UN NUEVO ACTO QUIRURGICO -->';
    return $html;
}


function clasesExcluidasFacturables($implode_clases, $arr_maestro_clases_insumos, $campo_tabla, $windentificador, $id_manual, $adicionar_clase = 'off')
{
    $windentificador = ($adicionar_clase == 'on') ? $windentificador : $windentificador.'_'.$id_manual;
    $expl_clases = (!empty($implode_clases)) ? explode("|", $implode_clases) : array();
    $cantidad_excluido = count($expl_clases);

    $html_clases = "";
    foreach ($expl_clases as $key => $value)
    {
        $nombre_ex = 'No esta en maestro';
        if(array_key_exists($value, $arr_maestro_clases_insumos))
        {
            $nombre_ex = $value.'-'.$arr_maestro_clases_insumos[$value];
        }
        $html_clases .= '<div id="div_check_clase_'.$windentificador.'_'.$value.'" ><input type="checkbox" checked="checked" value="'.$value.'" onclick="validarGuardarInsumosExcluidos(this, \''.$windentificador.'\', \''.$id_manual.'\', \'\')" />'.utf8_encode($nombre_ex).'</div>';
    }

    if($adicionar_clase == 'off')
    {
        $html ='<div style="text-align:center;" ><a href="javascript:verOcultarLista(\''.$windentificador.'\')" >Ver / Ocultar lista (<span id="excluidos_sum_'.$windentificador.'">'.$cantidad_excluido.'</span>)</a></div>
                <input type="hidden" id="hdn_campo_'.$windentificador.'" name="hdn_campo_'.$windentificador.'" value="'.$campo_tabla.'" />
                <div id="'.$windentificador.'" style="display:none;" >
                    <div id="div_agregar_clase_'.$windentificador.'" >
                        <span>Excluir:</span><span> <input type="text" class="clase_excluidos" id="wcod_clase_'.$windentificador.'" name="wcod_clase_'.$windentificador.'" windentificador="'.$windentificador.'" id_manual="'.$id_manual.'" codigo="" nombre="" /> </span>
                    </div>
                    <div id="div_checks_clases_'.$windentificador.'" style="height: 80px; overflow:auto; background-color: #FEFEFE;">
                        '.$html_clases.'
                    </div>
                </div>';
    }
    else
    {
        $html = $html_clases;
    }
    return $html;
}

function procedimientosListaFacturables($wprocedimiento_NoFact, $codigo_procedimiento, $arr_procedimientos, $id_manual, $id_relacion, $adicionar_procedimiento = 'off')
{
    // $windentificador = $id_manual.'_'.$id_relacion;
    $expl_procedimientos = (!empty($codigo_procedimiento)) ? explode("|", $codigo_procedimiento) : array();

    $html_relacion = "";
    foreach ($expl_procedimientos as $key => $value)
    {
        $nombre_ex = 'No esta en maestro';
        if(array_key_exists($value, $arr_procedimientos))
        {
            $nombre_ex = $value.'-'.$arr_procedimientos[$value];
        }
        // $html_relacion .= '<div id="div_check_clase_'.$windentificador.'_'.$value.'" ><input type="checkbox" checked="checked" value="'.$value.'" onclick="validarGuardarInsumosExcluidos(this, \''.$windentificador.'\', \''.$id_manual.'\', \'\')" />'.utf8_encode($nombre_ex).'</div>';
        $html_relacion .= '<div id="div_procedimiento_anula_'.$id_manual.'_'.$id_relacion.'_'.$value.'"><input type="checkbox" checked="checked" value="'.$value.'" id="chk_procedimiento_lista_'.$id_manual.'_'.$id_relacion.'_'.$value.'"  onclick="validarGuardarProcedimientoLista(this, \''.$id_manual.'\', \''.$id_relacion.'\', \''.$value.'\')" >'.$nombre_ex.'</div>';
    }

    if($adicionar_procedimiento == 'off')
    {
        $value_procedimiento = (array_key_exists($wprocedimiento_NoFact, $arr_procedimientos)) ? $wprocedimiento_NoFact.'-'.$arr_procedimientos[$wprocedimiento_NoFact]: '';
        $class_tr = "tr_relacion_{$id_manual}_{$id_relacion}";
        $html ='<tr class="'.$class_tr.'">
                    <td class="td_img2" style="width: 70px;">
                        <button style="cursor:pointer;" onclick="eliminarRelacionProcedimiento(\''.$id_relacion.'\',\''.$class_tr.'\');" >Eliminar</button>
                    </td>
                    <td class="fila1" valign="top" >
                        <input type="text" size="30" id="txt_procedimiento_NFac_'.$id_manual.'_'.$id_relacion.'" id_registro="'.$id_relacion.'" actualiza="procedimiento_no_fact" class="onBlurGrabar procedimiento_relacion procedimiento_relacion_'.$id_manual.'" codigo="'.$wprocedimiento_NoFact.'" nombre="'.$value_procedimiento.'" value="'.$value_procedimiento.'" >
                    </td>
                    <td class="fila1" >
                        Agregar: <input placeholder="Agregar procedimiento" type="text" class="procedimiento_relacion_lista" id="txt_procedimiento_relacion_lista_'.$id_manual.'_'.$id_relacion.'" id_manual="'.$id_manual.'" id_relacion="'.$id_relacion.'" codigo="" nombre="" >
                        <div id="div_lista_procedimientos_anulan_'.$id_manual.'_'.$id_relacion.'" style="height: 80px; overflow:auto;" >
                            '.$html_relacion.'
                        </div>
                    </td>
                </tr>';
    }
    else
    {
        $html = $html_relacion;
    }
    return $html;
}


function crearHtmlManualEncabezado($conex, $wbasedato, $wemp_pmla, $user_session, $cont_mnl, $arr_conceptos_manuales, $wcodigo_manual, $id_manual, $arr_entidades, $arr_maestro_clases_insumos, $arr_procedimientos, &$data, $incluir = "")
{
    $html                          = "";
    $wnombre_manual                = 'Nuevo Manual';
    $wtipoempresa                  = '';
    $wcentro_costo                 = '';
    $wempresa                      = '';
    $wpolitrauma                   = '';
    $wliquida_tiempos              = '';
    $wvalidar_por_articulo         = '';
    $wredondear_centena            = '';
    $winsumos_no_facturables       = '';
    $data['wempresa']              = '*';
    $data['wempresa_nombre']       = '*-TODOS';
    $data['wprocedimiento_manual'] = '*';
    $data['wprocedimiento_manualNombre'] = '*-TODOS';
    $data['wpolitrauma']           = 'off';
    $estado                        = 'off';

    //Límites de uvr y grupos qx
    $wlimite_uvr_nofac                  = '';
    $wlimite_material_uvr_nofac         = '';
    $wconcepto_cobrar_uvr               = '';
    $wconcepto_cobrar_material_uvr      = '';
    $wuvr_maximo_nof_ayudante           = '';
    $wgrupo_maximo_nof_ayudante         = '';
    $wlimite_grupoqx                    = '';
    $wlimite_material_grupoqx           = '';
    $wconcepto_cobrar_grupo_qx          = '';
    $wconcepto_cobrar_material_grupo_qx = '';

    $implode_clases_medicamentos_uvr          = '';
    $implode_clases_materiales_uvr            = '';
    $implode_clases_medicamentos_grupo        = '';
    $implode_clases_materiales_grupo          = '';

    $implode_clases_NoFact_medicamentos_uvr   = '';
    $implode_clases_NoFact_materiales_uvr     = '';
    $implode_clases_NoFact_medicamentos_grupo = '';
    $implode_clases_NoFact_materiales_grupo   = '';

    $implode_procedimientos_relacionados = array();

    $arr_conceptos = obtener_array_conceptos();

    $bloquear_campos = "";
    if(!empty($wcodigo_manual))
    {
        $bloquear_campos = 'disabled="disabled"';
        $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
        //consultar parámetros principales del manual
        $sql = "SELECT  Blqdes AS wnombre_manual, Blqcco AS wcentro_costo, Blqtem AS wtipoempresa, Blqemp AS wempresa, Blqpol AS wpolitrauma, Blqpro AS wprocedimiento_manual, Blqtie AS wliquida_tiempos, Blqape AS wvalidar_por_articulo, Blqrce AS wredondear_centena, Blqanf AS winsumos_no_facturables, Blqest AS estado,
                        Blquvr AS wlimite_uvr_nofac, Blqccu AS wconcepto_cobrar_uvr, Blqlgr AS wlimite_grupoqx, Blqccg AS wconcepto_cobrar_grupo_qx,
                        Blqmgr AS wlimite_material_grupoqx, Blqcmg AS wconcepto_cobrar_material_grupo_qx,
                        Blqmuv AS wlimite_material_uvr_nofac, Blqcmu AS wconcepto_cobrar_material_uvr,
                        Blqauv AS wuvr_maximo_nof_ayudante, Blqagr AS wgrupo_maximo_nof_ayudante,
                        Blqcma AS implode_clases_medicamentos_uvr, Blqcmb AS implode_clases_materiales_uvr, Blqcmc AS implode_clases_medicamentos_grupo, Blqcmd AS implode_clases_materiales_grupo,
                        Blqnfa AS implode_clases_NoFact_medicamentos_uvr, Blqnfb AS implode_clases_NoFact_materiales_uvr, Blqnfc AS implode_clases_NoFact_medicamentos_grupo, Blqnfd AS implode_clases_NoFact_materiales_grupo
                FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION}
                WHERE   Blqclq = '{$wcodigo_manual}'";

        if($result = mysql_query($sql,$conex))
        {
            $row = mysql_fetch_array($result);
            $wnombre_manual                     = utf8_encode($row['wnombre_manual']);
            $wcentro_costo                      = $row['wcentro_costo'];
            $wtipoempresa                       = $row['wtipoempresa'];
            $wempresa                           = $row['wempresa'];
            $wprocedimiento_manual              = $row['wprocedimiento_manual'];
            $wpolitrauma                        = $row['wpolitrauma'];
            $wliquida_tiempos                   = $row['wliquida_tiempos'];
            $wvalidar_por_articulo              = $row['wvalidar_por_articulo'];
            $wredondear_centena                 = $row['wredondear_centena'];
            $winsumos_no_facturables            = $row['winsumos_no_facturables'];
            $estado                             = $row['estado'];
            $wlimite_uvr_nofac                  = $row['wlimite_uvr_nofac'];
            $wlimite_material_uvr_nofac         = $row['wlimite_material_uvr_nofac'];
            $wconcepto_cobrar_uvr               = $row['wconcepto_cobrar_uvr'];
            $wconcepto_cobrar_material_uvr      = $row['wconcepto_cobrar_material_uvr'];
            $wuvr_maximo_nof_ayudante           = $row['wuvr_maximo_nof_ayudante'];
            $wgrupo_maximo_nof_ayudante         = $row['wgrupo_maximo_nof_ayudante'];
            $wlimite_grupoqx                    = $row['wlimite_grupoqx'];
            $wlimite_material_grupoqx           = $row['wlimite_material_grupoqx'];
            $wconcepto_cobrar_grupo_qx          = $row['wconcepto_cobrar_grupo_qx'];
            $wconcepto_cobrar_material_grupo_qx = $row['wconcepto_cobrar_material_grupo_qx'];

            $implode_clases_medicamentos_uvr          = $row['implode_clases_medicamentos_uvr'];
            $implode_clases_materiales_uvr            = $row['implode_clases_materiales_uvr'];
            $implode_clases_medicamentos_grupo        = $row['implode_clases_medicamentos_grupo'];
            $implode_clases_materiales_grupo          = $row['implode_clases_materiales_grupo'];

            $implode_clases_NoFact_medicamentos_uvr   = $row['implode_clases_NoFact_medicamentos_uvr'];
            $implode_clases_NoFact_materiales_uvr     = $row['implode_clases_NoFact_materiales_uvr'];
            $implode_clases_NoFact_medicamentos_grupo = $row['implode_clases_NoFact_medicamentos_grupo'];
            $implode_clases_NoFact_materiales_grupo   = $row['implode_clases_NoFact_materiales_grupo'];

            $arr_entidades           = unserialize(base64_decode($arr_entidades));
            // El (*) no existe en el array de entidades por eso se hace esta validación, en ese caso, estas variables se inicializar desde el principio con (*)
            if($wempresa != '*')
            {
                $data['wempresa']        = $wempresa;
                $data['wempresa_nombre'] = $wempresa.'-'.utf8_encode($arr_entidades[$wempresa]);
            }
            if($wprocedimiento_manual != '*')
            {
                $data['wprocedimiento_manual']        = $wprocedimiento_manual;
                $data['wprocedimiento_manualNombre'] = $wprocedimiento_manual.'-'.utf8_encode($arr_procedimientos[$wprocedimiento_manual]);
            }
            $data['wpolitrauma']     = $wpolitrauma;

            //CONSULTAR LOS PROCEDIMIENTOS RELACIONADOS PARA EL MANUAL QUE SE ACABA DE LEER
            //
            // $implode_procedimientos_relacionados =
            $sql = "SELECT  id AS id_relacion, Rnfpro AS wprocedimiento, Rnfrel AS procedimientos_relacionados
                    FROM    {$wbasedato}_000225
                    WHERE   Rnfman = '{$wcodigo_manual}'";

            if($result = mysql_query($sql,$conex))
            {
                while($row = mysql_fetch_array($result))
                {
                    if(!array_key_exists($row['id_relacion'], $implode_procedimientos_relacionados))
                    {
                        $implode_procedimientos_relacionados[$row['id_relacion']] = array("wprocedimiento"=>"", "procedimientos_relacionados"=>"");
                    }
                    $implode_procedimientos_relacionados[$row['id_relacion']] = array("wprocedimiento"=>$row['wprocedimiento'], "procedimientos_relacionados"=>$row['procedimientos_relacionados']);
                }
            }
        }
        else
        {
            $data['error'] = 1;
            $data['mensaje'] = "No se pudo leer de nuevo la informaci&oacute;n recientemente guardada.";
        }
    }

    $arr_centro_costo = arrayCentroCosto($conex, $wbasedato, $wemp_pmla);
    $optionsCentroCosto = '';
    $ocultarParametros = "";
    $tipo_liquidacion = "actividad";
    $color_manual = "#C007BC";
    foreach ($arr_centro_costo as $key => $value)
    {
        $selected = '';
        if($wcentro_costo == $key)
        {
            $selected = 'selected="selected"';
            if($value["tipo_cco"] != 'cir')
            {
                $color_manual = "#FEB413";
                $ocultarParametros = "display:none;";
                $tipo_liquidacion = $value["tipo_cco"];
            }
        }
        $optionsCentroCosto .= '<option value="'.$key.'" '.$selected.' tipocco="'.$value["tipo_cco"].'" >'.$key.'-'.utf8_encode($value["nombre"]).'</option>';
    }

    $arr_tipo_empresa = arrayTipoEmpresas($conex, $wbasedato, $wemp_pmla);
    $optionsTiposEmpresas = '';
    foreach ($arr_tipo_empresa as $key => $value)
    {
        $selected = '';
        if($wtipoempresa == $key)
        {
            $selected = 'selected="selected"';
        }
        $optionsTiposEmpresas .= '<option value="'.$key.'" '.$selected.' >'.$key.'-'.utf8_encode($value).'</option>';
    }

    $btn_nuevo_manual       = (empty($wcodigo_manual)) ? '': 'style="display:none;"'; // Para mostrar o no el botón de guardar y continuar
    $chk_tiempos            = ($wliquida_tiempos == 'on') ? 'checked="checked"' : '';
    $chk_valPorArticulo     = ($wvalidar_por_articulo == 'on') ? 'checked="checked"' : '';
    $chk_wredondear_centena = ($wredondear_centena == 'on') ? 'checked="checked"' : '';

    $visible_MsjNoFact = '';
    $visible_MsjSiFact = 'display:none;';
    $chk_insumos_noFact = '';
    if($winsumos_no_facturables == 'on') // Si por defecto los insumos deben ser todos no facturables, las clasificaciones o excepciones serán las SI facturables.
    {
        $chk_insumos_noFact =  'checked="checked"';
        $visible_MsjNoFact = 'display:none;';
        $visible_MsjSiFact = '';
    }

    // Para mostrar u ocultar el botón y sección para crear nuevos actos quirúrgicos
    $crearNuevoActoQx = 'none';
    $div_actos_quirurgicos = 'none';
    if($id_manual != '')
    {
        $crearNuevoActoQx = '';
        $div_actos_quirurgicos = '';
    }

    $columnas_conceptos = '';

    // Verifica si el centro de costo esta en el array de conceptos y se reescribe el array solo con los conceptos condfigurados para ese centro de costo.
    if(array_key_exists($wcentro_costo, $arr_conceptos_manuales))
    {
        $arr_conceptos_manuales = $arr_conceptos_manuales[$wcentro_costo];
    }

    foreach ($arr_conceptos_manuales as $wconcepto => $wnombre_concepto)
    {
        $columnas_conceptos .= '<li class="msj_tooltip" title="'.$wconcepto.'" >'.$wnombre_concepto.'</li>';
    }

    if(empty($columnas_conceptos))
    {
        $columnas_conceptos = "<li>NO HAY CONCEPTOS CONFIGURADOS</li>";
    }
    else
    {
        $columnas_conceptos = " <li>Cirug&iacute;a</li>
                                $columnas_conceptos
                                <li style='width: 170px;'>&nbsp;</li>
                                <li style='width: 170px;'>&nbsp;</li>";
    }

    // Si se indica que se quieren cargar todos los actos quirurgicos de un manual en especial, en esta subrutina se consultan y se crea el html correspondiente
    // en el caso por ejemplo, que solo sea agregar un acto quirúrgico en la edición, entonces no ingresará a este condicional.
    $html_lista_actos_qx          = "";
    $html_lista_actos_qx_paquetes = "";
    if($incluir == "incluir_actos_quirurgicos")
    {
        // Consultar los actos qx del manual a pintar.
        $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
        $TB_BASE_LIQ_ACTOSQX = TB_BASE_LIQ_ACTOSQX;
        // PROCEDIMIENTOS
        $sql = "SELECT  c187.Cxxblq, c187.Cxxtaq AS tipo_acto, c187.Cxxcod AS codigo_acto_qx, c187.id AS id_acto_qx, c187.Cxxnvi AS via, c187.Cxxnsp AS especialista, c187.Cxxbil AS bilateral, c187.Cxxest AS estado
                        , c187.Cxxdes AS descripcion
                FROM    {$wbasedato}_000186 AS c186
                        INNER JOIN
                        {$wbasedato}_000187 AS c187 ON (c186.Blqclq = c187.Cxxblq)
                WHERE   c186.id = '{$id_manual}'
                        AND c187.Cxxbrr = 'off'
                        AND c187.Cxxtaq = '{$tipo_liquidacion}'
                ORDER BY c187.Cxxcod ASC";
        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);

        if(mysql_num_rows($result) > 0)
        {
            while ($row = mysql_fetch_array($result))
            {
                $html_lista_actos_qx .= crearHtmlActoQx($conex, $wbasedato, $wemp_pmla, $user_session, $arr_conceptos_manuales, $id_manual, $wcodigo_manual, $row['id_acto_qx'], $row['tipo_acto'], $row['codigo_acto_qx'], $row['via'], $row['especialista'], $row['bilateral'], $row['estado'], $row['descripcion'], "incluir_cirugias");
            }
        }

        // PAQUETES
        $sql = "SELECT  c187.Cxxblq, c187.Cxxtaq AS tipo_acto, c187.Cxxcod AS codigo_acto_qx, c187.id AS id_acto_qx, c187.Cxxnvi AS via, c187.Cxxnsp AS especialista, c187.Cxxbil AS bilateral, c187.Cxxest AS estado
                        , c187.Cxxdes AS descripcion
                FROM    {$wbasedato}_000186 AS c186
                        INNER JOIN
                        {$wbasedato}_000187 AS c187 ON (c186.Blqclq = c187.Cxxblq)
                WHERE   c186.id = '{$id_manual}'
                        AND c187.Cxxbrr = 'off'
                        AND c187.Cxxtaq = 'pqte'
                ORDER BY c187.Cxxcod ASC";
        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);

        if(mysql_num_rows($result) > 0)
        {
            while ($row = mysql_fetch_array($result))
            {
                $html_lista_actos_qx_paquetes .= crearHtmlActoQx($conex, $wbasedato, $wemp_pmla, $user_session, $arr_conceptos_manuales, $id_manual, $wcodigo_manual, $row['id_acto_qx'], $row['tipo_acto'], $row['codigo_acto_qx'], $row['via'], $row['especialista'], $row['bilateral'], $row['estado'], $row['descripcion'], "incluir_cirugias");
            }
        }
    }

    // Opciones para el concepto a cobrar en limite de UVR de medicamentos
    $options_conceptos_uvr = "";
    foreach ($arr_conceptos as $key => $value)
    {
        $sltd = ($wconcepto_cobrar_uvr == $key) ? 'selected="selected"': '';
        $options_conceptos_uvr .= '<option value="'.$key.'" '.$sltd.' >'.$key.'-'.$value.'</option>';
    }

    // Opciones para el concepto a cobrar en limite de UVR de material
    $options_conceptos_material_uvr = "";
    foreach ($arr_conceptos as $key => $value)
    {
        $sltd = ($wconcepto_cobrar_material_uvr == $key) ? 'selected="selected"': '';
        $options_conceptos_material_uvr .= '<option value="'.$key.'" '.$sltd.' >'.$key.'-'.$value.'</option>';
    }

    // Opciones para el concepto a cobrar en límite de grupo Quirúrgico. Medicamento
    $options_conceptos_grupoqx = "";
    foreach ($arr_conceptos as $key => $value)
    {
        $sltd = ($wconcepto_cobrar_grupo_qx == $key) ? 'selected="selected"': '';
        $options_conceptos_grupoqx .= '<option value="'.$key.'" '.$sltd.' >'.$key.'-'.$value.'</option>';
    }

    // Opciones para el concepto a cobrar en límite de grupo Quirúrgico. Material
    $options_conceptos_material_grupoqx = "";
    foreach ($arr_conceptos as $key => $value)
    {
        $sltd = ($wconcepto_cobrar_material_grupo_qx == $key) ? 'selected="selected"': '';
        $options_conceptos_material_grupoqx .= '<option value="'.$key.'" '.$sltd.' >'.$key.'-'.$value.'</option>';
    }

    $select_politrauma[1] = ($data['wpolitrauma'] == 'off') ? 'selected="selected"': '';
    $select_politrauma[2] = ($data['wpolitrauma'] == 'on') ? 'selected="selected"': '';
    $select_politrauma[3] = ($data['wpolitrauma'] == '*') ? 'selected="selected"': '';

    $view_activo   = 'none';
    $view_inactivo = '';
    $desc_boton = "Activar";
    if($estado == 'on')
    {
        $view_activo   = '';
        $view_inactivo = 'none';
        $desc_boton = "Inactivar";
    }

    $explica_requiere_tiempo = '<div style=\'font-size:8pt;\'>
                                    <img border=\'0\' width=\'10\' height=\'10\' style=\'display:;\' src=\'../../images/medical/root/info.png\' >
                                    [Requiere liquidar tiempo]<br><br>
                                    Este campo seleccionado quiere decir que al momento de liquidar cirug&iacute;as debe mostrar <br>y solicitar datos adicionales como lo son:<br>
                                    * Tiempo de cirug&iacute;a<br>
                                    * Uso de equipos<br>
                                    * Sala de recuperaci&oacute;n<br><br>
                                    Esto aplica normalmente para PAPs</div>';

    $explica_asumir_noFact   = '<div style=\'font-size:8pt;\'>
                                    <img border=\'0\' width=\'10\' height=\'10\' style=\'display:;\' src=\'../../images/medical/root/info.png\' >
                                    [Asumir NO FACTURABLE]<br><br>
                                    Este campo seleccionado quiere decir que al momento de liquidar cirug&iacute;as, el material y medicamento
                                    ser&aacute; asumido como NO FACTURABLE y las clasificaciones ser&aacute;n la excepci&oacute;n de lo que s&iacute; de ser facturable
                                    <br><br>
                                    Si este campo no est&aacute; seleccionado quiere decir que los materiales y medicamentos ser&aacute;n asumidos<br>
                                    como S&iacute; FACTURABLES y las clasificaciones ser&aacute;n la excepci&oacute;n de lo que no debe ser facturable.</div>';

    $explica_campo_procedimiento = '<div style=\'font-size:8pt;\'>
                                    <img border=\'0\' width=\'10\' height=\'10\' style=\'display:;\' src=\'../../images/medical/root/info.png\' >
                                    [Procedimiento]<br><br>
                                    Por lo general en este campo siempre debe estar seleccionada la opci&oacute;n *-TODOS, pero a diferencia de algunos <br>
                                    casos particulares por ejemplo en caso de una Conizaci&oacute;n, se deber&iacute;a elegir el c&oacute;digo CUPS correspondiente a <br>
                                    dicho procedimiento para que el manual est&eacute; asociado solamente a ese c&oacute;digo</div>';

    $explica_lim_uvr        = '<div style=\'font-size:8pt;\'>
                                    <img border=\'0\' width=\'10\' height=\'10\' style=\'display:;\' src=\'../../images/medical/root/info.png\' >
                                    [L&iacute;mite de UVRs]<br><br>
                                    Este campo permite tener un l&iacute;mite de referencia para UVRs, si el procedimiento a liquidar es igual o inferior a la cantidad de UVRs configurado<br>
                                    en este campo entonces los medicamentos e insumos se liquidar&aacute;n como no facturables. Todo medicamento e insumo que sea mayor o igual al l&iacute;mite ser&aacute;<br>
                                    liquidado facturable</div>';

    $explica_lim_grupo      = '<div style=\'font-size:8pt;\'>
                                    <img border=\'0\' width=\'10\' height=\'10\' style=\'display:;\' src=\'../../images/medical/root/info.png\' >
                                    [L&iacute;mite de grupos quir&uacute;rgicos]<br><br>
                                    Este campo permite tener un l&iacute;mite de referencia para grupos<br><br>
                                    Si el procedimiento a liquidar es igual o inferior a la cantidad de grupo quir&uacute;rgico configurado en este campo entonces los <br>
                                    medicamentos e insumos se liquidar&aacute;n como no facturables. Todo medicamento e insumo que sea mayor o igual al l&iacute;mite ser&aacute;<br>
                                    liquidado facturable</div>';

    $html_relaciones_procedimientos = "";
    foreach ($implode_procedimientos_relacionados as $key_id_relacion => $arr_lista_relacionados)
    {
        //$implode_procedimientos_relacionados[$row['id_relacion']] = array("wprocedimiento"=>$row['wprocedimiento'], "procedimientos_relacionados"=>$row['procedimientos_relacionados']);
        $html_relaciones_procedimientos .= "
                                            ".procedimientosListaFacturables($arr_lista_relacionados["wprocedimiento"], $arr_lista_relacionados["procedimientos_relacionados"], $arr_procedimientos, $id_manual, $key_id_relacion);
    }
    // echo "<pre>".print_r($html_relaciones_procedimientos,true)."</pre>";

    $ms_procedimiento_NoF       = "Procedimiento que ser&aacute; NO FACTURABLE si en la misma liquidaci&oacute;n esta uno o varios procedimientos de la lista contigua.";
    $ms_procedimiento_NoF_lista = "Lista de procedimientos que har&aacute;n no facturable el procedimiento seleccionado en la columna izquierda.";

    $ms_clases_NoFactLimtInf = "Clasificaciones que ser&aacute;n NO FACTURABLES si los puntos de UVR o GRUPO NO superan o son iguales al l&iacute;mite de no facturable";
    $ms_clases_NoFactLimtSup = "Clasificaciones que ser&aacute;n NO FACTURABLES si los puntos de UVR o GRUPO S&iacute; superan el l&iacute;mite de no facturable";

    $ms_clases_SifactLimtInf = "Clasificaciones que ser&aacute;n SI FACTURABLES si los puntos de UVR o GRUPO NO superan o son iguales al l&iacute;mite de puntos";
    $ms_clases_SifactLimtSup = "Clasificaciones que ser&aacute;n SI FACTURABLES si los puntos de UVR o GRUPO S&iacute; superan el l&iacute;mite de puntos";

    if(($cont_mnl*1)<10)
    {
        $cont_mnl = "0".$cont_mnl;
    }

    $msj_redondeo   = 'Redondear valores de liquidaci&oacute;n a la centena m&aacute;s cercana.';
    $html_tdtiempos = '[<span class="fila1"><input type="checkbox" id="wredondear_centena'.$id_manual.'" name="wredondear_centena'.$id_manual.'" value="on" '.$chk_wredondear_centena.' class="onBlurGrabar" id_registro="'.$id_manual.'" actualiza="redondear_centena" ></span>]
                        Redondeo centena m&aacute;s cercana';

    $html_tr_extras = ' <tr style="'.$ocultarParametros.'" class="otrosParametrosEncabezado_'.$id_manual.'">
                            <td class="fila2 msj_tooltip" colspan="2" title="'.$explica_requiere_tiempo.'">
                                [<span class="fila1"><input type="checkbox" id="wliquida_tiempos_'.$id_manual.'" name="wliquida_tiempos_'.$id_manual.'" value="on" '.$chk_tiempos.' class="onBlurGrabar" id_registro="'.$id_manual.'" actualiza="req_tiempo" ></span>]
                                Requiere liquidar tiempo
                            </td>
                            <td class="fila2 msj_tooltip" colspan="2" title="Validar si el insumo es o no facturable en el maestro de artiulos por empresa, aparte de la clasificaci&oacute;n.">
                                [<span class="fila1"><input type="checkbox" id="wvalidar_por_articulo'.$id_manual.'" name="wvalidar_por_articulo'.$id_manual.'" value="on" '.$chk_valPorArticulo.' class="onBlurGrabar" id_registro="'.$id_manual.'" actualiza="valida_art_empresa" ></span>]
                                Validar tambi&eacute;n por art&iacute;culo
                            </td>
                            <td class="fila2 msj_tooltip" colspan="2" title="'.$msj_redondeo.'">
                                '.$html_tdtiempos.'
                            </td>
                        </tr>';

    if($ocultarParametros != '')
    {
        $html_tr_extras = ' <tr style="" class="otrosParametrosEncabezado_'.$id_manual.'">
                                <td class="fila2 msj_tooltip" colspan="6" title="'.$msj_redondeo.'">
                                    '.$html_tdtiempos.'
                                </td>
                            </tr>';
    }

    $html .= '  <div class="acordeon_manual" style="text-align:left; width: 1300px;" id="seccion_manual_'.$id_manual.'">
                    <h3><span style="background-color:'.$color_manual.';">&nbsp;&nbsp;&nbsp;</span>
                            <img id="img_estado_manual_h3_'.$id_manual.'_activo" border="0" src="../../images/medical/root/activo.gif" width="10" height="10" style="display:'.$view_activo.';" >
                            <img id="img_estado_manual_h3_'.$id_manual.'_inactivo" border="0" src="../../images/medical/root/inactivo.gif" width="10" height="10" style="display:'.$view_inactivo.';" >
                            &nbsp;<span id="spn_nombre_manual_'.$id_manual.'" ><span style="font-size:7.5pt;">['.$cont_mnl.']</span> '.$wnombre_manual.'</span></h3>
                    <div align="center" style="display:;">

                            <div class="div_width_max div_botones_opcion">&nbsp;
                                <ul class="botones_opcion_ul">
                                    <li class="">
                                        <img id="img_estado_manual_'.$id_manual.'_activo" border="0" src="../../images/medical/root/activo.gif" width="10" height="10" style="display:'.$view_activo.';" >
                                        <img id="img_estado_manual_'.$id_manual.'_inactivo" border="0" src="../../images/medical/root/inactivo.gif" width="10" height="10" style="display:'.$view_inactivo.';" >
                                        <button id="btn_estado_manual_'.$id_manual.'" onclick="cambiarEstadoManual(this, \''.$id_manual.'\');" estado="'.$estado.'" >'.$desc_boton.'</button>
                                    </li>
                                </ul>
                            </div>

                            <div class="div_width_max div_param_generales">
                                <table align="center" style="text-align:left;">
                                    <tr>
                                        <td class="encabezadoTabla">Nombre manual</td>
                                        <td colspan="5" class="fila2" ><input type="text" value="'.$wnombre_manual.'" id="wnombre_manual_'.$id_manual.'" name="wnombre_manual_'.$id_manual.'" size="30" onkeypress="return soloCaracteresPermitidos(event);" onkeyup="escribirCodigoManual(this,\'wcodigo_manual_'.$id_manual.'\'); acordeonNombre(this,\''.$id_manual.'\');" class="onBlurGrabar" id_registro="'.$id_manual.'" actualiza="nombre_manual"></td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla">Centro costos</td>
                                        <td class="fila2" >
                                            <select '.$bloquear_campos.' id="wcentro_costo_'.$id_manual.'" name="wcentro_costo_'.$id_manual.'" class="requerido save_ok" onchange="validarParametrosCentroCosto(this,\''.$id_manual.'\')">
                                                '.$optionsCentroCosto.'
                                            </select>
                                        </td>
                                        <td class="encabezadoTabla">C&oacute;digo</td>
                                        <td class="fila2" ><input '.$bloquear_campos.' type="text" value="'.$wcodigo_manual.'" id="wcodigo_manual_'.$id_manual.'" name="wcodigo_manual_'.$id_manual.'" size="15" class="mayuscula" codigo_guardado="'.$wcodigo_manual.'" onkeypress="return soloCaracteresPermitidos(event);" onkeyup="limpiarCodigo($(this).val(), \'wcodigo_manual_'.$id_manual.'\')" ></td>
                                        <td class="encabezadoTabla">Tipo Empresa</td>
                                        <td class="fila2" ><select '.$bloquear_campos.' id="wtipoempresa_'.$id_manual.'" name="wtipoempresa_'.$id_manual.'" class="requerido save_ok">
                                            '.$optionsTiposEmpresas.'
                                        </select></td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla">Empresa</td>
                                        <td class="fila2"><input '.$bloquear_campos.' type="text" value="'.$data['wempresa_nombre'].'" id="wempresa_'.$id_manual.'" name="wempresa_'.$id_manual.'" class="wempresa" codigo="'.$data['wempresa'].'" nombre="'.$data['wempresa_nombre'].'" size="30"></td>
                                        <td class="encabezadoTabla">Politraumatizados</td>
                                        <td class="fila2">
                                            <select '.$bloquear_campos.' id="wpolitrauma_'.$id_manual.'" name="wpolitrauma_'.$id_manual.'" >
                                                <option value="off" '.$select_politrauma[1].' >No</option>
                                                <option value="on" '.$select_politrauma[2].' >Si</option>
                                                <option value="*" '.$select_politrauma[3].' >Ambos</option>
                                            </select>
                                        </td>
                                        <td class="encabezadoTabla msj_tooltip" title="'.$explica_campo_procedimiento.'">Procedimiento</td>
                                        <td class="fila2"><input '.$bloquear_campos.' type="text" value="'.$data['wprocedimiento_manualNombre'].'" id="wprocedimiento_manual_'.$id_manual.'" name="wprocedimiento_manual_'.$id_manual.'" class="wprocedimiento_manual" codigo="'.$data['wprocedimiento_manual'].'" nombre="'.$data['wprocedimiento_manualNombre'].'" size="30"></td>
                                    </tr>

                                    '.$html_tr_extras.'

                                    <tr style="'.$ocultarParametros.'" class="otrosParametrosEncabezado_'.$id_manual.'">
                                        <td class="fila2 msj_tooltip" colspan="2" title="'.$explica_asumir_noFact.'">
                                            [<span class="fila1"><input type="checkbox" id="winsumos_no_facturables_'.$id_manual.'" name="winsumos_no_facturables_'.$id_manual.'" value="on" '.$chk_insumos_noFact.' class="onBlurGrabar" id_registro="'.$id_manual.'" actualiza="insumos_no_facturables" onclick="cambiarMensajesDefault(this,\''.$id_manual.'\');" ></span>]
                                            Asumir NO FACTURABLE (Materiales y medicamentos)
                                        </td>
                                        <td class="fila2" colspan="4">
                                        </td>
                                    </tr>
                                </table>

                            </div>
                            <div align="center" '.$btn_nuevo_manual.'><button style="margin-top: 10px; display:;" onclick="guardarNuevoManualContinuar();" >Guardar y continuar</button></div>

                            <div class="div_width_max div_param_generales" style="display:'.$div_actos_quirurgicos.';">
                                <ul class="param_generales_ul" style="'.$ocultarParametros.'">
                                    <li>
                                        <table align="center" class="estilo_tabla_limites" >
                                            <tr>
                                                <td colspan="6" class="encabezadoTabla msj_tooltip" style="text-align:justify;" title="NOTA PARA PREPAGADAS">
                                                    NOTA: Para configurar clasificaciones facturables o no facturables en prepagadas, el l&iacute;mite de materiales y medicamentos fijarlo en cero (0)
                                                    en la secci&oacute;n de L&iacute;mite UVR, no seleccionar concepto a facturar, <br>en clasificaci&oacute;n MAYOR al limite de UVR ingresar las clasificaciones a excluir.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="encabezadoTabla msj_tooltip" style="padding: 2px; width: 140px; text-align:center;"  title="'.$explica_lim_uvr.'" rowspan="3"  >
                                                    Cantidad m&aacute;xima de UVR para medicamentos y materiales no facturables
                                                </td>
                                                <td class="encabezadoTabla" >&nbsp;</td>
                                                <td class="encabezadoTabla msj_tooltip" title="'.$explica_lim_uvr.'" >L&iacute;mite UVR</td>
                                                <td class="encabezadoTabla" style="padding: 2px" >Concepto a facturar</td>
                                                <td class="encabezadoTabla" style="padding: 2px" >
                                                    <span style="'.$visible_MsjNoFact.'" class="spn_NOFact'.$id_manual.'">Clasificaci&oacute;n NO Facturable <br>MENOR al l&iacute;mite de UVR <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_clases_NoFactLimtInf.'" ></span>
                                                    <span style="'.$visible_MsjSiFact.'" class="spn_SIFact'.$id_manual.'">Clasificaci&oacute;n SI Facturable <br>MENOR al l&iacute;mite de UVR <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_clases_SifactLimtInf.'" ></span>
                                                </td>
                                                <td class="encabezadoTabla" style="padding: 2px" >
                                                    <span style="'.$visible_MsjNoFact.'" class="spn_NOFact'.$id_manual.'">Clasificaci&oacute;n NO Facturable <br>MAYOR al l&iacute;mite de UVR <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_clases_NoFactLimtSup.'" ></span>
                                                    <span style="'.$visible_MsjSiFact.'" class="spn_SIFact'.$id_manual.'">Clasificaci&oacute;n SI Facturable <br>MAYOR al l&iacute;mite de UVR <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_clases_SifactLimtSup.'" ></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fila1" >Medicamentos</td>
                                                <td class="fila1" ><input type="text" id="wuvr_maximo_nof_'.$id_manual.'" name="wuvr_maximo_nof_'.$id_manual.'"  puede_vacio="on" value="'.$wlimite_uvr_nofac.'" class="numerico onBlurGrabar" placeholder="0" size="5" id_registro="'.$id_manual.'" actualiza="limite_uvr" /></td>
                                                <td class="fila1" >
                                                    <select id="wconcepto_uvr_nof'.$id_manual.'" name="wconcepto_uvr_nof'.$id_manual.'"  puede_vacio="on" onchange="actualizarRegistro(this)" id_registro="'.$id_manual.'" actualiza="limite_concepto_uvr" >
                                                        <option value="" >Seleccione..</option>
                                                        '.$options_conceptos_uvr.'
                                                    </select>
                                                </td>
                                                <td class="fila1" style="text-align:left;" >
                                                    '.clasesExcluidasFacturables($implode_clases_medicamentos_uvr, $arr_maestro_clases_insumos, 'Blqcma', "excluidos_medicamentos_uvr", $id_manual).'
                                                </td>
                                                <td class="fila1" style="text-align:left;" >
                                                    '.clasesExcluidasFacturables($implode_clases_NoFact_medicamentos_uvr, $arr_maestro_clases_insumos, 'Blqnfa', "claseNoFact_medicamentos_uvr", $id_manual).'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fila2" >Materiales</td>
                                                <td class="fila2" ><input type="text" id="wuvr_maximo_nof_material_'.$id_manual.'" name="wuvr_maximo_nof_material_'.$id_manual.'"  puede_vacio="on" value="'.$wlimite_material_uvr_nofac.'" class="numerico onBlurGrabar" placeholder="0" size="5" id_registro="'.$id_manual.'" actualiza="limite_material_uvr" /></td>
                                                <td class="fila2" >
                                                    <select id="wconcepto_uvr_nof_material_'.$id_manual.'" name="wconcepto_uvr_nof_material_'.$id_manual.'"  puede_vacio="on" onchange="actualizarRegistro(this)" id_registro="'.$id_manual.'" actualiza="limite_concepto_materila_uvr" >
                                                        <option value="" >Seleccione..</option>
                                                        '.$options_conceptos_material_uvr.'
                                                    </select>
                                                </td>
                                                <td class="fila2" style="text-align:left;" >
                                                    '.clasesExcluidasFacturables($implode_clases_materiales_uvr, $arr_maestro_clases_insumos, 'Blqcmb', "excluidos_material_uvr", $id_manual).'
                                                </td>
                                                <td class="fila2" style="text-align:left;" >
                                                    '.clasesExcluidasFacturables($implode_clases_NoFact_materiales_uvr, $arr_maestro_clases_insumos, 'Blqnfb', "claseNoFact_material_uvr", $id_manual).'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="encabezadoTabla" >Ayudant&iacute;a</td>
                                                <td class="fila1" >Se cobra si<span title="MAYOR O IGUAL"> >= </span></td>
                                                <td class="fila1" ><input type="text" id="wuvr_maximo_nof_ayudante_'.$id_manual.'" name="wuvr_maximo_nof_ayudante_'.$id_manual.'"  puede_vacio="on" value="'.$wuvr_maximo_nof_ayudante.'" class="numerico onBlurGrabar" placeholder="0" size="5" id_registro="'.$id_manual.'" actualiza="limite_ayudante_uvr" /></td>
                                                <td class="fila1" >&nbsp;</td>
                                                <td class="fila1" >&nbsp;</td>
                                                <td class="fila1" >&nbsp;</td>
                                            </tr>

                                            <tr>
                                                <td class="encabezadoTabla msj_tooltip" style="padding: 2px; width: 140px; text-align:center;"  title="'.$explica_lim_grupo.'" rowspan="3"  >
                                                    Cantidad m&aacute;xima de GRUPO para medicamentos e insumos no facturables
                                                </td>
                                                <td class="encabezadoTabla" >&nbsp;</td>
                                                <td class="encabezadoTabla msj_tooltip" title="'.$explica_lim_grupo.'" >L&iacute;mite GRUPO</td>
                                                <td class="encabezadoTabla" style="padding: 2px" >Concepto a facturar</td>
                                                <td class="encabezadoTabla" style="padding: 2px" >
                                                    <span style="'.$visible_MsjNoFact.'" class="spn_NOFact'.$id_manual.'">Clasificaci&oacute;n NO Facturable <br>MENOR al l&iacute;mite de GRUPO <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_clases_NoFactLimtInf.'" ></span>
                                                    <span style="'.$visible_MsjSiFact.'" class="spn_SIFact'.$id_manual.'">Clasificaci&oacute;n SI Facturable <br>MENOR al l&iacute;mite de GRUPO <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_clases_SifactLimtInf.'" ></span>
                                                </td>
                                                <td class="encabezadoTabla" style="padding: 2px" >
                                                    <span style="'.$visible_MsjNoFact.'" class="spn_NOFact'.$id_manual.'">Clasificaci&oacute;n NO Facturable <br>MAYOR al l&iacute;mite de GRUPO <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_clases_NoFactLimtSup.'" ></span>
                                                    <span style="'.$visible_MsjSiFact.'" class="spn_SIFact'.$id_manual.'">Clasificaci&oacute;n SI Facturable <br>MAYOR al l&iacute;mite de GRUPO <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_clases_SifactLimtSup.'" ></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fila1" >Medicamentos</td>
                                                <td class="fila1" >
                                                    <input type="text" id="wgrupo_maximo_nof_'.$id_manual.'" name="wgrupo_maximo_nof_'.$id_manual.'" puede_vacio="on" value="'.$wlimite_grupoqx.'" class="numerico onBlurGrabar" placeholder="0" size="5" id_registro="'.$id_manual.'" actualiza="limite_grupoqx" />
                                                </td>
                                                <td class="fila1" >
                                                    <select id="wconcepto_grupoqx_nof_'.$id_manual.'" name="wconcepto_grupoqx_nof_'.$id_manual.'"  puede_vacio="on" onchange="actualizarRegistro(this)" id_registro="'.$id_manual.'" actualiza="limite_concepto_grupoqx" >
                                                        <option value="" >Seleccione..</option>
                                                        '.$options_conceptos_grupoqx.'
                                                    </select>
                                                </td>
                                                <td class="fila1" style="text-align:left;" >
                                                    '.clasesExcluidasFacturables($implode_clases_medicamentos_grupo, $arr_maestro_clases_insumos, 'Blqcmc', "excluidos_medicamentos_grupo", $id_manual).'
                                                </td>
                                                <td class="fila1" style="text-align:left;" >
                                                    '.clasesExcluidasFacturables($implode_clases_NoFact_medicamentos_grupo, $arr_maestro_clases_insumos, 'Blqnfc', "claseNoFact_medicamentos_grupo", $id_manual).'
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fila2" >Materiales</td>
                                                <td class="fila2" >
                                                    <input type="text" id="wgrupo_maximo_nof_material_'.$id_manual.'" name="wgrupo_maximo_nof_material_'.$id_manual.'" puede_vacio="on" value="'.$wlimite_material_grupoqx.'" class="numerico onBlurGrabar" placeholder="0" size="5" id_registro="'.$id_manual.'" actualiza="limite_material_grupo" />
                                                </td>
                                                <td class="fila2" >
                                                    <select id="wconcepto_grupoqx_nof_material_'.$id_manual.'" name="wconcepto_grupoqx_nof_material_'.$id_manual.'"  puede_vacio="on" onchange="actualizarRegistro(this)" id_registro="'.$id_manual.'" actualiza="limite_concepto_material_grupo" >
                                                        <option value="" >Seleccione..</option>
                                                        '.$options_conceptos_material_grupoqx.'
                                                    </select>
                                                </td>
                                                <td class="fila2" style="text-align:left;" >
                                                    '.clasesExcluidasFacturables($implode_clases_materiales_grupo, $arr_maestro_clases_insumos, 'Blqcmd', "excluidos_material_grupo", $id_manual).'
                                                </td>
                                                <td class="fila2" style="text-align:left;" >
                                                    '.clasesExcluidasFacturables($implode_clases_NoFact_materiales_grupo, $arr_maestro_clases_insumos, 'Blqnfd', "claseNoFact_material_grupo", $id_manual).'
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="encabezadoTabla" >Ayudant&iacute;a</td>
                                                <td class="fila1" >Se cobra si<span title="MAYOR O IGUAL"> >= </span></td>
                                                <td class="fila1" ><input type="text" id="wgrupo_maximo_nof_ayudante_'.$id_manual.'" name="wgrupo_maximo_nof_ayudante_'.$id_manual.'"  puede_vacio="on" value="'.$wgrupo_maximo_nof_ayudante.'" class="numerico onBlurGrabar" placeholder="0" size="5" id_registro="'.$id_manual.'" actualiza="limite_ayudante_grupo" /></td>
                                                <td class="fila1" >&nbsp;</td>
                                                <td class="fila1" >&nbsp;</td>
                                                <td class="fila1" >&nbsp;</td>
                                            </tr>
                                            <tr class="fila2">
                                                <td colspan="6" align="center">
                                                    <div class="encabezadoTabla" >Procedimientos relacionados no facturables <button style="cursor:pointer;" onclick="verOcultarLista(\'div_relacion_procedimientos_'.$id_manual.'\')">Ver</button></div>
                                                    <div style="display:none;" id="div_relacion_procedimientos_'.$id_manual.'" >
                                                        <table id="table_relacion_procedimientos_'.$id_manual.'" >
                                                            <tr class="encabezadoTabla">
                                                                <td align="center" colspan="3" >
                                                                    <button style="cursor:pointer;" onclick="agregarRelacionProcedimientos(this,\''.$id_manual.'\',\''.$wcodigo_manual.'\');" >Agregar nueva relaci&oacute;n</button>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td class="encabezadoTabla">Procedimiento no facturable <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_procedimiento_NoF.'" ></td>
                                                                <td class="encabezadoTabla">Lista de procedimientos facturables <img width="15" height="15" src="../../images/medical/root/info.png" class="msj_tooltip" title="'.$ms_procedimiento_NoF_lista.'" ></td>
                                                            </tr>
                                                            '.$html_relaciones_procedimientos.'
                                                            <tr class="encabezadoTabla pietabla_relaciones_'.$id_manual.'" >
                                                                <td valign="top" colspan="3" >
                                                                    &nbsp;
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </li>
                                </ul>
                            </div>

                            <div style="cursor:pointer;background-color:#83D8F7;border: 1px solid #999999;font-family: verdana;text-align:left;" onclick="verOcultarLista(\'div_actos_quirurgicos_procedimientos_'.$id_manual.'\');">
                                &nbsp;<img id="" border="0" src="../../images/medical/root/mas.png" width="14" height="14" >
                                Ver Porcentajes de cirug&iacute;as m&uacute;ltiples para PROCEDIMIENTOS
                            </div>
                            <div id="div_actos_quirurgicos_procedimientos_'.$id_manual.'" style="display:none;border: 1px solid #dddddd;">
                                <div style="margin: 3px; margin-top: 10px; text-align:center; display:'.$crearNuevoActoQx.';">
                                    <button onclick="crearNuevoActoQx(\''.$id_manual.'\', \''.$wcodigo_manual.'\', \''.$tipo_liquidacion.'\');" >Nuevo acto quir&uacute;rgico procedimiento</button>
                                </div>
                                <div id="div_actos_quirurgicos_'.$tipo_liquidacion.$id_manual.'" style="display:'.$div_actos_quirurgicos.';" class="div_width_max div_actos_quirurgicos">
                                    <!-- TABLA DE ENCABEZADO - COLUMNAS DE LOS CONCEPTOS -->
                                    <table align="center" style="width:100%;">
                                        <tr>
                                            <td style="width: 72px;">&nbsp;</td>
                                            <td style="width: 13px;">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td style="width: 225px;">&nbsp;</td>
                                            <td style="" align="left">
                                                <div class="div_cirugias_acto_qx_encabezado_'.$tipo_liquidacion.$id_manual.'">
                                                    <ul class="columnas_conceptos columnas_conceptos_enc encabezadoTabla">
                                                        '.$columnas_conceptos.'
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- TABLA DE ENCABEZADO - COLUMNAS DE LOS CONCEPTOS -->
                                    '.$html_lista_actos_qx.'
                                </div>
                            </div>

                            <div style="cursor:pointer;background-color:#83D8F7;border: 1px solid #999999;font-family: verdana;text-align:left;'.$ocultarParametros.'" onclick="verOcultarLista(\'div_actos_quirurgicos_paquetes_'.$id_manual.'\');">
                                &nbsp;<img id="" border="0" src="../../images/medical/root/mas.png" width="14" height="14" >
                                Ver Porcentajes de cirug&iacute;as m&uacute;ltiples para PAQUETES
                            </div>
                            <div id="div_actos_quirurgicos_paquetes_'.$id_manual.'" style="display:none;border: 1px solid #dddddd;">
                                <div style="margin: 3px; margin-top: 10px; text-align:center; display:'.$crearNuevoActoQx.';">
                                    <button onclick="crearNuevoActoQx(\''.$id_manual.'\', \''.$wcodigo_manual.'\', \'pqte\');" >Nuevo acto quir&uacute;rgico paquete</button>
                                </div>
                                <div id="div_actos_quirurgicos_pqte'.$id_manual.'" style="display:'.$div_actos_quirurgicos.';" class="div_width_max div_actos_quirurgicos">
                                    <!-- TABLA DE ENCABEZADO - COLUMNAS DE LOS CONCEPTOS -->
                                    <table align="center" style="width:100%;">
                                        <tr>
                                            <td style="width: 72px;">&nbsp;</td>
                                            <td style="width: 13px;">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td style="width: 225px;">&nbsp;</td>
                                            <td style="" align="left">
                                                <div class="div_cirugias_acto_qx_encabezado_pqte'.$id_manual.'">
                                                    <ul class="columnas_conceptos columnas_conceptos_enc encabezadoTabla">
                                                        <li>PAQUETE</li>
                                                        <li style="width: 170px;">(%) paquetes</li>
                                                        <li style="width: 170px;">&nbsp;</li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- TABLA DE ENCABEZADO - COLUMNAS DE LOS CONCEPTOS -->
                                    '.$html_lista_actos_qx_paquetes.'
                                </div>
                            </div>

                    </div>
                </div>';
    return $html;
}


if(isset($accion) && isset($form))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'','log_error'=>'');
    $no_exec_sub = 'No se ejecut&oacute; ning&uacute;na rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                case 'guardar_nuevo_manual_continuar':
                    $data['id_manual'] = '';

                    $wnombre_manual = utf8_decode($wnombre_manual);

                    $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
                    $sql = "INSERT INTO {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                (Medico, Fecha_data, Hora_data, Blqclq, Blqdes, Blqcco,
                                Blqtem, Blqemp, Blqpol, Blqpro, Blqtie, Blqape, Blqrce, Blqanf,
                                Blquvr, Blqccu, Blqlgr, Blqccg,
                                Blqmuv, Blqcmu, Blqmgr, Blqcmg,
                                Blqauv, Blqagr,
                                Blqcma, Blqcmb, Blqcmc, Blqcmd,
                                Blqest, Seguridad)
                            VALUES
                                ('{$wbasedato}', '".date('Y-m-d')."', '".date('H:i:s')."', '{$wcodigo_manual}', '{$wnombre_manual}', '{$wcentro_costo}',
                                '{$wtipoempresa}', '{$wempresa}', '{$wpolitrauma}', '{$wprocedimiento_manual}', '{$wliquida_tiempos}', '{$wvalidar_por_articulo}', '{$wredondear_centena}', '{$winsumos_no_facturables}',
                                '{$wuvr_maximo_nof}', '{$wconcepto_uvr_nof}', '{$wgrupo_maximo_nof}', '{$wconcepto_grupoqx_nof}',
                                '{$wuvr_maximo_nof_material}', '{$wconcepto_uvr_nof_material}', '{$wgrupo_maximo_nof_material}', '{$wconcepto_grupoqx_nof_material}',
                                '{$wuvr_maximo_nof_ayudante}', '{$wgrupo_maximo_nof_ayudante}',
                                '{$clases_excluidos_medicamentos_uvr}', '{$clases_excluidos_material_uvr}', '{$clases_excluidos_medicamentos_grupo}', '{$clases_excluidos_material_grupo}',
                                'on', 'C-{$user_session}')";
                    //$result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
                    if($result = mysql_query($sql,$conex))
                    {
                        $data['id_manual'] = mysql_insert_id();
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "No se pudo guardar los datos ingresados.";
                        $data['log_error'] = mysql_errno().' - '.mysql_error().' - SQL: '.$sql;
                    }
                    break;

                case 'agregar_relacion_procedimiento':
                        $arr_procedimientos = unserialize(base64_decode($arr_procedimientos));
                        $data['id_relacion'] = '';
                        $data["html"] = '';
                        $sql = "INSERT INTO {$wbasedato}_000225
                                            (Medico,          Fecha_data,      Hora_data,      Rnfman,             Rnfpro, Rnfrel, Rnfest, Seguridad )
                                VALUES      ('{$wbasedato}', '{$fecha_data}', '{$hora_data}', '{$wcodigo_manual}', '', '', 'off', 'C-{$user_session}')";

                        if($result = mysql_query($sql,$conex))
                        {
                            $id_relacion = mysql_insert_id();
                            $data['id_relacion'] = $id_relacion;
                            // $data["html"] = '<tr class="tr_relacion_'.$id_manual.'_'.$id_relacion.'">
                            //                     <td class="td_img2" style="width: 70px;">
                            //                         <button>Eliminar</button>
                            //                     </td>
                            //                     <td class="fila1" valign="top" >
                            //                         <input type="text" id="txt_procedimiento_NFac_'.$id_manual.'_'.$id_relacion.'" class="procedimiento_relacion_'.$id_manual.'" codigo="" nombre="" >
                            //                     </td>
                            //                     <td class="fila1" >
                            //                         Agregar: <input type="text" class="procedimiento_relacion_lista" id="txt_procedimiento_relacion_lista_'.$id_manual.'_'.$id_relacion.'" id_manual="'.$id_manual.'" id_relacion="'.$id_relacion.'" codigo="" nombre="" >
                            //                         <div id="div_lista_procedimientos_anulan_'.$id_manual.'_'.$id_relacion.'" style="height: 80px; overflow:auto;" >

                            //                         </div>
                            //                     </td>
                            //                 </tr>';
                            $data["html"] = procedimientosListaFacturables("", "", $arr_procedimientos, $id_manual, $id_relacion);
                        }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] = "No se pudo guardar registro para inicial configuraci&oacute;n.";
                            $data['log_error'] = mysql_errno().' - '.mysql_error().' - SQL: '.$sql;
                        }
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
                case 'actualizar_registro_bd':
                    $data["id_registro_nuevo"] = "";
                    $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
                    $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;
                    $TB_BASE_LIQ_ACTOSQX = TB_BASE_LIQ_ACTOSQX;
                    if($actualiza == 'cirugia')
                    {
                        if(empty($porcentaje)) { $porcentaje = 0; }

                        $sql = "SELECT t186.Medico, t186.Blqclq, t187.Cxxtaq, t187.Cxxcod, '{$wconcepto}', '{$posicion}'
                                FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION} AS t186
                                        INNER JOIN
                                        {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX} AS t187 ON (t186.Blqclq = t187.Cxxblq)
                                WHERE   t186.id = '{$id_manual}'
                                        AND t187.id = '{$id_acto_qx}'";
                        $result       = mysql_query($sql,$conex) or die (mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                        $row = mysql_fetch_array($result);

                        $Medico = $row["Medico"];
                        $Blqclq = $row["Blqclq"];
                        $Cxxtaq = $row["Cxxtaq"];
                        $Cxxcod = $row["Cxxcod"];

                        if(!empty($id_registro))
                        {
                            $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                                            SET    Liqpor = '{$valor_actualiza}',
                                                   Liqfed = '{$fecha_hora}',
                                                   Seguridad = 'C-{$user_session}'
                                    WHERE   id = '{$id_registro}'";
                            $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);

                            $query = "INSERT INTO {$wbasedato}_000353 (Medico, Fecha_data, Hora_data, Liqclq, Liqcxx, Liqcon, Liqtaq, Liqncx, Liqpor, User)
                                        VALUES('{$Medico}', '{$fecha_data}', '{$hora_data}', '{$Blqclq}', '{$Cxxcod}', '{$wconcepto}', '{$Cxxtaq}', '{$posicion}', '{$valor_actualiza}', 'C-{$user_session}')";
                            $res = mysql_query($query,$conex);
                        }
                        else
                        {
                            $sql = "INSERT INTO {$wbasedato}_{$TB_BASE_LIQPORCENTA} (Medico, Fecha_data, Hora_data, Liqclq, Liqtaq, Liqcxx, Liqcon, Liqncx, Liqpor, Liqest, Seguridad)
                                    VALUES ('{$Medico}', '{$fecha_data}', '{$hora_data}', '{$Blqclq}', '{$Cxxtaq}', '{$Cxxcod}', '{$wconcepto}', '{$posicion}', '{$valor_actualiza}', 'on', 'C-{$user_session}')";
                            $result = mysql_query($sql,$conex) or die (mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                            $data["id_registro_nuevo"] = mysql_insert_id();

                            $query = "INSERT INTO {$wbasedato}_000353 (Medico, Fecha_data, Hora_data, Liqclq, Liqcxx, Liqcon, Liqtaq, Liqncx, Liqpor, User)
                                        VALUES('{$Medico}', '{$fecha_data}', '{$hora_data}', '{$Blqclq}', '{$Cxxcod}', '{$wconcepto}', '{$Cxxtaq}', '{$posicion}', '{$valor_actualiza}', 'C-{$user_session}')";
                            $res = mysql_query($query,$conex);
                        }
                    }
                    elseif($actualiza == 'desc_acto')
                    {
                        $valor_actualiza = utf8_decode($valor_actualiza);
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX}
                                        SET    Cxxdes = '{$valor_actualiza}',
                                               Cxxfed = '{$fecha_hora}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'numero_via')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX}
                                        SET    Cxxnvi = '{$valor_actualiza}',
                                               Cxxfed = '{$fecha_hora}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        // $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                        if($result = mysql_query($sql,$conex))
                        {
                        }
                        else
                        {
                            $data["log_error"] = mysql_errno().'-'.mysql_error().'- SQL: '.$sql;
                            $data['error'] = 1;
                            $data['mensaje'] = "No se pudo actualizar, es posible que ya exista un acto quir&uacute;rgico con los mismo datos.";
                        }
                    }
                    elseif($actualiza == 'numero_especialista')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX}
                                        SET    Cxxnsp = '{$valor_actualiza}',
                                               Cxxfed = '{$fecha_hora}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        // $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                        if($result = mysql_query($sql,$conex))
                        {
                        }
                        else
                        {
                            $data["log_error"] = mysql_errno().'-'.mysql_error().'- SQL: '.$sql;
                            $data['error'] = 1;
                            $data['mensaje'] = "No se pudo actualizar, es posible que ya exista un acto quir&uacute;rgico con los mismo datos.";
                        }
                    }
                    elseif($actualiza == 'acto_bilateral')
                    {
                        $sql_add = "";
                        if($valor_actualiza == 'on')
                        {
                            $sql_add = ", Cxxnvi = '', Cxxnsp = ''";
                        }

                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX}
                                        SET    Cxxbil = '{$valor_actualiza}',
                                               Cxxfed = '{$fecha_hora}',
                                               Seguridad = 'C-{$user_session}'
                                               {$sql_add}
                                WHERE   id = '{$id_registro}'";
                        // $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                        if($result = mysql_query($sql,$conex))
                        {
                            $sql = "SELECT Cxxblq, Cxxcod
                                    FROM    {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX} AS t187
                                    WHERE   t187.id = '{$id_registro}'";
                            $result       = mysql_query($sql,$conex) or die (mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                            $row = mysql_fetch_array($result);

                            $Cxxblq = $row["Cxxblq"];
                            $Cxxcod = $row["Cxxcod"];

                            // Para todas las cirugías del acto quirúrgico en este caso bilateral, el campo de vía lo cambia a vacío para que no se tengan problemas
                            // al momento de buscar el porcentaje de las cirugías teniendo en cuenta el tipo de vía.
                            $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                                            SET    Liqvia = '', Liqesp = ''
                                    WHERE   Liqclq = '{$Cxxblq}'
                                            AND Liqtaq = '{$tipo_acto}'
                                            AND Liqcxx = '{$Cxxcod}'
                                            AND Liqbrr = 'off'";
                            // $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                            if($result = mysql_query($sql,$conex))
                            {
                            }
                            else
                            {
                                $data["log_error"] = mysql_errno().'-'.mysql_error().'- SQL: '.$sql;
                                $data['error'] = 1;
                                $data['mensaje'] = "No se pudo actualizar el campo de tipo de v&iacute;a para las cirug&iacute;as de este acto quir&uacute;rgico.";
                            }
                        }
                        else
                        {
                            $data["log_error"] = mysql_errno().'-'.mysql_error().'- SQL: '.$sql;
                            $data['error'] = 1;
                            $data['mensaje'] = "No se pudo actualizar, es posible que ya exista un acto quir&uacute;rgico con los mismo datos.";
                        }

                        //Aquí posiblemente adicionar tambien el html de select de vías que se le vayan al agregar al acto bilateral
                        //si se va a editar un acto bilateral agregando una nuevo cirugía entonces ese ya debe tener la nueva columna con el select
                        // <span>Vía: </span>
                        // <select name="" id="" >
                        //     <option value="">Igual</option>
                        //     <option value="">Diferente</option>
                        // </select>
                    }
                    elseif($actualiza == 'nombre_manual')
                    {
                        $valor_actualiza = utf8_decode($valor_actualiza);
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqdes = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'req_tiempo')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqtie = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'valida_art_empresa')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqape = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'redondear_centena')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqrce = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'insumos_no_facturables')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqanf = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_uvr')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blquvr = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_concepto_uvr')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqccu = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_material_uvr')
                    {
                        // Límite UVR para materiales
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqmuv = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_concepto_materila_uvr')
                    {
                        // Concepto para límite materiales por uvr
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqcmu = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_material_grupo')
                    {
                        // Límite GRUPO para materiales
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqmgr = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_concepto_material_grupo')
                    {
                        // Concepto para límite materiales por GRUPO
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqcmg = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_grupoqx')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqlgr = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_concepto_grupoqx')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqccg = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_ayudante_uvr')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqauv = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'limite_ayudante_grupo')
                    {
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqagr = '{$valor_actualiza}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'actualizar_exluidos')
                    {
                        // $campo =>  Blqcma | Blqcmb | Blqcmc | Blqcmd
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    {$campo} = '{$clases_excluidas}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_manual}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'actualizar_relacion_procedimientos')
                    {
                        // $campo =>  Blqcma | Blqcmb | Blqcmc | Blqcmd
                        $sql = "UPDATE {$wbasedato}_000225
                                        SET    Rnfrel = '{$lista_procedimientos}',
                                               Rnfest = 'on',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_relacion}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'procedimiento_no_fact')
                    {
                        // $campo =>  Blqcma | Blqcmb | Blqcmc | Blqcmd
                        $sql = "UPDATE {$wbasedato}_000225
                                        SET    Rnfpro = '{$valor_actualiza}',
                                               Rnfest = 'on',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    elseif($actualiza == 'cirugia_mayor')
                    {
                        // $campo =>  Blqcma | Blqcmb | Blqcmc | Blqcmd
                        // Rnfcmy Solo para la cirugía mayor (Si esta en la segunda cirugía se cobra solo la Segunda mayor de todo el acto Qx)
                        $sql = "UPDATE {$wbasedato}_000188
                                        SET    Liqcmy = '{$valor_actualiza}',
                                               Liqest = 'on',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_registro}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    }
                    break;

                case 'cambiar_estado_acto_qx':
                        $TB_BASE_LIQ_ACTOSQX = TB_BASE_LIQ_ACTOSQX;
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX}
                                        SET    Cxxest = '{$estado}',
                                               Cxxfed = '{$fecha_hora}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_acto_qx}'";
                        // $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                        if($result = mysql_query($sql,$conex))
                        {
                        }
                        else
                        {
                            $data["log_error"] = mysql_errno().'-'.mysql_error().'- SQL: '.$sql;
                            $data['error'] = 1;
                            $data['mensaje'] = "No se pudo actualizar, es posible que ya exista un acto quir&uacute;rgico con los mismo datos y el mismo estado.";
                        }
                    break;

                case 'cambiar_estado_manual':
                        $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                        SET    Blqest = '{$estado}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_manual}'";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    break;

                case 'cambiar_estado_cirugia':
                        // Una cirugía la componen varios registros relacionados por cada concepto
                        // por eso si se inactiva una cirugía, se inactivarán todos los registros como tantos conceptos estén configurados.
                        $ids_cirugias_exp = explode("_", $ids_cx_conceptos);
                        $ids_cirugias = implode("','", $ids_cirugias_exp);
                        $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                                        SET    Liqest = '{$estado}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id IN ('{$ids_cirugias}')";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    break;

                case 'cambiar_via_esp_cirugia_bilateral':
                        // Una cirugía la componen varios registros relacionados por cada concepto
                        // por eso si se cambia la vía a una cirugía, se cambiarán todos los registros como tantos conceptos estén configurados.
                        $ids_cirugias_exp = explode("_", $ids_cx_conceptos);
                        $ids_cirugias = implode("','", $ids_cirugias_exp);
                        $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;
                        $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                                        SET    {$campo} = '{$valor_nuevo}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id IN ('{$ids_cirugias}')";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                    break;

                case 'modificar_posiciones_cirugias':
                        // actualiza todos los ids a la nueva posición que corresponde a cada grupo de cirugía (recordar que si se cambia un posición de una cirugía,
                        // se deben cambiar las posiciones de todos los registros por concepto que estan asociadas a esa cirugía).
                        foreach ($lista_ids_cirugias as $ids_cirugias => $posicion)
                        {
                            $ids_cirugias_exp = explode("_", $ids_cirugias);
                            $ids_cirugias = implode("','", $ids_cirugias_exp);
                            $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;
                            $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                                            SET    Liqncx = '{$posicion}',
                                                   Liqfed = '{$fecha_hora}',
                                                   Seguridad = 'C-{$user_session}'
                                    WHERE   id IN ('{$ids_cirugias}')";
                            $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                        }

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
                case 'cargar_encabezado_html_manual':
                        $html                       = '';
                        $arr_conceptos_manuales     = unserialize(base64_decode($arr_conceptos_manuales));
                        $arr_maestro_clases_insumos = unserialize(base64_decode($arr_maestro_clases_insumos));
                        $arr_procedimientos         = unserialize(base64_decode($arr_procedimientos));

                        $html = crearHtmlManualEncabezado($conex, $wbasedato, $wemp_pmla, $user_session, 0, $arr_conceptos_manuales, $wcodigo_manual, $id_manual, $arr_entidades, $arr_maestro_clases_insumos, $arr_procedimientos, $data, '');
                        $data['html'] = $html;
                    break;

                case 'cargar_html_nuevo_acto_qx':
                        $html = '';
                        $data['id_acto_qx'] = '';
                        $id_acto_qx = '';
                        $arr_conceptos_manuales = unserialize(base64_decode($arr_conceptos_manuales));
                        if(array_key_exists($wcentro_costo, $arr_conceptos_manuales))
                        {
                            $arr_conceptos_manuales = $arr_conceptos_manuales[$wcentro_costo];
                        }
                        else
                        {
                            $arr_conceptos_manuales=array();
                        }

                        // Consultar el último codigo de acto quirúrgico creado.
                        $sql = "SELECT  c187.Cxxblq, c187.Cxxcod
                                FROM    {$wbasedato}_000186 AS c186
                                        INNER JOIN
                                        {$wbasedato}_000187 AS c187 ON (c186.Blqclq = c187.Cxxblq AND c187.Cxxbrr = 'off' AND c187.Cxxtaq = '{$tipo_acto}')
                                WHERE   c186.id = '{$id_manual}'
                                ORDER BY c187.Cxxcod DESC
                                LIMIT 1";
                        $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                        $codigo_acto_qx = '01';
                        if(mysql_num_rows($result) > 0)
                        {
                            $row = mysql_fetch_array($result);
                            // para los codigos menores a 10 les concatena el cero al inicio, para los mayores a 9 no se les concatena 0.
                            if(($row['Cxxcod']*1) >= 9) { $codigo_acto_qx = ($row['Cxxcod']*1) + 1; }
                            else { $codigo_acto_qx = '0'.(($row['Cxxcod']*1) + 1); }
                        }

                        // Insertar un nuevo acto quirúrgico con el código consultado anteriormente aumentado en 1
                        $sql = "INSERT INTO {$wbasedato}_000187
                                        (Medico, Fecha_data, Hora_data, Cxxblq, Cxxtaq, Cxxcod, Cxxest, Seguridad)
                                VALUES
                                        ('{$wbasedato}', '{$fecha_data}', '{$hora_data}', '{$wcodigo_manual}', '{$tipo_acto}', '{$codigo_acto_qx}', 'off', 'C-{$user_session}')";
                        // $result = mysql_query($sql,$conex) or die(mysql_errno().'-'.mysql_error().'- SQL: '.$sql);
                        if($result = mysql_query($sql,$conex))
                        {
                            // Recuperar el ID del nuevo acto quirúrgico creado.
                            $id_acto_qx = mysql_insert_id();
                            $data['id_acto_qx'] = $id_acto_qx;


                            $html_acto_qx = crearHtmlActoQx($conex, $wbasedato, $wemp_pmla, $user_session, $arr_conceptos_manuales, $id_manual, $wcodigo_manual, $id_acto_qx, $tipo_acto, $codigo_acto_qx, '', '', 'off', 'off', '');

                            $data['html'] = $html_acto_qx;
                        }
                        else
                        {
                            $data["log_error"] = mysql_errno().'-'.mysql_error().'- SQL: '.$sql;
                            $data['error'] = 1;
                            $data['mensaje'] = "No se pudo crear, es posible que ya exista un acto quir&uacute;rgico con los mismo datos.";
                        }

                    break;

                case 'cargar_html_nueva_cirugia':
                        $html = '';
                        $arr_conceptos_manuales = unserialize(base64_decode($arr_conceptos_manuales));

                        if(array_key_exists($wcentro_costo, $arr_conceptos_manuales))
                        {
                            $arr_conceptos_manuales = $arr_conceptos_manuales[$wcentro_costo];
                        }
                        else
                        {
                            $arr_conceptos_manuales=array();
                        }

                        $html = crearHtmlFilaCirugia($conex, $wbasedato, $wemp_pmla, $arr_conceptos_manuales , $no_existe_en_bd , $id_manual , $id_acto_qx , $wcodigo_manual , $codigo_acto_qx, $tipo_acto , $fecha_data , $hora_data , $user_session, $data, $ver_vias_bilateral);

                        $data['html'] = $html;
                    break;

                case 'validar_posible_manual_igual':
                    $wcodigo_manual = strtoupper($wcodigo_manual);

                    $data['posibles_iguales'] = "";
                    $data['hay_parecidos'] = 0;

                    $TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
                    // Verifica que ya no exista el mismo código del manual
                    $sql = "SELECT  Blqclq
                            FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION}
                            WHERE   Blqclq = '{$wcodigo_manual}'";
                    $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
                    if(mysql_num_rows($result) > 0)
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "El c&oacute;digo de manual ya existe";
                    }
                    else
                    {
                        // Verifica si existe un manual con los mismos parámetros principales del manual
                        $sql = "SELECT  Blqclq, Blqdes
                                FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION}
                                WHERE   Blqcco = '{$wcentro_costo}'
                                        AND Blqtem = '{$wtipoempresa}'
                                        AND Blqemp = '{$wempresa}'
                                        AND Blqpol = '{$wpolitrauma}'
                                        AND Blqpro = '{$wprocedimiento_manual}'
                                        AND Blqest = 'on'";
                        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
                        if(mysql_num_rows($result) > 0)
                        {
                            $data['hay_parecidos'] = 1;
                            $data['posibles_iguales'] = "Manuales con iguales par&aacute;metros Cco: [$wcentro_costo], Tipo empresa:[$wtipoempresa], Empresa:[$wempresa], Politraumatizados:[$wpolitrauma]: \n";
                            while ($row = mysql_fetch_array($result))
                            {
                                $data['posibles_iguales'] .= "\n[{$row['Blqclq']}-{$row['Blqdes']}]";
                            }
                            $data['posibles_iguales']  .= "\n\nYa existe un manual con los mismo par&aacute;metros para [$wcodigo_manual-$wnombre_manual]";
                            // $data['posibles_iguales']  .= "\n\n¿Quiere guardar esos mismo valores de parámetros para el manual actual [$wcodigo_manual-$wnombre_manual]?";
                            $data['posibles_iguales']  = str_replace(array("\r\n", "\r", "\n"), "\n",$data['posibles_iguales'] );
                        }
                    }
                    break;

                case 'agregar_clase_excluida':
                        $arr_maestro_clases_insumos = unserialize(base64_decode($arr_maestro_clases_insumos));
                        $data["html"] = clasesExcluidasFacturables($clase_excluida, $arr_maestro_clases_insumos, $campo, $windentificador, $id_manual, 'on');
                    break;

                case 'agregar_procedimiento_relacionado':
                        $arr_procedimientos = unserialize(base64_decode($arr_procedimientos));
                        $data["html"] = procedimientosListaFacturables('',$codigo_procedimiento, $arr_procedimientos, $id_manual, $id_relacion, 'on');
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
                case 'eliminar_acto_qx':
                    $TB_BASE_LIQ_ACTOSQX = TB_BASE_LIQ_ACTOSQX;
                    $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;
                    $sql = "SELECT  id
                            FROM    {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                            WHERE   Liqclq = '{$wcodigo_manual}'
                                    AND Liqtaq = '{$tipo_acto}'
                                    AND Liqcxx = '{$codigo_acto_qx}'
                                    AND Liqest = 'on'";
                    $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
                    $tiene_porcentajes = mysql_num_rows($result);

                    if($tiene_porcentajes == 0)
                    {
                        // Se elimina el registro en actos quirúrgicos y se eliminarán todos los registros de porcentajes de cirugías si
                        // están inactivos.
                        $sql = "DELETE FROM {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX} WHERE id = '{$id_acto_qx}'";
                        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());

                        $sql = "DELETE FROM {$wbasedato}_{$TB_BASE_LIQPORCENTA} WHERE Liqclq = '{$wcodigo_manual}' AND Liqtaq = '{$tipo_acto}' AND Liqcxx = '{$codigo_acto_qx}'";
                        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = "No se puede eliminar porque hay porcentajes activos para cirug&iacute;as m&uacute;ltiples.";
                        /*$sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQ_ACTOSQX}
                                        SET    Cxxbrr = 'on',
                                               Cxxfed = '{$fecha_hora}',
                                               Seguridad = 'C-{$user_session}'
                                WHERE   id = '{$id_acto_qx}'";
                        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());*/
                    }
                    break;

                case 'eliminar_cirugia':
                    // wcodigo_manual
                    // codigo_acto_qx
                    // id_manual
                    // id_acto_qx
                    $TB_BASE_LIQPORCENTA = TB_BASE_LIQPORCENTA;

                    $ids_cirugias_exp = explode("_", $ids_cirugias);
                    $ids_cirugias = implode("','", $ids_cirugias_exp);

                    $sql = "UPDATE {$wbasedato}_{$TB_BASE_LIQPORCENTA}
                                    SET    Liqbrr = 'on',
                                           Liqest = 'off',
                                           Liqfed = '{$fecha_hora}',
                                           Seguridad = 'C-{$user_session}'
                            WHERE   Liqclq = '{$wcodigo_manual}'
                                    AND Liqtaq = '{$tipo_acto}'
                                    AND Liqcxx = '{$codigo_acto_qx}'
                                    AND id IN ('{$ids_cirugias}')";
                    $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
                    break;

                case 'eliminar_relacion_procedimiento':
                        $sql = "DELETE FROM {$wbasedato}_000225 WHERE id = '{$id_relacion}'";
                        $result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
                    break;

                default:
                    $data['mensaje'] = utf8_encode('No se ejecut&oacute; ning&uacute;na rutina interna del programa');
                    break;
            }
            echo json_encode($data);
            break;
        default : break;
    }
    return;
}

include_once("root/comun.php");
// $wbasedato_HCE    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');

$arr_centro_costo = arrayCentroCosto($conex, $wbasedato, $wemp_pmla);
$arr_tipo_empresa = arrayTipoEmpresas($conex, $wbasedato, $wemp_pmla);

$arr_entidades = obtener_array_entidades();

// Array de conceptos a los que se configura un porcentaje por cada número de cirugía.
$sql = "SELECT  c208.Concco AS wcentro_costo, c200.Grucod AS wconcepto, c200.Grudes AS nombre
        FROM    {$wbasedato}_000208 AS c208
                INNER JOIN
                {$wbasedato}_000200 AS c200 ON  (c208.Concod = c200.Grucod)
        WHERE   c208.Conest = 'on'
        ORDER BY c208.Concco, c208.Conord ASC";
$result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
$arr_conceptos_manuales = array();
while ($row = mysql_fetch_array($result))
{
    $wcco_cod = $row['wcentro_costo'];
    $wcod_con = $row['wconcepto'];
    if(!array_key_exists($wcco_cod, $arr_conceptos_manuales))
    {
        $arr_conceptos_manuales[$wcco_cod] = array();
    }

    if(!array_key_exists($wcod_con, $arr_conceptos_manuales[$wcco_cod]))
    {
        $arr_conceptos_manuales[$wcco_cod][$wcod_con] = "";
    }
    $arr_conceptos_manuales[$wcco_cod][$wcod_con] = $row['nombre'];
}


// Array de clases de insumos

$sql = "SELECT  t203.Cpgcod AS codigoClase, t203.Cpgnom AS nombre
        FROM    {$wbasedato}_000203 AS t203
        WHERE   t203.Cpgest = 'on'
        ORDER BY t203.Cpgcod ASC";
$result = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());
$arr_maestro_clases_insumos = array();
while ($row = mysql_fetch_array($result))
{
    if(!array_key_exists($row['codigoClase'], $arr_maestro_clases_insumos))
    {
        $arr_maestro_clases_insumos[$row['codigoClase']] = "";
    }
	//MIGRA_1
    $arr_maestro_clases_insumos[$row['codigoClase']] = utf8_encode($row['nombre']);
}

$arr_procedimientos = obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato);

// Consulta todos los manuales que están guardados en la base de datos y genera el html para mostrarlos en la página.
$TB_BASE_LIQUIDACION = TB_BASE_LIQUIDACION;
$sql = "SELECT  Blqclq AS wcodigo_manual, Blqdes, id AS id_manual
        FROM    {$wbasedato}_{$TB_BASE_LIQUIDACION}
        ORDER BY Blqest Desc, Blqcco, Blqdes ASC";

$html_manuales_creados = array();
$arr_entidadesIni = base64_encode(serialize($arr_entidades));
$resultManuales = mysql_query($sql,$conex) or die("Error: ".mysql_errno()." ".$sql." - ".mysql_error());

?>
<html lang="es-ES">
<head>
    <title>Configurarci&oacute;n Manuales Cirug&iacute;as M&uacute;ltiples</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librería para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <link rel="stylesheet" href="../../../include/ips/facturacionERP.css">

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>


    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;

        $(document).on('mousemove', function(e){
            $('.caja_flotante').css({
               left:  e.pageX+12,
               top:   e.pageY
            });
        });

        // Inicializar primer acordeón
        $(function(){
            $(".acordeon_manual").accordion({
                 collapsible: true
                ,heightStyle: "content"
                ,active: false
            });
        });

        function simularPlaceHolder()
        {
            // Página con etiquetas de html5 de las que se podría verificar su compatibilidad
            // https://github.com/Modernizr/Modernizr/wiki/HTML5-Cross-browser-Polyfills
            // http://geeks.ms/blogs/gperez/archive/2012/01/10/modernizr-ejemplo-pr-225-ctico-1-utilizando-placeholder.aspx
            // http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
            if(!Modernizr.input.placeholder)
            {
                console.log("NAVEGADOR NO COMPATIBLE CON placeholder de HTML5, Se simula atributo placeholder.");
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


        $(document).ready( function () {
            $("#msjEspere").hide();
            simularPlaceHolder();

            inicializarSortable();

            /*$("#accordionDatosPaciente, #acordeon_basicos_liquidacion").accordion({
                collapsible: true,
                heightStyle: "content"
            });

            $("#accordionDetalles").accordion({
                collapsible: true,
                heightStyle: "content",
                active : false
            });*/

            // var datos  = eval('(' + $('#arr_terceros_especialidad').val() + ')');
            // var terceros      = new Array();
            // var index         = -1;
            // for (var cod_ter in datos)
            // {
            //     index++;
            //     terceros[index]                = {};
            //     terceros[index].value          = cod_ter+'-'+datos[cod_ter]['nombre'];
            //     terceros[index].label          = cod_ter+'-'+datos[cod_ter]['nombre'];
            //     terceros[index].codigo         = cod_ter;
            //     terceros[index].nombre         = cod_ter+'-'+datos[cod_ter]['nombre'];
            //     terceros[index].especialidades = datos[cod_ter]['especialidad'];
            // }

            // crearAutocomplete('arr_entidades', 'wempresa','*','*-TODOS'); // wemmpresa pero como clase css para inicializar todos los campos de empresa cuando se carga la página con datos
            // crearAutocomplete('arr_procedimientos', 'wprocedimiento_manual','*','*-TODOS'); // wprocedimiento_manual pero como clase css para inicializar todos los campos de procedimiento cuando se carga la página con datos

            inicializarOnBlurGrabar();
            mantenerTamanioBotones();

            resetStylePrefijo('cirugias_acto_qx');
            inicializarTooltip();

            $("#div_contenedor_manuales").show();
            crearAutocomplete('arr_maestro_clases_insumos', 'clase_excluidos','','');
            $('.clase_excluidos').on({
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

            inicializarCamposProcedimientosAutocomplete('');
        });

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

        function inicializarSortable()
        {
            $( ".div_cirugias_acto_qx" ).sortable({
                cursor: "move",
                tolerance: "intersect",
                stop: function( event, ui ) {
                    var elem_div = $(this).attr("id");
                    recorerActualizarPosicionesCirugias(elem_div);
                },
                items: "> ul"
            });
        }

        function recorerActualizarPosicionesCirugias(this_div)
        {
            var cantidad_cirugias = $("#"+this_div).find("ul").length;
            if(cantidad_cirugias > 0)
            {
                var obJson = {'lista_ids_cirugias':{}};
                var contador_ul = 1;
                var valor_pos_cx = '1';
                $("#"+this_div).find("ul").each(function(){
                    valor_pos_cx = contador_ul.toString();
                    if(contador_ul == cantidad_cirugias) { valor_pos_cx = '*'; } // Si es la última cirugía para pintar entonces debe mostrarse como (*)

                    $(this).attr("cx_posicion",valor_pos_cx);   // cambia el valor del atributo cx_posicion del ul
                    var id_ul   = $(this).attr("id");           // El id del ul
                    var ids_cxs = $(this).attr("ids_cxs");      // son los ids_ de las cirugías a las que se les debe cambiar el orden

                    $("#spn_num_cx_"+id_ul).html(valor_pos_cx); // Visualmente cambia el orden numérico de los ul
                    obJson['lista_ids_cirugias'][ids_cxs] = valor_pos_cx;
                    contador_ul = contador_ul+1;
                });

                resetStylePrefijo('cirugias_acto_qx');
                // console.log(obJson);
                // console.log(Object.keys(obJson['lista_ids_cirugias']).length);
                if(Object.keys(obJson['lista_ids_cirugias']).length > 0)
                {
                    obJson['accion'] = 'update';
                    obJson['form']   = 'modificar_posiciones_cirugias';
                    $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                        obJson,
                        function(data){
                            if(isset(data.error) && data.error == 1)
                            {
                                alert(data.mensaje);
                            }
                            else
                            {
                                console.log("Se modificaron las posiciones de las cirugias ")
                            }
                        },
                        "json"
                    ).done(function(){

                    });
                }
            }
        }

        /**
         * [mantenerTamanioBotones Selecciona todos los elementos tipo boton y les adigna un tamaño uniforme de texto.]
         * @return {[type]} [description]
         */
        function mantenerTamanioBotones()
        {
            $(".acordeon_manual input,select,button").css("font-size","8.5pt");
        }

        /**
         * [crearHtmlEncabezadoManual Se encarga de crear el html de los campos principales de un manual. (el encabezado del manual), si llegan valores para id_manual y wcodigo_manual
         *     entonces a todos los ID's que se creen bajo ese html se les concatenará al final los valores id_manual_wcodigo_manual esto con el fin de diferenciar los campos y encabezados de
         *     cada manual creado, si no llegan seteadas esas variables entonces esos ID's harán parte de un encabezado para un manual en construcción (apenas se está creando).
         *     Esta función se llama tanto para crear el encabezado de un manual nuevo como tambien para crear el encabezado de un manual ya existente en la base de datos (ya tiene un ID asignado
         *     en la base de datos).]
         * @param  {[type]} id_manual      [id que le fupe asignado al manual en la base de datos (wbasedato_000186)]
         * @param  {[type]} wcodigo_manual [Código con el que se creó el manual en la base de datos (wbasedato_000186)]
         * @param  {[type]} crear_nuevo    [Si está seteado quiere decir que se esta creando un nuevo manual y no se ha guardado aún en la base de datos]
         * @return {[type]}                [Como resultado se tiene el código html que muestra en pantalla un acordeón con los parámetros iniciales de un manual nuevo o ya creado con un id asociado en Base de datos]
         */
        function crearHtmlEncabezadoManual(id_manual, wcodigo_manual, crear_nuevo)
        {
            if($("#seccion_manual_").length == 0)
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                     : 'load',
                        form                       : 'cargar_encabezado_html_manual',
                        consultaAjax               : '',
                        wcodigo_manual             : wcodigo_manual,
                        id_manual                  : id_manual,
                        arr_entidades              : $("#arr_entidades_base64").val(),
                        arr_conceptos_manuales     : $("#arr_conceptos_manuales_base64").val(),
                        arr_maestro_clases_insumos : $("#arr_maestro_clases_insumos64").val(),
                        arr_procedimientos         : $("#arr_procedimientos64").val()
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            if($(".acordeon_manual").length == 0)
                            {
                                $("#div_contenedor_manuales").html("");
                            }
                            $("#div_contenedor_manuales").prepend(data.html);
                            aplicarAcordeon("seccion_manual_"+id_manual);
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                    mantenerTamanioBotones();

                    /**>> Autocompletar "entidades" **/
                    crearAutocomplete('arr_entidades', 'wempresa_'+id_manual,data.wempresa,data.wempresa_nombre);
                    crearAutocomplete('arr_procedimientos', 'wprocedimiento_manual_'+id_manual,data.wprocedimiento_manual,data.wprocedimiento_manualNombre);
                    // $("#wpolitrauma_"+id_manual).val(data.wpolitrauma);

                    // Hace que el autocomplete creado pueda reescribir autocomaticamente el texto del input si le borran parte del texto.
                    $('#wempresa_'+id_manual).on({
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

                    $('#wprocedimiento_manual_'+id_manual).on({
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

                    $('.clase_excluidos').on({
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

                    crearAutocomplete('arr_maestro_clases_insumos', 'clase_excluidos','','');


                    inicializarTooltip();
                    inicializarOnBlurGrabar();
                });
            }
            else
            {
                alert("Ya esta creando un manual que aun esta sin guardar.");
            }
        }

        function inicializarTooltip()
        {
            $(".msj_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
        }

        /**
         * [crearNuevoActoQx Se encarga de llamar las rutinas para insertar un nuevo acto quirúrgico para el manual que se está editando y se encarga de crear el html con la sección correspondiente
         *     a un acto quirúrgico]
         * @param  {[type]} id_manual      [id del manual al que corresponde el acto quirúrgico]
         * @param  {[type]} wcodigo_manual [Código del manual al que corresponde el acto quirúrgico]
         * @return {[type]}                [Como resultado se tiene el codigo html que muestra en pantalla una sección de acto quirúrgico donde se pueden agregar nuevas cirugías]
         */
        function crearNuevoActoQx(id_manual, wcodigo_manual, tipo_acto)
        {
            var actos_creados = $("#div_actos_quirurgicos_"+tipo_acto+id_manual).find(".tabla_acto_qx").length;
            if(actos_creados < 4)
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                 : 'load',
                        form                   : 'cargar_html_nuevo_acto_qx',
                        consultaAjax           : '',
                        id_manual              : id_manual,
                        tipo_acto              : tipo_acto,
                        wcodigo_manual         : wcodigo_manual,
                        wcentro_costo          : $("#wcentro_costo_"+id_manual).val(),
                        arr_conceptos_manuales : $("#arr_conceptos_manuales_base64").val()
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            $("#log_error").html(data.log_error);
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#div_actos_quirurgicos_"+tipo_acto+id_manual).append(data.html);
                        }
                    },
                    "json"
                ).done(function(){
                    inicializarOnBlurGrabar();
                    mantenerTamanioBotones();
                    inicializarTooltip();
                });
            }
            else
            {
                alert("No es posible crear mas actos quirurgicos para este manual.");
            }
        }

        /**
         * [crearNuevaCirugia Esta función se encarga de crear una nueva fila para indicar nueva cirugía asociada a un acto quirúrgico, estas filas se inicializar con "sortable" de jquery
         *     para que se dejen cambiar de posición y así dar un orden a las cirugías.
         *     Se encarga de insertar un nuevo registro en la base de datos correspondiente a una nueva cirugía asociada al manual y acto quirúrgico que se esta editando y de mostrar el
         *     html de esa nueva cirugía donde se asignarán nuevos porcentajes para liquidación]
         * @param  {[type]} id_manual               [id del manual en la base de datos]
         * @param  {[type]} id_acto_qx              [id del acto quirúrgico al que se asociará la nueva cirugía]
         * @param  {[type]} wcodigo_manual          [Código del manual]
         * @param  {[type]} codigo_acto_qx          [Código del acto quirúrgico]
         * @param  {[type]} codigo_numero_cirugia   [Código asignado a la cirugía en la base de datos]
         * @return {[type]}                         [description]
         */
        function crearNuevaCirugia(id_manual, id_acto_qx, wcodigo_manual, codigo_acto_qx, tipo_acto)
        {
            var porcentajes_iguales = false;
            var id_div_acto_qx = $("#div_cirugias_acto_qx_"+tipo_acto+id_manual+"_"+id_acto_qx).attr("id");
            var fila_con_ceros = false;
            $("#"+id_div_acto_qx).find("ul[id^="+tipo_acto+id_manual+"_"+id_acto_qx+"_]").each(function(){
                var id_ul = $(this).attr("id");
                var dif_cero = true;
                $("#"+id_ul).find("input[id^=wpocentaje_"+tipo_acto+id_manual+"_"+id_acto_qx+"]").each(function(){
                    var valor = $(this).val();
                    if(valor=='') { valor = '0'; }

                    if(parseInt(valor) == 0){}
                    else { dif_cero = false }
                });

                if(dif_cero)
                {
                    fila_con_ceros = true;
                }
            });

            if(!fila_con_ceros)
            {
                var ver_vias_bilateral = "";
                if($("#wbilateral_"+tipo_acto+id_manual+"_"+id_acto_qx).length > 0 && $("#wbilateral_"+tipo_acto+id_manual+"_"+id_acto_qx).is(":visible")
                    && $("#wbilateral_"+tipo_acto+id_manual+"_"+id_acto_qx).is(":checked") )
                {
                    ver_vias_bilateral = 'on';
                }

                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                 : 'load',
                        form                   : 'cargar_html_nueva_cirugia',
                        consultaAjax           : '',
                        id_manual              : id_manual,
                        id_acto_qx             : id_acto_qx,
                        wcodigo_manual         : wcodigo_manual,
                        codigo_acto_qx         : codigo_acto_qx,
                        tipo_acto              : tipo_acto,
                        wcentro_costo          : $("#wcentro_costo_"+id_manual).val(),
                        arr_conceptos_manuales : $("#arr_conceptos_manuales_base64").val(),
                        no_existe_en_bd        : 'no_existe',
                        ver_vias_bilateral     : ver_vias_bilateral
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#div_cirugias_acto_qx_"+tipo_acto+id_manual+'_'+id_acto_qx).append(data.html);
                        }
                    },
                    "json"
                ).done(function(){

                    var cirugias = $("span[id^=spn_num_cx_"+tipo_acto+id_manual+'_'+id_acto_qx+"_]").length;
                    // console.log(cirugias);
                    var contador = 0;
                    $("[id^=spn_num_cx_"+tipo_acto+id_manual+'_'+id_acto_qx+"_]").each(function(){
                        contador = contador+1;
                        // console.log(contador);
                        // console.log($(this));
                        if(contador == cirugias)
                        {
                            $(this).html("*");
                        }
                        else
                        {
                            $(this).html(contador);
                        }
                    });

                    resetStylePrefijo('cirugias_acto_qx');
                    mantenerTamanioBotones();
                    inicializarSortable();

                    inicializarOnBlurGrabar();

                    var btn_estado_acto = $("#btn_estado_acto_qx_"+tipo_acto+id_manual+"_"+id_acto_qx);
                    var estado = $(btn_estado_acto).attr("estado");

                    if(estado == 'off')
                    {
                        cambiarEstadoActoQx(btn_estado_acto, id_manual, id_acto_qx, tipo_acto);
                    }
                });
            }
            else
            {
                alert("Se intentó adicionar una nueva fila de cirugia con porcentajes en cero, pero ya existe una cirugia con esos mismos porcentajes.")
            }
        }

        /**
         * [guardarNuevoManualContinuar Esta función se encarga de validar sí el código que se está escribiendo para el manual ya existe en la base de datos, si es así entonces informa
         *     mediante una alerta al usuario, Si los parámetros principales como tipo de empresa, empresa y politraumatizados también tienen coincidencia con los parámetros de un
         *     manual ya guardado entonces muestra una alerta]
         * @return {[type]} [description]
         */
        function guardarNuevoManualContinuar()
        {
            var wnombre_manual          = $("#wnombre_manual_").val();
            var wcodigo_manual          = $("#wcodigo_manual_").val();
            var wcentro_costo           = $("#wcentro_costo_").val();
            var wtipoempresa            = $("#wtipoempresa_").val();
            var wempresa                = $("#wempresa_").attr("codigo");
            var wprocedimiento_manual   = $("#wprocedimiento_manual_").attr("codigo");
            var wpolitrauma             = $("#wpolitrauma_").val();
            // var wuvr_maximo_nof       = $("#wuvr_maximo_nof_").val();
            // var wconcepto_uvr_nof     = $("#wconcepto_uvr_nof").val();
            // var wgrupo_maximo_nof     = $("#wgrupo_maximo_nof_").val();
            // var wconcepto_grupoqx_nof = $("#wconcepto_grupoqx_nof").val();
            var wliquida_tiempos        = ($("#wliquida_tiempos_").is(":checked")) ? 'on': 'off';
            var wvalidar_por_articulo   = ($("#wvalidar_por_articulo_").is(":checked")) ? 'on': 'off';
            var wredondear_centena      = ($("#wredondear_centena").is(":checked")) ? 'on': 'off';
            var winsumos_no_facturables = ($("#winsumos_no_facturables").is(":checked")) ? 'on': 'off';

            var is_ok = true;

            if(wnombre_manual.replace(/ /gi, "") == '') { is_ok = false; }
            if(wcodigo_manual.replace(/ /gi, "") == '') { is_ok = false; }
            if(wtipoempresa.replace(/ /gi, "") == '') { is_ok = false; }
            if(wcentro_costo.replace(/ /gi, "") == '') { is_ok = false; }
            if(wempresa.replace(/ /gi, "") == '') { is_ok = false; }
            if(wprocedimiento_manual.replace(/ /gi, "") == '') { is_ok = false; }

            if(is_ok)
            {
                //verificar posible manual con mismo parametros principales
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                  : 'load',
                        form                    : 'validar_posible_manual_igual',
                        consultaAjax            : '',
                        wnombre_manual          : wnombre_manual,
                        wcodigo_manual          : wcodigo_manual,
                        wcentro_costo           : wcentro_costo,
                        wtipoempresa            : wtipoempresa,
                        wempresa                : wempresa,
                        wprocedimiento_manual   : wprocedimiento_manual,
                        wpolitrauma             : wpolitrauma,
                        wliquida_tiempos        : wliquida_tiempos,
                        wvalidar_por_articulo   : wvalidar_por_articulo,
                        wredondear_centena      : wredondear_centena,
                        winsumos_no_facturables : winsumos_no_facturables
                    },
                    function(dataValidar){
                        if(isset(dataValidar.error) && dataValidar.error == 1)
                        {
                            alert($.trim(dataValidar.mensaje));
                        }
                        else
                        {
                            if(dataValidar.hay_parecidos != 0)
                            {
                                if(confirm(dataValidar.posibles_iguales))
                                {
                                    // guardarEncabezadoManual();
                                }
                            }
                            else
                            {
                                guardarEncabezadoManual();
                            }

                        }
                    },
                    "json"
                ).done(function(){
                    /*resetStylePrefijo('cirugias_acto_qx');
                    mantenerTamanioBotones();
                    inicializarSortable();*/
                });
            }
            else
            {
                alert("Los datos no estan completos\nEl campo de Nombre, Codigo o Empresa no deben estar vacios.");
            }
        }

        /**
         * [guardarEncabezadoManual Se encarga de insertar en la base de datos un nuevo manual y para pintar de nuevo el html teniendo como referencia el nuevo id creado,
         *     entonces llama la función 'crearHtmlEncabezadoManual' enviándole el nuevo id y el nuevo código de manual para que se encargue de escribir el encabezado
         *     regerenciado sus ID's de etiquetas html con el nuevo id de base de datos.]
         * @return {[type]} [retorna el nuevo ID y el código del manual creado en la base de datos]
         */
        function guardarEncabezadoManual()
        {
            var wnombre_manual          = $("#wnombre_manual_").val();
            var wcodigo_manual          = $("#wcodigo_manual_").val();
            var wcentro_costo           = $("#wcentro_costo_").val();
            var wtipoempresa            = $("#wtipoempresa_").val();
            var wempresa                = $("#wempresa_").attr("codigo");
            var wprocedimiento_manual   = $("#wprocedimiento_manual_").attr("codigo");
            var wpolitrauma             = $("#wpolitrauma_").val();
            var wliquida_tiempos        = ($("#wliquida_tiempos_").is(":checked")) ? 'on': 'off';
            var wvalidar_por_articulo   = ($("#wvalidar_por_articulo").is(":checked")) ? 'on': 'off';
            var wredondear_centena      = ($("#wredondear_centena").is(":checked")) ? 'on': 'off';
            var winsumos_no_facturables = ($("#winsumos_no_facturables").is(":checked")) ? 'on': 'off';

            // var wuvr_maximo_nof                = $("#wuvr_maximo_nof_").val();
            // var wconcepto_uvr_nof              = $("#wconcepto_uvr_nof").val();
            // var wgrupo_maximo_nof              = $("#wgrupo_maximo_nof_").val();
            // var wconcepto_grupoqx_nof          = $("#wconcepto_grupoqx_nof_").val();

            // var wuvr_maximo_nof_material       = $("#wuvr_maximo_nof_material_").val();
            // var wconcepto_uvr_nof_material     = $("#wconcepto_uvr_nof_material_").val();
            // var wgrupo_maximo_nof_material     = $("#wgrupo_maximo_nof_material_").val();
            // var wconcepto_grupoqx_nof_material = $("#wconcepto_grupoqx_nof_material_").val();

            // var wuvr_maximo_nof_ayudante       = $("#wuvr_maximo_nof_ayudante_").val();
            // var wgrupo_maximo_nof_ayudante     = $("#wgrupo_maximo_nof_ayudante_").val();

            // var clases_excluidos_medicamentos_uvr   = validarGuardarInsumosExcluidos('', 'div_checks_clases_excluidos_medicamentos_uvr_', '', 'on');
            // var clases_excluidos_material_uvr       = validarGuardarInsumosExcluidos('', 'div_checks_clases_excluidos_material_uvr_', '', 'on');
            // var clases_excluidos_medicamentos_grupo = validarGuardarInsumosExcluidos('', 'div_checks_clases_excluidos_medicamentos_grupo_', '', 'on');
            // var clases_excluidos_material_grupo     = validarGuardarInsumosExcluidos('', 'div_checks_clases_excluidos_material_grupo_', '', 'on');

            var is_ok = true;

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion                              : 'insert',
                    form                                : 'guardar_nuevo_manual_continuar',
                    consultaAjax                        : '',
                    wnombre_manual                      : wnombre_manual,
                    wcodigo_manual                      : wcodigo_manual,
                    wcentro_costo                       : wcentro_costo,
                    wtipoempresa                        : wtipoempresa,
                    wempresa                            : wempresa,
                    wprocedimiento_manual               : wprocedimiento_manual,
                    wpolitrauma                         : wpolitrauma,
                    wliquida_tiempos                    : wliquida_tiempos,
                    wvalidar_por_articulo               : wvalidar_por_articulo,
                    wredondear_centena                  : wredondear_centena,
                    winsumos_no_facturables             : winsumos_no_facturables,
                    wuvr_maximo_nof                     : '', //wuvr_maximo_nof,
                    wconcepto_uvr_nof                   : '', //wconcepto_uvr_nof,
                    wgrupo_maximo_nof                   : '', //wgrupo_maximo_nof,
                    wconcepto_grupoqx_nof               : '', //wconcepto_grupoqx_nof,
                    wuvr_maximo_nof_material            : '', //wuvr_maximo_nof_material,
                    wconcepto_uvr_nof_material          : '', //wconcepto_uvr_nof_material,
                    wgrupo_maximo_nof_material          : '', //wgrupo_maximo_nof_material,
                    wconcepto_grupoqx_nof_material      : '', //wconcepto_grupoqx_nof_material,
                    wuvr_maximo_nof_ayudante            : '', //wuvr_maximo_nof_ayudante,
                    wgrupo_maximo_nof_ayudante          : '', //wgrupo_maximo_nof_ayudante,
                    clases_excluidos_medicamentos_uvr   : '', //clases_excluidos_medicamentos_uvr,
                    clases_excluidos_material_uvr       : '', //clases_excluidos_material_uvr,
                    clases_excluidos_medicamentos_grupo : '', //clases_excluidos_medicamentos_grupo,
                    clases_excluidos_material_grupo     : '', //clases_excluidos_material_grupo
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                       $("#log_error").html(data.log_error);
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#seccion_manual_").remove();
                    }
                    return data;
                },
                "json"
            ).done(function(data){
                if(data.error != 1)
                {
                    crearHtmlEncabezadoManual(data.id_manual, wcodigo_manual, '');
                }
            });
        }

        function cambiarEstadoActoQx(elem, id_manual, id_acto_qx, tipo_acto)
        {
            var estado = $(elem).attr("estado");
            var desc_boton = "Activar";
            if(estado == 'on') { estado = 'off'; }
            else { estado = 'on'; desc_boton = "Inactivar"; }
            var error = 0;

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion           : 'update',
                    form             : 'cambiar_estado_acto_qx',
                    consultaAjax     : '',
                    id_acto_qx       : id_acto_qx,
                    estado           : estado
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        error = 1;
                       $("#log_error").html(data.log_error);
                        alert(data.mensaje);
                    }
                    else
                    {
                        $(elem).attr("estado",estado);
                    }
                    return data;
                },
                "json"
            ).done(function(data){
                if(error == 0)
                {
                    $(elem).html(desc_boton);
                    if(estado == 'on')
                    {
                        $("#img_estado_acto_qx_"+tipo_acto+id_manual+'_'+id_acto_qx+'_activo').show();
                        $("#img_estado_acto_qx_"+tipo_acto+id_manual+'_'+id_acto_qx+'_inactivo').hide();
                    }
                    else
                    {
                        $("#img_estado_acto_qx_"+tipo_acto+id_manual+'_'+id_acto_qx+'_inactivo').show();
                        $("#img_estado_acto_qx_"+tipo_acto+id_manual+'_'+id_acto_qx+'_activo').hide();
                    }
                }
            });
        }

        function cambiarEstadoManual(elem, id_manual)
        {
            var estado = $(elem).attr("estado");
            var desc_boton = "Activar";
            if(estado == 'on') { estado = 'off'; }
            else { estado = 'on'; desc_boton = "Inactivar"; }

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion           : 'update',
                    form             : 'cambiar_estado_manual',
                    consultaAjax     : '',
                    id_manual       : id_manual,
                    estado           : estado
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                       $("#log_error").html(data.log_error);
                        alert(data.mensaje);
                    }
                    else
                    {
                        $(elem).attr("estado",estado);
                    }
                    return data;
                },
                "json"
            ).done(function(data){
                $(elem).html(desc_boton);
                if(estado == 'on')
                {
                    $("#img_estado_manual_"+id_manual+'_activo').show();
                    $("#img_estado_manual_"+id_manual+'_inactivo').hide();
                    $("#img_estado_manual_h3_"+id_manual+'_activo').show();
                    $("#img_estado_manual_h3_"+id_manual+'_inactivo').hide();
                }
                else
                {
                    $("#img_estado_manual_"+id_manual+'_inactivo').show();
                    $("#img_estado_manual_"+id_manual+'_activo').hide();
                    $("#img_estado_manual_h3_"+id_manual+'_inactivo').show();
                    $("#img_estado_manual_h3_"+id_manual+'_activo').hide();
                }
            });
        }


        function cambiarEstadoCirugias(elem, id_manual, id_acto_qx, ids_cx_conceptos, tipo_acto)
        {
            var estado = $(elem).attr("estado");
            var desc_boton = "Activar";
            if(estado == 'on') { estado = 'off'; }
            else { estado = 'on'; desc_boton = "Inactivar"; }

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion           : 'update',
                    form             : 'cambiar_estado_cirugia',
                    consultaAjax     : '',
                    ids_cx_conceptos : ids_cx_conceptos,
                    estado           : estado
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                       $("#log_error").html(data.log_error);
                        alert(data.mensaje);
                    }
                    else
                    {
                        $(elem).attr("estado",estado);
                    }
                    return data;
                },
                "json"
            ).done(function(data){
                $(elem).val(desc_boton);
                if(estado == 'on')
                {
                    $("#img_estado_cirugia_"+tipo_acto+id_manual+"_"+id_acto_qx+"_"+ids_cx_conceptos+'_activo').show();
                    $("#img_estado_cirugia_"+tipo_acto+id_manual+"_"+id_acto_qx+"_"+ids_cx_conceptos+'_inactivo').hide();
                }
                else
                {
                    $("#img_estado_cirugia_"+tipo_acto+id_manual+"_"+id_acto_qx+"_"+ids_cx_conceptos+'_inactivo').show();
                    $("#img_estado_cirugia_"+tipo_acto+id_manual+"_"+id_acto_qx+"_"+ids_cx_conceptos+'_activo').hide();
                }
            });
        }

        /**
         * [escribirCodigoManual Según el texto escrito en el campo de nombre, esta función se encarga de procesar ese texto para convertirlo en una cadena sin
         *     espacios ni acentos.]
         * @param  {[type]} elem           [description]
         * @param  {[type]} wcodigo_manual [description]
         * @return {[type]}                [description]
         */
        function escribirCodigoManual(elem,wcodigo_manual)
        {
            var nombre = $(elem).val();
            if($("#"+wcodigo_manual).attr("codigo_guardado").replace(/ /gi, "") == '')
            {
                limpiarCodigo(nombre, wcodigo_manual);
            }
        }

        function acordeonNombre(elem,id_manual)
        {
            var nombre = $(elem).val();
            if(nombre.replace(/ /gi, "") == '')
            {
                $("#spn_nombre_manual_"+id_manual).html("Nuevo Manual");
            }
            else
            {
                $("#spn_nombre_manual_"+id_manual).html(nombre);
            }
        }

        function limpiarCodigo(nombre, wcodigo_manual)
        {
            // Quitar acentos, espacios, mayúscula
            nombre = omitirAcentos(nombre);
            nombre = nombre.replace(/ /gi, "");
            nombre = nombre.replace(/\(/gi, "");
            nombre = nombre.replace(/\)/gi, "");
            nombre = nombre.replace(/\¨/gi, "");
            nombre = nombre.toUpperCase();
            $("#"+wcodigo_manual).val(nombre);
        }

        function actualizarRegistro(elem)
        {
            // Script para actualizar el valor de porcentaje
            var id_registro     = $(elem).attr("id_registro");
            var actualiza       = $(elem).attr("actualiza");
            var wconcepto       = $(elem).attr("wconcepto");
            var id_acto_qx      = $(elem).attr("id_acto_qx");
            var tipo_acto       = $(elem).attr("tipo_acto");
            var id_manual       = $(elem).attr("id_manual");
            var puede_vacio     = $(elem).attr("puede_vacio");
            var chk_bilat       = $(elem).attr("chk_bilat"); // aplica para el campo de via y de especialidad con el fin de validar si cuando se van a actualizar esos campos, el check de bilateral esta chequeado o no

            var error = 0;
            var posicion = $(elem).parent().parent().attr("cx_posicion");
            console.log(posicion);
            var valor_actualiza = $(elem).val();

            if($(elem).attr("type") == "checkbox")
            {
                valor_actualiza = ($(elem).is(":checked")) ? 'on': 'off';
            }

            if(actualiza == 'procedimiento_no_fact')
            {
                valor_actualiza = $(elem).attr("codigo");
            }

            if((actualiza == "numero_via" || actualiza == "numero_especialista") && $("#"+chk_bilat).attr("checked") == 'checked')
            {
                puede_vacio = 'on';
            }
            // if(id_registro != '')
            if(valor_actualiza != '' || puede_vacio == 'on')
            {
                if($(elem).attr("type") == "checkbox")
                {
                    valor_actualiza = ($(elem).is(":checked")) ? 'on': 'off';
                }

                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion           : 'update',
                        form             : 'actualizar_registro_bd',
                        consultaAjax     : '',
                        id_registro      : id_registro,
                        valor_actualiza  : valor_actualiza,
                        actualiza        : actualiza,
                        posicion         : posicion,
                        wconcepto        : wconcepto,
                        tipo_acto        : tipo_acto,
                        id_manual        : id_manual,
                        id_acto_qx       : id_acto_qx
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            error = 1;
                           $("#log_error").html(data.log_error);
                            alert(data.mensaje);
                        }
                        else
                        {
                            if(data.id_registro_nuevo != "")
                            {
                                $(elem).attr("id_registro",data.id_registro_nuevo);
                            }

                            if(actualiza == 'acto_bilateral')
                            {
                                // console.log("select[id^=sel_via_bilateral_"+id_manual+"_"+id_acto_qx);
                                $("select[id^=sel_via_bilateral_"+tipo_acto+id_manual+"_"+id_acto_qx+"_]").each(function(){
                                   $(this).val('');
                                   // consoloe.log($(this).id);
                                });
                            }
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                    if(error == 1)
                    {
                        if($(elem).attr("type") == "checkbox")
                        {
                            if($(elem).is(":checked"))
                            {
                                $(elem).removeAttr("checked");
                            }
                            else
                            {
                                $(elem).attr("checked","checked");
                            }
                        }
                        else
                        {
                            $(elem).val("");
                        }
                    }
                });
            }
        }

        function eliminarActoQx(wcodigo_manual, codigo_acto_qx, id_manual, id_acto_qx, tipo_acto)
        {
            if(confirm("¿Esta seguro de eliminar la configuracion para este acto quirurgico?"))
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion             : 'delete',
                        form               : 'eliminar_acto_qx',
                        consultaAjax       : '',
                        wcodigo_manual     : wcodigo_manual,
                        codigo_acto_qx     : codigo_acto_qx,
                        tipo_acto          : tipo_acto,
                        id_manual          : id_manual,
                        id_acto_qx         : id_acto_qx
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                           $("#log_error").html(data.log_error);
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#tabla_acto_qx_"+tipo_acto+id_manual+'_'+id_acto_qx).hide("slow",
                                    function(){
                                        $("#tabla_acto_qx_"+tipo_acto+id_manual+'_'+id_acto_qx).remove();
                                });
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                });
            }
        }

        function eliminarCirugia(wcodigo_manual, codigo_acto_qx, id_manual, id_acto_qx, ids_cirugias, tipo_acto)
        {
            var id_ul = tipo_acto+id_manual+'_'+id_acto_qx+'_'+ids_cirugias;
            var id_div_cxs = "div_cirugias_acto_qx_"+tipo_acto+id_manual+'_'+id_acto_qx;
            if(confirm("¿Esta seguro de eliminar la configuracion para este numero de cirugia?"))
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion             : 'delete',
                        form               : 'eliminar_cirugia',
                        consultaAjax       : '',
                        wcodigo_manual     : wcodigo_manual,
                        codigo_acto_qx     : codigo_acto_qx,
                        tipo_acto          : tipo_acto,
                        id_manual          : id_manual,
                        id_acto_qx         : id_acto_qx,
                        ids_cirugias       : ids_cirugias
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                           $("#log_error").html(data.log_error);
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#"+id_ul).hide("slow",
                                    function(){
                                        //
                                });
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                    $("#"+id_ul).remove();

                    // Organiza visualmente las posiciones de las cirugías
                    var cirugias = $("span[id^=spn_num_cx_"+tipo_acto+id_manual+'_'+id_acto_qx+"_]").length;
                    // console.log(cirugias);
                    var contador = 0;
                    $("[id^=spn_num_cx_"+tipo_acto+id_manual+'_'+id_acto_qx+"_]").each(function(){
                        contador = contador+1;
                        // console.log(contador);
                        // console.log($(this));
                        if(contador == cirugias)
                        {
                            $(this).html("*");
                        }
                        else
                        {
                            $(this).html(contador);
                        }
                    });
                    recorerActualizarPosicionesCirugias(id_div_cxs);
                });
            }
        }

        /**
         * [ocultarOtrosCamposBilateral Esta función de encarga de mostrar un solo checkbox bilateral por cada manual, si se desmarca entonces se habilitan todos los demás
         *     checkox hasta que nuevamente se seleccione un acto como bilateral]
         * @param  {[type]} check         [this del elemento checkbox al que le acaban de dar clic]
         * @param  {[type]} prefijo_campo [prefijo de los demás campos checkbox bilaterales del mismo manual]
         * @param  {[type]} id_manual     [id de base de datos del manual que está en edición]
         * @return {[type]}               [Muestra un solo checkbox bilateral si está chequeado o todos los campos bilateral si no hay checheqado (para un mismo manual)]
         */
        function ocultarOtrosCamposBilateral(check,prefijo_campo,id_manual,tipo_acto)
        {
            if($(check).is(":checked"))
            {
                var id_registro = $("#"+check.id).attr("id_registro");
                // $(".columna_vias_bilateral").show();
                $(".divtabla_vias_bilateral_"+tipo_acto+id_manual+"_"+id_registro).show();
                $(".via_esp_"+tipo_acto+id_manual+"_"+id_registro).hide();

                // Resetea los campos de vía y especialidad del acto quirúrgico
                $(".via_esp_"+tipo_acto+id_manual+"_"+id_registro).find(":input").each(function(){
                    $(this).val("");
                });

                // console.log(check.id);
                // Ocultar el resto de chexboxes bilaterales del manual
                $("table[id^=tabla_acto_qx_"+tipo_acto+id_manual+"]").find(":checkbox[id^="+prefijo_campo+"]").each(function(){
                    var id_iteracion = $(this).attr("id");
                    // console.log(check.id+" = "+id_iteracion);
                    if(check.id == id_iteracion){}
                    else
                    {
                        $(this).parent().parent().hide();
                    }
                });
            }
            else
            {
                var id_registro = $("#"+check.id).attr("id_registro");
                // $(".columna_vias_bilateral").hide();
                $(".divtabla_vias_bilateral_"+tipo_acto+id_manual+"_"+id_registro).hide();
                $(".via_esp_"+tipo_acto+id_manual+"_"+id_registro).show();

                // Resetea los campos de vía de porcentajes bilaterales
                $(".div_vias_bilateral_"+tipo_acto+id_manual+"_"+id_registro).each(function(){
                    $(this).val("");
                });

                // Resetea los campos de especialidad de porcentajes bilaterales
                $(".div_esp_bilateral_"+tipo_acto+id_manual+"_"+id_registro).each(function(){
                    $(this).val("");
                });

                // Mostrar todos los chexboxes bilaterales del manual
                $("table[id^=tabla_acto_qx_"+tipo_acto+id_manual+"]").find(":checkbox[id^="+prefijo_campo+"]").each(function(){
                    var id_iteracion = $(this).attr("id");
                    // console.log(check.id+" = "+id_iteracion);
                    $(this).parent().parent().show();
                });
            }
        }

        function validarGuardarInsumosExcluidos(check, div_contenedor_clase, id_manual, retornar_clases)
        {
            var clases_excluidas = '';
            var separador = '';
            var contador_true = 0;
            $("#"+div_contenedor_clase).find(":checkbox").each(function(){
                if($(this).is(":checked"))
                {
                    contador_true++;
                    clases_excluidas = clases_excluidas+separador+$(this).val();
                    separador = '|';
                }
            });
            $("#excluidos_sum_"+div_contenedor_clase).html(contador_true);

            if(retornar_clases == 'on')
            {
                return clases_excluidas;
            }

            var campo = $("#hdn_campo_"+div_contenedor_clase).val();
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion           : 'update',
                        form             : 'actualizar_registro_bd',
                        consultaAjax     : '',
                        actualiza        : 'actualizar_exluidos',
                        campo            : campo,
                        clases_excluidas : clases_excluidas,
                        id_manual        : id_manual
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                           $("#log_error").html(data.log_error);
                            alert(data.mensaje);
                        }
                        else
                        {
                            //
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                    if(check != '')
                    {
                        if(!$(check).is(":checked") && data.error == 0)
                        {
                            var clase = $(check).val();
                            var div = "div_check_clase_"+div_contenedor_clase+"_"+clase;
                            // var id_div = $(check).parent().attr("id");
                            // console.log(div);
                            $("#"+div).remove();
                        }
                    }
                });
        }

        function validarGuardarProcedimientoLista(check, id_manual, id_relacion, codigo_procedimiento)
        {
            var lista_procedimientos = '';
            var separador = '';
            var contador_true = 0;
            $("#div_lista_procedimientos_anulan_"+id_manual+"_"+id_relacion).find(":checkbox").each(function(){
                if($(this).is(":checked"))
                {
                    contador_true++;
                    lista_procedimientos = lista_procedimientos+separador+$(this).val();
                    separador = '|';
                }
            });

            // if(retornar_clases == 'on')
            // {
            //     return lista_procedimientos;
            // }

             // console.log(lista_procedimientos);
             // return;

            // var campo = $("#hdn_campo_"+div_contenedor_clase).val();
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion               : 'update',
                        form                 : 'actualizar_registro_bd',
                        consultaAjax         : '',
                        actualiza            : 'actualizar_relacion_procedimientos',
                        lista_procedimientos : lista_procedimientos,
                        id_manual            : id_manual,
                        id_relacion          : id_relacion
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                           $("#log_error").html(data.log_error);
                            alert(data.mensaje);
                        }
                        else
                        {
                            //
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                    //
                });
        }

        /**
         * [actualizarViaBilateralCx Cuando se selecciona acto quirúrgico como bilateral y para una sirugía de este tipo se lecciona un tipo de vía,
         * esta función se encarga de actualizar los registros del número de cirugía con el valor seleccionado en el tipo de vía, la actualización se realiza sobre
         * la lista de IDs de las cirugías que se pasan por parámetros a esta función]
         * @return {[type]} [description]
         */
        function actualizarViaBilateralCx(elem, id_manual, id_acto_qx, ids_cx_conceptos)
        {
            var valor_nuevo = $(elem).val();
            var campo = $(elem).attr("campo");

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion           : 'update',
                    form             : 'cambiar_via_esp_cirugia_bilateral',
                    consultaAjax     : '',
                    ids_cx_conceptos : ids_cx_conceptos,
                    valor_nuevo      : valor_nuevo,
                    campo            : campo
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                       $("#log_error").html(data.log_error);
                        alert(data.mensaje);
                    }
                    else
                    {
                        //
                    }
                    return data;
                },
                "json"
            ).done(function(data){
                //
            });
        }

        /**
         * [controlarPorcentajesIguales Esta función valída que no se repitan porcentajes de cirugías con porcentajes en cero, si dos cirugías o más tienen los mismos porcentajes
         *                              mayores a cero serán permitido, pero dos cirugías con porcentajes en cero no se permitirá.]
         * @param  {[type]} this_ [description]
         * @param  {[type]} num   [description]
         * @return {[type]}       [description]
         */
        function controlarPorcentajesIguales(this_,num)
        {
            var id_ul_editado = $(this_).parent().parent().attr("id");
            var id_manual     = $(this_).attr("id_manual");
            var id_acto_qx    = $(this_).attr("id_acto_qx");
            var tipo_acto     = $(this_).attr("tipo_acto");
            var id_div_acto_qx = $("#div_cirugias_acto_qx_"+tipo_acto+id_manual+"_"+id_acto_qx).attr("id");

            // Consultar los valores ingresados en la fila editada
            var editado_comparar = 0;
            $("#"+id_ul_editado).find("input[id^=wpocentaje_"+tipo_acto+id_manual+"_"+id_acto_qx+"]").each(function(){
                var valor = $(this).val();
                if($(this).attr("id") == $(this_).attr("id"))
                {
                    // valor = valor+''+num;
                    if(valor == '') { valor = 0; }
                    valor_aux = valor+''+num;
                    valor = parseInt(valor_aux);
                }
                // editado_comparar = editado_comparar+"_"+valor;
                editado_comparar = editado_comparar+parseInt(valor);
                editado_comparar = parseInt(editado_comparar);
            });
            // console.log(editado_comparar);

            // console.log(id_div_acto_qx);
            var porcentajes_iguales = false;
            $("#"+id_div_acto_qx).find("ul[id^="+tipo_acto+id_manual+"_"+id_acto_qx+"_]").each(function(){
                var id_ul = $(this).attr("id");
                // $("#"+id_ul).find("wpocentaje_"+tipo_acto+id_manual+"_"+id_acto_qx+"_")
                // console.log(id_ul);
                if(id_ul_editado != id_ul)
                {
                    var valores_comparar = 0;
                    $("#"+id_ul).find("input[id^=wpocentaje_"+tipo_acto+id_manual+"_"+id_acto_qx+"]").each(function(){
                        var valor = $(this).val();
                        // valores_comparar = valores_comparar+"_"+valor;
                        valores_comparar = valores_comparar+parseInt(valor);
                    });
                    // console.log(valores_comparar);

                    if(valores_comparar == 0 && valores_comparar == editado_comparar)
                    {
                        porcentajes_iguales = true;
                    }
                }
            });

            if(porcentajes_iguales)
            {
                alert("Ya existe una cirugia con igual valor de porcentajes para este acto quirurgico.");
                $(this_).val("");
                return false;
            }
            else
            {
                return true;
            }
        }

        function omitirAcentos(text)
        {
           var acentos = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç";
           var original = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc";
           for (var i=0; i<acentos.length; i++) {
               text = text.replace(acentos.charAt(i), original.charAt(i));
           }
           return text;
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

        function inicializarOnBlurGrabar()
        {
            $('.onBlurGrabar').on({
                focusout: function(e) {
                    actualizarRegistro(this);
                }
            });
        }

        $(function(){
            $('.numerico').on({
                keypress: function(e) {
                    var r = soloNumeros(e);
                    if(r==true)
                    {
                        var codeentr = (e.which) ? e.which : e.keyCode; /*if(codeentr == 13) { buscarDatosBasicos(); }*/
                        // Se hace una validacion adicional para poder controlar cuando se escribe un porcentaje para una cirugía
                        // se controla que cada que se escriba un número, este no haga coincidir a los mismo porcentajes de otra cirugía
                        // para un mismo acto quirúrgico.
                        var ctrl_porcent = true;
                        var num = '';
                        if($(this).attr("validar") != undefined)
                        {
                            num = String.fromCharCode(codeentr);
                            ctrl_porcent = controlarPorcentajesIguales(this,num);
                        }

                        if(ctrl_porcent)
                        {
                            return true;
                        }
                        else
                        {
                            return false
                        }
                    }
                    return false;
                }
            });

            $('#autocomplete1, #autocomplete2').on({
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

        });

        function crearAutocomplete(arr_opciones_seleccion, campo_autocomplete, codigo_default, nombre_default)
        {
            // console.log(campo_autocomplete+'---'+codigo_default+' '+nombre_default);
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

            var limite_buscar = 1;
            if( arr_opciones_seleccion == "arr_procedimientos")
            {
                limite_buscar = 3;
            }

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
            else if($("."+campo_autocomplete).length > 0) //  || campo_autocomplete == 'concepto_autocomplete'
            {
                /*$("."+campo_autocomplete).each(function(){
                        var id_campo = $(this).attr("id");
                        console.log(id_campo);
                        $("#"+id_campo).autocomplete({
                            source: arr_datos, minLength : 0,
                            select: function( event, ui ) {
                                        // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                                        var cod_sel = ui.item.codigo;
                                        var nom_sel = ui.item.nombre;
                                        // var id_el = $(this).attr("id");
                                        $("#"+id_campo).attr("codigo",cod_sel);
                                        $("#"+id_campo).attr("nombre",nom_sel);
                                        console.log(id_campo);
                                        // cargarConceptosPorProcedimientos(cod_sel);
                                    }
                        });
                });*/
                $("."+campo_autocomplete).autocomplete({
                        source: arr_datos, minLength : 1,
                        select: function( event, ui ) {
                                    // if(campo_autocomplete != 'concepto_autocomplete')
                                    // {
                                    //     cargarSelectEspecialidades( ui.item.especialidades , 'liq_autocomplete', $(this));
                                    // }
                                    // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                                    var cod_sel = ui.item.codigo;
                                    var nom_sel = ui.item.nombre;
                                    var id_el = $(this).attr("id");
                                    $("#"+id_el).attr("codigo",cod_sel);
                                    $("#"+id_el).attr("nombre",nom_sel);

                                    if(campo_autocomplete == "clase_excluidos")
                                    {
                                        // Crear html para agregar
                                        adicionarClaseExcluida(cod_sel, $("#"+id_el).attr("windentificador"), $("#"+id_el).attr("id_manual"));
                                    }

                                    if(campo_autocomplete == "procedimiento_relacion_lista")
                                    {
                                        // Crear html para agregar
                                        adicionarProcedimientoRelacionado(cod_sel, $("#"+id_el).attr("id_manual"), $("#"+id_el).attr("id_relacion"));
                                    }
                                    // console.log(id_el);
                                    // cargarConceptosPorProcedimientos(cod_sel);
                                }
                });
            }
        }

        function adicionarClaseExcluida(cod_sel, windentificador, id_manual)
        {
            var campo = $("#hdn_campo_"+windentificador).val();
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion                     : 'load',
                    form                       : 'agregar_clase_excluida',
                    consultaAjax               : '',
                    clase_excluida             : cod_sel,
                    windentificador            : windentificador,
                    id_manual                  : id_manual,
                    arr_maestro_clases_insumos : $("#arr_maestro_clases_insumos64").val(),
                    campo                      : campo
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                       // $("#log_error").html(data.log_error);
                        alert(data.mensaje);
                    }
                    else
                    {
                        if($("#div_check_clase_"+windentificador+'_'+cod_sel).length == 0)
                        {
                            $("#div_checks_clases_"+windentificador).append(data.html);
                        }
                    }
                    return data;
                },
                "json"
            ).done(function(data){
                $("#wcod_clase_"+windentificador).attr("codigo","");
                $("#wcod_clase_"+windentificador).attr("nombre","");
                // $("#wcod_clase_"+windentificador).attr("windentificador","");
                // $("#wcod_clase_"+windentificador).attr("id_manual","");
                $("#wcod_clase_"+windentificador).val("");

                if(isset(data.error) && data.error == 0)
                {
                    validarGuardarInsumosExcluidos('', windentificador, id_manual, '');
                }
            });
        }

        function adicionarProcedimientoRelacionado(cod_sel, id_manual, id_relacion)
        {
            // var codigo_procedimiento = $("#div_lista_procedimientos_anulan_"+id_manual+"_"+id_relacion).attr("codigo");

            if($("#div_lista_procedimientos_anulan_"+id_manual+"_"+id_relacion).find("#div_procedimiento_anula_"+id_manual+"_"+id_relacion+"_"+cod_sel).length == 0)
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion               : 'load',
                        form                 : 'agregar_procedimiento_relacionado',
                        consultaAjax         : '',
                        // clase_excluida       : cod_sel,
                        id_manual            : id_manual,
                        id_relacion          : id_relacion,
                        codigo_procedimiento : cod_sel,
                        arr_procedimientos   : $("#arr_procedimientos64").val()
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                           // $("#log_error").html(data.log_error);
                            alert(data.mensaje);
                        }
                        else
                        {
                            // if($("#div_check_clase_"+windentificador+'_'+cod_sel).length == 0)
                            // {
                                $("#div_lista_procedimientos_anulan_"+id_manual+"_"+id_relacion).append(data.html);
                            // }
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                    if(isset(data.error) && data.error == 0)
                    {
                        validarGuardarProcedimientoLista('', id_manual, id_relacion, '');
                    }
                });
            }
            else
            {
                alert("Ya existe el procedimiento en la lista, verifique si esta seleccionado");
            }
            $("#txt_procedimiento_relacion_lista_"+id_manual+"_"+id_relacion).attr("codigo","");
            $("#txt_procedimiento_relacion_lista_"+id_manual+"_"+id_relacion).attr("nombre","");
            // $("#txt_procedimiento_relacion_lista_"+id_manual+"_"+id_relacion).attr("windentificador","");
            // $("#txt_procedimiento_relacion_lista_"+id_manual+"_"+id_relacion).attr("id_manual","");
            $("#txt_procedimiento_relacion_lista_"+id_manual+"_"+id_relacion).val("");

        }

        function agregarRelacionProcedimientos(elem, id_manual, wcodigo_manual)
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion             : 'insert',
                    form               : 'agregar_relacion_procedimiento',
                    consultaAjax       : '',
                    id_manual          : id_manual,
                    wcodigo_manual     : wcodigo_manual,
                    arr_procedimientos : $("#arr_procedimientos64").val()
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                       // $("#log_error").html(data.log_error);
                        alert(data.mensaje);
                    }
                    else
                    {
                        $(data.html).insertBefore('.pietabla_relaciones_'+id_manual);
                    }
                    return data;
                },
                "json"
            ).done(function(data){
                inicializarCamposProcedimientosAutocomplete(id_manual);
                inicializarOnBlurGrabar();
            });
        }

        function inicializarCamposProcedimientosAutocomplete(id_manual)
        {
            crearAutocomplete('arr_procedimientos', 'procedimiento_relacion','','');
            crearAutocomplete('arr_procedimientos', 'procedimiento_relacion_lista','','');
            $('.procedimiento_relacion, .procedimiento_relacion_lista').on({
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

        function eliminarRelacionProcedimiento(id_relacion, class_tr)
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion             : 'delete',
                    form               : 'eliminar_relacion_procedimiento',
                    consultaAjax       : '',
                    id_relacion        : id_relacion
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                       // $("#log_error").html(data.log_error);
                        alert(data.mensaje);
                    }
                    else
                    {
                        alert("[!] Relacion eliminada correctamente.");
                         $("."+class_tr).hide("slow",
                            function(){
                                $("."+class_tr).remove();
                        });

                    }
                    return data;
                },
                "json"
            ).done(function(data){
                //
            });
        }

        function cambiarMensajesDefault(elem, id_manual)
        {
            var ck = ($(elem).is(":checked")) ? 'on': 'off';
            if(ck == 'on')
            {
                $(".spn_NOFact"+id_manual).hide();
                $(".spn_SIFact"+id_manual).show();
            }
            else
            {
                $(".spn_SIFact"+id_manual).hide();
                $(".spn_NOFact"+id_manual).show();
            }
        }

        function validarParametrosCentroCosto(elem, id_manual)
        {
            var opcion = $(elem).find("option:selected");

            if($(opcion).attr("tipocco") == 'urg')
            {
                $(".otrosParametrosEncabezado_"+id_manual).find(":checkbox:checked").each(function(){
                    $(this).removeAttr("checked");
                });
                $(".otrosParametrosEncabezado_"+id_manual).hide();
            }
            else
            {
                $(".otrosParametrosEncabezado_"+id_manual).show(500);
            }
        }
    </script>

    <script type="text/javascript">

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
            if($("tr").find("[id^="+prefijo+"]").length > 0)
            {
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
            else if($("."+prefijo).length > 0)
            {
                $("."+prefijo).each(function(){
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
            var charCode = (evt.which) ? evt.which : evt.keyCode;
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
                    || charCode == 95
                    || charCode == 37
                    || charCode == 39)
            {
                return true;
            }

             return false;
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

        .caja_flotante{
            position: absolute;
            /*top:0;*/
            /*left: 10px;*/
            /*border: 1px solid #CCC;*/
            /*background-color: #F2F2F2;*/
            /*width:150px;*/
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
        .pad{
            padding:    4px;
        }

        .alinear_derecha {
            display: block;
            float:right;
            width: 70px;
            text-align: center;
            color: #FF2F00;
        }

        .div_width_max {
            width: 100%;
            /*border: red solid 1px;*/
        }

        .div_botones_opcion {
            /*width: 100%;*/
            text-align: right;
            height: 30px;
        }

        ul.botones_opcion_ul
        {
            padding-right: 0px;
            padding-left: 0px;
            float: left;
            padding-bottom: 0px;
            margin: 0px 0px;
            /*width: 15%;*/
            padding-top: 0px;
            list-style-type: none
        }

        ul.botones_opcion_ul li
        {
            text-align: center;
            padding-right: 0px;
            display: inline;
            padding-right: 0px;
            float: left;
            padding-bottom: 0px;
            width: 103px;
            padding-top: 0px
        }


        .div_param_generales  {
            /*width: 100%;*/
            text-align: center;
            background-color: #FFFEE2;
        }

        ul.param_generales_ul
        {
            padding-right: 0px;
            padding-left: 0px;
            float: center;
            padding-bottom: 0px;
            margin: 0px 0px;
            /*width: 15%;*/
            padding-top: 0px;
            list-style-type: none
        }

        ul.param_generales_ul li
        {
            text-align: center;
            padding-right: 0px;
            display: inline;
            padding-right: 0px;
            float: center;
            padding-bottom: 0px;
            width: 55px;
            padding-top: 0px
        }

        /*.param_generales_ul li input,select,button {
            font-size: 8.5pt;
        }*/

        ul.columnas_conceptos
        {
            padding-right: 0px;
            padding-left: 0px;
            float: left;
            padding-bottom: 0px;
            margin: 0px 0px;
            width: 100%;
            padding-top: 0px;
            list-style-type: none
        }

        ul.columnas_conceptos li
        {
            padding-right: 1px;
            display: inline;
            padding-left: 1px;
            float: left;
            font-size: 8.5pt;
            text-align: center;
            padding-bottom: 1px;
            /*width: 100px;*/ /*Descomentar para que haga espacio para un nuevo concepto como columna*/
            width: 95px;
            /*width: 14%;*/ /*comentar si de activa el with anterior*/
            border-left: #FFFFFF;
            padding-top: 1px
        }

        ul.columnas_conceptos_enc li
        {
            padding-right: 1px;
            display: inline;
            /*height: 45px;*/
            padding-left: 1px;
            float: left;
            font-size: 8.5pt;
            text-align: center;
            padding-bottom: 1px;
            /*width: 100px;*/ /*Descomentar para que haga espacio para un nuevo concepto como columna*/
            width: 95px;
            /*width: 14%;*/ /*comentar si de activa el with anterior*/
            border-left: #FFFFFF;
            padding-top: 1px
        }

        ul.columnas_descripcion_acto_qx
        {
            padding-right: 0px;
            padding-left: 0px;
            float: left;
            padding-bottom: 0px;
            margin: 0px 0px;
            width: 100%;
            padding-top: 0px;
            list-style-type: none
        }

        ul.columnas_descripcion_acto_qx li
        {
            padding-right: 1px;
            display: inline;
            padding-left: 1px;
            float: left;
            padding-bottom: 1px;
            width: 36%;
            padding-top: 1px
        }

        .div_actos_quirurgicos table td{
            padding-right: 0px;
            padding-left: 0px;
            padding-bottom: 0px;
            padding-top: 0px;
        }

        .td_img {
            -webkit-background-size : cover;
            -moz-background-size    : cover;
            -o-background-size      : cover;
            background-image        : url("../../images/medical/root/corchete.png");
            background-size         : 12px 120px;
            /*height                  : 0px;*/
            width                   : 90%;
            margin                  : 0 auto;
            background-repeat       : no-repeat;
            background-position     : right top;
        }

        .td_img2 {
            -webkit-background-size : cover;
            -moz-background-size    : cover;
            -o-background-size      : cover;
            background-image        : url("../../images/medical/root/corchete.png");
            background-size         : 12px 103px;
            /*height                  : 0px;*/
            width                   : 90%;
            margin                  : 0 auto;
            background-repeat       : no-repeat;
            background-position     : right top;
        }

        .inactivo_css {
            background-color: #F78977;
            cursor: pointer;
        }
        .activo_css {
            background-color: #38B888;
            cursor: pointer;
        }

        .div_cirugias_acto_qx {
            text-align: center;
        }

        .columnas_conceptos li input {
            text-align: right;
            font-size: 8.5pt;
        }

        .ancho_campos_descripcion {
            width: 129px;
        }

        button {
            /*-webkit-background-size : cover;
            -moz-background-size    : cover;
            -o-background-size      : cover;
            background-image: url("../../images/medical/root/activo.gif");
            background-size         : 12px 12px;
            margin                  : 0 auto;
            background-repeat       : no-repeat;
            background-position     : left center;*/
            padding-left: 1px;
            padding-right: 1px;
        }

        .ui-accordion .ui-accordion-content {width: 102%;}

        .estilo_tabla_limites td{
            font-size: 8.5pt;
        }
    </style>
</head>
<body>
    <div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?=$wactualiza?></div>
    <input type='hidden' name='wbasedato_HCE' id='wbasedato_HCE' value="<?=$wbasedato_HCE?>">
    <input type="hidden" name="arr_entidades" id="arr_entidades" value='<?=json_encode($arr_entidades)?>'>
    <input type="hidden" name="arr_entidades_base64" id="arr_entidades_base64" value='<?=base64_encode(serialize($arr_entidades))?>'>
    <input type="hidden" name="arr_conceptos_manuales_base64" id="arr_conceptos_manuales_base64" value='<?=base64_encode(serialize($arr_conceptos_manuales))?>'>
    <input type="hidden" name="arr_maestro_clases_insumos64" id="arr_maestro_clases_insumos64" value='<?=base64_encode(serialize($arr_maestro_clases_insumos))?>'>
    <input type="hidden" name="arr_maestro_clases_insumos" id="arr_maestro_clases_insumos" value='<?=json_encode($arr_maestro_clases_insumos)?>'>
    <input type="hidden" name="arr_procedimientos" id="arr_procedimientos" value='<?=json_encode($arr_procedimientos)?>'>
    <input type="hidden" name="arr_procedimientos64" id="arr_procedimientos64" value='<?=base64_encode(serialize($arr_procedimientos))?>'>
    <!-- //Mensaje de espera -->
    <div id='msjEspere' style='display:;'>
        <br>
        <img src='../../images/medical/ajax-loader5.gif'/>
        <br><br> Por favor espere un momento ... <br><br>
    </div>

    <div style="margin-top: 8px; margin-bottom: 8px; text-align:center;">
                    <button onclick="crearHtmlEncabezadoManual('','','crear_nuevo');" >Crear nuevo manual</button>
    </div>

    <div style="text-align:left;"><span style="background-color:#C007BC;">__</span>Cirug&iacute;a | <span style="background-color:#FEB413;">__</span>Urgencias</div>

    <div style="width:100%; display:none;" align="center" id="div_contenedor_manuales" >
        <?php
        if(mysql_num_rows($resultManuales) == 0)
        {
        ?>
        <div style="margin: 50px; background-color:#f2f2f2;padding:20px;font-weight:bold;">NO HAY MANUALES CREADOS, PARA INICIAR UNO NUEVO PRESIONE EL BOT&oacute;N "Crear nuevo manual"</div>
        <?php
        }
        else
        {
            $cont_mnl = 0;
            while ($row = mysql_fetch_array($resultManuales))
            {
                $tempArr = array();
                $cont_mnl++;
                $html_manuales_ = crearHtmlManualEncabezado($conex, $wbasedato, $wemp_pmla, $user_session, $cont_mnl, $arr_conceptos_manuales, $row['wcodigo_manual'], $row['id_manual'], $arr_entidadesIni, $arr_maestro_clases_insumos, $arr_procedimientos, $tempArr, "incluir_actos_quirurgicos");
                echo $html_manuales_;
            }
            // foreach ($html_manuales_creados as $key => $value) {
            //     echo $value;
            // }
        }
        ?>
    </div>
    <div id="log_error" style="display:none;"></div>
</body>
</html>