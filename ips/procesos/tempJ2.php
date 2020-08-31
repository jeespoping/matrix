<?php
include_once("conex.php");
global $facturacionErp;
$facturacionErp = TRUE;
include_once("./../../movhos/procesos/cargoscpx.php");
$conexUnix = odbc_connect('facturacion','informix','sco');


// $sql2 = "
// SELECT his, ing, con, pro, abs(can) as cant, id
  // FROM tempJ 
 // WHERE grabaSandraNury = 'No se pudo activar en unix'

 // ";
// $res2 = mysql_query($sql2, $conex) or die("<b>ERROR EN QUERY MATRIX(sql2):</b><br>".mysql_error());
// while($row2 = mysql_fetch_array($res2))
// {			
	// $wusuario 					= "03150";
	// $wccogra					= "1130"; 
	// $tipoCargoPDA 				= "C";
	// $wemp_pmla					= "01"; 	
	// $whistoria					= $row2["his"]; 
	// $wprocod					= $row2["pro"];
	// $wcantidad 					= $row2["cant"];
	// $wfeccar_PDA				= "";
	// $horaCarRonda				= "";
	// $numCargoInv				= "";
	// $linCargoInv				= "";
	// $warningCargoInv			= "";
	// $conexUnix_FacturacionPpal	= "";
	// $wing						= $row2["ing"];
	// $permGrabarCargoCcoDifPda	= "on";
	
	// $res = grabarArticuloPorPda($wusuario, $wccogra, $tipoCargoPDA, $wemp_pmla, $whistoria, $wprocod, $wcantidad, $wfeccar_PDA, $horaCarRonda, $numCargoInv, $linCargoInv, $warningCargoInv, $conexUnix_FacturacionPpal,$wing, $permGrabarCargoCcoDifPda);
	
	// echo "<br>---------------------";
	// echo "<br>MSJ=".$warningCargoInv;
	// echo "<br>NUM=".$numCargoInv;
	// echo "<br>LIN=".$linCargoInv;
	// echo "<br> ID=".$row2["id"];
	// echo "<br> PR=".$wprocod;
	// echo "<br> CA=".$wcantidad;
	// echo "<br> HI=".$row2["his"];
	// echo "<pre>".print_r($res)."</pre>";
	
	// $sqlInsert = "
	// UPDATE tempJ
	   // SET grabaSandraNury = 'GRABADO2'
	 // WHERE  id = '".$row2["id"]."'
	// ";
	// mysql_query($sqlInsert, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInsert):</b><br>".mysql_error());
	
	

	// $sqlInfo = "
	// SELECT Ingcem, Pacdoc
	  // FROM cliame_000101, cliame_000100
	 // WHERE Inghis = '".$whistoria."'
	   // AND Ingnin = '".$wing."'
       // AND Pachis = Inghis	   
	// ";
	// $resInfo = mysql_query($sqlInfo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo):</b><br>".mysql_error());
	// if($rowInfo = mysql_fetch_array($resInfo))
	// {
		// $pac['sac'] = $wccogra;
		// $pac['his'] = $whistoria;
		// $pac['ing'] = $wing;
		// $pac['doc'] = $rowInfo['Pacdoc'];
		// $emp		= "01";
		// $wbasedato	= "movhos";
		// $usuario	= "03150";
		// $wuse		= "03150";
		// $cco['cod']	= $wccogra;
		// $desde_CargosPDA = true;
		
		// CargarCargosErp($conex, "movhos", "cliame", $wprocod, $tipoCargoPDA, $numCargoInv, $linCargoInv );
	// }
// }

// return;
// return;

$sql2 = "
SELECT his, ing
  FROM tempJ 
 WHERE grabaSandraNury = 'No se pudo activar en unix'
 GROUP BY his, ing";
$res2 = mysql_query($sql2, $conex) or die("<b>ERROR EN QUERY MATRIX(sql2):</b><br>".mysql_error());
while($row2 = mysql_fetch_array($res2))
{
	echo "<br>HISTORIA:".$row2['his']."   INGRESO:".$row2['ing'];
	$sqlAct = "
	SELECT pacnum
	  FROM inpac 
	 WHERE pachis = '".$row2['his']."'	 
	";
	$resAct = odbc_exec($conexUnix, $sqlAct);
	if(odbc_fetch_row($resAct))
		echo "<br>ACTIVO:".$row2['his']."-".odbc_result($resAct,'pacnum');
	else
		echo "<br>INACTIVO:";
}




return;
?>