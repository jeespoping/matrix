<!--//==========================================================================================================================================
//PROGRAMA				      : Recibe los parametros de consulta_glosas.php y genera un archivo en excel.						|
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-08-14.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2020-01-22.                                                                                             |
//                                      																						        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: cliame_000273, cliame_000024, cliame_000274, cliame_000275
//
//  FECHA ULTIMA ACTUALIZACION  : 2020-01-22.
//  * Se procede a quitar columnas del reporte.
//  * Se quita validador de fechas mayor a un mes.                                                                                                                                   |
//==========================================================================================================================================	
-->
<?php
// LIBRERIAS PARA DESCARGAR DE FORMA AUTOMATICA LA INFORMACION
		header('Content-type: application/vnd.ms-excel; charset=UTF-8');
		header("Content-disposition: attachment; filename=glosas.xls");
		header('Pragma: no-cache');
		header('Expires: 0');
?>

<!DOCTYPE html>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
        //RECIBIR LOS PARAMETROS DE CONSULTA_GLOSAS.PHP
		include_once("root/comun.php");
        $conex = obtenerConexionBD("matrix");
		$buscar=$_GET['fecha'];
		$buscar1=$_GET['fecha1'];
		
?>
</head>
<body>
  <?php
  include_once("conex.php");
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
  $wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	
// Conexión a la tabla y seleccion de registros MATRIX
			$select_glosa = mysql_query("Select Glonfa,Gloent,empnom,Glohis,Gloing,Glofhg,
										Gloecf,Gdecco,Gdevfa,Gdecgl,Gdevgl,Gdecau,Jusdes,Gdevac,Gdeobj 
										From ".$wcliame."_000273 AS A LEFT JOIN ".$wcliame."_000024 ON (Gloent = Empcod), ".$wcliame."_000274,".$wcliame."_000275 
										where gloest='on'
										and Glofhg BETWEEN '$buscar' AND '$buscar1'
										and glonrg=gdeidg
										and GDEEST='on'
										and gdeidg=Jusglo
										and gdecau=Juscau");

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
		?>
		<table border='1'>
			<tr align="center" style="background-color: #C3D9FF">
			  <td>FACTURA</div></td>
			  <td>ENTIDAD</div></td>
			  <td>NOMBRE_ENTIDAD</div></td>
			  <td>HISTORIA</div></td>
			  <td>INGRESO</div></td>
			  <td>FECHA Y HORA DE GRABACION GLOSA</div></td>
			  <td>ESTADO DE CARTERA PARA FACTURAS</div></td>
			  <td>CENTRO DE COSTOS</div></td>
			  <td>VALOR FACTURADO</div></td>
			  <td>CANTIDAD GLOSADA</div></td>
			  <td>VALOR GLOSADO</div></td>
			  <td>CAUSA</div></td>
			  <td>DESCRIPCION CAUSA</div></td>
			  <td>VALOR ACEPTADO</div></td>
			  <td>OBJECION AUDITORIA</div></td>
			</tr>
			   <?php
				while($resultado=mysql_fetch_array($select_glosa))
				{
					$Glonfa = $resultado[0]; $Gloent = $resultado[1]; $Empnom = $resultado[2];    $Glohis = $resultado[3]; 
					$Gloing = $resultado[4]; $Glofhg = $resultado[5]; $Gloecf = $resultado[6]; $Gdecco = $resultado[7]; 
					$Gdevfa = $resultado[8]; $Gdecgl = $resultado[9]; $Gdevgl = $resultado[10]; $Gdecau = $resultado[11]; 
					$Jusdes = $resultado[12]; $Gdevac = $resultado[13];$Gdeobj = $resultado[14];
				
				?>
					
				<tr>
					<td ><?php echo $Glonfa ?></td><td ><?php echo $Gloent ?></td><td><?php echo $Empnom ?></td>
					<td ><?php echo $Glohis ?></td><td ><?php echo $Gloing ?></td><td><?php echo $Glofhg ?></td>
					<td ><?php echo $Gloecf ?></td><td ><?php echo $Gdecco ?></td><td><?php echo $Gdevfa ?></td>
					<td ><?php echo $Gdecgl ?></td><td ><?php echo $Gdevgl ?></td><td><?php echo $Gdecau ?></td>
					<td ><?php echo $Jusdes ?></td><td ><?php echo $Gdevac ?></td><td ><?php echo $Gdeobj ?></td>
				</tr>	
				<?php
				}
				?>
						
		</table>
	<?php		
?>
</body>
</html>