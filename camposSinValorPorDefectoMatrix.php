<?php

	include_once("conex.php");
	include_once("root/comun.php");
	mysql_select_db("matrix");
	$conex = obtenerConexionBD("matrix");
	

	function consultarValorPorDefecto($tipoDato)
	{
		$valorDefault = "''";
		switch ($tipoDato) {
			case 'int':
			case 'bigint':
			case 'double':
			case 'float':
			case 'decimal':
				$valorDefault = 0;
				break;
				
			case 'char':
			case 'varchar':
			case 'text':
			case 'mediumtext':
			case 'longtext':
				$valorDefault = "''";
				break;
				
			case 'date':
				$valorDefault = "'0000-00-00'";
				break;
				
			case 'time':
				$valorDefault = "'00:00:00'";
				break;
				
			case 'datetime':
				$valorDefault = "'0000-00-00 00:00:00'";
				break;
			case 'timestamp':
			
				$valorDefault = "'CURRENT_TIMESTAMP'";
				break;
		}
		
		return $valorDefault;
	}
	
	function consultarCamposNotNullSinValorPorDefecto()
	{
		global $conex;
		$queryCampos =   "SELECT TABLE_NAME,COLUMN_NAME,DATA_TYPE
							FROM information_schema.COLUMNS
						   WHERE table_schema = 'matrix' 
							 AND IS_NULLABLE='NO'
							 AND ISNULL(COLUMN_DEFAULT)
							 AND COLUMN_NAME!='id';";
					 
		$resCampos = mysql_query($queryCampos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCampos . " - " . mysql_error());		   
		$numCampos = mysql_num_rows($resCampos);
		
		$arrayCampos = array();
		if($numCampos>0)
		{
			while($rowCampos = mysql_fetch_array($resCampos))
			{
				$valorDefault = consultarValorPorDefecto($rowCampos['DATA_TYPE']);
				
				$queryUpdate = "ALTER TABLE ".$rowCampos['TABLE_NAME']." ALTER ".$rowCampos['COLUMN_NAME']." SET DEFAULT ".$valorDefault.";";
				echo $queryUpdate."<br>";
				
				$resUpdate = mysql_query($queryUpdate,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdate." - ".mysql_error());
				// break;
			}
		}			
	}
	
	function consultarCamposNull()
	{
		global $conex;
		
		$queryCampos =   "SELECT TABLE_NAME,COLUMN_NAME,DATA_TYPE,COLUMN_DEFAULT,COLUMN_TYPE
							FROM information_schema.COLUMNS
						   WHERE table_schema = 'matrix' 
							 AND IS_NULLABLE='YES'
							 AND COLUMN_NAME!='id';";
					 
		$resCampos = mysql_query($queryCampos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCampos . " - " . mysql_error());		   
		$numCampos = mysql_num_rows($resCampos);
		
		$arrayCampos = array();
		if($numCampos>0)
		{
			while($rowCampos = mysql_fetch_array($resCampos))
			{
				// var_dump($rowCampos['COLUMN_DEFAULT']);
				$cambiarValorPorDefecto = "";
				// if($rowCampos['COLUMN_DEFAULT']=="null" && $rowCampos['COLUMN_DEFAULT']=="NULL")
				if($rowCampos['COLUMN_DEFAULT']=="NULL")
				{
					$valorDefault = consultarValorPorDefecto($rowCampos['DATA_TYPE']);
					$cambiarValorPorDefecto = " DEFAULT ".$valorDefault;
				}
				else
				{
					// if($rowCampos['DATA_TYPE'] == "int" || $rowCampos['DATA_TYPE'] == "bigint" || $rowCampos['DATA_TYPE'] == "double" || $rowCampos['DATA_TYPE'] == "float")
					// {
						// $cambiarValorPorDefecto = " DEFAULT ".$rowCampos['COLUMN_DEFAULT'];
					// }
					// else
					// {
						// $cambiarValorPorDefecto = " DEFAULT '".$rowCampos['COLUMN_DEFAULT']."'";
					// }
					$cambiarValorPorDefecto = " DEFAULT ".$rowCampos['COLUMN_DEFAULT']."";
				}
				
				
				$queryUpdate = "ALTER TABLE ".$rowCampos['TABLE_NAME']." ALTER ".$rowCampos['COLUMN_NAME']." SET ".$cambiarValorPorDefecto.";";
				
				// $queryUpdate = "ALTER TABLE ".$rowCampos['TABLE_NAME']." MODIFY ".$rowCampos['COLUMN_NAME']." ".$rowCampos['COLUMN_TYPE']." NOT NULL ".$cambiarValorPorDefecto.";";
				echo $queryUpdate."<br>";
				
				if($rowCampos['TABLE_NAME']!="TABLE 4584")
				{
					$resUpdate = mysql_query($queryUpdate,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdate." - ".mysql_error());
					
				}
				
				// // break;
			}
		}			
	}

	// function consultarCamposNull()
	// {
		// global $conex;
		
		// $queryCampos =   "SELECT TABLE_NAME,COLUMN_NAME,DATA_TYPE,COLUMN_DEFAULT,COLUMN_TYPE
							// FROM information_schema.COLUMNS
						   // WHERE table_schema = 'matrix' 
							 // AND IS_NULLABLE='YES'
							 // AND COLUMN_NAME!='id';";
					 
		// $resCampos = mysql_query($queryCampos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCampos . " - " . mysql_error());		   
		// $numCampos = mysql_num_rows($resCampos);
		
		// $arrayCampos = array();
		// if($numCampos>0)
		// {
			// while($rowCampos = mysql_fetch_array($resCampos))
			// {
				// // var_dump($rowCampos['COLUMN_DEFAULT']);
				// $cambiarValorPorDefecto = "";
				// // if($rowCampos['COLUMN_DEFAULT']=="null" && $rowCampos['COLUMN_DEFAULT']=="NULL")
				// if($rowCampos['COLUMN_DEFAULT']=="NULL")
				// {
					// $valorDefault = consultarValorPorDefecto($rowCampos['DATA_TYPE']);
					// $cambiarValorPorDefecto = " DEFAULT ".$valorDefault;
				// }
				// else
				// {
					// // if($rowCampos['DATA_TYPE'] == "int" || $rowCampos['DATA_TYPE'] == "bigint" || $rowCampos['DATA_TYPE'] == "double" || $rowCampos['DATA_TYPE'] == "float")
					// // {
						// // $cambiarValorPorDefecto = " DEFAULT ".$rowCampos['COLUMN_DEFAULT'];
					// // }
					// // else
					// // {
						// // $cambiarValorPorDefecto = " DEFAULT '".$rowCampos['COLUMN_DEFAULT']."'";
					// // }
					// $cambiarValorPorDefecto = " DEFAULT ".$rowCampos['COLUMN_DEFAULT']."";
				// }
				
				
				// $queryUpdate = "ALTER TABLE ".$rowCampos['TABLE_NAME']." MODIFY ".$rowCampos['COLUMN_NAME']." ".$rowCampos['COLUMN_TYPE']." NOT NULL ".$cambiarValorPorDefecto.";";
				// echo $queryUpdate."<br>";
				
				// if($rowCampos['TABLE_NAME']!="TABLE 4584")
				// {
					// // $resUpdate = mysql_query($queryUpdate,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdate." - ".mysql_error());
					
				// }
				
				// // // break;
			// }
		// }			
	// }



	?>
	<html>
	<head>
	  <title>...</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
		

<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY>
	<?php
	
	consultarCamposNotNullSinValorPorDefecto();
	echo "<br>-------------------------<br>";
	consultarCamposNull();
	
	?>
	</BODY>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php

?>
