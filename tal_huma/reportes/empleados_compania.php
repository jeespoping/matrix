<?php
include_once("conex.php");
session_start();
/**
 PROGRAMA                   : empleados_compania.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 28 Mayo de 2012

 DESCRIPCION:
 Búscame, es un reporte que se encarga de buscar e identificar al empleado que se busca mediante su código o número de cédula de ciudadanía.

 ACTUALIZACIONES:
 *  xx xx xxxx
    yyyyy yyyy          :
 *  Mayo 28 de 2012
    Edwar Jaramillo     : Fecha de la creación del reporte.

**/
$wactualiz = "(Mayo 28 de 2012)";
?>
    <head>
    <title>Empleados de la compañía</title>

    <!-- JQUERY para los tabs -->
    <link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <link type="text/css" href="../../../include/root/jquery.simpletree.css" rel="stylesheet" />

    <script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
    <script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
    <script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

    <script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
    <!-- Fin JQUERY para los tabs -->

    <!-- Include de codigo javascript propio de mensajeria Kardex -->
    <script type="text/javascript" src="../../../include/movhos/mensajeriaKardex.js"></script>

    <script type="text/javascript">

    if(document.getElementById("fixeddiv2"))
        {
            fixedMenuId2 = "fixeddiv2";
            var fixedMenu2 = {
                                hasInner:typeof window.innerWidth == "number",
                                hasElement:document.documentElement != null && document.documentElement.clientWidth,
                                menu:document.getElementById ? document.getElementById(fixedMenuId2) : document.all ? document.all[fixedMenuId2] : document.layers[fixedMenuId2]
                            };
            fixedMenu2.computeShifts = function() {
                                                    fixedMenu2.shiftX = fixedMenu2.hasInner ? pageXOffset : fixedMenu2.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft;
                                                    fixedMenu2.shiftX += fixedMenu2.targetLeft > 0 ? fixedMenu2.targetLeft : (fixedMenu2.hasElement ? document.documentElement.clientWidth : fixedMenu2.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu2.targetRight - fixedMenu2.menu.offsetWidth;
                                                    fixedMenu2.shiftY = fixedMenu2.hasInner ? pageYOffset : fixedMenu2.hasElement ? document.documentElement.scrollTop : document.body.scrollTop;
                                                    fixedMenu2.shiftY += fixedMenu2.targetTop > 0 ? fixedMenu2.targetTop : (fixedMenu2.hasElement ? document.documentElement.clientHeight : fixedMenu2.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu2.targetBottom - fixedMenu2.menu.offsetHeight
                                                };
            fixedMenu2.moveMenu = function() {
                                                fixedMenu2.computeShifts();
                                                if(fixedMenu2.currentX != fixedMenu2.shiftX || fixedMenu2.currentY != fixedMenu2.shiftY)
                                                {
                                                    fixedMenu2.currentX = fixedMenu2.shiftX;
                                                    fixedMenu2.currentY = fixedMenu2.shiftY;
                                                    if(document.layers) { fixedMenu2.menu.left = fixedMenu2.currentX; fixedMenu2.menu.top = fixedMenu2.currentY }
                                                    else {
                                                        fixedMenu2.menu.style.left = fixedMenu2.currentX + "px"; fixedMenu2.menu.style.top = fixedMenu2.currentY + "px"
                                                    }
                                                }
                                                fixedMenu2.menu.style.right = ""; fixedMenu2.menu.style.bottom = ""
                                            };
            fixedMenu2.floatMenu = function() {
                                                fixedMenu2.moveMenu();
                                                setTimeout("fixedMenu2.floatMenu()", 20)
                                            };
            fixedMenu2.addEvent = function(a, b, f) {
                                                if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined")
                                                {
                                                    a[b + "_num"] = 0;
                                                    if(typeof a[b] == "function")
                                                    {
                                                        a[b + 0] = a[b];
                                                        a[b + "_num"]++
                                                    }
                                                    a[b] = function(c) {
                                                                    var g = true;
                                                                    c = c ? c : window.event;
                                                                    for(var d = 0;d < a[b + "_num"];d++)
                                                                        if(a[b + d](c) === false)g = false; return g
                                                                }
                                                }
                                                for(var e = 0;e < a[b + "_num"];e++)
                                                    if(a[b + e] == f)
                                                        return;
                                                a[b + a[b + "_num"]] = f;
                                                a[b + "_num"]++
                                            };
            fixedMenu2.supportsFixed = function() {
                                                var a = document.createElement("div");
                                                a.id = "testingPositionFixed";
                                                a.style.position = "fixed";
                                                a.style.top = "0px";
                                                a.style.right = "0px";
                                                document.body.appendChild(a);
                                                var b = 1;
                                                if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")
                                                    b = parseInt(a.offsetTop);
                                                if(b == 0)return true;
                                                return false
                                            };
            fixedMenu2.init = function() {
                                        if(fixedMenu2.supportsFixed())fixedMenu2.menu.style.position = "fixed";
                                        else {
                                                var a = document.layers ? fixedMenu2.menu : fixedMenu2.menu.style;
                                                fixedMenu2.targetLeft = parseInt(a.left);
                                                fixedMenu2.targetTop = parseInt(a.top);
                                                fixedMenu2.targetRight = parseInt(a.right);
                                                fixedMenu2.targetBottom = parseInt(a.bottom);
                                                if(document.layers) { menu.left = 0; menu.top = 0 }
                                                fixedMenu2.addEvent(window, "onscroll", fixedMenu2.moveMenu);
                                                fixedMenu2.floatMenu()
                                        }
                                    };
            fixedMenu2.addEvent(window, "onload", fixedMenu2.init);
            fixedMenu2.hide = function() {
                                        if(fixedMenu2.menu.style.display != "none")fixedMenu2.menu.style.display = "none";
                                        return false };
                                        fixedMenu2.show = function(a) {
                                                                        document.getElementById("wtipoprot");
                                                                        var b = 0;
                                                                        for(b = 0;b < document.forms.forma.wtipoprot.length;b++)
                                                                            document.forms.forma.wtipoprot[b].disabled = true;
                                                                        for(b = 0;b < document.forms.forma.wtipoprot.length;b++)
                                                                            if(a.indexOf(document.forms.forma.wtipoprot[b].value) != -1)
                                                                            {
                                                                                document.forms.forma.wtipoprot[b].checked = true;
                                                                                document.forms.forma.wtipoprot[b].disabled = false
                                                                            }
                                                                        fixedMenu2.menu.style.display = "block";
                                                                        return false
                                                                    }
        };
    /*****************************************************************************************************************************
     * Inicializa jquery
     ******************************************************************************************************************************/
    function inicializarJquery()
    {
        $("#tabs").tabs({
                            fx: {opacity: 'toggle' },
                            select: function(event, ui) {
                                                            if(fixedMenu2 && fixedMenu2 != 'undefined') fixedMenu2.hide();
                                                        }
                        }
        ); //JQUERY:  Activa los tabs para las secciones del kardex

        // $("#tabs").tabs('select', 1);
        $("#tabs").tabs(1, 'select');
    }

    $(document).ready(function(){  inicializarJquery();  });
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


    </style>
    </head>
    <script type="text/javascript">
   function cerrarVentana()
    {
        window.close()
    }

    function enter()
    {
        document.empleados_compania.submit();
    }

    //FUNCION QUE PERMITE GENERAR UNA VENTANA EMERGENTE CON UN PATH ESPECIFICO
    function ejecutar(path)
    {
        window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
    }
    </script>
<body>

<?php

/*****************************************************************************************************
                                              F U N C I O N E S
*****************************************************************************************************/

/**
 * Función mostrar_filtros(), se encarga de mostrar el filtro para digitar el número de historia a buscar
 * y tambien se encarga de mostar la lista de ingresos luego de encontrar la historia del paciente buscado.
 *
 * @return unknown
 */
function mostrar_filtros()
{


    // $wcco1 = explode("-", $wcco);

    echo "
    <div align='center'>
        <input type='hidden' name='wformulario' id='wformulario' value='demo'>
        <table border='0' width='200px;'>
            <tr class='encabezadoTabla'>
                <td align='center'><font size=4>C&oacute;digo</font></td>
                <td align='center'><font size=4>C&eacute;dula</font></td>
            </tr>
            <tr class='fila2'>
                <td align='center'><input type='text' size='10' name='wcodigo' id='wcodigo'></td>
                <td align='center'><input type='text' size='10' name='wced' id='wced'></td>
            </tr>
            <tr class='fila1'>
                <td align='center' colspan='2'><input type='submit' id='buscar' name='buscar' value='Buscar..'></td>
            </tr>
        </table>
        <br/>
    </div>";

    // echo "<table>";
    // echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    // echo "</table>
    // ";
}
/*****************************************************************************************************************************************/

function getInfoFormulario($accion)
{
    $respuesta = '';
    switch($accion)
    {
        case 'dat_gen'  :   $respuesta = datosGenerales();
                            break;
        case 'logros'   :   $respuesta = datosLogros();
                            break;
        case 'estudios' :   $respuesta = datosEstudios();
                            break;
        case 'experto'  :   $respuesta = datosExperto();
                            break;
    }
    return $respuesta;
}
/*****************************************************************************************************************************************/

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
/*****************************************************************************************************************************************/

/**
 * description...
 *
 * @param unknown $wcedula
 * @return unknown
 */
function getFoto($wcedula = 'not_foto')
{
    $wruta_fotos = "../../images/medical/talento/";
    $wfoto = "silueta.gif";

    $wfoto_em   = $wruta_fotos.$wcedula.'.jpg';
    if (!file_exists($wfoto_em))
    {
         $wfoto_em = $wruta_fotos.$wfoto;
    }
    return $wfoto_em;
}
/*****************************************************************************************************************************************/

/**
 * description...
 *
 * @param unknown $seccion
 * @param unknown $n
 * @param unknown $wfoto
 * @return unknown
 */
function getPlantilla($seccion, $n_empleado = '',$wfoto_em = '')
{
    $n_empleado = ($n_empleado == '') ? 'NO SE ENCONTRARON DATOS' : $n_empleado;
    $wfoto_em   = ($wfoto_em == '') ? getFoto() : $wfoto_em;

    $form_tabla = ' <br/><br/>
                    <div id="div_marco" align="center">
                    <table style="text-align: left; width: 700px;" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                                <td class="encabezadoTabla" width="150px;" align="center">'.$seccion.'</td>
                                <td class="">&nbsp;</td>
                                <td class="">&nbsp;</td>
                                <td>&nbsp;</td>
                        </tr>
                    </table>
                    <table style="text-align: left; width: 700px;" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="brdtop brdleft bgGris1">&nbsp;</td>
                                <td class="brdtop bgGris1">&nbsp;</td>
                                <td class="brdtop brdright bgGris1">&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr class="">
                                <td class="brdleft bgGris1" width="20px;" >&nbsp;</td>
                                <td class="encabezadoTabla" align="center" width="550px;" >
                                        CL&Iacute;NICA LAS AMERICAS
                                </td>
                                <td class="brdright bgGris1" width="70px;" >&nbsp;</td>
                                <td width="70px;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="1" class="brdleft bgGris1">&nbsp;</td>
                                <td width="540px;" align="center" class="fila2"><font size="4" style="font-weight:bold;">'.$n_empleado.'</font></td>
                                <td colspan="2" rowspan="1" valign="top">
                                    <div id="div_foto" align="center" class="brdleft brdright brdtop brdbottom">
                                        <img width="140" height="140" src="'.$wfoto_em.'">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="brdleft bgGris1">&nbsp;</td>
                                <td colspan="1" align="center" class="bgGris1">


                                [DATA]


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

    return $form_tabla;
}
/*****************************************************************************************************************************************/

/**
 * description...
 *
 * @param unknown $fecha
 * @return unknown
 */
function calcularAnioMesesDiasTranscurridos($fecha_inicio)
{
    $datos = array('anios'=>0,'meses'=>0,'dias'=>0);

    $explodefi  = explode('-',$fecha_inicio);
    $anio_ini   = $explodefi[0];
    $mes_ini    = $explodefi[1];
    $dia_ini    = $explodefi[2];

    $anio_fin   = date("Y");
    $mes_fin    = date("m");
    $dia_fin    = date("d");

    $AInicio    = $anio_ini;
    $AFinal     = $anio_fin;

    $sumadiasBis = 0;

    for ($i = $AInicio; $i <= $AFinal; $i++)
    {
        $bis = (($i % 4) == 0) ? 86400 : 0;
        $sumadiasBis += $bis;
    }

    // Calculamos los segundos entre las dos fechas
    $fechaInicio = mktime(0,0,0,$mes_ini,$dia_ini,$anio_ini);
    $fechaFinal  = mktime(0,0,0,$mes_fin,$dia_fin,$anio_fin);

    $segundos = ($fechaFinal - $fechaInicio);
    $anyos = floor(($segundos-$sumadiasBis)/31536000);
    $datos['anios'] = $anyos;

    $segundosRestante = ($segundos-$sumadiasBis)%(31536000);
    $meses = floor($segundosRestante/2592000);
    $datos['meses'] = $meses;

    $segundosRestante = ($segundosRestante%2592000); // Suma un día mas por cada años bisiesto
    //$segundosRestante = (($segundosRestante-$sumadiasBis)%2592000); // No suma un día mas por cada año bisiesto
    $dias = floor($segundosRestante/86400);
    $datos['dias'] = $dias;
    return $datos;
}
/*****************************************************************************************************************************************/

/**
 * description...
 *
 * @return unknown
 */
function datosGenerales()
{
    global $conex;
    global $wmovhos;
    global $wemp_pmla;
    global $wusuario;
    global $wtabcco;
    global $wced;
    global $wcodigo;

    $form_tabla = '';

    if (isset($wced) || isset($wcodigo))
    {
        if($wced == '' && $wcodigo == '')
        {
            $wced = '*';
            $wcodigo = '*';
        }

        $filtro = '';
        $and = '';
        if(trim($wcodigo) != '')
        {
            $filtro = "percod = '".$wcodigo."'";
            $and = 'AND';
        }

        if(trim($wced) != '')
        {
            $filtro .= "$and perced = '".$wced."'";
        }

        $conexunix = odbc_connect('nomina','informix','sco') or die("No se ralizo Conexion con Unix");
        $q = "   SELECT  percod AS codigo, perced AS cedula, percco AS ccosto, perfin AS f_ingreso
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , perfna AS f_nacimiento, peretr AS estado, cconom AS nombre_cco
                        , oficod AS cod_oficina, ofinom AS cargo
                FROM    noper, cocco, noofi
                WHERE   $filtro
                        AND ccocod = percco
                        AND perofi = oficod";       //si esta activo

        $res = odbc_exec($conexunix,$q);

        if (odbc_fetch_row($res))
        {
            $wcodigo    = odbc_result($res,'codigo');
            $wced       = str_replace('.','',trim(odbc_result($res,'cedula')));
            $wccosto    = odbc_result($res,'ccosto');
            $wf_ingreso = odbc_result($res,'f_ingreso');
            $wnombre1   = odbc_result($res,'nombre1');
            $wnombre2   = odbc_result($res,'nombre2');
            $wapellido1 = odbc_result($res,'apellido1');
            $wapellido2 = odbc_result($res,'apellido2');
            $westado    = odbc_result($res,'estado');
            $wnom_cco   = odbc_result($res,'nombre_cco');
            $wf_nace    = odbc_result($res,'f_nacimiento');
            $cargo      = odbc_result($res,'cargo');
            $edad       = calculaEdad($wf_nace);

            $westado = ($westado == 'A')? 'Activo': 'Retirado';

            $t_laborado = calcularAnioMesesDiasTranscurridos($wf_ingreso);

            $n_empleado = trim($wnombre1.' '.$wnombre2.' '.$wapellido1.' '.$wapellido2);
            $wfoto_em = getFoto($wced);

            $datos = '
                <table width="540px;" style="text-align: left; height: 284px;" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr class="fila1">
                            <td width="160px;" class="tbold">&nbsp;&nbsp;Cargo actual:</td>
                            <td width="380px;">'.$cargo.'.</td>
                        </tr>
                        <tr class="fila2">
                            <td class="tbold">&nbsp;&nbsp;C&oacute;digo:</td>
                            <td>'.$wcodigo.'</td>
                        </tr>
                        <tr class="fila1">
                            <td width="240px;" class="tbold">&nbsp;&nbsp;Documento:</td>
                            <td width="300px;">'.$wced.'</td>
                        </tr>
                        <tr class="fila2">
                            <td class="tbold">&nbsp;&nbsp;Centro costo:</td>
                            <td>['.$wccosto.'] '.$wnom_cco.'.</td>
                        </tr>
                        <tr class="fila1">
                            <td class="tbold">&nbsp;&nbsp;Fecha ingreso:</td>
                            <td>'.$wf_ingreso.',
                                <font style="font-weight:bold">Hace '.$t_laborado['anios'].' A&ntilde;os '.$t_laborado['meses'].' meses '.$t_laborado['dias'].' d&iacute;as.</font>
                            </td>
                        </tr>
                        <tr class="fila2">
                            <td class="tbold">&nbsp;&nbsp;Estado:</td>
                            <td>'.$westado.'.</td>
                        </tr>
                        <tr class="fila1">
                            <td class="tbold">&nbsp;&nbsp;Fecha Nacimiento:</td>
                            <td>'.$wf_nace.',&nbsp;&nbsp;Edad '.$edad.' a&ntilde;os.</td>
                        </tr>
                        <tr class="fila2">
                            <td class="tbold">&nbsp;&nbsp;Extensi&oacute;n:</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>';

            $form_tabla = str_replace("[DATA]",$datos,getPlantilla('Datos Generales',$n_empleado,$wfoto_em));
        }
        else
        {
            $form_tabla = str_replace("[DATA]",'',getPlantilla('Datos Generales'));
        }
		
		odbc_close($conexunix);
		odbc_close_all();
    }
    return $form_tabla;
}
/*****************************************************************************************************************************************/

function datosLogros()
{
    global $conex;
    global $wmovhos;
    global $wemp_pmla;
    global $wusuario;
    global $wtabcco;
    global $wced;
    global $wcodigo;

    if (isset($wced) || isset($wcodigo))
    {
        $filtro = '';
        $and = '';
        if(trim($wcodigo) != '')
        {
            $filtro = "percod = '".$wcodigo."'";
            $and = 'AND';
        }

        if(trim($wced) != '')
        {
            $filtro .= "$and perced = '".$wced."'";
        }

        $conexunix = odbc_connect('nomina','informix','sco') or die("No se ralizo Conexion con Unix");
        $q = "   SELECT  percod AS codigo, perced AS cedula, percco AS ccosto, perfin AS f_ingreso
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , peretr AS estado
                FROM    noper
                WHERE   $filtro";       //si esta activo

        $res = odbc_exec($conexunix,$q);

        if (odbc_fetch_row($res))
        {
            $wcodigo    = odbc_result($res,'codigo');
            $wced       = str_replace('.','',trim(odbc_result($res,'cedula')));
            $wccosto    = odbc_result($res,'ccosto');
            $wf_ingreso = odbc_result($res,'f_ingreso');
            $wnombre1   = odbc_result($res,'nombre1');
            $wnombre2   = odbc_result($res,'nombre2');
            $wapellido1 = odbc_result($res,'apellido1');
            $wapellido2 = odbc_result($res,'apellido2');
            $westado    = odbc_result($res,'estado');

            $n_empleado = trim($wnombre1.' '.$wnombre2.' '.$wapellido1.' '.$wapellido2);
            $wfoto_em = getFoto($wced);

            $datos = '
                    <table width="540px;" style="text-align: left; height: 284px;" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr class="fila1">
                                <td width="240px;">&nbsp;&nbsp;Proyecto Talento Humano:</td>
                                <td width="300px;" align="justify">An&aacute;lisis, Diseño e Implementación de la herramienta para la gestión del TALENTO HUMANO.</td>
                            </tr>
                            <tr class="fila2">
                                <td>&nbsp;&nbsp;Proyecto Identificación:</td>
                                <td align="justify">Identificación mediante huella del personal administrativo y asistencia.</td>
                            </tr>
                            <tr class="fila1">
                                <td>&nbsp;&nbsp;Admin. Telefonía:</td>
                                <td>Elaboración del sistema para el control de telefonía de Clinica las Americas.</td>
                            </tr>
                            <tr class="fila2">
                                <td>&nbsp;&nbsp;Proyecto HCE:</td>
                                <td>Desarrollo de la Hist&oacute;ria Clinica Electr&oacute;nica</td>
                            </tr>
                            <tr class="fila1">
                                <td>&nbsp;&nbsp;Proyecto MAGENTA:</td>
                                <td>Puesta en marcha de nuevas herramientas para el servicio MAGENTA.</td>
                            </tr>
                            <tr class="fila2">
                                <td>&nbsp;&nbsp;Sistema Financiero:</td>
                                <td>Desarrollo de nuevo sistema para la administración y gestión de movimientos contables, desarrollo y pruebas de este nuevo sistema. Migración del anterior sistema contabla al nuevo sistema.</td>
                            </tr>
                            <tr class="fila1">
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="fila2">
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>';
            $form_tabla = str_replace("[DATA]",$datos,getPlantilla('Logros internos',$n_empleado,$wfoto_em));
        }
        else
        {
             $form_tabla = str_replace("[DATA]",'',getPlantilla('Logros internos'));
        }
		
		odbc_close($conexunix);
		odbc_close_all();
    }
    return $form_tabla;
}
/*****************************************************************************************************************************************/

function datosEstudios()
{
    global $conex;
    global $wmovhos;
    global $wemp_pmla;
    global $wusuario;
    global $wtabcco;
    global $wced;
    global $wcodigo;

    if (isset($wced) || isset($wcodigo))
    {
        $filtro = '';
        $and = '';
        if(trim($wcodigo) != '')
        {
            $filtro = "percod = '".$wcodigo."'";
            $and = 'AND';
        }

        if(trim($wced) != '')
        {
            $filtro .= "$and perced = '".$wced."'";
        }

        $conexunix = odbc_connect('nomina','informix','sco') or die("No se ralizo Conexion con Unix");
        $q = "   SELECT  percod AS codigo, perced AS cedula, percco AS ccosto, perfin AS f_ingreso
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , peretr AS estado
                FROM    noper
                WHERE   $filtro";       //si esta activo

        $res = odbc_exec($conexunix,$q);

        if (odbc_fetch_row($res))
        {
            $wcodigo    = odbc_result($res,'codigo');
            $wced       = str_replace('.','',trim(odbc_result($res,'cedula')));
            $wccosto    = odbc_result($res,'ccosto');
            $wf_ingreso = odbc_result($res,'f_ingreso');
            $wnombre1   = odbc_result($res,'nombre1');
            $wnombre2   = odbc_result($res,'nombre2');
            $wapellido1 = odbc_result($res,'apellido1');
            $wapellido2 = odbc_result($res,'apellido2');
            $westado    = odbc_result($res,'estado');

            $n_empleado = trim($wnombre1.' '.$wnombre2.' '.$wapellido1.' '.$wapellido2);
            $wfoto_em = getFoto($wced);

            $datos = '
                    <table width="540px;" style="text-align: left; height: 284px;" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr class="fila1">
                                <td width="240px;">&nbsp;&nbsp;Pregrado:</td>
                                <td width="300px;" align="justify">Ingeniero en Sistema y Computación</td>
                            </tr>
                            <tr class="fila2">
                                <td colspan="2">&nbsp;&nbsp;Posgrado en Telecomunicaciones.</td>
                            </tr>
                            <tr class="fila1">
                                <td colspan="2">&nbsp;&nbsp;Especialización Responsabilidad Social Empresarial.</td>
                            </tr>
                            <tr class="fila2">
                                <td colspan="2">&nbsp;&nbsp;Maestría en Ingeniería.</td>
                            </tr>
                            <tr class="fila1">
                                <td colspan="2">&nbsp;&nbsp;Maestría en Ingeniería de Telecomunicaciones.</td>
                            </tr>
                            <tr class="fila2">
                                <td colspan="2">&nbsp;&nbsp;Maestría en Educación.</td>
                            </tr>
                            <tr class="fila1">
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="fila2">
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>';
            $form_tabla = str_replace("[DATA]",$datos,getPlantilla('Estudios realizados',$n_empleado,$wfoto_em));
        }
        else
        {
             $form_tabla = str_replace("[DATA]",'',getPlantilla('Estudios realizados'));
        }
		
		odbc_close($conexunix);
		odbc_close_all();
    }
    return $form_tabla;
}
/*****************************************************************************************************************************************/

function datosExperto()
{
    global $conex;
    global $wmovhos;
    global $wemp_pmla;
    global $wusuario;
    global $wtabcco;
    global $wced;
    global $wcodigo;

    if (isset($wced) || isset($wcodigo))
    {
        $filtro = '';
        $and = '';
        if(trim($wcodigo) != '')
        {
            $filtro = "percod = '".$wcodigo."'";
            $and = 'AND';
        }

        if(trim($wced) != '')
        {
            $filtro .= "$and perced = '".$wced."'";
        }

        $conexunix = odbc_connect('nomina','informix','sco') or die("No se ralizo Conexion con Unix");
        $q = "   SELECT  percod AS codigo, perced AS cedula, percco AS ccosto, perfin AS f_ingreso
                        , perno1 AS nombre1, perno2 AS nombre2, perap1 AS apellido1, perap2 AS apellido2
                        , peretr AS estado
                FROM    noper
                WHERE   $filtro";       //si esta activo

        $res = odbc_exec($conexunix,$q);

        if (odbc_fetch_row($res))
        {
            $wcodigo    = odbc_result($res,'codigo');
            $wced       = str_replace('.','',trim(odbc_result($res,'cedula')));
            $wccosto    = odbc_result($res,'ccosto');
            $wf_ingreso = odbc_result($res,'f_ingreso');
            $wnombre1   = odbc_result($res,'nombre1');
            $wnombre2   = odbc_result($res,'nombre2');
            $wapellido1 = odbc_result($res,'apellido1');
            $wapellido2 = odbc_result($res,'apellido2');
            $westado    = odbc_result($res,'estado');

            $n_empleado = trim($wnombre1.' '.$wnombre2.' '.$wapellido1.' '.$wapellido2);
            $wfoto_em = getFoto($wced);

            $datos = '
                    <table width="540px;" style="text-align: left; height: 284px;" border="0" cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr class="fila1">
                                <td width="240px;">&nbsp;&nbsp;Clínica las Américas:</td>
                                <td width="300px;" align="justify">Ingeniero analista desarrollador, en el área de Informática.</td>
                            </tr>
                            <tr class="fila2">
                                <td>&nbsp;&nbsp;Infomedia Service S.A:</td>
                                <td align="justify">Ingeniero de Investigación y Desarrollo.</td>
                            </tr>
                            <tr class="fila1">
                                <td>&nbsp;&nbsp;Informática Siglo XXI:</td>
                                <td>Ingeniero desarrollador, en el área de producción y desarrollo.</td>
                            </tr>
                            <tr class="fila2">
                                <td>&nbsp;&nbsp;HIU Cruz Roja sec. Caldas:</td>
                                <td>Analista de sistemas, en el área de sistemas.</td>
                            </tr>
                            <tr class="fila1">
                                <td>&nbsp;&nbsp;Independiente:</td>
                                <td>Desarrollo de aplicaciones web.</td>
                            </tr>
                            <tr class="fila1">
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="fila2">
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>';
            $form_tabla = str_replace("[DATA]",$datos,getPlantilla('Experiencia laboral',$n_empleado,$wfoto_em));
        }
        else
        {
             $form_tabla = str_replace("[DATA]",'',getPlantilla('Experiencia laboral'));
        }
		odbc_close($conexunix);
		odbc_close_all();
    }
    return $form_tabla;
}
/*****************************************************************************************************************************************/

/**
 * description...
 *
 * @return unknown
 */
function iniciarForm()
{
    mostrar_filtros();
    /*****************
     * INICIO DE LA DIVISIÓN POR PESTAÑAS.
     *****************/
    //Mensaje de espera
    // echo "<div id='msjInicio' align=center>";
    // echo "<img src='../../images/medical/ajax-loader5.gif'/>Cargando las pestañas, por favor espere...";
    // echo "</div>";
    // echo getDemo();

    // echo "<input type=hidden id=hpestanas value='$usuario->pestanasKardex'>";
    echo "<input type=hidden id=hpestanas value=''>";

    echo "<div id='tabs' class='ui-tabs' style='display:block' align='center'>";   //Inicio de lo que va a ir encerrado en las pestañas
    echo "  <ul>";

    // if($usuario->pestanasKardex == "*")
    {
        echo "  <li><a href='#fragment-1'><span>Datos generales</span></a></li>
                <li><a href='#fragment-2'><span>Logros internos</span></a></li>
                <li><a href='#fragment-3'><span>Estudios realizados</span></a></li>
                <li><a href='#fragment-4'><span>Experiencia laboral</span></a></li>";
    }

    echo "  </ul>";

    echo "  <div id='fragment-1' style=''>".getInfoFormulario('dat_gen')."</div>";
    echo "  <div id='fragment-2' style=''>".getInfoFormulario('logros')."</div>";
    echo "  <div id='fragment-3' style=''>".getInfoFormulario('estudios')."</div>";
    echo "  <div id='fragment-4' style=''>".getInfoFormulario('experto')."</div>";

    echo "</div>";


}
/*****************************************************************************************************************************************/

//===============================================================================================================
//                                              P R I N C I P A L
//===============================================================================================================

if (!isset($_SESSION['user']))
{ echo "error"; }
else
{
    $pos        = strpos($user, "-");
    $wusuario   = substr($user, $pos + 1, strlen($user));

    include_once "movhos/movhos.inc.php";
    include_once "root/barcod.php";

    $wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
    $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
    $wafinidad = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');

    echo "<br>";
    echo "<br>";

    echo "<form name='empleados_compania' action='' method=post>";
    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

    encabezado("Empleados de la compa&ntilde;&iacute;a", $wactualiz, "clinica");

    if (isset($wformulario))
    {
        iniciarForm();
    }
    else
    {
        // Llamado a la función que permite mostrar los filtros para buscar empleados por código a cedula.
        mostrar_filtros();
        echo "<br>";
        echo "<table align=center>";
        echo "</table>";
    }
    echo "<table align=center>";
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
}
?>