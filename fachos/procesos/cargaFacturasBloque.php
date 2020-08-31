<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	cargaFacturasBloque
 * Fecha		:	2015-07-14
 * Por			:	Frederick Aguirre Sanchez
 * Condiciones  :  
 *********************************************************************************************************
 
 Actualizaciones:

* 2016-02-12	: Jessica Madrid Mejía - Se agrega explicación para la carga del archivo plano y mensaje 
					que indica por qué no se carga el archivo.		
 **********************************************************************************************************/
 
$wactualiz = "2016-02-12";
 
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

if( isset($consultaAjax) == false ){
	
?>
	<html>
	<head>
	<title>Carga de Facturas en Bloque</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script src="../../../include/root/jquery.form.js" type="text/javascript"></script>
	
<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		
		$(".enlace_retornar").click(function() {
			restablecer_pagina();
		});
		
		$("button").button({icons:	{
						primary: "ui-icon ui-icon-arrowreturnthick-1-n"
						}
				})
		.click(function( event ){
					publicar_script();
		});
		
		$("form").submit(function(){
			if( $("#file_script").val() == "" ){
				mostrarAlerta("Debe elegir un archivo válido");
				return false;
			}
		});
	});
	

	function publicar_script()
	{
		$('#msj_calculando').show(600);
		$('#td_boton').hide();
	
		var guardar='si';										//variable semaforo que me inidcara si se puede publicar o no
		if( $("#file_script").val() == "" )
			guardar = "no";
			
		if(guardar == 'si')
		{
			$('#form_publicar').ajaxForm({
				url:      'cargaFacturasBloque.php?action=publicar_script&consultaAjax=&campos_completos=si&wemp_pmla='+$("#wemp_pmla").val(),
				complete: function(respuesta) {
					respuesta = respuesta.responseText;
					
					$('#msj_calculando').hide();
					$('#td_boton').show();
					
					console.log(respuesta);	
					if( isJson(respuesta) ){
						var data = eval('(' + respuesta + ')');
						if( data.OK == 1 ){
							$("#file_script").val("");
							mostrarAlerta("Carga de archivo con éxito.");
							if( data.msj != "" )
								mostrarAlerta( data.msj );
						}else{
							// mostrarAlerta(data.msj);
							
							if(data.msj!=null)
							{
								mostrarAlerta(data.msj);
							}
							else
							{
								mostrarAlerta("El archivo plano esta corrupto, debe generarlo y cargarlo nuevamente");
							}
						}
						if( data.facturas != null )
							panelClones( data.facturas );
					}else{
						mostrarAlerta(respuesta);
					}
					//var array_respuesta = respuesta.split("|");					
					//mostrar_mensaje(array_respuesta[0]);					
					
				}
			});
		}
		else
		{			
			$('#msj_calculando').hide();
			$('#td_boton').show();
		}
	}
	
	function panelClones( arrClones ){
		$('#td_boton').hide();
		var html = "<h2>Las siguientes facturas ya existen en el sistema.<br>Seleccione por favor las que desea adicionar de todas maneras.</h2>";
		$.each(arrClones, function(index, value){
			html+= "<div><input type='checkbox' class='rfactura' value='"+value+"' checked>&nbsp;"+value+"</div>";
		});
		html+="<br><br><input type='button' onclick='guardarFacturas()' value='Cargar' />";
		$("#respuesta").html( html );
	}
	
	function guardarFacturas(){
		var facturas = new Array();
		$(".rfactura").each(function(){
			if( $(this).is(":checked") ){				
				facturas.push( $(this).val() );
			}
		});
		
		if( facturas.length == 0 ){
			mostrarAlerta("Debe elegir al menos una factura.");
			return;
		}
		
		$.blockUI({ message: $('#msjEspere') });

		var wemp_pmla = $("#wemp_pmla").val();
		
		//Realiza el llamado ajax con los parametros de busqueda
		$.get('cargaFacturasBloque.php', { wemp_pmla: wemp_pmla, action: "guardarFacturas", wfacturas: facturas, consultaAjax: ''} ,
		function(data) {
			$.unblockUI();
			if( data == "OK" ){
				mostrarAlerta("Facturas guardadas con éxito.");
				$("#respuesta").html( "" );
			}else{
				mostrarAlerta(data);
				console.log(data);
			}
		});
	}
	
	function mostrarAlerta(msj){
		$( "#dialog-text" ).html(msj);
		$( "#dialog-message" ).dialog({
		  modal: true,
		  buttons: {
			Ok: function() {
			  $( this ).dialog( "close" );
			}
		  }
		});
	}
	
	//Funcion que se activa cuando se presiona el enlace "retornar"
	function restablecer_pagina(){
		$("#respuesta").html("");
		$("#file_script").val("");
		$('#td_boton').show();
	}
	
	function isJson(value) {
		try {
			eval('(' + value + ')');
			return true;
		} catch (ex) {
			return false;
		}
	}

</script>
</head>
   
<?php
	
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "Facturacion hospitalaria");

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "guardarFacturas"){
		guardarFacturas( $wfacturas );
	}else if( $action == "publicar_script" ){
		publicar_script();
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************

	function publicar_script()
	{
		global $wbasedato;
		global $conex;
		global $wusuario;
		
		$resul = array('OK'=>1, 'facturas'=>NULL, 'msj'=>'');
		
		if($_FILES['file_script']['error'] > 0){
			$resul['OK'] = 0;
			$resul['msj'] = 'Error cargando el archivo.';
			echo json_encode($resul);
			exit;
		}
		if($_FILES['file_script']['type'] != 'text/plain'){
			$resul['OK'] = 0;
			$resul['msj'] = 'Tipo de archivo incorrecto, solo se admite .txt';
			echo json_encode($resul);
			exit;
		}
		
		// --> variables recibidas
		$script			=  $_FILES['file_script']['name'];				//Nombre del script
		$tamano 		=  $_FILES['file_script']['size'];				//Tamaño del archivo en Kb
		$error 			=  $_FILES['file_script']['error'];				//Si apareció algún error en la subida
		$script_temp	=  $_FILES['file_script']['tmp_name'];			//Nombre temporal que se le asigna al archivo cuando sube
		
		$arr_facturas_no_guardadas = array();
		$guarda = false;
		$fh = fopen($script_temp,'r');
		while(!feof($fh)) {
			$lineafull = fgets($fh);
			$lineas = preg_split('/\n|\r\n?/', $lineafull);
			foreach( $lineas as $line){
				// $array1  = array( '?', 'ÿþ');
				// $line = utf8_decode($line);
				// $array1  = array( '?', '\u0000');
				// $array2  = array(  '', '' );
				// $line = str_replace( $array1, $array2, $line);
				if( trim($line) != "" ){
					// $line = preg_replace('/[^\p{L}\p{N}\s]/u', '', $line);
					// $line = utf8_decode($line);
					// echo $line." - ";
					if (preg_match('/^[0-9]+$/', $line)) {
						
						$q_inser = " INSERT INTO ".$wbasedato."_000022
												( 	Medico, 			Fecha_data,   		Hora_data, 			  Fblcod, 			Fblusu, 		Fblest,				Seguridad)
										 VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', 	'".$line."',			 '', 		'on',  		 'C-".$wusuario."' )
									";
						mysql_query($q_inser,$conex);
				
						if(! mysql_insert_id() ){
							array_push($arr_facturas_no_guardadas, $line);
						}else{
							$guarda = true;
						}					
					}else{
						$resul['msj'].= "'".$line."' no tiene el formato adecuado y no sera guardado.<br>";
					}
				}
			}
		}
		fclose($fh);
		
		if( $guarda == false ){
			$resul['OK'] = 0;
			$resul['msj'].= 'No se guardo ninguna factura.';
		}
		
		if( count( $arr_facturas_no_guardadas ) > 0 )
			$resul['facturas'] = $arr_facturas_no_guardadas;
		
		echo json_encode($resul);
	}
	
	function guardarFacturas( $wfacturas ){
		global $wbasedato;
		global $conex;
		global $wusuario;
		
		$q_inser = " UPDATE ".$wbasedato."_000022
						SET Fblest = 'on',
							Fecha_data = '".date("Y-m-d")."',
							Hora_data = '".date("H:i:s")."'
					  WHERE Fblcod IN (".implode(",",$wfacturas).")";
		
		mysql_query($q_inser,$conex);
		echo "OK";
	}
	
	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz, $wbasedato, $wmovhos, $conex;
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		encabezado("CARGA DE FACTURAS EN BLOQUE", $wactualiz, "clinica");

		echo"
		<div align='center' class='bordeAzul' style='margin-top:10px;'>
			<br>
			<form id='form_publicar'  method='post' enctype='multipart/form-data'>
			
			<table width='50%' align='center'>";
			
		//	-->	Selección de script
		echo"<tr class='fila2'>
				<td class='encabezadoTabla' align='center'>Archivo .txt:</td>				
			</tr>";		
		
		//	-->	Desarrollador
		echo"<tr class='fila2'>
				<td align='center' style='padding:3px;'>
					<input type='file' size=60 name='file_script' id='file_script'>
					<div align='right' id='div_file_script' style='display:none;'></div>
				</td>
			</tr>";
		echo "<tr>
				<td style='font-size:7pt;background-color:#F8FAB8'> * Carga un archivo plano (.txt) con los números de facturas que desea imprimir, debe haber una factura por línea y en una sola columna. (Sin encabezados y sin más columnas)</td>
			  </tr>";
			
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$boton = '<button>OK</button><br>';
		
		if(preg_match('/MSIE/i',$u_agent))
		{
			$boton = "<input type='submit' style='cursor:pointer;border: 1px solid #888888;color: #888888;width:70px;font-size: 12pt;' value='OK' onClick='publicar_script();'><br>";
		}
			
		// --> Boton OK
		echo"<tr>
				<td align='center' colspan=2 style='font-weight: bold;font-family: verdana;	font-size: 10pt;'><br>
					<div id='td_boton'>
					".$boton."					
					</div>
					<div id='msj_calculando' style='display:none'>
						Espere un momento 
						<img style='cursor:pointer;' width='23' height='23' title='Subiendo archivo...' src='images/medical/ajax-loader11.gif'>
					</div>
				</td>
			</tr>";
		//--> Hidden de la accion
		echo "<input type='hidden' id='tipo_accion' value='pintar_publicar' >";
		echo"
			</table>	

			<br><br>
			<div id='respuesta'>&nbsp;</div>
			</form>
		</div>";
		echo "<center><a class='enlace_retornar' href='#' >RETORNAR</a></center>";
		//Mensaje de espera		
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		
		echo '<div id="dialog-message" title="Mensaje">
			  <p id="dialog-text">				
				
			  </p>			  
			</div>';
		
		echo "<br><br>";
		
		echo "<center>";		
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "</center>";
	}
	
?>
 <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>