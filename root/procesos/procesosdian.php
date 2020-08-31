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
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }
?>
</head>


<body width="616" height="47">

<table width="1500" height="113" style="border: groove; width: 1500px">
                        <tr>
                            <td width="20%" align="center" style="border: groove; width: 20%">
                                <input type="image" id="btnVer" src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80">                          </td>
                            <td width="48%" align="center" style="border: groove; width: 55%">
                                <p><b>CONSULTAR RUT </b><strong></strong></p>                          </td>
                            <td width="32%" style="border: groove; width: 35%">
                                <table>
                                    <tr>
                                        <td>Codigo: </td>
                                    </tr>
                                    <tr>
                                        <td>Version: 1.0 </td>
                                    </tr>
                                    <tr>
                                        <td>Pagina: 1</td>
                                    </tr>
                                    <tr>
                                        <td>Fecha de Emision: <?php echo $fechaEmision; ?></td>
                                    </tr>
                                </table>                          </td>
                        </tr>
</table>

<form action="procesosdian.php" method="post">
  <div align="center"><strong>BUSCAR NUMERO DE NIT :</strong> 
    <input name="palabra">
    <input type="submit" name="buscador" value="Buscar">
    <?php
		echo '<span class="nuevo" title="Agregar un nuevo producto"> 
	     	<a href="dian.php">  
	          NUEVO
	      	</a>
			</span>
			<br />';

	?>
  </div>
</form>


<p>
  <?php
	if 	($_POST['buscador'])
		{
			$buscar = $_POST['palabra'];

// Si está vacío, lista todo, sino realizamos la búsqueda
		if($buscar=='')
			{
			//echo "No se ha ingresado un valor a buscar";
			$select_root = mysql_query("SELECT * from root_000119");
			
			?>
			<table width="1500" height="44" border="1">
				<tr>
					<td style="background-color: silver" height="18"><div align="center"><strong>NUMERO DE FORMULARIO </strong></div></td>
					<td style="background-color: silver"><div align="center"><strong>RAZON SOCIAL</strong></div></td>
					<td style="background-color: silver"><div align="center"><strong>NUMERO DE IDENTIFICACION TRIBUTARIA (NIT) </strong></div></td>
					<td style="background-color: silver"><div align="center"><strong>ACCION</strong></div></td>
				</tr>
			<?php
			    while($resultado=mysql_fetch_array($select_root))
				{
					$dian4 = $resultado[4];
					//$dian4=trim($dian4);    
					$dian5 = $resultado[5];
					$dian35 = $resultado[20];
				?>

				<tr>
					<td width="244" height="18"><?php echo $dian4 ?> <div align="center"></div></td>
					<td width="152"><?php echo $dian35 ?></td>
					<td width="198"><?php echo $dian5 ?></td>
					<td width="198"><a href="dianeditar.php?actualizar=<?php echo $dian5 ?>">EDITAR </a></td>
				</tr>

				<?php
				}
			?>
			</table>
	<?php
	
		}else{
// Conexión a la base de datos y seleccion de registros MATRIX
			$select_root = mysql_query("SELECT * from root_000119 WHERE dian5 = '$buscar'");
        //$resultado1 = mysql_fetch_array($select_root);
		/*$select_amedian = "select * from amedian where dian5 = '$buscar'";
		//odbc_do($conex_o, $select_amedian);
		$resultado=odbc_do($conex_o, $select_amedian);*/

//SI HAY REGISTROS EN LA TABLA, TRAER ESOS REGISTROS:

    while($resultado=mysql_fetch_array($select_root))
    {
        $dian4 = $resultado[4];
		//$dian4=trim($dian4);    
		$dian5 = $resultado[5];
        $dian35 = $resultado[20];
		?>
		<table width="1500" height="44" border="1">
			<tr>
				<td style="background-color: silver" height="18"><div align="center"><strong>NUMERO DE FORMULARIO </strong></div></td>
				<td style="background-color: silver"><div align="center"><strong>RAZON SOCIAL </strong></div></td>
				<td style="background-color: silver"><div align="center"><strong>NUMERO DE IDENTIFICACION TRIBUTARIA (NIT) </strong></div></td>
				<td style="background-color: silver"><div align="center"><strong>ACCION</strong></div></td>
			</tr>
			<tr>
				<td width="244" height="18"><?php echo $dian4 ?> <div align="center"></div></td>
				<td width="152"><?php echo $dian35 ?></td>
				<td width="198"><?php echo $dian5 ?></td>
				<td width="198"><a href="dianeditar.php?actualizar=<?php echo $dian5 ?>">EDITAR </a></td>
			</tr>
		</table>
		<?php
	}
}
}
?>
</body>
</html>