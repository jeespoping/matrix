<?php
include_once("root/comun.php");

function puedeAutorizarArticulos( $conex, $wmovhos, $user ){
	
	$val = false;
	
	$sql = "SELECT *
			  FROM ".$wmovhos."_000294
			 WHERE Dirusu = ?
			   AND Direst = 'on'
			 ";
			 
	$st = mysqli_prepare( $conex, $sql );

	mysqli_stmt_bind_param( $st, "s", $user );

	/* Ejecutar la sentencia */
	$num = mysqli_stmt_execute( $st ) or die ( mysqli_errno($conex) . " - Error al consultar director médico" );
	
	$res = mysqli_stmt_get_result($st);
	
	mysqli_stmt_close( $st );
	
	if( $row = mysql_fetch_array($res) ){
		$val = true;
	}
	
	return $val;
}

function registrarAuditoriaKardexAutorizaciones( $conexion, $wbasedato, $auditoria ){

	$q = "INSERT INTO ".$wbasedato."_000055
				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaudes, Kaufec, Kaumen, Kauido, Seguridad)
			VALUES
				('movhos','".date("Y-m-d")."','".date("H:i:s")."','$auditoria->historia','$auditoria->ingreso','$auditoria->descripcion','$auditoria->fechaKardex','$auditoria->mensaje','$auditoria->idOriginal','A-$auditoria->seguridad')";

	$res = mysql_query($q, $conexion); // or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}


function suspenderMedicamentoAutorizaciones( $conex, $wmovhos, $historia, $ingreso, $fecha, $art, $ido, $user ){
	
	$val = false;
	
	$sql = "UPDATE ".$wmovhos."_000060 
			   SET Kadsus = 'on',
				   Kadare = 'off'
			 WHERE Kadhis = '$historia'
			   AND Kading = '$ingreso'
			   AND Kadfec = '$fecha'
			   AND Kadart = '$art'
			   AND Kadido = '$ido'
			  ";
	
	$resTemp = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - No actualizo suspension de articulo en temporal" );
	
	$sql = "UPDATE ".$wmovhos."_000054 
			   SET Kadsus = 'on',
				   Kadare = 'off'
			 WHERE Kadhis = '$historia'
			   AND Kading = '$ingreso'
			   AND Kadfec = '$fecha'
			   AND Kadart = '$art'
			   AND Kadido = '$ido'
			  ";
	
	$resDef = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - No actualizo suspension de articulo en definitivo" );
	
	if( $resDef || $resTemp ){
		
		$val = true;
		
		//Registro de auditoria
		$auditoria = new class {};

		$auditoria->historia 	= $historia;
		$auditoria->ingreso 	= $ingreso;
		$auditoria->descripcion = $art;
		$auditoria->fechaKardex = $fecha;
		$auditoria->mensaje 	= "Ariculo suspendido desde autorizaciones";
		$auditoria->seguridad 	= $user;
		$auditoria->idOriginal 	= $ido;
	
		registrarAuditoriaKardexAutorizaciones( $conex, $wmovhos, $auditoria );
	}
	else{
		echo "No se registro suspención de medicamentos";
	}
	
	return $val;
}

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
	
	$fecha 			  = date( "Y-m-d" );
	$estadoSuspension = 'on';
	
	$autorizacion['justificacion_medico_autoriza'] = utf8_decode( $autorizacion['justificacion_medico_autoriza'] );
	$autorizacion['justificacion_medico_ordena'] = utf8_decode( $autorizacion['justificacion_medico_ordena'] );
	
	$wmovhos=consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	
	$sqlK = " SELECT a.* 
			    FROM ".$wmovhos."_000054 a, ".$wmovhos."_000208 b
			   WHERE Kadhis  = '".$autorizacion['historia']."' 
				 AND Kading  = '".$autorizacion['ingreso']."' 
				 AND Kadart  = '".$autorizacion['codigo_medicamento']."' 
				 AND Kadhis  = Ekxhis
				 AND Kading  = Ekxing
				 AND Kadfec  = Ekxfec
				 AND Kadido  = Ekxido
				 AND Kadart  = Ekxart
				 AND Ekxfau  = '0000-00-00'
				 AND Kadfec  = '".$fecha."'
			   UNION
			  SELECT a.* 
			    FROM ".$wmovhos."_000060 a, ".$wmovhos."_000209 b
			   WHERE Kadhis  = '".$autorizacion['historia']."' 
				 AND Kading  = '".$autorizacion['ingreso']."' 
				 AND Kadart  = '".$autorizacion['codigo_medicamento']."' 
				 AND Kadhis  = Ekxhis
				 AND Kading  = Ekxing
				 AND Kadfec  = Ekxfec
				 AND Kadido  = Ekxido
				 AND Kadart  = Ekxart
				 AND Ekxfau  = '0000-00-00'
				 AND Kadfec  = '".$fecha."'";

	$resK = mysql_query( $sqlK, $conex ) or die ( "Error: ".mysql_errno()." - Al consultar pacientes 33 - " . mysql_error() );
	
	
	
	$sql = " UPDATE ".$wmovhos."_000208
			    SET ".$wmovhos."_000208.Ekxaut  = '".$autorizacion['autoriza']."',
					".$wmovhos."_000208.Ekxfau  = '".date(' Y-m-d')."',
					".$wmovhos."_000208.Ekxhau  = '".date("H:i:s")."',
					".$wmovhos."_000208.Ekxmau  = '".$user."',
					".$wmovhos."_000208.Ekxjau  = '".$autorizacion['justificacion_medico_autoriza']."'
			  WHERE ".$wmovhos."_000208.Ekxhis  = '".$autorizacion['historia']."' 
			    AND ".$wmovhos."_000208.Ekxing  = '".$autorizacion['ingreso']."' 
				AND ".$wmovhos."_000208.Ekxart  = '".$autorizacion['codigo_medicamento']."' 
				AND ".$wmovhos."_000208.Ekxfau  = '0000-00-00'
				AND ".$wmovhos."_000208.Ekxfec  = '".date( "Y-m-d" )."'";
			
	////////////////////////////////////////////////////
	$sql2 = " UPDATE ".$wmovhos."_000209
				 SET ".$wmovhos."_000209.Ekxaut='".$autorizacion['autoriza']."',
					 ".$wmovhos."_000209.Ekxfau='".date(' Y-m-d')."',
					 ".$wmovhos."_000209.Ekxhau='".date("H:i:s")."',
					 ".$wmovhos."_000209.Ekxmau='".$user."',
					 ".$wmovhos."_000209.Ekxjau='".$autorizacion['justificacion_medico_autoriza']."'
			   WHERE ".$wmovhos."_000209.Ekxhis='".$autorizacion['historia']."' 
				 AND ".$wmovhos."_000209.Ekxing='".$autorizacion['ingreso']."' 
				 AND ".$wmovhos."_000209.Ekxart='".$autorizacion['codigo_medicamento']."' 
				 AND ".$wmovhos."_000209.Ekxfau= '0000-00-00'
				 AND ".$wmovhos."_000209.Ekxfec='".date( "Y-m-d" )."'";

	$res = mysql_query( $sql, $conex ) or die ( "Error: ".mysql_errno()." - Al actualizar autorizacion 11 - " . mysql_error() );
	$res = mysql_query( $sql2, $conex ) or die ( "Error: ".mysql_errno()." - Al actualizar autorizacion  22 - " . mysql_error() );

	registrarlog( $conex, $autorizacion, $wemp_pmla, $user );
	
	if( $autorizacion['autoriza'] != 'on' ){
		
		while( $rows = mysql_fetch_array( $resK ) ){
			
			$ido = $rows[ 'Kadido' ];
			
			$sqlLEVIC = " SELECT Levido, Levlev
							FROM ".$wmovhos."_000171 a
						   WHERE levhis = '".$autorizacion['historia']."' 
							 AND leving = '".$autorizacion['ingreso']."'
							 AND levins = '".$autorizacion['codigo_medicamento']."'
							 AND levidi = '".$ido."'
						 ";

			$resLEVIC = mysql_query( $sqlLEVIC, $conex ) or die ( "Error: ".mysql_errno()." - Verificando si es un IC - " );			
			$numLEVIC = mysql_num_rows( $resLEVIC );
			
			if( $numLEVIC == 0 ){
				suspenderMedicamentoAutorizaciones( $conex, $wmovhos, $autorizacion['historia'], $autorizacion['ingreso'], $fecha, $autorizacion['codigo_medicamento'], $ido, $user );				
			}
			else{
				$rowLEVIC = mysql_fetch_array( $resLEVIC );
				
				suspenderMedicamentoAutorizaciones( $conex, $wmovhos, $autorizacion['historia'], $autorizacion['ingreso'], $fecha, $rowLEVIC['Levlev'], $rowLEVIC['Levido'], $user );
				
				$sqlItem = "  SELECT Levidi, Levins 
								FROM ".$wmovhos."_000171 a
							   WHERE levhis = '".$autorizacion['historia']."' 
								 AND leving = '".$autorizacion['ingreso']."'
								 AND levido = '".$rowLEVIC['Levido']."'
							 ";

				$resItem = mysql_query( $sqlItem, $conex ) or die ( "Error: ".mysql_errno()." - Consultando items de IC para suspender - " );
				
				while( $item = mysql_fetch_array( $resItem ) )
				{
					$idi 	 = $item[ 'Levidi' ];
					$artItem = $item[ 'Levins' ];
					
					suspenderMedicamentoAutorizaciones( $conex, $wmovhos, $autorizacion['historia'], $autorizacion['ingreso'], $fecha, $artItem, $idi, $user );				
				}
			}
		}
	}
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
	
	$descripcion = $autorizacion['codigo_medicamento']." - ".$mensaje
				   ."<br>Justificacion al ordenar: ".$autorizacion['justificacion_medico_ordena']
				   ."<br>Justificacion al autorizar: ".$autorizacion['justificacion_medico_autoriza'];
	
	$sql ="INSERT INTO 
			".$wmovhos."_000055(    Medico     , Fecha_data  ,  Hora_data ,       Kauhis                   ,  Kauing                       ,  Kaufec     ,        Kaudes     ,       Kaumen          , Kauido ,   Seguridad   ) 
						VALUES ( '".$wmovhos."', '".$fecha."', '".$hora."', '".$autorizacion['historia']."', '".$autorizacion['ingreso']."', '".$fecha."', '".$descripcion."', 'Proceso autorizacion',   '0'  , 'C-".$user."' )
		   "; 
   
   $res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
}


function consultarLog( $conex, $wemp_pmla, $wmovhos, $his, $ing ){
	  
	$val = [ 'data' => [] ]; 
	
	$sql = "SELECT Kauhis, Kauing, Kaufec, Kaudes, Kaumen, Kauido, a.Seguridad, Descripcion, Pacno1, Pacno2, Pacap1, Pacap2, Pactid, Pacced
			  FROM ".$wmovhos."_000055 a, usuarios b, root_000036, root_000037
			 WHERE Kaumen = 'Proceso autorizacion'
			   AND Kauhis = '".$his."'
			   AND Kauing = '".$ing."'
			   AND Codigo = SUBSTRING(a.Seguridad FROM INSTR(a.Seguridad,'-')+1)
			   AND orihis = kauhis
			   AND oritid = pactid
			   AND oriced = pacced
			   AND oriori = '".$wemp_pmla."'
			";
	
	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." Al consultar log - " . mysql_error());
	
	while( $rows = mysql_fetch_array($res) )
	{
		// $val['data'][] = [
				// 'historia' 		=> $rows[ 'Kauhis' ],
				// 'ingreso' 		=> $rows[ 'Kauing' ],
				// 'paciente' 		=> $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ],
				// 'mensaje' 		=> $rows[ 'Kaumen' ],
				// 'fecha' 		=> $rows[ 'Kaufec' ],
				// 'descripcion' 	=> $rows[ 'Kaudes' ],
				// 'medico' 		=> explode( "-", $rows[ 'Seguridad' ] )[1]."-".utf8_encode( $rows[ 'Descripcion' ] ),
				// // 'codigoMedico'	=> explode( "-", $rows[ 'Seguridad' ] )[1],
			// ];
			
		$val['data'][] = [
				'0' 		=> $rows[ 'Kauhis' ]."-".$rows[ 'Kauing' ],
				'1' 		=> $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ],
				'2' 		=> $rows[ 'Kaufec' ],
				'3' 		=> utf8_encode( $rows[ 'Kaudes' ] ),
				'4' 		=> explode( "-", $rows[ 'Seguridad' ] )[1]."-".utf8_encode( $rows[ 'Descripcion' ] ),
				'5'			=> explode( "-", $rows[ 'Seguridad' ] )[1],
				'6' 		=> $rows[ 'Kaumen' ],
			];
	}
	
	return $val;
}

if( $_GET['accion'] ){
	
	$wemp_pmla = $_GET['wemp_pmla'];

	if( empty($wemp_pmla) )
		$wemp_pmla = $_POST['wemp_pmla'];
	
	switch( $accion ){
		
		case 'consultarLog':
		
			$historia = $_POST['historia'];
			$ingreso  = $_POST['ingreso'];
			$wmovhos  = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );
			
			$result = consultarLog( $conex, $wemp_pmla, $wmovhos, $historia, $ingreso );
			
			echo json_encode( $result );
			
			break;
			
		case 'cargardatos':
			cargartabla($conex,$wemp_pmla);
			break;
		
		case 'actualizar':
			$autorizacion = $_POST['fila'];
			actualizar($conex,$autorizacion,$wemp_pmla);
			break;
	   
	}
	
	exit();
}