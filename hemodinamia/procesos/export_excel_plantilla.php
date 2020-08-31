<!--El programa realiza la consulta y gestion respectivo a crear cotizaciones con los valores de las respectivas tablas de tarifas de unix para la unidad de hemodinamia-->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a crear cotizaciones con los valores de las respectivas tablas de tarifas de unix para la unidad de hemodinamia                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-09-20.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-09-20.                                                                                             |
//                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: para insertar y gestionar cliame_000329, cliame_000330, cliame_000337
//
//  
// 
//                                                                                                                                     |
//==========================================================================================================================================	
-->
<?php
// LIBRERIAS PARA DESCARGAR DE FORMA AUTOMATICA LA INFORMACION
		header('Content-type: application/vnd.ms-excel; charset=UTF-8');
		header("Content-disposition: attachment; filename=pacientes.xls");
		header('Pragma: no-cache');
		header('Expires: 0');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<?php
		include_once("root/comun.php");
        $conex = obtenerConexionBD("matrix");
		// RECIBIR PARAMETROS DE MENUPLANTILLA PARA REALIZAR EL QUERY EL CLIAME_000337
		//$BcodplaR=$_GET['bcodplaR'];
		//$Btidr=$_GET['btidr'];
		//$Bidentificacion=$_GET['bidentificacion'];
		//$Bfecha=$_GET['bfecha'];
		//$BempcodR=$_GET['bempcodR'];
		
?>
</head>
<body>
	<?php	
		// RECIBIR PARAMETROS DE MENUPLANTILLA PARA REALIZAR EL QUERY EL CLIAME_000337
		$BcodplaR=$_GET['bcodplaR'];
		$Btidr=$_GET['btidr'];
		$Bidentificacion=$_GET['bidentificacion'];
		$Bfecha=$_GET['bfecha'];
		$BempcodR=$_GET['bempcodR'];
		//Obtener descripciones
		$select_nomPlan = mysql_query("SELECT * from cliame_000329 where Codpla='$BcodplaR'");
		$resultado_nomPlan=mysql_fetch_array($select_nomPlan);
		//$Placod = $resultado_nomPlan[3];
		$Nompla = $resultado_nomPlan[4];
		// query para obtener la tarifa
		$select_tarifa = mysql_query("SELECT Empcod,Empnom,Emptar from cliame_000024 where Empcod='$BempcodR'");
		$resultado_tarifa=mysql_fetch_array($select_tarifa);
		//$EmpcodR = $resultado_tarifa[0];
		$EmpnomR = $resultado_tarifa[1];
		$EmptarR = $resultado_tarifa[2];
		
		//Obtener tarifas y descripciones
		
		//Obtener los datos de las cotizaciones
		$select_cliame_337 = mysql_query ("select * from cliame_000337 where CodplaR = '$BcodplaR' and TidR = '$Btidr' and Identificacion='$Bidentificacion' and Fecha='$Bfecha' and EmpcodR='$BempcodR'");
		$select_cliame_337_D = mysql_query ("select * from cliame_000337 where CodplaR = '$BcodplaR' and TidR = '$Btidr' and Identificacion='$Bidentificacion' and Fecha='$Bfecha' and EmpcodR='$BempcodR'");
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
            <td width="207"><input type="image" id="btnVer" src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
		</tr>
		<tr>
			<td colspan="6" width="1000%" bgcolor="#C3D9FF"> <p align="center"><strong>PROMOTORA MEDICA LAS AMERICAS NIT 800067065-9</strong></p> </td>
		</tr>
		<tr>
			<td colspan="6" width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>PRESUPUESTO DE SERVICIOS</strong></p> </td>
		</tr>
	</table>
		<td width="6">&ensp;</td>
	<table width="1000" border="1" align="center">					   
		<tr>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Fecha:</strong></p></td>
			<td colspan="3">
			<strong><?php echo $Fecha ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Nombre del Paciente:</strong></p>
			</td>
			<td colspan="3"><strong><?php echo $Nompac ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Tipo de Identificacion:</strong></p>
			</td>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Identificacion:</strong></p>
			</td>													
		</tr>
		<tr>
			<td colspan="3"><strong><?php echo $TidR ?></strong></td>
			<td colspan="3"><strong><?php echo $Identificacion ?></strong></td>
		</tr>
		<tr>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Historia:</strong></p></td>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Ingreso:</strong></p></td>				
		</tr>
		<tr>	
			<td colspan="3"><strong><?php echo $Historia ?></strong></td>
			<td colspan="3"><strong><?php echo $Ingreso ?></strong></td>
		</tr>
		<tr>	
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Medico:</strong></p>
			</td>
			<td colspan="3" ><strong><?php echo $Medico ?></strong>
			</td>
		</tr>
		<tr>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Nombre y CUPS del procedimiento:</strong></p>
			</td>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Entidad:</strong></p>
			</td>
		</tr>
		<tr>						
			<td colspan="3">							
				<strong><?php echo $CodplaR.'-'.$Nompla ?></strong>
		 
			</td>
			<td colspan="3">
				<strong><?php echo $EmpcodR .'-'.$EmpnomR.'-'.$EmptarR ?> </strong>												
			</td>
		</tr>
		<tr>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Tipo de procedimiento:</strong></p></td>
			<td colspan="3">														
				<strong><?php echo $Tprocedimiento ?></strong>
			</td>
		</tr> 
		<tr>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>Requiere MIPRES:</strong></p></td>
			<td colspan="3">
				<strong><?php echo $Mipres ?></strong>									
			</td>
		</tr>
		<tr>
			<td colspan="3" bgcolor="#C3D9FF"><p align="left"><strong>NUMERO DE MIPRES:</strong></p></td>
			<td colspan="3">
				<strong><?php echo $NumMipres ?></strong>									
			</td>
		</tr>
	  </div>
		
	</table>									<!-- PINTAR LOS VALORES -->
	<table width="1000" height="44" border="1" align="center">
	  
	  <tr><td colspan="6" style="background-color: #C3D9FF" height="18"><div align="center"><strong>CODIGO PLANTILLA </strong></div></td> </tr>
	  <tr>						
	  <td colspan="6" width="50" height="18"><div align="center"><strong><?php echo $CodplaR.'-'.$Nompla ?></strong></div></td> 
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
		<tr><td colspan="6" height="18"><div align="center"><strong> Servicios que no incluye: </strong></div></td> </tr>
		<tr>						
		  <td colspan="6" width="50" height="18"><div align="left">GASTOS HOSPITALARIOS SEG&Uacute;N REQUERIMIENTOS DEL PACIENTE, Complicaciones, 
																	  Uso de sangre y/o hemoderivados, Ex&aacute;menes prequir&uacute;rgicos, Interconsultas con otros especialistas, 
																	  Servicios adicionales y/o insumos no especificados en este presupuesto.</div></td> 
		</tr>
		  <tr><td colspan="6" height="18"><div align="center"><strong> OBSERVACIONES GENERALES </strong></div></td> </tr>
		  <tr>						
			<td colspan="6" width="50" height="18"><div align="left">Primera: Los excedentes por estancia y servicios no relacionados en este presupuesto, 
																	  en caso de requerirse, se  facturaran a Tarifa Institucional, hasta el alta del paciente. 
																	  Estas tarifas tienen conceptos propios y no corresponden a ning&uacute;n manual tarifario.</div></td>
		   </tr>
			<tr>	
			<td colspan="6" width="50" height="18"><div align="left">Segunda: Es importante que el responsable del pago, tenga claridad sobre este documento en lo 
																	  relacionado con la informaci&oacute;n que aqu&iacute; se suministra. 
																	  Esto es, el presupuesto es un documento gu&iacute;a sobre el cual </div></td>
			</tr>
			<tr>
			<td colspan="6" width="50" height="18"><div align="left">Tercera: Los valores adicionales a este presupuesto, que se generen en la prestaci&oacute;n del servicio, 
																	  deber&aacute;n ser asumidos integralmente por la Entidad responsable de la aceptaci&oacute;n de &eacute;sta. </div></td>
			</tr>
			<tr>
			<td colspan="6" width="50" height="18"><div align="left">Cuarta: En caso de ser aceptado el  presente presupuesto, se deber&aacute; enviar orden de servicio 
																		a Nombre de Promotora M&eacute;dica las Am&eacute;ricas, especificando que se aceptan los t&eacute;rminos 
																		del presupuesto y  adjuntando copia de &eacute;stedebidamente firmado. 
																		Ambos documentos  deben ser firmadas por un funcionario con facultades para comprometer y obligar a la Entidad. </div></td>														  
		  
			</tr>
			<tr>
			<td colspan="6" width="50" height="18"><div align="left">Quinta: Este presupuesto tiene validez de un (1) mes despu&eacute;s de haber sido entregado a la Entidad Pagadora, 
																		para ser emitida la orden de servicio y notificada a la Cl&iacute;nica y tres meses para hacerse efectivo el servicio, 
																		a partir de &eacute;sta fecha, se deber&aacute; realizar nuevo presupuesto, 
																		que estar&iacute;a sujeto a renegociaci&oacute;n y aceptaci&oacute;n entre las partes. </div></td>														  
		  
			</tr>
			<tr>
			<td colspan="6" width="50" height="18"><div align="left">Sexta: En caso de no tener contrato con Cl&iacute;nica Las Am&eacute;ricas, se deber&aacute; consignar con anticipaci&oacute;n el valor total de este presupuesto, 
																	 en la cuenta de Ahorros BANCOLOMBIA N° 1023-2521708 a nombre de Promotora Medica Las Americas y enviar el documento de consignaci&oacute;n 
																	 al Fax 342-09-36 &oacute; escaneado al correo electr&oacute;nico portafolio@correo1lasamericas.com, indicando los datos del paciente que fue autorizado.. </div></td>														  
		  
			</tr>
			<tr>
			<td colspan="6" width="50" height="18"><div align="left"><?php echo 'Realizada por:   HEMODINAMIA Y EEF telefono: 3458572' ?> </div></td>														  
		  
			</tr>
			<tr>
			<td colspan="6" width="50" height="18"><div align="left"><strong><?php echo $Fecha.'-'.'Revision de valores con UNIX a tarifa'.' '.$EmpcodR.'-'.$EmpnomR.'('.$EmptarR.')' ?></strong> </div></td>														  
		  
			</tr>
	</table>				
</body>
</html>