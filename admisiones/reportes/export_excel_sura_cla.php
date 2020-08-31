<!--El programa realiza la consulta de un query respectivo y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_sura_cla. -->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_sura_cla.                                      |
//AUTOR				          : Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : 2019-07-08.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-07-08.                                                                                             |
//DESCRIPCION			      : El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_sura_cla.
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: cliame_000101 a, cliame_000024 b, cliame_000100 c 
//
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
		$buscar=$_GET['fecha'];
		$buscar1=$_GET['fecha1'];
		
?>
</head>
<body width="616" height="47">
<p>
  <?php
// Conexión a la tabla y seleccion de registros MATRIX
			$select_grd = mysql_query("SELECT a.Inghis,a.Ingnin,a.Ingfei,a.Ingsei,a.Ingcem,b.Empnom,c.Pactdo,c.Pacdoc,c.Pacap1,Pacap2,Pacno1,Pacno2,Pacfna 
     									FROM  cliame_000101 a 
										left join
										cliame_000024 b on (a.Ingcem = b.Empcod), cliame_000100 c 
    									WHERE Ingfei BETWEEN '$buscar' AND '$buscar1' 
										AND Ingsei in ('1800','1016','1130') 
										AND Ingcem IN ('800088702-2','800088702CO','800088702SB' )
               							AND Inghis = Pachis 
                                        order by a.Ingsei ");

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
		?>
		<table width="1500" height="44" border="1">
			<tr class="titulo">
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">HISTORIA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">INGRESO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">FECHA DE INGRESO </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">SERVICIO INGRESO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CODIGO ENTIDAD</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">NOMBRE</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">TIPO DOC</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">DOCUMENTO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">APELLIDO1</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">APELLIDO2</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">NOMBRE1</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">NOMBRE2</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FECHA NACIMIENTO</div></td>
			  </tr>
			   <?php
				while($resultado=mysql_fetch_array($select_grd))
				{
					$Inghis = $resultado[0];
					$Ingnin = $resultado[1];
					$Ingfei = $resultado[2];
					$Ingsei = $resultado[3];
					$Ingcem = $resultado[4];
					$Empnom = $resultado[5];
					$Pactdo = $resultado[6];
					$Pacdoc = $resultado[7];
					$Pacap1 = $resultado[8];
					$Pacap2 = $resultado[9];
					$Pacno1 = $resultado[10];
					$Pacno2 = $resultado[11];
					$Pacfna = $resultado[12];
				?>
						<tr>
							<td width="244"><?php echo $Inghis ?></td>
							<td width="152"><?php echo $Ingnin ?></td>
							<td width="198"><?php echo $Ingfei ?></td>
							<td width="198"><?php echo $Ingsei ?></td>
							<td width="198"><?php echo $Ingcem ?></td>
							<td width="198"><?php echo $Empnom ?></td>
							<td width="198"><?php echo $Pactdo ?></td>
							<td width="152"><?php echo $Pacdoc ?></td>
							<td width="198"><?php echo $Pacap1 ?></td>
							<td width="198"><?php echo $Pacap2 ?></td>
							<td width="198"><?php echo $Pacno1 ?></td>
							<td width="198"><?php echo $Pacno2 ?></td>
							<td width="198"><?php echo $Pacfna ?></td>
						</tr>	
				<?php
				}
				?>
						
		</table>
	<?php		
?>
</body>
</html>