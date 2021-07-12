<?php 
	include_once("root/comun.php");
	include_once("./Autorizacion_Medicamentos_Backend.php");

	$wactualiz='Julio 27 de 2021';
	
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	
	encabezado( "AUTORIZACION DE MEDICAMENTOS", $wactualiz, $institucion->baseDeDatos );
	
	list($id, $user) = explode("-",$_SESSION['user']);
	$director = puedeAutorizarArticulos( $conex, $wmovhos, $user );
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorizacion Medicamentos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="datatables.min.css" >
            
	<style type="text/css">
		table.striped > tbody > tr:nth-child(odd) {
			background-color: #c4dcfc;
			font-size: 10pt;
		}
		table.striped > tbody > tr:nth-child(even) {
			background-color: #ececf4;
			font-size: 10pt;
		}
		td{
			color: #000066;
		}
	</style>
</head>


<body>
	

  <div class="container" style="width:100%;max-width:2000px;" >
  
	<div id='dvTabs'>
  
		<ul>
			<li><a href="#dvAutorizacionesPendientes">Autorizaciones</a></li>
			<li><a href="#dvLog">Log</a></li>
		</ul>
		
		<div id="dvAutorizacionesPendientes" style="width:100%;" >
	  
			<table id="table" class="striped" style="margin:20px; ">
				<thead >
					<tr class=encabezadotabla >
				
						<th scope="col" style=" background: #000066;">Paciente</th>
						<th scope="col" style=" background: #000066;">Cod Medicamento</th>
						<th scope="col"style=" background: #000066;">Nombre Medicamento</th>
						<th scope="col"style=" background: #000066;">Cod Medico ordena</th>
						 <th scope="col"style=" background: #000066;">Nombre Medico</th>
						<th scope="col"style=" background: #000066;">Justificacion Ordenamiento</th>
						<th scope="col"style=" background: #000066;">Justificacion Autorizacion</th>
						<th scope="col"style=" background: #000066;">Autoriza?</th>
						<?php
							if( $director ){
								echo' <th scope="col" style=" background: #000066;">Accion</th>';
							}
						?>
			   
					</tr>
				</thead>
			<tbody>
			<?php 
			
			$wemp_pmla=$_GET['wemp_pmla'];
			
			$autorizaiones = cargartabla( $conex, $wemp_pmla );
			
			// $a = consultarLog( $conex, "movhos" );
			// var_dump($a);
			
			if( $autorizaiones>0)
			{  
				foreach ($autorizaiones as  $autorizaion)
				{
					echo '<tr id="tr'.$autorizaion["consecutivo"].'" class="'.$autorizaion["historia"]."-".$autorizaion["ingreso"]."-".$autorizaion["codigo"].'">';
					echo '<td scope="row" id="paciente'.$autorizaion["historia"]."-".$autorizaion["ingreso"].'">'.$autorizaion["historia"]."-".$autorizaion["ingreso"]." ".$autorizaion["nombre_paciente"].'</td>';//Cod Medicamento
					echo '<td scope="row" id="codigo'.$autorizaion["consecutivo"].'">'.$autorizaion["codigo"].'</td>';//Cod Medicamento
					echo '<td scope="row" id="n_medicamento'.$autorizaion["consecutivo"].'" >'.$autorizaion["nombre_medicamento"].'</td>';//Nombre Medicamento
					echo '<td scope="row" id="c_medico_ordena'.$autorizaion["consecutivo"].'">'.$autorizaion["usuario_ordena"].'</td>';//Cod Medico ordena
					echo '<td scope="row" id="n_medico'.$autorizaion["consecutivo"].'">'.$autorizaion["nombre_medico"].'</td>';//Cod Medico ordena
					echo '<td scope="row" id="j_medico_ordena'.$autorizaion["consecutivo"].'">'.$autorizaion["justificacion_ordena"].'</td>';//Justificacion Ordenamiento
				  
					echo '<td><textarea name="" id="j_medico_autoriza'.$autorizaion["consecutivo"].'" cols="30" rows="5"></textarea></td>';
					
					echo '<td>'; 
					echo '<select name="" id="autoriza'.$autorizaion["consecutivo"].'">';
					echo '<option value="">Selecione...</option>';
					echo '<option value="on">Si</option>';
					echo '<option value="off">No</option>';
					echo '</select>'; 
					
					echo '<input id="historia'.$autorizaion["consecutivo"].'" name="" type="hidden" value='.$autorizaion["historia"].'>';//Justificacion Ordenamiento
					echo '<input id="ingreso'.$autorizaion["consecutivo"].'" name="" type="hidden" value='.$autorizaion["ingreso"].'>';//Justificacion Ordenamiento
					
					echo '</td>';
					
					$codigo   = $autorizaion["consecutivo"];
					
					if( $director ){
						echo'<td scope="row"><button type="button" id="enviar'.$autorizaion["codigo"].'" onclick="guardar(\'' .$codigo . '\')" class="btn btn-primary  align-items-center" >Guardar</button></td>';
					}
					  
					echo' </tr>';
				}
			} 
			?>
			</tbody>
			</table>
			
			<?=' <input id="empresa"  type="hidden" value='.  $wemp_pmla.'>';?>
			
			<form>
				<?=' <input name="wemp_pmla"  type="hidden" value='.  $wemp_pmla.'>';?>
			</form>

			<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="crossorigin="anonymous"></script>-->
			
			<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
			<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
			<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
			
			
			
			
			
			
			
			
			
			<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
			<link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
			<link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
			<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
			<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
			<link type="text/css" href="../../../include/root/jquery.simpletree.css" rel="stylesheet" />
			<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
			<link type='text/css' href='../../../include/root/matrix.css' rel='stylesheet'>		<!-- HCE -->
			<link type='text/css' href='../../../include/root/burbuja.css' rel='stylesheet'>		<!-- HCE -->
			<link type='text/css' href="../../../include/root/jquery.ui.timepicker.css"  rel="stylesheet" />

			<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
			<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
			<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>	<!-- Acordeon -->
			<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
			<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
			<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
			<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
			<script type="text/javascript" src="../../../include/root/modernizr.custom.js"></script>

			<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
			<!-- <script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script> -->	<!-- Autocomplete -->
			<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
			<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
			<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
			<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
			<script type="text/javascript" src="../../../include/root/ui.datepicker.js"></script>
			<script type="text/javascript" src="../../../include/root/burbuja.js"></script>
			<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
			<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>

			
			
			
			
			
			
			
			
			
	   
			<script src="datatables.min.js"></script>   
			<script src="Autorizacion_medicamentos.js"></script>
			
			
			
		</div>
		
		
		<div id="dvLog" style="width:100%;">
			
			<div style='margin:0 auto;width:500px;padding:10px;' class='fila1'>
				<div style='width:100%;padding:5px;text-align:center;' class='encabezadotabla'>
					Busqueda por historia
				</div>
				<div style='width:100%;'>
					<input type='text' id='logHis' value='' placeholder='Ingrese la historia'>
					<input type='text' id='logIng' value='' placeholder='Ingrese el ingreso'>
					<input type='button' id='consultarLog' value='Buscar'>
				</div>
			</div>
			
			
			<table id="tableLog" class="striped" style="width:100%;margin :20px;">
				<thead>
					<tr class='encabezadotabla'>
						<th>Historia</th>
						<th>Paciente</th>
						<th>Fecha</th>
						<th>Mensaje</th>
						<th>Director M&eacute;dico</th>
					</tr>
				</thead>
			</table>
			
		</div>
	
	</div>
	
	
  </div>
</body>
</html>
