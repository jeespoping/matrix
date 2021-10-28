<!--El programa realiza la consulta y gestion respectivo a crear cotizaciones con los valores de las respectivas tablas de tarifas de unix para la unidad de hemodinamia-->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a crear cotizaciones con los valores de las respectivas tablas de tarifas de unix para la unidad de hemodinamia                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-09-20.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-09-30.                                                                                             |
//                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: 
//Para insertar y gestionar cliame_000329, cliame_000330, cliame_000337 
//TABLAS DE CONSULTA: Para consultar las de UNIX facon,inexa,inexatar,ivart,ivarttar,inpro,inprotar
//CONSULTA LA DESCRIPCION DEL PACIENTE DESDE CLIAME_000100 Y CLIAME_000101
//  
// LAS DESCRIPCION Y LOS PARRAFOS DE LAS PLANTILLAS ESTAN QUEMADAS EN EL CODIGO, Y SE DEBEN DE MODIFICAR.
//
// NOTA: 2019-10-30 Por Didier Orozco Carmona: Se agrega campo nuevo cuando seleccionan SI en MIPRES activar campo para ingresar un numero |
// NOTA: 2019-11-19 Por Didier Orozco Carmona: Se agrega campo nuevo en la pestaña de consultar las cotizaciones que hace relacion a la fecha |
//==========================================================================================================================================	
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
    include_once("conex.php");
    include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");
    $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
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
    }
?>
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
	<link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	<script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>

	<style>
		html body {
			width: 80%;
			margin: 0 auto 0 auto;
			
		}
	</style>
	 <script>
	  function opPaneles(idEtiqueta)
        {
            switch (idEtiqueta)
            {
                case 'xPlantilla':
                    document.getElementById('xPlantilla').style.display = 'block';
                    document.getElementById('xProceExa').style.display = 'none';
                    document.getElementById('xCotizacion').style.display = 'none';
					document.getElementById('xCotizacionRealizada').style.display = 'none';
					document.getElementById('xNuevaPlantilla').style.display = 'none';	
					document.getElementById('xNuevoProceExam').style.display = 'none';
					document.getElementById('xAlmacenoPlantilla').style.display = 'none';	
					document.getElementById('xAlmacenoDetalle').style.display = 'none';
					document.getElementById('xAlmacenoCotizacion').style.display = 'none';
                break;
                case 'xProceExa':
                    document.getElementById('xPlantilla').style.display = 'none';
                    document.getElementById('xProceExa').style.display = 'block';
                    document.getElementById('xCotizacion').style.display = 'none';
					document.getElementById('xCotizacionRealizada').style.display = 'none';	
					document.getElementById('xNuevaPlantilla').style.display = 'none';
					document.getElementById('xNuevoProceExam').style.display = 'none';
					document.getElementById('xAlmacenoPlantilla').style.display = 'none';	
					document.getElementById('xAlmacenoDetalle').style.display = 'none';
					document.getElementById('xAlmacenoCotizacion').style.display = 'none';
                break;
                case 'xCotizacion':
                    document.getElementById('xPlantilla').style.display = 'none';
                    document.getElementById('xProceExa').style.display = 'none';
                    document.getElementById('xCotizacion').style.display = 'block';
					document.getElementById('xCotizacionRealizada').style.display = 'none';
					document.getElementById('xNuevaPlantilla').style.display = 'none';
					document.getElementById('xNuevoProceExam').style.display = 'none';
					document.getElementById('xAlmacenoPlantilla').style.display = 'none';
					document.getElementById('xAlmacenoDetalle').style.display = 'none';
					document.getElementById('xAlmacenoCotizacion').style.display = 'none';
                break;
				case 'xCotizacionRealizada':
                    document.getElementById('xPlantilla').style.display = 'none';
                    document.getElementById('xProceExa').style.display = 'none';
                    document.getElementById('xCotizacion').style.display = 'none';
					document.getElementById('xCotizacionRealizada').style.display = 'block';
					document.getElementById('xNuevaPlantilla').style.display = 'none';
					document.getElementById('xNuevoProceExam').style.display = 'none';
					document.getElementById('xAlmacenoPlantilla').style.display = 'none';
					document.getElementById('xAlmacenoDetalle').style.display = 'none';
					document.getElementById('xAlmacenoCotizacion').style.display = 'none';
                break;
				 case 'xNuevaPlantilla':
                    document.getElementById('xPlantilla').style.display = 'none';
                    document.getElementById('xProceExa').style.display = 'none';
                    document.getElementById('xCotizacion').style.display = 'none';
					document.getElementById('xCotizacionRealizada').style.display = 'none';
					document.getElementById('xNuevaPlantilla').style.display = 'block';
					document.getElementById('xNuevoProceExam').style.display = 'none';
					document.getElementById('xAlmacenoPlantilla').style.display = 'none';
					document.getElementById('xAlmacenoDetalle').style.display = 'none';
					document.getElementById('xAlmacenoCotizacion').style.display = 'none';
					
					
                break;
				 case 'xNuevoProceExam':
                    document.getElementById('xPlantilla').style.display = 'none';
                    document.getElementById('xProceExa').style.display = 'none';
                    document.getElementById('xCotizacion').style.display = 'none';
					document.getElementById('xCotizacionRealizada').style.display = 'none';
					document.getElementById('xNuevaPlantilla').style.display = 'none';
					document.getElementById('xNuevoProceExam').style.display = 'block';
					document.getElementById('xAlmacenoPlantilla').style.display = 'none';
					document.getElementById('xAlmacenoDetalle').style.display = 'none';
					document.getElementById('xAlmacenoCotizacion').style.display = 'none';
					
                break;
				case 'xAlmacenoPlantilla':
                    document.getElementById('xPlantilla').style.display = 'none';
                    document.getElementById('xProceExa').style.display = 'none';
                    document.getElementById('xCotizacion').style.display = 'none';
					document.getElementById('xCotizacionRealizada').style.display = 'none';
					document.getElementById('xNuevaPlantilla').style.display = 'none';
					document.getElementById('xNuevoProceExam').style.display = 'none';
					document.getElementById('xAlmacenoPlantilla').style.display = 'block';
					document.getElementById('xAlmacenoDetalle').style.display = 'none';
					document.getElementById('xAlmacenoCotizacion').style.display = 'none';
                break;
				case 'xAlmacenoDetalle':
                    document.getElementById('xPlantilla').style.display = 'none';
                    document.getElementById('xProceExa').style.display = 'none';
                    document.getElementById('xCotizacion').style.display = 'none';
					document.getElementById('xCotizacionRealizada').style.display = 'none';
					document.getElementById('xNuevaPlantilla').style.display = 'none';
					document.getElementById('xNuevoProceExam').style.display = 'none';
					document.getElementById('xAlmacenoPlantilla').style.display = 'none';
					document.getElementById('xAlmacenoDetalle').style.display = 'block';
					document.getElementById('xAlmacenoCotizacion').style.display = 'none';
                break;
				case 'xAlmacenoCotizacion':
                    document.getElementById('xPlantilla').style.display = 'none';
                    document.getElementById('xProceExa').style.display = 'none';
                    document.getElementById('xCotizacion').style.display = 'none';
					document.getElementById('xCotizacionRealizada').style.display = 'none';
					document.getElementById('xNuevaPlantilla').style.display = 'none';
					document.getElementById('xNuevoProceExam').style.display = 'none';
					document.getElementById('xAlmacenoPlantilla').style.display = 'none';
					document.getElementById('xAlmacenoDetalle').style.display = 'none';
					document.getElementById('xAlmacenoCotizacion').style.display = 'block';
                break;
            }
        }
    </script>
	<script>
		function mensaje(CodPro) {
			var validacion = null;
			ancho = 300;    alto = 120;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
			settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';
			validacion = window.open ("validarCodigo.php?CodPro="+CodPro,"miwin",settings2);
			validacion.focus();
		}
		function buscarPaciente(Identificacion,TidR) {
			var validacion2 = null;
			ancho = 300;    alto = 120;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
			settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';
			validacion2 = window.open ("validarCodigo.php?Identificacion="+Identificacion+'&TidR='+TidR.value,"miwin",settings2);
			validacion2.focus();
		
		}//llamar o esconder campo mipres segun seleccion
		function mostrarCampo(valor){
			if (valor == 'SI'){
				document.getElementById('NumMipres').style.display='block';
			}else{
				document.getElementById('NumMipres').style.display='none';
			}
		}
		function multiplicar(Cant,Id){
			//alert (Cant);
			var valorun = document.getElementById('unitab'+Id).value;
			valorun = valorun.replace(/,/g, "");
			var totalsum = document.getElementById('total_cantidad').value;
			totalsum = totalsum.replace(/,/g, "");
			var resultado = parseInt(Cant)* parseInt(valorun);
			var R = formatNumber.new(resultado); // retorna "$123.456.779"
			document.getElementById('tottab'+Id).value = R;
			var totalizador = document.getElementById('total_filas').value;
            totalizar(totalizador);
		}
			function multiplicarDos(Cant,Id){
			//alert (Cant);
			var valorun = document.getElementById('canttab'+Id).value;
			Cant = Cant.replace(/,/g, "");
			var resultado = parseInt(Cant)* parseInt(valorun);
			var R = formatNumber.new(resultado); // retorna "$123.456.779"
			document.getElementById('tottab'+Id).value = R;
			var totalizador = document.getElementById('total_filas').value;
            totalizar(totalizador);
		}
		
		function totalizar(total){
			var sumar=0;
			for(var i=1;i<=total;i++){
				var valor_total = document.getElementById('tottab'+i).value;
				var valor_totalR = valor_total.replace(/,/g, "");
				sumar += parseInt(valor_totalR);
			}
			
			var Respuesta = formatNumber.new(sumar);
			document.getElementById('total_cantidad').value = Respuesta;
			
		}
		
		
		
		var formatNumber = {
			 separador: ".", // separador para los miles
			 sepDecimal: ',', // separador para los decimales
			 formatear:function (num){
			 num +='';
			 var splitStr = num.split('.');
			 var splitLeft = splitStr[0];
			 var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
			 var regx = /(\d+)(\d{3})/;
			 while (regx.test(splitLeft)) {
			 //splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
			 splitLeft = splitLeft.replace(regx, '$1' + this.sepDecimal + '$2');
			 }
			 return this.simbol + splitLeft +splitRight;
			 },
			 new:function(num, simbol){
			 this.simbol = simbol ||'';
			 return this.formatear(num);
			 }
			}
			
		
		
	</script>
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
	<?php 
		$activa=$_POST['plantilla_activa'];
		$activa_procexam=$_POST['activa_exam_proce']; 
		$activa_coti=$_POST['activa_cotizacion'];
		$Codpla=$_POST['Codpla'];   $Nompla=$_POST['Nompla']; $Estado=$_POST['Estado'];
		$accion = $_POST['accion']; $Fecha_data=date("Y-m-d"); $Hora_data=date("H:i:s");
		$CodPro = $_POST['CodPro']; $Cantidad= $_POST['Cantidad']; $Concepto=$_POST['Concepto'];
		
		//TABLA CAMPOS PARA CREAR
		$Fecha = $_POST['Fecha'];
		$activa_calcular=$_POST['calcular_plantilla'];
		$CodPro = $_POST['CodPro'];
		$Nompac = $_POST['Nompac'];
		$Identificacion	= $_POST['Identificacion'];
		$Tid	= $_POST['Tid'];
		$TidRe	= $_POST['TidR'];
		$Historia = $_POST['Historia'];
		$Ingreso = $_POST['Ingreso'];
		$Medico = $_POST['Medico'];
		$CodplaRe = $_POST['CodplaR'];
		$Empcod = $_POST['Empcod'];
		$EmpcodRe = $_POST['EmpcodR'];
		$Tprocedimiento = $_POST['Tprocedimiento'];
		$Mipres = $_POST['Mipres'];
		$NumMipres = $_POST['NumMipres'];
		$items1=($_POST['descritab']);
		$items2=($_POST['codtab']);
		$items3=($_POST['canttab']);
		$items4=($_POST['concetab']);
		$items5=($_POST['unitab']);
		$items6=($_POST['tottab']);
		$total_cantidad = $_POST['total_cantidad'];
		$total_cantidadR = str_replace(",","",$total_cantidad);
		
	?>	
</head>
<body width="1200" height="60">
<div class="datosIndicadores">
                <div align="center">
                  <table width="1200">
                    <tr style="border-bottom: groove">
                      <td width="207"><input type="image" id="btnVer" src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80">&ensp;</td>
                      <td width="200"><h5><strong>MENU DE COTIZACION:</strong></h5></td>
                    </tr>
					<tr>
					  <td>&ensp;</td>
                      <td>&ensp;</td>
                      <td width="200" class="listRow"><label><a href="#" onclick="opPaneles('xPlantilla')">MAESTRO DE PLANTILLA</a></label></td>
					  <td width="6">&ensp;</td>
					  <td width="200" class="listRow"><label><a href="#" onclick="opPaneles('xProceExa')">DETALLE DE PLANTILLA</a></label></td>
					  <td width="6">&ensp;</td>
					  <td width="200" class="listRow"><label><a href="#" onclick="opPaneles('xCotizacion')">PRESUPUESTO SERVICIO</a></label></td>
					  <td width="6">&ensp;</td>
					  <td width="200" class="listRow"><label><a href="#" onclick="opPaneles('xCotizacionRealizada')">CONSULTAR COTIZACIONES</a></label></td>
                    </tr>
                  </table>
                </div>
</div>
<!-- SE REALIZA EL LLENADO DEL MAESTRO DE LAS PLANTILLAS ACTIVA = 1 -->
			<?php
					$accion = isset($_POST['accion']) ? $_POST['accion'] : "";
					if($accion == 'guardar')
					{
						?>
						<div id="xAlmacenoPlantilla" class="xAlmacenoPlantilla" align="center" style="display: block">
						<?php
						$existe_plantilla = mysql_queryV("select * from cliame_000329 where Codpla = '$Codpla'");
						$resultado = mysql_fetch_array($existe_plantilla);
						if ($resultado > 0){
						?>
    						<div style="text-align: center" class="row">
        					<form method="post" action="Menuplantilla.php">
            				<label style="color: #080808"><strong>EL DATO YA EXISTE</strong> </label>
            				<strong>POR FAVOR DIGITAR EL CODIGO EN LA CONSULTA </strong>
            				<br>
            				<br>
							<input type="submit" class="text-success" value="CONSULTA"/>
							</form>
							</div>
						</div>	
   					<?php
	
					}else{
					/////MATRIX/////
					
					mysql_queryV("INSERT INTO cliame_000329(medico,Fecha_data,Hora_data,Codpla,Nompla,Estado,Seguridad,id)values
					('cliame','$Fecha_data','$Hora_data','$Codpla','$Nompla','$Estado','$wuse','')");
 					
					?>
						<div class="divxAlmacenoPlantilla" align="center">
								<div style="margin-top: 10px;  text-align: center" class="row">
								<form method="post" action="Menuplantilla.php">
								<label style="color: #080808"><strong>DATOS ALMACENADOS CORRECTAMENTE</strong> </label>
								<br><br>
								<input type="submit" class="text-success" value="ACEPTAR"/>
								</form>
								</div>
						</div>
					<?php
				   	}
				   	}
					?>

			<?php
			if($activa == 1)
			{
				?>
				<div id="xPlantilla" class="divxPlantilla" align="center" style="display: block">
				<?php
			}
			else
			{
				?>
				<div id="xPlantilla" class="divxPlantilla" align="center" style="display: none">
				<?php
			}
			?>
                <div id="divContenido1" class="divContenido1">
				<td width="6">&ensp;</td>
				
				<!-- FUNCION PARA INSERTAR EN LA TABLA DE CLIAME_000329 DEL MAESTRO DE PLANTILLAS -->
					
				<!-- INICIO DE FORMULARIO -->		
					<form action="Menuplantilla.php" method="post">
  					<div align="center"><strong>Buscar codigo de plantilla:</strong> 
    				<input name="palabra">
    				<input type="submit" name="buscador" value="Buscar">
						<td width="187" class="listRow"><label><a href="#" onclick="opPaneles('xNuevaPlantilla')">NUEVA PLANTILLA</a></label></td>
			  		</div>
					<input type="hidden" value="1" name="plantilla_activa"/>
					</form>
					<?php
							if($_POST['buscador'])
								{
									$buscar = $_POST['palabra'];
									// Si est&aacute; vac&iacute;o, lo informamos, sino realizamos la búsqueda
									if(empty($buscar))
									{
										$select_plantilla = mysql_queryV("SELECT * from cliame_000329");
										?>
										<table width="1000" height="44" border="1">
										  <tr>
										  	<td style="background-color: #C3D9FF" height="18"><div align="center"><strong>CODIGO PLANTILLA </strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>DESCRIPCION</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>ESTADO </strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>ACCION</strong></div></td>
										  </tr>	
										<?php
											while($resultado=mysql_fetch_array($select_plantilla))
    										{
												$Codpla = $resultado[3];   
												$Nompla = $resultado[4];
												$Estado = $resultado[5];
										?>
										  <tr>
										  <td width="100" height="18"><?php echo $Codpla ?> <div align="center"></div></td>
											<td width="200"><?php echo $Nompla ?></td>
											<td width="50"><?php echo $Estado ?></td>
											<td width="50"><a href="editarPlantilla.php?actualizar=<?php echo $Codpla ?>">EDITAR </a></td>
										  </tr>
										
										<?php
											}	
							?>
										</table>
									<?php								
									}else
									{
								// Conexión a la base de datos y seleccion de registros
									$select_plantilla = mysql_queryV("SELECT * from cliame_000329 WHERE Codpla = '$buscar'");
									while($resultado=mysql_fetch_array($select_plantilla))
    								{
										$Codpla = $resultado[3];   
										$Nompla = $resultado[4];
										$Estado = $resultado[5];
										?>
										<table width="1000" height="44" border="1">
										  <tr>
											<td style="background-color: #C3D9FF" height="18"><div align="center"><strong>CODIGO PLANTILLA </strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>DESCRIPCION</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>ESTADO </strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>ACCION</strong></div></td>
										  </tr>
										  <tr>
										  <td width="100" height="18"><?php echo $Codpla ?> <div align="center"></div></td>
											<td width="200"><?php echo $Nompla ?></td>
											<td width="50"><?php echo $Estado ?></td>
											<td width="50"><a href="editarPlantilla.php?actualizar=<?php echo $Codpla ?>">EDITAR </a></td>
										  </tr>
										</table>
										<?php
							 		}
								}
							}	
					?>
					
					
				</div>	
				</div> <!-- FIN DEL CONTENIDO DE LA CONSULTA DE LA PLANTILLA -->
				<!-- SE TRAE EL FORMULARIO DEL MAESTRO DE LAS PLANTILLAS -->				
				<div id="xNuevaPlantilla" class="divxNuevaPlantilla" align="center" style="display: none">	
					<form action="Menuplantilla.php" method="post">
					  <table width="1000" border="1" align="center">
						<tr>
						<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong> MAESTRO DE PLANTILLA </strong></p> </td>
					   </tr>
					   </table>		
						<p>&nbsp;</p>
						<div align="center">
						<table width="600" border="0">
						  <tr>
							<td bgcolor="#C3D9FF"><p align="left"><strong>Codigo de Plantilla:</strong></p></td>
							<td>
							<strong>
							  <input name="Codpla" type="text" id="Codpla" size="15" value="" />
							</strong></td>
						  </tr>
						  <tr>
							<td bgcolor="#C3D9FF"><p align="left"><strong>Nombre de Plantilla:</strong></p></td>
							<td>
							<strong>
							  <input name="Nompla" type="text" id="Nompla" size="30" value="" />
							</strong></td>
						  </tr>
						  <tr>
						  	<td bgcolor="#C3D9FF"><p align="left"><strong>Estado:</p></td>
                             <td>
							   <select name="Estado" id="Estado">
                                <option value="on" selected>Activo</option>
                                <option value="off">Inactivo</option>
                              </select>
                              </strong>
						  	 </td>
						  </tr>  
						  <tr>
							<td height="35" colspan="2"><div align="center">
							  <p>
							  	<input name="accion" type="hidden" value='guardar' />
								<input name="guardar" type="submit" class="btn-primary" value="Guardar" />
								<a href="#" onclick="opPaneles('xPlantilla')">RETORNAR</a></label>
								</p>
							</div>
							</td>
						  </tr>
						</table>
					  </div>
					</form>
                </div>
            </div>
			
			
			
            <!-- SE REALIZA EL LLENADO DEL MAESTRO DE LOS PROCEDIMIENTOS Y DE LOS EXAMENES = PROCEDIMIENTOS EN EL DETALLE DE LA PLATILLA -->
			<?php
					$accion = isset($_POST['accion']) ? $_POST['accion'] : "";
					if($accion == 'guardarDetalle')
					{
						?>
						<div id="xAlmacenoDetalle" class="xAlmacenoDetalle" align="center" style="display: block">
						<?php
						$existe_detalle = mysql_queryV("select * from cliame_000330 where Codpla = '$Codpla' and Codpro = '$CodPro'");
						$resultado = mysql_fetch_array($existe_detalle);
						if ($resultado > 0){
						?>
    						<div style="text-align: center" class="row">
        					<form method="post" action="Menuplantilla.php">
            				<label style="color: #080808"><strong>EL DATO YA EXISTE</strong> </label>
            				<strong>POR FAVOR DIGITAR EL CODIGO EN LA CONSULTA </strong>
            				<br>
            				<br>
							<input type="submit" class="text-success" value="CONSULTA"/>
							</form>
							</div>
						</div>
						<?php
	
					}else{
					/////MATRIX/////
					
					mysql_queryV("INSERT INTO cliame_000330(medico,Fecha_data,Hora_data,Codpla,CodPro,Cantidad,Concepto,Estado,Seguridad,id)values
					('cliame','$Fecha_data','$Hora_data','$Codpla','$CodPro','$Cantidad','$Concepto','$Estado','$wuse','')");
 					
					?>
						<div class="divxAlmacenoPlantilla" align="center">
						<table border="0">
							<tr>
							    <td>	
								<div style="margin-top: 10px;  text-align: center" class="row">
									<form method="post" action="Menuplantilla.php">
									<label style="color: #080808"><strong>DATOS ALMACENADOS CORRECTAMENTE</strong> </label>
									<br><br>
									<input type="submit" class="text-success" value="ACEPTAR"/>
									</form>
								</div>
								</td>	
							</tr>	
						</table>		
						</div>
					<?php
				   	}
				   	}
					?>
			
			
			<?php
			if($activa_procexam == 2)
			{
				?>
				<div id="xProceExa" class="divxProceExa" align="center" style="display: block">
				<?php
			}
			else
			{
				?>
				<div id="xProceExa" class="divxProceExa" align="center" style="display: none">
				<?php
			}
			?>
                <div id="divContenido1" class="divContenido1">
				<td width="6">&ensp;</td>		
					<form action="Menuplantilla.php" method="post">
  					<div align="center"><strong>Buscar Codigo de Plantilla:</strong> 
    				<input name="palabra_examen">
    				<input type="submit" name="buscador" value="Buscar">
						<td width="187" class="listRow"><label><a href="#" onclick="opPaneles('xNuevoProceExam')">INGRESAR CODIGO AL DETALLE</a></label></td>
			  		</div>
					<input type="hidden" value="2" name="activa_exam_proce"/>
					</form>			
					<?php
							if($_POST['buscador'])
								{
									$buscar = $_POST['palabra_examen'];
									// Si est&aacute; vac&iacute;o, lo informamos, sino realizamos la búsqueda
									
									if(empty($buscar))
									{
										echo 'POR FAVOR DIGITAR PLANTILLA DE CONSULTA';								
									}else
									{
									// Conexión a la base de datos y seleccion de registros
									// Querys para mostrar en la tabla
										$select_detalle = mysql_queryV("SELECT * from cliame_000330 where Codpla='$buscar' ORDER BY id");
										$select_nomPlan = mysql_queryV("SELECT * from cliame_000329 where Codpla='$buscar' ORDER BY id");
										$resultado_nomPlan=mysql_fetch_array($select_nomPlan);
										$Placod = $resultado_nomPlan[3];
										$Nompla = $resultado_nomPlan[4];
										
												
										?>
										<table width="1000" height="44" border="1">
										  
										  <tr><td colspan="7" style="background-color: #C3D9FF" height="18"><div align="center"><strong>CODIGO PLANTILLA </strong></div></td> </tr>
										  <tr>						
										  <td colspan="7" width="100" height="18"><div align="center"><strong><?php echo $Placod.'-'.$Nompla ?></strong></div></td> 
										  </tr>
										  <tr>						
											<td style="background-color: #C3D9FF"><div align="center"><strong>CODIGO</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>DESCRIPCION</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>CANTIDAD</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>CONCEPTOS</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>ESTADO</strong></div></td>									
										  </tr>	
										<?php
											while($resultado=mysql_fetch_array($select_detalle))
    										{
												//$Codpla = $resultado[3];   
												$CodPro = $resultado[4];
												$Cantidad = $resultado[5];
												$Concepto = $resultado[6];
												$Estado = $resultado[7];
												//QUERY CON UNIX PARA CONSULTAR EL NOMBRE DEL PROCEDIMIENTO, EXAMEN O MEDICAMENTO
												if ($CodPro !== null) {
													$select_inpro = "select * from inpro where procod = '$CodPro'";
													$resultado_inpro=odbc_do($conex_o, $select_inpro);
													odbc_fetch_row($resultado_inpro);
													//$Pronom =$CodPro;
													$Pronom = odbc_result($resultado_inpro, 2);
													//CONDICION INEXA
												
													if ($Pronom == null) {														
														$select_inexa = "select * from inexa where exacod = '$CodPro'";
														$resultado_inexa=odbc_do($conex_o, $select_inexa);
														odbc_fetch_row($resultado_inexa);
														//$Pronom ='2';
														$Pronom = odbc_result($resultado_inexa, 2);														
												    }
													//CONDICION IVART
													if ($Pronom == null){
														$select_ivart = "select * from ivart where artcod = '$CodPro'";
														$resultado_ivart=odbc_do($conex_o, $select_ivart);
														odbc_fetch_row($resultado_ivart);
														//$Pronom ='3';
														$Pronom = odbc_result($resultado_ivart, 2);
														
												    }
													//CONDICION VACIO
													If ($Pronom == null){
														$Pronom = 'CODIGO NO EXISTE EN UNIX';
													}
													If ($CodPro == '0'){
														$Pronom = 'Demas insumos utilizados en procedimientos';
													}
												}	
												else {
													$Pronom = 'CODIGO NO EXISTE';
												}
												
												
												
												
										?>
										  <tr>
											<td width="50"><?php echo $CodPro ?></td>
											<td width="200"><?php echo $Pronom ?></td>
											<td width="50"><?php echo $Cantidad ?></td>
											<td width="50"><?php echo $Concepto ?></td>
											<td width="50"><?php echo $Estado ?></td>
											<td width="50"><a href="editarDetalle.php?actualizar=<?php echo $Placod ?>&actualizarDos=<?php echo $CodPro ?>">EDITAR </a></td>
										  </tr>
										
										<?php
											}	
							?>
										</table>
										<?php
							 		}
								}
					?>
					
				</div>	
				</div> <!-- FIN DEL CONTENIDO DE LA CONSULTA DE LOS PROCEDIMIENTOS -->
				<!-- SE TRAE EL FORMULARIO DEL MAESTRO DE LOS PROCEDIMIENTOS -->
				<div id="xNuevoProceExam"  class="divxNuevoProceExam" align="center" style="display: none">	
				<td width="6">&ensp;</td>	
					<form action="Menuplantilla.php" method="post" id="detallePlantilla" name="detallePlantilla">
					  <table width="1000" border="0" align="center">
						<tr>
						<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong> DETALLE DE PLANTILLA </strong></p> </td>
					   </tr>
					   
					   
					    <tr>
							<td>							
									<select id="Codpla" name="Codpla">
										<?php
										$queryDetalle = "select Codpla,Nompla from cliame_000329 WHERE Estado = 'on' ORDER BY Codpla ASC";
										$resutlDetalle = mysql_query($queryDetalle, $conex) or die (mysql_errno()." - en el query: ".$queryDetalle." - ".mysql_error());
											while($datoplantilla = mysql_fetch_assoc($resutlDetalle))
											{
												$Codpla = $datoplantilla['Codpla'];    $Planom = $datoplantilla['Nompla'];
												echo "<option value='".$Codpla."'>".$Codpla.' - '.$Planom."</option>";
											}
										?>
										   <option selected disabled>Seleccione Platilla...</option>
									</select>
							 
							</td>
						 </tr>
						  </table>
						  <td width="6">&ensp;</td>
						  <table width="800" border="0" align="center">
						  
						  <tr>
							<td bgcolor="#C3D9FF"><p align="left"><strong>Procedimiento,Insumo o Examen:</strong></p></td>
							<td>
							<strong>
							  <input name="CodPro" type="text" id="CodPro" size="20" value="" onblur="mensaje(this.value)"/>
							</strong></td>
						  </tr>
						  <tr>
							<td bgcolor="#C3D9FF"><p align="left"><strong>Descripcion:</strong></p></td>
							<td>
							<strong>
							  <input style="background-color:#dddddd;" name="Coddes" type="text" id="Coddes" size="70" readonly />
							</strong></td>
						  </tr>
						  <tr>
							<td bgcolor="#C3D9FF"><p align="left"><strong>Cantidad:</strong></p></td>
							<td>
							<strong>
							  <input name="Cantidad" type="number" id="Cantidad" size="6"/>
							</strong></td>
						  </tr>
						  <tr>
							<td bgcolor="#C3D9FF"><p align="left"><strong>Concepto:</strong></p></td>
							<td>
							<select id="Concepto" name="Concepto" select style="width:500px">
										<?php
										$select_facon = "select concod,connom from facon where conact = 'S'";
										$resultado_facon=odbc_do($conex_o, $select_facon);
										while(odbc_fetch_row($resultado_facon)){
											$concod = odbc_result($resultado_facon, 1);
											$connom = odbc_result($resultado_facon, 2);
											echo "<option value='".$concod."'>".$concod.' - '.$connom."</option>";											
										}
										?>
											 <option value="0" selected>0-SIN CONCEPTO</option>
										   <option selected disabled>Seleccione Concepto...</option>
									</select>
							</td>	
						</tr>
						  <tr>
						  	<td bgcolor="#C3D9FF"><p align="left"><strong>Estado:</p></td>
                             <td>
							   <select name="Estado" id="Estado">
                                <option value="on" selected>Activo</option>
                                <option value="off">Inactivo</option>
                              </select>
                              </strong>	
						  	 </td>
						  </tr> 
						  <tr>
							<td height="35" colspan="2">
							<div align="center">
							  <p>
								<input name="accion" type="hidden" value='guardarDetalle' />
								<input name="guardar" type="submit" class="btn-primary" value="Guardar" />
								<a href="#" onclick="opPaneles('xProceExa')">RETORNAR</a></label>
							  </p>
							</div>
							</td>
						  </tr>
						</table>
					  </div>
					</form>
                </div>	
				
				
				
				
             <!-- FIN DEL CONTENIDO DE LA CONSULTA DE LOS CONCEPTOS -->
				<!-- SE TRAE EL FORMULARIO DEL MAESTRO DE LAS COTIZACIONES -->
			<?php
					$accion = isset($_POST['accion']) ? $_POST['accion'] : "";
					if($accion == 'guardarCotizacion')
					{
					?>
						<div id="xAlmacenoCotizacion"  class="xAlmacenoCotizacion" align="center" style="display: block">
						<?php
						$existe_cotizacion = mysql_queryV("select * from cliame_000337 where CodplaR = '$CodplaRe' and TidR = '$TidRe' and Identificacion='$Identificacion' and Fecha='$Fecha' and EmpcodR='$EmpcodRe'");
						$resultado_cotizacion = mysql_fetch_array($existe_cotizacion);
						if ($resultado_cotizacion > 0){
						?>
    						<div style="text-align: center" class="row" border="1">
								<form method="post" action="Menuplantilla.php">
								<label style="color: #080808"><strong>EL DATO YA EXISTE</strong> </label>
								<strong>POR FAVOR DIGITAR EL CODIGO EN CONSULTAR COTIZACIONES </strong>
								<br>
								<br>
								<input type="submit" class="text-success" value="ACEPTAR"/>
								</form>
								</div>
							</div>
						<?php
						}else{
						while(true) {
							 
							$item1 = current($items1);
							$item2 = current($items2);
							$item3 = current($items3);
							$item4 = current($items4);
							$item5 = current($items5);
							$item6 = current($items6);
			
							//ASIGNAR LA SEPARACION A VARIABLES
							$descri=(( $item1 !== false) ? $item1 : ", &nbsp;");
							$cod=(( $item2 !== false) ? $item2 : ", &nbsp;");
							$can=(( $item3 !== false) ? $item3 : ", &nbsp;");
							$contab=(( $item4 !== false) ? $item4 : ", &nbsp;");
							$uni=(( $item5 !== false) ? $item5 : ", &nbsp;");
							$tot=(( $item6 !== false) ? $item6 : ", &nbsp;");
							
							$uniR = str_replace(",","",$uni);
							$totR = str_replace(",","",$tot);
							//// CONCATENAR LOS VALORES EN ORDEN PARA SU FUTURA INSERCIÓN ////////
							$valores='('.$descri.',"'.$cod.'","'.$can.'","'.$contab.'","'.$uniR.'","'.$totR.'"),';
							//////// YA QUE TERMINA CON COMA CADA FILA, SE RESTA CON LA FUNCIÓN SUBSTR EN LA ULTIMA FILA /////////////////////
							$valoresQ= substr($valores, 0, -1);
							mysql_queryV("INSERT INTO cliame_000337(Medico,Fecha_data,Hora_data,Identificacion,TidR,Nompac,Historia,Ingreso,Fecha,Nmedico,CodplaR,EmpcodR,Tprocedimiento,Mipres,NumMipres,Descritab,Codtab,Canttab,Concetab,Unitab,Tottab,Total_cantidad,Seguridad)
										values('Cliame','$Fecha_data','$Hora_data','$Identificacion','$TidRe','$Nompac','$Historia','$Ingreso','$Fecha','$Medico','$CodplaRe','$EmpcodRe','$Tprocedimiento','$Mipres','$NumMipres','$descri','$cod','$can','$contab','$uniR','$totR','$total_cantidadR','$wuse')");
							//$sqlRes=$conexion->query($sql) or mysql_error();
 
                     
							// Up! Next Value
							$item1 = next( $items1 );
							$item2 = next( $items2 );
							$item3 = next( $items3 );
							$item4 = next( $items4 );
							$item5 = next( $items5 );
							$item6 = next( $items6 );
							
							// Check terminator
							if($item1 === false && $item2 === false && $item3 === false && $item4 === false && $item5 === false && $item6 === false) break;
						}
						
					?>
						<div class="xAlmacenoCotizacion" align="center">
						<div id="xAlmacenoCotizacion"  class="xAlmacenoCotizacion" align="center" style="display: block">	
						<table border="0">
							<tr>
							    <td>
								<div style="margin-top: 10px;  text-align: center" class="row">
									<form method="post" action="Menuplantilla.php">
									<label style="color: #080808"><strong>DATOS ALMACENADOS CORRECTAMENTE</strong> </label>
									<br><br>
									<input type="submit" class="text-success" value="ACEPTAR"/>
									</form>
								</div>
								</td>	
							</tr>	
						</table>		
						</div>
						</div>
						<?php
				   	
				   	}
					}
					?>
			<?php	
			if($activa_calcular == 3)
			{
				?>
				<div id="xCotizacion" class="divxCotizacion" align="center" style="display: block">
				<?php
			}
			else
			{
				?>
				<div id="xCotizacion" class="divxCotizacion" align="center" style="display: none">
				<?php
			}
				
			?>	
				
				
            <div id="xCotizacion" class="divxCotizacion" align="center" style="display: block">
				<td width="6">&ensp;</td>	
					<form action="Menuplantilla.php" method="post" id="presupuesto" name="presupuesto">
					  <table width="900" border="1" align="center">
							<tr>
								<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>PROMOTORA MEDICA LAS AMERICAS NIT 800067065-9</strong></p> </td>
							</tr>
							<tr>
								<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>PRESUPUESTO DE SERVICIOS</strong></p> </td>
							</tr>
						</table>	
						<td width="6">&ensp;</td>
					   <table width="900" border="1" align="center">					   
							<tr>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Fecha:</strong></p></td>
								<td>
								<strong><input name="Fecha" type="date" id="Fecha" size="20" value="<?php echo $Fecha ?>" required /></strong>
								</td>
							</tr>							
							<tr>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Tipo de Identificacion:</strong></p>
								</td>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Identificacion:</strong></p>
								</td>
							</tr>	
								<td>
								<select required id="TidR" name="TidR" select style="width:500px" onchange="buscarPaciente(this.value,Identificacion)">
										<?php
										$queryDetalleId = "select Codigo,Descripcion from root_000007 WHERE Estado = 'on'";
										$resutlDetalleId = mysql_query($queryDetalleId, $conex) or die (mysql_errno()." - en el query: ".$queryDetalleId." - ".mysql_error());
											while($datoplantillaId = mysql_fetch_assoc($resutlDetalleId))
											{
												$Tid = $datoplantillaId['Codigo'];    
												$TipoId = $datoplantillaId['Descripcion'];												
												echo "<option value='".$Tid."'>".$Tid.' - '.$TipoId."</option>";																							
											}
											?>
											<option selected><?php echo $TidRe ?></option>
											
										?>
									</select>
								</td>
								<td><strong><input name="Identificacion" type="text" id="Identificacion" size="30" value="<?php echo $Identificacion ?>" onblur="buscarPaciente(this.value,TidR)" required /></strong>
								</td>
							</tr>
							<tr>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Nombre del Paciente:</strong></p>
								</td>
								<td><strong><input name="Nompac" type="text" id="Nompac" size="50" value="<?php echo $Nompac ?>" required /></strong>
								</td>
							</tr>
							<tr>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Historia:</strong></p>
								</td>
								<td><strong><input name="Historia" type="text" id="Historia" size="10" value="<?php echo $Historia ?>"/></strong>
								</td>
							</tr>							
							<tr>	
								<td bgcolor="#C3D9FF"><p align="left"><strong>Ingreso:</strong></p>
								</td>
								<td><strong><input name="Ingreso" type="text" id="Ingreso" size="3" value="<?php echo $Ingreso ?>"/></strong>
								</td>
							</tr>
							<tr>	
								<td bgcolor="#C3D9FF"><p align="left"><strong>Medico:</strong></p>
								</td>
								<td><strong><input name="Medico" type="text" id="Medico" size="50" value="<?php echo $Medico ?>"/></strong>
								</td>
							</tr>
							<tr>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Nombre y CUPS del procedimiento:</strong></p>
								</td>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Entidad:</strong></p>
								</td>
							</tr>
							<tr>						
								<td>							
									<select id="CodplaR" name="CodplaR" style="width:500px" required>
										<?php
											$queryDetalle = "select Codpla,Nompla from cliame_000329 WHERE Estado = 'on' ORDER BY Codpla ASC";
											$resutlDetalle = mysql_query($queryDetalle, $conex) or die (mysql_errno()." - en el query: ".$queryDetalle." - ".mysql_error());
												while($datoplantilla = mysql_fetch_assoc($resutlDetalle))
												{	
													$CodplaR = $datoplantilla['Codpla'];    $PlanomR = $datoplantilla['Nompla'];
													echo "<option value='".$CodplaR."'>".$CodplaR.' - '.$PlanomR."</option>";
												}
											?>
											<option selected><?php echo $CodplaRe ?></option>
										   
									</select>
							 
								</td>
								<td>							
									<select id="EmpcodR" name="EmpcodR" select style="width:500px" required>
										<?php
										$queryEntidad = "select Empcod,Empnom,Emptar from cliame_000024 WHERE Empest = 'on' and Emptem NOT IN ('02','64','14') ORDER BY Emptar,Empnom ASC";
										$resutlEntidad = mysql_query($queryEntidad, $conex) or die (mysql_errno()." - en el query: ".$queryEntidad." - ".mysql_error());
											while($datoEntidad = mysql_fetch_assoc($resutlEntidad))
											{
												$Empcod = $datoEntidad['Empcod'];    $Empnom = $datoEntidad['Empnom']; $Emptar = $datoEntidad['Emptar'];
												echo "<option value='".$Empcod."'>".$Emptar.' - '.$Empnom.' - '.$Empcod."</option>";
											}
										?>
										   <option selected><?php echo $EmpcodRe ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Tipo de procedimiento:</p></td>
								<td>
									<select name="Tprocedimiento" id="Tprocedimiento" required>
									<option value="<?php echo $Tprocedimiento ?>" selected></option>
									<option value="POS" selected>POS</option>
									<option value="NO_POS">NO POS</option>
									<option value="INSUMO_ALTO_COSTO">Con insumos de alto costo</option>
									</select></strong>
								</td>
							</tr> 
							<tr>
								<td bgcolor="#C3D9FF"><p align="left"><strong>Requiere MIPRES:</p></td>
								<td>
									<select name="Mipres" id="Mipres" value="<?php echo $Mipres ?>" required onchange="mostrarCampo(this.value)">
									<option value="SI" selected>Si</option>
									<option value="NO">No</option>	
									</select></strong>										
								</td>
							</tr>
							<tr>	
								<td bgcolor="#C3D9FF"><p align="left"><strong>Numero de MIPRES:</strong></p>
								</td>
								<td><strong><input name="NumMipres" type="text" id="NumMipres" size="50" value="<?php echo $NumMipres ?>" /></strong>
								</td>
							</tr>
							<tr>
								<td height="35" colspan="2">
								<div align="center">
								<p>
									<input name="calcular_plantilla" type="hidden" value='3' />
									<input id="calcular" name="calcular" type="button" onclick="this.form.submit()" value="CALCULAR" class="btn-primary"/>	
									<a href="Menuplantilla.php" onclick="opPaneles('xProceExa')">CANCELAR</a></label>
								</p>
								</div>
								</td>
							</tr>
					  </div>
					
				</table>
				<?php
				if($_POST['CodplaR'] and $_POST['EmpcodR'])
								{
									?>
										<script>
											document.getElementById('calcular').style.display='none';
											document.getElementById('EmpcodR').style.display='none';
											document.getElementById('CodplaR').style.display='none';
											document.getElementById('Mipres').style.display='none';
										
										</script>
									<?php
									$buscar_plantilla = $_POST['CodplaR'];
									$buscar_responsable = $_POST['EmpcodR'];
									// Si est&aacute; vac&iacute;o, lo informamos, sino realizamos la búsqueda
									
									if(empty($buscar_plantilla))
									{
										echo 'POR FAVOR DIGITAR PLANTILLA DE CONSULTA';								
									}else
									{
										// Conexión a la base de datos y seleccion de registros
									// Querys para mostrar en la tabla
									    //Query para obtener el detalle y llenar toda la tabla cuando concepto no se ha 0
										$select_detalle = mysql_queryV("SELECT * from cliame_000330 where Codpla='$buscar_plantilla' and Estado='on' and Concepto != '0' order by id");
										// formar el en cabezado de la tabla con codigo plantilla y descripcion
										$select_nomPlan = mysql_queryV("SELECT * from cliame_000329 where Codpla='$buscar_plantilla'");
										$resultado_nomPlan=mysql_fetch_array($select_nomPlan);
										$Placod = $resultado_nomPlan[3];
										$Nompla = $resultado_nomPlan[4];
										// query para obtener la tarifa
										$select_tarifa = mysql_queryV("SELECT Empcod,Empnom,Emptar from cliame_000024 where Empcod='$buscar_responsable'");
										$resultado_tarifa=mysql_fetch_array($select_tarifa);
										$EmpcodR = $resultado_tarifa[0];
										$EmpnomR = $resultado_tarifa[1];
										$EmptarR = $resultado_tarifa[2];
										//QUERY PARA LLENAR LA TABLA CON CONCEPTO 0 Y OBTENER EL LOS NOMBRES DEL CONCEPTO
										$select_detalle_concep = mysql_queryV("SELECT * from cliame_000330 where Codpla='$buscar_plantilla' and Estado='on' and Concepto=0 order by id");
										$IdRowIn = 0;		
										?>
										<!-- TABLA PROVICIONAL PARA QUE PUEDAN COPIAR LOS DATOS-->
										
										<td width="6">&ensp;</td>
										<table width="1000" border="1" align="center">					   
											<tr>
												<td bgcolor="#C3D9FF"><p align="left"><strong>Fecha:</strong></p></td>
													<td>
													<strong><?php echo $Fecha ?></strong>
													</td>
												</tr>
												<tr>
													<td bgcolor="#C3D9FF"><p align="left"><strong>Nombre del Paciente:</strong></p>
													</td>
													<td><strong><?php echo $Nompac ?></strong>
													</td>
												</tr>
												<tr>
													<td bgcolor="#C3D9FF"><p align="left"><strong>Identificacion:</strong></p>
													</td>
													<td><strong><?php echo $Identificacion ?></strong>
													</td>
												</tr>
												<tr>
													<td bgcolor="#C3D9FF"><p align="left"><strong>Historia:</strong></p>
													</td>
													<td><?php echo $Historia ?></strong>
													</td>
												</tr>
												<tr>	
													<td bgcolor="#C3D9FF"><p align="left"><strong>Ingreso:</strong></p>
													</td>
													<td><?php echo $Ingreso ?></strong>
													</td>
												</tr>
												<tr>	
													<td bgcolor="#C3D9FF"><p align="left"><strong>Medico:</strong></p>
													</td>
													<td><strong><?php echo $Medico ?></strong>
													</td>
												</tr>
												<tr>
													<td bgcolor="#C3D9FF"><p align="left"><strong>Nombre y CUPS del procedimiento:</strong></p>
													</td>
													<td bgcolor="#C3D9FF"><p align="left"><strong>Entidad:</strong></p>
													</td>
												</tr>
												<tr>						
													<td>							
														<?php echo $Placod.'-'.$Nompla ?>
												 
													</td>
													<td>
														<?php echo $EmpcodR.'-'.$EmpnomR.'-'.$EmptarR ?>														
													</td>
												</tr>
												<tr>
													<td bgcolor="#C3D9FF"><p align="left"><strong>Tipo de procedimiento:</p></td>
													<td>														
														<?php echo $Tprocedimiento ?>
													</td>
												</tr> 
												<tr>
													<td bgcolor="#C3D9FF"><p align="left"><strong>Requiere MIPRES:</strong></p></td>
													<td>
													<strong>
														<?php echo $Mipres ?>
													</strong>
													</td>
												</tr>
												<tr>
													<td bgcolor="#C3D9FF"><p align="left"><strong>Numero de MIPRES:</strong></p></td>
													<td>
													<strong>
														<?php echo $NumMipres ?>
													</strong>
													</td>
												</tr>
										  </div>
										
									</table>
										<!-- PROCESOP BUENO -->
										<table width="1000" height="44" border="1">
										  
										  <tr><td colspan="7" style="background-color: #C3D9FF" height="18"><div align="center"><strong>CODIGO PLANTILLA </strong></div></td> </tr>
										  <tr>						
										  <td colspan="7" width="50" height="18"><div align="center"><strong><?php echo $Placod.'-'.$Nompla ?></strong></div></td> 
										  </tr>												
										  <tr>	
											<td width="50" style="background-color: #C3D9FF"><div align="center"><strong>DESCRIPCION</strong></div></td>
											<td width="50" style="background-color: #C3D9FF"><div align="center"><strong>CODIGO</strong></div></td>											
											<td width="50" style="background-color: #C3D9FF"><div align="center"><strong>CANTIDAD</strong></div></td>
											<td width="50" style="background-color: #C3D9FF"><div align="center"><strong>CONCEPTOS</strong></div></td>
											<td width="50" style="background-color: #C3D9FF"><div align="center"><strong>V/R UNITARIO </strong></div></td>
											<td width="50" style="background-color: #C3D9FF"><div align="center"><strong>V/R TOTAL</strong></div></td>
										  </tr>	
											<?php
											$total_sumaC = 0;
											
											while($resultado_concepto=mysql_fetch_array($select_detalle_concep))
    										{
												//$Codpla = $resultado[3];   
												$CodProC = $resultado_concepto[4];
												$CantidadC = $resultado_concepto[5];
												$ConceptoC = $resultado_concepto[6];
												$EstadoC = $resultado_concepto[7];									
												//QUERY CON UNIX PARA CONSULTAR EL NOMBRE DEL PROCEDIMIENTO, EXAMEN O MEDICAMENTO
												//VALIDACIONES PARA TRAER EL CONCEPTO Y EL VALOR, NOMBRE
												
												if ($CodProC !== null and $ConceptoC == 0) {
													$varprimeringreso=0;
													$select_inprotarC = "select protarpro,protarcon,protarval,concod,connom from inprotar,outer facon where inprotar.protarcon=facon.concod and protarpro = '$CodProC' and protartar = '$EmptarR' and protarcco in ('1330','1335')";
													$resultado_inprotarC=odbc_do($conex_o, $select_inprotarC);
													$total_sumaInp = 0;
													
													while (odbc_fetch_row($resultado_inprotarC)){
														$protarproC = odbc_result($resultado_inprotarC, 1);	
														$protarconC = odbc_result($resultado_inprotarC, 2);		
														$protarvalC = odbc_result($resultado_inprotarC, 3);
														$connomC = odbc_result($resultado_inprotarC, 5);
														if ($protarvalC > 0){
															$varprimeringreso=1;															
															$IdRowIn = $IdRowIn + 1;
															?>																
																<tr>
																<td width="170"><input name="descritab[]" type="text" id="descritab" size="60" readonly value="<?php echo $connomC ?>"/></td>											
																<td width="50"><input name="codtab[]" type="text" id="codtab" size="10" readonly value="<?php echo $protarproC ?>"/></td>																
																<td width="50"><input name="canttab[]" type="number" id="canttab<?php echo $IdRowIn ?>" size="10" onchange="multiplicar(this.value,<?php echo $IdRowIn ?>)" value="<?php echo $CantidadC ?>" /></td>																
																<td width="50"><input name="concetab[]" type="text" id="concetab" size="10" readonly value="<?php echo $protarconC ?>"/></td>												
																<td width="50"><input name="unitab[]" type="text" id="unitab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo number_format($protarvalC,0,'',',') ?>"/></td>
																<?php $total_valorInp= $CantidadC * $protarvalC ?>																
																<td width="50" align="center"><input name="tottab[]" type="text" id="tottab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo number_format($total_valorInp,0,'',',') ?>"/></td>
																<?php $total_sumaInp = $total_valorInp + $total_sumaInp ?>
																</tr>
															<?php
														}											
														
													}			
														//echo 'resultado del query'.$select_inprotarC; 
													//CONDICION INEXATAR
												    $varsegundoingreso = 0;		
													if ($varprimeringreso == 0 and $CodProC !== '0') {	
													    	
														$select_inexatarC = "select exatarexa,exatarcon,exatarval,concod,connom from inexatar,outer facon where inexatar.exatarcon=facon.concod and exatarexa = '$CodProC' and exatartar = '$EmptarR' and exatarcco in ('1330','1335')";
														$resultado_inexatarC=odbc_do($conex_o, $select_inexatarC);	
														$total_sumaIne = 0;			
														
														while (odbc_fetch_row($resultado_inexatarC)){
															$IdRowIn = $IdRowIn + 1;
															$protarproIn = odbc_result($resultado_inexatarC, 1);	
															$protarconCIn = odbc_result($resultado_inexatarC, 2);		
															$protarvalCIn = odbc_result($resultado_inexatarC, 3);
															$connomCIn = odbc_result($resultado_inexatarC, 5);
															?>															
															<td width="170"><input name="descritab[]" type="text" id="descritab" size="60" readonly value="<?php echo $connomCIn ?>"/></td>															
															<td width="50"><input name="codtab[]" type="text" id="codtab" size="10" readonly value="<?php echo $protarproIn ?>"/></td>														
															<td width="50"><input name="canttab[]" type="number" id="canttab<?php echo $IdRowIn ?>" size="10" onchange="multiplicar(this.value,<?php echo $IdRowIn ?>)" value="<?php echo $CantidadC ?>"/></td>															
															<td width="50"><input name="concetab[]" type="text" id="concetab" size="10" readonly value="<?php echo $protarconCIn ?>"/></td>															
															<td width="50"><input name="unitab[]" type="text" id="unitab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo number_format($protarvalCIn,0,'',',') ?>"/></td>
															<?php $total_valorIne= $CantidadC * $protarvalCIn ?>															
															<td width="50" align="center"><input name="tottab[]" type="text" id="tottab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo number_format($total_valorIne,0,'',',') ?>"/></td>
															<?php $total_sumaIne = $total_valorIne + $total_sumaIne ?>
															
															<?php
														
														}
														
														
														//echo 'resultado del query'.$select_inexatarC; 
														
																											
												    }
													if ($varprimeringreso == 0 && $varsegundoingreso == 0 and $CodProC !== '0'){
														$select_ivart = "select artcod,artnom,arttarval from ivart,outer ivarttar where ivart.artcod = ivarttar.arttarcod and artcod = '$CodProC' and arttartar='$EmptarR'";
														$resultado_ivart=odbc_do($conex_o, $select_ivart);
														odbc_fetch_row($resultado_ivart);
														$protarproIv = odbc_result($resultado_ivart, 1);
														$connomIv = odbc_result($resultado_ivart, 2);
														$protarvaIv = odbc_result($resultado_ivart, 3);	
														$IdRowIn = $IdRowIn + 1;
														?>
														<tr>
															
															<td width="170"><input name="descritab[]" type="text" id="descritab" size="60" readonly value="<?php echo $connomIv ?>"/></td>														
															<td width="50"><input name="codtab[]" type="text" id="codtab" size="10" readonly value="<?php echo $CodProC ?>"/></td>															
															<td width="50"><input name="canttab[]" type="number" id="canttab<?php echo $IdRowIn ?>" size="10" onchange="multiplicar(this.value,<?php echo $IdRowIn ?>)" value="<?php echo $CantidadC ?>"/></td>															
															<td width="50"><input name="concetab[]" type="text" id="concetab" size="10" readonly value="<?php echo $ConceptoC ?>"/></td>															
															<td width="50"><input name="unitab[]" type="text" id="unitab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo number_format($protarvaIv,0,'',',') ?>"/></td>
															<?php $total_valorC= $CantidadC * $protarvaIv ?>															
															<td width="50" align="center"><input name="tottab[]" type="text" id="tottab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo number_format($total_valorC,0,'',',') ?>"/></td>
															<?php $total_sumaC = $total_valorC + $total_sumaC ?>
														</tr>
														<?php
													}	
													//CONDICION VACIO
													If ($protarvalC == null){
														$protarvalC = 0;
													}
														If ($CodProC == '0'){
															$IdRowIn = $IdRowIn + 1;
															?>
															<tr>															
															<td width="170"><input name="descritab[]" type="text" id="descritab" size="60" readonly value="<?php echo 'Demas insumos utilizados en procedimientos' ?>"/></td>															
															<td width="50"><input name="codtab[]" type="text" id="codtab" size="10" readonly value="<?php echo $CodProC ?>"/></td>															
															<td width="50"><input name="canttab[]" type="number" id="canttab<?php echo $IdRowIn ?>" size="10" onchange="multiplicar(this.value,<?php echo $IdRowIn ?>)" value="<?php echo $CantidadC ?>"/></td>														
															<td width="50"><input name="concetab[]" type="text" id="concetab" size="10" readonly value="<?php echo $ConceptoC ?>"/></td>															
															<td width="50"><input name="unitab[]" type="text" title="Separar el punto que indica mil y millon por coma ," id="unitab<?php echo $IdRowIn ?>" size="10" onchange="multiplicarDos(this.value,<?php echo $IdRowIn ?>)" value="<?php echo '0' ?>"/></td>
															<?php //$total_valorC= $CantidadC * $protarvaIv ?>															
															<td width="50" align="center"><input name="tottab[]" type="text" id="tottab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo '0' ?>"/></td>
															<?php //$total_sumaC = $total_valorC + $total_sumaC ?>
															</tr>
															<?php
													}
												}	
												else {
													$protarvalC = 0;
												}
											?>
											
											<?php 					
											
											$total_sumaC = $total_sumaC + $total_sumaIne + $total_sumaInp; ?> 
													 
											<?php }
											
											?>
											
											<!-- PINTAR CUANDO EL RESULTADO SE TIENE CONCEPTO, TARIFA Y CODIGO -->
										<?php
											$total_suma = 0;
											while($resultado=mysql_fetch_array($select_detalle))
    										{
												//$Codpla = $resultado[3];   
												$CodPro = $resultado[4];
												$Cantidad = $resultado[5];
												$Concepto = $resultado[6];
												$Estado = $resultado[7];												
												//QUERY CON UNIX PARA CONSULTAR EL NOMBRE DEL PROCEDIMIENTO, EXAMEN O MEDICAMENTO
												if ($CodPro !== null ) {
													$select_inpro = "select * from inpro where procod = '$CodPro'";
													$resultado_inpro=odbc_do($conex_o, $select_inpro);
													odbc_fetch_row($resultado_inpro);									
													$Pronom = odbc_result($resultado_inpro, 2);
													//CONDICION INEXA
												
													if ($Pronom == null) {														
														$select_inexa = "select * from inexa where exacod = '$CodPro'";
														$resultado_inexa=odbc_do($conex_o, $select_inexa);
														odbc_fetch_row($resultado_inexa);														
														$exacod = odbc_result($resultado_inexa, 1);
														$Pronom = odbc_result($resultado_inexa, 2);
																											
												    }
													//CONDICION IVART
													if ($Pronom == null){
														$select_ivart = "select * from ivart where artcod = '$CodPro'";
														$resultado_ivart=odbc_do($conex_o, $select_ivart);
														odbc_fetch_row($resultado_ivart);
														$Pronom = odbc_result($resultado_ivart, 2);
												    }
													//CONDICION VACIO
													If ($Pronom == null){
														$Pronom = 'CODIGO NO EXISTE EN UNIX';
													}
													If ($CodPro == '0'){
														$Pronom = 'Demas insumos utilizados en procedimientos';
													}
												}	
												else {
													$Pronom = 'CODIGO NO EXISTE';
												}
												
												//VALIDACIONES PARA TRAER EL CONCEPTO Y EL VALOR
												if ($CodPro !== null and $Concepto !== 0) {
													$select_inprotar = "select * from inprotar where protarpro = '$CodPro' and protartar = '$EmptarR' and protarcon = '$Concepto' and protarcco in ('1330','1335')";
													$resultado_inprotar=odbc_do($conex_o, $select_inprotar);
													odbc_fetch_row($resultado_inprotar);									
													$protarval = odbc_result($resultado_inprotar, 8);
													//CONDICION INEXATAR
												
													if ($protarval == null) {														
														$select_inexatar = "select * from inexatar where exatarexa = '$CodPro' and exatartar = '$EmptarR' and exatarcon = '$Concepto' and exatarcco in ('1330','1335')";
														$resultado_inexatar=odbc_do($conex_o, $select_inexatar);
														odbc_fetch_row($resultado_inexatar);														
														//$exacod = odbc_result($resultado_inexa, 1);
														$protarval = odbc_result($resultado_inexatar, 8);
																											
												    }
													//CONDICION IVARTTAR
													if ($protarval == null){
														$select_ivarttar = "select * from ivarttar where arttarcod = '$CodPro' and arttartar = '$EmptarR'";
														$resultado_ivarttar=odbc_do($conex_o, $select_ivarttar);
														odbc_fetch_row($resultado_ivarttar);
														$protarval = odbc_result($resultado_ivarttar, 7);
												    }
													//CONDICION VACIO
													If ($protarval == null){
														$protarval = 0;
													}
														//If ($protarval == '0'){
															///$Pronom = 'Demas insumos utilizados en procedimientos';
													//}
												}	
												else {
													$protarval = 0;
												}
												
												
												
												$IdRowIn = $IdRowIn + 1;
										?>
											  <tr>													
													<td width="170"><input  name="descritab[]" type="text" id="descritab" size="60" readonly value="<?php  echo $Pronom ?>"/></td>													
													<td width="50"><input  name="codtab[]" type="text" id="codtab" size="10" readonly value="<?php echo $CodPro ?>"/></td>													
													<td width="50"><input  name="canttab[]" type="number" id="canttab<?php echo $IdRowIn ?>" size="10" onchange="multiplicar(this.value,<?php echo $IdRowIn ?>)" value="<?php echo $Cantidad ?>"/></td>																													
													<td width="50"><input  name="concetab[]" type="text" id="concetab" size="10" readonly value="<?php echo $Concepto ?>"/></td>													
													<td width="50"><input  name="unitab[]" type="text" id="unitab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo number_format($protarval,0,'',',') ?>"/></td>
													<?php $total_valor= $Cantidad * $protarval ?>													
													<td width="50" align="center"><input name="tottab[]" type="text" id="tottab<?php echo $IdRowIn ?>" size="10" readonly value="<?php echo number_format($total_valor,0,'',',') ?>"/></td>
													
												</tr>
													<?php $total_suma = $total_valor + $total_suma ?>
												<?php
											}	
										?>
										<td width="50">&ensp;</td>
										<td width="50">&ensp;</td>
										<td width="50">&ensp;</td>
										<td width="50">&ensp;</td>
										<td width="50">&ensp;</td>
										<td width="50">&ensp;</td>
										<tr>
											<td colspan="5" height="18"><strong><?php echo 'Este valor no incluye insumos de bajo costo' ?></strong></td>
										</tr>
										<tr>
											<td colspan="5" height="18"><strong><?php echo 'No incluye otros dispositivos de alto costo que el paciente requiera 
																			   intra-procedimiento, los cuales seran cobrados aparte de este valor.' ?></strong>											
											</td>
										</tr>
										<tr>
											<td style="background-color: #C3D9FF" width="50" align="center"><strong> TOTAL </strong>									
											<td>&ensp;</td>
											<td>&ensp;</td>
											<td>&ensp;</td>
											<td>&ensp;</td>
											<?php 
												$total_cotizaci = $total_suma + $total_sumaC; 

											?>								
											<td width="50" align="center"><input name="total_cantidad" type="text" id="total_cantidad" size="10" readonly value="<?php echo number_format($total_cotizaci,0,'',',') ?>"/><strong></strong>
											</td>
										</tr>
										<tr><td colspan="7" height="18"><div align="center"><strong> Servicios que no incluye: </strong></div></td> </tr>
										  <tr>						
										  <td colspan="7" width="50" height="18"><div align="left">GASTOS HOSPITALARIOS SEG&Uacute;N REQUERIMIENTOS DEL PACIENTE, Complicaciones, 
																									  Uso de sangre y/o hemoderivados, Ex&aacute;menes prequir&uacute;rgicos, Interconsultas con otros especialistas, 
																									  Servicios adicionales y/o insumos no especificados en este presupuesto.</div></td> 
										  </tr>
										  <tr><td colspan="7" height="18"><div align="center"><strong> OBSERVACIONES GENERALES </strong></div></td> </tr>
										  <tr>						
											<td colspan="7" width="50" height="18"><div align="left">Primera: Los excedentes por estancia y servicios no relacionados en este presupuesto, 
																									  en caso de requerirse, se  facturaran a Tarifa Institucional, hasta el alta del paciente. 
																									  Estas tarifas tienen conceptos propios y no corresponden a ning&uacute;n manual tarifario.</div></td>
										   </tr>
											<tr>	
											<td colspan="7" width="50" height="18"><div align="left">Segunda: Es importante que el responsable del pago, tenga claridad sobre este documento en lo 
																									  relacionado con la informaci&oacute;n que aqu&iacute; se suministra. 
																									  Esto es, el presupuesto es un documento gu&iacute;a sobre el cual </div></td>
											</tr>
											<tr>
											<td colspan="7" width="50" height="18"><div align="left">Tercera: Los valores adicionales a este presupuesto, que se generen en la prestaci&oacute;n del servicio, 
																									  deber&aacute;n ser asumidos integralmente por la Entidad responsable de la aceptaci&oacute;n de &eacute;sta. </div></td>
											</tr>
											<tr>
											<td colspan="7" width="50" height="18"><div align="left">Cuarta: En caso de ser aceptado el  presente presupuesto, se deber&aacute; enviar orden de servicio 
																										a Nombre de Promotora M&eacute;dica las Am&eacute;ricas, especificando que se aceptan los t&eacute;rminos 
																										del presupuesto y  adjuntando copia de &eacute;stedebidamente firmado. 
																										Ambos documentos  deben ser firmadas por un funcionario con facultades para comprometer y obligar a la Entidad. </div></td>														  
										  
											</tr>
											<tr>
											<td colspan="7" width="50" height="18"><div align="left">Quinta: Este presupuesto tiene validez de un (1) mes despu&eacute;s de haber sido entregado a la Entidad Pagadora, 
																										para ser emitida la orden de servicio y notificada a la Cl&iacute;nica y tres meses para hacerse efectivo el servicio, 
																										a partir de &eacute;sta fecha, se deber&aacute; realizar nuevo presupuesto, 
																										que estar&iacute;a sujeto a renegociaci&oacute;n y aceptaci&oacute;n entre las partes. </div></td>														  
										  
											</tr>
											<tr>
											<td colspan="7" width="50" height="18"><div align="left">Sexta: En caso de no tener contrato con Cl&iacute;nica Las Am&eacute;ricas, se deber&aacute; consignar con anticipaci&oacute;n el valor total de este presupuesto, 
																									 en la cuenta de Ahorros BANCOLOMBIA N° 1023-2521708 a nombre de Promotora Medica Las Americas y enviar el documento de consignaci&oacute;n 
																									 al Fax 342-09-36 &oacute; escaneado al correo electr&oacute;nico portafolio@correo1lasamericas.com, indicando los datos del paciente que fue autorizado.. </div></td>														  
										  
											</tr>
											<tr>
											<td colspan="7" width="50" height="18"><div align="left"><?php echo 'Realizada por:   HEMODINAMIA Y EEF telefono: 3458572' ?> </div></td>														  
										  
											</tr>
											<tr>
											<td colspan="7" width="50" height="18"><div align="left"><?php echo $Fecha.'-'.'Revision de valores con UNIX a tarifa'.' '.$EmpcodR.'-'.$EmpnomR.'('.$EmptarR.')' ?> </div></td>														  
										  
											</tr>
											<tr>
												<td height="35" colspan="7">
													<div align="center">
													<p>
														<input name="calcular_plantilla" type="hidden" value='4' />
														<input name="accion" type="hidden" value='guardarCotizacion'/>
														<input name="guardar" type="submit" class="btn-primary" value="GUARDAR" />
														<a href="Menuplantilla.php" onclick="opPaneles('xProceExa')">RETORNAR</a></label>											
													</p>
													</div>
												</td>
											</tr>									
										<?php
							 		}
									
									
								}
								?>
								<input name="total_filas" id="total_filas" type="hidden" value="<?php echo $IdRowIn ?>" />
								
						
						
					</form>
				</table>
			</div>				
				<!-- FIN DEL CONTENIDO DEL PROCESO DE COTIZACION -->
				<!-- SE REALIZA LA CONSULTA DE TODAS LAS COTIAZCIONES REALIZADAS -->	
            </div>
			<?php
			if($activa_coti == 6)
			{
				?>
				<div id="xCotizacionRealizada" class="divxCotizacionRealizada" align="center" style="display: block">
				<?php
			}
			else
			{
				?>
				<div id="xCotizacionRealizada" class="divxCotizacionRealizada" align="center" style="display: none">
				<?php
			}
			?>
			
			
			<!-- <div id="xCotizacionRealizada" class="divxCotizacionRealizada" align="center" style="display: none"> -->
				<!-- INICIO DE FORMULARIO PARA CONSULTAR LAS COTIZACIONES -->
				
				<table border='1'>
					<form action="Menuplantilla.php" method="post">
					  <table width="1000" border="1" align="center">
							<tr>
								<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong> CONSULTAR COTIZACIONES </strong></p> </td>
							</tr>
					   </table>		
						<p>&nbsp;</p>
						<div align="center">
						<table width="600" border="0">
						  <tr>
								<td bgcolor="#C3D9FF"><p align="center"><strong>Codigo Plantilla:</strong></p>
								</td>
								<td>
								<p>&nbsp;</p>
								</td>
								<td bgcolor="#C3D9FF"><p align="center"><strong>Identificacion:</strong></p>
								</td>
								<td>
								<p>&nbsp;</p>
								</td>
								<td bgcolor="#C3D9FF"><p align="center"><strong>Nit Responsable:</strong></p>
								</td>
								<td>
								<p>&nbsp;</p>
								</td>
								<td bgcolor="#C3D9FF"><p align="center"><strong>Fecha:</strong></p>
								</td>
						  </tr>
						  <tr>
								<td>							
									<strong><input name="bus_plantilla" type="text" id="bus_plantilla" size="20" value="<?php echo $Bus_plantilla ?>"/></strong>							 
								</td>
								<td>
								<p>&nbsp;</p>
								</td>
								<td>							
									<strong><input name="bus_identificacion" type="text" id="bus_identificacion" size="20" value="<?php echo $Bus_identificacion ?>"/></strong>
								</td>
								<td>
								<p>&nbsp;</p>
								</td>
								<td>							
									<strong><input name="bus_responsable" type="text" id="bus_responsable" size="22" <?php echo $Bus_responsable ?>/></strong>
								</td>
								<td>
								<p>&nbsp;</p>
								</td>
								<td>							
									<strong><input name="bus_fecha" type="date" id="bus_fecha" size="0" <?php echo $Bus_fecha ?>/></strong>
								</td>
							</tr>
							<tr>
								<td height="35" colspan="7"><div align="center">
									<input type="hidden" value="6" name="activa_cotizacion"/>
									<input align="center" name="cons_cotizacion" type="submit" class="btn-primary" value="CONSULTAR" />
									<input align="center" name="resetear" type="reset" class="btn-primary" value="LIMPIAR" />
									</div>
								</td>
							</tr>
						</table>
					  </div>
					</form>
				</table>	
					<?php
							if($_POST['cons_cotizacion'])
								{
									$Bus_plantilla = $_POST['bus_plantilla'];
									$Bus_identificacion = $_POST['bus_identificacion'];
									$Bus_responsable = $_POST['bus_responsable'];
									$Bus_fecha = $_POST['bus_fecha'];
									// Si est&aacute; vac&iacute;o, lo informamos, sino realizamos la búsqueda
									if(empty($Bus_plantilla) or empty($Bus_identificacion) or empty($Bus_responsable))
									{
										$select_cotizacion = mysql_queryV("SELECT DISTINCT Tidr,Identificacion,Nompac,Fecha,CodplaR,EmpcodR,Total_cantidad 
																		from cliame_000337 
																		where CodplaR='$Bus_plantilla' or Identificacion='$Bus_identificacion' or EmpcodR='$Bus_responsable' or Fecha='$Bus_fecha'");
										?>
										<table width="1000" height="44" border="1">
										  <tr>
										  	<td style="background-color: #C3D9FF" height="18"><div align="center"><strong>CODIGO PLANTILLA</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>TIPO IDENTIFICACION</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>IDENTIFICACION</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>NOMBRE DEL PACIENTE</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>FECHA</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>RESPONSABLE</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>VALOR TOTAL</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>VER DETALLE</strong></div></td>
										  </tr>	
										<?php
											while($resultadoCoti=mysql_fetch_array($select_cotizacion))
    										{
												$Btidr = $resultadoCoti[0];   
												$Bidentificacion = $resultadoCoti[1];
												$Bnompac = $resultadoCoti[2];
												$Bfecha = $resultadoCoti[3];
												$BcodplaR = $resultadoCoti[4];
												$BempcodR = $resultadoCoti[5];
												$Btotal_cantidad = $resultadoCoti[6];
										?>
											<tr>
												<td width="50" height="18"><?php echo $BcodplaR ?> <div align="center"></div></td>
												<td width="20"><?php echo $Btidr ?></td>
												<td width="50"><?php echo $Bidentificacion ?></td>
												<td width="50"><?php echo $Bnompac ?></td>
												<td width="50"><?php echo $Bfecha ?></td>
												<td width="50"><?php echo $BempcodR ?></td>
												<td width="50"><?php echo $Btotal_cantidad ?></td>
												<td width="50" align="center">
													<a href="consulta_cotizacion.php?bcodplaR=<?php echo $BcodplaR ?>&btidr=<?php echo $Btidr ?>&bidentificacion=<?php echo $Bidentificacion ?>&bfecha=<?php echo $Bfecha ?>&bempcodR=<?php echo $BempcodR ?>"><span class="glyphicon glyphicon-search"></span></a>
												</td>
											</tr>
										
										<?php
											}	
							?>
										</table>
									<?php								
									}else
									{
								// Conexión a la base de datos y seleccion de registros
									$select_cotizacion = mysql_queryV("SELECT DISTINCT Tidr,Identificacion,Nompac,Fecha,CodplaR,EmpcodR,Total_cantidad 
																		from cliame_000337
																		where CodplaR='$Bus_plantilla' and Identificacion='$Bus_identificacion' and EmpcodR='$Bus_responsable'");
									?>
										<table width="1000" height="44" border="1">
										  <tr>
										  	<td style="background-color: #C3D9FF" height="18"><div align="center"><strong>CODIGO PLANTILLA</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>TIPO IDENTIFICACION</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>IDENTIFICACION</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>NOMBRE DEL PACIENTE</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>FECHA</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>RESPONSABLE</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>VALOR TOTAL</strong></div></td>
											<td style="background-color: #C3D9FF"><div align="center"><strong>VER DETALLE</strong></div></td>
										  </tr>	
											<?php
									
									
										while($resultadoCoti=mysql_fetch_array($select_cotizacion))
    										{
												$Btidr = $resultadoCoti[0];   
												$Bidentificacion = $resultadoCoti[1];
												$Bnompac = $resultadoCoti[2];
												$Bfecha = $resultadoCoti[3];
												$BcodplaR = $resultadoCoti[4];
												$BempcodR = $resultadoCoti[5];
												$Btotal_cantidad = $resultadoCoti[6];
										?>
											<tr>
												<td width="50" height="18"><?php echo $BcodplaR ?> <div align="center"></div></td>
												<td width="20"><?php echo $Btidr ?></td>
												<td width="50"><?php echo $Bidentificacion ?></td>
												<td width="50"><?php echo $Bnompac ?></td>
												<td width="50"><?php echo $Bfecha ?></td>
												<td width="50"><?php echo $BempcodR ?></td>
												<td width="50"><?php echo $Btotal_cantidad ?></td>
												<td width="50" align="center">
													<a href="consulta_cotizacion.php?bcodplaR=<?php echo $BcodplaR ?>&btidr=<?php echo $Btidr ?>&bidentificacion=<?php echo $Bidentificacion ?>&bfecha=<?php echo $Bfecha ?>&bempcodR=<?php echo $BempcodR ?>"><span class="glyphicon glyphicon-search" target="_blank"></span></a>
												</td>
											</tr>
										
										<?php
											}
										?>
										</table>
									<?php	
									}
							}	
					?>
			</div>
        </div>
    </div>
</div>
</body>
</html>