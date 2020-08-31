<?php
include_once("conex.php");
function buscarSiEstaSuspendidoInc2($whis, $wing, $wart, $whora, $wfecha_actual, $idOriginal )
{
 global $conex;
 global $wbasedato;	
 
 //Convierto la fecha y hora a tiempo Unix
 $tiempoUnix = strtotime( "$wfecha_actual 12:00:00" );
 $fecha_anterior = date( "Y-m-d", $tiempoUnix-24*3600 );	//Calculo un dia anterior
 	 
 $q = " SELECT COUNT(*)  "
	 ."   FROM ".$wbasedato."_000055 A "
	 ."  WHERE kauhis  = '".$whis."'"
	 ."    AND kauing  = '".$wing."'"
	 //."    AND kaufec BETWEEN '".$fecha_anterior."' AND '".$wfecha_actual."'"
	 ."    AND TRIM( kaudes )  = '".$wart."'"
	 ."    AND kauido = '".$idOriginal."' "
	 ."    AND kaumen  = 'Articulo suspendido' "
	 ."    AND UNIX_TIMESTAMP( CONCAT( kaufec,' ', hora_data ) ) < '".($tiempoUnix-0*3600)."'";   //Si la hora de suspensión esta entre la RONDA anterior y la actual se puede aplicar (No se toma como suspendido)
	 // ."    AND UNIX_TIMESTAMP( CONCAT( kaufec,' ', hora_data ) ) BETWEEN '".($tiempoUnix-2*3600)."' AND '".($tiempoUnix+2*3600)."'";   //Si la hora de suspensión esta entre la RONDA anterior y la actual se puede aplicar (No se toma como suspendido)
	 
 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
 $row = mysql_fetch_array($res); 
 
 if ($row[0] > 0)  
	return "on";  //Indica que el articulo fue suspendido hace menos de dos horas, es decir que se puede aplicar, asi este suspendido
   else
	  return "off"; //Indica que fue Suspendido hace mas de dos horas
}

function consultarAplicadoJustificado($conex, $wbasedato, $whis, $wing, $wart, $whora_par_actual, $wfecha_actual, &$wjustificacion, $wido){

	//===============================================================
	//Paso la hora a formato de 12 horas
	//===============================================================
	//Dejo el formato a 24 horas con meridiano (AM - PM)
	$whora_a_buscar = gmdate( "H:00 - A", $whora_par_actual*3600 );
	//===============================================================

	//===============================================================
	$q = " SELECT COUNT(*) "
	." FROM ".$wbasedato."_000015 "
	." WHERE aplhis = '".$whis."'"
	." AND apling = '".$wing."'"
	." AND aplfec = '".$wfecha_actual."'"
	." AND aplron like '".trim($whora_a_buscar)."'"
	." AND aplart = '".$wart."'"
	." AND aplest = 'on' "
	." AND aplido = ".$wido;

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	if ($row[0] > 0){
		return true;
	}
	else //Si NO tiene aplicacion busco si tiene Justificacion de porque NO se aplico
	{
	//===============================================================
	//Busco si tiene Justificacion
	//===============================================================
	$q = " SELECT jusjus "
	." FROM ".$wbasedato."_000113 "
	." WHERE jushis = '".$whis."'"
	." AND jusing = '".$wing."'"
	." AND jusfec = '".$wfecha_actual."'"
	." AND jusron LIKE '".trim($whora_a_buscar)."'"
	." AND jusart = '".$wart."'"
	." AND jusido = ".$wido;

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	if( $num > 0 ){
		$row = mysql_fetch_array($res);
		$wjustificacion = $row[0];
	}
	else{
		$wjustificacion="";
	}

	return false; //Indica que no esta aplicado
	}
}


	include_once("root/comun.php");
	include_once("movhos/movhos.inc.php");
	
	$wbasedato =  "movhos";

	$hab = array();
	
	$fechorInic = strtotime( "2012-12-25 14:00:00" );
	$fechorFin = $fechorInic + 3*2*3600;
	
	for( $i = $fechorInic; $i <= $fechorFin; $i += 2*3600 )
	{
		$ronda = date( "H",$i);
		$fecha = date( "Y-m-d",$i);
		
		$q_aplicar = "  SELECT Kadhis, Kading, Kadart, Kadido, Kadcfr, Kadufr, Kadcnd, Kadfin, Kadhin, Perequ, Ubisac, Ubihac, Kadcma
						 FROM movhos_000054, movhos_000043, movhos_000018
						WHERE Kadper = Percod 
						  AND Kadfec = '".$fecha."'
						  AND Kadhis = Ubihis
						  AND Kading = Ubiing
						  AND Ubiald != 'on'
						  AND TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('".$fecha."',' ','".$ronda."',':00:00')) >= 0
						  AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('".$fecha."',' ','".$ronda."',':00:00')),perequ) = 0
						  ORDER BY Ubihac
					";
		
		$res_aplicar = mysql_query($q_aplicar,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_aplicar." - ".mysql_error());
		$num__aplicar = mysql_num_rows( $res_aplicar );
		
		if( $num__aplicar > 0 ){
			
			while( $rows = mysql_fetch_array( $res_aplicar ) ){
				
				if(!esANecesidadInc( $rows[ 'Kadcnd' ] ))
				{
					$estaApl = consultarAplicadoJustificado($conex, $wbasedato, $rows[ 'Kadhis' ], $rows[ 'Kading' ], $rows[ 'Kadart' ], $ronda, $fecha, $wjustificacion, $rows[ 'Kadido' ] );
					
					if( !$estaApl ){
					
						$estaSus = 'off';
					
						if( $rows[ 'Kadsus' ] == 'on' ){
							$estaSus = buscarSiEstaSuspendidoInc2( $rows[ 'Kadhis' ], $rows[ 'Kading' ], $rows[ 'Kadart' ], $ronda, $fecha, $rows[ 'Kadido' ] );								
						}
						
						if( $estaSus == 'off' ){							
							
							@$hab[  $rows[ 'Ubihac' ]][ $rows[ 'Kadhis' ]."-".$rows[ 'Kading' ]."-".$rows[ 'Kadart' ] ][ 'Ubisac' ] = $rows[ 'Ubisac' ];
							@$hab[  $rows[ 'Ubihac' ] ][ $rows[ 'Kadhis' ]."-".$rows[ 'Kading' ]."-".$rows[ 'Kadart' ] ][ 'Ubihac' ] = $rows[ 'Ubihac' ];
							@$hab[  $rows[ 'Ubihac' ] ][ $rows[ 'Kadhis' ]."-".$rows[ 'Kading' ]."-".$rows[ 'Kadart' ] ][ 'Kadhis' ] = $rows[ 'Kadhis' ];
							@$hab[  $rows[ 'Ubihac' ] ][ $rows[ 'Kadhis' ]."-".$rows[ 'Kading' ]."-".$rows[ 'Kadart' ] ][ 'Kading' ] = $rows[ 'Kading' ];
							@$hab[  $rows[ 'Ubihac' ] ][ $rows[ 'Kadhis' ]."-".$rows[ 'Kading' ]."-".$rows[ 'Kadart' ] ][ 'Kadart' ] = $rows[ 'Kadart' ];
							@$hab[  $rows[ 'Ubihac' ] ][ $rows[ 'Kadhis' ]."-".$rows[ 'Kading' ]."-".$rows[ 'Kadart' ] ][ 'Kadcfr' ] = $rows[ 'Kadcfr' ];
							@$hab[  $rows[ 'Ubihac' ] ][ $rows[ 'Kadhis' ]."-".$rows[ 'Kading' ]."-".$rows[ 'Kadart' ] ][ 'Kadufr' ] = $rows[ 'Kadufr' ];
							@$hab[  $rows[ 'Ubihac' ] ][ $rows[ 'Kadhis' ]."-".$rows[ 'Kading' ]."-".$rows[ 'Kadart' ] ][ 'can' ] += ($rows[ 'Kadcfr' ] );
						}
					}
				}
			}	
		}
		
	}
	
	echo "<table align='center' width='40%'>
			<tr class='Encabezadotabla'>
				<td>Cco</td>
				<td >Hab</td>
				<td >Historia</td>
				<td >Articulo</td>								
				<td>Can</td>
			</tr>
			";
	
	ksort($hab);
	//echo '<pre>';print_r($hab);echo '</pre>';
	foreach( $hab as $keyhab => $valuehab ){
	
		foreach( $valuehab as $key => $value ){
			
			echo "<tr class='Fila2'>";
								
			echo "<td>";
			echo $value[ 'Ubisac' ];
			echo "</td>";
			echo "<td>";
			echo $value[ 'Ubihac' ];
			echo "</td>";
			echo "<td>";
			echo $value[ 'Kadhis' ]." - ".$value[ 'Kading' ];
			echo "</td>";
			echo "<td>";
			echo $value[ 'Kadart' ];
			echo "</td>";		
			
			
			echo "<td>";
			echo $value[ 'can' ]." ".$value[ 'Kadufr' ];
			echo "</td>";
			
			echo "</tr>";			
		}
	}
	
	echo "</table>";
?>
