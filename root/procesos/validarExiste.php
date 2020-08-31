<!--El programa realiza la captura, consulta, edita la informacion correspondiente del root_000119 y de amedian. -->
<!--Publicacion: 2018-12-19, 
	Por: Didier Orozco Carmona. 
	-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
    if(!isset($_SESSION['user']))
    {
        ?>
<style type="text/css">
<!--
.Estilo1 {font-size: 9px}
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
        include("conex.php");
        include("root/comun.php");
        mysql_select_db("matrix");
		$conex = obtenerConexionBD("matrix");
        //$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }
?>
</head>
<body>
<?php
$validacion = $_GET['dian5'];
//echo 'MUESTRA'.$validacion;

	///////funcion validar el dato///////////

		$existe_root = mysql_query("select dian5 from root_000119 where dian5 = '$validacion'");
		$resultado = mysql_fetch_array($existe_root);
		$dian5_resultado = $resultado[0];
		
		if ($dian5_resultado > 0){
			?>
<div class="row" style="width:20%"> <span style="font-size: 0px">
  						</span><span style="font-size: 0px">
  							<label style="color: #080808">  </label>
  						</span>
  						<label style="color: #080808"></label>
  						<span style="font-size: 9px"></span><span style="font-size: 9px">  </span>
	<p align="center" class="Estilo1"><strong>EL DATO YA EXISTE </strong><strong>POR FAVOR </strong><span class="Estilo1"><strong>DIGITAR EL NIT EN LA CONSULTA </strong>
    <br>
    </span><br>
    <input type="button" class="text-success" value="ACEPTAR" onclick=" window.close();"/>
  </p>
</div>
    	<?php
			}else{
				?>
					<script> window.close(); </script>
				<?php
			}
		
	
	
?>
</body>
</html>