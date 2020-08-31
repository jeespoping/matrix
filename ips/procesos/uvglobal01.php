<?php
include_once("conex.php");
if( isset( $consultaAjax ) ){


    mysql_select_db("matrix") or die("No se selecciono la base de datos");

    if( $consultaAjax == "actualizarFecha" ){
        ( $campo == "wfre" ) ? $campoActualizar = " ordfre = '{$wfecha}' " : $campoActualizar = " ordfen = '{$wfecha}'";
        $query     = "UPDATE uvglobal_000133
                         SET {$campoActualizar},
                             Orduum = '$wcodigoUsuario'
                       WHERE ordnro = '$wnro'";
        $rs        = mysql_query( $query, $conex ) or die( mysql_error());
        $afectados = mysql_affected_rows();
        $datos = array( 'afectados'=>$afectados );
        echo json_encode( $datos );
        return;
    }
}
?>
<head>
<title>ORDENES DE LABORATORIO</title>
<style>
    /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
    .ui-datepicker {font-size:12px;}
    #ui-datepicker-div {font-size:13px;}
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
</style>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script>
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
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['esp']);
</script>
<script type="text/javascript">

    function deshabilitarCampo( campo ){
        campo.disabled = true;
    }

    function habilitarCampo( campo ){
        campo.disabled = false;
    }

    function desactivarRango( campo ){

        valor = campo.value;

        switch( valor ){
        case "1":
        case "2": habilitarCampo( uvglobal01.wran ); break;
        case "3":
        case "4": deshabilitarCampo(  uvglobal01.wran  ); break;
        default: break;
        }
    }

    function desactivarAlturaBifocal(){

        ledadd = uvglobal01.wdad;
        leiadd = uvglobal01.wiad;

        if( leiadd.options[ leiadd.selectedIndex ].value != "" || ledadd.options[ ledadd.selectedIndex ].value != "" ){
            habilitarCampo( uvglobal01.wbif );
        }
        else{
            deshabilitarCampo( uvglobal01.wbif );
        }
    }

    function deshabilitarCamposMonturaUVG(){
        deshabilitarCampo( uvglobal01.wref );   //Deshabilita el campo Cod.Montura

        if( uvglobal01.wedita.value != "disabled" ){
            deshabilitarCampo( uvglobal01.wvem );
        }   //Deshabilita el campo Vendedor de Montura
    }

    function habilitarSignoEsfera( lente ){

        //Los valores del lente son
        //1 : Lente Derecho
        //2 : Lente Izquierdo

        led = uvglobal01.wdes;
        lei = uvglobal01.wies;

        if( lente == 1 ){
            if(led.value == "N" ){
                deshabilitarCampo( uvglobal01.wdsi );
            }
            else{
                habilitarCampo( uvglobal01.wdsi );
            }
        }

        if( lente == 2 ){
            if(lei.value == "N" ){
                deshabilitarCampo( uvglobal01.wisi );
            }
            else{
                habilitarCampo( uvglobal01.wisi );
            }
        }
    }

    function habilitarCamposMonturaUVG(){
        habilitarCampo( uvglobal01.wref );  //habilita el campo Cod.Montura

        if( uvglobal01.wedita.value != "disabled" ){
            habilitarCampo( uvglobal01.wvem );  //habilita el campo Vendedor de Montura
        }

        habilitarCampo( uvglobal01.wmet );  //habilita el campo Material
        habilitarCampo( uvglobal01.wcom );  //habilita el campo Diseño
        habilitarCampo( uvglobal01.wcol );  //habilita el campo Color
    }

    function deshabilitarCamposMonturaPropia(){

        deshabilitarCampo( uvglobal01.wpin[0] );    //Deshabilita el campo Cod.Montura
        deshabilitarCampo( uvglobal01.wbra[0] );    //Deshabilita el campo Vendedor de Montura
        deshabilitarCampo( uvglobal01.wter[0] );    //Deshabilita el campo Material
        deshabilitarCampo( uvglobal01.wpla[0] );    //Deshabilita el campo Diseño
        deshabilitarCampo( uvglobal01.wotr[0] );    //Deshabilita el campo Color
        deshabilitarCampo( uvglobal01.wpin[1] );    //Deshabilita el campo Cod.Montura
        deshabilitarCampo( uvglobal01.wbra[1] );    //Deshabilita el campo Vendedor de Montura
        deshabilitarCampo( uvglobal01.wter[1] );    //Deshabilita el campo Material
        deshabilitarCampo( uvglobal01.wpla[1] );    //Deshabilita el campo Diseño
        deshabilitarCampo( uvglobal01.wotr[1] );    //Deshabilita el campo Color
        deshabilitarCampo( uvglobal01.wde1 );   //Deshabilita el campo Cod.Montura
        deshabilitarCampo( uvglobal01.wde2 );   //Deshabilita el campo Vendedor de Montura
        deshabilitarCampo( uvglobal01.wde3 );   //Deshabilita el campo Material
        deshabilitarCampo( uvglobal01.wde4 );   //Deshabilita el campo Diseño
        deshabilitarCampo( uvglobal01.wde5 );   //Deshabilita el campo Color

    }

    function habilitarCamposMonturaPropia(){
        habilitarCampo( uvglobal01.wpin[0] );   //habilita el campo Cod.Montura
        habilitarCampo( uvglobal01.wbra[0] );   //habilita el campo Vendedor de Montura
        habilitarCampo( uvglobal01.wter[0] );   //habilita el campo Material
        habilitarCampo( uvglobal01.wpla[0] );   //habilita el campo Diseño
        habilitarCampo( uvglobal01.wotr[0] );   //habilita el campo Color
        habilitarCampo( uvglobal01.wpin[1] );   //habilita el campo Cod.Montura
        habilitarCampo( uvglobal01.wbra[1] );   //habilita el campo Vendedor de Montura
        habilitarCampo( uvglobal01.wter[1] );   //habilita el campo Material
        habilitarCampo( uvglobal01.wpla[1] );   //habilita el campo Diseño
        habilitarCampo( uvglobal01.wotr[1] );   //habilita el campo Color
        habilitarCampo( uvglobal01.wde1 );  //habilita el campo Cod.Montura
        habilitarCampo( uvglobal01.wde2 );  //habilita el campo Vendedor de Montura
        habilitarCampo( uvglobal01.wde3 );  //habilita el campo Material
        habilitarCampo( uvglobal01.wde4 );  //habilita el campo Diseño
        habilitarCampo( uvglobal01.wde5 );  //habilita el campo Color
        habilitarCampo( uvglobal01.wmet );  //habilita el campo Material
        habilitarCampo( uvglobal01.wcom );  //habilita el campo Diseño
        habilitarCampo( uvglobal01.wcol );  //habilita el campo Color
    }

    /*
     * Segun la opcion Escogida habilita los botones necesarios para
     * la montura
     */
    function camposMontura( valor ){

        //valor indica la opcion elegida para el propietario de la montura
        //1 : Montura Propia
        //2 : Montura U.V.G.
        //3 : Solo Lentes

        switch( valor ){

            case 1:{
                deshabilitarCamposMonturaUVG();
                habilitarCamposMonturaPropia();
                habilitarCamposLentes()
            } break;

            case 2:{
                habilitarCamposMonturaUVG();
                deshabilitarCamposMonturaPropia();
                habilitarCamposLentes()
            } break;

            case 3:{
                 deshabilitarCamposMonturaUVG();
                 deshabilitarCamposMonturaPropia();
                 deshabilitarCampo( uvglobal01.wmet );  //Deshabilita el campo Material
                 deshabilitarCampo( uvglobal01.wcom );  //Deshabilita el campo Diseño
                 deshabilitarCampo( uvglobal01.wcol );  //Deshabilita el campo Color
                 habilitarCamposLentes()
            } break;

            case 4:{
                deshabilitarCamposMonturaUVG();
                deshabilitarCamposMonturaPropia();
                deshabilitarCampo( uvglobal01.wmet );   //Deshabilita el campo Material
                deshabilitarCampo( uvglobal01.wcom );   //Deshabilita el campo Diseño
                deshabilitarCampo( uvglobal01.wcol );   //Deshabilita el campo Color
                deshabilitarCamposLenteDerecho();
                deshabilitarCamposLenteIzquierdo();
                deshabilitarCampo( uvglobal01.wedp );
                deshabilitarCampo( uvglobal01.wtra );
                deshabilitarCampo( uvglobal01.wbif );
                deshabilitarCampo( uvglobal01.wvel );
            } break;

            default: break;
        }

    }

    function habilitarCamposLenteDerecho(){
        habilitarCampo( uvglobal01.wdsi );
        habilitarCampo( uvglobal01.wdes );
        habilitarCampo( uvglobal01.wdci );
        habilitarCampo( uvglobal01.wdej );
        habilitarCampo( uvglobal01.wdad );
        habilitarCampo( uvglobal01.wdte );

        if( uvglobal01.wdes.value == "N" ){
            deshabilitarCampo( uvglobal01.wdsi );
        }

    }

    function deshabilitarCamposLenteDerecho(){
        deshabilitarCampo( uvglobal01.wdsi );
        deshabilitarCampo( uvglobal01.wdes );
        deshabilitarCampo( uvglobal01.wdci );
        deshabilitarCampo( uvglobal01.wdej );
        deshabilitarCampo( uvglobal01.wdad );
        deshabilitarCampo( uvglobal01.wdte );
    }

    function habilitarCamposLenteIzquierdo(){
        habilitarCampo( uvglobal01.wisi );
        habilitarCampo( uvglobal01.wies );
        habilitarCampo( uvglobal01.wici );
        habilitarCampo( uvglobal01.wiej );
        habilitarCampo( uvglobal01.wiad );
        habilitarCampo( uvglobal01.wite );
    }

    function deshabilitarCamposLenteIzquierdo(){
        deshabilitarCampo( uvglobal01.wisi );
        deshabilitarCampo( uvglobal01.wies );
        deshabilitarCampo( uvglobal01.wici );
        deshabilitarCampo( uvglobal01.wiej );
        deshabilitarCampo( uvglobal01.wiad );
        deshabilitarCampo( uvglobal01.wite );
    }

    function habilitarCamposLentes(){

        led = uvglobal01.wled;
        lei = uvglobal01.wlei;
        der = uvglobal01.der;
        izq = uvglobal01.izq;
        ven = uvglobal01.wvel;


        if( led.value != "" && der.value == 1 ){
            habilitarCamposLenteDerecho();
        }
        else{
            deshabilitarCamposLenteDerecho();
        }

        if( lei.value != "" && izq.value == 1 ){
            habilitarCamposLenteIzquierdo();
        }
        else{
            deshabilitarCamposLenteIzquierdo();
        }

        if( (lei.value != "" && izq.value == 1) || (led.value != "" && der.value == 1 ) ){
            habilitarCampo( ven );
        }
        else{
            deshabilitarCampo( ven );
        }

        habilitarCampo( uvglobal01.wedp );
        habilitarCampo( uvglobal01.wtra );

        if( uvglobal01.wdad.options[ uvglobal01.wdad.selectedIndex ].value != "" ){
            habilitarCampo( uvglobal01.wbif );
        }

    }

    function actualizarFecha( input, campo ){
        var wnro           = $("#wnro").val();
        var wfecha         = $(input).val();
        var wcodigoUsuario = $("#wcodigoUsuario").val();
        $.ajax({
                url: "uvglobal01_fechaAjax.php",
               type: "POST",
              async: false,
               data: {
                     consultaAjax: "actualizarFecha",
                             wnro: wnro,
                            campo: campo,
                           wfecha: wfecha,
                   wcodigoUsuario: wcodigoUsuario,
				   wven:$("#wven").val()
                      },
                success: function(data)
                {
                    if( data.afectados*1 > 0 ){
                        if( campo == "wfre" )
                            alerta("Fecha de recepcion Actualizada");
                        else
                            alerta("Fecha de entrega Actualizada");
                    }
                },
                dataType: "json"
            });

    }

    function alerta( txt ){
        $("#textoAlerta").text( txt );
        $.blockUI({ message: $('#msjAlerta') });
            setTimeout( function(){
                            $.unblockUI();
                            enter();
                        }, 1600 );
    }

    function enter(){
      document.forms.uvglobal01.submit();   // Ojo para la funcion uvglobal01 <> Uvglobal01  (sencible a mayusculas)
    }

	function iragrabar()
	{
		$("#wdat").prop("checked", "checked");
		document.forms.uvglobal01.submit();
	}
</script>

<script>
function ira(){document.uvglobal01.wdoc.focus();}
</script>
</head>
<body  onload=ira() BGCOLOR="">
<BODY TEXT="#000066">
<?php
//==========================================================================================================================================
//PROGRAMA                    :Genera Ordenes de Laboratorio VI.
//AUTOR                       :Jair Saldarriaga Orozco.
//FECHA CREACION              :ENERO 16 DE 2008.
//FECHA ULTIMA ACTUALIZACION  :25 de Julio de 2008.
//FECHA ULTIMA ACTUALIZACION  :04 de Septiembre de 2008.
//FECHA ULTIMA ACTUALIZACION  :02 de Mayo de 2012.
//FECHA ULTIMA ACTUALIZACION  :08 de Mayo de 2012.
//
// Septiembre 03 de 2015 - Felipe Alvarez : se modifica el programa para que funcione con la venta y no con la factura esto es: que se llame la orden de laboratorio
//											con la venta , se pidan los lentes , las monturas desde la venta.
//
//
// Septiembre 26 de 2012    (Camilo Zapata): - Se modificó el programa para que cualquier usuario pueda actualizar la fecha de recepción de la orden, ademas se configuró para que seleccioné por
//                                             por calendario, y no se escriba la fecha, con el propósito de evitar errores.
// Mayo 08 de 2012          (Edwar)   : - (2012-05-08) se valída que $wlei es igual $wled, exista disponibilidad en la factura para mostrar los dos códigos, sino es así
//                                        se debe ver sólo un código en los campos de código de lente.
// Mayo 02 de 2012          (Edwar)   : - Se modifican las consultas para lista de 'Vendedor del lente' y 'Vendedor de la Montura' para que no aparezca usuarios Inactivos
//                                      - Se adiciona la opción 'NO APLICA' en el seleccionador del bifocal (este no aparecía al editar)
//                                      - Al crear el <select> del bifocal se estaba anteponiendo el signo '-' al value del <option> pero esto se quitó,
//                                           al tener ese signo impedía en algunos casos se pudiera editar esta opción (al editar se creaban los <option> sin el signo '-').
// Diciembre 5 de 2011      (Edwin MG): - Se borra el tipo del lente para las ordenes de laboratorio
//                                      - Se modifica para que solo un administrador pueda cambiar la orden.
// Agosto 29 de 2011        (Edwin MG): Se corrige la función tieneNCFacturacion, para evitar que las facturas con notas creditos se puedan facturar.
//                                      Ademas se agrega validación para el eje, este es: si se elige un cilidro, se debe elegir un eje.
//                                      Si un lente tiene codigo invima se trae automaticamente
// Agosto 5 de 2011         (Edwin MG): Se agrega código invima y lote para los lentes, tanto derecho como izquierdo, con su correspondiente validación.
//                                      La validacion es: Si coloca la fecha de entrega y hay lente, se debe colocar el codigo invima y lote correspondinte
//                                      al lente.
//==========================================================================================================================================

class clEstados{
    var $pintura;
    var $brazos;
    var $terminales;
    var $plaquetas;
    var $otros;

    function __construct(){
        $pintura = array();
        $pintura['estado'] = '';
        $pintura['descripcion'] = '';

        $brazos = array();
        $brazos['estado'] = '';
        $brazos['descripcion'] = '';

        $terminales = array();
        $terminales['estado'] = '';
        $terminales['descripcion'] = '';

        $plquetas = array();
        $plaquetas['estado'] = '';
        $plaquetas['descripcion'] = '';

        $otros = array();
        $otros['estado'] = '';
        $otros['descripcion'] = '';
    }
}

class clLentes{
    var $codigo;
    var $signoesfera;
    var $esfera;
    var $cilindro;
    var $eje;
    var $add;
    var $tipo;
    var $vendedor;

    /************************************************************
     * Diciembre 5 de 2011
     * Por defecto el tipo queda como NA (No aplica)
     ************************************************************/
    function __construct(){
        $this->tipo = "NA";
    }
    /************************************************************/
}

class clMonturas{

    var $codigo;
    var $vendedor;
    var $propietario;
    var $material;
    var $diseno;
    var $color;
    var $estados;

    function __construct(){
        $vendedor = '';
        $propietario = '';
        $material = '';
        $diseno = '';
        $color = '';

        $estado = new clEstados();
    }
}

/****************************************************************
 * Consulta el codigo invima de los lentes
 ****************************************************************/
function consultarCodigoInvima( $codigo ){

    global $conex;

    $val = "";

    $sql = "SELECT
                *
            FROM
                uvglobal_000001
            WHERE
                artcod = '$codigo'
                AND artest = 'on'
                AND artima != 'NO APLICA'
            ";

    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ". mysql_error() );
    $numrows = mysql_num_rows( $res );

    if( $numrows > 0 ){

        $rows = mysql_fetch_array( $res );

        $val = $rows['Artima'];
    }

    return $val;
}

/**
 * Retorna verdadero si una factura tiene nota credito de facturacion
 *
 * @param $fac
 * @param $cco
 * @return unknown_type
 */
function tieneNCFacturacion( $fac, $cco ){

    global $conex;

    $exp = explode( "-", $cco );

    $sql = "SELECT
                rdefac, rdefue, rdenum
            FROM
                uvglobal_000021, uvglobal_000003
            WHERE
                rdefac = '$fac'
                AND rdefue = ccofnc
                AND rdecco = ccocod
                AND ccocod = '{$exp[0]}'
                AND rdeest = 'on'
            "; //echo "<br>.......<pre>$sql</pre>";

    $sql = "SELECT
                rdefac, rdefue, rdenum
            FROM
                uvglobal_000021, uvglobal_000003
            WHERE
                rdefac = '$fac'
                AND rdefue = ccofnc
                AND rdecco = ccocod
                AND rdeest = 'on'
            "; //echo "<br>.......<pre>$sql</pre>";
   // echo "--------<br>".$sql;
    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

    if( mysql_num_rows( $res ) > 0 ){
        return true;
    }
    else{
        return false;
    }
}

function esAdministrador( $codigo ){

    global $conex;

    $esadmin = false;

    $sql = "SELECT
                cjeadm
            FROM
                uvglobal_000030
            WHERE
                cjeusu = '$codigo'
                and cjeest = 'on'";

    $res = mysql_query( $sql, $conex ) or die( mysql_errno()."- Error en el query $sql -".mysql_error() );

    if( $rows =  mysql_fetch_array($res) ){
        if( $rows[0] == 'on' ){
            $esadmin = true;
        }
    }

    return $esadmin;
}

/**
 * Graba la accion sobre la orden ingresada, ya sea creada o modificada
 *
 * @param $usuario
 * @param $accion
 * @param $ord
 * @return unknown_type
 */
function grabarAccion( $usuario, $accion, $ord ){

    global $conex;
    global $wbasedato;

    $fecha = date( "Y-m-d" );
    $hora = date( "H:i:s" );

    $sql = "INSERT INTO {$wbasedato}_000135
                    (   Medico  , fecha_data, hora_data, ordnro,  ordfec  ,  ordhor ,  ordacc  ,   ordusu  ,  seguridad  )
            VALUES	( 'uvglobal', '$fecha'  ,  '$hora' , '$ord', '$fecha' , '$hora' , '$accion', '$usuario', 'C-uvglobal')";

    $res = mysql_query( $sql ) or die( mysql_errno()." - Error al insertar datos en la tabla 135 $sql - ".mysql_error() );

    if( mysql_affected_rows( $res ) > 0 ){
        return true;
    }
    else{
        return false;
    }

}

/**
 * Consulto el signo de la esfera, como esta en la orden
 *
 * @param $nro
 * @param $lado
 * @return unknown_type
 */
function consultarSignoEsfera( $nro, $lado ){

    global $conex;
    global $wbasedato;

    $signo = "";

    $sql = "SELECT
                orddsi, ordisi
            FROM
                uvglobal_000133
            WHERE
                ordnro = '$nro'";

    $res = mysql_query( $sql, $conex ) or die ( mysql_errno()." - Error en la consulta $sql - ".mysql_error() );

    if( $rows = mysql_fetch_array( $res ) ){

        if( $lado == 0 ){
            $signo = $rows[0];
        }

        if( $lado == 1 ){
            $signo = $rows[1];
        }

    }

    return $signo;
}


/**
 * Devuvelve el codigo de quien creo la orden de laboratorio
 * @param $orden
 * @return unknown_type
 */
function creadorOrdenLaboratorio( $orden ){

    global $conex;
    global $wbasedato;

    $codigo = "";

    $sql = "SELECT
                venusu
            FROM
                uvglobal_000133,
                uvglobal_000016
            WHERE
                ordfac = vennfa
                AND ordffa = venffa
                AND ordnro = '$orden'
            ";

    $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

    if( $rows = mysql_fetch_array( $res ) ){
        $codigo = $rows['venusu'];
    }

    return $codigo;
}

/**
 * Busca si hay una orden de Laboratorio para la factura dada
 *
 * @param $fac
 * @return unknown_type
 */
function ordenLaboratorio( $fac ){

    global $conex;
    global $wbasedato;

	if ($fac == '')
	{
		return false;
	}

    $wbasedato='uvglobal';

    $sql = "SELECT
                *
            FROM
                {$wbasedato}_000133
            WHERE
                ordfac = '$fac'";

    $res = mysql_query( $sql, $conex );
    $num = mysql_num_rows( $res );

    if( $num > 0 )
        return true;
    else
        return false;
}

/**
 *
 * @return unknown_type
 */
function validarMontura( $montura ){

    global $mensaje;

    if( !empty($montura->codigo) && $montura->propietario == 2 ){

        if( empty($montura->vendedor) ){
            $mensaje = "DEBE SELECCIONAR UN VENDEDOR DE LA MONTURA";
            return;
        }
    }

    if( $montura->propietario != 3 ){

        if( empty($montura->material) ){
            $mensaje = "DEBE SELECCIONAR UN MATERIAL PARA LA MONTURA";
            return;
        }

        if( empty($montura->diseno) ){
            $mensaje = "DEBE SELECCIONAR UN DISEÑO PARA LA MONTURA";
            return;
        }

        if( empty($montura->color) ){
            $mensaje = "DEBE INGRESAR UN COLOR PARA LA MONTURA";
            return;
        }
    }

    //se valida los estados
    if( $montura->propietario == 1 ){

        if( $montura->estados->pintura['estado'] == 2
            && empty($montura->estados->pintura['descripcion'] )
        ){
            $mensaje = "DEBE INGRESAR UNA DESCRIPCION PARA PINTURA DE LA MONTURA";
            return;
        }
        else if( empty($montura->estados->pintura['estado']) ){
            $mensaje = "DEBE SELECCIONAR EL ESTADO DE LA PINTURA DE LA MONTURA";
            return;
        }

        if( $montura->estados->brazos['estado'] == 2
            && empty($montura->estados->brazos['descripcion'])
        ){
            $mensaje = "DEBE INGRESAR UNA DESCRIPCION PARA LOS BRAZOS DE LA MONTURA";
            return;
        }
        else if( empty($montura->estados->brazos['estado']) ){
            $mensaje = "DEBE SELECCIONAR EL ESTADO DE LOS BRAZOS DE LA MONTURA";
            return;
        }

        if( $montura->estados->terminales['estado'] == 2
            && empty($montura->estados->terminales['descripcion'])
        ){
            $mensaje = "DEBE INGRESAR UNA DESCRIPCION PARA LAS TERMINALES DE LA MONTURA";
            return;
        }
        else if( empty($montura->estados->terminales['estado']) ){
            $mensaje = "DEBE SELECCIONAR EL ESTADO DE LAS TERMINALES DE LA MONTURA";
            return;
        }

        if( $montura->estados->plaquetas['estado'] == 2
            && empty($montura->estados->plaquetas['descripcion'])
        ){
            $mensaje = "DEBE INGRESAR UNA DESCRIPCION PARA LAS PLAQUETAS DE LA MONTURA";
            return;
        }
        else if( empty($montura->estados->plaquetas['estado']) ){
            $mensaje = "DEBE SELECCIONAR EL ESTADO DE LAS PLAQUETAS DE LA MONTURA";
            return;
        }

        if( $montura->estados->otros['estado'] == 2
            && empty($montura->estados->otros['descripcion'] )
        ){
            $mensaje = "DEBE INGRESAR UNA DESCRIPCION PARA EL CAMPO OTROS DE LA MONTURA";
            return;
        }
        else if( empty($montura->estados->otros['estado']) ){
            $mensaje = "DEBE SELECCIONAR EL ESTADO DE OTROS DE LA MONTURA";
            return;
        }
    }

}
/**
 * Construye Mensajes para mostrar para los lentes
 *
 * @param $ld
 * @param $li
 * @return unknown_type
 */

function construirMensajes( $ld ){

    global $mensaje;

    if( empty($ld->signoesfera) && $ld->esfera != "N" ){
        $mensaje = "DEBE SELECCIONAR EL SIGNO DE LA ESFERA";
        return;
    }

    if( empty($ld->esfera) ){
        $mensaje = "DEBE SELECCIONAR UN VALOR PARA EL CAMPO ESFERA DEL LENTE";
        return;
    }

    // if( empty($ld->tipo) ){
        // $mensaje = "DEBE SELECCIONAR UN VALOR PARA EL CAMPO TIPO DEL LENTE";
        // return;
    // }

    if( empty($ld->vendedor) ){
        $mensaje = "DEBE SELECCIONAR UN VENDEDOR DE LENTE";
        return;
    }
}

function validar_datos($fe,$do,$fu,$fa,$ob,$fr,$ff,$ld,$li,$re, $clLd, $clLi, $mn, $wran, $wedp, $wtra, $wbif, $wcaj, $wtus, $wind, $wlod, $wini, $wloi )
 {
  //La fn recibe fecha,docmto,factura,observac,Fecha de recibo,fecha entrega,Lente Der,Lente Izq,Montura (referencia)
   global $todok;
   $todok = true;

   global $mensaje;

   $mensaje = "ERROR EN LOS DATOS DIGITADOS, DEBE TENER OBSERVACIONES!!!!";

   $query = "Select * From uvglobal_000041 Where Clidoc='".$do."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   $registro = mysql_fetch_row($resultado);
   $wnombre = $registro[4];
   if ($nroreg < 1 )     //No encontro
     $todok = false;

   // Chequeo la fecha con checkdate(mm,dd,aaaa)
   if ( !checkdate(substr($fe,5,2), substr($fe,8,2), substr($fe,0,4)) ){
        $todok = false;
        $mensaje = "LA FECHA DE ENVIO ES INCORRECTA!!!!";
    }

   // Chequeo la fecha con checkdate(mm,dd,aaaa)
   if ( ($fr != '0000-00-00') and ($fr != '') )
   {
        if ( !checkdate(substr($fr,5,2), substr($fr,8,2), substr($fr,0,4)) )
        {
            $todok = false;
            $mensaje = "LA FECHA DE RECIBO ES INCORRECTA!!!!";
        }
    }

   // Chequeo la fecha con checkdate(mm,dd,aaaa)
   if ( ($ff != '0000-00-00') and ($ff != '') )
   {
        if ( !checkdate(substr($ff,5,2), substr($ff,8,2), substr($ff,0,4)) )
        {
            $todok = false;
            $mensaje = "LA FECHA DE ENTREGA ES INCORRECTA!!!!";
        }
    }

   //if ( $fa == "" )    //Se quedo que pueden haber ordenes sin factura
   //  $todok = false;

   if ( $ob == "" ){
     $todok = false;
     $mensaje = "ERROR EN LOS DATOS DIGITADOS, DEBE TENER OBSERVACIONES!!!!";
    }

    if( $mn->propietario != "4" )
    {
        if ( $ld != "" )
        {
            $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
            $query=$query." WHERE Venffa = '".$fu."' And Vennfa = '".$fa."' And Vdenum = Vennum And Vdeart = Artcod";
            $query=$query." And Artcod = '".$ld."' And (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE');";
            $resultado = mysql_query($query);
            $nroreg = mysql_num_rows($resultado);
            if ($nroreg == 0)   //No Encontro codigo de Lente Derecho
            { $todok = false; }

            if( empty($clLd->esfera) || empty($clLd->tipo) || ( empty($clLd->signoesfera) && $clLd->esfera != "N" || empty($clLd->vendedor) ) )
            {
                $todok = false;
                construirMensajes( $clLd );
            }

            if( !empty($clLd->esfera) && ( !empty($clLd->cilindro) && $clLd->eje == "" ) )
            {
                $mensaje = "DEBE SELECCIONAR EJE PARA EL CILINDRO DEL OJO DERECHO";
                $todok = false;
            }
        }

        if ( $li != "" )
        {
            $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
            $query=$query." WHERE Venffa = '".$fu."' And Vennfa = '".$fa."' And Vdenum = Vennum And Vdeart = Artcod";
            $query=$query." And Artcod = '".$li."' And (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE' );";
            $resultado = mysql_query($query);
            $nroreg = mysql_num_rows($resultado);
            if ($nroreg == 0)   //No Encontro codigo de Lente Izquierdo
            { $todok = false; }

            if( empty($clLi->esfera) || empty($clLi->tipo) || ( empty($clLi->signoesfera) && $clLi->esfera != "N" || empty($clLi->vendedor) ) )
            {
                $todok = false;
                construirMensajes( $clLi );
            }

            if( !empty($clLi->esfera) && ( !empty($clLi->cilindro) && $clLi->eje == "" ) )
            {
                $mensaje = "DEBE SELECCIONAR EJE PARA EL CILINDRO DEL OJO IZQUIERDO";
                $todok = false;
            }
        }
    }
    else
    {
        if( ( empty($clLd->codigo) || $clLd->codigo == "")  && ( empty($clLi->codigo) ||$clLi->codigo == "" ) ){
            $mensaje = "DEBE INGRESAR UN CODIGO PARA EL LENTE LENTE";
            $todok = false;
            return;
        }
    }

    if( $mn->propietario == "2" )
    {
        if ( $re != "" )
        {
            $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
            $query=$query." WHERE Venffa = '".$fu."' And Vennfa = '".$fa."' And Vdenum = Vennum And Vdeart = Artcod";
            $query=$query." And Artcod = '".$re."' And mid(Artgru,1,2) = 'MT';";
            $resultado = mysql_query($query);
            $nroreg = mysql_num_rows($resultado);
            if ($nroreg == 0)   //No Encontro codigo de la montura o referencia
            { $todok = false; }

            if( empty($mn->vendedor) || empty($mn->material) || empty($mn->color) || empty($mn->diseno) )
            {
                $todok = false;
                validarMontura( $mn );
            }
        }
        else{
            $mensaje = "DEBE INGRESAR EL CODIGO DE LA MONTURA";
            $todok = false;
        }
    }
   else if( $mn->propietario == "1" )
   {
        if( empty($mn->material)
            || empty($mn->color)
            || empty($mn->diseno)
            || ($mn->estados->pintura['estado'] == 2 && empty($mn->estados->pintura['descripcion']) )
            || ($mn->estados->brazos['estado'] == 2 && empty($mn->estados->brazos['descripcion']) )
            || ($mn->estados->terminales['estado'] == 2 && empty($mn->estados->terminales['descripcion']) )
            || ($mn->estados->plaquetas['estado'] == 2 && empty($mn->estados->plaquetas['descripcion']) )
            || ($mn->estados->otros['estado'] == 2 && empty($mn->estados->otros['descripcion']) )
            || empty( $mn->estados->pintura['estado'] )
            || empty( $mn->estados->brazos['estado'] )
            || empty( $mn->estados->terminales['estado'] )
            || empty( $mn->estados->plaquetas['estado'] )
            || empty( $mn->estados->otros['estado'] )
          ){
            $todok = false;
            validarMontura( $mn );
         }
   }

   if( empty($wran) && ( $wtus == 1 || $wtus == 2 )  ){
        $todok = false;
        $mensaje = "DEBE SELECCIONAR UN RANGO";
   }

    if( empty($wedp) && $mn->propietario != "4" ){
        $todok = false;
        $mensaje = "DEBE INGRESAR UN VALOR PARA D.P.";
   }

   if( empty( $wtra ) && $mn->propietario != "4" ){
        $todok = false;
        $mensaje = "DEBE INGRESAR EL TRATAMIENTO";
   }

    if( empty( $wbif ) && ( !empty($clLd->add) || !empty($clLi->add) ) ){
        $todok = false;
        $mensaje = "DEBE SELECCIONAR UN VALOR PARA LA ALTURA DEL BIFOCAL";
   }


    if( empty( $wcaj ) ){
        $todok = false;
        $mensaje = "DEBE INGRESAR LA CAJA";
   }

   //Si la fecha final ya se escribio, el codigo invima para el lente derecho y el lote deben ser escritos
   if( ( $ff != '' && $ff != '0000-00-00' ) && !empty($clLd->codigo) && ( empty( $wind ) || empty( $wlod ) ) ){
        $todok = false;
        $mensaje = "DEBE INGRESAR EL CODIGO INVIMA Y EL LOTE PARA EL LENTE DERECHO";
   }

   //Si la fecha final ya se escribio, el codigo invima para el lente izquierdo y el lote deben ser escritos
   if( ( $ff != '' && $ff != '0000-00-00' ) && !empty($clLi->codigo) && ( empty( $wini ) || empty( $wloi ) ) ){
        $todok = false;
        $mensaje = "DEBE INGRESAR EL CODIGO INVIMA Y EL LOTE PARA EL LENTE IZQUIERDO";
   }

   return $todok;
 }


session_start();
if(!isset($_SESSION['user']))
    echo "error";
else
{
    $izq = 1;
    $der = 1;
    $wreadonly = "";

    if( !isset( $wtus )){
        $wtus= '';
    }

    if( !isset( $wnro ) ){
        $wnro = '';
    }

    $key = substr($user, 2, strlen($user));
    //$user = "1-uvla01";   //Temporal!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    $mensaje = '';



	//echo "aaaaaaaaaaaaaaaaaaaaa".$_GET[ 'wven' ]."aa".$wproceso."ddd".$wdoc;
	//var_dump( $_GET );


    mysql_select_db("matrix") or die("No se selecciono la base de datos");

    $wdsi = @trim($wdsi);
    $wisi = @trim($wisi);
    if( $wproceso == "Modificar" ){
        if( !isset( $wdsi ) || empty($wdsi) ){
            $wdsi = consultarSignoEsfera( $wnro, 0 );
        }

        if( !isset( $wisi ) || empty($wisi) ){
            $wisi = consultarSignoEsfera( $wnro, 1 );
        }
    }

    $query = "Select Cjecco From uvglobal_000030 Where Cjeusu = '".substr($user,2,80)."'";
    $resultado = mysql_query($query);
    $registro = mysql_fetch_row($resultado);
    $sede = $registro[0];

    echo "<form name='uvglobal01' action='uvglobal01_fechaAjax.php' method=post>";


	if($wven=='')
	{
		$queryventa = "  SELECT Fdenve
						 FROM 	uvglobal_000019
						 WHERE 	Fdeffa ='".$wffa."'
						 AND  	Fdefac = '".$wfac."'";

		$resultado = mysql_query($queryventa);
		while ($registro = mysql_fetch_array($resultado))
		{
			$wven = $registro['Fdenve'];

		}
	}

	//echo $queryventa."---".$wven;
    echo "<center><table border=1 >";
    echo "<tr><td colspan=1 rowspan=4  align=center><IMG SRC='/matrix/images/medical/pos/logo_uvglobal.png' ></td>";
    echo "<tr><td colspan=5 align=center><b>UNIDAD VISUAL GLOBAL S.A.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sede."<b></td></tr>";
    echo "<tr><td colspan=5 align=center><b>NIT. 811.017.919-1<b></td></tr>";
    echo "<tr><td colspan=5 align=center><b>ORDEN DE LABORATORIO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NRO: &nbsp;&nbsp;&nbsp;";

    if (($wproceso == "Nuevo") or (!isset($wproceso)))
    {
     //Tomo el consecutivo que sigue
     $wedita="";
     $query = "Select carcon From uvglobal_000040 Where Carfue = 'OT' And Carest = 'on' And Carotr = 'on' ";
     $resultado = mysql_query($query);
     $nroreg = mysql_num_rows($resultado);
     $registro = mysql_fetch_row($resultado);
     $wconsecutivo = $registro[0];
     //echo " ------------ entró acá y el nuevo consecutivo es: ".$wconsecutivo;
     echo "<INPUT TYPE='text' NAME='wnro' id='wnro' size=10 color=#003366 VALUE='".$wconsecutivo."' readonly></INPUT>";
    }
    else
    {
        $wedita="disabled";
        $wreadonly = "readOnly";
        echo "<INPUT TYPE='text' NAME='wnro' id='wnro' size=10 color=#003366 VALUE='".$wnro."' $wreadonly ></INPUT>";
    }

    echo "<INPUT TYPE='hidden' name='wedita' value='$wedita'>";

    echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>Cedula:</font></b><br>";
    if (isset($wdoc))
    {
        //echo " -------------- aca tambien Entro 22222";
         echo "<INPUT TYPE='text' NAME='wdoc' size=10 VALUE='".$wdoc."' $wreadonly></INPUT></td>";
         $query = "Select * From uvglobal_000041 Where Clidoc='".$wdoc."'";
         $resultado = mysql_query($query);
         $nroreg = mysql_num_rows($resultado);
         $registro = mysql_fetch_row($resultado);
         $wnombre = $registro[4];
         $wtelefono = $registro[5];
		 echo "<td align=center bgcolor=#DDDDDD ><b><font text color=#003366 size=2>Nro Venta</font></b><br>";

		 if($wproceso == 'Modificar')
		 {
			$modificarselect='disabled';
		 }
		 else
		 {
			$modificarselect ='';
		 }




		 echo "<select   NAME='wven' id='wven'  onchange='enter();'>";

		 if($modificarselect=='')
		 {
			 $query_venta = " SELECT Vennum
								   FROM uvglobal_000016 LEFT JOIN  uvglobal_000133 ON Vennum = ordven
								  WHERE Vennit = '".$wdoc."'
									AND Vennfa = ''
									AND ordven IS NULL";

			$res_venta = mysql_query($query_venta);
			while ($row_venta = mysql_fetch_array($res_venta))
			{
				if($wven == $row_venta['Vennum'])
				{
					echo "<option value='".$row_venta['Vennum']."' selected >".$row_venta['Vennum']."</option>";
				}
				else
				{
					echo "<option value='".$row_venta['Vennum']."' >".$row_venta['Vennum']."</option>";
				}
			}
		}
		else
		{
			echo "<option value='".$wven."' >".$wven."</option>";

		}

		 echo "</select>";

		 // echo "<INPUT TYPE='text' NAME='wven2' id='wven2' size=10 color=#003366 VALUE='".$wven."' readonly></INPUT></td>";
		 echo "</td>";
         echo "<td align=center bgcolor=#DDDDDD colspan=3><b><font text color=990000 size=4> ".$wnombre."</font></b></td>";
         echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=990000 size=3> Tel: ".$wtelefono."</font></b></td>";
    }
    else
    {
        echo "<INPUT TYPE='text' NAME='wdoc' size=10 onchange='enter()'></INPUT></td>";
        echo "<td align=center bgcolor=#DDDDDD ><font text color=#003366 size=2><b>Nro Venta</b></font><INPUT TYPE='text' NAME='wven' id='wven' size=10 color=#003366 VALUE='".$wven."' readonly></INPUT></td>";
		echo "<td align=center bgcolor=#DDDDDD colspan=2></td>";
        echo "<td align=center bgcolor=#DDDDDD colspan=2></td>";
    }

    //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
    //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
    echo "<tr><td bgcolor=#cccccc align=center colspan=3><b><font text color=#003366 size=2> <i>Rango: </font></b><select name='wran'>";
    if (!isset($wran)) //No esta seteada
    {
        echo "<option>";
        echo "<option value='1'>Rango 1";
        echo "<option value='2'>Rango 2";
        echo "<option value='3'>Rango 3";
    }
    else              //Ya esta seteada
    {
        if  ($wran == "")
        { echo "<option selected >"; }
        else
        { echo "<option>"; }

        if ($wran == "1")
        { echo "<option selected value='1'>Rango 1"; }
        else
        { echo "<option value='1'>Rango 1"; }

        if ($wran == "2")
        { echo "<option selected value='2'>Rango 2"; }
        else
        { echo "<option value='2'>Rango 2"; }

        if ($wran == "3")
        { echo "<option selected value='3'>Rango 3"; }
        else
        { echo "<option value='3'>Rango 3"; }
    }
    echo "</select></td>";



    echo "<td colspan=3 bgcolor=#cccccc align=center>";
    // para que se sostenga la seleccion hecha despues de un submit entonces:
    // (Si esta seteada y con valor uno) or si no esta seteada
    if ((isset($wtus) and $wtus == "1") )
    {
        echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='1' CHECKED onClick='javascript: desactivarRango(this);'>Beneficiario.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
        echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='2' onClick='javascript: desactivarRango(this);'>Cotizante.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
        echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='3' onClick='javascript: desactivarRango(this);'>Particular.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
        echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='4' onClick='javascript: desactivarRango(this);'>Prepagada.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
    }
    else
    {
        if ($wtus == "2")
        {
            echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='1'  onClick='javascript: desactivarRango(this);'>Beneficiario.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='2' CHECKED onClick='javascript: desactivarRango(this);'>Cotizante.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='3' onClick='javascript: desactivarRango(this);'>Particular.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='4' onClick='javascript: desactivarRango(this);'>Prepagada.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
        }
        else
        {
            if ($wtus == "3" or !isset($wtus) )
            {
                 echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='1'  onClick='javascript: desactivarRango(this);'>Beneficiario.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
                 echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='2'  onClick='javascript: desactivarRango(this);'>Cotizante.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
                 echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='3' CHECKED onClick='javascript: desactivarRango(this);'>Particular.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
                 echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='4' onClick='javascript: desactivarRango(this);'>Prepagada.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            }
            else
            {
                 echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='1'  onClick='javascript: desactivarRango(this);'>Beneficiario.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
                 echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='2'  onClick='javascript: desactivarRango(this);'>Cotizante.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
                 echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='3'  onClick='javascript: desactivarRango(this);'>Particular.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
                 echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='4' CHECKED onClick='javascript: desactivarRango(this);'>Prepagada.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            }
        }
    }

    echo "<tr>";
    echo "<td colspan=1 align=center><b>LENTES<b></td>";
    echo "<td colspan=1 align=center><b>ESFERA<b></td>";
    echo "<td colspan=1 align=center><b>CILINDRO<b></td>";
    echo "<td colspan=1 align=center><b>EJE<b></td>";
    echo "<td colspan=2 align=center><b>ADD<b></td>";
    echo "<td colspan=1 align=center style='display:none'><b>TIPO<b></td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Ojo Derecho:</font></b></td>";

     // CASO RARO si envio por el URL una variable que contiene el signo + este llega nulo
     // por lo que siempre los vuelvo a tomar del registro
     // if (($wproceso != "Nuevo") and  ( isset($wdat) ) )
     // if (($wproceso != "Nuevo") and  ( isset($wnro) ) )
     // {
     //  $query = "Select orddsi,ordisi From uvglobal_000133 where ordnro = ".$wnro;
     //  $resultado = mysql_query($query);
     //  $registro = mysql_fetch_row($resultado);
     //  $wdsi = $registro[0];
     //  $wisi = $registro[1];
     // }

         //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
         //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
         //  *** SIGNO DERECHO  ***
         echo "<td bgcolor=#cccccc align=center colspan=1><select name='wdsi'>";
         if (!isset($wdsi)) //No esta seteada
         {
          echo "<option selected>";
          echo "<option value='+'>+";
          echo "<option value='-'>-";
         }
         else              //Ya esta seteada
         {
          if  ($wdsi == "")
           echo "<option selected>";
          else
           echo "<option>";

          if ($wdsi == "+")
            echo "<option selected value='+'>+";
          else
            echo "<option value='+'>+";

          if ($wdsi == "-")
           echo "<option selected value='-'>-";
          else
           echo "<option value='-'>-";
         }
         echo "</select>";


        //  *** ESFERA DERECHA  ***
        echo "<select name='wdes' onChange='habilitarSignoEsfera( 1 );'>";
        if (!isset($wdes)) //No esta seteada
         echo "<option>";
        else               //Ya esta seteada
         if  ($wdes == "")
          echo "<option selected >";
         else
          echo "<option>";

        if (!isset($wdes)) //No esta seteada
         echo "<option value='N'>N";
        else               //Ya esta seteada
         if  ($wdes == "N")
          echo "<option selected value='N'>N";
         else
          echo "<option value='N'>N";

         $i=0.25;
         while ($i <= 25.00 )
         {
           //Formato con dos decimales coloca ceros
           $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);
           //Para que no imprima el cero a la izquierda
           if (substr($k,0,1) == 0)
             $k = " ".substr($k,1,4);

           if (!isset($wdes)) //No esta seteada
            echo "<option value=".$k.">".$k;
           else
            if ($wdes == $k)
             echo "<option selected value=".$k.">".$k;
            else
             echo "<option value=".$k.">".$k;
           $i = $i + 0.25;
         }
         echo "</select></td>";

        //  *** CILINDRO DERECHO  ***
        echo "<td bgcolor=#cccccc align=center colspan=1><font text color=#003366 size=4> <i> - </font></b><select name='wdci'>";
        if (!isset($wdci)) //No esta seteada
         echo "<option>";
        else               //Ya esta seteada
         if($wdci == "")
          echo "<option selected >";
         else
          echo "<option>";

        $i=0.25;
        while ($i <= 10.00 )
        {
          $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);
          //Para que no imprima el cero a la izquierda
           if (substr($k,0,1) == 0)
             $k = " ".substr($k,1,4);

          if (!isset($wdci)) //No esta seteada
           echo "<option value=".$k.">".$k;
          else
           if ($wdci == $k)
            echo "<option selected value=".$k.">".$k;
           else
            echo "<option value=".$k.">".$k;
          $i = $i + 0.25;
        }
        echo "</select></td>";

        //  *** EJE DERECHO  ***

        echo "<td bgcolor=#cccccc align=center colspan=1><select name='wdej'>";
        if (!isset($wdej)){ //No esta seteada
            echo "<option value='' selected></option>";
        }
        else{               //Ya esta seteada
            if( $wdej == "" ){
                echo "<option selected></option>";
            }
            else{
                echo "<option></option>";
            }
        }

        $i=0;
        while ($i <= 180 )
        {
            if (!isset($wdej)){ //No esta seteada
                echo "<option value='".$i."'>".$i;
            }
            else{               //Ya esta seteada
                if ( $wdej === "$i"){
                    echo "<option selected value='".$i."'>".$i;
                }
                else{
                    echo "<option value='".$i."'>".$i;
                }
            }

          $i = $i + 1;
        }
        echo "</select></td>";

        //  *** ADD DERECHO  ***
        echo "<td bgcolor=#cccccc align=center colspan=2><font text color=#003366 size=4> <i> + </font></b><select name='wdad' onChange='javascript: desactivarAlturaBifocal();'>";

        if (!isset($wdad)) //No esta seteada
         echo "<option>";
        else               //Ya esta seteada
         if  ($wdad == "")
          echo "<option selected >";
         else
          echo "<option>";

        $i=0.75;
        while ($i <= 3.50 )
        {
          $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);
          //Para que no imprima el cero a la izquierda
           if (substr($k,0,1) == 0)
             $k = " ".substr($k,1,4);

          if (!isset($wdad)) //No esta seteada
           echo "<option value=".$k.">".$k;
          else
           if ($wdad == $k)
            echo "<option selected value=".$k.">".$k;
           else
            echo "<option value=".$k.">".$k;
          $i = $i + 0.25;
        }
        echo "</select></td>";

         //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
         //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
         echo "<td bgcolor=#cccccc align=center colspan=1 style='display:none'><select name='wdte'>";
         if (!isset($wdte)) //No esta seteada
         {
          echo "<option>";
          echo "<option value='1'>Terminado";
          echo "<option value='2'>Tallado";
         }
         else              //Ya esta seteada
         {
         if ($wdte == "")
          echo "<option selected>";
         else
          echo "<option>";

         if ($wdte == "1")
          echo "<option selected value='1'>Terminado";
         else
          echo "<option value='1'>Terminado";

         if ($wdte == "2")
          echo "<option selected value='2'>Tallado";
         else
          echo "<option value='2'>Tallado";
         }
         echo "</select></td>";


        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Ojo Izquierdo:</font></b></td>";

         //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
         //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
          echo "<td bgcolor=#cccccc align=center colspan=1><select name='wisi'>";
         if (!isset($wisi)) //No esta seteada
         {
          echo "<option selected>";
           echo "<option value='+'>+";
          echo "<option value='-'>-";
         }
         else              //Ya esta seteada
         {
          if  ($wisi == "")
           echo "<option selected>";
          else
           echo "<option>";

          if ($wisi == "-"){
           echo "<option selected value='-'>-";
           echo "<option value='+'>+";
          }
          else if ($wisi == "+"){
            echo "<option selected value='+'>+";
            echo "<option value='-'>-";
          }
          else{
           echo "<option value='-'>-";
           echo "<option value='+'>+";
          }
         }
         echo "</select>";

        // *** ESFERA IZQUIERDA ****
        echo "<select name='wies' onChange='habilitarSignoEsfera( 2 );'>";
        if (!isset($wies)) //No esta seteada
         echo "<option>";
        else               //Ya esta seteada
         if  ($wies == "")
          echo "<option selected >";
         else
          echo "<option>";

        if (!isset($wies)) //No esta seteada
         echo "<option value='N'>N";
        else               //Ya esta seteada
         if  ($wies == "N")
          echo "<option selected value='N'>N";
         else
          echo "<option value='N'>N";

         $i=0.25;
         while ($i <= 25.00 )
         {
           $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);
           //Para que no imprima el cero a la izquierda
           if (substr($k,0,1) == 0)
             $k = " ".substr($k,1,4);
           if (!isset($wies)) //No esta seteada
            echo "<option value=".$k.">".$k;
           else
            if ($wies == $k)
             echo "<option selected value=".$k.">".$k;
            else
             echo "<option value=".$k.">".$k;
           $i = $i + 0.25;
         }
         echo "</select></td>";

        //  *** CILINDRO IZQUIERDO  ***
        echo "<td bgcolor=#cccccc align=center colspan=1><font text color=#003366 size=4> <i> - </font></b><select name='wici'>";
        if (!isset($wici)) //No esta seteada
         echo "<option>";
        else               //Ya esta seteada
         if  ($wici == "")
          echo "<option selected >";
         else
          echo "<option>";

        $i=0.25;
        while ($i <= 10.00 )
        {
          $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);
          //Para que no imprima el cero a la izquierda
           if (substr($k,0,1) == 0)
             $k = " ".substr($k,1,4);

          if (!isset($wici)) //No esta seteada
           echo "<option value=".$k.">".$k;
          else
           if ($wici == $k)
            echo "<option selected value=".$k.">".$k;
           else
            echo "<option value=".$k.">".$k;
          $i = $i + 0.25;
        }
        echo "</select></td>";

        //  *** EJE IZQUIERDO  ****
        echo "<td bgcolor=#cccccc align=center colspan=1><select name='wiej'>";
        if (!isset($wiej)) //No esta seteada
         echo "<option>";
        else               //Ya esta seteada
         if  ($wiej == "")
          echo "<option selected >";
         else
          echo "<option>";

        $i=0;
        while ($i <= 180 )
        {
          if (!isset($wiej)) //No esta seteada
           echo "<option value=".$i.">".$i;
          else               //Ya esta seteada
           if ($wiej == "$i")
            echo "<option selected value=".$i.">".$i;
           else
            echo "<option value=".$i.">".$i;

          $i = $i + 1;
        }
        echo "</select></td>";

         //  *** ADD IZQUIERDO  ***
        echo "<td bgcolor=#cccccc align=center colspan=2><font text color=#003366 size=4> <i> + </font></b><select name='wiad' onChange='javascript: desactivarAlturaBifocal();'>";
        if (!isset($wiad)) //No esta seteada
         echo "<option>";
        else               //Ya esta seteada
         if  ($wiad == "")
          echo "<option selected >";
         else
          echo "<option>";

        $i=0.75;
        while ($i <= 3.50 )
        {
          $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);
          //Para que no imprima el cero a la izquierda
           if (substr($k,0,1) == 0)
             $k = " ".substr($k,1,4);

          if (!isset($wiad)) //No esta seteada
           echo "<option value=".$k.">".$k;
          else
           if ($wiad == $k)
            echo "<option selected value=".$k.">".$k;
           else
            echo "<option value=".$k.">".$k;
          $i = $i + 0.25;
        }
        echo "</select></td>";


         //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
         //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
         echo "<td bgcolor=#cccccc align=center colspan=1 style='display:none'><select name='wite'>";
         if (!isset($wite)) //No esta seteada
         {
          echo "<option>";
          echo "<option value='1'>Terminado";
          echo "<option value='2'>Tallado";
         }
         else              //Ya esta seteada
         {
         if ($wite == "")
          echo "<option selected>";
         else
          echo "<option>";

         if ($wite == "1")
          echo "<option selected value='1'>Terminado";
         else
          echo "<option value='1'>Terminado";

         if ($wite == "2")
          echo "<option selected value='2'>Tallado";
         else
          echo "<option value='2'>Tallado";
         }
         echo "</select></td>";

/*
       //Como una AYUDA ADICIONAL lleno por defecto al entrar una orden nueva los campos 'Cod lente' con los facturados en la ultima factura

        if ( ($wproceso == "Nuevo") and (isset($wdoc)) and ( $wled == "") )
        {
         //Busco la ultima factura de este documento
         $query = "Select fenval,fennpa,fenfac From uvglobal_000018 Where Fendpa = '".$wdoc."' Order by Fenfac Desc";
         $resultado = mysql_query($query);
         $nroreg = mysql_num_rows($resultado);
         if ($nroreg > 0)   //Encontro
         {
          $registro = mysql_fetch_row($resultado);
          // busco el codigo de los lentes vendidos en la ultima factura de este documento (grupo de Lentes)
          $query = "SELECT
                    Vdenum, Vdeart, Vdecan
                FROM
                    uvglobal_000016, uvglobal_000017, uvglobal_000001
                WHERE
                    Vennfa = '".$registro[2]."'
                    And Vdenum = Vennum And Vdeart = Artcod
                    And (SUBSTR(Artgru,1,2) = 'LO' or SUBSTR(Artgru,1,2) = 'LC' or  SUBSTR(Artgru,1,2) = 'LE'); ";

           $resultado = mysql_query($query, $conex);
           $nroreg = mysql_num_rows($resultado);
           if ($nroreg > 0)   //Encontro
           {
            $registro = mysql_fetch_row($resultado);
            //Si cantidad = 2 coloco el codigo en el campo Lente Ojo Izq y el campo Ojo Der
            if ( $registro[2] == 2 )
            {
             $wled = $registro[1];
             $wlei = $registro[1];
            }
           }
          }
         }
*/

        /** Valída disponibilidad en la factura - 2012-05-08
        *   En una orden de laboratorio, al momento de escribir un mismo código en ambos campos de lente
        *   Se verifica que la cantidad de lentes facturados con el mismo código sea mayor a 1 y de esta manera
        *   se permita escribir el mismo código en los dos campos.
        */
        $cantidad_lts = 0;
        if (isset($wlei) && isset($wled) && $wlei != "" && $wled != "" && $wlei == $wled)
        {
            $query="SELECT  Vdecan
                    FROM    uvglobal_000016, uvglobal_000017, uvglobal_000001
                    WHERE   Venffa = '".$wffa."' AND Vennfa = '".$wfac."' AND Vdenum = Vennum AND Vdeart = Artcod
                            AND Artcod = '".$wled."' AND (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE');";
            $r = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
            //echo "--------<br>".$query;
            $row_c = mysql_fetch_array($r);
            $cantidad_lts = $row_c['Vdecan'];
        }
        // echo $cantidad_lts.' <hr>';

        $impr_otro_lente = true;
        echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Cod. lente OD:</font></b>";
        // if (isset($wled) and $wled != "" && isset($wdoc) && isset($wffa) &&  isset($wfac) )
        if (isset($wled) && $wled != "" && isset($wdoc) && isset($wven))
        {
            // Valída disponibilidad en la factura - 2012-05-08
            if ($cantidad_lts > 0)
            {
                $cantidad_lts--;
                if ($cantidad_lts == 0)
                { $impr_otro_lente = false; }
            }
            //echo "<INPUT TYPE='text' NAME='wled' size=10 VALUE='".$wled."' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
            echo "<select NAME='wled' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();' ><option value=''>...seleccione</option>";
			$query="SELECT  Artnom,Vdeart
                    FROM    uvglobal_000016, uvglobal_000017, uvglobal_000001
                    WHERE   Vdenum = '".$wven."' And Vdenum = Vennum And Vdeart = Artcod
                    AND    (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE')";


			$resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
            while($nroreg = mysql_fetch_array ($resultado))
			{
				if($wled == $nroreg['Vdeart'])
				{
					echo "<option selected value='".$nroreg['Vdeart']."'>".$nroreg['Vdeart']." - ".$nroreg['Artnom']."</option>";
				}
				else
				{
					echo "<option value='".$nroreg['Vdeart']."'>".$nroreg['Vdeart']." - ".$nroreg['Artnom']."</option>";
				}
			}

			echo"</select></td>";

			$query="SELECT  Artnom,Vdeart
                    FROM    uvglobal_000016, uvglobal_000017, uvglobal_000001
                    WHERE   Vdenum = '".$wven."' And Vdenum = Vennum And Vdeart = Artcod
                             AND Artcod = '".$wled."' And (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE');";
            $resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
            $nroreg = mysql_num_rows($resultado);
            if ($nroreg > 0)   //Encontro
            {
                $der = 1;	//Indica si se encontro un lente

                $registro = mysql_fetch_row($resultado);
                $wled = $registro[1];
                echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#006699 size=2> ".$registro[0]."</font></b></td>";
            }
            else
            {
                $der = 0;	//Inidca que no se econtro lente
                echo "<td align=center bgcolor=#DDDDDD colspan=4>";
                echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR NO EXISTE CODIGO DEL LENTE EN ESTA FACTURA !!!!</MARQUEE></font>";
                echo "</b></td>";
            }
        }
        else
        {
            //echo "<INPUT TYPE='text' NAME='wled' size=10 onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
            echo "<select NAME='wled' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();' ><option value=''>...seleccione</option>";
			$query="SELECT  Artnom,Vdeart
                    FROM    uvglobal_000016, uvglobal_000017, uvglobal_000001
                    WHERE   Vdenum = '".$wven."' And Vdenum = Vennum And Vdeart = Artcod
                    AND    (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE')";


			$resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
            while($nroreg = mysql_fetch_array ($resultado))
			{
				if($wled == $nroreg['Vdeart'])
				{
					echo "<option selected value='".$nroreg['Vdeart']."'>".$nroreg['Vdeart']." - ".$nroreg['Artnom']."</option>";
				}
				else
				{
					echo "<option value='".$nroreg['Vdeart']."'>".$nroreg['Vdeart']." - ".$nroreg['Artnom']."</option>";
				}
			}

			echo"</select></td>";
			echo "<td align=center bgcolor=#DDDDDD colspan=4>";
        }

        echo "<INPUT type='hidden' name='der' value='$der'>";

        echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Cod. lente OI:</font></b>";
        // if (isset($wlei) and $wlei <> "" and isset($wffa) and isset($wfac) )
        if (isset($wlei) && $wlei != "" && isset($wven) && $impr_otro_lente==true) // 2012-05-08
        {
           // echo "<INPUT TYPE='text' NAME='wlei' size=10 VALUE='".$wlei."' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
            echo "<select NAME='wlei' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();' ><option value=''>...seleccione</option>";
			$query="SELECT  Artnom,Vdeart
                    FROM    uvglobal_000016, uvglobal_000017, uvglobal_000001
                    WHERE   Vdenum = '".$wven."' And Vdenum = Vennum And Vdeart = Artcod
                    AND    (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE')";


			$resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
            while($nroreg = mysql_fetch_array ($resultado))
			{
				if($wlei == $nroreg['Vdeart'])
				{
					echo "<option selected value='".$nroreg['Vdeart']."'>".$nroreg['Vdeart']." - ".$nroreg['Artnom']."</option>";
				}
				else
				{
					echo "<option value='".$nroreg['Vdeart']."'>".$nroreg['Vdeart']." - ".$nroreg['Artnom']."</option>";
				}
			}

			echo"</select></td>";

			$query="SELECT  Artnom,Vdeart
                    FROM    uvglobal_000016, uvglobal_000017, uvglobal_000001
                    WHERE   Vdenum = '".$wven."' And Vdenum = Vennum And Vdeart = Artcod
                            AND Artcod = '".$wlei."' And ( mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE' );";
            $resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
            $nroreg = mysql_num_rows($resultado);
            if ($nroreg > 0)   //Encontro
            {
                $izq = 1;
                $registro = mysql_fetch_row($resultado);
                echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#006699 size=2> ".$registro[0]."</font></b></td>";
            }
            else
            {
                $izq = 0;
                echo "<td align=center bgcolor=#DDDDDD colspan=4>";
                echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR NO EXISTE CODIGO DEL LENTE EN ESTA FACTURA !!!!</MARQUEE></font>";
                echo "</b></td>";
            }
        }
        else
        {
            if (isset($wlei) && $wlei != '') { $wlei = ''; } // Valída disponibilidad en la factura - 2012-05-08
            //echo "<INPUT TYPE='text' NAME='wlei' size=10 onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
             echo "<select NAME='wlei' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();' ><option value=''>...seleccione</option>";
			 $query="SELECT  Artnom,Vdeart
                    FROM    uvglobal_000016, uvglobal_000017, uvglobal_000001
                    WHERE   Vdenum = '".$wven."' And Vdenum = Vennum And Vdeart = Artcod
                    AND    (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE')";


			$resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
            while($nroreg = mysql_fetch_array ($resultado))
			{
				if($wlei == $nroreg['Vdeart'])
				{
					echo "<option selected value='".$nroreg['Vdeart']."'>".$nroreg['Vdeart']." - ".$nroreg['Artnom']."</option>";
				}
				else
				{
					echo "<option value='".$nroreg['Vdeart']."'>".$nroreg['Vdeart']." - ".$nroreg['Artnom']."</option>";
				}
			}

			echo"</select></td>";
			echo "<td align=center bgcolor=#DDDDDD colspan=4>";
        }

        echo "<INPUT type='hidden' name='izq' value='$izq'>";

        // if( $wproceso != "Nuevo" && $wproceso != 'Consultar' && (!empty( $wled ) ) ){
        if( $wproceso != "Nuevo" && (!empty( $wled ) ) && empty( $winv ) ){
            $winv = consultarCodigoInvima( $wled );
        }
        // if( $wproceso != "Nuevo" && $wproceso != 'Consultar' && (!empty( $wled ) ) ){
        if( $wproceso != "Nuevo" && (!empty( $wled ) ) ){
            echo "<tr bgcolor=#DDDDDD>";
        }
        else{
            echo "<tr bgcolor=#DDDDDD style='display:none'>";
        }
        echo "<td align='center' colspan='2'><b><i>Lente derecho: </i></b></td>";
        echo "<td align='center' colspan='2'><b><font text color=#003366 size=2> <i>C&oacute;digo invima:&nbsp;</font></b><INPUT type='text' id='winv' name='winv' value='".@$winv."'></td>";
        echo "<td align='center' colspan='2'><b><font text color=#003366 size=2> <i>Lote:&nbsp;</font></b><INPUT type='text' id='wlot' name='wlot'  value='".@$wlot."'></td>";
        echo "</tr>";

        // if( $wproceso != "Nuevo" && $wproceso != 'Consultar' && ( !empty( $wlei ) ) ){
        if( $wproceso != "Nuevo" && ( !empty( $wlei ) ) && empty( $wini ) ){
            $wini = consultarCodigoInvima( $wlei );
        }
        // if( $wproceso != "Nuevo" && $wproceso != 'Consultar' && ( !empty( $wlei ) ) ){
        if( $wproceso != "Nuevo" && ( !empty( $wlei ) ) ){
            echo "<tr bgcolor=#DDDDDD>";
        }
        else{
            echo "<tr bgcolor=#DDDDDD style='display:none'>";
        }
        echo "<td align='center' colspan='2'><b><i>Lente izquierdo: </i></b></td>";
        echo "<td align='center' colspan='2'><b><font text color=#003366 size=2> <i>C&oacute;digo invima:&nbsp;</font></b><INPUT type='text' id='wini' name='wini' value='".@$wini."'></td>";
        echo "<td align='center' colspan='2'><b><font text color=#003366 size=2> <i>Lote:&nbsp;</font></b><INPUT type='text' id='wloi' name='wloi'  value='".@$wloi."'></td>";
        echo "</tr>";

    //Defino un ComboBox con los vendedores
    echo "<tr><td align=CENTER colspan=6 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Vendedor del lente: </font></b>";

    if ($wedita=='disabled')
    {
        $query = "SELECT ordvel FROM uvglobal_000133 WHERE ordnro =$wnro ";
        $resultado = mysql_query($query);          // Ejecuto el query
        $nroreg = mysql_num_rows($resultado);


        echo "<select name='wvel' ".$wreadonly.">";  //se agrega $wedita para saber si deja modificar o no ese campo con la propiedad disable. tavo 20081119.

        if( $wproceso != "Modificar" )
            echo "<option></option>";                  //primera opcion en blanco

        $Num_Filas = 0;
        while ( $Num_Filas < $nroreg )
        {
            $registro = mysql_fetch_row($resultado);
            echo "<option selected>".$registro[0]."</option>";
            $Num_Filas++;
        }
        echo "</select></td></tr>";
    }
    else
    {
        // Modificado para excluir los usuarios inactivos - 2012-05-02
        $query = "
                SELECT  Cjeusu,descripcion
                FROM    uvglobal_000030,usuarios
                WHERE   Cjeusu = codigo
                        AND Activo <> 'I'
                Order BY Cjeusu";
        $resultado = mysql_query($query);          // Ejecuto el query
        $nroreg = mysql_num_rows($resultado);

        echo "<select name='wvel' ".$wedita." >";  //se agrega $wedita para saber si deja modificar o no ese campo con la propiedad disable. tavo 20081119.

        if( $wproceso != "Modificar" )
        echo "<option></option>";                  //primera opcion en blanco

        $Num_Filas = 0;
        while ( $Num_Filas < $nroreg )
        {
            $registro = mysql_fetch_row($resultado);
            if(substr($wvel,0,strpos($wvel,"-")) == $registro[0])
              echo "<option selected>".$registro[0]."- ".$registro[1]."</option>";
            else
              echo "<option>".$registro[0]."- ".$registro[1]."</option>";
            $Num_Filas++;
        }
        echo "</select></td></tr>";
     }

        echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>D.P.</font></b><br>";
        if (isset($wedp))
         echo "<INPUT TYPE='text' NAME='wedp' size=10 VALUE='".$wedp."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wedp' size=10></INPUT></td>";


        echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#003366 size=2> <i>Tratamiento:</font></b><br>";
        if (isset($wtra))
         echo "<INPUT TYPE='text' NAME='wtra' size=60 VALUE='".$wtra."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wtra' size=60></INPUT></td>";

         //  *** ALTURA BIFOCAL  ***
        echo "<td bgcolor=#cccccc align=center colspan=1><b><font text color=#003366 size=2> <i>Altura Bifocal en mm.</font></b><br><select name='wbif'>";
        if (!isset($wbif)) //No esta seteada
        {  echo "<option>"; }
        else               //Ya esta seteada
        {
            if  ($wbif == "")
            { echo "<option selected >"; }
            else
            { echo "<option>"; }
        }

        if( isset($wbif) && $wbif == 'No tiene' ){
            echo "<option selected value='No tiene'>No tiene</option>";
        }
        else{
            echo "<option value='No tiene'>No tiene</option>";
        }

        if( isset($wbif) && $wbif == 'La misma' ){
            echo "<option selected value='La misma'>La misma</option>";
        }
        else{
            echo "<option value='La misma'>La misma</option>";
        }

        // Se adiciona este caso porque ántes no se tenía contemplado y al momento de editar, esta opción aparecía en blanco y en imprimir aparecía 'NO APLICA'
        if( isset($wbif) && $wbif == 'NO APLICA' ){
            echo "<option selected value='NO APLICA'>NO APLICA</option>";
        }
        else{
            echo "<option value='NO APLICA'>NO APLICA</option>";
        }

        $i=10;
        while ($i <= 25.00 )
        {
            if (!isset($wbif)) //No esta seteada
            {   // al value se le estaba anteponiendo el signo menos '-', se elimina este signo
                // puesto que generaba problemas al momento de editar (no aparecía seleccionado bifocal) - 2012-05-02
                echo "<option value=".$i.">".$i;
            }
            else
            {
                if ($wbif == $i)
                    echo "<option selected value=".$i.">".$i;
                else
                    echo "<option value=".$i.">".$i;
            }
            $i = $i + 1;
        }
        echo "</select></td>";

        echo "<tr><td colspan=6 bgcolor=#cccccc align=center>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor dos) or si no esta seteada
        if ((isset($wmon) and $wmon == "2") or !isset($wmon))     //Son tres posibles selcciones,Empiezo por 2 porque quiero que esta quede
        {                                                         //predefinida cuando entre un registro nuevo
         echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' onClick='javascript: camposMontura(1);'>Montura Propia<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' CHECKED onClick='javascript: camposMontura(2);'>Montura U.V.G.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '3' onClick='javascript: camposMontura(3);'>Solo Lentes.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '4' onClick='javascript: camposMontura(4);'>Lentes de Contacto.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
        }
        else{
         if ((isset($wmon) and $wmon == "1"))
         {
          echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' CHECKED onClick='javascript: camposMontura(1);'>Montura Propia<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
          echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' onClick='javascript: camposMontura(2);'>Montura U.V.G.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
          echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '3' onClick='javascript: camposMontura(3);'>Solo Lentes.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
          echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '4' onClick='javascript: camposMontura(4);'>Lentes de Contacto.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
         }
         else if( (isset($wmon) and $wmon == "3") )
         {
          echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' onClick='javascript: camposMontura(1);'>Montura Propia<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
          echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' onClick='javascript: camposMontura(2);'>Montura U.V.G.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
          echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '3' CHECKED onClick='javascript: camposMontura(3);'>Solo Lentes.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
          echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '4' onClick='javascript: camposMontura(4);'>Lentes de Contacto.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
         }
         else{
            echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' onClick='javascript: camposMontura(1);'>Montura Propia<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' onClick='javascript: camposMontura(2);'>Montura U.V.G.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '3' onClick='javascript: camposMontura(3);'>Solo Lentes.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '4' CHECKED onClick='javascript: camposMontura(4);'>Lentes de Contacto.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
         }
        }

        echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Cod. Montura:</font></b>";
        if (isset($wref) and $wref <> ""  and isset($wven) )
        {
          //echo "<INPUT TYPE='text' NAME='wref' size=10 VALUE='".$wref."' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
		  echo "<select NAME='wref'  onkeyup='this.value=this.value.toUpperCase();' onchange='enter();' ><option>...Seleccione</option>";

			$query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001
					 WHERE Vdenum='".$wven."' And Vdenum = Vennum And Vdeart = Artcod
					   AND mid(Artgru,1,2) = 'MT'";
			$resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");

			 while ($registro = mysql_fetch_array($resultado))
			 {
				if(trim($wref) == trim($registro['Vdeart']))
				{
					echo"<option selected value='".$registro['Vdeart']."'>".$registro['Vdeart']." - ".$registro['Artnom']."</option>";

				}else
				{
					echo"<option value='".$registro['Vdeart']."'>".$registro['Vdeart']." - ".$registro['Artnom']."</option>";
				}
			 }

			echo "</select></td>";


          $query="	SELECT 	Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001
					WHERE 	Vdenum = '".$wven."'  And Vdenum = Vennum And Vdeart = Artcod
					AND		Artcod = '".$wref."'  And mid(Artgru,1,2) = 'MT'";

          $resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
          $nroreg = mysql_num_rows($resultado);

          if ($nroreg > 0)   //Encontro
          {
           $registro = mysql_fetch_row($resultado);
           echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#006699 size=2> ".$registro[0]."</font></b></td>";
          }
          else
          {
           echo "<td align=center bgcolor=#DDDDDD colspan=4>";
           echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR NO EXISTE CODIGO DE MONTURA EN ESTA FACTURA !!!!</MARQUEE></font>";
           echo "</b></td>";
          }
        }
        else
        {
          //echo "<INPUT TYPE='text' NAME='wref' size=10 onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
          echo "<select NAME='wref'  onkeyup='this.value=this.value.toUpperCase();' onchange='enter();' ><option>...Seleccione</option>";

			$query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001
					 WHERE Vdenum='".$wven."' And Vdenum = Vennum And Vdeart = Artcod
					   AND mid(Artgru,1,2) = 'MT'";
			$resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");

			while ($registro = mysql_fetch_array($resultado))
			{
				if($wref == $registro['Vdeart'])
				{
					echo"<option selected value='".$registro['Vdeart']."'>".$registro['Vdeart']." - ".$registro['Artnom']."</option>";

				}else
				{
					echo"<option value='".$registro['Vdeart']."'>".$registro['Vdeart']." - ".$registro['Artnom']."</option>";
				}
			}

			echo "</select></td>";

		  echo "<td align=center bgcolor=#DDDDDD colspan=4>";
        }

    //Defino un ComboBox con los vendedores
    echo "<tr><td align=CENTER colspan=6 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Vendedor de la Montura: </font></b>";

    if ($wedita=='disabled')
    {
        $query = "SELECT ordvem FROM uvglobal_000133 WHERE ordnro = $wnro ";
        $resultado = mysql_query($query);          // Ejecuto el query
        $nroreg = mysql_num_rows($resultado);

        echo "<select name='wvem' ".$wreadonly." >";  //$wedita para saber si deja o no modificar el campo de vendedores disable ver if wproceso,

        if( $wproceso != "Modificar" )
            echo "<option></option>";                  //primera opcion en blanco

        $Num_Filas = 0;
        while ( $Num_Filas < $nroreg )
        {
            $registro = mysql_fetch_row($resultado);
            echo "<option selected>".$registro[0]."</option>";
            $Num_Filas++;
        }
        echo "</select></td></tr>";

    }
    else
    {
        // Modificado para excluir los usuarios inactivos - 2012-05-02
        $query = "
                SELECT  Cjeusu,descripcion
                FROM    uvglobal_000030,usuarios
                WHERE   Cjeusu = codigo
                        AND Activo <> 'I'
                Order BY Cjeusu";
        $resultado = mysql_query($query);          // Ejecuto el query
        $nroreg = mysql_num_rows($resultado);

        echo "<select name='wvem' ".$wedita." >";  //$wedita para saber si deja o no modificar el campo de vendedores disable ver if wproceso,

        echo "<option></option>";                  //primera opcion en blanco

        $Num_Filas = 0;
        while ( $Num_Filas < $nroreg )
        {
            $registro = mysql_fetch_row($resultado);
            if(substr($wvem,0,strpos($wvem,"-")) == $registro[0])
              echo "<option selected>".$registro[0]."- ".$registro[1]."</option>";
            else
              echo "<option>".$registro[0]."- ".$registro[1]."</option>";
            $Num_Filas++;
        }
        echo "</select></td></tr>";
    }

        echo "<tr><td bgcolor=#cccccc align=center colspan=2><b><font text color=#003366 size=2> <i>Material: </font></b><select name='wmet'>";
        if (!isset($wmet))   //No esta seteada
        {
         echo "<option>";
         echo "<option value='1'>Metal";
         echo "<option value='2'>Pasta";
         echo "<option value='3'>Otro";
        }
        else
        {
         if ($wmet == "")
           echo "<option selected>";
         else
            echo "<option>";
         if ($wmet == "1")
           echo "<option selected value='1'>Metal";
         else
            echo "<option value='1'>Metal";
         if ($wmet == "2")
           echo "<option selected value='2'>Pasta";
         else
           echo "<option value='2'>Pasta";
         if ($wmet == "3")
           echo "<option selected value='3'>Otro";
         else
           echo "<option value='3'>Otro";
        }
        echo "</select></td>";

        echo "<td bgcolor=#cccccc align=center colspan=2><b><font text color=#003366 size=2> <i>Diseño: </font></b><select name='wcom'>";
        if (!isset($wcom))   //No esta seteada
        {
         echo "<option>";
         echo "<option value='1'>Completa";
         echo "<option value='2'>Sem AA";
         echo "<option value='3'>AA";
        }
        else
        {
         if ($wcom == "")
           echo "<option selected>";
         else
            echo "<option>";
         if ($wcom == "1")
          echo "<option selected value='1'>Completa";
         else
          echo "<option value='1'>Completa";
         if ($wcom == "2")
          echo "<option selected value='2'>Sem AA";
         else
           echo "<option value='2'>Sem AA";
         if ($wcom == "3")
          echo "<option selected value='3'>AA";
         else
           echo "<option value='3'>AA";
        }
        echo "</select></td>";

        echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Color :</font></b>";
        if (isset($wcol))
         echo "<INPUT TYPE='text' NAME='wcol' size=20 VALUE='".$wcol."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wcol' size=20></INPUT></td>";

        echo "<tr>";
        echo "<td colspan=1 align=center><b>ESTADO<b></td>";
        echo "<td colspan=1 align=center><b>BUENO<b></td>";
        echo "<td colspan=1 align=center><b>MALO<b></td>";
        echo "<td colspan=3 align=center><b>DESCRIPCION<b></td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Pintura:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if( !isset($wpin) ){
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 1></INPUT>";
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 2></INPUT>";
        }
        else if ((isset($wpin) and $wpin == "1"))
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 1 CHECKED></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 2></INPUT>";
        }
        else
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 1></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 2 CHECKED></INPUT>";
        }

        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde1))
         echo "<INPUT TYPE='text' NAME='wde1' size=40 VALUE='".$wde1."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wde1' size=40></INPUT></td>";

        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Brazos:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if(  !isset($wbra) ){
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 1></INPUT>";
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 2></INPUT>";
        }
        else if ((isset($wbra) and $wbra == "1"))
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 1 CHECKED></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 2></INPUT>";
        }
        else
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 1 ></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 2 CHECKED></INPUT>";
        }

        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde2))
         echo "<INPUT TYPE='text' NAME='wde2' size=40 VALUE='".$wde2."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wde2' size=40></INPUT></td>";

        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Terminales:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if( !isset($wter) ){
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 1></INPUT>";
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 2></INPUT>";
        }
        else if ((isset($wter) and $wter == "1"))
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 1 CHECKED></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 2></INPUT>";
        }
        else
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 1 ></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 2 CHECKED></INPUT>";
        }

        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde3))
         echo "<INPUT TYPE='text' NAME='wde3' size=40 VALUE='".$wde3."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wde3' size=40></INPUT></td>";


        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Plaquetas:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if( !isset($wpla) ){
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 1></INPUT>";
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 2></INPUT>";
        }
        else if ((isset($wpla) and $wpla == "1") )
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 1 CHECKED></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 2></INPUT>";
        }
        else
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 1 ></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 2 CHECKED></INPUT>";
        }

        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde4))
         echo "<INPUT TYPE='text' NAME='wde4' size=40 VALUE='".$wde4."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wde4' size=40></INPUT></td>";

        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Otro:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if( !isset($wotr) ){
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 1></INPUT>";
            echo "<td colspan=1 bgcolor=#cccccc align=center>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 2></INPUT>";
        }
        else if ((isset($wotr) and $wotr == "1") )
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 1 CHECKED></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 2></INPUT>";
        }
        else
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 1 ></INPUT>";
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
         echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 2 CHECKED></INPUT>";
        }

        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde5))
         echo "<INPUT TYPE='text' NAME='wde5' size=40 VALUE='".$wde5."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wde5' size=40></INPUT></td>";

        echo "<tr><td align=center bgcolor=#DDDDDD colspan=5><b><font text color=#003366 size=2> <i>Observaciones:</font></b><br>";
        if (isset($wobs))
         echo "<INPUT TYPE='text' NAME='wobs' size=100 VALUE='".$wobs."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wobs' size=100></INPUT></td>";

        echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>Caja Nro:</font></b><br>";
        if (isset($wcaj))
         echo "<INPUT TYPE='text' NAME='wcaj' size=10 VALUE='".$wcaj."'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wcaj' size=10></INPUT></td>";

        //echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fte-Factura: </font></b>";
       // echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Numero de Venta</font></b><br>";
        // si existe factura




		 //echo "<INPUT TYPE='text' readonly NAME='wven' id='wven' size=10 color=#003366 VALUE='".$wven."'></INPUT></td><td align=center bgcolor=#DDDDDD colspan=4></td></tr>";


        echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fte-Factura: </font></b>";
        if($wven!='')
		{
			$query_factura= "SELECT Venffa, Vennfa ,Venvto
							   FROM uvglobal_000016
							  WHERE Vennum = '".$wven."' ";

			$res_factura = mysql_query($query_factura);
			if($row_factura = mysql_fetch_array($res_factura))
			{
				$wvennfa = $row_factura['Venffa'];
				$wvenfac = $row_factura['Vennfa'];
				$wvalorfac = $row_factura['Venvto'];
			}

			echo "<INPUT TYPE='text' NAME='wffa' size=5  VALUE='".$wvennfa."' readonly></INPUT>";
			echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$wvenfac."' readonly></INPUT></td>";
			echo "<td align=center bgcolor=#DDDDDD colspan=4 rowspan='1'><b><font text color=990000 size=2> VALOR: ".$wvalorfac."<b>&nbsp;&nbsp;&nbsp;</b></font></b></td>";


		}
		else
		{

			if (isset($wfac) )
			{
			   echo "<INPUT TYPE='text' NAME='wffa' size=5  VALUE='".$wffa."' readonly></INPUT>";

			   if( $wproceso != "Nuevo" ){
					echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$wfac."' readonly></INPUT></td>";
			   }
			   else{
					echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$wfac."'></INPUT></td>";
			   }

			   //Segun la sede tomo el prefijo
			   //$c=explode('-',$sede);
			   //$query = "Select Ccopve From uvglobal_000003 Where Ccocod ='".$c[0]."'";
			   //$resultado = mysql_query($query);
			   //$registro = mysql_fetch_row($resultado);
			   //$prefijo = $registro[0];

			   if( !tieneNCFacturacion( $wfac, $sede ) ){
				   $query = "Select fenval,fennpa From uvglobal_000018 Where Fendpa = '".$wdoc."' And fenfac ='".$wfac."' And fenffa ='".$wffa."'";
				   $resultado = mysql_query($query);
				   $nroreg = mysql_num_rows($resultado);
				   if ($nroreg > 0)   //Encontro
				   {
					$registro = mysql_fetch_row($resultado);
					echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=990000 size=2> VALOR: ".$registro[0]."<b>&nbsp;&nbsp;&nbsp;</b> USUARIO: ".$registro[1]."</font></b></td>";
				   }
				   else
				   {

					echo "<td align=center bgcolor=#DDDDDD colspan=4>";
					echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>NO EXISTE NRO DE FACTURA PARA ESTE DOCUMENTO !!!!</MARQUEE></font>";
					echo "</b></td>";
				   }
			   }
			   else{
					echo "<td align=center bgcolor=#DDDDDD colspan=4>";
					echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>LA FACTURA TIENE NOTA CREDITO!!!!</MARQUEE></font>";
					echo "</b></td>";
			   }

			}
			else
			{
			 if (isset($wdoc) )
			 {
			   //Busco la ultima factura de este documento
			   $query = "SELECT
							fenval,fennpa,fenfac,fenffa
						 FROM
							uvglobal_000018
						 WHERE
							Fendpa = '".$wdoc."'
						 ORDER by Fenfac Desc";
			 //echo " entró acá <br>".$query;
			   $resultado = mysql_query($query);
			   $nroreg = mysql_num_rows($resultado);
			   if ($nroreg > 0)   //Encontro
			   {

				$registro = mysql_fetch_row($resultado);

				if( !tieneNCFacturacion( $registro[2], $sede ) ){

				   if( ordenLaboratorio( $registro[2] ) ){

						echo "<INPUT TYPE='text' NAME='wffa' size=5 VALUE='".$registro[3]."' readonly></INPUT>";

						if( $wproceso != "Nuevo" ){
							echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$registro[2]."' readonly></INPUT></td>";
						}
						else{
							echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$registro[2]."'></INPUT></td>";
						}

						echo "<td align=center bgcolor=#DDDDDD colspan=4>";
						echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>YA EXISTE UNA ORDEN PARA ESTA FACTURA!!!!</MARQUEE></font>";
						echo "</b></td>";
					}
					else{
						echo "<INPUT TYPE='text' NAME='wffa' size=5 VALUE='".$registro[3]."' readonly></INPUT>";

						if( $wproceso != "Nuevo" ){
							echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$registro[2]."' readonly></INPUT></td>";
						}
						else{
							echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$registro[2]."'></INPUT></td>";
						}
					}
				}
				else{
					echo "<INPUT TYPE='text' NAME='wffa' size=5 VALUE='".$registro[3]."' readonly></INPUT>";

					if( $wproceso != "Nuevo" ){
						echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$registro[2]."' readonly></INPUT></td>";
					}
					else{
						echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$registro[2]."'></INPUT></td>";
					}

					echo "<td align=center bgcolor=#DDDDDD colspan=4>";
					echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>LA FACTURA TIENE NOTA CREDITO!!!!</MARQUEE></font>";
					echo "</b></td>";
				}

			   }
			   else
			   {
				echo "<td align=center bgcolor=#DDDDDD colspan=4>";
				echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>NO EXISTE NRO DE FACTURA PARA ESTE DOCUMENTO!!!!</MARQUEE></font>";
				echo "</b></td>";
			   }

			 }
			}
		}
        echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fecha de la orden:</font></b><br>";
        if (isset($wfec))
         echo "<INPUT TYPE='text' NAME='wfec' id='wfec' size=10 color=#003366 VALUE='".$wfec."'></INPUT></td>";
        else
        {
         $wfecha = date("Y-m-d");
         echo "<INPUT TYPE='text' NAME='wfec' id='wfec' size=10 color=#003366 VALUE='".$wfecha."'></INPUT></td>";
        }

        echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fecha de recepcion:</font></b><br>";
        if (isset($wfre))
         echo "<INPUT TYPE='text' NAME='wfre' id='wfre' size=10 VALUE='".$wfre."' readonly onchange=' actualizarFecha(this, \"wfre\");'></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wfre' id='wfre' size=10 readonly onchange='actualizarFecha(this, \"wfre\");'></INPUT></td>";

        echo '<script>
                $("#wfre").datepicker({
                 showOn: "button",
                 buttonImage: "../../images/medical/root/calendar.gif",
                 buttonImageOnly: true,
                 maxDate:"'.date("Y-m-d").'"
                });
             </script>';

        $ro     = "";
        $evento = "";
        if( !esAdministrador($key) ){
            $ro = "readonly";
        }else{
            $evento = " onchange='actualizarFecha(this, \"wfen\");' ";
        }

        echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fecha de entrega:</font></b><br>";
        if (isset($wfen))
         echo "<INPUT TYPE='text' NAME='wfen' id='wfen' size=10 VALUE='".$wfen."' readonly $evento ></INPUT></td>";
        else
         echo "<INPUT TYPE='text' NAME='wfen' id='wfen' size=10 readonly $evento></INPUT></td>";

        if( esAdministrador($key) ){
            echo '<script>
                    $("#wfen").datepicker({
                     showOn: "button",
                     buttonImage: "../../images/medical/root/calendar.gif",
                     buttonImageOnly: true,
                     maxDate:"'.date("Y-m-d").'"
                    });
                </script>';
        }


       // $wproceso es una variable escondida que enviaremos a travez del formulario
       if (isset($wproceso))
         echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>";
       else
         echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";


        echo "<input type='hidden' id='wcodigoUsuario' name='wcodigoUsuario' value='$key'/>";

    if( !esAdministrador($key) ){

        $creadorOrden = creadorOrdenLaboratorio( $wnro );

        if( false && $creadorOrden == $key && $wproceso == "Modificar" ){
            $es_modificable = true;
        }
        else{
            if( $wproceso != "Modificar" ){
                $es_modificable = true;
            }
            else{
                $es_modificable = false;
            }
        }
    }
    else{
        $es_modificable = true;
    }


    // if( isset($wfen) && $wfen != "0000-00-00" && !empty($wfen) ){
        // $es_modificable = false;
    // }

    if ($wproceso != "Consultar" && $es_modificable )  //PARA QUE NO MUESTRE EL BOTON <GRABAR> NI EL "Checkbox" SI ESTOY CONSULTANDO
    {
      // Boton grabar y variable 'wdat' en un checkbox para indicar que los datos ya estan completos para validar
        echo "<tr id='grabar'><td align=center colspan=6 bgcolor=#cccccc size=10>";
        echo "<input type='submit' onclick='iragrabar()' value='Grabar'>";

        if (isset($wdat))
            echo "<div id='divcheckbox' style='display:none'><INPUT TYPE = 'Checkbox' NAME='wdat' VALUE='".$wdat."'></INPUT></div></td>";
        else
            echo "<div id='divcheckbox' style='display:none'><INPUT TYPE='Checkbox' NAME='wdat' size=10></INPUT></div></td>";
    }


        echo "</center></table>";


if (isset($wnro) and isset($wfec) and isset($wdoc) and isset($wfac) and isset($wdat) )
///////////        CUANDO YA HAY DATOS DIGITADOS       ///////////////
{

    //tipo de usuario
    if( !isset($wtus) ){
        $wtus = '';
    }

    //valores ojo Derecho
    if( !isset($wdsi) || empty($wdsi) ){
        $wdsi = '';
    }

    if( !isset($wdes) || empty($wdes) ){
        $wdes = '';
    }

    if( !isset($wdej) ){
        $wdej = '';
    }

    if( !isset($wdci) || empty($wdci) ){
        $wdci = 0;
    }

    if( !isset($wdad) || empty($wdad) ){
        $wdad = '';
    }

    if( !isset($wdte) || empty($wdte) ){
        $wdte = '';
    }

    //valores ojo izquierdo
    if( !isset($wisi) || empty($wisi) ){
        $wisi = '';
    }

    if( !isset($wies) || empty($wies) ){
        $wies = '';
    }

    if( !isset($wiej) ){
        $wiej = '';
    }

    if( !isset($wici) || empty($wici) ){
        $wici = 0;
    }

    if( !isset($wiad) || empty($wiad) ){
        $wiad = '';
    }

    if( !isset($wite) || empty($wite) ){
        $wite = '';
    }

    if( !isset($wvel) ){
        $wvel = '';
    }

    //campos de la montura
    if( !isset( $wvem) ){
        $wvem = '';
    }

    if( !isset($wref) ){
        $wref = '';
    }

    if( !isset($wmet) ){
        $wmet = '';
    }

    if( !isset($wcom) ){
        $wcom = '';
    }

    if( !isset($wcol) ){
        $wcol = '';
    }

    if( !isset($wmon) ){
        $wmon = '';
    }

    //Campos restantes del formulario
    if( !isset($wran) ){
        $wran = '';
    }

    if( !isset($wedp) ){
        $wedp = '';
    }

    if( !isset($wtra) ){
        $wtra = '';
    }

    if( !isset($wbif) ){
        $wbif = '';
    }

    if( !isset($wcaj) ){
        $wcaj = '';
    }

    //construyendo Lente Derecho usando la clase Lentes
    $lenteDerecho = new clLentes();

    $lenteDerecho->codigo = $wled;
    $lenteDerecho->signoesfera = $wdsi;
    $lenteDerecho->esfera = $wdes;
    $lenteDerecho->eje = $wdej;
    $lenteDerecho->cilindro = $wdci;
    $lenteDerecho->add =  $wdad;
    // $lenteDerecho->tipo = $wdte;
    $lenteDerecho->vendedor = $wvel;

    //construyendo Lente Izquierdo usando la clase Lentes
    $lenteIzquierdo = new clLentes();

    $lenteIzquierdo->codigo = $wlei;
    $lenteIzquierdo->signoesfera = $wisi;
    $lenteIzquierdo->esfera = $wies;
    $lenteIzquierdo->eje = $wiej;
    $lenteIzquierdo->cilindro = $wici;
    $lenteIzquierdo->add =  $wiad;
    // $lenteIzquierdo->tipo = $wite;
    $lenteIzquierdo->vendedor = $wvel;

    $Montura = new clMonturas();

    $Montura->vendedor = $wvem;
    $Montura->codigo = $wref;
    $Montura->material = $wmet;
    $Montura->diseno = $wcom;
    $Montura->color = $wcol;
    $Montura->propietario = $wmon;
    @$Montura->estados->pintura['estado'] =  $wpin;
    @$Montura->estados->brazos['estado'] =  $wbra;
    @$Montura->estados->terminales['estado'] =  $wter;
    @$Montura->estados->plaquetas['estado'] =  $wpla;
    @$Montura->estados->otros['estado'] =  $wotr;
    @$Montura->estados->pintura['descripcion'] =  $wde1;
    @$Montura->estados->brazos['descripcion'] =  $wde2;
    @$Montura->estados->terminales['descripcion'] =  $wde3;
    @$Montura->estados->plaquetas['descripcion'] =  $wde4;
    @$Montura->estados->otros['descripcion'] =  $wde5;



    // invoco la funcion que valida los campos
    validar_datos($wfec,$wdoc,$wffa,$wfac,$wobs,$wfre,$wfen,$wled,$wlei,$wref, $lenteDerecho, $lenteIzquierdo, $Montura, $wran, $wedp, $wtra, $wbif, $wcaj, $wtus, $winv, $wlot, $wini, $wloi );

    if ($todok)
    {
     if ($wproceso == "Nuevo")
     {

        if( !tieneNCFacturacion( $wfac, $sede ) ){

            if( !ordenLaboratorio( $wfac ) ){

              //Por si otro usuario utilizo el consecutivo inicial antes de grabar actualizo el nro de consecutivo
              // (Aunque con el submit que siempre hace creo que no se necesita)
              $query = "Select carcon From uvglobal_000040 Where Carfue = 'OT' And Carest = 'on' And Carotr = 'on' ";
              $resultado = mysql_query($query);
              $nroreg = mysql_num_rows($resultado);
              $registro = mysql_fetch_row($resultado);
              $wnro = $registro[0];

              if( $wfen == '' or empty($wfen) ){
                $wfen = "0000-00-00";
              }

              if( $wfre == '' or empty($wfre) ){
                $wfre="0000-00-00";
              }

              $fecha = date("Y-m-d");
              $hora = (string)date("H:i:s");
              @$query1 = "INSERT INTO uvglobal_000133 (medico,fecha_data,hora_data,ordnro,orddoc,ordran,ordtus,orddsi,orddes,orddci,orddej,"
              ."orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,ordled,ordlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,"
              ."ordpin,ordde1,ordbra,ordde2,ordter,ordde3,ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordffa,ordfac,ordfec,ordfre,ordfen,ordvel,"
              ."ordvem,ordcco,seguridad) "
              ."VALUES ('uvglobal','".$fecha."','".$hora."',".$wnro.",'".$wdoc."','".$wran."','".$wtus."','".$wdsi."','".$wdes."',"
              ."'".$wdci."','".$wdej."','".$wdad."','".$wdte."','".$wisi."','".$wies."','".$wici."','".$wiej."','".$wiad."','".$wite."',"
              ."'".$wled."','".$wlei."','".$wedp."','".$wtra."','".$wbif."','".$wmon."','".$wref."','".$wmet."','".$wcom."','".$wcol."',"
              ."'".$wpin."','".$wde1."','".$wbra."','".$wde2."','".$wter."','".$wde3."','".$wpla."','".$wde4."','".$wotr."','".$wde5."',"
              ."'".$wobs."','".$wcaj."','".$wffa."','".$wfac."','".$wfec."','".$wfre."','".$wfen."','".$wvel."','".$wvem."','".$sede."','C-uvglobal')";
//				echo "...........<b>";
              $resultado = mysql_query($query1,$conex) or die("ERROR AL GRABAR CODIGO: ".mysql_errno().": ".mysql_error());  //ADICIONO
              if ($resultado)
              {
                   // echo "<center>";
                   // echo "Adicion Ok!<br>";
                   // echo "</center>";

                   //Actualizo el nro de orden
                   $var1 = 1 + (integer)($wnro);
                   $query = "Update uvglobal_000040 SET Carcon = ". $var1." Where Carfue = 'OT' And Carest = 'on' And Carotr = 'on'";
                   $resultado = mysql_query($query) or die("ERROR AL ACTUALIZAR NRO DE ORDEN CODIGO: ".mysql_errno().": ".mysql_error());
//			   	   echo "...........<b>";
                    ?><script>
                    var a = document.getElementById('grabar');

                    if( a ){
                        a.style.display = 'none';
                    }
                    </script>
                    <?php

                   //Para que regrese a un script especifico
                   echo "<script language='javascript'>";
                   echo "document.location.href = 'uvglobal00.php';";
                   echo "</script>";
              }
        }
        else{
            echo "<center><table border=1>";
            echo "<tr><td align=center colspan=100 bgcolor=#006699>";
            echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>YA HAY UNA ORDEN DE LABORATORIO PENDIENTE PARA ESTA FACTURA!!!!</MARQUEE></font>";
            echo "</td></tr></table></center>";
        }
      }
      else{
        echo "<center><table border=1>";
        echo "<tr><td align=center colspan=100 bgcolor=#006699>";
        echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>LA FACTURA TIENE NOTA CREDITO!!!!</MARQUEE></font>";
        echo "</td></tr></table></center>";
      }
     }
     else
     {
        if ( isset($wdat) )
        {
         //Modifico
         $query1 = "UPDATE uvglobal_000133 SET "
         ."orddoc='".$wdoc."',ordran='".$wran."',ordtus='".$wtus."',orddsi='".$wdsi."',orddes='".$wdes."',orddci='".$wdci."',orddej='".$wdej."',"
         ."orddad='".$wdad."',orddte='".$wdte."',ordisi='".$wisi."',ordies='".$wies."',ordici='".$wici."',ordiej='".$wiej."',ordiad='".$wiad."',"
         ."ordite='".$wite."',ordled='".$wled."',ordlei='".$wlei."',ordedp='".$wedp."',ordtra='".$wtra."',ordbif='".$wbif."',ordmon='".$wmon."',"
         ."ordref='".$wref."',ordmet='".$wmet."',ordcom='".$wcom."',ordcol='".$wcol."',ordpin='".@$wpin."',ordde1='".@$wde1."',ordbra='".@$wbra."',"
         ."ordde2='".@$wde2."',ordter='".@$wter."',ordde3='".@$wde3."',ordpla='".@$wpla."',ordde4='".@$wde4."',ordotr='".@$wotr."',ordde5='".@$wde5."',"
         ."ordobs='".$wobs."',ordcaj='".$wcaj."',ordffa='".$wffa."',ordfac='".$wfac."',ordfec='".$wfec."',ordfre='".$wfre."',ordfen='".$wfen."',"
         ."ordinv='".$winv."',ordlot='".$wlot."',ordini='".$wini."',ordloi='".$wloi."' WHERE ordnro = ".$wnro;

         $resultado = mysql_query($query1,$conex) or die("ERROR AL MODIFICAR CODIGO: ".mysql_errno().": ".mysql_error());  //MODIFICO

         echo "<center>";
         echo "Modificacion Ok!<br>";
         echo "</center>";

         ?><script>
            var a = document.getElementById('grabar');

            if( a ){
                a.style.display = 'none';
            }
            </script>
         <?php
        }
     }
    }

    else
    {
     //Para controlar que no muestre este mensaje por el submit que se hace la primera vez al digitar
     //la cedula o el documento por el <autoenter> Entonces coloco un campo adicional de "Datos completos"
     if ( isset($wdat) AND ($wproceso != "Consultar") )
     {
//     	$mensaje = "ERROR EN LOS DATOS DIGITADOS, DEBE TENER OBSERVACIONES!!!!";
//
//     	if( !empty($lenteDerecho->codigo ) )
//     		construirMensajes( $lenteDerecho );
//
//     	if( !empty($lenteIzquierdo->codigo ) )
//     		construirMensajes( $lenteIzquierdo );

      echo "<center><table border=1>";
      echo "<tr><td align=center colspan=100 bgcolor=#006699>";
      echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>$mensaje</MARQUEE></font>";
      echo "</td></tr></table></center>";
     }
    }

}    // De los datos digitados

}    // De la sesion

if ($wproceso == "Consultar")
{
 echo "<center>";
 echo "<li><A HREF='uvglobal03.php'>Regresar</A>";
 echo "</center>";
 echo "</form>";
}
 else
{
 echo "<center>";
 echo "<li><A HREF='uvglobal00.php'>Regresar</A>";
 echo "</center>";
 echo "</form>";
}

if( !isset($wmon) || empty($wmon) ){
    $valor = '2';
}
else{
    $valor = $wmon;
}
echo "<div id='msjAlerta' style='display:none;'>";
    echo '<br>';
    echo "<img src='../../images/medical/root/Advertencia.png'/>";
    echo "<br><br><div id='textoAlerta'></div><br><br>";
echo '</div>';
echo "<script type='text/javascript'>

    habilitarCamposLentes();
    camposMontura($valor);
    desactivarAlturaBifocal();

    for( i = 0; i < uvglobal01.wtus.length; i++ ){
        if( uvglobal01.wtus[i].checked ){
            desactivarRango( uvglobal01.wtus[i] );
        }
    }
</script>";

?>


</body>
</html>
