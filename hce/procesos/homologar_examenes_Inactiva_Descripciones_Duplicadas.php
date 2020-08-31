<?php
include_once("conex.php");

session_start();

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");
      
include_once("root/comun.php");


$conex = obtenerConexionBD("matrix");
$wactualiz="2014-02-18";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce" );

mysql_select_db("matrix") or die("No se selecciono la base de datos");  

$q_table = "CREATE TABLE IF NOT EXISTS`hce_aux_homologados` (
			  `Medico` varchar(8) NOT NULL,
			  `Fecha_data` date NOT NULL,
			  `Hora_data` time NOT NULL,
			  `codigo` varchar(80) NOT NULL,
			  `tiposervicio` varchar(80) NOT NULL,
			  `repetido` varchar(80) NOT NULL,
			  `descripcion` varchar(200) NOT NULL,
			  `principal` char(3) NOT NULL,
			  `Seguridad` varchar(80) NOT NULL,
			  `id` bigint(20) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$res_table = mysql_query($q_table,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_table." - ".mysql_error());


$q = "TRUNCATE TABLE hce_aux_homologados";
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	


$q =  " SELECT *
	      FROM ".$wbasedatohce."_000017
	     WHERE nuevo = 'on'
	  ORDER BY Descripcion";
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	

$array_proced_nuevos = array();

while($row = mysql_fetch_assoc($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists(trim($row['Descripcion']), $array_proced_nuevos))
        {
            $array_proced_nuevos[trim($row['Descripcion'])] = $row;
			
			$q = " INSERT INTO hce_aux_homologados (Medico, Fecha_data, Hora_data, codigo, tiposervicio, repetido, descripcion, principal, Seguridad     ) "
						  ."                     VALUES ('".$wbasedatohce."','".date("Y-m-d")."','".date("h-m-i")."','".$row['Codigo']."' , '".$row['Tipoestudio']."', '".$row['Codigo']."', '".$row['Descripcion']."', 'on', 'C-hce')";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());			
			
        }else{
			
			$q_aux =  " SELECT codigo, tiposervicio
						  FROM hce_aux_homologados
						 WHERE descripcion = '".$row['Descripcion']."'
						   AND principal = 'on'";
			$res_aux = mysql_query($q_aux,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_aux." - ".mysql_error());
			$row_aux = mysql_fetch_array($res_aux);
			$codigo = $row_aux['codigo'];	
			$tiposervicio = $row_aux['tiposervicio'];	
			
			$q = " INSERT INTO hce_aux_homologados(Medico, Fecha_data, Hora_data, codigo, tiposervicio, repetido, descripcion, Seguridad     ) "
						  ."                     VALUES ('".$wbasedatohce."','".date("Y-m-d")."','".date("h-m-i")."','".$codigo."' , '".$tiposervicio."', '".$row['Codigo']."','".$row['Descripcion']."', 'C-hceotro')";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			
			
			$q_del = "UPDATE ".$wbasedatohce."_000017
						 SET Estado = 'off'
					   WHERE Codigo = '".$row['Codigo']."'";
			$res_del = mysql_query($q_del, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_del . " - " . mysql_error());
			
		
		}
		
    }

//El mismo proceso para la tabla hce_000047	
	
$q =  " SELECT *
	      FROM ".$wbasedatohce."_000047
	     WHERE nuevo = 'off'
	  ORDER BY Descripcion";
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	

$array_proced_nuevos = array();

while($row = mysql_fetch_assoc($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists(trim($row['Descripcion']), $array_proced_nuevos))
        {
            $array_proced_nuevos[trim($row['Descripcion'])] = $row;
			
			$q = " INSERT INTO hce_aux_homologados (Medico, Fecha_data, Hora_data, codigo, tiposervicio, repetido, descripcion, principal, Seguridad     ) "
						  ."                     VALUES ('".$wbasedatohce."','".date("Y-m-d")."','".date("h-m-i")."','".$row['Codigo']."' , '".$row['Tipoestudio']."', '".$row['Codigo']."', '".$row['Descripcion']."', 'on', 'C-hce')";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());			
			
        }else{
			
			$q_aux =  " SELECT codigo, tiposervicio
						  FROM hce_aux_homologados
						 WHERE descripcion = '".$row['Descripcion']."'
						   AND principal = 'on'";
			$res_aux = mysql_query($q_aux,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_aux." - ".mysql_error());
			$row_aux = mysql_fetch_array($res_aux);
			$codigo = $row_aux['codigo'];	
			$tiposervicio = $row_aux['tiposervicio'];	
			
			$q = " INSERT INTO hce_aux_homologados(Medico, Fecha_data, Hora_data, codigo, tiposervicio, repetido, descripcion, Seguridad     ) "
						  ."                     VALUES ('".$wbasedatohce."','".date("Y-m-d")."','".date("h-m-i")."','".$codigo."' , '".$tiposervicio."', '".$row['Codigo']."','".$row['Descripcion']."', 'C-hceotro')";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			
			
			$q_del = "UPDATE ".$wbasedatohce."_000047
						 SET Estado = 'off'
					   WHERE Codigo = '".$row['Codigo']."'";
			$res_del = mysql_query($q_del, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_del . " - " . mysql_error());
			
		
		}
		
    }	
	
	
// echo "<pre>";
// print_r($array_proced_nuevos);
// echo "</pre>";
	
// $q =  " SELECT *
	      // FROM hce_aux_homologados 
		  // WHERE principal = ''";
// $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

// while($row = mysql_fetch_array($res)){
	
	
	// $upd = "UPDATE ".$wbasedatohce."_000027, ".$wbasedatohce."_000028 
			   // SET ordtor = '".$row['tiposervicio']."', 
			       // dettor = '".$row['tiposervicio']."', 
				   // detcod = '".$row['codigo']."'
			 // WHERE Ordtor = Dettor
			   // AND Ordnro = Detnro
			   // AND Detcod = '".$row['repetido']."'";
	// $res_upd = mysql_query($upd, $conex);
	 
	// echo $upd."<br>";
	
	
// }


// $q_del = "DELETE FROM ".$wbasedatohce."_000017			 
		   // WHERE estado = 'off'";
// $res_del = mysql_query($q_del, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_del . " - " . mysql_error());

// $q_del = "DELETE FROM ".$wbasedatohce."_000047		 
		   // WHERE estado = 'off'";
// $res_del = mysql_query($q_del, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_del . " - " . mysql_error());



?>