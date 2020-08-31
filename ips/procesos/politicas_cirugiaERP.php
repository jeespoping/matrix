<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : politicas_cirugiaERP.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 15 Noviembre de 2013

 DESCRIPCION:


 Notas:
 El valor del parámetro $ccotema es determinado en el programa gestor_aplicaciones.php
 */ $wactualiza = "(Abril 28 2020)"; /*
 ACTUALIZACIONES:
	-	2020-04-28: Jerson Trujillo: En el tipo de cobro por tiempo de uso en rangos, se agrego un select
		para escoger si el rango aplica al tiempo de cada procedimiento o al tiempo general de la cx.
 - Junio 01 2015 Edwar Jaramillo:
    * Se crea función "codigoPoliticaComoClave" modifica un código de plantilla para que pueda ser usado como clave dentro de un array cambiando el caracter "-" por "_"
        ya que en algunos casos deja de funcionar correctamente el key del array si tiene ese caracter.
    * En la función "guardarConfiguracionConceptos" se hacen más globales los arrays arr_cobro_anestesia y arr_cobro_uso, para que queden ántes del ciclo que recorre
        los conceptos principales de la plantilla, en cada ciclo se elimina del array los rangos que aún forman parte de la configuración y lo que realmente sobre o quede
        en esos arrays es porque ya deben dejar de ser parte de la plantilla y se deben inactivar, antes estaba mal el procesos y se consultaban los rangos cada que se hacía
        un nuevo ciclo y se inactivaban todos los demás rangos diferentes al concepto actual del ciclo porque no los estaba teniendo en cuenta. De igual manera estaba sucediendo
        con los rangos de uso.
    * Las variables tipo DEFINE para los nombres de algunas tablas son reemplazadas por los nombres específicos de las tablas para mayor claridad.

 - Marzo 31 2015 Edwar Jaramillo:
    * En los rangos de anestesia o tiempo se muestran los códigos de los procedimientos y conceptos para mayor claridad.

 - Marzo 11 2015 Edwar Jaramillo:
    * Se coloca un filtro para buscar plantillas creadas, la lista se organiza para que no se despliegue por completo sino que
        se permita navegar mediante scroll, otras modificaciones de estilos.

 -  Octubre 28 de 2014 Edwar Jaramillo:
    * Corrección al momento de generar automáticamente el código consecutivo para una nueva plantilla, solo estaba llegando hasta consecutivo 10,
        porque al momento de ordenar estaba encontrando el número 9 como mayor y el número 10 estaba ordenado junto con el número 1.
    * Se inicializa el campo de especialidad, cuando se cargaba el programa no se estaba inicializando este campo con la opción "*-TODOS"

 -  Noviembre 15 de 2013 Edwar Jaramillo: Fecha de la creación del programa.
**/
global $ccotema;
global $wbasedato_HCE, $bordemenu, $caracter_ok, $caracter_ma;






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
define('TARIFA'             ,'Tar&iacute;fa');
define('ENTIDAD'            ,'Entidad');
define('TIPO_EMPRESA'       ,'Tipo empresa');
define('BASE_LIQUIDACION'   ,'Base liquidaci&oacute;n');
define('NOMBRE_PLANTILLA'   ,'Nombre plantilla');

define('ANESTESIA'          ,'Anestesia');
define('TIEMPO_INICIAL_MTS' ,'Tiempo inicial (Minutos)');
define('TIEMPO_FINAL_MTS'   ,'Tiempo final (Minutos)');
define('CODIGO_RANGO'       ,'C&oacute;digo rango');
define('PREF_POL'           , 'PCX');
define('LB_ESPECIALIDAD'    , 'Especialidad');

/**********  TABLAS  **********/
define('TB_ENCABEZADO'      ,'000180');
define('TB_COBRO_HORA'      ,'000181');
define('TB_COBRO_ANESTESIA' ,'000182');
define('TB_COBRO_USO'       ,'000183');

define('TB_BASE_LIQUIDACION','000186');
define('TB_CONCEPTOS_POLITICA','000195');
define('TB_EXCEPCIONES_UVR' ,'000206');

define('COLOR_GRIS1' ,'f2f2f2');
define('COLOR_GRIS2' ,'dfdfdf');

$caracteres  = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");

// $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

/*
$descripcion = "tabla:'".$wbasedato."_000016'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza estudio actual'";
insertLog($conex, $wbasedato, $user_session, $accion, $form, '', $descripcion, $wuse);
*/

/**
    Reiniciar datos sesión
*/
function reiniciarDatosSesion()
{
    $arr_politica = array(); //unset($_SESSION['arr_conceptos']);
    return $arr_politica;
}

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

// Si el código se va a usar como clave en un array no debe llevar el caracter gruión '-', si tiene el guión se puede revertir.
function codigoPoliticaComoClave($arreglarComoCplave, $codigo_politica)
{
    if($arreglarComoCplave)
    {
        $codigo_politica = str_replace("-", "_", $codigo_politica);
    }
    else
    {
        $codigo_politica = str_replace("_", "-", $codigo_politica);
    }
    return $codigo_politica;
}

/**
    consultarCobroHora($conex, $wbasedato, $num_politica): Consulta y crea un array de todos los rangos de anestesia creado para una politica.

    @conex        : Link de conexión a la base de datos.
    @wbasedatp    : Prefijo de las tablas que se deben consultar.
    @num_politica : Es el código que identifica la politica para que se van a consultar sus cobros por hora.

    return array  : Rangos de anestesia de la politica
*/
function consultarCobroHora($conex, $wbasedato, $num_politica, $wconcepto)
{
    $query = "  SELECT  Cphcpp AS wconcepto_principal, Cphtmn AS wtiempo_minimo_hora, Cphcph AS wcobro_por_hora, Cphcon AS wcodigo_concepto, Cphpro AS wcodigo_procedimiento, Cphest AS estado, id
                FROM    {$wbasedato}_000181
                WHERE   Cphpol = '{$num_politica}'";
    $result = mysql_query($query, $conex);
    $arr_cobro_hora = array();
    while($row = mysql_fetch_array($result))
    {
        $clave = $row['wconcepto_principal'].'_'.$row['wtiempo_minimo_hora'].'_'.$row['wcobro_por_hora'].'_'.$row['wcodigo_concepto'].'_'.$row['wcodigo_procedimiento'];
        $arr_cobro_hora[$clave] = array("id"=>$row['id'], "estado"=>$row['estado']);
    }
    return $arr_cobro_hora;
}

/**
    consultarCobroAnestesia($conex, $wbasedato, $num_politica): Consulta y crea un array de todos los rangos de anestesia creado para una politica.

    @conex        : Link de conexión a la base de datos.
    @wbasedatp    : Prefijo de las tablas que se deben consultar.
    @num_politica : Es el código que identifica la politica para que se van a consultar sus rangos de anestesia.

    return array  : Rangos de anestesia de la politica
*/
function consultarCobroAnestesia($conex, $wbasedato, $num_politica, $wconcepto)
{
    $query = "  SELECT  Anecpp AS wconcepto_principal, Anecod AS wtipo_anestesia, Anepol, Anetin AS wtiempo_inicial, Anetfn AS wtiempo_final, Anecon AS wcodigo_concepto, Anepro AS wcodigo_procedimiento, id, Aneest AS estado
                FROM    {$wbasedato}_000182
                WHERE   Anepol = '{$num_politica}'";
    $result = mysql_query($query, $conex);
    $arr_cobro_anestesia = array();
    while($row = mysql_fetch_array($result))
    {
        $clave = $row['wconcepto_principal'].'_'.$row['wtipo_anestesia'].'_'.codigoPoliticaComoClave(true, $row['Anepol']).'_'.$row['wtiempo_inicial'].'_'.$row['wtiempo_final'].'_'.$row['wcodigo_concepto'].'_'.$row['wcodigo_procedimiento'];
        $arr_cobro_anestesia[$clave] = array("id"=>$row['id'], "estado"=>$row['estado']);
    }
    return $arr_cobro_anestesia;
}

/**
    consultarCobroUso($conex, $wbasedato, $num_politica): Consulta y crea un array de todos los rangos de uso creado para una politica.

    @conex        : Link de conexión a la base de datos.
    @wbasedatp    : Prefijo de las tablas que se deben consultar.
    @num_politica : Es el código que identifica la politica para que se van a consultar sus rangos de uso.

    return array  : Rangos de uso de la politica
*/
function consultarCobroUso($conex, $wbasedato, $num_politica, $wconcepto)
{
    $query = "  SELECT  Usocpp AS wconcepto_principal, Usotmn AS wtiempo_inicial, Usotfn AS wtiempo_final, Usocon AS wcodigo_concepto, Usopro AS wcodigo_procedimiento, Usoest AS estado, id
                FROM    {$wbasedato}_000183
                WHERE   Usopol = '{$num_politica}'";
    $result = mysql_query($query, $conex);
    $arr_cobro_uso = array();
    while($row = mysql_fetch_array($result))
    {
        $clave = $row['wconcepto_principal'].'_'.$row['wtiempo_inicial'].'_'.$row['wtiempo_final'].'_'.$row['wcodigo_concepto'].'_'.$row['wcodigo_procedimiento'];
        $arr_cobro_uso[$clave] = array("id"=>$row['id'], "estado"=>$row['estado']);
    }
    return $arr_cobro_uso;
}

/**
    consultarLimitesModalidad($conex, $wbasedato, $num_politica): Consulta y crea un array de todos los rangos de uso creado para una politica.

    @conex        : Link de conexión a la base de datos.
    @wbasedatp    : Prefijo de las tablas que se deben consultar.
    @num_politica : Es el código que identifica la politica para que se van a consultar sus rangos límites.

    return array  : Rangos de límites de la politica
*/
function consultarLimitesModalidad($conex, $wbasedato, $num_politica, $wconcepto)
{
    $query = "  SELECT  Limcon AS wcodigo_concepto, Liminf AS wlimit_inicial, Limsup AS wlimit_final, Limpro AS wcodigo_procedimiento, Limemp AS empresa, Limest AS estado, id
                FROM    {$wbasedato}_".TB_EXCEPCIONES_UVR."
                WHERE   Limpll = '{$num_politica}'
                        AND Limcon = '{$wconcepto}'";
    $result = mysql_query($query, $conex);
    $arr_limites_modalidad = array();
    while($row = mysql_fetch_array($result))
    {
        $clave = $row['wcodigo_concepto'].'_'.$row['wlimit_inicial'].'_'.$row['wlimit_final'].'_'.$row['wcodigo_procedimiento'].'_'.$row['empresa'];
        $clave = str_replace("*", "nxn", $clave);
        $arr_limites_modalidad[$clave] = array("id"=>$row['id'], "estado"=>$row['estado']);
    }
    return $arr_limites_modalidad;
}

/**
    consultarConceptosPrincipales($conex, $wbasedato, $num_politica): Consulta y crea un array de todos los conceptos principales para una politica.

    @conex        : Link de conexión a la base de datos.
    @wbasedatp    : Prefijo de las tablas que se deben consultar.
    @num_politica : Es el código que identifica la politica para que se van a consultar sus concpetos principales.

    return array  : Conceptos principales de la politica
*/
function consultarConceptosPrincipales($conex, $wbasedato, $num_politica)
{

    $query = "  SELECT  Cphcpp AS wconcepto_principal, 'cobro_hora' AS tipo_cobro
                FROM    {$wbasedato}_000181
                WHERE   Cphpol = '{$num_politica}'
                            AND Cphest = 'on'

                UNION

                SELECT  Anecpp AS wconcepto_principal, 'cobro_anestesia' AS tipo_cobro
                FROM    {$wbasedato}_000182
                WHERE   Anepol = '{$num_politica}'
                        AND Aneest = 'on'

                UNION

                SELECT  Usocpp AS wconcepto_principal, 'cobro_uso' AS tipo_cobro
                FROM    {$wbasedato}_000183
                WHERE   Usopol = '{$num_politica}'
                        AND Usoest = 'on'";

    // print_r($query);

    $result = mysql_query($query, $conex);
    $arr_conceptos_principales = array();
    while($row = mysql_fetch_array($result))
    {
        $clave = $row['wconcepto_principal'];
        $arr_conceptos_principales[$clave] = $row['tipo_cobro'];
    }
    return $arr_conceptos_principales;
}

function guardarConfiguracionConceptos($conex, $wbasedato, &$data, $arr_conceptos, &$num_politica, $fecha_actual, $hora_actual, $user_session, $wentidad, $arrTiempoApl)
{
    // $fp = fopen('archivo.txt',"w+");
    // fwrite($fp, print_r($arr_conceptos,true));
    // fclose($fp);
	$arrTiempoApl = json_decode($arrTiempoApl, true);

    $arr_conceptos_principales = consultarConceptosPrincipales($conex, $wbasedato, $num_politica);
    $arr_cobro_anestesia       = consultarCobroAnestesia($conex, $wbasedato, $num_politica, "");
    $arr_cobro_uso             = consultarCobroUso($conex, $wbasedato, $num_politica, "");
    $control_conceptos_ant     = $arr_conceptos_principales;
    // print_r($control_conceptos_ant); exit();
    foreach ($arr_conceptos as $wconcepto_principal => $arr_info)
    {
        $arr_cantidades_conceptos = consultarCantidadesConceptosPlantilla($conex, $wbasedato, $num_politica);
        $cantidad = $arr_info['cantidad_concepto'];

        // consultar si está inactivo el concepto en la tabla de conceptos de la plantilla, entonces lo activa
        $query = "  SELECT  Plccan AS cantidad_concepto
                    FROM    {$wbasedato}_000195
                    WHERE   Plcpol = '{$num_politica}'
                            AND Plccpp = '{$wconcepto_principal}'
                            AND Plcest = 'off'";
        $result = mysql_query($query, $conex);
        if(mysql_num_rows($result) > 0)
        {
            $query = "  UPDATE {$wbasedato}_000195 SET Plccan = '{$cantidad}', Plcest='on' WHERE Plcpol = '{$num_politica}' AND Plccpp = '{$wconcepto_principal}'";

            if($result = mysql_query($query, $conex))
            { }
            else
            {
                $data['error'] = 1;
                $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar cantidad de un concepto.";
            }
        }
        else
        {
            // Cuando no existe asociado en $control_conceptos_ant (osea a ningún tipo de cobro) ó por ejemplo es nuevo y no está en la tabla de conceptos de
            // la plantilla $arr_cantidades_conceptos
            if(!array_key_exists($wconcepto_principal, $control_conceptos_ant) || !array_key_exists($wconcepto_principal, $arr_cantidades_conceptos))
            {
                // consultar si está activo (puede que sea por rango y no tenga en el momento) el concepto en la tabla de conceptos de la plantilla, entonces lo activa
                $query = "  SELECT  Plccan AS cantidad_concepto
                            FROM    {$wbasedato}_000195
                            WHERE   Plcpol = '{$num_politica}'
                                    AND Plccpp = '{$wconcepto_principal}'
                                    AND Plcest = 'on'";
                $result = mysql_query($query, $conex);
                if(mysql_num_rows($result) == 0)
                {
                    $query = "  INSERT INTO {$wbasedato}_000195
                                            (Medico, Fecha_data, Hora_data, Plcpol, Plccpp, Plccan, Plcest, Seguridad)
                                VALUES      ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}', '{$num_politica}', '{$wconcepto_principal}', '{$cantidad}', 'on', 'C-{$user_session}')";
                    // print_r($query);
                    if($result = mysql_query($query, $conex))
                    {

                    }else{
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente guardando nuevo concepto pricipal de la plantilla [{$wconcepto_principal}].";
                    }
                }
            }
            else
            {
                // HACER UPDATE DEL CONCEPTO QUE DEBE EXISTIR. AL FINAL DE TODO INACTIVAR TODO LO QUE QUEDE EN $control_conceptos_ant
                $query = "  UPDATE {$wbasedato}_000195 SET Plccan = '{$cantidad}' WHERE Plcpol = '{$num_politica}' AND Plccpp = '{$wconcepto_principal}'";

                if($result = mysql_query($query, $conex))
                { }
                else
                {
                    $data['error'] = 1;
                    $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar cantidad de un concepto.";
                }
                unset($control_conceptos_ant[$wconcepto_principal]);
            }
        }

        if(array_key_exists('cobro_hora', $arr_info))
        {
            $arr_cobro_hora = consultarCobroHora($conex, $wbasedato, $num_politica, $wconcepto_principal);
            // $wid_cobro_hora      = $arr_info['cobro_hora']['id'];
            $wtiempo_minimo_hora = $arr_info['cobro_hora']['wtiempo_minimo_hora'];
            $wcobro_por_hora     = $arr_info['cobro_hora']['wcobro_por_hora'];
            $wconcepto_hora      = $arr_info['cobro_hora']['wconcepto_hora'];
            $wprocedimiento_hora = $arr_info['cobro_hora']['wprocedimiento_hora'];

            $clave = $wconcepto_principal.'_'.$wtiempo_minimo_hora.'_'.$wcobro_por_hora.'_'.$wconcepto_hora.'_'.$wprocedimiento_hora;

            // Si la clave existe en el array entonces actualice si es necesario
            if(array_key_exists($clave, $arr_cobro_hora))
            {
                // si esta inactivo entonces cambiarlo a esta activo
                if($arr_cobro_hora[$clave]['estado'] != 'on')
                {
                    // Inactivar cualquier otro registro que pertenezca a la misma politica y mismo concepto principal anter de insertar otro, solo debe haber un registro activo.
                    $query = "  UPDATE {$wbasedato}_000181 SET Cphest = 'off' WHERE Cphpol = '".$num_politica."' AND Cphcpp = '{$wconcepto_principal}'";
                    if($result = mysql_query($query, $conex))
                    { }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar cobro por hora.";
                    }

                    $query = "  UPDATE {$wbasedato}_000181 SET Cphest = 'on' WHERE id = '".$arr_cobro_hora[$clave]['id']."'";

                    if($result = mysql_query($query, $conex))
                    { }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar cobro por hora.";
                    }
                }
                unset($arr_cobro_hora[$clave]);
            }
            else
            {
                // Inactivar cualquier otro registro que pertenezca a la misma politica y mismo concepto principal anter de insertar otro, solo debe haber un registro activo.
                $query = "  UPDATE {$wbasedato}_000181 SET Cphest = 'off' WHERE Cphpol = '".$num_politica."' AND Cphcpp = '{$wconcepto_principal}'";
                if($result = mysql_query($query, $conex))
                { }
                else
                {
                    $data['error'] = 1;
                    $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar cobro por hora.";
                }

                $query = "  INSERT INTO {$wbasedato}_000181
                                        (Medico, Fecha_data, Hora_data, Cphpol, Cphcpp, Cphtmn, Cphcph
                                        , Cphcon, Cphpro, Cphest, Seguridad)
                            VALUES      ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}', '{$num_politica}', '{$wconcepto_principal}', '{$wtiempo_minimo_hora}', '{$wcobro_por_hora}'
                                        , '{$wconcepto_hora}', '{$wprocedimiento_hora}', 'on', 'C-{$user_session}')";
                // print_r($query);
                if($result = mysql_query($query, $conex))
                {
                }
                else
                {
                    $data['error'] = 1;
                    $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de guardar cobro por hora [$wtiempo_minimo_hora].";
                }
            }

            if((array_key_exists($wconcepto_principal, $arr_conceptos_principales)) && $arr_conceptos_principales[$wconcepto_principal] == 'cobro_hora')
            {
                unset($arr_conceptos_principales[$wconcepto_principal]);
            }
        }
        elseif(array_key_exists('cobro_anestesia', $arr_info))
        {
            foreach ($arr_info['cobro_anestesia'] as $sub_wconcepto => $cobro_anestesia)
            {
                // $wid_cobro_anestesia   = $cobro_anestesia['cobro_anestesia']['wid_cobro_anestesia'];
                $wtipo_anestesia       = $cobro_anestesia['wtipo_anestesia'];
                $wtiempo_inicial       = $cobro_anestesia['wtiempo_inicial'];
                $wtiempo_final         = $cobro_anestesia['wtiempo_final'];
                $wcodigo_concepto      = $cobro_anestesia['wcodigo_concepto'];
                $wcodigo_procedimiento = $cobro_anestesia['wcodigo_procedimiento'];

                $clave = $wconcepto_principal.'_'.$wtipo_anestesia.'_'.codigoPoliticaComoClave(true, $num_politica).'_'.$wtiempo_inicial.'_'.$wtiempo_final.'_'.$wcodigo_concepto.'_'.$wcodigo_procedimiento;

                // Si la clave existe en el array entonces actualice si es necesario
                if(array_key_exists($clave, $arr_cobro_anestesia))
                {
                    // si esta inactivo entonces cambiarlo a esta activo
                    if($arr_cobro_anestesia[$clave]['estado'] != 'on')
                    {
                        $query = "  UPDATE {$wbasedato}_000182 SET Aneest = 'on' WHERE id = '".$arr_cobro_anestesia[$clave]['id']."'";

                        if($result = mysql_query($query, $conex))
                        { }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar rango de anestesia [$wtiempo_inicial - $wtiempo_final].";
                        }
                    }
                    unset($arr_cobro_anestesia[$clave]);
                }
                else
                {
                    // Si la clave no existe en el array entonces insertelo como un nuevo registro
                    $query = "  INSERT INTO {$wbasedato}_000182
                                            (Medico, Fecha_data, Hora_data, Anecod, Anepol, Anecpp, Anetin
                                                , Anetfn , Anecon, Anepro, Aneest, Seguridad )
                                VALUES      ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}','{$wtipo_anestesia}','{$num_politica}','{$wconcepto_principal}', '{$wtiempo_inicial}'
                                            , '{$wtiempo_final}', '{$wcodigo_concepto}', '{$wcodigo_procedimiento}', 'on', 'C-{$user_session}')";
                    // print_r($query);
                    if($result = mysql_query($query, $conex))
                    {
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de guardar cobro por anestesia, rango [$wtiempo_inicial - $wtiempo_final].";
                    }
                }
            }

            if((array_key_exists($wconcepto_principal, $arr_conceptos_principales)) && $arr_conceptos_principales[$wconcepto_principal] == 'cobro_anestesia')
            {
                unset($arr_conceptos_principales[$wconcepto_principal]);
            }
        }
        elseif(array_key_exists('cobro_uso', $arr_info))
        {
            foreach ($arr_info['cobro_uso'] as $sub_wconcepto => $cobro_uso)
            {
                // $wid_cobro_uso         = $cobro_uso['cobro_uso']['wid_cobro_uso'];
                $wtiempo_inicial       = $cobro_uso['wtiempo_inicial'];
                $wtiempo_final         = $cobro_uso['wtiempo_final'];
                $wcodigo_concepto      = $cobro_uso['wcodigo_concepto'];
                $wcodigo_procedimiento = $cobro_uso['wcodigo_procedimiento'];

                $clave = $wconcepto_principal.'_'.$wtiempo_inicial.'_'.$wtiempo_final.'_'.$wcodigo_concepto.'_'.$wcodigo_procedimiento;

                // Si la clave existe en el array entonces actualice si es necesario
                if(array_key_exists($clave, $arr_cobro_uso))
                {
                    // si esta inactivo entonces cambiarlo a esta activo
                    if($arr_cobro_uso[$clave]['estado'] != 'on')
                    {
                        $query = "  UPDATE {$wbasedato}_000183 SET Usoest = 'on' WHERE id = '".$arr_cobro_uso[$clave]['id']."'";

                        if($result = mysql_query($query, $conex))
                        { }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar rango de uso [$wtiempo_inicial - $wtiempo_final].";
                        }
                    }
                    unset($arr_cobro_uso[$clave]);
                }
                else
                {
                    // Si la clave no existe en el array entonces insertelo como un nuevo registro
                    $query = "  INSERT INTO {$wbasedato}_000183
                                            (Medico, Fecha_data, Hora_data, Usopol, Usocpp, Usotmn
                                            , Usotfn , Usocon, Usopro, Usoest, Seguridad )
                                VALUES      ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}','{$num_politica}','{$wconcepto_principal}', '{$wtiempo_inicial}'
                                            , '{$wtiempo_final}', '{$wcodigo_concepto}', '{$wcodigo_procedimiento}', 'on', 'C-{$user_session}')";
                    // print_r($query);
                    if($result = mysql_query($query, $conex))
                    {
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de guardar cobro por tiempo de uso, rango [$wtiempo_inicial - $wtiempo_final].";
                    }
                }
            }

            if((array_key_exists($wconcepto_principal, $arr_conceptos_principales)) && $arr_conceptos_principales[$wconcepto_principal] == 'cobro_uso')
            {
                unset($arr_conceptos_principales[$wconcepto_principal]);
            }
			
			//--> Jerson: Actalizar el campo Usoatp (Aplicar con base al tiempo de la cx) 
			if( array_key_exists($wconcepto_principal, $arrTiempoApl)){
				$query = "  UPDATE {$wbasedato}_000183 
							   SET Usoatp = '".$arrTiempoApl[$wconcepto_principal]."' 
							 WHERE Usopol = '".$num_politica."'
							   AND Usocpp = '".$wconcepto_principal."'
						";
				mysql_query($query, $conex);
			}				
        }

        $arr_limites_modalidad = consultarLimitesModalidad($conex, $wbasedato, $num_politica, $wconcepto_principal);
        // print_r($arr_limites_modalidad);
        if(array_key_exists("limite_modalidad", $arr_info))
        {
            $limites = $arr_info['limite_modalidad'];

            foreach ($arr_info['limite_modalidad'] as $sub_limite => $limites)
            {
                $wlimit_inicial        = $limites['wlimit_inicial'];
                $wlimit_final          = $limites['wlimit_final'];
                $wcodigo_concepto      = $limites['wcodigo_concepto'];
                $wcodigo_procedimiento = $limites['wcodigo_procedimiento'];

                $clave = $wconcepto_principal.'_'.$wlimit_inicial.'_'.$wlimit_final.'_'.$wcodigo_procedimiento.'_'.$wentidad;
                $clave = str_replace("*", "nxn", $clave); // Se cambia el '*' para que se pueda encontrar la key
                // $clave = $row['wconcepto_principal'].'_'.$row['wlimit_inicial'].'_'.$row['wlimit_final'].'_'.$row['wcodigo_concepto'].'_'.$row['wcodigo_procedimiento'].'_'.$row['empresa'];

                $implode_excluidos = implode('|',array_keys($limites['excluidos'])); // Lista de Materiales y medicamentos a excluir para que no sean facturables.

                // Si la clave existe en el array entonces actualice si es necesario
                if(array_key_exists($clave, $arr_limites_modalidad))
                {
                    // si esta inactivo entonces cambiarlo a activo
                    // if($arr_limites_modalidad[$clave]['estado'] != 'on')
                    {
                        $query = "  UPDATE {$wbasedato}_".TB_EXCEPCIONES_UVR." SET Limest = 'on', Limexc = '{$implode_excluidos}' WHERE id = '".$arr_limites_modalidad[$clave]['id']."'";

                        if($result = mysql_query($query, $conex))
                        { }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar un límite de modalidad [$wlimit_inicial - $wlimit_final].";
                        }
                    }
                    unset($arr_limites_modalidad[$clave]);
                }
                else
                {
                    // Si la clave no existe en el array entonces insertelo como un nuevo registro
                   $query = "   INSERT INTO {$wbasedato}_".TB_EXCEPCIONES_UVR."
                                            (Medico, Fecha_data, Hora_data, Limpll, Limcon, Limemp
                                            , Liminf , Limsup, Limpro,  Limexc, Limest, Seguridad )
                                VALUES      ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}','{$num_politica}','{$wcodigo_concepto}', '{$wentidad}'
                                            , '{$wlimit_inicial}', '{$wlimit_final}', '".$wcodigo_procedimiento."', '{$implode_excluidos}', 'on', 'C-{$user_session}')";
                    // print_r($query);
                    if($result = mysql_query($query, $conex))
                    {
                    }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de guardar un límite de modalida, rango [$wlimit_inicial - $wlimit_final].";
                    }
                }
            }

            /*if((array_key_exists($wconcepto_principal, $arr_conceptos_principales)) && $arr_conceptos_principales[$wconcepto_principal] == 'limite_modalidad')
            {
                unset($arr_conceptos_principales[$wconcepto_principal]);
            }*/
        }

        // Si quedaron elementos en el array de limites de UVRs entonces dejarlos inactivos.
        $arr_temp_off = array();
        foreach ($arr_limites_modalidad as $clave => $arr_info)
        {
            $arr_temp_off[] = $arr_info['id'];
        }

        if(count($arr_temp_off) > 0)
        {
            $ids_off = implode("','", $arr_temp_off);
            $query = " UPDATE {$wbasedato}_".TB_EXCEPCIONES_UVR." SET Limest = 'off' WHERE id IN ('".$ids_off."')";
            if($result = mysql_query($query, $conex))
            {
            }
            else
            {
                $data['error'] = 1;
                $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de cambiar a estado inactivo rangos de límite de modalidad [IDs: '$ids_off'].";
            }
        }

        // print_r($wconcepto_principal);
    }

    // INACTIVAR RANGOS SOBRANTES COBRO_ANESTESIA
    // Si quedaron elementos en el array entonces dejarlos inactivos.
    $arr_temp_off = array();
    foreach ($arr_cobro_anestesia as $clave => $arr_info)
    {
        $arr_temp_off[] = $arr_info['id'];
    }

    if(count($arr_temp_off) > 0)
    {
        $ids_off = implode("','", $arr_temp_off);
        $query = " UPDATE {$wbasedato}_000182 SET Aneest = 'off' WHERE id IN ('".$ids_off."')";
        if($result = mysql_query($query, $conex))
        {
        }
        else
        {
            $data['error'] = 1;
            $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de cambiar a estado inactivo rangos de anestesia [IDs: '$ids_off'].";
        }
    }

    // INACTIVAR RANGOS SOBRANTES COBRO_USO
    // Si quedaron elementos en el array entonces dejarlos inactivos.
    $arr_temp_off = array();
    foreach ($arr_cobro_uso as $clave => $arr_info)
    {
        $arr_temp_off[] = $arr_info['id'];
    }

    if(count($arr_temp_off) > 0)
    {
        $ids_off = implode("','", $arr_temp_off);
        $query = " UPDATE {$wbasedato}_000183 SET Usoest = 'off' WHERE id IN ('".$ids_off."')";
        if($result = mysql_query($query, $conex))
        {
        }
        else
        {
            $data['error'] = 1;
            $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de cambiar a estado inactivo rangos de uso [IDs: '$ids_off'].";
        }
    }

    // print_r($arr_conceptos_principales);
    if(count($arr_conceptos_principales) > 0)
    {
        foreach ($arr_conceptos_principales as $wconcepto_principal => $tipo_cobro)
        {
            switch ($tipo_cobro) {
                case 'cobro_hora':
                    // Cambia a estado off los tipo cobro por hora
                    $query = "  UPDATE {$wbasedato}_000181 SET Cphest = 'off' WHERE Cphpol = '".$num_politica."' AND Cphcpp = '{$wconcepto_principal}'";
                    if($result = mysql_query($query, $conex))
                    { }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar cobro por hora.";
                    }
                    break;

                case 'cobro_anestesia':
                    // si esta activo entonces cambiarlo a esta inactivo
                    $query = "  UPDATE {$wbasedato}_000182 SET Aneest = 'off' WHERE Anepol = '".$num_politica."' AND Anecpp = '{$wconcepto_principal}'";

                    if($result = mysql_query($query, $conex))
                    { }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de cambiar a estado inactivo los tipos de cobro por anestesia";
                    }
                    break;

                case 'cobro_uso':
                    // si esta activo entonces cambiarlo a esta inactivo
                    $query = "  UPDATE {$wbasedato}_000183 SET Usoest = 'off' WHERE Usopol = '".$num_politica."' AND Usocpp = '{$wconcepto_principal}'";

                    if($result = mysql_query($query, $conex))
                    { }
                    else
                    {
                        $data['error'] = 1;
                        $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar rango de uso.";
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
    }

    if(count($control_conceptos_ant) > 0)
    {
        $inactivar_cods = implode("','", array_keys($control_conceptos_ant));
        $query = "  UPDATE {$wbasedato}_000195 SET Plcest = 'off' WHERE Plcpol = '{$num_politica}' AND Plccpp IN ('{$inactivar_cods}')";

        if($result = mysql_query($query, $conex))
        { }
        else
        {
            $data['error'] = 1;
            $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de inactivar Conceptos principales en la tabla de conceptos de la plantilla.";
        }
    }
}

function nombreConcepto($conex, $wbasedato, $codigo)
{
    $query = "  SELECT  Grucod AS codigo,Grudes AS nombre
                FROM    {$wbasedato}_000200
                WHERE  Grucod = '{$codigo}'";
    $result = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query (Consultar nombre concepto): ".$q_conceptos." - ".mysql_error());
    $row = mysql_fetch_array($result);
    $nombre = $row['nombre'];

    if($codigo == '*')
    {
        $nombre = 'TODOS';
    }

    return utf8_encode($nombre);
}

function nombreProcedimiento($conex, $wbasedato, $codigo)
{
    $query = "  SELECT  Procod AS codigo, Pronom AS nombre
                FROM    {$wbasedato}_000103
                WHERE  Procod = '{$codigo}'";
    $result = mysql_query($query, $conex) or die("Error: " . mysql_errno() . " - en el query (Consultar nombre procedimiento): ".$q_conceptos." - ".mysql_error());
    $row = mysql_fetch_array($result);
    $nombre = $row['nombre'];

    if($codigo == '*')
    {
        $nombre = 'TODOS';
    }

    return utf8_encode($nombre);
}

/**
    Esta funcion se encarga de leer un código de concepto y luego consultar los datos que tiene asiciados en la base de datos
*/
function cargarCobrosBD($conex, $wemp_pmla, $wbasedato, $identifica_concepto, $num_politica, $wconcepto, $tipo_cobro, &$arr_politica, $arr_tipo_anestesia, &$arr_temp_cb_x_horas)
{
    $html['cobros'] = "";
    // $html['limites_concepto'] = "";
    switch ($tipo_cobro)
    {
        case 'cobro_hora':
            if(!array_key_exists('cobro_hora', $arr_politica[$identifica_concepto])) { $arr_politica[$identifica_concepto]['cobro_hora'] = array(); }
            $query = "  SELECT  Cphcpp AS wconcepto_principal, Cphtmn AS wtiempo_minimo_hora, Cphcph AS wcobro_por_hora, Cphcon AS wcodigo_concepto, Cphpro AS wcodigo_procedimiento, Cphest AS estado, id
                        FROM    {$wbasedato}_000181
                        WHERE   Cphpol = '{$num_politica}'
                                AND Cphcpp = '{$wconcepto}'
                                AND Cphest = 'on'";
            $result = mysql_query($query, $conex);
            $arr_cobro_hora = array();
            while($row = mysql_fetch_array($result))
            {
                $clave = $row['wconcepto_principal'].'_'.$row['wtiempo_minimo_hora'].'_'.$row['wcobro_por_hora'].'_'.$row['wcodigo_concepto'].'_'.$row['wcodigo_procedimiento'];
                $arr_politica[$identifica_concepto][$tipo_cobro] = array("wconcepto_principal" => $row['wconcepto_principal'],
                                            "wtiempo_minimo_hora"   => $row['wtiempo_minimo_hora'],
                                            "wcobro_por_hora"       => $row['wcobro_por_hora'],
                                            "wconcepto_hora"      => $row['wcodigo_concepto'],
                                            "wprocedimiento_hora" => $row['wcodigo_procedimiento']);

                $arr_temp_cb_x_horas = array("wtiempo_minimo_hora"             => $row['wtiempo_minimo_hora'],
                                            "wcobro_por_hora"                  => $row['wcobro_por_hora'],
                                            "wconcepto_por_tiempo"             => $row['wcodigo_concepto'],
                                            "wprocedimiento_por_tiempo"        => $row['wcodigo_procedimiento'],
                                            "wconcepto_por_tiempo_nombre"      => nombreConcepto($conex, $wbasedato, $row['wcodigo_concepto']),
                                            "wprocedimiento_por_tiempo_nombre" => nombreProcedimiento($conex, $wbasedato, $row['wcodigo_procedimiento']));
            }
            break;

        case 'cobro_anestesia':
            if(!array_key_exists('cobro_anestesia', $arr_politica[$identifica_concepto])) { $arr_politica[$identifica_concepto]['cobro_anestesia'] = array(); }
            $query = "  SELECT  Anecpp AS wconcepto_principal, Anecod AS wtipo_anestesia, Anepol, Anetin AS wtiempo_inicial, Anetfn AS wtiempo_final, Anecon AS wcodigo_concepto, Anepro AS wcodigo_procedimiento, id, Aneest AS estado
                        FROM    {$wbasedato}_000182
                        WHERE   Anepol = '{$num_politica}'
                                AND Anecpp = '{$wconcepto}'
                                AND Aneest = 'on'";
            $result = mysql_query($query, $conex);
            $arr_cobro_anestesia = array();
            while($row = mysql_fetch_array($result))
            {
                /*$clave = $row['wtipo_anestesia'].'_'.$row['wtiempo_inicial'].'_'.$row['wtiempo_final'];
                $arr_politica[$identifica_concepto][$tipo_cobro][$clave] = array("wconcepto_principal"   => $row['wconcepto_principal'],
                                            "wtipo_anestesia"       => $row['wtipo_anestesia'],
                                            "Anepol"                => $row['Anepol'],
                                            "wtiempo_inicial"       => $row['wtiempo_inicial'],
                                            "wtiempo_final"         => $row['wtiempo_final'],
                                            "wcodigo_concepto"      => $row['wcodigo_concepto'],
                                            "wcodigo_procedimiento" => $row['wcodigo_procedimiento']);*/

                $wtipo_anestesia       = $row['wtipo_anestesia'];
                $wtiempo_inicial_anest = $row['wtiempo_inicial'];
                $wtiempofinal_anest    = $row['wtiempo_final'];
                $wcodigo_concepto      = $row['wcodigo_concepto'];
                $wcodigo_procedimiento = $row['wcodigo_procedimiento'];

                $wnombre_anestesia     = $arr_tipo_anestesia[$wtipo_anestesia];
                $wnombre_concepto      = nombreConcepto($conex, $wbasedato, $wcodigo_concepto);
                $wnombre_procedimiento = nombreProcedimiento($conex, $wbasedato, $wcodigo_procedimiento);

                $html['cobros'] .= nuevoRangoAnestesiaHtml($arr_politica, $wnombre_anestesia, $wtipo_anestesia, $wtiempo_inicial_anest, $wtiempofinal_anest, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento);
            }
            break;

        case 'cobro_uso':
            if(!array_key_exists('cobro_uso', $arr_politica[$identifica_concepto])) { $arr_politica[$identifica_concepto]['cobro_uso'] = array(); }
            $query = "  SELECT  Usocpp AS wconcepto_principal, Usotmn AS wtiempo_inicial, Usotfn AS wtiempo_final, Usocon AS wcodigo_concepto, Usopro AS wcodigo_procedimiento, Usoest AS estado, id
                        FROM    {$wbasedato}_000183
                        WHERE   Usopol = '{$num_politica}'
                                AND Usocpp = '{$wconcepto}'
                                AND Usoest = 'on'";
            $result = mysql_query($query, $conex);
            $arr_cobro_uso = array();
            while($row = mysql_fetch_array($result))
            {
                /*$clave = $row['wtiempo_inicial'].'_'.$row['wtiempo_final'];
                $arr_politica[$identifica_concepto][$tipo_cobro][$clave] = array(  "wconcepto_principal"   => $row['wconcepto_principal'],
                                        "wtiempo_inicial"       => $row['wtiempo_inicial'],
                                        "wtiempo_final"         => $row['wtiempo_final'],
                                        "wcodigo_concepto"      => $row['wcodigo_concepto'],
                                        "wcodigo_procedimiento" => $row['wcodigo_procedimiento']);*/

                $wtiempo_inicial       = $row['wtiempo_inicial'];
                $wtiempo_final         = $row['wtiempo_final'];
                $wcodigo_concepto      = $row['wcodigo_concepto'];
                $wcodigo_procedimiento = $row['wcodigo_procedimiento'];

                $wnombre_concepto      = nombreConcepto($conex, $wbasedato, $wcodigo_concepto);
                $wnombre_procedimiento = nombreProcedimiento($conex, $wbasedato, $wcodigo_procedimiento);

                $html['cobros'] .= nuevoRangoUsoHtml($arr_politica, $wtiempo_inicial, $wtiempo_final, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento);
            }
            break;
    }

    // Consultar las restricciones o excepciones que pueda tener según límites en la modalidad.
    // $identifica_concepto       $arr_politica[$identifica_concepto]
    $sql = "SELECT  Limpll, Limcon AS wcodigo_concepto, Liminf AS wlimit_inicial, Limsup AS wlimit_final, Limpro AS wcodigo_procedimiento, Limexc AS excluidos
            FROM    {$wbasedato}_".TB_EXCEPCIONES_UVR."
            WHERE   Limpll = '{$num_politica}'
                    AND Limcon = '{$identifica_concepto}'
                    AND Limest = 'on'";

    $result = mysql_query($sql, $conex);
    while($row = mysql_fetch_array($result))
    {
        $wlimit_inicial        = $row['wlimit_inicial'];
        $wlimit_final          = $row['wlimit_final'];
        $wcodigo_concepto      = $row['wcodigo_concepto'];
        $wcodigo_procedimiento = $row['wcodigo_procedimiento'];

        // Sección para guardar los límites configurados en el array de respuesta.
        $index_limit = $wlimit_inicial.'_'.$wlimit_final;
        if(!array_key_exists("limite_modalidad", $arr_politica[$wcodigo_concepto]))
        {
            $arr_politica[$wcodigo_concepto]["limite_modalidad"] = array();
        }

        if(!array_key_exists($index_limit, $arr_politica[$wcodigo_concepto]["limite_modalidad"]))
        {
            $arr_politica[$wcodigo_concepto]["limite_modalidad"][$index_limit] = array();
        }

        if(!array_key_exists($index_limit, $arr_politica[$wcodigo_concepto]['limite_modalidad']))
        {
            $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit] = array();
        }

        // $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["wlimit_inicial"]        = $row['wlimit_inicial'];
        // $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["wlimit_final"]          = $row['wlimit_final'];
        // $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["wcodigo_concepto"]      = $row['wcodigo_concepto'];
        // $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["wcodigo_procedimiento"] = $row['wcodigo_procedimiento'];
        // $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"]             = array();

        $arr_excluidos = array();
        if(!array_key_exists("excluidos", $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]))
        {
            $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"] = array();
        }

        if(trim($row['excluidos']) != '')
        {
            $arr_materiales_med = obtener_grupos_materiales($conex, $wemp_pmla, $wbasedato);
            $arr_excluidos_exp = explode("|", $row['excluidos']);
            foreach ($arr_excluidos_exp as $key => $codigo_excluido)
            {
                $arr_excluidos[$codigo_excluido] = $arr_materiales_med[$codigo_excluido];
                $nombre_excluido = $arr_materiales_med[$codigo_excluido];

                if(!array_key_exists($codigo_excluido, $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"]))
                {
                    $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"][$codigo_excluido] = "";
                }
                $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"][$codigo_excluido] = $nombre_excluido;
            }
        }

        $wnombre_concepto      = nombreConcepto($conex, $wbasedato, $wcodigo_concepto);
        $wnombre_procedimiento = nombreProcedimiento($conex, $wbasedato, $wcodigo_procedimiento);
        // $html['limites_concepto'] = nuevoLimiteHtml($arr_politica, $wlimit_inicial, $wlimit_final, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento, $arr_excluidos);
    }

    return $html;
}


/**
    Crea el html para adicionar una nueva fila de rangos de anestesia
*/
function nuevoRangoAnestesiaHtml(&$arr_politica, $wnombre_anestesia, $wtipo_anestesia, $wtiempo_inicial_anest, $wtiempofinal_anest, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento)
{
    // global $wnombre_anestesia, $wtipo_anestesia, $wtiempo_inicial_anest, $wtiempofinal_anest, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento;

    $dif_rango = $wtipo_anestesia.'_'.$wtiempo_inicial_anest.'_'.$wtiempofinal_anest;
    $id_unico = $identifica_concepto.'_'.$wtipo_anestesia.'_'.$wtiempo_inicial_anest.'_'.$wtiempofinal_anest.'_'.$wcodigo_concepto.'_'.$wcodigo_procedimiento;
    $id_unico = str_replace("*", "nxn", $id_unico);

    /*if(!array_key_exists($dif_rango, $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_anestesia']))
    {
        $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_anestesia'][$dif_rango] = array();
    }*/

    $arr_politica[$identifica_concepto]['cobro_anestesia'][$dif_rango] = array(   'wtipo_anestesia'    => $wtipo_anestesia
                                                                                  ,'wtiempo_inicial'    => $wtiempo_inicial_anest
                                                                                  ,'wtiempo_final'      => $wtiempofinal_anest
                                                                                  ,'wcodigo_concepto'   => $wcodigo_concepto
                                                                                  ,'wcodigo_procedimiento' => $wcodigo_procedimiento);

    $wnombre_concepto      = str_replace($wcodigo_concepto.'-', "", $wnombre_concepto); // Cuando se agrega desde la interfaz ya viene concatenado el código
    $wnombre_procedimiento = str_replace($wcodigo_procedimiento.'-', "", $wnombre_procedimiento); // Cuando se agrega desde la interfaz ya viene concatenado el código
    $html = '
            <tr id="tr_rango_tipo_anestesia_'.$id_unico.'" class="fila1">
                <td class="texto_add">
                    <input type="hidden" id="'.$id_unico.'_hdn_rango_tipo_anestesia" name="'.$id_unico.'_hdn_rango_tipo_anestesia" wtipo_anestesia="'.$wtipo_anestesia.'" wtiempo_inicial_anest="'.$wtiempo_inicial_anest.'" wtiempofinal_anest="'.$wtiempofinal_anest.'" wcodigo_concepto="'.$wcodigo_concepto.'" wcodigo_procedimiento="'.$wcodigo_procedimiento.'" >

                    '.$wtipo_anestesia.'-'.$wnombre_anestesia.'
                </td>
                <td class="texto_add">'.$wtiempo_inicial_anest.'</td>
                <td class="texto_add">'.$wtiempofinal_anest.'</td>
                <td class="texto_add">'.$wcodigo_concepto.'-'.$wnombre_concepto.'</td>
                <td class="texto_add">'.$wcodigo_procedimiento.'-'.$wnombre_procedimiento.'</td>
                <td class="texto_add" align="center">
                    <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" style="cursor:pointer;" onclick="eliminarFilaRango(\'cobro_anestesia\',\'tr_rango_tipo_anestesia_'.$id_unico.'\',\''.$identifica_concepto.'\',\'tr_rango_tipo_anestesia_\',\''.$dif_rango.'\');">
                </td>
            </tr>';
    return $html;
}

/**
    Crea el html para adicionar una nueva fila de rangos de uso
*/
function nuevoRangoUsoHtml(&$arr_politica, $wtiempo_inicial, $wtiempo_final, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento)
{
    // global $wtiempo_inicial, $wtiempo_final, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento;

    $dif_rango = $wtiempo_inicial.'_'.$wtiempo_final;
    $id_unico = $identifica_concepto.'_'.$wtiempo_inicial.'_'.$wtiempo_final.'_'.$wcodigo_concepto.'_'.$wcodigo_procedimiento;
    $id_unico = str_replace("*", "nxn", $id_unico);

    // if(!array_key_exists($dif_rango, $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_uso']))
    // {
    //     $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_uso'][$dif_rango] = array();
    // }

    $arr_politica[$identifica_concepto]['cobro_uso'][$dif_rango] = array(  'wtiempo_inicial'   => $wtiempo_inicial
                                                                           ,'wtiempo_final'     => $wtiempo_final
                                                                           ,'wcodigo_concepto'  => $wcodigo_concepto
                                                                           ,'wcodigo_procedimiento' => $wcodigo_procedimiento);

    $wnombre_concepto      = str_replace($wcodigo_concepto.'-', "", $wnombre_concepto); // Cuando se agrega desde la interfaz ya viene concatenado el código
    $wnombre_procedimiento = str_replace($wcodigo_procedimiento.'-', "", $wnombre_procedimiento); // Cuando se agrega desde la interfaz ya viene concatenado el código
    $html = '
            <tr id="tr_rango_de_rangos_'.$id_unico.'" class="fila1">
                <td class="texto_add">
                    <input type="hidden" id="'.$id_unico.'_hdn_rango_de_rangos" name="'.$id_unico.'_hdn_rango_de_rangos" wtiempo_inicial="'.$wtiempo_inicial.'" wtiempo_final="'.$wtiempo_final.'" wcodigo_concepto="'.$wcodigo_concepto.'" wcodigo_procedimiento="'.$wcodigo_procedimiento.'" >
                    '.$wtiempo_inicial.'
                </td>
                <td class="texto_add">'.$wtiempo_final.'</td>
                <td class="texto_add">'.$wcodigo_concepto.'-'.$wnombre_concepto.'</td>
                <td class="texto_add">'.$wcodigo_procedimiento.'-'.$wnombre_procedimiento.'</td>
                <td class="texto_add" align="center">
                    <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" style="cursor:pointer;" onclick="eliminarFilaRango(\'cobro_uso\',\'tr_rango_de_rangos_'.$id_unico.'\',\''.$identifica_concepto.'\',\'tr_rango_de_rangos_\',\''.$dif_rango.'\');">
                </td>
            </tr>';
    return $html;
}

/**
    Crea el html para adicionar una nueva fila a los límites de uvr's si aplica
*/
function nuevoLimiteHtml(&$arr_politica, $wlimit_inicial, $wlimit_final, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento, $arr_excluidos = array())
{
    $dif_rango = $wlimit_inicial.'_'.$wlimit_final;
    $id_unico = $identifica_concepto.'_'.$wlimit_inicial.'_'.$wlimit_final.'_'.$wcodigo_concepto.'_'.$wcodigo_procedimiento;
    $id_unico = str_replace("*", "nxn", $id_unico);

    // if(!array_key_exists($dif_rango, $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_uso']))
    // {
    //     $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_uso'][$dif_rango] = array();
    // }

    /*$arr_politica[$identifica_concepto]['limite_modalidad'][$dif_rango] = array(    'wlimit_inicial'   => $wlimit_inicial
                                                                                   ,'wlimit_final'     => $wlimit_final
                                                                                   ,'wcodigo_concepto'  => $wcodigo_concepto
                                                                                   ,'wcodigo_procedimiento' => $wcodigo_procedimiento);*/
    $arr_politica[$identifica_concepto]['limite_modalidad'][$dif_rango]['wlimit_inicial']        = $wlimit_inicial;
    $arr_politica[$identifica_concepto]['limite_modalidad'][$dif_rango]['wlimit_final']          = $wlimit_final;
    $arr_politica[$identifica_concepto]['limite_modalidad'][$dif_rango]['wcodigo_concepto']      = $wcodigo_concepto;
    $arr_politica[$identifica_concepto]['limite_modalidad'][$dif_rango]['wcodigo_procedimiento'] = $wcodigo_procedimiento;

    $html_excluidos = "";
    if(count($arr_excluidos) > 0)
    {
        foreach ($arr_excluidos as $codigo_excluido => $nombre_excluido)
        {
            $html_excluidos .= '
                            <li id="li_excluido_'.$id_unico.'_'.$codigo_excluido.'" >
                                <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" style="cursor:pointer;" onclick="eliminarExcluido(\'limite_modalidad\',\'li_excluido_'.$id_unico.'_'.$codigo_excluido.'\',\''.$id_unico.'\',\''.$codigo_excluido.'\');">
                                '.$codigo_excluido.'-'.$nombre_excluido.'
                            </li>';
        }
    }

    $html = '
            <tr id="tr_limites_'.$id_unico.'" class="fila1">
                <td class="texto_add" style="text-align:center;">
                    <input type="hidden" id="'.$id_unico.'_hdn_limite_modalidad" name="'.$id_unico.'_hdn_limite_modalidad" wlimit_inicial="'.$wlimit_inicial.'" wlimit_final="'.$wlimit_final.'" wcodigo_concepto="'.$wcodigo_concepto.'" wcodigo_procedimiento="'.$wcodigo_procedimiento.'" >
                    '.$wlimit_inicial.'
                </td>
                <td class="texto_add" style="text-align:center;">'.$wlimit_final.'</td>
                <!-- <td class="texto_add">'.$wnombre_concepto.'</td> -->
                <td class="texto_add">'.$wnombre_procedimiento.'</td>
                <td style="text-align:center;">
                            <table align="center">
                                <tr>
                                    <td style="background-color: #FFFEE2;" >Excluir grupo: <input type="text" id="wbuscador_materiales_'.$id_unico.'" name="wbuscador_materiales_'.$id_unico.'" class="buscar_materiales" > <input type="button" onclick="javascript: adicionarExcluir(\'wbuscador_materiales_'.$id_unico.'\',\'ul_excluidos_'.$id_unico.'\', \''.$id_unico.'\');" value="Agregar" > </td>
                                </tr>
                                <tr>
                                    <td style="text-align:left;">
                                        <div style="overflow:scroll; height: 75px; background-color:#ffffff">
                                            <ul id="ul_excluidos_'.$id_unico.'" style="margin:1px; padding:1px;">
                                                '.$html_excluidos.'
                                                <!--<li id="li_excluido_'.$id_unico.'" >
                                                    <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" style="cursor:pointer;" onclick="eliminarLimite(\'limite_modalidad\',\'tr_limites_'.$id_unico.'\',\''.$identifica_concepto.'\',\'tr_limites_\',\''.$dif_rango.'\');">
                                                    Oxigeno
                                                </li>
                                                <li id="li_excluido_'.$id_unico.'" >
                                                    <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" style="cursor:pointer;" onclick="eliminarLimite(\'limite_modalidad\',\'tr_limites_'.$id_unico.'\',\''.$identifica_concepto.'\',\'tr_limites_\',\''.$dif_rango.'\');">
                                                    Anestesias
                                                </li> -->
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                </td>
                <td class="texto_add" align="center">
                    <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" style="cursor:pointer;" onclick="eliminarLimite(\'limite_modalidad\',\'tr_limites_'.$id_unico.'\',\''.$identifica_concepto.'\',\'tr_limites_\',\''.$dif_rango.'\');">
                </td>
            </tr>';
    return $html;
}

function listarPlantillasGuardadas($conex, $wemp_pmla, $wbasedato) //, $arr_liquidacion
{
    global $caracter_ok, $caracter_ma;
    /***** Procedimientos *****/
    $arr_procedimientos = obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato);
    /***** Conceptos *****/
    $arr_conceptos = obtener_array_conceptos();
    /***** Entidades *****/
    $arr_entidades = obtener_array_entidades();
    /***** Tarifas *****/
    $arr_tarifas = Obtener_array_tarifas();
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

    $arr_especialidades = obtener_array_especialidades_fnLocal($conex, $wemp_pmla, $caracter_ok, $caracter_ma);

    /***** Listado de plantillas guardadas *****/
    $sql = "SELECT      Encpol AS num_politica, Encnom AS wnombre_plantilla, Encpro AS wprocedimiento, Encent AS wentidad, Enctar AS wtarifa, Encing AS wtipoempresa, Encesp AS wespecialidad
            FROM        {$wbasedato}_000180
            WHERE       Encest = 'on'";
    $result = mysql_query($sql,$conex) or die("[Listado de plantillas guardadas] Error: ".mysql_errno()." ".$sql." - ".mysql_error());
    $arr_plantillas = array();
    while ($row = mysql_fetch_array($result))
    {
        $arr_conceptos_pol = consultarConceptosPrincipales($conex, $wbasedato, $row['num_politica']);
        $arr_plantillas[$row['num_politica']] = array(  "num_politica"      => $row['num_politica'],
                                                        "wnombre_plantilla" => $row['wnombre_plantilla'],
                                                        "wprocedimiento"    => $row['wprocedimiento'],
                                                        "wentidad"          => $row['wentidad'],
                                                        "wtarifa"           => $row['wtarifa'],
                                                        "wtipoempresa"      => $row['wtipoempresa'],
                                                        "wespecialidad"     => $row['wespecialidad'],
                                                        // "wbaseliquidacion"  => $row['wbaseliquidacion'],
                                                        "arr_conceptos_pol" => $arr_conceptos_pol);
    }

    $html =
        '<table align="center" id="tabla_politicas">
            <tr class="encabezadoTabla encTabla">
                <td colspan="8" class="encabezadoTabla">Listado de politicas configuradas</td>
            </tr>
            <tr class="encabezadoTabla">
                <td>'.NOMBRE_PLANTILLA.'</td>
                <td>'.PROCEDIMIENTO.'</td>
                <td>'.ENTIDAD.'</td>
                <td>'.TARIFA.'</td>
                <td>'.TIPO_EMPRESA.'</td>
                <td>'.LB_ESPECIALIDAD.'</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>';

    $cont = 0;
    $cont_plantillas = 0;
    $filtro_buscar = '  <table align="center" style="width:100%;">
                            <tr class="encabezadoTabla encTabla">
                                <td colspan="8" class="encabezadoTabla" style="text-align:left;" >Filtrar plantillas:<input id="id_search_platilla" type="text" value="" name="id_search_platilla"></td>
                            </tr>
                        </table>';
    foreach ($arr_plantillas as $num_politica => $arr_politica)
    {
        $arr_vacio = array();
        $css = ($cont_plantillas%2 == 0) ? 'fila1': 'fila2';

        // El campo de procedimiento puede tener uno o varios procedimientos separados por comas.
        $lista_proced = "";
        $expl = explode(",", $arr_politica['wprocedimiento']);
        $cont = 0;
        foreach ($expl as $key => $value)
        {
            $color = 'background-color:'.(($cont % 2 == 0) ? '#'.COLOR_GRIS1.';': '#'.COLOR_GRIS2.';');
            $lista_proced .= '<div style="font-size:8pt; '.$color.'">'.$value.'-'.utf8_encode($arr_procedimientos[$value]).'</div>';
            $cont++;
        }

        $html .='
            <tr class="'.$css.' find" onmouseover="trOver(this);" onmouseout="trOut(this);">
                <td class="textoOcu">
                    <input type="hidden" id="arr_politica_'.$num_politica.'" name="arr_politica_'.$num_politica.'" value=\''.base64_encode(serialize(($arr_vacio))).'\'>
                    <input type="hidden" id="arr_conceptos_pol_'.$num_politica.'" name="arr_conceptos_pol_'.$num_politica.'" value=\''.json_encode($arr_politica['arr_conceptos_pol']).'\'>
                    <input type="hidden" id="arr_conceptos_pol_b64_'.$num_politica.'" name="arr_conceptos_pol_b64_'.$num_politica.'" value=\''.base64_encode(serialize($arr_politica['arr_conceptos_pol'])).'\'>
                    <span class="texto-columna texto-cortado">'.$num_politica.'-'.utf8_encode($arr_politica['wnombre_plantilla']).'</span>
                </td>
                <td class="texto-columna textoOcu" valign="middle" ><div style="height: 50px; overflow:auto;vertical-align:middle;">'.$lista_proced.'</div></td>
                <td class="texto-columna textoOcu"><span class="texto-cortado">'.utf8_encode($arr_entidades[$arr_politica['wentidad']]).'</span></td>
                <td class="texto-columna textoOcu"><span class="texto-cortado">'.$arr_politica['wtarifa'].'-'.utf8_encode($arr_tarifas[$arr_politica['wtarifa']]).'</span></td>
                <td class="texto-columna textoOcu"><span class="texto-cortado">'.utf8_encode($arr_tipo_empresa[$arr_politica['wtipoempresa']]).'</span></td>
                <td class="texto-columna textoOcu"><span class="texto-cortado">'.utf8_encode($arr_especialidades[$arr_politica['wespecialidad']]).'</span></td>
                <td class="texto-columna textoOcu" style="text-align:center;">
                    <button class="texto-columna btn_carga_pl" onclick="cargarPlantilla(this, \''.$num_politica.'\',\''.$arr_politica['wprocedimiento'].'\', \''.$arr_politica['wentidad'].'\', \''.$arr_politica['wtarifa'].'\', \''.$arr_politica['wtipoempresa'].'\', \''.utf8_encode($arr_politica['wnombre_plantilla']).'\', \''.utf8_encode($arr_politica['wespecialidad']).'\');" style="cursor:pointer;">Cargar</button>
                </td>
                <td class="textoOcu" style="text-align:center;">
                    <button class="texto-columna btn_carga_pl" onclick="eliminarPlantilla(this, \''.$num_politica.'\', \''.utf8_encode($arr_politica['wnombre_plantilla']).'\');" style="cursor:pointer;">Eliminar</button>
                </td>
            </tr>';

        $cont_plantillas++;
    }
    $html .= '</table>';

    if($cont_plantillas >= 5)
    {
        $html = '
                <table align="center">
                    <tr>
                        <td>'.$filtro_buscar.'</td>
                    </tr>
                    <tr>
                        <td>
                            <div align="center" style="height: 310px; overflow:auto;border: 2px solid #9f9f9f;">
                                '.$html.'
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <button onclick="reiniciarDatos();" >Iniciar nueva plantilla</button>
                        </td>
                    </tr>
                </table>';
    }
    else
    {
        $html = '
                <table align="center">
                    <tr>
                        <td>'.$filtro_buscar.'</td>
                    </tr>
                    <tr>
                        <td>
                            <div align="center" style="">
                                '.$html.'
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <button onclick="reiniciarDatos();" >Iniciar nueva plantilla</button>
                        </td>
                    </tr>
                </table>';
    }

    return $html;
}

function obtener_array_especialidades_fnLocal($conex, $wemp_pmla, $caracter_ok, $caracter_ma)
{
    global $conex, $wemp_pmla;
    $consultaAjax = '';
    include_once("root/comun.php");
    $arr_especialidades = obtener_array_especialidades();
    $arr_especialidades[""] = "";
    asort($arr_especialidades); // Ordenar alfabeticamente el array de especialidades
    return $arr_especialidades;
}

/**
 *  obtener_grupos_materiales: retorna un array con los grupos principales de materiales y medicamentos.
 */
function obtener_grupos_materiales($conex, $wemp_pmla, $wbasedato)
{
    $consultaAjax = '';
    include_once("root/comun.php");
    $wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
    $caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
    $sql = "    SELECT  SUBSTRING_INDEX(Artgru, '-', 1) AS codigo, SUBSTRING_INDEX(Artgru, '-', -1) AS nombre
                FROM    {$wbasedato_movhos}_000026
                GROUP BY Artgru";
    $res_materiales = mysql_query($sql,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar grupos de materiales): ".$sql." - ".mysql_error());

    $arr_grupos_materiales = array();
    while($row_material = mysql_fetch_array($res_materiales))
    {
        $row_material['nombre'] = str_replace($caracter_ma, $caracter_ok, $row_material['nombre']);
        $arr_grupos_materiales[trim($row_material['codigo'])] = utf8_encode($row_material['nombre']);
    }

    return $arr_grupos_materiales;
}

function listaProcedimientos($implode_procedimiento, $arr_procedimientos, $codigo_plantilla, $adicionar_nuevo_procedimiento = 'off')
{
    $html = '';
    $html_lista = '';
    $expl_procedimientos = (!empty($implode_procedimiento)) ? explode(",", $implode_procedimiento) : array();

    $cont = 0;
    foreach ($expl_procedimientos as $key => $procedimiento)
    {
        $nombre_ex = 'No está en el maestro';
        if(array_key_exists($procedimiento, $arr_procedimientos))
        {
            $nombre_ex = $procedimiento.'-'.$arr_procedimientos[$procedimiento];
        }
        $color = 'background-color:'.(($cont % 2 == 0) ? '#'.COLOR_GRIS1.';': '#'.COLOR_GRIS2.';');
        $cont++;
        $html_lista .= '<div id="div_wprocedimiento_'.(($procedimiento == '*') ? 'n' : $procedimiento).'" style="'.$color.'" ><input type="checkbox" checked="checked" value="'.$procedimiento.'" nombre="'.utf8_encode($nombre_ex).'" />'.utf8_encode($nombre_ex).'</div>';
    }

    if($adicionar_nuevo_procedimiento == 'off')
    {
        $html =$html_lista;
    }
    else
    {
        $html = $html_lista;
    }
    return $html;
}

if(isset($accion) && isset($form))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    switch($accion)
    {
        case 'insert':
            switch($form)
            {
                case 'guardar_datos_politica':
                        $arr_politica = unserialize(base64_decode($arr_politica));
                        // $wprocedimiento_add, $wconcepto_add
                        // print_r($arr_politica); exit();
                        $fecha_actual = date("Y-m-d");
                        $hora_actual = date("H:i:s");
                        $wpolitica = '';
                        $num_politica = '--';
                        if(count($arr_politica) > 0)
                        {
                            if($wcodigo_politica == '')
                            {
                                // No se puede filtar por lo activos porque puede generar llave duplicada.
                                $query = "  SELECT  Encpol
                                            FROM    {$wbasedato}_000180
                                            ORDER BY CONVERT(SUBSTRING_INDEX(Encpol, '-', -1) ,UNSIGNED INTEGER) DESC
                                            LIMIT 1";
                                if($result = mysql_query($query, $conex))
                                {
                                    if(mysql_num_rows($result) > 0)
                                    {
                                        $row = mysql_fetch_array($result);
                                        $exp = explode('-', $row['Encpol']);
                                        $consecutivo = ($exp[1]*1)+1;
                                        $wpolitica = PREF_POL.'-'.$consecutivo;
                                    }
                                    else
                                    {
                                        $wpolitica = PREF_POL.'-1';
                                    }
                                    $num_politica = $wpolitica;

                                    $wnombre_plantilla = utf8_decode($wnombre_plantilla);
                                    $query = "  INSERT INTO {$wbasedato}_000180
                                                            (Medico, Fecha_data, Hora_data, Encpol, Encnom, Encpro,
                                                            Encent, Enctar, Encing, Encesp, Encest, Seguridad )
                                                VALUES      ('{$wbasedato}', '{$fecha_actual}', '{$hora_actual}', '{$wpolitica}', '{$wnombre_plantilla}', '{$wprocedimiento}',
                                                            '{$wentidad}', '{$wtarifa}', '{$wtipoempresa}', '{$wespecialidad}', 'on', 'C-{$user_session}')";
                                    // print_r($query);
                                    if($result = mysql_query($query, $conex))
                                    {
                                        $data['mensaje'] = "Se guardó la plantilla con éxito.";
                                        guardarConfiguracionConceptos($conex, $wbasedato, $data, $arr_politica, $num_politica, $fecha_actual, $hora_actual, $user_session, $wentidad, $arrTiempoApl);
                                    }
                                    else
                                    {
                                        $data['error'] = 1;
                                        $data['mensaje'] = "Ocurrió un inconveniente al momento de guardar el encabezado de la politica.";
                                    }
                                }
                                else
                                {
                                    $data['error'] = 1;
                                    $data['mensaje'] = "No se pudo guardar la politica porque no fue posible consultar el siguiente consecutivo.";
                                }
                            }
                            else
                            {
                                $num_politica = $wcodigo_politica;
                                $wnombre_plantilla = utf8_decode($wnombre_plantilla);
                                $query = "  UPDATE {$wbasedato}_000180
                                            SET     Encnom = '{$wnombre_plantilla}'
                                                    , Encpro = '{$wprocedimiento}'
                                                    , Encent = '{$wentidad}'
                                                    , Enctar = '{$wtarifa}'
                                                    , Encing = '{$wtipoempresa}'
                                                    , Encesp = '{$wespecialidad}'
                                                    , Seguridad = 'C-{$user_session}'
                                            WHERE Encpol = '{$num_politica}'";
                                if($result = mysql_query($query, $conex))
                                {
                                    $data['mensaje'] .= "Plantilla actualizada.";
                                }
                                else
                                {
                                    $data['error'] = 1;
                                    $data['mensaje'] .= "\n\nOcurrió un inconveniente al momento de actualizar el encabezado de la politica.";
                                }
                                guardarConfiguracionConceptos($conex, $wbasedato, $data, $arr_politica, $num_politica, $fecha_actual, $hora_actual, $user_session, $wentidad, $arrTiempoApl);
                            }
                        }
                        else
                        {
                            $data['error'] = 1;
                            $data['mensaje'] = "No se han configurado Conceptos, dir&iacute;jase a la secci&oacute;n \n\n'Adicionar Concepto'\n\n para configurar una nueva politica.";
                        }

                        $data['arr_politica'] = base64_encode(serialize($arr_politica));
                        // echo "<pre>"; print_r($arr_politica); echo "</pre>";
                        // $data['arr_politica'] = base64_encode(serialize($arr_politica));
                        // echo json_encode($data);
                        // return;
                        $data['num_politica'] = $num_politica;
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
                case 'reserva_tipo_cobro':
                    $arr_politica = unserialize(base64_decode($arr_politica));
                    if(!array_key_exists($tipo_cobro, $arr_politica[$identifica_concepto]))
                    {
                        $arr_politica[$identifica_concepto][$tipo_cobro] = array();
                        if($tipo_cobro == 'cobro_hora')
                        {
                            $arr_politica[$identifica_concepto][$tipo_cobro] = array();
                            $arr_politica[$identifica_concepto][$tipo_cobro]['wtiempo_minimo_hora'] = '';
                            $arr_politica[$identifica_concepto][$tipo_cobro]['wcobro_por_hora']     = '';
                            $arr_politica[$identifica_concepto][$tipo_cobro]['wconcepto_hora']      = $identifica_concepto;
                            $arr_politica[$identifica_concepto][$tipo_cobro]['wprocedimiento_hora'] = $wprocedimiento;
                        }
                    }

                    $data['wtiempo_minimo_hora']       = '';
                    $data['wcobro_por_hora']           = '';
                    // $data['wconcepto_por_tiempo']      = '';
                    // $data['wprocedimiento_por_tiempo'] = '';
                    $data['tr_rango_tipo_anestesia']   = '';
                    $data['tr_rango_de_rangos']        = '';

                    if($tipo_cobro != 'cobro_hora')
                    {
                        unset($arr_politica[$identifica_concepto]['cobro_hora']);
                        $data['wtiempo_minimo_hora']       = 'wtiempo_minimo_hora_'.$identifica_concepto;
                        $data['wcobro_por_hora']           = 'wcobro_por_hora_'.$identifica_concepto;
                    }
                    if($tipo_cobro != 'cobro_anestesia')
                    {
                        unset($arr_politica[$identifica_concepto]['cobro_anestesia']);
                        $data['tr_rango_tipo_anestesia']   = 'tr_rango_tipo_anestesia_'.$identifica_concepto;
                    }
                    if($tipo_cobro != 'cobro_uso')
                    {
                        unset($arr_politica[$identifica_concepto]['cobro_uso']);
                        $data['tr_rango_de_rangos']        = 'tr_rango_de_rangos_'.$identifica_concepto;
                    }

                    $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'valores_cobro_hora':
                    $arr_politica = unserialize(base64_decode($arr_politica));
                    $arr_politica[$identifica_concepto]['cobro_hora']['wtiempo_minimo_hora'] = $wtiempo_minimo_hora;
                    $arr_politica[$identifica_concepto]['cobro_hora']['wcobro_por_hora']     = $wcobro_por_hora;
                    $arr_politica[$identifica_concepto]['cobro_hora']['wconcepto_hora']      = $wconcepto_hora;
                    $arr_politica[$identifica_concepto]['cobro_hora']['wprocedimiento_hora'] = $wprocedimiento_hora;
                    unset($arr_politica[$identifica_concepto]['cobro_anestesia']);
                    unset($arr_politica[$identifica_concepto]['cobro_uso']);
                    $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'cantidad_concepto':
                        $arr_politica = unserialize(base64_decode($arr_politica));

                        if(!array_key_exists($identifica_concepto, $arr_politica))
                        {
                            $arr_politica[$identifica_concepto] = array();
                        }

                        if(!array_key_exists("cantidad_concepto", $arr_politica[$identifica_concepto]))
                        {
                            $arr_politica[$identifica_concepto]['cantidad_concepto'] = 1;
                            echo "que pasóooo";
                        }
                        $arr_politica[$identifica_concepto]['cantidad_concepto'] = $cantidad;
                        $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'CODIGO_EJEMPLO' :
                        $arr_solpend = unserialize(base64_decode($arr_solpend));
                        $arr_solpend = $arr_solpend[$wid_solpend];
                        /* $arr_solpend = array('wid_solpend'=>'','wfuente'=>'','word_asociada'=>'','wcedula'=>'','wtipodoc'=> '','whistoria'=>'','whing'=> '','wtipoestudio'=>''); */
                        $wcedula    = $arr_solpend['wcedula'];
                        $wtipodoc   = $arr_solpend['wtipodoc'];

                        $sql = "UPDATE  ".$wbasedato."_".SOLICITUDES."
                                        SET Solapr = '".$westadoapr."'
                                WHERE   Solfue = '".$wfuente."'
                                        AND id = '".$wid_solpend."'";
                        if($resultE = mysql_query($sql,$conex))
                        {
                            $descripcion = "tabla:'".$wbasedato."_".SOLICITUDES."'|id:'$wid_solpend'|columnUpd:'Solapr'|columnFiltro:'Solfue-id'|valueFiltro:'".$wfuente."-".$wid_solpend."'|obs:' La solicitud con id. $wid_solpend combió su estado de aprobación a -$westadoapr-'";
                            insertLog($conex, $wbasedato, $user_session, $accion, $form, '', $descripcion, $wcedula);
                        }
                        else
                        {
                            $descripcion = "(".mysql_errno().') '.mysql_error()."|obs:'Error al cambiar el estado de aprobación de la solicitud de id. ".$wid_solpend;
                            insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wcedula, $sql);
                            $data['mensaje'] = utf8_encode('No se pudo cambiar el estado de aprobación de la solicitud.');
                            $data['error'] = 1;
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
                case 'agregar_nuevo_concepto':
                        $arr_politica = unserialize(base64_decode($arr_politica));
                        $html_filas_configuradas = '';
						$aplicarRangoTiempoCadaPro = "off";
                        // $html_filas_limites = '';

                        if($load_bd != 'cargar_de_la_bd')
                        {
                            // Esta sección es para que cuando se va a agregar un concepto llega el nombre del procedimiento o concepto concatenado con el
                            // código, entonces se elimina el código para que más adelante que se le concatena nuevamente el código no se vea repetido.
                            $nombre_concepto = explode("-", $nombre_concepto);
                            unset($nombre_concepto[0]);
                            $nombre_concepto = implode('-', $nombre_concepto);

                            $nombre_procedimiento = explode("-", $nombre_procedimiento);
                            unset($nombre_procedimiento[0]);
                            $nombre_procedimiento = implode('-', $nombre_procedimiento);
                        }

                        $wnuevo_concepto_gral_hora = $wnuevo_concepto_gral;
                        $nombre_concepto_hora      = $nombre_concepto;
                        $wprocedimiento_hora       = $wprocedimiento;
                        $nombre_procedimiento_hora = $nombre_procedimiento;
                        $arr_temp_cb_x_horas = array("wtiempo_minimo_hora"             => "",
                                                    "wcobro_por_hora"                  => "",
                                                    "wconcepto_por_tiempo"             => $wnuevo_concepto_gral,
                                                    "wprocedimiento_por_tiempo"        => $wprocedimiento,
                                                    "wconcepto_por_tiempo_nombre"      => $nombre_concepto,
                                                    "wprocedimiento_por_tiempo_nombre" => $nombre_procedimiento);

                        //-- Verificar si el concepto mueve inventario
                        $q_ConInv = "   SELECT  Gruinv
                                        FROM    {$wbasedato}_000200
                                        WHERE   Grucod = '{$identifica_concepto}'";
                        $res_ConInv = mysql_query($q_ConInv,$conex) or die("Error en el query: ".$q_ConInv."<br>Tipo Error:".mysql_error());
                        $ConceptoInventario = 'off';
                        if($row_ConInv = mysql_fetch_array($res_ConInv))
                        {
                            $ConceptoInventario = $row_ConInv['Gruinv'];
                        }
                        // --

                        if($ConceptoInventario != 'on')
                        {
                            // echo "<pre>"; print_r($_SESSION['arr_conceptos']); echo "</pre>";
                            /***** Tipos de anestesia *****/
                            $sql = "SELECT  Selcda AS codigo, Selnda AS nombre
                                    FROM    {$wbasedato_HCE}_000012
                                    WHERE   Seltab = '{$wgrupo_tipos_anestesia}'
                                            AND Selest = 'on'
                                    ORDER BY Selnda";
                            $result = mysql_query($sql,$conex) OR die ($sql.' <br> '.mysql_error());

                            $arr_tipo_anestesia = array();
                            while ($row = mysql_fetch_array($result))
                            {
                                $arr_tipo_anestesia[$row['codigo']] = utf8_encode($row['nombre']);
                            }

                            // print_r($arr_politica);
                            if(count($arr_politica) == 0)
                            {
                                $arr_politica = array();
                            }
                            if(!array_key_exists($identifica_concepto, $arr_politica))
                            {
                                // BUSCAR SI EL CONCEPTO TIENE UN VALOR DE CANTIDAD PARA INICIALIZARLO AQUÍ CON BASE A # DE PLANTILLA Y CONCEPTO
                                $arr_cantidades_conceptos = consultarCantidadesConceptosPlantilla($conex, $wbasedato, $wcodigo_politica, $identifica_concepto);
                                $valor_cantidad_concepto = (array_key_exists($identifica_concepto, $arr_cantidades_conceptos)) ? $arr_cantidades_conceptos[$identifica_concepto]: 1;
                                $arr_politica[$identifica_concepto] = array("cantidad_concepto" => $valor_cantidad_concepto);
                            }

                            $arr_conceptos_pol_temp = "";
                            $arr_conceptos_pol_b64 = "";

                            if($load_bd == 'cargar_de_la_bd')
                            {
                                $html_generado           = cargarCobrosBD($conex, $wemp_pmla, $wbasedato, $identifica_concepto, $wcodigo_politica, $wnuevo_concepto_gral, $tipo_cobro, $arr_politica, $arr_tipo_anestesia, $arr_temp_cb_x_horas);
                                $html_filas_configuradas = $html_generado['cobros'];
                                // $html_filas_limites      = $html_generado['limites_concepto'];

                                // Estos array son para poder controlar los conceptos que se deben ir pintando paso a paso cuando se va a editar una politica de las lista
                                // de politicas, el array json es para recorrerlo desde javascript y el base64 es para modificarlo en php
                                // ambos array deben tener los mismos datos cuando llegan nuevamente al javascript (cada iteración disminuye una posición en los array).
                                if($arr_conceptos_pol != '')
                                {
                                   $arr_conceptos_pol_temp = unserialize(base64_decode($arr_conceptos_pol)); // Este array controla los conceptos que se le deben pintar a una politica de la lista de plantillas.

                                   unset($arr_conceptos_pol_temp[$identifica_concepto]);
                                   $arr_conceptos_pol_b64 = base64_encode(serialize($arr_conceptos_pol_temp));
                                   $arr_conceptos_pol_temp = json_encode($arr_conceptos_pol_temp);
                                }

                                // Al llamar la función cargarCobroBD es posible que los valore del array arr_temp_cb_x_horas, tenga nuevos valores debído a que en la base de datos
                                // esta configurado el tipo de cobro diferentes a los del concepto general que se esta preparando para mostrar en la vista.
                                $wnuevo_concepto_gral_hora = $arr_temp_cb_x_horas['wconcepto_por_tiempo'];
                                $nombre_concepto_hora      = $arr_temp_cb_x_horas['wconcepto_por_tiempo_nombre'];
                                $wprocedimiento_hora       = $arr_temp_cb_x_horas['wprocedimiento_por_tiempo'];
                                $nombre_procedimiento_hora = $arr_temp_cb_x_horas['wprocedimiento_por_tiempo_nombre'];
								
								//JERSON
								//Consultar el tiempo a tener en cuenta, solo el 1 registro porque para todos aplica la misma conf
								if($tipo_cobro == "cobro_uso"){
									$sqlTiempoApl = "
									SELECT Usoatp
									  FROM ".$wbasedato."_000183
									 WHERE Usopol = '".$wcodigo_politica."'
									   AND Usocpp = '".$identifica_concepto."'
									   AND Usoest = 'on'
									 LIMIT 1
									";
									$resTiempoApl = mysql_query($sqlTiempoApl, $conex) or die("Error en $sqlTiempoApl:".mysql_error());
									if($rowTiempoApl = mysql_fetch_array($resTiempoApl)){
										$aplicarRangoTiempoCadaPro = $rowTiempoApl['Usoatp'];
									}
								}
							}

                            if($load_bd != 'cargar_de_la_bd')
                            {
                                // Por defecto la vista del concepto se inicia en cobro por hora, entonces se crea de una vez esta posición en el control de la sesión.
                                $arr_politica[$identifica_concepto]['cobro_hora'] = array(  'wtiempo_minimo_hora'   => ''
                                                                                            ,'wcobro_por_hora'      => ''
                                                                                            ,'wconcepto_hora'       => $identifica_concepto
                                                                                            ,'wprocedimiento_hora'  => $wprocedimiento);
                                $arr_politica[$identifica_concepto]['cantidad_concepto'] = 1;
                            }
							echo "<input type='hidden' id='aplicarRangoTiempoCadaPro_".$identifica_concepto."' value='".$aplicarRangoTiempoCadaPro."'>";
                            return include_once("../../ips/procesos/view_cirugiaERP.php");
                        }
                        else
                        {
                            // echo json_encode($data);
                            echo "inventario";
                            return;
                        }
                    break;

                case 'nuevo_rango_anestesia':
                        $arr_politica = unserialize(base64_decode($arr_politica));

                        unset($arr_politica[$identifica_concepto]['cobro_hora']);
                        // unset($arr_politica[$identifica_concepto]['cobro_anestesia']);
                        unset($arr_politica[$identifica_concepto]['cobro_uso']);

                        $data['html'] = nuevoRangoAnestesiaHtml($arr_politica, $wnombre_anestesia, $wtipo_anestesia, $wtiempo_inicial_anest, $wtiempofinal_anest, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento);


                        /*$dif_rango = $wtipo_anestesia.'_'.$wtiempo_inicial_anest.'_'.$wtiempofinal_anest;
                        $id_unico = $identifica_concepto.'_'.$wtipo_anestesia.'_'.$wtiempo_inicial_anest.'_'.$wtiempofinal_anest.'_'.$wcodigo_concepto.'_'.$wcodigo_procedimiento;
                        $id_unico = str_replace("*", "nxn", $id_unico);

                        // if(!array_key_exists($dif_rango, $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_anestesia']))
                        // {
                        //     $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_anestesia'][$dif_rango] = array();
                        // }

                        $arr_politica[$identifica_concepto]['cobro_anestesia'][$dif_rango] = array(   'wtipo_anestesia'    => $wtipo_anestesia
                                                                                                                  ,'wtiempo_inicial'    => $wtiempo_inicial_anest
                                                                                                                  ,'wtiempo_final'      => $wtiempofinal_anest
                                                                                                                  ,'wcodigo_concepto'   => $wcodigo_concepto
                                                                                                                  ,'wcodigo_procedimiento' => $wcodigo_procedimiento);

                        $data['html'] = '<tr id="tr_rango_tipo_anestesia_'.$id_unico.'" class="fila1">
                                            <td class="texto_add">
                                                <input type="hidden" id="'.$id_unico.'_hdn_rango_tipo_anestesia" name="'.$id_unico.'_hdn_rango_tipo_anestesia" wtipo_anestesia="'.$wtipo_anestesia.'" wtiempo_inicial_anest="'.$wtiempo_inicial_anest.'" wtiempofinal_anest="'.$wtiempofinal_anest.'" wcodigo_concepto="'.$wcodigo_concepto.'" wcodigo_procedimiento="'.$wcodigo_procedimiento.'" >

                                                '.$wnombre_anestesia.'
                                            </td>
                                            <td class="texto_add">'.$wtiempo_inicial_anest.'</td>
                                            <td class="texto_add">'.$wtiempofinal_anest.'</td>
                                            <td class="texto_add">'.$wnombre_concepto.'</td>
                                            <td class="texto_add">'.$wnombre_procedimiento.'</td>
                                            <td class="texto_add" align="center">
                                                <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" onclick="eliminarFilaRango(\'cobro_anestesia\',\'tr_rango_tipo_anestesia_'.$id_unico.'\',\''.$identifica_concepto.'\',\'tr_rango_tipo_anestesia_\',\''.$dif_rango.'\');">
                                            </td>
                                        </tr>';*/
                        $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'nuevo_rango_de_rangos':
                        $arr_politica = unserialize(base64_decode($arr_politica));

                        unset($arr_politica[$identifica_concepto]['cobro_hora']);
                        unset($arr_politica[$identifica_concepto]['cobro_anestesia']);
                        // unset($arr_politica[$identifica_concepto]['cobro_uso']);

                        $data['html'] = nuevoRangoUsoHtml($arr_politica, $wtiempo_inicial, $wtiempo_final, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento);

                        /*$dif_rango = $wtiempo_inicial.'_'.$wtiempo_final;
                        $id_unico = $identifica_concepto.'_'.$wtiempo_inicial.'_'.$wtiempo_final.'_'.$wcodigo_concepto.'_'.$wcodigo_procedimiento;
                        $id_unico = str_replace("*", "nxn", $id_unico);

                        // if(!array_key_exists($dif_rango, $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_uso']))
                        // {
                        //     $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_uso'][$dif_rango] = array();
                        // }

                        $arr_politica[$identifica_concepto]['cobro_uso'][$dif_rango] = array(  'wtiempo_inicial'   => $wtiempo_inicial
                                                                                                           ,'wtiempo_final'     => $wtiempo_final
                                                                                                           ,'wcodigo_concepto'  => $wcodigo_concepto
                                                                                                           ,'wcodigo_procedimiento' => $wcodigo_procedimiento);

                        $data['html'] = '<tr id="tr_rango_de_rangos_'.$id_unico.'" class="fila1">
                                            <td class="texto_add">
                                                <input type="hidden" id="'.$id_unico.'_hdn_rango_de_rangos" name="'.$id_unico.'_hdn_rango_de_rangos" wtiempo_inicial="'.$wtiempo_inicial.'" wtiempo_final="'.$wtiempo_final.'" wcodigo_concepto="'.$wcodigo_concepto.'" wcodigo_procedimiento="'.$wcodigo_procedimiento.'" >
                                                '.$wtiempo_inicial.'
                                            </td>
                                            <td class="texto_add">'.$wtiempo_final.'</td>
                                            <td class="texto_add">'.$wnombre_concepto.'</td>
                                            <td class="texto_add">'.$wnombre_procedimiento.'</td>
                                            <td class="texto_add" align="center">
                                                <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" onclick="eliminarFilaRango(\'cobro_uso\',\'tr_rango_de_rangos_'.$id_unico.'\',\''.$identifica_concepto.'\',\'tr_rango_de_rangos_\',\''.$dif_rango.'\');">
                                            </td>
                                        </tr>';*/
                        $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'nuevo_limite_excepciones':
                        // $arr_politica = unserialize(base64_decode($arr_politica));

                        // $data['html'] = nuevoLimiteHtml($arr_politica, $wtiempo_inicial, $wtiempo_final, $identifica_concepto, $wcodigo_concepto, $wcodigo_procedimiento, $wnombre_concepto, $wnombre_procedimiento);

                        // $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'agregar_grupo_excluido':
                        $arr_politica = unserialize(base64_decode($arr_politica));
                        // $id_unico = str_replace("*", "nxn", $id_unico);

                        //wcodigo_procedimiento
                        $index_limit = $wlimit_inicial.'_'.$wlimit_final;
                        if(!array_key_exists("excluidos", $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]))
                        {
                            $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"] = array();
                        }

                        if(!array_key_exists($codigo_excluido, $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"]))
                        {
                            $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"][$codigo_excluido] = "";
                        }


                        $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"][$codigo_excluido] = $nombre_excluido;

                        $data['html'] = '<li id="li_excluido_'.$id_unico.'_'.$codigo_excluido.'" >
                                            <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" style="cursor:pointer;" onclick="eliminarExcluido(\'limite_modalidad\',\'li_excluido_'.$id_unico.'_'.$codigo_excluido.'\',\''.$id_unico.'\',\''.$codigo_excluido.'\');">
                                            '.$nombre_excluido.'
                                        </li>';

                        /*$dif_rango = $wtiempo_inicial.'_'.$wtiempo_final;
                        $id_unico = $identifica_concepto.'_'.$wtiempo_inicial.'_'.$wtiempo_final.'_'.$wcodigo_concepto.'_'.$wcodigo_procedimiento;
                        $id_unico = str_replace("*", "nxn", $id_unico);

                        // if(!array_key_exists($dif_rango, $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_uso']))
                        // {
                        //     $_SESSION['arr_conceptos'][$identifica_concepto]['cobro_uso'][$dif_rango] = array();
                        // }

                        $arr_politica[$identifica_concepto]['cobro_uso'][$dif_rango] = array(  'wtiempo_inicial'   => $wtiempo_inicial
                                                                                                           ,'wtiempo_final'     => $wtiempo_final
                                                                                                           ,'wcodigo_concepto'  => $wcodigo_concepto
                                                                                                           ,'wcodigo_procedimiento' => $wcodigo_procedimiento);

                        $data['html'] = '<tr id="tr_rango_de_rangos_'.$id_unico.'" class="fila1">
                                            <td class="texto_add">
                                                <input type="hidden" id="'.$id_unico.'_hdn_rango_de_rangos" name="'.$id_unico.'_hdn_rango_de_rangos" wtiempo_inicial="'.$wtiempo_inicial.'" wtiempo_final="'.$wtiempo_final.'" wcodigo_concepto="'.$wcodigo_concepto.'" wcodigo_procedimiento="'.$wcodigo_procedimiento.'" >
                                                '.$wtiempo_inicial.'
                                            </td>
                                            <td class="texto_add">'.$wtiempo_final.'</td>
                                            <td class="texto_add">'.$wnombre_concepto.'</td>
                                            <td class="texto_add">'.$wnombre_procedimiento.'</td>
                                            <td class="texto_add" align="center">
                                                <img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Quitar de lista" onclick="eliminarFilaRango(\'cobro_uso\',\'tr_rango_de_rangos_'.$id_unico.'\',\''.$identifica_concepto.'\',\'tr_rango_de_rangos_\',\''.$dif_rango.'\');">
                                            </td>
                                        </tr>';*/
                        $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'eliminar_grupo_excluido':
                        $arr_politica = unserialize(base64_decode($arr_politica));
                        // $id_unico = str_replace("*", "nxn", $id_unico);

                        $index_limit = $wlimit_inicial.'_'.$wlimit_final;
                        if(array_key_exists("excluidos", $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]))
                        {
                            if(array_key_exists($codigo_excluido, $arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"]))
                            {
                                // Elimina el codigo de un grupo de material o medicamento que esta contenido dentro del rango de UVRs
                                unset($arr_politica[$wcodigo_concepto]['limite_modalidad'][$index_limit]["excluidos"][$codigo_excluido]);
                            }
                        }
                        $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'listarPlantillasGuardadas':
                    // $arr_liquidacion = array_base_liquidacion($conex, $wbasedato);
                    $data['html'] = listarPlantillasGuardadas($conex, $wemp_pmla, $wbasedato); //, $arr_liquidacion
                    break;

                case 'listar_procedimientos':
                        $arr_procedimientos         = unserialize(base64_decode($arr_procedimientos));
                        $data["html_procedimientos"] = listaProcedimientos($wprocedimiento_list, $arr_procedimientos, $num_politica);

                        $data["primer_procedimiento_cod"] = "";
                        $data["primer_procedimiento_nom"] = "";
                        if(!empty($wprocedimiento_list))
                        {
                            $expl = explode(",", $wprocedimiento_list);
                            $data["primer_procedimiento_cod"] = $expl[0];
                            $data["primer_procedimiento_nom"] = (array_key_exists($expl[0], $arr_procedimientos)) ? $arr_procedimientos[$expl[0]] : '';
                        }
                    break;

                case 'agregar_procedimiento_lista':
                        $arr_procedimientos         = unserialize(base64_decode($arr_procedimientos));
                        $data["html_procedimientos"] = listaProcedimientos($codigo_procedimiento, $arr_procedimientos, $codigo_plantilla);
                    break;

                default:
                        $data['mensaje'] = utf8_encode($no_exec_sub);
                        $data['error'] = 1;
                    break;
            }
            echo json_encode($data);
            break;

        case 'delete' :
            switch ($form) {
                case 'eliminar_concepto':
                    $arr_politica = unserialize(base64_decode($arr_politica));
                    unset($arr_politica[$wconcepto]);
                    $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'eliminar_rango_concepto':
                    $arr_politica = unserialize(base64_decode($arr_politica));
                    unset($arr_politica[$identifica_concepto][$tipo_cobro][$dif_rango]);
                    $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'eliminar_limite':
                    // Elimina un límite de UVR's en caso que tenga configurado este tipo de dato
                    $arr_politica = unserialize(base64_decode($arr_politica));
                    unset($arr_politica[$identifica_concepto][$tipo_cobro][$dif_rango]);
                    $data['arr_politica'] = base64_encode(serialize($arr_politica));
                    break;

                case 'eliminar_plantilla':
                    $sql = "DELETE FROM {$wbasedato}_000180 WHERE Encpol = '$num_politica';";
                    $result = mysql_query($sql,$conex) OR die ("Eliminar 000180>$num_politica 'eliminar plantilla':".$sql.' <br> '.mysql_error());
                    $sql = "DELETE FROM {$wbasedato}_000181 WHERE Cphpol = '$num_politica';";
                    $result = mysql_query($sql,$conex) OR die ("Eliminar 000181>$num_politica 'eliminar plantilla':".$sql.' <br> '.mysql_error());
                    $sql = "DELETE FROM {$wbasedato}_000182 WHERE Anepol = '$num_politica';";
                    $result = mysql_query($sql,$conex) OR die ("Eliminar 000182>$num_politica 'eliminar plantilla':".$sql.' <br> '.mysql_error());
                    $sql = "DELETE FROM {$wbasedato}_000183 WHERE Usopol = '$num_politica';";
                    $result = mysql_query($sql,$conex) OR die ("Eliminar 000183>$num_politica 'eliminar plantilla':".$sql.' <br> '.mysql_error());
                    $sql = "DELETE FROM {$wbasedato}_000195 WHERE Plcpol = '$num_politica';";
                    $result = mysql_query($sql,$conex) OR die ("Eliminar 000195>$num_politica 'eliminar plantilla':".$sql.' <br> '.mysql_error());
                    break;

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
                            insertLog($conex, $wbasedato, $user_session, $accion, $form, 'error_sql', $descripcion, $wfuente.'-'.$worden, $query);
                            $data['mensaje'] = 'No se pudo eliminar la observación.';
                            $data['error'] = 1;
                        }
                        $data['debug_log'] = utf8_encode(debug_log_inline());
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
$wbasedato_HCE = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$grupo_anestesia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipos_anestesia'); // Consulta el código del grupo que corresponde a tipos de anestesia en maestro HCE

/***** Procedimientos *****/
$arr_procedimientos = obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato);
/***** Conceptos *****/
$arr_conceptos = obtener_array_conceptos();
/***** Entidades *****/
$arr_entidades = obtener_array_entidades();
/***** Tarifas *****/
$arr_tarifas = Obtener_array_tarifas();
/***** ESPECIALIDADES *****/
$arr_especialidades = obtener_array_especialidades_fnLocal($conex, $wemp_pmla, $caracter_ok, $caracter_ma);


/***** Grupos materiales *****/
$arr_grupos_materiales = obtener_grupos_materiales($conex, $wemp_pmla, $wbasedato);

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

/***** BASE DE LIQUIDACION *****/
// $arr_liquidacion = array_base_liquidacion($conex, $wbasedato);

$lista_plantillas = listarPlantillasGuardadas($conex, $wemp_pmla, $wbasedato); //, $arr_liquidacion

$arr_politica = base64_encode(serialize(reiniciarDatosSesion()));

// $guardar = "arr_especialidades: ".print_r($arr_especialidades, true).PHP_EOL;
// seguimiento($guardar);

?>
<html lang="es-ES">
<head>
    <title>Políticas Cirugía</title>
    <meta charset="utf-8">

    <!-- Librería para compatibilidad HTML5 con varios navegadores -->
    <!--
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>

    <script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />

    <link href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.easyAccordion.js" type="text/javascript"></script>

    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script> -->


    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
    <script src="../../../include/ips/funcionInsumosqxERP.js" type="text/javascript"></script>

    <link rel="stylesheet" href="../../../include/ips/facturacionERP.css">
    <script src="../../../include/root/toJson.js" type="text/javascript"></script>
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>

    <!--<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript">-->


    <script type="text/javascript">
        var regExDecimal = /(^[0]{1}\.{1}[0-9]+$)|(^\d+\.{1}[0-9]+$)|(^\d+$)|(^[0]$)/;

        $(document).on('mousemove', function(e){
            $('.caja_flotante').css({
               left:  e.pageX+12,
               top:   e.pageY
            });
        });

        function iniciarTabsCX(identifica_seccion, tipo_cobro)
        {
            /*---------------------------------
            Tabs
            -----------------------------------*/
            // tab setup
            if(tipo_cobro == '')
            {
                $("#div_concepto_agregado_"+identifica_seccion).find('.tab-content').addClass('clearfix').not(':first').hide();
            }
            else
            {
                $("#div_concepto_agregado_"+identifica_seccion).find('.tab-content').addClass('clearfix').not('.current').hide();
            }

            $("#div_concepto_agregado_"+identifica_seccion).find('ul.pestania').each(function(){
                var current = $(this).find('li.current');
                if(tipo_cobro == '')
                {
                    if(current.length < 1) { $(this).find('li:first').addClass('current'); }
                }
                current = $(this).find('li.current a').attr('tabLink'+identifica_seccion);
                $(current).show();
            });

            // tab click
            $(document).on('click', 'ul.pestania a[tabLink'+identifica_seccion+'^="#"]', function(e){
                e.preventDefault();
                var pestania = $(this).parents('ul.pestania').find('li');
                var tab_next = $(this).attr('tabLink'+identifica_seccion);
                var tab_current = pestania.filter('.current').find('a').attr('tabLink'+identifica_seccion); // la que se ve actualmenete

                // var tab = pestania.find('a').attr('tabLink'+identifica_seccion); // Hacia donde debería ir, donde se dió clic
                // console.log(tab_next+' - '+tab+' - '+tab_current);
                /*if(tab_next != tab_current)
                {
                    if(confirm('La configuración en este tipo de cobro se reinicia si cambia a otro tipo de cobro.'))*/
                    {
                        $(tab_current).hide();
                        pestania.removeClass('current');
                        pestania.css("background-color","");
                        $(this).parent().addClass('current');
                        $(this).parent().css({"background-color":"#ffffff"});
                        $(tab_next).show();
                        //history.pushState( null, null, window.location.search + $(this).attr('tabLink') );
                        // $("#cambiar_tak_ok_"+identifica_seccion).val('ok');
                        return false;
                    }
                    /*else
                    {
                        // $("#cambiar_tak_ok_"+identifica_seccion).val('');
                        var pestania = $(this).parents('ul.pestania').find('li');
                        var tab_current = pestania.filter('.current').find('a').attr('tabLink'+identifica_seccion);
                        $(tab_current).click();
                        return false;
                    }
                }
                else
                {
                    var pestania = $(this).parents('ul.pestania').find('li');
                    var tab_current = pestania.filter('.current').find('a').attr('tabLink'+identifica_seccion);
                    $(tab_current).click();
                    return false;
                }*/
            });
            // tab hashtag identification and auto-focus
           /* var wantedTag = window.location.hash;
            if (wantedTag != "")
            {
                // This code can and does fail, hard, killing the entire app.
                // Esp. when used with the jQuery.Address project.
                try {
                    var allTabs = $("ul.pestania a[tabLink^=" + wantedTag + "]").parents('ul.pestania').find('li');
                    var defaultTab = allTabs.filter('.current').find('a').attr('tabLink');
                    $(defaultTab).hide();
                    allTabs.removeClass('current');
                    $("ul.pestania a[tabLink^=" + wantedTag + "]").parent().addClass('current');
                    $("#" + wantedTag.replace('#','')).show();
                } catch(e) {
                    // I have no idea what to do here, so I'm leaving this for the maintainer.
                }
            }*/
        }

        /*function validarCambioPestana(ele)
        {
            console.log($("#"+ele).val());
            if($("#"+ele).val() == 'ok') { $("#"+ele).val(''); return true; }
            return false;

            // var tab_next = $("#"+ele).find('a[tabLink'+identifica_seccion+'^="#"]').attr('tabLink'+identifica_seccion);
            // var tab_current = pestania.filter('.current').find('a').attr('tabLink'+identifica_seccion); // la que se ve actualmenete

            // // var tab = pestania.find('a').attr('tabLink'+identifica_seccion); // Hacia donde debería ir, donde se dió clic
            // // console.log(tab_next+' - '+tab+' - '+tab_current);
            // if(tab_next != tab_current)
            // {
            //     return true
            // }
        }*/

        // Inicializar primer acordeón
        $(function(){
            $( "#div_datos_basicos" ).attr( "acordeon", "" );
            $("#div_datos_basicos").accordion({
                 collapsible: true
                ,heightStyle: "content"
                //,active: -1
            });
        });


        function iniciarNotificacion()
        {
            $('#contenedor_programa_politicas_cx').append('<div id="notificacion"><span></span></div>');
            $(document).on('click', '.notificar', function() {
                var $boton = $(this);
                var icono = $boton.attr('data-icono');
                var mensaje = $boton.attr('data-mensaje');

                var $notificacion = $('#notificacion');
                $notificacion.attr('class', icono);
                $notificacion.find('span').text(mensaje);
                $notificacion.stop().animate({top:'0'});

                setTimeout(function() {
                    //$notificacion.removeClass('mostrar');
                    $notificacion.stop().animate({top:'-30px'});
                }, 2000);
            });
        }

        /***Limitar tamaño texto***/

        jQuery(document).ready(function(){

            $('.texto-cortado').each(function(){

            var longitud=25;

            if($(this).text().length > longitud){

                var texto=$(this).text().substring(0,longitud);
                var indiceUltimoEspacio= texto.lastIndexOf(' ');
                texto=texto.substring(0,indiceUltimoEspacio) +'<span class="puntos">...</span>';

                var primeraParte = '<span class="texto-mostrado">' + texto + '</span>';
                var segundaParte = '<span class="texto-ocultado" style="display:none;">' + $(this).text().substring(indiceUltimoEspacio,$(this).text().length - 0) + '</span>';

                $(this).html(primeraParte + segundaParte);
                $(this).after('<span class="boton_mas_info"><img width="10 " height="10" border="0" src="../../images/medical/hce/mas.PNG"></span>');

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
                    $(this).html('<img width="10" height="10" border="0" src="../../images/medical/hce/mas.PNG">');
                };
            });

            iniciarSearch();
        });

        function iniciarSearch()
        {
            $('input#id_search_platilla').quicksearch('#tabla_politicas .find');
        }

        function reiniciarDatos()
        {
            $("#num_politica").html("--");
            $("#wcodigo_politica").val("");
            $("#wnombre_plantilla").val("");
            $("#wtipoempresa").val("*");
            $("#wespecialidad").val("*");
            $("#div_contenedor_conceptos").html("");
            $("#arr_politica").val($("#arr_politica_vacio").val());
            /*$("#wbaseliquidacion").val("");*/
            reiniciarCamposEncabezado();
        }


        function reiniciarCamposEncabezado()
        {
            /**>> Autocompletar "procedimientos" **/
            crearAutocomplete('arr_procedimientos', 'wprocedimiento','','');
            $("#div_wprocedimientos_list").html("");
            $("#div_wprocedimientos_list").removeClass("campoRequerido");

            /**>> Autocompletar "entidades" **/
            crearAutocomplete('arr_entidades', 'wentidad','*','TODOS');

            /**>> Autocompletar "tarifas" **/
            crearAutocomplete('arr_tarifas', 'wtarifa','*','TODOS');

            /**>> Autocompletar "conceptos" **/
            crearAutocomplete('arr_conceptos', 'wnuevo_concepto_gral','','');
        }

        $(document).ready( function () {
            // iniciarTabsCX();
            simularPlaceHolder();

            // iniciarNotificacion();
            reiniciarCamposEncabezado();
        });

        function crearAutocomplete(arr_opciones_seleccion, campo_autocomplete, codigo_default, nombre_default)
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
                arr_datos[index] = {};
                arr_datos[index].value  = CodVal+'-'+datos[CodVal];
                arr_datos[index].label  = CodVal+'-'+datos[CodVal];
                arr_datos[index].codigo = CodVal;
                arr_datos[index].nombre = CodVal+'-'+datos[CodVal];
            }
            // console.log(arr_datos);
            $("#"+campo_autocomplete).autocomplete({
                    source: arr_datos, minLength : 0,
                    select: function( event, ui ) {
                                // Lee el valor seleccionado en el autocompletar y lee solo el código y lo adiciona a otro campo de solo código.
                                var cod_sel = ui.item.codigo;
                                var nom_sel = ui.item.nombre;
                                $("#"+campo_autocomplete).attr("codigo",cod_sel);
                                $("#"+campo_autocomplete).attr("nombre",nom_sel);
                                // cargarConceptosPorProcedimientos(cod_sel);


                                if(campo_autocomplete == "wprocedimiento")
                                {
                                    // Crear html para agregar
                                    adicionarProcedimientoLista(cod_sel, $("#wcodigo_politica").val());
                                }
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
                        return true;
                    }
                    return false;
                }
            });

            $('#wprocedimiento, #wnuevo_concepto_gral, #wentidad, #wtarifa').on({
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

        function adicionarProcedimientoLista(cod_sel, codigo_plantilla)
        {
            // var codigo_procedimiento = $("#div_lista_procedimientos_anulan_"+id_manual+"_"+id_relacion).attr("codigo");
            var cod_proced = cod_sel;

            if(cod_sel == '*')
            {
                cod_proced = 'n';
            }
            if($("#div_wprocedimientos_list").find("#div_wprocedimiento_"+cod_proced).length == 0)
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion               : 'load',
                        form                 : 'agregar_procedimiento_lista',
                        consultaAjax         : '',
                        codigo_plantilla     : codigo_plantilla,
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
                            // if($("#div_check_clase_"+windentificador+'_'+cod_proced).length == 0)
                            // {
                                $("#div_wprocedimientos_list").append(data.html_procedimientos);
                            // }
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                    var cont = 0;
                    $("#div_wprocedimientos_list").find("div[id^=div_wprocedimiento_]").each(function(){
                        var colorbg = (cont % 2 == 0) ? "#<?=COLOR_GRIS1?>" : "#<?=COLOR_GRIS2?>";
                        $(this).css("background-color","#"+colorbg+"");
                        cont++;
                    });
                });
            }
            else
            {
                alert("Ya existe el procedimiento en la lista, verifique si está seleccionado");
            }
            $("#wprocedimiento").val("");
            $("#wprocedimiento").attr("codigo","");
            $("#wprocedimiento").attr("nombre","");
            $("#div_wprocedimientos_list").removeClass("campoRequerido");
        }

        /**
            Esta función carga en la vista una plantilla que ya esta guardada en la base de datos, se usa por ejemplo para cargar plantillas
            de las lista de politicas configuradas que aparece al inicio del formulario al darle clic a una de las filas.
        */
        // var control_crear_concepto_bd = true;
        function cargarPlantilla(this_btn, num_politica, wprocedimiento_list, wentidad, wtarifa, wtipoempresa, wnombre_plantilla, wespecialidad)
        {
            if($(".cargando").length > 0)
            {
                // Si ya hay uno cargando entonces no haga nada hasta que termine
                return;
            }

            $(".btn_carga_pl").attr("disabled","disabled");
            $(this_btn).addClass("cargando");
            $(this_btn).html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >');
            $("#div_contenedor_conceptos").html("");

            // $("#control_crear_concepto_bd").val('on');
            var wprocedimiento = '';
            var wprocedimiento_nombre = '';

            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion              : 'load',
                    form                : 'listar_procedimientos',
                    consultaAjax        : '',
                    wprocedimiento_list : wprocedimiento_list,
                    arr_procedimientos  : $("#arr_procedimientos64").val(),
                    num_politica        : num_politica
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        wprocedimiento = data.primer_procedimiento_cod;
                        wprocedimiento_nombre = data.primer_procedimiento_nom;
                        $("#div_wprocedimientos_list").html(data.html_procedimientos);
                    }
                },
                "json"
            ).done(function(){
                $("#arr_politica").val($("#arr_politica_"+num_politica).val());
                $("#num_politica").html(num_politica);
                $("#wcodigo_politica").val(num_politica);

                $("#wnombre_plantilla").val(wnombre_plantilla);
                // iniciarCamposValoresAutocomplete("arr_procedimientos", "wprocedimiento", '');
                iniciarCamposValoresAutocomplete("arr_entidades", "wentidad", wentidad);
                iniciarCamposValoresAutocomplete("arr_tarifas", "wtarifa", wtarifa);
                $("#wtipoempresa").val(wtipoempresa);
                $("#wespecialidad").val(wespecialidad);
                // var wprocedimiento_nombre = $("#wprocedimiento").attr('nombre');

                // $("#wbaseliquidacion").val(wbaseliquidacion);

                // arr_conceptos_pol Conceptor de la plantilla preparados para cargar al formulario, "arr_conceptos_pol_.." conceptor de la plantilla que se deben cargar
                $("#arr_conceptos_pol").val($("#arr_conceptos_pol_"+num_politica).val());
                $("#arr_conceptos_pol_b64").val($("#arr_conceptos_pol_b64_"+num_politica).val());
                // console.log($("#arr_conceptos_pol_b64_"+num_politica).val());

                var arr_json_options = eval('(' + $("#arr_conceptos").val() + ')');
                var arr_conceptos_pol = eval('(' + $("#arr_conceptos_pol_"+num_politica).val() + ')');
                var concepto_nombre = '';
                for (var CodVal in arr_conceptos_pol)
                {
                    var codigo_concepto = CodVal;
                    var concepto_nombre = arr_json_options[CodVal];
                    var tipo_cobro = arr_conceptos_pol[CodVal];
                    // console.log(concepto_nombre+' - '+CodVal+' - '+arr_conceptos_pol[CodVal]);

                    /*while($("#control_crear_concepto_bd").val() != 'on')
                    {
                        // Se esjecuta mientras el concepto anterior no se haya creado por completo (la respuesta ajax se complete).
                        sleep(1000);
                    }*/
                    agregarConcepto('div_contenedor_conceptos', codigo_concepto, concepto_nombre, wprocedimiento, wprocedimiento_nombre, tipo_cobro, 'cargar_de_la_bd');
                    break;
                }
            });

        }


        function eliminarPlantilla(this_btn, num_politica, wnombre_plantilla)
        {
            if($(".cargando").length > 0)
            {
                // Si ya hay uno cargando entonces no haga nada hasta que termine
                return;
            }

            if(confirm("Va a eliminar plantilla ["+wnombre_plantilla+"] del sistema \n¿Quiere continuar?"))
            {
                $(".btn_carga_pl").attr("disabled","disabled");
                $(this_btn).addClass("cargando");
                $(this_btn).html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >');

                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion       : 'delete',
                        form         : 'eliminar_plantilla',
                        consultaAjax : '',
                        num_politica : num_politica
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                           // $("#log_error").html(data.log_error);
                            alert(data.mensaje);
                        }
                        else
                        {
                            var this_btn = $(".cargando");
                            $(this_btn).removeClass("cargando");
                            $(this_btn).html("Cargar");
                            $(".btn_carga_pl").removeAttr("disabled");
                        }
                        return data;
                    },
                    "json"
                ).done(function(data){
                    reiniciarDatos();
                    recargarListadoPlantillas();
                });
            }
        }

        function sleep(milliseconds) {
          var start = new Date().getTime();
          for (var i = 0; i < 1e7; i++) {
            if ((new Date().getTime() - start) > milliseconds){
              break;
            }
          }
        }

        function iniciarCamposValoresAutocomplete(arr_options, campo_auto, codigo)
        {
            var arr_json_options = eval('(' + $("#"+arr_options).val() + ')');
            $("#"+campo_auto).val(codigo+'-'+arr_json_options[codigo]);
            $("#"+campo_auto).attr("nombre",arr_json_options[codigo]);
            $("#"+campo_auto).attr("codigo",codigo);
        }

        function cargarConceptosPorProcedimiento(cod_procedimiento)
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion            : 'load',
                    form              : 'conceptos_por_procedimiento',
                    consultaAjax      : '',
                    cod_procedimiento : cod_procedimiento
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#"+contenedor).append(data);
                    }
                },
                "json"
            ).done(function(){
            });
        }


        /**
            load_bd: Es un parámetro que indica si los datos del concepto a agregar se deben leer de la base de datos o simplemente debe crear una sección
            de nuevo concepto sin configuración y lista para llenar datos.
        */
        function agregarConcepto(contenedor, wnuevo_concepto_gral, wnuevo_concepto_gral_nombre, wprocedimiento, wprocedimiento_nombre, tipo_cobro, load_bd)
        {
            // $("#control_crear_concepto_bd").val('off');
            var tipo_cobro_fn = tipo_cobro;
            var wcodigo_politica       = $("#wcodigo_politica").val();
            var identifica_concepto    = (wnuevo_concepto_gral != '')           ? wnuevo_concepto_gral:         $("#wnuevo_concepto_gral").attr('codigo');
            // var wprocedimiento         = '';
            var nombre_procedimiento   = '';
            if(wprocedimiento == '' && $('#div_wprocedimientos_list').find('input[type=checkbox]').filter(':checked').length > 0)
            {
                wprocedimiento       = $('#div_wprocedimientos_list').find('input[type=checkbox]').filter(':checked:first').val();
                nombre_procedimiento = $('#div_wprocedimientos_list').find('input[type=checkbox]').filter(':checked:first').attr("nombre");
            }

            var wnuevo_concepto_gral   = (wnuevo_concepto_gral != '')           ? wnuevo_concepto_gral:         $("#wnuevo_concepto_gral").attr('codigo');
            var nombre_concepto        = (wnuevo_concepto_gral_nombre != '')    ? wnuevo_concepto_gral_nombre:  $("#wnuevo_concepto_gral").attr('nombre');

            var arr_conceptos_pol = "";
            arr_conceptos_pol = $("#arr_conceptos_pol_b64").val();
            // este array tiene los posible campos a cargarle a una plantilla de las que se quiere editar, el que no termina con 654 se usa para recorrerlo en javascript


            if(identifica_concepto.replace(/ /gi, "") != '' && $("#div_concepto_agregado_"+identifica_concepto).length == 0)
            {
                url_add_params = addUrlCamposCompartidosTalento();
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                 : 'load',
                        form                   : 'agregar_nuevo_concepto',
                        consultaAjax           : '',
                        arr_politica           : $("#arr_politica").val(),
                        wcodigo_politica       : wcodigo_politica,
                        identifica_concepto    : identifica_concepto,
                        wnuevo_concepto_gral   : wnuevo_concepto_gral,
                        nombre_concepto        : nombre_concepto,
                        wprocedimiento         : wprocedimiento,
                        nombre_procedimiento   : nombre_procedimiento,
                        wgrupo_tipos_anestesia : $("#wgrupo_tipos_anestesia").val(),
                        wbasedato_HCE          : $("#wbasedato_HCE").val(),
                        load_bd                : load_bd,
                        tipo_cobro             : tipo_cobro_fn,
                        arr_conceptos_pol      : arr_conceptos_pol
                    },
                    function(data){
                       /* if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else*/
                        {
                            if(data == 'inventario') { alert("Conceptos de inventario no se configuran en plantillas."); }
                            else
                            {
                                $("#"+contenedor).append(data);
                                $("#wnuevo_concepto_gral").attr('codigo','');
                                $("#wnuevo_concepto_gral").attr('nombre','');
                                $("#wnuevo_concepto_gral").val('');
                                $("#wcantidad_concepto").val('1');

                                if(identifica_concepto.replace(/ /gi, "") != '')
                                {
                                    aplicarAcordeon('div_concepto_agregado_'+identifica_concepto, wcodigo_politica);
                                    iniciarTabsCX(identifica_concepto, tipo_cobro_fn);
                                }
                                // console.log($("#arr_politica_temp").val());
                                $("#arr_politica").val($("#arr_politica_temp").val());
                                $("#arr_politica_temp").remove();

                                $("#arr_conceptos_pol").val($("#arr_conceptos_pol_temp").val());
                                $("#arr_conceptos_pol_temp").remove();
                                // console.log($("#arr_conceptos_pol").val());

                                $("#arr_conceptos_pol_b64").val($("#arr_conceptos_pol_b64_temp").val());
                                $("#arr_conceptos_pol_b64_temp").remove();

                                if($("#arr_conceptos_pol").val() != '' && $("#arr_conceptos_pol").val() != '[]')
                                {
                                    // Esta sección se ejecuta si encuentra que todavía faltan conceptos para mostrar de una politica que se carga de la lista
                                    // En cada iteración se quita un concepto del array "arr_conceptos_pol_"
                                    var arr_json_options = eval('(' + $("#arr_conceptos").val() + ')');
                                    var arr_conceptos_pol = eval('(' + $("#arr_conceptos_pol").val() + ')');
                                    for (var CodVal in arr_conceptos_pol)
                                    {
                                        var codigo_concepto = CodVal;
                                        var concepto_nombre = arr_json_options[CodVal];
                                        var tipo_cobro = arr_conceptos_pol[CodVal];
                                        // console.log(concepto_nombre+' - '+CodVal+' - '+arr_conceptos_pol[CodVal]);

                                        agregarConcepto('div_contenedor_conceptos', codigo_concepto, concepto_nombre, wprocedimiento, wprocedimiento_nombre, tipo_cobro, 'cargar_de_la_bd');
                                        break;
                                    }
                                }
                                else
                                {
                                    var this_btn = $(".cargando");
                                    $(this_btn).removeClass("cargando");
                                    $(this_btn).html("Cargar");
                                    $(".btn_carga_pl").removeAttr("disabled");
                                }
								
								//JERSON
								aplicarRangoTiempoCadaPro = $("#aplicarRangoTiempoCadaPro_"+identifica_concepto).val();
								$("[tiempo_Aplicar][concepto="+identifica_concepto+"] option[value="+aplicarRangoTiempoCadaPro+"]").attr('selected', true);
								
                            }
                        }
                    }
                ).done(function(){

                });
            }
            else if($("#div_concepto_agregado_"+identifica_concepto).length > 0)
            {
                alert("Ya existe un concepto igual");
            }
        }

        function eliminarConceptoView(identifica_concepto)
        {
            $("#div_concepto_agregado_"+identifica_concepto).hide("slow",
                                    function(){
                                        $(this).remove();
                                });
        }

        function eliminarConcepto(wconcepto)
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion       : 'delete',
                    form         : 'eliminar_concepto',
                    consultaAjax : '',
                    arr_politica : $("#arr_politica").val(),
                    wconcepto    : wconcepto
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#arr_politica").val(data.arr_politica);
                    }
                },
                "json"
            ).done(function(){
            });
        }

        function nuevoRangoAnestesia(id_tabla, identifica_concepto)
        {
            var validar_config = true; // validar si hay info en otros tipos de cobro de este concepto
            validar_config = validar_cambio_TipoCobro(identifica_concepto);

            if(validar_config)
            {
                var campos_anestesia_ok = validarRequeridos('tabla_cont_rangos_anestesia_'+identifica_concepto);

                // Validar que los valores de rangos no se superen
                var t_inicial = $("#wtiempo_inicial_anest_"+identifica_concepto);
                var t_final   = $("#wtiempofinal_anest_"+identifica_concepto);
                if(!validar_cifra_decimal(t_inicial))
                {
                    campos_anestesia_ok = false;
                }

                if(!validar_cifra_decimal(t_final))
                {
                    campos_anestesia_ok = false;
                }
                if(parseFloat(t_inicial.val()) >= parseFloat(t_final.val()))
                {
                    campos_anestesia_ok = false;
                    $("#wtiempo_inicial_anest_"+identifica_concepto+",#wtiempofinal_anest_"+identifica_concepto).addClass("campoRequerido");
                    alert("El rango de tiempos no es correcto");
                }

                // Para validar que no existe un rango con los mismo parámetros.
                var wtipo_anestesia       = $("#wtipo_anestesia_"+identifica_concepto).val();
                var wtiempo_inicial_anest = $("#wtiempo_inicial_anest_"+identifica_concepto).val();
                var wtiempofinal_anest    = $("#wtiempofinal_anest_"+identifica_concepto).val();
                var wcodigo_concepto      = $("#wcodigo_concepto_anest_"+identifica_concepto).attr('codigo');
                var wcodigo_procedimiento = $("#wcodigo_procedimiento_anest_"+identifica_concepto).attr('codigo');
                var tr_id_buscar = wtipo_anestesia+'_'+wtiempo_inicial_anest+'_'+wtiempofinal_anest+'_'+wcodigo_concepto+'_'+wcodigo_procedimiento;
                tr_id_buscar = tr_id_buscar.replace(/\*/g,"nxn");

                tr_id_buscar = "tr_rango_tipo_anestesia_"+identifica_concepto+'_'+tr_id_buscar;

                if($("#"+tr_id_buscar).length > 0)
                {
                    campos_anestesia_ok = false;
                    alert("ya existe un rango con los mismo parámetros");
                }

                if(campos_anestesia_ok)
                {
                    $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                        {
                            accion                : 'load',
                            form                  : 'nuevo_rango_anestesia',
                            consultaAjax          : '',
                            arr_politica          : $("#arr_politica").val(),
                            id_tabla              : id_tabla,
                            identifica_concepto   : identifica_concepto,
                            wtipo_anestesia       : $("#wtipo_anestesia_"+identifica_concepto).val(),
                            wnombre_anestesia     : $("#wtipo_anestesia_"+identifica_concepto+" option:selected").text(),
                            wtiempo_inicial_anest : $("#wtiempo_inicial_anest_"+identifica_concepto).val(),
                            wtiempofinal_anest    : $("#wtiempofinal_anest_"+identifica_concepto).val(),
                            wcodigo_concepto      : $("#wcodigo_concepto_anest_"+identifica_concepto).attr('codigo'),
                            wnombre_concepto      : $("#wcodigo_concepto_anest_"+identifica_concepto).attr('nombre'),
                            wcodigo_procedimiento : $("#wcodigo_procedimiento_anest_"+identifica_concepto).attr('codigo'),
                            wnombre_procedimiento : $("#wcodigo_procedimiento_anest_"+identifica_concepto).attr('nombre')
                        },
                        function(data){
                            if(isset(data.error) && data.error == 1)
                            {
                                alert(data.mensaje);
                            }
                            else
                            {
                                $("#arr_politica").val(data.arr_politica);
                                $("#"+id_tabla).append(data.html);
                                $("#wtipo_anestesia_"+identifica_concepto).val('');
                                $("#wtiempo_inicial_anest_"+identifica_concepto).val('');
                                $("#wtiempofinal_anest_"+identifica_concepto).val('');
                                $("#wcodigo_procedimiento_anest_"+identifica_concepto).val('');

                                // $("#wcodigo_concepto_anest_"+identifica_concepto).attr('codigo','');
                                // $("#wcodigo_concepto_anest_"+identifica_concepto).attr('nombre','');
                                // $("#wcodigo_concepto_anest_"+identifica_concepto).val('');
                                // $("#wcodigo_procedimiento_anest_"+identifica_concepto).attr('codigo','');
                                // $("#wcodigo_procedimiento_anest_"+identifica_concepto).attr('nombre','');
                                // $("#wcodigo_procedimiento_anest_"+identifica_concepto).val('');
                            }
                        },
                        "json"
                    ).done(function(){
                        resetStylePrefijo("tr_rango_tipo_anestesia_"+identifica_concepto);
                    });
                }
            }
        }

        function nuevoRangoPorRangos(id_tabla, identifica_concepto)
        {
            var validar_config = true; // validar si hay info en otros tipos de cobro de este concepto
            validar_config = validar_cambio_TipoCobro(identifica_concepto);

            if(validar_config)
            {
                var campos_uso_ok = validarRequeridos('tabla_rangos_uso_'+identifica_concepto);

                // Validar que los valores de rangos no se superen
                var t_inicial = $("#wtiempo_inicial_por_rangos_"+identifica_concepto);
                var t_final   = $("#wtiempo_final_por_rangos_"+identifica_concepto);
                if(!validar_cifra_decimal(t_inicial))
                {
                    campos_uso_ok = false;
                }

                if(!validar_cifra_decimal(t_final))
                {
                    campos_uso_ok = false;
                }

                if(parseFloat(t_inicial.val()) >= parseFloat(t_final.val()))
                {
                    campos_uso_ok = false;
                    $("#wtiempo_inicial_por_rangos_"+identifica_concepto+",#wtiempo_final_por_rangos_"+identifica_concepto).addClass("campoRequerido");
                }

                // Para validar que no existe un rango con los mismo parámetros.
                var wtiempo_inicial       = $("#wtiempo_inicial_por_rangos_"+identifica_concepto).val();
                var wtiempo_final         = $("#wtiempo_final_por_rangos_"+identifica_concepto).val();
                var wcodigo_concepto      = $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).attr('codigo');
                var wcodigo_procedimiento = $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).attr('codigo');
                var tr_id_buscar = wtiempo_inicial+'_'+wtiempo_final+'_'+wcodigo_concepto+'_'+wcodigo_procedimiento;
                tr_id_buscar = tr_id_buscar.replace(/\*/g,"nxn");

                tr_id_buscar = "tr_rango_de_rangos_"+identifica_concepto+'_'+tr_id_buscar;

                if($("#"+tr_id_buscar).length > 0)
                {
                    campos_uso_ok = false;
                    alert("ya existe un rango con los mismo parámetros");
                }

                if(campos_uso_ok)
                {
                    $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                        {
                            accion                : 'load',
                            form                  : 'nuevo_rango_de_rangos',
                            consultaAjax          : '',
                            arr_politica          : $("#arr_politica").val(),
                            id_tabla              : id_tabla,
                            identifica_concepto   : identifica_concepto,
                            wtiempo_inicial       : $("#wtiempo_inicial_por_rangos_"+identifica_concepto).val(),
                            wtiempo_final         : $("#wtiempo_final_por_rangos_"+identifica_concepto).val(),
                            wcodigo_concepto      : $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).attr('codigo'),
                            wnombre_concepto      : $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).attr('nombre'),
                            wcodigo_procedimiento : $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).attr('codigo'),
                            wnombre_procedimiento : $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).attr('nombre')
                        },
                        function(data){
                            if(isset(data.error) && data.error == 1)
                            {
                                alert(data.mensaje);
                            }
                            else
                            {
                                $("#arr_politica").val(data.arr_politica);
                                $("#"+id_tabla).append(data.html);
                                $("#wtiempo_inicial_por_rangos_"+identifica_concepto).val('');
                                $("#wtiempo_final_por_rangos_"+identifica_concepto).val('');
                                $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).val('');

                                // $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).attr('codigo','');
                                // $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).attr('nombre','');
                                // $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).val('');

                                // $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).attr('codigo','');
                                // $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).attr('nombre','');
                                // $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).val('');
                            }
                        },
                        "json"
                    ).done(function(){
                        resetStylePrefijo("tr_rango_de_rangos_"+identifica_concepto);
                    });
                }
            }
        }

        function nuevoLimite(id_tabla, identifica_concepto, codigo_concepto, nombre_concepto)
        {
            $("#limit_inf_"+identifica_concepto+",#limit_sup_"+identifica_concepto).removeClass("campoRequerido");
            var campos_limit_ok = validarRequeridos('tabla_crear_limite_'+identifica_concepto);

            // Validar que los valores de rangos no se superen
            var t_inicial = $("#limit_inf_"+identifica_concepto);
            var t_final   = $("#limit_sup_"+identifica_concepto);
            if(!validar_cifra_decimal(t_inicial))
            {
                campos_limit_ok = false;
            }

            if(!validar_cifra_decimal(t_final))
            {
                campos_limit_ok = false;
            }

            if(parseFloat(t_inicial.val()) >= parseFloat(t_final.val()))
            {
                campos_limit_ok = false;
                $("#limit_inf_"+identifica_concepto+",#limit_sup_"+identifica_concepto).addClass("campoRequerido");
                alert("El límite inferior no puede ser mayor o icual al límite superior");
            }

            // Para validar que no existe un rango con los mismo parámetros.
            var wlimit_inicial       = $("#limit_inf_"+identifica_concepto).val();
            var wlimit_final         = $("#limit_sup_"+identifica_concepto).val();
            // var wcodigo_concepto      = $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).attr('codigo');
            var wcodigo_procedimiento = $("#wprocedimiento_limit_"+identifica_concepto).attr('codigo');
            var wnombre_procedimiento = $("#wprocedimiento_limit_"+identifica_concepto).attr('nombre');
            var tr_id_buscar = 'tr_limites_'+identifica_concepto+'_'+wlimit_inicial+'_'+wlimit_final+'_'+codigo_concepto+'_'+wcodigo_procedimiento;
            var id_unico = identifica_concepto+'_'+wlimit_inicial+'_'+wlimit_final+'_'+codigo_concepto+'_'+wcodigo_procedimiento;

            tr_id_buscar = tr_id_buscar.replace(/\*/g,"nxn");

            if($("#"+tr_id_buscar).length > 0)
            {
                campos_limit_ok = false;
                alert("Ya existe un rango con los mismo parámetros");
            }

            if(campos_limit_ok)
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                : 'load',
                        form                  : 'nuevo_limite_excepciones',
                        consultaAjax          : '',
                        arr_politica          : $("#arr_politica").val(),
                        id_tabla              : id_tabla,
                        identifica_concepto   : identifica_concepto,
                        wtiempo_inicial       : wlimit_inicial,
                        wtiempo_final         : wlimit_final,
                        wcodigo_concepto      : codigo_concepto,
                        wnombre_concepto      : nombre_concepto,
                        wcodigo_procedimiento : wcodigo_procedimiento,
                        wnombre_procedimiento : wnombre_procedimiento
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#arr_politica").val(data.arr_politica);
                            $("#"+id_tabla).append(data.html);
                            $("#limit_inf_"+identifica_concepto).val('');
                            $("#limit_sup_"+identifica_concepto).val('');

                            // $("#wprocedimiento_limit_"+identifica_concepto).val('');

                            // $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).attr('codigo','');
                            // $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).attr('nombre','');
                            // $("#wcodigo_concepto_rango_por_rango_"+identifica_concepto).val('');

                            // $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).attr('codigo','');
                            // $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).attr('nombre','');
                            // $("#wcodigo_procedimiento_rango_por_rango_"+identifica_concepto).val('');
                        }
                    },
                    "json"
                ).done(function(){
                    resetStylePrefijo("tr_limites_"+identifica_concepto);
                    crearAutocomplete('arr_grupos_materiales', 'wbuscador_materiales_'+id_unico, '', '');
                });
            }
        }

        function adicionarExcluir(wbuscador_materiales,ul_excluidos, id_unico)
        {
            var buscar = $("#"+wbuscador_materiales).val();
            if(buscar.replace(/ /gi,"") != '')
            {
                var id_datos_rango = id_unico+'_hdn_limite_modalidad';
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                : 'load',
                        form                  : 'agregar_grupo_excluido',
                        consultaAjax          : '',
                        arr_politica          : $("#arr_politica").val(),
                        id_unico              : id_unico,
                        codigo_excluido       : $("#"+wbuscador_materiales).attr("codigo"),
                        nombre_excluido       : $("#"+wbuscador_materiales).attr("nombre"),
                        wlimit_inicial        : $("#"+id_datos_rango).attr("wlimit_inicial"),
                        wlimit_final          : $("#"+id_datos_rango).attr("wlimit_final"),
                        wcodigo_concepto      : $("#"+id_datos_rango).attr("wcodigo_concepto"),
                        wcodigo_procedimiento : $("#"+id_datos_rango).attr("wcodigo_procedimiento")
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#arr_politica").val(data.arr_politica);
                            $("#"+ul_excluidos).append(data.html);
                        }
                    },
                    "json"
                ).done(function(){
                    $("#"+wbuscador_materiales).val("");
                    $("#"+wbuscador_materiales).attr("codigo","");
                    $("#"+wbuscador_materiales).attr("nombre","");
                });
            }
        }

        function eliminarExcluido(limite_modalidad, li_excluido, id_unico, codigo_excluido)
        {
            // me falta mandar el codigo del grupo excluido a eliminar
            var id_datos_rango = id_unico+'_hdn_limite_modalidad';
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion                : 'load',
                        form                  : 'eliminar_grupo_excluido',
                        consultaAjax          : '',
                        arr_politica          : $("#arr_politica").val(),
                        id_unico              : id_unico,
                        wlimit_inicial        : $("#"+id_datos_rango).attr("wlimit_inicial"),
                        wlimit_final          : $("#"+id_datos_rango).attr("wlimit_final"),
                        wcodigo_concepto      : $("#"+id_datos_rango).attr("wcodigo_concepto"),
                        wcodigo_procedimiento : $("#"+id_datos_rango).attr("wcodigo_procedimiento"),
                        codigo_excluido       : codigo_excluido
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#arr_politica").val(data.arr_politica);
                        }
                    },
                    "json"
                ).done(function(){
                    $("#"+li_excluido).remove();
                });
        }

        function validar_cambio_TipoCobro(identifica_concepto)
        {
            var validar_config = true; // validar si hay info en otros tipos de cobro de este concepto
            if($("tr[id^=tr_rango_tipo_anestesia_"+identifica_concepto+"]").length != 0 && !$("tr[id^=tr_rango_tipo_anestesia_"+identifica_concepto+"]").is(':visible')) {
                // && !$("tr[id^=tr_rango_tipo_anestesia_"+identifica_concepto+"]").is(':visible')
                validar_config = false;
                alert("Si va a configurar un cobro diferente , debe borrar la configuracion de los otros tipos de cobro en este concepto.");
            }
            else if($("tr[id^=tr_rango_de_rangos_"+identifica_concepto+"]").length != 0 && !$("tr[id^=tr_rango_de_rangos_"+identifica_concepto+"]").is(':visible')) {
                // && !$("tr[id^=tr_rango_de_rangos_"+identifica_concepto+"]").is(':visible')
                validar_config = false;
                alert("Si va a configurar un cobro diferente , debe borrar la configuracion de los otros tipos de cobro en este concepto.");
            }
            else if(    (   $("#wtiempo_minimo_hora_"+identifica_concepto).val() != ''
                            || $("#wcobro_por_hora_"+identifica_concepto).is(":checked") )
                        && !$("#wtiempo_minimo_hora_"+identifica_concepto).is(":visible"))
            {
                validar_config = false;
                alert("Si va a configurar un cobro diferente , debe borrar la configuracion de los otros tipos de cobro en este concepto.");
            }
            // else if($("#wtiempo_minimo_hora_"+identifica_concepto).is(":visible"))
            // {
            //     validar_config = false;
            // }

            return validar_config;
        }

        /* Función para guardar temporalmente el tipo de cobro (crea a la posición en un array) */
        function crearTipoCobroSsn(identifica_concepto, tipo_cobro, nombre_concepto, wprocedimiento, nombre_procedimiento)
        {
            var validar_config = true; // validar si hay info en otros tipos de cobro de este concepto
            validar_config = validar_cambio_TipoCobro(identifica_concepto);

            if(validar_config)
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion               : 'update',
                        form                 : 'reserva_tipo_cobro',
                        consultaAjax         : '',
                        arr_politica         : $("#arr_politica").val(),
                        identifica_concepto  : identifica_concepto,
                        tipo_cobro           : tipo_cobro,
                        wprocedimiento       : wprocedimiento,
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            $("#arr_politica").val(data.arr_politica);
                            if(data.wtiempo_minimo_hora == '')
                            {
                                $('#wtiempo_minimo_hora_'+identifica_concepto).val('');
                                $('#wcobro_por_hora_'+identifica_concepto).removeAttr("checked");
                                $('#wconcepto_por_tiempo_'+identifica_concepto).val(nombre_concepto);
                                $('#wconcepto_por_tiempo_'+identifica_concepto).attr('codigo',identifica_concepto);
                                $('#wconcepto_por_tiempo_'+identifica_concepto).attr('nombre',nombre_concepto);

                                $('#wprocedimiento_por_tiempo_'+identifica_concepto).val(nombre_procedimiento);
                                $('#wprocedimiento_por_tiempo_'+identifica_concepto).attr('codigo',wprocedimiento);
                                $('#wprocedimiento_por_tiempo_'+identifica_concepto).attr('nombre',nombre_procedimiento);

                                $('#wtiempo_minimo_hora_'+identifica_concepto).removeClass('campoRequerido');
                                $('#wconcepto_por_tiempo_'+identifica_concepto).removeClass('campoRequerido');
                                $('#wprocedimiento_por_tiempo_'+identifica_concepto).removeClass('campoRequerido');
                            }
                            if(data.tr_rango_tipo_anestesia != '') {
                                $("tr[id^="+data.tr_rango_tipo_anestesia+"]").remove();
                                $("tr[id^="+data.tr_rango_tipo_anestesia+"]").removeClass('campoRequerido');
                            }
                            if(data.tr_rango_de_rangos != '') {
                                $("tr[id^="+data.tr_rango_de_rangos+"]").remove();
                                $("tr[id^="+data.tr_rango_de_rangos+"]").removeClass('campoRequerido');
                            }
                        }
                    },
                    "json"
                ).done(function(){

                });
            }
            return false;
        }

        function llamar_insumos(div_contenedor,modal_ok,codigo_concepto)
        {
            var operacion         = 'proponerygrabar';
            var modal             = modal_ok;
            var div               = div_contenedor;
            var procedimiento     = $("#wprocedimiento").attr('codigo');
            var nom_procedimiento = $("#wprocedimiento").attr('nombre');
            var entidad           = $("#wentidad").attr('codigo');
            var nom_entidad       = $("#wentidad").attr('nombre');
            var tarifa            = $("#wtarifa").attr('codigo');
            var nom_tarifa        = $("#wtarifa").attr('nombre');
            var cod_tipo_empresa  = $("#wtipoempresa").val();
            var nom_tipoempresa   = $('#wtipoempresa option:selected').text();
            // console.log('proponerygrabar'+'|'+ 'si'+'|'+ 'div_insumo'+'|'+ $("#wprocedimiento").attr('codigo')+'|'+ $("#wprocedimiento").attr('nombre')+'|'+ $("#wentidad").attr('codigo')+'|'+ $("#wentidad").attr('nombre')+'|'+ $("#wtarifa").attr('codigo')+'|'+ $("#wtarifa").attr('nombre'));
            ventana_insumo(operacion,div,modal,procedimiento,entidad,tarifa,nom_procedimiento,nom_entidad,nom_tarifa,'','',cod_tipo_empresa,'off',nom_tipoempresa,codigo_concepto,'','');
        }

    </script>

    <script type="text/javascript">

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

        function aplicarAcordeon(id_div, wcodigo_politica)
        {
            if(wcodigo_politica!='')
            {

                $("#"+id_div).accordion({
                    collapsible: true
                    ,autoHeight: false
                    ,active: false
                    // ,clearStyle: true
                    // ,heightStyle: "content"
                });
            }
            else
            {
                $("#"+id_div).accordion({
                    collapsible: true
                    ,autoHeight: false
                    // ,clearStyle: true
                    // ,heightStyle: "content"
                    // ,active: -1
                });
            }
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

        function eliminarLimite(tipo_cobro ,id_fila, identifica_concepto, prefijoFila, dif_rango)
        {
            $("#"+id_fila).hide("slow",
                function(){
                    $(this).remove();
                    $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                        {
                            accion              : 'delete',
                            form                : 'eliminar_limite',
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

        /*$("#wfechaingreso").datepicker({
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

        function guardarPolitica()
        {
            var campos_anestesia_ok = true;
            var conf_conceptos = $("#div_contenedor_conceptos > div").length;
            if(conf_conceptos == 0)
            {
                alert("No se han configurado Conceptos, dirijase a la seccion \n\n'Adicionar Concepto'\n\n para configurar una nueva politica.");
                return;
            }

            $(".save_ok:visible").each(function(){
                var cmp = $(this);
                /*if(cmp.attr("type") == 'select' && cmp.val().replace(/ /gi, "") == '') //
                {
                    console.log("es select: "+cmp.attr("id"));
                    campos_anestesia_ok = false;
                }
                else*/ if(cmp.attr("codigo") == '')
                {
                    // console.log("es autocomplete: "+cmp.attr("id"));
                    campos_anestesia_ok = false;
                    cmp.addClass("campoRequerido");
                }
                else if(cmp.val().replace(/ /gi, "") == '')
                {
                    // console.log("es texto: "+cmp.attr("id"));
                    campos_anestesia_ok = false;
                    cmp.addClass("campoRequerido");
                }
            });

            // Leer el div de procedimientos que se van a guardar, si no hay ninguno chequeado entonces no permitir guardar
            var wprocedimiento = '';
            var separador = '';
            $("#div_wprocedimientos_list").find(":checkbox").each(function(){
                if($(this).is(":checked"))
                {
                    wprocedimiento = wprocedimiento+separador+$(this).val();
                    separador = ',';
                }
            });

            $("#div_wprocedimientos_list").removeClass("campoRequerido");
            if(wprocedimiento == '')
            {
                campos_anestesia_ok = false;
                alert("Debe agregar por lo menos un procedimiento en el encabezado (Datos básicos)");
                $("#div_wprocedimientos_list").addClass("campoRequerido");
            }
			
			//JERSON
			arrTiempoApl = new Object(); 
			$("[tiempo_Aplicar]").each(function(){
				concepto = $(this).attr("concepto");
				if(concepto != undefined && concepto != "")
					arrTiempoApl[concepto] = $(this).val();
			});
			
            if(campos_anestesia_ok)
            {
                $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                    {
                        accion            : 'insert',
                        form              : 'guardar_datos_politica',
                        consultaAjax      : '',
                        arr_politica      : $("#arr_politica").val(),
                        wcodigo_politica  : $("#wcodigo_politica").val(),
                        wprocedimiento    : wprocedimiento,
                        wentidad          : $("#wentidad").attr('codigo'),
                        wtarifa           : $("#wtarifa").attr('codigo'),
                        wtipoempresa      : $("#wtipoempresa").val(),
                        wespecialidad     : $("#wespecialidad").val(),
                        // wbaseliquidacion  : $("#wbaseliquidacion").val(),
                        wnombre_plantilla : $("#wnombre_plantilla").val(),
						arrTiempoApl	  : JSON.stringify(arrTiempoApl)
                        //formulario        : $('form#form_politica').serialize()
                    },
                    function(data){
                        if(isset(data.error) && data.error == 1)
                        {
                            alert(data.mensaje);
                        }
                        else
                        {
                            alert(data.mensaje);
                            $("#arr_politica").val(data.arr_politica);
                            $("#num_politica").html(data.num_politica);
                            $("#wcodigo_politica").val(data.num_politica);
                        }
                    },
                    "json"
                ).done(function(){
                    //resetStylePrefijo("tr_rango_tipo_anestesia_");
                    recargarListadoPlantillas();
                });
            }
        }

        function recargarListadoPlantillas()
        {
            $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
                {
                    accion            : 'load',
                    form              : 'listarPlantillasGuardadas',
                    consultaAjax      : ''
                },
                function(data){
                    if(isset(data.error) && data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#div_lista_politicas").html(data.html);
                    }
                },
                "json"
            ).done(function(){
                //resetStylePrefijo("tr_rango_tipo_anestesia_");
                iniciarSearch();
            });
        }

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
        #tooltip{
            color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
        }
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

        .texto_add{
            font-size: 8pt;
        }

        .cargando{
            /*background-image: url("../../images/medical/ajax-loader2.gif");*/
            /*background-repeat:no-repeat;*/
        }

        .submit{
            text-align: center;
            background: #C3D9FF;
        }

        .texto-columna{
            font-size: 8.5pt;
        }

        /*fieldset{
            border: 2px solid #e2e2e2;
        }

        legend{
            border: 2px solid #e2e2e2;
            border-top: 0px;
            font-weight: bold;
            font-family: Verdana,Helvetica;
            background-color: #e6e6e6;
        }*/
    </style>
</head>
<body>

<div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?=$wactualiza?></div>
<input type='hidden' name='wbasedato_HCE' id='wbasedato_HCE' value="<?=$wbasedato_HCE?>">
<input type='hidden' name='wgrupo_tipos_anestesia' id='wgrupo_tipos_anestesia' value="<?=$grupo_anestesia?>">
<input type="hidden" name="arr_wempresp" id="arr_wempresp" value='<?=json_encode($sel_responsables)?>'>
<input type="hidden" name="arr_procedimientos" id="arr_procedimientos" value='<?=json_encode($arr_procedimientos)?>'>
<input type="hidden" name="arr_procedimientos64" id="arr_procedimientos64" value='<?=base64_encode(serialize($arr_procedimientos))?>'>
<input type="hidden" name="arr_conceptos" id="arr_conceptos" value='<?=json_encode($arr_conceptos)?>'>
<input type="hidden" name="arr_entidades" id="arr_entidades" value='<?=json_encode($arr_entidades)?>'>
<input type="hidden" name="arr_tarifas" id="arr_tarifas" value='<?=json_encode($arr_tarifas)?>'>
<input type="hidden" name="arr_grupos_materiales" id="arr_grupos_materiales" value='<?=json_encode($arr_grupos_materiales)?>'>
<input type="hidden" name="arr_politica" id="arr_politica" value='<?=$arr_politica?>'>
<input type="hidden" name="arr_politica_vacio" id="arr_politica_vacio" value='<?=$arr_politica?>'>
<input type="hidden" name="arr_conceptos_pol" id="arr_conceptos_pol" value=''>
<input type="hidden" name="arr_conceptos_pol_b64" id="arr_conceptos_pol_b64" value=''>
<input type="hidden" name="control_crear_concepto_bd" id="control_crear_concepto_bd" value=''>
<input type="hidden" name="arr_especialidades" id="arr_especialidades" value='<?=base64_encode(serialize($arr_especialidades))?>'>
<table align="center">
    <tr>
        <td>
            <div id="contenedor_programa_politicas_cx" align="center">

                <div id="div_lista_politicas" align="center">
                    <?=$lista_plantillas?>
                </div>
                <br>

                <div style="background: #<?=$bordemenu?>; width: 100%;">&nbsp;</div>
                <div style="background: #f3f3f3; font-size: 12pt;">Crear o editar plantillas</div>
                <table width='1155px;' cellpadding='3' cellspacing='3'>
                    <tr>
                        <td align='left'  style='font-size: 10pt;font-weight:bold;color: #2A5DB0;'>
                            <!-- <form method="post" action="" name="form_politica" id="form_politica"> -->
                                <!-- DATOS BÁSICOS PLANTILLA -->

                                <input type="hidden" id="wcodigo_politica" name="wcodigo_politica" value="">

                                <div id='div_datos_basicos'>
                                    <h3>&nbsp;&nbsp;&nbsp;DATOS B&Aacute;SICOS</h3>
                                    <div id='div_datos_basicos_politica'>
                                        <!-- <fieldset id="">
                                            <legend>Datos básicos</legend> -->
                                            <table width='80%' id='' align="center">
                                                <tr class='encabezadoTabla'>
                                                    <td class='encTabla'><?=NOMBRE_PLANTILLA?></td>
                                                    <td class='encTabla'><?=PROCEDIMIENTO?></td>
                                                    <td class='encTabla'><?=ENTIDAD?></td>
                                                    <td class='encTabla'><?=TARIFA?></td>
                                                    <td class='encTabla'><?=TIPO_EMPRESA?></td>
                                                    <td class='encTabla'><?=LB_ESPECIALIDAD?></td>
                                                </tr>
                                                <tr class='encabezadoTabla'>
                                                    <td class='fila1'>
                                                        <input type="text" value="" id="wnombre_plantilla" name="wnombre_plantilla" class="requerido save_ok" placeholder="<?=NOMBRE_PLANTILLA?>"/>
                                                    </td>
                                                    <td class='fila1'>
                                                        <input type="text" value="" id="wprocedimiento" name="wprocedimiento" codigo="" nombre="" class="" placeholder="<?=PROCEDIMIENTO?>" />
                                                    </td>
                                                    <td class='fila1'>
                                                        <input type="text" value="" id="wentidad" name="wentidad" class="requerido save_ok" placeholder="<?=ENTIDAD?>"/>
                                                    </td>
                                                    <td class='fila1'>
                                                        <input type="text" value="" id="wtarifa" name="wtarifa" class="requerido save_ok" placeholder="<?=TARIFA?>"/>
                                                    </td>
                                                    <td class='fila1'>
                                                        <select id="wtipoempresa" name="wtipoempresa" class="requerido save_ok">
                                                            <?php
                                                            foreach ($arr_tipo_empresa as $key => $value) {
                                                                echo '<option value="'.$key.'">'.utf8_encode($value).'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td class='fila1'>
                                                        <select id="wespecialidad" name="wespecialidad" class="requerido save_ok">
                                                            <?php
                                                            foreach ($arr_especialidades as $key => $value) {
                                                                $selected = ($key == '*') ? 'selected="selected"': '';
                                                                echo '<option '.$selected.' value="'.$key.'">'.utf8_encode($value).'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <!-- <td class='fila1'>
                                                        <select id="wbaseliquidacion" name="wbaseliquidacion" class="requerido save_ok">
                                                            <option value="">Seleccione</option>
                                                            <?php
                                                            /*foreach ($arr_liquidacion as $key => $value) {
                                                                echo '<option value="'.$key.'">'.$key.'-'.utf8_encode($value).'</option>';
                                                            }*/
                                                            ?>
                                                        </select>
                                                    </td> -->
                                                </tr>
                                                <tr class='fila1'>
                                                    <td>&nbsp;</td>
                                                    <td colspan="2">
                                                        Procedimientos Plantilla
                                                        <div id="div_wprocedimientos_list" style="height: 80px; overflow:auto; background-color: #FEFEFE; font-size: 7.5pt;" >
                                                            <!-- <div style="height: 80px; overflow:auto; background-color: #FEFEFE;"><input type="checkbox" id="chk_wprocedimiento_">Apendicectomía</div> -->
                                                        </div>
                                                    </td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </table>
                                            <span>Platilla: </span><span id="num_politica">--</span> <!-- <button onclick="llamar_insumos();">Abrir insumos</button> -->
                                            <div id="div_insumo"></div>
                                        <!-- </fieldset>
                                        <fieldset id="">
                                            <legend>Adicionar concepto</legend> -->
                                            <table width='' id='' align="center">
                                                <tr class='encabezadoTabla'>
                                                    <td class='encTabla'><?=CONCEPTO_LABEL?></td>
                                                    <!-- <td class='encTabla'>Cantidad</td> -->
                                                    <td class='encTabla'>&nbsp;</td>
                                                </tr>
                                                <tr class='encabezadoTabla'>
                                                    <td class='fila1' style="text-align:center;">
                                                        <input type="text" id="wnuevo_concepto_gral" name="wnuevo_concepto_gral" codigo="" nombre="" class="" placeholder="Concepto" size="30" />
                                                    </td>
                                                    <!-- <td class='fila1' style="text-align:center;">
                                                        <input type="text" id="wcantidad_concepto" name="wcantidad_concepto" class="requerido numerico" placeholder="0" maxlength="2" size="3" value="1" />
                                                    </td> -->
                                                    <td class='notificar' id="boton_adicionar_concepto" style="text-align:center;">
                                                        <!-- <a style="color:#ffffff;" class="st_boton" href="javascript:agregarConcepto('div_contenedor_conceptos','','','','','','');" data-icono="chat" data-mensaje="Concepto agregado">Adicionar</a> -->
                                                        <button onclick="agregarConcepto('div_contenedor_conceptos','','','','','','');" >Adicionar</button>
                                                    </td>
                                                </tr>
                                            </table>
                                        <!-- </fieldset> -->
                                    </div>
                                </div>
                                <br>
                                <!-- DATOS BÁSICOS PLANTILLA -->

                                <!-- ADICIONAR CONCEPTOS PLANTILLA -->
                                <!-- <div id='div_conceptos_plantilla'>
                                    <h3>&nbsp;&nbsp;&nbsp;CONCEPTOS PROCEDIMIENTO</h3>
                                    <div id=''>
                                    </div>
                                </div> -->
                                <!-- ADICIONAR CONCEPTOS PLANTILLA -->

                                <!-- CONTENEDOR CONCEPTOS PLANTILLA -->
                                <div id="div_contenedor_conceptos">
                                    <!-- CONCEPTOS PLANTILLA -->

                                    <!-- CONCEPTOS PLANTILLA -->
                                </div>
                                <!-- CONTENEDOR CONCEPTOS PLANTILLA -->
                                <div class="submit">
                                    <button onclick="guardarPolitica();">Guardar plantilla</button>
                                </div>
                            <!-- </form> -->
                        </td>
                    <tr>
                </table>
            </div>
        </td>
    </tr>
</table>
</body>
</html>