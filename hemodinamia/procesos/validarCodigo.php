<!---->
<!--//==========================================================================================================================================
//PROGRAMA				      : El programa realiza la consulta y para consultar la informacion del nombre de los productos y informacion del paciente se reciben los parametros del prgorama Menuplantilla.php                                      |
//AUTOR				          : Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-09-20.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-11-05.                                                                                             |
//                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS SON: 
//
//TABLAS DE CONSULTA: Para consultar las de UNIX facon,inexa,inexatar,ivart,ivarttar,inpro,inprotar
//CONSULTA LA DESCRIPCION DEL PACIENTE DESDE CLIAME_000100 Y CLIAME_000101
//  
//
//
//NOTA: 2019-10-30 Por Didier Orozco Carmona: Se agrega condicion para llevar 
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
		$wemp_pmla=$_REQUEST['wemp_pmla'];
		$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
?>
</head>
<body>
<?php	
global $wcliame;
$CodPro = $_GET['CodPro'];
//echo 'MUESTRA'.$validacion;

	///////funcion validar el dato///////////
		if ($CodPro !== null) {
			$select_inpro = "select * from inpro where procod = '$CodPro'";
			$resultado_inpro=odbc_do($conex_o, $select_inpro);
			odbc_fetch_row($resultado_inpro);
			//$Pronom =$CodPro;
			$Pronom = odbc_result($resultado_inpro, 2);
			//CONDICION INEXA
				if ($Pronom == null) {														
					$select_inexa = "select * from inexa where exacod = '$CodPro'";
					$resultado_inexa=odbc_do($conex_o, $select_inexa);
					odbc_fetch_row($resultado_inexa);
					//$Pronom ='2';
					$Pronom = odbc_result($resultado_inexa, 2);														
				}
					//CONDICION IVART
					if ($Pronom == null){
						$select_ivart = "select * from ivart where artcod = '$CodPro'";
						$resultado_ivart=odbc_do($conex_o, $select_ivart);
						odbc_fetch_row($resultado_ivart);
						//$Pronom ='3';
						$Pronom = odbc_result($resultado_ivart, 2);
					}
						//CONDICION VACIO
						If ($Pronom == null)
						$Pronom = 'CODIGO NO EXISTE EN UNIX';
		}	
		else {
		$Pronom = 'CODIGO NO EXISTE';
		}
		if ($Pronom == 'CODIGO NO EXISTE EN UNIX'){
?>							
			<input name="DesValidar" type="text" id="DesValidar" size="20" value="<?php echo $Pronom ?>"/>
			<script type="text/javascript"> opener.document.detallePlantilla.Coddes.value=document.getElementById('DesValidar').value; </script>	
			
			<script> window.close(); </script>
			<?php
		}else{
			?>
				<input name="DesValidar" type="text" id="DesValidar" size="20" value="<?php echo $Pronom ?>"/>
				<script type="text/javascript"> opener.document.detallePlantilla.Coddes.value=document.getElementById('DesValidar').value; </script>
				<script> window.close(); </script>
			<?php
		}
		
		//FUNCION BUSCAR PACIENTE Y LLEVAR LA HISTORIA Y EL INGRESO POR MEDIO DE LA IDENTIFICACION.
		$Identificacion = $_GET['Identificacion'];
		echo 'la cc entro es ='.$Identificacion;
		$TidR = $_GET['TidR'];
		echo 'la tipo id entro es ='.$Tid;
		if ($Identificacion !== null) {
			$select_paciente = mysql_queryV("SELECT Inghis,Ingnin,Pactdo,Pacdoc,Pacap1,Pacap2,Pacno1,Pacno2,Pacact 
											from ".$wcliame."_000100 left join ".$wcliame."_000101 on ".$wcliame."_000100.Pachis=".$wcliame."_000101.Inghis 
											where Pacdoc='$Identificacion' and Pactdo='$TidR' order by ".$wcliame."_000101.id desc
limit 1");
			$resultado_paciente=mysql_fetch_array($select_paciente);
			$Inghis = $resultado_paciente[0];
			$Ingnin = $resultado_paciente[1];
			$Pactdo = $resultado_paciente[2];
			$Pacdoc = $resultado_paciente[3];
			$Pacap1 = $resultado_paciente[4];
			$Pacap2 = $resultado_paciente[5];
			$Pacno1 = $resultado_paciente[6];
			$Pacno2 = $resultado_paciente[7];
			$Pacact = $resultado_paciente[8];
			$concat_nombre = $Pacno1.' '.$Pacno2.' '.$Pacap1.' '.$Pacap2;
			if ($Pacact == 'on'){
				?>
				<input name="HisValidar" type="text" id="HisValidar" size="20" value="<?php echo $Inghis ?>"/>
				<script type="text/javascript"> opener.document.presupuesto.Historia.value=document.getElementById('HisValidar').value; </script>
				<input name="IngValidar" type="text" id="IngValidar" size="20" value="<?php echo $Ingnin ?>"/>
				<script type="text/javascript"> opener.document.presupuesto.Ingreso.value=document.getElementById('IngValidar').value; </script>
				<input name="NomValidar" type="text" id="NomValidar" size="20" value="<?php echo $concat_nombre ?>"/>
				<script type="text/javascript"> opener.document.presupuesto.Nompac.value=document.getElementById('NomValidar').value; </script>
				<script> window.close(); </script>
				<?php
			}else{
				$Ingnin = null;
				?>
				<input name="HisValidar" type="text" id="HisValidar" size="20" value="<?php echo $Inghis ?>"/>
				<script type="text/javascript"> opener.document.presupuesto.Historia.value=document.getElementById('HisValidar').value; </script>
				<input name="IngValidar" type="text" id="IngValidar" size="20" value="<?php echo $Ingnin ?>"/>
				<script type="text/javascript"> opener.document.presupuesto.Ingreso.value=document.getElementById('IngValidar').value; </script>
				<input name="NomValidar" type="text" id="NomValidar" size="20" value="<?php echo $concat_nombre ?>"/>
				<script type="text/javascript"> opener.document.presupuesto.Nompac.value=document.getElementById('NomValidar').value; </script>
				<script> window.close(); </script>
				<?php
			}
			
			
		}
?>						
						
</body>
</html>