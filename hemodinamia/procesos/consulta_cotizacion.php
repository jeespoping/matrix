<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
    include_once("conex.php");
    include_once("root/comun.php");
	$wemp_pmla=$_REQUEST['wemp_pmla'];
	$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
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
    <script src="//code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="//mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	<script src="//mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>

	<style>
		html body {
			width: 80%;
			margin: 20px auto 0 auto;
			
		}
	</style>
		<script>
		function mensaje(CodPro) {
			var validacion = null;
			ancho = 300;    alto = 120;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
			settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';
			validacion = window.open ("validarCodigo.php?wemp_pmla="+wemp_pmla.value+"&CodPro="+CodPro,"miwin",settings2);
			validacion.focus();
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
</head>	
<body>
	<?php	
		// RECIBIR PARAMETROS DE MENUPLANTILLA PARA REALIZAR EL QUERY EL CLIAME_000337
		global $wcliame;
		$BcodplaR=$_GET['bcodplaR'];
		$Btidr=$_GET['btidr'];
		$Bidentificacion=$_GET['bidentificacion'];
		$Bfecha=$_GET['bfecha'];
		$BempcodR=$_GET['bempcodR'];
		//Obtener descripciones
		$select_nomPlan = mysql_queryV("SELECT * from ".$wcliame."_000329 where Codpla='$BcodplaR'");
		$resultado_nomPlan=mysql_fetch_array($select_nomPlan);
		//$Placod = $resultado_nomPlan[3];
		$Nompla = $resultado_nomPlan[4];
		// query para obtener la tarifa
		$select_tarifa = mysql_queryV("SELECT Empcod,Empnom,Emptar from ".$wcliame."_000024 where Empcod='$BempcodR'");
		$resultado_tarifa=mysql_fetch_array($select_tarifa);
		//$EmpcodR = $resultado_tarifa[0];
		$EmpnomR = $resultado_tarifa[1];
		$EmptarR = $resultado_tarifa[2];
		
		//Obtener tarifas y descripciones
		
		//Obtener los datos de las cotizaciones
		$select_cliame_337 = mysql_query ("select * from ".$wcliame."_000337 where CodplaR = '$BcodplaR' and TidR = '$Btidr' and Identificacion='$Bidentificacion' and Fecha='$Bfecha' and EmpcodR='$BempcodR'");
		$select_cliame_337_D = mysql_query ("select * from ".$wcliame."_000337 where CodplaR = '$BcodplaR' and TidR = '$Btidr' and Identificacion='$Bidentificacion' and Fecha='$Bfecha' and EmpcodR='$BempcodR'");
		//echo $select_cliame_337;
		$resultado=mysql_fetch_array($select_cliame_337);
		$Identificacion = $resultado[3];
		$TidR = $resultado[4];
		$Nompac = $resultado[5];
		$Historia = $resultado[6];
		$Ingreso = $resultado[7];
		$Fecha = $resultado[8];
		$Medico = $resultado[9];
		$CodplaR = $resultado[10];
		$EmpcodR = $resultado[11];
		$Tprocedimiento = $resultado[12];
		$Mipres = $resultado[13];
		$NumMipres = $resultado[14];
		//$Descritab = $resultado[15];
		//$Codtab = $resultado[16];
		//$Canntab = $resultado[17];
		//$Concetab = $resultado[18];
		//$Unitab = $resultado[19];
		//$Tottab = $resultado[20];
		$Total_cantidad = $resultado[21];																
		?>
	<table width="1000" border="0" align="center">
		<tr>
            <td width="207"><input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
		</tr>
		<tr>
			<td width="1000%" bgcolor="#C3D9FF"> <p align="center"><strong>PROMOTORA MEDICA LAS AMERICAS NIT 800067065-9</strong></p> </td>
		</tr>
		<tr>
			<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>PRESUPUESTO DE SERVICIOS</strong></p> </td>
		</tr>
	</table>
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
			<td bgcolor="#C3D9FF"><p align="left"><strong>Tipo de Identificacion:</strong></p>
			</td>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Identificacion:</strong></p>
			</td>													
		</tr>
		<tr>
			<td><strong><?php echo $TidR ?></strong></td>
			<td><strong><?php echo $Identificacion ?></strong></td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Historia:</strong></p></td>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Ingreso:</strong></p></td>				
		</tr>
		<tr>	
			<td><strong><?php echo $Historia ?></strong></td>
			<td><strong><?php echo $Ingreso ?></strong></td>
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
				<strong><?php echo $CodplaR.'-'.$Nompla ?></strong>
		 
			</td>
			<td>
				<strong><?php echo $EmpcodR .'-'.$EmpnomR.'-'.$EmptarR ?> </strong>												
			</td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Tipo de procedimiento:</strong></p></td>
			<td>														
				<strong><?php echo $Tprocedimiento ?></strong>
			</td>
		</tr> 
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Requiere MIPRES:</strong></p></td>
			<td>
				<strong><?php echo $Mipres ?></strong>									
			</td>
		</tr>
		<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>NUMERO DE MIPRES:</strong></p></td>
			<td>
				<strong><?php echo $NumMipres ?></strong>								
			</td>
		</tr>
	  </div>
		
	</table>									<!-- PINTAR LOS VALORES -->
	<table width="1000" height="44" border="1" align="center">
	  
	  <tr><td colspan="7" style="background-color: #C3D9FF" height="18"><div align="center"><strong>CODIGO PLANTILLA </strong></div></td> </tr>
	  <tr>						
	  <td colspan="7" width="50" height="18"><div align="center"><strong><?php echo $CodplaR.'-'.$Nompla ?></strong></div></td> 
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
		while($resultado_valores=mysql_fetch_array($select_cliame_337_D))
		{  
			$Descritab = $resultado_valores[15];
			$Codtab = $resultado_valores[16];
			$Canntab = $resultado_valores[17];
			$Concetab = $resultado_valores[18];
			$Unitab = $resultado_valores[19];
			$Tottab = $resultado_valores[20];
		?>
		<tr>
			<td width="50" height="18"><?php echo $Descritab ?> <div align="center"></div></td>
			<td width="20"><?php echo $Codtab ?></td>
			<td width="50"><?php echo $Canntab ?></td>
			<td width="50"><?php echo $Concetab ?></td>
			<td width="50"><?php echo number_format($Unitab,0,'',',') ?></td>
			<td width="50"><?php echo number_format($Tottab,0,'',',') ?></td>					
		</tr>
	
	<?php
		}
	?>
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
			<td width="50" align="left"><strong><?php echo number_format($Total_cantidad,0,'',',') ?></strong>
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
			<td colspan="7" width="50" height="18"><div align="left"><strong><?php echo $Fecha.'-'.'Revision de valores con UNIX a tarifa'.' '.$EmpcodR.'-'.$EmpnomR.'('.$EmptarR.')' ?></strong> </div></td>														  
		  
			</tr>
			<tr>
				<td height="35" colspan="7">
					<div align="center">
						<a href="export_excel_plantilla.php?wemp_pmla=<?=$wemp_pmla?>&bcodplaR=<?php echo $BcodplaR ?>&btidr=<?php echo $Btidr ?>&bidentificacion=<?php echo $Bidentificacion ?>&bfecha=<?php echo $Bfecha ?>&bempcodR=<?php echo $BempcodR ?>"><span class="glyphicon glyphicon-cloud-download"></span>EXPORTAR</a>
					</div>
					<div align="center">
						<a href="Menuplantilla.php?wemp_pmla=<?=$wemp_pmla?>" onclick="opPaneles('xProceExa')">VOLVER</a></label>
					</div>
				</td>
			</tr>
	</table>				
</body>
</html>					