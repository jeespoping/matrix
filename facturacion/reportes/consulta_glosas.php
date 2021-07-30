<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a las glosas, y manda el enlace respectivo para generar la descarga en EXPORT_GLOSAS.                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-08-14.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2020-01-22.                                                                                             |
//                                      																						        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: cliame_000273, cliame_000024, cliame_000274, cliame_000275
//
//  
//FECHA ULTIMA ACTUALIZACION  : 2020-01-22.
//  * Se procede a quitar columnas del reporte.
//  * Se quita validador de fechas mayor a un mes.   
//                                                                                                                                     |
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
	$wemp_pmla=$_REQUEST['wemp_pmla'];
    if(!isset($_SESSION['user']))
    {
		 ?>
		<style 
			type="text/css">

		</style>
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
    <script>
        $(function() {
			$( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
			$( "#datepicker3" ).datepicker();
			$( "#datepicker4" ).datepicker();
        });
    </script>
</head>
<body width="1200" height="47">
	<form action="consulta_glosas.php?wemp_pmla=<?php echo($wemp_pmla) ?>" method="post">  
		<table width="1200" border="1" align="center">
			<tr>
				<td width="50%" align="" style="border: groove; width: 0%">
					<input type="image" id="btnVer" src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80">
				<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>GENERAR ARCHIVO DE GLOSAS - FACTURACION </strong></p> </td>
			</tr>
		</table>		
		<p>&nbsp;</p>
		<div align="center">
		<table width="258" border="1">
			<tr>
				<td width="248" colspan="2"><div align="center" class="h5"><strong>Ingrese los parametros de consulta: </strong></div>        </td>
			</tr>
			<tr>
				<td bgcolor="#C3D9FF"><p align="left"><strong>Fecha Inicial:</strong></p></td>
				<td >
				<strong>
					<input name="fecha1" type="text" id="datepicker1" size="8" readonly="on" value="<?php echo $_POST["fecha1"]?>" />
				</strong></td>
			</tr>
			<tr>
				<td bgcolor="#C3D9FF"><p align="left"><strong>Fecha Final: </strong></p></td>
				<td>
					<strong>
						<input name="fecha2" type="text" id="datepicker2" size="8" readonly="on" value="<?php echo $_POST["fecha2"]?>"/>
					</strong></td>
			</tr>
			<tr>
				<td height="35" colspan="2">
				<div align="center">
					<p>
						<input name="buscador" type="submit" class="btn-primary" value="Buscar" />
					</p>
				</div>
				</td>
			</tr>
		</table>
		</div>
	</form>


<p>
  <?php
	if 	($_POST['buscador'])
		{
			$buscar = $_POST['fecha1'];
			$buscar1 = $_POST['fecha2'];
			$buscar= $buscar.' '.'00:00:00';
			$buscar1= $buscar1.' '.'23:59:59';
			$nuevafecha = strtotime ( '+31 day' , strtotime ( $buscar ) ) ;
			$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
		// Si está vacío no realiza la busqueda, sino realizamos la búsqueda
		if($buscar=='' or $buscar1=='' or $buscar>$buscar1)
			{
				?>
					<style 
					type="text/css">
					</style>
					<div align="center">
					<label>Por favor validar las fecha:<br />- Que los campos no esten vacios <br />
					- Que la fecha final no se han mayor a la fecha inicial
					</label>
					</div>
				<?php
				//echo "Por favor ingresar las fechas";
				//echo "Por favor ingresar las fechas";
			}
			else{
				$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
				// Conexión a la tablas y seleccion de registros MATRIX SOLO CON UN COUNT
				$select_glosa = mysql_query("SELECT count(*) cant
											From ".$wcliame."_000273 AS A LEFT JOIN ".$wcliame."_000024 ON (Gloent = Empcod), ".$wcliame."_000274,".$wcliame."_000275 
											where gloest='on'
											and Glofhg BETWEEN '$buscar' and '$buscar1'
											and glonrg=gdeidg
											and GDEEST='on'
											and gdeidg=Jusglo
											and gdecau=Juscau");

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
	?>
	<table width="248" height="44" border="0" align="center">
			<tr>
 			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CANTIDAD DE REGISTROS ENCONTRADO:</div></td>
			</tr>
	<?php
    while($resultado=mysql_fetch_array($select_glosa))
    {
        $Cantidad_glosa = $resultado[0];
	?>
		
			<tr>
				<td align="center" width="244"><?php echo $Cantidad_glosa ?></td>
			</tr>	
	<?php
	}
	?>
	</table>
	<table align="center" style="padding-top: auto">
		<tr>
			<td width="198"><div align="center" class="Estilo3"><a href="export_glosa.php?wemp_pmla=<?=$wemp_pmla?>&fecha=<?php echo $buscar ?>&fecha1=<?php echo $buscar1 ?>">DESCARGAR </a></div></td>
		</tr>	
	</table>
			
	<?php
}
}
?>
</body>
</html>