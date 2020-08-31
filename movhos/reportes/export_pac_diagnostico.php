<!--El programa realiza la consulta de un query respectivo a pacientes con un diagnostico, y manda el enlace respectivo para generar la descarga en EXPORT_PAC_DIAGNOSTICO. -->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a egresos, y manda el enlace respectivo para generar la descarga en EXPORT_PAC_DIAGNOSTICO.                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2020-03-20.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2020-05-14.                                                                                             |
//DESCRIPCION			      : El programa realiza la consulta de un query respectivo a pacientes con un diagnostico, y manda el enlace respectivo para generar la descarga en EXPORT_PAC_DIAGNOSTICO.
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: movhos_000272 m, root_000011 b, cliame_000100 c100,cliame_000101 c101
//
//  
// MODIFICACION X DIDIER OROZCO CARMONA - 2020-05-14: Se quita el like y por ende a esto la condicion de los diagnosticos para que salgan todos. 
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
		$Cant=$_GET['cant'];
		$buscar=$_GET['fecha'];
		$buscar1=$_GET['fecha1']; 23059-87
		
?>
</head>
<body width="616" height="47">
<p>
  <?php
// Conexión a la tabla y seleccion de registros MATRIX
			$select_diagnostico = mysql_query("Select m.Diahis,m.Diaing,m.Diacod,r11.Descripcion,m.Diacco,m.Diafhc,
												m.Diahhc,m.Diausu,m.Diaest,pacsex,c100.Pacfna,Ingfei,
												TIMESTAMPDIFF(YEAR,c100.Pacfna,c101.Ingfei),c101.Ingcem,c101.Ingent,c100.Pactdo,c100.Pacdoc  	
												from movhos_000243 m
												left join
												root_000011 r11 on ( m.Diacod = r11.Codigo),
												cliame_000100 c100,cliame_000101 c101
												Where
												m.Diafhc between '$buscar' and '$buscar1'
												and m.Diahis = c100.Pachis
												and m.Diahis = c101.Inghis
												and m.Diaing = c101.Ingnin");

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
		?>
		<table width="1500" height="44" border="1">
			<tr class="titulo">
			<td style="background-color: #C3D9FF"><div align="center" class="Estilo7">HISTORIA </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">INGRESO </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">DIAGNOSTICO </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">DESCRIPCION DIAGNOSTICO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CCO QUE FIRMO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FECHA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">HORA</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">USUARIO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">ESTADO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">SEXO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FECHA DE NACIMIENTO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FECHA DE INGRESO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">EDAD</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">NIT</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">DESCRIPCION</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">TIPO DE DOCUMENTO PACIENTE</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">DOCUMENTO PACIENTE</div></td>
			  </tr>
			   <?php
				while($resultado=mysql_fetch_array($select_diagnostico))
				{
					$Diahis = $resultado[0];
					$Diaing = $resultado[1];
					$Diacod = $resultado[2];
					$Descripcion = $resultado[3];
					$Diacco = $resultado[4];
					$Diafhc = $resultado[5];
					$Diahhc = $resultado[6];
					$Diausu = $resultado[7];
					$Diaest = $resultado[8];
					$Pacsex = $resultado[9];
					$Pacfna = $resultado[10];
					$Ingfei = $resultado[11];
					$Edad = $resultado[12];
					$Ingcem = $resultado[13];
					$Ingent = $resultado[14];
					$Pactdo = $resultado[15];
					$Pacdoc = $resultado[16];
				?>
					
						<tr>
							<td width="244"><?php echo $Diahis ?></td>
							<td width="152"><?php echo $Diaing ?></td>
							<td width="198"><?php echo $Diacod ?></td>
							<td width="198"><?php echo $Descripcion ?></td>
							<td width="198"><?php echo $Diacco ?></td>
							<td width="198"><?php echo $Diafhc ?></td>
							<td width="198"><?php echo $Diahhc ?></td>
							<td width="198"><?php echo $Diausu ?></td>
							<td width="198"><?php echo $Diaest ?></td>
							<td width="198"><?php echo $Pacsex ?></td>
							<td width="198"><?php echo $Pacfna ?></td>
							<td width="198"><?php echo $Ingfei ?></td>
							<td width="198"><?php echo $Edad ?></td>
							<td width="198"><?php echo $Ingcem ?></td>
							<td width="198"><?php echo $Ingent ?></td>
							<td width="198"><?php echo $Pactdo ?></td>
							<td width="198"><?php echo $Pacdoc ?></td>
						</tr>	
				<?php
				}
				?>
						
		</table>
	<?php		
?>
</body>
</html>