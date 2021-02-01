<!---->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta y para consultar la informacion del nombre de los productos y de las respectivas tarifas|
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2020-01-20.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2020-01-25.                                                                                             |
//                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: 
//
//TABLAS DE CONSULTA: Las de UNIX ivarttar
//
//  
//
//
//
//                                                                                                                                      |
//==========================================================================================================================================	
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Listar Medicamentos y Tarifas</title>

<?php
//Validar conex y el comun
    include_once("conex.php");
    include_once("root/comun.php");
	$conex_o = odbc_connect('informix','','')  or die("No se realizo conexion con la BD de Facturacion");

    if(!isset($_SESSION['user']))
    {
		?>
		<style 
			type="text/css">

		</style>
		<div align="center">
				<label>Usuario no autenticado en el sistema.<br />
					   Recargue la pagina principal de Matrix o inicie sesion nuevamente.
				</label>
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
	}
	
	//<script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	//<script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
?>
<!-- Llamado de las librerias -->
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
	<link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="../../../matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	<script src="../../../matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	
    <script>
        $(function() {
			$( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
			$( "#datepicker3" ).datepicker();
			$( "#datepicker4" ).datepicker();
        });
    </script>
	<style>
		html body {
			width: 80%;
			margin: 0 auto 0 auto;
			
		}
	</style>
    <style>
        .alternar:hover{ background-color:#e1edf7;}
		.Estilo4 {color: #000000; font-weight: bold; }


    </style>
    <script>
        function centrar() {
			iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        
		}
	</script>
</head>


<body width="1200" height="47">
	<form action="TarifasMedicamentoUnix.php" method="post">  
		<table width="1200" border="1" align="center">
			<tr>
				<td width="50%" align="" style="border: groove; width: 0%">
					<input type="image" src="../../images/medical/root/clinica.jpg" width="140" height="80">
				<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>GESTION DE TARIFAS SERVINTE</strong></p> </td>
			</tr>
		</table>		
		<p>&nbsp;</p>
		<table width="700" border="0" align="center">
			<tr align="right">
				<td>
					<div align="right" class="Estilo3"><a href="AgregarTarifaUnix.php">AGREGAR TARIFA </a></div>
				</td>
			</tr>
		</table>		
		<p>&nbsp;</p>
		<div align="center" border="1">
			<table width="258" border="0">
				<tr>
					<td width="248" colspan="2"><div align="center" class="h5"><strong>Ingrese los parametros de consulta: </strong></div>        </td>
				</tr>
				<tr>
					<td bgcolor="#C3D9FF"><p align="left"><strong>Codigo:</strong></p></td>
					<td>
						<strong>
							<input name="Codigo" type="text" id="Codigo" size="10" value="<?php echo $Codigo ?>" onkeyup="javascript:this.value=this.value.toUpperCase();" />
						</strong>
					</td>
				</tr>
				<tr>
					<td bgcolor="#C3D9FF"><p align="left"><strong>Tarifa:</strong></p></td>
					<td>
						<strong>
							<input name="Tarifa" type="text" id="Tarifa" size="10" value="<?php echo $Tarifa ?>" onkeyup="javascript:this.value=this.value.toUpperCase();" />
						</strong>
					</td>
				</tr>				
				<tr>
					<td height="35" colspan="2">
						<div align="center">
							<p>
								<input name="buscador" type="submit" class="btn-primary" value="Consultar Tarifas" />
							</p>
						</div>
					</td>
				</tr>
			</table>
		</div>
	


<p>
  <?php
	if 	($_POST['buscador'])
		{
			$Codigo = $_POST['Codigo'];
			$Tarifa = $_POST['Tarifa'];
		// Si está vacío no realiza la busqueda, sino realizamos la búsqueda
		if($Codigo == null)
			{
				?>
					<style 
						type="text/css">
					</style>
					<div align="center">
						<label>Por favor validar<br /> Que el campo codigo no este vacio <br /></label>
					</div>
				<?php
				//echo "Por favor ingresar las fechas";
				//echo "Por favor ingresar las fechas";
			}
			else{
				// Conexión a la tablas y seleccion de registros MATRIX
			/*	$select_encabezado = mysql_query("Select *
											From movhos_000110
											where Enllav='$Lavanderia'
											and Enlfec >= '$buscar'
											and Enlcon='$Conceptos'
											and Enlron='$Rondas'");
				$select_detalle = mysql_query("Select count(*)
											From movhos_000111
											where mollav='$Lavanderia'
											and Fecha_data >= '$buscar'
											and Molcon='$Conceptos'
											and Molron='$Rondas'");
											
											
											where arttarcod='$Codigo'
									  and arttartar='$Tarifa'*/
				//							     1			2		3		   4		5
				$query_medicamento = "select arttarcod,arttartar,arttarvaa,arttarfec,arttarval 
									from ivarttar
									where arttarcod='$Codigo'
									or arttartar='$Tarifa'
									order by 1";
				$datos_medicamentos = odbc_do($conex_o, $query_medicamento);

//arttarcod,arttartip,arttartar,arttartse,arttarvaa,arttarfec,arttarval,arttaruad,arttarfad,arttarumo,arttarfmo

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
	?>
	<table width="1000" height="44" border="1" align="center">
		<tr>
			<td style="background-color: #C3D9FF"><div align="center" class="Estilo5"><strong>CONSULTA DE TARIFAS UNIX:</strong></div></td>
		</tr>
	</table>
	<table width="1000" height="44" border="0" align="center">
			<tr>
 			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5"><strong>Codigo:</strong></div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5"><strong>Tarifa:</strong></div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5"><strong>Valor Anterior:</strong></div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5"><strong>Fecha:</strong></div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5"><strong>Valor Actual:</strong></div></td>
			</tr>
	<?php
	while(odbc_fetch_row($datos_medicamentos))
	{	
        $Codigo_Medicamento = odbc_result($datos_medicamentos, 1);
		$Tarifa = odbc_result($datos_medicamentos, 2);
		$Valor = odbc_result($datos_medicamentos, 3);
		$Fecha = odbc_result($datos_medicamentos, 4);
		$Valor_Anterior = odbc_result($datos_medicamentos, 5);
	?>
			<tr>
				<td align="center" width="400"><?php echo $Codigo_Medicamento ?></td>
				<td align="center" width="400"><?php echo $Tarifa ?></td>
				<td align="center" width="400"><?php echo $Valor ?></td>
				<td align="center" width="400"><?php echo $Fecha ?></td>
				<td align="center" width="400"><?php echo $Valor_Anterior ?></td>
			</tr>	
	<?php
	}
	?>
	</table>
	<p>&nbsp;</p>
	<table align="center" style="padding-top: auto" border='0'>
		<tr>	
			<td>
				<div align="center"><input name="button" type="button" class="btn-primary" onclick="window.close();" value="CANCELAR" /> </div>
			</td>
		</tr>
	</table>
	</form>		
	<?php
}
}
?>
</body>
</html>