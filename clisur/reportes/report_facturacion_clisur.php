<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta de un query respectivo a facturas y notas fuente 27-28, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_REPORT_EXCEL y EXPORT_EXCEL_REPORT_EXCEL_FACTURAS.                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-05-00.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-05-00.                                                                                             |
//DESCRIPCION			      : El programa realiza la consulta de un query respectivo a facturas y notas fuente 27-28, y manda el enlace respectivo para generar la descarga en EXPORT_EXCEL_REPORT_EXCEL y EXPORT_EXCEL_REPORT_EXCEL_FACTURAS.                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: clisur_000020,clisur_000021,clisur_000018,clisur_000024,clisur_000065
//
//  
// 
//                                                                                                                                     |
//==========================================================================================================================================	
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
	include("conex.php");
    include("root/comun.php");
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	$wactualiz = 1;
	encabezado( "GENERAR REPORTE DE FACTURACION CLINICA DEL SUR DE NOTAS FUENTE 27-28 Y FACTURAS", $wactualiz, $institucion->baseDeDatos );
    if(!isset($_SESSION['user']))
    {
        ?>
<style type="text/css">
<!--
.Estilo5 {
	color: #000000;
	font-size: 16px;
	font-weight: bold;
}
.Estilo7 {font-size: 7px}
-->
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
	<!-- <script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	<script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script> -->
	<script src="../../../matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	<script src="../../../matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
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
<form action="report_facturacion_clisur.php?wemp_pmla=<?=$wemp_pmla?>" method="post">
  
	
    	<p>&nbsp;</p>
  <div align="center">
    <table width="384" border="2">
      <tr>
        <td colspan="2"><div align="center" class="h5"><strong>Ingrese los parametros de consulta: </strong></div>        </td>
      </tr>
      <tr>
        <td width="179" bgcolor="#C3D9FF"><p align="left"><strong>Fecha Inicial:</strong></p>        </td>
        <td width="189" ><strong>
          <input name="fecha1" type="text" id="datepicker1" size="8" readonly="on" value="<?php echo $_POST["fecha1"]?>" />
        </strong></td>
      </tr>
      <tr>
        <td height="31" bgcolor="#C3D9FF"><p align="left"><strong>Fecha Final: </strong></p>        </td>
        <td><strong>
          <input name="fecha2" type="text" id="datepicker2" size="8" readonly="on" value="<?php echo $_POST["fecha2"]?>"/>
        </strong></td>
      </tr>
      <tr>
        <td height="35" rowspan="2" bgcolor="#C3D9FF"><div align="center"><strong>Seleccione reporte:</strong><strong>
          <label></label>
          </strong></div></td>
        <td height="16"><p><strong>
          	<label>
          	<input name="notas" type="radio" value="notas" />
            Notas:</label>
          	Fuente 27-28.</strong>
            <label></label>
          </td>
      </tr>
      <tr>
        <td height="33"><label>
		  <input name="facturas" type="radio" value="facturas" /> 
		Facturas</label></td>
      </tr>
      <tr>
        <td height="35" colspan="2"><div align="center">
          <p>
            <input name="buscador" type="submit" class="btn-primary" value="Buscar" />	
			<input type="reset" class="btn-primary" value="Limpiar" />	
            </p>
        </div></td>
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
			$notas_clisur = $_POST['notas'];
			$facturas_clisur = $_POST['facturas'];
		if ($_POST['notas']){
			// Si está vacío no realiza la busqueda, sino realizamos la búsqueda
			if($buscar=='' or $buscar1=='')
				{
				echo "Por favor ingresar las fechas";
		
			}else{
				
			// Conexión a la tablas y seleccion de registros MATRIX SOLO CON UN COUNT
				$select_notas = mysql_queryV("SELECT count(*) cant
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
			<table width="248" height="44" border="0" align="center">
			 <tr>
 			  	<td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CANTIDAD DE NOTAS ENCONTRADAS:</div></td>
			 </tr>
	<?php
    while($resultado=mysql_fetch_array($select_notas))
    {
        $Rnotas = $resultado[0];
	?>
			<tr>
				<td align="center" width="244"><?php echo $Rnotas ?></td>
			</tr>	
	<?php
	}
	?>
</table>
		<table align="center" style="padding-top: auto">
			<tr>
				<td width="198"><div align="center" class="Estilo3"><a href="export_excel_report_clisur.php?rnotas=<?php echo $Rnotas ?>&notas=<?php echo $notas_clisur ?>&fecha_egreso=<?php echo $Egrfee ?>&fecha_ingreso=<?php echo $Ingfei ?>&fecha=<?php echo $buscar ?>&fecha1=<?php echo $buscar1 ?>&servicio=<?php echo $Sercod ?>">EXPORTAR </a></div></td>
				</tr>	
			</table>
			
	<?php
}
}

// consulta de facturas
		if ($_POST['facturas']){
			// Si está vacío no realiza la busqueda, sino realizamos la búsqueda
			if($buscar=='' or $buscar1=='')
				{
				echo "Por favor ingresar las fechas";
		
			}else{
				
			// Conexión a la tablas y seleccion de registros MATRIX SOLO CON UN COUNT
				$select_facturas = mysql_queryV("SELECT count(*) cant
											from  clisur_000018,clisur_000024,clisur_000065 
											where Fenfec BETWEEN '$buscar' AND '$buscar1'        
 											and  Fenest = 'on'  
 											and  Fencod = Empcod 
 											and  Fenfac = Fdedoc ");

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
	?>
			<table width="248" height="44" border="0" align="center">
			 <tr>
 			  	<td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CANTIDAD DE FACTURAS ENCONTRADAS:</div></td>
			 </tr>
	<?php
    while($resultado=mysql_fetch_array($select_facturas))
    {
        $Rfacturas = $resultado[0];
	?>
			<tr>
				<td align="center" width="244"><?php echo $Rfacturas ?></td>
			</tr>	
	<?php
	}
	?>
			</table>
		<table align="center" style="padding-top: auto">
			<tr>
				<td width="198"><div align="center" class="Estilo3"><a href="export_excel_report_clisur_facturas.php?rfacturas=<?php echo $Rfacturas ?>&facturas=<?php echo $notas_clisur ?>&fecha_egreso=<?php echo $Egrfee ?>&fecha_ingreso=<?php echo $Ingfei ?>&fecha=<?php echo $buscar ?>&fecha1=<?php echo $buscar1 ?>&servicio=<?php echo $Sercod ?>">EXPORTAR </a></div></td>
			</tr>	
		</table>
			
	<?php
}
}

}
?>
</body>
</html>