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
			$wactualiz='2020-07-04';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
/*	2020-07-04: Jerson Trujillo. 	       
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
		SELECT Fecha_data, Hora_data, Descripcion, Loghis, Logina, Loginn, Logids 
		  FROM ".$bdCliame."_000338 INNER JOIN usuarios ON (Logusu = Codigo)
		 ORDER BY Fecha_data DESC, Hora_data DESC
		";
		$resTraeLog = mysql_query($sqTraeLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqTraeLog):".$sqTraeLog."</b><br>".mysql_error());	
		while($rowLog = mysql_fetch_array($resTraeLog)){
			$html.= "
			<b>Fecha:</b> ".$rowLog['Fecha_data']." ".$rowLog['Hora_data']." 
			<b>Usuario:</b> ".$rowLog['Descripcion']."
			<b>Historia:</b> ".$rowLog['Loghis']."
			<b>Ingreso Anterior:</b> ".$rowLog['Logina']."
			<b>Ingreso Nuevo:</b> ".$rowLog['Loginn']."
			<b>Id Abonos Actualizados: </b>".$rowLog['Logids']."
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
		case 'cambiarIngreso':
		{
			$idCambiar = "'".implode(json_decode($idCambiar, true), "','")."'";;
			
			$sqlUpd = "
			UPDATE ".$bdCliame."_000106
			   SET Tcaring = '".$nuevoIngreso."'
			 WHERE id IN(".$idCambiar.")
			";
			mysql_query($sqlUpd, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpd):".$sqlUpd."</b><br>".mysql_error());	
			
			$sqlUpd2 = "
			UPDATE ".$bdCliame."_000021
			   SET Rdeing = '".$nuevoIngreso."'
			 WHERE Rdereg IN(".$idCambiar.")
			";
			mysql_query($sqlUpd2, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpd2):".$sqlUpd2."</b><br>".mysql_error());

			$sqLog = "
			  INSERT INTO ".$bdCliame."_000338
			  SET  Medico 	= '".$bdCliame."',
			   Fecha_data 	= '".date('Y-m-d')."',
				Hora_data  	= '".date("H:i:s")."',
				   Logusu 	= '".$wuse."', 
				   Loghis 	= '".$historia."', 
				   Logina 	= '".$ingresoAnt."', 
				   Loginn 	= '".$nuevoIngreso."', 
				   Logids 	= '".str_replace("'", "", $idCambiar)."', 
				Seguridad	= 'C-".$bdCliame."'
			";
			mysql_query($sqLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqLog):".$sqLog."</b><br>".mysql_error());
			
			$respuesta['msj'] = "Actualizacion Correcta";
			
			break;
		}
		case 'buscarAbonos':
		{
			$html 		= "<div style='font-size:7pt;font-family:verdana;font-weight:normal;'>";
			$html 		= "<small class='form-text text-muted'>";
			$htmlAbo 	= "";
			
			// --> Consultar abonos
			$sqlAbonos 	= "
			SELECT CONCAT(Tcarno1, ' ', Tcarno2, ' ', Tcarap1, ' ', Tcarap2) NomPac, Tcarfec, Tcarconcod, 
				   Tcarconnom, Tcarvto, Tcarfex, Tcarfre, A.id    
			  FROM ".$bdCliame."_000106 A INNER JOIN ".$bdCliame."_000004 ON(Tcarconcod = Grucod AND Gruabo = 'on')
			 WHERE Tcarhis = '".$historia."'
			   AND Tcaring = '".$ingreso."'
			   AND Tcarest = 'on'
			 ORDER BY Tcarfec
			";
			$resAbonos = mysql_query($sqlAbonos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAbonos):".$sqlAbonos."</b><br>".mysql_error());	
			$c = 0;
			while($rowAbonos = mysql_fetch_array($resAbonos)){
				$c++;
				$nomPac = $rowAbonos['NomPac'];
				$htmlAbo.= "
				".$c.") <b>Fecha:</b> ".$rowAbonos['Tcarfec']."
				<b>Concepto:</b> ".$rowAbonos['Tcarconcod']."-".$rowAbonos['Tcarconnom']."
				<br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Valor:</b> $".number_format(abs($rowAbonos['Tcarvto']),0,".",",")."
				<b>Facturado:</b> $".number_format(abs($rowAbonos['Tcarfex']),0,".",",")."(E) , $".number_format(abs($rowAbonos['Tcarfre']),0,".",",")."(R) 
				";
				if($rowAbonos['Tcarfex'] == 0 && $rowAbonos['Tcarfre'] == 0){
					$htmlAbo.= "
					<div class='has-success'>
					  <div class='checkbox'>
						<label>
						  <input type='checkbox' chkCambiarAbono value='".$rowAbonos['id']."'>
						  Seleccionar para cambiarle el numero de ingreso
						</label>
					  </div>
					</div>";
				}
					
				$htmlAbo.= "
					<hr/>";
			}
			
			if($c > 0){
				
				// --> Consultar ingresos
				$arrIng = array();
				$sqlIng = "
				SELECT Ingnin
				  FROM ".$bdCliame."_000101
				 WHERE Inghis = '".$historia."'
				   AND Ingnin != '".$ingreso."'
				 ORDER BY Ingnin
				";
				$resIng = mysql_query($sqlIng, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlIng):".$sqlIng."</b><br>".mysql_error());	
				while($rowIng = mysql_fetch_array($resIng))
					$arrIng[] = $rowIng['Ingnin'];
				
				$html.= "<div class='active'><b>Paciente: </b>".$nomPac."</div><hr/><b>Abonos:<br><br></b>".$htmlAbo;
				$html.= '
					<form class="form-inline">
						<div class="form-group">
							<label>Cambiar abonos seleccionados al ingreso:</label>
							<select class="form-control" id="nuevoIngreso">
								<option value="">Seleccione...</option>';
							foreach($arrIng as $ing)
								$html.= '<option value="'.$ing.'">'.$ing.'</option>';
				$html.= '
							</select>
						</div>
					</form>	
					<button type="button" class="btn btn-primary" onClick="cambiarIngreso()">Cambiar Ingreso</button>
				';
			}
			else
				$html.="No se encontraron abonos para esta historia e ingreso";
			
			$respuesta['msj'] = $html."</small>";
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
	  <title>Cambiar Abono a Otro Ingreso</title>
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
	});
	
	//--------------------------------------------------------
	//	--> 
	//--------------------------------------------------------
	function pintarLog(){
		
		$.post("actualizarAbonosOtroIngreso.php",
		{	
			accion			:   'pintarLog',
			wemp_pmla		:	$('#wemp_pmla').val(),
			bdCliame		:	$('#bdCliame').val()
		}, function(respuesta){	
			$("#divLog").html(respuesta.msj);
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Buscar abonos
	//--------------------------------------------------------
	function buscarAbono(){
		
		$("#divMsj").hide();
		
		historia 	= $("#inHistoria").val();
		ingreso 	= $("#inIngreso").val();
		
		if(historia == "" && ingreso == ""){
			$("#divMsj").attr("class", "alert alert-danger").text("Debe ingresar la historia y el ingreso.").show();
			return;
		}
		
		$("#modalCosultando").modal('show');
		
		$.post("actualizarAbonosOtroIngreso.php",
		{	
			accion			:   'buscarAbonos',
			wemp_pmla		:	$('#wemp_pmla').val(),
			bdCliame		:	$('#bdCliame').val(),
			historia		:	historia,
			ingreso			:	ingreso		

		}, function(respuesta){	
			if(respuesta.error){
				$("#divMsj").attr("class", "alert alert-danger").text(respuesta.msj).show();
			}
			else{				
				$("#divAbonos").html(respuesta.msj);				
			}
			
			$("#modalCosultando").modal('hide');
			
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Cambiar abonos de ingreso
	//--------------------------------------------------------
	function cambiarIngreso(){
		
		$("#divMsj").hide();
		
		var idCambiar = new Array();
		$("[chkCambiarAbono]:checked").each(function(){
			idCambiar.push($(this).val());
		});
		
		nuevoIngreso 	= $("#nuevoIngreso").val();
		historia 		= $("#inHistoria").val();
		ingresoAnt 		= $("#inIngreso").val();
		
		if(historia == "" && ingreso == ""){
			$("#divMsj").attr("class", "alert alert-danger").text("Debe ingresar la historia y el ingreso.").show();
			return;
		}
		
		if(idCambiar.length == 0){
			$("#divMsj").attr("class", "alert alert-danger").text("No ha seleccionado ningun abono.").show();
			return;
		}
		
		if(nuevoIngreso == ""){
			$("#divMsj").attr("class", "alert alert-danger").text("No ha seleccionado el nuevo ingreso del abono.").show();
			return;
		}
		
		$("#modalCosultando").modal('show');
		
		$.post("actualizarAbonosOtroIngreso.php",
		{	
			accion			:   'cambiarIngreso',
			wemp_pmla		:	$('#wemp_pmla').val(),
			bdCliame		:	$('#bdCliame').val(),
			historia		:	historia,
			ingresoAnt		:	ingresoAnt,
			nuevoIngreso	:	nuevoIngreso,
			idCambiar		:	JSON.stringify(idCambiar)		

		}, function(respuesta){	
			if(respuesta.error){
				$("#divMsj").attr("class", "alert alert-danger").text(respuesta.msj).show();
			}
			else{				
				buscarAbono();
				pintarLog();
				$("#divMsj").attr("class", "alert alert-success").text(respuesta.msj).show();					
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
	encabezado("<div class='titulopagina2'>Cambiar Abonos a Otro Ingreso</div>", $wactualiz, 'clinica');
	$bdCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	
	echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='bdCliame' value='".$bdCliame."'>";
	?>
	<div class="container" style="width:100%;padding:0px;" align="center">
		<div id="divMsj" class="" role="alert" style="width:40%;display:none;padding:0px;">						
		</div>
		<div class="form text-left" style="width:80%; padding:0px;">
			<div class="row">
				<div class="col-sm-6">  
					<div class="panel panel-info">
						<div class="panel-heading">Nuevo cambio</div>
						<div class="panel-body">
							<small class="form-text text-muted">
								Ingrese la historia y el ingreso del paciente, al que le desea hacer el cambio de abono. 
							</small>
							<form class="form-inline">
								<div class="form-group">
									<label>Historia:</label>
									<input type="text" class="form-control" id="inHistoria" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
								</div>
								<div class="form-group">
									<label>Ingreso:</label>
									<input type="text" class="form-control" id="inIngreso" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
								</div>
							</form>	
							<button type="button" class="btn btn-primary" onClick="buscarAbono()">Buscar Abonos</button>
						</div>
						<div class="panel-footer" id="divAbonos">
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
