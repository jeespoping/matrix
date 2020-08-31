<?php
include_once("conex.php");
/**
 Programa       : caracterizacion.php
 Autor          : Edwar Jaramillo
 Fecha creado   : 2012-07-05

 Descripción

 Este script se encarga de recopilar los datos de identificación y caracterización de los empleados, corresponde al grupo de programas
 de gestión de talento humano (base de datos talhuma), en primera instancia recopila datos del sistema de nómina en unix y los inserta
 en la base de datos talhuma en caso de no existir, posteriormente todas las actualizaciones y demas adiciones a la caracterización, serán
 almacenados en la base de datos talhuma (talhuma_000013).
*/

/****************************************************************************************************************
*  MODELO >>  -   GESTIÓN DE ACCESO A BASE DE DATOS - Esta parte del script en PHP es el MODELO de datos


La estructura de este bloque de código de se compone de cuatro partes o tipos de accione

if ($accion == 'add') Adiciona o inserta nueva infirmación

elseif ($accion == 'delete') Elimina registros de una tabla

elseif ($accion == 'update') Actualiza uno o varios campos de una tabla

elseif ($accion == 'load') Carga o reemplaza los datos de un contenedor en el html (p.e. el contenido de un div, select, input, entre otros)

else    al final de todo se encuentra una secuencia de consultas a base de datos que cargan la información a los formularios
        en el momento de abrir este programa.

****************************************************************************************************************/

/****************************************************************************************************************
* REGISTRO DE ACTUALIZACIONES - Poner la actualización más reciente antes que las demás
****************************************************************************************************************/
$VER_ACTUALIZACIONES = false; // activar en TRUE solo en desarrollo, con esta opción en TRUE se verán las modificaciones en pantalla

$wactualizacion[] = array(
                        'fecha' => '(Noviembre 21 de 2019)',
                        'autor' => 'Jessica Madrid MejÃ­a',
                        'descripcion' =>
                        "
							* Se inicializa array correctamente para solucionar error de migraciÃ³n,
                            * En la secciÃ³n de Condiciones de vida del empleado se comenta la pregunta: Rol que desempeÃ±a en la 
							  instituciÃ³n adicional a su cargo y tambiÃ©n se comenta la secciÃ³n completa de preguntas por solicitud 
							  de Eliana FernÃ¡ndez.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Julio 18 de 2016)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            * Paso previo en ventana modal para los usuarios que no estan registrados en talhuma_000013, se piden los datos y se registran por medio de un boton,
                                antes solo insertaba en la tabla pero si no ingresaban datos personales quedaban vancios en la tabla.
                            * Modificación a la hoja de estilos (Azules) de los formularios jquery.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Noviembre 15 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Se detectó que no se estaba solicitando información básica a personas que ingresan a caracterización y que no están en talhuma_000013 (no tienen datos personales),
                                Esto sucedía luego de haber realizado la actualización para concatenar el código de la empresa al código del empleado.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Octubre 10 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Documentacion de código.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Octubre 08 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Se modifica formulario caracterización para que, cuando una persona está logueada pero no existe en talhuma_000013
                                solicite ingresar los nombres, genero, documento de identidad, fecha de nacimiento.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Agosto 08 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Se inactiva la carga del lenguaje a los datatables puesto que ya no se muestran filtros y solo aparece la tabla sola.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Agosto 03 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Al momento de insertar, actualizar, se valída que si el campo en cuestión tiene valor 'on' u 'off' no aplique
                                la función UPP() que pasa todo el texto a mayúsculas, esta validación se hace puesto que puede generar comportamientos
                                no deseados si se guarda 'ON' y luego se hacen validaciones con 'on'.
                            *   Se elimina el div vacío que aparecía siempre al final de módulo de caracterización (generado por el datepicker).
                            *   Se muestra mensaje indicando que un módulo en especial se está cargando.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Agosto 01 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Se crea relación en tabla para escolaridad, cargo, centro de costo, con el fin de filtrar los grados escolares de ley
                                por cargo-centrocosto, se modifica script caracterizacion.php para mostrar listas por separado de grados escolares que no
                                son de ley y mostrar los que si son de ley pero en la sección correspondiente.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Julio 27 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Se adiciona nueva tabla para ingresar los estudios de requerimientos de ley en la sección de Educación.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Julio 24 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   En el formulario de cargos en la clínica se modifica el input por un seleccionador de cargos (select).
                            *   Se cambia el color de fondo de los títulos de las secciones para que resalte cada sección.
                            *   Se desarrolla Nueva pregunta para almuerzo habitual.
                            *   Se desarrolla la opción para especificar un arte u oficio para cada integrante del grupo familiar.
                            *   Se adiciona nueva funcionalidad para agragar observaciones a ciertos tipos de ingresos familiares,
                                si está activo agregar observaciones en el maestro de tipos de salarios, entonces al momento de chequear
                                la opción, se despliega un campo de texto para complementar la respuesta.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Julio 19 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Se modifica el estilo de los encabezados en las secciones de tal forma que se resalte cada una.
                        ");
                        $wactualizacion[] = array(
                        'fecha' => '(Julio 12 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            *   Se crea una nueva sección llamada 'Seccion de preguntas', aquí se pueden crear preguntas dinamicamente
                                por medio del maestro de 'Repositorio de preguntas' donde a su vez se puede configurar la manera como se
                                van a responder las preguntas.
                            *   Se modifica la tabla talhuma_000012 creandole nuevos campos para soportar la configuración de la pregunta y respuesta.
                            *   En talhuma_000013 se crea el campo para guardar la fecha de retiro de un empleado.
                            *   Se adiciona el campo 'Seguridad' en los inserts de este escript.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Julio 05 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            Para esta fecha se desarrolla el registro de eventos en el log de eventos del modulo talhuma.
                        ");
$wactualizacion[] = array(
                        'fecha' => '(Julio 04 de 2012)',
                        'autor' => 'Edwar Jaramillo',
                        'descripcion' =>
                        "
                            * Esta es la fecha en que se inicia el proceso de desarrollo para el registro de eventos en el log.
                            * Se creó la tabla root_000078 para paramatrizar ocupaciones.
                            * Se creó la tabla root_000079 para parametrizar cargos.
                        ");

if ($VER_ACTUALIZACIONES && !isset($accion)) // Ver el log de actualizaciones
{
    echo "<div align='left'>Log script:<pre>";print_r($wactualizacion);echo "</pre></div>";
}
$wactualiz = $wactualizacion[0]['fecha'];



include_once("root/comun.php");


// $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
include_once("funciones_talhuma.php");
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

// Hay momento en los que se cierra la sesion por tiempo de espera, en este caso no se debería dejar modificar la caracterización
// puesto que no hay sesion iniciada por el usuario y no es posible obtener el codigo del usuario. Cualquie modificación a la caracterización
// no tendría registro de quién lo hizo.
if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
elseif(isset($accion) && isset($form)  && $form == 'ajax_json' && !isset($_SESSION['user']))
{
    $data = array("html"=>"", "error"=>1, "mensaje"=>"Recargue la pantalla principal de matrix para activar nuevamente la sesion.");
    echo json_encode($data);
    return;
}
$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];
$user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);

//$user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

// echo '<pre>';print_r($_REQUEST);echo '</pre>';
// if(isset($wuse))
// {
    // $wuse = base64_decode($wuse);
// }

/**
 * UPP(): Esta función convierte a mayúsculas el texto que recibe por parámetros.
 *
 * @param string $str   : texto que puede venir en minúsculas.
 * @return string       : retorna texto en mayúsculas.
 */
function UPP($str)
{
    return strtoupper(strtolower($str));
}

if(isset($accion) && isset($form) && $form == 'ajax_json')
{
    $data = array("html"=>"", "error"=>0, "mensaje"=>"");

    switch ($accion)
    {
        case 'insert_registro':
                $reg_nombre1 = utf8_decode(strtoupper(strtolower($reg_nombre1)));
                $reg_nombre2 = utf8_decode(strtoupper(strtolower($reg_nombre2)));
                $reg_apellido1 = utf8_decode(strtoupper(strtolower($reg_apellido1)));
                $reg_apellido2 = utf8_decode(strtoupper(strtolower($reg_apellido2)));

                $insert = " INSERT INTO ".$wbasedato."_000013
                                (Medico, Fecha_data, Hora_data, Idefre, Ideuse,
                                Ideno1, Ideno2, Ideap1, Ideap2, Idefnc, Idegen, Ideced,
                                Ideest, Seguridad)
                            VALUES
                                ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','0000-00-00','".$wuse."',
                                '".$reg_nombre1."', '".$reg_nombre2."', '".$reg_apellido1."', '".$reg_apellido2."', '".$reg_fecha_nace."', '".$reg_wgeneroemp."', '".$reg_wcedemp."',
                                'on','C-".$user_session."')";

                // $insert = " INSERT INTO ".$wbasedato."_000013
                //                 (Medico, Fecha_data, Hora_data, Idefre, Ideuse, Ideest, Seguridad)
                //             VALUES
                //                 ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','0000-00-00','".$wuse."','on','C-".$user_session."')";
                if($res = mysql_query($insert,$conex))
                {
                    // $primera_vez_car = true;
                    $data["mensaje"] = "Datos registrados";
                }
                else
                {
                    $data["sql_error"] = $insert." > ".mysql_error();
                    $data["error"] = 1;
                    $data["mensaje"] = "No se pudo realizar el registro, consulte con soporte Matrix.";
                }
            break;

        default:
            # code...
            break;
    }
    echo json_encode($data);
    return;
}

// La variable $accion siempre esta seteada para los llamados ajax, generalmente se usa $accion para los llamados asincronos con jquery.
if(isset($accion) && $accion == 'add') // ACCION - ADICIONAR
{
    /**
        Este if es para todas las acciones que implican insertar registros en la base de datos, la variable $form esta seteada para los llamados desde jquery
        indicando el tipo de información que se va a insertar, de esta manera se puede verificar donde se ingresaron los datos y en que tabla de la base de datos
        se debe guardar.
        Para cada acción sobre la base de datos, se guarda una explicación de lo ocurrido en la tabla de log, esto se hace mediante la función insertLog();
    */
    if(isset($form) && $form == 'formAddEducacion') // Grado educación
    {
        $insert = " INSERT INTO ".$wbasedato."_000014
                        (Medico, Fecha_data, Hora_data, Edugrd, Edutit, Eduins, Eduani, Eduuse, Eduest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode(UPP($grado_edu))."','".utf8_decode(UPP($titulo_edu))."','".utf8_decode(UPP($inst_edu))."','".utf8_decode(UPP($anio_edu))."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Grado Escolar): ".$q." - ".mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000014'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta grado de educacion'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddReqLey') // Requerimiento de ley
    {
        $insert = " INSERT INTO ".$wbasedato."_000043
                        (Medico, Fecha_data, Hora_data, Leygrd, Leytit, Leyins, Leyani, Leyuse, Leyest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode(UPP($grado_edu_ley))."','".utf8_decode(UPP($titulo_edu_ley))."','".utf8_decode(UPP($inst_edu_ley))."','".utf8_decode(UPP($anio_edu_ley))."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Grado Escolar): ".$q." - ".mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000043'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta estudio requerimiento de ley'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddIdioma') // Idioma
    {
        $insert = " INSERT INTO ".$wbasedato."_000015
                        (Medico, Fecha_data, Hora_data, Idides, Idihab, Idilee, Idiesc, Idiuse, Idiest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode($idio_des)."','".$idio_habla."','".$idio_lee."','".$idio_escribe."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Idiomas): ".$q." - ".mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000015'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta idioma'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddEstudio') // Estudios actuales
    {
        $insert = " INSERT INTO ".$wbasedato."_000016
                        (Medico, Fecha_data, Hora_data, Nesdes, Nesdur, Nesins, Nesniv, Neshor, Nesuse, Nesest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode(UPP($estu_des))."','".utf8_decode(UPP($estu_dur))."','".utf8_decode(UPP($estu_inst))."','".utf8_decode(UPP($estu_niv))."','".utf8_decode(UPP($estu_hor))."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Nuevos Estudios): " . $insert . " - " . mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000016'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta estudio actual'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddCapacitacion') //Capacitaciones requeridas
    {
        $insert = " INSERT INTO ".$wbasedato."_000017
                        (Medico, Fecha_data, Hora_data, Capcod, Capesp, Capuse, Capest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode($cap_tip)."','".utf8_decode(UPP($cap_que))."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Capacitaciones requeridas): " . $insert . " - " . mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000017'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta capacitación requerida'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddArtes') // Artes u oficios
    {
        $insert = " INSERT INTO ".$wbasedato."_000018
                        (Medico, Fecha_data, Hora_data, Oaodes, Oaodae, Oaouse, Oaoest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode(UPP($art_des))."','".$art_comparte."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Artes Oficios): " . $insert . " - " . mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000018'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta arte u oficio'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddFamiliar') // Grupo Familiar
    {
        $insert = " INSERT INTO ".$wbasedato."_000021
                        (Medico, Fecha_data, Hora_data, Grunom, Gruape, Grugen, Grupar, Grufna, Gruesc, Gruocu, Grucom, Gruart, Gruuse, Gruest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode(UPP($grFam_nom))."','".utf8_decode(UPP($grFam_apl))."','".$grFam_gen."','".$grFam_paren."','".utf8_decode(UPP($grFam_fnac))."','".$grFam_niv."','".utf8_decode(UPP($grFam_ocup))."','".$grFam_vcon."','".utf8_decode(UPP($grFam_art))."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Familiar): " . $insert . " - " . mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000021'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta integrante familiar'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddDiscapacitado') // Familiar con discapacidad
    {
        // $winfofam = base64_decode($winfofam);
        $insert = " INSERT INTO ".$wbasedato."_000020
                        (Medico, Fecha_data, Hora_data, Discpa, Diseda, Disdis, Disinf, Disest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$discap_parent."','".utf8_decode(UPP($discap_edad))."','".utf8_decode(UPP($discap_tipo))."','".UPP($winfofam)."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Familiar Discapacitado): " . $insert . " - " . mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000020'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta familiar con discapacidad'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddServicio') // Servicios en los que laboró
    {
        $insert = " INSERT INTO ".$wbasedato."_000022
                        (Medico, Fecha_data, Hora_data, Cincco, Cintie, Cincgo, Cinmot, Cinuse, Cinest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode(UPP($serv_nombre))."','".utf8_decode(UPP($serv_tiempo))."','".utf8_decode(UPP($serv_cargo))."','".utf8_decode(UPP($serv_motivo))."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Servicios Laboró): " . $insert . " - " . mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000022'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta servicio donde laboró'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddEmpleo') // Empresas en las que laboró
    {
        $insert = " INSERT INTO ".$wbasedato."_000023
                        (Medico, Fecha_data, Hora_data, Utremp, Utrtie, Utrcar, Utruse, Utrest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode(UPP($empl_empresa))."','".utf8_decode(UPP($empl_tiempo))."','".utf8_decode(UPP($empl_cargo))."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Empleo anterior): " . $insert . " - " . mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000023'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta un último empleo'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    elseif(isset($form) && $form == 'formAddCredito') // Créditos
    {
        $insert = " INSERT INTO ".$wbasedato."_000025
                        (Medico, Fecha_data, Hora_data, Cremot, Creent, Creval, Crecuo, Creuse, Creest, Seguridad)
                    VALUES
                        ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode(UPP($cred_motivo))."','".utf8_decode(UPP($cred_entidad))."','".utf8_decode(UPP($cred_valor))."','".utf8_decode(UPP($cred_cuota))."','".$wuse."','on','C-".$user_session."')";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Crédito): " . $insert . " - " . mysql_error());
        $id = mysql_insert_id();

        $descripcion = "tabla:'".$wbasedato."_000025'|id:'$id'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta Información de crédito'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $id;
    }
    return;
}
elseif(isset($accion) && $accion == 'update') // ACCION - EDITAR
{
    /**
        -   Al ingresar a este condicional implica que lo que sucederá es una modificación sobre registros en alguna tabla.
        -   Por lo general, en la mayoria de los casos, por parámetros llega el nombre del campo sobre el que se hará la modificación "$columnName", tambien el valor que se le
            adignará, y el id o identificador único del registro que se debe modificar.
        -   En muchos casos, por parámetros también puede llegar el nombre de la tabla (sufijo) sobre el que se debe hacer la modificación.
     */

    if(isset($form) && $form == 'formAddEducacion') // Grado educación
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000014 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000014'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza grado de educacion'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddReqLey') // Requerimiento de ley
    {
        $update = " UPDATE  ".$wbasedato."_000043 SET
                            $columnName = '".utf8_decode(UPP($value))."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000043'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza estudio requerimiento de ley'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddIdioma') // Idioma
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000015 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000014'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza grado de educacion'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif (isset($form) && $form == 'identGeneral') // Identificación general
    {
        if(isset($campo) && $campo != '')
        {
            if(isset($id_registro) && trim($id_registro) != '') // Si existe código de caracterización se actualiza, caso contrario inserta registro y genera nuevo código
            {
                // $id_registro = base64_decode($id_registro);
                $value2 = ($value == 'on' || $value == 'off') ? $value : UPP($value);
                $update = " UPDATE  ".$wbasedato."_".$wtabla." SET
                                    $campo = '".$value2."'
                            WHERE   id = '$id_registro'";
                $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar registros: ".$update." - ".mysql_error());
                // $data = array('id_registro'=>base64_encode($id_registro),'error'=>0);
                $data = array('id_registro'=>$id_registro,'error'=>0);

                $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:'$campo'|columnFiltro:'id'|valueFiltro:'$id_registro'|obs:'actualiza Identificación General'";
                insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);

                /**
                    Cuando viene seteada la variable $delSeccion:
                    Casos en los que se debe eliminar registros, seleccionó 'NO' en creditos, o 'NO' tiene parientes discapacitados.
                    En estos casos elimina los registros asociados al usuario en la sección que se quiere borrar.
                */
                if(isset($delSeccion) && $delSeccion != '' && $value == 'off')
                {
                    if (    strpos($delFiltro, 'use') !== false
                            || strpos($delFiltro, 'id') !== false
                            || strpos($delFiltro, 'Disinf') !== false ) {
                        // $delValor = base64_decode($delValor);
                    }

                    $q = "  DELETE  FROM    ".$wbasedato."_$delSeccion
                            WHERE   ".$delFiltro." = '$delValor'";
                    $res = mysql_query($q,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Registros de sección $delSeccion): ".$q." - ".mysql_error());

                    $descripcion = "tabla:'".$wbasedato.'_'.$delSeccion."'|id:''|columnUpd:''|columnFiltro:'".$delFiltro."'|valueFiltro:'$delValor'|obs:'Elimina registros'";
                    insertLog($conex, $wbasedato, $user_session, 'delete', $descripcion, $wuse);
                }

                echo json_encode($data);
            }
            else
            {
                $prefijo = substr($campo, 0, 3);

                $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
                $insert = " INSERT INTO ".$wbasedato."_".$wtabla."
                                (Medico, Fecha_data, Hora_data, ".$prefijo."use, ".$prefijo."est, $campo, Seguridad)
                            VALUES
                                ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$wuse."','on','".$value2."','C-".$user_session."')";
                $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar registro): " . $insert . " - " . mysql_error());
                // $id_registro = base64_encode(mysql_insert_id());
                $id_registro = mysql_insert_id();
                $data = array('id_registro'=>$id_registro,'error'=>0);

                $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Inserta registro'";
                insertLog($conex, $wbasedato, $user_session, 'add', $descripcion, $wuse);
                echo json_encode($data);
            }
        }
        else
        {
            $data = array('error'=>1,'mensaje'=>'No se pudo guardar..');
            echo json_encode($data);
        }
    }
    elseif (isset($form) && $form == 'salarios') // Salarios u otros ingresos familiares
    {
        if(isset($campo) && $campo != '')
        {
            /**

             */
            $explode = explode('_',$id_campo);
            //$id_registro = $explode[0]; // Código de la tabla Informacion familiar talhuma_000019
            $id_salario = $explode[1]; // código del salario a modificar
            if(isset($id_registro) && $id_registro != '') // Si existe código de caracterización se actualiza, caso contrario inserta registro y genera nuevo código
            {
                // $id_registro = base64_decode($id_registro);
                $q = "  SELECT  $campo
                        FROM    ".$wbasedato."_".$wtabla."
                        WHERE   id = '$id_registro'";
                $res = mysql_query($q,$conex) or die("Error: " . mysql_errno() . " - en el query consulta salarios familiares: ".$q." - ".mysql_error());
                if($row = mysql_fetch_array($res))
                {
                    // $explode = ($row[$campo] == '') ? '': explode(',',$row[$campo]);
                    $explode = ($row[$campo] == '') ? array() : explode(',',$row[$campo]);
                    if($value == 'off')
                    {
                        if($wtabla == '000019')
                        {
                            $observ_arr = array();
                            //consulta y elimina observacion al salario
                            $qTsal= "   SELECT  id, Faming AS salario, Famosl AS observ
                                        FROM    ".$wbasedato."_000019
                                        WHERE   id = '".$id_registro."'";
                            $rTsal = mysql_query($qTsal,$conex);
                            $salario_familiar = array('id'=>'','salario'=>array());
                            while($row = mysql_fetch_array($rTsal))
                            {
                                // la observacion esta concatenada por (codigo_salario|observacion al salario) y cada llave de este tipo
                                // esta separado por ||
                                if($row['observ'] != '')
                                {
                                    $expObsTxt = explode('||',$row['observ']);

                                    // este ciclo separa los codigos del tipo de salario con su respectiva observación almaceneda.
                                    foreach($expObsTxt as $keyOb => $valorOb)
                                    {
                                        $expOb = explode('=>',$valorOb);
                                        $observ_arr[$expOb[0]] = (array_key_exists(1,$expOb)) ? $expOb[1]: '';
                                    }
                                }
                            }

                            // verifica que la observacion a eliminar esté asociada a un id de salario, posteriormente actualiza el campo de observaciones
                            /**
                                cuando se tiene un campo de observación y en un mismo registro de la base de datos se tiene un campo que puede contener varios códigos concatenados,
                                el campo de observaciones debe diferencias a cuál de los códigos concatenados corresponde el comentario adicional, esto se logra anteponiendole el código
                                a quien pertenece el comentario de la siguiente manera "...||05=>Este es un comentario para uno de los códigos concatenados"
                            */
                            if(array_key_exists($id_salario,$observ_arr)) { unset($observ_arr[$id_salario]); }
                            $campo_upd = '*';
                            foreach($observ_arr as $key => $val_arr)
                            {
                                $campo_upd .= '||'.$key.'=>'.$val_arr;
                            }
                            $guardar = str_replace('*||','',$campo_upd);
                            $guardar = str_replace('*','',$guardar); // pude suceder que solo quede *
                            $update = " UPDATE  ".$wbasedato."_".$wtabla." SET
                                                Famosl = '".utf8_decode(UPP($guardar))."'
                                        WHERE   id = '$id_registro'";
                            $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar observacion de salario familiar: ".$update." - ".mysql_error());

                            $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:'Famosl'|columnFiltro:'id'|valueFiltro:'$id_registro'|obs:'actualiza registro observacion salarios'";
                            insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
                        }

                        // Elimina id de los salarios concatenados
                        $borrar = array_search($id_salario,$explode);
                        //print_r($explode);
                        unset($explode[$borrar]);
                    }
                    else
                    {
                        $explode[] = $id_salario;
                    }

                    $guardar = implode(',',$explode);

                    $update = " UPDATE  ".$wbasedato."_".$wtabla." SET
                                        $campo = '".utf8_decode(UPP($guardar))."'
                                WHERE   id = '$id_registro'";
                    $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar salario familiar: ".$update." - ".mysql_error());
                    // $data = array('id_registro'=>base64_encode($id_registro),'error'=>0);
                    $data = array('id_registro'=>$id_registro,'error'=>0);

                    $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:'$campo'|columnFiltro:'id'|valueFiltro:'$id_registro'|obs:'actualiza registro'";
                    insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
                    echo json_encode($data);
                }
                else
                {
                    $data = array('error'=>1,'mensaje'=>'No se pudo guardar [No existe registro - '.$id_registro.']..');
                    echo json_encode($data);
                }
            }
            else
            {
                $prefijo = substr($campo, 0, 3);
                $explode = explode('_',$id_campo);
                $id_salario = $explode[1]; // código del salario a modificar

                $insert = " INSERT INTO ".$wbasedato."_".$wtabla."
                                (Medico, Fecha_data, Hora_data, ".$prefijo."use, ".$prefijo."est, $campo, Seguridad)
                            VALUES
                                ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$wuse."','on','".$id_salario."','C-".$user_session."')";
                $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Salario): " . $insert . " - " . mysql_error());
                // $id_registro = base64_encode(mysql_insert_id());
                $id_registro = mysql_insert_id();
                $data = array('id_registro'=>$id_registro,'error'=>0);

                // $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'".base64_decode($id_registro)."'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Insertó Registro'";
                $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'".$id_registro."'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'Insertó Registro'";
                insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
                echo json_encode($data);
            }
        }
        else
        {
            $data = array('error'=>1,'mensaje'=>'No se pudo guardar..');
            echo json_encode($data);
        }
    }
    elseif (isset($form) && $form == 'roles') // Roles
    {
        if(isset($campo) && $campo != '')
        {
            $explode = explode('_',$id_campo);
            $id_rol = $explode[1]; // código rol a modificar
            if(isset($id_registro) && $id_registro != '') // Si existe código de caracterización se actualiza, caso contrario inserta registro y genera nuevo código
            {
                    if($value == 'off')
                    {
                        /**
                            Si el valor es off, por ejemplo al desmarcar un checkbox, indica que se debe eliminar este registro de esta tabla porque se supone, está indicando que
                            no pertenece a ese organismo interno por ejemplo.
                        */
                        $insert = " DELETE  FROM    ".$wbasedato."_000026
                                    WHERE   id = $id_registro";
                        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Roles participa): ".$q." - ".mysql_error());
                        $data = array('id_registro'=>'','error'=>0);

                        $descripcion = "tabla:'".$wbasedato."_000026'|id:'$id_registro'|columnUpd:''|columnFiltro:'id'|valueFiltro:'$id_registro'|obs:'Eliminó Organismo al que pertenece'";
                        insertLog($conex, $wbasedato, $user_session, 'delete', $descripcion, $wuse);
                        echo json_encode($data);
                    }
                    else
                    {
                        $guardar = implode(',',$explode);

                        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
                        $update = " UPDATE  ".$wbasedato."_".$wtabla." SET
                                            $campo = '".$value2."'
                                    WHERE   id = '$id_registro'";
                        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar rol al que pertenece: ".$update." - ".mysql_error());
                        $data = array('id_registro'=>$id_registro,'error'=>0);

                        $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:'$campo'|columnFiltro:'id'|valueFiltro:'$id_registro'|obs:'actualiza Rol al que pertenece'";
                        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
                        echo json_encode($data);
                    }
            }
            else
            {
                /**
                    Si no existe ningún registro para modificar, entonces lo que se debe hacer es insertar estos datos por primera vez.
                */
                $prefijo = substr($campo, 0, 3);
                $explode = explode('_',$id_campo);
                $id_salario = $explode[1]; // código del rol a modificar

                $insert = " INSERT INTO ".$wbasedato."_".$wtabla."
                                (Medico, Fecha_data, Hora_data, ".$prefijo."use, ".$prefijo."est, $campo, Seguridad)
                            VALUES
                                ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$wuse."','on','".$id_rol."','C-".$user_session."')";
                $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar pertenece a organismo interno): " . $insert . " - " . mysql_error());
                $id_registro = mysql_insert_id();
                $data = array('id_registro'=>$id_registro,'error'=>0);

                $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta rol al que pertenece'";
                insertLog($conex, $wbasedato, $user_session, 'add', $descripcion, $wuse);
                echo json_encode($data);
            }
        }
        else
        {
            $data = array('error'=>1,'mensaje'=>'No se pudo guardar..');
            echo json_encode($data);
        }
    }
    elseif (isset($form) && $form == 'salario_obs') // Observaciones a otros ingresos, otros salarios
    {
        if(isset($campo) && $campo != '')
        {
            $explode = explode('_',$id_campo);
            $id_sal = $explode[1]; // código del salario a modificar
            if(isset($id_registro) && $id_registro != '') //
            {
                $observ_arr = array();
                $qTsal= "   SELECT  id, Faming AS salario, Famosl AS observ
                            FROM    ".$wbasedato."_000019
                            WHERE   id = '".$id_registro."'";
                $rTsal = mysql_query($qTsal,$conex);
                $salario_familiar = array('id'=>'','salario'=>array());
                while($row = mysql_fetch_array($rTsal))
                {
                    // la observacion esta concatenada por (codigo_salario|observacion al salario) y cada llave de este tipo
                    // esta separado por ||
                    if($row['observ'] != '')
                    {
                        $expObsTxt = explode('||',$row['observ']);

                        // este ciclo separa las los codigos del tipo de salario con su respectiva observación almaceneda.
                        foreach($expObsTxt as $keyOb => $valorOb)
                        {
                            $expOb = explode('=>',$valorOb);
                            $observ_arr[$expOb[0]] = (array_key_exists(1,$expOb)) ? $expOb[1]: '';
                        }
                    }
                }

                if(array_key_exists($id_sal,$observ_arr)) { $observ_arr[$id_sal] = $value; }
                else { $observ_arr[$id_sal] = $value; }

                $campo_upd = '*';
                foreach($observ_arr as $key => $val_arr)
                {
                    $campo_upd .= '||'.$key.'=>'.$val_arr;
                }

                $guardar = str_replace('*||','',$campo_upd);
                $guardar = str_replace('*','',$guardar); // pude suceder que solo quede *

                $update = " UPDATE  ".$wbasedato."_".$wtabla." SET
                                    $campo = '".utf8_decode(UPP($guardar))."'
                            WHERE   id = '$id_registro'";
                $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar salario familiar: ".$update." - ".mysql_error());
                $data = array('id_registro'=>$id_registro,'error'=>0);

                $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:'$campo'|columnFiltro:'id'|valueFiltro:'$id_registro'|obs:'actualiza observacion del salario'";
                insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
                echo json_encode($data);
            }
            else
            {
                $prefijo = substr($campo, 0, 3);
                $explode = explode('_',$id_campo);
                $id_salario = $explode[1]; // código del rol a modificar

                $insert = " INSERT INTO ".$wbasedato."_".$wtabla."
                                (Medico, Fecha_data, Hora_data, ".$prefijo."use, ".$prefijo."est, $campo, Seguridad)
                            VALUES
                                ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$wuse."','on','".$id_sal."','C-".$user_session."')";
                $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar observacion salario): " . $insert . " - " . mysql_error());
                $id_registro = mysql_insert_id();
                $data = array('id_registro'=>$id_registro,'error'=>0);

                $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta observacion salario'";
                insertLog($conex, $wbasedato, $user_session, 'add', $descripcion, $wuse);
                echo json_encode($data);
            }
        }
        else
        {
            $data = array('error'=>1,'mensaje'=>'No se pudo guardar..');
            echo json_encode($data);
        }
    }
    elseif (isset($form) && $form == 'preguntas') // Preguntas
    {
        if(isset($campo) && $campo != '')
        {
            $explode = explode('_',$id_campo);
            $id_pregunta = $explode[1]; // código de la pregunta
            if(isset($id_registro) && $id_registro != '') // Si existe código actualiza, caso contrario inserta registro y genera nuevo código
            {
                $campos = explode('|',$campo);
                $del_resp = '';
                if($value == 'off')
                {
                    $del_resp = ", Resres = ''";
                }

                $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
                $update = " UPDATE  ".$wbasedato."_".$wtabla." SET
                                    ".$campos[1]." = '".$value2."'
                                    $del_resp
                            WHERE   id = '$id_registro'";
                $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar Respuesta a preguntas: ".$update." - ".mysql_error());
                $data = array('id_registro'=>$id_registro,'error'=>0);

                $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:'".$campos[1]."'|columnFiltro:'id'|valueFiltro:'$id_registro'|obs:'actualiza Respuesta a pregunta'";
                insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
                echo json_encode($data);
            }
            else
            {
                $prefijo = substr($campo, 0, 3);
                $campos = explode('|',$campo);

                $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
                $insert = " INSERT INTO ".$wbasedato."_".$wtabla."
                                (Medico, Fecha_data, Hora_data, Resuse, Resest, ".$campos[0].", ".$campos[1].", Seguridad)
                            VALUES
                                ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$wuse."','on','".$id_pregunta."','".$value2."','C-".$user_session."')";
                $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar Respuesta a pregunta): " . $insert . " - " . mysql_error());
                $id_registro = mysql_insert_id();
                $data = array('id_registro'=>$id_registro,'error'=>0);

                $descripcion = "tabla:'".$wbasedato."_".$wtabla."'|id:'$id_registro'|columnUpd:''|columnFiltro:''|valueFiltro:''|obs:'inserta respuesta a pregunta'";
                insertLog($conex, $wbasedato, $user_session, 'add', $descripcion, $wuse);
                echo json_encode($data);
            }
        }
        else
        {
            $data = array('error'=>1,'mensaje'=>'No se pudo guardar..');
            echo json_encode($data);
        }
    }
    elseif(isset($form) && $form == 'formAddEstudio') // Estudios actuales
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000016 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000016'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza estudio actual'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddCapacitacion') // Capacitación
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000017 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000017'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza capacitación requerida'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddArtes') // Artes u oficios
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000018 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000018'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza artes u oficios'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddFamiliar') // Grupo Familiar
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000021 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Update Grupo Familiar: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000021'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza integrante familiar'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddDiscapacitado') // Familiar con discapacidad
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000020 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Update Familiar con discapacidad: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000020'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza familiar con discapacidad'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddServicio') // Servicio en que laboró
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000022 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Update Servicio en que laboró: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000022'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza servicio en que laboró'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddEmpleo') // Anterior empleo
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000023 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Update Último empleo: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000023'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza empleo anterior'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    elseif(isset($form) && $form == 'formAddCredito') // Crédito
    {
        $value2 = ($value == 'on' || $value == 'off') ? $value : utf8_decode(UPP($value));
        $update = " UPDATE  ".$wbasedato."_000025 SET
                            $columnName = '".$value2."'
                    WHERE   id = $id";
        $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Update Crédito: ".$update." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000025'|id:'$id'|columnUpd:'$columnName'|columnFiltro:'id'|valueFiltro:'$id'|obs:'actualiza información de crédito'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
        echo $value;
    }
    return;
}
elseif(isset($accion) && $accion == 'delete') // ACCION - ELIMINAR
{
    if(isset($form) && $form == 'formAddEducacion') // Grado educación
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000014
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Grado Escolar): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000014'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina grado escolar'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddArtes') // Artes u oficios
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000018
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Artes u oficios): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000018'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina arte u oficio'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddReqLey') // Requerimiento de ley
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000043
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar estudio requerimiento de ley): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000043'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina estudio requerimiento de ley'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddIdioma') // Idioma
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000015
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Grado Escolar): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000015'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina Idioma'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddEstudio') // Estudios actuales
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000016
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar estudio): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000016'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina estudio actual'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddCapacitacion') // Estudios actuales
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000017
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar capacitaciones): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000017'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina capacitacion'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddFamiliar') // Pariente
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000021
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Pariente): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000021'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina integrante familiar'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddDiscapacitado') // Pariente con discapacidad
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000020
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Pariente Discapacitado): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000020'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina pariente con discapacidad'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddServicio') // Servicio donde laboró
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000022
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Servicio donde laboró): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000022'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina servicio donde laboró'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddEmpleo') // Anterior empleo
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000023
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Último Empleo): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000023'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina anterior empleo'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    elseif(isset($form) && $form == 'formAddCredito') // Crédito
    {
        $insert = " DELETE  FROM    ".$wbasedato."_000025
                    WHERE   id = $id";
        $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Eliminar Crédito): ".$q." - ".mysql_error());

        $descripcion = "tabla:'".$wbasedato."_000025'|id:'$id'|columnUpd:''|columnFiltro:'id'|valueFiltro:''|obs:'elimina información de crédito'";
        insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $wuse);
    }
    return;
}
elseif(isset($accion) && $accion == 'load') // ACCION - LOAD - Carga de datos
{
    if(isset($form) && $form == 'formAddCapacitacion') //
    {
        $qTCap= "   SELECT  Tcapcod AS id, Tcades AS t_capacitacion
                    FROM    ".$wbasedato."_000011
                    WHERE  Tcaest = 'on'
                    ORDER BY Tcades ";
        $rTCap = mysql_query($qTCap,$conex);
        $arr_cap = array();
        while($row = mysql_fetch_array($rTCap))
        {
            $arr_cap[$row['id']] =  utf8_encode($row['t_capacitacion']);
        }
        echo json_encode($arr_cap);
    }
    elseif(isset($form) && $form == 'formAddGenero') // Lista de generos
    {
        $query= "   SELECT  Sexcod, Sexdes
                    FROM    root_000029
                    WHERE   Sexest = 'on'";
        $result = mysql_query($query,$conex);
        $arr_resp = array();
        while($row = mysql_fetch_array($result))
        {
            $arr_resp[$row['Sexcod']] =  utf8_encode($row['Sexdes']);
        }
        echo json_encode($arr_resp);
    }
    elseif(isset($form) && $form == 'formAddParentesco') // Lista de parentescos
    {
        $query= "   SELECT  Parcod AS id, Pardes
                    FROM    root_000067
                    WHERE   Parest = 'on'";
        $result = mysql_query($query,$conex);
        $arr_resp = array();
        while($row = mysql_fetch_array($result))
        {
            $arr_resp[$row['id']] =  utf8_encode($row['Pardes']);
        }
        echo json_encode($arr_resp);
    }
    elseif(isset($form) && $form == 'formAddEscolaridad') // Lista grados escolares
    {
        $query= "   SELECT  Scocod AS id, Scodes AS nombre, Scoley AS req_deLey
                    FROM    root_000066
                    WHERE   Scoest = 'on'";
        $result = mysql_query($query,$conex);
        $arr_resp = array();
        while($row = mysql_fetch_array($result))
        {
            if($row['req_deLey'] == 'off')
            {
                $arr_resp[$row['id']] =  utf8_encode($row['nombre']);
            }
        }
        echo json_encode($arr_resp);
    }
    elseif(isset($form) && $form == 'formAddEscolaridadLey') // Lista grados escolares de ley
    {
        $qIde= "SELECT  Ideuse AS wuse, Idecco AS ccosto, Ideccg AS cod_cargo
                FROM    ".$wbasedato."_000013
                WHERE   Ideest = 'on'
                        AND Ideuse = '".$wuse."'";
        $rIde = mysql_query($qIde,$conex);
        $cco = '*';
        $ccg = '*';
        if($row = mysql_fetch_array($rIde))
        {
            $cco = $row['ccosto'];
            $ccg = $row['cod_cargo'];
        }

        // Relacion grado escolar, cargo, centro costo.
        $qEsco= "   SELECT  Scocod AS id, Carcod AS cargo_ley, Ccocod AS ccosto_ley
                    FROM    ".$wbasedato."_000044
                    WHERE   Recest = 'on'
                            AND ( Carcod = '".$ccg."' OR Carcod = '*' )
                            AND ( Ccocod = '".$cco."' OR Ccocod = '*' )";
        $rEsco = mysql_query($qEsco,$conex);
        while($rLey = mysql_fetch_array($rEsco))
        {
            $grados_ley[$rLey['id']] = array('cargo_ley'=>$rLey['cargo_ley'],'ccosto_ley'=>$rLey['ccosto_ley']);
        }

        $query= "   SELECT  Scocod AS id, Scodes AS nombre, Scoley AS req_deLey, Scoine AS interno_externo
                    FROM    root_000066
                    WHERE   Scoest = 'on'";
        $result = mysql_query($query,$conex);
        $arr_resp = array();
        while($row = mysql_fetch_array($result))
        {
            if($row['req_deLey'] == 'on' && array_key_exists($row['id'],$grados_ley))
            {
                $int_ext = '';
                if(strtoupper(strtolower($row['interno_externo'])) == 'I') { $int_ext = '(Req. Interno)'; }
                if(strtoupper(strtolower($row['interno_externo'])) == 'E') { $int_ext = '(Req. Externo)'; }
                $arr_resp[$row['id']] =  utf8_encode($row['nombre']).' '.$int_ext;
            }
        }
        echo json_encode($arr_resp);
    }
    elseif(isset($form) && $form == 'formAddOcupaciones') // Tipos de ocupaciones
    {
        $query = "  SELECT  Ocucod AS id, Ocudes
                    FROM    root_000078
                    WHERE   Ocuest = 'on'
                    ORDER BY Ocudes";
        $result = mysql_query($query,$conex);
        $arr_resp = array();
        while($row = mysql_fetch_array($result))
        {
            $arr_resp[$row['id']] =  utf8_encode($row['Ocudes']);
        }
        echo json_encode($arr_resp);
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
    elseif(isset($form) && $form == 'load_pais_visa') // Lista paises visa
    {
        if(isset($id_padre) && $id_padre != '')
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

            $explode = ($paises_use != '') ? explode(',',$paises_use) : array();
            if(!in_array($id_padre,$explode)) { $explode[] = $id_padre; }

            $cod_paises = implode(',',$explode);

            $update = " UPDATE  ".$wbasedato."_000013 SET
                                Idepvi = '$cod_paises'
                        WHERE   Ideuse = '$wuse'";
            $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar paises visa: ".$update." - ".mysql_error());

            $descripcion = "tabla:'".$wbasedato."_000013'|id:''|columnUpd:'Idepvi'|columnFiltro:'Ideuse'|valueFiltro:'$wuse'|obs:'actualiza país de visa'";
            insertLog($conex, $wbasedato, $user_session, 'update', $descripcion, $wuse);
        }
        // Si biene seteado borrar, es porque en la pregunta "Tiene visa" seleccionó No, si tenía paises seleccionados los debe eliminar.
        if (isset($borrar) && $borrar == '1')
        {
            $q = "  UPDATE  ".$wbasedato."_000013 SET
                            Idepvi = ''
                    WHERE   Ideuse = '$wuse'";
            $res = mysql_query($q,$conex) or die("Error: " . mysql_errno() . " - en el query Actualizar paises visa: ".$q." - ".mysql_error());

            $descripcion = "tabla:'".$wbasedato."_000013'|id:''|columnUpd:'Idepvi'|columnFiltro:'Ideuse'|valueFiltro:'$wuse'|obs:'actualiza país de visa - borra países'";
            insertLog($conex, $wbasedato, $user_session, 'update', $descripcion, $wuse);
        }

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
                $result .= '<input type="checkbox" id="'.$cod_pais.'" name="'.$cod_pais.'" rel="000013" in="Idepvi" value="on" onClick="blurCampo(this,\'wcaracteriza\',\'salarios\',\'\',\'\',\'\');" checked="checked" />&nbsp;'.$nombre.'<br />';
            }
        }
        echo $result;
    }
    elseif(isset($form) && $form == 'formAddServicioCCO') // Lista centros de costos
    {
        $q = "  SELECT  Empdes,Emptcc
                FROM    root_000050
                WHERE   Empcod = '".$wemp_pmla."'";
        $res = mysql_query($q,$conex);

        $options = array();
        if($row = mysql_fetch_array($res))
        {
            $tabla_CCO = $row['Emptcc'];
            switch ($tabla_CCO)
            {
                case "clisur_000003":
                        $query = "select Ccocod AS codigo, Ccodes AS nombre from clisur_000003 ORDER BY Ccodes";
                        break;
                case "farstore_000003":
                        $query = "select Ccocod AS codigo, Ccodes AS nombre from farstore_000003 ORDER BY Ccodes";
                        break;
                case "costosyp_000005":
                        $query = "select Ccocod AS codigo, Cconom AS nombre from costosyp_000005 ORDER BY Cconom";
                        break;
                case "uvglobal_000003":
                        $query = "select Ccocod AS codigo, Ccodes AS nombre from uvglobal_000003 ORDER BY Ccodes";
                        break;
                default:
                        $query="select Ccocod AS codigo, Cconom AS nombre from costosyp_000005 ORDER BY Cconom";
            }

            $res = mysql_query($query,$conex);

            while($row = mysql_fetch_array($res))
            {
                $options[$row['codigo']] = $row['codigo'].'-'.utf8_encode(ucwords(strtolower($row['nombre'])));
            }
        }
        echo json_encode($options);
    }
    elseif(isset($form) && $form == 'formAddServicioCCG') // Lista centros de costos
    {
        $q = "  SELECT  Carcod AS id,Cardes AS cargo
                FROM    root_000079
                WHERE   Carest = 'on'
                ORDER By Cardes";
        $res = mysql_query($q,$conex);

        $options = array();

        while($row = mysql_fetch_array($res))
        {
            $options[$row['id']] = utf8_encode($row['cargo']);
        }

        echo json_encode($options);
    }
    elseif(isset($form) && $form == 'load_mejoras') // Lista mejoras para vivienda
    {
        $data = array('mensaje'=>'', 'error'=>0, 'req_complememto'=>'off','contenido'=>'&nbsp;');
        if($id_estVivienda != '')
        {
            $qEv = "SELECT  Esvcod AS id, Esvrcm AS req_complemento, Esvmcm AS msj_complemento
                    FROM    root_000070
                    WHERE   Esvest = 'on'
                            AND Esvcod = '$id_estVivienda'";
            $resEv = mysql_query($qEv,$conex) or die("Error: " . mysql_errno() . " - en el query Consulta mejoras: ".$qEv." - ".mysql_error());
            $est = mysql_fetch_array($resEv);

            if($est['req_complemento'] == 'on')
            {
                $data['mensaje'] = "N/A";

                $q = "  SELECT  Mejcod AS id, Mejdes AS mejora
                        FROM    ".$wbasedato."_000041
                        WHERE   Mejest = 'on'
                        ORDER By Mejdes";
                $res = mysql_query($q,$conex) or die("Error: " . mysql_errno() . " - en el query Consulta mejoras: ".$q." - ".mysql_error());

                $checks = "";
                while($row = mysql_fetch_array($res))
                {
                    $idInput = '_'.$row['id'].'_mej';
                    $checks .= '<input type="checkbox" id="'.$idInput.'" name="'.$idInput.'" rel="000024" in="Cvicmj" value="on" onClick="blurCampo(this,\'wcondicion\',\'salarios\',\'\',\'\',\'\');" />&nbsp;'.utf8_encode($row['mejora']).'<br />';
                }
                //$checks .= '&nbsp;Otra, Cu&aacutel?<input type="text" name="womej" id="womej" rel="000024" in="Cviomj" onblur="blurCampo(this,\'wcondicion\',\'identGeneral\',\'\',\'\',\'\');" value="" />';
                $data['contenido'] = ($checks != '') ? $checks: '<div class="parrafo1" style="background-color: #E4E4E4" align="center">[?] No hay tipos de mejoras para seleccionar.</div>';
                $data['mensaje'] = $est['msj_complemento'];
                $data['req_complemento'] = 'on';
            }
            else
            {
                // $id_registro = base64_decode($id_registro);
                $update = " UPDATE  ".$wbasedato."_000024 SET
                                    Cvicmj = '',
                                    Cviomj = ''
                            WHERE   id = $id_registro";
                $res = mysql_query($update,$conex) or die("Error: " . mysql_errno() . " - en el query Update Servicio en que laboró: ".$update." - ".mysql_error());
                $descripcion = "tabla:'".$wbasedato."_000024'|id:'$id_registro'|columnUpd:'Cvicmj,Cviomj'|columnFiltro:'id'|valueFiltro:'$id_registro'|obs:'actualiza mejoras vivienda - borra mejoras'";
                insertLog($conex, $wbasedato, $user_session, 'update', $descripcion, $wuse);
            }
        }
        echo json_encode($data);
    }
    elseif(isset($form) && $form == 'permisos') // Consulta permisos de lectura y escritura
    {
        $data = array('permiso'=>'ninguno', 'error'=>0, 'mensaje'=>'', 'html'=>'');
        if($user_session_wemp !== $wuse )
        {
            $sqlU = "   SELECT  Idecco, Ideccg, CONCAT(Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) AS nombres
                        FROM    ".$wbasedato."_000013
                        WHERE   Ideuse = '".$wuse."'";
            $resU = mysql_query($sqlU,$conex) or die("Error: " . mysql_errno() . " - en el query consultar informacion de usuario a ver: ".$sqlU." - ".mysql_error());

            $wcodcosto_ver = '+';
            $wcodcargo_ver = '+';
            $wnombres_ver = '';
            if(mysql_num_rows($resU))
            {
                $rowU = mysql_fetch_array($resU);
                $wcodcosto_ver = $rowU['Idecco'];
                $wcodcargo_ver = $rowU['Ideccg'];
                $wnombres_ver = $rowU['nombres'];
            }

            $permisoAdmin = consultarSiEsAdmin($conex, $wemp_pmla, $wtema, $wcodtab, $user_session);
            $permiso = consultarPermisosAdicionales($conex, $wemp_pmla, $wtema, $wcodtab, $wuse, $wcodcargo_ver, $wcodcosto_ver, $user_session);

            if(count($permisoAdmin) > 0) // Si es admin para esa pestaña, puede ver lo de quien sea.
            {
                $data['permiso'] = $permisoAdmin['permiso'];
            }
            elseif($permiso['permiso'] == 'consultar')  //&& $permiso['esAdmin'] != 'on' // Si no es admin, entonces
            {
                $data['permiso'] = 'consultar';
            }
            elseif($permiso['permiso'] == 'actualizar' ) // || $permiso['esAdmin'] == 'on'
            {
                $data['permiso'] = 'actualizar';
            }
            else
            {
                $data['html'] = "   <br /><br /><br /><br /><br /><br />
                                    <div style='color:orange;font-weight:bold; background-color:#F2F2F2;'>
                                        <br />
                                        [?] No tiene permitido ver la caracterizaci&oacute;n de: <br /><br />\"".utf8_encode(trim(strtoupper(strtolower($wnombres_ver))))."\"
                                        <br /><br />
                                    </div>
                                    <br /><br /><br /><br /><br /><br />";
            }
        }
        else
        {
            $data['permiso'] = 'actualizar';
        }
        echo json_encode($data);
    }
    return;
}
else
{

    /**
        *   Aquí es la iniciación o carga previa del formulario , se consultan todos los datos que se deben mostrar en los campos (parametrizables).
            Si es la primera vez que se carga el formulario se muestran datos parametrizados iniciales sino se muestran los mismos datos parametrizables pero seleccionando
            las opciones previamente elegidas, así como también todos los campos que fueron llenados previamente.

        $identGeneral: es un array que contiene todos los datos parametrizales y otros datos llenados previamente con el fin de pintarlos más adelante en este script
        en la zona de html.

        *   Consulta Id de caracterización.
            Lo que se hace es buscar el código $wuse que llega por parámetros, en la tabla 000013, si no se encontró registro, se setea la variable $error en 1
            para indicar que no se encontró pero no sin antes verificar que el $wuse que llega por parámetro es igual a $use_session (usuario logueado) de ser así
            se debe insertar este código en la base de datos, en la tabla 000013, si se inserta correctamente se indica que es la primera vez que se abre el
            formulario para este usuario y se le solicitará más adelante información personal. Si ocurre algún error la variable $error nuevamente se setea en 1.

            Este caso se desarrolló para los usuarios externos que nunca van a estar asociados a los registros de sistema de nómina en UNIX, pero que igual deben
            quedar dentro de la tabla .."000013" de forma centralizada.
    */

    /*********************************
    * Datos básicos de identificación
    */
    $q = "  SELECT  id
            FROM    ".$wbasedato."_000013
            WHERE   Ideuse = '$wuse'
                    AND Ideest = 'on'";
    $res = mysql_query($q,$conex);
    $wcaracteriza = '';
    $error = 0;
    $primera_vez_car = false;
    if(mysql_num_rows($res) <= 0 && $user_session_wemp != $wuse)
    {
        $error = 1;
    }
    elseif(mysql_num_rows($res) <= 0 && $user_session_wemp == $wuse)
    {
        // $insert = " INSERT INTO ".$wbasedato."_000013
        //                 (Medico, Fecha_data, Hora_data, Idefre, Ideuse, Ideest, Seguridad)
        //             VALUES
        //                 ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','0000-00-00','".$wuse."','on','C-".$user_session."')";
        // if($res = mysql_query($insert,$conex))
        {
            $primera_vez_car = true;
        }
        // else
        // {
        //     $error = 1;
        // }
    }

    // Si la variable $error por algún motivo fue seteada en 1, no se mostrará el formulario sino un mensaje.
    if (isset($wuse) && $wuse == '' || $error == 1)
    {
        echo '  <br /><br /><br />
            <div id="div_marco" align="center">
                <table cellspacing="0" cellpadding="0" border="0" style="text-align: left; width: 950px;">
                    <tbody>
                        <tr>
                            <td class="brdtop brdleft bgGris1">&nbsp;</td>
                            <td class="brdtop bgGris1">&nbsp;</td>
                            <td class="brdtop brdright bgGris1">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class="">
                            <td width="20px;" class="brdleft bgGris1">&nbsp;</td>
                            <td style="text-align: center; font-weight: bold; font-size:15pt;" class="encabezadoTabla">
                                    CARACTERIZACI&Oacute;N
                            </td>
                            <!-- ancho de td width="70px;" -->
                            <td width="25px" class="brdright bgGris1">&nbsp;</td>
                            <td width="70px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="brdleft bgGris1" colspan="1">&nbsp;</td>
                        <!-- ancho de td width="540px;" -->
                            <td width="" align="center" class="fila2">

                            </td>
                            <td class="brdright bgGris1">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr><td width="20px;" class="brdleft bgGris1">&nbsp;</td>
                            <td width="950px;" align="center" class="encabezadoTabla">
                                    <div style="text-align: center; font-weight: bold; font-size:15pt;">NO SE ENCONTRARON DATOS</div>
                            </td>
                            <td width="25px" class="brdright bgGris1">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr><tr>
                            <td class="brdleft bgGris1">&nbsp;</td>
                            <td align="center" class="bgGris1" colspan="1">


                            </td>
                            <td class="brdright bgGris1">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="brdleft brdbottom bgGris1">&nbsp;</td>
                            <td class="brdbottom bgGris1">&nbsp;</td>
                            <td class="brdbottom brdright bgGris1">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>';
        return;
    }

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
    * Datos de Grados escolares
    */
    $query= "   SELECT  ed.Edugrd, ed.Edutit, ed.Eduins, ed.Eduani, ed.Eduuse, ed.id, esc.Scodes
                FROM    ".$wbasedato."_000014 as ed
                        LEFT JOIN
                        root_000066 AS esc ON (ed.Edugrd = esc.Scocod)
                WHERE   Eduuse = '$wuse'
                        AND Eduest = 'on'
                ORDER BY Eduani ";
    $r = mysql_query($query,$conex);
    $niveles_educacion = array();
    while($row = mysql_fetch_array($r))
    {
        $niveles_educacion[$row['id']] =  array('grado'=>$row['Scodes'],'titulo'=>$row['Edutit'],'intitucion'=>$row['Eduins'],'anio'=>$row['Eduani']);
    }

    /*********************************
    * Datos de Estudios de requerimientos de ley
    */
    $query= "   SELECT  ed.Leygrd, ed.Leytit, ed.Leyins, ed.Leyani, ed.Leyuse, ed.id, esc.Scodes, esc.Scoine AS interno_externo
                FROM    ".$wbasedato."_000043 as ed
                        LEFT JOIN
                        root_000066 AS esc ON (ed.Leygrd = esc.Scocod)
                WHERE   Leyuse = '$wuse'
                        AND Leyest = 'on'
                ORDER BY Leyani ";
    $r = mysql_query($query,$conex);
    $requerimientos_ley = array();
    while($row = mysql_fetch_array($r))
    {
        $int_ext = '';
        if(strtoupper(strtolower($row['interno_externo'])) == 'I') { $int_ext = '(Req. Interno)'; }
        if(strtoupper(strtolower($row['interno_externo'])) == 'E') { $int_ext = '(Req. Externo)'; }
        $requerimientos_ley[$row['id']] =  array('id'=>$row['id'],'grado'=>$row['Scodes'].' '.$int_ext,'titulo'=>$row['Leytit'],'intitucion'=>$row['Leyins'],'anio'=>$row['Leyani']);
    }

    /*********************************
    * Datos de Idiomas
    */
    $qIdio= "   SELECT  id, Idides AS idioma, Idihab AS habla, Idilee AS lee, Idiesc AS escribe
                FROM    ".$wbasedato."_000015
                WHERE   Idiuse = '$wuse'
                ORDER BY Idides ";
    $rIdio = mysql_query($qIdio,$conex);
    $idiomas = array();
    while($row = mysql_fetch_array($rIdio))
    {
        $habla = ($row['habla'] == 'on') ? 'Si': 'No';
        $lee = ($row['lee'] == 'on') ? 'Si': 'No';
        $escribe = ($row['escribe'] == 'on') ? 'Si': 'No';
        $idiomas[$row['id']] =  array('idioma'=>$row['idioma'],'habla'=>$habla,'lee'=>$lee,'escribe'=>$escribe);
    }
    $identGeneral['idiomas'] = $idiomas;

    /*********************************
    * Datos de Estudios
    */
    $qEstu= "   SELECT  id, Nesdes AS estudio, Nesdur AS duracion, Nesins AS institucion, Nesniv AS nivel, Neshor AS horario
                FROM    ".$wbasedato."_000016
                WHERE  Nesuse = '$wuse'
                ORDER BY Nesdes ";
    $rEstu = mysql_query($qEstu,$conex);
    $estudios = array();
    while($row = mysql_fetch_array($rEstu))
    {
        $estudios[$row['id']] =  array('estudio'=>$row['estudio'],'duracion'=>$row['duracion'],'institucion'=>$row['institucion'],'nivel'=>$row['nivel'],'horario'=>$row['horario']);
    }
    $identGeneral['estudios'] = $estudios;

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
    * Datos Capacitaciones requeridas
    */
    $qCap = "   SELECT  cap_req.id, cap_req.Capcod AS cod_capacitacion, t_cap.Tcades AS capacitacion, cap_req.Capesp AS en_que
                FROM    ".$wbasedato."_000017 AS cap_req, ".$wbasedato."_000011 as t_cap
                WHERE   cap_req.Capcod = t_cap.Tcapcod
                        AND t_cap.Tcaest = 'on'
                        AND cap_req.Capuse = '".$wuse."'
                ORDER BY t_cap.Tcades";
    $rCap = mysql_query($qCap,$conex);
    $cap_requeridas = array();
    while($row = mysql_fetch_array($rCap))
    {
        $cap_requeridas[$row['id']] =  array('capacitacion'=>$row['capacitacion'],'en_que'=>$row['en_que']);
    }
    $identGeneral['cap_requeridas'] = $cap_requeridas;

    /*********************************
    * Datos Artes oficios
    */
    $qOao= "    SELECT  id, Oaodes AS arte_oficio, Oaodae AS enseniaria
                FROM    ".$wbasedato."_000018
                WHERE   Oaoest = 'on'
                        AND Oaouse = '".$wuse."'
                ORDER BY Oaodes";
    $rOao = mysql_query($qOao,$conex);
    $artes_oficios = array();
    while($row = mysql_fetch_array($rOao))
    {
        $ensenaria = ($row['enseniaria'] == 'on') ? 'Si': 'No';
        $artes_oficios[$row['id']] =  array('arte_oficio'=>$row['arte_oficio'],'enseniaria'=>$ensenaria);
    }
    $identGeneral['artes_oficios'] = $artes_oficios;

    /*********************************
    * Datos Tipo acompañantes
    */
    $qTAcomp = "SELECT  t_acomp.Acocod AS id, t_acomp.Acodes AS t_acompanante_des, vive.Famaco AS acompanante
                FROM    root_000075 AS t_acomp
                        LEFT JOIN
                        ".$wbasedato."_000019 as vive ON (t_acomp.Acocod = vive.Famaco AND vive.Famuse = '".$wuse."')
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
    * Datos Grupo Familiar
    */
    $qGrpF = "SELECT    grp.id, grp.Grunom AS nombres, grp.Gruape AS apellidos
                        , sex.Sexdes AS genero, par.Pardes AS parentesco
                        , grp.Grufna AS f_nacimiento, esc.Scodes AS escolaridad
                        , ocu.Ocudes AS ocupacion, grp.Grucom AS comparte
                        , grp.Gruart AS arte_oficio
                FROM    ".$wbasedato."_000021 as grp
                        LEFT JOIN
                        root_000067 AS par ON (grp.Grupar = par.Parcod)
                        LEFT JOIN
                        root_000066 AS esc ON (grp.Gruesc = esc.Scocod)
                        LEFT JOIN
                        root_000029 AS sex ON (grp.Grugen = sex.Sexcod)
                        LEFT JOIN
                        root_000078 AS ocu ON (grp.Gruocu = ocu.Ocucod)
                WHERE   grp.Gruuse = '".$wuse."'
                        AND grp.Gruest = 'on'
                ORDER BY grp.Grunom, grp.Gruape";

    $rGrpF = mysql_query($qGrpF,$conex);
    $grupo_familiar = array();
    while($row = mysql_fetch_array($rGrpF))
    {
        $comparte = ($row['comparte'] == 'on') ? 'Si': 'No';
        $grupo_familiar[$row['id']] =  array(
                                                'nombres'=>$row['nombres'],'apellidos'=>$row['apellidos'],
                                                'genero'=>$row['genero'],'parentesco'=>$row['parentesco'],
                                                'f_nacimiento'=>$row['f_nacimiento'],'escolaridad'=>$row['escolaridad'],
                                                'ocupacion'=>$row['ocupacion'],'comparte'=>$comparte,
                                                'arte_oficio'=>$row['arte_oficio']);
    }
    $identGeneral['grupo_familiar'] = $grupo_familiar;


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

    // Consulta Id de caracterización
    $queryc = " SELECT  idGen.id, idGen.Idepas AS pasaporte, idGen.Idevis AS visa, idGen.Idepvi AS pais_visa
                        , idGen.Ideesc As estado_civil, idGen.Ideinc AS l_nacimiento
                        , idGen.Idempo AS municipio, idGen.Idebrr AS barrio, idGen.Idesrh AS sangre
                        , idGen.Ideeps AS eps, idGen.Idestt AS estrato, idGen.Idescs AS poliza
                        , idGen.Idetoe AS otro_empleo
                        , idGen.Idefnc AS f_nacimiento, idGen.Idegen AS genero, idGen.Ideno1 AS nombre1, idGen.Ideno2 AS nombre2
                        , idGen.Ideap1 AS apellido1, idGen.Ideap2 AS apellido2, idGen.Ideced AS cedula, idGen.Idedir AS direccion
                        , idGen.Idetel AS telefono, idGen.Idecel AS celular, idGen.Ideeml AS email, idGen.Ideext AS extension
                        , idGen.Ideccg AS cod_cargo, crgs.Carley AS req_estud_ley
                        , idGen.Idecco AS ccosto
                FROM    ".$wbasedato."_000013 AS idGen
                        LEFT JOIN
                        root_000079 AS crgs ON (idGen.Ideccg = crgs.Carcod)
                WHERE   idGen.Ideuse = '$wuse'
                        AND idGen.Ideest = 'on'";
    $rc = mysql_query($queryc,$conex);

    $arr_general = array(   'pasaporte'     =>array('si'=>'', 'no'=>''),
                            'visa'          =>array('si'=>'', 'no'=>''),
                            'pais_visa'     =>array(),
                            'estado_civil'  =>'',
                            'l_nacimiento'  =>'',
                            'municipio'     =>'',
                            'barrio'        =>'',
                            'sangre'        =>'',
                            'estrato'       =>'',
                            'eps'           =>'',
                            'poliza'        =>'',
                            'otro_empleo'   =>array('si'=>'','no'=>'','ver'=>'display:none;'),
                            'f_nacimiento'  =>'',
                            'genero'        =>'',
                            'nombres'       =>'',
                            'cedula'        =>'',
                            'direccion'     =>'',
                            'telefono'      =>'',
                            'celular'       =>'',
                            'email'         =>'',
                            'extension'     =>'',
                            'codigo'        =>'',
                            'cod_cargo'     =>'',
                            'req_estud_ley' =>'',
                            'ccosto'        =>''
                        );
    if(mysql_num_rows($rc) > 0)
    {
        $row = mysql_fetch_array($rc);
        // $wcaracteriza = base64_encode($row['id']);
        $wcaracteriza = $row['id'];

        $displayvisa = 'display:none;';
        $visa_si = '';
        if($row['visa']=='on') { $visa_si = 'checked="checked"'; $displayvisa = 'display:block;'; }
        $visa_no = ($row['visa']=='off') ? 'checked="checked"': '';
        $pasaporte_si = ($row['pasaporte']=='on') ? 'checked="checked"': '';
        $pasaporte_no = ($row['pasaporte']=='off') ? 'checked="checked"': '';

        $nombres = trim($row['nombre1'].' '.$row['nombre2'].' '.$row['apellido1'].' '.$row['apellido2']);

        $empleo_si = '';
        $empleo_no = '';
        $display = 'display:none;';
        if($row['otro_empleo']=='on') { $empleo_si = 'selected="selected"'; $display = 'display:block;'; }
        $empleo_no = ($row['otro_empleo']=='off') ? 'selected="selected"': '';

        $genero = ($row['genero'] == 'M') ? 'Masculino': 'Femenino';

        // Información países de visa
        $paises_v = array();
        if ($row['pais_visa'] != '')
        {
            $explode = explode(',',$row['pais_visa']);
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
                $paises_v[$rowp['cod_pais']] = $rowp['nombre'];
            }
        }

        $arr_general = array(   'pasaporte'     => array('si'=>$pasaporte_si, 'no'=>$pasaporte_no),
                                'visa'          => array('si'=>$visa_si, 'no'=>$visa_no,'ver'=>$displayvisa),
                                'pais_visa'     => array('id'=>$row['id'],'paises'=>$paises_v),
                                'estado_civil'  => $row['estado_civil'],
                                'l_nacimiento'  => $row['l_nacimiento'],
                                'municipio'     => $row['municipio'],
                                'barrio'        => $row['barrio'],
                                'sangre'        => $row['sangre'],
                                'estrato'       => $row['estrato'],
                                'eps'           => $row['eps'],
                                'poliza'        => $row['poliza'],
                                'otro_empleo'   => array('si'=>$empleo_si,'no'=>$empleo_no,'ver'=>$display),
                                'f_nacimiento'  => $row['f_nacimiento'],
                                'genero'        => $genero,
                                'nombres'       => $nombres,
                                'cedula'        => $row['cedula'],
                                'direccion'     => $row['direccion'],
                                'telefono'      => $row['telefono'],
                                'celular'       => $row['celular'],
                                'email'         => $row['email'],
                                'extension'     => $row['extension'],
                                'codigo'        => $wuse,
                                'cod_cargo'     => $row['cod_cargo'],
                                'req_estud_ley' => $row['req_estud_ley'],
                                'ccosto'        => $row['ccosto']
                            );
    }
    $identGeneral['identificacion'] = $arr_general;

    /*********************************
    * Datos Relacion de Escolaridad Ley, cargo y centro de costos
    */
    $qEsco= "   SELECT  Scocod AS id, Carcod AS cargo_ley, Ccocod AS ccosto_ley
                FROM    ".$wbasedato."_000044
                WHERE   Recest = 'on'
                        AND ( Carcod = '".$identGeneral['identificacion']['cod_cargo']."' OR Carcod = '*')
                        AND ( Ccocod = '".$identGeneral['identificacion']['ccosto']."' OR Ccocod = '*')";
    $rEsco = mysql_query($qEsco,$conex);
    $grado_escolar_ley = array();
    while($row = mysql_fetch_array($rEsco))
    {
        $grado_escolar_ley[$row['id']] = array('cargo_ley'=>$row['cargo_ley'],'ccosto_ley'=>$row['ccosto_ley']);
    }
    $identGeneral['grado_escolar_ley'] = $grado_escolar_ley;

    /*********************************
    * Datos de Barrios
    */
    $barrios = array();
    if($identGeneral['identificacion']['municipio'] != '')
    {
        $cod_municipio = $identGeneral['identificacion']['municipio'];
        $qmun = "   SELECT  Barcod AS Codigo, Bardes AS Nombre
                    FROM    root_000034
                    WHERE   Barmun = '".$cod_municipio."'
                    ORDER BY Bardes";
        $rmun = mysql_query($qmun,$conex);

        while($row = mysql_fetch_array($rmun))
        {
            $barrios[$row['Codigo']] = $row['Nombre'];
        }
    }
    $identGeneral['barrios'] = $barrios;

    // Consulta Id de información familiar
    $queryif= " SELECT  id, Famtpd AS discapacitado, Famtms AS mascota, Famcab AS cabeza_familia, Fammac AS menores, Famaac AS adultos
                FROM    ".$wbasedato."_000019
                WHERE   Famuse = '$wuse'
                        AND Famest = 'on'";
    $rif = mysql_query($queryif,$conex);
    $winfofam = '';
    $informacion_familiar = array(  'discapacitado'=>array('si'=>'','no'=>'','ver'=>'display:none;'),'tipo_mascota'=>'','cabeza_familia'=>'','menores'=>'','adultos'=>'');
    if(mysql_num_rows($rif) > 0)
    {
        $row = mysql_fetch_array($rif);
        // $winfofam = base64_encode($row['id']);
        $winfofam = $row['id'];
        $disc_si = '';
        $display = 'display:none;';
        if($row['discapacitado']=='on') { $disc_si = 'selected="selected"'; $display = 'display:block;'; }
        $disc_no = ($row['discapacitado']=='off') ? 'selected="selected"': '';
        $informacion_familiar = array(  'discapacitado'=>array('si'=>$disc_si,'no'=>$disc_no,'ver'=>$display),
                                        'tipo_mascota'=>$row['mascota'],
                                        'cabeza_familia'=>$row['cabeza_familia'],'menores'=>$row['menores'],
                                        'adultos'=>$row['adultos']);
    }
    $identGeneral['informacion_familiar'] = $informacion_familiar;

    /*********************************
    * Datos Familiares con discapacidad
    */
    $qDF = "    SELECT  disc.id, par.Pardes AS parentesco, disc.Diseda AS edad, disc.Disdis AS tip_discapacidad
                FROM    ".$wbasedato."_000020 as disc
                        INNER JOIN
                        ".$wbasedato."_000019 AS grp ON (grp.id = disc.Disinf AND grp.Famuse = '".$wuse."')
                        LEFT JOIN
                        root_000067 AS par ON (disc.Discpa = par.Parcod)
                WHERE   disc.Disest = 'on'";

    $rDF = mysql_query($qDF,$conex);
    $discapacitados = array();
    while($row = mysql_fetch_array($rDF))
    {
        $discapacitados[$row['id']] =  array('parentesco'=>$row['parentesco'],'edad'=>$row['edad'],'tip_discapacidad'=>$row['tip_discapacidad']);
    }
    $identGeneral['discapacitados'] = $discapacitados;

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
    $q = "  SELECT  Empdes,Emptcc
            FROM    root_000050
            WHERE   Empcod = '".$wemp_pmla."'";
    $res = mysql_query($q,$conex);

    $tabla_CCO = '';
    $unidad_servicio = array();
    if($row = mysql_fetch_array($res))
    {
        $tabla_CCO = $row['Emptcc'];
        switch ($tabla_CCO)
        {
            case "clisur_000003":
                    $query="select Ccocod AS codigo, Ccodes AS nombre from clisur_000003 ORDER BY Ccodes";
                    break;
            case "farstore_000003":
                    $query="select Ccocod AS codigo, Ccodes AS nombre from farstore_000003 ORDER BY Ccodes";
                    break;
            case "costosyp_000005":
                    $query="select Ccocod AS codigo, Cconom AS nombre from costosyp_000005 ORDER BY Cconom";
                    break;
            case "uvglobal_000003":
                    $query="select Ccocod AS codigo, Ccodes AS nombre from uvglobal_000003 ORDER BY Ccodes";
                    break;
            default:
                    $query="select Ccocod AS codigo, Cconom AS nombre from costosyp_000005 ORDER BY Cconom";
        }

        $res = mysql_query($query,$conex);

        while($row = mysql_fetch_array($res))
        {
            $unidad_servicio[$row['codigo']] = $row['codigo'].'-'.utf8_encode(ucwords(strtolower($row['nombre'])));
        }
    }
    $identGeneral['unidad_servicio'] = $unidad_servicio;

    /*********************************
    * Datos Lista de cargos
    */
    $qCcg= "    SELECT  Carcod AS id, Cardes AS cargo
                FROM    root_000079
                WHERE   Carest = 'on'
                ORDER BY Cardes";
    $rCcg = mysql_query($qCcg,$conex);
    $cargos = array();
    while($row = mysql_fetch_array($rCcg))
    {
        $cargos[$row['id']] =  utf8_encode($row['cargo']);
    }
    $identGeneral['cargos'] = $cargos;

    /*********************************
    * Datos Servicios en los que laboró
    */
    $qCco= "    SELECT  cgc.id, cgc.Cincco AS centro_costo, cgc.Cintie AS tiempo, cgc.Cincgo AS cod_cargo, cg.Cardes AS cargo, cgc.Cinmot AS motivo
                FROM    ".$wbasedato."_000022 AS cgc
                        LEFT JOIN
                        root_000079 AS cg ON (cgc.Cincgo = cg.Carcod)
                WHERE   cgc.Cinest = 'on'
                        AND cgc.Cinuse = '".$wuse."'
                ORDER BY cgc.Cincco";
    $rCco = mysql_query($qCco,$conex);
    $servicios_laboro = array();
    while($row = mysql_fetch_array($rCco))
    {
        $c_costo = utf8_decode($identGeneral['unidad_servicio'][$row['centro_costo']]);
        $servicios_laboro[$row['id']] =  array('centro_costo'=>$c_costo,'tiempo'=>$row['tiempo'],'cargo'=>$row['cargo'],'motivo'=>$row['motivo']);
    }
    $identGeneral['servicios_laboro'] = $servicios_laboro;

    /*********************************
    * Datos Otros empleos
    */
    $qOemp= "   SELECT  id, Utremp AS empresa, Utrtie AS tiempo, Utrcar AS cargo
                FROM    ".$wbasedato."_000023
                WHERE   Utrest = 'on'
                        AND Utruse = '".$wuse."'
                ORDER BY Utremp";
    $rOemp = mysql_query($qOemp,$conex);
    $otros_empleos = array();
    while($row = mysql_fetch_array($rOemp))
    {
        $otros_empleos[$row['id']] =  array('empresa'=>$row['empresa'],'tiempo'=>$row['tiempo'],'cargo'=>$row['cargo']);
    }
    $identGeneral['otros_empleos'] = $otros_empleos;

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
    * Datos Salarios familiares
    */
    $qTsal= "   SELECT  id, Faming AS salario, Famosl AS observ
                FROM    ".$wbasedato."_000019
                WHERE   Famest = 'on'
                        AND Famuse = '".$wuse."'";
    $rTsal = mysql_query($qTsal,$conex);
    $salario_familiar = array('id'=>'','salario'=>array(),'observs'=>array());
    while($row = mysql_fetch_array($rTsal))
    {
        /*
            La observacion esta concatenada por (codigo_salario|observacion al salario) y cada llave de este tipo
            esta separado por ||
        */
        $expObsTxt = explode('||',$row['observ']);
        $observ_arr = array();

        // este ciclo separa las los codigos del tipo de salario con su respectiva observación almaceneda.
        foreach($expObsTxt as $keyOb => $valorOb)
        {
            $expOb = explode('=>',$valorOb);
            $observ_arr[$expOb[0]] = (array_key_exists(1,$expOb)) ? $expOb[1]: '';
        }

        $salario_familiar =  array('id'=>$row['id'],'salario'=>explode(',',$row['salario']),'observs'=>$observ_arr);
    }
    // echo "<div align='left'><pre>";print_r($salario_familiar);echo '</pre></div>';
    $identGeneral['salario_familiar'] = $salario_familiar;

    // Consulta Id de condiciones de vida
    $qCV= "     SELECT  id, Cviviv AS tenencia, Cvitvi AS tipo_viv, Cvitrz AS terraza, Cvilot AS lote
                        , Cvisvi AS estado_viv, Cvissp AS ss_publico, Cvicre AS credito, Cvitra AS transporte
                        , Cviotr AS otro_trans, Cvidep AS tipo_recreativa, Cvidod AS otra_recrea, Cviaca AS tipo_artes
                        , Cvioac AS otras_artes, Cviapa AS tipo_educativa, Cvihbb AS hobbies
                        , Cvical AS tipo_almuerzo, Cvioal AS otro_almuerzo
                        , Cvicmj AS cod_mejoras, Cviomj AS otra_mejora
                FROM    ".$wbasedato."_000024
                WHERE   Cviuse = '$wuse'";
    $rCV = mysql_query($qCV,$conex);
    $wcondicion = '';
    $condicion_vida = array(    'tenencia'      => '',
                                'tipo_viv'      => '',
                                'terraza'       => '',
                                'lote'          => '',
                                'estado_viv'    => '',
                                'ss_publico'    => array('id'=>'','cond_ss_publico'=>array()), // Adecuado para ckeckbox
                                'credito'       => array('si'=>'','no'=>'','ver'=>'display:none;'), // Selecciona la opción guardada, habilita o inhabilita tabla creditos
                                'transporte'    => array('id'=>'','tipo_transporte'=>array()), // Adecuado para ckeckbox
                                'otro_trans'    => '',
                                'recreativas'   => array('id'=>'','tipo_recreativa'=>array()), // Adecuado para ckeckbox
                                'otra_recrea'   => '',
                                'artes'         => array('id'=>'','tipo_artes'=>array()), // Adecuado para ckeckbox
                                'otras_artes'   => '',
                                'educativas'    => array('id'=>'','tipo_educativa'=>array()), // Adecuado para ckeckbox
                                'hobbies'       => '',
                                'tipo_almuerzo' => array('id'=>'','tipo_almuerzo'=>array()),
                                'otro_almuerzo' => '',
                                'cod_mejoras'   => '',
                                'otra_mejora'   => '');
    if(mysql_num_rows($rCV) > 0)
    {
        $row = mysql_fetch_array($rCV);
        // $wcondicion = base64_encode($row['id']);
        $wcondicion = $row['id'];

        $cred_si = '';
        $display = 'display:none;';
        if($row['credito'] == 'on') { $cred_si = 'selected="selected"'; $display = 'display:block;'; }
        $cred_no = ($row['credito'] == 'off') ? 'selected="selected"': '';

        $condicion_vida = array(    'tenencia'      => $row['tenencia'],
                                    'tipo_viv'      => $row['tipo_viv'],
                                    'terraza'       => $row['terraza'],
                                    'lote'          => $row['lote'],
                                    'estado_viv'    => $row['estado_viv'],
                                    'ss_publico'    => array('id'=>$row['id'],'cond_ss_publico'=>explode(',',$row['ss_publico'])), // Adecuado para ckeckbox
                                    'credito'       => array('si'=>$cred_si,'no'=>$cred_no,'ver'=>$display), // Selecciona la opción guardada, habilita o inhabilita tabla creditos
                                    'transporte'    => array('id'=>$row['id'],'tipo_transporte'=>explode(',',$row['transporte'])), // Adecuado para ckeckbox
                                    'otro_trans'    => $row['otro_trans'],
                                    'recreativas'   => array('id'=>$row['id'],'tipo_recreativa'=>explode(',',$row['tipo_recreativa'])), // Adecuado para ckeckbox
                                    'otra_recrea'   => $row['otra_recrea'],
                                    'artes'         => array('id'=>$row['id'],'tipo_artes'=>explode(',',$row['tipo_artes'])), // Adecuado para ckeckbox
                                    'otras_artes'   => $row['otras_artes'],
                                    'educativas'    => array('id'=>$row['id'],'tipo_educativa'=>explode(',',$row['tipo_educativa'])), // Adecuado para ckeckbox
                                    'hobbies'       => $row['hobbies'],
                                    'tipo_almuerzo' => array('id'=>$row['id'],'tipo_almuerzo'=>explode(',',$row['tipo_almuerzo'])),
                                    'otro_almuerzo' => $row['otro_almuerzo'],
                                    'cod_mejoras'   => array('id'=>$row['id'],'cod_mejoras'=>explode(',',$row['cod_mejoras'])),
                                    'otra_mejora'   => $row['otra_mejora']);
    }
    $identGeneral['condicion_vida'] = $condicion_vida;

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
    * Datos Mejoras de vivienda
    */
    $q = "   SELECT  Mejcod AS id, Mejdes AS mejora
            FROM    ".$wbasedato."_000041
            WHERE   Mejest = 'on'
            ORDER BY Mejdes";
    $res = mysql_query($q,$conex);
    $mejoras_vivienda = array();
    while($row = mysql_fetch_array($res))
    {
        $mejoras_vivienda[$row['id']] =  array('mejora'=>$row['mejora']);
    }
    $identGeneral['mejoras_vivienda'] = $mejoras_vivienda;

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
    * Datos Créditos empleado
    */
    $q = "  SELECT  id, Cremot AS motivo, Creent AS entidad, Creval AS valor, Crecuo AS cuota
            FROM    ".$wbasedato."_000025
            WHERE   Creest = 'on'
                    AND Creuse = '".$wuse."'";
    $res = mysql_query($q,$conex);
    $creditos = array();
    while($row = mysql_fetch_array($res))
    {
        $creditos[$row['id']] =  array('motivo'=>$row['motivo'],'entidad'=>$row['entidad'],'valor'=>$row['valor'],'cuota'=>$row['cuota']);
    }
    $identGeneral['creditos'] = $creditos;

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

    // /*********************************
    // * Datos Roles
    // */
    // $q = "  SELECT  t_rol.Rolcod AS id, t_rol.Roldes AS tipo_rol, t_rol.Rolobr AS observ_requerida
                    // , org.id AS id_participa, org.Oincod AS org_interno, org.Oinobs AS observacion
            // FROM    root_000074 AS t_rol
                    // LEFT JOIN
                    // ".$wbasedato."_000026 as org ON (t_rol.Rolcod = org.Oincod AND org.Oinuse = '".$wuse."')
            // WHERE   t_rol.Rolest = 'on'";
    // $res = mysql_query($q,$conex);
    // $tipo_rol = array();
    // while($row = mysql_fetch_array($res))
    // {
        // $tipo_rol[$row['id']] =  array( 'tipo_rol'=>$row['tipo_rol'],'observ_requerida'=>$row['observ_requerida'],
                                        // 'id_participa'=>$row['id_participa'],'org_interno'=>$row['org_interno'],
                                        // 'observacion'=>$row['observacion']);
    // }
    // $identGeneral['tipo_rol'] = $tipo_rol;

    // /*********************************
    // * Datos Repositorio de preguntas
    // */
    // $q = "  SELECT  reppre.Precod as id, reppre.Predes AS pregunta, reppre.Preraf AS req_afirmacion, reppre.Prerrs AS req_respuesta, reppre.Premsr AS msj_respuesta
                    // , res.id AS id_respuesta, res.Resafi AS afirmacion, res.Resres AS respuesta
            // FROM    ".$wbasedato."_000012 AS reppre
                    // LEFT JOIN
                    // ".$wbasedato."_000027 as res ON (reppre.Precod = res.Rescpr AND res.Resuse = '".$wuse."')
            // WHERE   reppre.Preest = 'on'";
    // $res = mysql_query($q,$conex);
    // $repositorio_preguntas = array();
    // while($row = mysql_fetch_array($res))
    // {
        // $repositorio_preguntas[$row['id']] =  array( 'pregunta'=>$row['pregunta'],'req_respuesta'=>$row['req_respuesta'],'msj_respuesta'=>$row['msj_respuesta'],
                                        // 'id_respuesta'=>$row['id_respuesta'],'req_afirmacion'=>$row['req_afirmacion'],'afirmacion'=>$row['afirmacion'],
                                        // 'respuesta'=>$row['respuesta']);
    // }
    // $identGeneral['repositorio_preguntas'] = $repositorio_preguntas;

    // echo "<div align='left'><pre>";print_r($identGeneral);echo '</pre></div>';
}

/****************************************************************************************************************
*  << MODELO
****************************************************************************************************************/

/****************************************************************************************************************
*  FUNCIONES PHP >>
****************************************************************************************************************/

/**
 * función insertLog()
 * Inserta los eventos generados por los usuarios al momento de actualizar o eliminar datos de su caractarización,
 * estos eventos son guardados en la tabla de log talhuma_000028
 *
 * @param string $wbasedato :   Base de datos en la que se debe guardar
 * @param string $user      :   Código del usuario que está activo en la sesión
 * @param string $accion    :   Acción realizada por el usuario activo => add, update, delete
 * @param string $descripcion:  Descripción textual de lo que se hizo
 * @param string $user      :   Código del usuario para el cual se está editando la caracterización
 * @return null
 */
function insertLog($conex, $wbasedato, $user_session, $accion, $descripcion, $user_update)
{
    $descripcion = str_replace("'",'"',$descripcion);
    $insert = " INSERT INTO ".$wbasedato."_000028
                    (Medico, Fecha_data, Hora_data, Logcdu, Logacc, Logdes, Loguse, Logest, Seguridad)
                VALUES
                    ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".utf8_decode($user_session)."','".utf8_decode($accion)."','".$descripcion."','".$user_update."','on','C-".$user_session."')";
    $res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());
}

/****************************************************************************************************************
*  << FUNCIONES PHP
****************************************************************************************************************/

global $wemp_pmla;
global $wtema;
global $wcodtab;
echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';
echo '<input type="hidden" id="wtema" name="wtema" value="'.$wtema.'" />';
echo '<input type="hidden" id="wcodtab" name="wcodtab" value="'.$wcodtab.'" />';
echo '<input type="hidden" id="wuse" name="wuse" value="'.$wuse.'" />';
echo '<input type="hidden" id="wcaracteriza" name="wcaracteriza" value="'.$wcaracteriza.'" />';
echo '<input type="hidden" id="winfofam" name="winfofam" value="'.$winfofam.'" />';
echo '<input type="hidden" id="wcondicion" name="wcondicion" value="'.$wcondicion.'" />';
echo '<input type="hidden" id="wroles" name="wroles" value="" />';
echo '<input type="hidden" id="contenedorPadre" name="contenedorPadre" value="'.$contenedorPadre.'" />';

$nota_anio = "
    <label style='font-size:10pt;color:#2A5DB0;'>
        <b>Nota:</b> Para seleccionar un a&ntilde;o inferior al 2002
        seleccione el 2002 despliegue de nuevo y se mostrar&aacute;n diez a&ntilde;os m&aacute;s hacia atr&aacute;s.<br />
    </label>";

/**
    Validando, si no existía en talhuma_000013 se deben pedir datos adicionales.
    Esto es para el caso en que es un usuario externo y no existe en UNIX y es la primera vez que ingresa a este formulario,
    Se le piden datos personales para que queden registrados en el sistema.
*/
$identGeneral['identificacion']['notanuevo'] = '';
$identGeneral['identificacion']['notanuevo_borde'] = '';
/*if($primera_vez_car)
{
    $identGeneral['identificacion']['notanuevo_borde'] = 'border:orange 2px solid;';
    $identGeneral['identificacion']['notanuevo'] = '
            <span style="font-weight:bold;color:darkorange;">NOTA: </span>Por favor llene los campos que est&aacute;n resaltados en color rojo, tenga en cuenta que luego de llenar los campos resaltados
            se guardar&aacute;n automaticamente y una vez salga de este formulario, los campos resaltados no podr&aacute;n ser modificados por usted, los dem&aacute;s campos si podr&aacute;n se modificados en otro momento.';
    $identGeneral['identificacion']['nombres'] = '
                <span style="font-size:9pt;">Primer nombre:</span><input class="nuevosdatos" type="text" size="20" maxlength="75" id="nombre1" name="nombre1" value="" onblur="blurCampo(this,\'wcaracteriza\',\'identGeneral\',\'\',\'\',\'\');" in="Ideno1" rel="000013" />
                <span style="font-size:9pt;">Segundo nombre:</span><input class="nuevosdatos" type="text" size="20" maxlength="75" id="nombre2" name="nombre2" value="" onblur="blurCampo(this,\'wcaracteriza\',\'identGeneral\',\'\',\'\',\'\');" in="Ideno2" rel="000013" /><br />
                <span style="font-size:9pt;">Primer apellido:</span><input class="nuevosdatos" type="text" size="20" maxlength="75" id="apellido1" name="apellido1" value="" onblur="blurCampo(this,\'wcaracteriza\',\'identGeneral\',\'\',\'\',\'\');" in="Ideap1" rel="000013" />
                <span style="font-size:9pt;">Segundo apellido:</span><input class="nuevosdatos" type="text" size="20" maxlength="75" id="apellido2" name="apellido2" value="" onblur="blurCampo(this,\'wcaracteriza\',\'identGeneral\',\'\',\'\',\'\');" in="Ideap2" rel="000013" />
                ';

    $identGeneral['identificacion']['f_nacimiento'] = '
                <input class="nuevosdatos" type="text" size="20" id="fecha_nace" name="fecha_nace" value=""  onchange="blurCampo(this,\'wcaracteriza\',\'identGeneral\',\'\',\'\',\'\');" in="Idefnc" rel="000013" />';

    $opg = '<select class="nuevosdatos" name="wgeneroemp" id="wgeneroemp" rel="000013" in="Idegen" onChange="blurCampo(this,\'wcaracteriza\',\'identGeneral\',\'\',\'\',\'\');" >
            <option value="" selected="selected">Seleccione..</option>';
    foreach($identGeneral['genero'] as $key => $value)
    {
        $opg .= "<option value='$key' >".utf8_encode($value)."</option>";
    }
    $opg .= "</select>";
    $identGeneral['identificacion']['genero'] = $opg;

    $identGeneral['identificacion']['cedula'] = '
                <input class="nuevosdatos" type="text" size="20" maxlength="75" id="wcedemp" name="wcedemp" value="" rel="000013" in="Ideced" onkeypress="return soloNumeros(event);" onChange="blurCampo(this,\'wcaracteriza\',\'identGeneral\',\'\',\'\',\'\');" />';
}*/

?>

<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_page.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_table.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_validation.css" rel="stylesheet"/>

<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<!-- <link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/> -->


<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.dataTables.editable.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.checkbox.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.datapicker.js" type="text/javascript"></script>

<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />


<script language="JavaScript" type="text/javascript">

    function mensajeFailAlert(this_, mensaje, xhr, textStatus, errorThrown)
    {
        // console.log(xhr);
        var obj_data = [];
        var data_ajax = this_["data"]
        var data_parcial = data_ajax.substr(0, 200)+"...";
        // console.log(data_parcial);
        var url_err = document.location.href;
        // console.log(url_err);

        var responseText = xhr.responseText.split("{\"");
        var detalle_error= "";
        if(responseText.length > 0)
        {
            detalle_error = responseText[0];
            // console.log(detalle_error);
        }

        var msg_fail = "";
        if (xhr.status === 0) {
            msg_fail = 'No hay conexión: verificar la red.';
            // console.log(msg_fail);
        } else if (xhr.status == 404) {
            msg_fail = 'Página no encontrada [404]';
            // console.log(msg_fail);
        } else if (xhr.status == 500) {
            msg_fail = 'Error interno del servidor [500].';
            // console.log(msg_fail);
        } else if (textStatus === 'parsererror') {
            msg_fail = 'Respuesta JSON falló.';
            // console.log(msg_fail);
        } else if (textStatus === 'timeout') {
            msg_fail = 'Error tiempo de respuesta agotado.';
            // console.log(msg_fail);
        } else if (textStatus === 'abort') {
            msg_fail = 'Respuesta ajax abortada.';
            // console.log(msg_fail);
        } else {
            msg_fail = 'Error desconocido: ' + xhr.responseText;
            // console.log(msg_fail);
        }

        var msj_extra = '';
        msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
        jAlert($("#failJquery").val()+msj_extra, "Mensaje");
        $("#div_error_interno").html(xhr.responseText);

        obj_data.push(msg_fail);
        obj_data.push(data_parcial);
        obj_data.push(detalle_error);
        obj_data.push(url_err);
        console.log(obj_data);

        // $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
        // {
        //     consultaAjax  : '',
        //     wemp_pmla     : $('#wemp_pmla').val(),
        //     accion        : 'insert',
        //     form          : 'guardar_error_ajax_log',
        //     msg_fail      : msg_fail,
        //     data_parcial  : data_parcial,
        //     detalle_error : detalle_error,
        //     url_err       : url_err
        // },function(data){
        //     if(data.error == 1)
        //     {
        //         console.log(data.mensaje);
        //     }
        //     else
        //     {
        //         console.log("Evento guardado en log de errores..");
        //     }
        // },
        // 'json'
        // ).done(function(){
        //     //
        // }).fail(function(xhrLog, textStatusLog, errorThrownLog) { console.log(xhrLog.responseText) });

        // console.log(xhr);
        // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
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
        $('#'+id_hijo).load("caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion="+accion+"&id_padre="+val+"&form="+form+"&wuse="+$("#wuse").val());

        if (vacio != '')
        {
            var elemento = document.getElementById(vacio);
            blurCampo(elemento,'wcaracteriza','identGeneral','','','');
        }
    }

    /**
        Esta función se encarga de resetear otro elemento referenciado por el elemento que desencadena la acción, por ejemplo si se deschequea el tipo de salario "Negocio familiar",
            Este tiene asociado un campo de texto como complemento, al desmarcar el elemento padre, se borra el contenido del elemento hijo.
        id_event:   Es el elemento que genera la acción
     */
    function resetear(id_event, resetea)
    {
        val = $('[name="'+id_event+'"]:checked').val();
        if(val != 'on')
        {
            $('#div_'+resetea).hide('slow');
            $('#'+resetea).val('');
        }
        else
        {
            $('#div_'+resetea).show('slow');
        }
    }

    /**
        Esta función se encarga de resetear otro elemento referenciado por el elemento que desencadena la acción, Este tiene asociado un campo de texto como complemento,
        al desmarcar el elemento padre, se borra el contenido del elemento hijo, a diferencia de la función anterior, este adicionalmente muestra un efecto sobre el div que debe ocultar.
        id_event:   Es el elemento que genera la acción
     */
    function resetear2(id_event, resetea)
    {
        val = $('#'+id_event).val();
        if(val != 'on')
        {
            $('#div_'+resetea).hide('explode',1000);
            $('#'+resetea).val('');
        }
        else
        {
            $('#div_'+resetea).show('slow');
        }
    }

    /**
        Esta función Se encarga de eliminar el contenido de un datatable, por ejemplo en el caso de empleos anteriores, id_event es el id del elemento por ejemplo donde se indica
        que el empleado tiene o no otros empleos anteriores, si se selecciona NO se oculta la tabla de otros empleaos y el datatable es reseteado por si se da el caso en que en el
        elemento padre se seleccione de nuevo que SI ha tenido empleos anteriores entonces no se muestre la información que había antes.

        id_event:       Es el elemento (this) que genera la acción.
        dataTable_clear:Es el identificador de la tabla tipo datatable que debe ser reseteada.
     */
    function clearTable(id_event,dataTable_clear)
    {
        var id = id_event.id;
        var tipo = $('#'+id).get(0).type;
        if (tipo == 'radio')
        {
            val = $('[name="'+id+'"]:checked').val();
        }
        else if(tipo == 'checkbox')
        {
            val = $('[name="'+id+'"]:checked').val();
            if(val != 'on') { val = 'off'}
        }
        else
        {
            val = $("#"+id).val();
        }

        // Si el valor seleccionado en el elemento padre corresponde a off, indica que se debe resetear el datatable que llega en el parámetro dataTable_clear.
        if(val != 'on')
        {
            var oTable = $('#'+dataTable_clear).dataTable();
            oTable.fnClearTable();
        }
    }

    /**
        Esta función se encarga de guardar automáticamente los valores o los datos digitados o seleccionados en casi todo el formulario.
        según el elemento que desencadene esta acción, esta funcion identifica que tipo de campo es (select, checkbox, radio, input), luego de esto, captura el valor de ese campo,
        posteriormente lee los atributos adicionales del campo (ó elemento de html) con el fin de obtener el nombre del campo que debe afectar en la base de datos y tambien recupera
        el nombre de la tabla o sufijo sobre el cual debe ir a modificar el campo cuyo nombre estaba en el otro atributo respectivo al nombre del campo
        (atributos in: Es el nombre del campo a modificar, rel: es el sufijo de la tabla a modificar).

        campo           : Es el id del elemento (this) que desencadena la acción (muchas veces mediante el evento onBlur u onChange).
        id_reg          : Es un elemento, generalemente un campo hidden que tiene el id del registro que debe modificar en la base de datos
        form            : Es el condicional que mediante llamado ajax debe ubicar para llevar a cabo la acción según el tipo de información a modificar.

        -- Los siguientes parametros se envían diferentes de vacío por ejemplo si el valor del campo anterior implica que se debe eliminar toda información asociada a este campo
        -- Un ejemplo es cuando se pregunta "trabajó en otra empresa?" y se responde "SI" luego se asocian las diferentes empresas en una tabla, pero si se selecciona "NO" se debería
        -- Eliminar toda información ingresada en la tabla en que se asociaron las empresas anteriores para ese empleado.

        borra_seccion   : Es el sufijo de la tabla donde se debe buscar y eliminar los registros asociados a la opción que se seleccionó en el primer parámetro.
        DFiltro         : Nombre del campo por el cual se debe buscar el valor a eliminar en la tabla envíada en el parámetro borra_seccion.
        DValor          : Valor que se busca en el DFiltro, los registros que coincidan con este valor son los que se van a eliminar.
     */
    function blurCampo(campo,id_reg,form,borra_seccion,DFiltro,DValor)
    {
        id = campo.id;

        var tipo = $('#'+id).get(0).type;
        if (tipo == 'radio')
        {
            val = $('[name="'+id+'"]:checked').val();
        }
        else if(tipo == 'checkbox')
        {
            val = $('[name="'+id+'"]:checked').val();
            if(val != 'on') { val = 'off'}
        }
        else
        {
            val = $("#"+id).val();
        }

        $.post("caracterizacion.php",
            {
                wemp_pmla:      $('#wemp_pmla').val(),
                wtema:          $('#wtema').val(),
                wuse:           $('#wuse').val(),
                id_registro:    $('#'+id_reg).val(),
                wtabla:         $("#"+id).attr("rel"),
                value:          val,
                consultaAjax:   '',
                accion:         'update',
                form:           form,
                campo:          $("#"+id).attr("in"),
                id_campo:       id,
                delSeccion:     borra_seccion,
                delFiltro:      DFiltro,
                delValor:       $('#'+DValor).val()
            }
            ,function(data) {
                if(data.error == 1)
                {
                    alert(data.mensaje);
                }
                else
                {
                    $("#"+id_reg).val(data.id_registro);
                }
            },
            "json"
        );
    }

    /**
    * Para el caso de negocio familiar por ejemplo, cuando seleccione clic debe actualizar el ID de referencia para el campo de complemento de la selección.
    */
    function actualizaID(actual, actualizar)
    {
        $('#'+actualizar).val($('#'+actual).val());
    }

    /**
        Esta funcion se encarga de dar permisos sobre el formulario, permite modificar los datos del formulario ó solo ver la información si no se tienen permisos para modificar
        ó indica si no se tiene ningún tipo de permiso para acceder a esta información.
    */
    function soloLectura()
    {
        padre = $("#contenedorPadre").val();
        $.post("caracterizacion.php",
            {
                wemp_pmla:      $('#wemp_pmla').val(),
                wtema:          $('#wtema').val(),
                wcodtab:        $('#wcodtab').val(),
                wuse:           $('#wuse').val(),
                consultaAjax:   '',
                accion:         'load',
                form:           'permisos'
            }
            ,function(data) {
            //alert(data.actualizar);
                $('#div_contenedor_caracterizacion').show();
                if(data.error == 1)
                {
                    // Si ocurrió algún error al consultar los permisos.
                    alert(data.mensaje);
                }
                else if(data.permiso == 'consultar') // || data.permiso == 'ninguno'
                {
                    // Si solo tiene permisos de consulta entonces inactiva todos los campos.
                    $("#"+padre+" :input").each(function() {
                        $(this).attr("disabled", true);
                    });
                }
                else if(data.permiso == 'actualizar')
                {
                    // Si tiene permiso de actualizar, entonces se inician todos los datatables y datapicker para ser usados y poder editar.
                    ReadyPlus(); // se cargan como datatables solo si se puede editar, de lo contrario solo será una tabla normal.
                    $("#ui-datepicker-div").hide();//para eliminar borde que aparece al final de la página, solo por crear un datapicker.
                }
                else{
                    // Reemplaza todo el formulario por un mensaje altenativo - un caso por ejemplo para quienes no pueden modificar ni consultar información.
                    $('#div_contenedor_caracterizacion').html(data.html);
                }
            },
            "json"
        );
    }

    function guardarDatosRegistro()
    {
        $("#reg_nombre1").removeClass("campoRequerido");
        $("#reg_apellido1").removeClass("campoRequerido");
        $("#reg_fecha_nace").removeClass("campoRequerido");
        $("#reg_wgeneroemp").removeClass("campoRequerido");
        $("#reg_wcedemp").removeClass("campoRequerido");

        var reg_nombre1    = $("#reg_nombre1").val().toUpperCase();
        var reg_nombre2    = $("#reg_nombre2").val().toUpperCase();
        var reg_apellido1  = $("#reg_apellido1").val().toUpperCase();
        var reg_apellido2  = $("#reg_apellido2").val().toUpperCase();
        var reg_fecha_nace = $("#reg_fecha_nace").val();
        var reg_wgeneroemp = $("#reg_wgeneroemp").val();
        var reg_wcedemp    = $("#reg_wcedemp").val();

        var todo_ok = true;
        if(reg_nombre1.replace(/ /gi,"") == '')
        {
            $("#reg_nombre1").addClass("campoRequerido");
            todo_ok = false;
        }

        if(reg_apellido1.replace(/ /gi,"") == '')
        {
            $("#reg_apellido1").addClass("campoRequerido");
            todo_ok = false;
        }

        if(reg_fecha_nace.replace(/ /gi,"") == '')
        {
            $("#reg_fecha_nace").addClass("campoRequerido");
            todo_ok = false;
        }

        if(reg_wgeneroemp.replace(/ /gi,"") == '')
        {
            $("#reg_wgeneroemp").addClass("campoRequerido");
            todo_ok = false;
        }

        if(reg_wcedemp.replace(/ /gi,"") == '')
        {
            $("#reg_wcedemp").addClass("campoRequerido");
            todo_ok = false;
        }

        if(todo_ok)
        {
            $(":button:contains('Registrar')").attr("disabled","disabled");
            $.post("caracterizacion.php",
            {
                consultaAjax   : '',
                wemp_pmla      : $("#wemp_pmla").val(),
                wtema          : $("#wtema").val(),
                accion         : 'insert_registro',
                form           : 'ajax_json',
                reg_nombre1    : reg_nombre1,
                reg_nombre2    : reg_nombre2,
                reg_apellido1  : reg_apellido1,
                reg_apellido2  : reg_apellido2,
                reg_fecha_nace : reg_fecha_nace,
                reg_wgeneroemp : reg_wgeneroemp,
                reg_wcedemp    : reg_wcedemp,
                wuse           : $("#wuse").val()
            },function(data){
                if(data.error == 1)
                {
                    $(":button:contains('Registrar')").removeAttr("disabled");
                    console.log(data.mensaje);
                    jAlert(data.mensaje,"Mensaje");
                }
                else
                {
                    var tab_cod = $("#wcodtab_tal").val();
                    if($("#id_href_"+tab_cod).length > 0)
                    {
                        $("#id_href_"+tab_cod).trigger("click");
                    }
                    else
                    {
                        jAlert("Recargue el programa para continuar (F5)","Mensaje");
                    }

                    console.log("Evento guardado en log de errores..");
                }
            },
            'json'
            ).done(function(){
                //
            }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert(this, '', xhr, textStatus, errorThrown); });

        }
        else
        {
            var mensaje = 'Campos obligatorios sin diligenciar';
            var colormej = 'red';
            $("#div_mensaje_add_proced").html("<img width='15' height='15' src='../../images/medical/root/info.png' />&nbsp;<span style='color:"+colormej+";'>"+mensaje+"</span>");
            //$("#div_mensaje_add_proced").css({"opacity":" 0.6","fontSize":"12px"});
            $("#div_mensaje_add_proced").hide();

            $("#div_mensaje_add_proced").effect("pulsate", {}, 1000);
                setTimeout(function() {
                $("#div_mensaje_add_proced").hide(500);
            }, 1000);
            return false;
        }
    }

    $(document).ready( function () {
        soloLectura();
        var nuevoEmpleado = $("#wnoexiste_empleado").val();
        if(nuevoEmpleado == 'on')
        {
            $("#tabla_contenido_caracterizacion").hide();
            $("#dv_registro_nuevo_empleado").dialog({
                show: {
                    effect: "blind",
                    duration: 100
                },
                hide: {
                    effect: "blind",
                    duration: 100
                },
                height: 400,
                width:  800,
                buttons: {
                   /* {
                        text: "Registrar",
                        click: function()
                            {
                                guardarDatosRegistro();
                                //$(this).dialog("close");
                            },
                        disabled: true
                    }*/
                    "Registrar": function() {
                        guardarDatosRegistro();
                        // $( this ).dialog( "close" );
                    }
                },
                dialogClass: 'fixed-dialog',
                modal: true,
                title: "Registrar datos b&aacute;sicos del empleado",
                beforeClose: function( event, ui ) {
                    //
                },
                create: function() {
                    $(this).find('.ui-icon-closethick').hide();
                    $(this).closest('.ui-dialog').on('keydown', function(ev) {
                       if (ev.keyCode === $.ui.keyCode.ESCAPE) {
                           // $( "#dv_registro_nuevo_empleado" ).dialog('close');
                       }
                    });
                },
                "closeOnEscape": false,
                "closeX": false,
                open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
            }).on("dialogopen", function( event, ui ) {
                //
            });
        }
        //ReadyPlus();
    } );

    /**
        Función que se encarga de inicar todos los datatables y los datapicker para que puedan funcionar.
        Aquí están todas las tablas que permiten adicionar nuevas filas con nueva información.

        $('#nombre_de_tabla')   : cada tabla se inicia con un identificador de la tabla que va a convertir en datatable.
        sUpdateURL              : Url y parámetros que se ejecutarán y se enviarán cuando se edita cada campo de la tabla.
        sAddURL                 : Url y parámetros que se ejecutarán y se enviarán cuando se ingresa una nueva fila.
        sDeleteURL              : Url y parámetros que se ejecutarán y se enviarán cuando se va a eliminar toda una fila de la tabla.

        Al editar un campo tipo select:
        loadurl: Es la url y los parámetros que darán origen a las opciones que son posibles seleccionar al momento de editar la opción de un select dentro de un datatable.
     */
    function ReadyPlus()
    {
        // Tabla de escolaridad o grados escolares.
        $('#tabla_educativos').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bJQueryUI": true,
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Edugrd"},
                {   sName: "Edutit"},
                {   sName: "Eduins"},
                {   sName: "Eduani","asSorting": [ "asc" ]}
            ]
        }).makeEditable({
            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddEducacion&wuse="+$("#wuse").val(),
            sAddURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddEducacion&wuse="+$("#wuse").val(),
            sAddHttpMethod: "GET",
            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddEducacion&wuse="+$("#wuse").val(),
            sAddNewRowFormId: "formAddEducacion",
            sAddNewRowButtonId: "btnAddEducacion",
            sAddNewRowCancelButtonId: "btnAddEducacionCancel",
            sAddNewRowOkButtonId: "btnAddEducacionOk",
            sDeleteRowButtonId: "btnDeleteEducacion",
            "aoColumns": [
                {   sName: "Edugrd", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                    , type: 'select'
                    , onblur: 'cancel'
                    , submit: 'Ok'
                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddEscolaridad&wuse="+$("#wuse").val()
                    , loadtype: 'GET'
                },
                {   sName: "Edutit", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                {   sName: "Eduins", cssclass: "required",tooltip: 'Doble Clic para editar', placeholder : 'Doble clic..'},
                {   sName: "Eduani", cssclass: "required date",tooltip: 'Doble Clic para editar', type : 'datepicker', placeholder : 'Doble clic..'}
            ]
            ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
            ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /*
            Tabla de estudios que son requisitos de ley por ejemplo para las asistenciales. Esta tabla no se muestra a todos los usuario,
            solo se muestra a quienes tienen el cargo configurado como que requieren estudios o requisitos de ley
            y que a demás su cargo está relacionado con estudios de ley, dicha relación está en la tabla 000044, relaciona el cargo con requeriemiento de ley y el estudio requisito de ley
        */
        $('#tabla_reqley').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bJQueryUI": true,
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   "bVisible": false }, // Se oculta la columna del ID
                {   sName: "Leygrd", cssclass: "required"},
                {   sName: "Leytit", cssclass: "required"},
                {   sName: "Leyins", cssclass: "required"},
                {   sName: "Leyani", cssclass: "required","asSorting": [ "asc" ]}
            ]
        }).makeEditable({
            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddReqLey&wuse="+$("#wuse").val(),
            sAddURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddReqLey&wuse="+$("#wuse").val(),
            sAddHttpMethod: "GET",
            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddReqLey&wuse="+$("#wuse").val(),
            sAddNewRowFormId: "formAddReqLey",
            sAddNewRowButtonId: "btnAddReqLey",
            sAddNewRowOkButtonId: "btnAddReqLeyOk",
            sAddNewRowCancelButtonId: "btnAddReqLeyCancel",
            sDeleteRowButtonId: "btnDeleteReqLey",
            "aoColumns": [
                {   sName: "Leygrd", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                    , type: 'select'
                    , onblur: 'cancel'
                    , submit: 'Ok'
                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddEscolaridadLey&wuse="+$("#wuse").val()
                    , loadtype: 'GET'
                },
                {   sName: "Leytit", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                {   sName: "Leyins", cssclass: "required",tooltip: 'Doble Clic para editar', placeholder : 'Doble clic..'},
                {   sName: "Leyani", cssclass: "required date",tooltip: 'Doble Clic para editar', type : 'datepicker', placeholder : 'Doble clic..'}
            ]
            ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
            ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* DATATABLE Idiomas */
        $('#tabla_idiomas').dataTable({
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bJQueryUI": true,
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                { sName: "Idides", cssclass: "required"},
                { sName: "Idihab", cssclass: "required"},
                { sName: "Idilee", cssclass: "required"},
                { sName: "Idiesc", cssclass: "required"}
            ]
        }).makeEditable({
            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddIdioma&wuse="+$("#wuse").val(),
            sAddURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddIdioma&wuse="+$("#wuse").val(),
            sAddHttpMethod: "GET",
            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddIdioma&wuse="+$("#wuse").val(),
            sAddNewRowFormId: "formAddIdioma",
            sAddNewRowButtonId: "btnAddIdioma",
            sAddNewRowOkButtonId: "btnAddIdiomaOk",
            sAddNewRowCancelButtonId: "btnAddIdiomaCancel",
            sDeleteRowButtonId: "btnDeleteIdioma",
            "aoColumns": [
                {   sName: "Idides", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                {   sName: "Idihab", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..',
                    type: 'select',onblur: 'submit',data: "{'on':'Si', 'off':'No'}"
                    , onblur: 'cancel'
                    , submit: 'Ok'
                },
                {   sName: "Idilee", tooltip: 'Doble Clic para editar', placeholder : 'Doble clic..',
                    type: 'select',onblur: 'submit',data: "{'on':'Si', 'off':'No'}"
                    , onblur: 'cancel'
                    , submit: 'Ok'
                },
                {   sName: "Idiesc", tooltip: 'Doble Clic para editar', placeholder : 'Doble clic..',
                    type: 'select',onblur: 'submit',data: "{'on':'Si', 'off':'No'}"
                    , onblur: 'cancel'
                    , submit: 'Ok'
                }
            ]
            ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
            ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* DATATABLE ESTUDIOS EN LOS QUE ESTÁ ACTUALMENTE */
        $('#tabla_estudios').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Nesdes", cssclass: "required"},
                {   sName: "Nesdur", cssclass: "required"},
                {   sName: "Nesins", cssclass: "required"},
                {   sName: "Nesniv", cssclass: "required"},
                {   sName: "Neshor", cssclass: "required"}
            ],
            "bJQueryUI": true
        }).makeEditable({
            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddEstudio&wuse="+$("#wuse").val(),
            sAddURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddEstudio&wuse="+$("#wuse").val(),
            sAddHttpMethod: "GET",
            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddEstudio&wuse="+$("#wuse").val(),
            sAddNewRowFormId: "formAddEstudio",
            sAddNewRowButtonId: "btnAddEstudio",
            sAddNewRowOkButtonId: "btnAddEstudioOk",
            sAddNewRowCancelButtonId: "btnAddEstudioCancel",
            sDeleteRowButtonId: "btnDeleteEstudio",
            "aoColumns": [
              { sName: "Nesdes", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
              { sName: "Nesdur", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
              { sName: "Nesins", cssclass: "required",tooltip: 'Doble Clic para editar', placeholder : 'Doble clic..'},
              { sName: "Nesniv", cssclass: "required",tooltip: 'Doble Clic para editar', placeholder : 'Doble clic..'},
              { sName: "Neshor", cssclass: "required",tooltip: 'Doble Clic para editar', placeholder : 'Doble clic..'}
            ]
            ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
            ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });


        /* DATATABLE CAPACITACIONES */
        $('#tabla_capacitacion').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Capcod", cssclass: "required"},
                {   sName: "Capesp", cssclass: "required"}
            ],
            "bJQueryUI": true
        }).makeEditable({
                            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddCapacitacion&wuse="+$("#wuse").val(),
                            sAddURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddCapacitacion&wuse="+$("#wuse").val(),
                            sAddHttpMethod: "GET",
                            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddCapacitacion&wuse="+$("#wuse").val(),
                            sAddNewRowFormId: "formAddCapacitacion",
                            sAddNewRowButtonId: "btnAddCapacitacion",
                            sAddNewRowOkButtonId: "btnAddCapacitacionOk",
                            sAddNewRowCancelButtonId: "btnAddCapacitacionCancel",
                            sDeleteRowButtonId: "btnDeleteCapacitacion",
                            "aoColumns": [
                                {   sName: "Capcod"
                                    , tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select'
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddCapacitacion&wuse="+$("#wuse").val()
                                    , loadtype: 'GET'
                                },
                                { sName: "Capesp", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'}
                        ]
                        ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
                        ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* DATATABLE ARTES U OFICIOS */
        $('#tabla_artes').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Oaodes", cssclass: "required"},
                {   sName: "Oaodae"}
            ],
            "bJQueryUI": true, //pone estilo contorno tabla
            "aoColumnDefs": [
                {
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "aTargets": [ -1 ]
                }
            ]
        }).makeEditable({
                            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddArtes&wuse="+$("#wuse").val(),
                            sAddURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddArtes&wuse="+$("#wuse").val(),
                            sAddHttpMethod: "GET",
                            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddArtes&wuse="+$("#wuse").val(),
                            sAddNewRowFormId: "formAddArtes",
                            sAddNewRowButtonId: "btnAddArtes",
                            sAddNewRowOkButtonId: "btnAddArtesOk",
                            sAddNewRowCancelButtonId: "btnAddArtesCancel",
                            sDeleteRowButtonId: "btnDeleteArtes",
                            "aoColumns": [
                                {   sName: "Oaodes", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Oaodae"
                                    , tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select',onblur: 'submit',data: "{'on':'Si', 'off':'No'}"
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                }
                        ]
                        ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
                        ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* DATATABLE GRUPO FAMILIAR */
        $('#tabla_familiar').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Grunom", cssclass: "required"},
                {   sName: "Gruape", cssclass: "required"},
                {   sName: "Grugen"},
                {   sName: "Grupar"},
                {   sName: "Grufna", cssclass: "required"},
                {   sName: "Gruesc"},
                {   sName: "Gruocu"},
                {   sName: "Grucom"},
                {   sName: "Gruart"}
            ],
            "bJQueryUI": true, //pone estilo contorno tabla
            "aoColumnDefs": [
                {
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "sWidth": "50%",
                    "aTargets": [ -1 ]
                }
            ]
        }).makeEditable({
                            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddFamiliar&wuse="+$("#wuse").val(),
                            sAddURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddFamiliar&wuse="+$("#wuse").val(),
                            sAddHttpMethod: "GET",
                            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddFamiliar&wuse="+$("#wuse").val(),
                            sAddNewRowFormId: "formAddFamiliar",
                            sAddNewRowButtonId: "btnAddFamiliar",
                            sAddNewRowOkButtonId: "btnAddFamiliarOk",
                            sAddNewRowCancelButtonId: "btnAddFamiliarCancel",
                            sDeleteRowButtonId: "btnDeleteFamiliar",
                            "aoColumns": [
                                {   sName: "Grunom", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Gruape", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Grugen", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select'
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddGenero&wuse="+$("#wuse").val()
                                    , loadtype: 'GET'
                                },
                                {   sName: "Grupar", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select'
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddParentesco&wuse="+$("#wuse").val()
                                    , loadtype: 'GET'
                                },
                                {   sName: "Grufna", cssclass: "required date",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..', type : 'datepicker'},
                                {   sName: "Gruesc", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select'
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddEscolaridad&wuse="+$("#wuse").val()
                                    , loadtype: 'GET'
                                },
                                {   sName: "Gruocu", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select'
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddOcupaciones&wuse="+$("#wuse").val()
                                    , loadtype: 'GET'
                                },
                                {   sName: "Grucom"
                                    , tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select',onblur: 'submit',data: "{'on':'Si', 'off':'No'}"
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                },
                                {   sName: "Gruart", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'}
                        ]
                        ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
                        ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* DATATABLE DISCAPACITADOS */
        $('#tabla_discapacitado').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Discpa", cssclass: "required"},
                {   sName: "Diseda", cssclass: "required"},
                {   sName: "Disdis", cssclass: "required"}
            ],
            "bJQueryUI": true
        }).makeEditable({
                            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddDiscapacitado&wuse="+$("#wuse").val()+"&winfofam="+$("#winfofam").val(),
                            sAddURL:    "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddDiscapacitado&wuse="+$("#wuse").val()+"&winfofam="+$("#winfofam").val(),
                            sAddHttpMethod: "GET",
                            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddDiscapacitado&wuse="+$("#wuse").val()+"&winfofam="+$("#winfofam").val(),
                            sAddNewRowFormId: "formAddDiscapacitado",
                            sAddNewRowButtonId: "btnAddDiscapacitado",
                            sAddNewRowOkButtonId: "btnAddDiscapacitadoOk",
                            sAddNewRowCancelButtonId: "btnAddDiscapacitadoCancel",
                            sDeleteRowButtonId: "btnDeleteDiscapacitado",
                            "aoColumns": [
                                {   sName: "Grupar", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select'
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddParentesco&wuse="+$("#wuse").val()
                                    , loadtype: 'GET'
                                },
                                {   sName: "Gruape", cssclass: "required digits",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Gruape", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'}
                        ]
                        ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
                        ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* DATATABLE SERVICIOS EN QUE LABORÓ */
        $('#tabla_servicio').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Cincco", cssclass: "required"},
                {   sName: "Cintie", cssclass: "required"},
                {   sName: "Cincgo", cssclass: "required"},
                {   sName: "Cinmot", cssclass: "required"}
            ],
            "bJQueryUI": true
        }).makeEditable({
                            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddServicio&wuse="+$("#wuse").val(),
                            sAddURL:    "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddServicio&wuse="+$("#wuse").val(),
                            sAddHttpMethod: "GET",
                            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddServicio&wuse="+$("#wuse").val(),
                            sAddNewRowFormId: "formAddServicio",
                            sAddNewRowButtonId: "btnAddServicio",
                            sAddNewRowOkButtonId: "btnAddServicioOk",
                            sAddNewRowCancelButtonId: "btnAddServicioCancel",
                            sDeleteRowButtonId: "btnDeleteServicio",
                            "aoColumns": [
                                {   sName: "Cincco", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select'
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddServicioCCO&wuse="+$("#wuse").val()
                                    , loadtype: 'GET'
                                },
                                {   sName: "Cintie", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Cincgo", tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'
                                    , type: 'select'
                                    , onblur: 'cancel'
                                    , submit: 'Ok'
                                    , loadurl: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&form=formAddServicioCCG&wuse="+$("#wuse").val()
                                    , loadtype: 'GET'
                                },
                                {   sName: "Cinmot", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'}
                        ]
                        ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
                        ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* DATATABLE OTROS EMPLEOS */
        $('#tabla_empleo').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Utremp", cssclass: "required"},
                {   sName: "Utrtie", cssclass: "required"},
                {   sName: "Utrcar", cssclass: "required"}
            ],
            "bJQueryUI": true
        }).makeEditable({
                            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddEmpleo&wuse="+$("#wuse").val(),
                            sAddURL:    "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddEmpleo&wuse="+$("#wuse").val(),
                            sAddHttpMethod: "GET",
                            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddEmpleo&wuse="+$("#wuse").val(),
                            sAddNewRowFormId: "formAddEmpleo",
                            sAddNewRowButtonId: "btnAddEmpleo",
                            sAddNewRowOkButtonId: "btnAddEmpleoOk",
                            sAddNewRowCancelButtonId: "btnAddEmpleoCancel",
                            sDeleteRowButtonId: "btnDeleteEmpleo",
                            "aoColumns": [
                                {   sName: "Utremp", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Utrtie", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Utrcar", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'}
                        ]
                        ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
                        ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* DATATABLE CREDITOS */
        $('#tabla_credito').dataTable( {
            "oLanguage": {
                  //"sUrl": "../../../include/root/dataTables.spanish.txt"
            },
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "aoColumns": [
                {   sName: "Cremot", cssclass: "required"},
                {   sName: "Creent", cssclass: "required"},
                {   sName: "Creval", cssclass: "required"},
                {   sName: "Crecuo", cssclass: "required"}
            ],
            "bJQueryUI": true
        }).makeEditable({
                            sUpdateURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=update&form=formAddCredito&wuse="+$("#wuse").val(),
                            sAddURL:    "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=add&form=formAddCredito&wuse="+$("#wuse").val(),
                            sAddHttpMethod: "GET",
                            sDeleteURL: "caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=delete&form=formAddCredito&wuse="+$("#wuse").val(),
                            sAddNewRowFormId: "formAddCredito",
                            sAddNewRowButtonId: "btnAddCredito",
                            sAddNewRowOkButtonId: "btnAddCreditoOk",
                            sAddNewRowCancelButtonId: "btnAddCreditoCancel",
                            sDeleteRowButtonId: "btnDeleteCredito",
                            "aoColumns": [
                                {   sName: "Cremot", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Creent", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Creval", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'},
                                {   sName: "Crecuo", cssclass: "required",tooltip: 'Doble clic para editar', placeholder : 'Doble clic..'}
                        ]
                        ,oAddNewRowButtonOptions: { icons: {primary:'ui-icon-plus' } } // Estilo para el boton adicionar - Comentar si no se quiere el estilo
                        ,oDeleteRowButtonOptions: { icons: {primary:'ui-icon-plus'} }  // Estilo para el boton Eliminar - Comentar si no se quiere el estilo
        });

        /* datapicker para seleccionar año en grado escolar */
        $("#anio_edu").datepicker(
        {
            monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
            nextText: 'Siguiente',
            prevText: 'Anterior',
            closeText: 'Cancelar',
            currentText: 'Hoy',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false,
            dateFormat: 'yy-mm-dd'
        });

        /* datapicker para fecha de nacimiento de familiares */
        $(".dpk_grFam_fnac").datepicker(
        {
            monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            dayNamesMin: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
            nextText: 'Siguiente',
            prevText: 'Anterior',
            closeText: 'Cancelar',
            currentText: 'Hoy',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false,
            dateFormat: 'yy-mm-dd'
        });

        /* datapicker para seleccionar año en grado escolar */
        $("#anio_edu_ley").datepicker(
        {
            monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
            nextText: 'Siguiente',
            prevText: 'Anterior',
            closeText: 'Cancelar',
            currentText: 'Hoy',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false,
            dateFormat: 'yy-mm-dd'
        });

        /* datapicker para seleccionar fecha de nacimiento */
        $("#fecha_nace").datepicker(
        {
            monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
            nextText: 'Siguiente',
            prevText: 'Anterior',
            closeText: 'Cancelar',
            currentText: 'Hoy',
            changeMonth: true,
            changeYear: true,
            showButtonPanel: false,
            dateFormat: 'yy-mm-dd'
        });
    }

    $.datepicker.regional['esp'] = {
        closeText: 'Cerrar',
        prevText: 'Antes',
        nextText: 'Despues',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesShort: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        yearSuffix: '',
        changeYear: true,
        changeMonth: true,
        yearRange: '-100:+0'
    };
    $.datepicker.setDefaults($.datepicker.regional['esp']);


    $("#reg_fecha_nace").datepicker({
        showOn: "button",
        buttonImage: "../../images/medical/root/calendar.gif",
        buttonImageOnly: true,
        maxDate:"+0D"
    });


    /* Cambia el estilo de todos los elementos tipo 'th' */
    $("th").css({
       "background": "#2A5DB0",
       "font-size":"9pt",
       "color":"#FFFFFF",
       "font-weight":"bold"
    });

    /* Cambia el estilo de todos los elementos input disabled */
    $('input:disabled').css({
        "background": "#F2F2F2",
        "border-top": "0px"
    });

    /**
        Esta función actúa si se ha seleccionado la opción "tiene visa: Si", desplegará un div con un seleccionador de países
    */
    function verDivPaises()
    {
        var val = $("input[name='wtienevisa']:checked").val();
        if(val=='on'){
            $('#div_list_pises').show("slow"); // Muestra div para seleccionar países
            $('#div_pises_visa').show("slow"); // Muestra, habilita div para agregar la lista de países en los que tiene visa.
        }
        else {
            $('#div_list_pises').hide("slow");
            $('#div_pises_visa').hide("slow");
            $('#div_pises_visa').load("caracterizacion.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&wtema="+$("#wtema").val()+"&accion=load&id_padre=&form=load_pais_visa&borrar=1&wuse="+$("#wuse").val());
            $('#wpaisvisa').val('');
        }
    }

    /**
        Esta función es usada en condiciones de vida del empleado mas exactamente al seleccionar la opción "Mejoras pendientes" en los estados de vivienda, esta función
        se encarga de habilitar un div en los que se va a mostrar una lista de checkbox de posibles mejoras pendientes.

        id_reg:     Es el id del elemento que desencadena la acción, se recupera el valor seleccionado, se hace un llamado ajax y se veririfica si esa opción tiene a su vez
                    opciones adicionales para que puedan ser seleccionadas, si es así entonces muestra las opciones adicionales que se pueden elegir.
     */
    function verDivMejoras(id_reg)
    {
        $.post(
            "caracterizacion.php",
            {
                consultaAjax:   '',
                wemp_pmla:      $("#wemp_pmla").val(),
                wtema:          $("#wtema").val(),
                accion:         'load',
                form:           'load_mejoras',
                id_estVivienda: $("#westadoviv").val(),
                id_registro:    $("#"+id_reg).val(),
                wuse:           $("#wuse").val()
            },
            function(data){
                if(data.error == 0)
                {
                    if(data.req_complemento == 'on')
                    {
                        // Si la opcion seleccionada requiere complemento, entonces se muestran las demás opciones que se pueden elegir.
                        $("#div_mejoras").show("slow");
                        $("#div_msj_complemento").html(data.mensaje);
                        $("#div_lista_mejoras").html(data.contenido);
                    }
                    else
                    {
                        // Si la opción seleccionada no requiere complemento, se ocultan las opciones de complemento.
                        $("#div_mejoras").hide("slow");
                        $("#div_msj_complemento").html('');
                        $("#div_lista_mejoras").html('');
                        $("#womej").val('');
                    }
                }
                else
                {
                    $("#div_mejoras").hide("slow");
                    alert('No se pudo cargar tipos de mejoras.. ');
                }
            },
            "json");
    }

    /* Guarda y activa informacion de discapacitados */
    $('#tiene_discap').change(function() {
        var val="";
        val = $('#tiene_discap').val();
        if(val=='on'){ $('#div_familiares_discap').show("slow"); }
        else { $('#div_familiares_discap').hide("slow");}
    });

    /* Guarda y activa informacion empleos anteriores */
    $('#otra_empresa').change(function() {
        var val="";
        val = $('#otra_empresa').val();
        if(val=='on'){ $('#div_otroEmpleo').show('slow'); }
        else { $('#div_otroEmpleo').hide('slow') ;}
    });

    /* Guarda y activa datos de créditos */
    $('#whaycredito').change(function() {
        var val="";
        val = $('#whaycredito').val();
        if(val=='on'){ $('#div_Creditos').show('slow'); }
        else { $('#div_Creditos').hide('slow') ;}
    });

    function soloNumeros(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
    }

</script>

<script type="text/javascript">
    function verSeccionCaracterizacion(id){
        $("#"+id).toggle("normal");
    }
</script>
<style type="text/css">
    .mayusculas {text-transform: uppercase}
    .campoRequerido{
        border: 1px orange solid;
        background-color:lightyellow;
    }

    .displCaracterizacion{
        display:none;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
        text-align:left;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #FFFFFF;
        background-color: #666666;
        font-family: verdana;
        font-weight: bold;
        font-size: 10pt;
        text-align: left;
    }
    .backgrd_seccion{
        background-color: #E4E4E4;
    }
    .carBold{
        font-weight:bold;
    }
    .tit_seccion{
        text-align:justify;
    }

    .formulario{
        display:none;
    }

    .nuevosdatos{
        background-color: #ffcccc;
        border: solid 2px red;
    }

</style>
<div align="center" id="div_contenedor_caracterizacion" style="display:none;">
    <div id="actualiza" class="version" style="text-align:right" >Subversi&oacute;n: <?php echo $wactualiz; ?></div>
    <input type="hidden" id="wnoexiste_empleado" name="wnoexiste_empleado" value="<?=(($primera_vez_car) ? 'on':'off')?>">
<table border="0" cellpadding="3" cellspacing="3" id="tabla_contenido_caracterizacion">
    <tr>
        <td align="center">
            <div id="ref_tbidgen" align="center">
                <div id="notanuevo" align="left" style="text-align:justify;background-color:lightblue;width:900px;font-weight:bold;<?=$identGeneral['identificacion']['notanuevo_borde']?>"><?php echo $identGeneral['identificacion']['notanuevo']; ?></div>
                <br />
                <table width="900" border="0" cellspacing="0" cellpadding="0">
                    <tr class="encabezadoTabla">
                        <td><div align="center" style="font-size:15pt"><?php echo utf8_encode($identGeneral['identificacion']['nombres']); ?></div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <table width="900" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="tit_seccion">
                            <a href="#null" onclick="javascript:verSeccionCaracterizacion('div_tbidgen');">
                                <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;IDENTIFICACI&Oacute;N GENERAL
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="div_tbidgen" align="center" class="borderDiv">
                <table width="900" border="0" align="center" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="160">&nbsp;</td>
                            <td width="170">&nbsp;</td>
                            <td width="19">&nbsp;</td>
                            <td width="145">&nbsp;</td>
                            <td width="260">&nbsp;</td>
                        </tr>

                    <tr class="fila2">
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Fecha de nacimiento:</td>
                        <td class="carBold">
                            <!-- <input name="wfnacimiento" type="text" disabled="disabled" id="wfnacimiento" onblur="blurCampo(this);" rel="000013" in="" value="" /> -->
                            &nbsp;<?php echo $identGeneral['identificacion']['f_nacimiento']; ?>
                        </td>
                        <td>&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Genero:</td>
                        <td class="carBold">
                            &nbsp;<?php echo $identGeneral['identificacion']['genero']; ?>
                        </td>
                    </tr>
                    <tr class="fila1">
                        <td class="encabezadoTabla resalto" width="145" style="border-bottom:1px solid;">N&uacute;mero de c&eacute;dula:</td>
                        <td class="carBold">
                            <!-- <input type="text" name="numced" id="numced" rel="000013" in="" onblur="blurCampo(this);" disabled="disabled" value="" /> -->
                            &nbsp;<?php echo $identGeneral['identificacion']['cedula']; ?>
                        </td>
                        <td>&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">C&oacute;digo de nomina:</td>
                        <td class="carBold">
                            <!-- <input type="text" name="wuse" id="wuse"  rel="000013" in="" onblur="blurCampo(this);" disabled="disabled" value="" /> -->
                            &nbsp;<?php echo $identGeneral['identificacion']['codigo']; ?>
                        </td>
                    </tr>
                    <tr class="fila2">
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Tiene pasaporte:</td>
                        <td class="carBold">
                            &nbsp;S&iacute;<input type="radio" name="wtienepas" id="wtienepas" value="on" rel="000013" in="Idepas" onClick="blurCampo(this,'wcaracteriza','identGeneral','','','');" <?=$identGeneral['identificacion']['pasaporte']['si']?> />
                            No<input type="radio" name="wtienepas" id="wtienepas" value="off" rel="000013" in="Idepas" onClick="blurCampo(this,'wcaracteriza','identGeneral','','','');" <?=$identGeneral['identificacion']['pasaporte']['no']?> />
                        </td>
                        <td>&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Tiene visa:</td>
                        <td align="center" class="carBold">
                            S&iacute;<input type="radio" name="wtienevisa" id="wtienevisa"  rel="000013" in="Idevis" onClick="blurCampo(this,'wcaracteriza','identGeneral','','',''); verDivPaises();" value="on" <?=$identGeneral['identificacion']['visa']['si']?> />
                            No<input type="radio" name="wtienevisa" id="wtienevisa" rel="000013" in="Idevis" onClick="blurCampo(this,'wcaracteriza','identGeneral','','',''); verDivPaises();" value="off" <?=$identGeneral['identificacion']['visa']['no']?> />
                        </td>
                    </tr>
                     <tr class="fila1">
                        <td class="encabezadoTabla" style="border-bottom:1px #FFFFFF solid;">&nbsp;</td>
                        <td class="resalto">&nbsp;</td>
                        <td class="resalto">&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px #FFFFFF solid;" valign="top">Pa&iacute;ses con visa:</td>
                        <td valign="top">
                            <div style="width:259px">
                                <div id="div_list_pises" style="<?=$identGeneral['identificacion']['visa']['ver']?>" >
                                    &nbsp;De que pa&iacute;ses
                                    <input type="hidden" id="wvaciopaises" rel="000013" in="Idepvi" value="" >
                                    &nbsp;<select name="wpaisvisa" id="wpaisvisa" rel="" in="" style="width:195px;" onChange="recargarDependiente(this,'div_pises_visa','load','load_pais_visa','');">
                                        <option value="" >Seleccione..</option>
                                        <?php
                                            foreach($identGeneral['paises'] as $key => $value)
                                            {
                                                echo "<option value='$key' >$value</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div id="div_pises_visa" style="<?=$identGeneral['identificacion']['visa']['ver']?>" >
                                    <?php
                                        if(array_key_exists('identificacion', $identGeneral)
                                            && array_key_exists('pais_visa', $identGeneral['identificacion'])
                                            && is_array($identGeneral['identificacion']['pais_visa'])
                                            && array_key_exists('paises', $identGeneral['identificacion']['pais_visa'])
                                            && count($identGeneral['identificacion']['pais_visa']['paises'])>0)
                                        {
                                            foreach($identGeneral['identificacion']['pais_visa']['paises'] as $key => $value)
                                            {
                                                $idReg = $identGeneral['identificacion']['pais_visa']['id'];
                                                $idInput = $idReg.'_'.$key.'_pais_v';
                                                $nom_pais = ucfirst(strtolower($value));

                                                echo '<input type="checkbox" id="'.$idInput.'" name="'.$idInput.'" rel="000013" in="Idepvi" value="on" onClick="blurCampo(this,\'wcaracteriza\',\'salarios\',\'\',\'\',\'\');" checked="checked" />&nbsp;'.$nom_pais.'<br />';

                                            }
                                        }
                                        else
                                        {
                                            echo "Ninguno";
                                        }
                                    ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="fila2">
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Estado civil:</td>
                        <td>
                            &nbsp;<select name="westadocivil" id="westadocivil" rel="000013" in="Ideesc" onChange="blurCampo(this,'wcaracteriza','identGeneral','','','');" >
                                <option value="">Seleccione..</option>
                                <?php
                                    foreach($identGeneral['estado_civil'] as $key => $est_c)
                                    {
                                        $ckd = ($identGeneral['identificacion']['estado_civil']==$key) ? 'selected="selected"' : '';
                                        echo "<option value='$key' ".$ckd." >$est_c</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Direcci&oacute;n de vivienda:</td>
                        <td>
                            &nbsp;<input maxlength="75" type="text" name="wdirvive" id="wdirvive" rel="000013" in="Idedir" onblur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?=$identGeneral['identificacion']['direccion']?>" />
                        </td>
                    </tr>
                    <tr class="fila1">
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Lugar de nacimiento:</td>
                        <td>&nbsp;<input maxlength="75" type="text" name="wlugarnac" id="wlugarnac" rel="000013" in="Ideinc" onblur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?=$identGeneral['identificacion']['l_nacimiento']?>" ></td>
                        <td>&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Estrato:</td>
                        <td>&nbsp;<input type="text" name="westrato" id="westrato" rel="000013" in="Idestt" onKeyPress="return soloNumeros(event);" onblur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?=$identGeneral['identificacion']['estrato']?>" /></td>
                    </tr>
                    <tr class="fila2">
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Municipio de residencia:</td>
                        <td>
                            &nbsp;<select name="wmuni" id="wmuni" rel="000013" in="Idempo" onChange="blurCampo(this,'wcaracteriza','identGeneral','','',''); recargarDependiente(this,'wbarrio','load','load_barrio','wvacio');" >
                                <option value="" >Seleccione..</option>
                                <?php
                                    foreach($identGeneral['municipios'] as $key => $value)
                                    {
                                        $ckd = ($identGeneral['identificacion']['municipio']==$key) ? 'selected="selected"' : '';
                                        echo "<option value='$key' $ckd >".ucwords(strtolower($value))."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Barrio:</td>
                        <td>
                            <input type="hidden" id="wvacio" rel="000013" in="Idebrr" value="" >
                            &nbsp;<select name="wbarrio" id="wbarrio" rel="000013" in="Idebrr" onChange="blurCampo(this,'wcaracteriza','identGeneral','','','');" >
                                <option>Seleccione..</option>
                                <?php
                                    foreach($identGeneral['barrios'] as $key => $value)
                                    {
                                        $ckd = ($identGeneral['identificacion']['barrio']==$key) ? 'selected="selected"' : '';
                                        echo "<option value='$key' $ckd >".ucwords(strtolower($value))."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="fila1">
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">N&uacute;mero telef&oacute;nico:</td>
                        <td>&nbsp;<input type="text" name="wnumtel" id="wnumtel" rel="000013" in="Idetel" onblur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?php echo $identGeneral['identificacion']['telefono']; ?>" /></td>
                        <td>&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Celular: </td>
                        <td>&nbsp;<input type="text" name="wcel" id="wcel" rel="000013" in="Idecel" onblur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?php echo $identGeneral['identificacion']['celular']; ?>" /></td>
                    </tr>
                    <tr class="fila2">
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Correo electr&oacute;nico:</td>
                        <td>&nbsp;<input name="wmail" type="text" id="wmail" rel="000013" in="Ideeml" onblur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?php echo $identGeneral['identificacion']['email']; ?>" /></td>
                        <td>&nbsp;</td>
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Tipo de sangre: </td>
                        <td>&nbsp;<input type="text" name="wsangr" id="wsangr" rel="000013" in="Idesrh" onblur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?=$identGeneral['identificacion']['sangre']?>" /></td>
                    </tr>
                    <tr class="fila1">
                        <td class="encabezadoTabla resalto" style="border-bottom:1px solid;">Extensi&oacute;n:</td>
                        <td>&nbsp;<input name="wextensioncaract" type="text" id="wextensioncaract" rel="000013" in="Ideext" onblur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?php echo $identGeneral['identificacion']['extension']; ?>" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                </table>
            </div>
            <br/>
            <br/>
            <div id="ref_educacion" align="center">
                <table width="900" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="tit_seccion">
                            <a href="#null" onclick="javascript:verSeccionCaracterizacion('div_educacion');">
                                <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;EDUCACI&Oacute;N
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="div_educacion" align="center" class='borderDiv displCaracterizacion'>
                <div id="div_msjGradosEsc" class="backgrd_seccion">
                     <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">Nivel educativo (Si usted tiene una educaci&oacute;n superior, relacione todas las que tenga)
                            </td>
                        </tr>
                    </table>
                </div>
                <br />
                <div id="div_nivedu">
                    <table align="center" width="800" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td colspan="1">
                                <div id="div_infoIdiomas">
                                    <!-- FORMULARIO PARA INGRESAR NIVELES EDUCATIVOS -->
                                    <form id="formAddEducacion" action="#" title="Educaci&oacute;n - Grado Escolar" style="width:240px;min-width:240px" class="formulario">
                                        <label for="grado_edu">Grado escolar</label><br />
                                            <select id="grado_edu" name="grado_edu" rel="0" >
                                            <?php
                                                $cont = 0;
                                                foreach($identGeneral['grado_escolar'] as $key => $value)
                                                {
                                                    if($value['req_deLey'] == 'off')
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' $ck >".utf8_encode($value['nombre'])."</option>";
                                                        $cont++;
                                                    }
                                                }
                                            ?>
                                            </select>
                                        <br />
                                        <label for="titulo_edu">T&iacute;tulo obtenido</label><br />
                                            <input type="text" name="titulo_edu" id="titulo_edu" class="required" rel="1" />
                                        <br />
                                        <label for="inst_edu">Nombre de la instituci&oacute;n</label><br />
                                            <input type="text" name="inst_edu" id="inst_edu" class="required" rel="2" />
                                        <br />
                                        <label for="anio_edu">Fecha</label><br />
                                            <input type="text" name="anio_edu" id="anio_edu" class="required date" rel="3" />
                                        <br />
                                        <br />
                                        <?php echo $nota_anio; ?>
                                        <!--<button id="btnAddEducacionCancel" value="cancel">Cancelar</button>
                                                                                <button id="btnAddEducacionOk" value="Ok">Adicionar nivel educativo</button>-->
                                    </form>

                                    <table border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteEducacion">Borrar </button>
                                        <button id="btnAddEducacion">Adicionar nuevo</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_educativos" style="width:800px;">
                                            <thead>
                                                <tr>
                                                    <th>GRADO ESCOLAR</th>
                                                    <th>T&Iacute;TULO OBTENIDO</th>
                                                    <th>NOMBRE DE LA INSTITUCI&Oacute;N</th>
                                                    <th>FECHA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($niveles_educacion as $key => $cols_value)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($cols_value as $key_col => $value_col)
                                                        {
                                                            echo '  <td>'.utf8_encode($value_col).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                    </table><!-- TERMINA INGRESO DE NIVELES EDUCATIVOS-->
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                if($identGeneral['identificacion']['req_estud_ley'] == 'on' && (count($identGeneral['grado_escolar_ley']) > 0 ))
                {
                ?>
                <br />
                <div id="div_msjEscLey" class="backgrd_seccion">
                     <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">Requerimientos de ley (Si usted tiene estudios terminados de requerimientos de ley relaci&oacute;nelos aqu&iacute;)
                            </td>
                        </tr>
                    </table>
                </div>
                <br />
                <div id="div_msj_obligatorioRLey" style="color:red;font-size:9pt; font-weight:bold;" align="left">
                    * Es estrictamente necesario que ingrese por lo menos un estudio de requisito de ley.<br />&nbsp;&nbsp;&nbsp;&Oacute; ingrese los &uacute;ltimos estudios de requisitos de ley realizados.
                </div>
                <br />
                <div id="div_eduley">
                    <table align="center" width="800" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td colspan="1">
                                <div id="div_infoEstLey">
                                    <!-- FORMULARIO PARA INGRESAR ESTUDIOS REQUERIMIENTOS DE LEY -->
                                    <form id="formAddReqLey" action="#" title="Requerimientos de ley" style="width:240px;min-width:240px" class="formulario">
                                        <label for="id_edu_ley"></label><br />
                                            <input type="hidden" name="id_edu_ley" id="id_edu_ley" rel="0" />
                                        <br />
                                        <label for="grado_edu_ley">Grado escolar</label><br />
                                            <select id="grado_edu_ley" name="grado_edu_ley" rel="1" >
                                            <?php
                                                $cont = 0;
                                                $grados_ley = $identGeneral['grado_escolar_ley'];
                                                foreach($identGeneral['grado_escolar'] as $key => $value)
                                                {
                                                    if($value['req_deLey'] == 'on' && array_key_exists($key,$grados_ley))
                                                    {
                                                        $int_ext = '';
                                                        if(strtoupper(strtolower($value['interno_externo'])) == 'I') { $int_ext = '(Req. Interno)'; }
                                                        if(strtoupper(strtolower($value['interno_externo'])) == 'E') { $int_ext = '(Req. Externo)'; }
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' $ck >".utf8_encode($value['nombre']).' '.$int_ext."</option>";
                                                        $cont++;
                                                    }
                                                }
                                            ?>
                                            </select>
                                        <br />
                                        <label for="titulo_edu_ley">T&iacute;tulo obtenido</label><br />
                                            <input type="text" name="titulo_edu_ley" id="titulo_edu_ley" class="required" rel="2" />
                                        <br />
                                        <label for="inst_edu_ley">Nombre de la instituci&oacute;n</label><br />
                                            <input type="text" name="inst_edu_ley" id="inst_edu_ley" class="required" rel="3" />
                                        <br />
                                        <label for="anio_edu_ley">Fecha</label><br />
                                            <input type="text" name="anio_edu_ley" id="anio_edu_ley" class="required" rel="4" />
                                        <br />
                                        <br />
                                        <?php echo $nota_anio; ?>
                                    </form>

                                    <table border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteReqLey">Borrar </button>
                                        <button id="btnAddReqLey">Adicionar nuevo</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_reqley" style="width:800px;">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>GRADO ESCOLAR</th>
                                                    <th>T&Iacute;TULO OBTENIDO</th>
                                                    <th>NOMBRE DE LA INSTITUCI&Oacute;N</th>
                                                    <th>FECHA</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($requerimientos_ley as $key => $cols_value)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($cols_value as $key_col => $value_col)
                                                        {
                                                            echo '  <td>'.utf8_encode($value_col).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </td>
                                    </tr>
                                    </table><!-- TERMINA INGRESO DE REQUERIMIENTOS DE LEY -->
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                } // fin si, requiere ingresar información de estudios de ley
                ?>
                &nbsp;
                <div id="div_oIdioms" class="backgrd_seccion">
                     <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">Manejo de otros idiomas
                            </td>
                        </tr>
                    </table>
                </div>
                <br />
                <div id="div_idiomas">
                    <table align="center" width="500" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td colspan="4">
                                <div id="div_idioms">
                                    <!-- FORMULARIO PARA INGRESAR IDIOMAS -->
                                    <form id="formAddIdioma" action="#" title="Nuevo Idioma" style="width:240px;min-width:240px" class="formulario">
                                        <label for="idio_des">Nombre dioma</label><br />
                                            <input type="text" name="idio_des" id="idio_des" class="required" rel="0" />
                                        <br /><br />
                                            <label for="idio_habla">Lo habla</label>
                                            <select id="idio_habla" name="idio_habla" rel="1" >
                                                <option value="on" selected="selected">Si</option>
                                                <option value="off">No</option>
                                            </select>
                                        <br /><br />
                                            <label for="idio_lee">Lo lee</label>
                                            <select id="idio_lee" name="idio_lee" rel="2" >
                                                <option value="on" selected="selected">Si</option>
                                                <option value="off">No</option>
                                            </select>
                                        <br /><br />
                                            <label for="idio_escribe">Lo escribe</label>
                                            <select id="idio_escribe" name="idio_escribe" rel="3" >
                                                <option value="on" selected="selected">Si</option>
                                                <option value="off">No</option>
                                            </select>
                                        <br />
                                    </form>

                                    <table border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteIdioma">Borrar Idioma</button>
                                        <button id="btnAddIdioma">Adicionar Idioma</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_idiomas" style="width:800px;" >
                                            <thead>
                                                <tr>
                                                    <th>IDIOMA</th>
                                                    <th>LO HABLA</th>
                                                    <th>LO LEE</th>
                                                    <th>LO ESCRIBE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($identGeneral['idiomas'] as $key => $valueI)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($valueI as $keyI => $valueI)
                                                        {
                                                            echo '  <td>'.utf8_encode($valueI).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
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
                <br/>
                <div id="div_estudiaok" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">
                                Estudios actuales (Si usted estudia actualmente, relacione todos los estudios en los que est&eacute;)
                            </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <div id="div_nuevosEstudios">
                    <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div id="div_estudios">
                                    <!-- FORMULARIO PARA INGRESAR ESTUDIOS -->
                                    <form id="formAddEstudio" action="#" title="Nuevo Estudio" style="width:240px;min-width:240px" class="formulario">
                                        <label for="estu_des">Qu&eacute; estudia</label><br />
                                            <input type="text" name="estu_des" id="estu_des" class="required" rel="0" />
                                        <br />
                                            <label for="estu_dur">Duraci&oacute;n</label><br />
                                            <input type="text" name="estu_dur" id="estu_dur" rel="1" />
                                        <br />
                                            <label for="estu_inst">Instituci&oacute;n educativa</label><br />
                                            <input type="text" name="estu_inst" id="estu_inst" class="required" rel="2" />
                                        <br />
                                            <label for="estu_niv">Nivel actual</label><br />
                                            <input type="text" name="estu_niv" id="estu_niv" rel="3" />
                                        <br />
                                            <label for="estu_hor">Horario</label><br />
                                            <input type="text" name="estu_hor" id="estu_hor" rel="4" />
                                        <br />
                                    </form>
                                    <table border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteEstudio">Borrar Estudio</button>
                                        <button id="btnAddEstudio">Adicionar Estudio</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_estudios" style="width:800px;" >
                                            <thead>
                                                <tr>
                                                    <th>Qu&eacute; estudia</th>
                                                    <th>Duraci&oacute;n</th>
                                                    <th>Instituci&oacute;n educativa</th>
                                                    <th>Nivel actual</th>
                                                    <th>Horario</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($identGeneral['estudios'] as $key => $value)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($value as $keyI => $valueI)
                                                        {
                                                            echo '  <td>'.utf8_encode($valueI).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
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
                <br/>
                <div id="div_m" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">Para realizar mejor su trabajo, qu&eacute; capacitaci&oacute;n necesita: </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <div id="div_cont_capacita">
                    <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div id="div_capacita">
                                    <!-- FORMULARIO PARA INGRESAR ESTUDIOS -->
                                    <form id="formAddCapacitacion" action="#" title="Nuevo tipo de capacitaci&oacute;n" style="width:240px;min-width:240px" class="formulario">
                                        <label for="cap_des">Tipo de capacitaci&oacute;n</label><br />
                                            <select style="width:205px;" id="cap_tip" name="cap_tip" rel="0" >
                                                <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['tip_capacitacion'] as $key => $value)
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' ".$ck.">".utf8_encode($value)."</option>";
                                                        $cont++;
                                                    }
                                                ?>
                                            </select>
                                        <br />
                                            <label for="cap_que">Especifique en qu&eacute;</label><br />
                                            <input type="text" name="cap_que" id="cap_que" class="required" rel="1" />
                                        <br />
                                    </form>
                                    <table border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteCapacitacion">Borrar seleccionado</button>
                                        <button id="btnAddCapacitacion">Adicionar capacitaci&oacute;n</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_capacitacion" style="width:800px;" >
                                            <thead>
                                                <tr>
                                                    <th>Tipo de capacitaci&oacute;n</th>
                                                    <th>Especifique en qu&eacute;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($identGeneral['cap_requeridas'] as $key => $value)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($value as $keyI => $valueI)
                                                        {
                                                            echo '  <td>'.utf8_encode($valueI).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
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
                <br />
                <div id="div_msjOtofics" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">Qu&eacute; otros tipos de artes u oficios sabe: </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <div id="div_Otofics">
                    <table align="center"width="600" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div id="div_artes">
                                    <!-- FORMULARIO PARA INGRESAR ARTES -->
                                    <form id="formAddArtes" action="#" title="Arte u oficio" style="width:240px;min-width:240px" class="formulario">
                                        <label for="art_des">Arte u oficio</label><br />
                                            <input type="text" name="art_des" id="art_des" class="required" rel="0" />
                                        <br />
                                            <label for="art_comparte">Estar&iacute;a dispuesto a ense&ntilde;ar este arte u oficio a sus compa&ntilde;eros</label><br />
                                            <select id="art_comparte" name="art_comparte" rel="1" >
                                                <option value="on" selected="selected">Si</option>
                                                <option value="off">No</option>
                                            </select>
                                        <br />
                                    </form>
                                    <table border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteArtes">Borrar seleccionado</button>
                                        <button id="btnAddArtes">Adicionar nuevo</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_artes" style="width:800px;" >
                                            <thead>
                                                <tr>
                                                    <th style="width:60%;" >Arte u oficio</th>
                                                    <th style="width:40%;" >Estar&iacute;a dispuesto a ense&ntilde;ar este arte u oficio a sus compa&ntilde;eros</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($identGeneral['artes_oficios'] as $key => $value)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($value as $keyI => $valueI)
                                                        {
                                                            echo '  <td>'.utf8_encode($valueI).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
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
            <br/>
            <br/>
            <div id="ref_familia" align="center">
                <table width="900" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="tit_seccion">
                            <a href="#null" onclick="javascript:verSeccionCaracterizacion('div_familia');">
                                <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;INFORMACI&Oacute;N FAMILIAR
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="div_familia" align="center"  class='borderDiv displCaracterizacion'>
                <div id="div_msjVivecon" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">Datos familiares</td>
                        </tr>
                    </table>
                </div>
                <table align="center" width="200" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="encabezadoTabla" align="center" colspan="2">&iquest;Con qui&eacute;n vive usted?</td>
                    </tr>
                    <?php
                        if(count($identGeneral['tipo_acompanantes']) > 0)
                        {
                            $fil = 0;
                            $css = '';
                            foreach($identGeneral['tipo_acompanantes'] as $key => $value)
                            {
                                $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                $fil++;
                                $chk = ($key == $value['acompanante']) ? 'checked="checked"' : '';
                                echo '  <tr class="'.$css.'">
                                            <td>'.utf8_encode($value['t_acompanante_des']).'</td>
                                            <td><input type="radio" id="vive_con" name="vive_con" rel="000019" in="Famaco" value="'.$key.'" onClick="blurCampo(this,\'winfofam\',\'identGeneral\',\'\',\'\',\'\');" '.$chk.' /></td>
                                        </tr>';
                            }
                        }
                        else
                        {
                            echo '  <tr class="fila2">
                                        <td class="parrafo1" align="center">[?] No existe informaci&oacute;n para seleccionar los tipos de personas que viven con usted.</td>
                                    </tr>';
                        }
                    ?>
                </table>
                <br/>
                <table align="center" width="550" border="0" cellspacing="0" cellpadding="0">
                    <tr class="encabezadoTabla">
                        <td width="190"><div align="center">&iquest;Es usted cabeza de familia?</div></td>
                        <td width="175"><div align="center">N&uacute;mero de ni&ntilde;os a cargo</div></td>
                        <td width="177"><div align="center">N&uacute;mero de adultos a cargo</div></td>
                    </tr>
                    <tr class="fila1">
                        <td><div align="center">
                            <?php $chkCF = ($identGeneral['informacion_familiar']['cabeza_familia'] == 'on')? 'checked="checked"': ''; ?>
                            <input type="checkbox"  name="wcabezfamilia" id="wcabezfamilia" <?=$chkCF ?> rel="000019" in="Famcab"  onClick="blurCampo(this,'winfofam','identGeneral','','','');" value="on" />
                        </div></td>
                        <td>
                            <div align="center">
                                <input name="wnumninoscargo" type="text" id="wnumninoscargo" rel="000019" in="Fammac"  onBlur="blurCampo(this,'winfofam','identGeneral','','','');" size="10" value="<?php echo $identGeneral['informacion_familiar']['menores']; ?>" />
                            </div>            </td>
                        <td>
                            <div align="center">
                                <input name="wnumadultcargo" type="text" id="wnumadultcargo" rel="000019" in="Famaac"  onBlur="blurCampo(this,'winfofam','identGeneral','','','');" size="10" value="<?php echo $identGeneral['informacion_familiar']['adultos']; ?>" />
                            </div>            </td>
                    </tr>
                </table>
                <br/>
                <div id="div_msjNucleo" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">Relacione en el siguiente cuadro las personas que conforman su n&uacute;cleo familiar, si tiene hijos por favor relaci&oacute;nelos as&iacute; no vivan con usted.</td>
                        </tr>
                    </table>
                </div>
                <br/>
                <div id="div_nucleo">
                    <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div id="div_familiares">
                                    <!-- FORMULARIO PARA INGRESAR FAMILIARES -->
                                    <form id="formAddFamiliar" action="#" title="Nuevo integrante familiar" style="width:240px;min-width:240px" class="formulario">
                                        <label for="grFam_nom">Nombres</label><br />
                                            <input type="text" name="grFam_nom" id="grFam_nom" class="required" rel="0" />
                                        <br />
                                        <label for="grFam_apl">Apellidos</label><br />
                                            <input type="text" name="grFam_apl" id="grFam_apl" class="required" rel="1" />
                                        <br />
                                        <label for="grFam_gen">Genero</label><br />
                                            <select id="grFam_gen" name="grFam_gen" rel="2" >
                                                <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['genero'] as $key => $value)
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' ".$ck.">".utf8_encode($value)."</option>";
                                                        $cont++;
                                                    }
                                                ?>
                                            </select>
                                        <br />
                                        <label for="grFam_paren">Parentesco</label><br />
                                            <select id="grFam_paren" name="grFam_paren" rel="3" >
                                                <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['parentescos'] as $key => $value)
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' ".$ck.">".utf8_encode($value)."</option>";
                                                        $cont++;
                                                    }
                                                ?>
                                            </select>
                                        <br />
                                        <label for="grFam_fnac">Fecha nacimiento</label><br />
                                            <input type="text" name="grFam_fnac" id="grFam_fnac" rel="4" class="dpk_grFam_fnac date" />
                                        <br />
                                        <label for="grFam_niv">Nivel educativo</label><br />
                                            <select id="grFam_niv" name="grFam_niv" rel="5" >
                                               <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['grado_escolar'] as $key => $value)
                                                    {
                                                        if($value['req_deLey'] == 'off')
                                                        {
                                                            $ck = ($cont == 0) ? 'selected="selected"': '';
                                                            echo "<option value='$key' ".$ck." >".utf8_encode($value['nombre'])."</option>";
                                                            $cont++;
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        <br />
                                        <label for="grFam_ocup">Ocupaci&oacute;n</label><br />
                                            <select id="grFam_ocup" name="grFam_ocup" rel="6" >
                                               <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['ocupaciones'] as $key => $value)
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' ".$ck." >".utf8_encode($value)."</option>";
                                                        $cont++;
                                                    }
                                                ?>
                                            </select>
                                        <br />
                                        <label for="grFam_vcon">Vive con usted</label><br />
                                            <select id="grFam_vcon" name="grFam_vcon" rel="7" >
                                                <option value="on" selected="selected">Si</option>
                                                <option value="off">No</option>
                                            </select>
                                        <br />
                                        <label for="grFam_art">Qu&eacute; arte u oficio sabe</label><br />
                                            <input type="text" name="grFam_art" id="grFam_art" rel="8" />
                                        <br />
                                        <br />
                                        <?php echo $nota_anio; ?>
                                    </form>
                                    <table border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteFamiliar">Borrar seleccionado</button>
                                        <button id="btnAddFamiliar">Adicionar nuevo</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_familiar" style="width:800px;" >
                                            <thead>
                                                <tr>
                                                    <th>Nombres</th>
                                                    <th>Apellidos</th>
                                                    <th>Genero</th>
                                                    <th>Parentesco</th>
                                                    <th>Fecha nacimiento</th>
                                                    <th>Nivel educativo</th>
                                                    <th>Ocupaci&oacute;n</th>
                                                    <th>Vive con usted</th>
                                                    <th>Arte u oficio</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($identGeneral['grupo_familiar'] as $key => $value)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($value as $keyI => $valueI)
                                                        {
                                                            echo '  <td>'.utf8_encode($valueI).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
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
                <br/>
                <div id="div_msjDiscapacitados">
                    <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="encabezadoTabla" align="center">
                                En su grupo familiar tiene personas con alg&uacute;n tipo de discapacidad.

                            </td>
                            <td class="fila1" align="center" >
                                <select id="tiene_discap" name="tiene_discap" rel="000019" in="Famtpd" onChange="blurCampo(this,'winfofam','identGeneral','000020','Disinf','winfofam'); clearTable(this,'tabla_discapacitado');" >
                                    <option value="">Seleccione..</option>
                                    <option value="on" <?=$identGeneral['informacion_familiar']['discapacitado']['si']?> >Si</option>
                                    <option value="off" <?=$identGeneral['informacion_familiar']['discapacitado']['no']?> >No</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <div id="div_discapacitados" >
                    <table align="center" width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div id="div_familiares_discap" style="<?=$identGeneral['informacion_familiar']['discapacitado']['ver']?>" >
                                    <!-- FORMULARIO PARA INGRESAR FAMILIARES DISCAPACITADOS -->
                                    <form id="formAddDiscapacitado" action="#" title="Familiar con discapacidad" style="width:240px;min-width:240px" class="formulario">
                                        <label for="discap_parent">Parentesco</label><br />
                                             <select id="discap_parent" name="discap_parent" rel="0" >
                                                <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['parentescos'] as $key => $value)
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key'>".utf8_encode($value)."</option>";
                                                        $cont++;
                                                    }
                                                ?>
                                            </select>
                                        <br />
                                        <label for="discap_edad">Edad (A&ntilde;os)</label><br />
                                            <input type="text" name="discap_edad" id="discap_edad" class="required digits" rel="1" />
                                        <br />
                                        <label for="discap_tipo">Tipo discapacidad</label><br />
                                            <input type="text" name="discap_tipo" id="discap_tipo" class="required" rel="2" />
                                        <br />
                                        <br />
                                    </form>
                                    <table align="center" border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteDiscapacitado">Borrar seleccionado</button>
                                        <button id="btnAddDiscapacitado">Adicionar nuevo</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_discapacitado" style="width:800px;" >
                                            <thead>
                                                <tr>
                                                    <th>Parentesco</th>
                                                    <th>Edad (A&ntilde;os)</th>
                                                    <th>Tipo de discapacidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($identGeneral['discapacitados'] as $key => $value)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($value as $keyI => $valueI)
                                                        {
                                                            echo '  <td>'.utf8_encode($valueI).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
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
                <br/>
                <table align="center" width="500" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="300" class="encabezadoTabla">Tiene mascota, de qu&eacute; tipo</td>
                        <td width="200" class="fila2">
                            <div align="center">
                                <input name="wmascotatip" type="text" id="wmascotatip" rel="000019" in="Famtms" onBlur="blurCampo(this,'winfofam','identGeneral','','','');" size="40" value="<?php echo $identGeneral['informacion_familiar']['tipo_mascota']; ?>" />
                            </div></td>
                    </tr>
                </table>
                <br/>
                <div id="div_msjDiscapacitados" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">
                                <div align="" class="parrafo1">SALUD</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="103" class="encabezadoTabla">EPS actual</td>
                        <td width="391" class="fila2">
                            &nbsp;<select name="weps" id="weps" rel="000013" in="Ideeps" onChange="blurCampo(this,'wcaracteriza','identGeneral','','','');" >
                                <option>Seleccone..</option>
                                <?php
                                    foreach($identGeneral['lista_eps'] as $key => $value)
                                    {
                                        $ckd = ($identGeneral['identificacion']['eps'] == $key) ? 'selected="selected"': '';
                                        echo "<option value='$key' $ckd >".utf8_encode($value)."</option>";
                                    }
                                ?>
                            </select>
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
                            <div align="center">
                                <input name="wpoliza" type="text" id="wpoliza" size="75" rel="000013" in="Idescs" onBlur="blurCampo(this,'wcaracteriza','identGeneral','','','');" value="<?=$identGeneral['identificacion']['poliza']?>" />
                                </div>
                        </td>
                    </tr>
                </table>
            </div>
            <br/>
            <br/>
            <div id="ref_empleoclinica" align="center">
                <table width="900" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="tit_seccion">
                            <a href="#null" onclick="javascript:verSeccionCaracterizacion('div_empleoclinica');">
                                <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;DATOS DEL EMPLEADO CON RESPECTO A LA CL&Iacute;NICA
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="div_empleoclinica" align="center" class='borderDiv displCaracterizacion'>
                <div id="div_msjLaboro" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">Servicios donde ha laborado.</td>
                        </tr>
                    </table>
                </div>
                <br/>
                <div id="div_laboro">
                    <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div id="div_servico_laboro">
                                    <!-- FORMULARIO PARA INGRESAR SERVICIOS LABORADOS -->
                                    <form id="formAddServicio" action="#" title="Nuevo Servicio" style="width:240px;min-width:240px" class="formulario">
                                        <label for="serv_nombre">Unidad o servicio</label><br />
                                            <select id="serv_nombre" name="serv_nombre" class="required" rel="0" style="width: 400px;" >
                                                <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['unidad_servicio'] as $key => $value)
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' ".$ck." >".$value."</option>";
                                                        $cont++;
                                                    }
                                                ?>
                                            </select>
                                        <br />
                                        <label for="serv_tiempo">Tiempo</label><br />
                                            <input type="text" name="serv_tiempo" id="serv_tiempo" class="required" rel="1" />
                                        <br />
                                        <label for="serv_cargo">Cargo</label><br />
                                            <select id="serv_cargo" name="serv_cargo" class="required" rel="2" style="width: 400px;" >
                                                <?php
                                                    $cont = 0;
                                                    foreach($identGeneral['cargos'] as $key => $value)
                                                    {
                                                        $ck = ($cont == 0) ? 'selected="selected"': '';
                                                        echo "<option value='$key' ".$ck." >".$value."</option>";
                                                        $cont++;
                                                    }
                                                ?>
                                            </select>
                                        <br />
                                        <label for="serv_motivo">Motivo del cambio</label><br />
                                            <input type="text" name="serv_motivo" id="serv_motivo" class="required" rel="3" />
                                        <br />
                                        <br />
                                    </form>
                                    <table border='0' style="width: 800px;">
                                    <tr>
                                    <td>
                                        <button id="btnDeleteServicio">Borrar seleccionado</button>
                                        <button id="btnAddServicio">Adicionar nuevo</button>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>
                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_servicio" style="width:800px;" >
                                            <thead>
                                                <tr>
                                                    <th>Unidad o servicio</th>
                                                    <th>Tiempo</th>
                                                    <th>Cargo</th>
                                                    <th>Motivo del cambio</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $filaCnt = 0;
                                                    foreach($identGeneral['servicios_laboro'] as $key => $value)
                                                    {
                                                        $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                        echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                        foreach($value as $keyI => $valueI)
                                                        {
                                                            echo '  <td>'.utf8_encode($valueI).'</td>';
                                                        }
                                                        echo '</tr>';
                                                        $filaCnt++;
                                                    }
                                                ?>
                                            </tbody>
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
                <br />
                <div id="div_msjOtroEmpleo">
                    <table align="center" width="515" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="encabezadoTabla" align="center">
                                Ha trabajado en otras empresas anteriores a la cl&iacute;nica
                            </td>
                            <td class="fila1" align="center" >
                                <select id="otra_empresa" name="otra_empresa" rel="000013" in="Idetoe" onChange="blurCampo(this,'wcaracteriza','identGeneral','000023','Utruse','wuse'); clearTable(this,'tabla_empleo');" >
                                    <option value="">Seleccione..</option>
                                    <option value="on" <?=$identGeneral['identificacion']['otro_empleo']['si']?> >Si</option>
                                    <option value="off" <?=$identGeneral['identificacion']['otro_empleo']['no']?> >No</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <div id="div_otroEmpleo" style="<?=$identGeneral['identificacion']['otro_empleo']['ver']?>" >
                    <div id="div_msjOtroMjsEmpleo" class="" style="align:center;">
                        <table width="550" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="parrafo1">
                                    Relacione los dos empleos anteriores a su vinculaci&oacute;n a la Cl&iacute;nica.
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br/>
                    <div id="div_infoEmpleos">
                        <table align="center" width="900" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <div id="div_empleos">
                                        <!-- FORMULARIO PARA INGRESAR OTROS EMPLEOS -->
                                        <form id="formAddEmpleo" action="#" title="Otro empleo" style="width:240px;min-width:240px" class="formulario">
                                            <label for="empl_empresa">Empresa</label><br />
                                                 <input type="text" name="empl_empresa" id="empl_empresa" class="required" rel="0" />
                                            <br />
                                            <label for="empl_tiempo">Tiempo</label><br />
                                                <input type="text" name="empl_tiempo" id="empl_tiempo" class="required" rel="1" />
                                            <br />
                                            <label for="empl_cargo">Cargo</label><br />
                                                <input type="text" name="empl_cargo" id="empl_cargo" class="required" rel="2" />
                                            <br />
                                            <br />
                                        </form>
                                        <table align="center" border='0' style="width: 800px;">
                                        <tr>
                                        <td>
                                            <button id="btnDeleteEmpleo">Borrar seleccionado</button>
                                            <button id="btnAddEmpleo">Adicionar nuevo</button>
                                        </td>
                                        </tr>
                                        <tr>
                                        <td>
                                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_empleo" style="width:800px;" >
                                                <thead>
                                                    <tr>
                                                        <th>Empresa</th>
                                                        <th>Tiempo</th>
                                                        <th>Cargo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $filaCnt = 0;
                                                        foreach($identGeneral['otros_empleos'] as $key => $value)
                                                        {
                                                            $filaC = ($filaCnt%2 == 0) ? 'fila1': 'fila2';
                                                            echo '<tr class="odd_gradeX '.$filaC.'" id="'.$key.'">';
                                                            foreach($value as $keyI => $valueI)
                                                            {
                                                                echo '  <td>'.utf8_encode($valueI).'</td>';
                                                            }
                                                            echo '</tr>';
                                                            $filaCnt++;
                                                        }
                                                    ?>
                                                </tbody>
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
                <br/>
                        <table align="center" width="500" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="345" class="encabezadoTabla" align="justify">&iquest;Recibe usted o su familia otro tipo de ingreso diferente al salario?</td>
                                <td width="149" class="fila2">
                                    <div align="center">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <?php
                                                if(count($identGeneral['tipo_salarios']) > 0)
                                                {
                                                    $fil = 0;
                                                    $css = '';
                                                    $salario_familiar = $identGeneral['salario_familiar'];
                                                    foreach($identGeneral['tipo_salarios'] as $key => $value)
                                                    {
                                                        $ech = '';
                                                        $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                                        $fil++;

                                                        $reset = "resetear('".$salario_familiar['id']."_".$key."_salario_fam','0_".$key."_salobs');";
                                                        $chk = (in_array($key,$salario_familiar['salario'])) ? 'checked="checked"' : '';
                                                        $ver = (in_array($key,$salario_familiar['salario'])) ? 'display:block;' : 'display:none;';
                                                        $ech .= '  <tr class="'.$css.'">
                                                                        <td width="88" align="left">&nbsp;'.utf8_encode($value['tipo_salario']).'</td>
                                                                        <td width="32">
                                                                            <input type="hidden" id="sal_text_'.$key.'" name="sal_text_'.$key.'" value="'.$salario_familiar['id'].'" />
                                                                            <input type="checkbox" id="'.$salario_familiar['id'].'_'.$key.'_salario_fam" name="'.$salario_familiar['id'].'_'.$key.'_salario_fam" rel="000019" in="Faming" value="on" onClick="blurCampo(this,\'winfofam\',\'salarios\',\'\',\'\',\'\'); '.$reset.'" '.$chk.' />
                                                                        </td>';

                                                        $ech .=  '      <td align="center">';

                                                        /**
                                                            Si la opción pintada ántes, requiere complemento entonces se crea un div oculto donde se creá un campo para ingresar la
                                                            información adicional a esa opción chequeada, los ids de todos estos elementos html están asocidados al id de la opción pintada $key,
                                                            ese código se concatena al id del elemento, es por esto que cuando se desencadena un evento, en el llamado ajax es usual hacer un explode
                                                            para recuperar el id en caso de necesitarlo, como por ejemplo para saber a quién le debe corresponder el comentario u observación adicional
                                                            a la opción o al checkbox pintado (chequeado o deschequeado).

                                                            Nota: se creo la función actualizaID en el onkyepress, porque el campo de observación tiene asociado un campo hidden donde se guarda el id
                                                            del registro en la base de datos que se usa como referencia para poder modificar en la base de datos, en algunos casos ese campo hidden estaba
                                                            quedando vacío entonces cada vez que se seleccionaba un checkbox se generaba un nuevo registro en la tabla pero se supone que solo debe haber un
                                                            solo registro por usuario en la tabla 000019, el campo Faming y Famosl, deben ser codigos u observaciones que deben quedar concatenados en un solo
                                                            campo y no tiene porque generar y nuevo registro cada vez para un mismo usuario.
                                                        */
                                                        if ($value['req_complemento'] == 'on')
                                                        {
                                                            $funcion = "blurCampo(this,'sal_text_".$key."','salario_obs','','','');";

                                                            $msjCom = $value['mjs_complemento'];

                                                            $obs = (array_key_exists($key,$salario_familiar['observs']) && $salario_familiar['observs'][$key] != '') ? $salario_familiar['observs'][$key] : '';
                                                            $ech .= '   <div id="div_0_'.$key.'_salobs" class="encabezadoTabla" style="width:280px;'.$ver.'">
                                                                            <label style="font-size: 10pt;" for="0_'.$key.'_salobs" >'.utf8_encode($msjCom).':</label><br />
                                                                            <textarea cols="33" rows="2" style="font-size: 8pt;" name="0_'.$key.'_salobs" id="0_'.$key.'_salobs" rel="000019" in="Famosl" onkeypress="actualizaID(\'winfofam\',\'sal_text_'.$key.'\');" onBlur="'.$funcion.'" >'.utf8_encode($obs).'</textarea>
                                                                        </div>';
                                                        }
                                                        else { $ech .= '&nbsp;';}

                                                        $ech .= '        </td>
                                                                    </tr>';

                                                        echo $ech;
                                                    }
                                                }
                                                else
                                                {
                                                    echo '  <tr class="fila2">
                                                                <td class="parrafo1" align="center">[?] No existe informaci&oacute;n.</td>
                                                            </tr>';
                                                }
                                            ?>
                                        </table>
                                    </div>
                                 </td>
                            </tr>
                        </table>
            </div>
            <br/>
            <br/>
            <div id="ref_condvida" align="center">
                <table width="900" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="tit_seccion">
                            <a href="#null" onclick="javascript:verSeccionCaracterizacion('div_condvida');">
                                <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;CONDICIONES DE VIDA DEL EMPLEADO
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="div_condvida" align="center" class='borderDiv displCaracterizacion'>
                <div id="div_msjVivienda" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">
                                VIVIENDA
                            </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <table align="center" width="" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="195" class="encabezadoTabla" style="border-bottom:1px solid;">Tenencia de vivienda</td>
                        <td width="180" class="fila1">
                            <div align="center">
                                <select name="wtenenciaviv" id="wtenenciaviv" style="width:160px" rel="000024" in="Cviviv" onChange="blurCampo(this,'wcondicion','identGeneral','','','');" >
                                    <option value="" >Seleccione..</option>
                                    <?php
                                        foreach($identGeneral['tenencia_vivienda'] as $key => $value)
                                        {
                                            $ckd = ($identGeneral['condicion_vida']['tenencia'] == $key) ? 'selected="selected"': '';
                                            echo "<option value='$key' $ckd >".utf8_encode($value['tenencia'])."</option>";
                                        }
                                    ?>
                                </select>
                            </div></td>
                    </tr>
                    <tr>
                        <td class="encabezadoTabla" style="border-bottom:1px solid;">Tipo de vivienda</td>
                        <td class="fila2">
                            <div align="center">
                                <select name="wtipoviv" style="width:160px" id="wtipoviv" rel="000024" in="Cvitvi" onChange="blurCampo(this,'wcondicion','identGeneral','','','');" >
                                    <option value="" >Seleccione..</option>
                                    <?php
                                        foreach($identGeneral['tipo_vivienda'] as $key => $value)
                                        {
                                            $ckd = ($identGeneral['condicion_vida']['tipo_viv'] == $key) ? 'selected="selected"': '';
                                            echo "<option value='$key' $ckd >".utf8_encode($value['t_vivienda'])."</option>";
                                        }
                                    ?>
                                </select>
                            </div></td>
                    </tr>
                    <tr>
                        <td class="encabezadoTabla" style="border-bottom:1px solid;">Tiene usted terraza propia</td>
                        <td class="fila1">
                            <div align="center">
                                <?php $chk = ($identGeneral['condicion_vida']['terraza'] == 'on')? 'checked="checked"': ''; ?>
                                <input type="checkbox" name="wterraza" id="wterraza" <?=$chk?> rel="000024" in="Cvitrz" onClick="blurCampo(this,'wcondicion','identGeneral','','','');" value="on" />
                            </div></td>
                    </tr>
                    <tr>
                        <td class="encabezadoTabla" style="border-bottom:1px solid;">Tiene usted lote propio</td>
                        <td class="fila2">
                            <div align="center">
                                <?php $chk = ($identGeneral['condicion_vida']['lote'] == 'on')? 'checked="checked"': ''; ?>
                                <input type="checkbox" name="wlote" id="wlote" <?=$chk?> rel="000024" in="Cvilot" onClick="blurCampo(this,'wcondicion','identGeneral','','','');" value="on" />
                            </div></td>
                    </tr>
                    <tr>
                        <td class="encabezadoTabla" style="border-bottom:1px solid;">Estado de la vivienda</td>
                        <td class="fila1" style="border-bottom: #FFFFFF 1px solid;">
                            <div align="center">
                                <table width="" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="fila1" align="center">
                                            <select name="westadoviv" id="westadoviv" style="width:160px" rel="000024" in="Cvisvi" onChange="blurCampo(this,'wcondicion','identGeneral','','','');verDivMejoras('wcondicion');" >
                                                <option value="" >Seleccione..</option>
                                                <?php
                                                    $req_comp = false;
                                                    $ver_mejora = 'display:none;';
                                                    $msj_comp = '&nbsp;';
                                                    foreach($identGeneral['estados_vivienda'] as $key => $value)
                                                    {
                                                        $ckd = '';
                                                        if($identGeneral['condicion_vida']['estado_viv'] == $key)
                                                        {
                                                            $ckd = 'selected="selected"';
                                                            if($identGeneral['estados_vivienda'][$key]['req_complemento']=='on')
                                                            {
                                                                $req_comp = true;
                                                                $ver_mejora = 'display:block;';
                                                                $msj_comp = $identGeneral['estados_vivienda'][$key]['msj_complemento'];
                                                            }
                                                        }
                                                        echo "<option value='$key' $ckd >".utf8_encode($value['estado'])."</option>";
                                                    }
                                                ?>
                                            </select>
                                            <div id="div_mejoras" style="<?=$ver_mejora?>">
                                                <br />
                                                <table width="" border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td class="encabezadoTabla">
                                                            <div id="div_msj_complemento" style="text-align:center;">
                                                                <?=$msj_comp?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fila2" align="left">
                                                            <div id="div_lista_mejoras">
                                                                <?php
                                                                    if($req_comp)
                                                                    {
                                                                        if(count($identGeneral['mejoras_vivienda']) > 0)
                                                                        {
                                                                            foreach($identGeneral['mejoras_vivienda'] as $key => $value)
                                                                            {
                                                                                $cod_mejoras_empleado = $identGeneral['condicion_vida']['cod_mejoras'];
                                                                                $chk = (in_array($key,$cod_mejoras_empleado['cod_mejoras'])) ? 'checked="checked"' : '';
                                                                                $idCk = '_'.$key.'_mej';
                                                                                echo '<input type="checkbox" id="'.$idCk.'" name="'.$idCk.'" rel="000024" in="Cvicmj" value="on" onClick="blurCampo(this,\'wcondicion\',\'salarios\',\'\',\'\',\'\');" '.$chk.' />&nbsp;'.utf8_encode($value['mejora']).'<br />';
                                                                            }
                                                                        }
                                                                        else
                                                                        {
                                                                            echo '<div class="parrafo1" style="background-color: #E4E4E4" align="center">[?] No hay tipos de mejoras para seleccionar.</div>';
                                                                        }
                                                                    }
                                                                    else
                                                                    { echo '&nbsp;'; }
                                                                ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="encabezadoTabla">
                                                            &nbsp;Otra, Cu&aacutel?
                                                        </td>
                                                    </tr>
                                                     <tr>
                                                        <td class="encabezadoTabla" align="center" >
                                                            <input type="text" name="womej" id="womej" rel="000024" in="Cviomj" onblur="blurCampo(this,'wcondicion','identGeneral','','','');" value="<?=utf8_encode($identGeneral['condicion_vida']['otra_mejora'])?>" />
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            &nbsp;
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="encabezadoTabla">Acceso a servicios p&uacute;blicos</td>
                        <td align="center" class="fila2">
                            <table width="95%" border="0" cellspacing="0" cellpadding="0">
                                <?php
                                    if(count($identGeneral['servicios_publicos']) > 0)
                                    {
                                        $fil = 0;
                                        $css = '';
                                        $cond_ss_publicos = $identGeneral['condicion_vida']['ss_publico'];
                                        foreach($identGeneral['servicios_publicos'] as $key => $value)
                                        {
                                            $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                            $fil++;
                                            $chk = (in_array($key,$cond_ss_publicos['cond_ss_publico'])) ? 'checked="checked"' : '';
                                            echo '  <tr class="'.$css.'">
                                                        <td width="88" align="left">'.utf8_encode($value['servicio_publico']).'</td>
                                                        <td width="32">
                                                            <input type="checkbox" id="'.$cond_ss_publicos['id'].'_'.$key.'_ss_publico" name="'.$cond_ss_publicos['id'].'_'.$key.'_ss_publico" rel="000024" in="Cvissp" value="on" onClick="blurCampo(this,\'wcondicion\',\'salarios\',\'\',\'\',\'\');" '.$chk.' />
                                                        </td>
                                                    </tr>';
                                        }
                                    }
                                    else
                                    {
                                        echo '  <tr class="fila2">
                                                    <td class="parrafo1" align="center">[?] No existe informaci&oacute;n.</td>
                                                </tr>';
                                    }
                                ?>
                            </table>
                        </td>
                    </tr>
                </table>
                <br/>
                <div id="div_msjCreditos" class="backgrd_seccion">
                        <table width="900" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="parrafo1">
                                    CR&Eacute;DITOS
                                </td>
                            </tr>
                        </table>
                </div>
                <div id="div_msjOtroCreditos">
                    <table align="center" width="500" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="encabezadoTabla">
                                Actualmente tiene usted alg&uacute;n credito.
                            </td>
                            <td class="fila1" align="center">
                                <select id="whaycredito" name="whaycredito" rel="000024" in="Cvicre" onChange="blurCampo(this,'wcondicion','identGeneral','000025','Creuse','wuse'); clearTable(this,'tabla_credito');" >
                                    <option value="" >Seleccione..</option>
                                    <option value="on" <?=$identGeneral['condicion_vida']['credito']['si']?> >Si</option>
                                    <option value="off" <?=$identGeneral['condicion_vida']['credito']['no']?> >No</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <br/>
                <div id="div_Creditos" style="<?=$identGeneral['condicion_vida']['credito']['ver']?>" >
                    <div id="div_mjsInfoCreditos" style="align:center" >
                        <table width="500" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td class="parrafo1">
                                    Relacione los datos de cr&eacute;ditos que tenga actualmente.
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br />
                    <div id="div_infoCredito">
                        <table align="center" width="900" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <div id="div_cred" >
                                        <!-- FORMULARIO CREDITOS -->
                                        <form id="formAddCredito" action="#" title="Creditos" style="width:240px;min-width:240px" class="formulario">
                                            <label for="cred_motivo">Motivo</label><br />
                                                 <input type="text" name="cred_motivo" id="cred_motivo" class="required" rel="0" />
                                            <br />
                                            <label for="cred_entidad">Entidad y/u otro</label><br />
                                                <input type="text" name="cred_entidad" id="cred_entidad" class="required" rel="1" />
                                            <br />
                                            <label for="cred_valor">Valor total del cr&eacute;dito</label><br />
                                                <input type="text" name="cred_valor" id="cred_valor" class="required" rel="2" />
                                            <br />
                                            <label for="cred_cuota">Cuota mensual</label><br />
                                                <input type="text" name="cred_cuota" id="cred_cuota" class="required" rel="3" />
                                            <br />
                                            <br />
                                        </form>
                                        <table align="center" border='0' style="width: 800px;">
                                        <tr>
                                        <td>
                                            <button id="btnDeleteCredito">Borrar seleccionado</button>
                                            <button id="btnAddCredito">Adicionar nuevo</button>
                                        </td>
                                        </tr>
                                        <tr>
                                        <td>
                                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="tabla_credito" style="width:800px;" >
                                                <thead>
                                                    <tr>
                                                        <th>Motivo</th>
                                                        <th>Entidad y/u otro</th>
                                                        <th>Valor total del cr&eacute;dito</th>
                                                        <th>Cuota mensual</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        foreach($identGeneral['creditos'] as $key => $value)
                                                        {
                                                            echo '<tr class="odd_gradeX fila1" id="'.$key.'">';
                                                            foreach($value as $keyI => $valueI)
                                                            {
                                                                echo '  <td>'.utf8_encode($valueI).'</td>';
                                                            }
                                                            echo '</tr>';
                                                        }
                                                    ?>
                                                </tbody>
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
                <br />
                <br />
                <div id="div_mjsTransporte" class="backgrd_seccion" >
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">
                                TRANSPORTE
                            </td>
                        </tr>
                    </table>
                </div>
                <br />
                <div id="div_infoTransporte">
                    <table align="center" width="600" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="441" class="encabezadoTabla">El transporte habitual que usted utiliza para ir a la Cl&iacute;nica es</td>
                            <td width="153" class="">
                                <div align="center">
                                    <table width="100%" border="0" cellspacing="0" cellpadding="2">
                                        <?php
                                            if(count($identGeneral['transporte']) > 0)
                                            {
                                                $fil = 0;
                                                $css = '';
                                                $transporte = $identGeneral['condicion_vida']['transporte'];
                                                foreach($identGeneral['transporte'] as $key => $value)
                                                {
                                                    $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                                    $fil++;
                                                    $chk = (in_array($key,$transporte['tipo_transporte'])) ? 'checked="checked"' : '';
                                                    echo '  <tr class="'.$css.'">
                                                                <td width="88" align="left">'.utf8_encode($value['tipo_transporte']).'</td>
                                                                <td width="32">
                                                                    <input type="checkbox" id="'.$transporte['id'].'_'.$key.'_transporte" name="'.$transporte['id'].'_'.$key.'_transporte" rel="000024" in="Cvitra" value="on" onClick="blurCampo(this,\'wcondicion\',\'salarios\',\'\',\'\',\'\');" '.$chk.' />
                                                                </td>
                                                            </tr>';
                                                }
                                            }
                                            else
                                            {
                                                echo '  <tr class="fila2">
                                                            <td class="parrafo1" align="center">[?] No existe informaci&oacute;n.</td>
                                                        </tr>';
                                            }
                                        ?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2"><div align="left" class="encabezadoTabla">&nbsp;&nbsp;&nbsp;Otro &iquest;Cu&aacute;l?</div>
                                <div align="center" class="fila2">
                                <input name="wotrotransporte" type="text" id="wotrotransporte" size="90" rel="000024" in="Cviotr" onBlur="blurCampo(this,'wcondicion','identGeneral','','','');" value="<?=$identGeneral['condicion_vida']['otro_trans']?>" />
                            </div></td>
                        </tr>
                    </table>
                </div>
                <br>
                <br>
                <div id="div_mjsOtros" class="backgrd_seccion" >
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">
                                INTER&Eacute;S GENERAL
                            </td>
                        </tr>
                    </table>
                </div>
                <br />
                <table align="center" width="650" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="459" class="encabezadoTabla">Usted habitualmente a la hora del almuerzo</td>
                        <td width="185">
                            <div align="center">
                                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                                    <?php
                                        if(count($identGeneral['tipo_almuerzo']) > 0)
                                        {
                                            $fil = 0;
                                            $css = '';
                                            $alimentacion = $identGeneral['condicion_vida']['tipo_almuerzo'];
                                            foreach($identGeneral['tipo_almuerzo'] as $key => $value)
                                            {
                                                $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                                $fil++;
                                                $chk = (in_array($key,$alimentacion['tipo_almuerzo'])) ? 'checked="checked"' : '';
                                                echo '  <tr class="'.$css.'">
                                                            <td width="88" align="left">'.utf8_encode($value['tipo_almuerzo']).'</td>
                                                            <td width="32">
                                                                <input type="checkbox" id="'.$alimentacion['id'].'_'.$key.'_alimentacion" name="'.$alimentacion['id'].'_'.$key.'_alimentacion" rel="000024" in="Cvical" value="on" onClick="blurCampo(this,\'wcondicion\',\'salarios\',\'\',\'\',\'\');" '.$chk.' />
                                                            </td>
                                                        </tr>';
                                            }
                                        }
                                        else
                                        {
                                            echo '  <tr class="fila2">
                                                        <td class="parrafo1" align="center">[?] No existe informaci&oacute;n.</td>
                                                    </tr>';
                                        }
                                    ?>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="encabezadoTabla">&nbsp;&nbsp;&nbsp;Otros. &iquest;Cu&aacute;les?:</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fila2">
                            <div align="center">
                                <input name="wotroalmuerzo" type="text" id="wotroalmuerzo" size="90" rel="000024" in="Cvioal" onBlur="blurCampo(this,'wcondicion','identGeneral','','','');" value="<?=$identGeneral['condicion_vida']['otro_almuerzo']?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="459" class="encabezadoTabla">En cu&aacute;l de estas actividades recreativas usted participar&iacute;a activamente</td>
                        <td width="185">
                            <div align="center">
                                <table width="100%" border="0" cellspacing="0" cellpadding="2">
                                    <?php
                                        if(count($identGeneral['tipo_recreativas']) > 0)
                                        {
                                            $fil = 0;
                                            $css = '';
                                            $recreativa = $identGeneral['condicion_vida']['recreativas'];
                                            foreach($identGeneral['tipo_recreativas'] as $key => $value)
                                            {
                                                $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                                $fil++;
                                                $chk = (in_array($key,$recreativa['tipo_recreativa'])) ? 'checked="checked"' : '';
                                                echo '  <tr class="'.$css.'">
                                                            <td width="88" align="left">'.trim(utf8_encode($value['tipo_recreativa'])).'</td>
                                                            <td width="32">
                                                                <input type="checkbox" id="'.$recreativa['id'].'_'.$key.'_recreativa" name="'.$recreativa['id'].'_'.$key.'_recreativa" rel="000024" in="Cvidep" value="on" onClick="blurCampo(this,\'wcondicion\',\'salarios\',\'\',\'\',\'\');" '.$chk.' />
                                                            </td>
                                                        </tr>';
                                            }
                                        }
                                        else
                                        {
                                            echo '  <tr class="fila2">
                                                        <td class="parrafo1" align="center">[?] No existe informaci&oacute;n.</td>
                                                    </tr>';
                                        }
                                    ?>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="encabezadoTabla">&nbsp;&nbsp;&nbsp;Otras. &iquest;Cu&aacute;les?:</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fila2">
                            <div align="center">
                                <input name="wotrarecre" type="text" id="wotrarecre" size="90" rel="000024" in="Cvidod" onBlur="blurCampo(this,'wcondicion','identGeneral','','','');" value="<?=$identGeneral['condicion_vida']['otra_recrea']?>" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="encabezadoTabla">En cu&aacute;l de estas actividades culturales y art&iacute;sticas participar&iacute;a activamente</td>
                        <td><div align="center">
                            <table width="100%" border="0" cellspacing="0" cellpadding="2">
                                <?php
                                    if(count($identGeneral['tipo_artes']) > 0)
                                    {
                                        $fil = 0;
                                        $css = '';
                                        $artes = $identGeneral['condicion_vida']['artes'];
                                        foreach($identGeneral['tipo_artes'] as $key => $value)
                                        {
                                            $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                            $fil++;
                                            $chk = (in_array($key,$artes['tipo_artes'])) ? 'checked="checked"' : '';
                                            echo '  <tr class="'.$css.'">
                                                        <td width="88" align="left">'.utf8_encode($value['tipo_artes']).'</td>
                                                        <td width="32">
                                                            <input type="checkbox" id="'.$artes['id'].'_'.$key.'_artes" name="'.$artes['id'].'_'.$key.'_artes" rel="000024" in="Cviaca" value="on" onClick="blurCampo(this,\'wcondicion\',\'salarios\',\'\',\'\',\'\');" '.$chk.' />
                                                        </td>
                                                    </tr>';
                                        }
                                    }
                                    else
                                    {
                                        echo '  <tr class="fila2">
                                                    <td class="parrafo1" align="center">[?] No existe informaci&oacute;n.</td>
                                                </tr>';
                                    }
                                ?>
                            </table></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="encabezadoTabla">&nbsp;&nbsp;&nbsp;Otros. &iquest;Cu&aacute;les?:</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fila2"><div align="center"><input name="wotracultural" type="text" id="wotracultural" size="90" rel="000024" in="Cvioac" onBlur="blurCampo(this,'wcondicion','identGeneral','','','');" value="<?=$identGeneral['condicion_vida']['otras_artes']?>" />
                        </div></td>
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
                        <td>
                            <div align="center">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <?php
                                        if(count($identGeneral['tipo_educativa']) > 0)
                                        {
                                            $fil = 0;
                                            $css = '';
                                            $educativas = $identGeneral['condicion_vida']['educativas'];
                                            foreach($identGeneral['tipo_educativa'] as $key => $value)
                                            {
                                                $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                                $fil++;
                                                $chk = (in_array($key,$educativas['tipo_educativa'])) ? 'checked="checked"' : '';
                                                echo '  <tr class="'.$css.'">
                                                            <td width="88" align="left">'.utf8_encode($value['tipo_educativa']).'</td>
                                                            <td width="32">
                                                                <input type="checkbox" id="'.$educativas['id'].'_'.$key.'_educativa" name="'.$educativas['id'].'_'.$key.'_educativa" rel="000024" in="Cviapa" value="on" onClick="blurCampo(this,\'wcondicion\',\'salarios\',\'\',\'\',\'\');" '.$chk.' />
                                                            </td>
                                                        </tr>';
                                            }
                                        }
                                        else
                                        {
                                            echo '  <tr class="fila2">
                                                        <td class="parrafo1" align="center">[?] No existe informaci&oacute;n.</td>
                                                    </tr>';
                                        }
                                    ?>
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="encabezadoTabla"><div align="left">Qu&eacute; actividades y hobbies pract&iacute;ca en su tiempo libre:</div></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="fila2">
                            <div align="center">
                                <input name="whobbies" type="text" id="whobbies" size="90"  rel="000024" in="Cvihbb" onBlur="blurCampo(this,'wcondicion','identGeneral','','','');" value="<?=$identGeneral['condicion_vida']['hobbies']?>" />
                            </div>
                        </td>
                    </tr>
                </table>
                <br />
				<!--
                <br />
                <div id="div_mjsRoles" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">
                                Rol que desempe&ntilde;a en la instituci&oacute;n adicional a su cargo
                            </td>
                        </tr>
                    </table>
                </div>
                <br />
                <div id="div_infoRoles" align="center">
                    <table align="center" width="600" border="0" cellspacing="0" cellpadding="2">
					-->
                        <?php
                            // if(count($identGeneral['tipo_rol']) > 0)
                            // {
                                // $fil = 0;
                                // $css = '';
                                // /**
                                    // Esta sección operar de manera muy similar a la sección de salarios familiares (tipos de ingresos) por ejemplo cuando se selecciona una opción
                                    // y a partir de lo que se selecciono muestre otros campos de complemento, Ver la explicación en el ejemplo citado anteriormente para tener
                                    // una idea del funcionamiento y construcción de estos campos.
                                 // */
                                // foreach($identGeneral['tipo_rol'] as $key => $value)
                                // {
                                    // $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                    // $fil++;
                                    // $chk = ($key == $value['org_interno']) ? 'checked="checked"' : '';

                                    // $ver = ($key == $value['org_interno']) ? 'display:block;' : 'display:none;';

                                    // $funcion = "blurCampo(this,'rol_text_$key','roles','','','');";
                                    // $reset = "resetear('0_".$key."_rol','0_".$key."_rolobs');";
                                    // echo '  <tr class="'.$css.'">
                                                // <td class="encabezadoTabla" style="border-bottom: 1px #ffffff solid;">'.utf8_encode($value['tipo_rol']).'</td>
                                                // <td>
                                                    // <table border="0" cellspacing="0" cellpadding="0">
                                                        // <tr>
                                                            // <td>
                                                                // <input type="hidden" id="rol_text_'.$key.'" name="rol_text_'.$key.'" value="'.$value['id_participa'].'" />
                                                                // <input type="checkbox" id="0_'.$key.'_rol" name="0_'.$key.'_rol" rel="000026" in="Oincod" value="on" onClick="'.$funcion.' '.$reset.'" '.$chk.' />
                                                            // </td>
                                                            // <td align="center">';
                                    // if ($value['observ_requerida'] == 'on')
                                    // {
                                        // echo '  <div id="div_0_'.$key.'_rolobs" style="width:245px;'.$ver.'">
                                                    // <label style="font-size: 10pt;" for="0_'.$key.'_rolobs" >De cu&aacute;les:</label> <input style="font-size: 8pt;" name="0_'.$key.'_rolobs" type="text" id="0_'.$key.'_rolobs" size="20" rel="000026" in="Oinobs" onBlur="'.$funcion.'" value="'.utf8_encode($value['observacion']).'" />
                                                // </div>';
                                    // }
                                    // else { echo '&nbsp;';}

                                    // echo '                </td>
                                                        // </tr>
                                                    // </table>
                                                // </td>
                                            // </tr>';
                                // }
                            // }
                            // else
                            // {
                                // echo '  <tr class="fila2">
                                                // <td class="parrafo1" align="center">[?] No existe informaci&oacute;n.</td>
                                            // </tr>';
                            // }
                        ?>
						<!--
                    </table>
                </div>
                <br>
				-->
            </div>
            <br />
			
			<!--
            <br />
            <div id="ref_addons" align="center">
                <table width="900" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="tit_seccion">
                            <a href="#null" onclick="javascript:verSeccionCaracterizacion('div_preguntas');">
                                <img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;SECCI&Oacute;N DE PREGUNTAS
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="div_preguntas" align="center" class='borderDiv displCaracterizacion'>
                <div id="div_mjsPreguntas" class="backgrd_seccion">
                    <table width="900" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="parrafo1">
                                Responda a las siguientes preguntas
                            </td>
                        </tr>
                    </table>
                </div>
                <br />
                <div id="div_infoPreguntas" align="center">
                    <table align="center" width="800" border="0" cellspacing="0" cellpadding="2">
					
					-->
                        <?php
                            // if(count($identGeneral['repositorio_preguntas']) > 0)
                            // {
                                // $fil = 0;
                                // $css = '';
                                // foreach($identGeneral['repositorio_preguntas'] as $key => $value)
                                // {
                                    // $css = ($fil % 2 == 0) ? "fila1" : "fila2";
                                    // $fil++;

                                    // $ver = '';
                                    // $reset = '';

                                    // $chkSi = ($value['afirmacion'] == 'on' ) ? 'selected="selected"' : '';
                                    // $chkNo = ($value['afirmacion'] == 'off') ? 'selected="selected"' : '';
                                    // $reset = "resetear2('0_".$key."_pre','0_".$key."_preobs');";

                                    // $ver = 'display:none;';
                                    // if ($value['req_afirmacion'] == 'on' && $value['req_respuesta'] == 'off')
                                    // {
                                        // $resp = '&nbsp;';
                                        // $reset = '';
                                    // }
                                    // elseif ($value['req_afirmacion'] == 'off' && $value['req_respuesta'] == 'on' )
                                    // {
                                        // $ver = 'display:block;';
                                        // $afirm = '&nbsp;';
                                        // $reset = '';
                                    // }
                                    // elseif( $value['req_afirmacion'] == 'on' && $value['req_respuesta'] == 'on' && $value['afirmacion'] == 'on')
                                    // {
                                        // $ver = 'display:block;';
                                    // }

                                    // $funcion = "blurCampo(this,'pre_text_$key','preguntas','','','');";
                                    // // campo si requiere afirmar mediante select
                                    // $afirm = '  <select id="0_'.$key.'_pre" name="0_'.$key.'_pre" rel="000027" in="Rescpr|Resafi" value="on" onChange="'.$funcion.' '.$reset.'" >
                                                        // <option value="">Seleccione..</option>
                                                        // <option value="on" '.$chkSi.' >Si</option>
                                                        // <option value="off" '.$chkNo.' >No</option>
                                                // </select>
                                                // ';

                                    // // campo si requiere escribir una respuesta
                                    // $resp = '   <label style="font-size: 10pt;" for="0_'.$key.'_preobs" >'.utf8_encode($value['msj_respuesta']).':</label><br />
                                                // <textarea cols="46" rows="2" style="font-size: 8pt;" name="0_'.$key.'_preobs" id="0_'.$key.'_preobs" rel="000027" in="|Resres" onBlur="'.$funcion.'" >'.utf8_encode($value['respuesta']).'</textarea>
                                                // ';

                                    // if ($value['req_afirmacion'] == 'on' && $value['req_respuesta'] == 'off') { $resp = '&nbsp;';}
                                    // elseif ($value['req_afirmacion'] == 'off' && $value['req_respuesta'] == 'on' ) { $afirm = '&nbsp;'; }
                                    // elseif($value['req_afirmacion'] == 'off' && $value['req_respuesta'] == 'off'
                                            // || $value['req_afirmacion'] == '' && $value['req_respuesta'] == '')
                                    // {
                                        // $afirm = '&nbsp;';
                                        // $ver = 'display:block;';
                                        // $resp = '<div class="parrafo1" style="background-color: #E4E4E4" align="center">[?] No se ha habilitado la forma de respuesta para esta regunta.</div>';
                                    // }

                                    // $secc = '<tr class="'.$css.'">
                                                // <td valign="top" class="encabezadoTabla" style="border-bottom: 1px #ffffff solid;">'.utf8_encode($value['pregunta']).'</td>
                                                // <td style="" valign="top">
                                                    // <table style="" border="0" cellspacing="0" cellpadding="0">
                                                        // <tr>
                                                            // <td valign="top" style="width: 130px;">
                                                            // <input type="hidden" id="pre_text_'.$key.'" name="pre_text_'.$key.'" value="'.$value['id_respuesta'].'" />
                                                                // '.$afirm.'
                                                            // </td>
                                                            // <td align="center" style="" >
                                                                // <div class="encabezadoTabla" id="div_0_'.$key.'_preobs" style="width:365px;'.$ver.'">
                                                                    // '.$resp.'
                                                                // </div>
                                                            // </td>
                                                        // </tr>
                                                    // </table>
                                                // </td>
                                             // </tr>';

                                    // echo $secc;
                                // }
                            // }
                            // else
                            // {
                                // echo '  <tr class="fila2">
                                                // <td class="parrafo1" align="center">[?] En este momento NO existen preguntas para responder.</td>
                                            // </tr>';
                            // }
                        ?>
						<!--
                    </table>
                </div>
                <br />
            </div>
			-->
        </td>
    </tr>
</table>
<div id="dv_registro_nuevo_empleado" style="display:none;">
    <p style="text-align:center;">No est&aacute registrado en la caracterizaci&oacute;n, diligencie los siguientes datos para continuar.<br>
    Los datos aqu&iacute; ingresados ya no pueden ser modificados por usted despues del registro.</p>
    <table align="center">
        <tr>
            <td class="encabezadoTabla">Primer nombre</td>
            <td class="fila1"><input type="text" class="mayusculas" id="reg_nombre1" name="reg_nombre1" value=""></td>
            <td class="encabezadoTabla">Segundo nombre</td>
            <td class="fila2"><input type="text" class="mayusculas" id="reg_nombre2" name="reg_nombre2" value=""></td>
        </tr>
        <tr>
            <td class="encabezadoTabla">Primer apellido</td>
            <td class="fila1"><input type="text" class="mayusculas" id="reg_apellido1" name="reg_apellido1" value=""></td>
            <td class="encabezadoTabla">Segundo apellido</td>
            <td class="fila2"><input type="text" class="mayusculas" id="reg_apellido2" name="reg_apellido2" value=""></td>
        </tr>
        <tr>
            <td class="encabezadoTabla">Fecha nacimiento</td>
            <td class="fila1"><input class="" type="text" size="20" id="reg_fecha_nace" name="reg_fecha_nace" value="" in="Idefnc" rel="000013" disabled="disabled" /></td>
            <td class="encabezadoTabla">G&eacute;nero</td>
            <td class="fila2">
                <select name="reg_wgeneroemp" id="reg_wgeneroemp">
                <?php
                    foreach($identGeneral['genero'] as $key => $value)
                    {
                        echo "<option value='$key' >".utf8_encode($value)."</option>";
                    }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <td class="encabezadoTabla">C&eacute;dula</td>
            <td class="fila1" colspan="3">
                <input type="text" id="reg_wcedemp" name="reg_wcedemp" value="" onkeypress="return soloNumeros(event);">
            </td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:center;">
                <div align="center" id="div_mensaje_add_proced" class="fondoAmarillo" style="display:none;border: 1px solid #2A5DB0;padding: 5px; text-align:center;font-size:12pt;"></div>
            </td>
        </tr>
    </table>
</div>
<input type='hidden' name='failJquery' id='failJquery' value='El programa termin&oacute; de ejecutarse pero con algunos inconvenientes <br>(El proceso no se complet&oacute; correctamente)' >
</div>