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
$wactualiz="2014-03-30";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce" );

mysql_select_db("matrix") or die("No se selecciono la base de datos");


$q =  " SELECT ordhis, ording, detnro, detcod, detite, detfec, ordtor
		  FROM hce_000027, hce_000028
		 WHERE Ordtor = Dettor 
		   AND Ordnro = Detnro 
		   AND Detest = 'on'
		   AND detcod not in (select codigo from hce_000017) 
 GROUP BY detcod order by hce_000028.Detfec ";
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	

$array_proced_nuevos = array();
$array_datos = array();
$array_nuevo = array();

while($row = mysql_fetch_assoc($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists(trim($row['detcod']), $array_proced_nuevos))
        {            
			
			$valor_a_buscar = $row['detnro'].",".$row['detite'];
			
			$q1 =  " SELECT Kaudes
					  FROM movhos_000055
					 WHERE Kauhis = '".$row['ordhis']."' 
					   AND Kauing = '".$row['ording']."'
					   AND Kaudes LIKE '%".$valor_a_buscar."%'
					   AND Kaumen = 'Examen creado' LIMIT 1";
			$res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
			
			while($row1 = mysql_fetch_assoc($res1))
			{
				$descripcion1 = explode("N:", $row1['Kaudes']);
				$descripcion2 = explode(",", $descripcion1[1]);
				$descripcion3 = $descripcion2[3];				
				
				$array_nuevo[] = array('codigo'=>$row['detcod'],'descripcion'=>$descripcion3,'Tipoestudio'=>$row['ordtor'],'cod_cups'=>$row['detcod'],'consecutivo'=>$row['detnro']);
				
				
			}			
			
        }
		
    }

echo "Se registraron ".count($array_nuevo)." examenes.";


foreach($array_nuevo as $key => $value)	{
	
	
	$q = " INSERT INTO hce_000017(Medico, Fecha_data, Hora_data, Codigo, Descripcion, Servicio, Tipoestudio, Codcups, Estado, Seguridad     ) "
        ."  VALUES ('hce','2015-03-27','00:00:01','".$value['codigo']."' , '".$value['descripcion']."', 'H','".$value['Tipoestudio']."','".$value['codigo']."', 'off' ,'C-hce')";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	
}



?>