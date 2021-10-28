<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Consultar Registro</title>

<?php
    include_once("conex.php");
    include_once("root/comun.php");
	$wemp_pmla=$_REQUEST['wemp_pmla'];
	$wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$conex = obtenerConexionBD("matrix");
    $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
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

	<style>
		html body {
			width: 80%;
			margin: 0 auto 0 auto;
			
		}
	</style>
		<script>
		function mensaje(CodPro) {
			let params 	= new URLSearchParams(location.search);
			var wemp_pmla 	= params.get('wemp_pmla');
			var validacion = null;
			ancho = 300;    alto = 120;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
			settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';
			validacion = window.open ("validarCodigo.php?wemp_pmla="+wemp_pmla+"&CodPro="+CodPro,"miwin",settings2);
			validacion.focus();
		}
	</script>
    <style>
        .alternar:hover{ background-color:#e1edf7;}
		.Estilo4 {color: #000000; font-weight: bold; }


    </style>
    <script>
        function centrar() {
			iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        
		}
	</script>
	<?php
	global $wcliame;
		$activa=$_POST['plantilla_activa'];
		$activa_procexam=$_POST['activa_exam_proce'];
		$Cod_pla=$_POST['Cod_pla'];   $Nom_pla=$_POST['Nom_pla']; $Est=$_POST['Est'];
		
		$id=$_GET['actualizar'];
		$select_cliame_329 = mysql_query ("select * from ".$wcliame."_000329 where Codpla = '$id'");
		$resultado=mysql_fetch_array($select_cliame_329);
		$Codpla = $resultado[3];   
		$Nompla = $resultado[4];
		$Estado = $resultado[5];
		
		
		$accion = isset($_POST['accion']) ? $_POST['accion'] : "";
		if($accion == 'guardar')
		{
			echo 'LLego la variable con ID='.$Cod_pla;
			mysql_queryV("update ".$wcliame."_000329 set Nompla='$Nom_pla',Estado='$Est' where Codpla='$Cod_pla'");
			?>
				<div style="margin-top: 10px;  text-align: center">
				<form method="post" action="Menuplantilla.php?wemp_pmla=<?=$wemp_pmla?>">
				<label style="color: #080808"><strong>DATOS ACTUALIZADOS CORRECTAMENTE</strong> </label>
				<br><br>
				<input type="submit" class="text-success" value="ACEPTAR"/>
				</form>
				</div>
				
    <?php
   }else{
?>
<body width="1200" height="60">
<form action="editarPlantilla.php?wemp_pmla=<?=$wemp_pmla?>" method="post">
	 <table width="1000" border="1" align="center">
		<tr>
			<td width="350%" bgcolor="#C3D9FF"> <p align="center"><strong> MODIFICAR MAESTRO DE PLANTILLA </strong></p> </td>
		</tr>
		</table>		
		<p>&nbsp;</p>
		<div align="center">
		<table width="600" border="0">
		  <tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Codigo de Plantilla:</strong></p></td>
		    <td>
			<strong>
			  <input name="Cod_pla" type="text" id="Cod_pla" size="15" value="<?php echo $Codpla ?>" readonly />
			</strong></td>
		 </tr>
		  <tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Nombre de Plantilla:</strong></p></td>
			<td>
			<strong>
			 <input name="Nom_pla" type="text" id="Nom_pla" size="30" value="<?php echo $Nompla ?>" />
			</strong></td>
			</tr>
			<tr>
			<td bgcolor="#C3D9FF"><p align="left"><strong>Estado:</p></td>
            <td>
				<label><strong>
					<select name="Est" id="Est">
						<?php if ($Estado == 'on'){
						?>
							<option value="on" selected="selected">ACTIVO</option>
						<?php
							}else{
						?>
								<option value="off" selected="selected">INACTIVO</option>
						<?php
							}
						?>
                                <option value="on"> ACTIVO </option> 
								<option value="off"> INACTIVO </option> 
					</select>
               </strong>
			   </label>
			</td>
			</tr>  
						  <tr>
							<td height="35" colspan="2"><div align="center">
							  <p>
							  	<input name="accion" type="hidden" value='guardar' />
								<input name="guardar" type="submit" class="btn-primary" value="Guardar" />
								<a href="Menuplantilla.php?wemp_pmla=<?=$wemp_pmla?>" >RETORNAR</a></label>
								</p>
							</div>
							</td>
						  </tr>
						</table>
					  </div>
					</form>
			
</body>
   <?php } ?>
</html>					