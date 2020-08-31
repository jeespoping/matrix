<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : plantillasMercadosCirugia.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 13 Marzo de 2015

 DESCRIPCION: Programa para crear plantillas de mercados de cirugía, estos mercados son usados solo a manera de consulta desde el monitor de cirugías
                para que los grabadores tengas una base de lo que debe llevar un mercado según un procedimiento.


 Notas:
 --
 */ $wactualiza = "(Abril 02 de 2018)"; /*
 ACTUALIZACIONES:
 *  Abril 02 de 2018 Edwar Jaramillo:
        Se modifica el include del jquery ui para los campos autocompletar, porque estaba tratando de incluir una ruta que no existía.
 *  Marzo 13 de 2015
    Edwar Jaramillo     : Fecha de la creación del programa.

**/

$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");





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
// $user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);

/*****  DICCIONARIO LOCAL *****/
// define('PROCEDIMIENTO'      ,'Procedimiento');


$caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");

/**
 * [seguimiento description: Funci? para uso solo de desarrollo, en ambiente local, crea un archivo de texto donde se imprimen variables y arrays para su seguimiento]
 * @param  [type] $seguir [Cadena de texto a guardar en el archivo, para guardar array recordar usar print_r($al_array, true), puede usar saltos de l?ea PHP as?PHP_EOL ]
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
 * [obtener_array_nombre_medicamentoPlantilla: Consulta los insumos que se permiten elegir para crear la plantilla de mercado]
 * @param  [type] $conex     [description]
 * @param  [type] $wemp_pmla [description]
 * @param  [type] $wbasedato [description]
 * @param  string $concepto  [description]
 * @return [type]            [description]
 */
function obtener_array_nombre_medicamentoPlantilla($conex, $wemp_pmla, $wbasedato, $concepto='')
{
    $arr_medicamento        = array();
    $wbasedato_movhos       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $codigo_grupo_excluidos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Grupos_medicamentos_no_incluidos'); // Solo los grupos de insumos que se pueden agregar a un mercado
    $codigo_grupo_excluidos = explode(",",$codigo_grupo_excluidos);
    if ($concepto =='')
    {
        $sql = "SELECT  Artcod as codigo,Artcom as nombre, Artgru as grupo, Artesm
                FROM    {$wbasedato_movhos}_000026
                WHERE   Artest !='off'";
    }
    else
    {
        $concepto_medicamento = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_medicamentos_mueven_inv');
        $concepto_materiales  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_materiales_mueven_inv');
        if ($concepto_medicamento == $concepto)
        {
            $sql = "SELECT  Artcod as codigo,Artcom as nombre, Artgru as grupo
                    FROM    {$wbasedato_movhos}_000026
                    WHERE   Artest !='off'
                            AND Artesm ='on'";
        }
        else if ($concepto_materiales == $concepto)
        {
                $sql = "SELECT  Artcod as codigo,Artcom as nombre, Artgru as grupo
                        FROM    {$wbasedato_movhos}_000026
                        WHERE   Artest !='off'
                                AND Artesm !='on'";
        }
    }
    $result = mysql_query($sql ,$conex) or die("Error en el query: ".$sql."<br>Tipo Error:".mysql_error());

    while($row_medicamento = mysql_fetch_array($result))
    {
        $cod_gru = explode("-" ,$row_medicamento['grupo']);
        $cod_gru = trim($cod_gru[0]);
        if(in_array($cod_gru ,$codigo_grupo_excluidos ))
        {
            $nombre = trim($row_medicamento['nombre']);
            $nombre   = utf8_encode(str_replace('"','', $nombre));
            $nombre   = str_replace("'","-",$nombre);

            $codigo = trim($row_medicamento['codigo']);
            $tipoInsumo = ($row_medicamento['Artesm']== 'on' ) ? "medicamento": "material"; // Esto permitirá que en la vista se pueden identificar qué códigos perteneces a materiales o medicamentos, ayuda visual.
            $arr_medicamento[trim($row_medicamento['codigo'])] = array("nombre"=>$nombre, "codigo"=>$codigo, "tipoInsumo"=>$tipoInsumo);
        }
    }
    return $arr_medicamento;
}

/**
 * [insertarInsumosMercado: Encargada de recibir un array de insumos y generar la secuencia de inserción en bloque]
 * @param  [type] $conex         [description]
 * @param  [type] $wemp_pmla     [description]
 * @param  [type] $wbasedato     [description]
 * @param  [type] $data          [Array de respuesta a la interfaz html]
 * @param  [type] $arr_mercado   [Array de insumos que se van a insertar en el detalle de la plantilla]
 * @param  [type] $fecha_actual  [Fecha actual del sistema]
 * @param  [type] $hora_actual   [Hora actual del sistema]
 * @param  [type] $user_session  [Código del usuario que genera la inserción]
 * @param  [type] $id_encabezado [Id o código del encabezado de la plantilla a quien se va a asociar los insumos de la plantilla]
 * @return [type]                [description]
 */
function insertarInsumosMercado($conex, $wemp_pmla, $wbasedato, $data, $arr_mercado, $fecha_actual, $hora_actual, $user_session, $id_encabezado)
{
    $sql = "INSERT INTO {$wbasedato}_000191
                    (Medico, Fecha_data, Hora_data,
                    Dincop, Dincom, Dincan, Dinest, Seguridad)
            VALUES  ";
    $sql_values = array();
    if(is_array($arr_mercado) && count($arr_mercado) > 0 )
    {
        foreach ($arr_mercado as $key_index => $arr_insumo)
        {
            $sql_values[] = "
                        ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}',
                        '{$id_encabezado}', '{$arr_insumo['codigo']}', '{$arr_insumo['cantidad']}', 'on', 'C-{$user_session}')";
        }
    }

    if(count($sql_values) > 0)
    {
        $sql .= implode(",", $sql_values);
        if($result = mysql_query($sql, $conex))
        {
            //DETALLE INSERTADO
            $data["mensaje"] = "Plantilla de mercado guardada";
            $data["sql"] .= $sql;
        }
        else
        {
            $data["error"]   = 1;
            $data["mensaje"] = "Ocurrió un error al intentar insertar el encabezado de la plantilla";
            $data["sql"]     .= "ERROR EN QUERY MATRIX - UPDATE Dincop = {$id_encabezado} (000191): ".mysql_error()." SQL: ".$sql;
        }
    }
    else
    {
        $data["error"]   = 1;
        $data["mensaje"] = "No hay insumos para agregar a la plantilla";
        $data["sql"]     .= "ERROR EN QUERY MATRIX - 000191): ".mysql_error()." SQL: ".$sql;
    }
    return $data;
}

/**
 * [listaPlantillas: Genera únicamente un array con las plantillas activas que se pueden mostrar en la interfaz del usuario]
 * @param  [type] $conex              [description]
 * @param  [type] $wemp_pmla          [description]
 * @param  [type] $wbasedato          [description]
 * @param  [type] $arr_procedimientos [Array maestro de procedimientos (es igual al que se muestra en el autocompletar de procedimientos)]
 * @param  string $id_plantilla       [ID opcional de la plantilla, si es vacía entonces se crea el array completo con todas las plantillas sino entonces solo consulta la información de una sola plantilla]
 * @return [type]                     [description]
 */
function listaPlantillas($conex, $wemp_pmla, $wbasedato, $arr_procedimientos, $id_plantilla='')
{
    $arr_listaPlantillas = array();
    $filtro = "";
    $and    = "";
	
	$arr_procedimientos = ((!is_array($arr_procedimientos)) ? array() : $arr_procedimientos);
	
    if($id_plantilla != '')
    {
        $filtro .= " id='{$id_plantilla}' AND ";
        $and    = "AND";
    }

    $sql = "SELECT  Eincop AS id_plantilla, Eincom AS wprocedimiento, Einent AS wentidad, Eintar AS wtarifa, Eintem AS wtipoempresa
            FROM    {$wbasedato}_000190
            WHERE   $filtro Einpaq = 'pll'";
    if($result = mysql_query($sql, $conex))
    {
        while ($row = mysql_fetch_array($result))
        {
            $id_plantilla = $row["id_plantilla"];
            $wprocedimiento = $row["wprocedimiento"];
            $nomb_procedimiento = (array_key_exists($wprocedimiento, $arr_procedimientos)) ? $arr_procedimientos[$wprocedimiento]: "--";
            if(!array_key_exists($id_plantilla, $arr_listaPlantillas))
            {
                $arr_listaPlantillas[$id_plantilla] = array();
            }
            $arr_listaPlantillas[$id_plantilla]["wprocedimiento"]     = $wprocedimiento;
            $arr_listaPlantillas[$id_plantilla]["nomb_procedimiento"] = $nomb_procedimiento;
            $arr_listaPlantillas[$id_plantilla]["wentidad"]           = $row["wentidad"];
            $arr_listaPlantillas[$id_plantilla]["wtarifa"]            = $row["wtarifa"];
            $arr_listaPlantillas[$id_plantilla]["wtipoempresa"]       = $row["wtipoempresa"];
        }
    }
    else
    {
        echo "ERROR EN QUERY MATRIX - SELECT (000190): ".mysql_error()." SQL: ".$sql;
    }
    return $arr_listaPlantillas;
}

/**
 * [listaInsumosPlantilla: Crea un array con todos los insumos ativos asociados a una plantilla]
 * @param  [type] $conex            [description]
 * @param  [type] $wemp_pmla        [description]
 * @param  [type] $wbasedato        [description]
 * @param  [type] $wbasedato_movhos [description]
 * @param  [type] $id_plantilla     [ID o código de la plantilla que requiere los insumos asociados]
 * @return [type]                   [description]
 */
function listaInsumosPlantilla($conex, $wemp_pmla, $wbasedato, $wbasedato_movhos, $id_plantilla)
{
    $arr_listaInsumosPlantilla = array();
    $sql = "SELECT  c191.Dincom AS codigo_insumo, c191.Dincan AS wcantidad, c191.Dinest AS westado,
                    m26.Artgru AS wgrupo, m26.Artesm AS tipoInsumo, m26.Artcom AS nombre_insumo
            FROM    {$wbasedato}_000191 AS c191
                    INNER JOIN
                    {$wbasedato_movhos}_000026 AS m26 ON (m26.Artcod = c191.Dincom)
            WHERE   c191.Dincop = '{$id_plantilla}'
                    AND c191.Dinest = 'on'
            ORDER BY m26.Artubi";
    if($result = mysql_query($sql, $conex))
    {
        while ($row = mysql_fetch_array($result))
        {
            $wcodigo_insumo = $row["codigo_insumo"];
            $tipoInsumo = ($row['tipoInsumo']== 'on' ) ? "medicamento": "material";
            if(!array_key_exists($wcodigo_insumo, $arr_listaInsumosPlantilla))
            {
                $arr_listaInsumosPlantilla[$wcodigo_insumo] = array();
            }
            $arr_listaInsumosPlantilla[$wcodigo_insumo]["codigo_insumo"] = $row["codigo_insumo"];
            $arr_listaInsumosPlantilla[$wcodigo_insumo]["wcantidad"]     = $row["wcantidad"];
            $arr_listaInsumosPlantilla[$wcodigo_insumo]["westado"]       = $row["westado"];
            $arr_listaInsumosPlantilla[$wcodigo_insumo]["tipoInsumo"]    = $tipoInsumo;
            $arr_listaInsumosPlantilla[$wcodigo_insumo]["nombre"]        = $row["nombre_insumo"];
            $arr_listaInsumosPlantilla[$wcodigo_insumo]["grupo"]         = $row["wgrupo"];
        }
    }
    else
    {
        echo "ERROR EN QUERY MATRIX - SELECT (000191-movhos_26): ".mysql_error()." SQL: ".$sql;
    }
    return $arr_listaInsumosPlantilla;
}

/**
 * [listaInsumosPlantillaHtml: Se encarga de generar las filas html de todos los insumos que llegan en el array arr_listaInsumosPlantilla y que serán mostrados en la interfaz de usuario.
 *                             NOTA: Cuando se crea una fila directamente desde el javascript se genera un código html igual a este, pr tanto una modidicafión en esta sección,
 *                             implica realizar el mismo cambio del lado de javascript].
 * @param  [type] $conex                     [description]
 * @param  [type] $wemp_pmla                 [description]
 * @param  [type] $wbasedato                 [description]
 * @param  [type] $id_plantilla              [ID o código de la plantilla]
 * @param  [type] $arr_listaInsumosPlantilla [Array de todos los insumos asociados a una plantilla]
 * @return [type]                            [TRs html listos para ser agregados a una tabla en la interfaz de usuario ]
 */
function listaInsumosPlantillaHtml($conex, $wemp_pmla, $wbasedato, $id_plantilla, $arr_listaInsumosPlantilla)
{
    $html_insumo = "";
    $cont_fila = 0;
    foreach ($arr_listaInsumosPlantilla as $codigo_insumo => $arr_insumo)
    {
        $css_tipoInsumo = ($arr_insumo["tipoInsumo"] == 'material') ? "filaMaterial": "filaMedicamento";
        $css_fila = ($cont_fila % 2 == 0) ? 'fila1': 'fila2';
        $nombre = utf8_encode($arr_insumo["nombre"]);
        $html_insumo .= '<tr class="'.$css_fila.' grabable" codigo="'.$codigo_insumo.'" nombre="" id="tr_insumo_'.$codigo_insumo.'" onmouseover="trOver(this);" onmouseout="trOut(this);" >
                            <td class="'.$css_tipoInsumo.'">&nbsp;&nbsp;</td>
                            <td>'.$codigo_insumo.'</td>
                            <td>'.$nombre.'</td>
                            <td><input type="text" class="solofloat CampoObligatorio" id="wcantidad_'.$codigo_insumo.'" id="wcantidad_'.$codigo_insumo.'" value="'.$arr_insumo['wcantidad'].'" onkeyup="regexnumeros(); resaltarModificado(this); validarCantidadMax(this,\'\',\'\');" size="4" style="text-align: right;" bordeobligatorio="si" /></td>
                            <td><img class="blockUpdate" src="../../images/medical/hce/cancel.PNG" title="Eliminar" style="cursor: pointer;" onclick="eliminar_insumo(\''.$codigo_insumo.'\')" ></td>
                        </tr>';
        $cont_fila++;
    }
    return $html_insumo;
}

/**
 * [listaPlantillasHtml: Crea las filas html de los encabezados de las plantillas para mostrarlos en la lista de plantillas creadas]
 * @param  [type] $conex               [description]
 * @param  [type] $wemp_pmla           [description]
 * @param  [type] $wbasedato           [description]
 * @param  [type] $arr_listaPlantillas [Array con la lista de plantillas a partir de las que se genera el html que se muestra en la interfaz]
 * @return [type]                      [description]
 */
function listaPlantillasHtml($conex, $wemp_pmla, $wbasedato, $arr_listaPlantillas)
{
    $html_reg = "";
    $cont_pll = 0;
    foreach ($arr_listaPlantillas as $id_plantilla => $arr_infoPlatilla)
    {
        $css = ($cont_pll % 2 == 0) ? 'fila1': 'fila2';
        $wprocedimiento = $arr_infoPlatilla["wprocedimiento"];
        $nomb_procedimiento = $arr_infoPlatilla["nomb_procedimiento"];
        $html_reg .= '<tr class="'.$css.' find" id="tr_plantilla_'.$id_plantilla.'" onmouseover="trOver(this);" onmouseout="trOut(this);">
                        <td>'.$wprocedimiento.'-'.$nomb_procedimiento.'</td>
                        <td style="text-align:center;"><input type="button" value="Ver" id="brn_ver" onclick="cargarPlatillaEditar(\''.$id_plantilla.'\');"></td>
                        <td style="text-align:center;"><img class="blockUpdate" src="../../images/medical/hce/cancel.PNG" title="Eliminar" style="cursor: pointer;" onclick="eliminar_plantilla(\''.$id_plantilla.'\', \''.$wprocedimiento.'\',\''.$nomb_procedimiento.'\');" ></td>
                    </tr>';
        $cont_pll++;
    }

    if($html_reg == '')
    {
        $html = '<div width="100%" style="text-align:center;font-size:14;font-weight:bold;background-color:#bfbfbf;">NO hay plantillas creadas en este momento</div>';
    }
    else
    {
        $html = '<table align="center" id="tabla_listaPlatillas">
                    <tr class="encabezadoTabla">
                        <td>Procedimiento</td>
                        <td>Ver</td>
                        <td>Eliminar</td>
                    </tr>
                    '.$html_reg.'
                </table>';

        // Si en la interfaz se van a mostrar más de 10 filas de encabezados de plantillas entonces se encapsula la lista en un div con scroll para evitar extender la página hacia abajo.
        if($cont_pll >= 10)
        {
            $html = '
                    <div align="center" style="height: 310px; overflow:auto;border: 2px solid #9f9f9f;">
                        '.$html.'
                    </div>';
        }
        else
        {
            $html = '<div align="center" style="">
                        '.$html.'
                    </div>';
        }
    }
    return $html;
}

/**
 * Lógica de los llamados AJAX del todo el programa
 */
if(isset($accion) && isset($form))
{
    include_once("ips/ValidacionGrabacionCargosERP.php");

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
                case 'grabar_actualizar_mercado':
                        $data["id_plantilla"] = $id_plantilla;
                        $arr_mercado = json_decode(stripslashes($arr_mercado), true);
                        // INSERTAR NUEVO MERCADO
                        if($id_plantilla == "")
                        {
                            $sql = "INSERT INTO {$wbasedato}_000190
                                                (Medico, Fecha_data, Hora_data,
                                                Eincop, Eincom, Einent, Eintar,
                                                Eintem, Einpaq, Einest, Seguridad)
                                    VALUES
                                                ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}',
                                                '', '{$wprocedimiento}', '*', '*',
                                                '*', 'pll', 'on', 'C-{$user_session}')";

                            if($result = mysql_query($sql, $conex))
                            {
                                $id_encabezado = mysql_insert_id();
                                $data["id_plantilla"] = $id_encabezado;
                                // Capturar el id del insert anterior para actualizarlo en la columna Eincop, se hace esto porque
                                // la tabla ya tenía este campo consecutivo generado con un autoincremento "calculado", en este caso simplemente
                                // se actualiza a partir del id origina de la tabla
                                $sql = "UPDATE  {$wbasedato}_000190
                                                SET Eincop='{$id_encabezado}'
                                        WHERE   id = '{$id_encabezado}'";

                                // Si se pudo actualizar el campo sin problemas entonces continuar insertando el detalle del mercado, sino entonces eliminar el registro antes creado
                                if($result = mysql_query($sql, $conex))
                                {
                                    insertarInsumosMercado($conex, $wemp_pmla, $wbasedato, $data, $arr_mercado, $fecha_actual, $hora_actual, $user_session, $id_encabezado);
                                }
                                else
                                {
                                    $data["error"]   = 1;
                                    $data["mensaje"] = "Ocurrió un error al intentar insertar el encabezado de la plantilla";
                                    $data["sql"]     .= "ERROR EN QUERY MATRIX - UPDATE Eincop ID={$id_encabezado} (000190): ".mysql_error()." SQL: ".$sql;
                                    $sql = "DELETE FROM {$wbasedato}_000190 WHERE id = '{$id_encabezado}'";
                                    if($result = mysql_query($sql, $conex))
                                    {
                                        $data["id_plantilla"] = "";
                                    }
                                    else
                                    {
                                        $data["error"]   = 1;
                                        $data["mensaje"] = "Ocurrió un error al intentar insertar el encabezado de la plantilla";
                                        $data["sql"]     .= "ERROR EN QUERY MATRIX - DELETE ID={$id_encabezado} (000190): ".mysql_error()." SQL: ".$sql;
                                    }
                                }
                            }
                            else
                            {
                                $data["error"]   = 1;
                                $data["mensaje"] = "Ocurrió un error al intentar insertar el encabezado de la plantilla";
                                $data["sql"]     = "ERROR EN QUERY MATRIX - INSERTAR (000190): ".mysql_error()." SQL: ".$sql;
                            }
                        }
                        else
                        {
                            //Actualizar los insumos existentes o eliminar los que hacen falta
                            $sql = "SELECT  Eincom AS wprocedimiento, Einent AS wentidad, Eintar AS wtarifa, Eintem AS wtipoempresa
                                    FROM    {$wbasedato}_000190
                                    WHERE   id = '{$id_plantilla}'";
                            if($result = mysql_query($sql, $conex))
                            {
                                // Actualizar encabezado de la plantilla
                                $row_pll = mysql_fetch_array($result);
                                if($row_pll['wprocedimiento'] != $wprocedimiento)
                                {
                                    $sql = "UPDATE  {$wbasedato}_000190
                                                    SET Eincom = '{$wprocedimiento}'
                                            WHERE   id = '{$id_plantilla}'";
                                    if($result = mysql_query($sql, $conex))
                                    {
                                        //
                                    }
                                }

                                // Actualizar detalle de la plantilla
                                // Consultar los insumos anteriores
                                $arr_insumos_plantilla = array();
                                $sql = "SELECT Dincom AS codigo_insumo, Dincan AS wcantidad, Dinest AS westado
                                        FROM    {$wbasedato}_000191
                                        WHERE   Dincop = '{$id_plantilla}'";
                                if($result = mysql_query($sql, $conex))
                                {
                                    while ($row_dll = mysql_fetch_array($result))
                                    {
                                        if(!array_key_exists($row_dll['codigo_insumo'], $arr_insumos_plantilla))
                                        {
                                            $arr_insumos_plantilla[$row_dll['codigo_insumo']] = array();
                                        }
                                        $arr_insumos_plantilla[$row_dll['codigo_insumo']] = $row_dll;
                                    }
                                }

                                if(is_array($arr_mercado) && count($arr_mercado) > 0 )
                                {
                                    $arr_insert = array();
                                    foreach ($arr_mercado as $key_index => $arr_insumo)
                                    {
                                        $codigo_insumo = $arr_insumo['codigo'];
                                        if(array_key_exists($codigo_insumo, $arr_insumos_plantilla))
                                        {
                                            if($arr_insumo['cantidad'] != $arr_insumos_plantilla[$codigo_insumo]['wcantidad'])
                                            {
                                                $sql = "UPDATE  {$wbasedato}_000191
                                                                SET Dincan = '{$arr_insumo['cantidad']}',
                                                                    Dinest = 'on'
                                                        WHERE   Dincop = '{$id_plantilla}'
                                                                AND Dincom = '{$codigo_insumo}'";
                                                if($result = mysql_query($sql, $conex))
                                                {
                                                    $data["sql"] .= $sql;
                                                }
                                                else
                                                {
                                                    $data["sql"] .= "ERROR EN QUERY MATRIX - UPDATE plantilla [{$id_plantilla}] insumo[$codigo_insumo] (000191): ".mysql_error()." SQL: ".$sql;
                                                }
                                            }
                                            // Eliminarlo de la lista, los que queden deben ser eliminados
                                            unset($arr_insumos_plantilla[$codigo_insumo]);
                                        }
                                        elseif(!array_key_exists($codigo_insumo, $arr_insumos_plantilla))
                                        {
                                            // Se crea un array con todos los insumos que fueron adicionados y que no existían en la base de datos
                                            // Luego se insertarán en bloque.
                                            $arr_insert[] = $arr_insumo;
                                        }
                                    }

                                    // Verifica si hay insumos por insertar
                                    if(count($arr_insert) > 0)
                                    {
                                        insertarInsumosMercado($conex, $wemp_pmla, $wbasedato, $data, $arr_insert, $fecha_actual, $hora_actual, $user_session, $id_plantilla);
                                    }

                                    // Validar si hay insumos por eliminar del mercado, son los que hayan quedado en el array inicial "arr_insumos_plantilla"
                                    if(count($arr_insumos_plantilla) > 0)
                                    {
                                        $codigos_insumos_delete = implode("','", array_keys($arr_insumos_plantilla));
                                        $sql = "DELETE FROM {$wbasedato}_000191 WHERE Dincop = '{$id_plantilla}' AND Dincom IN ('{$codigos_insumos_delete}')";
                                        if($result = mysql_query($sql, $conex))
                                        {
                                            //
                                        }
                                        else
                                        {
                                            $data["error"]   = 1;
                                            $data["mensaje"] = "Ocurrió un error al intentar eliminar algunos insumos que ya no deberían estar en la plantilla";
                                            $data["sql"]     = "ERROR EN QUERY MATRIX - DELETE (000191): ".mysql_error()." SQL: ".$sql;
                                        }
                                    }
                                }
                                else
                                {
                                    $data["error"]   = 1;
                                    $data["mensaje"] = "No hay insumos para agregar a la plantilla";
                                    $data["sql"]     = "Array=>: ".print_r($arr_mercado,true);
                                }
                            }
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
                case 'cargar_plantilla_edit':
                        $arr_procedimientos = $arr_mercado = json_decode(stripslashes($arr_procedimientos), true);
                        // print_r($arr_procedimientos);
                        $arr_encabezado_plantilla = listaPlantillas($conex, $wemp_pmla, $wbasedato, $arr_procedimientos, $id_plantilla);

                        $data["wprocedimiento"]        = $arr_encabezado_plantilla[$id_plantilla]["wprocedimiento"];
                        $data["nomb_procedimiento"]    = $arr_encabezado_plantilla[$id_plantilla]["nomb_procedimiento"];

                        $arr_insumos_plantilla = listaInsumosPlantilla($conex, $wemp_pmla, $wbasedato, $wbasedato_movhos, $id_plantilla);
                        $html_insumos = listaInsumosPlantillaHtml($conex, $wemp_pmla, $wbasedato, $id_plantilla, $arr_insumos_plantilla);
                        $data["listaInsumosPlantilla"] = $html_insumos;
                    break;

                case 'actualizar_lista_plantillas':
                        $arr_procedimientos = $arr_mercado = json_decode(stripslashes($arr_procedimientos), true);
                        $arr_listaPlantillas = listaPlantillas($conex, $wemp_pmla, $wbasedato, $arr_procedimientos);
                        $data['html'] = listaPlantillasHtml($conex, $wemp_pmla, $wbasedato, $arr_listaPlantillas);
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
                case 'eliminar_plantilla':
                        $sql = "DELETE FROM {$wbasedato}_000190 WHERE id = '{$id_plantilla}'";
                        if($result = mysql_query($sql, $conex))
                        {
                            $sql = "DELETE FROM {$wbasedato}_000191 WHERE Dincop = '{$id_plantilla}'";
                            $data["sql"] = $sql;
                            if($result = mysql_query($sql, $conex))
                            {
                                //
                            }
                            else
                            {
                                $data["error"]   = 1;
                                $data["mensaje"] = "Ocurrió un error al intentar eliminar todos los insumos de la plantilla (El encabezado si fue eliminado)";
                                $data["sql"]     .= "ERROR EN QUERY MATRIX - DELETE (000190): ".mysql_error()." SQL: ".$sql;
                            }
                        }
                        else
                        {
                            $data["error"]   = 1;
                            $data["mensaje"] = "Ocurrió un error al intentar eliminar el encabezado de la plantilla";
                            $data["sql"]     = "ERROR EN QUERY MATRIX - DELETE (000190): ".mysql_error()." SQL: ".$sql;
                        }
                    break;

                /*case 'CODIGO_EJEMPLO':
                        $query = "  UPDATE  ".$wbasedato."_".OBSERVACIONES_ORDEN."
                                            SET Segest = 'off'
                                    WHERE   id = '".$id_observ."'";
                        if($result = mysql_query($query, $conex))
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
// $wbasedato_HCE    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbasedato        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

$class_bloquear = "";
// Esta variable debe llegar desde el monitor directamente, es para indicar que, cuando se abre el programa de plantillas desde el monitor
// solo se debe permitir ver y consultar las plantillas sin dejar eliminar plantillas, insumos y modificar la lista de mercado.
if(isset($consultaview) && $consultaview == 'on')
{
    $class_bloquear = "blockUpdate";
}

/***** Procedimientos *****/
$arr_procedimientos = obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato);
if(array_key_exists("*", $arr_procedimientos)) { unset($arr_procedimientos["*"]); }

/***** Insumos *****/
$arr_insumos = obtener_array_nombre_medicamentoPlantilla($conex, $wemp_pmla, $wbasedato);
if(array_key_exists("*", $arr_insumos))
{
    unset($arr_insumos['*']);
}

/***** Lista de plantillas guardadas *****/
$arr_listaPlantillas = listaPlantillas($conex, $wemp_pmla, $wbasedato, $arr_procedimientos);

?>
<html lang="es-ES">
<head>
    <title>Platillas de mercados</title>
    <meta charset="utf-8">

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librer? para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
    <script src="../../../include/root/toJson.js" type="text/javascript"></script>
    <script src="../../../include/ips/funcionInsumosqxERP.js" type="text/javascript"></script>


    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;

        $(function(){
            // $("#div_datos_basicos").attr("acordeon", "");
            // $("#div_datos_basicos").accordion({
            //      collapsible: true
            //     ,heightStyle: "content"
            //     //,active: -1
            // });

            // $('.numerico').on({
            //     keypress: function(e) {
            //         var r = soloNumeros(e);
            //         if(r==true)
            //         {
            //             var codeentr = (e.which) ? e.which : e.keyCode; /*if(codeentr == 13) { buscarDatosBasicos(); }*/
            //             return true;
            //         }
            //         return false;
            //     }
            // });
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
        });

        $(document).ready( function ()
        {
            // reiniciarTooltip();

            // actualizarSearch();
            iniciarAutompletarProcedimiento("procedimiento_edit", "", "");
            iniciarAutompletarInsumos("add_insumo", "", "");
            limpiarFormEdit();
            actualizarSearch();
            inactivarOpciones();
        });

        function actualizarSearch()
        {
            $('input#id_search_platilla').quicksearch('#tabla_listaPlatillas .find');
        }

        function iniciarAutompletarProcedimiento(accion, init_codigo, init_nombre)
        {
            crearAutocomplete(accion, 'arr_procedimientos', 'procedimiento_edit','','',2);
        }

        function iniciarAutompletarInsumos(accion, init_codigo, init_nombre)
        {
            crearAutocomplete(accion, 'arr_insumos', 'add_insumo','','',2);
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
            obJson['wbasedato']        = $("#wbasedato").val();
            obJson['wbasedato_movhos'] = $("#wbasedato_movhos").val();
            // obJson['wbasedato_tcx']    = $("#wbasedato_tcx").val();
            obJson['consultaAjax']     = '';
            return obJson;
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
         * @return {[type]}                        [description]
         */
        function crearAutocomplete(accion, arr_opciones_seleccion, campo_autocomplete, codigo_default, nombre_default, limite_buscar)
        {
            $("#"+campo_autocomplete).val(nombre_default);
            $("#"+campo_autocomplete).attr("codigo",codigo_default);
            $("#"+campo_autocomplete).attr("nombre",nombre_default);

            arr_datos = new Array();
            //var datos = arr_wempresp;//eval( $("#arr_wempresp").val() );
            var datos = eval('(' + $("#"+arr_opciones_seleccion).val() + ')');
            var index = -1;
            // if(accion == 'add_insumo') { console.log(datos); }
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
                else if(accion == "add_insumo")
                {
                    arr_datos[index]            = {};
                    arr_datos[index].value      = CodVal+'-'+datos[CodVal]['nombre'];
                    arr_datos[index].label      = CodVal+'-'+datos[CodVal]['nombre'];
                    arr_datos[index].codigo     = CodVal;
                    arr_datos[index].nombre     = CodVal+'-'+datos[CodVal]['nombre'];
                    arr_datos[index].tipoInsumo = datos[CodVal]['tipoInsumo'];
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

            // console.log(arr_datos);
            if($("#"+campo_autocomplete).length > 0)
            {
                $("#"+campo_autocomplete).autocomplete({
                        source: arr_datos, minLength : limite_buscar,
                        select: function( event, ui ) {
                                    // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                                    var cod_sel    = ui.item.codigo;
                                    var nom_sel    = ui.item.nombre;
                                    var tipoInsumo = ui.item.tipoInsumo;
                                    $("#"+campo_autocomplete).attr("codigo",cod_sel);
                                    $("#"+campo_autocomplete).attr("nombre",nom_sel);

                                    if(accion == 'add_insumo')
                                    {
                                        agregarHtmlInsumo(cod_sel, nom_sel, tipoInsumo, campo_autocomplete);
                                        return false;
                                    }
                        }
                });
            }
            /*else if($("."+campo_autocomplete).length > 0 )
            {
                $("."+campo_autocomplete).autocomplete({
                        source: arr_datos, minLength : limite_buscar,
                        select: function( event, ui ) {
                                    var cod_sel = ui.item.codigo;
                                    var nom_sel = ui.item.nombre;
                                    var id_el = $(this).attr("id");
                                    $("#"+id_el).attr("codigo",cod_sel);
                                    $("#"+id_el).attr("nombre",nom_sel);
                                }
                });
            }*/
        }

        /**
         * [agregarHtmlInsumo: Esta función esta extendidad tambien en la sección PHP, un cambio aquí implica un cambio en la función PHP, se hace de esta manera para evitar un llamado adicional hasta el php]
         * @param  {[type]} cod_sel            [Código del nuevo insumo para la plantilla]
         * @param  {[type]} nom_sel            [Nombre del nuevo insumo]
         * @param  {[type]} tipoInsumo         [Indica si el insumo se marca como material o medicamento]
         * @param  {[type]} campo_autocomplete [Campo autocomplete que origina el nuevo insumo para la lista y que debe ser reiniciado.]
         * @return {[type]}                    [description]
         */
        function agregarHtmlInsumo(cod_sel, nom_sel, tipoInsumo, campo_autocomplete)
        {
            var htmlinsumo = "";
            var existe = $("#tr_insumo_"+cod_sel).length;
            if(existe == 0)
            {
                var css_tipoInsumo = (tipoInsumo=='material') ? "filaMaterial": "filaMedicamento";
                htmlinsumo = '  <tr class="fila2 grabable" codigo="'+cod_sel+'" nombre="" id="tr_insumo_'+cod_sel+'" onmouseover="trOver(this);" onmouseout="trOut(this);" >'
                                    +'<td class="'+css_tipoInsumo+'">&nbsp;&nbsp;</td>'
                                    +'<td>'+cod_sel+'</td>'
                                    +'<td>'+nom_sel+'</td>'
                                    +'<td><input type="text" class="solofloat CampoObligatorio" id="wcantidad_'+cod_sel+'" id="wcantidad_'+cod_sel+'" value="1" onkeyup="regexnumeros(); resaltarModificado(this); validarCantidadMax(this,\'\',\'\');" size="4" style="text-align: right;" bordeobligatorio="si" /></td>'
                                    +'<td><img src="../../images/medical/hce/cancel.PNG" title="Eliminar" style="cursor: pointer;" onclick="eliminar_insumo(\''+cod_sel+'\')" ></td>'
                                +'</tr>';
                $("#tabla_insumos_edit tr:first").after(htmlinsumo);
                resetStylePrefijo("tr_insumo_");
            }
            else
            {
                alert("Ya existe este insumo en la lista");
            }

            $("#"+campo_autocomplete).val("");
            $("#"+campo_autocomplete).attr("codigo","");
            $("#"+campo_autocomplete).attr("nombre","");
        }

        /**
         * [eliminar_insumo: Elimina un insumo de la lista, la función de guardar tendrá en cuenta solo los insumos que aparecen en pantalla]
         * @param  {[type]} codigo_insumo [description]
         * @return {[type]}               [description]
         */
        function eliminar_insumo(codigo_insumo)
        {
            $("#tr_insumo_"+codigo_insumo).remove();
            resetStylePrefijo("tr_insumo_");
        }

        /**
         * [guardarMercadoCirugia: Valída los campos del encabezado de la plantilla, genera un arreglo json con todos los insumos que aparecen en pantalla, envía a guardar]
         * @return {[type]} [description]
         */
        function guardarMercadoCirugia()
        {
            $(".requerido").removeClass("resaltarCantidadIngresada");
            $(".CampoObligatorio").removeClass("resaltarCantidadIngresada");
            var validacion = true;
            $('[bordeObligatorio=si]').css("border","").removeAttr('bordeObligatorio');

            $("#tabla_insumos_edit").find(".CampoObligatorio").each(function(){
                if($(this).val().length==0)
                {
                    validacion=false;
                    CampoObligatorio($(this).attr('id'));
                }
            });

            if(!validacion)
            {
                mensaje = "No se puede grabar, faltan campos por llenar";
                mostrar_mensajeAccion(mensaje,"div_mensaje_accion","red");
                return;
            }

            var arr_insumos = new Array();
            var wprocedimiento = $("#procedimiento_edit").attr("codigo");
            var id_plantilla  = $("#id_plantilla").val();

            ($("#procedimiento_edit").attr("codigo") == '') ? $("#procedimiento_edit").addClass("resaltarCantidadIngresada"):'';

            if(wprocedimiento!='')
            {
                if($(".grabable").length > 0)
                {
                    $(".grabable").each(function(){
                        var trFila = $(this);
                        var obj_insumo = new Object();
                        codigo = $(trFila).attr('codigo');
                        var cantidad = $("#wcantidad_"+codigo).val();
                        obj_insumo.codigo = codigo;
                        obj_insumo.cantidad = cantidad;
                        // obj_insumo.lotes = new Array();
                        // var lotes        = {};
                        // lotes.devolucion = cantDevuelto;
                        // obj_insumo.lotes.push(lotes);
                        arr_insumos.push( obj_insumo );
                    });
                    var arrJson = $.toJSON(arr_insumos);

                    var obJson               = parametrosComunes();
                    obJson['accion']         = 'update';
                    obJson['form']           = 'grabar_actualizar_mercado';
                    obJson['arr_mercado']    = arrJson;
                    obJson['wprocedimiento'] = wprocedimiento;
                    obJson['id_plantilla']   = id_plantilla;

                    $.post("plantillasMercadosCirugia.php",
                        obJson,
                        function(data){
                            if(data.id_plantilla != '') { $("#id_plantilla").val(data.id_plantilla); }

                            if(data.error == 1)
                            {
                                alert(data.mensaje);
                            }
                            else
                            {
                                mostrar_mensajeAccion("Mercado grabado con &eacute;xito","div_mensaje_accion",'');
                                actualizarListaPlantillas();
                            }
                        },
                        "json"
                    ).done(function(){
                        actualizarSearch();
                        $(".requerido").removeClass("resaltarCantidadIngresada");
                        $(".CampoObligatorio").removeClass("resaltarCantidadIngresada");
                    });
                }
                else
                {
                    mensaje = "Debe ingresar insumos para crear el mercado";
                    mostrar_mensajeAccion(mensaje,"div_mensaje_accion","red");
                }
            }
            else
            {
                mensaje = "Ingrese un procedimiento";
                mostrar_mensajeAccion(mensaje,"div_mensaje_accion","red");
            }
        }

        /**
         * [actualizarListaPlantillas: luego de crear una nueva plantilla o actualizar alguna, recarga la lista de plantillas creadas para que en la interfaz se puedan ver los cambios]
         * @return {[type]} [description]
         */
        function actualizarListaPlantillas()
        {
            var obJson                   = parametrosComunes();
            obJson['accion']             = 'load';
            obJson['form']               = 'actualizar_lista_plantillas';
            obJson['arr_procedimientos'] = $("#arr_procedimientos").val();

            $.post("plantillasMercadosCirugia.php",
                obJson,
                function(data){
                    if(data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#td_viewListaPlantillas").html(data.html);
                    }
                },
                "json"
            ).done(function(){
                actualizarSearch();
                inactivarOpciones();
            });
        }

        function inactivarOpciones()
        {
            var class_bloquear = $("#class_bloquear").val();
            if(class_bloquear == "blockUpdate")
            {
                $(".blockUpdate").hide();
            }
        }

        /**
         * [eliminar_plantilla: Desencadena el proceso para eliminar una plantilla completamente (encabezado y detalle)]
         * @param  {[type]} id_plantilla       [description]
         * @param  {[type]} wprocedimiento     [description]
         * @param  {[type]} nomb_procedimiento [description]
         * @return {[type]}                    [description]
         */
        function eliminar_plantilla(id_plantilla, wprocedimiento, nomb_procedimiento)
        {
            var id_platilla_edit = $("#id_plantilla").val();
            var msj_del = "Va a eliminar la plantilla de mercado para el procedimiento:\n["+wprocedimiento+"-"+nomb_procedimiento+"]\n\nDesea continuar?";
            if(confirm(msj_del))
            {
                var obJson               = parametrosComunes();
                obJson['accion']         = 'delete';
                obJson['form']           = 'eliminar_plantilla';
                obJson['wprocedimiento'] = wprocedimiento;
                obJson['id_plantilla']   = id_plantilla;

                $.post("plantillasMercadosCirugia.php",
                    obJson,
                    function(data){
                        if(data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            alert("La plantilla se ha eliminado");
                            // mostrar_mensajeAccion("Mercado grabado con éxito","div_mensaje_accion",'');
                        }
                    },
                    "json"
                ).done(function(){
                    actualizarSearch();
                    $("#tr_plantilla_"+id_plantilla).remove();
                    resetStylePrefijo('tr_plantilla_');
                    if(id_platilla_edit == id_plantilla)
                    {
                        $("#id_plantilla").val("");
                    }
                });
            }
        }

        /**
         * [cargarPlatillaEditar: Carga en el formulario de edición el encabezado de la plantillas y todos sus insumos asiciados]
         * @param  {[type]} id_plantilla [description]
         * @return {[type]}              [description]
         */
        function cargarPlatillaEditar(id_plantilla)
        {
            if(limpiarFormEdit())
            {
                var obJson                   = parametrosComunes();
                obJson['accion']             = 'load';
                obJson['form']               = 'cargar_plantilla_edit';
                obJson['id_plantilla']       = id_plantilla;
                obJson['arr_procedimientos'] = $("#arr_procedimientos").val();

                $.post("plantillasMercadosCirugia.php",
                    obJson,
                    function(data){
                        if(data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {

                            $("#procedimiento_edit").attr("codigo",data.wprocedimiento);
                            $("#procedimiento_edit").attr("nombre",data.nomb_procedimiento);
                            $("#procedimiento_edit").val(data.wprocedimiento+'-'+data.nomb_procedimiento);
                            $("#id_plantilla").val(id_plantilla);
                            $("#tabla_insumos_edit tr:first").after(data.listaInsumosPlantilla);
                        }
                    },
                    "json"
                ).done(function(){
                    inactivarOpciones();
                });
            }
            else
            {
                // Posiblemente para indicar que hay algo sin guardar
            }
        }

        /**
         * [limpiarFormEdit: Limpia el formulario de edición de plantillas para empezar a crear una nueva.]
         * @return {[type]} [description]
         */
        function limpiarFormEdit()
        {
            $("#id_plantilla").val("");
            $("#procedimiento_edit").val("");
            $("#procedimiento_edit").attr("codigo","");
            $("#procedimiento_edit").attr("nombre","");

            $(".grabable").remove();

            return true;
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


        function simularPlaceHolder()
        {
            // P?ina con etiquetas de html5 de las que se podr? verificar su compatibilidad
            // https://github.com/Modernizr/Modernizr/wiki/HTML5-Cross-browser-Polyfills
            // http://geeks.ms/blogs/gperez/archive/2012/01/10/modernizr-ejemplo-pr-225-ctico-1-utilizando-placeholder.aspx
            // http://www.hagenburger.net/BLOG/HTML5-Input-Placeholder-Fix-With-jQuery.html
            if(!Modernizr.input.placeholder)
            {
                console.log("NAVEGADOR NO COMPATIBLE CON placeholder de HTML5, Se sim?la atributo placeholder.");
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
         * Para aceptar caracteres num?icos, letras y algunos otros caracteres permitidos
         *
         * @return unknown
         */
        function soloCaracteresPermitidos(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            // alert(charCode);
            /*
                (charCode >= 48 && charCode <= 57) || (charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122 ) // N?meros, letras minusculas y mayusculas
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

        .ui-autocomplete{
            max-width:  230px;
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
            font-size:  9pt;
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

        .filaMaterial{
            background-color: yellow;
        }

        .filaMedicamento{
            background-color: lightgreen;
        }

    </style>
</head>
<body>
<?php
    encabezado("<div class='titulopagina2'>Platillas de mercados</div>", $wactualiza, "clinica");
?>
<input type='hidden' name='wbasedato' id='wbasedato' value="<?=$wbasedato?>">
<input type='hidden' name='wbasedato_movhos' id='wbasedato_movhos' value="<?=$wbasedato_movhos?>">
<input type='hidden' name='wemp_pmla' id='wemp_pmla' value="<?=$wemp_pmla?>">
<input type="hidden" name="arr_procedimientos" id="arr_procedimientos" value='<?=json_encode($arr_procedimientos)?>'>
<input type="hidden" name="arr_insumos" id="arr_insumos" value='<?=json_encode($arr_insumos)?>'>
<input type="hidden" name="class_bloquear" id="class_bloquear" value='<?=$class_bloquear?>'>

<table align="center" style="width:95%;">
    <tr>
        <td style="text-align:left;">
            <div id="contenedor_programa_plantillas" align="left" class="" >
                <table align="center">
                    <tr>
                        <td>
                            <table style="width:100%">
                                <tr class="encabezadoTabla">
                                    <td>Filtro plantillas mercados: <input id="id_search_platilla" type="text" value="" name="id_search_platilla"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td id="td_viewListaPlantillas">
                            <?=listaPlantillasHtml($conex, $wemp_pmla, $wbasedato, $arr_listaPlantillas)?>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;" class="encabezadoTabla">
                            <input type="button" class="blockUpdate" value="Limpiar formulario" id="btn_crearplantilla" name="btn_crearplantilla" onclick="limpiarFormEdit();" >
                        </td>
                    </tr>
                </table>
            </div>
            <br>
            <div id="div_form_edit_plantilla" class="">
                <table align="center">
                    <tr class="fondoAmarillo" >
                        <td>
                            <table style="width:100%">
                                <tr>
                                    <td colspan="2" class="encabezadoTabla" style="text-align:center;">Formulario Crear/Editar plantilla</td>
                                </tr>
                                <tr>
                                    <td class="encabezadoTabla">
                                        <input type="hidden" id="id_plantilla" name="id_plantilla" value="">
                                        Procedimiento:
                                    </td>
                                    <td class="fila2">
                                        <input type="text" class="campo_autocomplete requerido" id="procedimiento_edit" name="procedimiento_edit" codigo="" nombre="" size="30">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="fondoAmarillo" >
                        <td>
                            <table style="width:100%">
                                <tr class="blockUpdate">
                                    <td class="encabezadoTabla">Agregar insumo:</td>
                                    <td class="fila2">
                                        <input type="text" class="campo_autocomplete" id="add_insumo" name="add_insumo" value="" codigo="" nombre="" size="30">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding:0px;">
                                        <span class="filaMedicamento" style="font-weight:bold;font-size:8pt;">Medicamento</span>&nbsp;<span class="filaMaterial" style="font-weight:bold;font-size:8pt;">Material/Otro</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table style="width:100%" id="tabla_insumos_edit">
                                <tr class="encabezadoTabla">
                                    <td>&nbsp;</td>
                                    <td>C&oacute;digo</td>
                                    <td>Nombre insumo</td>
                                    <td>Cantidad</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">&nbsp;<span id="div_mensaje_accion" class="fondoAmarillo" style="display:none;border: 1px solid #2A5DB0;padding: 5px;"></span></td>
                    </tr>
                    <tr>
                        <td style="text-align:center;" class="encabezadoTabla">
                            <input type="button" class="blockUpdate" id="guardar_edit" name="guardar_edit" value="Guardar cambios" onclick="guardarMercadoCirugia();" >
                        </td>
                    </tr>
                </table>
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
</body>
</html>