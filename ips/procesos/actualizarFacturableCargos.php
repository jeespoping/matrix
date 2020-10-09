<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-06-29';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
/*	2019-11-22: Jerson Trujillo. 	       
*/
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_SESSION['user']))
{
    if(isset($accion))
	{
		$respuesta['error'] = true;
		$respuesta['msj'] 	= 'Primero recargue la página principal de Matrix ó inicie sesión nuevamente, para poder relizar esta acción.';
		
		echo json_encode($respuesta);
		return;		
	}
	else
	{
		echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
					[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
				</div>';
		return;
	}
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	$consultaAjax	= '';
	include_once("root/comun.php");
	$conex 			= obtenerConexionBD("matrix");

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//---------------------------------------------------------
	// --> Traer Log de Actualizaciones
	//---------------------------------------------------------
	function getLog(){
		global $conex;
		global $bdCliame;
		
		$html = "<div style='font-size:7pt;font-family:verdana;font-weight:normal;'>";
		$sqTraeLog = "
		SELECT Fecha_data, Hora_data, Logusu, Logfac, Logids 
		  FROM ".$bdCliame."_000337
		ORDER BY Fecha_data, Hora_data DESC
		";
		$resTraeLog = mysql_query($sqTraeLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqTraeLog):".$sqTraeLog."</b><br>".mysql_error());	
		while($rowLog = mysql_fetch_array($resTraeLog)){
			$html.= "
			<b>Fecha:</b> ".$rowLog['Fecha_data']." ".$rowLog['Hora_data']." 
			<b>Usuario:</b> ".$rowLog['Logusu']."
			<b>Actualizado a:</b> ".$rowLog['Logfac']."
			<br>
			<b>Id: </b>".$rowLog['Logids']."
			<hr/>";
		}
		$html.= "</div>";
		
		return $html;
	}


//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	$respuesta 	= array("error" => false, "msj" => "");
	
	switch($accion)
	{
		case 'pintarLog':
		{			
			$respuesta['msj'] = getLog();
			break;
		}
		case 'ejecutarActualizar':
		{			
			$arrIdCargos 	= explode(",", $idCargosText);
			$arrArchCargos 	= explode(",", $contenidoArchivo);
			$arrCargos 		= array_unique(array_filter(array_map('trim', array_merge($arrIdCargos, $arrArchCargos))), SORT_NUMERIC);
			asort($arrCargos);
			
			$log 	= "";
			$numUpd = 0;
			
			if(is_array($arrCargos)){
				foreach($arrCargos as $idCargo){
					$sqlUpd = "
					UPDATE ".$bdCliame."_000106
					   SET Tcarfac = '".$cambiarASel."'
					 WHERE id = ".$idCargo."
					   AND Tcarfex = 0
					   AND Tcarfre = 0
					";
					$res = mysql_query($sqlUpd, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpd):".$sqlUpd."</b><br>".mysql_error()); 
					if(mysql_affected_rows($conex) > 0){
						$log = $log.$idCargo.",";
						$numUpd++;
					}
				}
			}else{
				$respuesta['error'] = true;
				$respuesta['msj'] 	= "No se pudo leer los id para actualizar, revise que esten separados por ,";
			}
			
			if($log != ""){
				$sqLog = "
				INSERT INTO ".$bdCliame."_000337
				  SET Medico 	= '".$bdCliame."',
				   Fecha_data 	= '".date('Y-m-d')."',
					Hora_data  	= '".date("H:i:s")."',
					   Logusu 	= '".$wuse."', 
					   Logfac 	= '".$cambiarASel."', 
					   Logids 	= '".$log."', 
					Seguridad	= 'C-".$bdCliame."'
				";
				mysql_query($sqLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqLog):".$sqLog."</b><br>".mysql_error());				
			}
			
			$diff = (count($arrCargos)-$numUpd);
			
			$respuesta['msj'] = "Actualizacion Realizada:
			registros leidos (".count($arrCargos)."), registros actualizados (".$numUpd."),
			".(($diff > 0) ? "(".$diff.") registros no se actualizaron, puede ser porque ya estan en el mismo estado o ya estan facturados." : "")."";
			
			break;
		}
	}
	
	$respuesta['msj'] = utf8_encode($respuesta['msj']);
	echo json_encode($respuesta);
	return;
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{
	?>
	<html>
	<head>
	  <title>Actualizar Estado Facturable de Cargos</title>
	</head>	
		<meta charset="UTF-8">
		
		<script src="../../../include/root/jquery.min.js"></script>
		<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
		<script src="../../../include/root/bootstrap.min.js"></script>
		
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>		
		
		<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
		
		
	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	var contenidoArchivo = "";
	
	$(function(){
		pintarLog();
		document.getElementById('idCargosFile').addEventListener('change', leerArchivo, false);  
	});
	
	function leerArchivo(e){
		var archivo = e.target.files[0];
		if (!archivo || archivo.type != "text/plain"){
			$("#idCargosFile").val("");
			return;
		}
		var lector = new FileReader();
			lector.onload = function(e) {
			contenidoArchivo = e.target.result;
		};
		lector.readAsText(archivo);
	}
	
	//--------------------------------------------------------
	//	--> Actualizar monitor
	//--------------------------------------------------------
	function pintarLog(){
		
		$.post("actualizarFacturableCargos.php",
		{	
			accion			:   'pintarLog',
			wemp_pmla		:	$('#wemp_pmla').val(),
			bdCliame		:	$('#bdCliame').val()
		}, function(respuesta){	
			$("#divLog").html(respuesta.msj);
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Actualizar monitor
	//--------------------------------------------------------
	function ejecutar(){
		
		$("#divMsj").hide();
		
		idCargosText 	= $("#idCargosText").val();
		cambiarASel 	= $("#cambiarASel").val();
		
		if(contenidoArchivo == "" && idCargosText ==""){
			$("#divMsj").attr("class", "alert alert-danger").text("Debe digitar o cargar los id.").show();
			return;
		}
		
		if(cambiarASel == ""){
			$("#divMsj").attr("class", "alert alert-danger").text("Debe seleccionar el nuevo estado.").show();
			return;
		}
		
		$("#modalCosultando").modal('show');
		
		$.post("actualizarFacturableCargos.php",
		{	
			accion			:   'ejecutarActualizar',
			wemp_pmla		:	$('#wemp_pmla').val(),
			bdCliame		:	$('#bdCliame').val(),
			idCargosText	:	idCargosText,
			contenidoArchivo:	contenidoArchivo,
			cambiarASel		:	cambiarASel			

		}, function(respuesta){	
			if(respuesta.error){
				$("#divMsj").attr("class", "alert alert-danger").text(respuesta.msj).show();
			}
			else{				
				$("#divMsj").attr("class", "alert alert-success").text(respuesta.msj).show();				
				$("#idCargosText").val("");
				$("#cambiarASel").val("");
				$("#idCargosFile").val("");
				pintarLog();
			}
			
			$("#modalCosultando").modal('hide');
			
		}, 'json');
		
		
	}
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>

<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		.fila1
		{
			background-color: 	#C3D9FF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fila2
		{
			background-color: 	#E8EEF7;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		.titulopagina2
		{
			border-bottom-width: 1px;
			/*border-color: <?=$bordemenu?>;*/
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
		.wn
		{
			font-weight: normal;
		}
		.addScroll{
			overflow-y:auto;
			height: 63%;
		}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY width="100%">
	<?php
	
	// -->	ENCABEZADO
	encabezado("<div class='titulopagina2'>Actualizar Estado Facturable En Cargos</div>", $wactualiz, 'clinica');
	$bdCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	
	echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='bdCliame' value='".$bdCliame."'>";
	?>
	<div class="container" style="width:100%;padding:0px;" align="center">
		<div id="divMsj" class="" role="alert" style="width:40%;display:none;padding:0px;">						
		</div>
		<div class="form text-left" style="width:70%; padding:0px;">
			<div class="row">
				<div class="col-sm-6">  
					<div class="panel panel-info">
						<div class="panel-heading">Nueva Actualizaci&oacute;n</div>
						<div class="panel-body">
							<form>
							  <div class="form-group">
								<label for="exampleInputEmail1">Ingrese el Id de los cargos:</label>
								<textarea class="form-control" id="idCargosText" rows="3"></textarea>
								<small id="emailHelp" class="form-text text-muted">
									Digite los id de los cargos que desea cambiarles el estado facturable. Separados por coma (,).
								</small>
							  </div>
							  <div class="form-group">
								<label for="exampleFormControlFile1">O cargue un archivo con lista de id de los cargos:</label>
								<input type="file" class="form-control-file" id="idCargosFile">
								<small id="emailHelp" class="form-text text-muted">
									<b>Solo archivos .txt</b> con los id separados por coma (,).
								</small>
							  </div>
							  <div class="form-group">
								<label for="exampleFormControlSelect2">Cambiar a</label>
								<select class="form-control" id="cambiarASel">
								  <option value=""></option>
								  <option value="N">No Facturable</option>
								  <option value="S">Facturable</option>
								 </select>
							  </div>
							  <button type="button" class="btn btn-primary" onClick="ejecutar()">Cambiar Estado</button>
							</form>
						</div>
					</div>
				</div>	
				<div class="col-sm-6"> 
					<div class="panel panel-info">
						<div class="panel-heading">Log de Actualizaciones</div>
						<div class="panel-body addScroll" id="divLog">
						</div>
					</div>
				</div>
			</div>
		</div>		
	</div>
	
	
	<div id="modalCosultando" class="modal fade bs-example-modal-sm" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content">
				<div class="modal-body" align="center">
					<p>Procesando...<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></p>
				</div>
			</div>
		</div>
	</div>

	</BODY>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
