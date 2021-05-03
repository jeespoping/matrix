<!---->
<!--//==========================================================================================================================================
 /**
 * PROGRAMA					  : El Script recibe unos parametros del programa imp_etiquetas.php, al capturar esta informacion se proceden a realizar una consulta
								para que pueda retornar el valor.
								
 * AUTOR        			  : Didier Orozco Carmona.                                                                                       |
 * FECHA PUBLICACION	      : 2020-06-26.                                                                                             |
 * FECHA ULTIMA ACTUALIZACION : 2020-06-26. 
 *
 *									
 */
//                                                                                                                                      |
//==========================================================================================================================================	
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head><input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
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
	///////funcion validar el dato///////////		
		//FUNCION BUSCAR INFORMACION DEL MEDICAMENTO.
		$wbasedatocenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
		$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$wcod = $_GET['wcod'];
		if ($wcod !== null) {
			$select_medicamento = mysql_query("SELECT Artcom, Artgen, Artreg
											from ".$wbasedatomovhos."_000026
											where Artcod='$wcod'");								
			$resultado_medicamento=mysql_fetch_array($select_medicamento);
			$select_lote = mysql_query("SELECT Plocod,Plopro 	
										from ".$wbasedatocenpro."_000004
										where plopro = '$wcod'
										order by Plocod DESC
										limit 1;");
			$resultado_lote=mysql_fetch_array($select_lote);							
			if ($resultado_medicamento == null) {
				$Artcom = 'ARTICULO NO EXISTE';
				$Artgen = 'ARTICULO NO EXISTE';
			?>	
				<input name="ComValidar" type="text" id="ComValidar" size="20" value="<?php echo $Artcom ?>"/>
				<script type="text/javascript"> opener.document.info_medicamento.wnomc.value=document.getElementById('ComValidar').value; </script>
				<input name="GenValidar" type="text" id="GenValidar" size="20" value="<?php echo $Artgen ?>"/>
				<script type="text/javascript"> opener.document.info_medicamento.wnom.value=document.getElementById('GenValidar').value; </script>
				<script> window.close(); </script>
			<?php
			
			}else{
			$Plocod = trim($resultado_lote[0]);	
			$Artcom = trim($resultado_medicamento[0]);
			$Artgen = trim($resultado_medicamento[1]);
			$Artreg = trim($resultado_medicamento[2]);
?>
				<input name="ComValidar" type="text" id="ComValidar" size="20" value="<?php echo $Artcom ?>"/>
				<script type="text/javascript"> opener.document.info_medicamento.wnomc.value=document.getElementById('ComValidar').value; </script>
				<input name="GenValidar" type="text" id="GenValidar" size="20" value="<?php echo $Artgen ?>"/>
				<script type="text/javascript"> opener.document.info_medicamento.wnom.value=document.getElementById('GenValidar').value; </script>
				<input name="InvValidar" type="text" id="InvValidar" size="20" value="<?php echo $Artreg ?>"/>
				<script type="text/javascript"> opener.document.info_medicamento.winv.value=document.getElementById('InvValidar').value; </script>
				<input name="LoteValidar" type="text" id="LoteValidar" size="20" value="<?php echo $Plocod ?>"/>
				<script type="text/javascript"> opener.document.info_medicamento.wlot.value=document.getElementById('LoteValidar').value; </script>
				<script> window.close(); </script>
			<?php
			
			}	
		}else{
			?>
			<script> window.close(); </script>
			<?php
		}
		
		?>						
						
</body>
</html>