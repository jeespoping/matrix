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
//MODIFICACION X DIDIER OROZCO CARMONA - 2020-05-14: Se quita el like y por ende a esto la condicion de los diagnosticos para que salgan todos. 
//                                                                                                                                     |
//==========================================================================================================================================	
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
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
<form action="consulta_pac_diagnostico.php?wemp_pmla=<?php echo($wemp_pmla) ?>" method="post">
  
  <table width="1200" border="1" align="center">
  	<tr>
  	  <td width="50%" align="" style="border: groove; width: 0%">
       <input type="image" id="btnVer" src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80">
    <td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong>GENERAR ARCHIVO DE PACIENTES CON DIAGNOSTICO </strong></p> </td>
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
			
			include_once("root/comun.php");
			
			$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
			$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
			
// Conexión a la tablas y seleccion de registros MATRIX SOLO CON UN COUNT
			$select_diagnostico = mysql_query("SELECT count(*) cant
    									from ".$wmovhos."_000243 m
										left join
										 root_000011 r11 on ( m.Diacod = r11.Codigo),
										".$wcliame."_000100 c100,".$wcliame."_000101 c101
										Where
											m.Diafhc between '$buscar' and '$buscar1' 
											and m.Diahis = c100.Pachis
											and m.Diahis = c101.Inghis
											and m.Diaing = c101.Ingnin");   	
 
//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:
	?>
<table width="248" height="44" border="0" align="center">
			 <tr>
				<td style="background-color: #C3D9FF"><div align="center" class="Estilo5">CANTIDAD DE REGISTROS:</div></td>
			 </tr>
			 
   <?php
    while($resultado=mysql_fetch_array($select_diagnostico))
    {
        $Cant = $resultado[0];
	?>
		
			<tr>
				<td align="center" width="244"><?php echo $Cant ?></td>
			</tr>	
	<?php
	}
	?>
</table>
			<table align="center" style="padding-top: auto">
				<tr>
				<td width="198"><div align="center" class="Estilo3"><a href="export_pac_diagnostico.php?cant=<?php echo $Cant ?>&fecha=<?php echo $buscar ?>&fecha1=<?php echo $buscar1 ?>">EXPORTAR </a></div></td>
				</tr>	
			</table>
			
	<?php
}
}
?>
</body>
</html>