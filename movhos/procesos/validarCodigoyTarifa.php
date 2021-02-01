<!---->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta y para consultar la informacion del nombre de los productos y de las respectivas tarifas|
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2020-01-20.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2020-01-25.                                                                                             |
//                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: 
//
//TABLAS DE CONSULTA: Las de UNIX ivart,intar
//
//  
//
//
//
//                                                                                                                                      |
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
        mysql_select_db("matrix");
		$conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
?>
</head>
<body>
<?php	
$Codigo = $_GET['Codigo'];

		//FUNCION BUSCAR PACIENTE Y LLEVAR LA HISTORIA Y EL INGRESO POR MEDIO DE LA IDENTIFICACION.
		//$Identificacion = $_GET['Identificacion'];
		echo 'la codigo entro es ='.$Codigo;
		//$TidR = $_GET['TidR'];
		
		if ($Codigo !== null) {
			
			$select_ivart = "select * from ivart where artcod = '$Codigo' and artact = 'S'";
			$resultado_ivart=odbc_do($conex_o, $select_ivart);
			odbc_fetch_row($resultado_ivart);
			//$Pronom =$CodPro;
			$Pronom = odbc_result($resultado_ivart, 2);
			if ($Pronom == null){
				$Pronom = 'ARTICULO NO EXISTE';
				?>
				<input name="ProValidar" type="text" id="ProValidar" size="20" value="<?php echo $Pronom ?>"/>
				<script type="text/javascript"> opener.document.tarifas.Pronom.value=document.getElementById('ProValidar').value; </script>
				<script> window.close(); </script>
				<?php
			}else{
				//echo 'si entro if el codigo entro es ='.$Pronom;
				?>
				<input name="ProValidar" type="text" id="ProValidar" size="20" value="<?php echo $Pronom ?>"/>
				<script type="text/javascript"> opener.document.tarifas.Pronom.value=document.getElementById('ProValidar').value; </script>
				<script> window.close(); </script>
				<?php
			}	
			
			
		}else{
		?>	
			<script> window.close(); </script>
		<?php
		}
$Tarifa = $_GET['Tarifa'];
echo 'la tarifa entro es ='.$Tarifa;		
		if ($Tarifa !== null) {
			$select_intar = "select * from intar where tarcod = '$Tarifa' and taract = 'S'";
			$resultado_intar=odbc_do($conex_o, $select_intar);
			odbc_fetch_row($resultado_intar);
			//$Pronom =$CodPro;
			$Tarnom = odbc_result($resultado_intar, 2);
			if ($Tarnom == null){
				$Tarnom = 'TARIFA NO EXISTE';
				?>
				<input name="TarValidar" type="text" id="TarValidar" size="20" value="<?php echo $Tarnom ?>"/>
				<script type="text/javascript"> opener.document.tarifas.Tarnom.value=document.getElementById('TarValidar').value; </script>
				<script> window.close(); </script>
				<?php
			}else{
				//echo 'si entro if el codigo entro es ='.$Pronom;
				?>
				<input name="TarValidar" type="text" id="TarValidar" size="20" value="<?php echo $Tarnom ?>"/>
				<script type="text/javascript"> opener.document.tarifas.Tarnom.value=document.getElementById('TarValidar').value; </script>
				<script> window.close(); </script>
				<?php
			}
			?>
			<script> window.close(); </script>
		<?php
		}else{
		?>	
			<script> window.close(); </script>
		<?php
		}
?>						
						
</body>
</html>