<?php
include_once("conex.php");
/**
 Programa       : gestion_perfiles.php
 Autor          : Edwar Jaramillo
 Fecha creado   : Julio - Agosto 2012 (aprox.)

 Descripción

 Este script se encarga de configurar o modificar los permisos de acceso a los diferentes menús, se puede hacer una configuración por usuario, por centro de costos o por cargo.
 También se puede asignar permisos para que un usuario pueda ver información de otros usuarios.

 Este script en cuanto a su funcionalidad tiene tres secciones, que corresponden a un pequeño menú con las opciones
    *   Configurar Centros de Costos
        y Cargos                    :   En esta opción se configura el acceso a los menús seleccionados para las convinaciones entre centos de costos y cargos seleccionados.
                                        Esta es una manera de configurar de manera masiva el acceso a menús.

    *   Configurar Usuarios         :   En esta opción se configura el acceso a menús para un usuario en particular, además en esa misma configuración se le puede dar permisos para ver
                                        los datos de otros usuarios que cumplan con las convinaciones u opciones seleccionadas en centro de costo o cargos o ambos.
    *   Consultar configuraciones   :   Esta sección es simplemente para consultar las opciones que están habilitadas para centros de costos, cargos o usuarios, ó también consultar por
                                        menú para ver quienes tienen permiso de verlo.


 ACTUALIZACIONES:

 *  Abril 17 de 2013
    Edwar Jaramillo     :   *   Se modifica el llamado ajax agrupar_indicadores para que funcione correctamente al momento de redibujar submnús
                                de estadísticas, para esto sele envía expresamente el sub-árbol del padre que contiene los submenús de estadísticas.
                            *   El div para mostrar el árbol de permisos que tiene configurado un usuario se incluye dentro de una tabla para mejorar la vista del árbol.
                                Tambien se incluye el encabezado del programa.

 *  Noviembre 15 de 2012
    Edwar Jaramillo     :   *   Se integra la administración de permisos de indicadores, ahora por medio de esta misma interfaz se puede dar permisos para ver indicadores
                                que son usados en el Sistema de Información Estadística y Gerencial.

                            *   Al momento de guardar la configuración de permisos seleccionados, se consulta en la tabla el tipo de configuración y si ya existía uno similar
                                entonces no se incluye ese permiso en el insert, esto se hace con el fin de no estar repitiendo registros en la tabla de permisos.

 *  Octubre 10 de 2012
    Edwar Jaramillo     :   Documentación de código.
*/
include_once "funciones_talhuma.php";

/*****************!!!!!!!!!!!!!!!!!!!NOTA!!!!!!!!!!!!!!!!!!!!!!!!!!!  ****************
    $wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

    la linea anterior se pone entre comentario donde sea que se encuentre en este script porque se esta presentando un promeblema cuando se consultan menus de otros temas,
    esos otros temas tratan de buscar en [otro_prefijo]_000013 usuarios supuestamente en ese nuevo tema pero no los va a encontrar, puesto que generalmente los usuarios
    se deben buscar en talhuma_000013, es por eso que por el momento $wbasedato se va a inicializar siempre con el valor "talhuma"   $wbasedato = 'talhuma';  hasta tanto
    no se pueda encontrar una solución al caso descrito.
**************************************************************************************/
//$wbasedato = 'talhuma'; // Se inicializa siempre con talhuma porque de lo contrario el sistema trata de buscar los usuarios en el prefijo asociado al wtema en root_000076
// la anterior situación es solicionada con la línea siguiente => $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");

function existeConfiguracion($conex, $wgrupotema, $valueArbol, $valueCosto, $valueCargo, $keyUser, $permisos, $esadministrador)
{
    $qExiste = "
        SELECT  id
        FROM    root_000082
        WHERE   Prftem = '".$wgrupotema."'
                AND Prftab = '".$valueArbol."'
                AND Prfcco = '".$valueCosto."'
                AND Prfccg = '".$valueCargo."'
                AND Prfuse = '".$keyUser."'
                AND Prfacc = '".$permisos."'
                AND Prfadm = '".$esadministrador."'";
    $res = mysql_query($qExiste,$conex) or die("Error: " . mysql_errno() . " - Error al consultar existencia de configuracion - gestion_perfiles.php): ".$qExiste." - " . mysql_error());

    $iguales =(mysql_num_rows($res) > 0) ? mysql_num_rows($res): 0; // retorna el número de veces que se encontró en base de datos la misma configuración que se va a guardar.
    return $iguales;
}

if(isset($_SESSION['user']) && isset($accion))
{
    $consultaAjax = '';
    include_once("root/comun.php");
    

    // $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
    // $wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
    $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma"); // con esto ya se puede cambiar el código de la empresa en wemp_pmla y seleccionar adecuadamente el prefijo de su tabla de empleados

    if(isset($accion) && $accion=='guardar')
    {
        $user_session = explode('-',$_SESSION['user']);
        $user_session = $user_session[1];
        $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

        $insert = "";
        $insert_all = "";
        if(!isset($esadministrador))
        {
            $esadministrador = 'off';
        }

        /**
            * Si se lecciona centros de costos y cargos, entonces para cada centro de costos se guardan y se asocian tantos cargos esten seleccionados.
            * Si solo hay cargos, se guarda un registro por cada cargo.
            * Si solo hay centros de costos se guarda un registro por cada centro de costo.
            * *
            * Si es para dar permisos a un usuario y hay centros de costos seleccionados y cargos entonces para cada usuario se guardan tantos centros de costos y a su vez por cada centro de costos
            * se guardan tantos cargos como esten seleccionados.

            * Toda la enterior información también se debe replicar por cada uno de los menús chequeados.
        */

        if(isset($tipoconf) && $tipoconf == 'acc_pestanas') { $permisos = 'ninguno'; $esadministrador = 'off'; } // Es "ningúno" y administrador "off" cuando se guarda por ejemplo centros de costos y/o cargos pero no usuarios
        $coma = "|,";
        if(isset($wuse_pfls_chk)) // Si hay usuarios seleccionados
        {
            $inrt = false;
            $insert = "
                        INSERT INTO root_000082
                            (Medico, Fecha_data, Hora_data, Prftem, Prftab, Prfcco, Prfccg, Prfuse, Prfacc, Prfadm, Prfest, Seguridad)
                        VALUES ";

            foreach($arbol_chk as $keyArbol => $valueArbol) // Por cada menú del árbol se guarda todos los demás usuarios chequeados
            {
                foreach($wuse_pfls_chk as $keyUser => $valueUser) // Por cada usuario chequeado se guarda tantos cargos chequeados existan
                {
                    if(!isset($wcccargo_pfls_chk) && !isset($wccostos_pfls_chk)) // Si no hay cargos y no hay centro de costos
                    {
                        if(existeConfiguracion($conex, $wgrupotema, $valueArbol, '', '', $keyUser, $permisos, $esadministrador) == 0)
                        {
                            $insert .= "
                                            ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','','','".$keyUser."','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                            ";
                            $coma = ',';
                            $inrt = true;
                        }
                    }
                    elseif(isset($wcccargo_pfls_chk) && count($wcccargo_pfls_chk) > 0  && !isset($wccostos_pfls_chk)) // Si hay cargos pero no hay centro de costos y hay usuarios
                    {
                        foreach($wcccargo_pfls_chk as $keyCargo => $valueCargo)
                        {
                            if(existeConfiguracion($conex, $wgrupotema, $valueArbol, '', $valueCargo, $keyUser, $permisos, $esadministrador) == 0)
                            {
                                $insert .= "
                                                ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','','".$valueCargo."','".$keyUser."','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                                ";
                                $coma = ',';
                                $inrt = true;
                            }
                        }
                    }
                    elseif(isset($wccostos_pfls_chk) && count($wccostos_pfls_chk) > 0 && !isset($wcccargo_pfls_chk)) // Si hay centro costos pero no hay cargos y hay usuarios
                    {
                        foreach($wccostos_pfls_chk as $keyCosto => $valueCosto)
                        {
                            if(existeConfiguracion($conex, $wgrupotema, $valueArbol, $valueCosto, '', $keyUser, $permisos, $esadministrador) == 0)
                            {
                                $insert .= "
                                                ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','".$valueCosto."','','".$keyUser."','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                                ";
                                $coma = ",";
                                $inrt = true;
                            }
                        }
                    }
                    elseif(count($wccostos_pfls_chk) == 1 && array_key_exists('todos',$wccostos_pfls_chk)) // centro de costo (*) y cargo (*) o varios
                    {
                        $setOK = false;
                        if(count($wcccargo_pfls_chk) == 1 && array_key_exists('todos',$wcccargo_pfls_chk)) // centro de costo (*) y cargos (*)
                        {
                            if(existeConfiguracion($conex, $wgrupotema, $valueArbol, '*', '*', $keyUser, $permisos, $esadministrador) == 0)
                            {
                                $insert .= "
                                        ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','*','*','".$keyUser."','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                        ";
                                $setOK = true;
                                $inrt = true;
                            }
                        }
                        else
                        {
                            foreach($wcccargo_pfls_chk as $keyCargo => $valueCargo) // Si centro costos es (*) y hay mas de un cargo que es diferente de (*)
                            {
                                if(existeConfiguracion($conex, $wgrupotema, $valueArbol, '*', $valueCargo, $keyUser, $permisos, $esadministrador) == 0)
                                {
                                    $insert .= "
                                            ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','*','".$valueCargo."','".$keyUser."','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                            ";
                                    $coma = ",";
                                    $setOK = true;
                                    $inrt = true;
                                }
                            }
                        }
                        if($setOK == true)
                        { $coma = ","; }
                    }
                    else // Si centro de costos diferente a (*) y tambien hay cargos seleccionados
                    {
                        $implodeCargos = implode("','",$wcccargo_pfls_chk);
                        $qUseCargoCco = "
                            SELECT  Idecco, Ideccg
                            FROM    ".$wbasedato."_000013
                            WHERE   Ideccg IN ('".$implodeCargos."')
                            GROUP BY    Idecco, Ideccg";
                        $res = mysql_query($qUseCargoCco,$conex) or die("Error: " . mysql_errno() . " - Error al Insertar al consultar usuarios para los cargos seleccionados - gestion_perfiles.php): " . $qUseCargoCco . " - " . mysql_error());

                        $arrayCargoCostos = array();
                        while($row = mysql_fetch_array($res))
                        {
                            $arrayCargoCostos[$row['Idecco'].$row['Ideccg']] = $row['Idecco'];
                        }

                        if(count($wcccargo_pfls_chk) == 1 && array_key_exists('todos',$wcccargo_pfls_chk)) // centro de costo diferente de (*) y cargos es (*)
                        {
                           foreach($wccostos_pfls_chk as $keyCosto => $valueCosto)
                            {
                                if(existeConfiguracion($conex, $wgrupotema, $valueArbol, $valueCosto, '*', $keyUser, $permisos, $esadministrador) == 0)
                                {
                                    $insert .= "
                                                    ".$coma."('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','".$valueCosto."','*','".$keyUser."','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                            ";
                                    $coma = ',';
                                    $inrt = true;
                                }
                            }
                        }
                        else // Si centro de costos diferente a (*) y cartos diferente de (*), por cada centro de costos guarda todos los cargos.
                        {
                            foreach($wccostos_pfls_chk as $keyCosto => $valueCosto) // Recorre centro costos
                            {
                                foreach($wcccargo_pfls_chk as $keyCargo => $valueCargo) // Recorre cargos
                                {
                                    if(array_key_exists($valueCosto.$valueCargo,$arrayCargoCostos))
                                    {
                                        if(existeConfiguracion($conex, $wgrupotema, $valueArbol, $valueCosto, $valueCargo, $keyUser, $permisos, $esadministrador) == 0)
                                        {
                                            $insert .= "
                                                    ".$coma."('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','".$valueCosto."','".$valueCargo."','".$keyUser."','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                            ";
                                            $coma = ',';
                                            $inrt = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($inrt == true)
            { $insert_all .= $insert;}
        // Fin si se seleccionó usuarios
        }
        elseif(isset($wcccargo_pfls_chk) && count($wcccargo_pfls_chk) > 0 && !isset($wccostos_pfls_chk)) // Si hay cargos pero no hay centro de costos y no hay usuarios
        {
            $inrt = false;
            $insert = "
                        INSERT INTO root_000082
                            (Medico, Fecha_data, Hora_data, Prftem, Prftab, Prfcco, Prfccg, Prfuse, Prfacc, Prfadm, Prfest, Seguridad)
                        VALUES ";

            foreach($arbol_chk as $keyArbol => $valueArbol)
            {
                foreach($wcccargo_pfls_chk as $keyCargo => $valueCargo)
                {
                    if(existeConfiguracion($conex, $wgrupotema, $valueArbol, '', $valueCargo, '', $permisos, $esadministrador) == 0)
                    {
                        $insert .= "
                                        ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','','".$valueCargo."','','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                        ";
                        $coma = ',';
                        $inrt = true;
                    }
                }
            }
            if($inrt == true)
            { $insert_all .= $insert;}
        }
        elseif(isset($wccostos_pfls_chk) && count($wccostos_pfls_chk) > 0 && !isset($wcccargo_pfls_chk)) // Si hay centro costos pero no hay cargos y no hay usuarios
        {
            $inrt = false;
            $insert = "
                        INSERT INTO root_000082
                            (Medico, Fecha_data, Hora_data, Prftem, Prftab, Prfcco, Prfccg, Prfuse, Prfacc, Prfadm, Prfest, Seguridad)
                        VALUES ";

            foreach($arbol_chk as $keyArbol => $valueArbol)
            {
                foreach($wccostos_pfls_chk as $keyCosto => $valueCosto)
                {
                    if(existeConfiguracion($conex, $wgrupotema, $valueArbol, $valueCosto, '', '', $permisos, $esadministrador) == 0)
                    {
                        $insert .= "
                                        ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','".$valueCosto."','','','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                        ";
                        $coma = ",";
                        $inrt = true;
                    }
                }
            }
            if($inrt == true)
            { $insert_all .= $insert;}
        }
        else // Si hay cargos seleccionados y centros de costos seleccionados y no hay usuarios
        {
            $inrt = false;
            $insert = "
                        INSERT INTO root_000082
                            (Medico, Fecha_data, Hora_data, Prftem, Prftab, Prfcco, Prfccg, Prfuse, Prfacc, Prfadm, Prfest, Seguridad)
                        VALUES ";

            if(count($wccostos_pfls_chk) == 1 && array_key_exists('todos',$wccostos_pfls_chk)) // centro de costo (*) y cargo (*) o varios
            {
                $setOK == false;
                foreach($arbol_chk as $keyArbol => $valueArbol)
                {
                    if(count($wcccargo_pfls_chk) == 1 && array_key_exists('todos',$wcccargo_pfls_chk)) // centro de costo (*) y cargos (*)
                    {
                        if(existeConfiguracion($conex, $wgrupotema, $valueArbol, '*', '*', '', $permisos, $esadministrador) == 0)
                        {
                            $insert .= "
                                    ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','*','*','','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                    ";
                            $setOK = true;
                            $inrt = true;
                        }
                    }
                    else
                    {
                        foreach($wcccargo_pfls_chk as $keyCargo => $valueCargo) // Si centro costos es (*) y hay mas de un cargo que es diferente de (*)
                        {
                            if(existeConfiguracion($conex, $wgrupotema, $valueArbol, '*', $valueCargo, '', $permisos, $esadministrador) == 0)
                            {
                                $insert .= "
                                        ".$coma." ('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','*','".$valueCargo."','','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                        ";
                                $coma = ",";
                                $setOK = true;
                                $inrt = true;
                            }
                        }
                    }
                    if($setOK == true)
                    { $coma = ","; }
                }
            }
            else // Si centro de costos diferente a (*) y tambien hay cargos seleccionados
            {
                $implodeCargos = implode("','",$wcccargo_pfls_chk);
                $qUseCargoCco = "
                    SELECT  Idecco, Ideccg
                    FROM    ".$wbasedato."_000013
                    WHERE   Ideccg IN ('".$implodeCargos."')
                    GROUP BY    Idecco, Ideccg";
                $res = mysql_query($qUseCargoCco,$conex) or die("Error: " . mysql_errno() . " - Error al Insertar al consultar usuarios para los cargos seleccionados - gestion_perfiles.php): " . $qUseCargoCco . " - " . mysql_error());

                $arrayCargoCostos = array();
                while($row = mysql_fetch_array($res))
                {
                    $arrayCargoCostos[$row['Idecco'].$row['Ideccg']] = $row['Idecco'];
                }

                foreach($arbol_chk as $keyArbol => $valueArbol)
                {
                    if(count($wcccargo_pfls_chk) == 1 && array_key_exists('todos',$wcccargo_pfls_chk)) // centro de costo diferente de (*) y cargos es (*)
                    {
                        foreach($wccostos_pfls_chk as $keyCosto => $valueCosto)
                        {
                            if(existeConfiguracion($conex, $wgrupotema, $valueArbol, $valueCosto, '*', '', $permisos, $esadministrador) == 0)
                            {
                                $insert .= "
                                                ".$coma."('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','".$valueCosto."','*','','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                        ";
                                $coma = ',';
                                $inrt = true;
                            }
                        }
                    }
                    else // Si centro de costos diferente a (*) y cartos diferente de (*), por cada centro de costos guarda todos los cargos.
                    {
                        foreach($wccostos_pfls_chk as $keyCosto => $valueCosto) // Recorre centro costos
                        {
                            foreach($wcccargo_pfls_chk as $keyCargo => $valueCargo) // Recorre cargos
                            {
                                if(array_key_exists($valueCosto.$valueCargo,$arrayCargoCostos))
                                {
                                    if(existeConfiguracion($conex, $wgrupotema, $valueArbol, $valueCosto, $valueCargo, '', $permisos, $esadministrador) == 0)
                                    {
                                        $insert .= "
                                                ".$coma."('root','".date("Y-m-d")."','".date("H:i:s")."','".$wgrupotema."','".$valueArbol."','".$valueCosto."','".$valueCargo."','','".$permisos."','".$esadministrador."','on','C-".$user_session."')
                                        ";
                                        $coma = ',';
                                        $inrt = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if($inrt == true)
            { $insert_all .= $insert;}
        }

        if ($coma != "|,")
        {
            $insert = str_replace('|,', '', $insert_all).';';
            $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - Error al Insertar en Perfiles por usuarios (root_000082) - gestion_perfiles.php): " . $insert_all . " - " . mysql_error());
        }

        //echo "<pre>";print_r($insert);echo "</pre>";
        // echo "<pre>";print_r($_POST);echo "</pre>";
        $temaUrl = '';
        if(isset($temaselect) && $temaselect == 'on'){}
        else
        { $temaUrl = "&wtema=".$wtema; }

        header("location:gestion_perfiles.php?wemp_pmla=".$wemp_pmla.$temaUrl);//."&wtema=".$wtema
    }
    elseif(isset($accion) && $accion == 'load')
    {
        //$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
        if(isset($form) && $form == 'load_costo')
        {
            echo getOptionsCostos($wemp_pmla, $conex, $wbasedato, $id_padre);
        }
        elseif(isset($form) && $form == 'load_chk_costo') // centro de costo seleccionado
        {
            echo getOptionsCostos($wemp_pmla, $conex, $wbasedato, $id_padre, 'on');
        }
        if(isset($form) && $form == 'load_cargo')
        {
            echo getOptionsCargos($wemp_pmla, $conex, $wbasedato, $id_padre);
        }
        elseif(isset($form) && $form == 'load_chk_cargo') // Cargo seleccionado
        {
            echo getOptionsCargos($wemp_pmla, $conex, $wbasedato, $id_padre, 'on');
        }
        elseif(isset($form) && $form == 'load_users')
        {
            echo getOptionsUsers($wemp_pmla, $conex, $wbasedato, $id_padre);
        }
        elseif(isset($form) && $form == 'load_chk_users') // Cargo seleccionado
        {
            echo getOptionsUsers($wemp_pmla, $conex, $wbasedato, $id_padre, 'on');
        }
        elseif(isset($form) && $form == 'load_cdmenu')
        {
            echo getOptionsMenus($wemp_pmla, $conex, $wbasedato, $id_padre, $wtema);
        }
        elseif(isset($form) && $form == 'load_menu') // Crea y retorna el menú creado
        {
            $data = array();
            $menus_sin_ubicar = array();
            $arbol_menu = crearArbolMenus($wemp_pmla, $conex, $id_padre, $menus_sin_ubicar);
            $div = '';
            $menu = pintarMenuSeleccion($conex, $arbol_menu['menus_cod'],$arbol_menu['menus_info'], $div, 'checks');
            // $data['menus_sin_ubicar'] = implode('|',$menus_sin_ubicar);
            // echo '<pre>Arbol completo:<br />';print_r($menus_sin_ubicar);echo '</pre>';
            $data['total_menus'] = count($menu);
            $data['menu'] = utf8_encode($menu);
            echo json_encode($data);
        }
        elseif(isset($form) && $form == 'load_arbol_users') // Crea y retorna el menú creado
        {
            $menu = '';
            $detalle = '';
            if($id_padre != '')
            {
                $sqlU = "   SELECT  Idecco, Ideccg, CONCAT(Ideno1,' ',Ideno2, ' ', Ideap1, ' ',Ideap2) AS nombre_use
                            FROM    ".$wbasedato."_000013
                            WHERE   Ideuse = '".$id_padre."'";
                $resU = mysql_query($sqlU,$conex) or die("Error: " . mysql_errno() . " - en el query consultar informacion de usuario de sesión: ".$sqlU." - ".mysql_error());

                $info_use['cod_use'] = $id_padre;
                if(mysql_num_rows($resU))
                {
                    $rowU = mysql_fetch_array($resU);
                    $info_use['cod_cco'] = $rowU['Idecco'];
                    $info_use['cod_ccg'] = $rowU['Ideccg'];
                    $info_use['nombre_use'] = $rowU['nombre_use'];
                }

                $data = array();
                $menus_sin_ubicar = array();
                $arbol_menu = crearArbolMenus($wemp_pmla, $conex, $wtema, $menus_sin_ubicar, $info_use);
                $div = '';
                $menu = pintarMenuSeleccion($conex, $arbol_menu['menus_cod'],$arbol_menu['menus_info'], $div, 'solo_arbol');

                $res_detll = getMenusQuery($wemp_pmla, $conex, 'detalle', $wtema, $info_use);

                $msjVista = "    <br />
                                <div class='msj_explicativo'>Detalle de Acceso a las opciones por Centro de Costos o por Cargo que tienen habilitado men&uacute;s.</div>";

                $msjPermiso = " <br />
                                <div class='msj_explicativo'>Permisos que tiene para ver men&uacute;s y que adicionalmente puede ver informaci&oacute;n de otros usuarios.</div>";

                $detalle = generarDetalle($wemp_pmla, $conex, $wbasedato, $wtema, $res_detll, 'usuario', $msjVista, $msjPermiso);

                if($menu != '')
                {
                    $tiene_menu = "<div class='encabezadoTabla' style='margin-left: 20px;'>Tiene acceso a las siguientes opciones</div>";
                    $menu = $tiene_menu.$menu;
                }
                elseif ($menu == '' && $detalle == '')
                {
                    $menu = "
                                    <div class='msj_explicativo' style='margin-left: 20px; text-align:center;'>NO TIENE ACCESO A OPCIONES DE MEN&Uacute;.</div>";
                    $detalle = '';
                }
            }

            // $data['total_menus'] = count($menu);
            $data['menu'] = utf8_encode($menu);
            $data['menus_detalle'] = $detalle;
            echo json_encode($data);
        }
        elseif(isset($form) && $form == 'load_arbol_ccostos') // Crea y retorna el menú creado para centros de costos
        {
            $menu = '';
            $detalle = '';
            if($id_padre != '')
            {
                $info_use['cod_use'] = '';
                $info_use['cod_cco'] = $id_padre;
                $info_use['cod_ccg'] = '';
                $info_use['nombre_use'] = '';


                $data = array();
                $menus_sin_ubicar = array();
                $arbol_menu = crearArbolMenus($wemp_pmla, $conex, $wtema, $menus_sin_ubicar, $info_use, 'centro_costo');
                $div = '';
                $menu = pintarMenuSeleccion($conex, $arbol_menu['menus_cod'],$arbol_menu['menus_info'], $div, 'solo_arbol');

                $res_detll = getMenusQuery($wemp_pmla, $conex, 'detalle', $wtema, $info_use, 'centro_costo');

                $msjVista = "    <br />
                                <div class='msj_explicativo'>Detalle de Centros de Costos que tienen habilitados men&uacute;s.</div>";

                $msjPermiso = " <br />
                                <div class='msj_explicativo'>Usuarios que con ese Centro de Costo tienen configurados men&uacute;s y tienen permisos para ver otros usuarios.</div>";

                $detalle = generarDetalle($wemp_pmla, $conex, $wbasedato, $wtema, $res_detll, 'costos', $msjVista, $msjPermiso);

                if($menu != '')
                {
                    $tiene_menu = "<div class='encabezadoTabla' style='margin-left: 20px;'>Tiene acceso a las siguientes opciones</div>";
                    $menu = $tiene_menu.$menu;
                }
                elseif ($menu == '' && $detalle == '')
                {
                    $menu = "
                                    <div class='msj_explicativo' style='margin-left: 20px; text-align:center;'>NO TIENE ACCESO A OPCIONES DE MEN&Uacute;.</div>";
                    $detalle = '';
                }
            }

            // $data['total_menus'] = count($menu);
            $data['menu'] = utf8_encode($menu);
            $data['menus_detalle'] = $detalle;
            echo json_encode($data);
        }
        elseif(isset($form) && $form == 'load_arbol_cargos') // Crea y retorna el menú creado para centros de costos
        {
            $menu = '';
            $detalle = '';
            if($id_padre != '')
            {
                $info_use['cod_use'] = '';
                $info_use['cod_cco'] = '';
                $info_use['cod_ccg'] = $id_padre;
                $info_use['nombre_use'] = '';


                $data = array();
                $menus_sin_ubicar = array();
                $arbol_menu = crearArbolMenus($wemp_pmla, $conex, $wtema, $menus_sin_ubicar, $info_use, 'cargos');
                $div = '';
                $menu = pintarMenuSeleccion($conex, $arbol_menu['menus_cod'],$arbol_menu['menus_info'], $div, 'solo_arbol');

                $res_detll = getMenusQuery($wemp_pmla, $conex, 'detalle', $wtema, $info_use, 'cargos');

                $msjVista = "    <br />
                                <div class='msj_explicativo'>Detalle de Cargos que tienen habilitados men&uacute;s.</div>";

                $msjPermiso = " <br />
                                <div class='msj_explicativo'>Usuarios que con ese cargo tienen configurados men&uacute;s y tienen permisos para ver otros usuarios.</div>";
                $detalle = generarDetalle($wemp_pmla, $conex, $wbasedato, $wtema, $res_detll, 'cargos', $msjVista, $msjPermiso);

                if($menu != '')
                {
                    $tiene_menu = "
                                    <div class='encabezadoTabla' style='margin-left: 20px;'>Tiene acceso a las siguientes opciones</div>";
                    $menu = $tiene_menu.$menu;
                }
                elseif ($menu == '' && $detalle == '')
                {
                    $menu = "
                                    <div class='msj_explicativo' style='margin-left: 20px; text-align:center;'>NO TIENE ACCESO A OPCIONES DE MEN&Uacute;.</div>";
                    $detalle = '';
                }
            }

            // $data['total_menus'] = count($menu);
            $data['menu'] = utf8_encode($menu);
            $data['menus_detalle'] = $detalle;
            echo json_encode($data);
        }
        elseif(isset($form) && $form == 'load_arbol_cdmenu') // Quién tiene acceso al menú
        {
            $menu = '';
            $detalle = '';
            if($id_padre != '')
            {
                $info_use['cod_use'] = '';
                $info_use['cod_cco'] = '';
                $info_use['cod_ccg'] = '';
                $info_use['cod_tab'] = $id_padre;
                $info_use['nombre_use'] = '';

                // $data = array();
                // $menus_sin_ubicar = array();
                // $arbol_menu = crearArbolMenus($wemp_pmla, $conex, $wtema, $menus_sin_ubicar, $info_use, 'cargos');
                // $div = '';
                // $menu = pintarMenuSeleccion($conex, $arbol_menu['menus_cod'],$arbol_menu['menus_info'], $div, 'solo_arbol');

                $menu = '';

                $res_detll = getMenusQuery($wemp_pmla, $conex, 'detalle_menu', $wtema, $info_use, 'menu_ver');

                $msjVista = "    <br />
                                <div class='msj_explicativo'>Detalle de Cargos que tienen habilitados men&uacute;s.</div>";

                $msjPermiso = " <br />
                                <div class='msj_explicativo'>Usuarios que con ese cargo tienen configurados men&uacute;s y tienen permisos para ver otros usuarios.</div>";
                $detalle = generarDetalle($wemp_pmla, $conex, $wbasedato, $wtema, $res_detll, 'menus', $msjVista, $msjPermiso);

                // if($menu != '')
                // {
                    // $tiene_menu = "
                                    // <div class='encabezadoTabla' style='margin-left: 20px;'>Tiene acceso a las siguientes opciones</div>";
                    // $menu = $tiene_menu.$menu;
                // }
                // else
                if ($menu == '' && $detalle == '')
                {
                    $menu = "
                                    <div class='msj_explicativo' style='margin-left: 20px; text-align:center;'>ESTE MEN&Uacute; NO EST&Aacute; HABILITADO PARA NING&Uacute;N USUARIO.</div>";
                    $detalle = '';
                }
            }

            // $data['total_menus'] = count($menu);
            $data['menu'] = $menu;
            $data['menus_detalle'] = $detalle;
            echo json_encode($data);
        }
        elseif(isset($form) && $form == 'agrupar_indicadores')
        {
            $data = array();
            $menus_sin_ubicar = array();
            $arbol_menu = crearArbolMenus($wemp_pmla, $conex, $wtema, $menus_sin_ubicar);
            // $arbol_menu['menus_cod'] = $arbol_menu['menus_cod'][$id_opcion_padre];
            // print_r($arbol_menu);
            $div = '';
            $propiedades = array();
            $tabla_propiedades = $arbol_menu['menus_info'][$id_opcion_padre]['info']['prop_tabla'];
            $arbol_2[$id_opcion_padre] = $arbol_menu['menus_cod'][$id_opcion_padre];
			//print_r($arbol_menu['menus_cod'][$id_opcion_padre]);
            $menu = pintarMenuSeleccion ($conex, $arbol_2,$arbol_menu['menus_info'], $div, 'checks','','20',$id_check_padre,$propiedades,'','off','on',$tabla_sub_grp,$tabla_propiedades);
            // $data['menus_sin_ubicar'] = implode('|',$menus_sin_ubicar);
            // echo '<pre>Arbol completo:<br />';print_r($menus_sin_ubicar);echo '</pre>';
            $data['total_menus'] = count($menu);
            $data['menu'] = utf8_encode($menu);
            echo json_encode($data);
        }
        return;
    }
    elseif(isset($accion) && $accion == 'delete')
    {
        if(isset($form) && $form == 'borrar_reg')
        {
            $data = array('error'=>0,'mensaje'=>'');
            $q = "  DELETE  FROM    root_000082
                    WHERE   id = '$id_registro'";
            $res = @mysql_query($q,$conex);

            if(!$res)
            {
                $data['error'] = 1;
                $data['mensaje'] = "[?] Se produjo un error al tratar de eliminar el registro.";
            }
            else
            {
                $data['mensaje'] = "[!] El registro fue eliminado satisfactoriamente.";
            }
            echo json_encode($data);
        }
        return;
    }
    elseif(isset($accion) && $accion == 'update')
    {
        if(isset($form) && $form == 'update_estado')
        {
            $data = array('error'=>0,'mensaje'=>'');
            $q = "  UPDATE  root_000082
                    SET     ".$campo." = '".$value."'
                    WHERE   id = '$id_registro'";
            $res = @mysql_query($q,$conex);

            if(!$res)
            {
                $data['error'] = 1;
                $data['mensaje'] = "[?] Se produjo un error al tratar de actualizar el registro.";
            }
            else
            {
                $data['mensaje'] = "[!] El registro fue actualizado satisfactoriamente.";
            }
            echo json_encode($data);
        }
        return;
    }
}
else
{
    if(!isset($_SESSION['user']))
    {
        // Implementado para hacer algunas pruebas, a veces es necesario realizar algunas pruebas sin tener que estar loqueado.
        if(isset($user_session))
        {
            $_SESSION['user'] = $user_session;
        }
    }

    if(!isset($_SESSION['user']) && !isset($accion))
    {
        echo '  <br /><br /><br /><br />
                <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;text-align:center;" >
                    [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
                </div>';
        exit();
    }

    include_once("root/comun.php");
    



    $temaselect = 'off';
    if(!isset($wtema))
    { $temaselect = 'on'; }

    /*********************************
    * Datos Lista temas
    */
    $qT= "  SELECT  Temcod AS codigo, Temdes AS nombre
            FROM    root_000076
            WHERE   Temest = 'on'
            ORDER BY    Temdes";
    $rT = mysql_query($qT,$conex);
    $temasList = array();
    $primero = '';
    while($row = mysql_fetch_array($rT))
    {
        if($primero == '') { $primero = $row['codigo'];}
        $temasList[$row['codigo']] =  utf8_encode(strtoupper(strtolower($row['nombre'])));
    }

    if($primero != '') {
        if(!isset($wtema))
        {
            $wtema = $primero;
        }
    }


    // $wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
    $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma"); // con esto ya se puede cambiar el código de la empresa en wemp_pmla y seleccionar adecuadamente el prefijo de su tabla de empleados

    $centro_costos = getOptionsCostos($wemp_pmla, $conex, $wbasedato, '');

    $cargos = getOptionsCargos($wemp_pmla, $conex, $wbasedato, '');

    $usuariosList = getOptionsUsers($wemp_pmla, $conex, $wbasedato, '');

    $menusList = utf8_decode(getOptionsMenus($wemp_pmla, $conex, $wbasedato, '', $wtema));

    // echo '<pre>Arbol completo:<br />';print_r($arbol_menu);echo '</pre>';
?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='pragma' content='no-cache'>

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

<script type="text/javascript">

    /**
     * Cambia el estilo de la opción para borrar un registro de permisos. (usada en funciones_talhuma.php)
     *
     * @return unknown
     */
    function onOverDelete(ele)
    {
        $("#"+ele).css({'font-weight': 'bold', 'color': 'red', 'cursor' :'pointer'});
    }

    /**
     * Cambia el estilo de la opción para borrar un registro de permisos. (usada en funciones_talhuma.php)
     *
     * @return unknown
     */
    function onOutDelete(ele)
    {
        $("#"+ele).css({'font-weight': '', 'color': 'orange', 'cursor' :''});
    }

    function funcionesConfirmDelete(ele,id_reg,form,acc,row)
    {
        mes_confirm = "Esta acción elimina por completo este registo del sistema.\n\nQuiere Continuar?";
        if(confirm(mes_confirm))
        {
            eliminarReg(id_reg,form,acc,row);
        }
        else
        { return false; }
    }

    /**
     * Elimina un registro de permisos en la base de datos mediante un llamado ajax, adicionalmente recarga todas las opciones por usuario, centro de costos o cargos
     * por si en esos otros perfiles también estaba referenciado el permiso que ha sido borrado.
     *
     * @return unknown
     */
    function eliminarReg(id_reg,form,acc,row)
    {
        $.post("gestion_perfiles.php",
            {
                wemp_pmla:      $('#wemp_pmla').val(),
                wtema:          $('#wtema').val(),
                temaselect:     $('#temaselect').val(),
                wuse:           $('#wuse').val(),
                id_registro:    id_reg,
                consultaAjax:   '',
                accion:         acc,
                form:           form
            }
            ,function(data) {
                if(data.error == 1)
                {
                    alert(data.mensaje);
                }
                else
                {
                    $("#"+row).remove();

                    // $('#div_arbol_usuario').html('&nbsp;');
                    // $('#div_arbol_usuario_dtlle').html('&nbsp;');

                    // Recarga la tabla de permisos por usuarios
                    verOpciones('wuse_pfls_ser', 'div_arbol_usuario', 'load_arbol_users', 'div_arbol_usuario_dtlle');

                    // $('#div_arbol_ccostos').html('&nbsp;');
                    // $('#div_arbol_ccostos_dtlle').html('&nbsp;');

                    // Recarga la tabla de permisos por centro de costos
                    verOpciones('wccostos_pfls_ser', 'div_arbol_ccostos', 'load_arbol_ccostos', 'div_arbol_ccostos_dtlle');

                    // $('#div_arbol_cargos').html('&nbsp;');
                    // $('#div_arbol_cargos_dtlle').html('&nbsp;');

                    // Recarga la tabla de permisos por carcos
                    verOpciones('wcccargo_pfls_ser', 'div_arbol_cargos', 'load_arbol_ccostos', 'div_arbol_cargos_dtlle');
                    // alert(data.mensaje);
                }
            },
            "json"
        );
    }

    /**
     * Esta función es usada en la función generarDetalle(..) que se encuentra en funciones_talhuma.php
     * Actualiza permisos o nivel de acceso a un menú.
     * @return unknown
     */
    function updateCampo(ele, id_reg)
    {
        var tipo = $('#'+ele).get(0).type;
        if (tipo == 'radio')
        {
            val = $('[name="'+ele+'"]:checked').val();
        }
        else if(tipo == 'checkbox')
        {
            if($("#"+ele).is(':checked'))
            { val = 'on'; }
            else
            { val = 'off'; }
        }
        else
        {
            val = $("#"+ele).val();
        }

        field = $('#'+ele).attr('rel');

        $.post("gestion_perfiles.php",
            {
                wemp_pmla:      $('#wemp_pmla').val(),
                wtema:          $('#wtema').val(),
                temaselect:     $('#temaselect').val(),
                wuse:           $('#wuse').val(),
                id_registro:    id_reg,
                consultaAjax:   '',
                value:          val,
                campo:          field,
                accion:         'update',
                form:           'update_estado'
            }
            ,function(data) {
                if(data.error == 1)
                {
                    if($("#"+ele).is(':checked'))
                    {
                        $("#"+ele).removeAttr('checked');
                    }
                    else
                    {
                        $("#"+ele).attr('checked','checked');
                    }
                    alert(data.mensaje);
                }
                else
                {
                    // alert(data.mensaje); // update Ok.
                }
            },
            "json"
        );
    }


    /**
     * Esta función está referenciada desde la función pintarMenuSeleccion(..) en funciones_talhuma.php
     * y es usada para chequear o deschequear todos los checkbox hijos de un checkbox padre.
     *
     * @return unknown
     */
    function seleccionar(e, padre)
    {
        $('#div_req_menu').hide();
        if($("#"+e).is(':checked'))
        {
            $('[id='+padre+']').attr('checked','checked');
            p = padre.split('_');
            idp = 'padre';
            for (i=1; i < p.length; i++)
            {
                idp = idp+'_'+p[i];
                $('[id='+idp+']').attr('checked','checked');
            }
        }
        else
        {
            $('[id^='+e+']').removeAttr('checked');
        }
    }

    /**
     * Para la sección se adición de centros de costos, cargos o usuarios, esta función se encarga de verificar si el elemento a adicionar a la lista
     * existe ya o no existe, esto para posteriormente tomar la desición de adicionar el elemento porque no existe o por el contrario no hacer nada si ya existe.
     *
     * @return unknown
     */
    function buscarEnLista(obj_seleccionado, contenedor)
    {
        val = $("#"+obj_seleccionado).val();
        encontrado = false;
        $('#'+contenedor+' input[type=checkbox]').each(function() {
            if($(this).val() == val)
            { encontrado = true; return; }
        });
        return encontrado;
    }

    /**
     * Adiciona un nuevo elemento a la lista de seleccionados. (Adiciona centro de costos a la lista de configuración, ó cargos ó usuarios).
     * Si se selecciona la opción todos entonces se remueven los que existan y se adiciona a la lista la opción [Todos].
     *
     * @return unknown
     */
    function addList(id_padre, id_hijo, form)
    {
        $('#div_req_permisos').hide();
        $('#div_req_usuarios').hide();
        val = $("#"+id_padre).val();
        if(val == '*')
        {
            $("#"+id_hijo).find('div').each(function() {
                $(this).remove();
            });
        }

        checks = $("#"+id_hijo).find(':checkbox').length;
        if(checks > 0)
        {
            if(buscarEnLista(id_padre, id_hijo) == true) { $("#"+id_padre+" option[value='']").attr("selected",true); return; }
            if(checks == 1)
            {
                if($("#"+id_hijo).find("[id*=pfls_todos]").is(':checked'))
                {
                    $("#"+id_hijo).find("div").remove(); // si existe seleccionado la opción todos, entonces se remueve todo el contenedor de ese checked.
                    //$("#"+id_padre+" option[value='']").attr("selected",true); return;
                }
            }
        }

        $.post("gestion_perfiles.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
                temaselect  : $('#temaselect').val(),
                accion      : 'load',
                id_padre    : val,
                form        : form
            }
            ,function(data) {
                if(checks == 0 && data != '')
                {
                    $('#div_'+form).remove();
                    // $('#div_'+form).hide();
                }
                $('#'+id_hijo).append(data);
                $("#"+id_padre+" option[value='']").attr("selected",true);
            }
        );
    }

    /**
     * Usada para desencadenar el evento de busqueda de un centro de costo de un cargo o de un usuario al momento de presionar la tecla enter en los campos de búsqueda.
     *
     * @return boolean
     */
    function enterBuscar(ele,hijo,op,form,e)
    {
        tecla = (document.all) ? e.keyCode : e.which;
        if(tecla==13) { $("#"+hijo).focus(); }
        else { return true; }
        return false;
    }

    function cambioImagen(img1, img2)
    {
        $('#'+img1).hide(1000);
        $('#'+img2).show(1000);
    }

    /**
     * Actualiza la lista de opciones (select) de centros de costos, cargos o usuarios, despues de haber digitado algún valor en los campos de búsqueda.
     *
     * @return unknown
     */
    function recargarLista(id_padre, id_hijo, form)
    {
        val = $("#"+id_padre).val();
        if(val != '*')
        {
            $('#'+id_hijo).load(
                    "gestion_perfiles.php",
                    {
                        consultaAjax:   '',
                        wemp_pmla:  $("#wemp_pmla").val(),
                        wtema:      $("#wtema").val(),
                        temaselect: $('#temaselect').val(),
                        accion:     'load',
                        id_padre:   val,
                        form:       form
                    });
        }
    }

    /**
     * Esta fucnión elimina un elemento de las listas de configuración de permisos para centros de costos, cargos o usuarios.
     *
     * @param unknown obj_seleccionado  : NO SE ESTÁ USANDO.
     * @param unknown contenedor        : Es el id del div principal que contiene la opción o elemento a eliminar de la lista de configuración de centro de costos, cargos o usuarios.
     * @param unknown div_msj           : NO SE ESTÁ USANDO.
     * @return unknown
     */
    function desmarcarRemover(obj_seleccionado, contenedor, div_msj)
    {
        $('#'+contenedor).remove();
    }

    /**
     * Según el tema seleccionado, esta función se encarga de cargar las opciones de menú que están asociados a ese tema y actualiza la lista de menús en la interfaz.
     *
     * @return unknown
     */
    function cargarMenu(obj_seleccionado,contenedor_menu)
    {
        $("#wtema").val($("#wgrupotema").val());
        val = $("#"+obj_seleccionado).val();
        txt = $(obj_seleccionado).find("option:selected").text();
        if(val != '')
        {
            $('#div_req_wgrupotema').hide();
            $.post("gestion_perfiles.php",
                {
                    consultaAjax: '',
                    wemp_pmla   : $("#wemp_pmla").val(),
                    wtema       : $("#wtema").val(),
                    temaselect  : $('#temaselect').val(),
                    accion      : 'load',
                    id_padre    : val,
                    form        : 'load_menu'
                }
                ,function(data) {
                    if(data.total_menus == 0)
                    {
                        $('#'+contenedor_menu).html('<div id="div_msj_menu_req" class="div_msj_menureq">[?] El tema ['+txt+'] NO tiene opciones de men&uacute; configuradas.</div>');
                    }
                    else
                    {
                        $('#div_req_menu').hide();
                        $('#'+contenedor_menu).html(data.menu);
                    }
                },
                "json"
            );
        }
        else
        {
            $('#'+contenedor_menu).html('<div id="div_msj_menu_req" class="div_msj_menureq">[?] Debe seleccionar un Grupo tem&aacute;tico.</div>');
        }
    }

    /**
     * Se valída que estén llenos los campos mínimos requeridos para guardar una configuración, p.e. al dar clic en guardar debe haber seleccionado por lo menos
     * un menú de la lista de menús, un tema si tiene habilitada la opción de cambiar de tema, y por lo menos un centro de costos un cargo o un usuario.
     * En cualquier casol, si hace falte uno de los datos requeridos, se mostrará un mensaje informando que los datos aún no están completos.
     *
     * @return unknown
     */
    function validarGuardar(frm)
    {
        /**
            El elemento tipoconf es el encargado de controlar las opcione que se están viendo en un determinado momento en la interfaz,
            por ejemplo puede indicar si se están viendo las opciones para:
                *   Configurar Centros de Costos y Cargos
                *   Configurar Usuarios
                *   Consultar configuraciones
        */
        tipo_guardar = $('#tipoconf').val();/* tipos = acc_pestanas | porusuario | consultaconf */

        $('#accion').val('guardar');
        isOk = true;

        hay_tema    = ($('#wgrupotema').val() != '') ? true: false;
        hay_arbol   = ($("#div_area_menu").find(':checkbox').length > 0) ? true: false;
        hay_arbolChekOK = ($("#div_area_menu").find(':checkbox[checked]').length > 0) ? true: false;

        hay_ccostos = ($("#div_adds_costos").find(':checkbox[checked]').length > 0) ? true: false;
        hay_cargos  = ($("#div_adds_cargos").find(':checkbox[checked]').length > 0) ? true: false;
        hay_users   = ($("#div_adds_users").find(':checkbox[checked]').length > 0) ? true: false;

        switch(tipo_guardar)
        {
            case 'acc_pestanas' :
                        if(!hay_tema || !hay_arbolChekOK)
                        {
                            if(!hay_tema)       { $('#div_req_wgrupotema').show(); }
                            if(!hay_arbolChekOK)      { $('#div_req_menu').show(); }
                            isOk = false;
                        }

                        if(!hay_ccostos && !hay_cargos)
                        {
                            $('#div_req_permisos').show()
                            isOk = false;
                        }
                        break;
            case 'porusuario'   :
                        if(!hay_tema || !hay_arbolChekOK)
                        {
                            if(!hay_tema)       { $('#div_req_wgrupotema').show(); }
                            if(!hay_arbolChekOK)      { $('#div_req_menu').show(); }
                            isOk = false;
                        }

                        if(!hay_users)
                        {
                            $('#div_req_usuarios').show();
                            isOk = false;
                        }
                        break;
        }


        if(!hay_tema || !hay_arbolChekOK)
        {
            if(!hay_tema)       { $('#div_req_wgrupotema').show(); }
            if(!hay_arbolChekOK)      { $('#div_req_menu').show(); }
            isOk = false;
        }

        if(!hay_ccostos && !hay_cargos && !hay_users)
        { $('#div_req_permisos').show(); isOk = false; }

        return isOk;
    }

    /**
     * Muestra las opciones de menú que tiene configurado un elemento seleccionado en el select de usuarios, centros de costos o cargos.
     * id_padre : Es el id del elemento seleccionado (código de usuario, centro costo o cargo).
     * id_hijo  : Es el id del div donde se va a pintar las opciones de menú que se pueden ver según la opción seleccionada.
     * div_dtll : Es el id del div donde se va a pintar todos los perfiles que cumplen con esta misma configuración a además muestra las opciones de editar permisos o eliminar la opción
     *            para que ya no pueda tener acceso a ese menú.
     *
     * @return unknown
     */
    function verOpciones(id_padre, id_hijo, form, div_dtll)
    {
        $('#'+id_hijo).html('&nbsp;');
        val = $("#"+id_padre).val();
        $.post("gestion_perfiles.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
                temaselect  : $('#temaselect').val(),
                accion      : 'load',
                id_padre    : val,
                form        : form
            }
            ,function(data) {
                $('#'+id_hijo).html(data.menu);
                $('#'+div_dtll).html(data.menus_detalle);
            },
            "json"
        );
    }

    /**
     * Si en la configuración de permisos por centros de costos y cargos hay checkbox seleccionados, esta función se encarga de desmarcarlos a todos.
     *
     * @return unknown
     */
    function resetSeccionPestanas(menuform)
    {
        resetearCampos();
        $('#div_req_wgrupotema').hide();
        $('#div_req_menu').hide();
        $('#div_req_permisos').hide();
        $('#div_req_usuarios').hide();
        $('#div_consulta_perfiles').hide(); // ocultar area consulta de perfiles

        $('#div_contenedor_config').show(); // mostrar area de configuracion
        $('div[id^=div_tit_habilitar]').hide(); // quita todos los titulos activos
        $('#div_tit_habilitar_tabs').show(); // muestra el subtitulo para configurar pestañas por cargo y centro costo
        $('#div_tit_habilitar_tabs_ppal').show(); // muestra el titulo para configurar pestañas por cargo y centro costo
        $('#div_op_permisos').hide(); // quita seleccion de permisos
        $('#div_confusers').hide(); // quita seleccion de usuarios

        // Diferencia opción superior seleccionada
        $('td[id^=menform_]').css({'background-color': '', 'color': '', 'border-bottom': ''});
        $('td[id='+menuform+']').css({'background-color': '#F2F2F2', 'color': '#333333', 'border-bottom': '1px solid #666666'});
        $('#tipoconf').val('acc_pestanas');
    }

    /**
     * Si en la configuración de permisos por usuarios o por centros de costos y cargos hay checkbox seleccionados, esta función se encarga de desmarcarlos a todos.
     *
     * @return unknown
     */
    function resetSeccionPorUsuario(menuform)
    {
        resetearCampos();
        $('#div_req_wgrupotema').hide();
        $('#div_req_menu').hide();
        $('#div_req_permisos').hide();
        $('#div_req_usuarios').hide();
        $('#div_consulta_perfiles').hide(); // ocultar area consulta de perfiles

        $('#div_contenedor_config').show(); // mostrar area de configuracion
        $('div[id^=div_tit_habilitar]').hide();
        $('#div_tit_habilitar_permisos_use').show();
        $('#div_tit_habilitar_permisos_use_ppal').show();

        $('#div_op_permisos').show(); // mostrar seleccion de permisos
        $('#div_confusers').show(); // mostrar seleccion de usuarios

        // Diferencia opción superior seleccionada
        $('td[id^=menform_]').css({'background-color': '', 'color': '', 'border-bottom': ''});
        $('td[id='+menuform+']').css({'background-color': '#F2F2F2', 'color': '#333333', 'border-bottom': '1px solid #666666'});
        $('#tipoconf').val('porusuario');
    }

    /**
     * Si en la configuración de permisos por usuarios o por centros de costos y cargos hay checkbox seleccionados, esta función se encarga de desmarcarlos a todos.
     * y cambia la interfaz a modo vista y consulta de permisos.
     * @return unknown
     */
    function cambiarAConsultar(menuform)
    {
        resetSeccionPestanas('menform_acc');
        resetSeccionPorUsuario('menform_use');
        resetearCampos();
        $('#div_contenedor_config').hide(); // ocultar area de configuracion
        $('div[id^=div_tit_habilitar]').hide();
        $('#div_tit_habilitar_ver_perfil_ppal').show();
        $('#div_consulta_perfiles').show(); // mostrar area consulta de perfiles

        // Diferencia opción superior seleccionada
        $('td[id^=menform_]').css({'background-color': '', 'color': '', 'border-bottom': ''});
        $('td[id='+menuform+']').css({'background-color': '#F2F2F2', 'color': '#333333', 'border-bottom': '1px solid #666666'});
        $('#tipoconf').val('consultaconf');
    }

    /**
     * Valída que si hay algún checkbox seleccionado y se va a cambiar a otro modo de vista (configuración de centos de costos o por usuarios),
     * esta función hace dicha verificación e informa al usuario que si cambía de modo se pederán los datos que seleccionados que no se han guardados.
     *
     * @return unknown
     */
    function verOtrosDatos(menuform,tipo)
    {
        hay_arbol   = ($("#div_area_menu").find(':checkbox').length > 0) ? true: false;
        hay_arbolChekOK = ($("#div_area_menu").find(':checkbox[checked]').length > 0) ? true: false;

        hay_ccostos = ($("#div_adds_costos").find(':checkbox[checked]').length > 0) ? true: false;
        hay_cargos  = ($("#div_adds_cargos").find(':checkbox[checked]').length > 0) ? true: false;
        hay_users   = ($("#div_adds_users").find(':checkbox[checked]').length > 0) ? true: false;

        mes_confirm = "Hay campos seleccionados y no se han guardado, quiere continuar de todas formas?";

        switch(tipo)
        {
            case 'acc_pestanas' :
                        cambiar = true;
                        if(hay_ccostos || hay_cargos || hay_arbolChekOK) { cambiar = false;}

                        if(!cambiar)
                        {
                            if(confirm(mes_confirm))
                            { resetSeccionPestanas(menuform); }
                            else
                            { return false; }
                        }
                        else {
                            resetSeccionPestanas(menuform);
                        }
                        break;
            case 'porusuario'   :
                        cambiar = true;
                        if(hay_ccostos || hay_cargos || hay_arbolChekOK || hay_users) { cambiar = false;}

                        if(!cambiar)
                        {
                            if(confirm(mes_confirm))
                            { resetSeccionPorUsuario(menuform); }
                            else
                            { return false; }
                        }
                        else {
                            resetSeccionPorUsuario(menuform);
                        }
                        break;
            case 'consultaconf' :

                        cambiar = true;
                        if(hay_ccostos || hay_cargos || hay_arbolChekOK || hay_users) { cambiar = false;}

                        if(!cambiar)
                        {
                            if(confirm(mes_confirm))
                            { cambiarAConsultar(menuform); }
                            else
                            { return false; }
                        }
                        else {
                            cambiarAConsultar(menuform);
                        }

                        break;
        }
    }

    /**
     * Esta función se encarga de reordenar las opciones que corresponden a indicadores, generalmente usado en el Sistema de información estadística generencial.
     * Es usada en la función pintarMenuSeleccion() en el include que se hace a funciones_talhuma.php
     *
     * ele              : (this) generalmente de un elemento tipo radio
     * id_opcion_padre  : Es el id del menú u opción contenedora y que se puede chequear.
     * div_afectado     : Es el id del div que contiene las subobciones que se deben reordenar deacuerdo a la opción de agrupación seleccionada en 'ele'.
     * id_check_padre   : Es el id del del checkbox o menú principal al cual pertenecen los indicadores.
     *
     * @return unknown
     */
    function reOrdenar(ele, id_opcion_padre, div_afectado, id_check_padre)
    {
        var id_grp = $(ele).val();
        // alert(id_grp+" | "+id_opcion_padre+' | '+div_afectado+' | '+id_check_padre);
        $.post("gestion_perfiles.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
                temaselect  : $('#temaselect').val(),
                accion      : 'load',
                id_opcion_padre : id_opcion_padre,
                tabla_sub_grp   : id_grp,
                id_check_padre  : id_check_padre,
                form        : 'agrupar_indicadores'
            }
            ,function(data) {
                $('#'+div_afectado).html(data.menu);
            },
            "json"
        );
    }

    /**
     * Esta función se encarga de seleccionar checkbox que tienen el mismo id, esta situación se presenta para el caso de seleccionar opciones que son tipo indicadores
     * para el Información estadística y gerencial.
     *
     * ele : (this) elemento sobre el que se hace clic.
     *
     * @return unknown
     */
    function seleccionIguales(ele)
    {
        var idck = ele.id;
        if($(ele).is(':checked'))
        {
            $(":checkbox[id="+idck+"]").attr('checked','checked');
        }
        else
        {
            $("[id="+idck+"]").removeAttr('checked');
        }
    }

    /**
     * Esta función se encarga de limpiar las listas de configuración en el modo de configuración de centros de costos y cargos y en el modo de configuración de usuario,
     * es decir, si se ha seleccionado algún centro de costos o cargo o usuario en las listas de configuraión, esta función se encarga de limpiar esas listas de configuración.
     *
     * @return unknown
     */
    function resetearCampos()
    {
        // -- reinicia todos los divs de configuración
        $("#div_adds_users").html(  "<div id='div_load_chk_users' align='center' class='fila2' style='font-weight:bold;margin-top:35px; padding-top:10px; padding-bottom:15px;'>"
                                   + "   [?] No se ha seleccionado ning&uacute;n Usuario"
                                   +"</div>");

        $("#div_adds_costos").html(  "<div id='div_load_chk_costo' align='center' class='fila2' style='font-weight:bold;margin-top:35px; padding-top:10px; padding-bottom:15px;'>"
                                    +"   [?] No se ha seleccionado ning&uacute;n centro de costo"
                                    +"</div>");

        $("#div_adds_cargos").html(  "<div id='div_load_chk_cargo' align='center' class='fila2' style='font-weight:bold;margin-top:35px; padding-top:10px; padding-bottom:15px;'>"
                                    +"   [?] No se ha seleccionado ning&uacute;n cargo"
                                    +"</div>");

        $("#div_req_wgrupotema").hide();
        $("#div_req_menu").hide();
        $("#div_req_permisos").hide();
        $("#div_req_usuarios").hide();

        $('#div_area_menu :checkbox').removeAttr('checked');
        // --
    }

    function verSeccionGestionMenus(id){
        $("#"+id).toggle("normal");
    }

</script>

<style type="text/css">
.borderDivTop {
    border-top: 2px solid #999999;
    padding: 5px;
    text-align: left;
}
.borderDiv {
    border: 2px solid #999999;
    padding: 5px;
    text-align: left;
    background-color: #ffffff;
}
.borderDiv2 {
        border: 2px solid #2A5DB0;
        padding: 5px;
        text-align:left;
    }
.border_ppal{
    border: 2px solid #999999;
}
.border_ppal_left{
    border-right: 2px solid #999999;
}
.parrafo1 {
    background-color: #666666;
    font-family: verdana;
    font-size: 10pt;
    font-weight: bold;
    text-align: left;
    color:#FFFFFF;
}
.heightList {
    height: 120px;
    width:500px;
    overflow:scroll;
}
.div_msj_menureq {
    font-family: verdana;
    font-size: 11pt;
    font-weight: bold;
    text-align: center;
    color: orange;
}
.msjReq {
    font-family: verdana;
    display:none;
    color:orange;
    font-weight:bold;
    font-size:8pt;
}
.tablaDetalle {
    font-family: verdana;
    font-size:8pt;
}
.menuFomulario {
    cursor:pointer;
}
.msj_explicativo {
    background-color: #E8EEF7;
    text-align: justify;
    font-family:Arial, Helvetica, sans-serif;
    font-size: 9pt;
}
</style>
</head>
<body>
<?php
$wactualiz = "Abril 17 de 2013";
encabezado("GESTIÓN DE PERFILES", $wactualiz, "clinica");
?>
<table align="center">
    <tr>
        <td>
            <div align="center" style="width:1050px">
                <table style="width:1045px;" class="border_ppal">
                    <tr>
                        <td>
                            <form name="conftree" method="post" id="conftree" onSubmit="return validarGuardar(this);">
                            <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="<?php echo $wemp_pmla; ?>" />
                            <input type="hidden" id="wtema" name="wtema" value="<?php echo $wtema; ?>" />
                            <input type="hidden" id="temaselect" name="temaselect" value="<?php echo $temaselect; ?>" />
                            <input type="hidden" id="tipoconf" name="tipoconf" value="acc_pestanas" />
                            <table align="center" border="0" cellspacing="4" cellpadding="2">
                                <tr class="encabezadoTabla">
                                    <td id="menform_acc" class="menuFomulario" style="background-color:#F2F2F2;color:#333333;border-bottom: 1px solid #666666;" onClick="verOtrosDatos('menform_acc','acc_pestanas')">
                                        Configurar Centros de Costos y Cargos
                                    </td>
                                    <td id="menform_use" class="menuFomulario" onClick="verOtrosDatos('menform_use','porusuario')">
                                        Configurar Usuarios
                                    </td>
                                    <td id="menform_cons" class="menuFomulario" onClick="verOtrosDatos('menform_cons','consultaconf')">
                                        Consultar configuraciones
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <div style="background-color: #2A5DB0;">
                                <table align="center" border="0" cellspacing="0" cellpadding="0">
                                    <tr class="encabezadoTabla" >
                                        <td colspan="2" align="center" class="" >
                                            <div id="div_tit_habilitar_tabs_ppal" style="text-align:center; font-size:13pt;">
                                                GENERAR ACCESO A PESTA&Ntilde;AS<br />
                                            </div>
                                            <div id="div_tit_habilitar_permisos_use_ppal" class="" style="text-align:center; font-size:13pt; display:none;">
                                                CONFIGURAR PERMISOS A USUARIOS<br />
                                            </div>
                                            <div id="div_tit_habilitar_ver_perfil_ppal" class="" style="text-align:center; font-size:13pt; display:none;">
                                                VER PERFILES CREADOS<br />
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <br />
                            <div id="div_contenedor_config" >
                            <table align="center" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width:300px;" valign="top" class="">
                                        <table border="0" cellspacing="0" cellpadding="0" style="width:300px;">
                                            <tr>
                                                <td>
                                                    <div>
                                                        <br />
                                                        <br />
                                                        <div class="encabezadoTabla" id="" style="text-align:center; font-size:12pt;">
                                                            Men&uacute;s disponibles
                                                        </div>
                                                        <br />
                                                        <div style="background-color: #F2F2F2; margin-right:5px;" id="div_area_menu">
                                                            <?php
                                                                if($temaselect == 'on')
                                                                {
                                                            ?>
                                                                <div id="div_msj_menu_req" class="div_msj_menureq">[?] Debe seleccionar un Grupo tem&aacute;tico.</div>
                                                            <?php
                                                                }
                                                                else
                                                                {
                                                                    $menus_sin_ubicar = array();
                                                                    $arbol_menu = crearArbolMenus($wemp_pmla, $conex, $wtema, $menus_sin_ubicar);
                                                                    $div = '';
                                                                    $menu = pintarMenuSeleccion($conex, $arbol_menu['menus_cod'],$arbol_menu['menus_info'], $div, 'checks');
                                                                    echo $menu;
                                                                }
                                                            ?>
                                                        </div>
                                                        <br />
                                                        <br />
                                                        <div id="div_guardar" class="borderDivTop">
                                                            <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                                <tr>
                                                                    <td>
                                                                        <div id="div_mensajes_req" class="" style="display:block;text-align:left;background-color: #F2F2F2;">
                                                                            <div id='div_req_wgrupotema' class='msjReq'>&nbsp;[?] Seccionar Grupo tem&aacute;tico.</div>
                                                                            <div id='div_req_menu' class='msjReq'>&nbsp;[?] Seleccione men&uacute; disponible.</div>
                                                                            <div id='div_req_permisos' class='msjReq'>&nbsp;[?] Falta centro costo ó cargo.</div>
                                                                            <div id='div_req_usuarios' class='msjReq'>&nbsp;[?] Falta seleccionar usuario.</div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br />
                                                            <div id="div_op_permisos" style="display:none;">
                                                                <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                                    <tr>
                                                                        <td align="left" class="encabezadoTabla">&nbsp;Permisos para Consultar</td>
                                                                        <td class="fila1">&nbsp;<input type="radio" checked="checked" id="permisosConsul" name="permisos" value="consultar" /></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="left" class="encabezadoTabla">&nbsp;Permisos para Modificar</td>
                                                                        <td class="fila2">&nbsp;<input type="radio" id="permisosActualiz" name="permisos"  value="modificar" /></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td align="left" class="encabezadoTabla">&nbsp;Es administrador</td>
                                                                        <td class="fila1">&nbsp;<input type="checkbox" id="esadministrador" name="esadministrador" value="on" /></td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                                <tr>
                                                                    <td colspan="2" align="center" class="fila2"><input type="submit" value="Guardar configuración" id="enviar" /><input type="hidden" id="accion" name="accion" value="" /></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td valign="top" align="left" style="width:745px;">
                                        <br />
                                        <br />
                                        <table align="center" border="0" cellspacing="0" cellpadding="0" style="width:600px;">
                                            <tr>
                                                <td class="encabezadoTabla" style="width:200px;">Grupo tem&aacute;tico</td>
                                                <td class="fila1" style="width:500px;">&nbsp;
                                                    <?php
                                                        if($temaselect == 'on')
                                                        {
                                                    ?>
                                                        <select id="wgrupotema"  name="wgrupotema" onChange="cargarMenu('wgrupotema','div_area_menu');">
                                                            <option value="" selected="selected">Seleccione..</option>
                                                            <?php
                                                                //$cont = 0;
                                                                foreach($temasList as $key => $value)
                                                                {
                                                                    //$ck = ($cont == 0) ? 'selected="selected"': '';
                                                                    echo "<option value='$key' >$value</option>";
                                                                    //$cont++;
                                                                }
                                                            ?>
                                                        </select>
                                                    <?php
                                                        }
                                                        else
                                                        {
                                                            echo utf8_encode($temasList[$wtema]);
                                                            echo '<input type="hidden" id="wgrupotema" name="wgrupotema" value="'.$wtema.'" />';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        </table>
                                        <br />
                                        <table cellpadding="0" cellspacing="0" border="0" align="center">
                                            <tr>
                                                <td>
                                                    <div id="div_tit_habilitar_tabs">
                                                        <table cellpadding="0" cellspacing="0" border="0" align="center" style="width: 695px;" >
                                                        <tr>
                                                            <td align="center" class="msj_explicativo">
                                                            <div align="justify" style="font-weight:bold;">Configure qu&eacute; centros de costos o qu&eacute; cargos pueden ver los Men&uacute;s dispon&iacute;bles. Con estas opciones puede habilitar masivamente el acceso a las opciones de men&uacute; seleccionadas en el panel izquierdo "Men&uacute;s disponibles".</div>
                                                            <br />
                                                            <li> Si Selecciona solo centros de costos &oacute; solo cargos, se est&aacute; indicando que solo esos centros de costos o cargos tendrán habilitadas las opciones de men&uacute;s seleccionadas.</li>
                                                            <br />
                                                            <li> Si selecciona centros de costos y cargos se est&aacute; indicando que los cargos que est&eacute;n dentro de esos centros de costos tendr&aacute;n habilitadas las opciones de men&uacute;s seleccionadas.</li>
                                                            <br />
                                                            </td>
                                                        </tr>
                                                        </table>
                                                    </div>
                                                    <div id="div_tit_habilitar_permisos_use" style="display:none;">
                                                        <table cellpadding="0" cellspacing="0" border="0" align="center" style="width: 695px;" >
                                                        <tr>
                                                            <td align="center" class="msj_explicativo">
                                                            <div align="center" style="font-weight:bold;">Configure qui&eacute;n puede ver informaci&oacute;n de otros centros de costos o cargos de los Men&uacute;s dispon&iacute;bles.</div>
                                                            <br />
                                                            <li> Si al momento de guardar la configuraci&oacute;n hay uno o varios usuarios a configurar y NO hay Centro de costo seleccionado ni cargos seleccionados, se habilitar&aacute; el acceso a las pesta&ntilde;as seleccionadas sin permiso para ver lo de otros usuarios.</li>
                                                            <br />
                                                            <li> Si al momento de guardar la configuraci&oacute;n hay uno o varios usuarios a configurar y SI hay Centro de costo seleccionado &oacute; cargos seleccionados, se est&aacute; indicando que el usuario o usuarios seleccionados puedes ver las pesta&ntilde;as o men&uacute;s seleccionados adem&aacute;s con la opci&oacute; de poder ver la informaci&oacute;n de los centos de costos o cargos seleccionados en esos men&uacute;s.</li>
                                                            <br />
                                                            <li> Si se marca la casilla "Es administrador" indica que esto tiene mayor importancia que lo seleccionado en centro de costo o cargo, a&uacute;n cuando no se seleccione ver todos los centos de costos o cargos, si esta seleccionado "Es administrador" entonces tiene permiso para ver la informaci&oacute;n de todos.</li>
                                                            <br />
                                                            </td>
                                                        </tr>
                                                        </table>
                                                    </div>
                                                    <br />
                                                    <div id="div_dspl_confnuevo">
                                                        <div id="div_confusers" class="borderDivTop fila2" style="display:none;">
                                                            <div class="parrafo1">&nbsp;Usuarios a configurar</div>
                                                            <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                                <tr>
                                                                    <td class="encabezadoTabla" style="width:235px;">&nbsp;Buscar usuario:</td>
                                                                    <td class="fila2" style="width:450px;" align="center">&nbsp;
                                                                        <img title='Busque el nombre o parte del nombre del centro de costo' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
                                                                        <input id="wnomuse_pfls" name="wnomuse_pfls" value="" size="60" onkeypress='return enterBuscar("wnomuse_pfls","wuse_pfls","user","load_users",event);' onfocus='cambioImagen("cusel","cuload");' onBlur='recargarLista("wnomuse_pfls","wuse_pfls","load_users"); cambioImagen("cuload","cusel");'/>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="encabezadoTabla">&nbsp;Seleccionar usuario:</td>
                                                                    <td class="fila1">
                                                                        <table border='0' cellspacing='0' cellpadding='0' >
                                                                            <tr>
                                                                                <td>
                                                                                    <div id='cusel'><img title='Seleccione un usuario' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>
                                                                                    <div id='cuload' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
                                                                                </td>
                                                                                <td>
                                                                                    <select style="width:430px;" id="wuse_pfls" name="wuse_pfls" onChange="addList('wuse_pfls','div_adds_users','load_chk_users');">
                                                                                        <?php
                                                                                            echo $usuariosList;
                                                                                            // foreach($usuariosList as $key => $value)
                                                                                            // {
                                                                                                // echo "<option value='$key' >$value</option>";
                                                                                            // }
                                                                                        ?>
                                                                                    </select>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br />
                                                            <table border="0" cellspacing="0" cellpadding="0" align="center" class="borderDiv">
                                                            <tr>
                                                            <td class="" >
                                                            <div class="heightList" id="div_adds_users">
                                                                <div id="div_load_chk_users" align='center' class="fila2" style="font-weight:bold;margin-top:35px; padding-top:10px; padding-bottom:15px;">
                                                                        [?] No se ha seleccionado ning&uacute;n Usuario
                                                                </div>
                                                            </div>
                                                            </td>
                                                            </tr>
                                                            </table>
                                                        </div>
                                                        <br />
                                                        <div id="div_confcentrocostos" class="borderDivTop fila2">
                                                            <div class="parrafo1">&nbsp;Centro de costos a configurar</div>
                                                            <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                                <tr>
                                                                    <td class="encabezadoTabla" style="width:235px;">&nbsp;Buscar centro de costos:</td>
                                                                    <td class="fila2" style="width:450px;" align="center">&nbsp;
                                                                        <img title='Busque el nombre o parte del nombre del centro de costo' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
                                                                        <input id="wnomccosto" name="wnomccosto" value="" size="60" onkeypress='return enterBuscar("wnomccosto","wccostos_pfls","costos","load_costo",event);' onfocus='cambioImagen("ccsel","ccload");' onBlur='recargarLista("wnomccosto","wccostos_pfls","load_costo"); cambioImagen("ccload","ccsel");' />
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="encabezadoTabla">&nbsp;Seleccionar centro de costos:</td>
                                                                    <td class="fila1">
                                                                        <table border='0' cellspacing='0' cellpadding='0' >
                                                                            <tr>
                                                                                <td>
                                                                                    <div id='ccsel'><img title='Seleccione un centro de costos' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>
                                                                                    <div id='ccload' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
                                                                                </td>
                                                                                <td>
                                                                                    <select style="width:430px;" id="wccostos_pfls" name="wccostos_pfls" onChange="addList('wccostos_pfls','div_adds_costos','load_chk_costo');">
                                                                                        <?php
                                                                                            echo $centro_costos;
                                                                                            // foreach($centro_costos as $key => $value)
                                                                                            // {
                                                                                                // echo "<option value='$key' >$value</option>";
                                                                                            // }
                                                                                        ?>
                                                                                    </select>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br />
                                                                <table border="0" cellspacing="0" cellpadding="0" align="center" class="borderDiv">
                                                                <tr>
                                                                <td class="" >
                                                                <div class="heightList" id="div_adds_costos">
                                                                    <div id="div_load_chk_costo" align='center' class="fila2" style="font-weight:bold;margin-top:35px; padding-top:10px; padding-bottom:15px;">
                                                                        [?] No se ha seleccionado ning&uacute;n centro de costo
                                                                    </div>
                                                                </div>
                                                                </td>
                                                                </tr>
                                                                </table>
                                                        </div>
                                                        <br />
                                                        <div id="div_confcargos" class="borderDivTop fila2">
                                                            <div class="parrafo1">&nbsp;Cargos a configurar</div>
                                                            <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                                <tr>
                                                                    <td class="encabezadoTabla" style="width:235px;">&nbsp;Buscar cargo:</td>
                                                                    <td class="fila2" style="width:450px;" align="center">&nbsp;
                                                                        <img title='Busque el nombre o parte del nombre del centro de costo' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
                                                                        <input id="wnomcargo_pfls" name="wnomcargo_pfls" value="" size="60" onkeypress='return enterBuscar("wnomcargo_pfls","wcccargo_pfls","cargos","load_cargo",event);' onfocus='cambioImagen("ccgsel","ccgload");' onBlur='recargarLista("wnomcargo_pfls","wcccargo_pfls","load_cargo"); cambioImagen("ccgload","ccgsel");'/>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="encabezadoTabla">&nbsp;Seleccionar cargo:</td>
                                                                    <td class="fila1">
                                                                        <table border='0' cellspacing='0' cellpadding='0' >
                                                                            <tr>
                                                                                <td>
                                                                                    <div id='ccgsel'><img title='Seleccione un centro de costos' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>
                                                                                    <div id='ccgload' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
                                                                                </td>
                                                                                <td>
                                                                                    <select style="width:430px;" id="wcccargo_pfls" name="wcccargo_pfls" onChange="addList('wcccargo_pfls','div_adds_cargos','load_chk_cargo');">
                                                                                        <?php
                                                                                            echo $cargos;
                                                                                            // foreach($cargos as $key => $value)
                                                                                            // {
                                                                                                // echo "<option value='$key' >$value</option>";
                                                                                            // }
                                                                                        ?>
                                                                                    </select>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <br />
                                                                <table border="0" cellspacing="0" cellpadding="0" align="center" class="borderDiv">
                                                                <tr>
                                                                <td class="" >
                                                                <div class="heightList" id="div_adds_cargos">
                                                                    <div id="div_load_chk_cargo" align='center' class="fila2" style="font-weight:bold;margin-top:35px; padding-top:10px; padding-bottom:15px;">
                                                                        [?] No se ha seleccionado ning&uacute;n cargo
                                                                    </div>
                                                                </div>
                                                                </td>
                                                                </tr>
                                                                </table>
                                                        </div>
                                                        <br />
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            </div>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <br />
                            <div id="div_consulta_perfiles" style="display:none;">
                            <table border="0" cellspacing="0" cellpadding="0" align="center">
                                <tr>
                                    <td>
                                        <div id="ref_usuarios" align="left">
                                            <table style="width:1013px;" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="tit_seccion fila2">
                                                        <a href="#null" onClick="javascript:verSeccionGestionMenus('div_consulta_usuarios');">
                                                            <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;Consultar por Usuario
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                                            <tr>
                                                <td>
                                                    <div align="center" id="div_consulta_usuarios" class="borderDiv2">
                                                        <!-- SECCION PARA BUSCAR ARBOLES POR CCO O CCG O USUARIO -->
                                                        <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                            <tr>
                                                                <td colspan="2" class="encabezadoTabla" align="center">Ver men&uacute;s por usuarios</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="encabezadoTabla">&nbsp;Buscar nombre:</td>
                                                                <td class="fila2">&nbsp;
                                                                    <img title='Busque el nombre o parte del nombre del centro de costo' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
                                                                    <input id="wfindnomuse" name="wfindnomuse" value="" size="60" onkeypress='return enterBuscar("wfindnomuse","wuse_pfls_ser","user","load_users",event);' onfocus='cambioImagen("cuselSearch","culoadSearch");' onBlur='recargarLista("wfindnomuse","wuse_pfls_ser","load_users"); cambioImagen("culoadSearch","cuselSearch");' />
                                                                </td>
                                                                </tr>
                                                            <tr>
                                                                <td class="encabezadoTabla">&nbsp;Seleccionar para buscar:</td>
                                                                <td class="fila2">
                                                                    <table border='0' cellspacing='0' cellpadding='0' >
                                                                        <tr>
                                                                            <td>
                                                                                <div id='cuselSearch'><img title='Seleccione un usuario' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>
                                                                                <div id='culoadSearch' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
                                                                            </td>
                                                                            <td>
                                                                                <select style="width:430px;" id="wuse_pfls_ser" name="wuse_pfls_ser" onChange="verOpciones('wuse_pfls_ser','div_arbol_usuario','load_arbol_users','div_arbol_usuario_dtlle');">
                                                                                    <?php
                                                                                        echo $usuariosList;
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <br />
                                                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                                                            <tr>
                                                                <td align="center">
                                                                    <table border="0" cellspacing="0" cellpadding="0" align="center">
                                                                        <tr>
                                                                            <td>
                                                                                <div id="div_arbol_usuario" style="text-align:justify">&nbsp;</div>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                    <div id="div_arbol_usuario_dtlle" style="width:1000px;">&nbsp;</div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <br />
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <br />
                                        <div id="ref_centro_costos" align="left">
                                            <table style="width:1013px" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="tit_seccion fila2">
                                                        <a href="#null" onClick="javascript:verSeccionGestionMenus('div_consulta_ccostos');">
                                                            <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;Consultar por Centro de costos
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                                            <tr>
                                                <td>
                                                    <div align="center" id="div_consulta_ccostos" class="borderDiv2">
                                                        <!-- SECCION PARA BUSCAR ARBOLES POR CCO O CCG O USUARIO -->
                                                        <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                            <tr>
                                                                <td colspan="2" class="encabezadoTabla" align="center">Ver men&uacute;s por centros de costos</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="encabezadoTabla">&nbsp;Buscar nombre:</td>
                                                                <td class="fila2">&nbsp;
                                                                    <img title='Busque el nombre o parte del nombre del centro de costo' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
                                                                    <input id="wfindnomcco" name="wfindnomcco" value="" size="60" onkeypress='return enterBuscar("wfindnomcco","wccostos_pfls_ser","costos","load_costo",event);' onfocus='cambioImagen("ccoselSearch","ccoloadSearch");' onBlur='recargarLista("wfindnomcco","wccostos_pfls_ser","load_costo"); cambioImagen("ccoloadSearch","ccoselSearch");' />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="encabezadoTabla">&nbsp;Seleccionar para buscar:</td>
                                                                <td class="fila2">
                                                                    <table border='0' cellspacing='0' cellpadding='0' >
                                                                        <tr>
                                                                            <td>
                                                                                <div id='ccoselSearch'><img title='Seleccione un usuario' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>
                                                                                <div id='ccoloadSearch' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
                                                                            </td>
                                                                            <td>
                                                                                <select style="width:430px;" id="wccostos_pfls_ser" name="wccostos_pfls_ser" onChange="verOpciones('wccostos_pfls_ser','div_arbol_ccostos','load_arbol_ccostos','div_arbol_ccostos_dtlle');">
                                                                                    <?php
                                                                                        echo $centro_costos;
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <br />
                                                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                                                            <tr>
                                                                <td align="center">
                                                                    <div id="div_arbol_ccostos" style="width:300px; text-align:justify">&nbsp;</div>
                                                                    <div id="div_arbol_ccostos_dtlle" style="width:1000px;">&nbsp;</div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <br />
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <br />
                                        <div id="ref_cargos" align="left">
                                            <table style="width:1013px" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="tit_seccion fila2">
                                                        <a href="#null" onClick="javascript:verSeccionGestionMenus('div_consulta_cargos');">
                                                            <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;Consultar por Cargos
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                                            <tr>
                                                <td>
                                                    <div align="center" id="div_consulta_cargos" class="borderDiv2">
                                                        <!-- SECCION PARA BUSCAR ARBOLES POR CCO O CCG O USUARIO -->
                                                        <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                            <tr>
                                                                <td colspan="2" class="encabezadoTabla" align="center">Ver men&uacute;s por cargos</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="encabezadoTabla">&nbsp;Buscar nombre:</td>
                                                                <td class="fila2">&nbsp;
                                                                    <img title='Busque el nombre o parte del nombre del centro de costo' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
                                                                    <input id="wfindnomccg" name="wfindnomccg" value="" size="60" onkeypress='return enterBuscar("wfindnomccg","wcccargo_pfls_ser","costos","load_cargo",event);' onfocus='cambioImagen("ccgselSearch","ccgloadSearch");' onBlur='recargarLista("wfindnomccg","wcccargo_pfls_ser","load_cargo"); cambioImagen("ccgloadSearch","ccgselSearch");' />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="encabezadoTabla">&nbsp;Seleccionar para buscar:</td>
                                                                <td class="fila2">
                                                                    <table border='0' cellspacing='0' cellpadding='0' >
                                                                        <tr>
                                                                            <td>
                                                                                <div id='ccgselSearch'><img title='Seleccione un usuario' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>
                                                                                <div id='ccgloadSearch' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
                                                                            </td>
                                                                            <td>
                                                                                <select style="width:430px;" id="wcccargo_pfls_ser" name="wcccargo_pfls_ser" onChange="verOpciones('wcccargo_pfls_ser','div_arbol_cargos','load_arbol_cargos','div_arbol_cargos_dtlle');">
                                                                                    <?php
                                                                                        echo $cargos;
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <br />
                                                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                                                            <tr>
                                                                <td align="center">
                                                                    <div id="div_arbol_cargos" style="width:300px; text-align:justify">&nbsp;</div>
                                                                    <div id="div_arbol_cargos_dtlle" style="width:1000px;">&nbsp;</div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <br />
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <br />
                                        <div id="ref_cdmenu" align="left">
                                            <table style="width:1013px" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td class="tit_seccion fila2">
                                                        <a href="#null" onClick="javascript:verSeccionGestionMenus('div_consulta_cdmenu');">
                                                            <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;Consultar men&uacute;s
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                                            <tr>
                                                <td>
                                                    <div align="center" id="div_consulta_cdmenu" class="borderDiv2">
                                                        <!-- SECCION PARA BUSCAR MENÚS Y VER PARA QUIÉNES ESTÁN DISPONIBLES -->
                                                        <table border="0" cellspacing="1px" cellpadding="0" align="center">
                                                            <tr>
                                                                <td colspan="2" class="encabezadoTabla" align="center">Men&uacute;s</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="encabezadoTabla">&nbsp;Buscar nombre:</td>
                                                                <td class="fila2">&nbsp;
                                                                    <img title='Busque el nombre o parte del nombre del men&uacute;' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
                                                                    <input id="wfindnomcdmenu" name="wfindnomcdmenu" value="" size="60" onkeypress='return enterBuscar("wfindnomcdmenu","wcdmenu_pfls_ser","cdmenu","load_cdmenu",event);' onfocus='cambioImagen("cdmenuselSearch","cdmenuloadSearch");' onBlur='recargarLista("wfindnomcdmenu","wcdmenu_pfls_ser","load_cdmenu"); cambioImagen("cdmenuloadSearch","cdmenuselSearch");' />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="encabezadoTabla">&nbsp;Seleccionar para buscar:</td>
                                                                <td class="fila2">
                                                                    <table border='0' cellspacing='0' cellpadding='0' >
                                                                        <tr>
                                                                            <td>
                                                                                <div id='cdmenuselSearch'><img title='Seleccione un men&uacute;' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>
                                                                                <div id='cdmenuloadSearch' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>
                                                                            </td>
                                                                            <td>
                                                                                <select style="width:430px;" id="wcdmenu_pfls_ser" name="wcdmenu_pfls_ser" onChange="verOpciones('wcdmenu_pfls_ser','div_arbol_cdmenu','load_arbol_cdmenu','div_arbol_cdmenu_dtlle');">
                                                                                    <?php
                                                                                        echo $menusList;
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <br />
                                                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                                                            <tr>
                                                                <td align="center">
                                                                    <div id="div_arbol_cdmenu" style="width:300px; text-align:justify">&nbsp;</div>
                                                                    <div id="div_arbol_cdmenu_dtlle" style="width:1000px;">&nbsp;</div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <br />
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>
<table align='center'>
    <tr>
        <td align='center' colspan=9><input type='button' value='Cerrar Ventana' onclick='cerrarVentana();'></td>
    </tr>
</table>
</body>
</html>
<?php
}
?>