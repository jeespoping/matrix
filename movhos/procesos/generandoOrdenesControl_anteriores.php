
<title>IMPRESION MEDICAMENTOS DE CONTROL</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<script src="../../../include/root/print.js" type="text/javascript"></script>


<script>
document.oncontextmenu = function(){return false}



function consultar_fecha(tipo){
	
	if($("#wfecha_consulta").val() != ""){
		$("#tipo_consulta").val(tipo);
		$("#medcontrol").submit();
		
	}else{
		
		if(tipo == 'fgen'){
		
			var texto = 'fecha de generación';
		}else{
		
			var texto = 'fecha de impresión';
		}
		alert("Debe seleccionar una "+texto+".");
	}
	
	
	
}
//$(document).ready(function () {
$(document).keypress(function (e) { 
	// $("body").keypress(function (e) {
		
		console.log(e.which);
		//Enter
		if (e.which == 13) {
		 return false;
		}
		
		//F10
		if (e.which == 0) {
		 return false;
		}
		
		//Tecla p
		if (e.which == 112) {
		 return false;
		}
				
		//Tecla s
		if (e.which == 115) {
		 return false;
		}
		
		if (e.which == 18) {
		 return false;
		}
		
		
		
		//Tecla a
		if (e.which == 97) {
		 return false;
		}
		
		//Tecla c
		if (e.which == 99) {
		 return false;
		}
		
		if(event.altLeft) {
			if((e.which.keyCode == 37) || (e.which.keyCode == 39)) {
			//Bloquear Alt + Cursor Izq/Der.
			return false;
			}
		}
	});

	
function stopRKey(evt) { var evt = (evt) ? evt : ((event) ? event : null); var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); if ((evt.keyCode == 18) && (node.type=="text")) { return false; } } 

// document.onkeypress = stopRKey;


function boton_imp(){

	$(".printer").bind("click",function()
		{
		
			$(".areaimprimirMedControl").printArea({			
				
				popClose: false,
				popTitle : 'Medicamentos_de_Control',
				popHt    : 500,
				popWd    : 1200,
				popX     : 200,
				popY     : 200,
				
				});
				
			
		});

}

/************************************************************************
 * Busca la fila siguiente para mostrar o ocultar la fila
 * Por tanto campo es una Fila
 ************************************************************************/
function mostrarFila( campo ){

	var tabla = campo.parentNode;
	var index = campo.rowIndex;

	mostrar( tabla.rows[ index+1 ] );
}

function limpiarbusqueda(){

 $.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
$('input#id_search').val('');
location.reload();

}

function pulsar(e) {
	tecla=(document.all) ? e.keyCode : e.which;
  if(tecla==13) return false;
}

function recargar(){

$.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
				 
setTimeout(function() 
	  {
	   
		$.unblockUI()
		location.reload();
	  }, 3000);
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
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);	


$(function() {

	$( ".desplegable" ).accordion({
			collapsible: true,
			active:0,
			heightStyle: "content",
			icons: null
	});
	
	//Permite que al escribir en el campo buscar, se filtre la informacion del grid
	$('input#id_search').quicksearch('div#accordion');
	
	boton_imp();
	
	$("#wfecha_inicial").datepicker({
   
    });
	
	$("#wfecha_final").datepicker({
   
    });	
	
	
});

</script>
<?php
include_once("conex.php");

/**************************************************************************************************************
 * Impresion de formulas de control
 *
 * Fecha de creación:	2012-10-09
 * Por:					Edwin Molina Grisales
 **************************************************************************************************************/
/**************************************************************************************************************
 * DESCRIPCION:
 *
 * Al grabar las ordenes médicas se genera una orden de impresión para los medicamentos de control que el médico
 * halla ordenada.
 *
 * La orden debe ir por la cantidad segun el perfil, es decir la cantidad requerida hasta el día siguiente a la
 * hora de corte.
 *
 * ESPECIFICACIOENS:
 *
 * - El programa graba los articulos de control que el medico halla ordenado para un paciente.
 * - No se permite mandar mas de un medicamento de control a la vez con la misma frecuencia (???)
 * - 
 **************************************************************************************************************/
					  /*						MODIFICACIONES				*/
 /*
 Febrero 21 de 2018 (Edwin)		: - Se modifica para que se generen las ordenes de control en centros de costos de ayuda dx
 Abril 27 de 2017(Edwin)		: - Se actualiza los dx vacios con el dx actual
								: - Para las DA se muestra el nombre del articulo que fue ordenado por primera vez antes del reemplazo
								: - Se crea paramettro global editable para indicar que la impresión es solo de consulta. al momento de publicar solo se usa en ordenes
									para mostrar al médico la orden de impresión
 Abril 5 de 2017(Edwin)			: - Se mejora query que consulta el nombre del articulo a imprimir para articulos de CM con el filtro tipmat en la función consultarDescripcionArticulo
								  - Se consulta el dx desde la función de comun.php consultarUltimoDiagnosticoHCE
 Diciembre 15 de 2016 (Edwin)	: - Para los articulos ordenados por infusión continua y que sean de control, se generará una orden de impresión con una cantidad por defecto
									que está configurada en la definición de fracciones campo Defcci
 Diciembre 14 de 2016 (Edwin)	: - Para los articulos de CM se imprime la forma farmaceutica correspondiente a su insumo.
 Diciembre 13 de 2016 (Edwin)	: - Si el aritculo a imprimir es un articulo de central de mezclas (DA,NE) se imprime el nombre generico del insumo correspondiente que lo compone.
 Diciembre 06 de 2016 (Edwin)	: - Se agregan las fechas 3 al 5 en la impresión de las formulas de control.
 Diciembre 01 de 2016 (Edwin)	: - Se agregan campos nuevos para la impresión de medicamentos de control (Consecutivo,Dosis maxima,Diagnostico,Servicio actual,Habitacion actual).
								  - Se crea campo tabla de encabezados para los medicamentos de control
 Febrero 24 de 2015 (Jonatan)	: Se imprimen la formulas de control de forma individual.
 Febrero 16 de 2015 (Jonatan)	: Se agrega filtro por fecha a los medicamentos de control ya impresos, ademas se agrega la fecha de generacion a lado del articulo.
 
 Diciembre 12 de 2014 (Jonatan)	: Cuando se registra la orden de control para el dia siguiente de forma automatica, el registro se hara con el 
								 codigo del medico que la registró inicialmente.
 
 */
/****************************************************************************************************************
 * 												FUNCIONES
 ****************************************************************************************************************/
 
  
 
/******************************************************************************************************************
 * Consulta la frecuencia de acuerdo al codigo
 ******************************************************************************************************************/
function consultarFrecuenciaPorHoraImpControl( $conex, $wbasedato, $codigoFrecuencia ){

	$val = false;

	$sql = "SELECT Perequ
			  FROM {$wbasedato}_000043 b
			 WHERE percod = '$codigoFrecuencia'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['Perequ'];
	}

	return $val;
}

function pintarInforRegrente( $regentes ){
	// var_dump( $regentes  );
	if( count( $regentes ) > 0 ){
		
		$i = 1;
		foreach( $regentes as $key => $value ){
			
			if( $value['cantidad'] > 1 ){
				$cantidadLetras = montoescrito($value['cantidad']);
				
				//Esto es para sacar el valor por que monto escrito es para valores en pesos
				$cantidadLetras = trim( substr( $cantidadLetras, 0, strpos( $cantidadLetras, "PESOS" ) ) );
			}
			else{
				$cantidadLetras = "UNO";
			}
			
			echo "<tr>";
			echo "<td>Fecha $i ".$value['fecha']."</td>";
			echo "<td align='center'>".$cantidadLetras."</td>";
			echo "<td align='center'>".$value['cantidad']."</td>";
			echo "<td colspan=3>".$value['nombre']."</td>";
			echo "</tr>";
			$i++;
		}
	}
	
	for( $i = count($regentes)+1; $i < 6; $i++ ){
		echo "<tr>";
		echo "<td>Fecha $i</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";
	}
	
}
 
function consultarNombreUsuario( $conex, $usuario ){
	
	$val = false;
			
	// Busco el total dispensado
	//Solo se tiene en cuenta los cco de CM,SF y urgencias que son los que dispensan a piso lo ordenado
	$sql = "SELECT Descripcion
	          FROM usuarios
			 WHERE codigo = '".$usuario."'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) )
		$val = $rows[ 'Descripcion' ];
	
	return $val;
}


/****************************************************************************************************************************************
 * Consulta la cantidad dispensada por usuario para cierto medicamento
 *
 * Nota: Se tiene en cuenta que la misma cantidad de registros en la uuditoría es la misma cantidad de registros
 * de dispensación (movhos_00003) y están en el mismo orden. Por tal motivo cuando se quita un elemento del array de auditoria se quita
 * también un elemento del array de dispensados y en la misma posición
 ****************************************************************************************************************************************/
function consultarCantidadDispensada( $conex, $wbasedato, $his, $ing, $art, $ido, $fecha ){
	
	$val = false;
			
	// Busco el total dispensado
	//Solo se tiene en cuenta los cco de CM,SF y urgencias que son los que dispensan a piso lo ordenado
	$sql = "SELECT b.Hora_data, Fdecan, a.Fenfue, c.Ccofca, c.Ccofde, b.Seguridad
	          FROM ".$wbasedato."_000002 a, ".$wbasedato."_000003 b, ".$wbasedato."_000011 c
			 WHERE fenhis 		= '".$his."' 
			   AND fening 		= '".$ing."' 
			   AND fdeart 		= '".$art."'
			   AND a.fecha_data = '".$fecha."'
			   AND fdenum 		= fennum
			   AND fencco 		= ccocod
			   AND fenest 		= 'on'
			   AND fenest 		= 'on'
			   AND ccofac		= 'on'
			   AND ccotra		= 'on'
			   AND ccoima		= 'off'
			 UNION
			SELECT b.Hora_data, Fdecan, a.Fenfue, c.Ccofca, c.Ccofde, b.Seguridad
	          FROM ".$wbasedato."_000002 a, ".$wbasedato."_000003 b, ".$wbasedato."_000011 c
			 WHERE fenhis 		= '".$his."' 
			   AND fening 		= '".$ing."' 
			   AND fdeart 		= '".$art."'
			   AND a.fecha_data	= '".$fecha."'
			   AND fdenum 		= fennum
			   AND fenest 		= 'on'
			   AND fenest 		= 'on'
			   AND fencco 		= ccocod
			   AND ccofac		= 'on'
			   AND ccourg		= 'on'
			   AND ccoima		= 'off'
			   AND ccocir		= 'off'
		     UNION
			SELECT b.Hora_data, COUNT( DISTINCT fdenum ) as Fdecan, a.Fenfue, c.Ccofca, c.Ccofde, b.Seguridad
	          FROM ".$wbasedato."_000002 a, ".$wbasedato."_000003 b, ".$wbasedato."_000011 c
			 WHERE fenhis 		= '".$his."' 
			   AND fening 		= '".$ing."' 
			   AND fdeari 		= '".$art."'
			   AND a.fecha_data = '".$fecha."'
			   AND fdenum 		= fennum
			   AND fenest 		= 'on'
			   AND fenest 		= 'on'
			   AND fencco 		= ccocod
			   AND ccofac		= 'on'
			   AND ccotra		= 'on'
			   AND ccoima		= 'on'
		  ORDER BY 1
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	//Primero se crea un array con todo lo dispensado para el día correspondiente
	//Si es una devolución se quita el inmediatamente anterior de la lista de dispensados
	//Por último, se revisa de los dispensados cuál corresponde a la auditoría
	$dispensados = array();
	$devueltos = array();
	while( $rows = mysql_fetch_array( $res ) ){
		
		if( $rows[ 'Fdecan' ] > 0 ){
			
			$esCargo =  $rows[ 'Fenfue' ] == $rows[ 'Ccofca' ] ? true: false;
			if( $esCargo ){
				$dispensados[] = array(
					'hora' 		=> $rows[ 'Hora_data' ],				//Hora dispensado
					'cantidad' 	=> $rows[ 'Fdecan' ],					//Cantidad
					'usuario' 	=> substr( $rows[ 'Seguridad' ], 2 ),	//Usuario que dispensa
				);
			}
			else{
				$devueltos[] = array(
					'hora' 		=> $rows[ 'Hora_data' ],				//Hora dispensado
					'cantidad' 	=> $rows[ 'Fdecan' ],					//Cantidad
					'usuario' 	=> substr( $rows[ 'Seguridad' ], 2 ),	//Usuario que dispensa
				);
			}
		}
	}
	
	echo "======================DISPENSADOS================<br>";
	var_dump( $dispensados );
	echo "=================================================<br>";
	
	// Se busca los articulos dispensados según la auditoria
	$sql = "SELECT Hora_data, Kauhis, Kauing, Kaufec, Kaudes, Kaumen, Kauido, Seguridad
			  FROM ".$wbasedato."_000055 a
			 WHERE Kaufec =  '".$fecha."'
			   AND Kaumen LIKE  'Articulo dispensado'
			   AND kauhis =  '".$his."'
			   AND kauing =  '".$ing."'
			   AND kaudes LIKE '".$art."%'
		  ORDER BY 1
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	$dispensadosAuditoria = array();
	while( $rows = mysql_fetch_array($res) ){
		$dispensadosAuditoria[] = $rows;
	}
	
	//Quito lo dispensado antes de los devueltos
	foreach( $devueltos as $key => $value ){
		for( $i = 1; $i < count($dispensadosAuditoria)+1; $i++ ){
			if( $i == count($dispensadosAuditoria) || $value['hora'] < $dispensadosAuditoria[$i]['Hora_data'] ){
				
				$cantidadADevolver = $value['cantidad'];
				
				//Mientras no se agote la cantidad devuelta, voy quitando lo dispensado
				while( $cantidadADevolver > 0 && $i > 0 ){
					if( $dispensados[$i-1]['cantidad'] > $cantidadADevolver ){
						$dispensados[$i-1]['cantidad'] -= $cantidadADevolver;
						$cantidadADevolver = 0;
					}
					else{
						$cantidadADevolver -= $dispensados[$i-1]['cantidad'];
						array_splice( $dispensadosAuditoria, $i-1, 1 );
						array_splice( $dispensados, $i-1, 1 );
						$i--;
					}
				}
				
				// array_splice( $dispensadosAuditoria, $i-1, 1 );
				break;
			}
		}
	}
	
	//Quito todo lo dispensado de la auditoria que no tenga el ido buscado
	//Cómo se está buscando un registro en particular, elimino los que no son
	//Se elimina tanto en el array de dispensados como el de auditoría 
	//por que para cada registro de auditoría hay un cargo y están en el mismo orden
	// foreach( $dispensadosAuditoria as $key => $value ){
		// if( $value['Kauido'] != $ido ){
			// array_splice( $dispensadosAuditoria, $key, 1 );
			// array_splice( $dispensados, $key, 1 );
		// }
	// }
	
	for( $i = 0; $i < count($dispensadosAuditoria); $i++ ){
		if( $dispensadosAuditoria[$i]['Kauido'] != $ido ){
			array_splice( $dispensadosAuditoria, $i, 1 );
			array_splice( $dispensados, $i, 1 );
			$i--;
		}
	}
	
	echo "======================DISPENSADOS22222222222================<br>";
	var_dump( $dispensados );
	echo "=================================================<br>";
	
	echo "======================dispensadosAuditoria================<br>";
	var_dump( $dispensadosAuditoria );
	echo "=================================================<br>";
	
	
	$usuarios 	= array();	//Estos son los usuarios que dispensan
	$i = 0;
	if( count( $dispensadosAuditoria ) > 0 ){
		
		foreach( $dispensadosAuditoria as $key => $value ){
			
			for( ; $i < count( $dispensados)+1; $i++ ){
				if( $i == count( $dispensados) || $dispensados[$i]['hora'] > $value['Hora_data'] ){
					if( !isset( $usuarios[ $dispensados[$i-1]['usuario'] ] ) ){ 
						$usuarios[ $dispensados[$i-1]['usuario'] ]['fecha'] 	= $fecha;
						$usuarios[ $dispensados[$i-1]['usuario'] ]['cantidad'] 	= $dispensados[$i-1]['cantidad'];
						$usuarios[ $dispensados[$i-1]['usuario'] ]['codigo'] 	= $dispensados[$i-1]['usuario'];
						$usuarios[ $dispensados[$i-1]['usuario'] ]['nombre'] 	= consultarNombreUsuario( $conex, $dispensados[$i-1]['usuario'] );
					}
					else{
						$usuarios[ $dispensados[$i-1]['usuario'] ]['cantidad'] += $dispensados[$i-1]['cantidad'];
					}
					break;
				}
			}
		}
	} 
	
	return $usuarios;
}

function actualizarDxHCE( $conex, $wmovhos, $his, $ing, $diagnostico ){
	
	//Actualizo el dx solo para aquellos articulos cuyo diagnostico sea vacio
	$sql = "UPDATE ".$wmovhos."_000133
			   SET Ctrdia = '".$diagnostico."'
			 WHERE Ctrhis = '".$his."'
			   AND Ctring = '".$ing."'
			   AND Ctrdia = ''
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
}
 
function consultarCantidadDefecto( $conex, $wmovhos, $articulo ){
	
	$val = false;
	
	// Busco si existe encabezado
	$sql = "SELECT Defcci
			  FROM ".$wmovhos."_000059 a, ".$wmovhos."_000011 b
			 WHERE a.Defart = '".$articulo."'
			   AND a.Defest = 'on'
			   AND a.defcco = b.ccocod
			   AND b.ccotra = 'on'
			   AND b.ccoima = 'off'
			   AND b.ccofac = 'on'
			   AND a.Defcci != '0'
			   AND a.Defcci != ''
			   AND a.Defcci != 'NO APLICA'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		@$valor = $rows[ 'Defcci' ]*1;
		
		if( $valor*1 > 0 )
			$val = $valor;
	}
	
	return $val;
}

function consultarDescripcionArticulo( $conex, $wmovhos, $wcenpro, $articulo, $his, $ing, $ido, $fecha ){
	
	$val = false;
	
	/************************************************************************************************************************
	 * Si es un medicamento de CM y fue reamplazado, el nombre debe ser el de medicamento ordenado originalmente
	 * En caso de que no se halla reemplazado, mostrara de acuerdo a sus insumos
	 ************************************************************************************************************************/
	// Busco si existe encabezado
	$sql = "SELECT c.Artgen, c.Artfar
			  FROM ".$wmovhos."_000054 a, ".$wcenpro."_000002 b, ".$wmovhos."_000026 c
			 WHERE a.kadhis = '".$his."'
			   AND a.kading = '".$ing."'
			   AND a.kadart = '".$articulo."'
			   AND a.kadfec = '".$fecha."'
			   AND a.kadido = '".$ido."'
			   AND a.kadaan != ''
			   AND b.artcod = a.kadart
			   AND SUBSTRING( kadaan, 1,6 ) = c.artcod
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	else{
		// Busco si existe encabezado
		$sql = "SELECT g.Artgen, g.Artfar
				  FROM ".$wcenpro."_000002 a, ".$wcenpro."_000001 b, ".$wcenpro."_000003 c, ".$wcenpro."_000009 d, ".$wcenpro."_000002 e, ".$wcenpro."_000001 f, ".$wmovhos."_000026 g
				 WHERE a.artcod = '".$articulo."'
				   AND a.artest = 'on'
				   AND b.tipcdo = 'off'
				   AND c.pdepro = a.artcod
				   AND b.tipcod = a.arttip
				   AND d.appcod = c.pdeins
				   AND d.appest = 'on'
				   AND e.artcod = d.appcod
				   AND f.tipcod = e.arttip
				   AND f.tipmmq = 'off'
				   AND f.tipmat = 'off'
				   AND g.artcod = d.apppre
				";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$val = $rows;
		}
	}
	
	return $val;
}
 
/****************************************************************************************************************
 * Esta función devuelve true si existe un encabezado o lo crea
 ****************************************************************************************************************/
function consultarUbicacionPacientePorHistoriaIngreso( $conex, $wbasedato, $historia, $ingreso, $fecha ){
	
	$val = false;
	
	// Busco si existe encabezado
	$sql = "SELECT Ubisac, Ubihac, Ccocod
			  FROM ".$wbasedato."_000018
		 LEFT JOIN ".$wbasedato."_000011
		        ON ccocod = ubiste
			 WHERE Ubihis = '".$historia."'
			   AND Ubiing = '".$ingreso."'
			";
			
	$sql = "SELECT Habcco as Ubisac, Habcod as Ubihac, Habcco as Ccocod, id
			  FROM ".$wbasedato."_000067
			 WHERE Habhis 		= '".$historia."'
			   AND Habing 		= '".$ingreso."'
			   AND Fecha_data  <= '".$fecha."'
		  ORDER BY id DESC
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		// if( $rows['Ccocod'] !== NULL ){
			// $rows[ 'Ubisac' ] = $rows['Ccocod'];
			// $rows[ 'Ubihac' ] = '';
		// }
		
		$val = array(
			"servicioActual" 	=> $rows[ 'Ubisac' ],
			"habitacionActual" 	=> $rows[ 'Ubihac' ],
		);
	}
	
	return $val;
}

/****************************************************************************************************************
 * Esta función devuelve true si existe un encabezado o lo crea
 ****************************************************************************************************************/
function consultarEncabezado( $conex, $wbasedato, $historia, $ingreso ){
	
	$val = false;
	
	// Busco si existe encabezado
	$sql = "SELECT *
			  FROM ".$wbasedato."_000222
			 WHERE Ecthis = '".$historia."'
			   AND Ecting = '".$ingreso."'
			   AND Ectest = 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		$val = array(
			"fechaRegistro" => $rows[ 'Fecha_data' ],
			"horaRegistro" 	=> $rows[ 'Hora_data' ],
			"historia" 		=> $rows[ 'Ecthis' ],
			"ingreso" 		=> $rows[ 'Ecting' ],
			"direccion" 	=> $rows[ 'Ectdir' ],
			"telefono" 		=> $rows[ 'Ecttel' ],
		);
	}
	
	return $val;
}

/****************************************************************************************************************
 * Esta función devuelve true si existe un encabezado o lo crea
 ****************************************************************************************************************/
function crearEncabezado( $conex, $wbasedato, $historia, $ingreso, $direccion, $telefono ){
	
	$val = false;
	
	// Busco si existe encabezado
	$sql = "SELECT *
			  FROM ".$wbasedato."_000222
			 WHERE Ecthis = '".$historia."'
			   AND Ecting = '".$ingreso."'
			   AND Ectest = 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num == 0 ){
		//Si no existe encabezado procedo a crearlo
		
		$fecha = date( "Y-m-d" );
		$hora = date( "H:i:s" );
		
		// Actualizo el consecutivo
		$sql = "INSERT INTO 
				{$wbasedato}_000222(       Medico    , Fecha_data  ,  Hora_data ,     Ecthis     ,     Ecting    ,     Ectdir      ,     Ecttel     , Ectest,     Seguridad      )
							VALUES ( '".$wbasedato."', '".$fecha."', '".$hora."', '".$historia."', '".$ingreso."', '".$direccion."', '".$telefono."',  'on' , 'C-".$wbasedato."' )";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			$val = true;
		}
	}
	else{
		//Si existe encabezado
		$val = true;
	}
	
	
	return $val;
}
 
/************************************************************************************************
 * Actuazliza el consecutivo del nro de orden para medicamento de control en root_000051
 ************************************************************************************************/
function actualizarConsecutivo( $conex, $wemp_pmla ){
	
	$cons = consultarAliasPorAplicacion( $conex, $wemp_pmla, "consecutivosOrdenesControl" );
	$cons++;
	
	// Actualizo el consecutivo
	$sql = "UPDATE root_000051
			   SET Detval = Detval+1
			 WHERE Detemp = '".$wemp_pmla."'
			   AND Detapl = 'consecutivosOrdenesControl'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	return $cons;
}

/************************************************************************************************************
 * Consulta el diagnostico desde HCE
 ************************************************************************************************************/
function consultarDatosTablaHCE( $conex, $wemp_pmla, $whce, $his, $ing ){

	return consultarUltimoDiagnosticoHCE( $conex, $wemp_pmla, $whce, $his, $ing );
	
	// $val = "";

	// $camposRoot = consultarAliasPorAplicacion( $conex, $wemp_pmla, "dxsHce" );

	// if( !empty( $camposRoot ) ){
		
		// $campos = explode( ",", $camposRoot );
		
		// for( $i = 0; $i < count( $campos ); $i++ ){
		
			// list( $tabla, $cmp ) = explode( "-", $campos[$i] );
			
			// if( $i > 0 ){
				// $sql .= " UNION ";
			// }
			
			// $sql .= "SELECT
						// *
					// FROM
						// {$whce}_{$tabla}
					// WHERE
						// movhis = '$his'
						// AND moving = '$ing'
						// AND movcon = '$cmp'
					// ";
		// }
		
		// $sql .= " ORDER BY fecha_data DESC, hora_data DESC";
		
		// $res = mysql_query( $sql , $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		// // for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		// $i = 0;
		// if( $rows = mysql_fetch_array( $res ) ){
			
			// //Se hace de está manera por que el campo dxs de HCE puede ser tipo tabla
			// //El tipo tabla es un campo SELECT de HTML con selección multiple.
			// //Por tanto en el campo movdat puede haber varios options
			// //En el caso de que el campo sea tipo seleccion solo hay un option
			// $str = explode( "<option", trim( $rows[ 'movdat' ] ) );
			// foreach( $str as $valor )
			// {
				// if( !empty($valor) ){
					// if( $i == 0 ){
						// $val .= trim( strip_tags( trim( "<option".$valor ) ) );
					// }
					// else{
						// $val .= "\n".trim( strip_tags( trim( "<option".$valor ) ) );
					// }
					// break;
				// }
			// }
			
			// // if( trim( strip_tags( trim( $rows[ 'movdat' ] ) ) ) != '' ){
				// // // echo "<br>".trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// // if( $i == 0 ){
					// // $val .= trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// // }
				// // else{
					// // $val .= "\n".trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// // }
			// // }
		// }
	// }
	
	// return $val;
}
 
/****************************************************************************************************
 * Calcula la edad del paciente de acuerdo a la fecha de nacimiento
 ****************************************************************************************************/
function calcularEdad( $fecNac, $fecFin = '' ){
	
	if( empty( $fecFin ) )
		$fecFin = date( "Y-m-d" );
	
	list( $anno, $mes, $dia ) = explode( "-", $fecFin );

	$ann=(integer)substr($fecNac,0,4)*360 +(integer)substr($fecNac,5,2)*30 + (integer)substr($fecNac,8,2);
	// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$aa=(integer)$anno*360 +(integer)$mes*30 + (integer)$dia;
	$ann1=($aa - $ann)/360;
	$meses=(($aa - $ann) % 360)/30;
	if ($ann1<1){
		$dias1=(($aa - $ann) % 360) % 30;
		$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
	}
	else {
		$dias1=(($aa - $ann) % 360) % 30;
		//$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
		$wedad=(string)(integer)$ann1." a&ntilde;o(s) ";
	}
	
	$wedad = $wedad < 0 ? 0: $wedad;
	
	return $wedad;
}

/************************************************************************************************************************
 * Consulta algunos datos demográficos de la tabla de ingeso de pacientes (cliame_000100)
 ************************************************************************************************************************/ 
function datosDemograficosPorIngreso( $conex, $wbasedato, $wcliame, $historia, $ingreso ){
	 
	$val = false;
	
	// Busca la información demográfica del paciente (Departamento, ciudad, sexo, direccion, telefono)
	$sql = "SELECT Pactel, Paciu, b.Nombre as Ciudad, Pacdep, c.Descripcion as Departamento, Pacdir, Pacsex, Pacfna
			  FROM {$wcliame}_000100 a, root_000002 c, root_000006 b
			 WHERE pachis = '".$historia."'
			   AND b.codigo = paciu
			   AND c.codigo = Pacdep; ";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows($res);
	
	if($num > 0){
		
		$row = mysql_fetch_array($res);
		
		$val = array(
			'telefono' 			 => $row['Pactel'],
			'codigoCiudad' 		 => $row['Paciu'],
			'ciudad' 			 => $row['Ciudad'],
			'codigoDepartamento' => $row['Pacdep'],
			'departamento'		 => $row['Departamento'],
			'direccion'			 => $row['Pacdir'],
			'sexo'				 => $row['Pacsex'],
			'descripcionSexo'	 => $row['Pacsex'] == 'M' ? 'Masculino': 'Femenino',
			'fechaNacimiento'	 => $row['Pacfna'],
		);
	}
	
	return $val;
}
 
 
 function traer_observacion($wbasedato, $wemp_pmla, $whis, $wing, $wido, $cod_art, $fecha_generacion ){
 
	global $conex;
	
	$wobservacion = "";
	
	//Si el codigo del articulo no esta en el maestro de articulos entonces es posible que sea una dosis adapatada, si es asi devolvera la observacion para el articulo
	//desde la tabla movhos_000054.
	$sql = "SELECT Artcod
			  FROM {$wbasedato}_000026
			 WHERE Artcod = '$cod_art'";			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows($res);
	
	if($num == 0){
	
		$sqlo = "SELECT Kadobs
			       FROM {$wbasedato}_000054
			      WHERE kadhis = '$whis'
			        AND kading = '$wing'			
			        AND kadart = '$cod_art'			
			        AND kadfec = '$fecha_generacion'";			
		$reso = mysql_query( $sqlo, $conex ) or die( mysql_errno()." - Error en el query $sqlo - ".mysql_error() );
		$numo = mysql_num_rows($reso);
		
		if($numo > 0){
			
			$rowo = mysql_fetch_array($reso);
			$wobservacion = strip_tags( substr( $rowo['Kadobs'], 0, strpos($rowo['Kadobs'], "<span" ) ) );
		}
	
	}
	
	return $wobservacion;
	
 }
 
 
 //-----------------------------------------------------------------------------
 
function dimensionesImagen($idemed)
{
	global $altoimagen;
	global $anchoimagen;

	// Obtengo las propiedades de la imagen, ancho y alto
	@list($widthimg, $heightimg) = getimagesize('../../images/medical/hce/Firmas/'.$idemed.'.png');
	
	$altoimagen = '70';
	
	@$anchoimagen = (70 * $widthimg) / $heightimg;
	
	if($anchoimagen<136)
		$anchoimagen = 136;
}
 /****************************************************************************************************
 * Consulta la forma farmaceutica segun el codigo
 ****************************************************************************************************/
function consultarFormaFarmaceutica( $conex, $wbasedato, $codigo ){
	
	$val = "";
	
	$sql = "SELECT 
				Ffanom
			FROM
				{$wbasedato}_000046
			WHERE
				Ffacod = '$codigo'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows  = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Ffanom' ];
	}
	
	return $val;
}
 
/****************************************************************************************************
 * Cambia el estado de la impresion
 ****************************************************************************************************/
function cambiarEsadoImpresionPorId( $conex, $wbasedato, $id, $usuario ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000133
			   SET Ctrimp = 'on',
				   Ctruim = '$usuario',
				   Ctrfim = '".date( "Y-m-d" )."',
				   Ctrhim = '".date( "H:i:s" )."'
			 WHERE id = '$id'";	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows( ) > 0 ){
		$val = true;
	}
	
	return $val;
}
 
/**********************************************************************
 * Consulta la informacion de un medico
 **********************************************************************/
function consultarInformacionMedico( $conex, $wbasedato, $codigo ){

	$val = false;

	$sql = "SELECT *
			  FROM {$wbasedato}_000048
			 WHERE Meduma = '$codigo'
			   AND Medest = 'on'
			   AND Meduma != ''";	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	
	return $val;
}
 
/****************************************************************************************************************
 * Cambia el estado de la impresion segun el parametro estado
 ****************************************************************************************************************/
function cambiarEstadoImpresion( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $codMedico, $estado ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000133
			SET
				Ctrimp = '$estado'
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrmed = '$codMedico'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}


/****************************************************************************************************************
 * Cambia el estado del registro segun el campo estado. on para activarlo, off para desactivarlo
 ****************************************************************************************************************/
function cambiarEstadoRegistro( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $codMedico, $estado ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000133
			SET
				Ctrest = '$estado'
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrmed = '$codMedico'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}


/****************************************************************************************************************
 * Inserta un registro en la tabla
 ****************************************************************************************************************/
function insertarArticulos( $conex, $wbasedato, $historia, $ingreso, $articulo, $frecuencia, $cantidad, $idOriginal, $codMedico, $dosisMaxima, $diagnostico, $consecutivo, $servcioActual, $habitacionActual, $fecha, $hora ){

	$val = false;

	// $fecha1 = date( "Y-m-d" );
	// $hora1 = date( "H:i:s" );

	$sql = "INSERT INTO 
				{$wbasedato}_000133(     Medico  , Fecha_data , Hora_data ,    Ctrhis  ,   Ctring  ,   Ctrart   ,    Ctrper    ,    Ctrcan  ,    Ctrido    ,   Ctrmed    , Ctrfge  , Ctrhge ,    Ctrfim   ,   Ctrhim  , Ctruim , Ctrimp , Ctrest ,    Ctrdma     ,     Ctrdia    ,     Ctrcon    ,     Ctrsac      ,     Ctrhac         ,    Seguridad    )
							VALUES ( '$wbasedato', '$fecha'   ,  '$hora'  , '$historia', '$ingreso', '$articulo', '$frecuencia', '$cantidad', '$idOriginal', '$codMedico', '$fecha', '$hora', '0000-00-00', '00:00:00',    ''  ,  'off' ,  'on'  , '$dosisMaxima', '$diagnostico', '$consecutivo', '$servcioActual', '$habitacionActual','C-0104686' )";
							  
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}


/****************************************************************************************************************
 * Busca si ya fue insertado un registro en la base de datos, y de ser así devuelve el registro completo
 ****************************************************************************************************************/
function buscarSiExisteRegistro( $conex, $wbasedato, $historia, $ingreso, $articulo, $frecuencia, $cantidad, $idOriginal, $fecha, $estado = 'on' ){

	$val = false;

	$sql = "SELECT 
				* 
			FROM 
				{$wbasedato}_000133
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrest = '$estado'
				AND Ctrfge = '$fecha'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	
	return $val;
}


/********************************************************************************************************
 * Esta función recorre todos los articulos de control para un paciente y si es de control
 ********************************************************************************************************/
function generarArticulosAImprimir( $conex, $wbasedato, $wcenmez, $whce, $wcliame, $historia, $ingreso, $fechaKardex, $codMedico, $dx = '' ){
	
	global $wemp_pmla;
	
	$val = false;
				
	//Busco los articulos de control que sean del paciente
	//los articulos de control son aquellos que estan en el grupo CTR
	//del maestro de articulos			
	$sql = "SELECT Kadart,Kadper,Kadcdi,Kadido,Kadsad, Kadlev,Kadcnd,Kadusu,Kaddma, Ekxayu, Kadess, Kadcma, Kadcfr, Kadfin, Kadhin, Kadfec
			  FROM {$wbasedato}_000054 a 
	     LEFT JOIN {$wbasedato}_000208 c 
	            ON ekxhis = kadhis 
	           AND ekxing = kading 
	           AND ekxfec = kadfec 
	           AND ekxart = kadart 
	           AND ekxido = kadido
	    INNER JOIN {$wbasedato}_000114 b
			    ON famcod = kadctr
			 WHERE kadhis = '$historia'
			   AND kading = '$ingreso'
		       AND kadfec = '$fechaKardex'
			   AND kadest = 'on'
			   AND famctr = 'on'
			   AND kadctr != ''
			   AND ( ( ( ekxart IS NULL OR ekxayu = '' ) OR kadess = 'off' ) OR ( ekxayu != '' AND kadess = 'on' ) )
			 UNION
		    SELECT Kadart,Kadper,Kadcdi,Kadido,Kadsad, Kadlev,Kadcnd,Kadusu,Kaddma, Ekxayu, Kadess, Kadcma, Kadcfr, Kadfin, Kadhin, Kadfec
			  FROM {$wbasedato}_000054 a
		 LEFT JOIN {$wbasedato}_000208 d 
	            ON ekxhis = kadhis 
	           AND ekxing = kading 
	           AND ekxfec = kadfec 
	           AND ekxart = kadart 
	           AND ekxido = kadido
		INNER JOIN {$wbasedato}_000115 c
			    ON Relart = Kadart
		INNER JOIN {$wbasedato}_000114 b
			    ON famcod = Relfam
			 WHERE kadhis = '$historia'
			   AND kading = '$ingreso'
			   AND kadfec = '$fechaKardex'
			   AND famctr = 'on'
			   AND kadest = 'on'				
			   AND TRIM(kadctr) = ''
			   AND ( ( ( ekxart IS NULL OR ekxayu = '' ) OR kadess = 'off' ) OR ( ekxayu != '' AND kadess = 'on' ) )
			 UNION
			SELECT Kadart,Kadper,Kadcdi,Kadido,Kadsad, Kadlev,Kadcnd,Kadusu,Kaddma, '' as Ekxayu, Kadess, Kadcma, Kadcfr, Kadfin, Kadhin, Kadfec
			 FROM {$wbasedato}_000054 a, 
			 	  {$wcenmez}_000002 b, 
				  {$wcenmez}_000001 c, 
				  {$wcenmez}_000003 d, 
				  {$wcenmez}_000009 e, 
				  {$wbasedato}_000026 f, 
				  {$wbasedato}_000114 g
			WHERE kadhis = '$historia'
			  AND kading = '$ingreso'
			  AND kadfec = '$fechaKardex'
			  AND kadest = 'on'
			  AND kadess = 'off'
			  AND kadart = b.artcod
			  AND b.artcod = pdepro
			  AND pdeins = appcod
			  AND apppre = f.artcod
			  AND appest = 'on'
			  AND pdeest = 'on'
			  AND b.arttip = tipcod
			  AND tipcdo != 'on'
			  AND kadctr = famcod
			  AND famctr = 'on'
			UNION
		   SELECT Kadart,Kadper,Kadcdi,Kadido,Kadsad, Kadlev,Kadcnd,Kadusu,Kaddma, Ekxayu, Kadess, Kadcma, Kadcfr, Kadfin, Kadhin, Kadfec
			 FROM {$wbasedato}_000060 a 
	     LEFT JOIN {$wbasedato}_000209 c 
	            ON ekxhis = kadhis 
	           AND ekxing = kading 
	           AND ekxfec = kadfec 
	           AND ekxart = kadart 
	           AND ekxido = kadido
	    INNER JOIN {$wbasedato}_000114 b
			    ON famcod = kadctr
			 WHERE kadhis = '$historia'
			   AND kading = '$ingreso'
		       AND kadfec = '$fechaKardex'
			   AND kadest = 'on'
			   AND famctr = 'on'
			   AND kadctr != ''
			   AND ( ( ( ekxart IS NULL OR ekxayu = '' ) OR kadess = 'off' ) OR ( ekxayu != '' AND kadess = 'on' ) )
			 UNION
		    SELECT Kadart,Kadper,Kadcdi,Kadido,Kadsad, Kadlev,Kadcnd,Kadusu,Kaddma, Ekxayu, Kadess, Kadcma, Kadcfr, Kadfin, Kadhin, Kadfec
			  FROM {$wbasedato}_000060 a
		 LEFT JOIN {$wbasedato}_000209 d 
	            ON ekxhis = kadhis 
	           AND ekxing = kading 
	           AND ekxfec = kadfec 
	           AND ekxart = kadart 
	           AND ekxido = kadido
		INNER JOIN {$wbasedato}_000115 c
			    ON Relart = Kadart
		INNER JOIN {$wbasedato}_000114 b
			    ON famcod = Relfam
			 WHERE kadhis = '$historia'
			   AND kading = '$ingreso'
			   AND kadfec = '$fechaKardex'
			   AND famctr = 'on'
			   AND kadest = 'on'				
			   AND TRIM(kadctr) = ''
			   AND ( ( ( ekxart IS NULL OR ekxayu = '' ) OR kadess = 'off' ) OR ( ekxayu != '' AND kadess = 'on' ) )
			 UNION
			SELECT Kadart,Kadper,Kadcdi,Kadido,Kadsad, Kadlev,Kadcnd,Kadusu,Kaddma, '' as Ekxayu, Kadess, Kadcma, Kadcfr, Kadfin, Kadhin, Kadfec
			 FROM 
				{$wbasedato}_000060 a, 
				{$wcenmez}_000002 b, 
				{$wcenmez}_000001 c, 
				{$wcenmez}_000003 d, 
				{$wcenmez}_000009 e, 
				{$wbasedato}_000026 f, 
				{$wbasedato}_000114 g
			WHERE
				kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadfec = '$fechaKardex'
				AND kadest = 'on'
				AND kadess = 'off'
				AND kadart = b.artcod
				AND b.artcod = pdepro
				AND pdeins = appcod
				AND apppre = f.artcod
				AND appest = 'on'
				AND pdeest = 'on'
				AND b.arttip = tipcod
				AND tipcdo != 'on'
				AND kadctr = famcod
				AND famctr = 'on'
				";

	$resArticulos = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	$diagnostico = trim( consultarDatosTablaHCE( $conex, $wemp_pmla, $whce, $historia, $ingreso ) );
	
	if( empty( $diagnostico ) )
		$diagnostico = $dx;
	
	list( $diagnostico ) = explode( "\n", $diagnostico );
	
	//actualizo el dx solo para aquellos pacientes cuyo dx se encuentren vacios
	//Eso se hace por qué el médico ordena medicamentos sin haber diligenciado el formulario de ingreso de HCE y los dx quedan vacios
	if( !empty( $diagnostico ) )
		actualizarDxHCE( $conex, $wbasedato, $historia, $ingreso, $diagnostico );
	
	//Consulto datos demograficos faltantes (Direccion, sexo, ciudad, departamento, telefono )
	$datosDemograficos 	= datosDemograficosPorIngreso( $conex, $wbasedato, $wcliame, $historia, $ingreso );
	$telefono 			= $datosDemograficos['telefono'];
	$direccion 			= $datosDemograficos['direccion'];
	
	$ubicacionPaciente 	= consultarUbicacionPacientePorHistoriaIngreso( $conex, $wbasedato, $historia, $ingreso, $fechaKardex );
	$servcioActual		= $ubicacionPaciente[ 'servicioActual' ];
	$habitacionActual	= $ubicacionPaciente[ 'habitacionActual' ];
	
	//Código por defecto de una condición por INFUSION CONITUNA
	$condicionIC = consultarAliasPorAplicacion( $conex, $wemp_pmla, "condicionIC" );
	
	for( $i = 0; $rowsArticulos = mysql_fetch_array( $resArticulos ); $i++ ){
		
		$insertar = false;
		
		//Debo buscar si ya fue generado el articulo
		$existe = buscarSiExisteRegistro( $conex, $wbasedato, $historia, $ingreso, $rowsArticulos[ 'Kadart' ], $rowsArticulos[ 'Kadper' ], $rowsArticulos[ 'Kadcdi' ], $rowsArticulos[ 'Kadido' ], $fechaKardex, 'on' );
		
		if( trim( $rowsArticulos[ 'Ekxayu' ] ) != '' && strtolower( $rowsArticulos[ 'Kadess' ] ) == 'on' ){
			
			list( $fechorIniunix ) = explode( ":", $rowsArticulos['Kadhin'] );
			
			$diasDiff = ( strtotime( $rowsArticulos['Kadfin'] ) - strtotime( $fechaKardex ) )/(24*3600);
			
			$fechorIniunix = $fechorIniunix*1+24*$diasDiff;
			
			$per = consultarFrecuenciaPorHoraImpControl( $conex, $wbasedato, $rowsArticulos[ 'Kadper' ] );
			
			$ini = 0;
			$fin = 22; 
			
			$canApl = 0;
			if( $per > 0 ){
			
				for( $iii = $fechorIniunix*1; $iii <= $fin ; $iii+=$per ){
					if( $iii >= $ini && $iii <= $fin ){
						$canApl++;
					}
				}
			}
			
			if( trim( $rowsArticulos[ 'Kaddma' ] ) != '' )
				$canApl = min( $canApl, $rowsArticulos[ 'Kaddma' ]*1 );
			
			$rowsArticulos[ 'Kadcdi' ] = ceil( $rowsArticulos[ 'Kadcfr' ]/$rowsArticulos[ 'Kadcma' ]*$canApl );
		} 
		
		$cantidad = $rowsArticulos[ 'Kadcdi' ];
		
		if( $cantidad == 0 ){
			
			$per = consultarFrecuenciaPorHoraImpControl( $conex, $wbasedato, $rowsArticulos[ 'Kadper' ] );
			
			$cantidad = ceil( 24/$per*$rowsArticulos[ 'Kadcfr' ]/$rowsArticulos[ 'Kadcma' ] );
		}
		
		$canPorDefecto = consultarCantidadDefecto( $conex, $wbasedato, $rowsArticulos['Kadart'] );
		//Es de infusion continua si tiene el campo Kadlev en on
		if( !empty( $canPorDefecto ) && $canPorDefecto > 0 && strtolower( $rowsArticulos['Kadlev'] ) == 'on' && $condicionIC == $rowsArticulos[ 'Kadcnd' ] && $cantidad < $canPorDefecto){
			$cantidad = $canPorDefecto;
		}
		
		if( $cantidad > 0 ){
		
			if( empty( $existe ) ){ //Si no existe
				
				$val = true;
				$insertar = true;
			}
			else{
				
				//Si existe verifico si ha cambiado en algo la cantidad a pedir o la frecuencia
				//si uno de los ha cambiado hay que desactivar el registro y crear uno nuevo con las
				//cantidades correctas
				// if( $rowsArticulos[ 'Kadper' ] != $existe[ 'Ctrper' ] || $cantidad !=  $existe[ 'Ctrcan' ] ){
					
					// //desactivo el registro
					// $desactivado = cambiarEstadoRegistro( $conex, $wbasedato, $historia, $ingreso, $rowsArticulos[ 'Kadart' ], $rowsArticulos[ 'Kadido' ], $existe[ 'Ctrmed' ], 'off' );
					
					// if( $desactivado ){
					
						// //Inserto el registro
						// // insertarArticulos( $conex, $wbasedato, $historia, $ingreso, $rowsArticulos[ 'Kadart' ], $rowsArticulos[ 'Kadper' ], $cantidad, $rowsArticulos[ 'Kadido' ], $rowsArticulos[ 'Kadusu' ], $rowsArticulos[ 'Kaddma' ], $diagnostico, $consecutivo );
						
						// $val = true;
						// $insertar = true;
					// }
				// }
			}
			
			if( $insertar ){
				
				//Creo el encabezado si no existe
				$conEncabezado = crearEncabezado( $conex, $wbasedato, $historia, $ingreso, $direccion, $telefono );
				
				//Si existe encabezado inserto el detalle
				if( $conEncabezado ){
					
					$consecutivo = actualizarConsecutivo( $conex, $wemp_pmla );
					
					// insertarArticulos( $conex, $wbasedato, $historia, $ingreso, $rowsArticulos[ 'Kadart' ], $rowsArticulos[ 'Kadper' ], $cantidad, $rowsArticulos[ 'Kadido' ], $rowsArticulos[ 'Kadusu' ], $rowsArticulos[ 'Kaddma' ], $diagnostico, $consecutivo );
					insertarArticulos( $conex, $wbasedato, $historia, $ingreso, $rowsArticulos[ 'Kadart' ], $rowsArticulos[ 'Kadper' ], $cantidad, $rowsArticulos[ 'Kadido' ], $rowsArticulos[ 'Kadusu' ], $rowsArticulos[ 'Kaddma' ], $diagnostico, $consecutivo, $servcioActual, $habitacionActual, $fechaKardex, date( "H:i:s" ) );
				}
			}
		}
	}
	
	return $val;
}

/****************************************************************************************************************
 * 												FIN DE FUNCIONES
 ****************************************************************************************************************/



include_once("root/comun.php");
// include_once("movhos/otros.php");



$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));
  
$wuser1=explode("-",$user);
$wusuario=trim($wuser1[1]);

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
$whce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );

//
if( !isset( $editable ) ){
	$editable = 'on';
}

$consultaAjax = 11;

if( empty( $fecIni ) || empty($fecFin) ){
	exit( "Debe ingresar Fecha inicial (fecIni) y Fecha final (fecFin) " );
}

if( $consultaAjax ){	//si hay ajax

	switch( $consultaAjax ){
	
		case '10':
			$generoArts = generarArticulosAImprimir( $conex, $wbasedato, $wcenmez, $whce, $wcliame, $historia, $ingreso, $fechaKardex, $wusuario, $dxCIE10 );
			break;
			
		case '11':
		
			$arDxs = array();
		
			// connectOdbc( &$conex_o, 'inventarios' );
			$conex_o = odbc_connect( "admisiones",'','');
		
			echo "Proceso Iniciado: ".date( "Y-m-d H:i:s" );
			// Actualizando consecutivo anterior al actual
			// $sql = "UPDATE ".$wbasedato."_000133
					   // SET Ctrcoa = Ctrcon";
			
			// $rescoa = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
			$fechaInicial 	= strtotime( $fecIni );
			$fechaFinal 	= strtotime( $fecFin );
			
			for( $i = $fechaInicial; $i <= $fechaFinal; $i += 24*3600 ){
				
				$fechaKardex = date( "Y-m-d", $i );
				
				$sql = "SELECT Kadhis, Kading
						  FROM ".$wbasedato."_000054, root_000037
						 WHERE kadfec = '".$fechaKardex."'
						   AND orihis = kadhis
						   AND oriori = '".$wemp_pmla."'
					GROUP BY 1, 2 ";
				
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				
				// $kkk = 0;
				$kkk+= mysql_num_rows( $res );
				while( $rows = mysql_fetch_array( $res ) ){
					
					$historia = $rows[ 'Kadhis' ];
					$ingreso  = $rows[ 'Kading' ];
					
					if( $fechaKardex > '2017-12-05' ){
						
						$sql = "SELECT a.Fecha_data, a.Hora_data, Diacod, Descripcion
								  FROM ".$wbasedato."_000243 a, root_000011 b
								 WHERE Diahis 		 = '".$historia."' 
								   AND Diaing 		 = '".$ingreso."'
								   AND a.fecha_data 	<= '".$fechaKardex."'
								   AND Codigo 		= Diacod
							  ORDER BY a.Fecha_data desc, a.Hora_data desc
								 ";
						
						$resdx = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						
						$dxCIE10 = "";
						if( $rowsdx = mysql_fetch_array( $resdx ) ){
							$dxCIE10 = $rowsdx[ 'Diacod' ]."-".$rowsdx[ 'Descripcion' ];
						}
						
						if( empty($dxCIE10) ){
							
							$sql = "SELECT a.Fecha_data, a.Hora_data, Ctrdia
									  FROM ".$wbasedato."_000133 a
									 WHERE Ctrhis 		 = '".$historia."' 
									   AND Ctring 		 = '".$ingreso."'
									   AND a.fecha_data <= '".$fechaKardex."'
									   AND Ctrdia		!= ''
								  ORDER BY a.Fecha_data desc, a.Hora_data desc
									 ";
							
							$resdx = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
							
							$dxCIE10 = "";
							if( $rowsdx = mysql_fetch_array( $resdx ) ){
								$dxCIE10 = $rowsdx[ 'Ctrdia' ];
							}
							
							if( empty($dxCIE10) ){
						
								$query= "SELECT mdiadia, dianom 
										   FROM inmdia, india 
										  WHERE mdiahis = '".$historia."' 
											AND mdianum = '".$ingreso."' 
											AND mdiadia = diacod
											AND mdiatip = 'P' ";

								$err_o= odbc_do( $conex_o, $query ) or die ( "Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() );
								if(odbc_fetch_row($err_o) )
								{								
									$dxCIE10 = trim( odbc_result( $err_o, 1 ) )."-".trim( odbc_result( $err_o, 2 ) );
								}
							}
						}						
					}
					else{
						
						// $sql = "SELECT a.Fecha_data, a.Hora_data, Diacod, Descripcion
								  // FROM ".$wcliame."_000109 a, root_000011 b
								 // WHERE Diahis 		 = '".$historia."' 
								   // AND Diaing 		 = '".$ingreso."'
								   // AND Diatip 		 = 'P'
								   // AND Codigo 		 = Diacod
								 // ";
						
						// $resdx = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						
						// $dxCIE10 = "";
						// if( $rowsdx = mysql_fetch_array( $resdx ) ){
							// $dxCIE10 = $rowsdx[ 'Diacod' ]."-".$rowsdx[ 'Descripcion' ];
						// }
						
						if( !isset($arDxs[ $historia."-".$ingreso ]) ){
							
							$query= "SELECT mdiadia, dianom 
									   FROM inmdia, india 
									  WHERE mdiahis = '".$historia."' 
										AND mdianum = '".$ingreso."' 
										AND mdiadia = diacod
										AND mdiatip = 'P' ";

							$err_o= odbc_do( $conex_o, $query ) or die ( "Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error() );
							if(odbc_fetch_row($err_o) )
							{								
								$arDxs[ $historia."-".$ingreso ] = trim( odbc_result( $err_o, 1 ) )."-".trim( odbc_result( $err_o, 2 ) );
							}
							
						}
						
						$dxCIE10 = $arDxs[ $historia."-".$ingreso ];
					}
					
					$generoArts = generarArticulosAImprimir( $conex, $wbasedato, $wcenmez, $whce, $wcliame, $historia, $ingreso, $fechaKardex, $wusuario, $dxCIE10 );
				}
			}
			
			
			// for( $i = $fechaInicial; $i <= $fechaFinal; $i += 24*3600 ){
				
				$fechaKardex = date( "Y-m-d", $i );
				
				$sql = "SELECT *
						  FROM ".$wbasedato."_000133
						 WHERE Ctrfge = '".date( "Y-m-d", strtotime( $fecIni." 00:00:00" )-24*3600 )."'
					  ORDER BY Ctrcon*1 DESC
						";
				
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$rows = mysql_fetch_array( $res );
				$i =  $rows[ 'Ctrcon' ]*1+1;	//El menor consecutivo a partir de la fecha de inicio

				$sql = "SELECT *
						  FROM ".$wbasedato."_000133
						 WHERE Ctrfge >= '".$fecIni."'
						   AND Ctrfge <= '".$fecFin."'
					  ORDER BY Ctrfge asc, Ctrhge asc
						";
				
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

				for( ; $rows = mysql_fetch_array( $res ); $i++ ){
					
					$sql = "UPDATE ".$wbasedato."_000133
					           SET Ctrcon = '".$i."'
							 WHERE id = ".$rows['id'].".
						  ORDER BY Ctrfge asc, Ctrhge asc, id asc
							";
					
					$resupt = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				}
				
				// if( true ){
					// $sql = "UPDATE root_000051
							   // SET Detval = '".$i."'
							 // WHERE Detemp = '".$wemp_pmla."'
							   // AND Detapl = 'consecutivosOrdenesControl'
							// ";
								
					// $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				// }
			// }
			
			echo "<br>Proceso finalizado: ".date( "Y-m-d H:i:s" );
			
			break;
			
		default: break;
	}
	
	if( $generoArts ){
		?>
		<script>
			//alert( "Se generaron formatos de impresion para articulos de control" );
		</script>
		<?php
	}
	
	?>		
		<script>
			window.parent.cerrarModal();
		</script>
	<?php
}
else{	//si no hay ajax

	?>
	<style>
		
		body{
			height:auto;
		}
	<?php
	if( isset($imprimir) ){
	?>
		td {
			font-size: 9pt;
		}
	
	<?php
	}
	echo "</style>";
	
	if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");
	
	include_once("root/montoescrito.php");

	$institucion = consultarInstitucionPorCodigo($conex,$wemp_pmla);
	
	echo "<form id='medcontrol'>";
	
	echo "<input type='hidden' name='editable' id='editable' value='".$editable."'>";
	
	if( !isset($imprimir) ){
	
		$wactualiz = "Diciembre 13 de 2016";
		
		encabezado("IMPRESION MEDICAMENTOS DE CONTROL",$wactualiz, "clinica");
	
		$sql = "(SELECT
					Artcod, Artgen, Artcom, Artfar, Perequ, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*, a.id as ctcid
				FROM
					{$wbasedato}_000222 h, {$wbasedato}_000133 a, {$wbasedato}_000026 b, {$wbasedato}_000043 c, {$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g
				WHERE Ecthis = Ctrhis
					AND Ecting = Ctring
					AND ctrimp = 'off'
					AND ctrest = 'on'
					AND artcod = ctrart
					AND percod = ctrper
					AND ubihis = ctrhis
					AND ubiing = ctring
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla')
				UNION
				(SELECT
					Artcod, Artgen, Artcom, '' as Artfar, Perequ, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*, a.id as ctcid
				FROM
					{$wbasedato}_000222 h, {$wbasedato}_000133 a, cenpro_000002 b, {$wbasedato}_000043 c, {$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g
				WHERE Ecthis = Ctrhis
					AND Ecting = Ctring
					AND ctrimp = 'off'
					AND ctrest = 'on'
					AND artcod = ctrart
					AND percod = ctrper
					AND ubihis = ctrhis
					AND ubiing = ctring
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla' )
				ORDER BY
					ubisac, ubihac
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );	
				
		
		if( $num > 0 ){		
			
			
			echo "<br>";
			echo "<center>";
			echo "<table>";
			echo "<tr>";
			echo "<td class=encabezadotabla>Buscar</td>";		
			echo "<td class=encabezadotabla><input id='id_search' type='text' value='' name='search' onkeypress='return pulsar(event);'></td>";			
			echo "<td><img width='auto' width='15' height='15' border='0' onclick='limpiarbusqueda();' title='Reiniciar Búsqueda' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></td>";
			echo "</tr>";
			echo "</table>";
			echo "</center>";
			echo "<br>";
		
			$ccoAnt = '';
		
			$total = 0;
			$totalAImprimir = 0;
		
		
			$rows = mysql_fetch_array( $res );
			$ccoAnt = $rows[ 'Ubisac' ];
			
			for( $i = 0; ;  ){
				
				if( $ccoAnt == $rows[ 'Ubisac' ] ){
				
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'hab' ] = $rows[ 'Ubihac' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'nom' ] = $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'tot' ]++;
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'his' ] = $rows[ 'Ctrhis' ];
					
					$descripcionArticulo = consultarDescripcionArticulo( $conex, $wbasedato, $wcenmez, $rows['Artcod'], $rows[ 'Ctrhis' ], $rows[ 'Ctring' ], $rows[ 'Ctrido' ], $rows[ 'Ctrfge' ] );
					
					if( empty($descripcionArticulo ) )
						$descripcionArticulo = $rows[ 'Artgen' ];
					else
						$descripcionArticulo = $descripcionArticulo[ 'Artgen' ];
					
					
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'med' ][$rows[ 'ctcid' ]] = $descripcionArticulo." (".$rows[ 'Ctrfge' ].")";
				
					$cconom = $rows[ 'Cconom' ];
					$total++;
					$totalAImprimir++;
					$rows = mysql_fetch_array( $res );
				}
				elseif( $total > 0 ){	
				
					echo "<div id='accordion' class='desplegable'>";
					
					echo "<h3>$cconom</h3>";					
					
					$total = 0;
					$ccoAnt = $rows[ 'Ubisac' ];
					$i++;
					
					//creo una fila mas con la información del paciente que se quiere imprimir
					if( true ){
						
						echo "<div>";
					
						echo "<table align='center'>";
						
						echo "<tr class='encabezadotabla' align='center'>";
						echo "<td>Habitaci&oacute;n</td>";
						echo "<td>Historia</td>";
						echo "<td>Nombre</td>";
						echo "<td>Medicamentos</td>";
						echo "<td>Cantidad</td>";
						
						
						echo "</tr>";
						
						$k = 0;
						$j = 0;
						
						foreach( $pacientes as $keyPacientes => $hisPacientes ){
							
							$class2 = "fila".($k%2+1)."";
						
							echo "<tr class='$class2'>";
							
							echo "<td align='center'>";
							echo $hisPacientes[ 'hab' ];
							echo "</td>";
							
							echo "<td align='center'>";
							echo $keyPacientes;
							echo "</td>";
							
							echo "<td>";
							echo $hisPacientes[ 'nom' ];
							echo "</td>";
							
							echo "<td align='center'>";
							echo "<table>";
									foreach($hisPacientes[ 'med' ] as $key => $value){
									$class1 = "fila".($j%2+1)."";
									echo "<tr class='$class1'>";
									echo "<td width='350px'>";
									echo $value;
									echo "</td>";									
									echo "<td align='center'><a href='impresionMedicamentosControl.php?wemp_pmla=$wemp_pmla&imprimir=on&historia={$hisPacientes[ 'his' ]}&id_registro={$key}&editable=$editable' target='_blank'>Imprimir</a></td>";								
									echo "</tr>";									
									$j++;
									}								
								echo "</table>";
							echo "</td>";
							
							
							echo "<td align='center'>";
							echo $hisPacientes[ 'tot' ];
							echo "</td>";
							echo "</tr>";
							
							$k++;
						}
						
						echo "</table>";
						echo "</div>";
					}
					
					$pacientes = "";	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
					
					echo "</div>";
					if( !$rows ){
					
						break;
					}
				}				
			}			 
		}
		
		//SEGMENTO DE MEDICAMENTOS IMPRESOS
		
		if($wfecha_inicial == ""){
			$wfecha_inicial_aux = date('Y-m-d');			
		}else{
			$wfecha_inicial_aux = $wfecha_inicial;
		}
		
		if($wfecha_final == ""){
			$wfecha_final_aux = date('Y-m-d');			
		}else{
			$wfecha_final_aux = $wfecha_final;	
		}
		
		if($wfecha_inicial == ""){
			$wfecha_inicial = date('Y-m-d');
			
		}
		
		if($wfecha_final == ""){
			$wfecha_final = date('Y-m-d');
			
		}
		
		if(isset($tipo_consulta) and $tipo_consulta == 'fgen'){
			switch($tipo_consulta){
				case 'fgen' : $filtro_fecha = " AND Ctrfge BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
				break;
				
				case 'fimp' :  $filtro_fecha = " AND Ctrfim BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
				break;
				}
			
		}else{
		
			$filtro_fecha = " AND Ctrfim BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
		}
		
		$sql_imp = "(SELECT
					Artcod, Artgen, Artcom, Artfar, Perequ, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*, a.id as idctc
				FROM
					{$wbasedato}_000222 h, {$wbasedato}_000133 a, {$wbasedato}_000026 b, {$wbasedato}_000043 c, {$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g
				WHERE Ecthis = Ctrhis
					AND Ecting = Ctring
					AND ctrimp = 'on'
					AND ctrest = 'on'
					AND artcod = ctrart
					AND percod = ctrper
					AND ubihis = ctrhis
					AND ubiing = ctring
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					$filtro_fecha
					AND oriori = '$wemp_pmla')
				UNION
				(SELECT
					Artcod, Artgen, Artcom, '' as Artfar, Perequ, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*, a.id as idctc
				FROM
					{$wbasedato}_000222 h, {$wbasedato}_000133 a, cenpro_000002 b, {$wbasedato}_000043 c, {$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g
				WHERE Ecthis = Ctrhis
					AND Ecting = Ctring
					AND ctrimp = 'on'
					AND ctrest = 'on'
					AND artcod = ctrart
					AND percod = ctrper
					AND ubihis = ctrhis
					AND ubiing = ctring
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					$filtro_fecha
					AND oriori = '$wemp_pmla' )
				ORDER BY
					ubisac, ubihac
				";
				
		$res_imp = mysql_query( $sql_imp, $conex ) or die( mysql_errno()." - Error en el query $sql_imp - ".mysql_error() );
		$num_imp = mysql_num_rows( $res_imp );	
		
		
		echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "<hr>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";
						echo "<td class=encabezadotabla><font size=5><b>MEDICAMENTOS DE CONTROL IMPRESOS</b></font></td>";
						echo "<input type=hidden id='tipo_consulta' name='tipo_consulta' value=''>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
			
			echo "<br>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";										
						echo "<td colspan=2 class=encabezadotabla align=center><b>Buscar:</b></td>";
					echo "</tr>";					
					
					echo "<tr>";
						echo "<td class=fila1 align=left>Fecha inicial:</td><td align=left><input type=text name='wfecha_inicial' id='wfecha_inicial' value='".$wfecha_inicial_aux."'></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td class=fila2 align=left>Fecha final:</td><td align=left><input type=text name='wfecha_final' id='wfecha_final' value='".$wfecha_final_aux."'></td>";						
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan=2 align=center><input type='button' id='btn_consultar_fecha' onclick='consultar_fecha(\"fgen\");' value='Por fecha de generacion'><input type='button' onclick='consultar_fecha(\"fimp\");' value='Por fecha de impresion'></td>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
		
		if( $num_imp > 0 ){	
		
		
			$ccoAnt_imp = '';
		
			$total_imp = 0;
			$totalAImprimir_imp = 0;
		
		
			$rows_imp = mysql_fetch_array( $res_imp );
			$ccoAnt_imp = $rows_imp[ 'Ubisac' ];
			
			for( $i_imp = 0; ;  ){
				
				if( $ccoAnt_imp == $rows_imp[ 'Ubisac' ] ){
				
					@$pacientes_imp[ $rows_imp[ 'Ctrhis' ]."-".$rows_imp[ 'Ctring' ]  ][ 'hab' ] = $rows_imp[ 'Ubihac' ];
					@$pacientes_imp[ $rows_imp[ 'Ctrhis' ]."-".$rows_imp[ 'Ctring' ]  ][ 'nom' ] = $rows_imp[ 'Pacno1' ]." ".$rows_imp[ 'Pacno2' ]." ".$rows_imp[ 'Pacap1' ]." ".$rows_imp[ 'Pacap2' ];;
					@$pacientes_imp[ $rows_imp[ 'Ctrhis' ]."-".$rows_imp[ 'Ctring' ]  ][ 'tot' ]++;
					@$pacientes_imp[ $rows_imp[ 'Ctrhis' ]."-".$rows_imp[ 'Ctring' ]  ][ 'his' ] = $rows_imp[ 'Ctrhis' ];
					
					$descripcionArticulo = consultarDescripcionArticulo( $conex, $wbasedato, $wcenmez, $rows_imp['Artcod'], $rows_imp['Ctrhis'], $rows_imp['Ctring'], $rows_imp['Ctrido'], $rows_imp['Ctrfge'] );
					
					if( empty($descripcionArticulo ) )
						$descripcionArticulo = $rows_imp[ 'Artgen' ];
					else
						$descripcionArticulo = $descripcionArticulo[ 'Artgen' ];
					
					@$pacientes_imp[ $rows_imp[ 'Ctrhis' ]."-".$rows_imp[ 'Ctring' ]  ][ 'med' ][$rows_imp[ 'idctc' ]] = $descripcionArticulo." (".$rows_imp[ 'Ctrfge' ].")";
				
					$cconom_imp = $rows_imp[ 'Cconom' ];
					$total_imp++;
					$totalAImprimir_imp++;
					$rows_imp = mysql_fetch_array( $res_imp );
				}
				elseif( $total_imp > 0 ){	
				
					echo "<div id='accordion' class='desplegable'>";
					
					echo "<h3>$cconom_imp</h3>";					
					
					$total_imp = 0;
					$ccoAnt_imp = $rows_imp[ 'Ubisac' ];
					$i_imp++;
					
					//creo una fila mas con la información del paciente que se quiere imprimir
					if( true ){
						
						echo "<div>";
					
						echo "<table align='center'>";
						
						echo "<tr class='encabezadotabla' align='center'>";
						echo "<td>Habitaci&oacute;n</td>";
						echo "<td>Historia</td>";
						echo "<td>Nombre</td>";
						echo "<td>Medicamentos</td>";
						echo "<td>Cantidad</td>";						
						echo "</tr>";
						
						$k_imp = 0;
						// echo "<pre>";
						// print_r($pacientes_imp);
						// echo "</pre>";
						foreach( $pacientes_imp as $keyPacientes_imp => $hisPacientes_imp ){
							
							$class2 = "fila".($k_imp%2+1)."";
						
							echo "<tr class='$class2'>";
							
							echo "<td align='center'>";
							echo $hisPacientes_imp[ 'hab' ];
							echo "</td>";
							
							echo "<td align='center'>";
							echo $keyPacientes_imp;
							echo "</td>";
							
							echo "<td>";
							echo $hisPacientes_imp[ 'nom' ];
							echo "</td>";
							
							echo "<td align='left'>";
							echo "<table>";
									foreach($hisPacientes_imp[ 'med' ] as $key => $value){
									$class1 = "fila".($j%2+1)."";
									echo "<tr class='$class1'>";
									echo "<td width='350px'>";
									echo $value;
									echo "</td>";
									echo "<td align='center'><a href='impresionMedicamentosControl.php?wemp_pmla=$wemp_pmla&imprimir=on&reimprimir=on&historia={$hisPacientes_imp[ 'his' ]}&id_registro={$key}&editable=$editable' target='_blank'>Imprimir</a></td>";
									echo "</tr>";									
									$j++;
									}								
								echo "</table>";
							echo "</td>";
							
							echo "<td align='center'>";
							echo $hisPacientes_imp[ 'tot' ];
							echo "</td>";
							echo "</tr>";
							
							$k++;
						}
						
						echo "</table>";
						echo "</div>";
					}
					
					$pacientes_imp = "";	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
					
					echo "</div>";
					if( !$rows_imp ){
					
						break;
					}
				}				
			}			 
		}
		
		echo "<INPUT type='hidden' value='$wemp_pmla' name='wemp_pmla' id='wemp_pmla'>";		
		echo "<br>";		
		echo "<table align='center'>";
		echo "<tr>";		
		echo "<td>";
		echo "<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		
	}
	else{
				
		if( empty( $cco ) ){
			$cco = '%';
		}
		if( $editable == 'on' )
			echo "<p align=center><input type='button' class='printer' value='Imprimir'><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";
		
		echo "<div class='areaimprimirMedControl'>";
		$control_impresion = "ctrimp = 'off'";
		
		
			
		$copia = "";
		if($reimprimir=='on' && $editable == 'on' ){
				
			//Log de reimpresion de medicamento de control.
			$sql = "INSERT INTO {$wbasedato}_000165(     Medico   ,        Fecha_data  ,      Hora_data             ,    Impusu  ,           Impori                  , Impest ,  Seguridad    )
												 VALUES (  '$wbasedato', '".date('Y-m-d')."',  '".date('H:i:s')."'  , '$wusuario',    'impresionMedicamentosControl' ,  'on', 'C-$wusuario' )";
						  
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			$control_impresion = "ctrimp = 'on'";
			$copia = "COPIA";
		}
		
		if( $editable != 'on' )
			$control_impresion = "1 ";	//Esto para evitar error en la consulta
		
		//Consulto los articulos a imprimir
		$sql = "SELECT
					Artgen, Artcom, Artfar, Perequ, Ubihac, Ubisac, e.Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*, Ctrmed, i.cconom as nomser
				FROM
					{$wbasedato}_000133 a, {$wbasedato}_000026 b, {$wbasedato}_000043 c, {$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g, {$wbasedato}_000011 i
				WHERE $control_impresion
					AND ctrest = 'on'
					AND a.id = '$id_registro'
					AND artcod = ctrart
					AND percod = ctrper
					AND ubihis = ctrhis
					AND ubiing = ctring
					AND e.ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
					AND ctrhis LIKE '$historia'
					AND ubisac LIKE '$cco'
					AND i.ccocod = Ctrsac
				UNION
				SELECT
					Artgen, Artcom, '' as Artfar, Perequ, Ubihac, Ubisac, e.Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*, Ctrmed, i.cconom as nomser
				FROM
					{$wbasedato}_000133 a, cenpro_000002 b, cenpro_000001 h, {$wbasedato}_000043 c, {$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g, {$wbasedato}_000011 i
				WHERE $control_impresion
					AND ctrest = 'on'
					AND a.id = '$id_registro'
					AND artcod = ctrart
					AND percod = ctrper
					AND ubihis = ctrhis
					AND ubiing = ctring
					AND e.ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
					AND ctrhis LIKE '$historia'
					AND ubisac LIKE '$cco'
					AND b.arttip = tipcod
					AND tipcdo != 'on'
					AND i.ccocod = Ctrsac
				ORDER BY
					ubisac, ubihac
				"; 
				// echo "<pre>$sql</pre>";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		echo "<div style='width:20cm'>";
		
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$encabezado = consultarEncabezado( $conex, $wbasedato, $historia, $rows['Ctring'] );
			
			$rows[ 'Ctrdma' ] = trim( $rows[ 'Ctrdma' ] );
			
			//Consulto datos demograficos faltantes (Direccion, sexo, ciudad, departamento, telefono )
			$datosDemograficos = datosDemograficosPorIngreso( $conex, $wbasedato, $wcliame, $historia, $ingreso );
			
			$codigoCiudad 		= $datosDemograficos['codigoCiudad'];
			$ciudad 			= $datosDemograficos['ciudad'];
			$codigoDepartamento = $datosDemograficos['codigoDepartamento'];
			$departamento 		= $datosDemograficos['departamento'];
			$sexo 				= $datosDemograficos['sexo'];
			$descripcionSexo 	= $datosDemograficos['descripcionSexo'];
			$fechaNacimiento 	= $datosDemograficos['fechaNacimiento'];
			// //Dejo la fecha del registro cómo fecha de nacimiento, por que la edad debe ser la que tenía en el momento es que se creo el registro
			// $fechaNacimiento 	= $rows['Fecha_data'];
			$edad 				= calcularEdad( $fechaNacimiento, $rows['Ctrfge'] );
			
			$telefono 			= $datosDemograficos['telefono'];
			$direccion 			= $datosDemograficos['direccion'];
			if( $encabezado ){
				$telefono 			= $encabezado['telefono'];
				$direccion 			= $encabezado['direccion'];
			}
		
			//Consulto la información del médico
			$infoMedico = consultarInformacionMedico( $conex, $wbasedato, $rows[ 'Ctrmed' ] );
			
			//Calculo el valor en letras
			$cantidadLetras = montoescrito( $rows[ 'Ctrcan' ] );
			
			//Esto es para sacar el valor por que monto escrito es para valores en pesos
			$cantidadLetras = substr( $cantidadLetras, 0, strpos( $cantidadLetras, "PESOS" ) );
			
			dimensionesImagen($rowmed['Meddoc']);
			
			if(file_exists("../../images/medical/hce/Firmas/".$rows[ 'Ctrmed' ].".png"))
				$firmaMedico = "<img src='../../images/medical/hce/Firmas/".$rows[ 'Ctrmed' ].".png' width='$anchoimagen' height='$altoimagen'>";	//$infoMedico[ 'Firma' ]; //*****Aun no se sabe la tabla de firma
			else
				$firmaMedico = "";
			
			
			if( empty( $cantidadLetras ) &&  $rows[ 'Ctrcan' ] == 1 ){
				$cantidadLetras = "UNO";
			}
			
			//Consultar forma farmaceutica
			$formaFarmaceutica = consultarFormaFarmaceutica( $conex, $wbasedato, $rows[ 'Artfar' ] );
			
			//Hago un salgo de linea si se va a imprimir otra factura
			if( $i > 0 ){
				echo "<div style='page-Break-After:always'></div>";
			}
			
			$wobser_med = traer_observacion($wbasedato, $wemp_pmla, $rows['Ctrhis'], $rows['Ctring'], $rows['Ctrido'], $rows['Ctrart'], $rows['Ctrfge'] );
			if( !empty($wobser_med) )
				$wobser_med = "<br>&nbsp;".$wobser_med;
			
			$nroOrden = $rows[ 'Ctrcon' ];
			$diagnostico = $rows[ 'Ctrdia' ];
			
			//Está en dosis máxima, por tando lo convierto a días
			// $tiempoTratamiento 	= "";
			// if( !empty( $rows[ 'Ctrdma' ] ) && $rows[ 'Ctrdma' ]*1 > 0 ){
				// $tiempoTratamiento = ceil( ( $rows[ 'Ctrdma' ]-1 )*$rows[ 'Perequ' ]/24 );
				
				// if( $tiempoTratamiento > 0 )
					// $tiempoTratamiento .= " d&iacute;as";
			// }
			$tiempoTratamiento 	= "1 d&iacute;a";	//Abril 20 de 2017. Siempre debe ser un día
			
			$descripcionArticulo = consultarDescripcionArticulo( $conex, $wbasedato, $wcenmez, $rows['Ctrart'], $rows['Ctrhis'], $rows['Ctring'], $rows['Ctrido'], $rows['Ctrfge'] );
			if( empty($descripcionArticulo ) )
				$descripcionArticulo = $rows[ 'Artgen' ];
			else{
				$formaFarmaceutica = consultarFormaFarmaceutica( $conex, $wbasedato, $descripcionArticulo[ 'Artfar' ] );
				$descripcionArticulo = $descripcionArticulo[ 'Artgen' ];
			}
			
			?>
			
			<table width="669" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
			  <tr>
				<td width="128"><img src='../../images/medical/root/<?php echo $institucion->baseDeDatos; ?>.jpg' width="128" heigth="53"></td>				
				<td width="251"><div align="center"><b>RECETARIO PARA MEDICAMENTOS DE CONTROL ESPECIALES<br>Nro. <?php echo $nroOrden; ?></b></div></td>
				<!-- <td width="282" rowspan="5" align="center" style="border-bottom-style:none">&nbsp; -->
				<td width="282" rowspan="4" align="center" style="border-bottom-style:none">&nbsp;
				<center><img src='../../../include/root/clsGenerarCodigoBarras.php?width=170&height=40&barcode=<?php echo $historia; ?>'></center>
				<font style='font-size:8pt'>
				<?php 
					echo "<b>{$rows['Pacno1']} {$rows['Pacno2']} {$rows['Pacap1']} {$rows['Pacap2']}</b>";
					echo "<br><b>{$rows['Pactid']}-{$rows['Pacced']}</b>";
					echo "<br>$edad - $descripcionSexo";
					// echo "<br>{$rows['Cconom']}-{$rows['Ubihac']}";
					echo "<br>{$rows['nomser']}-{$rows['Ctrhac']}";
					echo "<br><b>{$rows['Ctrhis']}-{$rows['Ctring']}</b>";
					
					
					//echo "<br>Tel: $telefono";
					// echo "<br>$direccion, $departamento - $ciudad, tel:$telefono";
				?>
				</font>
				</td>
			  </tr>
			  <tr>
				<td colspan="2" style="border-left-style:none"><b>Fecha actual:</b> <?php echo date("Y-m-d H:i:s"); ?></td>
			  </tr>
			  <tr>
				<td colspan="2" style="border-left-style:none"><b>Diagn&oacute;stico</b></td>
			  </tr>
			  <tr>
				<td colspan="2" style="border-left-style:none"><?php echo $diagnostico; ?><b></td>
			  </tr>
			  <!-- <tr>
				<td height="37" colspan="2" style="border-bottom-style:none;border-left-style:none">&nbsp;</td>
			  </tr> -->
			  <tr>
				<!-- <td colspan="2"style="border-top-style:none;border-bottom-style:none;border-left-style:none"><b>MEDICAMENTO</b></td> -->
				<td colspan="3"style="border-top-style:none;border-bottom-style:none;border-left-style:none">
					<b>Direcci&oacute;n y tel&eacute;fono de paciente: </b><?=$direccion.", ".$departamento." - ".$ciudad.", tel:".$telefono; ?>
				</td>
			  </tr>
			</table>
			
			<table width="669" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse">
			  <tr>
				<td colspan="2" rowspan="2"><div align="center"><b>Nombre gen&eacute;rico y Concentraci&oacute;n</b></div></td>
				<td width="92" rowspan="2"><div align="center"><b>Forma farmaceutica</b></div></td>
				<td width="112" rowspan="2"><div align="center"><b>Frecuencia de administraci&oacute;n</b></div></td>
				<td colspan="2"><div align="center"><b>Cantidad prescrita</b></div></td>
			  </tr>
			  <tr>
				<td width="78" height="28"><div align="center"><b>N&uacute;meros</b></div></td>
				<td width="89"><div align="center"><b>Letras</b></div></td>
			  </tr>
			  <tr>
				<td height="40" colspan="2">&nbsp;<?php echo $descripcionArticulo;?><?php echo $wobser_med;?></td>
				<td align="center">&nbsp;<?php echo @$formaFarmaceutica; ?></td>
				<td align="center">&nbsp;Cada <?php echo $rows[ 'Perequ' ]; ?> horas</td>
				<td align="center">&nbsp;<?php echo $rows[ 'Ctrcan' ]; ?></td>
				<td align="center">&nbsp;<?php echo ucwords( strtolower( $cantidadLetras ) ); ?></td>
			  </tr>
			  
			  <tr>
				  <td colspan='2'><b>Tiempo de tratamiento:</b></td>
				  <td colspan='4'><?php echo $tiempoTratamiento; ?></td>
			  </tr>
			  
			  <!-- <tr>
				<td height="24" colspan="6"><b>PROFESIONAL</b></td>
			  </tr> -->
			  <tr>
				<td height="23" colspan="2"><div align="center"><b>Nombre del m&eacute;dico</b></div></td>
				<td><div align="center"><b>Registro</b></div></td>
				<td><div align="center"><b>C&eacute;dula</b></div></td>
				<td colspan="2"><div align="center"><b>Firma</b></div></td>
			  </tr>
			  <tr>
				<td colspan="2">&nbsp;<?php echo $infoMedico[ 'Medno1' ]." ".$infoMedico[ 'Medno2' ]." ".$infoMedico[ 'Medap1' ]." ".$infoMedico[ 'Medap2' ]; ?></td>
				<td align="center">&nbsp;<?php echo $infoMedico[ 'Medreg' ]; ?></td>
				<td align="center">&nbsp;<?php echo $infoMedico[ 'Meddoc' ]; ?></td>
				<td colspan="2">&nbsp;<?php echo $firmaMedico; ?></td>
			  </tr>
			  <tr>
				<td colspan="6"><b>SERVICIO FARMACEUTICO</b></td>
			  </tr>
			  <tr>
				<td width="158" rowspan="2"><div align="center">Fecha</div></td>
				<td colspan="2"><div align="center">Cantidad despachada</div></td>
				<td colspan="3" rowspan="2"><div align="center">Regente de farmacia</div></td>
			  </tr>
			  <tr>
				<td width="126"><div align="center">Letras</div></td>
				<td><div align="center">N&uacute;meros</div></td>
			  </tr>
			  <tr>
				<td>Fecha 1</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>Fecha 2</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>Fecha 3</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>Fecha 4</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>Fecha 5</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>
			</table>
			<div style='text-align:center'><b><?=$copia;?></b></div>

			
			
			<?php			
			
			//marco como impreso el articulo
			if( $editable == 'on' )
				cambiarEsadoImpresionPorId( $conex, $wbasedato, $id_registro, $wusuario );
		}
		
			echo "</div>";
		echo "</div>";
	}
	
	 echo "</form>";
}
?>