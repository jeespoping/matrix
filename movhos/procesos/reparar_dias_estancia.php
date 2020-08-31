<?php
include_once("conex.php");

include_once("root/comun.php");
include_once("movhos/movhos.inc.php");

function interval_date($init,$finish)
{
    //formateamos las fechas a segundos tipo 1374998435
    $diferencia = strtotime($finish) - strtotime($init);
 
    //comprobamos el tiempo que ha pasado en segundos entre las dos fechas
    //floor devuelve el número entero anterior, si es 5.7 devuelve 5
    if($diferencia < 60){
        $tiempo = ($diferencia);
    }else if($diferencia > 60 && $diferencia < 3600){
        $tiempo = $diferencia/60;
    }else if($diferencia > 3600 && $diferencia < 86400){
        $tiempo = $diferencia/3600;
    }else if($diferencia > 86400 && $diferencia < 2592000){
        $tiempo = $diferencia/86400;
    }else if($diferencia > 2592000 && $diferencia < 31104000){
        $tiempo = $diferencia/2592000;
    }else if($diferencia > 31104000){
        $tiempo = $diferencia/31104000;
    }else{
        $tiempo = "Error";
    }
	
	if($tiempo < 0){
	$tiempo = 0;
	}
	
    return round($tiempo,2);
}

$sql = "SELECT id, historia_clinica, num_ingreso, fecha_egre_serv, Hora_egr_serv
		FROM movhos_000033
		WHERE Tipo_egre_serv in (SELECT Ccocod
								FROM movhos_000011 
								WHERE Ccoest = 'on' AND Ccohos = 'on' AND ( Ccoing != 'on' OR Ccohib = 'on' )
								ORDER by 1)
			AND fecha_data >= '2014-01-01'
		order by Historia_clinica, Num_ingreso, concat(fecha_egre_serv, ' ', Hora_egr_serv)";
$result = mysql_query($sql, $conex);

$arr_consulta = array();

while ($row = mysql_fetch_array($result))
{
    $clave_his_ing = $row['historia_clinica'].'_'.$row['num_ingreso'];
    if(!array_key_exists($clave_his_ing, $arr_consulta))
    {
        $arr_consulta[$clave_his_ing] = array();
    }
    $arr_consulta[$clave_his_ing][$row['id']] = array("fecha_uno" => $row['fecha_egre_serv'], "hora_uno" => $row['Hora_egr_serv'], "fecha_dos" => '', "hora_dos" => '', "diferencia" => 0, "id_aux" => $row['id']);
}


$arr_copia = $arr_consulta;

$arr_completo = array();
foreach($arr_consulta as $his_ing => $arr_his_ing)
{
    $arr_siguiente = current($arr_copia[$his_ing]);
	
    foreach($arr_his_ing as $id => $arr_dato)
    {
        $arr_siguiente = next($arr_copia[$his_ing]);
		
        $arr_completo[$his_ing][$id] = $arr_dato;
        
		if(count($arr_siguiente) > 0)
        {

            $arr_completo[$his_ing][$id]['fecha_dos'] = $arr_siguiente['fecha_uno'];
            $arr_completo[$his_ing][$id]['hora_dos']  = $arr_siguiente['hora_uno'];
			$diferencia = interval_date($arr_completo[$his_ing][$id]['fecha_uno']." 00:00:00", $arr_siguiente['fecha_uno']." ".$arr_siguiente['hora_uno']);
            $arr_completo[$his_ing][$id]['diferencia'] = $diferencia;
            $arr_completo[$his_ing][$id]['id_aux'] = $arr_siguiente['id_aux'];
        }
    }

}


foreach($arr_completo as $key => $value){

	foreach($value as $id_registro => $datos){
		
		if($datos['id_aux'] != ""){
		
		$q =  " UPDATE movhos_000033 "
			. "    SET Dias_estan_serv = '".$datos['diferencia']."'"		
			. "  WHERE id      			= '".$datos['id_aux']."'";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	
	}
	
	}

}

$array_movhos33 = array();

$q = "SELECT *
		FROM movhos_000033  
	   WHERE Tipo_egre_serv in (SELECT Ccocod
								FROM movhos_000011 
								WHERE Ccoest = 'on' AND Ccohos = 'on' AND ( Ccoing != 'on' OR Ccohib = 'on' )
								ORDER by 1)
			
			AND servicio in (SELECT Ccocod
								FROM movhos_000011 
								WHERE Ccoest = 'on' AND Ccohos = 'on' AND ( Ccoing != 'on' OR Ccohib = 'on' )
								ORDER by 1)
		order by fecha_data";		     
$rs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

while($row_33 = mysql_fetch_array($rs)){
	//echo $row_33['Servicio']."<br>";
	if(!array_key_exists($row_33['Servicio'], $array_movhos33)){
	
		$array_movhos33[$row_33['Servicio']][$row_33['Fecha_egre_serv']] = array('dias'=>$row_33['Dias_estan_serv']);
	
	}else{
	
	
		$dias_estancia = $array_movhos33[$row_33['Servicio']][$row_33['Fecha_egre_serv']]['dias'] + $row_33['Dias_estan_serv'];
	
		$array_movhos33[$row_33['Servicio']][$row_33['Fecha_egre_serv']] = array('dias'=>$dias_estancia);
	
	}

}


$sql = "  SELECT Fecha_data, Cieser, Ciedes, Ciediam
			FROM movhos_000038";
$result = mysql_query($sql, $conex);

while($row_cco = mysql_fetch_array($result)){

	$diasTraslado = $array_movhos33[$row_cco['Cieser']][$row_cco['Fecha_data']]['dias'];
		
		
		if($diasTraslado > 0){
		$q =  " UPDATE movhos_000038 "
			. "    SET Ciedit = '".$diasTraslado."'"		
			. "  WHERE Cieser = '".$row_cco['Cieser']."'"
			."     and Fecha_data = '".$row_cco['Fecha_data']."'  ";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	
		$q =  " UPDATE movhos_000038 "
				. "    SET Ciedes = '".($diasTraslado+$row_cco['Ciediam'])."'"		
				. "  WHERE Cieser = '".$row_cco['Cieser']."'"
				."     and Fecha_data = '".$row_cco['Fecha_data']."'  ";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());	 
	
	
	}
}


?>
