<!--El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_CLISUR. -->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_CLISUR.                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-05-30.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-05-30.                                                                                             |
//DESCRIPCION			      : El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_CLISUR.                                      |.        |
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
		header("Content-disposition: attachment; filename=facturas.xls");
		header('Pragma: no-cache');
		header('Expires: 0');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
        //RECIBIR LOS PARAMETROS DE EXPORT_EXCEL_CLISUR.PHP
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
			$select_facturas = mysql_query("SELECT fenfec,fenfac,fenval,Fennit,Empnom,Fdecco,Fdecon,Fdevco
       									from  clisur_000018,clisur_000024,clisur_000065 
											where Fenfec BETWEEN '$buscar' AND '$buscar1'        
 											and  Fenest = 'on'  
 											and  Fencod = Empcod 
 											and  Fenfac = Fdedoc");

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
		?>
		<table width="1500" height="44" border="1">
			<tr class="titulo">
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">FECHA FACTURA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">FACTURA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">VALOR</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">NIT</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">DESCRIPCION</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CENTRO DE COSTOS</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CONCEPTO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">VALOR</div></td>
			  </tr>
			   <?php
				while($resultado=mysql_fetch_array($select_facturas))
				{
					$fenfec = $resultado[0];
					$fenfac = $resultado[1];
					$fenval = $resultado[2];
					$Fennit = $resultado[3];
					$Empnom = $resultado[4];
					$Fdecco = $resultado[5];
					$Fdecon = $resultado[6];
					$Fdevco = $resultado[7];
				?>
					
						<tr>
							<td width="244"><?php echo $fenfec ?></td>
							<td width="198"><?php echo $fenfac ?></td>
							<td width="198"><?php echo $fenval ?></td>
							<td width="198"><?php echo $Fennit ?></td>
							<td width="244"><?php echo $Empnom ?></td>
							<td width="152"><?php echo $Fdecco ?></td>
							<td width="198"><?php echo $Fdecon ?></td>
							<td width="198"><?php echo $Fdevco ?></td>
						</tr>	
				<?php
				}
				?>
						
		</table>
	<?php		
?>
</body>
</html>