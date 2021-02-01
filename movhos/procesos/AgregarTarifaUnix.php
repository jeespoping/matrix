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
//TABLAS DE CONSULTA Y DE INSERTAR: Las de UNIX ivarttar
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
<title>Crear Tarifa</title>

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
	<script>
	function buscarTarifas(Codigo) {
			var validacion2 = null;
			ancho = 300;    alto = 120;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
			settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';
			validacion2 = window.open ("validarCodigoyTarifa.php?Codigo="+Codigo,"miwin",settings2);
			validacion2.focus();
		}
	function buscarTarifas2(Tarifa) {
			var validacion2 = null;
			ancho = 300;    alto = 120;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
			settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';
			validacion2 = window.open ("validarCodigoyTarifa.php?Tarifa="+Tarifa,"miwin",settings2);
			validacion2.focus();
		}	
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

<?php
		
		$Codigo=$_POST['Codigo'];
		$Tarifa=$_POST['Tarifa'];
		$Valor=$_POST['Valor'];
		$Fecha=$_POST['Fecha'];
		$Valor_Actual=$_POST['Valor_Actual'];
		$Rondas=$_POST['Rondas'];
		$Fecha_data=date("Y-m-d"); 
		$Hora_data=date("H:i:s");
		$ConcatFecHor=$Fecha_data.' '.$Hora_data;
		$Seguridad=$wuse;
$accion = isset($_POST['accion']) ? $_POST['accion'] : "";
		if($accion == 'Guardar')
		{
			$query_medicamento = "select * from ivarttar
								  where arttarcod='$Codigo'
								  and arttartar='$Tarifa'";
			$datos_medicamentos = odbc_do($conex_o, $query_medicamento);
			odbc_fetch_row($datos_medicamentos);
			$respuesta = odbc_result($datos_medicamentos, 2);
			//echo 'resultado de consulta'.$respuesta;
			//echo $Codigo;
			//echo $Tarifa;
			if ($respuesta == 'C'){
				?>
					<div style="text-align: center" class="row">
						<form method="post" action="AgregarTarifaUnix.php">
							<label style="color: #080808"><strong>EL DATO YA EXISTE</strong> </label>
							<br>
							<br>
							<input type="submit" class="text-success" value="ACEPTAR"/>
						</form>
					</div>
				<?php
			}else{
			///// QUERY INSERTAR EN UNIX/////
			$insert_ivarttar = "INSERT INTO ivarttar(arttarcod,arttartip,arttartar,arttartse,arttarvaa,arttarfec,arttarval,arttaruad,arttarfad,arttarumo,arttarfmo) 
							   VALUES
							  ('$Codigo','C','$Tarifa','*','$Valor','$Fecha','$Valor_Actual','$Seguridad','$ConcatFecHor','$Seguridad','$ConcatFecHor')";
			odbc_do($conex_o, $insert_ivarttar);			 
			//arttarcod,arttartip,arttartar,arttartse,arttarvaa,arttarfec,arttarval,arttaruad,arttarfad,arttarumo,arttarfmo
			?>
				<div style="margin-top: 10px;  text-align: center">
				<form method="post" action="AgregarTarifaUnix.php">
				<label style="color: #080808"><strong>DATOS INCERTADOS CORRECTAMENTE</strong> </label>
				<br><br>
				<input type="submit" class="text-success" value="ACEPTAR"/>
				</form>
				</div>
				
			<?php
			}
			
		}else{
			?>
<body width="1200" height="60">
<form action="AgregarTarifaUnix.php" method="post" id="tarifas" name="tarifas">
	<table width="1200" border="1" align="center">
			<tr>
				<td width="50%" align="" style="border: groove; width: 0%">
					<input type="image" src="../../images/medical/root/clinica.jpg" width="140" height="80">
				<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>INSERTAR TARIFAS EN SERVINTE</strong></p> </td>
			</tr>
	</table>		
	<p>&nbsp;</p>
	<table width="1000" border="1" align="center">
		<tr>
			<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong> INGRESO DE REGISTROS </strong></p> </td>
		</tr>
	</table>		
	<p>&nbsp;</p>
	<div align="center">
	<table width="1000" border="0">
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Codigo Del Articulo:</strong></p></td>
		    <td>
				<strong>
					<input name="Codigo" type="text" id="Codigo" size="10" value="<?php echo $Codigo ?>" onblur="buscarTarifas(this.value)" onkeyup="javascript:this.value=this.value.toUpperCase();" required />
				</strong>
			</td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Nombre Del Articulo:</strong></p></td>
			<td><strong><input name="Pronom" type="text" id="Pronom" size="50" value="<?php echo $Pronom ?>" required readonly='on' style="background-color:silver" /></strong></td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Tarifa:</strong></p></td>
		    <td>
				<strong>
					<input name="Tarifa" type="text" id="Tarifa" size="10" value="<?php echo $Tarifa ?>" onblur="buscarTarifas2(this.value)" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
				</strong>
			</td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Nombre De Tarifa:</strong></p></td>
			<td><strong><input name="Tarnom" type="text" id="Tarnom" size="50" value="<?php echo $Tarnom ?>" required readonly='on' style="background-color:silver" /></strong></td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Valor Anterior:</strong></p></td>
		    <td>
				<strong>
					<input name="Valor" type="text" id="Valor" size="10" value="<?php echo $Valor ?>" />
				</strong>
			</td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Fecha:</strong></p></td>
		    <td>
				<strong>
					<input name="Fecha" type="date" id="Fecha" size="10" value="<?php echo $Fecha ?>" />
				</strong>
			</td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Valor Actual:</strong></p></td>
		    <td>
				<strong>
					<input name="Valor_Actual" type="text" id="Valor_Actual" size="10"  />
				</strong>
			</td>
		</tr>  
		<tr>
			<td height="35" colspan="2">
				<div align="center">
					<input name="accion" type="hidden" value='Guardar' />
					<input name="Guardar" type="submit" class="btn-primary" value="Guardar" />
					<a href="TarifasMedicamentoUnix.php">RETORNAR</a></label>
				</div>
			</td>
		</tr>
	</table>
	</div>
</form>
			
</body>
   <?php } ?>
</html>