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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
    include_once("conex.php");
    include_once("root/comun.php");
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
    <script src="//code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="//mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
	<script src="//mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
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
<form action="consulta_egresos_grd.php" method="post">
  
  <table width="1200" border="1" align="center">
  	<tr>
  	  <td width="50%" align="" style="border: groove; width: 0%">
       <input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80">
    <td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>GENERAR ARCHIVO DE PACIENTES EGRESADOS </strong></p> </td>
   </tr>
   </table>		
    <p>&nbsp;</p>
	<div align="center">
    <table width="258" border="1">
      <tr>
        <td width="248" colspan="2"><div align="center" class="h5"><strong>Ingrese los parametros de consulta: </strong></div>        </td>
      </tr>
      <tr>
        <td bgcolor="#C3D9FF"><p align="left"><strong>Fecha Inicial:</strong></p>        </td>
        <td ><strong>
          <input name="fecha1" type="text" id="datepicker1" size="8" readonly="on" value="<?php echo $_POST["fecha1"]?>" />
        </strong></td>
      </tr>
      <tr>
        <td bgcolor="#C3D9FF"><p align="left"><strong>Fecha Final: </strong></p>        </td>
        <td><strong>
          <input name="fecha2" type="text" id="datepicker2" size="8" readonly="on" value="<?php echo $_POST["fecha2"]?>"/>
        </strong></td>
      </tr>
      <tr>
        <td height="35" colspan="2"><div align="center">
          <p>
            <input name="buscador" type="submit" class="btn-primary" value="Buscar" />
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

// Si está vacío no realiza la busqueda, sino realizamos la búsqueda
		if($buscar=='' or $buscar1=='')
			{
			echo "Por favor ingresar las fechas";
	
		}else{
			
// Conexión a la tablas y seleccion de registros MATRIX SOLO CON UN COUNT
			$select_grd = mysql_queryV("SELECT count(*) cant
     									FROM cliame_000108 a, cliame_000112 b, cliame_000101 d, movhos_000011
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
<table width="248" height="44" border="0" align="center">
			 <tr>
			 <!-- <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">HISTORIA </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7"> INGRESO </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo7">FECHA DE EGRESO </div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FECHA DE INGRESO</div></td>
			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">FECHA DE INGRESO</div></td>-->
 			  <td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CANTIDAD DE REGISTROS:</div></td>
			 </tr>
			 
   <?php
    while($resultado=mysql_fetch_array($select_grd))
    {
        $Egrhis = $resultado[0];
		//$Egring = $resultado[1];
        //$Egrfee = $resultado[2];
		//$Ingfei = $resultado[3];
	?>
		
			<tr>
				<td align="center" width="244"><? echo $Egrhis ?></td>
		<!--		<td width="152"><? //echo $Egring ?></td>
				<td width="198"><? //echo $Egrfee ?></td>
				<td width="198"><? //echo $Ingfei ?></td>-->
			</tr>	
	<?php
	}
	?>
</table>
			<table align="center" style="padding-top: auto">
				<tr>
				<td width="198"><div align="center" class="Estilo3"><a href="export_excel_grd.php?historia=<?php echo $Egrhis ?>&ingreso=<?php echo $Egring ?>&fecha_egreso=<?php echo $Egrfee ?>&fecha_ingreso=<?php echo $Ingfei ?>&fecha=<?php echo $buscar ?>&fecha1=<?php echo $buscar1 ?>&servicio=<?php echo $Sercod ?>">EXPORTAR </a></div></td>
				</tr>	
			</table>
			
	<?php
}
}
?>
</body>
</html>