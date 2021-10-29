<?php
$consultaAjax = '';

include_once("conex.php");
include_once("root/comun.php");


$wactualiz = "2018-08-13";

if(!isset($_SESSION['user'])){
	 echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
	   <tr><td>Error, inicie nuevamente</td></tr>
	   </table></center>";
	 return;
} 



$wactualiz="Octubre 27 de 2021";         

  encabezado("REPORTE DE ENTREGA LACTARIOS",$wactualiz, "clinica");

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
					
					
	</head>
		<body>
			<div class="row justify-content-center align-items-center">
				<label class="control-label ">Rango de fecha:</label> 
				<input class="col-xl-4 col-md-4 col-4 custom-select custom-select-sm " type="text"
						name="daterange" id="filtro" readonly 
						style="min-width: 177px; display: inline-block;">
				<br>
				<br>
				<button type="button" class="btn btn-success waves-effect waves-light" id="generar">Generar</button>
				<br>
				<br>
			</div>
			<table id="lactarios" class=" table " style="width:100%">
				<thead 	class="thead-light">
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
					</tr>
				</thead>
				<tbody>
					
				
				</tbody>
			</table>
			
	
		</body>
</html>
