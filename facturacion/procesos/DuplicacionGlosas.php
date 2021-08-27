<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DUPLICACION DE GLOSAS - MATRIX</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="facSer_style.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


    <style>
        .Ntooltip
        {
        }

        a.Ntooltip {
            position: relative; /* es la posición normal */
            text-decoration: none !important; /* forzar sin subrayado */
            color:#0080C0 !important; /* forzar color del texto */
            font-weight:bold !important; /* forzar negritas */
        }

        a.Ntooltip:hover {
            z-index:999; /* va a estar por encima de todos */
            background-color:#000000; /* DEBE haber un color de fondo */
        }

        a.Ntooltip span {
            display: none; /* el elemento va a estar oculto */
        }

        a.Ntooltip:hover span {
            display: block; /* se fuerza a mostrar el bloque */
            position: absolute; /* se fuerza a que se ubique en un lugar de la pantalla */
            top:2em; left:2em; /* donde va a estar */
            width:250px; /* el ancho por defecto que va a tener */
            padding:5px; /* la separación entre el contenido y los bordes */
            background-color: #0080C0; /* el color de fondo por defecto */
            color: #FFFFFF; /* el color de los textos por defecto */
        }
    </style>
    <?php

//==============================================================================================================================
	//Mayo 28 de 2021 Leandro Meneses
	//Al campo godetcla (clasificada) se lleva l valor 'N' para que permita clasificar la glosa
	//==============================================================================================================================
    include("conex.php");
    include("root/comun.php");

	
	
	
    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
        </div>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        mysql_select_db("matrix");
        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }
	
	//$query  = "select max(gloencdoc) as numglosa  from cagloenc  where gloencfue='85'";
	$query  = "select max(gloencdoc) as numglosa  from cagloenc";


	$err_o = odbc_do($conex_o,$query) or die (odbc_errormsg());
	$num = odbc_num_fields($err_o);

	// while(($row = odbc_fetch_array($err_o)))
	// {
		// echo "Numero ultima glosa : ". $row['numglosa'] . " ";
	// }
	
	

    $fuente = '85';
    $fecha_Actual = date('Y-m-d');  
	$hora_Actual = date('H:m:s');
    $ano_Actual = date('Y');        
	$mes_Actual = date('m');

    $accion = $_POST['accion']; $numFactu = $_POST['numFactu']; $dato = $_POST['dato'];
    ?>
</head>

<body>





<div class="panel panel-info contenido" style="width: 95%">
    <div class="panel-heading encabezado">
<?php
$actualiz = "2021-03-26";
encabezado( "DUPLICACION GLOSAS SERVINTE", $actualiz ,"clinica" );
?>
    </div>



	<?php


	$glosaEncontrada = false;
	
	if($accion == 'consultar' || $accion == 'duplicar')
	{
		$query1 = "select * from cagloenc WHERE gloencdoc = '$dato' and gloencfue='85' and gloencanu='0'";
		$commit1 = odbc_do($conex_o, $query1);
		$conteoFacon = odbc_result($commit1,1);
		if($conteoFacon > 0)
		{
			$glosaEncontrada = true;
			$gloencfue = odbc_result($commit1,'gloencfue');
			$gloenccco = odbc_result($commit1,'gloenccco');
			$gloencdoc = odbc_result($commit1,'gloencdoc');
			$gloencper = odbc_result($commit1,'gloencper');
			$gloencfec = odbc_result($commit1,'gloencfec');
			$gloencnit = odbc_result($commit1,'gloencnit');
			$gloencest = odbc_result($commit1,'gloencest');
			$gloencuad = odbc_result($commit1,'gloencuad');
			$gloencreg = odbc_result($commit1,'gloencreg');
			$gloencval = odbc_result($commit1,'gloencval');
			$gloencobs = odbc_result($commit1,'gloencobs');
			$gloencanu = odbc_result($commit1,'gloencanu');
			$gloencfad = odbc_result($commit1,'gloencfad');
			$gloencumo = odbc_result($commit1,'gloencumo');
			$gloencfmo = odbc_result($commit1,'gloencfmo');
			$gloenctip = odbc_result($commit1,'gloenctip');			
			

			$query1 = "select * from  caglodet,famov, outer caglocod where glodetfca=movfue and glodetfac=movdoc and glodetdoc = '$dato' and glodetfue='85' and glodetdoc=glocoddoc and glodetcon=glocodcon";
			//echo $query1;
			$det_glosa = odbc_do($conex_o,$query1) or die (odbc_errormsg());
			$num = odbc_num_fields($det_glosa);				
				

		}

	}
	
	
	if ($glosaEncontrada == false && $accion != 'grabar')
	{
		?>
		<div class="card bg-light divContHome" style="border: none">
			<div class="navigation" style="margin-top: 80px">
				<form id="formHome" name="formHome" method="post" action="DuplicacionGlosas.php" style="margin-top: 50px">
				<?php
				if($accion == 'consultar')
				{
				?>
					<h4>La Glosa consultada no existe</h4>								
				<?php
				}
				?>
				
				  <div align="center" class="row">
					<div class="form-group col-md-5">
					</div>				  
					<div align="center" class="form-group col-md-2">
					  <label for="dato">GLOSA A DUPLICAR</label>
					  <input  value="<?php echo $gloencdoc ?>" class="form-control" name="dato" id="dato">
					</div>
					<div class="form-group col-md-5">
					</div>					
				  </div>				

				  <div  align="center" class="row">
					<div class="form-group col-md-5">
					</div>				  

					<div class="form-group col-md-2">
						<input type="hidden" name="accion" value="consultar">
						<input type="submit" class="btn btn-primary btn-block"  value="CONSULTAR" >
					</div>
					<div class="form-group col-md-5">
					</div>					
				  </div>				

				  
				</form>
			</div>
		</div>
		<?php
	}				

	//echo "fffffffffff ".$accion . " ". $glosaEncontrada . " " ;
	$glosaDuplicada = false;
	if ($accion == 'duplicar' && $glosaEncontrada == true)
	{
		
		$glosaDuplicada = true;
		$query  = "select max(gloencdoc) as numglosa  from cagloenc  where gloencfue='85'";

		$err_o = odbc_do($conex_o,$query) or die (odbc_errormsg());
		$nuevoNumGlosa = odbc_result($err_o,'numglosa');
		$nuevoNumGlosa++;
		$gloencdoc = $nuevoNumGlosa;
		$gloencper = date('Ym');
		$gloencfec = date('Y-m-d');	
		$gloencest = "GL";
		$gloencuad = $wuse;
		$gloencfad = date('Y-m-d H:m:s');	

		
		
		$sqlInsert = "insert into cagloenc(";
		$sqlInsert .= "gloencfue,gloenccco,gloencdoc,gloencper,";
		$sqlInsert .= "gloencfec,gloencnit,gloencest,gloencuad,";
		$sqlInsert .= "gloencreg,gloencval,gloencobs,gloencanu,";
		$sqlInsert .= "gloencfad,gloenctip";

		$sqlInsert .= ")values(";
		
		$sqlInsert .= "'$gloencfue','$gloenccco','$gloencdoc','$gloencper',";
		$sqlInsert .= "'$gloencfec','$gloencnit','$gloencest','$gloencuad',";
		$sqlInsert .= "'$gloencreg','$gloencval','$gloencobs','$gloencanu',";
		$sqlInsert .= "'$gloencfad','$gloenctip')";		
		//echo " ".$sqlInsert ." ";
		$err_insert = odbc_do($conex_o,$sqlInsert) or die (odbc_errormsg());	
		$array_facturas = array();		
		while(($row = odbc_fetch_array($det_glosa)))
		{
		
			$glodetfue = $row['glodetfue'];
			$glodetcco = $row['glodetcco'];
			$glodetdoc = $gloencdoc;
			$glodetfec = date('Y-m-d');		
			$glodetsec = $row['glodetsec'];
			$glodetfca = $row['glodetfca'];
			$glodetfac = $row['glodetfac'];
			$glodetenv = $row['glodetenv'];
			$glodetefe = $row['glodetefe'];
			//$glodetgfe = $row['glodetgfe'];
			$glodetgfe = date('Y-m-d');
			$glodetcon = $row['glodetcon'];
			$glodetval = $row['glodetval'];
			$glodetcau = $row['glodetcau'];
			$glodetnum = $row['glodetnum'];
			$glodetest = 'GL';
			$glodetrad = $row['glodetrad'];
			$glodetnit = $row['glodetnit'];
		//Modificación Mayo 28 de 2021 Leandro Meneses
		//Al campo godetcla (clasificada) se lleva l valor 'N' para que permita clasificar la glosa			
			$glodetcla = 'N';
			$glodetsed = $row['glodetsed'];
			$glodeteso = $row['glodeteso'];
			
			$glocodfue = $row['glocodfue'];
			$glocodcco = $row['glocodcco'];
			$glocoddoc = $gloencdoc;
			$glocodsec = $row['glocodsec'];
			$glocodfca = $row['glocodfca'];
			$glocodfac = $row['glocodfac'];
			$glocodcon = $row['glocodcon'];
			$glocodcod = $row['glocodcod'];
			$glocodter = $row['glocodter'];
			$glocoddte = $row['glocoddte'];
			$glocodreg = $row['glocodreg'];
			

		
			$sqlInsert = "insert into caglodet(";
			$sqlInsert .= "glodetfue,glodetcco,glodetdoc,glodetfec,";
			$sqlInsert .= "glodetsec,glodetfca,glodetfac,glodetenv,";
			$sqlInsert .= "glodetefe,glodetgfe,glodetcon,glodetval,glodetcau,";
			$sqlInsert .= "glodetnum,glodetest,glodetrad,glodetnit,";
			$sqlInsert .= "glodetcla,glodetsed,glodeteso";			

			$sqlInsert .= ")values(";
			
			$sqlInsert .= "'$glodetfue','$glodetcco','$glodetdoc','$glodetfec',";
			$sqlInsert .= "'$glodetsec','$glodetfca','$glodetfac','$glodetenv',";
			$sqlInsert .= "'$glodetefe','$glodetgfe','$glodetcon','$glodetval','$glodetcau',";
			$sqlInsert .= "'$glodetnum','$glodetest','$glodetrad','$glodetnit',";
			$sqlInsert .= "'$glodetcla','$glodetsed','$glodeteso')";	
			//echo " ".$sqlInsert ." ";
			$err_insert = odbc_do($conex_o,$sqlInsert) or die (odbc_errormsg());		
			
			
			
	
			
			if (trim($glocodter) != '')
			{
				
				$sqlInsert = "insert into caglocod";
				$sqlInsert .= "(glocodfue,glocodcco,glocoddoc,glocodsec,glocodfca,glocodfac,glocodcon,glocodcod,glocodter,glocoddte,glocodreg)";		
				$sqlInsert .= "values";				
				$sqlInsert .= "('$glocodfue','$glocodcco','$glocoddoc','$glocodsec','$glocodfca','$glocodfac','$glocodcon','$glocodcod','$glocodter','$glocoddte','$glocodreg')";

				//echo " ".$sqlInsert ." ";
				$err_insert = odbc_do($conex_o,$sqlInsert) or die (odbc_errormsg());	
			}
			$NuevaFactura = false;
			
			if (trim($glodetfac) != '')
			{
				if(array_key_exists($glodetfac, $array_facturas))
				{

					$array_facturas[$glodetfac] = $glodetfac;
					$NuevaFactura = false;

				}
				else
				{
					$NuevaFactura = true;
					$array_facturas[$glodetfac] = $glodetfac;
				}			
			}
	
			
			if ($NuevaFactura == true)
			{
				
				$query  = "select max(estmovsec) as secuencia  from caestmov where estmovfue='20' and estmovdoc='$glodetfac'";

				$err_o = odbc_do($conex_o,$query) or die (odbc_errormsg());
				$nuevoNumGlosaFactura = odbc_result($err_o,'secuencia');
				$nuevoNumGlosaFactura++;
				
				
				$estmovfue = '20';
				$estmovdoc = $row['glodetfac'];
				$estmovper = $gloencper;
				$estmovsec = $nuevoNumGlosaFactura;
				$estmovind = 'E';
				$estmovedo = 'GL';
				$estmovfmo = 85;
				$estmovdmo = $gloencdoc;
				$estmovemo = 'GL';
				$estmovusu = $wuse;
				$estmovfec = date('Y-m-d H:m:s');	
				
				$sqlInsert = "insert into caestmov (estmovfue,estmovdoc,estmovper,estmovsec,estmovind,estmovedo,estmovfmo,estmovdmo,estmovemo,estmovusu,estmovfec)";
				$sqlInsert .= " values ('$estmovfue','$estmovdoc','$estmovper','$estmovsec','$estmovind','$estmovedo','$estmovfmo','$estmovdmo','$estmovemo','$estmovusu','$estmovfec')";

				//echo " ".$sqlInsert ." ";
				$err_insert = odbc_do($conex_o,$sqlInsert) or die (odbc_errormsg());	
				
				
			    $sqlUpdate = "Update caenc set encest='GL' where encfue='20' and encdoc='".$row['glodetfac']."'";
				//echo " ".$sqlUpdate ." ";
				$err_update = odbc_do($conex_o,$sqlUpdate) or die (odbc_errormsg());				

			}

		}					

		$query1 = "select * from cagloenc WHERE gloencdoc = '$gloencdoc' and gloencfue='85' and gloencanu='0'";
		$commit1 = odbc_do($conex_o, $query1);
		$conteoFacon = odbc_result($commit1,1);
		if($conteoFacon > 0)
		{
			$glosaEncontrada = true;
			$accion = 'consultar';
			$dato = $nuevoNumGlosa;
			$gloencfue = odbc_result($commit1,'gloencfue');
			$gloenccco = odbc_result($commit1,'gloenccco');
			$gloencdoc = odbc_result($commit1,'gloencdoc');
			$gloencper = odbc_result($commit1,'gloencper');
			$gloencfec = odbc_result($commit1,'gloencfec');
			$gloencnit = odbc_result($commit1,'gloencnit');
			$gloencest = odbc_result($commit1,'gloencest');
			$gloencuad = odbc_result($commit1,'gloencuad');
			$gloencreg = odbc_result($commit1,'gloencreg');
			$gloencval = odbc_result($commit1,'gloencval');
			$gloencobs = odbc_result($commit1,'gloencobs');
			$gloencanu = odbc_result($commit1,'gloencanu');
			$gloencfad = odbc_result($commit1,'gloencfad');
			$gloencumo = odbc_result($commit1,'gloencumo');
			$gloencfmo = odbc_result($commit1,'gloencfmo');
			$gloenctip = odbc_result($commit1,'gloenctip');			
			

			$query1 = "select * from  caglodet,famov, outer caglocod where glodetfca=movfue and glodetfac=movdoc and glodetdoc = '$dato' and glodetfue='85' and glodetdoc=glocoddoc and glodetcon=glocodcon";
			//echo $query1;
			$det_glosa = odbc_do($conex_o,$query1) or die (odbc_errormsg());
			$num = odbc_num_fields($det_glosa);				
				

		}else{
			echo $query1;
		}	

		
	}				
	
	
	if($accion == 'consultar' && $glosaEncontrada == true)
	{
		

		$SePuedeDuplicar = true;


		
	?>
		<div class="card bg-light divContHome" style="border: none">

				<?php
				if ($glosaDuplicada == true)
				{
				?>
				
				  <div class="row">
					<div class="form-group col-md-12">
						 <label>Glosa generada exitosamente!!!</label>				
					</div>					
				  </div>					
							
				<?php
				}
				?>
				
				
				  <div class="row">
					<div class="form-group col-md-1">
					</div>				  
					<div class="form-group col-md-2">
					  <label  <?=($glosaDuplicada == true) ? 'class="bg-success text-white"' : ""?> for="numeroGlosa">Número Glosa</label>
					  <input readonly value="<?php echo $gloencdoc ?>" class="form-control" id="numeroGlosa">
					</div>
					<div class="form-group col-md-2">
					  <label for="fuente">Fuente</label>
					  <input readonly value="<?php echo $gloencfue ?>" class="form-control" id="fuente">
					</div>
					<div class="form-group col-md-7">
					</div>					
				  </div>			

				  <div class="row">
					<div class="form-group col-md-1">
					</div>	
					<div class="form-group col-md-2">
					  <label for="fecha">Fecha</label>
					  <input readonly value="<?php echo $gloencfec ?>" class="form-control" id="fecha" >
					</div>
					<div class="form-group col-md-2">
					  <label for="empresa">Empresa</label>
					  <input readonly value="<?php echo $gloencnit ?>" class="form-control" id="empresa">
					</div>
					<div class="form-group col-md-2">
					  <label for="estado">Estado</label>
					  <input readonly value="<?php echo $gloencest ?>" class="form-control" id="estado">
					</div>
					<div class="form-group col-md-2">
					  <label for="usuario">Usuario</label>
					  <input readonly value="<?php echo $gloencuad ?>" class="form-control" id="usuario" >
					</div>					
					<div class="form-group col-md-3">
					</div>							
				  </div>		

				  <div class="row">

					<div class="form-group col-md-12">
						<table class="table">
						  <thead class="thead-dark">
							<tr>
								<th scope="col">#</th>
								<th scope="col">Fuente</th>
								<th scope="col">Est</th>								
								<th scope="col">CCostos</th>
								<th scope="col">Documento</th>
								<th scope="col">Factura</th>
								<th scope="col">Fecha</th>
								<th scope="col">Env-Emp</th>
								<th scope="col">Fec-Envio</th>
								<th scope="col">Fec-Glosa</th>
								<th scope="col">Cpto</th>							  
								<th scope="col" class="col text-right">Valor</th>		
								<th scope="col">Causa</th>		
								<th scope="col">Cod</th>		
								<th scope="col">Tercero</th>									
								<th scope="col">Doctercero</th>		
							</tr>
						  </thead>
						  <tbody>
							<?php
							while(($row = odbc_fetch_array($det_glosa)))
							{
								if ($row['glodetest'] != "GS")
								{
									$SePuedeDuplicar = false;
								}
							?>
								<tr>
								  <th scope="row"><?php echo $row['glodetsec'] ?></th>
								  <td><?php echo $row['glodetfue'] ?></td>
								  <td><?php echo $row['glodetest'] ?></td>								  
								  <td><?php echo $row['glodetcco'] ?></td>								  
								  <td><?php echo $row['glodetdoc'] ?></td>
								  <td><?php echo $row['glodetfac'] ?></td>								  
								  <td><?php echo $row['movfec'] ?></td>
								  <td><?php echo $row['glodetenv'] ?></td>
								  <td><?php echo $row['glodetefe'] ?></td>
								  <td><?php echo $row['glodetgfe'] ?></td>
								  <td><?php echo $row['glodetcon'] ?></td>
								  <td class="col text-right"><?php echo formatMoney($row['glodetval'], 0) ?></td>								  
								  <td><?php echo $row['glodetcau'] ?></td>
								  <td><?php echo $row['glocodcod'] ?></td>
								  <td><?php echo $row['glocodter'] ?></td>					
								  <td><?php echo $row['glocoddte'] ?></td>										  
								</tr>							
							<?php
							}
							?>
								<tr>
								  <td></td>
								  <td></td>
								  <td></td>								  
								  <td></td>
								  <td></td>
								  <td></td>
								  <td></td>
								  <td></td>								  
								  <td></td>
								  <td></td>								  
								  <th scope="row">Total</th>
								  <th class="col text-right" scope="row"><?php echo formatMoney($gloencval, 0) ?></th>
								  <td></td>
								  <td></td>
								  <td></td>					
								  <td></td>										  
								</tr>	
						  </tbody>
						</table>				
					</div>					
							
				  </div>	
				
<?php				
				if ($glosaDuplicada == false && $SePuedeDuplicar == true)
				{	
?>					  
			<form id="formDatos" name="formDatos" method="post">
				  <div class="row">
					<div class="form-group col-md-4">
					</div>				  
					<input type="hidden" name="accion" value="duplicar">
					<input type="hidden" name="dato" value="<?php echo $dato ?>">
					 <div class="form-group col-md-4">
						<input type="submit" class="btn btn-primary btn-block" value="DUPLICAR" >
					 </div>
					<div class="form-group col-md-4">
					</div>
				  </div>	
			</form>

<?php				
				}
?>				

<?php				
				if ($glosaDuplicada == false && $SePuedeDuplicar == false)
				{	
?>					  
			<form id="formDatos" name="formDatos" method="post">
				  <div class="row">
					<div class="form-group col-md-4">
					</div>				  

					<div align="center" class="form-group col-md-4">
					  <label for="dato">LA GLOSA NO PUEDE DUPLICARSE: HAY DETALLES CON ESTADO DIFERENTE A "GS"</label>
					</div>						

					<div class="form-group col-md-4">
					</div>
				  </div>	
			</form>

<?php				
				}
?>				  
				<form id="formHome" name="formHome" method="post" action="DuplicacionGlosas.php" style="margin-top: 50px">

				
				  <div align="center" class="row">
					<div class="form-group col-md-5">
					</div>				  
					<div align="center" class="form-group col-md-2">
					  <label for="dato">GLOSA A DUPLICAR</label>
					  <input   class="form-control" name="dato" id="dato">
					</div>
					<div class="form-group col-md-5">
					</div>					
				  </div>				

				  <div  align="center" class="row">
					<div class="form-group col-md-5">
					</div>				  

					<div class="form-group col-md-2">
						<input type="hidden" name="accion" value="consultar">
						<input type="submit" class="btn btn-primary btn-block"  value="CONSULTAR" >
					</div>
					<div class="form-group col-md-5">
					</div>					
				  </div>				

				  
				</form>



		</div>
		<?php
	}


	?>

</div>

<?php
////////////FUNCIONES:


function formatMoney($number, $cents = 1) { // cents: 0=never, 1=if needed, 2=always
  if (is_numeric($number)) { // a number
    if (!$number) { // zero
      $money = ($cents == 2 ? '0.00' : '0'); // output zero
    } else { // value
      if (floor($number) == $number) { // whole number
        $money = number_format($number, ($cents == 2 ? 2 : 0)); // format
      } else { // cents
        $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2)); // format
      } // integer or decimal
    } // value
    return '$'.$money;
  } // numeric
} // formatMoney


function obtenerDatosUsuario($parametro,$wuse,$conex)
{
    switch($parametro)
    {
        case 1:
            $query1 = "select * from usuarios WHERE Codigo = '$wuse'";
            $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
            $dato1 = mysql_fetch_array($commit1);   $cCostosUsuario = $dato1['Ccostos'];
            return $cCostosUsuario;
            break;
    }
}

function obtenerNumFactura($fuente,$cCostos ,$conex_o)
{
    $query1 = "select fuesfu, fuecse from cafue WHERE fuecod = '$fuente' AND fuecco = '$cCostos'";
    $commit1 = odbc_do($conex_o, $query1);
    $fuesfu = odbc_result($commit1, 1);    $fuecse = odbc_result($commit1, 2);

    $query2 = "select * from cafue WHERE fuecod = '$fuesfu' AND fuecco = '$fuecse'";
    $commit2 = odbc_do($conex_o, $query2);
    $fuecod = odbc_result($commit2, 2); $fuesec = odbc_result($commit2,14); $fuecco = odbc_result($commit2, 5);
    $newConsecutivo = $fuesec + 1;

    $query3 = "update cafue set fuesec = '$newConsecutivo' WHERE fuecod = '$fuecod' AND fuecco = '$fuecco'";
    odbc_do($conex_o, $query3);
    //echo $newConsecutivo;
    return $newConsecutivo;
}

function obtenerNumFacturaTEMP($fuente,$cCostos ,$conex)
{
    $query = "select * from equipos_000009 ORDER BY consecutivo DESC LIMIT 1";
    $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());;
    $dato = mysql_fetch_array($commit);

    $consecutivo = $dato[1];
    $newConsecutivo = $consecutivo + 1;

    $query2 = "insert into equipos_000009 VALUES('','$newConsecutivo','$cCostos')";
    mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());

    echo $newConsecutivo;
}
?>
<script>
    const number = document.querySelector('.tvc2');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number.addEventListener('focus', (e) => {
        const element = e.target;
    const value = element.value;
    element.value = formatNumber(value);
    })

    const number2 = document.querySelector('.tvd2');
    function formatNumber2 (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number2.addEventListener('focus', (e) => {
        const element = e.target;
    const value2 = element.value;
    element.value = formatNumber2(value2);
    })

    const number3 = document.querySelector('.tvn2');
    function formatNumber3 (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number3.addEventListener('focus', (e) => {
        const element = e.target;
    const value3 = element.value;
    element.value = formatNumber3(value3);
    })
</script>
</body>
</html>