<?php
include_once("root/comun.php");
  
function Buscar_nombre_medico( $conex, $wemp_pmla, $c_medico ){
	
	$wmovhos=consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	
	$sql = "SELECT CONCAT(Medno1, ' ',  Medap1,' ',Medap2) AS nombres  
			  FROM ".$wmovhos."_000048 
			 WHERE meduma='".$c_medico."'";
	
	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	
	while($rows = mysql_fetch_array($res)){
		return $rows['nombres'];		
	}
}

function cargartabla( $conex, $wemp_pmla){
	
	$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	
	$sql = "SELECT ".$wmovhos."_000208.Ekxart AS codigo, ".$wmovhos."_000026.Artcom,".$wmovhos."_000054.Kadusu AS U_Ordena,".$wmovhos."_000208.Ekxjus AS J_orden,".$wmovhos."_000208.Ekxhis AS historia,".$wmovhos."_000208.Ekxing AS ingreso, ".$wmovhos."_000208.Ekxido AS ido 
			  FROM ".$wmovhos."_000208 
	    INNER JOIN ".$wmovhos."_000054 
				ON ".$wmovhos."_000208.Ekxhis=".$wmovhos."_000054.Kadhis 
			   AND ".$wmovhos."_000208.Ekxing=".$wmovhos."_000054.Kading 
			   AND ".$wmovhos."_000208.Ekxart=".$wmovhos."_000054.Kadart 
			   AND ".$wmovhos."_000208.Ekxfec=".$wmovhos."_000054.Kadfec
		INNER JOIN ".$wmovhos."_000026 
				ON ".$wmovhos."_000208.Ekxart=".$wmovhos."_000026.Artcod 
			 WHERE Ekxaut='off' 
			   AND Ekxfau='0000-00-00' 
			   AND Ekxfec='".date( "Y-m-d" )."'
			   AND Ekxhis NOT IN( SELECT Ekxhis 
									FROM ".$wmovhos."_000208 
								   WHERE ekxhis = kadhis
								     AND ekxing = kading
								     AND ekxart = kadart
									 AND ekxaut = 'on'
								   UNION
								  SELECT Ekxhis 
									FROM ".$wmovhos."_000209 
								   WHERE ekxhis = kadhis
								     AND ekxing = kading
								     AND ekxart = kadart
									 AND ekxaut = 'on' )
			 UNION 
			SELECT ".$wmovhos."_000209.Ekxart AS codigo, ".$wmovhos."_000026.Artcom,".$wmovhos."_000060.Kadusu AS U_Ordena,".$wmovhos."_000209.Ekxjus AS J_orden,".$wmovhos."_000209.Ekxhis AS historia,".$wmovhos."_000209.Ekxing AS ingreso, ".$wmovhos."_000209.Ekxido AS ido 
			  FROM ".$wmovhos."_000209 
	    INNER JOIN ".$wmovhos."_000060 
				ON ".$wmovhos."_000209.Ekxhis=".$wmovhos."_000060.Kadhis 
			   AND ".$wmovhos."_000209.Ekxing=".$wmovhos."_000060.Kading 
			   AND ".$wmovhos."_000209.Ekxart=".$wmovhos."_000060.Kadart 
			   AND ".$wmovhos."_000209.Ekxfec=".$wmovhos."_000060.Kadfec
		INNER JOIN ".$wmovhos."_000026 
				ON ".$wmovhos."_000209.Ekxart=".$wmovhos."_000026.Artcod 
			 WHERE Ekxaut='off' 
			   AND Ekxfau='0000-00-00' 
			   AND Ekxfec='".date( "Y-m-d" )."'
			   AND Ekxhis NOT IN( SELECT Ekxhis 
									FROM ".$wmovhos."_000208 
								   WHERE ekxhis = kadhis
								     AND ekxing = kading
								     AND ekxart = kadart
									 AND ekxaut = 'on'
								   UNION
								  SELECT Ekxhis 
									FROM ".$wmovhos."_000209 
								   WHERE ekxhis = kadhis
								     AND ekxing = kading
								     AND ekxart = kadart
									 AND ekxaut = 'on' )
			";
			
	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	$cons = 0;
	
	while($rows = mysql_fetch_array($res))
	{
		$paciente = consultarInfoPacientePorHistoria( $conex, $rows[4], $wemp_pmla );
		
		$autorizaciones[] = array( 
					"consecutivo"			=> $cons++,
					"codigo"				=> $rows['codigo'],
					"usuario_ordena" 		=> $rows['U_Ordena'],
					"justificacion_ordena"	=> $rows['J_orden'],
					"nombre_medicamento"	=> $rows['Artcom'],
					"historia"				=> $rows['historia'],
					"ingreso"				=> $rows['ingreso'],
					"identificador"			=> $rows['ido'],
					"nombre_medico"			=> Buscar_nombre_medico( $conex, $wemp_pmla, $rows['U_Ordena'] ),
					"nombre_paciente"		=> $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2,
				);
	}

	return $autorizaciones;
}

function actualizar($conex,$autorizacion,$wemp_pmla){
	
	list($id, $user) = explode("-",$_SESSION['user']);
	
	$wmovhos=consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	
	$sql = " UPDATE ".$wmovhos."_000208
			    SET ".$wmovhos."_000208.Ekxaut='".$autorizacion['autoriza']."',
					".$wmovhos."_000208.Ekxfau='".date(' Y-m-j')."',
					".$wmovhos."_000208.Ekxhau='".date("H:i:s")."',
					".$wmovhos."_000208.Ekxmau='".$user."',
					".$wmovhos."_000208.Ekxjau='".$autorizacion['justificacion_medico_autoriza']."'
			  WHERE ".$wmovhos."_000208.Ekxhis='".$autorizacion['historia']."' 
			    AND ".$wmovhos."_000208.Ekxing='".$autorizacion['ingreso']."' 
				AND ".$wmovhos."_000208.Ekxart='".$autorizacion['codigo_medicamento']."' 
				AND ".$wmovhos."_000208.Ekxfec='".date( "Y-m-d" )."'";
			
	////////////////////////////////////////////////////
	$sql2 = " UPDATE ".$wmovhos."_000209
				 SET ".$wmovhos."_000209.Ekxaut='".$autorizacion['autoriza']."',
					 ".$wmovhos."_000209.Ekxfau='".date(' Y-m-j')."',
					 ".$wmovhos."_000209.Ekxhau='".date("H:i:s")."',
					 ".$wmovhos."_000209.Ekxmau='".$user."',
					 ".$wmovhos."_000209.Ekxjau='".$autorizacion['justificacion_medico_autoriza']."'
			   WHERE ".$wmovhos."_000209.Ekxhis='".$autorizacion['historia']."' 
				 AND ".$wmovhos."_000209.Ekxing='".$autorizacion['ingreso']."' 
				 AND ".$wmovhos."_000209.Ekxart='".$autorizacion['codigo_medicamento']."' 
				 AND ".$wmovhos."_000209.Ekxfec='".date( "Y-m-d" )."'";

	$res = mysql_query( $sql, $conex ) or die ( "Error: ".mysql_errno()." - Al actualizar autorizacion - " . mysql_error() );
	$res = mysql_query( $sql2, $conex ) or die ( "Error: ".mysql_errno()." - Al actualizar autorizacion - " . mysql_error() );

	registrarlog( $conex, $autorizacion, $wemp_pmla, $user );
}

function registrarlog($conex,$autorizacion,$wemp_pmla,$user){
		
		$wmovhos=consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
		
		$mensaje="";
		
		$fecha=date(' Y-m-j');
		$hora=date("H:i:s");
		
		if($autorizacion['autoriza']=="off"){
			$mensaje="Medicamento no autorizado";
		}
		else{
			$mensaje="Medicamento autorizado";
		}
		
		$sql ="INSERT INTO 
				".$wmovhos."_000055(    Medico     , Fecha_data  ,  Hora_data ,       Kauhis                   ,  Kauing                       ,  Kaufec     ,           Kaudes                                        ,       Kaumen          , Kauido ,   Seguridad   ) 
							VALUES ( '".$wmovhos."', '".$fecha."', '".$hora."', '".$autorizacion['historia']."', '".$autorizacion['ingreso']."', '".$fecha."', '".$autorizacion['codigo_medicamento']." - ".$mensaje."', 'Proceso autorizacion',   '0'  , 'C-".$user."' )
			   "; 
	   
	   $res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
}

/*switch ($accion) {
    case 'cargardatos':
        cargartabla($conex,$wemp_pmla);
        break;
    case 'actualizar':
        actualizar($conex,$autorizacion,$wemp_pmla);
        break;
   
}
*/


$wemp_pmla = $_GET['wemp_pmla'];

if( empty($wemp_pmla) )
	$wemp_pmla=$_POST['wemp_pmla'];

$autorizacion=$_POST['fila'];
actualizar($conex,$autorizacion,$wemp_pmla);