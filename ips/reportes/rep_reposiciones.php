<?php
include_once("conex.php");
if (!isset($consultaAjax))
{
?>
<head>
  <title>REPORTE REPOSICIONES</title>
</head>
<body>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<style type="text/css">
    .esError{
        border: 1px solid red;
        background-color: lightyellow;
    }

</style>
<script type="text/javascript">
//Configuracion para el calendario

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
       
        };
$.datepicker.setDefaults($.datepicker.regional['esp']);

function cerrarVentana()
	 {
      top.close()
     }

//Esta funcion controla el tipo de dato que se quiere consultar.
function mostrar_tipo(tipo)
{
    
    switch(tipo)
        {
        case 'hising':
            $('#tr_auxiliar').hide(500);   
			$('#hising').show(500);	
			$('#botonenviar').attr({onclick: 'mostrar_datos();'});	
			$("#datos_reporte").html("<center><div id='datos_reporte'></div></center>"); 
			$("#botonlimpiar").show();
            $("#botonenviar").show();			
        break;
        case 'auxiliar':
            $('#hising').hide(500); 
			$('#tr_auxiliar').show(500);	
			$('#botonenviar').attr({onclick: 'mostrar_datos();'});	
			$("#datos_reporte").html("<center><div id='datos_reporte'></div></center>");	
			$("#botonlimpiar").show();
            $("#botonenviar").show();
        break;
		case 'repuesto_aux':           
			$('#tr_auxiliar').show(500);
			$('#hising').hide(500); 			
			$('#botonenviar').attr({onclick: 'mostrar_repuesto();'});
			$("#datos_reporte").html("<center><div id='datos_reporte'></div></center>");
			$("#botonlimpiar").show();
            $("#botonenviar").show();
        break;
		
		case 'rep_hising':
            $('#tr_auxiliar').hide(500);   
			$('#hising').show(500);	
			$('#botonenviar').attr({onclick: 'mostrar_repuesto();'});	
			$("#datos_reporte").html("<center><div id='datos_reporte'></div></center>"); 
			$("#botonlimpiar").show();
            $("#botonenviar").show();			
        break;
		
        default:
          break;
        };  
        
}

function agregar_clase(elemento)
{

    var id_codigo_art = $(elemento).attr("id");
    var cod_art = id_codigo_art.split("-");
    if($(elemento).is(":checked"))
       {
           $('#cant_a_reponer-'+cod_art[1]).addClass("requerido");
       }
       else
           {
             $('#cant_a_reponer-'+cod_art[1]).removeClass("requerido");
             $('#cant_a_reponer-'+cod_art[1]).removeClass("esError");
           }
}

//Funcion que graba las reposiciones.
function grabar_reposiciones()
{

    var separador = '';
    var string_guardar = '';
    var esok = true;
    var wbasedatos = $("#wbasedatos").val();
    var wemp_pmla = $("#wemp_pmla").val();
	var cod_auxiliar =  $("#auxiliar").val();

    $(".requerido").each(function(){

        var cantidad = $(this).val();

        if(cantidad == '')
            {
                esok = false;
                $(this).addClass("esError");
            }
            else
                {
                  $(this).removeClass("esError");
                }

    });

    //Valida si el dato es correcto.
    if(esok == true)
        {
			//busca dentro de la tabla con id datos_reporte, todos lo checkbox y sus caracteristicas.
            $('table[id^=datos_reporte]').find('input:checkbox:checked').each(function(){

                var id_chk = $(this).attr("id");
                var cod_art = id_chk.split("_"); //Separo el id del checked por guion bajo.

                var cantidad_reponer = $('#cant_a_reponer-'+cod_art[1]).val(); 
				var codigo_articulo = cod_art[2];
				var historia = cod_art[3];
				var ingreso = cod_art[4];
				var fecha_venta = cod_art[5];
				
				//Construyo un arreglo con la informacion de los cajones seleccionados.
                string_guardar += separador+cantidad_reponer+"-*-"+cod_auxiliar+"-*-"+codigo_articulo+"-*-"+historia+"-*-"+ingreso+"-*-"+fecha_venta;
				
				//Separador que permite identicar la informacion de cada checkbox.
                separador = '*|*';

            });
      
		//Valida si hay un elemento seleccionado para reponer.
		if(string_guardar == '')	
			{
			alert('Favor seleccionar un elemento para reponer.');
			return;
			}
			
        $.post("rep_reposiciones.php",
                   {
                       consultaAjax:   	'grabar_reposiciones',
                       wemp_pmla:      	wemp_pmla,
                       wbasedato:       wbasedatos,                       
                       wdatos_guardar:  string_guardar                       

                   }
                   ,function(data_json) {

                       if (data_json.error == 1)
                       {
                          alert(data_json.mensaje);
                          return;
                       }
                       else
                       {
                           alert(data_json.mensaje);                       
                           $("#datos_reporte").html('');                           
                           $("#botonlimpiar").show();
                           $("#botonenviar").show();
                       }

               },
               "json"
           );
		   
        }
        else
            {
                alert('Los articulos marcados en rojo no tienen cantidad asociada.');
                return false;
            }          

    

}


function ocultar_tablas()
{

    $("#datos_reporte").html("<center><div id='datos_reporte'></div></center>");
    $("#botonenviar").show();
    $("#botonlimpiar").show(); //auxiliar    
	$("#tr_auxiliar").show();
	$("#hising").hide()
	$("#auxiliar").val('');
	$("#hising").val('');	
}

function validarcuantos(elemento)
{
    var cantidad_maxima = $("#"+elemento.id).attr("cant_maxima");
    var cantidad_escrita = $("#"+elemento.id).val();

    if(parseInt(cantidad_escrita) > parseInt(cantidad_maxima))
        {
            alert('La cantidad asignada para este articulo es mayor a la cantidad aplicada.');
            $("#"+elemento.id).val(cantidad_maxima)
        }


}


function isNumber(num) {
    return parseFloat(num).toString() == num
}

//Muestro lo que se ha repues por auxiliar, historia o ingreso.
function mostrar_repuesto()
 {
 
	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
	
    var wauxiliar = $("#auxiliar").val();	
    var wbasedatos = $("#wbasedatos").val();
    var wemp_pmla = $("#wemp_pmla").val();
    var wfecha_inicial = $("#wfecha_inicial").val();
    var wfecha_final = $("#wfecha_final").val();
	var whistoria = $("#historia").val();
	var wingreso = $("#ingreso").val();
	var tipo_consulta = $("input[name='tipo_consulta']:checked").val();
	
	if(tipo_consulta == 'aux' && wauxiliar == '')
		{
            alert('Debe seleccionar un auxiliar');
            return;
        }
		
	if(tipo_consulta == 'hising')
		{
			if(whistoria == '')
				{
				alert('Debe ingresar la historia.');
				return;				
				}
			else
				{
				if(wingreso == '')
					{
					alert('Debe digitar el ingreso.');
					return;
					}
				}
            
        }		
	
    $.post("rep_reposiciones.php",
            {
                consultaAjax:       'mostrar_apl_repuestas',
                wemp_pmla:          wemp_pmla,
                wbasedato:          wbasedatos,
                wauxiliar:          wauxiliar,
                wfecha_inicial:     wfecha_inicial,
                wfecha_final:       wfecha_final,
				wtipo_consulta:		tipo_consulta,
				whistoria:			whistoria,
				wingreso:			wingreso			

            }
            ,function(data_json) {

                if (data_json.error == 1)
                {
                   alert(data_json.mensaje);
				   $("#datos_reporte").html("<center><div id='datos_reporte'></div></center>");
				   $.unblockUI();
                   return;
                }
                else
                {
                    $("#datos_reporte").html(data_json.table);   
					$.unblockUI();					
                }

        },
        "json"
    );
}

//Muestra los articulos que estan por reponer.
function mostrar_datos()
 {
	  $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
	
    var wauxiliar = $("#auxiliar").val();	
    var wbasedatos = $("#wbasedatos").val();
    var wemp_pmla = $("#wemp_pmla").val();
    var wfecha_inicial = $("#wfecha_inicial").val();
    var wfecha_final = $("#wfecha_final").val();
	var whistoria = $("#historia").val();
	var wingreso = $("#ingreso").val();
	var tipo_consulta = $("input[name='tipo_consulta']:checked").val();
	
	if(tipo_consulta == 'aux' && wauxiliar == '')
		{
            alert('Debe seleccionar un auxiliar');
			$.unblockUI();
            return;
        }
		
	if(tipo_consulta == 'hising')
		{
			if(whistoria == '')
				{
				alert('Debe ingresar la historia.');
				$.unblockUI();
				return;				
				}
			else
				{
				if(wingreso == '')
					{
					alert('Debe digitar el ingreso.');
					$.unblockUI();
					return;
					}
				}
            
        }		
	

    $.post("rep_reposiciones.php",
            {
                consultaAjax:       'mostrar_datos',
                wemp_pmla:          wemp_pmla,
                wbasedato:          wbasedatos,
                wauxiliar:          wauxiliar,
                wfecha_inicial:     wfecha_inicial,
                wfecha_final:       wfecha_final,
				wtipo_consulta:		tipo_consulta,
				whistoria:			whistoria,
				wingreso:			wingreso			

            }
            ,function(data_json) {

                if (data_json.error == 1)
                {
                   alert(data_json.mensaje);
				   $.unblockUI();
                   return;
                }
                else
                {
                    $("#datos_reporte").html(data_json.table);                   
					$.unblockUI();
                }

        },
        "json"
    );
}

$(document).ready(function() {
	//Calendario de la fecha inicial
     $("#wfecha_inicial").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true,
	  maxDate:"+2Y"
    });	
	//Calendario de la fecha final
    $("#wfecha_final").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true,
	  maxDate:"+2Y"
    });
	
	$("#tr_auxiliar").show(); //Muesra el campo auxiliar por defecto para hacer la busqueda.
});
</script>

<?php
}
/* ************************************************************************************************
   * PROGRAMA PARA GENERAR EL REPORTE DE ARTICULOS QUE SE HAN VENDIDO POR AUXILIAR PARA REPONERLOS
   ************************************************************************************************/

//==================================================================================================================================
//PROGRAMA                   : rep_reposiciones.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : Agosto 27 de 2013
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Septiembre 16 de 2013";
//DESCRIPCION
//====================================================================================================================================\\
//Programa muestra que articulos o medicamentos se han vendido por auxiliar o historia e ingreso, ademas de lo que se ha repuesto.
//====================================================================================================================================\\



if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{

include_once("root/comun.php");
include_once("movhos/movhos.inc.php");





$wbasedatos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');


// Se incializan variables de fecha hora y usuario
if (strpos($user, "-") > 0)
    $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
    else
        $wuser=$user;

//Consulta el cco de un auxiliar.		
function consultarcco_aux($wcod_auxiliar)
{
	global $conex;
	global $wbasedatos;
	
	//Centro de costos del usuario
    $q_usuario = " SELECT Cjecco "
                ."   FROM ".$wbasedatos."_000030 "
                ."  WHERE Cjeusu = '".$wcod_auxiliar."'"
				."    AND Cjeest = 'on'";
    $res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
    $row_usuario = mysql_fetch_array($res_usuario);
    $wdato_cco = explode('-',$row_usuario['Cjecco']);
	
	return $wdato_cco[0];

}
   
//Esta funcion graba la cantidad que se repondra para el articulo o medicamento, segun el arreglo que se ha formado en la funcion js grabar_reposiciones.
function grabar_reposiciones($wemp_pmla, $wbasedatos, $wdatos_guardar)
{

    global $conex;
	global $wuser;
    
    $datamensaje = array('mensaje'=>'', 'error'=>0);
    $array_ppal = array();	
	
	//Hago explode del arreglo por el simbolo *|* y luego por -*- para guardar la informacion.	
    $wdatos_reposicion_ppal = explode("*|*", $wdatos_guardar);
    
    foreach ($wdatos_reposicion_ppal as $key => $value) {
        
			$wdatos_reposicion = explode("-*-",$value);
        
            $wcantidad_reponer = $wdatos_reposicion[0];
            $wcod_auxiliar = $wdatos_reposicion[1];
			$wcod_art = $wdatos_reposicion[2];
            $whistoria = $wdatos_reposicion[3];
			$wingreso = $wdatos_reposicion[4];
			$wfecha_venta = $wdatos_reposicion[5];
			$wcco_aux = consultarcco_aux($wcod_auxiliar);
            
			//Inserto el log de reposiciones.
			$query = "INSERT INTO ".$wbasedatos."_000170 (    Medico,          Fecha_data,          Hora_data,             Rephis,          Reping,            Repaux,            Repcco,       Repcan,                   Repart,          Repfve,        Repest, Seguridad)
						   VALUES                       ('".$wbasedatos."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$whistoria."', '".$wingreso."', '".$wcod_auxiliar."',       '".$wcco_aux."', '".$wcantidad_reponer."', '".$wcod_art."', '".$wfecha_venta."'   ,'on', 'C-".$wuser."')";
			$res2 = mysql_query( $query ) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );     
      
    }
    
    $datamensaje['mensaje'] = 'Reposiciones guardadas con exito.';
    
    echo json_encode($datamensaje);    
   

}


function nombre_auxiliar($wcodigo, $wbasedatos)
{

    global $conex;

    //Nombre del usuario
    $q_usuario = " SELECT descripcion "
                ."   FROM usuarios "
                ."  WHERE codigo = '".$wcodigo."'";
    $res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
    $row_usuario = mysql_fetch_array($res_usuario);
    $wnombre = $row_usuario['descripcion'];

    return $wnombre;

}

function nombre_art($wcodigo_art)
	{
	
	global $conex;
	global $wbasedatos;
	
	$q =     " SELECT Artnom "
			."   FROM ".$wbasedatos."_000001 "
			."  WHERE Artcod = '".$wcodigo_art."'";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row = mysql_fetch_array($res);
    $wnombre_art = $row['Artnom'];
	
	return $wnombre_art;
	
	}


//Esta funcion permite mostrar lo que se ha repuesto por auxiliar o por historia e ingreso.
function mostrar_apl_repuestas($wemp_pmla, $wbasedatos, $wauxiliar, $wfecha_inicial, $wfecha_final, $wtipo_consulta, $whistoria, $wingreso)
    {
	
	global $conex;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'table'=>'');
	
	switch ($wtipo_consulta) {
			
			case 'rep_aux':
			
					//Se consulta en la tabla lo repuesto por auxiliar.
					$query_art = "SELECT Repart, Repfve, SUM(Repcan) as repuestas, Repaux
									FROM ".$wbasedatos."_000170
								   WHERE Repaux = '".$wauxiliar."'
									 AND Fecha_data BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'
									 AND Repest = 'on'									 
								GROUP BY Repart
								ORDER BY Repfve";
					
			break;
			
			case 'rep_hising':
			
					//Se consulta en la tabla lo repuesto por historia e ingreso.
					$query_art = "SELECT Repart, Repfve, SUM(Repcan) as repuestas, Repaux
									FROM ".$wbasedatos."_000170
								   WHERE Rephis = '".$whistoria."'
									 AND Reping = '".$wingreso."'
									 AND Fecha_data BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'
									 AND Repest = 'on'									
								GROUP BY Repart
								ORDER BY Repfve";
			break;			
			default:
			break;
		
		}			
		
	$res_art = mysql_query( $query_art, $conex) or die( mysql_errno()." - Error en el query $query_art - ".mysql_error() );
	$num_art = mysql_num_rows($res_art);
	
	$nombre_aux = nombre_auxiliar($wauxiliar, $wbasedatos);
	
	$texto_html .= "<br>";
	$texto_html .= "<table id='datos_repuestos' style='text-align: center;' border=1 cellspacing=0 >";
	$texto_html .= "<tr class=encabezadotabla><td colspan=3>".$nombre_aux."</td>";
	$texto_html .= "<tr class=encabezadotabla>
					<td>Articulo</td>
					<td>Fecha de venta</td>			
					<td>Cantidad Repuesta</td>				
					</tr>";
	$i = 1;
	//Recorro lo repuesto segun la consulta inicial.
	while($row_rep = mysql_fetch_array($res_art))
		{
			 if (is_integer($i/2))
                   $wclass="fila1";
                else
                   $wclass="fila2";
				   
			$wnombre_art = nombre_art($row_rep['Repart']);
			$texto_html .= "<tr class=".$wclass.">
								<td>".$row_rep['Repart']."-".$wnombre_art."</td>
								<td>".$row_rep['Repfve']."</td>					
								<td>".$row_rep['repuestas']."</td>								
							</tr>";
			
			$wtotal = $wtotal+$row_rep['repuestas'];					
			$i++;
		}
	$texto_html .= "<tr class=encabezadotabla><td>Total</td><td colspan=2>".$wtotal."</td></tr>";	
	$texto_html .= "</table>";
	
	if($num_art > 0)
		{
		$datamensaje['table'] = utf8_encode($texto_html);		
		}
	else
		{
		$datamensaje['error'] = 1;
		$datamensaje['mensaje'] = "No tiene articulos repuestos en este lapso de fechas.";		
		}
		
	echo json_encode($datamensaje);
	
	}



//Esta funcion muestra los articulos que estan para reponer de acuerdo al filtro de auxiliar o historia e ingreso.
function mostrar_datos($wemp_pmla, $wbasedatos, $wauxiliar, $wfecha_inicial, $wfecha_final, $wtipo_consulta, $whistoria, $wingreso)
    {

    global $conex;
    $texto_html = '';
    $wtienedatos = false;
    $datamensaje = array('mensaje'=>'', 'error'=>0, 'table'=>'');
	
	
	switch ($wtipo_consulta) {
			case 'aux':
								
				//Busca los articulo o medicamentos para reponer restando las cantidades vendidas con las devoluciones y filtrando por auxiliar.							
				$query_art = "SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, (sum(ventas)-sum(devoluciones)) as cantidad, tcarusu
								FROM
								(
								  SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, sum(tcarcan) as ventas, 0 as devoluciones, tcarusu
									FROM ".$wbasedatos."_000106
								   WHERE tcarusu = '".$wauxiliar."'									 
									 AND Tcardev = 'off'
									 AND Tcarest = 'on'
									 AND Tcarapr = 'off'
									 AND tcarfec BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'
								GROUP BY tcarfec, tcarprocod
								UNION ALL
								  SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, 0 as ventas, sum(tcarcan) as devoluciones, tcarusu
									FROM ".$wbasedatos."_000106
								   WHERE tcarusu = '".$wauxiliar."'									
									 AND Tcardev = 'on'
									 AND Tcarest = 'on'
									 AND Tcarapr = 'off'
									 AND tcarfec BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'
								GROUP BY tcarfec, tcarprocod
								) as t
								GROUP BY tcarfec, tcarprocod";	
			break;			
			case 'hising':
					
					//Busca los articulo o medicamentos para reponer restando las cantidades vendidas con las devoluciones y filtrando por his e ing.
					$query_art = "SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, (sum(ventas)-sum(devoluciones)) as cantidad, tcarusu
								FROM
								(
								  SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, sum(tcarcan) as ventas, 0 as devoluciones, tcarusu
									FROM ".$wbasedatos."_000106
								   WHERE tcarhis = '".$whistoria."'
									 AND tcaring = '".$wingreso."'								   
									 AND Tcardev = 'off'
									 AND Tcarest = 'on'
									 AND Tcarapr = 'off'
									 AND tcarfec BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'
								GROUP BY tcarfec, tcarprocod
								UNION ALL
								  SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, 0 as ventas, sum(tcarcan) as devoluciones, tcarusu
									FROM ".$wbasedatos."_000106
								   WHERE tcarhis = '".$whistoria."'
									 AND tcaring = '".$wingreso."'									
									 AND Tcardev = 'on'
									 AND Tcarest = 'on'
									 AND Tcarapr = 'off'
									 AND tcarfec BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'
								GROUP BY tcarfec, tcarprocod
								) as t
								GROUP BY tcarfec, tcarprocod";										
					
			break;
			
			default:
			
			break;
		
		}
	
	
	$res_art = mysql_query( $query_art, $conex) or die( mysql_errno()." - Error en el query $query_art - ".mysql_error() );
		
	
    $texto_html .= "<br>";
    $texto_html .= "<table id='datos_reporte' style='text-align: center;' border=1 cellspacing=0 >
                    <tbody>";
    

    $i = 1;
    //Recorro los articulos que estan para reponer.
    while($row = mysql_fetch_array($res_art))
        { 
        
		//Segun el filtro consulto el articulo que esta para reponer en la tabla de reposiciones por fecha, y auxiliar.
		switch ($wtipo_consulta) {
			case 'aux':
			
				$query_repuestas =   "SELECT sum(Repcan) as rep_cantidad
										FROM ".$wbasedatos."_000170
									   WHERE Repaux = '".$wauxiliar."'
										 AND Repart = '".$row['Tcarprocod']."'	
										 AND Repfve = '".$row['tcarfec']."'
										 AND Repaux = '".$row['tcarusu']."'
										 AND Repest = 'on'";			
			
			
			break;
			
			case 'hising':
			
				$query_repuestas =   "SELECT sum(Repcan) as rep_cantidad
										FROM ".$wbasedatos."_000170
									   WHERE Rephis = '".$whistoria."'
										 AND Reping = '".$wingreso."'
										 AND Repart = '".$row['Tcarprocod']."'	
										 AND Repfve = '".$row['tcarfec']."'
										 AND Repaux = '".$row['tcarusu']."'
										 AND Repest = 'on'";			
			break;
			
			
			default:
			break;
		}
		
		$res_repuestas = mysql_query( $query_repuestas, $conex) or die( mysql_errno()." - Error en el query $query_repuestas - ".mysql_error() );
		$row_repuestas = mysql_fetch_array($res_repuestas);
		$wrepuestas = $row_repuestas['rep_cantidad'];
		
		//Si la cantidad a reponer menos lo repuesto es mayor a cero mostrara la informacion en la interfaz.
         if(($row['cantidad']-$wrepuestas) > 0)
            {
			
				$wcantidad_total = $row['cantidad']-$wrepuestas;
				//Encabezado de la tabla con el articulo y el total a reponer.
                $texto_html .= "<tr class=encabezadotabla>
                                    <td>Articulo</td>
                                    <td>Cantidad<br>Aplicada</td>
								</tr>";
				
				$wnombre_art = nombre_art($row['Tcarprocod']);
                $texto_html .= "<tr class=fila1>
                                    <td>".$row['Tcarprocod']."-".$wnombre_art."</td>
                                    <td>".$wcantidad_total."</td>
                                </tr>";
				
				//Consulto de nuevo lo que esta por reponer con el filtro de articulo y fecha para saber si hay datos en el detalle.
				switch ($wtipo_consulta) {
						case 'aux':			
							
							//Busca los detalle del articulo, restando las cantidades vendidas con las devoluciones.							
							$query_det = "SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, (sum(ventas)-sum(devoluciones)) as cantidad
											FROM
											(
													  SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, sum(tcarcan) as ventas, 0 as devoluciones
														FROM ".$wbasedatos."_000106
													   WHERE tcarusu = '".$wauxiliar."'
														 AND tcarprocod = '".$row['Tcarprocod']."'
														 AND tcarfec = '".$row['tcarfec']."'
														 AND Tcardev = 'off'
														 AND Tcarest = 'on'
														 AND Tcarapr = 'off'
													GROUP BY tcarfec, tcarprocod
													UNION ALL
													  SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, 0 as ventas, sum(tcarcan) as devoluciones
														FROM ".$wbasedatos."_000106
													   WHERE tcarusu = '".$wauxiliar."'
														 AND tcarprocod = '".$row['Tcarprocod']."'
														 AND tcarfec = '".$row['tcarfec']."'
														 AND Tcardev = 'on'
														 AND Tcarest = 'on'
														 AND Tcarapr = 'off'
													GROUP BY tcarfec, tcarprocod
											) as t
											GROUP BY tcarfec, tcarprocod";								
								
							break;
							
						case 'hising':
						
							//Busca los detalle del articulo, restando las cantidades vendidas con las devoluciones.							
							$query_det = "SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, (sum(ventas)-sum(devoluciones)) as cantidad
											FROM
											(
													  SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, sum(tcarcan) as ventas, 0 as devoluciones
														FROM ".$wbasedatos."_000106
													   WHERE tcarhis = '".$whistoria."'
													     AND tcaring = '".$wingreso."'
														 AND tcarprocod = '".$row['Tcarprocod']."'
														 AND Tcardev !='on'
														 AND Tcarest = 'on'
														 AND Tcarapr = 'off'
													GROUP BY tcarfec, tcarprocod
													UNION ALL
													  SELECT Tcarprocod, tcarfec, id, Tcarhis, Tcaring, Tcarcmo, 0 as ventas, sum(tcarcan) as devoluciones
														FROM ".$wbasedatos."_000106
													   WHERE tcarhis = '".$whistoria."'
													     AND tcaring = '".$wingreso."'
														 AND tcarprocod = '".$row['Tcarprocod']."'
														 AND Tcardev !='off'
														 AND Tcarest = 'on'
														 AND Tcarapr = 'off'
													GROUP BY tcarfec, tcarprocod													
											) as t
											GROUP BY tcarfec";							
						
						break;
						default:
						break;
					
					}				
				
                $res_det = mysql_query( $query_det, $conex) or die( mysql_errno()." - Error en el query $query_det - ".mysql_error() );
                $num_det = mysql_num_fields($res_det);
				
				//Si no hay datos moestrara un mensaje de que todo a sido repuesto.
                if($num_det > 0)
                {
                    //Esta variable controla si ya se repusieron todos los articulos para el auxiliar, si se mantiene en ok imprimira la informacion.
                        $wtienedatos = true;
                }
                else
                {
                    //Esta variable controla si ya se repusieron todos los articulos para el auxiliar, si se mantiene en ok imprimira la informacion.
                        $wtienedatos = false;
                }
				
				//Muestra la informacion detalle del articulo, fecha, cantidad y cantidad maxima a reponer.
                $texto_html .= "<tr class='detalle' >
                                    <td colspan=4 align=center>
                                    <table style='text-align: center;' border=1 cellspacing=0>";
                $texto_html .= "<tr class=encabezadotabla><td>Fecha</td><td>Cantidad</td><td>Cantidad máxima a reponer</td><td>Reponer</td></tr>";

                while($row_det = mysql_fetch_array($res_det))
                    {                         
					
					switch ($wtipo_consulta) {
							case 'aux':
							
								$query_repuestas =  " SELECT Repcan
														FROM ".$wbasedatos."_000170
													   WHERE Repaux = '".$wauxiliar."'
														 AND Repart = '".$row['Tcarprocod']."'	
														 AND Repfve = '".$row['tcarfec']."'
														 AND Repaux = '".$row['tcarusu']."'
														 AND Repest = 'on'";				
							
							
							break;
							
							case 'hising':
							
								$query_repuestas =   "SELECT sum(Repcan) as rep_cantidad
														FROM ".$wbasedatos."_000170
													   WHERE Rephis = '".$whistoria."'
														 AND Reping = '".$wingreso."'
														 AND Repart = '".$row['Tcarprocod']."'	
														 AND Repfve = '".$row['tcarfec']."'
														 AND Repaux = '".$row['tcarusu']."'
														 AND Repest = 'on'";			
							break;
							
							
							default:
							break;					
							}
							
					$res_repuestas = mysql_query( $query_repuestas, $conex) or die( mysql_errno()." - Error en el query $query_art - ".mysql_error() );
					$row_repuestas = mysql_fetch_array($res_repuestas);
					$wrepuestas = $row_repuestas['Repcan'];					
					
					//Si la cantidad maxima a reponer es mayor a cero mostrara el detalle del articulo.
                    $wcantidad_max_reponer = $row_det['cantidad']-$wrepuestas;             
					
					if($wcantidad_max_reponer > 0)
						{
						$texto_html .="     <tr class=fila2>
											<td>".$row_det['tcarfec']."</td>
											<td>".$wcantidad_max_reponer."</td>
											<td><input type=text size=5 cant_maxima='".$wcantidad_max_reponer."'  onchange='validarcuantos(this);' value='".$wcantidad_max_reponer."' id='cant_a_reponer-".$row_det['id']."'></td>
											<td><input type=checkbox cant_maxima='".$wcantidad_max_reponer."' cod_art='".$row_det['Tcarprocod']."' id='dato-reponer_".$row_det['id']."_".$row_det['Tcarprocod']."_".$row_det['Tcarhis']."_".$row_det['Tcaring']."_".$row_det['tcarfec']."' onclick='agregar_clase(this);'></td>
											</tr>";
						}

                    }
                 $texto_html .="<tr><td colspan=4>&nbsp;</td></tr></table>
                                </td>
                                </tr>";

                    $i++;
            }
        }


     if($wtienedatos)
        {
         $texto_html .= "</table>";
         $texto_html .= "<table id='boton_guardar' style='text-align: center;' border=0>";
         $texto_html .= "<tr>";
         $texto_html .= "<td>";
         $texto_html .= "<input type=reset onclick='ocultar_tablas();' id='botonlimpiar' value='Limpiar'><input type=button onclick='grabar_reposiciones();' id='botonguardar' value='Guardar'>";
         $texto_html .= "</td>";
         $texto_html .= "</tr>";
         $texto_html .= "</table>";
         $texto_html .= "</center>";
         $datamensaje['table'] = utf8_encode($texto_html);
        }
     else
        {
         $text_div = '<div id=datos_reporte></div>';
         $datamensaje['table'] = $text_div;
         $datamensaje['error'] = 1;
		 if($wtipo_consulta == 'aux')
			{
			$datamensaje['mensaje'] = 'No hay aplicaciones hechas por el auxiliar en el lapso de tiempo seleccionado o ya fueron repuestas.';
			}
		else
			{
			$datamensaje['mensaje'] = 'No hay aplicaciones para la historia e ingreso en el lapso de tiempo seleccionado o ya fueron repuestas.';
			}
         

        }

    echo json_encode($datamensaje);

    }



//Este segmento interactua con los llamados ajax

//Si la variable $consultaAjax tiene datos entonces busca la funcion que trae la variable.
if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {

                    case 'mostrar_datos':
                        {
                            echo mostrar_datos($wemp_pmla, $wbasedatos, $wauxiliar, $wfecha_inicial, $wfecha_final, $wtipo_consulta, $whistoria, $wingreso);
                        }
                    break;
					
					case 'mostrar_apl_repuestas':
                        {
                            echo mostrar_apl_repuestas($wemp_pmla, $wbasedatos, $wauxiliar, $wfecha_inicial, $wfecha_final, $wtipo_consulta, $whistoria, $wingreso);
                        }
                    break;
                
                    case 'grabar_reposiciones':
                        {
                            echo grabar_reposiciones($wemp_pmla, $wbasedatos, $wdatos_guardar);
                        }
                    break;

                    default : break;
                }
            return;
            }


  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L
  //===========================================================================================================================================
  //===========================================================================================================================================

	echo "<form name='rep_desc_escalonados' id='rep_desc_escalonados' action=''>";
	echo "<input type='HIDDEN' id='wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' id='wbasedatos' value='".$wbasedatos."'>";
    echo "<input type='HIDDEN' id='wusuario' value='".$wuser."'>";

	encabezado("REPORTE REPOSICIONES", $wactualiz, $wbasedatos);

   //============================================================================================================
    $select_aux = '';
    $query_aux = "SELECT Cjeusu, descripcion, Cjecco
			         FROM usuarios, ".$wbasedatos."_000030
			        WHERE activo = 'A'
                      AND cjeusu = codigo
                      AND cjebod = 'on'
                 ORDER BY descripcion";
    $res_aux = mysql_query( $query_aux ) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );

    $arr_aux = array();
    while($row_aux = mysql_fetch_array($res_aux))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_aux['Cjeusu'], $arr_aux))
        {
            $arr_aux[$row_aux['Cjeusu']] = array();
        }
		$wcco_aux = explode("-",$row_aux['Cjecco']);
        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_aux[$row_aux['Cjeusu']] = $wcco_aux[0]."-".$row_aux['descripcion'];

    }

    $select_aux .=  "<select id='auxiliar'>";
    $select_aux .=  "<option value=''>Seleccione...</option>";    

    foreach ($arr_aux as $key => $value) {

            $select_aux .=  "<option value='".$key."'>".$key."-".strtoupper($value)."</option>";
    }

    $select_aux .=  "</select>";

    echo "<br>";
    echo "<center>";
    echo "<input type=hidden id='cod_relacion'>";
    echo "<table style='text-align: center; width: auto;'>
          <tbody>
		  <tr class=fila1 align=center>
            <td colspan=2>
			<table border='0'>
			  <tbody>
				<tr class=encabezadotabla>
				  <td colspan='2' rowspan='1' align=center>REPONER</td>
				  <td colspan='2' rowspan='1' align=center>REPUESTO</td>
				</tr>
				<tr class=titulo>
				  <td><input id='tipo_consulta_aux' onclick='mostrar_tipo(\"auxiliar\");' checked='checked' value='aux' name='tipo_consulta' type='radio'></td>
				  <td>Auxiliar</td>
				  <td><input id='tipo_consulta_rep' onclick='mostrar_tipo(\"repuesto_aux\");' value='rep_aux' name='tipo_consulta' type='radio'></td>
				  <td>Auxiliar</td>
				</tr>
				<tr class=titulo>
				  <td><input id='tipo_consulta_hising' onclick='mostrar_tipo(\"hising\");' value='hising' name='tipo_consulta' type='radio'></td>
				  <td>Historia e ingreso</td>
				  <td><input id='tipo_consulta_rep' onclick='mostrar_tipo(\"rep_hising\");' value='rep_hising' name='tipo_consulta' type='radio'></td>
				  <td>Historia e ingreso</td>
				</tr>
			  </tbody>
			</table>
			</td>           
            </tr>
            <tr id='tr_auxiliar' class=fila1 align=left style='display:none;'>
                <td><b>Auxiliar:</b></td>
                <td>
                $select_aux
                </td>
            </tr>
			<tr id='hising' class=fila1 align=left style='display:none;'>
                <td><b>Historia:</b></td>
                <td>
                <input type=text id='historia'><b>Ingreso:</b><input type=text id='ingreso' size=4>
                </td>
            </tr>			
            <tr class=fila1>
                <td align=left><b>Fecha inicial:</b></td>
                <td align=left><input type=text id=wfecha_inicial value='".date("Y-m-d")."'>
                </td>
            </tr>
            <tr class=fila1>
                <td align=left><b>Fecha final:</b></td>
                <td align=left><input type=text id=wfecha_final value='".date("Y-m-d")."'>
                </td>
            </tr>
           </tbody>
           </table>";
    echo " <table>";
    echo "  <tr>";
    echo "      <td>";
    echo "      <input type=reset onclick='ocultar_tablas();' id=botonlimpiar value=Limpiar><input type=button onclick='mostrar_datos();' id=botonenviar value=Enviar><br>&nbsp;&nbsp<input type=button onclick='cerrarVentana();' value='Cerrar ventana'>";
    echo "      </td>";
    echo "  </tr>";
    echo " </table>";
    echo "</center>";
    echo "<center>
            <div id='datos_reporte'></div>
          </center>";
	echo "</form>";
}
?>