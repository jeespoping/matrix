<?php
include_once("conex.php");
/*
 ************************* KRON HISTORIA E INGRESO PACIENTES CIRUGIA **********************************
 ****************************** DESCRIPCIÓN ***************************************************
 * Actualiza la historia y el ingreso de los pacientes de cirugia en la tabla tcx_000011
 * Creado por: Jonatan Lopez
 * Fecha: 2014-27-06
 *************************************************************************************************
 Modificaciones
 12 Julio de 2016 Jonatan: Se modifica el kron para que tenga en cuenta la hora de inicio de la cirugia en comparacion con la fecha y hora de admisiones, con estos datos
							actualiza la historia e ingreso en la tabla, se tienen en cuenta admisiones posteriores a la cirugia y se actualizan solo si la historia se 
							encuentra en cero en la tabla tcx_000011.
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

	//Funcion que actualiza la historia e ingreso de la tabla tcx_000011 de acuerdo a la tabla root_000037
	function actualizar_historia_ingreso()
	{
		
		global $conex;
		global $wemp_pmla;
		$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		
		$fecha = date('Y-m-d');		
		$wupdate = 0;
				
		$query = " SELECT turdoc, turtdo, turtur, UNIX_TIMESTAMP(CONCAT (turfec, ' ', turhin )) as fecha_hora_cir "
				."   FROM tcx_000011 "
				."	WHERE turfec = '".$fecha."'";
		$res = mysql_query($query,$conex);
		
		$array_pacientes = array();
	
		while($rowa = mysql_fetch_array($res))
		{
		
			//Se verifica si la cedula ya se encuentre en el arreglo, si no esta lo agrega.
			if(!array_key_exists($rowa['turtdo']."_".$rowa['turdoc']."_".$rowa['turtur'], $array_pacientes))
			{
				$array_pacientes[$rowa['turtdo']."_".$rowa['turdoc']."_".$rowa['turtur']] = $rowa;
			}
			
		}
		// echo "<pre>";
		// print_r($array_pacientes);
		// echo "</pre>";
		
		foreach($array_pacientes as $key => $value){
			
			
			$datos_paciente = explode("_",$key);
			
			$tipo_doc = $datos_paciente[0];
			$cedula = $datos_paciente[1];
			$turno = $datos_paciente[2];
			$historia = $value['turhis'];
			$fecha_hora_cir = $value['fecha_hora_cir'];
			
			//Busco la historia e ingreso en la tabla root_000037 y movhos_000018.
			$query1 = " SELECT orihis, oriing, UNIX_TIMESTAMP (CONCAT ( B.Fecha_data, ' ', B.Hora_data )) as fecha_admision "
					 ."   FROM root_000037 A, ".$wmovhos."_000018 B "
					 ."	 WHERE oriced = '".$cedula."'"
					 ."	   AND oritid = '".$tipo_doc."'"
					 ."	   AND oriori = '".$wemp_pmla."'"
					 ."	   AND orihis = ubihis "
					 ."    AND oriing = oriing "
					 ."	   AND ubiald = 'off' ";
			//echo $query1;
			$res1 = mysql_query($query1, $conex);
			$row2 = mysql_fetch_array($res1);
			
			$fecha_admision = $row2['fecha_admision']; 
		
			if($fecha_admision <= $fecha_hora_cir and $fecha_admision != ''){
			
				if($fecha_admision > 0){
					
				//Si encuetra la historia hace la actualizacion.
				$query2 = " UPDATE tcx_000011 "
						."	   SET Turhis = '".$row2['orihis']."', Turnin = '".$row2['oriing']."'"
						."	 WHERE Turdoc = '".$cedula."'"
						."	   AND Turfec = '".$fecha."'"
						."	   AND Turtdo = '".$tipo_doc."'"
						." 	   AND Turtur = '".$turno."'";
				$res1 = mysql_query($query2, $conex);			
				
				//Si hay registros
				if( mysql_affected_rows() > 0 ){
					 
					 echo $row2['orihis']."-".$row2['oriing']."<br>";
					 
					 $wupdate++;
					 
					 }
				}
				
			}else{
				
				if($historia == '0'){
					
					
				//Si encuetra la historia hace la actualizacion.					
				$query2 = " UPDATE tcx_000011 "
						."	   SET Turhis = '".$row2['orihis']."', Turnin = '".$row2['oriing']."'"
						."	 WHERE Turdoc = '".$cedula."'"
						."	   AND Turfec = '".$fecha."'"
						."	   AND Turtdo = '".$tipo_doc."'"
						." 	   AND Turtur = '".$turno."'";
				$res1 = mysql_query($query2, $conex);
				
				//Si hay registros
				if( mysql_affected_rows() > 0 ){
					 
					 echo $row2['orihis']."-".$row2['oriing']."<br>";
					 
					 $wupdate++;
					 
					 }
				}
			}
		
		}				
		
		echo "Se actualizaron : ".$wupdate." cedulas en tcx_000011";

	}
	
	/**************************************************************************************************/
		
	actualizar_historia_ingreso();
	
		
?>
