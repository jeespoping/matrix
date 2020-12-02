<?php 
/*
 * ASIGNACION DE HABITACIONES PARA CCO DE DOMICILIRIA
 * Autor: Edwin Molina Grisales
 */
 
include_once("conex.php");
include_once("root/comun.php");
include_once("movhos/movhos.inc.php");
include_once("../../interoperabilidad/procesos/funcionesGeneralesEnvioHL7.php");

$usuarioValidado = true;
$wactualiz = "Agosto 19 de 2020";

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos' );

/*****************************
 * FUNCIONES
 ****************************/




/**
 * muestra un select con los centros de cotos
 */
function pintarCcos( $datos ){

	if( count( $datos ) > 0 ){
		
		echo "<select id='wccos'>";
		echo "<option value=''>Seleccione...</option>";
		
		foreach( $datos as $key => $value ){
			echo "<option value='".$value['codigo']."'>".$value['descripcion']."</option>";
		}
		
		echo "</select>";
	}
	else{
		echo "<div style='text-align:center;'><span style='font-weight:bold;'>NO SE ENCUENTRAN CCOS DOMICILIARIOS CONFIGURADOS</span></div>";
	}
}

/****************************************************************************
 * Consulta los centros de costos que son domiciliarios
 ****************************************************************************/ 
function consultarCcoDomiciliarios( $conex, $wbasedato ){
	
	$val = [];
	
	$sql = "SELECT Ccocod, Cconom
			  FROM ".$wbasedato."_000011
			 WHERE ccodom  = 'on'
			   AND ccoest  = 'on'
			   AND ccotra != 'on'
			 ";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );
	
	while( $rows = mysql_fetch_array($res) ){
		$val[] = [
			'codigo' 		=> $rows['Ccocod'],
			'descripcion' 	=> $rows['Cconom'],
		];
	}
	
	return $val;
}


/************************************************************
 * Se asignar habitaciones a los pacientes
 ************************************************************/
function asignarHabitacionPaciente( $conex, $tabla, $cco, $hab, $his, $ing, $zona ){
	
	$val = false;
	
	$fecha = date( "H:i:s" );
	$hora  = date( "Y-m-d" );

	list( $wbasedato ) = explode( "_", $tabla );

	$sql = "REPLACE INTO 
			".$tabla." (   Medico       ,  Fecha_data , Hora_data  ,    Habcod , Habcco    , Habhis    ,  Habing   , Habali, Habdis, Habest, habpro,   habfal    ,    habhal, habprg, Habtmp,   Habzon   , Habord, Habtip, Habtfa,    Habcpa       , Habcub, Habvir,     Seguridad     ) 
			    VALUES ('".$wbasedato."', '".$fecha."', '".$hora."', '".$hab."', '".$cco."', '".$his."', '".$ing."', 'off' , 'off' , 'on'  ,  'off', '0000-00-00', '00:00:00', 'off', 'off' , '".$zona."',   ''  ,   ''  ,   ''  , '".$habitacion."', 'off',  'off', 'C-".$wbasedato."');
			";
		  
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );

	if( mysql_affected_rows() !== 0 ){
		$val = true;
	}
	
	return $val;
}

/************************************************************
 * Se asignar habitaciones a los pacientes
 ************************************************************/
function actualizarHabitacionPaciente( $conex, $wbasedato, $his, $ing, $hab ){
	
	$val = false;
	
	$fecha = date( "H:i:s" );
	$hora  = date( "Y-m-d" );

	$sql = "UPDATE ".$wbasedato."_000018
			   SET ubihac  = '".$hab."'
			 WHERE ubihis  = '".$his."'
			   AND ubiing  = '".$ing."'
			   AND ubiald != 'on'
			";
		  
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );

	if( mysql_affected_rows() !== 0 ){
		$val = true;
	}
	
	return $val;
}

/****************************************************************
 * Consulta las zonas según por centro de costos
 ****************************************************************/
function consultarZonasPorCco( $conex, $wbasedato, $cco ){
	
	$val = [];

	$sql = "SELECT Aredes, Arecod
			  FROM ".$wbasedato."_000169
			 WHERE Areest  = 'on'
			   AND Arecco != '.'
			   AND Arecco != 'NO APLICA'
			   AND ( Arecco  = '*'
			    OR FIND_IN_SET( '".$cco."', Arecco ) > 0 )
			";
		  
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );

	while( $rows = mysql_fetch_assoc($res) ){

		$val[] =  [
				'codigo' 		=> utf8_encode( $rows[ 'Arecod' ] ),
				'descripcion' 	=> utf8_encode( $rows[ 'Aredes' ] ),
			];
	}
	
	return $val;
}

function consultarPacientesAMostrar( $conex, $wemp_pmla, $wbasedato, $cco ){
	
	$val = [];
	
	$tablaHabitaciones 	= consultarTablaHabitaciones( $conex, $wbasedato, $cco );
	$zonas	  			= consultarZonasPorCco( $conex, $wbasedato, $cco );
	
	$sql = "SELECT Ubihis, Ubiing, Ubisac, Ubihan
			  FROM ".$wbasedato."_000018 a 
		 LEFT JOIN ".$tablaHabitaciones." b
			    ON b.habhis = a.ubihis
			   AND b.habing = a.ubiing
			 WHERE a.ubisac = '".$cco."'
			   AND b.habcco IS NULL
			   AND a.ubiald = 'off'
			 ";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );
	
	while( $rows = mysql_fetch_array( $res ) ){
		// echo "-".$rows['Ubihis'];
		//función en el script funcionesGeneralesEnvioHL7.php
		$paciente 					= informacionPaciente( $conex, $wemp_pmla, $rows['Ubihis'], $rows['Ubiing'] );
		$paciente['nombre1'] 		= utf8_encode( $paciente['nombre1'] );
		$paciente['nombre2'] 		= utf8_encode( $paciente['nombre2'] );
		$paciente['apellido1'] 		= utf8_encode( $paciente['apellido1'] );
		$paciente['apellido2'] 		= utf8_encode( $paciente['apellido2'] );
		$paciente['nombreCompleto']	= utf8_encode( $paciente['nombreCompleto'] );
		$paciente['direccion']		= utf8_encode( $paciente['direccion'] );
		$paciente['cco'] 			= $rows['Ubisac'];
		$paciente['barrio']			= utf8_encode( consultarBarrio( $conex, $wbasedato, $paciente['codigomMunicipio'], $paciente['codigoBarrio'] ) );
		$paciente['zonas'] 			= $zonas;
		$paciente['zonaAsignada']	= '';
		
		$val[] = $paciente;
	}
	
	return $val;
}

function consultarPacientesConZonaAsigandasAMostrar( $conex, $wemp_pmla, $wbasedato, $cco ){
	
	$val = [];
	
	$tablaHabitaciones 	= consultarTablaHabitaciones( $conex, $wbasedato, $cco );
	$zonas	  			= consultarZonasPorCco( $conex, $wbasedato, $cco );
	
	$sql = "SELECT Ubihis, Ubiing, Ubisac, Ubihan, Habzon
			  FROM ".$wbasedato."_000018 a , ".$tablaHabitaciones." b
			 WHERE a.ubisac = '".$cco."'
			   AND b.habhis = a.ubihis
			   AND b.habing = a.ubiing
			   AND a.ubiald = 'off'
			   AND a.ubiald = 'off'
			 ";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );
	
	while( $rows = mysql_fetch_array( $res ) ){
		// echo "-".$rows['Ubihis'];
		//función en el script funcionesGeneralesEnvioHL7.php
		$paciente 					= informacionPaciente( $conex, $wemp_pmla, $rows['Ubihis'], $rows['Ubiing'] );
		$paciente['nombre1'] 		= utf8_encode( $paciente['nombre1'] );
		$paciente['nombre2'] 		= utf8_encode( $paciente['nombre2'] );
		$paciente['apellido1'] 		= utf8_encode( $paciente['apellido1'] );
		$paciente['apellido2'] 		= utf8_encode( $paciente['apellido2'] );
		$paciente['nombreCompleto']	= utf8_encode( $paciente['nombreCompleto'] );
		$paciente['direccion']		= utf8_encode( $paciente['direccion'] );
		$paciente['cco'] 			= $rows['Ubisac'];
		$paciente['barrio']			= utf8_encode( consultarBarrio( $conex, $wbasedato, $paciente['codigomMunicipio'], $paciente['codigoBarrio'] ) );
		$paciente['zonas'] 			= $zonas;
		$paciente['zonaAsignada']	= $rows['Habzon'];
		
		$val[] = $paciente;
	}
	
	return $val;
}

if( isset($accion) ){
	
	switch( $accion ){
		case 'asignarHabitacionPaciente': 
			
			//La habitación se define como HAB_HISTORIA y corresponde al código de la habitación
			$habitacion = "HAB_".$whis;
		
			$tabla = consultarTablaHabitaciones( $conex, $wbasedato, $wcco );
			
			asignarHabitacionPaciente( $conex, $tabla, $wcco, $habitacion, $whis, $wing, $wzona );
			actualizarHabitacionPaciente( $conex, $wbasedato, $whis, $wing, $habitacion );
			
			echo "1";
		break;
		
		case 'consultarPacientesAMostrar': 
			$result = consultarPacientesAMostrar( $conex, $wemp_pmla, $wbasedato, $wcco );
			
			echo json_encode( $result );
		break;
		
		case 'consultarPacientesConZonaAsigandasAMostrar': 
			$result = consultarPacientesConZonaAsigandasAMostrar( $conex, $wemp_pmla, $wbasedato, $wcco );
			
			echo json_encode( $result );
		break;
	}
	
	exit();
}
else{
?>

<html>

<head>
<title>ASIGNACION DE ZONAS PARA CCOS HOSPITALARIAS</title>

<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>


<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>


<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>


<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>

<script type="text/javascript">

	function asignarHabitacionPaciente( whis, wing, wzona ){

		if( wzona != '' ){
			
			$.post("./asignacionZonaDomiciliaria.php",
				{
					consultaAjax	: '',
					accion			: 'asignarHabitacionPaciente',
					wemp_pmla		: '<?= $wemp_pmla ?>',
					whis			: whis,
					wing			: wing,
					wcco			: $( wccos ).val(),
					wzona  			: wzona,
				}
				,function( data_json ){
					
					if( data_json == '1' ){
						jAlert( "Asignaci&oacute;n de cama correcta", "ALERTA" );
					}
					else{
						jAlert( "No se realiz&oacute; la actualizaci&oacute;n de zona para el paciente correctamente", "ALERTA" );
					}
				},
				"json"
			);
		}
		else{
			jAlert( "Debe seleccionar una zona", "ALERTA" );
			return false;
		}
	}
	
	function agregarBotonMostrarPacientes( conZonas ){
		
		
		var accion 	= 'consultarPacientesAMostrar';
		var texto 	= "Sin Zona";
		
		if( conZonas ){
			accion 	= 'consultarPacientesConZonaAsigandasAMostrar';
			texto	= "Con Zona";
		}
		
		//Agregando la tabla al div que muestra los pacientes
		$( "<div style='text-align:right;'><span id='spConYSinZona' style='font-weight:bold; color:blue;cursor: pointer;'>"+texto+"</span></div>" ).appendTo( dvpacientes );
		
		//evento change al seleccionar ccos
		//Debe mostrar los pacientes que no se le ha asignado zonas para pacientes domiciliarios
		$( "#spConYSinZona" ).click(function(){
			
			$.post("./asignacionZonaDomiciliaria.php",
				{
					consultaAjax	: '',
					accion			: accion,
					wemp_pmla		: '<?= $wemp_pmla ?>',
					wcco			: $( wccos ).val(),
				}
				,function( data_json ){
					
					data_json = data_json || [];
					
					$( dvpacientes ).html('');
					mostrarPacientes( data_json, !conZonas );
				},
				"json"
			);
			
		});
		
	}
	
	function buscadorInformacionPaciente(){
		
		//Agregando la tabla al div que muestra los pacientes
		$( "<div style='display:block;margin: 0 auto; width:80%;'><span style='font-weight:bold;'>Buscador:</span><input id='buscarInformacion'></div>" ).insertAfter( $( "#spConYSinZona" ).parent() );
		
		console.log( $('#buscarInformacion') );
		$('#buscarInformacion').quicksearch('#dvpacientes .find');
	}
	
	function mostrarPacientes( datosJson, conZonas ){
		
		if( datosJson.length > 0 ){
			
			agregarBotonMostrarPacientes( conZonas );
		
			var titulo = "PACIENTES CON ZONAS ASIGANDA";
			if( conZonas )
				titulo = "PACIENTES SIN ZONAS ASIGANDA";
		
			///Creo la tabla que se mostrará en la que se mostrarán los pacientes
			var table = $(  "<table style='margin: 0 auto; width:80%;'>"
						  +		"<tr class='encabezadotabla'><td colspan=7 style='font-size:14pt;text-align:center;'>"+titulo+"</td></tr>"
						  +		"<tr class='encabezadotabla'>"
						  +			"<td>Historia</td>"
						  +			"<td>Identificaci&oacute;n</td>"
						  +			"<td>Nombre del paciente</td>"
						  +			"<td>Barrio</td>"
						  +			"<td>Direcci&oacute;n</td>"
						  +			"<td>Zona a asignar</td>"
						  +			"<td></td>"
						  + 	"</tr>"
						  + "<table>" );
						  
			
			$( datosJson ).each(function(index){
				
				//Selfe es cada elmento en el objeto json, el cuál trae la información del paciente
				var _self 	= this;
				
				//Asignando clase por fila
				var _class 	= index%2 ? "fila1" : "fila2";
				
				//Creando un tr por paciente que luego se agregará a la tabla
				var tr = $(   "<tr class='"+_class+" find'>"
							+ "<td>"+_self.historia+"-"+_self.ingreso+"</td>"
							+ "<td>"+_self.tipoDocumento+" "+_self.nroDocumento+"</td>"
							+ "<td>"+_self.nombreCompleto+"</td>"
							+ "<td>"+_self.barrio+"</td>"
							+ "<td>"+_self.direccion+"</td>"
							+ "<td></td>"
							+ "<td><a href='#null'>Trasladar</td>"
							+ "</tr>" );
				
				//Creando los options de las zonas
				var opts = '';
				
				$( _self.zonas ).each(function(index){
					opts += "<option value='"+this.codigo+"'>"+this.descripcion+"</option>";
				});
				
				//Creando el select para asignación de zonas
				//Se asigna también el evento change para la zona
				var slZonas = $( "<select>"
								 + 	"<option value=''>Seleccione...</option>"
								 + 	opts
								 + "</select>" 
								).change(function(){
									//Se llama a la función asignar asignarHabitacionPaciente, para acomodarlo en una habitación
									//En este caso this, se refiere al select de zona
									return asignarHabitacionPaciente( _self.historia, _self.ingreso, $( this ).val() );
								});
								
				$( "td:last", tr ).click(function(){
					$( "<div title='TRASLADO DE PACIENTES SERVICIO DOMICILIARIO' style='height:600px;'><iframe src='./cambiarPacientesCcoDomiciliarios.php?wemp_pmla=01&historia="+_self.historia+"' style='width:100%;height:100%;'></iframe></div>" ).dialog({
						width	: "90%", 
						height	: 500, 
						modal	: true,
						buttons	: {	
									Cerrar: function() {
										$( this ).dialog( "close" );
										$( wccos ).change();
									}
								},
					  })
				})
								
				//Agrego las zonas al último td del tr
				slZonas.appendTo( $( "td", tr ).eq(-2) );
				
				slZonas.val( _self.zonaAsignada );
				
				//agreando el tr a la tabla
				$( tr ).appendTo( $( "tbody", table ) );
			})
			
			//Agregando la tabla al div que muestra los pacientes
			$( table ).appendTo( dvpacientes );
			
			buscadorInformacionPaciente();
		}
		else{
			
			agregarBotonMostrarPacientes( conZonas );
			
			if( $(wccos).val() != ''  ){
				$( dvpacientes ).append("<div style='text-align:center;font-size:12pt;font-weight:bold;'>No hay pacientes a mostrar en el servicio domiciliario seleccionado</div>");
			}
			else{
				$( dvpacientes ).append("<div style='text-align:center;font-size:12pt;font-weight:bold;'>Debe seleccionar un Centro de costos</div>");
			}
			
		}
	}
	
	$(document).ready(function(){
		
		//evento change al seleccionar ccos
		//Debe mostrar los pacientes que no se le ha asignado zonas para pacientes domiciliarios
		$( wccos ).change(function(){
			
			$.post("./asignacionZonaDomiciliaria.php",
				{
					consultaAjax	: '',
					accion			: 'consultarPacientesAMostrar',
					wemp_pmla		: '<?= $wemp_pmla ?>',
					wcco			: $( wccos ).val(),
				}
				,function( data_json ){
					data_json = data_json || [];
					$( dvpacientes ).html('');
					mostrarPacientes( data_json, true );
				},
				"json"
			);
			
		});
	});

</script>

</head>

<body>
<?php
	 
	 /*****************************
	 * INCLUDES
	 ****************************/

	//Encabezado
	$consulta = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	encabezado("ASIGNACION DE ZONAS SERVICIO DOMICILIARIO",$wactualiz, $consulta->baseDeDatos );

	if (!$usuarioValidado){
		echo '<span class="subtituloPagina2" align="center">';
		echo 'Error: Usuario no autenticado';
		echo "</span><br><br>";

		terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
	}
	else{
		$wccos = consultarCcoDomiciliarios( $conex, $wbasedato );
		?>
		<div>
			<table style='margin: 0 auto;'>
				<tr class='encabezadotabla'>
					<td>SELECCIONE CENTRO DE COSTOS</td>
				</tr>
				<tr class='fila1' style='text-align:center;'>
					<td><?php pintarCcos( $wccos ); ?></td>
				</tr>
			<table>
			
			<div id='dvpacientes' style='padding: 20px;'>
			</div>
		</div>
		
		<div>
			<center>
				<input type="button" value="Cerrar ventana" onclick="javascript:cerrarVentana();">
			</center>
		</div>
	
		<?php
		
	}
}