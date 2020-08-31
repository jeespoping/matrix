<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Script que genera un alter table para la creación del indice por historia e ingreso de las tablas Matrix que no 
// 						los tienen, se muestran solo las tablas de los grupos que estén marcados con Gruest='on' en la tabla grupostablasmatrix.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2018-09-04
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2018-09-04';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//                
//
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
	// $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, '');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarTablas()
	{
		global $conex;
		
		$queryGrupos = "SELECT Grudes 
						  FROM gruposTablasMatrix 
						 WHERE Gruest='on';";
						 
		$resGrupos = mysql_query($queryGrupos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryGrupos . " - " . mysql_error());		   
		$numGrupos = mysql_num_rows($resGrupos);
		
		$arrayTablasSinIndice = array();
		$arrayIndice = array();
		if($numGrupos>0)
		{
			while($rowGrupos = mysql_fetch_array($resGrupos))
			{
				$queryTablasHistoria = "SELECT TABLE_NAME as tabla,COLUMN_NAME as campoHistoria
										  FROM information_schema.COLUMNS
										 WHERE table_schema = 'matrix'
										   AND COLUMN_NAME LIKE '%his%'
										   AND TABLE_NAME LIKE '".$rowGrupos['Grudes']."%';";
									  
				$resTablasHistoria = mysql_query($queryTablasHistoria, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTablasHistoria . " - " . mysql_error());		   
				$numTablasHistoria = mysql_num_rows($resTablasHistoria);
				
				
				if($numTablasHistoria>0)
				{
					while($rowTablasHistoria = mysql_fetch_array($resTablasHistoria))
					{
						$queryTablasIngreso = "SELECT TABLE_NAME,COLUMN_NAME as campoIngreso
												 FROM information_schema.COLUMNS
												WHERE table_schema = 'matrix'
												  AND TABLE_NAME='".$rowTablasHistoria['tabla']."'
												  AND COLUMN_NAME LIKE '%ing%';";
						
						$resTablasIngreso = mysql_query($queryTablasIngreso, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTablasIngreso . " - " . mysql_error());		   
						$numTablasIngreso = mysql_num_rows($resTablasIngreso);
						
						$condicionIngreso = "";
						$campoIngreso = "";
						$indiceIngreso = "";
						if($numTablasIngreso>0)
						{
							$rowTablasIngreso = mysql_fetch_array($resTablasIngreso);
							
							$condicionIngreso = "OR (COLUMN_NAME='".$rowTablasIngreso['campoIngreso']."' AND SEQ_IN_INDEX=2)";
							$campoIngreso = ",".$rowTablasIngreso['campoIngreso'];
							$indiceIngreso = "ing";
						}
						
						$queryIndice = "SELECT INDEX_NAME
										  FROM information_schema.statistics
										 WHERE table_schema = 'matrix'
										   AND table_name='".$rowTablasHistoria['tabla']."'
										   AND ((COLUMN_NAME='".$rowTablasHistoria['campoHistoria']."' AND SEQ_IN_INDEX=1) 
												".$condicionIngreso.")
									  GROUP BY INDEX_NAME;";
											
						$resIndice = mysql_query($queryIndice, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryIndice . " - " . mysql_error());		   
						$numIndice = mysql_num_rows($resIndice);
						
						if($numIndice==0)
						{
							$queryCrearIndice = "ALTER TABLE ".$rowTablasHistoria['tabla']." ADD INDEX his".$indiceIngreso."_idx (".$rowTablasHistoria['campoHistoria'].$campoIngreso.");";
							// echo $queryCrearIndice."<br>";
							
							$cantidadRegistros = consultarCantidadRegistros($rowTablasHistoria['tabla']);
							$arrayTablasSinIndice[$rowTablasHistoria['tabla']] = $cantidadRegistros;
							$arrayIndice[$rowTablasHistoria['tabla']] = $queryCrearIndice;
						}
					}
				}
				
			}
		}			
		  
		echo "<br>";
		echo "Cantidad de tablas: ".count($arrayTablasSinIndice);
		echo "<br><br>";
		
		arsort($arrayTablasSinIndice);
	
		foreach($arrayTablasSinIndice as $key => $value)
		{
			echo $arrayIndice[$key]."<br>";
		}
		
		echo "<br>";
		
		foreach($arrayTablasSinIndice as $key => $value)
		{
			echo "[".$key."] => ".number_format($value, 0, ',', '.')."<br>";
		}
		
		// echo "<pre>".print_r($arrayTablasSinIndice,true)."</pre>";
		
	}
	
	function consultarCantidadRegistros($tabla)
	{
		global $conex;
		
		$queryRegistros = "SELECT TABLE_ROWS 
							 FROM information_schema.`TABLES` 
							WHERE TABLE_SCHEMA='matrix' 
							  AND TABLE_NAME='".$tabla."';";
		
		$resRegistros = mysql_query($queryRegistros, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryRegistros . " - " . mysql_error());		   
		$numRegistros = mysql_num_rows($resRegistros);
		
		$cantidadRegistros = "";
		if($numRegistros>0)
		{
			$rowRegistros = mysql_fetch_array($resRegistros);
			$cantidadRegistros = $rowRegistros['TABLE_ROWS'];
			// $cantidadRegistros = number_format($rowRegistros['TABLE_ROWS'], 0, ',', '.');
		}
		
		return $cantidadRegistros;
	}
	
//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		// case '':
		// {	
			// break;
			// return;
		// }
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{
	?>
	<html>
	<head>
	  <title>TABLAS SIN INDICE POR HISTORIA E INGRESO</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
		
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	// function ()
	// {
	
	// }
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
	
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY>
	<?php
	// -->	ENCABEZADO
	encabezado("Tablas sin indice por historia e ingreso", $wactualiz, 'clinica');
	
	consultarTablas();
	
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
}

}//Fin de session
?>
