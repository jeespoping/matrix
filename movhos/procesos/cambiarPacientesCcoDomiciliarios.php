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
 
function esCcoDomiciliario( $conex, $wbasedato, $wcco ){
		
	$val = false;

	$sql = "SELECT Ccodom
			  FROM ".$wbasedato."_000011
			 WHERE ccocod = '".$wcco."'
			   AND ccodom = 'on'
			 ;";

	$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if($num > 0)
	{
		$val = true;
	}
	
	return $val;
}



function cambiarCcoPorPaciente( $conex, $wbasedato, $whis, $wing, $wcco_origen, $wcco_destino  ){

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'nombreccoayuda'=>'');
	
	$datamensaje['error'] 	= 1;
	$datamensaje['mensaje'] = "Este Centro de costos no permite traslado a otros servicios";
	
	$tablaHabitacionesOrigen  = consultarTablaHabitaciones( $conex, $wbasedato, $wcco_origen );
	$tablaHabitacionesDestino = consultarTablaHabitaciones( $conex, $wbasedato, $wcco_destino );
	
	$sql = "REPLACE INTO ".$tablaHabitacionesDestino." 
			 SELECT Medico,  CURRENT_DATE() , CURRENT_TIME(),    Habcod , '".$wcco_destino."'    , Habhis    ,  Habing   , Habali, Habdis, Habest, habpro,   habfal    ,    habhal, habprg, Habtmp,   Habzon   , Habord, Habtip, Habtfa,    Habcpa       , Habcub, Habvir,     Seguridad  , NULL    
			   FROM ".$tablaHabitacionesOrigen."
			  WHERE habhis = '".$whis."' 
				AND habing = '".$wing."'
			";
	  
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );
	
	$sql = "UPDATE ".$wbasedato."_000018 
			   SET Ubisac = '".$wcco_destino."'
			 WHERE ubihis = '".$whis."' 
			   AND ubiing = '".$wing."'
			";
	  
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );
	
	if( $tablaHabitacionesOrigen != $tablaHabitacionesDestino ){
		
		$sql = "DELETE FROM ".$tablaHabitacionesOrigen."
					  WHERE habhis = '".$whis."' 
						AND habing = '".$wing."'
				";
	  
		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ". mysql_error() );
	}
	
	// echo json_encode($datamensaje);
	
	return "1";
}



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
			 WHERE ccodom = 'on'
			   AND ccoest = 'on'
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

function consultarPaciente( $conex, $wemp_pmla, $wbasedato, $whis ){
	
	$val = [];
	
	$wing = consultarUltimoIngresoHistoria($conex, $whis, $wemp_pmla );
	
	$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $whis, $wing );
	
	$tablaHabitaciones 	= consultarTablaHabitaciones( $conex, $wbasedato, $ubicacionPaciente->servicioActual );
	
	if( true ){
		
		$error 		= '0';
		$mensaje 	= '';
		
		// $paciente 	= consultarInfoPacientePorHistoria( $conex, $whis, $wemp_pmla );
		$paciente 	= informacionPaciente( $conex, $wemp_pmla, $whis, $wing );
		$wccos_dom	= consultarCcoDomiciliarios( $conex, $wbasedato );
		
		$wccos = [];
		foreach( $wccos_dom as $value ){
			if( $value['codigo'] != $ubicacionPaciente->servicioActual )	
				$wccos[] = $value;
		}
		
		$esCcoDomiciliario = esCcoDomiciliario( $conex, $wbasedato, $ubicacionPaciente->servicioActual );
		
		if( !$esCcoDomiciliario ){
			$error 		= '1';
			$mensaje 	= 'El paciente no se encuentra en un centro de costos domiciliario, por tanto no se puede trasladar de servicio';
		}
		
		if( strtolower( $ubicacionPaciente->altaDefinitiva ) == 'on' ){
			$error 		= '1';
			$mensaje 	= 'Paciente con alta definitiva';
		}
		
		$cco = consultarCentroCosto( $conex, $ubicacionPaciente->servicioActual, $wbasedato );
		
		$val = [
					'error'		=> $error,
					'mensaje' 	=> $mensaje,
					'paciente' 	=> [
							'ccoActual' 		=> [
													'codigo' 	 => $cco->codigo,
													'descripcion'=> utf8_encode( $cco->nombre ),
												],
							'historia' 			=> $whis,
							'ingreso' 			=> $wing,
							'nombre1' 			=> utf8_encode( $paciente['nombre1'] ),
							'nombre2' 			=> utf8_encode( $paciente['nombre2'] ),
							'apellido1' 		=> utf8_encode( $paciente['apellido1'] ),
							'apellido2' 		=> utf8_encode( $paciente['apellido2'] ),
							'nombreCompleto'	=> utf8_encode( $paciente['nombreCompleto'] ),
							'nroDocumento'		=> $paciente['nroDocumento'],
							'tipoDocumento'		=> $paciente['tipoDocumento'],
							'barrio'			=> utf8_encode( consultarBarrio( $conex, $wbasedato, $paciente['codigomMunicipio'], $paciente['codigoBarrio'] ) ),
							'direccion'			=> utf8_encode( $paciente['direccion'] ),
							'ccos'				=> $wccos,
						],
				];
	}
	
	return $val;
}

if( isset($accion) ){
	
	switch( $accion ){
		case 'cambiarCcoPorPaciente': 
			
			$val = cambiarCcoPorPaciente( $conex, $wbasedato, $whis, $wing, $worigen, $wdestino );
			
			echo $val;
		break;
		
		case 'consultarPaciente': 
			$result = consultarPaciente( $conex, $wemp_pmla, $wbasedato, $whis );
			
			echo json_encode( $result );
		break;
	}
	
	exit();
}
else{
?>

<html>

<head>
<title>TRASLADO DE PACIENTES DE SERVICIO DOMICILIARIO</title>

<!-- JQUERY para los tabs -->
<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script>
<!-- Fin JQUERY para los tabs -->

<!-- Include de codigo javascript propio de mensajeria Kardex -->
<script type="text/javascript" src="../../../include/movhos/mensajeriaKardex.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>

<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>

<!-- Include de codigo javascript propio del kardex -->
<script type="text/javascript" src="kardex.js?v=<?=md5_file('kardex.js');?>"></script>

<script type="text/javascript">

	function cambiarCcoPorPaciente( whis, wing, worigen, wdestino ){

		if( wdestino != '' ){
			
			$.post("./cambiarPacientesCcoDomiciliarios.php",
				{
					consultaAjax	: '',
					accion			: 'cambiarCcoPorPaciente',
					wemp_pmla		: '<?= $wemp_pmla ?>',
					whis			: whis,
					wing			: wing,
					worigen			: worigen,
					wdestino		: wdestino,
				}
				,function( data_json ){
					
					if( data_json == '1' ){
						jAlert( "Asignaci&oacute;n de Cco correcta", "ALERTA" );
					}
					else{
						jAlert( "No se realiz&oacute; la actualizaci&oacute;n de zona para el paciente correctamente", "ALERTA" );
					}
				},
				"json"
			);
		}
		else{
			jAlert( "Debe seleccionar un centro de costos", "ALERTA" );
		}
	}
	
	function mostrarPacientes( datosJson ){
		
		if( datosJson.error == 0 ){
		
			///Creo la tabla que se mostrará en la que se mostrarán los pacientes
			var table = $(  "<table style='margin: 0 auto;'>"
						  +		"<tr class='encabezadotabla'>"
						  +			"<td>Historia</td>"
						  +			"<td>Identificaci&oacute;n</td>"
						  +			"<td>Nombre del paciente</td>"
						  +			"<td>Barrio</td>"
						  +			"<td>Direcci&oacute;n</td>"
						  +			"<td>Cco Actual</td>"
						  +			"<td>Cco a asignar</td>"
						  + 	"</tr>"
						  + "<table>" );
						  
			
			$( datosJson ).each(function(index){
				
				//Selfe es cada elmento en el objeto json, el cuál trae la información del paciente
				var _self 	= this.paciente;
				
				//Asignando clase por fila
				var _class 	= index%2 ? "fila1" : "fila2";
				
				//Creando un tr por paciente que luego se agregará a la tabla
				var tr = $(   "<tr class='"+_class+"'>"
							+ "<td>"+_self.historia+"-"+_self.ingreso+"</td>"
							+ "<td>"+_self.tipoDocumento+" "+_self.nroDocumento+"</td>"
							+ "<td>"+_self.nombreCompleto+"</td>"
							+ "<td>"+_self.barrio+"</td>"
							+ "<td>"+_self.direccion+"</td>"
							+ "<td>"+_self.ccoActual.codigo+"-"+_self.ccoActual.descripcion+"</td>"
							+ "<td></td>"
							+ "</tr>" );
				
				//Creando los options de las zonas
				var opts = '';
				
				$( _self.ccos ).each(function(index){
					opts += "<option value='"+this.codigo+"'>"+this.codigo+" - "+this.descripcion+"</option>";
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
									if( $( this ).val() != '' ){
										cambiarCcoPorPaciente( _self.historia, _self.ingreso, _self.ccoActual.codigo, $( this ).val() );
									}
									else{
										jAlert( "Debe seleccionar un centro de costos", "ALERTA" );
									}
								});
								
				//Agrego las zonas al último td del tr
				slZonas.appendTo( $( "td:last", tr ) );
				
				//agreando el tr a la tabla
				$( tr ).appendTo( $( "tbody", table ) );
			})
			
			//Agregando la tabla al div que muestra los pacientes
			$( table ).appendTo( dvpacientes );
		}
		else{
			$( dvpacientes ).html("<div style='text-align:center;font-size:12pt;font-weight:bold;'>No hay pacientes domiciliarios con zonas por asignar</div>");
		}
	}
	
	$(document).ready(function(){
		
		//evento change al seleccionar ccos
		//Debe mostrar los pacientes que no se le ha asignado zonas para pacientes domiciliarios
		$( btConsultar ).click(function(){
			
			$.post("./cambiarPacientesCcoDomiciliarios.php",
				{
					consultaAjax	: '',
					accion			: 'consultarPaciente',
					wemp_pmla		: '<?= $wemp_pmla ?>',
					whis			: $( inHistoria ).val(),
				}
				,function( data_json ){
					
					$( dvpacientes ).html('');
					mostrarPacientes( data_json );
				},
				"json"
			);
			
		})
		
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
	encabezado("TRASLADO DE PACIENTES DE SERVICIO DOMICILIARIO",$wactualiz, $consulta->baseDeDatos );

	$data_json = false;

	if( $_GET['historia'] ){
		
		$his = $_GET['historia'];
		
		if( !empty($his) ){
			
			$result = consultarPaciente( $conex, $wemp_pmla, $wbasedato, $his );
				
			$data_json = json_encode( $result );
		}
	}

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
					<td style='text-align:center;width:200px;'>INGRESE HISTORIA</td>
				</tr>
				<tr class='fila1' style='text-align:center;'>
					<td><input type='text' id='inHistoria'></td>
				</tr>
				<tr class='fila1' style='text-align:center;'>
					<td><input type='button' id='btConsultar' value='Consultar historia'></td>
				</tr>
			<table>
			
			<div id='dvpacientes' style='padding: 20px;'>
				<?php if($data_json) : ?>
					<script> mostrarPacientes( <?= $data_json ?> ); </script>
				<?php endif ?>
			</div>
		</div>
	
		<?php
		
	}
}