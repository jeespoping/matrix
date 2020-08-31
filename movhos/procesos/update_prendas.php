<?php
include_once("conex.php");

	include_once("root/comun.php");

	$conex = obtenerConexionBD("matrix");

	for($i=2;$i<10;$i++)
	{
		// Consulta todas las opciones de movhos
		$query = "    SELECT CONCAT( movfec, movron, movcco, movpre ), movfec, movron, movcco, movpre
						FROM movhos_000105
					   WHERE movron >= 16
					   GROUP BY 1 
					  HAVING COUNT( 1 ) = ".$i." ";
		$result = mysql_query($query,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());

		// Comienza recorrido de prendas
		while ($resulta = mysql_fetch_array($result))
		{ 

			$query = "    SELECT movfec, movron, movcco, movpre, movcan, id
							FROM movhos_000105
						   WHERE movfec = '".$resulta['movfec']."'
						     AND movron = '".$resulta['movron']."'
						     AND movcco = '".$resulta['movcco']."'
						     AND movpre = '".$resulta['movpre']."'
						   ";
			$res = mysql_query($query,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		
			$cont=1;
			while ( ( $resultb = mysql_fetch_array($res) ) && $cont < $i )
			{ 
				// Query para saber si tiene submenus asociados y consultar estos submenus
				$qupd = "  UPDATE movhos_000103 
							  SET Preedi = Preedi + ".$resultb['movcan']."
							WHERE Precod = '".$resultb['movpre']."' ";
				$resupd = mysql_query($qupd,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qupd . " - " . mysql_error());
				echo $qupd."<br>";
				

				// Query para saber si tiene submenus asociados y consultar estos submenus
				$qdel = "  DELETE 
							 FROM movhos_000105 
							WHERE id = ".$resultb['id']."  ";
				$resdel = mysql_query($qdel,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

				echo $qdel."<br>";
				
				// Imprime la prenda actual 
				echo "$i registros: ".$resultb['movpre']." - ".$resultb['movcan']." - ".$resultb['id']." <br><br>";
			
				$cont++;
			}
		} 
	}
	
?>