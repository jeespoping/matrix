<?php

/**DESCRIPCIÓN 27 DE OCTUBRE DEL 2021 
 * Este programa muestra el listado de entregas de nutriciones que se han hecho.
 * En este archivo lo unico que se vera reflejado es la parte front (todo lo que vera el usuario.)
*/

$consultaAjax = '';

include_once("conex.php");
include_once("root/comun.php");


if(!isset($_SESSION['user'])){
	 echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
	   <tr><td>Error, inicie nuevamente</td></tr>
	   </table></center>";
	 return;
} 

$wactivolactario = consultarAliasPorAplicacion( $conex, $wemp_pmla, "ProyectoLactario" );

if( $wactivolactario == 'off' ){
	echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Favor contacte a servicio de alimentaci&oacute;n, este programa no esta habilitado.</td></tr>
		</table></center>";
  	 return;
}

$fecha = date('Y-m-d');

$wactualiz="Octubre 27 de 2021";         

  encabezado("REPORTE DE ENTREGA LACTARIO",$wactualiz, $wemp_pmla,true);

?>
<html>

	<head>
		<meta content="text/html; charset=UTF8" http-equiv="content-type">
		<title>Reporte Entrega Lacatarios</title>
					<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
					<link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
					<link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
					<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
					<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
					<link type="text/css" href="../../../include/root/jquery.simpletree.css" rel="stylesheet" />
					<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
					<link
						href="Lactarios/datatables/dataTables.bootstrap4.css"
						rel="stylesheet" type="text/css" />
					<link
						href="Lactarios/datatables/responsive.bootstrap4.css"
						rel="stylesheet" type="text/css" />
					<link
						href="Lactarios/datatables/buttons.bootstrap4.css"
						rel="stylesheet" type="text/css" />
					<link
						href="Lactarios/datatables/select.bootstrap4.css"
						rel="stylesheet" type="text/css" />
					<link href="Lactarios/bootstrap.min.css"
						rel="stylesheet" type="text/css" />					
					
					


					<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
					<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
					<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
					<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
				

					<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
					<!-- <script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script> -->	<!-- Autocomplete -->
					<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
					<!-- <script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script> -->
					<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
					<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

					<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
					<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
					
					<script type="text/javascript" src="../../../include/root/datatables.min.js"></script>
					<script type="text/javascript" src="Lactarios/moment.min.js"></script>
					<link type='text/css' href="Lactarios/daterangepicker.css"  rel="stylesheet" />
					<script type="text/javascript" src="Lactarios/daterangepicker.js"></script>
					
					<script
						src="Lactarios/datatables/jquery.dataTables.min.js"></script>
					<script
						src="Lactarios/datatables/dataTables.bootstrap4.js"></script>
					

					<script
						src="Lactarios/datatables/responsive.bootstrap4.min.js"></script>
					<script
						src="Lactarios/datatables/dataTables.buttons.min.js"></script>
					<script
						src="Lactarios/datatables/buttons.bootstrap4.min.js"></script>
					<script
						src="Lactarios/datatables/buttons.html5.min.js"></script>
					<script
						src="Lactarios/datatables/buttons.flash.min.js"></script>
					<script
						src="Lactarios/datatables/buttons.print.min.js"></script>



					<script type="text/javascript" src="Lactarios/lactareos.js"></script>
					<script>
						/* Filtro sede */
						jQuery(document).ready(function($){

							$('#selectsede').change(function(e){
								localStorage.setItem('sede',$(this).val());

								setTimeout(function() {
										$('#activar-sede').trigger('click');
								}, 1000);

							});

						});
					</script>
					
	</head>
	<style>
			table {
			display: table;
			border-collapse: separate;
			box-sizing: border-box;
			text-indent: initial;
			white-space: normal;
			line-height: normal;
			font-weight: normal;
			font-size: medium;
			font-style: normal;
			color: -internal-quirk-inherit;
			text-align: start;
			border-spacing: 2px;
			border-color: grey;
			font-variant: normal;
		}
		.fila1 {
			background-color: #C3D9FF;
			color: #000000;
			font-size: 10pt;
		}
		.fila2 {
			background-color: #E8EEF7;
			color: #000000;
			font-size: 10pt;
		}
		.tituloPagina {
			font-family: verdana;
			font-size: 18pt;
			overflow: hidden;
			text-transform: uppercase;
			font-weight: bold;
			height: 30px;
			border-top-color: #2A5DB0;
			border-top-width: 1px;
			border-left-color: #2A5DB0;
			border-left-width: 1px;
			border-right-color: #2A5DB0;
			border-bottom-color: #2A5DB0;
			border-bottom-width: 1px;
			margin: 2pt;
		}
		.encabezadoTabla {
			background-color: #2A5DB0;
			color: #FFFFFF;
			font-size: 10pt;
			font-weight: bold;
		}


		.seccion1 {
			background-color: #C3D9FF;
		}
		.textoNormal {
			background: #FFFFFF;
			font-size: 12px;
			font-family: Tahoma;
			font-weight: normal;
			text-align: left;
			height: 20px;
			border-top-width: 1px;
			border-left-width: 1px;
			border-right-width: 2px;
			border-bottom-width: 2px;
			padding: 5px;
			padding-top: 1px;
			padding-bottom: 2px;
			background-attachment: #FFFFFF;
			background-color: #FFFFFF;
			background-position: #FFFFFF;
			background-repeat: #FFFFFF;
		}
		.entregado {
			color: #1abc9c !important;
		}
		.cancelado {
			color: #f1556c !important;
		}
	</style>	
	
		<body style="padding-left: 15px; !important">
			<div class="row justify-content-center align-items-center" style="display: none; ">
				<label class="control-label ">Rango de fecha:</label> 
				<input class="col-xl-4 col-md-4 col-4 custom-select custom-select-sm " type="text"
						name="daterange" id="filtro" readonly 
						style="min-width: 177px; display: inline-block;">
				<br>
				<br>
				<!-- <button type="button" class="btn btn-success waves-effect waves-light" id="generar">Generar</button> -->
				<br>
				<br>
			</div>
			<table align="center" cellspacing="2" >
				<tr class="seccion1">
					<td align="center" height="61px" width="140px"><b>Fecha Inicial</b><br>
						<?php 
							echo '<INPUT TYPE="text" NAME="wfecha_i" id="wfecha_i" value="'.$fecha.'" size="11" readonly class="textoNormal">'
						?>
						
					
					</td>
					<td align="center" height='61px' width='140px'><b>Fecha Final</b><br>
						<?php 
								echo '<INPUT TYPE="text" NAME="wfecha_f" id="wfecha_f" value="'.$fecha.'" size="11" readonly class="textoNormal">'
						?>
						
					</td>
			
				</tr>
				<tr>
					<td colspan="2" align="center">
					<button type="button" class="btn btn-info waves-effect waves-light" id="generar">Generar</button>
					</td>
				</tr>
			</table>	
			<div style="width:85%;">
			<table id="lactarios" class="table-striped" style=" width:100%; height:20;">
				<thead 	class="encabezadoTabla ">
					<tr>
						
						<th>Historia</th>
						<th>Ingreso</th>
						<th>Paciente</th>
						<th>Articulo</th>
						<th>Cantidad</th>
						<th>Entrego</th>
						<th>Recibio</th>
						<th>Fecha de entrega</th>
						<th>Hora de entrega</th>
						<th>Estado</th>
					</tr>
				</thead>
				<tbody class="table-striped">
					
				
				</tbody>
			</table>
			</div>
			
			
	
		</body>
</html>
