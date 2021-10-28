<!--El programa realiza la consulta de un query respectivo a notas, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_CLISUR. -->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a notas, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_CLISUR.                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-05-30.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-05-30.                                                                                            |
//DESCRIPCION			      : El programa realiza la consulta de un query respectivo a notas, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_CLISUR.                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: clisur_000020,clisur_000021,clisur_000018,clisur_000024,clisur_000065
//
//  
// 
//                                                                                                                                     |
//==========================================================================================================================================	
-->
<?php
// LIBRERIAS PARA DESCARGAR DE FORMA AUTOMATICA LA INFORMACION
		header('Content-type: application/vnd.ms-excel; charset=UTF-8');
		header("Content-disposition: attachment; filename=notas.xls");
		header('Pragma: no-cache');
		header('Expires: 0');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
        //RECIBIR LOS PARAMETROS DE REPORT_EGRESOS_GRD
		include("root/comun.php");
        mysql_select_db("matrix");
        $conex = obtenerConexionBD("matrix");
		$Rnotas=$_GET['notas'];
		$Rfacturas=$_GET['facturas'];
		$buscar=$_GET['fecha'];
		$buscar1=$_GET['fecha1'];
		
?>
</head>
<body width="616" height="47">
<p>
  <?php
// Conexión a la tabla y seleccion de registros MATRIX
			$select_notas = mysql_queryV("SELECT fenfec,Empnom,fenfac,fenval,fensal,fencod,rdevca,
      								 	rdecon,renfue,rennum,renvca,renfec,renobs
       									FROM  clisur_000020,clisur_000021,clisur_000018,clisur_000024
       									WHERE renfue  in ('27','28') 
         								AND renfec  BETWEEN '$buscar' AND '$buscar1'     
										AND renest = 'on'
										AND renfue  = rdefue
										AND rennum  = rdenum
										AND rdefac  = fenfac
										AND Fencod = Empcod");

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
		?>
		<table width="1500" height="44" border="1">
			<tr class="titulo">
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">FECHA FACTURA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">EMPRESA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">FACTURA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">VALOR</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">SALDO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">COD EMPRESA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">VALOR CONCEPTO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CONCEPTO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FUENTE</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">NUMERO DE NOTA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">VALOR</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FECHA NOTA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">OBSERVACIONES</div></td>
			  </tr>
			   <?php
				while($resultado=mysql_fetch_array($select_notas))
				{
					$fenfec = $resultado[0];
					$Empnom = $resultado[1];
					$fenfac = $resultado[2];
					$fenval = $resultado[3];
					$fensal = $resultado[4];
					$fencod = $resultado[5];
					$rdevca = $resultado[6];
					$rdecon = $resultado[7];
					$renfue = $resultado[8];
					$rennum = $resultado[9];
					$renvca = $resultado[10];
					$renfec = $resultado[11];
					$renobs = $resultado[12];
				?>
					
						<tr>
							<td width="244"><?php echo $fenfec ?></td>
							<td width="152"><?php echo $Empnom ?></td>
							<td width="198"><?php echo $fenfac ?></td>
							<td width="198"><?php echo $fenval ?></td>
							<td width="198"><?php echo $fensal ?></td>
							<td width="244"><?php echo $fencod ?></td>
							<td width="152"><?php echo $rdevca ?></td>
							<td width="198"><?php echo $rdecon ?></td>
							<td width="198"><?php echo $renfue ?></td>
							<td width="198"><?php echo $rennum ?></td>
							<td width="198"><?php echo $renvca ?></td>
							<td width="198"><?php echo $renfec ?></td>
							<td width="198"><?php echo $renobs ?></td>
						</tr>	
				<?php
				}
				?>
						
		</table>
	<?php		
?>
</body>
</html>