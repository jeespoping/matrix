<?php
include_once("conex.php");
header('Content-type: text/html; charset=ISO-8859-1');
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA                   : gestor_aplicaciones.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 28 Mayo de 2012

 DESCRIPCION:
 Búscame, es un reporte que se encarga de buscar e identificar al empleado que se busca mediante su código o número de cédula de ciudadanía.

 ACTUALIZACIONES:
 * Marzo 08 2016 Edwar Jaramillo:
    - Se verifica si por url llegan los parámetros "turnopermitido", "pqte_mon" para agregarlos al área de parámetros compartidos.
 * Febrero 15 2016 Edwar Jaramillo:
    - Se crea proceso "consultar_manuales", de esta manera el contenedor puede buscar manuales por cada menú cuando se carga cada programa, ya no es necesario
        cargar todos los manuales en un solo archivo asociado a todo el contenedor, solo es necesario colocar en el directorio manuales/ el archivo con el
        correspondiente nombre del escript (proceso/ o resporte/). Se pueden mostrar tanto manuales técnicos como de usuario.
 *  Octubre 07 de 2013
    Edwar Jaramillo     : Se posiciona menú #nav con z-index porque al incorporar el acordeon el manú quedaba bajo los títulos del primer tab del acordeon.

 *  Octubre 10 de 2012
    Edwar Jaramillo     : Documentación de código.

 *  Septiembre 26 de 2012
    Edwar Jaramillo     : 1.En la parte del código donde se consulta "Información de usuario" como su centro de costo y su cargo, se le quitó la variable $wbasedato
                            con el fin de no tener que repetir por cada tema toda la información de la tabla talhuma_000013 (para no diplicar talhuma_000013 en la base de datos
                            de MAGENTA por ejemplo).
                          2.Según el tema, el programa talento.php mostrará el título del tema correspondiente.

 *  Septiembre 06 de 2012
    Edwar Jaramillo     : A esta fecha, el archivo solo se encarga de gestionar los campos del div y form compartido, enviandoles dichos campos como parámetros
                            a los diferentes programas en las diferentes pestañas.
                            Adicionalmente se encarga de pintar las pestañas a las que sólo debe tener acceso el usuario que inició sesión.
 *  Agosto 08 de 2012
    Edwar Jaramillo     : Se incluye un echo para adicionar "<!DOCTYPE html>" cuando se carga la página por primera vez, esto soluciona las validaciones
                            de los formularios del dataTable en la caracterización.
 *  Agosto 02 de 2012
    Edwar Jaramillo     : Se realiza adecuación para que al cambiar de pestaña se recarge la página y se abra la pestaña que se acaba de
                            dar clic, esto con el fin de solucionar posibles problemas en los document ready como es en el caso de
                            caraterización.php que al hacer el cambio de pestañas y retornar a caracterización no iniciaba bien los calendarios.
 *  Mayo 28 de 2012
    Edwar Jaramillo     : Fecha de la creación del programa.

**/





include "gestor_aplicaciones_config.php";
include_once("root/comun.php");
// $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
include_once("gesapl_funciones.php");
/*
    $wtema es adicionada como parámetro de la url desde las opciones de matrix en root, el tema es importante porque identifica las tablas que deben ser usadas según el tema
    por ejemplom "talento humano" o "Magenta" para saber que tipo de información es la que deben suministrar ciertos tipos de programas que hacen parte de este módulo.

    consultarPrefijo() es una función que está en funciones.php que fué desarrollado para este nuevo módulo.
*/
if(!isset($_SESSION['user'])) // Habilitado para pruebas
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
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}

$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$wgr = consultarAliasGrupo($conex, $wemp_pmla, $wtema);
$bordemenu = $wgr['wcolormenu'];
$logo_tema = $wgr['wnombre_logo'];
define("GRUPO_TEMA",$wgr['grupo']);// nombre de un grupo o tema.


$wemp_use = (isset($_SESSION['user'])) ? empresaEmpleado($wemp_pmla, $conex, $wbasedato, $_SESSION['user']) : '+'; // Se consulta la empresa para el usuario autenticado.

if(isset($accion))
{
    $data = array('error'=>0,'mensaje'=>"",'html'=>'');

    switch ($accion) {
        case 'consultar_manuales':
                $data["manuales"] = "";
                $usuariomanualtec = explode('-',$_SESSION['user']);
                //Consulta que trae los usuarios que tienen acceso al manual tecnico
                $q_desarro = "  SELECT  Perusu, Descripcion
                                FROM    root_000042, usuarios
                                WHERE   perest = 'on'
                                        AND Percco = '(01)1710'
                                        AND Pertip IN('03', '02')
                                        AND Codigo = Perusu
                                        AND Perusu = '".$usuariomanualtec[1]."'";

                $res_desarro = mysql_query($q_desarro,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar desarrolladores): ".$q_desarro." - ".mysql_error());
                $arr_desarro = array();

                while($row_desarro = mysql_fetch_array($res_desarro))
                {
                    $arr_desarro[0] = $row_desarro['Perusu'];
                }

                $wroot_group_tal = str_replace("matrix/", "", $wroot_group_tal);

                $nombreArchivo = str_replace("procesos/","", $wproceso); // reemplaza procesos por manuales
                $nombreArchivo = str_replace("resportes/","", $nombreArchivo); // reemplaza resportes por manuales

                $nombreArchivo = str_replace(".php",".pdf", $nombreArchivo); // remplaza el .php por  .pdf
                $ruta_nombreArchivo = "../../".$wroot_group_tal."manuales/".$nombreArchivo;

                $existemusuario                   = file_exists($ruta_nombreArchivo);
                $data["nombreArchivo_manualUser"] = $ruta_nombreArchivo;
                $data["existemusuario"]           = $existemusuario;

                //-------link manual de usuario
                $manualusuario =''; // variable que contiene el link al manual de usuario , inicialmente vacia
                if($existemusuario ==1) // si existe se crea el link
                {
                    $manualusuario =" <span style='font-size:7pt;'>[<a href='".$ruta_nombreArchivo."' onclick='window.open(this.href);return false' style='cursor: pointer;'>Manual de Usuario<a/>]</span>";
                }
                //-------Link manual tecnico
                $manualtecnico=''; // variable que contiene el manual tecnico ; inicialmente vacia
                $nombreArchivo = str_replace(".pdf",".tec.pdf", $nombreArchivo); // se reemplaza el .pdf por .tec.pdf
                $ruta_nombreArchivoTecnico = "../../".$wroot_group_tal."manuales/".$nombreArchivo;


                $existeTecnico                   = file_exists($ruta_nombreArchivoTecnico);
                $data["nombreArchivo_manualTec"] = $ruta_nombreArchivoTecnico;
                $data["existeTecnico"]           = $existeTecnico;

                if( $arr_desarro[0] && $existeTecnico ) // si existe el archivo y el usuario tiene permiso de visualizacion se crea el link
                {
                    $manualtecnico=" <span style='font-size:7pt;'>[<a href='".$ruta_nombreArchivoTecnico."' onclick='window.open(this.href);return false' style='cursor: pointer;'>Manual T&eacute;cnico</a>]</span>";
                }
                $data["manuales"] = $manualusuario.$manualtecnico;
                $data["query_user"] = $q_desarro;
            break;

        default:
            # code...
            break;
    }
    echo json_encode($data);
    return;
}

/*switch($wtema)
{
    case '01': $bordemenu = "#2A5DB0"; break;
    case '04': $bordemenu = "#BD0019"; break;
    default : $bordemenu = "gray"; break;
}*/

$wactualiz = "(Junio 06 de 2013)";
?>
    <html>
    <head>
    <title>Gesti&oacute;n Procesos</title>
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
    <meta http-equiv='cache-control' content='no-cache'>
    <meta http-equiv='expires' content='0'>
    <meta http-equiv='pragma' content='no-cache'>

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

    <script type="text/javascript">

    /**
     * Función usada para la construcción del menú
     *
     * @return ''
     */
    function mainmenu(){
        $(" #nav ul ").css({display: "none"}); // Opera Fix
        $(" #nav li").hover(
                function(){
                    $(this).find('ul:first').css({visibility: "visible",display: "none"}).show(300);
                },
                function(){
                    $(this).find('ul:first').css({visibility: "hidden"});
                });
    }

    $(document).ready(function(){

        // Se valída si el navegador es IE6 para no cargar los estilos puesto que se genera un conflicto con esta versión.
        var ie6 = document.getElementById && document.all&&(navigator.appVersion.indexOf("MSIE 6.")>=0);

        //mainmenu();
        /*
           El menú no se carga si es IE 6, si es IE se carga sin estilos para que por lo menos se
           pueda ver el menú, para el que menú se pueda ver con estilos y una mejor distribución,
           esta aplicación se debería correr en un IE mayor a 6 o en Firefox por ejemplo.
        */
        if(ie6 == true)
        {
            $(" #nav ul ").css({display: "block"}); // Opera Fix
            $(" #nav li").hover(
                    function(){
                        $(this).find('ul:first').css({visibility: "visible",display: "block"}).show(300);
                    },
                    function(){
                        $(this).find('ul:first').css({visibility: "show"});
                    });
            $('#nav').attr({'id':'navIE'});
            //$('#nav').css({'list-style-type':'none','list-style-position':'outside'});
            $('#tabla_menu').attr({'align':'left','width':'100%'});
            $('#tabla_td_menu').attr({'align':'left'});
            $('#div_seccion_menu').css({'display':'block'});
            //$('#tabla_visor_titulo').prepend(document.createTextNode('<br><br><br><br><br><br><br><br>'));
            $('#div_seccion_menu').after('<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />');
        }
        else
        {
            mainmenu();
        }

        $("#nav").css('z-index','9999');
    });

    /**
     * Luego de haberse creado el menú, esta función busca en la sección de menú la primer opción de menú que contenga un vinculo (<a>)
     * y seguida a esto forza a ejecutar el evento click() y de esta manera abrir el contenido de ese menú o pestaña.
     *
     * @return unknown
     */
    function iniciarPrimeraTab()
    {
        valida_tab_tal = $('#url_tal').val();

        if(valida_tab_tal != '')
        {
            firts_exe = $('#'+valida_tab_tal);
            firts_exe.click();
        }
        else
        {
            firts_tab = $('#div_seccion_menu a:first');
            //alert(firts_tab.attr('id'));
            $('#url_tal').val('--');
            firts_tab.click();
        }
    }

    /**
        Esta funcion es referenciada en funciones.php por la función pintarMenuSeleccion(..) que es la que se encarga de pintar los menús con sus eventos.

        cod_tab     : Código de parametrización de la pestaña, menú o submenú.
        include     : Es el archivo o página que se debe ejecutar al darle clic al menú.
        params      : En este campo solo se está enviando el valor "consultaAjax=".
        cod_emp     : A la fecha, inicialmente se esta creando con el valor "[*WCODIGO*]" que posteriormente se reemplaza por '' y finalmente por el momento este parámetro no tiene uso.
        tab_cod     : Es el código único de cada menú o submenú.
        tab_nombre  : Es la descripción o nombre con el que está guardado el menú en la tabla.
    */
    function recargar(cod_tab,include,params,cod_emp,div,tab_cod,tab_nombre)
    {
        recargable = $('#url_tal').val();
        $('#wcodtab_tal').val(tab_cod);
        if(recargable == '' && include != '#' && include != '' && include != '.')
        {
            $('#url_tal').val(cod_tab);
            document.form_comun.submit();
            //return false;
        }
        else
        {
            getInclude(include,params,cod_emp,div,tab_cod, tab_nombre);
        }
        //return true;
    }

    /**
        El programa talento.php tiene una zona (un div con campos hidden) en los que se pueden adicionar otros campos que pueden ser compartidos por todas las demás pestañas o menús
        (por todos los programas que sean abiertos por cada menú).

        Esta función se encarga de adicionar a la url de cada menú todos los campos que estén en esta zona compartida.

        include     : Es el archivo o página que se debe ejecutar al darle clic al menú.
        params      : En este campo solo se está enviando el valor "consultaAjax=".
        cod_emp     : A la fecha, inicialmente se esta creando con el valor "[*WCODIGO*]" que posteriormente se reemplaza por '' y finalmente por el momento este parámetro no tiene uso.
        tab_cod     : Es el código único de cada menú o submenú.
        tab_nombre  : Es la descripción o nombre con el que está guardado el menú en la tabla.
     */
    function getInclude(include,params,cod_emp,div,tab_cod, tab_nombre)  /* incluye otros script a la plantilla actual */
    {
        $('#url_tal').val('');

        url_add_params = addUrlCamposCompartidosTalento();

        if(include != '' && include != '#')
        {
            var wproceso = include;
            pos = include.indexOf("?"); // busca si exise el signo ?
            if(pos == -1)
            {
                include = include+'?'; // Si no existe lo pone antes de agregar los parámetros
            }
            else
            {
                include = include+'&'; // si existe adiciona '&' para adicionar más parámetros
            }

            // include = include+params+"&wuse="+cod_emp+"&wuse_listado="+cod_emp_varios+"&contenedorPadre="+div;
            include = '../../../'+$('#wroot_group_tal').val()+include+params+"&contenedorPadre="+div+url_add_params;

            $('#'+div).html('&nbsp;');
            $("#visor_espera").html('<br /><br /><br /><br /><br /><br /><br /><br /><br /><br />'
                            +'<img  width="13" height="13" src="../../images/medical/ajax-loader7.gif" />&nbsp;<font style="font-weight:bold; color:#2A5DB0; font-size:13pt" >Iniciando m&oacute;dulo...</font>'
                            +'<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />');
            $.post(include, function(data) {
                $('#'+div).html(function() {
                                  $("#visor_espera").html('');
                                  return data;
                                }
                );
                $('#visor_programas_titulo').html('<span>&raquo; '+tab_nombre+'</span>');
            }).done(function(){
                // console.log(tab_cod);
                var objson                = parametrosComunes();
                objson['accion']          = 'consultar_manuales';
                objson['tab_cod']         = tab_cod;
                objson['wproceso']        = wproceso;
                objson['wroot_group_tal'] = $('#wroot_group_tal').val();
                $.post("gestor_aplicaciones.php",
                    objson,
                    function(data){
                        if(data.error == 1)
                        {
                            jAlert(data.mensaje, "Mensaje");
                        }
                        else
                        {
                           $('#visor_programas_titulo').append(data.manuales);
                        }
                    },
                    'json'
                ).done(function(){
                    //
                }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });
            });
        }
        else
        {
            return false;
        }
    }

    function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
        {
            var msj_extra = '';
            msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
            jAlert($("#failJquery").val()+msj_extra, "Mensaje");
            $("#div_error_interno").html(xhr.responseText);
            // console.log(xhr);
            // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
        }

    /**
     * Carga los párametros iniciales para usar en los objetos JSON de los métodos post.
     *
     * @author
     * @param {json} Objeto JSON
    */
    function parametrosComunes()
    {
        var objson             = {};
        objson['consultaAjax'] = '';
        objson['wemp_pmla']    = $("#wemp_pmla_tal").val();
        objson['wtema']        = $("#wtema_tal").val();
        // objson['wbasedato']     = $("#wbasedato").val();
        return objson;
    }

    /**
        Hace un recorrido a la zona de campos compartidos y los concatena para se adicionados a la url

     * @return string
     */
    function addUrlCamposCompartidosTalento()
    {
        url_complemento = '';
        $('#div_campos_compartidos').find('input').each(function() {
                id = $(this).attr('id');
                name = $('#'+id).attr('name');
                valor = $('#'+id).val();

                nuevo_parametro = '&'+name+'='+valor;
                url_complemento += nuevo_parametro;
        });
        return url_complemento;
    }

    /**
        Esta función esta siendo usada en el programa buscame.php
    */
    function ocultarElementoTal(elemento)
    {
        $("#"+elemento).hide(1000);
        $("#img_esp").show(1000);
        $("#img_bus").hide('slow');
        $("#img_det").hide('slow');
        $("#img_fin").hide('slow');
    }

    /**
        Esta función esta siendo usada en el programa buscame.php
    */
    function verElementoTal(elemento)
    {
        $("#"+elemento).show(1000);
    }

    function cerrarVentanaPpal()
    {
        window.close();
    }

    </script>

    <style type="text/css">
        A   {text-decoration: none;color: #000066;}
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
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
        }
        .parrafoTal{
            color: #676767;
            font-family: verdana;
        }

        .titulopagina2
        {
            border-bottom-width: 1px;
            border-color: <?=$bordemenu?>;
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


        /**************************/
        /* Estilos para los menús */
        /**************************/

        #nav, #nav ul{
            margin:0;
            padding:0;
            list-style-type:none;
            list-style-position:outside;
            position:relative;
            line-height:1.5em;
        }

        #nav a{
            display:block;
            padding:0px 8px;
            /*border:1px outset #BCBCBC;*/
            /*color:#212121;*/
            /*text-decoration:overline;*/
            background-color:#E4E4E4;
            height: 20px;
            border-top: 2px <?=$bordemenu?> solid;
            /*border-bottom: 3px #2A5DB0 solid;*/
        }
/*#nav a{
    display:block;
    padding:0px 8px;
    border:1px outset #BCBCBC;
    color:#212121;
    text-decoration:none;
    background-color:#E4E4E4;
}*/

        #nav a:hover{
            background-color:#CCCCCC;
            color:#333333;
        }

        #nav li{
            float:left;
            position:relative;
        }

        #nav ul {
            position:absolute;
            display:none;
            width:12em;
            top:1.5em;
        }

        #nav li ul a{
            width:25em;
            height:auto;
            float:left;
            text-align:left;
        }

        #nav ul ul{
            top:auto;
        }

        #nav li ul ul {
            left:12em;
            margin:0px 0 0 10px;
        }

        #nav li:hover ul ul, #nav li:hover ul ul ul, #nav li:hover ul ul ul ul{
            display:none;
        }
        #nav li:hover ul, #nav li li:hover ul, #nav li li li:hover ul, #nav li li li li:hover ul{
            display:block;
        }

    </style>
</head>
<body onload="iniciarPrimeraTab();">
    <center>
    <div id="contenedor_centrado">
<?php
/*****************************************************************************************************************************************/

// Información de usuario
$sqlT = "   SELECT  Temdes AS nombre_tema
            FROM    root_000076
            WHERE   Temcod = '".$wtema."'";
$resT = mysql_query($sqlT,$conex) or die("Error: " . mysql_errno() . " - en el query consultar informacion de temas: ".$sqlT." - ".mysql_error());

$nombre_tema = 'Módulo sin título';
if(mysql_num_rows($resT) > 0 )
{
    $row = mysql_fetch_array($resT);
    $nombre_tema = $row['nombre_tema'];
}

encabezado("<div class='titulopagina2'>".$nombre_tema."</div>", $wactualiz, $logo_tema);

/* Ya no es necesario porque este paso se incluye al inicio de este script cuando se concatena la empresa */
// $user_session = explode('-',$_SESSION['user']);
// $user_session = $user_session[1];
// $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

if(!isset($wcodigo) || ($wcodigo == '' && $buscar == '') ) { $wcodigo = $wemp_use; $find = 'Buscar..'; }
if(isset($prmcco) && $prmcco != '')
{
    // Este parámetro es útil por ejemplo para los programas de patología y cardiología, con este parámetro se busca el código del centro de costo
    // con el que se deben listar examenes u ordenes solicitadas desde hce haciendo el respectivo filtro por el centro de costo que consulta.
    $ccotema = consultarAliasPorAplicacion($conex, $wemp_pmla, $prmcco);
}
else
{
    $ccotema = '';
    $prmcco = '';
}

$tabla_empleados = consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");

// Información de usuario
$sqlU = "   SELECT  Idecco, Ideccg
            FROM    ".$tabla_empleados."_000013
            WHERE   Ideuse = '".$wemp_use."'";
$resU = mysql_query($sqlU,$conex) or die("Error: " . mysql_errno() . " - en el query consultar informacion de usuario de sesión: ".$sqlU." - ".mysql_error());

$info_use['cod_use'] = $wemp_use;
$info_use['cod_cco'] = '+';
$info_use['cod_ccg'] = '+';
if(mysql_num_rows($resU))
{
    $rowU = mysql_fetch_array($resU);
    $info_use['cod_cco'] = $rowU['Idecco'];
    $info_use['cod_ccg'] = $rowU['Ideccg'];
}

$contenedores = '';
$menus_sin_ubicar = array();

// Se consultan las opciones de menú para el usuario logueado.
$arbol_menu = crearArbolMenus($wemp_pmla, $conex, $wtema, $menus_sin_ubicar, $info_use);
$menu = pintarMenuSeleccion($conex, $arbol_menu['menus_cod'],$arbol_menu['menus_info'], $contenedores, 'tabs');

// echo '<div style="text-align:left;"><pre>';
// print_r($arbol_menu);
// echo '</pre></div>';

$menu = str_replace('[*WCODIGO*]','',$menu);

// echo '<pre>';
// print_r($_POST);
// echo '</pre>';

// todos los hidden que se pongan dentro de este div, serán compartidos con todos los programas que se abran por las pestañas seleccionadas.
// los 'name' se pusieron a propósito de diferente manera que el nombre escrito en el 'id' de cada campo hidden, los name son los que finamente serían usados en los programas abiertos.
$hiddens = "";
$nowempex = explode('-',$wemp_use);
$nowemp = (count($nowempex) > 1) ? $nowempex[0]: $wemp_use;
if(count($_POST) > 0)
{
    foreach($_POST as $key => $value)
    {
        $hiddens .= "<input type='hidden' id='".$key."_tal' name='".$key."' value='".$value."'>";
    }
}
else
{//<input type='hidden' id='wfunciones_tal' name='wfunciones' value='".FUNCIONES_GESTION."'>
    $hiddens = "
            <input type='hidden' id='wroot_group_tal' name='wroot_group' value='".str_replace(HOST_MATRIX.'/','',RAIZ_MATRIX)."/".GRUPO_TEMA."/'>
            <input type='hidden' id='wemp_pmla_tal' name='wemp_pmla' value='".$wemp_pmla."'>
            <input type='hidden' id='wtema_tal' name='wtema' value='".$wtema."'>
            <input type='hidden' id='prmcco_tal' name='prmcco' value='".$prmcco."'>
            <input type='hidden' id='ccotema_tal' name='ccotema' value='".$ccotema."'>
            <input type='hidden' id='wcodtab_tal' name='wcodtab' value='".((isset($wcodtab)? $wcodtab : ''))."'>
            <input type='hidden' id='wuse_tal' name='wuse' value='".$info_use['cod_use']."'>
            <input type='hidden' id='wuse_nowemp_tal' name='wuse_nowemp' value='".$nowemp."'>
            <input type='hidden' id='wuse_listado_tal' name='wuse_listado' value=''>
            <input type='hidden' id='url_tal' name='url' value='".((isset($url)? $url : ''))."'>
            <input type='hidden' id='bordemenu_tal' name='bordemenu' value='".str_replace("#", "", $bordemenu)."'>
            <input type='hidden' id='DEBUG_tal' name='DEBUG' value='".((isset($DEBUG)? $DEBUG : ''))."'>
            <input type='hidden' id='nombre_logo_tal' name='nombre_logo' value='".((isset($logo_tema)? $logo_tema : ''))."'>";

    if(count($_GET) > 0)
    {
        // Esta parte aplica cuando llega una historia e ingreso desde el monitor de facturación ERP.
        if(array_key_exists("whistoria", $_GET))
        {
            $hiddens .= "<input type='hidden' id='whistoria_tal' name='whistoria' value='".$_GET["whistoria"]."'>";
            $hiddens .= "<input type='hidden' id='wing_tal' name='wing' value='".$_GET["wing"]."'>";
        }
        if(array_key_exists("turnopermitido", $_GET))
        {
            $hiddens .= "<input type='hidden' id='turnopermitido_tal' name='turnopermitido' value='".$_GET["turnopermitido"]."'>";
        }
        if(array_key_exists("pqte_mon", $_GET))
        {
            $hiddens .= "<input type='hidden' id='pqte_mon_tal' name='pqte_mon' value='".$_GET["pqte_mon"]."'>";
        }
    }
}

echo "
    <form id='form_comun' name='form_comun' method='post' action=''>
        <div id='div_campos_compartidos' style='display:none;'>
            ".$hiddens."
        </div>
    </form>
    ";

echo "
    <div id='div_seccion_menu' style='background-color:#F4F4F4;border-bottom: 3px ".$bordemenu." solid;text-align:left;'>
        <table id='tabla_menu'>
            <tr>
                <td align='center' id='tabla_td_menu'>";   //Inicio de lo que va a ir encerrado en las pestañas

if($menu != '')
{
    echo $menu.'<br />';
}
else
{
    echo "  <table align='center'><tr><td align='center' style='font-weight:bold;font-size:12pt;color:#999999'>
                [?] No tiene configurado ning&uacute;n acceso a men&uacute;
                <br />
                <br />
                Pongase en contacto con el administrador de la aplicaci&oacute;n
                <br />para que le permita acceder a las opciones necesarias.
            </td></tr></table>";
}

echo "          </td>
            </tr>
        </table>
    </div>";


echo "
        <table cellspacing='0' cellpadding='0' border='0' align='left' id='tabla_visor_titulo'>
            <tr>
                <td><div align='center' id='visor_programas_titulo' style='font-size:12pt;color:#999999;font-weight: bold;'>&nbsp</div></td>
            </tr>
        </table>
        <br /><br />
        <div align='center' id='visor_espera' style='text-align:center;'></div>
        <div align='center' id='visor_programas' style=''>&nbsp</div>";

echo "  <br />
        <br />
        <table align='center'>
            <tr><td align='center' colspan=9><input type='button' value='Cerrar Ventana' onclick='cerrarVentanaPpal();'></td></tr>
        </table>";

?>
    </div>
    </center>
</body>
</html>