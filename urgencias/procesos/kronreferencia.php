<?php
include_once("conex.php");
/*
 ************************* KRON HISTORIA E INGRESO PACIENTES REFERENCIA Y CONTRAREFERENCIA **********************************
 ****************************** DESCRIPCIÓN ***************************************************
 * Actualiza la historia y el ingreso de los pacientes de REFERENCIA Y CONTRAREFERENCIA en la tabla urgen_000003
 * Creado por: Gabriel Agudelo
 * Fecha: 2015-07-29
 *************************************************************************************************
 
*/

	include_once("root/comun.php");
    include_once("movhos/otros.php");
	
	/**************************************************************************************************
    * DECLARACION DE VARIABLES GLOBALES
    **************************************************************************************************/

	//Conexion base de datos Matrix
	$conex = obtenerConexionBD("matrix");

	//Declaración de variable para determinar la empresa
	if(!isset($wemp_pmla))
	{
		$wemp_pmla = '01';
	}
	
	/**************************************************************************************************
    * FUNCIONES
    **************************************************************************************************/

	//Funcion que actualiza la historia e ingreso de la tabla urgen_000003 de acuerdo a la tabla root_000037
	function actualizar_historia_ingreso()
	{
		
		global $conex;
		global $wemp_pmla;	
		
		$fecha = date('Y-m-d');	
        $fecha1 = strtotime ( '-1 day' , strtotime ( $fecha ) ) ;
		$fecha1 = date ( 'Y-m-d' , $fecha1 );
 
		$wupdate = 0;
		
		$query = " SELECT Documento "
				."   FROM urgen_000003 "
				."	WHERE Fecha between '".$fecha1."' and '".$fecha."'";
		$res = mysql_query($query,$conex);
		
		$array_pacientes = array();
	
		while($rowa = mysql_fetch_array($res))
		{
		
			//Se verifica si el Documento ya se encuentre en el arreglo, si no esta lo agrega.
			if(!array_key_exists($rowa['Documento'], $array_pacientes))
			{
				$array_pacientes[$rowa['Documento']] = $rowa;
			}
			
		}
		
		foreach($array_pacientes as $key => $value){
			
			//Busco la historia e ingreso en la tabla root_000037.
			$query1 = " SELECT orihis, oriing "
					."   FROM root_000037 "
					."	WHERE oriced = '".$key."'"
					."	  AND oriori = '".$wemp_pmla."'";
			$res1 = mysql_query($query1, $conex);
			$row2 = mysql_fetch_array($res1);
			
			//Si encuetra la historia hace la actualizacion.
			if($row2['orihis'] != ""){
			
			//Actualizo el registro.
			$query2 = " UPDATE urgen_000003 "
					."	   SET Historia = '".$row2['orihis']."', Ingreso = '".$row2['oriing']."'"
					."	 WHERE Documento = '".$key."'"
					."	   AND (Fecha = '".$fecha1."' or Fecha = '".$fecha."')";
			$res1 = mysql_query($query2, $conex);
			
			
			//Si hay registros
			if( mysql_affected_rows() > 0 ){
				 
				 echo $row2['orihis']."-".$row2['oriing']."<br>";
				 
				 $wupdate++;
				 
				 }
			
			}
		
		}				
		
		echo "Se actualizaron : ".$wupdate." cedulas en urgen_000003";

	}
	
	/**************************************************************************************************/
		
	actualizar_historia_ingreso();
	
		
?>
