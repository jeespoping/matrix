<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Impresión del sticker de productos de central de mezclas
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2018-02-15
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2018-03-13';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//	2018-03-13		Jessica Madrid Mejía	Se aumenta el tamaño de la fecha de vencimiento y se pone en negrita
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	include_once("root/comun.php");
	

	$conex = obtenerConexionBD("matrix");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarTiempoInfusion($codigo)
	{
		global $conex;
		global $wcenmez;
		
		$queryTiempoInsusion = " SELECT Arttin 
								   FROM ".$wcenmez."_000002 
								  WHERE Artcod='".$codigo."';";

		$resTiempoInsusion = mysql_query($queryTiempoInsusion,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryTiempoInsusion." - ".mysql_error());
		$numTiempoInsusion = mysql_num_rows($resTiempoInsusion);
		
		$tiempoInfusion = "";
		if($numTiempoInsusion > 0)
		{
			$rowsTiempoInsusion = mysql_fetch_array($resTiempoInsusion);
			
			if($rowsTiempoInsusion['Arttin']>0)
			{
				$tfd = floor($rowsTiempoInsusion['Arttin']/60);
				$tfh = $rowsTiempoInsusion['Arttin']%60;
				
				if($tfd>0)
				{
					$tfd = $tfd." horas y ";
				}
				else
				{
					$tfd = "";
				}
				
				if($tfh<10)
				{
					$tfh = "0".$tfh;
				}
				
				$tiempoInfusion = $tfd.$tfh." minutos";
			}
			
			
		}
		
		return $tiempoInfusion;
	}
	
	function consultarUsuarios($codProducto,$lote)
	{
		global $conex;
		
		$queryUsuarios = "SELECT Ploela AS CodigoElabora,Plorev AS CodigoRevisa,b.Descripcion AS NombreElabora,c.Descripcion AS NombreRevisa
							FROM cenpro_000004 a
					   LEFT JOIN usuarios b
							  ON b.Codigo=Ploela
					   LEFT JOIN usuarios c
							  ON c.Codigo=Plorev
						   WHERE Plopro='".$codProducto."' 
							 AND Plocod='".$lote."';";
						 
		$resUsuarios = mysql_query($queryUsuarios,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUsuarios." - ".mysql_error());
		$numUsuarios = mysql_num_rows($resUsuarios);
		
		$arrayUsuarios = array();
		if($numUsuarios > 0)
		{
			$rowsUsuarios = mysql_fetch_array($resUsuarios);
			
			$arrayUsuarios['CodigoElabora'] = $rowsUsuarios['CodigoElabora'];
			$arrayUsuarios['CodigoRevisa'] = $rowsUsuarios['CodigoRevisa'];
			$arrayUsuarios['NombreElabora'] = substr($rowsUsuarios['NombreElabora'],0,23);
			$arrayUsuarios['NombreRevisa'] = substr($rowsUsuarios['NombreRevisa'],0,23);
		}
		
		return $arrayUsuarios;
	}
	
	
//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================

	?>
	<html>
	<head>
	  <title>IMPRESION STICKER</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
		
	$(document).ready(function(){
	
		var anchoVentana = $("#anchoVentana").val();
		var altoVentana = $("#altoVentana").val();
		
		$("#bodyStickers").css("width",anchoVentana);
		$("#bodyStickers").css("height",altoVentana);
		
		anchoVentana=parseInt(anchoVentana)+43; 
		altoVentana=parseInt(altoVentana)+82; 
		window.resizeTo(anchoVentana,altoVentana)
		
		window.print();
		window.close();
	});
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
	.sticker
	{
		margin-top: -5px;
		// margin-left: -8px;
		margin-left: -3px;
		border: none;
		width: 185px;
		height: 140px;
		overflow: hidden;
		// border: 2px solid red;
		position:relative;
	}
	
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY id='bodyStickers'>
	<?php
	
	$codigoBarras = "<img src='../../../include/root/clsGenerarCodigoBarras.php?width=150&height=115&barcode=".$codigo."' style='margin-left: 17px;'>";		
	$arrayUsuarios = consultarUsuarios($codigo,$lote);
	$firmaPreparado = "<img src='../../images/medical/hce/Firmas/".$arrayUsuarios['CodigoElabora'].".png' width='60px;' height='20px;' border='0'>";		
	$firmaAprobado = "<img src='../../images/medical/hce/Firmas/".$arrayUsuarios['CodigoRevisa'].".png' width='60px;' height='20px;' border='0'>";		
	$tiempoInfusion = consultarTiempoInfusion($codigo);
	if($tiempoInfusion!="")
	{
		$tiempoInfusion = "<b>TIEMPO INFUSIÓN:</b> ".$tiempoInfusion;
	}
		
	$cantidad = intval($cantidad);
	if($cantidad==0)
	{
		$cantidad = 1;
		
	}
	
	$altoVentana = ($cantidad * 140);
	$anchoVentana = 185;
	
	
	for ($i=0;$i<$cantidad;$i++)
	{
		echo "  <div id='stickerProducto' class='sticker'>
				".$codigoBarras."<br>
				<span id='codProducto' style='font-weight:bold;font-size:7pt;position:absolute;top:38.5px;'>".$codigo."</span>
				<span id='lote' style='font-size:6pt;position:absolute;top:40px;left:50px;'><b>LOTE:</b>".$lote."</span>
				<span id='fechaVencimiento' style='font-size:6pt;position:absolute;top:40px;left:110px;'><b>F.V:</b><span style='font-weight:bold;font-size:6.5pt;'>".$fechaVencimiento."</span></span><br>
				<span id='nombre1' style='font-size:7.5pt;font-weight:bold;position:absolute;top:50px;'>".$nombre1."</span>
				<span id='nombre2' style='font-size:7.5pt;font-weight:bold;position:absolute;top:60px;'>".$nombre2."</span>
				<span id='fechaPreparacion' style='font-size:6pt;position:absolute;top:72px;'><b>F. PREP:</b> ".$fechaPrep."</span>
				<span id='horaPreparacion' style='font-size:6pt;position:absolute;top:72px;left:100px'> <b>H. PREP:</b> ".$horaPrep."</span>
				<span id='preparado' style='font-size:6pt;position:absolute;top:80px;'> <b>Preparado por QF:</b> <br>".$arrayUsuarios['NombreElabora']."</span>
				<span id='firmaPreparado' style='position:absolute;position:absolute;left:125px;top:82px;'>".$firmaPreparado."</span>
				<span id='aprobado' style='font-size:6pt;position:absolute;top:100px;'> <b>Aprobó:</b> <br>".$arrayUsuarios['NombreRevisa']."</span>
				<span id='firmaAprobado' style='font-size:6pt;position:absolute;left:125px;top:102px;'>".$firmaAprobado."</span><br>
				<span id='tiempoInfusion' style='font-size:6pt;position:absolute;top:120px;'>".$tiempoInfusion."</span><br>
				<span id='nota' style='font-size:6pt;position:absolute;top:128px;'>Conservar en nevera de 2° a 8° C</span><br>
			</div>
			<input type='hidden' id='anchoVentana' value='".$anchoVentana."'>
			<input type='hidden' id='altoVentana' value='".$altoVentana."'>
			";
			
			
	}
	
	?>
	</BODY>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================


}//Fin de session
?>
