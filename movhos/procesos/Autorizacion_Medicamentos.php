<?php 
	include_once("root/comun.php");
	include_once("Autorizacion_Medicamentos_Backend.php");

	$wactualiz='Julio 27 de 2021';
	
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	
	encabezado( "AUTORIZACION DE MEDICAMENTOS", $wactualiz, $institucion->baseDeDatos );
	
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
     table.striped tr:nth-child(odd) {
       background-color: #c4dcfc;
	}
	table.striped tr:nth-child(even) {
	   background-color: #ececf4;
	}
	td{
	 color: #000066;
	}
   </style>
</head>
<body>
	

  <div class="container" style="width:100%;" >
	
  
	<?php 
		echo' <table id="table" class="encabezado tabla striped" style="margin:20px; ">';?>
		<thead >
       
			<th scope="col" style=" background: #000066; color:#C3D9FF">Paciente</th>
			<th scope="col" style=" background: #000066; color:#C3D9FF">Cod Medicamento</th>
			<th scope="col"style=" background: #000066; color:#C3D9FF">Nombre Medicamento</th>
			<th scope="col"style=" background: #000066; color:#C3D9FF">Cod Medico ordena</th>
			 <th scope="col"style=" background: #000066; color:#C3D9FF">Nombre Medico</th>
			<th scope="col"style=" background: #000066; color:#C3D9FF">Justificacion Ordenamiento</th>
			<th scope="col"style=" background: #000066; color:#C3D9FF">Justificacion Autorizacion</th>
			<th scope="col"style=" background: #000066; color:#C3D9FF">Autoriza?</th>
			<?php
				$director=$_GET['director'];
				if(  $director==="on"){
					echo' <th scope="col" style=" background: #000066; color:#C3D9FF">Accion</th>';
				}
			?>
       
    </thead>
    <tbody>
    <?php 
    
	$wemp_pmla=$_GET['wemp_pmla'];
	
    $autorizaiones = cargartabla($conex,$wemp_pmla);
	
	if( $autorizaiones>0)
	{  
		foreach ($autorizaiones as  $autorizaion)
		{
			echo '<tr id="tr'.$autorizaion["codigo"].'" >';
			echo '<td scope="row" id="paciente'.$autorizaion["historia"]."-".$autorizaion["ingreso"].'">'.$autorizaion["historia"]."-".$autorizaion["ingreso"]." ".$autorizaion["nombre_paciente"].'</td>';//Cod Medicamento
			echo '<td scope="row" id="codigo'.$autorizaion["codigo"].'">'.$autorizaion["codigo"].'</td>';//Cod Medicamento
			echo '<td scope="row" id="n_medicamento'.$autorizaion["codigo"].'" >'.$autorizaion["nombre_medicamento"].'</td>';//Nombre Medicamento
			echo '<td scope="row" id="c_medico_ordena'.$autorizaion["codigo"].'">'.$autorizaion["usuario_ordena"].'</td>';//Cod Medico ordena
			echo '<td scope="row" id="n_medico'.$autorizaion["codigo"].'">'.$autorizaion["nombre_medico"].'</td>';//Cod Medico ordena
			echo '<td scope="row" id="j_medico_ordena'.$autorizaion["codigo"].'">'.$autorizaion["justificacion_ordena"].'</td>';//Justificacion Ordenamiento
		  
			echo '<td><textarea name="" id="j_medico_autoriza'.$autorizaion["codigo"].'" cols="30" rows="5"></textarea></td>';
			
			echo '<td><select name="" id="autoriza'.$autorizaion["codigo"].'">';
			echo '<option value="on">Si</option>';
			echo '<option value="off" selected>No</option>';
			echo '</select></td>';
			
			echo '<input id="historia'.$autorizaion["codigo"].'" name="" type="hidden" value='.$autorizaion["historia"].'>';//Justificacion Ordenamiento
			echo '<input id="ingreso'.$autorizaion["codigo"].'" name="" type="hidden" value='.$autorizaion["ingreso"].'>';//Justificacion Ordenamiento
			
			$director=$_GET['director'];
			$codigo = "".$autorizaion["codigo"];
			
			if(  $director==="on"){
				echo'<td scope="row"><button type="button" id="enviar'.$autorizaion["codigo"].'" onclick="guardar(\'' .$codigo . '\')" class="btn btn-primary  align-items-center" >Guardar</button></td>';
			}
			  
			echo' </tr>';
		}
	} 
	?>
    </tbody>
    </table>
	
	<?php 
		echo  ' <input id="empresa"  type="hidden" value='.  $wemp_pmla.'>';
	?>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="crossorigin="anonymous"></script>
	<script src="datatables.min.js"></script>   
	<script src="Autorizacion_medicamentos.js"></script>
   
  </div>
</body>
</html>
