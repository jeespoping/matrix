<?php
include_once("conex.php");
/**
 PROGRAMA                   : funciones.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : Julio - Agosto 2012 (aprox.)

 DESCRIPCION:
 Este archivo contiene una serie de funciones que son instanciadas en diferentes programas del módulo tal_huma. Como por ejemplo
 creación del menú, buscadores de centros de costos, cargos, usuarios. Búsqueda de usuarios en unix y en talhuma.

 ACTUALIZACIONES:

*  Noviembre 15 de 2012
    Edwar Jaramillo     :   *   Se actualiza la función pintarMenuSeleccion() para que soporte la integración con la gestión de indicadores, esta función ahora se encarga de
                                verificar si la opción a pintar hace parde de un indicador o no, y también si un menú es contenedor de indicadores.

 *  Noviembre 08 de 2012
    Edwar Jaramillo     :   *   Se crea la función denominada "empresaEmpleado()", esta función se encarga de encontrar el código de la empresa a la que pertenece el usuario,
                                para lograrlo recibe por parámetros el código de sesión del usuario y lo busca en la tabla de usuarios de matrix. Se actualiza la función "buscarEnUnix()"
                                para que al momento de encontrar empleados en unix, le concatene el código de la empresa a la que pertenece, en este caso se le concatene la variable $wemp_pmla.
                                La función "buscarEnTalhuma()" es modificada para que ya no busque códigos exactamente iguales al que llega por parámetros sino que realiza búsquedas por medio
                                de la instrucción " LIKE 'xxxxx%' " puesto que en el buscado no debe ser especificado a que empresa pertenece el código a buscar. La función "consultarSiEsAdmin()"
                                también es afectada por la función "empresaEmpleado()" para que se tenga en cuenta que el usuario de sesión debe ser de la forma "xxxxx-yy".

                            *   En la funcion de busqueda en talhuma y busqueda en unix se adicionan nuevos parámetros a las funciones para poder buscar por fecha de ingreso o por fecha de retiro.

 *  Octubre 10 de 2012
    Edwar Jaramillo     : Documentación de código.
*/

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
function getMenusQuery($wemp_pmla, $conex, $tipo = 'principal', $tema, $info_use = '', $caso_arbol = 'usuario')
{
    $qMp = '';

    $filtra_estado = " AND usu.Prfest = 'on' ";
    if($tipo == 'detalle')
    {
        $filtra_estado = '';
    }

    $filtro = array('columnas'=>'','tabla_use'=>'','filtro'=>'','group'=>'');
    if(($caso_arbol == 'usuario' || $caso_arbol == '') && $info_use != '') // Filtros por usuario para consultar a lo que tiene acceso
    {
        $filtro['columnas'] = " , usu.Prfacc AS permiso, usu.Prfadm AS esAdmin
                                , usu.Prfcco AS cod_costo, usu.Prfccg AS cod_cargo
                                , usu.Prfuse AS usuario, usu.Prfest AS estado, usu.id AS id_reg";
        $filtro['tabla_use'] = "LEFT JOIN
                                root_000082 AS usu ON (usu.Prftem = rel.Rtptem AND usu.Prftab = rel.Rtpstb)";
        $filtro['filtro'] = $filtra_estado."
                            AND (  usu.Prfuse = '".$info_use['cod_use']."'
                                    OR (usu.Prfuse = '' AND  (usu.Prfccg = '".$info_use['cod_ccg']."' OR usu.Prfccg = '*') AND (usu.Prfcco = '' OR usu.Prfcco = '".$info_use['cod_cco']."') )
                                    OR (usu.Prfuse = '' AND usu.Prfccg = '' AND (usu.Prfcco = '".$info_use['cod_cco']."' OR usu.Prfcco = '*'))
                                 )";
        $filtro['group'] = "GROUP BY tem.Temcod, rel.Rtptab, rel.Rtpstb";
    }
    elseif($caso_arbol != '' && $caso_arbol == 'centro_costo')
    {
        $filtro['columnas'] = " , usu.Prfacc AS permiso, usu.Prfadm AS esAdmin
                                , usu.Prfcco AS cod_costo, usu.Prfccg AS cod_cargo
                                , usu.Prfuse AS usuario, usu.Prfest AS estado, usu.id AS id_reg";
        $filtro['tabla_use'] = "LEFT JOIN
                                root_000082 AS usu ON (usu.Prftem = rel.Rtptem AND usu.Prftab = rel.Rtpstb)";
        $filtro['filtro'] = $filtra_estado."

                            AND (usu.Prfcco = '".$info_use['cod_cco']."' OR usu.Prfcco = '*')";
        $filtro['group'] = "GROUP BY tem.Temcod, rel.Rtptab, rel.Rtpstb";
    }
    elseif($caso_arbol != '' && $caso_arbol == 'cargos')
    {
        $filtro['columnas'] = " , usu.Prfacc AS permiso, usu.Prfadm AS esAdmin
                                , usu.Prfcco AS cod_costo, usu.Prfccg AS cod_cargo
                                , usu.Prfuse AS usuario, usu.Prfest AS estado, usu.id AS id_reg";
        $filtro['tabla_use'] = "LEFT JOIN
                                root_000082 AS usu ON (usu.Prftem = rel.Rtptem AND usu.Prftab = rel.Rtpstb)";
        $filtro['filtro'] = $filtra_estado."

                            AND (usu.Prfccg = '".$info_use['cod_ccg']."' OR usu.Prfccg = '*')";
        $filtro['group'] = "GROUP BY tem.Temcod, rel.Rtptab, rel.Rtpstb";
    }
    elseif($caso_arbol != '' && $caso_arbol == 'menu_ver')
    {
        $filtro['columnas'] = " , per.Prfacc AS permiso, per.Prfadm AS esAdmin, per.Prftab AS cod_tab, mnu.Tabdes AS nom_menu
                                , per.Prfcco AS cod_cco, per.Prfccg AS cod_ccg, carg.Cardes AS nom_cargo, per.Prfuse AS cod_use
                                , CONCAT(us.Ideno1,' ',us.Ideno2,' ',us.Ideap1,' ',us.Ideap2) AS nom_use";
        $filtro['tabla_use']= "";
        $filtro['filtro']   = " AND Prftab = '".$info_use['cod_tab']."'";
        $filtro['group']    = "";
    }

    $filtro_tema = '';
    if($tema != '')
    {
        $filtro_tema = "AND rel.Rtptem = '".$tema."'";
    }

    switch($tipo)
    {
        case 'principal':
                /* LISTA LOS MENUS PRINCIPALES */
                $qMp = "SELECT  tem.Temcod AS cod_tema, rel.Rtptab AS cod_tab, rel.Rtpstb AS cod_subtab, tab.Tabdes AS nombre_subtab
                                , CONVERT(rel.Rtpord,DECIMAL) AS orden
                                , tab.Taburl AS url
                                , tab.Tabind AS indicador
                                , tab.Tabpro AS propiedades
                                , tab.Tabref AS prop_tabla
                                ".$filtro['columnas']."
                        FROM    root_000081 AS rel
                                INNER JOIN
                                root_000076 AS tem ON (tem.Temcod = rel.Rtptem AND tem.Tememp = '".$wemp_pmla."')
                                LEFT JOIN
                                root_000080 AS tab ON (rel.Rtpstb = tab.Tabcod)
                                ".$filtro['tabla_use']."
                        WHERE   tem.Temest = 'on'
                                AND rel.Rtpest = 'on'
                                AND rel.Rtptab = rel.Rtpstb
                                ".$filtro_tema."
                                ".$filtro['filtro']."
                        ".$filtro['group']."
                        ORDER BY orden";
                break;
        case 'submenus' :
                /* LISTA LOS SUBMENUS */
                $qMp = "SELECT  tem.Temcod AS cod_tema, rel.Rtptab AS cod_tab, rel.Rtpstb AS cod_subtab, tab.Tabdes AS nombre_subtab
                                , CONVERT(rel.Rtpord,DECIMAL) AS orden
                                , tab.Taburl AS url
                                , tab.Tabind AS indicador
                                , tab.Tabpro AS propiedades
                                , tab.Tabref AS prop_tabla
                                ".$filtro['columnas']."
                        FROM    root_000081 AS rel
                                INNER JOIN
                                root_000076 AS tem ON (tem.Temcod = rel.Rtptem AND tem.Tememp = '".$wemp_pmla."')
                                LEFT JOIN
                                root_000080 AS tab ON (rel.Rtpstb = tab.Tabcod)
                                ".$filtro['tabla_use']."
                        WHERE   tem.Temest = 'on'
                                AND rel.Rtpest = 'on'
                                AND rel.Rtptab <> rel.Rtpstb
                                AND tab.Tabest = 'on'
                                ".$filtro_tema."
                                ".$filtro['filtro']."
                        ".$filtro['group']."
                        ORDER BY orden";
                break;
        case 'detalle' :
                /* LISTA LOS MENUS Y SUBMENUS */
                $qMp = "
                        SELECT  tem.Temdes AS nom_tema, tem.Temcod AS cod_tema
                                , rel.Rtptab AS cod_tab, rel.Rtpstb AS cod_subtab
                                , tab.Tabdes AS nombre_subtab, CONVERT(rel.Rtpord,DECIMAL) AS orden
                                , tab.Taburl AS url
                                , tab.Tabind AS indicador
                                , tab.Tabpro AS propiedades
                                , tab.Tabref AS prop_tabla
                                ".$filtro['columnas']."
                        FROM    root_000081 AS rel
                                INNER JOIN
                                root_000076 AS tem ON (tem.Temcod = rel.Rtptem AND tem.Tememp = '".$wemp_pmla."')
                                LEFT JOIN
                                root_000080 AS tab ON (rel.Rtpstb = tab.Tabcod)
                                ".$filtro['tabla_use']."
                        WHERE   tem.Temest = 'on'
                                AND rel.Rtpest = 'on'
                                AND rel.Rtptab = rel.Rtpstb
                                AND tab.Tabest = 'on'
                                ".$filtro_tema."
                                ".$filtro['filtro']."

                        UNION

                        SELECT  tem.Temdes AS nom_tema, tem.Temcod AS cod_tema
                                , rel.Rtptab AS cod_tab, rel.Rtpstb AS cod_subtab
                                , tab.Tabdes AS nombre_subtab, rel.Rtpord AS orden
                                , tab.Taburl AS url
                                , tab.Tabind AS indicador
                                , tab.Tabpro AS propiedades
                                , tab.Tabref AS prop_tabla
                                ".$filtro['columnas']."
                        FROM    root_000081 AS rel
                                INNER JOIN
                                root_000076 AS tem ON (tem.Temcod = rel.Rtptem AND tem.Tememp = '".$wemp_pmla."')
                                LEFT JOIN
                                root_000080 AS tab ON (rel.Rtpstb = tab.Tabcod)
                                ".$filtro['tabla_use']."
                        WHERE   tem.Temest = 'on'
                                AND rel.Rtpest = 'on'
                                AND rel.Rtptab <> rel.Rtpstb
                                ".$filtro_tema."
                                ".$filtro['filtro']."

                        ORDER BY orden, cod_tema";
                break;
        case 'detalle_menu' :
                /* LISTA LOS MENUS Y SUBMENUS */
                $qMp = "SELECT  per.Prfacc AS permiso, per.Prfadm AS esAdmin
                                , per.Prfcco AS cod_costo, per.Prfccg AS cod_cargo
                                , per.Prfuse AS usuario, per.Prfest AS estado
                                , per.id AS id_reg
                                ,tem.Temdes AS nom_tema, tem.Temcod AS cod_tema
                                , mnu.Tabdes AS nombre_subtab
                        FROM    root_000082 AS per
                                INNER JOIN
                                root_000076 AS tem ON (tem.Temcod = per.Prftem AND tem.Temest = 'on' AND tem.Tememp = '".$wemp_pmla."')
                                LEFT JOIN
                                root_000080 AS mnu ON (mnu.Tabcod = per.Prftab)
                        WHERE   per.Prftem = '".$tema."'
                                ".$filtro['filtro']."
                        ORDER BY per.Prfuse, per.Prfccg, per.Prfcco";
                break;
    }


    $menus_cod = array();
    $menus_info = array();
    $menus = array();
    if($qMp != '')
    {
        //echo "<pre>".$qMp."</pre>";
        $resp = mysql_query($qMp,$conex);

        if($tipo == 'detalle' || $tipo == 'detalle_menu')
        {
            return $resp;
        }

        while( $row = mysql_fetch_array($resp) )
        {
            $llave = $row['cod_tab'].'->'.$row['cod_subtab'];
            if ($tipo == 'principal')
            { $llave = $row['cod_subtab']; }
            $menus_cod[$llave] = array();
            $menus_info[$row['cod_subtab']]['info'] = array(    'cod_tema'      =>$row['cod_tema'],
                                                                'cod_tab'       =>$row['cod_tab'],
                                                                'cod_subtab'    =>$row['cod_subtab'],
                                                                'nombre_subtab' =>$row['nombre_subtab'],
                                                                'orden'         =>$row['orden'],
                                                                'url'           =>$row['url'],
                                                                'indicador'     =>$row['indicador'],
                                                                'propiedades'   =>$row['propiedades'],
                                                                'prop_tabla'    =>$row['prop_tabla'],
                                                                'permiso'       =>'',
                                                                'esAdmin'       =>'',
                                                                'usuario'       =>''
                                                                );
            if($info_use != '')
            {
                $menus_info[$row['cod_subtab']]['info']['cod_costo'] = $row['cod_costo'];
                $menus_info[$row['cod_subtab']]['info']['cod_cargo'] = $row['cod_cargo'];
                $menus_info[$row['cod_subtab']]['info']['permiso'] = $row['permiso'];
                $menus_info[$row['cod_subtab']]['info']['esAdmin'] = $row['esAdmin'];
                $menus_info[$row['cod_subtab']]['info']['usuario'] = $info_use['cod_use'];
            }
        }
        $menus['menus_cod'] = $menus_cod;
        $menus['menus_info'] = $menus_info;
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
function armarArbolMenu(&$arbol, $arr_submenus)
{
    $arr_sobran = array();
    foreach($arr_submenus as $key => $value)
    {
        // echo '<br>';echo "<pre><br />en armar $key: ";print_r($arbol);echo '</pre>';
        if(!buscarPosicionMenu($arbol, $key))
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
    { armarArbolMenu($arbol, $arr_sobran); }

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
function buscarPosicionMenu(&$arbol, $cod_menu)
{
    if(is_array($arbol))
    {
        $explode = explode('->',$cod_menu);
        $padre = $explode[0];
        $hijo  = $explode[1];
        if(array_key_exists($padre,$arbol))
        {
            $arbol[$padre][$hijo] = array();
            return true;
        }
        elseif(count($arbol)>0)
        {
            foreach($arbol as $key => $value)
            {
                if(buscarPosicionMenu($arbol[$key], $cod_menu))
                { break; }
            }
        }
        else
        { return false; }
    }
    else
    { return false; }

    return true;
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
function crearArbolMenus($wemp_pmla, $conex, $tema, &$submenus_sobrantes, $info_use = '', $caso_arbol = '')
{
    $arbol_menu = getMenusQuery($wemp_pmla, $conex, 'principal', $tema, $info_use, $caso_arbol);
    $sub_menu = getMenusQuery($wemp_pmla, $conex, 'submenus', $tema, $info_use, $caso_arbol);

    $submenus_sobrantes = armarArbolMenu($arbol_menu['menus_cod'], $sub_menu['menus_cod']);

    // $merge_info = array_merge($arbol_menu['menus_info'], $sub_menu['menus_info']); // no usar merge puesto que al hacerlo se dañan algunos indices del array p.e. '10' queda como '0'
    $merge_info = array();
    foreach($arbol_menu['menus_info'] as $key => $value)
    {
        $merge_info[$key] = $value;
    }

    foreach($sub_menu['menus_info'] as $key => $value)
    {
        $merge_info[$key] = $value;
    }

    $arbol_completo = array('menus_cod'=>$arbol_menu['menus_cod'],'menus_info'=>$merge_info);
    return $arbol_completo;
}

/**
 * Esta es la función que se encarga de generar el código html que va a pintar las opciones de menús, pero la puede hacer de diferentes maneras,
 * ya sea como elementos <ul> y <li> para ser manipulados como pestañas, ó tambien puede armar el arbol vertical con opciones ckeckbox, ó mostrar
 * las opciones de menús sin los ckeckbox de forma vertical.
 *
 * $es_indicador: como parámetro es usado cuando se tiene que repintar las opciones de indicadores entonces debe estar seteada en 'off' puesto que la anterior opción debería ser el menú de los indicadores actuales.
 * $pro_especial: como parámetro es usado cuando se tiene que repintar las opciones de indicadores entonces debe estar seteada en 'on'.
 * $prop_especifica: como parámetro es usado cuando se tiene que repintar las opciones de indicadores entonces debe estar seteada con el nombre de la tabla de la forma a pintar p.e. sgc_000004.
 * $tabla_propiedades: tabla que relaciona los nombres de las tablas de propiedades de los indicadores.
 *
 * @param array $arbol
 * @param array $arbol_info
 * @param unknown $codmenu
 * @param unknown $sangria  : Aplica para mostra el menú de forma vertical de tal forma que se noten los niveles por sangría.
 * @param unknown $padre    : Cuando se hace llamado recursivo, en este parámetro se envía el código del nivel anterior.
 * @return unknown
 */
function pintarMenuSeleccion($conex, $arbol, $arbol_info, &$divs_include, $modoImprimir = 'checks', $codmenu = '', $sangria = '20', $padre = '', $propiedades = array(), $propiedad_ok = '', $es_indicador = '', $pro_especial = '', $prop_especifica = '', $tabla_propiedades = '')
{
    $result = '';
    if($arbol == '' || (is_array($arbol) && count($arbol) == 0) )
    {
        return;
    }
    else
    {
        $si_propiedad = array();
        if($modoImprimir == 'tabs' && $padre == '') { $result .= "<ul id='nav'>"; }
        elseif($modoImprimir == 'tabs' && $padre != '')
        {
            $result .= "
                    <ul>";
        }

        foreach($arbol as $key => $value)
        {
            if($modoImprimir == 'checks')
            {
                $es_indicador = ($es_indicador != '') ? $es_indicador : $arbol_info[$key]['info']['indicador'];
                // 2012-10-18 Para tener en cuenta que los indicadores deben pintarse de forma diferente en la asignación de permisos.
                if($es_indicador != 'on')
                {
                    $id_padre = ($codmenu == '') ? 'padre_' : $padre.'_';

                    // Si no es indicador entonces es un menú que posiblemente tiene propiedades especiales menú
                    $pro_especial = ($pro_especial != '') ? $pro_especial : $arbol_info[$key]['info']['propiedades'];
                    $propiedades = array();
                    $grupos_contenedor = '';
                    $prop_uno = '';

                    if($pro_especial == 'on') // Si el menú tiene propiedades especiales para pintar sus hijos.
                    {
                        $sangria = $sangria-20;
                        $filtro = '';
                        if($prop_especifica != '')
                        {
                            $prop_explode = explode('_',$prop_especifica);
                            //$filtro = "AND Proprf = '".$prop_explode[0]."'
                            $filtro = "AND Protbl = '".$prop_explode[1]."'";
                        }
                        $tabla_propiedades = ($tabla_propiedades != '') ? $tabla_propiedades : trim($arbol_info[$key]['info']['prop_tabla']);

                        //**************** [*1*] Consulta en la tabla de propiedades los tipos de agrupaciones principales.
                        $q_propiedades = "
                                SELECT  Prodes, Protbl, Proprf, Procpr, Prodpr, Profpr, Protcr, Propcr, Proccr, Profcr, Protcc
                                FROM    ".$tabla_propiedades."
                                WHERE   Proest = 'on'
                                        AND Proprf = 'sgc'
                                        $filtro
                                Order by Prodes";
                        $resp_q = mysql_query($q_propiedades,$conex);

                        $tablas_prop = array();

                        if($prop_especifica == '') // Si no es opcion reorganizar
                        {
                            $grupos_contenedor = "
                                    <table border='0' cellspacing='2' cellpadding='0' style='margin-left: ".$sangria."px;'>
                                        <tr>";
                        }

                        if($propiedad_ok != '')
                        {
                            $prop_uno = $propiedad_ok;
                        }

                        while ($rowq = mysql_fetch_array($resp_q))
                        {
                            $chk_r = '';
                            $tbl = $rowq['Proprf'].'_'.$rowq['Protbl'];
                            if($prop_uno == '')
                            {
                                $prop_uno = $tbl;
                                $chk_r = "checked='checked'";
                            }

                            $id_men_padre = explode('_',$padre);
                            $id_men_padre = $id_men_padre[(count($id_men_padre)-1)];
                            $id_men_padre = $key;

                            if($prop_especifica == '') // Si no es opcion reorganizar
                            {
                                $fn_evento = "onClick='reOrdenar(this,\"".$id_men_padre."\",\"inds_".$id_padre.$key."\",\"".$id_padre.$key."\");'";
                                $grupos_contenedor .= " <td class='noms_propiedad' style='font-size:8pt;color:gray;font-weight:bold;' nowrap='nowrap'>
                                                            <div style='display:inline;'>
                                                            <input type='radio' ".$chk_r." id='radio_".$tbl."' name='grupo_propiedad' value='".$tbl."' ".$fn_evento." />".$rowq['Prodes']."</td>
                                                            </div>";
                            }

                            if(!array_key_exists($tbl,$tablas_prop))
                            {
                                $tablas_prop[$tbl] = array();
                            }
                            $tablas_prop[$tbl] = array(
                                                        'nom_prop'      => $rowq['Prodes'], // Nombre de la propiedad.
                                                        'tabla_prop'    => $rowq['Protbl'], // Tabla de propiedades.
                                                        'pref_tprop'    => $rowq['Proprf'], // Prefijo de la Tabla de propiedades.
                                                        'codigo_prop'   => $rowq['Procpr'], // Código campo cruce de propiedades.
                                                        'filtros_prop'  => $rowq['Profpr'], // Filtros adicionales para ejecutar en el query.
                                                        'descr_prop'    => $rowq['Prodpr'], // Campo cruce de propiedad (descripción).
                                                        'tabla_buscar'  => $rowq['Protcr'], // Tabla donde se buscan indicadores de este grupo.
                                                        'pref_tbuscar'  => $rowq['Propcr'], // Prefijo de la tabla donde se buscan indicadores de este grupo.
                                                        'campo_buscar'  => $rowq['Proccr'], // Campo donde se buscarán los códigos de los grupos por cada indicador.
                                                        'filtros_buscar'=> $rowq['Profcr'], // Filtros adicionales que se deben ejecutar en la tabla donde se buscarán datos.
                                                        'tipo_buscar'   => $rowq['Protcc']);// Si el campo a buscar tiene los códigos de los grupos concatenados o no.
                        }

                        if($prop_especifica == '') // Si no es opcion reorganizar
                        {
                            $grupos_contenedor .= "
                                        </tr>
                                    </table>";
                        }

                        // $result .= print_r($tablas_prop,true);
                        foreach($tablas_prop as $tabla_propiedad => $arr_props)
                        {
                            if(!array_key_exists($tabla_propiedad,$propiedades))
                            {
                                $propiedades[$tabla_propiedad] = array();
                            }

                            // $result .= $arr_props['nom_prop'].'<br>';
                            //**************** [*2*] Por cada tipo de agrupación pricipal consulta las subagrupaciones para los indicadores.
                            $tabla_01_props = $arr_props['pref_tprop']."_".$arr_props['tabla_prop'];
                            $qp = " SELECT  ".$arr_props['codigo_prop'].", ".$arr_props['descr_prop']."
                                    FROM    ".$tabla_01_props."
                                    WHERE   ".trim($arr_props['filtros_prop'], 'AND')."";

                            if($resp_qp = mysql_query($qp,$conex))
                            {
                                while($row_p = mysql_fetch_array($resp_qp))
                                {
                                    $cod_p = $arr_props['codigo_prop'];
                                    $des_p = $arr_props['descr_prop'];

                                    $indice = $arr_props['pref_tprop'].'_'.$row_p[$cod_p];
                                    if(!array_key_exists($indice,$propiedades[$tabla_propiedad]))
                                    {
                                        $propiedades[$tabla_propiedad][$indice] = array();
                                        $propiedades[$tabla_propiedad][$indice]['nombre_grupo'] = $row_p[$des_p];
                                        $propiedades[$tabla_propiedad][$indice]['indicadores'] = array();
                                    }

                                    //**************** [*3*] Por cada tipo de subagrupación consulta los indicadores que pertenecen a esa subagrupación.
                                    // Ántes de hacerlo verificar como estan almacenados los códigos en el campo a buscar (es del tipo unico o multiple concatenado por comas)
                                    $q_ind = '';
                                    $tabla_02_inds = $arr_props['pref_tbuscar']."_".$arr_props['tabla_buscar'];
                                    if($arr_props['tipo_buscar'] == 'unico')
                                    {
                                        $q_ind = "
                                                SELECT  Indcod, Indnom, ".$arr_props['campo_buscar']."
                                                FROM    ".$tabla_02_inds."
                                                        INNER JOIN
                                                        $tabla_01_props ON (".$arr_props['codigo_prop']." = ".$arr_props['campo_buscar'].")
                                                WHERE   ".trim($arr_props['filtros_buscar'], 'AND')."";
                                    }
                                    elseif($arr_props['tipo_buscar'] == 'multiple')
                                    {
                                        $q_ind = "
                                                SELECT  Indcod, Indnom, ".$arr_props['campo_buscar']."
                                                FROM    ".$tabla_02_inds."
                                                WHERE   ".$arr_props['campo_buscar']." LIKE '%".$row_p[$cod_p]."%' ".trim($arr_props['filtros_buscar'])."";
                                    }

                                    if($resp_qi = mysql_query($q_ind,$conex))
                                    {
                                        while($row_i = mysql_fetch_array($resp_qi))
                                        {
                                            $codigo_buscar = $row_i[$arr_props['campo_buscar']];
                                            if($arr_props['tipo_buscar'] == 'multiple')
                                            {
                                                /*
                                                    valída que el id buscado si sea realmente uno de los ids concatenados, porque puede pasar que se busque el código '1'
                                                    Pero como la consulta busca con like entonces se puede confundir con '01' o con '10'.
                                                */
                                                $valor_encontrado = $codigo_buscar;
                                                $explode_encontrados = explode(',',$valor_encontrado);
                                                if(!in_array($row_p[$cod_p],$explode_encontrados))
                                                {
                                                    $row_i['Indcod'] = '';
                                                }
                                                else
                                                {
                                                    $codigo_buscar = $row_p[$cod_p];
                                                }
                                            }

                                            if($row_i['Indcod'] != '')
                                            {
                                                $indice_subgrupo = $arr_props['pref_tprop'].'_'.$codigo_buscar;
                                                if(array_key_exists($indice_subgrupo,$propiedades[$tabla_propiedad]))
                                                {
                                                    $propiedades[$tabla_propiedad][$indice_subgrupo]['indicadores'][$row_i['Indcod']] = $row_i['Indnom'];
                                                }
                                            }
                                        }
                                    }
                                    if(count($propiedades[$tabla_propiedad][$indice]['indicadores']) <= 0) // Si un tipo de propiedad no tiene indicadores se elimina el tipo de propiedad del array.
                                    {
                                        unset($propiedades[$tabla_propiedad][$indice]);
                                    }
                                }
                            }
                            if(count($propiedades[$tabla_propiedad]) <= 0) // Si la tabla de propiedad no tiene subgrupos entonces se elimina del array.
                            {
                                unset($propiedades[$tabla_propiedad]);
                            }
                        }
                        // $result .= print_r($propiedades,true);
                        // echo '<pre>'; print_r($propiedades); echo '</pre>';
                    }

                    if($prop_especifica == '') // Si no es opcion reorganizar, cuando se selecciona otra forma de reagrupar, el contenido de este if ya esta pintado en pantalla, entonces lo único que se debe hacer el reorganizar sus indicadores.
                    {
                        $input = '<input type="checkbox" id="'.$id_padre.$key.'" name="arbol_chk['.$key.']" onclick="seleccionar(\''.$id_padre.$key.'\',\''.$padre.'\');"  value="'.$key.'" />';
                        $result .= "<div style='color:#2A5DB0; font-weight:bold; font-size: 9pt;  background-color: #F4F4F4; border-bottom: 1px #ffffff solid; margin-left: ".$sangria."px;'>".$input."&nbsp;".$arbol_info[$key]['info']['nombre_subtab'].'</div>';
                        $result .= $grupos_contenedor;
                    }
                    $result .= pintarMenuSeleccion($conex, $arbol[$key], $arbol_info, $divs_include, $modoImprimir, $key, $sangria+20, $id_padre.$key, $propiedades, $prop_uno);
                }
                else
                {
                    // $result .=  print_r($propiedades,true);
                    $menu_contenedor = explode('_',$padre);
                    $id_menu_cont = $menu_contenedor[(count($menu_contenedor)-1)];
                    // $result .= '<br>'.$id_menu_cont;
                    $resultParcial = '';

                    $id_padre = ($codmenu == '') ? 'padre_' : $padre.'_';
                    $input = '<input type="checkbox" id="'.$id_padre.$key.'" name="arbol_chk['.$key.']" onclick="seleccionIguales(this); seleccionar(\''.$id_padre.$key.'\',\''.$padre.'\');"  value="'.$key.'" />';
                    $resultParcial .= " <div style='color:#2A5DB0; font-weight:bold; font-size: 8pt; text-aling:justify; background-color: #F4F4F4; border-bottom: 1px #ffffff solid; margin-left: ".$sangria."px;'>
                                        <table cellpadding='0' cellspacing='0' border='0'>
                                            <tr>
                                                <td valign='top'>".$input."&nbsp;</td>
                                                <td align='justify' valign='top' style='color:#2A5DB0; font-weight:bold; font-size: 8pt; text-align:justify; background-color: #F4F4F4; border-bottom: 1px #ffffff solid;'; '>".$arbol_info[$key]['info']['nombre_subtab'].'</td>
                                            </tr>
                                        </table>
                                        </div>';
                    $resultParcial .= pintarMenuSeleccion($conex, $arbol[$key], $arbol_info, $divs_include, $modoImprimir, $key, $sangria+20, $id_padre.$key);

                    $si_existe = false;
                    foreach($propiedades as $tb_pro => $arr_ids_prop)
                    {
                        foreach($arr_ids_prop as $id_prop => $indicadores_prop)
                        {
                            if(array_key_exists($key,$indicadores_prop['indicadores']))
                            {
                                $propiedades[$tb_pro][$id_prop]['indicadores'][$key] = $resultParcial;
                                //$si_existe = true;
                            }
                        }
                    }
                }
            }
            elseif($modoImprimir == 'tabs')
            {
                $es_indicador = $arbol_info[$key]['info']['indicador'];
                // 2012-10-18 Para tener en cuenta que no puede pintar en el menú las opciones que son tipo indicador.
                if($es_indicador != 'on')
                {
                    $url_tab = $arbol_info[$key]['info']['url'];
                    $nombre_tab = $arbol_info[$key]['info']['nombre_subtab'];
                    $idx = '';
                    $codtab = $key;
                    $buscame = '';
                    $indica_submenu = '';
                    if($url_tab == '') { $url_tab = '#'; $indica_submenu = '  &raquo;';}
                    if($padre == '')
                    {
                        $idx = $key;
                    }
                    else{ $idx = $padre; }

                    $permisoVer = '[*WCODIGO*]';
                    if($arbol_info[$key]['info']['esAdmin'] == 'off')  { $permisoVer = $arbol_info[$key]['info']['usuario']; }
                    if($arbol_info[$key]['info']['permiso'] == 'consultar')  { $permisoVer = '[*WCODIGO*]'; }

                    $funcionesJS = "recargar(\"id_href_".$codtab."\",\"".$url_tab."\",\"consultaAjax=\",\"".$permisoVer."\",\"visor_programas\",\"".$codtab."\",\"".$nombre_tab."\");";

                    $result .= "
                                <li><a href='#' id='id_href_".$codtab."' onclick='".$funcionesJS."' >
                                        <span>".$nombre_tab.$indica_submenu."</span>
                                    </a>";

                    $result .= pintarMenuSeleccion($conex, $arbol[$key], $arbol_info, $divs_include, $modoImprimir, $key, '', $idx);
                    $result .= '
                                </li>';
                }
            }
            elseif($modoImprimir == 'solo_arbol')
            {
                $id_padre = ($codmenu == '') ? 'padre_' : $padre.'_';
                $result .= "<div style='color:#2A5DB0; font-weight:bold; font-size: 9pt;  background-color: #F4F4F4; border-bottom: 1px #ffffff solid; margin-left: ".$sangria."px;'>&nbsp; ".$arbol_info[$key]['info']['nombre_subtab'].'</div>';
                $result .= pintarMenuSeleccion($conex, $arbol[$key], $arbol_info, $divs_include, $modoImprimir, $key, $sangria+20, $id_padre.$key);
            }
        }
        if($modoImprimir == 'tabs' && $padre == '') { $result .= "</ul>"; }
        elseif($modoImprimir == 'tabs' && $padre != '')
        {
            $result .= "
                    </ul>";
        }
        elseif($modoImprimir == 'checks' && count($propiedades) > 0 && $propiedad_ok != '')
        {
            // echo '<pre>'; print_r($propiedades[$propiedad_ok]); echo '</pre>';
            $result .= "<div id='inds_".$padre."'>";
            foreach($propiedades[$propiedad_ok] as $id_propiedad => $indicadores_list)
            {
                $result .= "
                            <div class='noms_propiedad' style='font-size:8pt;color:gray;font-weight:bold;margin-left: ".($sangria)."px;'>
                                ".$indicadores_list['nombre_grupo']."<br />";
                foreach($indicadores_list['indicadores'] as $id_indicador => $html_chk )
                {
                    $result .= $html_chk;
                }
                $result .= "</div>";
            }
            $result .= "</div>";
        }
    }
    return $result;
}

/**
 * Este es un buscador para centros de costos
 *
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param string $id_padre     : Es el id o parte del nombre de lo que se está buscando
 * @param string $especifico   : Si este parámetro está chequeado se retornará un checkbox chequeado dentro de un div.
 * @return html: retorna opctions para un select o un ckeckbox dentro de un div.
 */
function getOptionsCostos($wemp_pmla, $conex, $wbasedato, $id_padre, $especifico = '', $add_todos = 'on')
{
    $options = "";
    if(trim(strtolower($id_padre)) != '*')
    {
        $q = "  SELECT  Empdes,Emptcc
                FROM    root_000050
                WHERE   Empcod = '".$wemp_pmla."'";
        $res = mysql_query($q,$conex);

        if($especifico == '')
        {
            $options = '<option value="" >Seleccione..</option>';
            if($add_todos == 'on')
            {
                $options .= '<option value="*" >[ - TODOS - ]</option>';
            }
        }

        if($row = mysql_fetch_array($res))
        {
            $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
            $buscaNombre = strtoupper(strtolower($buscaNombre));

            $clisur_000003 = array('inner'=>'','filtro'=>'');
            $farstore_000003 = array('inner'=>'','filtro'=>'');
            $costosyp_000005 = array('inner'=>'','filtro'=>'');
            $uvglobal_000003 = array('inner'=>'','filtro'=>'');

            if($wbasedato != '')
            {
                $clisur_000003['inner'] = "INNER JOIN
                                           ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)";
                $clisur_000003['filtro'] = "OR tb1.Ccocod LIKE '".trim($buscaNombre)."'";

                $farstore_000003['inner'] = "INNER JOIN
                                           ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)";
                $farstore_000003['filtro'] = "OR tb1.Ccocod LIKE '".trim($buscaNombre)."'";

                $costosyp_000005['inner'] = "INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)";
                $costosyp_000005['filtro'] = "OR tb1.Ccocod LIKE '".trim($buscaNombre)."'";

                $uvglobal_000003['inner'] = "INNER JOIN
                                            ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)";
                $uvglobal_000003['filtro'] = "OR tb1.Ccocod LIKE '".trim($buscaNombre)."'";
            }

            $tabla_CCO = $row['Emptcc'];
            switch ($tabla_CCO)
            {
                case "clisur_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    clisur_000003 AS tb1
                                            ".$clisur_000003['inner']."
                                    WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                            ".$clisur_000003['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                case "farstore_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    farstore_000003 AS tb1
                                            ".$farstore_000003['inner']."
                                    WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                            ".$farstore_000003['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                case "costosyp_000005":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                    FROM    costosyp_000005 AS tb1
                                            ".$costosyp_000005['inner']."
                                    WHERE   tb1.Cconom LIKE '%".trim($buscaNombre)."%'
                                            ".$costosyp_000005['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Cconom";
                        break;
                case "uvglobal_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    uvglobal_000003 AS tb1
                                            ".$uvglobal_000003['inner']."
                                    WHERE   tb1.Ccodes LIKE '%".trim($buscaNombre)."%'
                                            ".$uvglobal_000003['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                default:
                        $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                    FROM    costosyp_000005 AS tb1
                                            ".$costosyp_000005['inner']."
                                    WHERE   tb1.Cconom LIKE '%".trim($buscaNombre)."%'
                                            ".$costosyp_000005['filtro']."
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Cconom";
            }

            $res = mysql_query($query,$conex);

            if($especifico != '')
            {
                if(mysql_num_rows($res) > 0)
                {
                    $row = mysql_fetch_array($res);
                    $options = $row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre'])));
                    $options = "
                            <div id='div_ckc_cco_".$row['codigo']."' class='fila2' style='border-top: 2px solid #ffffff;'>
                                <input type='checkbox' id='wccostos_pfls_".$row['codigo']."' name='wccostos_pfls_chk[".$row['codigo']."]' value='".$row['codigo']."' checked='checked' onClick='desmarcarRemover(\"wccostos_pfls_".$row['codigo']."\",\"div_ckc_cco_".$row['codigo']."\",\"div_adds_costos\");' >&nbsp;".$options."
                            </div>";
                }
                else
                {
                    $options = '';
                }
            }
            else
            {
                while($row = mysql_fetch_array($res))
                {
                    $options .= '<option value="'.$row['codigo'].'" >'.$row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre']))).'</option>';
                }
            }
        }
    }
    else
    {
        $options = "
                <div id='div_ckc_cco_todos' class='fila2' style='border-top: 2px solid #ffffff;'>
                    <input type='checkbox' id='wccostos_pfls_todos' name='wccostos_pfls_chk[todos]' value='*' checked='checked' onClick='desmarcarRemover(\"wccostos_pfls_todos\",\"div_ckc_cco_todos\",\"div_adds_costos\");' >&nbsp;[ - TODOS - ]
                </div>";
    }
    return $options;
}


/**
 * Este es un buscador para cargos pero retorna el result o Indice de la consulta
 *
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param unknown $arr_ids  : si se están buscando varios ids a la vez.
 * @return id#: indice de la consulta
 */
function consultarCentroCostos($wemp_pmla, $conex, $wbasedato, $arr_ids)
{
    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    if($row = mysql_fetch_array($res))
    {
        if(is_array($arr_ids))
        {
            $ids_in = implode("','",$arr_ids);
        }
        else
        { $ids_in = str_replace(",","','",trim($arr_ids)); }

        $tabla_CCO = $row['Emptcc'];
        switch ($tabla_CCO)
        {
            case "clisur_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    clisur_000003 AS tb1
                                        INNER JOIN
                                        ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                WHERE   tb1.Ccocod IN ('".trim($ids_in)."')
                                GROUP BY    tb1.Ccocod
                                ORDER BY    tb1.Ccodes";
                    break;
            case "farstore_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    farstore_000003 AS tb1
                                        INNER JOIN
                                        ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                WHERE   tb1.Ccocod IN ('".trim($ids_in)."')
                                GROUP BY    tb1.Ccocod
                                ORDER BY    tb1.Ccodes";
                    break;
            case "costosyp_000005":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    costosyp_000005 AS tb1
                                        INNER JOIN
                                        ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                WHERE   tb1.Ccocod IN ('".trim($ids_in)."')
                                GROUP BY    tb1.Ccocod
                                ORDER BY    tb1.Cconom";
                    break;
            case "uvglobal_000003":
                    $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                FROM    uvglobal_000003 AS tb1
                                        INNER JOIN
                                        ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                WHERE   tb1.Ccocod IN ('".trim($ids_in)."')
                                GROUP BY    tb1.Ccocod
                                ORDER BY    tb1.Ccodes";
                    break;
            default:
                    $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                FROM    costosyp_000005 AS tb1
                                        INNER JOIN
                                        ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                WHERE   tb1.Ccocod IN ('".trim($ids_in)."')
                                GROUP BY    tb1.Ccocod
                                ORDER BY    tb1.Cconom";
        }
        $res = mysql_query($query,$conex);

        if(mysql_num_rows($res) > 0){}
        else
        { $res = false; }

    }
    else
    { $res = false; }
    return $res;
}

/**
 * Este es un buscador para cargos
 *
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param string $id_padre     : Es el id o parte del nombre de lo que se está buscando
 * @param string $especifico   : Si este parámetro está chequeado se retornará un checkbox chequeado dentro de un div.
 * @return html: retorna opctions para un select o un ckeckbox dentro de un div.
 */
function getOptionsCargos($wemp_pmla, $conex, $wbasedato, $id_padre, $especifico = '', $add_todos = 'on')
{
    $optionsCar = '';
    if(trim(strtolower($id_padre)) != '*')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));

        $q = "  SELECT  Carcod AS codigo, Cardes AS nombre
                FROM    root_000079
                WHERE   Cardes LIKE '%".$buscaNombre."%'
                        OR Carcod LIKE '".$buscaNombre."'
                ORDER BY Cardes";
        $res = mysql_query($q,$conex);

        if($especifico == '')
        {
            $optionsCar = '<option value="" >Seleccione..</option>';
            if($add_todos == 'on')
            {
                $optionsCar .= '<option value="*" >[ - TODOS - ]</option>';
            }
        }

        if($especifico != '')
        {
            if(mysql_num_rows($res) > 0)
            {
                $row = mysql_fetch_array($res);
                $optionsCar = $row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre'])));
                $optionsCar = "
                        <div id='div_ckc_ccg_".$row['codigo']."' class='fila2' style='border-top: 2px solid #ffffff;'>
                            <input type='checkbox' id='wcccargo_pfls_".$row['codigo']."' name='wcccargo_pfls_chk[".$row['codigo']."]' value='".$row['codigo']."' checked='checked' onClick='desmarcarRemover(\"wcccargo_pfls_".$row['codigo']."\",\"div_ckc_ccg_".$row['codigo']."\",\"div_load_chk_cargo\");' >&nbsp;".$optionsCar."
                        </div>";
            }
            else
            {
                $optionsCar = '';
            }
        }
        else
        {
            while($row = mysql_fetch_array($res))
            {
                $optionsCar .= '<option value="'.$row['codigo'].'" >'.$row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre']))).'</option>';
            }
        }
    }
    else
    {
        $optionsCar = "
                <div id='div_ckc_ccg_todos' class='fila2' style='border-top: 2px solid #ffffff;'>
                    <input type='checkbox' id='wcccargo_pfls_todos' name='wcccargo_pfls_chk[todos]' value='*' checked='checked' onClick='desmarcarRemover(\"wcccargo_pfls_todos\",\"div_ckc_ccg_todos\",\"div_load_chk_cargo\");' >&nbsp;[ - TODOS - ]
                </div>";
    }
    return $optionsCar;
}

/**
 * Este es un buscador para cargos pero retorna el result o Indice de la consulta
 *
 * $arr_ids = array(0=>'01',2=>'02','3'=>'20',...) ó $arr_ids = 01,02,03,A,B,...
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param unknown $arr_ids
 * @return id#: indice de la consulta
 */
function consultarCargos($wemp_pmla, $conex, $wbasedato, $arr_ids)
{
    if(is_array($arr_ids))
    {
        $ids_in = implode("','",$arr_ids);
    }
    else
    { $ids_in = str_replace(",","','",trim($arr_ids)); }

    $q = "  SELECT  Carcod AS codigo, Cardes AS nombre
            FROM    root_000079
            WHERE   Carcod IN ('".$ids_in."')
            ORDER BY Cardes";
    $res = mysql_query($q,$conex);

    if(mysql_num_rows($res) > 0)
    {    }
    else
    { $res = false; }

    return $res;
}

/**
 * Este es un buscador para usuarios pero retorna el result o Indice de la consulta
 *
 * $arr_ids = array(0=>'01',2=>'02','3'=>'20',...) ó $arr_ids = 01,02,03,A,B,...
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param unknown $arr_ids
 * @return id#: indice de la consulta
 */
function consultarUsuarios($wemp_pmla, $conex, $wbasedato, $arr_ids)
{
    if(is_array($arr_ids))
    {
        $ids_in = implode("','",$arr_ids);
    }
    else
    { $ids_in = str_replace(",","','",trim($arr_ids)); }

    $q = "  SELECT  Ideuse AS codigo, CONCAT (Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) AS nombre
            FROM    ".$wbasedato."_000013
            WHERE   Ideuse IN ('".$ids_in."')";
    $res = mysql_query($q,$conex);

    if(mysql_num_rows($res) > 0)
    {    }
    else
    { $res = false; }

    return $res;
}

/**
 * Esta funcion hace una busqueda sobre la tabla de empleados buscando una coincidencia por el campo de código o por el campo de nombres y apellidos
 * Retorna elementos opctions que van a ser usados en un campo tipo select
 *
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param string $id_padre      : Es el código o parte del nombre que se está buscando.
 * @param string $especifico
 * @return html: options de usuarios.
 */
function getOptionsUsers($wemp_pmla, $conex, $wbasedato, $id_padre, $especifico = '')
{
    $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
    $buscaNombre = strtoupper(strtolower($buscaNombre));

    $q = "  SELECT  Ideuse AS codigo, Ideno1 AS nombre1, Ideno2 AS nombre2, Ideap1 AS apellido1, Ideap2 AS apellido2
            FROM    ".$wbasedato."_000013
            WHERE   Ideest = 'on'
                    AND (CONCAT (Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) LIKE '%".$buscaNombre."%')
                    OR Ideuse  LIKE '".$buscaNombre."'
            ORDER BY    Ideno1, Ideno2, Ideap1, Ideap2";
    $res = mysql_query($q,$conex);

    $options = '';
    if($especifico == '') { $options = '<option value="" >Seleccione..</option>'; }

    if($especifico != '')
    {
        if(mysql_num_rows($res) > 0)
        {
            $row = mysql_fetch_array($res);
            $n_empleado = strtoupper(strtolower(trim($row['nombre1'].' '.$row['nombre2'].' '.$row['apellido1'].' '.$row['apellido2'])));
            $options = $row['codigo'].' - '.utf8_encode(strtoupper(strtolower($n_empleado)));
            $options = "
                    <div id='div_ckc_user_".$row['codigo']."' class='fila2' style='border-top: 2px solid #ffffff;'>
                        <input type='checkbox' id='wuse_pfls_".$row['codigo']."' name='wuse_pfls_chk[".$row['codigo']."]' value='".$row['codigo']."' checked='checked' onClick='desmarcarRemover(\"wuse_pfls_".$row['codigo']."\",\"div_ckc_user_".$row['codigo']."\",\"div_load_chk_users\");' >&nbsp;".$options."
                    </div>";
        }
        else
        {
            $options = '';
        }
    }
    else
    {
        while($row = mysql_fetch_array($res))
        {
            $n_empleado = strtoupper(strtolower(trim($row['nombre1'].' '.$row['nombre2'].' '.$row['apellido1'].' '.$row['apellido2'])));
            $options .= '<option value="'.$row['codigo'].'" >'.$row['codigo'].' - '.utf8_encode($n_empleado).'</option>';
        }
    }
    return $options;
}

/**
 * Este es un buscador de menús por nombre.
 *
 * @param unknown $wemp
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param string $id_padre
 * @param unknown $especifico
 * @return html: retorna options para un select de nombres de menús
 */
function getOptionsMenus($wemp_pmla, $conex, $wbasedato, $id_padre, $wtema = '', $especifico = '', $add_todos = 'on')
{
    $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
    $buscaNombre = strtoupper(strtolower($buscaNombre));

    $q = "
            SELECT  Tabcod AS codigo, Tabdes  AS nombre
            FROM    root_000080
            WHERE   Tabdes LIKE '%".$buscaNombre."%'
                    OR Tabcod = '".$buscaNombre."'";

    $res = mysql_query($q,$conex);

    $options = '';
    if($especifico == '') { $options = '<option value="" >Seleccione..</option>'; }

    if($especifico != '')
    {
        if(mysql_num_rows($res) > 0)
        {
            $row = mysql_fetch_array($res);
            $options = $row['codigo'].' - '.utf8_encode(strtoupper(strtolower($row['nombre'])));
            $options = "
                    <div id='div_ckc_cdmenu_".$row['codigo']."' class='fila2' style='border-top: 2px solid #ffffff;'>
                        <input type='checkbox' id='wcdmenu_pfls_".$row['codigo']."' name='wcdmenu_pfls_chk[".$row['codigo']."]' value='".$row['codigo']."' checked='checked' onClick='desmarcarRemover(\"wcdmenu_pfls_".$row['codigo']."\",\"div_ckc_cdmenu_".$row['codigo']."\",\"div_load_chk_cdmenu\");' >&nbsp;".$options."
                    </div>";
        }
        else
        {
            $options = '';
        }
    }
    else
    {
        while($row = mysql_fetch_array($res))
        {
            $mnu = strtoupper(strtolower(trim($row['nombre'])));
            $options .= '<option value="'.$row['codigo'].'" >'.$row['codigo'].' - '.utf8_encode($mnu).'</option>';
        }
    }
    return $options;
}

/**
 * Esta función es la encargada de buscar el prefijo para el módulo de talento humano inicialmente. según el código de un tema
 * se busca el alias o prefijo con el que se deben buscar las tablas para este módulo.
 *
 * @param unknown $conex
 * @param unknown $wemp_pmla
 * @param sring $wtema
 * @return string
 */
function consultarPrefijo($conex, $wemp_pmla, $wtema)
{
    // Información de usuario
    $sqlPr = "  SELECT  Temprf AS prefijo
                FROM    root_000076
                WHERE   Tememp = '".$wemp_pmla."'
                        AND Temcod = '".$wtema."'";
    $resPr = mysql_query($sqlPr,$conex) or die("Error: " . mysql_errno() . " - en el query consultar prefijo: ".$sqlPr." - ".mysql_error());

    $wbasedato = '';
    if(mysql_num_rows($resPr) > 0)
    {
        $rowPr = mysql_fetch_array($resPr);
        $wbasedato = $rowPr['prefijo'];
    }

    if($wbasedato == '')
    {
        echo "[?] No existe alias o prefijo para esta aplicaci&oacute;n, no se ha definido wtema!!";
        exit();
    }
    return $wbasedato;
}


/**
 * Esta función es la encargada de buscar el nombre del grupo o carpeta donde encontrar los scripts del módulo. según el código de un tema.
 * La función retorna un array con el nombre del grupo o directorio donde estan los scripts, el color característico del tema y el nombre del logo del tema o empresa.
 *
 *
 * @param unknown $conex
 * @param unknown $wemp_pmla
 * @param sring $wtema
 * @return string
 */
function consultarAliasGrupo($conex, $wemp_pmla, $wtema)
{
    $sqlPr = "  SELECT  Temgru AS grupo, Temccm AS wcolormenu, Temnlg AS wnombre_logo
                FROM    root_000076
                WHERE   Tememp = '".$wemp_pmla."'
                        AND Temcod = '".$wtema."'";
    $resPr = mysql_query($sqlPr,$conex) or die("Error: " . mysql_errno() . " - en el query consultar prefijo: ".$sqlPr." - ".mysql_error());

    $wgrupo = array("grupo"=>"","wcolormenu"=>"#2A5DB0","wnombre_logo"=>"clinica");
    if(mysql_num_rows($resPr) > 0)
    {
        $rowPr = mysql_fetch_array($resPr);
        $wgrupo['grupo']        = $rowPr['grupo'];
        $wgrupo['wcolormenu']   = $rowPr['wcolormenu'];
        $wgrupo['wnombre_logo'] = $rowPr['wnombre_logo'];
    }

    if($wgrupo == '')
    {
        echo "[?] No existe alias o grupo de programas para esta aplicaci&oacute;n, o no se ha definido wtema!!";
        exit();
    }
    return $wgrupo;
}

/**
 * Esta función se encarga de buscar todos los permisos que tiene asignado un empleado al momento de ingresar a una opción de menú
 * Esta función retorna el tipo de permiso que va a tener el usuario para posteriormente dejarle ver o no algunas funcionalidades de cada programa
 *
 *
 * @param unknown $conex
 * @param unknown $wemp_pmla
 * @param unknown $wtema
 * @param unknown $wcodtab      : Es el código de la pestaña a la que se esta accediendo.
 * @param unknown $wcod_use_ver : El es código del usuario al que se pretende ver.
 * @param unknown $wcodcargo_ver: El es código del cargo del usuario al que se pretende acceder.
 * @param unknown $wcodcosto_ver: El es código del centro de costo del usuario al que se pretende acceder.
 * @param unknown $user_session : Es el código del usuario que está intentando solicitar el permiso para ver información.
 * @return unknown
 */
function consultarPermisosAdicionales($conex, $wemp_pmla, $wtema, $wcodtab, $wcod_use_ver, $wcodcargo_ver, $wcodcosto_ver, $user_session)
{
    $user_session = empresaEmpleado($wemp_pmla, $conex, '', $user_session);
    $sql = "
            SELECT  per.Prfacc AS permiso, per.Prfadm AS esAdmin
            FROM    root_000082 AS per
                    INNER JOIN
                    root_000076 AS tem ON (tem.Temcod = per.Prftem AND tem.Temest = 'on' AND tem.Tememp = '".$wemp_pmla."')
            WHERE   per.Prfuse = '".$user_session."'
                    AND per.Prfest = 'on'
                    AND per.Prftem = '".$wtema."'
                    AND per.Prftab = '".$wcodtab."'
                    AND (   (
                            per.Prfccg = '' AND per.Prfcco <> ''
                            AND (per.Prfcco = '*' OR per.Prfcco = '".$wcodcosto_ver."')
                            )
                            OR  (
                                per.Prfccg <> '' AND per.Prfcco = ''
                                AND (per.Prfccg = '".$wcodcargo_ver."' OR per.Prfccg = '*')
                            )
                            OR  (
                                per.Prfccg <> '' AND per.Prfcco <> ''
                                AND (per.Prfccg = '*' AND per.Prfcco = '*')
                                OR  (per.Prfccg = '".$wcodcargo_ver."' AND per.Prfcco = '*')
                                OR  (per.Prfccg = '".$wcodcargo_ver."' AND per.Prfcco = '".$wcodcosto_ver."')
                                OR  (per.Prfccg = '*' AND per.Prfcco = '".$wcodcosto_ver."')
                            )
                        )
            GROUP BY Prftem, Prftab, Prfcco, Prfccg, Prfacc
            HAVING MAX(per.id)";

    /*
        Con esta confifuracion no se podía ver lo de un solo cargo o lo de un centro de costos (en cuanto a permisos para ver lo de otros)

        "
            SELECT  per.Prfacc AS permiso, per.Prfadm AS esAdmin
            FROM    root_000082 AS per
                    INNER JOIN
                    root_000076 AS tem ON (tem.Temcod = per.Prftem AND tem.Temest = 'on' AND tem.Tememp = '".$wemp_pmla."')
            WHERE   per.Prfuse = '".$user_session."'
                    AND per.Prfest = 'on'
                    AND per.Prftem = '".$wtema."'
                    AND per.Prftab = '".$wcodtab."'
                    AND (   per.Prfccg <> '' AND per.Prfcco <> ''
                            AND (per.Prfccg = '*' AND per.Prfcco = '*')
                            OR  (per.Prfccg = '".$wcodcargo_ver."' AND per.Prfcco = '*')
                            OR  (per.Prfccg = '".$wcodcargo_ver."' AND per.Prfcco = '".$wcodcosto_ver."')
                            OR  (per.Prfccg = '*' AND per.Prfcco = '".$wcodcosto_ver."')
                        )
            GROUP BY Prftem, Prftab, Prfcco, Prfccg, Prfacc
            HAVING MAX(per.id)";
    */
    $res = mysql_query($sql,$conex) or die("Error: " . mysql_errno() . " - en el query consultar permisos para ver otros usuarios: ".$sql." - ".mysql_error());

    $permisos = array('permiso'=>'ninguno', 'esAdmin'=>'off');
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $permisos['permiso'] = $row['permiso'];
        $permisos['esAdmin'] = $row['esAdmin'];
    }
    return $permisos;
}


/**
 * Busca si para un usuario hay permiso de administrador a una pestaña de un tema determinado.
 *
 * @param unknown $conex
 * @param unknown $wemp_pmla
 * @param unknown $wtema
 * @param unknown $wcodtab
 * @param unknown $user
 * @return unknown
 */
function consultarSiEsAdmin($conex, $wemp_pmla, $wtema, $wcodtab, $user_session)
{
    $user_session = empresaEmpleado($wemp_pmla, $conex, '', $user_session);
    $sql = "
            SELECT  per.Prfacc AS permiso, per.Prfadm AS esAdmin
            FROM    root_000082 AS per
                    INNER JOIN
                    root_000076 AS tem ON (tem.Temcod = per.Prftem AND tem.Temest = 'on' AND tem.Tememp = '".$wemp_pmla."')
            WHERE   per.Prfuse = '".$user_session."'
                    AND per.Prfest = 'on'
                    AND per.Prftem = '".$wtema."'
                    AND per.Prftab = '".$wcodtab."'
                    AND per.Prfadm = 'on'
            GROUP BY Prftem, Prftab, Prfcco, Prfccg, Prfacc
            HAVING MAX(per.id)";

    $res = mysql_query($sql,$conex) or die("Error: " . mysql_errno() . " - en el query consultar permisos para ver otros usuarios: ".$sql." - ".mysql_error());

    $permisos = array();
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $permisos['permiso'] = $row['permiso'];
        $permisos['esAdmin'] = $row['esAdmin'];
    }
    return $permisos;
}

/**
 * Genera código html para mostrar la lista de permisos para un centro de costos, cargo o usuario.
 *
 * @param unknown $conex
 * @param unknown $wemp_pmla
 * @param unknown $wtema
 * @param unknown $res_detll
 * @return unknown
 */
function generarDetalle($wemp_pmla, $conex, $wbasedato, $wtema, $res_detll, $tipo, $msjVista = '', $msjPermiso = '')
{
    $detalle = "";
    $detallePermiso = "";
    $filaCnt = 0;
    $filaCnt2 = 0;
    if (mysql_num_rows($res_detll))
    {
        $detalle .= "
                    <table align='center' style='font-size:8pt;'>
                        <tr class='encabezadoTabla'>
                            <td align='center' colspan='8'>Detalle perfil</td>
                        </tr>
                        <tr class='encabezadoTabla'>
                            <td align='center'>Tema</td>
                            <td align='center'>Pesta&ntilde;a</td>
                            <td align='center'>Permiso</td>
                            <td align='center'>Administrador</td>
                            <td align='center'>Centro costo</td>
                            <td align='center'>Cargo</td>
                            <td align='center'>Est.</td>
                            <td align='center'>Eliminar</td>
                            <!-- <td align='center'>Id.</td> -->
                        </tr>
                        ";

        $detallePermiso = "
                    <table align='center' style='font-size:8pt;'>
                        <tr class='encabezadoTabla'>
                            <td align='center' colspan='9'>Detalle perfil por usuario espec&iacute;fico (Permisos para ver informaci&oacute;n de otros)</td>
                        </tr>
                        <tr class='encabezadoTabla'>
                            <td align='center'>Tema</td>
                            <td align='center'>Pesta&ntilde;a</td>
                            <td align='center'>Permiso</td>
                            <td align='center'>Administrador</td>
                            <td align='center'>Centro costo</td>
                            <td align='center'>Cargo</td>
                            <td align='center'>Usuario</td>
                            <td align='center'>Est.</td>
                            <td align='center'>Eliminar</td>
                            <!-- <td align='center'>Id.</td> -->
                        </tr>";

        $arr_ccostos = array();
        $arr_ccargos = array();
        $arr_users = array();
        while($row = mysql_fetch_array($res_detll))
        {
            $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
            $filaCP = ($filaCnt2%2 == 0) ? 'fila1': 'fila2';
            $arr_ccostos[$row['cod_costo']] = $row['cod_costo'];
            $arr_ccargos[$row['cod_cargo']] = $row['cod_cargo'];
            $arr_users[$row['usuario']] = $row['usuario'];
            $admin_chk = ($row['esAdmin'] == 'on') ? 'checked="checked"': '';
            $std_chk = ($row['estado'] == 'on') ? 'checked="checked"': '';
            $id_reg = $row['id_reg'];

            $options_permiso = '';
            switch($row['permiso'])
            {
                case 'consultar'    :
                                $options_permiso .= '
                                    <option value="consultar" selected="selected">Consultar</option>
                                    <option value="modificar">Modificar</option>
                                    <option value="ninguno">Ninguno</option>';
                                break;
                case 'modificar'    :
                                $options_permiso .= '
                                    <option value="consultar">Consultar</option>
                                    <option value="modificar" selected="selected">Modificar</option>
                                    <option value="ninguno">Ninguno</option>';
                                break;
                default:
                                $options_permiso .= '
                                    <option value="consultar">Consultar</option>
                                    <option value="modificar">Modificar</option>
                                    <option value="ninguno" selected="selected">Ninguno</option>';
                                break;
            }

            $id_compuesta = $tipo.'_'.$id_reg;
            if($row['usuario'] == '')
            {
                $detalle .= "
                            <tr class='".$filaC."' id='row_".$id_compuesta."'>
                                <td align='left' class='tablaDetalle'>".utf8_encode($row['nom_tema'])."</td>
                                <td align='left' class='tablaDetalle'>".utf8_encode($row['nombre_subtab'])."</td>
                                <td align='center' class='tablaDetalle'>
                                        ".ucfirst($row['permiso'])."
                                        <!--<select disabled='disabled' id='permiso_".$id_compuesta."' name='permiso_".$id_compuesta."' rel='Prfacc' onChange='updateCampo(\"permiso_".$id_compuesta."\",\"".$id_reg."\");'>
                                            ".$options_permiso."
                                        </select>-->
                                </td>
                                <td align='center' class='tablaDetalle'><input disabled='disabled' type='checkbox' rel='Prfadm' id='admin_".$id_compuesta."' name='admin_".$id_compuesta."' ".$admin_chk." value='".$row['esAdmin']."' onclick='updateCampo(\"admin_".$id_compuesta."\",\"".$id_reg."\");'></td>
                                <td align='left' class='tablaDetalle'>[CCO*".utf8_encode($row['cod_costo'])."*]</td>
                                <td align='left' class='tablaDetalle'>[CCG*".utf8_encode($row['cod_cargo'])."*]</td>
                                <td align='center' class='tablaDetalle'><input type='checkbox' rel='Prfest' id='est_".$id_compuesta."' name='est_".$id_compuesta."' ".$std_chk." value='".$row['estado']."' onclick='updateCampo(\"est_".$id_compuesta."\",\"".$id_reg."\");'></td>
                                <td align='center' class='tablaDetalle'><span id='span_".$id_compuesta."' style='color: orange;' onMouseOver='onOverDelete(\"span_".$id_compuesta."\");' onMouseOut='onOutDelete(\"span_".$id_compuesta."\");' onClick=\"funcionesConfirmDelete('span_".$id_compuesta."','".$id_reg."','borrar_reg','delete','row_".$id_compuesta."');\">Eliminar</span>
                                                                        <!--<input type='checkbox' id='delt_".$id_compuesta."' name='delt_".$id_compuesta."' ".$std_chk." value='".$id_reg."'>--></td>
                                <!-- <td align='left' class='tablaDetalle'>".$id_reg."</td> -->
                            </tr>
                            ";
                $filaCnt++;
            }
            else
            {
                $detallePermiso .= "
                            <tr class='".$filaCP."' id='row_".$id_compuesta."'>
                                <td align='left' class='tablaDetalle'>".utf8_encode($row['nom_tema'])."</td>
                                <td align='left' class='tablaDetalle'>".utf8_encode($row['nombre_subtab'])."</td>
                                <td align='center' class='tablaDetalle'>
                                        <select id='permiso_".$id_compuesta."' name='permiso_".$id_compuesta."' rel='Prfacc' onChange='updateCampo(\"permiso_".$id_compuesta."\",\"".$id_reg."\");'>
                                            ".$options_permiso."
                                        </select>
                                </td>
                                <td align='center' class='tablaDetalle'><input type='checkbox' rel='Prfadm' id='admin_".$id_compuesta."' name='admin_".$id_compuesta."' ".$admin_chk." value='".$row['esAdmin']."' onclick='updateCampo(\"admin_".$id_compuesta."\",\"".$id_reg."\");'></td>
                                <td align='left' class='tablaDetalle'>[CCO*".utf8_encode($row['cod_costo'])."*]</td>
                                <td align='left' class='tablaDetalle'>[CCG*".utf8_encode($row['cod_cargo'])."*]</td>
                                <td align='left' class='tablaDetalle'>[USE*".utf8_encode($row['usuario'])."*]</td>
                                <td align='center' class='tablaDetalle'><input type='checkbox' rel='Prfest' id='est_".$id_compuesta."' name='est_".$id_compuesta."' ".$std_chk." value='".$row['estado']."' onclick='updateCampo(\"est_".$id_compuesta."\",\"".$id_reg."\");'></td>
                                <td align='center' class='tablaDetalle'><span id='span_".$id_compuesta."' style='color: orange;' onMouseOver='onOverDelete(\"span_".$id_compuesta."\");' onMouseOut='onOutDelete(\"span_".$id_compuesta."\");' onClick='funcionesConfirmDelete(\"span_".$id_compuesta."\",\"".$id_reg."\",\"borrar_reg\",\"delete\",\"row_".$id_compuesta."\");'>Eliminar</span></td>
                                <!-- <td align='left' class='tablaDetalle'>".$id_reg."</td> -->
                            </tr>
                            ";
                $filaCnt2++;
            }
        }

        $detalle = str_replace("[CCO**]", '&nbsp;', $detalle);
        $detallePermiso = str_replace("[CCO**]", '&nbsp;', $detallePermiso);
        $detalle = str_replace("[CCO***]", '[TODOS]', $detalle);
        $detallePermiso = str_replace("[CCO***]", '[TODOS]', $detallePermiso);
        if(count($arr_ccostos) > 0)
        {
            $resCosto = consultarCentroCostos($wemp_pmla, $conex, $wbasedato, $arr_ccostos); // $row['codigo'], $row['nombre']

            if($resCosto != false)
            {
                while($row = mysql_fetch_array($resCosto))
                {
                    $detalle = str_replace("[CCO*".$row['codigo']."*]", $row['codigo'].' - '.utf8_encode($row['nombre']), $detalle);
                    $detallePermiso = str_replace("[CCO*".$row['codigo']."*]", $row['codigo'].' - '.utf8_encode($row['nombre']), $detallePermiso);
                }
            }
        }

        $detalle = str_replace("[CCG**]", '&nbsp;', $detalle);
        $detallePermiso = str_replace("[CCG**]", '&nbsp;', $detallePermiso);
        $detalle = str_replace("[CCG***]", '[TODOS]', $detalle);
        $detallePermiso = str_replace("[CCG***]", '[TODOS]', $detallePermiso);
        if(count($arr_ccargos) > 0)
        {
            $resCargo = consultarCargos($wemp_pmla, $conex, $wbasedato, $arr_ccargos); // $row['codigo'], $row['nombre']

            if($resCargo != false)
            {
                while($row = mysql_fetch_array($resCargo))
                {
                    $detalle = str_replace("[CCG*".$row['codigo']."*]", $row['codigo'].' - '.utf8_encode($row['nombre']), $detalle);
                    $detallePermiso = str_replace("[CCG*".$row['codigo']."*]", $row['codigo'].' - '.utf8_encode($row['nombre']), $detallePermiso);
                }
            }
        }

        $detalle = str_replace("[USE**]", '&nbsp;', $detalle);
        $detallePermiso = str_replace("[USE**]", '&nbsp;', $detallePermiso);
        if(count($arr_users) > 0)
        {
            $resUser = consultarUsuarios($wemp_pmla, $conex, $wbasedato, $arr_users); // $row['codigo'], $row['nombre']

            if($resUser != false)
            {
                while($row = mysql_fetch_array($resUser))
                {
                    $detalle = str_replace("[USE*".$row['codigo']."*]", trim(utf8_encode($row['nombre'])), $detalle); //$row['codigo'].' - '.
                    $detallePermiso = str_replace("[USE*".$row['codigo']."*]", trim(utf8_encode($row['nombre'])), $detallePermiso); //$row['codigo'].' - '.
                }
            }
        }

        $detalle .= "</table>";
        $detallePermiso .= "</table>";
    }

    if($filaCnt > 0 && $msjVista != '')
    {
        $detalle = $msjVista.$detalle;
    }
    else
    {
        $detalle = "";
        // $detalle = "<br />
                    // <div class='msj_explicativo' style='margin-left: 20px; text-align:center;'>LOS DEM&Aacute;S USUARIOS <font style='font-weight:bold;color:orange;'>NO TIENEN ACCESO</font> A LAS ANTERIORES OPCIONES DE MEN&Uacute.</div>";
    }

    if($filaCnt2 > 0)
    {
        $detallePermiso = $msjPermiso.$detallePermiso;
    }
    else
    {
        $detallePermiso = '';
    }

    $detalle_total = $detalle.$detallePermiso;

    return $detalle_total;
}

/**
 * Esta función se encarga de decir cuántos años meses y días hay entre un rando de fechas.
 *
 * @param unknown $fecha_inicio : Fecha inicial para hacer los calculos aaaa-mm-dd
 * @param unknown $fecha_fin    : Fecha final para hacer los calculos aaaa-mm-dd, si esta vacío la fecha final es la fecha actual del servidor.
 * @return array
 */
function calcularAnioMesesDiasTranscurridos($fecha_inicio, $fecha_fin = '')
{
    $datos = array('anios'=>0,'meses'=>0,'dias'=>0);

    if($fecha_inicio != '' && $fecha_inicio != '0000-00-00')
    {
        $fecha_de_nacimiento = $fecha_inicio;

        $fecha_actual = date ("Y-m-d");
        if($fecha_fin != '' && $fecha_fin != '0000-00-00')
        {
            $fecha_actual = $fecha_fin;
        }
        // echo "<br>Fecha final: $fecha_actual";
        // echo "<br>Fecha inicio: $fecha_de_nacimiento";

        // separamos en partes las fechas
        $array_nacimiento = explode ( "-", $fecha_de_nacimiento );
        $array_actual = explode ( "-", $fecha_actual );

        $anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años
        $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
        $dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días

        //ajuste de posible negativo en $días
        if ($dias < 0)
        {
            --$meses;

            //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
            switch ($array_actual[1]) {
                   case 1:     $dias_mes_anterior=31; break;
                   case 2:     $dias_mes_anterior=31; break;
                   case 3:
                        if (checkdate(2,29,$array_actual[0]))
                        {
                            $dias_mes_anterior=29; break;
                        } else {
                            $dias_mes_anterior=28; break;
                        }
                   case 4:     $dias_mes_anterior=31; break;
                   case 5:     $dias_mes_anterior=30; break;
                   case 6:     $dias_mes_anterior=31; break;
                   case 7:     $dias_mes_anterior=30; break;
                   case 8:     $dias_mes_anterior=31; break;
                   case 9:     $dias_mes_anterior=31; break;
                   case 10:     $dias_mes_anterior=30; break;
                   case 11:     $dias_mes_anterior=31; break;
                   case 12:     $dias_mes_anterior=30; break;
            }
            $dias=$dias + $dias_mes_anterior;
        }

        //ajuste de posible negativo en $meses
        if ($meses < 0)
        {
            --$anos;
            $meses=$meses + 12;
        }
        //echo "<br>Tu edad es: $anos años con $meses meses y $dias días";
        $datos['anios'] = $anos;
        $datos['meses'] = $meses;
        $datos['dias'] = $dias;
    }

    return $datos;
}
/*****************************************************************************************************************************************/

/**
 * retorna el nombre de un mes según el número de mes que llega como parámetro.
 *
 * @param unknown $m
 * @return unknown
 */
function getMonthText($m) {
    switch ($m)
   {
        case 1: $month_text = "Enero"; break;
        case 2: $month_text = "Febrero"; break;
        case 3: $month_text = "Marzo"; break;
        case 4: $month_text = "Abril"; break;
        case 5: $month_text = "Mayo"; break;
        case 6: $month_text = "Junio"; break;
        case 7: $month_text = "Julio"; break;
        case 8: $month_text = "Agosto"; break;
        case 9: $month_text = "Septiembre"; break;
        case 10: $month_text = "Octubre"; break;
        case 11: $month_text = "Noviembre"; break;
        case 12: $month_text = "Diciembre"; break;
        default: $month_text = "NO REGISTRA"; break;
    }
    return ($month_text);
}

/**
 * description...
 *
 * @param unknown $fechanacimiento
 * @return unknown
 */
function calculaEdad($fechanacimiento){
    list($ano,$mes,$dia) = explode("-",$fechanacimiento);
    $ano_diferencia  = date("Y") - $ano;
    $mes_diferencia = date("m") - $mes;
    $dia_diferencia   = date("d") - $dia;
    if ($dia_diferencia < 0 || $mes_diferencia < 0)
        $ano_diferencia--;
    return $ano_diferencia;
}

//FUNCIONES PARA ELIMINAR LOS NULL DE LO QUERYS A UNIX
function ejecutar_consulta ($query_campos, $query_from, $query_where, $llaves, $conexUnix )
{
        global $increment;
        $increment++;
        if ($query_where == NULL)
            $query =   " SELECT $query_campos FROM $query_from ";
        else
            $query =   " SELECT $query_campos FROM $query_from WHERE $query_where";

        $table= date("Mdhis").$increment;                //nombre de la tabla temporal
        $query=$query." into temp $table";              //creo la temporal con los resultados de la consulta que enviaron
        odbc_do($conexUnix,$query) or die( odbc_error()." - $query - ".odbc_errormsg() );

        $query1= "select * from $table";
        $err_o1 = odbc_do($conexUnix,$query1) or die( odbc_error()." - $query1 - ".odbc_errormsg() ); // Consulto la tabla temporal

        while (odbc_fetch_row($err_o1)) //RECORRO CADA REGISTRO DE LA TEMPORAL
        {
            $n_llaves=count($llaves);
            for ($x=0; $x<$n_llaves ;$x++)
            {
            $pk_valor[$x]=odbc_result($err_o1, $llaves[$x]);
            $pk_nombre[$x]=odbc_field_name($err_o1, $llaves[$x]);
            }
            for($i=1;$i<=odbc_num_fields($err_o1);$i++)
            {

                $campo=odbc_field_name($err_o1,$i);
                $valor=odbc_result($err_o1,$i);
                validar_nulos($query_from, $query_where, $campo, $valor, $pk_nombre, $pk_valor, $conexUnix, $table);//ESTA FUNCION ACTUALIZA EL VALOR DEL CAMPO DE LA TEMPORAL, DEPENDIENDO DEL VALOR DE LA ORGINAL
            }

        }
        $query1="select * from $table ";
        $err_o1 = odbc_do($conexUnix,$query1) or die( odbc_error()." - $query1 - ".odbc_errormsg() ); // retornar la consulta sin null
        return $err_o1;
}

function validar_nulos($query_from, $query_where, $campo, $valor, $pk_nombre, $pk_valor, $conexUnix, &$table)
{
    if ($query_where == NULL)
    {
        $query_no_null = " SELECT $campo FROM $query_from";
        $query_no_null = $query_no_null." Where $campo is not null  ";
    }
    else
    {
        $query_no_null = " SELECT $campo FROM $query_from WHERE $query_where";
        $query_no_null = $query_no_null." AND ($campo is not null or $campo != '')  ";
    }

    for($y=0; $y<count($pk_valor); $y++ )
    {
        $query_no_null = $query_no_null." AND ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
    }
    //echo $query_no_null;
    $res_no_null = odbc_do($conexUnix, $query_no_null)or die( odbc_error()." - query no null: - ".odbc_errormsg() );


    if (odbc_fetch_row($res_no_null))
    {
        $valor_no_null = odbc_result($res_no_null, 1);
        $query4="update $table ";
        $query4=$query4."set $campo = '$valor_no_null'  ";
        $query4=$query4." WHERE ";
        for($y=0; $y<count($pk_valor); $y++ )
            {
                if ($y==0)
                $query4=$query4." ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
                else
                $query4=$query4." AND ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
            }
        $err_o4 = odbc_do($conexUnix,$query4)or die( odbc_error()." - UPDATE TEMP con valor original: - ".odbc_errormsg() );
    }
}

/**
 * Esta función se encarga de buscar algúna coincidencia en la tabla de empleados de los parámetros que llegan a la función
 *
 * @param unknown $conex
 * @param unknown $wemp_pmla
 * @param unknown $wbasedato
 * @param unknown $user_session
 * @param unknown $wcodigo
 * @param unknown $wced
 * @param unknown $wnombre1
 * @param unknown $wnombre2
 * @param unknown $wapellido1
 * @param unknown $wapellido2
 * @param unknown $wccostos
 * @param unknown $wccargo
 * @param unknown $wsexo
 * @param unknown $westado
 * @return unknown
 */
function buscarEnTalhuma($conex,$wemp_pmla,$wbasedato,$user_session,$wcodigo,$wced,$wnombre1,$wnombre2,$wapellido1,$wapellido2,$wccostos,$wccargo,$wsexo,$westado,$wfechaingreso,$wfecharetiro,$wingresoretiro)
{
    $empleado = array();

    $filtro = '';
    $and = '';
    if(trim($wcodigo) != '')
    {
        //$wcodigo = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $wcodigo);
        $filtro = " Ideuse LIKE '".$wcodigo."%' ";  // Se cambia a LIKE puesto que se puede buscar un código de empleado que exista en varias empresas, el final mostrará las coincidencias y la empresa a la que pertenece.
        $lista = explode('|',$wcodigo);
        if(count($lista) > 1)
        {
            $lista = implode("','",explode('|',$wcodigo));
            $filtro = " Ideuse IN ('".$lista."') ";
        }
        $and = 'AND';
    }

    if(trim($wced) != '')
    {
        $filtro .= "$and Ideced = '".$wced."' ";
        $and = 'AND';
    }

    if(trim($wnombre1) != '')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wnombre1))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));
        // $filtro .= "$and CONCAT(perno1,' ',perno2,' ',perap1,' ',perap2) LIKE '%$buscaNombre%'";
        $filtro .= "$and Ideno1 LIKE '%$buscaNombre%' ";
        $and = 'AND';
    }
    if(trim($wnombre2) != '')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wnombre2))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));
        $filtro .= "$and Ideno2 LIKE '%$buscaNombre%' ";
        $and = 'AND';
    }
    if(trim($wapellido1) != '')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wapellido1))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));
        $filtro .= "$and Ideap1 LIKE '%$buscaNombre%' ";
        $and = 'AND';
    }
    if(trim($wapellido2) != '')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wapellido2))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));
        $filtro .= "$and Ideap2 LIKE '%$buscaNombre%' ";
        $and = 'AND';
    }
    if(trim($wccostos) != '')
    {
        // $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wccostos))));
        $buscaNombre = trim($wccostos);
        // $buscaNombre = strtoupper(strtolower($buscaNombre));
        // $filtro .= "$and Idecco LIKE '%$buscaNombre%' ";
        $filtro .= "$and Idecco = '$buscaNombre' ";
        $and = 'AND';
    }
    if(trim($wccargo) != '')
    {
        // $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wccargo))));
        $buscaNombre = trim($wccargo);
        // $buscaNombre = strtoupper(strtolower($buscaNombre));
        $filtro .= "$and Ideccg = '$buscaNombre' ";
        $and = 'AND';
    }
    if($westado != '' && $westado != '*')
    {
        $west = ($westado == 'A') ? 'on': 'off';
        $filtro .= "$and Ideest = '$west' ";
        $and = 'AND';
    }
    if($wsexo != '' && $wsexo != '*')
    {
        $filtro .= "$and Idegen = '$wsexo' ";
        $and = 'AND';
    }
    if($wingresoretiro != '' && $wingresoretiro != '*')
    {
        if($wingresoretiro == 'in')
        {
            $filtro .= "$and Idefin BETWEEN '$wfechaingreso' AND '$wfecharetiro'";
        }
        else
        {
            $filtro .= "$and Idefre BETWEEN '$wfechaingreso' AND '$wfecharetiro'";
        }
        $and = 'AND';
    }

    /*********************************
    * Datos Unidades de servicio - Centros de costos
    */
    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    $tabla_CCO = '';
    $centro_costo = array();
    if($row = mysql_fetch_array($res))
    {
        $tabla_CCO = $row['Emptcc'];
        switch ($tabla_CCO)
        {
            case "clisur_000003":
                    $qcc="select Ccocod AS codigo, Ccodes AS nombre from clisur_000003 ORDER BY Ccodes";
                    break;
            case "farstore_000003":
                    $qcc="select Ccocod AS codigo, Ccodes AS nombre from farstore_000003 ORDER BY Ccodes";
                    break;
            case "costosyp_000005":
                    $qcc="select Ccocod AS codigo, Cconom AS nombre from costosyp_000005 ORDER BY Cconom";
                    break;
            case "uvglobal_000003":
                    $qcc="select Ccocod AS codigo, Ccodes AS nombre from uvglobal_000003 ORDER BY Ccodes";
                    break;
            default:
                    $query="select Ccocod AS codigo, Cconom AS nombre from costosyp_000005 ORDER BY Cconom";
        }

        $res_cc = mysql_query($qcc,$conex);
        while($row = mysql_fetch_array($res_cc))
        {
            $centro_costo[trim($row['codigo'])] = utf8_encode(ucwords(strtolower($row['nombre'])));
        }
    }

    // consulta tabla de cargos
    $qCargo = " SELECT  Carcod AS codigo, Cardes AS nombre
                FROM    root_000079";
    $resCargo = mysql_query($qCargo,$conex);
    $cargos = array();
    while ($row = mysql_fetch_array($resCargo))
    {
        $cod = $row['codigo'];
        $cargos["$cod"] = $row['nombre'];
    }

    // echo '<div align="left" ><pre>'; print_r($cargos); echo '</pre></div>';

    $q = "  SELECT  Ideuse AS codigo, Ideced AS cedula, Idecco AS ccosto, Idefin AS f_ingreso
                    , Ideno1 AS nombre1, Ideno2 AS nombre2, Ideap1 AS apellido1, Ideap2 AS apellido2
                    , Idefnc AS f_nacimiento, Ideest AS estado, '' AS nombre_cco
                    , Ideccg AS cod_oficina, '' AS cargo, Idefre AS f_retiro
                    , Ideext AS extension, Ideeml AS email, Idegen AS genero
            FROM    ".$wbasedato."_000013
            WHERE   $filtro
            ORDER BY Ideno1, Ideno2, Ideap1";
    $res = mysql_query($q,$conex);

    while ($row = mysql_fetch_array($res))
    {
        $codCco = $row['ccosto'];
        $codCargo = trim($row['cod_oficina']);
        $cco = (array_key_exists(trim($codCco),$centro_costo)) ? $centro_costo[trim($codCco)] : '';
        $ccg = (array_key_exists(trim($codCargo),$cargos)) ? $cargos[trim($codCargo)] : '';
        $fnace = ($row['f_nacimiento'] != '' && $row['f_nacimiento'] != '0000-00-00') ? $row['f_nacimiento']: '0000-00-00';
        $edad_emp = ($row['f_nacimiento'] != '' && $row['f_nacimiento'] != '0000-00-00') ? calculaEdad($row['f_nacimiento']): '0000-00-00';

        $empleado[$row['codigo']] = array(
                                    'wcodigo'   =>$row['codigo'],
                                    'wced'      =>$row['cedula'],
                                    'wccosto'   =>trim($codCco),
                                    'wf_ingreso'=>$row['f_ingreso'],
                                    'wnombre1'  =>$row['nombre1'],
                                    'wnombre2'  =>$row['nombre2'],
                                    'wapellido1'=>$row['apellido1'],
                                    'wapellido2'=>$row['apellido2'],
                                    'westado'   =>$row['estado'],
                                    'wnom_cco'  =>(($cco != '') ? $cco : 'NO REGISTRA'), // consultado en otra tabla de matrix
                                    'wf_nace'   =>$fnace,
                                    'cargo'     =>(($ccg != '') ? $ccg : 'NO REGISTRA'), // consultado en otra tabla de matrix
                                    'edad'      =>$edad_emp, // valor calculado
                                    'f_retiro'  =>$row['f_retiro'],
                                    'wcodcargo' =>$codCargo,
                                    'extension' =>$row['extension'],
                                    'email'     =>$row['email'],
                                    'sex'       =>$row['genero']
                                );
    }

    // echo '<div align="left" ><pre>'; print_r($empleado); echo '</pre></div>';
    // exit();

    return $empleado;
}

/**
 * Esta función se encarga de buscar algúna coincidencia en la tabla en UNIX de empleados de los parámetros que llegan a la función, si encuentra algo entonces lo inserta en la tabla de matrix
 *
 * @param unknown $conex
 * @param unknown $wemp
 * @param unknown $wbasedato
 * @param unknown $user
 * @param unknown $wcodigo
 * @param unknown $wced
 * @param unknown $wnombre1
 * @param unknown $wnombre2
 * @param unknown $wapellido1
 * @param unknown $wapellido2
 * @param unknown $wccostos
 * @param unknown $wccargo
 * @param unknown $wsexo
 * @param unknown $westado
 * @return unknown
 */
function buscarEnUnix($conex,$wemp_pmla,$wbasedato,$user_session,$wcodigo,$wced,$wnombre1,$wnombre2,$wapellido1,$wapellido2,$wccostos,$wccargo,$wsexo,$westado,$wfechaingreso,$wfecharetiro,$wingresoretiro)
{
    $user_session = explode('-',$_SESSION['user']);
    $user_session = $user_session[1];

    if($user_session == '')
    {
        echo 'Recargue la pagina principal de Matrix o inicie sesión nuevamente..';
        exit();
    }

    $empleado = array();

    $filtro = '';
    $and = '';
    if(trim($wcodigo) != '')
    {
        $filtro = " percod = '".$wcodigo."' ";
        $and = 'AND';
    }

    if(trim($wced) != '')
    {
        $filtro .= "$and perced = '".$wced."' ";
        $and = 'AND';
    }

    if(trim($wnombre1) != '')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wnombre1))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));
        // $filtro .= "$and CONCAT(perno1,' ',perno2,' ',perap1,' ',perap2) LIKE '%$buscaNombre%'";
        $filtro .= "$and perno1 LIKE '%$buscaNombre%' ";
        $and = 'AND';
    }
    if(trim($wnombre2) != '')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wnombre2))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));
        $filtro .= "$and perno2 LIKE '%$buscaNombre%' ";
        $and = 'AND';
    }
    if(trim($wapellido1) != '')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wapellido1))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));
        $filtro .= "$and perap1 LIKE '%$buscaNombre%' ";
        $and = 'AND';
    }
    if(trim($wapellido2) != '')
    {
        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wapellido2))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));
        $filtro .= "$and perap2 LIKE '%$buscaNombre%' ";
        $and = 'AND';
    }
    if(trim($wccostos) != '')
    {
        // $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wccostos))));
        // $buscaNombre = strtoupper(strtolower($buscaNombre));
        // $filtro .= "$and percco LIKE '%$buscaNombre%' ";
        $buscaNombre = trim($wccostos);
        $filtro .= "$and percco = '$buscaNombre' ";
        $and = 'AND';
    }
    if(trim($wccargo) != '')
    {
        // $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($wccargo))));
        // $buscaNombre = strtoupper(strtolower($buscaNombre));
        // $filtro .= "$and oficod LIKE '%$buscaNombre%' ";
        $buscaNombre = trim($wccargo);
        $filtro .= "$and perofi = '$buscaNombre' ";
        $and = 'AND';
    }
    if($westado != '' && $westado != '*')
    {
        $filtro .= "$and peretr = '$westado' ";
        $and = 'AND';
    }
    if($wsexo != '' && $wsexo != '*')
    {
        $filtro .= "$and persex = '$wsexo' ";
        $and = 'AND';
    }
    if($wingresoretiro != '' && $wingresoretiro != '*')
    {
        if($wingresoretiro == 'in')
        {
            $filtro .= "$and perfin BETWEEN '$wfechaingreso' AND '$wfecharetiro'";
        }
        else
        {
            $filtro .= "$and perret BETWEEN '$wfechaingreso' AND '$wfecharetiro'";
        }
        $and = 'AND';
    }

    $odbc = nominaEmpresa($wemp_pmla, $conex, $wbasedato);
    $conexunix = odbc_connect($odbc,'informix','sco') or die("No se ralizo Conexion con Unix");
    $q = "   SELECT  percod AS codigo, perced AS cedula
                    , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                    , perfna AS f_nacimiento
                    , pertel AS telefono
                    , perdir AS direccion
                    , persex AS sex
                    , peretr AS estado
                    , cconom AS nombre_cco
                    , percco AS ccosto
                    , oficod AS cod_oficina
                    , ofinom AS cargo
                    , perfin AS f_ingreso
                    , perret AS f_retiro
            FROM    noper, cocco, noofi
            WHERE   $filtro
                    AND ccocod = percco
                    AND perofi = oficod
            ORDER BY perno1, perno2, perap1";

    // echo '<pre>';print_r($q);echo '</pre>';

    $long='                                    ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
    $table='fnd'.date("YmdHis");

    $q .= " into temp $table";
    $err_o = odbc_exec($conexunix,$q)or die( odbc_error()." Query buscame.php - $q - ".odbc_errormsg() );

    $select= "  codigo, cedula
                , '$long' AS nombre1, '$long' AS nombre2, '$long' AS apellido1, '$long' AS apellido2
                , f_nacimiento, telefono, direccion, sex, estado, nombre_cco, ccosto
                , cod_oficina, cargo, f_ingreso, '$long' AS f_retiro ";
    $from= "$table" ;
    $where=NULL;

    $llaves[0]=1;

    $res = ejecutar_consulta ($select, $from, $where, $llaves, $conexunix);
    //$res = odbc_exec($conexunix,$q) or die(odbc_error($conexunix).' => '.$q);
    //$num = odbc_num_rows($res);


    // $query1= "  select  ofiact, ofinom
                // from    noofi";
    // $resq = odbc_do($conexunix,$query1) or die( odbc_error()." - $query1 - ".odbc_errormsg() ); // Consulto la tabla temporal

    // echo '<div align="left">';
    // while (odbc_fetch_row($resq)) //RECORRO CADA REGISTRO DE LA TEMPORAL
    // {
        // echo trim(odbc_result($resq,'ofiact')).' '.trim(odbc_result($resq,'ofinom')).'<br />';
    // }echo '</div>';


    while (odbc_fetch_row($res))
    {
        $fnace = trim(odbc_result($res,'f_nacimiento'));
        $fnace = ($fnace != '' && $fnace != '0000-00-00') ? $fnace: '0000-00-00';

        $edad_emp = trim(odbc_result($res,'f_nacimiento'));
        $edad_emp = ($edad_emp != '' && $edad_emp != '0000-00-00') ? calculaEdad(trim(odbc_result($res,'f_nacimiento'))): '0000-00-00';
        $codigo_encontrado = odbc_result($res,'codigo').'-'.$wemp_pmla;
        $empleado[$codigo_encontrado] = array(
                                'wcodigo'   =>$codigo_encontrado,                // por el momento, todo empleado buscado en Nomina Clinica entonces pertenece a la empresa clinica '01'
                                'wced'      =>str_replace('.','',trim(odbc_result($res,'cedula'))),
                                'wnombre1'  =>trim(odbc_result($res,'nombre1')),
                                'wnombre2'  =>trim(odbc_result($res,'nombre2')),
                                'wapellido1'=>trim(odbc_result($res,'apellido1')),
                                'wapellido2'=>trim(odbc_result($res,'apellido2')),
                                'wf_nace'   =>$fnace,
                                'telefono'  =>trim(odbc_result($res,'telefono')),
                                'direccion' =>trim(odbc_result($res,'direccion')),
                                'sex'       =>trim(odbc_result($res,'sex')),
                                'westado'   =>trim(odbc_result($res,'estado')),
                                'wnom_cco'  =>trim(odbc_result($res,'nombre_cco')),
                                'wccosto'   =>trim(odbc_result($res,'ccosto')),
                                'wcodcargo' =>trim(odbc_result($res,'cod_oficina')),
                                'cargo'     =>trim(odbc_result($res,'cargo')),
                                'wf_ingreso'=>trim(odbc_result($res,'f_ingreso')),
                                'f_retiro'  =>trim(odbc_result($res,'f_retiro')),
                                'edad'      =>$edad_emp
                            );
    }

    if (count($empleado) >= 1)
    {
        foreach($empleado as $cod_use => $value)
        {
            $unix = $empleado[$cod_use];
            /* Busca si tiene email y extensión asociada en la tabla de requerimientos */
            $qUsu= "SELECT  Usuext, Usuema
                    FROM    root_000039
                    WHERE   Usucod = '$cod_use'";
            $rUsu = mysql_query($qUsu,$conex);
            $usu = mysql_fetch_array($rUsu);

            $empleado[$cod_use]['extension'] = $usu['Usuext'];
            $empleado[$cod_use]['email'] = $usu['Usuema'];

            $f_retiro = $unix['f_retiro'];

            $istEstado = (trim($unix['westado']) == 'A') ? 'on': 'off';

            $insert = " INSERT INTO ".$wbasedato."_000013
                            (   Medico, Fecha_data, Hora_data, Idefnc, Idegen,
                                Ideno1, Ideno2, Ideap1, Ideap2
                                , Idecco ,Ideccg,
                                Ideced, Idedir, Idetel, Ideeml, Ideext, Idefin, Idefre, Ideuse, Ideest, Seguridad)
                        VALUES
                            (   '".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$unix['wf_nace']."','".$unix['sex']."',
                                '".$unix['wnombre1']."','".$unix['wnombre2']."','".$unix['wapellido1']."','".$unix['wapellido2']."',
                                '".$unix['wccosto']."','".$unix['wcodcargo']."',
                                '".$unix['wced']."','".$unix['direccion']."','".$unix['telefono']."','".$usu['Usuema']."','".$usu['Usuext']."','".$unix['wf_ingreso']."','".$f_retiro."','".$unix['wcodigo']."','".$istEstado."','C-".$user_session."')";
            $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En ".$wbasedato."_000013 por primera vez): " . $insert . " - " . mysql_error());
        }
    }

    // echo '<div align="left" ><pre>'; print_r($empleado); echo '</pre></div>';
	odbc_close($conexunix);
	odbc_close_all();
	
    return $empleado;
	
}

/**
 * Este es un buscador genérico al cual se le debe pasar los campos del select y las tablas y los valores a buscar, todos estos mediante un array
 * y posteriormente generar una respuesta con options que van a ser parte de algún elemento tipo select.

 $params debe se de la forma:
    $params['tabla']='talhuma_000013';
    $params['campo_estado']="Ideest = 'on'";
    $params['campos'][]='Ideuse';
    $params['campos'][]='ideno1';
    $params['campos'][]='ideno2';
    $params['campos'][]='ideap1';
    $params['campos'][]='ideap2';
    ...

 * @param unknown $wemp_pmla
 * @param unknown $conex
 * @param unknown $wbasedato
 * @param unknown $params
 * @param unknown $buscado      : es el código o parte del nombre que se está buscando.
 * @param unknown $add_todos
 * @return unknown
 */
function getOptions($wemp_pmla, $conex, $wbasedato,$params , $buscado, $add_todos = 'off')
{
    $options = '';

    $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($buscado))));
    $buscaNombre = strtoupper(strtolower($buscaNombre));

    $cod = $params['campos'][0];
    unset($params['campos'][0]);
    $concatenables = implode(',',$params['campos']);
    $concatenables = str_replace(',',",' ',",$concatenables);

    $q = "  SELECT  ".$cod.", CONCAT(".$concatenables.") as concatenados
            FROM    ".$params['tabla']."
            WHERE   ".$params['campo_estado']."
                    AND ( CONCAT(".$concatenables.") LIKE '%".$buscaNombre."%')
                    OR ".$cod." LIKE '%".$buscaNombre."%'";

    $res = mysql_query($q,$conex);

    $options = '<option value="" >Seleccione..</option>';
    if($add_todos == 'on')
    {
        $options .= '<option value="*" >[ - TODOS - ]</option>';
    }

    while($row = mysql_fetch_array($res))
    {
        $chk = ($buscado == $row[$cod]) ? "selected='selected'": '';
        $options .= '<option value="'.$row[$cod].'" '.$chk.' >'.$row[$cod].' - '.trim(utf8_encode(strtoupper(strtolower($row['concatenados'])))).'</option>';
    }
    return $options;
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
 * nominaEmpresa se encarga de retornar el nombre de la tabla donde se debe buscar la lista de empleados que pertenecen a la empresa que llega por parámetros
 *
 * @param unknown $wemp_pmla
 * @param unknown $conex
 * @param unknown $wbasedato
 * @return unknown
 */
function nominaEmpresa($wemp_pmla, $conex, $wbasedato)
{
    $tabla = 'nomina';
    $q = "  SELECT  Conexion
            FROM    root_000023
            WHERE   Empresa = '".$wemp_pmla."'
                    AND Tipo_aplicacion = 'nomina'";
    $res = mysql_query($q,$conex);
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $tabla = $row['Conexion'];
    }
    return $tabla;
}

/**
 * Adiciona contenido a archivo temporal en memoria - Debug
 *
 * @param unknown $log      : Contenido string.
 * @param unknown $reset    : Determina si borra o mantiene el el contenido anterior almacenado en el buffer $DEBUG_LOG_FUNCIONES.
 * @return unknown
 */
 global $DEBUG_LOG_FUNCIONES;
// function debug_log_inline($return_log = 'on', $log = '', $reset = false)
// {
    // global $DEBUG_LOG_FUNCIONES, $DEBUG;
    // if($DEBUG!='') { $log=(!$reset && $log=='')? ' FIN SUB_LOG.<br>': $log;$DEBUG_LOG_FUNCIONES = ((!$reset) ? $DEBUG_LOG_FUNCIONES.'<br>* ('.date("Y-m-d H:i:s").') '.$log: '<br>'.$log); }
    // if($return_log == 'on') { return $DEBUG_LOG_FUNCIONES; }
// }
function debug_log_inline($return_log = 'on', $log = '', $reset = false)
{
    global $DEBUG_LOG_FUNCIONES, $DEBUG;
    if($DEBUG!='' && $DEBUG=='true') { $log=(!$reset && $log=='')? ' <span class="endlog">FIN</span> SUB_LOG.<br>': $log;$DEBUG_LOG_FUNCIONES = ((!$reset) ? $DEBUG_LOG_FUNCIONES.'<br>* ('.date("Y-m-d H:i:s").') '.$log: '<br>'.$log); }
    if($return_log == 'on') { return $DEBUG_LOG_FUNCIONES; }
}

/*****************************************************************************************************************************************/
/*
$descripcion = "tabla:'".$wbasedato."_000016'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza estudio actual'";
insertLog($conex, $wbasedato, $user_session, $accion, $form, '', $descripcion, $wuse);
*/

/**
 * Adiciona un registro a la tabla de LOG de la aplicación.
 *
 * @param unknown $conex        : Conexión a base de datos.
 * @param unknown $wbasedato    : Prefijo de la base de datos a consultar.
 * @param unknown $user_session : Código de usuario de la aplicación que genera la acción en el LOG.
 * @param unknown $accion       : Acción o clasificación principal de un proceso del programa (update,load,insert,delete,..).
 * @param unknown $form         : Clasificación más específica de un proceso del programa (actualiza_tabla, inserta_registro_x, carga_datos_y, ...).
 * @param unknown $err          : Tipo de error ocurrido, (error_sql, error_logico).
 * @param unknown $descripcion  : Texto con una explicación más detallada del evento.
 * @param unknown $user_update  : Código del usuario, del paciente o del elemento a quien se le insertan o modigican registros asociados a el.
 * @param unknown $sql_error    : Código o script en lenguaje SQL en el cual se detectó un error, para hacerle seguimiento.
 * @return unknown
 */
function insertLog($conex, $wbasedato, $user_session, $accion, $form, $err, $descripcion, $user_update, $sql_error = '')
{
    $descripcion = str_replace("'",'"',$descripcion);
    // $sql_error = str_replace(PHP_EOL,'',$sql_error); // elimina los cambios de línea.
    $sql_error = ereg_replace('([ ]+)',' ',$sql_error);

    $insert = " INSERT INTO ".$wbasedato."_".LOG."
                    (Medico, Fecha_data, Hora_data, Logcdu, Logacc, Logfrm, Logerr, Logsqe, Logdes, Loguse, Logest, Seguridad)
                VALUES
                    ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode($user_update)."','".utf8_decode($accion)."','".$form."','".$err."',\"".$sql_error."\",'".$descripcion."','".$user_session."','on','C-".$user_session."')";
    $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());
}
/*****************************************************************************************************************************************/
?>