<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
// session_start();
/**
 PROGRAMA                   : reporteador_caracterizacion.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 11 Septiembre de 2012

 DESCRIPCION:
 El reporteador de caracaterizacón permite concultar o cruzar los campos que se deseen con el con el fin de ejecutar la consulta
 y listar todos los registros que coincidan.

 ACTUALIZACIONES:
 *
 *  Julio 10 de 2013
    Edwar Jaramillo     : Se organiza la codificación de caracteres en el resultado del reporte (esto hacía que en producción fallara al momento de mostrar el resultado).

 *  Octubre 10 de 2012
    Edwar Jaramillo     : documentación de código.
 *  Septiembre 11 de 2012
    Edwar Jaramillo     : Fecha de la creación del reporte.

**/

/**
    Este condicional es ejecutado para exportar el resultado del reporte a excel, se hace por medio de jquery
*/
if(isset($accion) && isset($form))
{
    if(isset($accion) && $accion == 'exportar_excel') // se debe diferenciar por los dos o por otro diferente a $accion puesto que desde talento.php ya esta seteado $accion
    {
        header("Content-type: application/vnd.ms-excel; name='excel'");
        header("Content-Disposition: filename=reporte_caract_".date("Ymd").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $_POST['datos_a_enviar'];
        return;
    }
}





include_once("../procesos/funciones_talhuma.php");
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

if(!isset($_SESSION['user']))
{
    if(isset($user_session))
    {
        $_SESSION['user'] = $user_session;
    }
}

$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];
$user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

/**
    *   Estructura por campo ej:
        "000014-Eduuse|AND|Edugrd|=|05" Se pueden identificar siempre 5 posiciones que son
        TABLA | OPERADOR_LOGICO | NOMBRE_CAMPO_EN_TABLA | OPERADOR_DE_IGUALDAD | VALOR_A_BUSCAR_EN_CAMPO

        TABLA: se conforma por el sufijo de la tabla seguido del campo por el cual se puede hacer el cruce o join a esa tabla.


    *   En los llamados ajax generalmente se envías las siguientes variables que son la fuente a partir del cual se manipula y se consulta la información
        (Son campos que están representados por elementos tipo hidden en los que se va guardando concatenado los datos)

        campos_filas    :   En este campo se concatenan los datos que en el formulario se ven como tipo filas, se pueden identificar porque son tablas en las
                            que se puede ingresar filas completas a la tabla, un ejemplo de lo que iría guardado en este campo es lo siguiente
                            "000014-Eduuse|AND|Edugrd|=|05||000014-Eduuse|AND|Edutit|=|Ingeniero[TR#]000015-Idiuse|AND|Idides|=|Ingles"
                            Se puede ver que las filas están identificadas por el separador [TR#], esto indica que lo que sigue son datos de otra fila, y cada fila puede estar separada por
                            "||" lo que indica que lo que sigue es un campo o filtro y el separador "|" secciona los datos detallados para cada filtro

        campos_listas   :   En este campo se concatenan los campos que el formulario se ven como un tipo de listas, se pueden identificar porque
                            están marcadas por un recuadro punteado y el simbolo ">>", cada lista significa que son multiples valores para un mismo campo
                            un ejemplo para ver la estructura de esta información concateneda es la siguiente
                            "000013-Ideuse|AND|Ideuse|=|03636||000013-Ideuse|OR|Ideuse|=|00271||000013-Ideuse|AND|Ideinc|=|Manizales||...||...||...||..."

        campos_unicos   :   En este campo se concatenan los campos que sólo tienen un dato a la vez, es decir no se permite crear una lista, es un campo con un valor
                            único a buscar, la estructura concatenada de los campos es igual a la estructura de las listas relacionada anteriormente
                            "000013-Ideuse|AND|Ideap1|=|jaramillo||...||...||..."

        columnas_filtros:   Esta información corresponde a las columnas que se han chequeado o seleccionado en el panel de opciones del reporte de caracterización,
                            Son las columnas que se desean ver en la respuesta del reporte, la estructura de los campo concatenados en este campo es
                            "000013|Ideno1||000013|Ideno2||000013|Ideap1||000013|Ideap2||...||..." donde simplemente va "SUFIJO_TABLA | NOMBRE_CAMPO" concatenado tantas
                            veces como campos se han seleccionado para ver en el resultado.
*/

if(isset($accion) && isset($form)) // se debe diferenciar por los dos o por otro diferente a $accion puesto que desde talento.php ya esta seteado $accion
{
    if(isset($accion) && $accion == 'load')
    {
        if(isset($form) && $form == 'load_pais_visa') // Lista paises visa
        {
            $q = "  SELECT  Idepvi AS cod_paises
                    FROM    ".$wbasedato."_000013
                    WHERE   Ideuse = '".$wuse."'";
            $res = mysql_query($q,$conex) or die("Error: " . mysql_errno() . " - ".$wbasedato."_000013 $wuse: ".$q." - ".mysql_error());
            $paises_use = '';
            if($row = mysql_fetch_array($res))
            {
                $paises_use = $row['cod_paises'];
            }

            $result = 'Ninguno';
            if($paises_use != '')
            {
                $result = '';
                $explode = explode(',',$paises_use);
                $implode_cods = implode("','",$explode);
                $qp = " SELECT  Paicod AS cod_pais, Painom AS nombre
                        FROM    root_000077
                        WHERE   Paiest = 'on'
                                AND Paicod IN ('".$implode_cods."')
                        ORDER BY Painom";
                $resp = mysql_query($qp,$conex);
                $paises_v = array();

                while($rowp = mysql_fetch_array($resp))
                {
                    $cod_pais = $rowp['cod_pais'];
                    $nombre = ucwords(strtolower(utf8_encode($rowp['nombre'])));
                    $result .= '<input type="checkbox" id="'.$cod_pais.'" name="'.$cod_pais.'" rel="000013-Ideuse" in="Idepvi-*" value="on" checked="checked" />&nbsp;'.$nombre.'<br />';
                }
            }
            echo $result;
        }
        elseif(isset($form) && $form == 'load_barrio') // Lista Barrios por ciudad
        {
            $query= "   SELECT  Barcod, Bardes
                        FROM    root_000034
                        WHERE   Barmun = '".$id_padre."'
                        ORDER BY Bardes";
            $result = mysql_query($query,$conex);
            $options = '<option value="" >Seleccione..</option>';
            while($row = mysql_fetch_array($result))
            {
                $options .= '<option value="'.$row['Barcod'].'" >'.utf8_encode(ucwords(strtolower($row['Bardes']))).'</option>';
            }
            echo $options;
        }
    }
    elseif(isset($accion) && $accion == 'listar_columnas')
    {
        /** SELECCIONAR COLUMNAS PARA VER EN LA RESPUESTA DEL REPORTE

            En esta sección se consultan todos los campos estan relacionados a los filtros que han sido usados en el reportes
            esto con el fin de poder seleccionar más columnas a mostrar en el reportes.
        */
        $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');

        $tablas_cruce = array(); // Guarda todas las tablas que se deben usar en el JOIN
        $prefijo = $wbasedato.'_';
        $tablas_cruce[$prefijo.'000013'] = '000013'; // Por defecto siempre se incluye la tabla 000013
        $enc_campos = array();

        /**
            Se recorre la variable de campos concatenados haciendo un explode para extraer las tablas que corresponden a los campos a filtrar.
        */
        if($campos_unicos != '')
        {
            $explode_entrada = explode('||',$campos_unicos);
            $tablas_bloque = array();
            foreach($explode_entrada as $key_tb => $value_rb)
            {
                $explode_bloque = explode('|',$value_rb);
                $explode_tbl = explode('-',$explode_bloque[0]);
                $tabla = $explode_tbl[0];
                $campo_cruce = $explode_tbl[1];
                if(!array_key_exists($prefijo.$tabla,$tablas_cruce))
                {
                    $tablas_cruce[$prefijo.$tabla] = $tabla;
                }
            }
        }

        /**
            Se recorre la variable de campos concatenados haciendo un explode para extraer las tablas que corresponden a los campos a filtrar.
        */
        if($campos_listas != '')
        {
            $explode_entrada = explode('||',$campos_listas);
            $tablas_bloque = array();
            foreach($explode_entrada as $key_tb => $value_rb)
            {
                $explode_bloque = explode('|',$value_rb);
                $explode_tbl = explode('-',$explode_bloque[0]);
                $tabla = $explode_tbl[0];
                $campo_cruce = $explode_tbl[1];

                if(!array_key_exists($prefijo.$tabla,$tablas_cruce))
                {
                    $tablas_cruce[$prefijo.$tabla] = $tabla;
                }
            }
        }

        /**
            Se recorre la variable de campos concatenados haciendo un explode para extraer las tablas que corresponden a los campos a filtrar.
        */
        if($campos_filas != '')
        {
            $explode_entrada_fila = explode('[TR#]',$campos_filas);

            $tablas_bloque_fila = array();

            foreach($explode_entrada_fila as $key_fila => $value_fila)
            {
                if($value_fila != '')
                {
                    $explode_entrada = explode('||',$value_fila);
                    $tablas_bloque = array();
                    foreach($explode_entrada as $key_tb => $value_rb)
                    {
                        $explode_bloque = explode('|',$value_rb);
                        $explode_tbl = explode('-',$explode_bloque[0]);

                        $tabla = $explode_tbl[0];
                        $campo_cruce = $explode_tbl[1];

                        if(!array_key_exists($prefijo.$tabla,$tablas_cruce))
                        {
                            $tablas_cruce[$prefijo.$tabla] = $tabla;
                        }
                    }
                }
            }
        }

        /**
            Se recorre la variable de campos concatenados que corresponden a las columnas que se han seleccionado en las opciones del reporte de caracterización
            y que se quieren ver en el resultado del reporte.
        */
        if($columnas_filtros != '')
        {
            // echo '<pre>';print_r($columnas_filtros);echo '</pre>';
            $explode_entrada = explode('||',$columnas_filtros);
            foreach($explode_entrada as $key_tb => $value_rb)
            {
                $explode_bloque = explode('|',$value_rb);
                $tabla = $explode_bloque[0];
                $campo = $explode_bloque[1];

                // validación para saber si la tabla relacionada al campo existe dentro de las tablas de cruce, sino entonces no se tendrá en cuenta
                // ese campo para el select del query, Es decir, no se podrá pretender mostrar un campo que cuya tabla a la que pertenece no esta en el JOIN porque generará error del query.
                if(array_key_exists($prefijo.$tabla,$tablas_cruce))
                {
                    if(!array_key_exists($tabla,$enc_campos))
                    {
                        $enc_campos[$tabla] = array();
                    }
                    $enc_campos[$tabla][$campo] = $campo;
                }
            }
        }

        // echo "<pre>";print_r($tablas_cruce);echo "</pre>";
        // echo "<pre>";print_r($enc_campos);echo "</pre>";

        /**
            Se hace un impode a las tablas (sufijos) que harán parte del JOIN con el fin de traer todos los campos que pertenecen a cada tabla.
            Las columnas que se consultan aquí, más adelante podrán ser parte de las columnas del SELECT que dá origen al resultado del reporte.
         */
        $imp = implode(',',$tablas_cruce);
        $imp = str_replace(',',"','",$imp);

        $query = "
            SELECT  f.nombre AS nombre_tabla, f.medico AS prefijo, f.codigo AS tabla, det.Descripcion AS campo, rt.Dic_Descripcion AS desc_campo
            FROM    formulario AS f, det_formulario AS det, root_000030 AS rt
            WHERE   det.medico  = '".$wbasedato."'
                    AND det.codigo IN ('".$imp."')
                    AND rt.Dic_Usuario = det.medico
                    AND rt.Dic_Formulario = det.codigo
                    AND rt.Dic_Campo = det.campo
                    AND f.medico = det.medico
                    AND f.codigo = det.codigo";
        // $data['sql'] = $query;
// echo "<pre>";print_r($query);echo "</pre>";
        $result = mysql_query($query,$conex);
        $campos = array();
        $columnas_ppal = array();
        while($row = mysql_fetch_array($result))
        {
            $llave = $row['prefijo'].'_'.$row['tabla'];
            if(!array_key_exists($llave,$campos))
            {
                $campos[$llave] = array();
            }
            if($row['tabla'] == '000013' && ($row['campo'] == 'Ideno1' || $row['campo'] == 'Ideno2' || $row['campo'] == 'Ideap1' || $row['campo'] == 'Ideap2'))
            {
                /**
                    Todo los campos que estén en este array se mostrarán en las primeras columnas del resultado del reporte.
                */
                $columnas_ppal[$llave][] = array('campo'=>$row['campo'],'desc_campo'=>$row['desc_campo'],'nombre_tabla'=>$row['nombre_tabla']);
            }
            else
            {
                $campos[$llave][] = array('campo'=>$row['campo'],'desc_campo'=>$row['desc_campo'],'nombre_tabla'=>$row['nombre_tabla']);
            }
        }

        /**
            Los siguientes ciclos unen los array $columnas_ppal y $campos con el fin de dejar al principio los campos de columna principal.
            Se hace para garantizar que los campos o columnas que estén en $columnas_ppal siempre queden al principio, al momento de ver el resultado del
            reporte siempre se vean como las primeras columnas de la respuesta.
        */
        $columnas = array();
        foreach($columnas_ppal as $tabla => $arr_campos)
        {
            if(!array_key_exists($tabla,$columnas))
            {
                $columnas[$tabla] = array();
            }
            foreach($arr_campos as $arr_campo => $datos)
            {
                $columnas[$tabla][] = $datos;
            }
        }

        foreach($campos as $tabla => $arr_campos)
        {
            if(!array_key_exists($tabla,$columnas))
            {
                $columnas[$tabla] = array();
            }
            foreach($arr_campos as $arr_campo => $datos)
            {
                $columnas[$tabla][] = $datos;
            }
        }

        // $columnas = $campos;//array_chunk($campos, 2, true);

        // echo "<pre>";print_r(array_chunk($campos, 2, true));echo "</pre>";
        // echo "<pre>";print_r($columnas_ppal);echo "</pre>";
        // echo "<pre>";print_r($campos);echo "</pre>";
        // echo "<pre>";print_r($columnas);echo "</pre>";

        $tabla_campos = '';
        // foreach($columnas as $key_col => $value_col)
        // {
            $tds = '';
            $cont = 0;
            $nombre_tabla = '';
            foreach($columnas  as $tabla => $campos)
            {
                foreach($campos as $k => $value_c)
                {
                    $cls = 'fila2';
                    if(($cont%2)==0) { $cls = 'fila1'; }

                    $id = str_replace('=','99',base64_encode($tabla.$value_c['campo']));
                    $tbl = explode('_',$tabla);
                    $nombre_tabla = $value_c['nombre_tabla'];

                    $cmp = $value_c['campo'];
                    $ck = '';

                    /**
                        Cuando es la primer entrada al reportes desde el menú, se verifica si la variable $primera_vez == 'ready'
                        si es así se chequean por defecto las columnas de nombres de tal manera que por defecto en la respuesta del reportes siempre se mostraría
                        inicialmente las columnas de nombres.
                     */
                    if(isset($primera_vez) && $primera_vez == 'ready')
                    {
                        $ck = ( $cmp == 'Ideno1' || $cmp == 'Ideno2' || $cmp == 'Ideap1' || $cmp == 'Ideap2') ? 'checked="checked"': '';
                    }

                    if(array_key_exists($tbl[1],$enc_campos) && array_key_exists($cmp,$enc_campos[$tbl[1]]))
                    {
                        $ck = 'checked="checked"';
                    }

                    $tds .= '
                            <tr class="'.$cls.'">
                                <td class="campos_ver">
                                    <input type="checkbox" id="'.$id.'" name="'.$id.'" value="'.$value_c['campo'].'" rel="'.$tbl[1].'" '.$ck.' > ['.utf8_encode($nombre_tabla).'] '.utf8_encode($value_c['desc_campo']).'
                                </td>
                            </tr>';
                    $cont++;
                }
                $idgen = str_replace('=','99',base64_encode($nombre_tabla));// al codificar a base64 se reemplaza el "=" por "99", en caso de existir pues el "=" genera un error en el html al estar asignado en un id.
                $tabla_campos .= '
                        <table style="width:100%;" cellspacing="0" cellpadding="0" border="0" >
                            <tr>
                                <td class="encabezadoTabla" style="border-top:2px solid;color:white;cursor:pointer;" onClick="javascript:verSeccionCaracterizacion(\''.$idgen.'\');" >
                                    <img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;'.utf8_encode($nombre_tabla).'
                                </td>
                            </tr>
                        </table>
                        <table style="width:100%;" cellspacing="0" cellpadding="0" border="0" >
                        <tr><td align="center">
                            <div id="'.$idgen.'" class="borderDiv displCaracterizacion" >
                            <table style="width:100%;">

                                '.$tds.'
                            </table>
                            </div>
                        </td></tr>
                        </table>';
                                // <!-- <tr class="encabezadoTabla"><td>'.utf8_encode($nombre_tabla).'</td></tr> -->
                $tds = '';
            }
        // }

        $data['html'] = $tabla_campos;
        echo json_encode($data);
        return;
    }
    elseif(isset($accion) && $accion == 'ejecutar_query')
    {
        /** ARMAR QUERY, EJECUTAR EL QUERY, GENERAR EL REPORTE, MOSTRAR EL RESULTADO DEL REPORTE

            En esta sección se procesan todas las variables que traen la información concatenada con el fin de extrar los datos que van a dar origen al query del reporte.
         */
        $data = array(  'error'=>0,     // Si existe por ejemplo un error al ejecutar el query esta variable se seteará a 1
                        'mensaje'=>'',  // Cualquier mensaje que se pueda generar en este proceso y se quiera informar, p.e. un error al ejecutar el query ó el query no arrojó resultados.
                        'html'=>'',     // Código html, aquí se envía la tabla html del reporte generado.
                        'sql'=>'',      // En esta variable se guarda el query que fué generado para el reporte, para posteriormente mostrar dicho sql a ciertas personas que tengan el permiso.
                        'filtros'=>''   // Se muestran los filtros que fueron usados en formulario, filtros en los que se escribió datos.
                    );

        /**
            Si no se ha usado ningún filtro se informa y se termina el proceso.
        */
        if($campos_unicos == '' && $campos_listas == '' && $campos_filas == '')
        {
            $data['error'] = 1;
            $data['html'] = '&nbsp;';
            $data['sql'] = '';
            $data['mensaje'] = 'No ha seleccionado ninguno de los filtros del reporteador';
            echo json_encode($data);
            return;
        }

        $tablas_unicos = array();
        $tablas_listas = array();
        $tablas_filas = array();

        $prefijo = $wbasedato.'_';

        /**
            Por defecto se adiciona el campo Ideest a la variable de campos_unicos con el fin de solo filtrar los registros en "ON" de la tabla 000013
        */
        $tablas_cruce[$wbasedato.'_000013'] = 'Ideuse'; // tabla principal, respecto a la cual se van a cruzar todas las demás tablas, y tendrán asociado el campo con el que se deben cruzar
        if($campos_unicos == '')
        {
            $campos_unicos = '000013-Ideuse|AND|Ideest|=|on';
        }
        else
        {
            $campos_unicos .= '||000013-Ideuse|AND|Ideest|=|on';
        }

        /**
            Se recorre la variable de campos únicos con el fin de leer la información concatenada y distribuirla en un array para su manipulación.
        */
        if($campos_unicos != '')
        {
            $explode_entrada = explode('||',$campos_unicos);
            $tablas_bloque = array();
            foreach($explode_entrada as $key_tb => $value_rb)
            {
                $explode_bloque = explode('|',$value_rb);
                /*
                    $explode_bloque[0] => tabla
                    $explode_bloque[1] => operador lógico
                    $explode_bloque[2] => nombre campo en la tabla
                    $explode_bloque[3] => operador de igualdad
                    $explode_bloque[4] => valor buscado
                */
                $explode_tbl = explode('-',$explode_bloque[0]);

                $tabla = $explode_tbl[0];
                $campo_cruce = $explode_tbl[1];
                $campo = $explode_bloque[2];
                $opLog = $explode_bloque[1];
                $opMat = $explode_bloque[3];
                $valor = $explode_bloque[4];

                /**
                    Este condicional se encarga de verificar y recopilar todas las tablas involucradas en el JOIN y los campos que deben ser usados para hacer el cruce en el JOIN
                */
                if(!array_key_exists($prefijo.$tabla,$tablas_cruce))
                {
                    $tablas_cruce[$prefijo.$tabla] = $campo_cruce;
                }

                if(!array_key_exists($tabla,$tablas_bloque))
                {
                    $tablas_bloque[$tabla] = array();
                }

                if(!array_key_exists($campo,$tablas_bloque[$tabla]))
                {
                    $tablas_bloque[$tabla][$campo] = array();
                }

                $tablas_bloque[$tabla][$campo][] = array('operadorLog'=>$opLog,'operadorMat'=>$opMat,'valor'=>$valor);
            }

            $tablas_unicos = $tablas_bloque;
            // echo '<pre>';print_r($tablas_unicos);echo '</pre>';
        }

        /**
            Se recorre la variable de campos listas con el fin de leer la información concatenada y distribuirla en un array para su manipulación.
        */
        if($campos_listas != '')
        {
            $explode_entrada = explode('||',$campos_listas);
            $tablas_bloque = array();
            foreach($explode_entrada as $key_tb => $value_rb)
            {
                $explode_bloque = explode('|',$value_rb);
                /*
                    $explode_bloque[0] => tabla
                    $explode_bloque[1] => operador lógico
                    $explode_bloque[2] => nombre campo de en tabla
                    $explode_bloque[3] => operador de igualdad
                    $explode_bloque[4] => valor buscado
                */
                $explode_tbl = explode('-',$explode_bloque[0]);

                $tabla = $explode_tbl[0];
                $campo_cruce = $explode_tbl[1];
                $campo = $explode_bloque[2];
                $opLog = $explode_bloque[1];
                $opMat = $explode_bloque[3];
                $valor = $explode_bloque[4];

                // En caso de llegar un campo de la forma "Cvissp-*" lo que significa que el campo con '*' es multivalor osea que se debe buscar
                // dentro de una cadena por ejemplo |01,02,05,01| para este caso se debería cambiar el operador matemático por un like.
                $exp_campo = explode('-',$campo);
                if(count($exp_campo) > 1 && $exp_campo[1] == '*')
                {
                    $campo = $exp_campo[0];
                    if($opMat == '!=') { $opMat = 'NOT LIKE'; }
                    else{ $opMat = 'LIKE'; }

                    $valor = "%".$valor."%";
                }

                /**
                    Este condicional se encarga de verificar y recopilar todas las tablas involucradas en el JOIN y los campos que deben ser usados para hacer el cruce en el JOIN
                */
                if(!array_key_exists($prefijo.$tabla,$tablas_cruce))
                {
                    $tablas_cruce[$prefijo.$tabla] = $campo_cruce;
                }

                if(!array_key_exists($tabla,$tablas_bloque))
                {
                    $tablas_bloque[$tabla] = array();
                }

                if(!array_key_exists($campo,$tablas_bloque[$tabla]))
                {
                    $tablas_bloque[$tabla][$campo] = array();
                }

                $tablas_bloque[$tabla][$campo][] = array('operadorLog'=>$opLog,'operadorMat'=>$opMat,'valor'=>$valor);
            }
            $tablas_listas = $tablas_bloque;
            // echo '<pre>';print_r($tablas_listas);echo '</pre>';
        }

        /**
            Se recorre la variable de campos listas con el fin de leer la información concatenada y distribuirla en un array para su manipulación.
        */
        if($campos_filas != '')
        {
            $explode_entrada_fila = explode('[TR#]',$campos_filas);

            $tablas_bloque_fila = array();

            foreach($explode_entrada_fila as $key_fila => $value_fila)
            {
                if($value_fila != '')
                {
                    $explode_entrada = explode('||',$value_fila);
                    $tablas_bloque = array();
                    foreach($explode_entrada as $key_tb => $value_rb)
                    {
                        $explode_bloque = explode('|',$value_rb);
                        /*
                            $explode_bloque[0] => tabla
                            $explode_bloque[1] => operador lógico
                            $explode_bloque[2] => nombre campo de en tabla
                            $explode_bloque[3] => operador de igualdad
                            $explode_bloque[4] => valor buscado
                        */
                        $explode_tbl = explode('-',$explode_bloque[0]);

                        $tabla = $explode_tbl[0];
                        $campo_cruce = $explode_tbl[1];
                        $campo = $explode_bloque[2];
                        $opLog = $explode_bloque[1];
                        $opMat = $explode_bloque[3];
                        $valor = $explode_bloque[4];

                        /**
                            Este condicional se encarga de verificar y recopilar todas las tablas involucradas en el JOIN y los campos que deben ser usados para hacer el cruce en el JOIN
                        */
                        if(!array_key_exists($prefijo.$tabla,$tablas_cruce))
                        {
                            $tablas_cruce[$prefijo.$tabla] = $campo_cruce;
                        }

                        if(!array_key_exists($tabla,$tablas_bloque))
                        {
                            $tablas_bloque[$tabla] = array();
                        }

                        if(!array_key_exists($campo,$tablas_bloque[$tabla]))
                        {
                            $tablas_bloque[$tabla][$campo] = array();
                        }

                        $tablas_bloque[$tabla][$campo][] = array('operadorLog'=>$opLog,'operadorMat'=>$opMat,'valor'=>$valor);
                    }
                    $tablas_bloque_fila[] = $tablas_bloque;
                }
            }
            $tablas_filas = $tablas_bloque_fila;
            // echo '<pre>';print_r($tablas_filas);echo '</pre>';
        }

        /*********************************************************************************************************************
                                    Recorrer arrays para armar las condiciones o filtros de consulta
        *********************************************************************************************************************/
        $filtros_usados = array();
        // Filtros unicos
        $filtros_unicos = "";
        $iteracion = 0;

        /**
            Arma los condicionales del query para los campos únicos
         */
        foreach($tablas_unicos as $tabla => $campos)
        {
            if(!array_key_exists($tabla,$filtros_usados))
            {
                $filtros_usados[$tabla] = array();
            }
            $alias_tabla = 't_'.$prefijo.$tabla.'.'; // Alias con el que siempre se va a referenciar el campo
            foreach($campos as $campo => $condiciones)
            {
                $filtros_usados[$tabla][$campo] = $campo;
                foreach($condiciones as $key_dato => $condicion)
                {
                    $valor = $condicion['valor'];               // Valor a buscar en el campo
                    $operadorLog = $condicion['operadorLog'];   // Operador lógico que acompañará el campo
                    $operadorMat = $condicion['operadorMat'];   // Operador de igualdad que estará entre el campo y el valor a buscar en ese campo
                    if($iteracion == 0)
                    {
                        /**
                            Si es la primer instrucción del query "WHERE ..." se debe eliminar el operador lógico para que no cause error en el query.
                         */
                        $operadorLog = '';
                    }

                    /**
                        Si el valor a buscar es un "*" entonces el operador de igualdad se debe cambiar por un LIKE o NOT LIKE según sea el caso.
                    */
                    if(strpos($valor,'*') !== false)
                    {
                        $valor = str_replace('*','%',$valor);
                        if($operadorMat == '!=')
                        {
                            $operadorMat = str_replace('!=','NOT LIKE',$operadorMat);
                        }
                        else
                        {
                            $operadorMat = str_replace('=','LIKE',$operadorMat);
                            $operadorMat = str_replace('>=','LIKE',$operadorMat);
                            $operadorMat = str_replace('<=','LIKE',$operadorMat);
                        }
                    }

                    $filtros_unicos .= "
                        ".$operadorLog." ".$alias_tabla.$campo." ".$operadorMat." '".$valor."'";

                    $iteracion++;
                }
            }
        }

        // filtros de listas
        $filtros_listas = "";

        /**
            Arma los condicionales del query para los campos tipo listas, pueden haber varias opciones de un mismo campo dentro de parentesis.
         */
        foreach($tablas_listas as $tabla => $campos)
        {
            if(!array_key_exists($tabla,$filtros_usados))
            {
                $filtros_usados[$tabla] = array();
            }
            $alias_tabla = 't_'.$prefijo.$tabla.'.';
            foreach($campos as $campo => $condiciones)
            {
                $filtros_usados[$tabla][$campo] = $campo;
                $cont_condiciones = 0;
                $filtros = '';
                $op_ppal = '';
                $iteracion = 0;
                $sp = '';
                foreach($condiciones as $key_dato => $condicion)
                {
                    $valor = $condicion['valor'];               // Valor a buscar en el campo
                    $operadorLog = $condicion['operadorLog'];   // Operador lógico que acompañará el campo
                    $operadorMat = $condicion['operadorMat'];   // Operador de igualdad que estará entre el campo y el valor a buscar en ese campo

                    if($iteracion == 0 && $filtros_unicos == '')
                    {
                        /**
                            Si es la primer instrucción del query "WHERE ..." se debe eliminar el operador lógico para que no cause error en el query.
                         */
                        $operadorLog = '';
                    }
                    elseif($iteracion == 0 && $filtros_unicos != '')
                    {
                        /**
                            Si no es la primer instrucción, entonces se captura el primer operador de la lista de valores asociados a un mismo campo y ese operador
                            se va a usar en la parte externa de unos parentesis, los demás valores de ese mismo campo serán usados dentro del parentesis.
                         */
                        $op_ppal = $operadorLog;
                        $operadorLog = '';
                    }

                    /**
                        Si el valor a buscar es un "*" entonces el operador de igualdad se debe cambiar por un LIKE o NOT LIKE según sea el caso.
                    */
                    if(strpos($valor,'*') !== false)
                    {
                        $valor = str_replace('*','%',$valor);
                        if($operadorMat == '!=')
                        {
                            $operadorMat = str_replace('!=','NOT LIKE',$operadorMat);
                        }
                        else
                        {
                            $operadorMat = str_replace('=','LIKE',$operadorMat);
                            $operadorMat = str_replace('>=','LIKE',$operadorMat);
                            $operadorMat = str_replace('<=','LIKE',$operadorMat);
                        }
                    }

                    $filtros .= $operadorLog." ".$alias_tabla.$campo." ".$operadorMat." '".$valor."'
                                ";

                    $iteracion++;
                    $cont_condiciones++;
                }

                if($iteracion > 1)
                {
                    $filtros = "
                        $op_ppal (
                            ".$filtros."
                        )";
                }
                elseif($iteracion == 1)
                {
                    $filtros = $op_ppal.$filtros."
                            ";
                }

                $filtros_listas .= $filtros;
                $op_ppal = '';
            }
        }

        // Filtros filas
        $filtros_filas = "";

        /**
            Arma los condicionales del query para los campos tipo filas, pueden haber varias opciones de un mismo campo o campos diferentes de una misma fila, dentro de parentesis.
         */
        foreach($tablas_filas as $key => $filas)
        {
            $iteracion = 0;
            foreach($filas as $tabla => $campos)
            {
                if(!array_key_exists($tabla,$filtros_usados))
                {
                    $filtros_usados[$tabla] = array();
                }
                $op_ppal = '';
                $filtros = '';
                $alias_tabla = 't_'.$prefijo.$tabla.'.';
                foreach($campos as $campo => $condiciones)
                {
                    $filtros_usados[$tabla][$campo] = $campo;
                    $sp = '';
                    foreach($condiciones as $key_dato => $condicion)
                    {
                        $valor = $condicion['valor'];               // Valor a buscar en el campo
                        $operadorLog = $condicion['operadorLog'];   // Operador lógico que acompañará el campo
                        $operadorMat = $condicion['operadorMat'];   // Operador de igualdad que estará entre el campo y el valor a buscar en ese campo

                        if($iteracion == 0 && $filtros_unicos == '' && $filtros_listas == '' && $filtros_filas == '')
                        {
                            /**
                                Si es la primer instrucción del query "WHERE ..." se debe eliminar el operador lógico para que no cause error en el query.
                             */
                            $operadorLog = '';
                        }
                        elseif($iteracion == 0 && ($filtros_unicos != '' || $filtros_listas != '' || $filtros_filas != ''))
                        {
                            /**
                                Si no es la primer instrucción, entonces se captura el primer operador de la lista de valores asociados a un mismo campo o la misma fila y ese operador
                                se va a usar en la parte externa de unos parentesis, los demás valores de ese mismo campo o misma fila serán usados dentro del parentesis.
                             */
                            $op_ppal = $operadorLog;
                            $operadorLog = '';
                        }

                        /**
                            Si el valor a buscar es un "*" entonces el operador de igualdad se debe cambiar por un LIKE o NOT LIKE según sea el caso.
                        */
                        if(strpos($valor,'*') !== false)
                        {
                            $valor = str_replace('*','%',$valor);
                            if($operadorMat == '!=')
                            {
                                $operadorMat = str_replace('!=','NOT LIKE',$operadorMat);
                            }
                            else
                            {
                                $operadorMat = str_replace('=','LIKE',$operadorMat);
                                $operadorMat = str_replace('>=','LIKE',$operadorMat);
                                $operadorMat = str_replace('<=','LIKE',$operadorMat);
                            }
                        }

                        $filtros .= $operadorLog." ".$alias_tabla.$campo." ".$operadorMat." '".$valor."'
                                ";
                    }
                    $iteracion++;
                }
                $filtros = "
                        $op_ppal (
                            ".$filtros."
                        )";

                $filtros_filas .= $filtros;
                $op_ppal = '';
            }
        }

        /*********************************************************************************************************************
                                            Recorrer array de tablas para armar los JOIN's
        *********************************************************************************************************************/
        //$join = 'LEFT JOIN';
        $join = array('join'=>'','campo_join_ant'=>'','tabla_join_ant'=>'','on'=>'');
        $join_tablas = '';
        $limpiar_colunas = array();
        foreach($tablas_cruce as $key_cruce => $value_cruce)
        {
            $cruce_campos = '';
            if($join['campo_join_ant'] != '')
            {
                $cruce_campos = "(t_".$key_cruce.".".$value_cruce." = ".$join['campo_join_ant'].")";
            }

            $join_tablas .= "".$join['join']."
                        $key_cruce AS t_".$key_cruce." ".$join['on']." ".$cruce_campos."
                        ";

            if($join['campo_join_ant'] == '')
            {
                $join['campo_join_ant'] = 't_'.$key_cruce.'.'.$value_cruce;
                $join['join'] = 'INNER JOIN';
                $join['on'] = 'ON';
            }
        }

        // columnas_filtros, que conformarán el select, pero si la tabla a la que pertenece el campo no está dentro de las tablas de cruce
        // esto generaría un error, por lo tanto, se saca de las columnas a ver.
        $select_campos = '';
        $separar = '';
        $enc_campos = array();
        /**
            Se recorre la variable de campos concatenados que corresponden a las columnas que se han seleccionado en las opciones del reporte de caracterización
            y que se quieren ver en el resultado del reporte.
        */
        if($columnas_filtros != '')
        {
            // echo '<pre>';print_r($columnas_filtros);echo '</pre>';
            $explode_entrada = explode('||',$columnas_filtros);
            foreach($explode_entrada as $key_tb => $value_rb)
            {
                $explode_bloque = explode('|',$value_rb);
                $tabla = $explode_bloque[0];
                $campo = $explode_bloque[1];

                // validación para saber si la tabla relacionada al campo existe dentro de las tablas de cruce, sino entonces no se tendrá en cuenta
                // ese campo para el select del query, Es decir, no se podrá pretender mostrar un campo que cuya tabla a la que pertenece no esta en el JOIN porque generará error del query.
                if(array_key_exists($prefijo.$tabla,$tablas_cruce))
                {
                    $select_campos .= $separar." t_".$prefijo.$tabla.".".$campo." AS ".$prefijo.$tabla."_".$campo."
                        ";
                    if(!array_key_exists($tabla,$enc_campos))
                    {
                        $enc_campos[$tabla] = array();
                    }
                    $enc_campos[$tabla][$prefijo.$tabla.'_'.$campo] = $prefijo.$tabla.'_'.$campo;
                    $separar = ',';
                }
            }
        }
        else
        {
            $select_campos = '*';
        }

        // SE UNEN TODAS LAS PARTES DEL QUERY SEGÚN LOS FILTROS Y TABLAS SELECCIONADAS.
        $select = "
                SELECT   ".$select_campos;
        $from = "FROM    ".$join_tablas;
        $consulta =
                $select."
                ".$from."
                WHERE   $filtros_unicos $filtros_listas $filtros_filas
                ORDER BY 1";

        // Sección para ver el SQL que generó el reporte, solo está habilitado para los códigos a continuación.
        if($wuse == '03150-01' || $wuse == '03636-01')
        {
            $data['sql'] = '
                <img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;
                <span style="cursor:pointer;" onClick="javascript:verSeccionCaracterizacion(\'ver_query\');">Ver sql</span>
                <div style="display:none;background-color:white;border:2px solid orange;" id="ver_query">
                    <table style="width:100%;" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td>
                                <textarea style="height:150px;width:590px;">'.print_r(str_replace('                ','',$consulta),true).'</textarea>
                            </td>
                        </tr>
                    </table>
                </div>';
        }
        // echo '<pre>';print_r($consulta);echo '</pre>';
        // echo '<pre>';print_r($enc_campos);echo '</pre>';
        // echo '<pre>';print_r($filtros_usados);echo '</pre>';

        //--
        // se hace una consulta de las descripciones de los campos o filtros del reportes en  los que se escribió información.
        $imp = implode(',',array_keys($filtros_usados));
        $imp = str_replace(',',"','",$imp);

        $query = "
            SELECT  f.nombre AS nombre_tabla, f.medico AS prefijo, f.codigo AS tabla, det.Descripcion AS campo, rt.Dic_Descripcion AS desc_campo, det.comentarios AS ref_params
            FROM    formulario AS f, det_formulario AS det, root_000030 AS rt
            WHERE   det.medico  = '".$wbasedato."'
                    AND det.codigo IN ('".$imp."')
                    AND rt.Dic_Usuario = det.medico
                    AND rt.Dic_Formulario = det.codigo
                    AND rt.Dic_Campo = det.campo
                    AND f.medico = det.medico
                    AND f.codigo = det.codigo";

        $result = mysql_query($query,$conex);
        $nomb_campos_usados = array(); // En este array se guardan todos los nombres de los campos o filtros que fueron usados en el reporte, campos en los que escribió información.
        while($row = mysql_fetch_array($result))
        {
            $llave = $row['tabla'];

            if(array_key_exists($llave,$filtros_usados) && array_key_exists($row['campo'],$filtros_usados[$llave]))
            {
                if(!array_key_exists($llave,$nomb_campos_usados))
                {
                    $nomb_campos_usados[$llave] = array();
                    $nomb_campos_usados[$llave][] = $row['nombre_tabla'];// la primera posición es el nombre de la tabla a la que pertenecen los demás campos.
                }
                // $llave_campo = $row['prefijo'].'_'.$row['tabla'].'_'.$row['campo'];
                // $nomb_campos_usados[$llave][$llave_campo] = array('desc_campo'=>$row['desc_campo'],'ref_params'=>$row['ref_params']);
                $nomb_campos_usados[$llave][] = $row['desc_campo'];
            }
        }

        $nombres_filtros = '';
        $cm = '&nbsp;';
        /**
            Se imprime en código html los nombres de los campos que fuerón usados en el reporte para buscar información, son los campos en los que se
            escribió o se seleccionó algún tipo de dato.
         */
        foreach($nomb_campos_usados as $tabla => $campos)
        {
            $nombres_filtros .= $cm.'<span style="font-weight:bold;">['.$campos[0].']: </span>';
            unset($campos[0]);
            $nombres_filtros .= implode(', ',$campos);
            $cm = '.<br />&nbsp;';
        }
        $data['filtros'] = '<div onClick="javascript:verSeccionCaracterizacion(\'div_filtros_usados\');" style="font-size:8pt;text-align:justify;cursor:pointer;color:#999999;font-weight:bold;width:100%;">
                            &nbsp;<img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">
                                Filtros usados
                            </div>
                            <div id="div_filtros_usados" style="font-size:8pt;text-align:justify;display:none;">
                            '.$nombres_filtros.'
                            </div>';
        // echo '<pre>';print_r($nombres_filtros);echo '</pre>';

        /**
            * Se hace una consulta de las descripciones de los campos que fueron seleccionados para ser vistos en el reporte

            * Se hace un impode a las tablas (sufijos) que harán parte del JOIN con el fin de traer todos los campos que pertenecen a cada tabla.
              Las columnas que se consultan aquí, más adelante podrán ser parte de las columnas del SELECT que dá origen al resultado del reporte.
         */
        $imp = implode(',',array_keys($enc_campos));
        $imp = str_replace(',',"','",$imp);

        $query = "
            SELECT  f.nombre AS nombre_tabla, f.medico AS prefijo, f.codigo AS tabla, det.Descripcion AS campo, rt.Dic_Descripcion AS desc_campo, det.comentarios AS ref_params
            FROM    formulario AS f, det_formulario AS det, root_000030 AS rt
            WHERE   det.medico  = '".$wbasedato."'
                    AND det.codigo IN ('".$imp."')
                    AND rt.Dic_Usuario = det.medico
                    AND rt.Dic_Formulario = det.codigo
                    AND rt.Dic_Campo = det.campo
                    AND f.medico = det.medico
                    AND f.codigo = det.codigo";
        // $data['sql'] = $query;
        // echo "<pre>";print_r($query);echo "</pre>";
        $result = mysql_query($query,$conex);

        $pintar_campos = array();   // En este array se guardan todos los nombres de los campos que se van a mostrar en el reporte
        $nom_tablas = array();      // En este array se guardan todos los nombres de las tablas que fueron asociadas en los filtro usados en el reporte
        $ref_params = array();      // En este array se guardan todos los campos que deben ser consultados en otras tablas parametrizables.
        while($row = mysql_fetch_array($result))
        {
            // $llave = $row['prefijo'].'_'.$row['tabla'];
            $llave = $row['tabla'];
            if(!array_key_exists($llave,$pintar_campos))
            {
                $pintar_campos[$llave] = array();
                $ref_params[$llave] = array();
            }
            if(!array_key_exists($row['tabla'], $nom_tablas))
            {
                $nom_tablas[$row['tabla']] = $row['nombre_tabla'];
            }
            $llave_campo = $row['prefijo'].'_'.$row['tabla'].'_'.$row['campo'];
            $pintar_campos[$llave][$llave_campo] = array('desc_campo'=>$row['desc_campo'],'ref_params'=>$row['ref_params']);

            if($row['ref_params'] != '')
            {
                $ref_params[$llave][$llave_campo] = $row['ref_params'];
            }
        }

        /** TABLAS CON CAMPOS QUE REQUIEREN CONSULTAR CÓDIGOS PARAMETRIZABLES EN OTRAS TRABLAS
            * En det_formulario no se pueden hacer estas relaciones más espécificas puesto que muchos de estos campos tienen códigos concatenados.

            * Si al generar el reporte se ven columnas que solo muestran códigos sin descripción es porque talvez haga falta incluir la relación en esta sección.

            Excepciones, casos en lo que se debe generar una relación a otra tabla porque no se había especificado en det_formulario, estos casos
            son por ejemplo cuando un campo esta relacionado a otra tabla pero este campo tiene más de un código separados por comas (,)
        */
        if(array_key_exists('000013',$enc_campos) && !array_key_exists($prefijo.'000013_Idepvi',$ref_params['000013']))
        {
            $ref_params['000013'][$prefijo.'000013_Idepvi'] = "2-root-000077-0001-0002";
        }
        if(array_key_exists('000024',$enc_campos) && !array_key_exists($prefijo.'000024_Cvidep',$ref_params['000024']))
        {
            $ref_params['000024'][$prefijo.'000024_Cvidep'] = "2-000029-0001-0002";
        }
        if(array_key_exists('000024',$enc_campos) && !array_key_exists($prefijo.'000024_Cvisvi',$ref_params['000024']))
        {
            $ref_params['000024'][$prefijo.'000024_Cvisvi'] = "2-root-000070-0001-0002";
        }
        if(array_key_exists('000024',$enc_campos) && !array_key_exists($prefijo.'000024_Cvissp',$ref_params['000024']))
        {
            $ref_params['000024'][$prefijo.'000024_Cvissp'] = "2-root-000071-0001-0002";
        }
        if(array_key_exists('000024',$enc_campos) && !array_key_exists($prefijo.'000024_Cvitra',$ref_params['000024']))
        {
            $ref_params['000024'][$prefijo.'000024_Cvitra'] = "2-root-000072-0001-0002";
        }
        if(array_key_exists('000024',$enc_campos) && !array_key_exists($prefijo.'000024_Cviaca',$ref_params['000024']))
        {
            $ref_params['000024'][$prefijo.'000024_Cviaca'] = "2-000030-0001-0002";
        }
        if(array_key_exists('000024',$enc_campos) && !array_key_exists($prefijo.'000024_Cviapa',$ref_params['000024']))
        {
            $ref_params['000024'][$prefijo.'000024_Cviapa'] = "2-000031-0001-0002";
        }
        if(array_key_exists('000024',$enc_campos) && !array_key_exists($prefijo.'000024_Cvical',$ref_params['000024']))
        {
            $ref_params['000024'][$prefijo.'000024_Cvical'] = "2-000040-0001-0002";
        }
        if(array_key_exists('000024',$enc_campos) && !array_key_exists($prefijo.'000024_Cvicmj',$ref_params['000024']))
        {
            $ref_params['000024'][$prefijo.'000024_Cvicmj'] = "2-000041-0001-0002";
        }
        if(array_key_exists('000019',$enc_campos) && !array_key_exists($prefijo.'000019_Faming',$ref_params['000019']))
        {
            $ref_params['000019'][$prefijo.'000019_Faming'] = "2-000010-0001-0002";
        }

        // echo "<pre>";print_r($enc_campos);echo "</pre>";
        //////////////////////////////////////////////////////////////////////////////////////////////////
        // echo "<pre>";print_r($ref_params);echo "</pre>";

        // consultar las tablas parametrizables o de relación a otras tablas
        /*
            1.  Se recorren todos los campos de las tablas que tengan referencias a otras tablas.
            2.  Se hace un explode a cada campo de referencia y se separan en un array.
            3.  Luego mediante un implode se vuelven a unir mediante un OR para se consultados en det_formulario y poder tener los nombres de las columnas en la tabla de referencia.
            4.  Luego se crea un array con esos nombres de campos.
            5.  Seguido se hace una consulta sobre la tabla de relación y en el select estarán los nombres de los campos anteriormente consultados.
            6.  Luego se guardan los resultados de cada consulta de cada campo de relacion en el array $ref_params.
        */
        foreach($ref_params as $tabla => $campos)
        {
            foreach($campos as $campo => $relacion)
            {
                // print_r(explode('-',$relacion));
                $explode = explode('-',$relacion);
                $cont_pos = count($explode);
                $num_campos = $explode[0];
                $tabla_prefijo = $wbasedato;
                $tabla_referen = '';

                /**
                    El condicional dice 3 porque es la cantidad de elementos separados por "-" que aparecen antes de los campos de descripción,
                    eso indica que la relación puede estar de la forma 2-PREFIJO-SUFIJO-0001-0002
                */
                if(($cont_pos - $num_campos) == 3)
                {
                    $tabla_prefijo = $explode[1];
                    $tabla_referen = $explode[2];
                    $i=3;
                    $columnas = array();// Columnas de la tabla de referencia en det_formulario
                    while($i < $cont_pos)
                    {
                        $columnas[] = " campo = '".$explode[$i]."' ";
                        $i++;
                    }
                    // print_r($columnas);
                }
                elseif(($cont_pos - $num_campos) == 2)
                {
                    /**
                        El condicional dice 2 porque es la cantidad de elementos separados por "-" que aparecen antes de los campos de descripción,
                        eso indica que la relación puede estar de la forma 2-SUFIJO-0001-0002 y el prefijo entonces se debe completar con $wbasedato
                    */
                    $tabla_referen = $explode[1];
                    $i=2;
                    $columnas = array();// Columnas de la tabla de referencia en det_formulario
                    while($i < $cont_pos)
                    {
                        $columnas[] = " campo = '".$explode[$i]."' ";
                        $i++;
                    }
                }

                $filtro_campos = implode(' OR ',$columnas);

                /**
                    consulta todos los campos que estan configurados en el campo de relación para luego poderlos usar en la consulta que se hará
                    a la tabla que está en este mismo campo de relación.
                */
                $query_param = "
                    SELECT  descripcion AS campo
                    FROM    det_formulario
                    WHERE   medico = '".$tabla_prefijo."'
                            AND codigo = '".$tabla_referen."'
                            AND (
                                ".$filtro_campos."
                            )
                    ORDER BY posicion";
                $result_cmps = mysql_query($query_param,$conex);
                $cmps_select = array();
                if(mysql_num_rows($result_cmps) > 0)
                {
                    while($r_cmp = mysql_fetch_array($result_cmps))
                    {
                        $cmps_select[] = $r_cmp['campo'];
                    }
                }

                $implode_cmps = implode(',',$cmps_select);
                // En la tabla de relación se consultan todas las opciones parametrizables

                $tabla_parametros = $tabla_prefijo.'_'.$tabla_referen;
                $adds = '';
                if($tabla_parametros == 'root_000034')
                {
                    $adds = "WHERE Barmun = '05001'"; // limíta solo a los municipios de Antiquia puesto que existen muchos barrios para guardarlos en este array de parámetros.
                }

                /**
                    Se hace la consulta a la tabla de parametrización, se listan y se extraen todos los registros guardados en cada tabla, esto ayudará a despues
                    buscar un código dentro de estas tablas y poder asi mostrar la descripción que debe aparecer a cambio del código.
                 */
                $query_opts = "
                    SELECT  ".$implode_cmps."
                    FROM    ".$tabla_parametros."
                    ".$adds."
                    ORDER BY 1";
                $result_opts = mysql_query($query_opts,$conex);
                $lista_opts = array();
                if(mysql_num_rows($result_opts) > 0)
                {
                    while($r_fila = mysql_fetch_array($result_opts))
                    {
                        // $cmps_select[] = $r_fila['campo'];
                        $cols = array();
                        $cod_reg = $r_fila[$cmps_select[0]];
                        foreach($cmps_select as $k => $campo_tbl)
                        {
                            $cols[] = $r_fila[$campo_tbl];
                        }
                        // print_r($cols);
                        if(!is_array($ref_params[$tabla][$campo]))
                        {
                            $ref_params[$tabla][$campo] = array();
                        }
                        $ref_params[$tabla][$campo][$cod_reg] = trim(implode(' - ',$cols));
                    }
                }
                // print_r($ref_params);
            }
        }
// print_r($ref_params);

        // Indicar cuales fueron los filtros usados
        // print_r($tablas_unicos);
        // print_r($tablas_listas);
        // print_r($tablas_filas);

        $resp_html = '';
        $filas_encontradas = 0;
        $total_columnas = 0;
        /**
            Si la consulta generó algún resultado entonces se recorre para armar la tabla html donde se mostrará la respuesta.
        */
        if($result = mysql_query($consulta,$conex))
        {
            $filas_encontradas = mysql_num_rows($result);
            $resp_html .= '
                            <tr class="encabezadoTabla">';

            $tds_campos = '';

            /**
                * Se imprimen los nombres de las tablas que estan asociadas a los campos que se seleccionaron para ser vistos en el resultado del reporte
             */
            foreach($enc_campos as $tabla => $campos)
            {
                $colspan = count($campos);
                $total_columnas += $colspan;
                $resp_html .= "<td colspan='".$colspan."' align='center'>".utf8_encode($nom_tablas[$tabla])."</td>";

                /**
                    * Se imprimen los nombres de los campos que se seleccionaron para ser vistos en el resultado del reporte.
                */
                foreach($campos as $campo => $value)
                {
                    $texto = $pintar_campos[$tabla][$value]['desc_campo'];
                    $tds_campos .= "<td align='center'>".utf8_encode($texto)."</td>";
                }
            }
            $resp_html .= ' </tr>
                            <tr class="encabezadoTabla">
                                '.$tds_campos.'
                            </tr>';

            if(mysql_num_rows($result) > 0)
            {
                $cont_trs = 0;
                while($row = mysql_fetch_array($result))
                {
                    $cls = 'fila2';
                    if(($cont_trs%2)==0) { $cls = 'fila1'; }

                    $resp_html .= "
                            <tr class='".$cls."'>";
                    foreach($enc_campos as $tabla => $campos)
                    {
                        foreach($campos as $campo => $value)
                        {
                            $texto = $row[$value];
                            $e = explode(',',$texto);

                            /**
                                Si el campo a pintar está dentro de array de referencias a otras tablas entonces es muy probable que contenga un código,
                                siendo así se consulta ese código en el array de referencias y se busca la descripción a ese código.
                             */
                            if(array_key_exists($campo,$ref_params[$tabla]) && $texto != '') // si es un campo de relación a otra tabla entonces reemplace el código por el texto correspondiente al código relacionado.
                            {
                                /**
                                    Se comprueba si es un campo con códigos concatenados para buscar la descripción de cada código, sino entonces simplemente se
                                    busca la descripción al único código encontrado.
                                */
                                if(count(explode(',',$texto)) > 1)
                                {
                                    $expl_varios = explode(',',$texto);
                                    $arr_varios = array();
                                    foreach($expl_varios as $kv => $vl_v)
                                    {
                                        $arr_varios[] = $ref_params[$tabla][$campo][$vl_v];
                                    }
                                        // print_r($arr_varios);
                                    $texto = implode(', ',$arr_varios);
                                }
                                else
                                {
                                    $texto = $ref_params[$tabla][$campo][$texto];
                                }
                                // print_r($ref_params[$tabla][$campo]);
                            }
                            else
                            {
                                // echo $campo.'|||';
                            }

                            /**
                                En algunos casos, en el campo donde se guarda el lugar de nacimiento se guardaron unas etiquetas html que no deberían estar asi
                                pero igualmente se limpia el texto a imprimir para que no se vean estas etiquetas en el reporte además que es posible que dañe la tabla
                                del reporte generado.
                            */
                            $texto = trim($texto);
                            $texto = str_replace('<TD>','',$texto);
                            $texto = str_replace('<TD','',$texto);
                            $texto = str_replace('</TD>','',$texto);
                            $texto = str_replace('CLASS=','',$texto);
                            if($texto == 'on' || $texto == 'off') { $texto = ($texto=='on') ? 'Si': 'No';}
                            $resp_html .= "<td align='left'>".utf8_encode($texto)."</td>"; // se imprime cada columna en el reporte.
                        }
                    }
                    $resp_html .= "
                            </tr>";
                    $cont_trs++;
                }
            }
            else
            {
                $resp_html .= "
                            <tr>
                                <td  colspan='".$total_columnas."' align='center' class='fila2'>NO SE ENCONTRARON DATOS PARA ESTA CONSULTA</td>
                            </tr>";
            }

            // Encabezado de la tabla de respuesta: Total, tablas a las que pertenecen los campos a pintar, campos a pintar
            $resp_html = '
                        <table align="center" id="Exportar_a_Excel" style="width:100%">
                            <tr>
                                <td colspan="'.($total_columnas).'">'.$data['filtros'].'</td>
                            </tr>
                            <tr>
                                <td colspan="'.($total_columnas).'" align="left" style="font-weight:bold;">Total: '.$filas_encontradas.'</td>
                            </tr>
                            '.(($total_columnas == 0) ? '' : $resp_html).'
                        </table>';


            $data['html'] = utf8_encode($resp_html);
            $data['mensaje'] = 'Se ejecuto consulta.';
        }
        else
        {
            $data['error'] = 1;
            $data['mensaje'] = '[!] Hubo algun problema al ejecutar el reporte. No se pudo ejecutar el reporte.';
        }
        echo json_encode($data);
    }
    return;
}
include_once("root/comun.php");

/**
    Este array contiene todos los datos de parametrización que se irán consultando y posteriormente se van a imprimir en la sección de código html más abajo.
*/
$identGeneral = array();

/**
    Aquí inician todas las consultas que implican mostrar opciones parametrizadas (opciones de selects ckeckbox, ...)
*/


/*********************************
* Datos de Genero
*/
$qSex= "    SELECT  Sexcod, Sexdes
            FROM    root_000029
            WHERE   Sexest = 'on'";
$rSex = mysql_query($qSex,$conex);
$genero = array();
while($row = mysql_fetch_array($rSex))
{
    $genero[$row['Sexcod']] = $row['Sexdes'];
}
$identGeneral['genero'] = $genero;

/*********************************
* Datos de Municipios
*/
$qmun= "    SELECT  Codigo, Nombre
            FROM    root_000006
            WHERE   Codigo like ('05%')
            ORDER BY Nombre";
$rmun = mysql_query($qmun,$conex);
$municipios = array();
while($row = mysql_fetch_array($rmun))
{
    $municipios[$row['Codigo']] = $row['Nombre'];
}
$identGeneral['municipios'] = $municipios;

/*********************************
* Datos de Barrios
*/
$barrios = array();

$identGeneral['barrios'] = $barrios;

/*********************************
* Datos Paises visa
*/
$qEsco= "   SELECT  Paicod AS cod_pais, Painom AS nombre
            FROM    root_000077
            WHERE   Paiest = 'on'
            ORDER BY Painom";
$rEsco = mysql_query($qEsco,$conex);
$paises = array();
while($row = mysql_fetch_array($rEsco))
{
    $paises[$row['cod_pais']] = utf8_encode($row['nombre']);
}
$identGeneral['paises'] = $paises;

/*********************************
* Datos de estado civil
*/
$qEstC= "   SELECT  Scvcod AS id, Scvdes AS est_civil
            FROM    root_000065
            WHERE   Scvest = 'on'
            ORDER BY Scvdes";
$rstc = mysql_query($qEstC,$conex);
$estados_civiles = array();
while($row = mysql_fetch_array($rstc))
{
    $estados_civiles[$row['id']] = $row['est_civil'];
}
$identGeneral['estado_civil'] = $estados_civiles;

/*********************************
* Datos de Escolaridad
*/
$qEsco= "   SELECT  Scocod AS id, Scodes AS nombre, Scoley AS req_deLey, Scoine AS interno_externo
            FROM    root_000066
            WHERE   Scoest = 'on'
            ORDER BY Scodes";
$rEsco = mysql_query($qEsco,$conex);
$grado_escolar = array();
while($row = mysql_fetch_array($rEsco))
{
    $grado_escolar[$row['id']] = array('nombre'=>$row['nombre'],'req_deLey'=>$row['req_deLey'],'interno_externo'=>$row['interno_externo']);
}
$identGeneral['grado_escolar'] = $grado_escolar;

/*********************************
* Datos de Ocupaciones
*/
$q = "  SELECT  Ocucod AS id, Ocudes
        FROM    root_000078
        WHERE   Ocuest = 'on'
        ORDER BY Ocudes";
$res = mysql_query($q,$conex);
$ocupaciones = array();
while($row = mysql_fetch_array($res))
{
    $ocupaciones[$row['id']] = $row['Ocudes'];
}
$identGeneral['ocupaciones'] = $ocupaciones;

/*********************************
* Datos Relacion de Escolaridad Ley, cargo y centro de costos
*/
$qEsco= "   SELECT  Scocod AS id, Carcod AS cargo_ley, Ccocod AS ccosto_ley
            FROM    ".$wbasedato."_000044
            WHERE   Recest = 'on'";
$rEsco = mysql_query($qEsco,$conex);
$grado_escolar_ley = array();
while($row = mysql_fetch_array($rEsco))
{
    $grado_escolar_ley[$row['id']] = array('cargo_ley'=>$row['cargo_ley'],'ccosto_ley'=>$row['ccosto_ley']);
}
$identGeneral['grado_escolar_ley'] = $grado_escolar_ley;

/*********************************
* Datos, Tipos Capacitaciones
*/
$qTCap= "   SELECT  Tcapcod AS id, Tcades AS t_capacitacion
            FROM    ".$wbasedato."_000011
            WHERE   Tcaest = 'on'
            ORDER BY Tcades ";
$rTCap = mysql_query($qTCap,$conex);
$tipo_capacitaciones = array();
while($row = mysql_fetch_array($rTCap))
{
    $tipo_capacitaciones[$row['id']] =  $row['t_capacitacion'];
}
$identGeneral['tip_capacitacion'] = $tipo_capacitaciones;

/*********************************
* Datos de parentescos
*/
$qParen= "  SELECT  Parcod AS id, Pardes
            FROM    root_000067
            WHERE   Parest = 'on'
            ORDER BY Pardes";
$rParen = mysql_query($qParen,$conex);
$parentescos = array();
while($row = mysql_fetch_array($rParen))
{
    $parentescos[$row['id']] = $row['Pardes'];
}
$identGeneral['parentescos'] = $parentescos;

/*********************************
* Datos de EPS's
*/
$qEps= "    SELECT  Epscod, Epsnom
            FROM    root_000073
            WHERE   Epsest = 'on'
            ORDER BY Epsnom";
$rEps = mysql_query($qEps,$conex);
$lista_eps = array();
while($row = mysql_fetch_array($rEps))
{
    $lista_eps[$row['Epscod']] = $row['Epsnom'];
}
$identGeneral['lista_eps'] = $lista_eps;

/*********************************
* Datos Unidades de servicio - Centros de costos
*/
$centro_costos = getOptionsCostos($wemp_pmla, $conex, $wbasedato, '', '', 'off');

$cargos = getOptionsCargos($wemp_pmla, $conex, $wbasedato, '', '', 'off');

/*********************************
* Consulta tenecia de vivienda
*/
$qTv= "     SELECT  Tencod AS id, Tendes AS tenencia
            FROM    root_000068
            WHERE   Tenest = 'on'
            ORDER BY Tendes";
$rTv = mysql_query($qTv,$conex);
$tenencia_vivienda = array();
while($row = mysql_fetch_array($rTv))
{
    $tenencia_vivienda[$row['id']] =  array('tenencia'=>$row['tenencia']);
}
$identGeneral['tenencia_vivienda'] = $tenencia_vivienda;

/*********************************
* Datos Tipos de vivienda
*/
$qTsal= "   SELECT  Tpvcod AS id, Tpvdes AS t_vivienda
            FROM    root_000069
            WHERE   Tpvest = 'on'
            ORDER BY Tpvdes";
$rTsal = mysql_query($qTsal,$conex);
$tipo_vivienda = array();
while($row = mysql_fetch_array($rTsal))
{
    $tipo_vivienda[$row['id']] =  array('t_vivienda'=>$row['t_vivienda']);
}
$identGeneral['tipo_vivienda'] = $tipo_vivienda;

/*********************************
* Datos Estados de vivienda
*/
$q= "   SELECT  Esvcod AS id, Esvdes AS estado, Esvrcm AS req_complemento, Esvmcm AS msj_complemento
        FROM    root_000070
        WHERE   Esvest = 'on'
        ORDER BY Esvdes";
$res = mysql_query($q,$conex);
$estados_vivienda = array();
while($row = mysql_fetch_array($res))
{
    $estados_vivienda[$row['id']] =  array('estado'=>$row['estado'],'req_complemento'=>$row['req_complemento'],'msj_complemento'=>$row['msj_complemento']);
}
$identGeneral['estados_vivienda'] = $estados_vivienda;

/*********************************
* Datos Tipo Servicios público
*/
$q = "  SELECT  Sspcod AS id, Sspdes AS servicio_publico
        FROM    root_000071
        WHERE   Sspest = 'on'
        ORDER BY Sspdes";
$res = mysql_query($q,$conex);
$servicios_publicos = array();
while($row = mysql_fetch_array($res))
{
    $servicios_publicos[$row['id']] =  array('servicio_publico'=>$row['servicio_publico']);
}
$identGeneral['servicios_publicos'] = $servicios_publicos;

/*********************************
* Datos Tipo transporte
*/
$q = "  SELECT  Tracod AS id, Trades AS tipo_transporte
        FROM    root_000072
        WHERE   Traest = 'on'
        ORDER BY Trades";
$res = mysql_query($q,$conex);
$transporte = array();
while($row = mysql_fetch_array($res))
{
    $transporte[$row['id']] =  array('tipo_transporte'=>$row['tipo_transporte']);
}
$identGeneral['transporte'] = $transporte;

/*********************************
* Datos Tipo almuerzo habitual
*/
$q = "  SELECT  Almcod AS id, Almdes AS tipo_almuerzo
        FROM    ".$wbasedato."_000040
        WHERE   Almest = 'on'";
$res = mysql_query($q,$conex);
$tipo_almuerzo = array();
while($row = mysql_fetch_array($res))
{
    $tipo_almuerzo[$row['id']] =  array('tipo_almuerzo'=>$row['tipo_almuerzo']);
}
$identGeneral['tipo_almuerzo'] = $tipo_almuerzo;

/*********************************
* Datos Tipo actividades recreativas
*/
$q = "  SELECT  Arccod AS id, Arcdes AS tipo_recreativa
        FROM    ".$wbasedato."_000029
        WHERE   Arcest = 'on'";
$res = mysql_query($q,$conex);
$tipo_recreativas = array();
while($row = mysql_fetch_array($res))
{
    $tipo_recreativas[$row['id']] =  array('tipo_recreativa'=>$row['tipo_recreativa']);
}
$identGeneral['tipo_recreativas'] = $tipo_recreativas;

/*********************************
* Datos Tipo artes - actividades culturales y artísticas
*/
$q = "  SELECT  Acacod AS id, Acades AS tipo_artes
        FROM    ".$wbasedato."_000030
        WHERE   Acaest = 'on'";
$res = mysql_query($q,$conex);
$tipo_artes = array();
while($row = mysql_fetch_array($res))
{
    $tipo_artes[$row['id']] =  array('tipo_artes'=>$row['tipo_artes']);
}
$identGeneral['tipo_artes'] = $tipo_artes;

/*********************************
* Datos Tipo artes - actividades culturales y artísticas
*/
$q = "  SELECT  Acecod AS id, Acedes AS tipo_educativa
        FROM    ".$wbasedato."_000031
        WHERE   Aceest = 'on'";
$res = mysql_query($q,$conex);
$tipo_educativa = array();
while($row = mysql_fetch_array($res))
{
    $tipo_educativa[$row['id']] =  array('tipo_educativa'=>$row['tipo_educativa']);
}
$identGeneral['tipo_educativa'] = $tipo_educativa;

/*********************************
* Datos Roles
*/
$q = "  SELECT  t_rol.Rolcod AS id, t_rol.Roldes AS tipo_rol, t_rol.Rolobr AS observ_requerida
                , org.id AS id_participa, org.Oincod AS org_interno, org.Oinobs AS observacion
        FROM    root_000074 AS t_rol
                LEFT JOIN
                ".$wbasedato."_000026 as org ON (t_rol.Rolcod = org.Oincod)
        WHERE   t_rol.Rolest = 'on'";
$res = mysql_query($q,$conex);
$tipo_rol = array();
while($row = mysql_fetch_array($res))
{
    $tipo_rol[$row['id']] =  array( 'tipo_rol'=>$row['tipo_rol'],'observ_requerida'=>$row['observ_requerida'],
                                    'id_participa'=>$row['id_participa'],'org_interno'=>$row['org_interno'],
                                    'observacion'=>$row['observacion']);
}
$identGeneral['tipo_rol'] = $tipo_rol;

/*********************************
* Datos Tipo acompañantes
*/
$qTAcomp = "SELECT  t_acomp.Acocod AS id, t_acomp.Acodes AS t_acompanante_des, vive.Famaco AS acompanante
            FROM    root_000075 AS t_acomp
                    LEFT JOIN
                    ".$wbasedato."_000019 as vive ON (t_acomp.Acocod = vive.Famaco)
            WHERE   t_acomp.Acoest = 'on'
            ORDER BY t_acomp.Acodes";

$rTAcomp = mysql_query($qTAcomp,$conex);
$tipo_acompanantes = array();
while($row = mysql_fetch_array($rTAcomp))
{
    $tipo_acompanantes[$row['id']] =  array('t_acompanante_des'=>$row['t_acompanante_des'],'acompanante'=>$row['acompanante']);
}
$identGeneral['tipo_acompanantes'] = $tipo_acompanantes;

/*********************************
* Datos Tipo salarios
*/
$qTsal= "   SELECT  sl.Tincod AS id, sl.Tindes AS tipo_salario
                    , Tinrcm AS req_complemento, Tinmcm AS mjs_complemento
            FROM    ".$wbasedato."_000010 AS sl
            WHERE   sl.Tinest = 'on'
            ORDER BY sl.Tindes";
$rTsal = mysql_query($qTsal,$conex);
$tipo_salarios = array();
while($row = mysql_fetch_array($rTsal))
{
    $tipo_salarios[$row['id']] =  array('tipo_salario'=>$row['tipo_salario'],'req_complemento'=>$row['req_complemento'],'mjs_complemento'=>$row['mjs_complemento']);
}
$identGeneral['tipo_salarios'] = $tipo_salarios;

/*********************************
* Datos Repositorio de preguntas
*/
$q = "  SELECT  reppre.Precod as id, reppre.Predes AS pregunta, reppre.Preraf AS req_afirmacion, reppre.Prerrs AS req_respuesta, reppre.Premsr AS msj_respuesta
                , res.id AS id_respuesta, res.Resafi AS afirmacion, res.Resres AS respuesta
        FROM    ".$wbasedato."_000012 AS reppre
                LEFT JOIN
                ".$wbasedato."_000027 as res ON (reppre.Precod = res.Rescpr)
        WHERE   reppre.Preest = 'on'";
$res = mysql_query($q,$conex);
$repositorio_preguntas = array();
while($row = mysql_fetch_array($res))
{
    $repositorio_preguntas[$row['id']] =  array( 'pregunta'=>$row['pregunta'],'req_respuesta'=>$row['req_respuesta'],'msj_respuesta'=>$row['msj_respuesta'],
                                    'id_respuesta'=>$row['id_respuesta'],'req_afirmacion'=>$row['req_afirmacion'],'afirmacion'=>$row['afirmacion'],
                                    'respuesta'=>$row['respuesta']);
}
$identGeneral['repositorio_preguntas'] = $repositorio_preguntas;


/**
    Es función se encarga de pintar la opción o las opciones para seleccionar los operadores lógico u operadores de igualdad que siempre van a acompañar cada campo
    en el formulario del reporte (filtros del reporte). Estas son las opciones AND, OR, =, !=, >, ...
 */
function pintarOperadores($prefijo_campo, $vista_opr='')
{
    if($vista_opr=='compactar')
    {
        $td_operadores = '
            <td><div id="div_logic_'.$prefijo_campo.'" class="campoOperador" onclick="posicionElemento(this,\'logic_'.$prefijo_campo.'\',\'div_logic_'.$prefijo_campo.'\',\'logico\');">AND</div><input class="" type="hidden" id="logic_'.$prefijo_campo.'" name="logic_'.$prefijo_campo.'" value="AND" />
                <div id="div_mat_'.$prefijo_campo.'" class="campoOperador" onclick="posicionElemento(this,\'mat_'.$prefijo_campo.'\',\'div_mat_'.$prefijo_campo.'\',\'matematico\');">=</div><input class="" type="hidden" id="mat_'.$prefijo_campo.'" name="mat_'.$prefijo_campo.'" value="=" /></td>';
    }
    else
    {
        $td_operadores = '
             <td><div id="div_logic_'.$prefijo_campo.'" class="campoOperador" onclick="posicionElemento(this,\'logic_'.$prefijo_campo.'\',\'div_logic_'.$prefijo_campo.'\',\'logico\');">AND</div><input class="" type="hidden" id="logic_'.$prefijo_campo.'" name="logic_'.$prefijo_campo.'" value="AND" /></td>
             <td><div id="div_mat_'.$prefijo_campo.'" class="campoOperador" onclick="posicionElemento(this,\'mat_'.$prefijo_campo.'\',\'div_mat_'.$prefijo_campo.'\',\'matematico\');">=</div><input class="" type="hidden" id="mat_'.$prefijo_campo.'" name="mat_'.$prefijo_campo.'" value="=" /></td>';
    }
    return $td_operadores;
}

echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';
echo '<input type="hidden" id="wtema" name="wtema" value="'.$wtema.'" />';
echo '<input type="hidden" id="wcodtab" name="wcodtab" value="'.$wcodtab.'" />';
echo '<input type="hidden" id="wuse" name="wuse" value="'.$wuse.'" />';

$indicador = '&nbsp;&raquo;';

$wactualiz = "(Julio 10 de 2013)";
/*****************************************************************************************************************************************/
?>
<html lang="es-ES">
<head>
<title>Gesti&oacute;n de Talento Humano</title>
<meta charset="utf-8">
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script type="text/javascript">

    $(document).ready(function() {

        /**
            Inicializa la funcionalidad para generar la exportación a excel.
        */
        $(".botonExcel").click(function(event) {
            $("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
            $("#FormularioExportacion").submit();
        });

        /**
            Inicializa el div flotante fijo que se despliega al momento de dar clic en el operador lódigo o el operador de igualdad.
        */
        var posicion = $("#caja_flotante").offset();
        var margenSuperior = 15;
         $(window).scroll(function() {
             if ($(window).scrollTop() > posicion.top) {
                 $("#caja_flotante").stop().animate({
                     // marginTop: $(window).scrollTop() - posicion.top + margenSuperior
                 });
             } else {
                 $("#caja_flotante").stop().animate({
                     marginTop: 0
                 });
             };
        });

        /**
            Inicializa el div flotante deslizante que está desplegado en la parte superior izquierda de la pantalla, sobre la cuál aparecen opciones adicionales para el reporte.
        */
        var posicion_query = $("#caja_flotante_query").offset();
        var margenSuperior_query = 15;
         $(window).scroll(function() {
             if ($(window).scrollTop() > posicion_query.top) {
                 $("#caja_flotante_query").stop().animate({
                     marginTop: $(window).scrollTop() - posicion_query.top + margenSuperior_query
                 });
             } else {
                 $("#caja_flotante_query").stop().animate({
                     marginTop: 0
                 });
             };
         });

        /**
            Se asigna la clase unico_consulta a todos los elementos tipo select o text que inicialmente aparezcan al momento de abrir la vista del reporte.
            No es necesario hacerlo cada vez que se crea un nuevo campo puesto que al hacerlo se hace en base a la clonación de un objeto y por eso hereda
            esa propiedad.
        */
        $('input:text').addClass('unico_consulta');
        $('select').addClass('unico_consulta');

        $('#exec_query').val('listar_columnas'); // Valor cualquiera por defecto para saber que es el primer ingreso luego de seleccionar en el menú.
        seleccionarColumnas('ready');
    });

    /**
        Esta función se ejecuta cada vez que hay un onblur u onchange en los campos que tienen asociada el nombre de clase .unico_consulta
    */
    $(function(){
        $('.unico_consulta').on({
            blur : function() { armarQuery(); resaltarLleno(); },
            change: function() { armarQuery(); resaltarLleno(); }
        });
    });

    /**
        Función encargada de resaltar los campos en el reporte sobre los cuales se a ingresado o seleccionado información.
     */
    function resaltarLleno()
    {
        /**
            Recorre todos los campos unicos para ver si tienen algún dato digitado o seleccionado.
        */
        $('.unicos').each(function() {
            //id = $(this).attr('id');
            campo_txt = $(this).find('select,input:text');//:selected,input:text

            idcmp_txt = campo_txt.attr('id');

            //es suficiente con validar solo valor_txt puesto que siempre debe estar acompañado de un operador y una operación
            if(idcmp_txt) // puede ser que lea una columna sin nada como por ejemplo la de adicionar columnas, si hay valor sigue buscando dentro
            {
                valor_txt = getValorCampo(idcmp_txt);

                if(valor_txt != '')
                {
                    $('#'+idcmp_txt).css({'background-color':'lightgreen'});
                }
                else
                {
                    $('#'+idcmp_txt).css({'background-color':''});
                }
            }
        });

        /**
            Recorre todos los campos tipo filas para ver si tienen algún dato digitado o seleccionado.
        */
        $('tr[id*=_tr_]').each(function() {
            id = $(this).attr('id');

            $('#'+id).find('> td').each(function() {
                existe = $(this).find('select,input:text').length;

                if(existe > 0)
                {
                    campo_txt = $(this).find('select,input:text');

                    idcmp_txt = campo_txt.attr('id');

                    var valor_txt = '';

                    if(idcmp_txt)
                    {
                        valor_txt = getValorCampo(idcmp_txt);

                        if(valor_txt != '')
                        {
                            $('#'+idcmp_txt).css({'background-color':'lightgreen'});
                        }
                        else
                        {
                            $('#'+idcmp_txt).css({'background-color':''});
                        }
                    }
                }
            });
        });

        /**
            Los campos tipo listas tienen otra manera de resaltarse y es poniendo un borde coloreado a lado izquierdo de la lista.
        */
    }

    /**
        Hace posible desplegar y ocultar las opciones o información adicional referenciado por un div.

        id: Es el id del div que se debe ocultar o mostrar.
    */
    function verSeccionCaracterizacion(id){
        $("#"+id).toggle("normal");
    }

    /**
        Se encarga de pintar información en otro elemento dependiendo de algún valor seleccionado en otro elemento
        id_padre:   es el elemento (this) a partir del cual se debe pintar el otro elemento, según el valor seleccionado se envía la pertición ajax y se construyen
                    las opciones para el elemento hijo.
        id_hijo:    Es el id del elemento sobre el cual se van a acargar las opciones generadas a partir del valor seleccionado en el elemento padre.
        accion:     Es el tipo de condicional en el que se debe meter la solicitud ajax para encontrar así mismo el condicional con el valor apropiado para la variable form,
                    y de esta manera retornar las opciones adecuadas para el elemento hijo.
        form:       Condicional que finalmente armará las opciones para el elemento hijo.
        vacio:      Es mas que todo un truco para el caso de municipios y barrios, vacío contendrá un id de un elemento tipo hidden que siempre estará vacío, esto se usa para el caso puntual
                    de seleccionar municipios y barrios, si se selecciona un municipio se debe resetear el select de barrios y para que ocurra y se vea reflejado en la tabla de BD,
                    se selecciona el municipio y se ejecuta el onblur para guardar el nuevo valor del municipio y seguidamente el select barrio queda vacío pero se ejecuta el blur
                    con el valor del campo hidden vacío y en la tabla de BD para barrio guarda ese valor vacío.
     */
    function recargarDependiente(id_padre, id_hijo, accion, form, vacio)
    {
        val = $("#"+id_padre.id).val();
        $('#'+id_hijo).load("../reportes/reporteador_caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion="+accion+"&id_padre="+val+"&form="+form+"&wuse="+$("#wuse").val());
    }

    /**
        Se encarga de adicionar una fila nueva a las tablas (o campos tipos filas del formulario-reporte)

        ele         : Es el elemento (this) que desencadena la acción.
        referencia  : Es el id de la tabla a la cual se le va a adicionar una nueva fila.
        sufijo      : Hace referencia a la primer fila de la tabla, toda tabla siempre va a tener una primera fila y en base a esta se van a crear o clonar todas las demás
                        filas para esa tabla, para la tabla cuyo id está en la variable "referencia".
        tipo_add    : En este momento no tiene una funcionalidad, no se está usando por el momento.
        reset_select: En este momento no tiene una funcionalidad, no se está usando por el momento (al igual que el parámetro tipo_add).

     */
    function addFilaTabla(ele,referencia,sufijo,tipo_add,reset_select)
    {
        /**
            Se clona en el objeto fila la primera fila de la tabla cuyo id viene en el parámetro "referencia".
        */
        fila = $("#"+referencia+" tr#"+sufijo).clone(true);

        // Se cuantan cuantas filas se han adicionado a la misma tabla
        trs = $("#"+referencia).find('tr[id$='+referencia+']').length;
        value_id = 0;

        /**
            Solo se permite adicionar un máximo de 10 filas por tabla, al momento de realizar algunas prueba se detectó que en algunos casos falló
            la correcta asignación de los id a las nuevas filas y los nuevos elementos de cada fila. Al crear más de 10 filas, todas esas filas quedaban referenciadas con el consecutivo
            o id generado para la fila 10.
        */
        if(trs < 10)
        {
            // Si hay mas de una fila entonces se cuenta cuantas hay y se selecciona el último consecutivo de más alto valor y crear el siguiente a partir de ese número.
            if(trs > 0)
            {
                id_mayor = 0;
                // buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
                $("#"+referencia).find('tr[id$='+referencia+']').each(function() {
                    id_ = $(this).attr('id');
                    id_splt = id_.split('_');
                    id_this = (id_splt[0])*1;
                    if(id_this >= id_mayor)
                    {
                        id_mayor = id_this;
                    }
                });
                id_mayor++;
                value_id = id_mayor+'_tr_'+referencia;
            }

            // Se genera la opción de eliminar fila enviandole el nuevo id para la nueva fila.
            btn_eliminar = '<img width="10" height="10" border="0" src="../../images/medical/eliminar1.png" title="Eliminar Fila" onclick="removerElemento(this,\''+value_id+'\');" >';

            // Se modifican los nuevos id y name para el objeto antes clonado.
            fila.attr('id',value_id);
            fila.attr('name',value_id);
            fila.find('div[id=1_tr_'+referencia+'_remover]').attr('id',value_id).html(btn_eliminar); //para la nueva fila se cambia la imagen de adicionar por una imagen para eliminar la fila

            // actualiza los id de los divs operadores logicos. 'div_logic_1_' es el inicio del id del div en la primera fila, siempre tendrá asociado el # _1_
            fila.find('div[id^=div_logic_1_]').each(function() {
                id = $(this).attr('id');
                id = id.replace('_1_','_'+id_mayor+'_');
                ev_d = $(this).attr('onclick');

                ev_d = ev_d.replace('_1_','_'+id_mayor+'_');
                ev_d = ev_d.replace('_1_','_'+id_mayor+'_'); // se debe crear un replace por cada coincidencia, en el caso de 'ev' se debe reemplazar en dos parámetros.

                $(this).removeAttr('onclick');
                $(this).click(add_event(ev_d));

                $(this).attr('id',id); // se actualiza el id de div_logic
                ev_d = '';

            });

            // actualiza los id de los divs operadores matematicos. 'div_mat_1_' es el inicio del id del div en la primera fila, siempre tendrá asociado el # _1_
            fila.find('div[id^=div_mat_1_]').each(function() {
                id_ = $(this).attr('id');
                id_ = id_.replace('_1_','_'+id_mayor+'_');
                ev = $(this).attr('onclick');

                ev = ev.replace('_1_','_'+id_mayor+'_');
                ev = ev.replace('_1_','_'+id_mayor+'_'); // se debe crear un replace por cada coincidencia, en el caso de 'ev' se debe reemplazar en dos parámetros.

                $(this).removeAttr('onclick');

                /**
                    El evento onclick no puede ser reemplazado directamente, lo que se hace si es eliminar el anterior evento onclick y se genera
                    un evento onclick global para ese nuevo elemento con el código de la nueva función onclick, esto lo hace la función add_event();
                */
                $(this).click(add_event(ev));

                $(this).attr('id',id_); // se actualiza el id de div_logic
            });

            // actualiza los id de los hidden de operadores logicos y operadores matematicos
            fila.find(':hidden[id*=_1_]').each(function() {
                id = $(this).attr('id');
                id = id.replace('_1_','_'+id_mayor+'_');

                $(this).attr('name',id); // se actualiza el id de logic_, campo donde se guarda el operador lógico
                $(this).attr('id',id); // se actualiza el id de logic_, campo donde se guarda el operador lógico
            });

            // Cada vez que se cree una nueva fila se cambia el operador lógico por a 'OR'
            fila.find('input:hidden').eq(0).val('OR');
            fila.find('input:hidden').eq(0).prev().html('OR');

            // se elimina color de fondo que posiblemente podían tener los campos de la fila anterior.
            fila.find('input').css({'background-color':''});
            fila.find('select').css({'background-color':''});

            // actualiza los id de los hidden de operadores logicos y operadores matematicos
            fila.find(':input[id*=1_]').each(function() {

                id = $(this).attr('id');
                id = id.replace('1_',id_mayor+'_');

                $(this).val(''); // se borra el value
                $(this).attr('name',id); // se actualiza el id de logic_, campo donde se guarda el operador lógico
                $(this).attr('id',id); // se actualiza el id de logic_, campo donde se guarda el operador lógico
            });

            // Finalmente se adiciona la nueva fila a la tabla con todos los ids actualizados de los elementos que están contenidos en la fila.
            $("#"+referencia).append(fila);
        }
        armarQuery();
    }

    /**
        Crea un evento onclick global para un elemento
    */
    function add_event(param) {
        return function() {
                    eval(param);
               };
    }

    /**
        Se encarga de adicionar un campo o filtro nuevo a una lista de filtros para un mismo campo.

        ele         : Es el elemento (this) que desencadena la acción.
        referencia  : Es el id del campo al cuál se le va a tomar el valor escrito para luego enviarlo a la lista de filtros creados para ese mismo campo.
        sufijo      : Hace referencia a la primer fila de la tabla, toda tabla siempre va a tener una primera fila y en base a esta se van a crear o clonar todas las demás
                        filas para esa tabla, para la tabla cuyo id está en la variable "referencia".
        tipo_add    : Es el tipo de elemento o campo que debe adicionar la lista de campos, puede ser: adicionar un nuevo campo tipo checkbox si es una lista de checkbox
                        ó adicionar un nuevo campo tipo text si es una lista de campos text.
        reset_select: Este campo se usa cuando se van a adicionar elementos a partir de un campo tipo select, si este campo está seteado se indica entonces
                        que en el select debe quedar seleccionada la opción "Seleccione..".

    */
    function addfiltro(ele,referencia,sufijo,tipo_add,reset_select)
    {
        id_html = '';
        txt = '';
        value_id = '';

        if(referencia != '')
        {
            id_html = referencia;
        }
        else
        {
            id_html = ele.id;
        }

        /**
            Se leen los atributos rel e in que es donde se almacena el sufijo de las tablas y el nombre del campo respectivamente.
        */
        param_in = $('[id="'+id_html+'"]').attr('in');
        param_rel = $('[id="'+id_html+'"]').attr('rel');

        tipo_campo = tipoCampo(id_html); // Se evalúa que tipo de campo es para poder leer el valor (si es text, select, etc)

        if (tipo_campo == 'radio')
        {
            value = $('[id="'+id_html+'"]:checked').val();
            value_id = value;
        }
        else if(tipo_campo == 'checkbox')
        {
            value = $('[id="'+id_html+'"]:checked').val();
            value_id = value;
        }
        else if(tipo_campo == 'select-one')
        {
            value = $("#"+id_html).val();
            value_id = value;
            txt = $('#'+id_html+' option:selected').html();
        }
        else if(tipo_campo == 'text')
        {
            value = $("#"+id_html).val();
            txt = '';
        }

        cont_lista = 'listaconsulta_'+sufijo;

        /**
            Por defecto siempre se crea un nuevo campo con los acompañantes AND y =
        */
        op_log = 'AND';
        op_mat = '=';

        // A partir del segundo agragado se pone 'OR' al operador lógico
        divs = $("#"+cont_lista+' > div').length;
        /**
            Si ya hay un elemento creado en la lista entonces los siguientes se deben crear con el operador lógico OR
        */
        if(divs >= 1)
        {
            op_log = 'OR';
            op_mat = '=';
        }

        if(value != '')
        {
            html = "";

            if(value_id == '')
            {
                divs = $("#"+cont_lista).find('input:text').length;

                /**
                    Se cuentan cuantos elementos hay en la lista de filtros adicionados para un mismo campo y se recupera el último id mayor de la lista para
                    generar un id nuevo mayor a ese.
                */
                if(divs > 0)
                {
                    id_mayor = 0;
                    $("#"+cont_lista).find('input:text').each(function() {
                        id_ = $(this).attr('id');

                        id_splt = id_.split('_');
                        id_this = (id_splt[0])*1;

                        if(id_this > id_mayor)
                        {
                            id_mayor = id_this;
                        }
                    });

                    id_mayor++;
                    value_id = id_mayor;
                }
                else
                {
                    value_id = 1;
                }
            }

            id_div = 'div_'+value_id+'_'+sufijo;
            id_input = value_id+'_'+sufijo;

            divs = $("#"+cont_lista).find('[id='+id_div+']').length;

            /**
                Según el tipo de campo a pintar entonces se imprime el código para generar un checkbox o un text.
            */
            switch(tipo_add)
            {
                case 'checkbox':
                        html = '<div style="display:block; margin-left:3px;border-left: 3px solid lightgreen;" id="'+id_div+'">'
                                +'<table cellspacing="0" cellpadding="1px" border="0"><tr>'
                                +'<td><div id="div_logic_'+id_input+'" class="campoOperador" onclick="posicionElemento(this,\'logic_'+id_input+'\',\'div_logic_'+id_input+'\',\'logico\');">'+op_log+'</div><input class="" type="hidden" id="logic_'+id_input+'" name="logic_'+id_input+'" value="'+op_log+'" /></td>'
                                +'<td><div id="div_mat_'+id_input+'" class="campoOperador" onclick="posicionElemento(this,\'mat_'+id_input+'\',\'div_mat_'+id_input+'\',\'matematico\');">'+op_mat+'</div><input class="" type="hidden" id="mat_'+id_input+'" name="mat_'+id_input+'" value="'+op_mat+'" /></td>'
                                +'<td><input in="'+param_in+'" rel="'+param_rel+'" type="checkbox" id="'+id_input+'" checked="checked" onclick="removerElemento(this,\''+id_div+'\');" value="'+value+'" />&nbsp;'+txt
                                +'&nbsp;<img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Eliminar campo" onclick="removerElemento(this,\''+id_div+'\');" ></td>'
                                +'</tr></table></div>';
                        break;
                case 'text':
                        html = '<div style="display:block; margin-left:3px;border-left: 3px solid lightgreen;" id="'+id_div+'">'
                                +'<table cellspacing="0" cellpadding="1px" border="0"><tr>'
                                +'<td><div id="div_logic_'+id_input+'" class="campoOperador" onclick="posicionElemento(this,\'logic_'+id_input+'\',\'div_logic_'+id_input+'\',\'logico\');">'+op_log+'</div><input class="" type="hidden" id="logic_'+id_input+'" name="logic_'+id_input+'" value="'+op_log+'" /></td>'
                                +'<td><div id="div_mat_'+id_input+'" class="campoOperador" onclick="posicionElemento(this,\'mat_'+id_input+'\',\'div_mat_'+id_input+'\',\'matematico\');">'+op_mat+'</div><input class="" type="hidden" id="mat_'+id_input+'" name="mat_'+id_input+'" value="'+op_mat+'" /></td>'
                                +'<td><input in="'+param_in+'" rel="'+param_rel+'" type="text" id="'+id_input+'" value="'+value+'" onblur="armarQuery();" onchange="armarQuery();" />'
                                +'<img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Eliminar campo" onclick="removerElemento(this,\''+id_div+'\');" ></td>'
                                +'</tr></table></div>';
                        $("#"+id_html).val('');
                        //divs = 0; // cuando es texto siempre se adicionará uno nuevo
                        break;
            }

            if(divs <= 0)
            {
                $('#'+cont_lista).append(html);
            }
        }

        if(reset_select != '')
        {
            if(tipo_campo == 'select-one')
            {
                $("#"+id_html+" option[value='']").attr("selected",true);
            }
        }
        armarQuery();
    }

    /**
        Lo que hace esta función es eliminar un elemento, ya sea una fila completa o un filtro de una lista de filtros de un campo.
        Si se elimina un filtro de una lista se garantiza que el primer elemento que quede en la lista debe quedar con el operador lógico AND. Así es pues que,
        si se eliminó el primer elemento de la lista y el segundo elemento tenía como operador OR entonces este segundo pasa a ser ahora el primer elemento
        pero ahora con el operador AND.
    */
    function removerElemento(ele,referencia)
    {
        $('#caja_flotante').hide(500);
        if(referencia != '')
        {
            // Cada vez que se elimine una opción (un filtro) se verifica que si se está borrando el primer elemento de la lista
            // entonces debe modificar el operador lógico de la fila siguiente por 'AND'
            var ant_id_anWr = $('[id^='+referencia+']').prev().find('input:hidden').eq(0).length; //busca si hay un div inmediatamente anterior que contenga inputs tipo hidden
            if(ant_id_anWr == 0) // si no hay elementos antes significa que se esta borrando el primero, entonces al siguiente cambiar a 'AND' el operador lódigo
            {
                $('[id^='+referencia+']').next().find('input:hidden').eq(0).val('AND');
                $('[id^='+referencia+']').next().find('input:hidden').eq(0).prev().html('AND');
            }

            $('[id^='+referencia+']').remove();
        }
        else
        {
            //$('#'+ele.id).remove();
        }

        armarQuery();
    }

    /**
        Esta función lo que hace es adicionar un nuevo elemento a la lista de filtros para un mismo campo al momento de presionar la tecla enter.
    */
    function enterAdd(e,ele,referencia,sufijo,tipo_add,reset_select)
    {
        var tecla = (document.all) ? e.keyCode : e.which;
        //var charCode = (evt.which) ? evt.which : event.keyCode // Si en algunos navegadores no funciona la captura del enter con la linea anterior, probar entonces con esta que está comentada
        if(tecla==13)
        {
            $("#"+referencia).focus();
            addfiltro(ele,referencia,sufijo,tipo_add,reset_select);
            return false;
        }
    }

    /**
        Esta función no esta siendo usada pero se deja por si en algún momento se requiere adicionar una nueva fila al dar enter en alguno de los campos de la fila anterior.
    */
    function enterAddFila(e,ele,referencia,sufijo,tipo_add)
    {
        tecla = (document.all) ? e.keyCode : e.which;
        if(tecla==13)
        {
            $("#"+referencia).focus();
            addfiltro(ele,referencia,sufijo,tipo_add);
        }
    }

    /**
        Se encarga de verificar que tipo de campo es el que se esta evaluando.
        id: es el id del elemento html para el cual se quiere establecer el tipo de campo que es
     */
    function tipoCampo(id)
    {
        var tipo = $('#'+id).get(0).type;
        if (tipo == 'radio')        { val = 'radio'; }
        else if(tipo == 'checkbox') { val = 'checkbox'; }
        else if(tipo == 'select-one'){ val = 'select-one'; }
        else if(tipo == 'text')     { val = 'text'; }
        else if(tipo == 'hidden')   { val = 'text'; }
        else                        { val = 'otro'; }

        return val;
    }

    /**
        Esta función se encarga de ubicar el menú flotante de opciones de operadores justo encima del operador sobre el que se dió clic. Esta función
        también se encarga de resaltar al operador sobre el cual se ha dado clic.

        ele             : No esta sienedo usado en esta función.
        campo_operacion : Es el id del div del operador que acompaña a un campo o filtro.
        contenedor      : Es el id del div que contiene los operador logico y de igualdad de cada campo.
        tipo_operador   : Indica si es un operador lógico u operador de igualdad, esto con el fin de poder mostrar las opciones adecuadas para tipo de operador,
                            es decir, si se dió clic en un operador lódigo entonces se desplegarán opciones de operadores lógicos pero si se dió clic en un
                            operador de igual entonces solo debe mostrar operadores de igualdad (>, <, 0, !=)
     */
    function posicionElemento(ele,campo_operacion,contenedor,tipo_operador)
    {
        if(tipo_operador == 'logico')
        {
            deactivarOperadoresFlotantes();
            $('#op_flotante_and').show();
            $('#op_flotante_or').show();
        }
        else
        { // de igualdad
            activarOperadoresFlotantes();
            $('#op_flotante_and').hide();
            $('#op_flotante_or').hide();
        }
        $('#id_operacion').val(campo_operacion);
        $('#cont_operador').val(contenedor);
        var elemento = $("#"+ele.id);
        var posicion = elemento.offset();

        quitarResaltarOperadores();
        $('#'+contenedor).css({'border':'2px solid orange'});

        $('#caja_flotante').css({'left':posicion.left,'top':posicion.top-29});
        $('#caja_flotante').show(500);
    }

    /**
        Recibe como parámetros un operador y el nombre del operador, con base a esto entonces actualiza el operador que estaba acompañando a un campo, si antes
        un operador para un campo estaba AND y al deplegar las opciones de operador lógico se seleccionó OR entonces esta función se encarga de actializarlo a OR,
        esta actualización se debe hacer tanto en la interfaz (ver que se seleccionó) así como también en un campo hidden que acompaña al operador lógico de cada campo.
        Los operadores de igualdad también están acompañados de un campo hidden que es el que guarda el valor de operador de igualdad seleccionado.
     */
    function asignarOperador(op,nombre_operador)
    {
        quitarResaltarOperadores();
        if(op!='')
        {
            campo_operador = $('#id_operacion').val();
            contenedor = $('#cont_operador').val();

            $('#'+campo_operador).val(op);
            $('#div_'+campo_operador).html(nombre_operador);
        }
        $('#caja_flotante').hide(500);
        armarQuery();
    }

    /**
        Esta función se encarga de quitar todos los estilos que se usan para resaltar los operadore cuando se ha dado clic en alguno de ellos.
    */
    function quitarResaltarOperadores()
    {
        $('[id^=div_logic_]').css({'border':'2px solid gray'});
        $('[id^=div_mat_]').css({'border':'2px solid gray'});
    }

    /**
        Muestra o activa el div flotante con los operadores que se pueden seleccionar.
    */
    function activarOperadoresFlotantes()
    {
        $('#op_flotante_and').show();
        $('#op_flotante_or').show();
        $('#op_flotante_menorigual').show();
        $('#op_flotante_mayorigual').show();
        $('#op_flotante_diferente').show();
        $('#op_flotante_igual').show();
    }

    /**
        Oculta o inactiva el div flotante con los operadores que se pueden seleccionar.
    */
    function deactivarOperadoresFlotantes()
    {
        $('#op_flotante_and').hide();
        $('#op_flotante_or').hide();
        $('#op_flotante_menorigual').hide();
        $('#op_flotante_mayorigual').hide();
        $('#op_flotante_diferente').hide();
        $('#op_flotante_igual').hide();
    }

    /**
        Recibe por parámetros un id de un elemento html y según el tipo de elemento lee el valor que contiene ese elemento.
    */
    function getValorCampo(id_campo)
    {
        // tipo_campo = tipoCampo(id_campo);
        var tipo_campo = $('#'+id_campo).get(0).type;

        var value = '';
        if (tipo_campo == 'radio')
        {
            value = $('[id="'+id_campo+'"]:checked').val();
        }
        else if(tipo_campo == 'checkbox')
        {
            value = $('[id="'+id_campo+'"]:checked').val();
        }
        else if(tipo_campo == 'select-one')
        {
            value = $("#"+id_campo).val();
        }
        else if(tipo_campo == 'text')
        {
            value = $("#"+id_campo).val();
        }
        else
        {
            value = $("#"+id_campo).val();
        }
        return value;
    }

    /**
        Esta es una de las funciones principales del resporte, esta es la función que se encarga de recorrer todos los campos del reportes con el fin de concatenarlos
        en las variables respectivas, ya sean campos unicos, o tipo listas o tipos filas. Esta función tambien se encarga de concatenar las columnas que han sido chequeadas para
        luego verlas en el resultado del reporte.

        En una variable de javascript concatena cada campo,

        campos_unicos = '';
        campos_listas = '';
        campos_filas = '';
        columnas_filtros = '';

        Luego de recorrer todos los campos y guardar concatenada esta información según la estructura de concatenación que se describió al principio de este script,
        se procede a guardar estos datos en unos campos tipo hidden y de esta forma dejarlos disponibles por ejemplo para luego ser enviados mediante una petición ajax
        para que esa información sea procesada y dé origen al reporte.
    */
    function armarQuery()
    {
        total_unicos = $('.unicos').length;
        total_listas = $('div').find('[id^=listaconsulta_] > div').length;
        total_tr = $('tr[id*=_tr_]').length;
        $('#total_contador').html('Uni: '+total_unicos+' | Listas: '+total_listas+' | Trs: '+total_tr);
        lectura = '';

        lectura = lectura+'&Uacute;nicos';
        var campos_unicos = '';
        var campos_listas = '';
        var campos_filas = '';

        var separador_bloque = '';
        // Recorrer todos los tipo unicos
        $('.unicos').each(function() {
            //id = $(this).attr('id');

            campo_log = $(this).find('input:hidden[id^=logic_]');
            campo_mat = $(this).find('input:hidden[id^=mat_]');
            campo_txt = $(this).find('select,input:text');//:selected,input:text

            idcmp_log = campo_log.attr('id');
            idcmp_mat = campo_mat.attr('id');
            idcmp_txt = campo_txt.attr('id');

            //es suficiente con validar solo valor_txt puesto que siempre debe estar acompañado de un operador y una operación
            if(idcmp_txt) // puede ser que lea una columna sin nada como por ejemplo la de adicionar columnas, si hay valor sigue buscando dentro
            {
                valor_log = getValorCampo(idcmp_log);
                valor_mat = getValorCampo(idcmp_mat);
                valor_txt = getValorCampo(idcmp_txt);

                in_val = $("#"+idcmp_txt).attr('in');
                rel_val= $("#"+idcmp_txt).attr('rel');
                if(valor_txt != '')
                {
                    lectura = lectura+"<br />&nbsp;&nbsp;&nbsp;&nbsp;"+rel_val+" "+valor_log+" "+in_val+" "+valor_mat+" '"+valor_txt+"'";
                    campos_unicos = campos_unicos+separador_bloque+rel_val+"|"+valor_log+"|"+in_val+"|"+valor_mat+"|"+valor_txt;
                    or_ = '';
                    separador_bloque = '||';
                    ro_ok = true;
                }
            }
        });

        lectura = lectura+'<br />';

        lectura = lectura+'Listas';
        separador_bloque = '';
        // Recorrer todos los tipo listas
        $('div').find('[id^=listaconsulta_] > div').each(function() {
            id = $(this).attr('id');

            campo_log = $('#'+id).find('input:hidden[id^=logic_]');
            campo_mat = $('#'+id).find('input:hidden[id^=mat_]');
            campo_txt = $('#'+id).find('input:checkbox,input:text');//:selected,input:text

            idcmp_log = campo_log.attr('id');
            idcmp_mat = campo_mat.attr('id');
            idcmp_txt = campo_txt.attr('id');

            // es suficiente con validar solo valor_txt puesto que siempre debe estar acompañado de un operador y una operación
            if(idcmp_txt) // puede ser que lea una columna sin nada como por ejemplo la de adicionar columnas, si hay valor sigue buscando dentro
            {
                valor_log = getValorCampo(idcmp_log);
                valor_mat = getValorCampo(idcmp_mat);
                valor_txt = getValorCampo(idcmp_txt);

                in_val = $("#"+idcmp_txt).attr('in');
                rel_val= $("#"+idcmp_txt).attr('rel');
                if(valor_txt != '')
                {
                    lectura = lectura+"<br />&nbsp;&nbsp;&nbsp;&nbsp;"+rel_val+" "+valor_log+" "+in_val+" "+valor_mat+" '"+valor_txt+"'";
                    campos_listas = campos_listas+separador_bloque+rel_val+"|"+valor_log+"|"+in_val+"|"+valor_mat+"|"+valor_txt;
                    separador_bloque = '||';
                    or_ = '';
                    ro_ok = true;
                }
            }
        });

        lectura = lectura+'<br />';

        lectura = lectura+'Filas';//+' <br />OR (';
        var separador_filas = '';
        separador_bloque = '';

        contf = 0;
        // in_val = '';
        // rel_val = '';
        var existe = 0;
        var ro_ok = 0;
        var fila_tmp = '';
        // Recorrer todos los tipo fila
        $('tr[id*=_tr_]').each(function() {
            id = $(this).attr('id');

            or_ = '<br /> TR (';
            ro_ok = 0;
            fila_tmp = '';

            $('#'+id).find('> td').each(function() {
                existe = $(this).find('select,input:text').length;

                if(existe > 0)
                {
                    campo_log = $(this).find('input:hidden[id^=logic_]');
                    campo_mat = $(this).find('input:hidden[id^=mat_]');
                    campo_txt = $(this).find('select,input:text');//:selected,input:text

                    idcmp_log = campo_log.attr('id');
                    idcmp_mat = campo_mat.attr('id');
                    idcmp_txt = campo_txt.attr('id');

                    var valor_log = '';
                    var valor_mat = '';
                    var valor_txt = '';

                    // es suficiente con validar solo valor_txt puesto que siempre debe estar acompañado de un operador y una operación
                    if(idcmp_txt) // puede ser que lea una columna sin nada como por ejemplo la de adicionar columnas, si hay valor sigue buscando dentro
                    {
                        valor_log = getValorCampo(idcmp_log);
                        valor_mat = getValorCampo(idcmp_mat);
                        valor_txt = getValorCampo(idcmp_txt);

                        var in_val = $("#"+idcmp_txt).attr('in');
                        var rel_val= $("#"+idcmp_txt).attr('rel');
                        if(valor_txt != '')
                        {
                            if(campos_filas != '') { separador_filas = '[TR#]'; }

                            lectura = lectura+or_+"<br />&nbsp;&nbsp;&nbsp;&nbsp;"+rel_val+" "+valor_log+" "+in_val+" "+valor_mat+" '"+valor_txt+"'";
                            fila_tmp = fila_tmp+separador_bloque+rel_val+"|"+valor_log+"|"+in_val+"|"+valor_mat+"|"+valor_txt;
                            separador_bloque = '||';
                            or_ = '';
                            ro_ok = 1;
                            if(contf > 1)
                            {
                                separador_filas = '[TR#]';
                            }
                            contf++;
                        }
                    }
                }
            });
            campos_filas = campos_filas+separador_filas+fila_tmp;
            separador_filas = '';
            separador_bloque = '';

            if(ro_ok == 1)
            { lectura = lectura+'<br />)'; }
        });

        var columnas_filtros = '';
        separador_bloque = '';
        $('#ver_mensajes').find(':checkbox[checked]').each(function() {
                tb = $(this).attr('rel');
                cp = $(this).val();
                columnas_filtros = columnas_filtros+separador_bloque+tb+'|'+cp;
                separador_bloque = '||';
        });

        // $('#cod_query').html(campos_filas+'<br><br>'+lectura);
        // $('#cod_query').html(lectura);

        $('#campos_unicos').val(campos_unicos);
        $('#campos_listas').val(campos_listas);
        $('#campos_filas').val(campos_filas);
        $('#columnas_filtros').val(columnas_filtros);
    }

    /**
        Esta función se ejecuta al momento de presionar la opción "Ejecutar reporte", adecúa la información que se enviará mediante una petición ajax
        y luego de ser procesada entonces procede a mostrar el resultado de la consulta en el div de respuesta.
     */
    function ejecutarReporte()
    {
        $("#loading_img").css({'display':'inline'});
        armarQuery();

        var campos_unicos = $('#campos_unicos').val();
        var campos_listas = $('#campos_listas').val();
        var campos_filas = $('#campos_filas').val();
        var columnas_filtros = $('#columnas_filtros').val();

        // consultar = $('#exec_query').val();
        // if(consultar != '')
        // {

            $.post("../reportes/reporteador_caracterizacion.php",
                {
                    wemp_pmla:      $('#wemp_pmla').val(),
                    wtema:          $('#wtema').val(),
                    wuse:           $('#wuse').val(),
                    consultaAjax:   '',
                    accion:         'ejecutar_query',
                    form:           '',
                    campos_unicos:  campos_unicos,
                    campos_listas:  campos_listas,
                    campos_filas:   campos_filas,
                    columnas_filtros:columnas_filtros
                }
                ,function(data) {
                    $("#loading_img").css({'display':'none'});
                    if(data.error == 1)
                    {
                        $('#seccion_reporte').html(data.html);
                        alert(data.mensaje);
                        // $('#seccion_reporte').html(data.sql);
                        $('.displIdeGen').show(500);
                        $('#div_exportar').hide();
                        $('#ejecucion_ver').html(data.sql);
                        // $('#'+rep_conten).html(data.html);
                    }
                    else
                    {
                        // alert(data.mensaje);
                        // $('#'+rep_conten).html(data.sql);
                        $('#ejecucion_ver').html(data.sql);
                        $('#seccion_reporte').html(data.html);
                        $('#div_exportar').show(500);
                    }
                },
                "json"
            );
        // }
        // $('#exec_query').val('');
    }

    /**
        Esta función se encarga de controlar las columnas que se seleccionan para ser vistas en el resultado del reporte.
        El parámetro rdy posteriormente indicará si es la primera vez que se llama a esta función lo cual indica que apenas se abrió el reporte desde el menú
        lo que implica cargar alguna información por defecto
    */
    function seleccionarColumnas(rdy)
    {
        //listar_columnas
        // $('#exec_query').val('listar_columnas');
        armarQuery();

        var campos_unicos = $('#campos_unicos').val();
        var campos_listas = $('#campos_listas').val();
        var campos_filas = $('#campos_filas').val();
        var columnas_filtros = $('#columnas_filtros').val();
        // consultar = $('#exec_query').val();
        // if(consultar != '')
        // {

            $.post("../reportes/reporteador_caracterizacion.php",
            {
                wemp_pmla:      $('#wemp_pmla').val(),
                wtema:          $('#wtema').val(),
                wuse:           $('#wuse').val(),
                consultaAjax:   '',
                accion:         'listar_columnas',
                form:           '',
                campos_unicos:  campos_unicos,
                campos_listas:  campos_listas,
                campos_filas:   campos_filas,
                columnas_filtros:columnas_filtros,
                primera_vez     :rdy
            }
            ,function(data) {
                if(data.error == 1)
                {
                    alert(data.mensaje);
                    // $('#'+rep_conten).html(data.sql);
                    // $('#cod_query').html(data.sql);
                }
                else
                {
                    // alert(data.mensaje);
                    // $('#seccion_reporte').html(data.sql);
                    $('#cod_query').html(data.html);
                }
            },"json");
        // }
        // $('#exec_query').val('');
    }

    /**
     * NO ESTA EN USO EN ESTE MOMENTO
     */
    function setConsultar()
    {
        $('#exec_query').val('ejecutar_query');
    }

    /**
        Minimiza o reduce el espacio ocupado por el div flotante
    */
    function minFlotante(id_min)
    {
        $('#'+id_min).hide(500);
    }

    /**
        Aumenta el espacio ocupado por el div flotante
    */
    function maxFlotante(id_min)
    {
        $('#'+id_min).show(500);
    }

    function cerrarSecciones()
    {
        $('.displCaracterizacion,.displIdeGen').hide();
    }

    function onOver(ele)
    {
        $("#"+ele).css({'font-weight': 'bold','background-color':'#999999','color':'#ffffff'});
    }

    function onOut(ele)
    {
        $("#"+ele).css({'font-weight': '','background-color':'','color':''});
    }

    </script>
    <style type="text/css">
    .displCaracterizacion{
        display:none;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
        text-align:left;
    }
    .borderConsultar {
        border: 2px dashed gray;
        background:#CCCCCC;
        margin-top:3px;
        margin-bottom:4px;
    }
    .campoOperador {
        border: 2px solid gray;
        background:#green;
        width:60px;
        font-family:Verdana, Arial, Helvetica, sans-serif;
        font-size:9pt;
        text-align:center;
        font-weight:bold;
        cursor:pointer;
    }

    #caja_flotante{
        position: absolute;
        /*top:0;*/
        /*left: 10px;*/
        border: 1px solid #CCC;
        background-color: #F2F2F2;
        /*width:150px;*/
    }

    #caja_flotante_query{
        /*position: fixed;
        bottom: 0;
        left: 55%;*/
        position: absolute;
        top:0;
        /*left: 10px;*/
        border: 2px solid #999999;
        background-color: #f2f2f2;
        color: black;
        font-weight:bold;
        /*height:200px;*/
        /*overflow:scroll;*/
        width:624px;
        /*margin-left: 10px;*/
    }

    .opcionOperaciones {
        cursor:pointer;
        border: 1px solid #999999;
        background-color:#D2E8FF;
     }
    .anchoInput {
        width:40px;
     }

    .tit_seccion{
        text-align:justify;
    }

    .img_mas1{
        width: 9px;
        height:9px;
        cursor:pointer;
    }

    .img_del1{
        width: 9px;
        height:9px;
        cursor:pointer;
    }
    .campos_ver{
        font-size:8pt;
    }
    </style>
</head>
<body>
<!--<form action="reporteador_caracterizacion.php?wemp_pmla=01&wtema=01&wuse=03636&wcodtab=11" id="form_reporteador" method="post">-->
<div id="caja_flotante" style="display:none;">
        <div id="cont_caja_flotante" style="border:solid 1px orange;">
            <input type="hidden" id="id_operacion" name="id_operacion" value="">
            <input type="hidden" id="cont_operador" name="cont_operador" value="">
            <table>
                <tr>
                    <td id="op_flotante_and"         style="display:none;" class="opcionOperaciones" onClick="asignarOperador('AND','AND');"><span>AND</span></td>
                    <td id="op_flotante_or"          style="display:none;" class="opcionOperaciones" onClick="asignarOperador('OR','OR');">OR</td>
                    <td id="op_flotante_menorigual"  style="display:none;" class="opcionOperaciones" onClick="asignarOperador('<=','Menor =');">Menor =</td>
                    <td id="op_flotante_mayorigual"  style="display:none;" class="opcionOperaciones" onClick="asignarOperador('>=','Mayor =');">Mayor =</td>
                    <td id="op_flotante_diferente"   style="display:none;" class="opcionOperaciones" onClick="asignarOperador('!=','Diferente');">Diferente</td>
                    <td id="op_flotante_igual"       style="display:none;" class="opcionOperaciones" onClick="asignarOperador('=','=');">=</td>
                    <!--    <td class="opcionOperaciones" onClick="asignarOperador('x*','x*');">x*</td>
                                                    <td class="opcionOperaciones" onClick="asignarOperador('*x','*x');">*x</td>-->
                    <td class="opcionOperaciones" onClick="asignarOperador('','');"><img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Cerrar Opciones" ></td>
                </tr>
            </table>
        </div>
 </div>
 <div id="caja_flotante_query" style="" align="right">
        <div id="cont_caja_flotante_query" style="padding: 10px;text-align:left;">
            <input type="hidden" id="exec_query" name="exec_query" value="">
            <div style="font-size:9pt;color:black;" align="left">
                <table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
                    <tr>
                        <td>
                            <div style="display:inline;cursor:pointer;" align="left" >Opciones reporte caracterizaci&oacute;n</div>
                            <div id="" style="text-align:left;font-size:8pt;color:#666666;display:inline;" >
                                <!--<span style="border-bottom: 1px solid;cursor:pointer;" onClick="armarQuery('cod_query');">[Ver/Actualizar Selecci&oacute;n]</span>-->
                                <img width="10" height="10" border="0" src="../../images/medical/root/grabar.png">
                                <span id="gen_reporte" style="border-bottom: 1px solid;cursor:pointer;" onMouseOver="onOver('gen_reporte');" onMouseOut="onOut('gen_reporte');" onClick="cerrarSecciones();ejecutarReporte();minFlotante('ver_mensajes');">[Generar reporte]</span>
                                <div id="loading_img" style="display:none;font-size:7pt;"><img width="90" height="11" border="0" src="../../images/medical/ajax-loader.gif"> Consultando..</div>
                            </div>
                        </td>
                        <td>
                            <span style="cursor:pointer;background-color:gold;" title="Minimizar" onClick="minFlotante('ver_mensajes');">__</span>
                            <span style="cursor:pointer;background-color:gold;" title="Maximizar" onClick="maxFlotante('ver_mensajes');seleccionarColumnas('');">&equiv;&equiv;</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="ver_mensajes" style="text-align:left;font-size:8pt;display:none;">
                <!--Visor elementos: --><span id='total_contador' style="display:none"></span>
                <br />
                <table cellspacing="0" cellpadding="0" border="0" style="width:100%;">
                    <tr>
                        <td>Seleccione las columnas que desea ver en el reporte:</td>
                        <td align="right"><span id="sel_columnas" style="border-bottom: 1px solid;cursor:pointer;font-size:7pt;" onMouseOver="onOver('sel_columnas');" onMouseOut="onOut('sel_columnas');" onClick="seleccionarColumnas('');">[Actualizar]</span></td>
                    </tr>
                </table>
                <br />
                <div id="cod_query" style="text-align:left;font-size:7pt;height:200px;overflow:scroll;">&nbsp;</div>
                <div id="ejecucion_ver"></div>
            </div>
        </div>
 </div>
<div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?php echo $wactualiz; ?></div>
<input type="hidden" id="campos_unicos" name="campos_unicos" value="">
<input type="hidden" id="campos_listas" name="campos_listas" value="">
<input type="hidden" id="campos_filas" name="campos_filas" value="">
<input type="hidden" id="columnas_filtros" name="columnas_filtros" value="">
<input type="hidden" id="columnas_ver" name="columnas_ver" value="">
<table>
    <tr>
        <td>
            <div align="center" style="" id="div_contenedor_caracterizacion">
                <table cellspacing="3" cellpadding="3" border="0">
                    <tr>
                        <td align="center">
                            <div align="left" id="ref_tbidgen">
                                <table style="width:100%;" cellspacing="0" cellpadding="0" border="0">
                                    <tr class="encabezadoTabla">
                                        <td>
                                            <div align="center" style="font-size:15pt">REPORTEADOR</div>
                                        </td>
                                    </tr>
                                </table>
                                <table width="900" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="tit_seccion">
                                            <a onClick="javascript:verSeccionCaracterizacion('div_tbidgen');" href="#null">
                                                <img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;IDENTIFICACI&Oacute;N GENERAL
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div align="center" class="borderDiv displIdeGen" id="div_tbidgen">
                                <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr class="fila2">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Primer nombre:</td>
                                        <td valign="top">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wnombre1')?>
                                                        <td>
                                                        &nbsp;<input type="text" value="" in="Ideno1" rel="000013-Ideuse" id="1_wnombre1" name="1_wnombre1">                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>                                        </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Segundo nombre:</td>
                                        <td valign="top">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wnombre2')?>
                                                        <td>
                                                        &nbsp;<input type="text" value="" in="Ideno2" rel="000013-Ideuse" id="1_wnombre2" name="1_wnombre2" >                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="fila1">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Primer apellido:</td>
                                        <td valign="top">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wapellido1')?>
                                                        <td>
                                                        &nbsp;<input type="text" value="" in="Ideap1" rel="000013-Ideuse" id="1_wapellido1" name="1_wapellido1" >                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Seundo apellido:</td>
                                        <td valign="top">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wapellido2')?>
                                                        <td>
                                                        &nbsp;<input type="text" value="" in="Ideap2" rel="000013-Ideuse" id="1_wapellido2" name="1_wapellido2" >                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>                                        </td>
                                    </tr>
                                    <tr class="fila2">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Fecha de nacimiento:</td>
                                        <td class="carBold" valign="top">
                                            &nbsp;<input type="text" value="" in="Idefnc" rel="000013-Ideuse" id="wuserfnace" name="wuserfnace" onKeyPress="return enterAdd(event,this,'wuserfnace','wuserfnace','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wuserfnace','wuserfnace','text','');">
                                            <div id="listaconsulta_wuserfnace" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Genero:</td>
                                        <td class="carBold" valign="top">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wgenero_emp')?>
                                                        <td>
                                                        &nbsp;<select id="1_wgenero_emp" name="1_wgenero_emp" in="Idegen" rel="000013-Ideuse" >
                                                                <option value="" selected="selected">Seleccione..</option>
                                                                <?php
                                                                    foreach($identGeneral['genero'] as $key => $value)
                                                                    {
                                                                        echo "<option value='$key' >".utf8_encode($value)."</option>";
                                                                    }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="fila1">
                                        <td width="145" style="border-bottom:1px solid;" class="encabezadoTabla resalto">N&uacute;mero de c&eacute;dula:</td>
                                        <td class="carBold" valign="top">
                                            &nbsp;<input type="text" value="" in="Ideced" rel="000013-Ideuse" id="wuserced" name="wuserced" onKeyPress="return enterAdd(event,this,'wuserced','wuserced','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wuserced','wuserced','text','');">
                                            <div id="listaconsulta_wuserced" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">C&oacute;digo de nomina:</td>
                                        <td class="carBold" valign="top">
                                            &nbsp;<input type="text" value="" in="Ideuse" rel="000013-Ideuse" id="wcoduser" name="wcoduser" onKeyPress="return enterAdd(event,this,'wcoduser','wcoduser','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wcoduser','wcoduser','text','');">
                                            <div id="listaconsulta_wcoduser" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                    </tr>
                                    <tr class="fila2">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Tiene pasaporte:</td>
                                        <td class="carBold">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wtienepas')?>
                                                        <td>
                                                        &nbsp;<select id="1_wtienepas" name="1_wtienepas" in="Idepas" rel="000013-Ideuse" >
                                                                <option value="" selected="selected">Seleccione..</option>
                                                                <option value="on">Si</option>
                                                                <option value="off">No</option>
                                                            </select>                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>                                        </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Tiene visa:</td>
                                        <td align="left" class="carBold">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wtienevisa')?>
                                                        <td>
                                                        &nbsp;<select id="1_wtienevisa" name="1_wtienevisa" in="Idevis" rel="000013-Ideuse" >
                                                                <option value="" selected="selected">Seleccione..</option>
                                                                <option value="on">Si</option>
                                                                <option value="off">No</option>
                                                            </select>                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>                                        </td>
                                    </tr>
                                    <tr class="fila1">
                                        <td style="border-bottom:1px #FFFFFF solid;" class="encabezadoTabla">Estado civil:</td>
                                        <td class="resalto" valign="top">
                                        &nbsp;<select in="Ideesc" rel="000013-Ideuse" id="westadocivil" name="westadocivil" onChange="addfiltro(this,'','westadocivil','checkbox','reset');">
                                                <option value="">Seleccione..</option>
                                                    <?php
                                                        foreach($identGeneral['estado_civil'] as $key => $est_c)
                                                        {
                                                            $ckd = ($identGeneral['identificacion']['estado_civil']==$key) ? 'selected="selected"' : '';
                                                            echo "<option value='$key' ".$ckd." >$est_c</option>";
                                                        }
                                                    ?>
                                            </select>
                                            <div id="listaconsulta_westadocivil" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                        <td valign="top" style="border-bottom:1px #FFFFFF solid;" class="encabezadoTabla resalto">Pa&iacute;ses con visa:</td>
                                        <td valign="top">
                                            <div style="width:259px">
                                                <div style="display:block;" id="div_list_pises">
                                                    &nbsp;
                                                    <select onChange="addfiltro(this,'','wpaisvisa','checkbox','reset');" style="width:195px;" in="Idepvi-*" rel="000013-Ideuse" id="wpaisvisa" name="wpaisvisa" >
                                                        <option value="" >Seleccione..</option>
                                                        <?php
                                                            foreach($identGeneral['paises'] as $key => $value)
                                                            {
                                                                echo "<option value='$key' >$value</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div id="listaconsulta_wpaisvisa" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                    </tr>
                                    <tr class="fila2">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Direcci&oacute;n de vivienda:</td>
                                        <td valign="top">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wdirvive')?>
                                                        <td>
                                                        &nbsp;<input type="text" value="" in="Idedir" rel="000013-Ideuse" id="1_wdirvive" name="1_wdirvive" >                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>                                        </td>
                                    </tr>
                                    <tr class="fila1">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Lugar de nacimiento:</td>
                                        <td valign="top">
                                            &nbsp;<input type="text" value="" in="Ideinc" rel="000013-Ideuse" id="wlugarnac" name="wlugarnac" onKeyPress="return enterAdd(event,this,'wlugarnac','wlugarnac','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wlugarnac','wlugarnac','text','');">
                                            <div id="listaconsulta_wlugarnac" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Estrato:</td>
                                        <td valign="top">
                                            &nbsp;<input type="text" value="" in="Idestt" rel="000013-Ideuse" id="westrato" name="westrato" onKeyPress="return enterAdd(event,this,'westrato','westrato','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'westrato','westrato','text','');">
                                            <div id="listaconsulta_westrato" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                    </tr>
                                    <tr class="fila2">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Municipio de residencia:</td>
                                        <td valign="top">
                                            &nbsp;<select onChange="addfiltro(this,'','wmuni','checkbox',''); recargarDependiente(this,'wbarrio','load','load_barrio','wvacio');" in="Idempo" rel="000013-Ideuse" id="wmuni" name="wmuni" >
                                                <option value=''>Seleccione..</option>
                                                <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['municipios'] as $key => $value)
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' $ckd >".ucwords(strtolower($value))."</option>";
                                                        $cont++;
                                                    }
                                                ?>
                                                </select>
                                                <div id="listaconsulta_wmuni" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Barrio:</td>
                                        <td valign="top">
                                            <input type="hidden" value="" in="Idebrr" rel="000013-Ideuse" id="wvacio" >
                                            &nbsp;<select onChange="addfiltro(this,'','wbarrio','checkbox','');" in="Idebrr" rel="000013-Ideuse" id="wbarrio" name="wbarrio" >
                                                    <option value='' selected="selected">Seleccione..</option>
                                                    <?php
                                                        foreach($identGeneral['barrios'] as $key => $value)
                                                        {
                                                            echo "<option value='$key' >".ucwords(strtolower($value))."</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div id="listaconsulta_wbarrio" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                    </tr>
                                    <tr class="fila1">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">N&uacute;mero telef&oacute;nico:</td>
                                        <td valign="top">
                                            &nbsp;<input type="text" value="" in="Idetel" rel="000013-Ideuse" id="wnumtel" name="wnumtel" onKeyPress="return enterAdd(event,this,'wnumtel','wnumtel','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wnumtel','wnumtel','text','');">
                                            <div id="listaconsulta_wnumtel" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Celular: </td>
                                        <td valign="top">
                                            &nbsp;<input type="text" value="" in="Idecel" rel="000013-Ideuse" id="wcel" name="wcel" onKeyPress="return enterAdd(event,this,'wcel','wcel','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wcel','wcel','text','');">
                                            <div id="listaconsulta_wcel" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                    </tr>
                                    <tr class="fila2">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Correo electr&oacute;nico:</td>
                                        <td valign="top">
                                            &nbsp;<input type="text" value="" in="Ideeml" rel="000013-Ideuse" id="wmail" name="wmail" onKeyPress="return enterAdd(event,this,'wmail','wmail','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wmail','wmail','text','');">
                                            <div id="listaconsulta_wmail" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Tipo de sangre: </td>
                                        <td valign="top">
                                            &nbsp;<input type="text" value="" in="Idesrh" rel="000013-Ideuse" id="wsangr" name="wsangr" onKeyPress="return enterAdd(event,this,'wsangr','wsangr','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wsangr','wsangr','text','');">
                                            <div id="listaconsulta_wsangr" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                    </tr>
                                    <tr class="fila1">
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla resalto">Extensi&oacute;n:</td>
                                        <td valign="top">
                                            &nbsp;<input type="text" value="" in="Ideext" rel="000013-Ideuse" id="wextensioncaract" name="wextensioncaract" onKeyPress="return enterAdd(event,this,'wextensioncaract','wextensioncaract','text','');">
                                            <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wextensioncaract','wextensioncaract','text','');">
                                            <div id="listaconsulta_wextensioncaract" class="borderConsultar"><?=$indicador?></div>                                        </td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <br>
                            <div align="left" id="ref_educacion">
                                <table width="900" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="tit_seccion">
                                            <a onClick="javascript:verSeccionCaracterizacion('div_educacion');" href="#null">
                                                <img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;EDUCACI&Oacute;N
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div align="center" class="borderDiv displCaracterizacion" id="div_educacion" >
                                <div class="backgrd_seccion" id="div_msjGradosEsc">
                                     <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">Nivel educativo (Si usted tiene una educaci&oacute;n superior, relacione todas las que tenga)
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_nivedu">
                                    <table width="800" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td colspan="1">
                                                <div id="div_infoIdiomas">
                                                    <table border="0" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_educativos" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">GRADO ESCOLAR</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">T&Iacute;TULO OBTENIDO</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">NOMBRE DE LA INSTITUCI&Oacute;N</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">FECHA</th>
                                                                        <th style="">&nbsp;</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_educativos" class="fila1">
                                                                        <td class="borderConsultar" align="center">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grado_edu')?>
                                                                                        <td>
                                                                                            <select id="1_grado_edu" name="1_grado_edu" in="Edugrd" rel="000014-Eduuse" >
                                                                                                <option value="">Seleccione..</option>
                                                                                                <?php
                                                                                                    //$cont = 0;
                                                                                                    foreach($identGeneral['grado_escolar'] as $key => $value)
                                                                                                    {
                                                                                                        if($value['req_deLey'] == 'off')
                                                                                                        {
                                                                                                            $ck = '';//($cont == 0) ? 'selected="selected"': '';
                                                                                                            echo "<option value='$key' $ck >".utf8_encode($value['nombre'])."</option>";
                                                                                                            //$cont++;
                                                                                                        }
                                                                                                    }
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_titulo_edu')?>
                                                                                        <td>
                                                                                            <input type="text" value="" in="Edutit" rel="000014-Eduuse" id="1_titulo_edu" name="1_titulo_edu" >
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_inst_edu')?>
                                                                                        <td>
                                                                                            <input type="text" value="" in="Eduins" rel="000014-Eduuse" id="1_inst_edu" name="1_inst_edu" >
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_anio_edu')?>
                                                                                        <td>
                                                                                            <input type="text" value="" in="Eduani" rel="000014-Eduuse" id="1_anio_edu" name="1_anio_edu" >
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_educativos_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_educativos','1_tr_tabla_educativos','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table><!-- TERMINA INGRESO DE NIVELES EDUCATIVOS-->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div class="backgrd_seccion" id="div_msjEscLey">
                                     <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">
                                                Requerimientos de ley (Si usted tiene estudios terminados de requerimientos de ley relaci&oacute;nelos aqu&iacute;)
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <br>
                                <div id="div_eduley">
                                    <table width="800" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td colspan="1">
                                                <div id="div_infoEstLey">
                                                    <table border="0" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_reqley" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">GRADO ESCOLAR</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">T&Iacute;TULO OBTENIDO</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">NOMBRE DE LA INSTITUCI&Oacute;N</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">FECHA</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_reqley" class="fila1">
                                                                        <td class="borderConsultar" align="center">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grado_edu_ley')?>
                                                                                        <td>
                                                                                            <select id="1_grado_edu_ley" name="1_grado_edu_ley" in="Leygrd" rel="000043-Leyuse" >
                                                                                                <option value="">Seleccione..</option>
                                                                                                <?php
                                                                                                    //$cont = 0;
                                                                                                    $grados_ley = $identGeneral['grado_escolar_ley'];
                                                                                                    foreach($identGeneral['grado_escolar'] as $key => $value)
                                                                                                    {
                                                                                                        if($value['req_deLey'] == 'on' && array_key_exists($key,$grados_ley))
                                                                                                        {
                                                                                                            $int_ext = '';
                                                                                                            if(strtoupper(strtolower($value['interno_externo'])) == 'I') { $int_ext = '(Req. Interno)'; }
                                                                                                            if(strtoupper(strtolower($value['interno_externo'])) == 'E') { $int_ext = '(Req. Externo)'; }
                                                                                                            $ck = '';//($cont == 0) ? 'selected="selected"': '';
                                                                                                            echo "<option value='$key' $ck >".utf8_encode($value['nombre']).' '.$int_ext."</option>";
                                                                                                            //$cont++;
                                                                                                        }
                                                                                                    }
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_titulo_edu_ley')?>
                                                                                        <td>
                                                                                            <input type="text" value="" in="Leytit" rel="000043-Leyuse" id="1_titulo_edu_ley" name="1_titulo_edu_ley" >
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_inst_edu_ley')?>
                                                                                        <td>
                                                                                            <input type="text" value="" in="Leyins" rel="000043-Leyuse" id="1_inst_edu_ley" name="1_inst_edu_ley" >
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_anio_edu_ley')?>
                                                                                        <td>
                                                                                            <input type="text" value="" in="Leyani" rel="000043-Leyuse" id="1_anio_edu_ley" name="1_anio_edu_ley" >
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_reqley_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_reqley','1_tr_tabla_reqley','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table><!-- TERMINA INGRESO DE REQUERIMIENTOS DE LEY -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                &nbsp;
                                <div class="backgrd_seccion" id="div_oIdioms">
                                     <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">Manejo de otros idiomas
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_idiomas">
                                    <table width="500" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td colspan="4">
                                                <div id="div_idioms">
                                                    <table border="0" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_idiomas" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">IDIOMA</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">LO HABLA</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">LO LEE</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">LO ESCRIBE</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_idiomas" class="fila1">
                                                                        <td class="borderConsultar" align="center">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_idio_des')?>
                                                                                        <td>
                                                                                            <input class="" type="text" id="1_idio_des" name="1_idio_des" value="" in="Idides" rel="000015-Idiuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_idio_habla')?>
                                                                                        <td>
                                                                                            <select id="1_idio_habla" name="1_idio_habla" in="Idihab" rel="000015-Idiuse" >
                                                                                                <option value="" selected="selected">Seleccione..</option>
                                                                                                <option value="on">Si</option>
                                                                                                <option value="off">No</option>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_idio_lee')?>
                                                                                        <td>
                                                                                            <select id="1_idio_lee" name="1_idio_lee" in="Idilee" rel="000015-Idiuse" >
                                                                                                <option value="" selected="selected">Seleccione..</option>
                                                                                                <option value="on">Si</option>
                                                                                                <option value="off">No</option>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_idio_escribe')?>
                                                                                        <td>
                                                                                            <select id="1_idio_escribe" name="1_idio_escribe" in="Idiesc" rel="000015-Idiuse" >
                                                                                                <option value="" selected="selected">Seleccione..</option>
                                                                                                <option value="on">Si</option>
                                                                                                <option value="off">No</option>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_idiomas_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_idiomas','1_tr_tabla_idiomas','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- TERMINA INGRESO DE IDIOMAS -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div class="backgrd_seccion" id="div_estudiaok">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">
                                                Estudios actuales (Si usted estudia actualmente, relacione todos los estudios en los que est&eacute;)
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_nuevosEstudios">
                                    <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td>
                                                <div id="div_estudios">
                                                    <table border="0" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_estudios" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Qu&eacute; estudia</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Duraci&oacute;n</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Instituci&oacute;n educativa</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Nivel actual</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Horario</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_estudios" class="fila1">
                                                                        <td class="borderConsultar" align="center">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_estu_des','compactar')?>
                                                                                        <td>
                                                                                            <input class="" type="text" id="1_estu_des" name="1_estu_des" value="" in="Nesdes" rel="000016-Nesuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_estu_dur','compactar')?>
                                                                                        <td>
                                                                                            <input class="" type="text" id="1_estu_dur" name="1_estu_dur" value="" in="Nesdur" rel="000016-Nesuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_estu_inst','compactar')?>
                                                                                        <td>
                                                                                            <input class="" type="text" id="1_estu_inst" name="1_estu_inst" value="" in="Nesins" rel="000016-Nesuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_estu_niv','compactar')?>
                                                                                        <td>
                                                                                            <input class="" type="text" id="1_estu_niv" name="1_estu_niv" value="" in="Nesniv" rel="000016-Nesuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_estu_hor','compactar')?>
                                                                                        <td>
                                                                                            <input class="" type="text" id="1_estu_hor" name="1_estu_hor" value="" in="Neshor" rel="000016-Nesuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_estudios_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_estudios','1_tr_tabla_estudios','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- TERMINA INGRESO DE ESTUDIOS -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div class="backgrd_seccion" id="div_m">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">Para realizar mejor su trabajo, qu&eacute; capacitaci&oacute;n necesita: </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_cont_capacita">
                                    <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td>
                                                <div id="div_capacita">
                                                    <table border="0" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_capacitacion" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Tipo de capacitaci&oacute;n</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Especifique en qu&eacute;</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_capacitacion" class="fila1">
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_cap_tip')?>
                                                                                        <td>
                                                                                            <select style="width:205px;" id="1_cap_tip" name="1_cap_tip" in="Capcod" rel="000017-Capuse" >
                                                                                            <option value="">Seleccione..</option>
                                                                                                <?php
                                                                                                    foreach($identGeneral['tip_capacitacion'] as $key => $value)
                                                                                                    {
                                                                                                        echo "<option value='$key' >".utf8_encode($value)."</option>";
                                                                                                        $cont++;
                                                                                                    }
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_cap_que')?>
                                                                                        <td>
                                                                                            <input type="text" id="1_cap_que" name="1_cap_que" value="" in="Capesp" rel="000017-Capuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_capacitacion_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_capacitacion','1_tr_tabla_capacitacion','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- TERMINA INGRESO DE Capacitacion -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div class="backgrd_seccion" id="div_msjOtofics">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">Qu&eacute; otros tipos de artes u oficios sabe: </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_Otofics">
                                    <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td>
                                                <div id="div_artes">
                                                    <table border="0" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table align="center" cellspacing="0" cellpadding="0" border="0" style="" id="tabla_artes" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Arte u oficio</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Estar&iacute;a dispuesto a ense&ntilde;ar este arte u oficio a sus compa&ntilde;eros</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_artes" class="fila1">
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_art_des')?>
                                                                                        <td>
                                                                                            <input type="text" id="1_art_des" name="1_art_des" value="" in="Oaodes" rel="000018-Oaouse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_art_comparte')?>
                                                                                        <td>
                                                                                            <select style="width:205px;" id="1_art_comparte" name="1_art_comparte" in="Oaodae" rel="000018-Oaouse" >
                                                                                                <option value="" selected="selected">Seleccione..</option>
                                                                                                <option value="on">Si</option>
                                                                                                <option value="off">No</option>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_artes_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_artes','1_tr_tabla_artes','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- TERMINA INGRESO DE ARTES -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div><!-- FIN DIV EDUCACION -->
                            <br>
                            <br>
                            <div align="left" id="ref_familia">
                                <table width="900" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="tit_seccion">
                                            <a onClick="javascript:verSeccionCaracterizacion('div_familia');" href="#null">
                                                <img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;INFORMACI&Oacute;N FAMILIAR
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div align="center" class="borderDiv displCaracterizacion" id="div_familia" >
                                <div class="backgrd_seccion" id="div_msjVivecon">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">Datos familiares</td>
                                        </tr>
                                    </table>
                                </div>
                                <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tr>
                                        <td align="center" colspan="2" class="encabezadoTabla">&iquest;Con qui&eacute;n vive usted?</td>
                                    </tr>
                                    <tr class="fila1">
                                        <td>
                                            &nbsp;<select in="Famaco" rel="000019-Famuse" id="wvive_con" name="wvive_con" onChange="addfiltro(this,'','wvive_con','checkbox','reset');" >
                                                    <option value="" selected="selected">Seleccone..</option>
                                                    <?php
                                                        foreach($identGeneral['tipo_acompanantes'] as $key => $value)
                                                        {
                                                            echo "<option value='$key' >".utf8_encode($value['t_acompanante_des'])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div id="listaconsulta_wvive_con" class="borderConsultar"><?=$indicador?></div>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <table width="550" cellspacing="0" cellpadding="0" border="0" align="center" id="tabla_cabezafamilia">
                                    <tr class="encabezadoTabla">
                                        <td width="190"><div align="center">&iquest;Es usted cabeza de familia?</div></td>
                                        <td width="175"><div align="center">N&uacute;mero de ni&ntilde;os a cargo</div></td>
                                        <td width="177"><div align="center">N&uacute;mero de adultos a cargo</div></td>
                                    </tr>
                                    <tr id="1_tr_tabla_cabezafamilia" class="fila1">
                                        <td class="borderConsultar">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr>
                                                        <?=pintarOperadores('1_wcabezfamilia')?>
                                                        <td>
                                                            <select style="width:205px;" id="1_wcabezfamilia" name="1_wcabezfamilia" in="Famcab" rel="000019-Famuse" >
                                                                <option value="" selected="selected">Seleccione..</option>
                                                                <option value="on">Si</option>
                                                                <option value="off">No</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                        <td class="borderConsultar">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr>
                                                        <?=pintarOperadores('1_wnumninoscargo')?>
                                                        <td>
                                                            <input type="text" id="1_wnumninoscargo" name="1_wnumninoscargo" value="" in="Fammac" rel="000019-Famuse" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                        <td class="borderConsultar">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr>
                                                        <?=pintarOperadores('1_wnumadultcargo')?>
                                                        <td>
                                                            <input type="text" id="1_wnumadultcargo" name="1_wnumadultcargo" value="" in="Famaac" rel="000019-Famuse" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                        <td class="" align="center">
                                            <div id="1_tr_tabla_cabezafamilia_remover">
                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_cabezafamilia','1_tr_tabla_cabezafamilia','tr','');">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <div class="backgrd_seccion" id="div_msjNucleo">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">Relacione en el siguiente cuadro las personas que conforman su n&uacute;cleo familiar, si tiene hijos por favor relaci&oacute;nelos as&iacute; no vivan con usted.</td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_nucleo">
                                    <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td>
                                                <div id="div_familiares">
                                                    <table border="0" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_familiar" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Nombres</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Apellidos</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Genero</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Parentesco</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Fecha nacimiento</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Nivel educativo</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Ocupaci&oacute;n</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Vive con usted</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Arte u oficio</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_familiar" class="fila1">
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_nom','compactar')?>
                                                                                        <td>
                                                                                            <input class="anchoInput" type="text" id="1_grFam_nom" name="1_grFam_nom" value="" in="Grunom" rel="000021-Gruuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_apl','compactar')?>
                                                                                        <td>
                                                                                            <input class="anchoInput" type="text" id="1_grFam_apl" name="1_grFam_apl" value="" in="Gruape" rel="000021-Gruuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_gen','compactar')?>
                                                                                        <td>
                                                                                            <select class="anchoInput" style="width:100px;" id="1_grFam_gen" name="1_grFam_gen" in="Grugen" rel="000021-Gruuse" >
                                                                                                <option value="" selected="selected">Seleccione..</option>
                                                                                                <?php
                                                                                                    foreach($identGeneral['genero'] as $key => $value)
                                                                                                    {
                                                                                                        echo "<option value='$key' >".utf8_encode($value)."</option>";
                                                                                                    }
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_paren','compactar')?>
                                                                                        <td>
                                                                                            <select class="anchoInput" style="width:100px;" id="1_grFam_paren" name="1_grFam_paren" in="Grupar" rel="000021-Gruuse" >
                                                                                                <option value="" selected="selected">Seleccione..</option>
                                                                                                <?php
                                                                                                    foreach($identGeneral['parentescos'] as $key => $value)
                                                                                                    {
                                                                                                        echo "<option value='$key' >".utf8_encode($value)."</option>";
                                                                                                        $cont++;
                                                                                                    }
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_fnac','compactar')?>
                                                                                        <td>
                                                                                            <input class="anchoInput" type="text" id="1_grFam_fnac" name="1_grFam_fnac" value="" in="Grufna" rel="000021-Gruuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_niv','compactar')?>
                                                                                        <td>
                                                                                            <select class="anchoInput" style="width:100px;"  id="1_grFam_niv" name="1_grFam_niv" in="Gruesc" rel="000021-Gruuse" >
                                                                                                <option value="">Seleccione..</option>
                                                                                                <?php
                                                                                                    //$cont = 0;
                                                                                                    foreach($identGeneral['grado_escolar'] as $key => $value)
                                                                                                    {
                                                                                                        if($value['req_deLey'] == 'off')
                                                                                                        {
                                                                                                            $ck = '';//($cont == 0) ? 'selected="selected"': '';
                                                                                                            echo "<option value='$key' $ck >".utf8_encode($value['nombre'])."</option>";
                                                                                                            //$cont++;
                                                                                                        }
                                                                                                    }
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_ocup','compactar')?>
                                                                                        <td>
                                                                                            <select class="anchoInput" style="width:100px;" id="1_grFam_ocup" name="1_grFam_ocup" in="Gruocu" rel="000021-Gruuse" >
                                                                                                <option value="">Seleccione..</option>
                                                                                                <?php
                                                                                                    //$cont = 0;
                                                                                                    foreach($identGeneral['ocupaciones'] as $key => $value)
                                                                                                    {
                                                                                                        $ck = '';//($cont == 0) ? 'selected="selected"': '';
                                                                                                        echo "<option value='$key' ".$ck." >".utf8_encode($value)."</option>";
                                                                                                        //$cont++;
                                                                                                    }
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_vcon','compactar')?>
                                                                                        <td>
                                                                                            <select class="anchoInput" style="width:100px;" id="1_grFam_vcon" name="1_grFam_vcon" in="Grucom" rel="000021-Gruuse" >
                                                                                                <option value="" selected="selected">Seleccione..</option>
                                                                                                <option value="on">Si</option>
                                                                                                <option value="off">No</option>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_grFam_art','compactar')?>
                                                                                        <td>
                                                                                            <input class="anchoInput" type="text" id="1_grFam_art" name="1_grFam_art" value="" in="Gruart" rel="000021-Gruuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_familiar_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_familiar','1_tr_tabla_familiar','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- TERMINA INGRESO DE FAMILIARES -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_msjDiscapacitados">
                                    <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td align="center" class="encabezadoTabla">
                                                En su grupo familiar tiene personas con alg&uacute;n tipo de discapacidad.

                                            </td>
                                            <td align="center" class="fila1">
                                                <select in="Famtpd" rel="000019-Famuse" name="tiene_discap" id="tiene_discap" >
                                                    <option value="" selected="selected">Seleccione..</option>
                                                    <option value="on">Si</option>
                                                    <option value="off">No</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_discapacitados">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td>
                                                <div style="display:block;" id="div_familiares_discap">
                                                    <table border="0" align="center" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_discapacitado" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Parentesco</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Edad (A&ntilde;os)</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Tipo de discapacidad</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_discapacitado" class="fila1">
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_discap_parent')?>
                                                                                        <td>
                                                                                            <select style="width:205px;" id="1_discap_parent" name="1_discap_parent" in="Discpa" rel="000020-Disinf" >
                                                                                                <option value="" selected="selected">Seleccione..</option>
                                                                                                <?php
                                                                                                    foreach($identGeneral['parentescos'] as $key => $value)
                                                                                                    {
                                                                                                        echo "<option value='$key' >".utf8_encode($value)."</option>";
                                                                                                        $cont++;
                                                                                                    }
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_art_des')?>
                                                                                        <td>
                                                                                            <input type="text" id="1_discap_edad" name="1_discap_edad" value="" in="Diseda" rel="000020-Disinf" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_discap_tipo')?>
                                                                                        <td>
                                                                                            <input type="text" id="1_discap_tipo" name="1_discap_tipo" value="" in="Disdis" rel="000020-Disinf" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_discapacitado_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_discapacitado','1_tr_tabla_discapacitado','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- TERMINA INGRESO DE FAMILIARES DISCAPACITADOS -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tr>
                                        <td width="" class="encabezadoTabla">Tiene mascota, de qu&eacute; tipo</td>
                                        <td width="" class="fila2">
                                            <div align="left">
                                                &nbsp;<input type="text" value="" size="" in="Famtms" rel="000019-Famuse" id="wmascotatip" name="wmascotatip" onKeyPress="return enterAdd(event,this,'wmascotatip','wmascotatip','text','');" >
                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wmascotatip','wmascotatip','text','');">
                                                <div id="listaconsulta_wmascotatip" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <div class="backgrd_seccion" id="div_msjDiscapacitados">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">
                                                <div align="" class="parrafo1">SALUD</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tr>
                                        <td width="" class="encabezadoTabla">EPS actual</td>
                                        <td width="" class="fila2">
                                            &nbsp;<select in="Ideeps" rel="000013-Ideuse" id="weps" name="weps" onChange="addfiltro(this,'','weps','checkbox','reset');" >
                                                    <option value="">Seleccone..</option>
                                                    <?php
                                                        foreach($identGeneral['lista_eps'] as $key => $value)
                                                        {
                                                            echo "<option value='$key' >".utf8_encode($value)."</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div id="listaconsulta_weps" class="borderConsultar"><?=$indicador?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr class="encabezadoTabla">
                                        <td colspan="2">Tiene usted p&oacute;liza, medicina prepagada o plan complementario en salud, cu&aacute;l:</td>
                                    </tr>
                                    <tr class="fila2">
                                        <td colspan="2">
                                            <div align="left">
                                                <input type="text" value="" in="Idescs" rel="000013-Ideuse" size="75" id="wpoliza" name="wpoliza" onKeyPress="return enterAdd(event,this,'wpoliza','wpoliza','text','');" >
                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addfiltro(this,'wpoliza','wpoliza','text','');">
                                                <div id="listaconsulta_wpoliza" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <br>
                            <div align="left" id="ref_empleoclinica">
                                <table width="900" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="tit_seccion">
                                            <a onClick="javascript:verSeccionCaracterizacion('div_empleoclinica');" href="#null">
                                                <img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;DATOS DEL EMPLEADO CON RESPECTO A LA CL&Iacute;NICA
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div align="center" class="borderDiv displCaracterizacion" id="div_empleoclinica" >
                                <div class="backgrd_seccion" id="div_msjLaboro">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">Servicios donde ha laborado.</td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_laboro">
                                    <table width="600" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td>
                                                <div id="div_servico_laboro">
                                                    <table border="0" style="width: 800px;">
                                                        <tr>
                                                            <td>
                                                                <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_servicio" class="display">
                                                                    <tr>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Unidad o servicio</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Tiempo</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Cargo</th>
                                                                        <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Motivo del cambio</th>
                                                                    </tr>
                                                                    <tr id="1_tr_tabla_servicio" class="fila1">
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_serv_nombre','compactar')?>
                                                                                        <td>
                                                                                            <select style="width:205px;" id="1_serv_nombre" name="1_serv_nombre" in="Cincco" rel="000022-Cinuse" >
                                                                                                <?php
                                                                                                    echo $centro_costos
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_serv_tiempo','compactar')?>
                                                                                        <td>
                                                                                            <input type="text" id="1_serv_tiempo" name="1_serv_tiempo" value="" in="Cintie" rel="000022-Cinuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_serv_cargo','compactar')?>
                                                                                        <td>
                                                                                            <select style="width:205px;" id="1_serv_cargo" name="1_serv_cargo" in="Cincgo" rel="000022-Cinuse" >
                                                                                                <?php
                                                                                                    echo $cargos;
                                                                                                ?>
                                                                                            </select>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="borderConsultar">
                                                                            <div style="display:block; margin-left:3px;" id="">
                                                                                <table cellspacing="0" cellpadding="1" border="0">
                                                                                    <tr>
                                                                                        <?=pintarOperadores('1_serv_motivo','compactar')?>
                                                                                        <td>
                                                                                            <input type="text" id="1_serv_motivo" name="1_serv_motivo" value="" in="Cinmot" rel="000022-Cinuse" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </div>
                                                                        </td>
                                                                        <td class="" align="center">
                                                                            <div id="1_tr_tabla_servicio_remover">
                                                                                <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_servicio','1_tr_tabla_servicio','tr','');">
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <!-- TERMINA INGRESO DE SERVICIOS LABORADOS -->
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_msjOtroEmpleo">
                                    <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td align="center" class="encabezadoTabla">
                                                Ha trabajado en otras empresas anteriores a la cl&iacute;nica
                                            </td>
                                            <td align="center" class="fila1" valign="top">
                                                <div style="display:block; margin-left:3px;" id="">
                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                        <tr class="unicos">
                                                            <?=pintarOperadores('1_otra_empresa')?>
                                                            <td>
                                                            &nbsp;<select in="Idetoe" rel="000013-Ideuse" name="1_otra_empresa" id="1_otra_empresa" >
                                                                    <option value="" selected="selected">Seleccione..</option>
                                                                    <option value="on">Si</option>
                                                                    <option value="off">No</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div style="display:block;" id="div_otroEmpleo">
                                    <div style="align:center;" class="" id="div_msjOtroMjsEmpleo">
                                        <table width="550" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td class="parrafo1">
                                                    Relacione los dos empleos anteriores a su vinculaci&oacute;n a la Cl&iacute;nica.
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <br>
                                    <div id="div_infoEmpleos">
                                        <table width="900" cellspacing="0" cellpadding="0" border="0" align="center">
                                            <tr>
                                                <td>
                                                    <div id="div_empleos">
                                                        <table border="0" align="center" style="width: 800px;">
                                                            <tr>
                                                                <td>
                                                                    <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_empleo" class="display">
                                                                        <tr>
                                                                            <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Empresa</th>
                                                                            <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Tiempo</th>
                                                                            <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Cargo</th>
                                                                        </tr>
                                                                        <tr id="1_tr_tabla_empleo" class="fila1">
                                                                            <td class="borderConsultar">
                                                                                <div style="display:block; margin-left:3px;" id="">
                                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                                        <tr>
                                                                                            <?=pintarOperadores('1_empl_empresa')?>
                                                                                            <td>
                                                                                                <input type="text" id="1_empl_empresa" name="1_empl_empresa" value="" in="Utremp" rel="000023-Utruse" />
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                            <td class="borderConsultar">
                                                                                <div style="display:block; margin-left:3px;" id="">
                                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                                        <tr>
                                                                                            <?=pintarOperadores('1_empl_tiempo')?>
                                                                                            <td>
                                                                                                <input type="text" id="1_empl_tiempo" name="1_empl_tiempo" value="" in="Utrtie" rel="000023-Utruse" />
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                            <td class="borderConsultar">
                                                                                <div style="display:block; margin-left:3px;" id="">
                                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                                        <tr>
                                                                                            <?=pintarOperadores('1_empl_cargo')?>
                                                                                            <td>
                                                                                                <input type="text" id="1_empl_cargo" name="1_empl_cargo" value="" in="Utrcar" rel="000023-Utruse" />
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                            <td class="" align="center">
                                                                                <div id="1_tr_tabla_empleo_remover">
                                                                                    <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_empleo','1_tr_tabla_empleo','tr','');">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <!-- TERMINA INGRESO DE OTROS EMPLEOS-->
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <br>
                                <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tr>
                                        <td width="" align="justify" class="encabezadoTabla">&iquest;Recibe usted o su familia otro tipo de ingreso diferente al salario?</td>
                                        <td width="" class="fila1">
                                            <div align="left">
                                                &nbsp;<select in="Faming-*" rel="000019-Famuse" id="wtipo_salario" name="wtipo_salario" onChange="addfiltro(this,'','wtipo_salario','checkbox','reset');" >
                                                    <option value="" selected="selected">Seleccone..</option>
                                                    <?php
                                                        foreach($identGeneral['tipo_salarios'] as $key => $value)
                                                        {
                                                            echo "<option value='$key' >".utf8_encode($value['tipo_salario'])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div id="listaconsulta_wtipo_salario" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                         </td>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <br>
                            <div align="left" id="ref_condvida">
                                <table width="900" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="tit_seccion">
                                            <a onClick="javascript:verSeccionCaracterizacion('div_condvida');" href="#null">
                                                <img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;CONDICIONES DE VIDA DEL EMPLEADO
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div align="center" class="borderDiv displCaracterizacion" id="div_condvida" >
                                <div class="backgrd_seccion" id="div_msjVivienda">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">
                                                VIVIENDA
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tr>
                                        <td width="" style="border-bottom:1px solid;" class="encabezadoTabla">Tenencia de vivienda</td>
                                        <td width="" class="fila1">
                                            <div align="left">
                                            &nbsp;<select in="Cviviv" rel="000024-Cviuse" id="wtenenciaviv" name="wtenenciaviv" onChange="addfiltro(this,'','wtenenciaviv','checkbox','reset');" >
                                                    <option value="" selected="selected">Seleccone..</option>
                                                    <?php
                                                        foreach($identGeneral['tenencia_vivienda'] as $key => $value)
                                                        {
                                                            echo "<option value='$key' >".utf8_encode($value['tenencia'])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div id="listaconsulta_wtenenciaviv" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla">Tipo de vivienda</td>
                                        <td class="fila2">
                                            <div align="left">
                                                &nbsp;<select in="Cvitvi" rel="000024-Cviuse" id="wtipoviv" name="wtipoviv" onChange="addfiltro(this,'','wtipoviv','checkbox','reset');" >
                                                    <option value="" selected="selected">Seleccone..</option>
                                                    <?php
                                                        foreach($identGeneral['tipo_vivienda'] as $key => $value)
                                                        {
                                                            $ckd = ($identGeneral['condicion_vida']['tipo_viv'] == $key) ? 'selected="selected"': '';
                                                            echo "<option value='$key' $ckd >".utf8_encode($value['t_vivienda'])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div id="listaconsulta_wtipoviv" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla">Tiene usted terraza propia</td>
                                        <td class="fila1" valign="top">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wterraza')?>
                                                        <td>
                                                        &nbsp;<select in="Cvitrz" rel="000024-Cviuse" name="1_wterraza" id="1_wterraza" >
                                                                <option value="" selected="selected">Seleccione..</option>
                                                                <option value="on">Si</option>
                                                                <option value="off">No</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla">Tiene usted lote propio</td>
                                        <td class="fila2" valign="top">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wlote')?>
                                                        <td>
                                                        &nbsp;<select in="Cvilot" rel="000024-Cviuse" name="1_wlote" id="1_wlote" >
                                                                <option value="" selected="selected">Seleccione..</option>
                                                                <option value="on">Si</option>
                                                                <option value="off">No</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-bottom:1px solid;" class="encabezadoTabla">Estado de la vivienda</td>
                                        <td style="border-bottom: #FFFFFF 1px solid;" class="fila1">
                                            &nbsp;<select in="Cvisvi" rel="000024-Cviuse" id="westadoviv" name="westadoviv" onChange="addfiltro(this,'','westadoviv','checkbox','reset');" >
                                                <option value="" selected="selected">Seleccone..</option>
                                                <?php
                                                    foreach($identGeneral['estados_vivienda'] as $key => $value)
                                                    {
                                                        echo "<option value='$key' >".utf8_encode($value['estado'])."</option>";
                                                    }
                                                ?>
                                            </select>
                                            <div id="listaconsulta_westadoviv" class="borderConsultar"><?=$indicador?></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla">Acceso a servicios p&uacute;blicos</td>
                                        <td align="left" class="fila2">
                                            &nbsp;<select in="Cvissp-*" rel="000024-Cviuse" id="wservicios_publicos" name="wservicios_publicos" onChange="addfiltro(this,'','wservicios_publicos','checkbox','reset');" >
                                                <option value="" selected="selected">Seleccone..</option>
                                                <?php
                                                    foreach($identGeneral['servicios_publicos'] as $key => $value)
                                                    {
                                                        echo "<option value='$key' >".utf8_encode($value['servicio_publico'])."</option>";
                                                    }
                                                ?>
                                            </select>
                                            <div id="listaconsulta_wservicios_publicos" class="borderConsultar"><?=$indicador?></div>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <div class="backgrd_seccion" id="div_msjCreditos">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">
                                                CR&Eacute;DITOS
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="div_msjOtroCreditos">
                                    <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td class="encabezadoTabla">
                                                Actualmente tiene usted alg&uacute;n credito.
                                            </td>
                                            <td align="center" class="fila1">
                                                <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_whaycredito')?>
                                                        <td>
                                                        &nbsp;<select in="Cvicre" rel="000024-Cviuse" name="1_whaycredito" id="1_whaycredito" >
                                                                <option value="" selected="selected">Seleccione..</option>
                                                                <option value="on">Si</option>
                                                                <option value="off">No</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div style="display:block;" id="div_Creditos">
                                    <div style="align:center" id="div_mjsInfoCreditos">
                                        <table width="500" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td class="parrafo1">
                                                    Relacione los datos de cr&eacute;ditos que tenga actualmente.
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <br>
                                    <div id="div_infoCredito">
                                        <table width="900" cellspacing="0" cellpadding="0" border="0" align="center">
                                            <tr>
                                                <td>
                                                    <div id="div_cred">
                                                        <table border="0" align="center" style="width: 800px;">
                                                            <tr>
                                                                <td>
                                                                    <table cellspacing="0" cellpadding="0" border="0" style="width:800px;" id="tabla_credito" class="display">
                                                                        <tr>
                                                                            <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Motivo</th>
                                                                            <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Entidad y/u otro</th>
                                                                            <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Valor total del cr&eacute;dito</th>
                                                                            <th style="background: none repeat scroll 0% 0% rgb(42, 93, 176); font-size: 9pt; color: rgb(255, 255, 255); font-weight: bold;">Cuota mensual</th>
                                                                        </tr>
                                                                        <tr id="1_tr_tabla_credito" class="fila1">
                                                                            <td class="borderConsultar">
                                                                                <div style="display:block; margin-left:3px;" id="">
                                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                                        <tr>
                                                                                            <?=pintarOperadores('1_cred_motivo')?>
                                                                                            <td>
                                                                                                <input type="text" id="1_cred_motivo" name="1_cred_motivo" value="" in="Cremot" rel="000025-Creuse" />
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                            <td class="borderConsultar">
                                                                                <div style="display:block; margin-left:3px;" id="">
                                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                                        <tr>
                                                                                            <?=pintarOperadores('1_cred_entidad')?>
                                                                                            <td>
                                                                                                <input type="text" id="1_cred_entidad" name="1_cred_entidad" value="" in="Creent" rel="000025-Creuse" />
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                            <td class="borderConsultar">
                                                                                <div style="display:block; margin-left:3px;" id="">
                                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                                        <tr>
                                                                                            <?=pintarOperadores('1_cred_valor')?>
                                                                                            <td>
                                                                                                <input type="text" id="1_cred_valor" name="1_cred_valor" value="" in="Creval" rel="000025-Creuse" />
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                            <td class="borderConsultar">
                                                                                <div style="display:block; margin-left:3px;" id="">
                                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                                        <tr>
                                                                                            <?=pintarOperadores('1_cred_cuota')?>
                                                                                            <td>
                                                                                                <input type="text" id="1_cred_cuota" name="1_cred_cuota" value="" in="Crecuo" rel="000025-Creuse" />
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div>
                                                                            </td>
                                                                            <td class="" align="center">
                                                                                <div id="1_tr_tabla_credito_remover">
                                                                                    <img class="img_mas1" border="0" src="../../images/medical/HCE/mas.PNG" title="Adicionar otro campo" onClick="addFilaTabla(this,'tabla_credito','1_tr_tabla_credito','tr','');">
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <!-- TERMINA CREDITOS -->
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <div class="backgrd_seccion" id="div_mjsTransporte">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">
                                                TRANSPORTE
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div id="div_infoTransporte">
                                    <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td width="" class="encabezadoTabla">El transporte habitual que usted utiliza para ir a la Cl&iacute;nica es</td>
                                            <td width="" class="fila1">
                                                <div align="left">
                                                &nbsp;<select in="Cvitra-*" rel="000024-Cviuse" id="wtransporte" name="wtransporte" onChange="addfiltro(this,'','wtransporte','checkbox','reset');" >
                                                        <option value="" selected="selected">Seleccone..</option>
                                                        <?php
                                                            foreach($identGeneral['transporte'] as $key => $value)
                                                            {
                                                                echo "<option value='$key' >".utf8_encode($value['tipo_transporte'])."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                    <div id="listaconsulta_wtransporte" class="borderConsultar"><?=$indicador?></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="" colspan="2">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="fila2"><div align="left" class="encabezadoTabla">&nbsp;&nbsp;&nbsp;Otro &iquest;Cu&aacute;l?</div>
                                                <div style="display:block; margin-left:3px;" id="">
                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                        <tr class="unicos">
                                                            <?=pintarOperadores('1_wotrotransporte')?>
                                                            <td>
                                                            <input type="text" value="" in="Cviotr" rel="000024-Cviuse" size="90" id="1_wotrotransporte" name="1_wotrotransporte" >
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <br>
                                <div class="backgrd_seccion" id="div_mjsOtros">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">
                                                INTER&Eacute;S GENERAL
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                    <tr>
                                        <td width="" class="encabezadoTabla">Usted habitualmente a la hora del almuerzo</td>
                                        <td width="" class="fila1">
                                            <div align="left">
                                                &nbsp;<select in="Cvical-*" rel="000024-Cviuse" id="wtipo_almuerzo" name="wtipo_almuerzo" onChange="addfiltro(this,'','wtipo_almuerzo','checkbox','reset');" >
                                                        <option value="" selected="selected">Seleccone..</option>
                                                        <?php
                                                            foreach($identGeneral['tipo_almuerzo'] as $key => $value)
                                                            {
                                                                echo "<option value='$key' >".utf8_encode($value['tipo_almuerzo'])."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                    <div id="listaconsulta_wtipo_almuerzo" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="" colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla" colspan="2">&nbsp;&nbsp;&nbsp;Otros. &iquest;Cu&aacute;les?:</td>
                                    </tr>
                                    <tr>
                                        <td class="fila2" colspan="2" align="center">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wotroalmuerzo')?>
                                                        <td>
                                                        <input type="text" value="" in="Cvioal" rel="000024-Cviuse" size="90" id="1_wotroalmuerzo" name="1_wotroalmuerzo" >
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="" colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width="" class="encabezadoTabla">En cu&aacute;l de estas actividades recreativas usted participar&iacute;a activamente</td>
                                        <td width="" class="fila1">
                                            <div align="left">
                                                &nbsp;<select in="Cvidep-*" rel="000024-Cviuse" id="wtipo_recreativa" name="wtipo_recreativa" onChange="addfiltro(this,'','wtipo_recreativa','checkbox','reset');" >
                                                        <option value="" selected="selected">Seleccone..</option>
                                                        <?php
                                                            foreach($identGeneral['tipo_recreativas'] as $key => $value)
                                                            {
                                                                echo "<option value='$key' >".utf8_encode($value['tipo_recreativa'])."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                    <div id="listaconsulta_wtipo_recreativa" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="" colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla" colspan="2">&nbsp;&nbsp;&nbsp;Otras. &iquest;Cu&aacute;les?:</td>
                                    </tr>
                                    <tr>
                                        <td class="fila2" colspan="2" align="center">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wotrarecre')?>
                                                        <td>
                                                        <input type="text" value="" in="Cvidod" rel="000024-Cviuse" size="90" id="1_wotrarecre" name="1_wotrarecre" >
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="" colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla">En cu&aacute;l de estas actividades culturales y art&iacute;sticas participar&iacute;a activamente</td>
                                        <td class="fila1">
                                            <div align="left">
                                                &nbsp;<select in="Cviaca-*" rel="000024-Cviuse" id="wtipo_artes" name="wtipo_artes" onChange="addfiltro(this,'','wtipo_artes','checkbox','reset');" >
                                                        <option value="" selected="selected">Seleccone..</option>
                                                        <?php
                                                            foreach($identGeneral['tipo_artes'] as $key => $value)
                                                            {
                                                                echo "<option value='$key' >".utf8_encode($value['tipo_artes'])."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                    <div id="listaconsulta_wtipo_artes" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla" colspan="2">&nbsp;&nbsp;&nbsp;Otros. &iquest;Cu&aacute;les?:</td>
                                    </tr>
                                    <tr>
                                        <td class="fila2" colspan="2" align="center">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_wotracultural')?>
                                                        <td>
                                                        <input type="text" value="" in="Cvioac" rel="000024-Cviuse" size="90" id="1_wotracultural" name="1_wotracultural" >
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla">En cu&aacute;l de estas actividades educativas usted participar&iacute;a activamente</td>
                                        <td class="fila1">
                                            <div align="left">
                                                &nbsp;<select in="Cviapa-*" rel="000024-Cviuse" id="wtipo_educativa" name="wtipo_educativa" onChange="addfiltro(this,'','wtipo_educativa','checkbox','reset');" >
                                                        <option value="" selected="selected">Seleccone..</option>
                                                        <?php
                                                            foreach($identGeneral['tipo_educativa'] as $key => $value)
                                                            {
                                                                echo "<option value='$key' >".utf8_encode($value['tipo_educativa'])."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                    <div id="listaconsulta_wtipo_educativa" class="borderConsultar"><?=$indicador?></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="encabezadoTabla" colspan="2"><div align="left">Qu&eacute; actividades y hobbies pract&iacute;ca en su tiempo libre:</div></td>
                                    </tr>
                                    <tr>
                                        <td class="fila2" colspan="2" align="center">
                                            <div style="display:block; margin-left:3px;" id="">
                                                <table cellspacing="0" cellpadding="1" border="0">
                                                    <tr class="unicos">
                                                        <?=pintarOperadores('1_whobbies')?>
                                                        <td>
                                                        <input type="text" value="" in="Cvihbb" rel="000024-Cviuse" size="90" id="1_whobbies" name="1_whobbies" >
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <br>
                                <div align="center" id="div_infoRoles">
                                    <table width="" cellspacing="0" cellpadding="0" border="0" align="center">
                                        <tr>
                                            <td width="" class="encabezadoTabla">
                                                Rol que desempe&ntilde;a en la instituci&oacute;n adicional a su cargo
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="" class="fila1">
                                                &nbsp;<select in="Oincod" rel="000026-Oinuse" id="wtipo_rol" name="wtipo_rol" onChange="addfiltro(this,'','wtipo_rol','checkbox','reset');" >
                                                    <option value="" selected="selected">Seleccone..</option>
                                                    <?php
                                                        foreach($identGeneral['tipo_rol'] as $key => $value)
                                                        {
                                                            echo "<option value='$key' >".utf8_encode($value['tipo_rol'])."</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <div id="listaconsulta_wtipo_rol" class="borderConsultar"><?=$indicador?></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                            </div>
                            <br>
                            <br>
                            <div align="left" id="ref_addons">
                                <table width="900" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td class="tit_seccion">
                                            <a onClick="javascript:verSeccionCaracterizacion('div_preguntas');" href="#null">
                                                <img width="10" height="10" border="0" src="../../images/medical/iconos/gifs/i.p.next[1].gif">&nbsp;SECCI&Oacute;N DE PREGUNTAS
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div align="center" class="borderDiv displCaracterizacion" id="div_preguntas" >
                                <div class="backgrd_seccion" id="div_mjsPreguntas">
                                    <table width="900" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="parrafo1">
                                                Responda a las siguientes preguntas
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div align="center" id="div_infoPreguntas">
                                    <table width="" cellspacing="0" cellpadding="2" border="0" align="center">
                                        <?php
                                            if(count($identGeneral['repositorio_preguntas']) > 0)
                                            {
                                                $fil = 0;
                                                $css = '';
                                                foreach($identGeneral['repositorio_preguntas'] as $key => $value)
                                                {
                                                    $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                                    $fil++;

                                                    $ver = '';
                                                    $reset = '';

                                                    $chkSi = ($value['afirmacion'] == 'on' ) ? 'selected="selected"' : '';
                                                    $chkNo = ($value['afirmacion'] == 'off') ? 'selected="selected"' : '';
                                                    $reset = "resetear2('0_".$key."_pre','0_".$key."_preobs');";

                                                    $ver = 'display:none;';
                                                    if ($value['req_afirmacion'] == 'on' && $value['req_respuesta'] == 'off')
                                                    {
                                                        $resp = '&nbsp;';
                                                        $reset = '';
                                                    }
                                                    elseif ($value['req_afirmacion'] == 'off' && $value['req_respuesta'] == 'on' )
                                                    {
                                                        $ver = 'display:block;';
                                                        $afirm = '&nbsp;';
                                                        $reset = '';
                                                    }
                                                    elseif( $value['req_afirmacion'] == 'on' && $value['req_respuesta'] == 'on' && $value['afirmacion'] == 'on')
                                                    {
                                                        $ver = 'display:block;';
                                                    }

                                                    // campo si requiere afirmar mediante select
                                                    $afirm = '  <div style="display:block; margin-left:3px;" id="">
                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                        <tr class="unicos">
                                                                            '.pintarOperadores("1_".$key."_pre").'
                                                                            <td>
                                                                            &nbsp;<select in="Resafi" rel="000027-Resuse" name="1_'.$key.'_pre" id="1_'.$key.'_pre" >
                                                                                    <option value="" selected="selected">Seleccione..</option>
                                                                                    <option value="on">Si</option>
                                                                                    <option value="off">No</option>
                                                                                </select>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>';

                                                    // campo si requiere escribir una respuesta
                                                    $resp = '   <div class="encabezadoTabla" style="font-size: 10pt;" >'.utf8_encode($value['msj_respuesta']).':</div>
                                                                <div style="display:block; margin-left:3px;" id="">
                                                                    <table cellspacing="0" cellpadding="1" border="0">
                                                                        <tr class="unicos">
                                                                            '.pintarOperadores('1_'.$key.'_preobs').'
                                                                            <td>
                                                                            <input type="text" value="" in="Resres" rel="000027-Resuse" size="" id="1_'.$key.'_preobs" name="1_'.$key.'_preobs" >
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>';

                                                    if ($value['req_afirmacion'] == 'on' && $value['req_respuesta'] == 'off')
                                                    {
                                                        $resp = '&nbsp;';
                                                    }
                                                    elseif ($value['req_afirmacion'] == 'off' && $value['req_respuesta'] == 'on' )
                                                    {
                                                        $afirm = '&nbsp;';
                                                    }
                                                    elseif($value['req_afirmacion'] == 'off' && $value['req_respuesta'] == 'off'
                                                            || $value['req_afirmacion'] == '' && $value['req_respuesta'] == '')
                                                    {
                                                        $afirm = '&nbsp;';
                                                        $ver = 'display:block;';
                                                        $resp = '<div class="parrafo1" style="background-color: #E4E4E4" align="center">[?] No se ha habilitado la forma de respuesta para esta regunta.</div>';
                                                    }

                                                    $secc = '<tr class="'.$css.'">
                                                                <td valign="top" class="encabezadoTabla" style="border-bottom: 1px #ffffff solid;">'.utf8_encode($value['pregunta']).'</td>
                                                                <td style="" valign="top">
                                                                    '.$afirm.'
                                                                </td>
                                                                <td style="" valign="top">
                                                                    <div id="div_0_'.$key.'_preobs" style="width:365px;'.$ver.'">
                                                                        '.$resp.'
                                                                    </div>
                                                                </td>
                                                             </tr>';

                                                    echo $secc;
                                                }
                                            }
                                            else
                                            {
                                                echo '  <tr class="fila2">
                                                                <td class="parrafo1" align="center">[?] En este momento NO existen preguntas para responder.</td>
                                                            </tr>';
                                            }
                                        ?>
                                    </table>
                                </div>
                                <br>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <br />
            <div>
            <div align="left" style="text-align:left;">
                <span style="color:#999999;font-size:14pt;">Resultado de la consulta:</span>
                <div id="div_exportar" style="display:none;text-align:right;">
                    <form action="../reportes/reporteador_caracterizacion.php?form=&accion=exportar_excel" method="post" target="_blank" id="FormularioExportacion">
                        <span style="color:#999999;">Exportar</span>  <img width="28" height="14" border="0" src="../../images/medical/root/export_to_excel.gif" class="botonExcel" style="cursor:pointer;" />
                        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
                    </form>
                </div>
            </div>
                <div id="seccion_reporte" style="border:2px solid #999999;width:100%;">
                    &nbsp;
                </div>
            </div>
        </td>
    </tr>
</table>
<!--<input type="submit" value="Enviar" id="envio" />
</form>-->
</body>
</html>