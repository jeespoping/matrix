<?php
include_once("conex.php");

/************************************************************************************
 * Registra en la tabla correspondiente la humedad
 ************************************************************************************/
function registrarDatos( $conex, $wclisur, $fecha, $hora, $historia, $ingreso, $temperatura, $humedad, $usuario ){
	
	$val = false;
	
	$fecha_data = date( "Y-m-d" );
	$hora_data  = date( "H:i:s" );
	
	$sql = "INSERT INTO 
			".$wclisur."_000311(    Medico     ,   Fecha_data     ,    Hora_data    ,    Rhtfec   ,   Rhthor   ,    Rhthis	    ,    Rhting     ,   Rhthum     ,        Rhttem     ,     Rhtusu    ,    Seguridad     )
						 VALUES( '".$wclisur."', '".$fecha_data."', '".$hora_data."', '".$fecha."', '".$hora."', '".$historia."', '".$ingreso."','".$humedad."', '".$temperatura."', '".$usuario."', 'C-".$wclisur."' )";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}






include_once("root/comun.php");

$wclisur = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion" );

$wuser = "";
	
if(!$_SESSION["user"]){
	exit("<b>Usuario no registrado</b>");
}
else{

	$user = $_SESSION["user"];
	
	$wuser = substr($user,(strpos($user,"-")+1),strlen($user));

	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	
}

if( !empty( $accionAjax ) ){
	
	$data = array(
				'error' 	=> '' ,
				'message' 	=> '' ,
				'html' 		=> '' ,
			);
	
	switch( $accionAjax ){
		
		case 'guardarDatos': 
		
			$historia 	 = $_POST[ 'historia' ];
			$ingreso 	 = $_POST[ 'ingreso' ];
			$temperatura = $_POST[ 'temperatura' ];
			$humedad 	 = $_POST[ 'humedad' ];
			
			$registro = registrarDatos( $conex, $wclisur, date("Y-m-d"), date("H:i:s"), $historia, $ingreso, $temperatura, $humedad, $wuser );
			
			if( $registro ){
				$data[ 'error' ] 	= '0';
				$data[ 'message' ] 	= utf8_encode( 'Datos guardados correctamente' );
			}
			else{
				$data[ 'error' ] 	= '1';
				$data[ 'message' ] 	= utf8_encode( 'Los datos no fueron guardados correctamente' );
			}
			
		break;
		
		case '': 
		break;
		
		default: break;
	}
	
	//Si es ajax siempre debe devolver el json de data
	echo json_encode( $data );
}
else{
	
	$actualiz = "2018-04-24";
	
	//Consulto los pacientes hospitalizados
	$sql = "SELECT Pachis, Oriing, Pachis, Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2
			  FROM ".$wclisur."_000100, root_000037
			 WHERE pacact = 'on'
			   AND pachis = orihis
			   AND oriori = '".$wemp_pmla."'
			   AND pactam = 'MH'
			 ";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		
		$pacientes = array();
		while( $rows = mysql_fetch_array( $res ) ){
			
			$pacientes[] = array( 
				'historia' 		 => $rows[ 'Pachis' ],
				'ingreso' 	 	 => $rows[ 'Oriing' ],
				'nombreCompleto' => $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ],
				'tipoDocumento'  => $rows[ 'Pactdo' ],
				'numeroDocumento'=> $rows[ 'Pacdoc' ],
			);
		}
	}
?>

<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
	
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>


<script>
	
	function guardarDatos( historia, ingreso, temperatura, humedad ){
		
		$.post(
			"registrosTemperaturaHumedad.php",
			{
				consultaAjax: '',
				accionAjax	: 'guardarDatos',
				historia	: historia,
				ingreso		: ingreso,
				temperatura	: temperatura,
				humedad		: humedad,
				wemp_pmla	: '<?= $wemp_pmla ?>',
			},
			function( data ){
				console.log(data);
				if( data.error == 0 ){
					jAlert( data.message, "ALERTA" );
					
					$( "#dvHumedadTemperatura" ).dialog('close');
				}
			},
			"json"
		);
	}
	
	function abrirModalDatos( historia, ingreso ){
		
		var his = historia || undefined;
		var ing = ingreso || undefined;
		
		var dvModal = $( "#dvHumedadTemperatura" );
		
		$( "input:text", dvModal ).val('');
		
		dvModal.dialog({
			title	: "Ingrese los datos",
			modal	: true,
			buttons	: [
			{
				text	: 'Guardar',
				icon	: "ui-icon-heart",
				click	: function(){
					
					var historia 	= $( "[name=historia]", this ).val();
					var ingreso		= $( "[name=ingreso]", this ).val();
					var temperatura = $( "[name=temperatura]", this ).val();
					var humedad 	= $( "[name=humedad]", this ).val();
					
					if( temperatura*1 > 0 && humedad*1 > 0 ){
						guardarDatos( historia, ingreso, temperatura, humedad );
					}
					else{
						jAlert( "Debe ingresar tanto humedad como temperatura", "ALERTA" )
					}
				},
			},
			{
				text	: 'Cerrar',
				icon	: "ui-icon-heart",
				click	: function(){
					$( this ).dialog( "close" );
				},
			}],
		});
		
		if( his && ing ){
			$( "[name=historia]", dvModal ).val( historia );
			$( "[name=ingreso]", dvModal ).val( ingreso );
			
			$( "[name=historia]", dvModal ).css({disabled:true});
			$( "[name=ingreso]", dvModal ).css({disabled:true});
		}
	}
	
</script>

<style>
	#tbMain > tbody > tr:nth-child(odd){
		background-color: #C3D9FF;
	}
	
	#tbMain > tbody > tr:nth-child(even){
		background-color: #E8EEF7F;
	}
	
	#tbMain td{
		font-size: 10pt;
	}
	
	#tbMain{
		padding: 10px;
		margin: 0 auto;
	}
	
	.table { 
		display: table; 
	}
	
	.row { 
		display: table-row; 
	}
	
	.cell { 
		display: table-cell; 
	}
</style>

<body>
	
	<?php encabezado( "REGISTRO DE TEMPERATURA Y HUMEDAD", $actualiz ,"clinica" );	 ?>
	
	<!-- <div style='text-align:center;'>
		<input type='button' value='Ingresar datos' onclick='abrirModalDatos();'>
	</div> -->
	
	<div id='dvHumedadTemperatura' style='display:none;'>
	
		<div class='table'>
			<div class='row'>
				<div class='cell' style='width:100px;'>
					<label>Historia</label>
				</div>
				<div class='cell' style='width:100px;'>
					<input name='historia' type='text' style='width:100px;' disabled>
				</div>
			</div>
			<div class='row'>
				<div class='cell' style='width:100px;'>
					<label>Ingreso</label>
				</div>
				<div class='cell' style='width:100px;'>
					<input name='ingreso' type='text' style='width:100px' disabled>
				</div>
			</div>
			<div class='row'>
				<div class='cell' style='width:100px;'>
					<label>Humedad</label>
				</div>
				<div class='cell' style='width:150px;'>
					<input name='humedad' type='text' style='width:100px' onkeypress='return validarEntradaDecimal(event);'> %
				</div>
			</div>
			<div class='row'>
				<div class='cell' style='width:100px;'>
					<label>Temperatura</label>
				</div>
				<div class='cell' style='width:100px;'>
					<input name='temperatura' type='text' style='width:100px' onkeypress='return validarEntradaDecimal(event);'> &deg;C
				</div>
			</div>
		</div>
		
	</div>

	<h1 style='text-align:center;'>Pacientes hospitalizados</h1>
	
	<div style='text-align:center;margin: 0 auto'>
		<table id='tbMain'>
			<thead class='encabezadotabla' style='text-align:center;'>
				<tr>
					<td>Historia</td>
					<td>Documento</td>
					<td>Nombre</td>
					<td>Ingresar datos</td>
				</tr>
			</thead>
			
			<tbody>
<?php
	foreach( $pacientes as $key => $value ){
		
		echo "<tr>";
		
		echo "<td style='width:100px;text-align:center;'>".$value['historia']."-".$value['ingreso']."</td>";
		echo "<td style='width:150px;'>".$value['tipoDocumento']." ".$value['numeroDocumento']."</td>";
		echo "<td>".$value['nombreCompleto']."</td>";
		echo "<td><a href=#null onclick='abrirModalDatos( ".$value['historia'].", ".$value['ingreso']." )'>Ingresar datos</a></td>";
		
		echo "</tr>";
	}
?>
			</tbody>
		</table>
	</div>
	
	<div style='text-align:center'>
		<input type='button' value='Cerrar' style='width:100px;' onclick='cerrarVentana();'>
	</div>
	
</body>

<?php
}