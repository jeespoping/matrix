<!--El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_GRD. -->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_GRD.                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-03-19.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-03-19.                                                                                             |
//DESCRIPCION			      : El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_GRD.                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: cliame_000108 a, cliame_000112 b, cliame_000101 d, movhos_000011
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
<title>Consultar Registro</title>

<?php
        //RECIBIR LOS PARAMETROS DE CONSULTA_EGRESOS_GRD.PHP
		include_once("root/comun.php");
        $conex = obtenerConexionBD("matrix");
		$Egrhis=$_GET['historia'];
		$Egring=$_GET['ingreso'];
		$Egrfee=$_GET['fecha_egreso'];
		$Ingfei=$_GET['fecha_ingreso'];
		$Sercod=$_GET['servicio'];
		$buscar=$_GET['fecha'];
		$buscar1=$_GET['fecha1'];
		
?>
</head>
<body width="616" height="47">
<p>
  <?php
// Conexión a la tabla y seleccion de registros MATRIX
			$select_grd = mysql_queryV("SELECT Egrhis, Egring, Egrfee, Ingfei, Sercod, Ingsei, Pactel
     									FROM cliame_000108 a, cliame_000112 b, cliame_000101 d inner join 
										cliame_000100 c100 on (Inghis = Pachis) 
										, movhos_000011
    									WHERE Egrfee BETWEEN '$buscar' AND '$buscar1'
               							AND Egrhis = Serhis
										AND Egring = Sering
										AND Seregr = 'on'
										AND Sercod = ccocod
										AND Inghis = Egrhis
										AND Ingnin = Egring
										AND ccohos = 'on'");

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
		?>
		<table width="1500" height="44" border="1">
			<tr class="titulo">
			<td style="background-color: #C3D9FF"><div align="center" class="Estilo7">HISTORIA </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7"> INGRESO </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">FECHA DE EGRESO </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FECHA DE INGRESO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">SERVICIO EGRESO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">SERVICIO INGRESO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">TELEFONO</div></td>
			  </tr>
			   <?php
				while($resultado=mysql_fetch_array($select_grd))
				{
					$Egrhis = $resultado[0];
					$Egring = $resultado[1];
					$Egrfee = $resultado[2];
					$Ingfei = $resultado[3];
					$Sercod = $resultado[4];
					$Ingsei = $resultado[5];
					$Pactel = $resultado[6];
				?>
					
						<tr>
							<td width="244"><?php echo $Egrhis ?></td>
							<td width="152"><?php echo $Egring ?></td>
							<td width="198"><?php echo $Egrfee ?></td>
							<td width="198"><?php echo $Ingfei ?></td>
							<td width="198"><?php echo $Sercod ?></td>
							<td width="198"><?php echo $Ingsei ?></td>
							<td width="198"><?php echo $Pactel ?></td>
						</tr>	
				<?php
				}
				?>
						
		</table>
	<?php		
?>
</body>
</html>